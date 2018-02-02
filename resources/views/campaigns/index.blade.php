@extends('layouts.app')
@section('title','All Campaigns')
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">View All Campaigns</div>
                    <div class="panel-body">
                        <p>View below for all campaigns.</p>
                    </div>
                </div>
            </div>
            <div class="col-sm-12">
                <div class="panel panel-default">
                    <div class="panel-body no-padding">
                        <table class="table table-hover">
                            <tr class="heading">
                                <th>Campaign
                                <small>Status, Name, and Type</small>
                                </th>
                                <th>Advertiser
                                <small></small>
                                </th>
                                <th>Payout
                                <small></small>
                                </th>
                                <th>Daily Cap
                                <small></small>
                                </th>
                                <th>Has Creative?
                                <small></small>
                                </th>
                            </tr>
                            @foreach($campaigns as $campaign)
                                <tr>
                                    <td>
                                        <a href="{{route("campaigns.show",["id"=>$campaign->id])}}" data-toggle="tooltip" data-placement="top"
                                                       title="Go to {{$campaign->name}} campaign.">
                                            @if($campaign->hasAttributeOrEmpty('campaign_status') == 'live')
                                            <span class="status-circle accepted"></span>
                                            @else
                                            <span class="status-circle rejected"></span>
                                            @endif
                                            {{$campaign->name}}</a> &mdash; {{$campaign->type->name}}
                                    </td>
                                    <td>
                                        <a href="{{route("advertisers.show",["id" => $campaign->advertiser->id])}}">
                                                            ({{$campaign->advertiser->id}})&nbsp;{{$campaign->advertiser->name}}</a>
                                    </td>
                                    <td>
                                        ${{number_format(floatval($campaign->hasAttributeOrEmpty("cpl")),2)}}
                                    </td>
                                    <td>
                                        {{intval($campaign->hasAttributeOrEmpty("daily_cap"))}}
                                    </td>
                                    <td>
                                        @if($campaign->hasAttributeOrEmpty("creative_text")!="")
                                            <span class="status-circle accepted"></span>
                                        @else
                                            <span class="status-circle rejected"></span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </table>            
                    </div>
                </div>
            </div>
            
        </div>
    </div>
@endsection