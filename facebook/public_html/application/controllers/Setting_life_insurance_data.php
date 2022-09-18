<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Setting_life_insurance_data extends CI_Controller {
	function __construct()
	{
		parent::__construct();
	}
	
	
	public function coop_life_insurance()
	{
		$arr_data = array();

		$x=0;
		$join_arr = array();
		
		$this->paginater_all->type(DB_TYPE);
		$this->paginater_all->select('s_insurance_id,
										s_insurance_year,
										s_insurance_life_premium,
										s_insurance_accident_premium,
										s_insurance_defective_premium,
										updatetime');
		$this->paginater_all->main_table('coop_setting_life_insurance');
		$this->paginater_all->where("");
		$this->paginater_all->page_now(@$_GET["page"]);
		$this->paginater_all->per_page(20);
		$this->paginater_all->page_link_limit(20);
		$this->paginater_all->order_by('s_insurance_id DESC');
		$this->paginater_all->join_arr($join_arr);
		$row = $this->paginater_all->paginater_process();
		//echo"<pre>";print_r($row);exit;
		$paging = $this->pagination_center->paginating($row['page'], $row['num_rows'], $row['per_page'], $row['page_link_limit'], $_GET);//$page_now = 1, $row_total = 1, $per_page = 20, $page_limit = 20
		
		$i = $row['page_start'];

		$arr_data['num_rows'] = $row['num_rows'];
		$arr_data['paging'] = $paging;
		$arr_data['rs'] = $row['data'];
		$arr_data['i'] = $i;
		
		$this->libraries->template('setting_life_insurance_data/coop_life_insurance',$arr_data);
	}
	
	public function coop_life_insurance_save()
	{
		$data_insert = array();	
		$data_insert['s_insurance_year']    = @$_POST["s_insurance_year"];
		$data_insert['s_insurance_life_premium']    = @$_POST["s_insurance_life_premium"];
		$data_insert['s_insurance_accident_premium']    = @$_POST["s_insurance_accident_premium"];
		$data_insert['s_insurance_defective_premium']    = @$_POST["s_insurance_defective_premium"];
		$data_insert['admin_id']    = @$_SESSION['USER_ID'];
		$data_insert['updatetime']    = date('Y-m-d H:i:s');

		$id_edit = @$_POST["s_insurance_id"] ;
		$table = "coop_setting_life_insurance";

		if (empty($id_edit)) {	
		// add		
			$data_insert['createdatetime'] = date('Y-m-d H:i:s');
			$this->db->insert($table, $data_insert);
			$this->center_function->toast("บันทึกข้อมูลเรียบร้อยแล้ว");

		// add
		}else{
		// edit
			$this->db->where('s_insurance_id', $id_edit);
			$this->db->update($table, $data_insert);	
			$this->center_function->toast("แก้ไขข้อมูลเรียบร้อยแล้ว");

		// edit
		}
		
		echo"<script> document.location.href='".PROJECTPATH."/setting_life_insurance_data/coop_life_insurance' </script>"; 

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
	
	function check_year_life_insurance(){
		
		$row = $this->db->select('s_insurance_year')
		->from('coop_setting_life_insurance')
		->where("s_insurance_year='".@$_POST['s_insurance_year']."' AND s_insurance_id <> '".@$_POST['s_insurance_id']."'")
		->get()->result_array();
		$insurance_year = $row[0]['s_insurance_year'];
		if($insurance_year != ''){
			echo false;
		}else{
			echo true;
		}
		exit;
	}
}
