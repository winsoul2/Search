<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Report_cremation_data extends CI_Controller {


    public  $month_arr;

    public function __construct()
    {

        parent::__construct();
        $this->month_arr = array('1'=>'มกราคม','2'=>'กุมภาพันธ์','3'=>'มีนาคม','4'=>'เมษายน','5'=>'พฤษภาคม','6'=>'มิถุนายน','7'=>'กรกฎาคม','8'=>'สิงหาคม','9'=>'กันยายน','10'=>'ตุลาคม','11'=>'พฤศจิกายน','12'=>'ธันวาคม');
		//Logo
		$setting = $this->db->select("*")->from("coop_setting_cremation_detail")->where("cremation_id = 2 AND start_date <= NOW()")->order_by("start_date DESC")->get()->row();
		$this->logo_path = $setting->logo_path;
    }

    public function coop_report_cremation_request(){
        $arr_data = [];

        $this->libraries->template('report_cremation_data/coop_report_cremation_request',$arr_data);
    }

    public function preview_report_cremation_request(){
        $arr_data = [];

        if(!empty($_GET["filter_type"]) && $_GET["filter_type"] == "date") {
            $cond['t1.createdatetime >='] = date('Y-m-d H:i:s', strtotime( str_replace('/','-', $this->input->get('start_date'))." 00:00:00 -543 year"));
            $cond['t1.createdatetime <='] = date('Y-m-d H:i:s', strtotime( str_replace('/','-', $this->input->get('end_date'))." 23:59:59 -543 year"));
        }

        if(!empty($_GET["status"])) {
            if($_GET["status"] == "pending") {
                $cond['t1.cremation_status ='] = 0;
            } else if ($_GET["status"] == "approved") {
                $cond['t1.member_cremation_id IS NOT NULL'] = NULL;
            } else if ($_GET["status"] == "reject") {
                $cond['t1.cremation_status ='] = 5;
            } else if ($_GET["status"] == "member") {
                $cond['t1.cremation_status IN (6,7,10)'] = NULL;
            }
        }

        $this->db->select("t1.cremation_no, t1.createdatetime, t2.approve_date, t2.ref_member_id, t2.member_cremation_id, t1.cremation_status, t2.mem_type_id, CONCAT(ifnull(t3.prename_full, ''), t2.assoc_firstname, ' ', t2.assoc_lastname ) as fullname, t2.id_card, t2.member_id")
            ->from('coop_cremation_request t1')
            ->join('coop_member_cremation t2', 't1.member_cremation_raw_id = t2.id', 'inner')
            ->join('coop_prename t3', 't2.prename_id = t3.prename_id', 'left');
        if(!empty($cond)) $this->db->where($cond);

        $arr_data['data'] = $this->db->get()->result_array();

        $arr_data['mem_type_id'] = [ 'ไม่ระบุ', 'สมาชิกสามัญ', 'สมาชิกสมทบ'];
        $arr_data['relation'] = [ 'ไม่ระบุ', 'คู่สมรส', 'บุตร/บุตรบุญธรรม', 'บิดา', 'มารดา'];
        $arr_data['status'] = [ 0 => 'รออนุมัติ', 5 => 'ไม่อนุมัติ', 8 => 'เสียชีวิต', 9 => 'ลาออก', 11 => 'ให้ออก'];
        $arr_data['start_date'] = $this->center_function->ConvertToThaiDate(date('Y-m-d', strtotime( str_replace('/','-', $this->input->get('start_date'))." -543 year")));
        $arr_data['end_date'] = $this->center_function->ConvertToThaiDate(date('Y-m-d', strtotime( str_replace('/','-', $this->input->get('end_date'))." -543 year")));

        $this->preview_libraries->template_preview('report_cremation_data/preview_report_cremation_request',$arr_data);
    }

    public function check_report_request(){

        $result = [];

        if(!empty($_GET["filter_type"]) && $_GET["filter_type"] == "date") {
            $cond['t1.createdatetime >='] = date('Y-m-d H:i:s', strtotime( str_replace('/','-', $this->input->get('start_date'))."00:00:00 -543 year"));
            $cond['t1.createdatetime <='] = date('Y-m-d H:i:s', strtotime( str_replace('/','-', $this->input->get('end_date'))."23:59:59 -543 year"));
        }

        if(!empty($_GET["status"])) {
            if($_GET["status"] == "pending") {
                $cond['t1.cremation_status ='] = 0;
            } else if ($_GET["status"] == "approved") {
                $cond['t1.member_cremation_id IS NOT NULL'] = NULL;
            } else if ($_GET["status"] == "reject") {
                $cond['t1.cremation_status ='] = 5;
            }
        }

        $this->db->select('*')->from('coop_cremation_request as t1');
        $this->db->join('coop_member_cremation t2', 't1.member_cremation_raw_id = t2.id', 'inner');
        if(!empty($cond)) $this->db->where($cond);
        $result['status'] = $this->db->get()->num_rows() > 0;

        header('content-type: application/json; charset: utf-8;');
        echo json_encode($result);
        exit;
    }

    public function coop_report_cremation_pay(){
        $arr_data = [];

        $this->libraries->template('report_cremation_data/coop_report_cremation_pay',$arr_data);
    }

    public function preview_report_cremation_pay(){
        $arr_data = [];

        
        if(!empty($_GET["filter_type"]) && $_GET["filter_type"] == "date") {
            $cond['t1.createdatetime >='] = date('Y-m-d H:i:s', strtotime( str_replace('/','-', $this->input->get('start_date'))."00:00:00 -543 year"));
            $cond['t1.createdatetime <='] = date('Y-m-d H:i:s', strtotime( str_replace('/','-', $this->input->get('end_date'))."23:59:59 -543 year"));
        }
        $cond['t1.cremation_status ='] = 0;

        $this->db->select("t1.cremation_no, t1.createdatetime, t2.approve_date, t2.ref_member_id, t2.member_cremation_id, t1.cremation_status, t2.mem_type_id, CONCAT(ifnull(t3.prename_full, ''), t2.assoc_firstname, ' ', t2.assoc_lastname ) as fullname, t1.cremation_pay_amount, t1.cremation_pay_date")
            ->from('coop_cremation_request t1')
            ->join('coop_member_cremation t2', 't1.member_cremation_id = t2.member_cremation_id', 'inner')
            ->join('coop_prename t3', 't2.prename_id = t3.prename_id', 'left');
        $this->db->where($cond);

        $arr_data['data'] = $this->db->get()->result_array();;

        $arr_data['mem_type_id'] = [ 'ไม่ระบุ', 'สมาชิกสามัญ', 'สมาชิกสมทบ'];
        $arr_data['relation'] = [ 'ไม่ระบุ', 'คู่สมรส', 'บุตร/บุตรบุญธรรม', 'บิดา', 'มารดา'];
        $arr_data['status'] = [ 0 => 'รออนุมัติ', 1 => 'ชำระเงินแล้ว'];

        $arr_data['start_date'] = $this->center_function->ConvertToThaiDate(date('Y-m-d', strtotime( str_replace('/','-', $this->input->get('start_date'))." -543 year")));
        $arr_data['end_date'] = $this->center_function->ConvertToThaiDate(date('Y-m-d', strtotime( str_replace('/','-', $this->input->get('end_date'))." -543 year")));

        $this->preview_libraries->template_preview('report_cremation_data/preview_report_cremation_pay',$arr_data);
    }

    public function check_report_pay(){
        $result = [];
        if(!empty($_GET["filter_type"]) && $_GET["filter_type"] == "date") {
            $cond['t1.createdatetime >='] = date('Y-m-d H:i:s', strtotime( str_replace('/','-', $this->input->get('start_date'))."00:00:00 -543 year"));
            $cond['t1.createdatetime <='] = date('Y-m-d H:i:s', strtotime( str_replace('/','-', $this->input->get('end_date'))."23:59:59 -543 year"));
        }
        $cond['t1.cremation_status ='] = 0;

        $this->db->select('*')->from('coop_cremation_request as t1');
        $this->db->where($cond);
        $result['status'] = $this->db->get()->num_rows() > 0;

        header('content-type: application/json; charset: utf-8;');
        echo json_encode($result);
        exit;
    }

    public function coop_report_pass_away() {
        $arr_data = array();
        $this->libraries->template('report_cremation_data/coop_report_cremation_pass_away',$arr_data);
    }

    public function check_report_pass_away() {
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

        $where = "1=1";
        if($_POST['start_date'] != '' AND $_POST['end_date'] == ''){
            $where .= " AND t1.death_date BETWEEN '".$start_date." 00:00:00.000' AND '".$start_date." 23:59:59.000'";
        }else if($_POST['start_date'] != '' AND $_POST['end_date'] != ''){
            $where .= " AND t1.death_date BETWEEN '".$start_date." 00:00:00.000' AND '".$end_date." 23:59:59.000'";
        }

        if(!empty($_POST["status"])) {
            if($_POST["status"] == "pending") {
                $where .= " AND t2.cremation_receive_status = 0";
            } else if ($_POST["status"] == "approved") {
                $where .= " AND t2.cremation_receive_status = 1";
            } else if ($_POST["status"] == "reject") {
                $where .= " AND t2.cremation_receive_status = 2";
            }
        }

        $members = $this->db->select("t1.death_date")
                            ->from("coop_member_cremation as t1")
                            ->join("coop_cremation_request_receive as t2", "t1.member_cremation_id = t2.member_cremation_id AND t2.cremation_receive_status = 1", "inner")
                            ->where($where)
                            ->get()->result_array();
        if(!empty($members)){
            echo "success";
        }
    }

    public function coop_report_pass_away_preview() {
        $arr_data = array();

        $members = $this->get_member_pass_away_data($_GET);

        $datas = array();
        $page = 0;
        $first_page_size = 22;
        $page_size = 28;
        foreach($members as $index => $member) {
            if($index < $first_page_size) {
                $page = 1;
            } else {
                $page = ceil((($index + 1)-$first_page_size) / $page_size) + 1;
            }
            $datas[$page][] = $member;
        }

        $arr_data["datas"] = $datas;
        $arr_data["page_all"] = $page;

        $this->preview_libraries->template_preview('report_cremation_data/coop_report_cremation_pass_away_preview',$arr_data);
    }

    public function coop_report_pass_away_excel() {
        $arr_data = array();
        $arr_data["datas"] = $this->get_member_pass_away_data($_GET);
        $this->load->view('report_cremation_data/coop_report_cremation_pass_away_excel',$arr_data);
    }

    public function get_member_pass_away_data($data) {
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

        $where = "1=1";
        if($data['start_date'] != '' AND $data['end_date'] == ''){
            $where .= " AND t1.death_date BETWEEN '".$start_date." 00:00:00.000' AND '".$start_date." 23:59:59.000'";
        }else if($data['start_date'] != '' AND $data['end_date'] != ''){
            $where .= " AND t1.death_date BETWEEN '".$start_date." 00:00:00.000' AND '".$end_date." 23:59:59.000'";
        }

        if(!empty($_GET["status"])) {
            if($_GET["status"] == "pending") {
                $where .= " AND t2.cremation_receive_status = 0";
            } else if ($_GET["status"] == "approved") {
                $where .= " AND t2.cremation_receive_status = 1";
            } else if ($_GET["status"] == "reject") {
                $where .= " AND t2.cremation_receive_status = 2";
            }
        }

        $members = $this->db->select("t1.member_cremation_id,
                                        t1.assoc_firstname,
                                        t1.assoc_lastname,
                                        t1.member_id,
                                        t1.ref_member_id,
                                        t2.createdatetime,
                                        t2.cremation_receive_amount,
                                        t2.action_fee_percent,
                                        t2.cremation_balance_amount,
                                        t2.adv_payment_balance,
                                        t3.prename_full
                                    ")
                            ->from("coop_member_cremation as t1")
                            ->join("coop_cremation_request_receive as t2", "t1.member_cremation_id = t2.member_cremation_id", "inner")
                            ->join("coop_prename as t3", "t1.prename_id = t3.prename_id","left")
                            ->order_by("t2.createdatetime")
                            ->where($where)
                            ->get()->result_array();
        return $members;
    }

    public function coop_report_resign() {
        $arr_data = array();
        $this->libraries->template('report_cremation_data/coop_report_cremation_resign',$arr_data);
    }

    public function check_report_resign() {
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

        $where = "1=1";
        if ($data["status"] == "approved") {
            if($data['start_date'] != '' AND $data['end_date'] == ''){
                $where .= " AND t1.approved_date BETWEEN '".$start_date." 00:00:00.000' AND '".$start_date." 23:59:59.000'";
            }else if($data['start_date'] != '' AND $data['end_date'] != ''){
                $where .= " AND t1.approved_date BETWEEN '".$start_date." 00:00:00.000' AND '".$end_date." 23:59:59.000'";
            }
        } else {
            if($data['start_date'] != '' AND $data['end_date'] == ''){
                $where .= " AND t1.created_at BETWEEN '".$start_date." 00:00:00.000' AND '".$start_date." 23:59:59.000'";
            }else if($data['start_date'] != '' AND $data['end_date'] != ''){
                $where .= " AND t1.created_at BETWEEN '".$start_date." 00:00:00.000' AND '".$end_date." 23:59:59.000'";
            }
        }

        if(!empty($_POST["status"])) {
            if($_POST["status"] == "pending") {
                $where .= " AND t1.status = 0";
            } else if ($_POST["status"] == "approved") {
                $where .= " AND t1.status IN (1,3)";
            } else if ($_POST["status"] == "reject") {
                $where .= " AND t1.status = 2";
            }
        }

        $members = $this->db->select("t1.id")
                            ->from("coop_cremation_request_resign as t1")
                            ->where($where)
                            ->get()->result_array();
        if(!empty($members)){
            echo "success";
        }
    }

    public function coop_report_resign_preview() {
        $arr_data = array();

        $members = $this->get_cremation_resign_data($_GET);

        $datas = array();
        $page = 0;
        $first_page_size = 22;
        $page_size = 28;
        foreach($members as $index => $member) {
            if($index < $first_page_size) {
                $page = 1;
            } else {
                $page = ceil((($index + 1)-$first_page_size) / $page_size) + 1;
            }
            $datas[$page][] = $member;
        }

        $arr_data["datas"] = $datas;
        $arr_data["page_all"] = $page;

        $this->preview_libraries->template_preview('report_cremation_data/coop_report_cremation_resign_preview',$arr_data);
    }

    public function coop_report_resign_excel() {
        $arr_data = array();
        $arr_data["datas"] = $this->get_cremation_resign_data($_GET);
        $this->load->view('report_cremation_data/coop_report_cremation_resign_excel',$arr_data);
    }

    public function get_cremation_resign_data($data) {
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

        $where = "1=1";
        if ($data["status"] == "approved") {
            if($data['start_date'] != '' AND $data['end_date'] == ''){
                $where .= " AND t1.approved_date BETWEEN '".$start_date." 00:00:00.000' AND '".$start_date." 23:59:59.000'";
            }else if($data['start_date'] != '' AND $data['end_date'] != ''){
                $where .= " AND t1.approved_date BETWEEN '".$start_date." 00:00:00.000' AND '".$end_date." 23:59:59.000'";
            }
        } else {
            if($data['start_date'] != '' AND $data['end_date'] == ''){
                $where .= " AND t1.created_at BETWEEN '".$start_date." 00:00:00.000' AND '".$start_date." 23:59:59.000'";
            }else if($data['start_date'] != '' AND $data['end_date'] != ''){
                $where .= " AND t1.created_at BETWEEN '".$start_date." 00:00:00.000' AND '".$end_date." 23:59:59.000'";
            }
        }

        if(!empty($data["status"])) {
            if($data["status"] == "pending") {
                $where .= " AND t1.status = 0";
            } else if ($data["status"] == "approved") {
                $where .= " AND t1.status IN (1,3)";
            } else if ($data["status"] == "reject") {
                $where .= " AND t1.status = 2";
            }
        }

        $members = $this->db->select("t1.status,
                                        t1.member_cremation_id,
                                        t1.adv_payment_balance,
                                        t1.reason,
                                        t1.created_at,
                                        t1.approved_date,
                                        t2.assoc_firstname,
                                        t2.assoc_lastname,
                                        t2.member_id,
                                        t2.ref_member_id,
                                        t2.mem_type_id,
                                        t3.prename_full,
                                        t4.bank_type,
                                        t4.bank_id,
                                        t4.bank_branch_id,
                                        t4.bank_account_no,
                                        t4.account_id")
                            ->from("coop_cremation_request_resign as t1")
                            ->join("coop_member_cremation as t2", "t1.member_cremation_id = t2.member_cremation_id", "inner")
                            ->join("coop_prename as t3", "t2.prename_id = t3.prename_id", "left")
                            ->join("coop_cremation_transfer as t4", "t1.id = t4.cremation_resign_id", "left")
                            ->where($where)
                            ->get()->result_array();
        return $members;
    }

    public function coop_report_action_fee() {
        $arr_data = array();
        $this->libraries->template('report_cremation_data/coop_report_cremation_action_fee',$arr_data);
    }
    
    public function check_report_action_fee() {
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

        $where = "t1.transfer_status = 1";
        if($_POST['start_date'] != '' AND $_POST['end_date'] == ''){
            $where .= " AND t1.date_transfer BETWEEN '".$start_date." 00:00:00.000' AND '".$start_date." 23:59:59.000'";
        }else if($_POST['start_date'] != '' AND $_POST['end_date'] != ''){
            $where .= " AND t1.date_transfer BETWEEN '".$start_date." 00:00:00.000' AND '".$end_date." 23:59:59.000'";
        }

        $transactions = $this->db->select("t1.cremation_transfer_id")
                            ->from("coop_cremation_transfer as t1")
                            ->where($where)
                            ->get()->result_array();
        if(!empty($transactions)){
            echo "success";
        }
    }

    public function coop_report_action_fee_preview() {
        $arr_data = array();

        $members = $this->get_cremation_action_fee_data($_GET);

        $datas = array();
        $page = 0;
        $first_page_size = 22;
        $page_size = 28;
        foreach($members as $index => $member) {
            if($index < $first_page_size) {
                $page = 1;
            } else {
                $page = ceil((($index + 1)-$first_page_size) / $page_size) + 1;
            }
            $datas[$page][] = $member;
        }

        $arr_data["datas"] = $datas;
        $arr_data["page_all"] = $page;

        $this->preview_libraries->template_preview('report_cremation_data/coop_report_cremation_action_fee_preview',$arr_data);
    }

    public function coop_report_action_fee_excel() {
        $arr_data = array();
        $arr_data["datas"] = $this->get_cremation_action_fee_data($_GET);
        $this->load->view('report_cremation_data/coop_report_cremation_action_fee_excel',$arr_data);
    }

    public function get_cremation_action_fee_data($data) {
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

        $where = "t1.transfer_status = 1";
        if($data['start_date'] != '' AND $data['end_date'] == ''){
            $where .= " AND t1.date_transfer BETWEEN '".$start_date." 00:00:00.000' AND '".$start_date." 23:59:59.000'";
        }else if($data['start_date'] != '' AND $data['end_date'] != ''){
            $where .= " AND t1.date_transfer BETWEEN '".$start_date." 00:00:00.000' AND '".$end_date." 23:59:59.000'";
        }

        $members = $this->db->select("t1.date_transfer,
                                        t1.transfer_status,
                                        t2.member_cremation_id,
                                        t2.cremation_receive_amount,
                                        t2.action_fee_percent,
                                        t3.assoc_firstname,
                                        t3.assoc_lastname,
                                        t3.member_id,
                                        t3.ref_member_id
                                    ")
                            ->from("coop_cremation_transfer as t1")
                            ->join("coop_cremation_request_receive as t2", "t1.cremation_receive_id = t2.cremation_receive_id", "inner")
                            ->join("coop_member_cremation as t3", "t3.member_cremation_id = t2.member_cremation_id", "inner")
                            ->join("coop_prename as t4", "t3.prename_id = t4.prename_id", "left")
                            ->where($where)
                            ->get()->result_array();
        return $members;
    }

    public function coop_report_finance_month() {
        $arr_data = array();
        $this->libraries->template('report_cremation_data/coop_report_cremation_finance_month',$arr_data); 
    }

    public function check_report_finance_month() {
        $where = "1=1";
        if(!empty($_POST["month"]) && !empty($_POST["year"])) {
            $profile_month = $this->db->select("*")->from("coop_finance_month_profile")->where("profile_month = '".$_POST["month"]."' AND profile_year = '".$_POST["year"]."'")->get()->row();
            if(!empty($profile_month)) {
                $profile_id = $profile_month->profile_id;
            } else {
                exit;
            }
        } else {
            exit;
        }
        if(!empty($_POST["type"])) {
            if($_POST["type"] == "completed") $where .= " AND t1.pay_amount <= t1.real_pay_amount";
            if($_POST["type"] == "owe") $where .= " AND t1.pay_amount > t1.real_pay_amount";
        }
        if(!empty($_POST["member_type"])) {
            if($_POST["member_type"] == "ordinary_member") {
                $where .= " AND t2.mem_type_id = 1";
            } else if ($_POST["member_type"] == "assoc_member") {
                $where .= " AND t2.mem_type_id = 2";
            }
        }
        if(!empty($_POST["is_coop_member"])) {
            if($_POST["is_coop_member"] == 1) {
                $where .= " AND t2.type = 1";
            } else if ($_POST["is_coop_member"] == 2){
                $where .= " AND t2.type = 2";
            }
        }
        $where .= " AND t1.profile_id = '".$profile_id."'";
        $transaction = $this->db->select("t1.id")
                                    ->from("coop_cremation_finance_month as t1")
                                    ->join("coop_member_cremation as t2", "t1.member_cremation_id = t2.member_cremation_id", "inner")
                                    ->where($where)
                                    ->get()->row();
        if(!empty($transaction)){
            echo "success";
        }
    }

    public function coop_report_finance_month_preview() {
        $arr_data = array();

        $transactions = $this->get_cremation_finance_month_data($_GET);

        $datas = array();
        $page = 0;
        $first_page_size = 24;
        $page_size = 28;
        $index = 0;
        foreach($transactions as $key => $transaction) {
            if($index < $first_page_size) {
                $page = 1;
            } else {
                $page = ceil((($index + 1)-$first_page_size) / $page_size) + 1;
            }
            $datas[$page][] = $transaction;
            $index++;
        }

        $arr_data["datas"] = $datas;
        $arr_data["page_all"] = $page;

        $this->preview_libraries->template_preview('report_cremation_data/coop_report_cremation_finance_month_preview',$arr_data);
    }

    public function coop_report_finance_month_excel() {
        $arr_data = array();
        $arr_data["datas"] = $this->get_cremation_finance_month_data($_GET);
        $this->load->view('report_cremation_data/coop_report_cremation_finance_month_excel',$arr_data);
    }

    public function get_cremation_finance_month_data($data) {
        $profile_id = null;
        if(!empty($data["month"]) && !empty($data["year"])) {
            $profile_month = $this->db->select("*")->from("coop_finance_month_profile")->where("profile_month = '".$data["month"]."' AND profile_year = '".$data["year"]."'")->get()->row();
            if(!empty($profile_month)) {
                $profile_id = $profile_month->profile_id;
            }
        }
        $transactions = array();
        if(!empty($profile_id)) {
            $where = "1=1";
            if(!empty($data["type"])) {
                if($data["type"] == "completed") $where .= " AND t1.pay_amount <= t1.real_pay_amount";
                if($data["type"] == "owe") $where .= " AND t1.pay_amount > t1.real_pay_amount";
            }
            if(!empty($data["member_type"])) {
                if($data["member_type"] == "ordinary_member") {
                    $where .= " AND t3.mem_type_id = 1";
                } else if ($data["member_type"] == "assoc_member") {
                    $where .= " AND t3.mem_type_id = 2";
                }
            }
            if(!empty($data["is_coop_member"])) {
                if($data["is_coop_member"] == 1) {
                    $where .= " AND t3.type = 1";
                } else if ($data["is_coop_member"] == 2){
                    $where .= " AND t3.type = 2";
                }
            }
            $where .= " AND t1.profile_id = '".$profile_id."'";
            $transactions = $this->db->select("t1.id, t1.member_cremation_id, t1.pay_amount, t1.real_pay_amount, t1.created_at, t5.receipt_id, t3.member_id, t3.assoc_firstname, t3.assoc_lastname")
                                        ->from("coop_cremation_finance_month as t1")
                                        ->join("coop_cremation_finance_month_receipt as t2", "t1.id = t2.finance_month_id", "left")
                                        ->join("coop_member_cremation as t3", "t1.member_cremation_id = t3.member_cremation_id", "inner")
                                        ->join("coop_prename as t4", "t3.prename_id = t4.prename_id", "left")
                                        ->join("coop_cremation_receipt as t5", "t5.id = t2.receipt_id", "left")
                                        ->where($where)
                                        ->get()->result_array();
        }

        $results = array();
        foreach($transactions as $key => $transaction) {
            $results[$transaction["id"]]["create_datetime"] = $transaction["created_at"];
            $results[$transaction["id"]]["member_id"] = $transaction["member_id"];
            $results[$transaction["id"]]["pay_amount"] = $transaction["pay_amount"];
            $results[$transaction["id"]]["real_pay_amount"] = $transaction["real_pay_amount"];
            $results[$transaction["id"]]["firstname_th"] = $transaction["assoc_firstname"];
            $results[$transaction["id"]]["lastname_th"] = $transaction["assoc_lastname"];
            $results[$transaction["id"]]["prename_full"] = $transaction["prename_full"];
            $results[$transaction["id"]]["receipt_id"][] = $transaction["receipt_id"];
            $results[$transaction["id"]]["advance_pay"] = $transaction["advance_pay"];
            $results[$transaction["id"]]["member_cremation_id"] = $transaction["member_cremation_id"];
        }
        return $results;
    }

    public function coop_report_advance_finance_month() {
        $arr_data = array();
        $this->libraries->template('report_cremation_data/coop_report_cremation_advance_finance_month',$arr_data);
    }

    public function check_report_advance_finance_month() {
        $results = $this->db->select("t2.created_at")
                                    ->from("coop_finance_month_profile as t1")
                                    ->join("coop_cremation_finance_month as t2", "t1.profile_id = t2.profile_id", "inner")
                                    ->where("t1.profile_month = '".$_POST["month"]."' AND t1.profile_year = '".$_POST["year"]."'")
                                    ->get()->result_array();
        if(empty($results)) {
            $month = $_POST["month"] - 1;
            $year = $_POST["year"] - 543;
            $thru_date = date("Y-m-t", strtotime($year."-".$month."-01"))." 23:59:59";
            $where = "t1.type = 'CTAP' AND t1.status = 1 AND t1.created_at <= '".$thru_date."'";
            $results = $this->db->select("t1.created_at")
                                        ->from("coop_cremation_advance_payment_transaction as t1")
                                        ->join("coop_member_cremation as t2", "t1.member_cremation_id = t2.member_cremation_id", "inner")
                                        ->join("coop_cremation_request_receive as t4", "t4.cremation_receive_id = t1.cremation_receive_id", "inner")
                                        ->group_by("t1.member_cremation_id")
                                        ->where($where)
                                        ->get()->result_array();
        }

        if(!empty($results)){
            echo "success";
        }
    }

    public function coop_report_advance_finance_month_preview() {
        $arr_data = array();

        $transactions = $this->get_cremation_advance_finance_month_data($_GET);

        $datas = array();
        $page = 0;
        $first_page_size = 24;
        $page_size = 28;
        $index = 0;
        foreach($transactions as $key => $transaction) {
            if($index < $first_page_size) {
                $page = 1;
            } else {
                $page = ceil((($index + 1)-$first_page_size) / $page_size) + 1;
            }
            $datas[$page][] = $transaction;
            $index++;
        }

        $arr_data["datas"] = $datas;
        $arr_data["page_all"] = $page;

        $this->preview_libraries->template_preview('report_cremation_data/coop_report_cremation_advance_finance_month_preview',$arr_data);
    }

    public function coop_report_advance_finance_month_excel() {
        $arr_data = array();
        $arr_data["datas"] = $this->get_cremation_finance_month_data($_GET);
        $this->load->view('report_cremation_data/coop_report_cremation_advance_finance_month_excel',$arr_data);
    }

    public function get_cremation_advance_finance_month_data($data) {
        $results = $this->db->select("t2.created_at as create_datetime,
                                            t2.pay_amount,
                                            t2.member_cremation_id,
                                            t3.member_id,
                                            t3.assoc_firstname as firstname_th,
                                            t3.assoc_lastname as lastname_th,
                                            t4.prename_full
                                    ")
                                    ->from("coop_finance_month_profile as t1")
                                    ->join("coop_cremation_finance_month as t2", "t1.profile_id = t2.profile_id", "inner")
                                    ->join("coop_member_cremation as t3", "t2.member_cremation_id = t3.member_cremation_id", "inner")
                                    ->join("coop_prename as t4", "t3.prename_id = t4.prename_id", "left")
                                    ->where("t1.profile_month = '".$data["month"]."' AND t1.profile_year = '".$data["year"]."'")
                                    ->get()->result_array();
        if(empty($results)) {
            $month = $data["month"] - 1;
            $year = $data["year"] - 543;
            $thru_date = date("Y-m-t", strtotime($year."-".$month."-01"))." 23:59:59";
            $where = "t1.type = 'CTAP' AND t1.status = 1 AND t1.created_at <= '".$thru_date."'";
            $results = $this->db->select("t1.created_at as create_datetime,
                                                t1.member_cremation_id,
                                                t2.member_id,
                                                t2.assoc_firstname as firstname_th,
                                                t2.assoc_lastname as lastname_th,
                                                t3.prename_full,
                                                SUM(t1.amount) as pay_amount
                                            ")
                                        ->from("coop_cremation_advance_payment_transaction as t1")
                                        ->join("coop_member_cremation as t2", "t1.member_cremation_id = t2.member_cremation_id", "inner")
                                        ->join("coop_prename as t3", "t2.prename_id = t3.prename_id", "left")
                                        ->join("coop_cremation_request_receive as t4", "t4.cremation_receive_id = t1.cremation_receive_id", "inner")
                                        ->group_by("t1.member_cremation_id")
                                        ->where($where)
                                        ->get()->result_array();
        }

        return $results;
    }

    public function coop_report_finance_month_non_member() {
        $arr_data = array();
        $this->libraries->template('report_cremation_data/coop_report_cremation_finance_month_non_member',$arr_data); 
    }

    public function check_report_finance_month_non_member() {
        $where = "t1.status = '1' AND t1.type = 'CTAP'";
        $having = "SUM(t1.amount) > 0";
        if(!empty($_GET["month"]) && !empty($_GET["year"])) {
			$month = $_GET["month"];
			$year = $_GET["year"] - 543;
			$from_date = date("Y-m-d", strtotime($year."-".$month."-01"))." 00:00:00";
			$thru_date = date("Y-m-t", strtotime($year."-".$month."-01"))." 23:59:59";
			$where .= " AND t1.created_at BETWEEN '".$from_date."' AND '".$thru_date."'";
		}
        if(!empty($_POST["type"])) {
            if($_POST["type"] == "completed") $having .= " AND sum(t1.debt) = 0";
            if($_POST["type"] == "owe") $where .= " AND t1.debt > 0";
        }
        $transaction = $this->db->select("t1.id")
                                    ->from("coop_cremation_advance_payment_transaction as t1")
                                    ->join("coop_member_cremation as t3", "t1.member_cremation_id = t3.member_cremation_id AND t3.type = 2", "inner")
                                    ->where($where)
                                    ->group_by("t1.member_cremation_id")
                                    ->having($having)
                                    ->get()->row();
        if(!empty($transaction)){
            echo "success";
        }
    }

    public function coop_report_finance_month_preview_non_member() {
        $arr_data = array();

        $transactions = $this->get_cremation_finance_month_data_non_member($_GET);

        $datas = array();
        $page = 0;
        $first_page_size = 24;
        $page_size = 28;
        $index = 0;
        foreach($transactions as $key => $transaction) {
            if($index < $first_page_size) {
                $page = 1;
            } else {
                $page = ceil((($index + 1)-$first_page_size) / $page_size) + 1;
            }
            $datas[$page][] = $transaction;
            $index++;
        }

        $arr_data["datas"] = $datas;
        $arr_data["page_all"] = $page;

        $this->preview_libraries->template_preview('report_cremation_data/coop_report_cremation_finance_month_preview_non_member',$arr_data);
    }

    public function coop_report_finance_month_excel_non_member() {
        $arr_data = array();
        $arr_data["datas"] = $this->get_cremation_finance_month_data_non_member($_GET);
        $this->load->view('report_cremation_data/coop_report_cremation_finance_month_excel_non_member',$arr_data);
    }

    public function get_cremation_finance_month_data_non_member($data) {
        $where = "t1.status = '1' AND t1.type = 'CTAP'";
        $having = "SUM(t1.amount) > 0";
        if(!empty($_GET["month"]) && !empty($_GET["year"])) {
			$month = $_GET["month"];
			$year = $_GET["year"] - 543;
			$from_date = date("Y-m-d", strtotime($year."-".$month."-01"))." 00:00:00";
			$thru_date = date("Y-m-t", strtotime($year."-".$month."-01"))." 23:59:59";
			$where .= " AND t1.created_at BETWEEN '".$from_date."' AND '".$thru_date."'";
		}
        if(!empty($_GET["type"])) {
            if($_GET["type"] == "completed") $having .= " AND sum(t1.debt) = 0";
            if($_GET["type"] == "owe") $where .= " AND t1.debt > 0";
        }
        $transactions = $this->db->select("t1.id, SUM(t1.amount) as total,
                                            SUM(t1.debt) as sum_debt,
                                            t1.member_cremation_id,
                                            t2.receipt_id,
                                            t2.created_at,
                                            t3.member_id,
                                            t3.ref_member_id,
                                            t3.assoc_firstname,
                                            t3.assoc_lastname,
                                            t4.prename_full,
                                            t5.adv_payment_balance")
                                    ->from("coop_cremation_advance_payment_transaction as t1")
                                    ->join("coop_cremation_advance_payment_receipt as t2", "t2.transaction_id = t1.id AND t2.status = 1", "left")
                                    ->join("coop_member_cremation as t3", "t1.member_cremation_id = t3.member_cremation_id AND t3.type = 2", "inner")
                                    ->join("coop_prename as t4", "t3.prename_id = t4.prename_id", "left")
                                    ->join("coop_cremation_advance_payment as t5", "t1.member_cremation_id = t5.member_cremation_id", "inner")
                                    ->where($where)
                                    ->group_by("t2.receipt_id")
                                    ->having($having)
                                    ->order_by("t2.created_at")
                                    ->get()->result_array();

        $results = array();
        foreach($transactions as $key => $transaction) {
            $results[$transaction["receipt_id"]]["create_datetime"] = $transaction["created_at"];
            $results[$transaction["receipt_id"]]["member_id"] = !empty($transaction["member_id"]) ? $transaction["member_id"] : $transaction["ref_member_id"];
            $results[$transaction["receipt_id"]]["pay_amount"] = $transaction["total"];
            $results[$transaction["receipt_id"]]["real_pay_amount"] = $transaction["total"] - $transaction["sum_debt"];
            $results[$transaction["receipt_id"]]["firstname_th"] = $transaction["assoc_firstname"];
            $results[$transaction["receipt_id"]]["lastname_th"] = $transaction["assoc_lastname"];
            $results[$transaction["receipt_id"]]["prename_full"] = $transaction["prename_full"];
            $results[$transaction["receipt_id"]]["receipt_id"] = $transaction["receipt_id"];
            $results[$transaction["receipt_id"]]["advance_pay"] = $transaction["adv_payment_balance"];
            $results[$transaction["receipt_id"]]["member_cremation_id"][] = $transaction["member_cremation_id"];
        }
        return $results;
    }

    public function coop_report_member_data_history() {
        $arr_data = array();
        $this->libraries->template('report_cremation_data/coop_report_cremation_member_data_history',$arr_data); 
    }

    public function check_report_member_data_history() {
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

        $where = "1=1";
        if($_POST['start_date'] != '' AND $_POST['end_date'] == ''){
            $where .= " AND created_at BETWEEN '".$start_date." 00:00:00.000' AND '".$start_date." 23:59:59.000'";
        }else if($_POST['start_date'] != '' AND $_POST['end_date'] != ''){
            $where .= " AND created_at BETWEEN '".$start_date." 00:00:00.000' AND '".$end_date." 23:59:59.000'";
        }
        $change = $this->db->select("*")->from("coop_member_cremation_data_history")->where($where)->get()->row();
        if(!empty($change)){
            echo "success";
        }
    }

    public function coop_report_member_data_history_preview() {
        $arr_data = array();

        $changes = $this->get_member_data_history($_GET);

        $datas = array();
        $page = 0;
        $first_page_size = 2;
        $page_size = 2;
        $index = 0;
        foreach($changes as $key => $change) {
            if($index < $first_page_size) {
                $page = 1;
            } else {
                $page = ceil((($index + 1)-$first_page_size) / $page_size) + 1;
            }
            $datas[$page][] = $change;
            $index++;
        }

        $arr_data["datas"] = $datas;
        $arr_data["page_all"] = $page;

        $this->preview_libraries->template_preview('report_cremation_data/coop_report_cremation_member_data_history_preview',$arr_data);
    }

    public function coop_report_member_data_history_excel() {
        $arr_data = array();
        $arr_data["datas"] = $this->get_member_data_history($_GET);
        $this->load->view('report_cremation_data/coop_report_cremation_member_data_history_excel',$arr_data);
    }

    public function get_member_data_history($data) {
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
        $where = "1=1";
        if($data['start_date'] != '' AND $data['end_date'] == ''){
            $where .= " AND created_at BETWEEN '".$start_date." 00:00:00.000' AND '".$start_date." 23:59:59.000'";
        }else if($data['start_date'] != '' AND $data['end_date'] != ''){
            $where .= " AND created_at BETWEEN '".$start_date." 00:00:00.000' AND '".$end_date." 23:59:59.000'";
        }

        $changes = $this->db->select("t1.origin_value,
                                        t1.new_value,
                                        t1.input_name,
                                        t1.created_at,
                                        t2.member_cremation_id,
                                        t2.assoc_firstname as firstname_th,
                                        t2.assoc_lastname as lastname_th,
                                        t2.approve_date,
                                        t2.mem_type_id,
                                        t2.assoc_birthday as birth_day,
                                        t2.cur_addr_no as no,
                                        t2.cur_addr_village as village,
                                        t2.cur_addr_moo,
                                        t2.cur_addr_soi,
                                        t2.cur_addr_street,
                                        t2.cur_zip_code,
                                        t2.marry_name,
                                        t2.receiver_1,
                                        t2.receiver_2,
                                        t2.receiver_3,
                                        t2.funeral_manager,
                                        t3.prename_full,
                                        t4.province_name,
                                        t4.province_code,
                                        t5.amphur_name,
                                        t6.district_name,
                                        t7.req_resign_id,
                                        t8.resign_cause_name")
                            ->from("coop_member_cremation_data_history as t1")
                            ->join("coop_member_cremation as t2", "t1.member_cremation_raw_id = t2.id", "inner")
                            ->join("coop_prename as t3", "t2.prename_id = t3.prename_id", "left")
                            ->join("coop_province as t4", "t2.cur_province_id = t4.province_id", "left")
                            ->join("coop_amphur as t5", "t2.cur_amphur_id = t5.amphur_id", "left")
                            ->join("coop_district as t6", "t2.cur_district_id = t6.district_id", "left")
                            ->join("coop_mem_req_resign as t7", "t2.member_id = t7.member_id AND t7.req_resign_status = '1'", "left")
                            ->join("coop_mem_resign_cause as t8", "t7.resign_cause_id = t8.resign_cause_id", "left")
                            ->where($where)
                            ->get()->result_array();
        return $changes;
    }




    public function coop_report_advance_finance_month_paid() {
        $arr_data = array();
        $this->libraries->template('report_cremation_data/coop_report_cremation_advance_finance_month_paid',$arr_data);
    }

    public function check_report_advance_finance_month_paid() {
        $results = $this->db->select("t2.created_at")
                                    ->from("coop_finance_month_profile as t1")
                                    ->join("coop_cremation_finance_month as t2", "t1.profile_id = t2.profile_id AND t2.in_schedule_pay_amount > 0", "inner")
                                    ->where("t1.profile_month = '".$_POST["month"]."' AND t1.profile_year = '".$_POST["year"]."'")
                                    ->get()->result_array();

        if(!empty($results)){
            echo "success";
        }
    }

    public function coop_report_advance_finance_month_paid_preview() {
        $arr_data = array();

        $transactions = $this->get_cremation_advance_finance_month_paid_data($_GET);

        $datas = array();
        $page = 0;
        $first_page_size = 24;
        $page_size = 28;
        $index = 0;
        foreach($transactions as $key => $transaction) {
            if($index < $first_page_size) {
                $page = 1;
            } else {
                $page = ceil((($index + 1)-$first_page_size) / $page_size) + 1;
            }
            $datas[$page][] = $transaction;
            $index++;
        }

        $arr_data["datas"] = $datas;
        $arr_data["page_all"] = $page;

        $this->preview_libraries->template_preview('report_cremation_data/coop_report_cremation_advance_finance_month_paid_preview',$arr_data);
    }

    public function coop_report_advance_finance_month_paid_excel() {
        $arr_data = array();
        $arr_data["datas"] = $this->get_cremation_advance_finance_month_paid_data($_GET);
        $this->load->view('report_cremation_data/coop_report_cremation_advance_finance_month_paid_excel',$arr_data);
    }

    public function get_cremation_advance_finance_month_paid_data($data) {
        $results = $this->db->select("t2.created_at as create_datetime,
                                            t2.pay_amount,
                                            t2.member_cremation_id,
                                            t2.in_schedule_pay_amount,
                                            t3.member_id,
                                            t3.assoc_firstname as firstname_th,
                                            t3.assoc_lastname as lastname_th,
                                            t4.prename_full
                                    ")
                                    ->from("coop_finance_month_profile as t1")
                                    ->join("coop_cremation_finance_month as t2", "t1.profile_id = t2.profile_id AND t2.in_schedule_pay_amount > 0", "inner")
                                    ->join("coop_member_cremation as t3", "t2.member_cremation_id = t3.member_cremation_id", "inner")
                                    ->join("coop_prename as t4", "t3.prename_id = t4.prename_id", "left")
                                    ->where("t1.profile_month = '".$data["month"]."' AND t1.profile_year = '".$data["year"]."'")
                                    ->get()->result_array();

        return $results;
    }

    public function coop_report_refund_register_payment() {
        $arr_data = array();
        $this->libraries->template('report_cremation_data/coop_report_refund_register_payment',$arr_data);
    }

    public function check_report_register_refund() {
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

        $where = "refund_datetime IS NOT NULL";
        if($_POST['start_date'] != '' AND $_POST['end_date'] == ''){
            $where .= " AND refund_datetime BETWEEN '".$start_date." 00:00:00.000' AND '".$start_date." 23:59:59.000'";
        }else if($_POST['start_date'] != '' AND $_POST['end_date'] != ''){
            $where .= " AND refund_datetime BETWEEN '".$start_date." 00:00:00.000' AND '".$end_date." 23:59:59.000'";
        }
        $request = $this->db->select("*")->from("coop_cremation_request")->where($where)->get()->row();
        if(!empty($request)){
            echo "success";
        }
    }

    public function coop_report_register_refund_preview() {
        $arr_data = array();

        $transactions = $this->get_cremation_register_refund($_GET);

        $datas = array();
        $page = 0;
        $first_page_size = 24;
        $page_size = 28;
        $index = 0;
        foreach($transactions as $key => $transaction) {
            if($index < $first_page_size) {
                $page = 1;
            } else {
                $page = ceil((($index + 1)-$first_page_size) / $page_size) + 1;
            }
            $datas[$page][] = $transaction;
            $index++;
        }

        $arr_data["datas"] = $datas;
        $arr_data["page_all"] = $page;

        $this->preview_libraries->template_preview('report_cremation_data/coop_report_register_refund_preview',$arr_data);
    }

    public function coop_report_register_refund_excel() {
        $arr_data = array();
        $arr_data["datas"] = $this->get_cremation_register_refund($_GET);
        $this->load->view('report_cremation_data/coop_report_register_refund_excel',$arr_data);
    }

    public function get_cremation_register_refund($data) {
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

        $where = "t1.refund_datetime IS NOT NULL";
        if($_POST['start_date'] != '' AND $_POST['end_date'] == ''){
            $where .= " AND t1.refund_datetime BETWEEN '".$start_date." 00:00:00.000' AND '".$start_date." 23:59:59.000'";
        }else if($_POST['start_date'] != '' AND $_POST['end_date'] != ''){
            $where .= " AND t1.refund_datetime BETWEEN '".$start_date." 00:00:00.000' AND '".$end_date." 23:59:59.000'";
        }
        $results = $this->db->select("t1.cremation_no, t1.refund_datetime, t1.cremation_pay_amount,
                                        t2.mem_type_id, t2.assoc_firstname, t2.assoc_lastname, t4.prename_full,
                                        t3.bank_type, t3.account_id, t3.bank_account_no")
                            ->from("coop_cremation_request as t1")
                            ->join("coop_member_cremation as t2", "t1.member_cremation_raw_id = t2.id", "inner")
                            ->join("coop_cremation_transfer as t3", "t1.cremation_request_id = t3.cremation_request_id", "inner")
                            ->join("coop_prename as t4", "t2.prename_id = t4.prename_id", "left")
                            ->order_by("t1.refund_datetime")
                            ->where($where)->get()->result_array();

        return $results;
    }
}