<?php if( ! defined('BASEPATH')) exit('No direct script access allowed');

class Account_transaction extends CI_Model {
    public function __construct()
    {
        parent::__construct();
        //$this->load->database();
        # Load libraries
        //$this->load->library('parser');
        $this->load->helper(array('html', 'url'));
    }

    function set_data_account_trancetion_detail($match_id,$statement,$match_type,$ref,$money,$process){
        if(empty($match_id) || empty($statement) ||empty($match_type) ||empty($ref) ||empty($money) ||empty($process) ){
            echo $match_id.'---'.$statement.'---'.$match_type.'---'.$ref.'---'.$money.'---'.$process.'<br>';
            echo 'ข้อมูลบันทึกบัญชีไม่ครบ';
        }
        $data_process['process'] = $process;
        $data_process['ref'] = $ref;
        $data_process['money'] = $money;
        $data_process['match_type'] = $match_type;
        $data_process['match_id'] = $match_id;
        $data_process['statement'] =  $statement;

        return $data_process;
    }


    public function add_account_trancetion_detail($data_process){

        foreach($data_process as $key => $value_process){
            $this->db->select('*');
            $this->db->from('setting_account_tranction');
            $this->db->where("process = '" . $value_process['process'] . "' ");
            $row = $this->db->get()->result_array();
            $row_tranction = @$row[0];
            if(empty($row_tranction['description'])){
                $row_tranction['description'] = $value_process['process'];
                $row_tranction['ref_type'] = 'account_buy_list';
            }
            $data_acc['coop_account']['account_description'] = $row_tranction['description'];
            $data_acc['coop_account']['account_datetime'] = date('Y-m-d');
            $data_acc['coop_account']['ref'] = $value_process['ref'];
            $data_acc['coop_account']['ref_type'] = $row_tranction['ref_type'];
            $data_acc['coop_account']['process'] = $value_process['process'];

            $this->db->select('*');
            $this->db->from('coop_account_match');
            $this->db->where("match_type = '" . $value_process['match_type'] . "' AND match_id = '" . $value_process['match_id'] . "' ");
            $row = $this->db->get()->result_array();
            $row_account_match = @$row[0];
            $bankcharge = 0;
            if(!empty($row_account_match['bankcharge']) && $row_account_match['bankcharge'] != 0){

                $this->db->select('*');
                $this->db->from('coop_account_match');
                $this->db->where("match_type = 'main' AND match_id = '5' ");
                $row_bankcharge = $this->db->get()->result_array();
                $row_account_match_bankcharge = @$row_bankcharge[0];

                $data_acc['coop_account_detail']['bankcharge']['account_type'] = $value_process['statement']=='credit'? 'debit':'credit';
                $data_acc['coop_account_detail']['bankcharge']['account_amount'] = $row_account_match['bankcharge'];
                $data_acc['coop_account_detail']['bankcharge']['account_chart_id'] = $row_account_match_bankcharge['account_chart_id'];
                $bankcharge =  $row_account_match['bankcharge'];
            }
            $data_acc['coop_account_detail'][$key]['account_type'] = $value_process['statement'];
            $data_acc['coop_account_detail'][$key]['account_amount'] = $value_process['money']+$bankcharge;
            $data_acc['coop_account_detail'][$key]['account_chart_id'] = $row_account_match['account_chart_id'];

        }

        $this->account_transaction->account_process($data_acc);
    }

    public function account_process($data){
        $data_insert = array();

        if(!empty($data['coop_account']['ref'])){
            $data_insert['ref_id'] = $data['coop_account']['ref'];
            $data_insert['ref_type'] = $data['coop_account']['ref_type'];
            $data_insert['process'] = $data['coop_account']['process'];
            $data_insert['account_number'] = $data['coop_account']['account_number'];

        }

        $data_insert['account_description'] = $data['coop_account']['account_description'];
        $data_insert['account_datetime'] = $data['coop_account']['account_datetime'];
        $year = substr($data['coop_account']['account_datetime'], 0,4);
        $month = (int)substr($data['coop_account']['account_datetime'], 5,2);

        $account_period = $this->db->select("accm_month_ini")->from("coop_account_period_setting")->order_by("accm_date_modified desc")->get()->row();
        if(empty($month)) $month = $account_period->accm_month_ini;
        $year_be = $account_period->accm_month_ini <= $month ? $year + 543 : $year + 543 - 1;

        if(isset($data['coop_account']['account_status']) && $data['coop_account']['account_status']=='1'){
            $data_insert['account_status'] = '1';
        }
        // $this->db->insert('coop_account', $data_insert);

        $this->db->select('account_id');
        $this->db->from("coop_account");
        $this->db->order_by("account_id DESC");
        $this->db->limit(1);
        $row = $this->db->get()->result_array();

        $account_id = $row[0]['account_id'];

        foreach($data['coop_account_detail'] as $key => $value){
            if($value['account_amount'] > 0){
                $data_insert = array();
                $data_insert['account_id'] = $account_id;
                $data_insert['account_type'] = $value['account_type'];
                $data_insert['account_amount'] = $value['account_amount'];
                $data_insert['account_chart_id'] = $value['account_chart_id'];
                // $this->db->insert('coop_account_detail', $data_insert);

                // $this->account_transaction->increase_decrease_budget_year($value['account_chart_id'], $value['account_amount'], $value['account_type'], $year_be, 1);
            }
        }
        return $account_id;
    }
    function add_journal_ref($account_description,$account_datetime){
        $journal_ref ='';
        $date = $account_datetime;
        $detail = $account_description;

        $this->db->select(array(
            'account_id'
        ));
        $this->db->from('coop_account');
        $this->db->where("account_description ='{$detail}' AND account_datetime LIKE '{$date}%' AND journal_ref is null  " );
        $rs = $this->db->get()->result_array();

        $this->db->select('*');
        $this->db->from('coop_account');
        $this->db->where("journal_ref LIKE 'J%' " );
        $this->db->order_by("journal_ref DESC");
        $rs_all = $this->db->get()->result_array();
        $row = @$rs_all[0]['journal_ref'];
        if(!empty($row)) {
            $id = (int) substr($row, 2);
            $journal_ref = 'J'.sprintf("%06d", $id + 1);
        }else{
            $journal_ref = "J000001";
        }
        $index = 0;
        foreach($rs as $key => $value) {

            $confirm[$index]['journal_ref'] = $journal_ref;
            $confirm[$index]['account_id'] = $value['account_id'];
            $index++;
        }

        if(!empty($confirm)){
            $this->db->update_batch('coop_account', $confirm, 'account_id');
        }
    }

    function call_account_call_chart_report($month,$year){

        //ฟังชันการเก็บข้อมูลบัญชี ส่วนของการเก็บข้อมูลบัญชี แยกประเภทรายเดือนเพื่อนำไปใช้ในการคำนวนงบทดลอง
        $arr_data = array();
        $month_arr = array('1'=>'มกราคม','2'=>'กุมภาพันธ์','3'=>'มีนาคม','4'=>'เมษายน','5'=>'พฤษภาคม','6'=>'มิถุนายน','7'=>'กรกฎาคม','8'=>'สิงหาคม','9'=>'กันยายน','10'=>'ตุลาคม','11'=>'พฤศจิกายน','12'=>'ธันวาคม');
        $month_short_arr = array('1'=>'ม.ค.','2'=>'ก.พ.','3'=>'มี.ค.','4'=>'เม.ย.','5'=>'พ.ค.','6'=>'มิ.ย.','7'=>'ก.ค.','8'=>'ส.ค.','9'=>'ก.ย.','10'=>'ต.ค.','11'=>'พ.ย.','12'=>'ธ.ค.');
        $arr_data['month_arr'] = $month_arr;
        $arr_data['month_short_arr'] = $month_short_arr;

        if(@$month!='' && @$year!=''){
            $day = '';
            $month = @$month;
            $year = (@$year-543);
            $file_name_text = $month_arr[$month]."_".($year+543);

            $s_date = $year.'-'.sprintf("%02d",@$month).'-01'.' 00:00:00.000';
            $e_date = date('Y-m-t',strtotime($s_date)).' 23:59:59.000';
            $where = " AND account_datetime BETWEEN '".$s_date."' AND '".$e_date."'";
        }else{
            $day = '';
            $month = '';
            $year = (@$year-543);
            $file_name_text = ($year+543);
            $where = " AND account_datetime BETWEEN '".$year."-01-01 00:00:00.000' AND '".$year."-12-31 23:59:59.000' ";
        }

        $arr_data['day'] = $day;
        $arr_data['month'] = $month;
        $arr_data['year'] = $year;
        $arr_data['where_date'] = $where;
        $arr_data['file_name_text'] = $file_name_text;

        $this->db->select(array(
            'account_id',
            'account_datetime',
            'ref_id',
            'ref_type',
            'account_description',
            'account_detail_id',
            'account_type',
            'account_amount',
            'account_chart_id',
            'account_chart',
            'account_number'
        ));
        $this->db->from('account_day_book');
        $this->db->where("1=1 ".$where );
        $this->db->order_by("
			account_datetime ASC,account_chart_id ASC
			"
        );
        $rs = $this->db->get()->result_array();

        $data = array();

        $sort_array = array();
        $data_account_detail = array();

        $row  = array();

        $this->db->select(array('*'));
        $this->db->from("(SELECT
                            `t0`.account_number,
                            `t0`.account_description,
                            `t0`.account_page_number,
                            `t0`.account_status,
                            `t0`.ref_id,
                            `t0`.ref_type,
                            `t0`.process,
                            `t0`.status_audit,
                            `t0`.journal_ref,
                            LEFT(`t0`.account_datetime, 10) AS account_datetime,
                            `t1`.*,
                            `t2`.`account_chart`
                        FROM
                            `coop_account` AS `t0`
                            INNER JOIN `coop_account_detail` AS `t1` ON t0.account_id = t1.account_id
                            INNER JOIN `coop_account_chart` AS `t2` ON `t1`.`account_chart_id` = `t2`.`account_chart_id`
                        WHERE
                            1 = 1
                            AND (`account_status` != '2' OR `account_status` IS NULL )
                            AND( `t0`.`status_audit` <> '1'
                            OR `t0`.`status_audit` IS NULL )
                            $where
                        ) as t_all");
        $this->db->order_by("t_all.account_chart_id ASC,t_all.account_datetime ASC,`t_all`.`account_id` ASC");
//         echo "".$this->db->get_compiled_select(null, false)."<br><br><br><br>";exit;
        $row_detail = $this->db->get()->result_array();
        $row['data']['account_detail'] = $row_detail;

        foreach($row['data']  as $key => $row) {
            $account_datetime = array();
            $account_datetime =  explode(" ",$row['account_datetime']);
            //  echo"<pre>";print_r($row);
            foreach($row as $key2 => $row_detail){
                $i_d = 0;
                $account_datetime ='';
                $account_datetime =  explode(" ",$row_detail['account_datetime']);
                $data_account_detail[$account_datetime[0]][$row_detail['account_description']][$row_detail['account_chart_id'].$row_detail['account_type']]['account_chart_id'] = $row_detail['account_chart_id'];
                $data_account_detail[$account_datetime[0]][$row_detail['account_description']][$row_detail['account_chart_id'].$row_detail['account_type']]['account_chart'] = $row_detail['account_chart'];
                $data_account_detail[$account_datetime[0]][$row_detail['account_description']][$row_detail['account_chart_id'].$row_detail['account_type']]['account_type'] = $row_detail['account_type'];
                $data_account_detail[$account_datetime[0]][$row_detail['account_description']][$row_detail['account_chart_id'].$row_detail['account_type']]['account_amount'] += $row_detail['account_amount'];
            }
        }


        $sort_array = array_map(function($array_account_datetime){
            return array_map(function($var){
                ksort($var);
                return $var;
            },$array_account_datetime);

        },$data_account_detail);

        $number_count = 0;
        $sum_debit = array();
        $sum_credit = array();
        foreach($sort_array as $key => $value_main){
            foreach($value_main as $key_type => $value_data) {
                foreach($value_data as $key_type_dc => $value_data_dc) {
                    $data_filter[$value_data_dc['account_chart_id']][$number_count]['account_description']  = $key_type;
                    $data_filter[$value_data_dc['account_chart_id']][$number_count]['account_amount']  += $value_data_dc['account_amount'] ;
                    $data_filter[$value_data_dc['account_chart_id']][$number_count]['account_chart_id']  = $value_data_dc['account_chart_id'] ;
                    $data_filter[$value_data_dc['account_chart_id']][$number_count]['account_chart']  = $value_data_dc['account_chart'] ;
                    $data_filter[$value_data_dc['account_chart_id']][$number_count]['account_type']  = $value_data_dc['account_type'] ;
                    $data_filter[$value_data_dc['account_chart_id']][$number_count]['account_datetime']  = $key;
                    $number_count++;
                    if($value_data_dc['account_type'] == 'debit'){
                        $sum_debit[$value_data_dc['account_chart_id']] += $value_data_dc['account_amount'] ;
                    }else{
                        $sum_credit[$value_data_dc['account_chart_id']] += $value_data_dc['account_amount'] ;
                    }

                }
            }
        }

        if($_GET['month'] == 1 ){
            $old_mount = 12;
            $old_year =  $year-1;
        }else{
            $old_mount = $_GET['month'] -1 ;
            $old_year =  $year;
        }
        $this->db->select(array(
            '*'
        ));
        $this->db->from('coop_account_budget');
        $this->db->where("mount = '".$old_mount."' AND year = '".$old_year."'");
        $row_budget = $this->db->get()->result_array();
        $budget = array();
        foreach($row_budget as $key => $data_budget){
            $budget[$data_budget['account_chart_id']] = $data_budget;
        }

        $account_chart_main = array();
        $this->db->select(array('*'));
        $this->db->from('coop_account_chart');
        $this->db->order_by("account_chart_id ASC");
        $row = $this->db->get()->result_array();
        foreach($row as $key => $row_detail) {
            $account_chart_main[$row_detail['account_chart_id']] = $row_detail['account_chart'];
        }
        foreach($account_chart_main as $key => $value) {

            $budget_amount_sum = $budget[$key]['budget_amount'];
            $type_budget = $budget[$key]['budget_type']?$budget[$key]['budget_type']:'';

            if($data_filter[$key]) {
//                        echo '<pre>';print_r($data_filter[$key]);echo '</pre>';
                foreach (@$data_filter[$key] as $key_main => $value_detail) {
//                    echo  $budget_amount_sum .'   '.$key.'<br>';
                    if($type_budget == ''){
                        $type_budget = $value_detail['account_type'];
                    }
                    if ($value_detail['account_type'] != $type_budget) {
                        $budget_amount_sum = $budget_amount_sum - $value_detail['account_amount'];
//                         echo $budget_amount_sum.' = '.$budget_amount_sum.' -'.$value_detail['account_amount'].'<br>';

                    } else {
                        $budget_amount_sum = $budget_amount_sum + $value_detail['account_amount'];
//                         echo $budget_amount_sum.' = '.$budget_amount_sum.'+ '.$value_detail['account_amount'].'<br>';

                    }

                    // echo  $budget_amount_sum .'   '.$key.'<br>';
                    // echo $budget_amount_sum.' = '.$budget_amount_sum.'+ -'.$value_detail['account_amount'].'<br>';

                    if ($budget_amount_sum <= 0) {
                        $budget_amount_sum = $budget_amount_sum * (-1);
                        if ($type_budget == 'credit') {
                            $type_budget = 'debit';
                        } else {
                            $type_budget = 'credit';
                        }
                    }

                }
            }

//            echo  $budget_amount_sum .'   '.$key;
//            exit;

            //เก็บระหว่างเดือน
            $this->account_transaction->coop_account_chart_ledger_report($key, $_GET['month'], $year, $sum_credit[$key], $sum_debit[$key]);
            //เก็บยอดยกไป
            $this->account_transaction->carry_forward_chart_report($key, $_GET['month'], $year, $type_budget, $budget_amount_sum);

        }

    }
    function coop_account_chart_ledger_report($account_chart_id,$month,$year,$sum_credit,$sum_debit)
    {
        $index = 0;
        $data_insert = array();
        $confirm = array();
        $this->db->select(array(
            'id'
        ));
        $this->db->from('coop_account_chart_ledger');
        $this->db->where("account_chart_id = '".$account_chart_id."' AND mount = '".$month."'  AND year = '".$year."'     ");
        $check_update = $this->db->get()->result_array();

        $timestamp = date('Y-m-d H:i:s');
        if(empty($check_update[0]['id'])) {
            $data_insert[$index]['credit_account_chart'] = $sum_credit;
            $data_insert[$index]['debit_account_chart'] = $sum_debit;
            $data_insert[$index]['mount'] = $month;
            $data_insert[$index]['year'] = $year;
            $data_insert[$index]['account_chart_id'] = $account_chart_id;
            $data_insert[$index]['create_time'] = date('Y-m-t');
        }else {
            $confirm[$index]['credit_account_chart'] = $sum_credit;
            $confirm[$index]['debit_account_chart'] = $sum_debit;
            $confirm[$index]['mount'] = $month;
            $confirm[$index]['year'] = $year;
            $confirm[$index]['account_chart_id'] = $account_chart_id;
            $confirm[$index]['create_time'] = date('Y-m-t');
            $confirm[$index]['id'] = $check_update[0]['id'];
            $confirm[$index]['update_time'] = $timestamp;

        }
        $index++;
        if(sizeof($data_insert)){
            $this->db->insert_batch('coop_account_chart_ledger', $data_insert);
        }
        if(sizeof($confirm)){
            $this->db->update_batch('coop_account_chart_ledger', $confirm, 'id');
        }

    }

    function carry_forward_chart_report($account_chart_id,$month,$year,$budget_type,$budget_amount)
    {
        $data_insert = array();
        $confirm = array();
        $this->db->select(array(
            '*'
        ));
        $this->db->from('coop_account_budget');
        $this->db->where("account_chart_id = '".$account_chart_id."' AND mount = '".$month."'  AND year = '".$year."'");
        $check_update = $this->db->get()->result_array();
        $index = 0;
        $timestamp = date('Y-m-d H:i:s');
        if(empty($check_update[0]['id'])) {

            $data_insert[$index]['budget_amount'] = $budget_amount;
            $data_insert[$index]['account_chart_id'] = $account_chart_id;
            $data_insert[$index]['budget_type'] = $budget_type;
            $data_insert[$index]['mount'] = $month;
            $data_insert[$index]['year'] = $year;
            $data_insert[$index]['create_time'] = date('Y-m-t');

        }else{
            $confirm[$index]['budget_amount'] = $budget_amount;
            $confirm[$index]['account_chart_id'] = $account_chart_id;
            $confirm[$index]['budget_type'] = $budget_type;
            $confirm[$index]['mount'] = $month;
            $confirm[$index]['year'] = $year;
            $confirm[$index]['update_time'] = $timestamp;
            $confirm[$index]['id'] = $check_update[0]['id'];
        }


        if(sizeof($data_insert)){
            $this->db->insert_batch('coop_account_budget', $data_insert);
        }
        if(sizeof($confirm)){
            $this->db->update_batch('coop_account_budget', $confirm, 'id');
        }

    }
    function coop_account_chart_ledger_report_year($account_chart_id,$year,$sum_credit,$sum_debit)
    {
        $index = 0;
        $data_insert = array();
        $confirm = array();
        $this->db->select(array(
            'id'
        ));
        $this->db->from('coop_account_chart_ledger_year');
        $this->db->where("account_chart_id = '".$account_chart_id."'  AND year = '".$year."'     ");
        $check_update = $this->db->get()->result_array();

        $timestamp = date('Y-m-d H:i:s');
        if(empty($check_update[0]['id'])) {
            $data_insert[$index]['credit_account_chart'] = $sum_credit;
            $data_insert[$index]['debit_account_chart'] = $sum_debit;
            $data_insert[$index]['year'] = $year;
            $data_insert[$index]['account_chart_id'] = $account_chart_id;
            $data_insert[$index]['create_time'] = date('Y-m-t');
        }else {
            $confirm[$index]['credit_account_chart'] = $sum_credit;
            $confirm[$index]['debit_account_chart'] = $sum_debit;
            $confirm[$index]['year'] = $year;
            $confirm[$index]['account_chart_id'] = $account_chart_id;
            $confirm[$index]['create_time'] = date('Y-m-t');
            $confirm[$index]['id'] = $check_update[0]['id'];
            $confirm[$index]['update_time'] = $timestamp;

        }
        $index++;
        if(sizeof($data_insert)){
            $this->db->insert_batch('coop_account_chart_ledger_year', $data_insert);
        }
        if(sizeof($confirm)){
            $this->db->update_batch('coop_account_chart_ledger_year', $confirm, 'id');
        }

    }
    function carry_forward_chart_report_year($account_chart_id,$year,$budget_type,$budget_amount)
    {
        $data_insert = array();
        $confirm = array();
        $this->db->select(array(
            '*'
        ));
        $this->db->from('coop_account_budget_year');
        $this->db->where("account_chart_id = '".$account_chart_id."'  AND year = '".$year."'");
        $check_update = $this->db->get()->result_array();
        $index = 0;
        $timestamp = date('Y-m-d H:i:s');
        if(empty($check_update[0]['id'])) {

            $data_insert[$index]['budget_amount'] = $budget_amount;
            $data_insert[$index]['account_chart_id'] = $account_chart_id;
            $data_insert[$index]['budget_type'] = $budget_type;
            $data_insert[$index]['year'] = $year;
            $data_insert[$index]['create_time'] = date('Y-m-t');

        }else{
            $confirm[$index]['budget_amount'] = $budget_amount;
            $confirm[$index]['account_chart_id'] = $account_chart_id;
            $confirm[$index]['budget_type'] = $budget_type;
            $confirm[$index]['year'] = $year;
            $confirm[$index]['update_time'] = $timestamp;
            $confirm[$index]['id'] = $check_update[0]['id'];
        }


        if(sizeof($data_insert)){
            $this->db->insert_batch('coop_account_budget_year', $data_insert);
        }
        if(sizeof($confirm)){
            $this->db->update_batch('coop_account_budget_year', $confirm, 'id');
        }

    }

    function call_account_call_chart_report_year($year){

        //ฟังชันการเก็บข้อมูลบัญชี ส่วนของการเก็บข้อมูลบัญชี แยกประเภทรายเดือนเพื่อนำไปใช้ในการคำนวนงบทดลอง
        $arr_data = array();
        $month_arr = array('1'=>'มกราคม','2'=>'กุมภาพันธ์','3'=>'มีนาคม','4'=>'เมษายน','5'=>'พฤษภาคม','6'=>'มิถุนายน','7'=>'กรกฎาคม','8'=>'สิงหาคม','9'=>'กันยายน','10'=>'ตุลาคม','11'=>'พฤศจิกายน','12'=>'ธันวาคม');
        $month_short_arr = array('1'=>'ม.ค.','2'=>'ก.พ.','3'=>'มี.ค.','4'=>'เม.ย.','5'=>'พ.ค.','6'=>'มิ.ย.','7'=>'ก.ค.','8'=>'ส.ค.','9'=>'ก.ย.','10'=>'ต.ค.','11'=>'พ.ย.','12'=>'ธ.ค.');
        $arr_data['month_arr'] = $month_arr;
        $arr_data['month_short_arr'] = $month_short_arr;


            $day = '';
            $month = '';
            $year = (@$year-543);
            $year_old = $year-1;
            $file_name_text = ($year+543);
            $where = " AND account_datetime BETWEEN '".$year_old."-12-01 00:00:00.000' AND '".$year."-12-31 23:59:59.000' ";


        $arr_data['day'] = $day;
        $arr_data['month'] = $month;
        $arr_data['year'] = $year;
        $arr_data['where_date'] = $where;
        $arr_data['file_name_text'] = $file_name_text;

        $this->db->select(array(
            'account_id',
            'account_datetime',
            'ref_id',
            'ref_type',
            'account_description',
            'account_detail_id',
            'account_type',
            'account_amount',
            'account_chart_id',
            'account_chart',
            'account_number'
        ));
        $this->db->from('account_day_book');
        $this->db->where("1=1 ".$where );
        $this->db->order_by("
			account_datetime ASC,account_chart_id ASC
			"
        );
        $rs = $this->db->get()->result_array();

        $data = array();

        $sort_array = array();
        $data_account_detail = array();

        $row  = array();

        $this->db->select(array('*'));
        $this->db->from("(SELECT
                            `t0`.account_number,
                            `t0`.account_description,
                            `t0`.account_page_number,
                            `t0`.account_status,
                            `t0`.ref_id,
                            `t0`.ref_type,
                            `t0`.process,
                            `t0`.status_audit,
                            `t0`.journal_ref,
                            LEFT(`t0`.account_datetime, 10) AS account_datetime,
                            `t1`.*,
                            `t2`.`account_chart`
                        FROM
                            `coop_account` AS `t0`
                            INNER JOIN `coop_account_detail` AS `t1` ON t0.account_id = t1.account_id
                            INNER JOIN `coop_account_chart` AS `t2` ON `t1`.`account_chart_id` = `t2`.`account_chart_id`
                        WHERE
                            1 = 1
                            AND (`account_status` != '2' OR `account_status` IS NULL )
                            AND( `t0`.`status_audit` <> '1'
                            OR `t0`.`status_audit` IS NULL )
                            $where
                        ) as t_all");
        $this->db->order_by("t_all.account_chart_id ASC,t_all.account_datetime ASC,`t_all`.`account_id` ASC");
         // echo "".$this->db->get_compiled_select(null, false)."<br><br><br><br>";exit;
        $row_detail = $this->db->get()->result_array();
        $row['data']['account_detail'] = $row_detail;

        foreach($row['data']  as $key => $row) {
            $account_datetime = array();
            $account_datetime =  explode(" ",$row['account_datetime']);
            //  echo"<pre>";print_r($row);
            foreach($row as $key2 => $row_detail){
                $i_d = 0;
                $account_datetime ='';
                $account_datetime =  explode(" ",$row_detail['account_datetime']);
                $data_account_detail[$account_datetime[0]][$row_detail['account_description']][$row_detail['account_chart_id'].$row_detail['account_type']]['account_chart_id'] = $row_detail['account_chart_id'];
                $data_account_detail[$account_datetime[0]][$row_detail['account_description']][$row_detail['account_chart_id'].$row_detail['account_type']]['account_chart'] = $row_detail['account_chart'];
                $data_account_detail[$account_datetime[0]][$row_detail['account_description']][$row_detail['account_chart_id'].$row_detail['account_type']]['account_type'] = $row_detail['account_type'];
                $data_account_detail[$account_datetime[0]][$row_detail['account_description']][$row_detail['account_chart_id'].$row_detail['account_type']]['account_amount'] += $row_detail['account_amount'];
            }
        }


        $sort_array = array_map(function($array_account_datetime){
            return array_map(function($var){
                ksort($var);
                return $var;
            },$array_account_datetime);

        },$data_account_detail);

       // echo '<pre>';print_r($sort_array);echo '</pre>';

        $number_count = 0;
        $sum_debit = array();
        $sum_credit = array();
        foreach($sort_array as $key => $value_main){
            foreach($value_main as $key_type => $value_data) {
                foreach($value_data as $key_type_dc => $value_data_dc) {
                    $data_filter[$value_data_dc['account_chart_id']][$number_count]['account_description']  = $key_type;
                    $data_filter[$value_data_dc['account_chart_id']][$number_count]['account_amount']  += $value_data_dc['account_amount'] ;
                    $data_filter[$value_data_dc['account_chart_id']][$number_count]['account_chart_id']  = $value_data_dc['account_chart_id'] ;
                    $data_filter[$value_data_dc['account_chart_id']][$number_count]['account_chart']  = $value_data_dc['account_chart'] ;
                    $data_filter[$value_data_dc['account_chart_id']][$number_count]['account_type']  = $value_data_dc['account_type'] ;
                    $data_filter[$value_data_dc['account_chart_id']][$number_count]['account_datetime']  = $key;
                    $number_count++;
                    if($value_data_dc['account_type'] == 'debit'){
                        $sum_debit[$value_data_dc['account_chart_id']] += $value_data_dc['account_amount'] ;
                    }else{
                        $sum_credit[$value_data_dc['account_chart_id']] += $value_data_dc['account_amount'] ;
                    }

                }
            }
        }


            $old_year =  $year-1;

        $this->db->select(array(
            '*'
        ));
        $this->db->from('coop_account_budget_year');
        $this->db->where("year = '".$old_year."'");
        $row_budget = $this->db->get()->result_array();
        $budget = array();
        foreach($row_budget as $key => $data_budget){
            $budget[$data_budget['account_chart_id']] = $data_budget;
        }

        $account_chart_main = array();
        $this->db->select(array('*'));
        $this->db->from('coop_account_chart');
        $this->db->order_by("account_chart_id ASC");
        $row = $this->db->get()->result_array();
        foreach($row as $key => $row_detail) {
            $account_chart_main[$row_detail['account_chart_id']] = $row_detail['account_chart'];
        }

        foreach($account_chart_main as $key => $value) {

            $budget_amount_sum = $budget[$key]['budget_amount'];
            $type_budget = $budget[$key]['budget_type']?$budget[$key]['budget_type']:'';



            if($data_filter[$key]) {
                foreach (@$data_filter[$key] as $key_main => $value_detail) {
                    if($type_budget == ''){
                        $type_budget = $value_detail['account_type'];
                    }
                    if ($value_detail['account_type'] != $type_budget) {
                        $budget_amount_sum = $budget_amount_sum - $value_detail['account_amount'];

                    } else {
                        $budget_amount_sum = $budget_amount_sum + $value_detail['account_amount'];

                    }

                    if ($budget_amount_sum <= 0) {
                        $budget_amount_sum = $budget_amount_sum * (-1);
                        if ($type_budget == 'credit') {
                            $type_budget = 'debit';
                        } else {
                            $type_budget = 'credit';
                        }
                    }
                }
            }

//            echo 'account_id'.$key.'--budget_amount----'.$type_budget.'--'.$budget[$key]['budget_amount'].'---debit---'.$sum_debit[$key].'---credit---'.$sum_credit[$key].'---budget---'.$budget_amount_sum.'---type---'.$type_budget.'<br>';
//            //เก็บระหว่างเดือน
            $this->account_transaction->coop_account_chart_ledger_report_year($key, $year, $sum_credit[$key], $sum_debit[$key]);
//            //เก็บยอดยกไป
            $this->account_transaction->carry_forward_chart_report_year($key, $year, $type_budget, $budget_amount_sum);
        }
//        echo '---debit---'.$sum_all_debit.'---credit---'.$sum_all_credit.'<br>';
    }

    /*****
        Function for calculate budget year
        Table coop_account_budget_year
        Type 1=increase(Add account)/2=decrease(Remove account)
        $year :: BE
    *****/
    function increase_decrease_budget_year($chart_id, $amount, $account_type, $year, $type) {
        $setting = $this->db->select("*")->from("coop_account_setting")->where("type = 'budget_year_update'")->order_by("created_at")->get()->row();
        $budget_type = json_decode($setting->value);
        $chart_type = substr($chart_id, 0, 1);

        //UPDATE budget year only setting data
        if (in_array($chart_type, $budget_type)) {
            $chart = $this->db->select("entry_type")->from("coop_account_chart")->where("account_chart_id = '{$chart_id}'")->get()->row();
            $entry_type = $chart->entry_type;
            $budget_years = $this->db->select("id, budget_amount")->from("coop_account_budget_year")->where("account_chart_id = '".$chart_id."' AND year >= '".$year."'")->get()->result_array();
            foreach($budget_years as $budget_year) {
                $budget_amount = $budget_year["budget_amount"];
                if($type == 1) {
                    $budget_amount = $entry_type == 1 && $account_type == "debit" ? $budget_year["budget_amount"] + $amount :
                                        ($entry_type == 1 && $account_type == "credit" ? $budget_year["budget_amount"] - $amount :
                                        ($entry_type == 2 && $account_type == "credit" ? $budget_year["budget_amount"] + $amount :
                                        $budget_year["budget_amount"] - $amount));
                } else {
                    $budget_amount = $entry_type == 1 && $account_type == "debit" ? $budget_year["budget_amount"] - $amount :
                                        ($entry_type == 1 && $account_type == "credit" ? $budget_year["budget_amount"] + $amount :
                                        ($entry_type == 2 && $account_type == "credit" ? $budget_year["budget_amount"] - $amount :
                                        $budget_year["budget_amount"] + $amount));
                }
    
                $data_update = array();
                $data_update["budget_amount"] = $budget_amount;
                $data_update["update_time"] = date("Y-m-d H:i:s");
                $this->db->where('id', $budget_year["id"]);
                $this->db->update('coop_account_budget_year', $data_update);
            }
    
            if(empty($budget_years) && $type == 1) {
                $prev_year = $year - 1;
                $budget_prev_amount = 0;
                $budget_year = $this->db->select("budget_amount")->from("coop_account_budget_year")->where("account_chart_id = '".$chart_id."' AND year <= '".$year."'")->order_by("year desc")->get()->row();
                if(!empty($budget_year)) {
                    $budget_prev_amount = $budget_year->budget_amount;
                }
                $budget_amount = $entry_type == 1 && $account_type == "debit" ? $budget_prev_amount + $amount :
                                        ($entry_type == 1 && $account_type == "credit" ? $budget_prev_amount - $amount :
                                        ($entry_type == 2 && $account_type == "credit" ? $budget_prev_amount + $amount :
                                        $budget_prev_amount - $amount));
    
                $data_insert = array();
                $data_insert["budget_amount"] = $budget_amount;
                $data_insert["account_chart_id"] = $chart_id;
                $data_insert["budget_type"] = $entry_type == 1 ? "debit" : "credit";
                $data_insert["year"] = $year;
                $data_insert["create_time"] = date("Y-m-d H:i:s");
                $data_insert["update_time"] = date("Y-m-d H:i:s");
                $this->db->insert('coop_account_budget_year', $data_insert);
            }
        } else {
            $sub_chart_id = substr($chart_id,0,1);
            $type_account_setting = $this->db->select("type, value")->from("coop_account_setting")->where("type = 'profit_chart'")->get()->row();
            $account_chart_id = $type_account_setting->value;
            $budget_year = $this->db->select("id ,budget_amount")->from("coop_account_budget_year")->where("account_chart_id = '".$account_chart_id."' AND year = '".$year."'")->order_by("year desc")->get()->row();
            $account_budget_year_id = $budget_year->id;
            $budget_amount = $budget_year->budget_amount;
            if ($sub_chart_id == '4'){
                if ($account_type == 'debit'){
                    $new_amount = $amount*(-1);
                }else if($account_type == 'credit'){
                    $new_amount = $amount;
                }
            }else if ($sub_chart_id == '5'){
                if ($account_type == 'debit'){
                    $new_amount = $amount;
                }else if($account_type == 'credit'){
                    $new_amount = $amount*(-1);
                }
            }
            $budget_amount += $new_amount;

            $data = array(
                'budget_amount' => $budget_amount
            );
            $this->db->where('id', $account_budget_year_id);
            $this->db->update('coop_account_budget_year', $data);
        }
    }

    public function get_setting($key) {
        $result = $this->db->select("*")->from("coop_account_setting")->where("type = '".$key."'")->order_by("created_at")->get()->row_array();
        if(!empty($result)) {
            return $result['value'];
        } else {
            return NULL;
        }
    }

    /*
        date format : yyyy-mm-dd
    */
    public function get_voucher_page_by_date($date) {
        $result = array();
        $voucher_per_page = $this->get_setting("voucher_per_page");
        $cash_chart_id = $this->get_setting("cash_chart_id");
        $account_datas = $this->db->select("t2.account_detail_id")
                                    ->from("coop_account as t1")
                                    ->join("coop_account_detail as t2", "t1.account_id = t2.account_id", "INNER")
                                    ->where("t1.account_datetime = '".$date."' AND (t1.account_status = 0 OR t1.account_status IS NULL) AND (t2.account_chart_id != '".$cash_chart_id."' OR t1.journal_type = 'S')")
                                    ->order_by("t1.account_id, t2.account_detail_id")
                                    ->get()->result_array();

        if(!empty($account_datas)) {
            $result['page_count'] = count($account_datas);
            $i = 1;
            foreach($account_datas as $data) {
                $result['page'][$data['account_detail_id']] = ceil($i/$voucher_per_page);
                $i++;
            }
        } else {
            $result['page_count'] = 0;
            $result['page'] = array();
        }
        return $result;
    }

    //Disable auto insert / Wait for description.
    public function insert_account_transaction($data){
        // $data_insert = array();
        // if(!empty($data['coop_account']['ref'])){
        //     $data_insert['ref_id'] = $data['coop_account']['ref'];
        //     $data_insert['ref_type'] = $data['coop_account']['ref_type'];
        //     $data_insert['process'] = $data['coop_account']['process'];
        // }

        // $data_insert['account_description'] = $data['coop_account']['account_description'];
        // $data_insert['account_datetime'] = $data['coop_account']['account_datetime'];
        // $year = substr($data['coop_account']['account_datetime'], 0,4);
        // $month = (int)substr($data['coop_account']['account_datetime'], 5,2);

        // $account_period = $this->db->select("accm_month_ini")->from("coop_account_period_setting")->order_by("accm_date_modified desc")->get()->row();
        // if(empty($month)) $month = $account_period->accm_month_ini;
        // $year_be = $account_period->accm_month_ini <= $month ? $year + 543 : $year + 543 - 1;

        // $data_insert['account_status'] = '1';
        // $this->db->insert('coop_account', $data_insert);

        // $this->db->select('account_id');
        // $this->db->from("coop_account");
        // $this->db->order_by("account_id DESC");
        // $this->db->limit(1);
        // $row = $this->db->get()->result_array();

        // $account_id = $row[0]['account_id'];

        // $journal_type = "J";
        // $account_cash = $this->db->select("*")->from('coop_account_setting')->where("type = 'cash_chart_id'")->get()->row();
        // $cash_chart_id = $account_cash->value;

        // foreach($data['coop_account_detail'] as $key => $value){
        //     if($value['account_amount'] > 0){
        //         $data_insert = array();
        //         $data_insert['account_id'] = $account_id;
        //         $data_insert['account_type'] = $value['account_type'];
        //         $data_insert['account_amount'] = $value['account_amount'];
        //         $data_insert['account_chart_id'] = $value['account_chart_id'];
        //         $this->db->insert('coop_account_detail', $data_insert);

        //         $this->account_transaction->increase_decrease_budget_year($value['account_chart_id'], $value['account_amount'], $value['account_type'], $year_be, 1);

        //         if($value['account_chart_id'] == $cash_chart_id && $journal_type == "J") {
        //             $journal_type = $value['account_type'] == "credit" ? "P" : "R";
        //         } else {
        //             $journal_type = "J";
        //         }
        //     }
        // }

        // $year_lead = $year + 543 - 2500;
        // $last_journal_ref_account = $this->db->select("journal_ref, RIGHT(journal_ref, 6) as count_journal_ref")->from("coop_account")->where("journal_ref LIKE '__".$year_lead."%'")->order_by("RIGHT(journal_ref, 6) desc")->get()->row();
        // $journal_ref = '';
        // if(!empty($last_journal_ref_account)) {
        //     $nxt = sprintf('%06d', ((int)$last_journal_ref_account->count_journal_ref) + 1);
        //     $journal_ref = $journal_type.$year_lead.$nxt;
        // } else {
        //     $journal_ref = $journal_type.$year_lead."000001";
        // }

        // $data_update = array();
        // $data_update["journal_type"] = $journal_type;
        // $data_update["journal_ref"] = $journal_ref;
        // $this->db->where('account_id', $account_id);
        // $this->db->update('coop_account', $data_update);
        // return $account_id;
    }

    public function generate_date_no($date) {
        $ext_no = $this->db->select("*")->from("coop_account_date_no")->where("date = DATE('".$date."')")->get()->row_array();
        $no = NULL;
        if(!empty($ext_no)) {
            $no = $ext_no['no'];
        } else {
            $perv_no = $this->db->select("*")->from("coop_account_date_no")->where("YEAR(date) = YEAR('".$date."') AND date < '".$date."'")->order_by("no DESC")->get()->row_array();
            if(!empty($perv_no)) {
                $no = $perv_no['no'] + 1;
            } else {
                $yearPrefix = substr($date, 0,4) + 543 - 2500;
                $no = $yearPrefix."00000001";
            }

            $data_insert = array();
            $data_insert['date'] = $date;
            $data_insert['no'] = $no;
            $data_insert["created_at"] = date("Y-m-d H:i:s");
            $data_insert["updated_at"] = date("Y-m-d H:i:s");
            $this->db->insert('coop_account_date_no', $data_insert);

            //Check if date after $date exist no must re-render.
            $date_nos = $this->db->select("*")->from("coop_account_date_no")->where("YEAR(date) = YEAR('".$date."') AND date > '".$date."'")->order_by("no")->get()->result_array();
            $nxt_no = $no;
            foreach($date_nos as $d_no) {
                $nxt_no++;
                $data_update = array();
                $data_update['no'] = $nxt_no;
                $data_update["updated_at"] = date("Y-m-d H:i:s");
                $this->db->where('id', $d_no["id"]);
                $this->db->update('coop_account_date_no', $data_update);

                $accounts = $this->db->select("*")->from("coop_account")->where("account_datetime = '".$d_no['date']."'")->get()->result_array();
                $data_updates = array();
                foreach($accounts as $account) {
                    $data_update = array();
                    $data_update['account_id'] = $account['account_id'];
                    $data_update['journal_ref'] = $account['journal_type'].$nxt_no;
                    $data_updates[] = $data_update;
                }

                if(!empty($data_updates)) {
                    $this->db->update_batch('coop_account', $data_updates, 'account_id');
                }
            }
        }

        return $no;
    }

    public function get_voucher_number($date, $type) {
        $no = "";

        $curr_account = $this->db->select("journal_ref")->from("coop_account")->where("account_datetime = '".$date."' AND journal_type = '".$type."' AND account_status = '0'")->get()->row_array();
        if(!empty($curr_account)) {
            $no = substr($curr_account['journal_ref'], 1);
        } else {
            $perv_account = $this->db->select("journal_ref")
                                        ->from("coop_account")
                                        ->where("YEAR(account_datetime) = YEAR('".$date."') AND account_datetime < '".$date."' AND journal_type = '".$type."' AND account_status = '0'")
                                        ->order_by("account_datetime DESC, journal_ref DESC")
                                        ->get()->row_array();

            if(!empty($perv_account)) {
                $no = substr($perv_account['journal_ref'], 1) + 1;
            } else {
                $yearPrefix = substr($date, 0,4) + 543 - 2500;
                $no = $yearPrefix."00000001";
            }

            //Check if date after $date exist, no must re-render.
            $fut_accounts = $this->db->select("account_datetime, journal_type")
                                        ->from("coop_account")
                                        ->where("YEAR(account_datetime) = YEAR('".$date."') AND account_datetime > '".$date."' AND journal_type = '".$type."' AND account_status = '0'")
                                        ->order_by("account_datetime, journal_ref")
                                        ->group_by("account_datetime")
                                        ->get()->result_array();

            $nxt_no = $no;
            $data_updates = array();
            foreach($fut_accounts as $account) {
                $nxt_no++;
                $data_update = array();
                $data_update['account_datetime'] = $account['account_datetime'];
                $data_update['journal_ref'] = $account['journal_type'].$nxt_no;
                $data_updates[] = $data_update;
            }
            if(!empty($data_updates)) {
                $this->db->update_batch('coop_account', $data_updates, 'account_datetime');
            }
        }

        return $no;
    }

    /*
        $type must be array or NULL.
    */
    public function get_account_charts($type) {
        $result = array();

        $where = "cancel_status IS NULL";
        if(!empty($type)) {
            $where .= " AND type IN (".implode(',',$type).")";
        }
        $charts = $this->db->select("account_chart_id, account_chart, level, entry_type, type")->from("coop_account_chart")->where($where)->get()->result_array();

        $result['datas'] = $charts;
        return $result;
    }

    /*
        $status must be array or NULL.
    */
    public function get_account_bank($id, $status) {
        $result = array();

        $where = "1=1";
        if(!empty($id)) {
            $where .= " AND account_bank_id = '".$id."'";
        }
        if(!empty($status)) {
            $where .= " AND status IN (".implode(',',$status).")";
        }

        $accounts = $this->db->select("*")->from("coop_account_bank")->where($where)->get()->result_array();

        $result['datas'] = $accounts;
        return $result;
    }
}
