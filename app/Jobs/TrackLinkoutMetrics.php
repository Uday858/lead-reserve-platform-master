<?php

namespace App\Jobs;

use App\Campaign;
use App\MetricClick;
use App\Providers\Connectors\TwilioAPIConnector;
use App\Providers\PlatformEventHandlerServiceProvider;
use App\Publisher;
use App\PublisherCampaign;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class TrackLinkoutMetrics implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var $click
     */
    public $click;

    /**
     * @var $publisher
     */
    public $publisher;

    /**
     * @var $campaign
     */
    public $campaign;

    /**
     * @var $publisherCampaign
     */
    public $publisherCampaign;

    /**
     * @var $metrics
     */
    public $metrics;

    /**
     * Create a new job instance.
     * @param $clickId
     * @return mixed
     */
    public function __construct($clickId)
    {
        if (MetricClick::whereId($clickId)->exists()) {
            $this->click = MetricClick::whereId($clickId)->first();
            $this->publisher = Publisher::whereId($this->click->publisher_id)->first();
            $this->campaign = Campaign::whereId($this->click->campaign_id)->first();
            $this->publisherCampaign = PublisherCampaign::whereCampaignId($this->click->campaign_id)->wherePublisherId($this->click->publisher_id)->first();
            $this->metrics = [
                "cpl" => $this->campaign->hasAttributeOrEmpty("cpl"),
                "payout" => $this->publisherCampaign->payout,
                "net" => ($this->campaign->hasAttributeOrEmpty("cpl") - $this->publisherCampaign->payout)
            ];
        } else {
            $this->click = -1;
        }
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if(isset($this->click->id)) {
            // (new TwilioAPIConnector())->sendMessage("$".number_format($this->metrics["net"],2)." conversion for: " . $this->campaign->name);

            dispatch((new TrackPlatformEventAsync("revenue.track","Track revenue from lead conversion.",[
                "click_id" => intval($this->click->id),
                "campaign_id" => intval($this->campaign->id),
                "publisher_id" => intval($this->publisher->id),
                "lead_status" => "converted",
                "cpl" => floatval($this->metrics["cpl"]),
                "payout" => floatval($this->metrics["payout"]),
                "net" => floatval($this->metrics["net"]),
            ]))->onQueue("platform-processing"));

        } else {
            $this->fail();
        }
    }
}
