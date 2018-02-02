<?php

namespace App\Http\Controllers;

use App\Campaign;
use App\Publisher;
use App\APITransaction;
use App\PublisherCampaign;
use App\Jobs\BuildEmptyLeadForConversion;
use App\Jobs\BuildLeadObjectFromPublisher;
use App\Providers\Factories\IPFactory;
use App\Providers\Factories\UserAgentFactory;
use App\Providers\LeadCertificateProvider;
use App\Jobs\TrackLeadMetrics;
use App\Jobs\TrackLinkoutMetrics;
use App\Jobs\TrackPlatformEventAsync;
use App\Jobs\ExecuteDelayedLeadFlowProcess;
use App\Jobs\ProcessLeadTransaction;
use App\Providers\ReportingMetricsProvider;
use App\Lead;
use App\MetricClick;
use App\MetricConversion;
use App\MetricImpression;
use App\Providers\LeadFlowProvider;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use GuzzleHttp\Client as HttpClient;

class LeadController extends Controller
{

    public function customConvert()
    {
        if(!Input::has("campaignid") && !Input::has("clickid")) {
            return [
                "status" => false,
                "message" => "Please sent `campaignid` and `clickid` URL parameters."
            ];
        } else {
            return $this->convert(intval(Input::get("campaignid")),intval(Input::get("clickid")));
        }
    }

    /**
     * Functionality for tracking conversions from link out and CPA.
     *
     * @param $campaignId
     * @return array
     */
    public function convert($campaignId,$clickId)
    {
        // Get the campaign object.
        $campaign = Campaign::whereId($campaignId)->first();
        
        // Get the publisher ID. TODO: Safety
        $publisherId = MetricClick::whereId($clickId)->first()->publisher_id;
        
        try {
            // Get the conversion pixel
            $conversionPixel = $campaign->hasAttributeOrEmpty("publisher.".$publisherId.".conversion_pixel");

            // Replace variables in the conversion pixel.
            $conversionPixel = str_replace([
                "[ClickID]",
                "[AdvertiserID]",
                "[PublisherID]",
                "[CampaignID]",
                "[IPAddress]"
            ],[
                $clickId,
                $campaign->advertiser->id,
                $publisherId,
                $campaign->id,
                $_SERVER["REMOTE_ADDR"]
            ],$conversionPixel);

            // Track a conversion
            $conversion = MetricConversion::create([
                "campaign_id" => $campaignId,
                "click_id" => $clickId,
                "pixel_fire" => 0
            ]);

            $guzzleClient = new HttpClient();

            $response = $guzzleClient->request($campaign->hasAttributeOrEmpty("publisher.".$publisherId.".fire_method"), $conversionPixel);
            $responseContents = $response->getBody()->getContents();
        } catch(\Exception $e) {
            Log::error("Conversion pixel is broken!",[$e->getMessage()]);
        }

        // Revenue tracking.
        dispatch((new TrackLinkoutMetrics($conversion->click_id))->onQueue("platform-processing"));

        return "";
    }

    /**
     * Functionality for the link out campaigns.
     *
     * @param $campaignId
     * @param $publisherId
     * @return array
     */
    public function linkout($campaignId, $publisherId)
    {
        // Execute a click track.
        $click = MetricClick::create([
            "campaign_id" => $campaignId,
            "publisher_id" => $publisherId
        ]);

        // Fetch the campaign.
        $campaign = Campaign::whereId($campaignId)->first();

        // Return a redirect to the campaign posting URL.
        return redirect(
            $this->formatLinkoutURL(
                $campaign->hasAttributeOrEmpty("linkout_url"),
                $campaignId,
                $publisherId,
                $_SERVER["REMOTE_ADDR"],
                $click->id
            ), 302
        );
    }

    /**
     * @param $url
     * @param $campaignId
     * @param $publisherId
     * @param $ipAddress
     * @param $clickId
     * @return mixed
     */
    private function formatLinkoutURL($url, $campaignId, $publisherId, $ipAddress, $clickId)
    {
        return str_replace([
            "[ClickID]",
            "[CampaignID]",
            "[PublisherID]",
            "[IPAddress]"
        ], [
            $clickId, $campaignId, $publisherId, $ipAddress
        ], $url);
    }

    /**
     * Create a campaign impression.
     *
     * @param $campaignId
     * @param $publisherId
     * @return string
     */
    public function impression($campaignId, $publisherId)
    {
        // Create an impression.
        MetricImpression::create([
            "campaign_id" => $campaignId,
            "publisher_id" => $publisherId
        ]);
        // Blank return. (Meant to be iFramed)
        return "";
    }

    /**
     * Campaign actionable.
     *
     * @param $campaignId
     * @param $publisherId
     * @param $actionable
     * @param int $leadId
     * @return int|mixed
     */
    private function campaignActionable($campaignId, $publisherId, $actionable, $leadId = null)
    {
        if ($leadId != null) {
            $this->handleActionable($campaignId, $publisherId, $leadId, $actionable);
            return Input::get("lead_id");
        } else {
            $lead = $this->buildLeadObjectWithAttributes($campaignId, $publisherId);
            $this->handleActionable($campaignId, $publisherId, $lead->id, $actionable);
            return $lead->id;
        }
    }

    /**
     * Build out an empty lead object to retain an ID.
     * @param $campaignId
     * @param $publisherId
     * @return mixed
     */
    private function buildLeadObjectWithAttributes($campaignId, $publisherId)
    {
        // This functionality makes sure we allocate an insert ID for leads.
        $leadObject = new Lead();
        $leadObject->campaign_id = $campaignId;
        $leadObject->publisher_id = $publisherId;
        $leadObject->save();

        // Return the lead object.
        return $leadObject;
    }

    /**
     * Handle actionable,
     *
     * @param $campaignId
     * @param $publisherId
     * @param $leadId
     * @param $action
     */
    private function handleActionable($campaignId, $publisherId, $leadId, $action)
    {
        // Make sure the action is correct.
        switch ($action) {
            case "conversion":
                dispatch((new TrackPlatformEventAsync("lead.conversion", "Lead converted from a CPA/Linkout campaign.", [
                    "cid" => $campaignId,
                    "pid" => $publisherId,
                    "lead_id" => $leadId
                ]))->onQueue("platform-processing"));
                dispatch((new TrackLeadMetrics($leadId, $campaignId, $publisherId))->onQueue("lead-processing"));
                break;
            case "linkout":
                dispatch((new TrackPlatformEventAsync("lead.click", "Lead clicked from a CPA/Linkout campaign.", [
                    "cid" => $campaignId,
                    "pid" => $publisherId,
                    "lead_id" => $leadId
                ]))->onQueue("platform-processing"));
                break;
        }
    }

    public $transactionId,$campaignId,$publisherId,$startTTime,$inputArray,$ipAddress,$userAgent,$leadCertificate,$consentType;
    private function setTransactionVariables($transactionId,$campaignId,$publisherId,$inputArray,$startTime,$consentType) {
        $this->transactionId = $transactionId;
        $this->campaignId = $campaignId;
        $this->publisherId = $publisherId;
        $this->startTTime = $startTime;
        $this->consentType = $consentType;
    }
    private function flushTransactionVariables() {
        $this->transactionId = null;
        $this->campaignId = null;
        $this->publisherId = null;
        $this->startTTime = null;
        $this->inputArray = null;
    }
    private function initServerClientParameters()
    {
        // Get the remote address (IP Address)
        if (isset($_SERVER["REMOTE_ADDR"])) {
            if ($_SERVER["REMOTE_ADDR"] == "::1") {
                $this->ipAddress = IPFactory::retrieveIPString();
            } else {
                $this->ipAddress = $_SERVER["REMOTE_ADDR"];
            }
        } else {
            $this->ipAddress = IPFactory::retrieveIPString();
        }

        // Get a random user agent.
        if (isset($_SERVER["HTTP_USER_AGENT"])) {
            $this->userAgent = $_SERVER["HTTP_USER_AGENT"];
        } else {
            $this->userAgent = UserAgentFactory::retrieveRandomAgentString();
        }

        $this->leadCertificate = "";
    }

    public function capture()
    {
        // generate transaction id and transaction time.
        $transactionId = uniqid() . '-' . md5(microtime(1));
        $startingTransactionTime = microtime(1);

        if(!Input::has("cid") || !Input::has("pid")) {
            dispatch((new ProcessLeadTransaction(
                $transactionId,
                $_SERVER["REMOTE_ADDR"],
                number_format(microtime(1)-$startingTransactionTime,2),
                0,
                0,
                json_encode(Input::all()),
                "",
                "error",
                [],
                "CID or PID was not present!?"
            ))->onQueue('platform-processing'));            
            return [
                "status" => false,
                "message" => "Check to make sure you're sending - `cid` and `pid` fields.",
                "transaction_time" => number_format(microtime(1)-$startingTransactionTime,2)."s",
                "transaction_id" => $transactionId
            ];
        }

        // get the campaign id and publisher id
        $campaignId = intval(Input::get("cid"));
        $publisherId = intval(Input::get("pid"));

        if(!PublisherCampaign::whereCampaignId($campaignId)->wherePublisherId($publisherId)->exists()) {
            dispatch((new ProcessLeadTransaction(
                $transactionId,
                $_SERVER["REMOTE_ADDR"],
                number_format(microtime(1)-$startingTransactionTime,2),
                0,
                0,
                json_encode(Input::all()),
                "",
                "error",
                [],
                "Publisher was not assigned to campaign."
            ))->onQueue('platform-processing'));
            return [
                "status" => false,
                "message" => "You are not assigned to this campaign. Contact account manager for further information.",
                "transaction_time" => number_format(microtime(1)-$startingTransactionTime,2)."s",
                "transaction_id" => $transactionId
            ];
        }

        // init server client parameters
        $this->initServerClientParameters();

        // get campaign posting data
        if(!Cache::has("campaign.".$campaignId.".data")) {
            dispatch((new ProcessLeadTransaction(
                $transactionId,
                $this->ipAddress,
                number_format(microtime(1)-$startingTransactionTime,2),
                $this->campaignId,
                $this->publisherId,
                json_encode(Input::all()),
                "",
                "error",
                [],
                "Cache data does not exist"
            ))->onQueue('platform-processing'));
            return [ 
                "status" => false,
                "message" => "Campaign (".$campaignId.") does not exist or is inactive.",
                "transaction_time" => number_format(microtime(1)-$startingTransactionTime,2)."s",
                "transaction_id" => $transactionId
            ];
        }

        // get the campaign data, and decode.
        try {
            $campaignData = json_decode(Cache::get("campaign.".$campaignId.".data"),1);
        } catch(\Exception $e) {
            Log::error("JSON not encoded correctly!",[$e->getTraceAsString()]);
            dispatch((new ProcessLeadTransaction(
                $transactionId,
                $this->ipAddress,
                number_format(microtime(1)-$startingTransactionTime,2),
                $this->campaignId,
                $this->publisherId,
                json_encode(Input::all()),
                "",
                "error",
                [],
                $e->getMessage(),
                $campaignPostingURL
            ))->onQueue('platform-processing'));
            return [
                "status" => false,
                "message" => "Campaign (".$campaignId.") was set up incorrectly.",
                "transaction_time" => number_format(microtime(1)-$startingTransactionTime,2)."s",
                "transaction_id" => $transactionId
            ];
        }
        /*
        if($campaignData["consent_type"] != "--Select One--" || $campaignData["campaign_response_type"] == "queued") {
            // Create new lead object
            $leadObject = Lead::create([
                "campaign_id" => $campaignId,
                "publisher_id" => $publisherId
            ]);
            // Execute delayed lead flow process.
            dispatch(
                (new ExecuteDelayedLeadFlowProcess($campaignId, $publisherId, Input::all(),$leadObject))
                ->onQueue('lead-sending-processing')
            );
            // Create api transaction for the delayed lead flow!
            APITransaction::create([
                "transaction_id" => $transactionId,
                "ip_address" => $_SERVER['REMOTE_ADDR'],
                "transaction_time" => number_format(microtime(1)-$startingTransactionTime,2),
                "full_request_url" => "",
                "lead_id" => $leadObject->id,
                "pid" => $publisherId,
                "cid" => $campaignId,
                "payload" => json_encode(Input::all()),
                "error_message" => ""
            ]);
            return [
                "status" => true,
                "message" => "pending",
                "transaction_time" => number_format(microtime(1)-$startingTransactionTime,2)."s",
                "transaction_id" => $transactionId
            ];
        }
        */

        // get the input array.
        $inputArray = Input::all();

        // set transaction variables.
        $this->setTransactionVariables($transactionId,$campaignId,$publisherId,$inputArray,$startingTransactionTime,$campaignData["consent_type"]);

        // pass validation for inclusion(s).
        try {
            $leadTransactionData = $this->traverseInputTable($campaignData["campaign_fields"],$inputArray);
        } catch(\Exception $e) {
            dispatch((new ProcessLeadTransaction(
                $transactionId,
                $this->ipAddress,
                number_format(microtime(1)-$startingTransactionTime,2),
                $this->campaignId,
                $this->publisherId,
                json_encode(Input::all()),
                "",
                "error",
                [],
                $e->getMessage(),
                ""
            ))->onQueue('platform-processing'));
            return [
                "success" => false,
                "message" => $e->getMessage(),
                "transaction_time" => number_format(microtime(1)-$startingTransactionTime,2)."s",
                "transaction_id" => $transactionId
            ];
        }

        // request and shit.
        $guzzleClient = new \GuzzleHttp\Client();

        // posting url
        $campaignPostingURL = $campaignData["request_url"] . '?' . http_build_query($leadTransactionData);

        try {
            $res = $guzzleClient->request(
                        $campaignData["request_method"],
                        $campaignPostingURL
                        // get request options...
                    );
            $responseContents = $res->getBody()->getContents();
        } catch(\Exception $e) {
            dispatch((new ProcessLeadTransaction(
                $transactionId,
                $this->ipAddress,
                number_format(microtime(1)-$startingTransactionTime,2),
                $this->campaignId,
                $this->publisherId,
                json_encode(Input::all()),
                $responseContents,
                "error",
                Input::all(),
                "Advertiser server error (".$e->getMessage().")",
                $campaignPostingURL
            ))->onQueue('platform-processing'));
            return [
                "success" => false,
                "message" => "Advertiser server error.",
                "transaction_time" => number_format(microtime(1)-$startingTransactionTime,2)."s",
                "transaction_id" => $transactionId
            ];
        }

        if (
            str_contains(
                strtolower(
                    $responseContents
                ),
                strtolower(
                    $campaignData["request_success"]
                )
            )
        ) {
            dispatch((new ProcessLeadTransaction(
                $transactionId,
                $this->ipAddress,
                number_format(microtime(1)-$startingTransactionTime,2),
                $this->campaignId,
                $this->publisherId,
                json_encode(Input::all()),
                $responseContents,
                "accepted",
                Input::all(),
                "",
                $campaignPostingURL
            ))->onQueue('platform-processing'));
            return [
                "status" => "accepted",
                "transaction_time" => number_format(microtime(1)-$startingTransactionTime,2)."s",
                "transaction_id" => $transactionId
            ];
        } else {
            dispatch((new ProcessLeadTransaction(
                $transactionId,
                $this->ipAddress,
                number_format(microtime(1)-$startingTransactionTime,2),
                $this->campaignId,
                $this->publisherId,
                json_encode(Input::all()),
                $responseContents,
                "rejected",
                Input::all(),
                "",
                $campaignPostingURL
            ))->onQueue('platform-processing'));
            return [
                "status" => "rejected",
                "transaction_time" => number_format(microtime(1)-$startingTransactionTime,2)."s",
                "transaction_id" => $transactionId
            ];
        }
        
    }

    private function traverseInputTable($campaignFields,$inputArray) {
        $leadTransactionData = [];

        // take roll call.
        foreach($campaignFields as $field) {
            if($field["type"] == "field") {
                if(!in_array($field["key"],array_keys($inputArray))) {
                    throw new \Exception("You did not send the '".$field["key"]."' field.");
                }
            }
        }

        // go through field and inclusion fields.
        foreach($campaignFields as $field) {
            foreach($inputArray as $inputKey => $inputValue) {
                if($field["key"] == $inputKey) {
                    if($field["type"] == "field") {
                        $leadTransactionData[$field["field"]] = $inputValue;
                    }
                    if($field["type"] == "inclusion") {
                        $leadTransactionData[$field["field"]] = $this->handleInclusionFields($field,$inputValue);
                    }       
                }
            }
        }
        // go through random and hardcoded.
        foreach($campaignFields as $field) {
            if($field["type"] == "random") {
                $leadTransactionData[$field["field"]] = $this->handleRandomFields($field);
            }
            if($field["type"] == "hardcoded") {
                $leadTransactionData[$field["field"]] = $this->handleHardcodedFields($field);
            }
            if($field["type"] == "system") {
                $leadTransactionData[$field["field"]] = $this->handleSystemFields($field,$inputArray,$campaignFields);
            }
        }
        return $leadTransactionData;
    }

    private function handleRandomFields($field) {
        // get the value array.
        $valueArray = explode(",", $field["random_value"]);
        return $valueArray[rand(0, count($valueArray) - 1)];
    }
    private function handleHardcodedFields($field) {
        // checking for empty value
        if($field["hardcoded_value"] == null) {
            return " ";
        } else {
            return $field["hardcoded_value"];
        }
    }
    private function handleInclusionFields($field,$value) {
        // get the value array
        $valueArray = explode(",", $field["inclusion_value"]);
        if(in_array($value,$valueArray)) {
            return $value;
        } else {
            throw new \Exception("Value ('".$value."') for field ('".$field["key"]."') not within allowed values ('".$field["inclusion_value"]."')");
        }
    }
    private function handleSystemFields($field,$inputArray,$fields) {
        if($field["system_value"] != "tf_cert") {
            $values = [
                "campaign_id" => $this->campaignId,
                "publisher_id" => $this->publisherId,
                "ip" => "".$this->ipAddress,
                "ua" => "".$this->userAgent,
                "timestamp" => \Carbon\Carbon::now(),
            ];
            return $values[$field["system_value"]];    
        } else {
            // handle trusted form
            $leadArray = $inputArray;

            // Build out the certificate array.
            // TODO: This one's for (Trusted Form)
            $certificateArray = [
                "first_name" => "",
                "last_name" => "",
                "email_address" => "",
                "address1" => "",
                "address2" => "",
                "city" => "",
                "state" => "",
                "zipcode" => "",
                "phonenumber" => "",
                "dob" => ""
            ];

            foreach($leadArray as $key => $value) {
                foreach($fields as $fieldValue) {
                    if($key == $fieldValue["key"]) {
                        if($fieldValue["tf_value"] != null) {
                            $certificateArray[$fieldValue["tf_value"]] = $value;
                        }
                    }
                }
            }
            try {
                $trustedFormCertificate = (new LeadCertificateProvider())->sendLeadInformationToTFMSCrawler(
                    $certificateArray["first_name"],
                    $certificateArray["last_name"],
                    $certificateArray["email_address"],
                    $certificateArray["address1"],
                    $certificateArray["address2"],
                    $certificateArray["city"],
                    $certificateArray["state"],
                    $certificateArray["zipcode"],
                    $certificateArray["phonenumber"],
                    $certificateArray["dob"],
                    // Certificate type
                    $this->consentType
                );
            } catch(\Exception $e) {
                Log::error("Consent Didn't Work!",[$e->getTraceAsString()]);
                throw new \Exception("Advertiser consent error! Contact account manager to fix.");
            }            

            // finally, return the URL or cert..
            return $trustedFormCertificate;
        }
    }

    /**
     * One of the routes in this controller to capture a lead from API.
     * @return array
     */
    public function _capture()
    {
        // generate transaction id
        $transactionId = uniqid() . '-' . md5(microtime());

        // setup..
        $campaignPostingCacheData = [
            "request_url" => "https://hookb.in/Kb8Boxow",
            "request_method" => "POST",
            "success_response" => "true"
        ];
        $pivotInputTable = [
            "first-name" => "fname",
            "last-name" => "lname",
            "email-address" => "email"
        ];
        
        $leadTransactionData = [
            "hvalue" => 23
            // Add hardcoded + random stuff here.. 
        ];
        
        $inputArray = Input::all();

        foreach($inputArray as $key => $value) {
            foreach($pivotInputTable as $pivotKey => $pivotValue) {
                if($key == $pivotKey) {
                    $leadTransactionData[$pivotValue] = $value;
                }
            }
        }

        // check cap and shit..

        $guzzleClient = new \GuzzleHttp\Client();

        $res = $guzzleClient->request(
            $campaignPostingCacheData["request_method"],
            $campaignPostingCacheData["request_url"] . '?' . http_build_query($leadTransactionData)
            // get request options...
        );

        if (
            str_contains(
                strtolower(
                    $res->getBody()->getContents()
                ),
                strtolower(
                    $campaignPostingCacheData["success_response"]
                )
            )
        ) {
            return [
                "status" => "accepted",
                "transaction_id" => $transactionId
            ];
        } else {
            return [
                "status" => "rejected",
                "transaction_id" => $transactionId
            ];
        }

        // capture lead object.
        // - everything in INPUT besides CID/PID .. maybe even off transfer/pivot table!?
        // send to client (100ms)
        // get response back // send based off of success response from CACHE :)
    }

    /**
     * Just telling people to POST.
     *
     * @return array
     */
    public function captureRedirect()
    {
        return [
            "success" => false,
            "message" => "Please POST to this address"
        ];
    }

    private function intakeLeadProcess()
    {
        // Campaign and publisher cache keys.
        $campaignId = intval(Input::get("cid"));
        $publisherId = intval(Input::get("pid"));
        $campaignGetter = "campaign.intake." . $campaignId;
        $publisherGetter = $campaignGetter . ".publisher." . $publisherId;

        if(Cache::has($campaignGetter)) {
            if(Cache::has($publisherGetter)) {
                $campaignData = json_decode(Cache::get($campaignGetter),1);
                $publisherData = json_decode(Cache::get($publisherGetter),1);

                // Check to see if campaign is live.
                if($campaignData["campaign_status"] != "live") {
                    return [
                        "success" => false,
                        "message" => "campaign is not live"
                    ];
                }

                // Check to see if publisher has hit cap
                if($publisherData["current_leads_accepted"] == $publisherData["publisher_cap"]) {
                    return [
                        "success" => false,
                        "message" => "lead cap hit",
                    ];
                }

                // If queued, dispatch a "executeDelayedJob"..
                if($campaignData["campaign_response_type"] == "queued") {
                    // Create new lead object
                    $leadObject = Lead::create([
                        "campaign_id" => $campaignId,
                        "publisher_id" => $publisherId
                    ]);
                    // Execute delayed lead flow process.
                    dispatch(
                        (new ExecuteDelayedLeadFlowProcess($campaignId, $publisherId, Input::all(),$leadObject))
                        ->onQueue('lead-sending-processing')
                    );
                    return [
                        "success" => null,
                        "message" => "processing...",
                        "object" => [
                            "lead_id" => $leadObject->id
                        ]                 
                    ];
                }

                // Finally, send the bloody lead.
                return (new LeadFlowProvider())->processSingleCampaignFlow($campaignId, $publisherId, Input::all());

            } else {
                $publisherLeadCapCeiling = (new ReportingMetricsProvider())->getLeadAcceptedEventsByCampaignPublisher($campaignId,$publisherId);
                $leadCapForPublisher = 0;
                if(PublisherCampaign::whereCampaignId($campaignId)->wherePublisherId($publisherId)->exists()) {
                    // Get publisherCampaign object.
                    $publisherCampaign = PublisherCampaign::whereCampaignId($campaignId)->wherePublisherId($publisherId)->first();
                    $leadCapForPublisher = $publisherCampaign->lead_cap;
                }
                Cache::put($publisherGetter,json_encode([
                    "campaign_id" => $campaignId,
                    "publisher_id" => $publisherId,
                    "publisher_cap" => $leadCapForPublisher,
                    "current_leads_accepted" => $publisherLeadCapCeiling
                ]),1440);
                // Run through the intake process again.
                return $this->intakeLeadProcess();
            }
        } else {
            if(Campaign::whereId($campaignId)->exists()) {
                // Get campaign information (id, cap, status, response type)
                $campaign = Campaign::whereId($campaignId)->first();
                Cache::put($campaignGetter,json_encode([
                    "campaign_id" => $campaignId,
                    "campaign_cap" => intval($campaign->hasAttributeOrEmpty("daily_cap")),
                    "campaign_status" => $campaign->hasAttributeOrEmpty("campaign_status"),
                    "campaign_cpl" => floatval($campaign->hasAttributeOrEmpty("cpl")),
                    "campaign_response_type" => $campaign->hasAttributeOrEmpty("publisher_response_type")
                ]),1440);
                // Run through the intakeLeadProcess, again, with the cache all set.
                return  $this->intakeLeadProcess();
            } else {
                // Campaign does not exist
                return [
                    "success" => false,
                    "message" => "campaign does not exist"
                ];
            }
        }
    }

    /**
     * Our intake lead process.
     */
    private function __intakeLeadProcess()
    {
        /*
            campaign.1
            {
                campaign_id,
                campaign_cap,
                campaign_status,
                publisher_response_type
            },
            campaign.1.publisher.5
            {
                campaign_id,
                publisher_id,
                publisher_cap
            }
        */
        // check to see if cap is hit? (CACHED)
        // either process lead in real time or enqueue it.



        // Track lead as captured.
        dispatch((new TrackPlatformEventAsync("lead.captured", "Lead captured from publisher.", [
            "client_id" => $_SERVER["REMOTE_ADDR"],
            "cid" => Input::get("cid"),
            "pid" => Input::get("pid")
        ]))->onQueue("platform-processing"));

        Log::info("Lead Captured @ " . \Carbon\Carbon::now()->format('Y-m-d H:i:s.u'),[]);

        if(Campaign::whereId(Input::get("cid"))->exists()) {
            // Get campaign object.
            $campaign = Campaign::whereId(Input::get("cid"))->first();

            Log::info("DB Call 1 @ " . \Carbon\Carbon::now()->format('Y-m-d H:i:s.u'),[]);

            if(Publisher::whereId(Input::get("pid"))->exists()) {
                // Get publisher object.
                $publisher = Publisher::whereId(Input::get("pid"))->first();

                Log::info("DB Call 2 @ " . \Carbon\Carbon::now()->format('Y-m-d H:i:s.u'),[]);

                $leadCapForPublisher = 0;
                
                if(PublisherCampaign::whereCampaignId($campaign->id)->wherePublisherId($publisher->id)->exists()) {
                    // Get publisherCampaign object.
                    $publisherCampaign = PublisherCampaign::whereCampaignId($campaign->id)->wherePublisherId($publisher->id)->first();
                    $leadCapForPublisher = $publisherCampaign->lead_cap;

                    Log::info("DB Call 3+4 @ " . \Carbon\Carbon::now()->format('Y-m-d H:i:s.u'),[]);
                }
                
                // Hit cap?
                /*
                if((new ReportingMetricsProvider())->getLeadAcceptedEventsByCampaignPublisher($campaign->id,$publisher->id) < $leadCapForPublisher) {
                */
                if(true) { // hit cap check.. should shave off 14ish seconds

                    Log::info("Lead Accepted? @ " . \Carbon\Carbon::now()->format('Y-m-d H:i:s.u'),[]);

                    // Are we live?
                    if($campaign->hasAttributeOrEmpty("campaign_status") == "live") {

                        Log::info("DB Call 5 @ " . \Carbon\Carbon::now()->format('Y-m-d H:i:s.u'),[]);

                        // Realtime or processing?
                        if($campaign->hasAttributeOrEmpty("publisher_response_type") != "queued") {

                            Log::info("DB Call 6 @ " . \Carbon\Carbon::now()->format('Y-m-d H:i:s.u'),[]);

                            return (new LeadFlowProvider())->processSingleCampaignFlow(Input::get("cid"), Input::get("pid"), Input::all());
                        } else {

                            // Create new lead.
                            $leadObject = new Lead();
                            $leadObject->campaign_id = Input::get("cid");
                            $leadObject->publisher_id = Input::get("pid");
                            $leadObject->save();

                            // Execute delayed lead flow process.
                            dispatch((new ExecuteDelayedLeadFlowProcess(Input::get("cid"), Input::get("pid"), Input::all(),$leadObject))->onQueue('lead-sending-processing'));

                            // Return the shiza.
                            return [
                                "status" => "SUCCESS",
                                "message" => "Pending",
                                "campaign_id" => Input::get("cid"),
                                "publisher_id" => Input::get("pid"),
                                "lead_id" => $leadObject->id
                            ];
                        }
                    } else {
                        return [
                            "status" => false,
                            "message" => "Campaign is not live. Contact account manager."
                        ];
                    }
                } else {
                    return [
                            "status" => false,
                            "message" => "Lead cap hit. Contact account manager for an increase."
                        ];
                }
            } else {
                return [
                    "status" => false,
                    "message" => "Publisher does not exist or is inactive. Contact account manager."
                ];
            }
        } else {
            return [
                "status" => false,
                "message" => "Campaign does not exist. Contact account manager."
            ];
        }
    }

    /**
     * Retrieve the response message.
     *
     * @return array
     */
    private function getResponseMessage()
    {
        // TODO: Captured ? Accepted : Rejected
        return "captured";
    }
}