<div class="modal fade" tabindex="-1" role="dialog" id="{{$modalId}}">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Generate Test Link for <span style="opacity:0.5;">{{$campaign->name}}</span>
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="test-link-form">
                            <span class="hidden test-link-source">{{$campaign->posting_url}}</span>
                            @foreach($campaign->posting_params as $param)
                                <div class="form-group">
                                    <input class="form-control" placeholder="{{$param->label}}"
                                           name="{{$param->outgoing_field}}"/>
                                </div>
                            @endforeach
                        </div>
                        <div class="form-group">
                            <button class="btn btn-primary" id="test-link-generate-button">Generate Link</button>
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="form-group">
                            <textarea id="test-link-output" cols="70" rows="5">Test Link Will Be Generated Here...</textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>