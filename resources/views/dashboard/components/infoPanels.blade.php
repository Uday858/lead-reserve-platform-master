<div class="row">
    @php($infoPanels = [
    ["$".number_format($revenue["revenue"],2),"Gross Profit"],
    ["$(".number_format($revenue["payout"],2).")","Publisher Payout"],
    ["$".number_format($revenue["net"],2),"Net Profit"],
    [$leads["generated"],"Generated Leads"],
    [$leads["accepted"],"Accepted Leads"],
    [$leads["rejected"],"Rejected Leads"],
    [$leads["metric_impressions"],"Offer Impressions"],
    [$leads["metric_clicks"],"Offer Clicks"],
    [$leads["metric_conversions"],"Offer Conversions"],
    ])
    <div class="col-sm-12">
        <div class="row">
            @foreach($infoPanels as $panel)
            <div class="col-md-2">
                <div class="panel panel-default">
                    <div class="panel-body no-padding">
                        <div class="InfoPanel small clean">
                            <div class="InfoPanel__Value">{{$panel[0]}}</div>
                            <div class="InfoPanel__Caption">{{$panel[1]}}</div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div> 
    </div>
</div>