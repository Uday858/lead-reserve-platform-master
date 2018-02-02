<div class="panel panel-default">
	<div class="panel-body">
		<h5>Validation Checking For Lead</h5>
		<p>
			The below table assesses what checks have been performed against a lead that has been sent to us.
		</p>
		<hr/>
		<table class="table table-hover">
			<tr class="heading">
				<th>Validation Name</th>
				<th>Validation Status</th>
			</tr>
			<tr>
				<td>Age Validation</td>
				<td>
					{{$age == 1 ? "True" : "False"}}
				</td>
			</tr>
			<tr>
				<td>Gender Validation</td>
				<td>
					{{$gender == 1 ? "True" : "False"}}
				</td>
			</tr>
			<tr>
				<td>DateTime Validation</td>
				<td>
					{{$datetime == 1 ? "True" : "False"}}
				</td>
			</tr>
			<tr>
				<td>BlackList Validation</td>
				<td>
					{{$blacklist == 1 ? "True" : "False"}}
				</td>
			</tr>
			<tr>
				<td>Field Exclusion Validation</td>
				<td>
					{{$fieldExclusion == 1 ? "True" : "False"}}
				</td>
			</tr>
		</table>

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