<?php

namespace App\Http\Controllers;

use App\Providers\PostingServiceProvider;
use App\PublisherCampaign;
use Illuminate\Http\Request;

class OfferPathController extends Controller
{
    public function getPublisherPath($publisherId)
    {
        // Retrieve all campaigns (assigned to by publisher.)
        $publisherCampaigns = PublisherCampaign::wherePublisherId($publisherId)->get();

        // Empty array to contain offers.
        $offers = [];

        // Loop through all publisher campaigns and build out offer.
        foreach($publisherCampaigns as $publisherCampaign) {
            $offers[] = $this->buildOutOffer($publisherId,$publisherCampaign);
        }

        // Return the offer path.
        return view("offers.path", [
            "offers" => $offers,
        ]);
    }

    /**
     * Build out offer.
     *
     * @param $publisherId
     * @param $publisherCampaign
     * @return array
     */
    private function buildOutOffer($publisherId,$publisherCampaign)
    {
        return [
            "campaign_id" => $publisherCampaign->campaign->id,
            "publisher_id" => $publisherId,
            "is_linkout" => ($publisherCampaign->campaign->type->name == "CPA" || $publisherCampaign->campaign->type->name == "Linkout"),
            "offer_creative" => $publisherCampaign->campaign->hasAttributeOrEmpty("creative_image_url"),
            "offer_heading" => $publisherCampaign->campaign->hasAttributeOrEmpty("creative_heading"),
            "offer_text" => $publisherCampaign->campaign->hasAttributeOrEmpty("creative_text"),
            "offer_tcpa" => $publisherCampaign->campaign->hasAttributeOrEmpty("tcpa_text"),
            "offer_posting_action" => $this->buildOutOfferPostingAction($publisherId,$publisherCampaign)
        ];
    }

    /**
     * Retrieve and build out the posting action URL.
     *
     * @param $publisherId
     * @param $publisherCampaign
     * @return string
     */
    private function buildOutOfferPostingAction($publisherId,$publisherCampaign)
    {
        if($publisherCampaign->campaign->type->name == "CPA" || $publisherCampaign->campaign->type->name == "Linkout") {
            return (new PostingServiceProvider())->generatePublisherURL($publisherCampaign->campaign->id,$publisherId);
        } else {
            return (new PostingServiceProvider())->generateInternalPublisherURL() . "cid=" . $publisherCampaign->campaign->id . "&pid=" . $publisherId . "&";
        }
    }
}
