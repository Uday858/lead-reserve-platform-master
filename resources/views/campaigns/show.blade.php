@extends('layouts.app')
@section('title','' . $campaign->name)
@section('content')
    <div class="container-fluid">        
        <div class="NavigationBox">
            <div class="NavigationBox__Meta">
                <div class="Meta__Title">{{$campaign->name}}</div>
                <div class="Meta__Caption">Campaign Screen</div>
            </div>
            <div class="NavigationBox__Actions">
                <a class="NavigationBox__Action"
                   href="{{route("advertisers.show",["id"=>$campaign->advertiser->id])}}">
                    ({{$campaign->advertiser->id}}) {{$campaign->advertiser->name}}
                </a>
                <a class="NavigationBox__Action Action__Danger"
                   href="{{route("publishers.assign",["campaignId"=>$campaign->id])}}">
                    Assign Publisher
                </a>
                <a class="NavigationBox__Action Action__Warning"
                   href="" data-toggle="modal" data-target="#edit-campaign-modal">
                    Edit Campaign
                </a>
                <a class="NavigationBox__Action Action__Warning"
                   href="" data-toggle="modal" data-target="#duplicate-campaign-modal">
                    Duplicate Campaign
                </a>
                <a class="NavigationBox__Action Action__Danger"
                   href="" data-toggle="modal" data-target="#delete-campaign-modal">
                    Delete Campaign
                </a>
            </div>
        </div>
        @include("campaigns.components.infoPanels",["metrics" => $metrics])
        <div class="row">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-sm-12">
                        @if($campaign->type->name == "CPA" || $campaign->type->name == "Linkout")
                            @include("campaigns.components.campaignLinkoutSettings",["campaign" => $campaign])
                        @else
                            <CampaignPostingParameters :campaignId="{{$campaign->id}}"></CampaignPostingParameters>
                            @include("campaigns.components.campaignSettings",["campaign" => $campaign])
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                @if($campaign->type->name == "CPA" || $campaign->type->name == "Linkout")
                    <!-- Publisher Settings .. -->
                @else
                    @include("campaigns.components.leadgenPublisherSettings",["campaign" => $campaign])
                @endif
                
            </div>
            <div class="col-md-12">
                @include("campaigns.components.publisherMetrics",["campaignMetrics" => $metrics,"campaign" => $campaign])
            </div>
        </div>
    </div>
    @include("campaigns.modals.editCampaign",["campaign" => $campaign])
    @include("campaigns.modals.deleteCampaign",["campaign" => $campaign])
    @include("campaigns.modals.duplicateCampaign",["campaign" => $campaign])
@endsection