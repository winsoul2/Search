<?php if( ! defined('BASEPATH')) exit('No direct script access allowed');

class Report_payment_deposit_model extends CI_Model {
    public function __construct()
    {
        parent::__construct();
		$this->load->model("Report_accrued_interest_model", "report_accrued");
    }

	public function get_coop_account_transaction($start_date,$end_date,$type_id){
        $arr_data = array();
		$code_th = $this->get_code_th();
		$code_th['BF'] = 'ยกมา';
		$code_th['TF'] = 'ยกไป';
		$arr_run_row = $this->get_run_row_transaction();
		$code_type_int = $this->get_code_type('I'); //code ดอกเบี้ย	
		
		/*$sql = "SELECT 
			t2.account_name,
			t1.account_id,
			t1.transaction_id,
			t1.transaction_time,
			t1.transaction_list,
			t1.transaction_list AS transaction_code,
			t1.transaction_withdrawal,
			t1.transaction_deposit,
			t1.interest,
			t1.transaction_balance,
			t1.seq_no,
			t1.c_num
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
			)AS t1
			INNER JOIN coop_maco_account AS t2 ON t1.account_id = t2.account_id 
			WHERE
				t1.transaction_time BETWEEN '{$start_date}' AND '{$end_date}' 
				AND t2.type_id = '{$type_id}'
			ORDER BY  t1.account_id ASC, t1.transaction_time ASC, t1.transaction_id ASC, t1.c_num ASC	 
			";
		$rs = $this->db->query($sql);
		$row_detail = $rs->result_array();	
		*/
		$row_detail = $this->db->select("t1.account_id,
                                            t2.account_name,							
											t1.transaction_id,
											t1.transaction_time,
											t1.transaction_list,
											t1.transaction_list AS transaction_code,
											t1.transaction_withdrawal,
											t1.transaction_deposit,
											t1.interest,
											t1.transaction_balance,
											t1.seq_no
											")
					->from('coop_account_transaction AS t1')
					->join("coop_maco_account AS t2","t1.account_id = t2.account_id","inner")
					->where("t1.transaction_time BETWEEN '{$start_date}' AND '{$end_date}' AND t2.type_id = '{$type_id}'")
					->order_by("DATE(t1.transaction_time) ASC,t1.account_id ASC,t1.seq_no ASC")
					//->order_by("t1.transaction_time ASC,t1.transaction_id ASC")
					->get()->result_array();					
		//echo $this->db->last_query();		
		$row_head = $this->db->select("type_name")
		->from("coop_deposit_type_setting")
		->where("type_id = '{$type_id}'")
		->order_by("type_seq")
		->get()->result_array();

		$i=0;
		$flag = 0;
		$temp_data = array();
		
		if(!empty($row_detail)){
			foreach($row_detail AS $key_detail=>$val_detail){
				if(@$row_detail[$key_detail + 1]['transaction_list'] == 'WCHE'){	
					@$temp_data['transaction_withdrawal'] = @$row_detail[$key_detail + 1]['transaction_withdrawal'] - $val_detail['transaction_deposit'];
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

				$transaction_deposit = $val_detail['transaction_deposit'];
				$transaction_withdrawal = $val_detail['transaction_withdrawal'];
				$transaction_times = $val_detail['transaction_time'];
				$transaction_times = date('d-m-Y', strtotime($val_detail['transaction_time'])); 
				//$transaction_times = date('d-m-Y', strtotime("+543 year", strtotime($val_detail['transaction_time']))); 
				if($val_detail['c_num'] == '2'){
					$transaction_time = $this->report_accrued->get_date_transaction_in_id($val_detail['account_id'],$val_detail['transaction_id'],$val_detail['c_num']);
				}else{
					$transaction_time = $val_detail['transaction_time'];
				}
				//$transaction_time = $val_detail['transaction_time'];
				//$transaction_no = sprintf("%010d",$arr_run_row[$val_detail['account_id']][$val_detail['transaction_id']]);
				$seq_no = $val_detail['seq_no'];
				$arr_data[$transaction_times]['row_head']['type_name'] = $row_head[0]['type_name'];	
				$arr_data[$transaction_times]['row_head']['transaction_time'] = $transaction_time ;		
				$arr_data[$transaction_times]['row_detail'][$i] = $val_detail;        
				$arr_data[$transaction_times]['row_detail'][$i]['transaction_time'] = $transaction_time;						
				//$arr_data[$transaction_times]['row_detail'][$i]['transaction_no'] = $transaction_no;
				$arr_data[$transaction_times]['row_detail'][$i]['seq_no'] = $seq_no;
				$arr_data[$transaction_times]['row_detail'][$i]['transaction_list'] = $code_th[$val_detail['transaction_list']];
				$arr_data[$transaction_times]['row_detail'][$i]['transaction_withdrawal'] = $transaction_withdrawal;

				if(in_array($val_detail['transaction_list'],$code_type_int)){
					$arr_data[$transaction_times]['row_detail'][$i]['interest'] = $transaction_deposit;
					$arr_data[$transaction_times]['row_detail'][$i]['transaction_deposit'] = '';
				}else{
					$arr_data[$transaction_times]['row_detail'][$i]['interest'] = (@$val_detail['interest'] != '')?$val_detail['interest']:'';
					$arr_data[$transaction_times]['row_detail'][$i]['transaction_deposit'] = $transaction_deposit;
				}

				$i++;
			}	
		}
		//echo '<pre>'; print_r($arr_data); echo '</pre>';exit;
		return $arr_data;	
	}
	
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

    public function get_code_th(){
		$arr_data = array();		
		$row = $this->db->select("money_type_name_short,IF(money_type_name_th_short <> '' AND money_type_name_th_short IS NOT NULL ,money_type_name_th_short,money_type_name_short )AS money_type_name_th_short")
					->from("coop_money_type")
					->get()->result_array();
		if(!empty($row)){
			$arr_data = array_column($row,'money_type_name_th_short', 'money_type_name_short');
		}					
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

	public function get_interest_close($account_id,$transaction_time){
		$interest_close = 0;
		$row = $this->db->select("transaction_deposit AS interest_close")
					->from("coop_account_transaction")
					->where("account_id = '{$account_id}' AND transaction_time = '{$transaction_time}' AND transaction_list = 'INC'")
					->get()->row_array();
		if(!empty($row)){
			$interest_close = $row['interest_close'];
		}					
		return $interest_close;
	}
}
