<?php

namespace App\Jobs;

use App\Campaign;
use App\Providers\LeadMoldingProvider;
use App\PublisherCampaign;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;

class TrackLeadMetrics implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var integer $leadId
     */
    public $leadId;

    /**
     * @var integer $campaignId
     */
    public $campaignId;

    /**
     * @var mixed $campaign
     */
    public $campaign;

    /**
     * @var integer $publisherId
     */
    public $publisherId;

    /**
     * @var integer $status
     */
    public $status;

    /**
     * Create a new job instance.
     *
     * @param $leadId
     * @param $campaignId
     * @param $publisherId
     * @param $status
     */
    public function __construct($leadId,$campaignId,$publisherId,$status)
    {
        $this->leadId = $leadId;
        $this->campaignId = $campaignId;
        $this->campaign = Campaign::whereId($campaignId)->first();
        $this->publisherId = $publisherId;
        $this->status = $status;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // Track the lead revenue.
        $this->generateRevenueEvents();
    }

    /**
     * Track the revenue metrics for this this lead.
    */
    private function generateRevenueEvents()
    {
        if(!PublisherCampaign::whereCampaignId($this->campaignId)->wherePublisherId($this->publisherId)->exists()) {
            Log::error("Publisher is not assigned to campaign!",[$this->campaignId,$this->publisherId]);
            return;
        }

        // Retrieve the CPL from the campaign and the payout to the publisher.
        $campaignCPL = $this->campaign->hasAttributeOrEmpty("cpl");

        $publisherPayout = PublisherCampaign::whereCampaignId($this->campaignId)->wherePublisherId($this->publisherId)->first()->payout;
        $net = $campaignCPL - $publisherPayout;

        // Create RevenueEvent.
        dispatch((new TrackPlatformEventAsync("revenue.track","Track revenue from lead post.",[
            "lead_id" => intval($this->leadId),
            "campaign_id" => intval($this->campaignId),
            "publisher_id" => intval($this->publisherId),
            "lead_status" => $this->status,
            "cpl" => ($this->status=="accepted") ? floatval($campaignCPL) : 0,
            "payout" => ($this->status=="accepted") ? floatval($publisherPayout) : 0,
            "net" => ($this->status=="accepted") ? floatval($net) : 0,
        ]))->onQueue("platform-processing"));
    }

    /**
     * Track the financial impact per lead.
     */
    private function trackLeadRevenueMetrics()
    {
        if($this->campaign->type->name == "Leadgen" || $this->campaign->type->name == "CPL") {
            // Retrieve the status of the lead.
            $leadStatus = (new LeadMoldingProvider())->generateStatusForLead($this->leadId);
        } else {
            // TODO: By definition the acceptance of a CPL or Linkout lead is on conversion.
            $leadStatus = "accepted";
        }

        // Retrieve the CPL from the campaign and the payout to the publisher.
        $campaignCPL = Campaign::whereId($this->campaignId)->first()->hasAttributeOrEmpty("cpl");

        $publisherPayout = PublisherCampaign::whereCampaignId($this->campaignId)->wherePublisherId($this->publisherId)->first()->payout;
        $net = $campaignCPL - $publisherPayout;

        // Create RevenueEvent.
        dispatch((new TrackPlatformEventAsync("revenue.track","Track revenue from lead post.",[
            "lead_id" => intval($this->leadId),
            "campaign_id" => intval($this->campaignId),
            "publisher_id" => intval($this->publisherId),
            "lead_status" => $leadStatus,
            "cpl" => ($leadStatus=="accepted") ? floatval($campaignCPL) : 0,
            "payout" => ($leadStatus=="accepted") ? floatval($publisherPayout) : 0,
            "net" => ($leadStatus=="accepted") ? floatval($net) : 0,
        ]))->onQueue("platform-processing"));
    }
}
