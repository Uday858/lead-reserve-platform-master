import {Bar} from 'vue-chartjs';
import {ResourceHttp} from '../../services/Resource';
export default Bar.extend({
    name: "AcceptedRejectedLead",
    mounted() {
        var self = this;
        ResourceHttp.get("resources/front-reports/accept-reject-leads").then((d) => {
            // Data from the campaign type split.
            let data = d.data;
            var datasets = {
                leadgen: [],
                linkout: []
            };

            datasets.linkout = data["Linkout Ratio"];
            datasets.leadgen = data["Leadgen Ratio"];

            this.renderChart({
                labels: ["Accepted","Rejected","Conversions","Impressions"],
                datasets: [
                    {
                        label: "Leadgen Accept",
                        data: [datasets.leadgen[0]],
                        backgroundColor: self._getRandomRGBAColor("1")
                    },
                    {
                        label: "Leadgen Reject",
                        data: [datasets.leadgen[1]],
                        backgroundColor: self._getRandomRGBAColor("1")
                    },
                    {
                        label: "Linkout Conversions",
                        data: [datasets.linkout[0]],
                        backgroundColor: self._getRandomRGBAColor("1")
                    },
                    {
                        label: "Linkout Impressions",
                        data: [datasets.linkout[1]],
                        backgroundColor: self._getRandomRGBAColor("1")
                    }
                ]
            }, {
                responsive: true, maintainAspectRatio: false
            });
        });
    },
    methods: {
        _getRandomColorInt() {
            return Math.floor(Math.random() * (255 - 100) + 100);
        },
        _getRandomRGBAColor(opacity) {
            return "rgba(" + this._getRandomColorInt() + "," + this._getRandomColorInt() + "," + this._getRandomColorInt() + "," + opacity + ")";
        }
    }
});