<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Resignation extends CI_Controller {
	function __construct()
	{
		parent::__construct();
		$this->load->model("Deposit_seq_model", "deposit_seq");
	}
	public function index()
	{
		if($this->input->get('member_id')!=''){
			$member_id = $this->input->get('member_id');
		}else{
			$member_id = '';
		}
		$arr_data = array();
		$arr_data['member_id'] = $member_id;
		if($member_id != ''){
			$this->db->select(array('t1.*',
							't2.mem_group_name AS department_name',
							't3.mem_group_name AS faction_name',
							't4.mem_group_name AS level_name'));
			$this->db->from('coop_mem_apply as t1');			
			$this->db->join("coop_mem_group AS t2","t1.department = t2.id","left");
			$this->db->join("coop_mem_group AS t3","t1.faction = t3.id","left");
			$this->db->join("coop_mem_group AS t4","t1.level = t4.id","left");
			$this->db->where("t1.member_id = '".$member_id."'");
			$rs = $this->db->get()->result_array();
			$row = @$rs[0];
			
			$department = "";
			$department .= @$row['department_name'];
			$department .= (@$row["faction_name"]== 'ไม่ระบุ')?"":"  ".@$row["faction_name"];
			$department .= "  ".@$row["level_name"];
			$row['mem_group_name'] = $department;
			$arr_data['row_member'] = $row;	
			
			//อายุเกษียณ
			$this->db->select(array('retire_age'));
			$this->db->from('coop_profile');
			$rs_retired = $this->db->get()->result_array();
			$arr_data['retire_age'] = $rs_retired[0]['retire_age'];	
			
			//ประเภทสมาชิก
			$this->db->select('mem_type_id, mem_type_name');
			$this->db->from('coop_mem_type');
			$rs_mem_type = $this->db->get()->result_array();
			$mem_type_list = array();
			foreach($rs_mem_type AS $key=>$row_mem_type){
				$mem_type_list[$row_mem_type['mem_type_id']] = $row_mem_type['mem_type_name'];
			}
			
			$arr_data['mem_type_list'] = $mem_type_list;
			
			/*
			$share_type = array('SPA'=>'ซื้อหุ้นเพิ่มพิเศษ','SPM'=>'หุ้นรายเดือน');
			foreach($share_type as $key => $value){
				$this->db->select('*');
				$this->db->from('coop_mem_share');
				$this->db->where("member_id = '".$member_id."' AND share_type = '".$key."' AND share_status = '1'");
				$this->db->order_by('share_id DESC');
				$this->db->limit(1);
				$row = $this->db->get()->result_array();
				if(!empty($row)) {
					$arr_data['row_share'][$key] = $row[0];
				}
			}
			*/
			$this->db->select('*');
			$this->db->from('coop_mem_share');
			$this->db->where("member_id = '".$member_id."' AND share_status IN('1')");
			$this->db->order_by('share_date DESC, share_id DESC');
			$this->db->limit(1);
			$row_prev_share = $this->db->get()->result_array();
			$row_prev_share = @$row_prev_share[0];
			$cal_share = @$row_prev_share['share_collect_value'];
			$arr_data['cal_share'] = @$cal_share;
				
			$this->db->select('*');
			$this->db->from('coop_loan');
			$this->db->where("member_id = '".$member_id."' AND loan_status = '1'");
			$row = $this->db->get()->result_array();
			$arr_data['row_loan'] = $row;
			
			$loan_principal = 0;
			$loan_interest = 0;
			$loan_interest_remain_total = 0;
			$date_interesting = date('Y-m-d');
			$process_timestamp = date('Y-m-d H:i:s');
			foreach($arr_data['row_loan'] as $key => $value){
				$this->db->select('*');
				$this->db->from('coop_finance_transaction');
				$this->db->where("loan_id = '".$value['id']."'");
				$this->db->order_by('finance_transaction_id DESC');
				$this->db->limit(1);
				$row = $this->db->get()->result_array();
				if(!empty($row)) {
					$arr_data['row_loan'][$key]['payment_date'] = $row[0]['payment_date'];
				}else{
					$arr_data['row_loan'][$key]['payment_date'] = '';
				}
				
				$loan_principal += $value['loan_amount_balance'];

				$loan_amount = $value['loan_amount_balance'];//เงินกู้
				$loan_type = $value['loan_type'];//ประเภทเงินกู้ใช้หา เรทดอกเบี้ย
				$loan_id = $value['id'];//ใช้หาเรทดอกเบี้ยใหม่ 26/5/2562

				//Get refrain if exist
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
												->where("t1.loan_id = '".$value['id']."' AND t2.profile_year = '".$curr_BE."' AND t2.profile_month = '".date("m")."' AND t1.run_status = 1")
												->get()->row();
	
					if(empty($finance_month)) {
						$rs_date1 = $this->db->select("date(t1.transaction_datetime) as transaction_datetime")
												->from("coop_loan_transaction AS t1")
												->join("coop_receipt AS t2"," t1.receipt_id = t2.receipt_id","left")
												->join("coop_finance_transaction AS t3", "t3.receipt_id = t2.receipt_id AND t1.loan_id = t3.loan_id AND t3.interest > 0", "inner")
												->where("t1.loan_id = '".$value['id']."' AND (t2.receipt_status IS NULL OR t2.receipt_status = '') AND t1.receipt_code != 'C'")
												->order_by("t1.transaction_datetime DESC")
												->limit(1)
												->get()->result_array();
						$date1 = $rs_date1[0]['transaction_datetime'];
	
						$date2 = date("Y-m-d");//วันที่คิดดอกเบี้ย now
						$interest_data = $this->loan_libraries->calc_interest_loan_type($loan_amount, $loan_type, $date1, $date2);
						$interest_data = round($interest_data);
						$loan_interest += $interest_data;
					}
				}

				//Get loan interest non pay
				$loan_interest_remain = $this->db->select("loan_id, SUM(non_pay_amount_balance) as sum")
													->from("coop_non_pay_detail")
													->where("loan_id = '".$value['id']."' AND pay_type = 'interest'")
													->get()->row();
				if(!empty($loan_interest_remain)) {
					$loan_interest_remain_total += $loan_interest_remain->sum;
				}
			}

			//การกู้เงินฉุกเฉิน ATM
			$this->db->select(array('*'));
			$this->db->from('coop_loan_atm');
			$this->db->where("member_id = '".$member_id."' AND loan_atm_status = '1'");
			$this->db->order_by("loan_atm_id DESC");
			$rs_atm = $this->db->get()->result_array();
			//echo $this->db->last_query();
			//echo '<pre>'; print_r($rs_atm); echo '</pre>'; exit;
			foreach($rs_atm as $key_atm => $row_atm){
				$loan_principal += $row_atm['total_amount_approve']-$row_atm['total_amount_balance'];
				
				//Get Interest
				$curr_BE = date("Y") + 543;
				$finance_month = $this->db->select("*")
											->from("coop_finance_month_detail as t1")
											->join("coop_finance_month_profile as t2", "t1.profile_id = t2.profile_id")
											->where("t1.loan_atm_id = '".$row_atm['loan_atm_id']."' AND t2.profile_year = '".$curr_BE."' AND t2.profile_month = '".date("m")."' AND t1.run_status = 1")
											->get()->row();
				if(empty($finance_month)) {
					$cal_atm_interest = array();
					$cal_atm_interest['loan_atm_id'] = $row_atm['loan_atm_id'];
					$cal_atm_interest['date_interesting'] = date('Y-m-d');
					$interest_data = $this->loan_libraries->cal_atm_interest_report_test($cal_atm_interest,"echo", array("month"=> date("m"), "year" => date("Y") ), false, true )['interest_month'];
					$loan_interest += $interest_data;
				}	
				
				//Get loan interest non pay
				$loan_interest_remain = $this->db->select("loan_atm_id, SUM(non_pay_amount_balance) as sum")
													->from("coop_non_pay_detail")
													->where("loan_atm_id = '".$row_atm['loan_atm_id']."' AND pay_type = 'interest'")
													->get()->row();
				if(!empty($loan_interest_remain)) {
					$loan_interest_remain_total += $loan_interest_remain->sum;
				}
			}	

			$arr_data['loan_principal'] = @$loan_principal;
			$arr_data['loan_interest'] = @$loan_interest;
			$arr_data['loan_interest_remain_total'] = $loan_interest_remain_total;
			

			$this->db->select(
				array(
					'coop_loan.*',
					'coop_mem_apply.*'
				)
			);
			$this->db->from('coop_loan_guarantee_person');
			$this->db->join('coop_loan', 'coop_loan_guarantee_person.loan_id = coop_loan.id', 'inner');
			$this->db->join('coop_mem_apply', 'coop_loan.member_id = coop_mem_apply.member_id', 'inner');
			$this->db->where("coop_loan_guarantee_person.guarantee_person_id = '".$member_id."' AND coop_loan_guarantee_person.guarantee_person_id <>'' AND coop_loan.loan_status='1'");
			$rs_guarantee = $this->db->get()->result_array();
			$arr_data['row_guarantee'] = $rs_guarantee;
			$arr_data['count_contract'] = 0;
			$arr_data['sum_guarantee_balance'] = 0;
			foreach($rs_guarantee as $key => $row_count_guarantee){
				@$arr_data['sum_guarantee_balance'] += $row_count_guarantee['loan_amount_balance'];
				$arr_data['count_contract']++;
			}

			$this->db->select('*');
			$this->db->from('coop_maco_account');
			$this->db->where("mem_id = '".$member_id."'  AND account_status = '0'");
			$row = $this->db->get()->result_array();
			$arr_data['row_account'] = $row;
			$cal_account = 0;
			foreach($arr_data['row_account'] as $key => $value){
				$this->db->select('*');
				$this->db->from('coop_account_transaction');
				$this->db->where("account_id = '".$value['account_id']."'");
				$this->db->order_by('transaction_time DESC, transaction_id DESC');
				$this->db->limit(1);
				$row = $this->db->get()->result_array();

				$cal_result = $this->deposit_libraries->cal_deposit_interest_by_acc_date($value['account_id'], $process_timestamp);
				$close_account_interest = $cal_result['interest'];
				$close_account_interest_return = $cal_result['interest_return'];

				$arr_data['row_account'][$key]['transaction_balance'] = @$row[0]['transaction_balance'] + $close_account_interest - $close_account_interest_return;
				$cal_account += @$row[0]['transaction_balance'] + $close_account_interest - $close_account_interest_return;
			}
			$arr_data['cal_account'] = @$cal_account;
			$arr_data['total_income'] = @$cal_account+@$cal_share;
			$arr_data['total_pay'] = @$loan_principal+@$loan_interest+$loan_interest_remain_total;
			$arr_data['total_amount_all'] = @$arr_data['total_income']-@$arr_data['total_pay'];

			$this->db->select('*');
			$this->db->from('coop_mem_req_resign');
			$this->db->where("member_id = '".$member_id."'");
			$this->db->order_by('req_resign_id DESC');
			$row = $this->db->get()->result_array();
			if(!empty($row)) {
				$arr_data['data'] = $row[0];
			}

			$this->db->select(array('resign_cause_id','resign_cause_name','check_debt'));
			$this->db->from('coop_mem_resign_cause');
			$this->db->where('check_debt = 0');
			$row = $this->db->get()->result_array();
			$arr_data['resign_cause'] = $row;
			
			if(@$arr_data['total_amount_all']<0){
				$text_amount_all = "จำนวนเงินที่ต้องชำระเพิ่ม";
				$style_status = 'style="color: red"';
				$status_resignation = 'ไม่สามารถลาออกได้เนื่องจากหนี้สินมากกว่าสินทรัพย์รวม';
			}else{
				$text_amount_all = "จำนวนเงินที่จะได้รับ";
				$style_status = '';
				$status_resignation = '';
			}
			
			@$arr_data['text_amount_all'] = @$text_amount_all;
			@$arr_data['style_status'] = @$style_status;
			@$arr_data['status_resignation'] = $status_resignation;

		}else{
			$arr_data['row_member'] = array();
			$arr_data['row_share'] = array();
			$arr_data['row_loan'] = array();
			$arr_data['row_guarantee'] = array();
			$arr_data['row_account'] = array();
			$arr_data['data'] = array();
		}

		$this->libraries->template('resignation/index',$arr_data);
	}

	public function save_resignation(){
		$data = $this->input->post();
        $this->member_relinquish->save_dismiss($data);
		echo "<script> document.location.href = '".PROJECTPATH."/resignation?member_id=".$data['member_id']."' </script>";
		exit;
	}
	
	//เอาเก่าที่มีการปิดบัญชี 21 ปิดไว้ก่อน
	public function resignation_approve_old(){
		if($this->input->post()){

			$data = $this->input->post();
			$req_resign_id = $this->input->post('req_resign_id');
			$data['approve_date'] = date('Y-m-d H:i:s');
			$data['approve_user_id'] = $_SESSION['USER_ID'];

			$this->db->where('req_resign_id', $data['req_resign_id']);
			unset($data['req_resign_id']);
			$this->db->update('coop_mem_req_resign', $data);

			if($data['req_resign_status'] == '1'){
				$this->db->select('member_id');
				$this->db->from('coop_mem_req_resign');
				$this->db->where("req_resign_id = '".$this->input->post('req_resign_id')."'");
				$row = $this->db->get()->result_array();
				
				$data_member = array();
				$data_member['member_status'] = '2';
				$data_member['mem_type'] = '2';

				$this->db->where('member_id', $row[0]['member_id']);
				$this->db->update('coop_mem_apply', $data_member);
				
				$member_id = @$row[0]['member_id'];
				//ปิดบัญชี 21 อัตโนมัติ
				$this->db->select(array('coop_maco_account.account_id','coop_maco_account.mem_id','coop_deposit_type_setting.type_name'));
				$this->db->from('coop_maco_account');
				$this->db->join("coop_deposit_type_setting","coop_maco_account.type_id = coop_deposit_type_setting.type_id","inner");
				$this->db->where("
					coop_maco_account.mem_id = '".$member_id."' 
					AND coop_maco_account.account_status = '0'
					AND coop_deposit_type_setting.unique_account = '1'
				");
				$this->db->limit(1);
				$rs_account = $this->db->get()->result_array();
				$row_account = @$rs_account[0];
				if(!empty($row_account)){		
					$account_id = @$row_account['account_id'];
					
					$data_insert = array();
					$data_insert['account_amount'] = '0';
					$data_insert['account_status'] = '1';
					$data_insert['close_account_date'] = date('Y-m-d H:i:s');
					$this->db->where('account_id', $account_id);
					$this->db->update('coop_maco_account',$data_insert);
		
					$this->db->select('*');
					$this->db->from('coop_account_transaction');
					$this->db->where("account_id = '".$account_id."'");
					$this->db->order_by('transaction_time DESC');
					$this->db->limit(1);
					$rs_transaction = $this->db->get()->result_array();
					$row_transaction = 	$rs_transaction[0];
					//echo $this->db->last_query();
					//echo '<pre>'; print_r(@$row_transaction); echo '</pre>';
					if(@$row_transaction['transaction_balance'] > 0){
						$money = $row_transaction['transaction_balance'];
						$sum = 0;
						$sum_no_in = 0;
					
						$data_insert = array();
						$data_insert['transaction_time'] = date('Y-m-d H:i:s');
						$data_insert['transaction_list'] = 'CW';
						$data_insert['transaction_withdrawal'] = $money;
						$data_insert['transaction_deposit'] = '';
						$data_insert['transaction_balance'] = $sum;
						$data_insert['transaction_no_in_balance'] = $sum_no_in;
						$data_insert['user_id'] = $_SESSION['USER_ID'];
						$data_insert['account_id'] = $account_id;

						//ดึงข้อมูลลำดับรายการ ของรายการถัดไป
						$arr_seq = array(); 
						$arr_seq['account_id'] = $account_id; 
						$arr_seq['transaction_list'] = $data_insert['transaction_list'];
						$seq_no = $this->deposit_seq->gen_seq_account_transaction($arr_seq);
						$data_insert['seq_no'] = @$seq_no;

						$this->db->insert('coop_account_transaction', $data_insert);
					
						
						//////////////////////////////////////////////////////////////
						$receipt_id = $this->receipt_model->generate_receipt(date("Y-m-d H:i:s"), $rs_loan['transfer_type'] == '0' ? 0 : 1);

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
						$date_interesting = date('Y-m-d');
						foreach($row_loan as $key => $value){			
							
							$cal_loan_interest = array();
							$cal_loan_interest['loan_id'] = @$value['id'];
							$cal_loan_interest['date_interesting'] = $this->center_function->ConvertToSQLDate(@$date_interesting);
							$interest_data = $this->loan_libraries->cal_loan_interest($cal_loan_interest);
							
							$receipt_arr[$r]['receipt_id'] = @$receipt_id;
							$receipt_arr[$r]['member_id'] = @$member_id;
							$receipt_arr[$r]['loan_id'] = @$value['id'];
							$receipt_arr[$r]['account_list_id'] = '15';
							$receipt_arr[$r]['principal_payment'] = @$value['loan_amount_balance'];
							$receipt_arr[$r]['interest'] = $interest_data;
							$receipt_arr[$r]['total_amount'] = $value['loan_amount_balance']+$interest_data;
							$receipt_arr[$r]['payment_date'] = date('Y-m-d');
							$receipt_arr[$r]['createdatetime'] = date('Y-m-d H:i:s');
							$receipt_arr[$r]['loan_amount_balance'] = '0';
							$receipt_arr[$r]['transaction_text'] = 'ชำระเงินกู้เลขที่สัญญา '.@$value['contract_number'];
							$receipt_arr[$r]['deduct_type'] = 'all';
							
							$r++;
						}
						
						//การกู้เงินฉุกเฉิน ATM
						$this->db->select(array('*'));
						$this->db->from('coop_loan_atm');
						$this->db->where("member_id = '".$member_id."' AND loan_atm_status = '1'");
						$this->db->order_by("loan_atm_id DESC");
						$rs_atm = $this->db->get()->result_array();
						foreach($rs_atm as $key_atm => $row_atm){							
							$cal_loan_interest = array();
							$cal_loan_interest['loan_id'] = @$row_atm['loan_atm_id'];
							$cal_loan_interest['date_interesting'] = $this->center_function->ConvertToSQLDate(@$date_interesting);
							$interest_data = $this->loan_libraries->cal_atm_interest($cal_loan_interest);
							$loan_amount_balance = @$row_atm['total_amount_approve'] - @$row_atm['loan_amount_balance'];
							$receipt_arr[$r]['receipt_id'] = @$receipt_id;
							$receipt_arr[$r]['member_id'] = @$member_id;
							$receipt_arr[$r]['loan_atm_id'] = @$row_atm['loan_atm_id'];
							$receipt_arr[$r]['account_list_id'] = '31';
							$receipt_arr[$r]['principal_payment'] = @$loan_amount_balance;
							$receipt_arr[$r]['interest'] = @$interest_data;
							$receipt_arr[$r]['total_amount'] = @$loan_amount_balance+@$interest_data;
							$receipt_arr[$r]['payment_date'] = date('Y-m-d');
							$receipt_arr[$r]['createdatetime'] = date('Y-m-d H:i:s');
							$receipt_arr[$r]['loan_amount_balance'] = '0';
							$receipt_arr[$r]['transaction_text'] = 'ชำระเงินกู้เลขที่สัญญา '.$row_atm['contract_number'];
							$receipt_arr[$r]['deduct_type'] = 'all';
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
							$data_insert['receipt_id'] = @$value['receipt_id'];
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
							
							if(@$value['loan_id'] != ''){
								$loan_amount_balance = 0;
								$data_insert = array();
								$data_insert['loan_amount_balance'] = $loan_amount_balance;
								$data_insert['loan_status'] = '4';
								$this->db->where('id', @$value['loan_id']);
								$this->db->update('coop_loan', $data_insert);
							}
							
							
							if(@$value['loan_atm_id'] != ''){						
								
								$this->db->select(array(
									'loan_id',
									'loan_amount_balance'
								));
								$this->db->from('coop_loan_atm_detail');
								$this->db->where("loan_atm_id = '".@$value['loan_atm_id']."' AND loan_status = '0'");
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
									'total_amount_balance'
								));
								$this->db->from('coop_loan_atm');
								$this->db->where("loan_atm_id = '".@$value['loan_atm_id']."'");
								$row = $this->db->get()->result_array();
								$row_loan_atm = $row[0];
								
								$data_insert = array();
								$data_insert['total_amount_balance'] = 0;
								$data_insert['loan_atm_status'] = 4;
								$this->db->where('loan_atm_id', @$value['loan_atm_id']);
								$this->db->update('coop_loan_atm', $data_insert);
								
								$loan_amount_balance = 0;
								
								$atm_transaction = array();
								$atm_transaction['loan_atm_id'] = @$value['loan_atm_id'];
								$atm_transaction['loan_amount_balance'] = $loan_amount_balance;
								$atm_transaction['transaction_datetime'] = date('Y-m-d H:i:s');
								$atm_transaction['receipt_id'] = @$value['receipt_id'];
								$this->loan_libraries->atm_transaction($atm_transaction);
								
							}
						}
				
						if($sum_count>0){
							$data_insert = array();
							$data_insert['receipt_id'] = @$receipt_id;
							$data_insert['member_id'] = @$member_id;
							$data_insert['admin_id'] = @$_SESSION['USER_ID'];
							$data_insert['sumcount'] = $sum_count;
							$data_insert['receipt_datetime'] = date('Y-m-d H:i:s');
							$this->db->insert('coop_receipt', $data_insert);							
						}
						
						$data_insert = array();
						$data_insert['receipt_id'] = @$receipt_id;
						$this->db->where('req_resign_id', $req_resign_id);
						$this->db->update('coop_mem_req_resign', $data_insert);
						
					}
				}
			}
			echo "<script> document.location.href = '".PROJECTPATH."/resignation/resignation_approve' </script>";
		}
		$arr_data = array();
		
		$x=0;
		$join_arr = array();
		$join_arr[$x]['table'] = 'coop_mem_apply';
		$join_arr[$x]['condition'] = 'coop_mem_req_resign.member_id = coop_mem_apply.member_id';
		$join_arr[$x]['type'] = 'left';
		$x++;
		$join_arr[$x]['table'] = 'coop_mem_resign_cause';
		$join_arr[$x]['condition'] = 'coop_mem_req_resign.resign_cause_id = coop_mem_resign_cause.resign_cause_id';
		$join_arr[$x]['type'] = 'inner';
		$x++;
		$join_arr[$x]['table'] = 'coop_user';
		$join_arr[$x]['condition'] = 'coop_mem_req_resign.user_id = coop_user.user_id';
		$join_arr[$x]['type'] = 'inner';
		
		$this->paginater_all->type(DB_TYPE);
		$this->paginater_all->select('coop_mem_req_resign.*,
								coop_mem_apply.apply_date,
								coop_mem_apply.firstname_th,
								coop_mem_apply.lastname_th,
								coop_mem_resign_cause.resign_cause_name,
								coop_user.user_name');
		$this->paginater_all->main_table('coop_mem_req_resign');
		$this->paginater_all->where("");
		$this->paginater_all->page_now(@$_GET["page"]);
		$this->paginater_all->per_page(20);
		$this->paginater_all->page_link_limit(20);
		$this->paginater_all->order_by('req_resign_id DESC');
		$this->paginater_all->join_arr($join_arr);
		$row = $this->paginater_all->paginater_process();
		//echo"<pre>";print_r($row);exit;
		$paging = $this->pagination_center->paginating($row['page'], $row['num_rows'], $row['per_page'], $row['page_link_limit'], $_GET);//$page_now = 1, $row_total = 1, $per_page = 20, $page_limit = 20
		$i = $row['page_start'];


		$arr_data['num_rows'] = $row['num_rows'];
		$arr_data['paging'] = $paging;
		$arr_data['row'] = $row['data'];
		$arr_data['i'] = $i;
		//echo $this->db->last_query();exit;
		
		$this->libraries->template('resignation/resignation_approve',$arr_data);
	}

	public function resignation_approve(){
		if($this->input->post()){
            $data = $this->input->post();
            $this->member_relinquish->approve($data);
        }

		$arr_data = array();

		if(!empty($_GET['start_date'])){
			$start_date_arr = explode('/',@$_GET['start_date']);
			$start_day = $start_date_arr[0];
			$start_month = $start_date_arr[1];
			$start_year = $start_date_arr[2];
			$start_year -= 543;
			$start_date = $start_year.'-'.$start_month.'-'.$start_day;
		}

		if(!empty($_GET['end_date'])){
			$end_date_arr = explode('/',@$_GET['end_date']);
			$end_day = $end_date_arr[0];
			$end_month = $end_date_arr[1];
			$end_year = $end_date_arr[2];
			$end_year -= 543;
			$end_date = $end_year.'-'.$end_month.'-'.$end_day;
		}

		$where = "1=1";
		if($_GET["search_type"] == "1") {
			$where .= " AND coop_mem_req_resign.req_resign_date BETWEEN '".$start_date." 00:00:00.000' AND '".$end_date." 23:59:59.000'";
		} elseif ($_GET["search_type"] == "2") {
			$where .= " AND coop_mem_req_resign.approve_date BETWEEN '".$start_date." 00:00:00.000' AND '".$end_date." 23:59:59.000'";
		}

		if(!empty($_GET["resign_status"]) || $_GET["resign_status"] == "0") {
			$where .= " AND coop_mem_req_resign.req_resign_status = '".$_GET["resign_status"]."'";
		}

		if(!empty($_GET["member_id"])) {
			$where .= " AND coop_mem_req_resign.member_id = '".$_GET["member_id"]."'";
		}

		$x=0;
		$join_arr = array();
		$join_arr[$x]['table'] = 'coop_mem_apply';
		$join_arr[$x]['condition'] = 'coop_mem_req_resign.member_id = coop_mem_apply.member_id';
		$join_arr[$x]['type'] = 'left';
		$x++;
		$join_arr[$x]['table'] = 'coop_mem_resign_cause';
		$join_arr[$x]['condition'] = 'coop_mem_req_resign.resign_cause_id = coop_mem_resign_cause.resign_cause_id AND coop_mem_resign_cause.check_debt = 0';
		$join_arr[$x]['type'] = 'inner';
		$x++;
		$join_arr[$x]['table'] = 'coop_user';
		$join_arr[$x]['condition'] = 'coop_mem_req_resign.user_id = coop_user.user_id';
		$join_arr[$x]['type'] = 'inner';
		
		$this->paginater_all->type(DB_TYPE);
		$this->paginater_all->select('coop_mem_req_resign.*,
								coop_mem_apply.apply_date,
								coop_mem_apply.firstname_th,
								coop_mem_apply.lastname_th,
								coop_mem_resign_cause.resign_cause_name,
								coop_mem_resign_cause.check_debt,
								coop_user.user_name');
		$this->paginater_all->main_table('coop_mem_req_resign');
		$this->paginater_all->where($where);
		$this->paginater_all->page_now(@$_GET["page"]);
		$this->paginater_all->per_page(20);
		$this->paginater_all->page_link_limit(20);
		$this->paginater_all->order_by('req_resign_id DESC');
		$this->paginater_all->join_arr($join_arr);
		$row = $this->paginater_all->paginater_process();

		$paging = $this->pagination_center->paginating($row['page'], $row['num_rows'], $row['per_page'], $row['page_link_limit'], $_GET);//$page_now = 1, $row_total = 1, $per_page = 20, $page_limit = 20
		$i = $row['page_start'];

		$arr_data['num_rows'] = $row['num_rows'];
		$arr_data['paging'] = $paging;
		$arr_data['row'] = $row['data'];
		$arr_data['i'] = $i;

		$this->libraries->template('resignation/resignation_approve',$arr_data);
	}

	public function get_member_loans() {
		$arr_data = array();
		$member_id = $_GET['member_id'];

		$resign = $this->db->select("*")
							->from("coop_mem_req_resign")
							->where("member_id = '".$member_id."'")
							->order_by("req_resign_date DESC")
							->get()->row();
		$resign_date = strtotime($resign->req_resign_date." 00:00:00");

		$loans = $this->db->select('*')
							->from('coop_loan')
							->where("member_id = '".$member_id."' AND loan_status = '1'")
							->get()->result_array();

		$results = array();
		$debt_total = 0;
		$o = 0;
		$debug = array();
		foreach($loans as $loan) {
			$date_count = 0;
			if (($timestamp = strtotime($loan['date_last_interest'])) !== false) {
				$php_date = getdate($timestamp);
				$date = date("m", $timestamp);
				if($date == date("m", $resign_date)) {
					$date_count = date("d", $resign_date) - date("d", $timestamp);
				} else {
					$date_count = (int)date("d", $resign_date);
				}
			} else {
				$date_count = (int)date("d", $resign_date);
			}

			//Get Interest
			$loan_amount = $loan['loan_amount_balance'];//เงินกู้
			$loan_type = $loan['loan_type'];//ประเภทเงินกู้ใช้หา เรทดอกเบี้ย
			$loan_id = $loan['id'];//ใช้หาเรทดอกเบี้ยใหม่ 26/5/2562

			//echo $loan_id; exit;

			//Get refrain if exist
			$year = date("Y") + 543;
			$month = date("m");
			$loan_refrain = $this->db->select("refrain_loan_id, refrain_type")
										->from("coop_refrain_loan")
										->where("loan_id = '".$loan['id']."' AND status != 2 AND refrain_type IN (2,3) AND (year_start < ".$year." || (year_start = ".$year." AND month_start <= ".$month."))
													AND ((year_end > ".$year." || (year_end = ".$year." AND month_start >= ".$month.") || period_type = 2))")
										->get()->result_array();

			$interest = 0;
			if(empty($loan_refrain)) {
				$curr_BE = date("Y") + 543;
				$finance_month = $this->db->select("*")
											->from("coop_finance_month_detail as t1")
											->join("coop_finance_month_profile as t2", "t1.profile_id = t2.profile_id")
											->where("t1.loan_id = '".$loan['id']."' AND t2.profile_year = '".$curr_BE."' AND t2.profile_month = '".date("m")."' AND t1.run_status = 1")
											->get()->row();
				
				if(empty($finance_month)) {
					$rs_date1 = $this->db->select("date(t1.transaction_datetime) as transaction_datetime")
											->from("coop_loan_transaction AS t1")
											->join("coop_receipt AS t2"," t1.receipt_id = t2.receipt_id","left")
											->join("coop_finance_transaction AS t3", "t3.receipt_id = t1.receipt_id AND t1.loan_id = t3.loan_id AND t3.interest > 0", "inner")
											->where("t1.loan_id = '".$loan['id']."' AND t2.receipt_code != 'C'")
											//->where("t1.loan_id = '".$loan['id']."' AND (t2.receipt_status IS NULL OR t2.receipt_status = '') AND t1.receipt_id NOT LIKE '%C%'")
											->order_by("t1.transaction_datetime DESC")
											->limit(1)
											->get()->result_array();

					$date1 = $rs_date1[0]['transaction_datetime'];
	
					$tmp_date1 = date("Y-m", strtotime($date1) );
					//if($tmp_date1 != date("Y-m"))
					//	$date1 = date("Y-m-t", strtotime("last month"));
	
					//$date2 = date("Y-m-d");//วันที่คิดดอกเบี้ย now
					$date2 = date('Y-m-d', $resign_date);

					$debug['debug'][$o]['loan_id'] =$loan_id;
					$debug['debug'][$o]['date_1'] = $date1;
					$debug['debug'][$o]['date_2'] = $date2;
					$debug['debug'][$o]['loan_balance'] = $loan_amount;
					$o++;
					$interest = $this->loan_libraries->calc_interest_loan($loan_amount, $loan_id, $date1, $date2);
					$interest = round($interest);
				}
			}

			$loan_interest_remain = $this->db->select("coop_non_pay_detail.loan_id, ROUND(SUM(coop_non_pay_detail.non_pay_amount_balance),2) as sum")
												->from("coop_non_pay_detail")
												->join("coop_non_pay", "coop_non_pay_detail.non_pay_id = coop_non_pay.non_pay_id")
												->where("coop_non_pay_detail.loan_id = '".$loan['id']."' AND coop_non_pay_detail.pay_type = 'interest' AND coop_non_pay.non_pay_status = 1")
												->get()->row();
			$interest_dept = 0;
			if(!empty($loan_interest_remain)) {
				$interest_dept = $loan_interest_remain->sum;
			}

			$result = array();
			$result['debug'] = $debug;
			$result['id'] = $loan['id'];
			$result['contract_number'] = $loan['contract_number'];
			$result['loan_balance'] = $loan['loan_amount_balance'];
			$result['loan_balance_text'] = number_format($loan['loan_amount_balance'], 2);
			$result['interest'] = $interest;
			$result['interest_text'] = number_format($interest, 2);
			$result['interest_dept'] = $interest_dept;
			$result['interest_dept_text'] = number_format($interest_dept, 2);
			$result['type'] = 'loan';
			$results['loans'][] = $result;
			$debt_total += $loan['loan_amount_balance'] + $interest + $interest_dept;
		}

		$atms = $this->db->select(array('*'))
							->from('coop_loan_atm')
							->where("member_id = '".$member_id."' AND loan_atm_status = '1'")
							->order_by("loan_atm_id DESC")
							->get()->result_array();

		foreach($atms as $atm){
			$date_count = 0;
			if (($timestamp = strtotime($atm['date_last_interest'])) !== false) {
				$php_date = getdate($timestamp);
				$date = date("m", $timestamp);
				if($date == date("m", $resign_date)) {
					$date_count = date("d", $resign_date) - date("d", $timestamp);
				} else {
					$date_count = (int) date("d", $resign_date);
				}
			} else {
				$date_count = (int) date("d", $resign_date);
			}

			//Get Interest
			$curr_BE = date("Y") + 543;
			$finance_month = $this->db->select("*")
										->from("coop_finance_month_detail as t1")
										->join("coop_finance_month_profile as t2", "t1.profile_id = t2.profile_id")
										->where("t1.loan_atm_id = '".$atm['loan_atm_id']."' AND t2.profile_year = '".$curr_BE."' AND t2.profile_month = '".date("m")."' AND t1.run_status = 1")
										->get()->row();
			$interest = 0;
			if(empty($finance_month)) {
				$cal_atm_interest = array();
				$cal_atm_interest['loan_atm_id'] = $atm['loan_atm_id'];
				$cal_atm_interest['date_interesting'] = date('Y-m-d');
				$interest = $this->loan_libraries->cal_atm_interest_report_test($cal_atm_interest,"echo", array("month"=> date("m"), "year" => date("Y") ), false, true )['interest_month'];
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
										t1.loan_atm_id = '".$atm['loan_atm_id']."' 
										AND t1.transfer_status = '1' 
									")
									->get()->result_array();
			$loan_amount_balance = 0;
			foreach($row_detail as $key_detail => $value_detail){
				$loan_amount_balance += $value_detail['loan_amount_balance'];
			}

			$loan_interest_remain = $this->db->select("coop_non_pay_detail.loan_atm_id, ROUND(SUM(coop_non_pay_detail.non_pay_amount_balance),2) as sum")
												->from("coop_non_pay_detail")
												->join("coop_non_pay", "coop_non_pay_detail.non_pay_id = coop_non_pay.non_pay_id")
												->where("coop_non_pay_detail.loan_atm_id = '".$atm['loan_atm_id']."' AND coop_non_pay_detail.pay_type = 'interest' AND coop_non_pay.non_pay_status = 1")
												->get()->row();
			$interest_dept = 0;
			if(!empty($loan_interest_remain)) {
				$interest_dept = $loan_interest_remain->sum;
			}

			$result = array();
			$result['id'] = $atm['loan_atm_id'];
			$result['contract_number'] = $atm['contract_number'];
			$result['loan_balance'] = $loan_amount_balance;
			$result['loan_balance_text'] = number_format($loan_amount_balance, 2);
			$result['interest'] = $interest;
			$result['interest_text'] = number_format($interest, 2);
			$result['interest_dept'] = $interest_dept;
			$result['interest_dept_text'] = number_format($interest_dept, 2);
			$result['type'] = 'atm';
			$results['loans'][] = $result;
			$debt_total += $loan_amount_balance + $interest + $interest_dept;
		}

		$share_data = $this->db->select('*')
								->from('coop_mem_share')
								->where("member_id = '".$member_id."' AND share_status IN('1', '5')")
								->order_by('share_date DESC, share_id DESC')
								->get()->row();

		$share_balance = $share_data->share_collect_value;

		$accounts = $this->db->select('*')
								->from('coop_maco_account')
								->where("mem_id = '".$member_id."'  AND account_status = '0'")
								->get()->result_array();

		$account_balance = 0;
		foreach($accounts as $account){
			$transaction = $this->db->select('*')
										->from('coop_account_transaction')
										->where("account_id = '".$account['account_id']."'")
										->order_by('transaction_time DESC, transaction_id DESC')
										->get()->row();
			$process_timestamp = date('Y-m-d H:i:s', $resign_date);
			$cal_result = $this->deposit_libraries->cal_deposit_interest_by_acc_date($account['account_id'], $process_timestamp);
			$close_account_interest = $cal_result['interest'];
			$close_account_interest_return = $cal_result['interest_return'];

			$account_balance += $transaction->transaction_balance + $close_account_interest - $close_account_interest_return;
		}

		$account_balance = floor($account_balance);
		$results['share_balance'] = $share_balance;
		$results['account_balance'] = $account_balance;
		$results['income_balance'] = $share_balance + $account_balance;
		$results['income_balance_text'] = number_format($results['income_balance'], 2);
		$results['debt_total'] = $debt_total;

		echo json_encode($results);
	}

	public function coop_report_resign_debt_interest_payment() {
		$arr_data = array();
		$member_id = $_GET["member_id"];
		$resign = $this->db->select("*")
							->from("coop_mem_req_resign")
							->where("member_id = '{$member_id}' AND req_resign_status = 1")
							->get()->row();

		$member = $this->db->select("t1.member_id,
										t1.firstname_th,
										t1.lastname_th,
										t2.prename_full,
										t3.mem_group_name as department_name,
										t4.mem_group_name as faction_name,
										t5.mem_group_name as level_name")
							->from("coop_mem_apply as t1")
							->join("coop_prename as t2", "t1.prename_id = t2.prename_id", "left")
							->join("coop_mem_group AS t3","t1.department = t3.id","left")
							->join("coop_mem_group AS t4","t1.faction = t4.id","left")
							->join("coop_mem_group AS t5","t1.level = t5.id","left")
							->where("member_id = '{$member_id}'")
							->get()->row();

		$non_pays = $this->db->select("t1.receipt_id,
										t1.member_id,
										t1.non_pay_id,
										t2.receipt_id as out_receipt_id,
										t3.non_pay_amount,
										t3.non_pay_amount_balance,
										t3.loan_id,
										t3.loan_atm_id,
										t4.non_pay_month,
										t4.non_pay_year,
										t5.contract_number,
										t6.contract_number as contract_number_atm,
										t7.total as interst_debt
									")
								->from("coop_non_pay_receipt as t1")
								->join("coop_non_pay_receipt as t2", "t1.non_pay_id = t2.non_pay_id AND t1.receipt_id != t2.receipt_id", "left")
								->join("coop_non_pay_detail as t3", "t1.non_pay_id = t3.non_pay_id AND t3.pay_type = 'interest'", "inner")
								->join("coop_non_pay as t4", "t1.non_pay_id = t4.non_pay_id", "inner")
								->join("coop_loan as t5", "t5.id = t3.loan_id", "left")
								->join("coop_loan_atm as t6", "t6.loan_atm_id = t3.loan_atm_id", "left")
								->join("coop_mem_resign_non_pay as t7", "t7.non_pay_id = t1.non_pay_id", "left")
								->where("t1.receipt_id = '{$resign->receipt_id}' AND t1.receipt_status = 0 AND t4.non_pay_status != 0")
								->order_by("t4.non_pay_year, cast(t4.non_pay_month as int)")
								->get()->result_array();

		$datas = array();
		foreach($non_pays as $non_pay) {
			$contract_number = null;
			if(empty($non_pay["out_receipt_id"])) {
				if(!empty($non_pay["loan_id"])) {
					$contract_number = $non_pay["contract_number"];
				} else if (!empty($non_pay["loan_atm_id"])) {
					$contract_number = $non_pay["contract_number_atm"];
				}
				$non_pay_amount_balance = !empty($non_pay["interst_debt"]) ? $non_pay["non_pay_amount"] - $non_pay["interst_debt"] : $non_pay["non_pay_amount"];
				if(!empty($non_pay_amount_balance)) {
					$datas[$non_pay["non_pay_year"]][$non_pay["non_pay_month"]][$contract_number]["loan_id"] = $non_pay["loan_id"];
					$datas[$non_pay["non_pay_year"]][$non_pay["non_pay_month"]][$contract_number]["loan_atm_id"] = $non_pay["loan_atm_id"];
					$datas[$non_pay["non_pay_year"]][$non_pay["non_pay_month"]][$contract_number]["non_pay_amount"] = $non_pay["non_pay_amount"];
					$datas[$non_pay["non_pay_year"]][$non_pay["non_pay_month"]][$contract_number]["non_pay_amount_balance"] = $non_pay_amount_balance;
				}
			} else {
				$non_resign_interest = 0;
				$non_pay_amount_balance = !empty($non_pay["interst_debt"]) ? $non_pay["non_pay_amount"] - $non_pay["interst_debt"] : $non_pay["non_pay_amount"];
				if(!empty($non_pay["loan_id"])) {
					$contract_number = $non_pay["contract_number"];
					$non_resign_tran = $this->db->select("interest")
											->from("coop_finance_transaction")
											->where("loan_id = '".$non_pay["loan_id"]."' AND interest > 0 AND receipt_id = '".$non_pay["out_receipt_id"]."'")
											->get()->row();

					$non_resign_interest = $non_resign_tran->interest;
				} else if (!empty($non_pay["loan_atm_id"])) {
					$contract_number = $non_pay["contract_number_atm"];
					$non_resign_tran = $this->db->select("interest")
											->from("coop_finance_transaction")
											->where("loan_atm_id = '".$non_pay["loan_atm_id"]."' AND interest > 0 AND receipt_id = '".$non_pay["out_receipt_id"]."'")
											->get()->row();

					$non_resign_interest = $non_resign_tran->interest;
				}

				if(!empty($non_resign_interest)) {
					$non_pay_amount_balance = $non_pay_amount_balance - $non_resign_interest;
				}

				if(!empty($non_pay_amount_balance)) {
					$datas[$non_pay["non_pay_year"]][$non_pay["non_pay_month"]][$contract_number]["loan_id"] = $non_pay["loan_id"];
					$datas[$non_pay["non_pay_year"]][$non_pay["non_pay_month"]][$contract_number]["loan_atm_id"] = $non_pay["loan_atm_id"];
					$datas[$non_pay["non_pay_year"]][$non_pay["non_pay_month"]][$contract_number]["non_pay_amount"] = $non_pay["non_pay_amount"];
					$datas[$non_pay["non_pay_year"]][$non_pay["non_pay_month"]][$contract_number]["non_pay_amount_balance"] = $non_pay_amount_balance;
				}
			}
		}

		$arr_data["datas"] = $datas;
		$arr_data["member"] = $member;
		$this->preview_libraries->template_preview('resignation/coop_report_resign_debt_interest_payment',$arr_data);

	}
}
