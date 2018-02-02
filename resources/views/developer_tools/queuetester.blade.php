@extends("layouts.app")
@section("title","Queue Tester")
@section("content")
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="panel panel-default">
                	<div class="panel-heading blue">
                		LeadReserve Queue(s)<br/><small>For development purposes, to understand where the separate queue(s) is at.</small>
                	</div>
                	<div class="panel-body">
                		
                		<h3>
                			{{$platformProcessing}}
                			<br/><small>Realtime Platform Processing Queue</small>
                		</h3>
                		@if($platformProcessing < 5000)
	                		<div class="alert alert-success">
	                			<i class="fa fa-thumbs-up"></i>&nbsp;<strong>Sweet!</strong> Processing queue is under 5000 records long.
	                		</div>
                		@else
	                		<div class="alert alert-warning">
	                			<i class="fa fa-warning"></i>&nbsp;<strong>Warning!</strong> Processing queue is over 5000 records long.
	                		</div>
                		@endif
                		<hr/>

                		<h3>
                			{{$leadProcessing}}
                			<br/><small>Realtime Lead Processing Queue</small>
                		</h3>
                		@if($leadProcessing < 100)
	                		<div class="alert alert-success">
	                			<i class="fa fa-thumbs-up"></i>&nbsp;<strong>Sweet!</strong> Processing queue is under 100 records long.
	                		</div>
                		@else
	                		<div class="alert alert-warning">
	                			<i class="fa fa-warning"></i>&nbsp;<strong>Warning!</strong> Processing queue is over 100 records long.
	                		</div>
                		@endif
                		<hr/>

                		<h3>
                			{{$reportProcessing}}
                			<br/><small>Realtime Report Processing Queue &mdash; For caching, and, daily reports.</small>
                		</h3>
                		@if($reportProcessing < 50)
	                		<div class="alert alert-success">
	                			<i class="fa fa-thumbs-up"></i>&nbsp;<strong>Sweet!</strong> Processing queue is under 50 records long.
	                		</div>
                		@else
	                		<div class="alert alert-warning">
	                			<i class="fa fa-warning"></i>&nbsp;<strong>Warning!</strong> Processing queue is over 50 records long.
	                		</div>
                		@endif
                		<hr/>
                		
                		<h3>
                			{{$agedPlatformProcessing}}
                			<br/><small>Aged Data Platform Processing Queue &mdash; Any fucked up shite gets put here.</small>
                		</h3>
                		@if($agedPlatformProcessing < 5000)
	                		<div class="alert alert-success">
	                			<i class="fa fa-thumbs-up"></i>&nbsp;<strong>Sweet!</strong> Processing queue is under 5000 records long.
	                		</div>
                		@else
	                		<div class="alert alert-warning">
	                			<i class="fa fa-warning"></i>&nbsp;<strong>Warning!</strong> Processing queue is over 5000 records long.
	                		</div>
                		@endif
                		<hr/>
                		
                		<h3>
                			{{$leadSendingProcessing}}
                			<br/><small>Lead Sending Processing Queue</small>
                		</h3>
                		@if($leadSendingProcessing < 100)
	                		<div class="alert alert-success">
	                			<i class="fa fa-thumbs-up"></i>&nbsp;<strong>Sweet!</strong> Processing queue is under 100 records long.
	                		</div>
                		@else
	                		<div class="alert alert-warning">
	                			<i class="fa fa-warning"></i>&nbsp;<strong>Warning!</strong> Processing queue is over 100 records long.
	                		</div>
                		@endif
                		<hr/>
                	</div>
                </div>
            </div>
        </div>
    </div>
@endsection