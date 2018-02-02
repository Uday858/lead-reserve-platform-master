<?php

namespace App\Jobs;

use App\Lead;
use App\PlatformEvent;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class BuildEmptyLeadForConversion implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var $campaignId
     */
    public $campaignId;

    /**
     * @var $publisherId
     */
    public $publisherId;

    /**
     * @var $action
     */
    public $action;

    /**
     * @var $leadObject
     */
    private $leadObject;

    /**
     * @var $leadId
     */
    private $leadId;

    /**
     * Create a new job instance.
     *
     * @param $campaignId
     * @param $publisherId
     * @param $action
     * @param $leadId
     */
    public function __construct($campaignId, $publisherId, $action, $leadId=null)
    {
        $this->campaignId = $campaignId;
        $this->publisherId = $publisherId;
        $this->action = $action;
        $this->leadId = $leadId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if($this->leadId == null) {
            $this->buildLeadObjectWithAttributes();
        }
        $this->handleJobActionable();
    }

    /**
     * Build out an empty lead object to retain an ID.
     */
    private function buildLeadObjectWithAttributes()
    {
        // This functionality makes sure we allocate an insert ID for leads.
        $this->leadObject = new Lead();
        $this->leadObject->save();
    }

    /**
     * @param null $leadId
     */
    private function handleJobActionable($leadId=null)
    {
        // Formatting the lead id, correctly.
        $leadId = ($leadId==null) ? $this->leadObject->id : $leadId;

        // Make sure the action is correct.
        switch($this->action) {
            case "conversion":
                dispatch((new TrackPlatformEventAsync("lead.conversion","Lead converted from a CPA/Linkout campaign.",[
                    "cid" => $this->campaignId,
                    "pid" => $this->publisherId,
                    "lead_id" => $leadId
                ]))->onQueue("platform-processing"));
                break;
            case "linkout":
                dispatch((new TrackPlatformEventAsync("lead.click","Lead clicked from a CPA/Linkout campaign.",[
                    "cid" => $this->campaignId,
                    "pid" => $this->publisherId,
                    "lead_id" => $leadId
                ]))->onQueue("platform-processing"));
                break;
        }
    }
}
