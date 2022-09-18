<?php if( ! defined('BASEPATH')) exit('No direct script access allowed');

class Deposit_chooses_model extends CI_Model {
	public $user_id;
	public $debug = false;
	public $testmode = false;
	
	public function __construct()
	{
		parent::__construct();
		//$this->load->database();
		# Load libraries
		//$this->load->library('parser');
		$this->load->helper(array('html', 'url', 'ub_log'));
		$this->user_id = $_SESSION['USER_ID'];
	}
	
	public function cal_deposit_interest($row_member,$return_type = 'test_cal_interest',$transaction_id ,$money_withdrawal , $date_interest = '', $day_interest = '', $param_date_dep = ''){
		//$return_type = 'cal_interest';
		$is_process = false;
		$is_cal = true;
		$is_due = false;
		if($date_interest == ''){
			$date_interest = date('Y-m-d');
			$time_interest = date('H:i:s');
		}
		else {
			$is_process = true;
			$_tmp = explode(" ", $date_interest);
			$date_interest = $_tmp[0];
			$time_interest = $_tmp[1];
		}
		
		if($day_interest == ''){
			$day_interest = date('d');
		}
		
		$date_dep = $param_date_dep == '' ? $date_interest : $param_date_dep;

		$this->db->select(array('*'));
		$this->db->from('coop_deposit_type_setting_detail');
		$this->db->where("type_id = '".$row_member['type_id']."' AND start_date <= '".$date_interest."'");
		$this->db->order_by("start_date DESC");
		$this->db->limit(1);
		$row_detail = $this->db->get()->result_array();
		$row_detail = @$row_detail[0];

		//echo $this->db->last_query(); exit;
		//หาอัตราดอกเบี้ย
		/*$this->db->select(array(
			't1.type_detail_id',
			't1.type_id',
			't1.start_date',
			't2.num_month',
			't2.percent_interest as interest_rate'
		));
		$this->db->from('coop_deposit_type_setting_detail as t1');
		$this->db->join('coop_deposit_type_setting_interest as t2','t1.type_detail_id = t2.type_detail_id AND t1.condition_interest = t2.condition_interest','inner');
		$this->db->where("t1.type_id = '".@$row_member['type_id']."' AND start_date <= '".$date_interest."'");
		$this->db->order_by("start_date DESC");
		$this->db->limit(1);
		$row_interest_rate = $this->db->get()->row_array();
		//
		
		echo $this->db->last_query();
		*/
		if(empty($row_detail)){return false;}	

		$this->db->select("
							transaction_time,
							transaction_list,
							transaction_deposit,
							transaction_withdrawal,
							transaction_balance,
							balance_deposit,
							balance_deposit_int,
							ref_account_no");
		$this->db->from('coop_account_transaction');
		$this->db->where("account_id = '".$row_member['account_id']."' AND transaction_id = '".$transaction_id."'");
		$this->db->order_by("transaction_time ASC, transaction_id ASC");
		$_rs = $this->db->get();
		//echo $this->db->last_query(); exit;
		$transaction = array();
		$key = 0;
		foreach($_rs->result_array() as $value) {
			//วันที่ครบกำหนด due ล่าสุด
			$row_last_due = $this->db->select('transaction_time')->from('coop_account_transaction')->where("account_id = '".$row_member['account_id']."' AND ref_account_no = '".$value['ref_account_no']."' AND transaction_list = 'DFX'")->order_by("transaction_time DESC")->limit(1)->get()->row_array();
			//วันที่ทำรายการล่าสุด
			$row_last_transaction = $this->db->select('transaction_time,balance_deposit,balance_deposit_int,transaction_id')->from('coop_account_transaction')->where("account_id = '".$row_member['account_id']."' AND ref_account_no = '".$value['ref_account_no']."'")->order_by("transaction_time DESC")->limit(1)->get()->row_array();		
			$transaction_time_last = (@$row_last_due['transaction_time'] != '')?@$row_last_due['transaction_time']:@$row_last_transaction['transaction_time'];

			//หาอัตราดอกเบี้ย
			$row_interest_rate = $this->check_interest_rate($row_member['type_id'],$date_interest, $transaction_time_last);
			$interest_rate = $this->get_interest_rate($row_member['type_id'], $transaction_time_last, $date_interest);
			$tmp_date_interest = $transaction_time_last;
			while(strtotime($tmp_date_interest) < strtotime($date_interest)){
				$is_found = false;
				$tmp_interest_rate = 0;
				foreach($interest_rate as $k => $v){
					if( strtotime($tmp_date_interest) >= strtotime($v['start_date']) ){
						$tmp_interest_rate = $v['interest_rate'];
					}else{
						// echo "END DATE ".date("Y-m-d", strtotime("-1 day", strtotime($v['start_date'])))." ".$v['start_date']." !== ".$tmp_date_interest;
						$is_found = true;
						$new_date = date('Y-m-d',strtotime($v['start_date']));
						$transaction[$key]['_date_start'] = $transaction_time_last;
						$transaction[$key]['_date_end'] = $date_interest;
						$transaction[$key]['type_id'] = $row_member['type_id'];
						$transaction[$key]['type_id'] = $row_member['type_id'];
						$transaction[$key]['create_account_date'] = $row_member['create_account_date'];
						$transaction[$key]['date_begin'] = date('Y-m-d',strtotime($value['transaction_time']));
						$transaction[$key]['transaction_list'] = $value['transaction_list'];
						$transaction[$key]['transaction_id'] = $value['transaction_id'];
						$transaction[$key]['ref_transaction_id'] = $value['transaction_id'];
						$transaction[$key]['date_start'] = date('Y-m-d',strtotime($tmp_date_interest));
						$transaction[$key]['transaction_deposit'] = $value['transaction_deposit'];
						$transaction[$key]['transaction_balance'] = $money_withdrawal;
						$transaction[$key]['account_id'] = $row_member['account_id'];
						$transaction[$key]['interest_period'] = $value['period'];
						$transaction[$key]['date_end'] = $new_date;
						$transaction[$key]['deposit_interest_balance'] = $value['transaction_balance'];
						$transaction[$key++]['interest_rate'] = $tmp_interest_rate;
						$tmp_date_interest = $new_date;
						break;
					}
				}

				if($is_found == false){
					$transaction[$key]['_date_start'] = $transaction_time_last;
					$transaction[$key]['_date_end'] = $date_interest;
					$transaction[$key]['type_id'] = $row_member['type_id'];
					$transaction[$key]['create_account_date'] = $row_member['create_account_date'];
					$transaction[$key]['date_begin'] = date('Y-m-d',strtotime($value['transaction_time']));
					$transaction[$key]['transaction_list'] = $value['transaction_list'];
					$transaction[$key]['transaction_id'] = $value['transaction_id'];
					$transaction[$key]['ref_transaction_id'] = $value['transaction_id'];
					$transaction[$key]['date_start'] = date('Y-m-d',strtotime($new_date));
					$transaction[$key]['transaction_deposit'] = $value['transaction_deposit'];
					$transaction[$key]['transaction_balance'] = $money_withdrawal;
					$transaction[$key]['account_id'] = $row_member['account_id'];
					$transaction[$key]['interest_period'] = $value['period'];
					$transaction[$key]['date_end'] = $date_interest;
					$transaction[$key]['deposit_interest_balance'] = $value['transaction_balance'];
					$transaction[$key++]['interest_rate'] = $interest_rate[sizeof($interest_rate)-1]['interest_rate'];
					break;
				}
			}
		}
		//ไว้เช็คการถอนเงิน แล้วคิดดอกเบี้ยแบบขั้นบันได
		if($row_detail['condition_interest'] == '1'){			
		
		}else if($row_detail['condition_interest'] == '2'){
			
		}else if($row_detail['condition_interest'] == '3'){
			
		}
		
		if($row_detail['sub_condition_interest'] == '1') {
			$this->db->select(array(
				't1.type_detail_id',
				't1.type_id',
				't1.start_date',
				't2.amount_deposit',
				't2.percent_interest as interest_rate'
			));
			$this->db->from('coop_deposit_type_setting_detail as t1');
			$this->db->join('coop_deposit_type_setting_interest as t2','t1.type_detail_id = t2.type_detail_id AND t1.condition_interest = t2.condition_interest','inner');
			$this->db->where("t1.type_id = '".@$row_member['type_id']."' AND t2.percent_interest IS NOT NULL AND t2.amount_deposit IS NOT NULL");
			$this->db->order_by("start_date ASC, t2.amount_deposit ASC");
			$interest_rates = $this->db->get()->result_array();
		}
		foreach($transaction as $key => $value){
			$all_interest = 0;
			$interest_rate = @$value['interest_rate'];
			//$interest_rate = 2;
			$diff = @date_diff(date_create($value['_date_start']),date_create($value['_date_end']));
			$date_count_all = @$diff->format("%a");
			$date_count_all = $date_count_all;
			$transaction[$key]['date_count'] = @date_diff(date_create($value['date_start']),date_create($value['date_end']))->format("%a");
			//$day_of_year = date("z", strtotime(substr($value['date_start'], 0, 4)."-12-31")) + 1;
			
			//เช็ควันของแต่ละปี 2 step
			$array_check_date = $this->check_two_step($value['date_start'],$value['date_end']);
			//echo '<pre>'; print_r($array_check_date); echo '</pre>';
			//exit;
			foreach($array_check_date AS $key_chk=>$val_chk){
				$date_count = @$val_chk['date_count'];
				$day_of_year = @$val_chk['day_of_year'];
				
				// ฝากไม่ถึง  x เดือน ไม่คิดดอกเบี้ย คิดเป็น วัน
				$num_month_no_interest = $row_detail['num_month_no_interest']*30;
				$num_month_before = $row_detail['num_month_before']*30;
				
				//เช็คดอกเบี้ยตามจำนวนเดือนของเงินฝาก
				//if(!empty($row_detail['percent_depositor']) && !empty($row_detail['num_month_before']) && $date_count < $num_month_before) {
				if(!empty($row_detail['percent_depositor']) && !empty($row_detail['num_month_before']) && $date_count_all < $num_month_before) {
						$interest_rate = $row_detail['percent_depositor'];
				}
				
				//if($row_detail['type_interest'] == '3' &&  $date_count < $num_month_no_interest){
				if($row_detail['type_interest'] == '3' &&  $date_count_all < $num_month_no_interest){
					//คิดดอกเบี้ย ไม่คิดดอกเบี้ย เมื่อ ฝากไม่ถึง  x เดือน ไม่คิดดอกเบี้ย
					$interest = 0;
				}else if($row_detail['type_interest'] == '2' || $row_detail['type_interest'] == '4'){
					//คิดดอกเบี้ย ดอกเบี้ยเฉพาะเงินต้น
					if($row_detail['sub_condition_interest'] == '1') {
						$interest = 0;
						$interest_index = 0;
						$cal_balance = $transaction[$key]['transaction_balance'];
						
						foreach($interest_rates as $key2 => $value2){
							if($interest_rates[$key2]['amount_deposit'] > 0) {
								$_is_cal_interest = true;
								if($transaction[$key]['transaction_balance'] >= $interest_rates[$key2]['amount_deposit'] && $transaction[$key]['transaction_balance'] < $interest_rates[$key2 + 1]['amount_deposit']) {
									$cal_val = $cal_balance;
								}
								elseif($transaction[$key]['transaction_balance'] >= $interest_rates[$key2]['amount_deposit'] && $transaction[$key]['transaction_balance'] >= $interest_rates[$key2 + 1]['amount_deposit']) {
									if($interest_index == 0) {
										$cal_val = $interest_rates[$key2 + 1]['amount_deposit'] - 1;
									}
									else {
										$cal_val = $interest_rates[$key2 + 1]['amount_deposit'] - $interest_rates[$key2]['amount_deposit'];
										$cal_val = isset($interest_rates[$key2 + 1]) ? $cal_val : $cal_balance;
									}
								}
								else {
									$_is_cal_interest = false;
								}
								
								if($_is_cal_interest) {
									$interest += ((($cal_val*$interest_rates[$key2]['interest_rate'])/100)*$date_count)/$day_of_year;
									if($this->debug) { echo "interest_index: {$interest_index} : {$transaction[$key]['transaction_balance']} : {$cal_balance} : {$cal_val} : {$interest_rates[$key2]['interest_rate']} : {$interest} : {$date_count}<br>"; }
									$cal_balance -= $cal_val;
								}
								
								$interest_index++;
							}
							
						}
					}
					else {
						$interest = ((($value['transaction_balance']*@$interest_rate)/100)*$date_count)/$day_of_year;
					}
				}else{
					//คิดดอกเบี้ย ดอกเบี้ยทบต้น
					$interest = ((($value['transaction_balance']*@$interest_rate)/100)*$date_count)/$day_of_year;
					// echo '((('.$value['transaction_balance'].'*'.@$interest_rate.')/100)*'.$date_count.')/'.$day_of_year.'<br>';
				}
				
				//$transaction[$key]['interest'] = number_format($interest,2,'.','');		
				// echo "ADDED ".$interest."<br>";		
				$all_interest += ROUND($interest,2);
			}
			$transaction[$key]['interest'] = number_format($all_interest,2,'.','');
			//echo"<pre>";print_r($transaction);echo"</pre>"; exit;
			$diff = @date_diff(date_create($value['depositor_date_start']),date_create($value['date_end']));
			$date_count = @$diff->format("%a");
			$day_of_year = date("z", strtotime(substr($value['depositor_date_start'], 0, 4)."-12-31")) + 1;
			$interest_depositor = empty($row_detail["percent_depositor"]) ? 0 : ((($value['depositor_balance']*$row_detail["percent_depositor"])/100)*$date_count)/$day_of_year;
			$interest_depositor_sum = $interest_depositor + $value['interest_depositor_sum'];
			$transaction[$key]['interest_depositor_sum'] = number_format($interest_depositor_sum,2,'.','');
		}
		
		if($this->debug) { echo"<pre>";print_r($transaction);echo"</pre>"; }
		
		/*if($fix_saving === false){
			$transaction_tmp = array();
			foreach($transaction as $key => $value){
				$transaction_tmp[$value['date_start']] = $value;
			}
			$transaction = $transaction_tmp;
		}*/
		$interest_all = 0;
		$account_id = '';
		foreach($transaction as $key => $value){
			$account_id = $value['account_id'];
			@$interest_all += $value['interest'];
		}
		
		if($return_type == 'return_interest'){
			if(in_array($row_detail['pay_interest'], array('3', '1', '2','4'))) {
				$interest_return = 0;
				
				if($row_detail['pay_interest'] == '2') {
					//$interest_all = 0;
					foreach($transaction as $key => $value){
						if($row_detail['max_month'] != $value['interest_period']) {
							//$interest_all += 0 - ($value['transaction_balance'] + $value['interest'] - $value['transaction_deposit'] - $value['interest_depositor_sum']);
							//$interest_all += $value['interest'];
							$interest_return += $value['transaction_balance'] + $value['interest'] - $value['transaction_deposit'] - $value['interest_depositor_sum'];
						}
					}
					
					if(!empty($row_detail['percent_depositor'])) {
						//$interest_all = round($interest_all * $row_detail['percent_depositor'] / $interest_rate, 2);
						if($interest_rate != 0) {
							//$interest_return = round($interest_return * ($interest_rate - $row_detail['percent_depositor']) / $interest_rate, 2);
						}
					}
				}
				else {
					if($row_detail['type_interest'] == '4') {
						if($row_detail['staus_maturity'] == '1') {
							$this->db->select("transaction_id");
							$this->db->from("coop_account_transaction");
							$this->db->where("account_id = '".$row_member['account_id']."' AND transaction_time <= DATE_ADD('".$date_interest." 23:59:59', INTERVAL -".($row_detail['maturity_num_year'] * 12)." MONTH)");
							$this->db->order_by("transaction_time, transaction_id");
							$this->db->limit(1);
							if(!($row_chk = $this->db->get()->row_array())) {
								$this->db->select("SUM(transaction_deposit) AS return_interest");
								$this->db->from("coop_account_transaction");
								$this->db->where("account_id = '".$row_member['account_id']."' AND transaction_list IN ('INT', 'IN')");
								$row_return = $this->db->get()->row_array();
								$interest_return = $row_return['return_interest'] + $interest_all;
							}
						}
					}
					
					if(!empty($row_detail['percent_depositor'])) {
						if($interest_rate != 0) {
							$interest_return = round($interest_all * ($interest_rate - $row_detail['percent_depositor']) / $interest_rate, 2);
						}
					}
				}
				
				//คำนวณภาษีของดอกเบี้ย
				$tax_all = @$interest_all*@$row_detail['tax_rate']/100;
				$tax_all = number_format($tax_all,2,'.','');
				
				return array(
					"interest" => $interest_all,
					"tax" => $tax_all,
					//"interest_return" => $interest_return,
					//"percent_depositor" => $row_detail['percent_depositor'],
					"detail" => $transaction
				);
			}
		}
	}
	
	function cal_deposit_interest_by_acc_date($transaction_id, $account_id, $money_withdrawal, $date_interest){
		$this->db->select(array(
			'account_id',
			'type_id',
			'mem_id',
			'created as create_account_date'
		));
		$this->db->from("coop_maco_account");
		$this->db->where("account_id = '".$account_id."'");
		$row = $this->db->get()->row_array();
		$data = $this->cal_deposit_interest($row, "return_interest", $transaction_id, $money_withdrawal, $date_interest);
		return $data;
	}
	
	//เช็ควันของแต่ละปี 2 step
	function check_two_step($date_start,$date_end){
		$arr_data = array();
		$src_year = (date("z", strtotime(substr($date_start, 0, 4)."-12-31")) + 1);
		$des_year = (date("z", strtotime(substr($date_end, 0, 4)."-12-31")) + 1);
		if($src_year == $des_year){
			$diff = @date_diff(date_create($date_start),date_create($date_end));
			$date_count = @$diff->format("%a");
			$arr_data[0]['date_count'] = $date_count;
			$arr_data[0]['day_of_year'] = $src_year;	
		}else{			
			$date_middle = date("Y", strtotime($date_end)).'-01-01';
			
			$diff_one = @date_diff(date_create($date_start),date_create($date_middle));
			$date_count_one  = @$diff_one->format("%a");
			$arr_data[0]['date_count'] = $date_count_one;
			$arr_data[0]['day_of_year'] = $src_year;			
			
			$diff_two = @date_diff(date_create($date_middle),date_create($date_end));
			$date_count_two  = @$diff_two->format("%a");
			$arr_data[1]['date_count'] = $date_count_two;
			$arr_data[1]['day_of_year'] = $des_year;			
		}
		
		return $arr_data;
	}
	
	//หาอัตราดอกเบี้ยจากวันที่ครบ  due ล่าสุด
	function check_interest_rate($type_id,$date_interest,$date_last_due){
		$arr_data = array();
	
		$this->db->select(array(
			't1.type_detail_id',
			't1.type_id',
			't1.start_date',
			't2.num_month',
			't2.percent_interest as interest_rate'
		));
		$this->db->from('coop_deposit_type_setting_detail as t1');
		$this->db->join('coop_deposit_type_setting_interest as t2','t1.type_detail_id = t2.type_detail_id AND t1.condition_interest = t2.condition_interest','inner');
		$this->db->where("t1.type_id = '".@$type_id."' AND start_date <= '".$date_last_due."'");
		$this->db->order_by("start_date DESC");
		$this->db->limit(1);

		$arr_data = $this->db->get()->row_array();
		//echo $this->db->last_query(); exit;

		return $arr_data;
	}

	//หาอัตราดอกเบี้ย ตั้งแต่วันเริ่มจนถึงวันที่สิ้นสุด คิดดอกเบี้ย
	function get_interest_rate($type_id, $start_date, $end_date){
		$this->db->select(array(
			't1.type_detail_id',
			't1.type_id',
			't1.start_date',
			't2.num_month',
			't2.percent_interest as interest_rate'
		));
		$this->db->from('coop_deposit_type_setting_detail as t1');
		$this->db->join('coop_deposit_type_setting_interest as t2','t1.type_detail_id = t2.type_detail_id AND t1.condition_interest = t2.condition_interest','inner');
		$this->db->where("t1.type_id = '".$type_id."' AND (start_date <= '".$end_date."')");
		$this->db->order_by("start_date ASC");
		$interest_rate = $this->db->get()->result_array();
		return $interest_rate;
	}
}
