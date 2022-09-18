<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Report_new_loan_request extends CI_Controller {
	function __construct()
	{
		parent::__construct();
        $this->load->model('report_new_loan_request_model');
	}
	
	public function index(){
		$arr_data = array();
        $arr_data['loan_type'] = $this->db->get("coop_loan_type")->result_array();
//        echo '<pre>';print_r($arr_data);exit;
		$this->libraries->template('report_new_loan_request/index',$arr_data);
	}

    public function new_loan_request_preview(){
        $arr_data = array();
//        $arr_data['month_short_arr'] = array('1'=>'ม.ค.','2'=>'ก.พ.','3'=>'มี.ค.','4'=>'เม.ย.','5'=>'พ.ค.','6'=>'มิ.ย.','7'=>'ก.ค.','8'=>'ส.ค.','9'=>'ก.ย.','10'=>'ต.ค.','11'=>'พ.ย.','12'=>'ธ.ค.');
        $arr_data['month_arr'] = array('1'=>'มกราคม','2'=>'กุมภาพันธ์','3'=>'มีนาคม','4'=>'เมษายน','5'=>'พฤษภาคม','6'=>'มิถุนายน','7'=>'กรกฎาคม','8'=>'สิงหาคม','9'=>'กันยายน','10'=>'ตุลาคม','11'=>'พฤศจิกายน','12'=>'ธันวาคม');
        $arr_data['loan_type'] = $this->db->where("id = '".$_GET['loan_type']."'")->get("coop_loan_type")->row_array();
        $start_date = $this->center_function->ConvertToSQLDate($_GET['start_date']);
        $end_date = $this->center_function->ConvertToSQLDate($_GET['end_date']);

        $arr_data['start_date'] = $this->center_function->ConvertToThaiDate($start_date,'0');
        $arr_data['end_date'] = $this->center_function->ConvertToThaiDate($end_date,'0');

        if(!empty($_GET['order_by'])){
            if($_GET['order_by'] == 'loan_amount'){
                $arr_data['data'] = $this->report_new_loan_request_model->get_loan_request_loan_amount();
            }else if ($_GET['order_by'] == 'objective'){
                $arr_data['data'] = $this->report_new_loan_request_model->get_loan_request_objective();
            }else{
                $arr_data['data'] = array();
            }
        }

//        echo '<pre>';print_r($arr_data);exit;
        if(!empty($_GET['order_by'])){
            if($_GET['order_by'] == 'objective'){
                if(@$_GET['download']!=""){
                    $this->load->view('report_new_loan_request/new_loan_request_objective_preview',$arr_data);
                }else{
                    $this->preview_libraries->template_preview('report_new_loan_request/new_loan_request_objective_preview',$arr_data);
                }
            }else {
                if(@$_GET['download']!=""){
                    $this->load->view('report_new_loan_request/new_loan_request_preview',$arr_data);
                }else{
                    $this->preview_libraries->template_preview('report_new_loan_request/new_loan_request_preview',$arr_data);
                }
            }
        }

    }
}
