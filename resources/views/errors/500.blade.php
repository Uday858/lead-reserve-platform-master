@extends("layouts.resource")
@section("title","Page Not Found")
@section("content")
    <div class="panel panel-default transparent">
        <div class="panel-body no-padding">
            <div class="InfoBlock">
                <div class="InfoBlock__Heading">
                    <div class="InfoBlock__Heading__Name">Something Went Wrong</div>
                </div>
                <div class="InfoBlock__Content">
                    <div class="Content__Title">Why am I seeing this page?</div>
                    <div class="Content__Attribute">
                        This page is not found. Please contact your account manager to continue.
                        <div id="__errorBlockContainer" style="display:none;">
                            {{$exception->getMessage()}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection