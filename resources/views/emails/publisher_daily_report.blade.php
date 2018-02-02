<div style="padding:20px;">
	<img src="https://s3-us-west-2.amazonaws.com/lead-reserve/leadreservelogo.png" alt="Lead Reserve"/>
	<h2>Daily Report For {{$publisher->name}}</h2>
	<h3>{{$reportDate->toFormattedDateString()}}</h3>
	<div>
		<hr/>
		<h3>Statistics</h3>
		<table class="table table-hover">
			<tr class="heading">
				<th align="left" style="border:unset;background-color: #5858ff;padding: 5px;color: white;">Metric</th>
				<th align="left" style="border:unset;background-color: #5858ff;padding: 5px;color: white;">Value</th>
			</tr>
			<tr>
				<td align="right" style="font-weight:800;padding:10px;">Estimated Earnings</td>
				<td align="left">${{number_format($leadgenPerformance->sum('payout'),2)}}</td>
			</tr>
			<tr>
				<td align="right" style="font-weight:800;padding:10px;">Total Leads <strong>Generated</strong></td>
				<td align="left">{{$leadgenPerformance->sum('leads_generated')}}</td>
			</tr>
			<tr>
				<td align="right" style="font-weight:800;padding:10px;">Total Leads <strong>Accepted</strong></td>
				<td align="left"> {{$leadgenPerformance->sum('leads_accepted')}}</td>
			</tr>
			<tr>
				<td align="right" style="font-weight:800;padding:10px;">Total Leads <strong>Rejected</strong></td>
				<td align="left">{{$leadgenPerformance->sum('leads_rejected')}}</td>
			</tr>
			<tr>
				<td align="right" style="font-weight:800;padding:10px;">Acceptance Rate</td>
				<td align="left">{{number_format(($leadgenPerformance->sum('leads_accepted')/($leadgenPerformance->sum('leads_generated')==0?1:$leadgenPerformance->sum('leads_generated')))*100,2)}}%</td>
			</tr>
		</table>
	</div>
	<div>
		<hr/>
		<h3>Campaign Performance</h3>
		<table class="table table-hover">
			<tr class="heading">
				<th align="left" style="border:unset;background-color: #5858ff;padding: 10px;color: white;"><strong>Campaign</strong></th>
				<th align="left" style="border:unset;background-color: #5858ff;padding: 10px;color: white;"><strong>Leads</strong><br/><small>Generated / Accepted / Rejected</small></th>
				<th align="left" style="border:unset;background-color: #5858ff;padding: 10px;color: white;"><strong>Revenue</strong></th>
				<th align="left" style="border:unset;background-color: #5858ff;padding: 10px;color: white;"><strong>Error(s)</strong></th>
			</tr>
			@foreach($leadgenPerformance as $campaign)
			<tr style="background-color:#f9f9f9;">
				<td style="padding: 5px;background-color: #e4e4e4;font-weight: 800;color: black;border: unset;">{{\App\Campaign::whereId($campaign->campaign_id)->first()->name}}</td>
				<td style="padding:5px;">{{$campaign->leads_generated}} / {{$campaign->leads_accepted}} / {{$campaign->leads_rejected}}</td>
				<td style="padding:5px;">${{number_format($campaign->payout,2)}}</td>
				<td>
					<ul>
						{{-- This should be a user term! --}}
						@if((new \App\Jobs\Truths\LeadgenPublisherNotPerforming())->capNotMet($campaign->campaign_id,$campaign->publisher_id,68.50,$fromDate,$toDate))
						<li style="color:red;"><strong>You have not hit your daily cap on this campaign.</strong> (You are not at or above 70% of cap allowance.)</li>
						@endif
						@if((new \App\Jobs\Truths\LeadgenPublisherNotPerforming())->rejectRateIsHigh($campaign->campaign_id,$campaign->publisher_id,45.00,$fromDate,$toDate))
						<li style="color:red;"><strong>Your leads are being rejected.</strong> (Out of all the leads that you are sending, only 55% of them are being accepted.)</li>
						@endif
					</ul>
				</td>
			</tr>
			@endforeach
		</table>
	</div>
	<div>
		<hr/>
		<h4>
			Have a wonderful day!
		</h4>
		<p>
			If you have any questions about statistics, campaign performance, or otherwise, or want to stop recieving this email. <strong>Please contact your account manager.</strong>
		</p>
	</div>
</div>