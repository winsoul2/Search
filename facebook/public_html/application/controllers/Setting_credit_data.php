<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Setting_credit_data extends CI_Controller {
	function __construct()
	{
		parent::__construct();
	}
	
	public function coop_term_of_loan(){
		$arr_data = array();
		$id = @$_GET['id'];
		if(@$id){
			$this->db->select(array('*'));
			$this->db->from('coop_term_of_loan');
			$this->db->join('coop_loan_name','coop_term_of_loan.type_id = coop_loan_name.loan_name_id','inner');
			$this->db->where("id  = '{$id}' ");
			$rs = $this->db->get()->result_array();
			$arr_data['row'] = @$rs[0];
			
			$this->db->select(array('*'));
			$this->db->from('coop_loan_name');
			$this->db->where("loan_type_id  = '".$arr_data['row']['loan_type_id']."' ");
			$arr_data['loan_name_choose'] = $this->db->get()->result_array();
			
			$this->db->select(array('*'));
			$this->db->from('coop_loan_name');
			$this->db->where("loan_type_id  = '".$arr_data['row']['close_loan_type_id']."' ");
			$arr_data['close_loan_name_choose'] = $this->db->get()->result_array();
		}else{	
			$x=0;
			$join_arr = array();
			$join_arr[$x]['table'] = 'coop_loan_name';
			$join_arr[$x]['condition'] = 'coop_term_of_loan.type_id = coop_loan_name.loan_name_id';
			$join_arr[$x]['type'] = 'inner';
			$x++;
			$join_arr[$x]['table'] = 'coop_loan_type';
			$join_arr[$x]['condition'] = 'coop_loan_type.id = coop_loan_name.loan_type_id';
			$join_arr[$x]['type'] = 'inner';
			
			$where = '1=1';
			if(@$_GET['type_id']!=''){
				$where .= " AND coop_loan_name.loan_type_id = '".$_GET['type_id']."'";
			}
			
			$this->paginater_all->type(DB_TYPE);
			$this->paginater_all->select('coop_term_of_loan.*, coop_loan_name.loan_name, coop_loan_name.loan_name_description, coop_loan_type.loan_type');
			$this->paginater_all->main_table('coop_term_of_loan');
			$this->paginater_all->where($where);
			$this->paginater_all->page_now(@$_GET["page"]);
			$this->paginater_all->per_page(20);
			$this->paginater_all->page_link_limit(20);
			$this->paginater_all->order_by('type_id ASC, start_date DESC');
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
		$this->db->select(array('*'));
		$this->db->from('coop_loan_type');
		$arr_data['loan_type'] = $this->db->get()->result_array();
		
		$this->db->select(array('*'));
		$this->db->from('coop_loan_name');
		$this->db->join('coop_loan_type','coop_loan_name.loan_type_id = coop_loan_type.id','inner');
		$this->db->order_by('loan_type_id ASC');
		$arr_data['loan_name'] = $this->db->get()->result_array();
		$arr_data['arr_loan_name_status'] = array('0'=>'ไม่แสดง','1'=>'แสดง');
		$arr_data['arr_loan_type_status'] = array('0'=>'ไม่แสดง','1'=>'แสดง');
		$this->libraries->template('setting_credit_data/coop_term_of_loan',$arr_data);
	}
	
	public function coop_term_of_loan_save(){
		//echo"<pre>";print_r($_POST);echo"</pre>";exit;
		$data_insert = array();
		//$data_insert_sub = array();		
		
		if(@$_POST["start_date"]!=''){
			$data_insert['start_date'] = $this->center_function->ConvertToSQLDate($_POST['start_date']);
		}
		$data_insert['type_id'] = @$_POST["loan_name_id"];
		
		$this->db->select('loan_name');
		$this->db->from('coop_loan_name');
		$this->db->where("loan_name_id  = '".$_POST["loan_name_id"]."' ");
		$row_loan_name = $this->db->get()->result_array();
		
		$data_insert['type_name'] = @$row_loan_name[0]['loan_name'];
		$data_insert['less_than_multiple_salary']  = @$_POST["less_than_multiple_salary"];
		$data_insert['least_share_percent_for_loan']  = @$_POST["least_share_percent_for_loan"];
		$data_insert['min_month_member']  = @$_POST["min_month_member"];
		$data_insert['max_period']  = @$_POST["max_period"];
		$data_insert['min_installment_percent']	= @$_POST["min_installment_percent"];
		$data_insert['interest_rate']  = @$_POST["interest_rate"];
		$data_insert['num_guarantee']  = @$_POST["num_guarantee"];
		$data_insert['percent_share_guarantee']  = @$_POST["percent_share_guarantee"];
		$data_insert['percent_fund_quarantee']  = @$_POST["percent_fund_quarantee"];
		if(@$_POST["prefix_code"] != ''){
			$prefix_code = str_replace('.','',$_POST["prefix_code"]);
			$prefix_code = $prefix_code.".";
		}else{
			$prefix_code = '';
		}
		$data_insert['prefix_code']	= @$prefix_code;	
		$data_insert['credit_limit']  = @$_POST["credit_limit"];
		$data_insert['credit_limit_share_percent']  = @$_POST["credit_limit_share_percent"];
		$data_insert['min_share_fund_money']  = @$_POST["min_share_fund_money"];
		$data_insert['min_month_share_period']  = @$_POST["min_month_share_period"];
		$data_insert['min_share_total']  = @$_POST["min_share_total"];	

		$data_insert['share_guarantee']  = @$_POST["share_guarantee"];
		$data_insert['person_guarantee']  = @$_POST["person_guarantee"];
		$data_insert['age_limit']  = @$_POST["age_limit"];
		$data_insert['no_cal_dividend_average']  = @$_POST["no_cal_dividend_average"];
		$data_insert['guarantee_interest']  = @$_POST["guarantee_interest"];
		$data_insert['least_share_or_blue_acc_percent']  = @$_POST["least_share_or_blue_acc_percent"];
		$data_insert['money_use_balance']  = @$_POST["money_use_balance"];
		
		$data_insert['percent_guarantee']  = @$_POST["percent_guarantee"];
		$data_insert['percent_guarantee_option']  = @$_POST["percent_guarantee_option"];
		$data_insert['loan_fee']  = @$_POST["loan_fee"];
		$data_insert['loan_fee_option']  = @$_POST["loan_fee_option"];
		$data_insert['real_estate_guarantee']  = @$_POST["real_estate_guarantee"];
		$data_insert['life_insurance']  = @$_POST["life_insurance"];
		$data_insert['min_principal_amount']  = @$_POST["min_principal_amount"];
		$data_insert['prev_loan_period_min']  = @$_POST["prev_loan_period_min"];
		$data_insert['prev_loan_amount_min']  = @$_POST["prev_loan_amount_min"];
		$data_insert['deposit_guarantee']  = @$_POST["deposit_guarantee"];
		$data_insert['share_and_deposit_guarantee']  = @$_POST["share_and_deposit_guarantee"];
		$data_insert['close_loan_type_id']  = @$_POST["close_loan_type_id"];
		$data_insert['close_loan_name_id']  = @$_POST["close_loan_name_id"];

		//$data_insert_sub['loan_type'] = @$_POST["type_name"];
		//echo"<pre>";print_r($data_insert);exit;
		$type_add = @$_POST["type_add"] ;
		$id_edit = @$_POST["id"];
		
		$table = "coop_term_of_loan";
		//$table_sub = "coop_loan_type";

		if ($type_add == 'add') {
			/*$this->db->select('MAX(type_id) as _max');
			$this->db->from('coop_term_of_loan');
			$max = $this->db->get()->result_array();
			$type_id = @$max[0]["_max"] + 1 ;
			
			$data_insert['type_id']  = @$type_id;	*/	
			// add
			$this->db->insert($table, $data_insert);

			//$data_insert_sub['id'] = @$type_id;
			//$this->db->insert($table_sub, $data_insert_sub);

			$this->center_function->toast("บันทึกข้อมูลเรียบร้อยแล้ว");
			// add		
		}else{
			// edit
			$this->db->where('id', $id_edit);
			$this->db->update($table, $data_insert);

			
			$this->db->select('type_id');
			$this->db->from('coop_term_of_loan');
			$this->db->where("id  = '{$id_edit}' ");
			$re = $this->db->get()->result_array();
			$type_id = @$re[0]["type_id"];

			$this->db->where('id', $type_id);
			//$this->db->update($table_sub, $data_insert_sub);

			$this->center_function->toast("แก้ไขข้อมูลเรียบร้อยแล้ว");	
			// edit			
		}		
		echo"<script> document.location.href='".PROJECTPATH."/setting_credit_data/coop_term_of_loan' </script>";            
	}
	
	function del_coop_credit_data(){	
		$table = @$_POST['table'];
		$id = @$_POST['id'];
		$field = @$_POST['field'];

		$this->db->select('type_id');
		$this->db->from('coop_term_of_loan');
		$this->db->where("id  = '{$id}' ");
		$re = $this->db->get()->result_array();
		$type_id = @$re[0]["type_id"];

		$this->db->where($field, $id );
		$this->db->delete($table);

		$this->db->where('id', $type_id );
		$this->db->delete('coop_loan_type');

		$this->center_function->toast("ลบเรียบร้อยแล้ว");
		echo true;
		
	}

	public function coop_loan_reason(){
		$arr_data = array();
		$id = @$_GET['id'];
		if(@$id){
			$this->db->select(array('*'));
			$this->db->from('coop_loan_reason');
			$this->db->where("loan_reason_id  = '{$id}' ");
			$rs = $this->db->get()->result_array();
			$arr_data['row'] = @$rs[0];
		}else{	
			$x=0;
			$join_arr = array();
			
			$this->paginater_all->type(DB_TYPE);
			$this->paginater_all->select('*');
			$this->paginater_all->main_table('coop_loan_reason');
			$this->paginater_all->where("");
			$this->paginater_all->page_now(@$_GET["page"]);
			$this->paginater_all->per_page(20);
			$this->paginater_all->page_link_limit(20);
			$this->paginater_all->order_by('loan_reason_id ASC');
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
		$this->libraries->template('setting_credit_data/coop_loan_reason',$arr_data);
	}
	
	public function coop_loan_reason_save(){
		$data_insert = array();
		$data_insert['loan_reason']  = @$_POST["loan_reason"];

		$type_add = @$_POST["type_add"] ;
		$id_edit = @$_POST["loan_reason_id"];
		
		$table = "coop_loan_reason";

		if (@$_POST['loan_reason_id']!='') {	
			// edit
			$this->db->where('loan_reason_id', $id_edit);
			$this->db->update($table, $data_insert);
			$this->center_function->toast("แก้ไขข้อมูลเรียบร้อยแล้ว");	
			// edit		
				
		}else{
			// add
			$this->db->insert($table, $data_insert);
			$this->center_function->toast("บันทึกข้อมูลเรียบร้อยแล้ว");
			// add			
		}	

		echo"<script> document.location.href='".PROJECTPATH."/setting_credit_data/coop_loan_reason' </script>";            
	}
	
	function del_coop_reason_data(){	
		$table = @$_POST['table'];
		$id = @$_POST['id'];
		$field = @$_POST['field'];

		$this->db->where($field, $id );
		$this->db->delete($table);

		$this->center_function->toast("ลบเรียบร้อยแล้ว");
		echo true;
		
	}
	
	function check_use_type(){
		$this->db->select('*');
		$this->db->from('coop_loan_name');
		$this->db->where("loan_type_id = '".$_POST['id']."'");
		$row = $this->db->get()->result_array();
		if(!empty($row)){
			echo "error";
		}else{
			echo "success";
		}
		exit;
	}
	function check_use_name(){
		$this->db->select('*');
		$this->db->from('coop_term_of_loan');
		$this->db->where("type_id = '".$_POST['id']."'");
		$row = $this->db->get()->result_array();
		if(!empty($row)){
			echo "error";
		}else{
			echo "success";
		}
		exit;
	}
	
	function del_loan_type(){	
		$this->db->where('id', $_GET['id'] );
		$this->db->delete('coop_loan_type');
		$this->center_function->toast("ลบข้อมูลเรียบร้อยแล้ว");
		echo"<script> document.location.href='".PROJECTPATH."/setting_credit_data/coop_term_of_loan' </script>";       
		
	}
	
	function del_loan_name(){	
		$this->db->where('loan_name_id', $_GET['id'] );
		$this->db->delete('coop_loan_name');
		$this->center_function->toast("ลบข้อมูลเรียบร้อยแล้ว");
		echo"<script> document.location.href='".PROJECTPATH."/setting_credit_data/coop_term_of_loan' </script>";       
		
	}
	
	function coop_loan_type_save(){
		//echo"<pre>";print_r($_POST);exit;
		$data_insert = array();
		$data_insert['loan_type']  = @$_POST['loan_type'];	
		$data_insert['loan_type_status']  = (@$_POST['loan_type_status'] == '')?'0':@$_POST['loan_type_status'];	
		if(@$_POST['loan_type_id']!=''){			
			$this->db->where('id', @$_POST['loan_type_id']);
			$this->db->update('coop_loan_type', $data_insert);
		}else{
			$this->db->insert('coop_loan_type', $data_insert);
		}
		$this->center_function->toast("บันทึกข้อมูลเรียบร้อยแล้ว");
		echo"<script> document.location.href='".PROJECTPATH."/setting_credit_data/coop_term_of_loan' </script>";       
	}
	
	function coop_loan_name_save(){
		//echo"<pre>";print_r($_POST);exit;
		$data_insert = array();
		$data_insert['loan_name']  = @$_POST['loan_name'];	
		$data_insert['loan_name_description']  = @$_POST['loan_name_description'];	
		$data_insert['loan_type_id']  = @$_POST['loan_type_id'];	
		$data_insert['loan_name_status']  = (@$_POST['loan_name_status'] == '')?'0':@$_POST['loan_name_status'];	

		if(@$_POST['loan_name_id']!=''){
			$this->db->where('loan_name_id', @$_POST['loan_name_id']);
			$this->db->update('coop_loan_name', $data_insert);
		}else{
			$this->db->insert('coop_loan_name', $data_insert);
		}
		$this->center_function->toast("บันทึกข้อมูลเรียบร้อยแล้ว");
		echo"<script> document.location.href='".PROJECTPATH."/setting_credit_data/coop_term_of_loan' </script>";       
	}
	
	function change_loan_type(){
		$this->db->select('*');
		$this->db->from('coop_loan_name');
		$this->db->where("loan_type_id = '".$_POST['type_id']."'");
		$row = $this->db->get()->result_array();
		
		$text_return = "<option value=''>เลือกชื่อเงินกู้</option>";
		foreach($row as $key => $value){
			$text_return .= "<option value='".$value['loan_name_id']."'>".$value['loan_name']." ".$value['loan_name_description']."</option>";
		}
		echo $text_return;
		exit;
	}
	
	function coop_loan_guarantee(){
		if(@$_GET['action']=='del'){
			$this->db->where('id', $_GET['id'] );
			$this->db->delete('coop_guarantee_setting');
			$this->center_function->toast("ลบข้อมูลเรียบร้อยแล้ว");	
			echo"<script> document.location.href='".PROJECTPATH."/setting_credit_data/coop_loan_guarantee' </script>";       
		}
		if(@$_POST){
			$data_insert = array();
			$data_insert['salary_start']  = @$_POST["salary_start"];
			$data_insert['salary_end']  = @$_POST["salary_end"];
			$data_insert['guarantee_count']  = @$_POST["guarantee_count"];
			if($_POST['id']!=''){
				$this->db->where('id', $_POST['id']);
				$this->db->update('coop_guarantee_setting', $data_insert);
				$this->center_function->toast("แก้ไขข้อมูลเรียบร้อยแล้ว");	
			}else{
				$this->db->insert('coop_guarantee_setting', $data_insert);
				$this->center_function->toast("บันทึกข้อมูลเรียบร้อยแล้ว");	
			}
			echo"<script> document.location.href='".PROJECTPATH."/setting_credit_data/coop_loan_guarantee' </script>";       
		}
		$arr_data = array();
		
		$this->db->select('*');
		$this->db->from('coop_guarantee_setting');
		$this->db->order_by('salary_start ASC');
		$row = $this->db->get()->result_array();
		$arr_data['row'] = $row;
		
		$this->libraries->template('setting_credit_data/coop_loan_guarantee',$arr_data);
	}
	
	function coop_loan_atm_setting(){
		$arr_data = array();
		$id = @$_GET['id'];
		if(@$id){
			$this->db->select(array('*'));
			$this->db->from('coop_loan_atm_setting_template');
			$this->db->where("run_id  = '{$id}' ");
			$arr_data['row'] = $this->db->get()->row_array();
		}else{	
			$where = '1=1';
			
			$this->paginater_all->type(DB_TYPE);
			$this->paginater_all->select('*');
			$this->paginater_all->main_table('coop_loan_atm_setting_template');
			$this->paginater_all->where($where);
			$this->paginater_all->page_now(@$_GET["page"]);
			$this->paginater_all->per_page(20);
			$this->paginater_all->page_link_limit(20);
			$this->paginater_all->order_by('start_date DESC, run_id DESC');
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
		
		$this->libraries->template('setting_credit_data/coop_loan_atm_setting',$arr_data);
	}
	
	function coop_loan_atm_setting_save(){
		$data_insert = array();
		
		if(@$_POST["start_date"]!=''){
			$data_insert['start_date'] = $this->center_function->ConvertToSQLDate($_POST['start_date']);
		}
		
		$data_insert['prefix_code']  = @$_POST["prefix_code"];
		$data_insert['max_loan_amount']  = @$_POST["max_loan_amount"];
		$data_insert['interest_rate']  = @$_POST["interest_rate"];
		$data_insert['use_atm_count']  = @$_POST["use_atm_count"];
		$data_insert['use_atm_over_count_fee']	= @$_POST["use_atm_over_count_fee"];
		$data_insert['min_loan_amount']  = @$_POST["min_loan_amount"];
		$data_insert['min_month_share']  = @$_POST["min_month_share"];
		$data_insert['max_period']  = @$_POST["max_period"];
		$data_insert['max_withdraw_amount_day']  = @$_POST["max_withdraw_amount_day"];

		$id_edit = @$_POST["id"];
		
		$table = "coop_loan_atm_setting_template";

		if (empty($id_edit)) {
			// add
			$this->db->insert($table, $data_insert);
			$this->center_function->toast("บันทึกข้อมูลเรียบร้อยแล้ว");
		}else{
			// edit
			$this->db->where('run_id', $id_edit);
			$this->db->update($table, $data_insert);
			$this->center_function->toast("แก้ไขข้อมูลเรียบร้อยแล้ว");
		}
		
		$this->loan_libraries->update_loan_atm_setting_now();
		
		echo"<script> document.location.href='".PROJECTPATH."/setting_credit_data/coop_loan_atm_setting' </script>";
	}
	
	function coop_loan_atm_setting_delete(){
		$table = "coop_loan_atm_setting_template";
		$id = @$_POST['id'];
		$field = "run_id";

		$this->db->where($field, $id);
		$this->db->delete($table);

		$this->loan_libraries->update_loan_atm_setting_now();

		$this->center_function->toast("ลบเรียบร้อยแล้ว");
		echo true;
	}
	
	function coop_refrain_loan(){
		if($_POST){
			$data_insert = array();
			foreach($_POST as $key => $value){
				if($key == 'id'){
					$id = $value;
				}else{
					$data_insert[$key] = $value;	
				}
			}
			if(@$_POST['id'] == ''){
				$this->db->insert('coop_refrain_loan_setting',$data_insert);
			}else{
				$this->db->where('id',$id);
				$this->db->update('coop_refrain_loan_setting',$data_insert);
			}
			$this->center_function->toast('บันทึกข้อมูลเรียบร้อยแล้ว');
			echo"<script> document.location.href='".PROJECTPATH."/setting_credit_data/coop_refrain_loan' </script>";
		}
		$arr_data = array();
		
		$this->db->select('*');
		$this->db->from('coop_refrain_loan_setting');
		$row = $this->db->get()->result_array();
		$arr_data['row'] = @$row[0];
		
		$this->libraries->template('setting_credit_data/coop_refrain_loan',$arr_data);
	}

	function manage_garantor(){
		$id = @$_GET['id'];
		$main_condition = array( "null" => null );
		if($id){
			$main_condition = $this->db->get_where("coop_condition_of_loan", array(
				"term_of_loan_id" => $id,
				"result_type" => "guarantor"
			))->result_array();

			if($main_condition){
				foreach ($main_condition as $key => $value) {
					$this->db->join("coop_meta_condition", "coop_meta_condition.id = coop_condition_list.meta_condition_id");
					$condition = $this->db->get_where("coop_condition_list", array(
						"col_id" => $value['col_id']
					))->result_array();
					$main_condition[$key]['condition'] = $condition;

					$this->db->join("coop_meta_condition", "coop_meta_condition.id = coop_condition_of_loan_sub_guarantor.meta_condition_id");
					$condition_garantor = $this->db->get_where("coop_condition_of_loan_sub_guarantor", array(
						"col_id" => $value['col_id']
					))->result_array();
					$main_condition[$key]['condition_garantor'] = $condition_garantor;
				}
			}else{
				$main_condition = array( "null" => null );
			}
			
			
		}
		// $arr_data['main_condition'] = json_encode($main_condition);
		// echo "<pre>";var_dump($main_condition);exit;
		$arr_data['main_condition'] = $main_condition;
		$arr_data['meta_condition'] = $this->db->get("coop_meta_condition")->result_array();
		$this->libraries->template('setting_credit_data/manage_garantor',@$arr_data);
	}

	function save_manage_garantor(){
		$term_of_loan_id = @$_GET['id'];
		$data = $_POST;
		if($term_of_loan_id){
			$this->db->where("result_type", "guarantor");
			$this->db->where("term_of_loan_id", $term_of_loan_id);
			$this->db->delete("coop_condition_of_loan");
		}
		foreach ($data['condition_name'] as $key => $value) {
			$this->db->set("result_type", "guarantor");
			$this->db->set("detail_text", $value[0]);
			$this->db->set("term_of_loan_id", $term_of_loan_id);
			$this->db->insert("coop_condition_of_loan");
			$col_id = $this->db->insert_id();

			$condition = $data['condition'][$key];
			foreach ($condition as $i => $condition) {
				$meta_condition 	= $condition;
				$operation 			= $data['operation'][$key][$i];
				$val				= $data['value'][$key][$i];
				$this->db->set("meta_condition_id", $meta_condition);
				$this->db->set("operation", $operation);
				$this->db->set("value", $val);
				$this->db->set("col_id", $col_id);
				$this->db->insert("coop_condition_list");
				
				
			}
			$condition_garantor = $data['condition_garantor'][$key];
			foreach ($condition_garantor as $j => $condition_garantor) {
				$meta_condition_garantor 	= $condition_garantor;
				$operation_garantor 		= $data['operation_garantor'][$key][$j];
				$val_garantor				= $data['value_garantor'][$key][$j];
				$this->db->set("meta_condition_id", $meta_condition_garantor);
				$this->db->set("operation", $operation_garantor);
				$this->db->set("value", $val_garantor);
				$this->db->set("col_id", $col_id);
				$this->db->insert("coop_condition_of_loan_sub_guarantor");
			}
			
		}

		$this->center_function->toast("บันทึกข้อมูลสำเร็จ");
		header("location:".base_url("/setting_credit_data/manage_garantor?id=".$_GET['id']));

//		echo "<pre>";
//		var_dump($_POST);
//		echo "</pre>";
	}

	function manage_interest(){
		$id = @$_GET['id'];
		$main_condition = array( "null" => null );
		if($id){
			$main_condition = $this->db->get_where("coop_condition_of_loan", array(
				"term_of_loan_id" => $id,
				"result_value" => "interest"
			))->result_array();

			if($main_condition){
				foreach ($main_condition as $key => $value) {
					$this->db->join("coop_meta_condition", "coop_meta_condition.id = coop_condition_list.meta_condition_id");
					$condition = $this->db->get_where("coop_condition_list", array(
						"col_id" => $value['col_id']
					))->result_array();
					$main_condition[$key]['condition'] = $condition;
				}
			}else{
				$main_condition = array( "null" => null );
			}
			
			
		}
		// $arr_data['main_condition'] = json_encode($main_condition);
		// echo "<pre>";var_dump($main_condition);exit;
		$arr_data['main_condition'] = $main_condition;
		$arr_data['meta_condition'] = $this->db->get("coop_meta_condition")->result_array();
		$this->libraries->template('setting_credit_data/manage_interest',@$arr_data);
	}

	function save_manage_interest(){
		$term_of_loan_id = @$_GET['id'];
		$data = $_POST;
		// echo "<pre>";
		// var_dump($data);exit;
		if($term_of_loan_id){
			$this->db->where("result_type", "interest");
			$this->db->where("term_of_loan_id", $term_of_loan_id);
			$this->db->delete("coop_condition_of_loan");
		}
		foreach ($data['condition_name'] as $key => $value) {
			$this->db->set("result_type", "interest");
			$this->db->set("result_value", $data['result_value'][$key]);
			// $this->db->set("result_value", $key);
			$this->db->set("detail_text", $value[0]);
			$this->db->set("term_of_loan_id", $term_of_loan_id);
			$this->db->insert("coop_condition_of_loan");
			$col_id = $this->db->insert_id();

			$condition = $data['condition'][$key];
			foreach ($condition as $i => $condition) {
				$meta_condition 	= $condition;
				$operation 			= $data['operation'][$key][$i];
				$val				= $data['value'][$key][$i];
				$this->db->set("meta_condition_id", $meta_condition);
				$this->db->set("operation", $operation);
				$this->db->set("value", $val);
				$this->db->set("col_id", $col_id);
				$this->db->insert("coop_condition_list");
			}
		}
		
		$this->db->set("interest_rate", "0");
		$this->db->where("id", $term_of_loan_id);
		$this->db->update("coop_term_of_loan");

		//echo "<pre>";
		//var_dump($_POST);
		//echo "</pre>";
		echo"<script> document.location.href='".PROJECTPATH."/setting_credit_data/coop_term_of_loan' </script>";
	}

	function check_have_global_interest_rate(){
		$data = $_POST;

		$json_data = array();
		$term_of_loan_id = $data['term_of_loan_id'];
		$result = @$this->db->get_where("coop_term_of_loan", array(
			"id" => $term_of_loan_id
		))->result_array()[0];
		// if($result['interest_rate'] > 0){
		// 	$json_data['result'] = true;
		// }else{
		// 	$json_data['result'] = false;
		// }
		$json_data['result'] = true;
		header('Content-Type: application/json');
		echo json_encode($json_data);

	}

	function manage_condition(){
		$id = @$_GET['id'];
		$result_value = @$_GET['key'];
		$main_condition = array( "null" => null );
		if($id){
			$main_condition = $this->db->get_where("coop_condition_of_loan", array(
				"term_of_loan_id" => $id,
				"result_type" => $result_value
			))->result_array();

			if($main_condition){
				foreach ($main_condition as $key => $value) {
					$condition = $this->db->get_where("coop_condition_list", array(
						"col_id" => $value['col_id']
					))->result_array();

					// $ccd_id_a = 
					// $ccd_id_b = 
					
					// echo $condition;
					// exit;

					foreach ($condition as $k => $val) {
						$this->db->select(array('IF(a_is_meta = "1", detail_text, a) as a', 'op', 'IF(b_is_meta = "1", detail_text, b) as b'));
						$this->db->join("coop_meta_condition", "coop_meta_condition.id = coop_condition_detail.a", "left");
						$pair1 = $this->db->get_where("coop_condition_detail", array(
							"ccd_id" => $val['ccd_id_a']
						))->result_array()[0];

						$this->db->select(array('IF(a_is_meta = "1", detail_text, a) as a', 'op', 'IF(b_is_meta = "1", detail_text, b) as b'));
						$this->db->join("coop_meta_condition", "coop_meta_condition.id = coop_condition_detail.a", "left");
						$pair2 = $this->db->get_where("coop_condition_detail", array(
							"ccd_id" => $val['ccd_id_b']
						))->result_array()[0];
						$condition[$k]['pair1'] = $pair1;
						$condition[$k]['pair2'] = $pair2;
					}
					
					$this->db->select(array('IF(a_is_meta = "1", detail_text, a) as a', 'op', 'IF(b_is_meta = "1", detail_text, b) as b'));
					$this->db->join("coop_meta_condition", "coop_meta_condition.id = coop_condition_detail.a", "left");
					$result_value = $this->db->get_where("coop_condition_detail", array(
						"ccd_id" => $value['result_value']
					))->result_array()[0];

					$main_condition[$key]['condition'] = $condition;
					$main_condition[$key]['result_value'] = $result_value;
					// var_dump($main_condition[$key]['result_value']);exit;
				}
			}else{
				$main_condition = array( "null" => null );
			}
			
		}

		$this->db->select(array(
		    't1.term_of_loan_id as id',
            't2.type_name as name'
        ))->from("coop_condition_of_loan t1");
		$this->db->join("coop_term_of_loan t2", "t1.term_of_loan_id=t2.id", "inner");
		$this->db->where(array('t1.result_type' => $_GET['key']));
		$this->db->group_by('t1.term_of_loan_id');
		$this->db->order_by('t1.term_of_loan_id', 'asc');
		$arr_data['form_list'] = $this->db->get()->result_array();

		//echo $this->db->last_query(); exit;

		// $arr_data['main_condition'] = json_encode($main_condition);
		// echo "<pre>";var_dump($main_condition);exit;
		$arr_data['main_condition'] = $main_condition;
		$this->db->order_by("seq asc");
		$arr_data['meta_condition'] = $this->db->get("coop_meta_condition")->result_array();
		$this->libraries->template('setting_credit_data/mange_condition',@$arr_data);
	}

	function save_manage_condition(){
		$this->load->model("Condition_loan_model", "condition_model");
		$term_of_loan_id = @$_GET['id'];
		$result_type = @$_GET['key'];
		$data = $_POST;
		// echo "<pre>";
		// var_dump($data);
		// exit;
		if($term_of_loan_id){
			$this->db->where("result_type", $result_type);
			$this->db->where("term_of_loan_id", $term_of_loan_id);
			$coop_condition_of_loan = $this->db->select("col_id")->get("coop_condition_of_loan")->result_array();

			foreach ($coop_condition_of_loan as $key => $value) {
				
				$coop_condition_list = $this->db->get_where("coop_condition_list", array(
					"col_id" => $value['col_id']
				))->result_array();
				foreach ($coop_condition_list as $k => $val) {
					$this->db->where_in("ccd_id", array($val['ccd_id_a'], $val['ccd_id_b']));
					$this->db->delete("coop_condition_detail");
				}
				$this->db->where("col_id", $value['col_id']);
				$this->db->delete("coop_condition_list");

			}

			$this->db->where("result_type", $result_type);
			$this->db->where("term_of_loan_id", $term_of_loan_id);
			$this->db->delete("coop_condition_of_loan");
		}

		foreach ($data['condition_name'] as $key => $value) {
			if($this->condition_model->get_meta_id( $data['result_value_a'][$key] )!=""){
				$result_value_a 	= $this->condition_model->get_meta_id( $data['result_value_a'][$key] );
				$a_is_meta			= "1";
			}else{
				$result_value_a 	= $data['result_value_a'][$key];
				$a_is_meta			= "0";
			}

			if($this->condition_model->get_meta_id( $data['result_value_b'][$key] )!=""){
				$result_value_b 	= $this->condition_model->get_meta_id( $data['result_value_b'][$key] );
				$b_is_meta			= "1";
			}else{
				$result_value_b 	= $data['result_value_b'][$key];
				$b_is_meta			= "0";
			}
			$result_value_op 	= $data['result_value_op'][$key];
			$this->db->set("a", $result_value_a);
			$this->db->set("op", $result_value_op);
			$this->db->set("b", $result_value_b);
			$this->db->set("a_is_meta", $a_is_meta);
			$this->db->set("b_is_meta", $b_is_meta);
			$this->db->insert("coop_condition_detail");
			$ccd_id_result = $this->db->insert_id();

			$this->db->set("result_type", $result_type);
			$this->db->set("result_value", $ccd_id_result);
			$this->db->set("detail_text", $value[0]);
			$this->db->set("term_of_loan_id", $term_of_loan_id);
			$this->db->insert("coop_condition_of_loan");
			$col_id = $this->db->insert_id();

			$condition = $data['operation'][$key];
			foreach ($condition as $i => $condition) {
				/** pair_1 */
				if($this->condition_model->get_meta_id( $data['pair1_a'][$key][$i] )!=""){
					$pair1_a 			= $this->condition_model->get_meta_id( $data['pair1_a'][$key][$i] );
					$pair1_a_is_meta	= "1";
				}else{
					$pair1_a 			= $data['pair1_a'][$key][$i];
					$pair1_a_is_meta	= "0";
				}
				if($this->condition_model->get_meta_id( $data['pair1_b'][$key][$i] )!=""){
					$pair1_b 			= $this->condition_model->get_meta_id( $data['pair1_b'][$key][$i] );
					$pair1_b_is_meta	= "1";
				}else{
					$pair1_b 			= $data['pair1_b'][$key][$i];
					$pair1_b_is_meta	= "0";
				}
				/** pair_1 */

				/** pair_2 */
				if($this->condition_model->get_meta_id( $data['pair2_a'][$key][$i] )!=""){
					$pair2_a 			= $this->condition_model->get_meta_id( $data['pair2_a'][$key][$i] );
					$pair2_a_is_meta	= "1";
				}else{
					$pair2_a 			= $data['pair2_a'][$key][$i];
					$pair2_a_is_meta	= "0";
				}
				if($this->condition_model->get_meta_id( $data['pair2_b'][$key][$i] )!=""){
					$pair2_b 			= $this->condition_model->get_meta_id( $data['pair2_b'][$key][$i] );
					$pair2_b_is_meta	= "1";
				}else{
					$pair2_b 			= $data['pair2_b'][$key][$i];
					$pair2_b_is_meta	= "0";
				}
				/** pair_2 */

				// $pair1_a 			= $this->condition_model->get_meta_id( $data['pair1_a'][$key][$i] ) != "" ? $this->condition_model->get_meta_id( $data['pair1_a'][$key][$i] ) : $data['pair1_a'][$key][$i];
				// $pair1_b			= $this->condition_model->get_meta_id( $data['pair1_b'][$key][$i] ) != "" ? : $data['pair1_b'][$key][$i];
				
				// $pair2_a 			= $this->condition_model->get_meta_id( $data['pair2_a'][$key][$i] ) != "" ? $this->condition_model->get_meta_id( $data['pair2_a'][$key][$i] ) : $data['pair2_a'][$key][$i];
				// $pair2_b			= $this->condition_model->get_meta_id( $data['pair2_b'][$key][$i] ) != "" ? $this->condition_model->get_meta_id( $data['pair2_b'][$key][$i] ) : $data['pair2_b'][$key][$i];
				$op1				= $data['op1'][$key][$i];
				$op2				= $data['op2'][$key][$i];
				$operation 			= $data['operation'][$key][$i];

				$this->db->set("a", $pair1_a);
				$this->db->set("op", $op1);
				$this->db->set("b", $pair1_b);
				$this->db->set("a_is_meta", $pair1_a_is_meta);
				$this->db->set("b_is_meta", $pair1_b_is_meta);
				$this->db->insert("coop_condition_detail");
				$ccd_id_a = $this->db->insert_id();

				$this->db->set("a", $pair2_a);
				$this->db->set("op", $op2);
				$this->db->set("b", $pair2_b);
				$this->db->set("a_is_meta", $pair2_a_is_meta);
				$this->db->set("b_is_meta", $pair2_b_is_meta);
				$this->db->insert("coop_condition_detail");
				$ccd_id_b = $this->db->insert_id();

				$this->db->set("ccd_id_a", $ccd_id_a);
				$this->db->set("ccd_id_b", $ccd_id_b);
				$this->db->set("operation", $operation);
				$this->db->set("col_id", $col_id);
				$this->db->insert("coop_condition_list");
			}
		}

		// echo"<script> document.location.href='".PROJECTPATH."/setting_credit_data/manage_condition?id=".@$_GET['id']."&key=".@$_GET['key']."' </script>";
       // echo"<script> document.location.href='".PROJECTPATH."/setting_credit_data/coop_term_of_loan?act=add&id=".@$_GET['id']."</script>";
        $this->center_function->toast("บันทึกสำเร็จ");
		header("location: ".base_url("/setting_credit_data/manage_condition?id=".$_GET['id']."&key=".$_GET['key']));

	}

	private function cond_of_loan($id, $name){
        return $this->db->select('*')->from('coop_condition_of_loan')->where(array('term_of_loan_id' => $id, 'result_type' => $name))->get()->result_array();
    }

    private function cond_list($id){
	    return $this->db->select('*')->from('coop_condition_list')->where(array('col_id' => $id))->get()->result_array();
    }

    private function cond_detail($id = array()){
        return $this->db->select('*')->from('coop_condition_detail')->where_in('ccd_id' , $id)->get()->result_array();
    }

    private function find_max_list(){
	    return $this->db->select('max(list_id) as `max`')->from('coop_condition_list')->get()->row_array()['max'];
    }

    private function find_max_detail(){
        return $this->db->select('max(ccd_id) as `max`')->from('coop_condition_detail')->get()->row_array()['max'];
    }

    private function find_id_detail($main, $to){
	    $id = array();
	    $id[] = $main['result_value'];

	    $lists = $this->cond_list($main['col_id']);
	    foreach ($lists as $index => $list){
	        foreach( $list as $key => $val ){
	            if(in_array( $key, array('ccd_id_a', 'ccd_id_b'))){
                    $id[] = $val;
                }
            }
        }

	    $details = $this->cond_detail($id);

        $max_detail_start = $this->find_max_detail()+1;

        unset($main['col_id'], $main['result_value']);

        $main['result_value'] = $max_detail_start;
        $main['term_of_loan_id'] = $to;
        //echo '<pre>'; print_r($main); echo "</pre>";
        $this->db->insert('coop_condition_of_loan', $main);
        $last_id = $this->db->insert_id();
        $data_detail = array();
        $index = 0;
        foreach ($details as $key => $val){
            $data_detail[$index]['ccd_id'] = $max_detail_start;
            $data_detail[$index]['a'] = $val['a'];
            $data_detail[$index]['op'] = $val['op'];
            $data_detail[$index]['a_is_meta'] = $val['a_is_meta'];
            $data_detail[$index]['b_is_meta'] = $val['b_is_meta'];
            ++$max_detail_start;
            $index++;
        }
        //echo '<pre>'; print_r($data_detail); echo "</pre>";
        $this->db->insert_batch('coop_condition_detail', $data_detail);

        $max_list = $this->find_max_list();
        $data_list = array();
        $num = 0;
        $index = 1;

        foreach ($lists as $key => $list){
            $data_list[$num]['list_id'] = ++$max_list;
            $data_list[$num]['meta_condition_id'] = $list['meta_condition_id'];
            $data_list[$num]['operation'] = $list['operation'];
            $data_list[$num]['value'] = $list['value'];
            $data_list[$num]['col_id'] = $last_id;
            $data_list[$num]['ccd_id_a'] = $data_detail[$index]['ccd_id'];
            $data_list[$num]['ccd_id_b'] = $data_detail[$index+1]['ccd_id'];
            $index+=2;
            $num++;
        }
        //echo '<pre>'; print_r($data_list); echo "</pre>";
        $this->db->insert_batch('coop_condition_list', $data_list);

    }

    public function test_dup(){
	    $data = $this->db->select('*')->from('coop_condition_of_loan')->where(array('col_id' => 75))->limit(1)->get()->row_array();
        $this->find_id_detail($data);
    }

	public function duplicate(){
	    if(@$_GET['action'] == 'run') {
	        if(!isset($_GET['form_id']) || !isset($_GET['to_id']) || !isset($_GET['name'])){
	            echo "Stop";
	            exit;
            }
            $id = isset($_GET['form_id']) ? $_GET['form_id'] : 1;
            $name = isset($_GET['name']) ? $_GET['name'] : 'credit_limit';
            $move_to = isset($_GET['to_id']) ? $_GET['to_id'] : 9;


            $chk_cond =  $this->db->get_where('coop_condition_of_loan', array('term_of_loan_id' => $move_to, 'result_type' => $name))->result_array();

            if(sizeof($chk_cond)){
                $this->center_function->toast("ไม่สำเร็จ เนื่องจากมีเงื่อนไขอยู่แล้ว");
                header("location: ".base_url('/setting_credit_data/manage_condition?id='.$move_to.'&key='.$name));
                exit;
            }

            $main_cond = $this->cond_of_loan($id, $name);

            foreach ($main_cond as $index => $cond) {
                $this->find_id_detail($cond, $move_to);
            }
            $this->center_function->toast("คัดลอกข้อมูลสำเร็จแล้ว");
            header("location: ".base_url('/setting_credit_data/manage_condition?id='.$move_to.'&key='.$name));
        }else{
	        echo 'Ready';
        }
    }

    public function del_condition(){
	    if(@$_GET['action'] == 'run'){

	        if(!isset($_GET['id']) || !isset($_GET['name']) || !isset($_GET['col_id']) ||
                $_GET['id'] == "" || $_GET['name'] == "" || $_GET['col_id'] == ""){
	            echo "Stop";
	            exit;
            }

            $term_id = $_GET['id'];
	        $name = $_GET['name'];
            $col_id = $_GET['col_id'];

            if($_GET['debug'] == 'on'){
                echo "<pre>";
            }
	        $main_cond = self::find_main_condition($term_id, $name, $col_id);
	        foreach ($main_cond as $key => $value){
                $second_cond = self::find_second_condition($value['col_id']);
                foreach ($second_cond as $index => $item){
                     self::find_condition_list($item);
                }
                self::remove_condition_list($value['col_id']);
            }
            self::remove_condition_of_loan($term_id, $name, $col_id);

	        if($_GET['debug'] == 'on'){
	            echo "</pre>";
            }
	        echo "Success\n";
        }else{
	        echo "Ready\n";
        }
    }


    private function find_main_condition($id, $name, $number = ""){
        if(!isset($id) || $id == null || $id == "" || !isset($name) || $name == null || $name == ""){
            echo __METHOD__.":: error!!\n";
            exit;
        }
       $where = array('term_of_loan_id' => $id, 'result_type' => $name, 'col_id' => $number);
       return $this->db->select("*")->from("coop_condition_of_loan")->where($where)->get()->result_array();
    }

    private function find_second_condition($col_id){
	    if(!isset($col_id) || $col_id == null || $col_id == ""){
            echo __METHOD__.":: error!!\n";
	        exit;
        }
	    return $this->db->select("*")->from("coop_condition_list")->where(array(
	        'col_id' => $col_id
        ))->get()->result_array();
    }

    private function find_condition_list($item){
	    if(!isset($item) || sizeof($item) == 0){
	        return;
        }
        $list = array();
        $list[0] = $item['ccd_id_a'];
        $list[1] = $item['ccd_id_b'];

        foreach ($list as $key => $value){
            self::remove_condition_detail($value);
        }
    }

    private function remove_condition_detail($ccd_id){
	    if($ccd_id == ""){
	        return;
        }
        if($_GET['debug'] == 'on') {
            echo "DELETE FROM coop_condition_detail WHERE ccd_id='{$ccd_id}' \n";
        }else {
            $this->db->delete('coop_condition_detail', "ccd_id='{$ccd_id}'", 1);
        }
    }

    private function remove_condition_list($col_id){
	    if($col_id == ""){
	        return;
        }
        if($_GET['debug'] == 'on') {
            echo "DELETE FROM coop_condition_list WHERE col_id='{$col_id}' \n";
        }else {
            $this->db->delete("coop_condition_list", "col_id='{$col_id}'", 1);
        }
    }

    private function remove_condition_of_loan($term_id, $name, $id){
	    if($term_id == "" || $name == "" || $id == ""){
	        return;
        }

        if($_GET['debug'] == 'on'){
            echo "DELETE FROM coop_condition_of_loan WHERE term_of_loan_id='{$term_id}' AND result_type='{$name}' AND col_id='{$id}' \n";
        }else {
            $this->db->delete("coop_condition_of_loan", "term_of_loan_id='{$term_id}' AND result_type='{$name}' AND col_id='{$id}'");
        }
    }

}
