<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cashier extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
		$this->load->model("Finance_libraries", "Finance_libraries");
    }

    public function index()
    {
        $arr_data = array();
        if ($this->input->get('member_id') != '') {
            $member_id = $this->input->get('member_id');
        } else {
            $member_id = '';
        }
        $arr_data['member_id'] = $member_id;
		if($this->input->get('fix_date')!=""){
			$tmp_date = explode("/", $this->input->get('fix_date'));
			$date = ($tmp_date[2]-543)."-".$tmp_date[1]."-".$tmp_date[0];
		}else{
			$date = date('Y-m-d H:i:s');
		}
		$receipt_number = $this->receipt_model->generate_receipt($date);
        $arr_data['receipt_number'] = $receipt_number;
        $this->db->select('*');
        $this->db->from('coop_account_list');
        $this->db->where("account_type = '1'");
        $row = $this->db->get()->result_array();
        $arr_data['account_list'] = $row;
        $this->db->select('*');
        $this->db->from('coop_loan_atm_setting');
        $row = $this->db->get()->result_array();
        $loan_atm_setting = @$row[0];
        if ($member_id != '') {
            $this->db->select('*');
            $this->db->from('coop_mem_apply');
            $this->db->where("member_id = '" . $member_id . "'");
            $row = $this->db->get()->result_array();
            $arr_data['row_member'] = $row[0];
            $this->db->select(array(
                'coop_loan.id',
                "concat(coop_loan_name.loan_name,' ',coop_loan.contract_number) as `contract_number`",
                'coop_loan.loan_amount_balance',
                'coop_loan.interest_per_year',
                'coop_loan_transfer.date_transfer',
                'date_last_interest',
                'coop_loan.loan_type'
            ));
            $this->db->from('coop_loan');
            $this->db->join('coop_loan_transfer', 'coop_loan_transfer.loan_id = coop_loan.id', 'inner');
            $this->db->join('coop_loan_name', 'coop_loan.loan_type=coop_loan_name.loan_name_id');
            $this->db->where("coop_loan.member_id = '" . $member_id . "' AND coop_loan.loan_status in (1,8) AND coop_loan_transfer.transfer_status IN ('0','1')");
            $row = $this->db->get()->result_array();
            //echo $this->db->last_query();exit;
            foreach ($row as $key => $value) {
                $loan_amount = $value['loan_amount_balance'];//เงินกู้
                $loan_type = $value['loan_type'];//ประเภทเงินกู้ใช้หา เรทดอกเบี้ย
                $loan_id = $value['id'];//ใช้หาเรทดอกเบี้ยใหม่ 26/5/2562
                $date1 = $value['date_last_interest'];//วันคิดดอกเบี้ยล่าสุด
                $date2 = date("Y-m-d");
                if(isset($_GET['fix_date'])){
                    $tmp = explode("/", $_GET['fix_date']);
                    $date2 = ($tmp[2]-543)."-".$tmp[1]."-".$tmp[0];
                }

                $interest_loan = 0;
                $interest_loan = $this->loan_libraries->calc_interest_loan($loan_amount, $loan_id, $date1, $date2);
                $row[$key]['interest'] = round($interest_loan, 0);

                //Get loan interest non pay
                $loan_interest_remain = $this->db->select("loan_id, SUM(non_pay_amount_balance) as sum")
                    ->from("coop_non_pay_detail")
                    ->where("loan_id = '" . $value['id'] . "' AND pay_type = 'interest'")
                    ->get()->row();
                if (!empty($loan_interest_remain)) {
                    // $loan_interest_remain_total += $loan_interest_remain->sum;
                    $loan_interest_remain_total = @$loan_interest_remain->sum;
                }
                //ข้อมูลดอกเบี้ยค้างชำระสะสม
                $loan_interest_debt = $this->db->select("loan_id, SUM(interest_balance) AS sum_interest_balance")
                    ->from("coop_loan_interest_debt")
                    ->where("loan_id = '" . $value['id'] . "' AND interest_status = 0 AND member_id = '" . $member_id . "'")
                    ->get()->row();
                //echo $this->db->last_query(); echo '<br>';
                if (!empty($loan_interest_debt)) {
                    $loan_interest_debt_total = @$loan_interest_debt->sum_interest_balance;
                }
                $row[$key]['loan_interest_debt_total'] = $loan_interest_debt_total;
                $row[$key]['interest_non_pay'] = @$loan_interest_remain_total + @$loan_interest_debt_total;
                //echo '<pre>'; print_r($value); echo '</pre>';
                //Get refrain if exist
                $year = date("Y") + 543;
                $month = date("m");
                $loan_refrains = $this->db->select("refrain_loan_id, refrain_type")
                    ->from("coop_refrain_loan")
                    ->where("loan_id = '" . $value['id'] . "' AND status != 2 AND (year_start < " . $year . " || (year_start = " . $year . " AND month_start <= " . $month . "))
													 AND ((year_end > " . $year . " || (year_end = " . $year . " AND month_end >= " . $month . ") || period_type = 2))")
                    ->get()->result_array();
                $loan_refrain = array();
                foreach ($loan_refrains as $refrain_row) {
                    if ($refrain_row["refrain_type"] == 1) {
                        $loan_refrain["principal"] = $refrain_row["refrain_loan_id"];
                    } else if ($refrain_row["refrain_type"] == 2) {
                        $loan_refrain["interest"] = $refrain_row["refrain_loan_id"];
                    } else if ($refrain_row["refrain_type"] == 3) {
                        $loan_refrain["principal"] = $refrain_row["refrain_loan_id"];
                        $loan_refrain["interest"] = $refrain_row["refrain_loan_id"];
                    }
                }
                $row[$key]['refrain'] = $loan_refrain;
            }
            $arr_data['row_loan'] = $row;
            // echo"<pre>";print_r($arr_data['row_loan']);exit;
            $arr_data['row_loan_atm'] = array();
            $compomise_debts = $this->db->select("coop_loan_guarantee_compromise.other_debt_blance, coop_loan.contract_number, coop_loan_guarantee_compromise.id")
                ->from("coop_loan_guarantee_compromise")
                ->join("coop_loan", "coop_loan_guarantee_compromise.loan_id = coop_loan.id AND coop_loan.loan_status = 1")
                ->where("coop_loan_guarantee_compromise.member_id = '" . $member_id . "' AND status = 1")
                ->group_by("coop_loan_guarantee_compromise.loan_id")
                ->get()->result_array();
            $arr_data['compomise_debts'] = $compomise_debts;
            //Cremation
            $cremation_debts = $this->db->select("YEAR(t2.created_at) as year")
                ->from("coop_member_cremation as t1")
                ->join("coop_cremation_advance_payment_transaction as t2", "t1.member_cremation_id = t2.member_cremation_id", "inner")
                ->where("t1.member_id = '" . $member_id . "' AND t2.status = 1 AND t2.type = 'CTAP'")
                ->group_by("YEAR(t2.created_at), t2.member_cremation_id")
                ->order_by("t2.created_at")
                ->get()->result_array();
            $arr_data['cremation_debts'] = $cremation_debts;
        } else {
            $arr_data['row_member'] = array();
            $arr_data['row_loan'] = array();
            $arr_data['row_loan_atm'] = array();
        }

        $arr_data['maco_account'] = $this->db->get_where("coop_maco_account", array(
			"mem_id" => $member_id,
			"account_status" => "0",
        ))->result_array();
        
        $this->libraries->template('cashier/index', $arr_data);
    }

    function cal_receipt()
    {
        $data = $this->input->post();
        $principal_payment = 0;
        $interest = 0;
        if ($data['loan_id'] != '') {
            if (date('d') >= '21') {
                $principal_payment = $data['amount'];
                $interest = 0;

            } else {
                $this->db->select(
                    array(
                        'coop_loan.id as loan_id',
                        'coop_loan.contract_number',
                        'coop_loan.loan_amount_balance',
                        'coop_loan.interest_per_year',
                        'coop_loan_type.loan_type',
                        'coop_loan_transfer.date_transfer'
                    )
                );
                $this->db->from('coop_loan');
                $this->db->join('coop_loan_type', 'coop_loan_type.id = coop_loan.loan_type', 'inner');
                $this->db->join('coop_loan_transfer', 'coop_loan_transfer.loan_id = coop_loan.id', 'inner');
                $this->db->where("coop_loan.id = '" . $data['loan_id'] . "'");
                $row = $this->db->get()->result_array();
                $row_normal_loan = @$row[0];

                $date_interesting = date('Y-m-d');

                $this->db->select('payment_date');
                $this->db->from('coop_finance_transaction');
                $this->db->where("loan_id = '" . $row_normal_loan['loan_id'] . "'");
                $this->db->order_by("payment_date DESC");
                $this->db->limit(1);
                $row = $this->db->get()->result_array();
                $row_date_prev_paid = $row[0];

                $date_prev_paid = $row_date_prev_paid['payment_date'] != '' ? $row_date_prev_paid['payment_date'] : $row_normal_loan['date_transfer'];
                $diff = date_diff(date_create($date_prev_paid), date_create($date_interesting));
                $date_count = $diff->format("%a");
                $date_count = $date_count + 1;

                $interest = ((($row_normal_loan['loan_amount_balance'] * $row_normal_loan['interest_per_year']) / 100) / 365) * $date_count;
                $interest = round($interest);
                $principal_payment = $data['amount'] - $interest;
            }
        } else {
            $principal_payment = $data['amount'];
            $interest = 0;
        }
        $data_arr = array();
        if ($data['amount'] < $interest) {
            $data_arr['result'] = 'error';
            $data_arr['error_msg'] = 'กรุณากรอกจำนวนเงินมากกว่าจำนวนดอกเบี้ย';
        } else {
            $data_arr['account_list'] = $data['account_list'];
            $data_arr['account_list_text'] = $data['account_list_text'];
            $data_arr['loan_id'] = $data['loan_id'];
            $data_arr['amount'] = $data['amount'];
            $data_arr['principal_payment'] = $principal_payment;
            $data_arr['interest'] = $interest;
        }
        echo json_encode($data_arr);
        exit;
    }

    function save(){
        // var_dump($_POST);exit;
        $sum_interest = 0;
        $data_post = $this->input->post();

        if($data_post['fix_date']!=""){
            $tmp_date = explode("/", $data_post['fix_date']);
            $date = ($tmp_date[2]-543)."-".$tmp_date[1]."-".$tmp_date[0];
        }else{
            $date = date('Y-m-d H:i:s');
        }

		if($data_post["pay_type"]=="cash"){
			$data_post["pay_type"] = 0;
		}else if($data_post["pay_type"]=="transfer"){
			$data_post["pay_type"] = 1;
		}else if($data_post["pay_type"]=="cheque") {
			$data_post["pay_type"] = 1;
		}else if($data_post["pay_type"]=="railway"){
			$data_post["pay_type"] = 2;
		}else{
			$data_post["pay_type"] = 3;
		}

		$pay_type = $data_post["pay_type"];
		$receipt_number = $this->receipt_model->generate_receipt($date, $pay_type);
        $text = $this->receipt_model->getReceiptByType($pay_type, $date)['prefix'];
		$order_by_id = $row[0]["order_by"] + 1;
        $data_insert = array();
        $data_insert['receipt_id'] = $receipt_number;
        $data_insert['receipt_code'] = $text;
        $data_insert['member_id'] = $data_post['member_id'];
        $data_insert['order_by'] = @$order_by_id;
        $total = 0;
        foreach ($data_post['amount'] as $key => $value) {
            $total += $value;
        }

        $data_insert['sumcount'] = number_format($total, 2, '.', '');
        $data_insert['receipt_datetime'] = $date;
        $data_insert['admin_id'] = $_SESSION['USER_ID'];
        $data_insert['pay_type'] = $data_post["pay_type"];
        $data_insert['cheque_no'] = $data_post["cheque_no"];
        $data_insert['bank_id'] = $data_post["bank_id"];
        $data_insert['branch_code'] = $data_post["branch_code"];
        $data_insert['local_account_id'] = $data_post["local_account_id"];
        $data_insert['other'] = $data_post["other"];
        $data_insert['transfer_other'] = $data_post["transfer_other"];
        $this->db->insert('coop_receipt', $data_insert);

        if ($data_post["pay_type"] == 0) {
            $data_post_pay_type = '0';
        } else if($data_post["pay_type"] == 1){
            $data_post_pay_type = '1';
        }else{
            $data_post_pay_type = '2';
        }
        $process = 'cashier';
        $money = $total;
        $ref = $receipt_number;;
        $match_type = 'main';
        $match_id = '1';
        if ($data_post_pay_type == 1) {
            $statement = 'credit';
        } else {
            $statement = 'debit';
        }
        $data_process[] = $this->account_transaction->set_data_account_trancetion_detail($match_id, $statement, $match_type, $ref, $money, $process);
        $loan_amount_balance = 0;
        foreach ($data_post['account_list'] as $key => $value) {
            $this->db->select(array('*'));
            $this->db->from('coop_account_list');
            $this->db->where("account_id = '" . @$data_post['account_list'][$key] . "'");
            $this->db->limit(1);
            $row_account_list = $this->db->get()->result_array();
            $row_account_list = @$row_account_list[0];
            $data_insert = array();
            $data_insert['receipt_id'] = $receipt_number;
            $data_insert['receipt_list'] = $data_post['account_list'][$key];
            $data_insert['receipt_count'] = number_format($data_post['amount'][$key], 2, '.', '');
            $this->db->insert('coop_receipt_detail', $data_insert);
            if ($data_post['loan_id'][$key] != '') {
                $this->db->select(array('loan_amount_balance', 'contract_number'));
                $this->db->from('coop_loan');
                $this->db->where("id = '" . $data_post['loan_id'][$key] . "'");
                $row = $this->db->get()->result_array();
                $row_loan = @$row[0];
                $transaction_text = @$row_account_list['account_list'] . "เลขที่สัญญา " . @$row_loan['contract_number'];
                $loan_amount_balance = @$row_loan['loan_amount_balance'] - $data_post['principal_payment'][$key];
                if ($loan_amount_balance <= 0) {
                    $loan_amount_balance = 0;
                    $data_insert = array();
                    $data_insert['loan_amount_balance'] = $loan_amount_balance;
                    $data_insert['loan_status'] = '4';
                    $this->db->where('id', $data_post['loan_id'][$key]);
                    $this->db->update('coop_loan', $data_insert);
                } else {
                    $data_insert = array();
                    $data_insert['loan_amount_balance'] = number_format($loan_amount_balance, 2, '.', '');
                    $this->db->where('id', $data_post['loan_id'][$key]);
                    $this->db->update('coop_loan', $data_insert);
                }
                $loan_transaction = array();
                $loan_transaction['loan_id'] = $data_post['loan_id'][$key];
                $loan_transaction['loan_amount_balance'] = $loan_amount_balance;
                $loan_transaction['transaction_datetime'] = $date;
                $loan_transaction['receipt_id'] = $receipt_number;
                $this->loan_libraries->loan_transaction($loan_transaction);
                //Non pay
                $non_pay_sum = $this->db->select("loan_id, sum(non_pay_amount_balance) as sum_amount_balance")
                    ->from("coop_non_pay_detail")
                    ->where("loan_id = '" . $data_post['loan_id'][$key] . "' AND pay_type = 'principal'")
                    ->get()->row();
                if ($non_pay_sum->sum_amount_balance > $loan_amount_balance) {
                    $cal_balance = $non_pay_sum->sum_amount_balance - $loan_amount_balance;
                    $non_pays = $this->db->select("t1.run_id, t1.non_pay_amount_balance, t1.non_pay_id")
                        ->from("coop_non_pay_detail as t1")
                        ->join("coop_non_pay as t2", "t1.non_pay_id = t2.non_pay_id", "inner")
                        ->where("t1.loan_id = '" . $data_post['loan_id'][$key] . "' AND pay_type = 'principal' AND t1.non_pay_amount_balance > 0")
                        ->order_by("t2.non_pay_year, t2.non_pay_month")
                        ->get()->result_array();
                    foreach ($non_pays as $non_pay) {
                        if ($cal_balance >= $non_pay["non_pay_amount_balance"]) {
                            $data_insert = array();
                            $data_insert['non_pay_amount_balance'] = 0;
                            $this->db->where('run_id', $non_pay['run_id']);
                            $this->db->update('coop_non_pay_detail', $data_insert);
                            $cal_balance -= $non_pay["non_pay_amount_balance"];
                        } else {
                            $data_insert = array();
                            $data_insert['non_pay_amount_balance'] = $non_pay["non_pay_amount_balance"] - $cal_balance;
                            $this->db->where('run_id', $non_pay['run_id']);
                            $this->db->update('coop_non_pay_detail', $data_insert);
                            $cal_balance = 0;
                        }
                        $non_pay_details = $this->db->select("sum(non_pay_amount_balance) as sum_balance")
                            ->from("coop_non_pay_detail")
                            ->where("non_pay_id = '" . $non_pay["non_pay_id"] . "'")
                            ->get()->row();
                        $data_insert = array();
                        $data_insert['non_pay_amount_balance'] = $non_pay_details->sum_balance;
                        if ($non_pay_details->sum_balance <= 0) {
                            $data_insert['non_pay_status'] = 2;
                        }
                        $this->db->where('non_pay_id', $non_pay['non_pay_id']);
                        $this->db->update('coop_non_pay', $data_insert);
                    }
                }
                if ($data_post['deduct_type'][$key] == 'all') {
                    $data_insert = array();
                    $data_insert['date_last_interest'] = $date;
                    $this->db->where('id', $data_post['loan_id'][$key]);
                    $this->db->update('coop_loan', $data_insert);
                }
                if (!empty($data_post["loan_interest_refrain"][$key])) {
                    $data_insert = array();
                    $data_insert["refrain_loan_id"] = $data_post["loan_interest_refrain"][$key];
                    $data_insert["member_id"] = $data_post['member_id'];
                    $data_insert["pay_type"] = "interest";
                    $data_insert["org_value"] = $data_post['interest_all'][$key];
                    $data_insert["paid_value"] = 0;
                    $data_insert["status"] = 1;
                    $data_insert["paid_date"] = $date;
                    $data_insert["receipt_id"] = $receipt_number;
                    $data_insert["createdatetime"] = $date;
                    $data_insert["updatedatetime"] = $date;
                    $this->db->insert('coop_loan_refrain_history', $data_insert);
                }
            } else if ($data_post['loan_atm_id'][$key] != '') {
                $this->db->select(array(
                    'loan_id',
                    'loan_amount_balance'
                ));
                $this->db->from('coop_loan_atm_detail');
                $this->db->where("loan_atm_id = '" . $data_post['loan_atm_id'][$key] . "' AND loan_status = '0'");
                $this->db->order_by('loan_id ASC');
                $row = $this->db->get()->result_array();
                $principal_payment = $data_post['principal_payment'][$key];
                foreach ($row as $key_atm => $value_atm) {
                    if ($principal_payment > 0) {
                        if ($principal_payment >= $value_atm['loan_amount_balance']) {
                            $data_insert = array();
                            $data_insert['loan_amount_balance'] = 0;
                            $data_insert['loan_status'] = '1';
                            $data_insert['date_last_pay'] = date('Y-m-d');
                            $this->db->where('loan_id', $value_atm['loan_id']);
                            $this->db->update('coop_loan_atm_detail', $data_insert);
                            $principal_payment = $principal_payment - $value_atm['loan_amount_balance'];
                        } else {
                            $data_insert = array();
                            $data_insert['loan_amount_balance'] = $value_atm['loan_amount_balance'] - $principal_payment;
                            $data_insert['date_last_pay'] = date('Y-m-d');
                            $this->db->where('loan_id', $value_atm['loan_id']);
                            $this->db->update('coop_loan_atm_detail', $data_insert);
                            $principal_payment = 0;
                        }
                    }
                }
                $this->db->select(array(
                    'total_amount_approve',
                    'total_amount_balance',
                    'contract_number'
                ));
                $this->db->from('coop_loan_atm');
                $this->db->where("loan_atm_id = '" . $data_post['loan_atm_id'][$key] . "'");
                $row = $this->db->get()->result_array();
                $row_loan_atm = $row[0];
                $transaction_text = @$row_account_list['account_list'] . "เลขที่สัญญา " . @$row_loan_atm['contract_number'];
                $total_amount_balance = $row_loan_atm['total_amount_balance'] + $data_post['principal_payment'][$key];
                $data_insert = array();
                $data_insert['total_amount_balance'] = $total_amount_balance;
                $this->db->where('loan_atm_id', $data_post['loan_atm_id'][$key]);
                $this->db->update('coop_loan_atm', $data_insert);
                $loan_amount_balance = $row_loan_atm['total_amount_approve'] - $total_amount_balance;
                $atm_transaction = array();
                $atm_transaction['loan_atm_id'] = $data_post['loan_atm_id'][$key];
                $atm_transaction['loan_amount_balance'] = $loan_amount_balance;
                $atm_transaction['transaction_datetime'] = $date;
                $atm_transaction['receipt_id'] = $receipt_number;
                $this->loan_libraries->atm_transaction($atm_transaction);
                if ($data_post['deduct_type'][$key] == 'all') {
                    $data_insert = array();
                    $data_insert['date_last_interest'] = $date;
                    $this->db->where('loan_atm_id', $data_post['loan_atm_id'][$key]);
                    $this->db->update('coop_loan_atm', $data_insert);
                }
                //Non pay
                $non_pay_sum = $this->db->select("loan_atm_id, sum(non_pay_amount_balance) as sum_amount_balance")
                    ->from("coop_non_pay_detail")
                    ->where("loan_atm_id = '" . $data_post['loan_atm_id'][$key] . "' AND pay_type = 'principal'")
                    ->get()->row();
                if ($non_pay_sum->sum_amount_balance > $loan_amount_balance) {
                    $cal_balance = $non_pay_sum->sum_amount_balance - $loan_amount_balance;
                    $non_pays = $this->db->select("t1.run_id, t1.non_pay_amount_balance, t1.non_pay_id")
                        ->from("coop_non_pay_detail as t1")
                        ->join("coop_non_pay as t2", "t1.non_pay_id = t2.non_pay_id", "inner")
                        ->where("t1.loan_atm_id = '" . $data_post['loan_atm_id'][$key] . "' AND pay_type = 'principal' AND t1.non_pay_amount_balance > 0")
                        ->order_by("t2.non_pay_year, t2.non_pay_month")
                        ->get()->result_array();
                    foreach ($non_pays as $non_pay) {
                        if ($cal_balance >= $non_pay["non_pay_amount_balance"]) {
                            $data_insert = array();
                            $data_insert['non_pay_amount_balance'] = 0;
                            $this->db->where('run_id', $non_pay['run_id']);
                            $this->db->update('coop_non_pay_detail', $data_insert);
                            $cal_balance -= $non_pay["non_pay_amount_balance"];
                        } else {
                            $data_insert = array();
                            $data_insert['non_pay_amount_balance'] = $non_pay["non_pay_amount_balance"] - $cal_balance;
                            $this->db->where('run_id', $non_pay['run_id']);
                            $this->db->update('coop_non_pay_detail', $data_insert);
                            $cal_balance = 0;
                        }
                        $non_pay_details = $this->db->select("sum(non_pay_amount_balance) as sum_balance")
                            ->from("coop_non_pay_detail")
                            ->where("non_pay_id = '" . $non_pay["non_pay_id"] . "'")
                            ->get()->row();
                        $data_insert = array();
                        $data_insert['non_pay_amount_balance'] = $non_pay_details->sum_balance;
                        if ($non_pay_details->sum_balance <= 0) {
                            $data_insert['non_pay_status'] = 2;
                        }
                        $this->db->where('non_pay_id', $non_pay['non_pay_id']);
                        $this->db->update('coop_non_pay', $data_insert);
                    }
                }
            } elseif ($value == 47) {
                $total_amount = $data_post['amount'][$key];
                $compromises = $this->db->select("*")
                    ->from("coop_loan_guarantee_compromise")
                    ->where("id = '" . $data_post['compromise_id'][$key] . "'")
                    ->get()->result_array();
                foreach ($compromises as $compromise) {
                    if ($total_amount >= $compromise['other_debt_blance']) {
                        $total_amount -= $compromise['other_debt_blance'];
                        $data_insert = array();
                        $data_insert['other_debt_blance'] = 0;
                        $this->db->where('id', $compromise['id']);
                        $this->db->update('coop_loan_guarantee_compromise', $data_insert);
                    } else {
                        $debt_left = $compromise['other_debt_blance'] - $total_amount;
                        $data_insert = array();
                        $data_insert['other_debt_blance'] = $debt_left;
                        $this->db->where('id', $compromise['id']);
                        $this->db->update('coop_loan_guarantee_compromise', $data_insert);
                        $total_amount = 0;
                    }
                }
                $compromise_detail = $this->db->select("*")
                    ->from("coop_loan_guarantee_compromise")
                    ->where("compromise_id = '" . $data_post['compromise_id'][$key] . "'")
                    ->get()->row();
                $data_post['loan_id'][$key] = $compromise_detail->loan_id;
                $transaction_text = $row_account_list['account_list'];
            }else if($value == 46) {
				$transaction_text = @$data_post['other_text_desc'][$key];
			} else {
                $transaction_text = @$row_account_list['account_list'];
            }
            $loan_interest_now = @$data_post['interest'][$key]; //ดอกเบี้ยที่จ่าย
            $loan_interest_remain = 0; //ดอกเบี้ยคงเหลือค้างชำระ
            //หาสถานะของสมาชิก
            $this->db->select('mem_type,member_status');
            $this->db->from('coop_mem_apply');
            $this->db->where("member_id = '" . $data_post['member_id'] . "'");
            $this->db->limit(1);
            $row_mem_apply = $this->db->get()->result_array();
            $mem_type = @$row_mem_apply[0]['mem_type'];
            //echo '<pre>'; print_r($data_post); echo '</pre>';
            //@start บันทึกข้อมูลดอกเบี้ยค้างชำระสะสม
            if (($mem_type == '4' || $mem_type == '5' || $mem_type == '7') && empty($data_post["loan_interest_refrain"][$key])) {
                if (@$data_post['interest_all'][$key] != 0 && @$data_post['interest_all'][$key] != @$data_post['interest'][$key]) {
                    $interest_balance = @$data_post['interest_all'][$key];
                    $data_insert = array();
                    $data_insert['member_id'] = $data_post['member_id'];
                    $data_insert['loan_id'] = $data_post['loan_id'][$key];
                    $data_insert['loan_atm_id'] = $data_post['loan_atm_id'][$key];
                    $data_insert['interest_total'] = @$data_post['interest_all'][$key]; //ดอกเบี้ยทั้งหมด ณ วันที่ออกใบเสร็จ
                    $data_insert['interest_balance'] = @$interest_balance; //ดอกเบี้ยคงเหลือค้างชำระ
                    $data_insert['interest_date'] = $date;
                    $data_insert['receipt_id'] = $receipt_number;
                    $data_insert['interest_status'] = 0;
                    $data_insert['admin_id'] = $_SESSION['USER_ID'];
                    $data_insert['created_datetime'] = $date;
                    $data_insert['updated_datetime'] = $date;
                    $this->db->insert('coop_loan_interest_debt', $data_insert);
                }
                if ((@$data_post['interest_debt'][$key] != 0 && @$data_post['interest'][$key] != 0) || (@$data_post['interest'][$key] != 0 && @$data_post['interest_all'][$key] != 0)) {
                    $where_loan = (@$data_post['loan_id'][$key] != "") ? " AND loan_id = '" . $data_post['loan_id'][$key] . "'" : " AND loan_atm_id = '" . $data_post['loan_atm_id'][$key] . "'";
                    $this->db->select("id, loan_id,interest_total,interest_balance");
                    $this->db->from('coop_loan_interest_debt');
                    $this->db->where("interest_balance > 0 {$where_loan} AND interest_status = 0 ");
                    $this->db->order_by("created_datetime ASC");
                    $row_interest_debt = $this->db->get()->result_array();
                    $post_interest_balance = @$data_post['interest'][$key];
                    $interest_debt_pay = 0;
                    foreach ($row_interest_debt as $key_interest_debt => $value_interest_debt) {
                        if ($post_interest_balance > 0) {
                            if ($post_interest_balance >= $value_interest_debt['interest_balance']) {
                                $data_insert = array();
                                $data_insert['interest_balance'] = 0;
                                $data_insert['updated_datetime'] = $date;
                                $this->db->where('id', $value_interest_debt['id']);
                                $this->db->update('coop_loan_interest_debt', $data_insert);
                                $post_interest_balance = $post_interest_balance - $value_interest_debt['interest_balance'];
                            } else {
                                $data_insert = array();
                                $data_insert['interest_balance'] = $value_interest_debt['interest_balance'] - $post_interest_balance;
                                $data_insert['updated_datetime'] = $date;
                                $this->db->where('id', $value_interest_debt['id']);
                                $this->db->update('coop_loan_interest_debt', $data_insert);
                                $post_interest_balance = 0;
                            }
                        }
                    }
                }
                //$loan_interest_now; //ดอกเบี้ยที่จ่าย
                //$loan_interest_remain; //ดอกเบี้ยคงเหลือค้างชำระ
                //echo 'interest_all='.$data_post['interest_all'][$key].'<br>';
                //echo 'interest='.$data_post['interest'][$key].'<br>';
                //echo 'interest_debt='.$data_post['interest_debt'][$key].'<br>';
                if (@$data_post['interest'][$key] > @$data_post['interest_debt'][$key]) {
                    $loan_interest_now = (@$data_post['interest'][$key] - @$data_post['interest_debt'][$key] > 0) ? @$data_post['interest'][$key] - @$data_post['interest_debt'][$key] : 0;
                    $loan_interest_remain = (@$data_post['interest'][$key] - $loan_interest_now > 0) ? @$data_post['interest'][$key] - $loan_interest_now : 0;
                } else {
                    $loan_interest_now = 0;
                    $loan_interest_remain = @$data_post['interest'][$key];
                }
            }
            //echo 'เงินต้น = '.$data_post['principal_payment'][$key].'<br>';
            //echo 'ดอกเบี้ย = '.$loan_interest_now.'<br>';
            //echo 'ดอกคงค้าง = '.$loan_interest_remain.'<br>';
            //@end บันทึกข้อมูลดอกเบี้ยค้างชำระสะสม
            $data_insert = array();
            $data_insert['member_id'] = $data_post['member_id'];
            $data_insert['receipt_id'] = $receipt_number;
            $data_insert['loan_id'] = $data_post['loan_id'][$key];
            $data_insert['loan_atm_id'] = $data_post['loan_atm_id'][$key];
            $data_insert['deduct_type'] = $data_post['deduct_type'][$key];
            $data_insert['account_list_id'] = $data_post['account_list'][$key];
            $data_insert['principal_payment'] = number_format($data_post['principal_payment'][$key], 2, '.', '');
            //$data_insert['interest'] = number_format($data_post['interest'][$key],2,'.','');
            //$data_insert['loan_interest_remain'] = $data_post['interest_debt'][$key];
            $data_insert['interest'] = number_format($loan_interest_now, 2, '.', '');
            $data_insert['loan_interest_remain'] = number_format($loan_interest_remain, 2, '.', '');
            $data_insert['total_amount'] = $data_post['amount'][$key];
            $data_insert['loan_amount_balance'] = number_format($loan_amount_balance, 2, '.', '');
            $data_insert['payment_date'] = $date;
            $data_insert['createdatetime'] = $date;
            $data_insert['period_count'] = $this->loan_libraries->getPeriodCount($data_post['loan_id'][$key], $date);
            $data_insert['transaction_text'] = $transaction_text;
            $this->db->insert('coop_finance_transaction', $data_insert);
            $statement_status = 'debit';   // สถานะการจ่ายเงิน debit = เงินเข้าจากเคาน์เตอร์, credit  = เงินออกจากเคาน์เตอร์,
            $permission_id = $this->permission_model->permission_url($_SERVER['HTTP_REFERER'], $_SERVER['REQUEST_URI']);
            $this->tranction_financial_drawer->arrange_data_coop_financial_drawer($data_insert, $data_post["pay_type"], $permission_id, $statement_status, $_SERVER['REQUEST_URI']);
            $loan_interest_all = number_format(@$loan_interest_now, 2, '.', '') + number_format(@$loan_interest_remain, 2, '.', '');
            $sum_interest += number_format($loan_interest_all, 2, '.', '');
            if ($data_post['loan_id'][$key] == '') {
                $this->db->select('account_chart_id');
                $this->db->from('coop_account_match');
                $this->db->where("match_id = '" . $data_post['account_list'][$key] . "' AND match_type = 'account_list'");
                $row = $this->db->get()->result_array();
                $row_account_chart = @$row[0];
                $account_chart_id = @$row_account_chart['account_chart_id'];
            } else {
                $this->db->select('coop_account_match.account_chart_id');
                $this->db->from('coop_account_match');
                $this->db->join('coop_loan', 'coop_account_match.match_id = coop_loan.loan_type', 'left');
                $this->db->where("coop_loan.id = '" . $data_post['loan_id'][$key] . "' AND coop_account_match.match_type = 'loan'");
                $row = $this->db->get()->result_array();
                $row_account_chart = @$row[0];
                $account_chart_id = @$row_account_chart['account_chart_id'];
            }
            $process = 'cashier';
            $money = number_format($data_post['principal_payment'][$key], 2, '.', '');
            $ref = $receipt_number;
            $match_type = 'account_list';
            $match_id = $data_post['account_list'][$key];
            if ($data_post_pay_type == 1) {
                $statement = 'debit';
            } else {
                $statement = 'credit';
            }
            $data_process[] = $this->account_transaction->set_data_account_trancetion_detail($match_id, $statement, $match_type, $ref, $money, $process);
        }
        $process = 'cashier';
        $money = number_format($sum_interest, 2, '.', '');
        $ref = $receipt_number;
        $match_type = 'main';
        $match_id = '2';
        if ($data_post_pay_type == 1) {
            $statement = 'debit';
        } else {
            $statement = 'credit';
        }
        $data_process[] = $this->account_transaction->set_data_account_trancetion_detail($match_id, $statement, $match_type, $ref, $money, $process);
//        echo"<pre>";print_r($data_process);
        $this->account_transaction->add_account_trancetion_detail($data_process);
//        exit;
        echo "<script> window.open('" . PROJECTPATH . "/admin/receipt_form_pdf/" . @$receipt_number . "','_blank') </script>";
//        echo"<script>document.location.href='".base_url(PROJECTPATH.'/admin/receipt_form_pdf/'.$receipt_number)."'</script>";
//        exit;
        echo "<script>document.location.href='" . base_url(PROJECTPATH . '/cashier?member_id=' . @$data_post['member_id']) . "'</script>";
        exit;
    }
    function cashier_month()
    {
        // load model pharse
        $this->load->model('Memgroup_model');

        $arr_data = array();
        $month_arr = array('1' => 'มกราคม', '2' => 'กุมภาพันธ์', '3' => 'มีนาคม', '4' => 'เมษายน', '5' => 'พฤษภาคม', '6' => 'มิถุนายน', '7' => 'กรกฎาคม', '8' => 'สิงหาคม', '9' => 'กันยายน', '10' => 'ตุลาคม', '11' => 'พฤศจิกายน', '12' => 'ธันวาคม');
        $arr_data['month_arr'] = $month_arr;

        if (@$this->input->get('month') != '' && @$this->input->get('year') != '') {
            $month = (int)$_GET['month'];
            $year = $_GET['year'];
        } else {
            $month = (int)date('m');
            $year = date('Y') + 543;
        }
        $arr_data['month'] = $month;
        $arr_data['year'] = $year;

        $this->db->select('setting_value');
        $this->db->from('coop_share_setting');
        $this->db->where("setting_id = '1'");
        $row = $this->db->get()->result_array();
        $row_share_value = $row[0];
        $share_value = $row_share_value['setting_value'];

        $x = 0;
        $join_arr = array();
        $join_arr[$x]['table'] = 'coop_prename';
        $join_arr[$x]['condition'] = 'coop_prename.prename_id = coop_mem_apply.prename_id';
        $join_arr[$x]['type'] = 'left';
        $x++;
        $join_arr[$x]['table'] = 'coop_receipt';
        $join_arr[$x]['condition'] = 'coop_mem_apply.member_id = coop_receipt.member_id';
        $join_arr[$x]['type'] = 'inner';

        $this->paginater_all->type(DB_TYPE);
        $this->paginater_all->select(array('coop_mem_apply.*', 'coop_prename.prename_short', 'coop_receipt.receipt_id', 'coop_receipt.sumcount'));
        $this->paginater_all->main_table('coop_mem_apply');
        $this->paginater_all->where("
			coop_mem_apply.member_status = '1' 
			AND coop_receipt.month_receipt = '" . $month . "' 
			AND coop_receipt.year_receipt = '" . $year . "'
		");
        $this->paginater_all->page_now(@$_GET["page"]);
        $this->paginater_all->per_page(10);
        $this->paginater_all->page_link_limit(20);
        $this->paginater_all->order_by('coop_mem_apply.member_id ASC');
        $this->paginater_all->join_arr($join_arr);
        $row = $this->paginater_all->paginater_process();
        //echo"<pre>";print_r($row);exit;
        $paging = $this->pagination_center->paginating($row['page'], $row['num_rows'], $row['per_page'], $row['page_link_limit']);//$page_now = 1, $row_total = 1, $per_page = 20, $page_limit = 20
        $i = $row['page_start'];

        $arr_data['num_rows'] = $row['num_rows'];
        $arr_data['paging'] = $paging;
        $arr_data['data'] = $row['data'];
        $arr_data['i'] = $i;

        $this->db->select(array('deduct_code', 'deduct_detail'));
        $this->db->from('coop_deduct');
        $this->db->order_by('deduct_seq ASC');
        $deduct_list = $this->db->get()->result_array();
        $arr_data['deduct_list'] = $deduct_list;

        /*foreach($arr_data['data'] as $key => $value){
            $this->db->select(
                array(
                    'coop_receipt.receipt_id',
                    'coop_receipt.sumcount'
                )
            );
            $this->db->from('coop_receipt');
            $this->db->where("
                coop_receipt.member_id = '".$value['member_id']."'
                AND coop_receipt.month_receipt = '".$month."'
                AND coop_receipt.year_receipt = '".$year."'
            ");
            $row = $this->db->get()->result_array();
            $loan_receipt = 0;
            $receipt_id = '';
            foreach($row as $key2 => $row_check_receipt){
                $receipt_id = $row_check_receipt['receipt_id'];
                $arr_data['data'][$key]['receipt_id'] = $receipt_id;
                $arr_data['data'][$key]['total_amount'] = $row_check_receipt['sumcount'];
            }
        }*/
        //echo"<pre>";print_r($arr_data['data']);exit;

        // get all department
        $arr_data['departments'] = $this->Memgroup_model->get_department_all();

        // get page number ex. 1-100, 101-200
        // Warning!!! hardcode plaese reflector code
        $arr_data['page_numbers'] = array(
            1 => '1-100',
            2 => '101-200',
            3 => '201-300',
            4 => '301-400',
            5 => '401-500',
            6 => '501-600',
            7 => '601-700',
            8 => '701-800',
            9 => '801-900',
            10 => '901-1000',
            11 => '1001-1100',
            12 => '1101-1200',
            13 => '1201-1300',
            14 => '1301-1400',
            15 => '1401-1500'
        );

        // render view
        $this->libraries->template('cashier/cashier_month', $arr_data);
    }
    function cashier_month_bk()
    {
        $arr_data = array();
        $month_arr = array('1' => 'มกราคม', '2' => 'กุมภาพันธ์', '3' => 'มีนาคม', '4' => 'เมษายน', '5' => 'พฤษภาคม', '6' => 'มิถุนายน', '7' => 'กรกฎาคม', '8' => 'สิงหาคม', '9' => 'กันยายน', '10' => 'ตุลาคม', '11' => 'พฤศจิกายน', '12' => 'ธันวาคม');
        $arr_data['month_arr'] = $month_arr;

        if (@$this->input->get('month') != '' && @$this->input->get('year') != '') {
            $month = $_GET['month'];
            $year = $_GET['year'];
        } else {
            $month = date('m');
            $year = date('Y') + 543;
        }
        $arr_data['month'] = $month;
        $arr_data['year'] = $year;

        $this->db->select('setting_value');
        $this->db->from('coop_share_setting');
        $this->db->where("setting_id = '1'");
        $row = $this->db->get()->result_array();
        $row_share_value = $row[0];
        $share_value = $row_share_value['setting_value'];

        $x = 0;
        $join_arr = array();

        $this->paginater_all->type(DB_TYPE);
        $this->paginater_all->select('*');
        $this->paginater_all->main_table('coop_mem_apply');
        $this->paginater_all->where("member_status = '1'");
        $this->paginater_all->page_now(@$_GET["page"]);
        $this->paginater_all->per_page(10);
        $this->paginater_all->page_link_limit(20);
        $this->paginater_all->order_by('member_id ASC');
        $this->paginater_all->join_arr($join_arr);
        $row = $this->paginater_all->paginater_process();
        //echo"<pre>";print_r($row);exit;
        $paging = $this->pagination_center->paginating($row['page'], $row['num_rows'], $row['per_page'], $row['page_link_limit']);//$page_now = 1, $row_total = 1, $per_page = 20, $page_limit = 20
        $i = $row['page_start'];

        $arr_data['num_rows'] = $row['num_rows'];
        $arr_data['paging'] = $paging;
        $arr_data['data'] = $row['data'];
        $arr_data['i'] = $i;
        foreach ($arr_data['data'] as $key => $value) {
            $arr_data['data'][$key]['emergent_loan'] = 0;
            $arr_data['data'][$key]['normal_loan'] = 0;

            $this->db->select('change_value');
            $this->db->from('coop_change_share');
            $this->db->where("member_id = '" . $value['member_id'] . "' AND (change_share_status = '1' OR change_share_status = '2')");
            $this->db->order_by('change_share_id DESC');
            $this->db->limit(1);
            $row = $this->db->get()->result_array();
            $row_change_share = @$row[0];

            if (@$row_change_share['change_value'] != '') {
                $num_share = $row_change_share['change_value'];
            } else {
                $this->db->select('share_salary');
                $this->db->from('coop_share_rule');
                $this->db->where("salary_rule <= '" . $value['salary'] . "'");
                $this->db->order_by('salary_rule DESC');
                $this->db->limit(1);
                $row = $this->db->get()->result_array();
                $row_share_rule = @$row[0];

                $num_share = $row_share_rule['share_salary'];
            }
            $share = $num_share * $share_value;
            $arr_data['data'][$key]['share'] = $share;

            $this->db->select(
                array(
                    'coop_loan.id',
                    'coop_loan.loan_type',
                    'coop_loan.contract_number',
                    'coop_loan.loan_amount_balance',
                    'coop_loan.interest_per_year',
                    'coop_loan_transfer.date_transfer'
                )
            );
            $this->db->from('coop_loan');
            $this->db->join('coop_loan_transfer', 'coop_loan_transfer.loan_id = coop_loan.id', 'inner');
            $this->db->where("
				coop_loan.loan_amount_balance > 0
				AND coop_loan.member_id = '" . $value['member_id'] . "'
				AND coop_loan.loan_type IN ('1','2','3','5','6')
				AND coop_loan.loan_status = '1'
				AND coop_loan.date_start_period <= '" . ($year - 543) . "-" . $month . "-" . date('t', strtotime(($year - 543) . "-" . $month . "-01")) . "'
			");
            $row = $this->db->get()->result_array();
            $normal_loan = 0;
            foreach ($row as $key => $row_normal_loan) {
                $this->db->select(
                    array(
                        'principal_payment'
                    )
                );
                $this->db->from('coop_loan_period');
                $this->db->where("loan_id = '" . $row_normal_loan['id'] . "'");
                $this->db->limit(1);
                $row = $this->db->get()->result_array();
                $row_principal_payment = $row[0];

                $date_interesting = date('Y-m-t', strtotime(($year - 543) . "-" . sprintf("%02d", $month) . '-01'));
                $this->db->select(
                    array(
                        'payment_date'
                    )
                );
                $this->db->from('coop_finance_transaction');
                $this->db->where("loan_id = '" . $row_normal_loan['id'] . "'");
                $this->db->order_by("payment_date DESC");
                $this->db->limit(1);
                $row = $this->db->get()->result_array();
                $row_date_prev_paid = @$row[0];

                $date_prev_paid = $row_date_prev_paid['payment_date'] != '' ? $row_date_prev_paid['payment_date'] : $row_normal_loan['date_transfer'];
                $diff = date_diff(date_create($date_prev_paid), date_create($date_interesting));
                $date_count = $diff->format("%a");
                $date_count = $date_count + 1;

                $interest = ((($row_normal_loan['loan_amount_balance'] * $row_normal_loan['interest_per_year']) / 100) / 365) * $date_count;

                if ($row_principal_payment['principal_payment'] > $row_normal_loan['loan_amount_balance']) {
                    $principal_payment = $row_normal_loan['loan_amount_balance'];
                } else {
                    $principal_payment = $row_principal_payment['principal_payment'];
                }

                if ($row_normal_loan['loan_type'] == '3') {
                    $arr_data['data'][$key]['emergent_loan'] += $interest + $principal_payment;
                } else {
                    $arr_data['data'][$key]['normal_loan'] += $interest + $principal_payment;
                }

                $this->db->select(
                    array(
                        'coop_receipt.receipt_id',
                        'coop_loan.loan_type',
                        'coop_finance_transaction.total_amount'
                    )
                );
                $this->db->from('coop_receipt');
                $this->db->join('coop_finance_transaction', 'coop_receipt.receipt_id = coop_finance_transaction.receipt_id', 'inner');
                $this->db->join('coop_loan', 'coop_finance_transaction.loan_id = coop_loan.id', 'left');
                $this->db->where("
					coop_receipt.member_id = '" . $value['member_id'] . "' 
					AND coop_receipt.month_receipt = '" . $month . "' 
					AND coop_receipt.year_receipt = '" . $year . "'
				");
                $row = $this->db->get()->result_array();
                $normal_loan_re = 0;
                $emergent_loan_re = 0;
                $receipt_id = '';
                foreach ($row as $key2 => $row_check_receipt) {
                    $receipt_id = $row_check_receipt['receipt_id'];
                    if (in_array($row_check_receipt['loan_type'], array('1', '2', '5', '6'))) {
                        $normal_loan_re += $row_check_receipt['total_amount'];
                    } else if ($row_check_receipt['loan_type'] == '3') {
                        $emergent_loan_re += $row_check_receipt['total_amount'];
                    }
                }
                if ($normal_loan_re > 0) {
                    $arr_data['data'][$key]['normal_loan'] = $normal_loan_re;
                }
                if ($emergent_loan_re > 0) {
                    $arr_data['data'][$key]['emergent_loan'] = $emergent_loan_re;
                }
            }
        }

        $this->libraries->template('cashier/cashier_month', $arr_data);
    }

    function non_pay()
    {
        $arr_data = array();

        $where = '';
        $where_finance_month = '';
        if (@$_GET['year'] != '') {
            $where .= " AND non_pay_year = '" . @$_GET['year'] . "'";
            $where .= " AND non_pay_month = '" . @$_GET['month'] . "'";
            $where_finance_month = "WHERE t2.profile_month = '" . (int)@$_GET['month'] . "' AND t2.profile_year = '" . @$_GET['year'] . "'";
        } else {
            $where_finance_month = "WHERE t2.profile_month = '" . (int)date('m') . "' AND t2.profile_year = '" . (date('Y') + 543) . "'";
        }

        $x = 0;
        $join_arr = array();
        $join_arr[$x]['table'] = 'coop_mem_apply';
        $join_arr[$x]['condition'] = 'coop_non_pay.member_id = coop_mem_apply.member_id';
        $join_arr[$x]['type'] = 'inner';
        $x++;
        $join_arr[$x]['table'] = 'coop_prename';
        $join_arr[$x]['condition'] = 'coop_mem_apply.prename_id = coop_prename.prename_id';
        $join_arr[$x]['type'] = 'left';
        $x++;
        $join_arr[$x]['table'] = "(SELECT
									SUM(t1.pay_amount) AS pay_amount,
									t2.profile_month,
									t2.profile_year,
									t1.member_id
								FROM
									coop_finance_month_detail AS t1
								LEFT JOIN coop_finance_month_profile AS t2 ON t1.profile_id = t2.profile_id
								{$where_finance_month}
								GROUP BY t1.member_id
								) AS t3";
        $join_arr[$x]['condition'] = "coop_non_pay.non_pay_year = t3.profile_year AND coop_non_pay.non_pay_month = t3.profile_month AND coop_non_pay.member_id = t3.member_id";
        $join_arr[$x]['type'] = 'left';

        $this->paginater_all->type(DB_TYPE);
        $this->paginater_all->select('
			coop_non_pay.*, 
			coop_mem_apply.firstname_th, 
			coop_mem_apply.lastname_th,
			coop_prename.prename_short,
			t3.pay_amount
		');
        $this->paginater_all->main_table('coop_non_pay');
        $this->paginater_all->where("non_pay_status = '0'" . $where);
        $this->paginater_all->page_now(@$_GET["page"]);
        $this->paginater_all->per_page(10);
        $this->paginater_all->page_link_limit(20);
        $this->paginater_all->order_by('non_pay_year DESC, non_pay_month DESC, member_id ASC');
        $this->paginater_all->join_arr($join_arr);
        $row = $this->paginater_all->paginater_process();
        //echo"<pre>";print_r($row);exit;
        //echo $this->db->last_query();exit;
        $paging = $this->pagination_center->paginating($row['page'], $row['num_rows'], $row['per_page'], $row['page_link_limit'], @$_GET);//$page_now = 1, $row_total = 1, $per_page = 20, $page_limit = 20
        $i = $row['page_start'];

        $arr_data['num_rows'] = $row['num_rows'];
        $arr_data['paging'] = $paging;
        $arr_data['data'] = $row['data'];
        $arr_data['i'] = $i;

        $month_arr = array('1' => 'มกราคม', '2' => 'กุมภาพันธ์', '3' => 'มีนาคม', '4' => 'เมษายน', '5' => 'พฤษภาคม', '6' => 'มิถุนายน', '7' => 'กรกฎาคม', '8' => 'สิงหาคม', '9' => 'กันยายน', '10' => 'ตุลาคม', '11' => 'พฤศจิกายน', '12' => 'ธันวาคม');

        $arr_data['month_arr'] = $month_arr;

        $this->libraries->template('cashier/non_pay', $arr_data);
    }
    function non_pay_save()
    {
        //echo"<pre>";print_r($_POST);exit;

        foreach ($_POST['data']['list_data'] as $key => $value) {
            $this->db->select(array('non_pay_id'));
            $this->db->from('coop_non_pay');
            $this->db->where("member_id = '" . @$value['member_id'] . "' AND non_pay_month = '" . $_POST['non_pay_month'] . "' AND non_pay_year = '" . $_POST['non_pay_year'] . "'");
            $row_non_pay = $this->db->get()->result_array();
            $row_non_pay = @$row_non_pay[0];

            $data_insert = array();
            $data_insert['non_pay_month'] = @$_POST['non_pay_month'];
            $data_insert['non_pay_year'] = @$_POST['non_pay_year'];
            $data_insert['member_id'] = @$value['member_id'];
            $data_insert['non_pay_amount'] = str_replace(',', '', $value['non_pay_amount']);
            $data_insert['non_pay_amount_balance'] = str_replace(',', '', $value['non_pay_amount']);
            $data_insert['admin_id'] = @$_SESSION['USER_ID'];
            $data_insert['non_pay_status'] = '0';
            //echo '<pre>'; print_r($data_insert); echo '</pre>';
            if (@$row_non_pay['non_pay_id'] != '') {
                $this->db->where('non_pay_id', $row_non_pay['non_pay_id']);
                $this->db->update('coop_non_pay', $data_insert);
            } else {
                $this->db->insert('coop_non_pay', $data_insert);
            }
        }
        $this->center_function->toast('บันทึกข้อมูลเรียบร้อยแล้ว');
        echo "<script>document.location.href='" . base_url(PROJECTPATH . '/cashier/non_pay') . "'</script>";
    }
    function non_pay_delete($non_pay_id)
    {
        $this->db->where('non_pay_id', $non_pay_id);
        $this->db->delete('coop_non_pay');
        $this->center_function->toast('ลบข้อมูลเรียบร้อยแล้ว');
        echo "<script>document.location.href='" . base_url(PROJECTPATH . '/cashier/non_pay') . "'</script>";
    }

    function keypress_search_member()
    {
        $data = array();
        $deduct_id = $_POST['deduct_id'];
        //$member_id = sprintf("%06d",$_POST['member_id']);
        $member_id = $this->center_function->complete_member_id($_POST['member_id']);
        $non_pay_year = @$_POST['non_pay_year'];
        $non_pay_month = @$_POST['non_pay_month'];
        $this->db->select(array('t1.*', 't2.prename_short'));
        $this->db->from('coop_mem_apply as t1');
        $this->db->join('coop_prename as t2', 't1.prename_id = t2.prename_id', 'left');
        $this->db->where("t1.member_id = '" . $member_id . "'");
        $row = $this->db->get()->result_array();
        $row_member = $row[0];
        if (!empty($row_member)) {
            $data['row_member'] = $row_member;

            $this->db->select(array('*'));
            $this->db->from('coop_deduct');
            $this->db->where("deduct_id = '" . $deduct_id . "'");
            $row_deduct = $this->db->get()->result_array();
            $row_deduct = $row_deduct[0];
            $ref_arr = array();
            if ($row_deduct['deduct_type'] == '1') {
                if ($row_deduct['deduct_code'] == 'ATM') {
                    $this->db->select(array('loan_atm_id', 'contract_number'));
                    $this->db->from('coop_loan_atm');
                    $this->db->where("member_id = '" . $member_id . "' AND loan_atm_status = '1'");
                    $row_data = $this->db->get()->result_array();
                    if (!empty($row_data)) {
                        $i = 0;
                        foreach ($row_data as $key => $value) {
                            $ref_arr[$i]['value'] = $value['loan_atm_id'];
                            $ref_arr[$i]['text'] = $value['contract_number'];
                            $i++;
                        }
                    }
                } else if ($row_deduct['deduct_code'] == 'LOAN') {
                    $this->db->select(array('ref_id'));
                    $this->db->from('coop_deduct_detail');
                    $this->db->where("deduct_id = '" . $row_deduct['deduct_id'] . "'");
                    $row_deduct_detail = $this->db->get()->result_array();
                    $i = 0;
                    foreach ($row_deduct_detail as $key_deduct_detail => $value_deduct_detail) {
                        $this->db->select(array('id', 'contract_number'));
                        $this->db->from('coop_loan');
                        $this->db->where("
							member_id = '" . $member_id . "' 
							AND loan_type = '" . $value_deduct_detail['ref_id'] . "' 
							AND loan_status = '1'
						");
                        $row_data = $this->db->get()->result_array();
                        if (!empty($row_data)) {
                            foreach ($row_data as $key => $value) {
                                $ref_arr[$i]['value'] = $value['id'];
                                $ref_arr[$i]['text'] = $value['contract_number'];
                                $i++;
                            }
                        }
                    }
                }
            } else if ($row_deduct['deduct_type'] == '2') {
                $this->db->select(array('account_id', 'account_name'));
                $this->db->from('coop_maco_account');
                $this->db->where("mem_id = '" . $member_id . "' AND account_status = '0'");
                $row_data = $this->db->get()->result_array();
                if (!empty($row_data)) {
                    $i = 0;
                    foreach ($row_data as $key => $value) {
                        $ref_arr[$i]['value'] = $value['account_id'];
                        $ref_arr[$i]['text'] = $value['account_id'] . ":" . $value['account_name'];
                        $i++;
                    }
                }
            }
            $ref_data = '';
            if (!empty($ref_arr)) {
                $ref_data = '<select class="form-control" id="ref_data_' . $_POST['id'] . '" name="data[list_data][' . $_POST['id'] . '][ref_data]">';
                $ref_data .= '<option value="">เลือกข้อมูล</option>';
                foreach ($ref_arr as $key => $value) {
                    $ref_data .= '<option value="' . $value['value'] . '">' . $value['text'] . '</option>';
                }
                $ref_data .= "</select>";
            }

            $data['ref_data'] = $ref_data;

            //ข้อมูลยอดเงินทั้งหมดที่ต้องประมวลผลผ่านรายการ
            $total_pay_amount = $this->db->select(array('SUM(t1.pay_amount) AS pay_amount'))
                ->from('coop_finance_month_detail AS t1')
                ->join("coop_finance_month_profile AS t2", "t1.profile_id = t2.profile_id", "left")
                ->where("t1.member_id = '" . $member_id . "' AND t2.profile_month = '" . (int)$non_pay_month . "' AND t2.profile_year = '" . $non_pay_year . "'")
                ->limit(1)
                ->get()->result_array();
            $data['total_pay_amount'] = number_format($total_pay_amount[0]['pay_amount'], 2);

            echo json_encode($data);
        } else {
            echo "error";
        }
    }

    public function search_receipt()
    {
        $arr_data = array();
        $where = '1=1 ';
        if ($_POST['search_receipt_list'] == 'receipt_id') {
            $where .= " AND t1.receipt_id LIKE '%" . $_POST['search_receipt_text'] . "%'";
        } else if ($_POST['search_receipt_list'] == 'member_id') {
            $where .= " AND t1.member_id LIKE '%" . $_POST['search_receipt_text'] . "%'";
        } else if ($_POST['search_receipt_list'] == 'id_card') {
            $where .= " AND t2.id_card LIKE '%" . $_POST['search_receipt_text'] . "%'";
        } else if ($_POST['search_receipt_list'] == 'firstname_th') {
            $where .= " AND t2.firstname_th LIKE '%" . $_POST['search_receipt_text'] . "%'";
        } else if ($_POST['search_receipt_list'] == 'lastname_th') {
            $where .= " AND t2.lastname_th LIKE '%" . $_POST['search_receipt_text'] . "%'";
        }

        $this->db->select(array(
            't1.receipt_id',
            't1.member_id',
            't2.firstname_th',
            't2.lastname_th',
            't3.prename_short',
            't4.user_name',
            'IF(t1.receipt_status is null OR t1.receipt_status = 0, "ปกติ", "ยกเลิก" ) as receipt_status',
            't1.cancel_date',
            't1.receipt_datetime',
            't5.user_name as cancel_by'
        ));
        $this->db->from('coop_receipt as t1');
        $this->db->join('coop_mem_apply as t2', 't1.member_id = t2.member_id', 'inner');
        $this->db->join('coop_prename as t3', 't2.prename_id = t3.prename_id', 'left');
        $this->db->join('coop_user AS t4', 't1.admin_id = t4.user_id', 'left');
        $this->db->join('coop_user AS t5', 't1.cancel_by = t5.user_id', 'left');
        $this->db->where($where);
        $this->db->order_by('receipt_datetime DESC, receipt_id DESC');
        $this->db->limit(50);
        $row_data = $this->db->get()->result_array();
        foreach ($row_data as $key => $value) {
            $total_amount = 0;
            $this->db->select('total_amount');
            $this->db->from('coop_finance_transaction');
            $this->db->where("receipt_id = '" . $value['receipt_id'] . "'");
            $row_detail = $this->db->get()->result_array();
            foreach ($row_detail as $key2 => $value2) {
                $total_amount += $value2['total_amount'];
            }
            $row_data[$key]['total_amount'] = $total_amount;
        }

        $arr_data['data'] = $row_data;
        $this->db->select(array(
            't1.bill_id as receipt_id',
            't1.member_id',
            't2.firstname_th',
            't2.lastname_th',
            't3.prename_short',
            't4.user_name',
            't1.return_time as receipt_datetime',
            't5.user_name as cancel_by',
            't1.return_amount as total_amount'
        ));
        $this->db->from('coop_process_return as t1');
        $this->db->join('coop_mem_apply as t2', 't1.member_id = t2.member_id', 'inner');
        $this->db->join('coop_prename as t3', 't2.prename_id = t3.prename_id', 'left');
        $this->db->join('coop_user AS t4', 't1.user_id = t4.user_id', 'left');
        $this->db->join('coop_user AS t5', 't1.user_id = t5.user_id', 'left');
        $this->db->where($where);
        $this->db->order_by('receipt_datetime DESC, receipt_id DESC');
        $this->db->limit(50);
        $coop_process_return = $this->db->get()->result_array();
        foreach ($coop_process_return as $value) {
            array_push( $arr_data['data'], $value);
        }
        $this->load->view('cashier/search_receipt', $arr_data);
    }

    function non_pay_all_amount_save()
    {
        // echo"<pre>";print_r($_POST);
        //$this->db->select(array('member_id'));
        //$this->db->from('coop_non_pay');
        //$this->db->where("non_pay_month = '10' AND non_pay_year = '2561' AND non_pay_status = '0'");
        //$rs_non_pay = $this->db->get()->result_array();
        //$arr_non_pay = array_column($rs_non_pay, 'member_id');
        $arr_non_pay = @$_POST['member_id'];
        $month_non_pay = @$_POST['month_non_pay'];
        $year_non_pay = @$_POST['year_non_pay'];
        // echo '<pre>'; print_r($arr_non_pay); echo '</pre>';

        /*$this->db->select(array('member_id','SUM(pay_amount) AS pay_amount'));
        $this->db->from('coop_finance_month_detail');
        $this->db->where("profile_id = '41' AND run_status = '0'");
        $this->db->group_by("member_id");
        */
        //echo implode("','", $arr_non_pay);

        //หาจาก profile_id
        $rs_finance_month_profile = $this->db->select(array('profile_id', 'profile_month', 'profile_year'))
            ->from('coop_finance_month_profile')
            ->where("profile_month = '" . $month_non_pay . "' AND profile_year = '" . $year_non_pay . "' ")
            ->limit(1)
            ->get()->result_array();
        //echo '<pre>'; print_r($rs_finance_month_profile); echo '</pre>';
        $profile_id = @$rs_finance_month_profile[0]['profile_id'];
        if ($profile_id != '') {
            $rs_finance_month_detail = $this->db->select(array('member_id', 'SUM(pay_amount) AS pay_amount'))
                ->from('coop_finance_month_detail')
                ->where("profile_id = '" . $profile_id . "' AND run_status = '0' AND member_id IN ('" . implode("','", $arr_non_pay) . "')")
                ->group_by("member_id")
                ->get()->result_array();
            // echo $this->db->last_query();
            // exit;
            foreach ($rs_finance_month_detail AS $key => $value) {

                $data_insert = array();
                $data_insert['non_pay_month'] = $_POST['month_non_pay'];
                $data_insert['non_pay_year'] = $_POST['year_non_pay'];
                $data_insert['member_id'] = @$value['member_id'];
                $data_insert['non_pay_amount'] = str_replace(',', '', $value['pay_amount']);
                $data_insert['non_pay_amount_balance'] = str_replace(',', '', $value['pay_amount']);
                $data_insert['admin_id'] = @$_SESSION['USER_ID'];
                $data_insert['non_pay_status'] = '1';
                // echo '<pre>'; print_r($data_insert); echo '</pre>INERSERTED<br>';
                // non pay-
                $this->db->insert("coop_non_pay", $data_insert);
                $non_pay_id = $this->db->insert_id();

                // หาจาก finance month detail
                $this->db->where("profile_id", $profile_id);
                $this->db->where("member_id", @$value['member_id']);
                $query = $this->db->get("coop_finance_month_detail");
                foreach ($query->result_array() as $value_detail) {
                    $data_insert = array();
                    $data_insert['deduct_code'] = $value_detail['deduct_code'];
                    $data_insert['non_pay_amount'] = $value_detail['pay_amount'];
                    $data_insert['non_pay_amount_balance'] = $value_detail['pay_amount'];
                    $data_insert['loan_id'] = $value_detail['loan_id'];
                    $data_insert['loan_atm_id'] = $value_detail['loan_atm_id'];
                    $data_insert['pay_type'] = $value_detail['pay_type'];
                    $data_insert['finance_month_profile_id'] = $value_detail['profile_id'];
                    $data_insert['finance_month_detail_id'] = $value_detail['run_id'];
                    $data_insert['member_id'] = $value_detail['member_id'];
                    $data_insert['non_pay_id'] = $non_pay_id;
                    $data_insert['cremation_type_id'] = $value_detail['cremation_type_id'];
                    $data_insert['deposit_account_id'] = $value_detail['deposit_account_id'];
                    $this->db->insert('coop_non_pay_detail', $data_insert);

                    // run status = 1
                    // อัพเดทยอด real_pay_amount = 0
                    $this->db->set("run_status", 1);
                    $this->db->set("real_pay_amount", 0);
                    $this->db->set("update_datetime", date("Y-m-d"));
                    $this->db->where("run_id", $value_detail['run_id']);
                    $this->db->update('coop_finance_month_detail');
                }

                // exit;
            }
        }
        // exit;
        $this->center_function->toast('บันทึกข้อมูลเรียบร้อยแล้ว');
        echo "<script>document.location.href='" . base_url(PROJECTPATH . '/cashier/non_pay_all') . "'</script>";
    }

    function non_pay_all()
    {
        //exit;
        $arr_data = array();
        $month_arr = array('1' => 'มกราคม', '2' => 'กุมภาพันธ์', '3' => 'มีนาคม', '4' => 'เมษายน', '5' => 'พฤษภาคม', '6' => 'มิถุนายน', '7' => 'กรกฎาคม', '8' => 'สิงหาคม', '9' => 'กันยายน', '10' => 'ตุลาคม', '11' => 'พฤศจิกายน', '12' => 'ธันวาคม');
        $arr_data['month_arr'] = $month_arr;
        //ตอนแรกเป็น get แต่จะเปลี่ยน form เป็น post
        //$_GET = @$_POST;
        $month = @$_GET['month'] != '' ? $_GET['month'] : (int)date('m');
        $year = @$_GET['year'] != '' ? $_GET['year'] : (date('Y') + 543);
        $show_row = @$_GET['show_row'] != '' ? $_GET['show_row'] : '100';
        $arr_data['month'] = $month;
        $arr_data['year'] = $year;
        $arr_data['show_row'] = $show_row;
        if (@$_GET['level'] != '') {
            $mem_group = $_GET['level'];
        } else if (@$_GET['faction'] != '') {
            $mem_group = $_GET['faction'];
        } else if (@$_GET['department'] != '') {
            $mem_group = $_GET['department'];
        }
        $this->db->select('*');
        $this->db->from('coop_mem_group');
        $this->db->where("id = '" . @$mem_group . "'");
        $row = $this->db->get()->result_array();
        $row_mem_group = @$row[0];
        if (!empty($row_mem_group)) {
            $department = $row_mem_group['mem_group_name'];
        } else {
            $department = 'ทั้งหมด';
        }
        $arr_data['department'] = $department;

        $where = " AND member_status <> '3' ";

        if (@$_GET['department'] != '') {
            $where .= " AND department = '" . $_GET['department'] . "'";
        }
        if (@$_GET['faction'] != '') {
            $where .= " AND faction = '" . $_GET['faction'] . "'";
        }
        if (@$_GET['level'] != '') {
            $where .= " AND level = '" . $_GET['level'] . "'";
        }
        if (@$_GET['mem_type_id'] != '') {
            $where .= " AND mem_type_id = '" . $_GET['mem_type_id'] . "'";
        }
        if (@$_GET['member_id'] != '') {
        	if(strtoupper(substr($_GET['member_id'], 0, 1)) == "S"){
				$where .= " AND coop_mem_apply.member_id = '" . $_GET['member_id'] . "'";
			}else {
				$where .= " AND coop_mem_apply.member_id = '" . sprintf('%05d', $_GET['member_id']) . "'";
			}
        }

        //if(@$_GET['pay_type']!=''){
        //	$where .= " AND coop_receipt.pay_type = '".$_GET['pay_type']."'";
        //}
        //$where .= " AND (coop_receipt.receipt_id = '' OR coop_receipt.receipt_id IS NULL)";

        $this->db->select('profile_id');
        $this->db->from('coop_finance_month_profile');
        $this->db->where("profile_month = '" . (int)$month . "' AND profile_year = '" . $year . "' ");
        $row = $this->db->get()->result_array();
        $row_profile = @$row[0];
        if (@$row_profile['profile_id'] == '') {
            $data_insert = array();
            $data_insert['profile_month'] = (int)$month;
            $data_insert['profile_year'] = $year;
            $this->db->insert('coop_finance_month_profile', $data_insert);

            $profile_id = $this->db->insert_id();
        } else {
            $profile_id = $row_profile['profile_id'];
            //$this->db->where('profile_id', $profile_id);
            //$this->db->delete('coop_finance_month_detail');
        }

        $x = 0;
        $join_arr = array();
        $join_arr[$x]['table'] = 'coop_prename';
        $join_arr[$x]['condition'] = 'coop_prename.prename_id = coop_mem_apply.prename_id';
        $join_arr[$x]['type'] = 'left';
        $x++;
        $join_arr[$x]['table'] = 'coop_mem_group';
        $join_arr[$x]['condition'] = 'coop_mem_group.id = coop_mem_apply.level';
        $join_arr[$x]['type'] = 'left';
        $x++;
        $join_arr[$x]['table'] = '(SELECT SUM(pay_amount) AS pay_amount,member_id,profile_id,run_status FROM coop_finance_month_detail GROUP BY member_id,profile_id ) AS coop_finance_month_detail';
        $join_arr[$x]['condition'] = "coop_finance_month_detail.member_id = coop_mem_apply.member_id AND profile_id = '" . $profile_id . "' AND coop_finance_month_detail.run_status = '0' AND coop_finance_month_detail.pay_amount > 0";
        $join_arr[$x]['type'] = 'inner';
        $x++;
        $join_arr[$x]['table'] = "coop_non_pay";
        $join_arr[$x]['condition'] = "coop_finance_month_detail.member_id = coop_non_pay.member_id AND coop_non_pay.non_pay_month = '" . (int)$month . "' AND non_pay_year = '" . $year . "'";
        $join_arr[$x]['type'] = "left";

        //$this->paginater_all->debug = true;
        $this->paginater_all->field_count("coop_mem_apply.member_id");
        $this->paginater_all->type(DB_TYPE);
        $this->paginater_all->select(
            'coop_mem_apply.member_id,
				coop_mem_apply.firstname_th,
				coop_mem_apply.lastname_th,
				coop_prename.prename_short,
				coop_mem_group.mem_group_name'
        );

        $this->paginater_all->main_table('coop_mem_apply');
        $this->paginater_all->where("1=1 AND coop_non_pay.non_pay_amount IS NULL " . $where);
        $this->paginater_all->page_now(@$_GET["page"]);
        $this->paginater_all->per_page($show_row);
        $this->paginater_all->page_link_limit($show_row);
        $this->paginater_all->group_by('coop_mem_apply.member_id');
        $this->paginater_all->order_by('coop_mem_apply.member_id ASC');
        $this->paginater_all->join_arr($join_arr);
        $row = $this->paginater_all->paginater_process();
		//echo $this->db->last_query(); exit;
        $paging = $this->pagination_center->paginating($row['page'], $row['num_rows'], $row['per_page'], $row['page_link_limit'], @$_GET);//$page_now = 1, $row_total = 1, $per_page = 20, $page_limit = 20
        $i = $row['page_start'];
        $pay_type = array('0' => 'เงินสด', '1' => 'โอนเงิน');
        foreach ($row['data'] as $key => $value) {
            $pay_amount = 0;
            $this->db->select('*');
            $this->db->from('coop_finance_month_detail');
            $this->db->where("profile_id = '" . @$profile_id . "' AND member_id = '" . @$value['member_id'] . "'");
            $row_detail = $this->db->get()->result_array();
            foreach ($row_detail as $key2 => $value2) {
                $pay_amount += $value2['pay_amount'];
            }
            $row['data'][$key]['pay_amount'] = $pay_amount;

        }

        $arr_data['num_rows'] = $row['num_rows'];
        $arr_data['paging'] = $paging;
        $arr_data['row'] = $row['data'];
        $arr_data['i'] = $i;
        //echo '<pre>'; print_r($arr_data['row']); echo '</pre>'; exit;

        $this->db->select(array('id', 'mem_group_name'));
        $this->db->from('coop_mem_group');
        $this->db->where("mem_group_type = '1'");
        $row_mem_group = $this->db->get()->result_array();
        $arr_data['mem_group'] = $row_mem_group;

        foreach (@$row_mem_group AS $key => $value) {
            $arr_data['arr_mem_group'][@$value['id']] = @$value['mem_group_name'];
        }

        if (@$_GET['department'] != '') {
            $this->db->select(array('id', 'mem_group_name'));
            $this->db->from('coop_mem_group');
            $this->db->where("mem_group_parent_id = '" . $_GET['department'] . "'");
            $row_mem_group = $this->db->get()->result_array();

            $arr_data['faction'] = $row_mem_group;

            foreach (@$row_mem_group AS $key => $value) {
                $arr_data['arr_faction'][@$value['id']] = @$value['mem_group_name'];
            }
        }

        if (@$_GET['faction'] != '') {
            $this->db->select(array('id', 'mem_group_name'));
            $this->db->from('coop_mem_group');
            $this->db->where("mem_group_parent_id = '" . $_GET['faction'] . "'");
            $row_mem_group = $this->db->get()->result_array();
            $arr_data['level'] = $row_mem_group;

            foreach (@$row_mem_group AS $key => $value) {
                $arr_data['arr_level'][@$value['id']] = @$value['mem_group_name'];
            }
        }

        //หาจำนวนรายการที่ชำระแล้ว กับ ยังไม่ได้ชำระ
        /*$this->db->select(array('coop_mem_apply.member_id',
                                'coop_mem_apply.firstname_th',
                                'coop_mem_apply.lastname_th',
                                'coop_prename.prename_short',
                                'coop_mem_group.mem_group_name'));
        $this->db->from('coop_mem_apply');
        $this->db->join("coop_prename","coop_prename.prename_id = coop_mem_apply.prename_id","left");
        $this->db->join("coop_mem_group","coop_mem_group.id = coop_mem_apply.level","left");
        $this->db->where("1=1 ".$where);
        $row_summary = $this->db->get()->result_array();

        $arr_summary = array();
        $pay_num = 0;
        $total_pay_amount = 0;
        $total_pay_amount = 0;
        foreach($row_summary as $key => $value){
            $pay_amount = 0;
            $this->db->select('*');
            $this->db->from('coop_finance_month_detail');
            $this->db->where("profile_id = '".@$profile_id."' AND member_id = '".@$value['member_id']."'");
            $row_detail = $this->db->get()->result_array();
            foreach($row_detail as $key2 => $value2){
                $pay_amount += $value2['pay_amount'];
            }

            $pay_amount_m = $pay_amount;

            $this->db->select('*');
            $this->db->from('coop_non_pay');
            $this->db->where("member_id = '".@$value['member_id']."' AND non_pay_month = '".(int)$month."' AND non_pay_year = '".$year."'");
            $row_non_pay = $this->db->get()->result_array();
            $row_non_pay = @$row_non_pay[0];

            $real_pay_amount_m = $pay_amount - @$row_non_pay['non_pay_amount'];

            if(@$pay_amount_m == $real_pay_amount_m){
                @$pay_num++;
            }else{
                @$real_pay_num++;
            }
            $total_pay_amount += @$pay_amount;
        }*/
        $arr_data['pay_num'] = @$pay_num;
        $arr_data['real_pay_num'] = @$real_pay_num;
        $arr_data['total_pay_amount'] = @$total_pay_amount;
        //echo"<pre>";print_r($row['data']);echo"</pre>";exit;

        $this->db->select('t1.*');
        $this->db->from('coop_finance_month_profile as t1');
        $this->db->join('coop_finance_month_detail as t2', 't1.profile_id = t2.profile_id', 'inner');
        $this->db->where("t2.run_status = '1'");
        $this->db->order_by('profile_id DESC');
        $this->db->limit(1);
        $row = $this->db->get()->result_array();
        $arr_data['last_profile'] = @$row[0];

        $month_arr = array('1' => 'มกราคม', '2' => 'กุมภาพันธ์', '3' => 'มีนาคม', '4' => 'เมษายน', '5' => 'พฤษภาคม', '6' => 'มิถุนายน', '7' => 'กรกฎาคม', '8' => 'สิงหาคม', '9' => 'กันยายน', '10' => 'ตุลาคม', '11' => 'พฤศจิกายน', '12' => 'ธันวาคม');
        $arr_data['month_arr'] = $month_arr;

        $this->db->select('mem_type_id, mem_type_name');
        $this->db->from('coop_mem_type');
        $row = $this->db->get()->result_array();
        $arr_data['mem_type'] = $row;
        //echo '<pre>'; print_r($arr_data['row']); echo '</pre>'; exit;
        $this->libraries->template('cashier/non_pay_all', $arr_data);
    }

    public function get_non_pay_balance_by_loan_id()
    {
        $id = $_GET["id"];
        $type = $_GET["type"];
        $balance = 0;
        if ($type == "loan") {
            $detail = $this->db->select("sum(non_pay_amount_balance) as sum")
                ->from("coop_non_pay_detail")
                ->where("loan_id = '" . $id . "' AND pay_type = 'principal'")
                ->get()->row();
            $balance = $detail->sum;
        } elseif ($type == "loan_atm") {
            $detail = $this->db->select("sum(non_pay_amount_balance) as sum")
                ->from("coop_non_pay_detail")
                ->where("loan_atm_id = '" . $id . "' AND pay_type = 'principal'")
                ->get()->row();
            $balance = $detail->sum;
        }
        echo $balance;
        exit;
    }

    public function ajax_get_receipt(){
    	$type = $this->input->post("type");
    	$date = $this->input->post("date");
    	$date = $this->center_function->ConvertToSQLDate($date);

    	$receipt_id = $this->receipt_model->generate_receipt($date, $type);
    	header("content-type: application/json; charset:utf-8");
    	echo json_encode(array('receipt_id' => $receipt_id));
	}
	
	public function change_receipt_id() {
        $auth_id_setting = $this->db->select("value")->from("coop_setting_finance")->where("name = 'able_recript_id_edit' AND status = 1")->order_by("created_at")->get()->row_array();
        $auth_ids = explode(",", $auth_id_setting["value"]);
        if(!in_array($_SESSION['USER_ID'], $auth_ids)) {
            echo "<script>document.location.href='" . base_url(PROJECTPATH) . "'</script>";
            exit;
        }
        $this->libraries->template('cashier/receipt_id_change', $arr_data);
    }

    public function check_receipt_id_json() {
        $receipt_id = $_POST['receipt_id'];
        $result = $this->Finance_libraries->check_receipt_id($receipt_id);
        header('Content-Type: application/json');
        echo json_encode($result);
    }

    public function check_receipt_id_with_coop_buy_json() {
        $receipt_id = $_POST['receipt_id'];
        $result = $this->Finance_libraries->check_receipt_id_with_coop_buy($receipt_id);
        header('Content-Type: application/json');
        echo json_encode($result);
    }

    public function change_receipt_id_json() {
        $receipt_id = $_POST['receipt_id'];
        $new_receipt_id = $_POST['new_receipt_id'];
        $result = $this->Finance_libraries->change_receipt_id($receipt_id, $new_receipt_id);
        header('Content-Type: application/json');
        echo json_encode($result);
    }
}
