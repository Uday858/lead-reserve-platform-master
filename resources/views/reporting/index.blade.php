@extends('layouts.app')
@section('title','Reporting')
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <h5>What kind of report would you like?</h5>
                        <p>Choose via the drop down below!</p>
                        <form method="GET" action="{{route("reporting.index")}}">
                            <div class="form-group">
                                <select class="form-control" name="selection">
                                    <option>--Select One--</option>
                                    <option value="today" {{(isset($_GET["selection"]))?($_GET["selection"]=="today")?"selected":"":""}}>
                                        Today
                                    </option>
                                    <option value="yesterday" {{(isset($_GET["selection"]))?($_GET["selection"]=="yesterday")?"selected":"":""}}>
                                        Yesterday
                                    </option>
                                    <option value="wtd" {{(isset($_GET["selection"]))?($_GET["selection"]=="wtd")?"selected":"":""}}>
                                        Week (to date)
                                    </option>
                                    <option value="mtd" {{(isset($_GET["selection"]))?($_GET["selection"]=="mtd")?"selected":"":""}}>
                                        Month (to date)
                                    </option>
                                    <option value="7day" {{(isset($_GET["selection"]))?($_GET["selection"]=="7day")?"selected":"":""}}>
                                        7-Day
                                    </option>
                                    <option value="30day" {{(isset($_GET["selection"]))?($_GET["selection"]=="30day")?"selected":"":""}}>
                                        30-Day
                                    </option>
                                    <option value="90day" {{(isset($_GET["selection"]))?($_GET["selection"]=="90day")?"selected":"":""}}>
                                        90-Day
                                    </option>
                                    <option value="month" {{(isset($_GET["selection"]))?($_GET["selection"]=="month")?"selected":"":""}}>
                                        Month
                                    </option>
                                    <option value="year" {{(isset($_GET["selection"]))?($_GET["selection"]=="year")?"selected":"":""}}>
                                        Year
                                    </option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>From Date</label>
                                <input type="date" name="from_date" class="form-control" placeholder="From Date"
                                       value="{{isset($_GET["from_date"])?$_GET["from_date"]:""}}"/>
                                <label>To Date</label>
                                <input type="date" name="to_date" class="form-control" placeholder="From Date"
                                       value="{{isset($_GET["to_date"])?$_GET["to_date"]:""}}"/>
                            </div>
                            <div class="form-group">
                                <button type="submit" class="btn btn-success">Generate Report</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @if(isset($_GET["selection"]) || isset($_GET["from_date"]))
            <div class="row">
                <div class="col-sm-12">
                    <div class="ActionBlock white spacing">
                        <a class="ActionBlock__Action">
                            {{$info["dateRange"]}}
                        </a>
                        <a class="ActionBlock__Action">
                            {{$info["title"]}}
                        </a>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <div class="InfoPanelHorizontalContainer">
                                <div class="InfoPanel small clean">
                                    <div class="InfoPanel__Value">${{number_format($revenue["revenue"],2)}}</div>
                                    <div class="InfoPanel__Caption">Platform Revenue</div>
                                </div>
                                <div class="InfoPanel small clean">
                                    <div class="InfoPanel__Value">$({{number_format($revenue["payout"],2)}})</div>
                                    <div class="InfoPanel__Caption">Publisher Payout</div>
                                </div> 
                                <div class="InfoPanel small clean">
                                    <div class="InfoPanel__Value">${{number_format($revenue["net"],2)}}</div>
                                    <div class="InfoPanel__Caption">Split/Margin</div>
                                </div>
                                @php($infoPanels = [
                                    [$leads["generated"],"Leads Generated"],
                                    [$leads["accepted"],"Leads Accepted"],
                                    [$leads["rejected"],"Leads Rejected"],
                                    [$leads["metric_impressions"],"Impressions"],
                                    [$leads["metric_clicks"],"Clicks"],
                                    [$leads["metric_conversions"],"Conversions"],
                                ])
                                @foreach($infoPanels as $panel)
                                    <!--<div class="col-md-2">
                                        <div class="panel panel-default">
                                            <div class="panel-body no-padding">-->
                                                <div class="InfoPanel small clean">
                                                    <div class="InfoPanel__Value">{{$panel[0]}}</div>
                                                    <div class="InfoPanel__Caption">{{$panel[1]}}</div>
                                                </div>
                                            <!--</div>
                                        </div>
                                    </div>-->
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-default">
                        <div class="panel-heading clean">
                            <h4>Lead Generation Campaign Performance</h4>
                            <h5>Live Campaigns Only</h5>
                        </div>
                        <div class="panel-body no-padding">
                            {{--<CampaignPerformanceTable fromDate="{{$fromDate->toDateTimeString()}}" toDate="{{$toDate->toDateTimeString()}}"/>--}}
                            {{--@include("dashboard.components.leadgenCampaignPerformance",["metrics" => $metrics])--}}
                            @include("dashboard.components.agedCampaignPerformance",["campaigns" => $campaignPerformance])
                        </div>
                    </div>
                </div>{{--
                <div class="col-md-12">
                    <div class="panel panel-default">
                        <div class="panel-heading clean">
                            <h4>Linkout Campaign Performance</h4>
                            <h5>Live Campaigns Only</h5>
                        </div>
                        <div class="panel-body no-padding">
                            @include("dashboard.components.linkoutCampaignPerformance",["metrics" => $metrics])
                        </div>
                    </div>
                </div>--}}
            </div>
        @endif
    </div>
@endsection