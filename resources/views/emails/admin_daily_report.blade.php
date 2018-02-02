<div style="padding:20px;">
	<img src="https://s3-us-west-2.amazonaws.com/lead-reserve/leadreservelogo.png" alt="Lead Reserve"/>
	<h2>Daily Admin Report</h2>
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
				<td align="right" style="font-weight:800;padding:10px;">Revenue</td>
				<td align="left">${{number_format(collect($leadgenPerformance)->sum('revenue'),2)}}</td>
			</tr>
			<tr>
				<td align="right" style="font-weight:800;padding:10px;">Payout</td>
				<td align="left">${{number_format(collect($leadgenPerformance)->sum('payout'),2)}}</td>
			</tr>
			<tr>
				<td align="right" style="font-weight:800;padding:10px;">Margin (Earnings)</td>
				<td align="left">${{number_format(collect($leadgenPerformance)->sum('revenue')-collect($leadgenPerformance)->sum('payout'),2)}}</td>
			</tr>
			<tr>
				<td align="right" style="font-weight:800;padding:10px;">Total Leads <strong>Generated</strong></td>
				<td align="left">{{collect($leadgenPerformance)->sum('leads_generated')}}</td>
			</tr>
			<tr>
				<td align="right" style="font-weight:800;padding:10px;">Total Leads <strong>Accepted</strong></td>
				<td align="left"> {{collect($leadgenPerformance)->sum('leads_accepted')}}</td>
			</tr>
			<tr>
				<td align="right" style="font-weight:800;padding:10px;">Total Leads <strong>Rejected</strong></td>
				<td align="left">{{collect($leadgenPerformance)->sum('leads_rejected')}}</td>
			</tr>
			<tr>
				<td align="right" style="font-weight:800;padding:10px;">Overall Acceptance Rate</td>
				<td align="left">{{number_format((collect($leadgenPerformance)->sum('leads_accepted')/(collect($leadgenPerformance)->sum('leads_generated')==0?1:collect($leadgenPerformance)->sum('leads_generated')))*100,2)}}%</td>
			</tr>
		</table>
	</div>
	<div>
		@include("dashboard.components.agedCampaignPerformance",["campaigns" => $leadgenPerformance])
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