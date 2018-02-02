@extends("layouts.resource")
@section("title",$rac["resource_name"])
@section("content")

    <div class="panel panel-default transparent">
        <div class="panel-body no-padding">
            <div class="PublisherCampaignLeadFlowDiagram">
                <div class="Diagram__Publisher">
                    {{$publisher->name}}
                    <small>Publisher</small>
                </div>
                <div class="Diagram__Action">
                    <i class="fa fa-long-arrow-right"></i>
                </div>
                <div class="Diagram__Campaign">
                    {{$campaign->name}}
                    <small>Campaign</small>
                </div>
            </div>
        </div>
    </div>
    @if($campaign->type->name == "Leadgen")
        @include("resources.components.leadgenPosting")
    @elseif($campaign->type->name == "CPA" || $campaign->type->name == "CPL" || $campaign->type->name == "Linkout")
        @include("resources.components.linkoutPosting")
    @endif
    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-default no-box-shadow">
                <div class="panel-body no-padding">
                    <div class="InfoBlock">
                        <div class="InfoBlock__Heading">
                            <div class="InfoBlock__Heading__Name">Offer Creative and Copy</div>
                        </div>
                    </div>
                    <div style="padding:20px;">
                        <!-- Lead Reserve Offer Wall -->
                        <iframe style="border:unset!important;" src="{{route('resources.example.offer',["campaignId" => $campaign->id])}}" width="100%" height="400px"></iframe>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection