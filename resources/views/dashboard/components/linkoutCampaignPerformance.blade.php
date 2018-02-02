<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default" style="box-shadow:unset;margin:0;">
            <div class="panel-body no-padding">

                <table class="table table-hover">
                    <tr class="heading">
                        <th>
                            Campaign<br/>
                            <small>Click for more information.</small>
                        </th>
                        <th>
                            Advertiser<br/>
                            <small>Click for more information.</small>
                        </th>
                        <th>
                            Publishers<br/>
                            <small>View all pubs.</small>
                        </th>
                        <th>
                            Actions<br/>
                            <small>Impressions/Clicks/Conversions</small>
                        </th>
                        <th>
                            Metrics<br/>
                            <small>Gains/Margins/Payouts</small>
                        </th>
                        <th>
                            Details<br/>
                            <small>Daily Cap</small>
                        </th>
                    </tr>
                    @foreach($metrics["linkoutCampaignData"] as $campaign)
                        <tr>
                            <td>
                                <a href="{{route("campaigns.show",["id"=>$campaign["campaign"]["campaign_id"]])}}">
                                    <span class="status-circle accepted"></span>&nbsp;{{$campaign["campaign"]["campaign_name"]}}
                                </a>
                            </td>
                            <td>
                                <a href="{{route("advertisers.show",["id"=>$campaign["campaign"]["advertiser_id"]])}}">
                                    ({{$campaign["campaign"]["advertiser_id"]}})&nbsp;{{$campaign["campaign"]["advertiser_name"]}}
                                </a>
                            </td>
                            <td>
                                {{$campaign["campaign"]["active_publishers"]}}
                            </td>
                            <td>
                                {{$campaign["campaign"]["metric_impressions"]}} / {{$campaign["campaign"]["metric_clicks"]}} / {{$campaign["campaign"]["metric_conversions"]}}
                            </td>
                            <td>
                                ${{number_format($campaign["campaign"]["revenue_generated"],2)}} / ${{number_format($campaign["campaign"]["net_generated"],2)}} / $({{number_format($campaign["campaign"]["payout_amounts"],2)}})
                            </td>
                            <td>
                                {{$campaign["campaign"]["lead_cap"]}}
                            </td>
                        </tr>
                        @if(count($campaign["publishers"])!=0)
                            @foreach($campaign["publishers"] as $publisher)
                                <tr class="{{$publisher["current_status"]}}">
                                    <td></td>
                                    <td></td>
                                    <td>
                                        <a href="{{route("publishers.campaign",["campaignId"=>$campaign["campaign"]["campaign_id"],"publisherId"=>$publisher["publisher_id"]])}}">
                                            {{$publisher["publisher_name"]}}
                                        </a>
                                    </td>
                                    <td>{{$publisher["impressions"]}} / {{$publisher["clicks"]}} / {{$publisher["leads_accepted"]}}</td>
                                    <td>${{number_format($publisher["revenue"],2)}} / ${{number_format($publisher["revenue"]-$publisher["payout"],2)}} / $({{number_format($publisher["payout"],2)}})</td>
                                    <td>{{$publisher["lead_cap"]}}</td>
                                </tr>
                            @endforeach
                        @endif
                    @endforeach
                </table>
            </div>
        </div>
    </div>
</div>