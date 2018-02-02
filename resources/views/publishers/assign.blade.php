@extends('layouts.app')
@section('title','Assign Publisher')
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">Assign Campaign to Publisher</div>

                    <div class="panel-body">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="row">
                                @foreach($campaigns as $campaign)
                                    <div class="col-sm-6">
                                        <div class="panel panel-warning">
                                            <div class="panel-heading">{{$campaign->name}}</div>
                                            <div class="panel-body">
                                                <form method="POST" action="{{route("publishers.assign.store")}}">
                                                    {{csrf_field()}}
                                                    <input type="hidden" name="publisher_id" value="{{$publisher->id}}"/>
                                                    <input type="hidden" name="campaign_id" value="{{$campaign->id}}"/>
                                                    <div class="form-group">
                                                        <div class="input-group">
                                                            <span class="input-group-addon">$</span>
                                                            <input type="number" class="form-control" min="0" step="any" name="payout" placeholder="Payout (0.00)"/>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <input type="number" class="form-control" min="0" step="1" name="lead_cap" placeholder="Lead Cap"/>
                                                    </div>
                                                    <div class="form-group">
                                                        <button type="submit" class="btn btn-warning">Assign Campaign To Publisher</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection