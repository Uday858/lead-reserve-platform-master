@extends('layouts.app')
@section('title','' . $advertiser->name)
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-3">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <h5>Advertiser</h5>
                        <h2>{{$advertiser->name}}</h2>
                        <h4>{{$advertiser->email}}</h4>
                    </div>
                    <div class="panel-body">
                        <div class="InfoBlock">
                            <div class="InfoBlock__Heading">
                                <div class="InfoBlock__Heading__Name">Main Contact</div>
                            </div>
                            <div class="InfoBlock__Content">
                                <div class="Content__Title">Name</div>
                                <div class="Content__Attribute">{{$advertiser->hasAttributeOrEmpty("poc_main_name")}}</div>
                            </div>
                            <div class="InfoBlock__Content">
                                <div class="Content__Title">Email</div>
                                <div class="Content__Attribute">{{$advertiser->hasAttributeOrEmpty("poc_main_email")}}</div>
                            </div>
                            <div class="InfoBlock__Content">
                                <div class="Content__Title">Phone</div>
                                <div class="Content__Attribute">{{$advertiser->hasAttributeOrEmpty("poc_main_phone")}}</div>
                            </div>
                        </div>
                        <div class="InfoBlock">
                            <div class="InfoBlock__Heading">
                                <div class="InfoBlock__Heading__Name">Finance Contact</div>
                            </div>
                            <div class="InfoBlock__Content">
                                <div class="Content__Title">Name</div>
                                <div class="Content__Attribute">{{$advertiser->hasAttributeOrEmpty("poc_finance_name")}}</div>
                            </div>
                            <div class="InfoBlock__Content">
                                <div class="Content__Title">Email</div>
                                <div class="Content__Attribute">{{$advertiser->hasAttributeOrEmpty("poc_finance_email")}}</div>
                            </div>
                            <div class="InfoBlock__Content">
                                <div class="Content__Title">Phone</div>
                                <div class="Content__Attribute">{{$advertiser->hasAttributeOrEmpty("poc_finance_phone")}}</div>
                            </div>
                        </div>
                        <div class="InfoBlock">
                            <div class="InfoBlock__Heading">
                                <div class="InfoBlock__Heading__Name">Tech Contact</div>
                            </div>
                            <div class="InfoBlock__Content">
                                <div class="Content__Title">Name</div>
                                <div class="Content__Attribute">{{$advertiser->hasAttributeOrEmpty("poc_tech_name")}}</div>
                            </div>
                            <div class="InfoBlock__Content">
                                <div class="Content__Title">Email</div>
                                <div class="Content__Attribute">{{$advertiser->hasAttributeOrEmpty("poc_tech_email")}}</div>
                            </div>
                            <div class="InfoBlock__Content">
                                <div class="Content__Title">Phone</div>
                                <div class="Content__Attribute">{{$advertiser->hasAttributeOrEmpty("poc_tech_phone")}}</div>
                            </div>
                        </div>
                        <div class="InfoBlock">
                            <div class="InfoBlock__Heading">
                                <div class="InfoBlock__Heading__Name">Advertiser Notes</div>
                            </div>
                            <div class="InfoBlock__Content">
                                <div class="Content__Attribute">{{$advertiser->hasAttributeOrEmpty("advertiser_notes")}}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-9">
                <div class="panel panel-default">
                    <div class="panel-body no-padding">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="InfoPanelHorizontalContainer">
                                    <div class="InfoPanel small clean">
                                        <div class="InfoPanel__Value">${{number_format($financials["revenue"],2)}}</div>
                                        <div class="InfoPanel__Caption">Advertiser Revenue</div>
                                    </div>    
                                    <div class="InfoPanel small clean">
                                        <div class="InfoPanel__Value">${{number_format($financials["profit"],2)}}</div>
                                        <div class="InfoPanel__Caption">Platform Profit</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="panel panel-default">
                    <div class="panel-body no-padding">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="ActionBlock">
                                    <a class="ActionBlock__Action Action__Warning"
                                       href="{{route("advertisers.edit",["id"=>$advertiser->id])}}">
                                        Edit Advertiser
                                    </a>
                                    <a class="ActionBlock__Action Action__Success"
                                       href="{{route("campaigns.create",["advertiser_id"=>$advertiser->id])}}">
                                        New Campaign
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                @if(count($advertiser->campaigns) == 0)
                                    <p>
                                        No campaigns exist for <strong>{{$advertiser->name}}</strong>
                                    </p>
                                @else
                                    <table class="table table-hover">
                                        <tr>
                                            <th>Campaign Name</th>
                                            <th>CPL</th>
                                            <th>Daily Cap</th>
                                            <th>Daily %</th>
                                            <th>Overall Cap</th>
                                            <th>Overall %</th>
                                            <th>Revenue</th>
                                            <th>Profit</th>
                                        </tr>
                                        @foreach($advertiser->campaigns as $campaign)
                                        <tr>
                                            <td>
                                                <a href="{{route("campaigns.show",["id"=>$campaign->id])}}">
                                                    ({{$campaign->id}})&nbsp;{{$campaign->name}}
                                                </a>
                                            </td>
                                            <td>
                                                {{$campaign->hasAttributeOrEmpty("cpl")}}
                                            </td>
                                            <td>
                                                {{$campaign->hasAttributeOrEmpty("daily_cap")}}
                                            </td>
                                            <td>
                                                0.00%
                                            </td>
                                            <td>
                                                {{$campaign->hasAttributeOrEmpty("overall_cap")}}
                                            </td>
                                            <td>
                                                0.00%
                                            </td>
                                            <td>
                                                ${{number_format($campaign->reports->sum('revenue'),2)}}
                                            </td>
                                            <td>${{number_format($campaign->reports->sum('revenue')-$campaign->reports->sum('payout'),2)}}</td>
                                        </tr>
                                        @endforeach
                                    </table>
                                @endif
                            </div>
                            <div class="col-sm-4">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection