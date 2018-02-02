@extends("layouts.framed")
@section("content")
    <div class="offer-path-loading-panel">
        <i class="fa fa-circle-o-notch fa-spin fa-3x fa-fw"></i>
    </div>
    <div class="offer-path-completion-panel">
        <h5>Thank you for registering!</h5>
        <p>Your offers will soon be sent to you!</p>
    </div>
    <div class="row">
        @foreach($offers as $offer)
            <div class="col-sm-12 offer-path-offer offer offer-{{$offer["campaign_id"]}}"
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
                                        @if($offer["is_linkout"])
                                            <button type="submit"
                                                    class="btn btn-block btn-success"
                                                    onclick="OfferPath.redirectToOffer('{{$offer["campaign_id"]}}','{{$offer["offer_posting_action"]}}')">
                                                Yes
                                            </button>
                                        @else
                                            <button type="submit"
                                                    class="btn btn-block btn-success"
                                                    onclick="OfferPath.submitLeadToCampaign('{{$offer["campaign_id"]}}','{{$offer["offer_posting_action"]}}')">
                                                Yes
                                            </button>
                                        @endif
                                    </div>
                                    <div class="offer-action second">
                                        <a class="btn btn-block btn-danger"
                                           onclick="OfferPath.declineCampaign({{$offer["campaign_id"]}})">No</a>
                                    </div>
                                </div>
                            </form>
                            <p class="tcpa">{!! isset($offer["offer_tcpa"]) ? $offer["offer_tcpa"] : "" !!}</p>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    <!-- Make sure offer-path-js executes. -->
    <div id="execute-offer-path-js"></div>

    <!-- Built by LeadReserve -->
    <div class="bottom-path-text">
        Powered by <a href="http://leadreserve.com" target="_blank">LeadReserve</a>
    </div>
@endsection
