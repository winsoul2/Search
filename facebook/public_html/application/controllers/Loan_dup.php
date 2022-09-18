<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class Loan_dup extends CI_Controller {
	function __construct()
	{
		parent::__construct();
	}
	public function index()
	{
		$arr_data = array();

		if($this->input->get('member_id')!=''){
			$member_id = $this->input->get('member_id');
		}else{
			$member_id = '';
		}
		$arr_data = array();
		$arr_data['member_id'] = $member_id;

		$this->db->select('*');
		$this->db->from('coop_share_setting');
		$this->db->order_by('setting_id DESC');
		$row = $this->db->get()->result_array();
		$arr_data['share_value'] = $row[0]['setting_value'];

		if($member_id != '') {
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

			$this->db->select('*');
			$this->db->from('coop_mem_share');
			$this->db->where("member_id = '".$member_id."' AND share_status IN('1','2')");
			$this->db->order_by('share_date DESC, share_id DESC');
			$this->db->limit(1);
			$row_prev_share = $this->db->get()->result_array();
			$row_prev_share = @$row_prev_share[0];

			$arr_data['count_share'] = $row_prev_share['share_collect'];
			$arr_data['cal_share'] = $row_prev_share['share_collect_value'];

			$this->db->select('*');
			$this->db->from('coop_mem_share');
			$this->db->where("member_id = '".$member_id."' AND share_status IN('1','2') AND share_period IS NOT NULL");
			$this->db->order_by('share_date DESC, share_id DESC');
			$this->db->limit(1);
			$row_share_month = $this->db->get()->result_array();
			$row_share_month = @$row_share_month[0];
			$arr_data['share_period'] = @$row_share_month['share_period'];

			$this->db->select('*');
			$this->db->from('coop_maco_account');
			$this->db->where("mem_id = '".$member_id."'  AND account_status = '0'");
			$rs_account = $this->db->get()->result_array();
			$count_account = 0;
			$cal_account = 0;
			foreach($rs_account as $key => $row_account){
				$this->db->select('*');
				$this->db->from('coop_account_transaction');
				$this->db->where("account_id = '".$row_account['account_id']."'");
				$this->db->order_by('transaction_time DESC, transaction_id DESC');
				$this->db->limit(1);
				$row_account_trans = $this->db->get()->result_array();

				$cal_account += @$row_account_trans[0]['transaction_balance'];
				$count_account++;

				$rs_account[$key]['transaction_balance'] = @$row_account_trans[0]['transaction_balance'];
			}
			$arr_data['data_account'] = $rs_account;
			$arr_data['count_account'] = $count_account;
			$arr_data['cal_account'] = $cal_account;

			$this->db->select(array(
				't2.id',
				't2.petition_number',
				't2.contract_number',
				't2.member_id',
				't3.firstname_th',
				't3.lastname_th',
				't2.loan_amount',
				't2.loan_amount_balance'
			));
			$this->db->from('coop_loan_guarantee_person as t1');
			$this->db->join('coop_loan as t2','t1.loan_id = t2.id','inner');
			$this->db->join('coop_mem_apply as t3','t2.member_id = t3.member_id','inner');
			$this->db->where("t1.guarantee_person_id = '".$member_id."' AND t2.loan_status IN('1','2')");
			$rs_guarantee = $this->db->get()->result_array();

			$arr_data['count_contract'] = 0;
			$arr_data['sum_guarantee_balance'] = 0;
			$arr_data['rs_guarantee'] = @$rs_guarantee;
			foreach($rs_guarantee as $key => $row_count_guarantee){
				@$arr_data['sum_balance'] += $row_count_guarantee['loan_amount_balance'];
				$arr_data['count_contract']++;
			}

			$this->db->select(array(
				'*'
			));
			$this->db->from('coop_loan as t1');
			$this->db->where("t1.member_id = '".$member_id."' AND t1.loan_status IN('1','2')");
			$rs_count_loan = $this->db->get()->result_array();

			$arr_data['count_loan'] = 0;
			$arr_data['sum_loan_balance'] = 0;
			foreach($rs_count_loan as $key => $row_count_loan){
				$arr_data['sum_loan_balance'] += $row_count_loan['loan_amount_balance'];
				$arr_data['count_loan']++;
			}

			$this->db->select(array(
				't1.deduct_status',
				't1.createdatetime',
				't1.contract_number',
				't1.petition_number',
				't3.loan_name as loan_type_detail',
				't3.loan_name_description',
				't1.loan_amount',
				't1.loan_amount_balance',
				't1.guarantee_for_id',
				't2.user_name',
				't1.loan_status',
				't1.id',
				't1.loan_type',
				't4.id as transfer_id',
				't4.file_name as transfer_file',
				't5.petition_file'
			));
			$this->db->from('coop_loan as t1');
			$this->db->join('coop_loan_name as t3','t1.loan_type = t3.loan_name_id','inner');
			$this->db->join('coop_loan_type as t5','t3.loan_type_id = t5.id','inner');
			$this->db->join('coop_user as t2','t1.admin_id = t2.user_id','left');
			$this->db->join('coop_loan_transfer as t4',"t1.id = t4.loan_id AND t4.transfer_status != '2'",'left');
			$this->db->where("t1.member_id = '".$member_id."' ");
			$this->db->order_by("t1.id DESC");
			$rs_loan = $this->db->get()->result_array();
			$arr_data['rs_loan'] = $rs_loan;
			//echo $this->db->last_query();exit;
			//////////////////////////////////////////////////////
			$prev_loan_active_arr = array();
			$i=0;
			$this->db->select(array(
				'*'
			));
			$this->db->from('coop_loan as t1');
			$this->db->where("t1.member_id = '".$member_id."' AND t1.loan_status = '1'");
			$prev_loan_active = $this->db->get()->result_array();
			foreach($prev_loan_active as $key => $value){
				$prev_loan_active_arr[$i]['id'] = $value['id'];
				$prev_loan_active_arr[$i]['contract_number'] = $value['contract_number'];
				$prev_loan_active_arr[$i]['loan_amount_balance'] = $value['loan_amount_balance'];
				$prev_loan_active_arr[$i]['checked'] = "principal";
				$cal_loan_interest = array();
				$cal_loan_interest['loan_id'] = $value['id'];
				$cal_loan_interest['date_interesting'] = date('Y-m-d');
				$interest_loan = $this->loan_libraries->cal_loan_interest($cal_loan_interest);

				$prev_loan_active_arr[$i]['interest'] = $interest_loan;
				$prev_loan_active_arr[$i]['type'] = 'loan';
				$prev_loan_active_arr[$i]['prev_loan_total'] = $value['loan_amount_balance']+$interest_loan;

				$this->db->select(array(
					'*'
				));
				$this->db->from('coop_finance_month_detail as t1');
				$this->db->where("
					t1.loan_id = '".$value['id']."'
					AND t1.run_status = '0'
					AND t1.pay_type = 'principal'
				");
				$row = $this->db->get()->result_array();
				$principal_month = 0;
				foreach($row as $key2 => $value2){
					$principal_month += $value2['pay_amount'];
				}
				$prev_loan_active_arr[$i]['principal_without_finance_month'] = $value['loan_amount_balance'] - $principal_month;
				$i++;
			}
			$this->db->select(array(
				'*'
			));
			$this->db->from('coop_loan_atm as t1');
			$this->db->where("t1.member_id = '".$member_id."' AND t1.loan_atm_status = '1'");
			$prev_loan_active = $this->db->get()->result_array();
			foreach($prev_loan_active as $key => $value){
				$prev_loan_active_arr[$i]['id'] = $value['loan_atm_id'];
				$prev_loan_active_arr[$i]['contract_number'] = $value['contract_number'];
				$prev_loan_active_arr[$i]['loan_amount_balance'] = $value['total_amount_approve'] - $value['total_amount_balance'];
				$prev_loan_active_arr[$i]['checked'] = "all";
				$cal_loan_interest = array();
				$cal_loan_interest['loan_atm_id'] = $value['loan_atm_id'];
				$cal_loan_interest['date_interesting'] = date('Y-m-d');
				//อันเดิม
				//$interest_atm = $this->loan_libraries->cal_atm_interest($cal_loan_interest);
				//ดอกเบี้ยเงินกู้ตามช่วงเวลาที่มีการทำรายการ
				$interest_atm = $this->loan_libraries->cal_atm_interest_report_test($cal_loan_interest,"echo", array("month"=> date("m"), "year" => date("Y") ), false )['interest_month'];

				//รายการที่มีการผ่านรายการแล้ว
				// $total_atm_after_process = $this->loan_libraries->cal_atm_after_process($cal_loan_interest);

				$prev_loan_active_arr[$i]['interest'] = $interest_atm;
				$prev_loan_active_arr[$i]['type'] = 'atm';
				// $prev_loan_active_arr[$i]['prev_loan_total'] = (@$prev_loan_active_arr[$i]['loan_amount_balance']-@$total_atm_after_process)+@$interest_atm;
				//อันเดิม
				$prev_loan_active_arr[$i]['prev_loan_total'] = $prev_loan_active_arr[$i]['loan_amount_balance']+$interest_atm;

				$this->db->select(array(
					'*'
				));
				$this->db->from('coop_finance_month_detail as t1');
				$this->db->where("
					t1.loan_atm_id = '".$value['loan_atm_id']."'
					AND t1.run_status = '0'
					AND t1.pay_type = 'principal'
				");
				$row = $this->db->get()->result_array();
				$principal_month = 0;
				foreach($row as $key2 => $value2){
					$principal_month += $value2['pay_amount'];
				}
				$prev_loan_active_arr[$i]['principal_without_finance_month'] = $prev_loan_active_arr[$i]['loan_amount_balance'] - $principal_month;
				$i++;
			}
			$arr_data['prev_loan_active'] = $prev_loan_active_arr;
////////////////////////////////////////////////////////
		}

		$this->db->select(array(
			'*'
		));
		$this->db->from('coop_term_of_loan');
		$this->db->where("start_date <= '".date('Y-m-d')."'");
		$this->db->order_by("start_date ASC");
		$rs_rule = $this->db->get()->result_array();
		foreach($rs_rule as $key => $value){
			$arr_data['rs_rule'][$value['type_id']] = $value;
		}
		$this->db->select(array(
			'loan_reason_id','loan_reason'
		));
		$this->db->from('coop_loan_reason');
		$rs_loan_reason = $this->db->get()->result_array();
		$arr_data['rs_loan_reason'] = $rs_loan_reason;

		$this->db->select(array(
			'id','loan_type'
		));
		$this->db->from('coop_loan_type');
		$rs_loan_type = $this->db->get()->result_array();
		$arr_data['rs_loan_type'] = $rs_loan_type;

		$this->db->select(array(
			'loan_deduct_list_code','loan_deduct_list','deduct_type'
		));
		$this->db->from('coop_loan_deduct_list');
		$this->db->where("loan_deduct_list_code != 'deduct_pay_prev_loan' AND deduct_type='deduct'");
		$rs_loan_deduct_list = $this->db->get()->result_array();
		$loan_deduct_list_odd = array();
		$loan_deduct_list_even = array();
		$i=1;
		foreach($rs_loan_deduct_list as $key => $value){
			if(in_array($value['loan_deduct_list_code'],array('deduct_share','deduct_blue_deposit','deduct_pay_prev_loan'))){
				$readonly='readonly';
			}else{
				$readonly='';
			}
			$value['readonly'] = $readonly;
			if($i==1){
				$loan_deduct_list_odd[] = $value;
				$i++;
			}else{
				$loan_deduct_list_even[] = $value;
				$i = 1;
			}
		}
		//echo"<pre>";print_r($loan_deduct_list_odd);print_r($loan_deduct_list_even);exit;
		$arr_data['loan_deduct_list'] = $rs_loan_deduct_list;
		$arr_data['loan_deduct_list_odd'] = $loan_deduct_list_odd;
		$arr_data['loan_deduct_list_even'] = $loan_deduct_list_even;

		//รายการซื้อ
		$this->db->select(array(
			'loan_deduct_list_code','loan_deduct_list','deduct_type'
		));
		$this->db->from('coop_loan_deduct_list');
		$this->db->where("loan_deduct_list_code != 'deduct_pay_prev_loan' AND deduct_type='buy'");
		$rs_loan_buy_list = $this->db->get()->result_array();
		$loan_buy_list_odd = array();
		$loan_buy_list_even = array();
		$i=1;
		foreach($rs_loan_buy_list as $key => $value){
			if($i==1){
				$loan_buy_list_odd[] = $value;
				$i++;
			}else{
				$loan_buy_list_even[] = $value;
				$i = 1;
			}
		}
		$arr_data['loan_buy_list'] = $rs_loan_buy_list;
		$arr_data['loan_buy_list_odd'] = $loan_buy_list_odd;
		$arr_data['loan_buy_list_even'] = $loan_buy_list_even;


		//ประวัติการผิดนัดชำระ
		$this->db->select(array('coop_non_pay.non_pay_month'
								,'coop_non_pay.non_pay_year'
								,'coop_non_pay.non_pay_status'
								,'coop_non_pay.member_id'
								,'coop_finance_month_profile.profile_id'
								,'SUM(coop_finance_month_detail.pay_amount) AS pay_amount'
								,'coop_finance_month_detail.loan_id'
								,'coop_finance_month_detail.deduct_code'
								,'coop_loan.contract_number'));
		$this->db->from('coop_non_pay');
		$this->db->join("coop_finance_month_profile","coop_non_pay.non_pay_month = coop_finance_month_profile.profile_month
							AND coop_non_pay.non_pay_year = coop_finance_month_profile.profile_year ","inner");
		$this->db->join("coop_finance_month_detail","coop_finance_month_detail.profile_id = coop_finance_month_profile.profile_id
							AND coop_finance_month_detail.member_id = coop_non_pay.member_id","inner");
		$this->db->join("coop_loan","coop_finance_month_detail.loan_id = coop_loan.id","inner");

		$this->db->where("coop_non_pay.non_pay_status NOT IN ('0')
							AND coop_non_pay.member_id = '{$member_id}'
							AND coop_finance_month_detail.deduct_code = 'LOAN'
						");
		$rs_debt = $this->db->get()->result_array();
		$arr_data['rs_debt'] = 	@$rs_debt;
		$arr_data['count_debt'] = 0;
		if(!empty($rs_debt)){
			foreach($rs_debt as $key => $row_count_debt){
				if($row_count_debt['profile_id'] != '' ){
					@$arr_data['sum_debt_balance'] += $row_count_debt['pay_amount'];
					$arr_data['count_debt']++;
				}
			}
		}
		//echo $this->db->last_query();exit;
		$arr_data['month_arr'] = array('1'=>'มกราคม','2'=>'กุมภาพันธ์','3'=>'มีนาคม','4'=>'เมษายน','5'=>'พฤษภาคม','6'=>'มิถุนายน','7'=>'กรกฎาคม','8'=>'สิงหาคม','9'=>'กันยายน','10'=>'ตุลาคม','11'=>'พฤศจิกายน','12'=>'ธันวาคม');

		$this->db->select('province_id, province_name');
		$this->db->from('coop_province');
		$this->db->order_by('province_name');
		$row = $this->db->get()->result_array();
		$arr_data['province'] = $row;

		$this->db->select(array(
			'*'
		));
		$this->db->from('coop_bank');
		$rs_bank = $this->db->get()->result_array();
		$arr_data['rs_bank'] = $rs_bank;

		$this->libraries->template('loan/index',$arr_data);
	}
	public function loan_cancel()
	{
		if (@$_GET['status_to']!='') {
			$data_insert = array();
			$data_insert['loan_status'] = $_GET['status_to'];
			$this->db->where('id', $_GET['loan_id']);
			$this->db->update('coop_loan', $data_insert);

			$this->center_function->toast('บันทึกข้อมูลเรียบร้อยแล้ว');
			echo "<script> document.location.href='".base_url(PROJECTPATH.'/loan/loan_cancel')."' </script>";
		}
		$arr_data = array();

		$x=0;
		$join_arr = array();
		$join_arr[$x]['table'] = 'coop_mem_apply';
		$join_arr[$x]['condition'] = 'coop_mem_apply.member_id = coop_loan.member_id';
		$join_arr[$x]['type'] = 'left';
		$x++;
		$join_arr[$x]['table'] = 'coop_user';
		$join_arr[$x]['condition'] = 'coop_loan.admin_id = coop_user.user_id';
		$join_arr[$x]['type'] = 'left';

		$this->paginater_all->type(DB_TYPE);
		$this->paginater_all->select('*, coop_loan.id as loan_id');
		$this->paginater_all->main_table('coop_loan');
		$this->paginater_all->where("loan_status IN('2','3')");
		$this->paginater_all->page_now(@$_GET["page"]);
		$this->paginater_all->per_page(20);
		$this->paginater_all->page_link_limit(20);
		$this->paginater_all->order_by('cancel_date DESC');
		$this->paginater_all->join_arr($join_arr);
		$row = $this->paginater_all->paginater_process();
		//echo"<pre>";print_r($row);exit;
		$paging = $this->pagination_center->paginating($row['page'], $row['num_rows'], $row['per_page'], $row['page_link_limit']);//$page_now = 1, $row_total = 1, $per_page = 20, $page_limit = 20
		$i = $row['page_start'];


		$arr_data['num_rows'] = $row['num_rows'];
		$arr_data['paging'] = $paging;
		$arr_data['data'] = $row['data'];
		$arr_data['i'] = $i;

		$loan_type = array();
		$this->db->select('*');
		$this->db->from("coop_loan_name");
		$rs_type = $this->db->get()->result_array();
		foreach($rs_type as $key => $row_type){
			$loan_type[$row_type['loan_name_id']] = $row_type['loan_name'];
		}
		$arr_data['loan_type'] = $loan_type;
		$this->libraries->template('loan/loan_cancel',$arr_data);
	}
	public function loan_approve()
	{
		$arr_data = array();
		$where = '1=1 ';
		if(@$_GET['loan_type']!=''){
			$where .= " AND coop_loan_type.id = '".$_GET['loan_type']."' ";
		}
		if(@$_GET['loan_name']!=''){
			$where .= " AND coop_loan_name.loan_name_id = '".$_GET['loan_name']."' ";
		}
		if(@$_GET['loan_status']!=''){
			$where .= " AND coop_loan.loan_status = '".$_GET['loan_status']." '";
		}else{
			$where .= " AND loan_status IN('0','1','5') ";
		}

		$order_by = 'coop_loan.createdatetime DESC,coop_loan.id ASC';
		if($_GET['approve_date']!=''){
			$approve_date_arr = explode('/',$_GET['approve_date']);
			$approve_day = stripslashes($approve_date_arr[0]);
			$approve_month = stripslashes($approve_date_arr[1]);
			$approve_year = stripslashes($approve_date_arr[2]);
			$_GET['approve_date'] = $approve_day."/".$approve_month."/".$approve_year;
			$approve_year -= 543;
			$approve_date = $approve_year.'-'.$approve_month.'-'.$approve_day;
			$where .= " AND coop_loan.approve_date >= '".$approve_date." 00:00:00.000'";
			$order_by = 'coop_loan.approve_date ASC,coop_loan.id ASC';
		}
		if($_GET['thru_date']!=''){
			$thru_date_arr = explode('/',$_GET['thru_date']);
			$thru_day = stripslashes($thru_date_arr[0]);
			$thru_month = stripslashes($thru_date_arr[1]);
			$thru_year = stripslashes($thru_date_arr[2]);
			$_GET['thru_date'] = $thru_day."/".$thru_month."/".$thru_year;
			$thru_year -= 543;
			$thru_date = $thru_year.'-'.$thru_month.'-'.$thru_day;
			$where .= " AND coop_loan.approve_date <= '".$thru_date." 23:59:59.000'";
			$order_by = 'coop_loan.approve_date ASC,coop_loan.id ASC';
		}

		$x=0;
		$join_arr = array();
		//$join_arr[$x]['table'] = 'coop_mem_apply';
		//$join_arr[$x]['condition'] = 'coop_mem_apply.member_id = coop_loan.member_id';
		//$join_arr[$x]['type'] = 'inner';
		$x++;
		$join_arr[$x]['table'] = 'coop_user';
		$join_arr[$x]['condition'] = 'coop_loan.admin_id = coop_user.user_id';
		$join_arr[$x]['type'] = 'left';
		$x++;
		$join_arr[$x]['table'] = 'coop_loan_name';
		$join_arr[$x]['condition'] = 'coop_loan.loan_type = coop_loan_name.loan_name_id';
		$join_arr[$x]['type'] = 'left';
		$x++;
		$join_arr[$x]['table'] = 'coop_loan_type';
		$join_arr[$x]['condition'] = 'coop_loan_type.id = coop_loan_name.loan_type_id';
		$join_arr[$x]['type'] = 'left';

		$this->paginater_all->type(DB_TYPE);
		//$this->paginater_all->select('coop_loan.*, coop_mem_apply.firstname_th, coop_mem_apply.lastname_th, coop_user.user_name, coop_loan_type.petition_file');
		$this->paginater_all->select('coop_loan.*, coop_user.user_name, coop_loan_type.petition_file');
		$this->paginater_all->field_count('coop_loan.id');
		$this->paginater_all->main_table('coop_loan');
		$this->paginater_all->where($where);
		$this->paginater_all->page_now(@$_GET["page"]);
		$this->paginater_all->per_page(100);
		$this->paginater_all->page_link_limit(20);
		$this->paginater_all->order_by($order_by);
		$this->paginater_all->join_arr($join_arr);
		$row = $this->paginater_all->paginater_process();
		//echo"<pre>";print_r($row);exit;
		$paging = $this->pagination_center->paginating($row['page'], $row['num_rows'], $row['per_page'], $row['page_link_limit'],$_GET);//$page_now = 1, $row_total = 1, $per_page = 20, $page_limit = 20
		$i = $row['page_start'];

		foreach($row['data'] AS $key=>$value){
			$this->db->select('coop_mem_apply.firstname_th, coop_mem_apply.lastname_th');
			$this->db->from("coop_mem_apply");
			$this->db->where("member_id = '".@$value['member_id']."'");
			$rs_member = $this->db->get()->result_array();
			$row_member = @$rs_member[0];
			$row['data'][$key]['firstname_th'] = $row_member['firstname_th'];
			$row['data'][$key]['lastname_th'] = $row_member['lastname_th'];
		}

		$arr_data['num_rows'] = $row['num_rows'];
		$arr_data['paging'] = $paging;
		$arr_data['data'] = $row['data'];
		$arr_data['i'] = $i;

		$loan_type = array();
		$this->db->select('*');
		$this->db->from("coop_loan_type");
		$rs_type = $this->db->get()->result_array();
		foreach($rs_type as $key => $row_type){
			$loan_type[$row_type['id']] = $row_type['loan_type'];
		}
		$arr_data['loan_type'] = $loan_type;
		if(@$_GET['loan_type']!=''){
			$loan_name = array();
			$this->db->select('*');
			$this->db->from("coop_loan_name");
			$this->db->where("loan_type_id = '".$_GET['loan_type']."'");
			$rs_loan_name = $this->db->get()->result_array();
			foreach($rs_loan_name as $key => $row_loan_name){
				$loan_name[$row_loan_name['loan_name_id']] = $row_loan_name['loan_name'];
			}
			$arr_data['loan_name'] = $loan_name;
		}else{
			$arr_data['loan_name'] = array();
		}

		$this->libraries->template('loan/loan_approve',$arr_data);
	}

	function loan_approve_save(){
		//echo"<pre>";print_r($_GET);exit;
			$this->db->select(array('t1.*','t3.loan_type_code'));
			$this->db->from("coop_loan as t1");
			$this->db->join("coop_loan_name as t2",'t1.loan_type = t2.loan_name_id','inner');
			$this->db->join("coop_loan_type as t3",'t2.loan_type_id = t3.id','inner');
			$this->db->where("t1.id = '".@$_GET['loan_id']."'");
			$rs_loan = $this->db->get()->result_array();
			$rs_loan = $rs_loan[0];
			$member_id = @$rs_loan['member_id'];
			$loan_amount = $rs_loan['loan_amount'];
			if($_GET['status_to']=='1'){
				$yymm = (date("Y")+543).date("m");

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

				$receipt_arr = array();
				$this->db->select(array('t1.*'));
				$this->db->from("coop_loan_prev_deduct as t1");
				$this->db->where("
				t1.loan_id = '".@$_GET['loan_id']."'");
				$row = $this->db->get()->result_array();
				//echo"<pre>";print_r($row);exit;
				$r=0;
				foreach($row as $key => $value){
					//update หนี้ห้อย-------------------
					$extra_debt_amount	= 0;//หนี้ห้อย
					/*if(date("Y-m", strtotime($rs_loan['createdatetime']) ) != date("Y-m") ){
						$month = date("m", strtotime("+1 months", strtotime($rs_loan['createdatetime'])) );
						if($month=="01")
							$year = date("Y", strtotime($rs_loan['createdatetime']) ) + 543 + 1;
						else
							$year = date("Y", strtotime($rs_loan['createdatetime']) ) + 543;

						$this->db->select('profile_id');
						$this->db->from('coop_finance_month_profile');
						$this->db->where("profile_month = '".(int)$month."' AND profile_year = '".$year."' ");
						$profile_id = $this->db->get()->result_array()[0]['profile_id'];

						$this->db->select("sum(pay_amount) as sum_of_pay_amount");
						$finance_month_detail = $this->db->get_where("coop_finance_month_detail", array(
							"profile_id" => $profile_id,
							"member_id" => $rs_loan['member_id'],
							"loan_id" => $value['ref_id'],
							"pay_type" => "principal",
							"run_status" => 0
						))->result_array()[0];
						if($finance_month_detail){
							$extra_debt['total_princical'] += $finance_month_detail['sum_of_pay_amount'];
							$extra_debt_amount	= $finance_month_detail['sum_of_pay_amount'];
						}
							
					}
					*/

					if($extra_debt_amount){
						$this->db->where("loan_id", $rs_loan['id']);
						$this->db->where("run_id", $value['run_id']);
						$this->db->set("pay_amount", "pay_amount - ".$extra_debt_amount, false);
						$this->db->update("coop_loan_prev_deduct");

						$this->db->where("loan_id", $rs_loan['id']);
						$this->db->set("estimate_receive_money", "estimate_receive_money + ".$extra_debt_amount, false);
						$this->db->update("coop_loan_deduct_profile");

						$this->db->where("loan_id", $rs_loan['id']);
						$this->db->where("loan_deduct_list_code", "deduct_pay_prev_loan");
						$this->db->set("loan_deduct_amount", "loan_deduct_amount - ".$extra_debt_amount, false);
						$this->db->update("coop_loan_deduct");

						// $this->db->where("id", $value['ref_id']);
						// $this->db->set("loan_amount_balance", "loan_amount_balance - ".$extra_debt_amount, false);
						// $this->db->update("coop_loan");
					}
					//--------------------------------

					if($value['pay_type'] == 'all'){
						if($value['data_type'] == 'loan'){
							$this->db->select(array('t1.*'));
							$this->db->from("coop_loan as t1");
							$this->db->where("t1.id = '".@$value['ref_id']."'");
							$ref_loan = $this->db->get()->result_array();
							$ref_loan = $ref_loan[0];

							// $cal_loan_interest = array();
							// $cal_loan_interest['loan_id'] = $value['ref_id'];
							// $cal_loan_interest['date_interesting'] = date('Y-m-d');
							// $interest_loan = $this->loan_libraries->cal_loan_interest($cal_loan_interest);

							$loan_amount = $ref_loan['loan_amount_balance'];//เงินกู้
							$loan_type = $ref_loan['loan_type'];//ประเภทเงินกู้ใช้หา เรทดอกเบี้ย
							$this->db->select("date(transaction_datetime) as transaction_datetime");
							$this->db->order_by("transaction_datetime desc");
							$this->db->limit(1);
							$date1 = $this->db->get_where("coop_loan_transaction", array(
								"loan_id" => $ref_loan['id']
							))->result_array()[0]['transaction_datetime'];//วันคิดดอกเบี้ยล่าสุด
							$tmp_date1 = date("Y-m", strtotime($date1) );
							if($tmp_date1 != date("Y-m"))//ใช้ออกเรียกเก็บ
								$date1 = date("Y-m-t", strtotime($date1) );
							
							$date2 = date("Y-m-d");//วันที่คิดดอกเบี้ย now
							$interest_loan = $this->loan_libraries->calc_interest_loan($loan_amount, $loan_type, $date1, $date2);
							$interest_loan = round($interest_loan);

							$receipt_arr[$r]['receipt_id'] = $receipt_id;
							$receipt_arr[$r]['member_id'] = $member_id;
							$receipt_arr[$r]['loan_id'] = $value['ref_id'];
							$receipt_arr[$r]['account_list_id'] = '15';
							$receipt_arr[$r]['principal_payment'] = $ref_loan['loan_amount_balance'] - @$extra_debt_amount;
							$receipt_arr[$r]['interest'] = $interest_loan;
							$receipt_arr[$r]['total_amount'] = $ref_loan['loan_amount_balance'] - @$extra_debt_amount + $interest_loan;
							$receipt_arr[$r]['payment_date'] = date('Y-m-d');
							$receipt_arr[$r]['createdatetime'] = date('Y-m-d H:i:s');
							$receipt_arr[$r]['loan_amount_balance'] = '0';
							$receipt_arr[$r]['transaction_text'] = 'หักกลบเงินกู้เลขที่สัญญา '.$ref_loan['contract_number'];
							$receipt_arr[$r]['deduct_type'] = 'all';
							$r++;
							$data_insert = array();
							if(@$extra_debt_amount>=1){
								$data_insert['loan_status'] = '1';
								$data_insert['loan_amount_balance'] = $extra_debt_amount;//คงค้างหนี้ห้อยไว้ รอการผ่านรายการ
							}else{
								$data_insert['loan_status'] = '4';
								$data_insert['loan_amount_balance'] = '0';
							}
							$this->db->where('id',$value['ref_id']);
							$this->db->update('coop_loan',$data_insert);

							$loan_transaction = array();
							$loan_transaction['loan_id'] = $value['ref_id'];
							if(@$extra_debt_amount>=1)
								$loan_transaction['loan_amount_balance'] = @$extra_debt_amount;
							else
								$loan_transaction['loan_amount_balance'] = '0';
							$loan_transaction['transaction_datetime'] = date('Y-m-d H:i:s');
							$loan_transaction['receipt_id'] = $receipt_id;
							$this->loan_libraries->loan_transaction($loan_transaction);

							$data_insert = array();
							$data_insert['date_last_interest'] = date('Y-m-d H:i:s');
							$this->db->where('id',$value['ref_id']);
							$this->db->update('coop_loan',$data_insert);

						}else if($value['data_type'] == 'atm'){
							$this->db->select(array('t1.*'));
							$this->db->from("coop_loan_atm as t1");
							$this->db->where("
								t1.loan_atm_id = '".$value['ref_id']."'
							");
							$row_atm = $this->db->get()->result_array();
							$row_atm = @$row_atm[0];

							$loan_amount_balance = $row_atm['total_amount_approve'] - $row_atm['total_amount_balance'];

							$cal_atm_interest = array();
							$cal_atm_interest['loan_atm_id'] = $value['ref_id'];
							$cal_atm_interest['date_interesting'] = date('Y-m-d');
							//อันเดิม
							//$interest_loan = $this->loan_libraries->cal_atm_interest($cal_atm_interest);

							//ดอกเบี้ยเงินกู้ตามช่วงเวลาที่มีการทำรายการ
							// $interest_loan = $this->loan_libraries->cal_atm_interest_transaction($cal_atm_interest);
							// $interest_amount = $this->loan_libraries->cal_atm_interest($cal_loan_interest);
							$interest_loan = $this->loan_libraries->cal_atm_interest_report_test($cal_atm_interest,"echo", array("month"=> date("m"), "year" => date("Y") ), false )['interest_month'];
			

							//รายการที่มีการผ่านรายการแล้ว
							// $total_atm_after_process = $this->loan_libraries->cal_atm_after_process($cal_atm_interest);

							$receipt_arr[$r]['receipt_id'] = $receipt_id;
							$receipt_arr[$r]['member_id'] = $member_id;
							$receipt_arr[$r]['loan_atm_id'] = $value['ref_id'];
							$receipt_arr[$r]['account_list_id'] = '31';
							$receipt_arr[$r]['principal_payment'] = $loan_amount_balance;
							$receipt_arr[$r]['interest'] = $interest_loan;
							//$receipt_arr[$r]['total_amount'] = $loan_amount_balance+$interest_loan;
							$receipt_arr[$r]['total_amount'] = (@$loan_amount_balance-@$total_atm_after_process)+@$interest_loan;
							$receipt_arr[$r]['payment_date'] = date('Y-m-d');
							$receipt_arr[$r]['createdatetime'] = date('Y-m-d H:i:s');
							$receipt_arr[$r]['loan_amount_balance'] = '0';
							$receipt_arr[$r]['transaction_text'] = 'หักกลบเงินกู้เลขที่สัญญา '.$row_atm['contract_number'];
							$receipt_arr[$r]['deduct_type'] = 'all';
							$r++;
							$data_insert = array();
							$data_insert['loan_status'] = '1';
							$data_insert['loan_amount_balance'] = '0';
							$this->db->where('loan_atm_id',$value['ref_id']);
							$this->db->update('coop_loan_atm_detail',$data_insert);

							$data_insert = array();
							$data_insert['loan_atm_status'] = '3';
							$data_insert['total_amount_balance'] = $row_atm['total_amount_approve'];
							$this->db->where('loan_atm_id',$value['ref_id']);
							$this->db->update('coop_loan_atm',$data_insert);

							$atm_transaction = array();
							$atm_transaction['loan_atm_id'] = $value['ref_id'];
							$atm_transaction['loan_amount_balance'] = '0';
							$atm_transaction['transaction_datetime'] = date('Y-m-d H:i:s');
							$atm_transaction['receipt_id'] = $receipt_id;
							$this->loan_libraries->atm_transaction($atm_transaction);

							$data_insert = array();
							$data_insert['date_last_interest'] = date('Y-m-d H:i:s');
							$this->db->where('loan_atm_id',$value['ref_id']);
							$this->db->update('coop_loan_atm',$data_insert);
						}
					}else if($value['pay_type'] == 'principal'){
						if($value['data_type'] == 'loan'){
							$this->db->select(array('t1.*'));
							$this->db->from("coop_loan as t1");
							$this->db->where("
								t1.id = '".$value['ref_id']."'
							");
							$row_loan = $this->db->get()->result_array();
							$row_loan = @$row_loan[0];

							$loan_amount_balance = ($row_loan['loan_amount_balance'] - $value['pay_amount']) + @$extra_debt_amount;

							$data_insert = array();
							$data_insert['loan_amount_balance'] = $loan_amount_balance;
							$this->db->where('id',$value['ref_id']);
							$this->db->update('coop_loan',$data_insert);

							$receipt_arr[$r]['receipt_id'] = $receipt_id;
							$receipt_arr[$r]['member_id'] = $member_id;
							$receipt_arr[$r]['loan_id'] = $value['ref_id'];
							$receipt_arr[$r]['account_list_id'] = '15';
							$receipt_arr[$r]['principal_payment'] = $value['pay_amount'] - @$extra_debt_amount;
							$receipt_arr[$r]['total_amount'] = $value['pay_amount'] - @$extra_debt_amount;
							$receipt_arr[$r]['payment_date'] = date('Y-m-d');
							$receipt_arr[$r]['createdatetime'] = date('Y-m-d H:i:s');
							$receipt_arr[$r]['loan_amount_balance'] = $loan_amount_balance;
							$receipt_arr[$r]['transaction_text'] = 'หักกลบเงินกู้เลขที่สัญญา '.$row_loan['contract_number'];
							$receipt_arr[$r]['deduct_type'] = 'principal';
							$r++;
							$loan_transaction = array();
							$loan_transaction['loan_id'] = $value['ref_id'];
							$loan_transaction['loan_amount_balance'] = $loan_amount_balance;
							$loan_transaction['transaction_datetime'] = date('Y-m-d H:i:s');
							$loan_transaction['receipt_id'] = $receipt_id;
							$this->loan_libraries->loan_transaction($loan_transaction);
						}else if($value['data_type'] == 'atm'){
							$this->db->select(array('t1.*'));
							$this->db->from("coop_loan_atm_detail as t1");
							$this->db->where("
								t1.loan_atm_id = '".$value['ref_id']."'
								AND loan_status = '0'
							");
							$this->db->order_by('loan_id ASC');
							$row_loan = $this->db->get()->result_array();
							$pay_amount = $value['pay_amount'];
							foreach($row_loan as $key2 => $value2){
								if($pay_amount > $value2['loan_amount_balance']){
									$data_insert = array();
									$data_insert['loan_amount_balance'] = 0;
									$data_insert['loan_status'] = '1';
									$this->db->where('loan_id',$value2['loan_id']);
									$this->db->update('coop_loan_atm_detail',$data_insert);
									$pay_amount = $pay_amount - $value2['loan_amount_balance'];
								}else{
									$data_insert = array();
									$data_insert['loan_amount_balance'] = $value2['loan_amount_balance'] - $pay_amount;
									$this->db->where('loan_id',$value2['loan_id']);
									$this->db->update('coop_loan_atm_detail',$data_insert);
									$pay_amount = 0;
								}
								if($pay_amount == 0){
									break;
								}
							}
							$this->db->select(array('t1.*'));
							$this->db->from("coop_loan_atm as t1");
							$this->db->where("
								t1.loan_atm_id = '".$value['ref_id']."'
							");
							$row_loan = $this->db->get()->result_array();

							$data_insert = array();
							$data_insert['total_amount_balance'] = $row_loan[0]['total_amount_balance'] + $value['pay_amount'];
							$data_insert['loan_atm_status'] = '4';
							$this->db->where('loan_atm_id',$value['ref_id']);
							$this->db->update('coop_loan_atm',$data_insert);

							$loan_amount_balance = $row_loan[0]['total_amount_approve']-($row_loan[0]['total_amount_balance'] + $value['pay_amount']);

							$atm_transaction = array();
							$atm_transaction['loan_atm_id'] = $value['ref_id'];
							$atm_transaction['loan_amount_balance'] = $loan_amount_balance;
							$atm_transaction['transaction_datetime'] = date('Y-m-d H:i:s');
							$atm_transaction['receipt_id'] = $receipt_id;
							$this->loan_libraries->atm_transaction($atm_transaction);

							$receipt_arr[$r]['receipt_id'] = $receipt_id;
							$receipt_arr[$r]['member_id'] = $member_id;
							$receipt_arr[$r]['loan_atm_id'] = $value['ref_id'];
							$receipt_arr[$r]['account_list_id'] = '31';
							$receipt_arr[$r]['principal_payment'] = $value['pay_amount'];
							$receipt_arr[$r]['total_amount'] = $value['pay_amount'];
							$receipt_arr[$r]['payment_date'] = date('Y-m-d');
							$receipt_arr[$r]['createdatetime'] = date('Y-m-d H:i:s');
							$receipt_arr[$r]['loan_amount_balance'] = $loan_amount_balance;
							$receipt_arr[$r]['transaction_text'] = 'หักกลบเงินกู้เลขที่สัญญา '.$row_loan[0]['contract_number'];
							$receipt_arr[$r]['deduct_type'] = 'principal';
							$r++;
						}
					}
				}
			///////////////////////////////////////////////////////////
				$this->db->select(array('t1.*','t2.account_list_id','t3.account_list'));
				$this->db->from("coop_loan_deduct as t1");
				$this->db->join("coop_loan_deduct_list as t2",'t1.loan_deduct_list_code = t2.loan_deduct_list_code','inner');
				$this->db->join("coop_account_list as t3",'t2.account_list_id = t3.account_id','left');
				$this->db->where("
					t1.loan_id = '".$_GET['loan_id']."' AND t1.loan_deduct_list_code != 'deduct_pay_prev_loan'
				");
				$row_deduct = $this->db->get()->result_array();
				//echo $this->db->last_query();
				//echo "<pre>";print_r($row_deduct);exit;
				foreach($row_deduct as $key => $value){
					if($value['loan_deduct_list_code'] == 'deduct_blue_deposit' && $value['loan_deduct_amount'] > 0){
						//เช็คสมุดเงินฝากสีน้ำเงิน
						$this->db->select(array('coop_maco_account.account_id','coop_maco_account.mem_id','coop_deposit_type_setting.type_name'));
						$this->db->from('coop_maco_account');
						$this->db->join("coop_deposit_type_setting","coop_maco_account.type_id = coop_deposit_type_setting.type_id","inner");
						$this->db->where("
							coop_maco_account.mem_id = '".$member_id."'
							 AND coop_maco_account.account_status = '0'
							AND coop_deposit_type_setting.deduct_loan = '1'
						");
						$this->db->limit(1);
						$rs_account = $this->db->get()->result_array();
						$row_account = @$rs_account[0];
						if(empty($rs_account)){
							$this->db->select(array('type_id','type_name','type_code'));
							$this->db->from('coop_deposit_type_setting');
							$this->db->where("deduct_loan = '1'");
							$this->db->limit(1);
							$rs_deduct_loan = $this->db->get()->result_array();
							$row_deduct_loan = @$rs_deduct_loan[0];

							$this->db->select('account_id');
							$this->db->from('coop_maco_account');
							$this->db->where("type_id = '".$row_deduct_loan['type_id']."' AND account_status = '0'");
							$this->db->order_by("account_id DESC");
							$this->db->limit(1);
							$row = $this->db->get()->result_array();
							if(!empty($row)){
								$auto_account_id = str_replace("001".$row_deduct_loan['type_code'],'',$row[0]['account_id']);
								$auto_account_id = (int)$auto_account_id;
								$auto_account_id = $auto_account_id + 1;
							}else{
								$auto_account_id = 1;
							}
							$acc_id = "001".$row_deduct_loan['type_code'].sprintf("%06d",@$auto_account_id);
							$account_id = @$acc_id;
							//echo '<pre>'; print_r($row_deduct_loan); echo '</pre>';
							$this->db->select('*');
							$this->db->from('coop_mem_apply');
							$this->db->where("member_id = '".$member_id."'");
							$rs_member = $this->db->get()->result_array();
							$row_member = @$rs_member[0];
							
							//start เช็คบัญชีในตาราง coop_account_transaction
							$this->db->select('account_id');
							$this->db->from('coop_account_transaction');
							$this->db->where("account_id = '".$account_id."'");
							$this->db->order_by("transaction_time DESC, transaction_id DESC");
							$this->db->limit(1);
							$check_account_transaction = $this->db->get()->result_array(); 
							if(!empty($check_account_transaction)){
								//หา account_id อีกรอบ
								$this->db->select('account_id');
								$this->db->from('coop_account_transaction');
								$this->db->where("account_id LIKE '001".$row_deduct_loan['type_code']."%'");
								$this->db->order_by("transaction_time DESC, transaction_id DESC");
								$this->db->limit(1);
								$last_account_id = $this->db->get()->result_array();
								$auto_account_id = str_replace("001".$row_deduct_loan['type_code'],'',$last_account_id[0]['account_id']);
								$auto_account_id = (int)$auto_account_id;
								$auto_account_id = $auto_account_id + 1;
								$account_id = "001".$row_deduct_loan['type_code'].sprintf("%06d",@$auto_account_id);
							} 
							//end เช็คบัญชีในตาราง coop_account_transaction
							
							$data_insert = array();
							$data_insert['account_id'] = @$account_id;
							$data_insert['mem_id'] = $member_id;
							$data_insert['member_name'] = $row_member['firstname_th']." ".$row_member['lastname_th'];
							$data_insert['account_name'] = $row_member['firstname_th']." ".$row_member['lastname_th'];
							$data_insert['created'] = date('Y-m-d H:i:s');
							$data_insert['account_amount'] = '0';
							$data_insert['book_number'] = '1';
							$data_insert['type_id'] = $row_deduct_loan['type_id'];
							$data_insert['account_status'] = '0';
							$this->db->insert('coop_maco_account', $data_insert);
							//$account_id = $this->db->insert_id();
							//$account_id = @$acc_id;
						}else{
							$account_id = $row_account['account_id'];
						}

						$this->db->where("account_id = '".$account_id."' AND loan_id ='".$_GET['loan_id']."'");
						$this->db->delete("coop_account_transaction");

						$transaction_list = 'XD';
						$transaction_deposit = $value['loan_deduct_amount'];

						$this->db->select('*');
						$this->db->from('coop_account_transaction');
						$this->db->where("account_id = '".$account_id."'");
						$this->db->order_by('transaction_time DESC, transaction_id DESC');
						$this->db->limit(1);
						$row = $this->db->get()->result_array();
						if(!empty($row)){
							$balance = @$row[0]['transaction_balance'];
							$balance_no_in = @$row[0]['transaction_no_in_balance'];
						}else{
							$balance = 0;
							$balance_no_in = 0;
						}

						$sum = @$balance + @$transaction_deposit;
						$sum_no_in = @$balance_no_in + @$transaction_deposit;

						$data_insert = array();
						$data_insert['transaction_time'] = date('Y-m-d H:i:s');
						$data_insert['transaction_list'] = @$transaction_list;
						$data_insert['transaction_withdrawal'] = '';
						$data_insert['transaction_deposit'] = @$transaction_deposit;
						$data_insert['transaction_balance'] = @$sum;
						$data_insert['transaction_no_in_balance'] = @$sum_no_in;
						$data_insert['user_id'] = @$_SESSION['USER_ID'];
						$data_insert['account_id'] = @$account_id;
						$data_insert['loan_id'] = $_GET['loan_id'];
						$data_insert['receipt_id'] = $receipt_id;

						if ($this->db->insert('coop_account_transaction', $data_insert)) {
							/*$this->center_function->toast("ทำการฝากเงินเรียบร้อยแล้ว");

							$data_acc['coop_account']['account_description'] = "สมาชิกฝากเงินเข้าบัญชี";
							$data_acc['coop_account']['account_datetime'] = date('Y-m-d H:i:s');

							$i=0;
							$data_acc['coop_account_detail'][$i]['account_type'] = 'debit';
							$data_acc['coop_account_detail'][$i]['account_amount'] = @$transaction_deposit];
							$data_acc['coop_account_detail'][$i]['account_chart_id'] = '10100';
							$i++;
							$data_acc['coop_account_detail'][$i]['account_type'] = 'credit';
							$data_acc['coop_account_detail'][$i]['account_amount'] = @$transaction_deposit;
							$data_acc['coop_account_detail'][$i]['account_chart_id'] = '20100';
							$this->account_transaction->account_process($data_acc);
							*/
							$receipt_arr[$r]['receipt_id'] = $receipt_id;
							$receipt_arr[$r]['member_id'] = $member_id;
							$receipt_arr[$r]['account_list_id'] = '30';
							$receipt_arr[$r]['principal_payment'] = $value['loan_deduct_amount'];
							$receipt_arr[$r]['total_amount'] = $value['loan_deduct_amount'];
							$receipt_arr[$r]['payment_date'] = date('Y-m-d');
							$receipt_arr[$r]['createdatetime'] = date('Y-m-d H:i:s');
							$receipt_arr[$r]['loan_amount_balance'] = $balance;
							$receipt_arr[$r]['transaction_text'] = 'ฝากเงินเลขที่บัญชี '.$account_id;
							$receipt_arr[$r]['deduct_type'] = 'all';
							$r++;
						}

					}else if($value['loan_deduct_list_code'] == 'deduct_share' && $value['loan_deduct_amount'] > 0){
						$this->db->select('*');
						$this->db->from('coop_share_setting');
						$this->db->order_by('setting_id DESC');
						$row = $this->db->get()->result_array();
						$share_setting = $row[0];

						$this->db->select('*');
						$this->db->from('coop_mem_share');
						$this->db->where("member_id = '".$member_id."' AND share_status = '1'");
						$this->db->order_by('share_date DESC, share_id DESC');
						$this->db->limit(1);
						$row_share = $this->db->get()->result_array();
						$row_share = @$row_share[0];
						$data_insert = array();
						$data_insert['member_id'] = $member_id;
						$data_insert['admin_id'] = $_SESSION['USER_ID'];
						$data_insert['share_type'] = 'SPL';
						$data_insert['share_date'] = date('Y-m-d H:i:s');
						$data_insert['share_payable'] = @$row_share['share_collect'];
						$data_insert['share_payable_value'] = @$row_share['share_collect_value'];
						$data_insert['share_early'] = $value['loan_deduct_amount']/$share_setting['setting_value'];
						$data_insert['share_early_value'] = $value['loan_deduct_amount'];
						$data_insert['share_collect'] = @$row_share['share_collect'] + ($value['loan_deduct_amount']/$share_setting['setting_value']);
						$data_insert['share_collect_value'] = @$row_share['share_collect_value']+@$value['loan_deduct_amount'];
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
						$receipt_arr[$r]['payment_date'] = date('Y-m-d');
						$receipt_arr[$r]['createdatetime'] = date('Y-m-d H:i:s');
						$receipt_arr[$r]['loan_amount_balance'] = @$row_share['share_collect_value']+@$value['loan_deduct_amount'];
						$receipt_arr[$r]['transaction_text'] = 'หุ้น';
						$receipt_arr[$r]['deduct_type'] = 'all';
						$r++;
					}else if($value['loan_deduct_list_code'] == 'buy_share' && $value['loan_deduct_amount'] > 0){
						$this->db->select('*');
						$this->db->from('coop_share_setting');
						$this->db->order_by('setting_id DESC');
						$row = $this->db->get()->result_array();
						$share_setting = $row[0];

						$this->db->select('*');
						$this->db->from('coop_mem_share');
						$this->db->where("member_id = '".$member_id."' AND share_status = '1'");
						$this->db->order_by('share_date DESC, share_id DESC');
						$this->db->limit(1);
						$row_share = $this->db->get()->result_array();
						$row_share = @$row_share[0];
						$data_insert = array();
						$data_insert['member_id'] = $member_id;
						$data_insert['admin_id'] = $_SESSION['USER_ID'];
						$data_insert['share_type'] = 'SPL';
						$data_insert['share_date'] = date('Y-m-d H:i:s');
						$data_insert['share_payable'] = @$row_share['share_collect'];
						$data_insert['share_payable_value'] = @$row_share['share_collect_value'];
						$data_insert['share_early'] = $value['loan_deduct_amount']/$share_setting['setting_value'];
						$data_insert['share_early_value'] = $value['loan_deduct_amount'];
						$data_insert['share_collect'] = @$row_share['share_collect'] + ($value['loan_deduct_amount']/$share_setting['setting_value']);
						$data_insert['share_collect_value'] = @$row_share['share_collect_value']+@$value['loan_deduct_amount'];
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
						$receipt_arr[$r]['payment_date'] = date('Y-m-d');
						$receipt_arr[$r]['createdatetime'] = date('Y-m-d H:i:s');
						$receipt_arr[$r]['loan_amount_balance'] = @$row_share['share_collect_value']+@$value['loan_deduct_amount'];
						$receipt_arr[$r]['transaction_text'] = 'ซื้อหุ้นจากการกู้';
						$receipt_arr[$r]['deduct_type'] = 'all';
						$r++;
					}else{
						if($value['loan_deduct_amount']>0){
							$receipt_arr[$r]['receipt_id'] = $receipt_id;
							$receipt_arr[$r]['member_id'] = $member_id;
							$receipt_arr[$r]['account_list_id'] = $value['account_list_id'];
							$receipt_arr[$r]['principal_payment'] = $value['loan_deduct_amount'];
							$receipt_arr[$r]['total_amount'] = $value['loan_deduct_amount'];
							$receipt_arr[$r]['payment_date'] = date('Y-m-d');
							$receipt_arr[$r]['createdatetime'] = date('Y-m-d H:i:s');
							$receipt_arr[$r]['loan_amount_balance'] = '';
							$receipt_arr[$r]['transaction_text'] = $value['account_list'];
							$receipt_arr[$r]['deduct_type'] = 'all';
							$r++;
						}
					}
				}
				//echo"<pre>";print_r($receipt_arr);exit;
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
				}
				if($sum_count>0){
					$data_insert = array();
					$data_insert['receipt_id'] = $receipt_id;
					$data_insert['member_id'] = @$member_id;
					$data_insert['admin_id'] = @$_SESSION['USER_ID'];
					$data_insert['sumcount'] = $sum_count;
					$data_insert['receipt_datetime'] = date('Y-m-d H:i:s');
					$this->db->insert('coop_receipt', $data_insert);

					$data_insert = array();
					$data_insert['deduct_receipt_id'] = $receipt_id;
					$this->db->where('id',$_GET['loan_id']);
					$this->db->update('coop_loan',$data_insert);
				}


			}

			$data_insert = array();
			if($_GET['status_to']=='1'){
				//ปีในการ gen เลขสัญญา
				$rs_month_account = $this->db->select('accm_month_ini')
				->from("coop_account_period_setting")
				->limit(1)
				->get()->result_array();
				$month_account = $rs_month_account[0]['accm_month_ini'];
				$month_now = date('m');	
				if((int)$month_now >= (int)$month_account){
					$year = (date('Y')+543)+1;
				}else{
					$year = (date('Y')+543);
				}
				$year_short = substr($year,2,2);
				$new_contact_number = '';

				$this->db->select('*');
				$this->db->from("coop_term_of_loan");
				$this->db->where("type_id = '".@$rs_loan['loan_type']."' AND start_date <= '".date('Y-m-d')."'");
				$this->db->order_by('start_date DESC');
				$this->db->limit(1);
				$rs_term_of_loan = $this->db->get()->result_array();
				$row_term_of_loan = @$rs_term_of_loan[0];
				$new_contact_number = $row_term_of_loan['prefix_code'].$year_short;

				$contact_number_now = $this->loan_libraries->get_contract_number($year);

				$new_contact_number .= sprintf("% 05d",$contact_number_now);
				//$new_contact_number = $new_contact_number."/".(date('Y')+543);
				$data_insert['contract_number'] = @$new_contact_number;
				//echo $new_contact_number;exit;

			}

			$data_insert['loan_status'] = @$_GET['status_to'];
			$data_insert['approve_date'] = date('Y-m-d H:i:s');
			$this->db->where('id', @$_GET['loan_id']);
			$this->db->update('coop_loan', $data_insert);

			$this->center_function->toast('บันทึกข้อมูลเรียบร้อยแล้ว');
			echo "<script> document.location.href='".base_url(PROJECTPATH.'/loan/loan_approve')."' </script>";
			exit;
	}

	function loan_transfer(){
		/*
		$arr_data = array();
		if(@$_GET['action'] == 'delete_transfer'){
			$data_insert = array();
			$data_insert['transfer_status'] = '1';
			$data_insert['cancel_date'] = date('Y-m-d H:i:s');
			$this->db->where('id', $_GET['transfer_id']);
			$this->db->update('coop_loan_transfer', $data_insert);

			$this->center_function->toast("ยื่นขอยกเลิกรายการแล้ว");
			echo "<script>document.location.href = '".base_url(PROJECTPATH.'/loan/loan_transfer?loan_id='.$_GET['loan_id'])."';</script>";
		}

		$this->db->select(array(
			'*',
			'coop_loan.id',
			'coop_loan.createdatetime as loan_date',
			'coop_loan_name.loan_name as loan_type',
			'coop_loan_transfer.id as transfer_id',
			'coop_maco_account.account_name',
			'transfer_user.user_name'
		));
		$this->db->from('coop_loan');
		$this->db->join('coop_mem_apply','coop_loan.member_id = coop_mem_apply.member_id','inner');
		$this->db->join('coop_loan_name','coop_loan.loan_type = coop_loan_name.loan_name_id','inner');
		$this->db->join("(SELECT * FROM coop_loan_transfer WHERE transfer_status != '2') as coop_loan_transfer","coop_loan.id = coop_loan_transfer.loan_id",'left');
		$this->db->join('coop_maco_account','coop_loan_transfer.account_id = coop_maco_account.account_id','left');
		$this->db->join('coop_user as transfer_user','transfer_user.user_id = coop_loan_transfer.admin_id','left');
		$this->db->where("coop_loan.id = '".@$_GET['loan_id']."'");
		$row = $this->db->get()->result_array();
		//echo $this->db->last_query();exit;
		$row = @$row[0];
		$arr_data['row'] = $row;
		//echo"<pre>";print_r($arr_data['row']);echo"</pre>";exit;
		if(@$row['member_id']!=''){
			$this->db->select(array('*'));
			$this->db->from('coop_maco_account');
			$this->db->where("mem_id = '".$row['member_id']."'");
			$rs_account = $this->db->get()->result_array();
			$arr_data['rs_account'] = @$rs_account;
		}
		*/

		/*$this->db->select(array(
			'*',
			'coop_loan.id',
			'coop_loan.createdatetime as loan_date',
			'coop_loan_name.loan_name as loan_type',
			'coop_loan_transfer.id as transfer_id',
			'coop_maco_account.account_name',
			'transfer_user.user_name'
		));
		$this->db->from('coop_loan');
		$this->db->join('coop_mem_apply','coop_loan.member_id = coop_mem_apply.member_id','inner');
		$this->db->join('coop_loan_name','coop_loan.loan_type = coop_loan_name.loan_name_id','inner');
		$this->db->join("(SELECT * FROM coop_loan_transfer WHERE transfer_status != '2') as coop_loan_transfer","coop_loan.id = coop_loan_transfer.loan_id",'left');
		$this->db->join('coop_maco_account','coop_loan_transfer.account_id = coop_maco_account.account_id','left');
		$this->db->join('coop_user as transfer_user','transfer_user.user_id = coop_loan_transfer.admin_id','left');
		$this->db->where("coop_loan.id = '".@$_GET['loan_id']."'");
		$row = $this->db->get()->result_array();
		//echo $this->db->last_query();exit;
		$row = @$row[0];
		$arr_data['row'] = $row;
		//echo"<pre>";print_r($arr_data['row']);echo"</pre>";exit;
		if(@$row['member_id']!=''){
			$this->db->select(array('*'));
			$this->db->from('coop_maco_account');
			$this->db->where("mem_id = '".$row['member_id']."'");
			$rs_account = $this->db->get()->result_array();
			$arr_data['rs_account'] = @$rs_account;
		}
	*/
		//$arr_data = array();

		//$arr_data['pay_type'] = $this->pay_type;
		//$arr_data['transfer_status'] = $this->transfer_status;
		///////////

		//$this->db->join('coop_mem_apply','coop_loan.member_id = coop_mem_apply.member_id','inner');
		//$this->db->join('coop_loan_name','coop_loan.loan_type = coop_loan_name.loan_name_id','inner');
		//$this->db->join("(SELECT * FROM coop_loan_transfer WHERE transfer_status != '2') as coop_loan_transfer","coop_loan.id = coop_loan_transfer.loan_id",'left');
		//$this->db->join('coop_maco_account','coop_loan_transfer.account_id = coop_maco_account.account_id','left');
		//$this->db->join('coop_user as transfer_user','transfer_user.user_id = coop_loan_transfer.admin_id','left');

		$x=0;
		$join_arr = array();
		/*$join_arr[$x]['table'] = 'coop_mem_apply';
		$join_arr[$x]['condition'] = 'coop_loan.member_id = coop_mem_apply.member_id';
		$join_arr[$x]['type'] = 'inner';

		$x++;
		*/
		/*
		$join_arr[$x]['table'] = 'coop_loan_name';
		$join_arr[$x]['condition'] = 'coop_loan.loan_type = coop_loan_name.loan_name_id';
		$join_arr[$x]['type'] = 'inner';
		$x++;
		*/

		/*
		$join_arr[$x]['table'] = "(SELECT * FROM coop_loan_transfer WHERE transfer_status = '0') as coop_loan_transfer";
		$join_arr[$x]['condition'] = "coop_loan.id = coop_loan_transfer.loan_id";
		$join_arr[$x]['type'] = 'left';
		$x++;
		*/
		/*
		$join_arr[$x]['table'] = 'coop_maco_account';
		$join_arr[$x]['condition'] = 'coop_loan_transfer.account_id = coop_maco_account.account_id';
		$join_arr[$x]['type'] = 'left';
		$x++;
		*/
		$join_arr[$x]['table'] = 'coop_user as loan_user';
		$join_arr[$x]['condition'] = 'loan_user.user_id = coop_loan.admin_id';
		$join_arr[$x]['type'] = 'left';


		$this->paginater_all->type(DB_TYPE);
		$this->paginater_all->select(array(
									//'coop_loan.*',
									'coop_loan.member_id',
									'coop_loan.createdatetime',
									'coop_loan.id AS loan_id',
									'coop_loan.loan_amount',
									//'coop_mem_apply.*',
									//'coop_loan_name.loan_name as loan_type',
									//'coop_loan_transfer.id as transfer_id',
									//'coop_maco_account.account_name',
									'loan_user.user_name',
									//coop_loan_transfer.transfer_status'
									));
		$this->paginater_all->main_table('coop_loan');
		//$this->paginater_all->where("coop_loan.loan_status = '1' AND coop_loan_transfer.transfer_status IS NULL ");
		$this->paginater_all->where("coop_loan.loan_status = '1' AND coop_loan.transfer_status IS NULL ");
		$this->paginater_all->page_now(@$_GET["page"]);
		$this->paginater_all->per_page(20);
		$this->paginater_all->page_link_limit(20);
		$this->paginater_all->order_by('coop_loan.createdatetime DESC');
		$this->paginater_all->join_arr($join_arr);
		$row = $this->paginater_all->paginater_process();
		//echo"<pre>";print_r($row);exit;
		$paging = $this->pagination_center->paginating($row['page'], $row['num_rows'], $row['per_page'], $row['page_link_limit']);//$page_now = 1, $row_total = 1, $per_page = 20, $page_limit = 20
		$i = $row['page_start'];
		//echo $this->db->last_query(); exit;
		foreach($row['data'] AS $key=>$value){
			$this->db->select('coop_mem_apply.firstname_th, coop_mem_apply.lastname_th');
			$this->db->from("coop_mem_apply");
			$this->db->where("member_id = '".@$value['member_id']."'");
			$rs_member = $this->db->get()->result_array();
			if(@$_GET['debug']){
				echo $this->db->last_query()."<br>";
			}
			$row_member = @$rs_member[0];
			$row['data'][$key]['firstname_th'] = $row_member['firstname_th'];
			$row['data'][$key]['lastname_th'] = $row_member['lastname_th'];
		}
//echo"<pre>";print_r($row['data']);exit;
		$arr_data['num_rows'] = $row['num_rows'];
		$arr_data['paging'] = $paging;
		$arr_data['data'] = $row['data'];
		$arr_data['i'] = $i;

		$this->db->select(array(
			'*'
		));
		$this->db->from('coop_bank');
		$rs_bank = $this->db->get()->result_array();
		$arr_data['rs_bank'] = $rs_bank;

		$loan_type = array();
		$this->db->select('*');
		$this->db->from("coop_loan_type");
		$rs_type = $this->db->get()->result_array();
		foreach($rs_type as $key => $row_type){
			$loan_type[$row_type['id']] = $row_type['loan_type'];
		}
		$arr_data['loan_type'] = $loan_type;

		$this->libraries->template('loan/loan_transfer',$arr_data);
	}

	function loan_transfer_save(){
	  $amount_transfer = @str_replace(',','',@$_POST['amount_transfer']);
	  $loan_amount_transfer = @str_replace(',','',@$_POST['loan_amount']);

	  $this->db->select(array(
	   'coop_loan.loan_amount',
	   'coop_loan.loan_type',
	   'coop_loan.member_id',
	   'coop_loan_name.loan_type_id'
	  ));
	  $this->db->from('coop_loan');
	  $this->db->join('coop_loan_name','coop_loan.loan_type = coop_loan_name.loan_name_id','inner');
	  $this->db->where("coop_loan.id = '".@$_POST['loan_id']."'");
	  $row_loan = $this->db->get()->result_array();
	  $row_loan = $row_loan[0];
	  $loan_amount = @$row_loan['loan_amount'];
	  $member_id = @$row_loan['member_id'];

	  $date_arr = explode('/',@$_POST['date_transfer']);
	  $date_transfer = ($date_arr[2]-543)."-".$date_arr[1]."-".$date_arr[0]." ".@$_POST['time_transfer'];

	  $this->db->select(array(
	   'transaction_balance'
	  ));
	  $this->db->from('coop_account_transaction');
	  $this->db->where("account_id = '".$_POST['account_id']."'");
	  $this->db->order_by('transaction_time DESC, transaction_id DESC');
	  $this->db->limit(1);
	  $row_prev_trans = $this->db->get()->result_array();
	  $row_prev_trans = $row_prev_trans[0];

	  $transaction_balance = $row_prev_trans['transaction_balance'] + @$amount_transfer;

	 //start เปิดหบัญชีหลักประกันเงินกู้
		$this->db->select(array('*'));
		$this->db->from('coop_loan_deduct');
		$this->db->where("loan_id = '".@$_POST['loan_id']."'  AND loan_deduct_list_code = 'deduct_person_guarantee'");
		$rs_deduct_person_guarantee = $this->db->get()->result_array();
		$row_deduct_person_guarantee = @$rs_deduct_person_guarantee[0];
		$loan_deduct_amount = @$row_deduct_person_guarantee['loan_deduct_amount'];


		if(@$loan_deduct_amount>0){
			$this->db->select(array('t1.type_id','t1.type_code','t2.loan_type'));
			$this->db->from('coop_deposit_type_setting AS t1');
			$this->db->join('coop_deduct_guarantee_loan_type AS t2','t1.deduct_guarantee_id = t2.deduct_guarantee_id','left');
			$this->db->where("t2.loan_type = '".@$row_loan['loan_type_id']."'");
			$this->db->limit(1);
			$rs_deposit_setting = $this->db->get()->result_array();
			$row_deposit_setting = @$rs_deposit_setting[0];

			//เช็คสมุดเงินฝาก
			$this->db->select(array('coop_maco_account.account_id','coop_maco_account.mem_id'));
			$this->db->from('coop_maco_account');
			$this->db->where("coop_maco_account.mem_id = '".@$member_id."' AND type_id = '".@$row_deposit_setting['type_id']."' AND account_status = '0'");
			$this->db->limit(1);
			$rs_account = $this->db->get()->result_array();
			$row_account = @$rs_account[0];
			//print_r($this->db->last_query());
			//echo '<pre>'; print_r($row_account); echo '</pre>';
			if(empty($rs_account)){
				$this->db->select('account_id');
				$this->db->from('coop_maco_account');
				$this->db->where("type_id = '".$row_deposit_setting['type_id']."' AND account_status = '0'");
				$this->db->order_by("account_id DESC");
				$this->db->limit(1);
				$row = $this->db->get()->result_array();
				if(!empty($row)){
					$auto_account_id = str_replace("001".@$row_deposit_setting['type_code'],'',@$row[0]['account_id']);
					$auto_account_id = (int)$auto_account_id;
					$auto_account_id = @$auto_account_id + 1;
				}else{
					$auto_account_id = 1;
				}

				$acc_id = "001".@$row_deposit_setting['type_code'].sprintf("%06d",@$auto_account_id);
				$account_id = @$acc_id;
				$this->db->select('*');
				$this->db->from('coop_mem_apply');
				$this->db->where("member_id = '".@$member_id."'");
				$rs_member = $this->db->get()->result_array();
				$row_member = @$rs_member[0];

				//start เช็คบัญชีในตาราง coop_account_transaction
				$this->db->select('account_id');
				$this->db->from('coop_account_transaction');
				$this->db->where("account_id = '".$account_id."'");
				$this->db->order_by("transaction_time DESC, transaction_id DESC");
				$this->db->limit(1);
				$check_account_transaction = $this->db->get()->result_array(); 
				if(!empty($check_account_transaction)){
					//หา account_id อีกรอบ
					$this->db->select('account_id');
					$this->db->from('coop_account_transaction');
					$this->db->where("account_id LIKE '001".$row_deduct_loan['type_code']."%'");
					$this->db->order_by("transaction_time DESC, transaction_id DESC");
					$this->db->limit(1);
					$last_account_id = $this->db->get()->result_array();
					$auto_account_id = str_replace("001".$row_deduct_loan['type_code'],'',$last_account_id[0]['account_id']);
					$auto_account_id = (int)$auto_account_id;
					$auto_account_id = $auto_account_id + 1;
					$account_id = "001".$row_deduct_loan['type_code'].sprintf("%06d",@$auto_account_id);
				} 
				//end เช็คบัญชีในตาราง coop_account_transaction
							
				$data_insert = array();
				$data_insert['account_id'] = @$account_id;
				$data_insert['mem_id'] = @$member_id;
				$data_insert['member_name'] = @$row_member['firstname_th']." ".@$row_member['lastname_th'];
				$data_insert['account_name'] = @$row_member['firstname_th']." ".@$row_member['lastname_th'];
				$data_insert['created'] = date('Y-m-d H:i:s');
				$data_insert['account_amount'] = '0';
				$data_insert['book_number'] = '1';
				$data_insert['type_id'] = @$row_deposit_setting['type_id'];
				$data_insert['account_status'] = '0';
				$data_insert['sequester_status'] = '1';
				$this->db->insert('coop_maco_account', $data_insert);
				//$account_id = @$acc_id;
			}else{
				$account_id = @$row_account['account_id'];
			}

			if(@$account_id != ''){
				$transaction_list = 'XD';
				$transaction_deposit = @$loan_deduct_amount;

				$this->db->select('*');
				$this->db->from('coop_account_transaction');
				$this->db->where("account_id = '".$account_id."'");
				$this->db->order_by('transaction_time DESC, transaction_id DESC');
				$this->db->limit(1);
				$row = $this->db->get()->result_array();
				if(!empty($row)){
					$balance = @$row[0]['transaction_balance'];
					$balance_no_in = @$row[0]['transaction_no_in_balance'];
				}else{
					$balance = 0;
					$balance_no_in = 0;
				}

				$sum = @$balance + @$transaction_deposit;
				$sum_no_in = @$balance_no_in + @$transaction_deposit;

				$data_insert = array();
				$data_insert['transaction_time'] = date('Y-m-d H:i:s');
				$data_insert['transaction_list'] = @$transaction_list;
				$data_insert['transaction_withdrawal'] = '';
				$data_insert['transaction_deposit'] = @$transaction_deposit;
				$data_insert['transaction_balance'] = @$sum;
				$data_insert['transaction_no_in_balance'] = @$sum_no_in;
				$data_insert['user_id'] = @$_SESSION['USER_ID'];
				$data_insert['account_id'] = @$account_id;
				$data_insert['loan_id'] = @$_POST['loan_id'];
				$this->db->insert('coop_account_transaction', $data_insert);
				//echo '<pre>'; print_r($data_insert); echo '</pre>';
			}

		}
		//exit;
	  //end เปิดหบัญชีหลักประกันเงินกู้

	  if(@$_POST['pay_type'] == '1'){
		  $data_insert = array();
		  $data_insert['transaction_time'] = $date_transfer;
		  $data_insert['transaction_list'] = 'XD';
		  $data_insert['transaction_withdrawal'] = '0';
		  $data_insert['transaction_deposit'] = @$amount_transfer;
		  $data_insert['transaction_balance'] = $transaction_balance;
		  $data_insert['user_id'] = $_SESSION['USER_ID'];
		  $data_insert['account_id'] = $_POST['account_id'];
		  $this->db->insert('coop_account_transaction', $data_insert);
	  }

	  $data_insert = array();
	  $data_insert['loan_id'] = $_POST['loan_id'];
	  $data_insert['account_id'] = $_POST['account_id'];
	  $data_insert['date_transfer'] = $date_transfer;
	  $data_insert['createdatetime'] = date('Y-m-d H:i:s');
	  $data_insert['admin_id'] = $_SESSION['USER_ID'];
	  $data_insert['transfer_status'] = '0';
	  $data_insert['amount_transfer'] = @$amount_transfer;
	  $data_insert['pay_type'] = @$_POST['pay_type'];
	  if(@$_POST['pay_type'] == '2'){
	   $data_insert['dividend_bank_id'] = @$_POST['dividend_bank_id'];
	   //$data_insert['dividend_bank_branch_id'] = @$_POST['dividend_bank_branch_id'];
	   $data_insert['dividend_acc_num'] = @$_POST['dividend_acc_num'];
	  }
	  $this->db->insert('coop_loan_transfer', $data_insert);

	$loan_transaction = array();
	$loan_transaction['loan_id'] = $_POST['loan_id'];
	$loan_transaction['loan_amount_balance'] = $loan_amount;
	$loan_transaction['transaction_datetime'] = date('Y-m-d H:i:s');
	$this->loan_libraries->loan_transaction($loan_transaction);

	  $last_id = $this->db->insert_id();
	  if(@$_FILES){
	   $output_dir = $_SERVER["DOCUMENT_ROOT"].PROJECTPATH."/assets/uploads/loan_transfer_attach/";

	   if($_FILES['file_attach']['name']!=''){
		$new_file_name = $this->center_function->create_file_name($output_dir,$_FILES['file_attach']['name']);
		@copy($_FILES["file_attach"]["tmp_name"],$output_dir.$new_file_name);

		$data_insert = array();
		$data_insert['file_name'] = $new_file_name;
		$this->db->where('id', $last_id);
		$this->db->update('coop_loan_transfer', $data_insert);
	   }
	  }

	  $this->db->select(array(
	   't1.account_chart_id',
	   't2.account_chart'
	  ));
	  $this->db->from('coop_account_match as t1');
	  $this->db->join('coop_account_chart as t2','t1.account_chart_id = t2.account_chart_id','left');
	  $this->db->where("
	   t1.match_type = 'loan'
	   AND t1.match_id = '".$row_loan['loan_type']."'
	  ");
	  $row_account_match = $this->db->get()->result_array();
	  $row_account_match = @$row_account_match[0];

	  $account_id_transfer = '';
	  if(!empty($_POST['account_id'])){
		$account_id_transfer = $_POST['account_id'];
	  }else if(!empty($_POST['dividend_acc_num'])){
		$account_id_transfer = $_POST['dividend_acc_num'];
	  }else{
		$account_id_transfer = '';
	  }

	  
	  $data = array();
	  $data['coop_account']['account_description'] = "โอนเงินให้".$row_account_match['account_chart'];
	  $data['coop_account']['account_datetime'] = $date_transfer;
	  $data['coop_account']['account_number'] = $account_id_transfer;
	  $data['coop_account']['ref'] = $_POST['loan_id'];
	  $data['coop_account']['ref_type'] = 'loan';
	  $data['coop_account']['process'] = 'loan_transfer';

	  $i=0;
	  if(@$_POST['pay_type'] == '0'){
		$data['coop_account_detail'][$i]['account_type'] = 'credit';
		$data['coop_account_detail'][$i]['account_amount'] = @$loan_amount_transfer;
		$data['coop_account_detail'][$i]['account_chart_id'] = '10101001';
	  }else{
		$data['coop_account_detail'][$i]['account_type'] = 'debit';
		$data['coop_account_detail'][$i]['account_amount'] = @$loan_amount_transfer;
		$data['coop_account_detail'][$i]['account_chart_id'] = '20105014';
	  }



	  if(@$_POST['pay_type'] == '0'){
			$i++;
			$data['coop_account_detail'][$i]['account_type'] = 'debit';
			$data['coop_account_detail'][$i]['account_amount'] = @$loan_amount_transfer;
			$data['coop_account_detail'][$i]['account_chart_id'] = $row_account_match['account_chart_id'];
	  }else{
			$i++;
			$data['coop_account_detail'][$i]['account_type'] = 'credit';
			$data['coop_account_detail'][$i]['account_amount'] = @$loan_amount_transfer;
			$data['coop_account_detail'][$i]['account_chart_id'] = $row_account_match['account_chart_id'];
	  }
	  
	  $this->account_transaction->account_process($data);

	  $data_insert = array();
	  $data_insert['transfer_status'] = '0';
	  $this->db->where('id', @$_POST['loan_id']);
	  $this->db->update('coop_loan', $data_insert);
	  //exit;
	  $this->center_function->toast("บันทึกข้อมูลเรียบร้อยแล้ว");
	  echo "<script> document.location.href='".base_url(PROJECTPATH.'/loan/loan_transfer?loan_id='.$_POST['loan_id'])."' </script>";
	  exit;
	}

	function loan_transfer_cancel(){
		if ($_GET) {
			$data_insert = array();
			$data_insert['transfer_status'] = $_GET['status_to'];
			$this->db->where('id', $_GET['transfer_id']);
			$this->db->update('coop_loan_transfer', $data_insert);

			$this->center_function->toast('บันทึกข้อมูลเรียบร้อยแล้ว');
			echo "<script> document.location.href='".base_url(PROJECTPATH.'/loan/loan_transfer_cancel')."' </script>";
		}
		$arr_data = array();

		$x=0;
		$join_arr = array();
		$join_arr[$x]['table'] = 'coop_loan';
		$join_arr[$x]['condition'] = 'coop_loan_transfer.loan_id = coop_loan.id';
		$join_arr[$x]['type'] = 'left';
		$x++;
		$join_arr[$x]['table'] = 'coop_mem_apply';
		$join_arr[$x]['condition'] = 'coop_mem_apply.member_id = coop_loan.member_id';
		$join_arr[$x]['type'] = 'left';
		$x++;
		$join_arr[$x]['table'] = 'coop_user';
		$join_arr[$x]['condition'] = 'coop_loan.admin_id = coop_user.user_id';
		$join_arr[$x]['type'] = 'left';

		$this->paginater_all->type(DB_TYPE);
		$this->paginater_all->select('coop_loan_transfer.id as transfer_id,
				 coop_loan_transfer.cancel_date,
				 coop_loan.contract_number,
				 coop_loan.loan_amount,
				 coop_loan_transfer.date_transfer,
				 coop_loan_transfer.admin_id,
				 coop_loan_transfer.transfer_status,
				 coop_user.user_name,
				 coop_mem_apply.firstname_th,
				 coop_mem_apply.lastname_th');
		$this->paginater_all->main_table('coop_loan_transfer');
		$this->paginater_all->where("coop_loan.transfer_status IN('1','2')");
		$this->paginater_all->page_now(@$_GET["page"]);
		$this->paginater_all->per_page(20);
		$this->paginater_all->page_link_limit(20);
		$this->paginater_all->order_by('cancel_date DESC');
		$this->paginater_all->join_arr($join_arr);
		$row = $this->paginater_all->paginater_process();
		//echo"<pre>";print_r($row);exit;
		$paging = $this->pagination_center->paginating($row['page'], $row['num_rows'], $row['per_page'], $row['page_link_limit']);//$page_now = 1, $row_total = 1, $per_page = 20, $page_limit = 20
		$i = $row['page_start'];


		$arr_data['num_rows'] = $row['num_rows'];
		$arr_data['paging'] = $paging;
		$arr_data['data'] = $row['data'];
		$arr_data['i'] = $i;

		$loan_type = array();
		$this->db->select('*');
		$this->db->from("coop_loan_type");
		$rs_type = $this->db->get()->result_array();
		foreach($rs_type as $key => $row_type){
			$loan_type[$row_type['id']] = $row_type['loan_type'];
		}
		$arr_data['loan_type'] = $loan_type;

		$this->libraries->template('loan/loan_transfer_cancel',$arr_data);
	}

	function ajax_check_term_of_loan_before(){
		$arr_return = array();
		$loan_type = $_POST['loan_type'];
		$share_total = str_replace(',','',@$_POST['share_total']);
		$member_id = $_POST['member_id'];

		$this->db->select('*');
		$this->db->from("coop_mem_apply");
		$this->db->where("member_id = '".$member_id."'");
		$row_member = $this->db->get()->result_array();
		$row_member = $row_member[0];

			if($row_member['apply_date']!='0000-00-00'){
				$apply_date = $row_member['apply_date'];
			}else{
				$apply_date = date("Y-m-d");
			}
			$today = date("Y-m-d");   //จุดต้องเปลี่ยน
			list($byear, $bmonth, $bday)= explode("-",$apply_date);       //จุดต้องเปลี่ยน
			list($tyear, $tmonth, $tday)= explode("-",$today);                //จุดต้องเปลี่ยน
			$mbirthday = mktime(0, 0, 0, $bmonth, $bday, $byear);
			$mnow = mktime(0, 0, 0, $tmonth, $tday, $tyear );
			$mage = ($mnow - $mbirthday);
			//echo "วันเกิด $birthday"."<br>\n";
			//echo "วันที่ปัจจุบัน $today"."<br>\n";
			//echo "รับค่า $mage"."<br>\n";
			$u_y=date("Y", $mage)-1970;
			$u_m=date("m",$mage)-1;
			$u_d=date("d",$mage)-1;

			$month_count = ($u_y*12)+$u_m;

		$member_age = $this->center_function->cal_age($row_member['birthday']);
			//echo $month_count;

		$this->db->select('*');
		$this->db->from("coop_share_setting");
		$this->db->where("setting_id = '1'");
		$row_share_setting = $this->db->get()->result_array();
		$row_share_setting = $row_share_setting[0];

		$this->db->select('*');
		$this->db->from("coop_loan");
		$this->db->where("member_id = '".$member_id."' AND loan_type = '".$loan_type."' AND loan_status IN('1','2')");
		$rs_prev_loan = $this->db->get()->result_array();
		$prev_loan_amount = 0;
		$prev_loan_balance = 0;
		//echo $sql_prev_loan;
		foreach($rs_prev_loan as $key => $row_prev_loan){
			$prev_loan_amount += $row_prev_loan['loan_amount'];
			$prev_loan_balance += $row_prev_loan['loan_amount_balance'];
		}
		if($prev_loan_amount > 0){
			$prev_loan_percent = ($prev_loan_balance*100)/$prev_loan_amount;
		}else{
			$prev_loan_percent = 0;
		}
		//echo $prev_loan_percent;
		$share_value_total = $share_total * $row_share_setting['setting_value'];

		$this->db->select('*');
		$this->db->from("coop_term_of_loan");
		$this->db->where("type_id = '".$loan_type."' AND start_date <= '".date('Y-m-d')."'");
		$this->db->order_by('start_date DESC');
		$this->db->limit(1);
		$row_term_of_loan = $this->db->get()->result_array();
		$row_term_of_loan = @$row_term_of_loan[0];
		//print_r($this->db->last_query());
		//echo"<pre>";print_r($row_term_of_loan);exit;
		$arr_return['share_guarantee'] = $row_term_of_loan['share_guarantee'];
		$arr_return['person_guarantee'] = $row_term_of_loan['person_guarantee'];
		$arr_return['real_estate_guarantee'] = $row_term_of_loan['real_estate_guarantee'];

		$text_return = '';
		$icon_yes = "<span class='icon icon-check green'></span>";
		$icon_no = "<span class='icon icon-remove red'></span>";
		$count_error = 0;
		if(@$row_term_of_loan['min_month_member'] != ''){
			if($month_count < @$row_term_of_loan['min_month_member']){
				$icon = $icon_no;
				$count_error++;
			}else{
				$icon = $icon_yes;
			}
			$text_return .= $icon." เป็นสมาชิกสหกรณ์อย่างน้อย ".@$row_term_of_loan['min_month_member']." เดือน\n<br>";
		}
		$loan_deduct_share = '';
		if(@$row_term_of_loan['min_share_total'] != ''){
			if($share_value_total <= ($row_term_of_loan['min_share_total']*$row_share_setting['setting_value'])){
				$icon = $icon_no;
				$count_error++;
				$loan_deduct_share = ($row_term_of_loan['min_share_total']*$row_share_setting['setting_value']) - $share_value_total;
				$loan_deduct_share = ceil($loan_deduct_share/1000)*1000;
				$loan_deduct_share = number_format($loan_deduct_share,2);
			}else{
				$icon = $icon_yes;
				$loan_deduct_share = '';
			}
			$text_return .= $icon." มีหุ้นสะสมไม่น้อยกว่า ".number_format($row_term_of_loan['min_share_total'])." หุ้นเพื่อใช้ในการกู้\n<br>";
		}
		//$arr_return['debug'] = "share_value_total=".$share_value_total."|min_share_total=".$row_term_of_loan['min_share_total']."|setting_value=".$row_share_setting['setting_value'];
		$arr_return['loan_deduct_share'] = $loan_deduct_share;
		if(@$row_term_of_loan['min_installment_percent'] != ''){
			if($prev_loan_percent > $row_term_of_loan['min_installment_percent']){
				$icon = $icon_no;
				$count_error++;
			}else{
				$icon = $icon_yes;
			}
			$text_return .= $icon." ท่านต้องผ่อนชำระเงินกู้เดิมไม่ต่ำกว่า ".$row_term_of_loan['min_installment_percent']." % จึงจะสามารถกู้ใหม่ได้\n<br>";
		}
		if(@$row_term_of_loan['age_limit'] != ''){
			if($member_age > $row_term_of_loan['age_limit']){
				$icon = $icon_no;
				$count_error++;
			}else{
				$icon = $icon_yes;
			}
			$text_return .= $icon." อายุไม่เกิน ".$row_term_of_loan['age_limit']." ปี\n<br>";
		}

		$this->db->select('t2.loan_type_code');
		$this->db->from('coop_loan_name as t1');
		$this->db->join('coop_loan_type as t2','t1.loan_type_id = t2.id','inner');
		$this->db->where("t1.loan_name_id = '".$loan_type."'");
		$row = $this->db->get()->result_array();
		$loan_type_code = @$row[0]['loan_type_code'];

		$prev_loan_amount_balance = 0;

		if($loan_type_code == 'emergent'){
			$this->db->select(array('t1.*'));
			$this->db->from("coop_loan as t1");
			$this->db->join("coop_loan_name as t2",'t1.loan_type = t2.loan_name_id','inner');
			$this->db->join("coop_loan_type as t3",'t2.loan_type_id = t3.id','inner');
			$this->db->where("
			t3.loan_type_code = '".@$loan_type_code."'
			AND t1.loan_status = '1'
			AND t1.member_id = '".$member_id."'");
			$row = $this->db->get()->result_array();
			foreach($row as $key => $value){
				$prev_loan_amount_balance += $value['loan_amount_balance'];

				$cal_loan_interest = array();
				$cal_loan_interest['loan_id'] = $value['id'];
				$cal_loan_interest['date_interesting'] = date('Y-m-d');
				$interest_loan = $this->loan_libraries->cal_loan_interest($cal_loan_interest);
				$prev_loan_amount_balance += $interest_loan;
			}

			$this->db->select(array('t1.loan_amount_balance','t1.loan_atm_id'));
			$this->db->from('coop_loan_atm_detail as t1');
			$this->db->join('coop_loan_atm as t2','t1.loan_atm_id = t2.loan_atm_id','inner');
			$this->db->where("
			t1.loan_status = '0'
			AND t2.member_id = '".$member_id."'
			AND t2.loan_atm_status = '1'");
			$prev_loan = $this->db->get()->result_array();
			foreach($prev_loan as $key => $value){
				$loan_atm_id = $value['loan_atm_id'];
				$prev_loan_amount_balance += $value['loan_amount_balance'];
			}
			$cal_loan_interest = array();
			$cal_loan_interest['loan_atm_id'] = @$loan_atm_id;
			$cal_loan_interest['date_interesting'] = date('Y-m-d');
			//อันเดิม
			//$interest_atm = $this->loan_libraries->cal_atm_interest($cal_loan_interest);
			//ดอกเบี้ยเงินกู้ตามช่วงเวลาที่มีการทำรายการ
			$interest_atm = $this->loan_libraries->cal_atm_interest_transaction($cal_loan_interest);

			//รายการที่มีการผ่านรายการแล้ว
			$total_atm_after_process = $this->loan_libraries->cal_atm_after_process($cal_loan_interest);

			$prev_loan_amount_balance += $interest_atm;
		}else{
			$this->db->select(array('id','loan_amount_balance'));
			$this->db->from('coop_loan');
			$this->db->where("member_id = '".$member_id."' AND loan_type = '".$loan_type."' AND loan_status = '1'");
			$prev_loan = $this->db->get()->result_array();
			foreach($prev_loan as $key => $value){
				$prev_loan_amount_balance += $value['loan_amount_balance'];

				$cal_loan_interest = array();
				$cal_loan_interest['loan_id'] = $value['id'];
				$cal_loan_interest['date_interesting'] = date('Y-m-d');
				$interest_loan = $this->loan_libraries->cal_loan_interest($cal_loan_interest);
				$prev_loan_amount_balance += $interest_loan;
			}
		}

		$arr_return['prev_loan_amount_balance'] = number_format($prev_loan_amount_balance);

		if($count_error > 0){
			$arr_return['result'] = 'error';
			$arr_return['text_return'] = $text_return;
		}else{
			$arr_return['result'] = 'success';
		}

		echo json_encode($arr_return);

		exit;
	}
	function ajax_calculate_loan(){
		$arr_data = array();
		$this->load->view('loan/ajax_calculate_loan',$arr_data);
	}

	function ajax_check_term_of_loan(){
		$arr_return = array();

		$member_id = @$_POST['member_id'];
		$loan_type = @$_POST['loan_type'];
		$loan_amount = str_replace(',','',@$_POST['loan_amount']);
		$share_total = str_replace(',','',@$_POST['share_total']);
		$share_amount = str_replace(',','',@$_POST['share_amount']);
		$period_amount = @$_POST['period_amount'];
		$person_guarantee = @$_POST['person_guarantee'];
		$share_guarantee = @$_POST['share_guarantee'];
		$fund_total = str_replace(',','',@$_POST['fund_total']);
		$first_pay = str_replace(',','',@$_POST['first_pay']);
		$this->db->select('*');
		$this->db->from("coop_mem_apply");
		$this->db->where("member_id = '".$member_id."'");
		$rs_member = $this->db->get()->result_array();
		$row_member = @$rs_member[0];

		if(@$_POST['salary']!=''){
			$salary = str_replace(',','',@$_POST['salary']);
		}else{
			$salary = @$row_member['salary'];
		}
		$all_income = $salary + $row_member['other_income'];
		$all_income_balance = $all_income - $first_pay;

		if(@$row_member['apply_date']!='0000-00-00'){
			$birthday = @$row_member['apply_date'];
		}else{
			$birthday = date("Y-m-d");
		}
		$today = date("Y-m-d");   //จุดต้องเปลี่ยน
		list($byear, $bmonth, $bday)= explode("-",$birthday);       //จุดต้องเปลี่ยน
		list($tyear, $tmonth, $tday)= explode("-",$today);                //จุดต้องเปลี่ยน
		$mbirthday = mktime(0, 0, 0, $bmonth, $bday, $byear);
		$mnow = mktime(0, 0, 0, $tmonth, $tday, $tyear );
		$mage = ($mnow - $mbirthday);
		//echo "วันเกิด $birthday"."<br>\n";
		//echo "วันที่ปัจจุบัน $today"."<br>\n";
		//echo "รับค่า $mage"."<br>\n";
		$u_y=date("Y", $mage)-1970;
		$u_m=date("m",$mage)-1;
		$u_d=date("d",$mage)-1;

		$month_count = ($u_y*12)+$u_m;

		$this->db->select('*');
		$this->db->from("coop_term_of_loan");
		$this->db->where("type_id = '".$loan_type."' AND start_date <= '".date('Y-m-d')."'");
		$this->db->order_by('start_date DESC');
		$this->db->limit(1);
		$rs_term_of_loan = $this->db->get()->result_array();
		$row_term_of_loan = @$rs_term_of_loan[0];
		$multiple_money_limit = $salary*@$row_term_of_loan['less_than_multiple_salary'];

		if(@$row_term_of_loan['money_use_balance'] != '' && @$row_term_of_loan['money_use_balance'] > 0){
			$min_money_use_balance = ($all_income * $row_term_of_loan['money_use_balance'])/100;
		}else{
			$min_money_use_balance = '';
		}

		$this->db->select('*');
		$this->db->from("coop_share_setting");
		$this->db->where("setting_id = '1'");
		$rs_share_setting = $this->db->get()->result_array();
		$row_share_setting = @$rs_share_setting[0];

		$this->db->select('change_value');
		$this->db->from('coop_change_share');
		$this->db->where("member_id = '".$member_id."' AND (change_share_status = '1' OR change_share_status = '2')");
		$this->db->order_by('change_share_id DESC');
		$this->db->limit(1);
		$row = $this->db->get()->result_array();
		$row_change_share = @$row[0];

		if(@$row_change_share['change_value'] != ''){
			$num_share = $row_change_share['change_value'];
		}else{
			$this->db->select('share_salary');
			$this->db->from('coop_share_rule');
			$this->db->where("salary_rule <= '".$salary."'");
			$this->db->order_by('salary_rule DESC');
			$this->db->limit(1);
			$row = $this->db->get()->result_array();
			$row_share_rule = @$row[0];

			$num_share = $row_share_rule['share_salary'];
		}
		$all_income_balance = @$all_income_balance - (@$num_share * @$row_share_setting['setting_value']);

		$share_value_total = $share_total * @$row_share_setting['setting_value'];
		//print_r($this->db->last_query());exit;
		$credit_limit_arr = array('0'=>'0');
		if(@$row_term_of_loan['credit_limit_share_percent']!='' && @$row_term_of_loan['credit_limit_share_percent'] > 0){
			$percent_share_value_total = ($share_value_total*@$row_term_of_loan['credit_limit_share_percent'])/100;
			$percent_fund_value_total = ($fund_total*@$row_term_of_loan['credit_limit_share_percent'])/100;
			$credit_limit_arr[] = $percent_share_value_total+$percent_fund_value_total;
		}
		if($multiple_money_limit > 0 && @$row_term_of_loan['less_than_multiple_salary']!=''){
			$credit_limit_arr[] = $multiple_money_limit;
		}
		if(@$row_term_of_loan['credit_limit'] > 0 && @$row_term_of_loan['credit_limit'] != ''){
			$credit_limit_arr[] = @$row_term_of_loan['credit_limit'];
		}
		if(@$row_term_of_loan['percent_share_guarantee']!='' && @$row_term_of_loan['percent_share_guarantee'] > 0){
			$share_use_limit = ($share_value_total * @$row_term_of_loan['percent_share_guarantee'])/100;
		}
		if(@$row_term_of_loan['percent_fund_quarantee']!='' && @$row_term_of_loan['percent_fund_quarantee'] > 0){
			$fund_use_limit = ($fund_total * @$row_term_of_loan['percent_fund_quarantee'])/100;
		}
		if($share_guarantee == '1' && (@$share_use_limit+@$fund_use_limit) > 0){
			$credit_limit_arr[] = $share_use_limit+@$fund_use_limit;
		}
		$credit_limit = min(@$credit_limit_arr);

		if($person_guarantee=='1'){
			$least_share_for_loan = (@$loan_amount * @$row_term_of_loan['least_share_percent_for_loan'])/100;
		}

		$this->db->select(array('profile_id','retire_age','retire_month'));
		$this->db->from("coop_profile");
		$row_retire_age = $this->db->get()->result_array();
		$row_retire_age = @$row_retire_age[0];

		$retire_date = date('Y-m-t',strtotime('+'.$row_retire_age['retire_age'].' year',strtotime($row_member['birthday'])));
		$retire_date_arr = explode('-',$retire_date);
		$retire_date = $retire_date_arr[0].'-'.sprintf('%02d',$row_retire_age['retire_month']).'-'.$retire_date_arr[2];

		$text_return = '';
		$icon_yes = "<span class='icon icon-check green'></span>";
		$icon_no = "<span class='icon icon-remove red'></span>";
		$count_error = 0;
		/*if(@$row_term_of_loan['min_month_member'] != ''){
			 if($month_count < @$row_term_of_loan['min_month_member']){
				 $icon = $icon_no;
			 }else{
				 $icon = $icon_yes;
			 }
			 $text_return .= $icon." เป็นสมาชิกสหกรณ์อย่างน้อย ".@$row_term_of_loan['min_month_member']." เดือน\n<br>";
		}*/
		if($credit_limit > 0){
			 if($loan_amount > $credit_limit){
				$icon = $icon_no;
				$count_error++;
			 }else{
				 $icon = $icon_yes;
			 }
			$text_return .= $icon." วงเงินที่สามารถกู้ได้ ".number_format($credit_limit)." บาท\n<br>";
		}
		if(@$row_term_of_loan['max_period'] != ''){
			 if($period_amount > @$row_term_of_loan['max_period']){
				 $icon = $icon_no;
				 $count_error++;
			 }else{
				 $icon = $icon_yes;
			 }
			 $text_return .= $icon." ผ่อนชำระไม่เกิน ".$row_term_of_loan['max_period']." งวด\n<br>";
		}
		if(@$row_term_of_loan['least_share_percent_for_loan'] > 0 && $person_guarantee=='1'){
			 if($least_share_for_loan > $share_value_total){
				 $icon = $icon_no;
				 $count_error++;
			 }else{
				 $icon = $icon_yes;
			 }
			 $text_return .= $icon." มีมูลค่าหุ้นสะสมไม่น้อยกว่า ".@$row_term_of_loan['least_share_percent_for_loan']." % ของวงเงินกู้ กรณีใช้บุคคลค้ำประกัน\n<br>";
		}
		if(@$row_term_of_loan['min_share_fund_money'] > 0){
			 if(($share_value_total+$fund_total) < @$row_term_of_loan['min_share_fund_money']){
				  $icon = $icon_no;
				  $count_error++;
			 }else{
				 $icon = $icon_yes;
			 }
			 $text_return .= $icon." มีหุ้นสะสมและกองทุนสำรองเลี้ยงชีพรวมมากกว่า ".number_format(@$row_term_of_loan['min_share_fund_money'])." บาท\n<br>";
		}
		 if($_POST['last_date_period'] > $retire_date){
			  $icon = $icon_no;
			  $count_error++;
		 }else{
			 $icon = $icon_yes;
		 }
		 //$text_return .= "ท่านไม่สามารถผ่อนชำระจำนวน ".$period_amount." งวดได้เนื่องจากเกินกำหนดเกษียณ\n";
		 $text_return .= $icon." จำนวนงวดผ่อนชำระไม่เกินกำหนดเกษียณ\n<br>";
		 if($min_money_use_balance != ''){
			 if($all_income_balance < $min_money_use_balance){
				  $icon = $icon_no;
				  $count_error++;
			 }else{
				 $icon = $icon_yes;
			 }
			 $text_return .= $icon." รายได้คงเหลือมากกว่า ".$row_term_of_loan['money_use_balance']." %\n<br>";
		 }

		if($count_error > 0){
			 $arr_return['result'] = 'error';
			 $arr_return['text_return'] = $text_return;
		 }else{
			 $arr_return['result'] = 'success';
		 }

		echo json_encode($arr_return);

		exit;
	}

	function coop_loan_save(){
		//echo '<pre>'; print_r($_POST); echo '</pre>';exit;
		if(@$_POST['loan_id']==''){
			$data_insert = array();
			$data_insert['admin_id'] = @$_SESSION['USER_ID'];
			$data_insert['createdatetime'] = date('Y-m-d H:i:s');
			$data_insert['updatetimestamp'] = date('Y-m-d H:i:s');
			//$data_insert['contract_number'] = @$new_contact_number;
			$data_insert['contract_number'] = '';

			foreach(@$_POST['data']['coop_loan'] as $key => $value){
				if($key == 'date_period_1' || $key == 'date_period_2'){
					if(!empty($value)){
						$date_arr = explode('/',$value);
						$value = ($date_arr[2]-543)."-".$date_arr[1]."-".$date_arr[0];
					}
				}
				if($key == 'loan_amount' || $key == 'money_period_1' || $key == 'money_period_2' || $key == 'salary'){
					$value = str_replace(',','',@$value);
				}
				if($key == 'petition_number'){
					$this->db->select('petition_number');
					$this->db->from("coop_loan");
					$this->db->order_by("id DESC");
					$this->db->limit(1);
					$rs_petition_number = $this->db->get()->result_array();
					$row_petition_number = @$rs_petition_number[0];
					$petition_number = (int)@$row_petition_number['petition_number']+1;
					$value = sprintf('% 06d',@$petition_number);
				}
				$data_insert[$key] = @$value;
				if($key == 'loan_amount'){
					$data_insert['loan_amount_balance'] = @$value;
				}
				$data_insert['loan_status'] = '0';
				//echo '<pre>'; print_r($value); echo '<pre>';
			}
			//echo '<pre>'; print_r($data_insert); echo '<pre>';
			//exit;
			//add
			$this->db->insert('coop_loan', $data_insert);

			$loan_id = $this->db->insert_id();

			foreach(@$_POST['data']['coop_loan_guarantee'] as $key => $value){
				if(@$value['guarantee_type']!=''){
					$data_insert = array();
					$data_insert['loan_id'] = @$loan_id;
					$data_insert['guarantee_type'] = @$value['guarantee_type'];
					if($value['guarantee_type']=='1'){
						foreach($value['coop_loan_guarantee_person']['id'] as $key2 => $value2){
							$data_insert_person = array();
							$data_insert_person['loan_id'] = @$loan_id;
							$data_insert_person['guarantee_person_id'] = @$value2;
							$data_insert_person['guarantee_person_contract_number'] = @$value['coop_loan_guarantee_person']['guarantee_person_contract_number'][@$key2];
							$data_insert_person['guarantee_person_amount'] = str_replace(',','',@$value['coop_loan_guarantee_person']['guarantee_person_amount'][@$key2]);
							$data_insert_person['guarantee_person_amount_balance'] = str_replace(',','',@$value['coop_loan_guarantee_person']['guarantee_person_amount'][@$key2]);
							$this->db->insert('coop_loan_guarantee_person', $data_insert_person);
						//echo $sql_person."<br>";
						}
						//echo "_________________________<br>";
					}else if($value['guarantee_type']=='2'){
						if(isset($value['amount'])){
							$data_insert['amount'] = str_replace(',','',@$value['amount']);
						}
						if(isset($value['price'])){
							$data_insert['price'] = str_replace(',','',@$value['price']);
						}
						if(isset($value['other_price'])){
							$data_insert['other_price'] = str_replace(',','',@$value['other_price']);
						}
					}else if($value['guarantee_type']=='3'){
						$data_insert_real_estate = array();
						$data_insert_real_estate['loan_id'] = @$loan_id;
						$data_insert_real_estate['real_estate_position_1'] = @$_POST['data']['coop_loan_guarantee_real_estate']['real_estate_position_1'];
						$data_insert_real_estate['real_estate_position_2'] = @$_POST['data']['coop_loan_guarantee_real_estate']['real_estate_position_2'];
						$data_insert_real_estate['province_id'] = @$_POST['province_id'];
						$data_insert_real_estate['amphur_id'] = @$_POST['amphur_id'];
						$data_insert_real_estate['district_id'] = @$_POST['district_id'];
						$data_insert_real_estate['land_number'] = @$_POST['data']['coop_loan_guarantee_real_estate']['land_number'];
						$data_insert_real_estate['survey_page'] = @$_POST['data']['coop_loan_guarantee_real_estate']['survey_page'];
						$data_insert_real_estate['deed_number'] = @$_POST['data']['coop_loan_guarantee_real_estate']['deed_number'];
						$data_insert_real_estate['deed_book'] = @$_POST['data']['coop_loan_guarantee_real_estate']['deed_book'];
						$data_insert_real_estate['deed_page'] = @$_POST['data']['coop_loan_guarantee_real_estate']['deed_page'];
						$data_insert_real_estate['rai'] = @$_POST['data']['coop_loan_guarantee_real_estate']['rai'];
						$data_insert_real_estate['ngan'] = @$_POST['data']['coop_loan_guarantee_real_estate']['ngan'];
						$data_insert_real_estate['tarangwah'] = @$_POST['data']['coop_loan_guarantee_real_estate']['tarangwah'];
						$this->db->insert('coop_loan_guarantee_real_estate', $data_insert_real_estate);
					}
					//add coop_loan_guarantee
					$this->db->insert('coop_loan_guarantee', $data_insert);
				}
				//echo $sql."<br>";
			}
			//echo "_________________________<br>";
			foreach(@$_POST['data']['coop_loan_period'] as $key => $value){
				$data_insert = array();
				$data_insert['loan_id'] = @$loan_id;
				foreach($value as $key2 => $value2){
					//$sql .= " ".$key2." = '".$value2."',";
					$data_insert[$key2] = @$value2;
				}
				//add coop_loan_period
				$this->db->insert('coop_loan_period', $data_insert);
			}

		}else{
			$data_insert = array();
			//$data_insert['admin_id'] = @$_SESSION['USER_ID'];
			if(@$_POST['updatetimestamp'] != ''){
				$data_insert['updatetimestamp'] = @$_POST['updatetimestamp'];
			}
			foreach(@$_POST['data']['coop_loan'] as $key => $value){
				if($key == 'date_period_1' || $key == 'date_period_2'){
					if(!empty($value)){
						$date_arr = explode('/',$value);
						$value = ($date_arr[2]-543)."-".$date_arr[1]."-".$date_arr[0];
					}
				}
				if($key == 'loan_amount' || $key == 'money_period_1' || $key == 'money_period_2' || $key == 'salary'){
					$value = str_replace(',','',@$value);
				}
				$data_insert[$key] = @$value;
				if($key == 'loan_amount'){
					$data_insert['loan_amount_balance'] = @$value;
				}
			}

			//edit coop_loan
			$this->db->where('id', @$_POST['loan_id']);
			$this->db->update('coop_loan', $data_insert);
			$loan_id = @$_POST['loan_id'];

			$this->db->where("loan_id", $loan_id );
			$this->db->delete("coop_loan_guarantee");

			$this->db->where("loan_id", $loan_id );
			$this->db->delete("coop_loan_guarantee_person");

			$this->db->where("loan_id", $loan_id );
			$this->db->delete("coop_loan_guarantee_real_estate");

			foreach(@$_POST['data']['coop_loan_guarantee'] as $key => $value){
				if(@$value['guarantee_type']!=''){
					$data_insert = array();
					$data_insert['loan_id'] = @$loan_id;
					$data_insert['guarantee_type'] = @$value['guarantee_type'];

					if(@$value['guarantee_type']=='1'){
						foreach(@$value['coop_loan_guarantee_person']['id'] as $key2 => $value2){
							$data_insert_person = array();
							$data_insert_person['loan_id'] = @$loan_id;
							$data_insert_person['guarantee_person_id'] = @$value2;
							$data_insert_person['guarantee_person_contract_number'] = @$value['coop_loan_guarantee_person']['guarantee_person_contract_number'][$key2];
							$data_insert_person['guarantee_person_amount'] = str_replace(',','',@$value['coop_loan_guarantee_person']['guarantee_person_amount'][$key2]);
							$data_insert_person['guarantee_person_amount_balance'] = str_replace(',','',@$value['coop_loan_guarantee_person']['guarantee_person_amount'][@$key2]);

							//add coop_loan_guarantee_person
							$this->db->insert('coop_loan_guarantee_person', $data_insert_person);

						}
						//echo "_________________________<br>";
					}else if($value['guarantee_type']=='2'){
						if(isset($value['amount'])){
							$data_insert['amount'] = str_replace(',','',@$value['amount']);
						}
						if(isset($value['price'])){
							$data_insert['price'] = str_replace(',','',@$value['price']);
						}
						if(isset($value['other_price'])){
							$data_insert['other_price'] = str_replace(',','',@$value['other_price']);
						}
					}else if($value['guarantee_type']=='3'){
						$data_insert_real_estate = array();
						$data_insert_real_estate['loan_id'] = @$loan_id;
						$data_insert_real_estate['real_estate_position_1'] = @$_POST['data']['coop_loan_guarantee_real_estate']['real_estate_position_1'];
						$data_insert_real_estate['real_estate_position_2'] = @$_POST['data']['coop_loan_guarantee_real_estate']['real_estate_position_2'];
						$data_insert_real_estate['province_id'] = @$_POST['province_id'];
						$data_insert_real_estate['amphur_id'] = @$_POST['amphur_id'];
						$data_insert_real_estate['district_id'] = @$_POST['district_id'];
						$data_insert_real_estate['land_number'] = @$_POST['data']['coop_loan_guarantee_real_estate']['land_number'];
						$data_insert_real_estate['survey_page'] = @$_POST['data']['coop_loan_guarantee_real_estate']['survey_page'];
						$data_insert_real_estate['deed_number'] = @$_POST['data']['coop_loan_guarantee_real_estate']['deed_number'];
						$data_insert_real_estate['deed_book'] = @$_POST['data']['coop_loan_guarantee_real_estate']['deed_book'];
						$data_insert_real_estate['deed_page'] = @$_POST['data']['coop_loan_guarantee_real_estate']['deed_page'];
						$data_insert_real_estate['rai'] = @$_POST['data']['coop_loan_guarantee_real_estate']['rai'];
						$data_insert_real_estate['ngan'] = @$_POST['data']['coop_loan_guarantee_real_estate']['ngan'];
						$data_insert_real_estate['tarangwah'] = @$_POST['data']['coop_loan_guarantee_real_estate']['tarangwah'];
						$this->db->insert('coop_loan_guarantee_real_estate', $data_insert_real_estate);
					}
					//add coop_loan_guarantee
					$this->db->insert('coop_loan_guarantee', $data_insert);
				}
			}

			$this->db->where("loan_id", $loan_id );
			$this->db->delete("coop_loan_period");
			foreach(@$_POST['data']['coop_loan_period'] as $key => $value){
				$data_insert = array();
				$data_insert['loan_id'] = @$loan_id;
				foreach($value as $key2 => $value2){
					$data_insert[$key2] = @$value2;
				}
				//add coop_loan_period
				$this->db->insert('coop_loan_period', $data_insert);
			}

		}
		$this->db->where("loan_id", $loan_id );
		$this->db->delete("coop_loan_cost");

		$data_insert = array();
		$data_insert['loan_id'] = @$loan_id;
		$data_insert['member_id'] = @$_POST['data']['coop_loan']['member_id'];
		$data_insert['school_benefits'] = str_replace(',','',@$_POST['data']['coop_loan_cost']['school_benefits']);
		$data_insert['saving'] = str_replace(',','',@$_POST['data']['coop_loan_cost']['saving']);
		$data_insert['ch_p_k'] = str_replace(',','',@$_POST['data']['coop_loan_cost']['ch_p_k']);
		$data_insert['pension'] = str_replace(',','',@$_POST['data']['coop_loan_cost']['pension']);
		$data_insert['k_b_k'] = str_replace(',','',@$_POST['data']['coop_loan_cost']['k_b_k']);
		$data_insert['other'] = str_replace(',','',@$_POST['data']['coop_loan_cost']['other']);
		$this->db->insert('coop_loan_cost', $data_insert);

		$this->db->where("loan_id", $loan_id );
		$this->db->delete("coop_loan_deduct");

		$this->db->where("loan_id", $loan_id );
		$this->db->delete("coop_loan_deduct_profile");

		$this->db->where("id", $loan_id );
		$this->db->update("coop_loan", array('deduct_status'=>'0'));

		$data_insert = array();
		$data_insert['loan_id'] = @$loan_id;
		$data_insert['estimate_receive_money'] = str_replace(',','',$_POST['data']['loan_deduct_profile']['estimate_receive_money']);
		$data_insert['pay_per_month'] = str_replace(',','',$_POST['data']['coop_loan']['money_period_1']);
		$date_receive_money = explode('/',@$_POST['data']['loan_deduct_profile']['date_receive_money']);
		$date_receive_money = (@$date_receive_money[2]-543)."-".@$date_receive_money[1]."-".@$date_receive_money[0];
		$data_insert['date_receive_money'] = $date_receive_money;
		$data_insert['date_first_period'] = $_POST['data']['loan_deduct_profile']['date_first_period'];
		$data_insert['first_interest'] = str_replace(',','',$_POST['data']['loan_deduct_profile']['first_interest']);
		$this->db->insert('coop_loan_deduct_profile', $data_insert);
		$loan_deduct_id = $this->db->insert_id();

		$member_id = @$_GET['member'];

		$deduct_amount = 0;
		foreach($_POST['data']['loan_deduct'] as $key => $value){
			$data_insert = array();
			$data_insert['loan_id'] = @$loan_id;
			$data_insert['loan_deduct_list_code'] = $key;
			$data_insert['loan_deduct_amount'] = str_replace(',','',$value);
			$data_insert['loan_deduct_id'] = @$loan_deduct_id;
			$this->db->insert('coop_loan_deduct', $data_insert);

			$deduct_amount += str_replace(',','',$value);
		}
		if($deduct_amount>0){
			$this->db->where("id", $loan_id );
			$this->db->update("coop_loan", array('deduct_status'=>'1'));
		}
		//echo"<pre>";print_r($loan_deduct_arr);exit;
		$output_dir = $_SERVER["DOCUMENT_ROOT"].PROJECTPATH."/assets/uploads/loan_attach/";
		//echo $output_dir;
		if(!@mkdir($output_dir,0,true)){
		   chmod($output_dir, 0777);
		}else{
		   chmod($output_dir, 0777);
		}
		if($_FILES['file_attach']['name'][0]!=''){
			foreach($_FILES['file_attach']['name'] as $key_file => $value_file ){
				$fileName=array();
				$list_dir = array();
					$cdir = scandir($output_dir);
					foreach ($cdir as $key => $value) {
					   if (!in_array($value,array(".",".."))) {
						  if (@is_dir(@$dir . DIRECTORY_SEPARATOR . @$value)){
							$list_dir[$value] = dirToArray(@$dir . DIRECTORY_SEPARATOR . $value);
						  }else{
							if(substr($value,0,8) == date('Ymd')){
							$list_dir[] = $value;
							}
						  }
					   }
					}
					$explode_arr=array();
					foreach($list_dir as $key => $value){
						$task = explode('.',$value);
						$task2 = explode('_',$task[0]);
						$explode_arr[] = $task2[1];
					}
					$max_run_num = sprintf("%04d",count($explode_arr)+1);
					$explode_old_file = explode('.',$_FILES["file_attach"]["name"][$key_file]);
					$new_file_name = date('Ymd')."_".$max_run_num.".".$explode_old_file[(count($explode_old_file)-1)];
				if(!is_array($_FILES["file_attach"]["name"][$key_file]))
				{
						$fileName['file_name'] = $new_file_name;
						$fileName['file_type'] = $_FILES["file_attach"]["type"][$key_file];
						$fileName['file_old_name'] = $_FILES["file_attach"]["name"][$key_file];
						$fileName['file_path'] = $output_dir.$fileName['file_name'];
						move_uploaded_file($_FILES["file_attach"]["tmp_name"][$key_file],$output_dir.$fileName['file_name']);

						$data_insert = array();
						$data_insert['loan_id'] = @$loan_id;
						$data_insert['file_name'] = @$fileName['file_name'];
						$data_insert['file_type'] = @$fileName['file_type'];
						$data_insert['file_old_name'] = @$fileName['file_old_name'];
						$data_insert['file_path'] = @$fileName['file_path'];
						//add coop_loan_file_attach
						$this->db->insert('coop_loan_file_attach', $data_insert);
				}
			}
		}

		$this->db->where('loan_id',$loan_id);
		$this->db->delete('coop_loan_prev_deduct');
		if(!empty($_POST['prev_loan'])){
			foreach($_POST['prev_loan'] as $key => $value){
				if(@$value['id']!=''){
					$data_insert = array();
					$data_insert['loan_id'] = @$loan_id;
					$data_insert['ref_id'] = $value['id'];
					$data_insert['data_type'] = $value['type'];
					$data_insert['pay_type'] = $value['pay_type'];
					$data_insert['pay_amount'] = str_replace(',','',$value['amount']);
					$data_insert['interest_amount'] = $value['interest'];
					$this->db->insert('coop_loan_prev_deduct', $data_insert);
				}
			}
		}

		$this->db->where("loan_id", $loan_id );
		$this->db->delete("coop_loan_financial_institutions");
		foreach($_POST['data']['coop_loan_financial_institutions'] as $key => $value){
			if($value['financial_institutions_name'] != '' && $value['financial_institutions_amount'] != ''){
				$data_insert = array();
				$data_insert['loan_id'] = @$loan_id;
				$data_insert['financial_institutions_name'] = $value['financial_institutions_name'];
				$data_insert['financial_institutions_amount'] = str_replace(',','',$value['financial_institutions_amount']);
				$data_insert['order_by'] = $key;
				$this->db->insert('coop_loan_financial_institutions', $data_insert);
			}
		}
		//exit;
		$this->center_function->toast("บันทึกข้อมูลเรียบร้อยแล้ว");
		echo "<script> document.location.href='".base_url(PROJECTPATH.'/loan?member_id='.@$_GET['member'])."' </script>";
		exit;
	}

	function coop_loan_delete(){
		$loan_id = @$_GET['loan_id'];

		$data_insert = array();
		$data_insert['loan_status'] = @$_GET['status_to'];
		$data_insert['cancel_date'] =date('Y-m-d');

		$this->db->where('id', $loan_id);
		$this->db->update('coop_loan', $data_insert);

		$this->center_function->toast("ลบข้อมูลเรียบร้อยแล้ว");
		echo true;

	}

	function ajax_coop_loan_period_table(){
		$html = '';
		$html .= '
				<div id="period_table_space">
					<table class="table table-condensed">
						<thead>
							<tr>
								<th class="text-center" style="width: 8%;">งวดที่</th>
								<th class="text-right"  style="width: 12%;">เงินต้นคงเหลือ</th>
								<th class="text-right"  style="width: 15%;">วันที่หัก</th>
								<th class="text-right"  style="width: 14%;">จำนวนวัน</th>
								<th class="text-right"  style="width: 9%;">ดอกเบี้ย</th>
								<th class="text-right"  style="width: 14%;">เงินต้นชำระ</th>
								<th class="text-right"  style="width: 15%;">รวมชำระต่อเดือน</th>
							</tr>
						</thead>
						<tbody>
			';
				$this->db->select('*');
				$this->db->from("coop_loan_period");
				$this->db->where("loan_id = '".@$_POST['loan_id']."'");
				$this->db->order_by("period_count ASC");
				$rs = $this->db->get()->result_array();
				//print_r($this->db->last_query());
				$total_loan_int = 0;
				$total_loan_pri = 0;
				$total_loan_pay = 0;
				if(!empty($rs)){
					foreach(@$rs AS $key=>$row){
						$html .= '
							<tr>
								<td class="text-center">'.@$row['period_count'].'</td>
								<td class="text-right">'.number_format(@$row['outstanding_balance'] , 2).'</td>
								<td class="text-right">'.$this->center_function->mydate2date(@$row['date_period']).'</td>
								<th class="text-right">'.@$row['date_count'].'</th>
								<td class="text-right">'.number_format(@$row['interest'], 2).'</td>
								<td class="text-right">'.number_format(@$row['principal_payment'], 2).'</td>
								<td class="text-right">'.number_format(@$row['total_paid_per_month'], 2).'</td>
							</tr>
							';
				$total_loan_int += @$row['interest'];
				$total_loan_pri += @$row['principal_payment'];
				$total_loan_pay += @$row['total_paid_per_month'];
					if(date('m',strtotime(@$row['date_period']))=='12'){
				$html .= '
						<tr style="font-weight: bold;">
							<td class="text-center"></td>
							<td class="text-center"></td>
							<td class="text-right"></td>
							<td class="text-right"> รวมปี </td>
							<td class="text-right">'.number_format(@$total_loan_int, 2).'</td>
							<td class="text-right">'.number_format(@$total_loan_pri, 2).'</td>
							<td class="text-right">'.number_format(@$total_loan_pay, 2).'</td>
						</tr>
						';
					}
				}
				}
				$html .= '
					<tr style="font-weight: bold;">
						<td class="text-center"></td>
						<td class="text-center"></td>
						<td class="text-right"></td>
						<td class="text-right"> รวม </td>
						<td class="text-right">'.number_format(@$total_loan_int, 2).'</td>
						<td class="text-right">'.number_format(@$total_loan_pri, 2).'</td>
						<td class="text-right">'.number_format(@$total_loan_pay, 2).'</td>
					</tr>
				</tbody>
			</table>
		</div>
		<div class="text-center p-v-xxl hidden-print">
			<button type="button" class="btn btn-primary btn-calculate" onclick="printElem(\'period_table_space\');">พิมพ์</button>
		</div>
		';
		echo $html;
		exit;
	}

	function ajax_get_loan_data(){
		$member_id = isset($_POST['member_id']) ? trim(@$_POST['member_id']) : "";
		$member_id = sprintf("%06d",$member_id);
		$where = ' 1=1 ';
		if(@$_POST['loan_id']!=''){
			$where .= " AND coop_loan.id = '".@$_POST['loan_id']."' ";
		}
		if(@$_POST['contract_number']!=''){
			$where .= " AND contract_number = '".@$_POST['contract_number']."' ";
		}
		//echo '<pre>'; print_r($_POST); echo '/<pre>'; exit;
		/*$this->db->select(array(
					''));coop_loan.*',
					'coop_loan.id',
					'coop_loan.createdatetime',
					'coop_loan_name.loan_name as loan_type',
					'coop_loan_transfer.id as transfer_id',
					'coop_loan.transfer_type',
					'coop_loan_transfer.account_id',
					'coop_loan_transfer.file_name',
					'coop_maco_account.account_name',
					'transfer_user.user_name
*/	
		$this->db->select(			
				'coop_loan.admin_id,
				coop_loan.loan_type,
				coop_loan.petition_number,
				coop_loan.member_id,
				coop_loan.loan_amount,
				coop_loan.loan_interest_amount,
				coop_loan.loan_amount_total,
				coop_loan.loan_amount_balance,
				coop_loan.loan_amount_total_balance,
				coop_loan.loan_reason,
				coop_loan.interest_per_year,
				coop_loan.money_per_period,
				IF (
					coop_loan.period_type = 2,
					(
						SELECT
							principal_payment
						FROM
							coop_loan_period AS coop_loan_period
						WHERE
							coop_loan_period.loan_id = coop_loan.id
						LIMIT 1
					),
					coop_loan.period_amount
				) AS period_amount,
				coop_loan.date_start_period,
				coop_loan.date_period_1,
				coop_loan.money_period_1,
				coop_loan.date_period_2,
				coop_loan.money_period_2,
				coop_loan.createdatetime,
				coop_loan.updatetimestamp,
				coop_loan.loan_status,
				coop_loan.period_type,
				coop_loan.day_start,
				coop_loan.month_start,
				coop_loan.year_start,
				coop_loan.pay_type,
				coop_loan.pay_amount,
				coop_loan.contract_number,
				coop_loan.cancel_date,
				coop_loan.salary,
				coop_loan.id,
				coop_loan.guarantee_for_id,
				coop_loan.period_now,
				coop_loan.deduct_status,
				coop_loan.type_deduct,
				coop_loan.deduct_receipt_id,
				coop_loan.transfer_type,
				coop_loan.transfer_bank_account_id,
				coop_loan.approve_date,
				coop_loan.transfer_bank_id,
				coop_loan.transfer_account_id,
				coop_loan.transfer_status,
				coop_loan.date_last_interest,
				coop_loan_name.loan_name as loan_type,
				coop_loan_transfer.id as transfer_id,
				coop_loan_transfer.account_id,
				coop_loan_transfer.file_name,
				coop_maco_account.account_name,
				transfer_user.user_name');
		$this->db->from("coop_loan");
		$this->db->join("coop_loan_name", "coop_loan.loan_type = coop_loan_name.loan_name_id", "inner");
		$this->db->join("coop_loan_transfer", "coop_loan.id = coop_loan_transfer.loan_id AND coop_loan.transfer_status <> '2'", "left");
		$this->db->join("coop_maco_account", "coop_loan_transfer.account_id = coop_maco_account.account_id", "left");
		$this->db->join("coop_user as transfer_user", "transfer_user.user_id = coop_loan_transfer.admin_id", "left");
		$this->db->where($where);
		$rs1 = $this->db->get()->result_array();
		$data['sql'] = $this->db->last_query();
		$row1 = @$rs1[0];
		if(@$row1['id']!=''){
		foreach(@$row1 as $key => $value){
			if($key == 'date_period_1' || $key == 'date_period_2' || $key == 'createdatetime' || $key == 'date_transfer'){
			@$value = date('d/m/Y H:i น.',strtotime('+543 year',strtotime(@$value)));
			}
			if($key == 'loan_amount' || $key == 'salary'){
				@$value = number_format(@$value);
			}
			@$data['coop_loan'][$key] = @$value;
		}

		$loan_id = @$row1['id'];
		//echo '<pre>'; print_r($loan_id); echo '/<pre>'; exit;
		$this->db->select(array('*'));
		$this->db->from("coop_loan_guarantee");
		$this->db->where("loan_id = '".$loan_id."'");
		$rs2 = $this->db->get()->result_array();

		$i=0;
		if(!empty($rs2)){
			foreach(@$rs2 as $key => $row2){
				foreach(@$row2 as $key => $value){
					if($key == 'amount' || $key == 'price' || $key == 'other_price'){
						@$value = number_format(@$value);
					}
					@$data['coop_loan_guarantee'][$i][$key] = @$value;
				}
				$i++;
			}
		}

		$this->db->select(array('*','coop_mem_group.mem_group_name'));
		$this->db->from("coop_loan_guarantee_person");
		$this->db->join("coop_mem_apply", "coop_loan_guarantee_person.guarantee_person_id = coop_mem_apply.member_id", "inner");
		$this->db->join("coop_mem_group", "coop_mem_apply.level = coop_mem_group.id", "left");
		$this->db->where("loan_id = '".$loan_id."'");
		$rs3 = $this->db->get()->result_array();
		$a = 0;
		if(!empty($rs3)){
			foreach(@$rs3 as $key => $row3){
				@$data['coop_loan_guarantee_person'][$a] = @$row3;
				$this->db->select(array('*'));
				$this->db->from("coop_loan_guarantee_person as t1");
				$this->db->join("coop_loan as t2", "t1.loan_id = t2.id", "inner");
				$this->db->where("t1.guarantee_person_id = '".$row3['member_id']."' AND t2.loan_status = '1'");
				$rs_count_guarantee = $this->db->get()->result_array();
				$count_guarantee=0;
				if(!empty($rs_count_guarantee)){
					foreach(@$rs_count_guarantee as $key => $row_count_guarantee){
						$count_guarantee++;
					}
				}
				@$data['coop_loan_guarantee_person'][$a]['count_guarantee'] = @$count_guarantee;
				$a++;
			}
		}
		if(!empty($data['coop_loan_guarantee_person'])){
			foreach(@$data['coop_loan_guarantee_person'] as $key => $value){
					@$data['coop_loan_guarantee_person'][$key]['guarantee_person_amount'] = number_format(@$data['coop_loan_guarantee_person'][$key]['guarantee_person_amount']);
			}
		}

		$this->db->select(array('*'));
		$this->db->from("coop_loan_period");
		$this->db->where("loan_id = '".$loan_id."'");
		$rs4 = $this->db->get()->result_array();

		if(!empty($rs4)){
			foreach(@$rs4 as $key => $row4){
				@$data['coop_loan_period'][] = @$row4;
			}
		}

		$this->db->select(array('*'));
		$this->db->from("coop_loan_file_attach");
		$this->db->where("loan_id = '".$loan_id."'");
		$rs5 = $this->db->get()->result_array();
		if(!empty($rs5)){
			foreach(@$rs5 as $key => $row5){
				@$data['coop_loan_file_attach'][] = @$row5;
			}
		}

		$this->db->select(array('*'));
		$this->db->from("coop_mem_apply");
		$this->db->where("member_id = '".$row1['member_id']."'");
		$rs6 = $this->db->get()->result_array();
		$row6 = @$rs6[0];
		@$data['coop_mem_apply'] = @$row6;

		$this->db->select(array('*'));
		$this->db->from("coop_loan_deduct_profile");
		$this->db->where("loan_id = '".$loan_id."'");
		$rs = $this->db->get()->result_array();
		@$data['coop_loan_deduct_profile'] = @$rs[0];

		$this->db->select(array('*'));
		$this->db->from("coop_loan_deduct");
		$this->db->where("loan_id = '".$loan_id."'");
		$rs = $this->db->get()->result_array();
		@$data['coop_loan_deduct'] = $rs;

		$this->db->select(array('*'));
		$this->db->from("coop_loan_cost");
		$this->db->where("loan_id = '".$loan_id."'");
		$rs = $this->db->get()->result_array();
		@$data['coop_loan_cost']['school_benefits'] = @$rs[0]['school_benefits'];
		@$data['coop_loan_cost']['saving'] = @$rs[0]['saving'];
		@$data['coop_loan_cost']['ch_p_k'] = @$rs[0]['ch_p_k'];
		@$data['coop_loan_cost']['pension'] = @$rs[0]['pension'];
		@$data['coop_loan_cost']['k_b_k'] = @$rs[0]['k_b_k'];
		@$data['coop_loan_cost']['other'] = @$rs[0]['other'];

		$this->db->select(array('*'));
		$this->db->from("coop_loan_prev_deduct");
		$this->db->where("loan_id = '".$loan_id."'");
		$rs = $this->db->get()->result_array();
		@$data['coop_loan_prev_deduct'] = @$rs;

		$this->db->select(array('*'));
		$this->db->from("coop_loan_financial_institutions");
		$this->db->where("loan_id = '".$loan_id."'");
		$rs = $this->db->get()->result_array();
		@$data['coop_loan_financial_institutions'] = @$rs;

		$this->db->select(array('*'));
		$this->db->from("coop_loan_guarantee_real_estate");
		$this->db->where("loan_id = '".$loan_id."'");
		$rs = $this->db->get()->result_array();
		@$data['coop_loan_guarantee_real_estate'] = @$rs[0];

		echo json_encode($data);
		}else{
			echo 'not_found';
		}
		exit;
	}

	function ajax_delete_loan_file_attach(){
		$this->db->select(array('*'));
		$this->db->from("coop_loan_file_attach");
		$this->db->where("id = '".@$_POST['id']."'");
		$rs = $this->db->get()->result_array();
		$row = @$rs[0];

		//$attach_path = "../uploads/loan_attach/";
		$attach_path = $_SERVER["DOCUMENT_ROOT"].PROJECTPATH."/assets/uploads/loan_attach/";
		$file = @$attach_path.@$row['file_name'];
		unlink($file);

		$this->db->where("id", @$_POST['id'] );
		$this->db->delete("coop_loan_file_attach");
		if(@$rs){
			echo "success";
		}else{
			echo "error";
		}
		exit;
	}

	function change_loan_type(){
		$this->db->select('*');
		$this->db->from('coop_loan_name');
		$this->db->join('coop_term_of_loan','coop_loan_name.loan_name_id = coop_term_of_loan.type_id','inner');
		$this->db->where("loan_type_id = '".$_POST['type_id']."'");
		$row = $this->db->get()->result_array();

		$text_return = "<option value=''>เลือกทั้งหมด</option>";
		foreach($row as $key => $value){
			$text_return .= "<option value='".$value['loan_name_id']."'>".$value['loan_name']." ".$value['loan_name_description']."</option>";
		}
		echo $text_return;
		exit;
	}

	public function loan_all()
	{
		$arr_data = array();

		$x=0;
		$join_arr = array();
		/*$join_arr[$x]['table'] = 'coop_mem_apply';
		$join_arr[$x]['condition'] = 'coop_mem_apply.member_id = coop_loan.member_id';
		$join_arr[$x]['type'] = 'left';
		$x++;
		*/

		/*
		$join_arr[$x]['table'] = 'coop_prename';
		$join_arr[$x]['condition'] = 'coop_prename.prename_id = coop_mem_apply.prename_id';
		$join_arr[$x]['type'] = 'left';
		*/

		$this->paginater_all->type(DB_TYPE);
		//$this->paginater_all->select('coop_loan.*,coop_mem_apply.*,coop_prename.prename_short');
		$this->paginater_all->select('coop_loan.*');
		$this->paginater_all->main_table('coop_loan');
		$this->paginater_all->where("loan_status = '1'");
		$this->paginater_all->page_now(@$_GET["page"]);
		$this->paginater_all->per_page(20);
		$this->paginater_all->page_link_limit(20);
		//$this->paginater_all->order_by('coop_mem_apply.mem_apply_id DESC');
		$this->paginater_all->join_arr($join_arr);
		$row = $this->paginater_all->paginater_process();

		$paging = $this->pagination_center->paginating($row['page'], $row['num_rows'], $row['per_page'], $row['page_link_limit']);//$page_now = 1, $row_total = 1, $per_page = 20, $page_limit = 20
		$i = $row['page_start'];

		foreach($row['data'] AS $key=>$value){
			$this->db->select('coop_mem_apply.firstname_th, coop_mem_apply.lastname_th,coop_prename.prename_short');
			$this->db->join("coop_prename","coop_prename.prename_id = coop_mem_apply.prename_id","left");
			$this->db->from("coop_mem_apply");
			$this->db->where("member_id = '".@$value['member_id']."'");
			$rs_member = $this->db->get()->result_array();
			$row_member = @$rs_member[0];
			$row['data'][$key]['prename_short'] = $row_member['prename_short'];
			$row['data'][$key]['firstname_th'] = $row_member['firstname_th'];
			$row['data'][$key]['lastname_th'] = $row_member['lastname_th'];
		}

		$arr_data['num_rows'] = $row['num_rows'];
		$arr_data['paging'] = $paging;
		$arr_data['row'] = $row['data'];
		$arr_data['i'] = $i;

		$this->libraries->template('loan/loan_all',$arr_data);
	}

	function get_search_loan(){
		$where = "
		 	 AND (coop_mem_apply.member_id LIKE '%".$this->input->post('search_text')."%'
		 	OR coop_mem_apply.firstname_th LIKE '%".$this->input->post('search_text')."%'
			OR coop_mem_apply.lastname_th LIKE '%".$this->input->post('search_text')."%'
			OR coop_loan.contract_number LIKE '%".$this->input->post('search_text')."%')
		";
		$this->db->select(array(
					'coop_mem_apply.id',
					'coop_mem_apply.member_id',
					'coop_mem_apply.firstname_th',
					'coop_mem_apply.lastname_th',
					'coop_loan.contract_number',
					'coop_loan.loan_amount',
					'coop_loan.loan_amount_balance'
		));
		$this->db->from('coop_loan');
		$this->db->join("coop_mem_apply","coop_mem_apply.member_id = coop_loan.member_id","left");
		$this->db->where("loan_status = '1'".$where);
		$this->db->order_by('coop_mem_apply.mem_apply_id DESC');
		$row = $this->db->get()->result_array();
		$arr_data['data'] = $row;
		$arr_data['form_target'] = $this->input->post('form_target');
		//echo"<pre>";print_r($arr_data['data']);exit;
		$this->load->view('loan/get_search_loan',$arr_data);
	}

	function petition_normal_pdf($loan_id){
		$arr_data = array();

		$this->db->select(array(
			't1.*',
			't2.*',
			't3.prename_short',
			't4.district_name',
			't5.amphur_name',
			't6.province_name',
			't7.mem_group_name',
			't8.loan_reason'
		));
		$this->db->from('coop_loan as t1');
		$this->db->join("coop_mem_apply as t2","t2.member_id = t1.member_id","inner");
		$this->db->join("coop_prename as t3","t3.prename_id = t2.prename_id","left");
		$this->db->join("coop_district as t4","t2.c_district_id = t4.district_id","left");
		$this->db->join("coop_amphur as t5","t2.c_amphur_id = t5.amphur_id","left");
		$this->db->join("coop_province as t6","t2.c_province_id = t6.province_id","left");
		$this->db->join("coop_mem_group as t7","t2.level = t7.id","left");
		$this->db->join("coop_loan_reason as t8","t1.loan_reason = t8.loan_reason_id","left");
		$this->db->where("t1.id = '".$loan_id."'");
		$row = $this->db->get()->result_array();
		$arr_data['data'] = $row[0];

		$this->db->select(array('principal_payment','total_paid_per_month'));
		$this->db->from('coop_loan_period');
		$this->db->where("loan_id = '".$loan_id."' AND period_count = '1'");
		$row = $this->db->get()->result_array();
		$arr_data['data_period_1'] = $row[0];

		$this->load->view('loan/petition_normal_pdf',$arr_data);
	}

	function petition_emergent_pdf($loan_id){
		$arr_data = array();

		$this->db->select(array(
			't1.*',
			't2.*',
			't3.prename_short',
			't4.district_name',
			't5.amphur_name',
			't6.province_name',
			't7.mem_group_name',
			't8.loan_reason',
			't9.date_transfer'
		));
		$this->db->from('coop_loan as t1');
		$this->db->join("coop_mem_apply as t2","t2.member_id = t1.member_id","inner");
		$this->db->join("coop_prename as t3","t3.prename_id = t2.prename_id","left");
		$this->db->join("coop_district as t4","t2.c_district_id = t4.district_id","left");
		$this->db->join("coop_amphur as t5","t2.c_amphur_id = t5.amphur_id","left");
		$this->db->join("coop_province as t6","t2.c_province_id = t6.province_id","left");
		$this->db->join("coop_mem_group as t7","t2.level = t7.id","left");
		$this->db->join("coop_loan_reason as t8","t1.loan_reason = t8.loan_reason_id","left");
		$this->db->join("coop_loan_transfer as t9","t1.id = t9.loan_id","left");
		$this->db->where("t1.id = '".$loan_id."'");
		$row = $this->db->get()->result_array();
		$arr_data['data'] = $row[0];

		$this->db->select(array('date_period','principal_payment','total_paid_per_month'));
		$this->db->from('coop_loan_period');
		$this->db->where("loan_id = '".$loan_id."' AND period_count = '1'");
		$row = $this->db->get()->result_array();
		$arr_data['data_period_1'] = $row[0];

		$this->db->select(array('date_period','principal_payment','total_paid_per_month'));
		$this->db->from('coop_loan_period');
		$this->db->where("loan_id = '".$loan_id."'");
		$this->db->order_by("id DESC");
		$this->db->limit(1);
		$row = $this->db->get()->result_array();
		$arr_data['data_period_last'] = $row[0];

		$this->load->view('loan/petition_emergent_pdf',$arr_data);
	}

	function petition_emergent_atm_pdf($loan_id){
		$arr_data = array();

		$this->db->select(array(
			't1.*',
			't2.*',
			't3.prename_short',
			't4.district_name',
			't5.amphur_name',
			't6.province_name',
			't7.mem_group_name',
			't8.loan_reason',
			't9.date_transfer'
		));
		$this->db->from('coop_loan as t1');
		$this->db->join("coop_mem_apply as t2","t2.member_id = t1.member_id","inner");
		$this->db->join("coop_prename as t3","t3.prename_id = t2.prename_id","left");
		$this->db->join("coop_district as t4","t2.c_district_id = t4.district_id","left");
		$this->db->join("coop_amphur as t5","t2.c_amphur_id = t5.amphur_id","left");
		$this->db->join("coop_province as t6","t2.c_province_id = t6.province_id","left");
		$this->db->join("coop_mem_group as t7","t2.level = t7.id","left");
		$this->db->join("coop_loan_reason as t8","t1.loan_reason = t8.loan_reason_id","left");
		$this->db->join("coop_loan_transfer as t9","t1.id = t9.loan_id","left");
		$this->db->where("t1.id = '".$loan_id."'");
		$row = $this->db->get()->result_array();
		$arr_data['data'] = $row[0];

		$this->db->select(array('date_period','principal_payment','total_paid_per_month'));
		$this->db->from('coop_loan_period');
		$this->db->where("loan_id = '".$loan_id."' AND period_count = '1'");
		$row = $this->db->get()->result_array();
		$arr_data['data_period_1'] = $row[0];

		$this->db->select(array('date_period','principal_payment','total_paid_per_month'));
		$this->db->from('coop_loan_period');
		$this->db->where("loan_id = '".$loan_id."'");
		$this->db->order_by("id DESC");
		$this->db->limit(1);
		$row = $this->db->get()->result_array();
		$arr_data['data_period_last'] = $row[0];

		$this->load->view('loan/petition_emergent_atm_pdf',$arr_data);
	}

	function debt_settlement($loan_id){
		$this->db->select(array('*'));
		$this->db->from('coop_loan');
		$this->db->where("id = '".$loan_id."'");
		$row_loan = $this->db->get()->result_array();
		$row_loan = $row_loan[0];

		$this->db->select(array('t1.*'));
		$this->db->from('coop_loan_guarantee_person as t1');
		$this->db->join('coop_mem_apply as t2','t1.guarantee_person_id = t2.member_id','inner');
		$this->db->where("t1.loan_id = '".$loan_id."' AND t1.guarantee_person_id != ''");
		$this->db->order_by("t1.id ASC");
		$row_guarantee = $this->db->get()->result_array();
		$i=0;
		foreach($row_guarantee as $key => $value){
			$i++;
			$data_insert = array();
			$data_insert['admin_id'] = @$_SESSION['USER_ID'];
			$data_insert['loan_type'] = $row_loan['loan_type'];
			$data_insert['createdatetime'] = date('Y-m-d H:i:s');
			$data_insert['contract_number'] = $row_loan['contract_number']."/".$i;
			$data_insert['petition_number'] = $row_loan['petition_number'];//////////////////////////////////////
			$data_insert['member_id'] = $value['guarantee_person_id'];
			$data_insert['loan_reason'] = $row_loan['loan_reason'];
			$data_insert['loan_status'] = '1';
			$data_insert['period_type'] = $row_loan['period_type'];
			$data_insert['pay_type'] = $row_loan['pay_type'];
			$data_insert['guarantee_for_id'] = $loan_id;
			$this->db->insert('coop_loan', $data_insert);
		}

		$this->db->select(array('*'));
		$this->db->from('coop_term_of_loan');
		$this->db->where("type_id = '".$row_loan['loan_type']."' AND start_date <= '".$row_loan['createdatetime']."'");
		$this->db->order_by("start_date DESC");
		$this->db->limit(1);
		$row_term_of_loan = $this->db->get()->result_array();
		$row_term_of_loan = $row_term_of_loan[0];

		if($row_term_of_loan['guarantee_interest'] == '1'){
			$interest_rate = $row_loan['interest_per_year'];
		}else{
			$interest_rate = 0;
		}
		$loan_amount = round($row_loan['loan_amount_balance']/$i);

		$interest = (double)$interest_rate; // อัตราดอกเบี้ย
		$loan = (double)$loan_amount; // จำนวนเงินกู้
		$pay_type = '1'; // ปรเภท งวด เงิน
		$period = (double)$row_term_of_loan['max_period']; // จำนวน งวด  หรือ เงิน แล้วแต่ type
		$day = (double)date('d');
		$month = (double)date('m');
		$year  = (double)(date('Y')+543);
		$period_type= (double)'1'; // ประเภท ต้นคงที่ ต้นดอก
		$loan_type= $row_loan['loan_type']; // ประเภทการกู้เงิน
		/*if($loan_type == '3' || $loan_type == '4'){
			$cal_type = '2';
		}else{*/
			$cal_type = '1';
		//}

		if($cal_type == '2'){
			if($day > 15){
				$month++;
				if($month > 12){
					$month = 1;
					$year++;
				}
			}
		}else if($cal_type == '1'){
			$month++;
			if($month > 12){
				$month = 1;
				$year++;
			}
		}

		$pay_period = $loan / $period;
		$a = ceil($pay_period/10)*10;

		$daydiff = date('t') - $day;

		$loan_remain = $loan;
		$is_last = FALSE;
		$total_loan_pri = 0;
		$total_loan_int = 0;
		$total_loan_pay = 0;
		$d = $period - 1;
		$data = array();
		$data_period = array();
		for ($i=1; $i <= $period; $i++) {
			if($loan_remain <= 0 ){ break; }
			if($pay_type == 1) {
				if ($period_type == 1) {
							if ($month > 12) {
									$month = 1;
									$year += 1;
							}
							$loan_pri = $a;
							$nummonth = cal_days_in_month(CAL_GREGORIAN, $month , $year);
							$summonth = $nummonth;
							$daydiff = 31 - $day;
							if ($i == 1) {
								if ($daydiff >= 0) {
										if ($day <= 10) {
											$summonth -=  $day;
											$summonth += 1;
										} else if ($day >= 11 && $day <= 31) {
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
							}
							$loan_int = $loan_remain * ($interest / (365 / $summonth)) / 100;
							$loan_pay = $loan_pri + $loan_int;
							$loan_remain -= $loan_pri;
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
								if ($day <= 10) {
									$summonth -=  $day;
									$summonth += 1;
								} else if ($day >= 11 && $day <= 31) {
									$month += 1;
									$nummonth = cal_days_in_month(CAL_GREGORIAN, $month , $year);
									$summonth = $nummonth;
									$summonth = $daydiff + 31;
								}
						 }
					}
					$loan_pri = $period;
					$loan_int = $loan_remain * ($interest / (365 / $summonth)) / 100;
					$loan_pay = $loan_pri + $loan_int;
					$loan_remain -= $loan_pri;
			}
		}
			else if($pay_type == 2) {
				if ($month > 12) {
						$month = 1;
						$year += 1;
				}
				$nummonth = cal_days_in_month(CAL_GREGORIAN, $month , $year);
				$summonth = $nummonth;
				$daydiff = 31 - $day;
				if ($i == 1) {
					if ($daydiff >= 0) {
							if ($day <= 10) {
								$summonth -=  $day;
								$summonth += 1;
							} else if ($day >= 11 && $day <= 31) {
								$month += 1;
								$nummonth = cal_days_in_month(CAL_GREGORIAN, $month , $year);
								$summonth = $nummonth;
								$summonth = $daydiff + 31;
							}
					 }
				}
				$interest_m = $interest/1200;
				$result = $loan * $interest_m * (pow((1 + $interest_m),$period) / (pow((1 + $interest_m),$period) -1));
				$loan_pri = $period;
				$loan_int = $loan_remain * ($interest / (365 / $summonth)) / 100;
				$loan_pay = $result;
				$loan_pri = $loan_pay - $loan_int;
				$loan_remain -= $loan_pri;
			}

			if($loan_remain <= 0) {
				$loan_pri += $loan_remain;
				$loan_pay = $loan_pri + $loan_int;
				$loan_remain = 0;
				@$count = $count + 1;
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

				$data_period[$i]['period_count'] = $i;
				$data_period[$i]['outstanding_balance'] = number_format($sumloan,2,".","");
				$date_period = ($year-543)."-".sprintf('%02d',$month)."-".$nummonth;
				if($month."-".$nummonth == '2-29'){
					$date_period = ($year-543)."-".sprintf('%02d',$month)."-".($nummonth-1);
				}
				$data_period[$i]['date_period'] = $date_period;
				$data_period[$i]['date_count'] = $summonth;
				$data_period[$i]['interest'] = number_format($loan_int,2,".","");
				$data_period[$i]['principal_payment'] = number_format($loan_pri,2,".","");
				$data_period[$i]['total_paid_per_month'] = number_format($loan_pay,2,".","");

				@$data['loan_interest_amount'] += $data_period[$i]['interest'];

				if($i=='1'){
					$data['date_start_period'] = $data_period[$i]['date_period'];

					$data['date_period_1'] = $data_period[$i]['date_period'];
					$data['money_period_1'] = $data_period[$i]['total_paid_per_month'];
				}

				if($i=='2'){
					$data['date_period_2'] = $data_period[$i]['date_period'];
					$data['money_period_2'] = $data_period[$i]['total_paid_per_month'];
				}

			if($is_last) {
				break;
			}
			$month++;
		}
		$data['loan_amount'] = $loan;
		$data['loan_amount_balance'] = $loan;
		$data['loan_amount_total'] = $loan+@$data['loan_interest_amount'];
		$data['loan_amount_total_balance'] = $loan+@$data['loan_interest_amount'];
		$data['interest_per_year'] = $interest_rate;

		$this->db->select(array('id'));
		$this->db->from('coop_loan');
		$this->db->where("guarantee_for_id = '".$loan_id."'");
		$row_new_loan = $this->db->get()->result_array();
		foreach($row_new_loan as $key => $value){
			$data_insert = array();
			$data_insert['loan_amount'] = $data['loan_amount'];
			$data_insert['loan_interest_amount'] = $data['loan_interest_amount'];
			$data_insert['loan_amount_balance'] = $data['loan_amount_balance'];
			$data_insert['loan_amount_total'] = $data['loan_amount_total'];
			$data_insert['loan_amount_total_balance'] = $data['loan_amount_total_balance'];
			$data_insert['interest_per_year'] = $data['interest_per_year'];
			$data_insert['loan_interest_amount'] = $data['loan_interest_amount'];
			$data_insert['date_start_period'] = $data['date_start_period'];
			$data_insert['date_period_1'] = $data['date_period_1'];
			$data_insert['money_period_1'] = $data['money_period_1'];
			$data_insert['date_period_2'] = $data['date_period_2'];
			$data_insert['money_period_2'] = $data['money_period_2'];
			$this->db->where('id', $value['id']);
			$this->db->update('coop_loan', $data_insert);

			foreach(@$data_period as $key_period => $value_period){
				$data_insert = array();
				$data_insert['loan_id'] = $value['id'];
				foreach($value_period as $key2 => $value2){
					//$sql .= " ".$key2." = '".$value2."',";
					$data_insert[$key2] = @$value2;
				}
				//add coop_loan_period
				$this->db->insert('coop_loan_period', $data_insert);
			}
		}

		$data_insert = array();
		$data_insert['loan_status'] = '6';
		$this->db->where('id', $loan_id);
		$this->db->update('coop_loan', $data_insert);
		//echo"<pre>";print_r($data);print_r($data_period);echo"</pre>";
		echo "<script> document.location.href='".base_url(PROJECTPATH.'/loan?member_id='.$row_loan['member_id'])."' </script>";
		exit;
	}

	function get_share_or_blue_acc(){
		$loan_amount = $_POST['loan_amount'];
		$member_id = $_POST['member_id'];
		$loan_type = $_POST['loan_type'];
		$this->db->select('*');
		$this->db->from("coop_share_setting");
		$this->db->where("setting_id = '1'");
		$row_share_setting = $this->db->get()->result_array();
		$row_share_setting = $row_share_setting[0];

		$this->db->select('share_collect_value');
		$this->db->from("coop_mem_share");
		$this->db->where("member_id = '".$member_id."' AND share_status = '1'");
		$this->db->order_by("share_date DESC, share_id DESC");
		$this->db->limit(1);
		$row_share = $this->db->get()->result_array();
		$row_share = @$row_share[0];

		$blue_acc_amount = 0;
		$this->db->select('account_id');
		$this->db->from("coop_maco_account as t1");
		$this->db->join("coop_deposit_type_setting as t2",'t1.type_id = t2.type_id','inner');
		$this->db->where("t1.mem_id = '".$member_id."' AND t1.account_status = '0' AND t2.deduct_loan = '1'");
		$row_blue_acc = $this->db->get()->result_array();
		foreach($row_blue_acc as $key => $value){
			$this->db->select('transaction_balance');
			$this->db->from("coop_account_transaction");
			$this->db->where("account_id = '".$value['account_id']."'");
			$this->db->order_by("transaction_time DESC, transaction_id DESC");
			$this->db->limit(1);
			$row_blue_acc_amount = $this->db->get()->result_array();
			if(!empty($row_blue_acc_amount)){
				$blue_acc_amount += $row_blue_acc_amount[0]['transaction_balance'];
			}
		}
		$this->db->select('least_share_or_blue_acc_percent');
		$this->db->from("coop_term_of_loan");
		$this->db->where("type_id = '".$loan_type."'");
		$row_share_setting = $this->db->get()->result_array();
		$row_share_setting = @$row_share_setting[0];

		$total_amount = @$row_share['share_collect_value'] + $blue_acc_amount;

		if($row_share_setting['least_share_or_blue_acc_percent'] > 0){
			$least_share_or_blue_acc = $loan_amount*$row_share_setting['least_share_or_blue_acc_percent']/100;
			if($total_amount < $least_share_or_blue_acc){
				$share_diff = $least_share_or_blue_acc - @$total_amount;
				$share_diff = ceil($share_diff/1000)*1000;
			}else{
				$share_diff = 0;
			}
		}else{
			$share_diff = 0;
		}
		echo $share_diff;
		exit;
	}

	function get_loan_deduct(){
		$data = array();
		$loan_amount = $_POST['loan_amount'];
		$member_id = $_POST['member_id'];
		$loan_type = $_POST['loan_type'];
		$deduct_pay_prev_loan = @$_POST['deduct_pay_prev_loan'];

		//
		$prev_checkbox_array = @$_POST['prev_checkbox_array'];
		$prev_data_type_array = @$_POST['prev_data_type_array'];
		$prev_pay_type_array = @$_POST['prev_pay_type_array'];

		$this->db->select(array(
			't2.loan_type_code'
		));
		$this->db->from("coop_loan_name as t1");
		$this->db->join("coop_loan_type as t2",'t1.loan_type_id = t2.id','inner');
		$this->db->where("loan_name_id = '".$loan_type."'");
		$row_loan_type = $this->db->get()->result_array();
		$loan_type_code = @$row_loan_type[0]['loan_type_code'];

		$this->db->select(array(
			't1.loan_amount_balance','t1.id'
		));
		$this->db->from("coop_loan as t1");
		$this->db->join("coop_loan_name as t2",'t1.loan_type = t2.loan_name_id','inner');
		$this->db->join("coop_loan_type as t3",'t2.loan_type_id = t3.id','inner');
		$this->db->where("t1.member_id = '".$member_id."' AND t1.loan_status = '1' AND t3.loan_type_code = '".$loan_type_code."'");
		//$this->db->where("t1.member_id = '".$member_id."' AND t1.loan_status = '1'");
		$row_prev_loan = $this->db->get()->result_array();
		$data['sql'] = $this->db->last_query();
		$prev_loan_balance = 0;
		foreach($row_prev_loan as $key => $value){
			if(!empty($prev_checkbox_array)){
				foreach($prev_checkbox_array as $key_prev => $value_prev){
					if(@$value_prev == @$value['id']){
						if($prev_pay_type_array[$key_prev] == 'all'){
							$cal_loan_interest = array();
							$cal_loan_interest['loan_id'] = @$value['id'];
							$cal_loan_interest['date_interesting'] = date('Y-m-d');
							$interest_loan = $this->loan_libraries->cal_loan_interest($cal_loan_interest);
							$prev_loan_balance += $value['loan_amount_balance']+$interest_loan;
							$data['prev_loan_balance_B'] = $prev_loan_balance;
						}else{
							$this->db->select(array('*'));
							$this->db->from('coop_finance_month_detail as t1');
							$this->db->where("
								t1.loan_id = '".$value['id']."'
								AND t1.run_status = '0'
								AND t1.pay_type = 'principal'
							");
							$row_month = $this->db->get()->result_array();
							$principal_month = 0;
							foreach($row_month as $key2_month => $value2_month){
								$principal_month += $value2_month['pay_amount'];
							}

							$prev_loan_balance += $value['loan_amount_balance']-$principal_month;
							$data['prev_loan_balance_C'] = $prev_loan_balance;
						}
					}
				}

			}
			//code เดิม
			//$prev_loan_balance += $value['loan_amount_balance'];

		}
		$data['loan_type_code'] = $loan_type_code;
		if($loan_type_code == 'emergent'){
			$this->db->select(array(
				't1.total_amount_approve',
				't1.total_amount_balance'
			));
			$this->db->from("coop_loan_atm as t1");
			$this->db->where("t1.member_id = '".$member_id."' AND t1.loan_atm_status = '1'");
			$row_prev_loan = $this->db->get()->result_array();
			$data['sql'] = $this->db->last_query();
			foreach($row_prev_loan as $key => $value){
				$prev_loan_balance += ($value['total_amount_approve']-$value['total_amount_balance']);
			}
		}
		//$data['prev_loan_balance'] = $prev_loan_balance;

		$this->db->select(array(
		'percent_guarantee',
		'percent_guarantee_option',
		'loan_fee',
		'loan_fee_option'
		));
		$this->db->from("coop_term_of_loan");
		$this->db->where("type_id = '".$loan_type."'");
		$row_term_of_loan = $this->db->get()->result_array();
		$row_term_of_loan = @$row_term_of_loan[0];
		//$data['sql'] = $this->db->last_query();
		if($row_term_of_loan['percent_guarantee'] > 0){
			if($row_term_of_loan['percent_guarantee_option'] == '1'){
				$percent_guarantee = (($loan_amount-$prev_loan_balance)*$row_term_of_loan['percent_guarantee'])/100;
				$data['percent_guarantee_a'] = '(('.$loan_amount.'-'.$prev_loan_balance.')*'.$row_term_of_loan['percent_guarantee'].')/100';
			}else{
				$percent_guarantee = ($loan_amount*$row_term_of_loan['percent_guarantee'])/100;
			}
		}else{
			$percent_guarantee = 0;
		}

		if($row_term_of_loan['loan_fee'] > 0){
			if($row_term_of_loan['loan_fee_option'] == '1'){
				$loan_fee = (($loan_amount-$deduct_pay_prev_loan)*$row_term_of_loan['loan_fee'])/100;
			}else{
			$loan_fee = ($loan_amount*$row_term_of_loan['loan_fee'])/100;
			}
		}else{
		$loan_fee = 0;
		}

		$this->db->select(array('share_collect','share_collect_value'));
		$this->db->from('coop_mem_share');
		$this->db->where("member_id = '".$member_id."' AND share_status = '1'");
		$this->db->order_by('share_date DESC, share_id DESC');
		$this->db->limit(1);
		$row_share = $this->db->get()->result_array();


		//$data['percent_guarantee'] = number_format($percent_guarantee,2);
		$data['percent_guarantee'] = number_format($percent_guarantee); //ปัดเศษ
		$data['loan_fee'] = number_format($loan_fee,2);
		$data['share_collect'] = number_format(@$row_share[0]['share_collect']);
		$data['share_collect_value'] = number_format(@$row_share[0]['share_collect_value']);
		echo json_encode($data);
		exit;
	}

	function loan_cal_interest(){
		$arr_data = array();
		if($_POST){
			$cal_loan_interest = array();
			$cal_loan_interest['loan_id'] = $_POST['loan_id'];
			$cal_loan_interest['date_interesting'] = $this->center_function->ConvertToSQLDate($_POST['apply_date']);
			$interest_data = $this->loan_libraries->cal_loan_interest($cal_loan_interest,'array');
			//echo"<pre>";print_r($interest_data);exit;
			$arr_data['interest_data'] = $interest_data;
		}

		$this->db->select(array('t1.id','t1.contract_number','t1.member_id'));
		$this->db->from('coop_loan as t1');
		$this->db->join('coop_loan_transfer as t2','t1.id = t2.loan_id','inner');
		$this->db->where("t1.loan_status = '1' AND t2.transfer_status = '0'");
		$this->db->order_by("id ASC");
		$row = $this->db->get()->result_array();
		$arr_data['loan_list'] = $row;

		$this->libraries->template('loan/loan_cal_interest',$arr_data);
	}

	function loan_atm_cal_interest(){
		$arr_data = array();
		if($_POST){
			$cal_atm_interest = array();
			$cal_atm_interest['loan_atm_id'] = $_POST['loan_atm_id'];
			$cal_atm_interest['date_interesting'] = $this->center_function->ConvertToSQLDate($_POST['apply_date']);
			//อันเดิม
			//$interest_data = $this->loan_libraries->cal_atm_interest($cal_atm_interest,'array');
			//echo"<pre>";print_r($interest);exit;
			//ดอกเบี้ยเงินกู้ตามช่วงเวลาที่มีการทำรายการ
			$interest_data = $this->loan_libraries->cal_atm_interest_transaction($cal_atm_interest,'array');

			$arr_data['interest_data'] = $interest_data;
		}

		$this->db->select(array('t1.loan_atm_id','t1.contract_number','t1.member_id'));
		$this->db->from('coop_loan_atm as t1');
		$this->db->where("t1.loan_atm_status = '1'");
		$this->db->order_by("loan_atm_id ASC");
		$row = $this->db->get()->result_array();
		$arr_data['loan_list'] = $row;

		$this->libraries->template('loan/loan_atm_cal_interest',$arr_data);
	}

	function get_loan_data(){
	  $data = array();
	  $this->db->select(array(
	   't1.id AS loan_id',
	   't1.contract_number',
	   't3.member_id',
	   't3.firstname_th',
	   't3.lastname_th',
	   't1.loan_amount',
	   't3.dividend_bank_id',
	   't3.dividend_bank_branch_id',
	   't3.dividend_acc_num',
	   't4.estimate_receive_money',
	   't1.transfer_type',
	   't1.transfer_bank_id',
	   't1.transfer_bank_account_id',
	   't1.transfer_account_id'
	  ));
	  $this->db->from("coop_loan t1");
	  $this->db->join("coop_mem_apply t3",'t1.member_id = t3.member_id','left');
	  $this->db->join("coop_loan_deduct_profile t4",'t1.id = t4.loan_id','left');
	  $this->db->where("t1.id = '".$_POST['loan_id']."'");
	  $row = $this->db->get()->result_array();
	  $coop_loan_atm = @$row[0];
	  $data = array();
	  //echo $this->db->last_query();exit;
	  
	   //ชำระหนี้สถาบันการเงิน
	  $institutions_amount = $this->db->select(array('SUM(financial_institutions_amount) AS financial_institutions_amount'))
	  ->from("coop_loan_financial_institutions")
	  ->where("loan_id= '".$_POST['loan_id']."'")
	  ->get()->result_array();
	  $total_institutions_amount = @$institutions_amount[0]['financial_institutions_amount'];
	  
	  foreach($row[0] as $key => $value){
	   if($key == 'loan_amount'){
		$data[$key] = number_format($value);
	   }else if($key == 'estimate_receive_money'){
		$estimate_receive_money = @$value-@$total_institutions_amount;
		$data[$key] = number_format($estimate_receive_money,2);
	   }else{
		$data[$key] = @$value;
	   }
	  }

	  echo json_encode($data);
	  exit;
	 }

	public function get_account_list(){
		$member_id = @$_POST['member_id'];
		$arr_data = array();

		$this->db->select(array('coop_account_id'));
		$this->db->from('coop_mem_apply');
		$this->db->where("member_id = '".$member_id."'");
		$rs_mem = $this->db->get()->result_array();
		$mem_account_id = @$rs_mem[0]['coop_account_id'];

		$this->db->select(array('*'));
		$this->db->from('coop_maco_account');
		$this->db->where("mem_id = '".$member_id."' AND account_status = '0'");
		$rs_account = $this->db->get()->result_array();
		$arr_data['rs_account'] = @$rs_account;

		$this->load->view('loan/get_account_list',$arr_data);
	}

	function update_salary(){
		//echo"<pre>";print_r($_POST);exit;
		$data_insert = array();
		$data_insert['salary'] = str_replace(',','',$_POST['salary']);
		$data_insert['other_income'] = str_replace(',','',$_POST['other_income']);
		$this->db->where('member_id',$_POST['member_id']);
		$this->db->update('coop_mem_apply',$data_insert);
		echo "success";
		exit;
	}

	function loan_payment_detail($loan_id){
		$arr_data = array();

		$this->db->select(array(
			't1.*',
			't2.firstname_th',
			't2.lastname_th',
			't3.prename_short'
		));
		$this->db->from('coop_loan as t1');
		$this->db->join('coop_mem_apply as t2','t1.member_id = t2.member_id','inner');
		$this->db->join('coop_prename as t3','t2.prename_id = t3.prename_id','left');
		$this->db->where("t1.id = '".$loan_id."'");
		$loan_data = $this->db->get()->result_array();
		$arr_data['loan_data'] = $loan_data[0];

		$this->db->select(array('t1.receipt_id', 't1.payment_date', 't1.principal_payment', 't1.interest', 't1.loan_amount_balance','t2.finance_month_profile_id','t2.receipt_status'));
		$this->db->from('coop_finance_transaction as t1');
		$this->db->join('coop_receipt as t2','t1.receipt_id = t2.receipt_id','inner');
		$this->db->where("t1.loan_id = '".$loan_id."'");
		$this->db->order_by('payment_date ASC');
		$row_transaction = $this->db->get()->result_array();
// echo $this->db->last_query(); echo '<br>';
		$sql = "SELECT t1.receipt_id, t1.payment_date, t1.principal_payment, t1.interest, t1.loan_amount_balance,t2.finance_month_profile_id, '' ref_id, t1.finance_transaction_id , null as receipt_status, t1.transaction_text
						FROM coop_finance_transaction as t1
						LEFT JOIN coop_receipt as t2 ON t1.receipt_id = t2.receipt_id
						WHERE t1.loan_id = '{$loan_id}'
						UNION ALL
						SELECT bill_id receipt_id, DATE(return_time) payment_id
						, CASE WHEN return_type = 5 THEN return_principal WHEN return_type = 3 THEN return_amount * -1 WHEN return_type = 2 THEN CASE WHEN return_principal > 0 THEN return_principal * - 1 ELSE 0 END ELSE CASE WHEN return_principal > 0 THEN return_principal * - 1 ELSE 0 END END principal_payment
						, CASE WHEN return_type = 5 THEN CASE WHEN (return_principal = 0 and return_interest = 0) THEN return_amount ELSE return_interest END WHEN return_type = 2 THEN CASE WHEN (return_principal = 0 and return_interest = 0) THEN return_amount * - 1 ELSE return_interest * -1 END  WHEN return_type <> 3 AND (return_principal = 0 and return_interest = 0)  THEN return_amount * -1 ELSE return_interest*-1 END interest
						, '0' loan_amount_balance
						, '' finance_month_profile_id
						, receipt_id ref_id
						, 99999999
						, '' receipt_status
						, CASE WHEN return_desc != '' THEN return_desc WHEN return_type = 2 AND receipt_id is not null THEN concat('เงินคืน ', receipt_id) ELSE CASE WHEN return_type = 5 THEN concat('เก็บเพิ่ม ', receipt_id) WHEN return_type = 1 THEN concat('เงินคืน ', receipt_id) ELSE '' END END as transaction_text
						FROM coop_process_return
						WHERE loan_id = '{$loan_id}'
						UNION ALL
							SELECT 
								CONCAT(t1.receipt_id,' ') as receipt_id,
								t3.cancel_date as payment_date,
								SUM(t1.principal_payment) as principal_payment,
								SUM(t1.interest) as interest,
								t1.loan_amount_balance,
								t3.finance_month_profile_id,
								'' ref_id,
								t1.finance_transaction_id,
								t3.receipt_status,
								t1.transaction_text
						FROM
							coop_finance_transaction AS t1
						INNER JOIN coop_receipt AS t3 ON t1.receipt_id = t3.receipt_id
						WHERE
							t1.loan_id = '{$loan_id}' AND t3.receipt_status = 2 GROUP BY 1
                        ";
                        
		//, CASE WHEN return_type = 3 THEN return_amount * -1 WHEN return_type = 2 THEN return_principal * - 1 ELSE CASE WHEN return_principal > 0 THEN return_principal * - 1 ELSE 0 END END principal_payment
		$rs = $this->db->query($sql);
		$row_transaction = $rs->result_array();
		//echo $this->db->last_query();exit;
		// echo "<pre>";var_dump($row_transaction);exit;
		$non_pay_raws = $this->db->select('t1.non_pay_id, t1.non_pay_month as month, t1.non_pay_year as year, t2.non_pay_amount as principal_payment, t3.non_pay_amount as interest, t2.loan_id as l1, t2.loan_id as l2')
							->from("coop_non_pay as t1")
							->join("coop_non_pay_detail as t2", "t1.non_pay_id = t2.non_pay_id AND t2.pay_type = 'principal' && t2.loan_id = '{$loan_id}'", "left")
							->join("coop_non_pay_detail as t3", "t1.non_pay_id = t3.non_pay_id AND t3.pay_type = 'interest' && t3.loan_id = '{$loan_id}'", "left")
							->WHERE("(t2.loan_id = '{$loan_id}' || t3.loan_id = '{$loan_id}') && t1.non_pay_status != '0'")
							->get()->result_array();

		$non_pay_datas = array();

		
		foreach($non_pay_raws as $non_pay_raw) {
			$non_pay_data = array();
			$non_pay_data['receipt_id'] = "dummy-".$non_pay_raw['non_pay_id'];
			$month = $non_pay_raw['month'];
			$year = $non_pay_raw['year'] - 543;
			$non_pay_date = $year."-".$month."-1";
			$non_pay_data['payment_date'] = date("Y-m-t", strtotime($non_pay_date));
			$non_pay_data['principal_payment'] = $non_pay_raw['principal_payment'];
			$non_pay_data['interest'] = $non_pay_raw['interest'];
			$non_pay_data['loan_amount_balance'] = null;
			$non_pay_data['finance_month_profile_id'] = null;
			$non_pay_data['ref_id'] = null;
			$non_pay_data['finance_transaction_id'] = 'a';
			$non_pay_datas[] = $non_pay_data;
		}
		// echo "<pre>";var_dump($non_pay_datas);exit;
		$merge_transactions = array_merge($row_transaction,$non_pay_datas);
		array_multisort( array_column($merge_transactions, "payment_date"), SORT_ASC,
						array_column($merge_transactions, 'finance_transaction_id'), SORT_ASC,
							$merge_transactions );
		// echo "<pre>";var_dump($merge_transactions);exit;
			$transaction_data = array();
		$loan_amount_balance = $arr_data['loan_data']["loan_amount"];
		$date_Check = 0;
		$run_no = 0;
		$principal_total = 0;
		foreach($merge_transactions as $key => $value){
			//echo "<pre>"; print_r($value); echo "</pre>";
			$receipt_id = (@$value['receipt_id'] != '')?@$value['receipt_id']:$run_no++;
			if (strpos($value['receipt_id'], "dummy") !== false) {
				if($date_Check != $value['payment_date']) {
					//for monthly request
					$transaction_data[$receipt_id]['data_text'] = 'ชำระเงินรายเดือน';
					$transaction_data[$receipt_id]['receipt_id'] = $value['receipt_id'];
					$transaction_data[$receipt_id]['payment_date'] = $value['payment_date'];
					@$transaction_data[$receipt_id]['principal'] = @$value['principal_payment'];
					@$transaction_data[$receipt_id]['interest'] = @$value['interest'];
					//@$transaction_data[$receipt_id]['loan_amount_balance'] = $loan_amount_balance - $value['principal_payment'];		
					if($transaction_data[array_keys($transaction_data)[sizeof($transaction_data)-2]]['finance_transaction_id'] == '99999999'){
						@$transaction_data[$receipt_id]['loan_amount_balance'] = $transaction_data[array_keys($transaction_data)[sizeof($transaction_data)-3]]['loan_amount_balance'] - $value['principal_payment'];
					}else{
						@$transaction_data[$receipt_id]['loan_amount_balance'] = $loan_amount_balance - $value['principal_payment'];
					}
					@$transaction_data[$receipt_id]['finance_transaction_id'] = @$value['finance_transaction_id'];					
					
					//Minus
					$transaction_data[$receipt_id."_m"]['data_text'] = 'ชำระเงินรายเดือน';
					$transaction_data[$receipt_id."_m"]['receipt_id'] = $value['receipt_id'];
					$transaction_data[$receipt_id."_m"]['payment_date'] = $value['payment_date'];
					@$transaction_data[$receipt_id."_m"]['principal'] =  !empty($value['principal_payment']) ? "-".$value['principal_payment'] : 0;
					@$transaction_data[$receipt_id."_m"]['interest'] = !empty($value['interest']) ? "-".$value['interest'] : 0;
					//@$transaction_data[$receipt_id."_m"]['loan_amount_balance'] = $loan_amount_balance;
					@$transaction_data[$receipt_id."_m"]['loan_amount_balance'] = $transaction_data[array_keys($transaction_data)[sizeof($transaction_data)-2]]['loan_amount_balance']+$value['principal_payment'];
					@$transaction_data[$receipt_id."_m"]['finance_transaction_id'] = @$value['finance_transaction_id'];
				}
			} else {
				$has_dept = false;
				$principal_total = 0;
				$principal_paid = 0;
				$interest_total = 0;
				$princial_non_pay = 0;
				$interest_non_pay = 0;				
				if($value['finance_month_profile_id'] != ''){

						$transaction_data[$receipt_id]['data_text'] = 'ชำระเงินรายเดือน';
						$date_Check = date("Y-m-t", strtotime($value['payment_date']));
						$month_detail = $this->db->select("t1.pay_amount as principal, t1.real_pay_amount, t2.pay_amount as interest, t2.real_pay_amount as real_interest")
												->from("coop_finance_month_detail as t1")
												->join("coop_finance_month_detail as t2", "t1.profile_id = t2.profile_id AND t1.loan_id = t2.loan_id AND t2.pay_type = 'interest'")
												->where("t1.profile_id = '".$value['finance_month_profile_id']."' AND t1.loan_id = '".$loan_id."' AND t1.pay_type = 'principal'")
												->get()->row();
					if($month_detail->principal != $month_detail->real_pay_amount || $month_detail->interest != $month_detail->real_interest) {
						$has_dept = true;
					}
					$principal_total = $month_detail->principal;
					$principal_paid = $month_detail->real_pay_amount;
					$interest_total = $month_detail->interest;
					$interest_paid = $month_detail->real_interest;

					$non_pay = $this->db->select('t1.non_pay_amount as principal, t2.non_pay_amount as interest')
										->from("coop_non_pay_detail as t1")
										->join("coop_non_pay_detail as t2", "t1.non_pay_id = t2.non_pay_id AND t2.pay_type = 'interest' && t2.loan_id = '{$loan_id}'", "left")
										->where("t1.finance_month_profile_id = '".$value['finance_month_profile_id']."' AND t1.loan_id = '".$loan_id."' AND t1.pay_type = 'principal'")
										->get()->row();
					$princial_non_pay = $non_pay->principal;
					$interest_non_pay = $non_pay->interest;
					
				}else{
					if($value['receipt_id'] == '-')
					{
						$transaction_data[$receipt_id]['data_text'] = 'ดอกเบี้ยสะสมยกมา ณ 31 ส.ค. 61';
					}else if($value['receipt_id'] == ''){
						$transaction_data[$receipt_id]['data_text'] = $value['transaction_text'];
					}else{
						$transaction_data[$receipt_id]['data_text'] = 'ชำระเงินอื่นๆ';
					}
				}
			    
				if( $value['ref_id'] ) {
                    // if($value['return_type']==5){
                    //     $transaction_data[$receipt_id]['data_text'] = 'เก็บเพิ่ม '.$value['ref_id'];
                    // }else{
                        
                    // }
					// $transaction_data[$receipt_id]['data_text'] = $value['transaction_text']." ".$value['ref_id'];
					$transaction_data[$receipt_id]['data_text'] = $value['transaction_text'];
					
				}
				if(!$has_dept || strpos($value["receipt_id"],'C')) {
					if($value['receipt_status'] == '2'){
						unset($transaction_data[$receipt_id]); 
						$princial_non_pay = ($value['principal_payment']*-1);
						$interest_non_pay = ($value['interest']*-1);
						
						$transaction_data[$receipt_id."_m"]['data_text'] = 'error';
						$transaction_data[$receipt_id."_m"]['receipt_id'] = $value['receipt_id'];
						$transaction_data[$receipt_id."_m"]['payment_date'] = $value['payment_date'];
						$transaction_data[$receipt_id."_m"]['principal'] = $princial_non_pay;
						$transaction_data[$receipt_id."_m"]['interest'] = $interest_non_pay;
						$transaction_data[$receipt_id."_m"]['loan_amount_balance'] = $transaction_data[array_keys($transaction_data)[sizeof($transaction_data)-2]]['loan_amount_balance'] - $princial_non_pay;
						@$transaction_data[$receipt_id."_m"]['finance_transaction_id'] = @$value['finance_transaction_id'];
				}else{
						$transaction_data[$receipt_id]['receipt_id'] = $value['receipt_id'];
						$transaction_data[$receipt_id]['payment_date'] = $value['payment_date'];
						$transaction_data[$receipt_id]['principal'] += $value['principal_payment'];
						$transaction_data[$receipt_id]['interest'] += $value['interest'];
						$transaction_data[$receipt_id]['loan_amount_balance'] = $value['loan_amount_balance'];
						@$transaction_data[$receipt_id]['finance_transaction_id'] = @$value['finance_transaction_id'];
					}
				
				}else {
					$transaction_data[$receipt_id]['receipt_id'] = $value['receipt_id'];
					$transaction_data[$receipt_id]['payment_date'] = $value['payment_date'];
					$transaction_data[$receipt_id]['principal'] = $principal_total;
					$transaction_data[$receipt_id]['interest'] = $interest_total;
					$transaction_data[$receipt_id]['loan_amount_balance'] = $loan_amount_balance - $principal_total;
					@$transaction_data[$receipt_id]['finance_transaction_id'] = @$value['finance_transaction_id'];
				
					
					//Minus
					$transaction_data[$receipt_id."_m"]['data_text'] = 'ชำระเงินรายเดือน';
					$transaction_data[$receipt_id."_m"]['receipt_id'] = $value['receipt_id'];
					$transaction_data[$receipt_id."_m"]['payment_date'] = $value['payment_date'];
					$transaction_data[$receipt_id."_m"]['principal'] = !empty($princial_non_pay) ? "-".$princial_non_pay : 0;
					$transaction_data[$receipt_id."_m"]['interest'] = !empty($interest_non_pay) ? "-".$interest_non_pay : 0;
					$transaction_data[$receipt_id."_m"]['loan_amount_balance'] = $transaction_data[$receipt_id]['loan_amount_balance'] - ($princial_non_pay * -1);
					@$transaction_data[$receipt_id."_m"]['finance_transaction_id'] = @$value['finance_transaction_id'];
				}
				if(!empty($value['principal_payment'])) $loan_amount_balance = $value['loan_amount_balance'];
			}
		}

		$arr_data['transaction_data'] = $transaction_data;
		//echo"<pre>";print_r($transaction_data);
		//echo"<pre>";print_r($transaction_data);exit;
		$this->preview_libraries->template_preview('loan/loan_payment_detail',$arr_data);
	}

	public function loan_repayment()
	{
		$this->load->helper('cookie');
		$save_status = $this->uri->segment(3);
		if($save_status=="success"){
			$loan_id = $this->uri->segment(4);

			$contract_number = "";
			if($loan_id != ""){
				$loan = $this->db->get_where("coop_loan", array("id" => $loan_id))->result()[0];
				$contract_number = $loan->contract_number;
			}
			CI_Input::set_cookie("save_status", '1', 50);
			CI_Input::set_cookie("contract_number", $contract_number, 50);
			header("Location: ".base_url('loan/loan_repayment'));
		}
		$arr_data = array();

		$arr_data['save_status'] = @$_COOKIE['save_status'];
		$arr_data['contract_number'] = @$_COOKIE['contract_number'];
		$arr_data['bank'] = $this->db->get("coop_bank")->result();

		$this->libraries->template('loan/loan_repayment',$arr_data);
	}

	public function save_loan_repayment(){
		$tmp_data = $this->input->post();
		if($tmp_data['transfer_type']!=2)
			$tmp_data['account_id'] = "";

		if($tmp_data['transfer_type']!=3){
			$tmp_data['bank_id'] = "";
			$tmp_data['branch_code'] = "";
			$tmp_data['account_no'] = "";
		}

		$data = array();
		foreach ($tmp_data as $key => $value) {
			if($value!=""){
				$data[$key] = $value;
			}
		}

		$this->db->where('loan_id', $tmp_data['loan_id']);
		$this->db->from('coop_loan_repayment');

		$seq = $this->db->count_all_results();
		$data['seq'] = ($seq == 0 ) ? 1 : $seq+1;
		$data['admin_id'] = $_SESSION['USER_ID'];
		$data['transaction_time'] = date("Y-m-d H:i:s");
		$data['loan_request'] = implode("", explode(",", $tmp_data['loan_request']));

		if($data['loan_request']!="" && $data['loan_request']!=0 && $data['loan_id']!=""){
			$this->db->insert("coop_loan_repayment", $data);

			// $this->db->set('loan_amount_balance', 'loan_amount_balance+'.$data['loan_request'], FALSE);
			// $this->db->where("id", $data['loan_id']);
			// $this->db->where("loan_amount_balance <= loan_amount_balance+".$data['loan_request'], '', FALSE);
			// $this->db->update('coop_loan');

			// //กรณีเลือกโอนเงินเข้าบัญชีสหกรณ์
			// if($data['transfer_type']==2){
			// 	$this->db->order_by('transaction_time' , 'DESC');
			// 	$this->db->limit(1);
			// 	$q_transaction = $this->db->get_where("coop_account_transaction", array(
			// 		"account_id" => $data['account_id']
			// 	))->result()[0];

			// 	$coop_account_transaction['transaction_time'] = $data['transaction_time'];
			// 	$coop_account_transaction['transaction_list'] = "LRM";
			// 	$coop_account_transaction['transaction_withdrawal'] = 0;
			// 	$coop_account_transaction['transaction_deposit'] = $data['loan_request'];
			// 	$coop_account_transaction['transaction_balance'] = $data['loan_request'] + $q_transaction->transaction_balance;
			// 	$coop_account_transaction['transaction_no_in_balance'] = $data['loan_request'] + $q_transaction->transaction_no_in_balance;
			// 	$coop_account_transaction['user_id'] = $data['admin_id'];
			// 	$coop_account_transaction['account_id'] = $data['account_id'];
			// 	$coop_account_transaction['book_number'] = $q_transaction->book_number;
			// 	$coop_account_transaction['loan_id'] = $data['loan_id'];
			// 	$this->db->insert("coop_account_transaction", $coop_account_transaction);
			// }

		}


		header("Location: ".base_url('loan/loan_repayment/success/'.$data['loan_id']));
	}

	public function loan_transfer_repayment(){
		$this->db->select(array("coop_loan_repayment.*", "coop_user.user_name", "coop_loan.member_id", "coop_mem_apply.firstname_th", "coop_mem_apply.lastname_th"));
		$this->db->join("coop_user", "coop_loan_repayment.admin_id = coop_user.user_id");
		$this->db->join("coop_loan", "coop_loan.id = coop_loan_repayment.loan_id");
		$this->db->join("coop_mem_apply", "coop_loan.member_id = coop_mem_apply.member_id");
		$arr_data['loan'] = $this->db->get_where("coop_loan_repayment", array(
			"status" => "0"
		))->result();

		$this->libraries->template('loan/loan_transfer_repayment',@$arr_data);
	}

	public function get_loan_transfer_repayment(){
		$id = @$_POST['id'];
		$this->db->select(array("coop_loan_repayment.*", "coop_user.user_name", "coop_loan.member_id", "coop_mem_apply.firstname_th", "coop_mem_apply.lastname_th", "coop_loan.contract_number", "coop_bank.bank_name", "coop_loan.id as loan_id"));
		$this->db->join("coop_user", "coop_loan_repayment.admin_id = coop_user.user_id");
		$this->db->join("coop_loan", "coop_loan.id = coop_loan_repayment.loan_id");
		$this->db->join("coop_mem_apply", "coop_loan.member_id = coop_mem_apply.member_id");
		$this->db->join("coop_bank", "coop_bank.bank_id = coop_loan_repayment.bank_id", "left");
		$this->db->where("coop_loan_repayment.id", $id);

		$transfer = ["เงินสด", "โอนเงินบัญชีสหกรณ์", "โอนเงินบัญชีธนาคาร"];
		// $this->db->get("coop_loan_repayment")->result()[0]->transfer_type_name = $transfer[$this->db->get("coop_loan_repayment")->result()[0]->transfer_type];
		$result = $this->db->get("coop_loan_repayment")->result()[0];
		$result->transfer_type_name = $transfer[$result->transfer_type-1];
		$result->loan_request = number_format($result->loan_request, 2);
		$result->account_id = $this->center_function->convert_account_id($result->account_id);
		echo json_encode($result);
	}

	public function save_loan_repayment_approve(){
		$id = @$_POST['id'];
		$loan_repaymemt = $this->db->get_where("coop_loan_repayment", array("id" => $id))->result()[0];
		$loan_id = $loan_repaymemt->loan_id;
		if($loan_repaymemt!=""){

			$this->db->set('loan_amount_balance', 'loan_amount_balance+'.$loan_repaymemt->loan_request, FALSE);
			$this->db->where("id", $loan_repaymemt->loan_id);
			$this->db->where("loan_amount_balance <= loan_amount_balance+".$loan_repaymemt->loan_request, '', FALSE);
			$this->db->update('coop_loan');

			//กรณีเลือกโอนเงินเข้าบัญชีสหกรณ์
			if($loan_repaymemt->transfer_type==2){
				$this->db->order_by('transaction_time' , 'DESC');
				$this->db->limit(1);
				$q_transaction = $this->db->get_where("coop_account_transaction", array(
					"account_id" => $loan_repaymemt->account_id
				))->result()[0];

				$coop_account_transaction['transaction_time'] = date("Y-m-d H:i:s'");
				$coop_account_transaction['transaction_list'] = "LRM";
				$coop_account_transaction['transaction_withdrawal'] = 0;
				$coop_account_transaction['transaction_deposit'] = $loan_repaymemt->loan_request;
				$coop_account_transaction['transaction_balance'] = $loan_repaymemt->loan_request + $q_transaction->transaction_balance;
				$coop_account_transaction['transaction_no_in_balance'] = $loan_repaymemt->loan_request + $q_transaction->transaction_no_in_balance;
				$coop_account_transaction['user_id'] = $_SESSION['USER_ID'];
				$coop_account_transaction['account_id'] = $loan_repaymemt->account_id;
				$coop_account_transaction['book_number'] = $q_transaction->book_number;
				$coop_account_transaction['loan_id'] = $loan_repaymemt->id;
				$this->db->insert("coop_account_transaction", $coop_account_transaction);
			}

			$this->db->set('update_time', date("Y-m-d H:i:s"));
			$this->db->set('status', '1');
			$this->db->where("id", $id);
			$status_update = $this->db->update('coop_loan_repayment');

			if($status_update){
				$data_save['loan_id'] 	=	$loan_id;
				$this->db->select( array("loan_amount_balance") );
				$data_save['loan_amount_balance'] 	=	$this->db->get_where("coop_loan", array("id" => $loan_id) )->result()[0]->loan_amount_balance;
				$data_save['transaction_datetime'] 	=	date("Y-m-d H:i:s");
				$this->db->insert("coop_loan_transaction", $data_save);
				echo "success";
			}

			
			$this->db->select('coop_account_match.account_chart_id,coop_account_chart.account_chart,coop_loan_repayment.loan_id,coop_loan_repayment.transaction_time,coop_loan_repayment.loan_request,coop_loan_repayment.transfer_type,coop_loan_repayment.account_id,coop_loan_repayment.account_no');
			$this->db->from('coop_account_match');
			$this->db->join('coop_loan', 'coop_account_match.match_id = coop_loan.loan_type', 'left');
			$this->db->join('coop_loan_repayment', 'coop_loan_repayment.loan_id = coop_loan.id', 'left');
			$this->db->join('coop_account_chart', 'coop_account_chart.account_chart_id = `coop_account_match`.`account_chart_id`', 'left');
			$this->db->where("coop_loan_repayment.id = '".$_POST['id']."' ");
			// echo "".$this->db->get_compiled_select(null, false)."<br><br><br><br>";
	
			$row = $this->db->get()->result_array();
			$row_account_chart = @$row[0];
			$account_chart_id = @$row_account_chart['account_chart_id'];
			$loan_id = @$row_account_chart['loan_id'];

			$account_chart = @$row_account_chart['account_chart'];
			$transfer_type = @$row_account_chart['transfer_type'];

			  if(!empty($row_account_chart['account_id'])){
				$account_id_transfer = $row_account_chart['account_id'];
			  }else if(!empty($row_account_chart['account_no'])){
				$account_id_transfer = $row_account_chart['account_no'];
			  }else{
				$account_id_transfer = '';
			  }
			$data = array();
			$data['coop_account']['account_description'] = "โอนเงินให้".$account_chart;
			$data['coop_account']['account_datetime'] =  @$row_account_chart['transaction_time'];
			$data['coop_account']['account_number'] = $account_id_transfer;

			$data['coop_account']['ref'] = $loan_id;
			$data['coop_account']['ref_type'] = 'loan_transfer_repayment';
			$data['coop_account']['process'] = 'loan_transfer_repayment';
	
			$i=0;
			if($transfer_type == '1'){

				$data['coop_account_detail'][$i]['account_type'] = 'credit';
				$data['coop_account_detail'][$i]['account_amount'] = @$row_account_chart['loan_request'];
				$data['coop_account_detail'][$i]['account_chart_id'] = '10101001';
			}else{
				$data['coop_account_detail'][$i]['account_type'] = 'debit';
				$data['coop_account_detail'][$i]['account_amount'] = @$row_account_chart['loan_request'];
				$data['coop_account_detail'][$i]['account_chart_id'] = '20105014';
			}

			$i++;
			if($transfer_type == '1'){

				$data['coop_account_detail'][$i]['account_type'] = 'debit';
				$data['coop_account_detail'][$i]['account_amount'] = @$row_account_chart['loan_request'];
				$data['coop_account_detail'][$i]['account_chart_id'] = $account_chart_id;
			}else{
			
				$data['coop_account_detail'][$i]['account_type'] = 'credit';
				$data['coop_account_detail'][$i]['account_amount'] = @$row_account_chart['loan_request'];
				$data['coop_account_detail'][$i]['account_chart_id'] = $account_chart_id;	
			}

		}else{
			echo "fail";
		}

	}

	public function remove_loan_repayment(){
		$id = $_POST['id'];
		$loan_repayment = $this->db->get_where("coop_loan_repayment", array("id" => $id) )->result()[0];

		if($loan_repayment=="" || $loan_repayment->status!=="0"){
			echo "fail";
			exit;
		}

		$this->db->where("id", $id);
		$this->db->delete("coop_loan_repayment");
		echo "success";

	}

	public function manage_guarantor()
	{
		$data = $this->input->post();
		if(!empty($data)){
			$this->save_manage_guarantor($data);
		}
		$arr_data 	= 	array();
		$arr_data['cid']	=	@$this->db->get_where("coop_loan", array("id" => $data['loan_id']))->result()[0]->contract_number;
		$this->libraries->template('loan/manage_guarantor',	$arr_data);
		unset($_POST);
	}

	private function save_manage_guarantor($data){
		if($data['loan_id']==""){
			return false;
		}

		$this->db->trans_start();

		$previous_guarantor = $this->db->get_where("coop_loan_guarantee_person", array("loan_id" => $data['loan_id']));
		//save to history
		$now = date("Y-m-d H:i:s");
		$this->db->select_max('seq_no');
		$seq_no = $this->db->get_where("coop_loan_guarantee_person_history", array("loan_id" => $data['loan_id']))->result()[0]->seq_no;
		$data_history['admin_id'] 		=	$_SESSION['USER_ID'];
		$data_history['loan_id']		= 	$data['loan_id'];
		$data_history['create_date']	= 	$now;
		$data_history['seq_no']			=	($seq_no=="") ? '1' : ($seq_no+1);

		$this->db->insert("coop_loan_guarantee_person_history", $data_history);
		$insert_id = $this->db->insert_id();
		foreach ($previous_guarantor->result_array() as $key => $value) {
			unset($value['id']);
			unset($value['loan_id']);
			$value['history_id']	=	$insert_id;
			$this->db->insert("coop_loan_guarantee_person_history_list", $value);
		}

		$this->db->where("loan_id", $data['loan_id']);
		$this->db->delete("coop_loan_guarantee_person");
		foreach ($data['id_new_guarantor'] as $key => $value) {
			$member_id 		= $data['id_new_guarantor'][$key];
			$amount 		= implode("", explode(",", $data['amount_new_guarantor'][$key]));
			$data_save['guarantee_person_id'] 					= $member_id;
			$data_save['loan_id'] 								= $data['loan_id'];
			$data_save['guarantee_person_amount']				= $amount;
			$data_save['guarantee_person_amount_balance']		= $amount;
			$this->db->insert("coop_loan_guarantee_person", $data_save);
		}
		$this->db->trans_complete();
		unset($_POST);
	}

	private function save_manage_installment($data)
	{
		// var_dump($data);
		// init set
		$this->db->select(array("coop_loan.*", "DAY(approve_date) AS d", "MONTH(approve_date) AS m", "YEAR(approve_date) AS y",
	"(SELECT interest_rate FROM coop_term_of_loan WHERE coop_loan.loan_type = coop_term_of_loan.type_id AND start_date <= CURDATE() ORDER BY start_date DESC limit 1) as interest_rate"));
		$loan_row = $this->db->get_where("coop_loan", array("id" => $data['loan_id']) )->result()[0];
		if($loan_row!=""){

			$this->db->select_max('seq_no');
			$seq_no = $this->db->get_where("coop_loan_period_history", array("loan_id" => $data['loan_id']))->result()[0]->seq_no;
			$data_insert_history['create_date'] = date("Y-m-d H:i:s");
			$data_insert_history['admin_id'] 	= $_SESSION['USER_ID'];
			$data_insert_history['seq_no'] = ($seq_no=="") ? '1' : ($seq_no+1);
			$data_insert_history['pay_type'] = $loan_row->pay_type;
			$data_insert_history['period_amount'] = $loan_row->period_amount;
			$data_insert_history['pay_amount'] = $loan_row->money_per_period;
			$data_insert_history['loan_id'] = $data['loan_id'];

			$this->db->insert("coop_loan_period_history", $data_insert_history);
			$insert_id = $this->db->insert_id();

			$installment = $this->db->get_where("coop_loan_period", array("loan_id" => $data['loan_id']) );

			foreach ($installment->result_array() as $key => $value) {
				unset($value['id']);
				$value['history_id']	=	$insert_id;
				$this->db->insert("coop_loan_period_history_list", $value);
			}
			$this->db->where("loan_id", $data['loan_id']);
			$this->db->delete("coop_loan_period");
		}
		// var_dump($loan);
		$_POST["interest"] 			= 	$loan_row->interest_rate;
		$_POST['loan'] 				= 	$loan_row->loan_amount;
		$_POST["period"]			=	($_POST["new_installment_period_type"] == 1) ? $data['new_period_amount'] : $data['new_period_per_month'];
		$_POST["day"]				=	$loan_row->d;
		$_POST["month"]				=	$loan_row->m-1;
		$_POST["year"]				=	$loan_row->y;
		$_POST["pay_type"]			=	$data['new_installment_type'];
		// $_POST["pay_type"]			=	1;
		// init set

		$interest = (double)$_POST["interest"]; // อัตราดอกเบี้ย
		$loan = (double)$_POST["loan"]; // จำนวนเงินกู้
		$pay_type = $_POST["pay_type"]; // ปรเภท ชำระเท่ากันทุกงวด,ต้นเท่ากันทุกงวด
		$period = (double)$_POST["period"]; // จำนวน งวด  หรือ เงิน แล้วแต่ type
		$day = (double)$_POST["day"];
		$month = (double)$_POST["month"];
		$year  = (double)$_POST["year"];
		$period_type= $_POST["new_installment_period_type"]; // ประเภท งวดหรือจำนวนเงิน
		//1 งวด
		//2 จำนวนเงิน

		if($period_type == '1' && $pay_type=='2'){
			$total_per_period = $loan/$period;

			$date_start = ($year-543)."-".$month."-".$day;
			$date_period_1 = date('Y-m-t',strtotime('+1 month',strtotime($date_start)));
			$diff = date_diff(date_create($date_start),date_create($date_period_1));
			$date_count = $diff->format("%a");
			$date_count = 31;
			$interest_period_1 = ((($loan*$interest)/100)/365)*$date_count;
			$first_period = ($period_type == '1' && $pay_type=='2') ? $total_per_period : $loan / $period;
			//if($interest_period_1 > $total_per_period){
				$per_period = ($loan * ( (6/100) / 12 ))/( 1-pow(1/(1+( (6/100) /12)),$period));
				//$per_period = ceil($interest_period_1/100)*100;
				$period = $per_period;
				$period_type = 2;
			//}
		}else if($period_type == "2"){
			$first_period = $period;
		}else{
			$first_period = $loan / $period;
		}
		// echo $first_period;exit;
		$pay_period = $loan / $period;
		$a = ceil($pay_period/10)*10;

		$daydiff = date('t') - $day;

		// ---------------------------
				$loan_remain = $loan;
				$is_last = FALSE;
				$total_loan_pri = 0;
				$total_loan_int = 0;
				$total_loan_pay = 0;
				$d = $period - 1;

				$peroid_row = array();
				for ($i=1; $i <= $period; $i++) {

					if($loan_remain <= 0 ){ break; }
					if($pay_type == 1) {
						if ($period_type == 1) {
									if ($month > 12) {
											$month = 1;
											$year += 1;
									}
									//$loan_pri = $a;
									$loan_pri = ceil($a/100)*100;
									$nummonth = cal_days_in_month(CAL_GREGORIAN, $month , $year);
									$summonth = $nummonth;
									$daydiff = 31 - $day;
									if ($i == 1) {
										if ($daydiff >= 0) {
												/*if ($day <= 10) {
													$summonth -=  $day;
													$summonth += 1;
												} else if ($day >= 11 && $day <= 31) {*/
													$month += 1;
													if ($month > 12) {
															$month = 1;
															$year += 1;
													}
													$nummonth = cal_days_in_month(CAL_GREGORIAN, $month , $year);
													$summonth = $nummonth;
													$summonth = $daydiff + 31;
												//}
										 }
									}
									$summonth = $this->force_summonth($summonth,$i);
									//$loan_int = $loan_remain * ($interest / (365 / $summonth)) / 100;
									$loan_int = round($loan_remain * ($interest / (365 / $summonth)) / 100);
									if($loan_pri < 0){
										$loan_pri = 0;
									}
									$loan_pay = $loan_pri + $loan_int;
									$loan_remain -= ceil($loan_pri/100)*100;
									//$loan_remain -= $loan_pri;
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
										/*if ($day <= 10) {
											$summonth -=  $day;
											$summonth += 1;
										} else if ($day >= 11 && $day <= 31) {*/
											$month += 1;
											$nummonth = cal_days_in_month(CAL_GREGORIAN, $month , $year);
											$summonth = $nummonth;
											$summonth = $daydiff + 31;
										//}
								 }
							}
							$summonth = $this->force_summonth($summonth,$i);
							//$loan_pri = $period;
							$loan_pri = ceil($period/100)*100;
							//$loan_int = $loan_remain * ($interest / (365 / $summonth)) / 100;
							$loan_int = round($loan_remain * ($interest / (365 / $summonth)) / 100);
							if($loan_pri < 0){
								$loan_pri = 0;
							}
							$loan_pay = $loan_pri + $loan_int;
							//$loan_remain -= $loan_pri;
							$loan_remain -= ceil($loan_pri/100)*100;
					}
				}else if($pay_type == 2) {
						if ($period_type == 1) {
									if ($month > 12) {
											$month = 1;
											$year += 1;
									}
									//$loan_pri = $a;
									$loan_pri = ceil($a/100)*100;
									$nummonth = cal_days_in_month(CAL_GREGORIAN, $month , $year);
									$summonth = $nummonth;
									$daydiff = 31 - $day;
									if ($i == 1) {
										if ($daydiff >= 0) {
												/*if ($day <= 10) {
													$summonth -=  $day;
													$summonth += 1;
												} else if ($day >= 11 && $day <= 31) {*/
													$month += 1;
													if ($month > 12) {
															$month = 1;
															$year += 1;
													}
													$nummonth = cal_days_in_month(CAL_GREGORIAN, $month , $year);
													$summonth = $nummonth;
													$summonth = $daydiff + 31;
												//}
										 }
									}
									$summonth = $this->force_summonth($summonth,$i);
									//$loan_int = $loan_remain * ($interest / (365 / $summonth)) / 100;
									$loan_int = round($loan_remain * ($interest / (365 / $summonth)) / 100);
									$loan_pri = $loan_pri - $loan_int;
									if($loan_pri < 0){
										$loan_pri = 0;
									}
									$loan_pay = $loan_pri + $loan_int;
									$loan_remain -= ceil($loan_pri/100)*100;
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
										/*if ($day <= 10) {
											$summonth -=  $day;
											$summonth += 1;
										} else if ($day >= 11 && $day <= 31) {*/
											$month += 1;
											$nummonth = cal_days_in_month(CAL_GREGORIAN, $month , $year);
											$summonth = $nummonth;
											$summonth = $daydiff + 31;
										//}
								 }
							}
							$summonth = $this->force_summonth($summonth,$i);
							//$loan_pri = $period;
							$loan_pri = ceil($period/100)*100;
							//$loan_int = $loan_remain * ($interest / (365 / $summonth)) / 100;
							$loan_int = round($loan_remain * ($interest / (365 / $summonth)) / 100);
							$loan_pri = $loan_pri - $loan_int;
							if($loan_pri < 0){
								$loan_pri = 0;
							}
							$loan_pay = $loan_pri + $loan_int;
							//$loan_remain -= $loan_pri;
							$loan_remain -= ceil($loan_pri/100)*100;
					}
				}

					if($loan_remain <= 0) {
						$loan_pri += $loan_remain;
						$loan_pay = $loan_pri + $loan_int;
						$loan_remain = 0;
						@$count = $count + 1;
					}

					$sumloan = $loan_remain + $loan_pri;
					$sumloanarr[] = $loan_remain + $loan_pri;
					$sumint[] = $loan_int;

					if ($i == $period) {
						$loan_pri = $sumloanarr[$d];
						$loan_pay = $loan_pri + $loan_int;
					}

					@$total_loan_int += $loan_int;
					//@$total_loan_pri += $loan_pri;
					@$total_loan_pri += ceil($loan_pri/100)*100;
					@$total_loan_pay += $loan_pay;

					//@$total_loan_pri_m += $loan_pri;
					@$total_loan_pri_m += ceil($loan_pri/100)*100;
					@$total_loan_int_m += $loan_int;
					@$total_loan_pay_m += $loan_pay;

					if((int)$month == '2'){
						$nummonth = '28';
					}
					$peroid_row['period_count']				= $i;
					$peroid_row['outstanding_balance']		= $sumloan;
					$peroid_row['date_period']				= ($year)."-".sprintf('%02d',$month)."-".$nummonth;
					$peroid_row['date_count']				= $summonth;
					$peroid_row['interest']					= $loan_int;
					$peroid_row['principal_payment']		= $loan_pri;
					$peroid_row['total_paid_per_month']		= $loan_pay;
					$peroid_row['loan_id']					= $data['loan_id'];
					// echo "<pre>";
					// var_dump($peroid_row);
					// echo "</pre>";
					$this->db->insert("coop_loan_period",$peroid_row);

					if($is_last) {
						break;
					}
					$month++;
				}

				$update_coop_loan['money_per_period']	= $first_period;
				$update_coop_loan['period_amount']		= $i-1;
				$update_coop_loan['updatetimestamp']	= date("Y-m-d H:i:s");
				$update_coop_loan['pay_type']			= $data['new_installment_type'];
				$update_coop_loan['period_type']		= $data['new_installment_period_type'];
				$this->db->where("id", $data['loan_id']);
				$this->db->update("coop_loan", $update_coop_loan);
				// var_dump($first_period);
				// exit;

	}

	private function force_summonth($summonth,$period){
		if($period=='1'){
			$summonth = $summonth-1;
		}else{
			$summonth = $summonth;
		}
		return $summonth;
	}

	public function manage_guarantor_print(){
		$loan_id = $this->uri->segment(3);
		if(!$loan_id){
			exit;
		}

		$this->db->select( array("coop_loan.*", "coop_term_of_loan.type_name") );
		$this->db->join("coop_term_of_loan", "coop_term_of_loan.type_id = coop_loan.loan_type");
		$loan = $this->db->get_where("coop_loan", array("coop_loan.id" => $loan_id))->result()[0];
		$this->db->select( array("coop_mem_apply.*", "CONCAT(prename_short, firstname_th, ' ', lastname_th) AS fullname") );
		$this->db->join("coop_prename", "coop_prename.prename_id = coop_mem_apply.prename_id");
		$member = $this->db->get_where("coop_mem_apply", array("member_id" => $loan->member_id))->result()[0];
		$now = date("Y-m-d H:i:s");
		$this->db->select(array("coop_loan_guarantee_person_history.*", "coop_user.user_name"));
		$this->db->join("coop_user", "coop_user.user_id = coop_loan_guarantee_person_history.admin_id");
		$this->db->order_by("seq_no", "ASC");
		$guarantor_history = $this->db->get_where("coop_loan_guarantee_person_history", array("loan_id" => $loan_id))->result();
		foreach ($guarantor_history as $key => $value) {


			$this->db->select(array("coop_loan_guarantee_person_history_list.guarantee_person_id", "CONCAT(prename_short, firstname_th, ' ', lastname_th) AS fullname", "FORMAT(guarantee_person_amount,2)"));
			$this->db->join("coop_mem_apply", "coop_mem_apply.member_id = coop_loan_guarantee_person_history_list.guarantee_person_id");
			$this->db->join("coop_prename", "coop_prename.prename_id = coop_mem_apply.prename_id");
			$value->guarantor_previous = $this->db->get_where("coop_loan_guarantee_person_history_list", array("history_id" => $value->history_id))->result_array();

			if($key+1 == count($guarantor_history)){
				$this->db->select(array("coop_loan_guarantee_person.guarantee_person_id", "CONCAT(prename_short, firstname_th, ' ', lastname_th) AS fullname", "FORMAT(guarantee_person_amount,2)"));
				$this->db->join("coop_mem_apply", "coop_mem_apply.member_id = coop_loan_guarantee_person.guarantee_person_id");
				$this->db->join("coop_prename", "coop_prename.prename_id = coop_mem_apply.prename_id");
				$value->guarantor_new = $this->db->get_where("coop_loan_guarantee_person", array("loan_id" => $loan_id))->result_array();
			}else{
				$this->db->select(array("coop_loan_guarantee_person_history_list.guarantee_person_id", "CONCAT(prename_short, firstname_th, ' ', lastname_th) AS fullname", "FORMAT(guarantee_person_amount,2)"));
				$this->db->join("coop_mem_apply", "coop_mem_apply.member_id = coop_loan_guarantee_person_history_list.guarantee_person_id");
				$this->db->join("coop_prename", "coop_prename.prename_id = coop_mem_apply.prename_id");
				$value->guarantor_new = $this->db->get_where("coop_loan_guarantee_person_history_list", array("history_id" => $guarantor_history[$key+1]->history_id))->result_array();
			}
			$check_next_guarantor = $this->db->get_where("coop_loan_guarantee_person_history_list");

			// var_dump($value->guarantor_previous);
		}
		$arr_data['guarantor_history'] = $guarantor_history;
		$arr_data['member'] = $member;
		$arr_data['loan']	= $loan;
		// $this->libraries->template('loan/manage_guarantor_print',	$arr_data);
		$this->load->library('table');
		$arr_data['fn_table'] = $this->table;
		$this->preview_libraries->template_preview('loan/manage_guarantor_print', $arr_data);
	}

	public function manage_installment()
	{
		$data = $this->input->post();
		if(!empty($data)){
			$data['new_period_per_month'] = implode("", explode(",", $data['new_period_per_month']));
			$this->save_manage_installment($data);
		}
		$arr_data 	= 	array();
		$arr_data['cid']	=	@$this->db->get_where("coop_loan", array("id" => $data['loan_id']))->result()[0]->contract_number;
		$this->libraries->template('loan/manage_installment',	$arr_data);

	}


	public function manage_installment_print(){
		$loan_id = $this->uri->segment(3);
		if(!$loan_id){
			exit;
		}

		$this->db->select( array("coop_loan.*", "coop_term_of_loan.type_name") );
		$this->db->join("coop_term_of_loan", "coop_term_of_loan.type_id = coop_loan.loan_type");
		$loan = $this->db->get_where("coop_loan", array("coop_loan.id" => $loan_id))->result()[0];
		$this->db->select( array("coop_mem_apply.*", "CONCAT(prename_short, firstname_th, ' ', lastname_th) AS fullname") );
		$this->db->join("coop_prename", "coop_prename.prename_id = coop_mem_apply.prename_id");
		$member = $this->db->get_where("coop_mem_apply", array("member_id" => $loan->member_id))->result()[0];
		$now = date("Y-m-d H:i:s");

		$this->db->select(
			array("CASE pay_type
					WHEN '1' THEN
						'ต้น'
					WHEN '2' THEN
						'ยอดเท่ากัน'
					END AS col1",
					"pay_amount as col2",
					"period_amount as col3",
					"seq_no",
					"create_date",
					"coop_user.user_name"
		));
		$this->db->order_by("seq_no", "ASC");
		$this->db->join("coop_user", "coop_user.user_id = coop_loan_period_history.admin_id");
		$period_history = $this->db->get_where("coop_loan_period_history", array("loan_id" => $loan_id))->result();
		foreach ($period_history as $key => $value) {

			$value->installment_previous = array($value->col1, number_format($value->col2,2), $value->col3);
			if($key+1 == count($period_history)){
				$this->db->select( array("CASE pay_type
				WHEN '1' THEN
					'ต้นเท่ากัน'
				WHEN '2' THEN
					'ยอดเท่ากัน'
				END AS col1",
				"FORMAT(money_per_period,2) as col2",
				"period_amount as col3") );
				$value->installment_new = $this->db->get_where("coop_loan", array("id" => $loan_id))->result_array()[0];
			}else{
				$value->installment_new =  array($period_history[$key+1]->col1, number_format($period_history[$key+1]->col2,2), $period_history[$key+1]->col3);
			}

		}
		// echo "<pre>";
		// var_dump($period_history);
		// exit;
		$arr_data['period_history'] = $period_history;
		$arr_data['member'] = $member;
		$arr_data['loan']	= $loan;
		// $this->libraries->template('loan/manage_guarantor_print',	$arr_data);
		$this->load->library('table');
		$arr_data['fn_table'] = $this->table;
		$this->preview_libraries->template_preview('loan/manage_installment_print', $arr_data);
	}

	public function check_guarantee()
	{
		$this->load->helper('cookie');
		$save_status = $this->uri->segment(3);
		if($save_status=="success"){
			$loan_id = $this->uri->segment(4);

			$contract_number = "";
			if($loan_id != ""){
				$loan = $this->db->get_where("coop_loan", array("id" => $loan_id))->result()[0];
				$contract_number = $loan->contract_number;
			}
			CI_Input::set_cookie("save_status", '1', 50);
			CI_Input::set_cookie("contract_number", $contract_number, 50);
			header("Location: ".base_url('loan/check_guarantee'));
		}
		$arr_data = array();

		$arr_data['save_status'] = @$_COOKIE['save_status'];
		$arr_data['contract_number'] = @$_COOKIE['contract_number'];
		$arr_data['bank'] = $this->db->get("coop_bank")->result();

		$this->libraries->template('loan/check_guarantee',$arr_data);
	}

	public function calc_atm_csv(){
		$member_id = sprintf("%06d", @$_GET['member_id']);
		$loan_atm_id = @$this->db->get_where("coop_loan_atm", array(
			"member_id" => $member_id,
			"loan_atm_status" => 1
		))->result_array()[0]['loan_atm_id'];
		if(!$loan_atm_id)
			die("no atm id;");
		$date_month_end = date('Y-m-t',strtotime((@$_GET['year']-543).'-'.sprintf("%02d",@$_GET['month']).'-01'));
		$cal_loan_interest = array();
		$cal_loan_interest['loan_atm_id'] = $loan_atm_id;
		$cal_loan_interest['date_interesting'] = $date_month_end;
		$this->loan_libraries->cal_atm_interest_report_test($cal_loan_interest,"echo", array("month"=>@$_GET['month'], "year" => (@$_GET['year']-543) ));
	}

	public function step_logic(){
		$this->libraries->template('loan/step_logic');
	}
}
