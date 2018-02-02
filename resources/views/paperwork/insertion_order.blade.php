<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
	<head>
		<meta charset="utf-8">
    	<meta http-equiv="X-UA-Compatible" content="IE=edge">
    	<meta name="viewport" content="width=device-width, initial-scale=1">
	    <!-- CSRF Token -->
	    <meta name="csrf-token" content="{{ csrf_token() }}">
	    <title>{{$publisher->name}} &mdash; Publisher Insertion Order, {{\Carbon\Carbon::today()->toFormattedDateString()}}</title>
	    <!-- Styles -->
	    <style>
	    	body {
	    		font-family:Arial;
	    	}
	    	table td {
	    		padding:10px;
	    		background-color:#ececec;
	    	}
	    	table td[align=left] {
	    		width:380px;
	    	}
	    	.col25 {
	    		display:table-cell;
	    		width:25%;
	    	}
	    	.col50 {
	    		display:table-cell;
	    		width:50%;
	    	}
	    </style>
	</head>
	<body>
			<h2 style="text-align:center"> Publisher Insertion Order </h2>	

			<p>This insertion order agreement (the “Insertion Order”), effective as of {{\Carbon\Carbon::today()->toFormattedDateString()}} (the “Effective Date”), is by and between Reserve Tech, Inc (“Advertiser”), and {{$publisher->name}} (“Publisher”).</p>


			<div class="col50">
			<h3>Advertiser Information</h3>
			<table>
				<tr>
					<td align="right"><strong>Company Name</strong></td>
					<td align="left"><strong>Reserve Tech, Inc.</strong></td>
				</tr>
				<tr>
					<td align="right"><strong>Address</strong></td>
					<td align="left">65 Enterprise, 3rd Floor, Aliso Viejo, CA 92656</td>
				</tr>
				<tr>
					<td align="right"><strong>Main Contact</strong></td>
					<td align="left"><strong>Thomas Cutting</strong></td>
				</tr>
				<tr>
					<td align="right"><strong>Email</strong></td>
					<td align="left">thomas@reservetechinc.com</td>
				</tr>
				<tr>
					<td align="right"><strong>Phone</strong></td>
					<td align="left">+1 949 945 4814</td>
				</tr>
			</table>
			</div>
			<div class="col50">
			<h3>Publisher Information</h3>
			<table>
				<tr>
					<td align="right"><strong>Company Name</strong></td>
					<td align="left"><strong>{{$publisher->name}}</strong></td>
				</tr>
				<tr>
					<td align="right"><strong>Address</strong></td>
					<td align="left">{{$publisher->hasAttributeOrEmpty('address')}}</td>
				</tr>
				<tr>
					<td align="right"><strong>Main Contact</strong></td>
					<td align="left"><strong>{{$publisher->hasAttributeOrEmpty('poc_main_name')}}</strong></td>
				</tr>
				<tr>
					<td align="right"><strong>Email</strong></td>
					<td align="left">{{$publisher->email}}</td>
				</tr>
				<tr>
					<td align="right"><strong>Phone</strong></td>
					<td align="left">{{$publisher->hasAttributeOrEmpty('poc_main_phone')}}</td>
				</tr>
			</table>
			</div>
			<h3>Campaign Information</h3>
			<p>The Publisher will supply the Advertiser with leads in real time from their owned and operated sites. Campaign creative is below. See email chain or inquire for specific posting instructions and filters.</p>
			<iframe style="border:unset!important;" src="{{route('resources.example.offer',["campaignId" => $campaign->id])}}" width="100%" height="400px"></iframe>

			<h3>Terms and Conditions</h3>
			<p>This agreement will be governed by the IAB Standard Terms and Conditions Version 3 located at <a href="http://www.iab.com/wp-content/uploads/2015/06/IAB_4As-tsandcs-FINAL.pdf">http://www.iab.com/wp-content/uploads/2015/06/IAB_4As-tsandcs-FINAL.pdf</a>.   The following additional terms will apply attached in the Publisher Terms &amp; Conditions document.</p>

			<h3>Signatures</h3>

			<div class="col50">
				<h2>Reserve Tech, Inc.</h2>
				<table>
					<tr>
						<td>Signature:</td>
						<td>_________________________________________________________</td>
					</tr>
					<tr>
						<td>Name:</td>
						<td>Thomas Cutting</td>
					</tr>
					<tr>
						<td>Title:</td>
						<td>President</td>
					</tr>
					<tr>
						<td>Date:</td>
						<td>{{\Carbon\Carbon::today()->toFormattedDateString()}}</td>
					</tr>
				</table>	
			</div>
			<div class="col50">
				<h2>{{$publisher->name}}</h2>
				<table>
					<tr>
						<td>Signature:</td>
						<td>_________________________________________________________</td>
					</tr>
					<tr>
						<td>Name:</td>
						<td></td>
					</tr>
					<tr>
						<td>Title:</td>
						<td></td>
					</tr>
					<tr>
						<td>Date:</td>
						<td></td>
					</tr>
				</table>
			</div>
	</body>
</html>