<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default" style="box-shadow:unset;margin:0;">
            <div class="panel-body no-padding">

                <table class="table table-hover">
                    <tr class="heading">
                        <th>Campaign<br/>
                            <small>Click for more information.</small>
                        </th>
                        <th>Advertiser<br/>
                            <small>Click for more information.</small>
                        </th>
                        <th>Publishers<br/>
                            <small>View all pubs.</small>
                        </th>
                        <th>
                            Leads<br/>
                            <small>Captures/Accepts/Rejects</small>
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
                    @foreach($campaigns as $campaign)
                        <tr>
                            <td>
                                <a href="{{route("campaigns.show",["id"=>$campaign["campaign_id"]])}}">
                                    <span class="status-circle accepted"></span>
                                    ({{$campaign["campaign_id"]}})&nbsp;{{$campaign["campaign_name"]}}
                                </a>
                            </td>
                            <td>
                                <a href="{{route("advertisers.show",["id"=>$campaign["advertiser_id"]])}}">
                                    ({{$campaign["advertiser_id"]}})&nbsp;{{$campaign["advertiser_name"]}}
                                </a>
                            </td>
                            <td>

                            </td>
                            <td>
                                <a target="_blank" href="{{route('lead.explorer.index',[
                                    "campaign_id" => $campaign["campaign_id"],
                                    "from_date" => (isset($fromDate) ? $fromDate : \Carbon\Carbon::now()->startOfDay()),
                                    "to_date" => (isset($toDate) ? $toDate : \Carbon\Carbon::now())
                                ])}}">{{$campaign["leads_generated"]}}
                                    / {{$campaign["leads_accepted"]}}
                                    / {{$campaign["leads_rejected"]}}</a>
                            </td>
                            <td>
                                ${{number_format($campaign["revenue"],2)}} /
                                ${{number_format($campaign["net"],2)}} /
                                $({{number_format($campaign["payout"],2)}})
                            </td>
                            <td>
                                {{$campaign["daily_cap"]}}
                            </td>
                        </tr>
                        @if(count($campaign["publishers"])!=0)
                            @foreach($campaign["publishers"] as $publisher)
                                <tr class="{{$publisher["current_status"]}}">
                                    <td></td>
                                    <td></td>
                                    <td>
                                        <a href="{{route("publishers.campaign",["campaignId"=>$campaign["campaign_id"],"publisherId"=>$publisher["publisher_id"]])}}">
                                            ({{$publisher["publisher_id"]}})&nbsp;{{$publisher["publisher_name"]}}
                                        </a>
                                    </td>
                                    <td><a target="_blank" href="{{route('lead.explorer.index',[
                                    "campaign_id" => $campaign["campaign_id"],
                                    "publisher_id" => $publisher["publisher_id"],
                                    "from_date" => (isset($fromDate) ? $fromDate : \Carbon\Carbon::now()->startOfDay()),
                                    "to_date" => (isset($toDate) ? $toDate : \Carbon\Carbon::now())
                                ])}}">{{$publisher["leads_generated"]}} / {{$publisher["leads_accepted"]}}
                                            / {{$publisher["leads_rejected"]}}</a></td>
                                    <td>${{number_format($publisher["revenue"],2)}} /
                                        ${{number_format($publisher["net"],2)}} /
                                        $({{number_format($publisher["payout"],2)}})
                                    </td>
                                    <td>
                                        {{$publisher["daily_cap"]}}
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                    @endforeach
                </table>
            </div>
        </div>
    </div>
</div>