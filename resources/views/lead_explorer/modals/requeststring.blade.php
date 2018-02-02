<!-- Lead Points Modal -->
<div class="modal fade" tabindex="-1" role="dialog" id="lead-{{$lead["fields"]["id"]}}-request-modal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">({{$lead["fields"]["id"]}}) &mdash; Request String</h4>
            </div>
            <div class="modal-body">
                <pre>
                    {{$lead["request"]}}
                </pre>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->