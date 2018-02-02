<!-- Edit Campaign Modal -->
<div class="modal fade" tabindex="-1" role="dialog" id="edit-campaign-modal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Edit ({{$campaign->id}})&nbsp;{{$campaign->name}}</h4>
            </div>
            <div class="modal-body">
                <form method="POST" action="{{route("campaigns.update",["id"=>$campaign->id])}}">
                    {{csrf_field()}}
                    <input name="_method" type="hidden" value="PUT">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <input class="form-control" name="name" placeholder="Campaign Name"
                                       value="{{$campaign->name}}"/>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <div id="edit-campaign-campaign-type-value"
                                     data-campaign-type-id="{{$campaign->campaign_type_id}}"></div>
                                <select class="form-control" id="edit-campaign-campaign-type"
                                        name="campaign_type_id">
                                    <option>--Choose Campaign Type--</option>
                                    <option value="1">CPL</option>
                                    <option value="2">CPA</option>
                                    <option value="3">Leadgen</option>
                                    <option value="4">Linkout</option>
                                </select>
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
                                <button type="submit" class="btn btn-success btn-block">Update Campaign
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->