@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-3">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <h5>Publisher</h5>
                        <h2>{{$publisher->name}}</h2>
                        <h4>{{$publisher->email}}</h4>
                    </div>
                    <div class="panel-body no-padding">

                        <div class="InfoBlock">
                            <div class="InfoBlock__Heading">
                                <div class="InfoBlock__Heading__Name">Main Contact</div>
                            </div>
                            <div class="InfoBlock__Content">
                                <div class="Content__Title">Name</div>
                                <div class="Content__Attribute">{{$publisher->hasAttributeOrEmpty("publisher_poc_main_name")}}</div>
                            </div>
                            <div class="InfoBlock__Content">
                                <div class="Content__Title">Email</div>
                                <div class="Content__Attribute">{{$publisher->hasAttributeOrEmpty("publisher_poc_main_email")}}</div>
                            </div>
                            <div class="InfoBlock__Content">
                                <div class="Content__Title">Phone</div>
                                <div class="Content__Attribute">{{$publisher->hasAttributeOrEmpty("publisher_poc_main_phone")}}</div>
                            </div>
                        </div>

                        <div class="InfoBlock">
                            <div class="InfoBlock__Heading">
                                <div class="InfoBlock__Heading__Name">Finance Contact</div>
                            </div>
                            <div class="InfoBlock__Content">
                                <div class="Content__Title">Name</div>
                                <div class="Content__Attribute">{{$publisher->hasAttributeOrEmpty("publisher_poc_finance_name")}}</div>
                            </div>
                            <div class="InfoBlock__Content">
                                <div class="Content__Title">Email</div>
                                <div class="Content__Attribute">{{$publisher->hasAttributeOrEmpty("publisher_poc_finance_email")}}</div>
                            </div>
                            <div class="InfoBlock__Content">
                                <div class="Content__Title">Phone</div>
                                <div class="Content__Attribute">{{$publisher->hasAttributeOrEmpty("publisher_poc_finance_phone")}}</div>
                            </div>
                        </div>

                        <div class="InfoBlock">
                            <div class="InfoBlock__Heading">
                                <div class="InfoBlock__Heading__Name">Technical Contact</div>
                            </div>
                            <div class="InfoBlock__Content">
                                <div class="Content__Title">Name</div>
                                <div class="Content__Attribute">{{$publisher->hasAttributeOrEmpty("publisher_poc_tech_name")}}</div>
                            </div>
                            <div class="InfoBlock__Content">
                                <div class="Content__Title">Email</div>
                                <div class="Content__Attribute">{{$publisher->hasAttributeOrEmpty("publisher_poc_tech_email")}}</div>
                            </div>
                            <div class="InfoBlock__Content">
                                <div class="Content__Title">Phone</div>
                                <div class="Content__Attribute">{{$publisher->hasAttributeOrEmpty("publisher_poc_tech_phone")}}</div>
                            </div>
                        </div>

                        <div class="InfoBlock">
                            <div class="InfoBlock__Heading">
                                <div class="InfoBlock__Heading__Name">Publisher Notes</div>
                            </div>
                            <div class="InfoBlock__Content">
                                <div class="Content__Attribute">{{$publisher->hasAttributeOrEmpty("publisher_notes")}}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-8">
                <div class="row">
                    <div class="col-md-12">
                        <div class="panel panel-default">
                            <div class="panel-body no-padding">
                                <div class="InfoPanelHorizontalContainer">
                                    <div class="InfoPanel small clean">
                                        <div class="InfoPanel__Value">${{number_format($publisherMetrics->sum('revenue'),2)}}</div>
                                        <div class="InfoPanel__Caption">Revenue</div>
                                    </div>
                                    <div class="InfoPanel small clean">
                                        <div class="InfoPanel__Value">${{number_format($publisherMetrics->sum('revenue')-$publisherMetrics->sum('payout'),2)}}</div>
                                        <div class="InfoPanel__Caption">Profit</div>
                                    </div>
                                    <div class="InfoPanel small clean">
                                        <div class="InfoPanel__Value">$({{number_format($publisherMetrics->sum('payout'),2)}})</div>
                                        <div class="InfoPanel__Caption">Payout</div>
                                    </div>    
                                </div>
                                
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="panel panel-default">
                            <div class="panel-body no-padding">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="ActionBlock">
                                            <a class="ActionBlock__Action Action__Warning"
                                               href="{{route("publishers.edit",["id"=>$publisher->id])}}">
                                                Edit Publisher
                                            </a>
                                            <a class="ActionBlock__Action Action__Success"
                                               href="{{route("publishers.assign",["id"=>$publisher->id])}}">
                                                Assign Campaign
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12">
                                        @if(count($publisher->publisherCampaigns)==0)
                                            <p>{{$publisher->name}} has no campaigns assigned to them.</p>
                                        @else

                                            <table class="table table-hover">
                                                <tr>
                                                    <td>Campaign Name</td>
                                                    <td>&nbsp;</td>
                                                    <td>&nbsp;</td>
                                                </tr>
                                                @foreach($publisher->publisherCampaigns as $publisherCampaign)
                                                    <tr>
                                                        <td>
                                                            <a href="{{route("publishers.campaign",["publisherId"=>$publisher->id,"campaignId"=>$publisherCampaign->campaign->id])}}">{{$publisherCampaign->campaign->name}}</a>
                                                        </td>
                                                        <td>
                                                            <a href="{{route("publishers.unassign",["publisherCampaignId"=>$publisherCampaign->id])}}">Unassign</a>
                                                        </td>
                                                        <td>
                                                            <a href="{{route("campaigns.show",["campaignId"=>$publisherCampaign->campaign->id])}}">View Campaign</a>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </table>


                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection