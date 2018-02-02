<?php

namespace App\Http\Controllers;

use App\Campaign;
use App\CampaignAttribute;
use App\Publisher;
use App\PublisherCampaign;
use App\ResourceAccessCode;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class ResourceAccessController extends Controller
{
    /**
     * Access a resource.
     * @param $accessCode
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function access($accessCode)
    {
        foreach (ResourceAccessCode::all() as $resourceAccess) {
            Log::info("Acccess Code", [$accessCode, md5($resourceAccess["access_secret"])]);
            if ($accessCode == md5($resourceAccess["access_secret"])) {
                if ($resourceAccess["is_active"]) {
                    $context = explode(".", $resourceAccess["resource_path"]);
                    if ($context[0] == "posting") {
                        $campaignId = $context[1];
                        $publisherId = $context[2];
                        return view('resources.postingInstructions', [
                            'rac' => $resourceAccess,
                            'campaign' => Campaign::whereId($campaignId)->first(),
                            'publisher' => Publisher::whereId($publisherId)->first(),
                            'publisherCampaign' => PublisherCampaign::whereCampaignId($campaignId)->wherePublisherId($publisherId)->first()
                        ]);
                    } else if ($context[0] == "offer") {
                        $campaignId = $context[1];
                        return view('resources.exampleOffer', [
                            'rac' => $resourceAccess,
                            'campaign' => Campaign::whereId($campaignId)->first()
                        ]);
                    } else if ($context[0] == "campaignList") {
                        //$campaignsThatAreLive = 
                        $activeCampaignIDSelector = DB::table('campaign_attributes')
                              ->join('mutable_data_pairs','campaign_attributes.storage_id','=','mutable_data_pairs.id')
                              ->select(DB::raw('*'))
                              ->where('campaign_attributes.name','campaign_status')
                              ->where('mutable_data_pairs.string_value','live')
                              ->get(['campaign_id'])
                              ->pluck('campaign_id')
                              ->toArray();
                        $campaigns = Campaign::whereIn('id',$activeCampaignIDSelector)->get();
                        return view('resources.campaignList',[
                            'rac' => $resourceAccess,
                            'campaigns' => $campaigns
                        ]);
                    }
                } else {
                    return view('resources.notActive');
                }
            }
        }

        return view('errors.404');
    }

    /**
     * @param $campaignId
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function accessOfferExample($campaignId)
    {
        // Find campaign by id.
        $campaign = Campaign::whereId($campaignId)->first();

        return view('offers.example', [
            'offer' => [
                "campaign_id" => $campaignId,
                "publisher_id" => 0,
                "offer_creative" => $campaign->hasAttributeOrEmpty("creative_image_url"),
                "offer_heading" => $campaign->hasAttributeOrEmpty("creative_heading"),
                "offer_text" => $campaign->hasAttributeOrEmpty("creative_text"),
                "offer_tcpa" => $campaign->hasAttributeOrEmpty("tcpa_text"),
                "offer_posting_action" => ""
            ]
        ]);
    }

    /**
     * @param $campaignId
     * @return string
     */
    public function getAccessCodeForExampleOffer($campaignId)
    {
        if (ResourceAccessCode::whereResourcePath('offer.' . $campaignId)->exists()) {
            return redirect(route('resources.access',["accessCode" => md5(ResourceAccessCode::whereResourcePath('offer.' . $campaignId)->first()->access_secret)]));
        } else {
            $accessCode = ResourceAccessCode::create([
                "is_active" => 1,
                "resource_name" => "Offer Example",
                "resource_path" => "offer." . $campaignId,
                "access_secret" => "offer" . $campaignId . "." . Carbon::now()->toAtomString()
            ]);
            return redirect(route('resources.access',["accessCode" => md5($accessCode->access_secret)]));
        }
    }
}