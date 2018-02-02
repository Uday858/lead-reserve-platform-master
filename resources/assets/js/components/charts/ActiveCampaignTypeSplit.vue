<template>
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading clean text-center">Active Campaign Type % Split</div>
                <div class="panel-body">
                    <active-campaign-type-split></active-campaign-type-split>
                </div>
            </div>
        </div>
    </div>
</template>
<script type="text/babel">
    import ActiveCampaignTypeSplit from './ActiveCampaignTypeSplit';
    export default {
        name: "ActiveCampaignTypeSplit",
        components: {
            "active-campaign-type-split": ActiveCampaignTypeSplit
        }
    }
</script>