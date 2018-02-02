<div class="row">
        <div class="col-sm-5">
            <div class="panel panel-default no-box-shadow">
                <div class="panel-body no-padding">
                    <div class="InfoBlock">
                        <div class="InfoBlock__Heading">
                            <div class="InfoBlock__Heading__Name">Publisher Details</div>
                        </div>
                        <div class="InfoBlock__Content">
                            <div class="Content__Title">Payout (Per Qualified Lead)</div>
                            <div class="Content__Attribute">${{number_format($publisherCampaign->payout,2)}}</div>
                        </div>
                        <div class="InfoBlock__Content">
                            <div class="Content__Title">Daily Lead Cap</div>
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
                            <div class="Content__Title">How do I submit lead information?</div>
                            <div class="Content__Attribute">
                                The below posting instructions should help you understand how to send lead information
                                to us via API, and what to expect in return. If you have any questions, please reach out
                                to, <a href="mailto:support@reservetechinc.com">support@reservetechinc.com</a>.
                            </div>
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
                            <div class="InfoBlock__Heading__Name">API Submission</div>
                        </div>
                        <div class="InfoBlock__Content">
                            <div class="Content__Title">Request Information</div>
                            <div class="Content__Attribute">
                                <table class="table table-hover clean">
                                    <tr></tr>
                                    <tr>
                                        <td>
                                            <strong>
                                                Request URL
                                            </strong>
                                        </td>
                                        <td>
                                            {{(new \App\Providers\PostingServiceProvider())->generatePublisherURL($campaign->id,$publisher->id)}}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <strong>
                                                Request Method
                                            </strong>
                                        </td>
                                        <td>
                                            POST
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <div class="InfoBlock__Content">
                            <div class="Content__Title">Request Fields</div>
                            <div class="Content__Attribute">
                                <table class="table table-hover clean">
                                    <tr>
                                        <th>Field Label</th>
                                        <th>URL Parameter</th>
                                        <th>Description</th>
                                        <th>Allowed Values</th>
                                    </tr>
                                    <tr>
                                        <td>Test Switch</td>
                                        <td>
                                            <code>test</code>
                                        </td>
                                        <td>For testing only. If your account manager has asked you for a "test lead", include this value in the posting string. <strong>Remove for production or live campaigns.</strong></td>
                                        <td>y,Y,1</td>
                                    </tr>
                                    @foreach($campaign->fields as $field)
                                        @if($field["type"] == "field" || $field["type"] == "inclusion")
                                            <tr>
                                                <td>{{$field["label"]}}</td>
                                                <td>
                                                    <code>
                                                        {{$field["incoming_field"]}}
                                                    </code>
                                                </td>
                                                <td>{{$field["spec_caption"]}}</td>
                                                <td>{{$field["inclusion_value"]}}</td>
                                            </tr>
                                        @endif
                                    @endforeach
                                </table>
                            </div>
                        </div>
                        <div class="InfoBlock__Content">
                            <div class="Content__Title">Example Success Response</div>
                            <div class="Content__Attribute">
                                <pre>{
    "status": "accepted",
    "transaction_time": "1.00s",
    "transaction_id": "xxxxxxxxxxxxx-c6156fd30f858aaec24ce1bcc4eeaa9f"
}</pre><p>
                                </p>
                            </div>
                        </div>
                        <div class="InfoBlock__Content">
                            <div class="Content__Title">Example Reject Response</div>
                            <div class="Content__Attribute">
                                <pre>{
    "status": "rejected",
    "transaction_time": "1.00s",
    "transaction_id": "xxxxxxxxxxxxx-c6156fd30f858aaec24ce1bcc4eeaa9f"
}</pre><p>
                                </p>
                            </div>
                        </div>
                        <div class="InfoBlock__Content">
                            <div class="Content__Title">Example Fail Response</div>
                            <div class="Content__Attribute">
                                <pre>{
    "status": "rejected",
    "message": "Value (x) should not be sent in (a,b,c)",
    "transaction_time": "1.00s",
    "transaction_id": "xxxxxxxxxxxxx-c6156fd30f858aaec24ce1bcc4eeaa9f"
}</pre><p>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>