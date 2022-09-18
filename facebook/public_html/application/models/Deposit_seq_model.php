<?php if( ! defined('BASEPATH')) exit('No direct script access allowed');

class Deposit_seq_model extends CI_Model {
	public function __construct()
	{
		parent::__construct();
    }
	
	//อัพเดทลำดับรายการ ของรายการถัดไป
	public function update_seq_account_transaction($account_id, $transaction_time, $seq_no, $transaction_id){
		$row = $this->db->select('transaction_id,seq_chk,seq_no')->from('coop_account_transaction')
						->where("transaction_time >= '{$transaction_time}' AND account_id = '{$account_id}'  AND transaction_id >= '{$transaction_id}'")
						->order_by('transaction_time ASC,transaction_id ASC')->get()->result_array();
		//echo $this->db->last_query();
		$seq_no_next = $seq_no;
		$chk_wd_balance = 0;
        if(!empty($row)) {
            foreach ($row as $key => $value) {
				$chk_wd_balance = $this->chk_wd_balance_deposit($value['transaction_id']);
				if($chk_wd_balance == 1){
					$this->db->set("seq_chk", 1);
				}
				
				$this->db->set("seq_no", $seq_no_next);
				$this->db->where("transaction_id", $value['transaction_id']);
				$this->db->update("coop_account_transaction");
				
				$seq_no_next++;
				if($chk_wd_balance == 1){
					//บวกเพิ่ม
					$seq_no_next++;
				}
            }
        }
	}
	
	//สร้างลำดับรายการ ของรายการถัดไป
	public function gen_seq_account_transaction($data){
		$row = $this->db->select('seq_no')->from('coop_account_transaction')->where("account_id = '{$data['account_id']}'")
					->order_by('transaction_time DESC , transaction_id DESC')->limit(1)->get()->row_array();
		//echo $this->db->last_query();
		$seq_no_next = (empty($row['seq_no']))?1:$row['seq_no']+1;
		$chk_wd_last = $this->chk_wd_balance_deposit_last($data['account_id']);
		// if($data['transaction_list'] == 'WCA' && $data['balance_deposit'] > 0){
		// 	$seq_no_next++;
		// }else 
		if($chk_wd_last == 1){
			$seq_no_next++;
		}
		return $seq_no_next;
	}

	//เช็ค ถอนเงินไม่หมดตามยอดฝากกรณี ตั้งค่าถอนเงินแบบระบุยอดถอนเงินตามยอดฝาก
	public function chk_wd_balance_deposit($transaction_id){
		$row = $this->db->select('balance_deposit,seq_chk,seq_no')->from('coop_account_transaction')
					->where("transaction_list = 'WCA' 
					AND balance_deposit > 0 
					AND transaction_id = '{$transaction_id}'")
					->limit(1)->get()->row_array();
		$result = (!empty($row))?'1':'0';
		return $result;
	}

	//เช็ค ถอนเงินไม่หมดตามยอดฝากกรณี ตั้งค่าถอนเงินแบบระบุยอดถอนเงินตามยอดฝาก ของรายการล่าสุด
	public function chk_wd_balance_deposit_last($account_id){
		$row = $this->db->select('balance_deposit,seq_chk,seq_no')->from('coop_account_transaction')
					->where("account_id = '{$account_id}'")
					->order_by('transaction_time DESC,transaction_id DESC')
					->limit(1)->get()->row_array();
		$result = (@$row['seq_chk'] == '1')?'1':'0';
		return $result;
	}
}
