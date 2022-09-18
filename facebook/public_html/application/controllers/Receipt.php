<?php


class Receipt extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();
	}

	public function run_script(){
		$this->receipt_model->fixReceipt();
	}
	
	//ใบเสร็จคืนเงิน
	public function receipt_refund($receipt_refund_id){
		$arr_data = array();
		
		$arr_data['row'] = $this->db->select('receipt_refund_id,
							member_id,
							sumcount,
							receipt_datetime,
							pay_type,
							pay_for ')
							->from('coop_receipt_refund')
							->where("receipt_refund_id = '{$receipt_refund_id}'")
							->get()->row_array();

		
		$arr_data['rs_detail'] = $this->db->select('receipt_detail_id,
											account_list_id,
											principal_payment,
											interest_payment,
											total_amount,
											payment_date,
											pay_description')
										->from('coop_receipt_detail_refund')
										->where("receipt_refund_id = '{$receipt_refund_id}'")
										->get()->result_array();
		
		//ลายเซ็นต์
		$date_signature = date("Y-m-d", strtotime($arr_data['row']['receipt_datetime']));
		$arr_data['signature'] = $this->db->select(array('*'))
										->from('coop_signature')
										->where("start_date <= '{$date_signature}'")
										->order_by('start_date DESC')
										->limit(1)->get()->row_array();								
		$this->load->view('receipt/receipt_refund',$arr_data);
	}
}
