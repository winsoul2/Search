<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Setting_account_data2 extends CI_Controller {
	function __construct()
	{
		parent::__construct();
	}
	
	public function coop_account_chart(){
		$arr_data = array();
		$id = @$_GET['id'];
		if(@$id){
			$this->db->select(array('*'));
			$this->db->from('coop_account_chart');
			$this->db->where("account_chart_id  = '{$id}' ");
			$rs = $this->db->get()->result_array();
			$arr_data['row'] = @$rs[0];
		}else{	
			$x=0;
			$join_arr = array();

			$this->paginater_all->type(DB_TYPE);
			$this->paginater_all->select('*');
			$this->paginater_all->main_table('coop_account_chart');
			$this->paginater_all->where("");
			$this->paginater_all->page_now(@$_GET["page"]);
			$this->paginater_all->per_page(20);
			$this->paginater_all->page_link_limit(20);
			$this->paginater_all->order_by('account_chart_id ASC');
			$this->paginater_all->join_arr($join_arr);
			$row = $this->paginater_all->paginater_process();
			$paging = $this->pagination_center->paginating($row['page'], $row['num_rows'], $row['per_page'], $row['page_link_limit'], $_GET);//$page_now = 1, $row_total = 1, $per_page = 20, $page_limit = 20
			$i = $row['page_start'];

			$account_chart_groups = $this->db->select("*")->from("coop_account_chart")->where("(type = 1 OR type = 2) AND cancel_status IS NULL")->get()->result_array();

			$arr_data['num_rows'] = $row['num_rows'];
			$arr_data['paging'] = $paging;
			$arr_data['rs'] = $row['data'];
			$arr_data['i'] = $i;
			$arr_data['account_chart_groups'] = $account_chart_groups;
		}
		$this->libraries->template('setting_account_data2/coop_account_chart',$arr_data);
	}
	
	public function coop_account_chart_save(){
		$data_insert = array();
		$data_insert['account_chart_id'] = $_POST["account_chart_id"];
		$data_insert['account_chart'] = $_POST["account_chart"];
		$data_insert['is_fix'] = 0;
		if(!empty($_POST["account_parent_id"])) {
			$data_insert['account_parent_id'] = $_POST["account_parent_id"];
			$parent_chart = $this->db->select("*")->from("coop_account_chart")->where("account_chart_id = '".$_POST["account_parent_id"]."'")->get()->row();
			$data_insert['level'] = !empty($parent_chart->level) ? $parent_chart->level + 1 : 1;
		} else {
			$data_insert["level"] = 1;
		}

		$group = substr($_POST["account_chart_id"],0,1);
		if($group == 1 || $group == 5) {
			$data_insert['entry_type'] = 1;
		} else {
			$data_insert['entry_type'] = 2;
		}

		if($_POST["type"] == 'child') {
			$data_insert['type'] = 3;
		} else if ($data_insert["level"] == 1) {
			$data_insert['type'] = 1;
		} else {
			$data_insert['type'] = 2;
		}

		$id_edit = @$_POST["old_account_chart_id"];

		$table = "coop_account_chart";

		if(@$_POST['old_account_chart_id']!=''){
			// edit
			$this->db->where('account_chart_id', $id_edit);
			$this->db->update($table, $data_insert);
			$this->center_function->toast("แก้ไขข้อมูลเรียบร้อยแล้ว");
			// edit
		}else{
			// add
			$this->db->insert($table, $data_insert);
			$this->center_function->toast("บันทึกข้อมูลเรียบร้อยแล้ว");
			// add
		}
		echo"<script> document.location.href='".PROJECTPATH."/setting_account_data2/coop_account_chart' </script>";
	}
	
	public function check_account_chart(){
		$account_chart_id = trim(@$_POST['account_chart_id']);
		
		$this->db->select('COUNT(account_chart_id) as _c');
		$this->db->from('coop_account_chart');
		$this->db->where("account_chart_id  = '{$account_chart_id}' ");
		$count = $this->db->get()->result_array();
		$num_rows = $count[0]["_c"] ;
			
		if($num_rows > 0){
			echo "dupplicate";
		}else{
			echo "success";
		}
	}
	
	function del_coop_account_data(){	
		$data_update = array();
		$data_update["cancel_status"] = 1;
		$this->db->where('account_chart_id', $_POST['id'] );
		$this->db->update("coop_account_chart", $data_update);
		$this->center_function->toast("ลบเรียบร้อยแล้ว");
		echo true;
	}

	function enable_coop_account_data() {
		$data_update = array();
		$data_update["cancel_status"] = NULL;
		$this->db->where('account_chart_id', $_POST['id'] );
		$this->db->update("coop_account_chart", $data_update);
		$this->center_function->toast("เปิดใช้งานเรียบร้อยแล้ว");
		echo true;
	}
	
	public function coop_account_receipt(){
		$arr_data = array();
		$id = @$_GET['id'];
		if(@$id){
			$this->db->select(array('*'));
			$this->db->from('coop_account_list');
			$this->db->join("coop_account_match", "coop_account_list.account_id = coop_account_match.match_id AND coop_account_match.match_type = 'account_list'", "left");
			$this->db->where("coop_account_list.account_id  = '{$id}' ");
			$rs = $this->db->get()->result_array();
			$arr_data['row'] = @$rs[0];
			//print_r($this->db->last_query());exit;
		}else{	
			$x=0;
			$join_arr = array();
			$join_arr[$x]['table'] = 'coop_account_match';
			$join_arr[$x]['condition'] = "coop_account_list.account_id = coop_account_match.match_id AND coop_account_match.match_type = 'account_list'";
			$join_arr[$x]['type'] = 'left';
			
			$this->paginater_all->type(DB_TYPE);
			$this->paginater_all->select('coop_account_list.*,coop_account_match.account_chart_id');
			$this->paginater_all->main_table('coop_account_list');
			$this->paginater_all->where("");
			$this->paginater_all->page_now(@$_GET["page"]);
			$this->paginater_all->per_page(20);
			$this->paginater_all->page_link_limit(20);
			$this->paginater_all->order_by('account_id ASC');
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
		$this->db->from('coop_account_chart');
		$rs_account_chart = $this->db->get()->result_array();
		$arr_data['rs_account_chart'] = @$rs_account_chart;
		
		$this->libraries->template('setting_account_data2/coop_account_receipt',$arr_data);
	}
	
	public function coop_account_receipt_save(){
		$data_insert = array();
		$data_insert['account_list']	= @$_POST["account_list"];
		$data_insert['amount']  = @$_POST["amount"];
		$data_insert['account_type']  = '1';
		$data_insert['is_fix']  = '0';
		
		$data_insert_match = array();
		
		$type_add = @$_POST["type_add"] ;
		$id_edit = @$_POST["id"];

		$table = "coop_account_list";
		$table_sub = "coop_account_match";

		if ($type_add == 'add') {
			
			$this->db->select(array('*'));
			$this->db->from($table);
			$this->db->order_by('account_id DESC');
			$this->db->limit(1);
			$rs = $this->db->get()->result_array();
			$row = @$rs[0];
			
			$data_insert['account_id'] = $row['account_id']+1;
			// add
			$this->db->insert($table, $data_insert);			
			// add
			
			$account_id =  $data_insert['account_id'];
					
			$data_insert_match['match_type'] = 'account_list';
			$data_insert_match['account_chart_id'] = @$_POST["account_chart_id"];
			$data_insert_match['match_id'] = @$account_id;
			$data_insert_match['match_id_description'] = 'id จากตาราง coop_account_list';
			
			$this->db->insert($table_sub, $data_insert_match);			
			
			$this->center_function->toast("บันทึกข้อมูลเรียบร้อยแล้ว");
		}else{
			// edit
			$this->db->where('account_id', $id_edit);
			$this->db->update($table, $data_insert);			
			// edit
			
			$data_insert_match['account_chart_id'] = @$_POST["account_chart_id"];
			$this->db->where("match_id = '{$id_edit}' AND match_type = 'account_list' ");
			$this->db->update($table_sub, $data_insert_match);	
	  
			$this->center_function->toast("แก้ไขข้อมูลเรียบร้อยแล้ว");	
		}
		//print_r($this->db->last_query()); exit;	
		echo"<script> document.location.href='".PROJECTPATH."/setting_account_data2/coop_account_receipt' </script>";            
	}
	
	function del_coop_account_receipt_data(){	
		$table = @$_POST['table'];
		$id = @$_POST['id'];
		$field = @$_POST['field'];

		$this->db->where($field, $id );
		$this->db->delete($table);
		
		$this->db->where("match_id = '".$id."' AND match_type = 'account_list'");
		$this->db->delete('coop_account_match');
		
		
		$this->center_function->toast("ลบเรียบร้อยแล้ว");
		echo true;
		
	}
	
	public function coop_account_buy(){
		$arr_data = array();
		$id = @$_GET['id'];
		if(@$id){
			$this->db->select(array('*'));
			$this->db->from('coop_account_buy_list');
			$this->db->join("coop_account_match", "coop_account_buy_list.account_id = coop_account_match.match_id AND coop_account_match.match_type = 'account_buy_list'", "left");
			$this->db->where("coop_account_buy_list.account_id  = '{$id}' ");
			$rs = $this->db->get()->result_array();
			$arr_data['row'] = @$rs[0];
			//print_r($this->db->last_query());exit;
		}else{	
			$x=0;
			$join_arr = array();
			$join_arr[$x]['table'] = 'coop_account_match';
			$join_arr[$x]['condition'] = "coop_account_buy_list.account_id = coop_account_match.match_id AND coop_account_match.match_type = 'account_buy_list'";
			$join_arr[$x]['type'] = 'left';
			
			$this->paginater_all->type(DB_TYPE);
			$this->paginater_all->select('coop_account_buy_list.*,coop_account_match.account_chart_id');
			$this->paginater_all->main_table('coop_account_buy_list');
			$this->paginater_all->where("");
			$this->paginater_all->page_now(@$_GET["page"]);
			$this->paginater_all->per_page(20);
			$this->paginater_all->page_link_limit(20);
			$this->paginater_all->order_by('account_id ASC');
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
		$this->db->from('coop_account_chart');
		$rs_account_chart = $this->db->get()->result_array();
		$arr_data['rs_account_chart'] = @$rs_account_chart;
		
		$this->libraries->template('setting_account_data2/coop_account_buy',$arr_data);
	}
	
	public function coop_account_buy_save(){
		$data_insert = array();
		$data_insert['account_list']	= @$_POST["account_list"];
//		$data_insert['amount']  = @$_POST["amount"];
		
		$data_insert_match = array();
		
		$type_add = @$_POST["type_add"] ;
		$id_edit = @$_POST["id"];

		$table = "coop_account_buy_list";
		$table_sub = "coop_account_match";

		if ($type_add == 'add') {
			// add
			$this->db->insert($table, $data_insert);			
			// add
			
			$this->db->select(array('*'));
			$this->db->from($table);
			$this->db->order_by('account_id DESC');
			$this->db->limit(1);
			$rs = $this->db->get()->result_array();
			$row = @$rs[0];
					
			$data_insert_match['match_type'] = 'account_buy_list';
			$data_insert_match['account_chart_id'] = @$_POST["account_chart_id"];
			$data_insert_match['match_id'] = @$row['account_id'];
			$data_insert_match['match_id_description'] = 'id จากตาราง coop_account_buy_list';
			
			$this->db->insert($table_sub, $data_insert_match);			
			 
			$this->center_function->toast("บันทึกข้อมูลเรียบร้อยแล้ว");
		}else{
			// edit
			$this->db->where('account_id', $id_edit);
			$this->db->update($table, $data_insert);			
			// edit
			
			$data_insert_match['account_chart_id'] = @$_POST["account_chart_id"];
			$this->db->where("match_id = '{$id_edit}' AND match_type = 'account_buy_list' ");
			$this->db->update($table_sub, $data_insert_match);	
	  
			$this->center_function->toast("แก้ไขข้อมูลเรียบร้อยแล้ว");	
		}
		//print_r($this->db->last_query()); exit;	
		echo"<script> document.location.href='".PROJECTPATH."/setting_account_data2/coop_account_buy' </script>";            
	}
	
	function del_coop_account_buy_data(){	
		$table = @$_POST['table'];
		$id = @$_POST['id'];
		$field = @$_POST['field'];

		$this->db->where($field, $id );
		$this->db->delete($table);
		
		$this->db->where("match_id = '".$id."' AND match_type = 'account_buy_list'");
		$this->db->delete('coop_account_match');
		
		
		$this->center_function->toast("ลบเรียบร้อยแล้ว");
		echo true;
		
	}

	function coop_account_chart_preview(){
		$arr_data = array();
		$coop_account_chart = $this->db->select("*")->from("coop_account_chart")->order_by('account_chart_id ASC')->where('cancel_status = 0 OR cancel_status IS NULL')->get()->result_array();
		$account_chart_groups = $this->db->select("account_chart_id, account_chart")->from("coop_account_chart")->where("(type = 1 OR type = 2) AND cancel_status IS NULL")->get()->result_array();
		$arr_data['coop_account_chart'] = $coop_account_chart;
		$arr_data['account_chart_groups'] = $account_chart_groups;
		$this->load->view('setting_account_data2/coop_account_chart_preview',$arr_data);
	}
	function coop_account_chart_excel(){
		$arr_data = array();
		$coop_account_chart = $this->db->select("*")->from("coop_account_chart")->order_by('account_chart_id ASC')->where('cancel_status = 0 OR cancel_status IS NULL')->get()->result_array();
		$account_chart_groups = $this->db->select("account_chart_id, account_chart")->from("coop_account_chart")->where("(type = 1 OR type = 2) AND cancel_status IS NULL")->get()->result_array();
		$arr_data['coop_account_chart'] = $coop_account_chart;
		$arr_data['account_chart_groups'] = $account_chart_groups;

		$this->load->view('setting_account_data2/coop_account_chart_excel',$arr_data);
	}

	public function ajax_get_bank_data_by_id() {
		$arr_data = array();
		$results = $this->account_transaction->get_account_bank($_POST['id'], NULL);
		if(!empty($results['datas'])) {
			$arr_data['data'] = $results['datas'][0];
		}

		echo json_encode($arr_data);
	}

	public function bank() {
		$arr_data = array();
		$results = $this->account_transaction->get_account_bank(NULL, array(1));
		if(!empty($results['datas'])) {
			$arr_data['datas'] = $results['datas'];
		}

		$banks = $this->db->select("bank_code, bank_name")->from("coop_bank")->get()->result_array();
		$arr_data['banks'] = $banks;

		$charts = $this->account_transaction->get_account_charts(array(3));
		if(!empty($charts['datas'])) {
			$arr_data['charts'] = $charts['datas'];
		}

		$this->libraries->template('setting_account_data2/bank',$arr_data);
	}

	public function coop_account_bank_save() {
		$bank = $this->db->select("bank_code, bank_name")->from("coop_bank")->where("bank_code = '".$_POST['bank_code']."'")->get()->row_array();
		$data_insert = array();
		$data_insert['account_bank'] = $_POST['bank_code'];
		$data_insert['account_bank_name'] = $bank['bank_name'];
		$data_insert['account_bank_number'] = $_POST['account_bank_number'];
		$data_insert['account_chart_id'] = $_POST['account_chart_id'];
		if(empty($_POST['id'])) {
			$data_insert['status'] = 1;
			$this->db->insert("coop_account_bank", $data_insert);
			$this->center_function->toast("บันทึกข้อมูลเรียบร้อยแล้ว");
		} else {
			$this->db->where('account_bank_id', $_POST['id']);
			$this->db->update("coop_account_bank", $data_insert);
			$this->center_function->toast("แก้ไขข้อมูลเรียบร้อยแล้ว");
		}

		echo"<script> document.location.href='".PROJECTPATH."/setting_account_data2/bank' </script>";
	}

	public function delete_account_bank() {
		$data_insert = array();
		$data_insert['status'] = 3;
		$this->db->where('account_bank_id', $_POST['id']);
		$this->db->update("coop_account_bank", $data_insert);
		echo "success";
	}
}
