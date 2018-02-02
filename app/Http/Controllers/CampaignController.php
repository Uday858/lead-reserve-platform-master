<?php

namespace App\Http\Controllers;

use App\Advertiser;
use App\Campaign;
use App\CampaignAttribute;
use App\CampaignField;
use App\CampaignPostingParameter;
use App\Lead;
use App\MutableDataPair;
use App\Providers\MutableDataProcessorProvider;
use App\Providers\ReportingMetricsProvider;
use App\Publisher;
use App\PublisherCampaign;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class CampaignController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view("campaigns.index", [
            "campaigns" => Campaign::all()
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('campaigns.create', [
            "advertisers" => Advertiser::all([
                "id", "name"
            ])
        ]);
    }

    public function duplicateCampaign(Request $request, $oldCampaignId)
    {
        // Create a new campaign.
        $newCampaign = Campaign::create([
            "advertiser_id" => Input::get("advertiser_id"),
            "name" => Input::get("name"),
            "campaign_type_id" => Input::get("campaign_type_id"),
            "posting_url" => Input::get("posting_url")
        ]);

        // Get the campaign attributes.
        $oldCampaignAttributes = CampaignAttribute::whereCampaignId($oldCampaignId)->get();
        foreach($oldCampaignAttributes as $campaignAttribute) {
            $storageItem = (new MutableDataProcessorProvider())->createNewDataPair($newCampaign->id, "campaign", $campaignAttribute->data->value);
            CampaignAttribute::create([
                "campaign_id" => $newCampaign->id,
                "name" => $campaignAttribute->name,
                "storage_id" => $storageItem->id
            ]);
        }

        // Get the campaign fields.
        $oldCampaignFields = CampaignField::whereCampaignId($oldCampaignId)->get();
        foreach($oldCampaignFields as $campaignField) {
           CampaignField::create(array_merge([
                "campaign_id" => $newCampaign->id
            ],collect($campaignField)->except(["id","campaign_id","created_at","updated_at"])->toArray()));
        }

        // Redirect to the new campaign.
        return redirect(route("campaigns.show",["id" => $newCampaign->id]));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $campaign = Campaign::create([
            "advertiser_id" => Input::get("advertiser_id"),
            "name" => Input::get("name"),
            "campaign_type_id" => Input::get("campaign_type_id"),
            "posting_url" => Input::get("posting_url")
        ]);
        return redirect(route("campaigns.show", ["id" => $campaign->id]));
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // Retrieve the campaign object.
        $campaign = Campaign::whereId($id)->first();

        // Generate the postingURLGeneratedArray.
        $postingURLMappedArray = [];
        try {
            parse_str(parse_url($campaign->posting_url)["query"], $postingURLMappedArray);
        } catch (\Exception $e) {
            Log::error($e->getTraceAsString());
        }


        return view('campaigns.show', [
            "campaign" => Campaign::whereId($id)->first(),
            "default_posting_params" => $postingURLMappedArray,
            "metrics" => $this->returnReportingMetrics($id),
            // "publisher_metrics" => $this->returnPublisherMetrics($id)
        ]);
    }

    /**
     * @param $campaignId
     * @return array
     */
    private function returnReportingMetrics($campaignId)
    {

        // TODO: getting type here... if linkout, just shoot back metric_impressions,metric_clicks,metric_conversions.
        if(Campaign::whereId($campaignId)->first()->type->name == "Linkout" || Campaign::whereId($campaignId)->first()->type->name == "CPA") {

            $campaign = Campaign::whereId($campaignId)->first();

            return array_merge([
                "active_publishers" => count($campaign->publishers),
                /*"leads_captured" => $campaignMetrics["leads_generated"],
                "leads_accepted" => $campaignMetrics["leads_accepted"],*/
                "revenue_generated" => /*$campaign->revenue*/0,
                "net_generated" => 0,
                "payout_amounts" => 0,
                /*"publishers" => $campaign->publishers*/
            ],[
                "metric_impressions" => 0,
                "metric_clicks" => 0,
                "metric_conversions" => 0,
                "metric_click_through_rate" => 0 . "%",
                "metric_conversion_rate" => 0 . "%",
            ]);            
        }

        // Find the campaign metrics.
        $campaignMetrics = ((new ReportingMetricsProvider())->getAgedLeadgenPerformance(null,null,$campaignId));
        
        $campaignMetricsAreEmpty = false;

        if(count($campaignMetrics) == 0) {
            $campaignMetricsAreEmpty = true;
            $campaignMetrics = [
                "publishers" => [],
                "leads_generated" => 0,
                "leads_accepted" => 0,
                "leads_rejected" => 0,
                "metric_impressions" => 0,
                "metric_clicks" => 0,
                "metric_conversions" => 0,
                "revenue" => 0,
                "payout" => 0,
            ];
        } else {
            $campaignMetrics = $campaignMetrics[0];
        }

        $additionalMetrics = [];
        if (Campaign::whereId($campaignId)->first()->type->name == "CPA" || Campaign::whereId($campaignId)->first()->type->name == "Linkout") {
            if($campaignMetricsAreEmpty) {
                $additionalMetrics = [
                    "metric_impressions" => 0,
                    "metric_clicks" => 0,
                    "metric_conversions" => 0,
                    "metric_click_through_rate" => 0 . "%",
                    "metric_conversion_rate" => 0 . "%"
                ];
            } else {
                $additionalMetrics = [
                    "metric_impressions" => $campaignMetrics["metric_impressions"],
                    "metric_clicks" => $campaignMetrics["metric_clicks"],
                    "metric_conversions" => $campaignMetrics["metric_conversions"],
                    "metric_click_through_rate" => number_format(($campaignMetrics["metric_clicks"] / ($campaignMetrics["metric_impressions"] == 0 ? 1 : $campaignMetrics["metric_impressions"])) * 100, 0) . "%",
                    "metric_conversion_rate" => number_format(($campaignMetrics["metric_conversions"] / ($campaignMetrics["metric_clicks"] == 0 ? 1 : $campaignMetrics["metric_clicks"])) * 100, 0) . "%",
                ];    
            }
            
        }

        // TODO: if data doesn't exist, return an error_message.

        // Return the reporting metrics.
        return array_merge([
            "active_publishers" => count($campaignMetrics["publishers"]),
            "leads_captured" => $campaignMetrics["leads_generated"],
            "leads_accepted" => $campaignMetrics["leads_accepted"],
            "revenue_generated" => $campaignMetrics["revenue"],
            "net_generated" => $campaignMetrics["revenue"]-$campaignMetrics["payout"],
            "payout_amounts" => $campaignMetrics["payout"],
            "publishers" => $campaignMetrics["publishers"]
        ], $additionalMetrics);
    }

    /**
     * @param $campaignId
     * @return array
     */
    private function returnPublisherMetrics($campaignId)
    {
        return (new ReportingMetricsProvider())->returnCampaignPublishersReportingMetrics($campaignId);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        Campaign::whereId($id)->update([
            "name" => Input::get("name"),
            "campaign_type_id" => Input::get("campaign_type_id"),
            "posting_url" => Input::get("posting_url")
        ]);

        // Generate campaign cache, on update.
        (new \App\Jobs\CacheDispenser\CampaignData())->generate();

        return back();
    }

    /**
     * Update the existing campaign attributes/create new attributes.
     *
     * @param $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateAttributes(Request $request, $id)
    {
        // Campaign name.
        $campaignName = Campaign::whereId($id)->first()->name;

        if (($request->file("attributes")["creative_image_url"] !== null)) {
            $fileName = $this->uploadImage($campaignName,$request->file("attributes")["creative_image_url"]);
            try {
                if(CampaignAttribute::whereCampaignId($id)->whereName("creative_image_url")->exists()) {
                    (new MutableDataProcessorProvider())->updateDataPair(CampaignAttribute::whereCampaignId($id)->whereName("creative_image_url")->first()->storage_id, $fileName);
                } else {
                    $storageItem = (new MutableDataProcessorProvider())->createNewDataPair($id, "campaign", $fileName);
                    CampaignAttribute::create([
                        "campaign_id" => $id,
                        "name" => "creative_image_url",
                        "storage_id" => $storageItem->id
                    ]);
                }

            } catch (\Exception $e) {
                Log::info("Something or other..",[$e->getMessage() . $e->getTraceAsString()]);
            }
        }

        // Run through each attribute sent.
        foreach (collect(Input::get("attributes"))->toArray() as $item => $value) {
            // Update if existing.
            if (CampaignAttribute::whereCampaignId($id)->whereName($item)->exists()) {
                try {
                    (new MutableDataProcessorProvider())->updateDataPair(CampaignAttribute::whereCampaignId($id)->whereName($item)->first()->storage_id, $value);
                } catch (\Exception $e) {
                    // TODO: Swallow the exception.
                }
            } else {
                $storageItem = (new MutableDataProcessorProvider())->createNewDataPair($id, "campaign", $value);
                CampaignAttribute::create([
                    "campaign_id" => $id,
                    "name" => $item,
                    "storage_id" => $storageItem->id
                ]);
            }
        }

        // Go back.
        return back();
    }

    /**
     * Upload image to S3
     * @param $campaignName
     * @param $fileObject
     * @return string
     */
    private function uploadImage($campaignName, $fileObject)
    {
        // Change the campaign name, to something that is friendly.
        $newCampaignName = str_replace(' ', '_', strtolower($campaignName));

        // Create the image file name.
        $imageFileName = $newCampaignName . "__" . time() . "." . $fileObject->getClientOriginalExtension();

        // Fetch the S3 disk.
        $s3 = Storage::disk('s3');

        // Upload to the creatives directory.
        $s3->put('/creatives/' . $imageFileName, file_get_contents($fileObject), 'public');

        // Return the S3 URL.
        return 'https://s3.amazonaws.com/lr-offer-assets/creatives/' . $imageFileName;
    }

    /**
     * Create a posting parameter for a campaign.
     *
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function createPostingParam($id)
    {
        // Calculate the incoming field.
        $incomingField = (Input::get("paramType") == "field") ? Input::get("value")[Input::get("paramType")] : (Input::get("paramType") == "system") ? Input::get("value")[Input::get("paramType")] : "";

        // Create the campaign posting parameter.
        CampaignPostingParameter::create([
            "campaign_id" => $id,
            "label" => Input::get("label"),
            "type" => Input::get("paramType"),
            "is_static" => (Input::get("paramType") == "static" || Input::get("paramType") == "randomComma" || Input::get("paramType") == "dropdown"),
            "static_value" => (Input::get("paramType") == "static" || Input::get("paramType") == "randomComma" || Input::get("paramType") == "dropdown") ? Input::get("value")[Input::get("paramType")] : "",
            "incoming_field" => $incomingField,
            "outgoing_field" => Input::get("field")
        ]);

        // Return a redirect back.
        return back();
    }

    /**
     * Delete a posting param.
     *
     * @param $campaignId
     * @param $postingParamId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deletePostingParam($campaignId, $postingParamId)
    {
        if (CampaignPostingParameter::whereCampaignId($campaignId)->whereId($postingParamId)->exists()) {
            CampaignPostingParameter::whereCampaignId($campaignId)->whereId($postingParamId)->delete();
        }

        // Return a redirect.
        return back();
    }

    /**
     * Update the posting parameters for the campaign.
     *
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updatePostingParams($id)
    {
        $postingParams = collect(Input::get("posting-params"));
        for ($i = 0; $i <= count($postingParams->get("type")) - 1; $i++) {
            if (isset($postingParams->get("action")[$i])) {
                if ($postingParams->get("action")[$i] == "remove") {
                    CampaignPostingParameter::whereId($postingParams->get("id")[$i])->delete();
                } else if ($postingParams->get("action")[$i] == "add") {
                    CampaignPostingParameter::create([
                        "campaign_id" => $id,
                        "type" => $postingParams->get("type")[$i],
                        "incoming_field" => $postingParams->get("incoming_field")[$i],
                        "outgoing_field" => $postingParams->get("outgoing_field")[$i],
                        "label" => $postingParams->get("label")[$i],
                    ]);
                }
            } else if (isset($postingParams->get("id")[$i])) {
                CampaignPostingParameter::whereId($postingParams->get("id")[$i])->update([
                    "type" => $postingParams->get("type")[$i],
                    "incoming_field" => $postingParams->get("incoming_field")[$i],
                    "outgoing_field" => $postingParams->get("outgoing_field")[$i],
                    "label" => $postingParams->get("label")[$i]
                ]);
            }
        }
        return back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (Campaign::whereId($id)->exists()) {
            // This destroys everything.
            CampaignAttribute::whereCampaignId($id)->delete();
            CampaignPostingParameter::whereCampaignId($id)->delete();
            PublisherCampaign::whereCampaignId($id)->delete();
            Campaign::whereId($id)->delete();
        }

        return redirect(route("campaigns.index"));
    }
}
