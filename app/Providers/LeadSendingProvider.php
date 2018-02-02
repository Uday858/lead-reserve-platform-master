<?php

namespace App\Providers;

use App\Campaign;
use App\CampaignAttribute;
use App\Jobs\TrackLeadMetrics;
use App\Lead;
use App\LeadPoint;
use App\PlatformEvent;
use App\Providers\Factories\UserAgentFactory;
use App\Publisher;
use App\PublisherCampaign;
use Carbon\Carbon;
use GuzzleHttp\Exception\RequestException;
use App\Jobs\TrackPlatformEventAsync;
use GuzzleHttp\Client as HttpClient;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LeadSendingProvider
{

    /**
     * Variables to handle trajectory of lead send.
     */
    public
        $campaignId,
        $publisherId,
        $leadId,
        $campaign,
        $publisher,
        $publisherCampaign,
        $lead,
        $advertiserUrlBase,
        $advertiserUrlQuery,
        $urlFieldArray,
        $ipAddress,
        $userAgent,
        $certification;

    /**
     * Send the lead off to the advertiser, fetch response.
     * @param $campaignId
     * @param $publisherId
     * @param $ipAddress
     * @param $userAgent
     * @param $leadId
     * @oaran $certification
     * @return mixed
     */
    public function sendLeadAndGetResponse($campaignId, $publisherId, $ipAddress, $userAgent, $leadId, $certification = null)
    {
        // Initiate the class with the necessary components.
        $this->initProviderClass($campaignId, $publisherId, $ipAddress, $userAgent, $leadId);

        // Set the lead certification, if exists.
        $this->certification = ($certification != null) ? $certification : "-1";

        // Deconstruct the original URL.
        if ($this->deconstructOriginalUrl()) {
            // Build the URL field, format from - advertiser URL, fire lead.
            if ($this->buildUrlFieldArray()) {
                $this->buildAdvertiserUrl();
                return $this->fireLead();
            }
        }

        // Return the formatted response.
        return [
            "status" => "failed",
            "response" => [
                "message" => "Campaign was setup incorrectly, please contact account manager."
            ]
        ];
    }

    /**
     * Ping the lead off to the advertiser, try to retrieve a SUCCESS/FAIL.
     * @param $campaignId
     * @param $publisherId
     * @param $ipAddress
     * @param $userAgent
     * @param $leadId
     * @return array|string
     */
    public function pingLeadAndGetResponse($campaignId, $publisherId, $ipAddress, $userAgent, $leadId)
    {
        // Initiate the class with the necessary components.
        $this->initProviderClass($campaignId, $publisherId, $ipAddress, $userAgent, $leadId);

        // Go ahead and "pre-flight" the lead.
        return $this->preflightLead();
    }

    /**
     * Go ahead and initiate the service provider class.
     * @param $campaignId
     * @param $publisherId
     * @param $ipAddress
     * @param $userAgent
     * @param $leadId
     */
    private function initProviderClass($campaignId, $publisherId, $ipAddress, $userAgent, $leadId)
    {
        $this->ipAddress = $ipAddress;
        $this->userAgent = $userAgent;

        // Load campaignId and relative campaign object.
        $this->campaignId = $campaignId;
        $this->campaign = Campaign::whereId($this->campaignId)->first();

        // Load publisherId and relative publisher object.
        $this->publisherId = $publisherId;
        $this->publisher = Publisher::whereId($this->publisherId)->first();

        // Load publisherCampaign
        $this->publisherCampaign = PublisherCampaign::whereCampaignId($campaignId)->wherePublisherId($publisherId)->first();

        // Load leadId and relative lead object.
        $this->leadId = $leadId;
        $this->lead = Lead::whereId($this->leadId)->first();

        // Empty array to initiate the urlFieldArray variable.
        $this->urlFieldArray = [];
    }

    /**
     * Deconstruct the original URL.
     */
    private function deconstructOriginalUrl()
    {
        try {
            // Break down the original URL and Build the original URL without the query and set the class advertiserUrlBase.
            $this->advertiserUrlBase = $this->buildURLWithoutQuery(parse_url($this->campaign->posting_url));

            // Break down the query and set the class advertiserUrlQuery variable.
            $queryBreakDownArray = [];
            parse_str(parse_url($this->campaign->posting_url)["query"], $queryBreakDownArray);
            $this->advertiserUrlQuery = $queryBreakDownArray;

            return true;
        } catch (\Exception $e) {
            $returnValue = false;
            if (parse_url($this->campaign->posting_url) != false) {
                $this->advertiserUrlBase = $this->buildURLWithoutQuery(parse_url($this->campaign->posting_url));
                $this->advertiserUrlQuery = -1;
                $returnValue = true;
            } else {
                // Track this event in logs.
                dispatch((new TrackPlatformEventAsync("lead.sent.failed", "Lead failed to send to advertiser.", [
                    "code" => $e->getCode(),
                    "trace" => $e->getTraceAsString()
                ]))->onQueue("platform-processing"));
            }
            return $returnValue;
        }
    }

    /**
     * Build and return a url without query point.
     *
     * @param $parsedURL
     * @return string
     */
    private function buildURLWithoutQuery($parsedURL)
    {
        $returnValue = false;
        try {
            $returnValue = $parsedURL["scheme"] . "://" . $parsedURL["host"] . $parsedURL["path"] . "?";
        } catch (\Exception $e) {
            // ...
        }
        return $returnValue;
    }

    /**
     * Work with the original posting params and original query params.
     */
    private function buildUrlFieldArray()
    {
        return $this->buildWithCampaignPostingParams();
    }

    private function buildWithCampaignPostingParams()
    {
        $returnValue = true;
        // We're going through our SET posting parameters.
        foreach ($this->campaign->fields->toArray() as $postingParamKey => $postingParamValue) {

            if ($postingParamValue["type"] == "field") {
                if (isset($this->generateLeadFields()[$postingParamValue["incoming_field"]])) {

                    // Generate the transform value.
                    $transformValue = $this->generateLeadFields()[$postingParamValue["incoming_field"]];

                    // Add to the URL Field Array using the original URL key.
                    $this->urlFieldArray[$postingParamValue["outgoing_field"]] = $this->lead[$transformValue];

                } else if (LeadPoint::whereLeadId($this->leadId)->where("key", $postingParamValue["incoming_field"])->exists()) {

                    // Outgoing fields for lead point management.
                    $this->urlFieldArray[$postingParamValue["outgoing_field"]] = LeadPoint::whereLeadId($this->leadId)->where("key", $postingParamValue["incoming_field"])->first()->value;

                }
            } else if ($postingParamValue["type"] == "hardcoded") {

                if($postingParamValue["hardcoded_value"] == null) {
                    // Use static value.
                    $this->urlFieldArray[$postingParamValue["outgoing_field"]] = " ";
                } else {
                    $this->urlFieldArray[$postingParamValue["outgoing_field"]] = $postingParamValue["hardcoded_value"];
                }
                

            } else if ($postingParamValue["type"] == "random") {

                // Explode the static value (comma separated) array.
                $valueArray = explode(",", $postingParamValue["random_value"]);
                $this->urlFieldArray[$postingParamValue["outgoing_field"]] = $valueArray[rand(0, count($valueArray) - 1)];

            } else if ($postingParamValue["type"] == "inclusion") {

                // Explode the static value array.
                $valueArray = explode(",", $postingParamValue["inclusion_value"]);

                // Is the lead point value inside of the value array (above) ?
                if (in_array(LeadPoint::whereLeadId($this->leadId)->where("key", $postingParamValue["incoming_field"])->first()->value, $valueArray)) {

                    $this->urlFieldArray[$postingParamValue["outgoing_field"]] = LeadPoint::whereLeadId($this->leadId)->where("key", $postingParamValue["incoming_field"])->first()->value;

                } else {

                    // Track this event in logs.
                    dispatch((new TrackPlatformEventAsync("lead.sent.failed", "Disallowed value", [
                        "attemptedValue" => LeadPoint::whereLeadId($this->leadId)->where("key", $postingParamValue["incoming_field"])->first()->value,
                        "allowedValues" => $postingParamValue["inclusion_value"],
                        "lead_id" => $this->leadId
                    ]))->onQueue("platform-processing"));

                    // Uh oh!
                    $returnValue = false;
                }
            } else if ($postingParamValue["type"] == "system") {
                // Collect all system values
                $values = [
                    "campaign_id" => $this->campaignId,
                    "publisher_id" => $this->publisherId,
                    "timestamp" => Carbon::now()->toDateTimeString(),
                    "tf_cert" => "" . $this->certification . "",
                    "ip" => "" . $this->ipAddress . "",
                    "ua" => "" . $this->userAgent . ""
                ];

                // Build out with the correct value.
                $this->urlFieldArray[$postingParamValue["outgoing_field"]] = $values[$postingParamValue["system_value"]];

            }

        }
        return $returnValue;
    }

    /**
     * Take the final URLFieldArray..build it into a query and return it upon the base advertiser url.
     */
    private function buildAdvertiserUrl()
    {
        return $this->advertiserUrlBase . http_build_query($this->urlFieldArray);
    }

    /**
     * Ping advertiser server.
     * @return bool
     */
    private function preflightLead()
    {
        if ($this->campaign->hasAttributeOrEmpty("preping_available") == "Yes") {
            // Create the pre-ping HttpClient.
            $guzzleClient = new HttpClient();

            // Gather the ping URL and posting method.
            $pingAttributes = [
                "url" => $this->formatPrePingURL($this->lead, $this->campaign->hasAttributeOrEmpty("preping_url")),
                "method" => $this->campaign->hasAttributeOrEmpty("preping_posting_method")
            ];

            dispatch((new TrackPlatformEventAsync("lead.preflight", "Pinging advertiser", [
                "lead_id" => $this->leadId,
                "publisher_id" => $this->publisherId,
                "campaign_id" => $this->campaignId,
                "ping_url" => $pingAttributes["url"]
            ]))->onQueue("platform-processing"));

            try {
                $response = $guzzleClient->request($pingAttributes["method"], $pingAttributes["url"], $this->getRequestOptions());
            } catch (RequestException $e) {
                // TODO: Swallow the exception.
            }

            // Return value should be "false" to start with.
            $returnValue = false;

            if (isset($response)) {
                // Pull the response contents.
                $responseContents = $response->getBody()->getContents();
                // Track the preflight send.
                dispatch((new TrackPlatformEventAsync("lead.preflight.send", "Pinged advertiser", [
                    "lead_id" => $this->leadId,
                    "publisher_id" => $this->publisherId,
                    "campaign_id" => $this->campaignId,
                    "statusCode" => $response->getStatusCode(),
                    "contents" => $responseContents
                ]))->onQueue("platform-processing"));
                // Are we a success?
                if (str_contains($responseContents, $this->campaign->hasAttributeOrEmpty("preping_success_response"))) {
                    $returnValue = true;
                }
            }

            // Return the return value.
            return $returnValue;
        } else {
            return true;
        }
    }

    /**
     * Format the pre ping url.
     * @param $leadObject
     * @param $URL
     * @return mixed
     */
    private function formatPrePingURL($leadObject, $URL)
    {
        return str_replace([
            "[Email]",
            "[FirstName]",
            "[LastName]",
            "[LeadId]",
            "[PublisherId]"
        ], [
            $leadObject->email_address,
            $leadObject->first_name,
            $leadObject->last_name,
            $leadObject->id,
            $this->publisherId
        ], $URL);
    }

    private function getRequestOptions()
    {
        return [
            "headers" => [
                "User-Agent" => UserAgentFactory::retrieveRandomAgentString()
            ]
        ];
    }

    /**
     * @return array
     */
    private function fireLead()
    {
        // Build out return value.
        $returnValue = [
            "response" => [],
            "status" => ""
        ];

        // Daily Cap Check
        if ($this->isUnderCap()) {
            $guzzleClient = new HttpClient();

            dispatch((new TrackPlatformEventAsync("lead.presend", "Lead about to be sent to advertiser.", [
                "lead_id" => $this->leadId,
                "publisher_id" => $this->publisherId,
                "campaign_id" => $this->campaignId,
                "requestURL" => $this->buildAdvertiserUrl()
            ]))->onQueue("platform-processing"));

            try {
                $campaignPostingMethod = $this->campaign->hasAttributeOrEmpty("posting_method") != "" ? $this->campaign->hasAttributeOrEmpty("posting_method") : "POST";
                
                // TODO: Add options for "post string" or "x-application-encoded"
                $res = $guzzleClient->request($campaignPostingMethod, $this->buildAdvertiserUrl(), $this->getRequestOptions());

            } catch (RequestException $e) {
                $this->trackErrorHandlingForRequest($e);
            }

            // Make sure the request went through.
            if (isset($res)) {
                $leadStatus = $this->trackLeadSend([
                    "statusCode" => $res->getStatusCode(),
                    "contents" => $res->getBody()->getContents()
                ]);

                if(!is_array($leadStatus)) {
                    // Is accepted?
                    if ($leadStatus == "accepted") {
                        $returnValue["status"] = "accepted";
                        $returnValue["response"] = [
                            "status" => "SUCCESS",
                            "message" => "User information accepted"
                        ];
                    } else {
                        // User information was rejected
                        $returnValue["status"] = "failed";
                        $returnValue["response"] = [
                            "status" => "FAIL",
                            "message" => "User information rejected"
                        ];
                    }
                } else {
                    // We have a "detail" to send back.
                    $returnValue["status"] = "failed";
                    $returnValue["response"] = [
                        "status" => "FAIL",
                        "message" => "User information rejected",
                        "detail" => (isset($leadStatus["detail"]) ? $leadStatus["detail"] : "")
                    ];
                }




            } else {
                // Advertiser server error.
                $returnValue["status"] = "failed";
                $returnValue["response"] = [
                    "status" => "FAIL",
                    "message" => "Advertiser Server Error"
                ];
            }
        } else {

            dispatch((new TrackPlatformEventAsync("lead.failed.campaign.hit.cap", "Lead " . $this->leadId . " hit cap on Campaign " . $this->campaignId, [
                "lead_id" => $this->leadId,
                "publisher_id" => $this->publisherId,
                "campaign_id" => $this->campaignId,
                "requestURL" => $this->buildAdvertiserUrl()
            ]))->onQueue("platform-processing"));

            // Set the return value.
            $returnValue["status"] = "failed";
            $returnValue["response"] = [
                "status" => "FAIL",
                "message" => "Lead Cap Hit"
            ];
        }

        // Return the return value.
        return $returnValue;
    }

    /**
     * @return string
     */
    private function isUnderCap()
    {
        // A pseudo element to return.
        $returnValue = false;

        // Fetch a new instance of the reporting metrics provider.
        $reportingService = new ReportingMetricsProvider();

        // Fetch our publisher cap.
        $campaignCap = $this->campaign->hasAttributeOrEmpty('daily_cap');
        $publisherCap = $this->publisherCampaign->lead_cap;

        if($reportingService->getLeadAcceptedEventsByCampaignPublisher($this->campaignId,$this->publisherId) < $publisherCap) {
            $returnValue = true;
        }


        // Always default to this.
        return $returnValue;
    }

    /**
     * @param RequestException $exception
     */
    private function trackErrorHandlingForRequest($exception)
    {
        // Track this event in logs.
        dispatch((new TrackPlatformEventAsync("lead.sent.failed", "Lead failed to send to advertiser.", $this->getArrayFromException($exception)))->onQueue("platform-processing"));
    }

    /**
     * Get array from exception.
     * @param $exception
     * @return array
     */
    private function getArrayFromException($exception)
    {
        // Create a blank array to store all variable information.
        $platformEventArray = [];

        // Build out the event array for more context.
        if ($exception->hasResponse()) {
            $platformEventArray["response_status"] = $exception->getResponse()->getStatusCode();
            $platformEventArray["response_contents"] = $exception->getResponse()->getBody()->getContents();
        } else {
            $platformEventArray["error_message"] = "Request got no response.";
        }

        // Exception items.
        $platformEventArray["exception_code"] = $exception->getCode();
        $platformEventArray["exception_message"] = $exception->getMessage();

        return $platformEventArray;
    }

    private function trackLeadSend($responseFormattedData)
    {
        // Track that the lead was sent to the advertiser.
        dispatch((new TrackPlatformEventAsync("lead.sent", "Lead sent to advertiser.", [
            "lead_id" => intval($this->leadId),
            "publisher_id" => intval($this->publisherId),
            "campaign_id" => intval($this->campaignId),
            "response_data" => $responseFormattedData
        ]))->onQueue("platform-processing"));

        // Check formatted data.
        if (str_contains(strtolower($responseFormattedData["contents"]),strtolower($this->campaign->hasAttributeOrEmpty("success_response")))) {
            // Track the lead metrics after send.
            dispatch((new TrackLeadMetrics(
                $this->leadId,
                $this->campaignId,
                $this->publisherId,
                "accepted")
            )->onQueue("platform-processing"));
            return "accepted";
        } else {
            if($this->campaign->hasAttributeOrEmpty("detail_regex_match") != "" && $this->campaign->hasAttributeOrEmpty("detail_regex_match_number") != "") {
                return [
                    "detail" => $this->parseReponseContents(
                        $responseFormattedData["contents"],
                        $this->campaign->hasAttributeOrEmpty("detail_regex_match"),
                        $this->campaign->hasAttributeOrEmpty("detail_regex_match_number")
                    )
                ];
            }
            // Track the lead metrics after send.
            dispatch((new TrackLeadMetrics(
                $this->leadId,
                $this->campaignId,
                $this->publisherId,
                "rejected")
            )->onQueue("platform-processing"));
            return "rejected";
        }
    }

    /**
     * @param $contents
     * @param $pattern
     * @param $selectNumber
     * @return mixed
     */
    private function parseReponseContents($contents,$pattern,$selectNumber) {
        // Create the matches array.
        $matches = [];

        // Regular expression match.
        preg_match_all($pattern,$contents,$matches);

        if(count(explode(",",$selectNumber))!==1) {
            $searchMatchesArray = explode(",",$selectNumber);
            return $matches[$searchMatchesArray[0]][$searchMatchesArray[1]];
        }

        // Return the selected match number.
        return $matches[0][$selectNumber];
    }

    /**
     * Generate all of the lead fields in internal format.
     *
     * @return array
     */
    private function generateLeadFields()
    {
        // Return all base lead fields.
        return [
            "cid" => "campaign_id",
            "pid" => "publisher_id",
            "first-name" => "first_name",
            "last-name" => "last_name",
            "email-address" => "email_address",
            "ip-address" => "ip_address"
        ];
    }


}
