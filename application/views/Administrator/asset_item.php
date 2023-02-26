<div id="app">
	<div class="row">
		<div class="col-xs-12">
			<!-- PAGE CONTENT BEGINS -->
			<form class="form-horizontal" @submit.prevent="saveAssetItem">
				
				<div class="form-group">
					<label class="col-sm-3 control-label no-padding-right" for="form-field-1"> Item Name  </label>
					<label class="col-sm-1 control-label no-padding-right">:</label>
					<div class="col-sm-3">
						<input type="text" v-model="item.name" placeholder="Item name"  class="form-control" required />
					</div>
				</div>
				
				<div class="form-group">
					<label class="col-sm-3 control-label no-padding-right" for="description">Description </label>
					<label class="col-sm-1 control-label no-padding-right">:</label>
					<div class="col-sm-3">
						<textarea v-model="item.description" class="form-control" placeholder="Item description" ></textarea>
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
					<datatable :columns="columns" :data="items" :filter-by="filter">
						<template scope="{ row }">
							<tr>
								<td>{{ row.sl }}</td>
								<td>{{ row.name }}</td>
								<td>{{ row.description }}</td>
								<td>
									<?php if($this->session->userdata('accountType') != 'u'){?>
									<button type="button" class="button edit" @click="editAssetItem(row)">
										<i class="fa fa-pencil"></i>
									</button>
									<button type="button" class="button" @click="deleteAssetItem(row.id)">
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
		item: {
			id: null,
			name: '',
			description: ''
		},

		items: [],

		columns: [
			{ label: 'Serial', field: 'sl', align: 'center', filterable: false },
			{ label: 'Item Name', field: 'name', align: 'center' },
			{ label: 'Description', field: 'description', align: 'center' },
			{ label: 'Action', align: 'center', filterable: false }
		],
		page: 1,
		per_page: 10,
		filter: ''
	},

	async created() {
		await this.getAssetItem();
	},

	methods: {
		async getAssetItem() {
			await axios.get('get-asset-item')
			.then(res => {
				this.items  = res.data.map((item, key) => {
					item.sl = key + 1;
					return item;
				});
			})
		},

		async saveAssetItem() {
			if(this.item.name == '') {
				alert("Item name is not empty !");
				return;
			}

			let url = '';

			if(this.item.id != null) {
				url = 'update-asset-item';

			} else {
				url = 'add-asset-item';
				delete this.item.id
			}

			await axios.post(url, this.item)
			.then(res => {
				alert(res.data.message)
				this.getAssetItem();
				this.resetFrom();
			})
			.catch(err => {
				alert(err.response.data.message);
			})

		},

		editAssetItem(item) {
			Object.keys(this.item).map((key) => {
				this.item[key] = item[key];
			})
		},

		async deleteAssetItem(id) {
			if(confirm('Are you sure ?')) {
				await axios.post('delete-asset-item', { id: id})
				.then(res => {
					alert(res.data.message);
					this.getAssetItem();
				})
			}
		},

		resetFrom() {
			this.item.name = '';
			this.item.description = '';
		}
	}
})
</script>