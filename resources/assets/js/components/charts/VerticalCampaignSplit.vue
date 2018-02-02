<template>
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading clean text-center">Vertical Type % Split</div>
                <div class="panel-body">
                    <vertical-campaign-split></vertical-campaign-split>
                </div>
            </div>
        </div>
    </div>
</template>
<script type="text/babel">
    import VerticalCampaignSplit from './VerticalCampaignSplit';
    export default {
        name: "VerticalCampaignSplit",
        components: {
            "vertical-campaign-split": VerticalCampaignSplit
        }
    }
</script>