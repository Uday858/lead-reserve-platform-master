import {Doughnut} from 'vue-chartjs';
import {ResourceHttp} from '../../services/Resource';
export default Doughnut.extend({
    name: "VerticalCampaignSplit",
    mounted() {
        var self = this;
        this.renderChart({
            labels: [
                "Travel", "Jobs", "Finance"
            ],
            datasets: [
                {data:[24,31,45],backgroundColor:[
                    this._getRandomRGBAColor("0.5"),this._getRandomRGBAColor("0.5"),this._getRandomRGBAColor("0.5")
                ]}
            ]
        }, {responsive: true, maintainAspectRatio: false});
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