<template>
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default" style="background-color:#2d2d2d;">
                <div class="panel-heading clean text-center">Weekly Revenue, Last 7 Days</div>
                <div class="panel-body">
                    <div id="WeeklyRevenueChart" class="WeeklyRevenue__Chart">
                        <line-chart></line-chart>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script type="text/babel">
    import LineChart from '../LineChart';
    import {ResourceHttp} from '../../services/Resource';
    export default {
        name: "WeeklyRevenue",
        components: {
            "line-chart": LineChart
        }
    }
</script>