@extends("layouts.resource")
@section("title",$rac["resource_name"])
@section("content")
    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-default no-box-shadow">
                <div class="panel-body no-padding">
                    <div class="InfoBlock">
                        <div class="InfoBlock__Heading">
                            <div class="InfoBlock__Heading__Name">Active Campaign List</div>
                        </div>
                        <div class="InfoBlock__Content">
                            <div class="Content__Title">What are these offers below?</div>
                            <div class="Content__Attribute">You, as a publisher, can email your account manager to be placed to run one or many of the below opportunities.</div>
                        </div>
                        <div class="InfoBlock__Content">
                            <div class="Content__Title">How can I apply?</div>
                            <div class="Content__Attribute">We are still working on an automated solution, however, email your <em>Reserve Tech</em> contact to continue the application process.</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @foreach($campaigns as $campaign)
    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-default no-box-shadow">
                <div class="panel-body no-padding">
                    <div class="InfoBlock">
                        <div class="InfoBlock__Heading">
                            <div class="InfoBlock__Heading__Name">Campaign Attributes</div>
                        </div>
                        <div class="InfoBlock__Content">
                            <div class="Content__Attribute">
                                <h3>{{$campaign->name}}</h3>
                                <h5>{{$campaign->type->name}}</h5>
                                <hr/>
                                <h5>
                                    Payout &mdash; ${{number_format($campaign->hasAttributeOrEmpty('cpl')/2,2)}}
                                </h5>
                                <h5>
                                    Cap &mdash; 200-500/day to test. 1000/day afterwards.
                                </h5>                                
                            </div>
                        </div>
                    </div>
                    <div class="InfoBlock">
                        <div class="InfoBlock__Heading">
                            <div class="InfoBlock__Heading__Name">Offer Creative and Copy</div>
                        </div>
                    </div>
                    <div style="padding:20px;">
                        <!-- Lead Reserve Offer Wall -->
                        <iframe style="border:unset!important;" src="{{route('resources.example.offer',["campaignId" => $campaign->id])}}" width="100%" height="250px"></iframe>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endforeach
@endsection