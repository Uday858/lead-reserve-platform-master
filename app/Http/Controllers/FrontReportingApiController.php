<?php

namespace App\Http\Controllers;

use App\Campaign;
use App\CampaignAttribute;
use App\PlatformEvent;
use App\PlatformReport;
use App\Providers\PlatformEventHandlerServiceProvider;
use App\Providers\ReportingMetricsProvider;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;

class FrontReportingApiController extends Controller
{
    /**
     * Get the current week's revenue, based off of "linkout.conversion" and "revenue.track" events.
     * @return array
     */
    public function weeklyFinances()
    {
        return PlatformReport::where('timestamp','>=',Carbon::today()->startOfDay()->subDay(30))
            ->where('timestamp','<=',Carbon::today()->endOfDay())
            ->groupBy('timestamp')
            ->get([DB::raw('date(timestamp) as day,sum(revenue) as revenue,sum(payout) as payout,(sum(revenue)-sum(payout)) as net')]);
    }

    public function weeklyLeads()
    {
        return PlatformReport::where('timestamp','>=',Carbon::today()->startOfDay()->subDay(7))
            ->where('timestamp','<=',Carbon::today()->endOfDay())
            ->groupBy('timestamp')
            ->get([DB::raw('date(timestamp) as day,sum(leads_generated) as leads_generated,sum(leads_accepted) as leads_accepted,sum(leads_rejected) as leads_rejected')]);
    }

    /**
     * Get the current campaign type split.
     * @return array
     */
    public function campaignTypeSplit()
    {
        // Retrieve all active campaigns.
        $activeCampaigns = [];
        foreach (Campaign::all() as $campaign) {
            if ($campaign->hasAttributeOrEmpty("campaign_status") == "live") {
                $activeCampaigns[] = $campaign->type->name;
            }
        }

        // Split up the campaigns + percentage values.
        $campaignSplit = [];
        foreach (array_count_values($activeCampaigns) as $campaignTypeLabel => $amountOfCampaigns) {
            $campaignSplit[$campaignTypeLabel] = ($amountOfCampaigns / count($activeCampaigns)) * 100;
        }

        // Return the campaign split array (as JSON response).
        return $campaignSplit;
    }

    /**
     * Create leadgen and link out ratios for stacked bar graph.
     * @return array
     */
    public function acceptRejectLeads()
    {
        $globalMetrics = (new ReportingMetricsProvider())->returnGlobalMetrics(Carbon::today()->subDay(7), Carbon::now());
        return [
            "Leadgen Ratio" => [$globalMetrics["leadgenAmounts"]["leads_accepted"], $globalMetrics["leadgenAmounts"]["leads_rejected"]],
            "Linkout Ratio" => [$globalMetrics["linkoutAmounts"]["conversions"], $globalMetrics["linkoutAmounts"]["impressions"]]
        ];
    }

    /**
     * @return array
     */
    public function campaignPerformance()
    {
        $fromDate = Carbon::today();
        $toDate = Carbon::now();

        if (Input::get("fromDate")) {
            $fromDate = Carbon::parse(Input::get("fromDate"));
        }

        if (Input::get("toDate")) {
            $toDate = Carbon::parse(Input::get("toDate"));
        }

        return /*collect(*/(new ReportingMetricsProvider())->retrieveOnlyCampaignLeadgenDataWithinTimeframe($fromDate, $toDate);/*)->sortBy('net_generated',SORT_REGULAR,true)->toArray();*/
    }

    /**
     * @param $campaignId
     * @return array
     */
    public function publisherPerformancePerCampaign($campaignId)
    {
        $fromDate = Carbon::today();
        $toDate = Carbon::now();

        if (Input::get("fromDate")) {
            $fromDate = Carbon::parse(Input::get("fromDate"));
        }

        if (Input::get("toDate")) {
            $toDate = Carbon::parse(Input::get("toDate"));
        }

        return /*collect(*/(new ReportingMetricsProvider())->returnCampaignPublishersReportingMetrics($campaignId, $fromDate, $toDate);//)->sortBy('publisher_revenue',SORT_REGULAR,true)->toArray();
    }
}
