<div class="panel panel-default">
	<div class="panel-heading blue">
		Publisher Settings<br/>
		<small>Set the publisher payouts and caps.</small>
	</div>
	<div class="panel-body no-padding">
		<table class="table">
			<tr class="heading">
				<td>
					Publisher
				</td>
				<td>
					Settings
				</td>
			</tr>
			@foreach($campaign->publishers as $publisher)
			<tr>
				<td>
					<h4>{{$publisher->name}}</h4>
				</td>
				<td>
					<form method="POST" action="{{route("publishers.assign.update",["id"=>\App\PublisherCampaign::wherePublisherId($publisher->id)->whereCampaignId($campaign->id)->first()->id])}}">
                        {{csrf_field()}}
                        <input type="hidden" name="publisher_id" value="{{$publisher->id}}"/>
                        <input type="hidden" name="campaign_id" value="{{$campaign->id}}"/>
                        <div class="row">
                        	<div class="col-sm-3">
		                        <div class="form-group">
		                            <div class="input-group">
		                                <span class="input-group-addon">$</span>
		                                <input type="number" class="form-control" min="0" step="any" name="payout" placeholder="Payout (0.00)" value="{{\App\PublisherCampaign::wherePublisherId($publisher->id)->whereCampaignId($campaign->id)->first()->payout}}"/>
		                            </div>
		                        </div>		
                        	</div>
                        	<div class="col-sm-3">
		                        <div class="form-group">
		                            <input type="number" class="form-control" min="0" step="1" name="lead_cap" placeholder="Lead Cap" value="{{\App\PublisherCampaign::wherePublisherId($publisher->id)->whereCampaignId($campaign->id)->first()->lead_cap}}"/>
		                        </div>		
                        	</div>
                        	<div class="col-sm-3">
		                        <div class="form-group">
		                            <button type="submit" class="btn btn-primary">Edit Details</button>
		                        </div>		
                        	</div>
                        	<div class="col-sm-3">
					            <a class="btn btn-danger" style="color:#ffffff;" href="{{route("publishers.unassign",["publisherCampaignId"=>\App\PublisherCampaign::wherePublisherId($publisher->id)->whereCampaignId($campaign->id)->first()->id])}}">Unassign</a>    		
                        	</div>
                        </div>
                    </form>
				</td>
			</tr>
			@endforeach
		</table>
	</div>
</div>