<!-- Edit Campaign Modal -->
<div class="modal fade" tabindex="-1" role="dialog" id="duplicate-campaign-modal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Duplicate ({{$campaign->id}})&nbsp;{{$campaign->name}}</h4>
            </div>
            <div class="modal-body">
                <form method="POST" action="{{route("campaigns.duplicate",["id"=>$campaign->id])}}">
                    {{csrf_field()}}
                    <input type="hidden" name="advertiser_id" value="{{$campaign->advertiser->id}}">
                    <input type="hidden" name="campaign_type_id" value="{{$campaign->type->id}}">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <input class="form-control" name="name" placeholder="Campaign Name"
                                       value="{{$campaign->name}}"/>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="form-group">
                                <input class="form-control" name="posting_url" placeholder="Posting URL"
                                       value="{{$campaign->posting_url}}"/>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="form-group">
                                <button type="submit" class="btn btn-success btn-block">Duplicate Campaign
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->