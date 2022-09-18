<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Report_compromise_data extends CI_Controller {
	function __construct() {
        parent::__construct();
        $this->month_arr = array('1'=>'มกราคม','2'=>'กุมภาพันธ์','3'=>'มีนาคม','4'=>'เมษายน','5'=>'พฤษภาคม','6'=>'มิถุนายน','7'=>'กรกฎาคม','8'=>'สิงหาคม','9'=>'กันยายน','10'=>'ตุลาคม','11'=>'พฤศจิกายน','12'=>'ธันวาคม');
    }

    public function coop_report_compromise_payment() {
        $arr_data = array();
        $arr_data["month_arr"] = $this->month_arr;

		$this->libraries->template('report_compromise_data/coop_report_compromise_payment',$arr_data);
    }

    public function coop_report_compromise_payment_preview() {
        $arr_data = array();
        $compromises = $this->coop_get_compromise_payment_data($_GET);

        $datas = array();
        $page = 0;
        $first_page_size = 18;
        $page_size = 22;
        foreach($compromises as $index => $compromises) {
            if($index < $first_page_size) {
                $page = 1;
            } else {
                $page = ceil((($index + 1)-$first_page_size) / $page_size) + 1;
            }
            $datas[$page][] = $compromises;
        }

        $arr_data["datas"] = $datas;
        $arr_data["page_all"] = $page;

        $this->preview_libraries->template_preview('report_compromise_data/coop_report_compromise_payment_preview',$arr_data);
    }

    public function coop_report_compromise_payment_excel() {
        $arr_data = array();
        $compromises = $this->coop_get_compromise_payment_data($_GET);
        $arr_data["datas"] = $compromises;
        $this->load->view('report_compromise_data/coop_report_compromise_payment_excel',$arr_data);
    }

    public function coop_get_compromise_payment_data($data) {
        if($data['start_date']){
            $start_date_arr = explode('/',$_GET['start_date']);
            $start_day = $start_date_arr[0];
            $start_month = $start_date_arr[1];
            $start_year = $start_date_arr[2];
            $start_year -= 543;
            $start_date = $start_year.'-'.$start_month.'-'.$start_day;
        }

        if($data['end_date']){
            $end_date_arr = explode('/',$_GET['end_date']);
            $end_day = $end_date_arr[0];
            $end_month = $end_date_arr[1];
            $end_year = $end_date_arr[2];
            $end_year -= 543;
            $end_date = $end_year.'-'.$end_month.'-'.$end_day;
        }
        $compromises = $this->db->select("t2.contract_number,
                                            t2.loan_status,
                                            t1.type,
                                            t3.payment_date,
                                            t3.principal_payment,
                                            t3.interest,
                                            t3.loan_interest_remain,
                                            t3.receipt_id,
                                            t4.member_id,
                                            t4.firstname_th,
                                            t4.lastname_th,
                                            t5.prename_full,
                                            t6.fund_support
                                        ")
                                ->from("coop_loan_guarantee_compromise as t1")
                                ->join("coop_loan as t2", "t1.loan_id = t2.id", "inner")
                                ->join("coop_finance_transaction as t3", "t1.loan_id = t3.loan_id", "inner")
                                ->join("coop_mem_apply as t4", "t1.member_id = t4.member_id", "inner")
                                ->join("coop_prename as t5", "t4.prename_id = t5.prename_id", "left")
                                ->join("coop_loan_compromise as t6", "t1.compromise_id = t6.id", "inner")
                                ->join("coop_receipt as t7", "t3.receipt_id = t7.receipt_id", "inner")
                                ->where("(t7.receipt_status != 2 OR t7.receipt_status is null) AND t3.payment_date >= '".$start_date."' AND t3.payment_date <= '".$end_date."'")
                                ->order_by("t3.payment_date, t4.member_id, t3.createdatetime")
                                ->get()->result_array();

        return $compromises;
    }

    public function check_report_compromise_payment() {
        if($_POST['start_date']){
            $start_date_arr = explode('/',$_POST['start_date']);
            $start_day = $start_date_arr[0];
            $start_month = $start_date_arr[1];
            $start_year = $start_date_arr[2];
            $start_year -= 543;
            $start_date = $start_year.'-'.$start_month.'-'.$start_day;
        }
    
        if($_POST['end_date']){
            $end_date_arr = explode('/',$_POST['end_date']);
            $end_day = $end_date_arr[0];
            $end_month = $end_date_arr[1];
            $end_year = $end_date_arr[2];
            $end_year -= 543;
            $end_date = $end_year.'-'.$end_month.'-'.$end_day;
        }

        $compromises = $this->db->select("id")
                                ->from("coop_loan_guarantee_compromise as t1")
                                ->join("coop_finance_transaction as t3", "t1.loan_id = t3.loan_id", "inner")
                                ->join("coop_receipt as t7", "t3.receipt_id = t7.receipt_id", "inner")
                                ->where("(t7.receipt_status != 2 OR t7.receipt_status is null) AND t3.payment_date >= '".$start_date."' AND t3.payment_date <= '".$end_date."'")
                                ->get()->result_array();
        if(!empty($compromises)){
            echo "success";
        }else{
            echo "";
        }
    }
}