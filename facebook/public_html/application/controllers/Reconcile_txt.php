<?php
/*
	http://61.91.58.222/ws/service.php

	inquiryBalance
	transferWithInCardTxn
	withdrawalTxn
	transferWithInCoopTxn
	inquiryCustomerDetail
	transferWithInCoopInquiry
*/
defined('BASEPATH') OR exit('No direct script access allowed');

class Reconcile_txt extends CI_Controller {
	public $authConfig = array(
		"key" => "KJAUUMJZYRHL5TDX",
		"secret" => "srF4J^+cJ6e9YFV9tt#hrR^ufKENbCVh"
	);
	function __construct()
	{
		parent::__construct();
		$this->load->helper('url');
        $this->load->helper('file');
	}
	public function gen_file()
	{
		set_time_limit (1000);	
		$authConfig = $this->authConfig;

		//$date_gen = "20190430";
		$date_gen = date('Ymd',strtotime(date('Ymd') . "-1 days")); //สำหรับ run ตอนเที่ยงคืน ต้องเอาวันปัจจุบัน -1 วัน
		//$date_gen = date('Ymd');
		
		$name_file = "SPKC_".$date_gen.".txt";
		$coop_fiid = "SPKC";
		$path_file = 'assets/reconcile/'.$name_file;
		$path_file_full = FCPATH.$path_file;

		$this->db->select(array('t1.*','t2.channelID','t2.messageType'));
		$this->db->from("message_response_atm AS t1");
		$this->db->join("message_request_atm AS t2","t1.traceNo = t2.traceNo","inner");
		$this->db->where("t1.transactionDate = '".$date_gen."'");
		$this->db->order_by('t1.responseID ASC');
		$rs = $this->db->get()->result_array();

		$tab1 = ' ';
		$tab3 = '   ';
		$tab8 = '        ';
		$tab24 = '                        ';
		$tab167 = '                                                                                                                                                                       ';

		$data = '';
		$data .= '0'.$coop_fiid.$date_gen .$tab167;
		$data .= "\n";
		$records = 0;
		$detail_records = 0;
		if(!empty($rs)){
			foreach($rs as $key => $row){
				//echo '<pre>'; print_r($row); echo '</pre>';
				//***channeIID ถ้าเป็น Reserve จะใช้ เป็น(5-9) 
				if((@$row['messageType'] == '0400' || @$row['messageType'] == '0420')){
					$channel = '5';
				}else{
					if(@$row['channelID'] == ''){
						$channel = '2';
					}else{
						$channel = (int)@$row['channelID'];
					}
				}
				
				$fromAcctNo = @$row['fromAcctNo'].$tab8;
				if(@$row['toAcctNo'] == ''){
					$toAcctNo = '0000000000000000000';
				}else{
					$len_default = 19;
					$len_to = strlen($row['toAcctNo']);
					$len_diff = $len_default-$len_to;
					$tab_n = '';
					for($i=0;$i<$len_diff;$i++){
						$tab_n .= $tab1;
					}
					$toAcctNo = $row['toAcctNo'].$tab_n;
				}
				//echo $toAcctNo .'<hr>';
				//$toAcctNo = (@$row['toAcctNo'] == '')?'0000000000000000000':$row['toAcctNo'].$tab8;
				
				//reverse_flag = 50=รายการ revers Reverse,00=รายการปกติ				
				$reverse_flag = (@$row['messageType'] == '0400' || @$row['messageType'] == '0420')?'50':'00';													
				
				$term_id_len_default = 16;
				$term_id_len_to = strlen($row['termID']);
				$term_id_len_diff = $term_id_len_default-$term_id_len_to;
				$term_id_tab_n = '';
				for($i=0;$i<$term_id_len_diff;$i++){
					$term_id_tab_n .= $tab1;
				}
				$term_id = @$row['termID'].@$term_id_tab_n;
				
				$amount_len_default = 10;
				$amount_len_to = strlen($row['transactionAmount']);
				$amount_len_diff = $amount_len_default-$amount_len_to;
				$amount_zero = '';
				$text_zero = '0';
				for($i=0;$i<$amount_len_diff;$i++){
					$amount_zero .= $text_zero;
				}
				$transaction_amount = @$amount_zero.@$row['transactionAmount'];
				
				
				$data .= '1'.@$row['termFIID'].@$channel.@$row['cardNo'].'0000'.$tab3.$tab3;
				//$data .= @$term_id.@$row['termSeqID'].$tab3.$tab3;
				$data .= @$term_id.@$row['termSeqID'];
				$data .= @$row['transactionDate'].$row['transactionTime'].@$row['tranType'].@$row['fromAcctType'].@$row['toAcctType'].@$row['postDate'].$reverse_flag;
				$data .= @$fromAcctNo.@$toAcctNo.$tab3;
				$data .= @$transaction_amount.@$row['feeAmount1'].@$row['commAmount1'].@$row['feeAmount2'].$tab24;
				$data .= "\n";	
				$records++;	
			}
		}
		$detail_records = sprintf("%08d", @$records+2);
		$data .= '9'.@$coop_fiid.@$detail_records.@$tab167;
		$data .= "\n";

		//echo $data."<hr>";
		//exit;
		if(write_file($path_file_full, $data) == FALSE)
		{
		   echo '';

		} else {
			echo $path_file;                          
		}

		exit;
		
	}	
}
