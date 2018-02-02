@extends("layouts.resource")
@section("title","Page Not Found")
@section("content")
    <div class="panel panel-default transparent">
        <div class="panel-body no-padding">
            <div class="InfoBlock">
                <div class="InfoBlock__Heading">
                    <div class="InfoBlock__Heading__Name">Page Not Found</div>
                </div>
                <div class="InfoBlock__Content">
                    <div class="Content__Title">Why am I seeing this page?</div>
                    <div class="Content__Attribute">
                        This page is not found. Please contact your account manager to continue.
                    </div>
                </div>
                @if(isset($error_message))
                <div class="InfoBlock__Content">
                    <div class="Content__Attribute">
                        <div class="alert alert-danger">
                            <i class="fa fa-danger"></i>&nbsp;{{$error_message}}
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
@endsection