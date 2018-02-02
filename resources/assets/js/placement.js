/**
 * Bring in all requirements.
 */
require('./bootstrap');
require('./config');

/**
 * Setup the LeadReserve Offer Wall.
 * @type {{init: Window.leadReserveOfferWall.init}}
 */
window.leadReserveOfferWall = {
    generateElement: function (publisherId, leadObject) {
        // Create the dataString.
        var dataString = JSON.stringify(leadObject);

        // Source the iframe.
        var iframeSource = config.apiUrl + "/v1/offer-path/publisher/" + publisherId;

        // Return the iframe source element.
        return "<iframe name='" + dataString + "' src='" + iframeSource + "' style='border:unset;width:100%;height:600px;overflow:hidden!important;'></iframe>";
    }
};