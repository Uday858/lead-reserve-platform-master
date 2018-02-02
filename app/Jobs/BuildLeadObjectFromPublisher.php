<?php

namespace App\Jobs;

use App\Lead;
use App\LeadPoint;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;

class BuildLeadObjectFromPublisher implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var array $inputFields
     */
    public $inputFields;

    /**
     * @var integer $campaignId
     */
    public $campaignId;

    /**
     * @var integer $publisherId
     */
    public $publisherId;

    public $ipAddress;
    public $userAgent;

    /**
     * Create a new job instance.
     *
     * @param $campaignId
     * @param $publisherId
     * @param $inputFields
     */
    public function __construct($campaignId, $publisherId, $inputFields, $ipAddress, $userAgent)
    {
        $this->campaignId = $campaignId;
        $this->publisherId = $publisherId;
        $this->inputFields = $inputFields;
        $this->ipAddress = $ipAddress;
        $this->userAgent = $userAgent;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // Start the lead object building process.
        $leadObject = new Lead();

        // Create extra input field array.
        $extraInputFieldArray = [];

        // Loop through our Input fields.
        foreach ($this->inputFields as $inputKey => $inputValue) {
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
                if(is_array($inputValue)) {
                    foreach($inputValue as $key => $value) {
                        if(!is_null($value)) {
                            $extraInputFieldArray[$key] = $value;
                        }
                    }
                } else {
                    if(!is_null($inputValue)) {
                        $extraInputFieldArray[$inputKey] = $inputValue;
                    }
                }
            }
        }

        // Save the lead object.
        $leadObject->save();

        // Create extra points from array.
        $this->createLeadPointsFromArray($leadObject->id,$extraInputFieldArray);

        // Dispatch the Lead Firing Job.
        $this->sendLeadToAdvertiser($leadObject->id);
    }

    /**
     * Create lead points from array we provide.
     *
     * @param $leadId
     * @param $array
     */
    private function createLeadPointsFromArray($leadId, $array) {
        foreach($array as $item => $value) {
            LeadPoint::create([
                "campaign_id" => $this->campaignId,
                "lead_id" => $leadId,
                "key" => $item,
                "value" => $value
            ]);
        }
    }

    /**
     * Send lead back to advertiser through our LeadFunnel.
     *
     * @param $leadId
     */
    private function sendLeadToAdvertiser($leadId)
    {
        // Send off new FireLeadSend.
        dispatch((new FireLeadSend(
            $this->campaignId,
            $this->publisherId,
            $this->ipAddress,
            $this->userAgent,
            $leadId
        ))->onQueue("lead-processing"));
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
