<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Receipt_cancel extends CI_Controller {
	function __construct()
	{
		parent::__construct();
		$this->month_arr = array('1'=>'มกราคม','2'=>'กุมภาพันธ์','3'=>'มีนาคม','4'=>'เมษายน','5'=>'พฤษภาคม','6'=>'มิถุนายน','7'=>'กรกฎาคม','8'=>'สิงหาคม','9'=>'กันยายน','10'=>'ตุลาคม','11'=>'พฤศจิกายน','12'=>'ธันวาคม');
	}

	function coop_cashier_receipt_cancel() {
		$arr_data = array();
		// $this->db->trans_start();
		//Cancel Receipt If has receipt_id
		if ($_POST['receipt_id'] != '') {
			
			$receipt_id = $_POST['receipt_id'];

			$transactions = $this->db->select("*")
										->from("coop_finance_transaction as t1")
										->where("t1.receipt_id = '".$receipt_id."'")
										->get()->result_array();
			foreach($transactions as $transaction) {
				if(!empty($transaction["loan_id"])) {
					//Rollback loan
					$loan_id = $transaction['loan_id'];
					$amount = $transaction["principal_payment"];
					$temp_amount = $amount;
					
					//ต้องหาดอก ของใบเสร็จที่ยกเลิก
					$interest_balance = 0;
					$transaction_interest = @$transaction["interest"];
					if($transaction_interest > 0){
						//คืนข้อมูลดอกเบี้ยค้างชำระสะสม
						$this->db->select("id, loan_id,interest_total,interest_balance");
						$this->db->from('coop_loan_interest_debt');
						$this->db->where("loan_id = '".$loan_id."' AND interest_status = 0");
						$this->db->order_by("created_datetime ASC");
						$row_interest_debt = $this->db->get()->result_array();
						$post_interest_balance = @$transaction_interest;
						foreach($row_interest_debt as $key_interest_debt => $value_interest_debt){
							if($post_interest_balance > 0){
								$interest_balance = @$value_interest_debt['interest_balance']+@$post_interest_balance;
								$data_insert = array();
								$data_insert['interest_balance'] = (@$interest_balance > @$value_interest_debt['interest_total'])?@$value_interest_debt['interest_total']:@$interest_balance;
								$data_insert['updated_datetime'] = date('Y-m-d H:i:s');
								$this->db->where('id', $value_interest_debt['id']);
								$this->db->update('coop_loan_interest_debt', $data_insert);

								
								//เปลี่ยนสถานะ หลัง คืนข้อมูลดอกเบี้ยค้างชำระสะสม
								$this->db->select("id, loan_id,interest_total,interest_balance");
								$this->db->from('coop_loan_interest_debt');
								$this->db->where("id = '".$value_interest_debt['id']."' AND interest_status = 0");
								$this->db->limit(1);
								$rs_interest_debt_after = $this->db->get()->result_array();
								$row_interest_debt_after = @$rs_interest_debt_after[0];
								
								if(@$row_interest_debt_after['interest_balance'] >= @$value_interest_debt['interest_total']){
									$data_insert = array();
									$data_insert['interest_status'] = 1;
									$data_insert['updated_datetime'] = date('Y-m-d H:i:s');
									$this->db->where('id', $value_interest_debt['id']);
									$this->db->update('coop_loan_interest_debt', $data_insert);
								}
							}
						}
						
					}
					
					if($amount==0)
						continue;
					$this->db->set("loan_amount_balance", "(SELECT loan_amount_balance + $amount FROM (SELECT * FROM coop_loan WHERE id = ".$loan_id.") AS t1 )", false);
					$this->db->set("loan_status", "1", false);
					$this->db->where("id", $loan_id);
					$this->db->update("coop_loan");

					$this->db->where("receipt_id", $receipt_id);
					$loan_transaction = $this->db->get("coop_loan_transaction")->result()[0];

					$this->db->where("receipt_id", $receipt_id);
					$this->db->delete("coop_loan_transaction");
								
					//รัน อัพเดท statement
					$this->update_st->update_loan_transaction($loan_id, $loan_transaction->transaction_datetime);
				} else if (!empty($transaction["loan_atm_id"])) {
					//Rollback loan
					$loan_atm_id = $transaction['loan_atm_id'];
					$amount = $transaction["principal_payment"];

					//ต้องหาดอก ของใบเสร็จที่ยกเลิก
					$transaction_interest = @$transaction["interest"];
					if($transaction_interest > 0){
						//คืนข้อมูลดอกเบี้ยค้างชำระสะสม
						$this->db->select("id, loan_atm_id,interest_total,interest_balance");
						$this->db->from('coop_loan_interest_debt');
						$this->db->where("loan_atm_id = '".$loan_atm_id."' AND interest_status = 0");
						$this->db->order_by("created_datetime ASC");
						$row_interest_debt = $this->db->get()->result_array();
						$post_interest_balance = @$transaction_interest;
						foreach($row_interest_debt as $key_interest_debt => $value_interest_debt){
							if($post_interest_balance > 0){
								$interest_balance = @$value_interest_debt['interest_balance']+@$post_interest_balance;
								$data_insert = array();
								$data_insert['interest_balance'] = (@$interest_balance > @$value_interest_debt['interest_total'])?@$value_interest_debt['interest_total']:@$interest_balance;
								$data_insert['updated_datetime'] = date('Y-m-d H:i:s');
								$this->db->where('id', $value_interest_debt['id']);
								$this->db->update('coop_loan_interest_debt', $data_insert);

								
								//เปลี่ยนสถานะ หลัง คืนข้อมูลดอกเบี้ยค้างชำระสะสม
								$this->db->select("id, loan_atm_id,interest_total,interest_balance");
								$this->db->from('coop_loan_interest_debt');
								$this->db->where("id = '".$value_interest_debt['id']."' AND interest_status = 0");
								$this->db->limit(1);
								$rs_interest_debt_after = $this->db->get()->result_array();
								$row_interest_debt_after = @$rs_interest_debt_after[0];
								
								if(@$row_interest_debt_after['interest_balance'] >= @$value_interest_debt['interest_total']){
									$data_insert = array();
									$data_insert['interest_status'] = 1;
									$data_insert['updated_datetime'] = date('Y-m-d H:i:s');
									$this->db->where('id', $value_interest_debt['id']);
									$this->db->update('coop_loan_interest_debt', $data_insert);
								}
							}
						}
						
					}
					
					$this->db->set("total_amount_balance", "(SELECT total_amount_balance - $amount FROM (SELECT * FROM coop_loan_atm WHERE loan_atm_id = ".$loan_atm_id.") AS t1 )", false);
					$this->db->set("loan_atm_status", "1", false);
					$this->db->where("loan_atm_id", $loan_atm_id);
					$this->db->update("coop_loan_atm");

					$this->db->where("loan_amount != loan_amount_balance");
					$this->db->order_by("loan_atm_id", "DESC");
					$loan_atm_detail = $this->db->get_where("coop_loan_atm_detail", array("loan_atm_id" =>  $loan_atm_id) );
					foreach ($loan_atm_detail->result() as $key => $value) {
						if($amount <= 0)
							break;
						if($value->loan_amount_balance + $amount <= $value->loan_amount){
							$this->db->set("loan_amount_balance", ($value->loan_amount_balance + $amount));
						}else{
							$this->db->set("loan_amount_balance", $value->loan_amount);
							$amount = $amount - ($value->loan_amount - $value->loan_amount_balance);
						}
						$this->db->where("loan_id", $value->loan_id);
						$this->db->update("coop_loan_atm_detail");
					}

					$this->db->where("receipt_id", $receipt_id);
					$loan_transaction = $this->db->get("coop_loan_atm_transaction")->result()[0];

					$this->db->where("receipt_id", $receipt_id);
					$this->db->delete("coop_loan_atm_transaction");
					//รัน อัพเดท statement
					$this->update_st->update_loan_atm_transaction($loan_atm_id, $loan_transaction->transaction_datetime);
				} else if ($transaction["account_list_id"] == 14 || $transaction["account_list_id"] == 37) {
					//Rollback share
					$this->db->where("share_bill", $receipt_id);
					$temp_share_transacrion = $this->db->get("coop_mem_share")->result()[0];

					if(!empty($temp_share_transacrion)) {
						$this->db->set("share_status", 3);//ยกเลิกใบเสร็จ
						$this->db->where("share_bill", $receipt_id);
						$this->db->update("coop_mem_share");
					} else {
						$where_arr = array('share_date' => $transaction["createdatetime"],
											'member_id' => $transaction["member_id"],
											'share_early_value' => $transaction["principal_payment"]);
						$this->db->where($where_arr); 
						$this->db->where("share_date", $transaction["createdatetime"]);
						$temp_share_transacrion = $this->db->get("coop_mem_share")->result()[0];
						$this->db->set("share_status", 3);//ยกเลิกใบเสร็จ
						$this->db->where($where_arr); 
						$this->db->update("coop_mem_share");
					}

					//รัน อัพเดท statement
					$this->update_st->update_share_transaction($temp_share_transacrion->member_id, $temp_share_transacrion->share_date);
				}

				//Rollback DEPOSIT if exist
				$this->db->order_by("transaction_time", "ASC");
				$this->db->order_by("transaction_id", "ASC");
				$temp_account_transaction = $this->db->get_where("coop_account_transaction", array("receipt_id" => $receipt_id) )->result()[0];

				if(!empty($temp_account_transaction)) {
					$data_insert['transaction_time'] 			= date("Y-m-d H:i:s");
					$data_insert['transaction_list'] 			= "CANCEL";
					$data_insert['transaction_withdrawal']		= 0;
					$data_insert['transaction_deposit']			= $temp_account_transaction->transaction_deposit * -1;
					$data_insert['transaction_balance']			= $temp_account_transaction->transaction_balance + $data_insert['transaction_deposit'];
					$data_insert['transaction_no_in_balance']	= $temp_account_transaction->transaction_no_in_balance + $data_insert['transaction_deposit'];
					$data_insert['account_id']					= $temp_account_transaction->account_id;
					$data_insert['user_id']						= $_SESSION['USER_ID'];
					$this->db->insert("coop_account_transaction", $data_insert);

					//รัน อัพเดท statement
					$this->update_st->update_deposit_transaction($temp_account_transaction->account_id, $temp_account_transaction->transaction_time);
				}
			}

			$receiptData = array(
								'cancel_by' => $_SESSION['USER_ID'],
								'receipt_status' => '2',
								'cancel_date' =>date('Y-m-d H:i:s')
							);
			$this->db->where('receipt_id', $receipt_id)
					->update('coop_receipt', $receiptData);	
			
			echo"<script>document.location.href='".base_url(PROJECTPATH.'/receipt_cancel/coop_cashier_receipt_cancel')."'</script>";
		}

		$arr_data['month_arr'] = $this->month_arr;

		$x=0;
		$join_arr[$x]['table'] = 'coop_mem_apply as t4';
		$join_arr[$x]['condition'] = 't1.member_id = t4.member_id';
		$join_arr[$x]['type'] = 'inner';
		$x++;
		$join_arr[$x]['table'] = 'coop_user as t5';
		$join_arr[$x]['condition'] = 't1.admin_id = t5.user_id';
		$join_arr[$x]['type'] = 'left';	
		$x++;
		$join_arr[$x]['table'] = 'coop_prename as t6';
		$join_arr[$x]['condition'] = 't4.prename_id = t6.prename_id';
		$join_arr[$x]['type'] = 'left';
		$x++;
		$join_arr[$x]['table'] = 'coop_user as t7';
		$join_arr[$x]['condition'] = 't1.cancel_by = t7.user_id';
		$join_arr[$x]['type'] = 'left';	

		$where = "t1.month_receipt is null AND t1.year_receipt is null AND t1.finance_month_profile_id is null";
		$where .= " AND t1.member_id != 'FSG001'";//Prevent fund support payment

		if($_GET['search_list'] == 'member_id'){
			$where .= " AND t4.member_id LIKE '%".$_GET['search_text']."%'";
		}else if($_GET['search_list'] == 'firstname_th'){
			$where .= " AND t4.firstname_th LIKE '%".$_GET['search_text']."%'";
		}else if($_GET['search_list'] == 'lastname_th'){
			$where .= " AND t4.lastname_th LIKE '%".$_GET['search_text']."%'";
		}else if($_GET['search_list'] == 'receipt_id'){
			$where .= " AND t1.receipt_id LIKE '%".$_GET['search_text']."%'";
		}else if($_GET['search_list'] == 'employee_id'){
			$where .= " AND t4.employee_id LIKE '%".$_GET['search_text']."%'";
		}

		$this->paginater_all->type(DB_TYPE);
		$this->paginater_all->select('t1.receipt_id, t1.member_id, t1.receipt_status, t1.receipt_datetime, t6.prename_full, t4.firstname_th, t4.lastname_th, t5.user_name,t7.user_name AS user_name_cancel,t1.cancel_date');
		$this->paginater_all->main_table('coop_receipt as t1');
		$this->paginater_all->page_now(@$_GET["page"]);
		$this->paginater_all->per_page(20);
		$this->paginater_all->page_link_limit(20);
		$this->paginater_all->where($where);
		$this->paginater_all->order_by('t1.receipt_datetime DESC');
		$this->paginater_all->join_arr($join_arr);
		$row = $this->paginater_all->paginater_process();

		$paging = $this->pagination_center->paginating($row['page'], $row['num_rows'], $row['per_page'], $row['page_link_limit'],$_GET);//$page_now = 1, $row_total = 1, $per_page = 20, $page_limit = 20
		$i = $row['page_start'];

		$arr_data['num_rows'] = $row['num_rows'];
		$arr_data['paging'] = $paging;
		$arr_data['data'] = $row['data'];
		$arr_data['i'] = $i;
		$this->db->trans_complete();
		$this->libraries->template('receipt_cancel/coop_cashier_receipt_cancel',$arr_data);
	}
	
	public function coop_finance_month_process_receipt_cancel() {
		$arr_data = array();

		if ($_POST['receipt_id'] != '') {
			$receipt_id = $_POST['receipt_id'];

			$receipt = $this->db->select("*")
								->from("coop_receipt")
								->where("receipt_id = '".$receipt_id."'")
								->get()->row();

			$profile = $this->db->select("*")
								->from("coop_finance_month_profile")
								->where("profile_month = '".$receipt->month_receipt."' AND profile_year = '".$receipt->year_receipt."'")
								->get()->row();

			//Rollback Month Process
			if (strpos($receipt_id, 'B') !== false) {
				$where_arr = array('member_id' => $receipt->member_id,
									'profile_id' => $profile->profile_id);
				$this->db->set("update_datetime", date('Y-m-d H:i:s'));
				$this->db->set("run_status", '0');
				$this->db->where($where_arr); 
				$this->db->update("coop_finance_month_detail");

								/*------------
				คืนค่า เงินคืน atm หลังผ่านรายการ
				-------------*/
				$return_atm_stored = $this->db->get_where("coop_process_return_store", array(
					"receipt_id" => $receipt_id,
					"member_id" => $receipt->member_id
				))->result_array()[0];
				if(!empty($return_atm_stored)){
					if($return_atm_stored['return_status'] == 1){
						$this->db->where("unique_account", "1");
						$type_id = $this->db->get("coop_deposit_type_setting")->result_array()[0]['type_id'];
						$this->db->where("type_id", $type_id);
						$this->db->where("mem_id", $receipt->member_id);
						$account_id = $this->db->get("coop_maco_account")->result_array()[0]['account_id'];
						
						$this->db->where("return_type", 4);
						$this->db->where("account_id", $account_id);
						$this->db->where("member_id", $receipt->member_id);
						$this->db->where("return_amount", $return_atm_stored['return_amount']);
						$this->db->where("return_time like '".date("Y-m-d", strtotime($return_atm_stored['updatetime']))."%'");
						$this->db->limit(1);
						$this->db->delete("coop_process_return");
						
						$this->db->where("transaction_text", "process_interest_edit");
						$this->db->where("transaction_list", "REVD");
						$this->db->where("transaction_deposit", $return_atm_stored['return_amount']);
						$this->db->where("transaction_time like '".date("Y-m-d", strtotime($return_atm_stored['updatetime']))."%'");
						$this->db->where("account_id", $account_id);
						$this->db->limit(1);
						$this->db->delete("coop_account_transaction");

						$this->update_st->update_deposit_transaction($account_id, date("Y-m-d", strtotime("-1 days", strtotime($return_atm_stored['updatetime']))) );
					}

				}

				$this->db->where("receipt_id", $receipt_id);
				$this->db->where("member_id", $receipt->member_id);
				$this->db->delete("coop_process_return_store");
				//-------------------
			} else if (strpos($receipt_id, 'F') !== false) {
				$month_processes = $this->db->select("*")
										->from("coop_finance_month_detail")
										->where("member_id = '".$receipt->member_id."' AND profile_id = '".$profile->profile_id."'")
										->get()->result_array();
				foreach($month_processes as $process) {
					$set_arr = array("run_status" => 0,
										"real_pay_amount" => $process["pay_amount"],
										"update_datetime" => date('Y-m-d H:i:s'));
					$this->db->set($set_arr);
					$this->db->where("run_id", $process["run_id"]);
					$this->db->update("coop_finance_month_detail");

					$non_pay = $this->db->select("*")
										->from("coop_non_pay")
										->where("non_pay_month = '".$receipt->month_receipt."' AND non_pay_year = '".$receipt->year_receipt."' AND member_id = '".$receipt->member_id."'")
										->get()->row();
					$non_pay_id = $non_pay->non_pay_id;

					$this->db->where("non_pay_id", $non_pay_id);
					$this->db->delete("coop_non_pay");
					$this->db->where("non_pay_id", $non_pay_id);
					$this->db->delete("coop_non_pay_detail");
					$this->db->where("non_pay_id", $non_pay_id);
					$this->db->delete("coop_non_pay_receipt");
				}
			}

			$transactions = $this->db->select("*")
										->from("coop_finance_transaction as t1")
										->where("t1.receipt_id = '".$receipt_id."'")
										->get()->result_array();
			foreach($transactions as $transaction) {
				if(!empty($transaction["loan_id"])) {
					//Rollback loan
					$loan_id = $transaction['loan_id'];
					$amount = $transaction["principal_payment"];
					$temp_amount = $amount;
					if($amount==0)
						continue;
					$this->db->set("loan_amount_balance", "(SELECT loan_amount_balance + $amount FROM (SELECT * FROM coop_loan WHERE id = ".$loan_id.") AS t1 )", false);
					$this->db->set("loan_status", "1", false);
					$this->db->set("date_last_interest", "(SELECT t1.transaction_datetime FROM coop_loan_transaction t1 LEFT JOIN coop_receipt t2 ON t1.receipt_id=t2.receipt_id WHERE t1.loan_id='".$loan_id."' AND t1.receipt_id !='".$receipt_id."' AND t2.receipt_code != 'C' OR (t2.receipt_status IS NULL AND t2.receipt_status !='2') ORDER BY transaction_datetime DESC,loan_transaction_id DESC LIMIT 1)", false);
					$this->db->where("id", $loan_id);
					$this->db->update("coop_loan");

					$this->db->where("receipt_id", $receipt_id);
					$loan_transaction = $this->db->get("coop_loan_transaction")->result()[0];

					$this->db->where("receipt_id", $receipt_id);
					$this->db->delete("coop_loan_transaction");

					//รัน อัพเดท statement
					$this->update_st->update_loan_transaction($loan_id, $loan_transaction->transaction_datetime);
				} else if (!empty($transaction["loan_atm_id"])) {
					//Rollback loan
					$loan_atm_id = $transaction['loan_atm_id'];
					$amount = $transaction["principal_payment"];

					$this->db->set("total_amount_balance", "(SELECT total_amount_balance - $amount FROM (SELECT * FROM coop_loan_atm WHERE loan_atm_id = ".$loan_atm_id.") AS t1 )", false);
					$this->db->set("loan_atm_status", "1", false);
					$this->db->where("loan_atm_id", $loan_atm_id);
					$this->db->update("coop_loan_atm");

					$this->db->where("loan_amount != loan_amount_balance");
					$this->db->order_by("loan_atm_id", "DESC");
					$loan_atm_detail = $this->db->get_where("coop_loan_atm_detail", array("loan_atm_id" =>  $loan_atm_id) );
					foreach ($loan_atm_detail->result() as $key => $value) {
						if($amount <= 0)
							break;
						if($value->loan_amount_balance + $amount <= $value->loan_amount){
							$this->db->set("loan_amount_balance", ($value->loan_amount_balance + $amount));
						}else{
							$this->db->set("loan_amount_balance", $value->loan_amount);
							$amount = $amount - ($value->loan_amount - $value->loan_amount_balance);
						}
						$this->db->where("loan_id", $value->loan_id);
						$this->db->update("coop_loan_atm_detail");
					}

					$this->db->where("receipt_id", $receipt_id);
					$loan_transaction = $this->db->get("coop_loan_atm_transaction")->result()[0];

					$this->db->where("receipt_id", $receipt_id);
					$this->db->delete("coop_loan_atm_transaction");
					//รัน อัพเดท statement
					$this->update_st->update_loan_atm_transaction($loan_atm_id, $loan_transaction->transaction_datetime);
				} else if ($transaction["account_list_id"] == 16) {
					//Rollback share
					$this->db->where("share_bill", $receipt_id);
					$temp_share_transacrion = $this->db->get("coop_mem_share")->result()[0];

					if(!empty($temp_share_transacrion)) {
						$this->db->set("share_status", 3);//ยกเลิกใบเสร็จ
						$this->db->where("share_bill", $receipt_id);
						$this->db->update("coop_mem_share");
					} else {
						$where_arr = array('share_date' => $transaction["createdatetime"],
											'member_id' => $transaction["member_id"],
											'share_early_value' => $transaction["principal_payment"]);
						$this->db->where($where_arr); 
						$this->db->where("share_date", $transaction["createdatetime"]);
						$temp_share_transacrion = $this->db->get("coop_mem_share")->result()[0];
						$this->db->set("share_status", 3);//ยกเลิกใบเสร็จ
						$this->db->where($where_arr); 
						$this->db->update("coop_mem_share");
					}

					//รัน อัพเดท statement
					$this->update_st->update_share_transaction($temp_share_transacrion->member_id, $temp_share_transacrion->share_date);
				}

				//Rollback DEPOSIT if exist
				$this->db->order_by("transaction_time", "ASC");
				$this->db->order_by("transaction_id", "ASC");
				$temp_account_transaction = $this->db->get_where("coop_account_transaction", array("receipt_id" => $receipt_id) )->result()[0];

				if(!empty($temp_account_transaction)) {
					$data_insert['transaction_time'] 			= date("Y-m-d H:i:s");
					$data_insert['transaction_list'] 			= "CANCEL";
					$data_insert['transaction_withdrawal']		= 0;
					$data_insert['transaction_deposit']			= $temp_account_transaction->transaction_deposit * -1;
					$data_insert['transaction_balance']			= $temp_account_transaction->transaction_balance + $data_insert['transaction_deposit'];
					$data_insert['transaction_no_in_balance']	= $temp_account_transaction->transaction_no_in_balance + $data_insert['transaction_deposit'];
					$data_insert['account_id']					= $temp_account_transaction->account_id;
					$data_insert['user_id']						= $_SESSION['USER_ID'];
					$this->db->insert("coop_account_transaction", $data_insert);

					//รัน อัพเดท statement
					$this->update_st->update_deposit_transaction($temp_account_transaction->account_id, $temp_account_transaction->transaction_time);
				}
			}

			$receiptData = array(
				'cancel_by' => $_SESSION['USER_ID'],
				'receipt_status' => '2',
				'cancel_date' =>date('Y-m-d H:i:s')
			);
			$this->db->where('receipt_id', $receipt_id)->update('coop_receipt', $receiptData);
			
			echo"<script>document.location.href='".base_url(PROJECTPATH.'/receipt_cancel/coop_finance_month_process_receipt_cancel')."'</script>";
		}
		$arr_data['month_arr'] = $this->month_arr;

		$x=0;
		$join_arr[$x]['table'] = '(SELECT receipt_id, MAX(receipt_datetime) as max_time, member_id FROM coop_receipt GROUP BY member_id) as t2';
		$join_arr[$x]['condition'] = 't1.member_id = t2.member_id AND t1.receipt_datetime = t2.max_time';
		$join_arr[$x]['type'] = 'inner';
		$x++;
		$join_arr[$x]['table'] = 'coop_mem_apply as t4';
		$join_arr[$x]['condition'] = 't1.member_id = t4.member_id';
		$join_arr[$x]['type'] = 'inner';
		$x++;
		$join_arr[$x]['table'] = 'coop_user as t5';
		$join_arr[$x]['condition'] = 't1.admin_id = t5.user_id';
		$join_arr[$x]['type'] = 'left';	
		$x++;
		$join_arr[$x]['table'] = 'coop_prename as t6';
		$join_arr[$x]['condition'] = 't4.prename_id = t6.prename_id';
		$join_arr[$x]['type'] = 'left';
		$x++;
		$join_arr[$x]['table'] = 'coop_user as t7';
		$join_arr[$x]['condition'] = 't1.cancel_by = t7.user_id';
		$join_arr[$x]['type'] = 'left';	

		$where = "(t1.receipt_code = 'C' OR t1.receipt_code = 'B')";

		if($_GET['search_list'] == 'member_id'){
			$where .= " AND t4.member_id LIKE '%".$_GET['search_text']."%'";
		}else if($_GET['search_list'] == 'firstname_th'){
			$where .= " AND t4.firstname_th LIKE '%".$_GET['search_text']."%'";
		}else if($_GET['search_list'] == 'lastname_th'){
			$where .= " AND t4.lastname_th LIKE '%".$_GET['search_text']."%'";
		}else if($_GET['search_list'] == 'receipt_id'){
			$where .= " AND t1.receipt_id LIKE '%".$_GET['search_text']."%'";
		}else if($_GET['search_list'] == 'employee_id'){
			$where .= " AND t4.employee_id LIKE '%".$_GET['search_text']."%'";
		}

		$this->paginater_all->type(DB_TYPE);
		$this->paginater_all->select('t1.receipt_id, t1.member_id, t1.month_receipt, t1.year_receipt, t1.receipt_status, t1.receipt_datetime, t6.prename_full, t4.firstname_th, t4.lastname_th, t5.user_name,t7.user_name AS user_name_cancel,t1.cancel_date');
		$this->paginater_all->main_table('coop_receipt as t1');
		$this->paginater_all->page_now(@$_GET["page"]);
		$this->paginater_all->per_page(20);
		$this->paginater_all->page_link_limit(20);
		$this->paginater_all->where($where);
		$this->paginater_all->order_by('t1.receipt_datetime DESC');
		$this->paginater_all->join_arr($join_arr);
		$row = $this->paginater_all->paginater_process();

		$paging = $this->pagination_center->paginating($row['page'], $row['num_rows'], $row['per_page'], $row['page_link_limit'],$_GET);//$page_now = 1, $row_total = 1, $per_page = 20, $page_limit = 20
		$i = $row['page_start'];


		$arr_data['num_rows'] = $row['num_rows'];
		$arr_data['paging'] = $paging;
		$arr_data['data'] = $row['data'];
		$arr_data['i'] = $i;
		$this->db->trans_complete();
		$this->libraries->template('receipt_cancel/coop_finance_month_process_receipt_cancel',$arr_data);
	}
}
