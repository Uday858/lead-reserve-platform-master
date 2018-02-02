<template>
    <div class="PublisherPerformanceTable">
        <p class="text-center" v-if="currentlyLoading">
            <i class="fa fa-cog fa-spin fa-3x fa-fw"></i>
        </p>
        <table v-if="!currentlyLoading">
            <tr v-for="publisher in publishers">
                <td></td>
                <td></td>
                <td>
                    <a :href="'/dashboard/publishers/' + publisher.publisher_id">{{publisher.publisher_name}}</a>
                </td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
        </table>
    </div>
</template>
<script type="text/babel">
    import {ResourceHttp} from '../../services/Resource';
    export default {
        name: "PublisherPerformanceTable",
        props: ["campaignid"],
        data() {
            return {
                currentlyLoading:true,
                publishers:[]
            };
        },
        mounted() {
            ResourceHttp.get("com/frontend/publisher-performance/" + this.campaignid).then((res) => {
                this.currentlyLoading = false;
                this.publishers = res.data;
            });
        }
    }
</script>