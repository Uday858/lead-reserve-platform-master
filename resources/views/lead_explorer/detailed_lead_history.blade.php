@extends('layouts.app')
@section('title',$lead_id . " History")
@section('content')
<div class="container-fluid">
	<div class="row">
		<div class="col-md-12">
			<h3><strong>History For Lead ({{$lead_id}})</strong></h3><hr/>
			<div class="panel panel-default">
				<div class="panel-body">
					<div class="row">
						<div class="col-sm-3">
							<h4><small>Campaign</small><br/>{{$campaign->name}}</h4>		
						</div>
						<div class="col-sm-3">
							<h4><small>Publisher</small><br/>{{$publisher->name}}</h4>		
						</div>
						<div class="col-sm-3">
							<h4><small>Created At</small><br/>{{\Carbon\Carbon::parse($lead->created_at)->toDayDateTimeString()}}</h4>		
						</div>
					</div>
				</div>
			</div><hr/>

			<div class="row">
				<div class="col-sm-12">
					<LeadDetailExplorer :leadid={{$lead_id}}></LeadDetailExplorer>
				</div>
				<div class="col-sm-12">
					<h3>
						<strong>Transaction Details</strong><br/>
						<small>New way of referencing lead request, response.</small>
					</h3>
					<div class="alert alert-warning">
						<i class="fa fa-warning"></i>&nbsp;Look here now!
					</div>
					@if(isset($transactionData))
						@include("lead_explorer.components.transactionDataPanel",$transactionData)
					@endif
				</div>
				<div class="col-sm-12">
					<h3>
						<strong>Lead Status &amp; Validation</strong><br/>
						<small>If the lead has not been sent due to any kind of error. It will also show in this section.</small>
					</h3><hr/>	
				</div>
				<div class="col-sm-3">
					<div class="row">
						@if(isset($leadaccepted))
						<div class="col-sm-12">
							@include("lead_explorer.components.leadAcceptedPanel",$leadaccepted)
						</div>
						@endif
						@if(isset($leadrejected))
						<div class="col-sm-12">
							@include("lead_explorer.components.leadRejectedPanel",$leadrejected)
						</div>
						@endif
					</div>
				</div>
				<div class="col-sm-9">
					<div class="row">
						@if(isset($leadvalidation))
						<div class="col-sm-12">
							@include("lead_explorer.components.leadValidationPanel",$leadvalidation)
						</div>
						@endif
					</div>
				</div>
				@if(isset($leadsentfailed))
				<div class="col-sm-12">
					@include("lead_explorer.components.leadSentFailedPanel",$leadsentfailed)
				</div>
				@endif
			</div>
			<div class="row">
				<div class="col-sm-12">
					<h3>
						<strong>Request &amp; Response To Advertiser</strong><br/>
						<small>If the lead has not been sent due to any kind of error. It will also show in this section.</small>
					</h3><hr/>
				</div>
				@if(isset($leadpresend))
				<div class="col-sm-12">
					@include("lead_explorer.components.leadPresendPanel",$leadpresend)
				</div>
				@endif
				@if(isset($leadsent))
				<div class="col-sm-12">
					@include("lead_explorer.components.leadSentPanel",$leadsent)
				</div>
				@endif
			</div>
			<div class="row">
				<div class="col-sm-12">
					<h3>
						<strong>Other Metrics</strong><br/>
					</h3><hr/>	
				</div>
				<div class="col-sm-6">
					<div class="row">
						@if(isset($revenuetrack))
						<div class="col-sm-12">
							@include("lead_explorer.components.revenueTrackPanel",$revenuetrack)
						</div>
						@endif
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection