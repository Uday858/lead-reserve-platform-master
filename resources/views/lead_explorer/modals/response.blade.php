<!-- Lead Points Modal -->
<div class="modal fade" tabindex="-1" role="dialog" id="lead-{{$lead["fields"]["id"]}}-response-modal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">({{$lead["fields"]["id"]}}) &mdash; Response Contents</h4>
            </div>
            <div class="modal-body">
                <pre>
                    {{$lead["response"]}}
                </pre>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->