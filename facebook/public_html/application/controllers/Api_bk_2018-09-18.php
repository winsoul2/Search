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

class Api extends CI_Controller {
	public $authConfig = array(
		"key" => "KJAUUMJZYRHL5TDX",
		"secret" => "srF4J^+cJ6e9YFV9tt#hrR^ufKENbCVh"
	);
	function __construct()
	{
		parent::__construct();
	}
	public function atm_request()
	{
		$authConfig = $this->authConfig;
		//echo"<pre>";print_r($authConfig);exit;		
		if(@$_POST["_key"] != $authConfig["key"] || @$_POST["_secret"] != $authConfig["secret"]) {
			echo json_encode("Access denied.");
			exit;
		}	
		
		if(@$_POST['tranType'] == '30'){		
			$res = $this->balance_inquiry(@$_POST);
		}else if(@$_POST['tranType'] == '10'){			
			$res = $this->withdrawlTxn(@$_POST);
		}else if(@$_POST['tranType'] == '40'){
			$res = $this->transfer_withln_card_txn(@$_POST);
		}else if(@$_POST['tranType'] == '31'){
			$res = $this->transfer_withln_coop_inquiry(@$_POST);
		}else if(@$_POST['tranType'] == '41'){
			$res = $this->transfer_withln_coop_txn(@$_POST);
		}else{
			$res = $this->customer_detail_inquiry(@$_POST);
		}
		echo $res;
		
		/*
		if($_POST["_do"] == "inquiryBalance") {
			// request
			$_POST['requesterSystem'];
			$_POST['messageType'];
			$_POST['tranType'];
			$_POST['fromAcctType'];
			$_POST['toAcctType'];
			$_POST['traceNo'];
			$_POST['termFIID'];
			$_POST['termID'];
			$_POST['termBranchID'];
			$_POST['termRegionID'];
			$_POST['termSeqID'];
			$_POST['termType'];
			$_POST['transactionDate'];
			$_POST['transactionTime'];
			$_POST['coopCode'];
			$_POST['fromAcctNo'];
			$_POST['cardFIID'];
			$_POST['cardNo'];
			$_POST['terminalUsageFlag'];
			$_POST['feeAmount1'];
			$_POST['commAmount1'];
			$_POST['feeAmount2'];
			$_POST['commAmount2'];
			$_POST['postDate'];
			
			if($_POST['requesterSystem'] == 'D1'){
				
			}
			
			// response
			$res = array(
				'responseCode' => '',
				'responseDesc' => '',
				'requesterSystem' => '',
				'messageType' => '',
				'coopReferenceNo' => '',
				'tranType' => '',
				'fromAcctType' => '',
				'toAcctType' => '',
				'traceNo' => '',
				'termFIID' => '',
				'termID' => '',
				'termBranchID' => '',
				'termRegionID' => '',
				'termSeqID' => '',
				'termType' => '',
				'transactionDate' => '',
				'transactionTime' => '',
				'coopCode' => '',
				'fromAcctNo' => '',
				'fromAcctBranch' => '',
				'cardFIID' => '',
				'cardNo' => '',
				'terminalUsageFlag' => '',
				'ledgerBalance' => '',
				'avaliableBalance' => '',
				'feeAmount1' => '',
				'commAmount1' => '',
				'feeAmount2' => '',
				'commAmount2' => '',
				'postDate' => ''
			);
			echo json_encode($res);
		}
		
		*/
		
	}
	
	//รองรับการสอบถามข้อมูลของลูกค้า
	public function customer_detail_inquiry($get_request_data)
	{			
		//บันทึก request
		$this->save_request($get_request_data);		
		$this->db->select(array('*'));
		$this->db->from('tran_type_atm');			
		$this->db->where("tran_type_code = '0'");			
		$rs_type = $this->db->get()->result_array();
		$row_type = @$rs_type[0];
		$tran_type_description = $row_type['tran_type_description'];
		
		$coop_reference_no = "1";//
		
		
		$this->db->select(array('coop_mem_apply.*',
								'coop_prename.prename_full',
								'coop_district.district_name',
								'coop_amphur.amphur_name',
								'coop_province.province_name'));
		$this->db->from('coop_mem_apply');			
		$this->db->join("coop_prename","coop_prename.prename_id = coop_mem_apply.prename_id","left");		
		$this->db->join('coop_district', 'coop_district.district_id = coop_mem_apply.c_district_id', 'left');
		$this->db->join('coop_amphur', 'coop_amphur.amphur_id = coop_mem_apply.c_amphur_id', 'left');
		$this->db->join('coop_province', 'coop_province.province_id = coop_mem_apply.c_province_id', 'left');	
		$this->db->where("id_card = '".@$get_request_data['cifID']."'");			
		$rs_member = $this->db->get()->result_array();
		$row_member = @$rs_member[0];
		
		$birthDate = str_replace("-","",@$row_member['birthday']);
		$address1 = "";
		$address2 = "";
		$address3 = "";
		$address4 = "";
		if(@$row_member['c_address_no']) {
			$address1 .= " บ้านเลขที่ ".@$row_member['c_address_no'];
		}
		if(@$row_member['c_address_moo']) {
			$address1 .= " หมู่ ".@$row_member['c_address_moo'];
		}
		if(@$row_member['c_address_village']) {
			$address1 .= " หมู่บ้าน ".@$row_member['c_address_village'];
		}
		if(@$row_member['c_address_road']) {
			$address2 .= " ถนน ".@$row_member['c_address_road'];
		}
		if(@$row_member['c_address_soi']) {
			$address2 .= " ซอย ".@$row_member['c_address_soi'];
		}
		if(@$row_member['c_district_id']) {
			$address3 .= " ต. ".@$row_member['district_name'];
		}
		if(@$row_member['c_amphur_id']) {
			$address3 .= " อ. ".@$row_member['amphur_name'];
		}
		if(@$row_member['c_province_id']) {
			$address4 .= " จ. ".@$row_member['province_name'];
		}
		if(@$row_member['c_zipcode']) {
			$address4 .= " รหัสไปรษณีย์ ".@$row_member['c_zipcode'];
		}
		
		$sex = @$row_member['sex'];
		$c_zipcode = @$row_member['c_zipcode'];
		$mobile = @$row_member['mobile'];
			
		$this->db->select(array('*'));
		$this->db->from('coop_maco_account');	
		$this->db->where("account_id = '".@$get_request_data['coopID']."' AND mem_id = '".@$row_member['member_id']."' AND account_status = '0'");	
		$this->db->limit(1);	
		$rs_account = $this->db->get()->result_array();
		$row_account = @$rs_account[0];
		
		$this->db->select(array('*'));
		$this->db->from('coop_loan_atm');	
		$this->db->where("account_id = '".@$get_request_data['coopID']."' AND member_id = '".@$row_member['member_id']."'  AND loan_atm_status ='1'  AND activate_status = '0'");	
		$this->db->limit(1);	
		$rs_loan_atm = $this->db->get()->result_array();
		$row_loan_atm = @$rs_loan_atm[0];
		
		//account_id เงินฝาก
		if(!empty($row_account)){
			$response_code = "000";
			$acctType = '11';
			$titleEngName = @$row_account['account_title_name_eng'];
			$engName = @$row_account['account_name_eng'];
			$titleThaiName = @$row_account['account_title_name'];
			$thaiName = @$row_account['account_name'];
		}else if(!empty($row_loan_atm)){
			//account_id เงินกู้
			$response_code = "000";
			$acctType = '01';
			$titleEngName = '';
			$engName = @$row_member['firstname_en'].'  '.@$row_member['lastname_en'];
			$titleThaiName = @$row_member['prename_full'];
			$thaiName = @$row_member['firstname_th'].'  '.@$row_member['lastname_th'];
		}else{
			$response_code = "052";
			$birthDate = "";
			$address1 = "";
			$address2 = "";
			$address3 = "";
			$address4 = "";
			$sex = "";	
			$c_zipcode = "";
			$mobile = "";		
			$acctType = "";
			$titleEngName = "";
			$engName = "";
			$titleThaiName = "";
			$thaiName = "";
		}
			
		
		// response
		$res = array(
			'responseCode'=>@$response_code,
			'responseDesc'=>@$tran_type_description,
			'coopReferenceNo'=>@$coop_reference_no,
			'requestReferenceNo'=>@$get_request_data['requestReferenceNo'],
			'responseAction'=>@$get_request_data['requestAction'],			
			'accountList'=>array(
				'acctNo'=>@$get_request_data['accountNo'],
				'acctType'=>@$acctType,
				'acctBranch'=>'0001',
			),
			'customerInfo'=>array(
				'titleEngName'=>@$titleEngName,
				'engName'=>@$engName,
				'titleThaiName'=>@$titleThaiName,
				'thaiName'=>@$thaiName,
				'birthDate'=>@$birthDate ,
				'customerType'=>'0',
				'cIFType'=>'I',
				'cIFNo'=>@$get_request_data['cifID'],
				'coopID'=>@$get_request_data['coopID'],
				'gender'=>@$sex,
				'address1'=>@$address1,
				'address2'=>@$address2,
				'address3'=>@$address3,
				'address4'=>@$address4,
				'zipCode'=>@$c_zipcode,
				'telNo1'=>@$mobile,
				'telNo2'=>'',
				'telNo3'=>'',
				'billAddress1'=>'',
				'billAddress2'=>'',
				'billAddress3'=>'',
				'billAddress4'=>'',
				'billZipCode'=>'',
			)
		);
		//echo '<pre>'; print_r($res); echo '</pre>';
		
		$data_response = array(
			'responseCode'=>@$response_code,
			'responseDesc'=>@$tran_type_description,
			'coopReferenceNo'=>@$coop_reference_no,
			'requestReferenceNo'=>@$get_request_data['requestReferenceNo'],
			'responseAction'=>@$get_request_data['requestAction'],
			'acctNo'=>@$get_request_data['accountNo'],
			'acctType'=>@$acctType,
			'acctBranch'=>'0001',
			'titleEngName'=>@$titleEngName,
			'engName'=>@$engName,
			'titleThaiName'=>@$titleThaiName,
			'thaiName'=>@$thaiName,
			'birthDate'=>@$birthDate ,
			'customerType'=>'0',
			'cIFType'=>'I',
			'cIFNo'=>@$get_request_data['cifID'],
			'coopID'=>@$get_request_data['coopID'],
			'gender'=>@$sex ,
			'address1'=>@$address1,
			'address2'=>@$address2,
			'address3'=>@$address3,
			'address4'=>@$address4,
			'zipCode'=>@$c_zipcode,
			'telNo1'=>@$mobile,
			'telNo2'=>'',
			'telNo3'=>'',
			'billAddress1'=>'',
			'billAddress2'=>'',
			'billAddress3'=>'',
			'billAddress4'=>'',
			'billZipCode'=>'',
		);
		//echo '<pre>'; print_r($data_response); echo '</pre>';
		//บันทึก response
		$save_response = $this->save_response($data_response);	
		return json_encode($res);
	}	
	
	//รองรับการสอบถามยอดเงินในบัญชี
	public function balance_inquiry($get_request_data)
	{
		//บันทึก request
		$this->save_request($get_request_data);		
		
		//ResponseDesc
		$this->db->select(array('*'));
		$this->db->from('tran_type_atm');			
		$this->db->where("tran_type_code = '".@$get_request_data['tranType']."'");			
		$rs_type = $this->db->get()->result_array();
		$row_type = @$rs_type[0];
		$tran_type_description = $row_type['tran_type_description'];
		
		//ledger_balance
		$ledger_balance_tmp = $this->ledger_balance(@$get_request_data['fromAcctNo'],@$get_request_data['cardNo'],@$get_request_data['fromAcctType']);
		$ledger_balance_tmp = number_format($ledger_balance_tmp, 2, '.', '');
		$arr_ledger_balance_tmp = explode('.',$ledger_balance_tmp);
		$ledger_balance = sprintf("%08d",$arr_ledger_balance_tmp[0].$arr_ledger_balance_tmp[1]);	
		
		//AvaliableBalance
		$avaliable_balance_tmp = $this->avaliable_balance(@$get_request_data['fromAcctNo'],@$get_request_data['cardNo'],@$get_request_data['fromAcctType']);
		$avaliable_balance_tmp = number_format($avaliable_balance_tmp, 2, '.', '');
		$arr_avaliable_balance_tmp = explode('.',$avaliable_balance_tmp);
		$avaliable_balance = sprintf("%08d",$arr_avaliable_balance_tmp[0].$arr_avaliable_balance_tmp[1]);
		
		$transactionYYmmdd = date('Y').$get_request_data['transactionDate'];
		$response_code = $this->response_code(@$get_request_data,$avaliable_balance,$transactionYYmmdd);
		$message_type = $this->message_type(@$get_request_data['tranType']);
		$coop_reference_no = "";//ส่วนนี้ได้มาจากไหน
		$from_acct_branch = ""; //ส่วนนี้ได้มาจากไหน
		
		$commAmount1_tmp = (@$get_request_data['commAmount1'] == '')?0.00:@$get_request_data['commAmount1'];//ค่าธรรมเนียมการทำรายการข้ามเขต
		$commAmount1_tmp = number_format(($commAmount1_tmp), 2, '.', ''); 
		$arr_commAmount1 = explode('.',$commAmount1_tmp);
		$commAmount1 = sprintf("%06d",$arr_commAmount1[0].$arr_commAmount1[1]);
		
		$feeAmount2_tmp = (@$get_request_data['feeAmount2'] == '')?0.00:@$get_request_data['feeAmount2']; //ค่าธรรมเนียมการทำรายการตู้ต่างสหกรณ์ หรือตู้ต่างธนาคาร หรือค่าธรรมเนียมทำรายการตู้ตัวเองเกินจำนวนครั้ง
		$feeAmount2_tmp = number_format(($feeAmount2_tmp), 2, '.', ''); 
		$arr_feeAmount2 = explode('.',$feeAmount2_tmp);
		$feeAmount2 = sprintf("%06d",$arr_feeAmount2[0].$arr_feeAmount2[1]);
		
		
		$commAmount2_tmp= (@$get_request_data['commAmount2'] == '')?0.00:@$get_request_data['commAmount2']; //ค่าธรรมเนียมการโอนเงิน
		$commAmount2_tmp = number_format(($commAmount2_tmp), 2, '.', ''); 
		$arr_commAmount2 = explode('.',$commAmount2_tmp);
		$commAmount2 = sprintf("%06d",$arr_commAmount2[0].$arr_commAmount2[1]);
		
		$feeAmount1_tmp = number_format((@$commAmount1_tmp+@$feeAmount2_tmp+@$commAmount2_tmp), 2, '.', ''); //ค่าธรรมเนียมทั้งหมด
		$arr_feeAmount1 = explode('.',$feeAmount1_tmp);
		$feeAmount1 = sprintf("%06d",$arr_feeAmount1[0].$arr_feeAmount1[1]);
		
		// response
		$res = array(
			'responseCode'=>@$response_code,
			'responseDesc'=>@$tran_type_description,
			'messageType'=>@$message_type,
			'coopReferenceNo'=>@$coop_reference_no,
			'tranType'=>@$get_request_data['tranType'],
			'fromAcctType'=>@$get_request_data['fromAcctType'],
			'toAcctType'=>@$get_request_data['toAcctType'],
			'traceNo'=>@$get_request_data['traceNo'],
			'termFIID'=>@$get_request_data['termFIID'],
			'termID'=>@$get_request_data['termID'],
			'termBranchID'=>@$get_request_data['termBranchID'],
			'termRegionID'=>@$get_request_data['termRegionID'],
			'termSeqID'=>@$get_request_data['termSeqID'],
			'termType'=>@$get_request_data['termType'],
			'transactionDate'=>date('md'),
			'transactionTime'=>date('his'),
			'coopCode'=>@$get_request_data['coopCode'],
			'fromAcctNo'=>@$get_request_data['fromAcctNo'],
			'fromAcctBranch'=>@$from_acct_branch, 
			'cardFIID'=>@$get_request_data['cardFIID'],
			'cardNo'=>@$get_request_data['cardNo'],
			'terminalUsageFlag'=>@$get_request_data['terminalUsageFlag'],
			'ledgerBalance'=>@$ledger_balance,
			'avaliableBalance'=>@$avaliable_balance ,
			'feeAmount1'=>@$feeAmount1,
			'commAmount1'=>@$commAmount1,
			'feeAmount2'=>@$feeAmount2,
			'commAmount2'=>@$commAmount2,
			'postDate'=>date('md'),
		);

		//บันทึก response
		$save_response = $this->save_response($res);	
		return json_encode($res);
	}
	
	//รองรับการทำรายการถอนเงิน
	public function withdrawlTxn($get_request_data)
	{		
		//บันทึก request
		$this->save_request($get_request_data);	
		
		//ResponseDesc
		$this->db->select(array('*'));
		$this->db->from('tran_type_atm');			
		$this->db->where("tran_type_code = '".@$get_request_data['tranType']."'");			
		$rs_type = $this->db->get()->result_array();
		$row_type = @$rs_type[0];
		$tran_type_description = @$row_type['tran_type_description'];		
		
		//AvaliableBalanceCheck
		$avaliable_balance_check = $this->avaliable_balance(@$get_request_data['fromAcctNo'],@$get_request_data['cardNo'],@$get_request_data['fromAcctType']);
		
		$transactionYYmmdd = date('Y').$get_request_data['transactionDate'];
		$response_code = $this->response_code(@$get_request_data,$avaliable_balance_check,$transactionYYmmdd);
		
		$commAmount1_tmp = (@$get_request_data['commAmount1'] == '')?0.00:@$get_request_data['commAmount1'];//ค่าธรรมเนียมการทำรายการข้ามเขต
		$commAmount1_tmp = number_format(($commAmount1_tmp), 2, '.', ''); 
		$arr_commAmount1 = explode('.',$commAmount1_tmp);
		$commAmount1 = sprintf("%06d",$arr_commAmount1[0].$arr_commAmount1[1]);
		
		$feeAmount2_tmp = $this->other_atm_fee(@$get_request_data['termFIID'],@$get_request_data['termSeqID'],@$get_request_data['cardNo'],@$get_request_data['transactionDate'],@$get_request_data['tranType'],@$get_request_data['toAcctType'],$response_code,@$get_request_data['fromAcctNo']); //ค่าธรรมเนียมการทำรายการตู้ต่างสหกรณ์ หรือตู้ต่างธนาคาร หรือค่าธรรมเนียมทำรายการตู้ตัวเองเกินจำนวนครั้ง
		$feeAmount2_tmp = number_format(($feeAmount2_tmp), 2, '.', ''); 
		$arr_feeAmount2 = explode('.',$feeAmount2_tmp);
		$feeAmount2 = sprintf("%06d",$arr_feeAmount2[0].$arr_feeAmount2[1]);
		
		
		$commAmount2_tmp= (@$get_request_data['commAmount2'] == '')?0.00:@$get_request_data['commAmount2']; //ค่าธรรมเนียมการโอนเงิน
		$commAmount2_tmp = number_format(($commAmount2_tmp), 2, '.', ''); 
		$arr_commAmount2 = explode('.',$commAmount2_tmp);
		$commAmount2 = sprintf("%06d",$arr_commAmount2[0].$arr_commAmount2[1]);
		
		$feeAmount1_tmp = number_format((@$commAmount1_tmp+@$feeAmount2_tmp+@$commAmount2_tmp), 2, '.', ''); //ค่าธรรมเนียมทั้งหมด
		$arr_feeAmount1 = explode('.',$feeAmount1_tmp);
		$feeAmount1 = sprintf("%06d",$arr_feeAmount1[0].$arr_feeAmount1[1]);
		
		if(@$get_request_data['messageType'] == '0400' || @$get_request_data['messageType'] == '0420'){
			//reversal ต้องคืนเงินให้ลูกค้า
			$reversal = $this->reversal_refund(@$get_request_data['traceNo'],@$get_request_data['fromAcctType']);
			if($reversal == 0){
				//067 invalid cash back amt -> เงินคืนที่ไม่ถูกต้อง
				$response_code = '067';
			}
		}else{
			if(@$response_code == '000'){
				$transaction_integer = substr(@$get_request_data['transactionAmount'],0,6);//จำนวนเต็ม
				$transaction_decimal = substr(@$get_request_data['transactionAmount'],6,2);//ทศนิยม
				$transaction_amount = $transaction_integer.'.'.$transaction_decimal;//ยอดเงินที่ถอน
				
				//ค่าธรรมเนียม	
				$fee_integer = (int)substr(@$feeAmount1,0,4);//จำนวนเต็ม
				$fee_decimal = substr(@$feeAmount1,4,2);//ทศนิยม
				$fee_amount = $fee_integer.'.'.$fee_decimal;//ค่าธรรมเนียม
				//บันทึกการถอนเงิน	
				$this->saveWithdrawlTxn(@$get_request_data['fromAcctNo'],@$get_request_data['cardNo'],@$get_request_data['fromAcctType'],@$transaction_amount,@$get_request_data['traceNo'],@$get_request_data['termSeqID'],$fee_amount);
			}
		}
		
		
		
		//ledger_balance
		$ledger_balance_tmp = $this->ledger_balance(@$get_request_data['fromAcctNo'],@$get_request_data['cardNo'],@$get_request_data['fromAcctType']);
		$ledger_balance_tmp = number_format($ledger_balance_tmp, 2, '.', '');
		$arr_ledger_balance_tmp = explode('.',$ledger_balance_tmp);
		$ledger_balance = sprintf("%08d",$arr_ledger_balance_tmp[0].$arr_ledger_balance_tmp[1]);	
		
		//AvaliableBalance
		$avaliable_balance_tmp = $this->avaliable_balance(@$get_request_data['fromAcctNo'],@$get_request_data['cardNo'],@$get_request_data['fromAcctType']);
		$avaliable_balance_tmp = number_format($avaliable_balance_tmp, 2, '.', '');
		$arr_avaliable_balance_tmp = explode('.',$avaliable_balance_tmp);
		$avaliable_balance = sprintf("%08d",$arr_avaliable_balance_tmp[0].$arr_avaliable_balance_tmp[1]);
		
		$message_type = $this->message_type(@$get_request_data['tranType'],@$get_request_data['messageType']);
		$coop_reference_no = "";//ส่วนนี้ได้มาจากไหน
		$from_acct_branch = ""; //ส่วนนี้ได้มาจากไหน
		
		// response
		$res = array(
			'responseCode'=>@$response_code,
			'responseDesc'=>@$tran_type_description,
			'messageType'=>@$message_type,
			'coopReferenceNo'=>@$coop_reference_no,
			'tranType'=>@$get_request_data['tranType'],
			'fromAcctType'=>@$get_request_data['fromAcctType'],
			'toAcctType'=>@$get_request_data['toAcctType'],
			'traceNo'=>@$get_request_data['traceNo'],
			'termFIID'=>@$get_request_data['termFIID'],
			'termID'=>@$get_request_data['termID'],
			'termBranchID'=>@$get_request_data['termBranchID'],
			'termRegionID'=>@$get_request_data['termRegionID'],
			'termSeqID'=>@$get_request_data['termSeqID'],
			'termType'=>@$get_request_data['termType'],
			'transactionDate'=>date('md'),
			'transactionTime'=>date('his'),
			'transactionAmount'=>@$get_request_data['transactionAmount'],
			'coopCode'=>@$get_request_data['coopCode'],
			'fromAcctNo'=>@$get_request_data['fromAcctNo'],
			'fromAcctBranch'=>@$from_acct_branch, 
			'cardFIID'=>@$get_request_data['cardFIID'],
			'cardNo'=>@$get_request_data['cardNo'],
			'terminalUsageFlag'=>@$get_request_data['terminalUsageFlag'],
			'ledgerBalance'=>@$ledger_balance,
			'avaliableBalance'=>@$avaliable_balance ,
			'feeAmount1'=>@$feeAmount1,
			'commAmount1'=>@$commAmount1,
			'feeAmount2'=>@$feeAmount2,
			'commAmount2'=>@$commAmount2,
			'postDate'=>date('md'),
		);
		
		//บันทึก response
		$save_response = $this->save_response($res);	
	
		return json_encode($res);
	}
	
	//รองรับการทำรายการโอนเงินไปยังบัญชีภายในบัตร
	public function transfer_withln_card_txn($get_request_data)
	{
		//บันทึก request
		$this->save_request($get_request_data);		
		
		//ResponseDesc
		$this->db->select(array('*'));
		$this->db->from('tran_type_atm');			
		$this->db->where("tran_type_code = '".@$get_request_data['tranType']."'");			
		$rs_type = $this->db->get()->result_array();
		$row_type = @$rs_type[0];
		$tran_type_description = $row_type['tran_type_description'];
		
		//AvaliableBalanceCheck
		$avaliable_balance_check = $this->avaliable_balance(@$get_request_data['fromAcctNo'],@$get_request_data['cardNo'],@$get_request_data['fromAcctType']);
		
		$transactionYYmmdd = date('Y').$get_request_data['transactionDate'];
		$response_code = $this->response_code(@$get_request_data,$avaliable_balance_check,$transactionYYmmdd);
		if(@$response_code == '000'){
			$transaction_integer = substr(@$get_request_data['transactionAmount'],0,6);//จำนวนเต็ม
			$transaction_decimal = substr(@$get_request_data['transactionAmount'],6,2);//ทศนิยม
			$transaction_amount = $transaction_integer.'.'.$transaction_decimal;//ยอดเงินที่ถอน
			//บันทึกการการโอนเงินไปยังบัญชีภายในบัตร		
			$this->saveTransferWithlnCardTxn(@$get_request_data['fromAcctNo'],@$get_request_data['toAccountNo'],@$get_request_data['cardNo'],@$get_request_data['fromAcctType'],@$transaction_amount,@$get_request_data['traceNo'],@$get_request_data['termSeqID']);
		}
		
		//ledger_balance
		$ledger_balance_tmp = $this->ledger_balance(@$get_request_data['fromAcctNo'],@$get_request_data['cardNo'],@$get_request_data['fromAcctType']);
		$ledger_balance_tmp = number_format($ledger_balance_tmp, 2, '.', '');
		$arr_ledger_balance_tmp = explode('.',$ledger_balance_tmp);
		$ledger_balance = sprintf("%08d",$arr_ledger_balance_tmp[0].$arr_ledger_balance_tmp[1]);	
		
		//AvaliableBalance
		$avaliable_balance_tmp = $this->avaliable_balance(@$get_request_data['fromAcctNo'],@$get_request_data['cardNo'],@$get_request_data['fromAcctType']);
		$avaliable_balance_tmp = number_format($avaliable_balance_tmp, 2, '.', '');
		$arr_avaliable_balance_tmp = explode('.',$avaliable_balance_tmp);
		$avaliable_balance = sprintf("%08d",$arr_avaliable_balance_tmp[0].$arr_avaliable_balance_tmp[1]);
		
		$message_type = $this->message_type(@$get_request_data['tranType']);
		$coop_reference_no = "";//
		$from_acct_branch = ""; //
		$to_acct_branch = ""; //
		
		$commAmount1_tmp = (@$get_request_data['commAmount1'] == '')?0.00:@$get_request_data['commAmount1'];//ค่าธรรมเนียมการทำรายการข้ามเขต
		$commAmount1_tmp = number_format(($commAmount1_tmp), 2, '.', ''); 
		$arr_commAmount1 = explode('.',$commAmount1_tmp);
		$commAmount1 = sprintf("%06d",$arr_commAmount1[0].$arr_commAmount1[1]);
		
		$feeAmount2_tmp = (@$get_request_data['feeAmount2'] == '')?0.00:@$get_request_data['feeAmount2']; //ค่าธรรมเนียมการทำรายการตู้ต่างสหกรณ์ หรือตู้ต่างธนาคาร หรือค่าธรรมเนียมทำรายการตู้ตัวเองเกินจำนวนครั้ง
		$feeAmount2_tmp = number_format(($feeAmount2_tmp), 2, '.', ''); 
		$arr_feeAmount2 = explode('.',$feeAmount2_tmp);
		$feeAmount2 = sprintf("%06d",$arr_feeAmount2[0].$arr_feeAmount2[1]);
		
		
		$commAmount2_tmp= (@$get_request_data['commAmount2'] == '')?0.00:@$get_request_data['commAmount2']; //ค่าธรรมเนียมการโอนเงิน
		$commAmount2_tmp = number_format(($commAmount2_tmp), 2, '.', ''); 
		$arr_commAmount2 = explode('.',$commAmount2_tmp);
		$commAmount2 = sprintf("%06d",$arr_commAmount2[0].$arr_commAmount2[1]);
		
		$feeAmount1_tmp = number_format((@$commAmount1_tmp+@$feeAmount2_tmp+@$commAmount2_tmp), 2, '.', ''); //ค่าธรรมเนียมทั้งหมด
		$arr_feeAmount1 = explode('.',$feeAmount1_tmp);
		$feeAmount1 = sprintf("%06d",$arr_feeAmount1[0].$arr_feeAmount1[1]);
				
		// response
		$res = array(
			'responseCode'=>@$response_code,
			'responseDesc'=>@$tran_type_description,
			'messageType'=>@$message_type,
			'coopReferenceNo'=>@$coop_reference_no,
			'tranType'=>@$get_request_data['tranType'],
			'fromAcctType'=>@$get_request_data['fromAcctType'],
			'toAcctType'=>@$get_request_data['toAcctType'],
			'traceNo'=>@$get_request_data['traceNo'],
			'termFIID'=>@$get_request_data['termFIID'],
			'termID'=>@$get_request_data['termID'],
			'termBranchID'=>@$get_request_data['termBranchID'],
			'termRegionID'=>@$get_request_data['termRegionID'],
			'termSeqID'=>@$get_request_data['termSeqID'],
			'termType'=>@$get_request_data['termType'],
			'transactionDate'=>date('md'),
			'transactionTime'=>date('his'),
			'transactionAmount'=>@$get_request_data['transactionAmount'],
			'coopCode'=>@$get_request_data['coopCode'],
			'fromAcctNo'=>@$get_request_data['fromAcctNo'],
			'fromAcctBranch'=>@$from_acct_branch,
			'toAcctNo'=>@$get_request_data['toAccountNo'],
			'toAcctBranch'=>@$to_acct_branch, 
			'cardFIID'=>@$get_request_data['cardFIID'],
			'cardNo'=>@$get_request_data['cardNo'],
			'terminalUsageFlag'=>@$get_request_data['terminalUsageFlag'],
			'ledgerBalance'=>@$ledger_balance,
			'avaliableBalance'=>@$avaliable_balance ,
			'feeAmount1'=>@$feeAmount1,
			'commAmount1'=>@$commAmount1,
			'feeAmount2'=>@$feeAmount2,
			'commAmount2'=>@$commAmount2,
			'iRusageFlag'=>@$get_request_data['iRusageFlag'],
			'postDate'=>date('md'),
		);

		//บันทึก response
		$save_response = $this->save_response($res);	
		return json_encode($res);
	}
	
	//รองรับการทำรายการสอบถามข้อมูลบัญชีบุคคลอื่นภายในสหกรณ์ เพื่อใช้ในการโอนเงิน
	public function transfer_withln_coop_inquiry($get_request_data)
	{
		//บันทึก request
		$this->save_request($get_request_data);		
		
		//ResponseDesc
		$this->db->select(array('*'));
		$this->db->from('tran_type_atm');			
		$this->db->where("tran_type_code = '".@$get_request_data['tranType']."'");			
		$rs_type = $this->db->get()->result_array();
		$row_type = @$rs_type[0];
		$tran_type_description = $row_type['tran_type_description'];
		
		//ledger_balance
		$ledger_balance_tmp = $this->ledger_balance(@$get_request_data['fromAcctNo'],@$get_request_data['cardNo'],@$get_request_data['fromAcctType']);
		$ledger_balance_tmp = number_format($ledger_balance_tmp, 2, '.', '');
		$arr_ledger_balance_tmp = explode('.',$ledger_balance_tmp);
		$ledger_balance = sprintf("%08d",$arr_ledger_balance_tmp[0].$arr_ledger_balance_tmp[1]);	
		
		//AvaliableBalance
		$avaliable_balance_tmp = $this->avaliable_balance(@$get_request_data['fromAcctNo'],@$get_request_data['cardNo'],@$get_request_data['fromAcctType']);
		$avaliable_balance_tmp = number_format($avaliable_balance_tmp, 2, '.', '');
		$arr_avaliable_balance_tmp = explode('.',$avaliable_balance_tmp);
		$avaliable_balance = sprintf("%08d",$arr_avaliable_balance_tmp[0].$arr_avaliable_balance_tmp[1]);
		
		$transactionYYmmdd = date('Y').$get_request_data['transactionDate'];
		$response_code = $this->response_code(@$get_request_data,$avaliable_balance,$transactionYYmmdd);
		
		$message_type = $this->message_type(@$get_request_data['tranType']);
		$coop_reference_no = "";//
		$from_acct_branch = ""; //
		$to_acct_branch = ""; //
		$to_acct_name = $this->get_acct_name(@$get_request_data['toAccountNo'],'TH'); //ชื่อบัญชีปลายทาง (TH,ENG)
		
		$commAmount1_tmp = (@$get_request_data['commAmount1'] == '')?0.00:@$get_request_data['commAmount1'];//ค่าธรรมเนียมการทำรายการข้ามเขต
		$commAmount1_tmp = number_format(($commAmount1_tmp), 2, '.', ''); 
		$arr_commAmount1 = explode('.',$commAmount1_tmp);
		$commAmount1 = sprintf("%06d",$arr_commAmount1[0].$arr_commAmount1[1]);
		
		$feeAmount2_tmp = (@$get_request_data['feeAmount2'] == '')?0.00:@$get_request_data['feeAmount2']; //ค่าธรรมเนียมการทำรายการตู้ต่างสหกรณ์ หรือตู้ต่างธนาคาร หรือค่าธรรมเนียมทำรายการตู้ตัวเองเกินจำนวนครั้ง
		$feeAmount2_tmp = number_format(($feeAmount2_tmp), 2, '.', ''); 
		$arr_feeAmount2 = explode('.',$feeAmount2_tmp);
		$feeAmount2 = sprintf("%06d",$arr_feeAmount2[0].$arr_feeAmount2[1]);
		
		
		$commAmount2_tmp= (@$get_request_data['commAmount2'] == '')?0.00:@$get_request_data['commAmount2']; //ค่าธรรมเนียมการโอนเงิน
		$commAmount2_tmp = number_format(($commAmount2_tmp), 2, '.', ''); 
		$arr_commAmount2 = explode('.',$commAmount2_tmp);
		$commAmount2 = sprintf("%06d",$arr_commAmount2[0].$arr_commAmount2[1]);
		
		$feeAmount1_tmp = number_format((@$commAmount1_tmp+@$feeAmount2_tmp+@$commAmount2_tmp), 2, '.', ''); //ค่าธรรมเนียมทั้งหมด
		$arr_feeAmount1 = explode('.',$feeAmount1_tmp);
		$feeAmount1 = sprintf("%06d",$arr_feeAmount1[0].$arr_feeAmount1[1]);
		
		// response
		$res = array(
			'responseCode'=>@$response_code,
			'responseDesc'=>@$tran_type_description,
			'messageType'=>@$message_type,
			'coopReferenceNo'=>@$coop_reference_no,
			'tranType'=>@$get_request_data['tranType'],
			'fromAcctType'=>@$get_request_data['fromAcctType'],
			'toAcctType'=>@$get_request_data['toAcctType'],
			'traceNo'=>@$get_request_data['traceNo'],
			'termFIID'=>@$get_request_data['termFIID'],
			'termID'=>@$get_request_data['termID'],
			'termBranchID'=>@$get_request_data['termBranchID'],
			'termRegionID'=>@$get_request_data['termRegionID'],
			'termSeqID'=>@$get_request_data['termSeqID'],
			'termType'=>@$get_request_data['termType'],
			'transactionDate'=>date('md'),
			'transactionTime'=>date('his'),
			'transactionAmount'=>@$get_request_data['transactionAmount'],
			'coopCode'=>@$get_request_data['coopCode'],
			'fromAcctNo'=>@$get_request_data['fromAcctNo'],
			'fromAcctBranch'=>@$from_acct_branch,
			'toAcctNo'=>@$get_request_data['toAccountNo'],
			'toAcctBranch'=>@$to_acct_branch, 
			'toAcctName'=>@$to_acct_name, 
			'cardFIID'=>@$get_request_data['cardFIID'],
			'cardNo'=>@$get_request_data['cardNo'],
			'terminalUsageFlag'=>@$get_request_data['terminalUsageFlag'],
			'feeAmount1'=>@$feeAmount1,
			'commAmount1'=>@$commAmount1,
			'feeAmount2'=>@$feeAmount2,
			'commAmount2'=>@$commAmount2,
			'iRusageFlag'=>@$get_request_data['iRusageFlag'],
			'postDate'=>date('md'),
		);
		//บันทึก response
		$save_response = $this->save_response($res);	
		return json_encode($res);
	}
	
	//รองรับการทำรายการโอนเงินไปยังบัญชีบุคคลอื่นภายในสหกรณ์
	public function transfer_withln_coop_txn($get_request_data)
	{
		//บันทึก request
		$this->save_request($get_request_data);		
		
		//ResponseDesc
		$this->db->select(array('*'));
		$this->db->from('tran_type_atm');			
		$this->db->where("tran_type_code = '".@$get_request_data['tranType']."'");			
		$rs_type = $this->db->get()->result_array();
		$row_type = @$rs_type[0];
		$tran_type_description = $row_type['tran_type_description'];
		
		//AvaliableBalanceCheck
		$avaliable_balance_check = $this->avaliable_balance(@$get_request_data['fromAcctNo'],@$get_request_data['cardNo'],@$get_request_data['fromAcctType']);
		
		$transactionYYmmdd = date('Y').$get_request_data['transactionDate'];
		$response_code = $this->response_code(@$get_request_data,$avaliable_balance_check,$transactionYYmmdd);
		
		if(@$response_code == '000'){
			$transaction_integer = substr(@$get_request_data['transactionAmount'],0,6);//จำนวนเต็ม
			$transaction_decimal = substr(@$get_request_data['transactionAmount'],6,2);//ทศนิยม
			$transaction_amount = $transaction_integer.'.'.$transaction_decimal;//ยอดเงินที่ถอน
			//บันทึกการการโอนเงินไปยังบัญชีบุคคลอื่นภายในสหกรณ์		
			$this->saveTransferWithlnCardTxn(@$get_request_data['fromAcctNo'],@$get_request_data['toAccountNo'],@$get_request_data['cardNo'],@$get_request_data['fromAcctType'],@$transaction_amount,@$get_request_data['traceNo'],@$get_request_data['termSeqID']);
		}
		
		//ledger_balance
		$ledger_balance_tmp = $this->ledger_balance(@$get_request_data['fromAcctNo'],@$get_request_data['cardNo'],@$get_request_data['fromAcctType']);
		$ledger_balance_tmp = number_format($ledger_balance_tmp, 2, '.', '');
		$arr_ledger_balance_tmp = explode('.',$ledger_balance_tmp);
		$ledger_balance = sprintf("%08d",$arr_ledger_balance_tmp[0].$arr_ledger_balance_tmp[1]);	
		
		//AvaliableBalance
		$avaliable_balance_tmp = $this->avaliable_balance(@$get_request_data['fromAcctNo'],@$get_request_data['cardNo'],@$get_request_data['fromAcctType']);
		$avaliable_balance_tmp = number_format($avaliable_balance_tmp, 2, '.', '');
		$arr_avaliable_balance_tmp = explode('.',$avaliable_balance_tmp);
		$avaliable_balance = sprintf("%08d",$arr_avaliable_balance_tmp[0].$arr_avaliable_balance_tmp[1]);
		
		$message_type = $this->message_type(@$get_request_data['tranType']);
		$coop_reference_no = "";//
		$from_acct_branch = ""; //
		$to_acct_branch = ""; //
		
		$commAmount1_tmp = (@$get_request_data['commAmount1'] == '')?0.00:@$get_request_data['commAmount1'];//ค่าธรรมเนียมการทำรายการข้ามเขต
		$commAmount1_tmp = number_format(($commAmount1_tmp), 2, '.', ''); 
		$arr_commAmount1 = explode('.',$commAmount1_tmp);
		$commAmount1 = sprintf("%06d",$arr_commAmount1[0].$arr_commAmount1[1]);
		
		$feeAmount2_tmp = (@$get_request_data['feeAmount2'] == '')?0.00:@$get_request_data['feeAmount2']; //ค่าธรรมเนียมการทำรายการตู้ต่างสหกรณ์ หรือตู้ต่างธนาคาร หรือค่าธรรมเนียมทำรายการตู้ตัวเองเกินจำนวนครั้ง
		$feeAmount2_tmp = number_format(($feeAmount2_tmp), 2, '.', ''); 
		$arr_feeAmount2 = explode('.',$feeAmount2_tmp);
		$feeAmount2 = sprintf("%06d",$arr_feeAmount2[0].$arr_feeAmount2[1]);
		
		
		$commAmount2_tmp= (@$get_request_data['commAmount2'] == '')?0.00:@$get_request_data['commAmount2']; //ค่าธรรมเนียมการโอนเงิน
		$commAmount2_tmp = number_format(($commAmount2_tmp), 2, '.', ''); 
		$arr_commAmount2 = explode('.',$commAmount2_tmp);
		$commAmount2 = sprintf("%06d",$arr_commAmount2[0].$arr_commAmount2[1]);
		
		$feeAmount1_tmp = number_format((@$commAmount1_tmp+@$feeAmount2_tmp+@$commAmount2_tmp), 2, '.', ''); //ค่าธรรมเนียมทั้งหมด
		$arr_feeAmount1 = explode('.',$feeAmount1_tmp);
		$feeAmount1 = sprintf("%06d",$arr_feeAmount1[0].$arr_feeAmount1[1]);
		
		// response
		$res = array(
			'responseCode'=>@$response_code,
			'responseDesc'=>@$tran_type_description,
			'messageType'=>@$message_type,
			'coopReferenceNo'=>@$coop_reference_no,
			'tranType'=>@$get_request_data['tranType'],
			'fromAcctType'=>@$get_request_data['fromAcctType'],
			'toAcctType'=>@$get_request_data['toAcctType'],
			'traceNo'=>@$get_request_data['traceNo'],
			'termFIID'=>@$get_request_data['termFIID'],
			'termID'=>@$get_request_data['termID'],
			'termBranchID'=>@$get_request_data['termBranchID'],
			'termRegionID'=>@$get_request_data['termRegionID'],
			'termSeqID'=>@$get_request_data['termSeqID'],
			'termType'=>@$get_request_data['termType'],
			'transactionDate'=>date('md'),
			'transactionTime'=>date('his'),
			'transactionAmount'=>@$get_request_data['transactionAmount'],
			'coopCode'=>@$get_request_data['coopCode'],
			'fromAcctNo'=>@$get_request_data['fromAcctNo'],
			'fromAcctBranch'=>@$from_acct_branch,
			'toAcctNo'=>@$get_request_data['toAccountNo'],
			'toAcctBranch'=>@$to_acct_branch, 
			'cardFIID'=>@$get_request_data['cardFIID'],
			'cardNo'=>@$get_request_data['cardNo'],
			'terminalUsageFlag'=>@$get_request_data['terminalUsageFlag'],
			'ledgerBalance'=>@$ledger_balance,
			'avaliableBalance'=>@$avaliable_balance ,
			'feeAmount1'=>@$feeAmount1,
			'commAmount1'=>@$commAmount1,
			'feeAmount2'=>@$feeAmount2,
			'commAmount2'=>@$commAmount2,
			'iRusageFlag'=>@$get_request_data['iRusageFlag'],
			'postDate'=>date('md'),
		);
		//บันทึก response
		$save_response = $this->save_response($res);	
		return json_encode($res);
	}
	
	//หายอดเงินคงเหลือในบัญชีทั้งหมด -ของ ATM  -ของเงินฝาก
	public function ledger_balance($from_acct_no,$card_no,$from_acct_type)
	{
		$balance = 0;
		//from_acct_type 00=Unspecified ,01=loan atm,11=saving		
		//ATM
		if($from_acct_type == '01'){
			$this->db->select(array('*'));
			$this->db->from('coop_loan_atm');			
			//$this->db->where("atm_card_number = '".@$card_no."' AND loan_atm_status ='1' ");	
			$this->db->where("account_id = '".@$from_acct_no."' AND loan_atm_status ='1'  AND activate_status = '0'");	
			$this->db->limit(1);	
			$rs_loan_atm= $this->db->get()->result_array();
			$row_loan_atm = @$rs_loan_atm[0];
			
			if(!empty($row_loan_atm)){
				$balance = number_format(@$row_loan_atm['total_amount_balance'], 2, '.', '');
			}
		}
		
		if($from_acct_type == '11' || $from_acct_type == '00'){
			//เงินฝาก อื่นๆ 
			$this->db->select(array('*'));
			$this->db->from('coop_maco_account');			
			$this->db->where("account_id = '".@$from_acct_no."'");	
			$this->db->limit(1);	
			$rs_account = $this->db->get()->result_array();
			$row_account = @$rs_account[0];
			
			if(!empty($row_account)){
				$this->db->select(array('*'));
				$this->db->from('coop_account_transaction');			
				$this->db->where("account_id = '".@$from_acct_no."'");	
				$this->db->order_by("transaction_time DESC, transaction_id DESC");	
				$this->db->limit(1);	
				$rs_transaction = $this->db->get()->result_array();
				$row_transaction = @$rs_transaction[0];
				if(!empty($row_transaction)){
					$balance = number_format(@$row_transaction['transaction_balance'], 2, '.', '');
				}
			}
		}
		return $balance;
	}
	
	//หายอดเงินที่อนุมัติให้ทำรายการได้ -ของ ATM  -ของเงินฝาก
	public function avaliable_balance($from_acct_no,$card_no,$from_acct_type)
	{
		$balance = 0;
		//from_acct_type 00=Unspecified ,01=loan atm,11=saving
		//ATM
		if($from_acct_type == '01'){
			$this->db->select(array('*'));
			$this->db->from('coop_loan_atm');			
			$this->db->where("account_id = '".@$from_acct_no."' AND loan_atm_status ='1'  AND activate_status = '0' ");
			$this->db->limit(1);	
			$rs_loan_atm= $this->db->get()->result_array();
			$row_loan_atm = @$rs_loan_atm[0];
			
			if(!empty($row_loan_atm)){
				$balance = number_format(@$row_loan_atm['total_amount_balance'], 2, '.', '');
			}
		}
		
		if($from_acct_type == '11' || $from_acct_type == '00'){
			//เงินฝาก อื่นๆ 
			$this->db->select(array('*'));
			$this->db->from('coop_maco_account');			
			$this->db->where("account_id = '".@$from_acct_no."'");	
			$this->db->limit(1);	
			$rs_account = $this->db->get()->result_array();
			$row_account = @$rs_account[0];
			
			if(!empty($row_account)){
				$sequester_status = @$row_account['sequester_status'];
				$sequester_status_atm = @$row_account['sequester_status_atm'];
				$sequester_amount = @$row_account['sequester_amount'];
				$this->db->select(array('*'));
				$this->db->from('coop_account_transaction');			
				$this->db->where("account_id = '".@$from_acct_no."'");	
				$this->db->order_by("transaction_time DESC, transaction_id DESC");	
				$this->db->limit(1);	
				$rs_transaction = $this->db->get()->result_array();
				$row_transaction = @$rs_transaction[0];
				if(!empty($row_transaction)){
					if($sequester_status_atm == '1'){
						//อายัดเงินในบัญชีทั้งหมด
						$balance = number_format(0, 2, '.', '');
					}else if($sequester_status == '2'){
						//อายัดเงินในบัญชีบางส่วน
						$withdrawal_amount = @$row_transaction['transaction_balance']-@$sequester_amount;
						$balance = number_format(@$withdrawal_amount, 2, '.', '');
					}else{
						$balance = number_format(@$row_transaction['transaction_balance'], 2, '.', '');
					}
				}
			}
		}
		return $balance;
	}
	
	//หายอดเงินคงเหลือในบัญชีทั้งหมด ของเงินฝาก บัญชีบุคคลอื่นภายในสหกรณ์
	public function ledger_balance_another($to_account_no)
	{
		$balance = 0;
		//เงินฝาก 21
		$this->db->select(array('*'));
		$this->db->from('coop_maco_account');			
		$this->db->where("account_id = '".@$to_account_no."'");	
		$this->db->limit(1);	
		$rs_account = $this->db->get()->result_array();
		$row_account = @$rs_account[0];
		
		if(!empty($row_account)){
			$this->db->select(array('*'));
			$this->db->from('coop_account_transaction');			
			$this->db->where("account_id = '".@$row_account['account_id']."'");	
			$this->db->order_by("transaction_time DESC, transaction_id DESC");	
			$this->db->limit(1);	
			$rs_transaction = $this->db->get()->result_array();
			$row_transaction = @$rs_transaction[0];
			
			$balance = number_format(@$row_transaction['transaction_balance'], 2, '.', '');
		}
		return $balance;
	}
	
	//หายอดเงินที่อนุมัติให้ทำรายการได้  ของเงินฝาก บัญชีบุคคลอื่นภายในสหกรณ์
	public function avaliable_balance_another($to_account_no)
	{
		$balance = 0;
		//เงินฝาก 21
		$this->db->select(array('*'));
		$this->db->from('coop_maco_account');			
		$this->db->where("account_id = '".@$to_account_no."'");	
		$this->db->limit(1);	
		$rs_account = $this->db->get()->result_array();
		$row_account = @$rs_account[0];
		
		if(!empty($row_account)){
			$this->db->select(array('*'));
			$this->db->from('coop_account_transaction');			
			$this->db->where("account_id = '".@$row_account['account_id']."'");	
			$this->db->order_by("transaction_time DESC, transaction_id DESC");	
			$this->db->limit(1);	
			$rs_transaction = $this->db->get()->result_array();
			$row_transaction = @$rs_transaction[0];
			
			$balance = number_format(@$row_transaction['transaction_balance'], 2, '.', '');
		}
		return $balance;
	}
	
	public function save_request($get_request_data)
	{
		$transactionDate = (@$get_request_data['transactionDate'] != '')?date('Y').@$get_request_data['transactionDate']:"";
		$postDate = (@$get_request_data['postDate'] != '')?date('Y').@$get_request_data['postDate']:"";
		$data_insert = array(
			'requestID'=>@$get_request_data['requestID'],
			'requesterSystem'=>@$get_request_data['requesterSystem'],
			'requestReferenceNo'=>@$get_request_data['requestReferenceNo'],
			'requestAction'=>@$get_request_data['requestAction'],
			'teamCode'=>@$get_request_data['teamCode'],
			'userID'=>@$get_request_data['userID'],
			'cifID'=>@$get_request_data['cifID'],
			'coopID'=>@$get_request_data['coopID'],
			'accountNo'=>@$get_request_data['accountNo'],
			'messageType'=>@$get_request_data['messageType'],
			'tranType'=>@$get_request_data['tranType'],
			'fromAcctType'=>@$get_request_data['fromAcctType'],
			'toAcctType'=>@$get_request_data['toAcctType'],
			'traceNo'=>@$get_request_data['traceNo'],
			'termFIID'=>@$get_request_data['termFIID'],
			'termID'=>@$get_request_data['termID'],
			'termBranchID'=>@$get_request_data['termBranchID'],
			'termRegionID'=>@$get_request_data['termRegionID'],
			'termSeqID'=>@$get_request_data['termSeqID'],
			'termType'=>@$get_request_data['termType'],
			'transactionDate'=>@$transactionDate,
			'transactionTime'=>@$get_request_data['transactionTime'],
			'transactionAmount'=>@$get_request_data['transactionAmount'],
			'coopCode'=>@$get_request_data['coopCode'],
			'fromAcctNo'=>@$get_request_data['fromAcctNo'],
			'toAccountNo'=>@$get_request_data['toAccountNo'],
			'cardFIID'=>@$get_request_data['cardFIID'],
			'cardNo'=>@$get_request_data['cardNo'],
			'terminalUsageFlag'=>@$get_request_data['terminalUsageFlag'],
			'iRUsageFlag'=>@$get_request_data['iRUsageFlag'],
			'feeAmount1'=>@$get_request_data['feeAmount1'],
			'commAmount1'=>@$get_request_data['commAmount1'],
			'feeAmount2'=>@$get_request_data['feeAmount2'],
			'commAmount2'=>@$get_request_data['commAmount2'],
			'postDate'=>@$postDate,
			'channelID'=>@$get_request_data['channelID'],
			'createdatetime'=>date('Y-m-d H:i:s')
		);

		$this->db->insert("message_request_atm", $data_insert);
	}
	
	public function save_response($get_request_data)
	{
		$transactionDate = (@$get_request_data['transactionDate'] != '')?date('Y').@$get_request_data['transactionDate']:"";
		$postDate = (@$get_request_data['postDate'] != '')?date('Y').@$get_request_data['postDate']:"";
		
		$data_insert = array(
				'responseID'=>@$get_request_data['responseID'],
				'responseCode'=>@$get_request_data['responseCode'],
				'responseDesc'=>@$get_request_data['responseDesc'],
				'messageType'=>@$get_request_data['messageType'],
				'coopReferenceNo'=>@$get_request_data['coopReferenceNo'],
				'requesterSystem'=>@$get_request_data['requesterSystem'],
				'requestReferenceNo'=>@$get_request_data['requestReferenceNo'],
				'responseAction'=>@$get_request_data['responseAction'],
				'acctNo'=>@$get_request_data['acctNo'],
				'acctType'=>@$get_request_data['acctType'],
				'acctBranch'=>@$get_request_data['acctBranch'],
				'titleEngName'=>@$get_request_data['titleEngName'],
				'engName'=>@$get_request_data['engName'],
				'titleThaiName'=>@$get_request_data['titleThaiName'],
				'thaiName'=>@$get_request_data['thaiName'],
				'birthDate'=>@$get_request_data['birthDate'],
				'customerType'=>@$get_request_data['customerType'],
				'cIFType'=>@$get_request_data['cIFType'],
				'cIFNo'=>@$get_request_data['cIFNo'],
				'coopID'=>@$get_request_data['coopID'],
				'gender'=>@$get_request_data['gender'],
				'address1'=>@$get_request_data['address1'],
				'address2'=>@$get_request_data['address2'],
				'address3'=>@$get_request_data['address3'],
				'address4'=>@$get_request_data['address4'],
				'zipCode'=>@$get_request_data['zipCode'],
				'telNo1'=>@$get_request_data['telNo1'],
				'telNo2'=>@$get_request_data['telNo2'],
				'telNo3'=>@$get_request_data['telNo3'],
				'billAddress1'=>@$get_request_data['billAddress1'],
				'billAddress2'=>@$get_request_data['billAddress2'],
				'billAddress3'=>@$get_request_data['billAddress3'],
				'billAddress4'=>@$get_request_data['billAddress4'],
				'billZipCode'=>@$get_request_data['billZipCode'],
				'tranType'=>@$get_request_data['tranType'],
				'fromAcctType'=>@$get_request_data['fromAcctType'],
				'toAcctType'=>@$get_request_data['toAcctType'],
				'traceNo'=>@$get_request_data['traceNo'],
				'termFIID'=>@$get_request_data['termFIID'],
				'termID'=>@$get_request_data['termID'],
				'termBranchID'=>@$get_request_data['termBranchID'],
				'termRegionID'=>@$get_request_data['termRegionID'],
				'termSeqID'=>@$get_request_data['termSeqID'],
				'termType'=>@$get_request_data['termType'],
				'transactionDate'=> @$transactionDate,
				'transactionTime'=>@$get_request_data['transactionTime'],
				'transactionAmount'=>@$get_request_data['transactionAmount'],
				'coopCode'=>@$get_request_data['coopCode'],
				'fromAcctNo'=>@$get_request_data['fromAcctNo'],
				'fromAcctBranch'=>@$get_request_data['fromAcctBranch'],
				'toAcctNo'=>@$get_request_data['toAcctNo'],
				'toAcctBranch'=>@$get_request_data['toAcctBranch'],
				'toAcctName'=>@$get_request_data['toAcctName'],
				'cardFIID'=>@$get_request_data['cardFIID'],
				'cardNo'=>@$get_request_data['cardNo'],
				'terminalUsageFlag'=>@$get_request_data['terminalUsageFlag'],
				'ledgerBalance'=>@$get_request_data['ledgerBalance'],
				'avaliableBalance'=>@$get_request_data['avaliableBalance'],
				'feeAmount1'=>@$get_request_data['feeAmount1'],
				'commAmount1'=>@$get_request_data['commAmount1'],
				'feeAmount2'=>@$get_request_data['feeAmount2'],
				'commAmount2'=>@$get_request_data['commAmount2'],
				'iRUsageFlag'=>@$get_request_data['iRUsageFlag'],
				'postDate'=>@$postDate,
				'createdatetime'=>date('Y-m-d H:i:s')
		);
		$this->db->insert("message_response_atm", $data_insert);
	}
	
	public function response_code($get_request_data,$avaliable_balance,$transactionYYmmdd)
	{
		$card_no = @$get_request_data['cardNo'];
		$transaction_amount = @$get_request_data['transactionAmount'];
		$message_type = @$get_request_data['messageType'];
		$from_acct_no = @$get_request_data['fromAcctNo'];
		$to_account_no = @$get_request_data['toAccountNo'];
		$from_acct_type = @$get_request_data['fromAcctType'];
		$to_acct_type = @$get_request_data['toAcctType'];
		$tranType = @$get_request_data['tranType'];
		
		//ยอดเงินที่ถอน 
		$transaction_integer = substr(@$transaction_amount,0,6);//จำนวนเต็ม
		$transaction_decimal = substr(@$transaction_amount,6,2);//ทศนิยม
		$request_amount = $transaction_integer.'.'.$transaction_decimal;//ยอดเงินที่ถอน
		
		$this->db->select(array('max_withdraw_amount_day'));
		$this->db->from('coop_loan_atm_setting');					
		$rs_atm_setting = $this->db->get()->result_array();
		$row_atm_setting = @$rs_atm_setting[0];
		$max_withdraw_amount_day = @$row_atm_setting['max_withdraw_amount_day'];
		
		$total_withdraw_amount_day = 0;
		
		if($tranType == '10'){
			//วันปัจจุบันที่ทำรายการ  $transactionYYmmdd;		
			$this->db->select(array('transactionAmount AS total_transaction','count(termSeqID) AS counttermSeqID','responseCode'));
			$this->db->from('message_response_atm');		
			//$this->db->where("responseCode = '000' AND transactionDate = '".$transactionYYmmdd."' AND tranType = '10' AND cardNo = '".$card_no."'");			
			$this->db->where("transactionDate = '".$transactionYYmmdd."' AND tranType = '10' AND cardNo = '".$card_no."'");			
			//$this->db->where("responseCode = '000' AND transactionDate = '".$transactionYYmmdd."' AND tranType = '10'  AND cardNo = '".$card_no."' AND fromAcctNo = '".$from_acct_no."'");			
			$this->db->group_by("termSeqID");
			$rs_response_atm = $this->db->get()->result_array();
			
			$total_transaction_all = 0; 
			foreach($rs_response_atm AS $key_1=>$row_response_atm){
				if(@$row_response_atm['counttermSeqID'] == '1' && @$row_response_atm['responseCode'] == '000'){
					$total_transaction = 0;
					$transaction_integer = (int)substr(@$row_response_atm['total_transaction'],0,6);//จำนวนเต็ม
					$transaction_decimal = substr(@$row_response_atm['total_transaction'],6,2);//ทศนิยม
					$total_transaction = $transaction_integer.'.'.$transaction_decimal;//ยอดเงินที่ถอน
					//echo $total_transaction.'<br>';
					$total_transaction_all +=$total_transaction; //ยอดเงินที่เคยโอน
				}
			}
			//echo $total_transaction_all.'<hr>';
			
			//ยอดที่ถอนของวันปัจจุบันที่ทำรายการ
			$total_withdraw_amount_day = @$total_transaction_all+@$request_amount;
		}
		
		$total_transfer_amount_day = 0;
		if($tranType == '40' || $tranType == '41'){
			$this->db->select(array('transactionAmount AS total_transaction','count(termSeqID) AS counttermSeqID','responseCode'));
			$this->db->from('message_response_atm');		
			$this->db->where("transactionDate = '".$transactionYYmmdd."' AND tranType IN ('40','41')  AND cardNo = '".$card_no."'");			
			//$this->db->where("responseCode = '000' AND transactionDate = '".$transactionYYmmdd."' AND tranType IN ('40','41')  AND cardNo = '".$card_no."'");			
			//$this->db->where("responseCode = '000' AND transactionDate = '".$transactionYYmmdd."' AND tranType IN ('40','41')  AND cardNo = '".$card_no."' AND fromAcctNo = '".$from_acct_no."'");			
			$this->db->group_by("termSeqID");
			$rs_response_atm_transfer = $this->db->get()->result_array();
			
			$total_transaction_transfer_all = 0; 
			foreach($rs_response_atm_transfer AS $key_4=>$row_response_atm_transfer){
				if(@$row_response_atm_transfer['counttermSeqID'] == '1' && @$row_response_atm_transfer['responseCode'] == '000'){
					$total_transaction_transfer = 0;
					$transaction_transfer_integer = (int)substr(@$row_response_atm_transfer['total_transaction'],0,6);//จำนวนเต็ม
					$transaction_transfer_decimal = substr(@$row_response_atm_transfer['total_transaction'],6,2);//ทศนิยม
					$total_transaction_transfer = $transaction_transfer_integer.'.'.$transaction_transfer_decimal;//ยอดเงินที่ถอน
					//echo $total_transaction_transfer.'<br>';
					$total_transaction_transfer_all +=$total_transaction_transfer; //ยอดเงินที่เคยโอน
				}
			}
			//echo $total_transaction_transfer_all.'<hr>';
			
			//ยอดที่ถอนของวันปัจจุบันที่ทำรายการ
			$total_transfer_amount_day = @$total_transaction_transfer_all+@$request_amount;
		}
		
		$to_account_status = '';
		if(@$to_account_no != ''){
			//เช็คบัญชีปลายทาง
			//echo $from_acct_type.'<hr>';
			if($from_acct_type == '01'){	
				$this->db->select('*');
				$this->db->from('coop_maco_account');
				$this->db->where("account_id = '".$to_account_no."'");
				$this->db->limit(1);
				$to_rs_account = $this->db->get()->result_array();
				$to_account_status = @$to_rs_account[0]['account_status'];	
				$to_maco_account_id = @$to_rs_account[0]['account_id'];	//บัญชีปลายทาง			
			}
			else{	
				$this->db->select('*');
				$this->db->from('coop_maco_account');
				$this->db->where("account_id = '".$to_account_no."'");
				$this->db->limit(1);
				$to_rs_account = $this->db->get()->result_array();
				$to_account_status = @$to_rs_account[0]['account_status'];	
				$to_maco_account_id = @$to_rs_account[0]['account_id'];	//บัญชีปลายทาง
				
				if($to_maco_account_id == ''){
					$this->db->select(array('*'));
					$this->db->from('coop_loan_atm');
					$this->db->where("account_id = '".@$to_account_no."' AND loan_atm_status ='1' AND activate_status = '0'");
					$this->db->limit(1);
					$to_rs_account = $this->db->get()->result_array();
					//echo $this->db->last_query();
					if(empty($to_rs_account)){
						$to_activate_status = '1';
						$to_account_status = '1';
						$to_maco_account_id = '';
					}else{
						$to_activate_status = '0';
						$to_account_status = '0';	
						$to_maco_account_id = @$to_rs_account[0]['account_id'];	//บัญชีปลายทาง
					}
				}
			}
		}
		
		if($from_acct_type == '01'){
			$this->db->select(array('*'));
			$this->db->from('coop_loan_atm');
			$this->db->where("account_id = '".@$from_acct_no."' AND loan_atm_status ='1' AND activate_status = '0'");
			$this->db->limit(1);
			$from_rs_account = $this->db->get()->result_array();
			if(empty($from_rs_account)){
				$from_activate_status = '1';
				$from_account_status = '1';
				$from_maco_account_id = '';
			}else{
				$from_activate_status = '0';
				$from_account_status = '0';	
				$from_maco_account_id = @$from_rs_account[0]['account_id'];	//บัญชีต้นทาง
			}
		}else{	
			//เช็คบัญชีต้นทาง
			$this->db->select('*');
			$this->db->from('coop_maco_account');
			$this->db->where("account_id = '".$from_acct_no."'");
			$this->db->limit(1);
			$from_rs_account = $this->db->get()->result_array();
			$from_account_status = @$from_rs_account[0]['account_status'];	
			$from_maco_account_id = @$from_rs_account[0]['account_id'];	//บัญชีต้นทาง
			
			//เช็ค อายัดบัญชีเงินฝาก
			if(@$from_rs_account[0]['account_status'] == '0' && @$from_rs_account[0]['sequester_status_atm'] == '1'){
				$from_check_sequester = '1';
			}else{
				$from_check_sequester = '0';
			}
		}
		//echo 'from='.$from_maco_account_id.' != '.@$from_acct_no.'<hr>';
		//echo 'to='.$to_maco_account_id.' != '.@$to_account_no.'<hr>';
		if(@$from_check_sequester == '1'){
			//อายัดบัญชีเงินฝาก
			$response_code = "069";
		}else if(@$from_acct_no == @$to_account_no){			
			//บัญชีต้นทาง และ บัญชีปลายทาง ต้องไม่ใช่ค่าเดียวกัน
			$response_code = "056";
		}else if(@$from_maco_account_id != @$from_acct_no){			
			//ไม่พบบัญชีต้นทาง
			$response_code = "056";
			//echo 'A<hr>';
		}else if(@$to_maco_account_id != @$to_account_no){			
			//ไม่พบบัญชีปลายทาง
			$response_code = "056";
			//echo 'B<hr>';
		}else if($from_account_status == '1'){
			//บัญชีที่ปิดแล้ว			
			//056 ineligible account -> บัญชีที่ไม่มีสิทธิ์
			$response_code = "056";
			//echo 'C<hr>';
		}else if($from_activate_status == '1'){
			//บัญชีที่ถูกระงับ เงินกู้ฉุกเฉิน			
			//056 ineligible account -> บัญชีที่ไม่มีสิทธิ์
			$response_code = "056";
			//echo 'C<hr>';
		}else if($to_account_status == '1'){
			//บัญชีที่ปิดแล้ว			
			//056 ineligible account -> บัญชีที่ไม่มีสิทธิ์
			$response_code = "056";
			//echo 'D<hr>';
		}else if($to_activate_status == '1'){
			//บัญชีที่ถูกระงับ เงินกู้ฉุกเฉิน			
			//056 ineligible account -> บัญชีที่ไม่มีสิทธิ์
			$response_code = "056";
			//echo 'D<hr>';
		}else if((@$from_acct_type == '00' || @$from_acct_type == '11') && (@$to_acct_type == '01')){	
			//โอนเงินจากบัญชีออมทรัพย์ เข้าบัญชีกู้ฉุกเฉิน (ต้องไม่สามารถทำได้)
			//055 ineligible transaction -> ไม่สามารถทำรายการนี้ได้
			$response_code = "055";
		}else if(@$request_amount > @$avaliable_balance){
			//058 = insufficient funds ยอดเงินที่ถอนเกินยอดเงินที่สามารถถอนได้
			$response_code = "058";
		}else if(@$total_withdraw_amount_day > @$max_withdraw_amount_day){			
			//061 = withrawal ถอนได้ไม่เกิน วันละ 200,000
			$response_code = "063";
		}else if(@$total_transfer_amount_day > @$max_withdraw_amount_day){			
			//061 = withrawal โอนได้ไม่เกิน วันละ 200,000
			$response_code = "063";
		}else{
			///000 = approve
			$response_code = "000";
		}

		return $response_code;
	}
	public function message_type($tran_type,$message_type = '')
	{
		//MessageType		
		//0710 = response-Balance Inquiry
		//0210 = authorization - AUTH Module
		//0410 = response - Reversal
		//9230 = response SAF - Host
		if($tran_type == '30'){
			$message_type = "0710";
		}else if($tran_type == '10'){
			if($message_type == '0400' || $message_type == '0420'){
				$message_type = "0410";
			}else{
				$message_type = "0210";
			}
		}else if($tran_type == '40'){
			$message_type = "0210";
		}else if($tran_type == '31'){
			$message_type = "0210";
		}else if($tran_type == '41'){
			$message_type = "0210";
		}else if($tran_type == '35'){
			$message_type = "0230";
		}else if($tran_type == '20'){
			$message_type = "0230";
		}else{
			$message_type = "";
		}
		
		return $message_type;
	}
	
	//บันทึกการถอนเงิน
	public function saveWithdrawlTxn($from_acct_no,$card_no,$from_acct_type,$transaction_amount,$trace_no,$term_seq_id,$feeAmount1=0)
	{
		//from_acct_type 00=Unspecified ,01=loan atm,11=saving
		
		if($from_acct_type == '01'){
			$this->saveWithdrawlTxnLoan($from_acct_no,$card_no,$transaction_amount,$trace_no,$term_seq_id,$feeAmount1);
		}
		
		if($from_acct_type == '11' || $from_acct_type == '00'){			
			$this->saveWithdrawlTxnSaving($from_acct_no,$card_no,$transaction_amount,$feeAmount1);
		}
	}

	//บันทึกการถอนเงินจากบัญชี
	public function saveWithdrawlTxnSaving($from_acct_no,$card_no,$transaction_amount,$feeAmount1=0)
	{
		//get member_id
		$member_id = $this->getMemberId($from_acct_no);
		
		$this->db->select('*');
		$this->db->from('coop_account_transaction');
		$this->db->where("account_id = '".$from_acct_no."'");
		$this->db->order_by('transaction_time DESC, transaction_id DESC');
		$this->db->limit(1);
		$row = $this->db->get()->result_array();
		if(!empty($row)){
			$balance = $row[0]['transaction_balance'];
			$balance_no_in = $row[0]['transaction_no_in_balance'];
		}else{
			$balance = 0;
			$balance_no_in = 0;
		}
		
		$money = @$transaction_amount;
		$sum = @$balance - @$money;
		$sum_no_in = @$balance_no_in - @$money;
		if($sum_no_in <= 0 ){$sum_no_in = 0;}
		if($sum > 0) {	
			$data_insert = array();
			$data_insert['transaction_time'] = date('Y-m-d H:i:s');
			$data_insert['transaction_list'] = 'CW';
			$data_insert['transaction_withdrawal'] = @$money;
			$data_insert['transaction_deposit'] = '';
			$data_insert['transaction_balance'] = @$sum;
			$data_insert['transaction_no_in_balance'] = @$sum_no_in;
			$data_insert['member_id_atm'] = @$member_id;
			$data_insert['account_id'] = @$from_acct_no;
			$this->db->insert('coop_account_transaction', $data_insert);
			
			
		}
		
		if($feeAmount1 > 0){
			$sum = $sum - $feeAmount1;
			$sum_no_in = $sum_no_in - $feeAmount1;
			$data_insert = array();
			$data_insert['transaction_time'] = date('Y-m-d H:i:s');
			$data_insert['transaction_list'] = 'CM/FE';
			$data_insert['transaction_withdrawal'] = $feeAmount1;
			$data_insert['transaction_deposit'] = '';
			$data_insert['transaction_balance'] = $sum;
			$data_insert['transaction_no_in_balance'] = $sum_no_in;
			$data_insert['member_id_atm'] = @$member_id;
			$data_insert['account_id'] = @$from_acct_no;
			
			$this->db->insert('coop_account_transaction', $data_insert);
		}
		
		return true;
	}	
	
	//บันทึกการถอนเงินจากเงินกู้ ATM
	public function saveWithdrawlTxnLoan($from_acct_no,$card_no,$transaction_amount,$trace_no,$term_seq_id,$feeAmount1=0)
	{
		$transaction_amount = @$transaction_amount+@$feeAmount1;
		//get member_id
		$member_id = $this->getMemberId($from_acct_no);
		
		$this->db->select('*');
		$this->db->from("coop_loan_atm_setting");
		$this->db->limit(1);
		$row = $this->db->get()->result_array();
		$row_setting = @$row[0];
		
		$this->db->select(array('loan_atm_id','member_id','total_amount_approve','total_amount_balance'));
		$this->db->from('coop_loan_atm');
		$this->db->where("account_id = '".@$from_acct_no."' AND loan_atm_status ='1' ");
		//$this->db->where("atm_card_number = '".$card_no."'");
		$this->db->limit(1);
		$rs_loan_atm = $this->db->get()->result_array();
		$row_loan_atm = @$rs_loan_atm[0];
		if(!empty($row_loan_atm)){
			$loan_atm_id = @$row_loan_atm['loan_atm_id'];
			$member_id = @$row_loan_atm['member_id'];

			$data_insert = array();
			$data_insert['loan_atm_id'] = @$loan_atm_id;
			$data_insert['member_id'] = @$member_id;
			$data_insert['loan_amount'] = str_replace(',','',$transaction_amount);
			$data_insert['loan_amount_balance'] = $data_insert['loan_amount'];
			$data_insert['loan_date'] = date('Y-m-d H:i:s');
			$data_insert['loan_status'] = '0';
			$data_insert['loan_description'] = 'ทำรายการกู้ATM';
			$data_insert['date_start_period'] = date('Y-m-t',strtotime('+1 month'));
			$data_insert['transaction_at'] = '1';
			$data_insert['transfer_status'] = '1';
			$data_insert['member_id_atm'] = @$member_id;
			
			$principal_per_month = $data_insert['loan_amount']/$row_setting['max_period'];
			$data_insert['principal_per_month'] = ceil($principal_per_month);
			//echo"<pre>";print_r($data_insert);exit;
			
			$this->db->select(array('petition_number'));
			$this->db->from('coop_loan_atm_detail');
			$this->db->order_by('petition_number DESC');
			$this->db->limit(1);
			$row_petition_number = $this->db->get()->result_array();
			if(!empty($row_petition_number)){
				$petition_number = $row_petition_number[0]['petition_number']+1;
				$petition_number = sprintf('%06d',$petition_number);
			}else{
				$petition_number = sprintf('%06d',1);
			}
			$data_insert['petition_number'] = $petition_number;
			$data_insert['trace_no'] = $trace_no;
			$data_insert['term_seq_id'] = $term_seq_id;

			$this->db->insert('coop_loan_atm_detail',$data_insert);
			$loan_id = $this->db->insert_id();
			
			$total_amount_balance = @$row_loan_atm['total_amount_balance'] - @str_replace(',','',$transaction_amount);
			
			$loan_amount_balance = @$row_loan_atm['total_amount_approve'] - $total_amount_balance;
			
			$data_insert = array();
			$data_insert['total_amount_balance'] = @$total_amount_balance;
			$this->db->where('loan_atm_id',@$loan_atm_id);
			$this->db->update('coop_loan_atm',$data_insert);
			
			$atm_transaction = array();
			$atm_transaction['loan_atm_id'] = @$loan_atm_id;
			$atm_transaction['loan_amount_balance'] = @$loan_amount_balance;
			$atm_transaction['transaction_datetime'] = date('Y-m-d H:i:s');
			$this->loan_libraries->atm_transaction($atm_transaction);			
			
			$data_insert = array();			
			$data_insert['loan_id'] = @$loan_id;
			$data_insert['date_transfer'] =  date('Y-m-d H:i:s');
			$data_insert['createdatetime'] = date('Y-m-d H:i:s');
			$data_insert['admin_id'] = '';
			$data_insert['transfer_status'] = '0';
			$data_insert['pay_type'] = '2'; //ATM
			$this->db->insert('coop_loan_atm_transfer', $data_insert);	
			return true;	
		}
	}
	
	
	//บันทึกการถอนเงินจากบัญชี เพื่อเป็นการโอน
	public function saveTransferWithdrawlTxnSaving($from_acct_no,$to_acct_no,$card_no,$transaction_amount,$feeAmount1=0)
	{
		//get member_id
		$member_id = $this->getMemberId($from_acct_no);
		
		$this->db->select('*');
		$this->db->from('coop_account_transaction');
		$this->db->where("account_id = '".$from_acct_no."'");
		$this->db->order_by('transaction_time DESC, transaction_id DESC');
		$this->db->limit(1);
		$row = $this->db->get()->result_array();
		if(!empty($row)){
			$balance = $row[0]['transaction_balance'];
			$balance_no_in = $row[0]['transaction_no_in_balance'];
		}else{
			$balance = 0;
			$balance_no_in = 0;
		}
		
		$money = @$transaction_amount;
		$sum = @$balance - @$money;
		$sum_no_in = @$balance_no_in - @$money;
		if($sum_no_in <= 0 ){$sum_no_in = 0;}
		if($sum > 0) {	
			$data_insert = array();
			$data_insert['transaction_time'] = date('Y-m-d H:i:s');
			$data_insert['transaction_list'] = 'XW';
			$data_insert['transaction_withdrawal'] = @$money;
			$data_insert['transaction_deposit'] = '';
			$data_insert['transaction_balance'] = @$sum;
			$data_insert['transaction_no_in_balance'] = @$sum_no_in;
			$data_insert['member_id_atm'] = @$member_id;
			$data_insert['account_id'] = @$from_acct_no;
			$data_insert['ref_no'] = @$to_acct_no;
			$this->db->insert('coop_account_transaction', $data_insert);
			
			
		}
		
		if($feeAmount1 > 0){
			$sum = $sum - $feeAmount1;
			$sum_no_in = $sum_no_in - $feeAmount1;
			$data_insert = array();
			$data_insert['transaction_time'] = date('Y-m-d H:i:s');
			$data_insert['transaction_list'] = 'CM/FE';
			$data_insert['transaction_withdrawal'] = $feeAmount1;
			$data_insert['transaction_deposit'] = '';
			$data_insert['transaction_balance'] = $sum;
			$data_insert['transaction_no_in_balance'] = $sum_no_in;
			$data_insert['member_id_atm'] = @$member_id;
			$data_insert['account_id'] = @$from_acct_no;
			
			$this->db->insert('coop_account_transaction', $data_insert);
		}
		
		return true;
	}
	
	//บันทึกการการโอนเงินไปยังบัญชีภายในบัตร
	public function saveTransferWithlnCardTxn($from_acct_no,$to_acct_no,$card_no,$from_acct_type,$transaction_amount,$trace_no,$term_seq_id)
	{
		//from_acct_type 00=Unspecified ,01=loan atm,11=saving
		if($from_acct_type == '01'){
			$this->saveTransferWithlnCardTxnLoan($from_acct_no,$to_acct_no,$card_no,$transaction_amount,$trace_no,$term_seq_id);
		}
		
		if($from_acct_type == '11' || $from_acct_type == '00'){			
			$this->saveTransferWithlnCardTxnSaving($from_acct_no,$to_acct_no,$card_no,$transaction_amount);
		}
	}
	
	//บันทึกการการโอนเงินไปยังบัญชีภายในบัตร จากบัญชี
	public function saveTransferWithlnCardTxnSaving($from_acct_no,$to_acct_no,$card_no,$transaction_amount)
	{
		//get member_id
		$member_id = $this->getMemberId($from_acct_no);
		
		//บันทึกการถอนเงินจากบัญชี เพื่อเป็นการโอน
		$saveTransferWithdrawlTxnSaving = $this->saveTransferWithdrawlTxnSaving($from_acct_no,$to_acct_no,$card_no,$transaction_amount);
		if($saveTransferWithdrawlTxnSaving){
			$this->db->select('*');
			$this->db->from('coop_account_transaction');
			$this->db->where("account_id = '".$to_acct_no."'");
			$this->db->order_by('transaction_time DESC, transaction_id DESC');
			$this->db->limit(1);
			$row = $this->db->get()->result_array();
			if(!empty($row)){
				$balance = $row[0]['transaction_balance'];
				$balance_no_in = $row[0]['transaction_no_in_balance'];
			}else{
				$balance = 0;
				$balance_no_in = 0;
			}
			$money = @$transaction_amount;
			$sum = $balance + $money ;
			$sum_no_in = $balance_no_in + $money ;
			if($sum > 0) {	
				$data_insert = array();
				$data_insert['transaction_time'] = date('Y-m-d H:i:s');
				$data_insert['transaction_list'] = 'XD';
				$data_insert['transaction_withdrawal'] = '';
				$data_insert['transaction_deposit'] = @$money;
				$data_insert['transaction_balance'] = @$sum;
				$data_insert['transaction_no_in_balance'] = @$sum_no_in;
				$data_insert['member_id_atm'] = @$member_id;
				$data_insert['account_id'] = @$to_acct_no;
				$data_insert['ref_no'] = @$from_acct_no;
				$this->db->insert('coop_account_transaction', $data_insert);
			}

		}
	}

	//บันทึกการการโอนเงินไปยังบัญชีภายในบัตร จากเงินกู้ ATM
	public function saveTransferWithlnCardTxnLoan($from_acct_no,$to_acct_no,$card_no,$transaction_amount,$trace_no,$term_seq_id)
	{
		//get member_id
		$member_id = $this->getMemberId($from_acct_no);
		
		//บันทึกการถอนเงินจากบัญชี
		$saveWithdrawlTxnLoan = $this->saveWithdrawlTxnLoan($from_acct_no,$card_no,$transaction_amount,$trace_no,$term_seq_id);
		if($saveWithdrawlTxnLoan){
			$this->db->select('*');
			$this->db->from('coop_account_transaction');
			$this->db->where("account_id = '".$to_acct_no."'");
			$this->db->order_by('transaction_id DESC');
			$this->db->limit(1);
			$row = $this->db->get()->result_array();
			if(!empty($row)){
				$balance = $row[0]['transaction_balance'];
				$balance_no_in = $row[0]['transaction_no_in_balance'];
			}else{
				$balance = 0;
				$balance_no_in = 0;
			}
			
			$money = @$transaction_amount;
			$sum = $balance + $money ;
			$sum_no_in = $balance_no_in + $money ;
			if($sum > 0) {	
				$data_insert = array();
				$data_insert['transaction_time'] = date('Y-m-d H:i:s');
				$data_insert['transaction_list'] = 'XD';
				$data_insert['transaction_withdrawal'] = '';
				$data_insert['transaction_deposit'] = @$money;
				$data_insert['transaction_balance'] = @$sum;
				$data_insert['transaction_no_in_balance'] = @$sum_no_in;
				$data_insert['member_id_atm'] = @$member_id;
				$data_insert['account_id'] = @$to_acct_no;
				$this->db->insert('coop_account_transaction', $data_insert);
			}
			
		}
	}

	//หารหัสสมาชิก
	public function getMemberId($account_id){
		$this->db->select(array('*'));
		$this->db->from('coop_maco_account');	
		$this->db->where("account_id = '".@$account_id."'");	
		$this->db->limit(1);	
		$rs_account = $this->db->get()->result_array();
		$row_account = @$rs_account[0];
		
		$this->db->select(array('*'));
		$this->db->from('coop_loan_atm');	
		$this->db->where("account_id = '".@$account_id."'");	
		$this->db->limit(1);	
		$rs_loan_atm = $this->db->get()->result_array();
		$row_loan_atm = @$rs_loan_atm[0];
		
		if(!empty($row_account)){
			$member_id = @$row_account['mem_id'];
		}else if(!empty($row_loan_atm)){
			$member_id = @$row_loan_atm['member_id'];
		}else{
			$member_id = '';
		}
		
		return $member_id;
	}	
	
	//reversal ต้องคืนเงินให้ลูกค้า traceNo
	public function reversal_refund($trace_no,$from_acct_type ){		
		$this->db->select(array('fromAcctType','messageType','traceNo','termSeqID','fromAcctNo','cardNo','transactionAmount'));
		$this->db->from('message_request_atm');		
		$this->db->where("traceNo = '".$trace_no."'");		
		$this->db->limit(1);
		$rs_request = $this->db->get()->result_array();
		$row_request = @$rs_request[0];
		$card_no = @$row_request['cardNo'];//หมายเลขบัตร ATM
		$from_acct_no = @$row_request['fromAcctNo'];//เลขบัญชีธนาคาร
		$term_seq_id = @$row_request['termSeqID'];//
		
		//reversal ค่าธรรมเนียม
		$this->db->select(array('feeAmount1'));
		$this->db->from('message_response_atm');		
		$this->db->where("termSeqID = '".$term_seq_id."'");	
		$this->db->order_by('responseID DESC');	
		$this->db->limit(1);
		$rs_response = $this->db->get()->result_array();
		$row_response = @$rs_response[0];		
		$transaction_fee_integer = (int)substr(@$row_response['feeAmount1'],0,4);//จำนวนเต็ม
		$transaction_fee_decimal = substr(@$row_response['feeAmount1'],4,2);//ทศนิยม
		$transaction_fee_amount = $transaction_fee_integer.'.'.$transaction_fee_decimal;//ยอดเงินที่ถอน
		
		$transaction_integer = substr(@$row_request['transactionAmount'],0,6);//จำนวนเต็ม
		$transaction_decimal = substr(@$row_request['transactionAmount'],6,2);//ทศนิยม
		$transaction_amount = $transaction_integer.'.'.$transaction_decimal;//ยอดเงินที่ถอน
		$transaction_amount = @$transaction_amount+@$transaction_fee_amount;	

		//get member_id
		$member_id = $this->getMemberId($from_acct_no);
		
		//from_acct_type 00=Unspecified ,01=loan atm,11=saving
		if($from_acct_type == '01'){
			$this->db->select(array('loan_atm_id','member_id','total_amount_approve','total_amount_balance'));
			$this->db->from('coop_loan_atm');
			$this->db->where("account_id = '".@$from_acct_no."' AND loan_atm_status ='1' ");
			//$this->db->where("atm_card_number = '".$card_no."'");
			$this->db->limit(1);
			$rs_loan_atm = $this->db->get()->result_array();
			$row_loan_atm = @$rs_loan_atm[0];
			if(!empty($row_loan_atm)){
				$loan_atm_id = @$row_loan_atm['loan_atm_id'];
				$member_id = @$row_loan_atm['member_id'];				
				
				$this->db->select(array('loan_id'));
				$this->db->from('coop_loan_atm_detail');
				$this->db->where("term_seq_id = '".$term_seq_id."'");
				$this->db->limit(1);
				$rs_atm_detail = $this->db->get()->result_array();
				$row_atm_detail = @$rs_atm_detail[0];
				$loan_id = @$row_atm_detail['loan_id'];
				
				//echo"<pre>";print_r($row_atm_detail);exit;
				if(!empty($row_atm_detail)){
					$this->db->where("loan_id = '".@$loan_id."'");
					$this->db->delete('coop_loan_atm_detail');
					
					$total_amount_balance = @$row_loan_atm['total_amount_balance'] + @$transaction_amount;
			
					$loan_amount_balance = @$row_loan_atm['total_amount_approve'] - $total_amount_balance;
					
					$data_insert = array();
					$data_insert['total_amount_balance'] = @$total_amount_balance;
					$this->db->where('loan_atm_id',@$loan_atm_id);
					$this->db->update('coop_loan_atm',$data_insert);
					
					$atm_transaction = array();
					$atm_transaction['loan_atm_id'] = @$loan_atm_id;
					$atm_transaction['loan_amount_balance'] = @$loan_amount_balance;
					$atm_transaction['transaction_datetime'] = date('Y-m-d H:i:s');
					$this->loan_libraries->atm_transaction($atm_transaction);				
					
					$this->db->where("loan_id = '".@$loan_id."'");
					$this->db->delete('coop_loan_atm_transfer');
				
					$result = true;	
				}else{
					$result = false;
				}	
			}else{
				$result = false;
			}
			
		}
		
		if($from_acct_type == '11' || $from_acct_type == '00'){			
			$this->db->select('*');
			$this->db->from('coop_account_transaction');
			$this->db->where("account_id = '".$from_acct_no."'");
			$this->db->order_by('transaction_time DESC, transaction_id DESC');
			$this->db->limit(1);
			$row = $this->db->get()->result_array();
			if(!empty($row)){
				$balance = $row[0]['transaction_balance'];
				$balance_no_in = $row[0]['transaction_no_in_balance'];
			}else{
				$balance = 0;
				$balance_no_in = 0;
			}
			$money = @$transaction_amount;
			$sum = $balance + $money ;
			$sum_no_in = $balance_no_in + $money ;
			if($sum > 0) {	
				$data_insert = array();
				$data_insert['transaction_time'] = date('Y-m-d H:i:s');
				$data_insert['transaction_list'] = 'REVD';
				$data_insert['transaction_withdrawal'] = '';
				$data_insert['transaction_deposit'] = @$money;
				$data_insert['transaction_balance'] = @$sum;
				$data_insert['transaction_no_in_balance'] = @$sum_no_in;
				$data_insert['member_id_atm'] = @$member_id;
				$data_insert['account_id'] = @$from_acct_no;
				$this->db->insert('coop_account_transaction', $data_insert);
				$result = true;
			}else{
				$result = false;
			}
		}
		return $result;
	}	
	
	//ค่าธรรมเนียมการทำรายการตู้ต่างสหกรณ์ หรือค่าธรรมเนียมทำรายการตู้ต่างสหกรณ์เกินจำนวนครั้ง
	public function other_atm_fee($term_fiid,$term_seq_id,$card_no,$transaction_date,$tran_type,$to_acct_type,$response_code,$from_acct_no){			
		//ธนาคารอื่นๆ
		$arr_other_atm = array(	
								'GSBA',
								'KTBA',
								'ISBT',	
								'RFSC',
								'SCTP',
								'A003',
								'LDSC',
								'D1SC',
								'KUSC',
								'A010',
								'A011',
								'A014',
								'A019',
								'A028',
								'A035',
								'A039',
								'A041',
								'A042',
								'A055',
								'A064',
								'A065'
								);
		
		$amount_fee = 0.00;
		$transaction_mmyy = date('Y').substr(@$transaction_date,0,2);
		if($response_code == '000'){
			if($tran_type == '10'){
				if(in_array($term_fiid,$arr_other_atm)){
					////ใช้จริงแค่นี้	
					//10	Fast Cash & Withdraw	ถอนเงิน (ออมทรัพย์/กู้ฉุกเฉิน)
					//ถอนเงิน (ออมทรัพย์/กู้ฉุกเฉิน)
					$this->db->select(array('use_atm_count','use_atm_over_count_fee'));
					$this->db->from('coop_loan_atm_setting');
					$this->db->limit(1);
					$rs_atm_setting = $this->db->get()->result_array();
					$row_atm_setting = @$rs_atm_setting[0];
					if(!empty($row_atm_setting)){
						$use_atm_count = @$row_atm_setting['use_atm_count']; //ถอนเงิน ฟรี x ครั้งต่อเดือน 
						$use_atm_over_count_fee = @$row_atm_setting['use_atm_over_count_fee']; //เกิน x ครั้ง  นั้นมีค่าบริการ
						//echo $use_atm_count.'|'.$use_atm_over_count_fee.'<hr>';
						$amount_fee = $this->get_amount_fee($transaction_mmyy,$card_no,$arr_other_atm,$tran_type,$use_atm_count,$use_atm_over_count_fee,$from_acct_no);
					}
				}
			}
		}	
		return $amount_fee;
		
	}
	
	public function get_amount_fee($transaction_mmyy,$card_no,$arr_term_fiid,$tran_type,$other_count,$over_count_fee,$from_acct_no){
		$amount_fee = 0.00;
		$where = "";
		if($tran_type == '41'){
			$where = " AND t2.toAcctType IN('00','11')";
		}
		
		if($other_count > 0){
			$this->db->select(array('t1.cardNo','t1.termFIID','t1.termSeqID','count(t1.termSeqID) AS counttermSeqID' ,'t1.transactionDate','t1.fromAcctNo','t1.responseCode'));
			$this->db->from('message_response_atm t1');
			$this->db->join("message_request_atm t2","t1.traceNo = t2.traceNo","left");
			$this->db->where("SUBSTR(t1.transactionDate,1,6) = '".$transaction_mmyy."' 
								AND t1.termFIID IN ('".implode("','",$arr_term_fiid)."') 
								AND t1.cardNo = '".$card_no."' 
								AND t1.tranType = '".$tran_type."' 								
								{$where}");
								
			/*$this->db->where("SUBSTR(t1.transactionDate,1,6) = '".$transaction_mmyy."' 
								AND t1.termFIID IN ('".implode("','",$arr_term_fiid)."') 
								AND t1.cardNo = '".$card_no."' 
								AND t1.tranType = '".$tran_type."' 
								AND t1.fromAcctNo = '".$from_acct_no."' 
								{$where}");*/
			$this->db->group_by("t1.termSeqID");
			$rs_response_atm = $this->db->get()->result_array();
			//echo $this->db->last_query();
			$use_count = 1;
			if(!empty($rs_response_atm)){
				foreach($rs_response_atm AS $key=>$row_atm_setting){
					if(@$row_atm_setting['counttermSeqID'] == '1' && @$row_atm_setting['responseCode'] == '000'){
						$use_count++;
					}
				}
			}
			if($use_count > $other_count){
				$amount_fee = number_format(@$over_count_fee, 2, '.', '');
			}
		}
		
		return $amount_fee;
	}
	
	//ชื่อบัญชีปลายทาง (TH,ENG)
	public function get_acct_name($account_no,$language){
		$acct_name = '';
		$this->db->select(array('*'));
		$this->db->from('coop_maco_account');	
		$this->db->where("account_id = '".@$account_no."'");	
		$this->db->limit(1);	
		$rs_account = $this->db->get()->result_array();
		$row_account = @$rs_account[0];
		if(!empty($row_account)){	
			if($language == 'TH'){
				$acct_name = @$row_account['account_title_name'].@$row_account['account_name'];
			}else{
				$acct_name = @$row_account['account_title_name_eng'].@$row_account['account_name_eng'];
			}
		}else{
			$acct_name = '';
		}
		return $acct_name;
	}

	public function api_test_view()
	{
		$arr_data = array();
		$this->db->select(array('*'));
		$this->db->from('tran_type_atm');			
		$rs_type = $this->db->get()->result_array();
		$row_type = @$rs_type;
		$arr_data['tran_type_atm'] = $row_type;
		$reference_no = 1;
		$requestReferenceNo = date('YYmmdd').sprintf("%06d",$reference_no);
		
		$this->db->select(array('MAX(CAST(traceNo AS int)) AS n_max'));
		$this->db->from('message_request_atm');			
		$rs_request_n = $this->db->get()->result_array();
		$t_no = (int)@$rs_request_n[0]['n_max']+1;
		
		if($t_no == ''){
			$t_no = 1;
		}
		
		$this->db->select(array('MAX(CAST(termSeqID AS int)) AS s_max'));
		$this->db->from('message_request_atm');			
		$rs_request_s = $this->db->get()->result_array();
		$s_no = (int)@$rs_request_s[0]['s_max']+1;
		if($s_no == ''){
			$s_no = 1;
		}
		
		$trace_no = sprintf("%06d",@$t_no);
		$term_seq_id = sprintf("%06d",@$s_no);
		$term_id = 'TD01B770SPKCP999';
		$term_branch_id = '0001';
		$term_region_id = '0010';
		$term_fiid = 'SPKC';
		$coop_code = 'SPKC';
		$card_fiid = 'SPKC';
		$term_type = '30';
		$arr_data['tran_type'][0] = array(
			'requesterSystem'=>'D1',
			'requestReferenceNo'=>@$requestReferenceNo,
			'requestAction'=>'R',
			'coopCode'=>@$coop_code,
			'teamCode'=>'000017',
			'userID'=>'spkc.1',
			'cifID'=>'3026306810867',
			'coopID'=>'001240000000005',
			'accountNo'=>'',
			'channelID'=>'02',
		);
		$arr_data['tran_type'][30] = array(
			'requesterSystem'=>'D1',
			'messageType'=>'0700',
			'tranType'=>'30',
			'fromAcctType'=>'11',//01 11
			'toAcctType'=>'11',
			'traceNo'=>@$trace_no,
			'termFIID'=>@$term_fiid,
			'termID'=>@$term_id,
			'termBranchID'=>@$term_branch_id,
			'termRegionID'=>@$term_region_id,
			'termSeqID'=>@$term_seq_id,
			'termType'=>@$term_type,
			'transactionDate'=> date('md'),//
			'transactionTime'=> date('his'),//
			'coopCode'=>@$coop_code,
			'fromAcctNo'=>'001240000000005',
			'cardFIID'=>@$card_fiid,
			'cardNo'=>'5818950100179990039',//
			'terminalUsageFlag'=>'0',
			'feeAmount1'=>'',
			'commAmount1'=>'',
			'feeAmount2'=>'',
			'commAmount2'=>'',
			'postDate'=>date('md'),//
			'channelID'=>'02'
		);
		
		$arr_data['tran_type'][10] = array(
			'requesterSystem'=>'D1',
			'messageType'=>'0200',
			'tranType'=>'10',
			'fromAcctType'=>'11',//
			'toAcctType'=>'00',
			'traceNo'=>@$trace_no,
			'termFIID'=>@$term_fiid,
			'termID'=>@$term_id,
			'termBranchID'=>@$term_branch_id,
			'termRegionID'=>@$term_region_id,
			'termSeqID'=>@$term_seq_id,
			'termType'=>@$term_type,
			'transactionDate'=> date('md'),//
			'transactionTime'=> date('his'),//
			'transactionAmount'=>'00010000', //คือ กด 100 บาท
			'coopCode'=>@$coop_code,
			'fromAcctNo'=>'001240000000005',
			'cardFIID'=>@$card_fiid,
			'cardNo'=>'5818950100179990039',//
			'terminalUsageFlag'=>'0',
			'feeAmount1'=>'0',
			'commAmount1'=>'0',
			'feeAmount2'=>'0',
			'commAmount2'=>'0',
			'postDate'=>date('md'),//
			'channelID'=>'02',
		);
		
		$arr_data['tran_type'][40] = array(
			'requesterSystem'=>'D1',
			'messageType'=>'0200',
			'tranType'=>'40',
			'fromAcctType'=>'11',//
			'toAcctType'=>'11',
			'traceNo'=>@$trace_no,
			'termFIID'=>@$term_fiid,
			'termID'=>@$term_id,
			'termBranchID'=>@$term_branch_id,
			'termRegionID'=>@$term_region_id,
			'termSeqID'=>@$term_seq_id,
			'termType'=>@$term_type,
			'transactionDate'=> date('md'),//
			'transactionTime'=> date('his'),//
			'transactionAmount'=>'00010000', //คือ กด 100 บาท
			'coopCode'=>@$coop_code,
			'fromAcctNo'=>'001240000000005',
			'toAccountNo'=>'001240000000009',
			'cardFIID'=>@$card_fiid,
			'cardNo'=>'5818950100179990039',//
			'terminalUsageFlag'=>'0',
			'iRusageFlag'=>'0',			
			'postDate'=>date('md'),//
			'channelID'=>'02',
		);
		
		$arr_data['tran_type'][31] = array(
			'requesterSystem'=>'D1',
			'messageType'=>'0200',
			'tranType'=>'31',
			'fromAcctType'=>'11',//
			'toAcctType'=>'11',
			'traceNo'=>@$trace_no,
			'termFIID'=>@$term_fiid,
			'termID'=>@$term_id,
			'termBranchID'=>@$term_branch_id,
			'termRegionID'=>@$term_region_id,
			'termSeqID'=>@$term_seq_id,
			'termType'=>@$term_type,
			'transactionDate'=> date('md'),//
			'transactionTime'=> date('his'),//
			'transactionAmount'=>'00010000', //คือ กด 100 บาท
			'coopCode'=>@$coop_code,
			'fromAcctNo'=>'001240000000005',
			'toAccountNo'=>'001240000000009',
			'cardFIID'=>@$card_fiid,
			'cardNo'=>'5818950100179990039',//
			'terminalUsageFlag'=>'0',
			'iRusageFlag'=>'0',			
			'postDate'=>date('md'),//
			'channelID'=>'02',
		);
		
		$arr_data['tran_type'][41] = array(
			'requesterSystem'=>'D1',
			'messageType'=>'0200',
			'tranType'=>'41',
			'fromAcctType'=>'11',//
			'toAcctType'=>'11',
			'traceNo'=>@$trace_no,
			'termFIID'=>@$term_fiid,
			'termID'=>@$term_id,
			'termBranchID'=>@$term_branch_id,
			'termRegionID'=>@$term_region_id,
			'termSeqID'=>@$term_seq_id,
			'termType'=>@$term_type,
			'transactionDate'=> date('md'),//
			'transactionTime'=> date('his'),//
			'transactionAmount'=>'00010000', //คือ กด 100 บาท
			'coopCode'=>@$coop_code,
			'fromAcctNo'=>'001240000000005',
			'toAccountNo'=>'001240000000009',
			'cardFIID'=>@$card_fiid,
			'cardNo'=>'5818950100179990039',//
			'terminalUsageFlag'=>'0',
			'iRusageFlag'=>'0',			
			'postDate'=>date('md'),//
			'channelID'=>'02',
		);
		
		//echo '<pre>'; print_r($arr_data); echo '</pre>'; exit;
		//$this->libraries->template('api_test/index',$arr_data);
		$this->load->view('api_test/index',$arr_data);
	}		
}
