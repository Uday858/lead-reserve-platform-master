<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title','') | {{ config('app.name', 'Laravel') }}</title>

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body>
    <div id="app">
        @if(Auth::check())
        <div class="SidebarContainer">
            <div class="panel panel-primary panel-header-left-navigation">
                <div class="panel-heading">
                    <h2>LEADRESERVE</h2>
                </div>
                <div class="panel-body no-padding">
                    <div class="InfoBlock dark condensed">
                        <div class="InfoBlock__Content {{(new \App\Providers\RoutingServiceProvider())->isActiveUrl(url("/"))}}">
                            <div class="Content__Attribute">
                                <a href="{{url("/")}}">Dashboard</a>
                            </div>
                        </div>
                        <div class="InfoBlock__Content {{(new \App\Providers\RoutingServiceProvider())->isActiveUrl(route("reporting.index"))}}">
                            <div class="Content__Attribute">
                                <a href="{{route("reporting.index")}}">Reporting</a>
                            </div>
                        </div>
                        <div class="InfoBlock__Heading">
                            <div class="InfoBlock__Heading__Name">Campaigns</div>
                        </div>
                        <div class="InfoBlock__Content {{(new \App\Providers\RoutingServiceProvider())->isActiveUrl(route("campaigns.create"))}}">
                            <div class="Content__Attribute">
                                <a href="{{route("campaigns.create")}}">Add new Campaign</a>
                            </div>
                        </div>
                        <div class="InfoBlock__Content {{(new \App\Providers\RoutingServiceProvider())->isActiveUrl(route("campaigns.index"))}}">
                            <div class="Content__Attribute">
                                <a href="{{route("campaigns.index")}}">View All Campaigns</a>
                            </div>
                        </div>
                        <div class="InfoBlock__Heading">
                            <div class="InfoBlock__Heading__Name">Advertisers</div>
                        </div>
                        <div class="InfoBlock__Content {{(new \App\Providers\RoutingServiceProvider())->isActiveUrl(route("advertisers.create"))}}">
                            <div class="Content__Attribute">
                                <a href="{{route("advertisers.create")}}">Add new Advertiser</a>
                            </div>
                        </div>
                        <div class="InfoBlock__Content {{(new \App\Providers\RoutingServiceProvider())->isActiveUrl(route("advertisers.index"))}}">
                            <div class="Content__Attribute">
                                <a href="{{route("advertisers.index")}}">View All Advertisers</a>
                            </div>
                        </div>
                        <div class="InfoBlock__Heading">
                            <div class="InfoBlock__Heading__Name">Publishers</div>
                        </div>
                        <div class="InfoBlock__Content {{(new \App\Providers\RoutingServiceProvider())->isActiveUrl(route("publishers.create"))}}">
                            <div class="Content__Attribute">
                                <a href="{{route("publishers.create")}}">Add new Publisher</a>
                            </div>
                        </div>
                        <div class="InfoBlock__Content {{(new \App\Providers\RoutingServiceProvider())->isActiveUrl(route("publishers.index"))}}">
                            <div class="Content__Attribute">
                                <a href="{{route("publishers.index")}}">View All Publishers</a>
                            </div>
                        </div>
                        <div class="InfoBlock__Heading">
                            <div class="InfoBlock__Heading__Name">Developer Tools</div>
                        </div>
                        <div class="InfoBlock__Content {{(new \App\Providers\RoutingServiceProvider())->isActiveUrl(route("platformevents.index"))}}">
                            <div class="Content__Attribute">
                                <a href="{{route("platformevents.index")}}">Platform Event Explorer</a>
                            </div>
                        </div>
                        <div class="InfoBlock__Content {{(new \App\Providers\RoutingServiceProvider())->isActiveUrl(route("lead.explorer.index"))}}">
                            <div class="Content__Attribute">
                                <a href="{{route("lead.explorer.index")}}">Lead Explorer</a>
                            </div>
                        </div>
                        <div class="InfoBlock__Content {{(new \App\Providers\RoutingServiceProvider())->isActiveUrl(route("developer.tools.regex.tester"))}}">
                            <div class="Content__Attribute">
                                <a href="{{route("developer.tools.regex.tester")}}">Regular Expression Tester</a>
                            </div>
                        </div>
                        <div class="InfoBlock__Content {{(new \App\Providers\RoutingServiceProvider())->isActiveUrl(route("thirdpartyaccess.index"))}}">
                            <div class="Content__Attribute">
                                <a href="{{route("thirdpartyaccess.index")}}">Client Access Settings</a>
                            </div>
                        </div>
                        <div class="InfoBlock__Heading">
                            <div class="InfoBlock__Heading__Name">{{Auth::user()->name}} <span class="caret"></span></div>
                        </div>
                        <div class="InfoBlock__Content">
                            <div class="Content__Attribute">
                                <a href="#" onclick="event.preventDefault();document.getElementById('logout-form').submit()">
                                    Logout
                                    <form id="logout-form"
                                          method="POST"
                                          action="{{route("logout")}}"
                                          style="display:none!important">
                                        {{csrf_field()}}
                                    </form>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
        <div class="MainContentContainer">
            <div class="MainContentContainer__Content">
                @yield('content')
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}"></script>
    <script src="https://cdn.datatables.net/1.10.15/js/jquery.dataTables.min.js"></script>
</body>
</html>
