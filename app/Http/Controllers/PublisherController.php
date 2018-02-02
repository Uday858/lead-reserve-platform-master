<?php

namespace App\Http\Controllers;

use App\Campaign;
use App\CampaignPostingParameter;
use App\FoundationAttribute;
use App\Lead;
use App\Providers\LeadMoldingProvider;
use App\Providers\MutableDataProcessorProvider;
use App\Providers\PostingServiceProvider;
use App\Providers\ReportingMetricsProvider;
use App\Publisher;
use App\PublisherCampaign;
use App\ResourceAccessCode;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use App\Mail\AssignedPublisherToCampaign;
use Illuminate\Support\Facades\Mail;

class PublisherController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view("publishers.index",[
            "publishers" => Publisher::all([
                "id", "name", "email"
            ])
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view("publishers.create");
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Create the new publisher.
        $publisher = Publisher::create([
            "owner_user_id" => Auth::user()->id,
            "name" => Input::get("name"),
            "email" => Input::get("email")
        ]);

        // Run through each attribute sent.
        foreach (collect(Input::get("attributes"))->toArray() as $item => $value) {
            // Update if existing.
            if (FoundationAttribute::whereFoundationParentId($publisher->id)->whereName($item)->exists()) {
                (new MutableDataProcessorProvider())->updateDataPair(FoundationAttribute::whereFoundationParentId($publisher->id)->whereName($item)->first()->storage_id, $value);
            } else {
                $storageItem = (new MutableDataProcessorProvider())->createNewDataPair($publisher->id, "advertiser", $value);
                FoundationAttribute::create([
                    "foundation_parent_id" => $publisher->id,
                    "name" => $item,
                    "storage_id" => $storageItem->id
                ]);
            }
        }

        return redirect(route("publishers.show",["id" => $publisher->id]));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return view('publishers.show',[
            'publisherMetrics' => (new ReportingMetricsProvider())->getAgedLeadgenPublisherPerformance($id),
            'publisher' => Publisher::whereId($id)->first()
        ]);
    }

    /**
     * @param $publisherId
     * @param $campaignId
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showCampaign($publisherId,$campaignId)
    {
        $postingSpecURL = "";
        if(ResourceAccessCode::whereResourcePath("posting.".$campaignId.".".$publisherId)->exists()) {
            $postingSpecURL = route('resources.access',['accessCode' => md5(ResourceAccessCode::whereResourcePath("posting.".$campaignId.".".$publisherId)->first()->access_secret)]);
        } else {
            $resourceAccessCode = ResourceAccessCode::create([
               'is_active' => 1,
                'resource_name' => "Posting Instructions",
                'resource_path' => "posting.".$campaignId.".".$publisherId,
                'access_secret' => 'pub'.$publisherId.".".Carbon::now()->toAtomString()
            ]);
            $postingSpecURL = route('resources.access',["accessCode" => md5($resourceAccessCode->access_secret)]);
        }

        if(Input::get("showleads")) {
            $extArray = [
                'leads_captured' => (new LeadMoldingProvider())->generateLeadsFromCampaignAndPublisherIds($campaignId,$publisherId)
            ];
        } else {
            $extArray = [];
        }

        return view('publishers.showCampaign',array_merge([
            'publisher' => Publisher::whereId($publisherId)->first(),
            'campaign' => Campaign::whereId($campaignId)->first(),
            'publisher_url' => $postingSpecURL,
            'posting_parameters' => CampaignPostingParameter::whereCampaignId($campaignId)->get()
        ],$extArray));
    }

    /**
     * Assign a new campaign to a publisher.
     *
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function assign($id)
    {
        return view('publishers.assign',[
            'publisher' => Publisher::whereId($id)->first(),
            'campaigns' => Campaign::all()
        ]);
    }

    /**
     * @param $publisherCampaignId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function unassign($publisherCampaignId)
    {
        if(PublisherCampaign::whereId($publisherCampaignId)->exists()) {
            PublisherCampaign::whereId($publisherCampaignId)->delete();
        }
        return back();
    }

    /**
     * Assign a campaign to a publisher.
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function campaignAssign()
    {
        $publisherCampaign = PublisherCampaign::create([
            "publisher_id" => Input::get("publisher_id"),
            "campaign_id" => Input::get("campaign_id"),
            "payout" => Input::get("payout"),
            "lead_cap" => Input::get("lead_cap")
        ]);

        $campaignId = $publisherCampaign->campaign_id;
        $publisherId = $publisherCampaign->publisher_id;

        $postingSpecURL = "";
        if(ResourceAccessCode::whereResourcePath("posting.".$campaignId.".".$publisherId)->exists()) {
            $postingSpecURL = route('resources.access',['accessCode' => md5(ResourceAccessCode::whereResourcePath("posting.".$campaignId.".".$publisherId)->first()->access_secret)]);
        } else {
            $resourceAccessCode = ResourceAccessCode::create([
               'is_active' => 1,
                'resource_name' => "Posting Instructions",
                'resource_path' => "posting.".$campaignId.".".$publisherId,
                'access_secret' => 'pub'.$publisherId.".".Carbon::now()->toAtomString()
            ]);
            $postingSpecURL = route('resources.access',["accessCode" => md5($resourceAccessCode->access_secret)]);
        }

        (new \App\Jobs\CacheDispenser\CampaignData())->generate();
        (new \App\Jobs\CacheDispenser\CampaignPublisherData())->generate();

        $message = (new AssignedPublisherToCampaign($publisherCampaign->campaign_id,$publisherCampaign->publisher_id,$postingSpecURL,$publisherCampaign->payout,$publisherCampaign->lead_cap));
        $publisher = Publisher::whereId($publisherCampaign->publisher_id)->first();
        Mail::to($publisher->email,$publisher->name)->send($message);

        return back();
    }

    /**
     * Update the campaign update.
     * @param $id
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function campaignAssignUpdate($id)
    {
        PublisherCampaign::whereId($id)->update([
            "payout" => Input::get("payout"),
            "lead_cap" => Input::get("lead_cap"),
        ]);
        return back();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return view("publishers.edit",[
            "publisher" => Publisher::whereId($id)->first()
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        Publisher::whereId($id)->update([
            "name" => Input::get("name"),
            "email" => Input::get("email")
        ]);

        // Run through each attribute sent.
        foreach (collect(Input::get("attributes"))->toArray() as $item => $value) {
            // Update if existing.
            if (FoundationAttribute::whereFoundationParentId($id)->whereName($item)->exists()) {
                (new MutableDataProcessorProvider())->updateDataPair(FoundationAttribute::whereFoundationParentId($id)->whereName($item)->first()->storage_id, $value);
            } else {
                $storageItem = (new MutableDataProcessorProvider())->createNewDataPair($id, "advertiser", $value);
                FoundationAttribute::create([
                    "foundation_parent_id" => $id,
                    "name" => $item,
                    "storage_id" => $storageItem->id
                ]);
            }
        }

        return redirect(route("publishers.show",["id" => $id]));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
