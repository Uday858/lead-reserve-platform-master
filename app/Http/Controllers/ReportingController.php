<?php

namespace App\Http\Controllers;

use App\Lead;
use App\MetricClick;
use App\MetricConversion;
use App\MetricImpression;
use App\PlatformReport;
use App\Providers\ReportingMetricsProvider;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;

class ReportingController extends Controller
{
    public function index()
    {
        if(Input::get("from_date") == null && Input::get("selection") == null) {
            return view("reporting.index");
        } else {
            if(Input::get("from_date") != null) {
                // Format the fromdate and todate.
                $fromDateTimeObject = Carbon::parse(Input::get("from_date"));
                $toDateTimeObject = Carbon::parse(Input::get("to_date"));

                // Build out the date range array.
                $dateRange = [
                    "from" => $fromDateTimeObject->startOfDay(),
                    "to" => $toDateTimeObject->endOfDay()
                ];

                // Create the formatted date range string.
                $dateRangeString = $fromDateTimeObject->toFormattedDateString() . " - " . $toDateTimeObject->toFormattedDateString();
            } else {
                $dateRange = $this->getTwoDatesFromSelectionType(Input::get("selection"));
                $dateRangeInfo = $this->calculateDateRangeFromSelectionType(Input::get("selection"));
            }

            if($dateRange["from"] == null) {
                $dateRange = [
                    "from" => Carbon::today(),
                    "to" => Carbon::now()
                ];
            }

            $reports = PlatformReport::where("timestamp",">=",$dateRange["from"])
                          ->where("timestamp","<=",$dateRange["to"])
                          ->get();


                return view("reporting.index", [
                    "fromDate" => $dateRange["from"],
                    "toDate" => $dateRange["to"],
                    "info" => isset($dateRangeInfo) ? $dateRangeInfo : [
                        "dateRange" => $dateRangeString,
                        "title" => "Custom Date Selection"
                    ],
                "revenue" => [
                    "revenue" => $reports->sum('revenue'),
                    "payout" => $reports->sum('payout'),
                    "net" => $reports->sum('margin')
                ],
                "leads" => [
                    "generated" => $reports->sum('leads_generated'),
                    "accepted" => $reports->sum('leads_accepted'),
                    "rejected" => $reports->sum('leads_rejected'),
                    "metric_impressions" => $reports->sum('metric_impressions'),
                    "metric_clicks" => $reports->sum('metric_clicks'),
                    "metric_conversions" => $reports->sum('metric_conversions'),
                ],
                "campaignPerformance" => (new ReportingMetricsProvider())->getAgedLeadgenPerformance($dateRange["from"],$dateRange["to"])
                    // "campaignPerformance" => (new ReportingMetricsProvider())->getLeadgenPerformance($dateRange["from"],$dateRange["to"])
//                    'metrics' => (new ReportingMetricsProvider())->returnGlobalMetrics($dateRange["from"], $dateRange["to"])
                ]);
        }
    }

    private function calculateRevenueMetrics($fromDate, $toDate)
    {
        return (new ReportingMetricsProvider())->returnOverallFinanceMetricsForTimeframe($fromDate,$toDate);
    }

    private function calculateLeadStatuses($fromDate, $toDate)
    {
        $leadStatuses = $this->getLeadStatusNumbers($fromDate,$toDate);

        // Return information
        return [
            "metric_impressions" => MetricImpression::where("created_at",">=",$fromDate)->where("created_at","<=",$toDate)->count(),
            "metric_clicks" => MetricClick::where("created_at",">=",$fromDate)->where("created_at","<=",$toDate)->count(),
            "metric_conversions" => MetricConversion::where("created_at",">=",$fromDate)->where("created_at","<=",$toDate)->count(),
            "generated" => $leadStatuses["generated"],
            "accepted" => $leadStatuses["accepted"],
            "acceptPercent" => $leadStatuses["accepted"] / ($leadStatuses["generated"]==0?1:$leadStatuses["generated"]),
            "rejected" => $leadStatuses["rejected"],
            "rejectPercent" => $leadStatuses["rejected"] / ($leadStatuses["generated"]==0?1:$leadStatuses["generated"]),
            "unsent" => $leadStatuses["unsent"],
            "failingPercent" => $leadStatuses["unsent"] / ($leadStatuses["generated"]==0?1:$leadStatuses["generated"]),
        ];
    }

    private function getLeadStatusNumbers($fromDate,$toDate)
    {
        $leadNumbers = DB::table('platform_events')
            ->where(function ($query) {
                $query->where('name', 'lead.accepted')
                    ->orWhere('name', 'lead.rejected');
            })
            ->where('created_at', '>=', $fromDate)
            ->where('created_at', '<=', $toDate)
            ->select(DB::raw('count(*) as leads'))
            ->groupBy(DB::raw('name'))
            ->get(["leads"])->pluck("leads")->toArray();
        $leadStatuses = [
            "accepted" => isset($leadNumbers[0]) ? $leadNumbers[0] : 0,
            "rejected" => isset($leadNumbers[1]) ? $leadNumbers[1] : 0,
            "generated" => Lead::where('created_at', '>=', $fromDate)->where('created_at', '<=', $toDate)->count()
        ];
        $leadStatuses["unsent"] = $leadStatuses["generated"] - ($leadStatuses["accepted"] + $leadStatuses["rejected"]);
        return $leadStatuses;
    }

    private function getTwoDatesFromSelectionType($selection)
    {
        switch ($selection) {
            case "today":
                return [
                    "from" => Carbon::today()->startOfDay(),
                    "to" => Carbon::now()
                ];
                break;
            case "yesterday":
                return [
                    "from" => Carbon::yesterday()->startOfDay(),
                    "to" => Carbon::yesterday()->endOfDay()
                ];
                break;
            case "wtd":
                return [
                    "from" => Carbon::now()->startOfWeek(),
                    "to" => Carbon::now()
                ];
                break;
            case "mtd":
                return [
                    "from" => Carbon::now()->startOfMonth(),
                    "to" => Carbon::now()
                ];
                break;
            case "7day":
                return [
                    "from" => Carbon::today()->startOfDay()->subDay(7),
                    "to" => Carbon::now()
                ];
                break;
            case "30day":
                return [
                    "from" => Carbon::today()->startOfDay()->subDay(30),
                    "to" => Carbon::now()
                ];
                break;
            case "90day":
                return [
                    "from" => Carbon::today()->startOfDay()->subDay(90),
                    "to" => Carbon::now()
                ];
                break;
            case "month":
                return [
                    "from" => Carbon::today()->startOfDay()->subMonth(),
                    "to" => Carbon::now()
                ];
                break;
            case "year":
                return [
                    "from" => Carbon::today()->startOfDay()->subYear(),
                    "to" => Carbon::now()
                ];
                break;
        }
    }

    private function calculateDateRangeFromSelectionType($selection)
    {
        // A string for the date range.
        $dateRangeString = "";
        $titleString = "";

        switch ($selection) {
            case "today":
                $dateRangeString = Carbon::now()->toFormattedDateString();
                $titleString = "Today";
                break;
            case "yesterday":
                $dateRangeString = Carbon::yesterday()->toFormattedDateString();
                $titleString = "Yesterday";
                break;
            case "7day":
                $dateRangeString = Carbon::now()->subDay(7)->toFormattedDateString() . " - " . Carbon::now()->toFormattedDateString();
                $titleString = "Last 7 Days";
                break;
            case "30day":
                $dateRangeString = Carbon::now()->subDay(30)->toFormattedDateString() . " - " . Carbon::now()->toFormattedDateString();
                $titleString = "Last 30 Days";
                break;
            case "90day":
                $dateRangeString = Carbon::now()->subDay(90)->toFormattedDateString() . " - " . Carbon::now()->toFormattedDateString();
                $titleString = "Last 90 Days";
                break;
            case "month":
                $dateRangeString = Carbon::now()->subMonth()->toFormattedDateString() . " - " . Carbon::now()->toFormattedDateString();
                $titleString = "Month of " . Carbon::now()->month;
                break;
            case "year":
                $dateRangeString = Carbon::now()->subYear()->toFormattedDateString() . " - " . Carbon::now()->toFormattedDateString();
                $titleString = "Year of " . Carbon::now()->year;
                break;
        }

        return [
            "dateRange" => $dateRangeString,
            "title" => $titleString
        ];
    }

    public function campaigns()
    {
        return view("reporting.campaigns");
    }
}
