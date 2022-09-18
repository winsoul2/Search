<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Report_life_insurance extends CI_Controller {
	function __construct()
	{
		parent::__construct();
	}

    public function coop_report_life_insurance() {
        $arr_data = array();
        $types = $this->db->select("*")->from("coop_benefits_type")->get()->result_array();
        $arr_data["types"] = $types;
		$this->libraries->template('report_life_insurance/coop_report_life_insurance',$arr_data);
    }

	public function check_life_insurance() {
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

		$transactions = $this->db->select("*")
									->from("coop_life_insurance")
									->where("insurance_status = '1' AND insurance_date BETWEEN '".$start_date." 00:00:00.000' AND '".$end_date." 23:59:59.000'")
									->get()->result_array();
        if(!empty($transactions)){
            echo "success";
        }else{
            echo $end_date;
        }
	}

	public function coop_report_life_insurance_excel() {
		$arr_data = array();
        $arr_data["datas"] = $this->coop_get_life_insurance_data($_GET);
        $this->load->view('report_life_insurance/coop_report_life_insurance_excel',$arr_data);
	}

	public function coop_get_life_insurance_data($data) {
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
		
		//1)รวมสมัครใหม่+ปรับทุน 
		$arr_data = array();
        $result1 = $this->db->select("
								t1.member_id
								,t2.id_card
								,CONCAT(t3.prename_short,t2.firstname_th,' ',t2.lastname_th) AS full_name
								,IF(t2.sex = 'F','หญิง',IF(t2.sex = 'M','ชาย','')) AS sex
								,t2.birthday
								,t1.insurance_old
								,t1.insurance_amount
								,t1.insurance_new
								,t1.insurance_premium
								,t1.insurance_date	
								")
                                ->from("coop_life_insurance AS t1")
                                ->join("coop_mem_apply AS t2", "t1.member_id = t2.member_id", "left")
								->join("coop_prename AS t3", "t2.prename_id = t3.prename_id", "left")
                                ->where("t1.insurance_status = '1' AND t1.insurance_date BETWEEN '".$start_date." 00:00:00.000' AND '".$end_date." 23:59:59.000'")
                                ->order_by("t1.insurance_date ASC,t1.member_id ASC")
                                ->get()->result_array();
		//echo $this->db->last_query(); 						
		$arr_data['sheet1']['data'] = $result1;	
		$arr_data['sheet1']['type_name'] = 'ปรับทุนประกัน + สมัครใหม่';	
		
		
		//2)สมัครใหม่ หมายถึง สมาชิกยังไม่มีทุนประกันเดิม 
        $result2 = $this->db->select("
								t1.member_id
								,t2.id_card
								,CONCAT(t3.prename_short,t2.firstname_th,' ',t2.lastname_th) AS full_name
								,IF(t2.sex = 'F','หญิง',IF(t2.sex = 'M','ชาย','')) AS sex
								,t2.birthday
								,t1.insurance_old
								,t1.insurance_amount
								,t1.insurance_new
								,t1.insurance_premium
								,t1.insurance_date	
								")
                                ->from("coop_life_insurance AS t1")
                                ->join("coop_mem_apply AS t2", "t1.member_id = t2.member_id", "left")
								->join("coop_prename AS t3", "t2.prename_id = t3.prename_id", "left")
                                ->where("t1.insurance_status = '1' AND t1.insurance_date BETWEEN '".$start_date." 00:00:00.000' AND '".$end_date." 23:59:59.000' AND t1.insurance_old = 0")
                                ->order_by("t1.insurance_date ASC,t1.member_id ASC")
                                ->get()->result_array();
		$arr_data['sheet2']['data'] = $result2;	
		$arr_data['sheet2']['type_name'] = 'สมัครใหม่';	
		
		//3)ปรับทุน หมายถึง สมาชิกที่มีทุนประกันเดิม
        $result3 = $this->db->select("
								t1.member_id
								,t2.id_card
								,CONCAT(t3.prename_short,t2.firstname_th,' ',t2.lastname_th) AS full_name
								,IF(t2.sex = 'F','หญิง',IF(t2.sex = 'M','ชาย','')) AS sex
								,t2.birthday
								,t1.insurance_old
								,t1.insurance_amount
								,t1.insurance_new
								,t1.insurance_premium
								,t1.insurance_date	
								")
                                ->from("coop_life_insurance AS t1")
                                ->join("coop_mem_apply AS t2", "t1.member_id = t2.member_id", "left")
								->join("coop_prename AS t3", "t2.prename_id = t3.prename_id", "left")
                                ->where("t1.insurance_status = '1' AND t1.insurance_date BETWEEN '".$start_date." 00:00:00.000' AND '".$end_date." 23:59:59.000' AND t1.insurance_old <> 0")
                                ->order_by("t1.insurance_date ASC,t1.member_id ASC")
                                ->get()->result_array();
		$arr_data['sheet3']['data'] = $result3;	
		$arr_data['sheet3']['type_name'] = 'ปรับทุนประกัน';			
									
		return $arr_data;
    }
}
