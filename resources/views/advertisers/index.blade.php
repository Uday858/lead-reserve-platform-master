@extends('layouts.app')
@section('title','All Advertisers')
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">Advertiser Overview</div>
                    <div class="panel-body no-padding">
                        <table class="table table-hover">
                            <tr>
                                <th>Advertiser Name</th>
                                <th>Advertiser Email</th>
                            </tr>
                            @foreach($advertisers as $advertiser)
                            <tr>
                                <td>
                                    <a href="{{route("advertisers.show",["id" => $advertiser->id])}}">
                                        ({{$advertiser->id}})&nbsp;{{$advertiser->name}}
                                    </a>
                                </td>
                                <td>
                                    {{$advertiser->email}}
                                </td>
                            </tr>
                            @endforeach
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection