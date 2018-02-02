@if($campaign->type->name == "CPA" || $campaign->type->name == "Linkout")
    <div class="panel panel-default">
        <div class="panel-heading blue">Publishers</div>
        <div class="panel-body">
            <table class="table table-hover">
                <tr>
                    <th>Publisher Name</th>
                    <th>Impressions</th>
                    <th>Clicks</th>
                    <th>Conversions</th>
                    <th>Generated</th>
                    <th>Payout</th>
                    <th>Margin</th>
                    <th></th>
                </tr>
                @foreach($campaign->publishers as $publisher)
                <tr>
                    <td>
                        <a href="{{route("publishers.show",["id"=>$publisher->id])}}">({{$publisher->id}})&nbsp;{{$publisher->name}}</a>
                    </td>
                    <td>
                        {{\App\MetricImpression::whereCampaignId($campaign->id)->wherePublisherId($publisher->id)->count()}}
                    </td>
                    <td>
                        {{\App\MetricClick::whereCampaignId($campaign->id)->wherePublisherId($publisher->id)->count()}}
                    </td>
                    <td>
                        -1
                    </td>
                    <td>$0.00</td>
                    <td>$0.00</td>
                    <td>$0.00</td>
                    <td>
                        <a href="{{route("publishers.unassign",["publisherCampaignId"=>\App\PublisherCampaign::wherePublisherId($publisher->id)->whereCampaignId($campaign->id)->first()->id])}}">Unassign</a>
                    </td>
                </tr>
                @endforeach
                {{--
                @foreach($metrics['publishers'] as $metric)
                <tr>
                    <td>
                        <a href="{{route("publishers.show",["id"=>$metric["publisher_id"]])}}">({{$metric["publisher_id"]}}
                            )&nbsp;{{$metric["publisher_name"]}}</a>
                    </td>
                    <td>
                        {{$metric["metric_impressions"]}}
                    </td>
                    <td>
                        {{$metric["metric_clicks"]}}
                    </td>
                    <td>
                        {{$metric["metric_conversions"]}}
                    </td>
                    <td>
                        ${{number_format($metric["revenue"],2)}}
                    </td>
                    <td>
                        ${{number_format($metric["payout"],2)}}
                    </td>
                    <td>
                        ${{number_format($metric["revenue"] - $metric["payout"],2)}}
                    </td>
                    <td>
                        <a href="{{route("publishers.unassign",["publisherCampaignId"=>\App\PublisherCampaign::wherePublisherId($metric["publisher_id"])->whereCampaignId($campaign->id)->first()->id])}}">Unassign</a>
                    </td>
                </tr>
                @endforeach
                --}}
            </table>
        </div>
    </div>
@else
    <div class="panel panel-default">
    <div class="panel-heading blue">
        Past Publisher Metrics<br/>
        <small>For publishers that have already delivered on this campaign. (Metrics are from past day. Not real-time.)</small>
    </div>
    <div class="panel-body no-padding">
        <table class="table table-hover">
            <tr>
                <th>Publisher Name</th>
                <th>
                    Leads<br/>
                    <small>Captures/Accepts/Rejects</small>
                </th>
                <th>
                    Metrics<br/>
                    <small>Gains/Margins/Payouts</small>
                </th>
            </tr>
            @foreach($campaignMetrics["publishers"] as $metric)
                <tr class="">
                    <td>
                        <a href="{{route("publishers.show",["id"=>$metric["publisher_id"]])}}">({{$metric["publisher_id"]}}
                            )&nbsp;{{$metric["publisher_name"]}}</a>
                    </td>
                    <td>
                        {{$metric["leads_generated"]}} / {{$metric["leads_accepted"]}} / {{$metric["leads_rejected"]}}
                    </td>
                    <td>
                        ${{number_format($metric["revenue"],2)}} / ${{number_format($metric["revenue"] - $metric["payout"],2)}} / ${{number_format($metric["payout"],2)}}
                    </td>
                </tr>
            @endforeach
        </table>
    </div>
</div>
    @endif