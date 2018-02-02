<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Providers\ReportingMetricsProvider;
use App\CampaignReport;
use App\CampaignPublisherReport;
use App\MetricClick;
use App\MetricConversion;
use App\MetricImpression;
use App\PlatformReport;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class BuildReport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    // Create job variables.
    public $typeOfReport,
           $date,
           $campaignId,
           $publisherId;

    //(new \App\Jobs\BuildReport("platform","2017-10-01"))->handle();

    /**
     * Create a new job instance.
     *
     * @param $fromDate
     * @param $toDate
     * @return void
     */
    public function __construct($typeOfReport,$date,$campaignId = null,$publisherId = null)
    {
        $this->typeOfReport = $typeOfReport;
        $this->date = $date;
        $this->campaignId = $campaignId;
        $this->publisherId = $publisherId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if($this->typeOfReport == "platform") {
            $this->rebuildPlatformReport($this->date);
        }
        else if($this->typeOfReport == "platform.daily") {
            $this->savePlatformReport($this->date);
        }
    }

    /**
    * @param $date
    */
    protected function savePlatformReport($date) 
    {
        Log::info("Saving Daily Platform Report",[$date]);
        // Generate the object to save.
        $platformReport = $this->generatePlatformReport($date);
        // Create a platform report
        PlatformReport::create([
            "report_guid" => uniqid(),
            "timestamp" => $platformReport["timestamp"],
            "leads_generated" => $platformReport["leadgen_data"]["data"]["generated"],
            "leads_accepted" => $platformReport["leadgen_data"]["data"]["accepted"],
            "leads_rejected" => $platformReport["leadgen_data"]["data"]["rejected"],
            "metric_impressions" => $platformReport["linkout_data"]["data"]["impressions"],
            "metric_clicks" => $platformReport["linkout_data"]["data"]["clicks"],
            "metric_conversions" => $platformReport["linkout_data"]["data"]["conversions"],
            "revenue" => $platformReport["financial_data"]["revenue"],
            "payout" => $platformReport["financial_data"]["payout"],
            "margin" => $platformReport["financial_data"]["net"],
            "cache_data" => json_encode($platformReport)
        ]);
        // Go through the campaign data and per the data, create CampaignReport(s) and CampaignPublisherReport(s).
        foreach($platformReport["campaign_data"] as $campaign) {
            CampaignReport::create([
                "report_guid" => uniqid(),
                "timestamp" => $platformReport["timestamp"],
                "campaign_id" => $campaign["campaign_id"],
                "leads_generated" => (isset($campaign["leads_generated"])?$campaign["leads_generated"]:0),
                "leads_accepted" => (isset($campaign["leads_accepted"])?$campaign["leads_accepted"]:0),
                "leads_rejected" => (isset($campaign["leads_rejected"])?$campaign["leads_rejected"]:0),
                "metric_impressions" => (isset($campaign["metric_impressions"])?$campaign["metric_impressions"]:0),
                "metric_clicks" => (isset($campaign["metric_clicks"])?$campaign["metric_clicks"]:0),
                "metric_conversions" => (isset($campaign["metric_conversions"])?$campaign["metric_conversions"]:0),
                "revenue" => (isset($campaign["revenue"])?$campaign["revenue"]:0),
                "payout" => (isset($campaign["payout"])?$campaign["payout"]:0),
                "margin" => (isset($campaign["margin"])?$campaign["margin"]:0),
                "cache_data" => json_encode($campaign)
            ]);
            foreach($campaign["publishers"] as $publisher) {
                CampaignPublisherReport::create([
                    "report_guid" => uniqid(),
                    "timestamp" => $platformReport["timestamp"],
                    "campaign_id" => $publisher["campaign_id"],
                    "publisher_id" => $publisher["publisher_id"],
                    "leads_generated" => (isset($publisher["leads_generated"])?$publisher["leads_generated"]:0),
                    "leads_accepted" => (isset($publisher["leads_accepted"])?$publisher["leads_accepted"]:0),
                    "leads_rejected" => (isset($publisher["leads_rejected"])?$publisher["leads_rejected"]:0),
                    "metric_impressions" => (isset($publisher["metric_impressions"])?$publisher["metric_impressions"]:0),
                    "metric_clicks" => (isset($publisher["metric_clicks"])?$publisher["metric_clicks"]:0),
                    "metric_conversions" => (isset($publisher["metric_conversions"])?$publisher["metric_conversions"]:0),
                    "revenue" => (isset($publisher["revenue"])?$publisher["revenue"]:0),
                    "payout" => (isset($publisher["payout"])?$publisher["payout"]:0),
                    "margin" => (isset($publisher["margin"])?$publisher["margin"]:0),
                    "cache_data" => json_encode($publisher)
                ]);
            }
        }
    }

    /**
    * @param $fromDate
    * @param $toDate
    */
    protected function rebuildPlatformReport($date)
    {
        Log::info("Dashboard data cache update for :: ",[$date]);
        Cache::put('platform.dashboard.data',json_encode($this->generatePlatformReport($date)),60);
    }

    /**
    * @param $date
    */
    private function generatePlatformReport($date)
    {
        $fromDate = Carbon::parse($date)->startOfDay();
        $toDate = Carbon::parse($date)->endOfDay();
        return array_merge([
            "timestamp" => Carbon::parse($fromDate)->toDateTimeString(),
            "financial_data" => (new ReportingMetricsProvider())->fetchRevenueNumbersForTimeframe($fromDate,$toDate)
        ],$this->calculateLeadStatuses($fromDate,$toDate),[
            "campaign_data" => (new ReportingMetricsProvider())->getLeadgenPerformance($fromDate,$toDate)
        ]);
    }

    /**
     * @param $fromDate
     * @param $toDate
     * @return array
     */
    private function calculateLeadStatuses($fromDate, $toDate)
    {
        // I want to get the amount of leads accepted today, in general.
        $leadStatuses = (new ReportingMetricsProvider())->returnAcceptedRejectedLeadsBetweenTimeframe($fromDate,$toDate);

        // Return information
        return [
            "leadgen_data" => [
                "data" => [
                    "generated" => $leadStatuses["generated"],
                    "accepted" => $leadStatuses["accepted"],
                    "rejected" => $leadStatuses["rejected"],
                ],
                "metrics" => [
                    "accept_percentage" => $leadStatuses["accepted"] / ($leadStatuses["generated"]==0?1:$leadStatuses["generated"]),
                    "reject_percentage" => $leadStatuses["rejected"] / ($leadStatuses["generated"]==0?1:$leadStatuses["generated"])
                ]
            ],
            "linkout_data" => [
                "data" => [
                    "impressions" => MetricImpression::where("created_at",">=",$fromDate)->where("created_at","<=",$toDate)->count(),
                    "clicks" => MetricClick::where("created_at",">=",$fromDate)->where("created_at","<=",$toDate)->count(),
                    "conversions" => MetricConversion::where("created_at",">=",$fromDate)->where("created_at","<=",$toDate)->count()
                ]
            ]    
        ];
    }
}