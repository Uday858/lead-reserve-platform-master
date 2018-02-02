<div class="panel panel-default">
    <div class="panel-heading blue">Campaign Settings</div>
    <div class="panel-body">
        <form method="POST"
              action="{{route('campaigns.update-attributes',["id"=>$campaign->id])}}">

            <ul class="nav nav-tabs" role="tablist">
                <li role="presentation" class="active">
                    <a href="#payouts" role="tab" data-toggle="tab">
                        Payouts
                    </a>
                </li>
                <li role="presentation">
                    <a href="#posting-and-response" role="tab" data-toggle="tab">
                        Linkout Settings
                    </a>
                </li>
                <li role="presentation">
                    <a href="#publisher-pixels" role="tab" data-toggle="tab">
                        Publisher Pixels
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
                                <div class="panel-heading clean">Linkout Settings</div>
                                <div class="panel-body">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>Linkout Address (and variables)</label>
                                                <div class="input-group" style="width: 100%">
                                                    <input type="text" class="form-control"
                                                           name="attributes[linkout_url]"
                                                           style="width:100%"
                                                           placeholder="http://www.link.com/?clickid=[ClickID]"
                                                           value="{{$campaign->hasAttributeOrEmpty("linkout_url")}}"/>
                                                </div>
                                                <p>
                                                    <small>
                                                        <strong>Available Variables in Linkout</strong>
                                                        <ul>
                                                            <li>[ClickID]</li>
                                                            <li>[CampaignID]</li>
                                                            <li>[AdvertiserID]</li>
                                                            <li>[PublisherID]</li>
                                                            <li>[IPAddress]</li>
                                                        </ul>
                                                    </small>
                                                </p>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>Hit Cap Fallback URL</label>
                                                <div class="input-group"  style="width: 100%">
                                                    <input type="text" class="form-control"
                                                           name="attributes[hitcap_fallback_url]"
                                                           style="width:100%"
                                                           placeholder="http://www.google.com"
                                                           value="{{$campaign->hasAttributeOrEmpty("hitcap_fallback_url")}}"/>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div role="tabpanel" class="tab-pane" id="publisher-pixels">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="panel panel-default">
                                <div class="panel-heading clean">Publisher Pixels</div>
                                <div class="panel-body">
                                    <div class="row">

                                        <table class="table">
                                            <tr class="heading">
                                                <th>Publisher Name &amp; Unique Links</th>
                                                <th>Publisher Pixel Options</th>
                                            </tr>
                                            @foreach($campaign->publishers as $publisher)
                                            <tr>
                                                <td>
                                                    <h4>{{$publisher->name}}</h4>
                                                    <p>
                                                        <strong>Instructions Link</strong> <a href="{{(new \App\Providers\PublisherResourceProvider())->getPostingInstructionLink($campaign->id,$publisher->id)}}">Click Here</a>
                                                    </p>
                                                    <p>
                                                        <strong>Impression Code</strong>
                                                        <pre>&lt;img src="http://api.{{env('APP_DOMAIN')}}/v{{env('API_VERSION')}}/lead/impression/{{$campaign->id}}/{{$publisher->id}}" width="1" height="1" border="0"/></pre>
                                                    </p>
                                                    <p>
                                                        <strong>Offer Link</strong>
                                                        <pre>http://api.{{env('APP_DOMAIN')}}/v{{env('API_VERSION')}}/lead/redirect/{{$campaign->id}}/{{$publisher->id}}</pre>
                                                    </p>
                                                </td>
                                                <td>
                                                    <div class="form-group">
                                                        <label>Conversion Pixel Text</label>
                                                        <textarea class="form-control"
                                                      name="attributes[publisher.{{$publisher->id}}.conversion_pixel]">{{$campaign->hasAttributeOrEmpty("publisher.".$publisher->id.".conversion_pixel")}}</textarea>
                                                    </div>
                                                    <div class="form-group">
                                                        <label>Fire Method</label>
                                                        <select class="form-control"
                                                        name="attributes[publisher.{{$publisher->id}}.fire_method]">
                                                            <option value="GET" {{($campaign->hasAttributeOrEmpty("publisher.".$publisher->id.".fire_method")!="")?($campaign->hasAttributeOrEmpty("publisher.".$publisher->id.".fire_method")=="GET")?"selected":"":""}}>GET Request</option>
                                                            <option value="POST" {{($campaign->hasAttributeOrEmpty("publisher.".$publisher->id.".fire_method")!="")?($campaign->hasAttributeOrEmpty("publisher.".$publisher->id.".fire_method")=="POST")?"selected":"":""}}>POST Request</option>
                                                            <option value="PUT" {{($campaign->hasAttributeOrEmpty("publisher.".$publisher->id.".fire_method")!="")?($campaign->hasAttributeOrEmpty("publisher.".$publisher->id.".fire_method")=="PUT")?"selected":"":""}}>PUT Request</option>
                                                        </select>
                                                    </div>
                                                    <div class="form-group">
                                                        <label>Fire Rate</label>
                                                        <select class="form-control"
                                                                name="attributes[publisher.{{$publisher->id}}.fire_rate]"
                                                                placeholder="Campaign Status">
                                                            <option>--Select One--</option>
                                                            <option value="10" {{($campaign->hasAttributeOrEmpty("publisher.".$publisher->id.".fire_rate")!="")?($campaign->hasAttributeOrEmpty("publisher.".$publisher->id.".fire_rate")=="10")?"selected":"":""}}>
                                                                10%
                                                            </option>
                                                            <option value="20" {{($campaign->hasAttributeOrEmpty("publisher.".$publisher->id.".fire_rate")!="")?($campaign->hasAttributeOrEmpty("publisher.".$publisher->id.".fire_rate")=="20")?"selected":"":""}}>
                                                                20%
                                                            </option>
                                                            <option value="30" {{($campaign->hasAttributeOrEmpty("publisher.".$publisher->id.".fire_rate")!="")?($campaign->hasAttributeOrEmpty("publisher.".$publisher->id.".fire_rate")=="30")?"selected":"":""}}>
                                                                30%
                                                            </option>
                                                            <option value="40" {{($campaign->hasAttributeOrEmpty("publisher.".$publisher->id.".fire_rate")!="")?($campaign->hasAttributeOrEmpty("publisher.".$publisher->id.".fire_rate")=="40")?"selected":"":""}}>
                                                                40%
                                                            </option>
                                                            <option value="50" {{($campaign->hasAttributeOrEmpty("publisher.".$publisher->id.".fire_rate")!="")?($campaign->hasAttributeOrEmpty("publisher.".$publisher->id.".fire_rate")=="50")?"selected":"":""}}>
                                                                50%
                                                            </option>
                                                            <option value="60" {{($campaign->hasAttributeOrEmpty("publisher.".$publisher->id.".fire_rate")!="")?($campaign->hasAttributeOrEmpty("publisher.".$publisher->id.".fire_rate")=="60")?"selected":"":""}}>
                                                                60%
                                                            </option>
                                                            <option value="70" {{($campaign->hasAttributeOrEmpty("publisher.".$publisher->id.".fire_rate")!="")?($campaign->hasAttributeOrEmpty("publisher.".$publisher->id.".fire_rate")=="70")?"selected":"":""}}>
                                                                70%
                                                            </option>
                                                            <option value="80" {{($campaign->hasAttributeOrEmpty("publisher.".$publisher->id.".fire_rate")!="")?($campaign->hasAttributeOrEmpty("publisher.".$publisher->id.".fire_rate")=="80")?"selected":"":""}}>
                                                                80%
                                                            </option>
                                                            <option value="90" {{($campaign->hasAttributeOrEmpty("publisher.".$publisher->id.".fire_rate")!="")?($campaign->hasAttributeOrEmpty("publisher.".$publisher->id.".fire_rate")=="90")?"selected":"":""}}>
                                                                90%
                                                            </option>
                                                            <option value="100" {{($campaign->hasAttributeOrEmpty("publisher.".$publisher->id.".fire_rate")!="")?($campaign->hasAttributeOrEmpty("publisher.".$publisher->id.".fire_rate")=="100")?"selected":"":""}}>
                                                                100%
                                                            </option>
                                                        </select>
                                                    </div>
                                                </td>
                                            </tr>
                                            @endforeach    
                                        </table>
                                        <div class="col-md-12">
                                            <p>
                                                    <small>
                                                        <strong>Available Variables in Publisher pixel</strong>
                                                        <ul>
                                                            <li>[ClickID]</li>
                                                            <li>[CampaignID]</li>
                                                            <li>[AdvertiserID]</li>
                                                            <li>[PublisherID]</li>
                                                            <li>[IPAddress]</li>
                                                        </ul>
                                                    </small>
                                                </p>
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
                                <label>Creative Image URL</label>
                                <div class="input-group">
                                    <input class="form-control"
                                           type="file"
                                           name="attributes[creative_image_url]"
                                           value="{{$campaign->hasAttributeOrEmpty("creative_image_url")}}"
                                           placeholder="Do you want to save?"/>
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