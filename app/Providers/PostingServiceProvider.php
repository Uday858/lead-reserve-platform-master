<?php

namespace App\Providers;

use App\Campaign;
use App\CampaignPostingParameter;
use App\Publisher;

/**
 * Class PostingServiceProvider
 */
class PostingServiceProvider
{
    /**
     * Generate the URL for any publisher when providing posting instructions.
     *
     * @param $campaignId
     * @param $publisherId
     * @return string
     */
    public function generatePublisherURL($campaignId, $publisherId)
    {
        // Retrieve the campaign.
        $campaign = Campaign::whereId($campaignId)->first();


        // Generate internal URL via api endpoint.
        switch ($campaign->type->name) {
            case "CPA":
            case "Linkout":
                $internalURL = "http://api." . env("APP_DOMAIN") . "/v1/lead/redirect/" . $campaignId . "/" . $publisherId;
                break;
            case "CPL":
            case "Leadgen":
            default:
                $internalURL = "http://api." . env("APP_DOMAIN") . "/v1/lead/capture?";
                break;
        }

        // Check to see if we're using a Leadgen or CPL campaign.
        if($campaign->type->name == "Leadgen" || $campaign->type->name == "CPL") {
            $newCampaignFields = $campaign->fields;

            // Create the campaign field array and build query.
            $campaignFieldArray = [];
            $campaignFieldArray["cid"] = $campaignId;
            $campaignFieldArray["pid"] = $publisherId;
            foreach ($newCampaignFields as $field) {
                if($field["type"] == "field" || $field["type"] == "inclusion") {
                    $campaignFieldArray[$field["incoming_field"]] = "[" . $field["label"] . "]";
                }
            }
            $query = urldecode(http_build_query($campaignFieldArray));

            // Give back the internalURL + "/?..." query.
            return $internalURL . $query;
        } else {
            return $internalURL;
        }
    }

    /**
     * @return string
     */
    public function generateInternalPublisherURL()
    {
        return "http://api." . env("APP_DOMAIN") . "/v1/lead/capture?";
    }

    public function generateInternalPublisherMountableURL()
    {
        return "http://api." . env("APP_DOMAIN") . "/v1/lead/capture/back?";
    }
}