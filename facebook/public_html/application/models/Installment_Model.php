<?php


class Installment_Model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model("Loan_calculator_model", "LoanCalc");
    }

    public $_success = array('status' => 200, "status_code" => "success");
    public $_error = array('status' => 400, "status_code" => "error");

    public function setErr($data = array()){
        $this->_error;
    }

    public function getErr(){
        return $this->_error;
    }

    public function findLoanDataByMember(){

    }

    public function update_amount($id, $data = array()){
        $_where = array('id'=> $id);
        $this->db->set($data);
        $this->db->where($_where);
        $this->db->update("coop_loan");
        if($this->db->affected_rows()){
            return array( 'status' => 200, 'status_code' => "success", "msg" => "" );
        }else{
            return array( 'status' => 400, 'status_code' => "error", "data" => $data, "msg" => $this->db->last_query() );
        }
    }

    public function getAmount($id){
        return $this->db->where("id", $id)->get("coop_loan")->row()->installment_amount;
    }

    public function approve($data = array()){
        if(self::isFirstApprove($data['loan']['loan_id']) || $data["installment"]['seq'] == "1"){

			self::addChequeInstallment($data['loan'], $data['cheque'], $data["installment"]['seq']);
			$deduct_id =  self::addDeductProfile(self::createProfile($data));
			self::addLoanDeduct($data['deduct'], $data['installment']['seq'], $deduct_id);
        	self::approve_contract($data['loan'], $data['installment']);
        }else{

        	$receipt_id = $this->receipt_model->generate_receipt($data['loan']['approve_date'], 1);
            $installment = $data['installment'];
            $add = array();
            $add['loan_id'] = $installment['loan_id'];
            $add['transaction_datetime'] = self::checkTime($installment['loan_id'], $installment['transaction_datetime']);
            $add['add'] = $installment['amount'];
            $add['receipt_id'] = $receipt_id;
            self::addTransaction($add);
            //add Cheque Loan
			self::addChequeInstallment($data['loan'], $data['cheque'], $data['installment']['seq']);
			$deduct_id = self::addDeductProfile(self::createProfile($data));
			self::addLoanDeduct($data['deduct'], $data['installment']['seq'], $deduct_id);
			self::addPayment($data['loan'], $data['installment']['seq'], $receipt_id);
        }
        return self::add($data['installment']);
    }

    public function isFirstApprove($id){
        $count = $this->db->where(array("loan_id" => $id))
            ->from("coop_loan_installment")->count_all_results();
        return $count === 0;
    }

    public function update_contract($id, $data){
        $this->db->set($data);
        $this->db->where(array('id' => $id));
        $this->db->update("coop_loan");
    }

    public function getList($id = ""){

    	$sqldeduct = "SELECT SUM(`loan_deduct_amount`) as total_deduct,  intstallment_seq, loan_id FROM coop_loan_deduct WHERE loan_id='{$id}' GROUP BY loan_id, intstallment_seq";

        return $this->db->select(array("seq", "t1.transaction_datetime", "amount", "balance", "user_name", "transfer_status", "total_deduct", "estimate_receive_money"))
            ->from("coop_loan_installment as t1")
			->join("coop_loan_deduct_profile as t3", "t1.loan_id=t3.loan_id and t1.seq=t3.intstallment_seq", "inner")
			->join("(".$sqldeduct.") as t4", "t1.loan_id=t4.loan_id AND t1.seq=t4.intstallment_seq", "inner", false)
            ->join("coop_user as t2", "t1.creator=t2.user_id", "left")
            ->where(array('t1.loan_id' => $id))->get()->result_array();

    }

    public function getDeductInstallment($id){
    	$result = $this->db->where(array("loan_id" => $id))->order_by("intstallment_seq asc")->get("coop_loan_deduct")->result_array();
    	$data_deduct = array();
    	foreach ($result as $key => $value){
    		$data_deduct[$value['intstallment_seq']][$value['loan_deduct_list_code']] =  $value['loan_deduct_amount'];
		}
    	return $data_deduct;
	}

    public function add($arr_data){
        $res = array();
        if(sizeof($arr_data)){

        	if(self::installmentRemove($arr_data['loan_id'], $arr_data['seq'])) {

				$arr_data['creator'] = $arr_data['last_editor'] = $_SESSION['USER_ID'];
				$arr_data['createdatetime'] = date("Y-m-d H:i:s");
				$arr_data['approve_status'] = 1;
				$this->db->insert("coop_loan_installment", $arr_data);
				if ($this->db->affected_rows()) {
					$res = $this->_success;
					$res['data'] = array( "id" => $this->db->insert_id() );
					return $res;
				}
			}
        }
        return $this->_error;
    }

    public function addTransaction($data){
    	$data["loan_amount_balance"] = number_format(self::getLastTranstion($data['loan_id'])['loan_amount_balance'] + $data["add"], 2, '.', '');
        $this->loan_libraries->loan_transaction($data);
        self::updateBalanceContract($data['loan_id']);
    }

    public function updateBalanceContract($id){
        $row = self::getLastTranstion($id);

        if(sizeof($row)) {
            $data = array();
            $data['loan_amount_balance'] = $row['loan_amount_balance'];
            $this->db->set($data);
            $this->db->where("id", $id);
            $this->db->update("coop_loan");
        }
    }

    private function getLastTranstion($id){
    	return $this->db->order_by("transaction_datetime, loan_transaction_id", "desc")
			->where(array("loan_id" => $id))
			->get("coop_loan_transaction", 1)->row_array();
	}

    public function modify($id, $seq, $data = array()){
        $res = array();
        if(sizeof($data)){
            $this->db->where(array("loan_id" => $id, "seq" => $seq));
            $this->db->set($data);
            $this->db->update("coop_loan_installment");
            if($this->db->affected_rows()){
                $res = $this->_success;
                return $res;
            }
        }
        return $this->_error;
    }

    public function remove($id){
        $res = array();
        $this->db->delete("coop_loan_installment", "run_id='{$id}'");
        if($this->db->affected_rows()) {
            $res = $this->_success;
            return $res;
        }
        return $this->_error;
    }

    public function installmentRemove($loan_id, $seq){
		$this->db->delete("coop_loan_installment", "loan_id='{$loan_id}' AND seq='{$seq}'");
		if($this->db->affected_rows()) {
			return true;
		}
		return false;
	}

    public function approve_contract($arr_data, $installment){
        ini_set('precision', 16);

        $date_approve = @$arr_data['date_approve'];
        $arr_date_approve = explode("-", $date_approve);
        $date_approve_time = (@$arr_data['date_approve'] != '') ? @$date_approve." ".date('H:i:s'):date('Y-m-d H:i:s');
        $date_approve = (@$arr_data['date_approve'] != '')? @$date_approve : date('Y-m-d');
        $year_approve = (@$arr_data['date_approve'] != '')? ($arr_date_approve[0]) : date('Y');
        $month_approve = (@$arr_data['date_approve'] != '')? ($arr_date_approve[1]) : date('m');

        $this->db->select(array('t1.*','t3.loan_type_code'));
        $this->db->from("coop_loan as t1");
        $this->db->join("coop_loan_name as t2",'t1.loan_type = t2.loan_name_id','inner');
        $this->db->join("coop_loan_type as t3",'t2.loan_type_id = t3.id','inner');
        $this->db->where("t1.id = '".@$arr_data['loan_id']."'");
        $rs_loan = $this->db->get()->result_array();
        $rs_loan = $rs_loan[0];
        $member_id = @$rs_loan['member_id'];
        $loan_amount = $_loan_amount = $rs_loan['loan_amount'];
        $loan_approve_amount = $arr_data['amount'];

        //วันที่ได้รับเงิน จากหน้าขอกู้
        $date_receive_money = $date_approve_time;

        //@start บันทึกข้อมูลในตารางเก็บข้อมูลรายละเอียดการขอกู้เงิน เพื่อใช้ดูข้อมูลย้อนหลัง
        $this->db->select('salary,other_income');
        $this->db->from('coop_mem_apply');
        $this->db->where("coop_mem_apply.member_id = '".$member_id."'");
        $rs_member = $this->db->get()->result_array();
        $row_member = $rs_member[0];

        $salary = $row_member['salary']; //เงินเดือน
        $other_income = $row_member['other_income']; //รายได้อื่นๆ

        $this->db->select('share_collect_value');
        $this->db->from('coop_mem_share');
        $this->db->where("member_id = '".$member_id."' AND share_status IN('1','2')");
        $this->db->order_by('share_date DESC');
        $this->db->limit(1);
        $row_prev_share = $this->db->get()->result_array();
        $row_prev_share = @$row_prev_share[0];
        $now_share = $row_prev_share['share_collect_value']; //หุ้นที่มี่
        $rules_share = $loan_amount*20/100; //หุ้นตามหลักเกณฑ์

        //เช็คสมุดเงินฝากสีน้ำเงิน
        $this->db->select(array('coop_maco_account.account_id'));
        $this->db->from('coop_maco_account');
        $this->db->join("coop_deposit_type_setting","coop_maco_account.type_id = coop_deposit_type_setting.type_id","inner");
        $this->db->where("
				coop_maco_account.mem_id = '".$member_id."' 
				 AND coop_maco_account.account_status = '0'
				AND coop_deposit_type_setting.deduct_loan = '1'
			");
        $this->db->limit(1);
        $rs_account_blue = $this->db->get()->result_array();
        $account_id_blue =  @$rs_account_blue[0]['account_id'];
        if($account_id_blue != ''){
            $this->db->select(array('transaction_balance'));
            $this->db->from('coop_account_transaction');
            $this->db->where("account_id = '".$account_id_blue."'");
            $this->db->order_by('transaction_id DESC');
            $this->db->limit(1);
            $rs_account_blue_balance = $this->db->get()->result_array();
            $account_blue_deposit = @$rs_account_blue_balance[0]['transaction_balance'];

        }

        $data_insert = array();
        $data_insert['member_id'] = $member_id;
        $data_insert['loan_id'] = @$arr_data['loan_id'];
        $data_insert['salary'] = $salary;
        $data_insert['other_income'] = $other_income;
        $data_insert['rules_share'] = $rules_share;
        $data_insert['now_share'] = $now_share;
        $data_insert['account_blue_deposit'] = $account_blue_deposit;
        $data_insert['admin_id'] = $_SESSION['USER_ID'];
        $data_insert['createdatetime'] = date('Y-m-d H:i:s');
        $data_insert['updatetime'] = date('Y-m-d H:i:s');
        $this->db->insert('coop_loan_report_detail', $data_insert);
        //@end บันทึกข้อมูลในตารางเก็บข้อมูลรายละเอียดการขอกู้เงิน เพื่อใช้ดูข้อมูลย้อนหลัง

        if($arr_data['status_to']=='1'){
            //get receipt setting data
            $receipt_format = 1;
            $receipt_finance_setting = $this->db->select("*")->from("coop_setting_finance")->where("name = 'receipt_cashier_format' AND status = 1")->order_by("created_at DESC")->get()->row_array();
            if(!empty($receipt_finance_setting)) {
                $receipt_format = $receipt_finance_setting['value'];
            }
            if($receipt_format == 1) {
                //$date_approve_time
                $yymm = ($year_approve+543).$month_approve;

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
            } else {
                $receipt_id = $this->receipt_model->generate_receipt($date_approve_time, 1);
            }

            $receipt_arr = array();
            $this->db->select(array('t1.*'));
            $this->db->from("coop_loan_prev_deduct as t1");
            $this->db->where("t1.loan_id = '".@$arr_data['loan_id']."'");
            $row = $this->db->get()->result_array();
            //echo"<pre>";print_r($row);exit;
            $r=0;
            if(sizeof($row)) {
                foreach ($row as $key => $value) {
                    //update หนี้ห้อย-------------------
                    $extra_debt_amount = 0;//หนี้ห้อย

                    if ($extra_debt_amount) {
                        $this->db->where("loan_id", $rs_loan['id']);
                        $this->db->where("run_id", $value['run_id']);
                        $this->db->set("pay_amount", "pay_amount - " . $extra_debt_amount, false);
                        $this->db->update("coop_loan_prev_deduct");

                        $this->db->where("loan_id", $rs_loan['id']);
                        $this->db->set("estimate_receive_money", "estimate_receive_money + " . $extra_debt_amount, false);
                        $this->db->update("coop_loan_deduct_profile");

                        $this->db->where("loan_id", $rs_loan['id']);
                        $this->db->where("loan_deduct_list_code", "deduct_pay_prev_loan");
                        $this->db->set("loan_deduct_amount", "loan_deduct_amount - " . $extra_debt_amount, false);
                        $this->db->update("coop_loan_deduct");

                    }
                    //--------------------------------

                    //Get prev principal deduct.
                    $month_principal = $value['pay_amount'] - $value['interest_amount'];

                    if ($value['pay_type'] == 'all') {
                        if ($value['data_type'] == 'loan') {
                            $this->db->select(array( 't1.*' ));
                            $this->db->from("coop_loan as t1");
                            $this->db->where("t1.id = '" . @$value['ref_id'] . "'");
                            $ref_loan = $this->db->get()->result_array();
                            $ref_loan = $ref_loan[0];

                            $loan_amount = $ref_loan['loan_amount_balance'];//เงินกู้
                            $loan_type = $ref_loan['loan_type'];//ประเภทเงินกู้ใช้หา เรทดอกเบี้ย
                            $loan_id = $value['ref_id'];//ใช้หาเรทดอกเบี้ยใหม่ 26/5/2562

                            $date1 = date("Y-m-d", strtotime($ref_loan['date_last_interest']));

                            //$date2 = date("Y-m-d");//วันที่คิดดอกเบี้ย now
                            $date2 = date("Y-m-d", strtotime($date_receive_money));;//วันที่คิดดอกเบี้ย now

                            $cal_interest = array();
                            $cal_interest['loan_id'] = $value['ref_id'];
                            $cal_interest['entry_date'] = $date_receive_money;
                            $cal_interest['loan_type'] = $this->LoanCalc->get_loan_type($value['ref_id']);
                            $interest_loan = $this->LoanCalc->calc('PL', $cal_interest);
                            $interest_loan = $interest_loan['interest_arrear_bal'];

                            $loan_amount_balance = round($ref_loan['loan_amount_balance'] - $month_principal);

                            $receipt_arr[$r]['receipt_id'] = $receipt_id;
                            $receipt_arr[$r]['member_id'] = $member_id;
                            $receipt_arr[$r]['loan_id'] = $value['ref_id'];
                            $receipt_arr[$r]['account_list_id'] = '15';
                            $receipt_arr[$r]['principal_payment'] = $month_principal;
                            $receipt_arr[$r]['interest'] = $interest_loan;
                            $receipt_arr[$r]['total_amount'] = $month_principal + $interest_loan;
                            $receipt_arr[$r]['payment_date'] = $date_receive_money;
                            $receipt_arr[$r]['createdatetime'] = $date_receive_money;
                            $receipt_arr[$r]['loan_amount_balance'] = $loan_amount_balance;
                            $receipt_arr[$r]['transaction_text'] = 'หักกลบเงินกู้เลขที่สัญญา '.$ref_loan['contract_number'];
                            $receipt_arr[$r]['deduct_type'] = 'all';

                            $cal_interest = array();
                            $cal_interest['loan_id'] = $value['ref_id'];
                            $cal_interest['entry_date'] = $date_receive_money;
                            $cal_interest['loan_type'] = $this->LoanCalc->get_loan_type($value['ref_id']);
                            $cal_interest['interest'] = $interest_loan;
                            $arr_interest_loan = $this->LoanCalc->calc('CL', $cal_interest);

                            $receipt_arr[$r]['interest_cal'] = $arr_interest_loan['interest_calculate_arrears'];
                            $receipt_arr[$r]['interest_from'] = $arr_interest_loan['interest_from'];
                            $receipt_arr[$r]['interest_to'] = $arr_interest_loan['interest_to'];
                            $receipt_arr[$r]['interest_arrear'] = $arr_interest_loan['interest_arrears'];
                            $receipt_arr[$r]['interest_over'] = $arr_interest_loan['interest_arrear_bal'];
                            $receipt_arr[$r]['interest_over_new'] = $arr_interest_loan['interest_arrear_bal'];
                            $receipt_arr[$r]['interest_calculate'] = $arr_interest_loan['interest_calculate_arrears'];
                            $receipt_arr[$r]['item_type_code'] = $arr_interest_loan['loan_type_code'];

                            $r++;
                            $data_insert = array();
                            if (@$extra_debt_amount >= 1) {
                                $data_insert['loan_status'] = '1';
                                $data_insert['loan_amount_balance'] = $extra_debt_amount;//คงค้างหนี้ห้อยไว้ รอการผ่านรายการ
                            } else if ($loan_amount_balance > 0) {
                                $data_insert['loan_status'] = '1';
                                $data_insert['loan_amount_balance'] = $loan_amount_balance;
                            } else {
                                $data_insert['loan_status'] = '4';
                                $data_insert['loan_amount_balance'] = '0';
                            }
                            $this->db->where('id', $value['ref_id']);
                            $this->db->update('coop_loan', $data_insert);

                            $loan_transaction = array();
                            $loan_transaction['loan_id'] = $value['ref_id'];
                            if (@$extra_debt_amount >= 1) {
                                $loan_transaction['loan_amount_balance'] = @$extra_debt_amount;
                            } else if ($loan_amount_balance > 0) {
                                $loan_transaction['loan_amount_balance'] = $loan_amount_balance;
                            } else {
                                $loan_transaction['loan_amount_balance'] = '0';
                            }
                            $loan_transaction['transaction_datetime'] = $date_approve_time;
                            $loan_transaction['receipt_id'] = $receipt_id;
                            $loan_transaction['interest'] = $interest_loan;
                            $this->loan_libraries->loan_transaction($loan_transaction);

                            $data_insert = array();
                            $data_insert['date_last_interest'] = $date_approve_time;
                            $this->db->where('id', $value['ref_id']);
                            $this->db->update('coop_loan', $data_insert);

                        } else if ($value['data_type'] == 'atm') {
                            $this->db->select(array( 't1.*' ));
                            $this->db->from("coop_loan_atm as t1");
                            $this->db->where("
								t1.loan_atm_id = '" . $value['ref_id'] . "'
							");
                            $row_atm = $this->db->get()->result_array();
                            $row_atm = @$row_atm[0];

                            $loan_amount_balance = $row_atm['total_amount_approve'] - $row_atm['total_amount_balance'];

                            //อันเดิม
                            //$interest_loan = $this->loan_libraries->cal_atm_interest($cal_atm_interest);

                            //ดอกเบี้ยเงินกู้ตามช่วงเวลาที่มีการทำรายการ
                            $cal_atm_interest = array();
                            $cal_atm_interest['loan_atm_id'] = $value['ref_id'];
                            $cal_atm_interest['entry_date'] = $date_receive_money;
                            $interest_atm_loan = $this->ATMCalc->calc('CL', $cal_atm_interest);

                            $interest_loan = $interest_atm_loan['interest_arrear_bal'];

                            //รายการที่มีการผ่านรายการแล้ว
                            $receipt_arr[$r]['receipt_id'] = $receipt_id;
                            $receipt_arr[$r]['member_id'] = $member_id;
                            $receipt_arr[$r]['loan_atm_id'] = $value['ref_id'];
                            $receipt_arr[$r]['account_list_id'] = '31';
                            $receipt_arr[$r]['principal_payment'] = $loan_amount_balance;
                            $receipt_arr[$r]['interest'] = $interest_loan;
                            $receipt_arr[$r]['interest_debt'] = @$value['interest_debt'];
                            //$receipt_arr[$r]['total_amount'] = $loan_amount_balance+$interest_loan;
                            $receipt_arr[$r]['total_amount'] = $month_principal + @$interest_loan + @$value['interest_debt'];
                            $receipt_arr[$r]['payment_date'] = $date_receive_money;
                            $receipt_arr[$r]['createdatetime'] = $date_receive_money;
                            $receipt_arr[$r]['loan_amount_balance'] = '0';
                            $receipt_arr[$r]['transaction_text'] = 'หักกลบเงินกู้เลขที่สัญญา ' . $row_atm['contract_number'];
                            $receipt_arr[$r]['deduct_type'] = 'all';
                            $r++;
                            $data_insert = array();
                            $data_insert['loan_status'] = '1';
                            $data_insert['loan_amount_balance'] = '0';
                            $this->db->where('loan_atm_id', $value['ref_id']);
                            $this->db->update('coop_loan_atm_detail', $data_insert);

                            $data_insert = array();
                            if ($this->Setting_model->get('close_atm_for_deduct') == '1') {
                                $data_insert['loan_atm_status'] = '3';
                            }
                            $data_insert['total_amount_balance'] = $row_atm['total_amount_approve'];
                            $this->db->where('loan_atm_id', $value['ref_id']);
                            $this->db->update('coop_loan_atm', $data_insert);

                            $atm_transaction = array();
                            $atm_transaction['loan_atm_id'] = $value['ref_id'];
                            $atm_transaction['loan_amount_balance'] = '0';
                            $atm_transaction['transaction_datetime'] = $date_receive_money;
                            $atm_transaction['receipt_id'] = $receipt_id;
                            //$this->loan_libraries->atm_transaction($atm_transaction);
                            $this->loan_libraries->atm_transaction($atm_transaction);

                            $data_insert = array();
                            $data_insert['date_last_interest'] = date('Y-m-d H:i:s');
                            $this->db->where('loan_atm_id', $value['ref_id']);
                            $this->db->update('coop_loan_atm', $data_insert);
                        }
                    } else if ($value['pay_type'] == 'principal') {
                        if ($value['data_type'] == 'loan') {
                            $this->db->select(array( 't1.*' ));
                            $this->db->from("coop_loan as t1");
                            $this->db->where("
								t1.id = '" . $value['ref_id'] . "'
							");
                            $row_loan = $this->db->get()->result_array();
                            $row_loan = @$row_loan[0];

                            $loan_amount_balance = ($row_loan['loan_amount_balance'] - $value['pay_amount']) + @$extra_debt_amount;

                            $data_insert = array();
                            $data_insert['loan_amount_balance'] = $loan_amount_balance;
                            $this->db->where('id', $value['ref_id']);
                            $this->db->update('coop_loan', $data_insert);

                            $receipt_arr[$r]['receipt_id'] = $receipt_id;
                            $receipt_arr[$r]['member_id'] = $member_id;
                            $receipt_arr[$r]['loan_id'] = $value['ref_id'];
                            $receipt_arr[$r]['account_list_id'] = '15';
                            $receipt_arr[$r]['principal_payment'] = $month_principal;
                            $receipt_arr[$r]['total_amount'] = $month_principal;
                            $receipt_arr[$r]['payment_date'] = $date_receive_money;
                            $receipt_arr[$r]['createdatetime'] = $date_receive_money;
                            $receipt_arr[$r]['loan_amount_balance'] = $loan_amount_balance;
                            $receipt_arr[$r]['transaction_text'] = 'หักกลบเงินกู้เลขที่สัญญา ' . $row_loan['contract_number'];
                            $receipt_arr[$r]['deduct_type'] = 'principal';

                            $cal_interest = array();
                            $cal_interest['loan_id'] = $value['ref_id'];
                            $cal_interest['entry_date'] = $date_receive_money;
                            $cal_interest['loan_type'] = $this->LoanCalc->get_loan_type($value['ref_id']);
                            $arr_interest_loan = $this->LoanCalc->calc('PL', $cal_interest);

                            $receipt_arr[$r]['interest_cal'] = $arr_interest_loan['interest_calculate_arrears'];
                            $receipt_arr[$r]['interest_from'] = $arr_interest_loan['interest_from'];
                            $receipt_arr[$r]['interest_to'] = $arr_interest_loan['interest_to'];
                            $receipt_arr[$r]['interest_arrear'] = $arr_interest_loan['interest_arrears'];
                            $receipt_arr[$r]['interest_over'] = 0;
                            $receipt_arr[$r]['interest_over_new'] = $arr_interest_loan['interest_arrear_bal'];
                            $receipt_arr[$r]['interest_calculate'] = $arr_interest_loan['interest_calculate_arrears'];
                            $receipt_arr[$r]['item_type_code'] = $arr_interest_loan['loan_type_code'];
                            $r++;
                            $loan_transaction = array();
                            $loan_transaction['loan_id'] = $value['ref_id'];
                            $loan_transaction['loan_amount_balance'] = $loan_amount_balance;
                            $loan_transaction['transaction_datetime'] = $date_receive_money;
                            $loan_transaction['receipt_id'] = $receipt_id;
                            $loan_transaction['interest_over'] = 0;
                            $this->loan_libraries->loan_transaction($loan_transaction);
                        } else if ($value['data_type'] == 'atm') {
                            $this->db->select(array( 't1.*' ));
                            $this->db->from("coop_loan_atm_detail as t1");
                            $this->db->where("
								t1.loan_atm_id = '" . $value['ref_id'] . "'
								AND loan_status = '0'
							");
                            $this->db->order_by('loan_id ASC');
                            $row_loan = $this->db->get()->result_array();
                            $pay_amount = $value['pay_amount'];
                            foreach ($row_loan as $key2 => $value2) {
                                if ($pay_amount > $value2['loan_amount_balance']) {
                                    $data_insert = array();
                                    $data_insert['loan_amount_balance'] = 0;
                                    $data_insert['loan_status'] = '1';
                                    $this->db->where('loan_id', $value2['loan_id']);
                                    $this->db->update('coop_loan_atm_detail', $data_insert);
                                    $pay_amount = $pay_amount - $value2['loan_amount_balance'];
                                } else {
                                    $data_insert = array();
                                    $data_insert['loan_amount_balance'] = $value2['loan_amount_balance'] - $pay_amount;
                                    $this->db->where('loan_id', $value2['loan_id']);
                                    $this->db->update('coop_loan_atm_detail', $data_insert);
                                    $pay_amount = 0;
                                }
                                if ($pay_amount == 0) {
                                    break;
                                }
                            }
                            $this->db->select(array( 't1.*' ));
                            $this->db->from("coop_loan_atm as t1");
                            $this->db->where("
								t1.loan_atm_id = '" . $value['ref_id'] . "'
							");
                            $row_loan = $this->db->get()->result_array();

                            $data_insert = array();
                            $data_insert['total_amount_balance'] = $row_loan[0]['total_amount_balance'] + $value['pay_amount'];
                            $data_insert['loan_atm_status'] = '4';
                            $this->db->where('loan_atm_id', $value['ref_id']);
                            $this->db->update('coop_loan_atm', $data_insert);

                            $loan_amount_balance = $row_loan[0]['total_amount_approve'] - ($row_loan[0]['total_amount_balance'] + $value['pay_amount']);

                            $calc_arrears = array();
                            $calc_arrears['loan_atm_id'] = $value['ref_id'];
                            $calc_arrears['entry_date'] = $date_receive_money;
                            $arr_insert_atm = $this->ATMCalc->calc('PL', $calc_arrears);

                            $receipt_arr[$r]['interest_cal'] = $arr_insert_atm['interest_calculate_arrears'];
                            $receipt_arr[$r]['interest_from'] = $arr_insert_atm['interest_from'];
                            $receipt_arr[$r]['interest_to'] = $arr_insert_atm['interest_to'];
                            $receipt_arr[$r]['interest_arrear'] = $arr_insert_atm['interest_arrears'];
                            $receipt_arr[$r]['interest_over'] = 0;
                            $receipt_arr[$r]['interest_over_new'] = $arr_insert_atm['interest_arrear_bal'];
                            $receipt_arr[$r]['interest_calculate'] = $arr_insert_atm['interest_calculate_arrears'];
                            $receipt_arr[$r]['item_type_code'] = $arr_insert_atm['loan_type_code'];

                            $atm_transaction = array();
                            $atm_transaction['loan_atm_id'] = $value['ref_id'];
                            $atm_transaction['loan_amount_balance'] = $loan_amount_balance;
                            $atm_transaction['transaction_datetime'] = $date_approve_time;
                            $atm_transaction['receipt_id'] = $receipt_id;
                            $atm_transaction['interest_over'] = 0;
                            $atm_transaction['interest'] = $arr_insert_atm['interest_arrear_bal'];
                            $this->loan_libraries->atm_transaction($atm_transaction);

                            $receipt_arr[$r]['receipt_id'] = $receipt_id;
                            $receipt_arr[$r]['member_id'] = $member_id;
                            $receipt_arr[$r]['loan_atm_id'] = $value['ref_id'];
                            $receipt_arr[$r]['account_list_id'] = '31';
                            $receipt_arr[$r]['principal_payment'] = $month_principal;
                            $receipt_arr[$r]['total_amount'] = $month_principal;
                            $receipt_arr[$r]['payment_date'] = $date_receive_money;
                            $receipt_arr[$r]['createdatetime'] = $date_receive_money;
                            $receipt_arr[$r]['loan_amount_balance'] = $loan_amount_balance;
                            $receipt_arr[$r]['transaction_text'] = 'หักกลบเงินกู้เลขที่สัญญา ' . $row_loan[0]['contract_number'];
                            $receipt_arr[$r]['deduct_type'] = 'principal';
                            $r++;
                        }
                    }
                }
            }
            ///////////////////////////////////////////////////////////
            $this->db->select(array('t1.*','t2.account_list_id','t3.account_list'));
            $this->db->from("coop_loan_deduct as t1");
            $this->db->join("coop_loan_deduct_list as t2",'t1.loan_deduct_list_code = t2.loan_deduct_list_code','inner');
            $this->db->join("coop_account_list as t3",'t2.account_list_id = t3.account_id','left');
            $this->db->where("
					t1.loan_id = '".$arr_data['loan_id']."' AND t1.loan_deduct_list_code != 'deduct_pay_prev_loan'
				");
            $row_deduct = $this->db->get()->result_array();

            foreach($row_deduct as $key => $value) {

                 if ($value['loan_deduct_list_code'] == 'deduct_share' && $value['loan_deduct_amount'] > 0) {
                    $this->db->select('*');
                    $this->db->from('coop_share_setting');
                    $this->db->order_by('setting_id DESC');
                    $row = $this->db->get()->result_array();
                    $share_setting = $row[0];

                    $this->db->select('*');
                    $this->db->from('coop_mem_share');
                    $this->db->where("member_id = '" . $member_id . "' AND share_status = '1'");
                    $this->db->order_by('share_date DESC, share_id DESC');
                    $this->db->limit(1);
                    $row_share = $this->db->get()->result_array();
                    $row_share = @$row_share[0];
                    $data_insert = array();
                    $data_insert['member_id'] = $member_id;
                    $data_insert['admin_id'] = $_SESSION['USER_ID'];
                    $data_insert['share_type'] = 'SPL';
                    $data_insert['share_date'] = $date_receive_money;
                    $data_insert['share_payable'] = @$row_share['share_collect'];
                    $data_insert['share_payable_value'] = @$row_share['share_collect_value'];
                    $data_insert['share_early'] = $value['loan_deduct_amount'] / $share_setting['setting_value'];
                    $data_insert['share_early_value'] = $value['loan_deduct_amount'];
                    $data_insert['share_collect'] = @$row_share['share_collect'] + ($value['loan_deduct_amount'] / $share_setting['setting_value']);
                    $data_insert['share_collect_value'] = @$row_share['share_collect_value'] + @$value['loan_deduct_amount'];
                    $data_insert['share_value'] = $share_setting['setting_value'];
                    $data_insert['share_status'] = '1';
                    $data_insert['pay_type'] = @$data['pay_type'];
                    $data_insert['share_bill'] = @$receipt_id;

                    $this->db->insert('coop_mem_share', $data_insert);

                    $receipt_arr[$r]['receipt_id'] = $receipt_id;
                    $receipt_arr[$r]['member_id'] = $member_id;
                    $receipt_arr[$r]['account_list_id'] = '14';
                    $receipt_arr[$r]['principal_payment'] = $value['loan_deduct_amount'];
                    $receipt_arr[$r]['total_amount'] = $value['loan_deduct_amount'];
                    $receipt_arr[$r]['payment_date'] = $date_receive_money;
                    $receipt_arr[$r]['createdatetime'] = $date_receive_money;
                    $receipt_arr[$r]['loan_amount_balance'] = @$row_share['share_collect_value'] + @$value['loan_deduct_amount'];
                    $receipt_arr[$r]['transaction_text'] = 'หุ้น';
                    $receipt_arr[$r]['deduct_type'] = 'all';
                    $r++;
                } else if ($value['loan_deduct_list_code'] == 'buy_share' && $value['loan_deduct_amount'] > 0) {
                    $this->db->select('*');
                    $this->db->from('coop_share_setting');
                    $this->db->order_by('setting_id DESC');
                    $row = $this->db->get()->result_array();
                    $share_setting = $row[0];

                    $this->db->select('*');
                    $this->db->from('coop_mem_share');
                    $this->db->where("member_id = '" . $member_id . "' AND share_status = '1'");
                    $this->db->order_by('share_date DESC, share_id DESC');
                    $this->db->limit(1);
                    $row_share = $this->db->get()->result_array();
                    $row_share = @$row_share[0];
                    $data_insert = array();
                    $data_insert['member_id'] = $member_id;
                    $data_insert['admin_id'] = $_SESSION['USER_ID'];
                    $data_insert['share_type'] = 'SPL';
                    $data_insert['share_date'] = $date_receive_money;
                    $data_insert['share_payable'] = @$row_share['share_collect'];
                    $data_insert['share_payable_value'] = @$row_share['share_collect_value'];
                    $data_insert['share_early'] = $value['loan_deduct_amount'] / $share_setting['setting_value'];
                    $data_insert['share_early_value'] = $value['loan_deduct_amount'];
                    $data_insert['share_collect'] = @$row_share['share_collect'] + ($value['loan_deduct_amount'] / $share_setting['setting_value']);
                    $data_insert['share_collect_value'] = @$row_share['share_collect_value'] + @$value['loan_deduct_amount'];
                    $data_insert['share_value'] = $share_setting['setting_value'];
                    $data_insert['share_status'] = '1';
                    $data_insert['pay_type'] = @$data['pay_type'];
                    $data_insert['share_bill'] = @$receipt_id;

                    $this->db->insert('coop_mem_share', $data_insert);

                    $receipt_arr[$r]['receipt_id'] = $receipt_id;
                    $receipt_arr[$r]['member_id'] = $member_id;
                    $receipt_arr[$r]['account_list_id'] = '37';
                    $receipt_arr[$r]['principal_payment'] = $value['loan_deduct_amount'];
                    $receipt_arr[$r]['total_amount'] = $value['loan_deduct_amount'];
                    $receipt_arr[$r]['payment_date'] = $date_receive_money;
                    $receipt_arr[$r]['createdatetime'] = $date_receive_money;
                    $receipt_arr[$r]['loan_amount_balance'] = @$row_share['share_collect_value'] + @$value['loan_deduct_amount'];
                    $receipt_arr[$r]['transaction_text'] = 'ซื้อหุ้นจากการกู้';
                    $receipt_arr[$r]['deduct_type'] = 'all';
                    $r++;
                }else if($value['loan_deduct_list_code'] == 'deduct_before_interest' && $value['loan_deduct_amount'] > 0){
                    $receipt_arr[$r]['receipt_id'] = $receipt_id;
                    $receipt_arr[$r]['member_id'] = $member_id;
                    $receipt_arr[$r]['loan_id'] = $arr_data['loan_id'];
                    $receipt_arr[$r]['account_list_id'] = $value['account_list_id'];
                    $receipt_arr[$r]['interest'] = $value['loan_deduct_amount'];
                    $receipt_arr[$r]['principal_payment'] = 0;
                    $receipt_arr[$r]['total_amount'] = $value['loan_deduct_amount'];
                    $receipt_arr[$r]['payment_date'] = $date_receive_money;
                    $receipt_arr[$r]['createdatetime'] = $date_receive_money;
                    $receipt_arr[$r]['loan_amount_balance'] = $loan_approve_amount;
                    $receipt_arr[$r]['transaction_text'] = 'ชำระดอกเบี้ยเงินกู้';
                    $receipt_arr[$r]['deduct_type'] = 'interest';
                    $receipt_arr[$r]['period_count'] = 1;
                    $r++;
                }else{
                    if($value['loan_deduct_amount']>0){
                        $receipt_arr[$r]['receipt_id'] = $receipt_id;
                        $receipt_arr[$r]['member_id'] = $member_id;
                        $receipt_arr[$r]['account_list_id'] = $value['account_list_id'];
                        $receipt_arr[$r]['principal_payment'] = $value['loan_deduct_amount'];
                        $receipt_arr[$r]['total_amount'] = $value['loan_deduct_amount'];
                        $receipt_arr[$r]['payment_date'] = $date_receive_money;
                        $receipt_arr[$r]['createdatetime'] = $date_receive_money;
                        $receipt_arr[$r]['loan_amount_balance'] = '';
                        $receipt_arr[$r]['transaction_text'] = $value['account_list'];
                        $receipt_arr[$r]['deduct_type'] = 'all';
                        $r++;
                    }
                }
            }

            //insert coop_loan_transaction สัญญาใหม่
            $loan_transaction = array();
            $loan_transaction['loan_id'] = $arr_data['loan_id'];
            $loan_transaction['loan_amount_balance'] = $loan_approve_amount;
            $loan_transaction['transaction_datetime'] = $date_receive_money;
            $loan_transaction['receipt_id'] = 'NEW';
            $this->loan_libraries->loan_transaction($loan_transaction);

            $sum_count = 0;
            $interest_over_new = 0;
            foreach($receipt_arr as $key => $value){
                $data_insert = array();
                $data_insert['receipt_id'] = $value['receipt_id'];
                $data_insert['receipt_list'] = $value['account_list_id'];
                $data_insert['receipt_count'] = $value['total_amount'];
                $this->db->insert('coop_receipt_detail', $data_insert);

                //บันทึกการชำระเงิน
                $data_insert = array();
                $data_insert['receipt_id'] = $value['receipt_id'];
                $data_insert['member_id'] = @$value['member_id'];
                $data_insert['loan_id'] = @$value['loan_id'];
                $data_insert['loan_atm_id'] = @$value['loan_atm_id'];
                $data_insert['account_list_id'] = $value['account_list_id'];
                $data_insert['principal_payment'] = @$value['principal_payment'];
                $data_insert['interest'] = @$value['interest'];
                $data_insert['total_amount'] = @$value['total_amount'];
                $data_insert['payment_date'] = @$value['payment_date'];
                $data_insert['loan_amount_balance'] = @$value['loan_amount_balance'];
                $data_insert['createdatetime'] = @$value['createdatetime'];
                $data_insert['transaction_text'] = @$value['transaction_text'];
                $data_insert['deduct_type'] = @$value['deduct_type'];
                $this->db->insert('coop_finance_transaction', $data_insert);
                $sum_count += @$value['total_amount'];
                $sum_interest += @$value['interest'];

            }

            if($sum_count>0){
                $data_insert = array();
                $data_insert['receipt_id'] = $receipt_id;
                $data_insert['member_id'] = @$member_id;
                $data_insert['admin_id'] = @$_SESSION['USER_ID'];
                $data_insert['sumcount'] = $sum_count;
                $data_insert['receipt_datetime'] = $date_receive_money;
                $data_insert['pay_type'] = '1'; //0=เงินสด 1=โอนเงิน
                $this->db->insert('coop_receipt', $data_insert);

                $data_insert = array();
                $data_insert['deduct_receipt_id'] = $receipt_id;
                $this->db->where('id',$arr_data['loan_id']);
                $this->db->update('coop_loan',$data_insert);
            }

        }

        $data_insert = array();
        if($arr_data['status_to']=='1'){
            //ปีในการ gen เลขสัญญา
            $rs_month_account = $this->db->select('accm_month_ini')
                ->from("coop_account_period_setting")
                ->limit(1)
                ->get()->result_array();
            $month_account = $rs_month_account[0]['accm_month_ini'];
            $month_now = date('m');

            if((int)$month_now >= (int)$month_account && (int)$month_account != 1){
                $year = (date('Y')+543)+1;
            }else{
                $year = (date('Y')+543);
            }

            $new_contact_number = $this->loan_libraries->get_contract_number($year, $date_approve, $rs_loan['loan_type']);
            if(empty($rs_loan["contract_number"])) {
                $data_insert['contract_number'] = @$new_contact_number;
            }
            //echo $new_contact_number;exit;

        }

        //Check if compromise loan change status_to to 8 if loanee got responsibility
        $compormise_detail = $this->db->select("*")
            ->from("coop_loan_guarantee_compromise")
            ->where("loan_id = '".$arr_data['loan_id']."' AND type in (3,4)")
            ->get()->row();
        if(!empty($compormise_detail)) {
            $data_insert['loan_status'] = 8;
        } else {
            $data_insert['loan_status'] = @$arr_data['status_to'];
        }

        $data_insert['approve_date'] = $date_receive_money;
        $data_insert['loan_amount_balance'] = $loan_approve_amount;
        $data_insert['date_last_interest'] = date("Y-m-d",strtotime($date_receive_money));
        $this->db->where('id', @$arr_data['loan_id']);
        $this->db->update('coop_loan', $data_insert);

        //อัพเดตเลขที่สัญญาในระบบประกัน
        $life_insurance = $this->db->select("*")
            ->from("coop_life_insurance")
            ->where("loan_id = '".$arr_data['loan_id']."' AND insurance_status = 0")
            ->get()->row();
        if(!empty($life_insurance)) {
            $data_insert = array();
            $data_insert['insurance_date'] = $date_approve_time;
            $data_insert['contract_number'] = @$new_contact_number;
            $data_insert['insurance_status'] = 1;
            $this->db->where("member_id = '".@$member_id."' AND loan_id = '".$arr_data['loan_id']."'");
            $this->db->update('coop_life_insurance',$data_insert);

            if($arr_data['status_to']=='5'){
                //ลบข้อมูลระบบประกันชีวิต เมื่อไม่อนุมัติ
                $this->db->where("member_id = '".@$member_id."' AND loan_id = '".$arr_data['loan_id']."'");
                $this->db->delete("coop_life_insurance");
            }
        }

        //$this->center_function->toast('บันทึกข้อมูลเรียบร้อยแล้ว');
        //echo "<script> document.location.href='".base_url(PROJECTPATH.'/loan/loan_approve')."' </script>";
    }

    public function addChequeInstallment($data = array(), $cheque = array(), $ref = 0){
    	if(self::checkChequeInstallment($data["loan_id"], $ref)){
    		$this->db->where(["loan_id" => $data["loan_id"], "installment_seq" => $ref]);
    		$this->db->delete("coop_loan_cheque");
		}

    	$_cheque = array();
    	if(sizeof($cheque)) {
			foreach ($cheque as $key => $val) {
				$_cheque[$val['seq']][$val['type']] = join("", explode(",", $val['value']));
			}
		}

    	if(sizeof($_cheque)){
    		foreach ($_cheque as $key => $val){
				$data_insert = array();
				$data_insert['user_id'] = $_SESSION['USER_ID'];
				$data_insert['createdatetime'] = $data['approve_date'];
				$data_insert['installment_seq'] = $ref;
				$data_insert['seq'] = $key;
				$data_insert['loan_id'] = $data['loan_id'];
				$data_insert['amount'] = $val['amount'];
				$data_insert['receiver'] = $val['receiver'];
				$this->db->insert('coop_loan_cheque', $data_insert);
			}
		}

	}

	public function checkChequeInstallment($id, $seq){
		return $this->db->where(["loan_id" => $id, "seq" => $seq])->count_all_results("coop_loan_cheque") >= 1;
	}

	public function checkSeq($id, $seq){
    	return $this->db->where(["loan_id" => $id, "seq" => $seq])->count_all_results("coop_loan_installment") === 1;
	}

	private function getSeqCheque($loanId){
		return $this->db->order_by("seq", "desc")->get_where('coop_loan_cheque',
			array('loan_id' => $loanId),1)->row()->seq;
	}

	public function addLoanDeduct($data = array(), $seq_no = 1, $deduct_id){
		if (sizeof($data)) {
			$data_insert = array();
			$num =0;
			foreach ($data as $key => $amount) {
					if($key != "loan_id") {
						$data_insert[$num]['loan_deduct_list_code'] = $key;
						$data_insert[$num]['loan_deduct_amount'] = join("", explode(",", $amount));
						$data_insert[$num]['loan_id'] = $data['loan_id'];
						$data_insert[$num]['intstallment_seq'] = $seq_no;
						$data_insert[$num]['loan_deduct_id'] = $deduct_id;
					}
				$num++;
			}
			if (sizeof($data_insert)) {
				foreach ($data_insert as $key => $item){
					if(self::checkDeductList($item)){
						self::clearDeductList($item);
					}
					$this->db->insert("coop_loan_deduct", $item);
				}
				//$this->db->insert_batch("coop_loan_deduct", $data_insert);
			}
		}
	}

	public function checkDeductList($item = array()){
    	return $this->db->where(array("loan_id" => $item['loan_id']
		,"intstallment_seq" => $item['intstallment_seq']
		,"loan_deduct_list_code" => $item['loan_deduct_list_code']))
		->count_all_results("coop_loan_deduct") === 1;
	}

	public function clearDeductList($item = array()){
    	$this->db->where(array("loan_id" => $item['loan_id']
		,"intstallment_seq" => $item['intstallment_seq']
		,"loan_deduct_list_code" => $item['loan_deduct_list_code']));
    	$this->db->delete("coop_loan_deduct");
	}

	public function addDeductProfile($data = array()){
    	$data_insert = array();
    	$data_insert['loan_id'] = $data['loan_id'];
    	$data_insert['intstallment_seq'] = $data['seq'];
    	$data_insert['pay_per_month'] = $data['pay_per_month'];
    	$data_insert['date_receive_money'] = $data['date_receive_money'];
    	$data_insert['date_first_period'] = $data['date_first_period'];
    	$data_insert['first_interest'] = $data['interest'];
    	$data_insert['estimate_receive_money'] = $data['estimate'];
    	self::clearDeductProfile($data_insert);
    	$this->db->insert("coop_loan_deduct_profile", $data_insert);
    	return $this->db->insert_id();
	}

	private function createProfile($data = array()){
    	$result = array();
    	$installment = $data['installment'];
    	$loan = $data['loan'];
    	$contract = $this->contract->findContract($installment['loan_id']);
    	$result['loan_id'] = $installment['loan_id'];
    	$result['seq'] = $installment['seq'];
    	$result['date_receive_money'] = $loan['date_approve'];
    	$result['date_first_period'] = $contract->date_start_period;;
    	$result['interest'] = $loan['interest'];
    	$result['estimate'] = $loan['esitmate'];
    	$result['pay_per_month'] = $contract->money_period_1;
    	return $result;
	}

	private function clearDeductProfile($item){
    	$this->db->where(array("loan_id" => $item['loan_id']
		,"intstallment_seq" => $item['intstallment_seq']))
		->delete("coop_loan_deduct_profile");
	}

	public function getLoanDedectList(){
    	return $this->db->where(array("deduct_type" => "deduct", "loan_deduct_status" => 1, "loan_installment_show" => 1))
		->order_by("loan_installment_order", "asc")
		->get("coop_loan_deduct_list")->result_array();
	}

	public function profileDeduct($loan_id){
    	$profile = $this->db->order_by("intstallment_seq asc")->get_where("coop_loan_deduct_profile", ["loan_id" => $loan_id])->result_array();
    	$result = array();
    	foreach ($profile as $key => $item){
    		$result[$item["intstallment_seq"]] = $item;
		}
    	return $result;
	}

	public function addReceipt($data = array(), $sum_count = 0){
		if($sum_count>0) {
			$data_insert = array();
			$data_insert['receipt_id'] = $data['receipt_id'];
			$data_insert['member_id'] = $data['member_id'];
			$data_insert['admin_id'] = @$_SESSION['USER_ID'];
			$data_insert['sumcount'] = $sum_count;
			$data_insert['receipt_datetime'] = $data['receipt_datetime'];
			$data_insert['pay_type'] = '1'; //0=เงินสด 1=โอนเงิน
			$this->db->insert('coop_receipt', $data_insert);
		}
	}

	public function addReceiptDetail($receipt_arr = array()){
    	if(sizeof($receipt_arr)) {
			foreach ($receipt_arr as $key => $value) {
				$data_insert = array();
				$data_insert['receipt_id'] = $value['receipt_id'];
				$data_insert['receipt_list'] = $value['account_list_id'];
				$data_insert['receipt_count'] = $value['total_amount'];
				$this->db->insert('coop_receipt_detail', $data_insert);

				//บันทึกการชำระเงิน
				self::addFinanceTransaction($value);

				$sum_count += @$value['total_amount'];
				$sum_interest += @$value['interest'];
			}
			$receipt =  $receipt_arr[0];
			$data_receipt = array();
			$data_receipt['member_id'] = $receipt['member_id'];
			$data_receipt['receipt_id'] = $receipt['receipt_id'];
			$data_receipt['receipt_datetime'] = $receipt['createdatetime'];
			self::addReceipt($data_receipt, $sum_count);
		}
	}

	public function addFinanceTransaction($data = array()){
		$data_insert = array();
		$data_insert['receipt_id'] = @$data['receipt_id'];
		$data_insert['member_id'] = @$data['member_id'];
		$data_insert['loan_id'] = @$data['loan_id'];
		$data_insert['loan_atm_id'] = @$data['loan_atm_id'];
		$data_insert['account_list_id'] = $data['account_list_id'];
		$data_insert['principal_payment'] = @$data['principal_payment'];
		$data_insert['interest'] = @$data['interest'];
		$data_insert['total_amount'] = @$data['total_amount'];
		$data_insert['payment_date'] = @$data['payment_date'];
		$data_insert['loan_amount_balance'] = @$data['loan_amount_balance'];
		$data_insert['createdatetime'] = @$data['createdatetime'];
		$data_insert['transaction_text'] = @$data['transaction_text'];
		$data_insert['deduct_type'] = @$data['deduct_type'];
		$this->db->insert('coop_finance_transaction', $data_insert);
	}

	public function addPayment($data, $seq=0, $receipt_id = "")
	{

		$member_id = $this->contract->findContract($data['loan_id'])->member_id;
		$date_receive_money = self::checkTime($data['loan_id'], $data['date_approve']);
		$loan_approve_amount = self::getLastTranstion($data['loan_id'])['loan_amount_balance'];

		$this->db->select(array( 't1.*', 't2.account_list_id', 't3.account_list' ));
		$this->db->from("coop_loan_deduct as t1");
		$this->db->join("coop_loan_deduct_list as t2", 't1.loan_deduct_list_code = t2.loan_deduct_list_code', 'inner');
		$this->db->join("coop_account_list as t3", 't2.account_list_id = t3.account_id', 'left');
		$this->db->where("
					t1.loan_id = '" . $data['loan_id'] . "' AND t1.intstallment_seq='{$seq}' AND t1.loan_deduct_list_code != 'deduct_pay_prev_loan'
				");
		$row_deduct = $this->db->get()->result_array();
		$receipt_arr = array();
		$r=0;
		foreach ($row_deduct as $key => $value) {

			if ($value['loan_deduct_list_code'] == 'deduct_share' && $value['loan_deduct_amount'] > 0) {
				$this->db->select('*');
				$this->db->from('coop_share_setting');
				$this->db->order_by('setting_id DESC');
				$row = $this->db->get()->result_array();
				$share_setting = $row[0];

				$this->db->select('*');
				$this->db->from('coop_mem_share');
				$this->db->where("member_id = '" . $member_id . "' AND share_status = '1'");
				$this->db->order_by('share_date DESC, share_id DESC');
				$this->db->limit(1);
				$row_share = $this->db->get()->result_array();
				$row_share = @$row_share[0];
				$data_insert = array();
				$data_insert['member_id'] = $member_id;
				$data_insert['admin_id'] = $_SESSION['USER_ID'];
				$data_insert['share_type'] = 'SPL';
				$data_insert['share_date'] = $date_receive_money;
				$data_insert['share_payable'] = @$row_share['share_collect'];
				$data_insert['share_payable_value'] = @$row_share['share_collect_value'];
				$data_insert['share_early'] = $value['loan_deduct_amount'] / $share_setting['setting_value'];
				$data_insert['share_early_value'] = $value['loan_deduct_amount'];
				$data_insert['share_collect'] = @$row_share['share_collect'] + ($value['loan_deduct_amount'] / $share_setting['setting_value']);
				$data_insert['share_collect_value'] = @$row_share['share_collect_value'] + @$value['loan_deduct_amount'];
				$data_insert['share_value'] = $share_setting['setting_value'];
				$data_insert['share_status'] = '1';
				$data_insert['pay_type'] = @$data['pay_type'];
				$data_insert['share_bill'] = @$receipt_id;

				$this->db->insert('coop_mem_share', $data_insert);

				$receipt_arr[$r]['receipt_id'] = $receipt_id;
				$receipt_arr[$r]['member_id'] = $member_id;
				$receipt_arr[$r]['account_list_id'] = '14';
				$receipt_arr[$r]['principal_payment'] = $value['loan_deduct_amount'];
				$receipt_arr[$r]['total_amount'] = $value['loan_deduct_amount'];
				$receipt_arr[$r]['payment_date'] = $date_receive_money;
				$receipt_arr[$r]['createdatetime'] = $date_receive_money;
				$receipt_arr[$r]['loan_amount_balance'] = @$row_share['share_collect_value'] + @$value['loan_deduct_amount'];
				$receipt_arr[$r]['transaction_text'] = 'หุ้น';
				$receipt_arr[$r]['deduct_type'] = 'all';
				$r++;
			} else if ($value['loan_deduct_list_code'] == 'buy_share' && $value['loan_deduct_amount'] > 0) {
				$this->db->select('*');
				$this->db->from('coop_share_setting');
				$this->db->order_by('setting_id DESC');
				$row = $this->db->get()->result_array();
				$share_setting = $row[0];

				$this->db->select('*');
				$this->db->from('coop_mem_share');
				$this->db->where("member_id = '" . $member_id . "' AND share_status = '1'");
				$this->db->order_by('share_date DESC, share_id DESC');
				$this->db->limit(1);
				$row_share = $this->db->get()->result_array();
				$row_share = @$row_share[0];
				$data_insert = array();
				$data_insert['member_id'] = $member_id;
				$data_insert['admin_id'] = $_SESSION['USER_ID'];
				$data_insert['share_type'] = 'SPL';
				$data_insert['share_date'] = $date_receive_money;
				$data_insert['share_payable'] = @$row_share['share_collect'];
				$data_insert['share_payable_value'] = @$row_share['share_collect_value'];
				$data_insert['share_early'] = $value['loan_deduct_amount'] / $share_setting['setting_value'];
				$data_insert['share_early_value'] = $value['loan_deduct_amount'];
				$data_insert['share_collect'] = @$row_share['share_collect'] + ($value['loan_deduct_amount'] / $share_setting['setting_value']);
				$data_insert['share_collect_value'] = @$row_share['share_collect_value'] + @$value['loan_deduct_amount'];
				$data_insert['share_value'] = $share_setting['setting_value'];
				$data_insert['share_status'] = '1';
				$data_insert['pay_type'] = @$data['pay_type'];
				$data_insert['share_bill'] = @$receipt_id;

				$this->db->insert('coop_mem_share', $data_insert);

				$receipt_arr[$r]['receipt_id'] = $receipt_id;
				$receipt_arr[$r]['member_id'] = $member_id;
				$receipt_arr[$r]['account_list_id'] = '37';
				$receipt_arr[$r]['principal_payment'] = $value['loan_deduct_amount'];
				$receipt_arr[$r]['total_amount'] = $value['loan_deduct_amount'];
				$receipt_arr[$r]['payment_date'] = $date_receive_money;
				$receipt_arr[$r]['createdatetime'] = $date_receive_money;
				$receipt_arr[$r]['loan_amount_balance'] = @$row_share['share_collect_value'] + @$value['loan_deduct_amount'];
				$receipt_arr[$r]['transaction_text'] = 'ซื้อหุ้นจากการกู้';
				$receipt_arr[$r]['deduct_type'] = 'all';
				$r++;
			} else if ($value['loan_deduct_list_code'] == 'deduct_before_interest' && $value['loan_deduct_amount'] > 0) {
				$receipt_arr[$r]['receipt_id'] = $receipt_id;
				$receipt_arr[$r]['member_id'] = $member_id;
				$receipt_arr[$r]['loan_id'] = $data['loan_id'];
				$receipt_arr[$r]['account_list_id'] = $value['account_list_id'];
				$receipt_arr[$r]['interest'] = $value['loan_deduct_amount'];
				$receipt_arr[$r]['principal_payment'] = 0;
				$receipt_arr[$r]['total_amount'] = $value['loan_deduct_amount'];
				$receipt_arr[$r]['payment_date'] = $date_receive_money;
				$receipt_arr[$r]['createdatetime'] = $date_receive_money;
				$receipt_arr[$r]['loan_amount_balance'] = $loan_approve_amount;
				$receipt_arr[$r]['transaction_text'] = 'ชำระดอกเบี้ยเงินกู้';
				$receipt_arr[$r]['deduct_type'] = 'interest';
				$receipt_arr[$r]['period_count'] = 1;
				$r++;
			} else {
				if ($value['loan_deduct_amount'] > 0) {
					$receipt_arr[$r]['receipt_id'] = $receipt_id;
					$receipt_arr[$r]['member_id'] = $member_id;
					$receipt_arr[$r]['account_list_id'] = $value['account_list_id'];
					$receipt_arr[$r]['principal_payment'] = $value['loan_deduct_amount'];
					$receipt_arr[$r]['total_amount'] = $value['loan_deduct_amount'];
					$receipt_arr[$r]['payment_date'] = $date_receive_money;
					$receipt_arr[$r]['createdatetime'] = $date_receive_money;
					$receipt_arr[$r]['loan_amount_balance'] = '';
					$receipt_arr[$r]['transaction_text'] = $value['account_list'];
					$receipt_arr[$r]['deduct_type'] = 'all';
					$r++;
				}
			}
		}
		self::addReceiptDetail($receipt_arr);
	}

	public function checkTime($loan_id, $datetime){
    	$row = $this->db->order_by("transaction_datetime, loan_transaction_id desc")
			->get_where("coop_loan_transaction",
			array('loan_id' => $loan_id, 'transaction_datetime' => $datetime), 1)
			->row_array();

    	if(!empty($row)){
			if(date_create($row['transaction_datetime']) >= date_create($datetime)){
				if(date("H:i:s", strtotime($row['transaction_datetime'])) == "00:00:00"){
					return date("Y-m-d H:i:s", strtotime($datetime." ".date("H:i:s")));
				}else{
					return date("Y-m-d H:i:s", strtotime($row['transaction_datetime']. " +1 seconds"));
				}
			}
		}
    	return $datetime;
	}

	public function getInstallment($loan_id, $seq){
    	return $this->db->get_where("coop_loan_installment", array('loan_id' => $loan_id,'seq' => $seq), 1)->row_array();
	}

	/**
	 * Get Cheque List Item
	 *
	 * @param $loan_id
	 * @return array
	 */
	public function getChequeList($loan_id){
    	$list = $this->db->get_where("coop_loan_cheque", [
    		'loan_id' => $loan_id
		])->result_array();

    	$chequeList = array();
    	foreach ($list as $key => $val){
    		$chequeList[$val['installment_seq']][$val['seq']] = $val;
		}
    	return $chequeList;
	}


}
