<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Fscct_report extends CI_Controller {
	function __construct() {
        parent::__construct();
        $this->cremation_group_id = 2;
        $this->path = "fscct_report";
        $this->month_arr = array('01'=>'มกราคม','02'=>'กุมภาพันธ์','03'=>'มีนาคม','04'=>'เมษายน','05'=>'พฤษภาคม','06'=>'มิถุนายน','07'=>'กรกฎาคม','08'=>'สิงหาคม','09'=>'กันยายน','10'=>'ตุลาคม','11'=>'พฤศจิกายน','12'=>'ธันวาคม');
		$this->month_short_arr = array('1'=>'ม.ค.','2'=>'ก.พ.','3'=>'มี.ค.','4'=>'เม.ย.','5'=>'พ.ค.','6'=>'มิ.ย.','7'=>'ก.ค.','8'=>'ส.ค.','9'=>'ก.ย.','10'=>'ต.ค.','11'=>'พ.ย.','12'=>'ธ.ค.');
        $this->load->model('Sp_cremation/Cremation', 'cremation');
        $this->load->model('Sp_cremation/Report_cremation', 'report');
	}

    public function index() {}

    public function member(){
        $arr_data = array();
        $arr_data["path"] = $this->path;
        $this->libraries->template('sp_cremation/report_member',$arr_data);
    }

    public function check_member_report() {
        $result = $this->cremation->get_members($this->cremation_group_id, $_POST["status"]);
        echo json_encode($result);
    }

    public function member_pdf() {
        $arr_data = array();
        $condition = array();
        $condition["status"] = $_POST["status"];
        $result = $this->report->get_members($this->cremation_group_id, $condition);
        $arr_data["datas"] = $result["datas"];

        $arr_data["cremation"] = $this->cremation->get_cremation_info($this->cremation_group_id);

        $this->load->view('sp_cremation/report_member_pdf',$arr_data);
    }

    public function member_excel() {
        $arr_data = array();
        $condition = array();
        $condition["status"] = $_POST["status"];
        $result = $this->report->get_members($this->cremation_group_id, $condition);
        $arr_data["datas"] = $result["datas"];

        $arr_data["cremation"] = $this->cremation->get_cremation_info($this->cremation_group_id);

        $this->load->view('sp_cremation/report_member_excel',$arr_data);
    }

    public function register(){
        $arr_data = array();
        $arr_data["path"] = $this->path;
        $this->libraries->template('sp_cremation/report_register',$arr_data);
    }

    public function check_register_report() {
        $result = $this->cremation->check_registers($this->cremation_group_id, $_POST);
        echo json_encode($result);
    }

    public function register_pdf() {
        $arr_data = array();
        $result = $this->report->get_registers($this->cremation_group_id, $_POST);
        $arr_data["datas"] = $result["datas"];

        $arr_data["cremation"] = $this->cremation->get_cremation_info($this->cremation_group_id);

        $this->load->view('sp_cremation/report_register_pdf',$arr_data);
    }

    public function register_excel() {
        $arr_data = array();
        $result = $this->report->get_registers($this->cremation_group_id, $_POST);
        $arr_data["datas"] = $result["datas"];

        $arr_data["cremation"] = $this->cremation->get_cremation_info($this->cremation_group_id);

        $this->load->view('sp_cremation/report_register_excel',$arr_data);
    }

    public function resign(){
        $arr_data = array();
        $arr_data["path"] = $this->path;
        $this->libraries->template('sp_cremation/report_resign',$arr_data);
    }

    public function check_resign_report() {
        $result = $this->cremation->check_resigns($this->cremation_group_id, $_POST);
        echo json_encode($result);
    }

    public function resign_pdf() {
        $arr_data = array();
        $result = $this->report->get_resigns($this->cremation_group_id, $_POST);
        $arr_data["datas"] = $result["datas"];

        $arr_data["cremation"] = $this->cremation->get_cremation_info($this->cremation_group_id);

        $this->load->view('sp_cremation/report_resign_pdf',$arr_data);
    }

    public function resign_excel() {
        $arr_data = array();
        $result = $this->report->get_resigns($this->cremation_group_id, $_POST);
        $arr_data["datas"] = $result["datas"];

        $arr_data["cremation"] = $this->cremation->get_cremation_info($this->cremation_group_id);

        $this->load->view('sp_cremation/report_resign_excel',$arr_data);
    }

    public function request_money(){
        $arr_data = array();
        $arr_data["path"] = $this->path;
        $this->libraries->template('sp_cremation/report_request_money',$arr_data);
    }

    public function check_request_money_report() {
        $result = $this->cremation->check_request_moneys($this->cremation_group_id, $_POST);
        echo json_encode($result);
    }

    public function request_money_pdf() {
        $arr_data = array();
        $result = $this->report->get_request_moneys($this->cremation_group_id, $_POST);
        $arr_data["datas"] = $result["datas"];

        $arr_data["cremation"] = $this->cremation->get_cremation_info($this->cremation_group_id);

        $this->load->view('sp_cremation/report_request_money_pdf',$arr_data);
    }

    public function request_money_excel() {
        $arr_data = array();
        $result = $this->report->get_request_moneys($this->cremation_group_id, $_POST);
        $arr_data["datas"] = $result["datas"];

        $arr_data["cremation"] = $this->cremation->get_cremation_info($this->cremation_group_id);

        $this->load->view('sp_cremation/report_request_money_excel',$arr_data);
    }

    public function registration_period() {
        $arr_data = array();
        $arr_data["path"] = $this->path;
        $this->libraries->template('sp_cremation/report_registration_period',$arr_data);
    }

    public function check_registration_period_report() {
        $result = $this->cremation->check_registration_period($this->cremation_group_id, $_POST);
        echo json_encode($result);
    }

    public function registration_period_pdf() {
        $arr_data = array();
        $result = $this->report->get_registration_periods($this->cremation_group_id, $_POST);
        $arr_data["datas"] = $result["datas"];

        $arr_data["cremation"] = $this->cremation->get_cremation_info($this->cremation_group_id);

        $this->load->view('sp_cremation/report_registration_period_pdf',$arr_data);
    }

    public function registration_period_excel() {
        $arr_data = array();
        $result = $this->report->get_registration_periods($this->cremation_group_id, $_POST);
        $arr_data["datas"] = $result["datas"];

        $arr_data["cremation"] = $this->cremation->get_cremation_info($this->cremation_group_id);

        $this->load->view('sp_cremation/report_registration_period_excel',$arr_data);
    }

    public function fee_charge() {
        $arr_data = array();

        $conditions = array();
        $status = array();
        $status[] = 1;
        $conditions["status"] = $status;
        $periods = $this->cremation->get_registration_period($this->cremation_group_id, $conditions);

        $arr_data['periods'] = $periods["datas"];
        $arr_data["path"] = $this->path;
        $this->libraries->template('sp_cremation/report_fee_charge',$arr_data);
    }

    public function check_fee_charge_report() {
        $result = $this->report->check_fee_charge_report($this->cremation_group_id, $_POST);
        echo json_encode($result);
    }

    public function fee_charge_pdf() {
        $arr_data = array();

        $conditions = array();
        $conditions["status"] = array(1,2,4);
        if(!empty($_POST["year"])) $conditions["year"] = $_POST["year"];
        if(!empty($_POST["cremation_member_id"])) $conditions["cremation_member_id"] = $_POST["cremation_member_id"];
        if(!empty($_POST["period_id"])) $conditions["period_id"] = $_POST["period_id"];
        $result = $this->cremation->get_debts($this->cremation_group_id, $conditions);
        $arr_data["datas"] = $result["datas"];

        $arr_data["cremation"] = $this->cremation->get_cremation_info($this->cremation_group_id);

        $this->load->view('sp_cremation/report_fee_charge_pdf',$arr_data);
    }

    public function fee_charge_excel() {
        $arr_data = array();

        $conditions = array();
        $conditions["status"] = array(1,2,4);
        if(!empty($_POST["year"])) $conditions["year"] = $_POST["year"];
        if(!empty($_POST["cremation_member_id"])) $conditions["cremation_member_id"] = $_POST["cremation_member_id"];
        if(!empty($_POST["period_id"])) $conditions["period_id"] = $_POST["period_id"];
        $result = $this->cremation->get_debts($this->cremation_group_id, $conditions);
        $arr_data["datas"] = $result["datas"];

        $arr_data["cremation"] = $this->cremation->get_cremation_info($this->cremation_group_id);

        $this->load->view('sp_cremation/report_fee_charge_excel',$arr_data);
    }
}
