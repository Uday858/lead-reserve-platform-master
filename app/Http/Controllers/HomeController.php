<?php

namespace App\Http\Controllers;

use App\Advertiser;
use App\Lead;
use App\MetricClick;
use App\MetricConversion;
use App\MetricImpression;
use App\PlatformEvent;
use App\Providers\PlatformEventHandlerServiceProvider;
use App\Providers\ReportingMetricsProvider;
use App\Publisher;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Jobs\BuildReport;
use SebastianBergmann\CodeCoverage\Report\Xml\Report;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $yesterdayRevenue = DB::select(DB::raw("select revenue from daily_platform_reports where timestamp = '".Carbon::yesterday()->startOfDay()."'"));
        $yesterdayRevenue = count($yesterdayRevenue)!=0 ? $yesterdayRevenue[0]->revenue : 0;
        $potentialRevenue = DB::select(DB::raw("select sum(potential_value) as value from view_full_live_campaign_value where campaign_status = 'live'"));
        $potentialRevenue = count($potentialRevenue)!=0 ? $potentialRevenue[0]->value : 0;

        if((!is_null(cache('platform.dashboard.data'))) && env("ENABLE_CACHE")) {
            $dashboardData = json_decode(cache('platform.dashboard.data'),1); 
            // Return the home view.
            return view('dashboard.home',[
                'currentUser' => Auth::user(),
                'campaignPerformance' => $dashboardData["campaign_data"],
                'leads' => array_merge($dashboardData["leadgen_data"]["data"],$this->calculateLinkoutMetrics()),
                'revenue' => $dashboardData["financial_data"],
                'potentialRevenue' => $potentialRevenue,
                'yesterdayRevenue' => $yesterdayRevenue
            ]);
        } else {
            // Get the from and to date.
            $fromDate = Carbon::today()->startOfDay();
            $toDate = Carbon::now();

            dispatch((new BuildReport('platform',$fromDate))->onQueue('report-processing'));

            // Return the home view.
            return view('dashboard.home',[
                'currentUser' => Auth::user(),
                'campaignPerformance' => (new ReportingMetricsProvider())->getLeadgenPerformance($fromDate,$toDate),
                'leads' => $this->calculateLeadStatuses($fromDate,$toDate),
                'revenue' => $this->calculateRevenueMetrics($fromDate,$toDate),
                'potentialRevenue' => $potentialRevenue,
                'yesterdayRevenue' => $yesterdayRevenue
            ]);
        }
    }

    private function calculateLinkoutMetrics()
    {
        return [
            "metric_impressions" => MetricImpression::where("created_at",">=",Carbon::today())->where("created_at","<=",Carbon::now())->count(),
            "metric_clicks" => MetricClick::where("created_at",">=",Carbon::today())->where("created_at","<=",Carbon::now())->count(),
            "metric_conversions" => MetricConversion::where("created_at",">=",Carbon::today())->where("created_at","<=",Carbon::now())->count()
        ];        
    }

    /**
     * @param $fromDate
     * @param $toDate
     * @return array
     */
    private function calculateRevenueMetrics($fromDate, $toDate)
    {
        return (new ReportingMetricsProvider())->fetchRevenueNumbersForTimeframe($fromDate,$toDate);
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
            "metric_impressions" => MetricImpression::where("created_at",">=",Carbon::today())->where("created_at","<=",Carbon::now())->count(),
            "metric_clicks" => MetricClick::where("created_at",">=",Carbon::today())->where("created_at","<=",Carbon::now())->count(),
            "metric_conversions" => MetricConversion::where("created_at",">=",Carbon::today())->where("created_at","<=",Carbon::now())->count(),
            "generated" => $leadStatuses["generated"],
            "accepted" => $leadStatuses["accepted"],
            "acceptPercent" => $leadStatuses["accepted"] / ($leadStatuses["generated"]==0?1:$leadStatuses["generated"]),
            "rejected" => $leadStatuses["rejected"],
            "rejectPercent" => $leadStatuses["rejected"] / ($leadStatuses["generated"]==0?1:$leadStatuses["generated"])
        ];
    }
}
