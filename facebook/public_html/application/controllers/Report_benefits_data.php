<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Report_benefits_data extends CI_Controller {
	function __construct()
	{
		parent::__construct();
	}

	public function coop_report_benefits_slip(){
		$arr_data = array();

		$benefits = $this->db->select("t1.member_id, t1.benefits_approved_amount, t2.firstname_th, t2.lastname_th, t3.prename_full, t4.benefits_name")
								->from("coop_benefits_request as t1")
								->join("coop_mem_apply as t2", "t1.member_id = t2.member_id", "inner")
								->join("coop_prename as t3", "t2.prename_id = t3.prename_id", "left")
								->join("coop_benefits_type as t4", "t1.benefits_type_id = t4.benefits_id", "left")
								->where("t1.benefits_request_id IN (".implode(",", $_GET['benefits_request_id']).")")
								->get()->result_array();

		$datas = array();
        $page = 0;
        $first_page_size = 22;
        $page_size = 28;
        foreach($benefits as $index => $benefit) {
            if($index < $first_page_size) {
                $page = 1;
            } else {
                $page = ceil((($index + 1)-$first_page_size) / $page_size) + 1;
            }
            $datas[$page][] = $benefit;
		}
		

        $arr_data["datas"] = $datas;
		$arr_data["page_all"] = $page;

		$this->preview_libraries->template_preview('report_benefit_data/coop_report_benefits_slip_preview',$arr_data);
	}

	public function coop_report_benefits_paid() {
        $arr_data = array();
        $types = $this->db->select("*")->from("coop_benefits_type")->get()->result_array();
        $arr_data["types"] = $types;
		$this->libraries->template('report_benefit_data/coop_report_benefits_payment',$arr_data);
	}

	public function check_benefits_payment() {
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

        if(!empty($_POST["benefits_id"])) {
            $where = " AND coop_benefits_request.benefits_type_id = '".$_POST["benefits_id"]."'";
        }

		$transactions = $this->db->select("*")
                                    ->from("coop_benefits_transfer")
                                    ->join("coop_benefits_request", "coop_benefits_transfer.benefits_request_id = coop_benefits_request.benefits_request_id", "inner")
									->where("coop_benefits_transfer.transfer_status = '0' AND coop_benefits_transfer.date_transfer BETWEEN '".$start_date." 00:00:00.000' AND '".$end_date." 23:59:59.000'".$where)
									->get()->result_array();
        if(!empty($transactions)){
            echo "success";
        }else{
            echo $end_date;
        }
	}

	public function coop_report_benefits_payment_preview() {
		$arr_data = array();
        $benefits = $this->coop_get_benefits_payment_data($_GET);

        $datas = array();
        $page = 0;
        $first_page_size = 22;
        $page_size = 28;
        foreach($benefits as $index => $benefit) {
            if($index < $first_page_size) {
                $page = 1;
            } else {
                $page = ceil((($index + 1)-$first_page_size) / $page_size) + 1;
            }
            $datas[$page][] = $benefit;
        }

        $arr_data["datas"] = $datas;
        $arr_data["page_all"] = $page;

        $this->preview_libraries->template_preview('report_benefit_data/coop_report_benefits_payment_preview',$arr_data);
	}

	public function coop_report_benefits_payment_excel() {
		$arr_data = array();
        $benefits = $this->coop_get_benefits_payment_data($_GET);
        $arr_data["datas"] = $benefits;
        $this->load->view('report_benefit_data/coop_report_benefits_payment_excel',$arr_data);
	}

	public function coop_get_benefits_payment_data($data) {
        if($data['start_date']){
            $start_date_arr = explode('/',$data['start_date']);
            $start_day = $start_date_arr[0];
            $start_month = $start_date_arr[1];
            $start_year = $start_date_arr[2];
            $start_year -= 543;
            $start_date = $start_year.'-'.$start_month.'-'.$start_day;
        }
    
        if($data['end_date']){
            $end_date_arr = explode('/',$data['end_date']);
            $end_day = $end_date_arr[0];
            $end_month = $end_date_arr[1];
            $end_year = $end_date_arr[2];
            $end_year -= 543;
            $end_date = $end_year.'-'.$end_month.'-'.$end_day;
        }

        if(!empty($data["benefits_id"])) {
            $where = " AND t2.benefits_type_id = '".$data["benefits_id"]."'";
        }

        $benefits = $this->db->select("t2.member_id, t2.benefits_approved_amount, t3.firstname_th, t3.lastname_th, t4.prename_full, t5.benefits_name")
                                ->from("coop_benefits_transfer as t1")
                                ->join("coop_benefits_request as t2", "t1.benefits_request_id = t2.benefits_request_id", "inner")
                                ->join("coop_mem_apply as t3", "t2.member_id = t3.member_id", "inner")
								->join("coop_prename as t4", "t3.prename_id = t4.prename_id", "left")
								->join("coop_benefits_type as t5", "t2.benefits_type_id = t5.benefits_id", "left")
                                ->where("t1.transfer_status = '0' AND t1.date_transfer BETWEEN '".$start_date." 00:00:00.000' AND '".$end_date." 23:59:59.000'".$where)
                                ->order_by("t2.member_id")
                                ->get()->result_array();
        return $benefits;
    }

    public function coop_report_benefits_approved() {
        $arr_data = array();
        $types = $this->db->select("*")->from("coop_benefits_type")->get()->result_array();
        $arr_data["types"] = $types;
		$this->libraries->template('report_benefit_data/coop_report_benefits_approved',$arr_data);
    }

	public function check_benefits_approved() {
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

        if(!empty($_POST["benefits_id"])) {
            $where = " AND benefits_type_id = '".$_POST["benefits_id"]."'";
        }

		$transactions = $this->db->select("*")
									->from("coop_benefits_request")
									->where("benefits_status in (1,2,4) AND createdatetime BETWEEN '".$start_date." 00:00:00.000' AND '".$end_date." 23:59:59.000'".$where)
									->get()->result_array();
        if(!empty($transactions)){
            echo "success";
        }else{
            echo $end_date;
        }
	}

	public function coop_report_benefits_approved_preview() {
		$arr_data = array();
        $benefits = $this->coop_get_benefits_approved_data($_GET);

        $datas = array();
        $page = 0;
        $first_page_size = 22;
        $page_size = 28;
        foreach($benefits as $index => $benefit) {
            if($index < $first_page_size) {
                $page = 1;
            } else {
                $page = ceil((($index + 1)-$first_page_size) / $page_size) + 1;
            }
            $datas[$page][] = $benefit;
        }

        $arr_data["datas"] = $datas;
        $arr_data["page_all"] = $page;

        $this->preview_libraries->template_preview('report_benefit_data/coop_report_benefits_approved_preview',$arr_data);
	}

	public function coop_report_benefits_approved_excel() {
		$arr_data = array();
        $benefits = $this->coop_get_benefits_approved_data($_GET);
        $arr_data["datas"] = $benefits;
        $this->load->view('report_benefit_data/coop_report_benefits_approved_excel',$arr_data);
	}

	public function coop_get_benefits_approved_data($data) {
        if($data['start_date']){
            $start_date_arr = explode('/',$data['start_date']);
            $start_day = $start_date_arr[0];
            $start_month = $start_date_arr[1];
            $start_year = $start_date_arr[2];
            $start_year -= 543;
            $start_date = $start_year.'-'.$start_month.'-'.$start_day;
        }
        if($data['end_date']){
            $end_date_arr = explode('/',$data['end_date']);
            $end_day = $end_date_arr[0];
            $end_month = $end_date_arr[1];
            $end_year = $end_date_arr[2];
            $end_year -= 543;
            $end_date = $end_year.'-'.$end_month.'-'.$end_day;
        }

        if(!empty($data["benefits_id"])) {
            $where = " AND t2.benefits_type_id = '".$data["benefits_id"]."'";
        }
        $benefits = $this->db->select("t2.member_id, t2.benefits_approved_amount, t3.firstname_th, t3.lastname_th, t4.prename_full, t5.benefits_name")
                                ->from("coop_benefits_request as t2")
                                // ->from("coop_benefits_transfer as t1")
                                // ->join("coop_benefits_request as t2", "t1.benefits_request_id = t2.benefits_request_id", "inner")
                                ->join("coop_mem_apply as t3", "t2.member_id = t3.member_id", "inner")
								->join("coop_prename as t4", "t3.prename_id = t4.prename_id", "left")
								->join("coop_benefits_type as t5", "t2.benefits_type_id = t5.benefits_id", "left")
                                ->where("t2.benefits_status in (1,2,4) AND t2.createdatetime BETWEEN '".$start_date." 00:00:00.000' AND '".$end_date." 23:59:59.000'".$where)
                                ->order_by("t2.member_id")
                                ->get()->result_array();
        return $benefits;
    }
}
