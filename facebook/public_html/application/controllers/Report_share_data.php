<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Report_share_data extends CI_Controller {
	function __construct()
	{
		parent::__construct();
	}
	
	public function coop_report_change_share(){
		$arr_data = array();
		$this->db->select(array('id','loan_type'));
		$this->db->from('coop_loan_type');
		$this->db->order_by('order_by ASC');
		$rs_loan_type = $this->db->get()->result_array();
		//print_r($this->db->last_query()); exit;
		$loan_type = array();
		if(!empty($rs_loan_type)){
			foreach($rs_loan_type as $key => $row_loan_type){
				$loan_type[$row_loan_type['id']] = @$row_loan_type['loan_type'];
			}
		}
		$arr_data['loan_type'] = $loan_type;
		$this->libraries->template('report_share_data/coop_report_change_share',$arr_data);
	}
	
	function check_report_change_share(){
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

		$where_check = "";
		$where_check2 = "";

		if(@$_POST['start_date'] != '' AND @$_POST['end_date'] == ''){
			$where_check = " AND t1.create_date BETWEEN '".$start_date." 00:00:00.000' AND '".$start_date." 23:59:59.000'";
			$where_check2 = " AND t1.share_date BETWEEN '".$start_date." 00:00:00.000' AND '".$start_date." 23:59:59.000'";
		}else if(@$_POST['start_date'] != '' AND @$_POST['end_date'] != ''){
			$where_check = " AND t1.create_date BETWEEN '".$start_date." 00:00:00.000' AND '".$end_date." 23:59:59.000'";
			$where_check2 = " AND t1.share_date BETWEEN '".$start_date." 00:00:00.000' AND '".$end_date." 23:59:59.000'";
		}
		
		$this->db->select('t2.member_id');
		$this->db->from('coop_change_share as t1');
		$this->db->join("coop_mem_apply as t2", "t1.member_id = t2.member_id", "inner");
		$this->db->join("coop_prename as t3", "t2.prename_id = t3.prename_id", "left");
		$this->db->where("t1.change_share_status IN ('1', '2') {$where_check}");
		$rs_check = $this->db->get()->result_array();		
		$row_check = @$rs_check[0];
		//print_r($this->db->last_query()); exit;
		
		$this->db->select('t2.member_id');
		$this->db->from('coop_mem_share as t1');
		$this->db->join("coop_mem_apply as t2", "t1.member_id = t2.member_id", "inner");
		$this->db->join("coop_prename as t3", "t2.prename_id = t3.prename_id", "left");
		$this->db->where("t1.share_status IN ('1', '2') AND t1.share_type = 'SPA'  {$where_check2}");
		$rs_check2 = $this->db->get()->result_array();
		$row_check2 = @$rs_check2[0];
		
		if(@$row_check['member_id'] != '' || @$row_check2['member_id'] != ''){
			echo "success";
		}
	}
	
	function coop_report_change_share_preview(){
		$arr_data = array();
		$this->db->select(array('id','mem_group_name'));
		$this->db->from('coop_mem_group');
		$rs_group = $this->db->get()->result_array();
		$mem_group_arr = array();
		foreach($rs_group as $key => $row_group){
			$mem_group_arr[$row_group['id']] = $row_group['mem_group_name'];
		}
		$arr_data['mem_group_arr'] = $mem_group_arr;
		
		$this->db->select(array('id','loan_type'));
		$this->db->from('coop_loan_type');
		$rs_loan_type = $this->db->get()->result_array();
		$loan_type = array();
		foreach($rs_loan_type as $key => $row_loan_type){
			$loan_type[$row_loan_type['id']] = $row_loan_type['loan_type'];
		}
		$arr_data['loan_type'] = $loan_type;
		
		$this->db->select(array('setting_value'));
		$this->db->from('coop_share_setting');
		$this->db->where("setting_id = '1'");
		$row_share_value = $this->db->get()->result_array();
		$share_value = $row_share_value[0]['setting_value'];
		$arr_data['share_value'] = $share_value;
		
		$arr_data['month_arr'] = $this->center_function->month_arr();
		$arr_data['month_short_arr'] = $this->center_function->month_short_arr();
		
		$this->preview_libraries->template_preview('report_share_data/coop_report_change_share_preview',$arr_data);
	}
	
	function coop_report_change_share_excel(){
		$arr_data = array();
		$this->db->select(array('id','mem_group_name'));
		$this->db->from('coop_mem_group');
		$rs_group = $this->db->get()->result_array();
		$mem_group_arr = array();
		foreach($rs_group as $key => $row_group){
			$mem_group_arr[$row_group['id']] = $row_group['mem_group_name'];
		}
		$arr_data['mem_group_arr'] = $mem_group_arr;
		
		$this->db->select(array('id','loan_type'));
		$this->db->from('coop_loan_type');
		$rs_loan_type = $this->db->get()->result_array();
		$loan_type = array();
		foreach($rs_loan_type as $key => $row_loan_type){
			$loan_type[$row_loan_type['id']] = $row_loan_type['loan_type'];
		}
		$arr_data['loan_type'] = $loan_type;
		
		$this->db->select(array('setting_value'));
		$this->db->from('coop_share_setting');
		$this->db->where("setting_id = '1'");
		$row_share_value = $this->db->get()->result_array();
		$share_value = $row_share_value[0]['setting_value'];
		$arr_data['share_value'] = $share_value;
		
		$this->load->view('report_share_data/coop_report_change_share_excel',$arr_data);
	}
	
	public function coop_report_share_loan_balance(){
		$arr_data = array();

		$this->db->select(array('id','mem_group_name'));
		$this->db->from('coop_mem_group');
		$this->db->where("mem_group_type = '1'");
		$row = $this->db->get()->result_array();
		$arr_data['row_mem_group'] = $row;
		
		$arr_data['month_arr'] = $this->center_function->month_arr();
		$arr_data['month_short_arr'] = $this->center_function->month_short_arr();
		
		$this->libraries->template('report_share_data/coop_report_share_loan_balance',$arr_data);
	}
	
	function coop_report_share_loan_balance_preview(){
        set_time_limit(-1);
		if(@$_GET['start_date']){
			$start_date_arr = explode('/',@$_GET['start_date']);
			$start_day = $start_date_arr[0];
			$start_month = $start_date_arr[1];
			$start_year = $start_date_arr[2];
			$start_year -= 543;
			$get_start_date = $start_year.'-'.$start_month.'-'.$start_day;
			
		}
		
		if(@$_GET['type_date'] == '1'){
			$this->db->select(array('share_date'));
			$this->db->from('coop_mem_share');
			$this->db->where("share_status IN ('1', '2')");
			$this->db->order_by("share_date ASC");
			$this->db->limit(1);
			$rs_date_share = $this->db->get()->result_array();
			$date_share_min  =  date("Y-m-d", strtotime(@$rs_date_share[0]['share_date']));
			
			
			$this->db->select(array('createdatetime'));
			$this->db->from('coop_loan');
			$this->db->where("loan_status = '1'");
			$this->db->order_by("createdatetime ASC");
			$this->db->limit(1);
			$rs_date_loan = $this->db->get()->result_array();
			$date_loan_min  =  date("Y-m-d", strtotime(@$rs_date_loan[0]['createdatetime']));
			
			$this->db->select(array('transaction_datetime'));
			$this->db->from('coop_loan_atm_transaction');
			$this->db->order_by("transaction_datetime ASC");
			$this->db->limit(1);
			$rs_date_loan_atm = $this->db->get()->result_array();
			$date_loan_atm_min  =  date("Y-m-d", strtotime(@$rs_date_loan_atm[0]['transaction_datetime']));

			if($date_share_min < $date_loan_min){
				$start_date = $date_share_min;
			}else if($date_loan_min < $date_loan_atm_min){
				$start_date = $date_loan_min;
			}else if($date_loan_atm_min < $date_share_min){
				$start_date = $date_loan_atm_min;
			}else{
				$start_date = $date_share_min;
			}
			$end_date = $get_start_date;
		}else{		
			$start_date = $get_start_date;
			$end_date = $get_start_date;
		}
		
		
		$where_date = "";		
		$where_date_loan = "";		
		$where_date_loan_atm = "";		
		$where_date_loan_atm_transaction = "";		
		$where_date_loan_transaction = "";		
		if(@$_GET['start_date'] != ''){
			$where_date .= " AND coop_mem_share.share_date BETWEEN '".$start_date." 00:00:00.000' AND '".$end_date." 23:59:59.000'";
			$where_date_loan .= " AND coop_loan.createdatetime BETWEEN '".$start_date." 00:00:00.000' AND '".$end_date." 23:59:59.000'";
			$where_date_loan_atm .= " AND coop_loan_atm.createdatetime BETWEEN '".$start_date." 00:00:00.000' AND '".$end_date." 23:59:59.000'";
			$where_date_loan_atm_transaction .= " AND coop_loan_atm_transaction.transaction_datetime BETWEEN '".$start_date." 00:00:00.000' AND '".$end_date." 23:59:59.000'";
			$where_date_loan_transaction .= " AND coop_loan_transaction.transaction_datetime BETWEEN '".$start_date." 00:00:00.000' AND '".$end_date." 23:59:59.000'";
		}

		$this->db->select(array('coop_loan_name.loan_name_id','coop_loan_type.loan_type_code'));
		$this->db->from('coop_loan_name');
		$this->db->join('coop_loan_type','coop_loan_name.loan_type_id = coop_loan_type.id','left');
		$rs_type_code = $this->db->get()->result_array();
		$arr_loan_type_code = array();
		foreach($rs_type_code AS $key_type_code=>$row_type_code){
			$arr_loan_type_code[@$row_type_code['loan_name_id']] = @$row_type_code['loan_type_code'];
		}	
		
		$sql = "SELECT `coop_mem_apply`.`member_id`, `coop_mem_apply`.`prename_id`, `coop_mem_apply`.`firstname_th`, `coop_mem_apply`.`lastname_th`, `coop_mem_apply`.`department`, `coop_mem_apply`.`faction`, `coop_mem_apply`.`level`,
				
				`t4`.`share_collect`, `t4`.`share_collect_value`, `t4`.`share_id`, `t4`.`share_period`, `t4`.`share_date`,
				`t5`.`loan_id`, `t5`.`loan_amount_balance`, `t5`.`contract_number`, `t5`.`loan_type`,
				`t6`.`loan_atm_id`, `t6`.`contract_number` AS `contract_number_atm`, `t6`.`loan_amount_balance_atm`
				FROM (SELECT IF (
								(SELECT level_old FROM coop_mem_group_move WHERE date_move >= '".$end_date."' AND coop_mem_group_move.member_id = coop_mem_apply.member_id ORDER BY date_move ASC LIMIT 1),
								(SELECT level_old FROM coop_mem_group_move WHERE date_move >= '".$end_date."' AND coop_mem_group_move.member_id = coop_mem_apply.member_id ORDER BY date_move ASC LIMIT 1),
								coop_mem_apply. level
							) AS level,
							IF (
								(SELECT faction_old FROM coop_mem_group_move WHERE date_move >= '".$end_date."' AND coop_mem_group_move.member_id = coop_mem_apply.member_id ORDER BY date_move ASC LIMIT 1),
								(SELECT faction_old FROM coop_mem_group_move WHERE date_move >= '".$end_date."' AND coop_mem_group_move.member_id = coop_mem_apply.member_id ORDER BY date_move ASC LIMIT 1),
								coop_mem_apply.faction
							) AS faction,
							IF (
								(SELECT department_old FROM coop_mem_group_move WHERE date_move >= '".$end_date."' AND coop_mem_group_move.member_id = coop_mem_apply.member_id ORDER BY date_move ASC LIMIT 1),
								(SELECT department_old FROM coop_mem_group_move WHERE date_move >= '".$end_date."' AND coop_mem_group_move.member_id = coop_mem_apply.member_id ORDER BY date_move ASC LIMIT 1),
								coop_mem_apply.department
							) AS department, member_id, prename_id, firstname_th, lastname_th,member_status FROM coop_mem_apply) AS coop_mem_apply
		
				LEFT JOIN (SELECT t1.share_id, t1.share_collect, t1.share_collect_value, t1.member_id, t1.share_period, t1.share_date
							FROM coop_mem_share as t1
							WHERE t1.share_date BETWEEN '".$start_date." 00:00:00.000' AND '".$end_date." 23:59:59.000' GROUP BY member_id) AS t4 ON `coop_mem_apply`.`member_id` = `t4`.`member_id`
				LEFT JOIN (SELECT t3.member_id ,t3.contract_number ,t3.period_now ,t3.loan_type 
								,t1.loan_transaction_id
								,t1.loan_id
								,t1.loan_amount_balance
								,t1.transaction_datetime FROM coop_loan_transaction AS t1 LEFT JOIN coop_loan AS t3 ON t1.loan_id = t3.id WHERE t1.transaction_datetime BETWEEN '".$start_date." 00:00:00.000' AND '".$end_date." 23:59:59.000' GROUP BY t1.loan_id ORDER BY t1.loan_id DESC ,t1.loan_transaction_id DESC )
								AS t5 ON `coop_mem_apply`.`member_id` = `t5`.`member_id`
				LEFT JOIN (SELECT t3.member_id ,t3.contract_number
								,t1.loan_atm_transaction_id
								,t1.loan_atm_id
								,t1.loan_amount_balance as loan_amount_balance_atm
								FROM coop_loan_atm_transaction AS t1 LEFT JOIN coop_loan_atm AS t3 ON t1.loan_atm_id = t3.loan_atm_id WHERE t1.transaction_datetime BETWEEN '".$start_date." 00:00:00.000' AND '".$end_date." 23:59:59.000'
								GROUP BY t1.loan_atm_id ORDER BY t1.loan_atm_id DESC ,t1.loan_atm_transaction_id DESC ) AS t6
								ON `coop_mem_apply`.`member_id` = `t6`.`member_id`
				WHERE (`t5`.`loan_id` != '' OR `t4`.`share_id` != '' OR `t6`.`loan_atm_id` != '') AND coop_mem_apply.member_status = 1
				ORDER BY coop_mem_apply.member_id ASC";
		if($_GET['debug'] == "on") {
            echo $sql.'<hr>'; //exit;
        }
		$result = $this->db->query($sql)->result_array();
		
		$member_ids = array_column($result, 'member_id');
		//echo '<pre>'; print_r($member_ids); echo '</pre>'; exit;
		
		//Get Lastest Loan Information
		$loan_ids = array_column($result, 'loan_id');
        $where_loan = " 1=1 ";
        if(sizeof(array_filter($loan_ids))){
            $where_loan = " t1.loan_id IN  (".implode(',',array_filter($loan_ids)).") ";
        }
		$loans = $this->db->query("SELECT `t1`.`loan_transaction_id`, `t1`.`loan_id`, `t1`.`loan_amount_balance`, `t1`.`transaction_datetime`
									FROM `coop_loan_transaction` as `t1`
									INNER JOIN (SELECT loan_id, MAX(cast(transaction_datetime as Datetime)) as max FROM coop_loan_transaction WHERE transaction_datetime BETWEEN '".$start_date." 00:00:00' AND '".$end_date." 23:59:59' group by loan_id)
											as t2 ON `t1`.`loan_id` = `t2`.`loan_id` AND `t1`.`transaction_datetime` = `t2`.`max`
									WHERE {$where_loan}
									ORDER BY `t1`.`transaction_datetime`, `t1`.`loan_transaction_id` DESC
									")->result_array();
		$loan_members = array_column($loans, 'loan_id');
		//echo '<pre>'; print_r($loans); echo '</pre>'; exit;	
		//echo $this->db->last_query();exit;
		//Get Lastest Loan ATM Information
		$loan_atm_ids = array_column($result, 'loan_atm_id');
        $where_atm = " 1=1 ";
        if(sizeof(array_filter($loan_atm_ids))){
            $where_atm = " t1.loan_atm_id IN  (".implode(',',array_filter($loan_atm_ids)).") ";
        }
		$loan_atms = $this->db->query("SELECT t1.loan_atm_transaction_id, `t1`.`loan_atm_id`, `t1`.`transaction_datetime`,
									t1.loan_amount_balance AS loan_amount_balance
		
									FROM `coop_loan_atm_transaction` as `t1`
									INNER JOIN (SELECT loan_atm_id, MAX(cast(transaction_datetime as Datetime)) as max FROM coop_loan_atm_transaction WHERE transaction_datetime BETWEEN '".$start_date." 00:00:00' AND '".$end_date." 23:59:59' group by loan_atm_id)
											as t2 ON `t1`.`loan_atm_id` = `t2`.`loan_atm_id` AND `t1`.`transaction_datetime` = `t2`.`max`
									LEFT JOIN `coop_loan_atm_detail` AS `t3` ON `t1`.`loan_atm_id` = `t3`.`loan_atm_id`	AND `t1`.`transaction_datetime` = `t3`.`loan_date`
									LEFT JOIN `coop_finance_transaction` AS `t4` ON `t1`.`receipt_id` = `t4`.`receipt_id`	AND `t1`.`loan_atm_id` = `t4`.`loan_atm_id`
									LEFT JOIN coop_receipt AS t6 ON t1.receipt_id = t6.receipt_id
									WHERE {$where_atm}
									GROUP BY `t1`.`loan_atm_id`
									ORDER BY `t1`.`transaction_datetime`, `t1`.`loan_atm_transaction_id` DESC
									")->result_array();		
		$loan_atm_members = array_column($loan_atms, 'loan_atm_id');
		//echo '<pre>'; print_r($loan_atms); echo '</pre>'; exit;	
		$run_index = 0;
		$row = array();
		
		$check_row = "xx";
		$index = 0;

		foreach($result AS $key2=>$value2){

			if($check_row != @$value2['member_id']){				
				$check_row = @$value2['member_id'];
				$sql_shares = "SELECT 
									t1.member_id
									,t1.share_period
									,t1.share_collect_value
									,t1.share_status
									,t1.share_payable_value
									,'1' AS check_share
								FROM coop_mem_share AS t1
								WHERE t1.member_id = '".$value2['member_id']."'
								AND t1.share_date BETWEEN '".$start_date." 00:00:00.000' AND '".$end_date." 23:59:59.000'
								AND t1.share_status <> 0
								ORDER BY t1.share_date DESC ,t1.share_id DESC
								LIMIT 1";
				//echo $sql_shares.'<br>';				
				$shares = $this->db->query($sql_shares)->row_array();
				$share_period = (!empty($shares['share_period']))?@$shares['share_period']: "";		
					
				//echo $check_share .'<br>';	
				if(@$shares['share_status'] == 3){
					$share_collect_value = (!empty($shares['share_payable_value']))?@$shares['share_payable_value']: "";
				}else{
					$share_collect_value = (!empty($shares['share_collect_value']))?@$shares['share_collect_value']: "";
				}
				$check_share = (!empty($shares['check_share']) && !empty($share_collect_value))?@$shares['check_share']: "";
				
				$runno = 1;
			}else{	
				$runno++;
			}

			$row['data'][$value2['member_id']][$runno]['member_id'] = $value2['member_id'];
			$row['data'][$value2['member_id']][$runno]['prename_full'] = $value2['prename_full'];
			$row['data'][$value2['member_id']][$runno]['firstname_th'] = $value2['firstname_th'];
			$row['data'][$value2['member_id']][$runno]['lastname_th'] = $value2['lastname_th'];
			$row['data'][$value2['member_id']][$runno]['mem_group_name_main'] = $value2['mem_group_name_main'];
			$row['data'][$value2['member_id']][$runno]['mem_group_name_sub'] = $value2['mem_group_name_sub'];
			$row['data'][$value2['member_id']][$runno]['mem_group_name_level'] = $value2['name'];
			$row['data'][$value2['member_id']][$runno]['mem_group_id'] = $value2['id'];
			$row['data'][$value2['member_id']][$runno]['department'] = $value2['department'];
			$row['data'][$value2['member_id']][$runno]['faction'] = $value2['faction'];
			$row['data'][$value2['member_id']][$runno]['level'] = $value2['level'];
			$row['data'][$value2['member_id']][$runno]['loan_type'] = $value2['loan_type'];
			if($row_mem_group_level->sub_name == '' || $value2->sub_name=='ไม่ระบุ'){					
				$row['data'][$value2['member_id']][$runno]['mem_group_name_sub'] = $value2['main_name'];
			}else{					
				$row['data'][$value2['member_id']][$runno]['mem_group_name_sub'] = $value2['sub_name'];
			}
				
			$row['data'][$value2['member_id']][$runno]['mem_group_name_main'] = $value2['main_name'];
			
			//หุ้น
			if ($runno == 1) {
				$row['data'][$value2['member_id']][$runno]['share_period'] = $share_period;
				$row['data'][$value2['member_id']][$runno]['share_collect'] = $share_collect_value;
				$row['data'][$value2['member_id']][$runno]['check_share'] = $check_share;
			} else {
				$row['data'][$value2['member_id']][$runno]['share_period'] = "";
				$row['data'][$value2['member_id']][$runno]['share_collect'] = "";
				$row['data'][$value2['member_id']][$runno]['check_share'] = "";
			}
			$row['data'][$value2['member_id']][$runno]['runno'] = $runno;
			
			$loan_type_code = @$arr_loan_type_code[$value2['loan_type']];
			if(@$loan_type_code == 'emergent' && @$value2['loan_amount_balance'] != '' && in_array($value2['loan_id'],$loan_members)
					&& $loans[array_search($value2['loan_id'],$loan_members)]['loan_amount_balance']){
				//เงินกู้ฉุกเฉิน
				if ($runno == 1) {
					$row['data'][$value2['member_id']][$runno]['loan_emergent_period_now'] = @$value2['period_now'];
					$row['data'][$value2['member_id']][$runno]['loan_emergent_contract_number'] = @$value2['contract_number'];
					$row['data'][$value2['member_id']][$runno]['loan_emergent_balance'] = $loans[array_search($value2['loan_id'],$loan_members)]['loan_amount_balance'];
					$row['data'][$value2['member_id']][$runno]['check_emergent'] = 1;
				} else {
					for($no_count = 1; $no_count <= $runno; $no_count++) {
						if (empty($row['data'][$value2['member_id']][$no_count]['loan_emergent_contract_number'])) {
							$row['data'][$value2['member_id']][$no_count]['loan_emergent_period_now'] = @$value2['period_now'];
							$row['data'][$value2['member_id']][$no_count]['loan_emergent_contract_number'] = @$value2['contract_number'];
							$row['data'][$value2['member_id']][$no_count]['loan_emergent_balance'] = $loans[array_search($value2['loan_id'],$loan_members)]['loan_amount_balance'];
							$row['data'][$value2['member_id']][$no_count]['check_emergent'] = 1;
							break;
						} else if ($row['data'][$value2['member_id']][$no_count]['loan_emergent_contract_number'] == $value2['contract_number']) {
							break;
						}
					}
				}
				$run_emergent++;
				if($run_emergent > 1){
					//$runno++;
				}
			}

			if(@$loan_type_code == 'normal' && @$value2['loan_amount_balance'] != '' && in_array($value2['loan_id'],$loan_members)
					&& !empty($loans[array_search($value2['loan_id'],$loan_members)]['loan_amount_balance'])){
				if ($runno == 1) {
					$row['data'][$value2['member_id']][$runno]['loan_normal_period_now'] = @$value2['period_now'];
					$row['data'][$value2['member_id']][$runno]['loan_normal_contract_number'] = @$value2['contract_number'];
					$row['data'][$value2['member_id']][$runno]['loan_normal_balance'] = $loans[array_search($value2['loan_id'],$loan_members)]['loan_amount_balance'];
					$row['data'][$value2['member_id']][$runno]['check_normal'] = 1;
				} else {
					for($no_count = 1; $no_count <= $runno; $no_count++) {
						if ($row['data'][$value2['member_id']][$no_count]['loan_normal_contract_number'] == $value2['contract_number']) {
							break;
						} else if (empty($row['data'][$value2['member_id']][$no_count]['loan_normal_contract_number'])) {
							$row['data'][$value2['member_id']][$no_count]['loan_normal_period_now'] = @$value2['period_now'];
							$row['data'][$value2['member_id']][$no_count]['loan_normal_contract_number'] = @$value2['contract_number'];
							$row['data'][$value2['member_id']][$no_count]['loan_normal_balance'] = $loans[array_search($value2['loan_id'],$loan_members)]['loan_amount_balance'];
							$row['data'][$value2['member_id']][$no_count]['check_normal'] = 1;
							break;
						}
					}
				}
				$run_normal++;
			}
			
			if(@$loan_type_code == 'special' && @$value2['loan_amount_balance'] != '' && in_array($value2['loan_id'],$loan_members)
					&& !empty($loans[array_search($value2['loan_id'],$loan_members)]['loan_amount_balance'])){
				if ($runno == 1) {
					$row['data'][$value2['member_id']][$runno]['loan_special_period_now'] = @$value2['period_now'];
					$row['data'][$value2['member_id']][$runno]['loan_special_contract_number'] = @$value2['contract_number'];
					$row['data'][$value2['member_id']][$runno]['loan_special_balance'] = $loans[array_search($value2['loan_id'],$loan_members)]['loan_amount_balance'];
					$row['data'][$value2['member_id']][$runno]['check_special'] = 1;
				} else {
					for($no_count = 1; $no_count <= $runno; $no_count++) {
						if (empty($row['data'][$value2['member_id']][$no_count]['loan_special_contract_number'])) {
							$row['data'][$value2['member_id']][$no_count]['loan_special_period_now'] = @$value2['period_now'];
							$row['data'][$value2['member_id']][$no_count]['loan_special_contract_number'] = @$value2['contract_number'];
							$row['data'][$value2['member_id']][$no_count]['loan_special_balance'] = $loans[array_search($value2['loan_id'],$loan_members)]['loan_amount_balance'];
							$row['data'][$value2['member_id']][$no_count]['check_special'] = 1;
							break;
						} else if ($row['data'][$value2['member_id']][$no_count]['loan_special_contract_number'] == $value2['contract_number']) {
							break;
						}
					}
				}

				$run_special++;
			}

			if(@$value2['loan_amount_balance_atm'] != '' && in_array($value2['loan_atm_id'],$loan_atm_members)
						&& !empty($loan_atms[array_search($value2['loan_atm_id'],$loan_atm_members)]['loan_amount_balance'])){
				//เงินกู้ฉุกเฉิน ATM
				$atm_index_count = $runno;
				if(!empty($row['data'][$value2['member_id']][$runno]['loan_emergent_contract_number'])) {
					$atm_index_count = $runno+1;
				}
				for($no_count = 1; $no_count <= $atm_index_count; $no_count++) {
					if (empty($row['data'][$value2['member_id']][$no_count]['loan_emergent_contract_number'])) {
						$row['data'][$value2['member_id']][$no_count]['member_id'] = $value2['member_id'];
						$row['data'][$value2['member_id']][$no_count]['prename_full'] = $value2['prename_full'];
						$row['data'][$value2['member_id']][$no_count]['firstname_th'] = $value2['firstname_th'];
						$row['data'][$value2['member_id']][$no_count]['lastname_th'] = $value2['lastname_th'];
						$row['data'][$value2['member_id']][$no_count]['mem_group_name_main'] = $value2['mem_group_name_main'];
						$row['data'][$value2['member_id']][$no_count]['mem_group_name_sub'] = $value2['mem_group_name_sub'];
						$row['data'][$value2['member_id']][$no_count]['mem_group_name_level'] = $value2['name'];
						$row['data'][$value2['member_id']][$no_count]['mem_group_id'] = $value2['id'];
						$row['data'][$value2['member_id']][$no_count]['mem_group_id'] = $value2['id'];
						$row['data'][$value2['member_id']][$runno]['mem_group_name_level'] = $value2['name'];
						if($row_mem_group_level->sub_name == '' || $value2->sub_name=='ไม่ระบุ'){					
							$row['data'][$value2['member_id']][$no_count]['mem_group_name_sub'] = $value2['main_name'];
						}else{					
							$row['data'][$value2['member_id']][$no_count]['mem_group_name_sub'] = $value2['sub_name'];
						}
	
						$row['data'][$value2['member_id']][$no_count]['mem_group_name_main'] = $value2['main_name'];
						
						$row['data'][$value2['member_id']][$runno]['department'] = $value2['department'];
						$row['data'][$value2['member_id']][$runno]['faction'] = $value2['faction'];
						$row['data'][$value2['member_id']][$runno]['level'] = $value2['level'];
						$row['data'][$value2['member_id']][$runno]['loan_type'] = $value2['loan_type'];

						//หุ้น
						if ($runno == 1) {
							$row['data'][$value2['member_id']][$runno]['share_period'] = $share_period;
							$row['data'][$value2['member_id']][$runno]['share_collect'] = $share_collect_value;
							$row['data'][$value2['member_id']][$runno]['check_share'] = $check_share;
						} else {
							$row['data'][$value2['member_id']][$runno]['share_period'] = "";
							$row['data'][$value2['member_id']][$runno]['share_collect'] = "";
							$row['data'][$value2['member_id']][$runno]['check_share'] = "";
						}

						$row['data'][$value2['member_id']][$no_count]['runno'] = $runno;
						$row['data'][$value2['member_id']][$no_count]['loan_emergent_period_now'] = '';
						$row['data'][$value2['member_id']][$no_count]['loan_emergent_contract_number'] = @$value2['contract_number_atm'];
						$row['data'][$value2['member_id']][$no_count]['loan_emergent_balance'] = $loan_atms[array_search($value2['loan_atm_id'],$loan_atm_members)]['loan_amount_balance'];
						$row['data'][$value2['member_id']][$no_count]['check_emergent'] = 1;	
						break;
					} else if ($row['data'][$value2['member_id']][$no_count]['loan_emergent_contract_number'] == $value2['contract_number_atm']) {
						break;
					}
				}	
			}

			$run_index++;		

		}
		
		//Generate Fund support Information
		$where_fund = "1=1";
		$where_fund_t1 = $_GET["type_date"] == 1 ? "payment_date <= '".$end_date." 23:59:59.000'" : "payment_date BETWEEN '".$end_date." 00:00:00.000' AND '".$end_date." 23:59:59.000'";
		$funds = $this->db->select("SUM(t2.principal) as loan_amount_balance, t5.member_id, t5.prename_id, t5.firstname_th, t5.lastname_th, t5.level, t7.id as faction, t8.id as department, t9.prename_full,
									t6.mem_group_id as id, t6.mem_group_name as name, t7.mem_group_name as sub_name, t8.mem_group_name as main_name, t4.id as loan_id, t4.contract_number, t4.loan_type, t4.period_now")
							->from("(SELECT *, MAX(payment_date) as max_date FROM coop_loan_fund_balance_transaction WHERE ".$where_fund_t1." GROUP BY sub_compromise_id) as t1")
							->join("coop_loan_fund_balance_transaction as t2", "t1.sub_compromise_id = t2.sub_compromise_id AND t1.max_date = t2.payment_date", "inner")
							->join("coop_loan_compromise as t3", "t2.compromise_id = t3.id", "inner")
							->join("coop_loan as t4", "t3.loan_id = t4.id", "inner")
							->join("(SELECT IF (
										(SELECT level_old FROM coop_mem_group_move WHERE date_move >= '".$end_date."' AND coop_mem_group_move.member_id = coop_mem_apply.member_id ORDER BY date_move ASC LIMIT 1),
										(SELECT level_old FROM coop_mem_group_move WHERE date_move >= '".$end_date."' AND coop_mem_group_move.member_id = coop_mem_apply.member_id ORDER BY date_move ASC LIMIT 1),
										coop_mem_apply. level
									) AS level, member_id, prename_id, firstname_th, lastname_th,member_status FROM coop_mem_apply) as t5", "t3.member_id = t5.member_id", "inner")
							->join("coop_mem_group as t6", "t5.level = t6.id", "left")
							->join("coop_mem_group as t7", "t7.id = t6.mem_group_parent_id", "left")
							->join("coop_mem_group as t8", "t8.id = t7.mem_group_parent_id", "left")
							->join("coop_prename as t9", "t5.prename_id = t9.prename_id", "left")
							->where($where_fund)
							->group_by("t2.compromise_id")
							->get()->result_array();

		//echo '<pre>'; print_r($row); echo '</pre>'; exit;

		foreach($funds as $fund) {
			if($fund["loan_amount_balance"] > 0) {
				$data_arr = array();
				$data_arr["member_id"] = $fund["member_id"];
				$data_arr["prename_id"] = $fund["prename_id"];
				$data_arr["firstname_th"] = $fund["firstname_th"];
				$data_arr["lastname_th"] = $fund["lastname_th"];
				$data_arr["department"] = $fund["department"];
				$data_arr["faction"] = $fund["faction"];
				$data_arr["level"] = $fund["level"];
				$data_arr["prename_full"] = $fund["prename_full"];
				$data_arr["id"] = $fund["id"];
				$data_arr["name"] = $fund["name"];
				$data_arr["sub_name"] = $fund["sub_name"];
				$data_arr["main_name"] = $fund["main_name"];
				$data_arr["loan_id"] = $fund["loan_id"];
				$data_arr['loan_amount_balance'] = $fund["loan_amount_balance"];
				$data_arr["contract_number"] = $fund["contract_number"];
				$data_arr["loan_type"] = $fund["loan_type"];
				$data_arr["period_now"] = $fund["period_now"];
				$data_arr['mem_group_id'] = $fund["id"];
				$data_arr['mem_group_name_level'] = $fund["level"];
				$data_arr["mem_group_name_sub"] = $fund["faction"];
				$data_arr["mem_group_name_main"] = $fund["department"];
				$data_arr["loan_normal_period_now"] = $fund["period_now"];
				$data_arr["loan_normal_contract_number"] = $fund["contract_number"];
				$data_arr["loan_normal_balance"] = $fund["loan_amount_balance"];
				$data_arr["check_normal"] = 1;
				$row['data'][$fund["member_id"]][] = $data_arr;
			}
		}

		$data_tmp = $row['data'];	
		
		$data_tmp_sum = array();
		foreach($data_tmp AS $key=>$value){
			
			foreach($value AS $key2=>$value2){
				if(@$_GET['type_department'] == '1'){
					$data_tmp_sum[@$value2['department']]['share_person'] += !empty($value2['check_share']) ? $value2['check_share'] : 0;
					$data_tmp_sum[@$value2['department']]['share_collect'] += !empty($value2['share_collect']) ? $value2['share_collect'] : 0;
					
					$data_tmp_sum[@$value2['department']]['loan_emergent_person'] += !empty($value2['loan_emergent_person']) ? $value2['loan_emergent_person'] : 0;
					$data_tmp_sum[@$value2['department']]['loan_emergent_balance'] += !empty($value2['loan_emergent_balance']) ? $value2['loan_emergent_balance'] : 0;
					
					$data_tmp_sum[@$value2['department']]['loan_normal_person'] += !empty($value2['loan_normal_person']) ? $value2['loan_normal_person'] : 0;
					$data_tmp_sum[@$value2['department']]['loan_normal_balance'] += !empty($value2['loan_normal_balance']) ? $value2['loan_normal_balance'] : 0;
					
					$data_tmp_sum[@$value2['department']]['loan_special_person'] += !empty($value2['loan_special_person']) ? $value2['loan_special_person'] : 0;
					$data_tmp_sum[@$value2['department']]['loan_special_balance'] += !empty($value2['loan_special_balance']) ? $value2['loan_special_balance'] : 0;
				}else if(@$_GET['type_department'] == '2'){				
					$data_tmp_sum[@$value2['faction']]['share_person'] += !empty($value2['check_share']) ? $value2['check_share'] : 0;
					$data_tmp_sum[@$value2['faction']]['share_collect'] += !empty($value2['share_collect']) ? $value2['share_collect'] : 0;
					
					$data_tmp_sum[@$value2['faction']]['loan_emergent_person'] += !empty($value2['check_emergent']) ? $value2['check_emergent'] : 0;
					$data_tmp_sum[@$value2['faction']]['loan_emergent_balance'] += !empty($value2['loan_emergent_balance']) ? $value2['loan_emergent_balance'] : 0;

					$data_tmp_sum[@$value2['faction']]['loan_normal_person'] += !empty($value2['check_normal']) ? $value2['check_normal'] : 0;
					$data_tmp_sum[@$value2['faction']]['loan_normal_balance'] += !empty($value2['loan_normal_balance']) ? $value2['loan_normal_balance'] : 0;
					
					$data_tmp_sum[@$value2['faction']]['loan_special_person'] += !empty($value2['check_special']) ? $value2['check_special'] : 0;
					$data_tmp_sum[@$value2['faction']]['loan_special_balance'] += !empty($value2['loan_special_balance']) ? $value2['loan_special_balance'] : 0;
					
					$data_tmp_sum[@$value2['faction']]['share_balance_subdivision'] += !empty($value2['share_collect']) ? $value2['share_collect'] : 0;
					$data_tmp_sum[@$value2['faction']]['loan_balance_subdivision'] += !empty($value2['loan_emergent_balance']) ? @$value2['loan_emergent_balance']+@$value2['loan_normal_balance']+@$value2['loan_special_balance'] : 0;
				}					
				
			}	
		}
		//echo '<pre>'; print_r($data_tmp_sum); echo '</pre>';
		//exit;
		
		$arr_data = array();
		$x=0;
		$join_arr = array();
		
		$where = "";
		$field_department = "department";
		if(@$_GET['type_department'] == '1'){
			$where .= " AND coop_mem_group.mem_group_type = '1'";
			
		}else if(@$_GET['type_department'] == '2'){
			$where .= " AND coop_mem_group.mem_group_type = '2'";
			$field_department = "faction";
		}
		
		$this->paginater_all_preview->type(DB_TYPE);
		$this->paginater_all_preview->select(array(
													'coop_mem_group.id',
													'coop_mem_group.mem_group_id',
													'coop_mem_group.mem_group_parent_id',
													'coop_mem_group.mem_group_name'
												));
		$this->paginater_all_preview->main_table('coop_mem_group');
		$this->paginater_all_preview->where("{$where}");
		$this->paginater_all_preview->page_now(@$_GET["page"]);
		$this->paginater_all_preview->per_page(20);
		$this->paginater_all_preview->page_link_limit(28);
		$this->paginater_all_preview->page_limit_first(22);
		$this->paginater_all_preview->order_by('coop_mem_group.mem_group_id');
		$this->paginater_all_preview->join_arr($join_arr);
		$row = $this->paginater_all_preview->paginater_process();
		
		foreach($row['data'] AS $key=>$value){
			$check_group_id_sub = 'xx';
			foreach($value AS $key2=>$value2){
				//echo '<pre>'; print_r($row); echo '</pre>';
				//หุ้น
				$row['data'][$key][$key2]['share_person'] = @$data_tmp_sum[$value2['id']]['share_person'];
				$row['data'][$key][$key2]['share_collect'] = @$data_tmp_sum[$value2['id']]['share_collect'];
				//เงินกู้ฉุกเฉิน
				$row['data'][$key][$key2]['loan_emergent_person'] = @$data_tmp_sum[$value2['id']]['loan_emergent_person'];
				$row['data'][$key][$key2]['loan_emergent_balance'] = @$data_tmp_sum[$value2['id']]['loan_emergent_balance'];
				
				//เงินกู้สามัญ
				$row['data'][$key][$key2]['loan_normal_person'] = @$data_tmp_sum[$value2['id']]['loan_normal_person'];
				$row['data'][$key][$key2]['loan_normal_balance'] = @$data_tmp_sum[$value2['id']]['loan_normal_balance'];
				
				//เงินกู้พิเศษ
				$row['data'][$key][$key2]['loan_special_person'] = @$data_tmp_sum[$value2['id']]['loan_special_person'];
				$row['data'][$key][$key2]['loan_special_balance'] = @$data_tmp_sum[$value2['id']]['loan_special_balance'];
				
				//รวมเงินกู้คงเหลือ
				$row['data'][$key][$key2]['total_loan_balance'] = @$data_tmp_sum[$value2['id']]['loan_emergent_balance']+@$data_tmp_sum[$value2['id']]['loan_normal_balance']+@$data_tmp_sum[$value2['id']]['loan_special_balance'];
				
				$this->db->select(array('coop_mem_group.id','coop_mem_group.mem_group_id','coop_mem_group.mem_group_parent_id','coop_mem_group.mem_group_name'));
				$this->db->from('coop_mem_group');
				$this->db->where("coop_mem_group.id = '".@$value2['mem_group_parent_id']."'");
				$this->db->limit(1);
				$rs_mem_group_sub = $this->db->get()->result_array();
				$row_mem_group_sub = @$rs_mem_group_sub[0];
				if(@$row_mem_group_sub['mem_group_name'] == '' || @$row_mem_group_sub['mem_group_name']=='ไม่ระบุ'){
					$this->db->select(array('coop_mem_group.id','coop_mem_group.mem_group_id','coop_mem_group.mem_group_parent_id','coop_mem_group.mem_group_name'));
					$this->db->from('coop_mem_group');
					$this->db->where("coop_mem_group.id = '".@$row_mem_group_sub['mem_group_parent_id']."'");
					$this->db->limit(1);
					$rs_mem_group_main = $this->db->get()->result_array();
					
					$row['data'][$key][$key2]['mem_group_name_main'] = @$rs_mem_group_main[0]['mem_group_name'];
					$mem_group_id_sub = @$rs_mem_group_main[0]['id'];
					$row['data'][$key][$key2]['share_balance_subdivision'] = $row['data'][$key][$key2]['share_collect'];
					$row['data'][$key][$key2]['loan_balance_subdivision'] = $row['data'][$key][$key2]['total_loan_balance'];
				}else{	
					$mem_group_id_sub = @$value2['mem_group_parent_id'];
					$row['data'][$key][$key2]['mem_group_name_main'] = @$row_mem_group_sub['mem_group_name'];
					$row['data'][$key][$key2]['share_balance_subdivision'] = @$data_tmp_sum[$mem_group_id_sub]['share_balance_subdivision'];
					$row['data'][$key][$key2]['loan_balance_subdivision'] = @$data_tmp_sum[$mem_group_id_sub]['loan_balance_subdivision'];
				}
				
				if($mem_group_id_sub == $check_group_id_sub){				
					$run_sub++;	
				}else{
					$check_group_id_sub = $mem_group_id_sub;
				}
				
				$arr_data['arr_run_sub'][@$mem_group_id_sub] = @$run_sub;

				$row['data'][$key][$key2]['run_sub'] = @$run_sub;
				$row['data'][$key][$key2]['mem_group_id_sub'] = @$mem_group_id_sub;
			}	
		}
		//echo '<pre>'; print_r($row['data']); echo '</pre>';
		//exit;
		
		$arr_data['num_rows'] = $row['num_rows'];
		$arr_data['paging'] = $paging;
		$arr_data['data'] = $row['data'];
		$arr_data['page_all'] = $row['page_all'];
		
		$this->db->select(array('id','loan_type','loan_type_code'));
		$this->db->from('coop_loan_type');
		$this->db->order_by("order_by");
		$row = $this->db->get()->result_array();
		$arr_data['loan_type'] = $row;
		
		$arr_data['month_arr'] = $this->center_function->month_arr();
		$arr_data['month_short_arr'] = $this->center_function->month_short_arr();
		//echo"<pre>";print_r($arr_data);exit;
		if(@$_GET['type_department'] == '1'){
			$this->preview_libraries->template_preview('report_share_data/coop_report_share_loan_balance_preview',$arr_data);
		}else if(@$_GET['type_department'] == '2'){
			$this->preview_libraries->template_preview('report_share_data/coop_report_share_loan_balance_subdivision_preview',$arr_data);
		}
	}

	function coop_report_share_loan_balance_loan_type_preview(){
        set_time_limit(-1);
        ini_set("memory_limit", -1);
        if(@$_GET['start_date']){
            $start_date_arr = explode('/',@$_GET['start_date']);
            $start_day = $start_date_arr[0];
            $start_month = $start_date_arr[1];
            $start_year = $start_date_arr[2];
            $start_year -= 543;
            $get_start_date = $start_year.'-'.$start_month.'-'.$start_day;
        }

        if(@$_GET['end_date']){
            $end_date_arr = explode('/',@$_GET['end_date']);
            $end_day = $end_date_arr[0];
            $end_month = $end_date_arr[1];
            $end_year = $end_date_arr[2];
            $end_year -= 543;
            $get_end_date = $end_year.'-'.$end_month.'-'.$end_day;
        }

        if(@$_GET['type_date'] == '1'){
            $this->db->select(array('share_date'));
            $this->db->from('coop_mem_share');
            $this->db->where("share_status IN ('1', '2')");
            $this->db->order_by("share_date ASC");
            $this->db->limit(1);
            $rs_date_share = $this->db->get()->result_array();
            $date_share_min  =  date("Y-m-d", strtotime(@$rs_date_share[0]['share_date']));


            $this->db->select(array('createdatetime'));
            $this->db->from('coop_loan');
            $this->db->where("loan_status = '1'");
            $this->db->order_by("createdatetime ASC");
            $this->db->limit(1);
            $rs_date_loan = $this->db->get()->result_array();
            $date_loan_min  =  date("Y-m-d", strtotime(@$rs_date_loan[0]['createdatetime']));

            $this->db->select(array('transaction_datetime'));
            $this->db->from('coop_loan_atm_transaction');
            $this->db->order_by("transaction_datetime ASC");
            $this->db->limit(1);
            $rs_date_loan_atm = $this->db->get()->result_array();
            $date_loan_atm_min  =  date("Y-m-d", strtotime(@$rs_date_loan_atm[0]['transaction_datetime']));

            if($date_share_min < $date_loan_min){
                $start_date = $date_share_min;
            }else if($date_loan_min < $date_loan_atm_min){
                $start_date = $date_loan_min;
            }else if($date_loan_atm_min < $date_share_min){
                $start_date = $date_loan_atm_min;
            }else{
                $start_date = $date_share_min;
            }
            $end_date = $get_start_date;
        }else{
            $start_date = $get_start_date;
            $end_date = $get_end_date;
        }


        $where_date = "";
        $where_date_loan = "";
        $where_date_loan_atm = "";
        $where_date_loan_atm_transaction = "";
        $where_date_loan_transaction = "";

        if(@$_GET['start_date'] != ''){
            $where_date .= " AND coop_mem_share.share_date BETWEEN '".$start_date." 00:00:00.000' AND '".$end_date." 23:59:59.000'";
            $where_date_loan .= " AND coop_loan.createdatetime BETWEEN '".$start_date." 00:00:00.000' AND '".$end_date." 23:59:59.000'";
            $where_date_loan_atm .= " AND coop_loan_atm.createdatetime BETWEEN '".$start_date." 00:00:00.000' AND '".$end_date." 23:59:59.000'";
            $where_date_loan_atm_transaction .= " AND coop_loan_atm_transaction.transaction_datetime BETWEEN '".$start_date." 00:00:00.000' AND '".$end_date." 23:59:59.000'";
            $where_date_loan_transaction .= " AND coop_loan_transaction.transaction_datetime BETWEEN '".$start_date." 00:00:00.000' AND '".$end_date." 23:59:59.000'";
        }

        $this->db->select(array('coop_loan_name.loan_name_id','coop_loan_name.loan_name','coop_loan_type.loan_type_code'));
        $this->db->from('coop_loan_name');
        $this->db->join('coop_loan_type','coop_loan_name.loan_type_id = coop_loan_type.id','left');
        $this->db->where(" coop_loan_name.loan_name_id IN (SELECT loan_type FROM coop_loan WHERE loan_status = 1 GROUP BY loan_type) ");
        $this->db->order_by("coop_loan_name.loan_name_id asc");
        $rs_type_code = $this->db->get()->result_array();
        $arr_loan_type_code = array();

        foreach($rs_type_code AS $key_type_code=>$row_type_code){
            $arr_loan_type_code[@$row_type_code['loan_name_id']] = @$row_type_code['loan_type_code'];
        }

        $sql = "SELECT `coop_mem_apply`.`member_id`, `coop_mem_apply`.`prename_id`, `coop_mem_apply`.`firstname_th`, `coop_mem_apply`.`lastname_th`, `coop_mem_apply`.`department`, `coop_mem_apply`.`faction`, `coop_mem_apply`.`level`, `coop_mem_apply`.`sex`
				FROM (SELECT IF (
								(SELECT level_old FROM coop_mem_group_move WHERE date_move >= '".$end_date."' AND coop_mem_group_move.member_id = coop_mem_apply.member_id ORDER BY date_move ASC LIMIT 1),
								(SELECT level_old FROM coop_mem_group_move WHERE date_move >= '".$end_date."' AND coop_mem_group_move.member_id = coop_mem_apply.member_id ORDER BY date_move ASC LIMIT 1),
								coop_mem_apply. level
							) AS level,
							IF (
								(SELECT faction_old FROM coop_mem_group_move WHERE date_move >= '".$end_date."' AND coop_mem_group_move.member_id = coop_mem_apply.member_id ORDER BY date_move ASC LIMIT 1),
								(SELECT faction_old FROM coop_mem_group_move WHERE date_move >= '".$end_date."' AND coop_mem_group_move.member_id = coop_mem_apply.member_id ORDER BY date_move ASC LIMIT 1),
								coop_mem_apply.faction
							) AS faction,
							IF (
								(SELECT department_old FROM coop_mem_group_move WHERE date_move >= '".$end_date."' AND coop_mem_group_move.member_id = coop_mem_apply.member_id ORDER BY date_move ASC LIMIT 1),
								(SELECT department_old FROM coop_mem_group_move WHERE date_move >= '".$end_date."' AND coop_mem_group_move.member_id = coop_mem_apply.member_id ORDER BY date_move ASC LIMIT 1),
								coop_mem_apply.department
							) AS department, member_id, prename_id, firstname_th, lastname_th,member_status, if(sex = 'M', 2, 1) as `sex`  FROM coop_mem_apply) AS coop_mem_apply	  
				WHERE coop_mem_apply.member_status = 1 
				ORDER BY coop_mem_apply.member_id ASC";
        if($_GET['debug'] == "on") {
            echo $sql.'<hr>'; //exit;
        }
        $result = $this->db->query($sql)->result_array();

        $member_ids = array_column($result, 'member_id');

        //Get Lastest Loan Information
        $loan_ids = array_column($result, 'loan_id');
        $where_loan = " 1=1 ";
        if(sizeof(array_filter($member_ids))){
            $where_loan = " t3.loan_status = '1' AND t3.member_id IN  (".implode(',', array_map(function($v){ return sprintf("'%s'", $v); }, array_filter($member_ids))).") ";
        }

        $loans = $this->db->query("SELECT T.*FROM (SELECT `t3`.`member_id`,`t3`.`contract_number`,`t3`.`period_now`,`t3`.`loan_type`,`t1`.`loan_transaction_id`,`t1`.`loan_id`,`t1`.`loan_amount_balance`,`t1`.`transaction_datetime` FROM `coop_loan_transaction` AS `t1` INNER JOIN (SELECT loan_id,MAX(cast(transaction_datetime AS Datetime)) AS max FROM coop_loan_transaction WHERE transaction_datetime BETWEEN '".$start_date." 00:00:00' AND '".$end_date." 23:59:59' GROUP BY loan_id) AS t2 ON `t1`.`loan_id`=`t2`.`loan_id` AND `t1`.`transaction_datetime`=`t2`.`max` INNER JOIN coop_loan AS t3 ON t2.loan_id=t3.id WHERE {$where_loan}  ORDER BY `t1`.`transaction_datetime`,`t1`.`loan_transaction_id` DESC) T  ORDER BY member_id")->result_array();
        if($_GET['debug'] == "print_loans") {
            echo $this->db->last_query(); exit;
        }
        $loan_members = array_column($loans, 'loan_id');
        $lona_const_type = array();
        foreach ($rs_type_code as $index => $value){
            $lona_const_type[$value['loan_type_code']][] = $value;
        }
        if($_GET['debug'] == "loan_type"){
            echo "<pre>"; print_r($lona_const_type); exit;
        }
        $_loans = array();
        $_loan_type_chk = array();
        foreach ($loans as $key => $loan){

            $loan_type_code = $arr_loan_type_code[$loan['loan_type']];
            if(empty($lona_const_type[$loan_type_code])){
                echo  "loan_type_code : ".$loan_type_code ." , loan_type : ".$loan['loan_type']; exit;
            }
            foreach ($lona_const_type[$loan_type_code] as $key => $item){
                if($item['loan_name_id'] == $loan["loan_type"]){
                    $_loans[$loan['member_id']][$key][$arr_loan_type_code[$loan['loan_type']]] = $loan;
                }
            }
        }
        $loans = $_loans;
        unset($_loans);
        if($_GET['debug'] == "print_loans") {

            echo "<pre>"; print_r($lona_const_type);
            echo "<pre>"; print_r($loans); exit;
        }
        $run_index = 0;
        $row = array();
        $check_row = "xx";
        $index = 0;
        $sql_max_loan_type = "SELECT max(amount) as `max_type` FROM (SELECT coop_loan_type.loan_type_code, COUNT(coop_loan_type.loan_type_code) as `amount` FROM coop_loan_name LEFT JOIN coop_loan_type ON coop_loan_name.loan_type_id=coop_loan_type.id GROUP BY coop_loan_type.loan_type_code) T ";
        $max_type = $this->db->query($sql_max_loan_type)->row_array()['max_type'];

        $where_share = " 1=1 ";
        if(sizeof(array_filter($member_ids))){
            $where_share = " member_id IN  (".implode(',', array_map(function($v){ return sprintf("'%s'", $v); }, array_filter($member_ids))).") ";
        }
        $sql_shares = "SELECT t1.member_id,t1.share_period,t1.share_collect_value,t1.share_status,t1.share_payable_value,'1' AS check_share,t1.share_date FROM coop_mem_share AS t1 INNER JOIN (
SELECT inner_a.member_id,max(inner_a.share_id) AS share_id FROM coop_mem_share AS inner_a INNER JOIN (
SELECT member_id,max(share_date) AS share_date FROM coop_mem_share WHERE {$where_share} AND share_date BETWEEN '" . $start_date . " 00:00:00.000' AND '" . $end_date . " 23:59:59.000' GROUP BY member_id) inner_b ON inner_a.member_id=inner_b.member_id AND inner_a.share_date=inner_b.share_date GROUP BY member_id) t2 ON t1.member_id=t2.member_id AND t1.share_id=t2.share_id WHERE t1.share_status<> 0 ORDER BY t1.member_id ASC";

        $shares = $this->db->query($sql_shares)->result_array();
        $_shares = array();
        foreach ($shares as $key => $share){

            $_shares[$share['member_id']] = $share;
        }
        $shares = $_shares;
        unset($_shares);

        //Generate Fund support Information
        $where_fund = "1=1";
        $where_fund_t1 = $_GET["type_date"] == 1 ? "payment_date <= '".$end_date." 23:59:59.000'" : "payment_date BETWEEN '".$end_date." 00:00:00.000' AND '".$end_date." 23:59:59.000'";
        $funds = $this->db->select("SUM(t2.principal) as loan_amount_balance, t5.member_id, t5.prename_id, t5.firstname_th, t5.lastname_th, t5.level, t5.sex, t7.id as faction, t8.id as department, t9.prename_full,
									t6.mem_group_id as id, t6.mem_group_name as name, t7.mem_group_name as sub_name, t8.mem_group_name as main_name, t4.id as loan_id, t4.contract_number, t4.loan_type, t4.period_now")
            ->from("(SELECT *, MAX(payment_date) as max_date FROM coop_loan_fund_balance_transaction WHERE ".$where_fund_t1." GROUP BY sub_compromise_id) as t1")
            ->join("coop_loan_fund_balance_transaction as t2", "t1.sub_compromise_id = t2.sub_compromise_id AND t1.max_date = t2.payment_date", "inner")
            ->join("coop_loan_compromise as t3", "t2.compromise_id = t3.id", "inner")
            ->join("coop_loan as t4", "t3.loan_id = t4.id", "inner")
            ->join("(SELECT IF (
										(SELECT level_old FROM coop_mem_group_move WHERE date_move >= '".$end_date."' AND coop_mem_group_move.member_id = coop_mem_apply.member_id ORDER BY date_move ASC LIMIT 1),
										(SELECT level_old FROM coop_mem_group_move WHERE date_move >= '".$end_date."' AND coop_mem_group_move.member_id = coop_mem_apply.member_id ORDER BY date_move ASC LIMIT 1),
										coop_mem_apply. level
									) AS level, member_id, prename_id, firstname_th, lastname_th,member_status, IF(sex = 'M', 2, 1) as sex  FROM coop_mem_apply) as t5", "t3.member_id = t5.member_id", "inner")
            ->join("coop_mem_group as t6", "t5.level = t6.id", "left")
            ->join("coop_mem_group as t7", "t7.id = t6.mem_group_parent_id", "left")
            ->join("coop_mem_group as t8", "t8.id = t7.mem_group_parent_id", "left")
            ->join("coop_prename as t9", "t5.prename_id = t9.prename_id", "left")
            ->where($where_fund)
            ->group_by("t2.compromise_id")
            ->get()->result_array();

        //echo '<pre>'; print_r($row); echo '</pre>'; exit;

        foreach($funds as $fund) {
            if($fund["loan_amount_balance"] > 0) {
                $data_arr = array();
                $data_arr["member_id"] = $fund["member_id"];
                $data_arr["prename_id"] = $fund["prename_id"];
                $data_arr["firstname_th"] = $fund["firstname_th"];
                $data_arr["lastname_th"] = $fund["lastname_th"];
                $data_arr["department"] = $fund["department"];
                $data_arr["faction"] = $fund["faction"];
                $data_arr["level"] = $fund["level"];
                $data_arr["prename_full"] = $fund["prename_full"];
                $data_arr["id"] = $fund["id"];
                $data_arr["name"] = $fund["name"];
                $data_arr["sub_name"] = $fund["sub_name"];
                $data_arr["main_name"] = $fund["main_name"];
                $data_arr["loan_id"] = $fund["loan_id"];
                $data_arr['loan_amount_balance'] = $fund["loan_amount_balance"];
                $data_arr["contract_number"] = $fund["contract_number"];
                $data_arr["loan_type"] = $fund["loan_type"];
                $data_arr["period_now"] = $fund["period_now"];
                $data_arr['mem_group_id'] = $fund["id"];
                $data_arr['mem_group_name_level'] = $fund["level"];
                $data_arr["mem_group_name_sub"] = $fund["faction"];
                $data_arr["mem_group_name_main"] = $fund["department"];
                $data_arr["loan_normal_period_now"] = $fund["period_now"];
                $data_arr["loan_normal_contract_number"] = $fund["contract_number"];
                $data_arr["loan_normal_balance"] = $fund["loan_amount_balance"];
                $data_arr["check_normal"] = 1;
                $row['data'][$fund["member_id"]][] = $data_arr;
            }
        }

        $arr_data = array();
        $x=0;
        $join_arr = array();

        $where .= " AND coop_mem_group.mem_group_type = '2'";
        $field_department = "faction";

        $this->paginater_all_preview->type(DB_TYPE);
        $this->paginater_all_preview->select(array(
            'coop_mem_group.id',
            'coop_mem_group.mem_group_id',
            'coop_mem_group.mem_group_parent_id',
            'coop_mem_group.mem_group_name'
        ));
        $this->paginater_all_preview->main_table('coop_mem_group');
        $this->paginater_all_preview->where("{$where}");
        $this->paginater_all_preview->page_now(@$_GET["page"]);
        $this->paginater_all_preview->per_page(20);
        $this->paginater_all_preview->page_link_limit(24);
        $this->paginater_all_preview->page_limit_first(16);
        $this->paginater_all_preview->order_by('coop_mem_group.mem_group_id');
        $this->paginater_all_preview->join_arr($join_arr);
        $row = $this->paginater_all_preview->paginater_process();

        //echo "<pre>"; print_r($row); exit;
        $_tmp_check_member = array();
        $_tmp_check_loan = array();
        $_mng_rows_pointer = array();
        $rows = 0;

        //echo "<pre>"; var_dump($result); echo "</pre>"; exit;

        $_data_dumper = [];
        $num_page = 1;
		$count_rows = 0;
        foreach ($row['data'] as $key => $data) {

            foreach ($data as $index => $val) {
                $num_page = $key;
                foreach ($result as $num => $member) {

                    if ($member['faction'] == $val['id']) {

                        $comn += 1;

                        if (!empty($loans[$member['member_id']])) {

                            foreach ($loans[$member['member_id']] as $ii => $loan) {

                                if($ii == 0) {
                                    //mem ground label
                                    $_data_dumper[$num_page][$index][$ii]['id'] = $val['id'];
                                    $_data_dumper[$num_page][$index][$ii]['mem_group_id'] = $val['mem_group_id'];
                                    $_data_dumper[$num_page][$index][$ii]['mem_group_parent_id'] = $val['mem_group_parent_id'];
                                    $_data_dumper[$num_page][$index][$ii]['mem_group_name'] = $val['mem_group_name'];


                                    if (!in_array($member['member_id'], $_tmp_check_member)) {
                                        $nnonoo +=1;
                                        //หุ้น
                                        $_data_dumper[$num_page][$index][$ii]['share_person'] = (int)$_data_dumper[$num_page][$index][$ii]['share_person'] + 1;
                                        $_data_dumper[$num_page][$index][$ii]['mem_all_amt'] += 1;
                                        $_data_dumper[$num_page][$index][$ii]['mem_has_debt'] += 1;
                                        $_data_dumper[$num_page][$index][$ii]['mem_men'] += $member['sex'] == 2 ? 1 : 0;
                                        $_data_dumper[$num_page][$index][$ii]['mem_wemen'] += $member['sex'] == 1 ? 1 : 0;
                                        $_tmp_check_member[] = $member['member_id'];

                                    }

                                    if (@$shares[$member['member_id']]['share_status'] == '3') {
                                        $share_collect_value = (!empty($shares[$member['member_id']]['share_payable_value'])) ? @$shares[$member['member_id']]['share_payable_value'] : 0;
                                    } else {
                                        $share_collect_value = !empty($shares[$member['member_id']]['share_collect_value']) ? $shares[$member['member_id']]['share_collect_value'] : 0;
                                    }
                                    $_data_dumper[$num_page][$index][$ii]['share_collect'] = (int)$_data_dumper[$num_page][$index][$ii]['share_collect'] + $share_collect_value;
                                    //echo "<pre>"; print_r($loans[$member['member_id']]);
                                }

                                foreach($loan as $name => $item_loan) {
                                    $_data_dumper[$num_page][$index][$ii]['loan_' . $name . '_person'] = (int)$_data_dumper[$num_page][$index][$ii]['loan_' . $name . '_person'] + 1;
                                    $_data_dumper[$num_page][$index][$ii]['loan_' . $name . '_balance'] = (int)$_data_dumper[$num_page][$index][$ii]['loan_' . $name . '_balance'] + $item_loan['loan_amount_balance'];

                                    $_data_dumper[$num_page][$index][$ii]['total_loan_balance'] = (int)$_data_dumper[$num_page][$index][$ii]['total_loan_balance'] + $item_loan['loan_amount_balance'];
                                }
                            }
                        }else{

                            $_data_dumper[$num_page][$index][0]['id'] = $val['id'];
                            $_data_dumper[$num_page][$index][0]['mem_group_id'] = $val['mem_group_id'];
                            $_data_dumper[$num_page][$index][0]['mem_group_parent_id'] = $val['mem_group_parent_id'];
                            $_data_dumper[$num_page][$index][0]['mem_group_name'] = $val['mem_group_name'];

                            if (!in_array($member['member_id'], $_tmp_check_member)) {
                                $los += 1;
                                //หุ้น
                                $_data_dumper[$num_page][$index][0]['share_person'] = (int)$_data_dumper[$num_page][$index][0]['share_person'] + 1;
                                $_tmp_check_member[] = $member['member_id'];
                                $_data_dumper[$num_page][$index][0]['mem_all_amt'] += 1;
                                $_data_dumper[$num_page][$index][0]['mem_men'] += $member['sex'] == 2 ? 1 : 0;
                                $_data_dumper[$num_page][$index][0]['mem_wemen'] += $member['sex'] == 1 ? 1 : 0;
                            }
                            if (@$shares[$member['member_id']]['share_status'] == '3') {
                                $share_collect_value = (!empty($shares[$member['member_id']]['share_payable_value'])) ? @$shares[$member['member_id']]['share_payable_value'] : 0;
                            } else {
                                $share_collect_value = !empty($shares[$member['member_id']]['share_collect_value']) ? $shares[$member['member_id']]['share_collect_value'] : 0;
                            }
                            $_data_dumper[$num_page][$index][0]['share_collect'] = (int) $_data_dumper[$num_page][$index][0]['share_collect'] + $share_collect_value;
                        }
                    }
                }
            }
        }

        $temp = array();
        $new_page = 0;
        $count_rows = 0;
        foreach ($_data_dumper as $page => $group_data){
        	foreach ($group_data as $group_number => $data){
        		foreach ($data as $key => $row_data){
					$temp[$new_page][$group_number][$key] = $row_data;
					$count_rows++;
					if($count_rows == 25 && $new_page == 0){
						$count_rows = 0;
						$new_page++;
					}
					if($count_rows == 32 && $new_page != 0){
						$count_rows = 0;
						$new_page++;
					}
				}

			}
		}

        $row['data'] = $temp;
        if($_GET['debug'] == "print_data") {

            $summary_key =  array(
                'loan_emergent_balance',
                'loan_normal_balance',
                'loan_special_balance',
                'loan_covid_balance'
            );

            $_summary= self::sum_total_array($row['data'],$summary_key);
            echo "<pre>"; print_r($_summary);
            exit;
        }

        $summary_key =  array(
            'loan_emergent_balance',
            'loan_normal_balance',
            'loan_special_balance',
			'loan_covid_balance'
        );

        $arr_data['summary'] = self::sum_total_array($row['data'],$summary_key);

        $arr_data['num_rows'] = $row['num_rows'];
        $arr_data['paging'] = $paging;
        $arr_data['data'] = $row['data'];
        $arr_data['page_all'] = $new_page;

        $this->db->select(array('id','loan_type','loan_type_code'));
        $this->db->from('coop_loan_type');
        $this->db->order_by("order_by");
        $row = $this->db->get()->result_array();
        $arr_data['loan_type'] = $row;

        $arr_data['month_arr'] = $this->center_function->month_arr();
        $arr_data['month_short_arr'] = $this->center_function->month_short_arr();


        foreach ($arr_data['data'] as $key => $value){
            foreach ($value as $key2 => $value2) {
                ksort($arr_data['data'][$key][$key2]); // เรียงลำดับข้อมูลใหม่
            }
        }
        $this->preview_libraries->template_preview('report_share_data/coop_report_share_loan_balance_subdivision_custom_preview.php',$arr_data);

    }

    function coop_report_share_loan_balance_loan_type_excel(){
        set_time_limit(-1);
        if(@$_GET['start_date']){
            $start_date_arr = explode('/',@$_GET['start_date']);
            $start_day = $start_date_arr[0];
            $start_month = $start_date_arr[1];
            $start_year = $start_date_arr[2];
            $start_year -= 543;
            $get_start_date = $start_year.'-'.$start_month.'-'.$start_day;
        }

        if(@$_GET['type_date'] == '1'){
            $this->db->select(array('share_date'));
            $this->db->from('coop_mem_share');
            $this->db->where("share_status IN ('1', '2')");
            $this->db->order_by("share_date ASC");
            $this->db->limit(1);
            $rs_date_share = $this->db->get()->result_array();
            $date_share_min  =  date("Y-m-d", strtotime(@$rs_date_share[0]['share_date']));


            $this->db->select(array('createdatetime'));
            $this->db->from('coop_loan');
            $this->db->where("loan_status = '1'");
            $this->db->order_by("createdatetime ASC");
            $this->db->limit(1);
            $rs_date_loan = $this->db->get()->result_array();
            $date_loan_min  =  date("Y-m-d", strtotime(@$rs_date_loan[0]['createdatetime']));

            $this->db->select(array('transaction_datetime'));
            $this->db->from('coop_loan_atm_transaction');
            $this->db->order_by("transaction_datetime ASC");
            $this->db->limit(1);
            $rs_date_loan_atm = $this->db->get()->result_array();
            $date_loan_atm_min  =  date("Y-m-d", strtotime(@$rs_date_loan_atm[0]['transaction_datetime']));

            if($date_share_min < $date_loan_min){
                $start_date = $date_share_min;
            }else if($date_loan_min < $date_loan_atm_min){
                $start_date = $date_loan_min;
            }else if($date_loan_atm_min < $date_share_min){
                $start_date = $date_loan_atm_min;
            }else{
                $start_date = $date_share_min;
            }
            $end_date = $get_start_date;
        }else{
            $start_date = $get_start_date;
            $end_date = $get_start_date;
        }


        $where_date = "";
        $where_date_loan = "";
        $where_date_loan_atm = "";
        $where_date_loan_atm_transaction = "";
        $where_date_loan_transaction = "";

        if(@$_GET['start_date'] != ''){
            $where_date .= " AND coop_mem_share.share_date BETWEEN '".$start_date." 00:00:00.000' AND '".$end_date." 23:59:59.000'";
            $where_date_loan .= " AND coop_loan.createdatetime BETWEEN '".$start_date." 00:00:00.000' AND '".$end_date." 23:59:59.000'";
            $where_date_loan_atm .= " AND coop_loan_atm.createdatetime BETWEEN '".$start_date." 00:00:00.000' AND '".$end_date." 23:59:59.000'";
            $where_date_loan_atm_transaction .= " AND coop_loan_atm_transaction.transaction_datetime BETWEEN '".$start_date." 00:00:00.000' AND '".$end_date." 23:59:59.000'";
            $where_date_loan_transaction .= " AND coop_loan_transaction.transaction_datetime BETWEEN '".$start_date." 00:00:00.000' AND '".$end_date." 23:59:59.000'";
        }

        $this->db->select(array('coop_loan_name.loan_name_id','coop_loan_name.loan_name','coop_loan_type.loan_type_code'));
        $this->db->from('coop_loan_name');
        $this->db->join('coop_loan_type','coop_loan_name.loan_type_id = coop_loan_type.id','left');
        $this->db->where(" coop_loan_name.loan_name_id IN (SELECT loan_type FROM coop_loan WHERE loan_status = 1 GROUP BY loan_type) ");
        $this->db->order_by("coop_loan_name.loan_name_id asc");
        $rs_type_code = $this->db->get()->result_array();
        $arr_loan_type_code = array();

        foreach($rs_type_code AS $key_type_code=>$row_type_code){
            $arr_loan_type_code[@$row_type_code['loan_name_id']] = @$row_type_code['loan_type_code'];
        }

        $sql = "SELECT `coop_mem_apply`.`member_id`, `coop_mem_apply`.`prename_id`, `coop_mem_apply`.`firstname_th`, `coop_mem_apply`.`lastname_th`, `coop_mem_apply`.`department`, `coop_mem_apply`.`faction`, `coop_mem_apply`.`level`, `coop_mem_apply`.`sex`
				FROM (SELECT IF (
								(SELECT level_old FROM coop_mem_group_move WHERE date_move >= '".$end_date."' AND coop_mem_group_move.member_id = coop_mem_apply.member_id ORDER BY date_move ASC LIMIT 1),
								(SELECT level_old FROM coop_mem_group_move WHERE date_move >= '".$end_date."' AND coop_mem_group_move.member_id = coop_mem_apply.member_id ORDER BY date_move ASC LIMIT 1),
								coop_mem_apply. level
							) AS level,
							IF (
								(SELECT faction_old FROM coop_mem_group_move WHERE date_move >= '".$end_date."' AND coop_mem_group_move.member_id = coop_mem_apply.member_id ORDER BY date_move ASC LIMIT 1),
								(SELECT faction_old FROM coop_mem_group_move WHERE date_move >= '".$end_date."' AND coop_mem_group_move.member_id = coop_mem_apply.member_id ORDER BY date_move ASC LIMIT 1),
								coop_mem_apply.faction
							) AS faction,
							IF (
								(SELECT department_old FROM coop_mem_group_move WHERE date_move >= '".$end_date."' AND coop_mem_group_move.member_id = coop_mem_apply.member_id ORDER BY date_move ASC LIMIT 1),
								(SELECT department_old FROM coop_mem_group_move WHERE date_move >= '".$end_date."' AND coop_mem_group_move.member_id = coop_mem_apply.member_id ORDER BY date_move ASC LIMIT 1),
								coop_mem_apply.department
							) AS department, member_id, prename_id, firstname_th, lastname_th,member_status, if(sex = 'M', 2, 1) as `sex`  FROM coop_mem_apply) AS coop_mem_apply	  
				WHERE coop_mem_apply.member_status = 1 
				ORDER BY coop_mem_apply.member_id ASC";
        if($_GET['debug'] == "on") {
            echo $sql.'<hr>'; //exit;
        }
        $result = $this->db->query($sql)->result_array();

        $member_ids = array_column($result, 'member_id');

        //Get Lastest Loan Information
        $loan_ids = array_column($result, 'loan_id');
        $where_loan = " 1=1 ";
        if(sizeof(array_filter($member_ids))){
            $where_loan = " t3.loan_status = '1' AND t3.member_id IN  (".implode(',', array_map(function($v){ return sprintf("'%s'", $v); }, array_filter($member_ids))).") ";
        }

        $loans = $this->db->query("SELECT T.*FROM (SELECT `t3`.`member_id`,`t3`.`contract_number`,`t3`.`period_now`,`t3`.`loan_type`,`t1`.`loan_transaction_id`,`t1`.`loan_id`,`t1`.`loan_amount_balance`,`t1`.`transaction_datetime` FROM `coop_loan_transaction` AS `t1` INNER JOIN (SELECT loan_id,MAX(cast(transaction_datetime AS Datetime)) AS max FROM coop_loan_transaction WHERE transaction_datetime BETWEEN '".$start_date." 00:00:00' AND '".$end_date." 23:59:59' GROUP BY loan_id) AS t2 ON `t1`.`loan_id`=`t2`.`loan_id` AND `t1`.`transaction_datetime`=`t2`.`max` INNER JOIN coop_loan AS t3 ON t2.loan_id=t3.id WHERE {$where_loan} ORDER BY `t1`.`transaction_datetime`,`t1`.`loan_transaction_id` DESC) T ORDER BY member_id")->result_array();
//        if($_GET['debug'] == "print_loans") {
//            echo $this->db->last_query(); exit;
//        }
        $loan_members = array_column($loans, 'loan_id');
        $lona_const_type = array();
        foreach ($rs_type_code as $index => $value){
            $lona_const_type[$value['loan_type_code']][] = $value;
        }
        if($_GET['debug'] == "loan_type"){
            echo "<pre>"; print_r($lona_const_type); exit;
        }
        $_loans = array();
        $_loan_type_chk = array();
        foreach ($loans as $key => $loan){

            $loan_type_code = $arr_loan_type_code[$loan['loan_type']];
            foreach ($lona_const_type[$loan_type_code] as $key => $item){
                if($item['loan_name_id'] == $loan["loan_type"]){
                    $_loans[$loan['member_id']][$key][$arr_loan_type_code[$loan['loan_type']]] = $loan;
                }
            }
        }
        $loans = $_loans;
        unset($_loans);
        if($_GET['debug'] == "print_loans") {

            echo "<pre>"; print_r($lona_const_type);
            echo "<pre>"; print_r($loans); exit;
        }
        $run_index = 0;
        $row = array();
        $check_row = "xx";
        $index = 0;
        $sql_max_loan_type = "SELECT max(amount) as `max_type` FROM (SELECT coop_loan_type.loan_type_code, COUNT(coop_loan_type.loan_type_code) as `amount` FROM coop_loan_name LEFT JOIN coop_loan_type ON coop_loan_name.loan_type_id=coop_loan_type.id GROUP BY coop_loan_type.loan_type_code) T ";
        $max_type = $this->db->query($sql_max_loan_type)->row_array()['max_type'];

        $where_share = " 1=1 ";
        if(sizeof(array_filter($member_ids))){
            $where_share = " member_id IN  (".implode(',', array_map(function($v){ return sprintf("'%s'", $v); }, array_filter($member_ids))).") ";
        }
        $sql_shares = "SELECT t1.member_id,t1.share_period,t1.share_collect_value,t1.share_status,t1.share_payable_value,'1' AS check_share,t1.share_date FROM coop_mem_share AS t1 INNER JOIN (
SELECT inner_a.member_id,max(inner_a.share_id) AS share_id FROM coop_mem_share AS inner_a INNER JOIN (
SELECT member_id,max(share_date) AS share_date FROM coop_mem_share WHERE {$where_share} AND share_date BETWEEN '" . $start_date . " 00:00:00.000' AND '" . $end_date . " 23:59:59.000' GROUP BY member_id) inner_b ON inner_a.member_id=inner_b.member_id AND inner_a.share_date=inner_b.share_date GROUP BY member_id) t2 ON t1.member_id=t2.member_id AND t1.share_id=t2.share_id WHERE t1.share_status<> 0 ORDER BY t1.member_id ASC";

        $shares = $this->db->query($sql_shares)->result_array();
        $_shares = array();
        foreach ($shares as $key => $share){

            $_shares[$share['member_id']] = $share;
        }
        $shares = $_shares;
        unset($_shares);

        //Generate Fund support Information
        $where_fund = "1=1";
        $where_fund_t1 = $_GET["type_date"] == 1 ? "payment_date <= '".$end_date." 23:59:59.000'" : "payment_date BETWEEN '".$end_date." 00:00:00.000' AND '".$end_date." 23:59:59.000'";
        $funds = $this->db->select("SUM(t2.principal) as loan_amount_balance, t5.member_id, t5.prename_id, t5.firstname_th, t5.lastname_th, t5.level, t5.sex, t7.id as faction, t8.id as department, t9.prename_full,
									t6.mem_group_id as id, t6.mem_group_name as name, t7.mem_group_name as sub_name, t8.mem_group_name as main_name, t4.id as loan_id, t4.contract_number, t4.loan_type, t4.period_now")
            ->from("(SELECT *, MAX(payment_date) as max_date FROM coop_loan_fund_balance_transaction WHERE ".$where_fund_t1." GROUP BY sub_compromise_id) as t1")
            ->join("coop_loan_fund_balance_transaction as t2", "t1.sub_compromise_id = t2.sub_compromise_id AND t1.max_date = t2.payment_date", "inner")
            ->join("coop_loan_compromise as t3", "t2.compromise_id = t3.id", "inner")
            ->join("coop_loan as t4", "t3.loan_id = t4.id", "inner")
            ->join("(SELECT IF (
										(SELECT level_old FROM coop_mem_group_move WHERE date_move >= '".$end_date."' AND coop_mem_group_move.member_id = coop_mem_apply.member_id ORDER BY date_move ASC LIMIT 1),
										(SELECT level_old FROM coop_mem_group_move WHERE date_move >= '".$end_date."' AND coop_mem_group_move.member_id = coop_mem_apply.member_id ORDER BY date_move ASC LIMIT 1),
										coop_mem_apply. level
									) AS level, member_id, prename_id, firstname_th, lastname_th,member_status, IF(sex = 'M', 2, 1) as sex  FROM coop_mem_apply) as t5", "t3.member_id = t5.member_id", "inner")
            ->join("coop_mem_group as t6", "t5.level = t6.id", "left")
            ->join("coop_mem_group as t7", "t7.id = t6.mem_group_parent_id", "left")
            ->join("coop_mem_group as t8", "t8.id = t7.mem_group_parent_id", "left")
            ->join("coop_prename as t9", "t5.prename_id = t9.prename_id", "left")
            ->where($where_fund)
            ->group_by("t2.compromise_id")
            ->get()->result_array();

        //echo '<pre>'; print_r($row); echo '</pre>'; exit;

        foreach($funds as $fund) {
            if($fund["loan_amount_balance"] > 0) {
                $data_arr = array();
                $data_arr["member_id"] = $fund["member_id"];
                $data_arr["prename_id"] = $fund["prename_id"];
                $data_arr["firstname_th"] = $fund["firstname_th"];
                $data_arr["lastname_th"] = $fund["lastname_th"];
                $data_arr["department"] = $fund["department"];
                $data_arr["faction"] = $fund["faction"];
                $data_arr["level"] = $fund["level"];
                $data_arr["prename_full"] = $fund["prename_full"];
                $data_arr["id"] = $fund["id"];
                $data_arr["name"] = $fund["name"];
                $data_arr["sub_name"] = $fund["sub_name"];
                $data_arr["main_name"] = $fund["main_name"];
                $data_arr["loan_id"] = $fund["loan_id"];
                $data_arr['loan_amount_balance'] = $fund["loan_amount_balance"];
                $data_arr["contract_number"] = $fund["contract_number"];
                $data_arr["loan_type"] = $fund["loan_type"];
                $data_arr["period_now"] = $fund["period_now"];
                $data_arr['mem_group_id'] = $fund["id"];
                $data_arr['mem_group_name_level'] = $fund["level"];
                $data_arr["mem_group_name_sub"] = $fund["faction"];
                $data_arr["mem_group_name_main"] = $fund["department"];
                $data_arr["loan_normal_period_now"] = $fund["period_now"];
                $data_arr["loan_normal_contract_number"] = $fund["contract_number"];
                $data_arr["loan_normal_balance"] = $fund["loan_amount_balance"];
                $data_arr["check_normal"] = 1;
                $row['data'][$fund["member_id"]][] = $data_arr;
            }
        }

        $arr_data = array();
        $x=0;
        $join_arr = array();

        $where .= " AND coop_mem_group.mem_group_type = '2'";
        $field_department = "faction";

        $this->paginater_all_preview->type(DB_TYPE);
        $this->paginater_all_preview->select(array(
            'coop_mem_group.id',
            'coop_mem_group.mem_group_id',
            'coop_mem_group.mem_group_parent_id',
            'coop_mem_group.mem_group_name'
        ));
        $this->paginater_all_preview->main_table('coop_mem_group');
        $this->paginater_all_preview->where("{$where}");
        $this->paginater_all_preview->page_now(@$_GET["page"]);
        $this->paginater_all_preview->per_page(200);
        $this->paginater_all_preview->page_link_limit(200);
        $this->paginater_all_preview->page_limit_first(200);
        $this->paginater_all_preview->order_by('coop_mem_group.mem_group_id');
        $this->paginater_all_preview->join_arr($join_arr);
        $row = $this->paginater_all_preview->paginater_process();

        //echo "<pre>"; print_r($row); exit;
        $_tmp_check_member = array();
        $_tmp_check_loan = array();
        $_mng_rows_pointer = array();
        $rows = 0;

        //echo "<pre>"; var_dump($result); echo "</pre>"; exit;

        $_data_dumper = [];
        foreach ($row['data'] as $key => $data) {

            foreach ($data as $index => $val) {

                foreach ($result as $num => $member) {

                    if ($member['faction'] == $val['id']) {

                        $comn += 1;

                        if (!empty($loans[$member['member_id']])) {

                            foreach ($loans[$member['member_id']] as $ii => $loan) {

                                if($ii == 0) {
                                    //mem ground label
                                    $_data_dumper[$key][$index][$ii]['id'] = $val['id'];
                                    $_data_dumper[$key][$index][$ii]['mem_group_id'] = $val['mem_group_id'];
                                    $_data_dumper[$key][$index][$ii]['mem_group_parent_id'] = $val['mem_group_parent_id'];
                                    $_data_dumper[$key][$index][$ii]['mem_group_name'] = $val['mem_group_name'];


                                    if (!in_array($member['member_id'], $_tmp_check_member)) {
                                        $nnonoo +=1;
                                        //หุ้น
                                        $_data_dumper[$key][$index][$ii]['share_person'] = (int)$_data_dumper[$key][$index][$ii]['share_person'] + 1;
                                        $_data_dumper[$key][$index][$ii]['mem_all_amt'] += 1;
                                        $_data_dumper[$key][$index][$ii]['mem_has_debt'] += 1;
                                        $_data_dumper[$key][$index][$ii]['mem_men'] += $member['sex'] == 2 ? 1 : 0;
                                        $_data_dumper[$key][$index][$ii]['mem_wemen'] += $member['sex'] == 1 ? 1 : 0;
                                        $_tmp_check_member[] = $member['member_id'];

                                    }

                                    if (@$shares[$member['member_id']]['share_status'] == '3') {
                                        $share_collect_value = (!empty($shares[$member['member_id']]['share_payable_value'])) ? @$shares[$member['member_id']]['share_payable_value'] : 0;
                                    } else {
                                        $share_collect_value = !empty($shares[$member['member_id']]['share_collect_value']) ? $shares[$member['member_id']]['share_collect_value'] : 0;
                                    }
                                    $_data_dumper[$key][$index][$ii]['share_collect'] = (int)$_data_dumper[$key][$index][$ii]['share_collect'] + $share_collect_value;
                                    //echo "<pre>"; print_r($loans[$member['member_id']]);
                                }

                                foreach($loan as $name => $item_loan) {
                                    $_data_dumper[$key][$index][$ii]['loan_' . $name . '_person'] = (int)$_data_dumper[$key][$index][$ii]['loan_' . $name . '_person'] + 1;
                                    $_data_dumper[$key][$index][$ii]['loan_' . $name . '_balance'] = (int)$_data_dumper[$key][$index][$ii]['loan_' . $name . '_balance'] + $item_loan['loan_amount_balance'];

                                    $_data_dumper[$key][$index][$ii]['total_loan_balance'] = (int)$_data_dumper[$key][$index][$ii]['total_loan_balance'] + $item_loan['loan_amount_balance'];
                                }
                            }
                        }else{

                            $_data_dumper[$key][$index][0]['id'] = $val['id'];
                            $_data_dumper[$key][$index][0]['mem_group_id'] = $val['mem_group_id'];
                            $_data_dumper[$key][$index][0]['mem_group_parent_id'] = $val['mem_group_parent_id'];
                            $_data_dumper[$key][$index][0]['mem_group_name'] = $val['mem_group_name'];

                            if (!in_array($member['member_id'], $_tmp_check_member)) {
                                $los += 1;
                                //หุ้น
                                $_data_dumper[$key][$index][0]['share_person'] = (int)$_data_dumper[$key][$index][0]['share_person'] + 1;
                                $_tmp_check_member[] = $member['member_id'];
                                $_data_dumper[$key][$index][0]['mem_all_amt'] += 1;
                                $_data_dumper[$key][$index][0]['mem_men'] += $member['sex'] == 2 ? 1 : 0;
                                $_data_dumper[$key][$index][0]['mem_wemen'] += $member['sex'] == 1 ? 1 : 0;
                            }
                            if (@$shares[$member['member_id']]['share_status'] == '3') {
                                $share_collect_value = (!empty($shares[$member['member_id']]['share_payable_value'])) ? @$shares[$member['member_id']]['share_payable_value'] : 0;
                            } else {
                                $share_collect_value = !empty($shares[$member['member_id']]['share_collect_value']) ? $shares[$member['member_id']]['share_collect_value'] : 0;
                            }
                            $_data_dumper[$key][$index][0]['share_collect'] = (int) $_data_dumper[$key][$index][0]['share_collect'] + $share_collect_value;
                        }
                    }
                }
            }
        }

        $row['data'] = $_data_dumper;
        if($_GET['debug'] == "print_data") {
            $summary_key =  array(
                            'loan_emergent_balance',
                            'loan_normal_balance',
                            'loan_special_balance'
                        );

            $_summary = array();
            $_summary = self::sum_total_array($row['data'],$summary_key);
            echo "<pre>"; print_r($_summary);
            exit;
        }
        $summary_key =  array(
            'loan_emergent_balance',
            'loan_normal_balance',
            'loan_special_balance',
			'loan_covid_balance'
        );

        $_summary = array();
        $arr_data['summary'] = self::sum_total_array($row['data'],$summary_key);

        $arr_data['num_rows'] = $row['num_rows'];
        $arr_data['paging'] = $paging;
        $arr_data['data'] = $row['data'];
        $arr_data['page_all'] = $row['page_all'];

        $this->db->select(array('id','loan_type','loan_type_code'));
        $this->db->from('coop_loan_type');
        $this->db->order_by("order_by");
        $row = $this->db->get()->result_array();
        $arr_data['loan_type'] = $row;

        $arr_data['month_arr'] = $this->center_function->month_arr();
        $arr_data['month_short_arr'] = $this->center_function->month_short_arr();

        $this->preview_libraries->template_preview('report_share_data/coop_report_share_loan_balance_subdivision_custom_excel.php',$arr_data);

    }

    function sum_total_array($arr = array(), $keys, $index_num = 0){
	    foreach ($arr as $value){
	        foreach ($value as $index => $item){
	            if(is_array($keys)) {
	                foreach($keys as $in => $key) {
	                    for($i=0;$i <= 2; $i++){
                            $amt[$i][$key] += $item[$i][$key];
                        }
                    }
                }else{
	                $amt += (int)$item[$index_num][$keys];
                }
            }
        }
	    return $amt;
    }

	function coop_report_share_loan_balance_person_preview(){
		ini_set('memory_limit', -1);
		set_time_limit(-1);
		//$this->db->save_queries = FALSE;
		
		$datas = $this->get_share_loan_balance_person($_GET);
        $arr_data = $datas;
		$this->db->select('loan_name, loan_name_id, loan_type_id');
		$this->db->from('coop_loan_name');
		$this->db->where("loan_type_id = '8'");
        $arr_data['loan_type_normal'] = $this->db->get()->result_array();
        $loan_type_normal_new = array();
        foreach ($arr_data['loan_type_normal'] as $key => $value){
            $loan_type_normal_new[$value['loan_name_id']] = $value;
        }
        $arr_data['loan_type_normal'] = $loan_type_normal_new;
		if($_GET['dev'] == 'dev3'){
            echo '<pre>'; print_r($arr_data); echo '</pre>'; exit;
        }

		$this->preview_libraries->template_preview('report_share_data/coop_report_share_loan_balance_person_preview',$arr_data);
		
	}
	
	function coop_report_share_loan_balance_excel(){
		if(@$_GET['start_date']){
			$start_date_arr = explode('/',@$_GET['start_date']);
			$start_day = $start_date_arr[0];
			$start_month = $start_date_arr[1];
			$start_year = $start_date_arr[2];
			$start_year -= 543;
			$get_start_date = $start_year.'-'.$start_month.'-'.$start_day;
			
		}
		
		if(@$_GET['type_date'] == '1'){
			$this->db->select(array('share_date'));
			$this->db->from('coop_mem_share');
			$this->db->where("share_status IN ('1', '2')");
			$this->db->order_by("share_date ASC");
			$this->db->limit(1);
			$rs_date_share = $this->db->get()->result_array();
			$date_share_min  =  date("Y-m-d", strtotime(@$rs_date_share[0]['share_date']));
			
			
			$this->db->select(array('createdatetime'));
			$this->db->from('coop_loan');
			$this->db->where("loan_status = '1'");
			$this->db->order_by("createdatetime ASC");
			$this->db->limit(1);
			$rs_date_loan = $this->db->get()->result_array();
			$date_loan_min  =  date("Y-m-d", strtotime(@$rs_date_loan[0]['createdatetime']));
			
			$this->db->select(array('transaction_datetime'));
			$this->db->from('coop_loan_atm_transaction');
			$this->db->order_by("transaction_datetime ASC");
			$this->db->limit(1);
			$rs_date_loan_atm = $this->db->get()->result_array();
			$date_loan_atm_min  =  date("Y-m-d", strtotime(@$rs_date_loan_atm[0]['transaction_datetime']));

			if($date_share_min < $date_loan_min){
				$start_date = $date_share_min;
			}else if($date_loan_min < $date_loan_atm_min){
				$start_date = $date_loan_min;
			}else if($date_loan_atm_min < $date_share_min){
				$start_date = $date_loan_atm_min;
			}else{
				$start_date = $date_share_min;
			}
			$end_date = $get_start_date;
		}else{		
			$start_date = $get_start_date;
			$end_date = $get_start_date;
		}
		
		$where_date = "";		
		$where_date_loan = "";		
		$where_date_loan_atm = "";		
		$where_date_loan_atm_transaction = "";		
		$where_date_loan_transaction = "";		
		if(@$_GET['start_date'] != ''){
			$where_date .= " AND coop_mem_share.share_date BETWEEN '".$start_date." 00:00:00.000' AND '".$end_date." 23:59:59.000'";
			$where_date_loan .= " AND coop_loan.createdatetime BETWEEN '".$start_date." 00:00:00.000' AND '".$end_date." 23:59:59.000'";
			$where_date_loan_atm .= " AND coop_loan_atm.createdatetime BETWEEN '".$start_date." 00:00:00.000' AND '".$end_date." 23:59:59.000'";
			$where_date_loan_atm_transaction .= " AND coop_loan_atm_transaction.transaction_datetime BETWEEN '".$start_date." 00:00:00.000' AND '".$end_date." 23:59:59.000'";
			$where_date_loan_transaction .= " AND coop_loan_transaction.transaction_datetime BETWEEN '".$start_date." 00:00:00.000' AND '".$end_date." 23:59:59.000'";
		}

		$this->db->select(array('coop_loan_name.loan_name_id','coop_loan_type.loan_type_code'));
		$this->db->from('coop_loan_name');
		$this->db->join('coop_loan_type','coop_loan_name.loan_type_id = coop_loan_type.id','left');
		$rs_type_code = $this->db->get()->result_array();
		$arr_loan_type_code = array();
		foreach($rs_type_code AS $key_type_code=>$row_type_code){
			$arr_loan_type_code[@$row_type_code['loan_name_id']] = @$row_type_code['loan_type_code'];
		}	
		
		$sql = "SELECT `coop_mem_apply`.`member_id`, `coop_mem_apply`.`prename_id`, `coop_mem_apply`.`firstname_th`, `coop_mem_apply`.`lastname_th`, `coop_mem_apply`.`department`, `coop_mem_apply`.`faction`, `coop_mem_apply`.`level`,			
				`t4`.`share_collect`, `t4`.`share_collect_value`, `t4`.`share_id`, `t4`.`share_period`, `t4`.`share_date`,
				`t5`.`loan_id`, `t5`.`loan_amount_balance`, `t5`.`contract_number`, `t5`.`loan_type`,
				`t6`.`loan_atm_id`, `t6`.`contract_number` AS `contract_number_atm`, `t6`.`loan_amount_balance_atm`
				FROM (SELECT IF (
								(SELECT level_old FROM coop_mem_group_move WHERE date_move >= '".$end_date."' AND coop_mem_group_move.member_id = coop_mem_apply.member_id ORDER BY date_move ASC LIMIT 1),
								(SELECT level_old FROM coop_mem_group_move WHERE date_move >= '".$end_date."' AND coop_mem_group_move.member_id = coop_mem_apply.member_id ORDER BY date_move ASC LIMIT 1),
								coop_mem_apply. level
							) AS level,
							IF (
								(SELECT faction_old FROM coop_mem_group_move WHERE date_move >= '".$end_date."' AND coop_mem_group_move.member_id = coop_mem_apply.member_id ORDER BY date_move ASC LIMIT 1),
								(SELECT faction_old FROM coop_mem_group_move WHERE date_move >= '".$end_date."' AND coop_mem_group_move.member_id = coop_mem_apply.member_id ORDER BY date_move ASC LIMIT 1),
								coop_mem_apply.faction
							) AS faction,
							IF (
								(SELECT department_old FROM coop_mem_group_move WHERE date_move >= '".$end_date."' AND coop_mem_group_move.member_id = coop_mem_apply.member_id ORDER BY date_move ASC LIMIT 1),
								(SELECT department_old FROM coop_mem_group_move WHERE date_move >= '".$end_date."' AND coop_mem_group_move.member_id = coop_mem_apply.member_id ORDER BY date_move ASC LIMIT 1),
								coop_mem_apply.department
							) AS department, member_id, prename_id, firstname_th, lastname_th,member_status FROM coop_mem_apply) AS coop_mem_apply		
				LEFT JOIN (SELECT t1.share_id, t1.share_collect, t1.share_collect_value, t1.member_id, t1.share_period, t1.share_date
							FROM coop_mem_share as t1
							WHERE t1.share_date BETWEEN '".$start_date." 00:00:00.000' AND '".$end_date." 23:59:59.000' GROUP BY member_id) AS t4 ON `coop_mem_apply`.`member_id` = `t4`.`member_id`
				LEFT JOIN (SELECT t3.member_id ,t3.contract_number ,t3.period_now ,t3.loan_type 
								,t1.loan_transaction_id
								,t1.loan_id
								,t1.loan_amount_balance
								,t1.transaction_datetime FROM coop_loan_transaction AS t1 LEFT JOIN coop_loan AS t3 ON t1.loan_id = t3.id WHERE t1.transaction_datetime BETWEEN '".$start_date." 00:00:00.000' AND '".$end_date." 23:59:59.000' GROUP BY t1.loan_id ORDER BY t1.loan_id DESC ,t1.loan_transaction_id DESC )
								AS t5 ON `coop_mem_apply`.`member_id` = `t5`.`member_id`
				LEFT JOIN (SELECT t3.member_id ,t3.contract_number
								,t1.loan_atm_transaction_id
								,t1.loan_atm_id
								,t1.loan_amount_balance as loan_amount_balance_atm
								FROM coop_loan_atm_transaction AS t1 LEFT JOIN coop_loan_atm AS t3 ON t1.loan_atm_id = t3.loan_atm_id WHERE t1.transaction_datetime BETWEEN '".$start_date." 00:00:00.000' AND '".$end_date." 23:59:59.000'
								GROUP BY t1.loan_atm_id ORDER BY t1.loan_atm_id DESC ,t1.loan_atm_transaction_id DESC ) AS t6
								ON `coop_mem_apply`.`member_id` = `t6`.`member_id`
				WHERE (`t5`.`loan_id` != '' OR `t4`.`share_id` != '' OR `t6`.`loan_atm_id` != '') AND coop_mem_apply.member_status = 1 
				ORDER BY coop_mem_apply.member_id ASC";
		//echo $sql.'<hr>'; exit;		
		$result = $this->db->query($sql)->result_array();
		
		$member_ids = array_column($result, 'member_id');
		//echo '<pre>'; print_r($member_ids); echo '</pre>'; exit;
		
		//Get Lastest Loan Information
		$loan_ids = array_column($result, 'loan_id');
        $where_loan = " 1=1 ";
        if(sizeof(array_filter($loan_ids))){
            $where_loan = " t1.loan_id IN  (".implode(',',array_filter($loan_ids)).") ";
        }
		$loans = $this->db->query("SELECT `t1`.`loan_transaction_id`, `t1`.`loan_id`, `t1`.`loan_amount_balance`, `t1`.`transaction_datetime`
									FROM `coop_loan_transaction` as `t1`
									INNER JOIN (SELECT loan_id, MAX(cast(transaction_datetime as Datetime)) as max FROM coop_loan_transaction WHERE transaction_datetime BETWEEN '".$start_date." 00:00:00' AND '".$end_date." 23:59:59' group by loan_id)
											as t2 ON `t1`.`loan_id` = `t2`.`loan_id` AND `t1`.`transaction_datetime` = `t2`.`max`
									WHERE {$where_loan}
									ORDER BY `t1`.`transaction_datetime`, `t1`.`loan_transaction_id` DESC
									")->result_array();
		$loan_members = array_column($loans, 'loan_id');
		//echo '<pre>'; print_r($loans); echo '</pre>'; exit;	
		//echo $this->db->last_query();exit;
		//Get Lastest Loan ATM Information
		$loan_atm_ids = array_column($result, 'loan_atm_id');
		$where_atm = " 1=1 ";
		if(sizeof(array_filter($loan_atm_ids))){
            $where_atm = " t1.loan_atm_id IN  (".implode(',',array_filter($loan_atm_ids)).") ";
        }

		$loan_atms = $this->db->query("SELECT t1.loan_atm_transaction_id, `t1`.`loan_atm_id`, `t1`.`transaction_datetime`,
									t1.loan_amount_balance AS loan_amount_balance
		
									FROM `coop_loan_atm_transaction` as `t1`
									INNER JOIN (SELECT loan_atm_id, MAX(cast(transaction_datetime as Datetime)) as max FROM coop_loan_atm_transaction WHERE transaction_datetime BETWEEN '".$start_date." 00:00:00' AND '".$end_date." 23:59:59' group by loan_atm_id)
											as t2 ON `t1`.`loan_atm_id` = `t2`.`loan_atm_id` AND `t1`.`transaction_datetime` = `t2`.`max`
									LEFT JOIN `coop_loan_atm_detail` AS `t3` ON `t1`.`loan_atm_id` = `t3`.`loan_atm_id`	AND `t1`.`transaction_datetime` = `t3`.`loan_date`
									LEFT JOIN `coop_finance_transaction` AS `t4` ON `t1`.`receipt_id` = `t4`.`receipt_id`	AND `t1`.`loan_atm_id` = `t4`.`loan_atm_id`
									LEFT JOIN coop_receipt AS t6 ON t1.receipt_id = t6.receipt_id
									WHERE {$where_atm}
									GROUP BY `t1`.`loan_atm_id`
									ORDER BY `t1`.`transaction_datetime`, `t1`.`loan_atm_transaction_id` DESC
									")->result_array();		
		$loan_atm_members = array_column($loan_atms, 'loan_atm_id');
		//echo '<pre>'; print_r($loan_atms); echo '</pre>'; exit;	
		$run_index = 0;
		$row = array();
		
		$check_row = "xx";
		$index = 0;

		foreach($result AS $key2=>$value2){
			if($check_row != @$value2['member_id']){				
				$check_row = @$value2['member_id'];
				$sql_shares = "SELECT 
									t1.member_id
									,t1.share_period
									,t1.share_collect_value
									,t1.share_status
									,t1.share_payable_value
									,'1' AS check_share
								FROM coop_mem_share AS t1
								WHERE t1.member_id = '".$value2['member_id']."'
								AND t1.share_date BETWEEN '".$start_date." 00:00:00.000' AND '".$end_date." 23:59:59.000'
								AND t1.share_status <> 0
								ORDER BY t1.share_date DESC ,t1.share_id DESC
								LIMIT 1";
				//echo $sql_shares.'<br>';				
				$shares = $this->db->query($sql_shares)->row_array();
				$share_period = (!empty($shares['share_period']))?@$shares['share_period']: "";
				
				if(@$shares['share_status'] == 3){
					$share_collect_value = (!empty($shares['share_payable_value']))?@$shares['share_payable_value']: "";
				}else{
					$share_collect_value = (!empty($shares['share_collect_value']))?@$shares['share_collect_value']: "";
				}
				$check_share = (!empty($shares['check_share']) && !empty($share_collect_value))?@$shares['check_share']: "";
				
				$runno = 1;
			}else{	
				$runno++;
			}

			$row['data'][$value2['member_id']][$runno]['member_id'] = $value2['member_id'];
			$row['data'][$value2['member_id']][$runno]['prename_full'] = $value2['prename_full'];
			$row['data'][$value2['member_id']][$runno]['firstname_th'] = $value2['firstname_th'];
			$row['data'][$value2['member_id']][$runno]['lastname_th'] = $value2['lastname_th'];
			$row['data'][$value2['member_id']][$runno]['mem_group_name_main'] = $value2['mem_group_name_main'];
			$row['data'][$value2['member_id']][$runno]['mem_group_name_sub'] = $value2['mem_group_name_sub'];
			$row['data'][$value2['member_id']][$runno]['mem_group_name_level'] = $value2['name'];
			$row['data'][$value2['member_id']][$runno]['mem_group_id'] = $value2['id'];
			$row['data'][$value2['member_id']][$runno]['department'] = $value2['department'];
			$row['data'][$value2['member_id']][$runno]['faction'] = $value2['faction'];
			$row['data'][$value2['member_id']][$runno]['level'] = $value2['level'];
			$row['data'][$value2['member_id']][$runno]['loan_type'] = $value2['loan_type'];
			if($row_mem_group_level->sub_name == '' || $value2->sub_name=='ไม่ระบุ'){					
				$row['data'][$value2['member_id']][$runno]['mem_group_name_sub'] = $value2['main_name'];
			}else{					
				$row['data'][$value2['member_id']][$runno]['mem_group_name_sub'] = $value2['sub_name'];
			}
				
			$row['data'][$value2['member_id']][$runno]['mem_group_name_main'] = $value2['main_name'];
			
			//หุ้น
			if ($runno == 1) {
				$row['data'][$value2['member_id']][$runno]['share_period'] = $share_period;
				$row['data'][$value2['member_id']][$runno]['share_collect'] = $share_collect_value;
				$row['data'][$value2['member_id']][$runno]['check_share'] = $check_share;
			} else {
				$row['data'][$value2['member_id']][$runno]['share_period'] = "";
				$row['data'][$value2['member_id']][$runno]['share_collect'] = "";
				$row['data'][$value2['member_id']][$runno]['check_share'] = "";
			}
			$row['data'][$value2['member_id']][$runno]['runno'] = $runno;
			
			$loan_type_code = @$arr_loan_type_code[$value2['loan_type']];
			if(@$loan_type_code == 'emergent' && @$value2['loan_amount_balance'] != '' && in_array($value2['loan_id'],$loan_members)
					&& $loans[array_search($value2['loan_id'],$loan_members)]['loan_amount_balance']){
				//เงินกู้ฉุกเฉิน
				if ($runno == 1) {
					$row['data'][$value2['member_id']][$runno]['loan_emergent_period_now'] = @$value2['period_now'];
					$row['data'][$value2['member_id']][$runno]['loan_emergent_contract_number'] = @$value2['contract_number'];
					$row['data'][$value2['member_id']][$runno]['loan_emergent_balance'] = $loans[array_search($value2['loan_id'],$loan_members)]['loan_amount_balance'];
					$row['data'][$value2['member_id']][$runno]['check_emergent'] = 1;
				} else {
					for($no_count = 1; $no_count <= $runno; $no_count++) {
						if (empty($row['data'][$value2['member_id']][$no_count]['loan_emergent_contract_number'])) {
							$row['data'][$value2['member_id']][$no_count]['loan_emergent_period_now'] = @$value2['period_now'];
							$row['data'][$value2['member_id']][$no_count]['loan_emergent_contract_number'] = @$value2['contract_number'];
							$row['data'][$value2['member_id']][$no_count]['loan_emergent_balance'] = $loans[array_search($value2['loan_id'],$loan_members)]['loan_amount_balance'];
							$row['data'][$value2['member_id']][$no_count]['check_emergent'] = 1;
							break;
						} else if ($row['data'][$value2['member_id']][$no_count]['loan_emergent_contract_number'] == $value2['contract_number']) {
							break;
						}
					}
				}
				$run_emergent++;
				if($run_emergent > 1){
					//$runno++;
				}
			}

			if(@$loan_type_code == 'normal' && @$value2['loan_amount_balance'] != '' && in_array($value2['loan_id'],$loan_members)
					&& !empty($loans[array_search($value2['loan_id'],$loan_members)]['loan_amount_balance'])){
				if ($runno == 1) {
					$row['data'][$value2['member_id']][$runno]['loan_normal_period_now'] = @$value2['period_now'];
					$row['data'][$value2['member_id']][$runno]['loan_normal_contract_number'] = @$value2['contract_number'];
					$row['data'][$value2['member_id']][$runno]['loan_normal_balance'] = $loans[array_search($value2['loan_id'],$loan_members)]['loan_amount_balance'];
					$row['data'][$value2['member_id']][$runno]['check_normal'] = 1;
				} else {
					for($no_count = 1; $no_count <= $runno; $no_count++) {
						if ($row['data'][$value2['member_id']][$no_count]['loan_normal_contract_number'] == $value2['contract_number']) {
							break;
						} else if (empty($row['data'][$value2['member_id']][$no_count]['loan_normal_contract_number'])) {
							$row['data'][$value2['member_id']][$no_count]['loan_normal_period_now'] = @$value2['period_now'];
							$row['data'][$value2['member_id']][$no_count]['loan_normal_contract_number'] = @$value2['contract_number'];
							$row['data'][$value2['member_id']][$no_count]['loan_normal_balance'] = $loans[array_search($value2['loan_id'],$loan_members)]['loan_amount_balance'];
							$row['data'][$value2['member_id']][$no_count]['check_normal'] = 1;
							break;
						}
					}
				}
				$run_normal++;
			}
			
			if(@$loan_type_code == 'special' && @$value2['loan_amount_balance'] != '' && in_array($value2['loan_id'],$loan_members)
					&& !empty($loans[array_search($value2['loan_id'],$loan_members)]['loan_amount_balance'])){
				if ($runno == 1) {
					$row['data'][$value2['member_id']][$runno]['loan_special_period_now'] = @$value2['period_now'];
					$row['data'][$value2['member_id']][$runno]['loan_special_contract_number'] = @$value2['contract_number'];
					$row['data'][$value2['member_id']][$runno]['loan_special_balance'] = $loans[array_search($value2['loan_id'],$loan_members)]['loan_amount_balance'];
					$row['data'][$value2['member_id']][$runno]['check_special'] = 1;
				} else {
					for($no_count = 1; $no_count <= $runno; $no_count++) {
						if (empty($row['data'][$value2['member_id']][$no_count]['loan_special_contract_number'])) {
							$row['data'][$value2['member_id']][$no_count]['loan_special_period_now'] = @$value2['period_now'];
							$row['data'][$value2['member_id']][$no_count]['loan_special_contract_number'] = @$value2['contract_number'];
							$row['data'][$value2['member_id']][$no_count]['loan_special_balance'] = $loans[array_search($value2['loan_id'],$loan_members)]['loan_amount_balance'];
							$row['data'][$value2['member_id']][$no_count]['check_special'] = 1;
							break;
						} else if ($row['data'][$value2['member_id']][$no_count]['loan_special_contract_number'] == $value2['contract_number']) {
							break;
						}
					}
				}

				$run_special++;
			}

			if(@$value2['loan_amount_balance_atm'] != '' && in_array($value2['loan_atm_id'],$loan_atm_members)
						&& !empty($loan_atms[array_search($value2['loan_atm_id'],$loan_atm_members)]['loan_amount_balance'])){
				//เงินกู้ฉุกเฉิน ATM
				$atm_index_count = $runno;
				if(!empty($row['data'][$value2['member_id']][$runno]['loan_emergent_contract_number'])) {
					$atm_index_count = $runno+1;
				}
				for($no_count = 1; $no_count <= $atm_index_count; $no_count++) {
					if (empty($row['data'][$value2['member_id']][$no_count]['loan_emergent_contract_number'])) {
						$row['data'][$value2['member_id']][$no_count]['member_id'] = $value2['member_id'];
						$row['data'][$value2['member_id']][$no_count]['prename_full'] = $value2['prename_full'];
						$row['data'][$value2['member_id']][$no_count]['firstname_th'] = $value2['firstname_th'];
						$row['data'][$value2['member_id']][$no_count]['lastname_th'] = $value2['lastname_th'];
						$row['data'][$value2['member_id']][$no_count]['mem_group_name_main'] = $value2['mem_group_name_main'];
						$row['data'][$value2['member_id']][$no_count]['mem_group_name_sub'] = $value2['mem_group_name_sub'];
						$row['data'][$value2['member_id']][$no_count]['mem_group_name_level'] = $value2['name'];
						$row['data'][$value2['member_id']][$no_count]['mem_group_id'] = $value2['id'];
						$row['data'][$value2['member_id']][$no_count]['mem_group_id'] = $value2['id'];
						$row['data'][$value2['member_id']][$runno]['mem_group_name_level'] = $value2['name'];
						if($row_mem_group_level->sub_name == '' || $value2->sub_name=='ไม่ระบุ'){					
							$row['data'][$value2['member_id']][$no_count]['mem_group_name_sub'] = $value2['main_name'];
						}else{					
							$row['data'][$value2['member_id']][$no_count]['mem_group_name_sub'] = $value2['sub_name'];
						}
	
						$row['data'][$value2['member_id']][$no_count]['mem_group_name_main'] = $value2['main_name'];
						
						$row['data'][$value2['member_id']][$runno]['department'] = $value2['department'];
						$row['data'][$value2['member_id']][$runno]['faction'] = $value2['faction'];
						$row['data'][$value2['member_id']][$runno]['level'] = $value2['level'];
						$row['data'][$value2['member_id']][$runno]['loan_type'] = $value2['loan_type'];

						//หุ้น
						if ($runno == 1) {
							$row['data'][$value2['member_id']][$runno]['share_period'] = $share_period;
							$row['data'][$value2['member_id']][$runno]['share_collect'] = $share_collect_value;
							$row['data'][$value2['member_id']][$runno]['check_share'] = $check_share;
						} else {
							$row['data'][$value2['member_id']][$runno]['share_period'] = "";
							$row['data'][$value2['member_id']][$runno]['share_collect'] = "";
							$row['data'][$value2['member_id']][$runno]['check_share'] = "";
						}

						$row['data'][$value2['member_id']][$no_count]['runno'] = $runno;
						$row['data'][$value2['member_id']][$no_count]['loan_emergent_period_now'] = '';
						$row['data'][$value2['member_id']][$no_count]['loan_emergent_contract_number'] = @$value2['contract_number_atm'];
						$row['data'][$value2['member_id']][$no_count]['loan_emergent_balance'] = $loan_atms[array_search($value2['loan_atm_id'],$loan_atm_members)]['loan_amount_balance'];
						$row['data'][$value2['member_id']][$no_count]['check_emergent'] = 1;	
						break;
					} else if ($row['data'][$value2['member_id']][$no_count]['loan_emergent_contract_number'] == $value2['contract_number_atm']) {
						break;
					}
				}	
			}

			$run_index++;		

		}

		//Generate Fund support Information
		$where_fund = "1=1";
		$where_fund_t1 = $_GET["type_date"] == 1 ? "payment_date <= '".$end_date." 23:59:59.000'" : "payment_date BETWEEN '".$end_date." 00:00:00.000' AND '".$end_date." 23:59:59.000'";
		$funds = $this->db->select("SUM(t2.principal) as loan_amount_balance, t5.member_id, t5.prename_id, t5.firstname_th, t5.lastname_th, t5.level, t7.id as faction, t8.id as department, t9.prename_full,
									t6.mem_group_id as id, t6.mem_group_name as name, t7.mem_group_name as sub_name, t8.mem_group_name as main_name, t4.id as loan_id, t4.contract_number, t4.loan_type, t4.period_now")
							->from("(SELECT *, MAX(payment_date) as max_date FROM coop_loan_fund_balance_transaction WHERE ".$where_fund_t1." GROUP BY sub_compromise_id) as t1")
							->join("coop_loan_fund_balance_transaction as t2", "t1.sub_compromise_id = t2.sub_compromise_id AND t1.max_date = t2.payment_date", "inner")
							->join("coop_loan_compromise as t3", "t2.compromise_id = t3.id", "inner")
							->join("coop_loan as t4", "t3.loan_id = t4.id", "inner")
							->join("(SELECT IF (
										(SELECT level_old FROM coop_mem_group_move WHERE date_move >= '".$end_date."' AND coop_mem_group_move.member_id = coop_mem_apply.member_id ORDER BY date_move ASC LIMIT 1),
										(SELECT level_old FROM coop_mem_group_move WHERE date_move >= '".$end_date."' AND coop_mem_group_move.member_id = coop_mem_apply.member_id ORDER BY date_move ASC LIMIT 1),
										coop_mem_apply. level
									) AS level, member_id, prename_id, firstname_th, lastname_th,member_status FROM coop_mem_apply) as t5", "t3.member_id = t5.member_id", "inner")
							->join("coop_mem_group as t6", "t5.level = t6.id", "left")
							->join("coop_mem_group as t7", "t7.id = t6.mem_group_parent_id", "left")
							->join("coop_mem_group as t8", "t8.id = t7.mem_group_parent_id", "left")
							->join("coop_prename as t9", "t5.prename_id = t9.prename_id", "left")
							->where($where_fund)
							->group_by("t2.compromise_id")
							->get()->result_array();

		foreach($funds as $fund) {
			if($fund["loan_amount_balance"] > 0) {
				$data_arr = array();
				$data_arr["member_id"] = $fund["member_id"];
				$data_arr["prename_id"] = $fund["prename_id"];
				$data_arr["firstname_th"] = $fund["firstname_th"];
				$data_arr["lastname_th"] = $fund["lastname_th"];
				$data_arr["department"] = $fund["department"];
				$data_arr["faction"] = $fund["faction"];
				$data_arr["level"] = $fund["level"];
				$data_arr["prename_full"] = $fund["prename_full"];
				$data_arr["id"] = $fund["id"];
				$data_arr["name"] = $fund["name"];
				$data_arr["sub_name"] = $fund["sub_name"];
				$data_arr["main_name"] = $fund["main_name"];
				$data_arr["loan_id"] = $fund["loan_id"];
				$data_arr['loan_amount_balance'] = $fund["loan_amount_balance"];
				$data_arr["contract_number"] = $fund["contract_number"];
				$data_arr["loan_type"] = $fund["loan_type"];
				$data_arr["period_now"] = $fund["period_now"];
				$data_arr['mem_group_id'] = $fund["id"];
				$data_arr['mem_group_name_level'] = $fund["level"];
				$data_arr["mem_group_name_sub"] = $fund["faction"];
				$data_arr["mem_group_name_main"] = $fund["department"];
				$data_arr["loan_normal_period_now"] = $fund["period_now"];
				$data_arr["loan_normal_contract_number"] = $fund["contract_number"];
				$data_arr["loan_normal_balance"] = $fund["loan_amount_balance"];
				$data_arr["check_normal"] = 1;
				$row['data'][$fund["member_id"]][] = $data_arr;
			}
		}

		$data_tmp = $row['data'];	
		
		$data_tmp_sum = array();
		foreach($data_tmp AS $key=>$value){
			foreach($value AS $key2=>$value2){				
				if(@$_GET['type_department'] == '1'){
					$data_tmp_sum[@$value2['department']]['share_person'] += !empty($value2['check_share']) ? $value2['check_share'] : 0;
					$data_tmp_sum[@$value2['department']]['share_collect'] += !empty($value2['share_collect']) ? $value2['share_collect'] : 0;
					
					$data_tmp_sum[@$value2['department']]['loan_emergent_person'] += !empty($value2['loan_emergent_person']) ? $value2['loan_emergent_person'] : 0;
					$data_tmp_sum[@$value2['department']]['loan_emergent_balance'] += !empty($value2['loan_emergent_balance']) ? $value2['loan_emergent_balance'] : 0;
					
					$data_tmp_sum[@$value2['department']]['loan_normal_person'] += !empty($value2['loan_normal_person']) ? $value2['loan_normal_person'] : 0;
					$data_tmp_sum[@$value2['department']]['loan_normal_balance'] += !empty($value2['loan_normal_balance']) ? $value2['loan_normal_balance'] : 0;
					
					$data_tmp_sum[@$value2['department']]['loan_special_person'] += !empty($value2['loan_special_person']) ? $value2['loan_special_person'] : 0;
					$data_tmp_sum[@$value2['department']]['loan_special_balance'] += !empty($value2['loan_special_balance']) ? $value2['loan_special_balance'] : 0;
				}else if(@$_GET['type_department'] == '2'){				
					$data_tmp_sum[@$value2['faction']]['share_person'] += !empty($value2['check_share']) ? $value2['check_share'] : 0;
					$data_tmp_sum[@$value2['faction']]['share_collect'] += !empty($value2['share_collect']) ? $value2['share_collect'] : 0;
					
					$data_tmp_sum[@$value2['faction']]['loan_emergent_person'] += !empty($value2['check_emergent']) ? $value2['check_emergent'] : 0;
					$data_tmp_sum[@$value2['faction']]['loan_emergent_balance'] += !empty($value2['loan_emergent_balance']) ? $value2['loan_emergent_balance'] : 0;

					$data_tmp_sum[@$value2['faction']]['loan_normal_person'] += !empty($value2['check_normal']) ? $value2['check_normal'] : 0;
					$data_tmp_sum[@$value2['faction']]['loan_normal_balance'] += !empty($value2['loan_normal_balance']) ? $value2['loan_normal_balance'] : 0;
					
					$data_tmp_sum[@$value2['faction']]['loan_special_person'] += !empty($value2['check_special']) ? $value2['check_special'] : 0;
					$data_tmp_sum[@$value2['faction']]['loan_special_balance'] += !empty($value2['loan_special_balance']) ? $value2['loan_special_balance'] : 0;
					
					$data_tmp_sum[@$value2['faction']]['share_balance_subdivision'] += !empty($value2['share_collect']) ? $value2['share_collect'] : 0;
					$data_tmp_sum[@$value2['faction']]['loan_balance_subdivision'] += !empty($value2['loan_emergent_balance']) ? @$value2['loan_emergent_balance']+@$value2['loan_normal_balance']+@$value2['loan_special_balance'] : 0;
				}
			}	
		}
		
		$arr_data = array();
		$x=0;
		$join_arr = array();
		
		$where = "";
		$field_department = "department";
		if(@$_GET['type_department'] == '1'){
			$where .= " AND coop_mem_group.mem_group_type = '1'";
			
		}else if(@$_GET['type_department'] == '2'){
			$where .= " AND coop_mem_group.mem_group_type = '2'";
			$field_department = "faction";
		}
		
		$this->paginater_all_preview->type(DB_TYPE);
		$this->paginater_all_preview->select(array(
													'coop_mem_group.id',
													'coop_mem_group.mem_group_id',
													'coop_mem_group.mem_group_parent_id',
													'coop_mem_group.mem_group_name'
												));
		$this->paginater_all_preview->main_table('coop_mem_group');
		$this->paginater_all_preview->where("{$where}");
		$this->paginater_all_preview->page_now(@$_GET["page"]);
		$this->paginater_all_preview->per_page(20);
		$this->paginater_all_preview->page_link_limit(28);
		$this->paginater_all_preview->page_limit_first(22);
		$this->paginater_all_preview->order_by('coop_mem_group.mem_group_id');
		$this->paginater_all_preview->join_arr($join_arr);
		$row = $this->paginater_all_preview->paginater_process();
		
		foreach($row['data'] AS $key=>$value){
			$check_group_id_sub = 'xx';
			foreach($value AS $key2=>$value2){
				//echo '<pre>'; print_r($row); echo '</pre>';
				//หุ้น
				$row['data'][$key][$key2]['share_person'] = @$data_tmp_sum[$value2['id']]['share_person'];
				$row['data'][$key][$key2]['share_collect'] = @$data_tmp_sum[$value2['id']]['share_collect'];
				//เงินกู้ฉุกเฉิน
				$row['data'][$key][$key2]['loan_emergent_person'] = @$data_tmp_sum[$value2['id']]['loan_emergent_person'];
				$row['data'][$key][$key2]['loan_emergent_balance'] = @$data_tmp_sum[$value2['id']]['loan_emergent_balance'];
				
				//เงินกู้สามัญ
				$row['data'][$key][$key2]['loan_normal_person'] = @$data_tmp_sum[$value2['id']]['loan_normal_person'];
				$row['data'][$key][$key2]['loan_normal_balance'] = @$data_tmp_sum[$value2['id']]['loan_normal_balance'];
				
				//เงินกู้พิเศษ
				$row['data'][$key][$key2]['loan_special_person'] = @$data_tmp_sum[$value2['id']]['loan_special_person'];
				$row['data'][$key][$key2]['loan_special_balance'] = @$data_tmp_sum[$value2['id']]['loan_special_balance'];
				
				//รวมเงินกู้คงเหลือ
				$row['data'][$key][$key2]['total_loan_balance'] = @$data_tmp_sum[$value2['id']]['loan_emergent_balance']+@$data_tmp_sum[$value2['id']]['loan_normal_balance']+@$data_tmp_sum[$value2['id']]['loan_special_balance'];
				
				$this->db->select(array('coop_mem_group.id','coop_mem_group.mem_group_id','coop_mem_group.mem_group_parent_id','coop_mem_group.mem_group_name'));
				$this->db->from('coop_mem_group');
				$this->db->where("coop_mem_group.id = '".@$value2['mem_group_parent_id']."'");
				$this->db->limit(1);
				$rs_mem_group_sub = $this->db->get()->result_array();
				$row_mem_group_sub = @$rs_mem_group_sub[0];
				if(@$row_mem_group_sub['mem_group_name'] == '' || @$row_mem_group_sub['mem_group_name']=='ไม่ระบุ'){
					$this->db->select(array('coop_mem_group.id','coop_mem_group.mem_group_id','coop_mem_group.mem_group_parent_id','coop_mem_group.mem_group_name'));
					$this->db->from('coop_mem_group');
					$this->db->where("coop_mem_group.id = '".@$row_mem_group_sub['mem_group_parent_id']."'");
					$this->db->limit(1);
					$rs_mem_group_main = $this->db->get()->result_array();
					
					$row['data'][$key][$key2]['mem_group_name_main'] = @$rs_mem_group_main[0]['mem_group_name'];
					$mem_group_id_sub = @$rs_mem_group_main[0]['id'];
					$row['data'][$key][$key2]['share_balance_subdivision'] = $row['data'][$key][$key2]['share_collect'];
					$row['data'][$key][$key2]['loan_balance_subdivision'] = $row['data'][$key][$key2]['total_loan_balance'];
				}else{	
					$mem_group_id_sub = @$value2['mem_group_parent_id'];
					$row['data'][$key][$key2]['mem_group_name_main'] = @$row_mem_group_sub['mem_group_name'];
					$row['data'][$key][$key2]['share_balance_subdivision'] = @$data_tmp_sum[$mem_group_id_sub]['share_balance_subdivision'];
					$row['data'][$key][$key2]['loan_balance_subdivision'] = @$data_tmp_sum[$mem_group_id_sub]['loan_balance_subdivision'];
				}
				
				if($mem_group_id_sub == $check_group_id_sub){				
					$run_sub++;	
				}else{
					$check_group_id_sub = $mem_group_id_sub;
				}
				
				$arr_data['arr_run_sub'][@$mem_group_id_sub] = @$run_sub;

				$row['data'][$key][$key2]['run_sub'] = @$run_sub;
				$row['data'][$key][$key2]['mem_group_id_sub'] = @$mem_group_id_sub;
			}	
		}
		
		$arr_data['num_rows'] = $row['num_rows'];
		$arr_data['paging'] = $paging;
		$arr_data['data'] = $row['data'];
		$arr_data['page_all'] = $row['page_all'];
		
		$this->db->select(array('id','loan_type','loan_type_code'));
		$this->db->from('coop_loan_type');
		$this->db->order_by("order_by");
		$row = $this->db->get()->result_array();
		$arr_data['loan_type'] = $row;
		
		$arr_data['month_arr'] = $this->center_function->month_arr();
		$arr_data['month_short_arr'] = $this->center_function->month_short_arr();
		
		//echo"<pre>";print_r($arr_data['loan_type']);exit;
		if(@$_GET['type_department'] == '1'){
			$this->load->view('report_share_data/coop_report_share_loan_balance_excel',$arr_data);
		}else if(@$_GET['type_department'] == '2'){
			$this->load->view('report_share_data/coop_report_share_loan_balance_subdivision_excel',$arr_data);
		}
		
	}

	function coop_report_share_loan_balance_person_excel(){
		ini_set('memory_limit', -1);
		set_time_limit (180);
		
		$datas = $this->get_share_loan_balance_person($_GET);
		$arr_data = $datas;
		
		$this->load->view('report_share_data/coop_report_share_loan_balance_person_excel',$arr_data);	
	}

	public function coop_report_share_loan_balance_sms_person() {
		ini_set('memory_limit', -1);
		set_time_limit (180);
		if(@$_GET['start_date']){
			$start_date_arr = explode('/',@$_GET['start_date']);
			$start_day = $start_date_arr[0];
			$start_month = $start_date_arr[1];
			$start_year = $start_date_arr[2];
			$start_year -= 543;
			$get_start_date = $start_year.'-'.$start_month.'-'.$start_day;
		}

		if(@$_GET['type_date'] == '1'){
			$this->db->select(array('share_date'));
			$this->db->from('coop_mem_share');
			$this->db->where("share_status IN ('1', '2')");
			$this->db->order_by("share_date ASC");
			$this->db->limit(1);
			$rs_date_share = $this->db->get()->result_array();
			$date_share_min  =  date("Y-m-d", strtotime(@$rs_date_share[0]['share_date']));
			
			
			$this->db->select(array('createdatetime'));
			$this->db->from('coop_loan');
			$this->db->where("loan_status = '1'");
			$this->db->order_by("createdatetime ASC");
			$this->db->limit(1);
			$rs_date_loan = $this->db->get()->result_array();
			$date_loan_min  =  date("Y-m-d", strtotime(@$rs_date_loan[0]['createdatetime']));
			
			$this->db->select(array('transaction_datetime'));
			$this->db->from('coop_loan_atm_transaction');
			$this->db->order_by("transaction_datetime ASC");
			$this->db->limit(1);
			$rs_date_loan_atm = $this->db->get()->result_array();
			$date_loan_atm_min  =  date("Y-m-d", strtotime(@$rs_date_loan_atm[0]['transaction_datetime']));

			if($date_share_min < $date_loan_min){
				$start_date = $date_share_min;
			}else if($date_loan_min < $date_loan_atm_min){
				$start_date = $date_loan_min;
			}else if($date_loan_atm_min < $date_share_min){
				$start_date = $date_loan_atm_min;
			}else{
				$start_date = $date_share_min;
			}
			$end_date = $get_start_date;
		}else{		
			$start_date = $get_start_date;
			$end_date = $get_start_date;
		}

		$where_date = "";		
		$where_date_loan = "";		
		$where_date_loan_atm = "";		
		$where_date_loan_atm_transaction = "";		
		$where_date_loan_transaction = "";		
		if(@$_GET['start_date'] != ''){
			$where_date .= " AND coop_mem_share.share_date BETWEEN '".$start_date." 00:00:00.000' AND '".$end_date." 23:59:59.000'";
			$where_date_loan .= " AND coop_loan.createdatetime BETWEEN '".$start_date." 00:00:00.000' AND '".$end_date." 23:59:59.000'";
			$where_date_loan_atm .= " AND coop_loan_atm.createdatetime BETWEEN '".$start_date." 00:00:00.000' AND '".$end_date." 23:59:59.000'";
			$where_date_loan_atm_transaction .= " AND coop_loan_atm_transaction.transaction_datetime BETWEEN '".$start_date." 00:00:00.000' AND '".$end_date." 23:59:59.000'";
			$where_date_loan_transaction .= " AND coop_loan_transaction.transaction_datetime BETWEEN '".$start_date." 00:00:00.000' AND '".$end_date." 23:59:59.000'";
		}		
		
		
		$this->db->select(array('coop_loan_name.loan_name_id','coop_loan_type.loan_type_code'));
		$this->db->from('coop_loan_name');
		$this->db->join('coop_loan_type','coop_loan_name.loan_type_id = coop_loan_type.id','left');
		$rs_type_code = $this->db->get()->result_array();
		$arr_loan_type_code = array();
		foreach($rs_type_code AS $key_type_code=>$row_type_code){
			$arr_loan_type_code[@$row_type_code['loan_name_id']] = @$row_type_code['loan_type_code'];
		}
		
		$this->db->select(array('max_period'));
		$this->db->from('coop_loan_atm_setting');					
		$rs_atm_setting = $this->db->get()->result_array();
		$row_atm_setting = @$rs_atm_setting[0];
		$max_period_atm = $row_atm_setting['max_period'];

		$arr_data = array();
		
		$where = "";
	
		//Get All data
		$rs = $this->db->select(array(
			'coop_mem_apply.member_id',
			'coop_mem_apply.mobile',
			'coop_mem_apply.department',
			'coop_mem_apply.faction',
			'coop_mem_apply.level',
			't1.mem_group_id as id',
			't1.mem_group_name as name',
			't2.mem_group_name as sub_name',
			't3.mem_group_name as main_name',
			't4.share_id',
			't5.loan_id',
			't5.loan_amount_balance',
			't5.contract_number',
			't5.loan_type',
			't6.loan_atm_id',
			't6.contract_number AS contract_number_atm',
			't6.loan_amount_balance_atm'
		))
		->from("(SELECT member_id, mobile, department, faction, level FROM coop_mem_apply) as coop_mem_apply")
		->join("coop_mem_group as t1","t1.id = coop_mem_apply.level","left")
		->join("coop_mem_group as t2", "t2.id = t1.mem_group_parent_id", "left")
		->join("coop_mem_group as t3", "t3.id = t2.mem_group_parent_id", "left")
		->join("(SELECT 	 
						coop_mem_share.share_id,
						coop_mem_share.member_id
				 	FROM  
						coop_mem_share WHERE coop_mem_share.share_date BETWEEN '".$start_date." 00:00:00.000' AND '".$end_date." 23:59:59.000' GROUP BY member_id) AS t4",
					"coop_mem_apply.member_id = t4.member_id",
					"left"
				)
		->join("(SELECT
						t3.member_id
						,t3.contract_number
						,t3.period_now
						,t3.loan_type
						,t1.loan_transaction_id
						,t1.loan_id
						,t1.loan_amount_balance
						,t1.transaction_datetime
					FROM
						coop_loan_transaction AS t1
					LEFT JOIN coop_loan AS t3 ON t1.loan_id = t3.id 
					WHERE t1.transaction_datetime BETWEEN '".$start_date." 00:00:00' AND '".$end_date." 23:59:59'
					GROUP BY t1.loan_id
					ORDER BY t1.loan_id DESC ,t1.loan_transaction_id DESC
					) AS t5", "coop_mem_apply.member_id = t5.member_id", "left")
		->join("(SELECT
						t3.member_id
						,t3.contract_number
						,t1.loan_atm_transaction_id
						,t1.loan_atm_id
						,t1.loan_amount_balance as loan_amount_balance_atm
					FROM
						coop_loan_atm_transaction AS t1
					LEFT JOIN coop_loan_atm AS t3 ON t1.loan_atm_id = t3.loan_atm_id 
					WHERE t1.transaction_datetime BETWEEN '".$start_date." 00:00:00' AND '".$end_date." 23:59:59'
					GROUP BY t1.loan_atm_id
					ORDER BY t1.loan_atm_id DESC ,t1.loan_atm_transaction_id DESC
					) AS t6", "coop_mem_apply.member_id = t6.member_id", "left")
		->where("1=1  AND (t5.loan_id != '' OR t4.share_id != '' OR t6.loan_atm_id != '')")
		->order_by('coop_mem_apply.member_id ASC')
		->get()->result_array();

		//Get Lastest Share Information
		$member_ids = array_column($rs, 'member_id');
		$shares = $this->db->query("SELECT `t1`.`member_id`, `t1`.`share_date`, `t1`.`share_period`, `t1`.`share_collect_value`, `t1`.`share_collect`
									FROM `coop_mem_share` as `t1`
									INNER JOIN (SELECT member_id, share_date, MAX(cast(share_date as Datetime)) as max FROM coop_mem_share WHERE share_date BETWEEN '".$start_date." 00:00:00' AND '".$end_date." 23:59:59' group by member_id)
											as t2 ON `t1`.`member_id` = `t2`.`member_id` AND `t1`.`share_date` = `t2`.`max`
									WHERE `t1`.`share_collect_value` > 0 AND t1.member_id IN  (".implode(',',$member_ids).")
									")->result_array();
		$share_members = array_column($shares, 'member_id');

		//Get Lastest Loan Information
		$loan_ids = array_column($rs, 'loan_id');
        $where_loan = " 1=1 ";
        if(sizeof(array_filter($loan_ids))){
            $where_loan = " t1.loan_id IN  (".implode(',',array_filter($loan_ids)).") ";
        }
		$loans = $this->db->query("SELECT `t1`.`loan_transaction_id`, `t1`.`loan_id`, `t1`.`loan_amount_balance`, `t1`.`transaction_datetime`
									FROM `coop_loan_transaction` as `t1`
									INNER JOIN (SELECT loan_id, MAX(cast(transaction_datetime as Datetime)) as max FROM coop_loan_transaction WHERE transaction_datetime BETWEEN '".$start_date." 00:00:00' AND '".$end_date." 23:59:59' group by loan_id)
											as t2 ON `t1`.`loan_id` = `t2`.`loan_id` AND `t1`.`transaction_datetime` = `t2`.`max`
									WHERE `t1`.`loan_amount_balance` > 0 AND {$where_loan}
									ORDER BY `t1`.`transaction_datetime`, `t1`.`loan_transaction_id` DESC
									")->result_array();
		$loan_members = array_column($loans, 'loan_id');

		//Get Lastest Loan ATM Information
		$loan_atm_ids = array_column($rs, 'loan_atm_id');

        $where_atm = " AND  1=1 ";
        if(sizeof(array_filter($loan_atm_ids))){
            $where_atm = " AND t1.loan_atm_id IN  (".implode(',',array_filter($loan_atm_ids)).") ";
        }

		$loan_atms = $this->db->query("SELECT t1.loan_atm_transaction_id, `t1`.`loan_atm_id`, `t1`.`loan_amount_balance`, `t1`.`transaction_datetime`
									FROM `coop_loan_atm_transaction` as `t1`
									INNER JOIN (SELECT loan_atm_id, MAX(cast(transaction_datetime as Datetime)) as max FROM coop_loan_atm_transaction WHERE transaction_datetime BETWEEN '".$start_date." 00:00:00' AND '".$end_date." 23:59:59' group by loan_atm_id)
											as t2 ON `t1`.`loan_atm_id` = `t2`.`loan_atm_id` AND `t1`.`transaction_datetime` = `t2`.`max`
											WHERE `t1`.`loan_amount_balance` > 0 {$where_atm}
									ORDER BY `t1`.`transaction_datetime`, `t1`.`loan_atm_transaction_id` DESC
									")->result_array();
		$loan_atm_members = array_column($loan_atms, 'loan_atm_id');

		$run_index = 0;
		$row = array();
		
		$check_row = "xx";
		$index = 0;
		foreach($rs AS $key2=>$value2){
			if($check_row != @$value2['member_id']){				
				$check_row = @$value2['member_id'];
				$share_period = in_array($value2['member_id'],$share_members) ? $shares[array_search($value2['member_id'],$share_members)]['share_period'] : "";
				$share_collect_value = in_array($value2['member_id'],$share_members) ? $shares[array_search($value2['member_id'],$share_members)]['share_collect_value'] : "";
			}

			$row['data'][$value2['member_id']]['member_id'] = $value2['member_id'];
			$row['data'][$value2['member_id']]['mobile'] = $value2['mobile'];

			//หุ้น
			$row['data'][$value2['member_id']]['share_period'] = $share_period;
			$row['data'][$value2['member_id']]['share_collect'] = $share_collect_value;
			
			$loan_type_code = $arr_loan_type_code[$value2['loan_type']];
			if($loan_type_code == 'emergent' &&  in_array($value2['loan_id'],$loan_members)){
				$loan = array();
				$loan['loan_emergent_period_now'] = $value2['period_now'];
				$loan['loan_emergent_contract_number'] = $value2['contract_number'];
				$loan['loan_emergent_balance'] = in_array($value2['loan_id'],$loan_members) ? $loans[array_search($value2['loan_id'],$loan_members)]['loan_amount_balance'] : "";
				$row['data'][$value2['member_id']]['emergent'][$value2['contract_number']] = $loan;
				$run_emergent++;
			}

			if($loan_type_code == 'normal' && in_array($value2['loan_id'],$loan_members)){
				$loan = array();
				$loan['loan_emergent_period_now'] = $value2['period_now'];
				$loan['loan_emergent_contract_number'] = $value2['contract_number'];
				$loan['loan_emergent_balance'] = in_array($value2['loan_id'],$loan_members) ? $loans[array_search($value2['loan_id'],$loan_members)]['loan_amount_balance'] : "";
				$row['data'][$value2['member_id']]['normal'][$value2['contract_number']] = $loan;
				$run_normal++;
			}
			
			if($loan_type_code == 'special' && in_array($value2['loan_id'],$loan_members)){
				$loan = array();
				$loan['loan_emergent_period_now'] = $value2['period_now'];
				$loan['loan_emergent_contract_number'] = $value2['contract_number'];
				$loan['loan_emergent_balance'] = in_array($value2['loan_id'],$loan_members) ? $loans[array_search($value2['loan_id'],$loan_members)]['loan_amount_balance'] : "";
				$row['data'][$value2['member_id']]['special'][$value2['contract_number']] = $loan;
				$run_special++;
			}
			//เงินกู้ฉุกเฉิน ATM
			if(@$value2['loan_amount_balance_atm'] != '' && in_array($value2['loan_atm_id'],$loan_atm_members)){
				$loan = array();
				$loan['loan_emergent_period_now'] = '';
				$loan['loan_emergent_contract_number'] = $value2['contract_number_atm'];
				$loan['loan_emergent_balance'] = in_array($value2['loan_atm_id'],$loan_atm_members) ? $loan_atms[array_search($value2['loan_atm_id'],$loan_atm_members)]['loan_amount_balance'] : "";
				$row['data'][$value2['member_id']]['emergent'][$value2['contract_number_atm']] = $loan;
			}

			$run_index++;		

		}

		$arr_data['num_rows'] = $row['num_rows'];
		$arr_data['paging'] = $paging;
		$arr_data['datas'] = $row['data'];
		$arr_data['i'] = $i;
		
		$this->db->select(array('id','loan_type','loan_type_code'));
		$this->db->from('coop_loan_type');
		$this->db->order_by("order_by");
		$row = $this->db->get()->result_array();
		$arr_data['loan_type'] = $row;
		
		$arr_data['month_short_arr'] = $this->center_function->month_short_arr();

		if($_GET['sms_file_type'] == "csv") {
			$this->load->view('report_share_data/coop_report_share_loan_balance_sms_person_csv',$arr_data);
		} else {
			$this->load->view('report_share_data/coop_report_share_loan_balance_sms_person_excel',$arr_data);
		}
	}

	///รายงานซื้อหุ้น 
	public function coop_report_buy_share(){
		$arr_data = array();
		
		$this->db->select(array('id','mem_group_name'));
		$this->db->from('coop_mem_group');
		$this->db->where("mem_group_type = '1'");
		$row = $this->db->get()->result_array();
		$arr_data['row_mem_group'] = $row;
		
		$this->libraries->template('report_share_data/coop_report_buy_share',$arr_data);
	}
	
	function coop_report_buy_share_preview(){
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
			
			$start_date = $get_start_date;
			$end_date = $get_start_date;
		}

		if(@$_GET['end_date']){
			$end_date_arr = explode('/',@$_GET['end_date']);
			$end_day = $end_date_arr[0];
			$end_month = $end_date_arr[1];
			$end_year = $end_date_arr[2];
			$end_year -= 543;
			$get_end_date = $end_year.'-'.$end_month.'-'.$end_day;

			$end_date = $get_end_date;
		}		

		$where = " AND t1.share_status IN ('1','2')  AND share_type = 'SPA'";		
		if(@$get_start_date != ''){
			$where .= " AND t1.share_date BETWEEN '".$start_date." 00:00:00' AND '".$end_date." 23:59:59'";
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
		$x++;
		$join_arr[$x]['table'] = 'coop_mem_apply AS t4';
		$join_arr[$x]['condition'] = 't4.member_id = t1.member_id';
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
		
		$x++;
		$join_arr[$x]['table'] = 'coop_mem_apply_type AS t9';
		$join_arr[$x]['condition'] = 't9.apply_type_id = t4.apply_type_id';
		$join_arr[$x]['type'] = 'left';
		
		$x++;
		$join_arr[$x]['table'] = 'coop_mem_type AS t10';
		$join_arr[$x]['condition'] = 't10.mem_type_id = t4.mem_type_id';
		$join_arr[$x]['type'] = 'left';
		
		$this->paginater_all_preview->type(DB_TYPE);
		$this->paginater_all_preview->select(array(
												
												't1.member_id'
												,'t1.share_date'
												//,'(SELECT share_early FROM coop_mem_share AS t2 WHERE t2.member_id = t1.member_id ORDER BY t2.share_id DESC LIMIT 1) AS share_early'
												//,'(SELECT share_early_value FROM coop_mem_share AS t2 WHERE t2.member_id = t1.member_id ORDER BY t2.share_id DESC LIMIT 1) AS share_early_value'
												//,'(SELECT share_bill FROM coop_mem_share AS t2 WHERE t2.member_id = t1.member_id ORDER BY t2.share_id DESC LIMIT 1) AS share_bill'
												//,'(SELECT pay_type FROM coop_mem_share AS t2 WHERE t2.member_id = t1.member_id ORDER BY t2.share_id DESC LIMIT 1) AS pay_type'
												,'t1.share_early'
												,'t1.share_early_value'
												,'t1.share_bill'
												,'t1.pay_type'
												,'t4.firstname_th'
											    ,'t4.lastname_th'
												,'t4.department'
												,'t4.faction'
												,'t4.level'
												,'t9.apply_type_name'
												,'t10.mem_type_name'
												,'coop_prename.prename_short'
												,'t6.mem_group_id as mem_group_id'
												,'t6.mem_group_name as mem_group_name'
												,'t7.mem_group_name as sub_name'
												,'t8.mem_group_name as main_name'
												));
		$this->paginater_all_preview->main_table('coop_mem_share AS t1');
		$this->paginater_all_preview->where(" 1=1 {$where}");
		$this->paginater_all_preview->page_now(@$_GET["page"]);
		$this->paginater_all_preview->per_page(15);
		$this->paginater_all_preview->page_link_limit(18);
		$this->paginater_all_preview->page_limit_first(15);
		//$this->paginater_all_preview->group_by('t1.member_id');
		//$this->paginater_all_preview->order_by("IF(SUBSTR(t1.member_id,1,2) = '98',CONCAT('00',t1.member_id),IF(SUBSTR(t1.member_id,1,2) = '99',CONCAT('01',t1.member_id),CONCAT('02',t1.member_id))) ASC,t1.share_date DESC");
		$this->paginater_all_preview->order_by("t1.share_date ASC");
		$this->paginater_all_preview->join_arr($join_arr);
		$row = $this->paginater_all_preview->paginater_process();
		
		if(@$_GET['dev']=='dev'){
			print_r($this->db->last_query()); exit;
		}
		
		foreach($row['data'] AS $key=>$value){
			foreach($value AS $key2=>$value2){
				if($value2['sub_name'] == '' || $value2['sub_name']=='ไม่ระบุ'){					
					$row['data'][$key][$key2]['sub_name'] = $value2['main_name'];
				}else{					
					$row['data'][$key][$key2]['sub_name'] = $value2['sub_name'];
				}
			}
		}
		// echo "<pre>"; print_r($row['data']); exit;
		//exit;
		$arr_data['num_rows'] = $row['num_rows'];
		$arr_data['paging'] = $paging;
		$arr_data['data'] = $row['data'];
		$arr_data['page_all'] = $row['page_all'];
		$arr_data['arr_pay_type'] = array('0'=>'เงินสด','1'=>'โอนเงิน');
		
		$this->preview_libraries->template_preview('report_share_data/coop_report_buy_share_preview',$arr_data);
		
	}	

	function check_report_buy_share(){
		if(@$_POST['start_date']){
			$start_date_arr = explode('/',@$_POST['start_date']);
			$start_day = $start_date_arr[0];
			$start_month = $start_date_arr[1];
			$start_year = $start_date_arr[2];
			$start_year -= 543;
			$get_start_date = $start_year.'-'.$start_month.'-'.$start_day;
			
			$start_date = $get_start_date;
			$end_date = $get_start_date;
		}
		
		if(@$_POST['end_date']){
			$end_date_arr = explode('/',@$_POST['end_date']);
			$end_day = $end_date_arr[0];
			$end_month = $end_date_arr[1];
			$end_year = $end_date_arr[2];
			$end_year -= 543;
			$get_end_date = $end_year.'-'.$end_month.'-'.$end_day;

			$end_date = $get_end_date;
		}	

		$where = " AND t1.share_status IN ('1','2') ";		
		if(@$get_start_date != ''){
			$where .= " AND t1.share_date BETWEEN '".$start_date." 00:00:00' AND '".$end_date." 23:59:59'";
		}
		
		if(@$_POST['level'] != ''){
			$where .= " AND t4.level = '".@$_POST['level']."'";
		}else if(@$_POST['faction'] != ''){
			$where .= " AND t4.faction = '".@$_POST['faction']."'";
		}else if(@$_POST['department'] != ''){
			$where .= " AND t4.department = '".@$_POST['department']."'";
		}
		
		$this->db->select(array(
								'COUNT(`t1`.`member_id`) as count_member_id'
								));
		$this->db->from('coop_mem_share AS t1');
		$this->db->join("coop_mem_apply AS t4","t4.member_id = t1.member_id","inner");
		$this->db->where(" 1=1 {$where}");
		$rs_count = $this->db->get()->result_array();
		//echo $where; exit;
		if(!empty($rs_count[0]['count_member_id'])){
			echo "success";
		}else{
			echo "";
		}		
	}	
	
	function coop_report_buy_share_excel(){
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
			
			$start_date = $get_start_date;
			$end_date = $get_start_date;
		}	
		
		if(@$_GET['end_date']){
			$end_date_arr = explode('/',@$_GET['end_date']);
			$end_day = $end_date_arr[0];
			$end_month = $end_date_arr[1];
			$end_year = $end_date_arr[2];
			$end_year -= 543;
			$get_end_date = $end_year.'-'.$end_month.'-'.$end_day;

			$end_date = $get_end_date;
		}

		$where = " AND t1.share_status IN ('1','2')  AND share_type = 'SPA'";		
		if(@$get_start_date != ''){
			$where .= " AND t1.share_date BETWEEN '".$start_date." 00:00:00' AND '".$end_date." 23:59:59'";
		}
		
		if(@$_GET['level'] != ''){
			$where .= " AND t4.level = '".@$_GET['level']."'";
		}else if(@$_GET['faction'] != ''){
			$where .= " AND t4.faction = '".@$_GET['faction']."'";
		}else if(@$_GET['department'] != ''){
			$where .= " AND t4.department = '".@$_GET['department']."'";
		}	
			
		$row['data'] = $this->db->select(array(
							't1.member_id'
							,'t1.share_date'
							//,'(SELECT share_early FROM coop_mem_share AS t2 WHERE t2.member_id = t1.member_id ORDER BY t2.share_id DESC LIMIT 1) AS share_early'
							//,'(SELECT share_early_value FROM coop_mem_share AS t2 WHERE t2.member_id = t1.member_id ORDER BY t2.share_id DESC LIMIT 1) AS share_early_value'
							//,'(SELECT share_bill FROM coop_mem_share AS t2 WHERE t2.member_id = t1.member_id ORDER BY t2.share_id DESC LIMIT 1) AS share_bill'
							//,'(SELECT pay_type FROM coop_mem_share AS t2 WHERE t2.member_id = t1.member_id ORDER BY t2.share_id DESC LIMIT 1) AS pay_type'
							,'t1.share_early'
							,'t1.share_early_value'
							,'t1.share_bill'
							,'t1.pay_type'
							,'t4.firstname_th'
							,'t4.lastname_th'
							,'t4.department'
							,'t4.faction'
							,'t4.level'
							,'t9.apply_type_name'
							,'t10.mem_type_name'
							,'coop_prename.prename_short'
							,'t6.mem_group_id as mem_group_id'
							,'t6.mem_group_name as mem_group_name'
							,'t7.mem_group_name as sub_name'
							,'t8.mem_group_name as main_name'
							))
		->from("coop_mem_share AS t1")		
		->join("coop_mem_apply AS t4","t4.member_id = t1.member_id","inner")		
		->join("coop_prename","coop_prename.prename_id = t4.prename_id","left")
		->join("coop_mem_group as t6","t6.id = t4.level","inner")
		->join("coop_mem_group as t7","t7.id = t6.mem_group_parent_id","left")
		->join("coop_mem_group as t8","t8.id = t7.mem_group_parent_id","left")		
		->join("coop_mem_apply_type AS t9","t9.apply_type_id = t4.apply_type_id","left")		
		->join("coop_mem_type AS t10","t10.mem_type_id = t4.mem_type_id","left")			
		->where("1=1 {$where}")
		//->group_by('t1.member_id')
		//->order_by("t1.member_id,t1.share_date DESC ,t1.share_id DESC")
		->order_by("t1.share_date ASC")
		->get()->result_array();
		
		
		//echo '<pre>'; print_r($row['data']); echo '</pre>'; exit;
		foreach($row['data'] AS $key=>$value){
			if($value['sub_name'] == '' || $value['sub_name']=='ไม่ระบุ'){					
				$row['data'][$key]['sub_name'] = $value['main_name'];
			}else{					
				$row['data'][$key]['sub_name'] = $value['sub_name'];
			}
		}
		
		$arr_data['paging'] = $paging;
		$arr_data['data'] = $row['data'];	
		$arr_data['arr_pay_type'] = array('0'=>'เงินสด','1'=>'โอนเงิน');
		$this->load->view('report_share_data/coop_report_buy_share_excel',$arr_data);
		
	}

	public function coop_report_share_transaction(){
		$arr_data = array();

		if($_GET['member_id']!=''){
			$member_id = $_GET['member_id'];
		}else{
			$member_id = '';
		}
		$arr_data['member_id'] = $member_id;

		$member_name = '';
		if($member_id != '') {
			$member_info = $this->db->select(array('t1.member_id', 't1.firstname_th', 't1.lastname_th', 't2.prename_full'))
									->from('coop_mem_apply as t1')
									->join("coop_prename as t2","t2.prename_id = t1.prename_id","left")
									->where("t1.member_id = '{$member_id}'")
									->get()->row();
			$member_name = $member_info->prename_full.$member_info->firstname_th." ".$member_info->lastname_th;
		}

		$arr_data['member_name'] = $member_name;

		$this->libraries->template('report_share_data/coop_report_share_transaction',$arr_data);
	}

	public function coop_report_share_transaction_preview() {
		$arr_data = array();

		$dateCon = "";
		if(@$_GET['start_date'] && $_GET['type'] == '2'){
			$start_date_arr = explode('/',@$_GET['start_date']);
			$start_day = $start_date_arr[0];
			$start_month = $start_date_arr[1];
			$start_year = $start_date_arr[2];
			$start_year -= 543;
			$start_date = $start_year.'-'.$start_month.'-'.$start_day;
			$dateCon .= "share_date >= '".$start_date." 00:00:00' AND ";
		}

		if(@$_GET['end_date']){
			$end_date_arr = explode('/',@$_GET['end_date']);
			$end_day = $end_date_arr[0];
			$end_month = $end_date_arr[1];
			$end_year = $end_date_arr[2];
			$end_year -= 543;
			$end_date = $end_year.'-'.$end_month.'-'.$end_day;
			$dateCon .= "share_date <= '".$end_date." 23:59:59'";
		}

		$memberInfo = $this->db->select("firstname_th, lastname_th, prename_full")
								->from("coop_mem_apply")
								->join("coop_prename", "coop_prename.prename_id = coop_mem_apply.prename_id","left")
								->where("member_id = '".$_GET['member_id']."'")
								->get()->row();

		$member_name = " - ";
		if (!empty($memberInfo)) {
			$member_name = $memberInfo->prename_full.$memberInfo->firstname_th." ".$memberInfo->lastname_th;
		}

		$shares = $this->db->select("t1.member_id, t1.admin_id, t1.share_type, t1.share_status, t1.share_bill, t1.share_date, t1.share_payable, t1.share_payable_value,
										t1.share_early, t1.share_early_value, t1.share_collect, t1.share_collect_value, t2.user_name")
							->from('coop_mem_share as t1')
							->where("member_id = '".$_GET['member_id']."' AND ".$dateCon)
							->join("coop_user as t2","t1.admin_id = t2.user_id","left")
							->order_by('share_date ASC, share_id ASC')
							->get()->result_array();
		if(@$_GET['dev'] == 'dev'){
			echo $this->db->last_query(); 
			echo '<hr>';
		}
		$shareCount = count($shares);
		$firstPage = 20;
		$perPage = 30;
		
		$total = 0;
		$row = array();
		$page = 0;
		$duplicate_index = 0;
		
		$share_tran_data = array();
		if(!empty($shares)){	
			foreach($shares as $index=> $raw) {
				if (($index + $duplicate_index - 1) <= $firstPage) {
					$page = 1;
				} else {
					$page = ceil(($index + $duplicate_index - $firstPage -1)/$perPage)+1;
				}
				
				if($raw['share_status'] == '1' || $raw['share_status'] == '5') {
					$tran = array();
					$tran['member_id'] = $raw['member_id'];
					$tran['share_date'] = $raw['share_date'];
					$tran['share_type'] = $raw['share_type'];
					$tran['share_early_value'] = $raw['share_early_value'];
					$tran['share_early'] = $raw['share_early'];
					$tran['share_collect_value'] = $raw['share_collect_value'];
					$tran['share_collect'] = $raw['share_collect'];
					$tran['share_bill'] = $raw['share_bill'];
					$tran['share_type'] = $raw['share_type'];
					$tran['user_name'] = $raw['user_name'];
					$share_tran_data[$page][] = $tran;
				} else if ($raw['share_status'] == '3') {
					//Add month deduct
					$tran = array();
					$tran['member_id'] = $raw['member_id'];
					$tran['share_date'] = $raw['share_date'];
					$tran['share_type'] = $raw['share_type'];
					$tran['share_early_value'] = $raw['share_early_value'];
					$tran['share_early'] = $raw['share_early'];
					$tran['share_collect_value'] = $raw['share_collect_value'];
					$tran['share_collect'] = $raw['share_collect'];
					$tran['share_bill'] = $raw['share_bill'];
					$tran['share_type'] = $raw['share_type'];
					$tran['user_name'] = $raw['user_name'];
					$share_tran_data[$page][] = $tran;

					//Add non pay
					$month = date('m',strtotime($raw['share_date']));
					$year = date('Y',strtotime($raw['share_date'])) + 543;
					$non_pay = $this->db->select("t2.non_pay_amount")
										->from("coop_non_pay as t1")
										->join("coop_non_pay_detail as t2", "t1.non_pay_id = t2.non_pay_id AND t2.deduct_code = 'SHARE'", "inner")
										->where("t1.non_pay_month = '".(int)$month."' AND t1.non_pay_year = '".$year."' AND t1.member_id = '".$raw['member_id']."'")
										->get()->row();
					if(@$_GET['dev']=='dev'){
					echo '<hr>';	
					echo $this->db->last_query();
					}					
					if(!empty($non_pay)) {
						$tran = array();
						$tran['member_id'] = $raw['member_id'];
						$tran['share_date'] = $raw['share_date'];
						$tran['share_type'] = "ERR";
						$tran['share_early_value'] = "-".$raw['share_early_value'];
						$tran['share_early'] = $raw['share_early'];
						$tran['share_collect_value'] = $raw['share_collect_value'] - $raw['share_early_value'];
						$tran['share_collect'] = $raw['share_collect'] - $raw['share_early'];
						$tran['share_bill'] = $raw['share_bill'];
						$tran['user_name'] = (@$raw['user_name_cancel'] != '')?@$raw['user_name_cancel']:@$raw['user_name'];
						$share_tran_data[$page][] = $tran;
					} else {
						$tran = array();
						$tran['member_id'] = $raw['member_id'];
						$tran['share_date'] = $raw['share_date'];
						$tran['share_type'] = "ERR";
						$tran['share_early_value'] = "-".$raw['share_early_value'];
						$tran['share_early'] = $raw['share_early'];
						$tran['share_collect_value'] = $raw['share_collect_value'] - $raw['share_early_value'];
						$tran['share_collect'] = $raw['share_collect'] - $raw['share_early'];
						$tran['share_bill'] = $raw['share_bill'];
						$tran['user_name'] = (@$raw['user_name_cancel'] != '')?@$raw['user_name_cancel']:@$raw['user_name'];
						$share_tran_data[$page][] = $tran;
					}					
				}
			}
		}	
		$row['data'] = $share_tran_data;
		
		
		$total = $this->db->select(array('share_collect_value'))->from('coop_mem_share')
					->where("member_id = '".$_GET['member_id']."' AND share_status IN('1','2','5')")
					->order_by('share_date DESC,share_id DESC')->limit(1)
					->get()->row_array()['share_collect_value'];
		
		$page_all = $shareCount <= $firstPage ? 1 : ceil(($shareCount - $firstPage)/$perPage)+1;

		$arr_data['data'] = $row['data'];
		$arr_data['start_date'] = $start_date;
		$arr_data['end_date'] = $end_date;
		$arr_data['page_all'] = $page_all;
		$arr_data['total'] = $total;
		$arr_data['member_name'] = $member_name;
		
		//ประเภทหุ้น
		$this->db->select('share_type_code, share_type_name');
		$this->db->from('coop_share_type');
		$this->db->where("share_type_status ='1'");
		$rs_share_type = $this->db->get()->result_array();
		$arr_share_type = array();
		foreach($rs_share_type AS $key=>$row_share_type){
			$arr_share_type[$row_share_type['share_type_code']] = $row_share_type['share_type_name'];
		}
		$arr_data['share_type'] = @$arr_share_type;
		$this->preview_libraries->template_preview('report_share_data/coop_report_share_transaction_preview',$arr_data);
	}

	public function coop_report_share_transaction_excel() {
		$arr_data = array();

		$dateCon = "";
		if(@$_GET['start_date'] && $_GET['type'] == '2'){
			$start_date_arr = explode('/',@$_GET['start_date']);
			$start_day = $start_date_arr[0];
			$start_month = $start_date_arr[1];
			$start_year = $start_date_arr[2];
			$start_year -= 543;
			$start_date = $start_year.'-'.$start_month.'-'.$start_day;
			$dateCon .= "share_date >= '".$start_date." 00:00:00' AND ";
		}

		if(@$_GET['end_date']){
			$end_date_arr = explode('/',@$_GET['end_date']);
			$end_day = $end_date_arr[0];
			$end_month = $end_date_arr[1];
			$end_year = $end_date_arr[2];
			$end_year -= 543;
			$end_date = $end_year.'-'.$end_month.'-'.$end_day;
			$dateCon .= "share_date <= '".$end_date." 23:59:59'";
		}

		$memberInfo = $this->db->select("firstname_th, lastname_th, prename_full")
								->from("coop_mem_apply")
								->join("coop_prename", "coop_prename.prename_id = coop_mem_apply.prename_id","left")
								->where("member_id = '".$_GET['member_id']."'")
								->get()->row();

		$member_name = " - ";
		if (!empty($memberInfo)) {
			$member_name = $memberInfo->prename_full.$memberInfo->firstname_th." ".$memberInfo->lastname_th;
		}

		$shares = $this->db->select("t1.member_id, t1.admin_id, t1.share_type, t1.share_status, t1.share_bill, t1.share_date, t1.share_payable, t1.share_payable_value,
										t1.share_early, t1.share_early_value, t1.share_collect, t1.share_collect_value, t2.user_name")
							->from('coop_mem_share as t1')
							->where("member_id = '".$_GET['member_id']."' AND ".$dateCon)
							->join("coop_user as t2","t1.admin_id = t2.user_id","left")
							->order_by('share_date, share_id')
							->get()->result_array();

		$shareCount = count($shares);
		$firstPage = 20;
		$perPage = 30;
		
		$total = 0;
		$row = array();
		$page = 0;
		$duplicate_index = 0;
		
		/*foreach($shares as $index => $share) {
			if (($index + $duplicate_index - 1) <= $firstPage) {
				$page = 1;
			} else {
				$page = ceil(($index + $duplicate_index - $firstPage -1)/$perPage)+1;
			}
			$data = $share;
			$data['operation'] = "add";
			if ($share['share_type'] == 'SPM' && ($share['share_status'] == '1' || $share['share_status'] == '2')) {
				$data['status'] = "หักส่งรายเดือน";
				$row['data'][$page][] = $data;
			} else if ($share['share_type'] == 'SPM' && $share['share_status'] == '3') {
				$data['status'] = "หักส่งรายเดือน";
				$data['share_collect'] = $data['share_early'] + $data['share_payable'];
				$data['share_collect_value'] = $data['share_early_value'] + $data['share_payable_value'];
				$row['data'][$page][] = $data;
				$cancelData = $share;
				$cancelData['status'] = "ยกเลิกรายการ";
				$cancelData['operation'] = "subtract";
				$cancelData['share_collect'] = $data['share_collect'] - $data['share_early'];
				$cancelData['share_collect_value'] = $data['share_collect_value'] - $data['share_early_value'];
				$row['data'][$page][] = $cancelData;
				$duplicate_index++;
			} else if ($share['share_type'] == 'SPA' && ($share['share_status'] == '1' || $share['share_status'] == '2')) {
				$data['status'] = "ซื้อพิเศษ";
				$row['data'][$page][] = $data;
			} else if ($share['share_type'] == 'SPA' && $share['share_status'] == '3') {
				$data['status'] = "ซื้อพิเศษ";
				$data['share_collect'] = $data['share_early'] + $data['share_payable'];
				$data['share_collect_value'] = $data['share_early_value'] + $data['share_payable_value'];
				$row['data'][$page][] = $data;
				$cancelData = $share;
				$cancelData['status'] = "ยกเลิกรายการ";
				$cancelData['operation'] = "subtract";
				$cancelData['share_collect'] = $data['share_collect'] - $data['share_early'];
				$cancelData['share_collect_value'] = $data['share_collect_value'] - $data['share_early_value'];
				$row['data'][$page][] = $cancelData;
				$duplicate_index++;
			} else if ($share['share_type'] == 'SPL' && ($share['share_status'] == '1' || $share['share_status'] == '2')) {
				$data['status'] = "ซื้อจากการกู้";
				$row['data'][$page][] = $data;
			} else if ($share['share_type'] == 'SPL' && $share['share_status'] == '3') {
				$data['status'] = "ซื้อจากการกู้";
				$data['share_collect'] = $data['share_early'] + $data['share_payable'];
				$data['share_collect_value'] = $data['share_early_value'] + $data['share_payable_value'];
				$row['data'][$page][] = $data;
				$cancelData = $share;
				$cancelData['status'] = "ยกเลิกรายการ";
				$cancelData['operation'] = "subtract";
				$cancelData['share_collect'] = $data['share_collect'] - $data['share_early'];
				$cancelData['share_collect_value'] = $data['share_collect_value'] - $data['share_early_value'];
				$row['data'][$page][] = $cancelData;
				$duplicate_index++;
			} else if ($share['share_type'] == 'SRF') {
				$data['status'] = "คืนเงิน";
				$data['operation'] = "subtract";
				$row['data'][$page][] = $data;
			} else if ($share['share_type'] == 'SRP') {
				$data['status'] = "ถอนหุ้น";
				$data['operation'] = "subtract";
				$row['data'][$page][] = $data;
			} else if ($share['share_type'] == 'SPD') {
				$data['status'] = "ยกเลิกรายการ";
				$data['operation'] = "subtract";
				$row['data'][$page][] = $data;
			} else if ($share['share_type'] == 'SDP') {
				$data['status'] = "ชำระหนี้คงค้าง";
				$row['data'][$page][] = $data;
			}

			if (($shareCount-1) == $index) {
				$total = $share['share_collect_value'];
			}			
		}
		*/
		
		$share_tran_data = array();
		if(!empty($shares)){	
			foreach($shares as $index=> $raw) {
				if (($index + $duplicate_index - 1) <= $firstPage) {
					$page = 1;
				} else {
					$page = ceil(($index + $duplicate_index - $firstPage -1)/$perPage)+1;
				}
				
				if($raw['share_status'] == '1' || $raw['share_status'] == '5') {
					$tran = array();
					$tran['member_id'] = $raw['member_id'];
					$tran['share_date'] = $raw['share_date'];
					$tran['share_type'] = $raw['share_type'];
					$tran['share_early_value'] = $raw['share_early_value'];
					$tran['share_early'] = $raw['share_early'];
					$tran['share_collect_value'] = $raw['share_collect_value'];
					$tran['share_collect'] = $raw['share_collect'];
					$tran['share_bill'] = $raw['share_bill'];
					$tran['share_type'] = $raw['share_type'];
					$tran['user_name'] = $raw['user_name'];
					$share_tran_data[$page][] = $tran;
				} else if ($raw['share_status'] == '3') {
					//Add month deduct
					$tran = array();
					$tran['member_id'] = $raw['member_id'];
					$tran['share_date'] = $raw['share_date'];
					$tran['share_type'] = $raw['share_type'];
					$tran['share_early_value'] = $raw['share_early_value'];
					$tran['share_early'] = $raw['share_early'];
					$tran['share_collect_value'] = $raw['share_collect_value'];
					$tran['share_collect'] = $raw['share_collect'];
					$tran['share_bill'] = $raw['share_bill'];
					$tran['share_type'] = $raw['share_type'];
					$tran['user_name'] = $raw['user_name'];
					$share_tran_data[$page][] = $tran;

					//Add non pay
					$month = date('m',strtotime($raw['share_date']));
					$year = date('Y',strtotime($raw['share_date'])) + 543;
					$non_pay = $this->db->select("t2.non_pay_amount")
										->from("coop_non_pay as t1")
										->join("coop_non_pay_detail as t2", "t1.non_pay_id = t2.non_pay_id AND t2.deduct_code = 'SHARE'", "inner")
										->where("t1.non_pay_month = '".(int)$month."' AND t1.non_pay_year = '".$year."' AND t1.member_id = '".$raw['member_id']."'")
										->get()->row();
					if(@$_GET['dev']=='dev'){
					echo '<hr>';	
					echo $this->db->last_query();
					}					
					if(!empty($non_pay)) {
						$tran = array();
						$tran['member_id'] = $raw['member_id'];
						$tran['share_date'] = $raw['share_date'];
						$tran['share_type'] = "ERR";
						$tran['share_early_value'] = "-".$raw['share_early_value'];
						$tran['share_early'] = $raw['share_early'];
						$tran['share_collect_value'] = $raw['share_collect_value'] - $raw['share_early_value'];
						$tran['share_collect'] = $raw['share_collect'] - $raw['share_early'];
						$tran['share_bill'] = $raw['share_bill'];
						$tran['user_name'] = (@$raw['user_name_cancel'] != '')?@$raw['user_name_cancel']:@$raw['user_name'];
						$share_tran_data[$page][] = $tran;
					} else {
						$tran = array();
						$tran['member_id'] = $raw['member_id'];
						$tran['share_date'] = $raw['share_date'];
						$tran['share_type'] = "ERR";
						$tran['share_early_value'] = "-".$raw['share_early_value'];
						$tran['share_early'] = $raw['share_early'];
						$tran['share_collect_value'] = $raw['share_collect_value'] - $raw['share_early_value'];
						$tran['share_collect'] = $raw['share_collect'] - $raw['share_early'];
						$tran['share_bill'] = $raw['share_bill'];
						$tran['user_name'] = (@$raw['user_name_cancel'] != '')?@$raw['user_name_cancel']:@$raw['user_name'];
						$share_tran_data[$page][] = $tran;
					}					
				}
			}
		}	
		$row['data'] = $share_tran_data;
		
		
		$total = $this->db->select(array('share_collect_value'))->from('coop_mem_share')
					->where("member_id = '".$_GET['member_id']."' AND share_status IN('1','2','5')")
					->order_by('share_date DESC,share_id DESC')->limit(1)
					->get()->row_array()['share_collect_value'];

		$page_all = $shareCount <= $firstPage ? 1 : ceil(($shareCount - $firstPage)/$perPage)+1;

		$arr_data['data'] = $row['data'];
		$arr_data['start_date'] = $start_date;
		$arr_data['end_date'] = $end_date;
		$arr_data['page_all'] = $page_all;
		$arr_data['total'] = $total;
		$arr_data['member_name'] = $member_name;
		
		//ประเภทหุ้น
		$this->db->select('share_type_code, share_type_name');
		$this->db->from('coop_share_type');
		$this->db->where("share_type_status ='1'");
		$rs_share_type = $this->db->get()->result_array();
		$arr_share_type = array();
		foreach($rs_share_type AS $key=>$row_share_type){
			$arr_share_type[$row_share_type['share_type_code']] = $row_share_type['share_type_name'];
		}
		$arr_data['share_type'] = @$arr_share_type;
		$this->load->view('report_share_data/coop_report_share_transaction_excel',$arr_data);
	}

	public function check_coop_report_share_transaction() {
		$dateCon = "";
		if(@$_GET['start_date'] && $_GET['type'] == '2'){
			$start_date_arr = explode('/',@$_GET['start_date']);
			$start_day = $start_date_arr[0];
			$start_month = $start_date_arr[1];
			$start_year = $start_date_arr[2];
			$start_year -= 543;
			$start_date = $start_year.'-'.$start_month.'-'.$start_day;
			$dateCon .= "share_date >= '".$start_date." 00:00:00' AND ";
		}

		if(@$_GET['end_date']){
			$end_date_arr = explode('/',@$_GET['end_date']);
			$end_day = $end_date_arr[0];
			$end_month = $end_date_arr[1];
			$end_year = $end_date_arr[2];
			$end_year -= 543;
			$end_date = $end_year.'-'.$end_month.'-'.$end_day;
			$dateCon .= "share_date <= '".$end_date." 23:59:59'";
		}

		$shares = $this->db->select("t1.member_id, t1.admin_id, t1.share_type, t1.share_status, t1.share_bill, t1.share_date, t1.share_payable, t1.share_payable_value,
										t1.share_early, t1.share_early_value, t1.share_collect, t1.share_collect_value, t2.user_name")
							->from('coop_mem_share as t1')
							->where("member_id = '".$_GET['member_id']."' AND ".$dateCon)
							->join("coop_user as t2","t1.admin_id = t2.user_id","left")
							->order_by('share_date, share_id')
							->get()->result_array();
		if(!empty($shares)){
			echo "success";
		}else{
			echo "";
		}	
	}

	public function coop_report_loan_share_balance(){
		$arr_data = array();

		$this->db->select(array('id','mem_group_name'));
		$this->db->from('coop_mem_group');
		$this->db->where("mem_group_type = '1'");
		$row = $this->db->get()->result_array();
		$arr_data['row_mem_group'] = $row;

		$arr_data['month_arr'] = $this->center_function->month_arr();
		$arr_data['month_short_arr'] = $this->center_function->month_short_arr();

		$this->libraries->template('report_share_data/coop_report_loan_share_balance',$arr_data);
	}

	public function coop_report_loan_share_balance_loan_type_excel(){
		if($_GET["report_type"] == 1) {
			$datas = $this->get_loan_share_balance_data($_GET);
			$arr_data = $datas;

			$arr_data["from_date"] = "01 ".$this->center_function->month_arr()[$_GET["month"]]." ".$_GET["year"];
			$arr_data["thur_date"] = sprintf('%02d', date('t', strtotime(($_GET["year"] - 543)."-".($_GET["month"])."-01")))." ".$this->center_function->month_arr()[$_GET["month"]]." ".$_GET["year"];

			$this->preview_libraries->template_preview('report_share_data/coop_report_loan_share_balance_subdivision_excel',$arr_data);
		} else if($_GET["report_type"] == 2) {
			$datas = $this->get_loan_share_balance_person($_GET);
			$arr_data = $datas;

			$arr_data["from_date"] = "01 ".$this->center_function->month_arr()[$_GET["month"]]." ".$_GET["year"];
			$arr_data["thur_date"] = sprintf('%02d', date('t', strtotime(($_GET["year"] - 543)."-".($_GET["month"])."-01")))." ".$this->center_function->month_arr()[$_GET["month"]]." ".$_GET["year"];

			// echo "<pre>"; print_r($arr_data); exit;
			$this->preview_libraries->template_preview('report_share_data/coop_report_loan_share_balance_person_excel',$arr_data);
		}
	}

	public function get_loan_share_balance_data($param) {
		if(@$param['start_date']){
			$start_date_arr = explode('/',@$param['start_date']);
			$start_day = $start_date_arr[0];
			$start_month = $start_date_arr[1];
			$start_year = $start_date_arr[2];
			$start_year -= 543;
			$get_start_date = $start_year.'-'.$start_month.'-'.$start_day;
		}

		if(!empty($param["month"]) && !empty($param["year"])) {
			$get_start_date = date('Y-m-t', strtotime(($param["year"] - 543)."-".($param["month"])."-01"));
		}

		if(@$param['type_date'] == '1'){
			$this->db->select(array('share_date'));
			$this->db->from('coop_mem_share');
			$this->db->where("share_status IN ('1', '2') AND share_date != '0000-00-00 00:00:00'");
			$this->db->order_by("share_date ASC");
			$this->db->limit(1);
			$rs_date_share = $this->db->get()->result_array();
			$date_share_min  =  date("Y-m-d", strtotime(@$rs_date_share[0]['share_date']));

			$this->db->select(array('createdatetime'));
			$this->db->from('coop_loan');
			$this->db->where("loan_status = '1'");
			$this->db->order_by("createdatetime ASC");
			$this->db->limit(1);
			$rs_date_loan = $this->db->get()->result_array();
			$date_loan_min  =  date("Y-m-d", strtotime(@$rs_date_loan[0]['createdatetime']));

			$this->db->select(array('transaction_datetime'));
			$this->db->from('coop_loan_atm_transaction');
			$this->db->order_by("transaction_datetime ASC");
			$this->db->limit(1);
			$rs_date_loan_atm = $this->db->get()->result_array();
			$date_loan_atm_min  =  date("Y-m-d", strtotime(@$rs_date_loan_atm[0]['transaction_datetime']));

			if($date_share_min < $date_loan_min){
				$start_date = $date_share_min;
			}else if($date_loan_min < $date_loan_atm_min){
				$start_date = $date_loan_min;
			}else if($date_loan_atm_min < $date_share_min){
				$start_date = $date_loan_atm_min;
			}else{
				$start_date = $date_share_min;
			}
			$end_date = $get_start_date;
		}else{
			$start_date = $get_start_date;
			$end_date = $get_start_date;
		}

		$where_date = "";
		$where_date_loan = "";
		$where_date_loan_atm = "";
		$where_date_loan_atm_transaction = "";
		$where_date_loan_transaction = "";
		if(@$param['start_date'] != ''){
			$where_date .= " AND coop_mem_share.share_date BETWEEN '".$start_date." 00:00:00.000' AND '".$end_date." 23:59:59.000'";
			$where_date_loan .= " AND coop_loan.createdatetime BETWEEN '".$start_date." 00:00:00.000' AND '".$end_date." 23:59:59.000'";
			$where_date_loan_atm .= " AND coop_loan_atm.createdatetime BETWEEN '".$start_date." 00:00:00.000' AND '".$end_date." 23:59:59.000'";
			$where_date_loan_atm_transaction .= " AND coop_loan_atm_transaction.transaction_datetime BETWEEN '".$start_date." 00:00:00.000' AND '".$end_date." 23:59:59.000'";
			$where_date_loan_transaction .= " AND coop_loan_transaction.transaction_datetime BETWEEN '".$start_date." 00:00:00.000' AND '".$end_date." 23:59:59.000'";
		}

		$this->db->select(array('coop_loan_name.loan_name_id','coop_loan_type.loan_type_code'));
		$this->db->from('coop_loan_name');
		$this->db->join('coop_loan_type','coop_loan_name.loan_type_id = coop_loan_type.id','left');
		$rs_type_code = $this->db->get()->result_array();
		$arr_loan_type_code = array();
		$loan_type_datas = array();
		foreach($rs_type_code AS $key_type_code=>$row_type_code){
			$arr_loan_type_code[@$row_type_code['loan_name_id']] = @$row_type_code['loan_type_code'];
		}

		$sql = "SELECT `coop_mem_apply`.`member_id`, `coop_mem_apply`.`prename_id`, `coop_mem_apply`.`firstname_th`, `coop_mem_apply`.`lastname_th`, `coop_mem_apply`.`faction`, coop_mem_apply.sex,
					`t5`.`loan_id`, `t5`.`loan_amount_balance`, `t5`.`contract_number`, `t5`.`loan_type`, t5.prefix_code, t5.loan_amount
				FROM (SELECT IF (
								(SELECT faction_old FROM coop_mem_group_move WHERE date_move >= '".$end_date."' AND coop_mem_group_move.member_id = coop_mem_apply.member_id ORDER BY date_move ASC LIMIT 1),
								(SELECT faction_old FROM coop_mem_group_move WHERE date_move >= '".$end_date."' AND coop_mem_group_move.member_id = coop_mem_apply.member_id ORDER BY date_move ASC LIMIT 1),
								coop_mem_apply.faction
							) AS faction, member_id, prename_id, firstname_th, lastname_th,member_status, sex FROM coop_mem_apply) AS coop_mem_apply
				LEFT JOIN (SELECT t3.member_id ,t3.contract_number ,t3.period_now
					,t1.loan_transaction_id
					,t1.loan_id
					,t1.loan_amount_balance
					,t3.loan_amount
					,t1.transaction_datetime
					,t3.loan_type
					,t4.prefix_code FROM coop_loan_transaction AS t1
					LEFT JOIN coop_loan AS t3 ON t1.loan_id = t3.id
					LEFT JOIN coop_term_of_loan AS t4 ON t3.loan_type = t4.type_id
					WHERE t1.transaction_datetime BETWEEN '".$start_date." 00:00:00.000' AND '".$end_date." 23:59:59.000' AND t1.loan_amount_balance > 0 GROUP BY t1.loan_id ORDER BY t1.loan_id DESC ,t1.loan_transaction_id DESC )
					AS t5 ON `coop_mem_apply`.`member_id` = `t5`.`member_id`
				WHERE coop_mem_apply.member_status = 1
	   			ORDER BY coop_mem_apply.member_id ASC";

		if($param['debug'] == "on") {
            echo $sql.'<hr>'; //exit;
        }
		$result = $this->db->query($sql)->result_array();
		$member_ids = array_column($result, 'member_id');

		//Get Lastest Loan Information
		$loan_ids = array_column($result, 'loan_id');
        $where_loan = " 1=1 ";
        if(sizeof(array_filter($loan_ids))){
            $where_loan = " t1.loan_id IN  (".implode(',',array_filter($loan_ids)).") ";
        }
		$loans = $this->db->query("SELECT `t1`.`loan_transaction_id`, `t1`.`loan_id`, `t1`.`loan_amount_balance`, `t1`.`transaction_datetime`
									FROM `coop_loan_transaction` as `t1`
									INNER JOIN (SELECT loan_id, MAX(cast(transaction_datetime as Datetime)) as max FROM coop_loan_transaction WHERE transaction_datetime BETWEEN '".$start_date." 00:00:00' AND '".$end_date." 23:59:59' group by loan_id)
											as t2 ON `t1`.`loan_id` = `t2`.`loan_id` AND `t1`.`transaction_datetime` = `t2`.`max`
									WHERE {$where_loan}
									ORDER BY `t1`.`transaction_datetime`, `t1`.`loan_transaction_id` DESC
									")->result_array();
		$loan_members = array_column($loans, 'loan_id');

		$run_index = 0;
		$row = array();

		$check_row = "xx";
		$index = 0;

		$where_member_ids = "";
		if(sizeof(array_filter($member_ids))){
            $where_member_ids = "member_id IN  ('".implode("','",array_filter(array_unique($member_ids)))."') ";
        }

		$share_month_arr = $this->db->query("SELECT SUM(share_early_value) as val, share_type, member_id
												FROM coop_mem_share
												WHERE MONTH(share_date) = '".$param["month"]."' AND YEAR(share_date) = '".($param["year"] - 543)."' AND ".$where_member_ids." AND share_status = 1 AND share_type IN ('SPM', 'SPA')
												GROUP BY share_type, member_id")->result_array();

		$share_months = array();
		foreach($share_month_arr as $share_month) {
			$share_months[$share_month["member_id"]][$share_month["share_type"]] = $share_month["val"];
		}

		foreach($result AS $key2=>$value2){
			if($check_row != @$value2['member_id']){
				$check_row = @$value2['member_id'];
				$sql_shares = "SELECT
									t1.member_id,
									t1.share_period,
									t1.share_collect_value,
									t1.share_status,
									t1.share_payable_value,
									'1' AS check_share
								FROM coop_mem_share AS t1
								WHERE t1.member_id = '".$value2['member_id']."'
								AND t1.share_date BETWEEN '".$start_date." 00:00:00.000' AND '".$end_date." 23:59:59.000'
								AND t1.share_status <> 0
								ORDER BY t1.share_date DESC ,t1.share_id DESC
								LIMIT 1";
				$shares = $this->db->query($sql_shares)->row_array();
				$share_period = (!empty($shares['share_period']))?@$shares['share_period']: "";

				if(@$shares['share_status'] == 3){
					$share_collect_value = (!empty($shares['share_payable_value']))?@$shares['share_payable_value']: "";
				}else{
					$share_collect_value = (!empty($shares['share_collect_value']))?@$shares['share_collect_value']: "";
				}
				$check_share = (!empty($shares['check_share']) && !empty($share_collect_value))?@$shares['check_share']: "";

				$share_month = 0;
				$share_spec = 0;
				if(!empty($share_months[$member_id])) {
					if(!empty($share_months[$member_id]['SPM'])) {
						$share_month = $share_months[$member_id]['SPM'];
					}
					if (!empty($share_months[$member_id]['SPA'])) {
						$share_spec = $share_months[$member_id]['SPA'];
					} 
				}

				$runno = 1;
			}else{
				$runno++;
			}

			$row['data'][$value2['member_id']][$runno]['member_id'] = $value2['member_id'];
			$row['data'][$value2['member_id']][$runno]['prename_full'] = $value2['prename_full'];
			$row['data'][$value2['member_id']][$runno]['firstname_th'] = $value2['firstname_th'];
			$row['data'][$value2['member_id']][$runno]['lastname_th'] = $value2['lastname_th'];
			$row['data'][$value2['member_id']][$runno]['mem_group_name_main'] = $value2['mem_group_name_main'];
			$row['data'][$value2['member_id']][$runno]['mem_group_name_sub'] = $value2['mem_group_name_sub'];
			$row['data'][$value2['member_id']][$runno]['mem_group_name_level'] = $value2['name'];
			$row['data'][$value2['member_id']][$runno]['mem_group_id'] = $value2['id'];
			$row['data'][$value2['member_id']][$runno]['department'] = $value2['department'];
			$row['data'][$value2['member_id']][$runno]['faction'] = $value2['faction'];
			$row['data'][$value2['member_id']][$runno]['level'] = $value2['level'];
			$row['data'][$value2['member_id']][$runno]['sex'] = $value2['sex'];
			$row['data'][$value2['member_id']][$runno]['share_month'] = $share_month;
			$row['data'][$value2['member_id']][$runno]['share_spec'] = $share_spec;

			if($row_mem_group_level->sub_name == '' || $value2->sub_name=='ไม่ระบุ'){
				$row['data'][$value2['member_id']][$runno]['mem_group_name_sub'] = $value2['main_name'];
			}else{
				$row['data'][$value2['member_id']][$runno]['mem_group_name_sub'] = $value2['sub_name'];
			}

			$row['data'][$value2['member_id']][$runno]['mem_group_name_main'] = $value2['main_name'];

			//หุ้น
			if ($runno == 1) {
				$row['data'][$value2['member_id']][$runno]['share_period'] = $share_period;
				$row['data'][$value2['member_id']][$runno]['share_collect'] = $share_collect_value;
				$row['data'][$value2['member_id']][$runno]['check_share'] = $check_share;
			} else {
				$row['data'][$value2['member_id']][$runno]['share_period'] = "";
				$row['data'][$value2['member_id']][$runno]['share_collect'] = "";
				$row['data'][$value2['member_id']][$runno]['check_share'] = "";
			}
			$row['data'][$value2['member_id']][$runno]['runno'] = $runno;

			$loan_type_code = @$arr_loan_type_code[$value2['loan_type']];
			if(@$loan_type_code == 'emergent' && @$value2['loan_amount_balance'] != '' && in_array($value2['loan_id'],$loan_members)
					&& $loans[array_search($value2['loan_id'],$loan_members)]['loan_amount_balance']){
				//เงินกู้ฉุกเฉิน
				if ($runno == 1) {
					$row['data'][$value2['member_id']][$runno]['loan_emergent_period_now'] = @$value2['period_now'];
					$row['data'][$value2['member_id']][$runno]['loan_emergent_contract_number'] = @$value2['contract_number'];
					$row['data'][$value2['member_id']][$runno]['loan_emergent_balance'] = $loans[array_search($value2['loan_id'],$loan_members)]['loan_amount_balance'];
					$row['data'][$value2['member_id']][$runno]['check_emergent'] = 1;
					$row['data'][$value2['member_id']][$runno]['loan_emergent_prefix_code'] = $value2['prefix_code'];
					$row['data'][$value2['member_id']][$runno]['loan_emergent_type'] = $value2['loan_type'];
					$row['data'][$value2['member_id']][$runno]['loan_emergent_amount'] = $value2['loan_amount'];
				} else {
					for($no_count = 1; $no_count <= $runno; $no_count++) {
						if (empty($row['data'][$value2['member_id']][$no_count]['loan_emergent_contract_number'])) {
							$row['data'][$value2['member_id']][$no_count]['loan_emergent_period_now'] = @$value2['period_now'];
							$row['data'][$value2['member_id']][$no_count]['loan_emergent_contract_number'] = @$value2['contract_number'];
							$row['data'][$value2['member_id']][$no_count]['loan_emergent_balance'] = $loans[array_search($value2['loan_id'],$loan_members)]['loan_amount_balance'];
							$row['data'][$value2['member_id']][$no_count]['check_emergent'] = 1;
							$row['data'][$value2['member_id']][$no_count]['loan_emergent_prefix_code'] = $value2['prefix_code'];
							$row['data'][$value2['member_id']][$no_count]['loan_emergent_type'] = $value2['loan_type'];
							$row['data'][$value2['member_id']][$no_count]['loan_emergent_amount'] = $value2['loan_amount'];
							break;
						} else if ($row['data'][$value2['member_id']][$no_count]['loan_emergent_contract_number'] == $value2['contract_number']) {
							break;
						}
					}
				}
				$run_emergent++;
				if($run_emergent > 1){
					//$runno++;
				}
			}

			if(@$loan_type_code == 'normal' && @$value2['loan_amount_balance'] != '' && in_array($value2['loan_id'],$loan_members)
					&& !empty($loans[array_search($value2['loan_id'],$loan_members)]['loan_amount_balance'])){
				if ($runno == 1) {
					$row['data'][$value2['member_id']][$runno]['loan_normal_period_now'] = @$value2['period_now'];
					$row['data'][$value2['member_id']][$runno]['loan_normal_contract_number'] = @$value2['contract_number'];
					$row['data'][$value2['member_id']][$runno]['loan_normal_balance'] = $loans[array_search($value2['loan_id'],$loan_members)]['loan_amount_balance'];
					$row['data'][$value2['member_id']][$runno]['check_normal'] = 1;
					$row['data'][$value2['member_id']][$runno]['loan_normal_prefix_code'] = $value2['prefix_code'];
					$row['data'][$value2['member_id']][$runno]['loan_normal_type'] = $value2['loan_type'];
					$row['data'][$value2['member_id']][$runno]['loan_normal_amount'] = $value2['loan_amount'];
				} else {
					for($no_count = 1; $no_count <= $runno; $no_count++) {
						if ($row['data'][$value2['member_id']][$no_count]['loan_normal_contract_number'] == $value2['contract_number']) {
							break;
						} else if (empty($row['data'][$value2['member_id']][$no_count]['loan_normal_contract_number'])) {
							$row['data'][$value2['member_id']][$no_count]['loan_normal_period_now'] = @$value2['period_now'];
							$row['data'][$value2['member_id']][$no_count]['loan_normal_contract_number'] = @$value2['contract_number'];
							$row['data'][$value2['member_id']][$no_count]['loan_normal_balance'] = $loans[array_search($value2['loan_id'],$loan_members)]['loan_amount_balance'];
							$row['data'][$value2['member_id']][$no_count]['check_normal'] = 1;
							$row['data'][$value2['member_id']][$no_count]['loan_normal_prefix_code'] = $value2['prefix_code'];
							$row['data'][$value2['member_id']][$no_count]['loan_normal_type'] = $value2['loan_type'];
							$row['data'][$value2['member_id']][$no_count]['loan_normal_amount'] = $value2['loan_amount'];
							break;
						}
					}
				}
				$run_normal++;
			}

			if(@$loan_type_code == 'special' && @$value2['loan_amount_balance'] != '' && in_array($value2['loan_id'],$loan_members)
					&& !empty($loans[array_search($value2['loan_id'],$loan_members)]['loan_amount_balance'])){
				if ($runno == 1) {
					$row['data'][$value2['member_id']][$runno]['loan_special_period_now'] = @$value2['period_now'];
					$row['data'][$value2['member_id']][$runno]['loan_special_contract_number'] = @$value2['contract_number'];
					$row['data'][$value2['member_id']][$runno]['loan_special_balance'] = $loans[array_search($value2['loan_id'],$loan_members)]['loan_amount_balance'];
					$row['data'][$value2['member_id']][$runno]['check_special'] = 1;
					$row['data'][$value2['member_id']][$runno]['loan_special_prefix_code'] = $value2['prefix_code'];
					$row['data'][$value2['member_id']][$runno]['loan_special_type'] = $value2['loan_type'];
					$row['data'][$value2['member_id']][$runno]['loan_special_amount'] = $value2['loan_amount'];
				} else {
					for($no_count = 1; $no_count <= $runno; $no_count++) {
						if (empty($row['data'][$value2['member_id']][$no_count]['loan_special_contract_number'])) {
							$row['data'][$value2['member_id']][$no_count]['loan_special_period_now'] = @$value2['period_now'];
							$row['data'][$value2['member_id']][$no_count]['loan_special_contract_number'] = @$value2['contract_number'];
							$row['data'][$value2['member_id']][$no_count]['loan_special_balance'] = $loans[array_search($value2['loan_id'],$loan_members)]['loan_amount_balance'];
							$row['data'][$value2['member_id']][$no_count]['check_special'] = 1;
							$row['data'][$value2['member_id']][$no_count]['loan_special_prefix_code'] = $value2['prefix_code'];
							$row['data'][$value2['member_id']][$no_count]['loan_special_type'] = $value2['loan_type'];
							$row['data'][$value2['member_id']][$no_count]['loan_special_amount'] = $value2['loan_amount'];
							break;
						} else if ($row['data'][$value2['member_id']][$no_count]['loan_special_contract_number'] == $value2['contract_number']) {
							break;
						}
					}
				}

				$run_special++;
			}

			if(@$value2['loan_amount_balance_atm'] != '' && in_array($value2['loan_atm_id'],$loan_atm_members)
						&& !empty($loan_atms[array_search($value2['loan_atm_id'],$loan_atm_members)]['loan_amount_balance'])){
				//เงินกู้ฉุกเฉิน ATM
				$atm_index_count = $runno;
				if(!empty($row['data'][$value2['member_id']][$runno]['loan_emergent_contract_number'])) {
					$atm_index_count = $runno+1;
				}
				for($no_count = 1; $no_count <= $atm_index_count; $no_count++) {
					if (empty($row['data'][$value2['member_id']][$no_count]['loan_emergent_contract_number'])) {
						$row['data'][$value2['member_id']][$no_count]['member_id'] = $value2['member_id'];
						$row['data'][$value2['member_id']][$no_count]['prename_full'] = $value2['prename_full'];
						$row['data'][$value2['member_id']][$no_count]['firstname_th'] = $value2['firstname_th'];
						$row['data'][$value2['member_id']][$no_count]['lastname_th'] = $value2['lastname_th'];
						$row['data'][$value2['member_id']][$no_count]['mem_group_name_main'] = $value2['mem_group_name_main'];
						$row['data'][$value2['member_id']][$no_count]['mem_group_name_sub'] = $value2['mem_group_name_sub'];
						$row['data'][$value2['member_id']][$no_count]['mem_group_name_level'] = $value2['name'];
						$row['data'][$value2['member_id']][$no_count]['mem_group_id'] = $value2['id'];
						$row['data'][$value2['member_id']][$no_count]['mem_group_id'] = $value2['id'];
						$row['data'][$value2['member_id']][$runno]['mem_group_name_level'] = $value2['name'];
						if($row_mem_group_level->sub_name == '' || $value2->sub_name=='ไม่ระบุ'){
							$row['data'][$value2['member_id']][$no_count]['mem_group_name_sub'] = $value2['main_name'];
						}else{
							$row['data'][$value2['member_id']][$no_count]['mem_group_name_sub'] = $value2['sub_name'];
						}

						$row['data'][$value2['member_id']][$no_count]['mem_group_name_main'] = $value2['main_name'];

						$row['data'][$value2['member_id']][$runno]['department'] = $value2['department'];
						$row['data'][$value2['member_id']][$runno]['faction'] = $value2['faction'];
						$row['data'][$value2['member_id']][$runno]['level'] = $value2['level'];
						$row['data'][$value2['member_id']][$runno]['loan_type'] = $value2['loan_type'];

						//หุ้น
						if ($runno == 1) {
							$row['data'][$value2['member_id']][$runno]['share_period'] = $share_period;
							$row['data'][$value2['member_id']][$runno]['share_collect'] = $share_collect_value;
							$row['data'][$value2['member_id']][$runno]['check_share'] = $check_share;
						} else {
							$row['data'][$value2['member_id']][$runno]['share_period'] = "";
							$row['data'][$value2['member_id']][$runno]['share_collect'] = "";
							$row['data'][$value2['member_id']][$runno]['check_share'] = "";
						}

						$row['data'][$value2['member_id']][$no_count]['runno'] = $runno;
						$row['data'][$value2['member_id']][$no_count]['loan_emergent_period_now'] = '';
						$row['data'][$value2['member_id']][$no_count]['loan_emergent_contract_number'] = @$value2['contract_number_atm'];
						$row['data'][$value2['member_id']][$no_count]['loan_emergent_balance'] = $loan_atms[array_search($value2['loan_atm_id'],$loan_atm_members)]['loan_amount_balance'];
						$row['data'][$value2['member_id']][$no_count]['check_emergent'] = 1;
						break;
					} else if ($row['data'][$value2['member_id']][$no_count]['loan_emergent_contract_number'] == $value2['contract_number_atm']) {
						break;
					}
				}
			}
			$run_index++;
		}

		$data_tmp = $row['data'];
		$member_check = array();
		$member_debt_check = array();

		$data_tmp_sum = array();
		foreach($data_tmp AS $key=>$value){
			foreach($value AS $key2=>$value2){
				if(@$param['type_department'] == '1'){
					$data_tmp_sum[$value2['department']]['share_person'] += !empty($value2['check_share']) ? $value2['check_share'] : 0;
					$data_tmp_sum[$value2['department']]['share_collect'] += !empty($value2['share_collect']) ? $value2['share_collect'] : 0;

					$data_tmp_sum[$value2['department']]['loan_emergent_person'] += !empty($value2['loan_emergent_person']) ? $value2['loan_emergent_person'] : 0;
					$data_tmp_sum[$value2['department']]['loan_emergent_balance'] += !empty($value2['loan_emergent_balance']) ? $value2['loan_emergent_balance'] : 0;

					$data_tmp_sum[$value2['department']]['loan_normal_person'] += !empty($value2['loan_normal_person']) ? $value2['loan_normal_person'] : 0;
					$data_tmp_sum[$value2['department']]['loan_normal_balance'] += !empty($value2['loan_normal_balance']) ? $value2['loan_normal_balance'] : 0;

					$data_tmp_sum[$value2['department']]['loan_special_person'] += !empty($value2['loan_special_person']) ? $value2['loan_special_person'] : 0;
					$data_tmp_sum[$value2['department']]['loan_special_balance'] += !empty($value2['loan_special_balance']) ? $value2['loan_special_balance'] : 0;
				}else if(@$param['type_department'] == '2'){
					$data_tmp_sum[$value2['faction']]['share_person'] += !empty($value2['check_share']) ? $value2['check_share'] : 0;
					$data_tmp_sum[$value2['faction']]['share_collect'] += !empty($value2['share_collect']) ? $value2['share_collect'] : 0;

					if(!in_array($value2["member_id"], $member_check)) {
						if($value2["sex"] == "M") {
							$data_tmp_sum[$value2['faction']]["sex"]["m"] += 1;
						} else if ($value2["sex"] == "F") {
							$data_tmp_sum[$value2['faction']]["sex"]["f"] += 1;
						}

						$data_tmp_sum[$value2['faction']]['share_month'] = $value2["share_month"];
					}

					if(!empty($value2['check_special'])) {
						$type = $value2["loan_special_type"];
						$data_tmp_sum[$value2['faction']]['loan'][2][$type]['type'] = !empty($value2['loan_special_type']) ? $value2['loan_special_type'] : "";
						$data_tmp_sum[$value2['faction']]['loan'][2][$type]['prefix'] = !empty($value2['loan_special_prefix_code']) ? $value2['loan_special_prefix_code'] : "";
						$data_tmp_sum[$value2['faction']]['loan'][2][$type]['loan_person'] += !empty($value2['check_special']) ? $value2['check_special'] : 0;
						$data_tmp_sum[$value2['faction']]['loan'][2][$type]['loan_balance'] += !empty($value2['loan_special_balance']) ? $value2['loan_special_balance'] : 0;
						$data_tmp_sum[$value2['faction']]['loan'][2][$type]['amount'] += !empty($value2['loan_special_amount']) ? $value2['loan_special_amount'] : 0;
						if(!in_array($value2["member_id"], $member_debt_check)) {
							$data_tmp_sum[$value2['faction']]["debt_person"] += 1;
							$member_debt_check[] = $value2["member_id"];
						}
					}

					if(!empty($value2['check_emergent'])) {
						$type = $value2["loan_emergent_type"];
						$data_tmp_sum[$value2['faction']]['loan'][2][$type]['type'] = !empty($value2['loan_emergent_type']) ? $value2['loan_emergent_type'] : "";
						$data_tmp_sum[$value2['faction']]['loan'][2][$type]['prefix'] = !empty($value2['loan_emergent_prefix_code']) ? $value2['loan_emergent_prefix_code'] : "";
						$data_tmp_sum[$value2['faction']]['loan'][2][$type]['loan_person'] += !empty($value2['check_emergent']) ? $value2['check_emergent'] : 0;
						$data_tmp_sum[$value2['faction']]['loan'][2][$type]['loan_balance'] += !empty($value2['loan_emergent_balance']) ? $value2['loan_emergent_balance'] : 0;
						$data_tmp_sum[$value2['faction']]['loan'][2][$type]['amount'] += !empty($value2['loan_emergent_amount']) ? $value2['loan_emergent_amount'] : 0;
						if(!in_array($value2["member_id"], $member_debt_check)) {
							$data_tmp_sum[$value2['faction']]["debt_person"] += 1;
							$member_debt_check[] = $value2["member_id"];
						}
					}

					if(!empty($value2['check_normal'])) {
						$type = $value2["loan_normal_type"];

						if (in_array($value2["loan_normal_type"], array("12", "14", "15", "16", "22"))) {
							if(in_array($value2["loan_normal_type"], array("22"))) {
								$type = "12";
							}
							$data_tmp_sum[$value2['faction']]['loan'][1][$type]['type'] = !empty($value2['loan_normal_type']) ? $value2['loan_normal_type'] : "";
							$data_tmp_sum[$value2['faction']]['loan'][1][$type]['prefix'] = !empty($value2['loan_normal_prefix_code']) ? $value2['loan_normal_prefix_code'] : "";
							$data_tmp_sum[$value2['faction']]['loan'][1][$type]['loan_person'] += !empty($value2['check_normal']) ? $value2['check_normal'] : 0;
							$data_tmp_sum[$value2['faction']]['loan'][1][$type]['loan_balance'] += !empty($value2['loan_normal_balance']) ? $value2['loan_normal_balance'] : 0;
							$data_tmp_sum[$value2['faction']]['loan'][1][$type]['amount'] += !empty($value2['loan_normal_amount']) ? $value2['loan_normal_amount'] : 0;
						} else {
							$data_tmp_sum[$value2['faction']]['loan'][2][$type]['type'] = !empty($value2['loan_normal_type']) ? $value2['loan_normal_type'] : "";
							$data_tmp_sum[$value2['faction']]['loan'][2][$type]['prefix'] = !empty($value2['loan_normal_prefix_code']) ? $value2['loan_normal_prefix_code'] : "";
							$data_tmp_sum[$value2['faction']]['loan'][2][$type]['loan_person'] += !empty($value2['check_normal']) ? $value2['check_normal'] : 0;
							$data_tmp_sum[$value2['faction']]['loan'][2][$type]['loan_balance'] += !empty($value2['loan_normal_balance']) ? $value2['loan_normal_balance'] : 0;
							$data_tmp_sum[$value2['faction']]['loan'][2][$type]['amount'] += !empty($value2['loan_normal_amount']) ? $value2['loan_normal_amount'] : 0;
						}

						if(!in_array($value2["member_id"], $member_debt_check)) {
							$data_tmp_sum[$value2['faction']]["debt_person"] += 1;
							$member_debt_check[] = $value2["member_id"];
						}
					}

					$data_tmp_sum[@$value2['faction']]['share_balance_subdivision'] += !empty($value2['share_collect']) ? $value2['share_collect'] : 0;
					$data_tmp_sum[@$value2['faction']]['loan_balance_subdivision'] += !empty($value2['loan_emergent_balance']) ? @$value2['loan_emergent_balance']+@$value2['loan_normal_balance']+@$value2['loan_special_balance'] : 0;
				}
				$member_check[] = $value2["member_id"];
			}
		}

		$arr_data = array();
		$x=0;
		$join_arr = array();

		$where = "";
		$field_department = "department";
		if(@$param['type_department'] == '1'){
			$where .= " AND coop_mem_group.mem_group_type = '1'";

		}else if(@$param['type_department'] == '2'){
			$where .= " AND coop_mem_group.mem_group_type = '2'";
			$field_department = "faction";
		}

		$this->paginater_all_preview->type(DB_TYPE);
		$this->paginater_all_preview->select(array(
													'coop_mem_group.id',
													'coop_mem_group.mem_group_id',
													'coop_mem_group.mem_group_parent_id',
													'coop_mem_group.mem_group_name'
												));
		$this->paginater_all_preview->main_table('coop_mem_group');
		$this->paginater_all_preview->where("{$where}");
		$this->paginater_all_preview->page_now(@$param["page"]);
		$this->paginater_all_preview->per_page(20);
		$this->paginater_all_preview->page_link_limit(28);
		$this->paginater_all_preview->page_limit_first(22);
		$this->paginater_all_preview->order_by('coop_mem_group.mem_group_id');
		$this->paginater_all_preview->join_arr($join_arr);
		$row = $this->paginater_all_preview->paginater_process();

		foreach($row['data'] AS $key=>$value){
			$check_group_id_sub = 'xx';
			foreach($value AS $key2=>$value2){
				//หุ้น
				$row['data'][$key][$key2]['share_person'] = $data_tmp_sum[$value2['id']]['share_person'];
				$row['data'][$key][$key2]['share_collect'] = $data_tmp_sum[$value2['id']]['share_collect'];
				$row['data'][$key][$key2]['share_month'] = $data_tmp_sum[$value2['id']]['share_month'];
				$row['data'][$key][$key2]['share_spec'] = $data_tmp_sum[$value2['id']]['share_spec'];
				$row['data'][$key][$key2]['debt_person'] = $data_tmp_sum[$value2['id']]['debt_person'];
				$row['data'][$key][$key2]['loan'] = $data_tmp_sum[$value2['id']]["loan"];
				$row['data'][$key][$key2]['sex'] = $data_tmp_sum[$value2['id']]["sex"];

				$new_mem = $this->db->select("COUNT(*) as count")->from("coop_mem_apply")->where("MONTH(member_date) = '".$param["month"]."' AND YEAR(member_date) = '".($param["year"] - 543)."' AND faction = '".$value2['id']."'")->get()->row_array();
				$row['data'][$key][$key2]['new_mem'] = $new_mem["count"];

				$resign = $this->db->select("COUNT(*) as count")
									->from("coop_mem_apply as t1")
									->join("coop_mem_req_resign as t2", "t1.member_id = t2.member_id AND t2.req_resign_status = '1'")
									->where("MONTH(t2.approve_date) = '".$param["month"]."' AND YEAR(t2.approve_date) = '".($param["year"] - 543)."' AND t1.faction = '".$value2['id']."'")
									->get()->row_array();
				$row['data'][$key][$key2]['resign_mem'] = $resign["count"];

				//รวมเงินกู้คงเหลือ
				$row['data'][$key][$key2]['total_loan_balance'] = $data_tmp_sum[$value2['id']]['loan_emergent_balance']+$data_tmp_sum[$value2['id']]['loan_normal_balance']+$data_tmp_sum[$value2['id']]['loan_special_balance'];

				$this->db->select(array('coop_mem_group.id','coop_mem_group.mem_group_id','coop_mem_group.mem_group_parent_id','coop_mem_group.mem_group_name'));
				$this->db->from('coop_mem_group');
				$this->db->where("coop_mem_group.id = '".@$value2['mem_group_parent_id']."'");
				$this->db->limit(1);
				$rs_mem_group_sub = $this->db->get()->result_array();
				$row_mem_group_sub = @$rs_mem_group_sub[0];
				if(@$row_mem_group_sub['mem_group_name'] == '' || @$row_mem_group_sub['mem_group_name']=='ไม่ระบุ'){
					$this->db->select(array('coop_mem_group.id','coop_mem_group.mem_group_id','coop_mem_group.mem_group_parent_id','coop_mem_group.mem_group_name'));
					$this->db->from('coop_mem_group');
					$this->db->where("coop_mem_group.id = '".@$row_mem_group_sub['mem_group_parent_id']."'");
					$this->db->limit(1);
					$rs_mem_group_main = $this->db->get()->result_array();

					$row['data'][$key][$key2]['mem_group_name_main'] = @$rs_mem_group_main[0]['mem_group_name'];
					$mem_group_id_sub = @$rs_mem_group_main[0]['id'];
					$row['data'][$key][$key2]['share_balance_subdivision'] = $row['data'][$key][$key2]['share_collect'];
					$row['data'][$key][$key2]['loan_balance_subdivision'] = $row['data'][$key][$key2]['total_loan_balance'];
				}else{
					$mem_group_id_sub = @$value2['mem_group_parent_id'];
					$row['data'][$key][$key2]['mem_group_name_main'] = @$row_mem_group_sub['mem_group_name'];
					$row['data'][$key][$key2]['share_balance_subdivision'] = @$data_tmp_sum[$mem_group_id_sub]['share_balance_subdivision'];
					$row['data'][$key][$key2]['loan_balance_subdivision'] = @$data_tmp_sum[$mem_group_id_sub]['loan_balance_subdivision'];
				}

				if($mem_group_id_sub == $check_group_id_sub){
					$run_sub++;
				}else{
					$check_group_id_sub = $mem_group_id_sub;
				}

				$arr_data['arr_run_sub'][@$mem_group_id_sub] = @$run_sub;

				$row['data'][$key][$key2]['run_sub'] = @$run_sub;
				$row['data'][$key][$key2]['mem_group_id_sub'] = @$mem_group_id_sub;
			}
		}

		$arr_data['num_rows'] = $row['num_rows'];
		$arr_data['paging'] = $paging;
		$arr_data['data'] = $row['data'];
		$arr_data['page_all'] = $row['page_all'];

		$this->db->select(array('id','loan_type','loan_type_code'));
		$this->db->from('coop_loan_type');
		$this->db->order_by("order_by");
		$row = $this->db->get()->result_array();
		$arr_data['loan_type'] = $row;

		$arr_data['month_arr'] = $this->center_function->month_arr();
		$arr_data['month_short_arr'] = $this->center_function->month_short_arr();

		return $arr_data;
	}

	public function get_loan_share_balance_person($param) {
		if(@$param['start_date']){
			$start_date_arr = explode('/',@$param['start_date']);
			$start_day = $start_date_arr[0];
			$start_month = $start_date_arr[1];
			$start_year = $start_date_arr[2];
			$start_year -= 543;
			$get_start_date = $start_year.'-'.$start_month.'-'.$start_day;
		}

		if(!empty($param["month"]) && !empty($param["year"])) {
			$get_start_date = date('Y-m-t', strtotime(($param["year"] - 543)."-".($param["month"])."-01"));
		}

		if(@$param['type_date'] == '1'){
			$this->db->select(array('share_date'));
			$this->db->from('coop_mem_share');
			$this->db->where("share_status IN ('1', '2') AND share_date != '0000-00-00 00:00:00'");
			$this->db->order_by("share_date ASC");
			$this->db->limit(1);
			$rs_date_share = $this->db->get()->result_array();
			$date_share_min  =  date("Y-m-d", strtotime(@$rs_date_share[0]['share_date']));

			$this->db->select(array('createdatetime'));
			$this->db->from('coop_loan');
			$this->db->where("loan_status = '1'");
			$this->db->order_by("createdatetime ASC");
			$this->db->limit(1);
			$rs_date_loan = $this->db->get()->result_array();
			$date_loan_min  =  date("Y-m-d", strtotime(@$rs_date_loan[0]['createdatetime']));

			$this->db->select(array('transaction_datetime'));
			$this->db->from('coop_loan_atm_transaction');
			$this->db->order_by("transaction_datetime ASC");
			$this->db->limit(1);
			$rs_date_loan_atm = $this->db->get()->result_array();
			$date_loan_atm_min  =  date("Y-m-d", strtotime(@$rs_date_loan_atm[0]['transaction_datetime']));

			if($date_share_min < $date_loan_min){
				$start_date = $date_share_min;
			}else if($date_loan_min < $date_loan_atm_min){
				$start_date = $date_loan_min;
			}else if($date_loan_atm_min < $date_share_min){
				$start_date = $date_loan_atm_min;
			}else{
				$start_date = $date_share_min;
			}
			$end_date = $get_start_date;
		}else{
			$start_date = $get_start_date;
			$end_date = $get_start_date;
		}

		$where_date = "";
		$where_date_loan = "";
		$where_date_loan_atm = "";
		$where_date_loan_atm_transaction = "";
		$where_date_loan_transaction = "";
		if(@$param['start_date'] != ''){
			$where_date .= " AND coop_mem_share.share_date BETWEEN '".$start_date." 00:00:00.000' AND '".$end_date." 23:59:59.000'";
			$where_date_loan .= " AND coop_loan.createdatetime BETWEEN '".$start_date." 00:00:00.000' AND '".$end_date." 23:59:59.000'";
			$where_date_loan_atm .= " AND coop_loan_atm.createdatetime BETWEEN '".$start_date." 00:00:00.000' AND '".$end_date." 23:59:59.000'";
			$where_date_loan_atm_transaction .= " AND coop_loan_atm_transaction.transaction_datetime BETWEEN '".$start_date." 00:00:00.000' AND '".$end_date." 23:59:59.000'";
			$where_date_loan_transaction .= " AND coop_loan_transaction.transaction_datetime BETWEEN '".$start_date." 00:00:00.000' AND '".$end_date." 23:59:59.000'";
		}

		$this->db->select(array('coop_loan_name.loan_name_id', 'coop_loan_name.loan_name','coop_loan_type.loan_type_code'));
		$this->db->from('coop_loan_name');
		$this->db->join('coop_loan_type','coop_loan_name.loan_type_id = coop_loan_type.id','left');
		$rs_type_code = $this->db->get()->result_array();
		$arr_loan_type_code = array();
		$loan_type_datas = array();
		foreach($rs_type_code AS $key_type_code=>$row_type_code){
			$arr_loan_type_code[@$row_type_code['loan_name_id']] = @$row_type_code['loan_name'];
		}
		$arr_data['loan_name'] = $arr_loan_type_code;

		$sql = "SELECT `coop_mem_apply`.`member_id`, `coop_mem_apply`.`prename_id`, `coop_mem_apply`.`firstname_th`, `coop_mem_apply`.`lastname_th`, `coop_mem_apply`.`faction`, coop_mem_apply.sex,
					`t5`.`loan_id`, `t5`.`loan_amount_balance`, `t5`.`contract_number`, `t5`.`loan_type`, t5.prefix_code, t5.loan_amount
				FROM (SELECT IF (
								(SELECT faction_old FROM coop_mem_group_move WHERE date_move >= '".$end_date."' AND coop_mem_group_move.member_id = coop_mem_apply.member_id ORDER BY date_move ASC LIMIT 1),
								(SELECT faction_old FROM coop_mem_group_move WHERE date_move >= '".$end_date."' AND coop_mem_group_move.member_id = coop_mem_apply.member_id ORDER BY date_move ASC LIMIT 1),
								coop_mem_apply.faction
							) AS faction, member_id, prename_id, firstname_th, lastname_th,member_status, sex FROM coop_mem_apply) AS coop_mem_apply
				LEFT JOIN (SELECT t3.member_id ,t3.contract_number ,t3.period_now
					,t1.loan_transaction_id
					,t1.loan_id
					,t1.loan_amount_balance
					,t3.loan_amount
					,t1.transaction_datetime
					,t3.loan_type
					,t4.prefix_code FROM coop_loan_transaction AS t1
					LEFT JOIN coop_loan AS t3 ON t1.loan_id = t3.id
					LEFT JOIN coop_term_of_loan AS t4 ON t3.loan_type = t4.type_id
					WHERE t1.transaction_datetime BETWEEN '".$start_date." 00:00:00.000' AND '".$end_date." 23:59:59.000' AND t1.loan_amount_balance > 0 GROUP BY t1.loan_id ORDER BY t1.loan_id DESC ,t1.loan_transaction_id DESC )
					AS t5 ON `coop_mem_apply`.`member_id` = `t5`.`member_id`
				WHERE coop_mem_apply.member_status = 1
	   			ORDER BY coop_mem_apply.member_id ASC";

		if($param['debug'] == "on") {
            echo $sql.'<hr>'; //exit;
        }
		$result = $this->db->query($sql)->result_array();
		$member_ids = array_column($result, 'member_id');

		//Get Lastest Loan Information
		$loan_ids = array_column($result, 'loan_id');
        $where_loan = " 1=1 ";
        if(sizeof(array_filter($loan_ids))){
            $where_loan = " t1.loan_id IN  (".implode(',',array_filter($loan_ids)).") ";
        }
		$loans = $this->db->query("SELECT `t1`.`loan_transaction_id`, `t1`.`loan_id`, `t1`.`loan_amount_balance`, `t1`.`transaction_datetime`
									FROM `coop_loan_transaction` as `t1`
									INNER JOIN (SELECT loan_id, MAX(cast(transaction_datetime as Datetime)) as max FROM coop_loan_transaction WHERE transaction_datetime BETWEEN '".$start_date." 00:00:00' AND '".$end_date." 23:59:59' group by loan_id)
											as t2 ON `t1`.`loan_id` = `t2`.`loan_id` AND `t1`.`transaction_datetime` = `t2`.`max`
									WHERE {$where_loan}
									ORDER BY `t1`.`transaction_datetime`, `t1`.`loan_transaction_id` DESC
									")->result_array();
		$loan_members = array_column($loans, 'loan_id');

		$finance_trans = $this->db->query("SELECT t1.loan_id, SUM(t1.principal_payment) as principal, SUM(t1.interest) as interest, MAX(t1.period_count) as period_count, MAX(t1.payment_date) as payment_date
												FROM coop_finance_transaction as t1
												INNER JOIN coop_receipt as t2 ON t1.receipt_id = t2.receipt_id AND t2.receipt_status IS NULL
												WHERE YEAR(t1.payment_date) = '".($param["year"] - 543)."' AND MONTH(t1.payment_date) = '".$param["month"]."' AND t1.loan_id IS NOT NULL
												GROUP BY t1.loan_id")->result_array();
		$finance_tran_members = array_column($finance_trans, 'loan_id');

		$run_index = 0;
		$row = array();

		$check_row = "xx";
		$index = 0;

		$where_member_ids = "";
		if(sizeof(array_filter($member_ids))){
            $where_member_ids = "member_id IN  ('".implode("','",array_filter(array_unique($member_ids)))."') ";
        }

		$share_month_arr = $this->db->query("SELECT SUM(share_early_value) as val, share_type, member_id, MAX(share_date) as share_date
												FROM coop_mem_share
												WHERE MONTH(share_date) = '".$param["month"]."' AND YEAR(share_date) = '".($param["year"] - 543)."' AND ".$where_member_ids." AND share_status = 1 AND share_type IN ('SPM', 'SPA')
												GROUP BY share_type, member_id")->result_array();

		$share_months = array();
		foreach($share_month_arr as $share_month) {
			$share_months[$share_month["member_id"]][$share_month["share_type"]]['val'] = $share_month["val"];
			$share_months[$share_month["member_id"]][$share_month["share_type"]]['date'] = $share_month["share_date"];
		}

		foreach($result AS $key2=>$value2){
			if($check_row != @$value2['member_id']){
				$check_row = @$value2['member_id'];
				$sql_shares = "SELECT
									t1.member_id,
									t1.share_period,
									t1.share_collect_value,
									t1.share_status,
									t1.share_payable_value,
									'1' AS check_share
								FROM coop_mem_share AS t1
								WHERE t1.member_id = '".$value2['member_id']."'
								AND t1.share_date BETWEEN '".$start_date." 00:00:00.000' AND '".$end_date." 23:59:59.000'
								AND t1.share_status <> 0
								ORDER BY t1.share_date DESC ,t1.share_id DESC
								LIMIT 1";
				$shares = $this->db->query($sql_shares)->row_array();
				$share_period = (!empty($shares['share_period']))?@$shares['share_period']: "";

				if(@$shares['share_status'] == 3){
					$share_collect_value = (!empty($shares['share_payable_value']))?@$shares['share_payable_value']: "";
				}else{
					$share_collect_value = (!empty($shares['share_collect_value']))?@$shares['share_collect_value']: "";
				}
				$check_share = (!empty($shares['check_share']) && !empty($share_collect_value))?@$shares['check_share']: "";

				$share_month = 0;
				$share_spec = 0;
				$share_lastest_date = "";
				if(!empty($share_months[$value2['member_id']])) {
					if (!empty($share_months[$value2['member_id']]['SPA'])) {
						$share_spec = $share_months[$value2['member_id']]['SPA']['val'];
						$share_lastest_date = $share_months[$value2['member_id']]['SPA']['date'];
					}
					if(!empty($share_months[$value2['member_id']]['SPM'])) {
						$share_month = $share_months[$value2['member_id']]['SPM']['val'];
						$share_lastest_date = $share_months[$value2['member_id']]['SPM']['date'];
					}
				}

				$runno = 1;
			}else{
				$runno++;
			}

			$row['data'][$value2['member_id']]['member_id'] = $value2['member_id'];
			$row['data'][$value2['member_id']]['prename_full'] = $value2['prename_full'];
			$row['data'][$value2['member_id']]['firstname_th'] = $value2['firstname_th'];
			$row['data'][$value2['member_id']]['lastname_th'] = $value2['lastname_th'];
			$row['data'][$value2['member_id']]['mem_group_name_main'] = $value2['mem_group_name_main'];
			$row['data'][$value2['member_id']]['mem_group_name_sub'] = $value2['mem_group_name_sub'];
			$row['data'][$value2['member_id']]['mem_group_name_level'] = $value2['name'];
			$row['data'][$value2['member_id']]['mem_group_id'] = $value2['id'];
			$row['data'][$value2['member_id']]['department'] = $value2['department'];
			$row['data'][$value2['member_id']]['faction'] = $value2['faction'];
			$row['data'][$value2['member_id']]['level'] = $value2['level'];
			$row['data'][$value2['member_id']]['sex'] = $value2['sex'];

			if($row_mem_group_level->sub_name == '' || $value2->sub_name=='ไม่ระบุ'){
				$row['data'][$value2['member_id']]['mem_group_name_sub'] = $value2['main_name'];
			}else{
				$row['data'][$value2['member_id']]['mem_group_name_sub'] = $value2['sub_name'];
			}

			$row['data'][$value2['member_id']]['mem_group_name_main'] = $value2['main_name'];

			//หุ้น
			$row['data'][$value2['member_id']]['share_period'] = $share_period;
			$row['data'][$value2['member_id']]['share_collect'] = $share_collect_value;
			$row['data'][$value2['member_id']]['check_share'] = $check_share;
			$row['data'][$value2['member_id']]['share_transaction'] = $share_month + $share_spec;
			$row['data'][$value2['member_id']]['share_date'] = $share_lastest_date;
			$row['data'][$value2['member_id']]['check_share'] = $check_share;

			if(!empty($value2['loan_id'])) {
				if (in_array($value2["loan_type"], array("12", "14", "15", "16", "22"))) {
					$row['data'][$value2['member_id']]['loan'][1][$value2['loan_id']]['contract_number'] = @$value2['contract_number'];
					$row['data'][$value2['member_id']]['loan'][1][$value2['loan_id']]['balance'] = $loans[array_search($value2['loan_id'],$loan_members)]['loan_amount_balance'];
					$row['data'][$value2['member_id']]['loan'][1][$value2['loan_id']]['check'] = 1;
					$row['data'][$value2['member_id']]['loan'][1][$value2['loan_id']]['prefix_code'] = $value2['prefix_code'];
					$row['data'][$value2['member_id']]['loan'][1][$value2['loan_id']]['type'] = $value2['loan_type'];
					$row['data'][$value2['member_id']]['loan'][1][$value2['loan_id']]['amount'] = $value2['loan_amount'];

					$finance_tran = $finance_trans[array_search($value2['loan_id'],$finance_tran_members)];
					if(!empty($finance_tran)) {
						$row['data'][$value2['member_id']]['loan'][1][$value2['loan_id']]['payment_date'] = $finance_tran['payment_date'];
						$row['data'][$value2['member_id']]['loan'][1][$value2['loan_id']]['period'] = $finance_tran["period_count"];
						$row['data'][$value2['member_id']]['loan'][1][$value2['loan_id']]['principal'] = $finance_tran["principal"];
						$row['data'][$value2['member_id']]['loan'][1][$value2['loan_id']]['interest'] = $finance_tran["interest"];
					}
				} else {
					$row['data'][$value2['member_id']]['loan'][2][$value2['loan_id']]['contract_number'] = @$value2['contract_number'];
					$row['data'][$value2['member_id']]['loan'][2][$value2['loan_id']]['balance'] = $loans[array_search($value2['loan_id'],$loan_members)]['loan_amount_balance'];
					$row['data'][$value2['member_id']]['loan'][2][$value2['loan_id']]['check'] = 1;
					$row['data'][$value2['member_id']]['loan'][2][$value2['loan_id']]['prefix_code'] = $value2['prefix_code'];
					$row['data'][$value2['member_id']]['loan'][2][$value2['loan_id']]['type'] = $value2['loan_type'];
					$row['data'][$value2['member_id']]['loan'][2][$value2['loan_id']]['amount'] = $value2['loan_amount'];

					$finance_tran = $finance_trans[array_search($value2['loan_id'],$finance_tran_members)];
					if(!empty($finance_tran)) {
						$row['data'][$value2['member_id']]['loan'][2][$value2['loan_id']]['payment_date'] = $finance_tran['payment_date'];
						$row['data'][$value2['member_id']]['loan'][2][$value2['loan_id']]['period'] = $finance_tran["period_count"];
						$row['data'][$value2['member_id']]['loan'][2][$value2['loan_id']]['principal'] = $finance_tran["principal"];
						$row['data'][$value2['member_id']]['loan'][2][$value2['loan_id']]['interest'] = $finance_tran["interest"];
					}
				}
			}
		}

		$arr_data['data'] = $row['data'];

		$this->db->select(array('id','loan_type','loan_type_code'));
		$this->db->from('coop_loan_type');
		$this->db->order_by("order_by");
		$row = $this->db->get()->result_array();
		$arr_data['loan_type'] = $row;

		$arr_data['month_arr'] = $this->center_function->month_arr();
		$arr_data['month_short_arr'] = $this->center_function->month_short_arr();

		return $arr_data;
	}

    public function coop_report_members_buy_share(){
        $arr_data = array();
        $this->db->select(array('id','loan_type'));
        $this->db->from('coop_loan_type');
        $this->db->order_by('order_by ASC');
        $rs_loan_type = $this->db->get()->result_array();
        //print_r($this->db->last_query()); exit;
        $loan_type = array();
        if(!empty($rs_loan_type)){
            foreach($rs_loan_type as $key => $row_loan_type){
                $loan_type[$row_loan_type['id']] = @$row_loan_type['loan_type'];
            }
        }
        $arr_data['loan_type'] = $loan_type;
        $this->libraries->template('report_share_data/coop_report_members_buy_share',$arr_data);
    }

    function check_report_members_buy_share(){
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

        $where_check = "";
        $where_check2 = "";

        if(@$_POST['start_date'] != '' AND @$_POST['end_date'] == ''){
            $where_check = " AND t1.create_date BETWEEN '".$start_date." 00:00:00.000' AND '".$start_date." 23:59:59.000'";
            $where_check2 = " AND t1.share_date BETWEEN '".$start_date." 00:00:00.000' AND '".$start_date." 23:59:59.000'";
        }else if(@$_POST['start_date'] != '' AND @$_POST['end_date'] != ''){
            $where_check = " AND t1.create_date BETWEEN '".$start_date." 00:00:00.000' AND '".$end_date." 23:59:59.000'";
            $where_check2 = " AND t1.share_date BETWEEN '".$start_date." 00:00:00.000' AND '".$end_date." 23:59:59.000'";
        }

        $this->db->select('t2.member_id');
        $this->db->from('coop_change_share as t1');
        $this->db->join("coop_mem_apply as t2", "t1.member_id = t2.member_id", "inner");
        $this->db->join("coop_prename as t3", "t2.prename_id = t3.prename_id", "left");
        $this->db->where("t1.change_share_status IN ('1', '2') {$where_check}");
        $rs_check = $this->db->get()->result_array();
        $row_check = @$rs_check[0];
        //print_r($this->db->last_query()); exit;

        $this->db->select('t2.member_id');
        $this->db->from('coop_mem_share as t1');
        $this->db->join("coop_mem_apply as t2", "t1.member_id = t2.member_id", "inner");
        $this->db->join("coop_prename as t3", "t2.prename_id = t3.prename_id", "left");
        $this->db->where("t1.share_status IN ('1', '2') AND t1.share_type = 'SPA'  {$where_check2}");
        $rs_check2 = $this->db->get()->result_array();
        $row_check2 = @$rs_check2[0];

        if(@$row_check['member_id'] != '' || @$row_check2['member_id'] != ''){
            echo "success";
        }
    }

    function coop_report_members_buy_share_preview(){
        $arr_data = array();
        $this->db->select(array('id','mem_group_name'));
        $this->db->from('coop_mem_group');
        $rs_group = $this->db->get()->result_array();
        $mem_group_arr = array();
        foreach($rs_group as $key => $row_group){
            $mem_group_arr[$row_group['id']] = $row_group['mem_group_name'];
        }
        $arr_data['mem_group_arr'] = $mem_group_arr;

        $this->db->select(array('id','loan_type'));
        $this->db->from('coop_loan_type');
        $rs_loan_type = $this->db->get()->result_array();
        $loan_type = array();
        foreach($rs_loan_type as $key => $row_loan_type){
            $loan_type[$row_loan_type['id']] = $row_loan_type['loan_type'];
        }
        $arr_data['loan_type'] = $loan_type;

        $this->db->select(array('setting_value'));
        $this->db->from('coop_share_setting');
        $this->db->where("setting_id = '1'");
        $row_share_value = $this->db->get()->result_array();
        $share_value = $row_share_value[0]['setting_value'];
        $arr_data['share_value'] = $share_value;

        $arr_data['month_arr'] = $this->center_function->month_arr();
        $arr_data['month_short_arr'] = $this->center_function->month_short_arr();

        $this->preview_libraries->template_preview('report_share_data/coop_report_members_buy_share_preview',$arr_data);
    }

    function coop_report_members_buy_share_excel(){
        $arr_data = array();
        $this->db->select(array('id','mem_group_name'));
        $this->db->from('coop_mem_group');
        $rs_group = $this->db->get()->result_array();
        $mem_group_arr = array();
        foreach($rs_group as $key => $row_group){
            $mem_group_arr[$row_group['id']] = $row_group['mem_group_name'];
        }
        $arr_data['mem_group_arr'] = $mem_group_arr;

        $this->db->select(array('id','loan_type'));
        $this->db->from('coop_loan_type');
        $rs_loan_type = $this->db->get()->result_array();
        $loan_type = array();
        foreach($rs_loan_type as $key => $row_loan_type){
            $loan_type[$row_loan_type['id']] = $row_loan_type['loan_type'];
        }
        $arr_data['loan_type'] = $loan_type;

        $this->db->select(array('setting_value'));
        $this->db->from('coop_share_setting');
        $this->db->where("setting_id = '1'");
        $row_share_value = $this->db->get()->result_array();
        $share_value = $row_share_value[0]['setting_value'];
        $arr_data['share_value'] = $share_value;

        $this->load->view('report_share_data/coop_report_members_buy_share_excel',$arr_data);
    }

	function get_share_loan_balance_person($param){
		ini_set('memory_limit', -1);
		set_time_limit(-1);
		//$this->db->save_queries = FALSE;
		if(@$param['start_date']){
			$start_date_arr = explode('/',urldecode(@$param['start_date']));
			$start_day = $start_date_arr[0];
			$start_month = $start_date_arr[1];
			$start_year = $start_date_arr[2];
			$start_year -= 543;
			$get_start_date = $start_year.'-'.$start_month.'-'.$start_day;
		}

		if(@$param['type_date'] == '1'){
			$this->db->select(array('share_date'));
			$this->db->from('coop_mem_share');
			$this->db->where("share_status IN ('1', '2') AND share_date != '0000-00-00 00:00:00'");
			$this->db->order_by("share_date ASC");
			$this->db->limit(1);
			$rs_date_share = $this->db->get()->result_array();
			$date_share_min  =  date("Y-m-d", strtotime(@$rs_date_share[0]['share_date']));

			$this->db->select(array('createdatetime'));
			$this->db->from('coop_loan');
			$this->db->where("loan_status = '1'");
			$this->db->order_by("createdatetime ASC");
			$this->db->limit(1);
			$rs_date_loan = $this->db->get()->result_array();
			$date_loan_min  =  date("Y-m-d", strtotime(@$rs_date_loan[0]['createdatetime']));
			
			$this->db->select(array('transaction_datetime'));
			$this->db->from('coop_loan_transaction');
			$this->db->order_by("transaction_datetime ASC");
			$this->db->limit(1);
			$rs_date_loan_transaction = $this->db->get()->result_array();
			$date_loan_transaction_min  =  date("Y-m-d", strtotime(@$rs_date_loan_transaction[0]['transaction_datetime']));
			
			$this->db->select(array('transaction_datetime'));
			$this->db->from('coop_loan_atm_transaction');
			$this->db->order_by("transaction_datetime ASC");
			$this->db->limit(1);
			$rs_date_loan_atm = $this->db->get()->result_array();
			$date_loan_atm_min  =  date("Y-m-d", strtotime(@$rs_date_loan_atm[0]['transaction_datetime']));

			if($date_loan_transaction_min < $date_share_min){
                //echo "1";exit;
				$start_date = $date_loan_transaction_min;
			}else if($date_share_min < $date_loan_min){
                //echo "2";exit;
				$start_date = $date_share_min;
			}else if($date_loan_min < $date_loan_atm_min){
                //echo "3";exit;
				$start_date = $date_loan_min;
			}else if($date_loan_atm_min < $date_share_min){
                //echo "4";exit;
				$start_date = $date_loan_atm_min;
			}else{
                //echo "5";exit;
				$start_date = $date_share_min;
			}
			$end_date = $get_start_date;
		}else{		
			$start_date = $get_start_date;
			$end_date = $get_start_date;
		}

		$where_date = "";		
		$where_date_loan = "";		
		$where_date_loan_atm = "";		
		$where_date_loan_atm_transaction = "";		
		$where_date_loan_transaction = "";		
		if(@$param['start_date'] != ''){
			$where_date .= " AND coop_mem_share.share_date BETWEEN '".$start_date." 00:00:00.000' AND '".$end_date." 23:59:59.000'";
			$where_date_loan .= " AND coop_loan.createdatetime BETWEEN '".$start_date." 00:00:00.000' AND '".$end_date." 23:59:59.000'";
			$where_date_loan_atm .= " AND coop_loan_atm.createdatetime BETWEEN '".$start_date." 00:00:00.000' AND '".$end_date." 23:59:59.000'";
			$where_date_loan_atm_transaction .= " AND coop_loan_atm_transaction.transaction_datetime BETWEEN '".$start_date." 00:00:00.000' AND '".$end_date." 23:59:59.000'";
			$where_date_loan_transaction .= " AND coop_loan_transaction.transaction_datetime BETWEEN '".$start_date." 00:00:00.000' AND '".$end_date." 23:59:59.000'";
		}		

		$this->db->select(array('coop_loan_name.loan_name_id','coop_loan_type.loan_type_code'));
		$this->db->from('coop_loan_name');
		$this->db->join('coop_loan_type','coop_loan_name.loan_type_id = coop_loan_type.id','left');
		$rs_type_code = $this->db->get()->result_array();
		$arr_loan_type_code = array();
		foreach($rs_type_code AS $key_type_code=>$row_type_code){
			$arr_loan_type_code[@$row_type_code['loan_name_id']] = @$row_type_code['loan_type_code'];
		}
		
		$this->db->select(array('max_period'));
		$this->db->from('coop_loan_atm_setting');					
		$rs_atm_setting = $this->db->get()->result_array();
		$row_atm_setting = @$rs_atm_setting[0];
		$max_period_atm = $row_atm_setting['max_period'];

		$sql = "SELECT `coop_mem_apply`.`member_id`, `coop_mem_apply`.`prename_id`, `coop_mem_apply`.`firstname_th`, `coop_mem_apply`.`lastname_th`, `coop_mem_apply`.`department`, `coop_mem_apply`.`faction`, `coop_mem_apply`.`level`,
				`coop_prename`.`prename_full`,
				`t2`.`mem_group_id` as `id`, `t1`.`mem_group_name` as `name`,
				`t2`.`mem_group_name` as `sub_name`,
				`t3`.`mem_group_name` as `main_name`,
				`t4`.`share_collect`, `t4`.`share_collect_value`, `t4`.`share_id`, `t4`.`share_period`, `t4`.`share_date`,
				`t5`.`loan_id`, `t5`.`loan_amount_balance`, `t5`.`contract_number`, `t5`.`loan_type`,t5.period_now,
				`t6`.`loan_atm_id`, `t6`.`contract_number` AS `contract_number_atm`, `t6`.`loan_amount_balance_atm`
				FROM (SELECT IF (
								(SELECT level_old FROM coop_mem_group_move WHERE date_move >= '".$end_date."' AND coop_mem_group_move.member_id = coop_mem_apply.member_id ORDER BY date_move ASC LIMIT 1),
								(SELECT level_old FROM coop_mem_group_move WHERE date_move >= '".$end_date."' AND coop_mem_group_move.member_id = coop_mem_apply.member_id ORDER BY date_move ASC LIMIT 1),
								coop_mem_apply. level
							) AS level,
							IF (
								(SELECT faction_old FROM coop_mem_group_move WHERE date_move >= '".$end_date."' AND coop_mem_group_move.member_id = coop_mem_apply.member_id ORDER BY date_move ASC LIMIT 1),
								(SELECT faction_old FROM coop_mem_group_move WHERE date_move >= '".$end_date."' AND coop_mem_group_move.member_id = coop_mem_apply.member_id ORDER BY date_move ASC LIMIT 1),
								coop_mem_apply.faction
							) AS faction,
							IF (
								(SELECT department_old FROM coop_mem_group_move WHERE date_move >= '".$end_date."' AND coop_mem_group_move.member_id = coop_mem_apply.member_id ORDER BY date_move ASC LIMIT 1),
								(SELECT department_old FROM coop_mem_group_move WHERE date_move >= '".$end_date."' AND coop_mem_group_move.member_id = coop_mem_apply.member_id ORDER BY date_move ASC LIMIT 1),
								coop_mem_apply.department
							) AS department, member_id, prename_id, firstname_th, lastname_th,member_status, retry_date, mem_type FROM coop_mem_apply) AS coop_mem_apply
				LEFT JOIN `coop_prename` ON `coop_prename`.`prename_id` = `coop_mem_apply`.`prename_id`
				LEFT JOIN `coop_mem_group` as `t1` ON `t1`.`id` = `coop_mem_apply`.`level`
				LEFT JOIN `coop_mem_group` as `t2` ON `t2`.`id` = `t1`.`mem_group_parent_id`
				LEFT JOIN `coop_mem_group` as `t3` ON `t3`.`id` = `t2`.`mem_group_parent_id`
				LEFT JOIN (SELECT t1.share_id,t1.share_collect,t1.share_collect_value,t1.member_id,t1.share_period,t1.share_date FROM coop_mem_share AS t1 INNER JOIN (SELECT member_id,max(share_id) share_id FROM coop_mem_share WHERE share_date BETWEEN '".$start_date." 00:00:00.000' AND '".$end_date." 23:59:59.000' GROUP BY member_id) t2 ON t1.member_id=t2.member_id AND t1.share_id=t2.share_id) AS t4 ON `coop_mem_apply`.`member_id` = `t4`.`member_id`
				LEFT JOIN (SELECT t3.member_id ,t3.contract_number ,t3.period_now ,t3.loan_type 
								,t1.loan_transaction_id
								,t1.loan_id
								,t1.loan_amount_balance
								,t1.transaction_datetime FROM (SELECT t1.loan_transaction_id,t1.loan_id,t1.loan_amount_balance,t1.transaction_datetime FROM coop_loan_transaction t1 INNER JOIN (
SELECT max(t1.loan_transaction_id) loan_transaction_id,t1.loan_id FROM coop_loan_transaction t1 INNER JOIN (
SELECT loan_id,max(transaction_datetime) transaction_datetime FROM coop_loan_transaction WHERE transaction_datetime BETWEEN '".$start_date." 00:00:00.000' AND '".$end_date." 23:59:59.000' GROUP BY loan_id) t2 ON t1.loan_id=t2.loan_id AND t1.transaction_datetime=t2.transaction_datetime GROUP BY t1.loan_id) t2 ON t1.loan_transaction_id=t2.loan_transaction_id AND t1.loan_id=t2.loan_id
) AS t1 LEFT JOIN coop_loan AS t3 ON t1.loan_id = t3.id WHERE t1.transaction_datetime BETWEEN '".$start_date." 00:00:00.000' AND '".$end_date." 23:59:59.000' AND t1.loan_amount_balance > 0 GROUP BY t1.loan_id ORDER BY t1.loan_id DESC ,t1.loan_transaction_id DESC )
								AS t5 ON `coop_mem_apply`.`member_id` = `t5`.`member_id`
				LEFT JOIN (SELECT t3.member_id ,t3.contract_number
								,t1.loan_atm_transaction_id
								,t1.loan_atm_id
								,t1.loan_amount_balance as loan_amount_balance_atm
								FROM coop_loan_atm_transaction AS t1 LEFT JOIN coop_loan_atm AS t3 ON t1.loan_atm_id = t3.loan_atm_id WHERE t1.transaction_datetime BETWEEN '".$start_date." 00:00:00.000' AND '".$end_date." 23:59:59.000'
								GROUP BY t1.loan_atm_id ORDER BY t1.loan_atm_id DESC ,t1.loan_atm_transaction_id DESC ) AS t6
								ON `coop_mem_apply`.`member_id` = `t6`.`member_id`
				WHERE 1=1 AND ( coop_mem_apply.mem_type in (1, 4, 5, 8, 9)  OR (coop_mem_apply.member_status <> 3 AND  coop_mem_apply.retry_date > '".$end_date." 23:59:59.000')) AND coop_mem_apply.member_status != '2' 
				ORDER BY t2.mem_group_id ASC , coop_mem_apply.member_id ASC";
		$result = $this->db->query($sql)->result_array();
		//echo $this->db->last_query(); exit;
		$member_ids = array_column($result, 'member_id');
		if(@$param['dev']=='dev2'){
			print_r($this->db->last_query()); exit;
		}
		if(@$param['dev']=='dev'){
			print_r($this->db->last_query()); exit;
		}
		
		//Get Lastest Loan Information
		$loan_ids = array_column($result, 'loan_id');
        $where_loan = " 1=1 ";
        if(sizeof(array_filter($loan_ids))){
            $where_loan = " t1.loan_id IN  (".implode(',',array_filter($loan_ids)).") ";
        }
		$loans = $this->db->query("SELECT `t1`.`loan_transaction_id`, `t1`.`loan_id`, `t1`.`loan_amount_balance`, `t1`.`transaction_datetime`
									FROM `coop_loan_transaction` as `t1`
									INNER JOIN (SELECT loan_id, MAX(cast(transaction_datetime as Datetime)) as max FROM coop_loan_transaction WHERE transaction_datetime BETWEEN '".$start_date." 00:00:00' AND '".$end_date." 23:59:59' group by loan_id)
											as t2 ON `t1`.`loan_id` = `t2`.`loan_id` AND `t1`.`transaction_datetime` = `t2`.`max`
									WHERE {$where_loan}
									ORDER BY `t1`.`transaction_datetime`, `t1`.`loan_transaction_id` DESC
									")->result_array();
		$loan_members = array_column($loans, 'loan_id');
		//echo $this->db->last_query();exit;
		//Get Lastest Loan ATM Information
		$loan_atm_ids = array_column($result, 'loan_atm_id');
		$where_atm = " 1=1 ";
		if(sizeof(array_filter($loan_atm_ids))){
            $where_atm = " t1.loan_atm_id IN  (".implode(',',array_filter($loan_atm_ids)).") ";
        }

		$loan_atms = $this->db->query("SELECT t1.loan_atm_transaction_id, `t1`.`loan_atm_id`, `t1`.`transaction_datetime`,
									t1.loan_amount_balance AS loan_amount_balance
		
									FROM `coop_loan_atm_transaction` as `t1`
									INNER JOIN (SELECT loan_atm_id, MAX(cast(transaction_datetime as Datetime)) as max FROM coop_loan_atm_transaction WHERE transaction_datetime BETWEEN '".$start_date." 00:00:00' AND '".$end_date." 23:59:59' group by loan_atm_id)
											as t2 ON `t1`.`loan_atm_id` = `t2`.`loan_atm_id` AND `t1`.`transaction_datetime` = `t2`.`max`
									LEFT JOIN `coop_loan_atm_detail` AS `t3` ON `t1`.`loan_atm_id` = `t3`.`loan_atm_id`	AND `t1`.`transaction_datetime` = `t3`.`loan_date`
									LEFT JOIN `coop_finance_transaction` AS `t4` ON `t1`.`receipt_id` = `t4`.`receipt_id`	AND `t1`.`loan_atm_id` = `t4`.`loan_atm_id`
									LEFT JOIN coop_receipt AS t6 ON t1.receipt_id = t6.receipt_id
									WHERE ".$where_atm."
									GROUP BY `t1`.`loan_atm_id`
									ORDER BY `t1`.`transaction_datetime`, `t1`.`loan_atm_transaction_id` DESC
									")->result_array();

		$loan_atm_members = array_column($loan_atms, 'loan_atm_id');
		//echo $this->db->last_query();exit;
		$run_index = 0;

		$check_row = "xx";
		$index = 0;
		$row['data'] = array();
		$allCount = 0;

        $sql_shares = "SELECT t1.share_id,t1.share_collect,t1.share_collect_value,t1.share_payable,t1.share_payable_value,t1.member_id,t1.share_period,t1.share_date, t1.share_status FROM coop_mem_share AS t1 INNER JOIN (
SELECT t1.member_id,max(t1.share_id) share_id FROM coop_mem_share t1 INNER JOIN (SELECT member_id,max(share_date) share_date FROM coop_mem_share WHERE share_date BETWEEN '".$start_date." 00:00:00.000' AND '".$end_date." 23:59:59.000' GROUP BY member_id) t2 ON t1.member_id=t2.member_id AND t1.share_date=t2.share_date GROUP BY t1.member_id) t2 ON t1.member_id=t2.member_id AND t1.share_id=t2.share_id";
        $shares = $this->db->query($sql_shares)->result_array();
        $_shares = array();
        //echo $this->db->last_query(); exit;
        foreach ($shares as $key => $share){
            $_shares[$share['member_id']] = $share;
        }
        unset($shares);

        if(@$param['dev'] == "share"){
            echo "<pre>";
            print_r($_shares);
            exit;
        }

		foreach($result AS $key2=>$value2){
			if($check_row != @$value2['member_id']){
				$check_row = @$value2['member_id'];

				$shares = $_shares[$value2['member_id']];
				$share_period = (!empty($shares['share_period']))?@$shares['share_period']: "";		
				$check_share = (!empty($shares['check_share']))?@$shares['check_share']: "";
				if(@$shares['share_status'] == 3){
					$share_collect_value = (!empty($shares['share_payable_value']))?@$shares['share_payable_value']: "";
				}else{
					$share_collect_value = (!empty($shares['share_collect_value']))?@$shares['share_collect_value']: "";
				}
				
				$allCount += $runno;
				$runno = 1;
			}else{	
				$runno++;
			}
				$row['data'][$value2['member_id']][$runno] = $value2;

				$row['data'][$value2['member_id']][$runno]['mem_group_id'] = $value2['id'];
				$row['data'][$value2['member_id']][$runno]['mem_group_name_level'] = $value2['name'];
				if($value2->sub_name == 'ไม่ระบุ'){
					$row['data'][$value2['member_id']][$runno]['mem_group_name_sub'] = $value2['main_name'];
				}else{					
					$row['data'][$value2['member_id']][$runno]['mem_group_name_sub'] = $value2['sub_name'];
				}
					
				$row['data'][$value2['member_id']][$runno]['mem_group_name_main'] = $value2['main_name'];
				
				//หุ้น
				if ($runno == 1) {
					$row['data'][$value2['member_id']][$runno]['share_period'] = $share_period;
					$row['data'][$value2['member_id']][$runno]['share_collect'] = $share_collect_value;
				} else {
					$row['data'][$value2['member_id']][$runno]['share_period'] = "";
					$row['data'][$value2['member_id']][$runno]['share_collect'] = "";
				}

				$row['data'][$value2['member_id']][$runno]['runno'] = @$runno;
				
				$loan_type_code = @$arr_loan_type_code[$value2['loan_type']];

				if(@$loan_type_code == 'emergent' && @$value2['loan_amount_balance'] != ''
						&& in_array($value2['loan_id'],$loan_members) && !empty($loans[array_search($value2['loan_id'],$loan_members)]['loan_amount_balance']) ){
					//เงินกู้ฉุกเฉิน
					if ($runno == 1) {
						$row['data'][$value2['member_id']][$runno]['loan_emergent_period_now'] = @$value2['period_now'];
						$row['data'][$value2['member_id']][$runno]['loan_emergent_contract_number'] = @$value2['contract_number'];
						$row['data'][$value2['member_id']][$runno]['loan_emergent_balance'] = $loans[array_search($value2['loan_id'],$loan_members)]['loan_amount_balance'];
					} else {
						for($no_count = 1; $no_count <= $runno; $no_count++) {
							if (empty($row['data'][$value2['member_id']][$no_count]['loan_emergent_contract_number'])) {
								$row['data'][$value2['member_id']][$no_count]['loan_emergent_period_now'] = @$value2['period_now'];
								$row['data'][$value2['member_id']][$no_count]['loan_emergent_contract_number'] = @$value2['contract_number'];
								$row['data'][$value2['member_id']][$no_count]['loan_emergent_balance'] = $loans[array_search($value2['loan_id'],$loan_members)]['loan_amount_balance'];
								break;
							} else if ($row['data'][$value2['member_id']][$no_count]['loan_emergent_contract_number'] == $value2['contract_number']) {
								break;
							}
						}
					}
					$run_emergent++;
				}

				if(@$loan_type_code == 'normal' && @$value2['loan_amount_balance'] != ''
						&& in_array($value2['loan_id'],$loan_members) && !empty($loans[array_search($value2['loan_id'],$loan_members)]['loan_amount_balance'])){
					if ($runno == 1) {
						$row['data'][$value2['member_id']][$runno]['loan_normal_period_now'] = $value2['period_now'];
						$row['data'][$value2['member_id']][$runno]['loan_normal_contract_number'] = $value2['contract_number'];
						$row['data'][$value2['member_id']][$runno]['loan_normal_balance'] = $loans[array_search($value2['loan_id'],$loan_members)]['loan_amount_balance'];
                        $row['data'][$value2['member_id']][$runno]['loan_type_normal'] = $value2['loan_type'];
					} else {
						for($no_count = 1; $no_count <= $runno; $no_count++) {
							if ($row['data'][$value2['member_id']][$no_count]['loan_normal_contract_number'] == $value2['contract_number']) {
								break;
							} else if (empty($row['data'][$value2['member_id']][$no_count]['loan_normal_contract_number'])) {
								$row['data'][$value2['member_id']][$no_count]['loan_normal_period_now'] = @$value2['period_now'];
								$row['data'][$value2['member_id']][$no_count]['loan_normal_contract_number'] = @$value2['contract_number'];
								$row['data'][$value2['member_id']][$no_count]['loan_normal_balance'] = $loans[array_search($value2['loan_id'],$loan_members)]['loan_amount_balance'];
                                $row['data'][$value2['member_id']][$no_count]['loan_type_normal'] = $value2['loan_type'];
								break;
							}
						}
					}
					$run_normal++;
				}
				
				if(@$loan_type_code == 'special' && @$value2['loan_amount_balance'] != ''
						&& in_array($value2['loan_id'],$loan_members) && !empty($loans[array_search($value2['loan_id'],$loan_members)]['loan_amount_balance'])){
					if ($runno == 1) {
						$row['data'][$value2['member_id']][$runno]['loan_special_period_now'] = @$value2['period_now'];
						$row['data'][$value2['member_id']][$runno]['loan_special_contract_number'] = @$value2['contract_number'];
						$row['data'][$value2['member_id']][$runno]['loan_special_balance'] = $loans[array_search($value2['loan_id'],$loan_members)]['loan_amount_balance'];
					} else {
						for($no_count = 1; $no_count <= $runno; $no_count++) {
							if (empty($row['data'][$value2['member_id']][$no_count]['loan_special_contract_number'])) {
								$row['data'][$value2['member_id']][$no_count]['loan_special_period_now'] = @$value2['period_now'];
								$row['data'][$value2['member_id']][$no_count]['loan_special_contract_number'] = @$value2['contract_number'];
								$row['data'][$value2['member_id']][$no_count]['loan_special_balance'] = $loans[array_search($value2['loan_id'],$loan_members)]['loan_amount_balance'];
								break;
							} else if ($row['data'][$value2['member_id']][$no_count]['loan_special_contract_number'] == $value2['contract_number']) {
								break;
							}
						}
					}

					$run_special++;
				}

			if(@$loan_type_code == 'covid' && @$value2['loan_amount_balance'] != ''
				&& in_array($value2['loan_id'],$loan_members) && !empty($loans[array_search($value2['loan_id'],$loan_members)]['loan_amount_balance'])){
				if ($runno == 1) {
					$row['data'][$value2['member_id']][$runno]['loan_covid_period_now'] = $value2['period_now'];
					$row['data'][$value2['member_id']][$runno]['loan_covid_contract_number'] = $value2['contract_number'];
					$row['data'][$value2['member_id']][$runno]['loan_covid_balance'] = $loans[array_search($value2['loan_id'],$loan_members)]['loan_amount_balance'];
				} else {
					for($no_count = 1; $no_count <= $runno; $no_count++) {
						if ($row['data'][$value2['member_id']][$no_count]['loan_covid_contract_number'] == $value2['contract_number']) {
							break;
						} else if (empty($row['data'][$value2['member_id']][$no_count]['loan_covid_contract_number'])) {
							$row['data'][$value2['member_id']][$no_count]['loan_covid_period_now'] = @$value2['period_now'];
							$row['data'][$value2['member_id']][$no_count]['loan_covid_contract_number'] = @$value2['contract_number'];
							$row['data'][$value2['member_id']][$no_count]['loan_covid_balance'] = $loans[array_search($value2['loan_id'],$loan_members)]['loan_amount_balance'];
							break;
						}
					}
				}
				$run_covid++;
			}

				if(@$value2['loan_amount_balance_atm'] != ''
					&& in_array($value2['loan_atm_id'],$loan_atm_members) && !empty($loan_atms[array_search($value2['loan_atm_id'],$loan_atm_members)]['loan_amount_balance'])){
					//เงินกู้ฉุกเฉิน ATM
					$atm_index_count = $runno;
					if(!empty($row['data'][$value2['member_id']][$runno]['loan_emergent_contract_number'])) {
						$atm_index_count = $runno+1;
					}
					for($no_count = 1; $no_count <= $atm_index_count; $no_count++) {
						if (empty($row['data'][$value2['member_id']][$no_count]['loan_emergent_contract_number'])) {

							if ($no_count > $runno ) {
								$row['data'][$value2['member_id']][$no_count] = $value2;
							}
							$row['data'][$value2['member_id']][$no_count]['mem_group_id'] = $value2['id'];
							$row['data'][$value2['member_id']][$no_count]['mem_group_name_level'] = $value2['name'];
							if($value2->sub_name == '' || $value2->sub_name=='ไม่ระบุ'){
								$row['data'][$value2['member_id']][$no_count]['mem_group_name_sub'] = $value2['main_name'];
							}else{					
								$row['data'][$value2['member_id']][$no_count]['mem_group_name_sub'] = $value2['sub_name'];
							}
								
							$row['data'][$value2['member_id']][$no_count]['mem_group_name_main'] = $value2['main_name'];
							
							if ($runno == 1) {
								$row['data'][$value2['member_id']][$runno]['share_period'] = $share_period;
								$row['data'][$value2['member_id']][$runno]['share_collect'] = $share_collect_value;
							} else {
								$row['data'][$value2['member_id']][$runno]['share_period'] = "";
								$row['data'][$value2['member_id']][$runno]['share_collect'] = "";
							}

							$row['data'][$value2['member_id']][$no_count]['runno'] = $no_count;
							$row['data'][$value2['member_id']][$no_count]['loan_emergent_period_now'] = '';
							$row['data'][$value2['member_id']][$no_count]['loan_emergent_contract_number'] = @$value2['contract_number_atm'];
							$row['data'][$value2['member_id']][$no_count]['loan_emergent_balance'] = $loan_atms[array_search($value2['loan_atm_id'],$loan_atm_members)]['loan_amount_balance'];
							break;
						} else if ($row['data'][$value2['member_id']][$no_count]['loan_emergent_contract_number'] == $value2['contract_number_atm']) {
							break;
						}
					}	
				}
				$run_index++;
		}
		unset($result);
		//echo '<pre>'; print_r($row); echo '</pre>'; exit;
		//Generate Fund support Information
		$where_fund = "1=1";
		$where_fund_t1 = $param["type_date"] == 1 ? "payment_date <= '".$end_date." 23:59:59.000'" : "payment_date BETWEEN '".$end_date." 00:00:00.000' AND '".$end_date." 23:59:59.000'";
		$funds = $this->db->select("SUM(t2.principal) as loan_amount_balance, t5.member_id, t5.prename_id, t5.firstname_th, t5.lastname_th, t5.level, t7.id as faction, t8.id as department, t9.prename_full,
									t6.mem_group_id as id, t6.mem_group_name as name, t7.mem_group_name as sub_name, t8.mem_group_name as main_name, t4.id as loan_id, t4.contract_number, t4.loan_type, t4.period_now")
							->from("(SELECT *, MAX(payment_date) as max_date FROM coop_loan_fund_balance_transaction WHERE ".$where_fund_t1." GROUP BY sub_compromise_id) as t1")
							->join("coop_loan_fund_balance_transaction as t2", "t1.sub_compromise_id = t2.sub_compromise_id AND t1.max_date = t2.payment_date", "inner")
							->join("coop_loan_compromise as t3", "t2.compromise_id = t3.id", "inner")
							->join("coop_loan as t4", "t3.loan_id = t4.id", "inner")
							->join("(SELECT IF (
										(SELECT level_old FROM coop_mem_group_move WHERE date_move >= '".$end_date."' AND coop_mem_group_move.member_id = coop_mem_apply.member_id ORDER BY date_move ASC LIMIT 1),
										(SELECT level_old FROM coop_mem_group_move WHERE date_move >= '".$end_date."' AND coop_mem_group_move.member_id = coop_mem_apply.member_id ORDER BY date_move ASC LIMIT 1),
										coop_mem_apply. level
									) AS level, member_id, prename_id, firstname_th, lastname_th,member_status FROM coop_mem_apply) as t5", "t3.member_id = t5.member_id", "inner")
							->join("coop_mem_group as t6", "t5.level = t6.id", "left")
							->join("coop_mem_group as t7", "t7.id = t6.mem_group_parent_id", "left")
							->join("coop_mem_group as t8", "t8.id = t7.mem_group_parent_id", "left")
							->join("coop_prename as t9", "t5.prename_id = t9.prename_id", "left")
							->where($where_fund)
							->group_by("t2.compromise_id")
							->get()->result_array();

		foreach($funds as $fund) {
			if($fund["loan_amount_balance"] > 0) {
				$data_arr = array();
				$data_arr["member_id"] = $fund["member_id"];
				$data_arr["prename_id"] = $fund["prename_id"];
				$data_arr["firstname_th"] = $fund["firstname_th"];
				$data_arr["lastname_th"] = $fund["lastname_th"];
				$data_arr["department"] = $fund["department"];
				$data_arr["faction"] = $fund["faction"];
				$data_arr["level"] = $fund["level"];
				$data_arr["prename_full"] = $fund["prename_full"];
				$data_arr["id"] = $fund["id"];
				$data_arr["name"] = $fund["name"];
				$data_arr["sub_name"] = $fund["sub_name"];
				$data_arr["main_name"] = $fund["main_name"];
				$data_arr["loan_id"] = $fund["loan_id"];
				$data_arr['loan_amount_balance'] = $fund["loan_amount_balance"];
				$data_arr["contract_number"] = $fund["contract_number"];
				$data_arr["loan_type"] = $fund["loan_type"];
				$data_arr["period_now"] = $fund["period_now"];
				$data_arr['mem_group_id'] = $fund["id"];
				$data_arr['mem_group_name_level'] = $fund["level"];
				$data_arr["mem_group_name_sub"] = $fund["faction"];
				$data_arr["mem_group_name_main"] = $fund["department"];
				$data_arr["loan_normal_period_now"] = $fund["period_now"];
				$data_arr["loan_normal_contract_number"] = $fund["contract_number"];
				$data_arr["loan_normal_balance"] = $fund["loan_amount_balance"];
				$row['data'][$fund["member_id"]][] = $data_arr;
			}
		}
		//$allCount = count($row['data']);
		//echo '<pre>'; print_r($row); echo '</pre>'; exit;
		$arr_data['num_rows'] = $row['num_rows'];
		$arr_data['data'] = $row['data'];
		$arr_data['data_count'] = $allCount+1;
		$arr_data['i'] = $i;
		
		$this->db->select(array('id','loan_type','loan_type_code'));
		$this->db->from('coop_loan_type');
		$this->db->order_by("order_by");
		$row = $this->db->get()->result_array();
		$arr_data['loan_type'] = $row;
		
		$arr_data['month_arr'] = $this->center_function->month_arr();
		$arr_data['month_short_arr'] = $this->center_function->month_short_arr();
			
		return $arr_data;		
	}
}
