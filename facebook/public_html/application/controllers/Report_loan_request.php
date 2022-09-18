<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Report_loan_request extends CI_Controller {
	function __construct()
	{
		parent::__construct();
	}


    public function report_loan_request_excel(){
        $arr_data = array();
        $loan_type = $_GET['loan_type'];
        $date_start = $_GET['start_date'];
        $date_end = $_GET['thru_date'];
        $date_start_arr = explode("/", $date_start);
        $date_end_arr = explode("/", $date_end);
        $date_start = ($date_start_arr[2]-543).'-'.$date_start_arr[1].'-'.$date_start_arr[0].' 00:00:00';
        $date_end = ($date_end_arr[2]-543).'-'.$date_end_arr[1].'-'.$date_end_arr[0].' 23:59:59';
        $where = "t1.createdatetime BETWEEN '$date_start' AND '$date_end' AND t1.loan_status != '3'";

        $this->db->select(array('t1.id as loan_id',
            "GROUP_CONCAT(t3.member_id SEPARATOR '&,') as guarantee_person_id",
            "GROUP_CONCAT(CONCAT(IF(t4.prename_short is null, '', t4.prename_short), t3.firstname_th, ' ', t3.lastname_th) SEPARATOR '&,') as guarantee_full_name",
            "GROUP_CONCAT(IF(t5.mem_group_name is null,'', t5.mem_group_name) SEPARATOR '&,') as guarantee_mem_group_name",
            't1.createdatetime'));
        $this->db->from('coop_loan as t1');
        $this->db->join('coop_loan_guarantee_person as t2','t2.loan_id = t1.id', 'inner');
        $this->db->join('coop_mem_apply as t3','t3.member_id = t2.guarantee_person_id', 'left');
        $this->db->join('coop_prename as t4','t4.prename_id = t3.prename_id', 'left');
        $this->db->join('coop_mem_group as t5','t5.id = t3.faction', 'left');
        $this->db->where($where);
        $this->db->where("guarantee_person_id is not null AND guarantee_person_id != ''");
        $this->db->group_by('t1.id');
        $this->db->get();
        $guarantee_person = $this->db->last_query();

        if($_GET['loan_type'] != ''){
            $where .= "AND t11.loan_type_id = '$loan_type'";
            $loan_type_data = $this->db->select('id, loan_type')->from('coop_loan_type')->where("id = $loan_type")->get()->row_array();
            $loan_type_name = $loan_type_data['loan_type'];
        }else{
            $loan_type_name = 'ทั้งหมด';
        }
        $arr_data['loan_type_name'] = $loan_type_name;

        $this->db->select(array('t1.createdatetime', 't1.id as loan_id', 't1.date_start_period', 't2.member_id',
            "CONCAT(IF(t5.prename_short is null, '', t5.prename_short), t2.firstname_th, ' ', t2.lastname_th) as full_name",  "(YEAR(NOW()) - YEAR(t2.birthday)) as age",
            "t2.salary", "t2.share_month",
            "t3.mem_group_id", "t3.mem_group_name",
            "t1.loan_amount","t1.period_amount","t1.money_per_period",
            't1.contract_number',
            "t16.guarantee_person_id",
            "t16.guarantee_full_name",
            "t16.guarantee_mem_group_name",
            't1.pay_type',
            't12.guarantee_type',
            't10.loan_reason','t11.loan_type_id','t11.loan_name_id',
            "t17.total_paid_per_month",
            "t17.max_date_period"
        ));
        $this->db->from('coop_loan as t1');
        $this->db->join('coop_mem_apply as t2','t1.member_id = t2.member_id', 'inner');
        $this->db->join('coop_mem_group as t3','t3.id = t2.faction', 'left');
        $this->db->join('coop_prename as t5','t5.prename_id = t2.prename_id', 'left');

        $this->db->join('coop_loan_reason as t10','t10.loan_reason_id = t1.loan_reason', 'left');
        $this->db->join('coop_loan_name as t11','t11.loan_name_id = t1.loan_type', 'LEFT');
        $this->db->join('coop_loan_guarantee as t12','t12.loan_id = t1.id', 'LEFT');
        $this->db->join('('.$guarantee_person.') as t16','t16.loan_id = t1.id', 'left');
        $this->db->join("(SELECT loan_id,`principal_payment`, `total_paid_per_month` , MAX(date_period) as max_date_period FROM `coop_loan_period` WHERE  date_count = '31' GROUP BY loan_id) as t17",'t17.loan_id = t1.id', 'LEFT');
        $this->db->where($where);
        $this->db->group_by('t1.id');
        $this->db->order_by('t1.createdatetime');
        $data = $this->db->get()->result_array();
//        echo $this->db->last_query();exit;
        $new_datas = array();
        foreach($data as $key => $value){
            $loan_date = substr($value['createdatetime'], 0, 10);
            $show_date = $this->center_function->mydate2date($loan_date) ;
            $value['show_date'] =  $show_date;

            $value['guarantee']['person_id'] = explode("&,", $value['guarantee_person_id']);
            $value['guarantee']['full_name'] = explode("&,", $value['guarantee_full_name']);
            $value['guarantee']['mem_group_name'] = explode("&,", $value['guarantee_mem_group_name']);
            unset($value['guarantee_person_id']);
            unset($value['guarantee_full_name']);
            unset($value['guarantee_mem_group_name']);

            $new_datas[$loan_date][] = $value;
        }
        $arr_data['datas'] = $new_datas;

        $start_date = $this->center_function->ConvertToSQLDate($_GET['start_date']);
        $end_date = $this->center_function->ConvertToSQLDate($_GET['thru_date']);

        $arr_data['start_date'] = $this->center_function->ConvertToThaiDate($start_date,'0');
        $arr_data['end_date'] = $this->center_function->ConvertToThaiDate($end_date,'0');

        if($_GET['dev']=='dev'){
            echo '<pre>'; print_r($arr_data);exit;
        }

        if(@$_GET['download']!=""){
            $this->load->view('report_loan_request/report_loan_request_excel',$arr_data);
        }else{
            $this->preview_libraries->template_preview('report_loan_request/report_loan_request_excel',$arr_data);
        }
    }
}
