<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Report_summary_of_month extends CI_Controller {
    public $month_arr = array('1'=>'มกราคม','2'=>'กุมภาพันธ์','3'=>'มีนาคม','4'=>'เมษายน','5'=>'พฤษภาคม','6'=>'มิถุนายน','7'=>'กรกฎาคม','8'=>'สิงหาคม','9'=>'กันยายน','10'=>'ตุลาคม','11'=>'พฤศจิกายน','12'=>'ธันวาคม');
    public $month_short_arr = array('1'=>'ม.ค.','2'=>'ก.พ.','3'=>'มี.ค.','4'=>'เม.ย.','5'=>'พ.ค.','6'=>'มิ.ย.','7'=>'ก.ค.','8'=>'ส.ค.','9'=>'ก.ย.','10'=>'ต.ค.','11'=>'พ.ย.','12'=>'ธ.ค.');

    function __construct()
    {
        parent::__construct();
        $this->month_arr = array('1'=>'มกราคม','2'=>'กุมภาพันธ์','3'=>'มีนาคม','4'=>'เมษายน','5'=>'พฤษภาคม','6'=>'มิถุนายน','7'=>'กรกฎาคม','8'=>'สิงหาคม','9'=>'กันยายน','10'=>'ตุลาคม','11'=>'พฤศจิกายน','12'=>'ธันวาคม');
    }
    public function index(){
        $arr_data = array();

        $this->db->select(array('id','mem_group_name'));
        $this->db->from('coop_mem_group');
        $this->db->where("mem_group_type = '1'");
        $row = $this->db->get()->result_array();
        $arr_data['row_mem_group'] = $row;

        $this->db->select('mem_type_id, mem_type_name');
        $this->db->from('coop_mem_type');
        $row = $this->db->get()->result_array();
        $arr_data['mem_type'] = $row;

        //Get Loan Type
        $row = $this->db->select('type_id, type_name')->from('coop_term_of_loan')->get()->result_array();
        $arr_data['term_of_loans'] = $row;

        $this->libraries->template('report_summary_of_month/index',$arr_data);
    }

    function coop_report_summary_of_month_preview(){
        ini_set('memory_limit', -1);
        ini_set('max_execution_time', -1);
        if (!empty($_GET["mem_type"]) && in_array("all", $_GET["mem_type"])){
            $_GET['mem_type'] = '';
        }
        if (empty($_GET['department'])) $_GET['department'] = '';
        if (empty($_GET['faction'])) $_GET['faction'] = '';
        if (empty($_GET['level'])) $_GET['level'] = '';
        $_GET['type_department'] = '1';

        $arr_data = array();
        $this->db->select(array('id','mem_group_name'));
        $this->db->from('coop_mem_group');
        $rs_group = $this->db->get()->result_array();
        $mem_group_arr = array();
        foreach($rs_group as $key => $row_group){
            $mem_group_arr[$row_group['id']] = $row_group['mem_group_name'];
        }
        $arr_data['mem_group_arr'] = $mem_group_arr;

        $this->db->select(array('setting_value'));
        $this->db->from('coop_share_setting');
        $this->db->where("setting_id = '1'");
        $row_share_value = $this->db->get()->result_array();
        $share_value = $row_share_value[0]['setting_value'];
        $arr_data['share_value'] = $share_value;

        $this->db->select(array('id','loan_type','loan_type_code'));
        $this->db->from('coop_loan_type');
        $this->db->order_by("order_by");
        $row = $this->db->get()->result_array();
        $arr_data['loan_type'] = $row;

        $arr_data['month_arr'] = array('1'=>'มกราคม','2'=>'กุมภาพันธ์','3'=>'มีนาคม','4'=>'เมษายน','5'=>'พฤษภาคม','6'=>'มิถุนายน','7'=>'กรกฎาคม','8'=>'สิงหาคม','9'=>'กันยายน','10'=>'ตุลาคม','11'=>'พฤศจิกายน','12'=>'ธันวาคม');
        $arr_data['month_short_arr'] = array('1'=>'ม.ค.','2'=>'ก.พ.','3'=>'มี.ค.','4'=>'เม.ย.','5'=>'พ.ค.','6'=>'มิ.ย.','7'=>'ก.ค.','8'=>'ส.ค.','9'=>'ก.ย.','10'=>'ต.ค.','11'=>'พ.ย.','12'=>'ธ.ค.');

        $array_data_new = array();
//        if($_GET['type_department'] == '1'){
            $arr_data['data'] = $this->coop_report_summary_of_month_department();
//        }

        if($_GET['dev'] == 'dev'){
            echo "<pre>";print_r($arr_data); echo '</pre>';exit;
        }

        $this->preview_libraries->template_preview('report_summary_of_month/coop_report_summary_of_month_preview', $arr_data);

    }

    function coop_report_summary_of_month_department()
    {
        $this->db->select(array('id','loan_type','loan_type_code'));
        $this->db->from('coop_loan_type');
        $this->db->order_by("order_by");
        $row = $this->db->get()->result_array();
        $arr_data['loan_type'] = $row;
        $loan_type = $arr_data['loan_type'];

        $this->db->select(array('t1.loan_name_id', 't1.loan_name', 't2.loan_type_code'));
        $this->db->from('coop_loan_name as t1');
        $this->db->join('coop_loan_type as t2', 't1.loan_type_id = t2.id', 'inner');
        $this->db->order_by("t1.order_by");
        $row = $this->db->get()->result_array();
        $arr_data['loan_name'] = $row;
        $loan_name = $arr_data['loan_name'];
//        echo '<pre>';print_r($loan_name);echo '</pre>';

        if(@$_GET['month']!='' && @$_GET['year']!=''){
            $day = '';
            $month = @$_GET['month'];
            $year = @$_GET['year'];
            $title_date = " เดือน ".@$month_arr[$month]." ปี ".(@$year);
        }else{
            $day = '';
            $month = '';
            $year = @$_GET['year'];
            $title_date = " ปี ".(@$year);
        }

        $where = "";
//        if(@$_GET['department']){
//            $where .= " AND id = '{$_GET['department']}'";
//        }

        $apply_where = "1=1";


        if(@$_GET['department'] != ''  && @$_GET['faction'] == ''  && @$_GET['level'] == ''){
            $where .= " AND id = '{$_GET['department']}'";
            $apply_where .= " AND department='{$_GET['department']}'";
            $mem_group = 'department';
        }else if(@$_GET['department'] != '' && @$_GET['faction'] != '' && @$_GET['level'] == ''){
            $where .= " AND id = '{$_GET['faction']}'";
            $apply_where .= " AND faction='{$_GET['faction']}'";
            $mem_group = 'faction';
        }else if(@$_GET['department'] != '' && @$_GET['faction'] != '' && @$_GET['level'] != ''){
            $where .= " AND id = '{$_GET['level']}'";
            $apply_where .= " AND level='{$_GET['level']}'";
            $mem_group = 'level';
        }

//        $where .= " AND id = '{$_GET['faction']}'";
//        $apply_where = " AND faction='{$_GET['faction']}'";


        $rs_count = $this->db->select(array('mem_group_id','mem_group_name'))->from('coop_mem_group')->where("mem_group_type = '1' {$where}")->order_by("mem_group_id ASC")->get()->result_array();
        $num_rows = count($rs_count);
//        echo $num_rows;exit;
        $page_limit = 15;
        $page_all = @ceil($num_rows/$page_limit);
        $total_data = array();
        $total_data_new = array();
        $member_share_total = 0;
        $member_total = array();

        $rs = $this->db->select(array('id','mem_group_id','mem_group_name'))
            ->from('coop_mem_group')
            ->where("1=1 {$where}")
            ->order_by("mem_group_id ASC")
            ->limit($page_limit, $page_start)
            ->get()->result_array();
//            echo $this->db->last_query().";<br><br>";exit;

        if(!empty($rs)){
            foreach(@$rs as $key => $row){
                $sum_loan = 0;
                //จำนวนสมาชิก
                $date_range['start'] 	= date("Y-m", strtotime(($year-543)."-".$month."-01")	) . "-01";
                $date_range['end'] 	= date("Y-m-t", strtotime(($year-543)."-".$month."-01")	);
//                echo '<pre>'; print_r($date_range);exit;
                $departmentWhere = "department = '".$row['id']."'";
                $member_where = "IF ((SELECT department_old FROM coop_mem_group_move WHERE date_move >= '".$date_range['start']."' AND coop_mem_group_move.member_id = t1.member_id ORDER BY date_move ASC LIMIT 1),	(SELECT department_old FROM coop_mem_group_move WHERE date_move >= '".$date_range['start']."' AND coop_mem_group_move.member_id = t1.member_id ORDER BY date_move ASC LIMIT 1),	t1.`{$mem_group}`) = '".$row['id']."'";
                $member_where .= " AND t1.member_status <> 3 AND t3.pay_amount > 0";
                if (!empty($_GET["mem_type"])){
                    $departmentWhere .= " AND mem_type_id IN (".implode(',', $_GET["mem_type"]).")";
                    $member_where .= " AND t1.mem_type_id IN (".implode(',', $_GET["mem_type"]).")";
                }
                $rs_department = $this->db->select(array('COUNT(department) AS count_department'))->from('coop_mem_apply')->where($departmentWhere)->get()->result_array();
//                echo $this->db->last_query();exit;
                $count_department = $rs_department[0]['count_department'];
                $total_data['count_department'] += $count_department;
                $select_department = "IF ((SELECT department_old FROM coop_mem_group_move WHERE date_move >= '".$date_range['start']."' AND coop_mem_group_move.member_id = t1.member_id ORDER BY date_move ASC LIMIT 1),	(SELECT department_old FROM coop_mem_group_move WHERE date_move >= '".$date_range['start']."' AND coop_mem_group_move.member_id = t1.member_id ORDER BY date_move ASC LIMIT 1),	t1.`department`) as department";
                $rs_data = $this->db->select(array($select_department,'t3.member_id', 't3.deduct_code', 't3.loan_id', 't3.loan_atm_id', 't3.pay_type', "SUM(t3.pay_amount) as sum_pay_amount", 't4.loan_amount', 't5.loan_amount_balance', 't4.approve_date'))
//                    ->from('coop_mem_apply as t1')
                    ->from("(SELECT * FROM `coop_mem_apply` WHERE {$apply_where}) as t1")
                    ->join("coop_finance_month_profile as t2","t2.profile_month = '".$month."' AND t2.profile_year = '".$year."'","inner")
                    ->join("coop_finance_month_detail as t3","t1.member_id = t3.member_id AND t2.profile_id = t3.profile_id","inner")
                    ->join("coop_loan as t4","t3.loan_id = t4.id","left")
                    ->join("(SELECT t1.loan_id, t1.loan_amount_balance FROM coop_loan_transaction as t1
                            INNER JOIN (
                                SELECT max(t1.loan_transaction_id) as max_loan_transaction_id, t1.loan_id FROM coop_loan_transaction as t1
                                INNER JOIN (
                                    SELECT t2.loan_id, max(t2.transaction_datetime) as max_transaction_datetime 
                                    FROM coop_loan_transaction as t2 
                                    WHERE t2.transaction_datetime <= '{$date_range['end']} 23:59:59' AND t2.loan_amount_balance >= 0	
                                    GROUP BY t2.loan_id
                                ) as t2 ON t1.loan_id = t2.loan_id AND t1.transaction_datetime = t2.max_transaction_datetime
                                WHERE t1.transaction_datetime <= '{$date_range['end']} 23:59:59' AND t1.loan_amount_balance != 0
                                GROUP BY t1.loan_id
                            ) as t2 ON t1.loan_transaction_id = t2.max_loan_transaction_id) as t5","t3.loan_id = t5.loan_id","left")
                    ->where($member_where)
                    ->group_by("t3.deduct_code, t3.member_id, t3.pay_type, t3.loan_id, t3.loan_atm_id")
                    ->get()->result_array();
//                echo $this->db->last_query();exit;
                $array_data = array();
                $array_data_loan_new = array();
                $dep_members = array();
                $member_share = 0;
                $member_count = 0;
                $arr_member = array();
                $loan_new_status = '0';//สัญญาใหม่
                foreach($rs_data as $key_data => $value_data){
                    $is_cal = false;
                    if($value_data['deduct_code']=='LOAN'){
                        $loan_where = "";
                        if ($_GET['term_of_loan']) {
                            $loan_where .= " AND t1.loan_type = ".$_GET['term_of_loan'];
                        }
                        $rs_loan = $this->db->select(array('t3.id as loan_type_id', 't2.loan_name_id'))
                            ->from('coop_loan as t1')
                            ->join('coop_loan_name as t2','t1.loan_type = t2.loan_name_id','inner')
                            ->join('coop_loan_type as t3','t2.loan_type_id = t3.id','inner')
                            ->where("t1.id = '".$value_data['loan_id']."'".$loan_where)
                            ->get()->result_array();
                        if(!empty($rs_loan)) {
                            if($rs_loan[0]['loan_name_id'] == '13'){
//                                echo '<pre>'; print_r($value_data);echo '</pre>';
//                                exit;
                            }
                            $array_data[$value_data['deduct_code']][$rs_loan[0]['loan_name_id']]['loan_id'][$value_data['loan_id']] = 1;
                            $total_data[$value_data['deduct_code']][$rs_loan[0]['loan_name_id']]['loan_id'][$value_data['loan_id']] = 1;

                            if(strtotime($date_range['start']." 00:00:00") <= strtotime($value_data['approve_date']))
                            {
                                $loan_new_status = '1'; //สัญญาใหม่
                            }else{
                                $loan_new_status = '0';
                            }

                            if($loan_new_status == '1') {
                                $array_data_loan_new[$value_data['deduct_code']][$rs_loan[0]['loan_name_id']]['loan_id'][$value_data['loan_id']] = 1;
                                $total_data_new[$value_data['deduct_code']][$rs_loan[0]['loan_name_id']]['loan_id'][$value_data['loan_id']] = 1;
                            }

                            if($value_data['pay_type']=='principal'){
                                $array_data[$value_data['deduct_code']][$rs_loan[0]['loan_name_id']]['principal'] += $value_data['sum_pay_amount'];
                                $total_data[$value_data['deduct_code']][$rs_loan[0]['loan_name_id']]['principal'] += $value_data['sum_pay_amount'];
                                $array_data[$value_data['deduct_code']][$rs_loan[0]['loan_name_id']]['loan_amount'] += $value_data['loan_amount'];
                                $total_data[$value_data['deduct_code']][$rs_loan[0]['loan_name_id']]['loan_amount'] += $value_data['loan_amount'];
                                $array_data[$value_data['deduct_code']][$rs_loan[0]['loan_name_id']]['loan_amount_balance'] += $value_data['loan_amount_balance'];
                                $total_data[$value_data['deduct_code']][$rs_loan[0]['loan_name_id']]['loan_amount_balance'] += $value_data['loan_amount_balance'];

                                if($loan_new_status == '1') {
                                    $array_data_loan_new[$value_data['deduct_code']][$rs_loan[0]['loan_name_id']]['principal'] += $value_data['sum_pay_amount'];
                                    $total_data_new[$value_data['deduct_code']][$rs_loan[0]['loan_name_id']]['principal'] += $value_data['sum_pay_amount'];
                                    $array_data_loan_new[$value_data['deduct_code']][$rs_loan[0]['loan_name_id']]['loan_amount'] += $value_data['loan_amount'];
                                    $total_data_new[$value_data['deduct_code']][$rs_loan[0]['loan_name_id']]['loan_amount'] += $value_data['loan_amount'];
                                    $array_data_loan_new[$value_data['deduct_code']][$rs_loan[0]['loan_name_id']]['loan_amount_balance'] += $value_data['loan_amount_balance'];
                                    $total_data_new[$value_data['deduct_code']][$rs_loan[0]['loan_name_id']]['loan_amount_balance'] += $value_data['loan_amount_balance'];
                                }
                            }else{
                                $array_data[$value_data['deduct_code']][$rs_loan[0]['loan_name_id']]['interest'] += $value_data['sum_pay_amount'];
                                $total_data[$value_data['deduct_code']][$rs_loan[0]['loan_name_id']]['interest'] += $value_data['sum_pay_amount'];
                                if($loan_new_status == '1') {
                                    $array_data_loan_new[$value_data['deduct_code']][$rs_loan[0]['loan_name_id']]['interest'] += $value_data['sum_pay_amount'];
                                    $total_data_new[$value_data['deduct_code']][$rs_loan[0]['loan_name_id']]['interest'] += $value_data['sum_pay_amount'];
                                }
                            }
                            $is_cal = true;
                        }
                    }else if($value_data['deduct_code']=='ATM' && empty($_GET['term_of_loan'])){
                        $array_data[$value_data['deduct_code']]['loan_atm_id'][$value_data['loan_atm_id']] = 1;
                        $total_data[$value_data['deduct_code']]['loan_atm_id'][$value_data['loan_atm_id']] = 1;
                        if($value_data['pay_type']=='principal'){
                            $array_data[$value_data['deduct_code']]['principal'] += $value_data['sum_pay_amount'];
                            $total_data[$value_data['deduct_code']]['principal'] += $value_data['sum_pay_amount'];
                        }else{
                            $array_data[$value_data['deduct_code']]['interest'] += $value_data['sum_pay_amount'];
                            $total_data[$value_data['deduct_code']]['interest'] += $value_data['sum_pay_amount'];
                        }
                        $is_cal = true;
                    } else if ($value_data['deduct_code']=='SHARE' && empty($_GET['term_of_loan'])) {
                        $member_share++;
                        $member_share_total++;
                        $array_data[$value_data['deduct_code']] += $value_data['sum_pay_amount'];
                        $total_data[$value_data['deduct_code']] += $value_data['sum_pay_amount'];
                        $is_cal = true;
                    } else if (empty($_GET['term_of_loan'])){
                        $array_data[$value_data['deduct_code']] += $value_data['sum_pay_amount'];
                        $total_data[$value_data['deduct_code']] += $value_data['sum_pay_amount'];
                        $is_cal = true;
                    }
                    if($is_cal) {
                        $array_data['total_amount'] += $value_data['sum_pay_amount'];
                        $total_data['total_amount'] += $value_data['sum_pay_amount'];
                        if (!in_array($value_data['member_id'], $dep_members)) {
                            $dep_members[] = $value_data['member_id'];
                        }
                        if (!in_array($value_data['member_id'], $member_total)) {
                            $member_total[] = $value_data['member_id'];
                        }
                    }
                }
//                echo '<hr>';echo '$array_data';
//                echo '<pre>'; print_r($array_data);echo '</pre>';
//                echo '<hr>';echo '$array_data_loan_new';
//                echo '<pre>'; print_r($array_data_loan_new);echo '</pre>';
                $member_count = count($dep_members);
                if($array_data['total_amount']==0){ continue; }

                $row['mem_group_id'] = 0;
                $array_data_new[@$row['mem_group_id']]['mem_group_id'] = @$row['mem_group_id'];
                $array_data_new[@$row['mem_group_id']]['mem_group_name'] = @$row['mem_group_name'];
                $array_data_new[@$row['mem_group_id']]['member_count'] = $member_count;
                $array_data_new[@$row['mem_group_id']]['SHARE'] = @$array_data['SHARE'];
                $array_data_new[@$row['mem_group_id']]['member_share'] = $member_share;

                foreach($loan_name AS $key=>$row_loan_name){
                    if($row_loan_name['loan_type_code'] == 'emergent'){
                        if(empty($array_data['ATM']['loan_atm_id'])){
                            $array_data['ATM']['loan_atm_id'] = array();
                        }
                        if(empty($array_data['LOAN'][$row_loan_name['loan_name_id']]['loan_id'])){
                            $array_data['LOAN'][$row_loan_name['loan_name_id']]['loan_id'] = array();
                        }
                        $count_loan = count($array_data['LOAN'][$row_loan_name['loan_name_id']]['loan_id']) + count($array_data['ATM']['loan_atm_id']);
                        $loan_amount = $array_data['LOAN'][$row_loan_name['loan_name_id']]['loan_amount'];
                        $loan_amount_balance = $array_data['LOAN'][$row_loan_name['loan_name_id']]['loan_amount_balance'];
                        $principal = $array_data['LOAN'][$row_loan_name['loan_name_id']]['principal'] + $array_data['ATM']['principal'];
                        $interest = $array_data['LOAN'][$row_loan_name['loan_name_id']]['interest'] + $array_data['ATM']['interest'];

                        if(!empty($array_data_loan_new['LOAN'][$row_loan_name['loan_name_id']])) {
                            $count_loan_new = count($array_data_loan_new['LOAN'][$row_loan_name['loan_name_id']]['loan_id']);//+ count($array_data_loan_new['ATM']['loan_atm_id'])
                            $loan_amount_new = $array_data_loan_new['LOAN'][$row_loan_name['loan_name_id']]['loan_amount'];
                            $loan_amount_balance_new = $array_data_loan_new['LOAN'][$row_loan_name['loan_name_id']]['loan_amount_balance'];
                            $principal_new = $array_data_loan_new['LOAN'][$row_loan_name['loan_name_id']]['principal'] + $array_data_loan_new['ATM']['principal'];
                            $interest_new = $array_data_loan_new['LOAN'][$row_loan_name['loan_name_id']]['interest'] + $array_data_loan_new['ATM']['interest'];
                        }
                    }else{
                        if(empty($array_data['LOAN'][$row_loan_name['loan_name_id']]['loan_id'])){
                            $array_data['LOAN'][$row_loan_name['loan_name_id']]['loan_id'] = array();
                            $array_data_loan_new['LOAN'][$row_loan_name['loan_name_id']]['loan_id'] = array();
                        }
                        $count_loan = count($array_data['LOAN'][$row_loan_name['loan_name_id']]['loan_id']);
                        $loan_amount = $array_data['LOAN'][$row_loan_name['loan_name_id']]['loan_amount'];
                        $loan_amount_balance = $array_data['LOAN'][$row_loan_name['loan_name_id']]['loan_amount_balance'];
                        $principal = $array_data['LOAN'][$row_loan_name['loan_name_id']]['principal'];
                        $interest = $array_data['LOAN'][$row_loan_name['loan_name_id']]['interest'];

                        if(!empty($array_data_loan_new['LOAN'][$row_loan_name['loan_name_id']])) {
                            $count_loan_new = count($array_data_loan_new['LOAN'][$row_loan_name['loan_name_id']]['loan_id']);
                            $loan_amount_new = $array_data_loan_new['LOAN'][$row_loan_name['loan_name_id']]['loan_amount'];
                            $loan_amount_balance_new = $array_data_loan_new['LOAN'][$row_loan_name['loan_name_id']]['loan_amount_balance'];
                            $principal_new = $array_data_loan_new['LOAN'][$row_loan_name['loan_name_id']]['principal'];
                            $interest_new = $array_data_loan_new['LOAN'][$row_loan_name['loan_name_id']]['interest'];
                        }
                    }

                    if($count_loan > 0){
                        $array_data_new[@$row['mem_group_id']]['loan_type'][$row_loan_name['loan_name_id']]['loan_name'] = $row_loan_name['loan_name'];
                        $array_data_new[@$row['mem_group_id']]['loan_type'][$row_loan_name['loan_name_id']]['count_loan'] = $count_loan;
                        $array_data_new[@$row['mem_group_id']]['loan_type'][$row_loan_name['loan_name_id']]['loan_amount'] = $loan_amount;
                        $array_data_new[@$row['mem_group_id']]['loan_type'][$row_loan_name['loan_name_id']]['loan_amount_balance'] = $loan_amount_balance;
                        $array_data_new[@$row['mem_group_id']]['loan_type'][$row_loan_name['loan_name_id']]['principal'] = $principal;
                        $array_data_new[@$row['mem_group_id']]['loan_type'][$row_loan_name['loan_name_id']]['interest'] = $interest;
                    }

                    if(!empty($array_data_loan_new['LOAN'][$row_loan_name['loan_name_id']])) {
                        if ($count_loan_new > 0) {
                            $array_data_new[@$row['mem_group_id']]['loan_type'][$row_loan_name['loan_name_id']]['count_loan_new'] = $count_loan_new;
                            $array_data_new[@$row['mem_group_id']]['loan_type'][$row_loan_name['loan_name_id']]['loan_amount_new'] = $loan_amount_new;
                            $array_data_new[@$row['mem_group_id']]['loan_type'][$row_loan_name['loan_name_id']]['loan_amount_balance_new'] = $loan_amount_balance_new;
                            $array_data_new[@$row['mem_group_id']]['loan_type'][$row_loan_name['loan_name_id']]['principal_new'] = $principal_new;
                            $array_data_new[@$row['mem_group_id']]['loan_type'][$row_loan_name['loan_name_id']]['interest_new'] = $interest_new;
                        }
                    }
                }
                //หาจำนวนหุ้นสะสม
                $this->db->select("count(t1.member_id) as count_member_share ,sum(t1.share_collect_value) total_share_collect_value");
                $this->db->from("(SELECT
                    t1.member_id, 
                    t1.share_collect_value
                    FROM coop_mem_share as t1
                    INNER JOIN (
                        SELECT max(t1.share_id) as max_share_id, t1.member_id FROM coop_mem_share as t1
                        INNER JOIN (
                            SELECT t2.member_id, max(t2.share_date) as max_share_date
                            FROM coop_mem_share as t2 
                            WHERE t2.share_date <= '{$date_range['end']} 23:59:59' AND t2.share_collect_value >= 0	
                            GROUP BY t2.member_id
                        )  as t2 ON t1.member_id = t2.member_id AND t1.share_date = t2.max_share_date
                        WHERE t1.share_date <= '{$date_range['end']} 23:59:59' AND t1.share_collect_value != 0
                        GROUP BY t1.member_id
                    ) as t2 ON t1.share_id = t2.max_share_id
                    INNER JOIN (SELECT * FROM `coop_mem_apply` WHERE 1=1 AND {$mem_group} = '".$row['id']."') as t3 ON t3.member_id = t1.member_id
                    INNER JOIN `coop_finance_month_profile` as `t4` ON `t4`.`profile_month` = '{$month}' AND `t4`.`profile_year` = '{$year}' 
                    INNER JOIN `coop_finance_month_detail` as `t5` ON `t3`.`member_id` = `t5`.`member_id` AND `t4`.`profile_id` = `t5`.`profile_id` 
                    WHERE `t3`.`member_status` <> 3 AND t5.deduct_code = 'SHARE'
                    GROUP BY t1.member_id) as t1");
                $mem_share = $this->db->get()->row_array();
//                echo $this->db->last_query();exit;
                $array_data_new[@$row['mem_group_id']]['DEPOSIT'] = @$array_data['DEPOSIT'];
                $array_data_new[@$row['mem_group_id']]['CREMATION'] = @$array_data['CREMATION'];
                $array_data_new[@$row['mem_group_id']]['REGISTER_FEE'] = @$array_data['REGISTER_FEE'];
                $array_data_new[@$row['mem_group_id']]['OTHER'] = @$array_data['OTHER'];
                $array_data_new[@$row['mem_group_id']]['GUARANTEE_AMOUNT'] = @$array_data['GUARANTEE_AMOUNT'];
                $array_data_new[@$row['mem_group_id']]['total_amount'] = @$array_data['total_amount'];
                $array_data_new[@$row['mem_group_id']]['total_share_collect_value'] = @$mem_share['total_share_collect_value'];

                ksort($array_data_new[@$row['mem_group_id']]['loan_type']);
            }
        }

        $sum_loan_amount = 0;
//        foreach ($array_data_new[0]['loan_type'] as $item) {
//            $sum_loan_amount += $item['loan_amount'];
//        }

//        echo '<hr>$array_data_new';
//        echo '<pre>';print_r($array_data_new);exit;
        return $array_data_new;
    }
}
