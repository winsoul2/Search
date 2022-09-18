
<?php if( ! defined('BASEPATH')) exit('No direct script access allowed');

class Account_call_chart_report extends CI_Model {
    public function __construct()
    {
        parent::__construct();
        //$this->load->database();
        # Load libraries
        //$this->load->library('parser');
        $this->load->helper(array('html', 'url'));
    }


        function call_account_call_chart_report($month,$year){
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
            $where_date = " AND t0.account_datetime BETWEEN '".$s_date."' AND '".$e_date."'";
        }else{
            $day = '';
            $month = '';
            $year = (@$year-543);
            $file_name_text = ($year+543);
            $where_date = " AND t0.account_datetime BETWEEN '".$year."-01-01 00:00:00.000' AND '".$year."-12-31 23:59:59.000' ";
        }
        $arr_data['day'] = $day;
        $arr_data['month'] = $month;
        $arr_data['year'] = $year;
        $arr_data['where_date'] = $where_date;
        $arr_data['file_name_text'] = $file_name_text;

        $this->db->select(array('t0.*','t1.*','t2.account_chart'));
        $this->db->from('coop_account as t0 ');
        $this->db->join('coop_account_detail as t1','t0.account_id = t1.account_id','inner');
        $this->db->join('coop_account_chart as t2','t1.account_chart_id = t2.account_chart_id','inner');
        $this->db->where("t0.account_status <> '2' ".$where_date);
        $this->db->order_by("t2.account_chart_id ASC");


        $rs = $this->db->get()->result_array();

        $data = array();
        $sum_debit = array();
        $sum_credit = array();
        $count_type = array();

        foreach($rs as $key => $row){
            $this->db->select(array(
                'account_type'
            ));
            $this->db->from('coop_account_detail');
            $this->db->where("account_id = '".$row['account_id']."'");
            $rs_main = $this->db->get()->result_array();
            $data_main_arr = array();
            foreach($rs_main as $key3 => $row_main){
                @$data_main_arr[$row_main['account_type']]++;
            }
            if($data_main_arr['credit'] > $data_main_arr['debit']){
                $data_main = 'debit';
            }else{
                $data_main = 'credit';
            }
            if($row['account_type'] == 'credit'){
                $account_find = 'debit';
            }else{
                $account_find = 'credit';
            }

            $this->db->select(array(
                't0.account_description',
                't1.account_chart_id',
                't1.account_type',
                't1.account_amount',
                't2.account_chart'
            ));
            $this->db->from('coop_account as t0 ');
            $this->db->join('coop_account_detail as t1','t0.account_id = t1.account_id','inner');
            $this->db->join('coop_account_chart as t2','t1.account_chart_id = t2.account_chart_id','inner');
            $this->db->where("t1.account_id = '".$row['account_id']."' AND t1.account_type = '".$account_find."'");
            $rs_detail = $this->db->get()->result_array();
            foreach($rs_detail as $key2 => $row_detail){
                if($row_detail['account_type'] == $data_main){
                    $account_amount = $row['account_amount'];
                }else{
                    $account_amount = $row_detail['account_amount'];
                }
                if(empty($count_type[$row['account_chart_id']])){
                    $count_type[$row['account_chart_id']]['debit'] = 0;
                    $count_type[$row['account_chart_id']]['credit'] = 0;
                }
                if(empty($sum_debit[$row['account_chart_id']])){
                    $sum_debit[$row['account_chart_id']] = 0;
                }
                if(empty($sum_credit[$row['account_chart_id']])){
                    $sum_credit[$row['account_chart_id']] = 0;
                }
                $data[$row['account_chart_id']]['account_chart'] = $row['account_chart'];

                if($row_detail['account_type']=='debit'){
                    $data[$row['account_chart_id']][$row_detail['account_type']][$count_type[$row['account_chart_id']]['debit']]['account_description'] = $row['account_description'];

                    $data[$row['account_chart_id']][$row_detail['account_type']][$count_type[$row['account_chart_id']]['debit']]['account_chart'] = $row_detail['account_chart'];
                    $data[$row['account_chart_id']][$row_detail['account_type']][$count_type[$row['account_chart_id']]['debit']]['account_amount'] = $account_amount;
                    $data[$row['account_chart_id']][$row_detail['account_type']][$count_type[$row['account_chart_id']]['debit']]['account_datetime'] = date('Y-m-d',strtotime($row['account_datetime']));
                    $sum_debit[$row['account_chart_id']] += $account_amount;
                    $count_type[$row['account_chart_id']]['debit']++;
                }else{
                    $data[$row['account_chart_id']][$row_detail['account_type']][$count_type[$row['account_chart_id']]['debit']]['account_description'] = $row['account_description'];
                    $data[$row['account_chart_id']][$row_detail['account_type']][$count_type[$row['account_chart_id']]['credit']]['account_chart'] = $row_detail['account_chart'];
                    $data[$row['account_chart_id']][$row_detail['account_type']][$count_type[$row['account_chart_id']]['credit']]['account_amount'] = $account_amount;
                    $data[$row['account_chart_id']][$row_detail['account_type']][$count_type[$row['account_chart_id']]['credit']]['account_datetime'] = date('Y-m-d',strtotime($row['account_datetime']));
                    $sum_credit[$row['account_chart_id']] += $account_amount;
                    $count_type[$row['account_chart_id']]['credit']++;
                }

            }
        }
        $index = 0;
        foreach ($count_type as $key => $val) {
            $this->db->select(array(
                'id'
            ));
            $this->db->from('coop_account_chart_ledger');
            $this->db->where("account_chart_id = '".$key."' AND mount = '".$month."'  AND year = '".$year."'     ");
            $check_update = $this->db->get()->result_array();

            $timestamp = date('Y-m-d H:i:s');
            if(empty($check_update[0]['id'])) {
                $data_insert[$index]['credit_account_chart'] = $sum_credit[$key];
                $data_insert[$index]['debit_account_chart'] = $sum_debit[$key];
                $data_insert[$index]['mount'] = $month;
                $data_insert[$index]['year'] = $year;
                $data_insert[$index]['account_chart_id'] = $key;
                $data_insert[$index]['create_time'] = date('Y-m-t');
            }else {
                $confirm[$index]['credit_account_chart'] = $sum_credit[$key];
                $confirm[$index]['debit_account_chart'] = $sum_debit[$key];
                $confirm[$index]['mount'] = $month;
                $confirm[$index]['year'] = $year;
                $confirm[$index]['account_chart_id'] = $key;
                $confirm[$index]['create_time'] = date('Y-m-t');
                $confirm[$index]['id'] = $check_update[0]['id'];
                $confirm[$index]['update_time'] = $timestamp;

            }
            $index++;

        }

        if(sizeof($data_insert)){
            $this->db->insert_batch('coop_account_chart_ledger', $data_insert);
        }
        if(sizeof($confirm)){
            $this->db->update_batch('coop_account_chart_ledger', $confirm, 'id');
        }


    }


}
