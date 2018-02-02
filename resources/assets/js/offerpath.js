/**
 * Offer Path.
 * @type {{}}
 */
var OfferPath = {

    /**
     * Lead Object Variable.
     */
    leadObject: {},

    /**
     * Campaign ID Array.
     */
    campaignIds: [],

    /**
     * Setup OfferPath Variables
     */
    setupVariables: function () {
        // Access parent container's name, and attribute it to our lead object.
        OfferPath.leadObject = JSON.parse(window.name);

        // Load up our campaign Ids.
        OfferPath.getCampaignIdsFromOffers();
    },

    /**
     * Submit lead to a campaign.
     * @param campaignId
     * @param postingAction
     */
    submitLeadToCampaign: function (campaignId, postingAction) {
        // Go ahead and make sure that our lead object is not equal to a blank object.
        if (OfferPath.leadObject != {}) {
            OfferPath.hideCampaign(campaignId);
            OfferPath.showLoadingPanel();
            $.post(postingAction + $.param(OfferPath.leadObject)).then(function () {
                OfferPath.hideLoadingPanel();
                /*
                if (OfferPath.getNextCampaignId(campaignId) != -1 && OfferPath.getNextCampaignId(campaignId) != undefined) {
                    OfferPath.showCampaign(OfferPath.getNextCampaignId(campaignId));
                } else {
                    OfferPath.showEndPanel();
                }*/
            });
        } else {
            // TODO: Come up with a better error handling/messaging system.
            alert("Please contact site owner.");
        }
    },

    /**
     * Redirect person to the right page.
     * @param campaignId
     * @param postingAction
     */
    redirectToOffer: function(campaignId, postingAction) {
        // Open new offer window with URL.
        window.open(postingAction,"Your Deals!");
        // Go to next campaign or end offer cycle.
        OfferPath.hideCampaign(campaignId);
        /*
        if (OfferPath.getNextCampaignId(campaignId) != -1 && OfferPath.getNextCampaignId(campaignId) != undefined) {
            OfferPath.showCampaign(OfferPath.getNextCampaignId(campaignId));
        } else {
            OfferPath.showEndPanel();
        }*/
    },

    /**
     * The lead says no to the campaign shown.
     * @param campaignId
     */
    declineCampaign: function (campaignId) {
        OfferPath.hideCampaign(campaignId);
        /*if (OfferPath.getNextCampaignId(campaignId) != -1 && OfferPath.getNextCampaignId(campaignId) != undefined) {
            OfferPath.showCampaign(OfferPath.getNextCampaignId(campaignId));
        } else {
            OfferPath.showEndPanel();
        }*/
    },

    /**
     * Show Campaign based off of CampaignId.
     * @param campaignId
     */
    showCampaign: function (campaignId) {
        $("div.offer-path-offer.offer-" + campaignId).addClass("show");
    },

    /**
     * Show all campaigns.
     */
    showAllCampaigns: function() {
        $("div.offer-path-offer").addClass("show");
    },

    /**
     * Hide campaign
     * @param campaignId
     */
    hideCampaign: function (campaignId) {
        $("div.offer-path-offer.offer-" + campaignId).removeClass("show");
    },

    /**
     * Get campaign id from offer.
     */
    getCampaignIdsFromOffers: function () {
        $.each($("div.offer-path-offer"), function (i, v) {
            OfferPath.campaignIds.push(parseInt($(v).attr("data-campaign-id")));
        });
    },

    /**
     * Get the next campaign id.
     * @param currentCampaignId
     * @returns {number}
     */
    getNextCampaignId: function (currentCampaignId) {
        if ((OfferPath.campaignIds.indexOf(parseInt(currentCampaignId)) + 1) != -1) {
            return OfferPath.campaignIds[OfferPath.campaignIds.indexOf(parseInt(currentCampaignId)) + 1];
        } else {
            return -1;
        }
    },

    /**
     * Campaign offset exist?
     * @param campaignOffset
     * @returns {boolean}
     */
    campaignOffsetExists: function (campaignOffset) {
        return (OfferPath.campaignIds[campaignOffset]) ? true : false;
    },

    /**
     * Show the loading panel.
     */
    showLoadingPanel: function () {
        $("div.offer-path-loading-panel").show();
    },

    /**
     * Hide the loading panel.
     */
    hideLoadingPanel: function () {
        $("div.offer-path-loading-panel").hide();
    },

    /**
     * Show the "completion panel."
     */
    showEndPanel: function () {
        $("div.offer-path-completion-panel").addClass("show");
    }
};

(function ($) {
    $(document).ready(function () {
        if ($("div#execute-offer-path-js")[0]!=undefined) {
            window.OfferPath = OfferPath;

            // Do this to init the offerPath component.
            window.OfferPath.setupVariables();

            // Show the first offer.
            setTimeout(function () {
                window.OfferPath.hideLoadingPanel();
                window.OfferPath.showAllCampaigns();
            }, 75);
        }
    })
})(jQuery);