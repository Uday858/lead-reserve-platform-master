@extends("layouts.resource")
@section("title",$rac["resource_name"])
@section("content")
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
@endsection