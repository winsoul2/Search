<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Report_deposit_data extends CI_Controller {
	function __construct()
	{
		parent::__construct();
		$this->month_arr = array('1'=>'มกราคม','2'=>'กุมภาพันธ์','3'=>'มีนาคม','4'=>'เมษายน','5'=>'พฤษภาคม','6'=>'มิถุนายน','7'=>'กรกฎาคม','8'=>'สิงหาคม','9'=>'กันยายน','10'=>'ตุลาคม','11'=>'พฤศจิกายน','12'=>'ธันวาคม');
		$this->load->model("Report_payment_deposit_model", "Report_payment");
		$this->load->model("Report_accrued_interest_model", "report_accrued");
	}
	
	public function coop_report_transaction(){
		$arr_data = array();
		
		$this->db->select(array('t1.type_id','t1.type_name','t1.type_code'));
		$this->db->from('coop_deposit_type_setting as t1');
		$row = $this->db->get()->result_array();
		$arr_data['type_id'] = $row;
		
		$this->db->select(array('user_id','user_name'));
		$this->db->from('coop_user');
		$row = $this->db->get()->result_array();
		$arr_data['row_user'] = @$row;
		
		$arr_data['transaction_lists'] = array(
			"'IN', 'INT'",
			"'INC', 'CW'",
			"'WTI'",
			"'WTB'",
			"'WTD'",
			"'TRB'",
			"'REVD'",
			"'ERRA', 'EATM'",
			"'ERR'",
			"'YPF'",
			"'XD'",
			"'CD', 'DEN'",
            "'DEPP'"
		);
		
		$arr_data['type_transaction'] = array('1'=>'ผ่านระบบ','2'=>'ผ่าน ATM');
		
		$this->libraries->template('report_deposit_data/coop_report_transaction',$arr_data);
	}
	
	function coop_report_transaction_preview(){
		$arr_data = array();		
		///////////////////////
		$this->db->select(array('t1.type_id','t1.type_name'));
		$this->db->from('coop_deposit_type_setting as t1');
		$rs_type = $this->db->get()->result_array();
		$arr_type_deposit = array();

		foreach($rs_type AS $key=>$row_type){
			$arr_type_deposit[$row_type['type_id']] = $row_type['type_name'];
			
		}
		$arr_data['type_deposit'] = $arr_type_deposit;
		
		$arr_data['month_arr'] = $this->center_function->month_arr();
		$arr_data['month_short_arr'] = $this->center_function->month_short_arr();
		
		if(@$_GET['start_date']){
			$start_date_arr = explode('/',@$_GET['start_date']);
			$start_day = $start_date_arr[0];
			$start_month = $start_date_arr[1];
			$start_year = $start_date_arr[2];
			$start_year -= 543;
			$start_date = $start_year.'-'.$start_month.'-'.$start_day;
		}
		
		if(@$_GET['end_date']){
			$end_date_arr = explode('/',@$_GET['end_date']);
			$end_day = $end_date_arr[0];
			$end_month = $end_date_arr[1];
			$end_year = $end_date_arr[2];
			$end_year -= 543;
			$end_date = $end_year.'-'.$end_month.'-'.$end_day;
		}
		$where = "";
		
		if(@$_GET['transaction_lists'] && empty($_GET['transaction_list_all'])){
			$where .= " AND coop_account_transaction.transaction_list IN (".implode(",", $_GET['transaction_lists']).")";
		}
		
		if(@$_GET['type_id'] && @$_GET['type_id']!="all"){
			$where .= " AND coop_maco_account.type_id = '".@$_GET['type_id']."'";
		}
		
		if(@$_GET['type_transaction'] == 2){
			$where .= " AND coop_account_transaction.member_id_atm IS NOT NULL";
		}
		
		if(@$_GET['user_id']){
			$where .= " AND coop_account_transaction.user_id = '".@$_GET['user_id']."'";
		}
		
		if(@$_GET['member_id']){
			$where .= " AND coop_account_transaction.member_id_atm = '".sprintf("%06d", @$_GET['member_id'])."'";
		}
		
		if(@$_GET['start_date'] != '' AND @$_GET['end_date'] == ''){
			$where .= " AND coop_account_transaction.transaction_time BETWEEN '".$start_date." 00:00:00.000' AND '".$start_date." 23:59:59.000'";
		}else if(@$_GET['start_date'] != '' AND @$_GET['end_date'] != ''){
			$where .= " AND coop_account_transaction.transaction_time BETWEEN '".$start_date." 00:00:00.000' AND '".$end_date." 23:59:59.000'";
		}
		
		$x=0;
		$join_arr = array();
		$join_arr[$x]['table'] = 'coop_user';
		$join_arr[$x]['condition'] = 'coop_account_transaction.user_id = coop_user.user_id';
		$join_arr[$x]['type'] = 'left';
		$x++;
		$join_arr[$x]['table'] = 'coop_maco_account';
		$join_arr[$x]['condition'] = 'coop_account_transaction.account_id = coop_maco_account.account_id';
		$join_arr[$x]['type'] = 'inner';
		
		$this->paginater_all_preview->type(DB_TYPE);
		if(@$_GET['transaction_lists'][0] == "'INC', 'CW'"){
			$this->paginater_all_preview->select(array(
				'coop_account_transaction.transaction_time',
				'coop_account_transaction.transaction_list',
				'coop_account_transaction.account_id',
				'0 AS transaction_deposit',
				'SUM(coop_account_transaction.transaction_withdrawal) AS transaction_withdrawal',
				'SUM(coop_account_transaction.transaction_balance) AS transaction_balance',
				'SUM(coop_account_transaction.transaction_deposit) AS interest',
				'coop_account_transaction.member_id_atm',
				'coop_user.user_name',
				'coop_maco_account.account_name'
			));
			$this->paginater_all_preview->group_by("account_id, transaction_time");
		}else{
			$this->paginater_all_preview->select(array(
				'coop_account_transaction.transaction_time',
				'coop_account_transaction.transaction_list',
				'coop_account_transaction.account_id',
				'coop_account_transaction.transaction_deposit',
				'coop_account_transaction.transaction_withdrawal',
				'coop_account_transaction.transaction_balance',
				'coop_account_transaction.member_id_atm',
				'coop_user.user_name',
				'coop_maco_account.account_name'
			));
		}
		$this->paginater_all_preview->main_table('coop_account_transaction');
		$this->paginater_all_preview->where("{$where}");
		$this->paginater_all_preview->page_now(@$_GET["page"]);
		if(@$_GET['excel']){
			$this->paginater_all_preview->per_page(9999999);
			$this->paginater_all_preview->page_link_limit(9999999);
			$this->paginater_all_preview->page_limit_first(9999999);
		}else{	
			$this->paginater_all_preview->per_page(20);
			$this->paginater_all_preview->page_link_limit(34);
			$this->paginater_all_preview->page_limit_first(28);
		}
		$this->paginater_all_preview->order_by('YEAR(coop_account_transaction.transaction_time), MONTH(coop_account_transaction.transaction_time), DAY(coop_account_transaction.transaction_time), HOUR(coop_account_transaction.transaction_time), MINUTE(coop_account_transaction.transaction_time), coop_maco_account.account_id, coop_account_transaction.transaction_id');
		$this->paginater_all_preview->join_arr($join_arr);
		$row = $this->paginater_all_preview->paginater_process();
		//if(@$_GET['dev']=='dev'){
		//	print_r($this->db->last_query()); exit;
		//}
		
		foreach($row['data'] AS $key=>$value){
			foreach($value AS $key2=>$value2){
				$this->db->select('coop_mem_apply.firstname_th, coop_mem_apply.lastname_th,coop_prename.prename_short');
				$this->db->join("coop_prename","coop_prename.prename_id = coop_mem_apply.prename_id","left");
				$this->db->from("coop_mem_apply");
				$this->db->where("member_id = '".@$value2['member_id_atm']."'");
				$rs_member = $this->db->get()->result_array();
				$row_member = @$rs_member[0];
				$row['data'][$key][$key2]['member_name_atm'] = @$row_member['prename_short'].@$row_member['firstname_th'].'  '.@$row_member['lastname_th'];
			}
		}
	
		$arr_data['num_rows'] = $row['num_rows'];
		$arr_data['paging'] = $paging;
		$arr_data['data'] = $row['data'];
		$arr_data['page_all'] = $row['page_all'];

		$this->db->select(array('type_name_transection'));
		$this->db->from('coop_report_transaction_type_config');
		$this->db->where("id= 2 ");
		$rs_cw = $this->db->get()->result_array();
		$arr_data['rs_cw'] = array_column($rs_cw,'type_name_transection');
		//print_r($rs_int );exit;

		$this->db->select(array('type_name_transection'));
		$this->db->from('coop_report_transaction_type_config');
		$this->db->where("id = 3 ");
		$rs_int = $this->db->get()->result_array();
		$arr_data['rs_int'] = array_column($rs_int,'type_name_transection');

		
		if(@$_GET['excel']){
			//$this->preview_libraries->template_preview('report_deposit_data/coop_report_transaction_excel',$arr_data);
			$this->load->view('report_deposit_data/coop_report_transaction_excel',$arr_data);
		}else{
			$this->preview_libraries->template_preview('report_deposit_data/coop_report_transaction_preview',$arr_data);
		}
		
	}	

	function check_report_transaction(){	
		if(@$_POST['start_date']){
			$start_date_arr = explode('/',@$_POST['start_date']);
			$start_day = $start_date_arr[0];
			$start_month = $start_date_arr[1];
			$start_year = $start_date_arr[2];
			$start_year -= 543;
			$start_date = $start_year.'-'.$start_month.'-'.$start_day;
		}
		
		if(@$_POST['end_date']){
			$end_date_arr = explode('/',@$_POST['end_date']);
			$end_day = $end_date_arr[0];
			$end_month = $end_date_arr[1];
			$end_year = $end_date_arr[2];
			$end_year -= 543;
			$end_date = $end_year.'-'.$end_month.'-'.$end_day;
		}
		
		$where = "";
		if(@$_POST['transaction_lists'] && empty($_POST['transaction_list_all'])){
			$where .= " AND coop_account_transaction.transaction_list IN (".implode(",", $_POST['transaction_lists']).")";
		}
		
		if(@$_POST['type_id'] && @$_POST['type_id'] != "all"){
				$where .= " AND coop_maco_account.type_id = '".@$_POST['type_id']."'";	
		}
		
		if(@$_POST['type_transaction'] == 2){
			$where .= " AND coop_account_transaction.member_id_atm IS NOT NULL";
		}
		
		if(@$_POST['user_id']){
			$where .= " AND coop_account_transaction.user_id = '".@$_POST['user_id']."'";
		}
		
		if(@$_POST['member_id']){
			$where .= " AND coop_account_transaction.member_id_atm = '".sprintf("%06d", @$_POST['member_id'])."'";
		}
		
		if(@$_POST['start_date'] != '' AND @$_POST['end_date'] == ''){
			$where .= " AND coop_account_transaction.transaction_time BETWEEN '".$start_date." 00:00:00.000' AND '".$start_date." 23:59:59.000'";
		}else if(@$_POST['start_date'] != '' AND @$_POST['end_date'] != ''){
			$where .= " AND coop_account_transaction.transaction_time BETWEEN '".$start_date." 00:00:00.000' AND '".$end_date." 23:59:59.000'";
		}		
		
		$this->db->select(array('coop_account_transaction.*','coop_user.user_name'));
		$this->db->from('coop_account_transaction');
		$this->db->join("coop_user","coop_account_transaction.user_id = coop_user.user_id","left");
		$this->db->join("coop_maco_account","coop_account_transaction.account_id = coop_maco_account.account_id","inner");
		$this->db->where("1=1 {$where}");
		$rs_count = $this->db->get()->result_array();
		//print_r($this->db->last_query()); exit;
		//echo '<pre>'; print_r($rs_count); echo '</pre>';		
		if(!empty($rs_count)){
			echo "success";
		}else{
			echo "";
		}		
	}
	
	public function coop_report_payout(){
		$arr_data = array();
		
		$this->db->select(array('t1.type_id','t1.type_name'));
		$this->db->from('coop_deposit_type_setting as t1');
		$row = $this->db->get()->result_array();
		$arr_data['type_id'] = $row;
		
		$this->libraries->template('report_deposit_data/coop_report_payout',$arr_data);
	}
	
	function coop_report_payout_preview(){
		$arr_data = array();		
		///////////////////////
		$this->db->select(array('t1.type_id','t1.type_name'));
		$this->db->from('coop_deposit_type_setting as t1');
		$rs_type = $this->db->get()->result_array();
		$arr_type_deposit = array();
		foreach($rs_type AS $key=>$row_type){
			$arr_type_deposit[$row_type['type_id']] = $row_type['type_name'];
			
		}
		$arr_data['type_deposit'] = $arr_type_deposit;
		
		$arr_data['month_arr'] = $this->center_function->month_arr();
		$arr_data['month_short_arr'] = $this->center_function->month_short_arr();
		
		if(@$_GET['start_date']){
			$start_date_arr = explode('/',@$_GET['start_date']);
			$start_day = $start_date_arr[0];
			$start_month = $start_date_arr[1];
			$start_year = $start_date_arr[2];
			$start_year -= 543;
			$start_date = $start_year.'-'.$start_month.'-'.$start_day;
		}
	
		$x=0;
		$join_arr = array();
		
		$this->paginater_all_preview->type(DB_TYPE);
		$this->paginater_all_preview->select(array('coop_deposit_type_setting.*'));
		$this->paginater_all_preview->main_table('coop_deposit_type_setting');
		$this->paginater_all_preview->where("1=1");
		$this->paginater_all_preview->page_now(@$_GET["page"]);
		$this->paginater_all_preview->per_page(20);
		$this->paginater_all_preview->page_link_limit(28);
		$this->paginater_all_preview->page_limit_first(24);
		$this->paginater_all_preview->order_by('type_seq');
		$this->paginater_all_preview->join_arr($join_arr);
		$row = $this->paginater_all_preview->paginater_process();
		//echo '<pre>'; print_r($row); echo '</pre>';	 

		//วันที่ยอดยกมา
		//$income_day = date('Y-m-d',strtotime($start_date . "-1 days"));
		
		// ยอดยกมา
		$sql = "SELECT t6.type_id, COUNT(t3.account_id) AS account_count, SUM(t3.transaction_balance) AS transaction_balance
					FROM coop_account_transaction t3
						INNER JOIN (
							SELECT t1.account_id, MAX(t1.transaction_id) AS transaction_id
							FROM coop_account_transaction t1
							INNER JOIN (
								SELECT account_id, MAX(transaction_time) AS transaction_time
								FROM coop_account_transaction
								WHERE transaction_time < '{$start_date}'
								GROUP BY account_id
							) t2 ON t1.account_id = t2.account_id AND t1.transaction_time = t2.transaction_time
							GROUP BY t1.account_id
						) t4 ON t3.transaction_id = t4.transaction_id
						INNER JOIN coop_maco_account t5 ON t3.account_id = t5.account_id
						INNER JOIN coop_deposit_type_setting t6 ON t5.type_id = t6.type_id
					GROUP BY t6.type_id
					HAVING transaction_balance > 0";
		$entries = $this->db->query($sql)->result_array();
		$boms = array();
		foreach($entries as $_row) {
			$boms[$_row["type_id"]] = $_row["transaction_balance"];
		}
		
		$arr_receive = array('CD','IN','XD','ADJ');
		$arr_pay = array('CW','CM/FE','ADJ');
		$data = array();
		foreach($row['data'] AS $key=>$data_row){
			foreach($data_row AS $key_detail=>$data_row_detail){
				$data[$key][$key_detail]['type_name'] = $data_row_detail['type_code'].' '.$data_row_detail['type_name'];
				
				//income_day
				$income = $boms[$data_row_detail['type_id']];
				/*$income = 0;
				$this->db->select("(coop_account_transaction.transaction_balance) AS transaction_balance");
				$this->db->from('coop_account_transaction');
				$this->db->join("coop_maco_account","coop_account_transaction.account_id = coop_maco_account.account_id","inner");
				$this->db->where("1=1 
								  AND type_id = '".$data_row_detail['type_id']."'  
								  AND coop_account_transaction.transaction_time < '".$start_date." 00:00:00.000' 
								  ");	
				$this->db->order_by("coop_account_transaction.transaction_time DESC, coop_account_transaction.transaction_id DESC");
				$this->db->limit(1);				  
				$rs_income_day = $this->db->get()->result_array();
				$income = $rs_income_day[0]['transaction_balance'];*/
				//print_r($this->db->last_query()); exit;	
				//echo '<pre>'; print_r($rs_income_day); echo '</pre>';
				
				//start_date
				$this->db->select(array('coop_account_transaction.*','coop_user.user_name'));
				$this->db->from('coop_account_transaction');
				$this->db->join("coop_user","coop_account_transaction.user_id = coop_user.user_id","left");
				$this->db->join("coop_maco_account","coop_account_transaction.account_id = coop_maco_account.account_id","inner");
				$this->db->where("1=1 
								  AND type_id = '".$data_row_detail['type_id']."'  
								  AND coop_account_transaction.transaction_time BETWEEN '".$start_date." 00:00:00.000' AND '".$start_date." 23:59:59.000'
								  ");				
				$rs_transaction = $this->db->get()->result_array();
				
				$receive = 0;
				$pay = 0;
				foreach($rs_transaction AS $key_transaction=>$row_transaction){
					//if(in_array(@$row_transaction['transaction_list'],$arr_receive)){
						$receive += $row_transaction['transaction_deposit'];
					//}
					
					//if(in_array(@$row_transaction['transaction_list'],$arr_pay)){
						$pay += $row_transaction['transaction_withdrawal'];
					//}
				}

				$data[$key][$key_detail]['income'] = @$income; //ยอดยกมา
				$data[$key][$key_detail]['receive'] = @$receive; //รับ
				$data[$key][$key_detail]['pay'] = @$pay; //จ่าย
				$data[$key][$key_detail]['increase_decrease'] = @$receive-@$pay; //เพิ่ม/ลด
				$data[$key][$key_detail]['balance'] = (@$income+@$receive)-@$pay; //คงเหลือ
			}
		}
		
		$arr_data['num_rows'] = $row['num_rows'];
		$arr_data['paging'] = $paging;
		$arr_data['data'] = $data;
		$arr_data['page_all'] = $row['page_all'];
			
		$this->preview_libraries->template_preview('report_deposit_data/coop_report_payout_preview',$arr_data);
		
	}	
	
	public function coop_report_transaction_emergent_atm(){
		$arr_data = array();
		
		$this->db->select(array('t1.type_id','t1.type_name'));
		$this->db->from('coop_deposit_type_setting as t1');
		$row = $this->db->get()->result_array();
		$arr_data['type_id'] = $row;
		
		$this->db->select(array('user_id','user_name'));
		$this->db->from('coop_user');
		$row = $this->db->get()->result_array();
		$arr_data['row_user'] = @$row;
		
		$arr_data['type_transaction'] = array('1'=>'ผ่านระบบ','2'=>'ผ่าน ATM');
		
		$this->libraries->template('report_deposit_data/coop_report_transaction_emergent_atm',$arr_data);
	}
	
	function coop_report_transaction_emergent_atm_preview(){
		$arr_data = array();	
		
		$arr_data['month_arr'] = $this->center_function->month_arr();
		$arr_data['month_short_arr'] = $this->center_function->month_short_arr();
		
		if(@$_GET['start_date']){
			$start_date_arr = explode('/',@$_GET['start_date']);
			$start_day = $start_date_arr[0];
			$start_month = $start_date_arr[1];
			$start_year = $start_date_arr[2];
			$start_year -= 543;
			$start_date = $start_year.'-'.$start_month.'-'.$start_day;
		}
		
		if(@$_GET['end_date']){
			$end_date_arr = explode('/',@$_GET['end_date']);
			$end_day = $end_date_arr[0];
			$end_month = $end_date_arr[1];
			$end_year = $end_date_arr[2];
			$end_year -= 543;
			$end_date = $end_year.'-'.$end_month.'-'.$end_day;
		}
		$where = "";
		
		if(@$_GET['start_date'] != '' AND @$_GET['end_date'] == ''){
			$where .= " AND coop_loan_atm_detail.loan_date BETWEEN '".$start_date." 00:00:00.000' AND '".$start_date." 23:59:59.000'";
		}else if(@$_GET['start_date'] != '' AND @$_GET['end_date'] != ''){
			$where .= " AND coop_loan_atm_detail.loan_date BETWEEN '".$start_date." 00:00:00.000' AND '".$end_date." 23:59:59.000'";
		}
		
		$x=0;
		$join_arr = array();
		$join_arr[$x]['table'] = 'coop_loan_atm';
		$join_arr[$x]['condition'] = 'coop_loan_atm_detail.loan_atm_id = coop_loan_atm.loan_atm_id';
		$join_arr[$x]['type'] = 'left';
		
		$this->paginater_all_preview->type(DB_TYPE);
		$this->paginater_all_preview->select(array(
													'coop_loan_atm_detail.loan_atm_id',
													'coop_loan_atm_detail.member_id',
													'coop_loan_atm_detail.loan_amount',
													'coop_loan_atm_detail.loan_date',
													'coop_loan_atm.contract_number',
													'coop_loan_atm_detail.transaction_at'
												));
		$this->paginater_all_preview->main_table('coop_loan_atm_detail');
		$this->paginater_all_preview->where("{$where}");
		$this->paginater_all_preview->page_now(@$_GET["page"]);
		$this->paginater_all_preview->per_page(20);
		$this->paginater_all_preview->page_link_limit(36);
		$this->paginater_all_preview->page_limit_first(30);
		$this->paginater_all_preview->order_by('coop_loan_atm_detail.loan_date');
		$this->paginater_all_preview->join_arr($join_arr);
		$row = $this->paginater_all_preview->paginater_process();
		//if(@$_GET['dev']=='dev'){
		//	print_r($this->db->last_query()); exit;
		//}
		
		foreach($row['data'] AS $key=>$value){
			foreach($value AS $key2=>$value2){
				$this->db->select('coop_mem_apply.firstname_th, coop_mem_apply.lastname_th,coop_prename.prename_short');
				$this->db->join("coop_prename","coop_prename.prename_id = coop_mem_apply.prename_id","left");
				$this->db->from("coop_mem_apply");
				$this->db->where("member_id = '".trim(@$value2['member_id'])."'");
				$rs_member = $this->db->get()->result_array();
				//print_r($this->db->last_query());
				//echo '<hr>';
				$row_member = @$rs_member[0];
				$row['data'][$key][$key2]['member_name'] = @$row_member['prename_short'].@$row_member['firstname_th'].'  '.@$row_member['lastname_th'];
			}
		}
		//exit;
		$arr_data['num_rows'] = $row['num_rows'];
		$arr_data['paging'] = $paging;
		$arr_data['data'] = $row['data'];
		$arr_data['page_all'] = $row['page_all'];
		$this->preview_libraries->template_preview('report_deposit_data/coop_report_transaction_emergent_atm_preview',$arr_data);
		
	}	

	function check_report_transaction_emergent_atm(){	
		if(@$_POST['start_date']){
			$start_date_arr = explode('/',@$_POST['start_date']);
			$start_day = $start_date_arr[0];
			$start_month = $start_date_arr[1];
			$start_year = $start_date_arr[2];
			$start_year -= 543;
			$start_date = $start_year.'-'.$start_month.'-'.$start_day;
		}
		
		if(@$_POST['end_date']){
			$end_date_arr = explode('/',@$_POST['end_date']);
			$end_day = $end_date_arr[0];
			$end_month = $end_date_arr[1];
			$end_year = $end_date_arr[2];
			$end_year -= 543;
			$end_date = $end_year.'-'.$end_month.'-'.$end_day;
		}
		$where = "";
		
		if(@$_POST['start_date'] != '' AND @$_POST['end_date'] == ''){
			$where .= " AND coop_loan_atm_detail.loan_date BETWEEN '".$start_date." 00:00:00.000' AND '".$start_date." 23:59:59.000'";
		}else if(@$_POST['start_date'] != '' AND @$_POST['end_date'] != ''){
			$where .= " AND coop_loan_atm_detail.loan_date BETWEEN '".$start_date." 00:00:00.000' AND '".$end_date." 23:59:59.000'";
		}		
		
		$this->db->select(array(
								'coop_loan_atm_detail.loan_atm_id',
								'coop_loan_atm_detail.member_id',
								'coop_loan_atm_detail.loan_amount',
								'coop_loan_atm_detail.loan_date',
								'coop_loan_atm.contract_number'
								));
		$this->db->from('coop_loan_atm_detail');
		$this->db->join("coop_loan_atm","coop_loan_atm_detail.loan_atm_id = coop_loan_atm.loan_atm_id","left");
		$this->db->where("1=1 {$where}");
		$rs_count = $this->db->get()->result_array();	
		if(!empty($rs_count)){
			echo "success";
		}else{
			echo "";
		}		
	}	
	
	public function coop_report_pay_share(){
		$arr_data = array();
		
		$this->db->select(array('t1.type_id','t1.type_name'));
		$this->db->from('coop_deposit_type_setting as t1');
		$row = $this->db->get()->result_array();
		$arr_data['type_id'] = $row;
		
		$this->db->select(array('user_id','user_name'));
		$this->db->from('coop_user');
		$row = $this->db->get()->result_array();
		$arr_data['row_user'] = @$row;
		
		$arr_data['type_transaction'] = array('1'=>'ผ่านระบบ','2'=>'ผ่าน ATM');
		
		$this->libraries->template('report_deposit_data/coop_report_pay_share',$arr_data);
	}
	
	function coop_report_pay_share_preview(){
		ini_set('memory_limit', -1);
		set_time_limit (180);
	//	$this->db->save_queries = FALSE;
		$arr_data = array();	
		
		$row = $this->db->select(array('id','loan_type','loan_type_code'))
						->from('coop_loan_type')
						->order_by("order_by")
						->get()->result_array();
		$arr_data['loan_type'] = $row;
		
		$arr_data['month_arr'] = $this->center_function->month_arr();
		$arr_data['month_short_arr'] = $this->center_function->month_short_arr();

		if(@$_GET['start_date']){
			$start_date_arr = explode('/',@$_GET['start_date']);
			$start_day = $start_date_arr[0];
			$start_month = $start_date_arr[1];
			$start_year = $start_date_arr[2];
			$start_year -= 543;
			$start_date = $start_year.'-'.$start_month.'-'.$start_day;
			
		}
		
		if(@$_GET['end_date']){
			$end_date_arr = explode('/',@$_GET['end_date']);
			$end_day = $end_date_arr[0];
			$end_month = $end_date_arr[1];
			$end_year = $end_date_arr[2];
			$end_year -= 543;
			$end_date = $end_year.'-'.$end_month.'-'.$end_day;
		}

		$tran_infos = array();

		//Get Transaction
		$where = "1=1";
		$where .= " AND payment_date BETWEEN '".$start_date." 00:00:00.000' AND '".$end_date." 23:59:59.000'";
		
		$where .= " AND ((coop_finance_transaction.loan_id IS NOT NULL AND coop_finance_transaction.loan_id <> '')	OR (coop_finance_transaction.loan_atm_id IS NOT NULL AND coop_finance_transaction.loan_atm_id <> '') OR coop_finance_transaction.account_list_id IN ('14','16','37','33','28','32','34','30','15','31')) AND (coop_receipt.receipt_status is null OR coop_receipt.receipt_status = '0')";
	
		$datas = $this->db->query("SELECT
									coop_finance_transaction.member_id,
									coop_finance_transaction.receipt_id,
									coop_finance_transaction.account_list_id,
									coop_finance_transaction.principal_payment,
									coop_finance_transaction.interest,
									coop_finance_transaction.loan_atm_id,
									coop_finance_transaction.loan_id,
									coop_mem_apply.firstname_th,
									coop_mem_apply.lastname_th,
									coop_prename.prename_short,
									coop_loan_atm.contract_number AS atm_contract_number,
									coop_loan.contract_number,
									coop_loan_type.loan_type_code,
									coop_finance_transaction.loan_interest_remain,
									coop_finance_transaction.createdatetime,
									coop_receipt.pay_type,
									coop_receipt.finance_month_profile_id,
									coop_loan_compromise.created_at AS created_compromise
								FROM
									coop_finance_transaction
								INNER JOIN coop_mem_apply ON coop_finance_transaction.member_id = coop_mem_apply.member_id
								LEFT JOIN coop_prename ON coop_mem_apply.prename_id = coop_prename.prename_id
								LEFT JOIN coop_loan_atm ON coop_finance_transaction.loan_atm_id = coop_loan_atm.loan_atm_id
								LEFT JOIN coop_loan ON coop_finance_transaction.loan_id = coop_loan.id
								LEFT JOIN coop_loan_name ON coop_loan.loan_type = coop_loan_name.loan_name_id
								LEFT JOIN coop_loan_type ON coop_loan_name.loan_type_id = coop_loan_type.id
								INNER JOIN coop_receipt ON coop_receipt.receipt_id = coop_finance_transaction.receipt_id
								LEFT JOIN coop_loan_compromise ON coop_loan_compromise.loan_id = coop_finance_transaction.loan_id
								WHERE {$where}
								ORDER BY
									coop_finance_transaction.member_id ASC,coop_finance_transaction.createdatetime ASC")->result_array();							
								//echo $this->db->last_query();exit;
		if(@$_GET['dev'] == 'dev'){
			echo '<pre>'; print_r($datas); echo '</pre>';
			echo $this->db->last_query();exit;
		}
		
		$row_data = array();
		$contract_numbers = array();
		$last_receipt_id = null;	

		//Get share withdraw
		$where = "t1.share_date BETWEEN '".$start_date." 00:00:00.000' AND '".$end_date." 23:59:59.000' AND t1.share_type = 'SRP'";
		$withdraw_shares = $this->db->query("SELECT t1.member_id,
													SUM(t1.share_early_value) as share_withdraw,
													t1.share_date as createdatetime,
													t2.firstname_th,
													t2.lastname_th,
													t3.prename_short,
													CONCAT('SHARE_', t1.member_id, t1.share_id) as receipt_id,
													'SRP' as account_list_id
												FROM coop_mem_share as t1
												INNER JOIN coop_mem_apply as t2 ON t1.member_id = t2.member_id
												LEFT JOIN coop_prename as t3 ON t2.prename_id = t3.prename_id
												WHERE {$where} GROUP BY t1.member_id")->result_array();

		//Merge share withdraw data to finance transaction data
		$datas = array_merge($datas, $withdraw_shares);

		foreach($datas as $tran) {
			if(@$tran["createdatetime"] !== @$tran["created_compromise"]){
				$receiptCheckStr = $tran['month_receipt']."B".substr($tran['year_receipt'],2);
				$receiptFCheckStr = $tran['month_receipt']."F".substr($tran['year_receipt'],2);
				$loan_interest_remain = 0;	
				if(strpos($tran["receipt_id"],$receiptCheckStr) === false && strpos($tran["receipt_id"],$receiptFCheckStr) === false) {
					$row_data[$tran['member_id']][$tran["receipt_id"]]['member_name'] = $tran["prename_short"].$tran["firstname_th"].'  '.$tran["lastname_th"];
					$row_data[$tran['member_id']][$tran["receipt_id"]]['receipt_id'] = $tran["receipt_id"];
					$row_data[$tran['member_id']][$tran["receipt_id"]]['receipt_datetime'] = $tran["createdatetime"];
					$row_data[$tran['member_id']][$tran["receipt_id"]]['pay_type'] = $tran["pay_type"];
					

					if ($tran['account_list_id'] == 14 || $tran['account_list_id'] == 16 || $tran['account_list_id'] == 37) {	
						$row_data[$tran['member_id']][$tran["receipt_id"]]['share'] += $tran["principal_payment"];
						
					} else if (!empty($tran['loan_atm_id'])) {
						if(!empty($tran["principal_payment"])) $row_data[$tran['member_id']][$tran["receipt_id"]]['emergent'][$tran["atm_contract_number"]]['principal'] = $tran["principal_payment"];
						if(!empty($tran["interest"])) $row_data[$tran['member_id']][$tran["receipt_id"]]['emergent'][$tran["atm_contract_number"]]['interest'] = $tran["interest"];
						if(!empty($tran["loan_interest_remain"])) $loan_interest_remain += @$tran["loan_interest_remain"];
						//if(!empty($tran["loan_interest_remain"])) $row_data[$tran['member_id']][$tran["receipt_id"]][$tran['loan_type_code']][$tran["contract_number"]]['none_pay'] = @$tran["loan_interest_remain"];
						//if(!empty($row_data[$tran['member_id']][$tran["receipt_id"]]['emergent'][$tran["atm_contract_number"]]['none_pay'])) {
						//	$payment_date = explode('-',$tran['payment_date']);
						//	$row_data[$tran['member_id']][$tran["receipt_id"]]['emergent'][$tran["atm_contract_number"]]['none_pay'] = $this->get_none_pay_atm($tran['loan_atm_id'], $payment_date[1], $payment_date[0]);
						//}
					} else if (!empty($tran['loan_id'])) {
						if(!empty($tran["principal_payment"])) $row_data[$tran['member_id']][$tran["receipt_id"]][$tran['loan_type_code']][$tran["contract_number"]]['principal'] = $tran["principal_payment"];
						if(!empty($tran["interest"])) $row_data[$tran['member_id']][$tran["receipt_id"]][$tran['loan_type_code']][$tran["contract_number"]]['interest'] = $tran["interest"];
						if(!empty($tran["loan_interest_remain"])) $loan_interest_remain += @$tran["loan_interest_remain"];
						//if(!empty($tran["loan_interest_remain"])) $row_data[$tran['member_id']][$tran["receipt_id"]][$tran['loan_type_code']][$tran["contract_number"]]['none_pay'] = @$tran["loan_interest_remain"];
						//if(!empty($row_data[$tran['member_id']][$tran["receipt_id"]][$tran['loan_type_code']][$tran["contract_number"]]['none_pay'])) {
						//	$payment_date = explode('-',$tran['payment_date']);
							//$row_data[$tran['member_id']][$tran["receipt_id"]][$tran['loan_type_code']][$tran["contract_number"]]['none_pay'] = $this->get_none_pay($tran['loan_id'], $payment_date[1], $payment_date[0]);
						//}
					}else if ($tran['account_list_id'] == 33) {	
						$row_data[$tran['member_id']][$tran["receipt_id"]]['life_insurance'] += $tran["principal_payment"];					
					}else if ($tran['account_list_id'] == 28) {	
						$row_data[$tran['member_id']][$tran["receipt_id"]]['cremation'] += $tran["principal_payment"];					
					}else if ($tran['account_list_id'] == 32) {	
						$row_data[$tran['member_id']][$tran["receipt_id"]]['loan_fee'] += $tran["principal_payment"];					
					}else if ($tran['account_list_id'] == 34) {	
						$row_data[$tran['member_id']][$tran["receipt_id"]]['person_guarantee'] += $tran["principal_payment"];					
					}else if ($tran['account_list_id'] == 30) {	
						if(@$tran['finance_month_profile_id'] != ''){
							$row_data[$tran['member_id']][$tran["receipt_id"]]['deposit'] += $tran["principal_payment"];
						}else{
							$row_data[$tran['member_id']][$tran["receipt_id"]]['deposit_blue'] += $tran["principal_payment"];
						}	
					} else if ($tran['account_list_id'] == 'SRP') {
						//Set data for share withdraw
						$row_data[$tran['member_id']][$tran["receipt_id"]]['share_withdraw'] += $tran["share_withdraw"];
					}
					$row_data[$tran['member_id']][$tran["receipt_id"]]['loan_interest_remain'] = $loan_interest_remain;
					$last_receipt_id = $tran['receipt_id'];		
				}
			}
			
		}
		$max_rows = 0;
		foreach($row_data AS $member_id => $data_rows){
			foreach($data_rows AS $receipt_id => $data_row){
				$normal_nums = array();
				if(!empty($data_row['normal'])) {
					foreach($data_row['normal'] as $contract_number => $val) {
						$normal_nums[] = $contract_number;
					}
				}
				$emergent_nums = array();
				if(!empty($data_row['emergent'])) {
					foreach($data_row['emergent'] as $contract_number => $val) {
						$emergent_nums[] = $contract_number;
					}
				}
				$special_num = array();
				if(!empty($data_row['special'])) {
					foreach($data_row['special'] as $contract_number => $val) {
						$special_num[] = $contract_number;
					}
				}
				$normalSize = count($normal_nums);
				$emergentSize = count($emergent_nums);
				$specialSize = count($special_num);
				$max_loan_index = max($normalSize, $emergentSize, $specialSize) > 0 ? max($normalSize, $emergentSize, $specialSize) : 1;
				for($i = 0; $i < $max_loan_index; $i++) {
					$max_rows++;
				}
			}
		}		
		
		$arr_data['num_rows'] = $row['num_rows'];
		$arr_data['paging'] = $paging;
		$arr_data['datas'] = $row_data;
		$arr_data['page_all'] = $row['page_all'];
		$arr_data['tran_infos'] = $tran_infos;
		$arr_data['last_receipt_id'] = $last_receipt_id;
		$arr_data['max_rows'] = @$max_rows;
		$arr_data['arr_pay_type'] = array(''=>'','0'=>'เงินสด','1'=>'โอนเงิน');
		$this->preview_libraries->template_preview('report_deposit_data/coop_report_pay_share_preview',$arr_data);
	}
	
	function coop_report_pay_share_excel(){
		ini_set('memory_limit', -1);
		set_time_limit (180);
	//	$this->db->save_queries = FALSE;
		$arr_data = array();	
		
		$row = $this->db->select(array('id','loan_type','loan_type_code'))
						->from('coop_loan_type')
						->order_by("order_by")
						->get()->result_array();
		$arr_data['loan_type'] = $row;
		
		$arr_data['month_arr'] = $this->center_function->month_arr();
		$arr_data['month_short_arr'] = $this->center_function->month_short_arr();

		if(@$_GET['start_date']){
			$start_date_arr = explode('/',@$_GET['start_date']);
			$start_day = $start_date_arr[0];
			$start_month = $start_date_arr[1];
			$start_year = $start_date_arr[2];
			$start_year -= 543;
			$start_date = $start_year.'-'.$start_month.'-'.$start_day;
			
		}
		
		if(@$_GET['end_date']){
			$end_date_arr = explode('/',@$_GET['end_date']);
			$end_day = $end_date_arr[0];
			$end_month = $end_date_arr[1];
			$end_year = $end_date_arr[2];
			$end_year -= 543;
			$end_date = $end_year.'-'.$end_month.'-'.$end_day;
		}

		$tran_infos = array();

		//Get Transaction
		$where = "1=1";
		$where .= " AND payment_date BETWEEN '".$start_date." 00:00:00.000' AND '".$end_date." 23:59:59.000'";
		
		$where .= " AND ((coop_finance_transaction.loan_id IS NOT NULL AND coop_finance_transaction.loan_id <> '')	OR (coop_finance_transaction.loan_atm_id IS NOT NULL AND coop_finance_transaction.loan_atm_id <> '') OR coop_finance_transaction.account_list_id IN ('14','16','37','33','28','32','34','30','15','31')) AND (coop_receipt.receipt_status is null OR coop_receipt.receipt_status = '0')";
		
		$datas = $this->db->query("SELECT
									coop_finance_transaction.member_id,
									coop_finance_transaction.receipt_id,
									coop_finance_transaction.account_list_id,
									coop_finance_transaction.principal_payment,
									coop_finance_transaction.interest,
									coop_finance_transaction.loan_atm_id,
									coop_finance_transaction.loan_id,
									coop_mem_apply.firstname_th,
									coop_mem_apply.lastname_th,
									coop_prename.prename_short,
									coop_loan_atm.contract_number AS atm_contract_number,
									coop_loan.contract_number,
									coop_loan_type.loan_type_code,
									coop_finance_transaction.loan_interest_remain,
									coop_finance_transaction.createdatetime,
									coop_receipt.pay_type,
									coop_receipt.finance_month_profile_id,
									coop_loan_compromise.created_at AS created_compromise
								FROM
									coop_finance_transaction
								INNER JOIN coop_mem_apply ON coop_finance_transaction.member_id = coop_mem_apply.member_id
								LEFT JOIN coop_prename ON coop_mem_apply.prename_id = coop_prename.prename_id
								LEFT JOIN coop_loan_atm ON coop_finance_transaction.loan_atm_id = coop_loan_atm.loan_atm_id
								LEFT JOIN coop_loan ON coop_finance_transaction.loan_id = coop_loan.id
								LEFT JOIN coop_loan_name ON coop_loan.loan_type = coop_loan_name.loan_name_id
								LEFT JOIN coop_loan_type ON coop_loan_name.loan_type_id = coop_loan_type.id
								INNER JOIN coop_receipt ON coop_receipt.receipt_id = coop_finance_transaction.receipt_id
								LEFT JOIN coop_loan_compromise ON coop_loan_compromise.loan_id = coop_finance_transaction.loan_id
								WHERE {$where}
								ORDER BY
									coop_finance_transaction.member_id ASC,coop_finance_transaction.createdatetime ASC")->result_array();							
								//echo $this->db->last_query();exit;
		$row_data = array();
		$contract_numbers = array();
		$last_receipt_id = null;		

		//Get share withdraw
		$where = "t1.share_date BETWEEN '".$start_date." 00:00:00.000' AND '".$end_date." 23:59:59.000' AND t1.share_type = 'SRP'";
		$withdraw_shares = $this->db->query("SELECT t1.member_id,
													SUM(t1.share_early_value) as share_withdraw,
													t1.share_date as createdatetime,
													t2.firstname_th,
													t2.lastname_th,
													t3.prename_short,
													CONCAT('SHARE_', t1.member_id, t1.share_id) as receipt_id,
													'SRP' as account_list_id
												FROM coop_mem_share as t1
												INNER JOIN coop_mem_apply as t2 ON t1.member_id = t2.member_id
												LEFT JOIN coop_prename as t3 ON t2.prename_id = t3.prename_id
												WHERE {$where} GROUP BY t1.member_id")->result_array();

		//Merge share withdraw data to finance transaction data
		$datas = array_merge($datas, $withdraw_shares);

		foreach($datas as $tran) {
			if(@$tran["createdatetime"] !== @$tran["created_compromise"]){
				$receiptCheckStr = $tran['month_receipt']."B".substr($tran['year_receipt'],2);
				$receiptFCheckStr = $tran['month_receipt']."F".substr($tran['year_receipt'],2);
				$loan_interest_remain = 0;	
				if(strpos($tran["receipt_id"],$receiptCheckStr) === false && strpos($tran["receipt_id"],$receiptFCheckStr) === false) {
					$row_data[$tran['member_id']][$tran["receipt_id"]]['member_name'] = $tran["prename_short"].$tran["firstname_th"].'  '.$tran["lastname_th"];
					$row_data[$tran['member_id']][$tran["receipt_id"]]['receipt_id'] = $tran["receipt_id"];
					$row_data[$tran['member_id']][$tran["receipt_id"]]['receipt_datetime'] = $tran["createdatetime"];
					$row_data[$tran['member_id']][$tran["receipt_id"]]['pay_type'] = $tran["pay_type"];

					if ($tran['account_list_id'] == 14 || $tran['account_list_id'] == 16 || $tran['account_list_id'] == 37) {
						$row_data[$tran['member_id']][$tran["receipt_id"]]['share'] += $tran["principal_payment"];
					} else if (!empty($tran['loan_atm_id'])) {
						if(!empty($tran["principal_payment"])) $row_data[$tran['member_id']][$tran["receipt_id"]]['emergent'][$tran["atm_contract_number"]]['principal'] = $tran["principal_payment"];
						if(!empty($tran["interest"])) $row_data[$tran['member_id']][$tran["receipt_id"]]['emergent'][$tran["atm_contract_number"]]['interest'] = $tran["interest"];
						if(!empty($tran["loan_interest_remain"])) $loan_interest_remain += @$tran["loan_interest_remain"];
						//if(!empty($row_data[$tran['member_id']][$tran["receipt_id"]]['emergent'][$tran["atm_contract_number"]]['none_pay'])) {
						//	$payment_date = explode('-',$tran['payment_date']);
						//	$row_data[$tran['member_id']][$tran["receipt_id"]]['emergent'][$tran["atm_contract_number"]]['none_pay'] = $this->get_none_pay_atm($tran['loan_atm_id'], $payment_date[1], $payment_date[0]);
						//}
					} else if (!empty($tran['loan_id'])) {
						if(!empty($tran["principal_payment"])) $row_data[$tran['member_id']][$tran["receipt_id"]][$tran['loan_type_code']][$tran["contract_number"]]['principal'] = $tran["principal_payment"];
						if(!empty($tran["interest"])) $row_data[$tran['member_id']][$tran["receipt_id"]][$tran['loan_type_code']][$tran["contract_number"]]['interest'] = $tran["interest"];
						if(!empty($tran["loan_interest_remain"])) $loan_interest_remain += @$tran["loan_interest_remain"];
						//if(!empty($row_data[$tran['member_id']][$tran["receipt_id"]][$tran['loan_type_code']][$tran["contract_number"]]['none_pay'])) {
						//	$payment_date = explode('-',$tran['payment_date']);
						//	$row_data[$tran['member_id']][$tran["receipt_id"]][$tran['loan_type_code']][$tran["contract_number"]]['none_pay'] = $this->get_none_pay($tran['loan_id'], $payment_date[1], $payment_date[0]);
						//}
					}else if ($tran['account_list_id'] == 33) {	
						$row_data[$tran['member_id']][$tran["receipt_id"]]['life_insurance'] += $tran["principal_payment"];					
					}else if ($tran['account_list_id'] == 28) {	
						$row_data[$tran['member_id']][$tran["receipt_id"]]['cremation'] += $tran["principal_payment"];					
					}else if ($tran['account_list_id'] == 32) {	
						$row_data[$tran['member_id']][$tran["receipt_id"]]['loan_fee'] += $tran["principal_payment"];					
					}else if ($tran['account_list_id'] == 34) {	
						$row_data[$tran['member_id']][$tran["receipt_id"]]['person_guarantee'] += $tran["principal_payment"];					
					}else if ($tran['account_list_id'] == 30) {	
						if(@$tran['finance_month_profile_id'] != ''){
							$row_data[$tran['member_id']][$tran["receipt_id"]]['deposit'] += $tran["principal_payment"];
						}else{
							$row_data[$tran['member_id']][$tran["receipt_id"]]['deposit_blue'] += $tran["principal_payment"];
						}	
					} else if ($tran['account_list_id'] == 'SRP') {
						//Set data for share withdraw
						$row_data[$tran['member_id']][$tran["receipt_id"]]['share_withdraw'] += $tran["share_withdraw"];
					}
					$row_data[$tran['member_id']][$tran["receipt_id"]]['loan_interest_remain'] = $loan_interest_remain;
					$last_receipt_id = $tran['receipt_id'];		
				}
			}
		}
		$max_rows = 0;
		foreach($row_data AS $member_id => $data_rows){
			foreach($data_rows AS $receipt_id => $data_row){
				$max_rows++;
			}
		}		
		
		$arr_data['num_rows'] = $row['num_rows'];
		$arr_data['paging'] = $paging;
		$arr_data['datas'] = $row_data;
		$arr_data['page_all'] = $row['page_all'];
		$arr_data['tran_infos'] = $tran_infos;
		$arr_data['last_receipt_id'] = $last_receipt_id;
		$arr_data['max_rows'] = @$max_rows;
		$arr_data['arr_pay_type'] = array(''=>'','0'=>'เงินสด','1'=>'โอนเงิน');
		$this->load->view('report_deposit_data/coop_report_pay_share_excel',$arr_data);	
	}

	function get_none_pay($loan_id, $month, $year) {
		//$this->db->select('coop_non_pay.non_pay_id, coop_non_pay.non_pay_amount_balance');
		$this->db->select('coop_non_pay.non_pay_id, coop_non_pay_detail.non_pay_amount_balance');
		$this->db->from("coop_non_pay");
		$this->db->join("coop_non_pay_detail", "coop_non_pay.non_pay_id = coop_non_pay_detail.non_pay_id", "left");
		$this->db->where("coop_non_pay.non_pay_month = '".$month."' AND coop_non_pay.non_pay_year = '".$year."' AND coop_non_pay_detail.pay_type = 'interest' AND coop_non_pay_detail.loan_id = '{$loan_id}'");
		$loan = $this->db->get()->row();
		if (!empty($loan)) {
			return $loan->non_pay_amount_balance;
		}
		return 0;
	}

	//เงินฝากชมพู
	function coop_report_gov_bank() {
		$arr_data = array();
		
		//Get Account Type
		$arr_data['type_ids'] = $this->db->select(array('type_id','type_name','type_code'))->from('coop_deposit_type_setting')->order_by("type_seq")->get()->result_array();
		
		$this->db->select(array('t1.type_id','t1.type_name','t1.type_code'));
		$this->db->from('coop_deposit_type_setting as t1');
		$row = $this->db->get()->result_array();
		$arr_data['type_id'] = $row;
		
		$this->db->select(array('user_id','user_name'));
		$this->db->from('coop_user');
		$row = $this->db->get()->result_array();
		$arr_data['row_user'] = @$row;
		
		$this->db->select('apply_type_id, apply_type_name, age_limit');
		$this->db->from('coop_mem_apply_type');
		$row_mem_apply_type = $this->db->get()->result_array();
		$arr_data['row_mem_apply_type'] = $row_mem_apply_type;
		
		$arr_data['type_transaction'] = array('1'=>'ผ่านระบบ','2'=>'ผ่าน ATM');
		
		$this->libraries->template('report_deposit_data/coop_report_gov_bank',$arr_data);
	}

	function coop_report_gov_bank_preview() {
		$arr_data = array();
		set_time_limit (180);
		//$this->db->save_queries = FALSE;

		if(@$_GET['start_date']){
			$start_date_arr = explode('/',@$_GET['start_date']);
			$start_day = $start_date_arr[0];
			$start_month = $start_date_arr[1];
			$start_year = $start_date_arr[2];
			$start_year -= 543;
			$start_date = $start_year.'-'.$start_month.'-'.$start_day;
			
			//echo '<pre>'; print_r($_GET); echo '</pre>';
			$_GET['start_date'] = urlencode($_GET['start_date']);
		}
		
		if($_GET["report_type"] == 1) {
			$sql = "SELECT t5.account_id, t6.type_code, t6.type_name, COUNT(*) AS account_count, SUM(t3.transaction_balance) AS transaction_balance
						FROM coop_account_transaction t3
							INNER JOIN (
								SELECT t1.account_id, MAX(t1.transaction_id) AS transaction_id
								FROM coop_account_transaction t1
								INNER JOIN (
									SELECT account_id, MAX(transaction_time) AS transaction_time
									FROM coop_account_transaction
									WHERE transaction_time <= '{$start_date} 23:59:59'
									GROUP BY account_id
								) t2 ON t1.account_id = t2.account_id AND t1.transaction_time = t2.transaction_time
								GROUP BY t1.account_id
							) t4 ON t3.transaction_id = t4.transaction_id
							INNER JOIN coop_maco_account t5 ON t3.account_id = t5.account_id
							INNER JOIN coop_deposit_type_setting t6 ON t5.type_id = t6.type_id
						WHERE 1=1
						AND t3.transaction_list NOT IN ('WI')
						AND t3.transaction_balance > 0
						GROUP BY t6.type_code, t6.type_name
						HAVING transaction_balance > 0";
			$enties = $this->db->query($sql)->result_array();
			$arr_data['data'] = $enties;
			if(@$_GET['dev']=='dev'){
				echo $this->db->last_query();
			}

			$this->preview_libraries->template_preview('report_deposit_data/coop_report_gov_bank_by_type_preview',$arr_data);
		}
		else {			
			//$where = "t1.type_id = '2'";
			$where = "t5.type_id = '".$_GET['type_id']."'";
			if(@$_GET['apply_type_id'] != ''){
				$where .= " AND t6.apply_type_id = '".$_GET['apply_type_id']."'";
			}
			$where .= " AND t3.transaction_balance > 0 ";
			$where .= " AND t3.transaction_list NOT IN ( 'WI' )  ";
			
			$_rows = $this->db->select(array('type_id','type_name'))->from('coop_deposit_type_setting')->where("type_id = '".$_GET['type_id']."'")->get()->result_array();
			$arr_data["type_name"] = $_rows[0]["type_name"];
			
			$x=0;
			$join_arr = array();
			$join_arr[$x]['table'] = "(
								SELECT
									t1.account_id,
									MAX( t1.transaction_id ) AS transaction_id 
								FROM
									coop_account_transaction t1
									INNER JOIN ( SELECT account_id, MAX( transaction_time ) AS transaction_time FROM coop_account_transaction WHERE transaction_time <= '{$start_date} 23:59:59' GROUP BY account_id ) t2 ON t1.account_id = t2.account_id 
									AND t1.transaction_time = t2.transaction_time 
								GROUP BY
									t1.account_id 
								) t4";
			$join_arr[$x]['condition'] = "t3.transaction_id = t4.transaction_id";
			$join_arr[$x]['type'] = 'inner';
			$x++;
			$join_arr[$x]['table'] = 'coop_maco_account t5';
			$join_arr[$x]['condition'] = "t3.account_id = t5.account_id";
			$join_arr[$x]['type'] = 'left';
			$x++;
			$join_arr[$x]['table'] = 'coop_mem_apply as t6';
			$join_arr[$x]['condition'] = "t5.mem_id = t6.member_id";
			$join_arr[$x]['type'] = 'left';
			$x++;
			$join_arr[$x]['table'] = 'coop_prename as t7';
			$join_arr[$x]['condition'] = "t6.prename_id = t7.prename_id";
			$join_arr[$x]['type'] = 'left';

			$this->paginater_all->field_count("t5.account_id");
			$this->paginater_all->type(DB_TYPE);

			$this->paginater_all->select('t5.account_id,t5.type_id,t3.transaction_balance,t6.member_id, t6.firstname_th, t6.lastname_th, t7.prename_short');
			$this->paginater_all->main_table('coop_account_transaction t3');
			$this->paginater_all->where($where);
			$this->paginater_all->page_now(@$_GET["page"]);
			$this->paginater_all->per_page(100);
			$this->paginater_all->page_link_limit(36);
			$this->paginater_all->join_arr($join_arr);
			$this->paginater_all->order_by('t5.account_id');
			$row = $this->paginater_all->paginater_process();
			
			// print_r($row); exit;
			if(@$_GET['dev']=='dev'){
				echo $this->db->last_query(); //exit;
			}
			$paging = $this->pagination_center->paginating(intval($row['page']), $row['num_rows'], $row['per_page'], $row['page_link_limit'],@$_GET);//$page_now = 1, $row_total = 1, $per_page = 20, $page_limit = 20
			$runno = (($row['page'] * 100) - 100) + 1;
			foreach($row['data'] AS $key2=>$value2){
				$row['data'][$key2]['member_name'] = @$value2['prename_short'].@$value2['firstname_th'].'  '.@$value2['lastname_th'];
				$row['data'][$key2]['runno'] = $runno;
				$runno++;
			}


	
			if (intval($row['page']) == ceil($row['num_rows']/$row['per_page'])) {
				$rs = $this->db->select("SUM(t3.transaction_balance) AS sum_transaction_balance")
				->from('coop_account_transaction t3')
				->join("(
						SELECT
							t1.account_id,
							MAX( t1.transaction_id ) AS transaction_id 
						FROM
							coop_account_transaction t1
							INNER JOIN ( SELECT account_id, MAX( transaction_time ) AS transaction_time FROM coop_account_transaction WHERE transaction_time <= '{$start_date} 23:59:59' GROUP BY account_id ) t2 ON t1.account_id = t2.account_id 
							AND t1.transaction_time = t2.transaction_time 
						GROUP BY
							t1.account_id 
						) t4", "t3.transaction_id = t4.transaction_id", "inner")
				
				->join("coop_maco_account t5","t3.account_id = t5.account_id","left")
				->join("coop_mem_apply AS t6","t5.mem_id = t6.member_id","left")
				->where($where)->order_by('t5.account_id ')->get()->result_array();
				
				$arr_data['sum_transaction_balance'] = $rs[0]['sum_transaction_balance'];
								
			}
	
			$this->db->select('apply_type_id, apply_type_name');
			$this->db->from('coop_mem_apply_type');
			$rs_mem_apply_type = $this->db->get()->result_array();
			$arr_mem_apply_type = array();
			foreach($rs_mem_apply_type AS $key=>$row_mem_apply_type){
				$arr_mem_apply_type[@$row_mem_apply_type['apply_type_id']] = @$row_mem_apply_type['apply_type_name'];
			}
			$arr_data['arr_mem_apply_type'] = $arr_mem_apply_type;
		
			$arr_data['num_rows'] = $row['num_rows'];
			$arr_data['paging'] = $paging;
			$arr_data['data'] = $row['data'];
			$arr_data['page_all'] = ceil($row['num_rows']/$row['per_page']);
			$arr_data['page'] = $row['page'];
	
			$this->preview_libraries->template_preview('report_deposit_data/coop_report_gov_bank_preview',$arr_data);
		}
	}

	function check_coop_report_gov_bank() {
		if($_POST['start_date']){
			$start_date_arr = explode('/',@$_POST['start_date']);
			$start_day = $start_date_arr[0];
			$start_month = $start_date_arr[1];
			$start_year = $start_date_arr[2];
			$start_year -= 543;
			$start_date = $start_year.'-'.$start_month.'-'.$start_day;
		}
		
		if($_POST['end_date']){
			$end_date_arr = explode('/',@$_POST['end_date']);
			$end_day = $end_date_arr[0];
			$end_month = $end_date_arr[1];
			$end_year = $end_date_arr[2];
			$end_year -= 543;
			$end_date = $end_year.'-'.$end_month.'-'.$end_day;
		}

		$where = "t1.type_id = '2'";
		if($_POST['start_date'] != '' && $_POST['end_date'] == ''){
			$where .= " AND t2.transaction_time BETWEEN '".$start_date." 00:00:00.000' AND '".$start_date." 23:59:59.000'";
		}else if($_POST['start_date'] != '' && $_POST['end_date'] != ''){
			$where .= " AND t2.transaction_time BETWEEN '".$start_date." 00:00:00.000' AND '".$end_date." 23:59:59.000'";
		}

		$this->db->select('t1.account_id');
		$this->db->from("coop_maco_account as t1");
		$this->db->join("(SELECT * FROM coop_account_transaction WHERE transaction_time BETWEEN '".$start_date." 00:00:00.000' AND '".$end_date." 23:59:59.000' GROUP BY account_id) as t2", "t1.account_id = t2.account_id", "inner");
		$this->db->join("coop_mem_apply as t3", "t1.mem_id = t3.member_id", "inner");
		$this->db->where($where);
		$rs = $this->db->get()->row();
		if(!empty($rs)){
			echo "success";
		}else{
			echo "";
		}
	}

	function coop_report_gov_bank_excel() {
		$arr_data = array();
		set_time_limit (180);
		$this->db->save_queries = FALSE;

		if(@$_GET['start_date']){
			$start_date_arr = explode('/',@$_GET['start_date']);
			$start_day = $start_date_arr[0];
			$start_month = $start_date_arr[1];
			$start_year = $start_date_arr[2];
			$start_year -= 543;
			$start_date = $start_year.'-'.$start_month.'-'.$start_day;
		}
		
		if($_GET["report_type"] == 1) {
			$sql = "SELECT t5.account_id, t6.type_code, t6.type_name, COUNT(*) AS account_count, SUM(t3.transaction_balance) AS transaction_balance
					FROM coop_account_transaction t3
						INNER JOIN (
							SELECT t1.account_id, MAX(t1.transaction_id) AS transaction_id
							FROM coop_account_transaction t1
							INNER JOIN (
								SELECT account_id, MAX(transaction_time) AS transaction_time
								FROM coop_account_transaction
								WHERE transaction_time <= '{$start_date} 23:59:59'
								GROUP BY account_id
							) t2 ON t1.account_id = t2.account_id AND t1.transaction_time = t2.transaction_time
							GROUP BY t1.account_id
						) t4 ON t3.transaction_id = t4.transaction_id
						INNER JOIN coop_maco_account t5 ON t3.account_id = t5.account_id
						INNER JOIN coop_deposit_type_setting t6 ON t5.type_id = t6.type_id
					WHERE 1=1
					AND t3.transaction_list NOT IN ('WI')
					AND t3.transaction_balance > 0
					GROUP BY t6.type_code, t6.type_name
					HAVING transaction_balance > 0";
			$enties = $this->db->query($sql)->result_array();
			$arr_data['data'] = $enties;
			
			$this->load->view('report_deposit_data/coop_report_gov_bank_by_type_excel',$arr_data);
		}
		else {
			$where = "t5.type_id = '".$_GET['type_id']."'";
			if(@$_GET['apply_type_id'] != ''){
				$where .= " AND t6.apply_type_id = '".$_GET['apply_type_id']."'";
			}
            $where .= " AND t3.transaction_balance > 0";
			$where .= " AND t3.transaction_list NOT IN ( 'WI' )  ";
			$rs = $this->db->select("t5.account_id,t5.type_id,t3.transaction_balance,t6.member_id, t6.firstname_th, t6.lastname_th, t7.prename_short")
							->from('coop_account_transaction t3')
							->join("(
								SELECT
									t1.account_id,
									MAX( t1.transaction_id ) AS transaction_id 
								FROM
									coop_account_transaction t1
									INNER JOIN ( SELECT account_id, MAX( transaction_time ) AS transaction_time FROM coop_account_transaction WHERE transaction_time <= '{$start_date} 23:59:59' GROUP BY account_id ) t2 ON t1.account_id = t2.account_id 
									AND t1.transaction_time = t2.transaction_time 
								GROUP BY
									t1.account_id 
								) t4","t3.transaction_id = t4.transaction_id", "inner")
							->join("coop_maco_account t5", "t3.account_id = t5.account_id", "left")
							->join("coop_mem_apply as t6", "t5.mem_id = t6.member_id", "left")
							->join("coop_prename as t7", "t6.prename_id = t7.prename_id", "left")
							->order_by('t5.account_id')
							->where($where)->get()->result_array();
			$account_id = null;
			$all = count($rs);
			$row['data'] = array();
			foreach($rs as $key => $acc) {
				if ($key == $all || $acc['account_id'] != $rs[$key + 1]['account_id']) {
					$acc["member_name"] = $acc['prename_short'].$acc['firstname_th'].'  '.$acc['lastname_th'];
					$row['data'][] = $acc;
				}
			}
			
			$this->db->select('apply_type_id, apply_type_name');
			$this->db->from('coop_mem_apply_type');
			$rs_mem_apply_type = $this->db->get()->result_array();
			$arr_mem_apply_type = array();
			foreach($rs_mem_apply_type AS $key=>$row_mem_apply_type){
				$arr_mem_apply_type[@$row_mem_apply_type['apply_type_id']] = @$row_mem_apply_type['apply_type_name'];
			}
			$arr_data['arr_mem_apply_type'] = $arr_mem_apply_type;
			
			$_rows = $this->db->select(array('type_id','type_name'))->from('coop_deposit_type_setting')->where("type_id = '".$_GET['type_id']."'")->get()->result_array();
			$arr_data["type_name"] = $_rows[0]["type_name"];
			
			$arr_data['paging'] = $paging;
			$arr_data['data'] = $row['data'];
			
			$this->load->view('report_deposit_data/coop_report_gov_bank_excel',$arr_data);
		}
	}

	//รายงานความเคลื่อนไหวบัญชี
	function coop_report_account_transaction() {
		$arr_data = array();

		//Get Account Type
		$arr_data['type_ids'] = $this->db->select(array('type_id','type_name'))->from('coop_deposit_type_setting')->order_by("type_seq")->get()->result_array();

		$this->libraries->template('report_deposit_data/coop_report_account_transaction',$arr_data);
	}

	function coop_report_account_transaction_preview() {
		$arr_data = array();

		if(@$_GET['start_date']){
			$start_date_arr = explode('/',@$_GET['start_date']);
			$start_day = $start_date_arr[0];
			$start_month = $start_date_arr[1];
			$start_year = $start_date_arr[2];
			$start_year -= 543;
			$start_date = $start_year.'-'.$start_month.'-'.$start_day;
		}
		
		if(@$_GET['end_date']){
			$end_date_arr = explode('/',@$_GET['end_date']);
			$end_day = $end_date_arr[0];
			$end_month = $end_date_arr[1];
			$end_year = $end_date_arr[2];
			$end_year -= 543;
			$end_date = $end_year.'-'.$end_month.'-'.$end_day;
		}

		$where = "1=1";
		if(!empty($_GET['type_id'])) $where .= " AND t1.type_id = '".$_GET['type_id']."'";

		$x=0;
		$join_arr = array();
		$join_arr[$x]['table'] = "(SELECT * FROM coop_account_transaction WHERE transaction_time BETWEEN '".$start_date." 00:00:00.000' AND '".$end_date." 23:59:59.000') as t2";
		$join_arr[$x]['condition'] = "t1.account_id = t2.account_id";
		$join_arr[$x]['type'] = 'inner';
		
		$this->paginater_all->type(DB_TYPE);
		$this->paginater_all->select(array(
											't1.account_id',
											't1.account_name',
											't1.mem_id',
											"t2.transaction_balance",
											"t2.transaction_withdrawal",
											"t2.transaction_deposit",
											"t2.transaction_time"
										));
		$this->paginater_all->main_table('coop_maco_account as t1');
		$this->paginater_all->where($where);
		$this->paginater_all->page_now(@$_GET["page"]);
		$this->paginater_all->per_page(100);
		$this->paginater_all->page_link_limit(36);
		$this->paginater_all->join_arr($join_arr);
		$this->paginater_all->order_by('t2.transaction_time, t2.transaction_id');
		$row = $this->paginater_all->paginater_process();

		$paging = $this->pagination_center->paginating(intval($row['page']), $row['num_rows'], $row['per_page'], $row['page_link_limit'],@$_GET);//$page_now = 1, $row_total = 1, $per_page = 20, $page_limit = 20
		$runno = (($row['page'] * 100) - 100) + 1;
		foreach($row['data'] AS $key2=>$value2){
			$row['data'][$key2]['runno'] = $runno;
			$runno++;
		}
		$arr_data['num_rows'] = $row['num_rows'];
		$arr_data['paging'] = $paging;
		$arr_data['data'] = $row['data'];
		$arr_data['page_all'] = ceil($row['num_rows']/$row['per_page']);
		$arr_data['page'] = $row['page'];

		$this->preview_libraries->template_preview('report_deposit_data/coop_report_account_transaction_preview',$arr_data);
	}

	function coop_report_account_transaction_excel() {
		$arr_data = array();
		// set_time_limit (180);
		// $this->db->save_queries = FALSE;

		if(@$_GET['start_date']){
			$start_date_arr = explode('/',@$_GET['start_date']);
			$start_day = $start_date_arr[0];
			$start_month = $start_date_arr[1];
			$start_year = $start_date_arr[2];
			$start_year -= 543;
			$start_date = $start_year.'-'.$start_month.'-'.$start_day;
		}
		
		if(@$_GET['end_date']){
			$end_date_arr = explode('/',@$_GET['end_date']);
			$end_day = $end_date_arr[0];
			$end_month = $end_date_arr[1];
			$end_year = $end_date_arr[2];
			$end_year -= 543;
			$end_date = $end_year.'-'.$end_month.'-'.$end_day;
		}

		$where = "t2.transaction_time BETWEEN '".$start_date." 00:00:00.000' AND '".$end_date." 23:59:59.000'";
		if(!empty($_GET['type_id'])) $where .= " AND t1.type_id = '".$_GET['type_id']."'";

		$row['data'] = $this->db->select(array('t1.account_id', 't1.account_name', 't1.mem_id', "t2.transaction_balance", "t2.transaction_deposit", "t2.transaction_withdrawal", "t2.transaction_time"))
						->from('coop_maco_account as t1')
						->join("(SELECT * FROM coop_account_transaction WHERE transaction_time BETWEEN '".$start_date." 00:00:00.000' AND '".$end_date." 23:59:59.000') as t2", "t1.account_id = t2.account_id", "inner")
						->order_by("t2.transaction_time, t2.transaction_id")
						->where($where)->get()->result_array();

		// print_r(count($row['data'])); exit;
		$arr_data['paging'] = $paging;
		$arr_data['data'] = $row['data'];

		$this->load->view('report_deposit_data/coop_report_account_transaction_excel',$arr_data);	
	}

	function check_coop_report_account_transaction() {
		if(@$_POST['start_date']){
			$start_date_arr = explode('/',@$_POST['start_date']);
			$start_day = $start_date_arr[0];
			$start_month = $start_date_arr[1];
			$start_year = $start_date_arr[2];
			$start_year -= 543;
			$start_date = $start_year.'-'.$start_month.'-'.$start_day;
		}
		
		if(@$_POST['end_date']){
			$end_date_arr = explode('/',@$_POST['end_date']);
			$end_day = $end_date_arr[0];
			$end_month = $end_date_arr[1];
			$end_year = $end_date_arr[2];
			$end_year -= 543;
			$end_date = $end_year.'-'.$end_month.'-'.$end_day;
		}

		$where = "t2.transaction_time BETWEEN '".$start_date." 00:00:00.000' AND '".$end_date." 23:59:59.000'";
		if(!empty($_POST['type_id'])) $where .= " AND t1.type_id = '".$_POST['type_id']."'";

		$rs = $this->db->select(array('t1.account_id', 't1.account_name', 't1.mem_id', "t2.transaction_balance", "t2.transaction_deposit", "t2.transaction_withdrawal", "t2.transaction_time"))
						->from('coop_maco_account as t1')
						->join("(SELECT * FROM coop_account_transaction WHERE transaction_time BETWEEN '".$start_date." 00:00:00.000' AND '".$end_date." 23:59:59.000') as t2", "t1.account_id = t2.account_id", "inner")
						->order_by("t2.transaction_time")
						->where($where)->get()->result_array();

		if(!empty($rs)){
			echo "success";
		}else{
			echo "";
		}
	}

	//รายงานดอกเบี้ย
	function coop_report_account_interest() {
		$arr_data = array();

		//Get Account Type
		$arr_data['type_ids'] = $this->db->select(array('type_id','type_name','type_code'))->from('coop_deposit_type_setting')->order_by("type_seq")->get()->result_array();

		$this->libraries->template('report_deposit_data/coop_report_account_interest',$arr_data);
	}

	function coop_report_account_interest_preview() {
		$arr_data = array();

		if(@$_GET['start_date']){
			$start_date_arr = explode('/',@$_GET['start_date']);
			$start_day = $start_date_arr[0];
			$start_month = $start_date_arr[1];
			$start_year = $start_date_arr[2];
			$start_year -= 543;
			$start_date = $start_year.'-'.$start_month.'-'.$start_day;
		}
		
		if(@$_GET['end_date']){
			$end_date_arr = explode('/',@$_GET['end_date']);
			$end_day = $end_date_arr[0];
			$end_month = $end_date_arr[1];
			$end_year = $end_date_arr[2];
			$end_year -= 543;
			$end_date = $end_year.'-'.$end_month.'-'.$end_day;
		}

		$_rows = $this->db->select(array('type_id','type_name','type_code'))->from('coop_deposit_type_setting')->where("type_id = '".$_GET['type_id']."'")->get()->result_array();
		$arr_data["type_name"] = $_rows[0]["type_code"]." ".$_rows[0]["type_name"];
		
		$where = "1=1 AND t2.transaction_list IN ('IN', 'INT')".(!empty($_GET["ruid"]) ? " AND t2.user_id = '".$_GET["ruid"]."'" : "");
		if(!empty($_GET['type_id'])) $where .= " AND t1.type_id = '".$_GET['type_id']."'";

		$x=0;
		$join_arr = array();
		$join_arr[$x]['table'] = "(SELECT * FROM coop_account_transaction WHERE transaction_time BETWEEN '".$start_date." 00:00:00.000' AND '".$end_date." 23:59:59.000') as t2";
		$join_arr[$x]['condition'] = "t1.account_id = t2.account_id";
		$join_arr[$x]['type'] = 'inner';
		
		$this->paginater_all->type(DB_TYPE);
		$this->paginater_all->select(array(
											't1.account_id',
											't1.account_name',
											't1.mem_id',
											"t2.transaction_balance",
											"t2.transaction_withdrawal",
											"t2.transaction_deposit",
											"t2.transaction_time"
										));
		$this->paginater_all->main_table('coop_maco_account as t1');
		$this->paginater_all->where($where);
		$this->paginater_all->page_now(@$_GET["page"]);
		$this->paginater_all->per_page(100);
		$this->paginater_all->page_link_limit(36);
		$this->paginater_all->join_arr($join_arr);
		$this->paginater_all->order_by('t2.transaction_time, t2.transaction_id');
		$row = $this->paginater_all->paginater_process();

		$paging = $this->pagination_center->paginating(intval($row['page']), $row['num_rows'], $row['per_page'], $row['page_link_limit'],@$_GET);//$page_now = 1, $row_total = 1, $per_page = 20, $page_limit = 20
		$runno = (($row['page'] * 100) - 100) + 1;
		foreach($row['data'] AS $key2=>$value2){
			$row['data'][$key2]['runno'] = $runno;
			$runno++;
		}
		$arr_data['num_rows'] = $row['num_rows'];
		$arr_data['paging'] = $paging;
		$arr_data['data'] = $row['data'];
		$arr_data['page_all'] = ceil($row['num_rows']/$row['per_page']);
		$arr_data['page'] = $row['page'];
		
		if (intval($row['page']) == ceil($row['num_rows']/$row['per_page'])) {
			$rs = $this->db->select("SUM(t2.transaction_deposit) as total_deposit, SUM(t2.transaction_balance) as total_balance")
			->from('coop_maco_account as t1')
			->join("(SELECT * FROM coop_account_transaction WHERE transaction_time BETWEEN '".$start_date." 00:00:00.000' AND '".$end_date." 23:59:59.000') t2", "t1.account_id = t2.account_id", "inner")
			->order_by('t1.account_id')
			->where($where)->get()->result_array();
			$arr_data['total_deposit'] = $rs[0]['total_deposit'];
			$arr_data['total_balance'] = $rs[0]['total_balance'];
		}
		
		$this->preview_libraries->template_preview('report_deposit_data/coop_report_account_interest_preview',$arr_data);
	}

	function coop_report_account_interest_excel() {
		$arr_data = array();
		// set_time_limit (180);
		// $this->db->save_queries = FALSE;

		if(@$_GET['start_date']){
			$start_date_arr = explode('/',@$_GET['start_date']);
			$start_day = $start_date_arr[0];
			$start_month = $start_date_arr[1];
			$start_year = $start_date_arr[2];
			$start_year -= 543;
			$start_date = $start_year.'-'.$start_month.'-'.$start_day;
		}
		
		if(@$_GET['end_date']){
			$end_date_arr = explode('/',@$_GET['end_date']);
			$end_day = $end_date_arr[0];
			$end_month = $end_date_arr[1];
			$end_year = $end_date_arr[2];
			$end_year -= 543;
			$end_date = $end_year.'-'.$end_month.'-'.$end_day;
		}

		$_rows = $this->db->select(array('type_id','type_name','type_code'))->from('coop_deposit_type_setting')->where("type_id = '".$_GET['type_id']."'")->get()->result_array();
		$arr_data["type_name"] = $_rows[0]["type_code"]." ".$_rows[0]["type_name"];
		
		$where = "t2.transaction_list IN ('IN', 'INT') AND t2.transaction_time BETWEEN '".$start_date." 00:00:00.000' AND '".$end_date." 23:59:59.000'".(!empty($_GET["ruid"]) ? " AND t2.user_id = '".$_GET["ruid"]."'" : "");
		if(!empty($_GET['type_id'])) $where .= " AND t1.type_id = '".$_GET['type_id']."'";

		$row['data'] = $this->db->select(array('t1.account_id', 't1.account_name', 't1.mem_id', "t2.transaction_balance", "t2.transaction_deposit", "t2.transaction_withdrawal", "t2.transaction_time"))
						->from('coop_maco_account as t1')
						->join("(SELECT * FROM coop_account_transaction WHERE transaction_time BETWEEN '".$start_date." 00:00:00.000' AND '".$end_date." 23:59:59.000') as t2", "t1.account_id = t2.account_id", "inner")
						->order_by("t2.transaction_time, t2.transaction_id")
						->where($where)->get()->result_array();

		// print_r(count($row['data'])); exit;
		$arr_data['paging'] = $paging;
		$arr_data['data'] = $row['data'];

		$this->load->view('report_deposit_data/coop_report_account_interest_excel',$arr_data);	
	}

	function check_coop_report_account_interest() {
		if(@$_POST['start_date']){
			$start_date_arr = explode('/',@$_POST['start_date']);
			$start_day = $start_date_arr[0];
			$start_month = $start_date_arr[1];
			$start_year = $start_date_arr[2];
			$start_year -= 543;
			$start_date = $start_year.'-'.$start_month.'-'.$start_day;
		}
		
		if(@$_POST['end_date']){
			$end_date_arr = explode('/',@$_POST['end_date']);
			$end_day = $end_date_arr[0];
			$end_month = $end_date_arr[1];
			$end_year = $end_date_arr[2];
			$end_year -= 543;
			$end_date = $end_year.'-'.$end_month.'-'.$end_day;
		}

		$where = "t2.transaction_list IN ('IN', 'INT') AND t2.transaction_time BETWEEN '".$start_date." 00:00:00.000' AND '".$end_date." 23:59:59.000'";
		if(!empty($_POST['type_id'])) $where .= " AND t1.type_id = '".$_POST['type_id']."'";

		$rs = $this->db->select(array('t1.account_id', 't1.account_name', 't1.mem_id', "t2.transaction_balance", "t2.transaction_deposit", "t2.transaction_withdrawal", "t2.transaction_time"))
						->from('coop_maco_account as t1')
						->join("(SELECT * FROM coop_account_transaction WHERE transaction_time BETWEEN '".$start_date." 00:00:00.000' AND '".$end_date." 23:59:59.000') as t2", "t1.account_id = t2.account_id", "inner")
						->order_by("t2.transaction_time")
						->limit(1)
						->where($where)->get()->result_array();

		//echo $this->db->last_query();

		if(!empty($rs)){
			echo "success";
		}else{
			echo "";
		}
	}

	//รายงานเงินฝากครบกำหนด
	function coop_report_account_due() {
		$arr_data = array();

		//Get Account Type
		$arr_data['type_ids'] = $this->db->select(array('type_id','type_name','type_code'))->from('coop_deposit_type_setting')->order_by("type_seq")->get()->result_array();

		$this->libraries->template('report_deposit_data/coop_report_account_due',$arr_data);
	}

	function coop_report_account_due_preview() {
		$arr_data = array();

		if(@$_GET['start_date']){
			$start_date_arr = explode('/',@$_GET['start_date']);
			$start_day = $start_date_arr[0];
			$start_month = $start_date_arr[1];
			$start_year = $start_date_arr[2];
			$start_year -= 543;
			$start_date = $start_year.'-'.$start_month.'-'.$start_day;
		}
		
		$_rows = $this->db->select(array('type_id','type_name','type_code'))->from('coop_deposit_type_setting')->where("type_id = '".$_GET['type_id']."'")->get()->result_array();
		$arr_data["type_name"] = trim($_rows[0]["type_code"]." ".$_rows[0]["type_name"]);
		
		$where = "transaction_list IN ('OPN', 'OPT', 'TRB', 'XD', 'DEP', 'CD') AND
						TIMESTAMPDIFF(MONTH, transaction_time, '".$start_date."') +
						DATEDIFF('".$start_date."', transaction_time + INTERVAL TIMESTAMPDIFF(MONTH, transaction_time, '".$start_date."') MONTH) /
						DATEDIFF(transaction_time + INTERVAL TIMESTAMPDIFF(MONTH, transaction_time, '".$start_date."') + 1 MONTH, transaction_time + INTERVAL TIMESTAMPDIFF(MONTH, transaction_time, '".$start_date."') MONTH) = max_month AND
						t1.account_id NOT IN (SELECT account_id FROM coop_account_transaction WHERE transaction_list = 'W/C' AND transaction_time < '".$start_date."')";
		if(!empty($_GET['type_id'])) $where .= " AND t1.type_id = '".$_GET['type_id']."'";

		$x=0;
		$join_arr = array();
		$join_arr[$x]['table'] = "coop_account_transaction t2";
		$join_arr[$x]['condition'] = "t1.account_id = t2.account_id";
		$join_arr[$x]['type'] = 'inner';
		$x++;
		$join_arr[$x]['table'] = "(SELECT type_id, type_name, type_code, (
												SELECT max_month
												FROM coop_deposit_type_setting_detail
												WHERE coop_deposit_type_setting_detail.type_id = coop_deposit_type_setting.type_id AND start_date <= '".$start_date."'
												ORDER BY start_date
												LIMIT 1) AS max_month
											FROM coop_deposit_type_setting
											WHERE coop_deposit_type_setting.type_id IN (SELECT type_id FROM coop_deposit_type_setting_detail WHERE condition_age IN (1, 2, 3))) t3";
		$join_arr[$x]['condition'] = "t1.type_id = t3.type_id";
		$join_arr[$x]['type'] = 'inner';
		
		$this->paginater_all->type(DB_TYPE);
		$this->paginater_all->select(array(
											't1.account_id',
											't1.account_name',
											't1.mem_id',
											't1.created',
											"t2.transaction_balance",
											"t2.transaction_withdrawal",
											"t2.transaction_deposit",
											"t2.transaction_time",
											"t1.type_id",
											"t3.type_name",
											"t3.type_code"
										));
		$this->paginater_all->main_table('coop_maco_account t1');
		$this->paginater_all->where($where);
		$this->paginater_all->page_now(@$_GET["page"]);
		$this->paginater_all->per_page(100);
		$this->paginater_all->page_link_limit(36);
		$this->paginater_all->join_arr($join_arr);
		$this->paginater_all->order_by('t2.transaction_time, t2.transaction_id');
		$row = $this->paginater_all->paginater_process();

		$paging = $this->pagination_center->paginating(intval($row['page']), $row['num_rows'], $row['per_page'], $row['page_link_limit'],@$_GET);//$page_now = 1, $row_total = 1, $per_page = 20, $page_limit = 20
		$runno = (($row['page'] * 100) - 100) + 1;
		$index = 0;
		foreach($row['data'] AS $key2=>$value2){
			$data_cal = $this->deposit_libraries->cal_deposit_interest(array(
				'account_id' => $value2["account_id"],
				'type_id' => $value2["type_id"],
				'mem_id' => $value2["mem_id"],
				'create_account_date' => $value2["created"]
			), '', $start_date.' 00:00:00', date("d", strtotime($start_date)));
			
			if($row['data'][$key2]['account_id'] == $row['data'][$key2 - 1]['account_id']) {
				$index++;
			}
			else {
				$index = 0;
			}
			$row['data'][$key2]['interest_period'] = $data_cal[$index]["interest_period"];
			$row['data'][$key2]['interest'] = $data_cal[$index]["type_interest"] == 4 ? $data_cal["due"][$index]["interest"] : $data_cal[$index]["transaction_balance"] + $data_cal[$index]["interest"] - $value2["transaction_deposit"];
			
			$row['data'][$key2]['runno'] = $runno;
			$runno++;
		}
		$arr_data['num_rows'] = $row['num_rows'];
		$arr_data['paging'] = $paging;
		$arr_data['data'] = $row['data'];
		$arr_data['page_all'] = ceil($row['num_rows']/$row['per_page']);
		$arr_data['page'] = $row['page'];

		$this->preview_libraries->template_preview('report_deposit_data/coop_report_account_due_preview',$arr_data);
	}

	function coop_report_account_due_excel() {
		$arr_data = array();
		// set_time_limit (180);
		// $this->db->save_queries = FALSE;

		if(@$_GET['start_date']){
			$start_date_arr = explode('/',@$_GET['start_date']);
			$start_day = $start_date_arr[0];
			$start_month = $start_date_arr[1];
			$start_year = $start_date_arr[2];
			$start_year -= 543;
			$start_date = $start_year.'-'.$start_month.'-'.$start_day;
		}

		$_rows = $this->db->select(array('type_id','type_name','type_code'))->from('coop_deposit_type_setting')->where("type_id = '".$_GET['type_id']."'")->get()->result_array();
		$arr_data["type_name"] = trim($_rows[0]["type_code"]." ".$_rows[0]["type_name"]);
		
		$where = "transaction_list IN ('OPN', 'OPT', 'TRB', 'XD', 'DEP', 'CD') AND
						TIMESTAMPDIFF(MONTH, transaction_time, '".$start_date."') +
						DATEDIFF('".$start_date."', transaction_time + INTERVAL TIMESTAMPDIFF(MONTH, transaction_time, '".$start_date."') MONTH) /
						DATEDIFF(transaction_time + INTERVAL TIMESTAMPDIFF(MONTH, transaction_time, '".$start_date."') + 1 MONTH, transaction_time + INTERVAL TIMESTAMPDIFF(MONTH, transaction_time, '".$start_date."') MONTH) = max_month AND
						t1.account_id NOT IN (SELECT account_id FROM coop_account_transaction WHERE transaction_list = 'W/C' AND transaction_time < '".$start_date."')";

		$row['data'] = $this->db->select(array(
							't1.account_id',
							't1.account_name',
							't1.mem_id',
							't1.created',
							"t2.transaction_balance",
							"t2.transaction_withdrawal",
							"t2.transaction_deposit",
							"t2.transaction_time",
							"t1.type_id",
							"t3.type_name",
							"t3.type_code"))
						->from('coop_maco_account as t1')
						->join("coop_account_transaction t2", "t1.account_id = t2.account_id", "inner")
						->join("(SELECT type_id, type_name, type_code, (
										SELECT max_month
										FROM coop_deposit_type_setting_detail
										WHERE coop_deposit_type_setting_detail.type_id = coop_deposit_type_setting.type_id AND start_date <= '".$start_date."'
										ORDER BY start_date
										LIMIT 1) AS max_month
									FROM coop_deposit_type_setting
									WHERE coop_deposit_type_setting.type_id IN (SELECT type_id FROM coop_deposit_type_setting_detail WHERE condition_age IN (1, 2, 3))) t3", "t1.type_id = t3.type_id", "inner")
						->order_by("t2.transaction_time, t2.transaction_id")
						->where($where)->get()->result_array();

		// print_r(count($row['data'])); exit;
		$arr_data['paging'] = $paging;
		
		foreach($row['data'] AS $key2=>$value2){
			$data_cal = $this->deposit_libraries->cal_deposit_interest(array(
				'account_id' => $value2["account_id"],
				'type_id' => $value2["type_id"],
				'mem_id' => $value2["mem_id"],
				'create_account_date' => $value2["created"]
			), '', $start_date.' 00:00:00', date("d", strtotime($start_date)));
			
			if($row['data'][$key2]['account_id'] == $row['data'][$key2 - 1]['account_id']) {
				$index++;
			}
			else {
				$index = 0;
			}
			$row['data'][$key2]['interest_period'] = $data_cal[$index]["interest_period"];
			$row['data'][$key2]['interest'] = $data_cal[$index]["type_interest"] == 4 ? $data_cal["due"][$index]["interest"] : $data_cal[$index]["transaction_balance"] + $data_cal[$index]["interest"] - $value2["transaction_deposit"];
		}
		
		$arr_data['data'] = $row['data'];

		$this->load->view('report_deposit_data/coop_report_account_due_excel',$arr_data);	
	}
	
	//รายงานการทำรายการไม่ถูกต้อง
	function coop_report_error_transaction() {
		$arr_data = array();
		$this->libraries->template('report_deposit_data/coop_report_error_transaction',$arr_data);
	}

	function coop_report_error_transaction_preview() {
		$arr_data = array();

		if(@$_GET['start_date']){
			$start_date_arr = explode('/',@$_GET['start_date']);
			$start_day = $start_date_arr[0];
			$start_month = $start_date_arr[1];
			$start_year = $start_date_arr[2];
			$start_year -= 543;
			$start_date = $start_year.'-'.$start_month.'-'.$start_day;
		}
		
		if(@$_GET['end_date']){
			$end_date_arr = explode('/',@$_GET['end_date']);
			$end_day = $end_date_arr[0];
			$end_month = $end_date_arr[1];
			$end_year = $end_date_arr[2];
			$end_year -= 543;
			$end_date = $end_year.'-'.$end_month.'-'.$end_day;
		}

		$x=0;
		$join_arr = array();
		$join_arr[$x]['table'] = "(SELECT * FROM coop_account_transaction WHERE transaction_time BETWEEN '".$start_date." 00:00:00.000' AND '".$end_date." 23:59:59.000' AND transaction_list = 'ERR') as t2";
		$join_arr[$x]['condition'] = "t1.account_id = t2.account_id";
		$join_arr[$x]['type'] = 'inner';
		$x++;
		$join_arr[$x]['table'] = "coop_user as t3";
		$join_arr[$x]['condition'] = "t2.user_id = t3.user_id";
		$join_arr[$x]['type'] = 'left';
		
		$this->paginater_all->type(DB_TYPE);
		$this->paginater_all->select(array(
											't1.account_id',
											't1.account_name',
											't1.mem_id',
											"t2.transaction_balance",
											"t2.transaction_withdrawal",
											"t2.transaction_deposit",
											"t2.transaction_time",
											"t3.user_name"
										));
		$this->paginater_all->main_table('coop_maco_account as t1');
		$this->paginater_all->where($where);
		$this->paginater_all->page_now(@$_GET["page"]);
		$this->paginater_all->per_page(100);
		$this->paginater_all->page_link_limit(36);
		$this->paginater_all->join_arr($join_arr);
		$this->paginater_all->order_by('t2.transaction_time');
		$row = $this->paginater_all->paginater_process();

		$paging = $this->pagination_center->paginating(intval($row['page']), $row['num_rows'], $row['per_page'], $row['page_link_limit'],@$_GET);//$page_now = 1, $row_total = 1, $per_page = 20, $page_limit = 20
		$runno = (($row['page'] * 100) - 100) + 1;
		foreach($row['data'] AS $key2=>$value2){
			$row['data'][$key2]['runno'] = $runno;
			$runno++;
		}
		$arr_data['num_rows'] = $row['num_rows'];
		$arr_data['paging'] = $paging;
		$arr_data['data'] = $row['data'];
		$arr_data['page_all'] = ceil($row['num_rows']/$row['per_page']);
		$arr_data['page'] = $row['page'];

		$this->preview_libraries->template_preview('report_deposit_data/coop_report_error_transaction_preview',$arr_data);
	}

	function check_coop_report_error_transaction() {
		if(@$_POST['start_date']){
			$start_date_arr = explode('/',@$_POST['start_date']);
			$start_day = $start_date_arr[0];
			$start_month = $start_date_arr[1];
			$start_year = $start_date_arr[2];
			$start_year -= 543;
			$start_date = $start_year.'-'.$start_month.'-'.$start_day;
		}
		
		if(@$_POST['end_date']){
			$end_date_arr = explode('/',@$_POST['end_date']);
			$end_day = $end_date_arr[0];
			$end_month = $end_date_arr[1];
			$end_year = $end_date_arr[2];
			$end_year -= 543;
			$end_date = $end_year.'-'.$end_month.'-'.$end_day;
		}

		$x=0;
		$join_arr = array();
		$join_arr[$x]['table'] = "(SELECT * FROM coop_account_transaction WHERE transaction_time BETWEEN '".$start_date." 00:00:00.000' AND '".$end_date." 23:59:59.000' AND transaction_list = 'ERR') as t2";
		$join_arr[$x]['condition'] = "t1.account_id = t2.account_id";
		$join_arr[$x]['type'] = 'inner';
		$x++;
		$join_arr[$x]['table'] = "coop_user as t3";
		$join_arr[$x]['condition'] = "t2.user_id = t3.user_id";
		$join_arr[$x]['type'] = 'left';
		
		$this->paginater_all->type(DB_TYPE);
		$this->paginater_all->select(array(
											't1.account_id',
											't1.account_name',
											't1.mem_id',
											"t2.transaction_balance",
											"t2.transaction_withdrawal",
											"t2.transaction_deposit",
											"t2.transaction_time",
											"t3.user_name"
										));
		$this->paginater_all->main_table('coop_maco_account as t1');
		$this->paginater_all->where($where);
		$this->paginater_all->page_now(@$_POST["page"]);
		$this->paginater_all->per_page(100);
		$this->paginater_all->page_link_limit(36);
		$this->paginater_all->join_arr($join_arr);
		$this->paginater_all->order_by('t2.transaction_time');
		$row = $this->paginater_all->paginater_process();
		if(!empty($row['data'])){
			echo "success";
		}else{
			echo "";
		}
	}
	
	function coop_report_transaction_emergent_atm_excel(){
		$arr_data = array();
		set_time_limit (180);
		$this->db->save_queries = FALSE;

		if(@$_GET['start_date']){
			$start_date_arr = explode('/',@$_GET['start_date']);
			$start_day = $start_date_arr[0];
			$start_month = $start_date_arr[1];
			$start_year = $start_date_arr[2];
			$start_year -= 543;
			$start_date = $start_year.'-'.$start_month.'-'.$start_day;
		}
		
		if(@$_GET['end_date']){
			$end_date_arr = explode('/',@$_GET['end_date']);
			$end_day = $end_date_arr[0];
			$end_month = $end_date_arr[1];
			$end_year = $end_date_arr[2];
			$end_year -= 543;
			$end_date = $end_year.'-'.$end_month.'-'.$end_day;
		}	
		
		$arr_data['month_arr'] = $this->center_function->month_arr();
		$arr_data['month_short_arr'] = $this->center_function->month_short_arr();
		
		$where = "";
		
		if(@$_GET['start_date'] != '' AND @$_GET['end_date'] == ''){
			$where .= " AND coop_loan_atm_detail.loan_date BETWEEN '".$start_date." 00:00:00.000' AND '".$start_date." 23:59:59.000'";
		}else if(@$_GET['start_date'] != '' AND @$_GET['end_date'] != ''){
			$where .= " AND coop_loan_atm_detail.loan_date BETWEEN '".$start_date." 00:00:00.000' AND '".$end_date." 23:59:59.000'";
		}
		
		$row['data'] = $this->db->select(array(
							'coop_loan_atm_detail.loan_atm_id',
							'coop_loan_atm_detail.member_id',
							'coop_loan_atm_detail.loan_amount',
							'coop_loan_atm_detail.loan_date',
							'coop_loan_atm.contract_number',
							'coop_mem_apply.firstname_th', 
							'coop_mem_apply.lastname_th',
							'coop_prename.prename_short',
							'coop_loan_atm_detail.transaction_at'
							))
		->from("coop_loan_atm_detail")		
		->join("coop_loan_atm","coop_loan_atm_detail.loan_atm_id = coop_loan_atm.loan_atm_id","left")
		->join("coop_mem_apply","coop_mem_apply.member_id = coop_loan_atm.member_id","left")
		->join("coop_prename","coop_prename.prename_id = coop_mem_apply.prename_id","left")
		->where("1=1 {$where}")
		->get()->result_array();
		$arr_data['paging'] = $paging;
		$arr_data['data'] = $row['data'];	
		$this->load->view('report_deposit_data/coop_report_transaction_emergent_atm_excel',$arr_data);
		
	}
	///รายการสรุปการกู้ฉุกเฉิน ATM///
	public function coop_report_summary_emergent_atm(){
		$arr_data = array();
		
		$this->db->select(array('id','mem_group_name'));
		$this->db->from('coop_mem_group');
		$this->db->where("mem_group_type = '1'");
		$row = $this->db->get()->result_array();
		$arr_data['row_mem_group'] = $row;
		
		$this->libraries->template('report_deposit_data/coop_report_summary_emergent_atm',$arr_data);
	}
	
	function coop_report_summary_emergent_atm_preview(){
		$arr_data = array();	
		
		$arr_data['month_arr'] = $this->center_function->month_arr();
		$arr_data['month_short_arr'] = $this->center_function->month_short_arr();
		
		if(@$_GET['start_date']){
			$start_date_arr = explode('/',@$_GET['start_date']);
			$start_day = $start_date_arr[0];
			$start_month = $start_date_arr[1];
			$start_year = $start_date_arr[2];
			$start_year -= 543;
			$get_start_date = $start_year.'-'.$start_month.'-'.$start_day;
		}
		
		if(@$_GET['type_date'] == '1'){
			$this->db->select(array('transaction_datetime'));
			$this->db->from('coop_loan_atm_transaction');
			$this->db->order_by("transaction_datetime ASC");
			$this->db->limit(1);
			$rs_date_loan_atm = $this->db->get()->result_array();
			$date_loan_atm_min  =  date("Y-m-d", strtotime(@$rs_date_loan_atm[0]['transaction_datetime']));
			//print_r($this->db->last_query());
			$start_date = $date_loan_atm_min;			
			$end_date = $get_start_date;
			
		}else{		
			$start_date = $get_start_date;
			$end_date = $get_start_date;
		}
		
		$where = "";		
		if(@$get_start_date != ''){
			$where .= " AND t1.transaction_datetime BETWEEN '".$start_date." 00:00:00' AND '".$start_date." 23:59:59'";
		}
		
		if(@$_GET['level'] != ''){
			$where .= " AND t4.level = '".@$_GET['level']."'";
		}else if(@$_GET['faction'] != ''){
			$where .= " AND t4.faction = '".@$_GET['faction']."'";
		}else if(@$_GET['department'] != ''){
			$where .= " AND t4.department = '".@$_GET['department']."'";
		}	
			
		$x=0;
		$join_arr = array();
		$join_arr[$x]['table'] = 'coop_loan_atm AS t3';
		$join_arr[$x]['condition'] = 't1.loan_atm_id = t3.loan_atm_id';
		$join_arr[$x]['type'] = 'left';
		
		$x++;
		$join_arr[$x]['table'] = 'coop_mem_apply AS t4';
		$join_arr[$x]['condition'] = 't4.member_id = t3.member_id';
		$join_arr[$x]['type'] = 'inner';	
		
		$x++;
		$join_arr[$x]['table'] = 'coop_prename';
		$join_arr[$x]['condition'] = 'coop_prename.prename_id = t4.prename_id';
		$join_arr[$x]['type'] = 'left';
		
		$x++;
		$join_arr[$x]['table'] = 'coop_mem_group as t6';
		$join_arr[$x]['condition'] = 't6.id = t4.level';
		$join_arr[$x]['type'] = 'inner';
		$x++;
		$join_arr[$x]['table'] = 'coop_mem_group as t7';
		$join_arr[$x]['condition'] = 't7.id = t6.mem_group_parent_id';
		$join_arr[$x]['type'] = 'left';
		$x++;
		$join_arr[$x]['table'] = 'coop_mem_group as t8';
		$join_arr[$x]['condition'] = 't8.id = t7.mem_group_parent_id';
		$join_arr[$x]['type'] = 'left';
		
		
     	$this->paginater_all_preview->type(DB_TYPE);
		$this->paginater_all_preview->select(array(
												't4.member_id'
											   ,'t4.firstname_th'
											   ,'t4.lastname_th'
											   ,'t3.contract_number'
												,'t4.department'
												,'t4.faction'
												,'t4.level'
												,'coop_prename.prename_short'
												,'t6.mem_group_id as mem_group_id'
												,'t6.mem_group_name as mem_group_name'
												,'t7.mem_group_name as sub_name'
												,'t8.mem_group_name as main_name'
												,'(SELECT loan_atm_transaction_id FROM coop_loan_atm_transaction AS t2 WHERE t2.loan_atm_id = t1.loan_atm_id ORDER BY t2.loan_atm_transaction_id DESC LIMIT 1) AS loan_atm_transaction_id'
												,'(SELECT loan_atm_id FROM coop_loan_atm_transaction AS t2 WHERE t2.loan_atm_id = t1.loan_atm_id ORDER BY t2.loan_atm_transaction_id DESC LIMIT 1) AS loan_atm_id'
												,'(SELECT loan_amount_balance FROM coop_loan_atm_transaction AS t2 WHERE t2.loan_atm_id = t1.loan_atm_id ORDER BY t2.loan_atm_transaction_id DESC LIMIT 1) AS loan_amount_balance'
												));
		$this->paginater_all_preview->main_table('coop_loan_atm_transaction AS t1');
		$this->paginater_all_preview->where(" 1=1 {$where}");
		$this->paginater_all_preview->page_now(@$_GET["page"]);
		$this->paginater_all_preview->per_page(20);
		$this->paginater_all_preview->page_link_limit(36);
		$this->paginater_all_preview->page_limit_first(30);
		$this->paginater_all_preview->group_by('t1.loan_atm_id');
		$this->paginater_all_preview->order_by('t6.mem_group_id ASC,t4.member_id ASC,t1.loan_atm_id DESC ,t1.loan_atm_transaction_id DESC');
		$this->paginater_all_preview->join_arr($join_arr);
		$row = $this->paginater_all_preview->paginater_process();
		//if(@$_GET['dev']=='dev'){
		//	print_r($this->db->last_query()); //exit;
		//}
		
		foreach($row['data'] AS $key=>$value){
			foreach($value AS $key2=>$value2){
				if($value2['sub_name'] == '' || $value2['sub_name']=='ไม่ระบุ'){					
					$row['data'][$key][$key2]['sub_name'] = $value2['main_name'];
				}else{					
					$row['data'][$key][$key2]['sub_name'] = $value2['sub_name'];
				}
			}
		}
		//exit;
		$arr_data['num_rows'] = $row['num_rows'];
		$arr_data['paging'] = $paging;
		$arr_data['data'] = $row['data'];
		$arr_data['page_all'] = $row['page_all'];
		
		$this->preview_libraries->template_preview('report_deposit_data/coop_report_summary_emergent_atm_preview',$arr_data);
		
	}	

	function check_report_summary_emergent_atm(){	
		if(@$_GET['start_date']){
			$start_date_arr = explode('/',@$_GET['start_date']);
			$start_day = $start_date_arr[0];
			$start_month = $start_date_arr[1];
			$start_year = $start_date_arr[2];
			$start_year -= 543;
			$get_start_date = $start_year.'-'.$start_month.'-'.$start_day;
		}
		
		if(@$_GET['type_date'] == '1'){
			$this->db->select(array('transaction_datetime'));
			$this->db->from('coop_loan_atm_transaction');
			$this->db->order_by("transaction_datetime ASC");
			$this->db->limit(1);
			$rs_date_loan_atm = $this->db->get()->result_array();
			$date_loan_atm_min  =  date("Y-m-d", strtotime(@$rs_date_loan_atm[0]['transaction_datetime']));
			//print_r($this->db->last_query());
			$start_date = $date_loan_atm_min;			
			$end_date = $get_start_date;
			
		}else{		
			$start_date = $get_start_date;
			$end_date = $get_start_date;
		}
		
		$where = "";		
		if(@$get_start_date != ''){
			$where .= " AND t1.transaction_datetime BETWEEN '".$start_date." 00:00:00' AND '".$start_date." 23:59:59'";
		}
		
		if(@$_GET['level'] != ''){
			$where .= " AND t4.level = '".@$_GET['level']."'";
		}else if(@$_GET['faction'] != ''){
			$where .= " AND t4.faction = '".@$_GET['faction']."'";
		}else if(@$_GET['department'] != ''){
			$where .= " AND t4.department = '".@$_GET['department']."'";
		}	
		
		$this->db->select(array(
								'coop_loan_atm_detail.loan_atm_id',
								'coop_loan_atm_detail.member_id',
								'coop_loan_atm_detail.loan_amount',
								'coop_loan_atm_detail.loan_date',
								'coop_loan_atm.contract_number'
								));
		$this->db->from('coop_loan_atm_detail');
		$this->db->join("coop_loan_atm","coop_loan_atm_detail.loan_atm_id = coop_loan_atm.loan_atm_id","left");
		$this->db->where("1=1 {$where}");
		$rs_count = $this->db->get()->result_array();	
		if(!empty($rs_count)){
			echo "success";
		}else{
			echo "";
		}		
	}	
	
	function coop_report_summary_emergent_atm_excel(){
		$arr_data = array();
		set_time_limit (180);
		$this->db->save_queries = FALSE;
		
		$arr_data['month_arr'] = $this->center_function->month_arr();
		$arr_data['month_short_arr'] = $this->center_function->month_short_arr();
		
		if(@$_GET['start_date']){
			$start_date_arr = explode('/',@$_GET['start_date']);
			$start_day = $start_date_arr[0];
			$start_month = $start_date_arr[1];
			$start_year = $start_date_arr[2];
			$start_year -= 543;
			$get_start_date = $start_year.'-'.$start_month.'-'.$start_day;
		}
		
		if(@$_GET['type_date'] == '1'){
			$this->db->select(array('transaction_datetime'));
			$this->db->from('coop_loan_atm_transaction');
			$this->db->order_by("transaction_datetime ASC");
			$this->db->limit(1);
			$rs_date_loan_atm = $this->db->get()->result_array();
			$date_loan_atm_min  =  date("Y-m-d", strtotime(@$rs_date_loan_atm[0]['transaction_datetime']));
			//print_r($this->db->last_query());
			$start_date = $date_loan_atm_min;			
			$end_date = $get_start_date;
			
		}else{		
			$start_date = $get_start_date;
			$end_date = $get_start_date;
		}
		
		$where = "";		
		if(@$get_start_date != ''){
			$where .= " AND t1.transaction_datetime BETWEEN '".$start_date." 00:00:00' AND '".$start_date." 23:59:59'";
		}
		
		if(@$_GET['level'] != ''){
			$where .= " AND t4.level = '".@$_GET['level']."'";
		}else if(@$_GET['faction'] != ''){
			$where .= " AND t4.faction = '".@$_GET['faction']."'";
		}else if(@$_GET['department'] != ''){
			$where .= " AND t4.department = '".@$_GET['department']."'";
		}
			
		$row['data'] = $this->db->select(array(
							't4.member_id'
							,'t4.firstname_th'
							,'t4.lastname_th'
							,'t3.contract_number'
							,'t4.department'
							,'t4.faction'
							,'t4.level'
							,'coop_prename.prename_short'
							,'t6.mem_group_id as mem_group_id'
							,'t6.mem_group_name as mem_group_name'
							,'t7.mem_group_name as sub_name'
							,'t8.mem_group_name as main_name'
							,'(SELECT loan_atm_transaction_id FROM coop_loan_atm_transaction AS t2 WHERE t2.loan_atm_id = t1.loan_atm_id ORDER BY t2.loan_atm_transaction_id DESC LIMIT 1) AS loan_atm_transaction_id'
							,'(SELECT loan_atm_id FROM coop_loan_atm_transaction AS t2 WHERE t2.loan_atm_id = t1.loan_atm_id ORDER BY t2.loan_atm_transaction_id DESC LIMIT 1) AS loan_atm_id'
							,'(SELECT loan_amount_balance FROM coop_loan_atm_transaction AS t2 WHERE t2.loan_atm_id = t1.loan_atm_id ORDER BY t2.loan_atm_transaction_id DESC LIMIT 1) AS loan_amount_balance'
							))
		->from("coop_loan_atm_transaction AS t1")		
		->join("coop_loan_atm AS t3","t1.loan_atm_id = t3.loan_atm_id","left")
		->join("coop_mem_apply AS t4","t4.member_id = t3.member_id","inner")		
		->join("coop_prename","coop_prename.prename_id = t4.prename_id","left")
		->join("coop_mem_group as t6","t6.id = t4.level","inner")
		->join("coop_mem_group as t7","t7.id = t6.mem_group_parent_id","left")
		->join("coop_mem_group as t8","t8.id = t7.mem_group_parent_id","left")		
		->where("1=1 {$where}")
		->group_by('t1.loan_atm_id')
		->order_by("t6.mem_group_id ASC,t4.member_id ASC,t1.loan_atm_id DESC ,t1.loan_atm_transaction_id DESC")
		->get()->result_array();
		
		//echo '<pre>'; print_r($row['data']); echo '</pre>';
		foreach($row['data'] AS $key=>$value){
			if($value['sub_name'] == '' || $value['sub_name']=='ไม่ระบุ'){					
				$row['data'][$key]['sub_name'] = $value['main_name'];
			}else{					
				$row['data'][$key]['sub_name'] = $value['sub_name'];
			}
		}
		
		$arr_data['paging'] = $paging;
		$arr_data['data'] = $row['data'];	
		
		$this->load->view('report_deposit_data/coop_report_summary_emergent_atm_excel',$arr_data);
		
	}

	function coop_report_cancel_receipt() {
		$arr_data = array();
		$arr_data["month_arr"] = $this->month_arr;

		$this->libraries->template('report_deposit_data/coop_report_cancel_receipt',$arr_data);
	}

	function check_coop_report_cancel_receipt() {
		$whereType = '';
		if($_POST['type'] == 'paid') {
			$whereType .= " AND non_pay_status = '2'";
		} else if ($_POST['type'] == 'non_pay') {
			$whereType .= " AND non_pay_status = '1'";
		}

		$rs = $this->db->select("non_pay_id")
						->from("coop_non_pay")
						->where("non_pay_month = '".$_POST['month']."' AND non_pay_year = '".$_POST['year']."'".$whereType)
						->get()->row();

		if(!empty($rs)){
			echo "success";
		}else{
			"";
		}
	}

	function coop_report_cancel_receipt_preview_backup_06_11_2018(){
		$arr_data = array();	

		$arr_data['month_arr'] = $this->center_function->month_arr();

		$receipts = $this->db->select("t1.member_id, t2.deduct_code, t2.non_pay_amount as pay_amount, t2.non_pay_amount_balance, t2.loan_id, t2.loan_atm_id, t2.cremation_type_id, t2.pay_type
										, t3.contract_number, t4.contract_number as atm_contract_number")
								->from("coop_non_pay as t1")
								->join("coop_non_pay_detail as t2", "t1.non_pay_id = t2.non_pay_id", 'inner')
								->join("coop_loan as t3", "t2.loan_id = t3.id", "left")
								->join("coop_loan_atm as t4", "t2.loan_atm_id = t4.loan_atm_id", "left")
								->where("t1.non_pay_month = '".$_GET['month']."' AND t1.non_pay_year = '".$_GET['year']."'")
								->order_by("t2.member_id")
								->get()->result_array();
		$member_receipt_ids = array_filter(array_column($receipts, 'member_id'));

		//Get member detail
		$details = $this->db->select("t1.member_id, t1.firstname_th, t1.lastname_th, t2.mem_group_name as level_name, t3.mem_group_name as department_name, t4.prename_full")
									->from("coop_mem_apply as t1")
									->join("coop_mem_group as t2", "t1.level = t2.id", "left")
									->join("coop_mem_group as t3", "t1.department = t3.id", "left")
									->join("coop_prename as t4", "t1.prename_id = t4.prename_id", "left")
									->where("t1.member_id IN (".implode(",", $member_receipt_ids).")")
									->get()->result_array();
		$member_detail_ids = array_column($details, 'member_id');

		//Get Total Dept non pay(Exclude loan principal)
		$deptTotals = $this->db->select("t1.member_id, sum(t1.non_pay_amount), sum(t1.non_pay_amount_balance) as sum")
							->from("coop_non_pay_detail as t1")
							->where("t1.member_id IN  (".implode(",", $member_receipt_ids).") AND (!(t1.deduct_code = 'LOAN' AND t1.pay_type = 'principal') AND !(t1.deduct_code = 'ATM' AND t1.pay_type = 'principal'))")
							->group_by("t1.member_id")
							->get()->result_array();
		$member_dept_ids = array_column($deptTotals, 'member_id');

		$dept_loan_total = $this->db->select("member_id, sum(loan_amount_balance) as sum")
									->from("coop_loan")
									->where("member_id IN  (".implode(",", $member_receipt_ids).") AND loan_status = 1")
									->group_by("member_id")
									->get()->result_array();
		$loan_member_ids = array_column($dept_loan_total, 'member_id');

		$dept_atm_total = $this->db->select("member_id, sum(total_amount) as sum_total, sum(total_amount_balance) as sum_balance")
									->from("coop_loan_atm")
									->where("member_id IN  (".implode(",", $member_receipt_ids).") AND loan_atm_status = 1")
									->group_by("member_id")
									->get()->result_array();
		$atm_member_ids = array_column($dept_atm_total, 'member_id');

		$datas = array();
		$prev_member_id = "x";
		$member_total = array();
		$dept_total = array();
		$receipts_count = count($receipts);
		$row_count = 0;
		$max_first_page = 20;
		$max_per_page = 25;
		foreach($receipts as $index => $receipt) {
			if(($index >= 1 && $prev_member_id != $receipt["member_id"])) {
				$row_count++;
				$data = array();
				$data["member_id"] = $prev_member_id;
				$data["contract"] = "รวมเงิน";
				$data["pay_amount"] = $member_total['pay_amount'];
				$data["real_pay_amount"] = $member_total['real_pay_amount'];
				if ($row_count <= $max_first_page) {
					$page = 1;
				} else {
					$page = ceil(($row_count-$max_first_page) / $max_per_page)+1;
				}
				$datas[$page][] = $data;
				$dept_total[$data["member_id"]] = $member_total['pay_amount'] - $member_total["real_pay_amount"];
				$member_total = array();
			}
			$row_count++;
			$data = array();
			$detail = $details[array_search($receipt["member_id"],$member_detail_ids)];
			$deptTotal = $deptTotals[array_search($receipt["member_id"],$member_dept_ids)];
			$dept_loan = $dept_loan_total[array_search($receipt["member_id"],$dept_loan_total)];
			$dept_atm = $dept_atm_total[array_search($receipt["member_id"],$dept_atm_total)];
			$data["member_id"] = $receipt["member_id"];
			$data["member_name"] = $detail["prename_full"].$detail["firstname_th"]." ".$detail['lastname_th'];
			$data["department_name"] = $detail["department_name"].",".$detail["level_name"];
			$data["pay_amount"] = $receipt["pay_amount"];
			$data["real_pay_amount"] = $receipt["pay_amount"] - $receipt["non_pay_amount_balance"];
			$data["dept_total"] = $deptTotal['sum'];
			if(!empty($dept_loan)) {
				$data["dept_total"] += $dept_loan['sum'];
			}
			if(!empty($dept_atm)) {
				$data["dept_total"] += ($dept_atm['sum_total'] - $dept_atm['sum_balance']);
			}
			$member_total['pay_amount'] += $receipt["pay_amount"];
			$member_total["real_pay_amount"] += $data["real_pay_amount"];
			if($receipt['deduct_code'] == "SHARE") {
				$data["contract"] = "หุ้น";
			} else if ($receipt['deduct_code'] == "LOAN") {
				if($receipt["pay_type"] == "interest") {
					$data["contract"] = $receipt["contract_number"]."[ดอก]";
				} else {
					$data["contract"] = $receipt["contract_number"]."[ต้น]";
				}
			} else if ($receipt['deduct_code'] == "ATM") {
				if($receipt["pay_type"] == "interest") {
					$data["contract"] = $receipt["atm_contract_number"]."[ดอก]";
				} else {
					$data["contract"] = $receipt["atm_contract_number"]."[ต้น]";
				}
			} else if ($receipt['deduct_code'] == "DEPOSIT") {
				$data["contract"] = "เงินฝาก";
			} else if ($receipt['deduct_code'] == "GUARANTEE_AMOUNT") {
				$data["contract"] = "ชำระหนี้ค้ำประกัน";
			} else if ($receipt['deduct_code'] == "REGISTER_FEE") {
				$data["contract"] = "ค่าธรรมเนียมแรกเข้า";
			} else if ($receipt['deduct_code'] == "CREMATION") {
				$data["contract"] = "ฌสอ สป";
			}
			
			if ($row_count <= $max_first_page) {
				$page = 1;
			} else {
				$page = ceil(($row_count-$max_first_page) / $max_per_page)+1;
			}
			$datas[$page][] = $data;
			$prev_member_id = $data["member_id"];

			if($receipts_count == ($index+1)) {
				$row_count++;
				$data = array();
				$data["member_id"] = $prev_member_id;
				$data["contract"] = "รวมเงิน";
				$data["pay_amount"] = $member_total['pay_amount'];
				$data["real_pay_amount"] = $member_total['real_pay_amount'];
				if ($row_count <= $max_first_page) {
					$page = 1;
				} else {
					$page = ceil(($row_count-$max_first_page) / $max_per_page)+1;
				}
				$datas[$page][] = $data;
				$dept_total[$data["member_id"]] = $member_total['pay_amount'] - $member_total["real_pay_amount"];
				$member_total = array();
			}
		}

		$page_all = ceil($row_count / $max_per_page);

		$arr_data['dept_total'] = $dept_total;
		$arr_data['datas'] = $datas;
		$arr_data['page_all'] = $page_all;
		
		$this->preview_libraries->template_preview('report_deposit_data/coop_report_cancel_receipt_preview',$arr_data);
	}

	function coop_report_cancel_receipt_preview(){
		$arr_data = array();	

		$arr_data['month_arr'] = $this->center_function->month_arr();

		$whereType = '';
		if($_GET['type'] == 'paid') {
			$whereType .= " AND t1.non_pay_status = '2'";
		} else if ($_GET['type'] == 'non_pay') {
			$whereType .= " AND t1.non_pay_status = '1'";
		}

		/*$receipts = $this->db->select("t1.member_id, t2.deduct_code, t2.non_pay_amount as pay_amount, t2.non_pay_amount_balance, t2.loan_id, t2.loan_atm_id, t2.cremation_type_id, t7.pay_type
										, t3.contract_number, t4.contract_number as atm_contract_number, t5.loan_type_id")
								->from("coop_non_pay as t1")
								->join("coop_non_pay_detail as t2", "t1.non_pay_id = t2.non_pay_id", 'inner')
								->join("coop_loan as t3", "t2.loan_id = t3.id", "left")
								->join("coop_loan_atm as t4", "t2.loan_atm_id = t4.loan_atm_id", "left")
								->join('coop_loan_name as t5', 't3.loan_type = t5.loan_name_id', 'left')
								->join('coop_finance_month_profile AS t6', 't6.profile_month = t1.non_pay_month AND t6.profile_year = t1.non_pay_year', 'inner')
								->join('coop_finance_month_detail AS t7', 't6.profile_id = t7.profile_id AND t7.member_id = t1.member_id AND t7.deduct_code = t2.deduct_code', 'inner')
								->where("t1.non_pay_month = '".$_GET['month']."' AND t1.non_pay_year = '".$_GET['year']."' AND t1.non_pay_amount_balance <> 0 ".$whereType)
								->order_by("t2.member_id")
								->group_by("t7.deduct_code,t7.member_id,t7.pay_type")
								->get()->result_array();
		*/
		//$whereType .= " AND t7.member_id IN ('003210','014712') ";
		$receipts = $this->db->select("t1.member_id, t7.deduct_code, t7.pay_amount as pay_amount, t2.non_pay_amount_balance, t2.loan_id, t2.loan_atm_id, t2.cremation_type_id, t7.pay_type
										, t3.contract_number, t4.contract_number as atm_contract_number, t5.loan_type_id")
								->from("coop_finance_month_detail AS t7 ")
								->join("coop_finance_month_profile AS t6", "t6.profile_id = t7.profile_id", 'left')
								->join("coop_non_pay AS t1", "t6.profile_month = t1.non_pay_month AND t6.profile_year = t1.non_pay_year AND t7.member_id = t1.member_id", "left")
								->join("coop_non_pay_detail AS t2", "t1.non_pay_id = t2.non_pay_id AND t7.deduct_code = t2.deduct_code", "left")
								->join("coop_loan AS t3", "t7.loan_id = t3.id", "left")
								->join("coop_loan_atm AS t4", "t7.loan_atm_id = t4.loan_atm_id", "left")
								->join("coop_loan_name AS t5", "t3.loan_type = t5.loan_name_id", "left")
								->where("t1.non_pay_month = '".$_GET['month']."' AND t1.non_pay_year = '".$_GET['year']."' AND t1.non_pay_amount_balance <> 0 ".$whereType)
								->order_by("t7.member_id")
								->group_by("t7.deduct_code,t7.member_id,t7.pay_type")
								->get()->result_array();		
								
		$member_receipt_ids = array_filter(array_column($receipts, 'member_id'));
		if(@$_GET['dev'] == 'dev'){
			echo $this->db->last_query(); echo '<br>'; exit;
		}
		//Get member detail
		$details = $this->db->select("t1.member_id, t1.firstname_th, t1.lastname_th, t2.mem_group_name as level_name, t3.mem_group_name as department_name, t4.prename_full")
									->from("coop_mem_apply as t1")
									->join("coop_mem_group as t2", "t1.level = t2.id", "left")
									->join("coop_mem_group as t3", "t1.department = t3.id", "left")
									->join("coop_prename as t4", "t1.prename_id = t4.prename_id", "left")
									->where("t1.member_id IN (".implode(",", $member_receipt_ids).")")
									->get()->result_array();
		$member_detail_ids = array_column($details, 'member_id');

		//Get Total Dept non pay(Exclude loan principal)
		// $deptTotals = $this->db->select("t1.member_id, sum(t1.non_pay_amount), sum(t1.non_pay_amount_balance) as sum")
		// 					->from("coop_non_pay_detail as t1")
		// 					->where("t1.member_id IN  (".implode(",", $member_receipt_ids).") AND (!(t1.deduct_code = 'LOAN' AND t1.pay_type = 'principal') AND !(t1.deduct_code = 'ATM' AND t1.pay_type = 'principal'))")
		// 					->group_by("t1.member_id")
		// 					->get()->result_array();
		// $member_dept_ids = array_column($deptTotals, 'member_id');

		$dept_loan_total = $this->db->select("member_id, sum(loan_amount_balance) as sum")
									->from("coop_loan")
									->where("member_id IN  (".implode(",", $member_receipt_ids).") AND loan_status = 1")
									->group_by("member_id")
									->get()->result_array();
		$loan_member_ids = array_column($dept_loan_total, 'member_id');

		$dept_atm_total = $this->db->select("member_id, sum(total_amount) as sum_total, sum(total_amount_balance) as sum_balance")
									->from("coop_loan_atm")
									->where("member_id IN  (".implode(",", $member_receipt_ids).") AND loan_atm_status = 1")
									->group_by("member_id")
									->get()->result_array();
		$atm_member_ids = array_column($dept_atm_total, 'member_id');
		
		//
		$coop_finance_month_profile = $this->db->select("profile_id")
									->from("coop_finance_month_profile")
									->where("profile_month = '".(int)$_GET['month']."' AND profile_year = '".$_GET['year']."'")
									->get()->result_array();		
		$finance_month_profile = $coop_finance_month_profile[0]['profile_id'];
		//echo $finance_month_profile.'<hr>';							
		//echo $this->db->last_query(); echo '<br>';	
		$finance_month_detail = $this->db->select("member_id,pay_amount,real_pay_amount,pay_type,deduct_code,loan_id,loan_atm_id")
									->from("coop_finance_month_detail")
									->where("profile_id = '".$finance_month_profile."' AND member_id IN  (".implode(",", $member_receipt_ids).")")
									->get()->result_array();
		//echo $this->db->last_query(); echo '<br>';	exit;								
		$arr_finance_month_detail = array();
		foreach($finance_month_detail AS $key_finance=>$val_finance){			
			$arr_finance_month_detail[$val_finance['member_id']][$val_finance['deduct_code']][$val_finance['pay_type']]['pay_type'] = $val_finance['pay_type'];
			$arr_finance_month_detail[$val_finance['member_id']][$val_finance['deduct_code']][$val_finance['pay_type']]['pay_amount'] = $val_finance['pay_amount'];
			$arr_finance_month_detail[$val_finance['member_id']][$val_finance['deduct_code']][$val_finance['pay_type']]['real_pay_amount'] = $val_finance['real_pay_amount'];
		}
		//echo '<pre>'; print_r($arr_finance_month_detail); echo '</pre>';
		//echo $this->db->last_query(); echo '<br>';										
		//$finance_member_ids = array_column($finance_month_detail, 'member_id');
		//echo '<pre>'; print_r($finance_month_detail); echo '</pre>';
		//echo '<pre>'; print_r($finance_member_ids); echo '</pre>';
		
		
		//นับจำนวน ผิดนัดครั้งที่
		$date_last_month = date('Y-m-t',strtotime((@$_GET['year']-543).'-'.sprintf("%02d",@$_GET['month']).'-01'));
		
		$non_pay = $this->db->select("t1.non_pay_id,
												t1.member_id,
												t1.non_pay_amount,
												t1.non_pay_month,
												t1.non_pay_year,
												t1.non_pay_status,
												t1.non_pay_amount_balance")
									->from("coop_non_pay AS t1")
									->where("t1.member_id IN (".implode(",", $member_receipt_ids).") AND CONCAT(t1.non_pay_year,RIGHT(CONCAT('00', t1.non_pay_month), 2)) <= '".$_GET['year'].sprintf("%02d",$_GET['month'])."'")
									->get()->result_array();		
		$arr_non_pay = array();
		$arr_pay = array();
		foreach($non_pay AS $key_non_pay=>$val_non_pay){			
			$check_receipt = $this->db->select("t1.non_pay_id,
											t1.receipt_id,
											t1.receipt_id,
											t2.sumcount,
											t2.receipt_status,
											t2.receipt_datetime,
											t2.member_id")
									->from("coop_non_pay_receipt AS t1")
									->join("coop_receipt AS t2","t1.receipt_id = t2.receipt_id","left")
									->where("t1.non_pay_id = '".$val_non_pay['non_pay_id']."' AND t2.receipt_datetime <= '".$date_last_month."'")
									->get()->result_array();
			$sumcount = 0;	
			$arr_pay[@$val_non_pay['member_id']][$val_non_pay['non_pay_id']]['non_pay_id'] = @$val_non_pay['non_pay_id'];
			$arr_pay[@$val_non_pay['member_id']][$val_non_pay['non_pay_id']]['member_id'] = @$val_non_pay['member_id'];
			$arr_pay[@$val_non_pay['member_id']][$val_non_pay['non_pay_id']]['pay_amount'] = 0;
			foreach($check_receipt AS $key_check=>$val_check){
				$sumcount += @$val_check['sumcount'];				
				$arr_pay[@$val_non_pay['member_id']][$val_non_pay['non_pay_id']]['pay_amount'] = @$sumcount;
				
			}	
			$arr_non_pay[$val_non_pay['member_id']][$val_non_pay['non_pay_id']]['non_pay_id'] = $val_non_pay['non_pay_id'];
			$arr_non_pay[$val_non_pay['member_id']][$val_non_pay['non_pay_id']]['non_pay_amount'] = $val_non_pay['non_pay_amount'];
			$arr_non_pay[$val_non_pay['member_id']][$val_non_pay['non_pay_id']]['member_id'] = $val_non_pay['member_id'];
		}
		
		$arr_check_non_pay = array();
		foreach($arr_non_pay AS $key_non_pay => $val_non_pay){
			$num_non_pay = 0;
			foreach($val_non_pay AS $key_non_pay_id => $val_non_pay_detail){
				if(@$key_non_pay == $arr_pay[@$key_non_pay][$key_non_pay_id]['member_id']){
					if(@$val_non_pay_detail['non_pay_amount'] != $arr_pay[@$key_non_pay][$key_non_pay_id]['pay_amount']){
						$num_non_pay++;
						$arr_check_non_pay[$key_non_pay]['num_non_pay'] = $num_non_pay;
						
					}
				}
			}
		}
		

		$datas = array();
		$prev_member_id = "xx";
		$member_total = array();
		$dept_total = array();
		$receipts_count = count($receipts);
		$row_count = 0;
		foreach($receipts as $index => $receipt) {
			$row_count++;
			$detail = $details[array_search($receipt["member_id"],$member_detail_ids)];
			$dept_loan = in_array($receipt["member_id"], $loan_member_ids) ? $dept_loan_total[array_search($receipt["member_id"],$loan_member_ids)] : null;
			$dept_atm = in_array($receipt["member_id"], $atm_member_ids) ? $dept_atm_total[array_search($receipt["member_id"],$atm_member_ids)] : null;

			$datas[$receipt["member_id"]]["member_id"] = $receipt["member_id"];
			$datas[$receipt["member_id"]]["member_name"] = $detail["prename_full"].$detail["firstname_th"]." ".$detail['lastname_th'];
			$datas[$receipt["member_id"]]["department_name"] = $detail["department_name"].",".$detail["level_name"];
			$datas[$receipt["member_id"]]["dept_total"] = 0;
			
			$datas[$receipt["member_id"]]["num_non_pay"] = $arr_check_non_pay[$receipt["member_id"]]['num_non_pay'];
			if(!empty($dept_loan)) {
				$datas[$receipt["member_id"]]["dept_total"] += $dept_loan['sum'];
			}
			if(!empty($dept_atm)) {
				$datas[$receipt["member_id"]]["dept_total"] += ($dept_atm['sum_total'] - $dept_atm['sum_balance']);
			}
			if($receipt['deduct_code'] == "LOAN") {
				$row_count++;
				$data = array();
				if($receipt["pay_type"] == "interest") {
					$data["contract"] = $receipt["contract_number"]."[ดอก]";
				} else {
					$data["contract"] = $receipt["contract_number"]."[ต้น]";
				}
				//$data["pay_amount"] = $receipt["pay_amount"];
				//$data["real_pay_amount"] = $receipt["pay_amount"] - $receipt["non_pay_amount_balance"];
				//$datas[$receipt["member_id"]]["total"] += ($receipt["pay_amount"] - $data["real_pay_amount"]);
				$data["pay_amount"] = $arr_finance_month_detail[$receipt["member_id"]][$receipt['deduct_code']][$receipt["pay_type"]]['pay_amount'];					
				$data["real_pay_amount"] = $arr_finance_month_detail[$receipt["member_id"]][$receipt['deduct_code']][$receipt["pay_type"]]['real_pay_amount'];					
				$datas[$receipt["member_id"]]["total"] += ($data["pay_amount"] - $data["real_pay_amount"]);
				$datas[$receipt["member_id"]][$receipt["loan_type_id"]][] = $data;
			} else if ($receipt['deduct_code'] == "ATM") {
				$row_count++;
				$data = array();
				if($receipt["pay_type"] == "interest") {
					$data["contract"] = $receipt["atm_contract_number"]."[ดอก]";
				} else {
					$data["contract"] = $receipt["atm_contract_number"]."[ต้น]";
				}
				//$data["pay_amount"] = $receipt["pay_amount"];
				//$data["real_pay_amount"] = $receipt["pay_amount"] - $receipt["non_pay_amount_balance"];
				//$datas[$receipt["member_id"]]["total"] += ($receipt["pay_amount"] - $data["real_pay_amount"]);
				$data["pay_amount"] = $arr_finance_month_detail[$receipt["member_id"]][$receipt['deduct_code']][$receipt["pay_type"]]['pay_amount'];					
				$data["real_pay_amount"] = $arr_finance_month_detail[$receipt["member_id"]][$receipt['deduct_code']][$receipt["pay_type"]]['real_pay_amount'];					
				$datas[$receipt["member_id"]]["total"] += ($data["pay_amount"] - $data["real_pay_amount"]);
				$datas[$receipt["member_id"]]["7"][] = $data;
			} else if($receipt['deduct_code'] == "SHARE") {
				$row_count++;
				$data["contract"] = "หุ้น";
				//$data["pay_amount"] = $receipt["pay_amount"];		
				//$data["real_pay_amount"] = $receipt["pay_amount"] - $receipt["non_pay_amount_balance"];			
				//$datas[$receipt["member_id"]]["total"] += ($receipt["pay_amount"] - $data["real_pay_amount"]);	
				$data["pay_amount"] = $arr_finance_month_detail[$receipt["member_id"]][$receipt['deduct_code']][$receipt["pay_type"]]['pay_amount'];					
				$data["real_pay_amount"] = $arr_finance_month_detail[$receipt["member_id"]][$receipt['deduct_code']][$receipt["pay_type"]]['real_pay_amount'];					
				$datas[$receipt["member_id"]]["total"] += ($data["pay_amount"] - $data["real_pay_amount"]);
				$datas[$receipt["member_id"]][$receipt['deduct_code']][] = $data;
			} else if ($receipt['deduct_code'] == "DEPOSIT") {
				$row_count++;
				$data["contract"] = "เงินฝาก";
				//$data["pay_amount"] = $receipt["pay_amount"];
				//$data["real_pay_amount"] = $receipt["pay_amount"] - $receipt["non_pay_amount_balance"];
				//$datas[$receipt["member_id"]]["total"] += ($receipt["pay_amount"] - $data["real_pay_amount"]);
				$data["pay_amount"] = $arr_finance_month_detail[$receipt["member_id"]][$receipt['deduct_code']][$receipt["pay_type"]]['pay_amount'];					
				$data["real_pay_amount"] = $arr_finance_month_detail[$receipt["member_id"]][$receipt['deduct_code']][$receipt["pay_type"]]['real_pay_amount'];					
				$datas[$receipt["member_id"]]["total"] += ($data["pay_amount"] - $data["real_pay_amount"]);
				$datas[$receipt["member_id"]][$receipt['deduct_code']][] = $data;
			} else if ($receipt['deduct_code'] == "GUARANTEE_AMOUNT") {
				$row_count++;
				$data["contract"] = "ชำระหนี้ค้ำประกัน";
				//$data["pay_amount"] = $receipt["pay_amount"];
				//$data["real_pay_amount"] = $receipt["pay_amount"] - $receipt["non_pay_amount_balance"];
				//$datas[$receipt["member_id"]]["total"] += ($receipt["pay_amount"] - $data["real_pay_amount"]);
				$data["pay_amount"] = $arr_finance_month_detail[$receipt["member_id"]][$receipt['deduct_code']][$receipt["pay_type"]]['pay_amount'];					
				$data["real_pay_amount"] = $arr_finance_month_detail[$receipt["member_id"]][$receipt['deduct_code']][$receipt["pay_type"]]['real_pay_amount'];					
				$datas[$receipt["member_id"]]["total"] += ($data["pay_amount"] - $data["real_pay_amount"]);
				$datas[$receipt["member_id"]][$receipt['deduct_code']][] = $data;
			} else if ($receipt['deduct_code'] == "REGISTER_FEE") {
				$row_count++;
				$data["contract"] = "ค่าธรรมเนียมแรกเข้า";
				//$data["pay_amount"] = $receipt["pay_amount"];
				//$data["real_pay_amount"] = $receipt["pay_amount"] - $receipt["non_pay_amount_balance"];
				//$datas[$receipt["member_id"]]["total"] += ($receipt["pay_amount"] - $data["real_pay_amount"]);
				$data["pay_amount"] = $arr_finance_month_detail[$receipt["member_id"]][$receipt['deduct_code']][$receipt["pay_type"]]['pay_amount'];					
				$data["real_pay_amount"] = $arr_finance_month_detail[$receipt["member_id"]][$receipt['deduct_code']][$receipt["pay_type"]]['real_pay_amount'];					
				$datas[$receipt["member_id"]]["total"] += ($data["pay_amount"] - $data["real_pay_amount"]);
				$datas[$receipt["member_id"]][$receipt['deduct_code']][] = $data;
			} else if ($receipt['deduct_code'] == "CREMATION") {
				$row_count++;
				$data["contract"] = "ฌสอ สป";
				//$data["pay_amount"] = $receipt["pay_amount"];
				//$data["real_pay_amount"] = $receipt["pay_amount"] - $receipt["non_pay_amount_balance"];
				//$datas[$receipt["member_id"]]["total"] += ($receipt["pay_amount"] - $data["real_pay_amount"]);
				$data["pay_amount"] = $arr_finance_month_detail[$receipt["member_id"]][$receipt['deduct_code']][$receipt["pay_type"]]['pay_amount'];					
				$data["real_pay_amount"] = $arr_finance_month_detail[$receipt["member_id"]][$receipt['deduct_code']][$receipt["pay_type"]]['real_pay_amount'];					
				$datas[$receipt["member_id"]]["total"] += ($data["pay_amount"] - $data["real_pay_amount"]);
				$datas[$receipt["member_id"]][$receipt['deduct_code']][] = $data;
			}					

		}

		$sort_data = array();
		$index = 0;
		$max_first_page = 20;
		$max_per_page = 24;
		foreach($datas as $key => $member_data){
				$rowCount = 0;
				$mem_total = array();
				
				if(!empty($member_data["SHARE"])) {
					foreach($member_data["SHARE"] as $row) {
						$rowCount++;
						$index++;
						$mem_total['pay_amount'] += $row['pay_amount'];
						$mem_total['real_pay_amount'] += $row['real_pay_amount'];

						if ($index <= $max_first_page) {
							$page = 1;
						} else {
							$page = ceil(($index-$max_first_page) / $max_per_page)+1;
						}
						$data = array();
						$data['member_id'] = $member_data['member_id'];
						$data['member_name'] = $member_data['member_name'];
						$data['department_name'] = $member_data['department_name'];
						$data['dept_total'] = $member_data['dept_total'];
						$data['num_non_pay'] = $member_data['num_non_pay'];
						$data['contract'] = $row['contract'];
						$data['pay_amount'] = $row['pay_amount'];
						$data['real_pay_amount'] = $row['real_pay_amount'];
						$sort_data[$page][] = $data;
					}
				}
				if(!empty($member_data["8"])) {
					foreach($member_data["8"] as $row) {
						$rowCount++;
						$index++;
						$mem_total['pay_amount'] += $row['pay_amount'];
						$mem_total['real_pay_amount'] += $row['real_pay_amount'];

						if ($index <= $max_first_page) {
							$page = 1;
						} else {
							$page = ceil(($index-$max_first_page) / $max_per_page)+1;
						}
						$data = array();
						$data['member_id'] = $member_data['member_id'];
						$data['member_name'] = $member_data['member_name'];
						$data['department_name'] = $member_data['department_name'];
						$data['dept_total'] = $member_data['dept_total'];
						$data['num_non_pay'] = $member_data['num_non_pay'];
						$data['contract'] = $row['contract'];
						$data['pay_amount'] = $row['pay_amount'];
						$data['real_pay_amount'] = $row['real_pay_amount'];
						$sort_data[$page][] = $data;
					}
				}
				if(!empty($member_data["9"])) {
					foreach($member_data["9"] as $row) {
						$rowCount++;
						$index++;
						$mem_total['pay_amount'] += $row['pay_amount'];
						$mem_total['real_pay_amount'] += $row['real_pay_amount'];

						if ($index <= $max_first_page) {
							$page = 1;
						} else {
							$page = ceil(($index-$max_first_page) / $max_per_page)+1;
						}
						$data = array();
						$data['member_id'] = $member_data['member_id'];
						$data['member_name'] = $member_data['member_name'];
						$data['department_name'] = $member_data['department_name'];
						$data['dept_total'] = $member_data['dept_total'];
						$data['num_non_pay'] = $member_data['num_non_pay'];
						$data['contract'] = $row['contract'];
						$data['pay_amount'] = $row['pay_amount'];
						$data['real_pay_amount'] = $row['real_pay_amount'];
						$sort_data[$page][] = $data;
					}
				}
				if(!empty($member_data["7"])) {
					foreach($member_data["7"] as $row) {
						$rowCount++;
						$index++;
						$mem_total['pay_amount'] += $row['pay_amount'];
						$mem_total['real_pay_amount'] += $row['real_pay_amount'];

						if ($index <= $max_first_page) {
							$page = 1;
						} else {
							$page = ceil(($index-$max_first_page) / $max_per_page)+1;
						}
						$data = array();
						$data['member_id'] = $member_data['member_id'];
						$data['member_name'] = $member_data['member_name'];
						$data['department_name'] = $member_data['department_name'];
						$data['dept_total'] = $member_data['dept_total'];
						$data['num_non_pay'] = $member_data['num_non_pay'];
						$data['contract'] = $row['contract'];
						$data['pay_amount'] = $row['pay_amount'];
						$data['real_pay_amount'] = $row['real_pay_amount'];
						$sort_data[$page][] = $data;
					}
				}
				if(!empty($member_data["DEPOSIT"])) {
					foreach($member_data["DEPOSIT"] as $row) {
						$rowCount++;
						$index++;
						$mem_total['pay_amount'] += $row['pay_amount'];
						$mem_total['real_pay_amount'] += $row['real_pay_amount'];

						if ($index <= $max_first_page) {
							$page = 1;
						} else {
							$page = ceil(($index-$max_first_page) / $max_per_page)+1;
						}
						$data = array();
						$data['member_id'] = $member_data['member_id'];
						$data['member_name'] = $member_data['member_name'];
						$data['department_name'] = $member_data['department_name'];
						$data['dept_total'] = $member_data['dept_total'];
						$data['num_non_pay'] = $member_data['num_non_pay'];
						$data['contract'] = $row['contract'];
						$data['pay_amount'] = $row['pay_amount'];
						$data['real_pay_amount'] = $row['real_pay_amount'];
						$sort_data[$page][] = $data;
					}
				}
				if(!empty($member_data["GUARANTEE_AMOUNT"])) {
					foreach($member_data["GUARANTEE_AMOUNT"] as $row) {
						$rowCount++;
						$index++;
						$mem_total['pay_amount'] += $row['pay_amount'];
						$mem_total['real_pay_amount'] += $row['real_pay_amount'];

						if ($index <= $max_first_page) {
							$page = 1;
						} else {
							$page = ceil(($index-$max_first_page) / $max_per_page)+1;
						}
						$data = array();
						$data['member_id'] = $member_data['member_id'];
						$data['member_name'] = $member_data['member_name'];
						$data['department_name'] = $member_data['department_name'];
						$data['dept_total'] = $member_data['dept_total'];
						$data['num_non_pay'] = $member_data['num_non_pay'];
						$data['contract'] = $row['contract'];
						$data['pay_amount'] = $row['pay_amount'];
						$data['real_pay_amount'] = $row['real_pay_amount'];
						$sort_data[$page][] = $data;
					}
				}
				if(!empty($member_data["REGISTER_FEE"])) {
					foreach($member_data["REGISTER_FEE"] as $row) {
						$rowCount++;
						$index++;
						$mem_total['pay_amount'] += $row['pay_amount'];
						$mem_total['real_pay_amount'] += $row['real_pay_amount'];

						if ($index <= $max_first_page) {
							$page = 1;
						} else {
							$page = ceil(($index-$max_first_page) / $max_per_page)+1;
						}
						$data = array();
						$data['member_id'] = $member_data['member_id'];
						$data['member_name'] = $member_data['member_name'];
						$data['department_name'] = $member_data['department_name'];
						$data['dept_total'] = $member_data['dept_total'];
						$data['num_non_pay'] = $member_data['num_non_pay'];
						$data['contract'] = $row['contract'];
						$data['pay_amount'] = $row['pay_amount'];
						$data['real_pay_amount'] = $row['real_pay_amount'];
						$sort_data[$page][] = $data;
					}
				}
				if(!empty($member_data["CREMATION"])) {
					foreach($member_data["CREMATION"] as $row) {
						$rowCount++;
						$index++;
						$mem_total['pay_amount'] += $row['pay_amount'];
						$mem_total['real_pay_amount'] += $row['real_pay_amount'];

						if ($index <= $max_first_page) {
							$page = 1;
						} else {
							$page = ceil(($index-$max_first_page) / $max_per_page)+1;
						}
						$data = array();
						$data['member_id'] = $member_data['member_id'];
						$data['member_name'] = $member_data['member_name'];
						$data['department_name'] = $member_data['department_name'];
						$data['dept_total'] = $member_data['dept_total'];
						$data['num_non_pay'] = $member_data['num_non_pay'];
						$data['contract'] = $row['contract'];
						$data['pay_amount'] = $row['pay_amount'];
						$data['real_pay_amount'] = $row['real_pay_amount'];
						$sort_data[$page][] = $data;
					}
				}

				$index++;
				if ($index <= $max_first_page) {
					$page = 1;
				} else {
					$page = ceil(($index-$max_first_page) / $max_per_page)+1;
				}
				$data = array();
				$data['member_id'] = $member_data['member_id'];
				$data['member_name'] = $member_data['member_name'];
				$data['department_name'] = $member_data['department_name'];
				$data['dept_total'] = $member_data['dept_total'];
				$data['num_non_pay'] = $member_data['num_non_pay'];
				$data['contract'] = "รวม";
				$data['pay_amount'] = $mem_total['pay_amount'];
				$data['real_pay_amount'] = $mem_total['real_pay_amount'];
				$sort_data[$page][] = $data;
		}

		$page_all = ceil($row_count / $max_per_page);


		$arr_data['datas'] = $sort_data;
		$arr_data['page_all'] = $page_all;
		$this->preview_libraries->template_preview('report_deposit_data/coop_report_cancel_receipt_preview',$arr_data);
	}

	function coop_report_cancel_receipt_excel(){
		$arr_data = array();	

		$arr_data['month_arr'] = $this->center_function->month_arr();

		$whereType = '';
		if($_GET['type'] == 'paid') {
			$whereType .= " AND t1.non_pay_status = '2'";
		} else if ($_GET['type'] == 'non_pay') {
			$whereType .= " AND t1.non_pay_status = '1'";
		}

		/*$receipts = $this->db->select("t1.member_id, t2.deduct_code, t2.non_pay_amount as pay_amount, t2.non_pay_amount_balance, t2.loan_id, t2.loan_atm_id, t2.cremation_type_id, t7.pay_type
										, t3.contract_number, t4.contract_number as atm_contract_number, t5.loan_type_id")
								->from("coop_non_pay as t1")
								->join("coop_non_pay_detail as t2", "t1.non_pay_id = t2.non_pay_id", 'inner')
								->join("coop_loan as t3", "t2.loan_id = t3.id", "left")
								->join("coop_loan_atm as t4", "t2.loan_atm_id = t4.loan_atm_id", "left")
								->join('coop_loan_name as t5', 't3.loan_type = t5.loan_name_id', 'left')
								->join('coop_finance_month_profile AS t6', 't6.profile_month = t1.non_pay_month AND t6.profile_year = t1.non_pay_year', 'inner')
								->join('coop_finance_month_detail AS t7', 't6.profile_id = t7.profile_id AND t7.member_id = t1.member_id AND t7.deduct_code = t2.deduct_code', 'inner')
								->where("t1.non_pay_month = '".$_GET['month']."' AND t1.non_pay_year = '".$_GET['year']."' AND t1.non_pay_amount_balance <> 0 ".$whereType)
								->order_by("t2.member_id")
								->group_by("t7.deduct_code,t7.member_id,t7.pay_type")
								->get()->result_array();
		*/	
		$receipts = $this->db->select("t1.member_id, t7.deduct_code, t7.pay_amount as pay_amount, t2.non_pay_amount_balance, t2.loan_id, t2.loan_atm_id, t2.cremation_type_id, t7.pay_type
										, t3.contract_number, t4.contract_number as atm_contract_number, t5.loan_type_id")
								->from("coop_finance_month_detail AS t7 ")
								->join("coop_finance_month_profile AS t6", "t6.profile_id = t7.profile_id", 'left')
								->join("coop_non_pay AS t1", "t6.profile_month = t1.non_pay_month AND t6.profile_year = t1.non_pay_year AND t7.member_id = t1.member_id", "left")
								->join("coop_non_pay_detail AS t2", "t1.non_pay_id = t2.non_pay_id AND t7.deduct_code = t2.deduct_code", "left")
								->join("coop_loan AS t3", "t7.loan_id = t3.id", "left")
								->join("coop_loan_atm AS t4", "t7.loan_atm_id = t4.loan_atm_id", "left")
								->join("coop_loan_name AS t5", "t3.loan_type = t5.loan_name_id", "left")
								->where("t1.non_pay_month = '".$_GET['month']."' AND t1.non_pay_year = '".$_GET['year']."' AND t1.non_pay_amount_balance <> 0 ".$whereType)
								->order_by("t7.member_id")
								->group_by("t7.deduct_code,t7.member_id,t7.pay_type")
								->get()->result_array();	
		$member_receipt_ids = array_filter(array_column($receipts, 'member_id'));

		//Get member detail
		$details = $this->db->select("t1.member_id, t1.firstname_th, t1.lastname_th, t2.mem_group_name as level_name, t3.mem_group_name as department_name, t4.prename_full")
									->from("coop_mem_apply as t1")
									->join("coop_mem_group as t2", "t1.level = t2.id", "left")
									->join("coop_mem_group as t3", "t1.department = t3.id", "left")
									->join("coop_prename as t4", "t1.prename_id = t4.prename_id", "left")
									->where("t1.member_id IN (".implode(",", $member_receipt_ids).")")
									->get()->result_array();
		$member_detail_ids = array_column($details, 'member_id');

		//Get Total loan Dept
		$dept_loan_total = $this->db->select("member_id, sum(loan_amount_balance) as sum")
									->from("coop_loan")
									->where("member_id IN  (".implode(",", $member_receipt_ids).") AND loan_status = 1")
									->group_by("member_id")
									->get()->result_array();
		$loan_member_ids = array_column($dept_loan_total, 'member_id');

		$dept_atm_total = $this->db->select("member_id, sum(total_amount) as sum_total, sum(total_amount_balance) as sum_balance")
									->from("coop_loan_atm")
									->where("member_id IN  (".implode(",", $member_receipt_ids).") AND loan_atm_status = 1")
									->group_by("member_id")
									->get()->result_array();
		$atm_member_ids = array_column($dept_atm_total, 'member_id');

		$coop_finance_month_profile = $this->db->select("profile_id")
									->from("coop_finance_month_profile")
									->where("profile_month = '".(int)$_GET['month']."' AND profile_year = '".$_GET['year']."'")
									->get()->result_array();		
		$finance_month_profile = $coop_finance_month_profile[0]['profile_id'];
		//echo $finance_month_profile.'<hr>';							
		//echo $this->db->last_query(); echo '<br>';	
		$finance_month_detail = $this->db->select("member_id,pay_amount,real_pay_amount,pay_type,deduct_code,loan_id,loan_atm_id")
									->from("coop_finance_month_detail")
									->where("profile_id = '".$finance_month_profile."' AND member_id IN  (".implode(",", $member_receipt_ids).")")
									->get()->result_array();
		//echo $this->db->last_query(); echo '<br>';	exit;								
		$arr_finance_month_detail = array();
		foreach($finance_month_detail AS $key_finance=>$val_finance){			
			$arr_finance_month_detail[$val_finance['member_id']][$val_finance['deduct_code']][$val_finance['pay_type']]['pay_type'] = $val_finance['pay_type'];
			$arr_finance_month_detail[$val_finance['member_id']][$val_finance['deduct_code']][$val_finance['pay_type']]['pay_amount'] = $val_finance['pay_amount'];
			$arr_finance_month_detail[$val_finance['member_id']][$val_finance['deduct_code']][$val_finance['pay_type']]['real_pay_amount'] = $val_finance['real_pay_amount'];
		}

		//นับจำนวน ผิดนัดครั้งที่
		$date_last_month = date('Y-m-t',strtotime((@$_GET['year']-543).'-'.sprintf("%02d",@$_GET['month']).'-01'));
		
		$non_pay = $this->db->select("t1.non_pay_id,
												t1.member_id,
												t1.non_pay_amount,
												t1.non_pay_month,
												t1.non_pay_year,
												t1.non_pay_status,
												t1.non_pay_amount_balance")
									->from("coop_non_pay AS t1")
									->where("t1.member_id IN (".implode(",", $member_receipt_ids).") AND CONCAT(t1.non_pay_year,RIGHT(CONCAT('00', t1.non_pay_month), 2)) <= '".$_GET['year'].sprintf("%02d",$_GET['month'])."'")
									->get()->result_array();		
		$arr_non_pay = array();
		$arr_pay = array();
		foreach($non_pay AS $key_non_pay=>$val_non_pay){			
			$check_receipt = $this->db->select("t1.non_pay_id,
											t1.receipt_id,
											t1.receipt_id,
											t2.sumcount,
											t2.receipt_status,
											t2.receipt_datetime,
											t2.member_id")
									->from("coop_non_pay_receipt AS t1")
									->join("coop_receipt AS t2","t1.receipt_id = t2.receipt_id","left")
									->where("t1.non_pay_id = '".$val_non_pay['non_pay_id']."' AND t2.receipt_datetime <= '".$date_last_month."'")
									->get()->result_array();
			$sumcount = 0;	
			$arr_pay[@$val_non_pay['member_id']][$val_non_pay['non_pay_id']]['non_pay_id'] = @$val_non_pay['non_pay_id'];
			$arr_pay[@$val_non_pay['member_id']][$val_non_pay['non_pay_id']]['member_id'] = @$val_non_pay['member_id'];
			$arr_pay[@$val_non_pay['member_id']][$val_non_pay['non_pay_id']]['pay_amount'] = 0;
			foreach($check_receipt AS $key_check=>$val_check){
				$sumcount += @$val_check['sumcount'];				
				$arr_pay[@$val_non_pay['member_id']][$val_non_pay['non_pay_id']]['pay_amount'] = @$sumcount;
				
			}	
			$arr_non_pay[$val_non_pay['member_id']][$val_non_pay['non_pay_id']]['non_pay_id'] = $val_non_pay['non_pay_id'];
			$arr_non_pay[$val_non_pay['member_id']][$val_non_pay['non_pay_id']]['non_pay_amount'] = $val_non_pay['non_pay_amount'];
			$arr_non_pay[$val_non_pay['member_id']][$val_non_pay['non_pay_id']]['member_id'] = $val_non_pay['member_id'];
		}
		
		$arr_check_non_pay = array();
		foreach($arr_non_pay AS $key_non_pay => $val_non_pay){
			$num_non_pay = 0;
			foreach($val_non_pay AS $key_non_pay_id => $val_non_pay_detail){
				if(@$key_non_pay == $arr_pay[@$key_non_pay][$key_non_pay_id]['member_id']){
					if(@$val_non_pay_detail['non_pay_amount'] != $arr_pay[@$key_non_pay][$key_non_pay_id]['pay_amount']){
						$num_non_pay++;
						$arr_check_non_pay[$key_non_pay]['num_non_pay'] = $num_non_pay;
						
					}
				}
			}
		}
		
		$datas = array();
		$prev_member_id = "xx";
		$member_total = array();
		$dept_total = array();
		$receipts_count = count($receipts);
		$row_count = 0;
		$max_per_page = 20;
		foreach($receipts as $index => $receipt) {
			$detail = $details[array_search($receipt["member_id"],$member_detail_ids)];
			$dept_loan = in_array($receipt["member_id"], $loan_member_ids) ? $dept_loan_total[array_search($receipt["member_id"],$loan_member_ids)] : null;
			$dept_atm = in_array($receipt["member_id"], $atm_member_ids) ? $dept_atm_total[array_search($receipt["member_id"],$atm_member_ids)] : null;

			$datas[$receipt["member_id"]]["member_id"] = $receipt["member_id"];
			$datas[$receipt["member_id"]]["member_name"] = $detail["prename_full"].$detail["firstname_th"]." ".$detail['lastname_th'];
			$datas[$receipt["member_id"]]["department_name"] = $detail["department_name"].",".$detail["level_name"];
			$datas[$receipt["member_id"]]["dept_total"] = 0;
			$datas[$receipt["member_id"]]["num_non_pay"] = $arr_check_non_pay[$receipt["member_id"]]['num_non_pay'];

			if(!empty($dept_loan)) {
				$datas[$receipt["member_id"]]["dept_total"] += $dept_loan['sum'];
			}
			if(!empty($dept_atm)) {
				$datas[$receipt["member_id"]]["dept_total"] += ($dept_atm['sum_total'] - $dept_atm['sum_balance']);
			}
			if($receipt['deduct_code'] == "LOAN") {
				$data = array();
				if($receipt["pay_type"] == "interest") {
					$data["contract"] = $receipt["contract_number"]."[ดอก]";
				} else {
					$data["contract"] = $receipt["contract_number"]."[ต้น]";
				}
				$data["pay_amount"] = $arr_finance_month_detail[$receipt["member_id"]][$receipt['deduct_code']][$receipt["pay_type"]]['pay_amount'];					
				$data["real_pay_amount"] = $arr_finance_month_detail[$receipt["member_id"]][$receipt['deduct_code']][$receipt["pay_type"]]['real_pay_amount'];					
				$datas[$receipt["member_id"]]["total"] += ($receipt["pay_amount"] - $data["real_pay_amount"]);
				$datas[$receipt["member_id"]][$receipt["loan_type_id"]][] = $data;
			} else if ($receipt['deduct_code'] == "ATM") {
				$data = array();
				if($receipt["pay_type"] == "interest") {
					$data["contract"] = $receipt["atm_contract_number"]."[ดอก]";
				} else {
					$data["contract"] = $receipt["atm_contract_number"]."[ต้น]";
				}
				//$data["pay_amount"] = $receipt["pay_amount"];
				//$data["real_pay_amount"] = $receipt["pay_amount"] - $receipt["non_pay_amount_balance"];
				//$datas[$receipt["member_id"]]["total"] += ($receipt["pay_amount"] - $data["real_pay_amount"]);
				$data["pay_amount"] = $arr_finance_month_detail[$receipt["member_id"]][$receipt['deduct_code']][$receipt["pay_type"]]['pay_amount'];					
				$data["real_pay_amount"] = $arr_finance_month_detail[$receipt["member_id"]][$receipt['deduct_code']][$receipt["pay_type"]]['real_pay_amount'];					
				$datas[$receipt["member_id"]]["total"] += ($data["pay_amount"] - $data["real_pay_amount"]);
				$datas[$receipt["member_id"]]["7"][] = $data;
			} else if($receipt['deduct_code'] == "SHARE") {
				$data["contract"] = "หุ้น";
				//$data["pay_amount"] = $receipt["pay_amount"];
				//$data["real_pay_amount"] = $receipt["pay_amount"] - $receipt["non_pay_amount_balance"];
				//$datas[$receipt["member_id"]]["total"] += ($receipt["pay_amount"] - $data["real_pay_amount"]);
				$data["pay_amount"] = $arr_finance_month_detail[$receipt["member_id"]][$receipt['deduct_code']][$receipt["pay_type"]]['pay_amount'];					
				$data["real_pay_amount"] = $arr_finance_month_detail[$receipt["member_id"]][$receipt['deduct_code']][$receipt["pay_type"]]['real_pay_amount'];					
				$datas[$receipt["member_id"]]["total"] += ($data["pay_amount"] - $data["real_pay_amount"]);
				$datas[$receipt["member_id"]][$receipt['deduct_code']][] = $data;
			} else if ($receipt['deduct_code'] == "DEPOSIT") {
				$data["contract"] = "เงินฝาก";
				//$data["pay_amount"] = $receipt["pay_amount"];
				//$data["real_pay_amount"] = $receipt["pay_amount"] - $receipt["non_pay_amount_balance"];
				//$datas[$receipt["member_id"]]["total"] += ($receipt["pay_amount"] - $data["real_pay_amount"]);
				$data["pay_amount"] = $arr_finance_month_detail[$receipt["member_id"]][$receipt['deduct_code']][$receipt["pay_type"]]['pay_amount'];					
				$data["real_pay_amount"] = $arr_finance_month_detail[$receipt["member_id"]][$receipt['deduct_code']][$receipt["pay_type"]]['real_pay_amount'];					
				$datas[$receipt["member_id"]]["total"] += ($data["pay_amount"] - $data["real_pay_amount"]);
				$datas[$receipt["member_id"]][$receipt['deduct_code']][] = $data;
			} else if ($receipt['deduct_code'] == "GUARANTEE_AMOUNT") {
				$data["contract"] = "ชำระหนี้ค้ำประกัน";
				//$data["pay_amount"] = $receipt["pay_amount"];
				//$data["real_pay_amount"] = $receipt["pay_amount"] - $receipt["non_pay_amount_balance"];
				//$datas[$receipt["member_id"]]["total"] += ($receipt["pay_amount"] - $data["real_pay_amount"]);
				$data["pay_amount"] = $arr_finance_month_detail[$receipt["member_id"]][$receipt['deduct_code']][$receipt["pay_type"]]['pay_amount'];					
				$data["real_pay_amount"] = $arr_finance_month_detail[$receipt["member_id"]][$receipt['deduct_code']][$receipt["pay_type"]]['real_pay_amount'];					
				$datas[$receipt["member_id"]]["total"] += ($data["pay_amount"] - $data["real_pay_amount"]);
				$datas[$receipt["member_id"]][$receipt['deduct_code']][] = $data;
			} else if ($receipt['deduct_code'] == "REGISTER_FEE") {
				$data["contract"] = "ค่าธรรมเนียมแรกเข้า";
				//$data["pay_amount"] = $receipt["pay_amount"];
				//$data["real_pay_amount"] = $receipt["pay_amount"] - $receipt["non_pay_amount_balance"];
				//$datas[$receipt["member_id"]]["total"] += ($receipt["pay_amount"] - $data["real_pay_amount"]);
				$data["pay_amount"] = $arr_finance_month_detail[$receipt["member_id"]][$receipt['deduct_code']][$receipt["pay_type"]]['pay_amount'];					
				$data["real_pay_amount"] = $arr_finance_month_detail[$receipt["member_id"]][$receipt['deduct_code']][$receipt["pay_type"]]['real_pay_amount'];					
				$datas[$receipt["member_id"]]["total"] += ($data["pay_amount"] - $data["real_pay_amount"]);
				$datas[$receipt["member_id"]][$receipt['deduct_code']][] = $data;
			} else if ($receipt['deduct_code'] == "CREMATION") {
				$data["contract"] = "ฌสอ สป";
				//$data["pay_amount"] = $receipt["pay_amount"];
				//$data["real_pay_amount"] = $receipt["pay_amount"] - $receipt["non_pay_amount_balance"];
				//$datas[$receipt["member_id"]]["total"] += ($receipt["pay_amount"] - $data["real_pay_amount"]);
				$data["pay_amount"] = $arr_finance_month_detail[$receipt["member_id"]][$receipt['deduct_code']][$receipt["pay_type"]]['pay_amount'];					
				$data["real_pay_amount"] = $arr_finance_month_detail[$receipt["member_id"]][$receipt['deduct_code']][$receipt["pay_type"]]['real_pay_amount'];					
				$datas[$receipt["member_id"]]["total"] += ($data["pay_amount"] - $data["real_pay_amount"]);
				$datas[$receipt["member_id"]][$receipt['deduct_code']][] = $data;
			}
		}

		
		$sort_data = array();
		$index = 0;
		$max_first_page = 20;
		$max_per_page = 24;
		foreach($datas as $key => $member_data){
				$rowCount = 0;
				$mem_total = array();
				
				if(!empty($member_data["SHARE"])) {
					foreach($member_data["SHARE"] as $row) {
						$rowCount++;
						$index++;
						$mem_total['pay_amount'] += $row['pay_amount'];
						$mem_total['real_pay_amount'] += $row['real_pay_amount'];

						if ($index <= $max_first_page) {
							$page = 1;
						} else {
							$page = ceil(($index-$max_first_page) / $max_per_page)+1;
						}
						$data = array();
						$data['member_id'] = $member_data['member_id'];
						$data['member_name'] = $member_data['member_name'];
						$data['department_name'] = $member_data['department_name'];
						$data['dept_total'] = $member_data['dept_total'];
						$data['num_non_pay'] = $member_data['num_non_pay'];
						$data['contract'] = $row['contract'];
						$data['pay_amount'] = $row['pay_amount'];
						$data['real_pay_amount'] = $row['real_pay_amount'];
						$sort_data[$page][] = $data;
					}
				}
				if(!empty($member_data["8"])) {
					foreach($member_data["8"] as $row) {
						$rowCount++;
						$index++;
						$mem_total['pay_amount'] += $row['pay_amount'];
						$mem_total['real_pay_amount'] += $row['real_pay_amount'];

						if ($index <= $max_first_page) {
							$page = 1;
						} else {
							$page = ceil(($index-$max_first_page) / $max_per_page)+1;
						}
						$data = array();
						$data['member_id'] = $member_data['member_id'];
						$data['member_name'] = $member_data['member_name'];
						$data['department_name'] = $member_data['department_name'];
						$data['dept_total'] = $member_data['dept_total'];
						$data['num_non_pay'] = $member_data['num_non_pay'];
						$data['contract'] = $row['contract'];
						$data['pay_amount'] = $row['pay_amount'];
						$data['real_pay_amount'] = $row['real_pay_amount'];
						$sort_data[$page][] = $data;
					}
				}
				if(!empty($member_data["9"])) {
					foreach($member_data["9"] as $row) {
						$rowCount++;
						$index++;
						$mem_total['pay_amount'] += $row['pay_amount'];
						$mem_total['real_pay_amount'] += $row['real_pay_amount'];

						if ($index <= $max_first_page) {
							$page = 1;
						} else {
							$page = ceil(($index-$max_first_page) / $max_per_page)+1;
						}
						$data = array();
						$data['member_id'] = $member_data['member_id'];
						$data['member_name'] = $member_data['member_name'];
						$data['department_name'] = $member_data['department_name'];
						$data['dept_total'] = $member_data['dept_total'];
						$data['num_non_pay'] = $member_data['num_non_pay'];
						$data['contract'] = $row['contract'];
						$data['pay_amount'] = $row['pay_amount'];
						$data['real_pay_amount'] = $row['real_pay_amount'];
						$sort_data[$page][] = $data;
					}
				}
				if(!empty($member_data["7"])) {
					foreach($member_data["7"] as $row) {
						$rowCount++;
						$index++;
						$mem_total['pay_amount'] += $row['pay_amount'];
						$mem_total['real_pay_amount'] += $row['real_pay_amount'];

						if ($index <= $max_first_page) {
							$page = 1;
						} else {
							$page = ceil(($index-$max_first_page) / $max_per_page)+1;
						}
						$data = array();
						$data['member_id'] = $member_data['member_id'];
						$data['member_name'] = $member_data['member_name'];
						$data['department_name'] = $member_data['department_name'];
						$data['dept_total'] = $member_data['dept_total'];
						$data['num_non_pay'] = $member_data['num_non_pay'];
						$data['contract'] = $row['contract'];
						$data['pay_amount'] = $row['pay_amount'];
						$data['real_pay_amount'] = $row['real_pay_amount'];
						$sort_data[$page][] = $data;
					}
				}
				if(!empty($member_data["DEPOSIT"])) {
					foreach($member_data["DEPOSIT"] as $row) {
						$rowCount++;
						$index++;
						$mem_total['pay_amount'] += $row['pay_amount'];
						$mem_total['real_pay_amount'] += $row['real_pay_amount'];

						if ($index <= $max_first_page) {
							$page = 1;
						} else {
							$page = ceil(($index-$max_first_page) / $max_per_page)+1;
						}
						$data = array();
						$data['member_id'] = $member_data['member_id'];
						$data['member_name'] = $member_data['member_name'];
						$data['department_name'] = $member_data['department_name'];
						$data['dept_total'] = $member_data['dept_total'];
						$data['num_non_pay'] = $member_data['num_non_pay'];
						$data['contract'] = $row['contract'];
						$data['pay_amount'] = $row['pay_amount'];
						$data['real_pay_amount'] = $row['real_pay_amount'];
						$sort_data[$page][] = $data;
					}
				}
				if(!empty($member_data["GUARANTEE_AMOUNT"])) {
					foreach($member_data["GUARANTEE_AMOUNT"] as $row) {
						$rowCount++;
						$index++;
						$mem_total['pay_amount'] += $row['pay_amount'];
						$mem_total['real_pay_amount'] += $row['real_pay_amount'];

						if ($index <= $max_first_page) {
							$page = 1;
						} else {
							$page = ceil(($index-$max_first_page) / $max_per_page)+1;
						}
						$data = array();
						$data['member_id'] = $member_data['member_id'];
						$data['member_name'] = $member_data['member_name'];
						$data['department_name'] = $member_data['department_name'];
						$data['dept_total'] = $member_data['dept_total'];
						$data['num_non_pay'] = $member_data['num_non_pay'];
						$data['contract'] = $row['contract'];
						$data['pay_amount'] = $row['pay_amount'];
						$data['real_pay_amount'] = $row['real_pay_amount'];
						$sort_data[$page][] = $data;
					}
				}
				if(!empty($member_data["REGISTER_FEE"])) {
					foreach($member_data["REGISTER_FEE"] as $row) {
						$rowCount++;
						$index++;
						$mem_total['pay_amount'] += $row['pay_amount'];
						$mem_total['real_pay_amount'] += $row['real_pay_amount'];

						if ($index <= $max_first_page) {
							$page = 1;
						} else {
							$page = ceil(($index-$max_first_page) / $max_per_page)+1;
						}
						$data = array();
						$data['member_id'] = $member_data['member_id'];
						$data['member_name'] = $member_data['member_name'];
						$data['department_name'] = $member_data['department_name'];
						$data['dept_total'] = $member_data['dept_total'];
						$data['num_non_pay'] = $member_data['num_non_pay'];
						$data['contract'] = $row['contract'];
						$data['pay_amount'] = $row['pay_amount'];
						$data['real_pay_amount'] = $row['real_pay_amount'];
						$sort_data[$page][] = $data;
					}
				}
				if(!empty($member_data["CREMATION"])) {
					foreach($member_data["CREMATION"] as $row) {
						$rowCount++;
						$index++;
						$mem_total['pay_amount'] += $row['pay_amount'];
						$mem_total['real_pay_amount'] += $row['real_pay_amount'];

						if ($index <= $max_first_page) {
							$page = 1;
						} else {
							$page = ceil(($index-$max_first_page) / $max_per_page)+1;
						}
						$data = array();
						$data['member_id'] = $member_data['member_id'];
						$data['member_name'] = $member_data['member_name'];
						$data['department_name'] = $member_data['department_name'];
						$data['dept_total'] = $member_data['dept_total'];
						$data['num_non_pay'] = $member_data['num_non_pay'];
						$data['contract'] = $row['contract'];
						$data['pay_amount'] = $row['pay_amount'];
						$data['real_pay_amount'] = $row['real_pay_amount'];
						$sort_data[$page][] = $data;
					}
				}

				$index++;
				if ($index <= $max_first_page) {
					$page = 1;
				} else {
					$page = ceil(($index-$max_first_page) / $max_per_page)+1;
				}
				$data = array();
				$data['member_id'] = $member_data['member_id'];
				$data['member_name'] = $member_data['member_name'];
				$data['department_name'] = $member_data['department_name'];
				$data['dept_total'] = $member_data['dept_total'];
				$data['num_non_pay'] = $member_data['num_non_pay'];
				$data['contract'] = "รวม";
				$data['pay_amount'] = $mem_total['pay_amount'];
				$data['real_pay_amount'] = $mem_total['real_pay_amount'];
				$sort_data[$page][] = $data;
		}

		$arr_data['dept_total'] = $dept_total;
		$arr_data['datas'] = $sort_data;
		// echo "<pre>";
		// print_r($datas);
		// echo "</pre>";
		// exit;
		
		$this->load->view('report_deposit_data/coop_report_cancel_receipt_excel',$arr_data);	
		
	}	

	public function coop_report_pay_non_pay(){
		$arr_data = array();

		$this->libraries->template('report_deposit_data/coop_report_pay_non_pay',$arr_data);
	}

	public function check_report_pay_non_pay() {
		$row = $this->db->select(array('id','loan_type','loan_type_code'))
						->from('coop_loan_type')
						->order_by("order_by")
						->get()->result_array();
		$arr_data['loan_type'] = $row;

		$arr_data['month_arr'] = $this->center_function->month_arr();
		$arr_data['month_short_arr'] = $this->center_function->month_short_arr();

		if(@$_POST['start_date']){
			$start_date_arr = explode('/',@$_POST['start_date']);
			
			$start_day = $start_date_arr[0];
			$start_month = $start_date_arr[1];
			$start_year = $start_date_arr[2];
			$start_year -= 543;
			$start_date = $start_year.'-'.$start_month.'-'.$start_day;
		}

		if(@$_POST['end_date']){
			$end_date_arr = explode('/',@$_POST['end_date']);
			$end_day = $end_date_arr[0];
			$end_month = $end_date_arr[1];
			$end_year = $end_date_arr[2];
			$end_year -= 543;
			$end_date = $end_year.'-'.$end_month.'-'.$end_day;
		}

		$tran_infos = array();

		//Get Transaction
		$where = "1=1";
		if(@$_POST['start_date'] != '' AND @$_POST['end_date'] == ''){
			$where .= " AND payment_date BETWEEN '".$start_date." 00:00:00.000' AND '".$start_date." 23:59:59.000'";
		}else if(@$_POST['start_date'] != '' AND @$_POST['end_date'] != ''){
			$where .= " AND payment_date BETWEEN '".$start_date." 00:00:00.000' AND '".$end_date." 23:59:59.000'";
		}
		$where .= " AND (loan_id IS NOT NULL OR coop_finance_transaction.loan_atm_id IS NOT NULL OR account_list_id = 14 OR account_list_id = 16)";

		$datas = $this->db->query("SELECT coop_finance_transaction.*, coop_mem_apply.firstname_th, coop_mem_apply.lastname_th, coop_prename.prename_short, coop_loan_atm.contract_number as atm_contract_number,
										coop_loan.contract_number, coop_loan_type.loan_type_code, coop_receipt.month_receipt ,coop_receipt.year_receipt
									FROM coop_finance_transaction
									INNER JOIN coop_mem_apply ON coop_finance_transaction.member_id = coop_mem_apply.member_id
									LEFT JOIN coop_prename ON coop_mem_apply.prename_id = coop_prename.prename_id
									LEFT JOIN coop_loan_atm ON coop_finance_transaction.loan_atm_id = coop_loan_atm.loan_atm_id
									LEFT JOIN coop_loan ON coop_finance_transaction.loan_id = coop_loan.id
									LEFT JOIN coop_loan_name ON coop_loan.loan_type = coop_loan_name.loan_name_id
									LEFT JOIN coop_loan_type ON coop_loan_name.loan_type_id = coop_loan_type.id
									LEFT JOIN coop_receipt ON coop_receipt.receipt_id = coop_finance_transaction.receipt_id
									WHERE {$where}
									ORDER BY coop_finance_transaction.member_id ASC")->result_array();

		$row_data = array();
		$contract_numbers = array();
		$last_receipt_id = null;

		$hasData = false;
		foreach($datas as $tran) {
			$receiptCheckStr = "C";
			$check_c = strpos($tran["receipt_id"],$receiptCheckStr);
			if(!empty($check_c)){
				$hasData = true;
				break;
			}
		}
		if($hasData){
			echo "success";
		}else{
			echo "";
		}	
	}
		
	function coop_report_pay_non_pay_preview(){
		set_time_limit (180);
		$arr_data = array();	
		
		$row = $this->db->select(array('id','loan_type','loan_type_code'))
						->from('coop_loan_type')
						->order_by("order_by")
						->get()->result_array();
		$arr_data['loan_type'] = $row;
		
		$arr_data['month_arr'] = $this->center_function->month_arr();
		$arr_data['month_short_arr'] = $this->center_function->month_short_arr();

		if(@$_GET['start_date']){
			$start_date_arr = explode('/',@$_GET['start_date']);
			$start_day = $start_date_arr[0];
			$start_month = $start_date_arr[1];
			$start_year = $start_date_arr[2];
			$start_year -= 543;
			$start_date = $start_year.'-'.$start_month.'-'.$start_day;
			
		}
		
		if(@$_GET['end_date']){
			$end_date_arr = explode('/',@$_GET['end_date']);
			$end_day = $end_date_arr[0];
			$end_month = $end_date_arr[1];
			$end_year = $end_date_arr[2];
			$end_year -= 543;
			$end_date = $end_year.'-'.$end_month.'-'.$end_day;
		}

		$tran_infos = array();

		//Get Transaction
		$where = "1=1";
		if(@$_GET['start_date'] != '' AND @$_GET['end_date'] == ''){
			$where .= " AND payment_date BETWEEN '".$start_date." 00:00:00.000' AND '".$start_date." 23:59:59.000'";
		}else if(@$_GET['start_date'] != '' AND @$_GET['end_date'] != ''){
			$where .= " AND payment_date BETWEEN '".$start_date." 00:00:00.000' AND '".$end_date." 23:59:59.000'";
		}
		$where .= " AND (loan_id IS NOT NULL OR coop_finance_transaction.loan_atm_id IS NOT NULL OR account_list_id IN('14','16','20','28','36','37','46') )";

		$datas = $this->db->query("SELECT coop_finance_transaction.*, coop_mem_apply.firstname_th, coop_mem_apply.lastname_th, coop_prename.prename_short, coop_loan_atm.contract_number as atm_contract_number,
										coop_loan.contract_number, coop_loan_type.loan_type_code, coop_receipt.month_receipt ,coop_receipt.year_receipt
									FROM coop_finance_transaction
									INNER JOIN coop_mem_apply ON coop_finance_transaction.member_id = coop_mem_apply.member_id
									LEFT JOIN coop_prename ON coop_mem_apply.prename_id = coop_prename.prename_id
									LEFT JOIN coop_loan_atm ON coop_finance_transaction.loan_atm_id = coop_loan_atm.loan_atm_id
									LEFT JOIN coop_loan ON coop_finance_transaction.loan_id = coop_loan.id
									LEFT JOIN coop_loan_name ON coop_loan.loan_type = coop_loan_name.loan_name_id
									LEFT JOIN coop_loan_type ON coop_loan_name.loan_type_id = coop_loan_type.id
									LEFT JOIN coop_receipt ON coop_receipt.receipt_id = coop_finance_transaction.receipt_id
									WHERE {$where}
									ORDER BY coop_finance_transaction.member_id ASC")->result_array();

		$row_data = array();
		$contract_numbers = array();
		$last_receipt_id = null;
		foreach($datas as $tran) {
			$receiptCheckStr = "C";
			$check_c = strpos($tran["receipt_id"],$receiptCheckStr);
			if(!empty($check_c)){
				$row_data[$tran['member_id']][$tran["receipt_id"]]['member_name'] = $tran["prename_short"].$tran["firstname_th"].'  '.$tran["lastname_th"];
				$row_data[$tran['member_id']][$tran["receipt_id"]]['receipt_id'] = $tran["receipt_id"];

				if ($tran['account_list_id'] == 14 || $tran['account_list_id'] == 16 || $tran['account_list_id'] == 37) {
					$row_data[$tran['member_id']][$tran["receipt_id"]]['share'] = $tran["principal_payment"];
					$row_data[$tran['member_id']][$tran["receipt_id"]]["total"] += $tran["principal_payment"];
				} else if (!empty($tran['loan_atm_id'])) {
					if(!empty($tran["principal_payment"])) {
						$row_data[$tran['member_id']][$tran["receipt_id"]]['emergent'][$tran["atm_contract_number"]]['principal'] = $tran["principal_payment"];
						$row_data[$tran['member_id']][$tran["receipt_id"]]["total"] += $tran["principal_payment"];
					}
					if(!empty($tran["interest"])) {
						$row_data[$tran['member_id']][$tran["receipt_id"]]['emergent'][$tran["atm_contract_number"]]['interest'] = $tran["interest"];
						$row_data[$tran['member_id']][$tran["receipt_id"]]["total"] += $tran["interest"];
					}

					$non_pay_month = substr($tran["receipt_id"],0,2);
					$non_pay_year = '25'.substr($tran["receipt_id"],3,2);	
					$row_data[$tran['member_id']][$tran["receipt_id"]][$tran['loan_type_code']][$tran["contract_number"]]['none_pay'] = $this->get_none_pay_atm($tran['loan_atm_id'], $non_pay_month, $non_pay_year);
					$row_data[$tran['member_id']][$tran["receipt_id"]][$tran['loan_type_code']][$tran["contract_number"]]['period_count'] = !empty( $tran["period_count"]) ? $tran["period_count"] : "";
					$row_data[$tran['member_id']][$tran["receipt_id"]]['none_pay_all'] +=$this->get_none_pay_atm($tran['loan_atm_id'], $non_pay_month, $non_pay_year);
				} else if (!empty($tran['loan_id'])) {
					if(!empty($tran["principal_payment"])) {
						$row_data[$tran['member_id']][$tran["receipt_id"]][$tran['loan_type_code']][$tran["contract_number"]]['principal'] = $tran["principal_payment"];
						$row_data[$tran['member_id']][$tran["receipt_id"]]["total"] += $tran["principal_payment"];
					}
					if(!empty($tran["interest"])) {
						$row_data[$tran['member_id']][$tran["receipt_id"]][$tran['loan_type_code']][$tran["contract_number"]]['interest'] = $tran["interest"];
						$row_data[$tran['member_id']][$tran["receipt_id"]]["total"] += $tran["interest"];
					}

					$non_pay_month = substr($tran["receipt_id"],0,2);
					$non_pay_year = '25'.substr($tran["receipt_id"],3,2);	
					$row_data[$tran['member_id']][$tran["receipt_id"]][$tran['loan_type_code']][$tran["contract_number"]]['none_pay'] = $this->get_none_pay($tran['loan_id'], $non_pay_month, $non_pay_year);
					$row_data[$tran['member_id']][$tran["receipt_id"]][$tran['loan_type_code']][$tran["contract_number"]]['period_count'] = !empty($tran["period_count"]) ? $tran["period_count"] : "";
					$row_data[$tran['member_id']][$tran["receipt_id"]]['none_pay_all'] += $this->get_none_pay($tran['loan_id'], $non_pay_month, $non_pay_year);
				} else if ($tran['account_list_id'] == 28) {
					$row_data[$tran['member_id']][$tran["receipt_id"]]['cremation'] = $tran["principal_payment"];
					$row_data[$tran['member_id']][$tran["receipt_id"]]["total"] += $tran["principal_payment"];
				} else if ($tran["account_list_id"] == 20) {
					$row_data[$tran['member_id']][$tran["receipt_id"]]['register_fee'] = $tran["principal_payment"];
					$row_data[$tran['member_id']][$tran["receipt_id"]]["total"] += $tran["principal_payment"];
				} else if ($tran["account_list_id"] == 46) {
					$row_data[$tran['member_id']][$tran["receipt_id"]]['other'] = $tran["principal_payment"];
					$row_data[$tran['member_id']][$tran["receipt_id"]]["total"] += $tran["principal_payment"];
				} else if ($tran["account_list_id"] == 36) {
					$row_data[$tran['member_id']][$tran["receipt_id"]]['guarantee_amount'] = $tran["principal_payment"];
					$row_data[$tran['member_id']][$tran["receipt_id"]]["total"] += $tran["principal_payment"];
				}
				$last_receipt_id = $tran['receipt_id'];
			}
		}		
		//echo '<pre>'; print_r($row_data); echo '</pre>';
		$arr_data['num_rows'] = $row['num_rows'];
		$arr_data['paging'] = $paging;
		$arr_data['datas'] = $row_data;
		$arr_data['page_all'] = $row['page_all'];
		$arr_data['tran_infos'] = $tran_infos;
		$arr_data['last_receipt_id'] = $last_receipt_id;
		$this->preview_libraries->template_preview('report_deposit_data/coop_report_pay_non_pay_preview',$arr_data);
	}

	function coop_report_pay_non_pay_excel() {
		$arr_data = array();	
		
		$row = $this->db->select(array('id','loan_type','loan_type_code'))
						->from('coop_loan_type')
						->order_by("order_by")
						->get()->result_array();
		$arr_data['loan_type'] = $row;
		
		$arr_data['month_arr'] = $this->center_function->month_arr();
		$arr_data['month_short_arr'] = $this->center_function->month_short_arr();

		if(@$_GET['start_date']){
			$start_date_arr = explode('/',@$_GET['start_date']);
			$start_day = $start_date_arr[0];
			$start_month = $start_date_arr[1];
			$start_year = $start_date_arr[2];
			$start_year -= 543;
			$start_date = $start_year.'-'.$start_month.'-'.$start_day;
			
		}
		
		if(@$_GET['end_date']){
			$end_date_arr = explode('/',@$_GET['end_date']);
			$end_day = $end_date_arr[0];
			$end_month = $end_date_arr[1];
			$end_year = $end_date_arr[2];
			$end_year -= 543;
			$end_date = $end_year.'-'.$end_month.'-'.$end_day;
		}

		$tran_infos = array();

		//Get Transaction
		$where = "1=1";
		if(@$_GET['start_date'] != '' AND @$_GET['end_date'] == ''){
			$where .= " AND payment_date BETWEEN '".$start_date." 00:00:00.000' AND '".$start_date." 23:59:59.000'";
		}else if(@$_GET['start_date'] != '' AND @$_GET['end_date'] != ''){
			$where .= " AND payment_date BETWEEN '".$start_date." 00:00:00.000' AND '".$end_date." 23:59:59.000'";
		}
		$where .= " AND (loan_id IS NOT NULL OR coop_finance_transaction.loan_atm_id IS NOT NULL OR account_list_id IN('14','16','20','28','36','37','46'))";

		$datas = $this->db->query("SELECT coop_finance_transaction.*, coop_mem_apply.firstname_th, coop_mem_apply.lastname_th, coop_prename.prename_short, coop_loan_atm.contract_number as atm_contract_number,
										coop_loan.contract_number, coop_loan_type.loan_type_code
									FROM coop_finance_transaction
									INNER JOIN coop_mem_apply ON coop_finance_transaction.member_id = coop_mem_apply.member_id
									LEFT JOIN coop_prename ON coop_mem_apply.prename_id = coop_prename.prename_id
									LEFT JOIN coop_loan_atm ON coop_finance_transaction.loan_atm_id = coop_loan_atm.loan_atm_id
									LEFT JOIN coop_loan ON coop_finance_transaction.loan_id = coop_loan.id
									LEFT JOIN coop_loan_name ON coop_loan.loan_type = coop_loan_name.loan_name_id
									LEFT JOIN coop_loan_type ON coop_loan_name.loan_type_id = coop_loan_type.id
									WHERE {$where}
									ORDER BY coop_finance_transaction.member_id ASC")->result_array();

		$row_data = array();
		$contract_numbers = array();
		$last_receipt_id = null;
		foreach($datas as $tran) {
			$receiptCheckStr = $tran['month_receipt']."C".substr($tran['year_receipt'],2);
			$check_c = strpos($tran["receipt_id"],$receiptCheckStr);
			if(!empty($check_c)){
				$row_data[$tran['member_id']][$tran["receipt_id"]]['member_name'] = $tran["prename_short"].$tran["firstname_th"].'  '.$tran["lastname_th"];
				$row_data[$tran['member_id']][$tran["receipt_id"]]['receipt_id'] = $tran["receipt_id"];

				if ($tran['account_list_id'] == 14 || $tran['account_list_id'] == 16 || $tran['account_list_id'] == 37) {
					$row_data[$tran['member_id']][$tran["receipt_id"]]['share'] = $tran["principal_payment"];
					$row_data[$tran['member_id']][$tran["receipt_id"]]["total"] += $tran["principal_payment"];
				} else if (!empty($tran['loan_atm_id'])) {
					if(!empty($tran["principal_payment"])) {
						$row_data[$tran['member_id']][$tran["receipt_id"]]['emergent'][$tran["atm_contract_number"]]['principal'] = $tran["principal_payment"];
						$row_data[$tran['member_id']][$tran["receipt_id"]]["total"] += $tran["principal_payment"];
					}
					if(!empty($tran["interest"])) {
						$row_data[$tran['member_id']][$tran["receipt_id"]]['emergent'][$tran["atm_contract_number"]]['interest'] = $tran["interest"];
						$row_data[$tran['member_id']][$tran["receipt_id"]]["total"] += $tran["interest"];
					}
					
					$non_pay_month = substr($tran["receipt_id"],0,2);
					$non_pay_year = '25'.substr($tran["receipt_id"],3,2);	
					$row_data[$tran['member_id']][$tran["receipt_id"]][$tran['loan_type_code']][$tran["contract_number"]]['none_pay'] = $this->get_none_pay_atm($tran['loan_atm_id'], $non_pay_month, $non_pay_year);
					$row_data[$tran['member_id']][$tran["receipt_id"]]['none_pay_all'] +=$this->get_none_pay_atm($tran['loan_atm_id'], $non_pay_month, $non_pay_year);
				} else if (!empty($tran['loan_id'])) {
					if(!empty($tran["principal_payment"])) {
						$row_data[$tran['member_id']][$tran["receipt_id"]][$tran['loan_type_code']][$tran["contract_number"]]['principal'] = $tran["principal_payment"];
						$row_data[$tran['member_id']][$tran["receipt_id"]]["total"] += $tran["principal_payment"];
					}
					if(!empty($tran["interest"])) {
						$row_data[$tran['member_id']][$tran["receipt_id"]][$tran['loan_type_code']][$tran["contract_number"]]['interest'] = $tran["interest"];
						$row_data[$tran['member_id']][$tran["receipt_id"]]["total"] += $tran["interest"];
					}
					
					$non_pay_month = substr($tran["receipt_id"],0,2);
					$non_pay_year = '25'.substr($tran["receipt_id"],3,2);	
					$row_data[$tran['member_id']][$tran["receipt_id"]][$tran['loan_type_code']][$tran["contract_number"]]['none_pay'] = $this->get_none_pay($tran['loan_id'], $non_pay_month, $non_pay_year);
					$row_data[$tran['member_id']][$tran["receipt_id"]]['none_pay_all'] += $this->get_none_pay($tran['loan_id'], $non_pay_month, $non_pay_year);
				} else if ($tran['account_list_id'] == 28) {
					$row_data[$tran['member_id']][$tran["receipt_id"]]['cremation'] = $tran["principal_payment"];
					$row_data[$tran['member_id']][$tran["receipt_id"]]["total"] += $tran["principal_payment"];
				} else if ($tran["account_list_id"] == 20) {
					$row_data[$tran['member_id']][$tran["receipt_id"]]['register_fee'] = $tran["principal_payment"];
					$row_data[$tran['member_id']][$tran["receipt_id"]]["total"] += $tran["principal_payment"];
				} else if ($tran["account_list_id"] == 46) {
					$row_data[$tran['member_id']][$tran["receipt_id"]]['other'] = $tran["principal_payment"];
					$row_data[$tran['member_id']][$tran["receipt_id"]]["total"] += $tran["principal_payment"];
				} else if ($tran["account_list_id"] == 36) {
					$row_data[$tran['member_id']][$tran["receipt_id"]]['guarantee_amount'] = $tran["principal_payment"];
					$row_data[$tran['member_id']][$tran["receipt_id"]]["total"] += $tran["principal_payment"];
				}
				$last_receipt_id = $tran['receipt_id'];
			}
		}

		$arr_data['num_rows'] = $row['num_rows'];
		$arr_data['paging'] = $paging;
		$arr_data['datas'] = $row_data;
		$arr_data['page_all'] = $row['page_all'];
		$arr_data['tran_infos'] = $tran_infos;
		$arr_data['last_receipt_id'] = $last_receipt_id;

		$this->load->view('report_deposit_data/coop_report_pay_non_pay_excel',$arr_data);	
	}
	
	
	
	function get_none_pay_atm($loan_atm_id, $month, $year) {
		//$this->db->select('coop_non_pay.non_pay_id, coop_non_pay.non_pay_amount_balance');
		$this->db->select('coop_non_pay.non_pay_id, coop_non_pay_detail.non_pay_amount_balance');
		$this->db->from("coop_non_pay");
		$this->db->join("coop_non_pay_detail", "coop_non_pay.non_pay_id = coop_non_pay_detail.non_pay_id", "left");
		$this->db->where("coop_non_pay.non_pay_month = '".$month."' AND coop_non_pay.non_pay_year = '".$year."' AND coop_non_pay_detail.pay_type = 'interest' AND coop_non_pay_detail.loan_atm_id = '{$loan_atm_id}'");
		$loan = $this->db->get()->row();
		if (!empty($loan)) {
			return $loan->non_pay_amount_balance;
		}
		return 0;
	}

	public function coop_report_deposit_month_transaction() {
		$arr_data = array();
		$this->libraries->template('report_deposit_data/coop_report_deposit_month_transaction',$arr_data);
	}

	public function coop_report_deposit_month_transaction_preview() {
		$arr_data = array();

		$arr_data['month_arr'] = $this->month_arr;
		$arr_data['month_short_arr'] = $this->month_short_arr;

		$members = $this->get_data_report_deposit_month_transaction($_GET);

		$datas = array();
		$page = 0;
		$first_page_size = 16;
		$page_size = 24;
		foreach($members as $index => $member) {
			if($index < $first_page_size) {
				$page = 1;
			} else {
				$page = ceil((($index + 1)-$first_page_size) / $page_size) + 1;
			}
			$datas[$page][] = $member;
		}
		$arr_data["datas"] = $datas;
		$arr_data["page_all"] = $page;

		$this->preview_libraries->template_preview('report_deposit_data/coop_report_deposit_month_transaction_preview',$arr_data);
	}

	public function coop_report_deposit_month_transaction_excel() {
		$arr_data = array();

		$arr_data['month_arr'] = $this->month_arr;
		$arr_data['month_short_arr'] = $this->month_short_arr;
		$arr_data["datas"] = $this->get_data_report_deposit_month_transaction($_GET);

		$this->load->view('report_deposit_data/coop_report_deposit_month_transaction_excel',$arr_data);
	}

	public function get_data_report_deposit_month_transaction($data) {
		if($data['start_date']){
			$start_date_arr = explode('/',$data['start_date']);
			$start_day = $start_date_arr[0];
			$start_month = $start_date_arr[1];
			$start_year = $start_date_arr[2];
			$start_year -= 543;
			$start_date = $start_year.'-'.$start_month.'-'.$start_day;
		}
		if($data['end_date']){
			$end_date_arr = explode('/',$data['end_date']);
			$end_day = $end_date_arr[0];
			$end_month = $end_date_arr[1];
			$end_year = $end_date_arr[2];
			$end_year -= 543;
			$end_date = $end_year.'-'.$end_month.'-'.$end_day;
		}
		$where = "";
		if(!empty($data['start_date']) && empty($data['end_date'])) {
			$where = " AND t1.createdatetime BETWEEN '".$start_date." 00:00:00.000' AND '".$start_date." 23:59:59.000'";
		}else if(!empty($data['start_date']) && !empty($data['end_date'])) {
			$where = " AND t1.createdatetime BETWEEN '".$start_date." 00:00:00.000' AND '".$end_date." 23:59:59.000'";
		}

		$members = $this->db->select("t1.member_id,
										t1.account_id,
										t1.total_amount,
										t1.createdatetime,
										t2.firstname_th,
										t2.lastname_th,
										t2.salary,
										t3.prename_full,
										t4.mem_group_name as level_name,
										t5.mem_group_name as faction_name,
										t6.mem_group_name as department_name,
										t7.user_name
										")
							->from("coop_deposit_month_transaction as t1")
							//->join("coop_mem_apply as t2", "t1.member_id = t2.member_id", "inner")
							->join("((SELECT IF (
										(
											SELECT
												level_old
											FROM
												coop_mem_group_move
											WHERE
												date_move >= '".$start_date."'
											AND coop_mem_group_move.member_id = coop_mem_apply.member_id
											ORDER BY
												date_move ASC
											LIMIT 1
										),
										(
											SELECT
												level_old
											FROM
												coop_mem_group_move
											WHERE
												date_move >= '".$start_date."'
											AND coop_mem_group_move.member_id = coop_mem_apply.member_id
											ORDER BY
												date_move ASC
											LIMIT 1
										),
										coop_mem_apply. level
									) AS level,
								IF (
										(
											SELECT
												faction_old
											FROM
												coop_mem_group_move
											WHERE
												date_move >= '".$start_date."'
											AND coop_mem_group_move.member_id = coop_mem_apply.member_id
											ORDER BY
												date_move ASC
											LIMIT 1
										),
										(
											SELECT
												faction_old
											FROM
												coop_mem_group_move
											WHERE
												date_move >= '".$start_date."'
											AND coop_mem_group_move.member_id = coop_mem_apply.member_id
											ORDER BY
												date_move ASC
											LIMIT 1
										),
										coop_mem_apply.faction
									) AS faction,
									IF (
										(
											SELECT
												department_old
											FROM
												coop_mem_group_move
											WHERE
												date_move >= '".$start_date."'
											AND coop_mem_group_move.member_id = coop_mem_apply.member_id
											ORDER BY
												date_move ASC
											LIMIT 1
										),
										(
											SELECT
												department_old
											FROM
												coop_mem_group_move
											WHERE
												date_move >= '".$start_date."'
											AND coop_mem_group_move.member_id = coop_mem_apply.member_id
											ORDER BY
												date_move ASC
											LIMIT 1
										),
										coop_mem_apply.department
									) AS department, member_id, firstname_th, lastname_th, mem_type_id, prename_id,salary FROM coop_mem_apply
								)) as t2", "t1.member_id = t2.member_id", "inner")
							->join("coop_prename as t3", "t2.prename_id = t3.prename_id", "left")
							->join("coop_mem_group as t4", "t2.level = t4.id", "left")
							->join("coop_mem_group as t5", "t2.faction = t5.id", "left")
							->join("coop_mem_group as t6", "t2.department = t6.id", "left")
							->join("coop_user as t7", "t1.admin_id = t7.user_id", "left")
							->where("1=1 ".$where)
							->order_by("t1.createdatetime, t1.member_id")
							->get()->result_array();
		if(@$_GET['dev']=='dev'){
			echo $this->db->last_query(); exit;
		}
		$results = array();
		foreach($members as $member) {
			$result = array();
			$result = $member;

			$prev_deposit_month = $this->db->select("*")
											->from("coop_deposit_month_transaction")
											->where("member_id = '".$member["member_id"]."' AND createdatetime < '".$member["createdatetime"]."'")
											->order_by("createdatetime DESC")
											->get()->result_array();

			$result["prev_total_amount"] = !empty($prev_deposit_month) ? $prev_deposit_month[0]["total_amount"] : 0;
			$result["name"] = $member["prename_full"].$member["firstname_th"]." ".$member["lastname_th"];
			$result["group_name"] = $member["level_name"];
			$results[] = $result;
		}

		return $results;
	}

	public function check_report_deposit_month_transaction() {
		if($_POST['start_date']){
			$start_date_arr = explode('/',$_POST['start_date']);
			$start_day = $start_date_arr[0];
			$start_month = $start_date_arr[1];
			$start_year = $start_date_arr[2];
			$start_year -= 543;
			$start_date = $start_year.'-'.$start_month.'-'.$start_day;
		}
		if($_POST['end_date']){
			$end_date_arr = explode('/',$_POST['end_date']);
			$end_day = $end_date_arr[0];
			$end_month = $end_date_arr[1];
			$end_year = $end_date_arr[2];
			$end_year -= 543;
			$end_date = $end_year.'-'.$end_month.'-'.$end_day;
		}
		$where = "";
		if(!empty($_POST['start_date']) && empty($_POST['end_date'])) {
			$where = " AND t1.createdatetime BETWEEN '".$start_date." 00:00:00.000' AND '".$start_date." 23:59:59.000'";
		}else if(!empty($_POST['start_date']) && !empty($_POST['end_date'])) {
			$where = " AND t1.createdatetime BETWEEN '".$start_date." 00:00:00.000' AND '".$end_date." 23:59:59.000'";
		}

		$members = $this->db->select("t1.member_id
									")
							->from("coop_deposit_month_transaction as t1")
							->where("1=1 ".$where)
							->get()->result_array();

		if(!empty($members)){
			echo "success";
		}else{
			echo "";
		}	
	}

	public function coop_report_deposit_month_account_transaction() {
		$arr_data = array();
		$this->libraries->template('report_deposit_data/coop_report_deposit_month_account_transaction',$arr_data);
	}

	public function coop_report_deposit_month_account_transaction_preview() {
		$arr_data = array();

		$arr_data['month_arr'] = $this->month_arr;
		$arr_data['month_short_arr'] = $this->month_short_arr;

		$members = $this->get_data_report_deposit_month_account_transaction($_GET);

		$datas = array();
		$page = 0;
		$first_page_size = 20;
		$page_size = 28;
		foreach($members as $index => $member) {
			if($index < $first_page_size) {
				$page = 1;
			} else {
				$page = ceil((($index + 1)-$first_page_size) / $page_size) + 1;
			}
			$datas[$page][] = $member;
		}
		$arr_data["datas"] = $datas;
		$arr_data["page_all"] = $page;

		$this->preview_libraries->template_preview('report_deposit_data/coop_report_deposit_month_account_transaction_preview',$arr_data);
	}

	public function coop_report_deposit_month_account_transaction_excel() {
		$arr_data = array();

		$arr_data['month_arr'] = $this->month_arr;
		$arr_data['month_short_arr'] = $this->month_short_arr;
		$arr_data["datas"] = $this->get_data_report_deposit_month_account_transaction($_GET);

		$this->load->view('report_deposit_data/coop_report_deposit_month_account_transaction_excel',$arr_data);
	}

	public function get_data_report_deposit_month_account_transaction($data) {
		if($data['start_date']){
			$start_date_arr = explode('/',$data['start_date']);
			$start_day = $start_date_arr[0];
			$start_month = $start_date_arr[1];
			$start_year = $start_date_arr[2];
			$start_year -= 543;
			$start_date = $start_year.'-'.$start_month.'-'.$start_day;
		}
		if($data['end_date']){
			$end_date_arr = explode('/',$data['end_date']);
			$end_day = $end_date_arr[0];
			$end_month = $end_date_arr[1];
			$end_year = $end_date_arr[2];
			$end_year -= 543;
			$end_date = $end_year.'-'.$end_month.'-'.$end_day;
		}
		$where = "";
		if(!empty($data['start_date']) && empty($data['end_date'])) {
			$where = " AND t1.transaction_time BETWEEN '".$start_date." 00:00:00.000' AND '".$start_date." 23:59:59.000'";
		}else if(!empty($data['start_date']) && !empty($data['end_date'])) {
			$where = " AND t1.transaction_time BETWEEN '".$start_date." 00:00:00.000' AND '".$end_date." 23:59:59.000'";
		}

		$members = $this->db->select("t1.account_id,
										t1.transaction_time,
										t1.transaction_deposit,
										t3.member_id,
										t3.firstname_th,
										t3.lastname_th,
										t3.salary,
										t4.prename_full,
										t5.mem_group_name as level_name,
										t6.mem_group_name as faction_name,
										t7.mem_group_name as department_name,
										t8.user_name
									")
							->from("coop_account_transaction as t1")
							->join("coop_maco_account as t2", "t1.account_id = t2.account_id", "inner")
							->join("coop_mem_apply as t3", "t3.member_id = t2.mem_id", "inner")
							->join("coop_prename as t4", "t3.prename_id = t4.prename_id", "left")
							->join("coop_mem_group as t5", "t3.level = t5.id", "left")
							->join("coop_mem_group as t6", "t3.faction = t6.id", "left")
							->join("coop_mem_group as t7", "t3.department = t7.id", "left")
							->join("coop_user as t8", "t1.user_id = t8.username OR t1.user_id = t8.user_id", "left")
							->where("1=1 AND t1.transaction_list = 'DEPP' AND t1.cancel_status is null".$where)
							->order_by("t1.transaction_time, t3.member_id")
							->get()->result_array();

		return $members;
	}

	public function check_report_deposit_month_account_transaction() {
		if($_POST['start_date']){
			$start_date_arr = explode('/',$_POST['start_date']);
			$start_day = $start_date_arr[0];
			$start_month = $start_date_arr[1];
			$start_year = $start_date_arr[2];
			$start_year -= 543;
			$start_date = $start_year.'-'.$start_month.'-'.$start_day;
		}
		if($_POST['end_date']){
			$end_date_arr = explode('/',$_POST['end_date']);
			$end_day = $end_date_arr[0];
			$end_month = $end_date_arr[1];
			$end_year = $end_date_arr[2];
			$end_year -= 543;
			$end_date = $end_year.'-'.$end_month.'-'.$end_day;
		}
		$where = "";
		if(!empty($_POST['start_date']) && empty($_POST['end_date'])) {
			$where = " AND transaction_time BETWEEN '".$start_date." 00:00:00.000' AND '".$start_date." 23:59:59.000'";
		}else if(!empty($_POST['start_date']) && !empty($_POST['end_date'])) {
			$where = " AND transaction_time BETWEEN '".$start_date." 00:00:00.000' AND '".$end_date." 23:59:59.000'";
		}

		$trans = $this->db->select("account_id")
							->from("coop_account_transaction")
							->where("1=1 AND transaction_list = 'DEPP' AND cancel_status is null".$where)
							->get()->result_array();

		if(!empty($trans)){
			echo "success";
		}else{
			echo "";
		}
	}
	
	//รายงานดอกเบี้ยล่วงหน้า
	function coop_report_account_interest_forecast() {
		$arr_data = array();

		//Get Account Type
		$arr_data['type_ids'] = $this->db->select(array('type_id','type_name','type_code'))->from('coop_deposit_type_setting')->order_by("type_seq")->get()->result_array();

		$this->libraries->template('report_deposit_data/coop_report_account_interest_forecast',$arr_data);
	}

	function coop_report_account_interest_forecast_preview() {
		$arr_data = array();

		if(@$_GET['start_date']){
			$start_date_arr = explode('/',@$_GET['start_date']);
			$start_day = $start_date_arr[0];
			$start_month = $start_date_arr[1];
			$start_year = $start_date_arr[2];
			$start_year -= 543;
			$start_date = $start_year.'-'.$start_month.'-'.$start_day;
		}
		
		if(@$_GET['end_date']){
			$end_date_arr = explode('/',@$_GET['end_date']);
			$end_day = $end_date_arr[0];
			$end_month = $end_date_arr[1];
			$end_year = $end_date_arr[2];
			$end_year -= 543;
			$end_date = $end_year.'-'.$end_month.'-'.$end_day;
		}
		
		ob_start();
		$this->deposit_libraries->cal_deposit_interest_forecast($end_date." 05:00:00", "", $_GET['type_id']);
		ob_end_clean();
		
		$_rows = $this->db->select(array('type_id','type_name','type_code'))->from('coop_deposit_type_setting')->where("type_id = '".$_GET['type_id']."'")->get()->result_array();
		$arr_data["type_name"] = empty($_rows[0]["type_code"]) ? "" : $_rows[0]["type_code"]." ".$_rows[0]["type_name"];
		
		$where = "1=1 AND t2.transaction_list IN ('IN', 'INT') AND t2.transaction_time BETWEEN '".$start_date." 00:00:00.000' AND '".$end_date." 23:59:59.000'";
		if(!empty($_GET['type_id'])) $where .= " AND t1.type_id = '".$_GET['type_id']."'";
		
		if($_GET["report_type"] == 0) {
			$sql = "SELECT t3.type_code, t3.type_name, SUM(t2.transaction_deposit) AS transaction_deposit
						FROM coop_maco_account t1
							INNER JOIN coop_account_transaction_forecast t2 ON t1.account_id = t2.account_id
							INNER JOIN coop_deposit_type_setting t3 ON t1.type_id = t3.type_id
						WHERE {$where}
						GROUP BY t3.type_code, t3.type_name";
			$enties = $this->db->query($sql)->result_array();
			
			$arr_data['data'] = $enties;
			
			$this->preview_libraries->template_preview('report_deposit_data/coop_report_account_interest_forecast_preview',$arr_data);
		}
		elseif($_GET["report_type"] == 1) {
			$sql = "SELECT t3.type_code, t3.type_name, t1.account_id, t1.mem_id, t5.prename_short, t4.firstname_th, t4.lastname_th, SUM(t2.transaction_deposit) AS transaction_deposit
						FROM coop_maco_account t1
							INNER JOIN coop_account_transaction_forecast t2 ON t1.account_id = t2.account_id
							INNER JOIN coop_deposit_type_setting t3 ON t1.type_id = t3.type_id
							INNER JOIN coop_mem_apply t4 ON t1.mem_id = t4.member_id
							LEFT OUTER JOIN coop_prename t5 ON t4.prename_id = t5.prename_id
						WHERE {$where}
						GROUP BY t3.type_code, t3.type_name, t1.account_id, t1.mem_id, t5.prename_short, t4.firstname_th, t4.lastname_th";
			$enties = $this->db->query($sql)->result_array();
			
			$data_mas = [];
			$data = [];
			foreach($enties as $row) {
				$data_mas[$row["type_code"]] = [
					"type_code" => $row["type_code"],
					"type_name" => $row["type_name"]
				];
				$data[$row["type_code"]][] = $row;
			}
			
			$arr_data['master'] = $data_mas;
			$arr_data['data'] = $data;
			
			$this->preview_libraries->template_preview('report_deposit_data/coop_report_account_interest_forecast_mem_preview',$arr_data);
		}
	}

	function coop_report_account_interest_forecast_excel() {
		$arr_data = array();
		// set_time_limit (180);
		// $this->db->save_queries = FALSE;

		if(@$_GET['start_date']){
			$start_date_arr = explode('/',@$_GET['start_date']);
			$start_day = $start_date_arr[0];
			$start_month = $start_date_arr[1];
			$start_year = $start_date_arr[2];
			$start_year -= 543;
			$start_date = $start_year.'-'.$start_month.'-'.$start_day;
		}
		
		if(@$_GET['end_date']){
			$end_date_arr = explode('/',@$_GET['end_date']);
			$end_day = $end_date_arr[0];
			$end_month = $end_date_arr[1];
			$end_year = $end_date_arr[2];
			$end_year -= 543;
			$end_date = $end_year.'-'.$end_month.'-'.$end_day;
		}
		
		ob_start();
		$this->deposit_libraries->cal_deposit_interest_forecast($end_date." 05:00:00", "", $_GET['type_id']);
		ob_end_clean();
		
		$_rows = $this->db->select(array('type_id','type_name','type_code'))->from('coop_deposit_type_setting')->where("type_id = '".$_GET['type_id']."'")->get()->result_array();
		$arr_data["type_name"] = empty($_rows[0]["type_code"]) ? "" : $_rows[0]["type_code"]." ".$_rows[0]["type_name"];
		
		$where = "1=1 AND t2.transaction_list IN ('IN', 'INT') AND t2.transaction_time BETWEEN '".$start_date." 00:00:00.000' AND '".$end_date." 23:59:59.000'";
		if(!empty($_GET['type_id'])) $where .= " AND t1.type_id = '".$_GET['type_id']."'";
		
		if($_GET["report_type"] == 0) {
			$sql = "SELECT t3.type_code, t3.type_name, SUM(t2.transaction_deposit) AS transaction_deposit
						FROM coop_maco_account t1
							INNER JOIN coop_account_transaction_forecast t2 ON t1.account_id = t2.account_id
							INNER JOIN coop_deposit_type_setting t3 ON t1.type_id = t3.type_id
						WHERE {$where}
						GROUP BY t3.type_code, t3.type_name";
			$enties = $this->db->query($sql)->result_array();
			
			$arr_data['data'] = $enties;
	
			$this->load->view('report_deposit_data/coop_report_account_interest_forecast_excel',$arr_data);
		}
		elseif($_GET["report_type"] == 1) {
			$sql = "SELECT t3.type_code, t3.type_name, t1.account_id, t1.mem_id, t5.prename_short, t4.firstname_th, t4.lastname_th, SUM(t2.transaction_deposit) AS transaction_deposit
						FROM coop_maco_account t1
							INNER JOIN coop_account_transaction_forecast t2 ON t1.account_id = t2.account_id
							INNER JOIN coop_deposit_type_setting t3 ON t1.type_id = t3.type_id
							INNER JOIN coop_mem_apply t4 ON t1.mem_id = t4.member_id
							LEFT OUTER JOIN coop_prename t5 ON t4.prename_id = t5.prename_id
						WHERE {$where}
						GROUP BY t3.type_code, t3.type_name, t1.account_id, t1.mem_id, t5.prename_short, t4.firstname_th, t4.lastname_th";
			$enties = $this->db->query($sql)->result_array();
			
			$data_mas = [];
			$data = [];
			foreach($enties as $row) {
				$data_mas[$row["type_code"]] = [
					"type_code" => $row["type_code"],
					"type_name" => $row["type_name"]
				];
				$data[$row["type_code"]][] = $row;
			}
			
			$arr_data['master'] = $data_mas;
			$arr_data['data'] = $data;
			
			$this->preview_libraries->template_preview('report_deposit_data/coop_report_account_interest_forecast_mem_excel',$arr_data);
		}
	}
	
	//รายงานดอกเบี้ยเงินฝากล่วงหน้าสมาชิก
	function coop_report_member_interest_forecast() {
		$arr_data = array();
		
		if(!empty($_GET["member_id"])) {
			$this->db->select(array('t1.*',
							't2.mem_group_name AS department_name',
							't3.mem_group_name AS faction_name',
							't4.mem_group_name AS level_name',
							't5.prename_short'));
			$this->db->from('coop_mem_apply as t1');			
			$this->db->join("coop_mem_group AS t2","t1.department = t2.id","left");
			$this->db->join("coop_mem_group AS t3","t1.faction = t3.id","left");
			$this->db->join("coop_mem_group AS t4","t1.level = t4.id","left");
			$this->db->join("coop_prename AS t5","t1.prename_id = t5.prename_id","left");
			$this->db->where("t1.member_id = '".$_GET["member_id"]."'");
			$row = $this->db->get()->row_array();
			$arr_data['row_member'] = $row;
		}
		
		$this->libraries->template('report_deposit_data/coop_report_member_interest_forecast',$arr_data);
	}

	function coop_report_member_interest_forecast_preview() {
		$arr_data = array();
		
		$this->db->select(array('t1.*',
						't2.mem_group_name AS department_name',
						't3.mem_group_name AS faction_name',
						't4.mem_group_name AS level_name',
						't5.prename_short'));
		$this->db->from('coop_mem_apply as t1');			
		$this->db->join("coop_mem_group AS t2","t1.department = t2.id","left");
		$this->db->join("coop_mem_group AS t3","t1.faction = t3.id","left");
		$this->db->join("coop_mem_group AS t4","t1.level = t4.id","left");
		$this->db->join("coop_prename AS t5","t1.prename_id = t5.prename_id","left");
		$this->db->where("t1.member_id = '".$_GET["mem_id"]."'");
		$row = $this->db->get()->row_array();
		$arr_data['row_member'] = $row;
		
		$sql = "SELECT *
					FROM coop_maco_account
					WHERE account_status = '0' AND mem_id = '{$_GET["mem_id"]}'";
		$rs = $this->db->query($sql)->result_array();
		foreach($rs as $row) {
			$this->db->select("coop_deposit_type_setting_detail.*, coop_deposit_type_setting.type_code, coop_deposit_type_setting.type_name");
			$this->db->from("coop_deposit_type_setting_detail");
			$this->db->join("coop_deposit_type_setting", "coop_deposit_type_setting_detail.type_id = coop_deposit_type_setting.type_id", "inner");
			$this->db->where("coop_deposit_type_setting_detail.type_id = '{$row["type_id"]}'");
			$this->db->order_by("start_date", "DESC");
			$this->db->limit(1);
			$_row_detail = $this->db->get()->row_array();
			
			if(in_array($_row_detail["condition_age"], [1, 2 ,3])) {
				// แสดงเฉพาะ บช ที่มีวันครบกำหนด
				$this->db->select("*");
				$this->db->from("coop_account_transaction");
				$this->db->where("account_id = '{$row["account_id"]}'");
				$this->db->order_by("transaction_time, transaction_id");
				$this->db->limit(1);
				$_row_tran = $this->db->get()->row_array();
				
				$rs_date = $this->db->query("SELECT DATE_ADD('".$_row_tran["transaction_time"]."', INTERVAL ".$_row_detail["max_month"]." MONTH) AS date_cal");
				$row_date = $rs_date->row_array();
				if($this->deposit_libraries->is_holiday($row_date["date_cal"])) {
					for($i = 0; $i < 100; $i++) { // infinity loop limit 100
						$rs_date = $this->db->query("SELECT DATE_ADD('".$row_date["date_cal"]."', INTERVAL 1 DAY) AS date_cal");
						$row_date = $rs_date->row_array();
						if(!$this->deposit_libraries->is_holiday($row_date["date_cal"])) {
							break;
						}
					}
				}
				
				ob_start();
				$this->deposit_libraries->cal_deposit_interest_forecast($row_date["date_cal"]." 05:00:00", $row['account_id'], "");
				ob_end_clean();
				
				$sql = "SELECT YEAR(t2.transaction_time) AS y, MONTH(t2.transaction_time) AS m, SUM(t2.transaction_deposit) AS transaction_deposit
							FROM coop_maco_account t1
								INNER JOIN coop_account_transaction_forecast t2 ON t1.account_id = t2.account_id
							WHERE  t2.transaction_list IN ('IN', 'INT') AND t2.account_id = '{$row['account_id']}'
							GROUP BY YEAR(t2.transaction_time), MONTH(t2.transaction_time)";
				$enties = $this->db->query($sql)->result_array();
				
				$arr_data['master'][] = [
					"account_id" => $row['account_id'],
					"account_name" => $row['account_name'],
					"type_code" => $_row_detail['type_code'],
					"type_name" => $_row_detail['type_name']
				];
				$arr_data['data'][] = $enties;
			}
		}
		
		$this->preview_libraries->template_preview('report_deposit_data/coop_report_member_interest_forecast_preview',$arr_data);
	}

	function coop_report_member_interest_forecast_excel() {
		$arr_data = array();
		
		$this->db->select(array('t1.*',
						't2.mem_group_name AS department_name',
						't3.mem_group_name AS faction_name',
						't4.mem_group_name AS level_name',
						't5.prename_short'));
		$this->db->from('coop_mem_apply as t1');			
		$this->db->join("coop_mem_group AS t2","t1.department = t2.id","left");
		$this->db->join("coop_mem_group AS t3","t1.faction = t3.id","left");
		$this->db->join("coop_mem_group AS t4","t1.level = t4.id","left");
		$this->db->join("coop_prename AS t5","t1.prename_id = t5.prename_id","left");
		$this->db->where("t1.member_id = '".$_GET["mem_id"]."'");
		$row = $this->db->get()->row_array();
		$arr_data['row_member'] = $row;
		
		$sql = "SELECT *
					FROM coop_maco_account
					WHERE account_status = '0' AND mem_id = '{$_GET["mem_id"]}'";
		$rs = $this->db->query($sql)->result_array();
		foreach($rs as $row) {
			$this->db->select("coop_deposit_type_setting_detail.*, coop_deposit_type_setting.type_code, coop_deposit_type_setting.type_name");
			$this->db->from("coop_deposit_type_setting_detail");
			$this->db->join("coop_deposit_type_setting", "coop_deposit_type_setting_detail.type_id = coop_deposit_type_setting.type_id", "inner");
			$this->db->where("coop_deposit_type_setting_detail.type_id = '{$row["type_id"]}'");
			$this->db->order_by("start_date", "DESC");
			$this->db->limit(1);
			$_row_detail = $this->db->get()->row_array();
			
			if(in_array($_row_detail["condition_age"], [1, 2 ,3])) {
				// แสดงเฉพาะ บช ที่มีวันครบกำหนด
				$this->db->select("*");
				$this->db->from("coop_account_transaction");
				$this->db->where("account_id = '{$row["account_id"]}'");
				$this->db->order_by("transaction_time, transaction_id");
				$this->db->limit(1);
				$_row_tran = $this->db->get()->row_array();
				
				$rs_date = $this->db->query("SELECT DATE_ADD('".$_row_tran["transaction_time"]."', INTERVAL ".$_row_detail["max_month"]." MONTH) AS date_cal");
				$row_date = $rs_date->row_array();
				if($this->deposit_libraries->is_holiday($row_date["date_cal"])) {
					for($i = 0; $i < 100; $i++) { // infinity loop limit 100
						$rs_date = $this->db->query("SELECT DATE_ADD('".$row_date["date_cal"]."', INTERVAL 1 DAY) AS date_cal");
						$row_date = $rs_date->row_array();
						if(!$this->deposit_libraries->is_holiday($row_date["date_cal"])) {
							break;
						}
					}
				}
				
				ob_start();
				$this->deposit_libraries->cal_deposit_interest_forecast($row_date["date_cal"]." 05:00:00", $row['account_id'], "");
				ob_end_clean();
				
				$sql = "SELECT YEAR(t2.transaction_time) AS y, MONTH(t2.transaction_time) AS m, SUM(t2.transaction_deposit) AS transaction_deposit
							FROM coop_maco_account t1
								INNER JOIN coop_account_transaction_forecast t2 ON t1.account_id = t2.account_id
							WHERE  t2.transaction_list IN ('IN', 'INT') AND t2.account_id = '{$row['account_id']}'
							GROUP BY YEAR(t2.transaction_time), MONTH(t2.transaction_time)";
				$enties = $this->db->query($sql)->result_array();
				
				$arr_data['master'][] = [
					"account_id" => $row['account_id'],
					"account_name" => $row['account_name'],
					"type_code" => $_row_detail['type_code'],
					"type_name" => $_row_detail['type_name']
				];
				$arr_data['data'][] = $enties;
			}
		}

		$this->load->view('report_deposit_data/coop_report_member_interest_forecast_excel',$arr_data);	
	}

	public function coop_report_account_status_detail(){
		$columns = array(
			// "#" 					=> 	"ลำดับ",
			"account_id" 			=> 	"เลขที่บัญชี",
			"account_name"			=>	"ชื่อบัญชี",
			"fullname" 				=> 	"ชื่อ-สกุล",
			"member_id" 			=> 	"รหัสสมาชิก",
			"open_date" 			=> 	"วันที่เปิดบัญชี",
			"close_date" 			=> 	"วันที่ปิดบัญชี",
			
		);
		$arr_data['column'] = $columns;
		$arr_data['mem_type'] = $this->db->get("coop_mem_type")->result_array();
		$arr_data['account_status'] = array("0" => "ใช้งาน", "1" => "ไม่ใช้งาน");
		$this->libraries->template('report_deposit_data/coop_report_account_status_detail',$arr_data);
	}

	public function coop_report_account_status_detail_preview(){
		// var_dump($_POST);
		$columns = array(
			// "#" 					=> 	"ลำดับ",
			"account_id" 			=> 	"เลขที่บัญชี",
			"account_name"			=>	"ชื่อบัญชี",
			"fullname" 				=> 	"ชื่อ-สกุล",
			"member_id" 			=> 	"รหัสสมาชิก",
			"open_date" 			=> 	"วันที่เปิดบัญชี",
			"close_date" 			=> 	"วันที่ปิดบัญชี",
			
		);
		$column = array();
		$column_key = array();
		foreach ($_POST['column'] as $key => $value) {
			
			array_push($column, $columns[$value]);
			array_push($column_key, $value);
		}
		$arr_data['column'] = $column;
		// var_dump($arr_data['column']);
		// exit;
		$tmp_date = explode("/", @$_POST['start_date']);
		$start_date = ($tmp_date[2]-543)."-".$tmp_date[1]."-".sprintf('%02d', $tmp_date[0]);
		$arr_data['start_date'] = $start_date;
		// var_dump(@$_POST['start_date']);exit;
		if(@$_POST['start_date']!=""){
			$this->db->where("(coop_maco_account.created like '".$start_date."%' or coop_maco_account.close_account_date like '".$start_date."%')");
		}

		if(@$_POST['mem_type_id']!=""){
            $mem_type_id = "";
            foreach ($_POST['mem_type_id'] as $key => $value) {
                $mem_type_id .= $value;
                if( $key != sizeof($_POST['mem_type_id'])-1 ){
                    $mem_type_id .= ",";
                }
            }
            $this->db->where("mem_type_id in (".$mem_type_id.")");
		}

		if(@$_POST['account_status']!=""){
            $account_status = "";
            foreach ($_POST['account_status'] as $key => $value) {
                $account_status .= $value;
                if( $key != sizeof($_POST['account_status'])-1 ){
                    $account_status .= ",";
                }
            }
            $this->db->where("account_status in (".$account_status.")");
		}
		// $this->db->limit(1000);
		$this->db->join("coop_mem_apply", "coop_mem_apply.member_id = coop_maco_account.mem_id");
		$this->db->join("coop_prename", "coop_prename.prename_id = coop_mem_apply.prename_id", "left");

		$accounts = $this->db->get_where("coop_maco_account");
		$cell = array();
		
		foreach ($accounts->result_array() as $key => $value) {
			// var_dump($value);

			$tmp = array();
			$column_tmp = $_POST['column'];
			if( sizeof($column_tmp) <= 0 ){
				$column_tmp = ["account_id", "account_name", "fullname", "member_id", "open_date"];
			}
			foreach ($column_key as $col_order) {
				
				if( "account_id" == $col_order ){
					$tmp[] = substr($value['account_id'], 0, 3)."-".substr($value['account_id'], 3,2)."-".substr($value['account_id'], 5,5)."-".substr($value['account_id'], 10);
				}
	
				if( "account_name" == $col_order ){
					$tmp[] = $value['account_name'];
				}
	
				if( "fullname" == $col_order ){
					$tmp[] = $value["prename_short"].$value['firstname_th']." ".$value['lastname_th'];
				}
	
				if( "member_id" == $col_order ){
					$tmp[] = $value['member_id'];
				}
	
				if( "open_date" == $col_order ){
					$tmp[] = $this->center_function->mydate2date($value['created']);
				}
	
				if( "close_date" == $col_order ){
					$tmp[] = $this->center_function->mydate2date($value['close_account_date']);
				}
				array_shift($column_tmp);
			}
			
			if(sizeof($tmp)!=0){
                array_push($cell, $tmp);
            }
		}
		
		$arr_data['data'] = $cell;
		$this->preview_libraries->template_preview('report_deposit_data/coop_report_account_status_detail_preview',$arr_data);

	}

	function coop_report_deposit_balance(){
		$arr_data = array();

		//Get Account Types
		$arr_data['month_arr'] = $this->month_arr;
		$arr_data['type_ids'] = $this->db->select(array('type_id','type_name'))->from('coop_deposit_type_setting')->order_by("type_seq")->get()->result_array();

		$this->libraries->template('report_deposit_data/coop_report_deposit_balance',$arr_data);
	}

	function coop_report_deposit_balance_preview(){
		$arr_data = array();
		
		$this->db->select(array('t1.type_id','t1.type_name'));
		$this->db->from('coop_deposit_type_setting as t1');
		$rs_type = $this->db->get()->result_array();
		$arr_type_deposit = array();
		foreach($rs_type AS $key=>$row_type){
			$arr_type_deposit[$row_type['type_id']] = $row_type['type_name'];

		}
		$arr_data['type_deposit'] = $arr_type_deposit;

		if(!empty($_GET['type_id'])){
			$type_id = $_GET['type_id'];
		}else{
			$type_id = 1;
		}
		
		if(!empty($_GET['start_date'])){
			$start_date = $this->center_function->ConvertToSQLDate($_GET['start_date']);
		}	
		
		if(!empty($_GET['end_date'])){
			$end_date = $this->center_function->ConvertToSQLDate($_GET['end_date']);
		}
		
		$arr_data['code_th'] = $this->report_accrued->get_code_th(); //ตัวย่อการทำรายการ ภาษาไทย
		$arr_data['arr_run_row'] = $this->report_accrued->get_run_row_transaction();
		$arr_data['code_type_int'] = $this->report_accrued->get_code_type('I'); //code ดอกเบี้ย	
		
		$sql = "SELECT T.* FROM (
			SELECT 
				t1.transaction_time,
				 '' as transaction_list,
				t1.account_id,
				'0'  as transaction_deposit,
				'0'  as transaction_withdrawal,
				t1.transaction_balance,
				t1.transaction_id,
				t3.account_name,
				t1.seq_no AS seq_no,
				t1.seq_chk AS seq_chk,
				'1' AS c_num,
				t1.ref_account_no,
				t1.transaction_time AS date_due 
			FROM 
				coop_account_transaction t1
			INNER JOIN (
					SELECT max(inner_t1.transaction_id) as transaction_id, inner_t1.account_id FROM coop_account_transaction inner_t1 
					INNER JOIN (
					SELECT max(a.transaction_time) as transaction_time, a.account_id FROM coop_account_transaction a inner join coop_maco_account b ON a.account_id= b.account_id  WHERE a.transaction_time < '{$start_date} 00:00:00' and b.type_id = '{$type_id}' GROUP BY account_id) inner_t2 ON inner_t1.account_id=inner_t2.account_id AND inner_t1.transaction_time = inner_t2.transaction_time
					GROUP BY inner_t1.account_id ) t2 ON t1.account_id=t2.account_id AND t1.transaction_id=t2.transaction_id
			INNER JOIN coop_maco_account t3 ON t2.account_id = t3.account_id
			LEFT JOIN coop_user t4 ON t1.user_id = t4.user_id
			WHERE t1.transaction_balance > 0
			AND t1.account_id NOT IN (SELECT
										t3.account_id
									FROM
										coop_account_transaction AS t3
										INNER JOIN coop_maco_account AS t5 ON t3.account_id = t5.account_id 
									WHERE
										t5.type_id = '{$type_id}' 
										AND t3.transaction_time BETWEEN '{$start_date} 00:00:00.000' AND '{$end_date} 23:59:59.000')
			UNION ALL
				SELECT
					t3.transaction_time,
					t3.transaction_list,
					t3.account_id,
					t3.transaction_deposit,
					t3.transaction_withdrawal,
					t3.transaction_balance,
					t3.transaction_id,
					t5.account_name,
					t3.seq_no AS seq_no,
					t3.seq_chk AS seq_chk,
					'1' AS c_num,
					t3.ref_account_no,
					t3.transaction_time AS date_due 
				FROM
					coop_account_transaction AS t3
					LEFT JOIN coop_user AS t4 ON t3.user_id = t4.user_id
					INNER JOIN coop_maco_account AS t5 ON t3.account_id = t5.account_id 
				WHERE
					t5.type_id = '{$type_id}' 
					AND t3.transaction_time BETWEEN '{$start_date} 00:00:00.000' AND '{$end_date} 23:59:59.000' 
				
			UNION ALL
				SELECT
					  t1.transaction_time AS transaction_time,
						'DFX' AS transaction_list,
						t1.account_id AS account_id,
						t1.balance_deposit AS transaction_deposit,
						'0' AS transaction_withdrawal,
						t1.transaction_balance AS transaction_balance,
						t1.transaction_id AS transaction_id,
						t2.account_name,
						t1.seq_no + t1.seq_chk AS seq_no,
						t1.seq_chk AS seq_chk,
						'2' AS c_num,
						t1.ref_account_no,
						IF( t3.transaction_time != '', t3.transaction_time, t1.transaction_time ) AS date_due
				FROM
					coop_account_transaction t1
				INNER JOIN coop_maco_account AS t2 ON t1.account_id = t2.account_id
				LEFT JOIN coop_account_transaction AS t3 ON t1.account_id = t3.account_id 
					AND t1.ref_account_no = t3.ref_account_no 
					AND t3.transaction_list = 'DFX' 
					AND YEAR ( t1.transaction_time ) = YEAR ( t3.transaction_time ) 
				WHERE
					(
						t1.transaction_list = 'WCA'
					)
					AND t2.type_id = '{$type_id}'
					AND t1.transaction_time BETWEEN '{$start_date} 00:00:00.000' AND '{$end_date} 23:59:59.000' 
					AND t3.transaction_time BETWEEN '{$start_date} 00:00:00.000' AND '{$end_date} 23:59:59.000' 
			) T 
			-- WHERE  T.date_due BETWEEN '{$start_date} 00:00:00.000' AND '{$end_date} 23:59:59.000' 
			ORDER BY T.account_id ASC, T.transaction_time ASC, T.transaction_id ASC, T.c_num ASC";
		//echo $sql.'<br>'; exit;
		$data_row = $this->db->query($sql)->result_array();
		
		//นับจำนวนของรายการบัญชี
		$sql_count_account = "SELECT
								count(T2.account_id) AS count_account_id,T2.account_id
							FROM
								( ".$sql." ) T2 GROUP BY T2.account_id";
							
		$row_count_account = $this->db->query($sql_count_account)->result_array();
		$arr_count_account = array_column($row_count_account, 'count_account_id', 'account_id');

		$sql2 = "SELECT
				t1.transaction_time,
				t1.account_id,
				t1.transaction_balance,
				t1.transaction_id
			FROM
				coop_account_transaction t1
				INNER JOIN (
				SELECT
					max( inner_t1.transaction_id ) AS transaction_id,
					inner_t1.account_id 
				FROM
					coop_account_transaction inner_t1
					INNER JOIN (
					SELECT
						max( a.transaction_time ) AS transaction_time,
						a.account_id 
					FROM
						coop_account_transaction a
						INNER JOIN coop_maco_account b ON a.account_id = b.account_id 
					WHERE
						a.transaction_time < '{$start_date} 00:00:00' and b.type_id = '{$type_id}'
					GROUP BY
						account_id 
					) inner_t2 ON inner_t1.account_id = inner_t2.account_id 
					AND inner_t1.transaction_time = inner_t2.transaction_time 
				GROUP BY
					inner_t1.account_id 
				) t2 ON t1.account_id = t2.account_id 
				AND t1.transaction_id = t2.transaction_id
			WHERE t1.transaction_balance > 0
			GROUP BY t1.account_id
			ORDER BY t1.account_id";
		$row_transaction_balance = $this->db->query($sql2)->result_array();
		$arr_transaction_balance = array_column($row_transaction_balance, 'transaction_balance', 'account_id');

		$flag = 0;
		$temp_data = array();
		$runno = 0;
		$data = array();
		$chk_account_id  = '';
		//echo '<pre>'; print_r($data_row); echo '</pre>';
		if(!empty($data_row)){
			foreach(@$data_row as $key => $row){
				$runno++;
				$data[$runno]['account_id'] = $row['account_id'];
				$data[$runno]['account_name'] = $row['account_name'];

				if(@$data_row[$key + 1]['transaction_list'] == 'WCHE'){	
					@$temp_data['transaction_withdrawal'] = @$data_row[$key + 1]['transaction_withdrawal'] - $row['transaction_deposit'];
					@$temp_data['w_interest'] = @$row['transaction_deposit'];
					@$temp_data['seq_no'] = @$row['seq_no'];
					$runno--;
					$flag = 1;
					continue;
				}else{
					if ($flag) {						
						$row['transaction_withdrawal'] = $temp_data['transaction_withdrawal'];
						$row['w_interest'] = $temp_data['w_interest'];
						$row['seq_no'] = $temp_data['seq_no'];
						unset($temp_data);
						$flag = 0;
					}
				}

				if($chk_account_id != $row['account_id']){
					$bf_transaction_balance = @$arr_transaction_balance[@$row['account_id']];
					$chk_account_id = $row['account_id'];
					$i=1;
				}else{
					$bf_transaction_balance = 0;
					$i++;
				}
				
				$chk_row = $arr_count_account[$chk_account_id];
				
				if($i == $chk_row){
					$transaction_balance = $row['transaction_balance'];
				}else{
					$transaction_balance = 0;
				}
				
				$transaction_withdrawal = $row['transaction_withdrawal'];
				$w_interest = $row['w_interest'];
				$data[$runno]['bf_transaction_balance'] = $bf_transaction_balance;

				if(in_array($row['transaction_list'],$arr_data['code_type_int'])){
					$d_interest = $row['transaction_deposit'];
					$transaction_deposit = 0;
				}else{
					$d_interest = 0;
					$transaction_deposit = $row['transaction_deposit'];
				}

				$w_transaction_time  = '';
				$w_transaction_list  = '';
				if($row['transaction_withdrawal'] > 0){
					$w_transaction_time = $row['transaction_time']; 
					$w_transaction_list = $row['transaction_list'];
				}

				$d_transaction_time  = '';
				$d_transaction_list  = '';
				if($row['transaction_deposit'] > 0){
					$d_transaction_time = ($row['c_num'] == '2')?$row['date_due']:$row['transaction_time'];
					$d_transaction_list = $row['transaction_list'];
				}

				$seq_no = '';
				if(@$transaction_deposit > 0 || @$d_interest> 0 || @$transaction_withdrawal > 0 || @$d_interest > 0){
					$seq_no = $row['seq_no'];
				}else{
					$seq_no = '';
				}

				$data[$runno]['seq_no'] = $seq_no;
				
				$data[$runno]['transaction_balance'] = $transaction_balance;

				//ฝาก
				$data[$runno]['d_transaction_list'] = $d_transaction_list;
				$data[$runno]['d_transaction_time'] = $d_transaction_time;
				$data[$runno]['transaction_deposit'] = $transaction_deposit;
				$data[$runno]['d_interest'] = $d_interest;

				//ถอน
				$data[$runno]['w_transaction_list'] = $w_transaction_list;
				$data[$runno]['w_transaction_time'] = $w_transaction_time;
				$data[$runno]['transaction_withdrawal'] = $transaction_withdrawal;
				$data[$runno]['w_interest'] = $w_interest;
			}
		}
//echo '<pre>'; print_r($data); echo '</pre>';
		//แบ่งหน้า
		$depositCount = count($data);
		$firstPage = 19;
		$perPage = 20;
		
		$total = 0;
		$page = 0;
		$duplicate_index = 0;
		
		$deposit_data = array();

		if(!empty($data)){
			foreach($data AS $index=>$val){
				if (($index + $duplicate_index - 1) <= $firstPage) {
					$page = 1;
				} else {
					$page = ceil(($index + $duplicate_index - $firstPage -1)/$perPage)+1;
				}

				$deposit_data[$page][] = $val;
			}
		}
		$page_all = $depositCount <= $firstPage ? 1 : ceil(($depositCount - $firstPage)/$perPage)+1;

		$arr_data['num_rows'] = $row['num_rows'];
		$arr_data['paging'] = $paging;
		$arr_data['data'] = $deposit_data;
		$arr_data['page_all'] = $page_all;
		
		$this->preview_libraries->template_preview('report_deposit_data/coop_report_deposit_balance_preview',$arr_data);

	}

	public function check_report_deposit_balance() {
		if(!empty($_POST['type_id'])){
			$type_id = $_POST['type_id'];
		}else{
			$type_id = 1;
		}
		
		if(!empty($_POST['start_date'])){
			$start_date = $this->center_function->ConvertToSQLDate($_POST['start_date']);
		}	
		
		if(!empty($_POST['end_date'])){
			$end_date = $this->center_function->ConvertToSQLDate($_POST['end_date']);
		}
		
		$sql = "SELECT * FROM (
			SELECT 
				t1.transaction_time,
				 '' as transaction_list,
				t1.account_id,
				'0'  as transaction_deposit,
				'0'  as transaction_withdrawal,
				t1.transaction_balance,
				t1.transaction_id,
				t3.account_name,
				t1.seq_no AS seq_no,
				t1.seq_chk AS seq_chk,
				'1' AS c_num 
			FROM 
				coop_account_transaction t1
			INNER JOIN (
					SELECT max(inner_t1.transaction_id) as transaction_id, inner_t1.account_id FROM coop_account_transaction inner_t1 
					INNER JOIN (
					SELECT max(a.transaction_time) as transaction_time, a.account_id FROM coop_account_transaction a inner join coop_maco_account b ON a.account_id= b.account_id  WHERE a.transaction_time < '{$start_date} 00:00:00' and b.type_id = '{$type_id}' GROUP BY account_id) inner_t2 ON inner_t1.account_id=inner_t2.account_id AND inner_t1.transaction_time = inner_t2.transaction_time
					GROUP BY inner_t1.account_id ) t2 ON t1.account_id=t2.account_id AND t1.transaction_id=t2.transaction_id
			INNER JOIN coop_maco_account t3 ON t2.account_id = t3.account_id
			LEFT JOIN coop_user t4 ON t1.user_id = t4.user_id
			WHERE t1.transaction_balance > 0
			AND t1.account_id NOT IN (SELECT
										t3.account_id
									FROM
										coop_account_transaction AS t3
										INNER JOIN coop_maco_account AS t5 ON t3.account_id = t5.account_id 
									WHERE
										t5.type_id = '{$type_id}' 
										AND t3.transaction_time BETWEEN '{$start_date} 00:00:00.000' AND '{$end_date} 23:59:59.000')
			UNION ALL
				SELECT
					t3.transaction_time,
					t3.transaction_list,
					t3.account_id,
					t3.transaction_deposit,
					t3.transaction_withdrawal,
					t3.transaction_balance,
					t3.transaction_id,
					t5.account_name,
					t3.seq_no AS seq_no,
					t3.seq_chk AS seq_chk,
					'1' AS c_num 
				FROM
					coop_account_transaction AS t3
					LEFT JOIN coop_user AS t4 ON t3.user_id = t4.user_id
					INNER JOIN coop_maco_account AS t5 ON t3.account_id = t5.account_id 
				WHERE
					t5.type_id = '{$type_id}' 
					AND t3.transaction_time BETWEEN '{$start_date} 00:00:00.000' AND '{$end_date} 23:59:59.000' 
				
			UNION ALL
				SELECT
					  t1.transaction_time AS transaction_time,
						'DFX' AS transaction_list,
						t1.account_id AS account_id,
						t1.balance_deposit AS transaction_deposit,
						'0' AS transaction_withdrawal,
						t1.transaction_balance AS transaction_balance,
						t1.transaction_id AS transaction_id,
						t2.account_name,
						t1.seq_no + t1.seq_chk AS seq_no,
						t1.seq_chk AS seq_chk,
						'2' AS c_num
				FROM
					coop_account_transaction t1
				INNER JOIN coop_maco_account AS t2 ON t1.account_id = t2.account_id
				WHERE
					(
						t1.transaction_list = 'WCA'
					)
					AND t2.type_id = '{$type_id}'
					AND t1.transaction_time BETWEEN '{$start_date} 00:00:00.000' AND '{$end_date} 23:59:59.000' 
			) T  ORDER BY T.account_id ASC, T.transaction_time ASC, T.transaction_id ASC, T.c_num ASC";
		//echo $sql.'<br>'; exit;
		$data_row = $this->db->query($sql)->result_array();
		
		if(!empty($data_row)){
			echo "success";
		}else{
			echo "";
		}	
	}

	function coop_report_payment_deposit(){
		$arr_data = array();
		$arr_data['month_arr'] = $this->month_arr;
		$arr_data['type_ids'] = $this->db->select(array('type_id','type_name','type_code'))->from('coop_deposit_type_setting')->order_by("type_seq")->get()->result_array();
		$this->libraries->template('report_deposit_data/coop_report_payment_deposit',$arr_data);
	}
	public function check_report_payment_deposit() {
		if($_POST['start_date']){
			$start_date_arr = explode('/',$_POST['start_date']);
			$start_day = $start_date_arr[0];
			$start_month = $start_date_arr[1];
			$start_year = $start_date_arr[2];
			$start_year -= 543;
			$start_date = $start_year.'-'.$start_month.'-'.$start_day." 00:00:00";
		}
		if($_POST['end_date']){
			$end_date_arr = explode('/',$_POST['end_date']);
			$end_day = $end_date_arr[0];
			$end_month = $end_date_arr[1];
			$end_year = $end_date_arr[2];
			$end_year -= 543;
			$end_date = $end_year.'-'.$end_month.'-'.$end_day." 23:59:59";
		}
	$type_id=$_POST['type_id'];

		$members = $this->db->select("t1.transaction_id")
							->from("coop_account_transaction AS t1")
							->join("coop_maco_account AS t2","t1.account_id = t2.account_id","inner")
							->where("t1.transaction_time BETWEEN '{$start_date}' AND '{$end_date}' AND t2.type_id = '{$type_id}'")
							->get()->result_array();

		if(!empty($members)){
			echo "success";
		}else{
			echo "";
		}	
	}


	function coop_report_payment_deposit_preview(){
		// echo "<pre>";print_r($_GET);exit;
		if(@$_GET['start_date']){
			$start_date_arr = explode('/',@$_GET['start_date']);
			$start_day = $start_date_arr[0];
			$start_month = $start_date_arr[1];
			$start_year = $start_date_arr[2];
			$start_year -= 543;
			$start_date = $start_year.'-'.$start_month.'-'.$start_day.' '.'00:00:00';
		}
		
		if(@$_GET['end_date']){
			$end_date_arr = explode('/',@$_GET['end_date']);
			$end_day = $end_date_arr[0];
			$end_month = $end_date_arr[1];
			$end_year = $end_date_arr[2];
			$end_year -= 543;
			$end_date = $end_year.'-'.$end_month.'-'.$end_day.' '.'23:59:59';
		}
			$type_id=$_GET['type_id'];
			$arr_data['data'] = $this->Report_payment->get_coop_account_transaction($start_date,$end_date,$type_id);
			$arr_data['text_title_1'] = @$_SESSION['COOP_NAME'];
			// $arr_data['text_title_2'] = $this->Report_payment->get_row_head($type_id);
			// echo "<pre>";print_r($arr_data);exit;
			if($_GET['report']=='2'){
				$this->load->view('report_deposit_data/coop_report_payment_deposit_excel',$arr_data);
			}else{
				$this->preview_libraries->template_preview('report_deposit_data/coop_report_payment_deposit_detail_preview', $arr_data);
			}
		
		

	}
	function coop_report_payment_deposit_excel(){
		echo "<pre>";print_r($_GET);exit;
		if(@$_GET['start_date']){
			$start_date_arr = explode('/',@$_GET['start_date']);
			$start_day = $start_date_arr[0];
			$start_month = $start_date_arr[1];
			$start_year = $start_date_arr[2];
			$start_year -= 543;
			$start_date = $start_year.'-'.$start_month.'-'.$start_day.' '.'00:00:00';
		}
		
		if(@$_GET['end_date']){
			$end_date_arr = explode('/',@$_GET['end_date']);
			$end_day = $end_date_arr[0];
			$end_month = $end_date_arr[1];
			$end_year = $end_date_arr[2];
			$end_year -= 543;
			$end_date = $end_year.'-'.$end_month.'-'.$end_day.' '.'23:59:59';
		}

		$type_id=$_GET['type_id'];
		
		
			$arr_data['data'] = $this->Report_payment->get_coop_account_transaction($start_date,$end_date,$type_id);
			$arr_data['text_title_1'] = @$_SESSION['COOP_NAME'];
			// $arr_data['text_title_2'] = $this->Report_payment->get_row_head($type_id);
			//echo "<pre>";print_r($arr_data);exit;
			$this->load->view('report_deposit_data/coop_report_payment_deposit_excel',$arr_data);
		

	}

}
