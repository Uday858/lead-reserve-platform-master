<div class="panel panel-default">
	<div class="panel-body">
		<h5 class="text-center">
			<i class="fa fa-thumbs-down fa-3x"></i><br/><br/>
			<strong>Lead Was Rejected</strong>
		</h5>
		@if(isset($error_message))
		<div class="alert alert-danger">
			<i class="fa fa-warning"></i>&nbsp;<strong>{{$error_message}}</strong>
		</div>
		@endif
		@if(isset($warning_message))
		<div class="alert alert-warning">
			<i class="fa fa-warning"></i>&nbsp;<strong>{{$warning_message}}</strong>
		</div>
		@endif
		@if(isset($success_message))
		<div class="alert alert-success">
			<i class="fa fa-thumbs-up"></i>&nbsp;<strong>{{$success_message}}</strong>
		</div>
		@endif
	</div>
</div>