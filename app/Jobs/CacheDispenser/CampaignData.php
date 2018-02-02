<?php
namespace App\Jobs\CacheDispenser;

use App\Jobs\CacheDispenser\Dispenser;

use App\Campaign;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class CampaignData extends Dispenser {

	/**
	* Key to get/set information
	* @var $cacheKey
	*/
	public $cacheKey = "campaign.{id}.data";

	/**
	* Generate campaign data, as per class title, and push into cache.
	*/
	public function generate() {
		// Fetch all campaigns.
		$liveCampaigns = $this->fetchAllLiveCampaignIds();
		foreach($liveCampaigns as $id) {
			// generate campaign data for this id.
			$campaignData = $this->generateCampaignData($id);
			// put data in cache.
			Cache::put(str_replace("{id}",$id,$this->cacheKey),json_encode($campaignData),1440);
		}
	}

	/**
	* Fetch all IDs associated with live campaigns.
	*/
	private function fetchAllLiveCampaignIds() {
		// todo: flatten this bitch.
		$data = DB::select('SELECT * FROM view_active_campaign_ids;'); 
		return collect($data)->map(function($x){ return $x->campaign_id; })->toArray();
	}

	/**
	* Generate the campaign data for cache.
	* @var $campaignId
	*/
	private function generateCampaignData(int $campaignId) {
        // Get campaign information (id, cap, status, response type)
        $campaign = Campaign::whereId($campaignId)->first();
        return [
        	// Campaign Attributes
            "campaign_id" => $campaignId,
            "campaign_cap" => intval($campaign->hasAttributeOrEmpty("daily_cap")),
            "campaign_status" => $campaign->hasAttributeOrEmpty("campaign_status"),
            "campaign_cpl" => floatval($campaign->hasAttributeOrEmpty("cpl")),
            "campaign_response_type" => $campaign->hasAttributeOrEmpty("publisher_response_type"),

            // Request
            "request_url" => $campaign->posting_url,
            "request_method" => $campaign->hasAttributeOrEmpty("posting_method"),
            "request_success" => $campaign->hasAttributeOrEmpty("success_response"),

            // Consent
            "consent_type" => $campaign->hasAttributeOrEmpty("consent_cert_type"),

            // Pre-ping
            "preping_posting_method" => $campaign->hasAttributeOrEmpty("preping_posting_method"),
            "preping_url" => $campaign->hasAttributeOrEmpty("preping_url"),
            "preping_success_response" => $campaign->hasAttributeOrEmpty("preping_success_response"),

            // Fields..
            "campaign_fields" => $this->generateCampaignFields($campaignId)
        ];
	}

	/**
	* Generate the campaign field array.
	* @var $campaignId
	*/
	private function generateCampaignFields(int $campaignId) {
		$campaign = Campaign::whereId($campaignId)->first();
		$fieldData = [];
		foreach($campaign->fields as $field) {
			//$fieldData[] = $field;
			$fieldData[] = [
				"key" => $field->incoming_field,
				"type" => $field->type,
				"field" => $field->outgoing_field,
				"tf_value" => $field->tf_value,
				"random_value" => $field->random_value,
				"inclusion_value" => $field->inclusion_value,
				"hardcoded_value" => $field->hardcoded_value,
				"system_value" => $field->system_value,
			];
		}
		return $fieldData;
	}
}