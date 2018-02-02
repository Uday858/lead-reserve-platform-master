<template>
	<div class="panel panel-default">
		<div class="panel-heading black">
			Lead Object Points<br/>
			<small>This is what the publisher sent us.</small>
		</div>
		<div class="panel-body">
			<div v-for="value in leadPoints">
				<div class="InfoKeyValueBlock" v-for="(value,key) in value">
					<div class="Key">{{key}}</div>
					<div class="Value">{{value}}</div>
				</div>
			</div>
		</div>
	</div>
</template>
<script>

	import {ResourceHttp} from '../services/Resource';

	export default {
		props:['leadid'],
		data() {
			return {
				leadPoints:[]
			};
		},
		mounted() {
			ResourceHttp.post('com/internal-resource/access/Lead/id/' + this.leadid,{},{
				headers:{'Authorization':'$2y$10$ZuoXI1BlRCLOWMZnNPzlsuaB37Cf4v24F2a1rBfi/92lcuL3MWk3a'}
			}).then((res) => {
				let data = res.data;
				console.log(data);
				this.leadPoints.push(data.object);
			});
		}
	}
</script>
<style></style>