<style>
    .v-select {
        margin-bottom: 5px;
    }

    .v-select .form-control {
        height: 17px;
    }
</style>

<div id="material-category">
    <div class="row">
        <div class="col-md-10 col-md-offset-3">
            <form id="materialForm" class="form-horizontal" v-on:submit.prevent="saveMaterialCategory">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label col-md-4">Category Name</label>
                        <label class="control-label col-md-1"> : </label>
                        <div class="col-md-6">
                            <input type="text" v-model="materialCategory.name" class="form-control">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-4">Description</label>
                        <label class="control-label col-md-1"> : </label>
                        <div class="col-md-6">
                            <textarea v-model="materialCategory.description" class="form-control"></textarea>
                        </div>
                    </div>
                    <div class="form-group" style="margin-top: 15px;">
                        <div class="col-md-offset-5 col-md-6">
                            <button type="submit" name="btnSubmit" title="Save" class="btn btn-sm btn-success pull-left">
                                <span v-if="materialCategoryId == null">Save</span>  
                                <span v-else>Update</span>  
                                <i class="ace-icon fa fa-arrow-right icon-on-right bigger-110"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>


    <div class="row">
        <div class="col-sm-12">
            <h4>Material Category List</h4>
        </div>
        <div class="col-sm-12 form-inline">
            <div class="form-group">
                <label for="filter" class="sr-only">Filter</label>
                <input type="text" class="form-control" v-model="filter" placeholder="Filter">
            </div>
        </div>
        <div class="col-sm-12">
            <div class="table-responsive">
                <datatable :columns="columns" :data="materialCategories" :filter-by="filter">
                    <template scope="{ row }">
                        <tr>
                            <td>{{ row.serial }}</td>
                            <td>{{ row.name }}</td>
                            <td>{{ row.description }}</td>
                            <td>
                                <?php if($this->session->userdata('accountType') != 'u'){?>
                                <button class="button edit" @click="editMaterialCategory(row)">
                                    <i class="fa fa-pencil"></i>
                                </button>
                                <button class="button" v-bind:class="{active: row.status == 1}" @click="deleteMaterialCategory(row)">
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

<script src="<?php echo base_url();?>assets/js/vue/vue.min.js"></script>
<script src="<?php echo base_url();?>assets/js/vue/axios.min.js"></script>
<script src="<?php echo base_url();?>assets/js/vue/vuejs-datatable.js"></script>
<script src="<?php echo base_url();?>assets/js/vue/vue-select.min.js"></script>

<script>
    Vue.component('v-select', VueSelect.VueSelect);
    new Vue({
        el: '#material-category',
        data() {
            return {
                columns: [
                    { label: 'SL', field: 'serial' },
                    { label: 'Category Name', field: 'name' },
                    { label: 'Description', field: 'description' },
                    { label: 'Action', filterable: false }
                ],
                page: 1,
                per_page: 10,
                filter: '',
                materialCategory: {
                    name: '',
                    description: '',
                },
                selectedMaterialCategory: null,
                materialCategoryId: null,
                materialCategories: [],
            }
        },
        created() {
            this.getMaterialCategories();
        },
        methods: {
            getMaterialCategories() {
                axios.get('/get_material_categories')
                    .then(res => {
                        this.materialCategories = res.data;
                        this.materialCategories.map((mc, i) => {
                            mc.serial = i + 1;
                        })
                    })
            },
            saveMaterialCategory() {
                if(!this.materialCategory.name){
                    alert('The category name field is required');
                    return;
                }

                let materialCategory = {...this.materialCategory}

                let url = '/add_material_category';
                if (this.materialCategoryId) {
                    materialCategory.id = this.materialCategoryId;
                    url = '/update_material_category';
                }

                axios.post(url, materialCategory)
                    .then(res => {
                        let r = res.data;
                        alert(r.message);
                        if (r.success) {
                            this.resetForm();
                            this.getMaterialCategories();
                        }
                    })
            },
            editMaterialCategory(item){
                this.materialCategoryId = item.id;
                this.materialCategory.name = item.name;
                this.materialCategory.description = item.description;
            },
            deleteMaterialCategory(item){
               if (!confirm('Are you sure?')) return;

               axios.post('/delete_material_category', {id: item.id})
                    .then(res => {
                        let r = res.data;
                        alert(r.message);
                        if (r.success) {
                            this.getMaterialCategories();
                        }
                    })
            },
            resetForm() {
                this.materialCategory.name = '';
                this.materialCategory.description = '';
                this.selectedMaterialCategory = null;
                this.materialCategoryId = null;
            }
        }
    })
</script>