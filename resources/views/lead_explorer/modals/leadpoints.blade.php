<!-- Lead Points Modal -->
<div class="modal fade" tabindex="-1" role="dialog" id="lead-{{$lead["fields"]["id"]}}-points-modal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">({{$lead["fields"]["id"]}}) &mdash; Lead Point Explorer</h4>
            </div>
            <div class="modal-body">
                <p>Lead fields and additional lead points.</p>
                <div class="DataPointCollection">
                    @foreach($lead["fields"] as $key => $value)
                        <div class="DataPoint">
                            <div class="Key">{{$key}}</div>
                            <div class="Value">{{$value}}</div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->