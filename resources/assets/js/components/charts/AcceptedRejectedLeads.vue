<template>
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading clean text-center">Accepted/Rejected Leads</div>
                <div class="panel-body">
                    <accept-rejected-leads></accept-rejected-leads>
                </div>
            </div>
        </div>
    </div>
</template>
<script type="text/babel">
    import AcceptedRejectedLeads from './AcceptedRejectedLeads';
    export default {
        name: "AcceptedRejectedLeads",
        components: {
            "accept-rejected-leads": AcceptedRejectedLeads
        }
    }
</script>