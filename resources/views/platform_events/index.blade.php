@extends('layouts.app')
@section('title','Platform Event Explorer')
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">Platform Event Explorer</div>
                    <div class="panel-body">
                        <p>Use this page to debug the LeadReserve platform. <strong>Development experience only</strong>.
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        Events
                    </div>
                    <div class="panel-body no-padding" style="overflow: scroll">
                        <table class="table table-hover">
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Description</th>
                                <th></th>
                                <th></th>
                            </tr>
                            @foreach($platformEvents as $event)
                                <tr>
                                    <td>{{$event->id}}</td>
                                    <td>{{$event->name}}</td>
                                    <td>{{$event->description}}</td>
                                    <td>
                                        <a href="#" data-toggle="modal" data-target="#{{$event->id}}-event-code-modal">View
                                            Code</a>
                                        <div class="modal fade" tabindex="-1" role="dialog" id="{{$event->id}}-event-code-modal">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-body">
                                                        <h3>{{$event->description}}</h3>
                                                        <hr/>
                                                        <code>
                                                            @if(strlen($event["json_value"])>=2000)
                                                                <a href="{{route("platformevents.codeexplorer",["id" => $event->id])}}">Code Explorer</a>
                                                                @else
                                                                {{var_export(json_decode($event["json_value"],1),1)}}
                                                            @endif
                                                        </code>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <form method="POST"
                                              action="{{route("platformevents.destroy",["id"=>$event->id])}}">
                                            <input type="hidden" name="_method" value="DELETE">
                                            <button type="submit" class="btn btn-danger">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </table>
                    </div>
                </div>
                <div class="panel panel-default">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-sm-6">
                                @if(isset($_GET["page"]))
                                    @if($_GET["page"] != 1)
                                        <a href="{{route("platformevents.index",["page" => 1])}}">
                                            <<
                                        </a>
                                        <a href="{{route("platformevents.index",["page" => $_GET["page"]-1])}}">
                                            Previous Page
                                        </a>
                                    @endif
                                @endif
                            </div>
                            <div class="col-sm-6 text-right">
                                <a href="{{route("platformevents.index",["page" => (isset($_GET["page"])?$_GET["page"]:1)+1])}}">
                                    Next Page
                                </a>
                                <a href="{{route("platformevents.index",["page" => "-0"])}}">
                                    >>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection