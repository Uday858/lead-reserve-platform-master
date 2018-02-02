<template>
    <div class="CampaignPerformanceTable">
        <p class="text-center" v-if="currentlyLoading">
            <i class="fa fa-cog fa-spin fa-3x fa-fw"></i>
        </p>
        <table class="table table-hover" v-if="!currentlyLoading">
            <tr class="heading">
                <th>Campaign<br/>
                    <small>Click for more information.</small>
                </th>
                <th>Advertiser<br/>
                    <small>Click for more information.</small>
                </th>
                <th>Publishers<br/>
                    <small>View all pubs.</small>
                </th>
                <th>
                    Leads<br/>
                    <small>Captures/Accepts/Rejects</small>
                </th>
                <th>
                    Metrics<br/>
                    <small>Gains/Margins/Payouts</small>
                </th>
                <th>
                    Details<br/>
                    <small>Daily Cap</small>
                </th>
            </tr>
            <template v-for="campaign in campaigns">
                <tr>
                    <td>
                        <a :href="'/dashboard/campaigns/' + campaign.campaign_id">
                            ({{campaign.campaign_id}}) {{campaign.campaign_name}}
                        </a>
                    </td>
                    <td>
                        <a :href="'/dashboard/advertisers/' + campaign.advertiser_id">
                            ({{campaign.advertiser_id}}) {{campaign.advertiser_name}}
                        </a>
                    </td>
                    <td>
                        <a class="btn btn-primary" style="color:#ffffff;" v-on:click="loadPublisher(campaign)">{{campaign.active_publishers}}</a>
                    </td>
                    <td>
                        {{campaign.leads_captured}} / {{campaign.leads_accepted}} / {{campaign.leads_rejected}}
                    </td>
                    <td>
                        {{formatCurrency(campaign.revenue_generated)}} / {{formatCurrency(campaign.net_generated)}} / {{formatCurrency(campaign.payout_amounts)}}
                    </td>
                    <td>
                        {{campaign.lead_cap}}
                    </td>
                </tr>
                <template v-if="campaign.isDisplayingPublishers">
                    <p class="text-center" v-if="campaign.isLoadingPublishers">
                        <i class="fa fa-cog fa-spin fa-3x fa-fw"></i>
                    </p>
                    <tr v-for="publisher in campaign.publishers" v-if="!campaign.isLoadingPublishers" :class="publisher.current_status">
                        <td></td>
                        <td></td>
                        <td>
                            <a :href="'/dashboard/publishers/' + publisher.publisher_id + '/campaign/' + campaign.campaign_id">
                                ({{publisher.publisher_id}}) {{publisher.publisher_name}}
                            </a>
                        </td>
                        <td>
                            <a :href="generateLeadExplorerURL(campaign.campaign_id,publisher.publisher_id)">{{publisher.leads_captured}} / {{publisher.leads_accepted}} / {{publisher.leads_rejected}}</a>
                        </td>
                        <td>
                            {{formatCurrency(publisher.revenue)}} / {{formatCurrency(publisher.revenue-publisher.payout)}} / {{formatCurrency(publisher.payout)}}
                        </td>
                        <td>
                            {{publisher.lead_cap}}
                        </td>
                    </tr>
                </template>
            </template>
        </table>
    </div>
</template>
<script type="text/babel">
    import {ResourceHttp} from '../../services/Resource';
    import PublisherPerformance from './PublisherPerformanceTable.vue';
    export default {
        name: "CampaignPerformanceTable",
        props: [
            "fromdate",
            "todate"
        ],
        components: {
            "publisher-performance": PublisherPerformance
        },
        data() {
            return {
                campaigns: [],
                currentlyLoading: true
            };
        },
        mounted() {
            ResourceHttp.get("com/frontend/campaign-performance/",{
                params:{
                    fromDate: this.fromdate,
                    toDate: this.todate,
                }
            }).then((res) => {
                this.currentlyLoading = false;
                this.campaigns = res.data.filter((item) => {
                    item.isDisplayingPublishers = false;
                    item.isLoadingPublishers = false;
                    item.publishers = [];
                    return item;
                });
            });
        },
        methods: {
            loadPublisher: function (campaign) {
                if (campaign.isDisplayingPublishers) {
                    campaign.isDisplayingPublishers = false;
                } else {
                    campaign.isDisplayingPublishers = true;
                    campaign.isLoadingPublishers = true;
                    ResourceHttp.get("com/frontend/publisher-performance/" + campaign.campaign_id,{
                        params:{
                            fromDate: this.fromdate,
                            toDate: this.todate,
                        }
                    }).then((res) => {
                        campaign.isLoadingPublishers = false;
                        campaign.publishers = res.data;
                    });
                }
            },
            generateLeadExplorerURL: function(campaign_id, publisher_id) {
                var queryObject = {
                    campaign_id: campaign_id,
                    publisher_id: publisher_id,
                    fromDate: this.fromdate,
                    toDate: this.todate
                };
                return "/dashboard/lead-explorer?" + $.param(queryObject);
            },
            formatCurrency: function(currencyAmount) {
                var formatter = new Intl.NumberFormat('en-US', {
                    style: 'currency',
                    currency: 'USD',
                    minimumFractionDigits: 2
                });
                return formatter.format(currencyAmount);
            }
        }
    }
</script>