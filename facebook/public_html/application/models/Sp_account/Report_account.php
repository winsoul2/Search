<?php if( ! defined('BASEPATH')) exit('No direct script access allowed');

class Report_account extends CI_Model {
    public function __construct() {
        parent::__construct();
        $this->load->model('Sp_account/Account', 'account');
    }

    /* Conditions must be array : Uesd conditions = array() in case of null or empty */

    public function check_day_book($group_id, $conditions) {
        if(!empty($conditions["report_date"])){
            $date_arr = explode('/',$conditions['report_date']);
            $day = (int)@$date_arr[0];
            $month = (int)@$date_arr[1];
            $year = (int)@$date_arr[2];
            $year = $year - 543;

            $s_date = $year.'-'.sprintf("%02d",@$month).'-'.sprintf("%02d",@$day).' 00:00:00.000';
            $e_date = $year.'-'.sprintf("%02d",@$month).'-'.sprintf("%02d",@$day).' 23:59:59.000';
            $where = " AND account_datetime BETWEEN '".$s_date."' AND '".$e_date."'";
        } else {
            if(!empty($conditions['month']) && !empty($conditions['year'])){
                $day = '';
                $month = $conditions['month'];
                $year = ($conditions['year']-543);
                $s_date = $year.'-'.sprintf("%02d",$month).'-01'.' 00:00:00.000';
                $e_date = date('Y-m-t',strtotime($s_date)).' 23:59:59.000';
                $where = " AND account_datetime BETWEEN '".$s_date."' AND '".$e_date."'";
            }else{
                $day = '';
                $month = '';
                $year = (@$conditions['year']-543);
                $where = " AND account_datetime BETWEEN '".$year."-01-01 00:00:00.000' AND '".$year."-12-31 23:59:59.000' ";
            }
        }

        $this->db->select(array('*'));
        $this->db->from('coop_sp_account');
        $this->db->where("group_id = '".$this->account_group_id."' AND (account_status != '2' OR account_status IS NULL ) AND (status_audit <> '1' OR status_audit is null )  ".$where);
        $this->db->order_by("account_datetime ASC");
        $this->db->limit(1);
        $row = $this->db->get()->result_array();
        if(!empty($row)) {
            return "success";
        }
        return "non_data";
    }

    public function get_day_book_data($group_id, $conditions) {
        $data = array();

        $data_account_detail = array();
        $result = $this->get_account_day_book($group_id, $conditions, "R");
        foreach($result as $key => $data) {
            $data_account_detail[$key][] = $data;
        }
        $result = $this->get_account_day_book($group_id, $conditions, "P");
        foreach($result as $key => $data) {
            $data_account_detail[$key][] = $data;
        }
        $result = $this->get_account_day_book($group_id, $conditions, "J");
        foreach($result as $key => $data) {
            $data_account_detail[$key][] = $data;
        }
        $result = $this->get_account_day_book($group_id, $conditions, "S");
        foreach($result as $key => $data) {
            $data_account_detail[$key][] = $data;
        }
        if(!empty($data_account_detail)) {
            $data['datas'] = $data_account_detail;
            $data['status'] = "success";
        } else {
            $data['status'] = "no_data";
        }
        return $data;
    }

    public function get_account_day_book($group_id, $data, $type = null) {
        $where = " AND t0.journal_type = '{$type}' AND t0.group_id = '".$group_id."'";
        if($data['report_date'] != ''){
            $date_arr = explode('/',$data['report_date']);
            $day = (int)@$date_arr[0];
            $month = (int)@$date_arr[1];
            $year = (int)@$date_arr[2];
            $year -= 543;
            $s_date = $year.'-'.sprintf("%02d",@$month).'-'.sprintf("%02d",@$day).' 00:00:00.000';
            $e_date = $year.'-'.sprintf("%02d",@$month).'-'.sprintf("%02d",@$day).' 23:59:59.000';
            $where .= " AND account_datetime BETWEEN '".$s_date."' AND '".$e_date."'";
        }else{
            if($data['month']!='' && $data['year']!=''){
                $day = '';
                $month = @$data['month'];
                $year = (@$data['year']-543);

                $s_date = $year.'-'.sprintf("%02d",$month).'-01'.' 00:00:00.000';
                $e_date = date('Y-m-t',strtotime($s_date)).' 23:59:59.000';
                $where .= " AND account_datetime BETWEEN '".$s_date."' AND '".$e_date."'";
            }else{
                $day = '';
                $month = '';
                $year = ($data['year']-543);
                $where .= " AND account_datetime BETWEEN '".$year."-01-01 00:00:00.000' AND '".$year."-12-31 23:59:59.000' ";
            }
        }
        $arr_data['day'] = $day;
        $arr_data['month'] = $month;
        $arr_data['year'] = $year;

        $data = array();
        $sort_array = array();
        $data_account_detail = array();

        $row = array();
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
                            `t0`.journal_type,
                            LEFT(`t0`.account_datetime, 10) AS account_datetime,
                            `t1`.*,
                            `t2`.`account_chart` 
                        FROM
                            `coop_sp_account` AS `t0`
                            INNER JOIN `coop_sp_account_detail` AS `t1` ON t0.account_id = t1.account_id
                            INNER JOIN `coop_sp_account_chart` AS `t2` ON `t1`.`account_chart_id` = `t2`.`account_chart_id` AND t0.group_id = t2.group_id
                        WHERE
                            1 = 1 
                            AND (`account_status` != '2' OR `account_status` IS NULL )
                            AND( `t0`.`status_audit` <> '1' OR `t0`.`status_audit` IS NULL)
                            {$where}
                        ) as t_all");
        $this->db->order_by("t_all.account_datetime ASC,
                            t_all.account_type DESC,
                            t_all.account_chart_id ASC");

        $row_detail = $this->db->get()->result_array();
        $row['data']['account_detail'] = $row_detail;

        foreach($row['data']  as $key => $row) {
            $account_datetime ='';
            $account_datetime =  explode(" ",$row['account_datetime']);
            foreach($row as $key2 => $row_detail){
                $i_d = 0;
                $account_datetime ='';
                $account_datetime =  explode(" ",$row_detail['account_datetime']);
                $data_account_detail[$account_datetime[0]][$row_detail['account_id']][$row_detail['account_chart_id'].$row_detail['account_type'].$key2]['account_chart_id'] = $row_detail['account_chart_id'];
                $data_account_detail[$account_datetime[0]][$row_detail['account_id']][$row_detail['account_chart_id'].$row_detail['account_type'].$key2]['account_chart'] = $row_detail['account_chart'];
                $data_account_detail[$account_datetime[0]][$row_detail['account_id']][$row_detail['account_chart_id'].$row_detail['account_type'].$key2]['account_type'] = $row_detail['account_type'];
                $data_account_detail[$account_datetime[0]][$row_detail['account_id']][$row_detail['account_chart_id'].$row_detail['account_type'].$key2]['account_amount'] += $row_detail['account_amount'];
                $data_account_detail[$account_datetime[0]][$row_detail['account_id']][$row_detail['account_chart_id'].$row_detail['account_type'].$key2]['journal_type'] = $row_detail['journal_type'];
            }
        }
        return $data_account_detail;
    }

    public function check_account_chart($group_id, $conditions) {
        $where = "t1.group_id = '".$group_id."'";
        if ($conditions['from_date'] != '' && !empty($conditions['thru_date'])) {
            $date_arr = explode('/',$conditions['from_date']);
            $day = (int)$date_arr[0];
            $month = (int)$date_arr[1];
            $year = (int)$date_arr[2];
            $year -= 543;
            $s_date = $year.'-'.sprintf("%02d",@$month).'-'.sprintf("%02d",@$day).' 00:00:00.000';
            $date_arr = explode('/',$conditions['thru_date']);
            $day = (int)$date_arr[0];
            $month = (int)$date_arr[1];
            $year = (int)$date_arr[2];
            $year -= 543;
            $e_date = $year.'-'.sprintf("%02d",@$month).'-'.sprintf("%02d",@$day).' 23:59:59.000';
            $where .= " AND t1.account_datetime BETWEEN '".$s_date."' AND '".$e_date."'";
        } else {
            if($conditions['month']!='' && $conditions['year']!=''){
                $day = '';
                $month = $conditions['month'];
                $year = ($conditions['year']-543);
                $s_date = $year.'-'.sprintf("%02d",@$month).'-01'.' 00:00:00.000';
                $e_date = date('Y-m-t',strtotime($s_date)).' 23:59:59.000';
                $where .= " AND t1.account_datetime BETWEEN '".$s_date."' AND '".$e_date."'";
            }else{
                $day = '';
                $month = '';
                $year = ($conditions['year']-543);
                $where .= " AND t1.account_datetime BETWEEN '".$year."-01-01 00:00:00.000' AND '".$year."-12-31 23:59:59.000' ";
            }
        }

        if(!empty($conditions["account_chart_id"])) {
            $where .= " AND t2.account_chart_id = '".$conditions["account_chart_id"]."'";
        }

        $this->db->select(array(
            't1.account_id',
            't1.account_datetime',
            't2.account_type'
        ));
        $this->db->from('coop_sp_account as t1');
        $this->db->join('coop_sp_account_detail as t2','t1.account_id = t2.account_id','inner');
        $this->db->where($where." AND (t1.status_audit <> '1' OR t1.status_audit is null)" );
        $this->db->order_by("t1.account_datetime ASC");
        $this->db->limit(1);
        $row = $this->db->get()->result_array();
        if(!empty($row)){
            return "success";
        } else {
            return "no_data";
        }
    }

    public function get_account_chart_report($group_id, $conditions) {
        $arr_data = array();
        $from_year_be = 0;
        $from_month = 0;
        $balance_thru_date = "";
        $s_date = "";
        $e_date = "";
        if($conditions['from_date'] != '' && !empty($conditions['thru_date'])) {
            $date_arr = explode('/',$conditions['from_date']);
            $day = (int)$date_arr[0];
            $month = (int)$date_arr[1];
            $from_month = $month;
            $year = (int)$date_arr[2];
            $from_year_be = $year;
            $year -= 543;
            $s_date = $year.'-'.sprintf("%02d",@$month).'-'.sprintf("%02d",@$day).' 00:00:00.000';
            $date_arr = explode('/',$conditions['thru_date']);
            $day = (int)$date_arr[0];
            $month = (int)$date_arr[1];
            $year = (int)$date_arr[2];
            $year -= 543;
            $e_date = $year.'-'.sprintf("%02d",@$month).'-'.sprintf("%02d",@$day).' 23:59:59.000';
            $where = " AND t0.account_datetime BETWEEN '".$s_date."' AND '".$e_date."'";
        } else if (!empty($conditions['month']) && !empty($conditions['year'])) {
            $month = $conditions['month'];
            $year = ($conditions['year']-543);
            $s_date = $year.'-'.sprintf("%02d",@$month).'-01'.' 00:00:00.000';
            $e_date = date('Y-m-t',strtotime($s_date)).' 23:59:59.000';
            $where = " AND t0.account_datetime BETWEEN '".$s_date."' AND '".$e_date."'";
            $from_year_be = $year + 543;
        } else if (!empty($conditions['year'])) {
            $year = ($_POST['year']-543);
            $s_date = $year."-01-01 00:00:00.000";
            $e_date = $year."-12-31 23:59:59.000"; 
            $where = " AND t0.account_datetime BETWEEN '".$s_date."' AND '".$e_date."'";
            $from_year_be = $year + 543;
        }

        $arr_data['s_date'] = $s_date;
        $arr_data['e_date'] = $e_date;
        $balance_thru_date = $s_date;

        if(!empty($conditions["account_chart_id"])) {
            $where .= " AND t1.account_chart_id = '".$conditions["account_chart_id"]."'";
        }

        $data = array();

        $sort_array = array();
        $data_account_detail = array();

        $row  = array();

        $this->db->select(array('*'));
        $this->db->from("(SELECT
                            `t0`.account_number,
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
                            `coop_sp_account` AS `t0`
                            INNER JOIN `coop_sp_account_detail` AS `t1` ON t0.account_id = t1.account_id
                            INNER JOIN `coop_sp_account_chart` AS `t2` ON `t1`.`account_chart_id` = `t2`.`account_chart_id` AND t0.group_id = t2.group_id
                        WHERE
                            1 = 1
                            AND (`account_status` != '2' OR `account_status` IS NULL )
                            AND( `t0`.`status_audit` <> '1' OR `t0`.`status_audit` IS NULL )
                            AND t0.group_id = '".$group_id."'
                            $where
                        ) as t_all");
        $this->db->order_by("t_all.account_chart_id ASC,t_all.account_datetime ASC");
        $row_detail = $this->db->get()->result_array();
        $row['data']['account_detail'] = $row_detail;

        foreach($row['data']  as $key => $row) {
            $account_datetime ='';
            $account_datetime =  explode(" ",$row['account_datetime']);
            foreach($row as $key2 => $row_detail){
                $i_d = 0;
                $account_datetime ='';
                $account_datetime =  explode(" ",$row_detail['account_datetime']);
                $data_account_detail[$account_datetime[0]][$row_detail['account_chart_id']][$row_detail['account_type']][$key2]['account_chart_id'] = $row_detail['account_chart_id'];
                $data_account_detail[$account_datetime[0]][$row_detail['account_chart_id']][$row_detail['account_type']][$key2]['account_chart'] = $row_detail['account_chart'];
                $data_account_detail[$account_datetime[0]][$row_detail['account_chart_id']][$row_detail['account_type']][$key2]['account_type'] = $row_detail['account_type'];
                $data_account_detail[$account_datetime[0]][$row_detail['account_chart_id']][$row_detail['account_type']][$key2]['account_amount'] += $row_detail['account_amount'];
                $data_account_detail[$account_datetime[0]][$row_detail['account_chart_id']][$row_detail['account_type']][$key2]['account_description'] = $row_detail['description'];
                $data_account_detail[$account_datetime[0]][$row_detail['account_chart_id']][$row_detail['account_type']][$key2]['journal_ref'] = $row_detail['journal_ref'];
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
            foreach($value_main as $key_type_dc => $value_data_dc_r) {
                $group_id = substr($key_type_dc,0,1);
                if($group_id == 1 || $group_id == 5) {
                    if(!empty($value_data_dc_r["debit"])) {
                        foreach($value_data_dc_r["debit"] as $value_data_dc) {
                            $data_filter[$value_data_dc['account_chart_id']][$number_count]['account_description'] = $value_data_dc['account_description'];
                            $data_filter[$value_data_dc['account_chart_id']][$number_count]['account_amount'] += $value_data_dc['account_amount'];
                            $data_filter[$value_data_dc['account_chart_id']][$number_count]['account_chart_id'] = $value_data_dc['account_chart_id'];
                            $data_filter[$value_data_dc['account_chart_id']][$number_count]['account_chart'] = $value_data_dc['account_chart'];
                            $data_filter[$value_data_dc['account_chart_id']][$number_count]['account_type'] = $value_data_dc['account_type'];
                            $data_filter[$value_data_dc['account_chart_id']][$number_count]['journal_ref'] = $value_data_dc['journal_ref'];
                            $data_filter[$value_data_dc['account_chart_id']][$number_count]['account_datetime']  = $key;

                            $number_count++;
                            if($value_data_dc['account_type'] == 'debit'){
                                $sum_debit[$value_data_dc['account_chart_id']] += $value_data_dc['account_amount'];
                            }else{
                                $sum_credit[$value_data_dc['account_chart_id']] += $value_data_dc['account_amount'];
                            }
                        }
                    }
                    if(!empty($value_data_dc_r["credit"])) {
                        foreach($value_data_dc_r["credit"] as $value_data_dc) {
                            $data_filter[$value_data_dc['account_chart_id']][$number_count]['account_description'] = $value_data_dc['account_description'];
                            $data_filter[$value_data_dc['account_chart_id']][$number_count]['account_amount'] += $value_data_dc['account_amount'];
                            $data_filter[$value_data_dc['account_chart_id']][$number_count]['account_chart_id'] = $value_data_dc['account_chart_id'];
                            $data_filter[$value_data_dc['account_chart_id']][$number_count]['account_chart'] = $value_data_dc['account_chart'];
                            $data_filter[$value_data_dc['account_chart_id']][$number_count]['account_type'] = $value_data_dc['account_type'];
                            $data_filter[$value_data_dc['account_chart_id']][$number_count]['journal_ref'] = $value_data_dc['journal_ref'];
                            $data_filter[$value_data_dc['account_chart_id']][$number_count]['account_datetime']  = $key;

                            $number_count++;
                            if($value_data_dc['account_type'] == 'debit'){
                                $sum_debit[$value_data_dc['account_chart_id']] += $value_data_dc['account_amount'];
                            }else{
                                $sum_credit[$value_data_dc['account_chart_id']] += $value_data_dc['account_amount'];
                            }
                        }
                    }
                } else {
                    if(!empty($value_data_dc_r["credit"])) {
                        foreach($value_data_dc_r["credit"] as $value_data_dc) {
                            $data_filter[$value_data_dc['account_chart_id']][$number_count]['account_description'] = $value_data_dc['account_description'];
                            $data_filter[$value_data_dc['account_chart_id']][$number_count]['account_amount'] += $value_data_dc['account_amount'];
                            $data_filter[$value_data_dc['account_chart_id']][$number_count]['account_chart_id'] = $value_data_dc['account_chart_id'];
                            $data_filter[$value_data_dc['account_chart_id']][$number_count]['account_chart'] = $value_data_dc['account_chart'];
                            $data_filter[$value_data_dc['account_chart_id']][$number_count]['account_type'] = $value_data_dc['account_type'];
                            $data_filter[$value_data_dc['account_chart_id']][$number_count]['journal_ref'] = $value_data_dc['journal_ref'];
                            $data_filter[$value_data_dc['account_chart_id']][$number_count]['account_datetime']  = $key;

                            $number_count++;
                            if($value_data_dc['account_type'] == 'debit'){
                                $sum_debit[$value_data_dc['account_chart_id']] += $value_data_dc['account_amount'];
                            }else{
                                $sum_credit[$value_data_dc['account_chart_id']] += $value_data_dc['account_amount'];
                            }
                        }
                    }
                    if(!empty($value_data_dc_r["debit"])) {
                        foreach($value_data_dc_r["debit"] as $value_data_dc) {
                            $data_filter[$value_data_dc['account_chart_id']][$number_count]['account_description'] = $value_data_dc['account_description'];
                            $data_filter[$value_data_dc['account_chart_id']][$number_count]['account_amount'] += $value_data_dc['account_amount'];
                            $data_filter[$value_data_dc['account_chart_id']][$number_count]['account_chart_id'] = $value_data_dc['account_chart_id'];
                            $data_filter[$value_data_dc['account_chart_id']][$number_count]['account_chart'] = $value_data_dc['account_chart'];
                            $data_filter[$value_data_dc['account_chart_id']][$number_count]['account_type'] = $value_data_dc['account_type'];
                            $data_filter[$value_data_dc['account_chart_id']][$number_count]['journal_ref'] = $value_data_dc['journal_ref'];
                            $data_filter[$value_data_dc['account_chart_id']][$number_count]['account_datetime']  = $key;

                            $number_count++;
                            if($value_data_dc['account_type'] == 'debit'){
                                $sum_debit[$value_data_dc['account_chart_id']] += $value_data_dc['account_amount'];
                            }else{
                                $sum_credit[$value_data_dc['account_chart_id']] += $value_data_dc['account_amount'];
                            }
                        }
                    }
                }
            }
        }

        $account_chart_main = array();
        $this->db->select(array('*'));
        $this->db->from('coop_sp_account_chart');
        $this->db->where("group_id = '".$group_id."'");
        $this->db->order_by("account_chart_id ASC");
        $row = $this->db->get()->result_array();
        foreach($row as $key => $row_detail) {
            $account_chart_main[$row_detail['account_chart_id']] = $row_detail['account_chart'];
        }

        //Get account balance
        $account_balances = array();
        $account_period = $this->db->select("accm_month_ini")->from("coop_account_period_setting")->order_by("accm_date_modified desc")->get()->row();
        if(empty($from_month)) $from_month = $account_period->accm_month_ini;
        $account_prev_year = $account_period->accm_month_ini <= $from_month ? $from_year_be - 1 : $from_year_be - 2;
        $account_budget_years = $this->db->select("t1.account_chart_id, t1.entry_type, t2.budget_amount, t2.budget_type")
                                            ->from("coop_sp_account_chart as t1")
                                            ->join("coop_sp_account_budget_year as t2", "t1.account_chart_id = t2.account_chart_id AND year = '".$account_prev_year."' AND t1.group_id = t2.group_id", "left")
                                            ->where("t1.group_id = '".$group_id."'")
                                            ->get()->result_array();

        foreach($account_budget_years as $budget) {
            if(!empty($budget["budget_type"])) {
                $account_balances[$budget["account_chart_id"]]["type"] = $budget["budget_type"];
                $account_balances[$budget["account_chart_id"]]["amount"] = $budget["budget_amount"];
            } else {
                $account_balances[$budget["account_chart_id"]]["type"] = $budget["entry_type"] == 1 ? "debit" : "credit";
                $account_balances[$budget["account_chart_id"]]["amount"] = 0;
            }
        }

        $balance_from_date = ($account_prev_year + 1 - 543)."-".sprintf("%02d",$account_period->accm_month_ini)."-01 00:00:00.000";
        $where_balance = " AND t1.account_datetime >= '".$balance_from_date."' AND t1.account_datetime < '".$balance_thru_date."'";
        $account_details = $this->db->select("SUM(t2.account_amount) as amount, t2.account_type, t2.account_chart_id")
                                    ->from("coop_sp_account as t1")
                                    ->join("coop_sp_account_detail as t2", "t1.account_id = t2.account_id", "inner")
                                    ->where("(t1.account_status is null OR t1.account_status != 2) AND t1.group_id = '".$group_id."'".$where_balance)
                                    ->group_by("t2.account_chart_id, t2.account_type")
                                    ->get()->result_array();

        foreach($account_details as $detail) {
            if($account_balances[$detail["account_chart_id"]]["type"] == $detail["account_type"]) {
                $account_balances[$detail["account_chart_id"]]["amount"] += $detail["amount"];
            } else {
                $account_balances[$detail["account_chart_id"]]["amount"] -= $detail["amount"];
            }
        }

        $arr_data['row_budget'] = $budget;
        $arr_data['account_chart_main'] = $account_chart_main;
        $arr_data['data'] = $data_filter;
        $arr_data['sum_debit'] = $sum_debit;
        $arr_data['sum_credit'] = $sum_credit;
        $arr_data['account_balances'] = $account_balances;

        return $arr_data;
    }

    public function account_experimental_budget($group_id, $conditions) {
        if(@$conditions['report_date'] != ''){
            $date_arr = explode('/',@$conditions['report_date']);
            $day = (int)$date_arr[0];
            $month = (int)$date_arr[1];
            $year = (int)$date_arr[2];
            $year -= 543;
            $s_date = $year.'-'.sprintf("%02d",$month).'-'.sprintf("%02d",$day).' 00:00:00.000';
            $e_date = $year.'-'.sprintf("%02d",$month).'-'.sprintf("%02d",$day).' 23:59:59.000';
            $where = " AND t1.account_datetime BETWEEN '".$s_date."' AND '".$e_date."'";
        }else{
            if($conditions['month']!='' && $conditions['year']!=''){
                $day = '';
                $month = $conditions['month'];
                $year = ($conditions['year']-543);

                $s_date = $year.'-'.sprintf("%02d",$month).'-01'.' 00:00:00.000';
                $e_date = date('Y-m-t',strtotime($s_date)).' 23:59:59.000';
                $where = " AND t1.account_datetime BETWEEN '".$s_date."' AND '".$e_date."'";
            }else{
                $day = '';
                $month = '';
                $year = ($conditions['year']-543);
                $where = " AND t1.account_datetime BETWEEN '".$year."-01-01 00:00:00.000' AND '".$year."-12-31 23:59:59.000' ";
            }
        }

        $this->db->select(array('t1.account_id'));
        $this->db->from('coop_sp_account as t1');
        $this->db->where("1=1 {$where} AND t1.group_id = '".$group_id."' AND t1.status_audit <> '1' OR t1.status_audit is null");
        $rs = $this->db->get()->result_array();
        $row = @$rs[0];
        if(!empty($row)){
            return "success";
        } else {
            return "no_data";
        }
    }

    public function get_account_experimental_budget_data($group_id, $data) {
        $results = array();

        $month_arr = array('1'=>'มกราคม','2'=>'กุมภาพันธ์','3'=>'มีนาคม','4'=>'เมษายน','5'=>'พฤษภาคม','6'=>'มิถุนายน','7'=>'กรกฎาคม','8'=>'สิงหาคม','9'=>'กันยายน','10'=>'ตุลาคม','11'=>'พฤศจิกายน','12'=>'ธันวาคม');
        $where_acc = "";
        $where_prev_budget = "(t1.account_status = 0 OR t1.account_status IS NULL)";
        if(@$data['report_date'] != '') {
            $date_arr = explode('/',@$data['report_date']);
            $day = (int)$date_arr[0];
            $month = (int)$date_arr[1];
            $year = (int)$date_arr[2];
            $year_be = $year;
            $year -= 543;
            $textTitle = "ณ วันที่ ".$day." ".$month_arr[$month]." ".($year+543);
            $where = " AND mount = '".$month."'  AND year = '".$year."'   ";
            $where_acc = " AND account_datetime between '".$year.'-'.sprintf("%02d",@$month).'-'.sprintf("%02d",@$day)." 00:00:00.000'
                                                    AND '".$year.'-'.sprintf("%02d",@$month).'-'.sprintf("%02d",@$day)." 23:59:59.000'";
            $where_prev_budget .= " AND t1.account_datetime between '".$year."-01-01 00:00:00.000' AND '".$year."-".sprintf("%02d",@$month)."-".sprintf("%02d",($day-1))." 23:59:59.000'";
        } else {
            if(!empty($data['month']) && !empty($data['year'])) {
                $day = '';
                $month = @$data['month'];
                $year_be = $data['year'];
                $year = (@$data['year']-543);
                $last_day_of_month = date("t", strtotime($year."-".sprintf("%02d",$month)."-01"));
                $textTitle = "ระหว่างวันที่ 01 ".$month_arr[$month]." ".($year+543);
                $textTitle .= " ถึงวันที่ ".$last_day_of_month." ".$month_arr[$month]." ".($year+543);
                $where = " AND mount = '".$month."'  AND year = '".$year."'   ";
                $where_acc = " AND MONTH(account_datetime) = ".$month." AND YEAR(account_datetime) = ".$year;
                $where_prev_budget .= " AND MONTH(t1.account_datetime) < '".$month."' AND YEAR(t1.account_datetime) = '".$year."'";
            } else {
                $day = '';
                $month = '';
                $year_be = $data['year'];
                $year = (@$data['year']-543);
                $textTitle = "ประจำปี ".($year+543);
                $where = " AND mount = '".$month."'  AND year = '".$year."'   ";
                $where_acc = " AND YEAR(account_datetime) = ".$year;
            }
        }

        $results['day'] = $day;
        $results['month'] = $month;
        $results['year'] = $year;
        $results['textTitle'] = $textTitle;

        if($data['month'] == 1 ) {
            $old_mount = 12;
            $old_year =  $year-1;
        } else {
            $old_mount = $data['month'] -1 ;
            $old_year =  $year;
        }

        //ยอดยกมาเงินงบทดลอง
        //Get budget of last year
        $prev_budgets = array();
        $prev_budget_year_raw = $this->db->select("*")->from("coop_sp_account_budget_year")->where("year <= '".($year_be - 1)."' AND group_id = '".$group_id."'")->get()->result_array();
        foreach($prev_budget_year_raw as $budget) {
            if(!array_key_exists($budget["account_chart_id"], $prev_budgets)) {
                $prev_budgets[$budget["account_chart_id"]] = $budget;
            }
        }
        $prev_detail_raw = $this->db->select("t2.account_chart_id, t2.account_type, SUM(t2.account_amount) as amount, t3.entry_type")
                                    ->from("coop_sp_account as t1")
                                    ->join("coop_sp_account_detail as t2", "t1.account_id = t2.account_id", "inner")
                                    ->join("coop_sp_account_chart as t3", "t2.account_chart_id = t3.account_chart_id AND t1.group_id = t3.group_id", "inner")
                                    ->where($where_prev_budget." AND t1.group_id = '".$group_id."'")
                                    ->group_by("t2.account_chart_id, t2.account_type")
                                    ->get()->result_array();
        foreach($prev_detail_raw as $detail) {
            if(!empty($prev_budgets[$detail["account_chart_id"]])) {
                $prev_budgets[$detail["account_chart_id"]]["budget_amount"] += $detail["entry_type"] == 1 && $detail["account_type"] == "debit" ? $detail["amount"]
                                                                                : ($detail["entry_type"] == 1 && $detail["account_type"] == "credit" ? ($detail["amount"] * (-1))
                                                                                : ($detail["entry_type"] == 2 && $detail["account_type"] == "credit" ? $detail["amount"]
                                                                                : ($detail["amount"] * (-1))));
            } else {
                $prev_budgets[$detail["account_chart_id"]]["budget_amount"] += $detail["entry_type"] == 1 && $detail["account_type"] == "debit" ? $detail["amount"]
                                                                                : ($detail["entry_type"] == 1 && $detail["account_type"] == "credit" ? ($detail["amount"] * (-1))
                                                                                : ($detail["entry_type"] == 2 && $detail["account_type"] == "credit" ? $detail["amount"]
                                                                                : ($detail["amount"] * (-1))));
                $prev_budgets[$detail["account_chart_id"]]["budget_type"] = $detail["entry_type"] == 1 ? "debit" : "credit";
            }
        }
        $results["prev_budgets"] = $prev_budgets;

        //ยอดยกมาเงินยอดระหว่างเดือน
        $rs_chart_ledger = array();
        $details = $this->db->select("t2.*")
                        ->from("coop_sp_account as t1")
                        ->join("coop_sp_account_detail as t2", "t1.account_id = t2.account_id", "inner")
                        ->where("t1.group_id = '".$group_id."' AND (t1.account_status != 2 OR t1.account_status IS null)".$where_acc)
                        ->get()->result_array();
        foreach($details as $detail) {
            $rs_chart_ledger[$detail['account_chart_id']][$detail["account_type"]] += $detail["account_amount"];
        }
        $results['rs'] = $rs_chart_ledger;

        //ยอดยกมารายการทั้งหมด
        $this->db->select(array('*'));
        $this->db->from('coop_account_chart');
        $this->db->where("group_id = '".$group_id."'");
        $this->db->order_by("account_chart_id ASC");
        $data_chart = $this->db->get()->result_array();
        $results['data_chart'] = @$data_chart;

        return $results;
    }

    
    public function get_account_balance_data($group_id, $data) {
        $result = array();
        $month_arr = $this->month_arr;
        $month_short_arr = $this->month_short_arr;

        $result['month_arr'] = $month_arr;
        $result['month_short_arr'] = $month_short_arr;

        $account_period = $this->db->select("accm_month_ini")->from("coop_account_period_setting")->order_by("accm_date_modified desc")->get()->row();

        $where_prev_year = "1=1"; // Condition for prev year budget
        $where_prev = "1=1"; // condition for prev account data for current year
        $where_period = "1=1"; // condition for account data for current period
        if(!empty($data["from_date"]) && !empty($data["thru_date"])) {
            $from_date_arr = explode('/',$data['from_date']);
            $from_date = ($from_date_arr[2] - 543).'-'.sprintf("%02d",$from_date_arr[1]).'-'.sprintf("%02d",$from_date_arr[0]).' 00:00:00.000';
            $thur_date_arr = explode('/',$data['thru_date']);
            $thur_date = ($thur_date_arr[2] - 543).'-'.sprintf("%02d",$thur_date_arr[1]).'-'.sprintf("%02d",$thur_date_arr[0]).' 23:59:59.000';
            $prev_date = ($from_date_arr[2] - 543).'-'.sprintf("%02d",$from_date_arr[1]).'-'.sprintf("%02d",($from_date_arr[0] -1)).' 00:00:00.000';

            $from_date_header = $this->center_function->ConvertToThaiDate($from_date,'1','0');
            $thur_date_header = $this->center_function->ConvertToThaiDate($thur_date,'1','0');
            $prev_date_header = $this->center_function->ConvertToThaiDate($prev_date,'1','0');

            $from_year_be = $from_date_arr[2];
            $from_month = $from_date_arr[1];

            if(empty($from_month)) $from_month = $account_period->accm_month_ini;
            $year_acc = $account_period->accm_month_ini <= $from_month ? $from_year_be : $from_year_be - 1;
            $where_prev_year = "year <= '".($year_acc - 1)."'";

            $from_prev_date = ($year_acc - 543)."-".sprintf("%02d",$account_period->accm_month_ini)."-01 00:00:00.000";
            $where_prev = "t1.account_datetime >= '".$from_prev_date."' AND t1.account_datetime < '".$from_date."'";

            $where_period = "t1.account_datetime BETWEEN '".$from_date."' AND '".$thur_date."'";
        } else if (!empty($data["month"]) && !empty($data["year"])) {
            $from_date = ($data["year"] - 543).'-'.sprintf("%02d",$data["month"]).'-01 00:00:00.000';
            $thur_date = date('Y-m-t',strtotime(($data["year"] - 543).'-'.sprintf("%02d",$data["month"]).'-01')).' 23:59:59.000';
            if($data["month"] == 1) {
                $prev_date  = ($data["year"] - 543 - 1).'-12-31 00:00:00.000';
            } else {
                $prev_date  = date('Y-m-t',strtotime(($data["year"] - 543).'-'.sprintf("%02d",($data["month"] - 1)).'-01')).' 23:59:59.000';
            }

            $thur_month = $data["month"];
            $prev_month = (int) $data["month"] != 1 ? $data["month"] - 1 : 12;
            $from_date_header = $this->month_arr[(int) $data["month"]];
            $thur_date_header = $this->month_arr[(int) $thur_month];
            $prev_date_header = $this->month_arr[(int) $prev_month];

            $year_acc = $account_period->accm_month_ini <= $data["month"] ? $data["year"] : $data["year"] - 1;
            $where_prev_year = "year <= '".($year_acc - 1)."'";

            $from_prev_date = ($year_acc - 543)."-".sprintf("%02d",$account_period->accm_month_ini)."-01 00:00:00.000";
            $where_prev = "t1.account_datetime >= '".$from_prev_date."' AND t1.account_datetime < '".$from_date."'";

            $where_period = "t1.account_datetime BETWEEN '".$from_date."' AND '".$thur_date."'";
        } else if (!empty($data["year"])) {
            $thru_month = $account_period->accm_month_ini == 1 ? 12 : $account_period->accm_month_ini - 1;
            $thru_year = $account_period->accm_month_ini == 1 ? $data["year"] : $data["year"] + 1;
            $from_date = ($data["year"] - 543).'-'.sprintf("%02d",$account_period->accm_month_ini).'-01 00:00:00.000';
            $thur_date = date('Y-m-t',strtotime(($thru_year - 543).'-'.sprintf("%02d",$thru_month).'-01')).' 23:59:59.000';
            $prev_date  =  date('Y-m-t',strtotime(($thru_year - 543 - 1).'-'.sprintf("%02d",$thru_month).'-01')).' 23:59:59.000';

            $where_prev_year = "year <= '".($data["year"] - 1)."'";

            $from_date_header = $this->month_arr[12];
            $thur_date_header = $this->month_arr[12]." ".$thru_year;
            $prev_date_header = $this->month_arr[12]." ".($thru_year - 1);

            $from_prev_month = $account_period->accm_month_ini;
            $from_prev_date = ($data["year"] - 543)."-".sprintf("%02d",$from_prev_month)."-01 00:00:00.000";
            $where_prev = "t1.account_datetime >= '".$from_prev_date."' AND t1.account_datetime < '".$from_date."'";

            $where_period = "t1.account_datetime BETWEEN '".$from_date."' AND '".$thur_date."'";
        }

        $result["from_date"] = $from_date;
        $result["thur_date"] = $thur_date;
        $result["prev_date"] = $prev_date;

        $result["from_date_header"] = $from_date_header;
        $result["thur_date_header"] = $thur_date_header;
        $result["prev_date_header"] = $prev_date_header;

        //Get Account Chart
        $account_charts = $this->db->select("*")->from("coop_sp_account_chart")->where("account_chart_id NOT LIKE '4%' AND account_chart_id NOT LIKE '5%' AND type IN (1,3) AND group_id = '".$group_id."'")->order_by("account_chart_id")->get()->result_array();
        $result["account_charts"] = $account_charts;

        //Get previous year balance
        $prev_year_budgets = array();
        $prev_year_budget_raw = $this->db->select("account_chart_id, budget_amount, budget_type")
                                            ->from("coop_sp_account_budget_year")
                                            ->where($where_prev_year." AND account_chart_id NOT LIKE '4%' AND account_chart_id NOT LIKE '5%' AND group_id = '".$group_id."'")
                                            ->order_by("year desc")->get()->result_array();
        foreach($prev_year_budget_raw as $budget) {
            if(!array_key_exists($budget["account_chart_id"], $prev_year_budgets)) {
                $prev_year_budgets[$budget["account_chart_id"]] = $budget["budget_amount"];
            }
        }

        $perv_account_raw = $this->db->select("t2.account_chart_id, t2.account_type, t2.account_amount, t3.entry_type")
                                        ->from("coop_sp_account as t1")
                                        ->join("coop_sp_account_detail as t2", "t1.account_id = t2.account_id", "INNER")
                                        ->join("coop_sp_account_chart as t3", "t2.account_chart_id = t3.account_chart_id AND t1.group_id = t3.group_id", "LEFT")
                                        ->where($where_prev." AND t1.account_status = 0 AND t1.group_id = '".$group_id."'")
                                        ->get()->result_array();

        foreach($perv_account_raw as $account) {
            $nature = $account["entry_type"] == 1 ? "debit" : "credit";
            if($nature == $account["account_type"]) {
                $prev_year_budgets[$account["account_chart_id"]] += $account["account_amount"];
            } else {
                $prev_year_budgets[$account["account_chart_id"]] -= $account["account_amount"];
            }
        }

        //Get period account
        $year_budgets = $prev_year_budgets;
        $account_raw = $this->db->select("t2.account_chart_id, t2.account_type, t2.account_amount, t3.entry_type")
                                ->from("coop_sp_account as t1")
                                ->join("coop_sp_account_detail as t2", "t1.account_id = t2.account_id", "INNER")
                                ->join("coop_sp_account_chart as t3", "t2.account_chart_id = t3.account_chart_id AND t1.group_id = t3.group_id", "LEFT")
                                ->where($where_period." AND (t1.account_status = 0 OR t1.account_status IS NULL) AND t1.group_id = '".$group_id."'")
                                ->get()->result_array();
        foreach($account_raw as $account) {
            $nature = $account["entry_type"] == 1 ? "debit" : "credit";
            if($nature == $account["account_type"]) {
                $year_budgets[$account["account_chart_id"]] += $account["account_amount"];
            } else {
                $year_budgets[$account["account_chart_id"]] -= $account["account_amount"];
            }
        }

        //Special chart id
        //30001 profit - loss
        //Prev
        $profit_loss = $this->db->select("t2.account_type, SUM(t2.account_amount) as amount")
                                    ->from("coop_sp_account as t1")
                                    ->join("coop_sp_account_detail as t2", "t1.account_id = t2.account_id AND (t2.account_chart_id like '4%' OR t2.account_chart_id like '5%')")
                                    ->where($where_prev." AND t1.account_status = 0 AND t1.group_id = '".$group_id."'")
                                    ->group_by("t2.account_type")
                                    ->get()->result_array();
        $profit_total = 0;
        foreach($profit_loss as $detail) {
            if($detail["account_type"] == 'credit') {
                $profit_total += $detail["amount"];
            } else if ($detail["account_type"] == 'debit') {
                $profit_total -= $detail["amount"];
            }
        }
        $prev_year_budgets[30001] = $profit_total;

        //Current
        $profit_total = 0;
        $profit_loss = $this->db->select("t2.account_type, SUM(t2.account_amount) as amount")
                                ->from("coop_sp_account as t1")
                                ->join("coop_sp_account_detail as t2", "t1.account_id = t2.account_id AND (t2.account_chart_id like '4%' OR t2.account_chart_id like '5%')")
                                ->where($where_period." AND t1.account_status = 0 AND t1.group_id = '".$group_id."'")
                                ->group_by("t2.account_type")
                                ->get()->result_array();
        $profit_total = 0;
        foreach($profit_loss as $detail) {
            if($detail["account_type"] == 'credit') {
                $profit_total += $detail["amount"];
            } else if ($detail["account_type"] == 'debit') {
                $profit_total -= $detail["amount"];
            }
        }
        $year_budgets[30001] = $prev_year_budgets[30001] + $profit_total;

        $result["year_budgets"] = $year_budgets;
        $result["prev_year_budgets"] = $prev_year_budgets;
        
        return $result;
    }

    public function check_account_profit_lost_statement($group_id, $conditions) {
        if(@$conditions['report_date'] != ''){
            $date_arr = explode('/',@$conditions['report_date']);
            $day = (int)$date_arr[0];
            $month = (int)$date_arr[1];
            $year = (int)$date_arr[2];
            $year -= 543;
            $s_date = $year.'-'.sprintf("%02d",$month).'-'.sprintf("%02d",$day).' 00:00:00.000';
            $e_date = $year.'-'.sprintf("%02d",$month).'-'.sprintf("%02d",$day).' 23:59:59.000';
            $where = " AND t1.account_datetime BETWEEN '".$s_date."' AND '".$e_date."'";
        }else{
            if(@$conditions['month']!='' && @$conditions['year']!=''){
                $day = '';
                $month = $conditions['month'];
                $year = ($conditions['year']-543);
                $s_date = $year.'-'.sprintf("%02d",@$month).'-01'.' 00:00:00.000';
                $e_date = date('Y-m-t',strtotime($s_date)).' 23:59:59.000';
                $where = " AND t1.account_datetime BETWEEN '".$s_date."' AND '".$e_date."'";
            }else{
                $day = '';
                $month = '';
                $year = ($conditions['year']-543);
                $where = " AND t1.account_datetime BETWEEN '".$year."-01-01 00:00:00.000' AND '".$year."-12-31 23:59:59.000' ";
            }
        }

        $this->db->select(array('t1.account_id',
            't1.account_datetime',
            't2.account_type'
        ));
        $this->db->from('coop_sp_account as t1');
        $this->db->join("coop_sp_account_detail as t2", "t1.account_id = t2.account_id", "inner");
        $this->db->where("(t1.account_status <> '2' OR t1.account_status IS NULL) {$where} AND t1.group_id = '".$group_id."'");

        $rs = $this->db->get()->result_array();
        $row = @$rs[0];
        if(@$row['account_id'] != ''){
            return "success";
        } else {
            return "no_data";
        }
    }
    
    public function get_account_profit_lost_data($group_id, $data) {
        $result = array();
        $month_arr = $this->month_arr;
        $month_short_arr = $this->month_short_arr;

        $result['month_arr'] = $month_arr;
        $result['month_short_arr'] = $month_short_arr;

        $account_period = $this->db->select("accm_month_ini")->from("coop_account_period_setting")->order_by("accm_date_modified desc")->get()->row();

        $where_prev = "1=1"; // condition for prev account data for current year
        $where_period = "1=1"; // condition for account data for current period
        if(!empty($data["from_date"]) && !empty($data["thru_date"])) {
            $from_date_arr = explode('/',$data['from_date']);
            $from_date = ($from_date_arr[2] - 543).'-'.sprintf("%02d",$from_date_arr[1]).'-'.sprintf("%02d",$from_date_arr[0]).' 00:00:00.000';
            $thur_date_arr = explode('/',$data['thru_date']);
            $thur_date = ($thur_date_arr[2] - 543).'-'.sprintf("%02d",$thur_date_arr[1]).'-'.sprintf("%02d",$thur_date_arr[0]).' 23:59:59.000';
            $prev_date = ($from_date_arr[2] - 543).'-'.sprintf("%02d",$from_date_arr[1]).'-'.sprintf("%02d",($from_date_arr[0] -1)).' 00:00:00.000';

            $from_date_header = $this->center_function->ConvertToThaiDate($from_date,'1','0');
            $thur_date_header = $this->center_function->ConvertToThaiDate($thur_date,'1','0');
            $prev_date_header = $this->center_function->ConvertToThaiDate($prev_date,'1','0');

            $from_year_be = $from_date_arr[2];
            $from_month = $from_date_arr[1];

            if(empty($from_month)) $from_month = $account_period->accm_month_ini;
            $year_acc = $account_period->accm_month_ini <= $from_month ? $from_year_be : $from_year_be - 1;

            $from_prev_date = ($year_acc - 543)."-".sprintf("%02d",$account_period->accm_month_ini)."-01 00:00:00.000";
            $where_prev = "t1.account_datetime >= '".$from_prev_date."' AND t1.account_datetime < '".$from_date."'";

            $thur_year_be = $thur_date_arr[2];
            $thur_month = $thur_date_arr[1];

            if(empty($thur_month)) $thur_month = $account_period->accm_month_ini;
            $thur_year_acc = $account_period->accm_month_ini <= $thur_month ? $thur_year_be : $thur_year_be - 1;
            $from_date_str = ($thur_year_acc - 543)."-".sprintf("%02d",$account_period->accm_month_ini)."-01 00:00:00.000";
            $where_period = "t1.account_datetime BETWEEN '".$from_date_str."' AND '".$thur_date."'";
        } else if (!empty($data["month"]) && !empty($data["year"])) {
            $from_date = ($data["year"] - 543).'-'.sprintf("%02d",$data["month"]).'-01 00:00:00.000';
            $thur_date = date('Y-m-t',strtotime(($data["year"] - 543).'-'.sprintf("%02d",$data["month"]).'-01')).' 23:59:59.000';
            if($data["month"] == 1) {
                $prev_date  = ($data["year"] - 543 - 1).'-12-31 00:00:00.000';
                $year_acc = $account_period->accm_month_ini <= $data["month"] ? $data["year"] - 1 : $data["year"] - 2;
                $thur_year_acc = $account_period->accm_month_ini <= $data["month"] ? $data["year"] : $data["year"] - 1;
            } else {
                $prev_date  = date('Y-m-t',strtotime(($data["year"] - 543).'-'.sprintf("%02d",($data["month"] - 1)).'-01')).' 23:59:59.000';
                $year_acc = $account_period->accm_month_ini <= $data["month"] ? $data["year"] : $data["year"] - 1;
                $thur_year_acc = $account_period->accm_month_ini <= $data["month"] ? $data["year"] : $data["year"] - 1;
            }

            $thur_month = $data["month"];
            $prev_month = (int) $data["month"] != 1 ? $data["month"] - 1 : 12;
            $from_date_header = $this->month_arr[(int) $data["month"]];
            $thur_date_header = $this->month_arr[(int) $thur_month];
            $prev_date_header = $this->month_arr[(int) $prev_month];

            $from_prev_date = ($year_acc - 543)."-".sprintf("%02d",$account_period->accm_month_ini)."-01 00:00:00.000";
            $where_prev = "t1.account_datetime >= '".$from_prev_date."' AND t1.account_datetime < '".$from_date."'";
            $from_date_str = ($thur_year_acc - 543)."-".sprintf("%02d",$account_period->accm_month_ini)."-01 00:00:00.000";
            $where_period = "t1.account_datetime BETWEEN '".$from_date_str."' AND '".$thur_date."'";
        } else if (!empty($data["year"])) {
            $thru_month = $account_period->accm_month_ini == 1 ? 12 : $account_period->accm_month_ini - 1;
            $thru_year = $account_period->accm_month_ini == 1 ? $data["year"] : $data["year"] + 1;
            $from_date = ($data["year"] - 543).'-'.sprintf("%02d",$account_period->accm_month_ini).'-01 00:00:00.000';
            $thur_date = date('Y-m-t',strtotime(($thru_year - 543).'-'.sprintf("%02d",$thru_month).'-01')).' 23:59:59.000';
            $prev_date  =  date('Y-m-t',strtotime(($thru_year - 543 - 1).'-'.sprintf("%02d",$thru_month).'-01')).' 23:59:59.000';

            $from_date_header = $this->month_arr[12];
            $thur_date_header = $this->month_arr[12]." ".$thru_year;
            $prev_date_header = $this->month_arr[12]." ".($thru_year - 1);

            $from_prev_month = $account_period->accm_month_ini;
            $from_prev_date = ($data["year"] - 543 - 1)."-".sprintf("%02d",$from_prev_month)."-01 00:00:00.000";
            $where_prev = "t1.account_datetime >= '".$from_prev_date."' AND t1.account_datetime < '".$from_date."'";

            $from_date_str = ($data["year"] - 543)."-".sprintf("%02d",$from_prev_month)."-01 00:00:00.000";
            $where_period = "t1.account_datetime BETWEEN '".$from_date."' AND '".$thur_date."'";
        }

        $result["from_date"] = $from_date;
        $result["thur_date"] = $thur_date;
        $result["prev_date"] = $prev_date;
        $result["from_date_header"] = $from_date_header;
        $result["thur_date_header"] = $thur_date_header;
        $result["prev_date_header"] = $prev_date_header;

        //Get Account Chart
        $account_charts = $this->db->select("*")->from("coop_sp_account_chart")->where("account_chart_id NOT LIKE '1%' AND account_chart_id NOT LIKE '2%' AND account_chart_id NOT LIKE '3%' AND type IN (1,3) AND group_id = '".$group_id."'")->order_by("account_chart_id")->get()->result_array();
        $result["account_charts"] = $account_charts;

        //Get previous year balance
        $prev_year_budgets = array();
        $perv_account_raw = $this->db->select("t2.account_chart_id, t2.account_type, t2.account_amount, t3.entry_type")
                                        ->from("coop_sp_account as t1")
                                        ->join("coop_sp_account_detail as t2", "t1.account_id = t2.account_id", "INNER")
                                        ->join("coop_sp_account_chart as t3", "t2.account_chart_id = t3.account_chart_id AND t1.group_id = t3.group_id", "LEFT")
                                        ->where($where_prev." AND t1.account_status = 0 AND t1.group_id = '".$group_id."'")
                                        ->get()->result_array();
        foreach($perv_account_raw as $account) {
            $nature = $account["entry_type"] == 1 ? "debit" : "credit";
            if($nature == $account["account_type"]) {
                $prev_year_budgets[$account["account_chart_id"]] += $account["account_amount"];
            } else {
                $prev_year_budgets[$account["account_chart_id"]] -= $account["account_amount"];
            }
        }

        $result["prev_year_budgets"] = $prev_year_budgets;

        //Get period account
        $year_budgets = array();
        $account_raw = $this->db->select("t2.account_chart_id, t2.account_type, t2.account_amount, t3.entry_type")
                                ->from("coop_sp_account as t1")
                                ->join("coop_sp_account_detail as t2", "t1.account_id = t2.account_id", "INNER")
                                ->join("coop_sp_account_chart as t3", "t2.account_chart_id = t3.account_chart_id AND t1.group_id = t3.group_id", "LEFT")
                                ->where($where_period." AND t1.account_status = 0 AND t1.group_id = t3.group_id")
                                ->get()->result_array();
        foreach($account_raw as $account) {
            $nature = $account["entry_type"] == 1 ? "debit" : "credit";
            if($nature == $account["account_type"]) {
                $year_budgets[$account["account_chart_id"]] += $account["account_amount"];
            } else {
                $year_budgets[$account["account_chart_id"]] -= $account["account_amount"];
            }
        }
        $result["year_budgets"] = $year_budgets;

        return $result;
    }

    public function get_account_transaction_vouchers($group_id, $conditions) {
        $where_approve_date = '';
        $where_journal_type = '';
        if($conditions['approve_date']!=''){
            $approve_date_arr = explode('/',$conditions['approve_date']);
            $approve_day = stripslashes($approve_date_arr[0]);
            $approve_month = stripslashes($approve_date_arr[1]);
            $approve_year = stripslashes($approve_date_arr[2]);
            $conditions['approve_date'] = $approve_day."/".$approve_month."/".$approve_year;
            $approve_year -= 543;
            $approve_date = $approve_year.'-'.$approve_month.'-'.$approve_day;
            $where_approve_date = "AND t0.account_datetime like '{$approve_date}%' ";
        }else{
            $approve_date_arr = explode(' ',date('Y-m-d H:i:s'));
            $approve_date_arr = explode('-',$approve_date_arr[0]);
            $approve_day = stripslashes($approve_date_arr[2]);
            $approve_month = stripslashes($approve_date_arr[1]);
            $approve_year = stripslashes($approve_date_arr[0]);
            $conditions['approve_date'] = $approve_day."/".$approve_month."/".($approve_year + 543);
            $approve_date = $approve_year.'-'.$approve_month.'-'.$approve_day;
            $where_approve_date = "AND t0.account_datetime like '{$approve_date}%' ";
        }

        if(!empty($conditions["journal_type"])) {
            $where_journal_type = " AND t0.journal_type = '".$conditions["journal_type"]."'";
        }

        $result = array();

        //Get account for cash
        $cash_account = $this->db->select("*")->from("coop_account_setting")->where("type = 'cash_chart_id'")->get()->row();
        $result["cash_id"] = $cash_account->value;

        //Get type of account
        $account_types = $this->db->select("*")->from("coop_account_setting")->where("type = 'account_type'")->get()->row();
        $result["account_types"] = json_decode($account_types->value);

        $row = array();
        $this->db->select(array('t0.*','t1.*','t2.account_chart'));
        $this->db->from('coop_sp_account as t0 ');
        $this->db->join('coop_sp_account_detail as t1','t0.account_id = t1.account_id','inner');
        $this->db->join('coop_sp_account_chart as t2','t1.account_chart_id = t2.account_chart_id AND t0.group_id = t2.group_id','inner');
        $this->db->where("t0.status_audit is null AND (t0.account_status is null OR t0.account_status = 0) {$where_approve_date} {$where_journal_type} AND t0.group_id = '".$group_id."'");
        $this->db->order_by("account_type DESC ,account_detail_id DESC");
        $row_detail = $this->db->get()->result_array();
        $row['data']['account_detail'] = $row_detail;
        $data_account_detail = array();

        foreach($row['data'] as $key => $row_all) {
            $account_datetime ='';
            $account_datetime =  explode(" ",$row_all['account_datetime']);
            foreach($row_all as $key2 => $row_detail_all){
                $account_datetime ='';
                $account_datetime =  explode(" ",$row_detail_all['account_datetime']);
                $data_account_detail[$account_datetime[0]][$row_detail_all['account_id']][$row_detail_all['account_type'].$row_detail_all['account_detail_id']]['account_chart_id'] = $row_detail_all['account_chart_id'];
                $data_account_detail[$account_datetime[0]][$row_detail_all['account_id']][$row_detail_all['account_type'].$row_detail_all['account_detail_id']]['account_chart'] = $row_detail_all['account_chart'];
                $data_account_detail[$account_datetime[0]][$row_detail_all['account_id']][$row_detail_all['account_type'].$row_detail_all['account_detail_id']]['account_type'] = $row_detail_all['account_type'];
                $data_account_detail[$account_datetime[0]][$row_detail_all['account_id']][$row_detail_all['account_type'].$row_detail_all['account_detail_id']]['account_amount'] += $row_detail_all['account_amount'];
                $data_account_detail[$account_datetime[0]][$row_detail_all['account_id']][$row_detail_all['account_type'].$row_detail_all['account_detail_id']]['account_detail_id'] = $row_detail_all['account_detail_id'];
                $data_account_detail[$account_datetime[0]][$row_detail_all['account_id']][$row_detail_all['account_type'].$row_detail_all['account_detail_id']]['account_description'] = $row_detail_all['account_description'];
                $data_account_detail[$account_datetime[0]][$row_detail_all['account_id']][$row_detail_all['account_type'].$row_detail_all['account_detail_id']]['journal_ref'] = $row_detail_all['journal_ref'];
                $data_account_detail[$account_datetime[0]][$row_detail_all['account_id']][$row_detail_all['account_type'].$row_detail_all['account_detail_id']]['seq_no'] = $row_detail_all['seq_no'];
            }
        }
        $sort_array = array_map(function($array_account_datetime){
            return array_map(function($var){
                rsort($var);
                return $var;
            },$array_account_datetime);

        },$data_account_detail);

        //ตั้งค่าว่ารายการในแต่ละหน้าจะมีกี่รายการ ตั้งแต่รายการที่เท่าไรถึงเท่าไร
        if(empty($_GET["page"])){
            $firest_p = 1;
        }else{
            $firest_p =$_GET["page"];
        }
        $max_list = (10 *(@$firest_p));
        $min_list = (10 *(@$firest_p-1));
        $result['max_list']  = $max_list;
        $result['min_list']  = $min_list;
        //ตั้งค่าว่ารายการในแต่ละหน้าจะมีกี่รายการ ตั้งแต่รายการที่เท่าไรถึงเท่าไร

        $result['approve_date'] = $row['approve_date'];
        $result['data'] = $row['data'];
        $result['data_account_detail'] = $data_account_detail;
        $result['space'] = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";

        return $result;
    }

    public function get_account_transaction_voucher($group_id, $conditions) {
        $detail = $conditions['detail'];
        $date = $conditions['date'];
        $account_detail_ids = $conditions['account_detail_ids'];
        $result = array();

        if(@$date != ''){
            $date_arr = explode('-',@$date);
            $day = (int) $date_arr[2];
            $month = (int) $date_arr[1];
            $year = (int) $date_arr[0];
            $s_date = $year.'-'.sprintf("%02d", $month).'-'.sprintf("%02d", $day).' 00:00:00.000';
            $e_date = $year.'-'.sprintf("%02d", $month).'-'.sprintf("%02d", $day).' 23:59:59.000';
        }
        $result['day'] = $day;
        $result['month'] = $month;
        $result['year'] = $year;

        if(!empty($_POST['account_detail_ids'])) {
            $account_details = $this->db->select("t1.account_id, t1.journal_ref, t1.journal_type, t1.account_datetime,
                                                    t2.account_detail_id, t2.account_amount, t2.account_chart_id, t2.description, t2.account_type, t2.seq_no,
                                                    t3.user_name, t4.account_chart")
                                        ->from("coop_sp_account_detail as t2")
                                        ->join("coop_sp_account as t1", "t1.account_id = t2.account_id", "left")
                                        ->join("coop_user as t3", "t1.user_id = t3.user_id", "left")
                                        ->join("coop_sp_account_chart as t4", "t2.account_chart_id = t4.account_chart_id AND t1.group_id = t4.group_id", "left")
                                        ->where("t2.account_detail_id in (".implode(',',$account_detail_ids).")")
                                        ->get()->result_array();
            $date_for_page = substr($account_details[0]['account_datetime'],0,10);
            $page_data = $this->get_voucher_page_by_date($group_id, $date_for_page);

        } else {
            $account_details = array();
            $page_data = $this->get_voucher_page_by_date($group_id, "0000-00-00");
        }

        $result['per_page'] = $this->account->get_setting($group_id, "voucher_per_page");
        $result['page_count'] = $page_data['page_count'];
        $result['page'] = $page_data['page'];
        $result['datas'] = $account_details;

        return $result;
    }

    /*
        date format : yyyy-mm-dd
    */
    public function get_voucher_page_by_date($group_id, $date) {
        $result = array();
        $voucher_per_page = $this->account->get_setting($group_id, "voucher_per_page");
        $cash_chart_id = $this->account->get_setting($group_id, "cash_chart_id");
        $account_datas = $this->db->select("t2.account_detail_id")
                                    ->from("coop_sp_account as t1")
                                    ->join("coop_sp_account_detail as t2", "t1.account_id = t2.account_id", "INNER")
                                    ->where("t1.group_id = '".$group_id."' AND t1.account_datetime = '".$date."' AND (t1.account_status = 0 OR t1.account_status IS NULL) AND (t2.account_chart_id != '".$cash_chart_id."' OR t1.journal_type = 'S')")
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

    public function check_cash_journal($group_id, $conditions) {
        $from_date_arr = explode('/',$conditions['from_date']);
        $from_date = ($from_date_arr[2] - 543).'-'.sprintf("%02d",$from_date_arr[1]).'-'.sprintf("%02d",$from_date_arr[0]).' 00:00:00.000';
        $thur_date_arr = explode('/',$conditions['from_date']);
        $thur_date = ($thur_date_arr[2] - 543).'-'.sprintf("%02d",$thur_date_arr[1]).'-'.sprintf("%02d",$thur_date_arr[0]).' 23:59:59.000';

        $account = $this->db->select("account_id")->from("coop_sp_account")
                                ->where("(account_status != 2 OR account_status IS NULL) AND (journal_type = 'P' OR journal_type = 'R') AND account_datetime BETWEEN '".$from_date."' AND '".$thur_date."' AND group_id = '".$group_id."'")
                                ->get()->row();
        if(!empty($account)) {
            return "success";
        }else {
            return "no_data";
        }
    }

    public function get_cash_journal($group_id, $data) {
        $from_date_arr = explode('/',$data['from_date']);
        $from_date = ($from_date_arr[2] - 543).'-'.sprintf("%02d",$from_date_arr[1]).'-'.sprintf("%02d",$from_date_arr[0]).' 00:00:00.000';
        $thur_date_arr = explode('/',$data['from_date']);
        $thur_date = ($thur_date_arr[2] - 543).'-'.sprintf("%02d",$thur_date_arr[1]).'-'.sprintf("%02d",$thur_date_arr[0]).' 23:59:59.000';
        $where = " AND t1.account_datetime BETWEEN '".$from_date."' AND '".$thur_date."'";
        $where_prev = " AND t1.account_datetime < '".$from_date."'";
        $year_be = $from_date_arr[2];
        $year = $from_date_arr[2] - 543;

        //GET cash chart_id
        $cash_chart_id = $this->db->select("*")->from("coop_sp_account_setting")->where("type = 'cash_chart_id' AND group_id = '".$group_id."'")->get()->row()->value;

        //Get Account
        $result = array();
        $accounts = $this->db->select("
                                        t1.account_id,
                                        t1.journal_ref,
                                        t1.journal_type,
                                        t2.account_chart_id,
                                        t2.account_type,
                                        t2.account_amount as amount,
                                        t3.account_chart,
                                        t3.entry_type
                                        ")
                                ->from("coop_sp_account as t1")
                                ->join("coop_sp_account_detail as t2", "t1.account_id = t2.account_id", "inner")
                                ->join("coop_sp_account_chart as t3", "t2.account_chart_id = t3.account_chart_id AND t2.account_chart_id != '{$cash_chart_id}' AND t1.group_id = t3.group_id", "inner")
                                ->where("(t1.account_status != 2 OR t1.account_status IS NULL) AND (t1.journal_type = 'R' OR t1.journal_type = 'P') AND t1.group_id = '".$group_id."' ".$where)
                                ->order_by("t1.journal_type DESC, t2.account_chart_id")
                                ->get()->result_array();
        $result["accounts"] = $accounts;

        $cashs = $this->db->select("t2.account_type, SUM(t2.account_amount) as amount")
                                ->from("coop_sp_account as t1")
                                ->join("coop_sp_account_detail as t2", "t1.account_id = t2.account_id", "inner")
                                ->join("coop_sp_account_chart as t3", "t2.account_chart_id = t3.account_chart_id AND t2.account_chart_id = '{$cash_chart_id}' AND t1.group_id = t3.group_id", "inner")
                                ->where("(t1.account_status != 2 OR t1.account_status IS NULL) AND (t1.journal_type = 'R' OR t1.journal_type = 'P') AND t1.group_id = '".$group_id."' ".$where)
                                ->group_by("t2.account_type")
                                ->get()->result_array();
        $cash_debit = 0;
        $cash_credit = 0;
        foreach($cashs as $cash) {
            if($cash["account_type"] == "debit") $cash_debit += $cash["amount"];
            if($cash["account_type"] == "credit") $cash_credit += $cash["amount"];
        }
        $result["cash_debit"] = $cash_debit;
        $result["cash_credit"] = $cash_credit;

        //Get Cash balance
        $cash_budget_year = $this->db->select("budget_amount")->from("coop_sp_account_budget_year")->where("account_chart_id = '{$cash_chart_id}' AND year = '".($year_be - 1)."' AND group_id = '".$group_id."'")->order_by("year DESC")->get()->row();
        $balance = $cash_budget_year->budget_amount;
        $result["cash_balance"] = $balance;
        $cash_transactions = $this->db->select("t2.account_type, SUM(t2.account_amount) as amount")
                                        ->from("coop_sp_account as t1")
                                        ->join("coop_sp_account_detail as t2", "t1.account_id = t2.account_id AND t2.account_chart_id = '{$cash_chart_id}'", "inner")
                                        ->where("1=1 ".$where_prev." AND YEAR(t1.account_datetime) = '{$year}' AND (t1.account_status != 2 OR t1.account_status IS NULL) AND t1.group_id = '".$group_id."'")
                                        ->group_by("t2.account_type")
                                        ->get()->result_array();
        $diff_cash = 0;
        foreach($cash_transactions as $tran) {
            if($tran["account_type"] == "debit") $diff_cash += $tran["amount"];
            if($tran["account_type"] == "credit") $diff_cash -= $tran["amount"];
        }

        $result["diff_cash"] = $diff_cash;

        return $result;
    }

    public function check_general_journal($group_id, $conditions) {
        $from_date_arr = explode('/',$conditions['from_date']);
        $from_date = ($from_date_arr[2] - 543).'-'.sprintf("%02d",$from_date_arr[1]).'-'.sprintf("%02d",$from_date_arr[0]).' 00:00:00.000';
        $thur_date_arr = explode('/',$conditions['thur_date']);
        $thur_date = ($thur_date_arr[2] - 543).'-'.sprintf("%02d",$thur_date_arr[1]).'-'.sprintf("%02d",$thur_date_arr[0]).' 23:59:59.000';

        $account = $this->db->select("account_id")->from("coop_sp_account")
                                ->where("(account_status != 2 OR account_status IS NULL) AND journal_type = 'J' AND account_datetime BETWEEN '".$from_date."' AND '".$thur_date."' AND group_id = '".$group_id."'")
                                ->get()->row();
        if(!empty($account)) {
            return "success";
        } else {
            return "no_data";
        }
    }

    public function get_general_journal($group_id, $data) {
        $from_date_arr = explode('/',$data['from_date']);
        $from_date = ($from_date_arr[2] - 543).'-'.sprintf("%02d",$from_date_arr[1]).'-'.sprintf("%02d",$from_date_arr[0]).' 00:00:00.000';
        $thur_date_arr = explode('/',$data['thur_date']);
        $thur_date = ($thur_date_arr[2] - 543).'-'.sprintf("%02d",$thur_date_arr[1]).'-'.sprintf("%02d",$thur_date_arr[0]).' 23:59:59.000';
        $where = " AND t1.account_datetime BETWEEN '".$from_date."' AND '".$thur_date."'";

        //Get Account
        $accounts = $this->db->select("t1.journal_ref,
                                        t2.account_chart_id,
                                        t2.account_type,
                                        SUM(t2.account_amount) as amount,
                                        t3.account_chart,
                                        t3.entry_type
                                        ")
                                ->from("coop_sp_account as t1")
                                ->join("coop_sp_account_detail as t2", "t1.account_id = t2.account_id", "inner")
                                ->join("coop_sp_account_chart as t3", "t2.account_chart_id = t3.account_chart_id AND t1.group_id = t3.group_id", "inner")
                                ->where("(t1.account_status != 2 OR t1.account_status IS NULL) AND t1.journal_type = 'J' AND t1.group_id = '".$group_id."'".$where)
                                ->group_by("t1.account_id, t2.account_chart_id, t2.account_type")
                                ->get()->result_array();
        $result = array();
        foreach($accounts as $account) {
            $result[$account["account_chart_id"]]["account_chart_id"] = $account["account_chart_id"];
            $result[$account["account_chart_id"]]["account_chart"] = $account["account_chart"];
            $result[$account["account_chart_id"]][$account["account_type"]] += $account["amount"];
            $result[$account["account_chart_id"]]["journal_ref"] = $account["journal_ref"];
        }

        return $result;
    }
}
