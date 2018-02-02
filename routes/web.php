<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::domain("api." . env("APP_DOMAIN"))->group(function () {
    Route::prefix("v" . env("API_VERSION"))->group(function () {
        /*
         * API for Lead Capture
         * */
        Route::prefix("lead")->group(function () {
            // For co-reg and lead-gen campaign types.
            Route::match(["GET", "PUT", "PATCH", "DELETE"], "capture", "LeadController@captureRedirect")->name("lead.capture.error");
            Route::post("capture", "LeadController@capture")->name("lead.capture");

            // For CPA and Linkout campaign types.
            Route::get("impression/{campaign_id}/{publisher_id}", "LeadController@impression")->name("lead.impression");
            Route::get("redirect/{campaign_id}/{publisher_id}", "LeadController@linkout")->name("lead.linkout");
            Route::get("convert/{campaign_id}/{click_id}", "LeadController@convert")->name("lead.convert");
            Route::get("custom-convert","LeadController@customConvert")->name("lead.custom.convert");
            Route::post("convert/{campaign_id}/{click_id}", "LeadController@convert")->name("lead.convert.postback");
        });
        /*
         * API for Offer Wall (for publishers)
         * */
        Route::prefix("offer-path")->group(function () {
            Route::get("publisher/{id}", "OfferPathController@getPublisherPath")->name("offer.path.publisher");
        });
        /*
         * API for Resource Management
         * */
        Route::prefix("internal-resource")->middleware(['auth.access'])->group(function() {
            Route::post("access/{resource}/{queryKey}/{queryValue}","InternalResourceController@access")->name("api.internal.resource.access");
            Route::post("access-multiple/{resource}/{queryKey}/{queryValue}","InternalResourceController@accessMultiple")->name("api.internal.resource.access.multiple");
        });
    });
});

Route::get('/', function () {
    if (\Illuminate\Support\Facades\Auth::check()) {
        return redirect(route("dashboard"));
    } else {
        return view('welcome');
    }

});

Auth::routes();

Route::get('/dashboard', 'HomeController@index')->name('dashboard');

Route::prefix('resources')->group(function () {

    Route::get("access/{accessCode}","ResourceAccessController@access")->name("resources.access");
    Route::get("offerExample/{campaignId}","ResourceAccessController@accessOfferExample")->name("resources.example.offer");
    Route::get("offerResource/{campaignId}","ResourceAccessController@getAccessCodeForExampleOffer")->name("resources.get.offer");

    /*
     * API for front reports.
     * */
    Route::prefix("front-reports")->group(function () {
        Route::get("weekly", "FrontReportingApiController@weeklyFinances")->name("front.reporting.weekly.revenue");
        Route::get("campaign-type-split", "FrontReportingApiController@campaignTypeSplit")->name("front.reporting.campaign.type.split");
        Route::get("accept-reject-leads", "FrontReportingApiController@acceptRejectLeads")->name("front.reporting.accept.reject.leads");
    });
    /*
     * API for campaign settings.
     * */
    Route::prefix("campaign-settings")->group(function(){
        Route::get("parameters/{id}","CampaignSettingsApiController@getPostingParameters")->name("campaign.settings.posting.parameters");
    });
});
Route::prefix('com')->group(function(){

    Route::get('lead-status/{leadId}','LeadExplorerController@getLeadStatus')->name("com.lead.status");
    Route::get('lead-avatar-url/{leadEmailAddress}','LeadExplorerController@getLeadImageURL')->name("com.lead.image");
    Route::get('lead-full-history/{leadId}','LeadExplorerController@getFullLeadInformation')->name("com.lead.full.history");

    Route::prefix("campaign-fields")->group(function(){
       Route::get("/{campaignId}","CampaignFieldApiController@getCampaignFields")->name("com.campaign.fields.get");
        Route::post("/{campaignId}","CampaignFieldApiController@setCampaignFields")->name("com.campaign.fields.set");
    });
    Route::prefix("frontend")->group(function(){
        // Campaign Performance
        Route::get("campaign-performance","FrontReportingApiController@campaignPerformance")->name("front.reporting.campaign.performance");
        Route::get("publisher-performance/{campaignId}/","FrontReportingApiController@publisherPerformancePerCampaign")->name("front.reporting.campaign.performance");
    });


    /*
     * API for Resource Management
     * */
    Route::prefix("internal-resource")->middleware(['auth.access'])->group(function() {
        Route::post("access/{resource}/{queryKey}/{queryValue}","InternalResourceController@access")->name("api.internal.resource.access");
        Route::post("access-multiple/{resource}/{queryKey}/{queryValue}","InternalResourceController@accessMultiple")->name("api.internal.resource.access.multiple");
    });

});

/*
 * Dashboard Routes
 * */
Route::prefix('dashboard')->middleware(['auth'])->group(function () {
    /*
     * Advertiser Routes.
     * - If we need to override, express methods in front.
     * */
    Route::resource('advertisers', 'AdvertiserController');

    /*
     * Campaign Routes.
     * - If we need to override, express methods in front.
     * */
    Route::post('campaigns/create-posting-param/{id}', 'CampaignController@createPostingParam')->name('campaigns.create.posting-param');
    Route::post('campaigns/delete-posting-param/{campaignId}/{paramId}', 'CampaignController@deletePostingParam')->name('campaigns.destroy.posting-param');
    Route::post('campaigns/update-posting-params/{id}', 'CampaignController@updatePostingParams')->name("campaigns.update-posting-params");
    Route::post('campaigns/update-attributes/{id}', 'CampaignController@updateAttributes')->name('campaigns.update-attributes');
    Route::post('campaigns/duplicate/{id}', 'CampaignController@duplicateCampaign')->name('campaigns.duplicate');
    Route::resource('campaigns', 'CampaignController');

    /*
     * Publisher Routes.
     * - If we need to override, express methods in front.
     * */
    Route::get('publishers/{publisherId}/campaign/{campaignId}', 'PublisherController@showCampaign')->name('publishers.campaign');
    Route::get('publishers/assign/{id}', 'PublisherController@assign')->name('publishers.assign');
    Route::get('publishers/unassign/{publisherCampaignId}', 'PublisherController@unassign')->name('publishers.unassign');
    Route::post('publishers/assign', 'PublisherController@campaignAssign')->name('publishers.assign.store');
    Route::post('publishers/update/assign/{id}', 'PublisherController@campaignAssignUpdate')->name('publishers.assign.update');
    Route::resource('publishers', 'PublisherController');

    /*
     * Administrator (Reserve Tech) Routes
     * */
    Route::get('reporting', 'ReportingController@index')->name('reporting.index');

    /*
     * Lead Explorer Routes
     * */
    Route::get('lead-explorer','LeadExplorerController@index')->name('lead.explorer.index');
    Route::get('lead-explorer-history/{leadId}','LeadExplorerController@specificLeadHistory')->name('lead.explorer.detail');
    Route::get('transaction-history/{transactionId}','LeadExplorerController@transactionHistory')->name('transaction.history');

    /*
     * Developer Tool Routes
     */
    Route::get('platformevents/codeexplorer/{eventId}', 'PlatformEventController@codeExplorer')->name('platformevents.codeexplorer');
    Route::resource('platformevents', 'PlatformEventController');
    Route::resource('thirdpartyaccess', 'ThirdPartyAccessController');

    Route::prefix('developer-tools')->group(function(){
        Route::get('regex-tester','DeveloperToolsController@getRegularExpressionTester')->name('developer.tools.regex.tester');
        Route::get('regex-debug','DeveloperToolsController@debugRegularExpression')->name('developer.tools.regex.debug');
        Route::get('queue-tester','DeveloperToolsController@getQueueDebuggerTester')->name('developer.tools.queue.debugger.tester');
        Route::get('cache-tester','DeveloperToolsController@getCacheDebuggerTester')->name('developer.tools.cache.debugger.tester');
    });

});