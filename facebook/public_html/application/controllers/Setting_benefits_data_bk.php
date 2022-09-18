<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Setting_benefits_data extends CI_Controller {
	function __construct()
	{
		parent::__construct();
	}
	
	public function benefits_member()
	{
		$arr_data = array();
		$id = @$_GET['id'];
		if(!empty($id)){
			$this->db->select(array('*'));
			$this->db->from('coop_benefits_member');
			$this->db->where("benefits_member_id = '{$id}'");
			$rs = $this->db->get()->result_array();
			$arr_data['row'] = @$rs[0]; 	
		}else{	
			$x=0;
			$join_arr = array();
			
			$this->paginater_all->type(DB_TYPE);
			$this->paginater_all->select('*');
			$this->paginater_all->main_table('coop_benefits_member');
			$this->paginater_all->where("");
			$this->paginater_all->page_now(@$_GET["page"]);
			$this->paginater_all->per_page(10);
			$this->paginater_all->page_link_limit(20);
			$this->paginater_all->order_by('benefits_member_id DESC');
			$this->paginater_all->join_arr($join_arr);
			$row = $this->paginater_all->paginater_process();
			//echo $this->db->last_query();exit;
			//echo"<pre>";print_r($row);exit;
			$paging = $this->pagination_center->paginating($row['page'], $row['num_rows'], $row['per_page'], $row['page_link_limit'], $_GET);//$page_now = 1, $row_total = 1, $per_page = 20, $page_limit = 20
			
			$i = $row['page_start'];

			$arr_data['num_rows'] = $row['num_rows'];
			$arr_data['paging'] = $paging;
			$arr_data['rs'] = $row['data'];
			$arr_data['i'] = $i;
		}
		$this->libraries->template('setting_benefits_data/benefits_member',$arr_data);
	}
	
	public function benefits_member_save()
	{
		$data_insert = array();			
		$data_insert['benefits_member_name']    = @$_POST["benefits_member_name"];
		$data_insert['start_date']    = $this->center_function->ConvertToSQLDate(@$_POST["start_date"]);
		$data_insert['updatetime']    = date('Y-m-d H:i:s');

		$type_add = @$_POST["type_add"] ;
		$id_edit = @$_POST["id"] ;
			

		$table = "coop_benefits_member";

		if ($type_add == 'add') {	
		// add		
			$data_insert['createdatetime'] = date('Y-m-d H:i:s');
			$this->db->insert($table, $data_insert);
			$this->center_function->toast("บันทึกข้อมูลเรียบร้อยแล้ว");

		// add
		}else{
		// edit
			$this->db->where('benefits_member_id', $id_edit);
			$this->db->update($table, $data_insert);	
			$this->center_function->toast("แก้ไขข้อมูลเรียบร้อยแล้ว");

		// edit
		}
		
		echo"<script> document.location.href='".PROJECTPATH."/setting_benefits_data/benefits_member' </script>"; 

	}
	
	public function benefits_heir()
	{
		$arr_data = array();
		$id = @$_GET['id'];
		if(!empty($id)){
			$this->db->select(array('*'));
			$this->db->from('coop_benefits_heir');
			$this->db->where("benefits_heir_id = '{$id}'");
			$rs = $this->db->get()->result_array();
			$arr_data['row'] = @$rs[0]; 	
		}else{	
			$x=0;
			$join_arr = array();
			
			$this->paginater_all->type(DB_TYPE);
			$this->paginater_all->select('*');
			$this->paginater_all->main_table('coop_benefits_heir');
			$this->paginater_all->where("");
			$this->paginater_all->page_now(@$_GET["page"]);
			$this->paginater_all->per_page(10);
			$this->paginater_all->page_link_limit(20);
			$this->paginater_all->order_by('benefits_heir_id DESC');
			$this->paginater_all->join_arr($join_arr);
			$row = $this->paginater_all->paginater_process();
			//echo $this->db->last_query();exit;
			//echo"<pre>";print_r($row);exit;
			$paging = $this->pagination_center->paginating($row['page'], $row['num_rows'], $row['per_page'], $row['page_link_limit'], $_GET);//$page_now = 1, $row_total = 1, $per_page = 20, $page_limit = 20
			
			$i = $row['page_start'];

			$arr_data['num_rows'] = $row['num_rows'];
			$arr_data['paging'] = $paging;
			$arr_data['rs'] = $row['data'];
			$arr_data['i'] = $i;
		}
		$this->libraries->template('setting_benefits_data/benefits_heir',$arr_data);
	}
	
	public function benefits_heir_save()
	{
		$data_insert = array();			
		$data_insert['benefits_heir_detail']    = @$_POST["benefits_heir_detail"];
		$data_insert['start_date']    = $this->center_function->ConvertToSQLDate(@$_POST["start_date"]);
		$data_insert['updatetime']    = date('Y-m-d H:i:s');

		$type_add = @$_POST["type_add"] ;
		$id_edit = @$_POST["id"] ;
			

		$table = "coop_benefits_heir";

		if ($type_add == 'add') {	
		// add		
			$data_insert['createdatetime'] = date('Y-m-d H:i:s');
			$this->db->insert($table, $data_insert);
			$this->center_function->toast("บันทึกข้อมูลเรียบร้อยแล้ว");

		// add
		}else{
		// edit
			$this->db->where('benefits_heir_id', $id_edit);
			$this->db->update($table, $data_insert);	
			$this->center_function->toast("แก้ไขข้อมูลเรียบร้อยแล้ว");

		// edit
		}
		
		echo"<script> document.location.href='".PROJECTPATH."/setting_benefits_data/benefits_heir' </script>"; 

	}
	
	
	public function benefits_type()
	{
		$arr_data = array();
		$id = @$_GET['id'];
		if(!empty($id)){
			$this->db->select(array('*'));
			$this->db->from('coop_benefits_type');
			$this->db->where("benefits_id = '{$id}'");
			$rs = $this->db->get()->result_array();
			$arr_data['row'] = @$rs[0]; 	
		}else{	
			$x=0;
			$join_arr = array();
			
			$this->paginater_all->type(DB_TYPE);
			$this->paginater_all->select('*');
			$this->paginater_all->main_table('coop_benefits_type');
			$this->paginater_all->where("");
			$this->paginater_all->page_now(@$_GET["page"]);
			$this->paginater_all->per_page(10);
			$this->paginater_all->page_link_limit(20);
			$this->paginater_all->order_by('benefits_id DESC');
			$this->paginater_all->join_arr($join_arr);
			$row = $this->paginater_all->paginater_process();
			//echo $this->db->last_query();exit;
			//echo"<pre>";print_r($row);exit;
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
	
	public function benefits_type_detail_save()
	{
		$data_insert = array();			
		$data_insert['benefits_detail']    = @$_POST["benefits_detail"];
		$data_insert['start_date']    = $this->center_function->ConvertToSQLDate(@$_POST["start_date"]);
		$data_insert['updatetime']    = date('Y-m-d H:i:s');

		$id_edit = @$_POST["id"] ;

		$table = "coop_benefits_type";

		$this->db->where('benefits_id', $id_edit);
		$this->db->update($table, $data_insert);	
		$this->center_function->toast("บันทึกข้อมูลเรียบร้อยแล้ว");
		
		echo"<script> document.location.href='".PROJECTPATH."/setting_benefits_data/benefits_type' </script>"; 

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

}
