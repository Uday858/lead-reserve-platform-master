<?php

namespace App\Jobs;

use App\Campaign;
use App\CampaignAttribute;
use App\Lead;
use App\LeadPoint;
use App\PlatformEvent;
use App\Publisher;
use Carbon\Carbon;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use GuzzleHttp\Client as HttpClient;

class FireLeadSend implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Variables to handle trajectory of lead send.
     */
    public $campaignId, $publisherId, $leadId;

    /**
     * @var Campaign $campaign
     */
    public $campaign;

    /**
     * @var Publisher $publisher
     */
    public $publisher;

    /**
     * @var Lead $lead
     */
    public $lead;

    /**
     * @var $advertiserUrlBase
     * The base of the original advertiser url (Everything bar query.)
     */
    public $advertiserUrlBase;

    /**
     * @var $advertiserUrlQuery
     * The query of the original advertiser URL -> converted already to array.
     */
    public $advertiserUrlQuery;

    /**
     * @var $urlFieldArray
     * This variable is an array of key=>value pairs for http_build_query
     * This guy is SENT to advertiser. (SEND NO INTERNAL DATA!)
     */
    public $urlFieldArray;

    public $ipAddress;
    public $userAgent;

    /**
     * FireLeadSend constructor.
     * @param $campaignId
     * @param $publisherId
     * @param $ipAddress
     * @param $userAgent
     * @param $leadId
     */
    public function __construct($campaignId, $publisherId, $ipAddress, $userAgent, $leadId)
    {
        $this->ipAddress = $ipAddress;
        $this->userAgent = $userAgent;

        // Load campaignId and relative campaign object.
        $this->campaignId = $campaignId;
        $this->campaign = Campaign::whereId($this->campaignId)->first();

        // Load publisherId and relative publisher object.
        $this->publisherId = $publisherId;
        $this->publisher = Publisher::whereId($this->publisherId)->first();

        // Load leadId and relative lead object.
        $this->leadId = $leadId;
        $this->lead = Lead::whereId($this->leadId)->first();

        // Empty array to initiate the urlFieldArray variable.
        $this->urlFieldArray = [];
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if ($this->deconstructOriginalUrl()) {
            if ($this->buildUrlFieldArray()) {
                $this->buildAdvertiserUrl();
                $this->fireLead();
            }
        }
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
        return $parsedURL["scheme"] . "://" . $parsedURL["host"] . $parsedURL["path"] . "?";
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
        foreach ($this->campaign->posting_params->toArray() as $postingParamKey => $postingParamValue) {

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

            } else if ($postingParamValue["type"] == "static") {

                // Use static value.
                $this->urlFieldArray[$postingParamValue["outgoing_field"]] = $postingParamValue["static_value"];

            } else if ($postingParamValue["type"] == "randomComma") {

                // Explode the static value (comma separated) array.
                $valueArray = explode(",", $postingParamValue["static_value"]);
                $this->urlFieldArray[$postingParamValue["outgoing_field"]] = $valueArray[rand(0, count($valueArray) - 1)];

            } else if ($postingParamValue["type"] == "dropdown") {

                // Explode the static value array.
                $valueArray = explode(",", $postingParamValue["static_value"]);

                // Is the lead point value inside of the value array (above) ?
                if (in_array(LeadPoint::whereLeadId($this->leadId)->where("key", $postingParamValue["outgoing_field"])->first()->value, $valueArray)) {

                    $this->urlFieldArray[$postingParamValue["outgoing_field"]] = LeadPoint::whereLeadId($this->leadId)->where("key", $postingParamValue["outgoing_field"])->first()->value;

                } else {

                    // Track this event in logs.
                    dispatch((new TrackPlatformEventAsync("lead.sent.failed", "Disallowed value", [
                        "attemptedValue" => LeadPoint::whereLeadId($this->leadId)->where("key", $postingParamValue["outgoing_field"])->first()->value,
                        "allowedValues" => $postingParamValue["static_value"],
                        "lead_id" => $this->leadId
                    ]))->onQueue("platform-processing"));

                    // Fail the job... Do not continue!
                    $this->fail(new \Exception());

                    // Uh oh!
                    $returnValue = false;
                }
            } else if ($postingParamValue["type"] == "system") {

                // Collect all system values (TODO: DBD)
                $values = [
                    "campaign_id" => $this->campaignId,
                    "publisher_id" => $this->publisherId,
                    "timestamp" => Carbon::now()->toFormattedDateString(),
                    "ip" => "" . $this->ipAddress . "",
                    "ua" => "" . $this->userAgent . ""
                ];

                // Build out with the correct value.
                $this->urlFieldArray[$postingParamValue["outgoing_field"]] = $values[$postingParamValue["incoming_field"]];

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

    private function fireLead()
    {
        $guzzleClient = new HttpClient();

        dispatch((new TrackPlatformEventAsync("lead.presend", "Lead about to be sent to advertiser.", [
            "lead_id" => $this->leadId,
            "publisher_id" => $this->publisherId,
            "campaign_id" => $this->campaignId,
            "requestURL" => $this->buildAdvertiserUrl()
        ]))->onQueue("platform-processing"));

        try {
            $campaignPostingMethod = $this->campaign->hasAttributeOrEmpty("posting_method") != "" ? $this->campaign->hasAttributeOrEmpty("posting_method") : "POST";
            $res = $guzzleClient->request($campaignPostingMethod, $this->buildAdvertiserUrl());
        } catch (RequestException $e) {
            $this->trackErrorHandlingForRequest($e);
        }

        // Make sure the request went through.
        if (isset($res)) {
            $this->trackLeadSend([
                "statusCode" => $res->getStatusCode(),
                "contents" => $res->getBody()->getContents()
            ]);
        }
    }

    /**
     * @param RequestException $exception
     */
    private function trackErrorHandlingForRequest($exception)
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

        // Track this event in logs.
        dispatch((new TrackPlatformEventAsync("lead.sent.failed", "Lead failed to send to advertiser.", $platformEventArray))->onQueue("platform-processing"));
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

        // Track the lead metrics after send (Essentially, revenue.)
        dispatch((new TrackLeadMetrics($this->leadId, $this->campaignId, $this->publisherId))->onQueue("platform-processing"));
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
