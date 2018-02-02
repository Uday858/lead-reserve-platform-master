@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        Posting Instructions
                    </div>
                    <div class="panel-body">
                        <p>
                            <a href="{{$publisher_url}}" target="_blank">Link to posting instructions.</a>
                        </p>
                    </div>
                </div>
                <div class="panel panel-default">
                    <div class="panel-heading">
                        Leads Captured
                    </div>
                    <div class="panel-body no-padding" style="overflow: scroll;">
                        @if(isset($_GET["showleads"]))
                        <table class="table table-striped">
                            <tr>
                                @if(isset($leads_captured[0]))
                                    @foreach(array_keys($leads_captured[0]) as $key)
                                        <th>{{$key}}</th>
                                    @endforeach
                                    <th>Status</th>
                                @endif
                            </tr>
                            @foreach($leads_captured as $lead)
                                <tr>
                                    @foreach(array_values($lead) as $value)
                                        <td>{{$value}}</td>
                                    @endforeach
                                    <td>
                                        <span class="status-circle {{((new \App\Providers\LeadMoldingProvider())->getStatusForLead($lead["id"]))}}"></span>
                                    </td>
                                </tr>
                            @endforeach
                        </table>
                            @else
                            <a href="?showleads=1">Show Leads</a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection