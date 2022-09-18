<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Report_summary_and_purpose extends CI_Controller {
	function __construct()
	{
		parent::__construct();
        $this->load->model('report_summary_and_purpose_model');
	}
	
	public function index(){
		$arr_data = array();
        $arr_data['loan_type'] = $this->db->get("coop_loan_type")->result_array();
		$this->libraries->template('report_summary_and_purpose/index',$arr_data);
	}

    public function summary_and_purpose_preview(){
        $arr_data = array();
//        $arr_data['month_short_arr'] = array('1'=>'ม.ค.','2'=>'ก.พ.','3'=>'มี.ค.','4'=>'เม.ย.','5'=>'พ.ค.','6'=>'มิ.ย.','7'=>'ก.ค.','8'=>'ส.ค.','9'=>'ก.ย.','10'=>'ต.ค.','11'=>'พ.ย.','12'=>'ธ.ค.');
        $arr_data['month_arr'] = array('1'=>'มกราคม','2'=>'กุมภาพันธ์','3'=>'มีนาคม','4'=>'เมษายน','5'=>'พฤษภาคม','6'=>'มิถุนายน','7'=>'กรกฎาคม','8'=>'สิงหาคม','9'=>'กันยายน','10'=>'ตุลาคม','11'=>'พฤศจิกายน','12'=>'ธันวาคม');
        $arr_data['loan_type'] = $this->db->where("id = '".$_GET['loan_type']."'")->get("coop_loan_type")->row_array();
        $start_date = $this->center_function->ConvertToSQLDate($_GET['start_date']);
        $end_date = $this->center_function->ConvertToSQLDate($_GET['end_date']);

        $arr_data['start_date'] = $this->center_function->ConvertToThaiDate($start_date,'0');
        $arr_data['end_date'] = $this->center_function->ConvertToThaiDate($end_date,'0');

        $get_data = $this->report_summary_and_purpose_model->get_loan_summary_and_purpose();
        $arr_data['data'] = $get_data['data'];
        $arr_data['loan_reason'] = $get_data['loan_reason'];
        $arr_data['count_loan_reason'] = count($get_data['loan_reason']);

        if(@$_GET['download']!=""){
            $this->load->view('report_summary_and_purpose/summary_and_purpose_preview',$arr_data);
        }else{
            $this->preview_libraries->template_preview('report_summary_and_purpose/summary_and_purpose_preview',$arr_data);
        }
    }
}
