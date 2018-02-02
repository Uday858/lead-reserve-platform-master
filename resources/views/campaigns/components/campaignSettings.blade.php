<div class="panel panel-default">
    <div class="panel-heading blue">Campaign Settings</div>
    <div class="panel-body">
        <form method="POST"
              action="{{route('campaigns.update-attributes',["id"=>$campaign->id])}}"
              enctype="multipart/form-data">

            <ul class="nav nav-tabs" role="tablist">
                <li role="presentation" class="active">
                    <a href="#payouts" role="tab" data-toggle="tab">
                        Payouts
                    </a>
                </li>
                <li role="presentation">
                    <a href="#posting-and-response" role="tab" data-toggle="tab">
                        Posting and Response(s)
                    </a>
                </li>
                <li role="presentation">
                    <a href="#validation" role="tab" data-toggle="tab">
                        Validation
                    </a>
                </li>
                <li role="presentation">
                    <a href="#filters" role="tab" data-toggle="tab">
                        Extra Filters
                    </a>
                </li>
                <li role="presentation">
                    <a href="#creative" role="tab" data-toggle="tab">
                        Creative
                    </a>
                </li>
                <li role="presentation">
                    <a href="#status" role="tab" data-toggle="tab">
                        Status
                    </a>
                </li>
            </ul>
            <div class="tab-content">
                <div role="tabpanel" class="tab-pane active" id="payouts">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Payout per Qualified Lead</label>
                                <div class="input-group">
                                    <span class="input-group-addon">$</span>
                                    <input type="number" class="form-control" min="0" step="any"
                                           name="attributes[cpl]"
                                           value="{{$campaign->hasAttributeOrEmpty("cpl")}}"/>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Campaign Daily Cap</label>
                                <div class="input-group">
                                    <input class="form-control" type="number" min="1" step="1"
                                           name="attributes[daily_cap]"
                                           value="{{$campaign->hasAttributeOrEmpty("daily_cap")}}"
                                           placeholder="Campaign Daily Cap"/>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div role="tabpanel" class="tab-pane" id="posting-and-response">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="panel panel-default">
                                <div class="panel-heading clean">Certificates?</div>
                                <div class="panel-body">
                                    <div class="form-group">
                                        <label>Campaign includes proof of consent certificate?</label>
                                        <div class="input-group">
                                            <select class="form-control"
                                                    name="attributes[include_consent_cert]">
                                                <option>--Select One--</option>
                                                <option value="Yes" {{($campaign->hasAttributeOrEmpty("include_consent_cert")!="")?($campaign->hasAttributeOrEmpty("include_consent_cert")=="Yes")?"selected":"":""}}>
                                                    Yes
                                                </option>
                                                <option value="No" {{($campaign->hasAttributeOrEmpty("include_consent_cert")!="")?($campaign->hasAttributeOrEmpty("include_consent_cert")=="No")?"selected":"":""}}>
                                                    No
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label>Which type of consent certificate?</label>
                                        <div class="input-group">
                                            <select class="form-control"
                                                    name="attributes[consent_cert_type]">
                                                <option>--Select One--</option>
                                                <option value="trusted_form" {{($campaign->hasAttributeOrEmpty("consent_cert_type")!="")?($campaign->hasAttributeOrEmpty("consent_cert_type")=="trusted_form")?"selected":"":""}}>
                                                    Trusted Form
                                                </option>
                                                <option value="lead_id" {{($campaign->hasAttributeOrEmpty("consent_cert_type")!="")?($campaign->hasAttributeOrEmpty("consent_cert_type")=="lead_id")?"selected":"":""}}>
                                                    Universal Lead ID
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="panel panel-default">
                                <div class="panel-heading clean">Response To Publisher</div>
                                <div class="panel-body">
                                    <div class="form-group">
                                        <label>Does this campaign fetch a real-time response, or send back a processing response?</label>
                                        <div class="input-group">
                                            <select class="form-control"
                                                    name="attributes[publisher_response_type]">
                                                <option>--Select One--</option>
                                                <option value="realtime" {{($campaign->hasAttributeOrEmpty("publisher_response_type")!="")?($campaign->hasAttributeOrEmpty("publisher_response_type")=="realtime")?"selected":"":""}}>
                                                    Real-Time
                                                </option>
                                                <option value="queued" {{($campaign->hasAttributeOrEmpty("publisher_response_type")!="")?($campaign->hasAttributeOrEmpty("publisher_response_type")=="queued")?"selected":"":""}}>
                                                    Processing
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="panel panel-default">
                                <div class="panel-heading clean">Pre-Ping to Advertiser -
                                    Settings
                                </div>
                                <div class="panel-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Campaign has pre-ping?</label>
                                                <div class="input-group">
                                                    <select class="form-control"
                                                            name="attributes[preping_available]">
                                                        <option>--Select One--</option>
                                                        <option value="Yes" {{($campaign->hasAttributeOrEmpty("preping_available")!="")?($campaign->hasAttributeOrEmpty("preping_available")=="Yes")?"selected":"":""}}>
                                                            Yes
                                                        </option>
                                                        <option value="No" {{($campaign->hasAttributeOrEmpty("preping_available")!="")?($campaign->hasAttributeOrEmpty("preping_available")=="No")?"selected":"":""}}>
                                                            No
                                                        </option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Pre-Ping Posting Method</label>
                                                <div class="input-group">
                                                    <select class="form-control"
                                                            name="attributes[preping_posting_method]">
                                                        <option>--Select One--</option>
                                                        <option value="GET" {{($campaign->hasAttributeOrEmpty("preping_posting_method")!="")?($campaign->hasAttributeOrEmpty("preping_posting_method")=="GET")?"selected":"":""}}>
                                                            GET
                                                        </option>
                                                        <option value="POST" {{($campaign->hasAttributeOrEmpty("preping_posting_method")!="")?($campaign->hasAttributeOrEmpty("preping_posting_method")=="POST")?"selected":"":""}}>
                                                            POST
                                                        </option>
                                                        <option value="PUT" {{($campaign->hasAttributeOrEmpty("preping_posting_method")!="")?($campaign->hasAttributeOrEmpty("preping_posting_method")=="PUT")?"selected":"":""}}>
                                                            PUT
                                                        </option>
                                                        <option value="PATCH" {{($campaign->hasAttributeOrEmpty("preping_posting_method")!="")?($campaign->hasAttributeOrEmpty("preping_posting_method")=="PATCH")?"selected":"":""}}>
                                                            PATCH
                                                        </option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Pre-Ping URL</label>
                                                <div class="input-group">
                                                    <input type="text" class="form-control"
                                                           name="attributes[preping_url]"
                                                           placeholder="http://www.api.leadgen.com/preping/?email=[Email]" value="{{$campaign->hasAttributeOrEmpty("preping_url")}}"/>
                                                </div>
                                                <p>
                                                    <small>
                                                        <strong>Available Variables in
                                                            Pre-Ping</strong>
                                                        <ul>
                                                            <li>[Email]</li>
                                                            <li>[FirstName]</li>
                                                            <li>[LastName]</li>
                                                            <li>[LeadId]</li>
                                                            <li>[PublisherId]</li>
                                                        </ul>
                                                    </small>
                                                </p>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Success Response From Pre-Ping</label>
                                                <div class="input-group">
                                            <textarea class="form-control"
                                                      name="attributes[preping_success_response]">{{$campaign->hasAttributeOrEmpty("preping_success_response")}}</textarea>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Rejected Response From Pre-Ping</label>
                                                <div class="input-group">
                                            <textarea class="form-control"
                                                      name="attributes[preping_reject_response]">{{$campaign->hasAttributeOrEmpty("preping_reject_response")}}</textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="panel panel-default">
                                <div class="panel-heading clean">Post to Advertiser - Settings
                                </div>
                                <div class="panel-body">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <input disabled type="text" class="form-control" value="{{$campaign->posting_url}}"/>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Campaign Posting Method</label>
                                                <div class="input-group">
                                                    <select class="form-control"
                                                            name="attributes[posting_method]"
                                                            placeholder="Posting Method to Advertiser">
                                                        <option>--Select One--</option>
                                                        <option value="GET" {{($campaign->hasAttributeOrEmpty("posting_method")!="")?($campaign->hasAttributeOrEmpty("posting_method")=="GET")?"selected":"":""}}>
                                                            GET
                                                        </option>
                                                        <option value="POST" {{($campaign->hasAttributeOrEmpty("posting_method")!="")?($campaign->hasAttributeOrEmpty("posting_method")=="POST")?"selected":"":""}}>
                                                            POST
                                                        </option>
                                                        <option value="PUT" {{($campaign->hasAttributeOrEmpty("posting_method")!="")?($campaign->hasAttributeOrEmpty("posting_method")=="PUT")?"selected":"":""}}>
                                                            PUT
                                                        </option>
                                                        <option value="PATCH" {{($campaign->hasAttributeOrEmpty("posting_method")!="")?($campaign->hasAttributeOrEmpty("posting_method")=="PATCH")?"selected":"":""}}>
                                                            PATCH
                                                        </option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Success Response From Advertiser</label>
                                                <div class="input-group">
                                            <textarea class="form-control"
                                                      name="attributes[success_response]">{{$campaign->hasAttributeOrEmpty("success_response")}}</textarea>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Rejected Response From Advertiser</label>
                                                <div class="input-group">
                                            <textarea class="form-control"
                                                      name="attributes[reject_response]">{{$campaign->hasAttributeOrEmpty("reject_response")}}</textarea>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Detail Regular Expression Match From Advertiser For Rejected or Failure</label>
                                                <div class="input-group">
                                            <textarea class="form-control"
                                                      name="attributes[detail_regex_match]">{{$campaign->hasAttributeOrEmpty("detail_regex_match")}}</textarea>
                                                </div>
                                                <p>
                                                    <small>
                                                        "/(\w*)/" is the regular expression pattern to match.<br/>
                                                        w - word<br/>
                                                        . - anything
                                                    </small>
                                                </p>
                                            </div>
                                            <div class="form-group">
                                                <label>Pattern Match Number From Advertiser</label>
                                                <div class="input-group">
                                            <input class="form-control" type="text" name="attributes[detail_regex_match_number]" value="{{$campaign->hasAttributeOrEmpty("detail_regex_match_number")}}"/>
                                                </div>
                                                <p>
                                                    <small>
                                                        <strong>Remember</strong>, the pattern matching is zero-index based.
                                                    </small>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div role="tabpanel" class="tab-pane" id="validation">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="panel panel-default">
                                <div class="panel-heading clean">Field Exclusion Check</div>
                                <div class="panel-body">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>Postal/ZIP Exclusion</label>
                                                <input type="text" class="form-control"
                                                       placeholder="92656,90001"
                                                       name="attributes[ziplist_exclusion]" value="{{$campaign->hasAttributeOrEmpty("ziplist_exclusion")}}"/>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>State Exclusion</label>
                                                <input type="text" class="form-control"
                                                       placeholder="NJ,FL,TX"
                                                       name="attributes[statelist_exclusion]" value="{{$campaign->hasAttributeOrEmpty("statelist_exclusion")}}"/>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="panel panel-default">
                                <div class="panel-heading clean">Days/Time Check</div>
                                <div class="panel-body">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>Campaign has specific days/time?</label>
                                                <div class="input-group">
                                                    <select class="form-control"
                                                            name="attributes[has_daystimecheck]">
                                                        <option>--Select One--</option>
                                                        <option value="Yes" {{($campaign->hasAttributeOrEmpty("has_daystimecheck")!="")?($campaign->hasAttributeOrEmpty("has_daystimecheck")=="Yes")?"selected":"":""}}>
                                                            Yes
                                                        </option>
                                                        <option value="No" {{($campaign->hasAttributeOrEmpty("has_daystimecheck")!="")?($campaign->hasAttributeOrEmpty("has_daystimecheck")=="No")?"selected":"":""}}>
                                                            No
                                                        </option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Specific Days</label>
                                                <div class="input-group">
                                                    <input type="checkbox" id="daystime_monday"
                                                           name="attributes[daystime_monday]"
                                                           value="true"/>&nbsp;&nbsp;<label
                                                            for="daystime_monday">Monday</label>
                                                </div>
                                                <div class="input-group">
                                                    <input type="checkbox" id="daystime_tuesday"
                                                           name="attributes[daystime_tuesday]"
                                                           value="true"/>&nbsp;&nbsp;<label
                                                            for="daystime_tuesday">Tuesday</label>
                                                </div>
                                                <div class="input-group">
                                                    <input type="checkbox"
                                                           id="daystime_wednesday"
                                                           name="attributes[daystime_wednesday]"
                                                           value="true"/>&nbsp;&nbsp;<label
                                                            for="daystime_wednesday">Wednesday</label>
                                                </div>
                                                <div class="input-group">
                                                    <input type="checkbox"
                                                           id="daystime_thursday"
                                                           name="attributes[daystime_thursday]"
                                                           value="true"/>&nbsp;&nbsp;<label
                                                            for="daystime_thursday">Thursday</label>
                                                </div>
                                                <div class="input-group">
                                                    <input type="checkbox" id="daystime_friday"
                                                           name="attributes[daystime_friday]"
                                                           value="true"/>&nbsp;&nbsp;<label
                                                            for="daystime_friday">Friday</label>
                                                </div>
                                                <div class="input-group">
                                                    <input type="checkbox"
                                                           id="daystime_saturday"
                                                           name="attributes[daystime_saturday]"
                                                           value="true"/>&nbsp;&nbsp;<label
                                                            for="daystime_saturday">Saturday</label>
                                                </div>
                                                <div class="input-group">
                                                    <input type="checkbox" id="daystime_sunday"
                                                           name="attributes[daystime_sunday]"
                                                           value="true"/>&nbsp;&nbsp;<label
                                                            for="daystime_sunday">Sunday</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <h5>Specific Times</h5>
                                            <div class="row">
                                                <div class="col-sm-2">
                                                    <div class="form-group">
                                                        <select class="form-control"
                                                                name="attributes[daystime_from_time]">
                                                            @for($i = 1; $i <= 12; $i++)
                                                                <option value="{{$i}}" {{($campaign->hasAttributeOrEmpty("daystime_from_time")!="")?($campaign->hasAttributeOrEmpty("daystime_from_time")==$i)?"selected":"":""}}>
                                                                    {{$i}}
                                                                </option>
                                                            @endfor
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-sm-2">
                                                    <div class="form-group">
                                                        <select class="form-control"
                                                                name="attributes[daystime_from_period]">
                                                            <option value="AM" {{($campaign->hasAttributeOrEmpty("daystime_from_period")!="")?($campaign->hasAttributeOrEmpty("daystime_from_period")=="AM")?"selected":"":""}}>
                                                                AM
                                                            </option>
                                                            <option value="PM" {{($campaign->hasAttributeOrEmpty("daystime_from_period")!="")?($campaign->hasAttributeOrEmpty("daystime_from_period")=="PM")?"selected":"":""}}>
                                                                PM
                                                            </option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-sm-2">
                                                    <div class="form-group">
                                                        <select class="form-control"
                                                                name="attributes[daystime_from_time]">
                                                            @for($i = 1; $i <= 12; $i++)
                                                                <option value="{{$i}}" {{($campaign->hasAttributeOrEmpty("daystime_to_time")!="")?($campaign->hasAttributeOrEmpty("daystime_to_time")==$i)?"selected":"":""}}>
                                                                    {{$i}}
                                                                </option>
                                                            @endfor
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-sm-2">
                                                    <div class="form-group">
                                                        <select class="form-control"
                                                                name="attributes[daystime_from_period]">
                                                            <option value="AM" {{($campaign->hasAttributeOrEmpty("daystime_to_period")!="")?($campaign->hasAttributeOrEmpty("daystime_to_period")=="AM")?"selected":"":""}}>
                                                                AM
                                                            </option>
                                                            <option value="PM" {{($campaign->hasAttributeOrEmpty("daystime_to_period")!="")?($campaign->hasAttributeOrEmpty("daystime_to_period")=="PM")?"selected":"":""}}>
                                                                PM
                                                            </option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="panel panel-default">
                                <div class="panel-heading clean">Blacklist</div>
                                <div class="panel-body">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>Email Domain Blacklist</label>
                                                <input type="text" class="form-control"
                                                       placeholder="john@doe.com, thomas@doe.com"
                                                       name="attributes[blacklist_email]"
                                                       value="{{$campaign->hasAttributeOrEmpty("blacklist_email")}}"/>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>Name Blacklist</label>
                                                <input type="text" class="form-control"
                                                       placeholder="John Doe, Thomas Doe"
                                                       name="attributes[name_blacklist]"
                                                       value="{{$campaign->hasAttributeOrEmpty("name_blacklist")}}"/>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div role="tabpanel" class="tab-pane" id="filters">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="panel panel-default">
                                <div class="panel-heading clean">Age</div>
                                <div class="panel-body">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>Campaign has specific age filter?</label>
                                                <div class="input-group">
                                                    <select class="form-control"
                                                            name="attributes[has_age_filter]">
                                                        <option>--Select One--</option>
                                                        <option value="Yes" {{($campaign->hasAttributeOrEmpty("has_age_filter")!="")?($campaign->hasAttributeOrEmpty("has_age_filter")=="Yes")?"selected":"":""}}>
                                                            Yes
                                                        </option>
                                                        <option value="No" {{($campaign->hasAttributeOrEmpty("has_age_filter")!="")?($campaign->hasAttributeOrEmpty("has_age_filter")=="No")?"selected":"":""}}>
                                                            No
                                                        </option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>From Age</label>
                                                <input type="number" step="1" class="form-control"
                                                       placeholder="Age"
                                                       name="attributes[age_from_range]" value="{{$campaign->hasAttributeOrEmpty("age_from_range")}}"/>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>To Age</label>
                                                <input type="number" step="1" class="form-control"
                                                       placeholder="Age"
                                                       name="attributes[age_to_range]" value="{{$campaign->hasAttributeOrEmpty("age_to_range")}}"/>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="panel panel-default">
                                <div class="panel-heading clean">Gender</div>
                                <div class="panel-body">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>Campaign has specific gender filter?</label>
                                                <div class="input-group">
                                                    <select class="form-control"
                                                            name="attributes[has_gender_filter]">
                                                        <option>--Select One--</option>
                                                        <option value="Yes" {{($campaign->hasAttributeOrEmpty("has_gender_filter")!="")?($campaign->hasAttributeOrEmpty("has_gender_filter")=="Yes")?"selected":"":""}}>
                                                            Yes
                                                        </option>
                                                        <option value="No" {{($campaign->hasAttributeOrEmpty("has_gender_filter")!="")?($campaign->hasAttributeOrEmpty("has_gender_filter")=="No")?"selected":"":""}}>
                                                            No
                                                        </option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="input-group">
                                                <select class="form-control"
                                                        name="attributes[gender_filter]">
                                                    <option>--Select One--</option>
                                                    <option value="Male" {{($campaign->hasAttributeOrEmpty("gender_filter")!="")?($campaign->hasAttributeOrEmpty("gender_filter")=="Male")?"selected":"":""}}>
                                                        Male
                                                    </option>
                                                    <option value="Female" {{($campaign->hasAttributeOrEmpty("gender_filter")!="")?($campaign->hasAttributeOrEmpty("gender_filter")=="Female")?"selected":"":""}}>
                                                        Female
                                                    </option>
                                                    <option value="Other" {{($campaign->hasAttributeOrEmpty("gender_filter")!="")?($campaign->hasAttributeOrEmpty("gender_filter")=="Other")?"selected":"":""}}>
                                                        Other
                                                    </option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div role="tabpanel" class="tab-pane" id="creative">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <img width="120" height="60" src="{{$campaign->hasAttributeOrEmpty("creative_image_url")}}"/>
                                <br/>
                                <label>Creative Image URL</label>
                                <div class="input-group">
                                    <input class="form-control"
                                           type="file"
                                           name="attributes[creative_image_url]"
                                           value="{{$campaign->hasAttributeOrEmpty("creative_image_url")}}"/>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Creative Heading</label>
                                <div class="input-group">
                                    <input class="form-control"
                                           name="attributes[creative_heading]"
                                           value="{{$campaign->hasAttributeOrEmpty("creative_heading")}}"
                                           placeholder="Do you want to save?"/>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Creative Text</label>
                                <div class="input-group">
                                            <textarea class="form-control"
                                                      name="attributes[creative_text]">{{$campaign->hasAttributeOrEmpty("creative_text")}}</textarea>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>TCPA Text</label>
                                <div class="input-group">
                                            <textarea class="form-control"
                                                      name="attributes[tcpa_text]">{{$campaign->hasAttributeOrEmpty("tcpa_text")}}</textarea>
                                </div>
                            </div>
                            <div class="form-group">
                                <a href="{{route('resources.get.offer',["campaignId" => $campaign->id])}}" target="_blank">
                                    Example Offer
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div role="tabpanel" class="tab-pane" id="status">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Campaign Status</label>
                                <div class="input-group">
                                    <select class="form-control"
                                            name="attributes[campaign_status]"
                                            placeholder="Campaign Status">
                                        <option>--Select One--</option>
                                        <option value="testing" {{($campaign->hasAttributeOrEmpty("campaign_status")!="")?($campaign->hasAttributeOrEmpty("campaign_status")=="testing")?"selected":"":""}}>
                                            Testing
                                        </option>
                                        <option value="live" {{($campaign->hasAttributeOrEmpty("campaign_status")!="")?($campaign->hasAttributeOrEmpty("campaign_status")=="live")?"selected":"":""}}>
                                            Live
                                        </option>
                                        <option value="inactive" {{($campaign->hasAttributeOrEmpty("campaign_status")!="")?($campaign->hasAttributeOrEmpty("campaign_status")=="inactive")?"selected":"":""}}>
                                            Inactive
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">Update Attributes</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>