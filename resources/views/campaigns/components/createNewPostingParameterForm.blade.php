<div class="panel panel-default">
    <div class="panel-heading green">Create New Posting Parameter</div>
    <div class="panel-body">
        <form action="{{route("campaigns.create.posting-param",["id" => $campaign->id])}}"
              method="POST">
            <div class="row">
                <div class="col-sm-12">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label>Param Label</label>
                                <input type="text" class="form-control" placeholder="Affiliate ID"
                                       name="label"/>
                            </div>
                            <div class="form-group">
                                <label>Outgoing Field</label>
                                <input type="text" class="form-control" placeholder="aff_id"
                                       name="field"/>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <ul class="nav nav-tabs" role="tablist">
                                <li role="presentation" class="active">
                                    <a href="#field" role="tab" data-toggle="tab"
                                       class="postingParamSwitcher" data-type="field">
                                        Field Variable
                                    </a>
                                </li>
                                <li role="presentation">
                                    <a href="#hardcoded" role="tab" data-toggle="tab"
                                       class="postingParamSwitcher" data-type="static">
                                        Hard Coded Variable
                                    </a>
                                </li>
                                <li role="presentation">
                                    <a href="#system" role="tab" data-toggle="tab"
                                       class="postingParamSwitcher" data-type="system">
                                        System Variable
                                    </a>
                                </li>
                                <li role="presentation">
                                    <a href="#dropdown" role="tab" data-toggle="tab"
                                       class="postingParamSwitcher" data-type="dropdown">
                                        Dropdown Selection Variable
                                    </a>
                                </li>
                                <li role="presentation">
                                    <a href="#randomselection" role="tab" data-toggle="tab"
                                       class="postingParamSwitcher" data-type="randomComma">
                                        Random Selection Variable
                                    </a>
                                </li>
                            </ul>

                            <input type="hidden" name="paramType" value="field" id="paramTypeField"/>

                            <div class="tab-content">
                                <div role="tabpanel" class="tab-pane active" id="field">
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <label>Field Param Name</label>
                                                <input type="text" class="form-control"
                                                       placeholder="&incoming=" name="value[field]"/>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div role="tabpanel" class="tab-pane" id="hardcoded">
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <label>Hard Coded Value</label>
                                                <input type="text" class="form-control"
                                                       placeholder="1234" name="value[static]"/>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div role="tabpanel" class="tab-pane" id="system">
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <label>System Attribute Value</label>
                                                <select class="form-control" name="value[system]">
                                                    <option value="campaign_id">Campaign ID</option>
                                                    <option value="publisher_id">Publisher ID</option>
                                                    <option value="timestamp">Timestamp</option>
                                                    <option value="ip">IP Address</option>
                                                    <option value="ua">User Agent</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-12">
                                            <table class="table table-hover">
                                                <tr>
                                                    <th>System Variable Name</th>
                                                    <th>Type</th>
                                                    <th>Example</th>
                                                    <th>Notes</th>
                                                </tr>
                                                <tr>
                                                    <td>Campaign ID</td>
                                                    <td>Integer</td>
                                                    <td>({{$campaign->id}})</td>
                                                    <td></td>
                                                </tr>
                                                <tr>
                                                    <td>Publisher ID</td>
                                                    <td>Integer</td>
                                                    <td>(32)</td>
                                                    <td>Use this for source tracking.</td>
                                                </tr>
                                                <tr>
                                                    <td>Timestamp</td>
                                                    <td>String</td>
                                                    <td>{{\Carbon\Carbon::now()->toFormattedDateString()}}</td>
                                                    <td></td>
                                                </tr>
                                                <tr>
                                                    <td>IP Address</td>
                                                    <td>String</td>
                                                    <td>{{\Illuminate\Support\Facades\Request::ip()}}</td>
                                                    <td></td>
                                                </tr>
                                                <tr>
                                                    <td>User Agent</td>
                                                    <td>String</td>
                                                    <td>Mozilla/5.0 (Macintosh; Intel Mac OS X x.y;
                                                        rv:42.0) Gecko/20100101 Firefox/42.0
                                                    </td>
                                                    <td></td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <div role="tabpanel" class="tab-pane" id="randomselection">
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <label>Random Selection Of Comma Separated
                                                    Values</label>
                                                <input type="text" class="form-control"
                                                       placeholder="FirstValue,SecondValue,ThirdValue"
                                                       name="value[randomComma]"/>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div role="tabpanel" class="tab-pane" id="dropdown">
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <label>Dropdown Selection<br/>
                                                    <small>Place in comma separated values.</small>
                                                </label>
                                                <input type="text" class="form-control"
                                                       placeholder="FirstValue,SecondValue,ThirdValue"
                                                       name="value[dropdown]"/>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <button type="submit" class="btn btn-success">
                                Add New Field
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
<div class="panel panel-default">
    <div class="panel-heading">Posting Parameters</div>
    <div class="panel-body no-padding" style="overflow:scroll">
        <div class="row">
            <div class="col-sm-12">
                <div class="row">
                    <div class="col-sm-12">
                        <table class="table">
                            <tr>
                                <th>Label</th>
                                <th>Field</th>
                                <th>Type</th>
                                <th></th>
                                <th></th>
                            </tr>
                            @foreach($campaign->posting_params as $param)
                                <tr>
                                    <td>{{$param->label}}</td>
                                    <td>{{($param->incoming_field != "") ? $param->incoming_field : $param->outgoing_field}}</td>
                                    <td>{{$param->type}}</td>
                                    <td>{{
                                                    ($param->type == "type" /*TODO: Write more conditions for type.*/) ? "" : $param->static_value
                                                }}</td>
                                    <td>
                                        <form action="{{route("campaigns.destroy.posting-param",["campaignId" => $campaign->id,"paramId" => $param->id])}}"
                                              method="POST">
                                            <button type="submit" class="btn btn-danger">Remove</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </table>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>