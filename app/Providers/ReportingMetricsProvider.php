<?php

namespace App\Providers;

use App\Advertiser;
use App\Campaign;
use App\CampaignAttribute;
use App\CampaignType;
use App\Lead;
use App\MetricClick;
use App\MetricConversion;
use App\MetricImpression;
use App\PlatformEvent;
use App\Publisher;
use App\PublisherCampaign;

use App\CampaignReport;
use App\CampaignPublisherReport;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ReportingMetricsProvider
{
    public function getAgedLeadgenPerformance($fromDate = null,$toDate = null,$campaignId = null)
    {
        if($fromDate == null && $toDate == null) {
            $fromDate = Carbon::create(2017, 1, 1, 0, 0, 0);
            $toDate = Carbon::now();
        }
        // Pull down campaign and publisher names
        $campaignNames = Campaign::all(['id','name'])->toArray();
        $publisherNames = Publisher::all(['id','name'])->toArray();
        // Data array
        $performanceData = [];
        // Campaigns..
        if($campaignId!=null) {
            $campaigns = CampaignReport::where("timestamp",">=",$fromDate)
                          ->where("timestamp","<=",$toDate)
                          ->where("campaign_id",$campaignId)
                          ->groupBy('campaign_id')
                          ->get([DB::raw("campaign_id, sum(leads_generated) as leads_generated, sum(leads_accepted) as leads_accepted, sum(leads_rejected) as leads_rejected, sum(revenue) as revenue, sum(payout) as payout")])
                          ->sortByDesc('revenue');
        } else {
            $campaigns = CampaignReport::where("timestamp",">=",$fromDate)
                          ->where("timestamp","<=",$toDate)
                          ->groupBy('campaign_id')
                          ->get([DB::raw("campaign_id, sum(leads_generated) as leads_generated, sum(leads_accepted) as leads_accepted, sum(leads_rejected) as leads_rejected, sum(revenue) as revenue, sum(payout) as payout")])
                          ->sortByDesc('revenue');
        }
        $publishers = CampaignPublisherReport::where("timestamp",">=",$fromDate)
                      ->where("timestamp","<=",$toDate)
                      ->groupBy(["campaign_id","publisher_id"])
                      ->get([DB::raw("campaign_id, publisher_id, sum(leads_generated) as leads_generated, sum(leads_accepted) as leads_accepted, sum(leads_rejected) as leads_rejected, sum(revenue) as revenue, sum(payout) as payout")])
                      ->sortByDesc('revenue');
        foreach($campaigns as $campaign) {
            $campaignPerformance = [
                "campaign_id" => $campaign["campaign_id"],
                "campaign_name" => $campaignNames[array_search($campaign["campaign_id"],array_column($campaignNames,"id"))]["name"],
                "leads_generated" => intval($campaign["leads_generated"]),
                "leads_accepted" => intval($campaign["leads_accepted"]),
                "leads_rejected" => intval($campaign["leads_rejected"]),
                "revenue" => floatval($campaign["revenue"]),
                "net" => floatval($campaign["revenue"])-floatval($campaign["payout"]),
                "payout" => floatval($campaign["payout"]),
                "publishers" => []
            ];
            foreach($publishers->where('campaign_id',$campaign["campaign_id"]) as $publisher) {
                $campaignPerformance["publishers"][] = [
                    "campaign_id" => $publisher["campaign_id"],
                    "publisher_id" => $publisher["publisher_id"],
                    "publisher_name" => $publisherNames[array_search($publisher["publisher_id"],array_column($publisherNames,"id"))]["name"],
                    "leads_generated" => intval($publisher["leads_generated"]),
                    "leads_accepted" => intval($publisher["leads_accepted"]),
                    "leads_rejected" => intval($publisher["leads_rejected"]),
                    "revenue" => $publisher["revenue"],
                    "payout" => $publisher["payout"],
                    "net" => floatval($publisher["revenue"])-floatval($publisher["payout"])
                ];
            }
            $performanceData[] = $campaignPerformance;
        }
        return $performanceData;
    }
    
    public function getLeadgenPerformanceForCampaignAndPublisher($campaignId,$publisherId,$fromDate,$toDate)
    {
      return CampaignPublisherReport::where("timestamp",">=",$fromDate)
                                  ->where("timestamp","<=",$toDate)
                                  ->where("campaign_id",$campaignId)
                                  ->where("publisher_id",$publisherId)
                                  ->get();
    }

    public function getAgedLeadgenPublisherPerformance($publisherId,$fromDate = null, $toDate = null)
    {
        if($fromDate == null && $toDate == null) {
            $fromDate = Carbon::create(2017, 1, 1, 0, 0, 0);
            $toDate = Carbon::now();
        }
        $publishers = CampaignPublisherReport::where("timestamp",">=",$fromDate)
                      ->where("timestamp","<=",$toDate)
                      ->where("publisher_id",$publisherId)
                      ->groupBy(["campaign_id","publisher_id"])
                      ->get([DB::raw("campaign_id, publisher_id, sum(leads_generated) as leads_generated, sum(leads_accepted) as leads_accepted, sum(leads_rejected) as leads_rejected, sum(revenue) as revenue, sum(payout) as payout")])
                      ->sortByDesc('revenue');
        return $publishers;
    }

    /**
     * @param $fromDate
     * @param $toDate
     * @return array
     */
    public function getLeadgenPerformance($fromDate,$toDate)
    {
        $campaigns = (new ReportingMetricsProvider())->leadgenCampaignPerformance($fromDate,$toDate);
        $publishers = (new ReportingMetricsProvider())->leadgenPublisherPerformance($fromDate,$toDate);
        foreach($campaigns as &$campaign) {
            $campaign["leads_generated"] = Lead::whereCampaignId($campaign["campaign_id"])->where("created_at",">=",$fromDate)->where("created_at","<=",$toDate)->count();
            $campaign["publishers"] = [];
            foreach($publishers as $publisher) {
                if($campaign["campaign_id"] == $publisher["campaign_id"]) {
                    $publisher["leads_generated"] = Lead::whereCampaignId($campaign["campaign_id"])->wherePublisherId($publisher["publisher_id"])->where("created_at",">=",$fromDate)->where("created_at","<=",$toDate)->count();
                    $publisher["current_status"] = ($publisher["leads_generated"] != 0) ? (($publisher["leads_generated"] == $publisher["daily_cap"]) ? "success" : "warning") : "danger";
                    $campaign["publishers"][] = $publisher;
                }
            }
        }
        return $campaigns;
    }

    /**
     * @param $fromDate
     * @param $toDate
     * @return array
     */
    public function leadgenCampaignPerformance($fromDate,$toDate)
    {
        $campaignData = DB::select("select c.id as campaign_id, c.name as campaign_name, c.advertiser_id as advertiser_id, a.name as advertiser_name, sum(pe.name = \"lead.accepted\") as leads_accepted, sum(pe.name = \"lead.rejected\") as leads_rejected, CAST(mdp.string_value as unsigned) as daily_cap, ifnull(sum(round(pe.`json_value`->\"$.cpl\",2)),0) AS revenue, ifnull(sum(round(pe.`json_value`->\"$.payout\",2)),0) AS payout, ifnull(sum(round(pe.`json_value`->\"$.net\",2)),0) AS net from campaigns c join platform_events pe on cast(pe.`json_value`->\"$.campaign_id\" as unsigned) = c.id join advertisers a on c.advertiser_id = a.id join campaign_attributes ca on ca.campaign_id = c.id join mutable_data_pairs mdp on ca.storage_id = mdp.id where(pe.name = \"lead.accepted\" or pe.name = \"lead.rejected\" or pe.name = \"revenue.track\") and (c.id IN ( select `campaign_attributes`.`campaign_id` from `campaign_attributes` inner join `mutable_data_pairs` on `campaign_attributes`.`storage_id` = `mutable_data_pairs`.`id` where `campaign_attributes`.`name` = \"campaign_status\" and `mutable_data_pairs`.`string_value` = \"live\")) and (ca.name = \"daily_cap\") and (pe.created_at <= \"".$toDate."\" and pe.created_at >= \"".$fromDate."\") group by c.id,mdp.id order by revenue desc");
        return collect($campaignData)->map(function($x){ return (array) $x; })->toArray();
    }

    /**
     * @param $fromDate
     * @param $toDate
     * @return array
     */
    public function leadgenPublisherPerformance($fromDate,$toDate)
    {
        $publisherData = DB::select("select pc.campaign_id, pc.publisher_id, p.name as publisher_name, pc.lead_cap as daily_cap, sum(pe.name = \"lead.accepted\") as leads_accepted, sum(pe.name = \"lead.rejected\") as leads_rejected, ifnull(sum(round(pe.`json_value`->\"$.cpl\",2)),0) AS revenue, ifnull(sum(round(pe.`json_value`->\"$.payout\",2)),0) AS payout, ifnull(sum(round(pe.`json_value`->\"$.net\",2)),0) AS net from publisher_campaigns pc join platform_events pe on cast(pe.json_value->\"$.campaign_id\" as unsigned) = pc.campaign_id and cast(pe.`json_value`->\"$.publisher_id\" as unsigned) = pc.publisher_id join publishers p on pc.publisher_id = p.id where pc.campaign_id IN( select `campaign_attributes`.`campaign_id` from `campaign_attributes` inner join `mutable_data_pairs` on `campaign_attributes`.`storage_id` = `mutable_data_pairs`.`id` where `campaign_attributes`.`name` = \"campaign_status\" and `mutable_data_pairs`.`string_value` = \"live\") and (pe.name = \"lead.accepted\" or pe.name = \"lead.rejected\" or pe.name = \"revenue.track\") and (pe.created_at <= \"".$toDate."\" and pe.created_at >= \"".$fromDate."\") group by pc.campaign_id,pc.publisher_id,pc.id;");
        return collect($publisherData)->map(function($x){ return (array) $x; })->toArray();
    }

    /**
     * @param $campaignId
     * @param null $fromDate
     * @param null $toDate
     * @return array
     */
    public function getCampaignAndPublisherMetrics($campaignId, $fromDate = null, $toDate = null)
    {
        return array_merge(
            $this->getOpenCampaignMetrics($campaignId,$fromDate,$toDate),
            [
                "publishers" => $this->getPublisherMetricsForCampaign($campaignId,$fromDate,$toDate)
            ]
        );
    }

    /**
     * @param $campaignIdArray
     * @param null $fromDate
     * @param null $toDate
     * @return array
     */
    public function getManyCampaignMetrics($campaignIdArray, $fromDate = null, $toDate = null)
    {
        $campaignResultArray = [];

        foreach ($campaignIdArray as $id) {
            $campaignResultArray[] = $this->getOpenCampaignMetrics($id, $fromDate, $toDate);
        }

        return $campaignResultArray;
    }

    /**
     * @param $campaignId
     * @param null $fromDate
     * @param null $toDate
     * @return array
     */
    public function getOpenCampaignMetrics($campaignId, $fromDate = null, $toDate = null)
    {
        $campaignObject = DB::table('campaigns')
            ->join('advertisers', 'campaigns.advertiser_id', '=', 'advertisers.id')
            ->join('campaign_attributes', 'campaign_attributes.campaign_id', '=', 'campaigns.id')
            ->join('mutable_data_pairs', 'campaign_attributes.storage_id', '=', 'mutable_data_pairs.id')
            ->where('campaign_attributes.name', 'daily_cap')
            ->where('campaigns.id', $campaignId)
            ->select(DB::raw("campaigns.id as campaign_id,
campaigns.name as campaign_name,
advertisers.id as advertiser_id,
advertisers.name as advertiser_name,
CAST(mutable_data_pairs.string_value as unsigned) as daily_cap"))
            ->first();

        return [
            "campaign" => collect($campaignObject)->toArray(),
            "leads" => $this->returnAcceptedRejectedLeads($campaignId, $fromDate, $toDate),
            "financials" => $this->getCampaignFinancials($campaignId, $fromDate, $toDate)
        ];
    }

    public function getPublisherMetricsForCampaign($campaignId, $fromDate = null, $toDate = null)
    {
        $publisherIds = PublisherCampaign::whereCampaignId($campaignId)->get(["publisher_id"])->pluck("publisher_id")->toArray();
        $publisherResultArray = [];
        foreach($publisherIds as $id) {
           $publisherResultArray[] = $this->getOpenPublisherMetrics($campaignId,$id,$fromDate,$toDate);
        }
        return $publisherResultArray;
    }

    public function getOpenPublisherMetrics($campaignId, $publisherId, $fromDate = null, $toDate = null)
    {
        $publisherObject = DB::table('publishers')
            ->join('publisher_campaigns', 'publisher_campaigns.publisher_id', '=', 'publishers.id')
            ->where('publishers.id', $publisherId)
            ->where('publisher_campaigns.campaign_id', $campaignId)
            ->select(DB::raw("publishers.id as publisher_id,publishers.name as publisher_name,publisher_campaigns.lead_cap as daily_cap"))
            ->first();
        return [
            "publisher" => collect($publisherObject)->toArray(),
            "leads" => $this->returnAcceptedRejectedLeads($campaignId, $fromDate, $toDate, $publisherId),
            "financials" => $this->getCampaignFinancials($campaignId, $fromDate, $toDate, $publisherId)
        ];
    }

    /**
     * @param $campaignId
     * @param null $fromDate
     * @param null $toDate
     * @param null $publisherId
     * @return array
     */
    private function getCampaignFinancials($campaignId, $fromDate = null, $toDate = null, $publisherId = null)
    {
        // Execute the query.
        $revenueEvents = DB::table('platform_events')
            ->where('name', 'revenue.track')
            ->where(function ($query) use ($campaignId) {
                $query->where('json_value->campaign_id', intval($campaignId))
                    ->orWhere('json_value->campaign_id', "" . $campaignId);
            });

        // Make sure the dates are correct.
        if ($fromDate != null) {
            $revenueEvents->where('created_at', '>=', $fromDate);
        }
        if ($toDate != null) {
            $revenueEvents->where('created_at', '<=', $toDate);
        }

        if ($publisherId != null) {
            $revenueEvents->where(function ($query) use ($publisherId) {
                $query->where('json_value->publisher_id', intval($publisherId))
                    ->orWhere('json_value->publisher_id', "" . $publisherId);
            });
        }

        // Execute the query
        $revenueEvents = $revenueEvents->select(DB::raw('ifnull(sum(round(json_value->"$.cpl",2)),0) as revenue, ifnull(sum(round(json_value->"$.net",2)),0) as net, ifnull(sum(round(json_value->"$.payout",2)),0) as payout'))
            ->first();

        // Return a basic array.
        return collect($revenueEvents)->toArray();
    }


    /**
     * Return the dashboard.
     * @param $fromDate
     * @param $toDate
     * @return array
     */
    public function returnGlobalMetrics($fromDate = null, $toDate = null)
    {
        if ($fromDate != null && $toDate != null) {
            $leadGenCampaignData = $this->getCampaignReportingDataWithinTimeframe($fromDate, $toDate, "Leadgen");
            $linkoutCampaignData = $this->getCampaignReportingDataWithinTimeframe($fromDate, $toDate, "CPA");
        } else {
            $leadGenCampaignData = $this->getCampaignReportingData();
            $linkoutCampaignData = $this->getCampaignReportingDataWithinTimeframe($fromDate, $toDate, "CPA");
        }
        $leadgenAmounts = [
            "generated" => 0,
            "net" => 0,
            "payout" => 0,
            "lead_caps" => 0,
            "leads_captured" => 0,
            "leads_accepted" => 0,
            "leads_rejected" => 0
        ];
        foreach ($leadGenCampaignData as $campaign) {
            $leadgenAmounts["generated"] += $campaign["campaign"]["revenue_generated"];
            $leadgenAmounts["net"] += $campaign["campaign"]["net_generated"];
            $leadgenAmounts["payout"] += $campaign["campaign"]["payout_amounts"];
            $leadgenAmounts["leads_captured"] += $campaign["campaign"]["leads_captured"];
            $leadgenAmounts["leads_accepted"] += $campaign["campaign"]["leads_accepted"];
            $leadgenAmounts["leads_rejected"] += $campaign["campaign"]["leads_rejected"];
        }
        $linkoutAmounts = [
            "generated" => 0,
            "net" => 0,
            "payout" => 0,
            "impressions" => 0,
            "clicks" => 0,
            "conversions" => 0
        ];
        foreach ($linkoutCampaignData as $campaign) {
            $linkoutAmounts["generated"] += $campaign["campaign"]["revenue_generated"];
            $linkoutAmounts["net"] += $campaign["campaign"]["net_generated"];
            $linkoutAmounts["payout"] += $campaign["campaign"]["payout_amounts"];
            $linkoutAmounts["impressions"] += $campaign["campaign"]["metric_impressions"];
            $linkoutAmounts["clicks"] += $campaign["campaign"]["metric_clicks"];
            $linkoutAmounts["conversions"] += $campaign["campaign"]["metric_conversions"];
        }

        return [
            "leadgenAmounts" => $leadgenAmounts,
            "linkoutAmounts" => $linkoutAmounts,
            "leadgenCampaignData" => $leadGenCampaignData,
            "linkoutCampaignData" => $linkoutCampaignData,
        ];
    }

    public function fetchRevenueNumbersForTimeframe($fromDate = null, $toDate = null)
    {
        // Make sure that from and to date are set correctly.
        if ($fromDate == null && $toDate == null) {
            $fromDate = Carbon::today();
            $toDate = Carbon::now();
        }

        // Return a formatted revenue array.
        return $this->buildRevenueArray($fromDate, $toDate);
    }

    /**
     * Return overall (added) finance metrics for timeframe.
     * @param null $fromDate
     * @param null $toDate
     * @return array
     */
    public function returnOverallFinanceMetricsForTimeframe($fromDate = null, $toDate = null)
    {
        // Make sure that from and to date are set correctly.
        if ($fromDate == null && $toDate == null) {
            $fromDate = Carbon::today();
            $toDate = Carbon::now();
        }

        // Get the link out amounts and lead gen amounts.
        $linkoutAmounts = $this->buildReportingRevenueArrayWithOptionalQuery($fromDate, $toDate, null, "linkout.conversion");
        $leadgenAmounts = $this->buildReportingRevenueArrayWithOptionalQuery($fromDate, $toDate);

        // Return the combined array of finances within timeframe.
        return [
            "revenue" => $linkoutAmounts["revenue"] + $leadgenAmounts["revenue"],
            "payout" => $linkoutAmounts["payout"] + $leadgenAmounts["payout"],
            "net" => $linkoutAmounts["net"] + $leadgenAmounts["net"],
        ];
    }

    /**
     * Retrieve and return the campaign reporting metrics.
     *
     * @param $campaignId
     * @return array
     */
    public function returnCampaignReportingMetrics($campaignId)
    {
        return $this->returnReportingMetrics($campaignId);
    }

    /**
     * @param $campaignId
     * @param $fromDate
     * @param $toDate
     * @return array
     */
    public function returnCampaignPublishersReportingMetrics($campaignId, $fromDate = null, $toDate = null)
    {
        // Fetch the campaign.
        $campaign = Campaign::whereId($campaignId)->first();

        // Get campaign type.
        $campaignType = $campaign->type->name;

        // Retrieve list of publisher ids.
        $publisherIds = PublisherCampaign::whereCampaignId($campaignId)->get(["publisher_id"])->toArray();

        // Create list of publisher objects.
        $publishers = [];
        foreach ($publisherIds as $pid) {
            $publishers[] = Publisher::whereId($pid)->first();
        }

        // Create list of "publisher metrics."
        $publisherMetricArray = [];
        foreach ($publishers as $publisher) {

            if ($fromDate != null && $toDate != null) {

                $leadNumbers = DB::table('platform_events')
                    ->where(function ($query) use ($campaignId) {
                        $query->where('json_value->campaign_id', intval($campaignId))
                            ->orWhere('json_value->campaign_id', "" . $campaignId);
                    })
                    ->where(function ($query) use ($publisher) {
                        $query->where('json_value->publisher_id', intval($publisher->id))
                            ->orWhere('json_value->publisher_id', "" . $publisher->id);
                    })
                    ->where(function ($query) {
                        $query->where('name', 'lead.accepted')
                            ->orWhere('name', 'lead.rejected');
                    })
                    ->where('created_at', '>=', $fromDate)
                    ->where('created_at', '<=', $toDate)
                    ->select(DB::raw('count(*) as leads'))
                    ->groupBy(DB::raw('name'))
                    ->get(["leads"])->pluck("leads")->toArray();

                // Get statuses of leads from campaign/publisher identification.
                $statusArray = [
                    "accepted" => isset($leadNumbers[0]) ? $leadNumbers[0] : 0,
                    "rejected" => isset($leadNumbers[1]) ? $leadNumbers[1] : 0,
                    "captured" => Lead::where('created_at', '>=', $fromDate)
                        ->where('created_at', '<=', $toDate)
                        ->where('campaign_id', $campaignId)
                        ->where('publisher_id', $publisher->id)->count()
                ];

                $revenueEvents = DB::table('platform_events')
                    ->where('name', 'revenue.track')
                    ->where(function ($query) use ($campaignId) {
                        $query->where('json_value->campaign_id', intval($campaignId))
                            ->orWhere('json_value->campaign_id', "" . $campaignId);
                    })
                    ->where(function ($query) use ($publisher) {
                        $query->where('json_value->campaign_id', intval($publisher->id))
                            ->orWhere('json_value->campaign_id', "" . $publisher->id);
                    })
                    ->where('created_at', '>=', $fromDate)
                    ->where('created_at', '<=', $fromDate)
                    ->select(DB::raw('sum(round(json_value->"$.cpl",2)) as revenue, sum(round(json_value->"$.net",2)) as net, sum(round(json_value->"$.payout",2)) as payout'))
                    ->first();
                $revenueArray = collect($revenueEvents)->toArray();

                // Retrieve the additional linkout metrics.
                $additionalLinkoutMetrics = [
                    "impressions" => MetricImpression::wherePublisherId($publisher->id)
                        ->whereCampaignId($campaignId)
                        ->where("created_at", ">=", $fromDate)
                        ->where("created_at", "<=", $toDate)
                        ->count(),
                    "clicks" => MetricClick::wherePublisherId($publisher->id)
                        ->whereCampaignId($campaignId)
                        ->where("created_at", ">=", $fromDate)
                        ->where("created_at", "<=", $toDate)
                        ->count()
                ];
            } else {
                // Get statuses of leads from campaign/publisher identification.
                $leadNumbers = DB::table('platform_events')
                    ->where(function ($query) use ($campaignId) {
                        $query->where('json_value->campaign_id', intval($campaignId))
                            ->orWhere('json_value->campaign_id', "" . $campaignId);
                    })
                    ->where(function ($query) use ($publisher) {
                        $query->where('json_value->publisher_id', intval($publisher->id))
                            ->orWhere('json_value->publisher_id', "" . $publisher->id);
                    })
                    ->where(function ($query) {
                        $query->where('name', 'lead.accepted')
                            ->orWhere('name', 'lead.rejected');
                    })
                    ->select(DB::raw('count(*) as leads'))
                    ->groupBy(DB::raw('name'))
                    ->get(["leads"])->pluck("leads")->toArray();

                // Get statuses of leads from campaign/publisher identification.
                $statusArray = [
                    "accepted" => isset($leadNumbers[0]) ? $leadNumbers[0] : 0,
                    "rejected" => isset($leadNumbers[1]) ? $leadNumbers[1] : 0,
                    "captured" => Lead::where('campaign_id', $campaignId)
                        ->where('publisher_id', $publisher->id)->count()
                ];

                $revenueEvents = DB::table('platform_events')
                    ->where('name', 'revenue.track')
                    ->where(function ($query) use ($campaignId) {
                        $query->where('json_value->campaign_id', intval($campaignId))
                            ->orWhere('json_value->campaign_id', "" . $campaignId);
                    })
                    ->where(function ($query) use ($publisher) {
                        $query->where('json_value->campaign_id', intval($publisher->id))
                            ->orWhere('json_value->campaign_id', "" . $publisher->id);
                    })
                    ->select(DB::raw('sum(round(json_value->"$.cpl",2)) as revenue, sum(round(json_value->"$.net",2)) as net, sum(round(json_value->"$.payout",2)) as payout'))
                    ->first();
                $revenueArray = collect($revenueEvents)->toArray();

                $additionalLinkoutMetrics = [
                    "impressions" => MetricImpression::wherePublisherId($publisher->id)
                        ->whereCampaignId($campaignId)
                        ->count(),
                    "clicks" => MetricClick::wherePublisherId($publisher->id)
                        ->whereCampaignId($campaignId)
                        ->count()
                ];
            }

            // Get the lead cap
            $leadCap = PublisherCampaign::wherePublisherId($publisher->id)->whereCampaignId($campaignId)->first()->lead_cap;

            if ($campaignType == "CPA" || $campaignType == "Linkout") {
                $currentStatus = [
                    "current_status" => ($statusArray["captured"] != 0) ? (($statusArray["captured"] == $leadCap) ? "success" : "warning") : "danger"
                ];
            } else {
                $currentStatus = [
                    "current_status" => ($statusArray["captured"] != 0) ? (($statusArray["captured"] == $leadCap) ? "success" : "warning") : "danger"
                ];
            }

            $publisherMetricArray[] = array_merge([
                "publisher_id" => $publisher->id,
                "publisher_name" => $publisher->name,
                "leads_captured" => $statusArray["captured"],
                "leads_accepted" => $statusArray["accepted"],
                "leads_rejected" => $statusArray["rejected"],
                "revenue" => $revenueArray["revenue"],
                "payout" => $revenueArray["payout"],
                "lead_cap" => $leadCap
            ], $additionalLinkoutMetrics, $currentStatus);
        }
        return $publisherMetricArray;
    }

    /**
     * Grab the lead status for campaign/publisher.
     *
     * @param $campaignId
     * @param $publisherId
     * @param fromDate
     * @param toDate
     * @return array
     */
    private function retrieveLeadStatusCountForCampaignPublisher($campaignId, $publisherId, $fromDate = null, $toDate = null)
    {
        if ($fromDate != null && $toDate != null) {
            $leadIdArray = Lead::whereCampaignId($campaignId)
                ->wherePublisherId($publisherId)
                ->where('created_at', ">=", $fromDate)
                ->where('created_at', "<=", $toDate)
                ->get(["id"])
                ->pluck('id')
                ->toArray();
        } else {
            $leadIdArray = Lead::whereCampaignId($campaignId)
                ->wherePublisherId($publisherId)
                ->get(["id"])
                ->pluck('id')
                ->toArray();
        }

        return $this->returnAcceptedRejectedLeadsFromArray($leadIdArray);
    }

    /**
     * Return the campaign reporting data.
     *
     * @return array
     */
    private function getCampaignReportingData()
    {
        $campaigns = Campaign::all();
        $campaignData = [];
        foreach ($campaigns as $campaign) {
            if ($campaign->hasAttributeOrEmpty("campaign_status") != "") {
                if ($campaign->hasAttributeOrEmpty("campaign_status") == "live") {
                    $campaignData[] = [
                        "campaign" => $this->returnReportingMetrics($campaign->id),
                        "publishers" => $this->returnCampaignPublishersReportingMetrics($campaign->id)
                    ];
                }
            }
        }
        return $campaignData;
    }

    /**
     * @param $fromDate
     * @param $toDate
     * @return array
     */
    public function retrieveOnlyCampaignLeadgenDataWithinTimeframe($fromDate, $toDate)
    {
        // Get all live campaign ids.
        $liveCampaignIds = DB::table("campaign_attributes")
            ->join('mutable_data_pairs', 'campaign_attributes.storage_id', '=', 'mutable_data_pairs.id')
            ->select("campaign_attributes.campaign_id")
            ->where("campaign_attributes.name", "campaign_status")
            ->where("mutable_data_pairs.string_value", "live")
            ->get(["campaign_id"])
            ->pluck("campaign_id")
            ->toArray();

        // Get all campaigns.
        $campaigns = DB::table("campaigns")
            ->join('campaign_types', 'campaigns.campaign_type_id', '=', 'campaign_types.id')
            ->select("campaigns.id", "campaign_types.name")
            ->where("campaign_types.name", "leadgen")
            ->whereIn("campaigns.id", $liveCampaignIds)
            ->get();

        // Campaign data attribute.
        $campaignData = [];

        foreach ($campaigns as $campaign) {
            $campaignData[] = $this->returnReportingMetricsWithinTimeframe($campaign->id, $fromDate, $toDate);
        }
        return $campaignData;
    }

    /**
     * @param $fromDate
     * @param $toDate
     * @param $type
     * @return array
     */
    private function getCampaignReportingDataWithinTimeframe($fromDate, $toDate, $type)
    {
        // Get all live campaign ids.
        $liveCampaignIds = DB::table("campaign_attributes")
            ->join('mutable_data_pairs', 'campaign_attributes.storage_id', '=', 'mutable_data_pairs.id')
            ->select("campaign_attributes.campaign_id")
            ->where("campaign_attributes.name", "campaign_status")
            ->where("mutable_data_pairs.string_value", "live")
            ->get(["campaign_id"])
            ->pluck("campaign_id")
            ->toArray();
        $campaignIds = DB::table("campaigns")
            ->join('campaign_types', 'campaigns.campaign_type_id', '=', 'campaign_types.id')
            ->select("campaigns.id", "campaign_types.name")
            ->where("campaign_types.name", $type)
            ->whereIn("campaigns.id", $liveCampaignIds)
            ->get(["id"])->pluck("id")->toArray();
        return $this->getManyCampaignMetrics($campaignIds,$fromDate,$toDate);
        /*
        // Get all campaigns.
        $campaigns = DB::table("campaigns")
            ->join('campaign_types', 'campaigns.campaign_type_id', '=', 'campaign_types.id')
            ->select("campaigns.id", "campaign_types.name")
            ->where("campaign_types.name", $type)
            ->whereIn("campaigns.id", $liveCampaignIds)
            ->get();

        // Campaign data attribute.
        $campaignData = [];

        // Get accept/rejected leads per campaign
        $campaignAcceptReject = collect(DB::table('platform_events')
            ->where(function ($query) {
                $query->where('platform_events.name', 'lead.accepted')
                    ->orWhere('platform_events.name', 'lead.rejected');
            })
            ->where('platform_events.created_at', '>=', $fromDate)
            ->where('platform_events.created_at', '<=', $toDate)
            ->select(DB::raw('CAST(`json_value`->"$.campaign_id" AS unsigned) as campaign_id,platform_events.name as event_name,count(*) as leads'))
            ->groupBy("campaign_id", "platform_events.name")
            ->get())->groupBy("campaign_id");

        foreach ($campaigns as $campaign) {
            if ($type == "Linkout" || $type == "CPA") {
                $campaignData[] = [
                    "campaign" => $this->returnLinkoutReportingWithinTimeframe($campaign->id, $fromDate, $toDate),
                    "publishers" => $this->returnCampaignPublishersReportingMetrics($campaign->id, $fromDate, $toDate)
                ];
            } else {
                $campaignData[] = [
                    "campaign" => $this->returnReportingMetricsWithinTimeframe($campaign->id, $fromDate, $toDate, isset($campaignAcceptReject[$campaign->id]) ? $campaignAcceptReject[$campaign->id] : null),
                    "publishers" => $this->returnCampaignPublishersReportingMetrics($campaign->id, $fromDate, $toDate)
                ];
            }
        }
        return $campaignData;
        */
    }

    private function returnReportedMetrics($campaignId)
    {

    }

    /**
     * @param $campaignId
     * @return array
     */
    private function returnReportingMetrics($campaignId)
    {
        // TODO: Come up with a job to generate reports every day.
        $activePublishers = PublisherCampaign::whereCampaignId($campaignId)->count();
        $leadsGenerated = Lead::whereCampaignId($campaignId)->count();

        // Get the acceptReject stats.
        $leadStatuses = $this->returnAcceptedRejectedLeads($campaignId);

        // Execute the query.
        $revenueEvents = DB::table('platform_events')
            ->where('name', 'revenue.track')
            ->where(function ($query) use ($campaignId) {
                $query->where('json_value->campaign_id', intval($campaignId))
                    ->orWhere('json_value->campaign_id', "" . $campaignId);
            })
            ->select(DB::raw('sum(round(json_value->"$.cpl",2)) as revenue, sum(round(json_value->"$.net",2)) as net, sum(round(json_value->"$.payout",2)) as payout'))
            ->first();

        // Return a basic array.
        $campaignFinancials = collect($revenueEvents)->toArray();

        $revenueAmount = $campaignFinancials["revenue"];
        $netAmount = $campaignFinancials["net"];
        $payoutAmount = $campaignFinancials["payout"];

        // Campaign object.
        $campaignObject = Campaign::whereId($campaignId)->first();

        // Additional metrics.
        $additionalMetrics = [];

        // If linkout or CPA..
        if ($campaignObject->type->name == "Linkout" || $campaignObject->type->name == "CPA") {
            $additionalMetrics = [
                "metric_impressions" => MetricImpression::whereCampaignId($campaignId)->count(),
                "metric_clicks" => MetricClick::whereCampaignId($campaignId)->count(),
                "metric_conversions" => MetricConversion::whereCampaignId($campaignId)->count()
            ];
        }

        // Return the reporting metrics.
        return array_merge([
            "campaign_id" => $campaignId,
            "campaign_name" => $campaignObject->name,
            "advertiser_id" => $campaignObject->advertiser_id,
            "advertiser_name" => Advertiser::whereId(Campaign::whereId($campaignId)->first()->advertiser_id)->first()->name,
            "active_publishers" => $activePublishers,
            "leads_captured" => $leadsGenerated,
            "leads_accepted" => $leadStatuses["accepted"],
            "leads_rejected" => $leadStatuses["rejected"],
            "revenue_generated" => $revenueAmount,
            "net_generated" => $netAmount,
            "payout_amounts" => $payoutAmount
        ], $additionalMetrics);
    }

    /**
     * Get reporting data within time frame.
     * @param $campaignId
     * @param $fromDate
     * @param $toDate
     * @param $leadStatuses
     * @return array
     */
    public function returnReportingMetricsWithinTimeframe($campaignId, $fromDate, $toDate, $leadStatusArray)
    {
        // Transform the lead status
        $leadStatuses = [
            "accepted" => 0,
            "rejected" => 0,
            "generated" => Lead::whereCampaignId($campaignId)
                ->where('created_at', '>=', $fromDate)
                ->where('created_at', '<=', $toDate)->count()
        ];

        // Go through the lead status array and work through it.
        if ($leadStatusArray != null) {
            $leadStatusArray->map(function ($item) use (&$leadStatuses) {
                if ($item->event_name == "lead.accepted") {
                    $leadStatuses["accepted"] += $item->leads;
                } else if ($item->event_name == "lead.rejected") {
                    $leadStatuses["rejected"] += $item->leads;
                }
            });
        }

        // Revenue generated...
        $campaignFinancials = $this->buildRevenueArray($fromDate, $toDate, [
            "campaign_id" => $campaignId
        ]);

        $revenueAmount = $campaignFinancials["revenue"];
        $netAmount = $campaignFinancials["net"];
        $payoutAmount = $campaignFinancials["payout"];

        // Get campaign
        $campaign = Campaign::whereId($campaignId)->first();
        $activePublishers = PublisherCampaign::whereCampaignId($campaignId)->count();

        // Return the reporting metrics.
        return [
            "campaign_id" => $campaignId,
            "campaign_name" => $campaign->name,
            "advertiser_id" => $campaign->advertiser_id,
            "advertiser_name" => Advertiser::whereId(Campaign::whereId($campaignId)->first()->advertiser_id)->first()->name,
            "active_publishers" => $activePublishers,
            "lead_cap" => $campaign->hasAttributeOrEmpty("daily_cap"),
            "leads_captured" => $leadStatuses["generated"],
            "leads_accepted" => $leadStatuses["accepted"],
            "leads_rejected" => $leadStatuses["rejected"],
            "revenue_generated" => $revenueAmount,
            "net_generated" => $netAmount,
            "payout_amounts" => $payoutAmount
        ];
    }

    private function getLeadsAcceptedRejectedByCampaign($campaignId, $arrayOfLeadStatuses, $fromDate = null, $toDate = null)
    {
        return [
            "generated" => Lead::whereCampaignId($campaignId)
                ->where('created_at', '>=', $fromDate)
                ->where('created_at', '<=', $toDate)->count(),
            "accepted" => isset($arrayOfLeadStatuses->where("campaign_id", $campaignId)->where("name", "lead.accepted")->leads) ? $arrayOfLeadStatuses->where("campaign_id", $campaignId)->where("name", "lead.accepted")->leads : 0,
            "rejected" => isset($arrayOfLeadStatuses->where("campaign_id", $campaignId)->where("name", "lead.rejected")->leads) ? $arrayOfLeadStatuses->where("campaign_id", $campaignId)->where("name", "lead.rejected")->leads : 0
        ];
    }


    /**
     * @param $campaignId
     * @param $fromDate
     * @param $toDate
     * @return array
     */
    public function returnLinkoutReportingWithinTimeframe($campaignId, $fromDate, $toDate)
    {
        // Get the active number of publisher on a campaign.
        $activePublishers = PublisherCampaign::whereCampaignId($campaignId)->count();
        $linkoutMetrics = [
            "impressions" => MetricImpression::whereCampaignId($campaignId)->where('created_at', '>=', $fromDate)->where('created_at', "<=", $toDate)->count(),
            "clicks" => MetricClick::whereCampaignId($campaignId)->where('created_at', '>=', $fromDate)->where('created_at', "<=", $toDate)->count(),
            "conversions" => MetricConversion::whereCampaignId($campaignId)->where('created_at', '>=', $fromDate)->where('created_at', "<=", $toDate)->count(),
        ];
        $campaignFinancials = $this->retrieveCampaignRevenueWithinTimeframe($campaignId, $fromDate, $toDate, "linkout.conversion");
        $campaign = Campaign::whereId($campaignId)->first();
        return [
            "campaign_id" => $campaignId,
            "campaign_name" => $campaign->name,
            "advertiser_id" => $campaign->advertiser_id,
            "advertiser_name" => Advertiser::whereId(Campaign::whereId($campaignId)->first()->advertiser_id)->first()->name,
            "active_publishers" => $activePublishers,
            "lead_cap" => $campaign->hasAttributeOrEmpty("daily_cap"),
            "metric_impressions" => $linkoutMetrics["impressions"],
            "metric_clicks" => $linkoutMetrics["clicks"],
            "metric_conversions" => $linkoutMetrics["conversions"],
            "revenue_generated" => $campaignFinancials["revenue"],
            "net_generated" => $campaignFinancials["net"],
            "payout_amounts" => $campaignFinancials["payout"]
        ];
    }

    /**
     * Get leads generated today, by CampaignID
     * @param $campaignId
     * @return int
     */
    public function getLeadsGeneratedTodayByCampaign($campaignId)
    {
        return Lead::whereCampaignId($campaignId)->whereDay("created_at", Carbon::now()->day)->get()->count();
    }

    /**
     * Get leads generated today, by PublisherID
     * @param $publisherId
     * @return int
     */
    public function getLeadsGeneratedTodayByPublisher($publisherId)
    {
        return Lead::wherePublisherId($publisherId)->whereDay("created_at", Carbon::now()->day)->get()->count();
    }

    /**
     * @param $campaignId
     * @param $publisherId
     * @return int
     */
    public function getLeadAcceptedEventsByCampaignPublisher($campaignId, $publisherId)
    {
        return (new PlatformEventHandlerServiceProvider())->buildPlatformQuery("lead.accepted", "campaign_id", "" . $campaignId, Carbon::today(), Carbon::now())->where("json_value->publisher_id", "" . $publisherId)->count();
    }

    /**
     * Find LeadSent events by campaign. (These are actual lead sends.)
     * @param $campaignId
     * @return int
     */
    public function getLeadSentEventsByCampaign($campaignId)
    {
        return count((new PlatformEventHandlerServiceProvider())->findEventsToday("lead.accepted")->withDecodedValue("campaign_id", $campaignId)->get());
    }

    /**
     * Find LeadSent events by publisher. (These are actual lead sends.)
     * @param $publisherId
     * @return int
     */
    public function getLeadSentEventsByPublisher($publisherId)
    {
        return count((new PlatformEventHandlerServiceProvider())->findEventsToday("lead.accepted")->withDecodedValue("publisher_id", $publisherId)->get());
    }

    /**
     * Retrieve the campaign publisher revenue.
     *
     * @param $campaignId
     * @param $publisherId
     * @param $fromDate
     * @param $toDate
     * @return array
     */
    private function retrieveCampaignPublisherRevenue($campaignId, $publisherId, $fromDate = null, $toDate = null)
    {
        // Return built amount array.
        if ($fromDate != null && $toDate != null) {
            return $this->buildReportingRevenueArrayWithOptionalQuery($fromDate, $toDate, [
                "key" => ["campaign_id", "publisher_id"],
                "value" => [$campaignId, $publisherId]
            ]);
        } else {
            return $this->buildRevenueArrayFromQuery([
                "key" => ["campaign_id", "publisher_id"],
                "value" => [$campaignId, $publisherId]
            ]);
        }
    }

    /**
     * Get the campaign financial metrics.
     *
     * @param $campaignId
     * @return array
     */
    public function getCampaignMetrics($campaignId)
    {
        return $this->retrieveCampaignRevenue($campaignId);
    }

    /**
     * Get the publisher financial metrics.
     *
     * @param $publisherId
     * @return array
     */
    public function getPublisherMetrics($publisherId)
    {
        return $this->buildRevenueArrayFromQuery([
            "key" => "publisher_id",
            "value" => $publisherId
        ]);
    }

    /**
     * Get advertiser revenue metrics.
     *
     * @param $advertiserId
     * @return array
     */
    public function getAdvertiserRevenue($advertiserId)
    {
        // Advertiser revenue.
        $metrics = [
            "revenue" => 0,
            "payout" => 0,
            "net" => 0
        ];

        // Get the advertiser campaign ids.
        $campaignIds = Campaign::whereAdvertiserId($advertiserId)->get(["id"])->map(function ($item, $key) {
            return $item["id"];
        })->all();

        // Campaign financials
        $campaignMetrics = [];
        foreach ($campaignIds as $id) {
            $metrics["revenue"] += $this->retrieveCampaignRevenue($id)["revenue"];
            $metrics["payout"] += $this->retrieveCampaignRevenue($id)["payout"];
            $metrics["net"] += $this->retrieveCampaignRevenue($id)["net"];
        }

        // Return the advertiser metrics.
        return $metrics;
    }

    /**
     * Retrieve the revenue for a campaign.
     *
     * @param $campaignId
     * @return array
     */
    private function retrieveCampaignRevenue($campaignId)
    {
        // Return built amount array.
        return $this->buildRevenueArrayFromQuery([
            "key" => "campaign_id", "value" => $campaignId
        ]);
    }

    /**
     * Retrieve the campaign revenue within the timeframe provided.
     * @param $campaignId
     * @param $fromDate
     * @param $toDate
     * @param null $specificEventName
     * @return array
     */
    public function retrieveCampaignRevenueWithinTimeframe($campaignId, $fromDate, $toDate, $specificEventName = null)
    {
        if ($specificEventName != null) {
            return $this->buildReportingRevenueArrayWithOptionalQuery($fromDate, $toDate, [
                "key" => "campaign_id", "value" => $campaignId
            ], $specificEventName);
        } else {
            return $this->buildReportingRevenueArrayWithOptionalQuery($fromDate, $toDate, [
                "key" => "campaign_id", "value" => $campaignId
            ]);
        }
    }

    /**
     * Build revenue array from here.
     *
     * @param $query
     * @return array
     */
    private function buildRevenueArrayFromQuery($query)
    {
        // Retrieve the revenue track platform_event.
        $revenueEvents = (new PlatformEventHandlerServiceProvider())
            ->findEvents("revenue.track")
            ->withDecodedValue($query["key"], $query["value"])
            ->get();

        $amounts = [
            "revenue" => 0,
            "payout" => 0,
            "net" => 0
        ];

        if (count($revenueEvents) != 0) {
            foreach ($revenueEvents as $event) {
                // Decode the platform_event json value.
                // Add up all things.
                $amounts["revenue"] += $event["value"]["cpl"];
                $amounts["payout"] += $event["value"]["payout"];
                $amounts["net"] += $event["value"]["net"];
            }
        }

        return $amounts;
    }

    /**
     * @param $fromDate
     * @param $toDate
     */
    public function buildRevenueArray($fromDate, $toDate, $query = null)
    {
        // Start building the query.
        $revQuery = DB::table('platform_events')
            ->where('name', 'revenue.track');

        // From and to date.
        if ($fromDate != null) {
            $revQuery->where('created_at', '>=', $fromDate);
        }
        if ($toDate != null) {
            $revQuery->where('created_at', '<=', $toDate);
        }

        // If we pass in query, make sure to add a where clause.
        if ($query != null) {
            if (is_array($query)) {
                foreach ($query as $key => $value) {
                    $revQuery->where('json_value->' . $key, $value);
                }
            }
        }

        $revQuery->select(DB::raw('sum(round(json_value->"$.cpl",2)) as revenue, sum(round(json_value->"$.net",2)) as net, sum(round(json_value->"$.payout",2)) as payout'));

        // Execute the query.
        $revenueEvents = $revQuery->first();

        // Return a basic array.
        return collect($revenueEvents)->toArray();
    }

    /**
     * Allow us to build out a revenue array with a time frame.
     * @param $fromDate
     * @param $toDate
     * @param null $query
     * @param null $specificEvent
     * @return array
     */
    public function buildReportingRevenueArrayWithOptionalQuery($fromDate, $toDate, $query = null, $specificEvent = null)
    {
        // Default specific event we're tracking against.
        $defaultEvent = "revenue.track";

        if ($specificEvent != null) {
            $defaultEvent = $specificEvent;
        }

        // Retrieve the revenue track platform_event.
        if ($query != null) {
            $revenueEvents = (new PlatformEventHandlerServiceProvider())
                ->findEventsInDateRange($defaultEvent, $fromDate, $toDate)
                ->withDecodedValue($query["key"], $query["value"])
                ->get();
        } else {
            $revenueEvents = (new PlatformEventHandlerServiceProvider())
                ->findEventsInDateRange($defaultEvent, $fromDate, $toDate)
                ->get();
        }

        $amounts = [
            "revenue" => 0,
            "payout" => 0,
            "net" => 0
        ];

        if (count($revenueEvents) != 0) {
            foreach ($revenueEvents as $event) {
                // Decode the platform_event json value.
                // Add up all things.
                $amounts["revenue"] += $event["value"]["cpl"];
                $amounts["payout"] += $event["value"]["payout"];
                $amounts["net"] += $event["value"]["net"];
            }
        }

        return $amounts;
    }

    /**
     * Return leads.
     *
     * @param $campaignId
     * @param $fromDate
     * @param $toDate
     * @param $publisherId
     * @return array
     */
    private function returnAcceptedRejectedLeads($campaignId, $fromDate = null, $toDate = null, $publisherId = null)
    {
        $leadNumbers = DB::table('platform_events')
            ->where(function ($query) use ($campaignId) {
                $query->where('json_value->campaign_id', intval($campaignId))
                    ->orWhere('json_value->campaign_id', "" . $campaignId);
            })
            ->where(function ($query) {
                $query->where('name', 'lead.accepted')
                    ->orWhere('name', 'lead.rejected');
            });
        $leadQuery = Lead::where('campaign_id', $campaignId);

        // Make sure the dates are correct.
        if ($fromDate != null) {
            $leadNumbers->where('created_at', '>=', $fromDate);
            $leadQuery->where('created_at', '>=', $fromDate);
        }
        if ($toDate != null) {
            $leadNumbers->where('created_at', '<=', $toDate);
            $leadQuery->where('created_at', '<=', $toDate);
        }

        if ($publisherId != null) {
            $leadNumbers->where(function ($query) use ($publisherId) {
                $query->where('json_value->publisher_id', intval($publisherId))
                    ->orWhere('json_value->publisher_id', "" . $publisherId);
            });
        }


        // Execute the query.
        $leadNumbers = $leadNumbers->select(DB::raw('name as event_name,count(*) as leads'))
            ->groupBy(DB::raw('name'))
            ->get();

        $leadStatuses = [
            "accepted" => 0,
            "rejected" => 0,
            "generated" => $leadQuery->count()
        ];

        $leadNumbers->map(function ($item) use (&$leadStatuses) {
            if ($item->event_name == "lead.accepted") {
                $leadStatuses["accepted"] += $item->leads;
            } else if ($item->event_name == "lead.rejected") {
                $leadStatuses["rejected"] += $item->leads;
            }
        });

        $leadStatuses["unsent"] = $leadStatuses["generated"] - ($leadStatuses["accepted"] + $leadStatuses["rejected"]);


        return $leadStatuses;
    }

    /**
     * @param $fromDate
     * @param $toDate
     * @return array
     */
    public function returnAcceptedRejectedLeadsBetweenTimeframe($fromDate, $toDate)
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
        return [
            "accepted" => isset($leadNumbers[0]) ? $leadNumbers[0] : 0,
            "rejected" => isset($leadNumbers[1]) ? $leadNumbers[1] : 0,
            "generated" => Lead::where('created_at', '>=', $fromDate)->where('created_at', '<=', $toDate)->count()
        ];
    }

    /**
     * Return the accepted/rejected leads from an input array.
     * @param $array
     * @return array
     */
    public function returnAcceptedRejectedLeadsFromArray($array)
    {
        return (new LeadMoldingProvider())->getStatusesForLeads($array);
    }

    /**
     * @param $campaignId
     * @return array
     */
    private function returnPublisherMetrics($campaignId)
    {
        $publisherIds = PublisherCampaign::whereCampaignId($campaignId)->get(["publisher_id"])->toArray();
        $publishers = [];
        foreach ($publisherIds as $pid) {
            $publishers[] = Publisher::whereId($pid)->first();
        }
        $publisherMetricArray = [];
        foreach ($publishers as $publisher) {
            $publisherMetricArray[] = [
                "publisher_id" => $publisher->id,
                "publisher_name" => $publisher->name,
                "leads_captured" => Lead::whereCampaignId($campaignId)->wherePublisherId($publisher->id)->count(),
                "leads_accepted" => Lead::whereCampaignId($campaignId)->wherePublisherId($publisher->id)->count(),
                "leads_rejected" => 0,
                "revenue" => (Lead::whereCampaignId($campaignId)->wherePublisherId($publisher->id)->count() * CampaignAttribute::whereName("cpl")->whereCampaignId($campaignId)->first()->data->value),
                "payout" => Lead::whereCampaignId($campaignId)->wherePublisherId($publisher->id)->count() * PublisherCampaign::wherePublisherId($publisher->id)->whereCampaignId($campaignId)->first()->payout,
                "lead_cap" => PublisherCampaign::wherePublisherId($publisher->id)->whereCampaignId($campaignId)->first()->lead_cap
            ];
        }
        return $publisherMetricArray;
    }

}
