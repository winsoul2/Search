<?php


class Deposit_model extends CI_Model
{

	public function __construct()
	{
		parent::__construct();
	}

	public function get_withdrawal_guarantee_loan($account_id,$transaction_balance){
		$arr_data = array();
		//เช็คยอดเงินบัญชีค้ำประกัน  คำนวณจาก ยอดเงินกู้คงเหลือ - ทุนเรือนหุ้นทั้งหมด = เงินฝากที่ถอนไม่ได้
		$row_loan_guarantee = $this->db->select('t2.loan_status,t2.member_id,SUM(t2.loan_amount_balance) AS loan_amount_balance')
							->from("coop_loan_guarantee_saving AS t1")
							->join("coop_loan AS t2", "t1.loan_id = t2.id", "inner")
							->where("t1.account_id = '".$account_id."'")->get()->row_array();
		$arr_data['is_guarantee_loan'] = (@$row_loan_guarantee['loan_status']=="1") ? true : false;
		$guarantee_loan_amount_balance = @$row_loan_guarantee['loan_amount_balance'];//ยอดเงินกู้คงเหลือของเงินฝากที่มีบัญชีค้ำประกัน 		
		$arr_data['guarantee_loan_amount_balance'] = @$guarantee_loan_amount_balance;//ยอดเงินกู้คงเหลือของเงินฝากที่มีบัญชีค้ำประกัน 		
		
		$member_id = @$row_loan_guarantee['member_id'];
		$cal_share  = 0;
		if($member_id != ''){
		$row_prev_share = $this->db->select('share_collect_value')
									->from('coop_mem_share')
									->where("member_id = '".$member_id."' AND share_status IN('1','2','5')")
									->order_by('share_date DESC, share_id DESC')
									->limit(1)
									->get()->row_array();
			$cal_share = $row_prev_share['share_collect_value'];	//ทุนเรือนหุ้นสะสมปัจจุบัน
			$arr_data['cal_share'] = $cal_share;	//ทุนเรือนหุ้นสะสมปัจจุบัน
		}
		$withdrawal_guarantee_loan = @$guarantee_loan_amount_balance-@$cal_share;
		$arr_data['withdrawal_guarantee_loan'] = ($withdrawal_guarantee_loan > 0)?$withdrawal_guarantee_loan:0;
		$arr_data['guarantee_balance'] = number_format(@$transaction_balance - @$withdrawal_guarantee_loan,2,".","");
		return $arr_data;
	}
	
	public function get_member_id($account_id){
		$member_id = '';
		$member_id = $this->db->select("mem_id AS member_id")->from("coop_maco_account")
			->where("account_id = '{$account_id}'")
			->get()->row_array()["member_id"];		
		return $member_id;
	}	
	
	public function get_time_transaction($account_id,$date_transaction){        
		$data = $this->db->select('DATE(transaction_time) AS date_last,TIME(transaction_time) AS time_last')
			->from('coop_account_transaction')
			->where("account_id = '{$account_id}' AND DATE(transaction_time) = '{$date_transaction}'")
			->order_by('transaction_time DESC, transaction_id DESC')
			->limit(1)->get()->row_array();
		//echo $this->db->last_query();	
        return $data;
    }
	
	public function get_account_member_name($account_id){
		$member_name = '';
		$member_name = $this->db->select("member_name")->from("coop_maco_account")
			->where("account_id = '{$account_id}'")
			->get()->row_array()["member_name"];		
		return $member_name;
	}
}
