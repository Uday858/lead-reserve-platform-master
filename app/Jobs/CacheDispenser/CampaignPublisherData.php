<?php
namespace App\Jobs\CacheDispenser;

use App\Jobs\CacheDispenser\Dispenser;

use App\Campaign;
use App\Publisher;
use App\PublisherCampaign;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

use App\Providers\ReportingMetricsProvider;

class CampaignPublisherData extends Dispenser {
	
	public $cacheKey = "campaign.{campaignId}.publisher.{publisherId}.data";

	public function generate() {
		foreach($this->getAllCampaignsInCache() as $campaignData) {
			$data = json_decode($campaignData,1);
			$campaign = Campaign::whereId($data["campaign_id"])->first();
			foreach($this->getPublishersForCampaign($campaign) as $publisherId) {
				Cache::put(str_replace(['{campaignId}','{publisherId}'],[$campaign->id,$publisherId],$this->cacheKey),json_encode($this->generateCampaignPublisherData($data["campaign_id"],$publisherId)),1440); // 2 mins! .. Hopefully this should be long/short enough for proper attribution of campaign listings.
			}
		}
	}

	private function generateCampaignPublisherData($campaignId,$publisherId) {
		// TODO: fix THIS!
		// $currentLeadsSent = (new ReportingMetricsProvider())->getLeadAcceptedEventsByCampaignPublisher($campaignId,$publisherId);
		// $publisherCampaign = PublisherCampaign::whereCampaignId($campaignId)->wherePublisherId($publisherId)->first();
        // $leadCapForPublisher = $publisherCampaign->lead_cap;
        
        return [
            "campaign_id" => $campaignId,
            "publisher_id" => $publisherId,
            "publisher_cap" => 1000,
            "current_leads_accepted" => 0
        ];
	}

	private function getAllCampaignsInCache() {
		// data
        $cacheData = []; // for campaign data.
        // get live campaign ids
        foreach($this->fetchAllLiveCampaignIds() as $campaignId) {
            $cacheData[] = Cache::get("campaign.".$campaignId.".data");
        }
        return $cacheData;
	}

	private function getPublishersForCampaign($campaign) {
		return collect($campaign->publishers)->map(function($x){ return $x->id; })->toArray();
	}

	private function fetchAllLiveCampaignIds() {
		// todo: flatten this bitch.
		$data = DB::select('SELECT * FROM view_active_campaign_ids;'); 
		return collect($data)->map(function($x){ return $x->campaign_id; })->toArray();
	}

}