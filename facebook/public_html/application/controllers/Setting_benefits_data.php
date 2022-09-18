<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Setting_benefits_data extends CI_Controller {
	function __construct()
	{
		parent::__construct();
		$this->month_arr = array('1'=>'มกราคม','2'=>'กุมภาพันธ์','3'=>'มีนาคม','4'=>'เมษายน','5'=>'พฤษภาคม','6'=>'มิถุนายน','7'=>'กรกฎาคม','8'=>'สิงหาคม','9'=>'กันยายน','10'=>'ตุลาคม','11'=>'พฤศจิกายน','12'=>'ธันวาคม');

	}
	
	
	public function benefits_type()
	{
		$arr_data = array();
		$id = @$_GET['id'];
		$act = @$_GET['act'];
		$detail_id = @$_GET['detail_id'];
		if(!empty($id)){
			$this->db->select(array('benefits_name'));
			$this->db->from('coop_benefits_type');
			$this->db->where("benefits_id = '".$id."'");
			$rs = $this->db->get()->result_array();
			$arr_data['benefits_type'] = @$rs[0];
				
			if($act == 'detail'){
				$this->db->select(array('*'));
				$this->db->from('coop_benefits_type_detail');
				$this->db->where("benefits_id = '".$id."'");
				$this->db->order_by("start_date DESC");
				$rs_detail = $this->db->get()->result_array();
				$arr_data['rs_detail'] = @$rs_detail;

			}else{
				$this->db->select(array('*'));
				$this->db->from('coop_benefits_type_detail');
				$this->db->where("id = '".$detail_id."'");
				$rs = $this->db->get()->result_array();
				$arr_data['row'] = @$rs[0];
			}
			
			$limit_types = $this->db->select("*")->from("coop_benefits_limit_type")->get()->result_array();
			$arr_data["limit_types"] = $limit_types;

			$limits = $this->db->select("t1.*, t2.prefix, t2.postfix_unit")
								->from("coop_benefits_type_limit as t1")
								->join("coop_benefits_limit_type as t2", "t1.type_code = t2.code", "inner")
								->where("t1.benefit_detail_id = '{$detail_id}'")->get()->result_array();
			$arr_data["limits"] = $limits;

			$choice_raws = $this->db->select("t1.benefit_detail_id, t1.id as choice_id, t1.detail, t1.amount, t1.has_number, t2.id as cond_id, t2.cond_data_code, t2.cond_data_operation, t2.cond_data_value")
								->from("coop_benefits_type_choice as t1")
								->join("coop_benefits_type_choice_cond as t2", "t1.id = t2.type_choice_id", "left")
								->where("t1.benefit_detail_id = '{$detail_id}'")->get()->result_array();
			$choices = array();

			foreach($choice_raws as $choice_raw) {
				$choice = array();
				$choices[$choice_raw["choice_id"]]["benefit_detail_id"] = $choice_raw["benefit_detail_id"];
				$choices[$choice_raw["choice_id"]]["detail"] = $choice_raw["detail"];
				$choices[$choice_raw["choice_id"]]["amount"] = $choice_raw["amount"];
				$choices[$choice_raw["choice_id"]]["has_number"] = $choice_raw["has_number"];
				$cond = array();
				$cond["cond_data_code"] = $choice_raw["cond_data_code"];
				$cond["cond_data_operation"] = $choice_raw["cond_data_operation"];
				$cond["cond_data_value"] = $choice_raw["cond_data_value"];
				$choice["cond"] = $cond;
				$choices[$choice_raw["choice_id"]]["cond"][] = $cond;
			}

			$arr_data["choices"] = $choices;
		}else{	
			$x=0;
			$join_arr = array();
			$this->paginater_all->type(DB_TYPE);
			$this->paginater_all->select('*');
			$this->paginater_all->main_table('coop_benefits_type');
			$this->paginater_all->where("");
			$this->paginater_all->page_now(@$_GET["page"]);
			$this->paginater_all->per_page(20);
			$this->paginater_all->page_link_limit(20);
			$this->paginater_all->order_by('benefits_id DESC');
			$this->paginater_all->join_arr($join_arr);
			$row = $this->paginater_all->paginater_process();

			$paging = $this->pagination_center->paginating($row['page'], $row['num_rows'], $row['per_page'], $row['page_link_limit'], $_GET);//$page_now = 1, $row_total = 1, $per_page = 20, $page_limit = 20

			$i = $row['page_start'];

			$arr_data['num_rows'] = $row['num_rows'];
			$arr_data['paging'] = $paging;
			$arr_data['rs'] = $row['data'];
			$arr_data['i'] = $i;
		}
		$this->libraries->template('setting_benefits_data/benefits_type',$arr_data);
	}

	public function benefits_type_save()
	{
		$data_insert = array();			
		$data_insert['benefits_name']    = @$_POST["benefits_name"];
		$data_insert['start_date']    = $this->center_function->ConvertToSQLDate(@$_POST["start_date"]);
		$data_insert['updatetime']    = date('Y-m-d H:i:s');

		$id_edit = @$_POST["benefits_id"] ;
		$table = "coop_benefits_type";

		if (empty($id_edit)) {	
		// add		
			$data_insert['createdatetime'] = date('Y-m-d H:i:s');
			$this->db->insert($table, $data_insert);
			$this->center_function->toast("บันทึกข้อมูลเรียบร้อยแล้ว");

		// add
		}else{
		// edit
			$this->db->where('benefits_id', $id_edit);
			$this->db->update($table, $data_insert);	
			$this->center_function->toast("แก้ไขข้อมูลเรียบร้อยแล้ว");

		// edit
		}
		
		echo"<script> document.location.href='".PROJECTPATH."/setting_benefits_data/benefits_type' </script>"; 

	}
	
	public function benefits_type_detail_save() {
		$data_insert = array();	
		$id = $_POST["id"] ;
		$id_edit = $_POST["detail_id"] ;
		$type_add = $_POST["type_add"] ;
		$table = "coop_benefits_type_detail";
		$process_datetime = date('Y-m-d H:i:s');

		$data_insert['benefits_id'] = $id;
		$data_insert['benefits_detail'] = $_POST["benefits_detail"];
		$data_insert['choice_type'] = $_POST["choice_type"];
		$data_insert['start_date'] = $this->center_function->ConvertToSQLDate($_POST["start_date"]);
		$data_insert['updatetime'] = date('Y-m-d H:i:s');

		if($type_add == 'add') {
			$data_insert['createdatetime'] = date('Y-m-d H:i:s');
			$this->db->insert($table, $data_insert);
			$id_edit = $this->db->insert_id();
		} else {
			$this->db->where('id', $id_edit);
			$this->db->update($table, $data_insert);	
		}

		//Update limits
		$this->db->where("benefit_detail_id", $id_edit);
		$this->db->delete("coop_benefits_type_limit");

		$limit_vals = $_POST["limit_val"];
		$limit_codes = $_POST["limit_code"];
		$data_limits = array();
		foreach($limit_vals as $key => $limit_val) {
			if(!empty($limit_codes[$key])) {
				$data_limit = array();
				$data_limit["benefit_detail_id"] = $id_edit;
				$data_limit["type_code"] = $limit_codes[$key];
				$data_limit["value"] = $limit_val;
				$data_limit['created_at'] = date('Y-m-d H:i:s');
				$data_limit['updated_at'] = date('Y-m-d H:i:s');
				$data_limits[] = $data_limit;
			}
		}

		if(!empty($data_limits)) $this->db->insert_batch('coop_benefits_type_limit', $data_limits);

		//Update Choices
		$this->db->where("benefit_detail_id", $id_edit);
		$this->db->delete("coop_benefits_type_choice");

		$data_choices = array();
		foreach($_POST["cond"] as $cond) {
			$data_choice = array();
			$data_choice["benefit_detail_id"] = $id_edit;
			$data_choice["detail"] = $cond["detail"];
			$data_choice["amount"] = $cond["benefit_amount"];
			$data_choice["has_number"] = $cond["number_add"];
			$data_choice['created_at'] = date('Y-m-d H:i:s');
			$data_choice['updated_at'] = date('Y-m-d H:i:s');
			$this->db->insert("coop_benefits_type_choice", $data_choice);
			$choice_id = $this->db->insert_id();

			foreach($cond["data"] as $data) {
				$choice_cond = array();
				$choice_cond['type_choice_id'] = $choice_id;
				$choice_cond['cond_data_code'] = $data["cond_data_add"];
				$choice_cond['cond_data_operation'] = $data["operation"];
				$choice_cond['cond_data_value'] = $data["data_val"];
				$choice_cond['created_at'] = date('Y-m-d H:i:s');
				$this->db->insert("coop_benefits_type_choice_cond", $choice_cond);
			}
		}

		$this->center_function->toast("บันทึกข้อมูลเรียบร้อยแล้ว");
		echo"<script> document.location.href='".PROJECTPATH."/setting_benefits_data/benefits_type?act=detail&id={$id}' </script>"; 
	}
	
	function del_coop_data(){	
		$table = @$_POST['table'];
		$table_sub = @$_POST['table_sub'];
		$id = @$_POST['id'];
		$field = @$_POST['field'];


		if (!empty($table_sub)) {
			$this->db->where($field, $id );
			$this->db->delete($table_sub);	
        }

		$this->db->where($field, $id );
		$this->db->delete($table);
		$this->center_function->toast("ลบเรียบร้อยแล้ว");
		echo true;
		
	}
	
	function check_benefits_type_detail(){	
		$id = @$_POST['id'];
		$this->db->select(array('*'));
		$this->db->from('coop_benefits_type_detail');
		$this->db->where("benefits_id = '{$id}'");
		$rs = $this->db->get()->result_array();
		$row = @$rs[0];
		if(@$row['benefits_id']){
			echo false;
		}else{
			echo true;
		}		
		exit;
	}
	
	function check_date_detail(){
		$start_date = $this->center_function->ConvertToSQLDate(@$_POST["start_date"]);
		$id = @$_POST["id"];
		$detail_id = @$_POST["detail_id"];
		
		if(@$detail_id){
			$where = " AND id <> {$detail_id}";
		}else{
			$where = "";
		}
		
		$this->db->select(array('*'));
		$this->db->from('coop_benefits_type_detail');
		$this->db->where("start_date = '{$start_date}' AND benefits_id = '{$id}' {$where}");
		$rs = $this->db->get()->result_array();
		$row = @$rs[0]; 
	
		if(@$row['start_date']){
			echo false;
		}else{
			echo true;
		}
		exit;
	}

	function check_request_detail_exists() {
		$detail_id = $_POST["detail_id"];
		$requests = $this->db->select("*")->from("coop_benefits_request")->where("benefits_detail_id = '".$detail_id."'")->get()->result_array();
		if(!empty($requests)) {
			echo true;
		} else {
			echo false;
		}
	}

}
