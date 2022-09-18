<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Run_script extends CI_Controller {
	function __construct()
	{
		parent::__construct();
	}
	
	public function index(){
		exit;
	}
	
	function run_script_deposit_interest(){
		echo "Process...<br>";
		$this->db->select(array(
			'account_id',
			'type_id',
			'mem_id',
			'created as create_account_date'
		));
		$this->db->from('coop_maco_account');
		//$this->db->where("t2.account_status = '0'");
		//$this->db->where("account_status = '0' AND type_id IN ('12', '8', '19', '10', '14', '19', '21', '11', '13', '22', '24', '3', '4', '20', '1', '2', '5', '6', '7', '15', '16', '17')");
		//   73,  64,  80,  71,  75,   80,  82,   72,  74,  83,  84,  23, 24, 81, 01, 21, 26, 27, 28, 76,  77,  78
		//$this->db->where("account_status = '0' AND type_id IN ('12', '8', '19', '10', '14', '19', '21', '11', '13', '22', '24', '3', '4', '20', '15', '16', '17', '29', '30', '31', '32')");
		//   73,  64,  80,  71,  75,   80,  82,   72,  74,  83,  84,  23, 24,  81,  76,  77,  78,   85,   86,  87,   88
		//$this->db->where("account_status = '0' AND type_id IN ('1', '2', '5', '6', '7', '9')");
																								//  01, 21, 26, 27, 28, 69

		$this->db->where("account_status = '0' AND type_id IN ('1','2','3','4','5','6','7','8','9','11','13','100') ");
		//$this->db->where("account_status = '0' AND type_id IN ('2','5','8','9') ");
		$rs_member = $this->db->get()->result_array();
		//echo"<pre>";print_r($rs_member);echo"</pre>"; //exit;
		foreach($rs_member as $key_member => $row_member){
			$this->deposit_libraries->user_id = 'SYSTEM';
			//$this->deposit_libraries->testmode = true;
			$this->deposit_libraries->cal_deposit_interest($row_member, 'cal_interest', '', '');
		}
		echo "Completed.";
		exit;
	}
	
	function run_script_deposit_interest2(){
		echo "Process...<br>";
		$this->db->select(array(
			'account_id',
			'type_id',
			'mem_id',
			'created as create_account_date'
		));
		$this->db->from('coop_maco_account');
		$this->db->where("account_status = '0' AND type_id IN ('1', '2', '5', '6', '7', '9')");
																								//  01, 21, 26, 27, 28, 69
		$rs_member = $this->db->get()->result_array();
		//echo"<pre>";print_r($rs_member);echo"</pre>"; //exit;
		foreach($rs_member as $key_member => $row_member){
			$this->deposit_libraries->user_id = 'SYSTEM';
			//$this->deposit_libraries->testmode = true;
			$this->deposit_libraries->cal_deposit_interest($row_member, 'cal_interest', '', '');
		}
		echo "Completed.";
		exit;
	}

	public function test_result_interest(){
		ini_set("precision", 11);
		$except_results = $this->db->get('except_results')->result_array();
		$test_result = array();
		$i = 0;
		foreach ($except_results as $except_result){
			$data = $this->deposit_libraries->cal_deposit_interest_by_acc_date($except_result['account_id'], $except_result['interest_to']);
			if($_GET['dev'] == 'dev') {
				echo "<pre>";
				print_r($data);
			}
			$test_result[$i]['test_result'] = $data['interest'];
			$test_result[$i]['id'] = $except_result['id'];
			$i++;
		}
		if($_GET['dev'] == 'dev'){
			echo "<pre>";
			print_r($test_result);
			exit;
		}
		$this->db->update_batch('except_results', $test_result, 'id');
		if($this->db->affected_rows()){
			echo "success.";
		}

	}


	
	function run_script_deposit_interest_test(){
		echo "Process...<br>";
		$this->db->select(array(
			'account_id',
			'type_id',
			'mem_id',
			'created as create_account_date'
		));
		$this->db->from('coop_maco_account');
		//$this->db->where("account_status = '0' AND type_id IN ('12', '8', '19', '10', '14', '19', '21', '11', '13', '22', '24', '3', '4', '20', '1', '2', '5', '6', '7', '15', '16', '17')");
																								//   73,  64,  80,  71,  75,   80,  82,   72,  74,  83,  84,  23, 24, 81, 01, 21, 26, 27, 28, 76,  77,  78
		//$this->db->where("account_status = '0' AND type_id IN ('1', '2', '5', '6', '7', '9')");
																								//  01, 21, 26, 27, 28, 69
		$this->db->where("account_id = '0005000115'");
		$this->db->where("account_status = '0'");

		$rs_member = $this->db->get()->result_array();
		//echo"<pre>";print_r($rs_member);echo"</pre>"; //exit;
		foreach($rs_member as $key_member => $row_member){
			//$this->deposit_libraries->cal_deposit_interest($row_member, 'cal_interest', '', '');
			$this->deposit_libraries->user_id = 'SYSTEM';
			$this->deposit_libraries->debug = true;
			$this->deposit_libraries->testmode = true;
			//$this->deposit_libraries->cal_deposit_interest($row_member, 'cal_interest', '2019-04-25 05:00:00', '25', ''); // Data @2018-10-06
			$this->deposit_libraries->cal_deposit_interest($row_member, 'cal_interest', '2020-06-30 05:00:00', '', '');
		}
		echo "Completed.";
		exit;
	}

	function run_script_deposit_interest_test_result(){
		$this->db->select(array(
			'account_id',
			'type_id',
			'mem_id',
			'created as create_account_date'
		));
		$this->db->from('coop_maco_account');
		$this->db->where("account_status = '0' AND type_id = '2' ");
		$rs_member = $this->db->get()->result_array();
		foreach($rs_member as $key_member => $row_member){
			$this->deposit_libraries->cal_deposit_interest_by_acc_date($row_member, '2020-06-30 05:00:00');
		}
	}
	
	function run_script_deposit_interest_forcast(){
		echo "Process...<br>";
		$this->deposit_libraries->cal_deposit_interest_forecast("2019-10-02 05:00:00", "00173002541");
		echo "Completed.";
		exit;
	}
	
	function run_script_deposit_interest_update(){
		echo "Process...<br>";
		
		$mode = "debug";	// debug, test, real
		//$date_interest = date('Y-m-d');
		$date_interest = "2019-01-10";
		$transaction_time = $date_interest." 17:30:00";
		
		$this->db->select("account_id");
		$this->db->from("coop_maco_account");
		//$this->db->where("account_status = '0' AND type_id IN ('19')");
		$this->db->where("account_id = '00180000248'");
		$rs = $this->db->get()->result_array();
		foreach($rs as $row){
			$this->deposit_libraries->user_id = 'SYSTEM';
			$this->deposit_libraries->debug = true;
			$this->deposit_libraries->testmode = true;

			$data = $this->deposit_libraries->cal_deposit_interest_by_acc_date($row["account_id"], $date_interest);
			
			if($mode == "debug") {
				echo"<pre>*** RETURN ***</pre>";
				echo"<pre>";print_r($data);echo"</pre>";
				echo"<pre>*** END RETURN ***</pre>";
			}
			elseif($mode == "test" || $mode == "real") {
				$this->db->select("transaction_balance, transaction_no_in_balance");
				$this->db->from("coop_account_transaction as t1");
				$this->db->where("account_id = '".$row["account_id"]."' AND transaction_time <= '".$transaction_time."'");
				$this->db->order_by("transaction_time DESC, transaction_id DESC");
				$this->db->limit(1);
				$row_balance = $this->db->get()->row_array();
				$balance = $row_balance["transaction_balance"];
				$interest = number_format($data["interest"],2,'.','');
				$balance += $interest;
				
				$data_insert = array();
				$data_insert['transaction_time'] = $transaction_time;
				$data_insert['transaction_list'] = 'IN';
				$data_insert['transaction_withdrawal'] = '';
				$data_insert['transaction_deposit'] = $interest;
				$data_insert['transaction_balance'] = number_format($balance,2,'.','');
				$data_insert['transaction_no_in_balance'] = '';
				$data_insert['user_id'] = 'SYSTEM';
				$data_insert['account_id'] = $row["account_id"];
				
				if($mode == "real") writeToLog("insert: ".json_encode($data_insert), FCPATH."/application/logs/cal_dep_int.log", true);
				$this->db->insert($mode == "real" ? 'coop_account_transaction' : 'coop_account_transaction_tmp', $data_insert);
			}
		}
		
		echo "Completed.";
	}
	
	function run_script_deposit_due(){
		echo "Process...<br>";
		
		$start_date = date('Y-m-d');
		//$start_date = '2018-10-27';
		
		$where = "account_status = '0' AND transaction_list IN ('OPN', 'OPT', 'TRB', 'XD', 'DEP', 'CD') AND
						TIMESTAMPDIFF(MONTH, transaction_time, '".$start_date."') +
						DATEDIFF('".$start_date."', transaction_time + INTERVAL TIMESTAMPDIFF(MONTH, transaction_time, '".$start_date."') MONTH) /
						DATEDIFF(transaction_time + INTERVAL TIMESTAMPDIFF(MONTH, transaction_time, '".$start_date."') + 1 MONTH, transaction_time + INTERVAL TIMESTAMPDIFF(MONTH, transaction_time, '".$start_date."') MONTH) = max_month";
		if(!empty($_GET['type_id'])) $where .= " AND t1.type_id = '".$_GET['type_id']."'";

		$row = $this->db->select(array(
							't1.account_id'))
						->from('coop_maco_account as t1')
						->join("coop_account_transaction t2", "t1.account_id = t2.account_id", "inner")
						->join("(SELECT type_id, type_name, type_code, (
										SELECT max_month
										FROM coop_deposit_type_setting_detail
										WHERE coop_deposit_type_setting_detail.type_id = coop_deposit_type_setting.type_id AND start_date <= '".$start_date."'
										ORDER BY start_date
										LIMIT 1) AS max_month
									FROM coop_deposit_type_setting
									WHERE coop_deposit_type_setting.type_id IN (SELECT type_id FROM coop_deposit_type_setting_detail WHERE condition_age IN (1, 2))) t3", "t1.type_id = t3.type_id", "inner")
						->where($where)
						->limit(1)
						->get()->row_array();
		if(!empty($row)) {
			$this->db->insert('coop_notification', array(
				"notification_id" => "3",
				"notification_title" => "ระบบเงินฝาก",
				"notification_text" => "มีเงินฝากครบกำหนด @".$this->center_function->mydate2date($start_date),
				"notification_datetime" => date('Y-m-d H:i:s'),
				"notification_status" => "1",
				"notification_link" => PROJECTPATH.'/report_deposit_data/coop_report_account_due_preview?start_date='.$this->center_function->mydate2date($start_date)
			));
		}
		
		echo "Completed.";
		exit;
	}
	
	function run_script_holiday_setting_alert(){
		echo "Process...<br>";
		
		$y = date('Y') + 1;
		$m = date('m');
		if($m == 12) {
			$row = $this->db->select("*")
							->from("coop_calendar_work")
							->where("work_year = '{$y}'")
							->limit(1)
							->get()->row_array();
			if(empty($row["work_year"])) {
				$this->db->insert('coop_notification', array(
					"notification_id" => "4",
					"notification_title" => "วันหยุดสหกรณ์",
					"notification_text" => "ยังไม่ได้ตั้งค่าวันหยุดสหกรณ์ ปี ".($y + 543),
					"notification_datetime" => date('Y-m-d H:i:s'),
					"notification_status" => "1",
					"notification_link" => PROJECTPATH.'/setting_basic_data/coop_holiday'
				));
			}
		}
		
		echo "Completed.";
		exit;
	}
	
	function run_script_deposit_interest_bk(){
		$this->db->select(array('*'));
		$this->db->from('coop_deposit_type_setting');
		$this->db->where("type_id = '3'");
		$row = $this->db->get()->result_array();
		//echo"<pre>";print_r($row);echo"</pre>";
		foreach($row as $key_acc => $value_acc){
			$this->db->select(array('*'));
			$this->db->from('coop_deposit_type_setting_detail');
			$this->db->where("type_id = '".$value_acc['type_id']."' AND start_date <= '".date('Y-m-d')."'");
			$this->db->order_by("start_date DESC");
			$this->db->limit(1);
			$row_detail = $this->db->get()->result_array();
			$row_detail = @$row_detail[0];
			if(empty($row_detail)){continue;}
			$this->db->select(array(
				't1.transaction_id',
				't1.transaction_balance', 
				't1.transaction_time',
				't1.account_id',
				't2.type_id'
			));
			$this->db->from('coop_account_transaction as t1');
			$this->db->join('coop_maco_account as t2','t1.account_id = t2.account_id','inner');
			$this->db->where("type_id = '".$value_acc['type_id']."'");
			$this->db->order_by('t1.account_id ASC, t1.transaction_time ASC');
			$row_transaction = $this->db->get()->result_array();
			$transaction = array();
			foreach($row_transaction as $key => $value){
				$transaction[$key]['type_id'] = $value['type_id'];
				$transaction[$key]['transaction_id'] = $value['transaction_id'];
				$transaction[$key]['date_start'] = date('Y-m-d',strtotime($value['transaction_time']));
				$transaction[$key]['transaction_balance'] = $value['transaction_balance'];
				$transaction[$key]['account_id'] = $value['account_id'];
				if(@$row_transaction[($key+1)]['transaction_time'] != '' && @$row_transaction[($key+1)]['account_id'] == $value['account_id']){
					$transaction[$key]['date_end'] = date('Y-m-d',strtotime($row_transaction[($key+1)]['transaction_time']));
				}else{
					$transaction[$key]['date_end'] = date('Y-m-d');
				}
			}
			
			if($row_detail['condition_interest'] == '1'){
				$this->db->select(array(
					't1.type_detail_id',
					't1.type_id',
					't1.start_date',
					't2.percent_interest as interest_rate'
				));
				$this->db->from('coop_deposit_type_setting_detail as t1');
				$this->db->join('coop_deposit_type_setting_interest as t2','t1.type_detail_id = t2.type_detail_id AND t1.condition_interest = t2.condition_interest','inner');
				$this->db->where("t1.type_id = '".@$value_acc['type_id']."'");
				$this->db->order_by("start_date ASC");
				$row_interest_rate = $this->db->get()->result_array();
				//echo $this->db->last_query();
				
				foreach($row_interest_rate as $key2 => $value2){
					if(@$row_interest_rate[($key2+1)]['start_date'] != ''){
						$row_interest_rate[$key2]['end_date'] = date('Y-m-d',strtotime($row_interest_rate[($key2+1)]['start_date']));
					}else{
						$row_interest_rate[$key2]['end_date'] = date('Y-m-d');
					}
				}
				//echo"<pre>";print_r($row_interest_rate);echo"</pre>";
				$i=0;
				$transaction_new = array();
				foreach($transaction as $key => $value){
					$transaction_new[$i] = $value;
					foreach($row_interest_rate as $key2 => $value2){
						if(strtotime($value['date_start']) >= strtotime($value2['start_date']) && strtotime($value['date_start']) <= strtotime($value2['end_date'])){
							if(strtotime($value['date_start']) > strtotime($value2['start_date'])){
								$transaction_new[$i]['interest_rate'] = $value2['interest_rate'];
							}
							if(strtotime($value['date_end']) > strtotime($value2['end_date'])){
								$transaction_new[$i]['date_end'] = $value2['end_date'];
								
								$i += 1;
								$transaction_new[$i] = $value;
								$transaction_new[$i]['date_start'] = $value2['end_date'];
								$transaction_new[$i]['interest_rate'] = $row_interest_rate[($key2+1)]['interest_rate'];
							}
						}
					}
					$i++;
				}
				$transaction = $transaction_new;
				
				$transaction_new = array();
				foreach($transaction as $key => $value){
					$transaction_new[$value['account_id']][$key] = $value;
				}
				$transaction = $transaction_new;
			}else if($row_detail['condition_interest'] == '2'){
				$this->db->select(array(
					't1.type_detail_id',
					't1.type_id',
					't1.start_date',
					't2.num_month',
					't2.percent_interest as interest_rate'
				));
				$this->db->from('coop_deposit_type_setting_detail as t1');
				$this->db->join('coop_deposit_type_setting_interest as t2','t1.type_detail_id = t2.type_detail_id AND t1.condition_interest = t2.condition_interest','inner');
				$this->db->where("t1.type_id = '".@$value_acc['type_id']."' AND t2.percent_interest != '' AND t2.num_month != ''");
				$this->db->order_by("start_date ASC, t2.num_month ASC");
				$row_interest_rate = $this->db->get()->result_array();
				echo $this->db->last_query();
				foreach($row_interest_rate as $key2 => $value2){
					
				}
			}
			echo "<pre>";print_r($transaction);echo"</pre>";exit;
			foreach($transaction as $key => $value){
				foreach($transaction[$key] as $key2 => $value2){
					$interest_rate = @$value2['interest_rate'];
					$diff = @date_diff(date_create($value2['date_start']),date_create($value2['date_end']));
					$date_count = @$diff->format("%a");
					$date_count = $date_count+1;
					$transaction[$key][$key2]['date_count'] = $date_count;
					$interest = ((($value2['transaction_balance']*@$interest_rate)/100)*$date_count)/365;
					$transaction[$key][$key2]['interest'] = $interest;
					@$transaction[$key]['interest'] += $interest;
				}
			}
			
			//echo $row_member['member_id']." : ".$row_member['account_id']." : ".$account_interest."<br>";
			//echo"<pre>";print_r($transaction);echo"</pre>";exit;
			//echo $interest_sum;
			foreach($transaction as $key => $value){
				$interest = number_format($value['interest'],2,'.','');
				if($interest > 0){
					$this->db->select(array(
						'transaction_balance'
					));
					$this->db->from('coop_account_transaction as t1');
					$this->db->where("account_id = '".$key."'");
					$this->db->order_by('transaction_time DESC');
					$this->db->limit(1);
					$row_balance = $this->db->get()->result_array();
					$row_balance = @$row_balance[0];
					$balance     = $row_balance["transaction_balance"];

					$sum = $balance + $interest;
					
					$data_insert = array();
					$data_insert['transaction_time'] = date('Y-m-d H:i:s');
					$data_insert['transaction_list'] = 'IN';
					$data_insert['transaction_withdrawal'] = '';
					$data_insert['transaction_deposit'] = $interest;
					$data_insert['transaction_balance'] = $sum;
					$data_insert['user_id'] = '1';
					$data_insert['account_id'] = $key;
					$this->db->insert('coop_account_transaction', $data_insert);
					//$sum_account_interest += $value['interest'];
					
					$data['coop_account']['account_description'] = "ดอกเบี้ยเงินฝาก เลขที่บัญชี ".$key;
					$data['coop_account']['account_datetime'] = date('Y-m-d H:i:s');
					
					$i=0;
					$data['coop_account_detail'][$i]['account_type'] = 'debit';
					$data['coop_account_detail'][$i]['account_amount'] = $interest;
					$data['coop_account_detail'][$i]['account_chart_id'] = '50100';
					$i++;
					$data['coop_account_detail'][$i]['account_type'] = 'credit';
					$data['coop_account_detail'][$i]['account_amount'] = $interest;
					$data['coop_account_detail'][$i]['account_chart_id'] = '10100';
					$this->account_transaction->account_process($data);
				}
			}
		}
		exit;
	}

	function run_script_deposit_interest_bk2(){
		$this->db->select(array(
			't1.member_id',
			't2.account_id',
			't2.type_id',
			't2.created as create_account_date'
		));
		$this->db->from('coop_mem_apply as t1');
		$this->db->join('coop_maco_account as t2','t1.member_id = t2.mem_id','inner');
		$rs_member = $this->db->get()->result_array();
		//echo"<pre>";print_r($rs_member);echo"</pre>"; //exit;
		foreach($rs_member as $key_member => $row_member){
			$this->db->select(array('*'));
			$this->db->from('coop_deposit_type_setting_detail');
			$this->db->where("type_id = '".$row_member['type_id']."' AND start_date <= '".date('Y-m-d')."'");
			$this->db->order_by("start_date DESC");
			$this->db->limit(1);
			$row_detail = $this->db->get()->result_array();
			$row_detail = @$row_detail[0];
			//echo $row_member['member_id'].'<hr>';
			if(empty($row_detail)){continue;}
			
			//เช็คการจ่ายดอกเบี้ย
			if($row_detail['pay_interest'] == '1'){
				//จ่ายทุกสิ้นเดือน
				if(date('Y-m-d') != date('Y-m-t')){
					continue;
				}
			}else if($row_detail['pay_interest'] == '2'){
				//จ่ายทุกเดือน ตามวันที่ครบกำหนด
				$create_account_day = date('d',strtotime($row_member['create_account_date']));
				//echo $create_account_day;exit;
			}
			//echo $row_detail['pay_interest'].'<hr>';
			
			$this->db->select(array(
				't1.transaction_id',
				't1.transaction_balance', 
				't1.transaction_time',
				't1.account_id',
				't1.transaction_list'
			));
			$this->db->from('coop_account_transaction as t1');
			$this->db->join('coop_maco_account as t2','t1.account_id = t2.account_id','inner');
			$this->db->where("t2.account_id = '".$row_member['account_id']."'");
			$this->db->order_by('t1.transaction_time ASC');
			$row_transaction = $this->db->get()->result_array();
			//echo $this->db->last_query();
			$transaction = array();
			foreach($row_transaction as $key => $value){
				$transaction[$key]['type_id'] = $row_member['type_id'];
				$transaction[$key]['create_account_date'] = $row_member['create_account_date'];
				$transaction[$key]['date_begin'] = date('Y-m-01',strtotime($row_member['create_account_date']));
				$transaction[$key]['transaction_list'] = $value['transaction_list'];
				$transaction[$key]['transaction_id'] = $value['transaction_id'];
				$transaction[$key]['date_start'] = date('Y-m-d',strtotime($value['transaction_time']));
				$transaction[$key]['transaction_balance'] = $value['transaction_balance'];
				$transaction[$key]['account_id'] = $value['account_id'];
				if(@$row_transaction[($key+1)]['transaction_time'] != '' && @$row_transaction[($key+1)]['account_id'] == $value['account_id']){
					$transaction[$key]['date_end'] = date('Y-m-d',strtotime($row_transaction[($key+1)]['transaction_time']));
				}else{
					$transaction[$key]['date_end'] = date('Y-m-d');
				}
			}
			//echo"<pre>";print_r($transaction);echo"</pre>";
			if($row_detail['condition_interest'] == '1'){
				$this->db->select(array(
					't1.type_detail_id',
					't1.type_id',
					't1.start_date',
					't2.percent_interest as interest_rate'
				));
				$this->db->from('coop_deposit_type_setting_detail as t1');
				$this->db->join('coop_deposit_type_setting_interest as t2','t1.type_detail_id = t2.type_detail_id AND t1.condition_interest = t2.condition_interest','inner');
				$this->db->where("t1.type_id = '".@$row_member['type_id']."'");
				$this->db->order_by("start_date ASC");
				$row_interest_rate = $this->db->get()->result_array();
				//echo $this->db->last_query();
				
				foreach($row_interest_rate as $key2 => $value2){
					if(@$row_interest_rate[($key2+1)]['start_date'] != ''){
						$row_interest_rate[$key2]['end_date'] = date('Y-m-d',strtotime($row_interest_rate[($key2+1)]['start_date']));
					}else{
						$row_interest_rate[$key2]['end_date'] = date('Y-m-d');
					}
				}
				//echo"<pre>";print_r($row_interest_rate);echo"</pre>";
				$i=0;
				$transaction_new = array();
				foreach($transaction as $key => $value){
					$transaction_new[$i] = $value;
					foreach($row_interest_rate as $key2 => $value2){
						if(strtotime($value['date_start']) >= strtotime($value2['start_date']) && strtotime($value['date_start']) <= strtotime($value2['end_date'])){
							if(strtotime($value['date_start']) > strtotime($value2['start_date'])){
								$transaction_new[$i]['interest_rate'] = $value2['interest_rate'];
							}
							if(strtotime($value['date_end']) > strtotime($value2['end_date'])){
								$transaction_new[$i]['date_end'] = $value2['end_date'];
								
								$i += 1;
								$transaction_new[$i] = $value;
								$transaction_new[$i]['date_start'] = $value2['end_date'];
								$transaction_new[$i]['interest_rate'] = $row_interest_rate[($key2+1)]['interest_rate'];
							}
						}
					}
					$i++;
				}
				$transaction = $transaction_new;
				
			}else if($row_detail['condition_interest'] == '2'){
				$this->db->select(array(
					't1.type_detail_id',
					't1.type_id',
					't1.start_date',
					't2.num_month',
					't2.percent_interest as interest_rate'
				));
				$this->db->from('coop_deposit_type_setting_detail as t1');
				$this->db->join('coop_deposit_type_setting_interest as t2','t1.type_detail_id = t2.type_detail_id AND t1.condition_interest = t2.condition_interest','inner');
				$this->db->where("t1.type_id = '".@$row_member['type_id']."' AND t2.percent_interest != '' AND t2.num_month != ''");
				$this->db->order_by("start_date ASC, t2.num_month ASC");
				$row_interest_rate = $this->db->get()->result_array();
				//echo $this->db->last_query();
				
				foreach($transaction as $key => $value){
					for($i=0;$i<=500;$i++){
						$date_interesting = date('Y-m-01',strtotime('+'.$i.' month',strtotime($value['date_begin'])));
						if(date('Y-m-01',strtotime($value['date_start'])) == $date_interesting){
							$transaction[$key]['month_count'] = $i+1;
							break;
						}
					}
					foreach($row_interest_rate as $key2 => $value2){
						if($transaction[$key]['month_count'] >= $value2['num_month']){
							$transaction[$key]['interest_rate'] = $value2['interest_rate'];
						}
					}
				}
			}else if($row_detail['condition_interest'] == '3'){
				$this->db->select(array(
					't1.type_detail_id',
					't1.type_id',
					't1.start_date',
					't2.amount_deposit',
					't2.percent_interest as interest_rate'
				));
				$this->db->from('coop_deposit_type_setting_detail as t1');
				$this->db->join('coop_deposit_type_setting_interest as t2','t1.type_detail_id = t2.type_detail_id AND t1.condition_interest = t2.condition_interest','inner');
				$this->db->where("t1.type_id = '".@$row_member['type_id']."' AND t2.percent_interest != '' AND t2.amount_deposit != ''");
				$this->db->order_by("start_date ASC, t2.amount_deposit DESC");
				$row_interest_rate = $this->db->get()->result_array();
				//echo $this->db->last_query();
				$max_interest_rate = 0;
				foreach($transaction as $key => $value){
					foreach($row_interest_rate as $key2 => $value2){
						if($transaction[$key]['transaction_balance'] <= $value2['amount_deposit']){
							$transaction[$key]['interest_rate'] = $value2['interest_rate'];
						}
						if($value2['interest_rate'] > $max_interest_rate){
							$max_interest_rate = $value2['interest_rate'];
						}
					}
					if(@$transaction[$key]['interest_rate'] == ''){
						$transaction[$key]['interest_rate'] = $max_interest_rate;
					}
				}
			}
			
			foreach($transaction as $key => $value){
				$interest_rate = @$value['interest_rate'];
				$diff = @date_diff(date_create($value['date_start']),date_create($value['date_end']));
				$date_count = @$diff->format("%a");
				$date_count = $date_count+1;
				$transaction[$key]['date_count'] = $date_count;
				$interest = ((($value['transaction_balance']*@$interest_rate)/100)*$date_count)/365;
				$transaction[$key]['interest'] = $interest;
				$transaction['account_id'] = $value['account_id'];
				@$transaction['interest'] += $interest;
			}
			//echo"<pre>";print_r($transaction);echo"</pre>";
			
			if(@$transaction['account_id'] != '' && @$transaction['interest'] > 0){
				$interest = number_format($transaction['interest'],2,'.','');
				if($interest > 0){
					$this->db->select(array(
						'transaction_balance',
						'transaction_no_in_balance'
					));
					$this->db->from('coop_account_transaction as t1');
					$this->db->where("account_id = '".$transaction['account_id']."'");
					$this->db->order_by('transaction_time DESC');
					$this->db->limit(1);
					$row_balance = $this->db->get()->result_array();
					$row_balance = @$row_balance[0];
					$balance = $row_balance["transaction_balance"];
					$balance_no_interest = $row_balance["transaction_no_in_balance"];

					$sum = $balance + $interest;
					
					$data_insert = array();
					$data_insert['transaction_time'] = date('Y-m-d H:i:s');
					$data_insert['transaction_list'] = 'IN';
					$data_insert['transaction_withdrawal'] = '';
					$data_insert['transaction_deposit'] = $interest;
					$data_insert['transaction_balance'] = number_format($sum,2,'.','');
					$data_insert['transaction_no_in_balance'] = $balance_no_interest;
					$data_insert['user_id'] = '1';
					$data_insert['account_id'] = $transaction['account_id'];
					$this->db->insert('coop_account_transaction', $data_insert);
					//$sum_account_interest += $value['interest'];
					
					$data['coop_account']['account_description'] = "ดอกเบี้ยเงินฝาก เลขที่บัญชี ".$transaction['account_id'];
					$data['coop_account']['account_datetime'] = date('Y-m-d H:i:s');
					
					$i=0;
					$data['coop_account_detail'][$i]['account_type'] = 'debit';
					$data['coop_account_detail'][$i]['account_amount'] = $interest;
					$data['coop_account_detail'][$i]['account_chart_id'] = '50100';
					$i++;
					$data['coop_account_detail'][$i]['account_type'] = 'credit';
					$data['coop_account_detail'][$i]['account_amount'] = $interest;
					$data['coop_account_detail'][$i]['account_chart_id'] = '10100';
					$this->account_transaction->account_process($data);
				}
			}
			
			
		}
		exit;
	}
	
	function run_script_update_loan_atm_setting_now(){
		echo "Process...<br>";
		$this->loan_libraries->update_loan_atm_setting_now();
		echo "Completed.";
		exit;
	}
	
	function run_script_deposit_balance_update(){
		echo "Process...<br>";
		
		$this->db->select(array(
			'account_id',
			'type_id',
			'mem_id',
			'created as create_account_date'
		));
		$this->db->from('coop_maco_account');
		$this->db->where("account_status = '0' AND type_id IN ('19')");
		$rs = $this->db->get()->result_array();
		foreach($rs as $key => $row){
			$this->_post_url("https://{$_SERVER["HTTP_HOST"]}/save_money/update_transaction_balance", [
				"account_id" => $row["account_id"],
				"date" => "2019-04-10"
			]);
		}
		
		echo "Completed.";
		exit;
	}
	
	function _post_url($url, $data) {
		$options = array(
			'http' => array(
				'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
				'method'  => 'POST',
				'timeout' => 30,
				'content' => http_build_query($data)
			),
			'ssl' => array(
				'verify_peer' => false,
				'verify_peer_name' => false,
			)
		);
		$context = stream_context_create($options);
		return file_get_contents($url, false, $context);
	}
}
