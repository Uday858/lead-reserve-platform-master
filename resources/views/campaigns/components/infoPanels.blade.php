<div class="row" style="margin-top:100px">
    <div class="col-md-2">
        <div class="panel panel-default">
            <div class="panel-body no-padding">
                <div class="InfoPanel small clean">
                    <div class="InfoPanel__Value">${{number_format($metrics["net_generated"],2)}}</div>
                    <div class="InfoPanel__Caption">Net Profit</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="panel panel-default">
            <div class="panel-body no-padding">
                <div class="InfoPanel small clean">
                    <div class="InfoPanel__Value">${{number_format($metrics["payout_amounts"],2)}}</div>
                    <div class="InfoPanel__Caption">Total Payout</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="panel panel-default">
            <div class="panel-body no-padding">
                <div class="InfoPanel small clean">
                    <div class="InfoPanel__Value">${{number_format($metrics["revenue_generated"],2)}}</div>
                    <div class="InfoPanel__Caption">Total Revenue</div>
                </div>
            </div>
        </div>
    </div>
    @if($campaign->type->name == "CPA" || $campaign->type->name == "Linkout")
        <div class="col-md-2">
            <div class="panel panel-default">
                <div class="panel-body no-padding">
                    <div class="InfoPanel small clean">
                        <div class="InfoPanel__Value">{{isset($metrics["metric_impressions"])?$metrics["metric_impressions"]:-1}}</div>
                        <div class="InfoPanel__Caption">Impression(s)</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="panel panel-default">
                <div class="panel-body no-padding">
                    <div class="InfoPanel small clean">
                        <div class="InfoPanel__Value">{{isset($metrics["metric_clicks"])?$metrics["metric_clicks"]:-1}}</div>
                        <div class="InfoPanel__Caption">Click(s)</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="panel panel-default">
                <div class="panel-body no-padding">
                    <div class="InfoPanel small clean">
                        <div class="InfoPanel__Value">{{isset($metrics["metric_click_through_rate"])?$metrics["metric_click_through_rate"]:-1}}</div>
                        <div class="InfoPanel__Caption">CTR</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="panel panel-default">
                <div class="panel-body no-padding">
                    <div class="InfoPanel small clean">
                        <div class="InfoPanel__Value">{{isset($metrics["metric_conversions"])?$metrics["metric_conversions"]:-1}}</div>
                        <div class="InfoPanel__Caption">Conversion(s)</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="panel panel-default">
                <div class="panel-body no-padding">
                    <div class="InfoPanel small clean">
                        <div class="InfoPanel__Value">{{isset($metrics["metric_conversion_rate"])?$metrics["metric_conversion_rate"]:-1}}</div>
                        <div class="InfoPanel__Caption">CVR</div>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="col-md-2">
            <div class="panel panel-default">
                <div class="panel-body no-padding">
                    <div class="InfoPanel small clean">
                        <div class="InfoPanel__Value">{{isset($metrics["leads_captured"])?$metrics["leads_captured"]:-1}}</div>
                        <div class="InfoPanel__Caption">Leads</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="panel panel-default">
                <div class="panel-body no-padding">
                    <div class="InfoPanel small clean">
                        <div class="InfoPanel__Value">{{isset($metrics["leads_accepted"])?$metrics["leads_accepted"]:-1}}</div>
                        <div class="InfoPanel__Caption">Accepted</div>
                    </div>
                </div>
            </div>
        </div>
    @endif
    <div class="col-md-2">
        <div class="panel panel-default">
            <div class="panel-body no-padding">
                <div class="InfoPanel small clean">
                    <div class="InfoPanel__Value">{{isset($metrics["active_publishers"])?$metrics["active_publishers"]:-1}}</div>
                    <div class="InfoPanel__Caption">Publisher(s)</div>
                </div>
            </div>
        </div>
    </div>
</div>