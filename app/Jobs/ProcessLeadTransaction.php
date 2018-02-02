<?php

namespace App\Jobs;

use App\Lead;
use App\LeadPoint;
use App\APITransaction;
use App\Jobs\TrackLeadMetrics;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;

class ProcessLeadTransaction implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $transactionId,$ipAddress,$transactionTime,$campaignId,$publisherId,$requestPayload,$responseFromAdvertiser,$errorMessage,$leadStatus,$requestURL,$inputFields;

    // Only try twice.
    public $tries = 2;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(
        $transactionId,
        $ipAddress,
        $transactionTime,
        $campaignId,
        $publisherId,
        $requestPayload,
        $responseFromAdvertiser,
        $leadStatus,
        $inputFields=[],
        $errorMessage="",
        $requestURL="")
    {
        $this->transactionId = $transactionId;
        $this->ipAddress = $ipAddress;
        $this->transactionTime = $transactionTime;
        $this->campaignId = $campaignId;
        $this->publisherId = $publisherId;
        $this->requestPayload = $requestPayload;
        $this->responseFromAdvertiser = $responseFromAdvertiser;
        $this->leadStatus = $leadStatus;
        $this->inputFields = $inputFields;
        $this->errorMessage = $errorMessage;
        $this->requestURL = $requestURL;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            if($this->leadStatus == "error") {
                APITransaction::create([
                    "transaction_id" => $this->transactionId,
                    "ip_address" => $this->ipAddress,
                    "transaction_time" => $this->transactionTime,
                    "full_request_url" => $this->requestURL,
                    "lead_id" => 0,
                    "pid" => null,
                    "cid" => null,
                    "lead_status" => $this->leadStatus,
                    "payload" => $this->requestPayload,
                    "response_from_advertiser" => $this->responseFromAdvertiser,
                    "error_message" => $this->errorMessage
                ]);
                Log::error("Campaign " . $this->campaignId . " : Publisher " . $this->publisherId . " -- " . $this->errorMessage,[1]);
                return 1;
            }

            // create the api transaction model
            $apiTransaction = APITransaction::create([
                "transaction_id" => $this->transactionId,
                "ip_address" => $this->ipAddress,
                "transaction_time" => $this->transactionTime,
                "full_request_url" => $this->requestURL,
                "lead_id" => 0,
                "pid" => $this->campaignId,
                "cid" => $this->publisherId,
                "payload" => $this->requestPayload,
                "lead_status" => $this->leadStatus,
                "response_from_advertiser" => $this->responseFromAdvertiser,
                "error_message" => $this->errorMessage
            ]);
            // create the lead
            $leadObject = Lead::create([
                "campaign_id" => $this->campaignId,
                "publisher_id" => $this->publisherId
            ]);

            $apiTransaction->lead_id = $leadObject->id;
            $apiTransaction->save();

            // Execute the translation/URL build.
            $inputFields = $this->inputFields;
            $extraInputFieldArray = $this->translateLeadAndGenerateExtraInputFieldArray($inputFields, $leadObject);
            // Create extra points from array.
            $this->createLeadPointsFromArray($leadObject->id, $extraInputFieldArray);
            if($this->leadStatus == "accepted") {
                dispatch((new TrackPlatformEventAsync("lead.accepted", $leadObject->id . " has been accepted by advertiser.", [
                    "lead_id" => $leadObject->id,
                    "campaign_id" => $this->campaignId,
                    "publisher_id" => $this->publisherId
                ]))->onQueue("platform-processing"));
                dispatch((new TrackLeadMetrics(
                    $leadObject->id,
                    $this->campaignId,
                    $this->publisherId,
                    "accepted")
                )->onQueue("platform-processing"));
            } else if($this->leadStatus == "rejected") {
                dispatch((new TrackPlatformEventAsync("lead.rejected", $leadObject->id . " has been rejected", [
                    "lead_id" => $leadObject->id,
                    "campaign_id" => $this->campaignId,
                    "publisher_id" => $this->publisherId,
                    "flow_state" => "newsend",
                    "reason" => "User information rejected."
                ]))->onQueue("platform-processing"));
                dispatch((new TrackLeadMetrics(
                    $leadObject->id,
                    $this->campaignId,
                    $this->publisherId,
                    "rejected")
                )->onQueue("platform-processing"));
            }
        } catch(\Exception $e) {
            Log::error("Process Lead Transaction Failed...",[$e->getMessage()]);
        }
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
                "campaign_id" => $this->campaignId,
                "lead_id" => $leadId,
                "key" => $item,
                "value" => $value
            ]);
        }
    }
}
