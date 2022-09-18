<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Compromise extends CI_Controller {
	function __construct() {
        parent::__construct();
        $this->month_arr = array('1'=>'มกราคม','2'=>'กุมภาพันธ์','3'=>'มีนาคม','4'=>'เมษายน','5'=>'พฤษภาคม','6'=>'มิถุนายน','7'=>'กรกฎาคม','8'=>'สิงหาคม','9'=>'กันยายน','10'=>'ตุลาคม','11'=>'พฤศจิกายน','12'=>'ธันวาคม');
	}

    public function index() {
        $x=0;
		$join_arr[$x]['table'] = 'coop_mem_apply as t2';
		$join_arr[$x]['condition'] = 't1.member_id = t2.member_id';
		$join_arr[$x]['type'] = 'inner';
		$x++;
		$join_arr[$x]['table'] = 'coop_prename as t3';
		$join_arr[$x]['condition'] = 't2.prename_id = t3.prename_id';
        $join_arr[$x]['type'] = 'left';
        $x++;
		$join_arr[$x]['table'] = 'coop_loan as t4';
		$join_arr[$x]['condition'] = 't1.loan_id = t4.id';
        $join_arr[$x]['type'] = 'inner';
        $x++;
		$join_arr[$x]['table'] = 'coop_user as t5';
		$join_arr[$x]['condition'] = 't1.user_id = t5.user_id';
        $join_arr[$x]['type'] = 'inner';

        $where = "t1.status = 1";

        $this->paginater_all->type(DB_TYPE);
        $this->paginater_all->select('t1.id as compromise_id, t1.created_at, t4.contract_number, t3.prename_full, t2.firstname_th, t2.lastname_th, t2.member_id, t5.user_name');
        $this->paginater_all->main_table('coop_loan_compromise as t1');
        $this->paginater_all->page_now($_GET["page"]);
        $this->paginater_all->per_page(20);
        $this->paginater_all->page_link_limit(20);
        $this->paginater_all->where($where);
        $this->paginater_all->order_by('t1.updated_at DESC');
        $this->paginater_all->join_arr($join_arr);
        $row = $this->paginater_all->paginater_process();

        $paging = $this->pagination_center->paginating($row['page'], $row['num_rows'], $row['per_page'], $row['page_link_limit'],$_GET);//$page_now = 1, $row_total = 1, $per_page = 20, $page_limit = 20

        foreach($row["data"] as $key => $data) {
            //Get new loan detail
            $details = $this->db->select("t1.status, t1.type, t2.id, t2.loan_type, t2.contract_number, t2.loan_amount_balance, t3.member_id, t3.firstname_th, t3.lastname_th, t4.prename_full")
                                ->from("coop_loan_guarantee_compromise as t1")
                                ->join("coop_loan as t2", "t1.loan_id = t2.id", "inner")
                                ->join("coop_mem_apply as t3", "t1.member_id = t3.member_id", "inner")
                                ->join("coop_prename as t4", "t3.prename_id = t4.prename_id", "left")
                                ->where("t1.compromise_id = '".$data["compromise_id"]."' AND t1.status IN (1,3)")
                                ->order_by("t1.created_at desc")
                                ->get()->result_array();
            $row["data"][$key]["details"] = $details;
        }

        $arr_data["datas"] = $row["data"];
        $arr_data["paging"] = $paging;
        $this->libraries->template('compromise/index',$arr_data);
    }

    public function guarantees_process() {
        $arr_data = array();

        if(!empty($_GET["member_id"])) {
            //Get member detail
            $member = $this->db->select("t1.member_id, t1.firstname_th, t1.lastname_th, t2.prename_full")
                                ->from("coop_mem_apply as t1")
                                ->join("coop_prename as t2", "t1.prename_id = t2.prename_id", "left")
                                ->where("member_id = '".$_GET["member_id"]."'")
                                ->get()->result_array()[0];
            $arr_data["member"] = $member;

            //Get member loan
            $loans = $this->db->select("t1.id as loan_id, t1.contract_number")
                                ->from("coop_loan as t1")
                                ->join("coop_loan_compromise as t2", "t1.id = t2.loan_id", "left")
                                ->where("t1.member_id = '".$_GET["member_id"]."' AND t1.loan_status in (1,6) AND t2.id is null")
                                ->get()->result_array();
            $arr_data["loans"] = $loans;

            //Get loan type
            $loan_types = $this->db->select("loan_name_id, loan_name")
                                    ->from("coop_loan_name")
                                    ->order_by("order_by")
                                    ->get()->result_array();
            $arr_data["loan_types"] = $loan_types;
            //Get loan interest rate
            $term_of_loans = $this->db->select("*")
                                        ->from("coop_term_of_loan")
                                        ->where("start_date <= '".date('Y-m-d')."'")
                                        ->order_by("start_date")
                                        ->get()->result_array();
            $interest_rates = array();
            foreach($term_of_loans as $term) {
                $interest_rates[$term["type_id"]] = $term["interest_rate"];
            }
            $arr_data["interest_rates"] = $interest_rates;

            $resign_infos = $this->db->select("*")->from("coop_mem_req_resign")->where("member_id = '".$_GET["member_id"]."' AND req_resign_status = 1")->get()->result_array();
            $arr_data["resign_info"] = $resign_infos[0];
        }

        $this->libraries->template('compromise/guarantees_process',$arr_data);
    }

    public function loaner_process() {
        $arr_data = array();

        if(!empty($_GET["member_id"])) {
            //Get member detail
            $member = $this->db->select("t1.member_id, t1.firstname_th, t1.lastname_th, t2.prename_full")
                                ->from("coop_mem_apply as t1")
                                ->join("coop_prename as t2", "t1.prename_id = t2.prename_id", "left")
                                ->where("member_id = '".$_GET["member_id"]."'")
                                ->get()->result_array()[0];
            $arr_data["member"] = $member;

            //Get member loan
            $loans = $this->db->select("t1.id as loan_id, t1.contract_number")
                                ->from("coop_loan as t1")
                                ->join("coop_loan_compromise as t2", "t1.id = t2.loan_id", "left")
                                ->where("t1.member_id = '".$_GET["member_id"]."' AND t1.loan_status in (1,6) AND t2.id is null")
                                ->get()->result_array();
            $arr_data["loans"] = $loans;

            //Get loan type
            $loan_types = $this->db->select("loan_name_id, loan_name")
                                    ->from("coop_loan_name")
                                    ->order_by("order_by")
                                    ->get()->result_array();
            $arr_data["loan_types"] = $loan_types;
            //Get loan interest rate
            $term_of_loans = $this->db->select("*")
                                        ->from("coop_term_of_loan")
                                        ->where("start_date <= '".date('Y-m-d')."'")
                                        ->order_by("start_date")
                                        ->get()->result_array();
            $interest_rates = array();
            foreach($term_of_loans as $term) {
                $interest_rates[$term["type_id"]] = $term["interest_rate"];
            }
            $arr_data["interest_rates"] = $interest_rates;

            $resign_infos = $this->db->select("*")->from("coop_mem_req_resign")->where("member_id = '".$_GET["member_id"]."' AND req_resign_status = 1")->get()->result_array();
            $arr_data["resign_info"] = $resign_infos[0];
        }

        $this->libraries->template('compromise/loaner_process',$arr_data);
    }

    public function return_process() {
        $arr_data = array();

        if(!empty($_GET["compromise_id"])) {
            //Get compromise detail
            $compromises = $this->db->select("t2.compromise_id,
                                                t2.member_id,
                                                t2.other_debt_blance,
                                                t2.loan_id as new_loan_id,
                                                t5.loan_amount_balance,
                                                t3.firstname_th,
                                                t3.lastname_th,
                                                t4.prename_full,
                                                t5.contract_number,
                                                t5.loan_amount_balance,
                                                t5.loan_type,
                                                sum(t6.principal_payment) as principal,
                                                sum(t6.interest) as interest,
                                                t1.loan_id as old_loan_id,
                                                t1.interest_debt as old_interest_debt,
                                                t1.fund_support as fund,
                                                t1.fund_support_interest as fund_interest,
                                                t1.fund_support_percent as fund_percent,
                                                t2.fund_support as divide_fund,
                                                t2.fund_support_interest as divide_fund_interest,
                                                t2.fund_support_balance as fund_balance,
                                                t2.fund_support_interest_balance as fund_interest_balance,
                                            ")
                                    ->from("coop_loan_compromise as t1")
                                    ->join("coop_loan_guarantee_compromise as t2", "t1.id = t2.compromise_id")
                                    ->join("coop_mem_apply as t3", "t2.member_id = t3.member_id", "inner")
                                    ->join("coop_prename as t4", "t3.prename_id = t4.prename_id", "left")
                                    ->join("coop_loan as t5", "t2.loan_id = t5.id", "inner")
                                    ->join("coop_finance_transaction as t6", "t2.loan_id = t6.loan_id", "left")
                                    ->join("coop_receipt as t7", "t6.receipt_id = t7.receipt_id", "left")
                                    ->where("t1.id = '".$_GET["compromise_id"]."' AND (t7.receipt_status is null or t7.receipt_status = 0 or t7.receipt_status = '')")
                                    ->group_by("t2.loan_id")
                                    ->get()->result_array();

            //Get old loan
            $loan_id = $compromises[0]["old_loan_id"];
            $loan = $this->db->select("t1.id as loan_id, t1.contract_number, t1.loan_amount, t1.loan_amount_balance, t1.member_id, t2.loan_name")
                                ->from("coop_loan as t1")
                                ->join("coop_loan_name as t2", "t1.loan_type = t2.loan_name_id", "left")
                                ->where("t1.id = '".$loan_id."'")
                                ->get()->result_array()[0];
            $arr_data["loan"] = $loan;

            //Get member detail
            $member = $this->db->select("t1.member_id, t1.firstname_th, t1.lastname_th, t2.prename_full")
                                ->from("coop_mem_apply as t1")
                                ->join("coop_prename as t2", "t1.prename_id = t2.prename_id", "left")
                                ->where("member_id = '".$loan["member_id"]."'")
                                ->get()->result_array()[0];
            $arr_data["member"] = $member;

            //Get loan type
            $loan_types = $this->db->select("loan_name_id, loan_name")
                                    ->from("coop_loan_name")
                                    ->order_by("order_by")
                                    ->get()->result_array();
            $arr_data["loan_types"] = $loan_types;
            //Get loan interest rate
            $term_of_loans = $this->db->select("*")
                                        ->from("coop_term_of_loan")
                                        ->where("start_date <= '".date('Y-m-d')."'")
                                        ->order_by("start_date")
                                        ->get()->result_array();
            $interest_rates = array();
            foreach($term_of_loans as $term) {
                $interest_rates[$term["type_id"]] = $term["interest_rate"];
            }
            $arr_data["interest_rates"] = $interest_rates;

            //Get debt
            $interest_debt = 0;
            $other_debt = 0;
            foreach($compromises as $key => $compromise) {
                //Get other debt left
                $other_debt += $compromise["other_debt_blance"];
                $fund_balance += $compromise["fund_balance"];
                $fund_interest_balance += $compromise["fund_interest_balance"];

                //Get interest non pay left
                $non_pay_interests = $this->db->select("SUM(non_pay_amount_balance) as interest_debt")
                                                ->from("coop_non_pay_detail")
                                                ->where("loan_id = '".$compromise["new_loan_id"]."' AND deduct_code = 'LOAN' AND pay_type = 'interest'")
                                                ->get()->row();
                $interest_debt += $non_pay_interests->interest_debt;

                $loan_interest_debt = $this->db->select("SUM(interest_balance) as interest_debt")->from("coop_loan_interest_debt")->where("interest_status = 0 AND loan_id = '".$compromise["new_loan_id"]."'")->get()->row();
                $interest_debt += $loan_interest_debt->interest_debt;

                //Get current interest
                $interest_data_raw = 0;
                $curr_BE = date("Y") + 543;
                $finance_month = $this->db->select("*")
                                            ->from("coop_finance_month_detail as t1")
                                            ->join("coop_finance_month_profile as t2", "t1.profile_id = t2.profile_id")
                                            ->where("t1.loan_id = '".$compromise["new_loan_id"]."' AND t2.profile_year = '".$curr_BE."' AND t2.profile_month = '".date("m")."' AND t1.run_status = 1")
                                            ->get()->row();
                if(empty($finance_month)) {
                    $rs_date1 = $this->db->select("date(t1.transaction_datetime) as transaction_datetime")
                                            ->from("coop_loan_transaction AS t1")
                                            ->join("coop_receipt AS t2"," t1.receipt_id = t2.receipt_id","left")
                                            ->join("coop_finance_transaction AS t3", "t3.receipt_id = t2.receipt_id AND t1.loan_id = t3.loan_id AND t3.interest > 0", "inner")
											->where("t1.loan_id = '".$compromise["new_loan_id"]."' AND (t2.receipt_status IS NULL OR t2.receipt_status = '') AND t2.receipt_id != 'C'")
                                            ->order_by("t1.transaction_datetime DESC")
                                            ->limit(1)
                                            ->get()->result_array();
                    $date1 = $rs_date1[0]['transaction_datetime'];

                    $tmp_date1 = date("Y-m", strtotime($date1) );
                    if($tmp_date1 != date("Y-m")) {
                        $date1 = date("Y-m-t", strtotime("last month"));
                    }

                    $date2 = date("Y-m-d");
                    $interest_data_raw = $this->loan_libraries->calc_interest_loan($compromise["loan_amount_balance"], $compromise["new_loan_id"], $date1, $date2);
                }
                $compromises[$key]["interest_data_raw"] = round($interest_data_raw);
                //Get refrain if exist
                $year = date("Y") + 543;
                $month = date("m");
                $loan_refrain = $this->db->select("refrain_loan_id, refrain_type")
                                            ->from("coop_refrain_loan")
                                            ->where("loan_id = '".$compromise["new_loan_id"]."' AND status != 2 AND refrain_type IN (2,3) AND (year_start < ".$year." || (year_start = ".$year." AND month_start <= ".$month."))
                                                        AND ((year_end > ".$year." || (year_end = ".$year." AND month_start >= ".$month.") || period_type = 2))")
                                            ->get()->result_array();
                if(!empty($loan_refrain) && !empty($interest_data_raw)) {
                    // $interest_data = 0;
                    $compromises[$key]["refrain_loan_id"] = $loan_refrain[0]["refrain_loan_id"];
                } else {
                    $interest_debt += round($interest_data_raw);
                }
            }

            // echo "<pre>";
            // print_r($compromises);
            // exit;
            $arr_data["compromises"] = $compromises;
            $arr_data["interest_debt"] = $interest_debt;
            $arr_data["other_debt"] = $other_debt;
            $arr_data["fund_balance"] = $fund_balance;
            $arr_data["fund_interest_balance"] = $fund_interest_balance;
            $arr_data["fund"] = $compromises[0]["fund"];
            $arr_data["fund_interest"] = $compromises[0]["fund_interest"];
        }

        $this->libraries->template('compromise/return_process',$arr_data);
    }

    public function view_compromise() {
        $arr_data = array();

        if(!empty($_GET["compromise_id"])) {
            //Get compromise detail
            $compromises = $this->db->select("t2.compromise_id,
                                                t2.member_id,
                                                t2.other_debt_blance,
                                                t2.loan_id as new_loan_id,
                                                t5.loan_amount_balance,
                                                t3.firstname_th,
                                                t3.lastname_th,
                                                t4.prename_full,
                                                t5.contract_number,
                                                t5.loan_amount_balance,
                                                t5.loan_type,
                                                sum(t6.principal_payment) as principal,
                                                sum(t6.interest) as interest,
                                                t1.loan_id as old_loan_id,
                                                t1.interest_debt as old_interest_debt,
                                                t1.fund_support as fund,
                                                t1.fund_support_interest as fund_interest,
                                                t1.fund_support_percent as fund_percent,
                                                t2.fund_support as divide_fund,
                                                t2.fund_support_interest as divide_fund_interest,
                                                t2.fund_support_balance as fund_balance,
                                                t2.fund_support_interest_balance as fund_interest_balance,
                                            ")
                                    ->from("coop_loan_compromise as t1")
                                    ->join("coop_loan_guarantee_compromise as t2", "t1.id = t2.compromise_id")
                                    ->join("coop_mem_apply as t3", "t2.member_id = t3.member_id", "inner")
                                    ->join("coop_prename as t4", "t3.prename_id = t4.prename_id", "left")
                                    ->join("coop_loan as t5", "t2.loan_id = t5.id", "inner")
                                    ->join("coop_finance_transaction as t6", "t2.loan_id = t6.loan_id", "left")
                                    ->join("coop_receipt as t7", "t6.receipt_id = t7.receipt_id", "left")
                                    ->where("t1.id = '".$_GET["compromise_id"]."' AND (t7.receipt_status is null or t7.receipt_status = 0 or t7.receipt_status = '')")
                                    ->group_by("t2.loan_id")
                                    ->get()->result_array();
            $arr_data["compromises"] = $compromises;

            //Get old loan
            $loan_id = $compromises[0]["old_loan_id"];
            $loan = $this->db->select("t1.id as loan_id, t1.contract_number, t1.loan_amount, t1.loan_amount_balance, t1.member_id, t2.loan_name")
                                ->from("coop_loan as t1")
                                ->join("coop_loan_name as t2", "t1.loan_type = t2.loan_name_id", "left")
                                ->where("t1.id = '".$loan_id."'")
                                ->get()->result_array()[0];
            $arr_data["loan"] = $loan;

            //Get member detail
            $member = $this->db->select("t1.member_id, t1.firstname_th, t1.lastname_th, t2.prename_full")
                                ->from("coop_mem_apply as t1")
                                ->join("coop_prename as t2", "t1.prename_id = t2.prename_id", "left")
                                ->where("member_id = '".$loan["member_id"]."'")
                                ->get()->result_array()[0];
            $arr_data["member"] = $member;

            //Get loan type
            $loan_types = $this->db->select("loan_name_id, loan_name")
                                    ->from("coop_loan_name")
                                    ->order_by("order_by")
                                    ->get()->result_array();
            $arr_data["loan_types"] = $loan_types;
            //Get loan interest rate
            $term_of_loans = $this->db->select("*")
                                        ->from("coop_term_of_loan")
                                        ->where("start_date <= '".date('Y-m-d')."'")
                                        ->order_by("start_date")
                                        ->get()->result_array();
            $interest_rates = array();
            foreach($term_of_loans as $term) {
                $interest_rates[$term["type_id"]] = $term["interest_rate"];
            }
            $arr_data["interest_rates"] = $interest_rates;

            //Get debt
            $interest_debt = 0;
            $other_debt = 0;
            foreach($compromises as $compromise) {
                //Get other debt left
                $other_debt += $compromise["other_debt_blance"];
                $fund_balance += $compromise["fund_balance"];
                $fund_interest_balance += $compromise["fund_interest_balance"];

                //Get interest non pay left
                $non_pay_interests = $this->db->select("SUM(non_pay_amount_balance) as interest_debt")
                                                ->from("coop_non_pay_detail")
                                                ->where("loan_id = '".$compromise["new_loan_id"]."' AND deduct_code = 'LOAN' AND pay_type = 'interest'")
                                                ->get()->row();
                $interest_debt += $non_pay_interests->interest_debt;

                $loan_interest_debt = $this->db->select("SUM(interest_balance) as interest_debt")->from("coop_loan_interest_debt")->where("interest_status = 0 AND loan_id = '".$compromise["new_loan_id"]."'")->get()->row();
                $interest_debt += $loan_interest_debt->interest_debt;

                //Get current interest
                $year = date("Y") + 543;
                $month = date("m");
                $loan_refrain = $this->db->select("refrain_loan_id, refrain_type")
                                            ->from("coop_refrain_loan")
                                            ->where("loan_id = '".$value['id']."' AND status != 2 AND refrain_type IN (2,3) AND (year_start < ".$year." || (year_start = ".$year." AND month_start <= ".$month."))
                                                        AND ((year_end > ".$year." || (year_end = ".$year." AND month_start >= ".$month.") || period_type = 2))")
                                            ->get()->result_array();
                if(empty($loan_refrain)) {
                    $curr_BE = date("Y") + 543;
                    $finance_month = $this->db->select("*")
                                                ->from("coop_finance_month_detail as t1")
                                                ->join("coop_finance_month_profile as t2", "t1.profile_id = t2.profile_id")
                                                ->where("t1.loan_id = '".$compromise["new_loan_id"]."' AND t2.profile_year = '".$curr_BE."' AND t2.profile_month = '".date("m")."' AND t1.run_status = 1")
                                                ->get()->row();
                    if(empty($finance_month)) {
                        $rs_date1 = $this->db->select("date(t1.transaction_datetime) as transaction_datetime")
                                                ->from("coop_loan_transaction AS t1")
                                                ->join("coop_receipt AS t2"," t1.receipt_id = t2.receipt_id","left")
                                                ->join("coop_finance_transaction AS t3", "t3.receipt_id = t2.receipt_id AND t1.loan_id = t3.loan_id AND t3.interest > 0")
											    ->where("t1.loan_id = '".$compromise['new_loan_id']."' AND (t2.receipt_status IS NULL OR t2.receipt_status = '') AND t2.receipt_id != 'C'")
                                                ->order_by("t1.transaction_datetime DESC")
                                                ->limit(1)
                                                ->get()->result_array();
                        $date1 = $rs_date1[0]['transaction_datetime'];

                        $tmp_date1 = date("Y-m", strtotime($date1) );
                        if($tmp_date1 != date("Y-m")) {
                            $date1 = date("Y-m-t", strtotime("last month"));
                        }

                        $date2 = date("Y-m-d");
                        $interest_data = $this->loan_libraries->calc_interest_loan($compromise["loan_amount_balance"], $compromise["new_loan_id"], $date1, $date2);
                        $interest_debt += round($interest_data);
                    }
                }
            }
            $arr_data["interest_debt"] = $interest_debt;
            $arr_data["other_debt"] = $other_debt;
            $arr_data["fund_balance"] = $fund_balance;
            $arr_data["fund_interest_balance"] = $fund_interest_balance;
            $arr_data["fund"] = $compromises[0]["fund"];
            $arr_data["fund_interest"] = $compromises[0]["fund_interest"];
        }

        $this->libraries->template('compromise/view_compromise',$arr_data);
    }

    public function get_loan_detail() {
        $result = array();
        $loan_id = $_GET["loan_id"];

        //Get loan detail
        $loan = $this->db->select("t1.id as loan_id, t1.contract_number, t1.loan_amount, t1.loan_amount_balance, t1.member_id, t2.loan_name")
                            ->from("coop_loan as t1")
                            ->join("coop_loan_name as t2", "t1.loan_type = t2.loan_name_id", "left")
                            ->where("t1.id = '".$loan_id."'")
                            ->get()->row();
        $result["loan_id"] = $loan_id;
        $result["contract_number"] = $loan->contract_number;
        $result["loan_amount"] = $loan->loan_amount;
        $result["loan_amount_balance"] = $loan->loan_amount_balance;
        $result["loan_amount_text"] = number_format($loan->loan_amount,2);
        $result["loan_amount_balance_text"] = number_format($loan->loan_amount_balance,2);
        $result["loan_name"] = $loan->loan_name;

        //Get debt interest
        $debt = $this->db->select("sum(balance) as sum_balance")
                            ->from("coop_mem_resign_non_pay")
                            ->where("loan_id = '".$loan_id."'")
                            ->get()->row();
        if(!empty($debt->sum_balance)) {
            $result["debt_interest"] = $debt->sum_balance;
            $result["debt_interest_text"] = number_format($debt->sum_balance,2);
        } else {
            $debt = $this->db->select("sum(non_pay_amount_balance) as sum_balance")
                                ->from("coop_non_pay_detail")
                                ->where("loan_id = '".$loan_id."' AND pay_type = 'interest'")
                                ->get()->row();
            $result["debt_interest"] = $debt->sum_balance;
            $result["debt_interest_text"] = number_format($debt->sum_balance,2);
        }

        //Get fund
        $total_fund = 0;
        $accounts = $this->db->select("t1.account_id")
                            ->from("coop_maco_account as t1")
                            ->where("t1.mem_id = '".$loan->member_id."' AND t1.account_status = '0' AND t1.type_id in (26,27,28)")
                            ->get()->result_array();
        foreach($accounts as $account) {
            $transaction = $this->db->select("transaction_balance")
                                    ->from("coop_account_transaction")
                                    ->where("account_id = '".$account['account_id']."' AND cancel_status is null")
                                    ->order_by("transaction_time DESC")
                                    ->get()->result_array();
            $total_fund += $transaction[0]["transaction_balance"];
        }
        $result["total_fund"] = $total_fund;
        $result["total_fund_text"] = number_format($total_fund,2);

        //GET guarantees
        $guarantees = $this->db->select("coop_prename.prename_full, coop_mem_apply.firstname_th, coop_mem_apply.lastname_th, coop_mem_apply.member_id")
                                ->from("coop_loan_guarantee_person")
                                ->join("coop_mem_apply", "coop_loan_guarantee_person.guarantee_person_id = coop_mem_apply.member_id", "inner")
                                ->join("coop_prename", "coop_prename.prename_id = coop_mem_apply.prename_id", "left")
                                ->where("loan_id = '".$loan_id."' AND coop_loan_guarantee_person.guarantee_person_id is not null AND coop_loan_guarantee_person.guarantee_person_id != ''")
                                ->get()->result_array();
        $result["guarantees"] = $guarantees;

        //Get Refrain
        $year = date("Y") + 543;
        $month = date("m");
        $loan_refrain = $this->db->select("refrain_loan_id, refrain_type")
                                    ->from("coop_refrain_loan")
                                    ->where("loan_id = '".$loan_id."' AND status != 2 AND refrain_type IN (2,3) AND (year_start < ".$year." || (year_start = ".$year." AND month_start <= ".$month."))
                                                AND ((year_end > ".$year." || (year_end = ".$year." AND month_start >= ".$month.") || period_type = 2))")
                                    ->get()->result_array();
        if(!empty($loan_refrain)) {
            $result["refrain_loan_id"] = $loan_refrain[0]["refrain_loan_id"];
        }

        echo json_encode($result);
    }

    public function run_compromise_guarantees_process() {
        //Set formation of data
        $loan_id = $_POST["contract_number"];
        $debt_divides = array_values(array_filter($_POST['debt_divide']));
        $interest_debt_divides = array_values(array_filter($_POST['interest_debt_divide']));
        $period_count_divides = array_values(array_filter($_POST['period_count']));
        $period_divides = array_values(array_filter($_POST['period']));
        $other_divides = array_values(array_filter($_POST['other_debt']));
        $fund_supports = array_values(array_filter($_POST['fund_support_divide']));
        $fund_support_interests = array_values(array_filter($_POST['fund_support_interest_divide']));

        $loan_type = $_POST['loan_type'];

        //Get Old loan Info
        $loan = $this->db->select("*")
                            ->from("coop_loan")
                            ->where("id = '".$loan_id."'")
                            ->get()->row();


        $process_date = date('Y-m-d H:i:s');
        $date_approve = date("Y-m-d", strtotime(str_replace("/", "-", $_POST['date_approve']) ." -543 Year"))." ".date("H:i:s");
        $yymm = (date("Y")+543).date("m");

        //Create Compromise
        $data_insert = array();
        $data_insert["loan_id"] = $loan_id;
        $data_insert["member_id"] = $_POST["member_id"];
        $data_insert["fund_support"] = $_POST["fund_support"];
        $data_insert["fund_support_interest"] = $_POST["fund_support_interest"];
        if($_POST["fund_unit"] == 2) $data_insert["fund_support_percent"] = $_POST["fund_support_percent"];
        $data_insert["interest_debt"] = $_POST["interest_debt"];
        $data_insert["other_debt"] = $_POST["new_loan_other_debt"];
        $data_insert["other_debt_blance"] = $_POST["new_loan_other_debt"];
        $data_insert["status"] = 1;
        $data_insert["user_id"] = $_SESSION['USER_ID'];
        $data_insert['created_at'] = $process_date;
        $data_insert['updated_at'] = $process_date;
        $this->db->insert('coop_loan_compromise', $data_insert);
        $compromise_id = $this->db->insert_id();

        $receipts = array();
        $new_loan_ids = array();
        //Create New Loan & Close old loan
        $balance = $loan->loan_amount_balance;
        $guarantor_inserts = array();
        $total_debt_interest = 0;
        $guarantee_persons = $this->db->select("*")
                                        ->from("coop_loan_guarantee_person")
                                        ->where("loan_id = '".$loan_id."' AND guarantee_person_id is not null && guarantee_person_id != ''")
                                        ->get()->result_array();
        foreach($_POST["gua_persons"] as $key => $member_id) {
            $index = $key+1;
            //Generate petition number
            $this->db->select('petition_number');
            $this->db->from("coop_loan");
            $this->db->order_by("petition_number DESC");
            $this->db->limit(1);
            $rs_petition_number = $this->db->get()->result_array();
            $row_petition_number = $rs_petition_number[0];
            $petition_number = (int)$row_petition_number['petition_number']+1;
            $petition_number_text = sprintf('% 06d',@$petition_number);

            $gua_member_info = $this->db->select("*")->from("coop_mem_apply")->where("member_id = '".$member_id."'")->get()->row();

            $data_insert = array();
			$data_insert['admin_id'] = $_SESSION['USER_ID'];
			$data_insert['loan_type'] = $loan_type;
			$data_insert['createdatetime'] = $process_date;
            $data_insert['updatetimestamp'] = $process_date;
			$data_insert['contract_number'] = $loan->contract_number."/".$index;
            $data_insert['loan_amount'] = $debt_divides[$key];
            $data_insert['loan_amount_balance'] = $debt_divides[$key];
            $data_insert['loan_amount_balance'] = $debt_divides[$key];
            $data_insert['loan_status'] = '1';
            $data_insert['petition_number'] = $petition_number_text;
            $data_insert['member_id'] = $member_id;
            $data_insert["loan_reason"] = 45;
            $data_insert['period_type'] = $_POST["cal_period_type"];
            $data_insert['period_amount'] = $period_count_divides[$key];
            $data_insert['loan_amount_total'] = $_POST["data"][$member_id]["loan_amount_total"];
            $data_insert['loan_amount_total_balance'] = $_POST["data"][$member_id]["loan_amount_total"];
            $data_insert['interest_per_year'] = $_POST["interest_rate"];
            $data_insert['money_per_period'] = $period_divides[$key];
            $data_insert["date_start_period"] = $_POST["data"][$member_id]["date_start_period"];
            $data_insert["pay_type"] = 2;
            $data_insert["salary"] = $gua_member_info->salary;
            $data_insert["deduct_status"] = '0';
            $data_insert["approve_date"] = $date_approve;
            $data_insert["transfer_status"] = '0';
            $data_insert['date_last_interest'] = $date_approve;
			$this->db->insert('coop_loan', $data_insert);
            $new_loan_id = $this->db->insert_id();

            $data_insert = array();
            $data_insert['loan_id'] = $new_loan_id;
            $data_insert['loan_amount_balance'] = $debt_divides[$key];
            $data_insert['transaction_datetime'] = $date_approve;
            $this->db->insert('coop_loan_transaction', $data_insert);

            $data_insert = array();
            $data_insert['loan_id'] = $new_loan_id;
            $data_insert['date_receive_money'] = $process_date;
            $data_insert['date_first_period'] = $_POST["data"][$member_id]["date_start_period"];
            $data_insert['first_interest'] = $_POST["data"][$member_id]["first_interest"];
            $data_insert['pay_per_month'] = $period_divides[$key];
            $data_insert['estimate_receive_money'] = $_POST["data"][$member_id]["loan_amount_total"];
            $this->db->insert('coop_loan_deduct_profile', $data_insert);
            $loan_deduct_id = $this->db->insert_id();

            $data_insert = array();
            $data_insert['loan_id'] = $new_loan_id;
            $data_insert['date_transfer'] = $date_approve;
            $data_insert['createdatetime'] = $process_date;
            $data_insert['admin_id'] = $_SESSION['USER_ID'];
            $data_insert['transfer_status'] = '0';
            $data_insert['amount_transfer'] = 0;
            $this->db->insert('coop_loan_transfer', $data_insert);

            $data_insert = array();
            $res = $this->db->select('outgoing_code')->from('coop_outgoing')->where('outgoing_status = 1')->get()->result_array();
            $index = 0;
            $data_insert = array();
            foreach ($res as $res_key => $val){
                $data_insert[$index]['member_id'] = $member_id;
                $data_insert[$index]['loan_id'] = $new_loan_id;
                $data_insert[$index]['loan_cost_code'] = $val['outgoing_code'];
                $data_insert[$index]['loan_cost_amount'] = 0;
                $index++;
            }
            unset($index);
            if(!empty($data_insert))$this->db->insert_batch('coop_loan_cost_mod', $data_insert);

            //Create loan period
            $data_inserts = array();
            foreach($_POST['data']['coop_loan_period'][$member_id] as $loan_period) {
                $data_insert = array();
                $data_insert['date_period'] = $loan_period['date_period'];
                $data_insert['date_count'] = $loan_period['date_count'];
                $data_insert['interest'] = $loan_period['interest'];
                $data_insert['principal_payment'] = $loan_period['principal_payment'];
                $data_insert['total_paid_per_month'] = $loan_period['total_paid_per_month'];
                $data_insert['period_count'] = $loan_period['period_count'];
                $data_insert['outstanding_balance'] = $loan_period['outstanding_balance'];
                $data_insert["loan_id"] = $new_loan_id;
                $data_inserts[] = $data_insert;
            }
            if (!empty($data_inserts)) {
                $this->db->insert_batch('coop_loan_period', $data_inserts);
            }

            //Generate non pay
            if(!empty($interest_debt_divides[$key]) && $interest_debt_divides[$key] > 0) {
                $data_insert = array();
                $data_insert['member_id'] = $member_id;
                $data_insert['loan_id'] = $new_loan_id;
                $data_insert['interest_total'] = $interest_debt_divides[$key];
                $data_insert['interest_balance'] = $interest_debt_divides[$key];
                $data_insert['interest_date'] = $process_date;
                $data_insert['interest_status'] = 0;
                $data_insert['admin_id'] = $_SESSION['USER_ID'];
                $data_insert['created_datetime'] = $process_date;
                $data_insert['updated_datetime'] = $process_date;
                $this->db->insert('coop_loan_interest_debt', $data_insert);

                $total_debt_interest += $interest_debt_divides[$key];
            }
            // coop_loan_guarantee_compromise
            $data_insert = array();
            $data_insert["compromise_id"] = $compromise_id;
            $data_insert["loan_id"] = $new_loan_id;
            $data_insert["member_id"] = $member_id;
            $data_insert["type"] = $_POST["type"];
            $data_insert["status"] = 1;
            $data_insert["interest_debt"] = $interest_debt_divides[$key];
            $data_insert["other_debt"] = $other_divides[$key];
            $data_insert["other_debt_blance"] = $other_divides[$key];
            $data_insert["fund_support"] = $fund_supports[$key];
            $data_insert["fund_support_interest"] = $fund_support_interests[$key];
            $data_insert["fund_support_balance"] = $fund_supports[$key];
            $data_insert["fund_support_interest_balance"] = $fund_support_interests[$key];
            if($_POST["fund_unit"] == 2) $data_insert["fund_support_percent"] = $_POST["fund_support_percent"];
            $data_insert['created_at'] = $process_date;
            $data_insert['updated_at'] = $process_date;
            $this->db->insert('coop_loan_guarantee_compromise', $data_insert);
            $sub_compromise_id = $this->db->insert_id();

            //Create receipt
            $this->db->select(array('*'));
            $this->db->from('coop_receipt');
            $this->db->where("receipt_id LIKE '".$yymm."%'");
            $this->db->order_by("receipt_id DESC");
            $this->db->limit(1);
            $row_receipt = $this->db->get()->result_array();
            $row_receipt = @$row_receipt[0];

            if(@$row_receipt['receipt_id'] != '') {
                $id = (int) substr($row_receipt["receipt_id"], 6);
                $receipt_id = $yymm.sprintf("%06d", $id + 1);
            }else {
                $receipt_id = $yymm."000001";
            }

            //Generate old loan transaction
            if($balance > 0 || !empty($interest_debt_divides[$key])) {
                $payment = 0;
                if($balance > $debt_divides[$key]) {
                    $balance = $balance - $debt_divides[$key];
                    $payment = $debt_divides[$key];
                } else {
                    $payment = $balance;
                    $balance = 0;
                }

                $data_insert = array();
                $data_insert['receipt_id'] = $receipt_id;
                $data_insert['member_id'] = $member_id;
                $data_insert['loan_id'] = $loan_id;
                $data_insert['account_list_id'] = 15;
                $data_insert['principal_payment'] = $payment;
                $data_insert['loan_interest_remain'] = $interest_debt_divides[$key];
                $data_insert['total_amount'] = $interest_debt_divides[$key] + $payment;
                $data_insert['payment_date'] = $date_approve;
                $data_insert['loan_amount_balance'] = $balance;
                $data_insert['createdatetime'] = $process_date;
                $data_insert['transaction_text'] = "หักกลบเงินกู้เลขที่สัญญา ".$loan->contract_number;
                $data_insert['deduct_type'] = "all";
                $this->db->insert('coop_finance_transaction', $data_insert);

                $data_insert = array();
                $data_insert['receipt_id'] = $receipt_id;
                $data_insert['receipt_list'] = 15;
                $data_insert['receipt_count'] = $interest_debt_divides[$key] + $payment;
                $this->db->insert('coop_receipt_detail', $data_insert);

                $data_insert = array();
                $data_insert['receipt_id'] = $receipt_id;
                $data_insert['member_id'] = $member_id;
                $data_insert['admin_id'] = $_SESSION['USER_ID'];
                $data_insert['sumcount'] = $interest_debt_divides[$key] + $payment;
                $data_insert['receipt_datetime'] = $date_approve;
                $this->db->insert('coop_receipt', $data_insert);
                $receipts[] = $receipt_id;

                $lastest_loan_balance = $this->db->select("*")->from("coop_loan_transaction")->where("loan_id = '".$loan_id."'")->order_by("transaction_datetime DESC, loan_transaction_id DESC")->get()->row();
                $data_insert = array();
                $data_insert['loan_id'] = $loan_id;
                $data_insert['loan_amount_balance'] = $lastest_loan_balance - $payment;
                $data_insert['transaction_datetime'] = $date_approve;
                $data_insert['receipt_id'] = $receipt_id;
                $this->db->insert('coop_loan_transaction', $data_insert);
            }

            //Create Guarantee Data
            $gua_count = count($guarantee_persons);
            $amount = (ceil(($_POST["new_loan_amount_balance"]/$gua_count) * 100) / 100);
            foreach($guarantee_persons as $index => $guarantee_person) {
                if($member_id != $guarantee_person["guarantee_person_id"]) {
                    $amount = (floor(($_POST["new_loan_amount_balance"]/$gua_count) * 100) / 100);
                    $data_insert = array();
                    $data_insert["guarantee_person_id"] = $guarantee_person["guarantee_person_id"];
                    $data_insert["guarantee_person_amount"] = $amount;
                    $data_insert["guarantee_person_amount_balance"] = $amount;
                    $data_insert["loan_id"] = $new_loan_id;
                    $guarantor_inserts[] = $data_insert;
                }
            }

            //create fund support transaction
            $data_insert = array();
            $data_insert["compromise_id"] = $compromise_id;
            $data_insert["sub_compromise_id"] = $sub_compromise_id;
            $data_insert["principal"] = $fund_supports[$key];
            $data_insert["interest"] = $fund_support_interests[$key];
            $data_insert["payment_date"] = $date_approve;
            $data_insert["createdatetime"] = $process_date;
            $this->db->insert('coop_loan_fund_balance_transaction', $data_insert);
        }

        //Generate Fund support payment for old loan
        //Create receipt
        $this->db->select(array('*'));
        $this->db->from('coop_receipt');
        $this->db->where("receipt_id LIKE '".$yymm."%'");
        $this->db->order_by("receipt_id DESC");
        $this->db->limit(1);
        $row_receipt = $this->db->get()->result_array();
        $row_receipt = @$row_receipt[0];
        if(@$row_receipt['receipt_id'] != '') {
            $id = (int) substr($row_receipt["receipt_id"], 6);
            $receipt_id = $yymm.sprintf("%06d", $id + 1);
        }else {
            $receipt_id = $yymm."000001";
        }

        $data_insert = array();
        $data_insert['receipt_id'] = $receipt_id;
        $data_insert['member_id'] = "FSG001";//Fund member id
        $data_insert['loan_id'] = $loan_id;
        $data_insert['account_list_id'] = 15;
        $data_insert['principal_payment'] = $_POST["fund_support"];
        $data_insert['loan_interest_remain'] = $_POST["fund_support_interest"];
        $data_insert['total_amount'] = $_POST["fund_support"] + $_POST["fund_support_interest"];
        $data_insert['payment_date'] = $date_approve;
        $data_insert['loan_amount_balance'] = 0;
        $data_insert['createdatetime'] = $process_date;
        $data_insert['transaction_text'] = "หักกลบเงินกู้เลขที่สัญญา ".$loan->contract_number;
        $data_insert['deduct_type'] = "all";
        $this->db->insert('coop_finance_transaction', $data_insert);

        $data_insert = array();
        $data_insert['receipt_id'] = $receipt_id;
        $data_insert['receipt_list'] = 15;
        $data_insert['receipt_count'] = $_POST["fund_support"] + $_POST["fund_support_interest"];
        $this->db->insert('coop_receipt_detail', $data_insert);

        $data_insert = array();
        $data_insert['receipt_id'] = $receipt_id;
        $data_insert['member_id'] = "FSG001";//Fund member id
        $data_insert['admin_id'] = $_SESSION['USER_ID'];
        $data_insert['sumcount'] = $_POST["fund_support"] + $_POST["fund_support_interest"];
        $data_insert['receipt_datetime'] = $date_approve;
        $this->db->insert('coop_receipt', $data_insert);
        $receipts[] = $receipt_id;

        $data_insert = array();
        $data_insert['loan_id'] = $loan_id;
        $data_insert['loan_amount_balance'] = 0;
        $data_insert['transaction_datetime'] = $date_approve;
        $data_insert['receipt_id'] = $receipt_id;
        $this->db->insert('coop_loan_transaction', $data_insert);

        //Insert Guarantee Data
        if (!empty($guarantor_inserts)) {
            $this->db->insert_batch('coop_loan_guarantee_person', $guarantor_inserts);
        }

        //close old loan
        $data_insert = array();
        $data_insert['loan_status'] = 7;
        $data_insert['loan_amount_balance'] = 0;
        $this->db->where('id',$loan_id);
        $this->db->update('coop_loan',$data_insert);

        //Clear non pay
        $non_pay_details = $this->db->select("run_id, non_pay_id")
                                    ->from("coop_non_pay_detail")
                                    ->where("loan_id = '".$loan_id."' AND non_pay_amount_balance > 0")
                                    ->group_by("non_pay_id")
                                    ->get()->result_array();
        $data_insert = array();
        foreach($non_pay_details as $key => $detail) {
            foreach($receipts as $non_pay_receipt_id) {
                $data_insert[$key]["member_id"] = $_POST["member_id"];
                $data_insert[$key]["non_pay_id"] = $detail["non_pay_id"];
                $data_insert[$key]["receipt_id"] = $non_pay_receipt_id;
                $data_insert[$key]["createdatetime"] = $process_date;
                $data_insert[$key]["receipt_status"] = 0;
            }
        }
        if (!empty($data_insert)) {
            $this->db->insert_batch('coop_non_pay_receipt', $data_insert);
        }
        $data_insert = array();
        $data_insert['non_pay_amount_balance'] = 0;
        $this->db->where('loan_id', $loan_id);
        $this->db->update('coop_non_pay_detail',$data_insert);

        $data_insert = array();
        $data_insert['balance'] = 0;
        $this->db->where('loan_id', $loan_id);
        $this->db->update('coop_mem_resign_non_pay',$data_insert);

        $non_pay_details = $this->db->select("run_id, non_pay_id, sum(non_pay_amount_balance) as sum")
                                    ->from("coop_non_pay_detail")
                                    ->where("loan_id = '".$loan_id."'")
                                    ->group_by("non_pay_id")
                                    ->get()->result_array();
        foreach($non_pay_details as $detail) {
            $balance = $detail['sum'];
            $data_insert = array();
            if($balance <= 0) {
                $balance == 0;
                $data_insert['non_pay_status'] = 2;
            }
            $data_insert['non_pay_amount_balance'] = $balance;
            $data_insert['updatetimestamp'] = $process_date;
            $data_insert['pay_admin_id'] = $_SESSION['USER_ID'];
            $this->db->where('non_pay_id = "'.$detail['non_pay_id'].'"');
            $this->db->update('coop_non_pay', $data_insert);
        }

        $data_insert = array();
        $data_insert['mem_type'] = 4;
        $this->db->where('member_id',$_POST["member_id"]);
        $this->db->update('coop_mem_apply',$data_insert);

        
        if(!empty($_POST["refrain_loan_id"]) && !empty($_POST["data_interest"])) {
            $data_insert = array();
            $data_insert["refrain_loan_id"] = $_POST["refrain_loan_id"];
            $data_insert["member_id"] = $_POST["member_id"];
            $data_insert["pay_type"] = "interest";
            $data_insert["org_value"] = $_POST["data_interest"];
            $data_insert["paid_value"] = 0;
            $data_insert["status"] = 1;
            $data_insert["paid_date"] = $process_date;
            $data_insert["receipt_id"] = $receipt_id;
            $data_insert["createdatetime"] = $process_date;
            $data_insert["updatedatetime"] = $process_date;
            $this->db->insert('coop_loan_refrain_history', $data_insert);
        }

        echo "<script> document.location.href='".base_url(PROJECTPATH.'/compromise')."' </script>";
    }

    public function run_compromise_loaner_process() {
        //Get loan monry payment
        $cal_value = array();
        $cal_value["interest"] = $_POST["new_interest_rate"];
        $cal_value["loan"] = $_POST["new_loan_amount_balance"];
        $cal_value["pay_type"] = 2;
        $cal_value["period_type"] = $_POST["cal_period_type"];
        $cal_value["period"] = $_POST["period"];
        $cal_value["period_amount_bath"] = $_POST["period_amount_bath"];
        $cal_value["day"] = date("d");
        $cal_value["month"] = date("m");
        $cal_value["year"] = date("Y") + 543;
        $loan_periods = $this->get_cal_period($cal_value);

        $loan_id = $_POST["contract_number"];
        $member_id = $_POST["member_id"];
        $process_date = date('Y-m-d H:i:s');
        $yymm = (date("Y")+543).date("m");
        $member_info = $this->db->select("*")->from("coop_mem_apply")->where("member_id = '".$member_id."'")->get()->row();

        //Get Old loan Info
        $loan = $this->db->select("*")
                            ->from("coop_loan")
                            ->where("id = '".$loan_id."'")
                            ->get()->row();

        //Create Compromise
        $data_insert = array();
        $data_insert["loan_id"] = $loan_id;
        $data_insert["member_id"] = $member_id;
        $data_insert["interest_debt"] = $_POST["new_loan_interest_debt"];
        $data_insert["other_debt"] = $_POST["new_loan_other_debt"];
        $data_insert["other_debt_blance"] = $_POST["new_loan_other_debt"];
        $data_insert["status"] = 1;
        $data_insert["user_id"] = $_SESSION['USER_ID'];
        $data_insert['created_at'] = $process_date;
        $data_insert['updated_at'] = $process_date;
        $this->db->insert('coop_loan_compromise', $data_insert);
        $compromise_id = $this->db->insert_id();

        //Generate petition number
        $this->db->select('petition_number');
        $this->db->from("coop_loan");
        $this->db->order_by("id DESC");
        $this->db->limit(1);
        $rs_petition_number = $this->db->get()->result_array();
        $row_petition_number = $rs_petition_number[0];
        $petition_number = (int)$row_petition_number['petition_number']+1;
        $petition_number_text = sprintf('% 06d',@$petition_number);

        $data_insert = array();
        $data_insert['admin_id'] = $_SESSION['USER_ID'];
        $data_insert['loan_type'] = $_POST["new_loan_type"];
        $data_insert['createdatetime'] = $process_date;
        $data_insert['updatetimestamp'] = $process_date;
        $data_insert['contract_number'] = $_POST["new_contract_number"];
        $data_insert['loan_amount'] = $_POST["new_loan_amount_balance"];
        $data_insert['loan_amount_balance'] = $_POST["new_loan_amount_balance"];
        $data_insert['loan_status'] = '1';
        $data_insert['petition_number'] = $petition_number_text;
        $data_insert['member_id'] = $member_id;
        $data_insert["loan_reason"] = 45;
        $data_insert['period_type'] = $_POST["cal_period_type"];
        $data_insert['period_amount'] = count($loan_periods["periods"]);
        $data_insert['loan_amount_total'] = $loan_periods["total_loan_pay"];
        $data_insert['loan_amount_total_balance'] = $loan_periods["total_loan_pay"];
        $data_insert['interest_per_year'] = $_POST["new_interest_rate"];
        $data_insert['money_per_period'] = $loan_periods["periods"][0]["total_paid_per_month"];
        $data_insert["date_start_period"] = $loan_periods["periods"][0]["date_period"];
        $data_insert["pay_type"] = 2;
        $data_insert["salary"] = $member_info->salary;
        $data_insert["deduct_status"] = '0';
        $data_insert["approve_date"] = $process_date;
        $data_insert["transfer_status"] = '0';
        $this->db->insert('coop_loan', $data_insert);
        $new_loan_id = $this->db->insert_id();

        $data_insert = array();
        $data_insert['loan_id'] = $new_loan_id;
        $data_insert['loan_amount_balance'] = $_POST["new_loan_amount_balance"];
        $data_insert['transaction_datetime'] = $process_date;
        $this->db->insert('coop_loan_transaction', $data_insert);

        $data_insert = array();
        $data_insert['loan_id'] = $new_loan_id;
        $data_insert['date_receive_money'] = $process_date;
        $data_insert['date_first_period'] = $loan_periods["periods"][0]["date_period"];
        $data_insert['first_interest'] = $loan_periods["periods"][0]["interest"];
        $data_insert['pay_per_month'] = $loan_periods["periods"][0]["total_paid_per_month"];
        $data_insert['estimate_receive_money'] = $loan_periods["total_loan_pay"];
        $this->db->insert('coop_loan_deduct_profile', $data_insert);
        $loan_deduct_id = $this->db->insert_id();

        $data_insert = array();
        $data_insert['loan_id'] = $new_loan_id;
        $data_insert['date_transfer'] = $process_date;
        $data_insert['createdatetime'] = $process_date;
        $data_insert['admin_id'] = $_SESSION['USER_ID'];
        $data_insert['transfer_status'] = '0';
        $data_insert['amount_transfer'] = 0;
        $this->db->insert('coop_loan_transfer', $data_insert);

        $data_insert = array();
        $res = $this->db->select('outgoing_code')->from('coop_outgoing')->where('outgoing_status = 1')->get()->result_array();
        $index = 0;
        $data_insert = array();
        foreach ($res as $res_key => $val){
            $data_insert[$index]['member_id'] = $member_id;
            $data_insert[$index]['loan_id'] = $new_loan_id;
            $data_insert[$index]['loan_cost_code'] = $val['outgoing_code'];
            $data_insert[$index]['loan_cost_amount'] = 0;
            $index++;
        }
        unset($index);
        if(!empty($data_insert)) $this->db->insert_batch('coop_loan_cost_mod', $data_insert);

        //Create loan period
        $data_inserts = array();
        foreach($loan_periods["periods"] as $key => $loan_period) {
            $data_insert = array();
            $data_insert['date_period'] = $loan_period['date_period'];
            $data_insert['date_count'] = $loan_period['date_count'];
            $data_insert['interest'] = $loan_period['interest'];
            $data_insert['principal_payment'] = $loan_period['principal_payment'];
            $data_insert['total_paid_per_month'] = $loan_period['total_paid_per_month'];
            $data_insert['period_count'] = $key+1;
            $data_insert['outstanding_balance'] = $loan_period['outstanding_balance'];
            $data_insert["loan_id"] = $new_loan_id;
            $data_inserts[] = $data_insert;
        }
        if (!empty($data_inserts)) {
            $this->db->insert_batch('coop_loan_period', $data_inserts);
        }

        //Generate Non pay for interest debt
        $total_debt_interest = 0;
        $new_loan_interest_debt = $_POST["new_loan_interest_debt"];
        if(!empty($new_loan_interest_debt) && $new_loan_interest_debt > 0) {
            $data_insert = array();
            $data_insert['member_id'] = $member_id;
            $data_insert['loan_id'] = $new_loan_id;
            $data_insert['interest_total'] = $new_loan_interest_debt;
            $data_insert['interest_balance'] = $new_loan_interest_debt;
            $data_insert['interest_date'] = $process_date;
            $data_insert['interest_status'] = 0;
            $data_insert['admin_id'] = $_SESSION['USER_ID'];
            $data_insert['created_datetime'] = $process_date;
            $data_insert['updated_datetime'] = $process_date;
            $this->db->insert('coop_loan_interest_debt', $data_insert);

            $total_debt_interest += $new_loan_interest_debt;
        }
        
        // coop_loan_guarantee_compromise
        $data_insert = array();
        $data_insert["compromise_id"] = $compromise_id;
        $data_insert["loan_id"] = $new_loan_id;
        $data_insert["member_id"] = $member_id;
        $data_insert["type"] = $_POST["type"];
        $data_insert["status"] = 1;
        $data_insert["other_debt"] = $_POST["new_loan_other_debt"];
        $data_insert["other_debt_blance"] = $_POST["new_loan_other_debt"];
        $data_insert["interest_debt"] = $_POST["new_loan_interest_debt"];
        $data_insert['created_at'] = $process_date;
        $data_insert['updated_at'] = $process_date;
        $this->db->insert('coop_loan_guarantee_compromise', $data_insert);

        //close old loan
        //Generate old loan transaction
        $receipts = array();
        $balance = $loan->loan_amount_balance;
        if($balance > 0 || !empty($total_debt_interest)) {
            $payment = $balance;
            $balance = 0;

            //Create Receipt
            $this->db->select(array('*'));
            $this->db->from('coop_receipt');
            $this->db->where("receipt_id LIKE '".$yymm."%'");
            $this->db->order_by("receipt_id DESC");
            $this->db->limit(1);
            $row_receipt = $this->db->get()->result_array();
            $row_receipt = @$row_receipt[0];

            if(@$row_receipt['receipt_id'] != '') {
                $id = (int) substr($row_receipt["receipt_id"], 6);
                $receipt_id = $yymm.sprintf("%06d", $id + 1);
            }else {
                $receipt_id = $yymm."000001";
            }

            $data_insert = array();
            $data_insert['receipt_id'] = $receipt_id;
            $data_insert['member_id'] = $member_id;
            $data_insert['loan_id'] = $loan_id;
            $data_insert['account_list_id'] = 15;
            $data_insert['principal_payment'] = $payment;
            $data_insert['loan_interest_remain'] = $total_debt_interest;
            $data_insert['total_amount'] = $total_debt_interest + $payment;
            $data_insert['payment_date'] = $process_date;
            $data_insert['loan_amount_balance'] = $balance;
            $data_insert['createdatetime'] = $process_date;
            $data_insert['transaction_text'] = "หักกลบเงินกู้เลขที่สัญญา ".$loan->contract_number;
            $data_insert['deduct_type'] = "all";
            $this->db->insert('coop_finance_transaction', $data_insert);

            $data_insert = array();
            $data_insert['receipt_id'] = $receipt_id;
            $data_insert['receipt_list'] = 15;
            $data_insert['receipt_count'] = $total_debt_interest + $payment;
            $this->db->insert('coop_receipt_detail', $data_insert);

            $data_insert = array();
            $data_insert['receipt_id'] = $receipt_id;
            $data_insert['member_id'] = $member_id;
            $data_insert['admin_id'] = $_SESSION['USER_ID'];
            $data_insert['sumcount'] = $total_debt_interest + $payment;
            $data_insert['receipt_datetime'] = $process_date;
            $this->db->insert('coop_receipt', $data_insert);
            $receipts[] = $receipt_id;

            $data_insert = array();
            $data_insert = array();
            $data_insert['loan_id'] = $loan_id;
            $data_insert['loan_amount_balance'] = 0;
            $data_insert['transaction_datetime'] = $process_date;
            $data_insert['receipt_id'] = $receipt_id;
            $this->db->insert('coop_loan_transaction', $data_insert);

            if(!empty($_POST["refrain_loan_id"]) && !empty($_POST["data_interest"])) {
                $data_insert = array();
                $data_insert["refrain_loan_id"] = $_POST["refrain_loan_id"];
                $data_insert["member_id"] = $member_id;
                $data_insert["pay_type"] = "interest";
                $data_insert["org_value"] = $_POST["data_interest"];
                $data_insert["paid_value"] = 0;
                $data_insert["status"] = 1;
                $data_insert["paid_date"] = $process_date;
                $data_insert["receipt_id"] = $receipt_id;
                $data_insert["createdatetime"] = $process_date;
                $data_insert["updatedatetime"] = $process_date;
                $this->db->insert('coop_loan_refrain_history', $data_insert);
            }
        }
        $data_insert = array();
        $data_insert['loan_status'] = 7;
        $data_insert['loan_amount_balance'] = 0;
        $this->db->where('id',$loan_id);
        $this->db->update('coop_loan',$data_insert);

        //Create Guarantee Data
        $guarantor_inserts = array();
        $guarantee_persons = $this->db->select("*")
                                        ->from("coop_loan_guarantee_person")
                                        ->where("loan_id = '".$loan_id."'")
                                        ->get()->result_array();
        $gua_count = count($guarantee_persons);
        foreach($guarantee_persons as $index => $guarantee_person) {
            $amount = 0;
            if($index == 0) {
                $amount = (ceil(($_POST["new_loan_amount_balance"]/$gua_count) * 100) / 100);
            } else {
                $amount = (floor(($_POST["new_loan_amount_balance"]/$gua_count) * 100) / 100);
            }
            $data_insert = array();
            $data_insert["guarantee_person_id"] = $guarantee_person["guarantee_person_id"];
            $data_insert["guarantee_person_amount"] = $amount;
            $data_insert["guarantee_person_amount_balance"] = $amount;
            $data_insert["loan_id"] = $new_loan_id;
            $guarantor_inserts[] = $data_insert;
        }
        //Insert Guarantee Data
        if (!empty($guarantor_inserts)) {
            $this->db->insert_batch('coop_loan_guarantee_person', $guarantor_inserts);
        }

        //Clear non pay
        $non_pay_details = $this->db->select("run_id, non_pay_id")
                                    ->from("coop_non_pay_detail")
                                    ->where("loan_id = '".$loan_id."' AND non_pay_amount_balance > 0")
                                    ->group_by("non_pay_id")
                                    ->get()->result_array();
        $data_insert = array();
        foreach($non_pay_details as $key => $detail) {
            foreach($receipts as $receipt_id) {
                $data_insert[$key]["member_id"] = $_POST["member_id"];
                $data_insert[$key]["non_pay_id"] = $detail["non_pay_id"];
                $data_insert[$key]["receipt_id"] = $receipt_id;
                $data_insert[$key]["createdatetime"] = $process_date;
                $data_insert[$key]["receipt_status"] = 0;
            }
        }
        if (!empty($data_insert)) {
            $this->db->insert_batch('coop_non_pay_receipt', $data_insert);
        }

        $data_insert = array();
        $data_insert['non_pay_amount_balance'] = 0;
        $this->db->where('loan_id', $loan_id);
        $this->db->update('coop_non_pay_detail',$data_insert);

        $data_insert = array();
        $data_insert['balance'] = 0;
        $this->db->where('loan_id', $loan_id);
        $this->db->update('coop_mem_resign_non_pay',$data_insert);

        $non_pay_details = $this->db->select("run_id, non_pay_id, sum(non_pay_amount_balance) as sum")
                                    ->from("coop_non_pay_detail")
                                    ->where("loan_id = '".$loan_id."'")
                                    ->group_by("non_pay_id")
                                    ->get()->result_array();
        foreach($non_pay_details as $detail) {
            $balance = $detail['sum'];
            $data_insert = array();
            if($balance <= 0) {
                $balance == 0;
                $data_insert['non_pay_status'] = 2;
            }
            $data_insert['non_pay_amount_balance'] = $balance;
            $data_insert['updatetimestamp'] = $process_date;
            $data_insert['pay_admin_id'] = $_SESSION['USER_ID'];
            $this->db->where('non_pay_id = "'.$detail['non_pay_id'].'"');
            $this->db->update('coop_non_pay', $data_insert);
        }

        $data_insert = array();
        $data_insert['mem_type'] = 4;
        $this->db->where('member_id',$_POST["member_id"]);
        $this->db->update('coop_mem_apply',$data_insert);

        echo "<script> document.location.href='".base_url(PROJECTPATH.'/compromise')."' </script>";
    }

    public function run_compromise_return_process() {
        //Get loan monry payment
        $cal_value = array();
        $cal_value["interest"] = $_POST["new_interest_rate"];
        $cal_value["loan"] = $_POST["new_loan_amount_balance"];
        $cal_value["pay_type"] = 2;
        $cal_value["period_type"] = $_POST["cal_period_type"];
        $cal_value["period"] = $_POST["period"];
        $cal_value["period_amount_bath"] = $_POST["period_amount_bath"];
        $cal_value["day"] = date("d");
        $cal_value["month"] = date("m");
        $cal_value["year"] = date("Y") + 543;
        $loan_periods = $this->get_cal_period($cal_value);

        $compromise_id = $_POST["compromise_id"];
        $process_date = date('Y-m-d H:i:s');
        $yymm = (date("Y")+543).date("m");

        $compromise = $this->db->select("loan_id, member_id")->from("coop_loan_compromise")->where("id = '".$compromise_id."'")->get()->row();
        $compromise_details = $this->db->select("*")->from("coop_loan_guarantee_compromise")->where("compromise_id = '".$compromise_id."'")->get()->result_array();
        $loan = $this->db->select("*")->from("coop_loan")->where("id = '".$compromise->loan_id."'")->get()->row();
        $member_id = $compromise->member_id;

        $data_insert = array();
        $data_insert['updated_at'] = $process_date;
        $this->db->where('id', $compromise_id);
        $this->db->update('coop_loan_compromise',$data_insert);

        //Generate New contract
        //Generate petition number
        $this->db->select('petition_number');
        $this->db->from("coop_loan");
        $this->db->order_by("id DESC");
        $this->db->limit(1);
        $rs_petition_number = $this->db->get()->result_array();
        $row_petition_number = $rs_petition_number[0];
        $petition_number = (int)$row_petition_number['petition_number']+1;
        $petition_number_text = sprintf('% 06d',@$petition_number);
        
        $data_insert = array();
        $data_insert['admin_id'] = $_SESSION['USER_ID'];
        $data_insert['loan_type'] = $_POST["new_loan_type"];
        $data_insert['createdatetime'] = $process_date;
        $data_insert['updatetimestamp'] = $process_date;
        $data_insert['contract_number'] = $loan->contract_number."/R";
        $data_insert['loan_amount'] = $_POST["new_loan_amount_balance"];
        $data_insert['loan_amount_balance'] = $_POST["new_loan_amount_balance"];
        $data_insert['loan_status'] = '1';
        $data_insert['petition_number'] = $petition_number_text;
        $data_insert['member_id'] = $member_id;
        $data_insert["loan_reason"] = 45;
        $data_insert['period_type'] = $_POST["cal_period_type"];
        $data_insert['period_amount'] = count($loan_periods["periods"]);
        $data_insert['loan_amount_total'] = $loan_periods["total_loan_pay"];
        $data_insert['loan_amount_total_balance'] = $loan_periods["total_loan_pay"];
        $data_insert['interest_per_year'] = $_POST["new_interest_rate"];
        $data_insert['money_per_period'] = $loan_periods["periods"][0]["total_paid_per_month"];
        $data_insert["date_start_period"] = $loan_periods["periods"][0]["date_period"];
        $data_insert["pay_type"] = 2;
        $data_insert["salary"] = $member_info->salary;
        $data_insert["deduct_status"] = '0';
        $data_insert["approve_date"] = $process_date;
        $data_insert["transfer_status"] = '0';
        $this->db->insert('coop_loan', $data_insert);
        $new_loan_id = $this->db->insert_id();

        $data_insert = array();
        $data_insert['loan_id'] = $new_loan_id;
        $data_insert['loan_amount_balance'] = $_POST["new_loan_amount_balance"];
        $data_insert['transaction_datetime'] = $process_date;
        $this->db->insert('coop_loan_transaction', $data_insert);

        $data_insert = array();
        $data_insert['loan_id'] = $new_loan_id;
        $data_insert['date_receive_money'] = $process_date;
        $data_insert['date_first_period'] = $loan_periods["periods"][0]["date_period"];
        $data_insert['first_interest'] = $loan_periods["periods"][0]["interest"];
        $data_insert['pay_per_month'] = $loan_periods["periods"][0]["total_paid_per_month"];
        $data_insert['estimate_receive_money'] = $loan_periods["total_loan_pay"];
        $this->db->insert('coop_loan_deduct_profile', $data_insert);
        $loan_deduct_id = $this->db->insert_id();

        $data_insert = array();
        $data_insert['loan_id'] = $new_loan_id;
        $data_insert['date_transfer'] = $process_date;
        $data_insert['createdatetime'] = $process_date;
        $data_insert['admin_id'] = $_SESSION['USER_ID'];
        $data_insert['transfer_status'] = '0';
        $data_insert['amount_transfer'] = 0;
        $this->db->insert('coop_loan_transfer', $data_insert);

        $res = $this->db->select('outgoing_code')->from('coop_outgoing')->where('outgoing_status = 1')->get()->result_array();
        $index = 0;
        $data_insert = array();
        foreach ($res as $res_key => $val){
            $data_insert[$index]['member_id'] = $member_id;
            $data_insert[$index]['loan_id'] = $new_loan_id;
            $data_insert[$index]['loan_cost_code'] = $val['outgoing_code'];
            $data_insert[$index]['loan_cost_amount'] = 0;
            $index++;
        }
        unset($index);
        if(!empty($data_insert)) $this->db->insert_batch('coop_loan_cost_mod', $data_insert);

        //Create loan period
        $data_inserts = array();
        foreach($loan_periods["periods"] as $key => $loan_period) {
            $data_insert = array();
            $data_insert['date_period'] = $loan_period['date_period'];
            $data_insert['date_count'] = $loan_period['date_count'];
            $data_insert['interest'] = $loan_period['interest'];
            $data_insert['principal_payment'] = $loan_period['principal_payment'];
            $data_insert['total_paid_per_month'] = $loan_period['total_paid_per_month'];
            $data_insert['period_count'] = $key + 1;
            $data_insert['outstanding_balance'] = $loan_period['outstanding_balance'];
            $data_insert["loan_id"] = $new_loan_id;
            $data_inserts[] = $data_insert;
        }
        if (!empty($data_inserts)) {
            $this->db->insert_batch('coop_loan_period', $data_inserts);
        }

        //Generate interest debt
        $data_insert = array();
        $data_insert['member_id'] = $member_id;
        $data_insert['loan_id'] = $new_loan_id;
        $data_insert['interest_total'] = $_POST["new_loan_interest_debt"];
        $data_insert['interest_balance'] = $_POST["new_loan_interest_debt"];
        $data_insert['interest_date'] = $process_date;
        $data_insert['interest_status'] = 0;
        $data_insert['admin_id'] = $_SESSION['USER_ID'];
        $data_insert['created_datetime'] = $process_date;
        $data_insert['updated_datetime'] = $process_date;
        $this->db->insert('coop_loan_interest_debt', $data_insert);

        //Close old compromise And Generate Guarantees
        foreach($compromise_details as $compromise_detail) {
            $data_insert = array();
            $data_insert['status'] = 3;
            $data_insert["other_debt_blance"] = 0;   
            $data_insert['updated_at'] = $process_date;
            $this->db->where('id', $compromise_detail["id"]);
            $this->db->update('coop_loan_guarantee_compromise',$data_insert);

            //Update Fund support balance transaction
            $data_insert = array();
            $data_insert["compromise_id"] = $compromise_id;
            $data_insert["sub_compromise_id"] = $compromise_detail["id"];
            $data_insert["principal"] = 0;
            $data_insert["interest"] = 0;
            $data_insert["payment_date"] = $process_date;
            $data_insert["createdatetime"] = $process_date;
            $this->db->insert('coop_loan_fund_balance_transaction', $data_insert);

            $old_loan = $this->db->select("*")
                                    ->from("coop_loan")
                                    ->where("id = '".$compromise_detail["loan_id"]."'")
                                    ->get()->row();

            //close old loan
            //Generate old loan transaction
            $interest_debts = $this->db->select("sum(non_pay_amount_balance) as sum")
                                        ->from("coop_non_pay_detail")
                                        ->where("loan_id = '".$old_loan->id."' AND pay_type = 'interest'")
                                        ->get()->row();

            $total_debt_interest = $interest_debts->sum;
            $balance = $old_loan->loan_amount_balance;
            $receipts = array();
            if($balance > 0 || !empty($total_debt_interest)) {
                //Create Receipt
                $this->db->select(array('*'));
                $this->db->from('coop_receipt');
                $this->db->where("receipt_id LIKE '".$yymm."%'");
                $this->db->order_by("receipt_id DESC");
                $this->db->limit(1);
                $row_receipt = $this->db->get()->result_array();
                $row_receipt = $row_receipt[0];

                if(@$row_receipt['receipt_id'] != '') {
                    $id = (int) substr($row_receipt["receipt_id"], 6);
                    $receipt_id = $yymm.sprintf("%06d", $id + 1);
                }else {
                    $receipt_id = $yymm."000001";
                }

                $data_insert = array();
                $data_insert['receipt_id'] = $receipt_id;
                $data_insert['member_id'] = $member_id;
                $data_insert['loan_id'] = $old_loan->id;
                $data_insert['account_list_id'] = 15;
                $data_insert['principal_payment'] = $balance;
                $data_insert['loan_interest_remain'] = $total_debt_interest;
                $data_insert['total_amount'] = $total_debt_interest + $balance;
                $data_insert['payment_date'] = $process_date;
                $data_insert['loan_amount_balance'] = 0;
                $data_insert['createdatetime'] = $process_date;
                $data_insert['transaction_text'] = "หักกลบเงินกู้เลขที่สัญญา ".$old_loan->contract_number;
                $data_insert['deduct_type'] = "all";
                $this->db->insert('coop_finance_transaction', $data_insert);

                $data_insert = array();
                $data_insert['receipt_id'] = $receipt_id;
                $data_insert['receipt_list'] = 15;
                $data_insert['receipt_count'] = $total_debt_interest + $balance;
                $this->db->insert('coop_receipt_detail', $data_insert);

                $data_insert = array();
                $data_insert['receipt_id'] = $receipt_id;
                $data_insert['member_id'] = $member_id;
                $data_insert['admin_id'] = $_SESSION['USER_ID'];
                $data_insert['sumcount'] = $total_debt_interest + $balance;
                $data_insert['receipt_datetime'] = $process_date;
                $this->db->insert('coop_receipt', $data_insert);
                $receipts[] = $receipt_id;

                $data_insert = array();
                $data_insert = array();
                $data_insert['loan_id'] = $old_loan->id;
                $data_insert['loan_amount_balance'] = 0;
                $data_insert['transaction_datetime'] = $process_date;
                $data_insert['receipt_id'] = $receipt_id;
                $this->db->insert('coop_loan_transaction', $data_insert);
            }
            $data_insert = array();
            $data_insert['loan_status'] = 4;
            $data_insert['loan_amount_balance'] = 0;
            $this->db->where('id',$old_loan->id);
            $this->db->update('coop_loan',$data_insert);

            //Clear non pay
            $non_pay_details = $this->db->select("run_id, non_pay_id")
                                        ->from("coop_non_pay_detail")
                                        ->where("loan_id = '".$loan_id."' AND non_pay_amount_balance > 0")
                                        ->group_by("non_pay_id")
                                        ->get()->result_array();
            $data_insert = array();
            foreach($non_pay_details as $key => $detail) {
                foreach($receipts as $non_pay_receipt_id) {
                    $data_insert[$key]["member_id"] = $_POST["member_id"];
                    $data_insert[$key]["non_pay_id"] = $detail["non_pay_id"];
                    $data_insert[$key]["receipt_id"] = $non_pay_receipt_id;
                    $data_insert[$key]["createdatetime"] = $process_date;
                    $data_insert[$key]["receipt_status"] = 0;
                }
            }
            if (!empty($data_insert)) {
                $this->db->insert_batch('coop_non_pay_receipt', $data_insert);
            }

            $data_insert = array();
            $data_insert['non_pay_amount_balance'] = 0;
            $this->db->where('loan_id', $old_loan->id);
            $this->db->update('coop_non_pay_detail',$data_insert);

            $data_insert = array();
            $data_insert['balance'] = 0;
            $this->db->where('loan_id', $old_loan->id);
            $this->db->update('coop_mem_resign_non_pay',$data_insert);

            $non_pay_details = $this->db->select("run_id, non_pay_id, sum(non_pay_amount_balance) as sum")
                                        ->from("coop_non_pay_detail")
                                        ->where("loan_id = '".$old_loan->id."'")
                                        ->group_by("non_pay_id")
                                        ->get()->result_array();
            foreach($non_pay_details as $detail) {
                $balance = $detail['sum'];
                $data_insert = array();
                if($balance <= 0) {
                    $balance == 0;
                    $data_insert['non_pay_status'] = 2;
                }
                $data_insert['non_pay_amount_balance'] = $balance;
                $data_insert['updatetimestamp'] = $process_date;
                $data_insert['pay_admin_id'] = $_SESSION['USER_ID'];
                $this->db->where('non_pay_id = "'.$detail['non_pay_id'].'"');
                $this->db->update('coop_non_pay', $data_insert);
            }

            if(!empty($_POST["refrain_loan_id"][$compromise_detail["member_id"]]) && !empty($_POST["interest_debt"][$compromise_detail["member_id"]])) {
                $data_insert = array();
                $data_insert["refrain_loan_id"] = $_POST["refrain_loan_id"][$compromise_detail["member_id"]];
                $data_insert["member_id"] = $compromise_detail["member_id"];
                $data_insert["pay_type"] = "interest";
                $data_insert["org_value"] = $_POST["interest_debt"][$compromise_detail["member_id"]];
                $data_insert["paid_value"] = 0;
                $data_insert["status"] = 1;
                $data_insert["paid_date"] = $process_date;
                $data_insert["receipt_id"] = $receipt_id;
                $data_insert["createdatetime"] = $process_date;
                $data_insert["updatedatetime"] = $process_date;
                $this->db->insert('coop_loan_refrain_history', $data_insert);
            }
        }

        // coop_loan_guarantee_compromise
        $data_insert = array();
        $data_insert["compromise_id"] = $compromise_id;
        $data_insert["loan_id"] = $new_loan_id;
        $data_insert["member_id"] = $member_id;
        $data_insert["type"] = 2;
        $data_insert["status"] = 1;
        $data_insert["other_debt"] = $_POST['new_loan_other_debt'];
        $data_insert["other_debt_blance"] = $_POST['new_loan_other_debt'];   
        $data_insert["interest_debt"] = $_POST["new_loan_interest_debt"];     
        $data_insert['created_at'] = $process_date;
        $data_insert['updated_at'] = $process_date;
        $this->db->insert('coop_loan_guarantee_compromise', $data_insert);

        //Create Guarantee Data
        $guarantee_persons = $this->db->select("*")
                                        ->from("coop_loan_guarantee_person")
                                        ->where("loan_id = '".$loan->id."'")
                                        ->get()->result_array();
        $gua_count = count($guarantee_persons);
        foreach($guarantee_persons as $index => $guarantee_person) {
            $amount = 0;
            if($index == 0) {
                $amount = (ceil(($_POST["new_loan_amount_balance"]/$gua_count) * 100) / 100);
            } else {
                $amount = (floor(($_POST["new_loan_amount_balance"]/$gua_count) * 100) / 100);
            }
            $data_insert = array();
            $data_insert["guarantee_person_id"] = $guarantee_person["guarantee_person_id"];
            $data_insert["guarantee_person_amount"] = $amount;
            $data_insert["guarantee_person_amount_balance"] = $amount;
            $data_insert["loan_id"] = $new_loan_id;
            $guarantor_inserts[] = $data_insert;
        }
        //Insert Guarantee Data
        if (!empty($guarantor_inserts)) {
            $this->db->insert_batch('coop_loan_guarantee_person', $guarantor_inserts);
        }

        echo "<script> document.location.href='".base_url(PROJECTPATH.'/compromise')."' </script>";
    }

    public function cal_member_loans_interest() {
        $member_id = $_GET["member_id"];
        $from_date_arr = explode("/",$_GET["from_date"]);
        $from_year = $from_date_arr[2] - 543;
        $from_date = $from_year."-".$from_date_arr[1]."-".$from_date_arr[0];
        $loan_id = $_GET["loan_id"];

        $loan = $this->db->select("*")->from("coop_loan")->where("id = '".$loan_id."'")->get()->row();

        $date1 = $from_date." 00:00:00.000";
        $date2 = date("Y-m-d");
        $loan_amount = $loan->loan_amount_balance;
        $loan_type = $loan->loan_type;
        $resign = $this->db->select("t2.cal_interest_af")
                            ->from("coop_mem_req_resign as t1")
                            ->join("coop_mem_resign_cause as t2", "t1.resign_cause_id = t2.resign_cause_id","inner")
                            ->where("t1.member_id = '".$member_id."' AND t1.req_resign_status = '1'")
                            ->get()->row();
        if(!empty($resign->cal_interest_af)) {
            $interest_loan = 0;
        } else {
            $terms = $this->db->select("*")->from("coop_term_of_loan")->where("type_id = '{$loan_type}' AND start_date between '{$date1}' AND '{$date2}'")->order_by("start_date")->get()->result_array();
            if(!empty($terms)) {
                $term_count = count($terms);
                //Cal first term
                $interest_loan = $this->loan_libraries->calc_interest_loan($loan_amount, $loan_id, $date1,$terms[0]["start_date"]);
                //Cal rest term
                foreach($terms as $index => $term) {
                    $date1 = $term["start_date"];
                    $last_date_cal = $index+1 < $term_count ? $terms[$index+1]["start_date"] : $date2;
                    $interest_loan += $this->loan_libraries->calc_interest_loan($loan_amount, $loan_id, $date1, $last_date_cal);
                }
            } else {
                $interest_loan = $this->loan_libraries->calc_interest_loan($loan_amount, $loan_id, $date1, $date2);
            }
        }
        $interest = round($interest_loan);

        //Get debt interest
        $debt = $this->db->select("sum(balance) as sum_balance")
                            ->from("coop_mem_resign_non_pay")
                            ->where("loan_id = '".$loan_id."'")
                            ->get()->row();
        if(!empty($debt->sum_balance)) {
            $interest += $debt->sum_balance;
        } else {
            $debt = $this->db->select("sum(non_pay_amount_balance) as sum_balance")
                                ->from("coop_non_pay_detail")
                                ->where("loan_id = '".$loan_id."' AND pay_type = 'interest'")
                                ->get()->row();
            $interest += $debt->sum_balance;
        }

        echo $interest;
    }

    public function force_summonth($summonth,$period){
        if($period=='1'){
            $summonth = $summonth-1;
        }else{
            $summonth = $summonth;
        }
        return $summonth;
    }
    public function compromise_cal_period() {
        echo json_encode($this->get_cal_period($_POST));
    }

    public function get_cal_period($data) {
        $interest = (double)$data["interest"];
		$loan = (double)$data["loan"];
		$pay_type = $data["pay_type"];
		$period = ($data["period_type"] == '1')? $data["period"]: $data["period_amount_bath"];
		$day = (double)$data["day"];
		$month = (double)$data["month"];
		$year  = (double)$data["year"] - 543;
        $period_type= (double)$data["period_type"];

        $pay_period = $loan / $period;
		$a = ceil($pay_period/1)*1;
		$daydiff = date('t') - $day;

        if($period_type == '1' && $pay_type=='2'){
			$total_per_period = $loan/$period;
			$date_start = ($year-543)."-".$month."-".$day;
			$date_period_1 = date('Y-m-t',strtotime('+1 month',strtotime($date_start)));
			$diff = date_diff(date_create($date_start),date_create($date_period_1));
			$date_count = $diff->format("%a");
			$date_count = 31;
			$interest_period_1 = ((($loan*$interest)/100)/365)*$date_count;
            $per_period = ($loan * ( ($interest/100) / 12 ))/( 1-pow(1/(1+( ($interest/100) /12)),$period));
            if($period_old == $period){
                $period = $money_period_2;
            }else{
                $period = $per_period;
            }
            $period_type = 2;
        }

        $loan_remain = $loan;
        $is_last = FALSE;
        $total_loan_pri = 0;
        $total_loan_int = 0;
        $total_loan_pay = 0;
        $d = $period - 1;

        $results = array();
        for ($i=1; $i <= $period; $i++) {
            if($loan_remain <= 0 ){ break; }
            if ($period_type == 1) {
                if ($month > 12) {
                    $month = 1;
                    $year += 1;
                }
                $loan_pri = ceil($a/1)*1;
                $nummonth = cal_days_in_month(CAL_GREGORIAN, $month , $year);
                $summonth = $nummonth;
                $daydiff = 31 - $day;
                if ($i == 1) {
                    if ($daydiff >= 0) {
                        $month += 1;
                        if ($month > 12) {
                                $month = 1;
                                $year += 1;
                        }
                        $nummonth = cal_days_in_month(CAL_GREGORIAN, $month , $year);
                        $summonth = $nummonth;
                        $summonth = $daydiff + 31;
                    }
                }
                $summonth = $this->force_summonth($summonth,$i);
                $loan_int = round($loan_remain * ($interest / (365 / $summonth)) / 100);
                $loan_pri = $loan_pri - $loan_int;
                if($loan_pri < 0){
                    $loan_pri = 0;
                }
                $loan_pay = $loan_pri + $loan_int;
                $loan_remain -= ceil($loan_pri/1)*1;
            } else if ($period_type == 2) {
                if ($month > 12) {
                    $month = 1;
                    $year += 1;
                }
                $nummonth = cal_days_in_month(CAL_GREGORIAN, $month , $year);
                $summonth = $nummonth;
                $daydiff = 31 - $day;
                if ($i == 1) {
                    if ($daydiff >= 0) {
                        $month += 1;
                        if ($month > 12) {
                            $month = 1;
                            $year += 1;
                        }
                        $nummonth = cal_days_in_month(CAL_GREGORIAN, $month , $year);
                        $summonth = $nummonth;
                        $summonth = $daydiff + 31;
                    }
                }
                $summonth = $this->force_summonth($summonth,$i);
                $loan_pri = ceil($period/1)*1;
                $loan_int = round($loan_remain * ($interest / (365 / $summonth)) / 100);
                $loan_pri = $loan_pri - $loan_int;
                if($loan_pri < 0){
                    $loan_pri = 0;
                }
                $loan_pay = $loan_pri + $loan_int;
                $loan_remain -= ceil($loan_pri/1)*1;
            }

            if($loan_remain <= 0) {
                $loan_pri += $loan_remain;
                $loan_pay = $loan_pri + $loan_int;
                $loan_remain = 0;
                $count = $count + 1;
            }

            $sumloan = $loan_remain + $loan_pri;
            $sumloanarr[] = $loan_remain + $loan_pri;
            $sumint[] = $loan_int;

            if ($i == $period) {
                $loan_pri = $sumloanarr[$d];
                $loan_pay = $loan_pri + $loan_int;
            }

            $total_loan_int += $loan_int;
            $total_loan_pri += $loan_pri;
            $total_loan_pay += $loan_pay;

            $result = array();
            $result["interest"] = $loan_int;
            $result["principal_payment"] = $loan_pri;
            $result["total_paid_per_month"] = $loan_pay;
            $result["date_period"] = $year."-".sprintf('%02d',$month)."-".$nummonth;
            $result["date_count"] = $summonth;
            $result["outstanding_balance"] = $sumloan;
            $results["periods"][] = $result;
            $month++;
        }
        $results["total_loan_int"] = $total_loan_int;
        $results["total_loan_pri"] = $total_loan_pri;
        $results["total_loan_pay"] = $total_loan_pay;
        return $results;
    }

    public function fund_support_payment() {
        $arr_data = array();

        $where = "t1.status = 1 AND (t1.fund_support is not null OR t1.fund_support_interest is not null OR t1.fund_support_percent is not null)";
        $loanee_where = "";
        if(!empty($_GET["member_id"])) {
            if($_GET["type"] == 1) {
                $loanee_where .= " AND t4.member_id = '".$_GET["member_id"]."'";
            } else {
                $where .= " AND t1.member_id = '".$_GET["member_id"]."'";
            }
        }
        $x=0;
		$join_arr[$x]['table'] = 'coop_finance_transaction as t2';
		$join_arr[$x]['condition'] = 't1.loan_id = t2.loan_id AND t2.member_id != "FSG001"';
		$join_arr[$x]['type'] = 'inner';
		$x++;
		$join_arr[$x]['table'] = 'coop_loan_fund_support_receipt as t3';
		$join_arr[$x]['condition'] = 't2.receipt_id = t3.member_receipt_id';
        $join_arr[$x]['type'] = 'left';
        $x++;
        $join_arr[$x]['table'] = 'coop_loan_compromise as t4';
		$join_arr[$x]['condition'] = 't1.compromise_id = t4.id'.$loanee_where;
        $join_arr[$x]['type'] = 'inner';

        $this->paginater_all->type(DB_TYPE);
        $this->paginater_all->select('t1.compromise_id,
                                        t1.loan_id,
                                        t1.member_id,
                                        t2.receipt_id,
                                        t2.payment_date,
                                        t3.fund_receipt_id,
                                        t4.member_id as loanee_member_id
                                    ');
        $this->paginater_all->main_table('coop_loan_guarantee_compromise as t1');
        $this->paginater_all->page_now($_GET["page"]);
        $this->paginater_all->per_page(20);
        $this->paginater_all->page_link_limit(20);
        $this->paginater_all->where($where);
        $this->paginater_all->order_by('t2.payment_date DESC');
        $this->paginater_all->group_by("t2.loan_id, t2.receipt_id");
        $this->paginater_all->join_arr($join_arr);
        $row = $this->paginater_all->paginater_process();

        $paging = $this->pagination_center->paginating($row['page'], $row['num_rows'], $row['per_page'], $row['page_link_limit'],$_GET);

        foreach($row["data"] as $index => $data) {
            $loan = $this->db->select("*")
                                ->from("coop_loan as t1")
                                ->where("t1.id = '".$data["loan_id"]."'")
                                ->get()->row();
            $row["data"][$index]["contract_number"] = $loan->contract_number;

            $member = $this->db->select("t1.firstname_th, t1.lastname_th, t2.prename_full")
                                ->from("coop_mem_apply as t1")
                                ->join("coop_prename as t2", "t1.prename_id = t2.prename_id", "left")
                                ->where("member_id = '".$data["member_id"]."'")
                                ->get()->row();
            $row["data"][$index]["prename"] = $member->prename_full;
            $row["data"][$index]["firstname"] = $member->firstname_th;
            $row["data"][$index]["lastname"] = $member->lastname_th;

            $loanee = $this->db->select("t1.firstname_th, t1.lastname_th, t2.prename_full")
                                ->from("coop_mem_apply as t1")
                                ->join("coop_prename as t2", "t1.prename_id = t2.prename_id", "left")
                                ->where("member_id = '".$data["loanee_member_id"]."'")
                                ->get()->row();
            $row["data"][$index]["loanee_prename"] = $loanee->prename_full;
            $row["data"][$index]["loanee_firstname"] = $loanee->firstname_th;
            $row["data"][$index]["loanee_lastname"] = $loanee->lastname_th;

            if(!empty($data["fund_receipt_id"])) {
                $receipt = $this->db->select("t1.receipt_datetime, t2.user_name")
                                    ->from("coop_receipt as t1")
                                    ->join("coop_user as t2", "t1.admin_id = t2.user_id", "left")
                                    ->where("t1.receipt_id = '".$data["fund_receipt_id"]."'")
                                    ->get()->row();
                $row["data"][$index]["receipt_datetime"] = $receipt->receipt_datetime;
                $row["data"][$index]["user_name"] = $receipt->user_name;
            }
        }
        $arr_data["page_start"] = $row["page_start"];
        $arr_data["datas"] = $row["data"];
        $arr_data["paging"] = $paging;

        $this->libraries->template('compromise/fund_support_payment',$arr_data);
    }

    public function get_compromise_payment_detail() {
        $receipt_id = $_GET["receipt_id"];

        $where = "receipt_id = '".$receipt_id."'";
        $transaction = $this->db->select("
                                            t1.loan_id,
                                            SUM(t1.principal_payment) as principal,
                                            SUM(t1.interest) as interest,
                                            SUM(t1.loan_interest_remain) as interest_remain,
                                            t2.fund_support_percent,
                                            t2.fund_support,
                                            t2.fund_support_interest,
                                            t2.fund_support_balance,
                                            t2.fund_support_interest_balance,
                                            t3.contract_number
                                        ")
                                ->from("coop_finance_transaction as t1")
                                ->join("coop_loan_guarantee_compromise as t2", "t1.loan_id = t2.loan_id", "inner")
                                ->join("coop_loan as t3", "t1.loan_id = t3.id")
                                ->where($where)
                                ->group_by("t1.loan_id")
                                ->get()->result_array();
        echo json_encode($transaction);
    }

    public function save_compromise_pay() {
        $member_id = "FSG001";//Member Id for Fund
        $process_timestamp = date('Y-m-d H:i:s');
        foreach($_POST["loan_ids"] as $index => $loan_id) {
            $loan_id = "FUND_".$loan_id;
            $principal = $_POST["principals"][$index];
            $interest = $_POST["interests"][$index];
            $interest_debt = $_POST["interest_debts"][$index];
            if(!empty($principal) || !empty($interest) || !empty($interest_debt)) {
                //Get loan data
                $where = "t1.id = '".$loan_id."'";
                $loan = $this->db->select("t1.contract_number, t2.id, t2.fund_support_balance, t2.fund_support_interest_balance, t2.compromise_id")
                                    ->from("coop_loan as t1")
                                    ->join("coop_loan_guarantee_compromise as t2", "t1.id = t2.loan_id AND t2.status = 1", "inner")
                                    ->where($where)
                                    ->get()->row();

                //Calculate fund support balance
                $fund_support_balance = $loan->fund_support_balance - $principal;
                $fund_support_interest_balance = $loan->fund_support_interest_balance - ($interest + $interest_debt);

                //Generate receipt
                $yymm = (date("Y")+543).date("m");
                $mm = date("m");
                $yy = (date("Y")+543);
                $yy_full = (date("Y")+543);
                $yy = substr($yy,2);
                $row = $this->db->select('*')
                                    ->from('coop_receipt')
                                    ->where("receipt_id LIKE '".$yy_full.$mm."%'")
                                    ->order_by("receipt_id DESC")
                                    ->limit(1)
                                    ->get()->result_array();
                
                if(!empty($row)) {
                    $id = (int) substr($row[0]["receipt_id"], 6);
                    $receipt_number = $yymm.sprintf("%06d", $id + 1);
                } else {
                    $receipt_number = $yymm."000001";
                }
                $order_by_id =  $row[0]["order_by"]+1 ; 

                $data_insert = array();
                $data_insert['receipt_id'] = $receipt_number;
                $data_insert['member_id'] = $member_id;
                $data_insert['order_by'] = $order_by_id;
                $data_insert['sumcount'] = $principal + $interest + $interest_debt;
                $data_insert['receipt_datetime'] = $process_timestamp;
                $data_insert['admin_id'] = $_SESSION['USER_ID'];
                $data_insert['pay_type'] = 1;
                $this->db->insert('coop_receipt', $data_insert);

                $data_insert = array();
                $data_insert['receipt_id'] = $receipt_number;
                $data_insert['receipt_list'] = 15;//Account List for loan transaction
                $data_insert['receipt_count'] = $principal + $interest + $interest_debt;
                $this->db->insert('coop_receipt_detail', $data_insert);

                //Create Transaction
                $data_insert = array();
                $data_insert['member_id'] = $member_id;
                $data_insert['receipt_id'] = $receipt_number;
                $data_insert['loan_id'] = $loan_id;
                $data_insert['deduct_type'] = "all";
                $data_insert['account_list_id'] = 15;
                $data_insert['principal_payment'] = $principal;
                $data_insert['interest'] = $interest;
                $data_insert['loan_interest_remain'] = $interest_debt;
                $data_insert['loan_amount_balance'] = $fund_support_balance;
                $data_insert['total_amount'] = $principal + $interest + $interest_debt;
                $data_insert['payment_date'] = $process_timestamp;
                $data_insert['createdatetime'] = $process_timestamp;
                $data_insert['transaction_text'] = "ชำระเงินกู้เลขที่สัญญา ".$loan->contract_number;
                $this->db->insert('coop_finance_transaction', $data_insert);

                //Relate member Transaction to Fund transaction
                $data_insert = array();
                $data_insert["loan_id"] = $loan_id;
                $data_insert["member_id"] = $member_id;
                $data_insert["member_receipt_id"] = $_POST["receipt_id"];
                $data_insert["fund_receipt_id"] = $receipt_number;
                $data_insert["created_at"] = $process_timestamp;
                $data_insert["updated_at"] = $process_timestamp;
                $this->db->insert('coop_loan_fund_support_receipt', $data_insert);

                //Update fund support balance
                $data_update = array();
                $data_update['fund_support_balance'] = $fund_support_balance;
                $data_update['fund_support_interest_balance'] = $fund_support_interest_balance;
                $this->db->where('id',$loan->id);
                $this->db->update('coop_loan_guarantee_compromise',$data_update);

                //update fund support transaction
                $last_support_transaction = $this->db->select("*")->from("coop_loan_fund_balance_transaction")->where("sub_compromise_id = '".$loan->id."'")->order_by("payment_date DESC, id DESC")->get()->row();
                $data_insert = array();
                $data_insert["compromise_id"] = $last_support_transaction->compromise_id;
                $data_insert["sub_compromise_id"] = $last_support_transaction->sub_compromise_id;
                $data_insert["receipt_id"] = $receipt_number;
                $data_insert["principal"] = $fund_support_balance;
                $data_insert["interest"] = $fund_support_interest_balance;
                $data_insert["payment_date"] = $process_timestamp;
                $data_insert["createdatetime"] = $process_timestamp;
                $this->db->insert('coop_loan_fund_balance_transaction', $data_insert);
            }
        }

        $this->center_function->toast("บันทึกข้อมูลเรียบร้อยแล้ว");
        echo "<script> document.location.href='".base_url(PROJECTPATH.'/compromise/fund_support_payment')."' </script>";
    }

    public function get_member_fund_supports_json() {
        $member_id = (int) $_GET["member_id"];
        if($_GET["type"] == 1) {
            $compromises = $this->db->select("t1.firstname_th, t1.lastname_th, t1.member_id, t3.prename_full, t2.id as compromise_id, t2.fund_support, t2.fund_support_interest")
                                    ->from("coop_mem_apply as t1")
                                    ->join("coop_loan_compromise as t2", "t1.member_id = t2.member_id AND t2.status = 1", "left")
                                    ->join("coop_prename as t3", "t1.prename_id = t3.prename_id", "left")
                                    ->where("t1.member_id = ".$member_id)
                                    ->get()->result_array();
        } else {
            $compromises = $this->db->select("t1.firstname_th, t1.lastname_th, t1.member_id, t3.prename_full, t2.compromise_id, t2.fund_support, t2.fund_support_interest")
                                    ->from("coop_mem_apply as t1")
                                    ->join("coop_loan_guarantee_compromise as t2", "t1.member_id = t2.member_id AND t2.status = 1", "left")
                                    ->join("coop_prename as t3", "t1.prename_id = t3.prename_id", "left")
                                    ->where("t1.member_id = ".$member_id)
                                    ->get()->result_array();
        }

        echo json_encode($compromises);
    }
}
