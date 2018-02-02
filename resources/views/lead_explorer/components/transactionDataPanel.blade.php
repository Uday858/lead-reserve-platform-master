<div class="panel panel-default">
	<div class="panel-body">
		<h5>Transaction Data</h5>
		<p>
			Transaction details.
		</p>
		<hr/>

		<table class="table table-striped">
			<tr>
				<th>Transaction ID</th>
				<th>IP Address</th>
				<th>Transaction Time</th>
			</tr>
			<tr>
				<td>
					<strong>{{$transactionData["transaction_id"]}}</strong>
				</td>
				<td>{{$transactionData["ip_address"]}}</td>
				<td>{{$transactionData["transaction_time"]}} seconds</td>
			</tr>
		</table>
		<table class="table table-striped">
			<tr>
				<th>Request URL</th>
			</tr>
			<tr>
				<td><pre class="updated dark">{{$transactionData["full_request_url"]}}</pre></td>
			</tr>
		</table>
		<table class="table table-striped">
			<tr>
				<th>Response Contents</th>
			</tr>
			<tr>
				<td><pre class="updated dark">{{$transactionData["response_from_advertiser"]}}</pre></td>
			</tr>
		</table>
		<table class="table table-striped">
			<tr>
				<th>Payload</th>
			</tr>
			<tr>
				<td><pre class="updated dark">{{$transactionData["payload"]}}</pre></td>
			</tr>
		</table>
		<table class="table table-striped">
			<tr>
				<th>Error Message</th>
			</tr>
			<tr>
				<td><pre class="updated dark">{{$transactionData["error_message"]}}</pre></td>
			</tr>
		</table>
	</div>
</div>