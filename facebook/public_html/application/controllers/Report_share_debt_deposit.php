<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Report_share_debt_deposit extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
	}

	public function report_share_debt_deposit()
	{
		$arr_data = array();
		$this->db->select(array('id', 'mem_group_name'));
		$this->db->from('coop_mem_group');
		$this->db->where("mem_group_type = '1'");
		$row = $this->db->get()->result_array();
		$arr_data['row_mem_group'] = $row;
		$arr_data['month_arr'] = $this->center_function->month_arr();
		$arr_data['month_short_arr'] = $this->center_function->month_short_arr();

		$this->libraries->template('report_share_debt_deposit/report_share_debt_deposit', $arr_data);
	}
	public function report_share_debt_deposit_pdf()
	{
		$arr_data = array();

		$this->db->select(array('id', 'mem_group_name'));
		$this->db->from('coop_mem_group');
		$this->db->where("mem_group_type = '1' AND ");
		$row = $this->db->get()->result_array();
		$arr_data['row_mem_group'] = $row;

		$arr_data['month_arr'] = $this->center_function->month_arr();
		$arr_data['month_short_arr'] = $this->center_function->month_short_arr();

		$this->libraries->template('report_share_debt_deposit/report_share_debt_deposit_pdf', $arr_data);
	}

	function report_share_debt_deposit_person_pdf(){
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

		$this->preview_libraries->template_preview('report_share_debt_deposit/report_share_debt_deposit_person_pdf',$arr_data);

	}

	function report_share_debt_deposit_excel(){
		ini_set('memory_limit', -1);
		set_time_limit (-1);

		$datas = $this->get_share_loan_balance_person($_GET);
		$arr_data = $datas;

		$this->load->view('report_share_debt_deposit/report_share_debt_deposit_excel',$arr_data);
	}

	function get_share_loan_balance_person($param){
		ini_set('memory_limit', -1);
		set_time_limit(-1);
		//$this->db->save_queries = FALSE;=
		if(@$param['start_date']){
			$start_date_arr = explode('/',urldecode(@$param['start_date']));
			$start_day = $start_date_arr[0];
			$start_month = $start_date_arr[1];
			$start_year = $start_date_arr[2];
			$start_year -= 543;
			$get_start_date = $start_year.'-'.$start_month.'-'.$start_day;
		}

		$whereMember = "1=1 ";
		if(!empty($_GET["member_id"])) {
			$whereMember .= " AND member_id = '".$_GET["member_id"]."'";
		}
		if(!empty($_GET['level'])){
			$whereMember .= " AND level = '".$_GET['level']."'";
		}else if(!empty($_GET['faction'])){
			$whereMember .= " AND faction = '".$_GET['faction']."'";
		}else if(!empty($_GET['department'])){
			$whereMember .= " AND department = '".$_GET['department']."'";
		}
		//echo "<pre>"; print_r($whereMember);exit;

		if(@$param['type_date'] = '1'){
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
		$where_date_account_atm = "";
		$where_date_account = "";
		if(@$param['start_date'] != ''){
			$where_date .= " AND coop_mem_share.share_date BETWEEN '".$start_date." 00:00:00.000' AND '".$end_date." 23:59:59.000'";
			$where_date_loan .= " AND coop_loan.createdatetime BETWEEN '".$start_date." 00:00:00.000' AND '".$end_date." 23:59:59.000'";
			$where_date_loan_atm .= " AND coop_loan_atm.createdatetime BETWEEN '".$start_date." 00:00:00.000' AND '".$end_date." 23:59:59.000'";
			$where_date_loan_atm_transaction .= " AND coop_loan_atm_transaction.transaction_datetime BETWEEN '".$start_date." 00:00:00.000' AND '".$end_date." 23:59:59.000'";
			$where_date_loan_transaction .= " AND coop_loan_transaction.transaction_datetime BETWEEN '".$start_date." 00:00:00.000' AND '".$end_date." 23:59:59.000'";
			$where_date_account_atm .= " AND coop_loan_transaction.transaction_datetime BETWEEN '".$start_date." 00:00:00.000' AND '".$end_date." 23:59:59.000'";
			$where_date_account .= " AND coop_account_transaction.transaction_time BETWEEN '".$start_date." 00:00:00.000' AND '".$end_date." 23:59:59.000'";
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

		$sql = "SELECT `coop_mem_apply`.`member_id`, `coop_mem_apply`.`prename_id`, `coop_mem_apply`.`address_send_doc`, `coop_mem_apply`.`firstname_th`, `coop_mem_apply`.`lastname_th`, `coop_mem_apply`.`department`, `coop_mem_apply`.`faction`, `coop_mem_apply`.`level`,
				`coop_prename`.`prename_full`,
				`t1`.`mem_group_id` as `id`, 
				`t1`.`mem_group_name` as `name`,
				`t2`.`mem_group_name` as `sub_name`,
				`t3`.`mem_group_name` as `main_name`,
				`t4`.`share_collect`, `t4`.`share_collect_value`, `t4`.`share_id`, `t4`.`share_period`, `t4`.`share_date`,
				`t5`.`loan_id`, `t5`.`loan_amount_balance`, `t5`.`contract_number`, `t5`.`loan_type`,t5.period_now,
				`t6`.`loan_atm_id`, `t6`.`contract_number` AS `contract_number_atm`, `t6`.`loan_amount_balance_atm`,
				t7.account_id,m1.transaction_balance,m1.account_id
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
							) AS department, member_id, prename_id, firstname_th, lastname_th,member_status, retry_date, mem_type,address_send_doc FROM coop_mem_apply WHERE ".$whereMember.") AS coop_mem_apply
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
				LEFT JOIN (SELECT account_id, mem_id FROM coop_maco_account WHERE account_status = 0 GROUP BY mem_id  ) AS t7 ON t7.mem_id = `coop_mem_apply`.`member_id`
				LEFT JOIN (SELECT account_id,transaction_balance,transaction_time FROM coop_account_transaction ORDER BY transaction_time ASC) AS m1 ON m1.account_id = t7.account_id
				WHERE 1=1  AND ( coop_mem_apply.mem_type in (1, 4, 5, 8, 9)  OR (coop_mem_apply.member_status <> 3 AND  coop_mem_apply.retry_date > '".$end_date." 23:59:59.000')) AND coop_mem_apply.member_status != '2' 
				ORDER BY  CAST(t1.mem_group_id AS int) ASC, coop_mem_apply.member_id ASC";
		// ORDER BY  coop_mem_apply.address_send_doc,coop_mem_apply.level ,t2.mem_group_id ,coop_mem_apply.member_id ASC,m1.transaction_time DESC
		$result = $this->db->query($sql)->result_array();
		if($_GET['dev'] == 'last_query') {
            echo $this->db->last_query();
            exit;
        }


//		foreach($result as $key=>$value){
//			$this->db->select("account_id,transaction_balance ");
//			$this->db->from("coop_account_transaction");
//			$this->db->where("account_id = '".$value['account_id']."'");
//			$this->db->order_by("transaction_time DESC ");
//			$arr1 = $this->db->get()->row_array();
//			$result[$key]['transaction_balance'] = $arr1["transaction_balance"];
//		}
		//echo $this->db->last_query();exit;
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
		//   เงินฝาก
		$account_ids = array_column($result, 'account_id');
		$implode_acc = implode(',',array_filter($account_ids));
		if (!empty($implode_acc)) {
			$accounts = $this->db->query("SELECT t2.transaction_id, `t2`.`account_id`, `t2`.`transaction_balance`, `t2`.`transaction_time`, t4.type_name, t3.account_status
											FROM (SELECT account_id, transaction_id, transaction_balance, transaction_time
													FROM coop_account_transaction
													WHERE transaction_time <= '".$end_date." 23:59:59.000' AND account_id IN  (".implode(',',array_filter($account_ids)).")
													ORDER BY transaction_time DESC)
													as t2
											LEFT JOIN coop_maco_account as t3 ON t2.account_id = t3.account_id
											LEFT JOIN coop_deposit_type_setting as t4 ON t4.type_id = t3.type_id
											WHERE t3.account_status = 0
											ORDER BY t2.transaction_time,`t2`.`account_id` DESC 
										")->result_array();
			$account_members = array_column($accounts,'transaction_balance','account_id');
		}//echo $this->db->last_query();exit;
		//echo '<pre>'; print_r($account_members); echo '</pre>'; exit;

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

		$sql_shares = "SELECT t1.share_id,t1.share_collect,t1.share_collect_value,t1.share_payable,t1.share_payable_value,t1.member_id,t1.share_period,t1.share_date, t1.share_status FROM coop_mem_share AS t1 
		INNER JOIN (SELECT t1.member_id,max(t1.share_id) share_id FROM coop_mem_share t1 INNER JOIN (SELECT member_id,max(share_date) share_date 
		FROM coop_mem_share WHERE share_date BETWEEN '".$start_date." 00:00:00.000' AND '".$end_date." 23:59:59.000' GROUP BY member_id) t2 ON t1.member_id=t2.member_id
		AND t1.share_date=t2.share_date GROUP BY t1.member_id) t2 ON t1.member_id=t2.member_id AND t1.share_id=t2.share_id";
		$shares = $this->db->query($sql_shares)->result_array();
		$_shares = array();
//		echo $this->db->last_query(); exit;
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

			$run_index++;
		}
		unset($result);
		//echo '<pre>'; print_r($row); echo '</pre>'; exit;
		//Generate Fund support Information
		$where_fund = "1=1";
		$where_fund_t1 = $param["type_date"] == 1 ? "payment_date <= '".$end_date." 23:59:59.000'" : "payment_date BETWEEN '".$end_date." 00:00:00.000' AND '".$end_date." 23:59:59.000'";
		$funds = $this->db->select("SUM(t2.principal) as loan_amount_balance, t5.member_id, t5.prename_id, t5.firstname_th, t5.lastname_th, t5.level,t5.address_send_doc, t7.id as faction, t8.id as department, t9.prename_full,
									t6.mem_group_id as id, t6.mem_group_name as name, t7.mem_group_name as sub_name, t8.mem_group_name as main_name, t4.id as loan_id, t4.contract_number, t4.loan_type, t4.period_now,m2.account_id,m1.transaction_balance")
			->from("(SELECT *, MAX(payment_date) as max_date FROM coop_loan_fund_balance_transaction WHERE ".$where_fund_t1." GROUP BY sub_compromise_id) as t1")
			->join("coop_loan_fund_balance_transaction as t2", "t1.sub_compromise_id = t2.sub_compromise_id AND t1.max_date = t2.payment_date", "inner")
			->join("coop_loan_compromise as t3", "t2.compromise_id = t3.id", "inner")
			->join("coop_loan as t4", "t3.loan_id = t4.id", "inner")
			->join("(SELECT IF (
										(SELECT level_old FROM coop_mem_group_move WHERE date_move >= '".$end_date."' AND coop_mem_group_move.member_id = coop_mem_apply.member_id ORDER BY date_move ASC LIMIT 1),
										(SELECT level_old FROM coop_mem_group_move WHERE date_move >= '".$end_date."' AND coop_mem_group_move.member_id = coop_mem_apply.member_id ORDER BY date_move ASC LIMIT 1),
										coop_mem_apply. level
									) AS level, member_id, prename_id, firstname_th, lastname_th,member_status,address_send_doc FROM coop_mem_apply) as t5", "t3.member_id = t5.member_id", "inner")
			->join("coop_mem_group as t6", "t5.level = t6.id", "left")
			->join("coop_mem_group as t7", "t7.id = t6.mem_group_parent_id", "left")
			->join("coop_mem_group as t8", "t8.id = t7.mem_group_parent_id", "left")
			->join("coop_prename as t9", "t5.prename_id = t9.prename_id", "left")
			->join("coop_maco_account  as m2", "t5.member_id = m2.mem_id", "left")
			->join("coop_account_transaction as m1", "m2.account_id = m1.account_id", "left")
			->where($where_fund)
			->group_by("t2.compromise_id")
			->get()->result_array();

//		echo $this->db->last_query();exit;

		foreach($funds as $fund) {

			//echo"<pre>";print_r($fund);exit;
			if($fund["loan_amount_balance"] > 0) {//echo"<pre>";print_r($fund["account_id"]);exit;
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
				$data_arr["account_id"] = $fund["account_id"];
				//$data_arr["transaction_balance"] = $account_members[$fund["account_id"]];
				$data_arr["address_send_doc"] = $fund["address_send_doc"];
				$row['data'][$fund["member_id"]][] = $data_arr;
//				exit;

			}
		}
//		echo "<pre>";print_r($row['data']);exit;
		//$allCount = count($row['data']);
//		exit;
//		echo '<pre>'; print_r($row); echo '</pre>'; exit;
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
		$arr_data['max_data'] = count($arr_data['data']);
		return $arr_data;
	}

}

