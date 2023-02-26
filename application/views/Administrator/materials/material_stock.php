<style>
    .v-select {
        margin-bottom: 5px;
    }

    .v-select .dropdown-toggle {
        padding: 0px;
    }

    .v-select input[type=search],
    .v-select input[type=search]:focus {
        margin: 0px;
    }

    .v-select .vs__selected-options {
        overflow: hidden;
        flex-wrap: nowrap;
    }

    .v-select .selected-tag {
        margin: 2px 0px;
        white-space: nowrap;
        position: absolute;
        left: 0px;
    }

    .v-select .vs__actions {
        margin-top: -5px;
    }

    .v-select .dropdown-menu {
        width: auto;
        overflow-y: auto;
    }
</style>
<div id="materialStock">
    <div class="row">
        <div class="col-xs-12 col-md-12 col-lg-12" style="border-bottom:1px #ccc solid;margin-bottom:5px;">
            <div class="form-group" style="margin-top:10px;">
                <label class="col-sm-1 col-sm-offset-1 control-label no-padding-right"> Select Type </label>
                <div class="col-sm-2">
                    <v-select v-bind:options="searchTypes" v-model="selectedSearchType" label="text" v-on:input="onChangeSearchType"></v-select>
                </div>
            </div>
    
            <div class="form-group" style="margin-top:10px;" v-if="selectedSearchType.value == 'category'">
                <div class="col-sm-2" style="margin-left:15px;">
                    <v-select v-bind:options="categories" v-model="selectedCategory" label="name"></v-select>
                </div>
            </div>
    
            <div class="form-group" style="margin-top:10px;" v-if="selectedSearchType.value == 'product'">
                <div class="col-sm-2" style="margin-left:15px;">
                    <v-select v-bind:options="products" v-model="selectedProduct" label="display_text"></v-select>
                </div>
            </div>

            <div class="form-group" style="margin-top:10px;" v-if="selectedSearchType.value != 'current'">
				<div class="col-sm-2" style="margin-left:15px;">
					<input type="date" class="form-control" v-model="date">
				</div>
			</div>
    
            <div class="form-group">
                <div class="col-sm-2"  style="margin-left:15px;">
                    <input type="button" class="btn btn-primary" value="Show Report" v-on:click="getMaterialStock" style="margin-top:0px;border:0px;height:28px;">
                </div>
            </div>
        </div>
    </div>
    <div class="row" v-if="searchType != null" style="display:none" v-bind:style="{display: searchType == null ? 'none' : ''}">
		<div class="col-md-12" style="margin-bottom: 5px;">
			<a href="" v-on:click.prevent="print"><i class="fa fa-print"></i> Print</a>
			<button style="float: right;" id="btnExport" onclick="fnExcelReport();"> EXPORT </button>
		</div>
	</div>
    <div class="row">
        <div class="col-sm-12">
            <div class="table-responsive" id="stockContent">
                <table id="headerTable" class="table table-bordered" style="display:none" v-bind:style="{display: searchType != null ? '' : 'none'}">
                    <thead>
                        <tr>
                            <th>Sl.</th>
                            <th>Code</th>
                            <th>Material Name</th>
                            <th>Category</th>
                            <th>Total Purchased</th>
                            <th>Used in Production</th>
                            <th>Damaged</th>
                            <th>Current Stock</th>
                            <th>Stock Value</th>
                        </tr>
                    </thead>
                    <tbody style="display:none;" v-bind:style="{display:stock.length > 0 ? '' : 'none'}">
                        <tr v-for="(material, sl) in stock">
                            <td>{{ sl+1 }}</td>
                            <td>{{ material.code }}</td>
                            <td>{{ material.name }}</td>
                            <td>{{ material.category_name }}</td>
                            <td>{{ material.purchased_quantity }} {{ material.unit_name}}</td>
                            <td>{{ material.production_quantity }} {{ material.unit_name}}</td>
                            <td>{{ material.damage_quantity }} {{ material.unit_name}}</td>
                            <td>{{ material.stock_quantity }} {{ material.unit_name}}</td>
                            <td>{{ material.stock_value }}</td>
                        </tr>
                        <tr>
                            <th colspan="8" style="text-align:right;">Total=</th>
                            <th>{{ stock.reduce((prev, curr) => {
                                    return prev + parseFloat(curr.stock_value)
                                },0).toFixed(2) }}
                            </th>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="<?php echo base_url();?>assets/js/vue/vue.min.js"></script>
<script src="<?php echo base_url();?>assets/js/vue/axios.min.js"></script>
<script src="<?php echo base_url();?>assets/js/vue/vue-select.min.js"></script>
<script src="<?php echo base_url();?>assets/js/moment.min.js"></script>
<script type="text/javascript">
	function fnExcelReport()
	{
	    var tab_text="<table border='2px'><tr bgcolor='#87AFC6'>";
	    var textRange; var j=0;
	    tab = document.getElementById('headerTable'); // id of table

	    for(j = 0 ; j < tab.rows.length ; j++) 
	    {     
	        tab_text=tab_text+tab.rows[j].innerHTML+"</tr>";
	        //tab_text=tab_text+"</tr>";
	    }

	    tab_text=tab_text+"</table>";
	    tab_text= tab_text.replace(/<A[^>]*>|<\/A>/g, "");//remove if u want links in your table
	    tab_text= tab_text.replace(/<img[^>]*>/gi,""); // remove if u want images in your table
	    tab_text= tab_text.replace(/<input[^>]*>|<\/input>/gi, ""); // reomves input params

	    var ua = window.navigator.userAgent;
	    var msie = ua.indexOf("MSIE "); 

	    if (msie > 0 || !!navigator.userAgent.match(/Trident.*rv\:11\./))      // If Internet Explorer
	    {
	        txtArea1.document.open("txt/html","replace");
	        txtArea1.document.write(tab_text);
	        txtArea1.document.close();
	        txtArea1.focus(); 
	        sa=txtArea1.document.execCommand("SaveAs",true,"sales_record.xls");
	    }  
	    else                 //other browser not tested on IE 11
	        sa = window.open('data:application/vnd.ms-excel,' + encodeURIComponent(tab_text));  

	    return (sa);
	}
</script>
<script>
    Vue.component('v-select', VueSelect.VueSelect);
    new Vue({
        el: '#materialStock',
        data(){
            return {
                searchTypes: [
					{text: 'Current Stock', value: 'current'},
                    {text: 'Total Stock', value: 'total'},
					{text: 'Category Wise Stock', value: 'category'},
					{text: 'Material Wise Stock', value: 'product'},
				],
				selectedSearchType: {
					text: 'select',
					value: ''
				},
				searchType: null,
				categories: [],
				selectedCategory: null,
				products: [],
				selectedProduct: null,
                selectionText: '',
                date: moment().format('YYYY-MM-DD'),
				stock: [],
				totalStockValue: 0.00
            }
        },
        filters: {
			decimal(value) {
				return value == null ? '0.00' : parseFloat(value).toFixed(2);
			}
		},
        methods: {
            onChangeSearchType(){
				if(this.selectedSearchType.value == 'category' && this.categories.length == 0){
					this.getCategories();
				} else if(this.selectedSearchType.value == 'product' && this.products.length == 0){
					this.getProducts();
				}
			},
			getCategories(){
				axios.get('/get_material_categories').then(res => {
					this.categories = res.data;
				})
			},
			getProducts(){
				axios.get('/get_materials').then(res => {
					this.products =  res.data;
				})
			},
            getMaterialStock(){

                this.searchType = this.selectedSearchType.value;
				let url = '';
				let parameters = {};

                if(this.searchType == 'current'){
					url = '/get_material_stock';
				} else {
					url = '/get_material_total_stock';
					parameters.date = this.date;
				}

				this.selectionText = "";

                if(this.searchType == 'category' && this.selectedCategory == null){
					alert('Select a category');
					return;
				} else if(this.searchType == 'category' && this.selectedCategory != null) {
					parameters.categoryId = this.selectedCategory.id;
					this.selectionText = "Category: " + this.selectedCategory.name;
				}

				if(this.searchType == 'product' && this.selectedProduct == null){
					alert('Select a Material');
					return;
				} else if(this.searchType == 'product' && this.selectedProduct != null) {
					parameters.productId = this.selectedProduct.material_id;
					this.selectionText = "product: " + this.selectedProduct.display_text;
				}

                axios.post(url, parameters).then(res => {
					if(this.searchType == 'current'){
						this.stock = res.data.filter((pro)=> pro.stock_quantity != 0);
					}else{
						this.stock = res.data;
					}
				})
            },
            async print(){
				let reportContent = `
					<div class="container">
						<h4 style="text-align:center">${this.selectedSearchType.text} Report</h4 style="text-align:center">
						<h6 style="text-align:center">${this.selectionText}</h6>
					</div>
					<div class="container">
						<div class="row">
							<div class="col-xs-12">
								${document.querySelector('#stockContent').innerHTML}
							</div>
						</div>
					</div>
				`;

				var reportWindow = window.open('', 'PRINT', `height=${screen.height}, width=${screen.width}, left=0, top=0`);
				reportWindow.document.write(`
					<?php $this->load->view('Administrator/reports/reportHeader.php');?>
				`);

				reportWindow.document.body.innerHTML += reportContent;

				reportWindow.focus();
				await new Promise(resolve => setTimeout(resolve, 1000));
				reportWindow.print();
				reportWindow.close();
			}
        }
    })
</script>