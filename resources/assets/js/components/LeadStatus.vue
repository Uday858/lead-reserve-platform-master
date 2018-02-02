<template>
	<div class="LeadStatusCard">
		<div class="Status__Icon">
			<span v-if="leadStatus == 'accepted'" class="status-circle accepted"></span>
			<span v-if="leadStatus == 'rejected'" class="status-circle rejected"></span>
			<span v-if="leadStatus != 'accepted' && leadStatus != 'rejected'" class="status-circle pending"></span>
		</div>
		<div class="Status__Text">
			{{leadStatus}}
		</div>
	</div>
</template>
<script>
	import {ResourceHttp} from '../services/Resource';
	export default {
		props:['leadid'],	
		data() {
			return {
				leadStatus: ''
			};
		},
		mounted() {
			ResourceHttp.get('com/lead-status/' + this.leadid).then((res) => {
				this.leadStatus = res.data;
			})
		}
	}
</script>