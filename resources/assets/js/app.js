
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');
require('./editable');
require('./offerpath');

window.Vue = require('vue');

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

Vue.component('example', require('./components/Example.vue'));

// Charts
Vue.component('weeklyrevenue', require('./components/charts/WeeklyRevenue.vue'));
Vue.component('activecampaigntypesplit',require('./components/charts/ActiveCampaignTypeSplit.vue'));
Vue.component('verticalcampaignsplit',require('./components/charts/VerticalCampaignSplit.vue'));
Vue.component('acceptedrejectedleads',require('./components/charts/AcceptedRejectedLeads.vue'));
// CampaignSettings
Vue.component('campaignpostingparameters',require('./components/campaignSettings/CampaignPostingParameters.vue'));
// RegularExpressionTester
Vue.component('regularexpressiontester',require('./components/regexTester/RegularExpressionTester.vue'));
// CamapignPerformanceTable
Vue.component('campaignperformancetable',require('./components/campaignPerformance/CampaignPerformanceTable.vue'));

// LeadDetailExplorer
Vue.component('leaddetailexplorer',require('./components/LeadDetailExplorer.vue'));

// LeadCard (used to place on lead explorer, etc.)
Vue.component('leadcard',require('./components/LeadCard.vue'));

// LeadCard (used to place on lead explorer, etc.)
Vue.component('leadstatus',require('./components/LeadStatus.vue'));

const app = new Vue({
    el: '#app'
});