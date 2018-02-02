<template>
	<div class="panel panel-default">
		<div class="panel-body text-center">
			<h5>
				<span v-if="leadStatus == 'accepted'" class="status-circle accepted"></span>
				<span v-if="leadStatus == 'rejected'" class="status-circle rejected"></span>
				<span v-if="leadStatus != 'accepted' && leadStatus != 'rejected'" class="status-circle pending"></span>
				{{leadStatus}}
			</h5>
			<h5>{{lead.first_name}} {{lead.last_name}}</h5>
			<h5>
				<i class="fa fa-envelope"></i>&nbsp;{{lead.email_address}}
			</h5>
			<h5>
				<i class="fa fa-clock"></i>&nbsp;{{lead.created_at}}
			</h5>
			<button type="button" class="btn btn-primary" data-toggle="modal" :data-target="'#' + uniqueId">
				Expand Details
			</button>
			<!-- Modal -->
			<div class="modal fade" :id="uniqueId" tabindex="-1" role="dialog" aria-labelledby="Expanded Details Modal">
			  <div class="modal-dialog" role="document">
			    <div class="modal-content">
			      <div class="modal-header">
			        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			        <h4 class="modal-title" id="myModalLabel">Lead {{lead.id}}</h4>
			      </div>
			      <div class="modal-body">
			        <leaddetailexplorer :leadid="lead.id"></leaddetailexplorer>
			        <h5><strong>Request</strong><br/><small>The request to advertiser string.</small></h5>
        			<p>
        				<pre class="updated dark">{{request}}</pre>
        			</p>
        			<h5><strong>Response</strong><br/><small>The response from the advertiser.</small></h5>
        			<p>
        				<pre class="updated dark">{{response}}</pre>
        			</p>
        			<h5><strong>Status</strong><br/><small>The status of the lead.</small></h5>
        			<p>
        				<pre class="updated dark">{{leadStatus}}</pre>
        			</p>
			      </div>
			    </div>
			  </div>
			</div>
		</div>
	</div>
</template>
<script>

	import {ResourceHttp} from '../services/Resource';

	export default {
		props:['leadobject'],
		data() {
			return {
				lead: this.leadobject,
				uniqueId: '',
				request: '',
				response: '',
				leadStatus: ''
			};
		},
		mounted() {
			// this.lead = this.leadobject;
			this.uniqueId = Date.now() + "lead" + this.lead.id;
			ResourceHttp.get("com/lead-full-history/" + this.lead.id).then((res) => {
				let data = res.data;
				if(data.length == 1) {
					this.request = data[0].request;
					this.response = data[0].response;
					this.leadStatus = data[0].status;
				}
			});
		}
	}
</script>