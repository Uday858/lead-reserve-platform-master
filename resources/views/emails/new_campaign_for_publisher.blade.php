<div style="padding:20px;">
	<img src="https://s3-us-west-2.amazonaws.com/lead-reserve/leadreservelogo.png" alt="Lead Reserve"/>
	<h2>You were assigned {{$campaign->name}}</h2>
	<h3>Testing now active at {{$reportDate}}</h3>
	<div>
		<hr/>
		<h4>{{$leadcap}} leads a day at ${{number_format($payout,2)}}</h4>
		<hr/>
		<p>{{$publisher->name}} was just assigned to {{$campaign->name}}. <a href="{{$postingInstructionLink}}">Please see posting instructions</a>. Once you've been setup, <strong>please send an email to your account manager</strong>.</p>
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