<?php if( ! defined('BASEPATH')) exit('No direct script access allowed');

class Report_accrued_interest_model extends CI_Model {
    public function __construct()
    {
        parent::__construct();
    }

	public function get_deposit_type_name($type_id){
        $data = $this->db->select(array('type_id', 'type_name'))->from('coop_deposit_type_setting')->where("type_id='{$type_id}'")->get()->row_array()['type_name'];
		return $data;
	}
	
	public function get_deposit_type_interest($type_id,$date_start){
		$data_detail = $this->get_deposit_type_setting_detail($type_id,$date_start);
        $interest_rate = @$data_detail['interest_rate'];
		return $interest_rate;
	}
	
	public function get_deposit_type_days($type_id,$date_start){
		$data_detail = $this->get_deposit_type_setting_detail($type_id,$date_start);
		$days_in_year = $this->deposit_libraries->get_day_of_year_by_type($data_detail['days_in_year'], $date_start);
		return $days_in_year;
	}
	
	public function get_deposit_type_setting_detail($type_id,$_date_start){
		$data_detail = array();
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
		$data_detail = $this->db->get()->row_array();
		return $data_detail;
	}
	
	public function get_sum_accu_int($data){
		$arr_data = array();
		$type_id = $data['type_id'];
		$start_date = $data['start_date'];
		
		$row = $this->db->select("t1.account_id ,SUM(t2.interest) AS sum_accu_int")
						->from('coop_maco_account AS t1')
						->join("coop_account_transaction_acc_int AS t2","t1.account_id = t2.account_id","inner")
						->where("t1.type_id = '{$type_id}' AND YEAR(t2.date_start) = YEAR('{$start_date}')")
						->group_by("t1.account_id")
						->get()->result_array();		
		//echo $this->db->last_query(); exit;					
		if(!empty($row)){
			$arr_data = array_column($row,'sum_accu_int','account_id');
		}
		return $arr_data;
	}
	
	public function get_account_member_type($data){
		$arr_data = array();
		$type_id = $data['type_id'];
		$start_date = $data['start_date'];
		$end_date = $data['end_date'];
		
		$row = $this->db->select("t1.account_id,
									t1.mem_id AS member_id,
									t1.account_name,
									CONCAT(t3.prename_full,	t2.firstname_th,'  ',	t2.lastname_th) AS full_member_name")
						->from('coop_maco_account AS t1')
						->join("coop_mem_apply AS t2","t1.mem_id = t2.member_id","inner")
						->join("coop_prename AS t3","t3.prename_id = t2.prename_id","left")
						->where("t1.type_id = '{$type_id}' AND (t1.close_account_date >= '{$end_date}' OR t1.close_account_date IS NULL)")
						->order_by("t1.account_id ASC")
						->get()->result_array();
		
		$row_accu_int = $this->get_sum_accu_int($data);	
				
		if(!empty($row)){
			foreach($row AS $key=>$val){
				$arr_data[$key] = $val;
				$arr_data[$key]['deposit_interest'] = $row_accu_int[$val['account_id']];
			}
		}
		return $arr_data;
	}
	
	public function get_account_member_detail($data){
		$arr_data = array();
		$type_id = $data['type_id'];
		
		//$arr_code_withdrawal = $this->arr_code_transaction('W');//ถอน
		//$arr_code_deposit = $this->arr_code_transaction('D');//ฝาก
		//$arr_code_interest = $this->arr_code_transaction('I');//ดอกเบี้ย
		//$arr_code_tax = $this->arr_code_transaction('T');//ภาษี
		
		//$code_th = $this->get_code_th();
		//$code_th['BF'] = 'ยกมา';
		//$code_th['TF'] = 'ยกไป';
					
		if(@$data['account_id'] != ''){
			$where_account = " AND t1.account_id = '{$data['account_id']}'";
		}	
		
		if(trim(@$data['member_id']) != ''){
			$where_account = " AND t1.mem_id = '{$data['member_id']}'";
		}
		
		$row_transaction = $this->get_account_transaction($type_id,$data['start_date'],$data['end_date']);
		
		$row_head = $this->db->select("t1.type_id,
									t1.account_id,
									t1.account_name,
									t1.mem_id AS member_id,
									IF(t1.account_status = 0,'ปกติ' ,'ปิดบัญชี') AS account_status,
									t1.created,
									t1.close_account_date")
						->from('coop_maco_account AS t1')
						->where("t1.type_id = '{$type_id}' {$where_account} 
								AND (t1.close_account_date > '{$data['start_date']}' OR t1.close_account_date IS NULL ) AND YEAR(t1.created) <= YEAR('{$data['start_date']}') ")
						->order_by("t1.account_id ASC")
						->get()->result_array();

		if(!empty($row_head)){
			foreach($row_head AS $key=>$val){
				$arr_data[$val['account_id']]['row_head'] = $val;
				$arr_data[$val['account_id']]['row_head']['created'] = $this->center_function->mydate2date($val['created']);
				$arr_data[$val['account_id']]['row_head']['close_account_date'] = ($val['close_account_date'] !='')?$this->center_function->mydate2date($val['close_account_date']):'__/__/___';				
				$arr_data[$val['account_id']]['row_detail'] = $row_transaction[$val['account_id']]['row_detail'];
			}
		}
		return $arr_data;
	}
	
	public function arr_code_transaction($text){
		$arr_data = array();		
		$row = $this->db->select("money_type_name_short")
					->from("coop_money_type")
					->where("money_type = '{$text}'")
					->get()->result_array();
		if(!empty($row)){
			$arr_data = array_column($row, 'money_type_name_short');
		}	
		return $arr_data;
	}
	
	public function get_code_th(){
		$arr_data = array();		
		$row = $this->db->select("money_type_name_short,IF(money_type_name_th_short <> '' AND money_type_name_th_short IS NOT NULL ,money_type_name_th_short,money_type_name_short )AS money_type_name_th_short")
					->from("coop_money_type")
					->get()->result_array();
		if(!empty($row)){
			$arr_data = array_column($row,'money_type_name_th_short', 'money_type_name_short');
		}			
		//echo '<pre>'; print_r($arr_data); echo '</pre>';  exit;			
		return $arr_data;
	}
	
	//ยอดยกมา
	public function get_account_bf($start_date){
		$arr_data = array();
		$row = $this->db->select("	t2.account_id,
									t2.transaction_id,
									t2.date_start AS transaction_time,
									t2.transaction_list,
									t2.transaction_list AS transaction_code,
									'' AS transaction_withdrawal,
									t2.transaction_deposit,
									t2.interest,
									t2.transaction_balance,
									t2.interest AS accu_int")
				->from("(SELECT account_id,MAX(transaction_id) AS max_transaction_id,	MAX(date_start) AS max_transaction_time FROM coop_account_transaction_acc_int WHERE date_start <= '{$start_date}' AND transaction_list = 'BF'  GROUP BY account_id ) AS t1")
				->join("coop_account_transaction_acc_int AS t2","t1.account_id= t2.account_id AND t1.max_transaction_id = t2.transaction_id AND t1.max_transaction_time = t2.date_start","left")
				->where("t2.date_start <= '{$start_date}' ")
				->group_by("t2.account_id")
				->get()->result_array();
		//echo $this->db->last_query();		
		if(!empty($row)){
			$arr_data = $row;
		}			
		return $arr_data;
	}
	
	//ยอดยกไป
	public function get_account_tf($end_date){
		$arr_data = array();
		$row = $this->db->select("t2.account_id,
									t2.transaction_id,
									CONCAT(DATE(t2.transaction_time),' 23:59:59') AS transaction_time,
									'TF' AS transaction_list,
									'TF' AS transaction_code,
									t2.transaction_withdrawal,
									t2.transaction_deposit,
									t2.interest,
									t2.transaction_balance,
									'' AS accu_int")
					->from("(SELECT account_id,MAX(transaction_id) AS max_transaction_id,	MAX(transaction_time) AS max_transaction_time FROM coop_account_transaction WHERE transaction_time < '{$end_date}' GROUP BY account_id ) AS t1")
					->join("coop_account_transaction AS t2","t1.account_id= t2.account_id AND t1.max_transaction_id = t2.transaction_id AND t1.max_transaction_time = t2.transaction_time","left")
					->where("t2.transaction_time < '{$end_date}' ")
					->group_by("t2.account_id")
					->get()->result_array();
		//echo $this->db->last_query();			
		if(!empty($row)){
			$arr_data = $row;
		}
		return $arr_data;
	}
	
	//รายการที่
	public function get_run_row_transaction($account_id=''){
		$arr_data = array();
		$row = $this->db->select("transaction_id,account_id")
					->from("coop_account_transaction")
					//->where("account_id = '{$account_id}'")
					->order_by("account_id ASC,transaction_time ASC,transaction_id ASC")
					->get()->result_array();
		$chk_account_id = '';
		$i=0;
		if(!empty($row)){			
			foreach($row AS $key=>$val){
				if($val['account_id'] == $chk_account_id){
					$i++;
				}else{
					$chk_account_id = $val['account_id'];
					$i=1;
				}
				$arr_data[$val['account_id']][$val['transaction_id']] = $i;
			}	
		}
		return $arr_data;
	}
	
	public function get_account_transaction($type_id,$start_date,$end_date){
		$arr_data = array();
		$arr_code_withdrawal = $this->arr_code_transaction('W');//ถอน
		$arr_code_deposit = $this->arr_code_transaction('D');//ฝาก
		$arr_code_interest = $this->arr_code_transaction('I');//ดอกเบี้ย
		$arr_code_tax = $this->arr_code_transaction('T');//ภาษี
		
		$code_th = $this->get_code_th();
		$code_th['BF'] = 'ยกมา';
		$code_th['TF'] = 'ยกไป';
		
		$arr_run_row = $this->get_run_row_transaction();
		$row_account_bf = $this->get_account_bf($start_date);
		
		$row_detail = $this->db->select("t1.account_id,
											t1.transaction_id,
											t1.transaction_time,
											t1.transaction_list,
											t1.transaction_list AS transaction_code,
											t1.transaction_withdrawal,
											t1.transaction_deposit,
											t1.interest,
											t1.transaction_balance,
											t3.interest AS accu_int")
					->from('coop_account_transaction AS t1')
					->join("coop_maco_account AS t2","t1.account_id = t2.account_id","inner")
					->join("coop_account_transaction_acc_int AS t3","t1.transaction_id = t3.transaction_id AND t3.transaction_list <> 'BF'","left")
					->where("t1.transaction_time BETWEEN '{$start_date}' AND '{$end_date}' AND t2.type_id = '{$type_id}'")
					->order_by("t1.transaction_time ASC,t1.transaction_id ASC")
					->get()->result_array();
		//echo $this->db->last_query(); //exit;			
		$row_account_tf = array();
		$row_account_tf = $this->get_account_tf($end_date);
		$merge_transactions = array_merge($row_account_bf,$row_detail,$row_account_tf);
				array_multisort( array_column($merge_transactions, "transaction_time"), SORT_ASC,$merge_transactions );

		$arr_account = array_unique(array_column($row_detail,'account_id'));
		
		if(!empty($merge_transactions)){
			foreach($merge_transactions AS $key_detail=>$val_detail){
				if(in_array($val_detail['transaction_list'],$arr_code_withdrawal)){
					$transaction_withdrawal = $val_detail['transaction_withdrawal'];
					$date_withdrawal = $this->center_function->mydate2date(@$val_detail['transaction_time']);
				}else{
					$transaction_withdrawal = 0;
					$date_withdrawal= '__/__/___';
				}
				
				if(in_array($val_detail['transaction_list'],$arr_code_deposit)){
					$transaction_deposit = $val_detail['transaction_deposit'];
				}else{
					$transaction_deposit = 0;
				}
				
				if(in_array($val_detail['transaction_list'],$arr_code_interest)){
					$interest = $val_detail['transaction_deposit'];
				}else{
					$interest = 0;
				}					
				
				if(in_array($val_detail['transaction_list'],$arr_code_tax)){
					$tax = $val_detail['transaction_withdrawal'];
				}else{
					$tax = 0;
				}
				
				$arr_data[$val_detail['account_id']]['row_detail'][$key_detail] = $val_detail;
				if(in_array($val_detail['transaction_list'],array('BF','TF'))){
					$transaction_time = '';
					$transaction_no = '';
					$transaction_withdrawal = '';
					$transaction_deposit = '';
					$date_withdrawal= '';
					$interest= '';
					$tax= '';
				}else{
					$transaction_time = $val_detail['transaction_time'];
					$transaction_no = sprintf("%010d",$arr_run_row[$val_detail['account_id']][$val_detail['transaction_id']]);
				}
				
				$arr_data[$val_detail['account_id']]['row_detail'][$key_detail]['transaction_time'] = $transaction_time;						
				$arr_data[$val_detail['account_id']]['row_detail'][$key_detail]['transaction_no'] = $transaction_no;
				$arr_data[$val_detail['account_id']]['row_detail'][$key_detail]['transaction_list'] = $code_th[$val_detail['transaction_list']];
				$arr_data[$val_detail['account_id']]['row_detail'][$key_detail]['transaction_withdrawal'] = $transaction_withdrawal;
				$arr_data[$val_detail['account_id']]['row_detail'][$key_detail]['date_withdrawal'] = $date_withdrawal;
				$arr_data[$val_detail['account_id']]['row_detail'][$key_detail]['transaction_deposit'] = $transaction_deposit;
				$arr_data[$val_detail['account_id']]['row_detail'][$key_detail]['interest'] = $interest;
				$arr_data[$val_detail['account_id']]['row_detail'][$key_detail]['tax'] = $tax;
				$arr_data[$val_detail['account_id']]['row_detail'][$key_detail]['accrued_interest'] = $val_detail['accu_int'];
			}	
		}
		//echo '<pre>'; print_r($arr_data); echo '</pre>';	exit;
		return $arr_data;	
				
	}
	
	//เพิ่มข้อมูลในตารางเก็บดอกเบี้ยเงินฝาก
	public function insert_transaction_acc_int(){
		//$type_id = '11';
		$type_id = '13';
		
		//$arr_account = $this->get_account_member_type($type_id);
		$arr_account = $this->get_account_all_member_type($type_id);
		//echo '<pre>'; print_r($arr_account); echo '</pre>'; exit;
		
		//เงินฝากออมทรัพย์พิเศษเกษียณเพิ่มสุข 12 เดือน //type_id = '11'
		//$account_id = '0011000019';
		//$date_interest = '2020-12-06';
		
		//เงินฝากประจำเพื่อสร้างอนาคต ยกเว้นภาษี 24 เดือน //type_id = '13';
		//$account_id = '0013000328';
		//$date_interest = '2020-10-10';
		
		
		///$account_id = '0013000328';
		//$date_interest = date('Y-m-d');
		//$date_interest = '2020-10-08';
		//หาวันที่ครบกำหนดออกดอก
		//$date_interest = '2020-10-10';
		//$date_interest = '2020-12-31';
		if(!empty($arr_account)){
			foreach($arr_account AS $key=>$val){
				//$date_interest = $this->get_date_open_account($account_id);
				$account_id = $val['account_id'];
				echo 'account_id='.$account_id.'<br>';
				$date_due = $this->get_date_due_account($account_id);
				
				$this->db->select(array('t1.*' ,'t2.main_type_id'));
				$this->db->from('coop_deposit_type_setting_detail AS t1');
				$this->db->join("coop_deposit_type_setting AS t2","t1.type_id = t2.type_id","inner");
				$this->db->where("t1.type_id = '".$type_id."' AND t1.start_date <= '".$date_due."'");
				$this->db->order_by("t1.start_date DESC");
				$this->db->limit(1);
				$row_detail = $this->db->get()->row_array();
		
				if($row_detail['pay_interest'] == '4'){
					$date_interest = date('Y-m-d', strtotime("+".$row_detail['ext_num_month_maturity_day']." day", strtotime($date_due)));
				}else{
					$date_interest = $date_due;
				}
				//$date_interest = $this->get_date_close_account($account_id);
				//echo 'date_interest='.$date_interest.'<br>';
				
				//echo 'account_id='.$account_id.'|'.$val_interest['account_id'].'<br>';
				$arr_accrued_interest = $this->get_account_int_cal($account_id,$date_interest);
				//echo '<pre>'; print_r($arr_accrued_interest); echo '</pre>';
				if(!empty($arr_accrued_interest)){
					foreach($arr_accrued_interest AS $key_interest =>$val_interest){
						if($val_interest['transaction_id'] != ''){
							$data_insert = array();
							$data_insert['account_id'] = $val_interest['account_id'];
							$data_insert['type_id'] = $val_interest['type_id'];
							//$data_insert['transaction_time'] = $val_interest['transaction_time'];
							$data_insert['transaction_list'] = $val_interest['transaction_list'];
							$data_insert['transaction_deposit'] = number_format($val_interest['transaction_deposit'],2,'.','');
							$data_insert['transaction_balance'] = number_format($val_interest['transaction_balance'],2,'.','');
							$data_insert['date_start'] = $val_interest['date_start'];
							$data_insert['date_end'] = $val_interest['date_end'];
							$data_insert['date_count'] = $val_interest['date_count'];
							$data_insert['interest'] = $val_interest['interest'];
							$data_insert['interest_rate'] = $val_interest['interest_rate'];
							$data_insert['transaction_id'] = $val_interest['transaction_id'];
							$data_insert['user_id'] = $_SESSION['USER_ID'];
							$this->db->insert("coop_account_transaction_acc_int", $data_insert);
							//echo '<pre>'; print_r($data_insert); echo '</pre>';
						}
					}	
				}
				
			}
		}
	}	
	
	public function get_account_int_cal($account_id,$date_interest){
		$arr_data = array();
		//$date_interest = $end_date;
		//echo 'account_id='.$account_id.'<br>';
		//echo 'date_interest_1='.$date_interest.'<hr>';
		$cal_data = $this->deposit_libraries->cal_deposit_interest_by_acc_date($account_id, $date_interest);
		//$cal_data = $this->deposit_libraries->cal_deposit_interest($account_id, 'cal_interest', $date_interest." 23:00:00", '');
		//echo '<pre>'; print_r($cal_data); echo '</pre>'; 
		//exit;
		/*if(!empty($cal_data['detail'])){
			foreach($cal_data['detail'] AS $key=>$val){
				if($val['date_start'] >= $start_date && $val['date_end'] <= $end_date){					
					$arr_data[$val['transaction_id']]['date_count'] = $val['date_count'];
					$arr_data[$val['transaction_id']]['interest'] = $val['interest'];
				}
			}
		}
		*/
		$arr_data = $cal_data['detail'];
		return $arr_data;
	}
	
	//ข้อมูลบัญชีเงินฝากทั้งหมดของแต่ละประเภท
	public function get_account_all_member_type($type_id){
		$arr_data = array();
		$row = $this->db->select("t1.account_id,
									t1.mem_id AS member_id,
									t1.account_name,
									CONCAT(t3.prename_full,	t2.firstname_th,'  ',	t2.lastname_th) AS full_member_name")
						->from('coop_maco_account AS t1')
						->join("coop_mem_apply AS t2","t1.mem_id = t2.member_id","inner")
						->join("coop_prename AS t3","t3.prename_id = t2.prename_id","left")
						//->where("t1.type_id = '{$type_id}' AND  t1.account_id = '0013000001'")
						//->where("t1.type_id = '{$type_id}' AND  t1.account_id = '0013000328'")
						//->where("t1.type_id = '{$type_id}' AND  t1.account_id = '0013000270'")
						//->where("t1.type_id = '{$type_id}' AND  t1.account_id = '0013000001'")
						->where("t1.type_id = '{$type_id}' ")
						->order_by("t1.account_id ASC")
						->get()->result_array();
		//echo $this->db->last_query(); exit;				
		if(!empty($row)){
			$arr_data = $row;
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
	
	public function get_date_close_account($account_id){
		$date_close = '';
		$row= $this->db->select("t1.transaction_time")
						->from('coop_account_transaction AS t1')
						->join("coop_maco_account AS t2 "," t1.account_id = t2.account_id","inner")
						->where("t1.account_id = '{$account_id}' AND t2.account_status = '1'")
						->order_by("t1.transaction_time DESC,t1.transaction_id DESC")
						->limit(1)
						->get()->row_array()['transaction_time'];
		//echo $this->db->last_query(); exit;				
		if(!empty($row)){
			$date_close = $row;
		}
		return $date_close;
	}
	
	//public function get_date_due_account($account_id,$num_month_maturity){
	public function get_date_due_account($account_id){
		//$num_month_maturity = '12';	//เงินฝากออมทรัพย์พิเศษเกษียณเพิ่มสุข 12 เดือน
		$num_month_maturity = '24';	//	เงินฝากประจำเพื่อสร้างอนาคต ยกเว้นภาษี 24 เดือน
		$date_open = $this->get_date_open_account($account_id);
		echo 'date_open='.$date_open.'<br>';
		if($date_open != ''){
			$transaction_time= $this->db->select("transaction_time, transaction_id")
						->from('coop_account_transaction')
						->where("account_id = '{$account_id}' 
							AND transaction_list IN ('OPN', 'OCA', 'OPT', 'TRB', 'XD', 'DEP', 'DCA', 'DFX', 'DEPP', 'CD','DOT')
							AND (PERIOD_DIFF( DATE_FORMAT(transaction_time, '%Y%m'), DATE_FORMAT('{$date_open}', '%Y%m')  )+1) = {$num_month_maturity}")
						->order_by("transaction_time DESC,transaction_id DESC")
						->limit(1)
						->get()->row_array()['transaction_time'];	
			
			if(empty($transaction_time)){
				$sql = "SELECT t1.* FROM (
							SELECT
								(@row_number:=@row_number + 1) AS num_row,
								transaction_time,
								transaction_id 
							FROM
								coop_account_transaction ,
									(SELECT @row_number:=0) AS t
							WHERE
								account_id = '{$account_id}' 
								AND transaction_list IN ( 'OPN', 'OCA', 'OPT', 'TRB', 'XD', 'DEP', 'DCA', 'DFX', 'DEPP', 'CD', 'DOT' ) 

							ORDER BY 	transaction_time ASC,	transaction_id ASC 
						) AS t1 WHERE t1.num_row = {$num_month_maturity} ";
				$rs = $this->db->query($sql);
				$transaction_time = $rs->row_array()['transaction_time'];
			}
			
		}
		if(!empty($transaction_time)){
			$date_last = date("Y-m-d", strtotime($transaction_time));
		}
		return $date_last;
	}
	
	//อัพเดตวันที่ปิดบัญชี ของบัญชีที่ปิดไปแล้ว
	public function update_close_account_date(){
		$arr_data = array();
		$row = $this->db->select("account_id,account_status,close_account_date")
						->from('coop_maco_account')
						->where("account_status = 1 AND close_account_date IS NULL")
						->get()->result_array();
		//echo '<pre>'; print_r($row); echo '</pre>';
		foreach($row AS $key=>$val){
			$transaction_time_last = $this->db->select("transaction_time")
						->from('coop_account_transaction')
						->where("account_id = '{$val['account_id']}'")
						->order_by("transaction_time DESC,transaction_id DESC")
						->limit(1)
						->get()->row_array()['transaction_time'];
			$data_update = "UPDATE coop_maco_account SET close_account_date = '{$transaction_time_last}' WHERE account_id = '{$val['account_id']}';";			
			//echo $data_update.'<br>'; //อัพเดตเอง
			//$data_update = array();
			//$data_update['close_account_date'] = $transaction_time_last;
			//$this->db->where("account_id", $val['account_id']);
			//$this->db->update("coop_maco_account", $data_update);			
			//echo $val['account_id'].'<br>';
			//echo '<pre>'; print_r($transaction_time_last); echo '</pre>';
		}	
		return $arr_data;
	}
	
}
