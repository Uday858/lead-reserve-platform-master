<div class="row">
    <div class="col-sm-5">
        <div class="panel panel-default no-box-shadow">
            <div class="panel-body no-padding">
                <div class="InfoBlock">
                    <div class="InfoBlock__Heading">
                        <div class="InfoBlock__Heading__Name">Publisher Details</div>
                    </div>
                    <div class="InfoBlock__Content">
                        <div class="Content__Title">Conversion Price</div>
                        <div class="Content__Attribute">${{number_format($publisherCampaign->payout,2)}}</div>
                    </div>
                    <div class="InfoBlock__Content">
                        <div class="Content__Title">Daily Conversion Cap</div>
                        <div class="Content__Attribute">{{$publisherCampaign->lead_cap}}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-7">
        <div class="panel panel-default no-box-shadow">
            <div class="panel-body no-padding">
                <div class="InfoBlock">
                    <div class="InfoBlock__Heading">
                        <div class="InfoBlock__Heading__Name">Lead Submission</div>
                    </div>
                    <div class="InfoBlock__Content">
                        <div class="Content__Title">Where are my unique links?</div>
                        <div class="Content__Attribute">View the box below to get your impression code, and your unique offer link.</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-12">
        <div class="panel panel-default no-box-shadow">
            <div class="panel-body no-padding">
                <div class="InfoBlock">
                    <div class="InfoBlock__Heading">
                        <div class="InfoBlock__Heading__Name">Unique Links</div>
                    </div>
                    <div class="InfoBlock__Content">
                        <div class="Content__Title">Impression Code</div>
                        <div class="Content__Attribute">
                            <pre>&lt;img src="http://api.{{env('APP_DOMAIN')}}/v{{env('API_VERSION')}}/lead/impression/{{$campaign->id}}/{{$publisher->id}}" width="1" height="1" border="0"/></pre>
                        </div>
                    </div>
                    <div class="InfoBlock__Content">
                        <div class="Content__Title">Offer Link</div>
                        <div class="Content__Attribute">
                            <p>
                                <a href="http://api.{{env('APP_DOMAIN')}}/v{{env('API_VERSION')}}/lead/redirect/{{$campaign->id}}/{{$publisher->id}}"><strong>http://api.{{env('APP_DOMAIN')}}/v{{env('API_VERSION')}}/lead/redirect/{{$campaign->id}}/{{$publisher->id}}</strong></a>
                            </p>
                        </div>
                    </div>
                    <div class="InfoBlock__Content">
                        <div class="Content__Title">Your Conversion Pixel</div>
                        <div class="Content__Attribute">
                            <p>
                                <strong>Please send it to your account manager</strong>, who can set it up within LeadReserve.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>