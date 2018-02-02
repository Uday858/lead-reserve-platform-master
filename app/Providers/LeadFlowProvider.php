<?php

namespace App\Providers;

use App\Campaign;
use App\Jobs\FireLeadSend;
use App\Jobs\TrackPlatformEventAsync;
use App\Lead;
use App\LeadPoint;
use App\Providers\Factories\IPFactory;
use App\Providers\Factories\UserAgentFactory;
use App\Publisher;
use Illuminate\Support\Facades\Log;

class LeadFlowProvider
{
    public $currentCampaign;
    public $publisher;

    /**
     * Variables for storing the IDs as reference.
     */
    public $publisherId, $campaignId;

    /**
     * IP Address and User Agent. Check to make sure it works.
     * @var $ipAddress
     * @var $userAgent
     */
    public $ipAddress, $userAgent;

    /**
     * The lead certificate, if there is one.
     * @var $leadCertificate
     */
    public $leadCertificate;

    /**
     * Process the lead flow process, for a single campaign.
     * @param $campaignId
     * @param $publisherId
     * @param $inputFields
     * @return mixed
     */
    public function processSingleCampaignFlow($campaignId, $publisherId, $inputFields, $lead = null)
    {

        Log::info("File Load @ " . \Carbon\Carbon::now()->format('Y-m-d H:i:s.u'),[]);

        // Set the ids as reference.
        $this->publisherId = $publisherId;
        $this->campaignId = $campaignId;

        // Set the current publisher for the lead flow process.
        $this->setPublisher($publisherId);

        // Set the current campaign id.
        $this->setCampaignId($campaignId);

        // Get the IP Address and Random User Agent.
        $this->initServerClientParameters();

        // Start the lead flow process with lead capture.
        return $this->startLeadFlowProcessWithCapture($inputFields,$lead);
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

    /**
     * Set the publisher.
     * @param $publisherId
     */
    private function setPublisher($publisherId)
    {
        // Set the publisher.
        $this->publisher = Publisher::whereId($publisherId)->first();
    }

    /**
     * Set the current campaign.
     * @param $campaignId
     */
    private function setCampaignId($campaignId)
    {
        $this->currentCampaign = Campaign::whereId($campaignId)->first();
    }

    /**
     * Retrieve the current campaign id.
     */
    private function getCurrentCampaignId()
    {
        return $this->currentCampaign->id;
    }

    /**
     * Validate lead information with .. Exclusion List, Time Check, Suppression Check
     * @param $leadObject
     * @return boolean
     */
    private function validateLead($leadObject)
    {
        return (new LeadValidationProvider())->checkValidations($leadObject, $this->currentCampaign);
    }

    /**
     * #1-Start of the lead flow. (LeadCapture)
     * @param $inputFields
     * @return mixed
     */
    private function startLeadFlowProcessWithCapture($inputFields, $lead = null)
    {
        if($lead == null) {
            // Start the lead object building process.
            $leadObject = new Lead();
            // Save the lead object.
            $leadObject->save();
        } else {
            $leadObject = $lead;
        }

        Log::info("DB Write 1 @ " . \Carbon\Carbon::now()->format('Y-m-d H:i:s.u'),[]);

        // Execute the translation/URL build.
        $extraInputFieldArray = $this->translateLeadAndGenerateExtraInputFieldArray($inputFields, $leadObject);

        // Create extra points from array.
        $this->createLeadPointsFromArray($leadObject->id, $extraInputFieldArray);

        // Validate the lead.
        $leadValidation = $this->validateLead($leadObject);
        if ($leadValidation["status"]) {
            if ($this->isTestLead($extraInputFieldArray)) {
                dispatch((new TrackPlatformEventAsync("lead.test", $leadObject->id . " is a test lead and was not sent.", [
                    "lead_id" => $leadObject->id,
                    "campaign_id" => $this->campaignId,
                    "flow_state" => "test",
                    "reason" => "test"
                ]))->onQueue("platform-processing"));
                return $this->getResponseObject($leadObject, "Success", "Test user information captured.");
            } else {
                // Pre-flight the lead.
                if ($this->presendLead($leadObject)) {

                    // Does current campaign require trusted form
                    if ($this->currentCampaign->hasAttributeOrEmpty("include_consent_cert") === "Yes") {

                        Log::info("DB Call 7 @ " . \Carbon\Carbon::now()->format('Y-m-d H:i:s.u'),[]);

                        $leadCert = $this->pingTFMS($leadObject,($this->currentCampaign->hasAttributeOrEmpty("consent_cert_type") == "trusted_form" ? "trusted_form" : "lead_id"));
                        if ($leadCert != false) {
                            $this->leadCertificate = $leadCert;
                            return $this->sendLead($leadObject->id);
                        } else {
                            dispatch((new TrackPlatformEventAsync("lead.rejected", $leadObject->id . " certification has failed", [
                                "lead_id" => $leadObject->id,
                                "campaign_id" => $this->campaignId,
                                "flow_state" => "leadcertification",
                                "reason" => "consent certificate failed"
                            ]))->onQueue("platform-processing"));
                            return $this->getResponseObject($leadObject, "FAIL", "User information did not pass consent test, contact account manager.");
                        }
                    } else {
                        // Send the lead to the advertiser.
                        return $this->sendLead($leadObject->id);
                    }

                } else {
                    dispatch((new TrackPlatformEventAsync("lead.rejected", $leadObject->id . " has been rejected", [
                        "lead_id" => $leadObject->id,
                        "campaign_id" => $this->campaignId,
                        "flow_state" => "preflight",
                        "reason" => "User information already exists."
                    ]))->onQueue("platform-processing"));
                    return $this->getResponseObject($leadObject, "FAIL", "User information already exists");
                }
            }
        } else {
            dispatch((new TrackPlatformEventAsync("lead.rejected", $leadObject->id . " validation has failed", [
                "lead_id" => $leadObject->id,
                "campaign_id" => $this->campaignId,
                "flow_state" => "validation",
                "reason" => $leadValidation["message"]
            ]))->onQueue("platform-processing"));
            return $this->getResponseObject($leadObject, "FAIL", "User information did not pass validation -- " . $leadValidation["message"]);
        }
    }

    /**
     * Is this lead a test lead?
     * @param $extraInputFields
     * @return bool
     */
    private function isTestLead($extraInputFields)
    {
        $returnValue = false;

        foreach ($extraInputFields as $field => $value) {
            if (strtolower($field) == "test") {
                if(strtolower($value) != "n" || intval($value) != 0) {
                    $returnValue = true;
                }
            }
        }

        return $returnValue;
    }

    /**
     * @param $leadObject
     * @param $status
     * @param $message
     * @param $extraParams
     * @return array
     */
    private function getResponseObject($leadObject, $status, $message, $extraParams = [])
    {
        // Send back a response object.
        return array_merge($this->getBlankResponseObject($leadObject), [
            "status" => $status,
            "message" => $message
        ], $extraParams);
    }

    /**
     * @param $leadObject
     * @return array
     */
    private function getBlankResponseObject($leadObject)
    {
        // Build and return a blank response object.
        return [
            "campaign_id" => $this->currentCampaign->id,
            "publisher_id" => $this->publisher->id,
            // Integer or Object.
            "lead_id" => (is_int($leadObject) ? $leadObject : $leadObject->id)
        ];
    }

    /**
     * Translate the lead object given. Generate extra field array by returning it.
     * @param $inputFields
     * @param $leadObject
     * @return array
     */
    private function translateLeadAndGenerateExtraInputFieldArray($inputFields, &$leadObject)
    {
        // Create extra input field array.
        $extraInputFieldArray = [];

        // Loop through our Input fields.
        foreach ($inputFields as $inputKey => $inputValue) {
            $inputIsDefaultLeadField = false;
            // Loop through our general "lead fields.."
            foreach ($this->generateLeadFields() as $leadKey => $leadValue) {
                if ($inputKey == $leadKey) {
                    $leadObject[$leadValue] = $inputValue;
                    $inputIsDefaultLeadField = true;
                }
            }
            // Input is not default lead field...Let's collect anyway!
            if (!$inputIsDefaultLeadField) {
                if (is_array($inputValue)) {
                    foreach ($inputValue as $key => $value) {
                        if (!is_null($value)) {
                            $extraInputFieldArray[$key] = $value;
                        }
                    }
                } else {
                    if (!is_null($inputValue)) {
                        $extraInputFieldArray[$inputKey] = $inputValue;
                    }
                }
            }
        }

        // Re save?
        $leadObject->save();

        // Return the extra input field array (from lead points.)
        return $extraInputFieldArray;
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

    /**
     * Create lead points from array we provide.
     *
     * @param $leadId
     * @param $array
     */
    private function createLeadPointsFromArray($leadId, $array)
    {
        foreach ($array as $item => $value) {
            LeadPoint::create([
                "campaign_id" => $this->getCurrentCampaignId(),
                "lead_id" => $leadId,
                "key" => $item,
                "value" => $value
            ]);
        }
    }

    /**
     * Presend the lead? -- Preping functionality
     * @param $leadObject
     * @return boolean
     */
    private function presendLead($leadObject)
    {
        return (new LeadSendingProvider())->pingLeadAndGetResponse($this->getCurrentCampaignId(), $this->publisher->id, $this->ipAddress, $this->userAgent, $leadObject->id);
    }

    /**
     * Ping the TrustedFormManagementSystem
     * @param $leadObject
     * @return mixed
     */
    private function pingTFMS($leadObject,$certType = "trusted_form")
    {
        try {
            $certificateLead = $leadObject->getTranslationValueToCertificateProvider($this->campaignId);
            $trustedFormCertificate = (new LeadCertificateProvider())->sendLeadInformationToTFMSCrawler(
                $certificateLead["first_name"],
                $certificateLead["last_name"],
                $certificateLead["email_address"],
                $certificateLead["address1"],
                $certificateLead["address2"],
                $certificateLead["city"],
                $certificateLead["state"],
                $certificateLead["zipcode"],
                $certificateLead["phonenumber"],
                $certificateLead["dob"],
                // Certificate type
                $certType
            );
            return $trustedFormCertificate;
        } catch (\Exception $e) {
            Log::info("Error", [$e->getMessage() . ",,,,,," . $e->getTraceAsString()]);
            return false;
        }
    }

    /**
     * Send lead back to advertiser through our LeadFunnel.
     *
     * @param $leadId
     * @return mixed
     */
    private function sendLead($leadId)
    {
        Log::info("Before Lead Send @ " . \DateTime::createFromFormat('U.u', number_format(microtime(true), 6, '.', ''))->format('Y-m-d H:i:s.u'),[]);

        // Get advertiser response.
        $advertiserResponse = (new LeadSendingProvider())->sendLeadAndGetResponse($this->getCurrentCampaignId(), $this->publisher->id, $this->ipAddress, $this->userAgent, $leadId, $this->leadCertificate);

        Log::info("After Lead Send @ " . \Carbon\Carbon::now()->format('Y-m-d H:i:s.u'),[]);

        if ($advertiserResponse["status"] == "failed") {
            dispatch((new TrackPlatformEventAsync("lead.rejected", $leadId . " has been rejected", [
                "lead_id" => $leadId,
                "campaign_id" => $this->campaignId,
                "publisher_id" => $this->publisherId,
                "flow_state" => "send",
                "reason" => "User information rejected."
            ]))->onQueue("platform-processing"));
            if (isset($advertiserResponse["response"]["detail"])) {
                return $this->getResponseObject($leadId, "FAIL", $advertiserResponse["response"]["message"], [
                    "detail" => $advertiserResponse["response"]["detail"]
                ]);
            } else {
                return $this->getResponseObject($leadId, "FAIL", $advertiserResponse["response"]["message"]);
            }

        } else {
            dispatch((new TrackPlatformEventAsync("lead.accepted", $leadId . " has been accepted by advertiser.", [
                "lead_id" => $leadId,
                "campaign_id" => $this->campaignId,
                "publisher_id" => $this->publisherId
            ]))->onQueue("platform-processing"));
            return array_merge($advertiserResponse["response"], $this->getBlankResponseObject($leadId));
        }
    }
}
