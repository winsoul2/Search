<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Report_loan_atm extends CI_Controller {
	function __construct()
	{
		parent::__construct();
	}
	
	public function atm_detail(){							
		$this->libraries->template('report_loan_atm/atm_detail',$arr_data);
	}
	
	public function atm_detail_report(){
		//$account_id = @$_GET['account_id'];
		$member_id = (@$_GET['member_id'] != '')?sprintf("%06d",@$_GET['member_id']):'';
		$date_start = @$this->center_function->ConvertToSQLDate(@$_GET['date_start']).' 00:00:00';
		$date_end = @$this->center_function->ConvertToSQLDate(@$_GET['date_end']).' 23:59:59';
		$where = "";
		if($date_start != '' && $date_end != ''){
			$where .= " AND t1.createdatetime BETWEEN '{$date_start}' AND '{$date_end}'";
		}
		
		if($member_id != ''){
			$where .= " AND t2.mem_id = '{$member_id}'";
		}
		
		$arr_data = array();
		$account_id = @$_GET['account_id'];
		$response_atm =$this->db->select('t1.responseCode,
											t1.responseDesc,
											t1.messageType,
											t1.tranType,
											t1.fromAcctType,
											t1.toAcctType,
											t1.termType,
											t1.transactionDate,
											t1.transactionTime,
											t1.transactionAmount,
											t1.fromAcctNo,
											t1.createdatetime,
											t2.mem_id,
											t2.account_id
											')
		->from('message_response_atm AS t1')
		->join("coop_maco_account AS t2 ","t1.fromAcctNo = t2.account_id","inner")
		->where("1=1 AND t2.type_id = '2' {$where}")
		->order_by('t1.createdatetime ASC')
		->get()->result_array();
		//echo $this->db->last_query();exit;
		//echo '<pre>'; print_r($response_atm); echo '</pre>';
		$arr_data['row'] = $response_atm;
		
		$arr_data['type_list'] = array('0' => 'ผูกบัตร',
										'30' => 'สอบถามยอด',
										'10' => 'ถอนเงิน',
										'40' => 'โอนเงินไปยังบัญชีภายในบัตร',
										'31' => 'สอบถามยอด',
										'41' => 'โอนเงินไปยังบัญชีบุคคลอื่นภายในสหกรณ์');							
		$this->preview_libraries->template_preview('report_loan_atm/atm_detail_report',$arr_data);
	}
}
