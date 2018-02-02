@extends('layouts.app')
@section('title','Lead Explorer')
@section('content')
<div class="container-fluid">
	<div class="row">
		<div class="col-md-12">
			<h3><strong>Lead Explorer</strong></h3><hr/>
			<div class="PublisherCampaignLeadFlowDiagram">
				<div class="Diagram__Publisher">
					{{$publisher->name}}
					<small>Publisher</small>
				</div>
				<div class="Diagram__Action">
					<i style="color:#8c8c8c" class="fa fa-long-arrow-right"></i>
				</div>
				<div class="Diagram__Campaign">
					{{$campaign->name}}
					<small>Campaign</small>
				</div>
			</div>
			<hr/>
		</div>
	</div>
	<div class="row">
		<div class="col-md-12">
			<div class="panel panel-default">
				<div class="panel-body">
					<h4>Page {{$currentPage}} of {{$lastPage}}</h4>
					<h5><strong>Order</strong>:&nbsp;Oldest -> Newest</h5>
				</div>
			</div>
			<div class="row">
				<div class="col-sm-12">
					<div class="panel panel-default">
						<div class="panel-body no-padding">
							<table class="table table-hover">
								<tr class="heading">
									<th>Lead ID</th>
									<th>Email Address</th>
									<th>First Name</th>
									<th>Last Name</th>
									<th>Created At</th>
									<th></th>
									<th></th>
								</tr>
								@foreach($leads as $lead)
								<tr>
									<td>{{$lead->id}}</td>
									<td>{{$lead->email_address}}</td>
									<td>{{$lead->first_name}}</td>
									<td>{{$lead->last_name}}</td>
									<td>{{\Carbon\Carbon::parse($lead->created_at)->toDayDateTimeString()}}</td>
									<td>
										<a target="_blank" href="{{route('lead.explorer.detail',['lead_id' => $lead->id])}}">View</a>
									</td>
									<td>
										<LeadStatus :leadid="{{$lead->id}}"></LeadStatus>
									</td>
								</tr>
								@endforeach
							</table>		
						</div>
					</div>
					<nav aria-label="Page navigation">
					  <ul class="pagination">
					    @if(isset($_GET["page"]))
	                        @if($_GET["page"] != 0)
	                        	<li>
	                        		<a href="{{route("lead.explorer.index",array_merge($_GET,["page" => 0]))}}">
	                                	<span>&ll;</span>
	                            	</a>
	                        	</li>
	                        	<li>
	                        		<a href="{{route("lead.explorer.index",array_merge($_GET,["page" => $_GET["page"]-1]))}}">
	                                	Previous Page
	                            	</a>
	                        	</li>
	                        @endif
	                    @endif
	                    @for($i = 0; $i <= $lastPage; $i++)
	                        @if(isset($_GET["page"]))
	                            @if($_GET["page"] == $i)
	                            	<li class="active">
	                            		<a href="{{route("lead.explorer.index",array_merge($_GET,["page" => $i]))}}">
	                                    	{{$i}}
	                                	</a>	
	                            	</li>
	                                @else
	                                <li>
	                                	<a href="{{route("lead.explorer.index",array_merge($_GET,["page" => $i]))}}">
	                                    	{{$i}}
	                                	</a>
	                                </li>
	                            @endif
	                        @else
	                            <a href="{{route("lead.explorer.index",array_merge($_GET,["page" => $i]))}}">
	                                {{$i}}
	                            </a>
	                        @endif
	                    @endfor
	                    <li>
	                        <a href="{{route("lead.explorer.index",array_merge($_GET,["page" => (isset($_GET["page"])?$_GET["page"]:0)+1]))}}">
	                            Next Page
	                        </a>	
	                    </li>
	                    <li>
	                        <a href="{{route("lead.explorer.index",array_merge($_GET,["page" => -1]))}}">
	                            <span>&gg;</span>
	                        </a>
					    </li>
					  </ul>
					</nav>
				</div>
			</div>
		</div>
	</div>
</div>
</div>
@endsection