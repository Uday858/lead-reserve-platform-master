<?php

namespace App\Http\Controllers;

use App\CampaignPostingParameter;
use Illuminate\Http\Request;

class CampaignSettingsApiController extends Controller
{
    /**
     * Retrieve the campaign posting parameters for the frontend component.
     * @param $campaignId
     * @return \Illuminate\Support\Collection
     */
    public function getPostingParameters($campaignId)
    {
        return CampaignPostingParameter::whereCampaignId($campaignId)->get();
    }
}
