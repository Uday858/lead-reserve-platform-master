@extends('layouts.app')
@section('title','Lead Explorer')
@section('content')
    <div class="container-fluid">
        @if(!isset($_GET["from_date"]))
            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-default">
                        <div class="panel-heading clean">
                            <h4>Lead Explorer</h4>
                            <h5>Select the leads you want to view based on the options, below.</h5>
                        </div>
                        <div class="panel-body">
                            <form method="GET" action="{{route("lead.explorer.index")}}">
                                <div class="row">
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            <label>Lead ID</label>
                                            <input type="number" step="1" min="0" name="lead_id" class="form-control"
                                                   placeholder="1"/>
                                        </div>
                                        <h5>OR</h5>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            <label>Campaign ID</label>
                                            <input type="number" step="1" min="0" name="campaign_id"
                                                   class="form-control" placeholder="1"/>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            <label>Publisher ID</label>
                                            <input type="number" step="1" min="0" name="publisher_id"
                                                   class="form-control" placeholder="1"/>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            <label>From Date</label>
                                            <input type="date" name="from_date" class="form-control"
                                                   placeholder="From Date"/>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            <label>To Date</label>
                                            <input type="date" name="to_date" class="form-control"
                                                   placeholder="To Date"/>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <button type="submit" class="btn btn-success">Submit</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-default">
                        <div class="panel-heading clean">
                            <h4>{{$total}} Lead(s)</h4>
                            <h5>Explore the lead(s) below, to understand the breadcrumbs of their journey.</h5>
                        </div>
                        <div class="panel-body no-padding">
                            <table class="table">
                                <tr class="heading">
                                    <th>
                                        Details<br/>
                                        <small>
                                            Lead ID/Campaign ID/Publisher ID
                                        </small>
                                    </th>
                                    <th>
                                        Lead Points<br/>
                                        <small>
                                            Click to expand lead view.
                                        </small>
                                    </th>
                                    <th>
                                        Lead Events<br/>
                                        <small>
                                            Click to event view.
                                        </small>
                                    </th>
                                    <th>
                                        Request<br/>
                                        <small>To Advertiser</small>
                                    </th>
                                    <th>
                                        Response<br/>
                                        <small>From Advertiser</small>
                                    </th>
                                    <th>Status</th>
                                </tr>
                                @foreach($leads as $lead)
                                    <tr>
                                        <td>
                                            {{$lead["fields"]["id"]}} / {{$lead["fields"]["campaign_id"]}}
                                            / {{$lead["fields"]["publisher_id"]}}
                                        </td>
                                        <td>
                                            <a href="#lead-{{$lead["fields"]["id"]}}-points-modal" data-toggle="modal">Show
                                                Points</a>
                                        </td>
                                        <td>
                                            <a href="#lead-{{$lead["fields"]["id"]}}-events-modal" data-toggle="modal">Show
                                                Events</a>
                                        </td>
                                        <td>
                                            <a href="#lead-{{$lead["fields"]["id"]}}-request-modal" data-toggle="modal">See
                                                Request</a>
                                        </td>
                                        <td>
                                            <a href="#lead-{{$lead["fields"]["id"]}}-response-modal"
                                               data-toggle="modal">See Response</a>
                                        </td>
                                        <td>
                                            {{$lead["status"]}}
                                        </td>
                                    </tr>
                                    @include("lead_explorer.modals.leadpoints",["lead" => $lead])
                                    @include("lead_explorer.modals.leadevents",["lead" => $lead])
                                    @include("lead_explorer.modals.requeststring",["lead" => $lead])
                                    @include("lead_explorer.modals.response",["lead" => $lead])
                                @endforeach
                            </table>
                        </div>
                    </div>
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-sm-12 links">
                                    <p>
                                        Page {{isset($_GET["page"]) ? $_GET["page"] : 0}}
                                    </p>
                                    @if(isset($_GET["page"]))
                                        @if($_GET["page"] != 0)
                                            <a href="{{route("lead.explorer.index",array_merge($_GET,["page" => 0]))}}">
                                                <<
                                            </a>
                                            <a href="{{route("lead.explorer.index",array_merge($_GET,["page" => $_GET["page"]-1]))}}">
                                                Previous Page
                                            </a>
                                        @endif
                                    @endif
                                    @for($i = 0; $i < $last_page; $i++)
                                        @if(isset($_GET["page"]))
                                            @if($_GET["page"] == $i)
                                                <a class="selected" href="{{route("lead.explorer.index",array_merge($_GET,["page" => $i]))}}">
                                                    {{$i}}
                                                </a>
                                                @else
                                                <a href="{{route("lead.explorer.index",array_merge($_GET,["page" => $i]))}}">
                                                    {{$i}}
                                                </a>
                                            @endif
                                        @else
                                            <a href="{{route("lead.explorer.index",array_merge($_GET,["page" => $i]))}}">
                                                {{$i}}
                                            </a>
                                        @endif

                                    @endfor
                                    <a href="{{route("lead.explorer.index",array_merge($_GET,["page" => (isset($_GET["page"])?$_GET["page"]:0)+1]))}}">
                                        Next Page
                                    </a>
                                    <a href="{{route("lead.explorer.index",array_merge($_GET,["page" => -1]))}}">
                                        >>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection