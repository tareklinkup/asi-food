<div id="app">
	<div class="row">
		<div class="col-xs-12">
			<!-- PAGE CONTENT BEGINS -->
			<form class="form-horizontal" @submit.prevent="saveAssetAccount">
				
				<div class="form-group">
					<label class="col-sm-3 control-label no-padding-right" for="form-field-1"> Asset Name  </label>
					<label class="col-sm-1 control-label no-padding-right">:</label>
					<div class="col-sm-3">
						<input type="text" v-model="asset.name" placeholder="Asset name"  class="form-control" required />
					</div>
				</div>
				
				<div class="form-group">
					<label class="col-sm-3 control-label no-padding-right" for="description">Description </label>
					<label class="col-sm-1 control-label no-padding-right">:</label>
					<div class="col-sm-3">
						<textarea v-model="asset.description" class="form-control" placeholder="Asset description" ></textarea>
					</div>
				</div>
				
				<div class="form-group">
					<label class="col-sm-3 control-label no-padding-right" for="form-field-1"></label>
					<label class="col-sm-1 control-label no-padding-right"></label>
					<div class="col-sm-8">
							<button type="submit" class="btn btn-sm btn-success" name="btnSubmit">
								Submit
								<i class="ace-icon fa fa-arrow-right icon-on-right bigger-110"></i>
							</button>
					</div>
				</div>
				
			</form>
		</div>
	</div>

	<div class="row">
		<div class="col-xs-12">

			<div class="clearfix">
				<div class="pull-right tableTools-container"></div>
			</div>
			<div class="table-header">
				Asset Account Information
			</div>

			<!-- div.table-responsive -->

			<!-- div.dataTables_borderWrap -->
			<div class="table-responsive">
				<div class="col-md-3">
					<div class="form-group">
						<label for="filter" class="sr-only">Filter</label>
						<input type="text" class="form-control" v-model="filter" placeholder="Filter">
					</div>
				</div>
				<div class="col-md-12">
					<datatable :columns="columns" :data="assets" :filter-by="filter">
						<template scope="{ row }">
							<tr>
								<td>{{ row.sl }}</td>
								<td>{{ row.name }}</td>
								<td>{{ row.description }}</td>
								<td>
									<?php if($this->session->userdata('accountType') != 'u'){?>
									<button type="button" class="button edit" @click="editasset(row)">
										<i class="fa fa-pencil"></i>
									</button>
									<button type="button" class="button" @click="deleteasset(row.id)">
										<i class="fa fa-trash"></i>
									</button>
									<?php }?>
								</td>
							</tr>
						</template>
					</datatable>
					<datatable-pager v-model="page" type="abbreviated" :per-page="per_page"></datatable-pager>
				</div>
			</div>
		</div>
	</div>
</div>
					
<script src="<?php echo base_url();?>assets/js/vue/vue.min.js"></script>
<script src="<?php echo base_url();?>assets/js/vue/axios.min.js"></script>
<script src="<?php echo base_url();?>assets/js/vue/vuejs-datatable.js"></script>
<script>
const app = new Vue({
	el: '#app',
	data: {
		asset: {
			id: null,
			name: '',
			description: ''
		},

		assets: [],

		columns: [
			{ label: 'Serial', field: 'sl', align: 'center', filterable: false },
			{ label: 'Asset Name', field: 'name', align: 'center' },
			{ label: 'Description', field: 'description', align: 'center' },
			{ label: 'Action', align: 'center', filterable: false }
		],
		page: 1,
		per_page: 10,
		filter: ''
	},

	async created() {
		await this.getAssetAccounts();
	},

	methods: {
		async getAssetAccounts() {
			await axios.get('get-asset-account')
			.then(res => {
				this.assets  = res.data.map((asset, key) => {
					asset.sl = key + 1;
					return asset;
				});
			})
		},

		async saveAssetAccount() {
			if(this.asset.name == '') {
				alert("asset name is not empty !");
				return;
			}

			let url = '';

			if(this.asset.id != null) {
				url = 'update-asset-account';

			} else {
				url = 'add-asset-account';
				delete this.asset.id
			}

			await axios.post(url, this.asset)
			.then(res => {
				alert(res.data.message)
				this.getAssetAccounts();
				this.resetFrom();
			})
			.catch(err => {
				alert(err.response.data.message);
			})

		},

		editasset(asset) {
			Object.keys(this.asset).map((key) => {
				this.asset[key] = asset[key];
			})
		},

		async deleteasset(id) {
			if(confirm('Are you sure ?')) {
				await axios.post('delete-asset-account', { id: id})
				.then(res => {
					alert(res.data.message);
					this.getAssetAccounts();
				})
			}
		},

		resetFrom() {
			this.asset.name = '';
			this.asset.description = '';
		}
	}
})
</script>