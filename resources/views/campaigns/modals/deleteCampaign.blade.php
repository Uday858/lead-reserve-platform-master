<!-- Delete Campaign Modal -->
<div class="modal fade" tabindex="-1" role="dialog" id="delete-campaign-modal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Are you sure you want to delete ({{$campaign->id}})&nbsp;{{$campaign->name}}
                    ?</h4>
            </div>
            <div class="modal-body">
                <form method="POST" action="{{route("campaigns.destroy",["id"=>$campaign->id])}}">
                    {{csrf_field()}}
                    <input name="_method" type="hidden" value="DELETE">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <button type="submit" class="btn btn-success btn-block">Yes</button>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <button data-dismiss="modal" class="btn btn-danger btn-block">No</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->