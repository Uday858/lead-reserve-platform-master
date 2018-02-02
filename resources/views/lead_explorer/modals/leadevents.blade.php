<!-- Lead Points Modal -->
<div class="modal fade" tabindex="-1" role="dialog" id="lead-{{$lead["fields"]["id"]}}-events-modal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">({{$lead["fields"]["id"]}}) &mdash; Lead Event Explorer</h4>
            </div>
            <div class="modal-body">
                <p>Lead events, in lead flow, format.</p>
                <div class="DataPointCollection">
                    @foreach($lead["events"] as $value)
                        <div class="DataPoint">
                            <div class="Key">
                                {{$value["name"]}}<br/>
                                <small>{{$value["description"]}}</small>
                            </div>
                            <div class="Value" style="padding:0px !important;vertical-align:middle;">
                                <code>{{$value["json_value"]}}</code>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->