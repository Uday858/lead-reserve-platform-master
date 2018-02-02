@extends('layouts.app')
@section('title','Dashboard')
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <WeeklyRevenue></WeeklyRevenue>
            </div>
        </div>
        <div class="row">
            <div class="col-md-3">
                <div class="panel panel-default">
                    <div class="panel-body no-padding">
                        <div class="InfoPanel small clean">
                            <div class="InfoPanel__Value">
                                @if($potentialRevenue)
                                    ${{number_format($potentialRevenue,2)}}
                                @else
                                    0
                                @endif    
                            </div>
                            <div class="InfoPanel__Caption">Potential Daily Revenue</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="panel panel-default">
                    <div class="panel-body no-padding">
                        <div class="InfoPanel small clean">
                            <div class="InfoPanel__Value">
                                @if($potentialRevenue)
                                    ${{number_format(($potentialRevenue*0.65) * 30,2)}}
                                @else
                                    0
                                @endif        
                            </div>
                            <div class="InfoPanel__Caption">Potential Monthly Revenue</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="panel panel-default">
                    <div class="panel-body no-padding">
                        <div class="InfoPanel small clean">
                            <div class="InfoPanel__Value">
                                @if($potentialRevenue)
                                    {{number_format(($yesterdayRevenue/$potentialRevenue)*100,2)}}%
                                @else
                                    0%
                                @endif    
                            </div>
                            <div class="InfoPanel__Caption">Daily Budget Fulfillment</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="panel panel-default">
                    <div class="panel-body no-padding">
                        <div class="InfoPanel small clean">
                            <div class="InfoPanel__Value">
                                @if($potentialRevenue)
                                    ${{number_format($potentialRevenue*0.3,2)}}
                                @else
                                    0
                                @endif 
                            </div>
                            <div class="InfoPanel__Caption">Estimated Daily Profit</div>
                        </div>
                    </div>
                </div>
            </div>
        </div> 
        @include("dashboard.components.infoPanels",[
            "revenue" => $revenue,
            "leads" => $leads
        ])
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading clean">
                        <h4>Lead Generation Campaign Performance</h4>
                        <h5>Live Campaigns Only,
                            For {{Carbon\Carbon::now()->toFormattedDateString()}}</h5>
                    </div>
                    <div class="panel-body no-padding">
                        @include("dashboard.components.campaignPerformance",["campaigns" => $campaignPerformance])
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
