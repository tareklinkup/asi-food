<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MaterialCategory extends CI_Controller{
    public function __construct()
    {
        parent::__construct();
        $this->branch_id = $this->session->userdata('BRANCHid');
        $access = $this->session->userdata('userId');
        if ($access == '') {
            redirect("Login");
        }
        $this->load->model('Model_table', "mt", TRUE);
    }
    
    public function materialCategory() {
        $access = $this->mt->userAccess();
        if(!$access) {
            redirect(base_url());
        }
        $data['title'] = "Material Category";
        $data['content'] = $this->load->view('Administrator/materials/material_category', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }

    public function getMaterialCategories() {

        $materialCategories = $this->db->query("
            SELECT * FROM tbl_material_categories 
            WHERE status = 'a'
            and branch_id = '$this->branch_id'
            ORDER BY id DESC
        ")->result();

        echo json_encode($materialCategories);
    }

    public function addMaterialCategory() {

        $res = ['success' => false, 'message' => ''];

        try{
            $data = json_decode($this->input->raw_input_stream);

            $nameQuery = $this->db->query("select * from tbl_material_categories where name = '$data->name' and branch_id = '$this->branch_id'");
            $nameCount = $nameQuery->num_rows();

            if($nameCount != 0){
                $res = ['success' => false, 'message' => 'Duplicate material category name ' . $data->name];
                echo json_encode($res);
                exit;
            }

            $materialCategory = array(
                'name' => $data->name,
                'description' => $data->description,
                'status' => 'a',
                'added_by' => $this->session->userdata("FullName"),
                'created_at' => date("Y-m-d H:i:s"),
                'branch_id' => $this->branch_id,
            );

            $this->db->insert('tbl_material_categories', $materialCategory);

            $res = ['success'=>true, 'message'=>'Material category added successfully'];

        } catch (Exception $ex){

            $res = ['success' => false, 'message' => $ex->getMessage()];
        }

        echo json_encode($res);
    }

    public function updateMaterialCategory() {

        $res = ['success' => false, 'message' => ''];

        try{
            $data = json_decode($this->input->raw_input_stream);
            $id = $data->id;

            $nameQuery = $this->db->query("select * from tbl_material_categories where name = '$data->name' and id != '$id' and branch_id ='$this->branch_id'");
            $nameCount = $nameQuery->num_rows();

            if($nameCount != 0){
                $res = ['success' => false, 'message' => 'Duplicate material category name ' . $data->name];
                echo json_encode($res);
                exit;
            }

            $materialCategory = array(
                'name' => $data->name,
                'description' => $data->description,
                'status' => 'a',
                'updated_by' => $this->session->userdata("FullName"),
                'updated_at' => date("Y-m-d H:i:s"),
            );

            $this->db->where('id', $id)->update('tbl_material_categories', $materialCategory);

            $res = ['success'=>true, 'message'=>'Material category updated successfully'];

        } catch (Exception $ex){

            $res = ['success' => false, 'message' => $ex->getMessage()];
        }

        echo json_encode($res);
    }

    public function deleteMaterialCategory() {

        $res = ['success' => false, 'message' => ''];

        try{
            $data = json_decode($this->input->raw_input_stream);

            $id = $data->id;

            $this->db->where('id', $id)->update('tbl_material_categories', ['status' => 'd']);

            $res = ['success'=>true, 'message'=>'Material category deleted successfully'];

        } catch (Exception $ex){

            $res = ['success' => false, 'message' => $ex->getMessage()];
        }

        echo json_encode($res);
    }
}