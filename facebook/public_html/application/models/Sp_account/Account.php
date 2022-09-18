<?php if( ! defined('BASEPATH')) exit('No direct script access allowed');

class Account extends CI_Model {
    public function __construct() {
        parent::__construct();
    }

    public function get_setting($group_id, $key) {
        $setting = $this->db->select("value")->from("coop_sp_account_setting")->where("type = '".$key."' AND group_id = '".$group_id."'")->get()->row();
        if(!empty($setting)) {
            return $setting->value;
        }
        return null;
    }

    //status 1=approve, 2=cancel
    public function get_charts($group_id, $parent_id, $type, $status) {
        $result = array();
        $where = "group_id = '".$group_id."'";
        if(!empty($parent_id)) {
            $where .= " AND parent_id = '".$parent_id."'";
        }
        if(!empty($type)) {
            $where .= " AND type = '".$type."'";
        }
        if(!empty($status)) {
            if($status == 1) {
                $where .= " AND cancel_status IS NULL";
            } else if ($status == 2) {
                $where .= " AND cancel_status = 1";
            }
        }

        $charts = $this->db->select("*")->from("coop_sp_account_chart")->where($where)->get()->result_array();
        if(!empty($charts)) {
            $result["charts"] = $charts;
            $result["status"] = "success";
        } else {
            $result["status"] = "non_data";
        }
       return $result;
    }

    public function get_account_transactions($group_id, $conditions) {
        $result = array();
        $where = "";

        if(!empty($conditions)) {
            if(!empty($conditions["account_datetime"])) {
                $where .= " AND t1.account_datetime = '".$conditions["account_datetime"]."'";
            }
        }
        
        $data_account_detail = array();
        $accounts = $this->db->select("t1.account_id, t1.account_datetime, t1.account_description, t1.run_status, t1.journal_type, t1.journal_ref,
                                        t2.account_detail_id, t2.account_chart_id, t2.account_type, t2.account_amount,
                                        t3.account_chart")
                                ->from("coop_sp_account as t1")
                                ->join("coop_sp_account_detail as t2", "t1.account_id = t2.account_id", "left")
                                ->join("coop_sp_account_chart as t3", "t2.account_chart_id = t3.account_chart_id AND t1.group_id = t3.group_id", "left")
                                ->where("(t1.account_status != 2 OR t1.account_status is null) AND t1.group_id = '".$group_id."'".$where)
                                ->order_by("t2.account_type desc")
                                ->get()->result_array();

        foreach($accounts as $key => $row_all) {
            $data_account_detail[$row_all['account_datetime']][$row_all['account_id']][$row_all['account_detail_id']]['account_chart_id'] = $row_all['account_chart_id'];
            $data_account_detail[$row_all['account_datetime']][$row_all['account_id']][$row_all['account_detail_id']]['account_chart'] = $row_all['account_chart'];
            $data_account_detail[$row_all['account_datetime']][$row_all['account_id']][$row_all['account_detail_id']]['account_type'] = $row_all['account_type'];
            $data_account_detail[$row_all['account_datetime']][$row_all['account_id']][$row_all['account_detail_id']]['account_amount'] += $row_all['account_amount'];
            $data_account_detail[$row_all['account_datetime']][$row_all['account_id']][$row_all['account_detail_id']]['account_datetime'] = $row_all['account_datetime'];
            $data_account_detail[$row_all['account_datetime']][$row_all['account_id']][$row_all['account_detail_id']]['account_id'] = $row_all['account_id'];
            $data_account_detail[$row_all['account_datetime']][$row_all['account_id']][$row_all['account_detail_id']]['account_description'] = $row_all['account_description'];
            $data_account_detail[$row_all['account_datetime']][$row_all['account_id']][$row_all['account_detail_id']]['run_status'] = $row_all['run_status'];
            $data_account_detail[$row_all['account_datetime']][$row_all['account_id']][$row_all['account_detail_id']]['journal_type'] = $row_all['journal_type'];
            $data_account_detail[$row_all['account_datetime']][$row_all['account_id']][$row_all['account_detail_id']]['journal_ref'] = $row_all['journal_ref'];
        }

        $result["datas"] = $data_account_detail;
        $result["status"] = "success";

        return $result;
    }

    public function get_account_detail_by_id($account_id) {
        $result = array();
        $account = $this->db->select("account_id,
                                        group_id,
                                        account_description,
                                        account_datetime,
                                        journal_ref,
                                        journal_type")
                            ->from("coop_sp_account")
                            ->where("account_id = '{$account_id}'")
                            ->get()->row();
        $result["account_id"] = $account_id;
        $result["account_description"] = $account->account_description;
        $result["account_datetime"] = $account->account_datetime;
        $result["account_datetime_be"] = date("d", strtotime($account->account_datetime))."/".sprintf('%02d',date("m", strtotime($account->account_datetime)))."/".(date("Y", strtotime($account->account_datetime))+543);
        $result["journal_ref"] = $account->journal_ref;
        $result["journal_type"] = $account->journal_type;

        $details = $this->db->select("t1.account_id,
                                        t1.account_detail_id,
                                        t1.account_type,
                                        t1.account_amount,
                                        t1.account_chart_id,
                                        t1.description,
                                        t1.seq_no,
                                        t2.account_chart")
                            ->from("coop_sp_account_detail as t1")
                            ->join("coop_sp_account_chart as t2", "t1.account_chart_id = t2.account_chart_id AND t2.group_id = '".$account->group_id."'", "left")
                            ->where("t1.account_id = '{$account_id}'")
                            ->get()->result_array();
        $result["details"] = $details;
        return $result;
    }

    public function save($group_id, $param) {
        $result = array();
        $data = $param['data'];
        $account_datetime_arr = explode('/',$data['coop_account']['account_datetime']);
        $year = $account_datetime_arr[2];
        $month = $account_datetime_arr[1];

        $month_acc = $month;
        $account_period = $this->db->select("accm_month_ini")->from("coop_account_period_setting")->order_by("accm_date_modified desc")->get()->row();
        if(empty($month_acc)) $month_acc = $account_period->accm_month_ini;
        $year_acc = $account_period->accm_month_ini <= $month ? $year + 1 : $year;

        $data['coop_account']['account_datetime'] = ($account_datetime_arr[2]-543)."-".sprintf('%02d',$account_datetime_arr[1])."-".sprintf('%02d',$account_datetime_arr[0]);

        $year_ref = $year_acc - 2500;
        $last_journal_ref_account = $this->db->select("journal_ref, RIGHT(journal_ref, 6) as count_journal_ref")
                                                ->from("coop_sp_account")->where("journal_ref LIKE '__".$year_ref."%' AND group_id = '".$group_id."'")->order_by("RIGHT(journal_ref, 6) desc")->get()->row();
        $year_lead = $year -2500;
        $journal_ref = '';

        if($param["journal_type"] == "R" || $param["journal_type"] == "P") {
            $journal_no = $this->generate_date_no($group_id, $data['coop_account']['account_datetime']);
        } else {
            $journal_no = $this->get_voucher_number($group_id, $data['coop_account']['account_datetime'], $_POST["journal_type"]);
        }
        $journal_ref = $param["journal_type"].$journal_no;

        $data_insert = array();
        $data_insert['account_description'] = $data['coop_account']['account_description'];
        $data_insert['account_datetime'] = $data['coop_account']['account_datetime'];
        $data_insert['process'] = 'add_manual';
        $data_insert['journal_type'] = $param["journal_type"];
        $data_insert["journal_ref"] = $journal_ref;
        $data_insert["group_id"] = $group_id;
        $data_insert["run_status"] = 0;
        $data_insert['user_id'] = $_SESSION['USER_ID'];

        $account_id = $param["account_id"];
        if(!empty($param["account_id"])) {
            $data_insert = array();
            $data_insert['account_description'] = $data['coop_account']['account_description'];
            $data_insert['account_datetime'] = $data['coop_account']['account_datetime'];
            $data_insert['journal_type'] = $param["journal_type"];
            $data_insert['user_id'] = $_SESSION['USER_ID'];
            $this->db->where('account_id', $param["account_id"]);
            $this->db->update('coop_sp_account', $data_insert);

            //Calculate year budget(Remove previous data)
            $account_details = $this->db->select("YEAR(t1.account_datetime) AS year, MONTH(t1.account_datetime) AS month, t2.account_chart_id, t2.account_amount, t2.account_type, t3.entry_type")
                                        ->from("coop_sp_account as t1")
                                        ->join("coop_sp_account_detail as t2", "t1.account_id = t2.account_id")
                                        ->join("coop_sp_account_chart as t3", "t2.account_chart_id = t3.account_chart_id AND t3.group_id = '".$group_id."'", "left")
                                        ->where('t1.account_id = "'.$param["account_id"].'"')
                                        ->get()->result_array();
            foreach($account_details as $detail) {
                $month_acc = $detail["month"];
                $account_period = $this->db->select("accm_month_ini")->from("coop_account_period_setting")->order_by("accm_date_modified desc")->get()->row();
                if(empty($month_acc)) $month_acc = $account_period->accm_month_ini;
                $year_be = $account_period->accm_month_ini <= $month_acc ? $detail["year"] + 543 + 1 : $detail["year"] + 543;
                $this->increase_decrease_budget_year($group_id, $detail["account_chart_id"], $detail["account_amount"], $detail["account_type"], $year_be, 2);
            }

            $this->db->where('account_id', $param["account_id"]);
            $this->db->delete('coop_sp_account_detail');
        } else {
            $data_insert = array();
            $data_insert["group_id"] = $group_id;
            $data_insert['account_description'] = $data['coop_account']['account_description'];
            $data_insert['account_datetime'] = $data['coop_account']['account_datetime'];
            $data_insert['process'] = 'add_manual';
            $data_insert['journal_type'] = $param["journal_type"];
            $data_insert["journal_ref"] = $journal_ref;
            $data_insert["run_status"] = 0;
            $data_insert["account_status"] = 0;
            $data_insert['user_id'] = $_SESSION['USER_ID'];
            $this->db->insert('coop_sp_account', $data_insert);
            $account_id = $this->db->insert_id();
        }

        $index = 1;
        if($param["journal_type"] == "JV") {
            foreach($data['coop_account_detail'] as $key => $value){
                $data_insert = array();
                $data_insert['account_id'] = $account_id;
                $data_insert['account_type'] = $value['account_type'];
                $data_insert['account_amount'] = $value['account_amount'];
                $data_insert['account_chart_id'] = $value['account_chart_id'];
                $data_insert['description'] = $value['account_description'];
                $data_insert['seq_no'] = $index++;
                $this->db->insert('coop_sp_account_detail', $data_insert);

                $account_period = $this->db->select("accm_month_ini")->from("coop_account_period_setting")->order_by("accm_date_modified desc")->get()->row();
                if(empty($month)) $month = $account_period->accm_month_ini;
                $year_acc = $account_period->accm_month_ini <= $month ? $year + 1 : $year;
                $this->increase_decrease_budget_year($group_id, $value["account_chart_id"], $value["account_amount"], $value["account_type"], $year_acc, 1);
            }
        } else {
            $account_type = $param["journal_type"] == "RV" ? "credit" : "debit";
            $account_cash_type = $account_type == "credit" ? "debit" : "credit";
            $total_amount = 0;
            foreach($data['coop_account_detail'] as $key => $value){
                $data_insert = array();
                $data_insert['account_id'] = $account_id;
                $data_insert['account_type'] = $account_type;
                $data_insert['account_amount'] = $value['account_amount'];
                $data_insert['account_chart_id'] = $value['account_chart_id'];
                $data_insert['description'] = $value['account_description'];
                $data_insert['seq_no'] = $index++;
                $this->db->insert('coop_sp_account_detail', $data_insert);
                $total_amount += $value['account_amount'];

                $account_period = $this->db->select("accm_month_ini")->from("coop_account_period_setting")->order_by("accm_date_modified desc")->get()->row();
                if(empty($month)) $month = $account_period->accm_month_ini;
                $year_acc = $account_period->accm_month_ini <= $month ? $year + 1 : $year;
                $this->increase_decrease_budget_year($group_id, $value["account_chart_id"], $value["account_amount"], $account_type, $year_acc, 1);
            }

            //Get account for cash
            $cash_account = $this->db->select("*")->from("coop_sp_account_setting")->where("type = 'cash_chart_id' AND group_id = '".$group_id."'")->get()->row();
            $cash_id = $cash_account->value;

            $data_insert = array();
            $data_insert['account_id'] = $account_id;
            $data_insert['account_type'] = $account_cash_type;
            $data_insert['account_amount'] = $total_amount;
            $data_insert['account_chart_id'] = $cash_id;
            $data_insert['seq_no'] = $index++;
            $this->db->insert('coop_sp_account_detail', $data_insert);

            $account_period = $this->db->select("accm_month_ini")->from("coop_account_period_setting")->order_by("accm_date_modified desc")->get()->row();
            if(empty($month)) $month = $account_period->accm_month_ini;
            $year_acc = $account_period->accm_month_ini <= $month ? $year + 1 : $year;
            $this->increase_decrease_budget_year($group_id, $cash_id, $value["account_amount"], $account_cash_type, $year_acc, 1);
        }
        $result["status"] = "success";
        return $result;
    }

    public function cancel_account_transaction($group_id, $account_id) {
        $result = array();
        $accounts = $this->db->select("YEAR(t1.account_datetime) as year, MONTH(t1.account_datetime) as month, t2.account_type, t2.account_amount, t2.account_chart_id")
                            ->from("coop_sp_account as t1")
                            ->join("coop_sp_account_detail as t2", "t1.account_id = t2.account_id", "inner")
                            ->where("t1.account_id = '".$account_id."'")
                            ->get()->result_array();

        foreach($accounts as $account) {
            $year = $account["year"] + 543;
            $month = $account["month"];
            $account_period = $this->db->select("accm_month_ini")->from("coop_account_period_setting")->order_by("accm_date_modified desc")->get()->row();
            if(empty($month)) $month = $account_period->accm_month_ini;
            $year_acc = $account_period->accm_month_ini <= $month ? $year + 1 : $year;
            $this->increase_decrease_budget_year($account["account_chart_id"], $account["account_amount"], $account["account_type"], $year_acc, 2);
        }

        $data_insert = array();
        $data_insert['account_status'] = 2;
        $this->db->where('account_id', $_POST["account_id"]);
        $this->db->update('coop_sp_account', $data_insert);
        $result["status"] = "success";
        return $result;
    }

    /*****
        Function for calculate budget year
        Table coop_account_budget_year
        Type 1=increase(Add account)/2=decrease(Remove account)
        $year :: BE
    *****/
    public function increase_decrease_budget_year($group_id, $chart_id, $amount, $account_type, $year, $type) {
        $setting = $this->db->select("*")->from("coop_sp_account_setting")->where("type = 'budget_year_update' AND group_id = '".$group_id."'")->order_by("created_at")->get()->row();
        $budget_type = json_decode($setting->value);
        $chart_type = substr($chart_id, 0, 1);

        //UPDATE budget year only setting data
        if (in_array($chart_type, $budget_type)) {
            $chart = $this->db->select("entry_type")->from("coop_sp_account_chart")->where("account_chart_id = '{$chart_id}' AND group_id = '".$group_id."'")->get()->row();
            $entry_type = $chart->entry_type;
            $budget_years = $this->db->select("id, budget_amount")->from("coop_sp_account_budget_year")->where("account_chart_id = '".$chart_id."' AND year >= '".$year."' AND group_id = '".$group_id."'")->get()->result_array();
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
                $this->db->update('coop_sp_account_budget_year', $data_update);
            }
    
            if(empty($budget_years) && $type == 1) {
                $prev_year = $year - 1;
                $budget_prev_amount = 0;
                $budget_year = $this->db->select("budget_amount")->from("coop_sp_account_budget_year")->where("account_chart_id = '".$chart_id."' AND year <= '".$year."' AND group_id = '".$group_id."'")->order_by("year desc")->get()->row();
                if(!empty($budget_year)) {
                    $budget_prev_amount = $budget_year->budget_amount;
                }
                $budget_amount = $entry_type == 1 && $account_type == "debit" ? $budget_prev_amount + $amount :
                                        ($entry_type == 1 && $account_type == "credit" ? $budget_prev_amount - $amount :
                                        ($entry_type == 2 && $account_type == "credit" ? $budget_prev_amount + $amount :
                                        $budget_prev_amount - $amount));
    
                $data_insert = array();
                $data_insert["group_id"] = $group_id;
                $data_insert["budget_amount"] = $budget_amount;
                $data_insert["account_chart_id"] = $chart_id;
                $data_insert["budget_type"] = $entry_type == 1 ? "debit" : "credit";
                $data_insert["year"] = $year;
                $data_insert["create_time"] = date("Y-m-d H:i:s");
                $data_insert["update_time"] = date("Y-m-d H:i:s");
                $this->db->insert('coop_sp_account_budget_year', $data_insert);
            }
        }
    }

    public function generate_date_no($group_id, $date) {
        $ext_no = $this->db->select("*")->from("coop_sp_account_date_no")->where("date = DATE('".$date."') AND group_id = '".$group_id."'")->get()->row_array();
        $no = NULL;
        if(!empty($ext_no)) {
            $no = $ext_no['no'];
        } else {
            $perv_no = $this->db->select("*")->from("coop_sp_account_date_no")->where("YEAR(date) = YEAR('".$date."') AND date < '".$date."' AND group_id = '".$group_id."'")->order_by("no DESC")->get()->row_array();
            if(!empty($perv_no)) {
                $no = $perv_no['no'] + 1;
            } else {
                $yearPrefix = substr($date, 0,4) + 543 - 2500;
                $no = $yearPrefix."00000001";
            }

            $data_insert = array();
            $data_insert['date'] = $date;
            $data_insert['no'] = $no;
            $data_insert['group_id'] = $group_id;
            $data_insert["created_at"] = date("Y-m-d H:i:s");
            $data_insert["updated_at"] = date("Y-m-d H:i:s");
            $this->db->insert('coop_sp_account_date_no', $data_insert);

            //Check if date after $date exist no must re-render.
            $date_nos = $this->db->select("*")->from("coop_sp_account_date_no")->where("YEAR(date) = YEAR('".$date."') AND date > '".$date."' AND group_id = '".$group_id."'")->order_by("no")->get()->result_array();
            $nxt_no = $no;
            foreach($date_nos as $d_no) {
                $nxt_no++;
                $data_update = array();
                $data_update['no'] = $nxt_no;
                $data_update["updated_at"] = date("Y-m-d H:i:s");
                $this->db->where('id', $d_no["id"]);
                $this->db->update('coop_sp_account_date_no', $data_update);

                $accounts = $this->db->select("*")->from("coop_sp_account")->where("account_datetime = '".$d_no['date']."' AND group_id = '".$group_id."'")->get()->result_array();
                $data_updates = array();
                foreach($accounts as $account) {
                    $data_update = array();
                    $data_update['account_id'] = $account['account_id'];
                    $data_update['journal_ref'] = $account['journal_type'].$nxt_no;
                    $data_updates[] = $data_update;
                }

                if(!empty($data_updates)) {
                    $this->db->update_batch('coop_sp_account', $data_updates, 'account_id');
                }
            }
        }

        return $no;
    }

    public function get_voucher_number($group_id, $date, $type) {
        $no = "";

        $curr_account = $this->db->select("journal_ref")->from("coop_sp_account")->where("account_datetime = '".$date."' AND journal_type = '".$type."' AND account_status = '0' AND group_id = '".$group_id."'")->get()->row_array();
        if(!empty($curr_account)) {
            $no = substr($curr_account['journal_ref'], 1);
        } else {
            $perv_account = $this->db->select("journal_ref")
                                        ->from("coop_sp_account")
                                        ->where("YEAR(account_datetime) = YEAR('".$date."') AND account_datetime < '".$date."' AND journal_type = '".$type."' AND account_status = '0' AND group_id = '".$group_id."'")
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
                                        ->from("coop_sp_account")
                                        ->where("YEAR(account_datetime) = YEAR('".$date."') AND account_datetime > '".$date."' AND journal_type = '".$type."' AND account_status = '0' AND group_id = '".$group_id."'")
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
                $this->db->update_batch('coop_sp_account', $data_updates, 'account_datetime');
            }
        }

        return $no;
    }
}
