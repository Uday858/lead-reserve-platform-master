@extends('layouts.app')
@section('title','Create New Campaign')
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">Create a Campaign</div>

                    <div class="panel-body">
                        <div class="row">
                            <div class="col-sm-12">
                                <form method="POST" action="{{route("campaigns.store")}}">
                                    {{csrf_field()}}
                                    @if(isset($_GET["advertiser_id"]))
                                        <input type="hidden" value="{{$_GET["advertiser_id"]}}" name="advertiser_id"/>
                                    @endif
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <select class="form-control" name="advertiser_id"
                                                        id="create-campaign-advertiser-select">
                                                    <option>--Choose Advertiser--</option>
                                                    @foreach($advertisers as $advertiser)
                                                        <option value="{{$advertiser->id}}">
                                                            ({{$advertiser->id}})&nbsp;{{$advertiser->name}}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @if(isset($_GET["advertiser_id"]))
                                                    <div id="create-campaign-advertiser-value"
                                                         data-advertiser-id="{{$_GET["advertiser_id"]}}"></div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <input class="form-control" name="name" placeholder="Campaign Name"/>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <select class="form-control" name="campaign_type_id">
                                                    <option>--Choose Campaign Type--</option>
                                                    <option value="1">CPL</option>
                                                    <option value="2">CPA</option>
                                                    <option value="3">Leadgen</option>
                                                    <option value="4">Linkout</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <input class="form-control" name="posting_url"
                                                       placeholder="Posting URL"/>
                                            </div>
                                        </div>
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <button type="submit" class="btn btn-success btn-block">Create Campaign
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection