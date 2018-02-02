<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Providers\LeadFlowProvider;
use Illuminate\Support\Facades\Log;

class ExecuteDelayedLeadFlowProcess implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $campaignId,$publisherId,$inputFields,$leadObject;
    public $timeout = 200;
    public $tries = 2;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($campaignId,$publisherId,$inputFields,$leadObject)
    {
        $this->campaignId = $campaignId;
        $this->publisherId = $publisherId;
        $this->inputFields = $inputFields;
        $this->leadObject = $leadObject;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            // Execute the lead flow campaign flow.
            return (new LeadFlowProvider())->processSingleCampaignFlow(
                $this->campaignId,
                $this->publisherId,
                $this->inputFields,
                $this->leadObject
            );
        } catch(\Exception $e) {
            Log::error("Delayed Lead Flow Process Fail - " . "Campaign (".$this->campaignId.") Publisher (".$this->publisherId.") Input Fields (".json_encode($this->inputFields).") Lead Object (".json_encode($this->leadObject).") - Exception Message (".$e->getMessage().")");
        }
    }
}
