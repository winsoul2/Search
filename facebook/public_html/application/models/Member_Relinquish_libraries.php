<?php if( ! defined('BASEPATH')) exit('No direct script access allowed');

class Member_Relinquish_libraries extends CI_Model {
	public function __construct()
	{
		parent::__construct();
		//$this->load->database();
		# Load libraries
		//$this->load->library('parser');
		$this->load->helper(array('html', 'url'));
		$this->load->model("Deposit_seq_model", "deposit_seq");
    }

    public function save_dismiss($data) {
        $data['user_id'] = $_SESSION['USER_ID'];

		if($data['delete_resign']=='1'){
			$this->db->where('req_resign_id', $data['req_resign_id']);
			$this->db->delete('coop_mem_req_resign');
			$this->center_function->toast("ยกเลิกรายการเรียบร้อยแล้ว");
		}else{
			unset($data['delete_resign']);
			$req_resign_date_arr = explode('/',$data['req_resign_date']);
			$data['req_resign_date'] = ($req_resign_date_arr[2]-543)."-".$req_resign_date_arr[1]."-".$req_resign_date_arr[0];
			$resign_date_arr = explode('/',$data['resign_date']);
			$data['resign_date'] = ($resign_date_arr[2]-543)."-".$resign_date_arr[1]."-".$resign_date_arr[0];
			if($data['req_resign_id']=='' || $data['req_resign_status']=='2'){
				$this->db->select('req_resign_no');
				$this->db->from('coop_mem_req_resign');
				$this->db->order_by('req_resign_id DESC');
				$this->db->limit(1);
				$row = $this->db->get()->result_array();
				if(!empty($row)){
					$req_resign_no = (int)$row[0]['req_resign_no']+1;
				}else{
					$req_resign_no = 1;
				}

				$data['req_resign_no'] = sprintf('% 06d',$req_resign_no);
				$data['req_resign_status'] = '0';

				unset($data['req_resign_id']);

				$this->db->insert('coop_mem_req_resign', $data);
			}else{
				$this->db->where('req_resign_id', $data['req_resign_id']);
				unset($data['req_resign_id']);
				$this->db->update('coop_mem_req_resign', $data);
			}
			$this->center_function->toast("บันทึกข้อมูลเรียบร้อยแล้ว");
		}
    }

    public function approve($data) {
        if($this->input->post()){
			if(empty($data["is_fired_process"])) {
                $this->db->select('member_id, resign_date');
                $this->db->from('coop_mem_req_resign');
                $this->db->where("req_resign_id = '".$this->input->post('req_resign_id')."'");
                $row = $this->db->get()->result_array();

                $member_id = @$row[0]['member_id'];
                $resign_date = @$row[0]['resign_date'];

				$process_timestamp = $resign_date.' 00:00:00';
				$datenow_timestamp = date('Y-m-d H:i:s');
				$req_resign_id = $this->input->post('req_resign_id');
				$data['approve_date'] = $datenow_timestamp;
				$data['approve_user_id'] = $_SESSION['USER_ID'];

				$this->db->where('req_resign_id', $data['req_resign_id']);
				unset($data['req_resign_id']);
				$this->db->update('coop_mem_req_resign', $data);

				if($data['req_resign_status'] == '1'){
					
					$data_member = array();
					$data_member['member_status'] = '2';
					$data_member['mem_type'] = '2';

					$this->db->where('member_id', $row[0]['member_id']);
					$this->db->update('coop_mem_apply', $data_member);

					$receipt_id = $this->receipt_model->generate_receipt($process_timestamp, 1);
					
					$receipt_arr = array();
					$r = 0;
					//การกู้เงิน
					$this->db->select('*');
					$this->db->from('coop_loan');
					$this->db->where("member_id = '".$member_id."' AND loan_status = '1'");
					$rs = $this->db->get()->result_array();
					$row_loan = @$rs;
					
					$loan_principal = 0;
					$loan_interest = 0;
					if(!empty($resign_date)){
                        $date_interesting = $resign_date;
                    }else{
                        $date_interesting = date('Y-m-d');
                    }
					foreach($row_loan as $key => $value){
						//Get Interest
						$loan_amount = $value['loan_amount_balance'];//เงินกู้
						$loan_type = $value['loan_type'];//ประเภทเงินกู้ใช้หา เรทดอกเบี้ย

						$curr_BE = date("Y") + 543;
						$finance_month = $this->db->select("*")
													->from("coop_finance_month_detail as t1")
													->join("coop_finance_month_profile as t2", "t1.profile_id = t2.profile_id")
													->where("t1.loan_id = '".$value['id']."' AND t2.profile_year = '".$curr_BE."' AND t2.profile_month = '".date("m")."' AND t1.run_status = 1")
													->get()->row();
						$interest_data = 0;
						if(empty($finance_month)) {
							$rs_data = $this->db->get_where('coop_loan', array('id' => $value['id']))->row_array();
							$date1 = $rs_data['date_last_interest'];

							$date2 = $date_interesting;
							$interest_data = $this->loan_libraries->calc_interest_loan($loan_amount, $loan_type, $date1, $date2);
							$interest_data = $interest_data > 0 ? round($interest_data) : 0;
						} else {
							$interest_data = 0;
						}

						//Get refrain if exist
						$year = date("Y") + 543;
						$month = date("m");
						$loan_refrain = $this->db->select("refrain_loan_id, refrain_type")
													->from("coop_refrain_loan")
													->where("loan_id = '".$value['id']."' AND status != 2 AND refrain_type IN (2,3) AND (year_start < ".$year." || (year_start = ".$year." AND month_start <= ".$month."))
																AND ((year_end > ".$year." || (year_end = ".$year." AND month_start >= ".$month.") || period_type = 2))")
													->get()->result_array();
						if(!empty($loan_refrain) && !empty($interest_data)) {
							$data_insert = array();
							$data_insert["refrain_loan_id"] = $loan_refrain[0]["refrain_loan_id"];
							$data_insert["member_id"] = $member_id;
							$data_insert["pay_type"] = "interest";
							$data_insert["org_value"] = $interest_data;
							$data_insert["paid_value"] = 0;
							$data_insert["status"] = 1;
							$data_insert["paid_date"] = $process_timestamp;
							$data_insert["receipt_id"] = $receipt_id;
							$data_insert["createdatetime"] = $datenow_timestamp;
							$data_insert["updatedatetime"] = $datenow_timestamp;
							$this->db->insert('coop_loan_refrain_history', $data_insert);
							$interest_data = 0;
						}

						//Get loan interest non pay
						$loan_interest_remain = $this->db->select("loan_id, SUM(non_pay_amount_balance) as sum")
															->from("coop_non_pay_detail")
															->where("loan_id = '".$value['id']."' AND pay_type = 'interest'")
															->get()->row();

						$receipt_arr[$r]['receipt_id'] = @$receipt_id;
						$receipt_arr[$r]['member_id'] = @$member_id;
						$receipt_arr[$r]['loan_id'] = @$value['id'];
						$receipt_arr[$r]['account_list_id'] = '15';
						$receipt_arr[$r]['principal_payment'] = @$value['loan_amount_balance'];
						$receipt_arr[$r]['interest'] = $interest_data;
						$receipt_arr[$r]['total_amount'] = $value['loan_amount_balance']+$interest_data;
						$receipt_arr[$r]['payment_date'] = date('Y-m-d');
						$receipt_arr[$r]['createdatetime'] = $datenow_timestamp;
						$receipt_arr[$r]['loan_amount_balance'] = '0';
						$receipt_arr[$r]['transaction_text'] = 'ชำระเงินกู้เลขที่สัญญา '.@$value['contract_number'];
						$receipt_arr[$r]['deduct_type'] = 'all';
						$receipt_arr[$r]['contract_number'] = $value['contract_number'];
						if(!empty($loan_interest_remain)) {
							$receipt_arr[$r]['loan_amount_interest_debt'] = $loan_interest_remain->sum;
							$receipt_arr[$r]['total_amount'] += $loan_interest_remain->sum;
						}
						
						$r++;
					}

					//การกู้เงินฉุกเฉิน ATM
					$this->db->select(array('*'));
					$this->db->from('coop_loan_atm');
					$this->db->where("member_id = '".$member_id."' AND loan_atm_status = '1'");
					$this->db->order_by("loan_atm_id DESC");
					$rs_atm = $this->db->get()->result_array();
					foreach($rs_atm as $key_atm => $row_atm){
						//Get Interest
						$curr_BE = date("Y") + 543;
						$finance_month = $this->db->select("*")
													->from("coop_finance_month_detail as t1")
													->join("coop_finance_month_profile as t2", "t1.profile_id = t2.profile_id")
													->where("t1.loan_atm_id = '".$row_atm['loan_atm_id']."' AND t2.profile_year = '".$curr_BE."' AND t2.profile_month = '".date("m")."' AND t1.run_status = 1")
													->get()->row();
						$interest_data = 0;
						if(empty($finance_month)) {
							$cal_atm_interest = array();
							$cal_atm_interest['loan_atm_id'] = $row_atm['loan_atm_id'];
							$cal_atm_interest['date_interesting'] = date('Y-m-d');
							$interest_data = $this->loan_libraries->cal_atm_interest_report_test($cal_atm_interest,"echo", array("month"=> date("m"), "year" => date("Y") ), false, true )['interest_month'];
						} else {
							$interest_data = 0;
						}
						//Get total debt
						$row_detail = $this->db->select(array(
															't1.loan_id',
															't1.loan_amount_balance',
															't1.loan_date',
															't1.date_last_pay',
															't1.date_last_interest'
														))
												->from('coop_loan_atm_detail as t1')
												->where("
													t1.loan_atm_id = '".$row_atm['loan_atm_id']."' 
													AND t1.transfer_status = '1' 
												")
												->get()->result_array();
						$loan_amount_balance = 0;
						foreach($row_detail as $key_detail => $value_detail){
							$loan_amount_balance += $value_detail['loan_amount_balance'];
						}

						//Get loan interest non pay
						$loan_interest_remain = $this->db->select("loan_id, SUM(non_pay_amount_balance) as sum")
															->from("coop_non_pay_detail")
															->where("loan_atm_id = '".$row_atm['loan_atm_id']."' AND pay_type = 'interest'")
															->get()->row();

						$receipt_arr[$r]['receipt_id'] = @$receipt_id;
						$receipt_arr[$r]['member_id'] = @$member_id;
						$receipt_arr[$r]['loan_atm_id'] = @$row_atm['loan_atm_id'];
						$receipt_arr[$r]['account_list_id'] = '31';
						$receipt_arr[$r]['principal_payment'] = @$loan_amount_balance;
						$receipt_arr[$r]['interest'] = @$interest_data;
						$receipt_arr[$r]['total_amount'] = @$loan_amount_balance+@$interest_data;
						$receipt_arr[$r]['payment_date'] = date('Y-m-d');
						$receipt_arr[$r]['createdatetime'] = $datenow_timestamp;
						$receipt_arr[$r]['loan_amount_balance'] = '0';
						$receipt_arr[$r]['transaction_text'] = 'ชำระเงินกู้เลขที่สัญญา '.$row_atm['contract_number'];
						$receipt_arr[$r]['deduct_type'] = 'all';
						$receipt_arr[$r]['contract_number'] = $row_atm['contract_number'];
						if(!empty($loan_interest_remain)) {
							$receipt_arr[$r]['loan_amount_interest_debt'] = $loan_interest_remain->sum;
							$receipt_arr[$r]['total_amount'] += $loan_interest_remain->sum;
						}
						$r++;							
					}	

					//บันทึกการชำระเงิน	
					$sum_count = 0;
					foreach($receipt_arr as $key => $value){
						$data_insert = array();
						$data_insert['receipt_id'] = $value['receipt_id'];
						$data_insert['receipt_list'] = $value['account_list_id'];
						$data_insert['receipt_count'] = $value['total_amount'];
						$this->db->insert('coop_receipt_detail', $data_insert);
						
						//บันทึกการชำระเงิน
						$data_insert = array();
						$data_insert['receipt_id'] = $value['receipt_id'];
						$data_insert['member_id'] = $value['member_id'];
						$data_insert['loan_id'] = $value['loan_id'];
						$data_insert['loan_atm_id'] = $value['loan_atm_id'];
						$data_insert['account_list_id'] = $value['account_list_id'];
						$data_insert['principal_payment'] = $value['principal_payment'];
						$data_insert['interest'] = $value['interest'];
						$data_insert['total_amount'] = $value['total_amount'];
						$data_insert['payment_date'] = $value['payment_date'];
						$data_insert['loan_amount_balance'] = $value['loan_amount_balance'];
						$data_insert['createdatetime'] = $value['createdatetime'];
						$data_insert['transaction_text'] = $value['transaction_text'];
						$data_insert['deduct_type'] = $value['deduct_type'];
						$data_insert['loan_interest_remain'] = $value['loan_amount_interest_debt'];
						$this->db->insert('coop_finance_transaction', $data_insert);
						$sum_count += $value['total_amount'];							
						
						if($value['loan_id'] != ''){
							$loan_amount_balance = 0;
							$data_insert = array();
							$data_insert['loan_amount_balance'] = $loan_amount_balance;
							$data_insert['loan_status'] = '4';
							$this->db->where('id', $value['loan_id']);
							$this->db->update('coop_loan', $data_insert);

							$data_insert = array();
							$data_insert['loan_id'] = $value['loan_id'];
							$data_insert['loan_amount_balance'] = $loan_amount_balance;
							$data_insert['transaction_datetime'] = $process_timestamp;
							$data_insert['receipt_id'] = $value['receipt_id'];
							$this->db->insert("coop_loan_transaction", $data_insert);

							//Resign Loan Detail
							$data_insert = array();
							$data_insert['member_id'] = $member_id;
							$data_insert['loan_id'] = $value['loan_id'];
							$data_insert['contract_number'] = $value['contract_number'];
							$data_insert['loan_amount_principal'] = $value['principal_payment'];
							$data_insert['loan_amount_interest'] = $value['interest'];
							$data_insert['loan_amount_interest_debt'] = $value['loan_amount_interest_debt'];
							$data_insert['loan_amount_debt'] = 0;
							$data_insert['receipt_id'] = $value['receipt_id'];
							$data_insert['careate_datetime'] = $datenow_timestamp;
							$data_insert['update_datetime'] = $datenow_timestamp;
							$this->db->insert("coop_resign_loan_detail", $data_insert);
						}

						if($value['loan_atm_id'] != ''){						
							
							$this->db->select(array(
								'loan_id',
								'loan_amount_balance'
							));
							$this->db->from('coop_loan_atm_detail');
							$this->db->where("loan_atm_id = '".$value['loan_atm_id']."' AND loan_status = '0'");
							$this->db->order_by('loan_id ASC');
							$row = $this->db->get()->result_array();
							foreach($row as $key_atm => $value_atm){										
								$data_insert = array();
								$data_insert['loan_amount_balance'] = 0;
								$data_insert['date_last_pay'] = date('Y-m-d');
								$this->db->where('loan_id', $value_atm['loan_id']);
								$this->db->update('coop_loan_atm_detail', $data_insert);
								$principal_payment = 0;	
							}
							
							$this->db->select(array(
								'total_amount_approve',
								'total_amount_balance',
								'total_amount'
							));
							$this->db->from('coop_loan_atm');
							$this->db->where("loan_atm_id = '".$value['loan_atm_id']."'");
							$row = $this->db->get()->result_array();
							$row_loan_atm = $row[0];
							
							$data_insert = array();
							$data_insert['total_amount_balance'] = $row_loan_atm['total_amount'];
							$data_insert['loan_atm_status'] = 4;
							$this->db->where('loan_atm_id', $value['loan_atm_id']);
							$this->db->update('coop_loan_atm', $data_insert);

							$data_insert = array();
							$data_insert['loan_atm_id'] = $value['loan_atm_id'];
							$data_insert['loan_amount_balance'] = 0;
							$data_insert['transaction_datetime'] = $process_timestamp;
							$data_insert['receipt_id'] = $value['receipt_id'];
							$this->db->insert("coop_loan_atm_transaction", $data_insert);

                            $loan_amount_balance = 0;

							//Resign Loan Detail
							$data_insert = array();
							$data_insert['member_id'] = $member_id;
							$data_insert['loan_atm_id'] = $value['loan_atm_id'];
							$data_insert['contract_number'] = $value['contract_number'];
							$data_insert['loan_amount_principal'] = $value['principal_payment'];
							$data_insert['loan_amount_interest'] = $value['interest'];
							$data_insert['loan_amount_interest_debt'] = $value['loan_amount_interest_debt'];
							$data_insert['loan_amount_debt'] = 0;
							$data_insert['receipt_id'] = $value['receipt_id'];
							$data_insert['careate_datetime'] = $datenow_timestamp;
							$data_insert['update_datetime'] = $datenow_timestamp;
							$this->db->insert("coop_resign_loan_detail", $data_insert);
						}
					}

					if($sum_count>0){
						$data_insert = array();
						$data_insert['receipt_id'] = $receipt_id;
						$data_insert['member_id'] = $member_id;
						$data_insert['admin_id'] = $_SESSION['USER_ID'];
						$data_insert['sumcount'] = $sum_count;
						$data_insert['receipt_datetime'] = $process_timestamp;
						$this->db->insert('coop_receipt', $data_insert);

						$data_insert = array();
						$data_insert['receipt_id'] = $receipt_id;
						$this->db->where('req_resign_id', $req_resign_id);
						$this->db->update('coop_mem_req_resign', $data_insert);
					}

					$non_pays = $this->db->select("non_pay_id")
													->from("coop_non_pay")
													->where("non_pay_amount_balance > 0 AND member_id = '".$member_id."'")
													->get()->result_array();
					$non_pay_receipts = array();
					foreach($non_pays as $non_pay) {
						$non_pay_receipt = array();
						$non_pay_receipt["member_id"] = $member_id;
						$non_pay_receipt["non_pay_id"] = $non_pay["non_pay_id"];
						$non_pay_receipt["receipt_id"] = $receipt_id;
						$non_pay_receipt["createdatetime"] = $datenow_timestamp;
						$non_pay_receipt["updatetime"] = $datenow_timestamp;
						$non_pay_receipt["receipt_status"] = 0;
						$non_pay_receipts[] = $non_pay_receipt;
                    }
                    if(!empty($non_pay_receipts)) {
                        $this->db->insert_batch('coop_non_pay_receipt', $non_pay_receipts);
                    }

					$non_pay_details = $this->db->select("t1.member_id,
															t1.loan_id,
															t1.loan_atm_id,
															t1.pay_type,
															t2.non_pay_month,
															t2.non_pay_year,
															t1.non_pay_amount as principal,
															t1.non_pay_amount_balance as principal_balance,
															t3.non_pay_amount as interest,
															t3.non_pay_amount_balance as interest_balance")
												->from("coop_non_pay_detail as t1")
												->join("coop_non_pay as t2", "t1.non_pay_id = t2.non_pay_id", "inner")
												->join("coop_non_pay_detail as t3", "t1.non_pay_id = t3.non_pay_id AND t1.loan_id = t3.loan_id AND t1.loan_atm_id = t3.loan_atm_id AND t3.pay_type = 'interest'", "left")
												->where("t1.member_id = '{$member_id}' AND t1.non_pay_amount_balance > 0 AND t1.pay_type = 'principal'")
												->get()->result_array();
					$detail_inserts = array();
					foreach($non_pay_details as $detail) {
						$detail_insert = array();
						$detail_insert["member_id"] = $member_id;
						$detail_insert["loan_id"] = $detail["loan_id"];
						$detail_insert["loan_atm_id"] = $detail["loan_atm_id"];
						$detail_insert["month"] = $detail["non_pay_month"];
						$detail_insert["year"] = $detail["non_pay_year"];
						$detail_insert["principal"] = $detail["principal"];
						$detail_insert["principal_balance"] = $detail["principal_balance"];
						$detail_insert["interest"] = $detail["interest"];
						$detail_insert["interest_balance"] = $detail["interest_balance"];
						$detail_inserts[] = $detail_insert;
                    }
                    if(!empty($detail_inserts)) {
                       $this->db->insert_batch('coop_resign_non_pay_detail', $detail_inserts);
                    }

					$data_insert = array();
					$data_insert['non_pay_amount_balance'] = 0;
					$this->db->where('member_id', $member_id);
					$this->db->update('coop_non_pay_detail', $data_insert);

					$data_insert = array();
					$data_insert['non_pay_amount_balance'] = 0;
					$data_insert['non_pay_status'] = 2;
					$this->db->where('member_id', $member_id);
					$this->db->update('coop_non_pay', $data_insert);

					//Bank Account
					$transaction_list = 'CW';
					$accounts = $this->db->select("*")
										->from("coop_maco_account")
										->where("mem_id = '".$member_id."' AND account_status = '0'")
										->get()->result_array();
					$total_account_amount = 0;

					foreach($accounts as $account) {
						$cal_result = $this->deposit_libraries->cal_deposit_interest_by_acc_date($account['account_id'], $process_timestamp);
						$close_account_interest = $cal_result['interest'];
						$close_account_interest_return = $cal_result['interest_return'];

						$row = $this->db->select(array('transaction_balance','transaction_no_in_balance'))
										->from('coop_account_transaction')
										->where("account_id = '".$account['account_id']."'")
										->order_by('transaction_time DESC, transaction_id DESC')
										->limit(1)
										->get()->result_array();
						$row_transaction = $row[0];
						$transaction_balance = $row_transaction['transaction_balance'];
						$transaction_no_in_balance = $row_transaction['transaction_no_in_balance'];

						//Deposit Interest
						if($close_account_interest > 0){
							$transaction_balance = $transaction_balance+$close_account_interest;
							$data_insert_in = array();
							$data_insert_in['transaction_time'] = $process_timestamp;
							$data_insert_in['transaction_list'] = 'IN';
							$data_insert_in['transaction_withdrawal'] = '0';
							$data_insert_in['transaction_deposit'] = $close_account_interest;
							$data_insert_in['transaction_balance'] = $transaction_balance;
							$data_insert_in['transaction_no_in_balance'] = $transaction_no_in_balance;
							$data_insert_in['user_id'] = $_SESSION['USER_ID'];
							$data_insert_in['account_id'] = $account['account_id'];
							
							//ดึงข้อมูลลำดับรายการ ของรายการถัดไป
							$arr_seq = array(); 
							$arr_seq['account_id'] = $data_insert_in['account_id'] ; 
							$arr_seq['transaction_list'] = $data_insert_in['transaction_list'];
							$seq_no = $this->deposit_seq->gen_seq_account_transaction($arr_seq);
							$data_insert_in['seq_no'] = @$seq_no;

							$this->db->insert('coop_account_transaction', $data_insert_in);
						}
						//Deduct Interest
						if($close_account_interest_return > 0){
							$transaction_balance = $transaction_balance-$close_account_interest_return;
							$data_insert_in = array();
							$data_insert_in['transaction_time'] = $process_timestamp;
							$data_insert_in['transaction_list'] = 'WTI';
							$data_insert_in['transaction_withdrawal'] = $close_account_interest_return;
							$data_insert_in['transaction_deposit'] = 0;
							$data_insert_in['transaction_balance'] = $transaction_balance;
							$data_insert_in['transaction_no_in_balance'] = $transaction_no_in_balance;
							$data_insert_in['user_id'] = $_SESSION['USER_ID'];
							$data_insert_in['account_id'] = $account['account_id'];

							//ดึงข้อมูลลำดับรายการ ของรายการถัดไป
							$arr_seq = array(); 
							$arr_seq['account_id'] = $data_insert_in['account_id'] ; 
							$arr_seq['transaction_list'] = $data_insert_in['transaction_list'];
							$seq_no = $this->deposit_seq->gen_seq_account_transaction($arr_seq);
							$data_insert_in['seq_no'] = @$seq_no;

							$this->db->insert('coop_account_transaction', $data_insert_in);
						}
						
						if($transaction_balance > 0){
							$data_insert = array();
							$data_insert['transaction_time'] = $process_timestamp;
							$data_insert['transaction_list'] = $transaction_list;
							$data_insert['transaction_withdrawal'] = $transaction_balance;
							$data_insert['transaction_deposit'] = '0';
							$data_insert['transaction_balance'] = '0';
							$data_insert['transaction_no_in_balance'] = '0';
							$data_insert['user_id'] = $_SESSION['USER_ID'];
							$data_insert['account_id'] = $account['account_id'];

							//ดึงข้อมูลลำดับรายการ ของรายการถัดไป
							$arr_seq = array(); 
							$arr_seq['account_id'] = $data_insert['account_id'] ; 
							$arr_seq['transaction_list'] = $data_insert['transaction_list'];
							$seq_no = $this->deposit_seq->gen_seq_account_transaction($arr_seq);
							$data_insert['seq_no'] = @$seq_no;

							$this->db->insert('coop_account_transaction', $data_insert);

							$total_account_amount += $transaction_balance;
						}
						
						$data_insert = array();
						$data_insert['account_amount'] = '0';
						$data_insert['account_status'] = '1';
						$data_insert['sequester_status'] = '1';
						$data_insert['sequester_status_atm'] = '1';
						$data_insert['close_account_date'] = $process_timestamp;
						$data_insert['close_account_pay_type'] = "0";
						$this->db->where('account_id', $account['account_id']);
						$this->db->update('coop_maco_account',$data_insert);
					}

					$data_insert = array();
					$data_insert["income_code"] = "income_deposit";
					$data_insert["income_amount"] = $total_account_amount;
					$data_insert["member_id"] = $member_id;
					$data_insert["receipt_id"] = $receipt_id;
					$this->db->insert('coop_resign_income_detail', $data_insert);

					//Share
					$share_setting = $this->db->select("*")
												->from("coop_share_setting")
												->get()->row();
					$share = $this->db->select("*")
										->from("coop_mem_share")
										->where("member_id = '".$member_id."'")
										->order_by("share_date DESC, share_id DESC")
										->get()->row();
					$data_insert = array();
					$data_insert['member_id'] = $member_id;
					$data_insert['share_date'] = $process_timestamp;
					$data_insert['admin_id'] = $_SESSION['USER_ID'];	
					$data_insert['share_type'] = "SRP";
					$data_insert['share_status'] = "5";
					$data_insert['share_payable'] = $share->share_collect;
					$data_insert['share_payable_value'] = $share->share_collect_value;
					$data_insert['share_early'] = $share->share_collect;
					$data_insert['share_early_value'] = $share->share_collect_value;
					$data_insert['share_collect'] = 0;
					$data_insert['share_collect_value'] = 0;
					$data_insert['share_value'] = $share_setting->setting_value;
					$this->db->insert('coop_mem_share', $data_insert);

					$data_insert = array();
					$data_insert["income_code"] = "income_share";
					$data_insert["income_amount"] = $share->share_collect;
					$data_insert["member_id"] = $member_id;
					$data_insert["receipt_id"] = $receipt_id;
					$this->db->insert('coop_resign_income_detail', $data_insert);
					$data_insert = array();
					$data_insert["income_code"] = "balance_share";
					$data_insert["income_amount"] = $share->share_collect;
					$data_insert["member_id"] = $member_id;
					$data_insert["receipt_id"] = $receipt_id;
					$this->db->insert('coop_resign_income_detail', $data_insert);

					$data_insert = array();
					$data_insert["income_code"] = "income_amount";
					$data_insert["income_amount"] = $share->share_collect_value + $total_account_amount;
					$data_insert["member_id"] = $member_id;
					$data_insert["receipt_id"] = $receipt_id;
					$this->db->insert('coop_resign_income_detail', $data_insert);

					//Update cremation status if exist
					$this->change_cremation_member_type($member_id, 2);
				}
				echo "<script> document.location.href = '".PROJECTPATH."/resignation/resignation_approve' </script>";
			} else {
                $this->db->select('member_id, resign_date');
                $this->db->from('coop_mem_req_resign');
                $this->db->where("req_resign_id = '".$this->input->post('req_resign_id')."'");
                $row = $this->db->get()->result_array();

                $member_id = @$row[0]['member_id'];
                $resign_date = @$row[0]['resign_date'];

                $process_timestamp = $resign_date.' 00:00:00';
                $datenow_timestamp = date('Y-m-d H:i:s');
                if(!empty($resign_date)){
                    $date_interesting = $resign_date;
                }else{
                    $date_interesting = date('Y-m-d');
                }

				$data_req = array();
				$data_req['req_resign_status'] = '1';
				$data_req['conclusion'] = $data['conclusion'];

				$req_resign_id = $this->input->post('req_resign_id');
				$data_req['approve_date'] = $datenow_timestamp;
				$data_req['approve_user_id'] = $_SESSION['USER_ID'];
				$data_req['req_resign_id'] = $req_resign_id;

				$this->db->where('req_resign_id', $data_req['req_resign_id']);
				unset($data_req['req_resign_id']);
				$this->db->update('coop_mem_req_resign', $data_req);

				if($data['req_resign_status'] == '1'){
					$this->db->select('member_id');
					$this->db->from('coop_mem_req_resign');
					$this->db->where("req_resign_id = '".$this->input->post('req_resign_id')."'");
					$row = $this->db->get()->result_array();
					$resign_date = strtotime($row['req_resign_date']." 00:00:00");

					$data_member = array();
					$data_member['member_status'] = '1';
					$data_member['mem_type'] = '5';
					$this->db->where('member_id', $row[0]['member_id']);
					$this->db->update('coop_mem_apply', $data_member);

					$member_id = @$row[0]['member_id'];

					$receipt_id = $this->receipt_model->generate_receipt($process_timestamp, 1);

					//Collect non pay data
					$non_pay_details = $this->db->select("t1.member_id,
															t1.loan_id,
															t1.loan_atm_id,
															t1.pay_type,
															t2.non_pay_month,
															t2.non_pay_year,
															t1.non_pay_amount as principal,
															t1.non_pay_amount_balance as principal_balance,
															t3.non_pay_amount as interest,
															t3.non_pay_amount_balance as interest_balance")
												->from("coop_non_pay_detail as t1")
												->join("coop_non_pay as t2", "t1.non_pay_id = t2.non_pay_id", "inner")
												->join("coop_non_pay_detail as t3", "t1.non_pay_id = t3.non_pay_id AND t1.loan_id = t3.loan_id AND t1.loan_atm_id = t3.loan_atm_id AND t3.pay_type = 'interest'", "left")
												->where("t1.member_id = '{$member_id}' AND t1.non_pay_amount_balance > 0 AND t1.pay_type = 'principal'")
												->get()->result_array();
					$detail_inserts = array();
					foreach($non_pay_details as $detail) {
						$detail_insert = array();
						$detail_insert["member_id"] = $member_id;
						$detail_insert["loan_id"] = $detail["loan_id"];
						$detail_insert["loan_atm_id"] = $detail["loan_atm_id"];
						$detail_insert["month"] = $detail["non_pay_month"];
						$detail_insert["year"] = $detail["non_pay_year"];
						$detail_insert["principal"] = $detail["principal"];
						$detail_insert["principal_balance"] = $detail["principal_balance"];
						$detail_insert["interest"] = $detail["interest"];
						$detail_insert["interest_balance"] = $detail["interest_balance"];
						$detail_inserts[] = $detail_insert;
                    }
                    if(!empty($detail_inserts)) {
                        $this->db->insert_batch('coop_resign_non_pay_detail', $detail_inserts);
                    }

					$receipt_arr = array();
					$r = 0;
					//การกู้เงิน
					$this->db->select('*');
					$this->db->from('coop_loan');
					$this->db->where("member_id = '".$member_id."' AND loan_status = '1'");
					$rs = $this->db->get()->result_array();
					$row_loan = @$rs;
					$loan_principal = 0;
					$loan_interest = 0;
					foreach($row_loan as $key => $value){
						//Get Interest
						$loan_amount = $value['loan_amount_balance'];//เงินกู้
						$loan_type = $value['loan_type'];//ประเภทเงินกู้ใช้หา เรทดอกเบี้ย

						$curr_BE = date("Y") + 543;
						$finance_month = $this->db->select("*")
													->from("coop_finance_month_detail as t1")
													->join("coop_finance_month_profile as t2", "t1.profile_id = t2.profile_id")
													->where("t1.loan_id = '".$value['id']."' AND t2.profile_year = '".$curr_BE."' AND t2.profile_month = '".date("m")."' AND t1.run_status = 1")
													->get()->row();
						$interest_data = 0;
						if(empty($finance_month)) {
							$rs_data = $this->db->get_where('coop_loan', array('id' => $value['id']))->row_array();
							$date1 = $rs_data['date_last_interest'];

							$date2 = $date_interesting;
							$interest_data = $this->loan_libraries->calc_interest_loan($loan_amount, $loan_type, $date1, $date2);
							$interest_data = $interest_data > 0 ? round($interest_data) : 0;
						} else {
							$interest_data = 0;
						}

						//Get refrain if exist
						$year = date("Y") + 543;
						$month = date("m");
						$loan_refrain = $this->db->select("refrain_loan_id, refrain_type")
													->from("coop_refrain_loan")
													->where("loan_id = '".$value['id']."' AND status != 2 AND refrain_type IN (2,3) AND (year_start < ".$year." || (year_start = ".$year." AND month_start <= ".$month."))
																AND ((year_end > ".$year." || (year_end = ".$year." AND month_start >= ".$month.") || period_type = 2))")
													->get()->result_array();
						if(!empty($loan_refrain) && !empty($interest_data)) {
							$data_insert = array();
							$data_insert["refrain_loan_id"] = $loan_refrain[0]["refrain_loan_id"];
							$data_insert["member_id"] = $member_id;
							$data_insert["pay_type"] = "interest";
							$data_insert["org_value"] = $interest_data;
							$data_insert["paid_value"] = 0;
							$data_insert["status"] = 1;
							$data_insert["paid_date"] = $process_timestamp;
							$data_insert["receipt_id"] = $receipt_id;
							$data_insert["createdatetime"] = $datenow_timestamp;
							$data_insert["updatedatetime"] = $datenow_timestamp;
							$this->db->insert('coop_loan_refrain_history', $data_insert);
							$interest_data = 0;
						}

						//Get loan interest non pay
						$loan_interest_remain = $this->db->select("loan_id, SUM(non_pay_amount_balance) as sum")
															->from("coop_non_pay_detail")
															->where("loan_id = '".$value['id']."' AND pay_type = 'interest'")
															->get()->row();

						$receipt_arr[$r]['receipt_id'] = @$receipt_id;
						$receipt_arr[$r]['member_id'] = @$member_id;
						$receipt_arr[$r]['loan_id'] = @$value['id'];
						$receipt_arr[$r]['account_list_id'] = '15';
						$receipt_arr[$r]['principal_payment'] = @$value['loan_amount_balance'];
						$receipt_arr[$r]['interest'] = $interest_data;
						$receipt_arr[$r]['total_amount'] = $value['loan_amount_balance']+$interest_data;
						$receipt_arr[$r]['payment_date'] = date('Y-m-d');
						$receipt_arr[$r]['createdatetime'] = $datenow_timestamp;
						$receipt_arr[$r]['loan_amount_balance'] = '0';
						$receipt_arr[$r]['transaction_text'] = 'ชำระเงินกู้เลขที่สัญญา '.@$value['contract_number'];
						$receipt_arr[$r]['deduct_type'] = 'all';
						$receipt_arr[$r]['contract_number'] = $value['contract_number'];
						if(!empty($loan_interest_remain)) {
							$receipt_arr[$r]['loan_amount_interest_debt'] = $loan_interest_remain->sum;
							$receipt_arr[$r]['total_amount'] += $loan_interest_remain->sum;
						}

						$r++;
					}
					//การกู้เงินฉุกเฉิน ATM
					$this->db->select(array('*'));
					$this->db->from('coop_loan_atm');
					$this->db->where("member_id = '".$member_id."' AND loan_atm_status = '1'");
					$this->db->order_by("loan_atm_id DESC");
					$rs_atm = $this->db->get()->result_array();
					foreach($rs_atm as $key_atm => $row_atm){
						//Get Interest
						$curr_BE = date("Y") + 543;
						$finance_month = $this->db->select("*")
													->from("coop_finance_month_detail as t1")
													->join("coop_finance_month_profile as t2", "t1.profile_id = t2.profile_id")
													->where("t1.loan_atm_id = '".$row_atm['loan_atm_id']."' AND t2.profile_year = '".$curr_BE."' AND t2.profile_month = '".date("m")."' AND t1.run_status = 1")
													->get()->row();
						$interest_data = 0;
						if(empty($finance_month)) {
							$cal_atm_interest = array();
							$cal_atm_interest['loan_atm_id'] = $row_atm['loan_atm_id'];
							$cal_atm_interest['date_interesting'] = date('Y-m-d');
							$interest_data = $this->loan_libraries->cal_atm_interest_report_test($cal_atm_interest,"echo", array("month"=> date("m"), "year" => date("Y") ), false, true )['interest_month'];
						} else {
							$interest_data = 0;
						}
						//Get total debt
						$row_detail = $this->db->select(array(
															't1.loan_id',
															't1.loan_amount_balance',
															't1.loan_date',
															't1.date_last_pay',
															't1.date_last_interest'
														))
												->from('coop_loan_atm_detail as t1')
												->where("
													t1.loan_atm_id = '".$row_atm['loan_atm_id']."' 
													AND t1.transfer_status = '1' 
												")
												->get()->result_array();
						$loan_amount_balance = 0;
						foreach($row_detail as $key_detail => $value_detail){
							$loan_amount_balance += $value_detail['loan_amount_balance'];
						}

						//Get loan interest non pay
						$loan_interest_remain = $this->db->select("loan_id, SUM(non_pay_amount_balance) as sum")
															->from("coop_non_pay_detail")
															->where("loan_atm_id = '".$row_atm['loan_atm_id']."' AND pay_type = 'interest'")
															->get()->row();

						$receipt_arr[$r]['receipt_id'] = @$receipt_id;
						$receipt_arr[$r]['member_id'] = @$member_id;
						$receipt_arr[$r]['loan_atm_id'] = @$row_atm['loan_atm_id'];
						$receipt_arr[$r]['account_list_id'] = '31';
						$receipt_arr[$r]['principal_payment'] = @$loan_amount_balance;
						$receipt_arr[$r]['interest'] = @$interest_data;
						$receipt_arr[$r]['total_amount'] = @$loan_amount_balance+@$interest_data;
						$receipt_arr[$r]['payment_date'] = date('Y-m-d');
						$receipt_arr[$r]['createdatetime'] = $datenow_timestamp;
						$receipt_arr[$r]['loan_amount_balance'] = '0';
						$receipt_arr[$r]['transaction_text'] = 'ชำระเงินกู้เลขที่สัญญา '.$row_atm['contract_number'];
						$receipt_arr[$r]['deduct_type'] = 'all';
						$receipt_arr[$r]['contract_number'] = $row_atm['contract_number'];
						if(!empty($loan_interest_remain)) {
							$receipt_arr[$r]['loan_amount_interest_debt'] = $loan_interest_remain->sum;
							$receipt_arr[$r]['total_amount'] += $loan_interest_remain->sum;
						}
						$r++;
					}	
					//บันทึกการชำระเงิน	
					$sum_count = 0;
					foreach($receipt_arr as $key => $value){
						$data_insert = array();
						$data_insert['receipt_id'] = $value['receipt_id'];
						$data_insert['receipt_list'] = $value['account_list_id'];
						$data_insert['receipt_count'] = $value['total_amount'];
						$this->db->insert('coop_receipt_detail', $data_insert);

						//บันทึกการชำระเงิน
						$has_payment = false;
						$balance_payment = 0;
						$interest_payment = 0;
						$interest_dept_payment = 0;
						$balance_left = 0;
						$interest_left = 0;
						$interest_dept_left = 0;
						if(!empty($value['loan_id'])) {
							if(!empty($data[$value['loan_id']."_loan_loan_balance"]) || !empty($data[$value['loan_id']."_loan_interest_text"]) || !empty($data[$value['loan_id']."_loan_interest_dept_text"])) {
								$has_payment = true;
								$balance_payment = !empty($data[$value['loan_id']."_loan_loan_balance"]) ? $data[$value['loan_id']."_loan_loan_balance"] : 0;
								$interest_payment = !empty($data[$value['loan_id']."_loan_interest_text"]) ? $data[$value['loan_id']."_loan_interest_text"] : 0;
								$interest_dept_payment = !empty($data[$value['loan_id']."_loan_interest_dept_text"]) ? $data[$value['loan_id']."_loan_interest_dept_text"] : 0;
							}
						} else if(!empty($value['loan_atm_id'])) {
							if(!empty($data[$value['loan_atm_id']."_atm_loan_balance"]) || !empty($data[$value['loan_atm_id']."_atm_interest_text"]) || !empty($data[$value['loan_atm_id']."_atm_interest_dept_text"])) {
								$has_payment = true;
								$balance_payment = !empty($data[$value['loan_atm_id']."_atm_loan_balance"]) ? $data[$value['loan_atm_id']."_atm_loan_balance"] : 0;
								$interest_payment = !empty($data[$value['loan_atm_id']."_atm_interest_text"]) ? $data[$value['loan_atm_id']."_atm_interest_text"] : 0;
								$interest_dept_payment = !empty($data[$value['loan_atm_id']."_atm_interest_dept_text"]) ? $data[$value['loan_atm_id']."_atm_interest_dept_text"] : 0;
							}
						}

						if ($has_payment) {
							$balance_left = $value['principal_payment'] - $balance_payment;
							$interest_left = $value['interest'] - $interest_payment;
							$interest_dept_left = $value['loan_amount_interest_debt'] - $interest_dept_payment;

							$data_insert = array();
							$data_insert['receipt_id'] = $value['receipt_id'];
							$data_insert['member_id'] = $value['member_id'];
							$data_insert['loan_id'] = $value['loan_id'];
							$data_insert['loan_atm_id'] = $value['loan_atm_id'];
							$data_insert['account_list_id'] = $value['account_list_id'];
							$data_insert['principal_payment'] = $balance_payment;
							$data_insert['interest'] = $interest_payment;
							$data_insert['total_amount'] = $balance_payment+$interest_payment+$balance_left;
							$data_insert['payment_date'] = $value['payment_date'];
							$data_insert['loan_amount_balance'] = $balance_left;
							$data_insert['createdatetime'] = $value['createdatetime'];
							$data_insert['transaction_text'] = $value['transaction_text'];
							$data_insert['deduct_type'] = $value['deduct_type'];
							$data_insert['loan_interest_remain'] = $interest_dept_payment;
							$this->db->insert('coop_finance_transaction', $data_insert);

							if(!empty($interest_left)) {
								$data_insert = array();
								$data_insert['loan_id'] = $value['loan_id'];
								$data_insert['loan_atm_id'] = $value['loan_atm_id'];
								$data_insert['total'] =  $value['interest'];
								$data_insert['balance'] = $interest_left;
								$data_insert['date_cal'] = $process_timestamp;
								$data_insert['created_at'] = $datenow_timestamp;
								$data_insert['updated_at'] = $datenow_timestamp;
								$this->db->insert("coop_mem_resign_non_pay", $data_insert);
							}

							$sum_count += $balance_payment+$interest_payment+$balance_left;
						}
						if($value['loan_id'] != ''){
							$loan_amount_balance = $balance_left;
							$data_insert = array();
							$data_insert['loan_amount_balance'] = $loan_amount_balance;
							if($loan_amount_balance <= 0) {
								$data_insert['loan_status'] = '4';
							}
							$data_insert['date_last_interest'] = $date_interesting;
							$this->db->where('id', $value['loan_id']);
							$this->db->update('coop_loan', $data_insert);

							$data_insert = array();
							$data_insert['loan_id'] = $value['loan_id'];
							$data_insert['loan_amount_balance'] = $loan_amount_balance;
							$data_insert['transaction_datetime'] = $process_timestamp;
							$data_insert['receipt_id'] = $value['receipt_id'];
							$this->db->insert("coop_loan_transaction", $data_insert);

							$data_insert = array();
							$data_insert['non_pay_amount_balance'] = 0;
							$this->db->where('loan_id = "'.$value['loan_id'].'" AND pay_type = "principal"');
							$this->db->update('coop_non_pay_detail', $data_insert);

							$non_pay_details = $this->db->select("t1.non_pay_id,
																	t1.non_pay_amount_balance as total_balance,
																	t2.run_id,
																	t2.non_pay_amount as total_interest,
																	t2.non_pay_amount_balance as interest")
														->from("coop_non_pay as t1")
														->join("coop_non_pay_detail as t2", "t1.non_pay_id = t2.non_pay_id", "inner")
														->where("t1.non_pay_status = 1 AND t2.loan_id = '".$value['loan_id']."' AND t2.pay_type = 'interest'")
														->order_by("t1.non_pay_year, t1.non_pay_month")
														->get()->result_array();

							foreach($non_pay_details as $non_pay_detail) {
								if($non_pay_detail['interest'] > 0) {
									$balance = 0;
									if($interest_dept_payment >= $non_pay_detail['interest']) {
										$interest_dept_payment -= $non_pay_detail['interest'];
										$balance = 0;
									} else {
										$balance = $non_pay_detail['interest'] - $interest_dept_payment;
										$interest_dept_payment = 0;
									}

									if($balance > 0) {
										$data_insert = array();
										$data_insert['loan_id'] = $value['loan_id'];
										$data_insert['non_pay_id'] = $non_pay_detail["non_pay_id"];
										$data_insert['total'] = $balance;
										$data_insert['balance'] = $balance;
										$data_insert['date_cal'] = $process_timestamp;
										$data_insert['created_at'] = $datenow_timestamp;
										$data_insert['updated_at'] = $datenow_timestamp;
										$this->db->insert("coop_mem_resign_non_pay", $data_insert);
									}

									$data_insert = array();
									$data_insert['non_pay_amount_balance'] = $balance;
									$data_insert["finance_month_detail_id"] = "TE";
									$this->db->where('run_id', $non_pay_detail['run_id']);
									$this->db->update('coop_non_pay_detail', $data_insert);
								}
							}

							//Resign Loan Detail
							$data_insert = array();
							$data_insert['member_id'] = $member_id;
							$data_insert['loan_id'] = $value['loan_id'];
							$data_insert['contract_number'] = $value['contract_number'];
							$data_insert['loan_amount_principal'] = $value['principal_payment'];
							$data_insert['loan_amount_interest'] = $value['interest'];
							$data_insert['loan_amount_interest_debt'] = $value['loan_amount_interest_debt'];
							$data_insert['loan_amount_debt'] = $loan_amount_balance;
							$data_insert['receipt_id'] = $value['receipt_id'];
							$data_insert['careate_datetime'] = $datenow_timestamp;
							$data_insert['update_datetime'] = $datenow_timestamp;
							$this->db->insert("coop_resign_loan_detail", $data_insert);
						}
						if($value['loan_atm_id'] != ''){
							$atm_total_balance = 0;
							$this->db->select(array(
								'loan_id',
								'loan_amount_balance'
							));
							$this->db->from('coop_loan_atm_detail');
							$this->db->where("loan_atm_id = '".$value['loan_atm_id']."' AND loan_status = '0'");
							$this->db->order_by('loan_id ASC');
							$row = $this->db->get()->result_array();
							foreach($row as $key_atm => $value_atm){
								$loan_amount_balance = 0;
								if($balance_payment >= $value_atm['loan_amount_balance']) {
									$balance_payment -= $value_atm['loan_amount_balance'];
								} else {
									$loan_amount_balance = $value_atm['loan_amount_balance'] - $balance_payment;
									$balance_payment = 0;
								}
								$data_insert = array();
								$data_insert['loan_amount_balance'] = $loan_amount_balance;
								$data_insert['date_last_pay'] = date('Y-m-d');
								$this->db->where('loan_id', $value_atm['loan_id']);
								$this->db->update('coop_loan_atm_detail', $data_insert);
								$principal_payment = 0;	
								$atm_total_balance += $loan_amount_balance;
							}

							$this->db->select(array(
								'total_amount_approve',
								'total_amount_balance',
								'total_amount'
							));
							$this->db->from('coop_loan_atm');
							$this->db->where("loan_atm_id = '".$value['loan_atm_id']."'");
							$row = $this->db->get()->result_array();
							$row_loan_atm = $row[0];
							
							$data_insert = array();
							$data_insert['total_amount_balance'] = $row_loan_atm["total_amount"] - $atm_total_balance;
							if($atm_total_balance == 0) $data_insert['loan_atm_status'] = 4;
							$data_insert['date_last_interest'] = $date_interesting;
							$this->db->where('loan_atm_id', $value['loan_atm_id']);
							$this->db->update('coop_loan_atm', $data_insert);

							$data_insert = array();
							$data_insert['loan_atm_id'] = $value['loan_atm_id'];
							$data_insert['loan_amount_balance'] = $atm_total_balance;
							$data_insert['transaction_datetime'] = $process_timestamp;
							$data_insert['receipt_id'] = $value['receipt_id'];
							$this->db->insert("coop_loan_atm_transaction", $data_insert);

							$data_insert = array();
							$data_insert['non_pay_amount_balance'] = 0;
							$this->db->where('loan_atm_id = "'.$value['loan_atm_id'].'" AND pay_type = "principal"');
							$this->db->update('coop_non_pay_detail', $data_insert);

							$non_pay_details = $this->db->select("t1.non_pay_id,
														t1.non_pay_amount_balance as total_balance,
														t2.run_id,
														t2.non_pay_amount as total_interest,
														t2.non_pay_amount_balance as interest")
											->from("coop_non_pay as t1")
											->join("coop_non_pay_detail as t2", "t1.non_pay_id = t2.non_pay_id", "inner")
											->where("t1.non_pay_status = 1 AND t2.loan_atm_id = '".$value['loan_atm_id']."' AND t2.pay_type = 'interest'")
											->order_by("t1.non_pay_year, t1.non_pay_month")
											->get()->result_array();

							foreach($non_pay_details as $non_pay_detail) {
								if($non_pay_detail['interest'] > 0) {
									$balance = 0;
									if($interest_dept_payment >= $non_pay_detail['interest']) {
										$interest_dept_payment -= $non_pay_detail['interest'];
										$balance = 0;
									} else {
										$balance = $non_pay_detail['interest'] - $interest_dept_payment;
										$interest_dept_payment = 0;
									}

									if($balance > 0) {
										$data_insert = array();
										$data_insert['loan_atm_id'] = $value['loan_atm_id'];
										$data_insert['non_pay_id'] = $non_pay_detail["non_pay_id"];
										$data_insert['total'] = $balance;
										$data_insert['balance'] = $balance;
										$data_insert['date_cal'] = $process_timestamp;
										$data_insert['created_at'] = $datenow_timestamp;
										$data_insert['updated_at'] = $datenow_timestamp;
										$this->db->insert("coop_mem_resign_non_pay", $data_insert);
									}

									$data_insert = array();
									$data_insert['non_pay_amount_balance'] = $balance;
									$this->db->where('run_id', $non_pay_detail['run_id']);
									$this->db->update('coop_non_pay_detail', $data_insert);
								}
							}

							//Resign Loan Detail
							$data_insert = array();
							$data_insert['member_id'] = $member_id;
							$data_insert['loan_atm_id'] = $value['loan_atm_id'];
							$data_insert['contract_number'] = $value['contract_number'];
							$data_insert['loan_amount_principal'] = $value['principal_payment'];
							$data_insert['loan_amount_interest'] = $value['interest'];
							$data_insert['loan_amount_interest_debt'] = $value['loan_amount_interest_debt'];
							$data_insert['loan_amount_debt'] = $atm_total_balance;
							$data_insert['receipt_id'] = $value['receipt_id'];
							$data_insert['careate_datetime'] = $datenow_timestamp;
							$data_insert['update_datetime'] = $datenow_timestamp;
							$this->db->insert("coop_resign_loan_detail", $data_insert);
						}
					}
					if($sum_count>0){
						$data_insert = array();
						$data_insert['receipt_id'] = $receipt_id;
						$data_insert['member_id'] = $member_id;
						$data_insert['admin_id'] = $_SESSION['USER_ID'];
						$data_insert['sumcount'] = $sum_count;
						$data_insert['receipt_datetime'] = $process_timestamp;
						$this->db->insert('coop_receipt', $data_insert);

						$data_insert = array();
						$data_insert['receipt_id'] = $receipt_id;
						$this->db->where('req_resign_id', $req_resign_id);
						$this->db->update('coop_mem_req_resign', $data_insert);
					}

					$non_pays = $this->db->select("non_pay_id")
											->from("coop_non_pay")
											->where("non_pay_amount_balance > 0 AND member_id = '".$member_id."'")
											->get()->result_array();
					$non_pay_receipts = array();
					foreach($non_pays as $non_pay) {
						$non_pay_receipt = array();
						$non_pay_receipt["member_id"] = $member_id;
						$non_pay_receipt["non_pay_id"] = $non_pay["non_pay_id"];
						$non_pay_receipt["receipt_id"] = $receipt_id;
						$non_pay_receipt["createdatetime"] = $datenow_timestamp;
						$non_pay_receipt["updatetime"] = $datenow_timestamp;
						$non_pay_receipt["receipt_status"] = 0;
						$non_pay_receipts[] = $non_pay_receipt;
                    }
                    if(!empty($non_pay_receipts)) {
                        $this->db->insert_batch('coop_non_pay_receipt', $non_pay_receipts);
                    }
					$non_pay_details = $this->db->select("run_id, non_pay_id, sum(non_pay_amount_balance) as sum")
												->from("coop_non_pay_detail")
												->where("member_id = '".$member_id."'")
												->group_by("non_pay_id")
												->get()->result_array();
					foreach($non_pay_details as $detail) {
						$balance = $detail['sum'];
						$data_insert = array();
						if($balance <= 0) {
							$balance == 0;
							$data_insert['non_pay_status'] = 2;
						} else {
							$data_insert['non_pay_status'] = 6;
						}
						$data_insert['non_pay_amount_balance'] = 0;
						$this->db->where('non_pay_id = "'.$detail['non_pay_id'].'"');
						$this->db->update('coop_non_pay', $data_insert);
					}

					$data_insert = array();
					$data_insert['non_pay_amount_balance'] = 0;
					$this->db->where('member_id', $member_id);
					$this->db->update('coop_non_pay_detail', $data_insert);

					//Bank Account
					$transaction_list = 'CW';
					$accounts = $this->db->select("*")
										->from("coop_maco_account")
										->where("mem_id = '".$member_id."' AND account_status = '0'")
										->get()->result_array();
					$total_account_amount = 0;
					foreach($accounts as $account) {
						$cal_result = $this->deposit_libraries->cal_deposit_interest_by_acc_date($account['account_id'], $process_timestamp);
						$close_account_interest = $cal_result['interest'];
						$close_account_interest_return = $cal_result['interest_return'];

						$row = $this->db->select(array('transaction_balance','transaction_no_in_balance'))
										->from('coop_account_transaction')
										->where("account_id = '".$account['account_id']."'")
										->order_by('transaction_time DESC, transaction_id DESC')
										->limit(1)
										->get()->result_array();
						$row_transaction = $row[0];
						$transaction_balance = $row_transaction['transaction_balance'];
						$transaction_no_in_balance = $row_transaction['transaction_no_in_balance'];

						//Deposit Interest
						if($close_account_interest > 0){
							$transaction_balance = $transaction_balance+$close_account_interest;
							$data_insert_in = array();
							$data_insert_in['transaction_time'] = $process_timestamp;
							$data_insert_in['transaction_list'] = 'IN';
							$data_insert_in['transaction_withdrawal'] = '0';
							$data_insert_in['transaction_deposit'] = $close_account_interest;
							$data_insert_in['transaction_balance'] = $transaction_balance;
							$data_insert_in['transaction_no_in_balance'] = $transaction_no_in_balance;
							$data_insert_in['user_id'] = $_SESSION['USER_ID'];
							$data_insert_in['account_id'] = $account['account_id'];

							//ดึงข้อมูลลำดับรายการ ของรายการถัดไป
							$arr_seq = array(); 
							$arr_seq['account_id'] = $data_insert_in['account_id'] ; 
							$arr_seq['transaction_list'] = $data_insert_in['transaction_list'];
							$seq_no = $this->deposit_seq->gen_seq_account_transaction($arr_seq);
							$data_insert_in['seq_no'] = @$seq_no;

							$this->db->insert('coop_account_transaction', $data_insert_in);
						}
						//Deduct Interest
						if($close_account_interest_return > 0){
							$transaction_balance = $transaction_balance-$close_account_interest_return;
							$data_insert_in = array();
							$data_insert_in['transaction_time'] = $process_timestamp;
							$data_insert_in['transaction_list'] = 'WTI';
							$data_insert_in['transaction_withdrawal'] = $close_account_interest_return;
							$data_insert_in['transaction_deposit'] = 0;
							$data_insert_in['transaction_balance'] = $transaction_balance;
							$data_insert_in['transaction_no_in_balance'] = $transaction_no_in_balance;
							$data_insert_in['user_id'] = $_SESSION['USER_ID'];
							$data_insert_in['account_id'] = $account['account_id'];

							//ดึงข้อมูลลำดับรายการ ของรายการถัดไป
							$arr_seq = array(); 
							$arr_seq['account_id'] = $data_insert_in['account_id'] ; 
							$arr_seq['transaction_list'] = $data_insert_in['transaction_list'];
							$seq_no = $this->deposit_seq->gen_seq_account_transaction($arr_seq);
							$data_insert_in['seq_no'] = @$seq_no;

							$this->db->insert('coop_account_transaction', $data_insert_in);
						}
						if($transaction_balance > 0){
							$data_insert = array();
							$data_insert['transaction_time'] = $process_timestamp;
							$data_insert['transaction_list'] = $transaction_list;
							$data_insert['transaction_withdrawal'] = $transaction_balance;
							$data_insert['transaction_deposit'] = '0';
							$data_insert['transaction_balance'] = '0';
							$data_insert['transaction_no_in_balance'] = '0';
							$data_insert['user_id'] = $_SESSION['USER_ID'];
							$data_insert['account_id'] = $account['account_id'];

							//ดึงข้อมูลลำดับรายการ ของรายการถัดไป
							$arr_seq = array(); 
							$arr_seq['account_id'] = $data_insert['account_id'] ; 
							$arr_seq['transaction_list'] = $data_insert['transaction_list'];
							$seq_no = $this->deposit_seq->gen_seq_account_transaction($arr_seq);
							$data_insert['seq_no'] = @$seq_no;

							$this->db->insert('coop_account_transaction', $data_insert);

							$total_account_amount += $transaction_balance;
						}

						$data_insert = array();
						$data_insert['account_amount'] = '0';
						$data_insert['account_status'] = '1';
						$data_insert['sequester_status'] = '1';
						$data_insert['sequester_status_atm'] = '1';
						$data_insert['close_account_date'] = $process_timestamp;
						$data_insert['close_account_pay_type'] = "0";
						$this->db->where('account_id', $account['account_id']);
						$this->db->update('coop_maco_account',$data_insert);
					}

					$data_insert = array();
					$data_insert["income_code"] = "income_deposit";
					$data_insert["income_amount"] = $total_account_amount;
					$data_insert["member_id"] = $member_id;
					$data_insert["receipt_id"] = $receipt_id;
					$this->db->insert('coop_resign_income_detail', $data_insert);

					//Share
					$share_setting = $this->db->select("*")
												->from("coop_share_setting")
												->get()->row();
					$share = $this->db->select("*")
										->from("coop_mem_share")
										->where("member_id = '".$member_id."'")
										->order_by("share_date DESC, share_id DESC")
										->get()->row();
					$data_insert = array();
					$data_insert['member_id'] = $member_id;
					$data_insert['share_date'] = $process_timestamp;
					$data_insert['admin_id'] = $_SESSION['USER_ID'];
					$data_insert['share_type'] = "SRP";
					$data_insert['share_status'] = "5";
					$data_insert['share_payable'] = $share->share_collect;
					$data_insert['share_payable_value'] = $share->share_collect_value;
					$data_insert['share_early'] = $share->share_collect;
					$data_insert['share_early_value'] = $share->share_collect_value;
					$data_insert['share_collect'] = 0;
					$data_insert['share_collect_value'] = 0;
					$data_insert['share_value'] = $share_setting->setting_value;
					$this->db->insert('coop_mem_share', $data_insert);

					$data_insert = array();
					$data_insert["income_code"] = "income_share";
					$data_insert["income_amount"] = $share->share_collect;
					$data_insert["member_id"] = $member_id;
					$data_insert["receipt_id"] = $receipt_id;
					$this->db->insert('coop_resign_income_detail', $data_insert);
					$data_insert = array();
					$data_insert["income_code"] = "balance_share";
					$data_insert["income_amount"] = $share->share_collect;
					$data_insert["member_id"] = $member_id;
					$data_insert["receipt_id"] = $receipt_id;
					$this->db->insert('coop_resign_income_detail', $data_insert);

					$data_insert = array();
					$data_insert["income_code"] = "income_amount";
					$data_insert["income_amount"] = $share->share_collect_value + $total_account_amount;
					$data_insert["member_id"] = $member_id;
					$data_insert["receipt_id"] = $receipt_id;
					$this->db->insert('coop_resign_income_detail', $data_insert);

					//Update cremation status if exist
					$this->change_cremation_member_type($member_id, 2);
                }
			}
		}
	}
	
	public function change_cremation_member_type($member_id, $type) {
		$data_insert = array();
		$data_insert['type'] = $type;
		$this->db->where('member_id', $member_id);
		$this->db->update('coop_member_cremation',$data_insert);

		$data_insert = array();
		$data_insert['type'] = $type;
		$this->db->where('ref_member_id', $member_id);
		$this->db->update('coop_member_cremation',$data_insert);

		$member_cremations = $this->db->select("id, type")->from("coop_member_cremation")
										->where("member_id = '".$member_id."' AND ref_member_id = '".$member_id."' AND member_cremation_id IS NOT NULL")
										->get()->result_array();
		$lastest_change = $this->db->select("ref_id")->from("coop_member_cremation_data_history")->order_by("ref_id DESC")->get()->row();

		$process_timestamp = date('Y-m-d H:i:s');
		$change_datas = array();
		foreach($member_cremations as $member_cremation) {
			$change_data = array();
			$change_data["input_name"] = "type";
			$change_data["ref_id"] = !empty($lastest_change->ref_id) ? $lastest_change->ref_id + 1 : 1;
			$change_data["member_cremation_raw_id"] = $member_cremation["id"];
			$change_data["origin_value"] = $member_cremation["type"];
			$change_data["new_value"] = $type;
			$change_data["user_id"] = $_SESSION['USER_ID'];
			$change_data["created_at"] = $process_timestamp;
			$change_datas[] = $change_data;
		}

		if(!empty($change_datas)) {
			$this->db->insert_batch('coop_member_cremation_data_history', $change_datas);
		}
	}
}
