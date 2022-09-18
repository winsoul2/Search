<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Report_payment_interest extends CI_Controller {
	function __construct()
	{
		parent::__construct();
		$this->load->model("Report_payment_interest_model", "report_payment_interest");
	}
	public function index()
	{
		$data=$this->report_payment_interest->get_money_type();
		$this->libraries->template('report_payment_interest/index',$data);
	}
	public function check_empty()
	{
		if(@$_POST['start_date']){
			$start_date_arr = explode('/',@$_POST['start_date']);
			$start_day = $start_date_arr[0];
			$start_month = $start_date_arr[1];
			$start_year = $start_date_arr[2];
			$start_year -= 543;
			$start_date = $start_year.'-'.$start_month.'-'.$start_day.' '.'00:00:00';
		}	
		if(@$_POST['end_date']){
			$end_date_arr = explode('/',@$_POST['end_date']);
			$end_day = $end_date_arr[0];
			$end_month = $end_date_arr[1];
			$end_year = $end_date_arr[2];
			$end_year -= 543;
			$end_date = $end_year.'-'.$end_month.'-'.$end_day.' '.'23:59:59';
		}
			$type_id=$_POST['type_id'];
			$data=$this->report_payment_interest->check_empty_report_payment_interest($start_date,$end_date,$type_id);
			if(!empty($data)){
				echo "TRUE";
			}else{
				echo "FALSE";
			}
		
	}
	function get_report_payment_interest(){
		if(@$_POST['start_date']){
			$start_date_arr = explode('/',@$_POST['start_date']);
			$start_day = $start_date_arr[0];
			$start_month = $start_date_arr[1];
			$start_year = $start_date_arr[2];
			$start_year -= 543;
			$start_date = $start_year.'-'.$start_month.'-'.$start_day.' '.'00:00:00';
		}	
		if(@$_POST['end_date']){
			$end_date_arr = explode('/',@$_POST['end_date']);
			$end_day = $end_date_arr[0];
			$end_month = $end_date_arr[1];
			$end_year = $end_date_arr[2];
			$end_year -= 543;
			$end_date = $end_year.'-'.$end_month.'-'.$end_day.' '.'23:59:59';
		}
			$type_id=$_POST['type_id'];
			$data=$this->report_payment_interest->get_data_report_payment_interest($start_date,$end_date,$type_id);
		// echo"<pre>";print_r($data);exit;
			$this->load->view('report_payment_interest/slip_payment_interest_customize',$data);
			// slip_payment_interest_customize.php

	}
}