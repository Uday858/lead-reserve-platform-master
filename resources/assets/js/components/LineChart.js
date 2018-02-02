import {Line} from 'vue-chartjs';
import {ResourceHttp} from '../services/Resource';

/**
 * Extend the line object.
 */
export default Line.extend({
    mounted() {

        ResourceHttp.get("resources/front-reports/weekly").then(res => {
            // Format the response data.
            let data = res.data;

            // Create the labels array + datasets array, to store the relevant information.
            var labels = [];
            var datasets = {
                revenue: [],
                payout: [],
                net: []
            };

            // Go through the response data and format the aforementioned empty arrays.
            data.forEach((i) => {
                labels.push(i.day);
                datasets.revenue.push(i.revenue);
                datasets.payout.push(i.payout);
                datasets.net.push(i.net);
            });

            this.renderChart({
                labels: labels,
                datasets: [
                    {
                        label: 'Net Margins',
                        borderColor:'#00FF84',
                        backgroundColor: 'rgba(0, 255, 132, 0.40)',
                        data: datasets.net
                    },{
                        label: 'Losses, Payouts',
                        borderColor:'#FF003B',
                        backgroundColor: 'rgba(255, 0, 59, 0.40)',
                        data: datasets.payout
                    },{
                        label: 'Gains, Revenue',
                        borderColor:'#A708FF',
                        backgroundColor: 'rgba(167, 8, 255, 0.40)',
                        data: datasets.revenue
                    }
                ]
            }, {responsive: true, maintainAspectRatio: false, legend: {
                labels : {
                    fontColor : '#ffffff'
                }
            }});
        });
    }
});