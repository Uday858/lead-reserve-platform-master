import {Doughnut} from 'vue-chartjs';
import {ResourceHttp} from '../../services/Resource';
export default Doughnut.extend({
    name: "ActiveCampaignTypeSplit",
    mounted() {
        var self = this;
        ResourceHttp.get("resources/front-reports/campaign-type-split").then((d) => {
            // Data from the campaign type split.
            let data = d.data;
            var labels = [];
            var datasets = [{
                data: [],
                backgroundColor: []
            }];

            Object.keys(data).forEach((i) => {
                labels.push(i);
                datasets[0].data.push(data[i]);
                datasets[0].backgroundColor.push(self._getRandomRGBAColor("0.5"))
            });

            console.log({
                labels: labels,
                datasets: datasets
            });

            this.renderChart({
                labels: labels,
                datasets: datasets
            }, {responsive: true, maintainAspectRatio: false});
        });
    },
    methods:{
        _getRandomColorInt() {
            return Math.floor(Math.random() * (255 - 100) + 100);
        },
        _getRandomRGBAColor(opacity) {
            return "rgba(" + this._getRandomColorInt() + "," + this._getRandomColorInt() + "," + this._getRandomColorInt() + "," + opacity + ")";
        }
    }
});