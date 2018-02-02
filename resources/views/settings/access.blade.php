@extends('layouts.app')
@section('title','Client Access Settings')
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        Access Settings
                    </div>
                    <div class="panel-body">
                        <h5>Current Security Keys</h5>
                        <table>
                            <tr>
                                <th>Label</th>
                                <th>Hash</th>
                                <th>Permissions</th>
                                <th></th>
                            </tr>
                            @foreach($securityKeys as $securityKey)
                            <tr>
                                <td>{{$securityKey->label}}</td>
                                <td>{{$securityKey->hash}}</td>
                                <td>
                                    <ul>
                                        @foreach($securityKey->permissions->pluck('action')->toArray() as $item)
                                            <li>
                                                <code>
                                                    {{$item}}
                                                </code>
                                            </li>
                                        @endforeach
                                    </ul>
                                </td>
                                <td>
                                    <form action="{{route('thirdpartyaccess.destroy',['id'=>$securityKey->id])}}" method="POST">
                                        <input type="hidden" name="_method" value="DELETE">
                                        <button type="submit" class="btn btn-small btn-danger">
                                            Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </table>
                        <h5>Build New Security Key</h5>
                        <form method="POST" action="{{route('thirdpartyaccess.store')}}">
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <input type="text" class="form-control" placeholder="Label" name="label"/>
                                    </div>
                                    <div class="form-group">
                                        <input type="text" class="form-control" placeholder="Secret" name="secret"/>
                                        <p>
                                            <small>
                                                This gets hashed.
                                            </small>
                                        </p>
                                    </div>
                                    <div class="form-group">
                                        <select multiple name="permissions[]">
                                            @foreach($availablePermissions as $permission)
                                                <option value="{{$permission}}">{{$permission}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <button type="submit" class="btn btn-success">
                                            Create New Security Key
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
@endsection