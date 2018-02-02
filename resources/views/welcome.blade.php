<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body class="white-background landing">
<img src="{{asset('images/blue-triangle-full.png')}}" class="top-blue-triangle"/>
<section class="header">
    <div class="jumbotron">
        <div class="row">
            <div class="col-sm-12">
                <img src="{{asset('images/laptop-demo.png')}}"/>
                <h2>We built an advertiser/publisher agency tool.</h2>
                <h5>Why waste time with other SaaS products when everything you need is right here. We're disrupting the
                    customer acquisition and lead generation industry with our technology and brand.</h5>
                <h5 class="price-point">Beta Launch Soon.</h5>
                <a href="{{route("login")}}" class="btn btn-bordered btn-blue">Sign In</a>
            </div>
        </div>
    </div>
</section>
<img src="{{asset('images/blue-triangle-full.png')}}" class="bottom-blue-triangle"/>
</body>
</html>
