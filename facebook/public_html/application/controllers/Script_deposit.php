<?php
class Script_deposit extends CI_Controller{

	public function __construct()
	{
		parent::__construct();
		$this->load->model("Report_accrued_interest_model", "report_accrued");
	}

	public function index(){

	}

	public function run_date_close_account(){
		$result = $this->report_accrued->update_close_account_date();		
	}
	
	//เพิ่มข้อมูลดอกเบี้ยเงินฝากและยอดยอกมา
	public function add_transaction_acc_int(){
		$type_id = '13';	//ประเภทเงินฝาก
		$date_interest = '2020-12-31'; //วันที่คำนวณดอกเบี้ย
		$result = $this->report_accrued->insert_transaction_acc_int($type_id,$date_interest,'','');		
	}
	
	/*เดี๋ยวต้องมาทำส่วนของ  รันปีถัดไปอัตโนมัติ เมื่อถึงวันที่ 31 ธ.ค. ของทุกปี  ให้รัน sctipt ปีถัดไปเลย*/
}
