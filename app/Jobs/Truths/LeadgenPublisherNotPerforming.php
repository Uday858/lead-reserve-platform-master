<?php

namespace App\Jobs\Truths;

use App\Providers\ReportingMetricsProvider;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\PublisherCampaign;
use App\CampaignPublisherReport;

class LeadgenPublisherNotPerforming extends Truth {
	/**
	*	Check to see if publisher cap is less or more than the passed in percentage.
	*	@param $campaignId
	*	@param $percentageToCheckAgainst
	*	@param $fromDate
	*	@param $toDate
	*/
	public function capNotMet($campaignId,
							  $publisherId,
							  $percentageToCheckAgainst,
							  $fromDate,
							  $toDate) {
		// Get the lead cap. (Daily)
		$leadCap = PublisherCampaign::wherePublisherId($publisherId)->whereCampaignId($campaignId)->first()->lead_cap;
		// Get the reports accepted between time frame.
		$reports = CampaignPublisherReport::where("timestamp",">=",$fromDate)
                                  ->where("timestamp","<=",$toDate)
                                  ->where("campaign_id",$campaignId)
                                  ->where("publisher_id",$publisherId)
                                  ->get([DB::raw("count(timestamp) as days,sum(leads_accepted) as accepted")])
                                  ->first()->toArray();
		if($reports["days"] != 0) {
			// Get the average fill cap.                                  
			$averageFillCap = ceil($reports["accepted"]/$reports["days"]);
			// Turn it into a percentage
			$capPercentage = ($averageFillCap/$leadCap)*100;
			// Return value.
			return ($percentageToCheckAgainst <= $capPercentage);
		} else {
			// TRUE for not performing and not hitting cap.
			return true;
		}
	}

	/**
	*	Check to see if publisher performance is rejecting at a certain rate.
	*	@param $campaignId
	*	@param $percentageToCheckAgainst
	*	@param $fromDate
	*	@param $toDate
	*/
	public function rejectRateIsHigh($campaignId,
							  $publisherId,
							  $percentageToCheckAgainst,
							  $fromDate,
							  $toDate) {
		$reports = CampaignPublisherReport::where("timestamp",">=",$fromDate)
                                  ->where("timestamp","<=",$toDate)
                                  ->where("campaign_id",$campaignId)
                                  ->where("publisher_id",$publisherId)
                                  ->get(["leads_accepted","leads_generated"]);
        if(count($reports) != 0) {
        	// Compare.
        	return ($percentageToCheckAgainst <= (100-(($reports->sum("leads_accepted")/$reports->sum("leads_generated"))*100)));
        } else {
        	// Nothing was found.
        	return false;
        }
	}
}