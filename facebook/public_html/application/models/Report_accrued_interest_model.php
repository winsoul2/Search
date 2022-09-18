<?php if( ! defined('BASEPATH')) exit('No direct script access allowed');

class Report_accrued_interest_model extends CI_Model {
    public function __construct()
    {
        parent::__construct();
    }
	/*เดี๋ยวต้องมาทำส่วนของ  รันปีถัดไปอัตโนมัติ เมื่อถึงวันที่ 31 ธ.ค. ของทุกปี  ให้รัน sctipt ปีถัดไปเลย*/
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
		$deposit_interest = 0;		
		if(!empty($row)){
			foreach($row AS $key=>$val){
				$deposit_interest = @$row_accu_int[$val['account_id']];
				if($deposit_interest <= 0){
					continue;
				}
				$arr_data[$key] = $val;
				$arr_data[$key]['deposit_interest'] = $deposit_interest;
			}
		}
		return $arr_data;
	}
	
	public function get_account_member_detail($data){
		$arr_data = array();
		$type_id = $data['type_id'];
		
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
				if(sizeof($row_transaction[$val['account_id']]['row_detail']) == 1){
					continue;
				}
				$arr_data[$val['account_id']]['row_head'] = $val;
				$arr_data[$val['account_id']]['row_head']['created'] = $this->center_function->mydate2date($val['created']);
				$arr_data[$val['account_id']]['row_head']['close_account_date'] = ($val['close_account_date'] !='')?$this->center_function->mydate2date($val['close_account_date']):'__/__/___';								
				$arr_data[$val['account_id']]['row_detail'] = $row_transaction[$val['account_id']]['row_detail'];
			}
		}
		//echo '<pre>'; print_r($arr_data); echo '</pre>'; exit;
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
									t2.interest AS accu_int,
									'' AS seq_no,
									'1' AS c_num")
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
									'' AS accu_int,
									'' AS seq_no,
									'1' AS c_num")
					->from("(SELECT account_id,MAX(transaction_id) AS max_transaction_id,	MAX(transaction_time) AS max_transaction_time FROM coop_account_transaction WHERE transaction_time <= '{$end_date}' GROUP BY account_id ) AS t1")
					->join("coop_account_transaction AS t2","t1.account_id= t2.account_id AND t1.max_transaction_id = t2.transaction_id AND t1.max_transaction_time = t2.transaction_time","left")
					->where("t2.transaction_time <= '{$end_date}' ")
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
		$where = '1=1';
		if(@$account_id != ''){
			$where = "account_id = '{$account_id}'";
		}
		$row = $this->db->select("transaction_id,account_id,c_num")
					->from("coop_account_transaction_view")
					//->where("account_id = '{$account_id}'")
					->where($where)
					->order_by("account_id ASC,transaction_time ASC,transaction_id ASC,c_num ASC")
					->get()->result_array();
		//echo '<pre>'; print_r($row); echo '</pre>';
		$chk_account_id = '';
		$i=0;
		if(!empty($row)){			
			foreach($row AS $key=>$val){
				$transaction_id_n = $val['transaction_id'].'_'.$val['c_num'];
				if($val['account_id'] == $chk_account_id){
					$i++;
				}else{
					$chk_account_id = $val['account_id'];
					$i=1;
				}				
				//$arr_data[$val['account_id']][$val['transaction_id']] = $i;
				$arr_data[$val['account_id']][$transaction_id_n] = $i;
			}	
		}
		//echo '<pre>'; print_r($arr_data); echo '</pre>'; exit;
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
		
		$arr_date_due = $this->get_date_transaction();
		//echo '<pre>'; print_r($arr_date_due); echo '</pre>'; exit;
		
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
											t3.interest AS accu_int,
											t1.seq_no,
											t1.c_num")
					//->from('coop_account_transaction AS t1')
					->from('coop_account_transaction_view AS t1')
					->join("coop_maco_account AS t2","t1.account_id = t2.account_id","inner")
					->join("coop_account_transaction_acc_int AS t3","t1.transaction_id = t3.transaction_id AND t3.transaction_list <> 'BF'","left")
					->where("t1.transaction_time BETWEEN '{$start_date} 00:00:00' AND '{$end_date} 23:59:59' AND t2.type_id = '{$type_id}'")
					//->order_by("t1.transaction_time ASC,t1.transaction_id ASC")
					->order_by("t1.transaction_time ASC,t1.transaction_id ASC,t1.c_num ASC")
					->get()->result_array();
		//echo $this->db->last_query(); //exit;			
		$row_account_tf = array();
		$row_account_tf = $this->get_account_tf($end_date);
		$merge_transactions = array_merge($row_account_bf,$row_detail,$row_account_tf);
				array_multisort( array_column($merge_transactions, "transaction_time"), array_column($merge_transactions, "transaction_id"), array_column($merge_transactions, "c_num"), SORT_ASC,$merge_transactions );

		$arr_account = array_unique(array_column($row_detail,'account_id'));
		
		$flag = 0;
		$temp_data = array();

		if(!empty($merge_transactions)){
			foreach($merge_transactions AS $key_detail=>$val_detail){
				if(@$merge_transactions[$key_detail + 1]['transaction_list'] == 'WCHE'){	
					@$temp_data['transaction_withdrawal'] = @$merge_transactions[$key_detail + 1]['transaction_withdrawal'] - $val_detail['transaction_deposit'];
					@$temp_data['w_interest'] = @$val_detail['transaction_deposit'];
					@$temp_data['seq_no'] = @$val_detail['seq_no'];
					$runno--;
					$flag = 1;
					continue;
				}else{
					if ($flag) {						
						$val_detail['transaction_withdrawal'] = $temp_data['transaction_withdrawal'];
						$val_detail['interest']  = $temp_data['w_interest'];
						$val_detail['seq_no'] = $temp_data['seq_no'];
						unset($temp_data);
						$flag = 0;
					}
				}

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
					$interest = ($val_detail['interest'] != '')?$val_detail['interest']:0;
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
					$transaction_time = ($val_detail['c_num'] == '2')?$arr_date_due[$val_detail['account_id']][$val_detail['transaction_id'].'_'.$val_detail['c_num']]:$val_detail['transaction_time'];			
					//$transaction_no = sprintf("%010d",$arr_run_row[$val_detail['account_id']][$val_detail['transaction_id']]);
					//$transaction_no = sprintf("%010d",$arr_run_row[$val_detail['account_id']][$val_detail['transaction_id'].'_'.$val_detail['c_num']]);
					//$transaction_no = $arr_run_row[$val_detail['account_id']][$val_detail['transaction_id'].'_'.$val_detail['c_num']];
					$transaction_no = $val_detail['seq_no'];
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
	public function insert_transaction_acc_int($type_id,$date_interest,$account_id = '',$transaction_id = ''){
		$arr_accrued_interest = array();
		//echo $type_id.','.$date_interest.','.$account_id.'<br>'; exit;
		//$type_id = '11';
		//$type_id = '13';
		//if($date_interest == ''){
		//	$date_interest = '2020-12-31';
		//}
		
		/*
		//รันดอกคงค้างแค่ 2 ประเภท
		type_id	type_name			type_code
		11	เงินฝากออมทรัพย์พิเศษเกษียณเพิ่มสุข 12 เดือน	0011
		13	เงินฝากประจำเพื่อสร้างอนาคต ยกเว้นภาษี 24 เดือน	0013
		*/
		//exit;
		//echo $account_id.'<br>'; exit;
		if(@$account_id != ''){
			$arr_accrued_interest = $this->get_account_int_cal($account_id,$date_interest,$type_id,$transaction_id);
			$this->save_coop_account_transaction_acc_int($arr_accrued_interest);
		}else{
			$arr_account = $this->get_account_all_member_type($type_id);
			//echo '<pre>'; print_r($arr_account); echo '</pre>'; exit;

			if(!empty($arr_account)){
				foreach($arr_account AS $key=>$val){
					$account_id = $val['account_id'];
					//echo 'account_id='.$account_id.'<br>';
					
					$arr_accrued_interest = $this->get_account_int_cal($account_id,$date_interest,$type_id,$transaction_id);
					$this->save_coop_account_transaction_acc_int($arr_accrued_interest);				
				}
			}
		}
	}

	public function save_coop_account_transaction_acc_int($arr_accrued_interest){
		$arr_data = array();
		$affected_rows = 0;
		//echo '<pre>'; print_r($arr_accrued_interest); echo '</pre>';
		//exit;
		if(!empty($arr_accrued_interest)){
			foreach($arr_accrued_interest AS $key_interest =>$val_interest){
				if($val_interest['transaction_id'] != ''){
					if($val_interest['date_start'] > $val_interest['date_end']){
						continue;
					}
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
					$data_insert['createtime'] = date("Y-m-d H:i:s");
					$this->db->insert("coop_account_transaction_acc_int", $data_insert);
					//echo '<pre>'; print_r($data_insert); echo '</pre>';
					
					if($this->db->affected_rows()){
						$affected_rows++;
					}
		
				}
				
			}	
		}		
		
		if($affected_rows > 0){
			$arr_data['status'] = 'success';
		}else{
			$arr_data['status'] = 'error';
		}
		return $arr_data;
	}	
	
	public function get_account_int_cal($account_id,$date_interest,$type_id,$transaction_id = ''){
		$arr_data = array();
		
		if($type_id == '11'){
			//เงินฝากออมทรัพย์พิเศษเกษียณเพิ่มสุข 12 เดือน
			$arr_data = $this->get_data_account_k12($account_id,$date_interest,$transaction_id);
		}
		
		if($type_id == '13'){
			//เงินฝากประจำเพื่อสร้างอนาคต ยกเว้นภาษี 24 เดือน
			$arr_data = $this->get_data_account_n24($account_id,$date_interest,$transaction_id);
		}
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
						//->where("t1.type_id = '{$type_id}' AND  t1.account_id = '0013000220'")
						->where("t1.type_id = '{$type_id}' ")
						->order_by("t1.account_id ASC")
						->get()->result_array();
		//echo $this->db->last_query(); exit;
		//echo '<pre>'; print_r($row); echo '</pre>';	
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
	
	public function get_date_due_account($account_id){
		$num_month_maturity = '12';	//เงินฝากออมทรัพย์พิเศษเกษียณเพิ่มสุข 12 เดือน
		//$num_month_maturity = '24';	//	เงินฝากประจำเพื่อสร้างอนาคต ยกเว้นภาษี 24 เดือน
		$date_open = $this->get_date_open_account($account_id);
		//echo 'date_open='.$date_open.'<br>';
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
		//echo $this->db->last_query(); echo '<br>';
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
	
	//เงินฝากออมทรัพย์พิเศษเกษียณเพิ่มสุข 12 เดือน
	public function get_data_account_k12($account_id,$date_interest,$transaction_id=''){
			$data = array();
			//$account_id = '0011000003';
			//$date_end = '2020-12-31';
			$date_end = $date_interest;
			$chk_year = date("Y", strtotime($date_end));
			$type_id = '11';
			
			$this->db->select(array('t1.*' ,'t2.main_type_id'));
			$this->db->from('coop_deposit_type_setting_detail AS t1');
			$this->db->join("coop_deposit_type_setting AS t2","t1.type_id = t2.type_id","inner");
			$this->db->where("t1.type_id = '".$type_id."' AND t1.start_date <= '".$date_interest."'");
			$this->db->order_by("t1.start_date DESC");
			$this->db->limit(1);
			$row_detail = $this->db->get()->row_array();		
			$main_type_id = $row_detail['main_type_id'];
			
			$where = "";
			if(@$transaction_id != ''){
				$where = " AND t2.transaction_id = '{$transaction_id}'";
			}			
			$sql = "SELECT 
				t2.account_id,
				DATE(t2.transaction_time) AS transaction_time,
				DATE(t2.transaction_time) AS date_start,
				t2.transaction_id,
				t2.transaction_deposit,
				t2.transaction_balance,	
				t2.balance_deposit,
				t2.transaction_list,
				t2.ref_account_no
			FROM (
			SELECT
				account_id,
				ref_account_no,
				MAX( transaction_time ) AS max_transaction_time,
				MAX( transaction_id ) AS max_transaction_id 
			FROM
				coop_account_transaction 
			WHERE
				YEAR ( transaction_time ) = '{$chk_year}' AND account_id = '{$account_id}'
			GROUP BY
				account_id,
				ref_account_no
				) AS t1 
				LEFT JOIN coop_account_transaction AS t2 ON t1.account_id = t2.account_id AND t1.max_transaction_time = t2.transaction_time AND t1.max_transaction_id = t2.transaction_id AND t1.ref_account_no = t2.ref_account_no
				WHERE t2.account_id = '{$account_id}' AND YEAR ( t2.transaction_time ) = '{$chk_year}' {$where}
			AND t2.balance_deposit > 0
			ORDER BY t2.transaction_time ASC";
			$rs = $this->db->query($sql);
			$row = $rs->result_array();
			//echo $sql.'<br>'; exit;
			//echo '<pre>'; print_r($row); echo '</pre>'; exit;
			//echo '<pre>'; print_r($row_detail); echo '</pre>';
			$all_interest = 0;
			$i=0;
			foreach($row AS $key=>$value){
				$arr_chk_fee = array();
				$array_check_date = $this->deposit_libraries->check_interest_two_step($value['date_start'], $date_end, $type_id, $value['transaction_list'],$arr_chk_fee,$main_type_id);
				//echo '<pre>'; print_r($array_check_date); echo '</pre>';
				//$all_interest = 0;
				foreach($array_check_date AS $key_chk=>$val_chk){
					$date_count = @$val_chk['date_count'];
					//เคส นับต้น ไม่นับปลาย
					if($key_chk == 0){
						//บวก 1 วัน  รายการแรก
						$date_count = @$val_chk['date_count']+1;
					}
					
					$day_of_year = $val_chk['day_of_year'];
					$transaction_balance = $value['balance_deposit'];
					if(is_array($val_chk['interest_rate']) && $this->deposit_libraries->is_condition_step($value['date_start'], $date_end, $type_id)){
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
					
					//ส่วนที่ไว้เช็คเงินฝากออมทรัพย์พิเศษเกษียณเพิ่มสุข 12 เดือน
					$interest = round(((($transaction_balance*@$interest_rate)/100)*$date_count)/$day_of_year,2);
					
					$data[$i]['account_id'] = $value['account_id'];
					$data[$i]['type_id'] = $type_id;
					$data[$i]['transaction_list'] = $value['transaction_list'];
					$data[$i]['transaction_deposit'] = number_format($value['transaction_deposit'],2,'.','');
					$data[$i]['transaction_balance'] = number_format($value['transaction_balance'],2,'.','');
					$data[$i]['date_start'] = $value['date_start'];
					$data[$i]['date_end'] = $date_end;
					$data[$i]['date_count'] = $date_count;
					$data[$i]['interest'] = $interest;
					$data[$i]['interest_rate'] = $interest_rate;
					$data[$i]['transaction_id'] = $value['transaction_id'];
							
					//echo 'i='.$i.'<br>';
					//echo 'date_start='.$value['date_start'].'<br>';
					//echo 'date_end='.$date_end.'<br>';
					//echo 'round(((('.$transaction_balance.'*'.@$interest_rate.')/100)*'.$date_count.')/'.$day_of_year.',2)<br>';
					
					//echo 'interest='.$interest.'<br>';
					//echo '<hr>';
					
					$all_interest += $interest;
					$i++;
				}
				
			}
			//echo '<pre>'; print_r($data); echo '</pre>'; exit;
			return $data;
	}
	
	//เงินฝากประจำเพื่อสร้างอนาคต ยกเว้นภาษี 24 เดือน
	public function get_data_account_n24($account_id,$date_interest,$transaction_id){
		$data = array();
		$transaction = array();
		//$account_id = '0013000328';
		//$date_end = '2020-12-31';
		//$date_end = $date_interest;
		//$chk_year = date("Y", strtotime($date_end));
		
		$date_bf_year = date('Y-01-01', strtotime($date_interest));
		//echo $date_bf_year.'<br>';
		
		$chk_year = date("Y", strtotime($date_interest));
		$chk_year_last = date("Y", strtotime($date_interest))-1;
		$type_id = '13';
		
		$this->db->select(array('t1.*' ,'t2.main_type_id'));
		$this->db->from('coop_deposit_type_setting_detail AS t1');
		$this->db->join("coop_deposit_type_setting AS t2","t1.type_id = t2.type_id","inner");
		$this->db->where("t1.type_id = '".$type_id."' AND t1.start_date <= '".$date_interest."'");
		$this->db->order_by("t1.start_date DESC");
		$this->db->limit(1);
		$row_detail = $this->db->get()->row_array();		
		
		$main_type_id = $row_detail['main_type_id'];

		$num_month_maturity = (int)$row_detail['num_month_maturity'];	
		
		$chk_date_ext_maturity = $this->deposit_libraries->get_date_last_account($account_id,$num_month_maturity,$row_detail['ext_num_month_maturity_day']);
		if($chk_date_ext_maturity != ''){
			$date_ext_maturity = $chk_date_ext_maturity;
			$date_ext = date('Y-m-d', strtotime("+".$row_detail['ext_num_month_maturity_day']." day", strtotime($chk_date_ext_maturity)));						
		}else{
			$date_ext_maturity = $date_interest;
			$date_ext = $date_interest;
		}
		
		//เงินฝากประจำเพื่อสร้างอนาคต ยกเว้นภาษี 24 เดือน
		if($row_detail['main_type_id'] == 5){
			$date_interest = ($date_interest > $date_ext)?$date_ext:$date_interest;
		}
		
		//หารายการสุดท้ายของปีก่อนหน้า
		$row_last = array();
		$sql_last = "SELECT
					t1.account_id,
					DATE( t1.transaction_time ) AS transaction_time,
					DATE( t1.transaction_time ) AS date_start,
					t1.transaction_id,
					t1.transaction_deposit,
					t1.transaction_balance,
					'BF' AS transaction_list
				FROM
					 coop_account_transaction AS t1
				WHERE
					t1.account_id = '{$account_id}' 
					AND YEAR ( t1.transaction_time ) = '{$chk_year_last}'
					AND transaction_list IN ('OPN', 'OCA', 'OPT', 'TRB', 'XD', 'DEP', 'DCA', 'DFX', 'DEPP', 'CD','DOT')
				ORDER BY
					t1.transaction_time DESC LIMIT 1";
		$rs_last = $this->db->query($sql_last);
		$row_last = $rs_last->result_array();
		
		//echo $sql_last.'<br>';
		//echo '<pre>'; print_r($row_last); echo '</pre>';
		
		
		//เงินฝากประจำเพื่อสร้างอนาคต ยกเว้นภาษี 24 เดือน
		$where = "";
		if(@$transaction_id != ''){
			$where = " AND t1.transaction_id = '{$transaction_id}'";
		}
		$sql = "SELECT
					t1.account_id,
					DATE( t1.transaction_time ) AS transaction_time,
					DATE( t1.transaction_time ) AS date_start,
					t1.transaction_id,
					t1.transaction_deposit,
					t1.transaction_balance,
					t1.transaction_list
				FROM
					 coop_account_transaction AS t1
				WHERE
					t1.account_id = '{$account_id}' 
					AND YEAR ( t1.transaction_time ) = '{$chk_year}'
					AND transaction_list IN ('OPN', 'OCA', 'OPT', 'TRB', 'XD', 'DEP', 'DCA', 'DFX', 'DEPP', 'CD','DOT')
					{$where}
				ORDER BY
					t1.transaction_time ASC";
		$rs = $this->db->query($sql);
		//$row = $rs->result_array();
		$transaction = $rs->result_array();
		//echo $sql.'<br>';
		//echo '<pre>'; print_r($row); echo '</pre>'; exit;
		//echo '<pre>'; print_r($row_detail); echo '</pre>';
		
		$transaction = array_merge($transaction,$row_last);
				array_multisort( array_column($transaction, "date_start"), SORT_ASC,$transaction );
		$i=0;
		if(!empty($transaction)){
			foreach($transaction as $key => $value){
				$transaction_new[$i] = $value;			
				if($key === (sizeof($transaction)-1)){
					$transaction_new[$i]['date_end'] = $date_interest;
				}else{
					$transaction_new[$i]['date_end'] = $transaction[$key + 1]['date_start'];
				}
				$i++;
			}
		}
		$transaction = $transaction_new;
		//echo '<pre>'; print_r($transaction); echo '</pre>'; //exit;
			
		$all_interest = 0;
		$i=0;
		if(!empty($transaction)){
			foreach($transaction AS $key=>$value){
				$arr_chk_fee = array();
				if($value['transaction_list'] == 'BF'){
					$value['date_start'] = $date_bf_year;
				}
				$array_check_date = $this->deposit_libraries->check_interest_two_step($value['date_start'], $value['date_end'], $type_id, $value['transaction_list'],$arr_chk_fee,$main_type_id);
				//echo '<pre>'; print_r($array_check_date); echo '</pre>';
				//$all_interest = 0;
				if(!empty($transaction)){
					foreach($array_check_date AS $key_chk=>$val_chk){
						$date_count = @$val_chk['date_count'];
						//เคส นับต้น ไม่นับปลาย
						//if($key_chk == 0){
							//บวก 1 วัน  รายการแรก
							//$date_count = @$val_chk['date_count']+1;
						//}
						
						$day_of_year = $val_chk['day_of_year'];
						$transaction_balance = $value['balance_deposit'];
						if(is_array($val_chk['interest_rate']) && $this->deposit_libraries->is_condition_step($value['date_start'], $value['date_end'], $type_id)){
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
						
						$interest = round(((($value['transaction_balance'] * @$val_chk['interest_rate']) / 100) * $date_count) / $day_of_year,2);
						
						$data[$i]['account_id'] = $value['account_id'];
						$data[$i]['type_id'] = $type_id;
						$data[$i]['transaction_list'] = $value['transaction_list'];
						$data[$i]['transaction_deposit'] = number_format($value['transaction_deposit'],2,'.','');
						$data[$i]['transaction_balance'] = number_format($value['transaction_balance'],2,'.','');
						$data[$i]['date_start'] = $value['date_start'];
						$data[$i]['date_end'] = $value['date_end'];
						$data[$i]['date_count'] = $date_count;
						$data[$i]['interest'] = $interest;
						$data[$i]['interest_rate'] = $interest_rate;
						$data[$i]['transaction_id'] = $value['transaction_id'];
								
						//echo 'i='.$i.'<br>';
						//echo 'date_start='.$value['date_start'].'<br>';
						//echo 'date_end='.$date_end.'<br>';
						//echo '((('.$value['transaction_balance'].'*'.@$val_chk['interest_rate'].')/100)*'.$date_count.')/'.$day_of_year.'<br>';					
						//echo 'interest='.$interest.'<br>';
						//echo '<hr>';
						
						$all_interest += $interest;
						$i++;
					}
				}				
			}
		}
		//echo '<pre>'; print_r($data); echo '</pre>'; exit;
		return $data;
	}
	
	//หาวันที่ครบกำหนด กรณีถอนออกไม่หมดเมื่อครบกำหนด
	public function get_date_transaction($account_id = ''){
		$arr_data = array();
		$where = '';
		if($account_id != ''){
			$where = " AND t1.account_id = '{$account_id}'";
		}
		
		$row = $this->db->select("t1.transaction_id,t1.account_id,t1.c_num
									,IF(t2.transaction_time != '',t2.transaction_time,t1.transaction_time) AS date_due")
					->from("coop_account_transaction_view AS t1")
					->join("coop_account_transaction AS t2","t1.account_id = t2.account_id 
							 AND t1.ref_account_no = t2.ref_account_no 
							 AND t2.transaction_list = 'DFX' AND YEAR(t1.transaction_time) = YEAR(t2.transaction_time)","left")
					->where("t1.c_num = '2' {$where}")
					->get()->result_array();
		//echo $this->db->last_query();			
		//echo '<pre>'; print_r($row); echo '</pre>';
		$chk_account_id = '';
		if(!empty($row)){			
			foreach($row AS $key=>$val){
				$transaction_id_n = $val['transaction_id'].'_'.$val['c_num'];
				$arr_data[$val['account_id']][$transaction_id_n] = $val['date_due'];
			}	
		}
		//echo '<pre>'; print_r($arr_data); echo '</pre>'; exit;
		return $arr_data;
	}

	public function get_code_type($money_type){
		$arr_data = array();		
		$row = $this->db->select("money_type_name_short")
					->from("coop_money_type")
					->where("money_type = '{$money_type}'")
					->get()->result_array();
		if(!empty($row)){
			$arr_data = array_column($row,'money_type_name_short');
		}					
		return $arr_data;
	}

	public function get_date_transaction_in_id($account_id,$transaction_id,$c_num){
		$arr_data = array();
		$date_due = '';
		$where = '';
		if($account_id != ''){
			$where = " AND t1.account_id = '{$account_id}'";
		}
		
		$sql = "SELECT 
				t1.transaction_id,
				t1.account_id,
				t1.c_num,
				IF( t2.transaction_time != '', t2.transaction_time, t1.transaction_time ) AS date_due 
			FROM (
			SELECT
				t1.account_id AS account_id,
				t1.transaction_time AS transaction_time,
				t1.transaction_list AS transaction_list,
				t1.transaction_withdrawal AS transaction_withdrawal,
				t1.transaction_deposit AS transaction_deposit,
				t1.interest AS interest,
				t1.transaction_balance AS transaction_balance,
				t1.transaction_id AS transaction_id,
				t1.balance_deposit AS balance_deposit,
				t1.ref_account_no AS ref_account_no,
				t1.user_id AS user_id,
				t1.seq_no AS seq_no,
				t1.seq_chk AS seq_chk,
				'1' AS c_num 
			FROM
				coop_account_transaction t1 UNION ALL
			SELECT
				t1.account_id AS account_id,
				t1.transaction_time AS transaction_time,
				'DFX' AS transaction_list,
				'0' AS transaction_withdrawal,
				t1.balance_deposit AS transaction_deposit,
				t1.interest AS interest,
				t1.transaction_balance AS transaction_balance,
				t1.transaction_id AS transaction_id,
				t1.balance_deposit AS balance_deposit,
				t1.ref_account_no AS ref_account_no,
				t1.user_id AS user_id,
				t1.seq_no + t1.seq_chk AS seq_no,
				t1.seq_chk AS seq_chk,
				'2' AS c_num 
			FROM
				coop_account_transaction t1 
			WHERE
				t1.transaction_list IN ( 'WCA', 'WCT' ) 
				AND t1.balance_deposit > 0 
			ORDER BY
				account_id,
				transaction_time,
				transaction_id
			) AS t1
			LEFT JOIN coop_account_transaction AS t2 ON t1.account_id = t2.account_id 
				AND t1.ref_account_no = t2.ref_account_no 
				AND t2.transaction_list = 'DFX' 
				AND YEAR ( t1.transaction_time ) = YEAR ( t2.transaction_time ) 
			WHERE t1.account_id = '{$account_id}' AND t1.c_num = '2'";
		$rs = $this->db->query($sql);
		$row = $rs->result_array();
		
		$chk_account_id = '';
		if(!empty($row)){			
			foreach($row AS $key=>$val){
				$transaction_id_n = $val['transaction_id'].'_'.$val['c_num'];
				$arr_data[$val['account_id']][$transaction_id_n] = $val['date_due'];
			}	
		}	
		
		$date_due = $arr_data[$account_id][$transaction_id.'_'.$c_num];
		
		return $date_due;
	}
}
