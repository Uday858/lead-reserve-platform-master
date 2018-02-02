@extends("layouts.framed")
@section("content")
<div class="col-sm-12 offer-path-offer offer show offer-{{$offer["campaign_id"]}}"
     data-campaign-id="{{$offer["campaign_id"]}}">
    <div class="row">
        <div class="col-sm-4">
            <img src="{{$offer["offer_creative"]}}"/>
        </div>
        <div class="col-sm-8">
            <div class="offer-data">
                <h2>{!! $offer["offer_heading"] !!}</h2>
                <p>{!! $offer["offer_text"] !!}</p>
                <form onsubmit="return false;">
                    <div class="offer-action-row">
                        <div class="offer-action">
                                <button type="submit"
                                        class="btn btn-block btn-success"
                                        onclick="">
                                    Yes
                                </button>
                        </div>
                        <div class="offer-action second">
                            <a class="btn btn-block btn-danger"
                               onclick="">No</a>
                        </div>
                    </div>
                </form>
                <p class="tcpa">
                    {!! $offer["offer_tcpa"] !!}
                </p>
            </div>
        </div>
    </div>
</div>
@endsection