<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Finance extends CI_Controller {
	function __construct()
	{
		parent::__construct();
	}
	public function finance_month()
	{
		$arr_data = array();
		$month_arr = array('1'=>'มกราคม','2'=>'กุมภาพันธ์','3'=>'มีนาคม','4'=>'เมษายน','5'=>'พฤษภาคม','6'=>'มิถุนายน','7'=>'กรกฎาคม','8'=>'สิงหาคม','9'=>'กันยายน','10'=>'ตุลาคม','11'=>'พฤศจิกายน','12'=>'ธันวาคม');
		$arr_data['month_arr'] = $month_arr;

		$this->db->select(array('id','mem_group_name'));
		$this->db->from('coop_mem_group');
		$this->db->where("mem_group_type = '1'");
		$row = $this->db->get()->result_array();
		$arr_data['row_mem_group'] = $row;

		$this->libraries->template('finance/finance_month',$arr_data);
	}

	//ก่อนปรับคิวรี่
	function finance_all_money_report_old(){
		$arr_data = array();
		//echo"<pre>";print_r($_GET);exit;

		if(@$_GET['level'] != ''){
			$mem_group = $_GET['level'];
		}else if(@$_GET['faction'] != ''){
			$mem_group = $_GET['faction'];
		}else if(@$_GET['department'] != ''){
			$mem_group = $_GET['department'];
		}
		$this->db->select('*');
		$this->db->from('coop_mem_group');
		$this->db->where("id = '".@$mem_group."'");
		$row = $this->db->get()->result_array();
		$row_mem_group = @$row[0];
		if(!empty($row_mem_group)){
			$department = $row_mem_group['mem_group_name'];
		}else{
			$department = 'ทั้งหมด';
		}
		$arr_data['department'] = $department;

		$where = "1=1 AND member_status = '1' ";

		if(@$_GET['department']!=''){
			$where .= " AND department = '".$_GET['department']."'";
		}
		if(@$_GET['faction']!=''){
			$where .= " AND faction = '".$_GET['faction']."'";
		}
		if(@$_GET['level']!=''){
			$where .= " AND level = '".$_GET['level']."'";
		}

		$this->db->select(array('coop_mem_apply.*','coop_prename.prename_short','coop_mem_group.mem_group_name'));
		$this->db->from('coop_mem_apply');
		$this->db->join('coop_prename','coop_prename.prename_id = coop_mem_apply.prename_id','left');
		$this->db->join('coop_mem_group','coop_mem_group.id = coop_mem_apply.level','left');
		$this->db->where($where);
		$this->db->order_by('member_id ASC');
		$row_member = $this->db->get()->result_array();

		$this->db->select('profile_id');
		$this->db->from('coop_finance_month_profile');
		$this->db->where("profile_month = '".(int)$_GET['month']."' AND profile_year = '".$_GET['year']."'");
		$row = $this->db->get()->result_array();
		$row_profile = @$row[0];

		if(@$row_profile['profile_id'] == ''){
			$data_insert = array();
			$data_insert['profile_month'] = (int)$_GET['month'];
			$data_insert['profile_year'] = $_GET['year'];
			$this->db->insert('coop_finance_month_profile', $data_insert);

			$profile_id = $this->db->insert_id();
		}else{
			$profile_id = $row_profile['profile_id'];
			//$this->db->where('profile_id', $profile_id);
			//$this->db->delete('coop_finance_month_detail');
		}

			$data_get = $this->input->get();
			//echo"<pre>";print_r($data_get);exit;
			$date_month_start = (@$_GET['year']-543).'-'.sprintf("%02d",@$_GET['month']).'-01';
			$date_month_end = date('Y-m-t',strtotime((@$_GET['year']-543).'-'.sprintf("%02d",@$_GET['month']).'-01'));

			$this->db->select('setting_value');
			$this->db->from('coop_share_setting');
			$this->db->where("setting_id = '1'");
			$row = $this->db->get()->result_array();
			$row_share_value = $row[0];
			$share_value = $row_share_value['setting_value'];

			//echo"<pre>";print_r($row_member);exit;

			$this->db->select(array('deduct_id','deduct_code','deduct_detail','deduct_type','deduct_format','deposit_type_id','deposit_amount'));
			$this->db->from('coop_deduct');
			$this->db->order_by('deduct_seq ASC');
			$deduct_list = $this->db->get()->result_array();

			$this->db->select(array('cremation_id','cremation_name_short'));
			$this->db->from('coop_cremation_data');
			$cremation_list = $this->db->get()->result_array();
			$cremation_arr = array();
			foreach($cremation_list as $key => $value){
				$this->db->select(array('cremation_id','pay_type','pay_per_person'));
				$this->db->from('coop_cremation_data_detail');
				$this->db->where("start_date <= '".$date_month_end ."' AND cremation_id = '".$value['cremation_id']."'");
				$this->db->order_by('start_date DESC');
				$this->db->limit(1);
				$cremation_detail = $this->db->get()->result_array();
				$cremation_detail = @$cremation_detail[0];
				//echo $this->db->last_query(); echo '<br>';
				if($cremation_detail['pay_type'] == '1'){
					$this->db->select(array('cremation_transfer_id'));
					$this->db->from('coop_cremation_transfer as t1');
					$this->db->join('coop_cremation_request_receive as t2','t1.cremation_request_id = t2.cremation_request_id','inner');
					$this->db->where("
						t1.date_transfer >= '".$date_month_start."'
						AND t1.date_transfer <= '".$date_month_end."'
						AND t2.cremation_type_id = '".$value['cremation_id']."'
					");
					$cremation_req = $this->db->get()->result_array();

					$cremation_arr[$value['cremation_id']]['cremation_name_short'] = $value['cremation_name_short'];
					$cremation_arr[$value['cremation_id']]['pay_per_person'] = $cremation_detail['pay_per_person'];
					$cremation_arr[$value['cremation_id']]['count_req'] = count($cremation_req);
					$cremation_arr[$value['cremation_id']]['total_pay'] = $cremation_arr[$value['cremation_id']]['pay_per_person']*$cremation_arr[$value['cremation_id']]['count_req'];
				}
			}

			$this->db->select('*');
			$this->db->from('coop_loan_atm_setting');
			$row = $this->db->get()->result_array();
			$loan_atm_setting = @$row[0];

		$data_detail = array();
		$n = 0;
		$interest_arr = array();
		foreach($row_member as $key_member => $row){
			$sum_total = 0;
			foreach($deduct_list as $key2 => $value2){
				$data_arr = array();
				$p=0;
				if($value2['deduct_code']=='OTHER'){
					$OTHER = 0;
					if($OTHER > 0){
						$data_arr[$p]['pay_amount'] = $OTHER;
						$sum_total += $OTHER;
					}
				}else if($value2['deduct_code']=='ATM'){
					$ATM = 0;
					$this->db->select(array(
						't1.loan_amount_balance',
						't1.principal_per_month',
						't2.contract_number',
						't2.loan_atm_id',
						't1.date_last_pay',
						't1.loan_date'
					));
					$this->db->from('coop_loan_atm_detail as t1');
					$this->db->join('coop_loan_atm as t2', 't1.loan_atm_id = t2.loan_atm_id', 'inner');
					$this->db->where("
						t2.member_id = '".$row['member_id']."'
						AND t2.loan_atm_status = '1'
						AND t1.date_start_period <= '".$date_month_end."'
						AND t1.loan_status = '0'
					");
					$row_atm = $this->db->get()->result_array();
					$principal_per_month = 0;
					$loan_amount_balance = 0;
					if(!empty($row_atm)){
						foreach($row_atm as $key_atm => $value_atm){
							$loan_atm_id = $value_atm['loan_atm_id'];
							$principal_per_month += $value_atm['principal_per_month'];
							$loan_amount_balance += $value_atm['loan_amount_balance'];
						}
						if($principal_per_month < $loan_atm_setting['min_principal_pay_per_month']){
							$principal_per_month = $loan_atm_setting['min_principal_pay_per_month'];
						}
						if($principal_per_month > $loan_amount_balance){
							$principal_per_month = $loan_amount_balance;
						}

						$cal_loan_interest = array();
						$cal_loan_interest['loan_atm_id'] = $loan_atm_id;
						$cal_loan_interest['date_interesting'] = $date_month_end;
						$interest = $this->loan_libraries->cal_atm_interest($cal_loan_interest);

						$deduct_format = @$value2['deduct_format'];
						if($deduct_format == '2'){
							$data_arr[$p]['pay_type'] = 'principal';
							@$data_arr[$p]['pay_amount'] = $principal_per_month;
						}else{
							$data_arr[$p]['pay_type'] = 'interest';
							@$data_arr[$p]['pay_amount'] = $interest;
						}
						$data_arr[$p]['loan_atm_id'] = $loan_atm_id;
						$sum_total += $data_arr[$p]['pay_amount'];
						$p++;
					}
				}else if($value2['deduct_code']=='LOAN' || $value2['deduct_code']=='GUARANTEE'){
					$LOAN = array();
					$where = '';
					if($value2['deduct_code'] == 'LOAN'){
						$where .= " AND (coop_loan.guarantee_for_id = '' OR coop_loan.guarantee_for_id IS NULL) ";
					}else{
						$where .= " AND (coop_loan.guarantee_for_id != '' OR coop_loan.guarantee_for_id IS NOT NULL) ";
					}
					$this->db->select(
						array(
							'coop_loan.id',
							'coop_loan.loan_type',
							'coop_loan.contract_number',
							'coop_loan.loan_amount_balance',
							'coop_loan.interest_per_year',
							'coop_loan_transfer.date_transfer',
							'coop_loan_name.loan_name',
							'coop_loan.pay_type',
							'coop_loan.money_period_1',
							'coop_loan.createdatetime',
							'coop_loan.guarantee_for_id'
						)
					);
					$this->db->from('coop_loan');
					$this->db->join('coop_loan_transfer', 'coop_loan_transfer.loan_id = coop_loan.id', 'left');
					$this->db->join('coop_loan_name', 'coop_loan_name.loan_name_id = coop_loan.loan_type', 'inner');
					$this->db->where("
						coop_loan.loan_amount_balance > 0
						AND coop_loan.member_id = '".$row['member_id']."'
						AND coop_loan.loan_status = '1'
						AND coop_loan_transfer.transfer_status = '0'
						AND coop_loan.date_start_period <= '".($data_get['year']-543)."-".sprintf("%02d",$data_get['month'])."-".date('t',strtotime(($data_get['year']-543)."-".$data_get['month']."-01"))."'
					".$where);
					$row_loan = $this->db->get()->result_array();
					//echo $this->db->last_query()."<br>";
					$j=0;

					foreach($row_loan as $key => $row_normal_loan){
						$this->db->select(array('deduct_id','ref_id'));
						$this->db->from('coop_deduct_detail');
						$this->db->where("deduct_id = '{$value2['deduct_id']}' AND ref_id = '{$row_normal_loan['loan_type']}'");
						$rs_deduct_detail = $this->db->get()->result_array();
						$ref_id = @$rs_deduct_detail[0]['ref_id'];
						if(!empty($ref_id)){
							$deduct_format = @$value2['deduct_format'];

							if($row_normal_loan['guarantee_for_id']!=''){
								$for_loan_id = $row_normal_loan['guarantee_for_id'];
							}else{
								$for_loan_id = $row_normal_loan['id'];
							}

							$this->db->select(
								array(
									'outstanding_balance',
									'principal_payment',
									'total_paid_per_month'
								)
							);
							$this->db->from('coop_loan_period');
							$this->db->where("loan_id = '".$for_loan_id."'");
							$this->db->limit(1);
							$row_loan_period = $this->db->get()->result_array();
							$row_principal_payment = $row_loan_period[0];

							$date_interesting = $date_month_end;

							//@start งดหักเงินต้นเงินกู้สามัญ
							$this->db->select(array('*'));
							$this->db->from('coop_refrain_loan');
							$this->db->where("loan_id = '".$row_normal_loan['id']."'");
							$rs_period = $this->db->get()->result_array();

							$num_period = 0;
							$diff_month = 0;
							$num_diff = 0;
							$arr_month_refrain = array();
							if(!empty($rs_period)){
								foreach($rs_period AS $key=>$row_period){
									$start = ($row_period['year_start']-543)."-".$row_period['month_start']."-01";
									$end = ($row_period['year_end']-543)."-".$row_period['month_end']."-01";
									$num_period += @$this->center_function->diff_month_interval($start,$end);

									//หาเดือน ปีที่บันทึกแล้ว
									$diff_month = @$this->center_function->diff_month_interval($start,$end);
									$num_diff = $row_period['month_start']+$diff_month;
									for($mm = $row_period['month_start'];$mm<$num_diff;$mm++){
										if($mm > 12){
											$refrain_year = $row_period['year_start']+1;
											$refrain_month = 1;
										}else{
											$refrain_year = $row_period['year_start'];
											$refrain_month = $mm;
										}
										$arr_month_refrain[$refrain_year][$refrain_month] = $refrain_month;
									}
								}
							}

							//เช็คเดือน ปี
							$check_month = 0;
							foreach($arr_month_refrain AS $key=>$year_refrain){
								foreach($year_refrain AS $key2=>$month_refrain){
									if($data_get['month'] == $key2 AND $data_get['year'] == $key){
										$check_month = 1;
									}
								}
							}
							//@end งดหักเงินต้นเงินกู้สามัญ


							$cal_loan_interest = array();
							$cal_loan_interest['loan_id'] = $row_normal_loan['id'];
							$cal_loan_interest['date_interesting'] = $date_interesting;
							$interest = $this->loan_libraries->cal_loan_interest($cal_loan_interest);

							if($row_principal_payment['principal_payment'] > $row_normal_loan['loan_amount_balance']){
								$principal_payment = @$row_normal_loan['loan_amount_balance'];
								$balance = 0;
							}else{
								$principal_payment = @$row_principal_payment['principal_payment'];
								$balance = @$row_normal_loan['loan_amount_balance']-@$row_principal_payment['principal_payment'];
							}

							$LOAN[$j]['loan_id'] = $row_normal_loan['id'];
							$LOAN[$j]['loan_type'] = $row_normal_loan['loan_name'];
							$LOAN[$j]['loan_type_id'] = $row_normal_loan['loan_type'];
							$LOAN[$j]['contract_number'] = $row_normal_loan['contract_number'];
							$LOAN[$j]['money_period_1'] = $row_normal_loan['money_period_1'];
							$LOAN[$j]['pay_loan_type'] = $row_normal_loan['pay_type'];
							$LOAN[$j]['check_month'] = @$check_month;


							if($deduct_format == '2'){
								$LOAN[$j]['text_title'] = 'ต้นเงินกู้เลขที่สัญญา';
								$LOAN[$j]['principal_payment'] = $principal_payment;
								$LOAN[$j]['interest'] = 0;
								$LOAN[$j]['total'] = $principal_payment;
								$LOAN[$j]['pay_type'] = 'principal';

							}else if($deduct_format == '1'){
								$LOAN[$j]['text_title'] = 'ดอกเบี้ยเงินกู้เลขที่สัญญา';
								$LOAN[$j]['principal_payment'] = 0;
								$LOAN[$j]['interest'] = $interest;
								$LOAN[$j]['total'] = $interest;
								$LOAN[$j]['pay_type'] = 'interest';
								$interest_arr[$row_normal_loan['id']] = $interest;
							}
							$balance = @$row_normal_loan['loan_amount_balance']-$principal_payment-$interest;
							$LOAN[$j]['balance'] = $balance;
						}
						$j++;
					}
					//echo"<pre>";print_r($LOAN);echo"</pre>";
					if(!empty($LOAN)){
						foreach($LOAN as $key3 => $value3){
							if($value3['check_month'] == 1 && $value3['pay_type'] == 'principal'){continue;}
							$data_arr[$p]['pay_type'] = @$value3['pay_type'];
							$data_arr[$p]['loan_id'] = @$value3['loan_id'];
							if($value3['pay_loan_type'] == '2' && $value3['pay_type'] == 'principal'){
								if($row_normal_loan['loan_amount_balance'] > @$value3['money_period_1']){
									$data_arr[$p]['pay_amount'] = @$value3['money_period_1'] - @$interest_arr[@$value3['loan_id']];
								}else{
									$data_arr[$p]['pay_amount'] = $row_normal_loan['loan_amount_balance'];
								}
							}else{
								$data_arr[$p]['pay_amount'] = @$value3['total'];
							}

							$sum_total += $data_arr[$p]['pay_amount'];
							$p++;

						}
					}
					//echo"<pre>";print_r($data_arr);echo"</pre>";
				}else if($value2['deduct_code']=='SHARE'){
					//งดหุ้นชั่วคราว
					$check_refrain_share = 0;
					$this->db->select('*');
					$this->db->from('coop_refrain_share');
					$this->db->where("member_id = '".$row['member_id']."' AND type_refrain = '2' AND month_refrain = '".@$data_get['month']."' AND year_refrain = '".@$data_get['year']."'");
					$this->db->order_by('refrain_id DESC');
					$rs_refrain_temporary = $this->db->get()->result_array();
					if(!empty($rs_refrain_temporary)){
						foreach($rs_refrain_temporary AS $key=>$value){
							$check_refrain_share = 1;
						}
					}

					//งดหุ้นถาวร
					$this->db->select('*');
					$this->db->from('coop_refrain_share');
					$this->db->where("member_id = '".$row['member_id']."' AND type_refrain = '1'");
					$this->db->order_by('refrain_id DESC');
					$rs_refrain_permanent = $this->db->get()->result_array();
					if(!empty($rs_refrain_permanent)){
						foreach($rs_refrain_permanent AS $key=>$value){
							$check_refrain_share = 1;
						}
					}

					//ทุนเรือนหุ้น
					if(@$row['apply_type_id'] == '1' && $check_refrain_share == 0){
						$share = @$row['share_month'];
						$data_arr[$p]['pay_amount'] = $share;
						$sum_total += @$share;
					}
				}else if($value2['deduct_code']=='DEPOSIT'){
					//เงินฝาก
					$DEPOSIT = 0;
					$deposit_type_id = @$value2['deposit_type_id'];
					$DEPOSIT = @$value2['deposit_amount'];

					$deposit_period_count = 1;
					$deposit_balance = $DEPOSIT;

					$this->db->select('*');
					$this->db->from('coop_maco_account');
					$this->db->where("mem_id = '".@$row['member_id']."' AND type_id = '".@$deposit_type_id."' AND account_status = '0'");
					$this->db->limit(1);
					$rs_account = $this->db->get()->result_array();
					$account_id = @$rs_account[0]['account_id'];
					if(!empty($account_id)){
						if($DEPOSIT > 0){
							$data_arr[$p]['deposit_account_id'] = $account_id;
							$data_arr[$p]['pay_amount'] = $DEPOSIT;
							$sum_total += @$DEPOSIT;
							$p++;
						}
					}
					$this->db->select('*');
					$this->db->from('coop_deposit_month_transaction');
					$this->db->where("member_id = '".@$row['member_id']."' AND deduction_month <= '".((int)$data_get['month'])."' AND deduction_year <= '".$data_get['year']."'");
					$this->db->order_by('deduction_year DESC, deduction_month DESC');
					$this->db->limit(1);
					$rs_deposit_month = $this->db->get()->result_array();
					$deposit_month = $rs_deposit_month[0];
					//echo $this->db->last_query();exit;
					if($deposit_month['deduction_type']=='0'){
						$data_arr[$p]['deposit_account_id'] = $deposit_month['account_id'];
						$data_arr[$p]['pay_amount'] = $deposit_month['total_amount'];
						$sum_total += $deposit_month['total_amount'];
						$p++;
					}
				}else if($value2['deduct_code']=='INSURANCE'){
					$INSURANCE = 0;
					if($INSURANCE > 0){
						$data_arr[$p]['pay_amount'] = $INSURANCE;
						$sum_total += $INSURANCE;
					}
				}else if($value2['deduct_code']=='CREMATION'){
					//ฌาปนกิจ
					$CREMATION = array();
					$this->db->select(array('cremation_type_id'));
					$this->db->from('coop_cremation_request');
					$this->db->where("member_id = '".@$row['member_id']."' AND cremation_status IN ('6')");
					$row_cremation = $this->db->get()->result_array();
					foreach($row_cremation as $key3 => $value3){
						if(@$cremation_arr[$value3['cremation_type_id']]['total_pay'] > 0){
							$CREMATION[$value3['cremation_type_id']] = $cremation_arr[$value3['cremation_type_id']];
						}
					}

					if(!empty($CREMATION)){
						foreach($CREMATION as $key3 => $value3){
							$data_arr[$p]['pay_amount'] = $value3['total_pay'];
							$data_arr[$p]['cremation_type_id'] = $key3;
							$sum_total += $value3['total_pay'];
						}
					}
				}else if($value2['deduct_code']=='ENTRANCE_FEE'){
					$EMTRANCE_FEE = 0;
					if($EMTRANCE_FEE > 0){
						$data_arr[$p]['pay_amount'] = $EMTRANCE_FEE;
						$sum_total += $EMTRANCE_FEE;
					}
				}

				foreach($data_arr as $key4 => $value4){
					$this->db->where('profile_id', $profile_id);
					$this->db->where('member_id', $row['member_id']);
					$this->db->where('finance_month_type !=', '1');
					$this->db->delete('coop_finance_month_detail');

					$data_detail[$n]['profile_id'] = $profile_id;
					$data_detail[$n]['member_id'] = $row['member_id'];
					$data_detail[$n]['deduct_code'] = $value2['deduct_code'];
					$data_detail[$n]['deduct_id'] = $value2['deduct_id'];
					$data_detail[$n]['pay_amount'] = @$value4['pay_amount'];
					if(@$value4['pay_type'] != ''){
						$pay_type = $value4['pay_type'];
					}else{
						$pay_type = 'principal';
					}
					$data_detail[$n]['pay_type'] = $pay_type;
					$data_detail[$n]['loan_id'] = @$value4['loan_id'];
					$data_detail[$n]['loan_atm_id'] = @$value4['loan_atm_id'];
					$data_detail[$n]['cremation_type_id'] = @$value4['cremation_type_id'];
					$data_detail[$n]['deposit_account_id'] = @$value4['deposit_account_id'];
					$n++;
					//echo"<pre>";print_r($data_detail);echo"</pre>";
				}
			}
			$row_member[$key_member]['sum_total'] = $sum_total;
			$this->db->select('*');
			$this->db->from('coop_finance_month_detail');
			$this->db->where("
				member_id = '".$row['member_id']."'
				AND profile_id = '".$profile_id."'
				AND finance_month_type = '1'
			");
			$row_other = $this->db->get()->result_array();
			foreach($row_other as $key_other => $value_other){
				$row_member[$key_member]['sum_total'] += $value_other['pay_amount'];
			}
		}
		//exit;
		//echo"<pre>";print_r($data_detail);echo"</pre>";exit;
		foreach($data_detail as $key => $value){
			$data_insert = array();
			$data_insert['profile_id'] = $value['profile_id'];
			$data_insert['member_id'] = $value['member_id'];
			$data_insert['deduct_code'] = $value['deduct_code'];
			$data_insert['deduct_id'] = $value['deduct_id'];
			$data_insert['pay_amount'] = number_format($value['pay_amount'],2,'.','');
			$data_insert['real_pay_amount'] = number_format($value['pay_amount'],2,'.','');
			$data_insert['pay_type'] = $value['pay_type'];
			$data_insert['loan_id'] = $value['loan_id'];
			$data_insert['loan_atm_id'] = $value['loan_atm_id'];
			$data_insert['cremation_type_id'] = $value['cremation_type_id'];
			$data_insert['deposit_account_id'] = $value['deposit_account_id'];
			$data_insert['run_status'] = '0';
			$data_insert['finance_month_type'] = '0';
			$this->db->insert('coop_finance_month_detail', $data_insert);
		}

		$arr_data['row_member'] = $row_member;
		$month_arr = $this->center_function->month_arr();
		$this->center_function->toast('สร้างรายการเรียกเก็บเดือน'.$month_arr[(int)$_GET['month']]." ".$_GET['year']." เรียบร้อยแล้ว");
		echo "<script>document.location.href = '".base_url(PROJECTPATH.'/finance/finance_month')."'</script>";
		exit;
		//$this->load->view('finance/finance_all_money_report',$arr_data);
	}

	function finance_all_money_report(){
		//set_time_limit (9000000000);
		$arr_data = array();
		//echo"<pre>";print_r($_GET);exit;

		if(@$_GET['level'] != ''){
			$mem_group = $_GET['level'];
		}else if(@$_GET['faction'] != ''){
			$mem_group = $_GET['faction'];
		}else if(@$_GET['department'] != ''){
			$mem_group = $_GET['department'];
		}
		$row = $this->db->select('*')
		->from('coop_mem_group')
		->where("id = '".@$mem_group."'")
		->get()->result_array();
		$row_mem_group = @$row[0];
		if(!empty($row_mem_group)){
			$department = $row_mem_group['mem_group_name'];
		}else{
			$department = 'ทั้งหมด';
		}
		$arr_data['department'] = $department;

		$where = "1=1 AND member_status = '1' AND mem_type = '1' ";

		if(@$_GET['department']!=''){
			$where .= " AND department = '".$_GET['department']."'";
		}
		if(@$_GET['faction']!=''){
			$where .= " AND faction = '".$_GET['faction']."'";
		}
		if(@$_GET['level']!=''){
			$where .= " AND level = '".$_GET['level']."'";
		}
		// $where .= " AND coop_mem_apply.member_id IN ('015997')";
		if(@$_GET['run_current']!=""){
			$this->db->limit( @$_GET['limit'], ($_GET['run_current']-1)*@$_GET['limit'] );
		}
		$row_member = $this->db->select(array('coop_mem_apply.member_id','coop_mem_apply.apply_type_id','coop_mem_apply.share_month','coop_prename.prename_short','coop_mem_group.mem_group_name'))
		->from('coop_mem_apply')
		->join('coop_prename','coop_prename.prename_id = coop_mem_apply.prename_id','left')
		->join('coop_mem_group','coop_mem_group.id = coop_mem_apply.level','left')
		->where($where)
		->order_by('member_id ASC')
		->get()->result_array();
		if(!$row_member && @$_GET['run_current'] != ""){
			echo json_encode(array("result" => 'end'));
			exit;
		}

		$row_profile = $this->db->select('profile_id')
		->from('coop_finance_month_profile')
		->where("profile_month = '".(int)$_GET['month']."' AND profile_year = '".$_GET['year']."'")
		->get()->result_array();
		$row_profile = $row_profile[0];

		if($row_profile['profile_id'] == ''){
			$data_insert = array();
			$data_insert['profile_month'] = (int)$_GET['month'];
			$data_insert['profile_year'] = $_GET['year'];
			$this->db->insert('coop_finance_month_profile', $data_insert);

			$profile_id = $this->db->insert_id();
		}else{
			$profile_id = $row_profile['profile_id'];
			//$this->db->where('profile_id', $profile_id);
			//$this->db->delete('coop_finance_month_detail');
		}

		$data_get = $this->input->get();
		//echo"<pre>";print_r($data_get);exit;
		$date_month_start = (@$_GET['year']-543).'-'.sprintf("%02d",@$_GET['month']).'-01';
		$date_month_end = date('Y-m-t',strtotime((@$_GET['year']-543).'-'.sprintf("%02d",@$_GET['month']).'-01'));

		$row = $this->db->select('setting_value')
		->from('coop_share_setting')
		->where("setting_id = '1'")
		->get()->result_array();
		$row_share_value = $row[0];
		$share_value = $row_share_value['setting_value'];

		//echo"<pre>";print_r($row_member);exit;

		$deduct_list = $this->db->select(array('deduct_id','deduct_code','deduct_detail','deduct_type','deduct_format','deposit_type_id','deposit_amount'))
		->from('coop_deduct')
		->order_by('deduct_seq ASC')
		->get()->result_array();
		//echo $this->db->last_query(); echo '<br>';
		$cremation_list = $this->db->select(array('cremation_id','cremation_name_short'))
		->from('coop_cremation_data')
		->get()->result_array();
		$cremation_arr = array();
		foreach($cremation_list as $key => $value){
			$cremation_detail = $this->db->select(array('cremation_id','pay_type','pay_per_person'))
			->from('coop_cremation_data_detail')
			->where("start_date <= '".$date_month_end ."' AND cremation_id = '".$value['cremation_id']."'")
			->order_by('start_date DESC')
			->limit(1)
			->get()->result_array();
			$cremation_detail = @$cremation_detail[0];
			//echo $this->db->last_query(); echo '<br>';
			if($cremation_detail['pay_type'] == '1'){
				$cremation_req = $this->db->select(array('cremation_transfer_id'))
				->from('coop_cremation_transfer as t1')
				->join('coop_cremation_request_receive as t2','t1.cremation_request_id = t2.cremation_request_id','inner')
				->where("
					t1.date_transfer >= '".$date_month_start."'
					AND t1.date_transfer <= '".$date_month_end."'
					AND t2.cremation_type_id = '".$value['cremation_id']."'
				")
				->get()->result_array();

				$cremation_arr[$value['cremation_id']]['cremation_name_short'] = $value['cremation_name_short'];
				$cremation_arr[$value['cremation_id']]['pay_per_person'] = $cremation_detail['pay_per_person'];
				$cremation_arr[$value['cremation_id']]['count_req'] = count($cremation_req);
				$cremation_arr[$value['cremation_id']]['total_pay'] = $cremation_arr[$value['cremation_id']]['pay_per_person']*$cremation_arr[$value['cremation_id']]['count_req'];
			}
		}

		$row = $this->db->select('*')
		->from('coop_loan_atm_setting')
		->get()->result_array();
		$loan_atm_setting = @$row[0];

		$data_detail = array();
		$n = 0;
		$interest_arr = array();
		foreach($row_member as $key_member => $row){
			$sum_total = 0;
			foreach($deduct_list as $key2 => $value2){
				$data_arr = array();
				$p=0;
				if($value2['deduct_code']=='OTHER'){
					$OTHER = 0;
					if($OTHER > 0){
						$data_arr[$p]['pay_amount'] = $OTHER;
						$sum_total += $OTHER;
					}
				}else if($value2['deduct_code']=='ATM'){
					$ATM = 0;
					$this->db->select(array(
						't1.loan_amount_balance',
						't1.principal_per_month',
						't2.contract_number',
						't2.loan_atm_id',
						't1.date_last_pay',
						't1.loan_date'
					));
					$row_atm = $this->db->from('coop_loan_atm_detail as t1')
					->join('coop_loan_atm as t2', 't1.loan_atm_id = t2.loan_atm_id', 'inner')
					->where("
						t2.member_id = '".$row['member_id']."'
						AND t2.loan_atm_status = '1'
						AND t1.date_start_period <= '".$date_month_end."'
						AND t1.loan_status = '0'
					")
					->get()->result_array();
					//echo $this->db->last_query(); echo '<br>';
					$principal_per_month = 0;
					$loan_amount_balance = 0;
					if(!empty($row_atm)){
						foreach($row_atm as $key_atm => $value_atm){
							$loan_atm_id = $value_atm['loan_atm_id'];
							//$principal_per_month += $value_atm['principal_per_month'];
							$loan_amount_balance += $value_atm['loan_amount_balance'];
						}
						/*if($principal_per_month < $loan_atm_setting['min_principal_pay_per_month']){
							$principal_per_month = $loan_atm_setting['min_principal_pay_per_month'];
						}
						if($principal_per_month > $loan_amount_balance){
							$principal_per_month = $loan_amount_balance;
						}
						*/

						$cal_loan_interest = array();
						$cal_loan_interest['loan_atm_id'] = $loan_atm_id;
						$cal_loan_interest['date_interesting'] = $date_month_end;
						$interest = $this->loan_libraries->cal_atm_interest_report($cal_loan_interest,"echo", array("month"=>@$_GET['month'], "year" => (@$_GET['year']-543) ));
						//echo '========<br>';
						//echo '<pre>'; print_r($interest); echo '</pre>';
						//echo '========<br>';

						$principal_per_month = @$interest['principal_month'];
						if($principal_per_month < $loan_atm_setting['min_principal_pay_per_month']){
							$principal_per_month = $loan_atm_setting['min_principal_pay_per_month'];
						}
						if($principal_per_month > $loan_amount_balance){
							$principal_per_month = $loan_amount_balance;
						}

						$deduct_format = @$value2['deduct_format'];
						if($deduct_format == '2'){
							$data_arr[$p]['pay_type'] = 'principal';
							//@$data_arr[$p]['pay_amount'] = $principal_per_month;
							//@$data_arr[$p]['pay_amount'] = ceil($principal_per_month/100)*100;
							@$data_arr[$p]['pay_amount'] = ceil($principal_per_month/100)*100;
						}else{
							$data_arr[$p]['pay_type'] = 'interest';
							@$data_arr[$p]['pay_amount'] = @$interest['interest_month'];
						}
						$data_arr[$p]['loan_atm_id'] = @$loan_atm_id;
						$sum_total += $data_arr[$p]['pay_amount'];
						$p++;
					}
				}else if($value2['deduct_code']=='LOAN' || $value2['deduct_code']=='GUARANTEE'){
					$LOAN = array();
					$where = '';
					if($value2['deduct_code'] == 'LOAN'){
						$where .= " AND (coop_loan.guarantee_for_id = '' OR coop_loan.guarantee_for_id IS NULL) ";
					}else{
						$where .= " AND (coop_loan.guarantee_for_id != '' OR coop_loan.guarantee_for_id IS NOT NULL) ";
					}
					$row_loan = $this->db->select(
						array(
							'coop_loan.id',
							'coop_loan.loan_type',
							'coop_loan.contract_number',
							'coop_loan.loan_amount_balance',
							'coop_loan.interest_per_year',
							'coop_loan_transfer.date_transfer',
							'coop_loan_name.loan_name',
							'coop_loan.pay_type',
							'coop_loan.money_period_1',
							'coop_loan.money_period_2',
							'coop_loan.createdatetime',
							'coop_loan.guarantee_for_id',
							'date_last_interest',
							'approve_date'
						)
					)
					->from('coop_loan')
					->join('coop_loan_transfer', 'coop_loan_transfer.loan_id = coop_loan.id', 'left')
					->join('coop_loan_name', 'coop_loan_name.loan_name_id = coop_loan.loan_type', 'inner')
					->where("
						coop_loan.loan_amount_balance > 0
						AND coop_loan.member_id = '".$row['member_id']."'
						AND coop_loan.loan_status = '1'
						AND coop_loan_transfer.transfer_status = '0'
						AND coop_loan.date_start_period <= '".($data_get['year']-543)."-".sprintf("%02d",$data_get['month'])."-".date('t',strtotime(($data_get['year']-543)."-".$data_get['month']."-01"))."'
					".$where)
					->get()->result_array();
					// echo $this->db->last_query()."<br><br>";
					$j=0;

					foreach($row_loan as $key => $row_normal_loan){
						$rs_deduct_detail = $this->db->select(array('deduct_id','ref_id'))
						->from('coop_deduct_detail')
						->where("deduct_id = '{$value2['deduct_id']}' AND ref_id = '{$row_normal_loan['loan_type']}'")
						->get()->result_array();
						$ref_id = @$rs_deduct_detail[0]['ref_id'];
						if(!empty($ref_id)){
							$deduct_format = @$value2['deduct_format'];

							if($row_normal_loan['guarantee_for_id']!=''){
								$for_loan_id = $row_normal_loan['guarantee_for_id'];
							}else{
								$for_loan_id = $row_normal_loan['id'];
							}

							$row_loan_period = $this->db->select(
								array(
									'outstanding_balance',
									'principal_payment',
									'total_paid_per_month'
								)
							)
							->from('coop_loan_period')
							->where("loan_id = '".$for_loan_id."'")
							->limit(1)
							->get()->result_array();
							$row_principal_payment = $row_loan_period[0];
							// var_dump($row_principal_payment);
							// echo "<br>";
							$date_interesting = $date_month_end;

							//@start งดหักเงินต้นเงินกู้สามัญ
							$rs_period = $this->db->select(array('month_start','year_start','month_end','year_end'))
							->from('coop_refrain_loan')
							->where("loan_id = '".$row_normal_loan['id']."'")
							->get()->result_array();

							$num_period = 0;
							$diff_month = 0;
							$num_diff = 0;
							$arr_month_refrain = array();
							if(!empty($rs_period)){
								foreach($rs_period AS $key=>$row_period){
									$start = ($row_period['year_start']-543)."-".$row_period['month_start']."-01";
									$end = ($row_period['year_end']-543)."-".$row_period['month_end']."-01";
									$num_period += @$this->center_function->diff_month_interval($start,$end);

									//หาเดือน ปีที่บันทึกแล้ว
									$diff_month = @$this->center_function->diff_month_interval($start,$end);
									$num_diff = $row_period['month_start']+$diff_month;
									for($mm = $row_period['month_start'];$mm<$num_diff;$mm++){
										if($mm > 12){
											$refrain_year = $row_period['year_start']+1;
											$refrain_month = 1;
										}else{
											$refrain_year = $row_period['year_start'];
											$refrain_month = $mm;
										}
										$arr_month_refrain[$refrain_year][$refrain_month] = $refrain_month;
									}
								}
							}

							//เช็คเดือน ปี
							$check_month = 0;
							foreach($arr_month_refrain AS $key=>$year_refrain){
								foreach($year_refrain AS $key2=>$month_refrain){
									if($data_get['month'] == $key2 AND $data_get['year'] == $key){
										$check_month = 1;
									}
								}
							}
							//@end งดหักเงินต้นเงินกู้สามัญ


							$cal_loan_interest = array();
							$cal_loan_interest['loan_id'] = $row_normal_loan['id'];
							$cal_loan_interest['date_interesting'] = $date_interesting;
							$interest = $this->loan_libraries->cal_loan_interest($cal_loan_interest,"echo", array("month"=>@$_GET['month'], "year" => (@$_GET['year']-543) ) );
							// echo "<br>";
							// var_dump($row_normal_loan);
							// echo "<hr>";
							if($row_principal_payment['principal_payment'] > $row_normal_loan['loan_amount_balance']){
								$principal_payment = @$row_normal_loan['loan_amount_balance'];
								$balance = 0;
							}else{
								$principal_payment = @$row_principal_payment['principal_payment'];
								$balance = @$row_normal_loan['loan_amount_balance']-@$row_principal_payment['principal_payment'];
							}


							$LOAN[$j]['loan_id'] = $row_normal_loan['id'];
							$LOAN[$j]['loan_type'] = $row_normal_loan['loan_name'];
							$LOAN[$j]['loan_type_id'] = $row_normal_loan['loan_type'];
							$LOAN[$j]['contract_number'] = $row_normal_loan['contract_number'];
							$LOAN[$j]['money_period_1'] = $row_normal_loan['money_period_1'];
							$LOAN[$j]['money_period_2'] = $row_normal_loan['money_period_2'];
							$LOAN[$j]['date_last_interest'] = $row_normal_loan['date_last_interest'];
							$LOAN[$j]['pay_loan_type'] = $row_normal_loan['pay_type'];
							$LOAN[$j]['check_month'] = @$check_month;


							if($deduct_format == '2'){
								$LOAN[$j]['text_title'] = 'ต้นเงินกู้เลขที่สัญญา';
								$LOAN[$j]['principal_payment'] = $principal_payment;
								$LOAN[$j]['interest'] = 0;
								$LOAN[$j]['total'] = $principal_payment;
								$LOAN[$j]['pay_type'] = 'principal';

							}else if($deduct_format == '1'){
								$LOAN[$j]['text_title'] = 'ดอกเบี้ยเงินกู้เลขที่สัญญา';
								$LOAN[$j]['principal_payment'] = 0;
								$LOAN[$j]['interest'] = $interest;
								$LOAN[$j]['total'] = $interest;
								$LOAN[$j]['pay_type'] = 'interest';
								$interest_arr[$row_normal_loan['id']] = $interest;
							}

							$balance = @$row_normal_loan['loan_amount_balance'] - $principal_payment - $interest;
							$LOAN[$j]['balance'] = $balance;
						}
						$j++;
					}
					//echo"<pre>";print_r($LOAN);echo"</pre>";
					if(!empty($LOAN)){
						foreach($LOAN as $key3 => $value3){
							// echo"<pre>";print_r($value3);echo"</pre>";
							if($value3['check_month'] == 1 && $value3['pay_type'] == 'principal'){continue;}
							$data_arr[$p]['pay_type'] = @$value3['pay_type'];
							$data_arr[$p]['loan_id'] = @$value3['loan_id'];
							if($value3['pay_loan_type'] == '2' && $value3['pay_type'] == 'principal'){
								if($row_normal_loan['loan_amount_balance'] > @$value3['money_period_1']){
									if($value3['check_month'] == 0){

										//-------------------------แก้ไข เฉพาะกิจ
										$sql = 'SELECT
														id as loan_id,
														DATEDIFF(
															"'.($data_get['year']-543)."-".sprintf("%02d",$data_get['month'])."-".date('t',strtotime(($data_get['year']-543)."-".$data_get['month']."-01")).'",

														IF (
															ISNULL(date_last_interest),
															approve_date,
															date_last_interest
														)
														),
														ROUND(
															DATEDIFF(
																"'.($data_get['year']-543)."-".sprintf("%02d",$data_get['month'])."-".date('t',strtotime(($data_get['year']-543)."-".$data_get['month']."-01")).'",

															IF (
																ISNULL(date_last_interest),
																approve_date,
																date_last_interest
															)
															) * loan_amount_balance * interest_per_year / 100 / 365
														) as interest,
														pay_type,
														period_type,
														period_amount,
														IF( pay_type = 2 , (SELECT total_paid_per_month FROM `coop_loan_period` WHERE coop_loan_period.loan_id = coop_loan.id GROUP BY total_paid_per_month HAVING count(*) > 1 LIMIT 1), (SELECT
																principal_payment FROM `coop_loan_period` WHERE coop_loan_period.loan_id = coop_loan.id LIMIT 1)) as money_period_1
													FROM
														coop_loan
													WHERE
														id = '. $value3['loan_id'];
										$query_interest = $this->db->query($sql)->result_array()[0];
										$interest_fix = 6;
										$period;
										$period_type;
										$pay_type = "";
										// var_dump($query_interest);
										// $this->loan_libraries->generate_period_loan($loan_id, $pay_type, $period_type, $period, $interest_fix);
										if (!empty($value3['money_period_2']) && !empty($value3['date_last_interest'])) {
											$data_arr[$p]['pay_amount'] = @$value3['money_period_2'] > $query_interest['interest'] ? @$value3['money_period_2'] - $query_interest['interest'] : 0;
										} else {
											$data_arr[$p]['pay_amount'] = @$value3['money_period_1'] > $query_interest['interest'] ? @$value3['money_period_1'] - $query_interest['interest'] : 0;
										}
										// echo "<br>+++++++++++++++++++++++++".$data_arr[$p]['pay_amount'];
										//-------------------------------
									}else{
										if (!empty($value3['money_period_2']) && !empty($value3['date_last_interest'])) {
											$data_arr[$p]['pay_amount'] = @$value3['money_period_2'] > @$interest_arr[@$value3['loan_id']] ? @$value3['money_period_2'] - @$interest_arr[@$value3['loan_id']] : 0;
										} else {
											$data_arr[$p]['pay_amount'] = @$value3['money_period_1'] > @$interest_arr[@$value3['loan_id']] ? @$value3['money_period_1'] - @$interest_arr[@$value3['loan_id']] : 0;
										}
									}

								}else{
									$data_arr[$p]['pay_amount'] = $row_normal_loan['loan_amount_balance'];
								}
							}else{
								//-------------------------แก้ไข เฉพาะกิจ

								if($value3['check_month'] == 0 && $value3['pay_type'] != 'principal'){
									$sql = 'SELECT
													id as loan_id,
													DATEDIFF(
														"'.($data_get['year']-543)."-".sprintf("%02d",$data_get['month'])."-".date('t',strtotime(($data_get['year']-543)."-".$data_get['month']."-01")).'",

													IF (
														ISNULL(date_last_interest),
														approve_date,
														date_last_interest
													)
													),
													ROUND(
														DATEDIFF(
															"'.($data_get['year']-543)."-".sprintf("%02d",$data_get['month'])."-".date('t',strtotime(($data_get['year']-543)."-".$data_get['month']."-01")).'",

														IF (
															ISNULL(date_last_interest),
															approve_date,
															date_last_interest
														)
														) * loan_amount_balance * interest_per_year / 100 / 365
													) as interest,
													pay_type,
													period_type,
													period_amount,
													IF( pay_type = 2, (SELECT total_paid_per_month FROM `coop_loan_period` WHERE coop_loan_period.loan_id = coop_loan.id GROUP BY total_paid_per_month HAVING count(*) > 1 LIMIT 1), (SELECT
															principal_payment FROM `coop_loan_period` WHERE coop_loan_period.loan_id = coop_loan.id LIMIT 1) ) as money_period_1
												FROM
													coop_loan
												WHERE
													id = '. $value3['loan_id'];
									$query_interest = $this->db->query($sql)->result_array()[0];
									$interest_fix = 6;
									$period;
									$period_type;
									$pay_type = "";
									// var_dump($query_interest);
									// $this->loan_libraries->generate_period_loan($loan_id, $pay_type, $period_type, $period, $interest_fix);
									$data_arr[$p]['pay_amount'] = $query_interest['interest'];
									// echo "<br>!!!!!!!!!!!!!!!!!!!!!!!!".$data_arr[$p]['pay_amount'];
								}else{
									$data_arr[$p]['pay_amount'] = @$value3['total'];
								}


							}

							$sum_total += $data_arr[$p]['pay_amount'];
							$p++;

						}
					}
					//echo"<pre>";print_r($data_arr);echo"</pre>";
				}else if($value2['deduct_code']=='SHARE'){
					//งดหุ้นชั่วคราว
					$check_refrain_share = 0;
					$rs_refrain_temporary = $this->db->select('*')
					->from('coop_refrain_share')
					->where("member_id = '".$row['member_id']."' AND type_refrain = '2' AND month_refrain = '".@$data_get['month']."' AND year_refrain = '".@$data_get['year']."'")
					->order_by('refrain_id DESC')
					->get()->result_array();
					if(!empty($rs_refrain_temporary)){
						foreach($rs_refrain_temporary AS $key=>$value){
							$check_refrain_share = 1;
						}
					}

					//งดหุ้นถาวร
					$rs_refrain_permanent = $this->db->select('*')
					->from('coop_refrain_share')
					->where("member_id = '".$row['member_id']."' AND type_refrain = '1'")
					->order_by('refrain_id DESC')
					->get()->result_array();
					if(!empty($rs_refrain_permanent)){
						foreach($rs_refrain_permanent AS $key=>$value){
							$check_refrain_share = 1;
						}
					}

					//ทุนเรือนหุ้น
					if(@$row['apply_type_id'] == '1' && $check_refrain_share == 0){
						$share = @$row['share_month'];
						$data_arr[$p]['pay_amount'] = $share;
						$sum_total += @$share;
					}
				}else if($value2['deduct_code']=='DEPOSIT'){
					//เงินฝาก
					$DEPOSIT = 0;
					$deposit_type_id = @$value2['deposit_type_id'];
					// $DEPOSIT = @$value2['deposit_amount'];

					$deposit_period_count = 1;
					$deposit_balance = $DEPOSIT;

					//$rs_account = $this->db->select('*')
					$rs_account = $this->db->select('account_id')
					->from('coop_maco_account')
					->where("mem_id = '".@$row['member_id']."' AND type_id = '".@$deposit_type_id."' AND account_status = '0'")
					->limit(1)
					->get()->result_array();
					$account_id = @$rs_account[0]['account_id'];
					if(!empty($account_id)){
						if($DEPOSIT > 0){
							$data_arr[$p]['deposit_account_id'] = $account_id;
							$data_arr[$p]['pay_amount'] = $DEPOSIT;
							$sum_total += @$DEPOSIT;
							$p++;
						}
					}
					$rs_deposit_month = $this->db->select(array('account_id','total_amount','deduction_type'))
					->from('coop_deposit_month_transaction')
					->where("member_id = '".$row['member_id']."' AND deduction_month <= '".((int)$data_get['month'])."' AND deduction_year <= '".$data_get['year']."'")
					->order_by('deduction_year DESC, deduction_month DESC')
					->limit(1)
					->get()->result_array();
					$deposit_month = $rs_deposit_month[0];
					//echo $this->db->last_query(); echo '<br>';
					//echo $this->db->last_query();exit;
					if($deposit_month['deduction_type']=='0'){
						$data_arr[$p]['deposit_account_id'] = $deposit_month['account_id'];
						$data_arr[$p]['pay_amount'] = $deposit_month['total_amount'];
						$sum_total += $deposit_month['total_amount'];
						$p++;
					}
				}else if($value2['deduct_code']=='INSURANCE'){
					$INSURANCE = 0;
					if($INSURANCE > 0){
						$data_arr[$p]['pay_amount'] = $INSURANCE;
						$sum_total += $INSURANCE;
					}
				}else if($value2['deduct_code']=='CREMATION'){
					//ฌาปนกิจ
					$CREMATION = array();
					$row_cremation = $this->db->select(array('cremation_type_id'))
					->from('coop_cremation_request')
					->where("member_id = '".@$row['member_id']."' AND cremation_status IN ('6')")
					->get()->result_array();
					foreach($row_cremation as $key3 => $value3){
						if(@$cremation_arr[$value3['cremation_type_id']]['total_pay'] > 0){
							$CREMATION[$value3['cremation_type_id']] = $cremation_arr[$value3['cremation_type_id']];
						}
					}

					if(!empty($CREMATION)){
						foreach($CREMATION as $key3 => $value3){
							$data_arr[$p]['pay_amount'] = $value3['total_pay'];
							$data_arr[$p]['cremation_type_id'] = $key3;
							$sum_total += $value3['total_pay'];
						}
					}
				}else if($value2['deduct_code']=='ENTRANCE_FEE'){
					$EMTRANCE_FEE = 0;
					if($EMTRANCE_FEE > 0){
						$data_arr[$p]['pay_amount'] = $EMTRANCE_FEE;
						$sum_total += $EMTRANCE_FEE;
					}
				}
				/*else if($value2['deduct_code']=='FEE_ATM'){
					//ค่าธรรมเนียม ฉ ATM
					$FEE_ATM = 0;
					$FEE_ATM = array();
					$transaction_time_start = ($data_get['year']-543)."-".sprintf("%02d",$data_get['month'])."-01 00:00:00";
					$transaction_time_end = ($data_get['year']-543)."-".sprintf("%02d",$data_get['month'])."-".date('t',strtotime(($data_get['year']-543)."-".$data_get['month']."-01")).' 23:59:59';

					$row_fee_atm = $this->db->select(array(
							't1.member_id_atm',
							't1.transaction_time',
							't1.transaction_list',
							't1.transaction_withdrawal',
							't1.account_id',
							't2.mem_id'
						))
					->from('coop_account_transaction AS t1')
					->join("coop_maco_account AS t2","t1.account_id = t2.account_id","left")
					->where("t1.transaction_list = 'CM/FE' AND t1.transaction_time BETWEEN '2018-10-01 00:00:00'AND '2018-10-31 23:59:59' AND t2.mem_id = '".@$row['member_id']."'")
					->get()->result_array();
					if(!empty($row_fee_atm)){
						foreach($row_fee_atm as $key_fee_atm => $value_fee_atm){
							if(@$value_fee_atm['transaction_withdrawal'] > 0){
								$data_arr[$p]['pay_amount'] = $value_fee_atm['transaction_withdrawal'];
								$sum_total += $value_fee_atm['transaction_withdrawal'];
							}
						}
					}
					//echo $sum_total.'<hr>';
					//echo '<pre>'; print_r($data_arr); echo '</pre>';
					//echo '<pre>'; print_r($sum_total); echo '</pre>';
				}
				*/

				foreach($data_arr as $key4 => $value4){
					$this->db->where('profile_id', $profile_id);
					$this->db->where('member_id', $row['member_id']);
					$this->db->where('finance_month_type !=', '1');
					$this->db->delete('coop_finance_month_detail');

					$data_detail[$n]['profile_id'] = $profile_id;
					$data_detail[$n]['member_id'] = $row['member_id'];
					$data_detail[$n]['deduct_code'] = $value2['deduct_code'];
					$data_detail[$n]['deduct_id'] = $value2['deduct_id'];
					$data_detail[$n]['pay_amount'] = @$value4['pay_amount'];
					if(@$value4['pay_type'] != ''){
						$pay_type = $value4['pay_type'];
					}else{
						$pay_type = 'principal';
					}
					$data_detail[$n]['pay_type'] = $pay_type;
					$data_detail[$n]['loan_id'] = @$value4['loan_id'];
					$data_detail[$n]['loan_atm_id'] = @$value4['loan_atm_id'];
					$data_detail[$n]['cremation_type_id'] = @$value4['cremation_type_id'];
					$data_detail[$n]['deposit_account_id'] = @$value4['deposit_account_id'];
					$n++;
					//echo"<pre>";print_r($data_detail);echo"</pre>";
				}
			}

			$row_member[$key_member]['sum_total'] = $sum_total;
			//$row_other = $this->db->select('*')
			$row_other = $this->db->select('pay_amount')
			->from('coop_finance_month_detail')
			->where("
				member_id = '".$row['member_id']."'
				AND profile_id = '".$profile_id."'
				AND finance_month_type = '1'
			")
			->get()->result_array();
			foreach($row_other as $key_other => $value_other){
				$row_member[$key_member]['sum_total'] += $value_other['pay_amount'];
			}
		}
		//exit;
		//echo"<pre>";print_r($data_detail);echo"</pre>";exit;
		foreach($data_detail as $key => $value){
			$data_insert = array();
			$data_insert['profile_id'] = $value['profile_id'];
			$data_insert['member_id'] = $value['member_id'];
			$data_insert['deduct_code'] = $value['deduct_code'];
			$data_insert['deduct_id'] = $value['deduct_id'];
			$data_insert['pay_amount'] = number_format($value['pay_amount'],2,'.','');
			$data_insert['real_pay_amount'] = number_format($value['pay_amount'],2,'.','');
			$data_insert['pay_type'] = $value['pay_type'];
			$data_insert['loan_id'] = $value['loan_id'];
			$data_insert['loan_atm_id'] = $value['loan_atm_id'];
			$data_insert['cremation_type_id'] = $value['cremation_type_id'];
			$data_insert['deposit_account_id'] = $value['deposit_account_id'];
			$data_insert['run_status'] = '0';
			$data_insert['finance_month_type'] = '0';
			$this->db->insert('coop_finance_month_detail', $data_insert);
		}

		if(@$_GET['run_current'] != ""){
			echo json_encode(array("result" => 'next'));
			exit;
		}
		$arr_data['row_member'] = $row_member;
		$month_arr = $this->center_function->month_arr();
		$this->center_function->toast('สร้างรายการเรียกเก็บเดือน'.$month_arr[(int)$_GET['month']]." ".$_GET['year']." เรียบร้อยแล้ว");
		// echo "<script>document.location.href = '".base_url(PROJECTPATH.'/finance/finance_month')."'</script>";
		// exit;
		//$this->load->view('finance/finance_all_money_report',$arr_data);
	}

	function finance_month_process(){
		//exit;
		$arr_data = array();
		$month_arr = array('1'=>'มกราคม','2'=>'กุมภาพันธ์','3'=>'มีนาคม','4'=>'เมษายน','5'=>'พฤษภาคม','6'=>'มิถุนายน','7'=>'กรกฎาคม','8'=>'สิงหาคม','9'=>'กันยายน','10'=>'ตุลาคม','11'=>'พฤศจิกายน','12'=>'ธันวาคม');
		$arr_data['month_arr'] = $month_arr;
		//ตอนแรกเป็น get แต่จะเปลี่ยน form เป็น post
		//$_GET = @$_POST;
		$month = @$_GET['month']!=''?$_GET['month']:(int)date('m');
		$year = @$_GET['year']!=''?$_GET['year']:(date('Y')+543);
		$show_row = @$_GET['show_row']!=''?$_GET['show_row']:'100';
		$arr_data['month'] = $month;
		$arr_data['year'] = $year;
		$arr_data['show_row'] = $show_row;
		if(@$_GET['level'] != ''){
			$mem_group = $_GET['level'];
		}else if(@$_GET['faction'] != ''){
			$mem_group = $_GET['faction'];
		}else if(@$_GET['department'] != ''){
			$mem_group = $_GET['department'];
		}
		$this->db->select('*');
		$this->db->from('coop_mem_group');
		$this->db->where("id = '".@$mem_group."'");
		$row = $this->db->get()->result_array();
		$row_mem_group = @$row[0];
		if(!empty($row_mem_group)){
			$department = $row_mem_group['mem_group_name'];
		}else{
			$department = 'ทั้งหมด';
		}
		$arr_data['department'] = $department;

		$where = " AND member_status = '1' ";

		if(@$_GET['department']!=''){
			$where .= " AND department = '".$_GET['department']."'";
		}
		if(@$_GET['faction']!=''){
			$where .= " AND faction = '".$_GET['faction']."'";
		}
		if(@$_GET['level']!=''){
			$where .= " AND level = '".$_GET['level']."'";
		}
		if(@$_GET['mem_type_id']!=''){
			$where .= " AND mem_type_id = '".$_GET['mem_type_id']."'";
		}
		if(@$_GET['member_id']!=''){
			$where .= " AND coop_mem_apply.member_id = '".$_GET['member_id']."'";
		}

		//if(@$_GET['pay_type']!=''){
		//	$where .= " AND coop_receipt.pay_type = '".$_GET['pay_type']."'";
		//}
		//$where .= " AND (coop_receipt.receipt_id = '' OR coop_receipt.receipt_id IS NULL)";

		$this->db->select('profile_id');
		$this->db->from('coop_finance_month_profile');
		$this->db->where("profile_month = '".(int)$month."' AND profile_year = '".$year."' ");
		$row = $this->db->get()->result_array();
		$row_profile = @$row[0];
		if(@$row_profile['profile_id'] == ''){
			$data_insert = array();
			$data_insert['profile_month'] = (int)$month;
			$data_insert['profile_year'] = $year;
			$this->db->insert('coop_finance_month_profile', $data_insert);

			$profile_id = $this->db->insert_id();
		}else{
			$profile_id = $row_profile['profile_id'];
			//$this->db->where('profile_id', $profile_id);
			//$this->db->delete('coop_finance_month_detail');
		}

		$x=0;
		$join_arr = array();
		$join_arr[$x]['table'] = 'coop_prename';
		$join_arr[$x]['condition'] = 'coop_prename.prename_id = coop_mem_apply.prename_id';
		$join_arr[$x]['type'] = 'left';
		$x++;
		$join_arr[$x]['table'] = 'coop_mem_group';
		$join_arr[$x]['condition'] = 'coop_mem_group.id = coop_mem_apply.level';
		$join_arr[$x]['type'] = 'left';
		$x++;
		$join_arr[$x]['table'] = '(SELECT * FROM coop_finance_month_detail GROUP BY member_id,profile_id ) AS coop_finance_month_detail';
		$join_arr[$x]['condition'] = "coop_finance_month_detail.member_id = coop_mem_apply.member_id AND profile_id = '".$profile_id."' AND coop_finance_month_detail.run_status = '0'";
		$join_arr[$x]['type'] = 'inner';


		//$join_arr[$x]['table'] = 'coop_receipt';
		//$join_arr[$x]['condition'] = "coop_receipt.member_id = coop_mem_apply.member_id AND finance_month_profile_id = '".$profile_id."'";
		//$join_arr[$x]['type'] = 'left';


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
		/*$this->paginater_all->select(
				'coop_mem_apply.member_id,
				coop_mem_apply.firstname_th,
				coop_mem_apply.lastname_th,
				coop_prename.prename_short,
				coop_mem_group.mem_group_name,
				coop_receipt.receipt_id,
				coop_receipt.pay_type'
		);
		*/
		$this->paginater_all->main_table('coop_mem_apply');
		$this->paginater_all->where("1=1 ".$where);
		$this->paginater_all->page_now(@$_GET["page"]);
		$this->paginater_all->per_page($show_row);
		$this->paginater_all->page_link_limit($show_row);
		$this->paginater_all->group_by('coop_mem_apply.member_id');
		$this->paginater_all->order_by('coop_mem_apply.member_id ASC');
		$this->paginater_all->join_arr($join_arr);
		$row = $this->paginater_all->paginater_process();
		//echo $this->db->last_query(); exit;
		$paging = $this->pagination_center->paginating($row['page'], $row['num_rows'], $row['per_page'], $row['page_link_limit'],@$_GET);//$page_now = 1, $row_total = 1, $per_page = 20, $page_limit = 20
		$i = $row['page_start'];

		$pay_type = array('0'=>'เงินสด','1'=>'โอนเงิน');
		foreach($row['data'] as $key => $value){
			$pay_amount = 0;
			$this->db->select('*');
			$this->db->from('coop_finance_month_detail');
			$this->db->where("profile_id = '".@$profile_id."' AND member_id = '".@$value['member_id']."'");
			$row_detail = $this->db->get()->result_array();
			foreach($row_detail as $key2 => $value2){
				$pay_amount += $value2['pay_amount'];
			}
			$row['data'][$key]['pay_amount'] = $pay_amount;

			$this->db->select('*');
			$this->db->from('coop_non_pay');
			$this->db->where("member_id = '".@$value['member_id']."' AND non_pay_month = '".(int)$month."' AND non_pay_year = '".$year."'");
			$row_non_pay = $this->db->get()->result_array();
			$row_non_pay = @$row_non_pay[0];

			$row['data'][$key]['real_pay_amount'] = $pay_amount - @$row_non_pay['non_pay_amount'];

			/*$this->db->select(array('receipt_id','pay_type'));
			$this->db->from('coop_receipt');
			$this->db->where("finance_month_profile_id = '".@$profile_id."' AND member_id = '".@$value['member_id']."'");
			$row_receipt = $this->db->get()->result_array();
			$row_receipt = @$row_receipt[0];*/

			$row['data'][$key]['receipt_id'] = @$value['receipt_id'];
			$row['data'][$key]['pay_type'] = @$pay_type[@$value['pay_type']];
		}

		$arr_data['num_rows'] = $row['num_rows'];
		$arr_data['paging'] = $paging;
		$arr_data['row'] = $row['data'];
		$arr_data['i'] = $i;
		//echo '<pre>'; print_r($arr_data['row']); echo '</pre>'; exit;

		$this->db->select(array('id','mem_group_name'));
		$this->db->from('coop_mem_group');
		$this->db->where("mem_group_type = '1'");
		$row_mem_group = $this->db->get()->result_array();
		$arr_data['mem_group'] = $row_mem_group;

		foreach(@$row_mem_group AS $key=>$value){
			$arr_data['arr_mem_group'][@$value['id']] = @$value['mem_group_name'];
		}

		if(@$_GET['department']!=''){
			$this->db->select(array('id','mem_group_name'));
			$this->db->from('coop_mem_group');
			$this->db->where("mem_group_parent_id = '".$_GET['department']."'");
			$row_mem_group = $this->db->get()->result_array();

			$arr_data['faction'] = $row_mem_group;

			foreach(@$row_mem_group AS $key=>$value){
				$arr_data['arr_faction'][@$value['id']] = @$value['mem_group_name'];
			}
		}

		if(@$_GET['faction']!=''){
			$this->db->select(array('id','mem_group_name'));
			$this->db->from('coop_mem_group');
			$this->db->where("mem_group_parent_id = '".$_GET['faction']."'");
			$row_mem_group = $this->db->get()->result_array();
			$arr_data['level'] = $row_mem_group;

			foreach(@$row_mem_group AS $key=>$value){
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
		$this->db->join('coop_finance_month_detail as t2','t1.profile_id = t2.profile_id','inner');
		$this->db->where("t2.run_status = '1'");
		$this->db->order_by('profile_id DESC');
		$this->db->limit(1);
		$row = $this->db->get()->result_array();
		$arr_data['last_profile'] = @$row[0];

		$month_arr = array('1'=>'มกราคม','2'=>'กุมภาพันธ์','3'=>'มีนาคม','4'=>'เมษายน','5'=>'พฤษภาคม','6'=>'มิถุนายน','7'=>'กรกฎาคม','8'=>'สิงหาคม','9'=>'กันยายน','10'=>'ตุลาคม','11'=>'พฤศจิกายน','12'=>'ธันวาคม');
		$arr_data['month_arr'] = $month_arr;

		$this->db->select('mem_type_id, mem_type_name');
		$this->db->from('coop_mem_type');
		$row = $this->db->get()->result_array();
		$arr_data['mem_type'] = $row;
		//echo '<pre>'; print_r($arr_data['row']); echo '</pre>'; exit;
		$this->libraries->template('finance/finance_month_process',$arr_data);
	}

	function finance_month_run_process_backup(){
		//echo"<pre>";print_r($_GET);print_r($_POST);exit;
		$process_date = $this->center_function->ConvertToSQLDate($_POST['process_date']); //exit;
		$month = @$_GET['month'];
		$year = @$_GET['year'];

		$get_param = '?';
		foreach(@$_GET as $key => $value){
			$get_param .= '&'.$key.'='.$value;
		}

		$where = "1=1 AND member_status = '1' ";

		/*if(@$_GET['department']!=''){
			$where .= " AND department = '".$_GET['department']."'";
		}
		if(@$_GET['faction']!=''){
			$where .= " AND faction = '".$_GET['faction']."'";
		}
		if(@$_GET['level']!=''){
			$where .= " AND level = '".$_GET['level']."'";
		}
		if(@$_GET['mem_type_id']!=''){
			$where .= " AND mem_type_id = '".$_GET['mem_type_id']."'";
		}
		if(@$_GET['member_id']!=''){
			$where .= " AND member_id = '".$_GET['member_id']."'";
		}*/
		if(!empty($_POST['member_id'])){
			$where .= " AND member_id IN(";
			foreach($_POST['member_id'] as $key => $value){
				$where .= "'".$value."',";
			}
			$where = substr($where,0,-1);
			$where .= ")";
		}else{
			$this->center_function->toastDanger('ไม่สามารถประมวลผลได้');
			echo "<script>document.location.href = '".base_url(PROJECTPATH.'/finance/finance_month_process'.$get_param)."'</script>";
			exit;
		}


		$this->db->select('profile_id');
		$this->db->from('coop_finance_month_profile');
		$this->db->where("profile_month = '".(int)$month."' AND profile_year = '".$year."' ");
		$row = $this->db->get()->result_array();
		$row_profile = @$row[0];
		$profile_id = $row_profile['profile_id'];

		$this->db->select('setting_value');
		$this->db->from('coop_share_setting');
		$this->db->where("setting_id = '1'");
		$row = $this->db->get()->result_array();
		$row_share_value = $row[0];
		$share_value = $row_share_value['setting_value'];

		$this->db->select(array('coop_mem_apply.member_id'));
		$this->db->from('coop_mem_apply');
		$this->db->where($where);
		$this->db->order_by('member_id ASC');
		$row_member = $this->db->get()->result_array();
		//echo $this->db->last_query();exit;
		foreach($row_member as $key => $value){
			$this->db->select('*');
			$this->db->from('coop_non_pay');
			$this->db->where("member_id = '".@$value['member_id']."' AND non_pay_month = '".(int)$month."' AND non_pay_year = '".$year."' AND non_pay_status = '0'");
			$row_non_pay = $this->db->get()->result_array();
			$row_non_pay = @$row_non_pay[0];
			$text = '';
			if(!empty($row_non_pay)){
				$non_pay_balance = $row_non_pay['non_pay_amount'];

				$this->db->select(array('*'));
				$this->db->from('coop_finance_month_detail');
				$this->db->where("profile_id = '".@$profile_id."' AND member_id = '".$value['member_id']."' AND run_status = '0'");
				$this->db->order_by('run_id DESC');
				$row_detail = $this->db->get()->result_array();
				foreach($row_detail as $key_detail => $value_detail){
					$pay_amount = $value_detail['pay_amount'];
					$real_pay_amount = $pay_amount;
					if($pay_amount > $non_pay_balance){
						$real_pay_amount = $pay_amount - $non_pay_balance;
						$non_pay_amount = $non_pay_balance;
						$non_pay_balance = 0;
					}else{
						$non_pay_balance = $non_pay_balance - $pay_amount;
						$non_pay_amount = $pay_amount;
						$real_pay_amount = 0;
					}
					$data_insert = array();
					$data_insert['deduct_code'] = $value_detail['deduct_code'];
					$data_insert['non_pay_amount'] = $non_pay_amount;
					$data_insert['non_pay_amount_balance'] = $non_pay_amount;
					$data_insert['loan_id'] = $value_detail['loan_id'];
					$data_insert['loan_atm_id'] = $value_detail['loan_atm_id'];
					$data_insert['pay_type'] = $value_detail['pay_type'];
					$data_insert['finance_month_profile_id'] = $value_detail['profile_id'];
					$data_insert['finance_month_detail_id'] = $value_detail['run_id'];
					$data_insert['member_id'] = $value_detail['member_id'];
					$data_insert['non_pay_id'] = $row_non_pay['non_pay_id'];
					$data_insert['cremation_type_id'] = $value_detail['cremation_type_id'];
					$data_insert['deposit_account_id'] = $value_detail['deposit_account_id'];
					$this->db->insert('coop_non_pay_detail',$data_insert);

					$data_insert = array();
					$data_insert['real_pay_amount'] = $real_pay_amount;
					$this->db->where('run_id',$value_detail['run_id']);
					$this->db->update('coop_finance_month_detail',$data_insert);
					if($non_pay_balance == 0){
						break;
					}
				}

				$data_insert = array();
				$data_insert['non_pay_status'] = '1';
				$this->db->where('non_pay_id',$row_non_pay['non_pay_id']);
				$this->db->update('coop_non_pay',$data_insert);
				$text = 'F';
			}

			// $yymm = (date("Y")+543).date("m");
			$yymm = date("m");
			$yy = (date("Y")+543);
			$yy_full = (date("Y")+543);
			$yy = substr($yy,2);

			if(empty($text)) {
				$text = 'B';
			}

			$this->db->select(array('*'));
			$this->db->from('coop_receipt');
			$this->db->where("receipt_id LIKE '".$yymm.'B'.$yy."%' OR receipt_id LIKE '".$yymm.'Y'.$yy."%'");
			$this->db->order_by("order_by DESC");
			$this->db->limit(1);
			//  echo "".$this->db->get_compiled_select(null, false)."<br><br><br><br>";exit;

			$row_receipt = $this->db->get()->result_array();
			$row_receipt = @$row_receipt[0];

			// if(@$row_receipt['receipt_id'] != '') {
			// 	$id = (int) substr($row_receipt["receipt_id"], 6);
			// 	$receipt_number = $yymm.sprintf("%06d", $id + 1);
			// }else {
			// 	$receipt_number = $yymm."000001";
			// }

			if($row_receipt['receipt_id'] != '') {
				$id = (int) substr($row_receipt["receipt_id"], 6);
				$receipt_number = $yymm.''.$text.''.$yy.sprintf("%06d", $id + 1);

			}else {
				$receipt_number = $yymm.''.$text.''.$yy."000001";
			}

			$order_by_id =  $row_receipt["order_by"]+1 ;

			$sum_count = 0;

			//Prepare return
			//For Monthly Process
			$data_insert = array();
			$data_insert['member_id'] = $value['member_id'];
			$data_insert['finance_month_profile_id'] = @$profile_id;
			$data_insert['createdatetime'] = $process_date." ".date('H:i:s');
			$data_insert['return_profile_status'] = '0';
			$data_insert['return_from'] = 'monthly';
			$data_insert['admin_id'] = $_SESSION['USER_ID'];
			$this->db->insert('coop_return_interest_profile', $data_insert);
			$return_profile_id = $this->db->insert_id();
			$total_return_amount = 0;

			$this->db->select(array('*'));
			$this->db->from('coop_finance_month_detail');
			$this->db->where("profile_id = '".@$profile_id."' AND member_id = '".$value['member_id']."' AND run_status = '0'");
			$this->db->order_by('run_id ASC');
			$row_process = $this->db->get()->result_array();
			foreach($row_process as $key_process => $value_process){
				$data_insert = array();
				$data_insert['run_status'] = '1';
				$this->db->where('run_id', $value_process['run_id']);
				$this->db->update('coop_finance_month_detail', $data_insert);
				//echo $this->db->last_query()."<br>";
				if($value_process['real_pay_amount']>0){

					$this->db->select(array('*'));
					$this->db->from('coop_deduct');
					$this->db->where("deduct_id = '".$value_process['deduct_id']."'");
					$this->db->limit(1);
					$row_deduct = $this->db->get()->result_array();
					$row_deduct = @$row_deduct[0];

					if($value_process['deduct_code'] == 'LOAN' || $value_process['deduct_code'] == 'GUARANTEE'){
							$this->db->select(
								array(
									'coop_loan.id',
									'coop_loan.loan_type',
									'coop_loan.contract_number',
									'coop_loan.loan_amount_balance',
									'coop_loan.interest_per_year',
									'coop_loan.period_now',
									'coop_loan_transfer.date_transfer',
									'coop_loan_name.loan_name',
									'coop_loan.createdatetime'
								)
							);
							$this->db->from('coop_loan');
							$this->db->join('coop_loan_transfer', 'coop_loan_transfer.loan_id = coop_loan.id', 'left');
							$this->db->join('coop_loan_name', 'coop_loan_name.loan_name_id = coop_loan.loan_type', 'inner');
							$this->db->where("
								coop_loan.id = '".$value_process['loan_id']."'
							");
							$row_loan = $this->db->get()->result_array();
							$row_loan = $row_loan[0];

							if($row_loan['period_now']!=''){
								$period_count = $row_loan['period_now']+1;
							}else{
								$period_count = 1;
							}

						if($value_process['pay_type'] == 'principal'){
							$interest = '';
							$return_amount = 0;
							if($value_process['real_pay_amount'] > $row_loan['loan_amount_balance']){
								$return_amount = $value_process['real_pay_amount'] - $row_loan['loan_amount_balance'];
								$pay_amount = $value_process['real_pay_amount'];
								$balance = 0;
							}else{
								$pay_amount = $value_process['real_pay_amount'];
								$balance = $row_loan['loan_amount_balance'] - $pay_amount;
							}

							$transaction_text = 'ต้นเงินกู้เลขที่สัญญา '.$row_loan['contract_number'];

							if($balance > 0){
								$data_insert = array();
								$data_insert['loan_amount_balance'] = $balance;
								$data_insert['period_now'] = $period_count;
								//$data_insert['loan_status'] = '1';
								$this->db->where('id', $value_process['loan_id']);
								$this->db->update('coop_loan', $data_insert);
							}else{
								$data_insert = array();
								$data_insert['loan_amount_balance'] = $balance;
								$data_insert['period_now'] = $period_count;
								$data_insert['loan_status'] = '4';
								$this->db->where('id', $value_process['loan_id']);
								$this->db->update('coop_loan', $data_insert);
							}
							$loan_transaction = array();
							$loan_transaction['loan_id'] = $value_process['loan_id'];
							$loan_transaction['loan_amount_balance'] = $balance;
							$loan_transaction['transaction_datetime'] = date('Y-m-d H:i:s');
							$loan_transaction['receipt_id'] = $receipt_number;
							$this->loan_libraries->loan_transaction($loan_transaction);

							if($return_amount > 0){
								$data_insert = array();
								$data_insert['return_interest_amount'] = $return_amount;
								$data_insert['return_month'] = $month;
								$data_insert['return_year'] = $year;
								$data_insert['member_id'] = $value_process['member_id'];
								$data_insert['createdate'] = $process_date." ".date('H:i:s');
								$data_insert['loan_id'] = $value_process['loan_id'];
								$data_insert['pay_type'] = $value_process['pay_type'];
								$data_insert['finance_month_profile_id'] = @$profile_id;
								$data_insert['return_status'] = '0';
								$data_insert['return_profile_id'] = $return_profile_id;
								$this->db->insert('coop_return_interest', $data_insert);
								$total_return_amount += $return_amount;
							}
						}else{
							$date_interesting = $process_date;
							$cal_loan_interest = array();
							$cal_loan_interest['loan_id'] = $row_loan['id'];
							$cal_loan_interest['date_interesting'] = $date_interesting;
							$real_interest = $this->loan_libraries->cal_loan_interest($cal_loan_interest);

							//หาดอกเบี้ยจากยอดคงเหลือในจำนวนวันที่เหลือถึงสิ้นเดือน
							$this->db->select('loan_amount_balance');
							$this->db->from('coop_loan_transaction');
							$this->db->where("loan_id = '".$row_loan['id']."' AND transaction_datetime < '".$date_interesting."'");
							$this->db->order_by('transaction_datetime DESC');
							$this->db->limit(1);
							$row_last_balance_before = $this->db->get()->result_array();
							$row_last_balance_before = $row_last_balance_before[0];
							//หาจำนวนเงินก่อนะที่จะจ่ายในเดือนนี้

							$this->db->select('real_pay_amount');
							$this->db->from('coop_finance_month_detail');
							$this->db->where("loan_id = '".$row_loan['id']."' AND profile_id = '".$profile_id."' AND pay_type = 'principal'");
							$row_this_principal = $this->db->get()->result_array();
							$row_this_principal = $row_this_principal[0];
							//หาเงินต้นที่จ่ายในเดือนนี้

							$balance_after_prncipal = $row_last_balance_before['loan_amount_balance'] - $row_this_principal['real_pay_amount'];

							$date_finish_month = date('Y-m-t',strtotime($date_interesting));

							$diff = date_diff(date_create($date_interesting),date_create($date_finish_month));
							$date_count = $diff->format("%a");

							$interest_after_principal = ((($balance_after_prncipal*$row_loan['interest_per_year'])/100)/365)*$date_count;
							$interest_after_principal = round($interest_after_principal);

							$real_interest += $interest_after_principal;
							//end หาดอกเบี้ยจากยอดคงเหลือในจำนวนวันที่เหลือถึงสิ้นเดือน

							$data_insert = array();
							$data_insert['date_last_interest'] = $date_finish_month;
							$this->db->where('id',$row_loan['id']);
							$this->db->update('coop_loan',$data_insert);

							$interest = $value_process['real_pay_amount'];
							$pay_amount = '';
							$balance = '';
							$transaction_text = 'ดอกเบี้ยเงินกู้เลขที่สัญญา '.$row_loan['contract_number'];

							//คืนดอกเบี้ย
							if($value_process['real_pay_amount'] > $real_interest){
								$data_insert = array();
								$data_insert['return_interest_amount'] = ($value_process['real_pay_amount'] - $real_interest);
								$data_insert['return_month'] = $month;
								$data_insert['return_year'] = $year;
								$data_insert['member_id'] = $value_process['member_id'];
								$data_insert['createdate'] = $process_date." ".date('H:i:s');
								$data_insert['loan_id'] = $value_process['loan_id'];
								$data_insert['pay_type'] = $value_process['pay_type'];
								$data_insert['finance_month_profile_id'] = @$profile_id;
								$data_insert['return_status'] = '0';
								$data_insert['return_profile_id'] = $return_profile_id;
								$this->db->insert('coop_return_interest', $data_insert);
								$total_return_amount += ($value_process['real_pay_amount'] - $real_interest);
							}
						}
					}else if($value_process['deduct_code'] == 'SHARE'){
						$this->db->select(array('*'));
						$this->db->from('coop_mem_share');
						$this->db->where("member_id = '".$value_process['member_id']."' AND share_status = '1'");
						$this->db->order_by("share_date DESC");
						$this->db->limit(1);
						$row_share = $this->db->get()->result_array();
						$row_share = @$row_share[0];

						$pay_amount = $value_process['real_pay_amount'];
						$interest = '';
						$balance = $row_share['share_collect_value'] + $value_process['real_pay_amount'];
						$period_count = $row_share['share_period']+1;
						$transaction_text = 'ชำระเงินค่าหุ้นรายเดือน';

						$data_insert = array();
						$data_insert['member_id'] = @$value_process['member_id'];
						$data_insert['admin_id'] = @$_SESSION['USER_ID'];
						$data_insert['share_type'] = 'SPM';
						$data_insert['share_date'] = $process_date." ".date('H:i:s');
						$data_insert['share_payable'] = @$row_share['share_collect'];
						$data_insert['share_payable_value'] = @$row_share['share_collect_value'];
						$data_insert['share_early'] = ($pay_amount/$share_value);
						$data_insert['share_early_value'] = $pay_amount;
						$data_insert['share_collect'] = ($balance/$share_value);
						$data_insert['share_collect_value'] = $balance;
						$data_insert['share_value'] = $share_value;
						$data_insert['share_status'] = '1';
						$data_insert['share_bill'] = $receipt_number;
						$data_insert['share_bill_date'] = $process_date." ".date('H:i:s');
						$data_insert['share_period'] = $period_count;
						$this->db->insert('coop_mem_share', $data_insert);

					}else if($value_process['deduct_code'] == 'CREMATION'){
						$this->db->select(array('*'));
						$this->db->from('coop_cremation_data');
						$this->db->where("cremation_id = '".$value_process['cremation_type_id']."'");
						$this->db->limit(1);
						$row_cremation = $this->db->get()->result_array();
						$row_cremation = @$row_cremation[0];

						$pay_amount = $value_process['real_pay_amount'];
						$interest = '';
						$balance = '';
						$period_count = '';
						$transaction_text = $row_deduct['deduct_detail']." ".$row_cremation['cremation_name_short'];
					}else if($value_process['deduct_code'] == 'DEPOSIT'){
						$DEPOSIT = $value_process['real_pay_amount'];

						$this->db->select('*');
						$this->db->from('coop_maco_account');
						$this->db->where("account_id = '".$value_process['deposit_account_id']."'");
						$this->db->limit(1);
						$row_account = $this->db->get()->result_array();
						$row_account = @$row_account[0];

						$this->db->select('*');
						$this->db->from('coop_account_transaction');
						$this->db->where("account_id = '".$value_process['deposit_account_id']."'");
						$this->db->order_by('transaction_time DESC');
						$this->db->limit(1);
						$row_transaction = $this->db->get()->result_array();
						if(!empty($row_transaction)){
							$balance = @$row_transaction[0]['transaction_balance'];
							$balance_no_in = @$row_transaction[0]['transaction_no_in_balance'];
						}else{
							$balance = 0;
							$balance_no_in = 0;
						}
						$sum = $balance + $DEPOSIT;
						$sum_no_in = $balance_no_in + $DEPOSIT;

						$data_insert = array();
						$data_insert['transaction_time'] = $process_date." ".date('H:i:s');
						$data_insert['transaction_list'] = 'XD';
						$data_insert['transaction_withdrawal'] = '';
						$data_insert['transaction_deposit'] = $DEPOSIT;
						$data_insert['transaction_balance'] = $sum;
						$data_insert['transaction_no_in_balance'] = $sum_no_in;
						$data_insert['user_id'] = $_SESSION['USER_ID'];
						$data_insert['account_id'] = $value_process['deposit_account_id'];
						$data_insert['receipt_id'] = @$receipt_number;
						$this->db->insert('coop_account_transaction', $data_insert);

						$account_period = $row_account['account_period']!=''?($row_account['account_period']+1):1;

						$data_insert = array();
						$data_insert['account_period'] = $account_period;
						$this->db->where('account_id', $value_process['deposit_account_id']);
						$this->db->update('coop_maco_account', $data_insert);

						$pay_amount = $value_process['real_pay_amount'];
						$interest = '';
						$balance = $sum;
						$period_count = $account_period;
						$transaction_text = $row_deduct['deduct_detail']." เลขที่บัญชี".$value_process['deposit_account_id'];
					}else if($value_process['deduct_code'] == 'ATM'){
						$this->db->select(
							array(
								't1.loan_atm_id',
								't1.total_amount_approve',
								't1.total_amount_balance',
								't1.contract_number'
							)
						);
						$this->db->from('coop_loan_atm as t1');
						$this->db->where("
							t1.loan_atm_id = '".$value_process['loan_atm_id']."'
						");
						$row_loan_atm = $this->db->get()->result_array();
						$row_loan_atm = $row_loan_atm[0];
						$this->db->select(
							array(
								't1.loan_id',
								't1.loan_atm_id',
								't1.loan_amount_balance'
							)
						);
						$this->db->from('coop_loan_atm_detail as t1');
						$this->db->where("
							t1.loan_atm_id = '".$value_process['loan_atm_id']."'
							AND t1.loan_status = '0'
						");
						$this->db->order_by('loan_id ASC');
						$row_loan_atm_detail = $this->db->get()->result_array();
						if($value_process['pay_type'] == 'principal'){
							$interest = '';
							$pay_amount = $value_process['real_pay_amount'];
							$return_amount = 0;
							$principal_payment = $value_process['real_pay_amount'];
							foreach($row_loan_atm_detail as $key_atm => $value_atm){
								if($principal_payment > 0){
									if($principal_payment >= $value_atm['loan_amount_balance']){
										$data_insert = array();
										$data_insert['loan_amount_balance'] = 0;
										$data_insert['loan_status'] = '1';
										$data_insert['date_last_pay'] = $process_date;
										$this->db->where('loan_id', $value_atm['loan_id']);
										$this->db->update('coop_loan_atm_detail', $data_insert);
										$principal_payment = $principal_payment - $value_atm['loan_amount_balance'];
									}else{
										$data_insert = array();
										$data_insert['loan_amount_balance'] = $value_atm['loan_amount_balance']-$principal_payment;
										$data_insert['date_last_pay'] = $process_date;
										$this->db->where('loan_id', $value_atm['loan_id']);
										$this->db->update('coop_loan_atm_detail', $data_insert);
										$principal_payment = 0;
									}
								}
							}
							$total_amount_balance = $row_loan_atm['total_amount_balance']+$value_process['real_pay_amount'];
							if($total_amount_balance > $row_loan_atm['total_amount_approve']){
								$total_amount_balance = $row_loan_atm['total_amount_approve'];
							}
							$data_insert = array();
							$data_insert['total_amount_balance'] = $total_amount_balance;
							$this->db->where('loan_atm_id', $value_process['loan_atm_id']);
							$this->db->update('coop_loan_atm', $data_insert);

							$loan_amount_balance = $row_loan_atm['total_amount_approve'] - $total_amount_balance;
							$balance = $loan_amount_balance;

							$atm_transaction = array();
							$atm_transaction['loan_atm_id'] = $value_process['loan_atm_id'];
							$atm_transaction['loan_amount_balance'] = $loan_amount_balance;
							$atm_transaction['transaction_datetime'] = $process_date." ".date('H:i:s');
							$atm_transaction['receipt_id'] = @$receipt_number;
							$this->loan_libraries->atm_transaction($atm_transaction);

							$transaction_text = 'ต้นเงินกู้เลขที่สัญญา '.$row_loan_atm['contract_number'];
							if($principal_payment > 0){
								$data_insert = array();
								$data_insert['return_interest_amount'] = $return_amount;
								$data_insert['return_month'] = $month;
								$data_insert['return_year'] = $year;
								$data_insert['member_id'] = $value_process['member_id'];
								$data_insert['createdate'] = $process_date." ".date('H:i:s');
								$data_insert['loan_atm_id'] = $value_process['loan_atm_id'];
								$data_insert['pay_type'] = $value_process['pay_type'];
								$data_insert['finance_month_profile_id'] = @$profile_id;
								$data_insert['return_status'] = '0';
								$data_insert['return_profile_id'] = $return_profile_id;
								$this->db->insert('coop_return_interest', $data_insert);
								$total_return_amount += $return_amount;
							}
						}else{
							$date_interesting = $process_date;
							$cal_atm_interest = array();
							$cal_atm_interest['loan_atm_id'] = $value_process['loan_id'];
							$cal_atm_interest['date_interesting'] = $date_interesting;
							$real_interest = $this->loan_libraries->cal_atm_interest($cal_atm_interest);

							//หาดอกเบี้ยจากยอดคงเหลือในจำนวนวันที่เหลือถึงสิ้นเดือน
							$this->db->select('loan_amount_balance');
							$this->db->from('coop_loan_atm_transaction');
							$this->db->where("loan_atm_id = '".$row_loan['id']."' AND transaction_datetime < '".$date_interesting."'");
							$this->db->order_by('transaction_datetime DESC');
							$this->db->limit(1);
							$row_last_balance_before = $this->db->get()->result_array();
							$row_last_balance_before = $row_last_balance_before[0];
							//หาจำนวนเงินก่อนะที่จะจ่ายในเดือนนี้

							$this->db->select('real_pay_amount');
							$this->db->from('coop_finance_month_detail');
							$this->db->where("loan_atm_id = '".$value_process['loan_atm_id']."' AND profile_id = '".$profile_id."' AND pay_type = 'principal'");
							$row_this_principal = $this->db->get()->result_array();
							$row_this_principal = $row_this_principal[0];
							//หาเงินต้นที่จ่ายในเดือนนี้

							$balance_after_prncipal = $row_last_balance_before['loan_amount_balance'] - $row_this_principal['real_pay_amount'];

							$date_finish_month = date('Y-m-t',strtotime($date_interesting));

							$diff = date_diff(date_create($date_interesting),date_create($date_finish_month));
							$date_count = $diff->format("%a");

							$this->db->select('*');
							$this->db->from('coop_loan_atm_setting');
							$row_loan_atm_setting = $this->db->get()->result_array();
							$loan_atm_setting = @$row_loan_atm_setting[0];

							$interest_after_principal = ((($balance_after_prncipal*$loan_atm_setting['interest_rate'])/100)/365)*$date_count;
							$interest_after_principal = round($interest_after_principal);

							$real_interest += $interest_after_principal;
							//end หาดอกเบี้ยจากยอดคงเหลือในจำนวนวันที่เหลือถึงสิ้นเดือน

							$data_insert = array();
							$data_insert['date_last_interest'] = $date_finish_month;
							$this->db->where('id',$row_loan['id']);
							$this->db->update('coop_loan',$data_insert);

							$interest = $value_process['real_pay_amount'];
							$pay_amount = '';
							$balance = '';
							$transaction_text = 'ดอกเบี้ยเงินกู้เลขที่สัญญา '.$row_loan_atm['contract_number'];

							//คืนดอกเบี้ย
							if($value_process['real_pay_amount'] > $real_interest){
								$data_insert = array();
								$data_insert['return_interest_amount'] = ($value_process['real_pay_amount'] - $real_interest);
								$data_insert['return_month'] = $month;
								$data_insert['return_year'] = $year;
								$data_insert['member_id'] = $value_process['member_id'];
								$data_insert['createdate'] = $process_date." ".date('H:i:s');
								$data_insert['loan_atm_id'] = $value_process['loan_atm_id'];
								$data_insert['pay_type'] = $value_process['pay_type'];
								$data_insert['finance_month_profile_id'] = @$profile_id;
								$data_insert['return_status'] = '0';
								$data_insert['return_profile_id'] = $return_profile_id;
								$this->db->insert('coop_return_interest', $data_insert);
								$total_return_amount += ($value_process['real_pay_amount'] - $real_interest);
							}
						}
					}else{
						$pay_amount = $value_process['real_pay_amount'];
						$interest = '';
						$balance = '';
						$period_count = '';
						$transaction_text = $row_deduct['deduct_detail'];
					}

					$data_insert = array();
					$data_insert['receipt_id'] = $receipt_number;
					$data_insert['receipt_list'] = $row_deduct['account_list_id'];
					$data_insert['receipt_count'] = $pay_amount!=''?$pay_amount:$interest;
					$this->db->insert('coop_receipt_detail', $data_insert);

					//บันทึกการชำระเงิน
					$data_insert = array();
					$data_insert['receipt_id'] = $receipt_number;
					$data_insert['member_id'] = @$value_process['member_id'];
					$data_insert['loan_id'] = @$value_process['loan_id'];
					$data_insert['loan_atm_id'] = @$value_process['loan_atm_id'];
					$data_insert['account_list_id'] = $row_deduct['account_list_id'];
					$data_insert['principal_payment'] = @$pay_amount;
					$data_insert['interest'] = @$interest;
					$data_insert['total_amount'] = (@$pay_amount+@$interest);
					$data_insert['payment_date'] = $process_date;
					$data_insert['period_count'] = @$period_count;
					$data_insert['loan_amount_balance'] = $balance;
					$data_insert['createdatetime'] = $process_date." ".date('H:i:s');
					$data_insert['transaction_text'] = $transaction_text;
					$data_insert['deduct_type'] = 'all';
					$this->db->insert('coop_finance_transaction', $data_insert);
					$sum_count = $sum_count + @$pay_amount + @$interest;
				}
			}
			if($sum_count > 0){
				$data_insert = array();
				$data_insert['receipt_id'] = @$receipt_number;
				$data_insert['member_id'] = @$value['member_id'];
				$data_insert['admin_id'] = @$_SESSION['USER_ID'];
				$data_insert['sumcount'] = $sum_count;
				$data_insert['receipt_datetime'] = $process_date." ".date('H:i:s');
				$data_insert['month_receipt'] = $month;
				$data_insert['year_receipt'] = $year;
				$data_insert['finance_month_profile_id'] = @$profile_id;
				$data_insert['pay_type'] = @$_POST['pay_type'];
				$data_insert['order_by'] = @$order_by_id;
				$this->db->insert('coop_receipt', $data_insert);
			}
			if($total_return_amount > 0){
				$data_insert = array();
				$data_insert['total_return_amount'] = $total_return_amount;
				$this->db->where('return_profile_id',$return_profile_id);
				$this->db->update('coop_return_interest_profile',$data_insert);
			}else{
				$this->db->where('return_profile_id',$return_profile_id);
				$this->db->delete('coop_return_interest_profile');
			}
		}
		$this->center_function->toast('ประมวลผลเรียบร้อยแล้ว');
		echo "<script>document.location.href = '".base_url(PROJECTPATH.'/finance/finance_month_process'.$get_param)."'</script>";
	}

	function finance_month_run_process(){
		//echo"<pre>";print_r($_GET);print_r($_POST);exit;
		$process_date = $this->center_function->ConvertToSQLDate($_POST['process_date']); //exit;
		$month = @$_GET['month'];
		$year = @$_GET['year'];

		$get_param = '?';
		foreach(@$_GET as $key => $value){
			$get_param .= '&'.$key.'='.$value;
		}

		$where = "1=1 AND member_status = '1' ";

		/*if(@$_GET['department']!=''){
			$where .= " AND department = '".$_GET['department']."'";
		}
		if(@$_GET['faction']!=''){
			$where .= " AND faction = '".$_GET['faction']."'";
		}
		if(@$_GET['level']!=''){
			$where .= " AND level = '".$_GET['level']."'";
		}
		if(@$_GET['mem_type_id']!=''){
			$where .= " AND mem_type_id = '".$_GET['mem_type_id']."'";
		}
		if(@$_GET['member_id']!=''){
			$where .= " AND member_id = '".$_GET['member_id']."'";
		}*/
		if(!empty($_POST['member_id'])){
			$where .= " AND member_id IN(";
			foreach($_POST['member_id'] as $key => $value){
				$where .= "'".$value."',";
			}
			$where = substr($where,0,-1);
			$where .= ")";
		}else{
			$this->center_function->toastDanger('ไม่สามารถประมวลผลได้');
			echo "<script>document.location.href = '".base_url(PROJECTPATH.'/finance/finance_month_process'.$get_param)."'</script>";
			exit;
		}


		$this->db->select('profile_id');
		$this->db->from('coop_finance_month_profile');
		$this->db->where("profile_month = '".(int)$month."' AND profile_year = '".$year."' ");
		$row = $this->db->get()->result_array();
		$row_profile = @$row[0];
		$profile_id = $row_profile['profile_id'];

		$this->db->select('setting_value');
		$this->db->from('coop_share_setting');
		$this->db->where("setting_id = '1'");
		$row = $this->db->get()->result_array();
		$row_share_value = $row[0];
		$share_value = $row_share_value['setting_value'];

		$this->db->select(array('coop_mem_apply.member_id'));
		$this->db->from('coop_mem_apply');
		$this->db->where($where);
		$this->db->order_by('member_id ASC');
		$row_member = $this->db->get()->result_array();
		foreach($row_member as $key => $value){
			$this->db->select('*');
			$this->db->from('coop_non_pay');
			$this->db->where("member_id = '".@$value['member_id']."' AND non_pay_month = '".(int)$month."' AND non_pay_year = '".$year."' AND non_pay_status = '0'");
			$row_non_pay = $this->db->get()->result_array();
			$row_non_pay = @$row_non_pay[0];
			$text = '';
			if(!empty($row_non_pay)){
				$non_pay_balance = $row_non_pay['non_pay_amount'];

				$this->db->select(array('*'));
				$this->db->from('coop_finance_month_detail');
				$this->db->where("profile_id = '".@$profile_id."' AND member_id = '".$value['member_id']."' AND run_status = '0'");
				$this->db->order_by('run_id DESC');
				$row_detail = $this->db->get()->result_array();
				foreach($row_detail as $key_detail => $value_detail){
					$pay_amount = $value_detail['pay_amount'];
					$real_pay_amount = $pay_amount;
					if($pay_amount > $non_pay_balance){
						$real_pay_amount = $pay_amount - $non_pay_balance;
						$non_pay_amount = $non_pay_balance;
						$non_pay_balance = 0;
					}else{
						$non_pay_balance = $non_pay_balance - $pay_amount;
						$non_pay_amount = $pay_amount;
						$real_pay_amount = 0;
					}
					$data_insert = array();
					$data_insert['deduct_code'] = $value_detail['deduct_code'];
					$data_insert['non_pay_amount'] = $non_pay_amount;
					$data_insert['non_pay_amount_balance'] = $non_pay_amount;
					$data_insert['loan_id'] = $value_detail['loan_id'];
					$data_insert['loan_atm_id'] = $value_detail['loan_atm_id'];
					$data_insert['pay_type'] = $value_detail['pay_type'];
					$data_insert['finance_month_profile_id'] = $value_detail['profile_id'];
					$data_insert['finance_month_detail_id'] = $value_detail['run_id'];
					$data_insert['member_id'] = $value_detail['member_id'];
					$data_insert['non_pay_id'] = $row_non_pay['non_pay_id'];
					$data_insert['cremation_type_id'] = $value_detail['cremation_type_id'];
					$data_insert['deposit_account_id'] = $value_detail['deposit_account_id'];
					$this->db->insert('coop_non_pay_detail',$data_insert);

					$data_insert = array();
					$data_insert['real_pay_amount'] = $real_pay_amount;
					$this->db->where('run_id',$value_detail['run_id']);
					$this->db->update('coop_finance_month_detail',$data_insert);
					if($non_pay_balance == 0){
						break;
					}
				}

				$data_insert = array();
				$data_insert['non_pay_status'] = '1';
				$this->db->where('non_pay_id',$row_non_pay['non_pay_id']);
				$this->db->update('coop_non_pay',$data_insert);
				$text = 'F';
			}

			$yymm = date("m");
			$yy = (date("Y")+543);
			$yy_full = (date("Y")+543);
			$yy = substr($yy,2);

			if(empty($text)) {
				$text = 'B';
			}

			$this->db->select(array('*'));
			$this->db->from('coop_receipt');
			$this->db->where("receipt_id LIKE '".$yymm.'B'.$yy."%' OR receipt_id LIKE '".$yymm.'Y'.$yy."%'");
			$this->db->order_by("order_by DESC");
			$this->db->limit(1);

			$row_receipt = $this->db->get()->result_array();
			$row_receipt = @$row_receipt[0];

			if($row_receipt['receipt_id'] != '') {
				$id = (int) substr($row_receipt["receipt_id"], 6);
				$receipt_number = $yymm.''.$text.''.$yy.sprintf("%06d", $id + 1);

			}else {
				$receipt_number = $yymm.''.$text.''.$yy."000001";
			}

			$check_receipt_id = $this->db->get_where("coop_receipt", array("receipt_id" => $receipt_number) )->result();
			$count_order_by = $row_receipt["order_by"];
			while ($check_receipt_id) {
				$count_order_by++;
				$id = (int) substr($receipt_number, 6);
				$receipt_number = $yymm.''.$text.''.$yy.sprintf("%06d", $id + 1);
				$check_receipt_id = $this->db->get_where("coop_receipt", array("receipt_id" => $receipt_number) )->result();
			}

			$order_by_id =  $count_order_by+1 ;

			// exit;
			$sum_count = 0;

			//Prepare return
			//For Monthly Process
			$data_insert = array();
			$data_insert['member_id'] = $value['member_id'];
			$data_insert['finance_month_profile_id'] = @$profile_id;
			$data_insert['createdatetime'] = $process_date." ".date('H:i:s');
			$data_insert['return_profile_status'] = '0';
			$data_insert['return_from'] = 'monthly';
			$data_insert['admin_id'] = $_SESSION['USER_ID'];
			$this->db->insert('coop_return_interest_profile', $data_insert);
			$return_profile_id = $this->db->insert_id();
			$total_return_amount = 0;

			//For Occasional
			$data_insert = array();
			$data_insert['member_id'] = $value['member_id'];
			$data_insert['finance_month_profile_id'] = @$profile_id;
			$data_insert['createdatetime'] = $process_date." ".date('H:i:s');
			$data_insert['return_profile_status'] = '0';
			$data_insert['return_from'] = 'occasional';
			$data_insert['admin_id'] = $_SESSION['USER_ID'];
			$this->db->insert('coop_return_interest_profile', $data_insert);
			$return_occasional_profile_id = $this->db->insert_id();
			$total_occasional_return_amount = 0;

			//Prepare Arry for keep loan with occasional payment
			$occasional_loans = array();
			$occasional_loan_atms = array();

			$this->db->select(array('*'));
			$this->db->from('coop_finance_month_detail');
			$this->db->where("profile_id = '".@$profile_id."' AND member_id = '".$value['member_id']."' AND run_status = '0'");
			$this->db->order_by('run_id ASC');
			$row_process = $this->db->get()->result_array();
			foreach($row_process as $key_process => $value_process){
				$data_insert = array();
				$data_insert['run_status'] = '1';
				$this->db->where('run_id', $value_process['run_id']);
				$this->db->update('coop_finance_month_detail', $data_insert);
				//echo $this->db->last_query()."<br>";
				if($value_process['real_pay_amount']>0){

					$this->db->select(array('*'));
					$this->db->from('coop_deduct');
					$this->db->where("deduct_id = '".$value_process['deduct_id']."'");
					$this->db->limit(1);
					$row_deduct = $this->db->get()->result_array();
					$row_deduct = @$row_deduct[0];

					if($value_process['deduct_code'] == 'LOAN' || $value_process['deduct_code'] == 'GUARANTEE'){
							$this->db->select(
								array(
									'coop_loan.id',
									'coop_loan.loan_type',
									'coop_loan.contract_number',
									'coop_loan.loan_amount_balance',
									'coop_loan.interest_per_year',
									'coop_loan.period_now',
									'coop_loan_transfer.date_transfer',
									'coop_loan_name.loan_name',
									'coop_loan.createdatetime'
								)
							);
							$this->db->from('coop_loan');
							$this->db->join('coop_loan_transfer', 'coop_loan_transfer.loan_id = coop_loan.id', 'left');
							$this->db->join('coop_loan_name', 'coop_loan_name.loan_name_id = coop_loan.loan_type', 'inner');
							$this->db->where("
								coop_loan.id = '".$value_process['loan_id']."'
							");
							$row_loan = $this->db->get()->result_array();
							$row_loan = $row_loan[0];

							if($row_loan['period_now']!=''){
								$period_count = $row_loan['period_now']+1;
							}else{
								$period_count = 1;
							}

						if($value_process['pay_type'] == 'principal'){
							$interest = '';
							$return_amount = 0;
							if($value_process['real_pay_amount'] > $row_loan['loan_amount_balance']){
								$return_amount = $value_process['real_pay_amount'] - $row_loan['loan_amount_balance'];
								$pay_amount = $value_process['real_pay_amount'];
								$balance = 0;
							}else{
								$pay_amount = $value_process['real_pay_amount'];
								$balance = $row_loan['loan_amount_balance'] - $pay_amount;
							}

							$transaction_text = 'ต้นเงินกู้เลขที่สัญญา '.$row_loan['contract_number'];

							if($balance > 0){
								$data_insert = array();
								$data_insert['loan_amount_balance'] = $balance;
								$data_insert['period_now'] = $period_count;
								//$data_insert['loan_status'] = '1';
								$this->db->where('id', $value_process['loan_id']);
								$this->db->update('coop_loan', $data_insert);
							}else{
								$data_insert = array();
								$data_insert['loan_amount_balance'] = $balance;
								$data_insert['period_now'] = $period_count;
								$data_insert['loan_status'] = '4';
								$this->db->where('id', $value_process['loan_id']);
								$this->db->update('coop_loan', $data_insert);
							}
							$loan_transaction = array();
							$loan_transaction['loan_id'] = $value_process['loan_id'];
							$loan_transaction['loan_amount_balance'] = $balance;
							$loan_transaction['transaction_datetime'] = date('Y-m-d H:i:s');
							$loan_transaction['receipt_id'] = $receipt_number;
							$this->loan_libraries->loan_transaction($loan_transaction);

							// if($return_amount > 0){
							// 	$data_insert = array();
							// 	$data_insert['return_interest_amount'] = $return_amount;
							// 	$data_insert['return_month'] = $month;
							// 	$data_insert['return_year'] = $year;
							// 	$data_insert['member_id'] = $value_process['member_id'];
							// 	$data_insert['createdate'] = $process_date." ".date('H:i:s');
							// 	$data_insert['loan_id'] = $value_process['loan_id'];
							// 	$data_insert['pay_type'] = $value_process['pay_type'];
							// 	$data_insert['finance_month_profile_id'] = $profile_id;
							// 	$data_insert['return_status'] = '0';
							// 	$data_insert['return_profile_id'] = $return_occasional_profile_id;
							// 	$this->db->insert('coop_return_interest', $data_insert);
							// 	$total_occasional_return_amount += $return_amount;
							// 	$occasional_loans[] = $value_process['loan_id'];
							// }
						}else{
							$date_interesting = $process_date;
							$cal_loan_interest = array();
							$cal_loan_interest['loan_id'] = $row_loan['id'];
							$cal_loan_interest['date_interesting'] = $date_interesting;
							$real_interest = $this->loan_libraries->cal_loan_interest($cal_loan_interest);

							//หาดอกเบี้ยจากยอดคงเหลือในจำนวนวันที่เหลือถึงสิ้นเดือน
							$this->db->select('loan_amount_balance');
							$this->db->from('coop_loan_transaction');
							$this->db->where("loan_id = '".$row_loan['id']."' AND transaction_datetime < '".$date_interesting."'");
							$this->db->order_by('transaction_datetime DESC');
							$this->db->limit(1);
							$row_last_balance_before = $this->db->get()->result_array();
							$row_last_balance_before = $row_last_balance_before[0];
							//หาจำนวนเงินก่อนะที่จะจ่ายในเดือนนี้

							$this->db->select('real_pay_amount');
							$this->db->from('coop_finance_month_detail');
							$this->db->where("loan_id = '".$row_loan['id']."' AND profile_id = '".$profile_id."' AND pay_type = 'principal'");
							$row_this_principal = $this->db->get()->result_array();
							$row_this_principal = $row_this_principal[0];
							//หาเงินต้นที่จ่ายในเดือนนี้

							$balance_after_prncipal = $row_last_balance_before['loan_amount_balance'] - $row_this_principal['real_pay_amount'];

							$date_finish_month = date('Y-m-t',strtotime($date_interesting));


							$diff = date_diff(date_create($date_interesting),date_create($date_finish_month));
							$date_count = $diff->format("%a");

							$interest_after_principal = ((($balance_after_prncipal*$row_loan['interest_per_year'])/100)/365)*$date_count;
							$interest_after_principal = round($interest_after_principal);

							$interest_before_principal = ((($row_last_balance_before['loan_amount_balance']*$row_loan['interest_per_year'])/100)/365)*$date_count;
							$interest_before_principal = round($interest_before_principal);

							$real_interest += $interest_after_principal;
							//end หาดอกเบี้ยจากยอดคงเหลือในจำนวนวันที่เหลือถึงสิ้นเดือน

							$data_insert = array();
							$data_insert['date_last_interest'] = $date_finish_month;
							$this->db->where('id',$row_loan['id']);
							$this->db->update('coop_loan',$data_insert);

							$interest = $value_process['real_pay_amount'];
							$pay_amount = '';
							$balance = '';
							$transaction_text = 'ดอกเบี้ยเงินกู้เลขที่สัญญา '.$row_loan['contract_number'];

							//คืนดอกเบี้ย
							// if($interest_before_principal > $interest_after_principal){
							// 	$data_insert = array();
							// 	$data_insert['return_interest_amount'] = ($interest_before_principal - $interest_after_principal);
							// 	$data_insert['return_month'] = $month;
							// 	$data_insert['return_year'] = $year;
							// 	$data_insert['member_id'] = $value_process['member_id'];
							// 	$data_insert['createdate'] = $process_date." ".date('H:i:s');
							// 	$data_insert['loan_id'] = $value_process['loan_id'];
							// 	$data_insert['pay_type'] = $value_process['pay_type'];
							// 	$data_insert['finance_month_profile_id'] = @$profile_id;
							// 	$data_insert['return_status'] = '0';
							// 	$data_insert['return_profile_id'] = $return_profile_id;
							// 	$this->db->insert('coop_return_interest', $data_insert);
							// 	$total_return_amount += ($interest_before_principal - $interest_after_principal);
							// }
						}
					}else if($value_process['deduct_code'] == 'SHARE'){
						$this->db->select(array('*'));
						$this->db->from('coop_mem_share');
						$this->db->where("member_id = '".$value_process['member_id']."' AND share_status = '1'");
						$this->db->order_by("share_date DESC");
						$this->db->limit(1);
						$row_share = $this->db->get()->result_array();
						$row_share = @$row_share[0];

						$pay_amount = $value_process['real_pay_amount'];
						$interest = '';
						$balance = $row_share['share_collect_value'] + $value_process['real_pay_amount'];
						$period_count = $row_share['share_period']+1;
						$transaction_text = 'ชำระเงินค่าหุ้นรายเดือน';

						$data_insert = array();
						$data_insert['member_id'] = @$value_process['member_id'];
						$data_insert['admin_id'] = @$_SESSION['USER_ID'];
						$data_insert['share_type'] = 'SPM';
						$data_insert['share_date'] = $process_date." ".date('H:i:s');
						$data_insert['share_payable'] = @$row_share['share_collect'];
						$data_insert['share_payable_value'] = @$row_share['share_collect_value'];
						$data_insert['share_early'] = ($pay_amount/$share_value);
						$data_insert['share_early_value'] = $pay_amount;
						$data_insert['share_collect'] = ($balance/$share_value);
						$data_insert['share_collect_value'] = $balance;
						$data_insert['share_value'] = $share_value;
						$data_insert['share_status'] = '1';
						$data_insert['share_bill'] = $receipt_number;
						$data_insert['share_bill_date'] = $process_date." ".date('H:i:s');
						$data_insert['share_period'] = $period_count;
						$this->db->insert('coop_mem_share', $data_insert);

					}else if($value_process['deduct_code'] == 'CREMATION'){
						$this->db->select(array('*'));
						$this->db->from('coop_cremation_data');
						$this->db->where("cremation_id = '".$value_process['cremation_type_id']."'");
						$this->db->limit(1);
						$row_cremation = $this->db->get()->result_array();
						$row_cremation = @$row_cremation[0];

						$pay_amount = $value_process['real_pay_amount'];
						$interest = '';
						$balance = '';
						$period_count = '';
						$transaction_text = $row_deduct['deduct_detail']." ".$row_cremation['cremation_name_short'];
					}else if($value_process['deduct_code'] == 'DEPOSIT'){
						$DEPOSIT = $value_process['real_pay_amount'];

						$this->db->select('*');
						$this->db->from('coop_maco_account');
						$this->db->where("account_id = '".$value_process['deposit_account_id']."'");
						$this->db->limit(1);
						$row_account = $this->db->get()->result_array();
						$row_account = @$row_account[0];

						$this->db->select('*');
						$this->db->from('coop_account_transaction');
						$this->db->where("account_id = '".$value_process['deposit_account_id']."'");
						$this->db->order_by('transaction_time DESC');
						$this->db->limit(1);
						$row_transaction = $this->db->get()->result_array();
						if(!empty($row_transaction)){
							$balance = @$row_transaction[0]['transaction_balance'];
							$balance_no_in = @$row_transaction[0]['transaction_no_in_balance'];
						}else{
							$balance = 0;
							$balance_no_in = 0;
						}
						$sum = $balance + $DEPOSIT;
						$sum_no_in = $balance_no_in + $DEPOSIT;

						$data_insert = array();
						$data_insert['transaction_time'] = $process_date." ".date('H:i:s');
						$data_insert['transaction_list'] = 'XD';
						$data_insert['transaction_withdrawal'] = '';
						$data_insert['transaction_deposit'] = $DEPOSIT;
						$data_insert['transaction_balance'] = $sum;
						$data_insert['transaction_no_in_balance'] = $sum_no_in;
						$data_insert['user_id'] = $_SESSION['USER_ID'];
						$data_insert['account_id'] = $value_process['deposit_account_id'];
						$data_insert['receipt_id'] = @$receipt_number;
						$this->db->insert('coop_account_transaction', $data_insert);

						$account_period = $row_account['account_period']!=''?($row_account['account_period']+1):1;

						$data_insert = array();
						$data_insert['account_period'] = $account_period;
						$this->db->where('account_id', $value_process['deposit_account_id']);
						$this->db->update('coop_maco_account', $data_insert);

						$pay_amount = $value_process['real_pay_amount'];
						$interest = '';
						$balance = $sum;
						$period_count = $account_period;
						$transaction_text = $row_deduct['deduct_detail']." เลขที่บัญชี".$value_process['deposit_account_id'];
					}else if($value_process['deduct_code'] == 'ATM'){
						$this->db->select(
							array(
								't1.loan_atm_id',
								't1.total_amount_approve',
								't1.total_amount_balance',
								't1.contract_number'
							)
						);
						$this->db->from('coop_loan_atm as t1');
						$this->db->where("
							t1.loan_atm_id = '".$value_process['loan_atm_id']."'
						");
						$row_loan_atm = $this->db->get()->result_array();
						$row_loan_atm = $row_loan_atm[0];
						$this->db->select(
							array(
								't1.loan_id',
								't1.loan_atm_id',
								't1.loan_amount_balance'
							)
						);
						$this->db->from('coop_loan_atm_detail as t1');
						$this->db->where("
							t1.loan_atm_id = '".$value_process['loan_atm_id']."'
							AND t1.loan_status = '0'
						");
						$this->db->order_by('loan_id ASC');
						$row_loan_atm_detail = $this->db->get()->result_array();

						if($value_process['pay_type'] == 'principal'){
							$interest = '';
							$pay_amount = $value_process['real_pay_amount'];
							$return_amount = 0;
							$principal_payment = $value_process['real_pay_amount'];
							foreach($row_loan_atm_detail as $key_atm => $value_atm){
								if($principal_payment > 0){
									if($principal_payment >= $value_atm['loan_amount_balance']){
										$data_insert = array();
										$data_insert['loan_amount_balance'] = 0;
										$data_insert['loan_status'] = '1';
										$data_insert['date_last_pay'] = $process_date;
										$this->db->where('loan_id', $value_atm['loan_id']);
										$this->db->update('coop_loan_atm_detail', $data_insert);
										$principal_payment = $principal_payment - $value_atm['loan_amount_balance'];
									}else{
										$data_insert = array();
										$data_insert['loan_amount_balance'] = $value_atm['loan_amount_balance']-$principal_payment;
										$data_insert['date_last_pay'] = $process_date;
										$this->db->where('loan_id', $value_atm['loan_id']);
										$this->db->update('coop_loan_atm_detail', $data_insert);
										$principal_payment = 0;
									}
								}
							}
							$total_amount_balance = $row_loan_atm['total_amount_balance'] + $value_process['real_pay_amount'];
							if($total_amount_balance > $row_loan_atm['total_amount_approve']){
								$total_amount_balance = $row_loan_atm['total_amount_approve'];
							}
							$data_insert = array();
							$data_insert['total_amount_balance'] = $total_amount_balance;
							$this->db->where('loan_atm_id', $value_process['loan_atm_id']);
							$this->db->update('coop_loan_atm', $data_insert);

							$loan_amount_balance = $row_loan_atm['total_amount_approve'] - $total_amount_balance;
							$balance = $loan_amount_balance;

							$atm_transaction = array();
							$atm_transaction['loan_atm_id'] = $value_process['loan_atm_id'];
							$atm_transaction['loan_amount_balance'] = $loan_amount_balance;
							$atm_transaction['transaction_datetime'] = $process_date." ".date('H:i:s');
							$atm_transaction['receipt_id'] = @$receipt_number;
							$this->loan_libraries->atm_transaction($atm_transaction);

							$transaction_text = 'ต้นเงินกู้เลขที่สัญญา '.$row_loan_atm['contract_number'];

							// if($principal_payment > 0){
							// 	$data_insert = array();
							// 	$data_insert['return_interest_amount'] = $principal_payment;
							// 	$data_insert['return_month'] = $month;
							// 	$data_insert['return_year'] = $year;
							// 	$data_insert['member_id'] = $value_process['member_id'];
							// 	$data_insert['createdate'] = $process_date." ".date('H:i:s');
							// 	$data_insert['loan_atm_id'] = $value_process['loan_atm_id'];
							// 	$data_insert['pay_type'] = $value_process['pay_type'];
							// 	$data_insert['finance_month_profile_id'] = $profile_id;
							// 	$data_insert['return_status'] = '0';
							// 	$data_insert['return_profile_id'] = $return_occasional_profile_id;
							// 	$this->db->insert('coop_return_interest', $data_insert);

							// 	$total_occasional_return_amount += $principal_payment;
							// 	$occasional_loan_atms[] = $value_process['loan_atm_id'];
							// }
						}else{
							$date_interesting = $process_date;
							$cal_atm_interest = array();
							$cal_atm_interest['loan_atm_id'] = $value_process['loan_id'];
							$cal_atm_interest['date_interesting'] = $date_interesting;
							$real_interest = $this->loan_libraries->cal_atm_interest($cal_atm_interest);

							//หาดอกเบี้ยจากยอดคงเหลือในจำนวนวันที่เหลือถึงสิ้นเดือน
							$this->db->select('loan_amount_balance');
							$this->db->from('coop_loan_atm_transaction');
							$this->db->where("loan_atm_id = '".$row_loan['id']."' AND transaction_datetime < '".$date_interesting."'");
							$this->db->order_by('transaction_datetime DESC');
							$this->db->limit(1);
							$row_last_balance_before = $this->db->get()->result_array();
							$row_last_balance_before = $row_last_balance_before[0];
							//หาจำนวนเงินก่อนะที่จะจ่ายในเดือนนี้

							$this->db->select('real_pay_amount');
							$this->db->from('coop_finance_month_detail');
							$this->db->where("loan_atm_id = '".$value_process['loan_atm_id']."' AND profile_id = '".$profile_id."' AND pay_type = 'principal'");
							$row_this_principal = $this->db->get()->result_array();
							$row_this_principal = $row_this_principal[0];
							//หาเงินต้นที่จ่ายในเดือนนี้

							$balance_after_prncipal = $row_last_balance_before['loan_amount_balance'] - $row_this_principal['real_pay_amount'];

							$date_finish_month = date('Y-m-t',strtotime($date_interesting));

							$diff = date_diff(date_create($date_interesting),date_create($date_finish_month));
							$date_count = $diff->format("%a");

							$this->db->select('*');
							$this->db->from('coop_loan_atm_setting');
							$row_loan_atm_setting = $this->db->get()->result_array();
							$loan_atm_setting = @$row_loan_atm_setting[0];

							$interest_after_principal = ((($balance_after_prncipal*$loan_atm_setting['interest_rate'])/100)/365)*$date_count;
							$interest_after_principal = round($interest_after_principal);

							$interest_before_principal = ((($row_last_balance_before['loan_amount_balance']*$loan_atm_setting['interest_rate'])/100)/365)*$date_count;
							$interest_before_principal = round($interest_before_principal);

							$real_interest += $interest_after_principal;
							//end หาดอกเบี้ยจากยอดคงเหลือในจำนวนวันที่เหลือถึงสิ้นเดือน

							$data_insert = array();
							$data_insert['date_last_interest'] = $date_finish_month;
							$this->db->where('id',$row_loan['id']);
							$this->db->update('coop_loan',$data_insert);

							$interest = $value_process['real_pay_amount'];
							$pay_amount = '';
							$balance = '';
							$transaction_text = 'ดอกเบี้ยเงินกู้เลขที่สัญญา '.$row_loan_atm['contract_number'];

							//คืนดอกเบี้ย
							// if($interest_before_principal > $interest_after_principal){
							// 	$data_insert = array();
							// 	$data_insert['return_interest_amount'] = ($interest_before_principal - $interest_after_principal);
							// 	$data_insert['return_month'] = $month;
							// 	$data_insert['return_year'] = $year;
							// 	$data_insert['member_id'] = $value_process['member_id'];
							// 	$data_insert['createdate'] = $process_date." ".date('H:i:s');
							// 	$data_insert['loan_atm_id'] = $value_process['loan_atm_id'];
							// 	$data_insert['pay_type'] = $value_process['pay_type'];
							// 	$data_insert['finance_month_profile_id'] = @$profile_id;
							// 	$data_insert['return_status'] = '0';
							// 	$data_insert['return_profile_id'] = $return_profile_id;
							// 	$this->db->insert('coop_return_interest', $data_insert);
							// 	$total_return_amount += ($interest_before_principal - $interest_after_principal);
							// }
						}
					}else{
						$pay_amount = $value_process['real_pay_amount'];
						$interest = '';
						$balance = '';
						$period_count = '';
						$transaction_text = $row_deduct['deduct_detail'];
					}

					$data_insert = array();
					$data_insert['receipt_id'] = $receipt_number;
					$data_insert['receipt_list'] = $row_deduct['account_list_id'];
					$data_insert['receipt_count'] = $pay_amount!=''?$pay_amount:$interest;
					$this->db->insert('coop_receipt_detail', $data_insert);

					//บันทึกการชำระเงิน
					if(empty($interest)) $interest = 0;
					if(empty($pay_amount)) $pay_amount = 0;
					$data_insert = array();
					$data_insert['receipt_id'] = $receipt_number;
					$data_insert['member_id'] = @$value_process['member_id'];
					$data_insert['loan_id'] = @$value_process['loan_id'];
					$data_insert['loan_atm_id'] = @$value_process['loan_atm_id'];
					$data_insert['account_list_id'] = $row_deduct['account_list_id'];
					$data_insert['principal_payment'] = @$pay_amount;
					$data_insert['interest'] = @$interest;
					$data_insert['total_amount'] = ($pay_amount+$interest);
					$data_insert['payment_date'] = $process_date;
					$data_insert['period_count'] = @$period_count;
					$data_insert['loan_amount_balance'] = $balance;
					$data_insert['createdatetime'] = $process_date." ".date('H:i:s');
					$data_insert['transaction_text'] = $transaction_text;
					$data_insert['deduct_type'] = 'all';
					$this->db->insert('coop_finance_transaction', $data_insert);
					$sum_count = $sum_count + $pay_amount + $interest;
				}
			}
			if($sum_count > 0){
				$data_insert = array();
				$data_insert['receipt_id'] = @$receipt_number;
				$data_insert['member_id'] = @$value['member_id'];
				$data_insert['admin_id'] = @$_SESSION['USER_ID'];
				$data_insert['sumcount'] = $sum_count;
				$data_insert['receipt_datetime'] = $process_date." ".date('H:i:s');
				$data_insert['month_receipt'] = $month;
				$data_insert['year_receipt'] = $year;
				$data_insert['finance_month_profile_id'] = @$profile_id;
				$data_insert['pay_type'] = @$_POST['pay_type'];
				$data_insert['order_by'] = @$order_by_id;
				$this->db->insert('coop_receipt', $data_insert);
			}

			if($total_occasional_return_amount > 0) {
				$returnWhere = '1=1 ';
				if(!empty($occasional_loans) && !empty($occasional_loan_atms)) {
					$returnWhere .= "AND (loan_id IN (".implode(',',$occasional_loans).") OR loan_atm_id in IN (".implode(',',$occasional_loan_atms)."))";
				} else if (!empty($occasional_loans)) {
					$returnWhere .= "AND loan_id IN (".implode(',',$occasional_loans).")";
				} else if (!empty($occasional_loan_atms)) {
					$returnWhere .= "AND loan_atm_id IN (".implode(',',$occasional_loan_atms).")";
				}
				$occ_interests = $this->db->from("coop_return_interest")
											->where($returnWhere." AND return_profile_id = '".$return_profile_id."' AND pay_type = 'interest'")
											->get()->result_array();
				foreach($occ_interests as $occ_interest) {
					$total_return_amount -= $occ_interest["return_interest_amount"];
					$total_occasional_return_amount += $occ_interest["return_interest_amount"];

					$data_insert = array();
					$data_insert['return_profile_id'] = $return_occasional_profile_id;
					$this->db->where('run_id',$occ_interest['run_id'])
								->update('coop_return_interest',$data_insert);
				}
				$data_insert = array();
				$data_insert['total_return_amount'] = $total_occasional_return_amount;
				$this->db->where('return_profile_id',$return_occasional_profile_id)
						->update('coop_return_interest_profile',$data_insert);
			}else{
				$this->db->where('return_profile_id',$return_occasional_profile_id)
							->delete('coop_return_interest_profile');
			}
			if($total_return_amount > 0){
				$data_insert = array();
				$data_insert['total_return_amount'] = $total_return_amount;
				$this->db->where('return_profile_id',$return_profile_id);
				$this->db->update('coop_return_interest_profile',$data_insert);
			}else{
				$this->db->where('return_profile_id',$return_profile_id);
				$this->db->delete('coop_return_interest_profile');
			}
		}
		$this->center_function->toast('ประมวลผลเรียบร้อยแล้ว');

		echo "<script>document.location.href = '".base_url(PROJECTPATH.'/finance/finance_month_process'.$get_param)."'</script>";
	}

	function finance_month_cancel_process(){
		$this->db->select('t1.*');
		$this->db->from('coop_finance_month_profile as t1');
		$this->db->join('coop_finance_month_detail as t2','t1.profile_id = t2.profile_id','inner');
		$this->db->where("t2.run_status = '1'");
		$this->db->order_by('profile_id DESC');
		$this->db->limit(1);
		$row = $this->db->get()->result_array();
		$last_profile = @$row[0];

		$this->db->select('*');
		$this->db->from('coop_finance_month_detail');
		$this->db->where("profile_id = '".$last_profile['profile_id']."'");
		$row = $this->db->get()->result_array();

		foreach($row as $key => $value){
			$data_insert = array();
			$data_insert['real_pay_amount'] = $value['pay_amount'];
			$data_insert['run_status'] = '0';
			$this->db->where('run_id',$value['run_id']);
			$this->db->update('coop_finance_month_detail',$data_insert);
		}

		$data_insert = array();
		$data_insert['non_pay_status'] = '0';
		$this->db->where('non_pay_month',$last_profile['profile_month']);
		$this->db->where('non_pay_year',$last_profile['profile_year']);
		$this->db->update('coop_non_pay',$data_insert);

		$this->db->where('finance_month_profile_id',$last_profile['profile_id']);
		$this->db->delete('coop_non_pay_detail');

		$this->db->where('finance_month_profile_id',$last_profile['profile_id']);
		$this->db->delete('coop_return_interest');

		$this->db->where('finance_month_profile_id',$last_profile['profile_id']);
		$this->db->delete('coop_return_interest_profile');

		$this->db->select('*');
		$this->db->from('coop_receipt');
		$this->db->where("finance_month_profile_id = '".$last_profile['profile_id']."'");
		$row = $this->db->get()->result_array();

		foreach($row as $key => $value){
			$this->db->where('receipt_id',$value['receipt_id']);
			$this->db->delete('coop_receipt_detail');

			$this->db->where('share_bill',$value['receipt_id']);
			$this->db->delete('coop_mem_share');

			$this->db->where('receipt_id',$value['receipt_id']);
			$this->db->delete('coop_account_transaction');

			$this->db->select('*');
			$this->db->from('coop_finance_transaction');
			$this->db->where("receipt_id = '".$value['receipt_id']."'");
			$row_finance_transaction = $this->db->get()->result_array();
			foreach($row_finance_transaction as $key2 => $value2){
				if($value2['loan_id'] != '' && $value2['principal_payment'] != ''){
					$this->db->select('*');
					$this->db->from('coop_loan');
					$this->db->where("id = '".$value2['loan_id']."'");
					$row_loan = $this->db->get()->result_array();
					$row_loan = @$row_loan[0];
					$loan_amount_balance = $row_loan['loan_amount_balance'] + $value2['principal_payment'];

					$data_insert = array();
					$data_insert['loan_status'] = '1';
					$data_insert['loan_amount_balance'] = $loan_amount_balance;
					$this->db->where('id',$value2['loan_id']);
					$this->db->update('coop_loan',$data_insert);
				}else if($value2['loan_atm_id'] != '' && $value2['principal_payment'] != ''){
					$this->db->select('*');
					$this->db->from('coop_loan_atm');
					$this->db->where("loan_atm_id = '".$value2['loan_atm_id']."'");
					$row_loan = $this->db->get()->result_array();
					$row_loan = @$row_loan[0];
					$total_amount_balance = $row_loan['total_amount_balance'] - $value2['principal_payment'];

					$data_insert = array();
					$data_insert['total_amount_balance'] = $total_amount_balance;
					$this->db->where('loan_atm_id',$value2['loan_atm_id']);
					$this->db->update('coop_loan_atm',$data_insert);

					$this->db->select('*');
					$this->db->from('coop_loan_atm_detail');
					$this->db->where("loan_atm_id = '".$value2['loan_atm_id']."'");
					$row_loan_detail = $this->db->get()->result_array();
					foreach($row_loan_detail as $key3 => $value3){
						$data_insert = array();
						$data_insert['loan_status'] = '0';
						$data_insert['loan_amount_balance'] = $value3['loan_amount'];// + $value2['principal_payment'];
						$this->db->where('loan_id',$value3['loan_id']);
						$this->db->update('coop_loan_atm_detail',$data_insert);
					}

				}
			}
			$this->db->where('receipt_id',$value['receipt_id']);
			$this->db->delete('coop_loan_atm_transaction');

			$this->db->where('receipt_id',$value['receipt_id']);
			$this->db->delete('coop_loan_transaction');

			$this->db->where('receipt_id',$value['receipt_id']);
			$this->db->delete('coop_finance_transaction');

			$this->db->where('receipt_id',$value['receipt_id']);
			$this->db->delete('coop_receipt');

			$this->db->where('receipt_id',$value['receipt_id']);
			$this->db->delete('coop_non_pay_receipt');
		}

		$this->center_function->toast('ยกเลิกรายการเรียบร้อยแล้ว');
		echo "<script>document.location.href = '".base_url(PROJECTPATH.'/finance/finance_month_process?year='.$last_profile['profile_year'].'&month='.$last_profile['profile_month'])."'</script>";
	}

	function finance_return_old(){
		$arr_data = array();
		$month_arr = array('1'=>'มกราคม','2'=>'กุมภาพันธ์','3'=>'มีนาคม','4'=>'เมษายน','5'=>'พฤษภาคม','6'=>'มิถุนายน','7'=>'กรกฎาคม','8'=>'สิงหาคม','9'=>'กันยายน','10'=>'ตุลาคม','11'=>'พฤศจิกายน','12'=>'ธันวาคม');
		$arr_data['month_arr'] = $month_arr;

		$month = @$_GET['month']!=''?$_GET['month']:(int)date('m');
		$year = @$_GET['year']!=''?$_GET['year']:(date('Y')+543);
		$arr_data['month'] = $month;
		$arr_data['year'] = $year;

		$this->db->select('profile_id');
		$this->db->from('coop_finance_month_profile');
		$this->db->where("profile_month = '".(int)$month."' AND profile_year = '".$year."' ");
		$row = $this->db->get()->result_array();
		$row_profile = @$row[0];
		$profile_id = $row_profile['profile_id'];

		$x=0;
		$join_arr = array();
		$join_arr[$x]['table'] = 'coop_mem_apply as t2';
		$join_arr[$x]['condition'] = 't1.member_id = t2.member_id';
		$join_arr[$x]['type'] = 'inner';
		$x++;
		$join_arr[$x]['table'] = 'coop_prename as t3';
		$join_arr[$x]['condition'] = 't2.prename_id = t3.prename_id';
		$join_arr[$x]['type'] = 'left';
		$x++;
		$join_arr[$x]['table'] = 'coop_mem_group as t4';
		$join_arr[$x]['condition'] = 't2.level = t4.id';
		$join_arr[$x]['type'] = 'left';

		$this->paginater_all->type(DB_TYPE);
		$this->paginater_all->select(
				't1.member_id,
				t1.return_profile_id,
				t1.total_return_amount,
				t1.return_profile_status,
				t2.firstname_th,
				t2.lastname_th,
				t3.prename_short,
				t4.mem_group_name'
		);
		$this->paginater_all->main_table('coop_return_interest_profile as t1');
		$this->paginater_all->where("1=1 AND finance_month_profile_id = '".$profile_id."'");
		$this->paginater_all->page_now(@$_GET["page"]);
		$this->paginater_all->per_page(20);
		$this->paginater_all->page_link_limit(20);
		$this->paginater_all->order_by('member_id ASC');
		$this->paginater_all->join_arr($join_arr);
		$row = $this->paginater_all->paginater_process();

		$paging = $this->pagination_center->paginating($row['page'], $row['num_rows'], $row['per_page'], $row['page_link_limit'],@$_GET);//$page_now = 1, $row_total = 1, $per_page = 20, $page_limit = 20
		$i = $row['page_start'];

		$arr_data['num_rows'] = $row['num_rows'];
		$arr_data['paging'] = $paging;
		$arr_data['row'] = $row['data'];
		$arr_data['i'] = $i;

		$this->libraries->template('finance/finance_return',$arr_data);
	}

	function finance_return(){
		$arr_data = array();

		//Set Selection value
		$this->db->select(array('id','mem_group_name'));
		$this->db->from('coop_mem_group');
		$this->db->where("mem_group_type = '1'");
		$row_mem_group = $this->db->get()->result_array();
		$arr_data['mem_group'] = $row_mem_group;
		$this->db->select('mem_type_id, mem_type_name');
		$this->db->from('coop_mem_type');
		$row = $this->db->get()->result_array();
		$arr_data['mem_type'] = $row;

		if($_GET['department']!=''){
			$this->db->select(array('id','mem_group_name'));
			$this->db->from('coop_mem_group');
			$this->db->where("mem_group_parent_id = '".$_GET['department']."'");
			$row_mem_group = $this->db->get()->result_array();

			$arr_data['faction'] = $row_mem_group;

			foreach(@$row_mem_group AS $key=>$value){
				$arr_data['arr_faction'][@$value['id']] = @$value['mem_group_name'];
			}
		}

		if($_GET['faction']!=''){
			$this->db->select(array('id','mem_group_name'));
			$this->db->from('coop_mem_group');
			$this->db->where("mem_group_parent_id = '".$_GET['faction']."'");
			$row_mem_group = $this->db->get()->result_array();
			$arr_data['level'] = $row_mem_group;

			foreach(@$row_mem_group AS $key=>$value){
				$arr_data['arr_level'][@$value['id']] = @$value['mem_group_name'];
			}
		}

		$month_arr = array('1'=>'มกราคม','2'=>'กุมภาพันธ์','3'=>'มีนาคม','4'=>'เมษายน','5'=>'พฤษภาคม','6'=>'มิถุนายน','7'=>'กรกฎาคม','8'=>'สิงหาคม','9'=>'กันยายน','10'=>'ตุลาคม','11'=>'พฤศจิกายน','12'=>'ธันวาคม');
		$arr_data['month_arr'] = $month_arr;

		$month = @$_GET['month']!=''?$_GET['month']:(int)date('m');
		$year = @$_GET['year']!=''?$_GET['year']:(date('Y')+543);
		$arr_data['month'] = $month;
		$arr_data['year'] = $year;

		$this->db->select('profile_id');
		$this->db->from('coop_finance_month_profile');
		$this->db->where("profile_month = '".(int)$month."' AND profile_year = '".$year."' ");
		$row = $this->db->get()->result_array();
		$row_profile = @$row[0];
		$profile_id = $row_profile['profile_id'];

		//Set condition from search function
		$where = "";
		if($_GET['department']!=''){
			$where .= " AND t2.department = '".$_GET['department']."'";
		}
		if($_GET['faction']!=''){
			$where .= " AND t2.faction = '".$_GET['faction']."'";
		}
		if($_GET['level']!=''){
			$where .= " AND t2.level = '".$_GET['level']."'";
		}
		if($_GET['mem_type_id']!=''){
			$where .= " AND t2.mem_type_id = '".$_GET['mem_type_id']."'";
		}
		if($_GET['member_id']!=''){
			$where .= " AND t2.member_id like '%".$_GET['member_id']."%'";
		}
		if($_GET['return_from']!='' && $_GET['return_from']!='all') {
			$where .= " AND t1.return_from = '".$_GET['return_from']."'";
		}

		$x=0;
		$join_arr = array();
		$join_arr[$x]['table'] = 'coop_mem_apply as t2';
		$join_arr[$x]['condition'] = 't1.member_id = t2.member_id';
		$join_arr[$x]['type'] = 'inner';
		$x++;
		$join_arr[$x]['table'] = 'coop_prename as t3';
		$join_arr[$x]['condition'] = 't2.prename_id = t3.prename_id';
		$join_arr[$x]['type'] = 'left';
		$x++;
		$join_arr[$x]['table'] = 'coop_mem_group as t4';
		$join_arr[$x]['condition'] = 't2.level = t4.id';
		$join_arr[$x]['type'] = 'left';

		$row_per_page = $_GET['show_row']!='' ? (int)$_GET['show_row'] : 20;

		$this->paginater_all->type(DB_TYPE);
		$this->paginater_all->select(
				't1.member_id,
				t1.return_profile_id,
				t1.total_return_amount,
				t1.return_profile_status,
				t1.return_from,
				t2.firstname_th,
				t2.lastname_th,
				t2.mem_type,
				t3.prename_short,
				t4.mem_group_name'
		);
		$this->paginater_all->main_table('coop_return_interest_profile as t1');
		$this->paginater_all->where("1=1 AND finance_month_profile_id = '".$profile_id."'".$where);
		$this->paginater_all->page_now(@$_GET["page"]);
		$this->paginater_all->per_page($row_per_page);
		$this->paginater_all->page_link_limit(20);
		$this->paginater_all->order_by('member_id ASC');
		$this->paginater_all->join_arr($join_arr);
		$row = $this->paginater_all->paginater_process();

		$paging = $this->pagination_center->paginating($row['page'], $row['num_rows'], $row['per_page'], $row['page_link_limit'],@$_GET);//$page_now = 1, $row_total = 1, $per_page = 20, $page_limit = 20
		$i = $row['page_start'];

		$arr_data['num_rows'] = $row['num_rows'];
		$arr_data['paging'] = $paging;
		$arr_data['row'] = $row['data'];
		$arr_data['i'] = $i;

		$this->libraries->template('finance/finance_return',$arr_data);
	}
	function get_return_detail(){
		$this->db->select(array('t1.*','t2.contract_number as contract_number_loan','t3.contract_number as contract_number_atm'));
		$this->db->from('coop_return_interest as t1');
		$this->db->join('coop_loan as t2','t1.loan_id = t2.id','left');
		$this->db->join('coop_loan_atm as t3','t1.loan_atm_id = t3.loan_atm_id','left');
		$this->db->where("t1.return_profile_id = '".$_POST['return_profile_id']."'");
		$row = $this->db->get()->result_array();
		//echo $this->db->last_query();
		$data = array();
		$i = 0;
		foreach($row as $key => $value){
			$contract_number = '';
			if($value['loan_id']!=''){
				$contract_number = $value['contract_number_loan'];
			}else if($value['loan_atm_id'] != ''){
				$contract_number = $value['contract_number_atm'];
			}
			$data[$i]['contract_number'] = $contract_number;
			if($value['pay_type'] == 'interest'){
				$data[$i]['description'] = 'ดอกเบี้ยเงินกู้เลขที่สัญญา '.$contract_number;
			}else{
				$data[$i]['description'] = 'ต้นเงินกู้เลขที่สัญญา '.$contract_number;
			}
			$data[$i]['return_interest_amount'] = $value['return_interest_amount'];
			$data[$i]['run_id'] = $value['run_id'];
			$i++;
		}
		//echo"<pre>";print_r($data);echo"</pre>";
		$arr_data = array();
		$arr_data['data'] = $data;
		$this->load->view('finance/get_return_detail',$arr_data);
	}

	function approve_return(){
		set_time_limit(300);
		// echo"<pre>";print_r($_POST);exit;
		$timestamp = date('Y-m-d H:i:s');

		foreach($_POST['return_profile_id'] as $return_profile_id) {
			$profile = $this->db->select("*")
								->from("coop_return_interest_profile")
								->where('return_profile_id',$return_profile_id)
								->get()->row();

			$data_insert = array();
			$data_insert['return_profile_status'] = '1';
			$data_insert['approve_date'] = $timestamp;
			$data_insert['approve_id'] = $_SESSION['USER_ID'];
			$this->db->where('return_profile_id',$return_profile_id);
			$this->db->update('coop_return_interest_profile',$data_insert);

			$data_insert = array();
			$data_insert['return_status'] = '1';
			$this->db->where('return_profile_id',$return_profile_id);
			$this->db->update('coop_return_interest',$data_insert);

			$account = $this->db->select("*")
								->from("coop_maco_account")
								->where("mem_id = '".$profile->member_id."' AND type_id = '2' AND account_status = '0'")
								->get()->row();

			if(!empty($account)) {
				$timestamp = date('Y-m-d H:i:s');
				$balance = 0;
				$balance_no_in = 0;
				$this->db->select('*');
				$this->db->from('coop_account_transaction');
				$this->db->where("account_id = '".$account->account_id."'");
				$this->db->order_by('transaction_time DESC');
				$this->db->limit(1);
				$row_transaction = $this->db->get()->result_array();
				if(!empty($row_transaction)){
					$balance = @$row_transaction[0]['transaction_balance'];
					$balance_no_in = @$row_transaction[0]['transaction_no_in_balance'];
				}
				$sum = $balance + $profile->total_return_amount;
				$sum_no_in = $balance_no_in + $profile->total_return_amount;

				$data_insert = array();
				$data_insert['transaction_time'] = $timestamp;
				$data_insert['transaction_list'] = 'REVD';
				$data_insert['transaction_deposit'] = $profile->total_return_amount;
				$data_insert['transaction_balance'] = $sum;
				$data_insert['transaction_no_in_balance'] = $sum_no_in;
				$data_insert['user_id'] = $_SESSION['USER_ID'];
				$data_insert['account_id'] = $account->account_id;
				$this->db->insert('coop_account_transaction', $data_insert);

				$data_insert = array();
				$data_insert['return_profile_status'] = '2';
				$data_insert['date_transfer'] = $timestamp;
				$data_insert['account_id'] = $account->account_id;
				$data_insert['transfer_type'] = '1';
				$this->db->where('return_profile_id',$return_profile_id);
				$this->db->update('coop_return_interest_profile',$data_insert);

				$data_insert = array();
				$data_insert['return_status'] = '2';
				$this->db->where('return_profile_id',$return_profile_id);
				$this->db->update('coop_return_interest',$data_insert);
			}

		}

		$this->center_function->toast('บันทึกข้อมูลเรียบร้อยแล้ว');
		$url_para = "";
		if(!empty($_POST['department'])) {
			$url_para .= "&department=".$_POST['department'];
		}
		if(!empty($_POST['faction'])) {
			$url_para .= "&faction=".$_POST['faction'];
		}
		if(!empty($_POST['level'])) {
			$url_para .= "&level=".$_POST['level'];
		}
		if(!empty($_POST['mem_type_id'])) {
			$url_para .= "&mem_type_id=".$_POST['mem_type_id'];
		}
		if(!empty($_POST['member_id'])) {
			$url_para .= "&member_id=".$_POST['member_id'];
		}
		if(!empty($_POST['show_row'])) {
			$url_para .= "&show_row=".$_POST['show_row'];
		}
		if(!empty($_POST['return_from'])) {
			$url_para .= "&return_from=".$_POST['return_from'];
		}
		if(!empty($_POST['page'])) {
			$url_para .= "&page=".$_POST['page'];
		}

		// echo "<script>document.location.href = '".base_url(PROJECTPATH.'/finance/finance_return?year='.$_POST['year'].'&month='.$_POST['month']).$url_para."'</script>";
		echo "<script>document.location.href = '".base_url('/finance/finance_return?year='.$_POST['year'].'&month='.$_POST['month']).$url_para."'</script>";
	}

	function finance_pay_return(){
		$x=0;
		$join_arr = array();
		$join_arr[$x]['table'] = 'coop_mem_apply as t2';
		$join_arr[$x]['condition'] = 't1.member_id = t2.member_id';
		$join_arr[$x]['type'] = 'inner';
		$x++;
		$join_arr[$x]['table'] = 'coop_prename as t3';
		$join_arr[$x]['condition'] = 't2.prename_id = t3.prename_id';
		$join_arr[$x]['type'] = 'left';
		$x++;
		$join_arr[$x]['table'] = 'coop_user as t4';
		$join_arr[$x]['condition'] = 't1.approve_id = t4.user_id';
		$join_arr[$x]['type'] = 'left';

		$this->paginater_all->type(DB_TYPE);
		$this->paginater_all->select(array(
			't1.return_profile_id',
			't1.approve_date',
			't1.member_id',
			't2.firstname_th',
			't2.lastname_th',
			't3.prename_short',
			't1.total_return_amount',
			't4.user_name'
		));
		$this->paginater_all->main_table('coop_return_interest_profile as t1');
		$this->paginater_all->where("t1.return_profile_status = '1'");
		$this->paginater_all->page_now(@$_GET["page"]);
		$this->paginater_all->per_page(20);
		$this->paginater_all->page_link_limit(20);
		$this->paginater_all->order_by('t1.approve_date ASC');
		$this->paginater_all->join_arr($join_arr);
		$row = $this->paginater_all->paginater_process();
		//echo"<pre>";print_r($row);exit;
		$paging = $this->pagination_center->paginating($row['page'], $row['num_rows'], $row['per_page'], $row['page_link_limit'],@$_GET);//$page_now = 1, $row_total = 1, $per_page = 20, $page_limit = 20
		$i = $row['page_start'];
		//echo $this->db->last_query(); exit;

		$arr_data['num_rows'] = $row['num_rows'];
		$arr_data['paging'] = $paging;
		$arr_data['data'] = $row['data'];
		$arr_data['i'] = $i;

		$this->libraries->template('finance/finance_pay_return',$arr_data);
	}

	function get_return_data(){
		$data_arr = array();
		$this->db->select(array(
			't1.*',
			't2.firstname_th',
			't2.lastname_th',
			't3.prename_short',
			't2.dividend_bank_id',
			't2.dividend_bank_branch_id',
			't2.dividend_acc_num'
		));
		$this->db->from('coop_return_interest_profile as t1');
		$this->db->join('coop_mem_apply as t2','t1.member_id = t2.member_id','inner');
		$this->db->join('coop_prename as t3','t2.prename_id = t3.prename_id','left');
		$this->db->where("t1.return_profile_id = '".$_POST['return_profile_id']."'");
		$row = $this->db->get()->result_array();
		$row_profile = @$row[0];

		$data_arr['member_id'] = $row_profile['member_id'];
		$data_arr['prename_short'] = $row_profile['prename_short'];
		$data_arr['firstname_th'] = $row_profile['firstname_th'];
		$data_arr['lastname_th'] = $row_profile['lastname_th'];
		$data_arr['total_return_amount'] = $row_profile['total_return_amount'];
		$data_arr['dividend_bank_id'] = $row_profile['dividend_bank_id'];
		$data_arr['dividend_bank_branch_id'] = $row_profile['dividend_bank_branch_id'];
		$data_arr['dividend_acc_num'] = $row_profile['dividend_acc_num'];

		echo json_encode($data_arr);
		exit;
	}

	function finance_pay_return_save(){
		//echo"<pre>";print_r($_POST);exit;
		$data_insert = array();
		$data_insert['date_transfer'] = $_POST['date_transfer'];
		$data_insert['transfer_type'] = $_POST['pay_type'];
		if($_POST['pay_type'] == '1'){
			$data_insert['account_id'] = $_POST['account_id'];
		}else if($_POST['pay_type'] == '2'){
			$data_insert['dividend_bank_id'] = $_POST['dividend_bank_id'];
			$data_insert['dividend_bank_branch_id'] = $_POST['dividend_bank_branch_id'];
			$data_insert['dividend_acc_num'] = $_POST['dividend_acc_num'];
		}
		$data_insert['return_profile_status'] = '2';
		$this->db->where('return_profile_id',$_POST['return_profile_id']);
		$this->db->update('coop_return_interest_profile',$data_insert);

		$data_insert = array();
		$data_insert['return_status'] = '2';
		$this->db->where('return_profile_id',$_POST['return_profile_id']);
		$this->db->update('coop_return_interest',$data_insert);

		$this->center_function->toast('บันทึกข้อมูลเรียบร้อยแล้ว');
		echo "<script>document.location.href = '".base_url(PROJECTPATH.'/finance/finance_pay_return')."'</script>";
	}

	function finance_month_process_excel(){
		ini_set('memory_limit', -1);
		set_time_limit (90000);
		$this->db->save_queries = FALSE;

		$arr_data = array();
		$month_arr = $this->center_function->month_arr();
		$arr_data['month_arr'] = $month_arr;
		//ตอนแรกเป็น get แต่จะเปลี่ยน form เป็น post
		//$_GET = @$_POST;
		$month = @$_GET['month']!=''?$_GET['month']:(int)date('m');
		$year = @$_GET['year']!=''?$_GET['year']:(date('Y')+543);
		$show_row = @$_GET['show_row']!=''?$_GET['show_row']:'100';
		$arr_data['month'] = $month;
		$arr_data['year'] = $year;
		$arr_data['show_row'] = $show_row;
		if(@$_GET['level'] != ''){
			$mem_group = $_GET['level'];
		}else if(@$_GET['faction'] != ''){
			$mem_group = $_GET['faction'];
		}else if(@$_GET['department'] != ''){
			$mem_group = $_GET['department'];
		}
		$this->db->select('*');
		$this->db->from('coop_mem_group');
		$this->db->where("id = '".@$mem_group."'");
		$row = $this->db->get()->result_array();
		$row_mem_group = @$row[0];
		if(!empty($row_mem_group)){
			$department = $row_mem_group['mem_group_name'];
		}else{
			$department = 'ทั้งหมด';
		}
		$arr_data['department'] = $department;

		$where = " AND member_status = '1' ";

		if(@$_GET['department']!=''){
			$where .= " AND department = '".$_GET['department']."'";
		}
		if(@$_GET['faction']!=''){
			$where .= " AND faction = '".$_GET['faction']."'";
		}
		if(@$_GET['level']!=''){
			$where .= " AND level = '".$_GET['level']."'";
		}
		if(@$_GET['mem_type_id']!=''){
			$where .= " AND mem_type_id = '".$_GET['mem_type_id']."'";
		}
		if(@$_GET['member_id']!=''){
			$where .= " AND coop_mem_apply.member_id = '".$_GET['member_id']."'";
		}
		if(@$_GET['pay_type']!=''){
			$where .= " AND coop_receipt.pay_type = '".$_GET['pay_type']."'";
		}

		$this->db->select('profile_id');
		$this->db->from('coop_finance_month_profile');
		$this->db->where("profile_month = '".(int)$month."' AND profile_year = '".$year."' ");
		$row = $this->db->get()->result_array();
		$row_profile = @$row[0];
		if(@$row_profile['profile_id'] == ''){
			$data_insert = array();
			$data_insert['profile_month'] = (int)$month;
			$data_insert['profile_year'] = $year;
			$this->db->insert('coop_finance_month_profile', $data_insert);

			$profile_id = $this->db->insert_id();
		}else{
			$profile_id = $row_profile['profile_id'];
			//$this->db->where('profile_id', $profile_id);
			//$this->db->delete('coop_finance_month_detail');
		}

		$row['data'] = $this->db->select(
				"coop_mem_apply.member_id,
				coop_mem_apply.firstname_th,
				coop_mem_apply.lastname_th,
				coop_prename.prename_short,
				coop_mem_group.mem_group_name,
				t4.pay_amount
				")
			->from('coop_mem_apply')
			->join("coop_prename","coop_prename.prename_id = coop_mem_apply.prename_id","left")
			->join("coop_mem_group","coop_mem_group.id = coop_mem_apply.level","left")
			->join("coop_receipt","coop_receipt.member_id = coop_mem_apply.member_id AND finance_month_profile_id = '".$profile_id."'","left")
			->join("(SELECT
						t2.member_id,
						t2.profile_id,
						SUM(t2.pay_amount) AS pay_amount
					FROM
							coop_finance_month_detail AS t2
					WHERE t2.profile_id = '".$profile_id."'
					GROUP BY t2.member_id,t2.profile_id) AS t4",
					" t4.member_id = coop_mem_apply.member_id","left")
			->where("1=1 ".$where)
			->order_by('member_id ASC')
			->get()->result_array();

		$pay_type = array('0'=>'เงินสด','1'=>'โอนเงิน');
		foreach($row['data'] as $key => $value){

			$row['data'][$key]['pay_amount'] = @$value['pay_amount'];

			$row_non_pay = $this->db->select('non_pay_amount')
			->from('coop_non_pay')
			->where("member_id = '".@$value['member_id']."' AND non_pay_month = '".(int)$month."' AND non_pay_year = '".$year."'")
			->get()->result_array();
			$row_non_pay = @$row_non_pay[0];

			$row['data'][$key]['real_pay_amount'] = @$value['pay_amount'] - @$row_non_pay['non_pay_amount'];

			$row_receipt = $this->db->select(array('receipt_id','pay_type'))
			->from('coop_receipt')
			->where("finance_month_profile_id = '".@$profile_id."' AND member_id = '".@$value['member_id']."'")
			->get()->result_array();
			$row_receipt = @$row_receipt[0];

			$row['data'][$key]['receipt_id'] = @$row_receipt['receipt_id'];
			$row['data'][$key]['pay_type'] = @$pay_type[@$row_receipt['pay_type']];

		}
		//echo '<pre>'; print_r($row['data']); echo '</pre>';
		$arr_data['data'] = $row['data'];

		$this->load->view('finance/finance_month_process_excel',$arr_data);
	}

	function finance_month_return_excel(){
		ini_set('memory_limit', -1);
		set_time_limit (90000);
		$this->db->save_queries = FALSE;

		$arr_data = array();
		$month_arr = $this->center_function->month_arr();
		$arr_data['month_arr'] = $month_arr;

		$profile = $this->db->select('profile_id')
							->from('coop_finance_month_profile')
							->where("profile_month = '".(int)$_GET['month']."' AND profile_year = '".$_GET['year']."'")
							->get()->row();

		//Set condition from search function
		$where = "";
		if($_GET['department']!=''){
			$where .= " AND t4.department = '".$_GET['department']."'";
		}
		if($_GET['faction']!=''){
			$where .= " AND t4.faction = '".$_GET['faction']."'";
		}
		if($_GET['level']!=''){
			$where .= " AND t4.level = '".$_GET['level']."'";
		}
		if($_GET['mem_type_id']!=''){
			$where .= " AND t4.mem_type_id = '".$_GET['mem_type_id']."'";
		}
		if($_GET['member_id']!=''){
			$where .= " AND t4.member_id like '%".$_GET['member_id']."%'";
		}
		if($_GET['return_from']!='' && $_GET['return_from']!='all') {
			$where .= " AND t1.return_from = '".$_GET['return_from']."'";
		}

		$return_profiles = $this->db->select('t1.member_id, t1.return_from, t2.account_id, t2.account_name, t3.transaction_deposit, t3.transaction_balance')
									->from("coop_return_interest_profile as t1")
									->join("coop_maco_account as t2", "t1.account_id = t2.account_id", "inner")
									->join("coop_account_transaction as t3", "t2.account_id = t3.account_id AND t3.transaction_list = 'REVD' AND t3.transaction_time = t1.date_transfer", "inner")
									->join("coop_mem_apply as t4", "t1.member_id = t4.member_id", "inner")
									->where("t1.finance_month_profile_id = '".$profile->profile_id."' AND t1.return_profile_status = 2".$where)
									->order_by("t1.member_id")
									->get()->result_array();
		$arr_data["datas"] = $return_profiles;

		$this->load->view('finance/finance_month_process_return_excel',$arr_data);
	}
}
