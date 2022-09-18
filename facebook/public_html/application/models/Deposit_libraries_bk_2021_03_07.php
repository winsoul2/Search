<?php if( ! defined('BASEPATH')) exit('No direct script access allowed');

class Deposit_libraries extends CI_Model {
	public $user_id;
	public $debug = false;
	public $testmode = false;
	public $forecast = "";
	public $close = false;
	
	public function __construct()
	{
		parent::__construct();
		//$this->load->database();
		# Load libraries
		//$this->load->library('parser');
		$this->load->helper(array('html', 'url', 'ub_log'));
		$this->user_id = $_SESSION['USER_ID'];
	}
	
	private function _is_holiday($date) {
		$day = date("N", strtotime($date));
		$y = date("Y", strtotime($date));
		$row = $this->db->select("*")->from("coop_calendar_work")->where("work_year = '".$y."'")->get()->row_array();
		$holidays = explode(",", $row["holidays"]);
		if(in_array($day, $holidays)) {
			return true;
		}
		
		$row = $this->db->select("*")->from("coop_calendar_holiday")->where("holiday_date = '".$date."'")->get()->row_array();
		if(!empty($row)) {
			return true;
		}
		
		return false;
	}
	
	private function _get_near_holiday_list($date) {
		$entries = array();
		
		$i = 1;
		while($i < 100) { // infinity loop limit 100
			$rs_date = $this->db->query("SELECT DATE_ADD('".$date."', INTERVAL -".$i." DAY) AS date_prev");
			$row_date = $rs_date->row_array();
			$date_prev = $row_date["date_prev"];
			if($this->_is_holiday($date_prev)) {
				array_push($entries, $date_prev);
			}
			else {
				break;
			}
			
			$i++;
		}
		sort($entries);
		
		return $entries;
	}
	
	public function cal_deposit_interest($row_member,$return_type = 'test_cal_interest', $date_interest = '', $day_interest = '', $param_date_dep = '', $from_holiday = false){
		//$return_type = 'cal_interest';
		ini_set("precision", 11);
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

		if(@$row_detail['is_day_cal_interest'] == 1 && $this->close == true){
			//ปิดไว้ก่อน ไม่รู้ fix ไว้ทำไม
			//if(@$row_detail['pay_interest'] == 6){
			//	$date_interest = date("Y-m-d",  strtotime($date_interest));
			//}else {
				$date_interest = date("Y-m-d", strtotime("-1 day", strtotime($date_interest)));
			//}
		}
		//echo $this->db->last_query();
		if(empty($row_detail)){return false;}

		if($this->debug) { echo"<pre>";print_r($row_detail);echo"</pre>"; }
		
		// วันหยุด
		if($return_type != 'return_interest') {
			$is_holiday = (@$row_detail['is_not_holiday'] == '1')?'':$this->_is_holiday($date_interest);
			if(in_array($row_detail['condition_age'], array(1, 2, 3)) && !$is_holiday && $param_date_dep == '') {
				$holidays =  $this->_get_near_holiday_list($date_interest);
				if(!empty($holidays)) {
					foreach($holidays as $holiday) {
						echo"<pre>*** START ".$holiday." ***</pre>";
						$this->cal_deposit_interest($row_member, $return_type, $date_interest." ".$time_interest, $day_interest, $holiday, true);
						echo"<pre>*** END ".$holiday." ***</pre>";
					}
				}
			}
		}
		
		//เช็คการจ่ายดอกเบี้ย
		if($row_detail['pay_interest'] == '1'){
			//จ่ายทุกสิ้นเดือน
			$num_month_maturity = 1;
			
			if($return_type != 'return_interest') {
				$rs_chk = $this->db->select("*")->from("coop_maco_account")->where("account_id = '".$row_member['account_id']."' AND account_status = '0'")->get()->result_array();
				if(empty($rs_chk)){
					return false;
				}
			}
			
			$where = "t1.account_id = '".$row_member['account_id']."' AND transaction_list IN ('OPN', 'OCA', 'OPT', 'TRB', 'XD', 'DEP', 'DCA', 'DFX', 'DEPP', 'CD','DOT') AND
							TIMESTAMPDIFF(MONTH, transaction_time, '".$date_dep."') +
							DATEDIFF('".$date_dep."', transaction_time + INTERVAL TIMESTAMPDIFF(MONTH, transaction_time, '".$date_dep."') MONTH) /
							DATEDIFF(transaction_time + INTERVAL TIMESTAMPDIFF(MONTH, transaction_time, '".$date_dep."') + 1 MONTH, transaction_time + INTERVAL TIMESTAMPDIFF(MONTH, transaction_time, '".$date_dep."') MONTH) = max_month";
			$rs_due = $this->db->select(array(
								't1.account_id',
								't1.account_name',
								't1.mem_id',
								"t2.transaction_balance",
								"t2.transaction_withdrawal",
								"t2.transaction_deposit",
								"t2.transaction_time",
								"t3.type_name",
								"t3.type_code"))
							->from('coop_maco_account as t1')
							->join("coop_account_transaction{$this->forecast} t2", "t1.account_id = t2.account_id", "inner")
							->join("(SELECT type_id, type_name, type_code, (
											SELECT max_month
											FROM coop_deposit_type_setting_detail
											WHERE coop_deposit_type_setting_detail.type_id = coop_deposit_type_setting.type_id AND start_date <= '".$date_dep."'
											ORDER BY start_date DESC
											LIMIT 1) AS max_month
										FROM coop_deposit_type_setting
										WHERE coop_deposit_type_setting.type_id IN (SELECT type_id FROM coop_deposit_type_setting_detail WHERE condition_age IN (1, 2, 3))) t3", "t1.type_id = t3.type_id", "inner")
							->order_by("t2.transaction_time, t2.transaction_id")
							->where($where)->get()->result_array();
			if(!empty($rs_due)){
				$is_due = true;
			}
			
			if(date("d", strtotime($date_dep)) != date("t", strtotime($date_dep))){
				$is_cal = false;
			}
			
			if($row_detail['type_interest'] == '4' && $is_holiday && $param_date_dep == '') {
				return false;
			}
			
			$is_near_holiday_end_month = false;
			$near_holidays = $this->_get_near_holiday_list($date_dep);
			if(!empty($near_holidays)) {
				foreach($near_holidays as $near_holiday) {
					if(date("d", strtotime($near_holiday)) == date("t", strtotime($near_holiday))) {
						$is_near_holiday_end_month = true;
						break;
					}
				}
			}
			
			$is_process = false;
			if($row_detail['type_interest'] == '4' && $param_date_dep == '' && !$is_holiday && $is_near_holiday_end_month) {
				$is_process = true;
			}

			if($date_dep != date('Y-m-t', strtotime($date_dep)) && !$is_process && !$is_due && !$this->close){
				return false;
			}
		}else if($row_detail['pay_interest'] == '2'){
			//จ่ายทุกเดือน ตามวันที่ครบกำหนด
			$num_month_maturity = 1;
			$where_chk = " AND (";
			$where_chk .= "(YEAR(transaction_time) = YEAR(DATE_ADD('".$date_dep."', INTERVAL -1 MONTH))
				AND MONTH(transaction_time) = MONTH(DATE_ADD('".$date_dep."', INTERVAL -1 MONTH))
				AND DAY(transaction_time) = DAY(DATE_ADD('".$date_dep."', INTERVAL -1 MONTH)))";
			
			$cur_day_count = date("t", strtotime($date_dep));
			$_row = $this->db->query("SELECT DATE_ADD('".$date_dep."', INTERVAL -1 MONTH) AS prev_date")->row_array();
			if(date("d", strtotime($date_dep)) == $cur_day_count){
				$prev_day_count = date("t", strtotime($_row["prev_date"]));
				$day_diff = $prev_day_count - $cur_day_count;
				for($i = 1; $i <= $day_diff; $i++) {
					$_row2 = $this->db->query("SELECT DATE_ADD('".$_row["prev_date"]."', INTERVAL ".$i." DAY) AS next_date")->row_array();
					$where_chk .= " OR (YEAR(transaction_time) = YEAR('".$_row2["next_date"]."')
						AND MONTH(transaction_time) = MONTH('".$_row2["next_date"]."')
						AND DAY(transaction_time) = DAY('".$_row2["next_date"]."'))";
				}
			}
			
			/*if($this->_is_holiday(date("Y-m-d", strtotime($_row["prev_date"])))) {
				for($i = 1; $i <= 31; $i++) { // anti infinity loop
					$_row2 = $this->db->query("SELECT DATE_ADD('".$_row["prev_date"]."', INTERVAL ".$i." DAY) AS next_date")->row_array();
					if(!$this->_is_holiday(date("Y-m-d", strtotime($_row2["next_date"])))) {
						$where_chk .= " OR (YEAR(transaction_time) = YEAR('".$_row2["next_date"]."')
							AND MONTH(transaction_time) = MONTH('".$_row2["next_date"]."')
							AND DAY(transaction_time) = DAY('".$_row2["next_date"]."'))";
						break;
					}
				}
			}*/
			
			$where_chk .= ")";
			
			$this->db->select('transaction_id');
			$this->db->from("coop_account_transaction{$this->forecast}");
			$this->db->where("fixed_deposit_status <> '1' AND account_id = '".$row_member['account_id']."' AND transaction_deposit > 0 ".$where_chk);
			$this->db->limit(1);
			//echo $this->db->get_compiled_select(null, false)."<br>";
			$rs_chk = $this->db->get()->result_array();
			
			if(empty($rs_chk) && $return_type == 'cal_interest'){
				return false;
			}
			//echo $create_account_day;exit;
		}else if($row_detail['pay_interest'] == '3'){
			//จ่าย 2 ครั้ง ต่อปี
			if(substr($date_interest, 5, 5) != substr($row_detail['pay_date1'], 5, 5) && substr($date_interest, 5, 5) != substr($row_detail['pay_date2'], 5, 5) && !$is_process){
				return false;
			}
			
		}else if($row_detail['pay_interest'] == '4'){
			//จ่ายเมื่อครบกำหนด  x เดือน (เงินฝากประจำ)
			$num_month_maturity = (int)$row_detail['num_month_maturity'];
			$ext_num_month_maturity_day = (int)$row_detail['ext_num_month_maturity_day'];
			if($ext_num_month_maturity_day>0){
				$this->db->select('transaction_id');
				$this->db->from("coop_account_transaction{$this->forecast}");
				$this->db->where("account_id = '".$row_member['account_id']."' AND transaction_deposit > 0 AND '".$date_interest."' >= DATE_ADD( DATE_FORMAT(transaction_time ,'%Y-%m-01'), INTERVAL 24 month )");
				$this->db->order_by("transaction_time asc");
				$this->db->limit(1);
				$rs_chk = $this->db->get()->result_array();
				if(!empty($rs_chk)){
					$this->db->select("transaction_id, IF('".$date_interest."' >= DATE_ADD(DATE_FORMAT( transaction_time, '%Y-%m-%d' ), INTERVAL ".$ext_num_month_maturity_day." DAY ), 1, 0 ) AS ext_day");
					$this->db->from("coop_account_transaction{$this->forecast}");
					$this->db->where("account_id = '".$row_member['account_id']."' AND transaction_deposit > 0 AND transaction_list in ('CD')");
					$this->db->order_by("transaction_time DESC");
					$this->db->limit(1);
					$rs_chk_ext = $this->db->get()->row_array();
					if($rs_chk_ext['ext_day']!=1){
						return false;
					}
				}
				// echo $this->db->last_query(); exit;
			}else{
				$where_chk = " AND YEAR(transaction_time) = YEAR(DATE_ADD('".$date_dep."', INTERVAL -".$num_month_maturity." MONTH))
					AND MONTH(transaction_time) = MONTH(DATE_ADD('".$date_dep."', INTERVAL -".$num_month_maturity." MONTH))
					AND DAY(transaction_time) = DAY(DATE_ADD('".$date_dep."', INTERVAL -".$num_month_maturity." MONTH))";
				$this->db->select('transaction_id');
				$this->db->from("coop_account_transaction{$this->forecast}");
				$this->db->where("account_id = '".$row_member['account_id']."' AND transaction_deposit > 0 ".$where_chk);
				$this->db->limit(1);
				//echo $this->db->get_compiled_select(null, false)."<br>";
				$rs_chk = $this->db->get()->result_array();
				// echo $this->db->last_query(); exit;
			}
			
			if((empty($rs_chk) || $is_holiday) && $return_type != 'return_interest'){
				return false;
			}
		}else if($row_detail['pay_interest'] == '5'){
			//@2019-09-20 จ่ายเมื่อครบกำหนด  x เดือน (เงินฝากทั่วไป)
			$num_month_maturity = (int)$row_detail['num_month_maturity_normal'];
			$where_chk = " AND YEAR(transaction_time) = YEAR(DATE_ADD('".$date_dep."', INTERVAL -".$num_month_maturity." MONTH))
				AND MONTH(transaction_time) = MONTH(DATE_ADD('".$date_dep."', INTERVAL -".$num_month_maturity." MONTH))
				AND DAY(transaction_time) = DAY(DATE_ADD('".$date_dep."', INTERVAL -".$num_month_maturity." MONTH))";
			$this->db->select('transaction_id');
			$this->db->from("coop_account_transaction{$this->forecast}");
			$this->db->where("account_id = '".$row_member['account_id']."' AND transaction_deposit > 0 ".$where_chk);
			$this->db->limit(1);
			//echo $this->db->get_compiled_select(null, false)."<br>";
			$rs_chk = $this->db->get()->result_array();
			
			if((empty($rs_chk) || $is_holiday) && $return_type != 'return_interest'){
				return false;
			}
		}else if($row_detail['pay_interest'] == '6'){
		    //คิดดอกเบี้ยตอนสิ้นปี
            $num_month_maturity = 12;
            if($this->close){
            	$date_dep =date('Y-m-d', strtotime($date_dep." +1 day"));
			}
            $next_year = date("Y", strtotime($date_dep));
            $date_checker = date("Y-m-d", strtotime($next_year."-12-31"));
            if($date_checker !== date("Y-m-d", strtotime($date_dep)) && $return_type != 'return_interest' && !$this->close){
                return false;
            }
		}
		
		$this->db->select(array(
			'transaction_time'
		));
		$this->db->from("coop_account_transaction{$this->forecast}");
		$this->db->where("account_id= '".$row_member['account_id']."' AND (transaction_list = 'IN' OR transaction_list = 'INT')");
		$this->db->order_by('transaction_time DESC');
		$this->db->limit(1);
		$rs_last_interest = $this->db->get()->result_array();
		$row_last_interest = @$rs_last_interest[0];
		$where = '';
		if(@$row_last_interest['transaction_time']!=''){
			$where .= " AND t1.transaction_time >= '".$row_last_interest['transaction_time']."' ";
		}
		//
		$fix_saving = false;
		if($row_detail['pay_interest'] == '2'){
			if($return_type == 'return_interest') {
				$where_chk = "";
			}
			else {
				$where_chk = " AND (";
				$where_chk .= "(transaction_time <= DATE_ADD('".$date_dep." 23:59:59', INTERVAL -1 MONTH)".
					(empty($row_detail["max_month"]) ? "" : " AND transaction_time >= DATE_ADD('".$date_dep."', INTERVAL -".$row_detail["max_month"]." MONTH)")."
					AND DAY(transaction_time) = DAY('".$date_dep."'))";
				
				$cur_day_count = date("t", strtotime($date_dep));
				if(date("d", strtotime($date_dep)) == $cur_day_count){
					$_row = $this->db->query("SELECT DATE_ADD('".$date_dep."', INTERVAL -1 MONTH) AS prev_date")->row_array();
					$prev_day_count = date("t", strtotime($_row["prev_date"]));
					$day_diff = $prev_day_count - $cur_day_count;
					for($i = 1; $i <= $day_diff; $i++) {
						$_row2 = $this->db->query("SELECT DATE_ADD('".$_row["prev_date"]."', INTERVAL ".$i." DAY) AS next_date")->row_array();
						$where_chk .= " OR (transaction_time <= '".$_row2["next_date"]." 23:59:59'".
							(empty($row_detail["max_month"]) ? "" : " AND transaction_time >= DATE_ADD('".$_row2["next_date"]."', INTERVAL -".$row_detail["max_month"]." MONTH)")."
							AND DAY(transaction_time) = DAY('".$_row2["next_date"]."'))";
					}
				}
				
				$where_chk .= ")";
			}
			$this->db->select("transaction_time, transaction_id, transaction_deposit, transaction_balance, transaction_list,
				TIMESTAMPDIFF(MONTH, transaction_time, '".$date_dep." 23:59:59') + CASE WHEN DAY(transaction_time) = ".date("d", strtotime($date_dep))." THEN 0 ELSE 1 END AS period");
			$this->db->from("coop_account_transaction{$this->forecast}");
			$this->db->where("fixed_deposit_status <> '1' AND account_id = '".$row_member['account_id']."' AND transaction_list IN ('OPN', 'OCA', 'OPT', 'TRB', 'XD', 'DEP', 'DCA', 'DFX', 'DEPP', 'CD','DOT') ".$where_chk);
			$this->db->order_by("transaction_time, transaction_id");
			//echo $this->db->get_compiled_select(null, false)."<br>";
			$_rs = $this->db->get();
			$transaction = array();
			foreach($_rs->result_array() as $key => $value) {
				$transaction[$key]['type_id'] = $row_member['type_id'];
				$transaction[$key]['create_account_date'] = $row_member['create_account_date'];
				$transaction[$key]['date_begin'] = date('Y-m-d',strtotime($value['transaction_time']));
				$transaction[$key]['transaction_list'] = $value['transaction_list'];
				$transaction[$key]['transaction_id'] = $value['transaction_id'];
				$transaction[$key]['ref_transaction_id'] = $value['transaction_id'];
				$transaction[$key]['date_start'] = date('Y-m-d',strtotime($value['transaction_time']));
				$transaction[$key]['transaction_deposit'] = $value['transaction_deposit'];
				$transaction[$key]['transaction_balance'] = $value['transaction_balance'];
				$transaction[$key]['account_id'] = $row_member['account_id'];
				$transaction[$key]['interest_period'] = $value['period'];
				$transaction[$key]['date_end'] = $date_interest;
				$transaction[$key]['deposit_interest_balance'] = $value['transaction_balance'];
			}
			
			$fix_saving = true;
		}
		elseif($row_detail['pay_interest'] == '4'){
			$chk_date_ext_maturity = $this->get_date_last_account($row_member['account_id'],$num_month_maturity,$row_detail['ext_num_month_maturity_day']);
			if($chk_date_ext_maturity != ''){
				//$date_interest = '2021-01-31';
				$date_ext_maturity = $chk_date_ext_maturity;
			}else{
				$date_ext_maturity = $date_interest;
			}
			
			$where_chk = "";
			if($return_type == 'return_interest') {
				//$where_chk = " AND transaction_time >= DATE_ADD('".$date_interest."', INTERVAL -".($row_detail["max_month"]+1)." MONTH)";
				$where_chk = " AND transaction_time >= DATE_ADD('".$date_ext_maturity."', INTERVAL -".($row_detail["max_month"]+1)." MONTH)";
			}
			else {
				
				$where_chk = " AND transaction_time <= DATE_ADD('".$date_dep." 23:59:59', INTERVAL -".$num_month_maturity." MONTH)".
					(empty($row_detail["max_month"]) ? "" : " AND transaction_time >= DATE_ADD('".$date_dep."', INTERVAL -".$row_detail["max_month"]." MONTH)")."
					AND DAY(transaction_time) = DAY(DATE_ADD('".$date_dep."', INTERVAL -".$num_month_maturity." MONTH))";	
			}
			//$this->db->select("transaction_time, transaction_id, transaction_deposit, transaction_balance, transaction_list, TIMESTAMPDIFF(MONTH,transaction_time, '".$date_interest." 23:59:59') AS period,ref_account_no");
			$this->db->select("transaction_time, transaction_id, transaction_deposit, transaction_balance, transaction_list, TIMESTAMPDIFF(MONTH,transaction_time, '".$date_ext_maturity." 23:59:59') AS period,ref_account_no");
			$this->db->from("coop_account_transaction{$this->forecast}");
			$this->db->where("fixed_deposit_status <> '1' AND account_id = '".$row_member['account_id']."' AND transaction_list IN ('OPN', 'OCA', 'OPT', 'TRB', 'XD', 'DEP', 'DCA', 'DFX', 'DEPP', 'CD','DOT') ".$where_chk);
			$this->db->order_by("transaction_time, transaction_id");
			/*
			$this->db->select("transaction_time, transaction_id, transaction_deposit, transaction_balance, transaction_list, TIMESTAMPDIFF(MONTH,transaction_time, '".$date_ext_maturity." 23:59:59') AS period,ref_account_no");
			$this->db->from("coop_account_transaction{$this->forecast}");
			$this->db->where("fixed_deposit_status <> '1' AND account_id = '".$row_member['account_id']."' AND transaction_list IN ('OPN', 'OCA', 'OPT', 'TRB', 'XD', 'DEP', 'DCA', 'DFX', 'DEPP', 'CD','DOT') ".$where_chk);
			$this->db->order_by("transaction_time, transaction_id");
			*/
			//echo $this->db->get_compiled_select(null, false)."<br>"; exit;
			$_rs = $this->db->get();
			$transaction = array();
			foreach($_rs->result_array() as $key => $value) {
				if(@$row_detail['is_withdrawal_specify'] == '1') {
					//แบบเลือกยอดถอนเงินฝาก
					$this->db->select(array(
						'transaction_balance',
						'transaction_no_in_balance',
						'balance_deposit',
						'ref_account_no'
					));
					$this->db->from("coop_account_transaction{$this->forecast} as t1");
					$this->db->where("account_id = '".$row_member['account_id']."' AND ref_account_no = '".$value['ref_account_no']."'");
					$this->db->order_by('transaction_time DESC, transaction_id DESC');
					$this->db->limit(1);
					$row_balance_last_ref = $this->db->get()->row_array();
					//echo $this->db->last_query(); echo '<br>';
					$transaction_deposit = @$row_balance_last_ref['balance_deposit'];
					$transaction_balance = @$row_balance_last_ref['balance_deposit'];
				}else{
					$transaction_deposit = @$value['transaction_deposit'];
					$transaction_balance = @$value['transaction_balance'];
				}
								
				$transaction[$key]['type_id'] = $row_member['type_id'];
				$transaction[$key]['create_account_date'] = $row_member['create_account_date'];
				$transaction[$key]['date_begin'] = date('Y-m-d',strtotime($value['transaction_time']));
				$transaction[$key]['transaction_list'] = $value['transaction_list'];
				$transaction[$key]['transaction_id'] = $value['transaction_id'];
				$transaction[$key]['ref_transaction_id'] = $value['transaction_id'];
				$transaction[$key]['date_start'] = date('Y-m-d',strtotime($value['transaction_time']));
				//$transaction[$key]['transaction_deposit'] = $value['transaction_deposit'];
				$transaction[$key]['transaction_deposit'] = $transaction_deposit;
				//$transaction[$key]['transaction_balance'] = $value['transaction_balance'];
				$transaction[$key]['transaction_balance'] = $transaction_balance;
				$transaction[$key]['account_id'] = $row_member['account_id'];
				$transaction[$key]['interest_period'] = $value['period'];
				$transaction[$key]['date_end'] = $date_interest;
				//$transaction[$key]['deposit_interest_balance'] = $value['transaction_balance'];
				$transaction[$key]['deposit_interest_balance'] = $transaction_balance;
				$transaction[$key]['ref_account_no'] = @$value['ref_account_no'];
			}
			$fix_saving = true;
		}
		elseif($row_detail['pay_interest'] == '5'){
			//@2019-09-20 จ่ายเมื่อครบกำหนด  x เดือน (เงินฝากทั่วไป)
			$where_chk = " AND transaction_time >= DATE_ADD('".$date_interest."', INTERVAL -".($row_detail["max_month"]+1)." MONTH)";
			
			$this->db->select("transaction_time, transaction_id, transaction_deposit, transaction_balance, transaction_list, TIMESTAMPDIFF(MONTH,transaction_time, '".$date_interest." 23:59:59') AS period,ref_account_no");
			$this->db->from("coop_account_transaction{$this->forecast}");
			$this->db->where("fixed_deposit_status <> '1' AND account_id = '".$row_member['account_id']."' AND transaction_list IN ('OPN', 'OCA', 'OPT', 'TRB', 'XD', 'DEP', 'DCA', 'DFX', 'DEPP', 'CD','DOT') ".$where_chk);
			$this->db->order_by("transaction_time, transaction_id");			
			//echo $this->db->get_compiled_select(null, false)."<br>";
			$_rs = $this->db->get();
			$row = $_rs->result_array();
			//echo $this->db->last_query(); echo ';<br>';
			$transaction = array();
			foreach($row as $key => $value) {
				if(@$row_detail['is_withdrawal_specify'] == '1') {
					//แบบเลือกยอดถอนเงินฝาก
					$this->db->select(array(
						'transaction_balance',
						'transaction_no_in_balance',
						'balance_deposit',
						'ref_account_no'
					));
					$this->db->from("coop_account_transaction{$this->forecast} as t1");
					$this->db->where("account_id = '".$row_member['account_id']."' AND ref_account_no = '".$value['ref_account_no']."'");
					$this->db->order_by('transaction_time DESC, transaction_id DESC');
					$this->db->limit(1);
					$row_balance_last_ref = $this->db->get()->row_array();
					//echo $this->db->last_query(); echo '<br>';
					$transaction_deposit = @$row_balance_last_ref['balance_deposit'];
					$transaction_balance = @$row_balance_last_ref['balance_deposit'];
				}else{
					$transaction_deposit = @$value['transaction_deposit'];
					$transaction_balance = @$value['transaction_balance'];
				}
								
				$transaction[$key]['type_id'] = $row_member['type_id'];
				$transaction[$key]['create_account_date'] = $row_member['create_account_date'];
				$transaction[$key]['date_begin'] = date('Y-m-d',strtotime($value['transaction_time']));
				$transaction[$key]['transaction_list'] = $value['transaction_list'];
				$transaction[$key]['transaction_id'] = $value['transaction_id'];
				$transaction[$key]['ref_transaction_id'] = $value['transaction_id'];
				$transaction[$key]['date_start'] = date('Y-m-d',strtotime($value['transaction_time']));
				//$transaction[$key]['transaction_deposit'] = $value['transaction_deposit'];
				$transaction[$key]['transaction_deposit'] = $transaction_deposit;
				//$transaction[$key]['transaction_balance'] = $value['transaction_balance'];
				$transaction[$key]['transaction_balance'] = $transaction_balance;
				$transaction[$key]['account_id'] = $row_member['account_id'];
				$transaction[$key]['interest_period'] = $value['period'];
				$transaction[$key]['date_end'] = $date_interest;
				//$transaction[$key]['deposit_interest_balance'] = $value['transaction_balance'];
				$transaction[$key]['deposit_interest_balance'] = $transaction_balance;
				$transaction[$key]['ref_account_no'] = @$value['ref_account_no'];

			}
			
			$fix_saving = true;
		}
		else if($row_detail['pay_interest'] == '6') {
			$row_detail["max_month"] = 12;
			//Get lastst IN transaction for filter calculation.
			$last_int = $this->db->select("transaction_time")->from("coop_account_transaction{$this->forecast}")->where("account_id = '".$row_member['account_id']."' AND transaction_list = 'IN'")->order_by("transaction_time DESC")->get()->row();
        	$where_chk = " AND `transaction_time` <= '".$date_dep." 23:59:58' AND transaction_time >= DATE_SUB('".$date_interest." 00:00:00', INTERVAL ".$row_detail["max_month"]." MONTH) AND transaction_time >= '".$last_int->transaction_time."'";
            $this->db->select("transaction_time, transaction_id, transaction_deposit, transaction_balance, transaction_list, TIMESTAMPDIFF(MONTH,transaction_time, '".$date_interest." 23:59:59') AS period");
            $this->db->from("coop_account_transaction{$this->forecast}");
            $this->db->where(" account_id = '".$row_member['account_id']."' ".$where_chk);
            $this->db->order_by("transaction_time, transaction_id");
            //echo $this->db->get_compiled_select(null, false)."<br>";
            $_rs = $this->db->get();
            $transaction = array();
            foreach($_rs->result_array() as $key => $value) {
                $transaction[$key]['type_id'] = $row_member['type_id'];
                $transaction[$key]['create_account_date'] = $row_member['create_account_date'];
                $transaction[$key]['date_begin'] = date('Y-m-d',strtotime($value['transaction_time']));
                $transaction[$key]['transaction_list'] = $value['transaction_list'];
                $transaction[$key]['transaction_id'] = $value['transaction_id'];
                $transaction[$key]['ref_transaction_id'] = $value['transaction_id'];
                $transaction[$key]['date_start'] = date('Y-m-d',strtotime($value['transaction_time']));
                $transaction[$key]['transaction_deposit'] = $value['transaction_deposit'];
                $transaction[$key]['transaction_balance'] = $value['transaction_balance'];
                $transaction[$key]['account_id'] = $row_member['account_id'];
                $transaction[$key]['interest_period'] = $value['period'];
                $transaction[$key]['date_end'] = $date_interest;
                $transaction[$key]['deposit_interest_balance'] = $value['transaction_balance'];
            }
            $is_fixed_year = true;

           //echo "echo <pre>"; print_r($transaction); echo "</pre>";
           //exit;
        }
		else{
			/*$this->db->select(array(
				't1.transaction_id',
				't1.transaction_balance', 
				't1.transaction_no_in_balance', 
				't1.transaction_time',
				't1.account_id',
				't1.transaction_list'
			));
			$this->db->from("coop_account_transaction{$this->forecast} as t1");
			$this->db->join('coop_maco_account as t2','t1.account_id = t2.account_id','inner');
			$this->db->where("t2.account_id = '".$row_member['account_id']."' {$where}");
			$this->db->order_by('t1.transaction_time ASC');
			$row_transaction = $this->db->get()->result_array();
		
			$transaction = array();
			foreach($row_transaction as $key => $value){
				$transaction[$key]['type_id'] = $row_member['type_id'];
				$transaction[$key]['create_account_date'] = $row_member['create_account_date'];
				$transaction[$key]['date_begin'] = date('Y-m-01',strtotime($row_member['create_account_date']));
				$transaction[$key]['transaction_list'] = $value['transaction_list'];
				$transaction[$key]['transaction_id'] = $value['transaction_id'];
				$transaction[$key]['date_start'] = date('Y-m-d',strtotime($value['transaction_time']));
				$transaction[$key]['transaction_balance'] = $value['transaction_balance'];
				$transaction[$key]['transaction_no_in_balance'] = $value['transaction_no_in_balance'];
				$transaction[$key]['account_id'] = $value['account_id'];
				if(@$row_transaction[($key+1)]['transaction_time'] != '' && @$row_transaction[($key+1)]['account_id'] == $value['account_id']){
					$transaction[$key]['date_end'] = date('Y-m-d',strtotime($row_transaction[($key+1)]['transaction_time']));
				}else{
					$transaction[$key]['date_end'] = $date_interest;
				}
			}*/
		}

		if($row_detail['condition_interest'] == '1'){
			$this->db->select(array(
				't1.type_detail_id',
				't1.type_id',
				't1.start_date',
				't2.num_month',
				't2.percent_interest as interest_rate',
				't1.percent_depositor'
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
					$row_interest_rate[$key2]['end_date'] = $date_interest;
				}
			}

			if($fix_saving) {
				//@start 2019-09-19 ปรับการหาวันเพื่อคิดดอกเบี้ย
				$transaction_last = array();
				foreach($row_interest_rate as $key_rate => $value_rate){
					//echo $value_rate['end_date'].' != '.$date_interest.'<br>';
					if($value_rate['end_date'] != $date_interest && $date_interest > $value_rate['end_date']){
						$this->db->select(array('*'));
						$this->db->from('coop_account_transaction');
						$this->db->where("account_id = '{$row_member['account_id']}' AND transaction_time < '{$value_rate['end_date']}' AND YEAR(transaction_time) = ".date("Y", strtotime("-1 year", strtotime($date_interest))));
						$this->db->order_by("transaction_time DESC,transaction_id DESC");
						$this->db->limit(1);
						$row_transaction_last_rate = $this->db->get()->row_array();
						//echo $this->db->last_query(); echo ';';
						if(!empty($row_transaction_last_rate)){			
							$transaction_last[$key_rate]['type_id'] = $row_member['type_id'];
							$transaction_last[$key_rate]['create_account_date'] = $row_member['create_account_date'];
							$transaction_last[$key_rate]['date_begin'] = date('Y-m-d',strtotime($value_rate['end_date']));
							$transaction_last[$key_rate]['transaction_list'] = $row_transaction_last_rate['transaction_list'];
							$transaction_last[$key_rate]['transaction_id'] = $row_transaction_last_rate['transaction_id'];
							$transaction_last[$key_rate]['ref_transaction_id'] = $row_transaction_last_rate['transaction_id'];
							$transaction_last[$key_rate]['date_start'] = date('Y-m-d',strtotime($value_rate['end_date']));
							$transaction_last[$key_rate]['transaction_deposit'] = $row_transaction_last_rate['transaction_deposit'];
							$transaction_last[$key_rate]['transaction_balance'] = $row_transaction_last_rate['transaction_balance'];
							$transaction_last[$key_rate]['account_id'] = $row_member['account_id'];
							$transaction_last[$key_rate]['interest_period'] = $row_transaction_last_rate['period'];
							$transaction_last[$key_rate]['date_end'] = $date_interest;
							$transaction_last[$key_rate]['deposit_interest_balance'] = $row_transaction_last_rate['transaction_balance'];
							$transaction_last[$key_rate]['ref_account_no'] = @$row_transaction_last_rate['ref_account_no'];
						}
					}
				}
				//echo "<pre>"; print_r($transaction_last); exit;
				//exit;
				$i=0;
				$transaction_new = array();
				//echo '<pre>'; print_r($transaction); echo '</pre>';
				
				if(@$row_detail['is_withdrawal_specify'] == '1') {
					//แบบเลือกยอดถอนเงินฝาก
					$transaction = array_merge($transaction);
					array_multisort( array_column($transaction, "date_begin"), SORT_ASC,$transaction);
				}else{	
					$transaction = array_merge($transaction, $transaction_last);
					array_multisort( array_column($transaction, "date_begin"), SORT_ASC,$transaction);
				}
				//@end 2019-09-19 ปรับการหาวันเพื่อคิดดอกเบี้ย
				$transaction_count = count($transaction);
				foreach($transaction as $key => $value){
					//$transaction_new[$i] = $value;
					$interest_rate = 0;
					$interest_depositor_sum = 0;
					$depositor_balance = $value['transaction_deposit'];
					$transaction_balance = $value['transaction_deposit'];
					$period = 1;
					for($period_m = $num_month_maturity; $period_m < $value["interest_period"]; $period_m += $num_month_maturity) {
						foreach($row_interest_rate as $key2 => $value2){
							if($period <= @$value2['num_month']){
								$interest_rate = $value2['interest_rate'];
								break;
							}
						}
						
						$rs_date = $this->db->query("SELECT DATEDIFF(DATE_ADD('".$value['date_start']."', INTERVAL ".$period." MONTH), DATE_ADD('".$value['date_start']."', INTERVAL ".($period - $num_month_maturity)." MONTH)) AS date_count,
																			DATE_ADD('".$value['date_start']."', INTERVAL ".$period." MONTH) AS date_start, DATE_ADD('".$value['date_start']."', INTERVAL ".($period - $num_month_maturity)." MONTH) AS date_prev");
						$row_date = $rs_date->row_array();
						
						$_cals = array();
						foreach($row_interest_rate as $key2 => $value2){
							if(strtotime($row_date['date_prev']) >= strtotime($value2['start_date']) && strtotime($row_date['date_prev']) <= strtotime($value2['end_date'])){
								if(strtotime($row_date['date_prev']) >= strtotime($value2['start_date'])){
									$_cals[0]['date_start'] = $row_date['date_prev'];
									$_cals[0]['date_end'] = $row_date['date_start'];
									$_cals[0]['interest_rate'] = $value2['interest_rate'];
								}
								if(strtotime($row_date['date_start']) > strtotime($value2['end_date'])){
									$_cals[0]['date_end'] = $value2['end_date'];
									$_cals[1]['date_start'] = $value2['end_date'];
									$_cals[1]['date_end'] = $row_date['date_start'];
									$_cals[1]['interest_rate'] = @$row_interest_rate[($key2+1)]['interest_rate'];
								}
							}
						}
						
						foreach($_cals as $_cal) {
							$day_of_year = date("z", strtotime(substr($_cal["date_start"], 0, 4)."-12-31")) + 1;
							
							$diff = @date_diff(date_create($_cal['date_start']),date_create($_cal['date_end']));
							$date_count = @$diff->format("%a");
							
							$interest = round(((($transaction_balance*@$_cal["interest_rate"])/100)*$date_count)/$day_of_year, 2);
							if($row_detail['type_interest'] != '2' && $row_detail['type_interest'] != '4') {
								$interest_depositor = empty($row_detail["percent_depositor"]) ? 0 : round(((($depositor_balance*$row_detail["percent_depositor"])/100)*$date_count)/$day_of_year, 2);
								$interest_depositor_sum = round($interest_depositor_sum + $interest_depositor, 2);
								$depositor_balance = round($depositor_balance + $interest_depositor, 2);
								$transaction_balance = round($transaction_balance + $interest, 2);
							}
							
							if($this->debug) { echo "period: {$period} : {$value['transaction_deposit']} : {$interest} : {$transaction_balance} : {$_cal["date_end"]} : {$day_of_year} : {$interest_depositor} : {$interest_depositor_sum}<br>"; }
						}
						
						$period++;
					}					
					
					
					//@start 2019-09-19 ปรับการหาวันเพื่อคิดดอกเบี้ย
					$this->db->select(array(
						't1.type_detail_id',
						't1.type_id',
						't1.start_date',
						't2.num_month',
						't2.percent_interest as interest_rate'
					));
					$this->db->from('coop_deposit_type_setting_detail as t1');
					$this->db->join('coop_deposit_type_setting_interest as t2','t1.type_detail_id = t2.type_detail_id AND t1.condition_interest = t2.condition_interest','inner');
					$this->db->where("t1.type_id = '".@$row_member['type_id']."' AND t1.start_date <= '{$value["date_start"]}'");
					$this->db->order_by("start_date DESC");
					$this->db->limit(1);
					$row_interest_rate_0 = $this->db->get()->row_array();

					//echo $this->db->last_query(); echo ';';
					
					$transaction_new[$key] = $value;
					if(@$row_detail['is_withdrawal_specify'] == '1') {
						//แบบเลือกยอดถอนเงินฝาก
						//วันที่ครบกำหนด due ล่าสุด
						$row_last_due = $this->db->select('transaction_time')->from('coop_account_transaction')->where("account_id = '".$row_member['account_id']."' AND ref_account_no = '".$value['ref_account_no']."' AND transaction_list = 'DFX'")->order_by("transaction_time DESC")->limit(1)->get()->row_array();
						//วันที่ทำรายการล่าสุด
						$row_last_transaction = $this->db->select('transaction_time,balance_deposit,balance_deposit_int,transaction_id')->from('coop_account_transaction')->where("account_id = '".$row_member['account_id']."' AND ref_account_no = '".$value['ref_account_no']."'")->order_by("transaction_time DESC")->limit(1)->get()->row_array();
						$transaction_time_last = (@$row_last_due['transaction_time'] != '')?@$row_last_due['transaction_time']:@$row_last_transaction['transaction_time'];
						//หาอัตราดอกเบี้ย
						$data_interest_rate = $this->check_interest_rate($row_member['type_id'],$date_interest,$transaction_time_last);
						$transaction_new[$key]['interest_rate'] = $data_interest_rate['interest_rate'];

					}else{
						$transaction_new[$key]['interest_rate'] = $row_interest_rate_0['interest_rate'];
						$transaction_new[$key]['date_start'] = ($transaction[$key]['date_begin'] != '')?$transaction[$key]['date_begin']:$date_interest;
						$transaction_new[$key]['date_end'] = $value['date_begin'];
					}

					//ถ้าเป็นงวดสุดท้ายของฝากประจำจะต้องคำนวนวันตามค่าที่กำหนด
					if(($key + 1) == $transaction_count && ($key + 1) >= $row_detail['num_month_maturity'] && !empty($row_detail['ext_num_month_maturity_day'])) {
						$transaction_new[$key]['date_end'] = date('Y-m-d', strtotime("+".$row_detail['ext_num_month_maturity_day']." day", strtotime($transaction_new[$key]['date_start'])));
						$transaction_new[$key]['last_of_fixed_dept'] = 1;
					}

					//@end 2019-09-19 ปรับการหาวันเพื่อคิดดอกเบี้ย


					$i++;
				}

//				echo "<pre>"; print_r($transaction_new); exit;
				$transaction = $transaction_new;

			} else if ($is_fixed_year) {
                $i=0;
                $transaction_new = array();
                $is_first = true;


                //echo " Before Size: ".sizeof($transaction)." <br>";
                foreach($transaction as $key => $value){
                    $year_fix = date("Y-m-d", strtotime($date_dep." -1 year"));
                    $transaction_new[$i] = $value;
                    $interest_rate = 0;
                    $interest_depositor_sum = 0;
                    $depositor_balance = $value['transaction_balance'];
                    $transaction_balance = $value['transaction_balance'];
                    $period = 1;

                    if($value['date_start'] === $year_fix && $is_first){
                        $is_first = false;
                        //echo "Date Fixed <br>";
						$transaction_new[$i]['date_start'] = $date_start = date("Y-m-d", strtotime($year_fix));
					} else if ($value['transaction_list'] == "IN") {
						$inc = " +1 day";
						$transaction_new[$i]['date_start'] =  date('Y-m-d', strtotime($value['date_start'].$inc));
                    }else{
                        $date_start = $value['date_start'];
                    }

                    $transaction_new[$i]['interest_period'] = $period;
                    $transaction_new[$i]['transaction_balance'] = $transaction_balance;
                    $transaction_new[$i]['deposit_interest_balance'] = $transaction_balance;
                    $transaction_new[$i]['interest_rate'] = $interest_rate;
                    //if(!empty($row_date["date_start"])) $transaction_new[$i]['date_start'] = $row_date["date_start"];
                    $transaction_new[$i]['depositor_date_start'] = "";
                    $transaction_new[$i]['interest_depositor_sum'] = $interest_depositor_sum;
                    $transaction_new[$i]['depositor_balance'] = $depositor_balance;
                    if(empty($transaction[$key + 1]['date_start'])) {
                        if($return_type != 'return_interest') {
                            $transaction_new[$i]['date_end'] = $data_end = date("Y-m-d", strtotime((date("Y", strtotime($date_dep))) . "-12-31"));
                        }else{
                            $transaction_new[$i]['date_end'] = $date_dep;
                        }
                    }else{
                        $transaction_new[$i]['date_end'] = $data_end = $transaction[$key + 1]['date_start'];
                    }

                    foreach($row_interest_rate as $key2 => $value2){

                         if(strtotime($date_start) >= strtotime($value2['start_date']) && strtotime($date_start) <= strtotime($value2['end_date'])){
                            if(strtotime($date_start) >= strtotime($value2['start_date'])){
                                $transaction_new[$i]['interest_rate'] = $value2['interest_rate'];
                            }
                        }
                    }
                    $i++;
				}
                $transaction = $transaction_new;
        }else {
				$transaction = array();
				$index = 0;
				$balance = 0;
				$is_prev_holiday = false;
				
				if($row_detail['pay_interest'] == '3') {
					$pay_ym1 = substr($row_detail['pay_date1'], 5, 5);
					$pay_ym2 = substr($row_detail['pay_date2'], 5, 5);
					$cal_ym = substr($date_interest, 5, 5);
					$cal_y = substr($date_interest, 0, 4);
					
					if($cal_ym == $pay_ym1) {
						if(strtotime($row_detail['pay_date1']) < strtotime($row_detail['pay_date2'])) {
							$prev_date = ($cal_y - 1)."-".$pay_ym2;
						}
						else {
							$prev_date = $cal_y."-".$pay_ym2;
						}
					}
					elseif($cal_ym == $pay_ym2) {
						if(strtotime($row_detail['pay_date1']) < strtotime($row_detail['pay_date2'])) {
							$prev_date = $cal_y."-".$pay_ym1;
						}
						else {
							$prev_date = ($cal_y - 1)."-".$pay_ym1;
						}
					}
					
					if($return_type == 'return_interest') {
						$pay_date1 = substr($date_interest, 0, 4)."-".substr($row_detail['pay_date1'], 5, 5);
						$pay_date2 = substr($date_interest, 0, 4)."-".substr($row_detail['pay_date2'], 5, 5);
						
						if(strtotime($date_interest) < strtotime($pay_date1) && strtotime($date_interest) < strtotime($pay_date2)) {
							$date_s = ($cal_y - 1)."-".substr($row_detail['pay_date2'], 5, 5);
						}
						elseif(strtotime($date_interest) > strtotime($pay_date1) && strtotime($date_interest) > strtotime($pay_date2)) {
							$date_s = $cal_y."-".substr($row_detail['pay_date2'], 5, 5);
						}
						else {
							$date_s = $cal_y."-".substr($row_detail['pay_date1'], 5, 5);
						}
						
						if($this->debug) { echo $pay_date1." ".$pay_date2." ".$date_s."<br>"; }
						
						$this->db->select("transaction_id, transaction_list, transaction_time, transaction_balance, account_id");
						$this->db->from("coop_account_transaction{$this->forecast}");
						$this->db->where("account_id = '".$row_member['account_id']."'
							AND transaction_time >= '".$date_s."'");
						$this->db->order_by("transaction_time, transaction_id");
						$this->db->limit(1);
						$_row = $this->db->get()->row_array();
						$prev_date = substr($_row["transaction_time"], 0, 10);
					}
					
					$start_date = $prev_date;
					$this->db->select("transaction_id, transaction_list, transaction_time, transaction_balance, account_id");
					$this->db->from("coop_account_transaction{$this->forecast}");
					$this->db->where("account_id = '".$row_member['account_id']."'
						AND YEAR(transaction_time) = YEAR('".$prev_date."') AND MONTH(transaction_time) = MONTH('".$prev_date."') AND DAY(transaction_time) = DAY('".$prev_date."')");
					$this->db->order_by("transaction_time DESC, transaction_id DESC");
					$this->db->limit(1);
					$row_prev = $this->db->get()->row_array();
					
					if(empty($row_prev)) {
						$this->db->select("transaction_id, transaction_list, transaction_time, transaction_balance, account_id");
						$this->db->from("coop_account_transaction{$this->forecast}");
						$this->db->where("account_id = '".$row_member['account_id']."'
							AND transaction_time > '".$prev_date."'");
						$this->db->order_by("transaction_time, transaction_id");
						$this->db->limit(1);
						$row_prev = $this->db->get()->row_array();
					}
				}
				else {
					if($row_detail['type_interest'] == '4' && $row_detail['pay_interest'] == '1') {
						$rs_date = $this->db->query("SELECT DATE_ADD('".$date_dep."', INTERVAL -1 MONTH) AS date_prev");
						$row_date = $rs_date->row_array();
						$is_holiday_prev = (@$row_detail['is_not_holiday'] == '1')?'':$this->_is_holiday(date("Y-m-t", strtotime($row_date["date_prev"])));

						if($is_holiday_prev) {
							$is_prev_holiday = true;
							
							$this->db->select("transaction_time");
							$this->db->from("coop_account_transaction{$this->forecast}");
							$this->db->where("account_id = '".$row_member['account_id']."' AND transaction_list IN ('INT', 'IN')
								AND YEAR(transaction_time) = YEAR('".$date_dep."')
								AND MONTH(transaction_time) = MONTH('".$date_dep."')");
							$this->db->order_by('transaction_time, transaction_id');
							$this->db->limit(1);
							$row_int = $this->db->get()->row_array();
							$prev_int_date = date("Y-m-d", strtotime($row_int["transaction_time"]));
							
							$this->db->select("transaction_id, transaction_list, transaction_time, transaction_balance, account_id");
							$this->db->from("coop_account_transaction{$this->forecast}");
							$this->db->where("account_id = '".$row_member['account_id']."'
								AND transaction_time BETWEEN '".$prev_int_date."' AND '".$prev_int_date." 23:59:59'");
							$this->db->order_by("transaction_time DESC, transaction_id DESC");
							$this->db->limit(1);
						}
						else {
							$this->db->select("transaction_id, transaction_list, transaction_time, transaction_balance, account_id");
							$this->db->from("coop_account_transaction{$this->forecast}");
							$this->db->where("account_id = '".$row_member['account_id']."'
								AND YEAR(transaction_time) = YEAR(DATE_ADD('".$date_dep."', INTERVAL -1 MONTH))
								AND MONTH(transaction_time) <= MONTH(DATE_ADD('".$date_dep."', INTERVAL -1 MONTH))");
							$this->db->order_by("transaction_time DESC, transaction_id DESC");
							$this->db->limit(1);
						}
					}
					else {
						$this->db->select("transaction_id, transaction_list, transaction_time, transaction_balance, account_id");
						$this->db->from("coop_account_transaction{$this->forecast}");
						$this->db->where("account_id = '".$row_member['account_id']."'
							AND YEAR(transaction_time) = YEAR(DATE_ADD('".$date_dep."', INTERVAL -1 MONTH))
							AND MONTH(transaction_time) <= MONTH(DATE_ADD('".$date_dep."', INTERVAL -1 MONTH))");
						$this->db->order_by("transaction_time DESC, transaction_id DESC");
						$this->db->limit(1);
					}
					$row_prev = $this->db->get()->row_array();
				}
				if(!empty($row_prev)) {
					$balance = $row_prev["transaction_balance"];
					$start_date = $row_prev['transaction_time'];
					
					$transaction[$index]['type_id'] = $row_member['type_id'];
					$transaction[$index]['create_account_date'] = $row_member['create_account_date'];
					$transaction[$index]['date_begin'] = date('Y-m-d',strtotime($row_prev['transaction_time']));
					$transaction[$index]['transaction_list'] = $row_prev['transaction_list'];
					$transaction[$index]['transaction_id'] = $row_prev['transaction_id'];
					$transaction[$index]['date_start'] = date('Y-m-d',strtotime($row_prev['transaction_time']));
					$transaction[$index]['transaction_balance'] = $row_prev['transaction_balance'];
					$transaction[$index]['account_id'] = $row_prev['account_id'];
					$transaction[$index]['date_end'] = $date_interest;
					$transaction[$index]['interest_period'] = $row_detail["max_month"];
					$transaction[$index]['type_interest'] = $row_detail["type_interest"];
					
					foreach($row_interest_rate as $key2 => $value2){
						if(strtotime($transaction[$index]['date_start']) >= strtotime($value2['start_date']) && strtotime($transaction[$index]['date_start']) <= strtotime($value2['end_date'])){
							if(strtotime($transaction[$index]['date_start']) > strtotime($value2['start_date'])){
								$transaction[$index]['interest_rate'] = $value2['interest_rate'];
							}
							if(strtotime($transaction[$index]['date_start']) > strtotime($value2['end_date'])){
								$transaction[$index]['date_end'] = $value2['end_date'];
								$index++;
								$transaction[$index] = $transaction[$index - 1];
								$transaction[$index]['date_start'] = $value2['end_date'];
								$transaction[$index]['date_end'] = $date_interest;
								$transaction[$index]['interest_rate'] = @$row_interest_rate[($key2+1)]['interest_rate'];
							}
						}
					}
					
					$index++;
				}

				if($row_detail['pay_interest'] == '3') {
					$this->db->select("transaction_time, transaction_list, transaction_deposit, transaction_withdrawal, transaction_balance");
					$this->db->from("coop_account_transaction{$this->forecast}");
					$this->db->where("account_id = '".$row_member['account_id']."'
						AND transaction_time > '".$start_date."' AND transaction_time <= '".$date_interest."'");
					// AND transaction_list IN ('OPN', 'OCA', 'OPT', 'TRB', 'YPF', 'XD', 'DEP', 'DCA', 'DFX', 'DEPP', 'CD', 'INT', 'IN', 'WTD', 'WTB', 'CW', 'TRP', 'REVD', 'DEPL', 'ERRA', 'LRM', 'DTRN', 'DGIV', 'DEN', 'FWB')
					$this->db->order_by("transaction_time, transaction_id");
				}
				else {
					$this->db->select("transaction_time, transaction_list, transaction_deposit, transaction_withdrawal, transaction_balance");
					$this->db->from("coop_account_transaction{$this->forecast}");
					$this->db->where("account_id = '".$row_member['account_id']."' AND transaction_list IN ('OPN', 'OPNX', 'OCA', 'OPT', 'TRB', 'XD', 'DEP', 'DCA', 'DFX', 'DEPP', 'CD', 'INT', 'IN', 'WTD', 'WTB', 'CW','DOT','WCQ','XW')
						AND YEAR(transaction_time) = YEAR('".$date_dep."')
						AND MONTH(transaction_time) = MONTH('".$date_dep."')".
						($is_prev_holiday ? " AND transaction_time > '".$prev_int_date." 23:59:59'" : ""));
					$this->db->order_by("transaction_time, transaction_id");
				}
				$_rs = $this->db->get();
				foreach($_rs->result_array() as $_row) {
					if($_row["transaction_deposit"] != 0) {
						$balance = round($balance + $_row["transaction_deposit"], 2);
						if($this->debug) { echo "{$_row['transaction_time']} : {$_row["transaction_deposit"]} : {$balance}<br>"; }
						if($transaction[$index - 1]['date_start'] == date('Y-m-d', strtotime($_row['transaction_time'])) && $transaction[$index - 1]['type'] == "DEP") {
							if($this->debug) { echo $transaction[$index - 1]['date_start']."<br>"; }
							$transaction[$index - 1]['transaction_balance'] = $balance > 0 ? $balance : 0;
						}
						else {
							if($index > 0) {
								$transaction[$index - 1]['date_end'] = date('Y-m-d', strtotime($_row['transaction_time']));
								$transaction[$index] = $transaction[$index - 1];
							}
							$transaction[$index]['type_id'] = $row_member['type_id'];
							$transaction[$index]['account_id'] = $row_member['account_id'];
							$transaction[$index]['date_start'] = $_row['transaction_list'] == "OPN" ||  $_row['transaction_list'] == "OPNX" ? date('Y-m-d', strtotime("-1 day", strtotime($_row['transaction_time']))) : date('Y-m-d', strtotime($_row['transaction_time']));
							$transaction[$index]['date_end'] = $date_interest;
							$transaction[$index]['transaction_balance'] = $balance > 0 ? $balance : 0;
							$transaction[$index]['type'] = 'DEP';
							
							foreach($row_interest_rate as $key2 => $value2){
								if(strtotime($transaction[$index]['date_start']) >= strtotime($value2['start_date']) && strtotime($transaction[$index]['date_start']) <= strtotime($value2['end_date'])){
								//if(strtotime($transaction[$index]['date_start']) >= strtotime($transaction[$index]['date_end']) && strtotime($transaction[$index]['date_start']) <= strtotime($value2['end_date'])){
									$transaction[$index]['interest_rate'] = $value2['interest_rate'];
								}
							}
							
							$index++;
						}
					}
					
					if($_row["transaction_withdrawal"] != 0) {
						$balance = round($balance - $_row["transaction_withdrawal"], 2);
						if($index > 0) {
							$transaction[$index - 1]['date_end'] = date('Y-m-d', strtotime($_row['transaction_time']));
							$transaction[$index] = $transaction[$index - 1];
						}
						$transaction[$index]['date_start'] = date('Y-m-d', strtotime($_row['transaction_time']));
						$transaction[$index]['date_end'] = $date_interest;
						$transaction[$index]['transaction_balance'] = $balance > 0 ? $balance : 0;
						$transaction[$index]['type'] = 'WTD';
						
						foreach($row_interest_rate as $key2 => $value2){
							if(strtotime($transaction[$index]['date_start']) >= strtotime($value2['start_date']) && strtotime($transaction[$index]['date_start']) <= strtotime($value2['end_date'])){
							//if(strtotime($transaction[$index]['date_start']) >= strtotime($transaction[$index]['date_end']) && strtotime($transaction[$index]['date_start']) <= strtotime($value2['end_date'])){
								$transaction[$index]['interest_rate'] = $value2['interest_rate'];
							}
						}
						
						$index++;
					}
				}
			}
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
			$this->db->where("t1.type_id = '".@$row_member['type_id']."' AND t2.percent_interest IS NOT NULL AND t2.num_month != ''");
			$this->db->order_by("start_date ASC, t2.id ASC");
			$row_interest_rate = $this->db->get()->result_array();
			
			foreach($transaction as $key => $value){
				$interest_rate = 0;
				$interest_depositor_sum = 0;
				$depositor_balance = $value['transaction_deposit'];
				$transaction_balance = $value['transaction_deposit'];
				for($period = 1; $period < $value["interest_period"]; $period++) {
					foreach($row_interest_rate as $key2 => $value2){
						if($period <= $value2['num_month']){
							$interest_rate = $value2['interest_rate'];
							break;
						}
					}
					
					$rs_date = $this->db->query("SELECT DATEDIFF(DATE_ADD('".$value['date_start']."', INTERVAL ".$period." MONTH), DATE_ADD('".$value['date_start']."', INTERVAL ".($period - 1)." MONTH)) AS date_count,
																		DATE_ADD('".$value['date_start']."', INTERVAL ".$period." MONTH) AS date_start, DATE_ADD('".$value['date_start']."', INTERVAL ".($period - 1)." MONTH) AS date_prev");
					$row_date = $rs_date->row_array();
					
					$day_of_year = date("z", strtotime(substr($row_date["date_prev"], 0, 4)."-12-31")) + 1;
					$interest = round(((($transaction_balance*@$interest_rate)/100)*$row_date["date_count"])/$day_of_year, 2);
					$interest_depositor = empty($row_detail["percent_depositor"]) ? 0 : round(((($depositor_balance*$row_detail["percent_depositor"])/100)*$row_date["date_count"])/$day_of_year, 2);
					$interest_depositor_sum = round($interest_depositor_sum + $interest_depositor, 2);
					$depositor_balance = round($depositor_balance + $interest_depositor, 2);
					$transaction_balance = round($transaction_balance + $interest, 2);
					
					if($this->debug) { echo "period: {$period} : {$value['transaction_deposit']} : {$interest_rate} : {$row_date["date_count"]} : {$interest} : {$transaction_balance} : {$row_date["date_start"]} : {$day_of_year} : {$interest_depositor} : {$interest_depositor_sum}<br>"; }
				}
				
				foreach($row_interest_rate as $key2 => $value2){
					if($period <= $value2['num_month']){
						$interest_rate = $value2['interest_rate'];
						break;
					}
				}
				
				$transaction[$key]['interest_period'] = $period;
				$transaction[$key]['transaction_balance'] = $transaction_balance;
				$transaction[$key]['deposit_interest_balance'] = $transaction_balance;
				$transaction[$key]['interest_rate'] = $interest_rate;
				if(!empty($row_date["date_start"])) $transaction[$key]['date_start'] = $row_date["date_start"];
				$transaction[$key]['depositor_date_start'] = $transaction[$key]['date_start'];
				$transaction[$key]['interest_depositor_sum'] = $interest_depositor_sum;
				$transaction[$key]['depositor_balance'] = $depositor_balance;
				
				if($return_type == 'return_interest'){
					$this->db->select("transaction_time");
					$this->db->from("coop_account_transaction{$this->forecast}");
					$this->db->where("account_id = '".$value['account_id']."' AND transaction_list IN ('INT', 'IN')");
					$this->db->order_by('transaction_time DESC, transaction_id DESC');
					$this->db->limit(1);
					$row_chk1 = $this->db->get()->row_array();
					
					$rs_chk2 = $this->db->query("SELECT DATE_ADD('".$transaction[$key]['date_start']."', INTERVAL 1 MONTH) AS d,
																		DATEDIFF(DATE_ADD('".$transaction[$key]['date_start']."', INTERVAL 1 MONTH), '".$row_chk1["transaction_time"]."') AS c");
					$row_chk2 = $rs_chk2->row_array();
					$transaction[$key]['depositor_datediff'] = $row_chk2["c"];
					if($row_chk2["c"] <= 0) {
						foreach($row_interest_rate as $key2 => $value2){
							if($transaction_balance >= $value2['amount_deposit']){
								$interest_rate = $value2['interest_rate'];
								break;
							}
						}
						
						$rs_date = $this->db->query("SELECT DATEDIFF(DATE_ADD('".$value['date_start']."', INTERVAL ".$period." MONTH), DATE_ADD('".$value['date_start']."', INTERVAL ".($period - $num_month_maturity)." MONTH)) AS date_count,
																			DATE_ADD('".$value['date_start']."', INTERVAL ".$period." MONTH) AS date_start, DATE_ADD('".$value['date_start']."', INTERVAL ".($period - $num_month_maturity)." MONTH) AS date_prev");
						$row_date = $rs_date->row_array();
						
						$day_of_year = date("z", strtotime(substr($row_date["date_prev"], 0, 4)."-12-31")) + 1;
						$interest = round(((($transaction_balance*@$interest_rate)/100)*$row_date["date_count"])/$day_of_year, 2);
						$transaction_balance = round($transaction_balance + $interest, 2);
						
						$transaction[$key]['date_start'] = $row_chk2["d"];
						$transaction[$key]['transaction_balance'] = $transaction_balance;
						$transaction[$key]['deposit_interest_balance'] = $transaction_balance;
					}
				}
				
				
				/*$where_chk = " AND transaction_time <= DATE_ADD('".$date_interest." 23:59:59', INTERVAL -1 MONTH)
					AND DAY(transaction_time) = DAY(DATE_ADD('".$date_interest."', INTERVAL -1 MONTH))";
				$this->db->select('transaction_time, transaction_id, transaction_deposit, transaction_balance');
				$this->db->from("coop_account_transaction{$this->forecast}");
				$this->db->where("account_id = '".$row_member['account_id']."' AND transaction_deposit > 0 ".$where_chk);
				$this->db->order_by("transaction_time DESC, transaction_id DESC");echo $this->db->get_compiled_select(null, false)."<br>";
				$_rs = $this->db->get();
				$is_start = false;
				$interest_period = 0;
				$prev_balance = 0;
				$transaction_balance = 0;
				foreach($_rs->result_array() as $_row) {
					if($is_start) {
						if($prev_balance == $_row["transaction_balance"]) {
							$interest_period++;
							$prev_balance = round($_row["transaction_balance"] - $_row["transaction_deposit"], 2);
							$transaction_balance = round($transaction_balance + $_row["transaction_deposit"], 2);
						}
					}
					
					if($_row["transaction_id"] == $value["transaction_id"]) {
						$is_start = true;
						$interest_period++;
						$prev_balance = round($_row["transaction_balance"] - $_row["transaction_deposit"], 2);
						$transaction_balance = round($transaction_balance + $_row["transaction_deposit"], 2);
					}
				}
				
				if($interest_period <= $row_detail["max_month"]) {
					$transaction[$key]['interest_period'] = $interest_period;
					$transaction[$key]['transaction_balance'] = $transaction_balance;
					$transaction[$key]['deposit_interest_balance'] = $transaction_balance;
					
					foreach($row_interest_rate as $key2 => $value2){
						if($transaction[$key]['interest_period'] <= $value2['num_month']){
							$transaction[$key]['interest_rate'] = $value2['interest_rate'];
							break;
						}
					}
				}*/
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
			$this->db->where("t1.type_id = '".@$row_member['type_id']."' AND t2.percent_interest IS NOT NULL AND t2.amount_deposit IS NOT NULL");
			$this->db->order_by("start_date ASC, t2.amount_deposit DESC");
			$row_interest_rate = $this->db->get()->result_array();
			$max_interest_rate = 0;

			if($fix_saving) {
				foreach($transaction as $key => $value){
					$interest_rate = 0;
					$interest_depositor_sum = 0;
					$depositor_balance = $value['transaction_deposit'];
					$transaction_balance = $value['transaction_deposit'];
					$period = 1;
					for($period_m = $num_month_maturity; $period_m < $value["interest_period"]; $period_m += $num_month_maturity) {
						foreach($row_interest_rate as $key2 => $value2){
							if($transaction_balance >= $value2['amount_deposit']){
								$interest_rate = $value2['interest_rate'];
								break;
							}
						}
						
						$rs_date = $this->db->query("SELECT DATEDIFF(DATE_ADD('".$value['date_start']."', INTERVAL ".$period." MONTH), DATE_ADD('".$value['date_start']."', INTERVAL ".($period - $num_month_maturity)." MONTH)) AS date_count,
																			DATE_ADD('".$value['date_start']."', INTERVAL ".$period." MONTH) AS date_start, DATE_ADD('".$value['date_start']."', INTERVAL ".($period - $num_month_maturity)." MONTH) AS date_prev");
						$row_date = $rs_date->row_array();
						
						$day_of_year = date("z", strtotime(substr($row_date["date_prev"], 0, 4)."-12-31")) + 1;
						$interest = round(((($transaction_balance*@$interest_rate)/100)*$row_date["date_count"])/$day_of_year, 2);
						$interest_depositor = empty($row_detail["percent_depositor"]) ? 0 : round(((($depositor_balance*$row_detail["percent_depositor"])/100)*$row_date["date_count"])/$day_of_year, 2);
						$interest_depositor_sum = round($interest_depositor_sum + $interest_depositor, 2);
						$depositor_balance = round($depositor_balance + $interest_depositor, 2);
						$transaction_balance = round($transaction_balance + $interest, 2);
						
						if($this->debug) { echo "period: {$period} : {$value['transaction_deposit']} : {$interest} : {$transaction_balance} : {$row_date["date_start"]} : {$day_of_year} : {$interest_depositor} : {$interest_depositor_sum}<br>"; }
						
						$period++;
					}
					
					foreach($row_interest_rate as $key2 => $value2){
						if($transaction_balance >= $value2['amount_deposit']){
							$interest_rate = $value2['interest_rate'];
							break;
						}
					}
					
					$transaction[$key]['interest_period'] = $period;
					// $transaction[$key]['transaction_balance'] = $transaction_balance;
					$transaction[$key]['deposit_interest_balance'] = $transaction_balance;
					$transaction[$key]['interest_rate'] = $interest_rate;
					// if(!empty($row_date["date_start"])) $transaction[$key]['date_start'] = $row_date["date_start"];
					$transaction[$key]['depositor_date_start'] = $transaction[$key]['date_start'];
					$transaction[$key]['interest_depositor_sum'] = $interest_depositor_sum;
					$transaction[$key]['depositor_balance'] = $depositor_balance;
					
					if($return_type == 'return_interest'){
						$this->db->select("transaction_time");
						$this->db->from("coop_account_transaction{$this->forecast}");
						$this->db->where("account_id = '".$value['account_id']."' AND transaction_list IN ('INT', 'IN')");
						$this->db->order_by('transaction_time DESC, transaction_id DESC');
						$this->db->limit(1);
						$row_chk1 = $this->db->get()->row_array();
						
						$rs_chk2 = $this->db->query("SELECT DATE_ADD('".$transaction[$key]['date_start']."', INTERVAL 1 MONTH) AS d,
																			DATEDIFF(DATE_ADD('".$transaction[$key]['date_start']."', INTERVAL 1 MONTH), '".$row_chk1["transaction_time"]."') AS c");
						$row_chk2 = $rs_chk2->row_array();
						$transaction[$key]['depositor_datediff'] = $row_chk2["c"];
						if($row_chk2["c"] <= 0) {
							foreach($row_interest_rate as $key2 => $value2){
								if($transaction_balance >= $value2['amount_deposit']){
									$interest_rate = $value2['interest_rate'];
									break;
								}
							}
							
							$rs_date = $this->db->query("SELECT DATEDIFF(DATE_ADD('".$value['date_start']."', INTERVAL ".$period." MONTH), DATE_ADD('".$value['date_start']."', INTERVAL ".($period - $num_month_maturity)." MONTH)) AS date_count,
																				DATE_ADD('".$value['date_start']."', INTERVAL ".$period." MONTH) AS date_start, DATE_ADD('".$value['date_start']."', INTERVAL ".($period - $num_month_maturity)." MONTH) AS date_prev");
							$row_date = $rs_date->row_array();
							
							$day_of_year = date("z", strtotime(substr($row_date["date_prev"], 0, 4)."-12-31")) + 1;
							$interest = round(((($transaction_balance*@$interest_rate)/100)*$row_date["date_count"])/$day_of_year, 2);
							$transaction_balance = round($transaction_balance + $interest, 2);
							
							// $transaction[$key]['date_start'] = $row_chk2["d"];
							// $transaction[$key]['transaction_balance'] = $transaction_balance;
							$transaction[$key]['deposit_interest_balance'] = $transaction_balance;
						}
					}
					
					
					/*foreach($row_interest_rate as $key2 => $value2){
						if($transaction[$key]['transaction_balance'] <= $value2['amount_deposit']){
							$transaction[$key]['interest_rate'] = $value2['interest_rate'];
						}
						if($value2['interest_rate'] > $max_interest_rate){
							$max_interest_rate = $value2['interest_rate'];
						}
					}
					if(@$transaction[$key]['interest_rate'] == ''){
						$transaction[$key]['interest_rate'] = $max_interest_rate;
					}*/
				}
			}
			else {
				$transaction = array();
				$index = 0;
				$balance = 0;
				$date_dep_in = $date_dep;
				
				$rs_date = $this->db->query("SELECT DATE_ADD('".$date_dep."', INTERVAL -1 MONTH) AS date_prev");
				$row_date = $rs_date->row_array();
				$date_dep_prev = date("Y-m-t", strtotime($row_date['date_prev']));
				
				if($row_detail['type_interest'] == '4') {
					if($row_detail['pay_interest'] == '1') {
						if(date("d", strtotime($date_dep)) != date("t", strtotime($date_dep))) {
							$date_dep_in = $date_dep_prev;
							
							$is_holiday_dep_in = (@$row_detail['is_not_holiday'] == '1')?'':$this->_is_holiday($date_dep_in);							
							if($is_holiday_dep_in) {
								$is_cal = true;
							}
						}
					}
				}
				
				$is_holiday_dep_prev = (@$row_detail['is_not_holiday'] == '1')?'':$this->_is_holiday($date_dep_prev);				
				if($row_detail['pay_interest'] == '1' && $is_holiday_dep_prev && date("d", strtotime($date_dep)) == date("t", strtotime($date_dep))) {
					$this->db->select("transaction_id, transaction_list, transaction_time, transaction_balance, account_id");
					$this->db->from("coop_account_transaction{$this->forecast}");
					$this->db->where("account_id = '".$row_member['account_id']."' AND transaction_list IN ('INT', 'IN')
						AND transaction_time < '".$date_dep."'");
					$this->db->order_by("transaction_time DESC, transaction_id DESC");
					$this->db->limit(1);
					$row_date_in = $this->db->get()->row_array();
					
					$this->db->select("transaction_id, transaction_list, transaction_time, transaction_balance, account_id");
					$this->db->from("coop_account_transaction{$this->forecast}");
					$this->db->where("account_id = '".$row_member['account_id']."'
						AND YEAR(transaction_time) = YEAR('".$row_date_in['transaction_time']."')
						AND MONTH(transaction_time) = MONTH('".$row_date_in['transaction_time']."')
						AND DAY(transaction_time) = DAY('".$row_date_in['transaction_time']."')");
					$this->db->order_by("transaction_time DESC, transaction_id DESC");
					$this->db->limit(1);
					$row_prev = $this->db->get()->row_array();
					if(!empty($row_prev)) {
						$balance = $row_prev["transaction_balance"];
					}
				}
				else {
					$this->db->select("transaction_id, transaction_list, transaction_time, transaction_balance, account_id");
					$this->db->from("coop_account_transaction{$this->forecast}");
					$this->db->where("account_id = '".$row_member['account_id']."'
						AND YEAR(transaction_time) = YEAR(DATE_ADD('".$date_dep_in."', INTERVAL -1 MONTH))
						AND MONTH(transaction_time) <= MONTH(DATE_ADD('".$date_dep_in."', INTERVAL -1 MONTH))");
					$this->db->order_by("transaction_time DESC, transaction_id DESC");
					$this->db->limit(1);
					$row_prev = $this->db->get()->row_array();
					if(!empty($row_prev)) {
						$balance = $row_prev["transaction_balance"];
						$start_date = $row_prev['transaction_time'];
						
						$interest_rate = 0;
						foreach($row_interest_rate as $key2 => $value2){
							if($balance >= $value2['amount_deposit']){
								$interest_rate = $value2['interest_rate'];
								break;
							}
						}
						
						$transaction[$index]['type_id'] = $row_member['type_id'];
						$transaction[$index]['create_account_date'] = $row_member['create_account_date'];
						$transaction[$index]['date_begin'] = date('Y-m-d',strtotime($row_prev['transaction_time']));
						$transaction[$index]['transaction_list'] = $row_prev['transaction_list'];
						$transaction[$index]['transaction_id'] = $row_prev['transaction_id'];
						$transaction[$index]['date_start'] = date('Y-m-d',strtotime($row_prev['transaction_time']));
						$transaction[$index]['transaction_balance'] = $row_prev['transaction_balance'];
						$transaction[$index]['account_id'] = $row_prev['account_id'];
						$transaction[$index]['date_end'] = $date_interest;
						$transaction[$index]['interest_period'] = $row_detail["max_month"];
						$transaction[$index]['type_interest'] = $row_detail["type_interest"];
						$transaction[$index]['interest_rate'] = $interest_rate;
						
						$index++;
					}
					// var_dump($transaction);
					// exit;
				}
				
				$this->db->select("transaction_time, transaction_list, transaction_deposit, transaction_withdrawal, transaction_balance");
				$this->db->from("coop_account_transaction{$this->forecast}");
				$this->db->where("account_id = '".$row_member['account_id']."' AND transaction_list IN ('OPN','OPNX', 'OCA', 'OPT', 'TRB', 'XD', 'DEP', 'DCA', 'DFX', 'DEPP', 'CD', 'INT', 'IN', 'WTI', 'WTD', 'WTB', 'CW', 'WCQ', 'CM/FE', 'ERR','DOT','WCQ','XW')
					AND YEAR(transaction_time) = YEAR('".$date_dep_in."')
					AND MONTH(transaction_time) = MONTH('".$date_dep_in."')");
				$this->db->order_by("transaction_time, transaction_id");
				$_rs = $this->db->get();
				foreach($_rs->result_array() as $_row) {
					if($_row["transaction_deposit"] != 0) {
						$balance = round($balance + $_row["transaction_deposit"], 2);
						if($this->debug) { echo "{$_row['transaction_time']} : {$_row["transaction_deposit"]} : {$balance}<br>"; }
						if($transaction[$index - 1]['date_start'] == date('Y-m-d', strtotime($_row['transaction_time'])) && $transaction[$index - 1]['type'] == "DEP") {
							if($this->debug) { echo $transaction[$index - 1]['date_start']."<br>"; }
							$transaction[$index - 1]['transaction_balance'] = $balance > 0 ? $balance : 0;
						}
						else {
							if($index > 0) {
								$transaction[$index - 1]['date_end'] = date('Y-m-d', strtotime($_row['transaction_time']));
								$transaction[$index] = $transaction[$index - 1];
							}
							$transaction[$index]['type_id'] = $row_member['type_id'];
							$transaction[$index]['account_id'] = $row_member['account_id'];
							$transaction[$index]['date_start'] = $_row['transaction_list'] == "OPN" ||  $_row['transaction_list'] == "OPNX" ? date('Y-m-d', strtotime("-1 day", strtotime($_row['transaction_time']))) : date('Y-m-d', strtotime($_row['transaction_time']));

							$transaction[$index]['date_end'] = $date_interest;
							$transaction[$index]['transaction_balance'] = $balance > 0 ? $balance : 0;
							$transaction[$index]['type'] = 'DEP';
							
							$interest_rate = 0;
							foreach($row_interest_rate as $key2 => $value2){
								if($balance >= $value2['amount_deposit']){
									$interest_rate = $value2['interest_rate'];
									break;
								}
							}
							
							$transaction[$index]['interest_rate'] = $interest_rate;
							
							$index++;
						}
					}
					
					if($_row["transaction_withdrawal"] != 0) {
						$balance = round($balance - $_row["transaction_withdrawal"], 2);
						if($index > 0) {
							if($row_detail['is_non_pay_interest_after_withdraw'] == 1 && $this->close == true) {
								$transaction[$index - 1]['date_end'] = date('Y-m-d', strtotime($_row['transaction_time']));
							}else {
								$transaction[$index - 1]['date_end'] = date('Y-m-d', strtotime($_row['transaction_time']));
							}
							$transaction[$index] = $transaction[$index - 1];
						}

						if ($_row['transaction_list'] == "OPN" ||  $_row['transaction_list'] == "OPNX") {
							$transaction[$index]['date_start'] = date('Y-m-d', strtotime($_row['transaction_time'] . " -1 DAY"));
						} else {
							$transaction[$index]['date_start'] = date('Y-m-d', strtotime($_row['transaction_time']));
						}
						$transaction[$index]['date_end'] = $date_interest;
						$transaction[$index]['transaction_balance'] = $balance > 0 ? $balance : 0;
						$transaction[$index]['type'] = 'WTD';
						
						$interest_rate = 0;
						foreach($row_interest_rate as $key2 => $value2){
							if($balance >= $value2['amount_deposit']){
								$interest_rate = $value2['interest_rate'];
								break;
							}
						}
						
						$transaction[$index]['interest_rate'] = $interest_rate;
						$index++;
					}
				}
			}
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
			$interest_rate = @$value['interest_rate'];

			if($row_detail['type_interest'] == 1 && $row_detail['pay_interest'] == 1){
				if($key == 0 && sizeof($transaction) > 1){
					$value['date_end'] = date("Y-m-d", strtotime($transaction[$key]['date_end']. " -1 day"));
				}else if($key === (sizeof($transaction)-1) && sizeof($transaction) > 1){

					$value['date_end'] = date("Y-m-d", strtotime($value['date_end']. " +1 day"));
				}
			}else if($row_detail['type_interest'] == 1 && $row_detail['pay_interest'] == 6){
				if(@$row_detail['is_day_cal_interest'] == 1 && $this->close == true){
					//ลบ 1 วัน
					$value['date_end'] = date("Y-m-d", strtotime($value['date_end']. " -1 day"));
				}else{	
					//การจ่ายดอกเบี้ยทุกสิ้นปี
					if(@$row_detail['chk_date_cal_interest'] == '2'){				
						//เคส นับต้น นับปลาย
						if($key == 0 && sizeof($transaction) > 1){
							//ไม่บวกเพิ่ม
							$value['date_end'] = $transaction[$key]['date_end'];
						}else if($key === (sizeof($transaction)-1) && sizeof($transaction) > 1){
							//บวกเพิ่ม 1 วัน
							$value['date_end'] = date("Y-m-d", strtotime($value['date_end']. " +1 day"));
						}else if(sizeof($transaction) === 1 && $value['transaction_list'] == 'OPN'){
							//บวกเพิ่ม 1 วัน
							$value['date_end'] = date("Y-m-d", strtotime($value['date_end']. " +1 day"));
						}				
					}
					
					if(@$row_detail['chk_date_cal_interest'] == '3'){
						//เคส ไม่นับต้น นับปลาย
						if($key == 0 && sizeof($transaction) > 1 && $value['transaction_list'] != 'OPN'){
							//ลบ 1 วัน
							$value['date_end'] = date("Y-m-d", strtotime($value['date_end']. " -1 day"));
						}else if(sizeof($transaction) === 1 && $value['transaction_list'] == 'OPN'){
							//บวกเพิ่ม 1 วัน
							$value['date_end'] = date("Y-m-d", strtotime($value['date_end']. " +1 day"));
						}else if($key === (sizeof($transaction)-1) && sizeof($transaction) > 1){
							//บวกเพิ่ม 1 วัน
							$value['date_end'] = date("Y-m-d", strtotime($value['date_end']. " +1 day"));
						}else if($value['transaction_list'] == 'OPN'){
							//ไม่บวกเพิ่ม
							$value['date_end'] = $transaction[$key]['date_end'];
						}
					}					
				}
				//echo 'date_end='.$value['date_end'].'<br>';
			}else {
				if (isset($transaction[$key + 1]['date_start']) && $transaction[$key]['ref_account_no']==$transaction[$key + 1]['ref_account_no']) {	
					if (isset($transaction[$key + 1]['type']) && $transaction[$key + 1]['type'] == 'WTD') {
						$date_end = $transaction[$key]['date_end'];
					} else {
						$date_end = $transaction[$key + 1]['date_start'];
					}
				} else if(!empty($value['last_of_fixed_dept'])) {
					$date_end = $value['date_end'];
				} else {
					$date_end = date("Y-m-d", strtotime("+0 day", strtotime($date_interest)));
				}
				$transaction[$key]['date_end'] = $date_end;
				$value['date_end'] = $date_end;
			}

			$diff = @date_diff(date_create($value['date_start']),date_create($value['date_end']));
			$diff_opn = @date_diff(date_create($value['create_account_date']),date_create($value['date_end']));
			//$date_count = @$diff->format("%a");
			//$date_count = $date_count;
			//$transaction[$key]['date_count'] = $date_count;
			//$day_of_year = date("z", strtotime(substr($value['date_start'], 0, 4)."-12-31")) + 1;
			
			$date_count_all = @$diff->format("%a");
			$date_count_all = $date_count_all;
			$date_count_at_opn = @$diff_opn->format("%a");
			$transaction[$key]['date_count'] = $date_count_all;
			$transaction[$key]['date_count_at_opn'] = $date_count_at_opn;

			//เช็ควันของแต่ละปี 2 step
			if($row_detail['is_withdrawal_specify'] == 1) {
				$array_check_date = self::check_fixed_deposit_list($value['account_id'], $value['date_start'], $value['date_end'], $value['ref_account_no']);
			}else{
				$array_check_date = $this->check_interest_two_step($value['date_start'], $value['date_end'], $value['type_id'], $value['transaction_list']);
			}

			$all_interest = 0;
			foreach($array_check_date AS $key_chk=>$val_chk){
				$date_count = @$val_chk['date_count'];
				$day_of_year = $val_chk['day_of_year'];
				$transaction_balance = $val_chk['transaction_balance'];
				if(is_array($val_chk['interest_rate']) && $this->is_condition_step($value['date_start'], $value['date_end'], $value['type_id'])){
					$new_interest_condition_3 = 0;
					foreach($val_chk['interest_rate'] as $row_interest_condition_3) {
						//เช็คเพื่ออะไร ใช้ทำไม ใช้กลับเคสไหนบาง แล้วอะไรบางถึงจะเซตเป็น 0
						if($value['transaction_balance'] >= $row_interest_condition_3['amount_deposit'] && $row_interest_condition_3['amount_deposit']>0 && $row_detail['condition_interest'] == 3){
							$new_interest_condition_3 = $row_interest_condition_3['interest_rate'];
						}else if($row_detail['condition_interest'] == 1 || $row_detail['condition_interest'] == 2){
							$new_interest_condition_3 = $row_interest_condition_3['interest_rate'];
						}
					}
					$interest_rate = $val_chk['interest_rate'] = $new_interest_condition_3;
				}
				// ฝากไม่ถึง  x เดือน ไม่คิดดอกเบี้ย คิดเป็น วัน
				$num_month_no_interest = $row_detail['num_month_no_interest']*30;
				if($row_detail['type_interest'] == '3' &&  $date_count_all < $num_month_no_interest && $date_count_at_opn < $num_month_no_interest){
					//คิดดอกเบี้ย ไม่คิดดอกเบี้ย เมื่อ ฝากไม่ถึง  x เดือน ไม่คิดดอกเบี้ย
					$interest = 0;
				}else if($row_detail['type_interest'] == '2' || $row_detail['type_interest'] == '4' || ($row_detail['type_interest'] == '3' && $date_count_at_opn >= $num_month_no_interest)){
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
					} else {
						//$interest = ((($value['transaction_balance']*@$interest_rate)/100)*$date_count)/$day_of_year;
						if($row_detail['type_interest'] == '3'){
							$interest = round(((($transaction_balance*@$interest_rate)/100)*$date_count)/$day_of_year,2);
							//ส่วนที่ไว้เช็คเงินฝากออมทรัพย์พิเศษเกษียณเพิ่มสุข 12 เดือน
						}else{
							$interest = ((($transaction_balance*@$interest_rate)/100)*$date_count)/$day_of_year;
						}
					}
				} else {
					//คิดดอกเบี้ย ดอกเบี้ยทบต้น
					if($row_detail['is_withdrawal_specify'] == 1){
						$interest = ((($val_chk['transaction_balance'] * $interest_rate) / 100) * $date_count) / $day_of_year;
					}else {
						//$interest = ((($value['transaction_balance'] * @$val_chk['interest_rate']) / 100) * $date_count) / $day_of_year;
						$interest = round(((($value['transaction_balance'] * @$val_chk['interest_rate']) / 100) * $date_count) / $day_of_year,2);
						//echo '((('.$value['transaction_balance'].'*'.@$val_chk['interest_rate'].')/100)*'.$date_count.')/'.$day_of_year.'<br>';
					}
				}
				
				//$transaction[$key]['interest'] = number_format($interest,3,'.','');
				//$all_interest += round($interest, 2);
				$all_interest += $interest;
				
			}
			
			if($row_detail['type_interest'] == '3'){
				$all_interest = $all_interest;
			}else{
				$all_interest = round($all_interest, 2);
			}
			//echo "</pre>";
			$transaction[$key]['interest'] = $all_interest;
			
			$diff = @date_diff(date_create($value['depositor_date_start']),date_create($value['date_end']));
			$date_count = @$diff->format("%a");
			
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
		$interest_all = $this->round_down($interest_all, 2); //ตำแหน่งที่ 3 ปัดเศษทิ้ง
		//echo '<hr>';
		//echo $account_id."<br>";
		//echo $interest_all."<br>";
		//echo"<pre>";print_r($transaction);echo"</pre>";
		//exit;
		//writeToLog("transaction: ".json_encode($transaction), FCPATH."/application/logs/cal_dep_int.log", true);
		if($return_type == 'cal_interest'){			
			if(@$account_id != '' && @$interest_all > 0 || $row_detail['pay_interest'] == '3'){
				if($fix_saving === false){
					$interest = number_format($interest_all,2,'.','');
					$transaction_time = $date_interest." ".$time_interest;
					
					$this->db->select(array(
						'transaction_balance',
						'transaction_no_in_balance'
					));
					$this->db->from("coop_account_transaction{$this->forecast} as t1");
					$this->db->where("account_id = '".$account_id."' AND transaction_time <= '".$transaction_time."'");
					$this->db->order_by('transaction_time DESC, transaction_id DESC');
					$this->db->limit(1);
					$row_balance = $this->db->get()->row_array();
					$balance = $row_balance["transaction_balance"];
					$balance_no_interest = $row_balance["transaction_no_in_balance"];
					
					if($interest > 0 && $is_cal || $row_detail['pay_interest'] == '3'){
						if($row_detail['type_interest'] == '4' && $row_detail['condition_age'] == '1') {
							$this->db->select("SUM(transaction_deposit) AS interest_deduct");
							$this->db->from("coop_account_transaction{$this->forecast} as t1");
							$this->db->where("account_id = '".$account_id."' AND transaction_list IN ('INT', 'IN')
								AND transaction_time < '".$transaction_time."'
								AND YEAR(transaction_time) = YEAR('".$transaction_time."')
								AND MONTH(transaction_time) = MONTH('".$transaction_time."')".
								($is_prev_holiday ? " AND transaction_time > '".$prev_int_date." 23:59:59'" : ""));
							if($row_int = $this->db->get()->row_array()) {
								if($this->debug) { echo"<pre>";print_r($row_int);echo"</pre>"; }
								$interest -= (double)$row_int["interest_deduct"];
							}
						}
						
						$sum = $balance + $interest;
						
						if(!($from_holiday && $row_detail['pay_interest'] == '1' && $row_detail['type_interest'] != '4')) {
							$data_insert = array();
							$data_insert['transaction_time'] = $transaction_time;
							$data_insert['transaction_list'] = 'IN';
							$data_insert['transaction_withdrawal'] = '';
							$data_insert['transaction_deposit'] = $interest;
							$data_insert['transaction_balance'] = number_format($sum,2,'.','');
							$data_insert['transaction_no_in_balance'] = $balance_no_interest;
							$data_insert['user_id'] = $this->user_id;
							$data_insert['account_id'] = $account_id;
							if($this->debug) {
								echo"<pre>";print_r($data_insert);echo"</pre>";
							}
							else {
								writeToLog("insert: ".json_encode($data_insert), FCPATH."/application/logs/cal_dep_int.log", true);
								$this->db->insert($this->testmode ? "coop_account_transaction_tmp" : "coop_account_transaction{$this->forecast}", $data_insert);
							}
						}
						
						if(!empty($row_detail['percent_depositor']) && empty($row_detail['num_month_before'])) {
							$coop_interest = round($interest * ($interest_rate - $row_detail['percent_depositor']) / $interest_rate, 2);
							$data_insert['transaction_list'] = 'WTI';
							$data_insert['transaction_withdrawal'] = $coop_interest;
							$data_insert['transaction_deposit'] = '';
							$data_insert['transaction_balance'] = number_format($sum - $coop_interest,2,'.','');
							if($this->debug) {
								echo"<pre>";print_r($data_insert);echo"</pre>";
							}
							else {
								writeToLog("insert: ".json_encode($data_insert), FCPATH."/application/logs/cal_dep_int.log", true);
								$this->db->insert($this->testmode ? "coop_account_transaction_tmp" : "coop_account_transaction{$this->forecast}", $data_insert);
							}
						}
						
						if($row_detail['type_interest'] == '4') {
							$this->db->select('transfer_type, dividend_acc_num');
							$this->db->from('coop_maco_account');
							$this->db->where("account_id = '".$row_member["account_id"]."'");
							$row_acc = $this->db->get()->row_array();
							
							$data_insert['transaction_list'] = $row_acc["transfer_type"] == 1 ? 'WTI' : 'WTB';
							$data_insert['transaction_withdrawal'] = $interest;
							$data_insert['transaction_deposit'] = '';
							$data_insert['transaction_balance'] = number_format($sum - $interest,2,'.','');
							if($this->debug) {
								echo"<pre>";print_r($data_insert);echo"</pre>";
							}
							else {
								writeToLog("insert: ".json_encode($data_insert), FCPATH."/application/logs/cal_dep_int.log", true);
								$this->db->insert($this->testmode ? "coop_account_transaction_tmp" : "coop_account_transaction{$this->forecast}", $data_insert);
							}
							
							if($row_acc["transfer_type"] == 1 && !empty($row_acc["dividend_acc_num"])) {
								$this->db->select(array(
									'transaction_balance',
									'transaction_no_in_balance'
								));
								$this->db->from("coop_account_transaction{$this->forecast} as t1");
								$this->db->where("account_id = '".$row_acc["dividend_acc_num"]."' AND transaction_time <= '".$transaction_time."'");
								$this->db->order_by('transaction_time DESC, transaction_id DESC');
								$this->db->limit(1);
								$row_balance = $this->db->get()->row_array();
								$balance2 = $row_balance["transaction_balance"];
								$balance_no_interest = $row_balance["transaction_no_in_balance"];
								
								$sum2 = $balance2 + $interest;
								
								$data_insert = array();
								$data_insert['transaction_time'] = $transaction_time;
								$data_insert['transaction_list'] = 'TRB';
								$data_insert['transaction_withdrawal'] = '';
								$data_insert['transaction_deposit'] = $interest;
								$data_insert['transaction_balance'] = number_format($sum2,2,'.','');
								$data_insert['transaction_no_in_balance'] = $balance_no_interest;
								$data_insert['user_id'] = $this->user_id;
								$data_insert['account_id'] = $row_acc["dividend_acc_num"];
								$data_insert['ref_no'] = $row_member["account_id"];
								if($this->debug) {
									echo"<pre>";print_r($data_insert);echo"</pre>";
								}
								else {
									writeToLog("insert: ".json_encode($data_insert), FCPATH."/application/logs/cal_dep_int.log", true);
									$this->db->insert($this->testmode ? "coop_account_transaction_tmp" : "coop_account_transaction{$this->forecast}", $data_insert);
								}
							}
						}
						
						//$sum_account_interest += $value['interest'];
						
						/*$data['coop_account']['account_description'] = "ดอกเบี้ยเงินฝาก เลขที่บัญชี ".$account_id;
						$data['coop_account']['account_datetime'] = $date_interest." ".date('H:i:s');
						
						$i=0;
						$data['coop_account_detail'][$i]['account_type'] = 'debit';
						$data['coop_account_detail'][$i]['account_amount'] = $interest;
						$data['coop_account_detail'][$i]['account_chart_id'] = '50100';
						$i++;
						$data['coop_account_detail'][$i]['account_type'] = 'credit';
						$data['coop_account_detail'][$i]['account_amount'] = $interest;
						$data['coop_account_detail'][$i]['account_chart_id'] = '10100';*/
						//$this->account_transaction->account_process($data);
					}
					
					if($is_due && !$is_holiday) {
						foreach($rs_due as $row_due) {
							if($row_detail["pay_interest"] == 1) {
								$this->db->select("DATEDIFF('".$date_interest."', transaction_time) AS date_count");
								$this->db->from("coop_account_transaction{$this->forecast}");
								$this->db->where("account_id = '".$value['account_id']."' AND transaction_time < '".$transaction_time."' AND transaction_list IN ('INT', 'IN') AND fixed_deposit_status = '0'");
								//$this->db->where("account_id = '".$value['account_id']."' AND transaction_time >= DATE_ADD('".date("Y-m-", strtotime($transaction_time))."01', INTERVAL -1 DAY) AND transaction_list IN ('INT', 'IN')");
								$this->db->order_by('transaction_time DESC, transaction_id DESC');
								$this->db->limit(1);
							}
							else {
								$this->db->select("DATEDIFF('".$date_interest."', transaction_time) AS date_count");
								$this->db->from("coop_account_transaction{$this->forecast}");
								$this->db->where("account_id = '".$value['account_id']."' AND transaction_time < '".$transaction_time."' AND transaction_list IN ('INT', 'IN')");
								$this->db->order_by('transaction_time DESC, transaction_id DESC');
								$this->db->limit(1);
							}
							$row_date = $this->db->get()->row_array();
							
							foreach($row_interest_rate as $key2 => $value2){
								if(strtotime($date_interest) >= strtotime($value2['start_date']) && strtotime($date_interest) <= strtotime($value2['end_date'])){
									$interest_rate = $value2['interest_rate'];
									break;
								}
							}
							
							$interest = 0;
							if($row_detail["pay_interest"] == 1 && date("d", strtotime($date_interest)) != date("t", strtotime($date_interest))) {
								$day_of_year = date("z", strtotime(substr($date_interest, 0, 4)."-12-31")) + 1;
								$interest = round(((($row_due["transaction_deposit"]*@$interest_rate)/100)*$row_date["date_count"])/$day_of_year, 2);
								
								// ดอกเบี้ยปิดยอด
								$data_insert = array();
								$data_insert['transaction_time'] = $transaction_time;
								$data_insert['transaction_list'] = 'IN';
								$data_insert['transaction_withdrawal'] = '';
								$data_insert['transaction_deposit'] = $interest;
								$data_insert['transaction_balance'] = number_format($balance + $interest,2,'.','');
								$data_insert['transaction_no_in_balance'] = $balance_no_interest;
								$data_insert['user_id'] = $this->user_id;
								$data_insert['account_id'] = $account_id;
								$data_insert['fixed_deposit_status'] = '1';
								if($this->debug) {
									echo"<pre>";print_r($data_insert);echo"</pre>";
								}
								else {
									writeToLog("insert: ".json_encode($data_insert), FCPATH."/application/logs/cal_dep_int.log", true);
									$this->db->insert($this->testmode ? "coop_account_transaction_tmp" : "coop_account_transaction{$this->forecast}", $data_insert);
								}
							}
							
							$sum = $row_due["transaction_deposit"] + $interest;
							
							if($row_detail['condition_age'] == 1) {
								// ถอนและปิดบัญชีทันที (เป็นยอดๆ) และแจ้งเตือน
								$data_insert['account_id'] = $account_id;
								$data_insert['transaction_list'] = 'WTD';
								$data_insert['transaction_withdrawal'] = $sum;
								$data_insert['transaction_deposit'] = '';
								$data_insert['transaction_balance'] = number_format($balance + $interest - $sum,2,'.','');
								if($this->debug) {
									echo"<pre>";print_r($data_insert);echo"</pre>";
								}
								else {
									writeToLog("insert: ".json_encode($data_insert), FCPATH."/application/logs/cal_dep_int.log", true);
									$this->db->insert($this->testmode ? "coop_account_transaction_tmp" : "coop_account_transaction{$this->forecast}", $data_insert);
								}
							}
							elseif($row_detail['condition_age'] == 2) {
								// ถอนและปิดบัญชีทันที (ทั้งเล่ม) และแจ้งเตือน
								$data_insert['account_id'] = $account_id;
								$data_insert['transaction_list'] = 'W/C';
								$data_insert['transaction_withdrawal'] = $balance;
								$data_insert['transaction_deposit'] = '';
								$data_insert['transaction_balance'] = 0;
								if($this->debug) {
									echo"<pre>";print_r($data_insert);echo"</pre>";
								}
								else {
									writeToLog("insert: ".json_encode($data_insert), FCPATH."/application/logs/cal_dep_int.log", true);
									$this->db->insert($this->testmode ? "coop_account_transaction_tmp" : "coop_account_transaction{$this->forecast}", $data_insert);
								}
							}
							elseif($row_detail['condition_age'] == 3) {
								// โอนเข้าบัญชีอื่นๆ  (เลือกบัญชีจากส่วนจัดการข้อมูลสมาชิก)
								$data_insert['account_id'] = $account_id;
								$data_insert['transaction_list'] = 'WTD';
								$data_insert['transaction_withdrawal'] = $sum;
								$data_insert['transaction_deposit'] = '';
								$data_insert['transaction_balance'] = number_format($balance + $interest - $sum,2,'.','');
								if($this->debug) {
									echo"<pre>";print_r($data_insert);echo"</pre>";
								}
								else {
									writeToLog("insert: ".json_encode($data_insert), FCPATH."/application/logs/cal_dep_int.log", true);
									$this->db->insert($this->testmode ? "coop_account_transaction_tmp" : "coop_account_transaction{$this->forecast}", $data_insert);
								}
								
								$this->db->select('dividend_acc_num');
								$this->db->from('coop_maco_account');
								$this->db->where("transfer_type = '1' AND account_id = '".$row_member["account_id"]."'");
								$row_acc = $this->db->get()->row_array();
								if(!empty($row_acc["dividend_acc_num"])) {
									$this->db->select(array(
										'transaction_balance',
										'transaction_no_in_balance'
									));
									$this->db->from("coop_account_transaction{$this->forecast} as t1");
									$this->db->where("account_id = '".$row_acc["dividend_acc_num"]."' AND transaction_time <= '".$transaction_time."'");
									$this->db->order_by('transaction_time DESC, transaction_id DESC');
									$this->db->limit(1);
									$row_balance = $this->db->get()->row_array();
									$balance = $row_balance["transaction_balance"];
									$balance_no_interest = $row_balance["transaction_no_in_balance"];
									
									$data_insert2 = array();
									$data_insert2['transaction_time'] = $transaction_time;
									$data_insert2['transaction_list'] = 'TRB';
									$data_insert2['transaction_withdrawal'] = '';
									$data_insert2['transaction_deposit'] = $sum;
									$data_insert2['transaction_balance'] = number_format($balance - $sum,2,'.','');
									$data_insert2['transaction_no_in_balance'] = $balance_no_interest;
									$data_insert2['user_id'] = $this->user_id;
									$data_insert2['account_id'] = $row_acc["dividend_acc_num"];
									$data_insert2['ref_no'] = $row_member["account_id"];
									if($this->debug) {
										echo"<pre>";print_r($data_insert2);echo"</pre>";
									}
									else {
										writeToLog("insert: ".json_encode($data_insert2), FCPATH."/application/logs/cal_dep_int.log", true);
										$this->db->insert($this->testmode ? "coop_account_transaction_tmp" : "coop_account_transaction{$this->forecast}", $data_insert2);
									}
								}
							}
							
							$balance = $data_insert['transaction_balance'];
							
							if(in_array($row_detail['condition_age'], array(1, 2, 3))) {
								if($data_insert['transaction_balance'] == 0) {
									if($this->debug) {
										echo"<pre>Close Account {$account_id}</pre>";
									}
									elseif(!$this->testmode && empty($this->forecast)) {
										writeToLog("update: account_id: {$account_id}, account_status: 1, close_account_date: {$transaction_time}", FCPATH."/application/logs/cal_dep_int.log", true);
										$this->db->where("account_id", $account_id);
										$this->db->update("coop_maco_account", array("account_status" => "1", "close_account_date" => $transaction_time));
									}
								}
							}
						}
					}
				}else{		
					//@start 2019-09-20 เพิ่มการบันทีดอกเบี้ย  จ่ายเมื่อครบกำหนด X เดือน (เงินฝากทั่วไป)
					if($row_detail['pay_interest'] == '5'){						
						$transaction_interest = 0;						
						foreach($transaction as $key => $value){								
								$transaction_interest += $value['interest'];
						}
						$transaction_interest = $this->round_down($transaction_interest, 2); //ตำแหน่งที่ 3 ปัดเศษทิ้ง
						
						$arr_transaction = array();
						$r=0;
						$chk_account_id = '';
						foreach($transaction as $key => $value){
							if($value['account_id'] != $chk_account_id){
								$arr_transaction[$r] = $value;
								$arr_transaction[$r]['interest'] = $transaction_interest;
								$chk_account_id = $value['account_id'];
								$r++;
							}	
						}
						$transaction = $arr_transaction;
					}
					//@end 2019-09-20 เพิ่มการบันทีดอกเบี้ย  จ่ายเมื่อครบกำหนด X เดือน (เงินฝากทั่วไป)

					//echo 'is_withdrawal_specify='.@$row_detail['is_withdrawal_specify'].'<br>';
					//echo "===========transaction==========<br>";
					//echo '<pre>'; print_r($transaction); echo '</pre>';
					//exit;
					foreach($transaction as $key => $value){
						$interest = number_format($value['interest'],2,'.','');
						if($interest > 0){
							$transaction_time = $date_interest." ".$time_interest;
							$adj_interest_period = $row_detail['pay_interest'] == 4 ? $value['interest_period'] * $row_detail['num_month_maturity'] : $value['interest_period'];
							//echo "((".$row_detail['max_month']." == ".$adj_interest_period." && !".$is_holiday.") || (".$row_detail['max_month']." != ".$value['interest_period']." && ".$date_interest." == ".$date_dep.")  || (".$row_detail['max_month']." == ".$value['interest_period']." && ".$date_interest." == ".$date_dep." && ".$row_detail['is_withdrawal_specify'] ."== '1'))";
							//if(($row_detail['max_month'] == $adj_interest_period && !$is_holiday) || ($row_detail['max_month'] != $value['interest_period'] && $date_interest == $date_dep)) {
							if(($row_detail['max_month'] == $adj_interest_period && !$is_holiday) || ($row_detail['max_month'] != $value['interest_period'] && $date_interest == $date_dep)  || ($row_detail['max_month'] == $value['interest_period'] && $date_interest == $date_dep && $row_detail['is_withdrawal_specify'] == '1')) {
								//$row_detail['is_withdrawal_specify']= 1 คือ  แบบเลือกยอดถอนเงินฝาก
								$this->db->select(array(
									'transaction_balance',
									'transaction_no_in_balance'
								));
								$this->db->from("coop_account_transaction{$this->forecast} as t1");
								$this->db->where("account_id = '".$value['account_id']."' AND transaction_time <= '".$transaction_time."'");
								$this->db->order_by('transaction_time DESC, transaction_id DESC');
								$this->db->limit(1);
								$row_balance = $this->db->get()->row_array();
								
								$balance = $row_balance["transaction_balance"];
								$balance_no_interest = $row_balance["transaction_no_in_balance"];
	
								$sum = $balance + $interest;
								
								$data_insert = array();
								$data_insert['transaction_time'] = $transaction_time;
								$data_insert['transaction_list'] = 'IN';
								$data_insert['transaction_withdrawal'] = '';
								$data_insert['transaction_deposit'] = $interest;
								$data_insert['transaction_balance'] = number_format($sum,2,'.','');
								$data_insert['transaction_no_in_balance'] = $balance_no_interest;
								$data_insert['user_id'] = $this->user_id;
								$data_insert['account_id'] = $account_id;
								$data_insert['deposit_interest_balance'] = $value['deposit_interest_balance'] + $interest;
								$data_insert['ref_transaction_id'] = $value['ref_transaction_id'];
								$data_insert['interest_period'] = $value['interest_period'];
								$data_insert['fixed_deposit_status'] = '0';
								$data_insert['fixed_deposit_type'] = 'interest';
								if(@$row_detail['is_withdrawal_specify'] == '1') {
									//แบบเลือกยอดถอนเงินฝาก
									$this->db->select(array(
										'transaction_balance',
										'transaction_no_in_balance',
										'balance_deposit',
										'ref_account_no'
									));
									$this->db->from("coop_account_transaction{$this->forecast} as t1");
									$this->db->where("account_id = '".$value['account_id']."' AND ref_account_no = '".$value['ref_account_no']."'");
									$this->db->order_by('transaction_time DESC, transaction_id DESC');
									$this->db->limit(1);
									$row_balance_last_ref = $this->db->get()->row_array();
									 
									$balance_deposit = @$row_balance_last_ref['balance_deposit'];
									$balance_deposit_int = @$interest;
									$ref_account_no = @$row_balance_last_ref['ref_account_no'];						
									
									$data_insert['balance_deposit'] = number_format($balance_deposit,2,'.','');
									$data_insert['balance_deposit_int'] = number_format($balance_deposit_int,2,'.','');
									$data_insert['ref_account_no'] = $ref_account_no;
								}
								
								if($this->debug) {
									echo"<pre>";print_r($data_insert);echo"</pre>";
								}
								else {
									writeToLog("insert: ".json_encode($data_insert), FCPATH."/application/logs/cal_dep_int.log", true);
									$this->db->insert($this->testmode ? "coop_account_transaction_tmp" : "coop_account_transaction{$this->forecast}", $data_insert);
								}
								
								if($row_detail['is_tax']) {
									$tax = $interest * $row_detail['tax_rate'] / 100;
									$tax = number_format($tax,2,'.','');
									$sum -= $tax;
									
									$data_insert['transaction_list'] = 'WTX';
									$data_insert['transaction_withdrawal'] = $tax;
									$data_insert['transaction_deposit'] = '';
									$data_insert['transaction_balance'] = number_format($sum,2,'.','');
									if(@$row_detail['is_withdrawal_specify'] == '1') {
										//แบบเลือกยอดถอนเงินฝาก
										$balance_deposit = @$row_balance_last_ref['balance_deposit'];
										$balance_deposit_int =  @$interest-@$tax;				
										
										$data_insert['balance_deposit'] = number_format($balance_deposit,2,'.','');
										$data_insert['balance_deposit_int'] = number_format($balance_deposit_int,2,'.','');
										$data_insert['ref_account_no'] = $ref_account_no;
									}
									
									if($this->debug) {
										echo"<pre>";print_r($data_insert);echo"</pre>";
									}
									else {
										writeToLog("insert: ".json_encode($data_insert), FCPATH."/application/logs/cal_dep_int.log", true);
										$this->db->insert($this->testmode ? "coop_account_transaction_tmp" : "coop_account_transaction{$this->forecast}", $data_insert);
									}
								}
								
								if(@$row_detail['is_withdrawal_specify'] == '1') {										
									//ถอน
									$money_due_withdrawal = @$row_balance_last_ref['balance_deposit'];
									$sum -= @$money_due_withdrawal;
									
									$balance_deposit = @$balance_deposit-@$row_balance_last_ref['balance_deposit'];
									$balance_deposit_int = @$interest-@$tax;
									
									$data_insert['transaction_list'] = 'WFX';
									$data_insert['transaction_withdrawal'] = @$money_due_withdrawal;
									$data_insert['transaction_deposit'] = 0;
									$data_insert['transaction_balance'] = number_format($sum,2,'.','');
									$data_insert['balance_deposit'] = number_format($balance_deposit,2,'.','');
									$data_insert['balance_deposit_int'] = number_format($balance_deposit_int,2,'.','');
									$data_insert['ref_account_no'] = $ref_account_no;
									
									if($this->debug) {
										echo"<pre>";print_r($data_insert);echo"</pre>";
									}
									else {
										writeToLog("insert: ".json_encode($data_insert), FCPATH."/application/logs/cal_dep_int.log", true);
										$this->db->insert($this->testmode ? "coop_account_transaction_tmp" : "coop_account_transaction{$this->forecast}", $data_insert);
									}

									//ถอนดอกเบี้ย
									$money_due_withdrawal_interest = @$interest-@$tax;
									$sum -= @$money_due_withdrawal_interest;
									$balance_deposit = 0;
									$balance_deposit_int = 0;

									$data_insert['transaction_list'] = 'WFI';
									$data_insert['transaction_withdrawal'] = @$money_due_withdrawal_interest;
									$data_insert['transaction_deposit'] = 0;
									$data_insert['transaction_balance'] = number_format($sum,2,'.','');
									$data_insert['balance_deposit'] = number_format($balance_deposit,2,'.','');
									$data_insert['balance_deposit_int'] = number_format($balance_deposit_int,2,'.','');
									$data_insert['ref_account_no'] = $ref_account_no;

									if($this->debug) {
										echo"<pre>";print_r($data_insert);echo"</pre>";
									}
									else {
										writeToLog("insert: ".json_encode($data_insert), FCPATH."/application/logs/cal_dep_int.log", true);
										$this->db->insert($this->testmode ? "coop_account_transaction_tmp" : "coop_account_transaction{$this->forecast}", $data_insert);
									}
									
									//ฝาก
									$money_due = $money_due_withdrawal+$money_due_withdrawal_interest;
									$sum += $money_due;
									$balance_deposit = @$money_due;
									$balance_deposit_int = 0;
									
									$data_insert['transaction_list'] = 'DFX';
									$data_insert['transaction_withdrawal'] = 0;
									$data_insert['transaction_deposit'] = $money_due;
									$data_insert['transaction_balance'] = number_format($sum,2,'.','');
									$data_insert['balance_deposit'] = number_format($balance_deposit,2,'.','');
									$data_insert['balance_deposit_int'] = number_format($balance_deposit_int,2,'.','');
									$data_insert['ref_account_no'] = $ref_account_no;
									
									if($this->debug) {
										echo"<pre>";print_r($data_insert);echo"</pre>";
									}
									else {
										writeToLog("insert: ".json_encode($data_insert), FCPATH."/application/logs/cal_dep_int.log", true);
										$this->db->insert($this->testmode ? "coop_account_transaction_tmp" : "coop_account_transaction{$this->forecast}", $data_insert);
									}
								}
								
								
								if($row_detail['type_interest'] == '4') {
									$this->db->select('transfer_type, dividend_acc_num');
									$this->db->from('coop_maco_account');
									$this->db->where("account_id = '".$row_member["account_id"]."'");
									$row_acc = $this->db->get()->row_array();
									
									$data_insert['transaction_list'] = $row_acc["transfer_type"] == 1 ? 'WTI' : 'WTB';
									$data_insert['transaction_withdrawal'] = $interest;
									$data_insert['transaction_deposit'] = '';
									$data_insert['transaction_balance'] = number_format($sum - $interest,2,'.','');
									if($this->debug) {
										echo"<pre>";print_r($data_insert);echo"</pre>";
									}
									else {
										writeToLog("insert: ".json_encode($data_insert), FCPATH."/application/logs/cal_dep_int.log", true);
										$this->db->insert($this->testmode ? "coop_account_transaction_tmp" : "coop_account_transaction{$this->forecast}", $data_insert);
									}
									
									if($row_acc["transfer_type"] == 1 && !empty($row_acc["dividend_acc_num"])) {
										$this->db->select(array(
											'transaction_balance',
											'transaction_no_in_balance'
										));
										$this->db->from("coop_account_transaction{$this->forecast} as t1");
										$this->db->where("account_id = '".$row_acc["dividend_acc_num"]."' AND transaction_time <= '".$transaction_time."'");
										$this->db->order_by('transaction_time DESC, transaction_id DESC');
										$this->db->limit(1);
										$row_balance = $this->db->get()->row_array();
										$balance = $row_balance["transaction_balance"];
										$balance_no_interest = $row_balance["transaction_no_in_balance"];
										
										$sum2 = $balance + $interest;
										
										$data_insert = array();
										$data_insert['transaction_time'] = $transaction_time;
										$data_insert['transaction_list'] = 'TRB';
										$data_insert['transaction_withdrawal'] = '';
										$data_insert['transaction_deposit'] = $interest;
										$data_insert['transaction_balance'] = number_format($sum2,2,'.','');
										$data_insert['transaction_no_in_balance'] = $balance_no_interest;
										$data_insert['user_id'] = $this->user_id;
										$data_insert['account_id'] = $row_acc["dividend_acc_num"];
										$data_insert['ref_no'] = $row_member["account_id"];
										if($this->debug) {
											echo"<pre>";print_r($data_insert);echo"</pre>";
										}
										else {
											writeToLog("insert: ".json_encode($data_insert), FCPATH."/application/logs/cal_dep_int.log", true);
											$this->db->insert($this->testmode ? "coop_account_transaction_tmp" : "coop_account_transaction{$this->forecast}", $data_insert);
										}
									}
								}
							}
							
							if($row_detail['max_month'] == $adj_interest_period && !$is_holiday) {
								$where = "fixed_deposit_status <> '1' AND account_id = '".$row_member['account_id']."' AND transaction_list IN ('OPN', 'OCA', 'OPT', 'TRB', 'XD', 'DEP', 'DEPP', 'DCA', 'DFX', 'CD','DOT') AND
												TIMESTAMPDIFF(MONTH, transaction_time, '".$date_dep."') +
												DATEDIFF('".$date_dep."', transaction_time + INTERVAL TIMESTAMPDIFF(MONTH, transaction_time, '".$date_dep."') MONTH) /
												DATEDIFF(transaction_time + INTERVAL TIMESTAMPDIFF(MONTH, transaction_time, '".$date_dep."') + 1 MONTH, transaction_time + INTERVAL TIMESTAMPDIFF(MONTH, transaction_time, '".$date_dep."') MONTH) < ".$row_detail['max_month']."";
								$row_chk = $this->db->select(array("transaction_id"))
												->from("coop_account_transaction{$this->forecast}")
												->where($where)->get()->row_array();
								$is_due_all = empty($row_chk) ? (count($transaction) == $key + 1) : false;
								
								if($row_detail['condition_age'] == 1) {
									// ถอนและปิดบัญชีทันที (เป็นยอดๆ) และแจ้งเตือน
									$data_insert['account_id'] = $account_id;
									$data_insert['transaction_list'] = 'WTD';
									$data_insert['transaction_withdrawal'] = $is_due_all ? $sum : $value['deposit_interest_balance'] + $interest;
									$data_insert['transaction_deposit'] = '';
									$data_insert['transaction_balance'] = number_format($is_due_all ? 0 : $sum - $value['deposit_interest_balance'] - $interest,2,'.','');
									if($this->debug) {
										echo"<pre>";print_r($data_insert);echo"</pre>";
									}
									else {
										writeToLog("insert: ".json_encode($data_insert), FCPATH."/application/logs/cal_dep_int.log", true);
										$this->db->insert($this->testmode ? "coop_account_transaction_tmp" : "coop_account_transaction{$this->forecast}", $data_insert);
									}
								}
								elseif($row_detail['condition_age'] == 2) {
									// ถอนและปิดบัญชีทันที (ทั้งเล่ม) และแจ้งเตือน
									$data_insert['account_id'] = $account_id;
									$data_insert['transaction_list'] = 'W/C';
									$data_insert['transaction_withdrawal'] = number_format($sum,2,'.','');
									$data_insert['transaction_deposit'] = '';
									$data_insert['transaction_balance'] = 0;
									if($this->debug) {
										echo"<pre>";print_r($data_insert);echo"</pre>";
									}
									else {
										writeToLog("insert: ".json_encode($data_insert), FCPATH."/application/logs/cal_dep_int.log", true);
										$this->db->insert($this->testmode ? "coop_account_transaction_tmp" : "coop_account_transaction{$this->forecast}", $data_insert);
									}
								}
								elseif($row_detail['condition_age'] == 3) {
									// โอนเข้าบัญชีอื่นๆ  (เลือกบัญชีจากส่วนจัดการข้อมูลสมาชิก)
									$data_insert['account_id'] = $account_id;
									$data_insert['transaction_list'] = 'WTD';
									$data_insert['transaction_withdrawal'] = $is_due_all ? $sum : $value['deposit_interest_balance'] + $interest;
									$data_insert['transaction_deposit'] = '';
									$data_insert['transaction_balance'] = number_format($is_due_all ? 0 : $sum - $value['deposit_interest_balance'] - $interest,2,'.','');
									if($this->debug) {
										echo"<pre>";print_r($data_insert);echo"</pre>";
									}
									else {
										writeToLog("insert: ".json_encode($data_insert), FCPATH."/application/logs/cal_dep_int.log", true);
										$this->db->insert($this->testmode ? "coop_account_transaction_tmp" : "coop_account_transaction{$this->forecast}", $data_insert);
									}
									
									$this->db->select('dividend_acc_num');
									$this->db->from('coop_maco_account');
									$this->db->where("transfer_type = '1' AND account_id = '".$row_member["account_id"]."'");
									$row_acc = $this->db->get()->row_array();
									if(!empty($row_acc["dividend_acc_num"])) {
										$this->db->select(array(
											'transaction_balance',
											'transaction_no_in_balance'
										));
										$this->db->from("coop_account_transaction{$this->forecast} as t1");
										$this->db->where("account_id = '".$row_acc["dividend_acc_num"]."' AND transaction_time <= '".$transaction_time."'");
										$this->db->order_by('transaction_time DESC, transaction_id DESC');
										$this->db->limit(1);
										$row_balance = $this->db->get()->row_array();
										$balance = $row_balance["transaction_balance"];
										$balance_no_interest = $row_balance["transaction_no_in_balance"];
										
										$dep = $is_due_all ? $sum : $value['deposit_interest_balance'] + $interest;
										$sum2 = $balance + $dep;
										
										$data_insert2 = array();
										$data_insert2['transaction_time'] = $transaction_time;
										$data_insert2['transaction_list'] = 'TRB';
										$data_insert2['transaction_withdrawal'] = '';
										$data_insert2['transaction_deposit'] = $dep;
										$data_insert2['transaction_balance'] = number_format($sum2,2,'.','');
										$data_insert2['transaction_no_in_balance'] = $balance_no_interest;
										$data_insert2['user_id'] = $this->user_id;
										$data_insert2['account_id'] = $row_acc["dividend_acc_num"];
										$data_insert2['ref_no'] = $row_member["account_id"];
										if($this->debug) {
											echo"<pre>";print_r($data_insert2);echo"</pre>";
										}
										else {
											writeToLog("insert: ".json_encode($data_insert2), FCPATH."/application/logs/cal_dep_int.log", true);
											$this->db->insert($this->testmode ? "coop_account_transaction_tmp" : "coop_account_transaction{$this->forecast}", $data_insert2);
										}
									}
								}
								
								if(in_array($row_detail['condition_age'], array(1, 2, 3))) {
									if($data_insert['transaction_balance'] == 0) {
										if($this->debug) {
											echo"<pre>Close Account {$account_id}</pre>";
										}
										elseif(!$this->testmode && empty($this->forecast)) {
											writeToLog("update: account_id: {$account_id}, account_status: 1, close_account_date: {$transaction_time}", FCPATH."/application/logs/cal_dep_int.log", true);
											$this->db->where("account_id", $account_id);
											$this->db->update("coop_maco_account", array("account_status" => "1", "close_account_date" => $transaction_time));
										}
									}
								}
							}
						}
					}
				}
			}
		}elseif($return_type == 'return_interest'){
			if(in_array($row_detail['pay_interest'], array('3', '1', '2','4','5','6'))) {
				$interest_return = 0;
				$tax_return = 0;
				
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
							$this->db->from("coop_account_transaction{$this->forecast}");
							$this->db->where("account_id = '".$row_member['account_id']."' AND transaction_time <= DATE_ADD('".$date_interest." 23:59:59', INTERVAL -".($row_detail['maturity_num_year'] * 12)." MONTH)");
							$this->db->order_by("transaction_time, transaction_id");
							$this->db->limit(1);
							if(!($row_chk = $this->db->get()->row_array())) {
								$this->db->select("SUM(transaction_deposit) AS return_interest");
								$this->db->from("coop_account_transaction{$this->forecast}");
								$this->db->where("account_id = '".$row_member['account_id']."' AND transaction_list IN ('INT', 'IN')");
								$row_return = $this->db->get()->row_array();
								$interest_return = $row_return['return_interest'] + $interest_all;
							}
						}
					}
					
					//ต้องฝากครบ X ปี ถ้าไม่ครบปีไม่ได้ ต้องคืนเงิน
					$this->db->select("transaction_id");
					$this->db->from("coop_account_transaction{$this->forecast}");
					$this->db->where("account_id = '".$row_member['account_id']."' AND transaction_time <= DATE_ADD('".$date_interest." 23:59:59', INTERVAL -".($row_detail['maturity_num_year'] * 12)." MONTH)");
					$this->db->order_by("transaction_time, transaction_id");
					$this->db->limit(1);
					$chk_maturity = $this->db->get()->row_array();
					if(!($chk_maturity) && $row_detail['staus_maturity'] == '1') {
						$this->db->select("SUM(transaction_deposit) AS return_interest");
						$this->db->from("coop_account_transaction{$this->forecast}");
						$this->db->where("account_id = '".$row_member['account_id']."' AND transaction_list IN ('INT', 'IN')");
						$row_return = $this->db->get()->row_array();
						$interest_return = $row_return['return_interest'];
						$interest_all = 0;							
					}else{
						//เช็คเงินฝากถอนก่อนกำหนด
						$this->db->select(array('created'));
						$this->db->from('coop_maco_account');
						$this->db->where("account_id = '".$row_member['account_id']."'");
						$row_account = $this->db->get()->result_array();
						$row_account = $row_account[0];
						$create_date = date('Y-m-d',strtotime($row_account['created']));
						$end_date = date('Y-m-d',strtotime('+ '.$row_detail['num_month_before'].' month',strtotime($create_date)));
						
						if($row_detail['pay_interest'] == '4'){
							$chk_date_ext_maturity = $this->get_date_last_account($row_member['account_id'],$num_month_maturity,$row_detail['ext_num_month_maturity_day']);
							if($chk_date_ext_maturity != ''){
								$end_date = date('Y-m-d',strtotime('+ '.$row_detail['ext_num_month_maturity_day'].' day',strtotime($chk_date_ext_maturity)));
							}
						}
						
						if($date_interest < $end_date){
							//ผู้ฝากได้รับดอกเบี้ย xx %ที่เหลือสหกรณ์ได้รับดอกเบี้ย
							if(!empty($row_detail['percent_depositor'])) {
								//$interest_all = round($interest_all * $row_detail['percent_depositor'] / $interest_rate, 2);
								if($interest_rate != 0) {
									$interest_return = round($interest_all * ($interest_rate - $row_detail['percent_depositor']) / $interest_rate, 2);
								}
							}
						
							//เสียภาษีอัตรา % ของดอกเบี้ย เมื่อถอนก่อนกำหนด
							if(@$row_detail['before_due_tax_rate'] > 0) {
								$interest = $interest_all - $interest_return;
								$tax_return = $interest * $row_detail['before_due_tax_rate'] / 100;
							}	
						}
						
						if($row_detail['is_tax']) {
							$tax_return = $interest * $row_detail['tax_rate'] / 100;
						}
					}	
				}
				return array(
					"interest" => $interest_all,
					"interest_return" => $interest_return,
					"tax_return" => $tax_return,
					"percent_depositor" => $row_detail['percent_depositor'],
					"detail" => $transaction
				);
			}
		}else{
			if($is_due) {
				$transaction_time = $date_interest." ".$time_interest;
				$transaction["due"] = array();
				foreach($rs_due as $row_due) {
					$this->db->select("DATEDIFF('".$date_interest."', transaction_time) AS date_count");
					$this->db->from("coop_account_transaction{$this->forecast}");
					$this->db->where("account_id = '".$value['account_id']."' AND transaction_time < '".$transaction_time."' AND transaction_list IN ('INT', 'IN')");
					$this->db->order_by('transaction_time DESC, transaction_id DESC');
					$this->db->limit(1);
					$row_date = $this->db->get()->row_array();
					
					foreach($row_interest_rate as $key2 => $value2){
						if(strtotime($date_interest) >= strtotime($value2['start_date']) && strtotime($date_interest) <= strtotime($value2['end_date'])){
							$interest_rate = $value2['interest_rate'];
							break;
						}
					}
					
					$day_of_year = date("z", strtotime(substr($date_interest, 0, 4)."-12-31")) + 1;
					$interest = round(((($row_due["transaction_deposit"]*@$interest_rate)/100)*$row_date["date_count"])/$day_of_year, 2);
					$sum = $row_due["transaction_deposit"] + $interest;
					
					array_push($transaction["due"], array(
						"deposit" => $interest,
						"interest" => $interest
					));
				}
			}
			
			return $transaction;
		}
	}
	
	function cal_deposit_interest_by_acc_date($account_id, $date_interest){
		$this->db->select(array(
			'account_id',
			'type_id',
			'mem_id',
			'created as create_account_date'
		));
		$this->db->from("coop_maco_account");
		$this->db->where("account_id = '".$account_id."'");
		$row = $this->db->get()->row_array();
		$data = $this->cal_deposit_interest($row, "return_interest", $date_interest);
		
		return $data;
	}
	
	function cal_deposit_interest_by_account($account_id, $cal_type='test'){
		$date_interest = date('Y-m-d');
		
		$this->db->select(array(
			't1.type_id',
		));
		$this->db->from('coop_maco_account as t1');
		$this->db->where("t1.account_id= '".$account_id."'");
		$row = $this->db->get()->result_array();
		$account_data = @$row[0];
		$type_id = $account_data['type_id'];
		
		$this->db->select(array(
			't1.transaction_time',
		));
		$this->db->from("coop_account_transaction{$this->forecast} as t1");
		$this->db->where("t1.account_id= '".$account_id."' AND (t1.transaction_list = 'IN' OR t1.transaction_list = 'INT') ");
		$this->db->order_by('t1.transaction_time DESC');
		$this->db->limit(1);
		$rs_last_interest = $this->db->get()->result_array();
		$row_last_interest = @$rs_last_interest[0];
		$where = '';
		if(@$row_last_interest['transaction_time']!=''){
			$where .= " AND t1.transaction_time >= '".$row_last_interest['transaction_time']."' ";
		}
		
		$this->db->select(array('*'));
		$this->db->from('coop_deposit_type_setting_detail');
		$this->db->where("type_id = '".$type_id."' AND start_date <= '".$date_interest."'");
		$this->db->order_by("start_date DESC");
		$this->db->limit(1);
		$row_detail = $this->db->get()->result_array();
		$row_detail = @$row_detail[0];
		if(empty($row_detail)){return false;}
		if($row_detail['type_fee'] == '3'){//ปรเภทการฝากที่ต้องครบ 24 เดือน
			if($row_detail['condition_interest']=='2'){ //ประเภทเงินฝากที่คิดดอกเบี้ยตามวันที่ฝาก
				$this->db->select(array('transaction_id','deposit_balance','transaction_time'));
				$this->db->from("coop_account_transaction{$this->forecast}");
				$this->db->where("
					account_id = '".$account_id."' 
					AND fixed_deposit_type='principal' 
					AND date_end_saving > '".$date_interest."'
				");
				$row = $this->db->get()->result_array();
				$return_to_coop = 0;
				if(!empty($row)){
					foreach($row as $key => $value){
						$interest_rate = $row_detail['percent_depositor'];
						$date_start = date('Y-m-d',strtotime($value['transaction_time']));
						$date_end = $date_interest;
						$diff = @date_diff(date_create($date_start),date_create($date_end));
						$date_count = @$diff->format("%a");
						$date_count = $date_count;
						
						$interest = ((($value['deposit_balance']*@$interest_rate)/100)*$date_count)/365;
						
						
							$this->db->select(array('deposit_interest_balance'));
							$this->db->from("coop_account_transaction{$this->forecast}");
							$this->db->where("ref_transaction_id = '".$value['transaction_id']."' AND fixed_deposit_type = 'interest'");
							$this->db->order_by('interest_period DESC');
							$this->db->limit(1);
							$row_last_interest = $this->db->get()->result_array();
							$received_interest = $row_last_interest[0]['deposit_interest_balance'] - $value['deposit_balance'];
							//echo $received_interest;exit;
							if($received_interest > $interest){
								$return_to_coop += $received_interest - $interest;
							}
							//echo $return_to_coop;exit;
							//เอา $return_to_coop ไปลงบัญชี เพื่อคืนเงินให้สหกรณ์ เมื่อระบบบัญชีเสร็จ
						
						@$interest_all += $interest;
					}
				}
			}else{
				$this->db->select(array('created'));
				$this->db->from('coop_maco_account');
				$this->db->where("account_id = '".$account_id."'");
				$row_account = $this->db->get()->result_array();
				$row_account = $row_account[0];
				$create_date = date('Y-m-d',strtotime($row_account['created']));
				$end_date = date('Y-m-d',strtotime('+ '.$row_detail['num_month_before'].' month',strtotime($create_date)));
				if($date_interest < $end_date){
					$this->db->select(array('transaction_balance','transaction_no_in_balance'));
					$this->db->from("coop_account_transaction{$this->forecast}");
					$this->db->where("account_id = '".$account_id."'");
					$this->db->order_by('transaction_time DESC, transaction_id DESC');
					$this->db->limit(1);
					$row_transaction = $this->db->get()->result_array();
					
					$interest_rate = $row_detail['percent_depositor'];
					$date_start = $create_date;
					$date_end = $date_interest;
					$diff = @date_diff(date_create($date_start),date_create($date_end));
					$date_count = @$diff->format("%a");
					$date_count = $date_count;
					
					$interest = ((($row_transaction[0]['transaction_no_in_balance']*@$interest_rate)/100)*$date_count)/365;
						
					$received_interest = $row_transaction[0]['transaction_balance'] - $row_transaction[0]['transaction_no_in_balance'];
					if($received_interest > $interest){
						$return_to_coop = $received_interest - $interest;
					}
					//เอา $return_to_coop ไปลงบัญชี เพื่อคืนเงินให้สหกรณ์ เมื่อระบบบัญชีเสร็จ
					
					@$interest_all = $interest;
				}
			}
		}else{
			$this->db->select(array(
				't1.transaction_id',
				't1.transaction_balance', 
				't1.transaction_no_in_balance', 
				't1.transaction_time',
				't1.account_id',
				't1.transaction_list',
				't2.type_id',
				't2.created'
			));
			$this->db->from("coop_account_transaction{$this->forecast} as t1");
			$this->db->join('coop_maco_account as t2','t1.account_id = t2.account_id','inner');
			$this->db->where("t2.account_id = '".$account_id."' {$where}");
			$this->db->order_by('t1.transaction_time ASC');
			$row_transaction = $this->db->get()->result_array();
		//echo $this->db->last_query();
			$transaction = array();
			foreach($row_transaction as $key => $value){
				$transaction[$key]['type_id'] = $value['type_id'];
				$transaction[$key]['create_account_date'] = $value['created'];
				$transaction[$key]['date_begin'] = date('Y-m-01',strtotime($value['created']));
				$transaction[$key]['transaction_list'] = $value['transaction_list'];
				$transaction[$key]['transaction_id'] = $value['transaction_id'];
				$transaction[$key]['date_start'] = date('Y-m-d',strtotime($value['transaction_time']));
				$transaction[$key]['transaction_balance'] = $value['transaction_balance'];
				$transaction[$key]['transaction_no_in_balance'] = $value['transaction_no_in_balance'];
				$transaction[$key]['account_id'] = $value['account_id'];
				if(@$row_transaction[($key+1)]['transaction_time'] != '' && @$row_transaction[($key+1)]['account_id'] == $value['account_id']){
					$transaction[$key]['date_end'] = date('Y-m-d',strtotime($row_transaction[($key+1)]['transaction_time']));
				}else{
					$transaction[$key]['date_end'] = $date_interest;
				}
				$diff = @date_diff(date_create($transaction[$key]['date_start']),date_create($transaction[$key]['date_end']));
				$date_count = @$diff->format("%a");
				$date_count = $date_count;
				$transaction[$key]['date_count'] = $date_count;
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
				$this->db->where("t1.type_id = '".@$type_id."'");
				$this->db->order_by("start_date ASC");
				$row_interest_rate = $this->db->get()->result_array();
				//echo $this->db->last_query();
				
				foreach($row_interest_rate as $key2 => $value2){
					if(@$row_interest_rate[($key2+1)]['start_date'] != ''){
						$row_interest_rate[$key2]['end_date'] = date('Y-m-d',strtotime($row_interest_rate[($key2+1)]['start_date']));
					}else{
						$row_interest_rate[$key2]['end_date'] = $date_interest;
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
								$transaction_new[$i]['interest_rate'] = @$row_interest_rate[($key2+1)]['interest_rate'];
							}
						}
					}
					$i++;
				}
				$transaction = $transaction_new;
				
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
				$this->db->where("t1.type_id = '".@$type_id."' AND t2.percent_interest IS NOT NULL AND t2.amount_deposit IS NOT NULL");
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
				$date_count = $date_count;
				$transaction[$key]['date_count'] = $date_count;
				
				// ฝากไม่ถึง  x เดือน ไม่คิดดอกเบี้ย คิดเป็น วัน
				$num_month_no_interest = $row_detail['num_month_no_interest']*30;
				if($row_detail['type_interest'] == '3' &&  $date_count < $num_month_no_interest){
					//คิดดอกเบี้ย ไม่คิดดอกเบี้ย เมื่อ ฝากไม่ถึง  x เดือน ไม่คิดดอกเบี้ย
					$interest = 0;
				}else if($row_detail['type_interest'] == '2'){
					//คิดดอกเบี้ย ดอกเบี้ยเฉพาะเงินต้น
					$interest = ((($value['transaction_no_in_balance']*@$interest_rate)/100)*$date_count)/365;
					//echo $value['account_id'].'|'.$value['transaction_no_in_balance'].'|'.$interest .'<hr>';
				}else{					
					//คิดดอกเบี้ย ดอกเบี้ยทบต้น
					$interest = ((($value['transaction_balance']*@$interest_rate)/100)*$date_count)/365;
				}
				
				$transaction[$key]['interest'] = number_format($interest,2,'.','');
				
				
			}
			$transaction_tmp = array();
			foreach($transaction as $key => $value){
				$transaction_tmp[$value['date_start']] = $value;
			}
			$transaction = $transaction_tmp;
			
			$interest_all = 0;
			$account_id = '';
			foreach($transaction as $key => $value){
				$account_id = $value['account_id'];
				@$interest_all += $value['interest'];
			}
		}
		//echo $return_to_coop;exit;
		if($cal_type == 'real'){
			if(@$account_id != '' && @$interest_all > 0){
				$interest = number_format($interest_all,2,'.','');
				$return_to_coop = number_format($return_to_coop,2,'.','');
				$this->db->select(array(
					'transaction_balance',
					'transaction_no_in_balance'
				));
				$this->db->from("coop_account_transaction{$this->forecast} as t1");
				$this->db->where("account_id = '".$account_id."'");
				$this->db->order_by('transaction_time DESC');
				$this->db->limit(1);
				$row_balance = $this->db->get()->result_array(); 
				$row_balance = @$row_balance[0];
				$balance = $row_balance["transaction_balance"];
				$balance_no_interest = $row_balance["transaction_no_in_balance"];
				if($return_to_coop > 0){
					$balance = $balance - $return_to_coop;
					$data_insert = array();
					$data_insert['transaction_time'] = $date_interest." ".date('H:i:s');
					$data_insert['transaction_list'] = 'RE/COOP';
					$data_insert['transaction_withdrawal'] = $return_to_coop;
					$data_insert['transaction_deposit'] = '0';
					$data_insert['transaction_balance'] = $balance;
					$data_insert['transaction_no_in_balance'] = $balance_no_interest;
					$data_insert['user_id'] = $this->user_id;
					$data_insert['account_id'] = $account_id;
					$this->db->insert("coop_account_transaction{$this->forecast}", $data_insert);  
				}else{
					if($interest > 0){
						$sum = $balance + $interest;
						
						$data_insert = array();
						$data_insert['transaction_time'] = $date_interest." ".date('H:i:s');
						$data_insert['transaction_list'] = 'IN';
						$data_insert['transaction_withdrawal'] = '';
						$data_insert['transaction_deposit'] = $interest;
						$data_insert['transaction_balance'] = number_format($sum,2,'.','');
						$data_insert['transaction_no_in_balance'] = $balance_no_interest;
						$data_insert['user_id'] = $this->user_id;
						$data_insert['account_id'] = $account_id;
						$this->db->insert("coop_account_transaction{$this->forecast}", $data_insert);
					}
				}
			}	
		}else if($cal_type == 'return'){
			//echo $account_id."<br>";
			return @$interest_all;
			//echo"<pre>";print_r($transaction);echo"</pre>";
		}else{
			echo $account_id."<br>";
			echo $return_to_coop."<br>";
			echo @$interest_all."<br>";
			echo"<pre>";print_r($transaction);echo"</pre>";
		}
	}
	
	function is_holiday($date) {
		return $this->_is_holiday($date);
	}
	
	function cal_deposit_interest_forecast($date_interest, $account_id = "", $type_id = ""){
		$date_now = date("Y-m-d H:i:s");
		
		if(!empty($account_id)) {
			$this->db->delete("coop_account_transaction_forecast", array("account_id" => $account_id));
			$this->db->query("INSERT INTO coop_account_transaction_forecast SELECT * FROM coop_account_transaction WHERE account_id = '{$account_id}'");
			
			$this->db->select("type_id");
			$this->db->from("coop_maco_account");
			$this->db->where("account_id = '{$account_id}'");
			$_row = $this->db->get()->row_array();
			
			$where = " AND account_id = '{$account_id}'";
			$where_type = "type_id = '{$_row["type_id"]}'";
		}
		elseif(!empty($type_id)) {
			$this->db->query("DELETE coop_account_transaction_forecast
											FROM coop_account_transaction_forecast
												INNER JOIN coop_maco_account ON coop_account_transaction_forecast.account_id = coop_maco_account.account_id
											WHERE type_id = '{$type_id}'");
			
			$this->db->query("INSERT INTO coop_account_transaction_forecast
											SELECT coop_account_transaction.*
											FROM coop_account_transaction
												INNER JOIN coop_maco_account ON coop_account_transaction.account_id = coop_maco_account.account_id
											WHERE type_id = '{$type_id}'");
			
			$where = " AND account_status = '0'";
			$where_type = "type_id = '{$type_id}'";
		}
		else {
			$this->db->truncate("coop_account_transaction_forecast");
			$this->db->query("INSERT INTO coop_account_transaction_forecast SELECT * FROM coop_account_transaction");
			
			$where = " AND account_status = '0'";
			$where_type = "1 = 1";
		}
		
		$this->db->select("*");
		$this->db->from("coop_deposit_type_setting");
		$this->db->where($where_type);
		$_rs = $this->db->get()->result_array();
		foreach($_rs as $_row) {
			$this->db->select("*");
			$this->db->from("coop_deposit_type_setting_detail");
			$this->db->where("type_id = '{$_row["type_id"]}'");
			$this->db->order_by("start_date", "DESC");
			$this->db->limit(1);
			$_row_detail = $this->db->get()->row_array();
			
			$this->db->select(array(
				'account_id',
				'type_id',
				'mem_id',
				'created as create_account_date'
			));
			$this->db->from('coop_maco_account');
			$this->db->where("type_id = '{$_row["type_id"]}'".$where);
			$rs_member = $this->db->get()->result_array();
			
			if($_row_detail["pay_interest"] == 1 && $_row_detail["condition_age"] == 0) {
				// จ่ายทุกสิ้นเดือน
				$rs_date = $this->db->query("SELECT TIMESTAMPDIFF(MONTH, '{$date_now}', '{$date_interest}') AS month_count");
				$row_date = $rs_date->row_array();
				for($i = 0; $i < $row_date["month_count"] + 1; $i++) {
					$rs_date2 = $this->db->query("SELECT DATE_ADD('".$date_now."', INTERVAL ".$i." MONTH) AS date_cal");
					$row_date2 = $rs_date2->row_array();
					
					if(date("Y-m-t", strtotime($row_date2["date_cal"])) != date("Y-m-d", strtotime($date_now))) {
						foreach($rs_member as $key_member => $row_member){
							$this->user_id = 'SYSTEM';
							//$this->debug = true;
							//$this->testmode = true;
							$this->forecast = "_forecast";
							$this->cal_deposit_interest($row_member, 'cal_interest', date("Y-m-t", strtotime($row_date2["date_cal"])), date("t", strtotime($row_date2["date_cal"])), '');
						}
					}
				}
			}
			elseif($_row_detail["pay_interest"] == 2 || $_row_detail["pay_interest"] == 4 || in_array($_row_detail["condition_age"], [1, 2 ,3])) {
				// จ่ายทุกเดือน ตามวันที่ครบกำหนด || จ่ายเมื่อครบกำหนด x เดือน
				$rs_date = $this->db->query("SELECT DATEDIFF('{$date_interest}', '{$date_now}') AS date_count");
				$row_date = $rs_date->row_array();
				for($i = 1; $i <= $row_date["date_count"]; $i++) {
					$rs_date2 = $this->db->query("SELECT DATE_ADD('".$date_now."', INTERVAL ".$i." DAY) AS date_cal");
					$row_date2 = $rs_date2->row_array();
					
					foreach($rs_member as $key_member => $row_member){
						$this->user_id = 'SYSTEM';
						//$this->debug = true;
						//$this->testmode = true;
						$this->forecast = "_forecast";
						$this->cal_deposit_interest($row_member, 'cal_interest', $row_date2["date_cal"], date("d", strtotime($row_date2["date_cal"])), '');
					}
				}
			}
			elseif($_row_detail["pay_interest"] == 3 && $_row_detail["condition_age"] == 0) {
				// จ่าย 2 ครั้ง ต่อปี
				$cal_y = substr($date_interest, 0, 4);
				$pay_date1 = substr($date_interest, 0, 4)."-".substr($_row_detail['pay_date1'], 5, 5);
				$pay_date2 = substr($date_interest, 0, 4)."-".substr($_row_detail['pay_date2'], 5, 5);
				
				if(strtotime($date_interest) < strtotime($pay_date1) && strtotime($date_interest) < strtotime($pay_date2)) {
					$date_cal = ($cal_y - 1)."-".substr($_row_detail['pay_date2'], 5, 5);
				}
				elseif(strtotime($date_interest) > strtotime($pay_date1) && strtotime($date_interest) > strtotime($pay_date2)) {
					$date_cal = $cal_y."-".substr($_row_detail['pay_date2'], 5, 5);
				}
				else {
					$date_cal = $cal_y."-".substr($_row_detail['pay_date1'], 5, 5);
				}
				echo $date_cal." ".$date_now."<br>";
				if(strtotime($date_cal) > strtotime(date("Y-m-d", strtotime($date_now)))) {
					foreach($rs_member as $key_member => $row_member){
						$this->user_id = 'SYSTEM';
						//$this->debug = true;
						//$this->testmode = true;
						$this->forecast = "_forecast";
						$this->cal_deposit_interest($row_member, 'cal_interest', $date_cal, date("d", strtotime($date_cal)), '');
					}
				}
			}
		}
	}
	
	function round_down($value, $precision) {       
		$value = (float)$value;
		$precision = (int)$precision;
		if ($precision < 0) {
			$precision = 0;
		}
		$decPointPosition = strpos($value, '.');
		if ($decPointPosition === false) {
			return $value;
		}
		return (float)(substr($value, 0, $decPointPosition + $precision + 1));       
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
			't1.days_in_year',
			't2.num_month',
			't2.percent_interest as interest_rate'
		));
		$this->db->from('coop_deposit_type_setting_detail as t1');
		$this->db->join('coop_deposit_type_setting_interest as t2','t1.type_detail_id = t2.type_detail_id AND t1.condition_interest = t2.condition_interest','inner');
		$this->db->where("t1.type_id = '".@$type_id."' AND start_date <= '".$date_last_due."'");
		$this->db->order_by("start_date DESC");
		$this->db->limit(1);
		
		$arr_data = $this->db->get()->row_array();
		return $arr_data;
	}

	function check_interest_two_step($date_start,$date_end, $type_id, $checkList = array()){		
		$arr_data = array();
		$src_year = date("z", strtotime(substr($date_start, 0, 4)."-12-31")) + 1;
		$des_year = date("z", strtotime(substr($date_end, 0, 4)."-12-31")) + 1;
		//เซ็ตวันที่ในการหาดอกเบี้ย ด้วยเว้นวันที่คิดดอกเบี้ยไปแล้ว
		if(in_array($checkList, array('IN'))){
			$_date_start = date("Y-m-d", strtotime($date_start . " +1 DAY" ));
		}else{
			$_date_start = date("Y-m-d", strtotime($date_start));
		}
		
		if($src_year == $des_year){
			$date_middle = date("Y-m-d", strtotime(date("Y", strtotime($date_start)).'-12-31' . " +1 DAY" ));
			if(strtotime($date_end) >= strtotime($date_middle)){
				$date_end_2 = $date_middle;
			}else{
				$date_end_2 = $date_end;
			}

			$diff = @date_diff(date_create($date_start),date_create($date_end_2));
			$date_count = @$diff->format("%a");
			$arr_data[0]['date_count'] = $date_count;
			$arr_data[0]['day_of_year'] = $src_year;
			if($this->is_condition_step($date_start, $date_end, $type_id)){
				$this->db->where("type_id", $type_id);
				$this->db->where("start_date <=", $_date_start);
				$this->db->order_by("start_date desc");
				$this->db->limit(1);
				$type_detail_id = $this->db->get_where("coop_deposit_type_setting_detail")->row_array()['type_detail_id'];
				$this->db->select(array(
					't1.type_detail_id',
					't1.type_id',
					't1.start_date',
					't1.days_in_year',
					't2.num_month',
					't2.percent_interest as interest_rate',
					't2.amount_deposit'
				));
				$this->db->from('coop_deposit_type_setting_detail as t1');
				$this->db->join('coop_deposit_type_setting_interest as t2','t1.type_detail_id = t2.type_detail_id AND t1.condition_interest = t2.condition_interest','inner');
				$this->db->where("t1.type_id = '".$type_id."' AND t1.type_detail_id = '".$type_detail_id."'");
				$this->db->order_by("start_date DESC");
				$arr_data[0]['interest_rate'] =  $this->db->get()->result_array();
				$arr_data[0]['day_of_year'] = $this->get_day_of_year_by_type($arr_data[0]['interest_rate'][0]['days_in_year'], $date_start);
			}else{
				$arr_data[0]['interest_rate'] = self::check_interest_rate($type_id, '', $date_start)['interest_rate'];
				$arr_data[0]['day_of_year'] = $this->get_day_of_year_by_type($arr_data[0]['interest_rate'][0]['days_in_year'], $date_start);
			}
			
			//step 2 กรณีมีวันที่ใช้คำนวณดอกเบี้ยข้ามปี
			if(strtotime($date_end) >= strtotime($date_middle)){
				$diff_two = @date_diff(date_create($date_middle),date_create($date_end));
				$date_count_two  = @$diff_two->format("%a");
				$arr_data[1]['date_count'] = $date_count_two;
				$arr_data[1]['day_of_year'] = $des_year;
				if($this->is_condition_step($date_middle, $date_end, $type_id)){
					$this->db->where("type_id", $type_id);
					$this->db->where("start_date <=", $date_middle);
					$this->db->order_by("start_date desc");
					$this->db->limit(1);
					$type_detail_id = $this->db->get_where("coop_deposit_type_setting_detail")->row_array()['type_detail_id'];
					$this->db->select(array(
						't1.type_detail_id',
						't1.type_id',
						't1.start_date',
						't1.days_in_year',
						't2.num_month',
						't2.percent_interest as interest_rate',
						't2.amount_deposit'
					));
					$this->db->from('coop_deposit_type_setting_detail as t1');
					$this->db->join('coop_deposit_type_setting_interest as t2','t1.type_detail_id = t2.type_detail_id AND t1.condition_interest = t2.condition_interest','inner');
					$this->db->where("t1.type_id = '".$type_id."' AND t1.type_detail_id = '".$type_detail_id."'");
					$this->db->order_by("start_date DESC");
					$arr_data[1]['interest_rate'] =  $this->db->get()->result_array();
					$arr_data[1]['day_of_year'] = $this->get_day_of_year_by_type($arr_data[0]['interest_rate'][0]['days_in_year'], $date_end);
				}else{
					$arr_data[1]['interest_rate'] = self::check_interest_rate($type_id, '', $date_middle)['interest_rate'];
					$arr_data[1]['day_of_year'] = $this->get_day_of_year_by_type($arr_data[0]['interest_rate'][0]['days_in_year'], $date_end);
				}
			}
		}else{
			
			/*$date_middle = date("Y", strtotime($date_end)).'-01-01';
			$diff_one = @date_diff(date_create($date_start),date_create($date_middle));
			$date_count_one  = @$diff_one->format("%a");
			$arr_data[0]['date_count'] = $date_count_one;
			$arr_data[0]['day_of_year'] = $src_year;
			if($this->is_condition_step($date_start, $date_end, $type_id)){
				$this->db->where("type_id", $type_id);
				$this->db->where("start_date <=", $_date_start);
				$this->db->order_by("start_date desc");
				$this->db->limit(1);
				$type_detail_id = $this->db->get_where("coop_deposit_type_setting_detail")->row_array()['type_detail_id'];
				$this->db->select(array(
					't1.type_detail_id',
					't1.type_id',
					't1.start_date',
					't1.days_in_year',
					't2.num_month',
					't2.percent_interest as interest_rate',
					't2.amount_deposit'
				));
				$this->db->from('coop_deposit_type_setting_detail as t1');
				$this->db->join('coop_deposit_type_setting_interest as t2','t1.type_detail_id = t2.type_detail_id AND t1.condition_interest = t2.condition_interest','inner');
				$this->db->where("t1.type_id = '".$type_id."' AND t1.type_detail_id = '".$type_detail_id."'");
				$this->db->order_by("start_date DESC");
				$arr_data[0]['interest_rate'] =  $this->db->get()->result_array();
				$arr_data[0]['day_of_year'] = $this->get_day_of_year_by_type($arr_data[0]['interest_rate'][0]['days_in_year'], $date_start);
			}else{
				$arr_data[0]['interest_rate'] = self::check_interest_rate($type_id, '', $date_start)['interest_rate'];
				$arr_data[0]['day_of_year'] = $this->get_day_of_year_by_type($arr_data[0]['interest_rate'][0]['days_in_year'], $date_start);
			}

			$diff_two = @date_diff(date_create($date_middle),date_create($date_end));
			$date_count_two  = @$diff_two->format("%a");
			$arr_data[1]['date_count'] = $date_count_two;
			$arr_data[1]['day_of_year'] = $des_year;
			if($this->is_condition_step($date_middle, $date_end, $type_id)){
				$this->db->where("type_id", $type_id);
				$this->db->where("start_date <=", $date_middle);
				$this->db->order_by("start_date desc");
				$this->db->limit(1);
				$type_detail_id = $this->db->get_where("coop_deposit_type_setting_detail")->row_array()['type_detail_id'];
				$this->db->select(array(
					't1.type_detail_id',
					't1.type_id',
					't1.start_date',
					't1.days_in_year',
					't2.num_month',
					't2.percent_interest as interest_rate',
					't2.amount_deposit'
				));
				$this->db->from('coop_deposit_type_setting_detail as t1');
				$this->db->join('coop_deposit_type_setting_interest as t2','t1.type_detail_id = t2.type_detail_id AND t1.condition_interest = t2.condition_interest','inner');
				$this->db->where("t1.type_id = '".$type_id."' AND t1.type_detail_id = '".$type_detail_id."'");
				$this->db->order_by("start_date DESC");
				$arr_data[1]['interest_rate'] =  $this->db->get()->result_array();
				$arr_data[1]['day_of_year'] = $this->get_day_of_year_by_type($arr_data[0]['interest_rate'][0]['days_in_year'], $date_end);
			}else{
				$arr_data[1]['interest_rate'] = self::check_interest_rate($type_id, '', $date_middle)['interest_rate'];
				$arr_data[1]['day_of_year'] = $this->get_day_of_year_by_type($arr_data[0]['interest_rate'][0]['days_in_year'], $date_end);
			}
			*/
			$date_middle = date("Y", strtotime($date_end)).'-01-01';
			$check_year = $this->check_year($date_start,$date_end);
			if($check_year == 1){
				$arr_one_step = $this->cal_int_one_step($date_start,$date_end,$date_middle,$type_id,$_date_start,$src_year);
				$arr_two_step = $this->cal_int_two_step($date_end,$date_middle,$type_id,$des_year);				
				$arr_data = array_merge($arr_one_step,$arr_two_step);								
			}else{
				$arr_data = $this->cal_int_one_step($date_start,$date_end,$date_middle,$type_id,$_date_start,$src_year);
			}
		}

		return $arr_data;
	}
	
	//เช็ควันคำนวณดอกเบี้ยข้ามปี
	function check_year($date_start,$date_end){
		$year_s = date("Y", strtotime($date_start));
		$year_e = date("Y", strtotime($date_end));
		if($year_s == $year_e){
			//ไม่ข้ามปี
			$check = false;
		}else{
			//ข้ามปี
			$check = true;
		}
		return  $check;
	}
	
	//คำนวณดอกเบี้ย 1 step
	function cal_int_one_step($date_start,$date_end,$date_middle,$type_id,$_date_start,$src_year){
		$arr_data = array();
		$diff_one = @date_diff(date_create($date_start),date_create($date_middle));
		$date_count_one  = @$diff_one->format("%a");
		$arr_data[0]['date_count'] = $date_count_one;
		$arr_data[0]['day_of_year'] = $src_year;
		if($this->is_condition_step($date_start, $date_end, $type_id)){
			$this->db->where("type_id", $type_id);
			$this->db->where("start_date <=", $_date_start);
			$this->db->order_by("start_date desc");
			$this->db->limit(1);
			$type_detail_id = $this->db->get_where("coop_deposit_type_setting_detail")->row_array()['type_detail_id'];
			$this->db->select(array(
				't1.type_detail_id',
				't1.type_id',
				't1.start_date',
				't1.days_in_year',
				't2.num_month',
				't2.percent_interest as interest_rate',
				't2.amount_deposit'
			));
			$this->db->from('coop_deposit_type_setting_detail as t1');
			$this->db->join('coop_deposit_type_setting_interest as t2','t1.type_detail_id = t2.type_detail_id AND t1.condition_interest = t2.condition_interest','inner');
			$this->db->where("t1.type_id = '".$type_id."' AND t1.type_detail_id = '".$type_detail_id."'");
			$this->db->order_by("start_date DESC");
			$arr_data[0]['interest_rate'] =  $this->db->get()->result_array();
			$arr_data[0]['day_of_year'] = $this->get_day_of_year_by_type($arr_data[0]['interest_rate'][0]['days_in_year'], $date_start);
		}else{
			$arr_data[0]['interest_rate'] = self::check_interest_rate($type_id, '', $date_start)['interest_rate'];
			$arr_data[0]['day_of_year'] = $this->get_day_of_year_by_type($arr_data[0]['interest_rate'][0]['days_in_year'], $date_start);
		}
		return  $arr_data;
	}	
	
	//คำนวณดอกเบี้ย 2 step
	function cal_int_two_step($date_end,$date_middle,$type_id,$des_year){
		$arr_data = array();
		$diff_two = @date_diff(date_create($date_middle),date_create($date_end));
		$date_count_two  = @$diff_two->format("%a");
		$arr_data[1]['date_count'] = $date_count_two;
		$arr_data[1]['day_of_year'] = $des_year;
		if($this->is_condition_step($date_middle, $date_end, $type_id)){
			$this->db->where("type_id", $type_id);
			$this->db->where("start_date <=", $date_middle);
			$this->db->order_by("start_date desc");
			$this->db->limit(1);
			$type_detail_id = $this->db->get_where("coop_deposit_type_setting_detail")->row_array()['type_detail_id'];
			$this->db->select(array(
				't1.type_detail_id',
				't1.type_id',
				't1.start_date',
				't1.days_in_year',
				't2.num_month',
				't2.percent_interest as interest_rate',
				't2.amount_deposit'
			));
			$this->db->from('coop_deposit_type_setting_detail as t1');
			$this->db->join('coop_deposit_type_setting_interest as t2','t1.type_detail_id = t2.type_detail_id AND t1.condition_interest = t2.condition_interest','inner');
			$this->db->where("t1.type_id = '".$type_id."' AND t1.type_detail_id = '".$type_detail_id."'");
			$this->db->order_by("start_date DESC");
			$row = $this->db->get()->result_array();
			$arr_data[1]['interest_rate'] =  $row;
			$arr_data[1]['day_of_year'] = $this->get_day_of_year_by_type($row[0]['days_in_year'], $date_end);
		}else{
			$arr_data[1]['interest_rate'] = self::check_interest_rate($type_id, '', $date_middle)['interest_rate'];
			$arr_data[1]['day_of_year'] = $this->get_day_of_year_by_type($arr_data[0]['interest_rate'][0]['days_in_year'], $date_end);
		}
		return  $arr_data;
	}

	/*
		1=365
		2=366
		3=depend of calendar
	*/
	function get_day_of_year_by_type($type, $date) {
		if($type == 2) {
			return 366;
		} else if ($type == 3) {
			return date("z", strtotime(substr($date, 0, 4)."-12-31")) + 1;
		} else {
			return 365;
		}
	}

	/*
	coop_deposit_type_setting_interest.condition_interest = 3
	*/
	function is_condition_step($date_start,$date_end, $type_id){
		$row = $this->db->get_where("coop_deposit_type_setting_detail", array(
			"type_id" => $type_id,
			"start_date <=" => $date_start
		))->row_array();
		return (!empty($row)) ? true : false;
	}

	function check_fixed_deposit_list($account_id, $date_start, $date_end, $ref_id){
		$list  = array( 'OPN', 'DCA', 'DFX', 'WCA');

		$account = $this->db->get_where('coop_maco_account', array('account_id' => $account_id))->row_array();

		$transaction = $this->db->select('*')->from("coop_account_transaction")->where(array(
			'account_id' => $account_id,
			'transaction_time >= ' => $date_start,
			'transaction_time <= ' => $date_end,
			'ref_account_no' => $ref_id
		))->where_in('transaction_list' , $list)
		->order_by('transaction_time', 'asc')->get()->result_array();

		if(sizeof($transaction)){

			$date_target = "";
			$arr_data = array();
			$list_deposit  = array( 'OPN', 'DCA', 'DFX');
			for($i = 0; $i < sizeof($transaction); $i++){

				if(in_array($transaction[$i]['transaction_list'], $list_deposit)){
					$date_target = date('Y-m-d H:i:s', strtotime($transaction[$i]['transaction_time']." + 12 month"));
				}

				$date_start = $transaction[$i]['transaction_time'];
				$date_end = $i+1 == sizeof($transaction) ? $date_target : $transaction[$i+1]['transaction_time'] ;
				$type_id = $account['type_id'];
				$transaction_balance = $transaction[$i]['balance_deposit'];

				$result = self::check_interest_two_step($date_start, $date_end, $type_id);

				foreach ($result as $index => $value){
					$value['transaction_balance'] = $transaction_balance;
					$arr_data[] = $value;
				}

			}
			return $arr_data;
		}else{
			return array();
		}

	}
	
	function get_account_name($account_id){
		$account_name = $this->db->get_where("coop_maco_account", array("account_id" => $account_id))->row()->account_name;
		return $account_name;
	}
	
	function check_interest_step_year($date_start,$date_end){
		$arr_data = array();
		$src_year = date("z", strtotime(substr($date_start, 0, 4)."-12-31")) + 1;
		$des_year = date("z", strtotime(substr($date_end, 0, 4)."-12-31")) + 1;

		$_date_start = date("Y-m-d", strtotime($date_start));
		if($date_start <= $date_end){
			if($src_year == $des_year){
				$arr_data[0]['date_start'] = $date_start;
				$arr_data[0]['date_end'] = $date_end;			
			}else{
				$date_middle = date("Y", strtotime($date_end)).'-01-01';

				$arr_data[0]['date_start'] = $date_start;
				$arr_data[0]['date_end'] = $date_middle;
				
				$arr_data[1]['date_start'] = $date_middle;
				$arr_data[1]['date_end'] = $date_end;
			}
		}
		return $arr_data;
	}
	
	//หาวันที่เปิดบัญชี 
	public function get_date_open_account($account_id){
		$date_open = '';
		$row= $this->db->select("DATE(transaction_time) AS transaction_time")
						->from('coop_account_transaction')
						->where("account_id = '{$account_id}' AND transaction_list IN ('OPNX','OPN')")
						->limit(1)
						->get()->row_array()['transaction_time'];
		//echo $this->db->last_query();
		if(!empty($row)){
			$date_open = $row;
		}else{
			$date_open = $this->get_date_first_account($account_id);
		}
		return $date_open;
	}
	
	//หาวันที่เริ่มบัญชีรายการแรก
	public function get_date_first_account($account_id){
		$date_first = '';
		$row = $this->db->select("DATE(transaction_time) AS transaction_time")
						->from('coop_account_transaction')
						->where("account_id = '{$account_id}'")
						->order_by("transaction_time ASC,transaction_id ASC")
						->limit(1)
						->get()->row_array()['transaction_time'];
		//echo $this->db->last_query();
		if(!empty($row)){
			$date_first = $row;
		}
		return $date_first;
	}
	
	//หาวันที่หลังจากฝากเงินครั้งล่าสุดครบ เมื่อครบกำหนด x เดือน
	public function get_date_last_account($account_id,$num_month_maturity,$ext_num_month_maturity_day){
		//echo 'ext_num_month_maturity_day='.$ext_num_month_maturity_day.'<br>';
		$date_open = $this->get_date_open_account($account_id);
		$date_last = '';
		if($date_open != ''){				
			/*$transaction_time = $this->db->select("DATE(transaction_time) AS transaction_time")
							->from('coop_account_transaction')
							->where("fixed_deposit_status <> '1' AND account_id = '{$account_id}' 
								AND transaction_list IN ( 'OPN', 'OCA', 'OPT', 'TRB', 'XD', 'DEP', 'DCA', 'DFX', 'DEPP', 'CD', 'DOT' ) 
								AND transaction_time >= DATE_ADD( '{$date_open}', INTERVAL + (24-1) MONTH ) ")
							->order_by("transaction_time ASC,transaction_id ASC")
							->limit(1)
							->get()->row_array()['transaction_time'];	
			*/				

			$transaction_time= $this->db->select("transaction_time, transaction_id")
						->from('coop_account_transaction')
						->where("account_id = '{$account_id}' 
							AND transaction_list IN ('OPN', 'OCA', 'OPT', 'TRB', 'XD', 'DEP', 'DCA', 'DFX', 'DEPP', 'CD','DOT')
							AND (PERIOD_DIFF( DATE_FORMAT(transaction_time, '%Y%m'), DATE_FORMAT('{$date_open}', '%Y%m')  )+1) = {$num_month_maturity}")
						->order_by("transaction_time DESC,transaction_id DESC")
						->limit(1)
						->get()->row_array()['transaction_time'];				
			//echo $this->db->last_query(); exit;
		}
		if(!empty($transaction_time)){
			$date_last = date("Y-m-d", strtotime("-1 day", strtotime($transaction_time)));
			//echo 'date_last='.$date_last.'<br>'; 
		}
		return $date_last;
	}
}
