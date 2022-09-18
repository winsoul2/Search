<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Report_accrued_interest extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
		$this->load->model("Report_accrued_interest_model", "report_accrued");
	}
	function coop_report_accrued_interest()
	{
		$arr_data = array();
		//Get Account Type
		//$arr_data['type_ids'] = $this->db->select(array('type_id', 'type_name'))->from('coop_deposit_type_setting')->where("type_id IN ('11','13')")->order_by("type_seq")->get()->result_array();
		$arr_data['type_ids'] = $this->db->select(array('type_id', 'type_name'))->from('coop_deposit_type_setting')->order_by("type_seq")->get()->result_array();
		$arr_data['month_arr'] = $this->center_function->month_arr();
		$this->libraries->template('report_accrued_interest/coop_report_accrued_interest', $arr_data);
	}

	public function check_report_deposit_month_transaction()
	{
		if ($_POST['start_date']) {
			$start_date_arr = explode('/', $_POST['start_date']);
			$start_day = $start_date_arr[0];
			$start_month = $start_date_arr[1];
			$start_year = $start_date_arr[2];
			$start_year -= 543;
			$start_date = $start_year . '-' . $start_month . '-' . $start_day;
		}
		if ($_POST['end_date']) {
			$end_date_arr = explode('/', $_POST['end_date']);
			$end_day = $end_date_arr[0];
			$end_month = $end_date_arr[1];
			$end_year = $end_date_arr[2];
			$end_year -= 543;
			$end_date = $end_year . '-' . $end_month . '-' . $end_day;
		}
		$where = "";
		if (!empty($_POST['start_date']) && empty($_POST['end_date'])) {
			$where = " AND t1.createdatetime BETWEEN '" . $start_date . " 00:00:00.000' AND '" . $start_date . " 23:59:59.000'";
		} else if (!empty($_POST['start_date']) && !empty($_POST['end_date'])) {
			$where = " AND t1.createdatetime BETWEEN '" . $start_date . " 00:00:00.000' AND '" . $end_date . " 23:59:59.000'";
		}

		$members = $this->db->select("t1.member_id
									")
			->from("coop_deposit_month_transaction as t1")
			->where("1=1 " . $where)
			->get()->result_array();

		if (!empty($members)) {
			echo "success";
		} else {
			echo "";
		}
	}

	function coop_report_accrued_interest_preview()
	{
		$set_param = @$_GET;
		$year = (@$set_param['year']-543);
		$start_date = $year.'-01-01';
		$end_date = $year.'-12-31';
		$set_param['start_date'] = $start_date;
		$set_param['end_date'] = $end_date;
		//echo 'account_id='.$set_param['account_id'].'<br>';
		//$set_param['account_id'] = $set_param['account_id'];
		//echo 'account_id='.$set_param['account_id'].'<br>';
		//exit;
		if(@$set_param['report_type'] == '1'){
			//$arr_data['data'] = $this->report_accrued->get_account_member_type($set_param['type_id'],$end_date);
			$arr_data['data'] = $this->report_accrued->get_account_member_type($set_param);
			//echo '<pre>'; print_r($arr_data['data']); echo '</pre>'; exit;
			
			$arr_data['deposit_type'] = $this->report_accrued->get_deposit_type_name($set_param['type_id']);
			$percent_interest = $this->report_accrued->get_deposit_type_interest($set_param['type_id'],$start_date);
			$days_in_year = $this->report_accrued->get_deposit_type_days($set_param['type_id'],$start_date);
			$arr_data['text_report'] = "ดอกเบี้ยค้างจ่าย ปี ".@$set_param["year"];
			$arr_data['text_interest'] = "อัตราดอกเบี้ย ".$percent_interest."% จำนวน ".$days_in_year." วัน อัตราดอกเบี้ยประจำปี ".@$set_param["year"];
			$arr_data['text_start_date'] = $this->center_function->ConvertToThaiDate($start_date);
			$arr_data['text_end_date'] = $this->center_function->ConvertToThaiDate($end_date);
			if (@$set_param['excel']) {
				$this->preview_libraries->template_preview('report_accrued_interest/coop_report_accrued_interest_excel', $arr_data);
			} else {
				$this->preview_libraries->template_preview('report_accrued_interest/coop_report_accrued_interest_preview', $arr_data);
			}
		}else if(@$set_param['report_type'] == '2'){
			//account_id
			$arr_data['data'] = $this->report_accrued->get_account_member_detail($set_param);
			//echo '<pre>'; print_r($arr_data['data']); echo '</pre>'; exit;
			
			//$arr_data['code_transaction'] = $this->report_accrued->get_code_transaction_config();
			//echo '<pre>'; print_r($arr_code); echo '</pre>'; exit;
			
			$arr_data['deposit_type'] = $this->report_accrued->get_deposit_type_name($set_param['type_id']);
			$percent_interest = $this->report_accrued->get_deposit_type_interest($set_param['type_id'],$start_date);
			$days_in_year = $this->report_accrued->get_deposit_type_days($set_param['type_id'],$start_date);
			
			$arr_data['text_title_1'] = @$_SESSION['COOP_NAME'];
			$arr_data['text_title_2'] = "ระหว่างวันที่ ".$this->center_function->ConvertToThaiDate($start_date,0,0)." - ".$this->center_function->ConvertToThaiDate($end_date,0,0);
			
			if (@$set_param['excel']) {
				$this->preview_libraries->template_preview('report_accrued_interest/coop_report_accrued_interest_detail_excel', $arr_data);
			} else {
				$this->preview_libraries->template_preview('report_accrued_interest/coop_report_accrued_interest_detail_preview', $arr_data);
			}
		}	

	}

}
