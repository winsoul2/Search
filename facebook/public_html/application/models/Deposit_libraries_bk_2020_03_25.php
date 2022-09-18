<?php if( ! defined('BASEPATH')) exit('No direct script access allowed');

class Deposit_libraries extends CI_Model {
	public $user_id;
	public $debug = false;
	public $testmode = false;
	public $forecast = "";
	
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
	
	private function _cal_interest($balance, $interest_rate, $date_start, $date_end) {
		// ดอกเบี้ยข้ามปี 365 366 วัน;
		$day_of_year_s = date("z", strtotime(substr($date_start, 0, 4)."-12-31")) + 1;
		$day_of_year_e = date("z", strtotime(substr($date_end, 0, 4)."-12-31")) + 1;
		
		if($day_of_year_s != $day_of_year_e) {
			$diff1 = @date_diff(date_create($date_start),date_create(substr($date_start, 0, 4)."-12-31"));
			$date_count1 = @$diff1->format("%a");
			$day_of_year1 = date("z", strtotime(substr($date_start, 0, 4)."-12-31")) + 1;
			
			$diff2 = @date_diff(date_create(substr($date_start, 0, 4)."-12-31"),date_create($date_end));
			$date_count2 = @$diff2->format("%a");
			$day_of_year2 = date("z", strtotime(substr($date_end, 0, 4)."-12-31")) + 1;
			
			$interest1 = ((($balance*$interest_rate)/100)*$date_count1)/$day_of_year1;
			//echo "t1=>(((".$balance."*".$interest_rate.")/100)*".$date_count1.")/".$day_of_year1."<br>";
			$interest2 = ((($balance*$interest_rate)/100)*$date_count2)/$day_of_year2;
			//echo "t2=>(((".$balance."*".$interest_rate.")/100)*".$date_count2.")/".$day_of_year2."<br>";
			$interest = $interest1 + $interest2;
		}
		else {
			$diff = @date_diff(date_create($date_start),date_create($date_end));
			$date_count = @$diff->format("%a");
			$day_of_year = date("z", strtotime(substr($date_start, 0, 4)."-12-31")) + 1;
			$interest = ((($balance*$interest_rate)/100)*$date_count)/$day_of_year;
			//echo "t3=>(((".$balance."*".$interest_rate.")/100)*".$date_count.")/".$day_of_year."<br>";
		}
		
		return $interest;
	}
	
	public function cal_deposit_interest($row_member,$return_type = 'test_cal_interest', $date_interest = '', $day_interest = '', $param_date_dep = '', $from_holiday = false, &$interest_close = 0){
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
		
		if(@$row_detail['is_day_cal_interest'] == 1){
			$date_interest = date("Y-m-d", strtotime("-1 day", strtotime($date_interest)));
		}

		//echo $this->db->last_query();
		if(empty($row_detail)){return false;}
		
		if($this->debug) { echo"<pre>";print_r($row_detail);echo"</pre>"; }
		
		// วันหยุด
		if($return_type != 'return_interest') {
			$is_holiday = $this->_is_holiday($date_interest);
			if(in_array($row_detail['condition_age'], array(1, 2, 3)) && !$is_holiday && $param_date_dep == '') {
				$holidays =  $this->_get_near_holiday_list($date_interest);
				if(!empty($holidays)) {
					foreach($holidays as $holiday) {
						echo"<pre>*** START ".$holiday." ***</pre>";
						$this->cal_deposit_interest($row_member, $return_type, $date_interest." ".$time_interest, $day_interest, $holiday, true, $interest_close);
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
			
			$where = "t1.account_id = '".$row_member['account_id']."' AND transaction_list IN ('OPN', 'OCA', 'OPT', 'TRB', 'XD', 'DEP', 'DCA', 'DFX', 'DEPP', 'CD', 'BF', 'BF') AND
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
											ORDER BY start_date
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
			
			if($return_type != 'return_interest') {
				$is_process = false;
			}
			
			if($row_detail['type_interest'] == '4' && $param_date_dep == '' && !$is_holiday && $is_near_holiday_end_month) {
				$is_process = true;
			}
			
			//if($date_dep != date('Y-m-t') && !$is_process && !$is_due){
			if($date_dep != date('Y-m-t', strtotime($date_dep)) && !$is_process && !$is_due){
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
			//จ่ายเมื่อครบกำหนด  x เดือน
			$num_month_maturity = (int)$row_detail['num_month_maturity'];
			
			$where_chk = " AND (";
			$where_chk .= "(YEAR(transaction_time) = YEAR(DATE_ADD('".$date_dep."', INTERVAL -".$num_month_maturity." MONTH))
				AND MONTH(transaction_time) = MONTH(DATE_ADD('".$date_dep."', INTERVAL -".$num_month_maturity." MONTH))
				AND DAY(transaction_time) = DAY(DATE_ADD('".$date_dep."', INTERVAL -".$num_month_maturity." MONTH)))";
			
			$cur_day_count = date("t", strtotime($date_dep));
			$_row = $this->db->query("SELECT DATE_ADD('".$date_dep."', INTERVAL -".$num_month_maturity." MONTH) AS prev_date")->row_array();
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
			
			if($this->_is_holiday(date("Y-m-d", strtotime($_row["prev_date"]))) && $row_detail['type_interest'] == 4) {
				for($i = 1; $i <= 31; $i++) { // anti infinity loop
					$_row2 = $this->db->query("SELECT DATE_ADD('".$_row["prev_date"]."', INTERVAL ".$i." DAY) AS next_date")->row_array();
					if(!$this->_is_holiday(date("Y-m-d", strtotime($_row2["next_date"])))) {
						$where_chk .= " OR (YEAR(transaction_time) = YEAR('".$_row2["next_date"]."')
							AND MONTH(transaction_time) = MONTH('".$_row2["next_date"]."')
							AND DAY(transaction_time) = DAY('".$_row2["next_date"]."'))";
						break;
					}
				}
			}
			
			$where_chk .= ")";
			
			$this->db->select('transaction_id');
			$this->db->from("coop_account_transaction{$this->forecast}");
			$this->db->where("fixed_deposit_status <> '1' AND account_id = '".$row_member['account_id']."' AND transaction_deposit > 0 ".$where_chk);
			$this->db->limit(1);
			//echo $this->db->get_compiled_select(null, false)."<br>";
			$rs_chk = $this->db->get()->result_array();
			
			if((empty($rs_chk) || $is_holiday) && $return_type != 'return_interest'){
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
			$this->db->where("fixed_deposit_status <> '1' AND account_id = '".$row_member['account_id']."' AND transaction_list IN ('OPN', 'OCA', 'OPT', 'TRB', 'XD', 'DEP', 'DCA', 'DFX', 'DEPP', 'CD') ".$where_chk);
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
			
			
			
			
			/*$this->db->select(array(
				't1.transaction_id',
				't1.deposit_balance', 
				't1.transaction_time'
			));
			$this->db->from("coop_account_transaction{$this->forecast} as t1");
			$this->db->where("
			t1.account_id = '".$row_member['account_id']."' 
			AND t1.day_cal_interest = '".$day_interest."' 
			AND t1.fixed_deposit_type = 'principal'
			AND t1.fixed_deposit_status = '0'
			");
			$this->db->order_by('t1.transaction_time ASC');
			$row_transaction = $this->db->get()->result_array();
		
			$transaction = array();
			foreach($row_transaction as $key => $value){
				$this->db->select(array('t1.transaction_id'));
				$this->db->from("coop_account_transaction{$this->forecast} as t1");
				$this->db->where("
					t1.ref_transaction_id = '".$value['transaction_id']."'
					AND t1.interest_period = '".$row_detail['num_month_before']."'
				");
				$row_chk_limit = $this->db->get()->result_array();
				if(!empty($row_chk_limit)){
					continue;
				}
				$this->db->select(array(
					't1.transaction_id',
					't1.deposit_interest_balance', 
					't1.transaction_time',
					't1.interest_period',
					't1.ref_transaction_id'
				));
				$this->db->from("coop_account_transaction{$this->forecast} as t1");
				$this->db->where("
					t1.ref_transaction_id = '".$value['transaction_id']."' 
				");
				$this->db->order_by('t1.interest_period DESC');
				$this->db->limit(1);
				$row_prev_interest = $this->db->get()->result_array();
				if(!empty($row_prev_interest)){
					$row_prev_interest = $row_prev_interest[0];
					$transaction[$key]['type_id'] = $row_member['type_id'];
					$transaction[$key]['create_account_date'] = $row_member['create_account_date'];
					$transaction[$key]['date_begin'] = date('Y-m-d',strtotime($value['transaction_time']));
					$transaction[$key]['transaction_list'] = $value['transaction_list'];
					$transaction[$key]['transaction_id'] = $row_prev_interest['transaction_id'];
					$transaction[$key]['ref_transaction_id'] = $row_prev_interest['ref_transaction_id'];
					$transaction[$key]['date_start'] = date('Y-m-d',strtotime($row_prev_interest['transaction_time']));
					$transaction[$key]['transaction_balance'] = $row_prev_interest['deposit_interest_balance'];
					$transaction[$key]['account_id'] = $row_member['account_id'];
					$transaction[$key]['interest_period'] = $row_prev_interest['interest_period']+1;
					$transaction[$key]['date_end'] = $date_interest;
					$transaction[$key]['deposit_interest_balance'] = $row_prev_interest['deposit_interest_balance'];
				}else{
					$transaction[$key]['type_id'] = $row_member['type_id'];
					$transaction[$key]['create_account_date'] = $row_member['create_account_date'];
					$transaction[$key]['date_begin'] = date('Y-m-d',strtotime($value['transaction_time']));
					$transaction[$key]['transaction_list'] = $value['transaction_list'];
					$transaction[$key]['transaction_id'] = $value['transaction_id'];
					$transaction[$key]['ref_transaction_id'] = $value['transaction_id'];
					$transaction[$key]['date_start'] = date('Y-m-d',strtotime($value['transaction_time']));
					$transaction[$key]['transaction_balance'] = $value['deposit_balance'];
					$transaction[$key]['account_id'] = $row_member['account_id'];
					$transaction[$key]['interest_period'] = '1'; 
					$transaction[$key]['date_end'] = $date_interest;
					$transaction[$key]['deposit_interest_balance'] = $value['deposit_balance'];
				}
					
			}*/
			
			$fix_saving = true;
		}
		elseif($row_detail['pay_interest'] == '4'){
			if($return_type == 'return_interest') {
				$where_chk = " AND transaction_time >= DATE_ADD('".$date_interest."', INTERVAL -".$row_detail["max_month"]." MONTH)";
			}
			else {
				/*$where_chk = " AND transaction_time <= DATE_ADD('".$date_dep." 23:59:59', INTERVAL -".$num_month_maturity." MONTH)".
					(empty($row_detail["max_month"]) ? "" : " AND transaction_time >= DATE_ADD('".$date_dep."', INTERVAL -".$row_detail["max_month"]." MONTH)")."
					AND DAY(transaction_time) = DAY(DATE_ADD('".$date_dep."', INTERVAL -".$num_month_maturity." MONTH))";*/
				
				$where_chk = " AND (";
				$where_chk .= "(YEAR(transaction_time) = YEAR(DATE_ADD('".$date_dep."', INTERVAL -".$num_month_maturity." MONTH))
					AND MONTH(transaction_time) = MONTH(DATE_ADD('".$date_dep."', INTERVAL -".$num_month_maturity." MONTH))
					AND DAY(transaction_time) = DAY(DATE_ADD('".$date_dep."', INTERVAL -".$num_month_maturity." MONTH)))";
				
				$cur_day_count = date("t", strtotime($date_dep));
				$_row = $this->db->query("SELECT DATE_ADD('".$date_dep."', INTERVAL -".$num_month_maturity." MONTH) AS prev_date")->row_array();
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
				
				if($this->_is_holiday(date("Y-m-d", strtotime($_row["prev_date"]))) && $row_detail['type_interest'] == 4) {
					for($i = 1; $i <= 31; $i++) { // anti infinity loop
						$_row2 = $this->db->query("SELECT DATE_ADD('".$_row["prev_date"]."', INTERVAL ".$i." DAY) AS next_date")->row_array();
						if(!$this->_is_holiday(date("Y-m-d", strtotime($_row2["next_date"])))) {
							$where_chk .= " OR (YEAR(transaction_time) = YEAR('".$_row2["next_date"]."')
								AND MONTH(transaction_time) = MONTH('".$_row2["next_date"]."')
								AND DAY(transaction_time) = DAY('".$_row2["next_date"]."'))";
							break;
						}
					}
				}
				
				$where_chk .= ")";
			}
			$this->db->select("transaction_time, transaction_id, transaction_deposit, transaction_balance, transaction_list, TIMESTAMPDIFF(MONTH,transaction_time, '".$date_interest." 23:59:59') AS period");
			$this->db->from("coop_account_transaction{$this->forecast}");
			$this->db->where("fixed_deposit_status <> '1' AND account_id = '".$row_member['account_id']."' AND transaction_list IN ('OPN', 'OCA', 'OPT', 'TRB', 'XD', 'DEP', 'DCA', 'DFX', 'DEPP', 'CD') ".$where_chk);
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
			//echo"<pre>";print_r($row_interest_rate);echo"</pre>";
			
			if($fix_saving) {
				$i=0;
				$transaction_new = array();
				foreach($transaction as $key => $value){
					$transaction_new[$i] = $value;
					
					$interest_rate = 0;
					$interest_depositor_sum = 0;
					$depositor_balance = $value['transaction_deposit'];
					$transaction_balance = $value['transaction_deposit'];
					$period = 1;
					for($period_m = $num_month_maturity; $period_m < $value["interest_period"]; $period_m += $num_month_maturity) {
						foreach($row_interest_rate as $key2 => $value2){
							if($period <= $value2['num_month']){
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
					
					$transaction_new[$i]['interest_period'] = $period;
					$transaction_new[$i]['transaction_balance'] = $transaction_balance;
					$transaction_new[$i]['deposit_interest_balance'] = $transaction_balance;
					$transaction_new[$i]['interest_rate'] = $interest_rate;
					if(!empty($row_date["date_start"])) {echo $row_date["date_start"];
						if($this->_is_holiday(date("Y-m-d", strtotime($row_date["date_start"]))) && $row_detail['type_interest'] == 4) {
							for($i2 = 1; $i2 <= 31; $i2++) { // anti infinity loop
								$_row2 = $this->db->query("SELECT DATE_ADD('".$row_date["date_start"]."', INTERVAL ".$i2." DAY) AS next_date")->row_array();
								if(!$this->_is_holiday(date("Y-m-d", strtotime($_row2["next_date"])))) {
									$row_date["date_start"] = $_row2["next_date"];
									break;
								}
							}
						}
						
						$transaction_new[$i]['date_start'] = $row_date["date_start"];
					}
					$transaction_new[$key]['depositor_date_start'] = $_cal["date_end"];
					$transaction_new[$key]['interest_depositor_sum'] = $interest_depositor_sum;
					$transaction_new[$key]['depositor_balance'] = $depositor_balance;
					
					foreach($row_interest_rate as $key2 => $value2){
						if(strtotime($value['date_start']) >= strtotime($value2['start_date']) && strtotime($value['date_start']) <= strtotime($value2['end_date'])){
							if(strtotime($value['date_start']) >= strtotime($value2['start_date'])){
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
			}
			else {
				$transaction = array();
				$index = 0;
				$balance = 0;
				$is_prev_holiday = false;
				$cur_end_date = "";
				
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
						if($this->_is_holiday(date("Y-m-t", strtotime($row_date["date_prev"])))) {
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
								$transaction[$index]['percent_depositor'] = $value2['percent_depositor'];
								$cur_end_date = $value2['end_date'];
							}
							/*if(strtotime($date_dep) > strtotime($value2['end_date'])){
								$end_date = date_create($value2['end_date']);
								date_add($end_date, date_interval_create_from_date_string("-1 day"));
								$end_date = date_format($end_date, "Y-m-d");
								
								$transaction[$index]['date_end'] = $end_date;
								$index++;
								$transaction[$index] = $transaction[$index - 1];
								$transaction[$index]['date_start'] = $end_date;
								$transaction[$index]['date_end'] = $date_interest;
								$transaction[$index]['interest_rate'] = @$row_interest_rate[($key2+1)]['interest_rate'];
								$transaction[$index]['percent_depositor'] = @$row_interest_rate[($key2+1)]['percent_depositor'];
							}*/
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
					$this->db->where("account_id = '".$row_member['account_id']."'
						AND YEAR(transaction_time) = YEAR('".$date_dep."')
						AND MONTH(transaction_time) = MONTH('".$date_dep."')".
						($is_prev_holiday ? " AND transaction_time > '".$prev_int_date." 23:59:59'" : ""));
					// AND transaction_list IN ('OPN', 'OCA', 'OPT', 'TRB', 'XD', 'DEP', 'DCA', 'DFX', 'DEPP', 'CD', 'INT', 'IN', 'WTD', 'WTB', 'CW', 'ERR')
					$this->db->order_by("transaction_time, transaction_id");
				}
				$_rs = $this->db->get();
				$is_split_rate = false;
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
							$transaction[$index]['account_id'] = $row_member['account_id'];
							$transaction[$index]['date_start'] = date('Y-m-d', strtotime($_row['transaction_time']));
							$transaction[$index]['date_end'] = $date_interest;
							$transaction[$index]['transaction_balance'] = $balance > 0 ? $balance : 0;
							$transaction[$index]['type'] = 'DEP';
							
							foreach($row_interest_rate as $key2 => $value2){
								if(strtotime($transaction[$index]['date_start']) >= strtotime($value2['start_date']) && strtotime($transaction[$index]['date_start']) <= strtotime($value2['end_date'])){
								//if(strtotime($transaction[$index]['date_start']) >= strtotime($transaction[$index]['date_end']) && strtotime($transaction[$index]['date_start']) <= strtotime($value2['end_date'])){
									$transaction[$index]['interest_rate'] = $value2['interest_rate'];
									
									if($this->debug) { echo "debug_dep: ".$transaction[$index - 1]['date_end']." : ".$value2['end_date']." : ".$cur_end_date."<br>"; }
									if($value2['end_date'] != $cur_end_date && $cur_end_date != ""){
										$is_split_rate = true;
										$end_date = date_create($cur_end_date);
										date_add($end_date, date_interval_create_from_date_string("-1 day"));
										$end_date = date_format($end_date, "Y-m-d");
										
										$transaction[$index - 1]['date_end'] = $end_date;
										$transaction[$index - 1]['interest_rate'] = $row_interest_rate[$key2 - 1]['interest_rate'];
										$transaction[$index + 1] = $transaction[$index];
										
										$transaction[$index]['date_end'] = $transaction[$index]['date_start'];
										$transaction[$index]['date_start'] = $end_date;
										$transaction[$index]['transaction_balance'] = $transaction[$index - 1]['transaction_balance'];
										
										$index++;
										$transaction[$index]['date_start'] = $transaction[$index - 1]['date_end'];
									}
									
									$cur_end_date = $value2['end_date'];
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
								
								if($this->debug) { echo "debug_wtd: ".$transaction[$index - 1]['date_end']." : ".$value2['end_date']." : ".$cur_end_date."<br>"; }
								if($value2['end_date'] != $cur_end_date && $cur_end_date != ""){
									$is_split_rate = true;
									$end_date = date_create($cur_end_date);
									date_add($end_date, date_interval_create_from_date_string("-1 day"));
									$end_date = date_format($end_date, "Y-m-d");
									
									$transaction[$index - 1]['date_end'] = $end_date;
									$transaction[$index - 1]['interest_rate'] = $row_interest_rate[$key2 - 1]['interest_rate'];
									$transaction[$index + 1] = $transaction[$index];
									
									$transaction[$index]['date_end'] = $transaction[$index]['date_start'];
									$transaction[$index]['date_start'] = $end_date;
									$transaction[$index]['transaction_balance'] = $transaction[$index - 1]['transaction_balance'];
									
									$index++;
									$transaction[$index]['date_start'] = $transaction[$index - 1]['date_end'];
								}
								
								$cur_end_date = $value2['end_date'];
							}
						}
						
						$index++;
					}
				}
				
				$index--;
				if(strtotime($transaction[$index]['date_start']) < strtotime($row_interest_rate[count($row_interest_rate) - 1]["start_date"])) {
					foreach($row_interest_rate as $key2 => $value2){
						if(strtotime($transaction[$index]['date_start']) >= strtotime($value2['start_date']) && strtotime($transaction[$index]['date_start']) <= strtotime($value2['end_date'])){
							if(strtotime($transaction[$index]['date_end']) > strtotime($value2['end_date'])){
								$end_date = date_create($value2['end_date']);
								date_add($end_date, date_interval_create_from_date_string("-1 day"));
								$end_date = date_format($end_date, "Y-m-d");
								
								$transaction[$index]['date_end'] = $end_date;
								$index++;
								$transaction[$index] = $transaction[$index - 1];
								$transaction[$index]['date_start'] = $end_date;
								$transaction[$index]['date_end'] = $date_interest;
								$transaction[$index]['interest_rate'] = @$row_interest_rate[($key2+1)]['interest_rate'];
								$transaction[$index]['percent_depositor'] = @$row_interest_rate[($key2+1)]['percent_depositor'];
							}
						}
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
				
				if($row_detail['type_interest'] == '4' && $row_detail['pay_interest'] == '1' && $return_type != 'return_interest') {
					if(date("d", strtotime($date_dep)) != date("t", strtotime($date_dep))) {
						$date_dep_in = $date_dep_prev;
						
						if($this->_is_holiday($date_dep_in)) {
							$is_cal = true;
						}
					}
				}
				
				if($row_detail['pay_interest'] == '1' && $this->_is_holiday($date_dep_prev) && date("d", strtotime($date_dep)) == date("t", strtotime($date_dep))) {
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
					$this->db->where("account_id = '".$row_member['account_id']."' AND transaction_list IN ('INT', 'IN')
						AND transaction_time < '".$date_dep."'");
					$this->db->order_by("transaction_time DESC, transaction_id DESC");
					$this->db->limit(1);
					$row_date_in = $this->db->get()->row_array();
					if(!empty($row_date_in)) {
						$this->db->select("transaction_id, transaction_list, transaction_time, transaction_balance, account_id");
						$this->db->from("coop_account_transaction{$this->forecast}");
						$this->db->where("account_id = '".$row_member['account_id']."'
							AND YEAR(transaction_time) = YEAR('".$row_date_in['transaction_time']."')
							AND MONTH(transaction_time) = MONTH('".$row_date_in['transaction_time']."')
							AND DAY(transaction_time) = DAY('".$row_date_in['transaction_time']."')");
						$this->db->order_by("transaction_time DESC, transaction_id DESC");
						$this->db->limit(1);
					}
					else {
						$this->db->select("transaction_id, transaction_list, transaction_time, transaction_balance, account_id");
						$this->db->from("coop_account_transaction{$this->forecast}");
						$this->db->where("account_id = '".$row_member['account_id']."'
							AND YEAR(transaction_time) = YEAR(DATE_ADD('".$date_dep_in."', INTERVAL -1 MONTH))
							AND MONTH(transaction_time) <= MONTH(DATE_ADD('".$date_dep_in."', INTERVAL -1 MONTH))");
						$this->db->order_by("transaction_time DESC, transaction_id DESC");
						$this->db->limit(1);
					}
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
				}
				
				$this->db->select("transaction_time, transaction_list, transaction_deposit, transaction_withdrawal, transaction_balance");
				$this->db->from("coop_account_transaction{$this->forecast}");
				$this->db->where("account_id = '".$row_member['account_id']."'
					AND YEAR(transaction_time) = YEAR('".$date_dep_in."')
					AND MONTH(transaction_time) = MONTH('".$date_dep_in."')");
				// AND transaction_list IN ('OPN', 'OCA', 'OPT', 'TRB', 'XD', 'DEP', 'DCA', 'DFX', 'DEPP', 'CD', 'INT', 'IN', 'WTI', 'WTD', 'WTB', 'CW', 'CM/FE', 'ERR')
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
							$transaction[$index]['account_id'] = $row_member['account_id'];
							$transaction[$index]['date_start'] = date('Y-m-d', strtotime($_row['transaction_time']));
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
							$transaction[$index - 1]['date_end'] = date('Y-m-d', strtotime($_row['transaction_time']));
							$transaction[$index] = $transaction[$index - 1];
						}
						$transaction[$index]['date_start'] = date('Y-m-d', strtotime($_row['transaction_time']));
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
				't1.type_detail_id'
			));
			$this->db->from('coop_deposit_type_setting_detail as t1');
			$this->db->where("t1.type_id = '".@$row_member['type_id']."' AND t1.start_date <= '".$date_dep."'");
			$this->db->order_by("t1.start_date DESC");
			$this->db->limit(1);
			$row_dep_type_detail = $this->db->get()->row_array();
			
			$this->db->select(array(
				't1.type_detail_id',
				't1.type_id',
				't1.start_date',
				't2.amount_deposit',
				't2.percent_interest as interest_rate'
			));
			$this->db->from('coop_deposit_type_setting_detail as t1');
			$this->db->join('coop_deposit_type_setting_interest as t2','t1.type_detail_id = t2.type_detail_id AND t1.condition_interest = t2.condition_interest','inner');
			$this->db->where("t1.type_id = '".@$row_member['type_id']."' AND t1.type_detail_id = '".$row_dep_type_detail["type_detail_id"]."' AND t2.percent_interest IS NOT NULL AND t2.amount_deposit IS NOT NULL");
			$this->db->order_by("start_date ASC, t2.amount_deposit ASC");
			$interest_rates = $this->db->get()->result_array();
		}
		
		foreach($transaction as $key => $value){
			$interest_rate = @$value['interest_rate'];
			$diff = @date_diff(date_create($value['date_start']),date_create($value['date_end']));
			$date_count = @$diff->format("%a");
			$date_count = $date_count;
			$transaction[$key]['date_count'] = $date_count;
			$day_of_year = date("z", strtotime(substr($value['date_start'], 0, 4)."-12-31")) + 1;
			
			// ฝากไม่ถึง  x เดือน ไม่คิดดอกเบี้ย คิดเป็น วัน
			$num_month_no_interest = $row_detail['num_month_no_interest']*30;
			if($row_detail['type_interest'] == '3' &&  $date_count < $num_month_no_interest){
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
								//$interest += ((($cal_val*$interest_rates[$key2]['interest_rate'])/100)*$date_count)/$day_of_year;
								$interest += $this->_cal_interest($cal_val, $interest_rates[$key2]['interest_rate'], $value['date_start'], $value['date_end']);
								if($this->debug) { echo "interest_index: {$interest_index} : {$transaction[$key]['transaction_balance']} : {$cal_balance} : {$cal_val} : {$interest_rates[$key2]['interest_rate']} : {$interest} : {$date_count}<br>"; }
								$cal_balance -= $cal_val;
							}
							
							$interest_index++;
						}
						
					}
				}
				else {
					//$interest = ((($value['transaction_balance']*@$interest_rate)/100)*$date_count)/$day_of_year;
					$interest = $this->_cal_interest($value['transaction_balance'], $interest_rate, $value['date_start'], $value['date_end']);
				}
			}else{
				//คิดดอกเบี้ย ดอกเบี้ยทบต้น
				//$interest = ((($value['transaction_balance']*@$interest_rate)/100)*$date_count)/$day_of_year;
				$interest = $this->_cal_interest($value['transaction_balance'], $interest_rate, $value['date_start'], $value['date_end']);
			}

			$transaction[$key]['interest'] = $interest;
			
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
		$interest_all -= $interest_close;
		
		if($this->debug) { echo"<pre>";echo "interest_close: ".$interest_close." interest_all: ".$interest_all;echo"</pre>"; }
		
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
								AND YEAR(transaction_time) = YEAR('".$date_dep."')
								AND MONTH(transaction_time) = MONTH('".$date_dep."')".
								($is_prev_holiday ? " AND transaction_time > '".$prev_int_date." 23:59:59'" : ""));
							if($row_int = $this->db->get()->row_array()) {
								if($this->debug) { echo"<pre>";echo "is_prev_holiday: ".$is_prev_holiday." transaction_time: ".$transaction_time." date_dep: ".$date_dep." prev_int_date: ".$prev_int_date;echo"</pre>"; }
								if($this->debug) { echo"<pre>";print_r($row_int);echo"</pre>"; }
								$interest -= (double)$row_int["interest_deduct"];
							}
						}
						
						$sum = $balance + $interest;
						
						$is_interest = false;
						if(!($from_holiday && $row_detail['pay_interest'] == '1' && $row_detail['type_interest'] != '4')) {
							$is_interest = true;
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
							if($row_detail["pay_interest"] == 1 && (date("d", strtotime($date_interest)) != date("t", strtotime($date_interest)) || $from_holiday) && !$is_interest) {
								if($this->debug) { echo"<pre>";echo "due : transaction_deposit: ".$row_due["transaction_deposit"]." date_count: ".$row_date["date_count"]." interest_rate: ".$interest_rate;echo"</pre>"; }
								
								if($row_detail['condition_age'] == 2) {
									$interest = number_format($interest_all,2,'.','');
								}
								else {
									$day_of_year = date("z", strtotime(substr($date_interest, 0, 4)."-12-31")) + 1;
									$interest = round(((($row_due["transaction_deposit"]*@$interest_rate)/100)*$row_date["date_count"])/$day_of_year, 2);
								}
								
								$interest_close += $interest;
								
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
								$data_insert['transaction_withdrawal'] = number_format($balance + $interest,2,'.','');
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
							
							if($row_detail['condition_age'] == 2) {
								break;
							}
						}
					}
				}else{
					foreach($transaction as $key => $value){
						$interest = number_format($value['interest'],2,'.','');
						if($interest > 0){
							$transaction_time = $date_interest." ".$time_interest;
							$adj_interest_period = $row_detail['pay_interest'] == 4 ? $value['interest_period'] * $row_detail['num_month_maturity'] : $value['interest_period'];
							
							if(($row_detail['max_month'] == $adj_interest_period && !$is_holiday)
							   || ($row_detail['max_month'] != $value['interest_period'] && $date_interest == $date_dep && $row_detail['type_interest'] != 4)
							   || ($row_detail['max_month'] != $value['interest_period'] && $date_interest == $date_dep && $row_detail['type_interest'] == 4 && !$is_holiday)
							   || !empty($param_date_dep))
							{
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
								$where = "fixed_deposit_status <> '1' AND account_id = '".$row_member['account_id']."' AND transaction_list IN ('OPN', 'OCA', 'OPT', 'TRB', 'XD', 'DEP', 'DEPP', 'DCA', 'DFX', 'CD') AND
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
			if(in_array($row_detail['pay_interest'], array('3', '1', '2'))) {
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
					elseif($row_detail['type_interest'] == '1') {
						$this->db->select("transaction_id");
						$this->db->from("coop_account_transaction{$this->forecast}");
						$this->db->where("account_id = '".$row_member['account_id']."' AND transaction_time <= DATE_ADD('".$date_interest." 23:59:59', INTERVAL - ".((int)$row_detail['num_month_before'])." MONTH)");
						$this->db->order_by("transaction_time, transaction_id");
						$this->db->limit(1);
						if(!($row_chk = $this->db->get()->row_array())) {
							$this->db->select("SUM(transaction_deposit) AS return_interest");
							$this->db->from("coop_account_transaction{$this->forecast}");
							$this->db->where("account_id = '".$row_member['account_id']."' AND transaction_list IN ('INT', 'IN', 'ERR')");
							$row_return = $this->db->get()->row_array();
							$interest_return = $row_return['return_interest'] * ($transaction[0]["interest_rate"] - $row_detail['percent_depositor']) / $transaction[0]["interest_rate"];
						}
					}
					
					/*if(!empty($row_detail['percent_depositor'])) {
						//$interest_all = round($interest_all * $row_detail['percent_depositor'] / $interest_rate, 2);
						if($interest_rate != 0) {
							$interest_return = round($interest_all * ($interest_rate - $row_detail['percent_depositor']) / $interest_rate, 2);
						}
					}*/
					
					foreach($transaction as $key => $value) {
						if(!empty($value['percent_depositor'])) {
							if($value["interest_rate"] != 0) {
								$interest_return += $value["interest"] * ($value["interest_rate"] - $row_detail['percent_depositor']) / $value["interest_rate"];
							}
						}
					}
				}
				
				return array(
					"interest" => $interest_all,
					"interest_return" => round($interest_return, 2),
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
}
