@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">Publisher Overview</div>
                    <div class="panel-body no-padding">
                        <table class="table table-hover">
                            <tr>
                                <th>Publisher Name</th>
                                <th>Publisher Email</th>
                            </tr>
                            @foreach($publishers as $publisher)
                                <tr>
                                    <td>
                                        <a href="{{route("publishers.show",["id" => $publisher->id])}}">
                                            ({{$publisher->id}})&nbsp;{{$publisher->name}}
                                        </a>
                                    </td>
                                    <td>
                                        {{$publisher->email}}
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