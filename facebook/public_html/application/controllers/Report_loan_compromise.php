<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Report_loan_compromise extends CI_Controller {
	function __construct()
	{
		parent::__construct();
	}
	
	public function coop_report_loan(){
		$arr_data = array();
		$this->db->select(array('id','loan_type'));
		$this->db->from('coop_loan_type');
		$this->db->order_by('order_by ASC');
		$rs_loan_type = $this->db->get()->result_array();
		
		$loan_type = array();
        $loan_name = array();
		if(!empty($rs_loan_type)){
			foreach($rs_loan_type as $key => $row_loan_type){
				$loan_type[$row_loan_type['id']] = @$row_loan_type['loan_type'];
                $this->db->order_by("order_by asc");
                $tmp_loan_name = $this->db->get_where("coop_loan_name", array(
                    "loan_type_id" => $row_loan_type['id']
                ))->result_array();
                $loan_name[$row_loan_type['id']] = $tmp_loan_name;


			}
		}
		$arr_data['loan_type'] = $loan_type;
        $arr_data['loan_name'] = $loan_name;
        if($_GET['dev']=='dev'){
            echo '<pre>';print_r($arr_data);exit;
        }
		$this->libraries->template('report_loan_compromise/coop_report_loan',$arr_data);
	}

    function check_report_loan(){
        if(@$_POST['report_date'] != ''){
            $date_arr = explode('/',@$_POST['report_date']);
            $day = (int)@$date_arr[0];
            $month = (int)@$date_arr[1];
            $year = (int)@$date_arr[2];
            $year -= 543;
            $s_date = $year.'-'.sprintf("%02d",@$month).'-'.sprintf("%02d",@$day).' 00:00:00.000';
            $e_date = $year.'-'.sprintf("%02d",@$month).'-'.sprintf("%02d",@$day).' 23:59:59.000';
            $where = " AND t1.createdatetime BETWEEN '".$s_date."' AND '".$e_date."'";
        }else{
            if(@$_POST['month']!='' && @$_POST['year']!=''){
                $day = '';
                $month = $_POST['month'];
                $year = ($_POST['year']-543);
                $s_date = $year.'-'.sprintf("%02d",@$month).'-01'.' 00:00:00.000';
                $e_date = date('Y-m-t',strtotime($s_date)).' 23:59:59.000';
                $where = " AND t1.createdatetime BETWEEN '".$s_date."' AND '".$e_date."'";
            }else{
                $day = '';
                $month = '';
                $year = (@$_POST['year']-543);
                $where = " AND t1.createdatetime BETWEEN '".$year."-01-01 00:00:00.000' AND '".$year."-12-31 23:59:59.000' ";
            }
        }

        $this->db->select('t1.id as loan_id');
        $this->db->from('coop_loan as t1');
        $this->db->join("coop_mem_apply as t2", "t1.member_id = t2.member_id", "inner");
        $this->db->join("coop_prename as t3", "t2.prename_id = t3.prename_id", "left");
        $this->db->join("coop_loan_reason as t4", "t1.loan_reason = t4.loan_reason_id", "inner");
        $this->db->join("coop_loan_name as t5", "t1.loan_type = t5.loan_name_id", "left");
        $this->db->join("coop_loan_type as t6", "t5.loan_type_id = t6.id", "left");
        $this->db->where("t6.id = '".$_POST['loan_type']."' AND t1.loan_status IN ('1','4') {$where}");
        if(@$_POST['loan_name']!=""){
            $this->db->where("t5.loan_name_id in (".implode(",", $_POST['loan_name']).")");
        }
        $this->db->where("t1.contract_number LIKE '%/%'");
        //$this->db->where("t1.loan_type = '".$_POST['loan_type']."' AND t1.loan_status IN ('1','4') {$where}");
        $this->db->order_by('t1.createdatetime ASC');
        $rs_check = $this->db->get()->result_array();
        $row_check = @$rs_check[0];
//        echo $this->db->last_query();
        if(@$row_check['loan_id'] != ''){
            echo "success";
        }
    }

	function coop_report_loan_emergent_excel(){
		$arr_data = array();
		
		$arr_data = array();
		$this->db->select(array('id','mem_group_name'));
		$this->db->from('coop_mem_group');
		$rs_group = $this->db->get()->result_array();
		$mem_group_arr = array();
		foreach($rs_group as $key => $row_group){
			$mem_group_arr[$row_group['id']] = $row_group['mem_group_name'];
		}
		$arr_data['mem_group_arr'] = $mem_group_arr;
		
		$this->db->select(array('id','loan_type'));
		$this->db->from('coop_loan_type');
		$rs_loan_type = $this->db->get()->result_array();
		$loan_type = array();
		foreach($rs_loan_type as $key => $row_loan_type){
			$loan_type[$row_loan_type['id']] = $row_loan_type['loan_type'];
		}
		$arr_data['loan_type'] = $loan_type;
		
		$this->db->select(array('setting_value'));
		$this->db->from('coop_share_setting');
		$this->db->where("setting_id = '1'");
		$row_share_value = $this->db->get()->result_array();
		$share_value = $row_share_value[0]['setting_value'];
		$arr_data['share_value'] = $share_value;

		$this->load->view('report_loan_compromise/coop_report_loan_emergent_excel',$arr_data);
	}

	function coop_report_loan_emergent_preview(){
		$arr_data = array();
		
		$arr_data = array();
		$this->db->select(array('id','mem_group_name'));
		$this->db->from('coop_mem_group');
		$rs_group = $this->db->get()->result_array();
		$mem_group_arr = array();
		foreach($rs_group as $key => $row_group){
			$mem_group_arr[$row_group['id']] = $row_group['mem_group_name'];
		}
		$arr_data['mem_group_arr'] = $mem_group_arr;
		
		$this->db->select(array('id','loan_type'));
		$this->db->from('coop_loan_type');
		$rs_loan_type = $this->db->get()->result_array();
		$loan_type = array();
		foreach($rs_loan_type as $key => $row_loan_type){
			$loan_type[$row_loan_type['id']] = $row_loan_type['loan_type'];
		}
		$arr_data['loan_type'] = $loan_type;
		
		$this->db->select(array('setting_value'));
		$this->db->from('coop_share_setting');
		$this->db->where("setting_id = '1'");
		$row_share_value = $this->db->get()->result_array();
		$share_value = $row_share_value[0]['setting_value'];
		$arr_data['share_value'] = $share_value;
		
		$arr_data['month_arr'] = array('1'=>'มกราคม','2'=>'กุมภาพันธ์','3'=>'มีนาคม','4'=>'เมษายน','5'=>'พฤษภาคม','6'=>'มิถุนายน','7'=>'กรกฎาคม','8'=>'สิงหาคม','9'=>'กันยายน','10'=>'ตุลาคม','11'=>'พฤศจิกายน','12'=>'ธันวาคม');
		$arr_data['month_short_arr'] = array('1'=>'ม.ค.','2'=>'ก.พ.','3'=>'มี.ค.','4'=>'เม.ย.','5'=>'พ.ค.','6'=>'มิ.ย.','7'=>'ก.ค.','8'=>'ส.ค.','9'=>'ก.ย.','10'=>'ต.ค.','11'=>'พ.ย.','12'=>'ธ.ค.');

		$this->preview_libraries->template_preview('report_loan_compromise/coop_report_loan_emergent_preview',$arr_data);
	}
}
