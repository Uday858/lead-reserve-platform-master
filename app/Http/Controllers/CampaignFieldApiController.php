<?php

namespace App\Http\Controllers;

use App\CampaignField;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

class CampaignFieldApiController extends Controller
{
    /**
     * Retrieve campaign fields.
     * @param $campaignId
     * @return \Illuminate\Support\Collection|string
     */
    public function getCampaignFields($campaignId)
    {
        if(CampaignField::whereCampaignId($campaignId)->count()!=0) {
            return CampaignField::whereCampaignId($campaignId)->get();
        } else {
            return "default";
        }
    }

    /**
     * Set the campaign fields.
     * @param $campaignId
     * @return string
     */
    public function setCampaignFields($campaignId)
    {
        $inputFields = Input::all();

        // Remove all of the campaign fields for this specific campaign.
        CampaignField::whereCampaignId($campaignId)->delete();

        foreach($inputFields as $field) {
            CampaignField::create(array_merge([
              "campaign_id" => $campaignId
            ],collect($field)->except(["id","campaign_id","created_at","updated_at"])->toArray()));
        }

        return "ok";
    }
}
