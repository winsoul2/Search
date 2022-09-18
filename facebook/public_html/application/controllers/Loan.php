<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Loan extends CI_Controller {
	function __construct()
	{
		parent::__construct();
		$this->load->model("Installment_Model", "installment");
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
			$this->db->where("member_id = '".$member_id."' AND share_status IN('1','2','5')");
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
				't3.loan_group_id',
				't1.loan_amount',
				't1.loan_amount_balance',
				't1.guarantee_for_id',
				'IF(t1.loan_type_input	= "1","ระบบกู้เงินออนไลน์",t2.user_name) AS user_name',
				't1.loan_status',
				't1.id',
				't1.loan_type',
				't4.id as transfer_id',
				't4.file_name as transfer_file',
				't5.petition_file',
				't9.prename_full as com_prename',
				't8.firstname_th as com_firstname',
				't8.lastname_th as com_lastname'
			));
			$this->db->from('coop_loan as t1');
			$this->db->join('coop_loan_name as t3','t1.loan_type = t3.loan_name_id','inner');
			$this->db->join('coop_loan_type as t5','t3.loan_type_id = t5.id','inner');
			$this->db->join('coop_user as t2','t1.admin_id = t2.user_id','left');
			$this->db->join("(SELECT * FROM coop_loan_transfer WHERE transfer_status != '2' GROUP BY loan_id) as t4","t1.id = t4.loan_id",'left');
			$this->db->join('coop_loan_guarantee_compromise as t6', "t1.id = t6.loan_id", "left");
			$this->db->join('coop_loan_compromise as t7', "t6.compromise_id = t7.id", "left");
			$this->db->join('coop_mem_apply as t8', "t7.member_id = t8.member_id", "left");
			$this->db->join('coop_prename as t9', "t8.prename_id = t9.prename_id", "left");
			$this->db->where("t1.member_id = '".$member_id."' ");
			$this->db->order_by("t1.id DESC");
			$rs_loan = $this->db->get()->result_array();
			$arr_data['rs_loan'] = $rs_loan;
			// echo $this->db->last_query();exit;
            if($_GET['dev'] == 'dev'){
                echo $this->db->last_query();exit;
            }
			//////////////////////////////////////////////////////
			$prev_loan_active_arr = array();
			$i=0;
			$this->db->select(array(
				'*',
                '(select loan_name from coop_loan_name where loan_name_id = t1.loan_type) as loan_name'
			));
			$this->db->from('coop_loan as t1');
			$this->db->where("t1.member_id = '".$member_id."' AND t1.loan_status = '1' AND t1.loan_amount_balance <> 0");
			$prev_loan_active = $this->db->get()->result_array();
			//echo $this->db->last_query();exit;
			foreach($prev_loan_active as $key => $value){
				$prev_loan_active_arr[$i]['id'] = $value['id'];
				$prev_loan_active_arr[$i]['loan_name'] = $value['loan_name'];
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
			$this->db->where("t1.member_id = '".$member_id."' AND t1.loan_atm_status = '1' AND (t1.total_amount_approve - t1.total_amount_balance) <> 0");
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
				$interest_atm = $this->loan_libraries->cal_atm_interest_report_test($cal_loan_interest,"echo", array("month"=> date("m"), "year" => date("Y") ), false, true )['interest_month'];

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

			//ค่าใช้จ่าย ของรายการกู้ ล่าสุด
			$this->db->select("id AS loan_id ,member_id,createdatetime")->from("coop_loan")->where("member_id = '".$member_id."'  AND loan_status NOT IN (0,5)")->order_by("createdatetime DESC")->limit(1);
			$loan_last = $this->db->get()->row_array();
			
			$rs_loan_cost_mod = $this->db->select('t1.outgoing_name,t2.loan_cost_amount,t2.loan_cost_code')
			->from('coop_outgoing AS t1')
			->join("coop_loan_cost_mod AS t2","t1.outgoing_code = t2.loan_cost_code","left")
			->where("t2.loan_id = '".$loan_last['loan_id']."'")
			->order_by("t1.outgoing_id ASC")
			->get()->result_array();
			foreach($rs_loan_cost_mod as $key => $val){
				@$arr_data['coop_loan_cost'][$val['loan_cost_code']] = $val['loan_cost_amount'];
			}

			//บัญชีเงินฝาก
			$this->db->where("mem_id", $member_id);
			$this->db->where("account_status", "0");
			$arr_data['saving_list'] = $this->db->get_where("coop_maco_account")->result_array();
			foreach($arr_data['saving_list'] as $key => $val){
				$this->db->select("transaction_balance");
				$this->db->where("account_id", $val['account_id']);
				$this->db->order_by("transaction_time desc, transaction_id desc");
				$this->db->limit(1);
				$arr_data['saving_list'][$key]['transaction_balance'] = $this->db->get("coop_account_transaction")->row_array()['transaction_balance'];
			}
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
		$this->db->where("loan_type_status = 1");
		$rs_loan_type = $this->db->get()->result_array();
		$arr_data['rs_loan_type'] = $rs_loan_type;

		$this->db->select(array(
			'loan_deduct_list_code','loan_deduct_list','deduct_type','loan_deduct_show', 'loan_deduct_status'
		));
		$this->db->from('coop_loan_deduct_list');
		$this->db->where("loan_deduct_list_code != 'deduct_pay_prev_loan' AND deduct_type='deduct' AND loan_deduct_status = 1");
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
			'loan_deduct_list_code','loan_deduct_list','deduct_type','loan_deduct_show', 'loan_deduct_status'
		));
		$this->db->from('coop_loan_deduct_list');
		$this->db->where("loan_deduct_list_code != 'deduct_pay_prev_loan' AND deduct_type='buy' AND loan_deduct_status = 1");
		$this->db->order_by('sort asc');
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

		//ระบบประกันชีวิต
		//ชสอ. จากการนำเข้ามูล
		$row_cremation_1 = $this->db->select('t1.import_amount_balance')
		->from('coop_import_data_cremation AS t1')
		->where("t1.member_id = '{$member_id}' AND t1.import_cremation_type = '1'")
		->order_by("import_cremation_year DESC")
		->limit(1)
		->get()->result_array();
		$cremation_balance_1 = @$row_cremation_1[0]['import_amount_balance']; 
		$arr_data['cremation_balance_1'] = @$cremation_balance_1;
		
		//สสอค. จากการนำเข้ามูล
		$row_cremation_2 = $this->db->select('t1.import_amount_balance')
		->from('coop_import_data_cremation AS t1')
		->where("t1.member_id = '{$member_id}' AND t1.import_cremation_type = '2'")
		->order_by("import_cremation_year DESC")
		->limit(1)
		->get()->result_array();
		$cremation_balance_2 = @$row_cremation_2[0]['import_amount_balance']; 
		$arr_data['cremation_balance_2'] = @$cremation_balance_2;

		//รายการค่าใข้จ่าย
        $this->db->select('*')->from('coop_outgoing')->where('outgoing_status = 1')->order_by('outgoing_no asc');
        $arr_data['outgoings'] = $this->db->get()->result_array();

        //ควบคุมการแสดงผลการจ่ายเงินกู้
        $this->db->select('*')->from('coop_setting_transfer')->where('transfer_status = 1')->order_by('id asc');
        $arr_data['transfers'] = $this->db->get()->result_array();

        //เช็ค
		$arr_data['cheque_bank_title'] = $this->db->select('*')->from('coop_cheque_title')->get()->result_array();

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
			if(@$_GET['loan_status'] == '1'){
				$where .= " AND coop_loan.loan_status IN('1','4') ";
			}else{
				$where .= " AND coop_loan.loan_status = '".$_GET['loan_status']." '";
			}
		}else{
			$where .= " AND coop_loan.loan_status IN('0','1','4','5') ";
		}

		$order_by = 'coop_loan.createdatetime DESC,coop_loan.id ASC';

		$key_search = isset($_GET['search_key_date']) ? $_GET['search_key_date'] : 'approve_date';

		if($_GET['start_date']!=''){
			$approve_date_arr = explode('/',$_GET['start_date']);
			$approve_day = stripslashes($approve_date_arr[0]);
			$approve_month = stripslashes($approve_date_arr[1]);
			$approve_year = stripslashes($approve_date_arr[2]);
			$_GET['approve_date'] = $approve_day."/".$approve_month."/".$approve_year;
			$approve_year -= 543;
			$approve_date = $approve_year.'-'.$approve_month.'-'.$approve_day;
			$where .= " AND coop_loan.{$key_search} >= '".$approve_date." 00:00:00.000'";
			$order_by = "coop_loan.{$key_search} ASC,coop_loan.id ASC";
		}
		if($_GET['thru_date']!=''){
			$thru_date_arr = explode('/',$_GET['thru_date']);
			$thru_day = stripslashes($thru_date_arr[0]);
			$thru_month = stripslashes($thru_date_arr[1]);
			$thru_year = stripslashes($thru_date_arr[2]);
			$_GET['thru_date'] = $thru_day."/".$thru_month."/".$thru_year;
			$thru_year -= 543;
			$thru_date = $thru_year.'-'.$thru_month.'-'.$thru_day;
			$where .= " AND coop_loan.{$key_search} <= '".$thru_date." 23:59:59.000'";
			$order_by = "coop_loan.{$key_search} ASC,coop_loan.id ASC";
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
		$this->paginater_all->select('coop_loan.*, IF(coop_loan.loan_type_input	= "1","ระบบกู้เงินออนไลน์",coop_user.user_name) AS user_name, coop_loan_type.petition_file, coop_loan_name.loan_group_id');
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
		if(@$_GET['dev'] == 'dev'){
			echo $this->db->last_query();exit;
		}
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
		// $this->db->trans_start();

			$log_file = FCPATH."/application/logs/".sprintf("approved_contract_number_%s.log", date("Ymd"));
			$arr_date_approve = explode('/',@$_GET['date_approve']);
			$date_approve = ($arr_date_approve[2]-543)."-".$arr_date_approve[1]."-".$arr_date_approve[0];
			$date_approve_time = (@$_GET['date_approve'] != '')?@$date_approve." ".date('H:i:s'):date('Y-m-d H:i:s');
			$date_approve = (@$_GET['date_approve'] != '')?@$date_approve:date('Y-m-d');
			$year_approve = (@$_GET['date_approve'] != '')?($arr_date_approve[2]-543):date('Y');
			$month_approve = (@$_GET['date_approve'] != '')?($arr_date_approve[1]):date('m');
			$date_receive_money = $date_approve;
			$date_last_interest = $date_approve;

//			echo 'date_approve='.$date_approve.'<br>';
//			 echo"<pre>";print_r($_GET);exit;
//			 var_dump(@$_POST);
//			 exit;
			$this->db->select(array('t1.*','t3.loan_type_code'));
			$this->db->from("coop_loan as t1");
			$this->db->join("coop_loan_name as t2",'t1.loan_type = t2.loan_name_id','inner');
			$this->db->join("coop_loan_type as t3",'t2.loan_type_id = t3.id','inner');
			$this->db->where("t1.id = '".@$_GET['loan_id']."'");
			$rs_loan = $this->db->get()->result_array();
			$rs_loan = $rs_loan[0];
			$member_id = @$rs_loan['member_id'];
			$loan_amount = $rs_loan['loan_amount'];
			$transfer_type = $rs_loan['transfer_type'];

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
			$data_insert['loan_id'] = @$_GET['loan_id'];
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
			
			if($_GET['status_to']=='1'){
				//$date_approve_time
				$yymm = ($year_approve+543).$month_approve;



				$receipt_arr = array();
				$this->db->select(array('t1.*', 't2.date_receive_money'));
				$this->db->from("coop_loan_prev_deduct as t1");
				$this->db->join('coop_loan_deduct_profile as t2', "t1.loan_id=t2.loan_id", 'inner');
				$this->db->where("t1.loan_id = '".@$_GET['loan_id']."'");
				$row = $this->db->get()->result_array();
				//echo"<pre>";print_r($row);exit;
				$r=0;

				$deduct = $this->db->where(array("loan_id" => $_GET['loan_id']))->get("coop_loan_deduct")->result_array();
				$deductTotal = array_sum(array_column($deduct, 'loan_deduct_amount'));
				$receipt_id = "";
				if($deductTotal){
					$receipt_id = $this->receipt_model->add_receipt($date_approve, $rs_loan['transfer_type'] == '0' ? 0 : 1);
				}
				foreach($row as $key => $value){
					//update หนี้ห้อย-------------------
					$extra_debt_amount	= 0;//หนี้ห้อย
					$date_receive_money = $value['date_receive_money'];
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

							$loan_amount = $ref_loan['loan_amount_balance'];//เงินกู้
							$loan_type = $ref_loan['loan_type'];//ประเภทเงินกู้ใช้หา เรทดอกเบี้ย
							$loan_id = $value['ref_id'];//ใช้หาเรทดอกเบี้ยใหม่ 26/5/2562

							$date1 = date("Y-m-d", strtotime($ref_loan['date_last_interest']));

							//$date2 = date("Y-m-d");//วันที่คิดดอกเบี้ย now
							//$date2 = $date_approve;//วันที่คิดดอกเบี้ย now
							$date2 = date("Y-m-d", strtotime($value['date_receive_money']));//วันที่คิดดอกเบี้ย now

							$interest_loan = $this->loan_libraries->calc_interest_loan($loan_amount, $loan_id, $date1, $date2);
							$interest_loan = round($interest_loan);

							$receipt_arr[$r]['receipt_id'] = $receipt_id;
							$receipt_arr[$r]['member_id'] = $member_id;
							$receipt_arr[$r]['loan_id'] = $value['ref_id'];
							$receipt_arr[$r]['account_list_id'] = '15';
							$receipt_arr[$r]['principal_payment'] = $ref_loan['loan_amount_balance'] - @$extra_debt_amount;
							$receipt_arr[$r]['interest'] = $interest_loan;
							$receipt_arr[$r]['total_amount'] = $ref_loan['loan_amount_balance'] - @$extra_debt_amount + $interest_loan;
							$receipt_arr[$r]['payment_date'] = $date2;
							$receipt_arr[$r]['createdatetime'] = $date_approve_time;
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
							$loan_transaction['transaction_datetime'] = $date2;
							$loan_transaction['receipt_id'] = $receipt_id;
							$this->loan_libraries->loan_transaction($loan_transaction);

							$data_insert = array();
							$data_insert['date_last_interest'] = $date2;
							$this->db->where('id',$value['ref_id']);
							$this->db->update('coop_loan',$data_insert);

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
							$receipt_arr[$r]['payment_date'] = $date_receive_money;
							$receipt_arr[$r]['createdatetime'] = $date_approve_time;
							$receipt_arr[$r]['loan_amount_balance'] = $loan_amount_balance;
							$receipt_arr[$r]['transaction_text'] = 'หักกลบเงินกู้เลขที่สัญญา '.$row_loan['contract_number'];
							$receipt_arr[$r]['deduct_type'] = 'principal';
							$r++;
							$loan_transaction = array();
							$loan_transaction['loan_id'] = $value['ref_id'];
							$loan_transaction['loan_amount_balance'] = $loan_amount_balance;
							$loan_transaction['transaction_datetime'] = $date_receive_money;
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
							$atm_transaction['transaction_datetime'] = $date_receive_money;
							$atm_transaction['receipt_id'] = $receipt_id;
							$this->loan_libraries->atm_transaction($atm_transaction);

							$receipt_arr[$r]['receipt_id'] = $receipt_id;
							$receipt_arr[$r]['member_id'] = $member_id;
							$receipt_arr[$r]['loan_atm_id'] = $value['ref_id'];
							$receipt_arr[$r]['account_list_id'] = '31';
							$receipt_arr[$r]['principal_payment'] = $value['pay_amount'];
							$receipt_arr[$r]['total_amount'] = $value['pay_amount'];
							$receipt_arr[$r]['payment_date'] = $date_receive_money;
							$receipt_arr[$r]['createdatetime'] = $date_approve_time;
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
				foreach($row_deduct as $key => $value) {
                    if ($value['loan_deduct_list_code'] == 'deduct_blue_deposit' && $value['loan_deduct_amount'] > 0) {
                        //เช็คสมุดเงินฝากสีน้ำเงิน
                        $this->db->select(array('coop_maco_account.account_id', 'coop_maco_account.mem_id', 'coop_deposit_type_setting.type_name'));
                        $this->db->from('coop_maco_account');
                        $this->db->join("coop_deposit_type_setting", "coop_maco_account.type_id = coop_deposit_type_setting.type_id", "inner");
                        $this->db->where("
							coop_maco_account.mem_id = '" . $member_id . "'
							 AND coop_maco_account.account_status = '0'
							AND coop_deposit_type_setting.deduct_loan = '1'
						");
                        $this->db->limit(1);
                        $rs_account = $this->db->get()->result_array();
                        $row_account = @$rs_account[0];
                        if (empty($rs_account)) {
                            $this->db->select(array('type_id', 'type_name', 'type_code'));
                            $this->db->from('coop_deposit_type_setting');
                            $this->db->where("deduct_loan = '1'");
                            $this->db->limit(1);
                            $rs_deduct_loan = $this->db->get()->result_array();
                            $row_deduct_loan = @$rs_deduct_loan[0];

                            $this->db->select('account_id');
                            $this->db->from('coop_maco_account');
                            $this->db->where("type_id = '" . $row_deduct_loan['type_id'] . "' AND account_status = '0'");
                            $this->db->order_by("account_id DESC");
                            $this->db->limit(1);
                            $row = $this->db->get()->result_array();
                            if (!empty($row)) {
                                $auto_account_id = str_replace("001" . $row_deduct_loan['type_code'], '', $row[0]['account_id']);
                                $auto_account_id = (int)$auto_account_id;
                                $auto_account_id = $auto_account_id + 1;
                            } else {
                                $auto_account_id = 1;
                            }
                            $acc_id = "001" . $row_deduct_loan['type_code'] . sprintf("%06d", @$auto_account_id);
                            $account_id = @$acc_id;
                            //echo '<pre>'; print_r($row_deduct_loan); echo '</pre>';
                            $this->db->select('*');
                            $this->db->from('coop_mem_apply');
                            $this->db->where("member_id = '" . $member_id . "'");
                            $rs_member = $this->db->get()->result_array();
                            $row_member = @$rs_member[0];

                            //start เช็คบัญชีในตาราง coop_account_transaction
                            $this->db->select('account_id');
                            $this->db->from('coop_account_transaction');
                            $this->db->where("account_id = '" . $account_id . "'");
                            $this->db->order_by("transaction_time DESC, transaction_id DESC");
                            $this->db->limit(1);
                            $check_account_transaction = $this->db->get()->result_array();
                            if (!empty($check_account_transaction)) {
                                //หา account_id อีกรอบ
                                $this->db->select('account_id');
                                $this->db->from('coop_account_transaction');
                                $this->db->where("account_id LIKE '001" . $row_deduct_loan['type_code'] . "%'");
                                $this->db->order_by("transaction_time DESC, transaction_id DESC");
                                $this->db->limit(1);
                                $last_account_id = $this->db->get()->result_array();
                                $auto_account_id = str_replace("001" . $row_deduct_loan['type_code'], '', $last_account_id[0]['account_id']);
                                $auto_account_id = (int)$auto_account_id;
                                $auto_account_id = $auto_account_id + 1;
                                $account_id = "001" . $row_deduct_loan['type_code'] . sprintf("%06d", @$auto_account_id);
                            }
                            //end เช็คบัญชีในตาราง coop_account_transaction

                            $data_insert = array();
                            $data_insert['account_id'] = @$account_id;
                            $data_insert['mem_id'] = $member_id;
                            $data_insert['member_name'] = $row_member['firstname_th'] . " " . $row_member['lastname_th'];
                            $data_insert['account_name'] = $row_member['firstname_th'] . " " . $row_member['lastname_th'];
                            $data_insert['created'] = date('Y-m-d H:i:s');
                            $data_insert['account_amount'] = '0';
                            $data_insert['book_number'] = '1';
                            $data_insert['type_id'] = $row_deduct_loan['type_id'];
                            $data_insert['account_status'] = '0';
                            $this->db->insert('coop_maco_account', $data_insert);
                            //$account_id = $this->db->insert_id();
                            //$account_id = @$acc_id;
                        } else {
                            $account_id = $row_account['account_id'];
                        }

                        $this->db->where("account_id = '" . $account_id . "' AND loan_id ='" . $_GET['loan_id'] . "'");
                        $this->db->delete("coop_account_transaction");

                        $transaction_list = 'XD';
                        $transaction_deposit = $value['loan_deduct_amount'];

                        $this->db->select('*');
                        $this->db->from('coop_account_transaction');
                        $this->db->where("account_id = '" . $account_id . "'");
                        $this->db->order_by('transaction_time DESC, transaction_id DESC');
                        $this->db->limit(1);
                        $row = $this->db->get()->result_array();
                        if (!empty($row)) {
                            $balance = @$row[0]['transaction_balance'];
                            $balance_no_in = @$row[0]['transaction_no_in_balance'];
                        } else {
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
                            $receipt_arr[$r]['payment_date'] = $date_receive_money;
                            $receipt_arr[$r]['createdatetime'] = date('Y-m-d H:i:s');
                            $receipt_arr[$r]['loan_amount_balance'] = $balance;
                            $receipt_arr[$r]['transaction_text'] = 'ฝากเงินเลขที่บัญชี ' . $account_id;
                            $receipt_arr[$r]['deduct_type'] = 'all';
                            $r++;
                        }

                    } else if ($value['loan_deduct_list_code'] == 'deduct_share' && $value['loan_deduct_amount'] > 0) {
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
                        $data_insert['share_date'] = $date_approve_time;
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
                        $receipt_arr[$r]['createdatetime'] = $date_approve_time;
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
                        $receipt_arr[$r]['createdatetime'] = $date_approve_time;
                        $receipt_arr[$r]['loan_amount_balance'] = @$row_share['share_collect_value'] + @$value['loan_deduct_amount'];
                        $receipt_arr[$r]['transaction_text'] = 'ซื้อหุ้นจากการกู้';
                        $receipt_arr[$r]['deduct_type'] = 'all';
                        $r++;
                    }else if($value['loan_deduct_list_code'] == 'deduct_before_interest' && $value['loan_deduct_amount'] > 0){
                        $receipt_arr[$r]['receipt_id'] = $receipt_id;
                        $receipt_arr[$r]['member_id'] = $member_id;
                        $receipt_arr[$r]['loan_id'] = $_GET['loan_id'];
                        $receipt_arr[$r]['account_list_id'] = $value['account_list_id'];
                        $receipt_arr[$r]['interest'] = $value['loan_deduct_amount'];
                        $receipt_arr[$r]['principal_payment'] = 0;
                        $receipt_arr[$r]['total_amount'] = $value['loan_deduct_amount'];
                        $receipt_arr[$r]['payment_date'] = $date_receive_money;
                        $receipt_arr[$r]['createdatetime'] = $date_approve_time;
                        $receipt_arr[$r]['loan_amount_balance'] = $loan_amount;

                        $loan_type_name = $this->loan_libraries->getLoanTypeNameByLoanId($_GET['loan_id']);
                        $loan_name = $this->loan_libraries->getLoanName($_GET['loan_id']);

                        $receipt_arr[$r]['transaction_text'] = 'ชำระดอกเบี้ย'.$loan_type_name."(".$loan_name.")";
                        $receipt_arr[$r]['deduct_type'] = 'interest';
                        $r++;

						$loan_transaction = array();
						$loan_transaction['loan_id'] = $_GET['loan_id'];
						$loan_transaction['loan_amount_balance'] = $loan_amount;
						$loan_transaction['transaction_datetime'] = date('Y-m-d', strtotime($date_receive_money));
						$loan_transaction['receipt_id'] = $receipt_id;
						$this->loan_libraries->loan_transaction($loan_transaction);

						$data_insert = array();
						$data_insert['approve_date'] = $date_approve_time;
						$data_insert['date_last_interest'] = $date_last_interest = date("Y-m-t",strtotime($date_receive_money));
						$this->db->where('id', @$_GET['loan_id']);
						$this->db->update('coop_loan', $data_insert);


                    }else{
						if($value['loan_deduct_amount']>0){
							$receipt_arr[$r]['receipt_id'] = $receipt_id;
							$receipt_arr[$r]['member_id'] = $member_id;
							$receipt_arr[$r]['account_list_id'] = $value['account_list_id'];
							$receipt_arr[$r]['principal_payment'] = $value['loan_deduct_amount'];
							$receipt_arr[$r]['total_amount'] = $value['loan_deduct_amount'];
							$receipt_arr[$r]['payment_date'] = $date_receive_money;
							$receipt_arr[$r]['createdatetime'] = $date_approve_time;
							$receipt_arr[$r]['loan_amount_balance'] = '';
							$receipt_arr[$r]['transaction_text'] = $value['account_list'];
							$receipt_arr[$r]['deduct_type'] = 'all';
							$r++;
						}
					}
				}
				//echo"<pre>";print_r($receipt_arr);exit;
				$data = array();
				$data['coop_account']['account_description'] = "รายการรับชำระเงิน";
				$data['coop_account']['account_datetime'] = $date_approve_time;
				$data['coop_account']['ref'] = @$_GET['loan_id'];
				$data['coop_account']['ref_type'] = 'loan_refinance';
				$data['coop_account']['process'] = 'refinance';

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
					$data_insert['period_count'] = !empty($value['loan_id']) && !empty($value['principal_payment']) ? $this->loan_libraries->getPeriodCount($value['loan_id'], $value['payment_date']) : "";
					$this->db->insert('coop_finance_transaction', $data_insert);
					$sum_count += @$value['total_amount'];
					$sum_interest += @$value['interest'];

					$this->db->select(array('t1.*'));
					$this->db->from("coop_loan as t1");
					$this->db->where("
						t1.id = '".$value['loan_id']."'
					");
					$row_loan = $this->db->get()->result_array();
					$row_loan = @$row_loan[0];

					if(!empty($row_loan['loan_type']) && $value['account_list_id'] != '15' ){
						$match_id = $value['account_list_id'];
						$match_type = 'loan';

					}else{
						$match_id = $value['account_list_id'];
						$match_type = 'account_list';
					}

					$this->db->select(array(
						't1.account_chart_id',
						't2.account_chart'
					   ));
					   $this->db->from('coop_account_match as t1');
					   $this->db->join('coop_account_chart as t2','t1.account_chart_id = t2.account_chart_id','left');
					   $this->db->where("
						t1.match_type = '{$match_type}'
						AND t1.match_id = '{$match_id}'
					   ");
					// echo "".$this->db->get_compiled_select(null, false)."<br><br><br><br>";

					   $row_account_match = $this->db->get()->result_array();
					   $row_account_match = @$row_account_match[0];
					   $account_chart_id = @$row_account_match['account_chart_id'];

					$data['coop_account_detail'][$key]['account_type'] = 'credit';
					$data['coop_account_detail'][$key]['account_amount'] = @$value['total_amount'];
					$data['coop_account_detail'][$key]['account_chart_id'] = $account_chart_id;



				}

				
				$data['coop_account_detail'][10101001]['account_type'] = 'debit';
				$data['coop_account_detail'][10101001]['account_amount'] = $sum_count;
				$data['coop_account_detail'][10101001]['account_chart_id'] = '10101001';

				$data['coop_account_detail'][40101001]['account_type'] = 'credit';
				$data['coop_account_detail'][40101001]['account_amount'] = $sum_interest;
				$data['coop_account_detail'][40101001]['account_chart_id'] = '40101001';
				
				$this->account_transaction->account_process($data);
				
				if($sum_count>0){
				    if($transfer_type == '0'){
                        $pay_type = '0';
                    }else{
                        $pay_type = '1';
                    }
					$data_insert = array();
					$data_insert['member_id'] = @$member_id;
					$data_insert['admin_id'] = @$_SESSION['USER_ID'];
					$data_insert['sumcount'] = $sum_count;
					$data_insert['pay_type'] = $pay_type; //0=เงินสด 1=โอนเงิน
					$this->db->where("receipt_id", $receipt_id);
					$this->db->set($data_insert);
					$this->db->update('coop_receipt');

					$data_insert = array();
					$data_insert['deduct_receipt_id'] = $receipt_id;
					$data_insert['period_now'] = 0;
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

				if((int)$month_now >= (int)$month_account && (int)$month_account != 1){
					$year = (date('Y')+543)+1;
				}else{
					$year = (date('Y')+543);
				}
				$year_short = substr($year,2,2);
				$new_contact_number = '';

				$this->db->select('*');
				$this->db->from("coop_term_of_loan");
				$this->db->where("type_id = '".@$rs_loan['loan_type']."' AND start_date <= '".$date_approve."'");
				$this->db->order_by('start_date DESC');
				$this->db->limit(1);
				$rs_term_of_loan = $this->db->get()->result_array();
				$row_term_of_loan = @$rs_term_of_loan[0];
				$new_contact_number = $year;

				writeToLog("search: {\"id\":\"{$_GET['loan_id']}\", \"loan_type\": \"{$rs_loan['loan_type']}\", \"start_date\":\"{$date_approve}}\" ", $log_file, true);
				$contact_number_now = $this->loan_libraries->get_contract_number($year, $rs_loan['loan_type']);
				$new_contact_number .= sprintf("% 06d",$contact_number_now);
				writeToLog("result: ".json_encode(array('id' => $_GET['loan_id'], 'number_contract' => $new_contact_number)), $log_file, true);
				//$new_contact_number = $new_contact_number."/".(date('Y')+543);
				if(empty($rs_loan["contract_number"])) {
					$data_insert['contract_number'] = @$new_contact_number;
				}
				//echo $new_contact_number;exit;

			}

			//Check if compromise loan change status_to to 8 if loanee got responsibility
			$compormise_detail = $this->db->select("*")
											->from("coop_loan_guarantee_compromise")
											->where("loan_id = '".$_GET['loan_id']."' AND type in (3,4)")
											->get()->row();

			if(!empty($compormise_detail)) {
				$data_insert['loan_status'] = 8;
			} else {
				$data_insert['loan_status'] = @$_GET['status_to'];
			}

			$data_insert['approve_date'] = $date_approve_time;
			$data_insert['date_last_interest'] = $date_last_interest;
			$this->db->where('id', @$_GET['loan_id']);
			$this->db->update('coop_loan', $data_insert);
			writeToLog("update: ".json_encode($data_insert), $log_file, true);
			
			//อัพเดตเลขที่สัญญาในระบบประกัน
			$life_insurance = $this->db->select("*")
											->from("coop_life_insurance")
											->where("loan_id = '".$_GET['loan_id']."' AND insurance_status = 0")
											->get()->row();
			if(!empty($life_insurance)) {
				$data_insert = array();
				$data_insert['insurance_date'] = $date_receive_money;
				$data_insert['contract_number'] = @$new_contact_number;
				$data_insert['insurance_status'] = 1;
				$this->db->where("member_id = '".@$member_id."' AND loan_id = '".$_GET['loan_id']."'");
				$this->db->update('coop_life_insurance',$data_insert);
				
				if($_GET['status_to']=='5'){
					//ลบข้อมูลระบบประกันชีวิต เมื่อไม่อนุมัติ
					$this->db->where("member_id = '".@$member_id."' AND loan_id = '".$_GET['loan_id']."'");
					$this->db->delete("coop_life_insurance");
				}
			}	
			
			//$this->center_function->toast('บันทึกข้อมูลเรียบร้อยแล้ว');
			//echo "<script> document.location.href='".base_url(PROJECTPATH.'/loan/loan_approve')."' </script>";
			echo 'บันทึกข้อมูลเรียบร้อยแล้ว';
			exit;
	}

	function loan_transfer(){


		$where = "coop_loan.loan_status = '1' AND coop_loan_installment.transfer_status = '0'";
		if(@$_GET['loan_type']!=''){
			$where .= " AND coop_loan_type.id = '".$_GET['loan_type']."' ";
		}
		if(@$_GET['loan_name']!=''){
			$where .= " AND coop_loan_name.loan_name_id = '".$_GET['loan_name']."' ";
		}

		$order_by = 'coop_loan.createdatetime DESC,coop_loan.id ASC';
		if($_GET['approve_date']!=''){
			$approve_date_arr = explode('/',$_GET['approve_date']);
			$approve_date_arr[2] = $approve_date_arr[2]-543;
			$approve_date = join('-', array_reverse($approve_date_arr));
			$where .= " AND coop_loan.approve_date >= '".$approve_date." 00:00:00.000'";
			$order_by = 'coop_loan.approve_date ASC,coop_loan.id ASC';
		}
		if($_GET['thru_date']!=''){
			$thru_date_arr = explode('/',$_GET['thru_date']);
			$thru_date_arr[2] = $thru_date_arr[2]-543;
			$thru_date = join("-", array_reverse($thru_date_arr));
			$where .= " AND coop_loan.approve_date <= '".$thru_date." 23:59:59.000'";
			$order_by = 'coop_loan.approve_date ASC,coop_loan.id ASC';
		}

		$where .= " OR (coop_loan_transfer.balance_transfer > 0 )";

		$x=0;
		$join_arr = array();
		$join_arr[$x]['table'] = "coop_loan_name";
		$join_arr[$x]['condition'] = "coop_loan_name.loan_name_id=coop_loan.loan_type";
		$join_arr[$x]['type'] = "inner";

		$x++;
		$join_arr[$x]['table'] = "coop_loan_type";
		$join_arr[$x]['condition'] = "coop_loan_type.id=coop_loan_name.loan_type_id";
		$join_arr[$x]['type'] = "inner";

		$x++;
		$join_arr[$x]['table'] = "coop_loan_installment";
		$join_arr[$x]['condition'] = "coop_loan_installment.loan_id=coop_loan.id";
		$join_arr[$x]['type'] = "inner";

		$x++;
		$join_arr[$x]['table'] = 'coop_user as loan_user';
		$join_arr[$x]['condition'] = 'loan_user.user_id = coop_loan.admin_id';
		$join_arr[$x]['type'] = 'left';

		$x++;
		$join_arr[$x]['table'] = "coop_loan_transfer";
		$join_arr[$x]['condition'] = "coop_loan.id=coop_loan_transfer.loan_id";
		$join_arr[$x]['type'] = "left";

		$this->paginater_all->type(DB_TYPE);
		$this->paginater_all->select(array(
									'coop_loan.member_id',
									'coop_loan_installment.transaction_datetime as createdatetime',
									'coop_loan.id as loan_id',
									'coop_loan_installment.amount as loan_amount',
									'coop_loan_installment.seq as seq',
									'coop_loan_type.loan_type',
									'IF(coop_loan.loan_type_input = "1","ระบบกู้เงินออนไลน์",loan_user.user_name) as user_name'
									));
		$this->paginater_all->main_table('coop_loan');
		$this->paginater_all->where($where);
		$this->paginater_all->page_now(@$_GET["page"]);
		$this->paginater_all->per_page(20);
		$this->paginater_all->page_link_limit(20);
		$this->paginater_all->order_by($order_by);
		$this->paginater_all->join_arr($join_arr);
		$row = $this->paginater_all->paginater_process();
		//echo"<pre>";print_r($row);exit;
		$paging = $this->pagination_center->paginating($row['page'], $row['num_rows'], $row['per_page'], $row['page_link_limit']);//$page_now = 1, $row_total = 1, $per_page = 20, $page_limit = 20
		$i = $row['page_start'];
		if($_GET['dev'] == "dev") {
			echo $this->db->last_query(); exit;
		}
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

		//เช็ค
		$arr_data['cheque_bank_title'] = $this->db->select('*')
			->from('coop_cheque_title')->get()->result_array();

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

		$this->libraries->template('loan/loan_transfer',$arr_data);
	}

	function loan_transfer_save(){
		$amount_transfer = @str_replace(',', '', @$_POST['amount_transfer']);
		$loan_amount_transfer = @str_replace(',', '', @$_POST['loan_amount']);
		$transfer_real_amount = str_replace(",", "", $_POST["transfer_real_amount"]);
		$transfer_balance = str_replace(",", "", $_POST["transfer_balance"]);

		$this->db->select(array(
			'coop_loan.loan_amount',
			'coop_loan.loan_type',
			'coop_loan.member_id',
			'coop_loan_name.loan_type_id',
			'coop_loan.loan_application_id'
		));
		$this->db->from('coop_loan');
		$this->db->join('coop_loan_name', 'coop_loan.loan_type = coop_loan_name.loan_name_id', 'inner');
		$this->db->where("coop_loan.id = '" . @$_POST['loan_id'] . "'");
		$row_loan = $this->db->get()->result_array();
		$row_loan = $row_loan[0];
		$loan_amount = @$row_loan['loan_amount'];
		$member_id = @$row_loan['member_id'];

		$date_arr = explode('/', @$_POST['date_transfer']);
		$date_transfer = ($date_arr[2] - 543) . "-" . $date_arr[1] . "-" . $date_arr[0] . " " . @$_POST['time_transfer'];

		$this->db->where(array("loan_id" => $_POST['loan_id']));
		if ($this->db->count_all_results("coop_loan_transfer")) {

			$data_insert = array();
			$data_insert['loan_id'] = $_POST['loan_id'];
			$data_insert['account_id'] = $_POST['account_id'];
			$data_insert['date_transfer'] = $date_transfer;
			$data_insert['createdatetime'] = date('Y-m-d H:i:s');
			$data_insert['admin_id'] = $_SESSION['USER_ID'];
			$data_insert['transfer_status'] = '0';
			$data_insert['amount_transfer'] = @$amount_transfer;
			$data_insert['pay_type'] = @$_POST['pay_type'];
			$data_insert['local_account_id'] = @$_POST['local_account_id'];
			$data_insert['cheque_no'] = @$_POST['cheque_no'];
			$data_insert['bank_id'] = @$_POST['bank_id'];
			$data_insert['branch_code'] = @$_POST['branch_code'];
			$data_insert['other'] = @$_POST['other'];
			$data_insert['transfer_other'] = @$_POST['transfer_other'];
			$data_insert['transfer_real_amount'] = $transfer_real_amount;
			$data_insert['balance_transfer'] = $transfer_balance - $transfer_real_amount;
			$data_insert['period'] = $_POST['transfer_period'];
			$this->db->update("coop_loan_transfer", $data_insert, array("loan_id" => $_POST['loan_id']));
			if($this->db->affected_rows()){
				$this->transfer->addHistory($data_insert);
			}

			if(isset($_POST['cheque']) && sizeof($_POST['cheque'])) {

				$transfer = $this->db->get_where('coop_loan_transaction', array('loan_id' =>  $_POST['loan_id']), 1)->row_array();

				$data_cheque = array();
				$number = 0;
				foreach ($_POST['cheque'] as $key => $cheque) {
					$data_cheque[$number]['id'] = $cheque['id'];
					$data_cheque[$number]['cheque_number'] = $cheque['cheque_number'];
					$data_cheque[$number]['amount'] = (double)join("", explode(",", $cheque['amount']));
					$data_cheque[$number]['cheque_status_transfer'] = 1;
					$data_cheque[$number]['finance_transfer'] = $_SESSION['USER_ID'];
					$data_cheque[$number]['cheque_transfer_date'] = $date_transfer;
					$data_cheque[$number]['ref_transfer_id'] = $transfer['id'];
					$number++;
				}
				$this->db->update_batch('coop_loan_cheque', $data_cheque, 'id');
			}

			if(isset($_POST['seq'])){
				$data_inst = array();
				$data_inst['transfer_status'] = 1;
				$this->db->update("coop_loan_installment", $data_inst,
					['loan_id' =>$_POST['loan_id'], 'seq' => $_POST['seq']]);
			}

		} else {

			$data_insert = array();
			$data_insert['loan_id'] = $_POST['loan_id'];
			$data_insert['account_id'] = $_POST['account_id'];
			$data_insert['date_transfer'] = $date_transfer;
			$data_insert['createdatetime'] = date('Y-m-d H:i:s');
			$data_insert['admin_id'] = $_SESSION['USER_ID'];
			$data_insert['transfer_status'] = '0';
			$data_insert['amount_transfer'] = @$amount_transfer;
			$data_insert['pay_type'] = @$_POST['pay_type'];
			$data_insert['local_account_id'] = @$_POST['local_account_id'];
			$data_insert['cheque_no'] = @$_POST['cheque_no'];
			$data_insert['bank_id'] = @$_POST['bank_id'];
			$data_insert['branch_code'] = @$_POST['branch_code'];
			$data_insert['other'] = @$_POST['other'];
			$data_insert['transfer_other'] = @$_POST['transfer_other'];
			$data_insert['transfer_real_amount'] = $transfer_real_amount;
			$data_insert['balance_transfer'] = $transfer_balance - $transfer_real_amount;
			$data_insert['period'] = $_POST['transfer_period'];
			$this->db->insert('coop_loan_transfer', $data_insert);
			if($this->db->affected_rows()){
				$this->transfer->addHistory($data_insert);
			}

			//$loan_transaction = array();
			//$loan_transaction['loan_id'] = $_POST['loan_id'];
			//$loan_transaction['loan_amount_balance'] = $loan_amount;
			//$loan_transaction['transaction_datetime'] = date('Y-m-d', strtotime($date_transfer));
			//$this->loan_libraries->loan_transaction($loan_transaction);

			$last_id = $this->db->insert_id();
			if (@$_FILES) {
				$output_dir = $_SERVER["DOCUMENT_ROOT"] . PROJECTPATH . "/assets/uploads/loan_transfer_attach/";

				if ($_FILES['file_attach']['name'] != '') {
					$new_file_name = $this->center_function->create_file_name($output_dir, $_FILES['file_attach']['name']);
					@copy($_FILES["file_attach"]["tmp_name"], $output_dir . $new_file_name);

					$data_insert = array();
					$data_insert['file_name'] = $new_file_name;
					$this->db->where('id', $last_id);
					$this->db->update('coop_loan_transfer', $data_insert);
				}
			}

			if(isset($_POST['cheque']) && sizeof($_POST['cheque'])) {
				$data_cheque = array();
				$number = 0;
				foreach ($_POST['cheque'] as $key => $cheque) {
					$data_cheque[$number]['id'] = $cheque['id'];
					$data_cheque[$number]['cheque_number'] = $cheque['cheque_number'];
					$data_cheque[$number]['amount'] = (double)join("", explode(",", $cheque['amount']));
					$data_cheque[$number]['cheque_status_transfer'] = 1;
					$data_cheque[$number]['finance_transfer'] = $_SESSION['USER_ID'];
					$data_cheque[$number]['cheque_transfer_date'] = $date_transfer;
					//$data_cheque[$number]['ref_transfer_id'] = $last_id;
					$number++;
				}
				$this->db->update_batch('coop_loan_cheque', $data_cheque, 'id');
			}
		}



		$this->db->select(array(
			't1.account_chart_id',
			't2.account_chart'
		));
		$this->db->from('coop_account_match as t1');
		$this->db->join('coop_account_chart as t2', 't1.account_chart_id = t2.account_chart_id', 'left');
		$this->db->where("
	   t1.match_type = 'loan'
	   AND t1.match_id = '" . $row_loan['loan_type'] . "'
	  ");
		$row_account_match = $this->db->get()->result_array();
		$row_account_match = @$row_account_match[0];

		$account_id_transfer = '';
		if (!empty($_POST['account_id'])) {
			$account_id_transfer = $_POST['account_id'];
		} else if (!empty($_POST['dividend_acc_num'])) {
			$account_id_transfer = $_POST['dividend_acc_num'];
		} else {
			$account_id_transfer = '';
		}


		$data = array();
		$data['coop_account']['account_description'] = "โอนเงินให้" . $row_account_match['account_chart'];
		$data['coop_account']['account_datetime'] = $date_transfer;
		$data['coop_account']['account_number'] = $account_id_transfer;
		$data['coop_account']['ref'] = $_POST['loan_id'];
		$data['coop_account']['ref_type'] = 'loan';
		$data['coop_account']['process'] = 'loan_transfer';

		$i = 0;
		if (@$_POST['pay_type'] == '0') {
			$data['coop_account_detail'][$i]['account_type'] = 'credit';
			$data['coop_account_detail'][$i]['account_amount'] = @$loan_amount_transfer;
			$data['coop_account_detail'][$i]['account_chart_id'] = '10101001';
		} else {
			$data['coop_account_detail'][$i]['account_type'] = 'debit';
			$data['coop_account_detail'][$i]['account_amount'] = @$loan_amount_transfer;
			$data['coop_account_detail'][$i]['account_chart_id'] = '20105014';
		}


		if (@$_POST['pay_type'] == '0') {
			$i++;
			$data['coop_account_detail'][$i]['account_type'] = 'debit';
			$data['coop_account_detail'][$i]['account_amount'] = @$loan_amount_transfer;
			$data['coop_account_detail'][$i]['account_chart_id'] = $row_account_match['account_chart_id'];
		} else {
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
		//อัพเดตสถานะ การโอนเงิน ระบบกู้เงินออนไลน์
		$loan_application_id = @$row_loan['loan_application_id'];
		if ($loan_application_id != '') {
			$this->load->Model('Loan_online_transfer');
			$result_online_transfer = $this->Loan_online_transfer->update_confirm_transfer($loan_application_id);
		}

		$this->center_function->toast("บันทึกข้อมูลเรียบร้อยแล้ว");
		echo "<script> document.location.href='" . base_url(PROJECTPATH . '/loan/loan_transfer?loan_id=' . $_POST['loan_id']) . "' </script>";
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

	    if(isset($_GET['createdatetime']) && !empty($_GET['createdatetime'])){
            $arr_createdatetime = explode('/',@$_POST['createdatetime']);
            $data_createdatetime = ($arr_createdatetime[2]-543)."-".$arr_createdatetime[1]."-".$arr_createdatetime[0];
            $createdatetime = (@$_POST['createdatetime'] != '')?@$data_createdatetime." ".date('H:i:s'):date('Y-m-d H:i:s');
        }else{
            $createdatetime = date('Y-m-d');
        }

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
				$apply_date = date('Y-m-d', strtotime($createdatetime));
			}
			$today =date('Y-m-d', strtotime($createdatetime));   //จุดต้องเปลี่ยน
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
		$this->db->where("type_id = '".$loan_type."' AND start_date <= '".date('Y-m-d', strtotime($createdatetime))."'");
		$this->db->order_by('start_date DESC');
		$this->db->limit(1);
		$row_term_of_loan = $this->db->get()->result_array();
		$row_term_of_loan = @$row_term_of_loan[0];
		//print_r($this->db->last_query());
		//echo"<pre>";print_r($row_term_of_loan);exit;

		$condition_of_loan = $this->db->get_where("coop_condition_of_loan", array(
			"term_of_loan_id" => $row_term_of_loan['id'],
			"result_type" => "guarantor"
		))->result_array();
		foreach ($condition_of_loan as $key => $row) {
			$this->db->join("coop_meta_condition", "coop_meta_condition.id = coop_condition_list.meta_condition_id");
			$condition = $this->db->get_where("coop_condition_list", array(
				"col_id" => $row['col_id']
			))->result_array();
			$this->db->join("coop_meta_condition", "coop_meta_condition.id = coop_condition_of_loan_sub_guarantor.meta_condition_id");
			$garantor_condition = $this->db->get_where("coop_condition_of_loan_sub_guarantor", array(
				"col_id" => $row['col_id']
			))->result_array();
			$condition_of_loan[$key]['condition'] = $condition;
			$condition_of_loan[$key]['garantor_condition'] = $garantor_condition;
		}
		$arr_return['condition_garantor'] = $condition_of_loan;

		$arr_return['share_guarantee'] = $row_term_of_loan['share_guarantee'];
		$arr_return['person_guarantee'] = $row_term_of_loan['person_guarantee'];
		$arr_return['real_estate_guarantee'] = $row_term_of_loan['real_estate_guarantee'];
		$arr_return['deposit_guarantee'] = $row_term_of_loan['deposit_guarantee'];

		$text_return = '';
		$icon_yes = "<span class='icon icon-check green'></span>";
		$icon_no = "<span class='icon icon-remove red'></span>";
		$count_error = 0;
		/*-----------------------
		- เช็ค min_refinance_every กู้วนซ้ำได้ทุกๆ n เดือน
		*/////////////////////////
		$last_loan = $this->db->get_where("coop_loan", array(
			"member_id" => $member_id,
			"loan_type" => $loan_type,
			"loan_status" => "1"
		))->result_array()[0];
		if($last_loan){
			$refinance_min_date = date("Y-m-d", strtotime("+".$row_term_of_loan['min_refinance_every']." months", strtotime($last_loan['approve_date'])) );
			if( strtotime(date("Y-m-d")) >= strtotime($refinance_min_date) ){
				$icon = $icon_yes;
			}else{
				$icon = $icon_no;
				$count_error++;
			}
			$text_return .= $icon." กู้วนซ้ำได้ทุกๆ " .@$row_term_of_loan['min_month_member']." เดือน\n<br>";
		}
		/**************************/
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
		if(@$row_term_of_loan['min_installment_percent'] != 0){
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
				$cal_loan_interest['date_interesting'] = date('Y-m-d', strtotime($createdatetime));
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
			$cal_loan_interest['date_interesting'] = date('Y-m-d', strtotime($createdatetime));
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
				$cal_loan_interest['date_interesting'] = date('Y-m-d', strtotime($createdatetime));
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
		$this->load->model("Condition_loan_model", "condition_model");
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
		if(empty($first_pay)){
            $first_pay = 0;
        }
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

		if(@$row_term_of_loan['money_use_balance_baht'] != '' && @$row_term_of_loan['money_use_balance_baht'] > 0){
			$min_money_use_balance_baht = $row_term_of_loan['money_use_balance_baht'];
		}else{
			$min_money_use_balance_baht = '';
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
		 if($min_money_use_balance_baht != ''){
			if($all_income_balance < $min_money_use_balance_baht){
				 $icon = $icon_no;
				 $count_error++;
			}else{
				$icon = $icon_yes;
			}
			$text_return .= $icon." รายได้คงเหลือมากกว่า ".number_format($row_term_of_loan['money_use_balance_baht'],0)." บาท\n<br>";
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
//		echo '<pre>'; print_r($_POST); echo '</pre>';exit;
		$arr_createdatetime = explode('/',@$_POST['createdatetime']);
		$data_createdatetime = ($arr_createdatetime[2]-543)."-".$arr_createdatetime[1]."-".$arr_createdatetime[0];
		$createdatetime = (@$_POST['createdatetime'] != '')?@$data_createdatetime." ".date('H:i:s'):date('Y-m-d H:i:s');
		if(@$_POST['loan_id']==''){
			$data_insert = array();
			$data_insert['admin_id'] = @$_SESSION['USER_ID'];
			$data_insert['createdatetime'] = @$createdatetime;
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
				//ปิดไว้รถไฟจะคีย์เอง
				/*if($key == 'petition_number'){
					$this->db->select('accm_month_ini');
					$this->db->from('coop_account_period_setting');
					$this->db->limit(1);
					$row_period_setting = $this->db->get()->row_array();
					$accm_month_ini = @$row_period_setting['accm_month_ini'];
					if((int)date('m') < (int)$accm_month_ini){
						$year_now = date('Y');
					}else{
						$year_now = date('Y')+1;
					}
					$this->db->select('MAX(petition_number) AS petition_number');
					$this->db->from("coop_loan");
					$this->db->where("YEAR(createdatetime) = '".$year_now."'");
					$rs_petition_number = $this->db->get()->result_array();
					$row_petition_number = @$rs_petition_number[0];
					$petition_number = (int)@$row_petition_number['petition_number']+1;
					$value = sprintf('% 06d',@$petition_number);
				}
				*/
				
				if($key != 'summonth_period_1' && $key != 'summonth_period_2'){
					$data_insert[$key] = @$value;
				}
				
				if($key == 'loan_amount'){
					$data_insert['loan_amount_balance'] = @$value;
					$loan_amount_balance = @$value;
				}
				$data_insert['loan_status'] = '0';
				
				//echo '<pre>'; print_r($value); echo '<pre>';
			}
			$data_insert['installment_amount'] = 1; //default
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
						if(!empty($value['coop_loan_guarantee_person']['id'])){
							foreach($value['coop_loan_guarantee_person']['id'] as $key2 => $value2){
							    if(!empty($value2)){
							        // ถ้าไม่มี member_id คนค้ำไม่ต้องบันทึก
                                    $data_insert_person = array();
                                    $data_insert_person['loan_id'] = @$loan_id;
                                    $data_insert_person['guarantee_person_id'] = @$value2;
                                    $data_insert_person['guarantee_person_contract_number'] = @$value['coop_loan_guarantee_person']['guarantee_person_contract_number'][@$key2];
                                    $data_insert_person['guarantee_person_amount'] = str_replace(',','',@$value['coop_loan_guarantee_person']['guarantee_person_amount'][@$key2]);
                                    $data_insert_person['guarantee_person_amount_balance'] = str_replace(',','',@$value['coop_loan_guarantee_person']['guarantee_person_amount'][@$key2]);
                                    $this->db->insert('coop_loan_guarantee_person', $data_insert_person);
                                }
							//echo $sql_person."<br>";
							}
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
			$data_insert['createdatetime'] = @$createdatetime;
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
				
				if($key != 'summonth_period_1' && $key != 'summonth_period_2'){
					$data_insert[$key] = @$value;
				}
				
				if($key == 'loan_amount'){
					$data_insert['loan_amount_balance'] = @$value;
					$loan_amount_balance = @$value;
				}
			}
			$data_insert['installment_amount'] = 1; //default

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
						if(!empty($value['coop_loan_guarantee_person']['id'])){
							foreach(@$value['coop_loan_guarantee_person']['id'] as $key2 => $value2){
                                if(!empty($value2)) {
                                    // ถ้าไม่มี member_id คนค้ำไม่ต้องบันทึก
                                    $data_insert_person = array();
                                    $data_insert_person['loan_id'] = @$loan_id;
                                    $data_insert_person['guarantee_person_id'] = @$value2;
                                    $data_insert_person['guarantee_person_contract_number'] = @$value['coop_loan_guarantee_person']['guarantee_person_contract_number'][$key2];
                                    $data_insert_person['guarantee_person_amount'] = str_replace(',', '', @$value['coop_loan_guarantee_person']['guarantee_person_amount'][$key2]);
                                    $data_insert_person['guarantee_person_amount_balance'] = str_replace(',', '', @$value['coop_loan_guarantee_person']['guarantee_person_amount'][@$key2]);
                                    //add coop_loan_guarantee_person
                                    $this->db->insert('coop_loan_guarantee_person', $data_insert_person);
                                }

							}
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

        $this->db->where("loan_id", $loan_id );
		$this->db->delete("coop_loan_cost_mod");


        $data_insert = array();
		if(@$_POST['data']['coop_loan_cost']) {
            $index = 0;
            foreach (@$_POST['data']['coop_loan_cost'] as $key => $val){
                $data_insert[$index]['loan_id'] = @$loan_id;
                $data_insert[$index]['member_id'] = @$_POST['data']['coop_loan']['member_id'];
                $data_insert[$index]['loan_cost_code'] = $key;
                $data_insert[$index]['loan_cost_amount'] = str_replace(',','',$val);
                $index++;
            }
            unset($index);
            $this->db->insert_batch('coop_loan_cost_mod', $data_insert);
        }

		$this->db->where("loan_id", $loan_id );
		$this->db->delete("coop_loan_deduct");

		$this->db->where("loan_id", $loan_id );
		$this->db->delete("coop_loan_deduct_profile");

		$this->db->where("id", $loan_id );
		$this->db->update("coop_loan", array('deduct_status'=>'0'));

		$data_insert = array();
		$data_insert['loan_id'] = @$loan_id;
		$data_insert['estimate_receive_money'] = str_replace(',','',$_POST['data']['loan_deduct_profile']['estimate_receive_money']);
		$data_insert['pay_per_month'] = (@$_POST['data']['coop_loan']['summonth_period_2']== '31')?str_replace(',','',$_POST['data']['coop_loan']['money_period_2']):str_replace(',','',$_POST['data']['coop_loan']['money_period_1']);
		$date_receive_money = explode('/',@$_POST['data']['loan_deduct_profile']['date_receive_money']);
		$date_receive_money = (@$date_receive_money[2]-543)."-".@$date_receive_money[1]."-".@$date_receive_money[0];
		$data_insert['date_receive_money'] = $date_receive_money;
		$data_insert['date_first_period'] = $_POST['data']['loan_deduct_profile']['date_first_period'];
		$data_insert['first_interest'] = str_replace(',','',$_POST['data']['loan_deduct_profile']['first_interest']);
		$data_insert['intstallment_seq'] = 1; //add first installment
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
			$data_insert['intstallment_seq'] = 1; //add first installment
			$this->db->insert('coop_loan_deduct', $data_insert);

			$deduct_amount += (double)str_replace(',','',$value);
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

		//Multi Cheque
		$this->db->where('loan_id', $loan_id);
		$this->db->delete('coop_loan_cheque');
		$num = 1;
		$data_cheque = array();
		foreach ($_POST['cheque'] as $key => $item){
			$data_cheque[$key]['receiver'] =  $item['receiver'];
			$data_cheque[$key]['installment_seq'] = '1';
			$data_cheque[$key]['seq'] =  $num;
			$data_cheque[$key]['amount'] = join("", explode(",", $item['amount']));
			$data_cheque[$key]['createdatetime'] = $createdatetime;
			$data_cheque[$key]['loan_id'] = @$loan_id;
			$data_cheque[$key]['user_id'] = @$_SESSION['USER_ID'];
			$num++;
		}
		$this->db->insert_batch('coop_loan_cheque', $data_cheque);

		$this->db->where("loan_id", $loan_id);
		$this->db->delete('coop_loan_installment');
		$data_insert = array();
		$data_insert['seq'] = 1;
		$data_insert['transaction_datetime'] = $createdatetime;
		$data_insert['loan_id'] = $loan_id;
		$data_insert['balance'] = 0;
		$data_insert['amount'] = $loan_amount_balance;
		$data_insert['creator'] = $_SESSION['USER_ID'];
		$data_insert['createdatetime'] = $createdatetime;
		$data_insert['last_editor'] = $_SESSION['USER_ID'];
		$this->db->insert("coop_loan_installment", $data_insert);


		$this->db->where("loan_id", $loan_id );
		$this->db->delete("coop_loan_financial_institutions");
		if(isset($_POST['data']['coop_loan_financial_institutions']) && sizeof($_POST['data']['coop_loan_financial_institutions'])){
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
		}
		
		//@start ระบบประกันชีวิต
		$this->db->where("loan_id = '".$loan_id."' AND member_id='".$member_id."'");
		$this->db->delete("coop_life_insurance_cremation");
		if(!empty($_POST['cremation_type'])){
			foreach($_POST['cremation_type'] as $key => $value){
				if($value['id'] != '' && $value['amount_balance'] != ''){
					$data_insert = array();
					$data_insert['loan_id'] = @$loan_id;
					$data_insert['member_id'] = @$member_id;
					$data_insert['import_cremation_type'] = $value['id'];
					$data_insert['import_amount_balance'] = str_replace(',','',$value['amount_balance']);
					$data_insert['admin_id'] = @$_SESSION['USER_ID'];
					$data_insert['createdatetime'] = @$createdatetime;
					$data_insert['updatetime'] = date('Y-m-d H:i:s');
					$this->db->insert('coop_life_insurance_cremation', $data_insert);
				}
			}
		}
		
		$this->db->where("loan_id = '".$loan_id."' AND member_id='".$member_id."'");
		$this->db->delete("coop_life_insurance");
		
		$insurance_amount = str_replace(',','',@$_POST['insurance_amount']);
		if($insurance_amount > 0){
			$data_insert = array();
			$data_insert['loan_id'] = @$loan_id;
			$data_insert['member_id'] = @$member_id;
			$data_insert['insurance_year'] = @$_POST['insurance_year'];
			//$data_insert['insurance_date'] = @$_POST['insurance_date'];
			$data_insert['contract_number'] = '';
			$data_insert['insurance_amount'] = str_replace(',','',@$_POST['insurance_amount']);
			$data_insert['insurance_premium'] = str_replace(',','',@$_POST['insurance_premium']);
			$data_insert['insurance_type'] = '2';//การกู้
			$data_insert['admin_id'] = @$_SESSION['USER_ID'];
			$data_insert['createdatetime'] = @$createdatetime;
			$data_insert['insurance_new'] = str_replace(',','',@$_POST['insurance_new_input']);
			$data_insert['insurance_old'] = str_replace(',','',@$_POST['insurance_old_input']);
			$data_insert['insurance_status'] = 0;
			$this->db->insert('coop_life_insurance', $data_insert);		
		}			
		//@end	ระบบประกันชีวิต

		//หลักประกันเงินฝาก
		if(!empty($_POST['guarantee_saving'])){
			foreach($_POST['guarantee_saving'] as $key => $value){
				$account_id = $value;
				$this->db->set("account_id", $account_id);
				$this->db->set("loan_id", $loan_id);
				$this->db->replace("coop_loan_guarantee_saving");
			}
		}

		$this->center_function->toast("บันทึกข้อมูลเรียบร้อยแล้ว");
		echo "<script> document.location.href='".base_url(PROJECTPATH.'/loan?member_id='.@$_GET['member'])."' </script>";
		exit;
	}

	function coop_loan_delete(){
		$loan_id = @$_GET['loan_id'];
		$member_id = @$_GET['member_id'];

		$data_insert = array();
		$data_insert['loan_status'] = @$_GET['status_to'];
		$data_insert['cancel_date'] =date('Y-m-d');

		$this->db->where('id', $loan_id);
		$this->db->update('coop_loan', $data_insert);
		
		//ลบข้อมูลระบบประกันชีวิต เมื่อยกเลิกรายการ
		$this->db->where("member_id = '".@$member_id."' AND loan_id = '".$loan_id ."'");
		$this->db->delete("coop_life_insurance");

		$this->center_function->toast("ลบข้อมูลเรียบร้อยแล้ว");
		echo true;

	}

	function ajax_coop_loan_period_table(){
		$arr_data = array();

        $x=0;
        $join_arr = array();
        $this->paginater_all_preview->type(DB_TYPE);
        $this->paginater_all_preview->select('period_count,date_period,principal_payment,interest,outstanding_balance, total_paid_per_month');
        $this->paginater_all_preview->main_table('coop_loan_period');
        $this->paginater_all_preview->where("loan_id = '".@$_POST['loan_id']."'");
        $this->paginater_all_preview->per_page(57);
		$this->paginater_all_preview->page_link_limit(57);
		$this->paginater_all_preview->page_limit_first(53);
        $this->paginater_all_preview->order_by('period_count ASC');
		$arr_data['row'] = $this->paginater_all_preview->paginater_process();



       //echo "<pre>"; print_r($arr_data['row']); exit;
		$this->db->select('*');
		$this->db->from("coop_loan");
		$this->db->where("id = '".@$_POST['loan_id']."'");
		$arr_data['loan'] = $this->db->get()->row_array();

		$this->db->select("a3.prename_full,a2.firstname_th,a2.lastname_th ");
		$this->db->from('coop_loan as a1 ');
		$this->db->join('coop_mem_apply as a2','a1.member_id = a2.member_id', 'inner');
		$this->db->join('coop_prename as a3','a2.prename_id = a3.prename_id', 'inner');
		$this->db->where("a1.id = '".@$_POST['loan_id']."'");
		$arr_data['mem'] = $this->db->get()->row_array();
		//echo $this->db->get_compiled_select(); exit;

		$this->db->select("a1.loan_id,CONCAT(a3.prename_full,a2.firstname_th,' ',a2.lastname_th) as fullname",FALSE);
	 	$this->db->from('coop_loan_guarantee_person as a1 ');
		$this->db->join('coop_mem_apply as a2','a1.guarantee_person_id = a2.member_id', 'inner');
		$this->db->join('coop_prename as a3','a3.prename_id = a2.prename_id', 'inner');
		$this->db->where("a1.loan_id = '".@$_POST['loan_id']."'");
		$arr_data['guarantee'] = $this->db->get()->result_array();
		//echo $this->db->get_compiled_select(); exit;

		$this->db->select('*');
		$this->db->from("coop_loan_period");
		$this->db->where("loan_id = '".@$_POST['loan_id']."'");
		$this->db->order_by("period_count ASC");
		$arr_data['rs'] = $this->db->get()->result_array();
		
		$this->load->view("loan/ajax_coop_loan_period_table",$arr_data);
	}

	function ajax_get_loan_data(){
		$member_id = isset($_POST['member_id']) ? trim(@$_POST['member_id']) : "";
		$member_id = sprintf("%06d",$member_id);
		$where = ' 1=1 ';
		$is_compromise = 0;
		if(@$_POST['loan_id']!=''){
			$where .= " AND coop_loan.id = '".@$_POST['loan_id']."' ";
			$compromise = $this->db->select("id")->from("coop_loan_guarantee_compromise")->where("loan_id = '".$_POST['loan_id']."'")->get()->row();
			if(!empty($compromise)) $is_compromise = 1;
		}
		if(@$_POST['contract_number']!=''){
			$where .= " AND contract_number = '".@$_POST['contract_number']."' ";
		}
		//echo '<pre>'; print_r($_POST); echo '/<pre>'; exit;
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
				coop_loan.period_amount AS period_amount,
				IF (
					coop_loan.pay_type = 2,
					(
						SELECT
							total_paid_per_month
						FROM
							coop_loan_period AS coop_loan_period
						WHERE
							coop_loan_period.loan_id = coop_loan.id
						LIMIT 1
					),
					(
						SELECT
							principal_payment
						FROM
							coop_loan_period AS coop_loan_period
						WHERE
							coop_loan_period.loan_id = coop_loan.id
						LIMIT 1
					)
				) AS period_amount_bath,
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
				coop_loan.transfer_prompt_pay,
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
				//@$value = date('d/m/Y H:i น.',strtotime('+543 year',strtotime(@$value)));
				@$value = $this->center_function->mydate2date(@$value);
			}
			if($key == 'loan_amount' || $key == 'salary'){
				@$value = empty($is_compromise) || $key == 'salary' ? number_format(@$value) :$value ;
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
		$this->db->where("loan_id = '".$loan_id."' AND coop_loan_guarantee_person.guarantee_person_id <> '' ");
		$rs3 = $this->db->get()->result_array();
		$a = 0;
		if(!empty($rs3)){
			foreach(@$rs3 as $key => $row3){
				@$data['coop_loan_guarantee_person'][$a] = @$row3;
				$this->db->select(array('*'));
				$this->db->from("coop_loan_guarantee_person as t1");
				$this->db->join("coop_loan as t2", "t1.loan_id = t2.id", "inner");
				$this->db->where("t1.guarantee_person_id = '".$row3['member_id']."' AND t2.loan_status = '1' AND t1.guarantee_person_id <> '' ");
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

		$data['coop_loan_cheque'] = $this->db->get_where('coop_loan_cheque',
			array('loan_id' => $loan_id, "installment_seq" => $seq))->result_array();

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

//		$this->db->select(array('*'));
//		$this->db->from("coop_loan_cost");
//		$this->db->where("loan_id = '".$loan_id."'");
//		$rs = $this->db->get()->result_array();
//		@$data['coop_loan_cost']['school_benefits'] = @$rs[0]['school_benefits'];
//		@$data['coop_loan_cost']['saving'] = @$rs[0]['saving'];
//		@$data['coop_loan_cost']['ch_p_k'] = @$rs[0]['ch_p_k'];
//		@$data['coop_loan_cost']['pension'] = @$rs[0]['pension'];
//		@$data['coop_loan_cost']['k_b_k'] = @$rs[0]['k_b_k'];
//		@$data['coop_loan_cost']['other'] = @$rs[0]['other'];

		$this->db->select('outgoing_code, outgoing_name, loan_cost_amount');
		$this->db->from("coop_outgoing");
		$this->db->join("coop_loan_cost_mod", "coop_outgoing.outgoing_code=coop_loan_cost_mod.loan_cost_code", "inner");
        $this->db->where("loan_id = '".$loan_id."'");
        $rs = $this->db->get()->result_array();
        @$data['coop_loan_cost'] = array();
        if(sizeof($rs)) {
            foreach ($rs as $key => $val) {
                @$data['coop_loan_cost'][$val['outgoing_code']] = $val['loan_cost_amount'];
            }
        }

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

		$this->db->where("loan_id", $loan_id);
		$data['guarantee_saving'] = $this->db->get_where("coop_loan_guarantee_saving")->result_array();

		$data['is_compromise'] = $is_compromise;
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
		//$this->db->join('coop_term_of_loan','coop_loan_name.loan_name_id = coop_term_of_loan.type_id','inner');
		$this->db->where("loan_type_id = '".$_POST['type_id']."' AND loan_name_status = 1");
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

		$arr_data['month_arr'] = array('1'=>'มกราคม','2'=>'กุมภาพันธ์','3'=>'มีนาคม','4'=>'เมษายน','5'=>'พฤษภาคม','6'=>'มิถุนายน','7'=>'กรกฎาคม','8'=>'สิงหาคม','9'=>'กันยายน','10'=>'ตุลาคม','11'=>'พฤศจิกายน','12'=>'ธันวาคม');

		$this->db->select(array(
			't1.admin_id',
			't1.loan_type',
			't1.petition_number',
			't1.member_id',
			't1.loan_amount',
			't1.loan_interest_amount',
			't1.loan_amount_total',
			't1.loan_amount_balance',
			't1.loan_amount_total_balance',
			't1.loan_reason',
			't1.interest_per_year',
			't1.money_per_period',
			't1.period_amount',
			't1.date_start_period',
			't1.date_period_1',
			't1.money_period_1',
			't1.date_period_2',
			't1.money_period_2',
			't1.createdatetime',
			't1.updatetimestamp',
			't1.loan_status',
			't1.period_type',
			't1.day_start',
			't1.month_start',
			't1.year_start',
			't1.pay_type',
			't1.pay_amount',
			't1.contract_number',
			't1.cancel_date',
			't1.salary',
			't1.id',
			't1.guarantee_for_id',
			't1.period_now',
			't1.deduct_status',
			't1.type_deduct',
			't1.deduct_receipt_id',
			't1.transfer_type',
			't1.transfer_bank_account_id',
			't1.approve_date as loan_approve_date',
			't1.transfer_bank_id',
			't1.transfer_account_id',
			't1.transfer_status',
			't1.date_last_interest',
			't1.transfer_prompt_pay',
			't1.loan_type_input',
			't1.loan_application_id',
			't1.lastupdate_by',
			't1.lastupdate_datetime',
			't1.loan_reason as loan_reason_id',
			't2.*',
			't3.prename_short',
			't4.district_name',
			't5.amphur_name',
			't6.province_name',
			't7.mem_group_name',
			't8.loan_reason',
			't9.loan_group_id',
			't9.loan_name'
		));
		$this->db->from('coop_loan as t1');
		$this->db->join("coop_mem_apply as t2","t2.member_id = t1.member_id","inner");
		$this->db->join("coop_prename as t3","t3.prename_id = t2.prename_id","left");
		$this->db->join("coop_district as t4","t2.c_district_id = t4.district_id","left");
		$this->db->join("coop_amphur as t5","t2.c_amphur_id = t5.amphur_id","left");
		$this->db->join("coop_province as t6","t2.c_province_id = t6.province_id","left");
		$this->db->join("coop_mem_group as t7","t2.level = t7.id","left");
		$this->db->join("coop_loan_reason as t8","t1.loan_reason = t8.loan_reason_id","left");
		$this->db->join("coop_loan_name as t9","t1.loan_type = t9.loan_name_id","left");
		$this->db->where("t1.id = '".$loan_id."'");
		$row = $this->db->get()->result_array();
		$arr_data['data'] = $row[0];

		if($_GET['dev'] == "dev"){
			echo "<pre>";
			echo $this->db->last_query();
			print_r($arr_data['data']);
			exit;

		}

		//ชื่อสัญญาเงินกู้ใช้กับเอกสารคำขอ
		$arr_data['loan_name'] = $this->db->get_where('coop_loan_name',
			array('loan_name_id' => $row[0]['loan_type']))->row_array();
		//echo $arr_data['loan_name']['loan_name_id']." ". $arr_data['loan_name']['loan_title']; exit;

		$this->db->select(array('principal_payment','total_paid_per_month'));
		$this->db->from('coop_loan_period');
		$this->db->where("loan_id = '".$loan_id."' AND period_count = '1'");
		$row = $this->db->get()->result_array();
		$arr_data['data_period_1'] = $row[0];

		 $arr_data['data']['share_collect_value'] = $this->db->select("share_collect_value")->from("coop_mem_share")->where(array("share_bill_date <=" => $arr_data['data']['createdatetime']))->order_by("share_bill_date", "desc")->limit(1)->get()->row_array()['share_collect_value'];

		 $guarantee = $this->db->select(array('t2.*','t4.district_name','t5.amphur_name','t6.province_name','t7.mem_group_name', 'prename_full'))->from("coop_loan_guarantee_person t1")
			 ->join('coop_mem_apply t2', 't1.guarantee_person_id=t2.member_id', 'inner')
			 ->join('coop_prename t3', 't2.prename_id=t3.prename_id', 'left')
			 ->join("coop_district as t4","t2.c_district_id = t4.district_id","left")
			 ->join("coop_amphur as t5","t2.c_amphur_id = t5.amphur_id","left")
			 ->join("coop_province as t6","t2.c_province_id = t6.province_id","left")
			 ->join("coop_mem_group as t7","t2.level = t7.id","left")
			 ->where("t1.loan_id='{$loan_id}' ")->get()->result_array();

		 $guarantors = array();
		 $num = 0;
		 foreach ($guarantee as $key => $value ){

		 	//หาสัญญาของคนค้ำ
		 	$guarantee_loan_id = $this->db->select("id")->from("coop_loan t1")->join("coop_loan_name t2", "t1.loan_type=t2.loan_name_id")->where("member_id = '{$value['member_id']}' AND t2.loan_type_id=8")->order_by("createdatetime", "desc")->limit(1)->get()->row_array()['id'];

		 	$share_collect = $this->db->order_by("share_bill_date", "desc")->limit(1)->get_where("coop_mem_share", "share_bill_date <= '{$arr_data['data']['createdatetime']}' AND member_id = {$value['member_id']}")->row_array();

		 	if(sizeof($share_collect)){
		 		$guarantors[$num] =  $value;
		 		$guarantors[$num]['share_collect_value'] = $share_collect['share_collect_value'];

			}

			 $sub_guarantee = $this->db->select("concat(prename_full, firstname_th, ' ', lastname_th) as `guarantee_name`")->from("coop_loan_guarantee_person t1")->join('coop_mem_apply t2', 't1.guarantee_person_id=t2.member_id', 'inner')->join('coop_prename t3', 't2.prename_id=t3.prename_id', 'left')->where("t1.loan_id='{$guarantee_loan_id}' ")->get()->result_array();
			 $row_index = 0;
			 if(sizeof($sub_guarantee)){
				 foreach ($sub_guarantee as $index => $item){
					 $guarantors[$num]['guarantee'][$row_index]['guarantee_name'] = $item['guarantee_name'];
					 $row_index++;
				 }
			 }
			 $num++;
		 }

		 $arr_data['guarantee_type'] = $this->db->select("person_guarantee, deposit_guarantee, share_guarantee, share_and_deposit_guarantee")->from("coop_term_of_loan")->where(array('start_date <=' => $arr_data['data']['createdatetime'] , 'type_id' => $arr_data['data']['loan_type']))->order_by('start_date', 'desc')->limit(1)->get()->row_array();

		 $arr_data['guarantee'] =  $guarantors;

		 if($arr_data['data']['loan_type'] == '17'){
		     $this->load->view('loan/petition_normal_education_pdf',$arr_data);
		 }else{
		     $this->load->view('loan/petition_normal_pdf',$arr_data);
		 }

	}

	function petition_emergent_pdf($loan_id){
		$arr_data = array();

		$arr_data['month_arr'] = array('1'=>'มกราคม','2'=>'กุมภาพันธ์','3'=>'มีนาคม','4'=>'เมษายน','5'=>'พฤษภาคม','6'=>'มิถุนายน','7'=>'กรกฎาคม','8'=>'สิงหาคม','9'=>'กันยายน','10'=>'ตุลาคม','11'=>'พฤศจิกายน','12'=>'ธันวาคม');

		$this->db->select(array(
			't1.admin_id',
			't1.loan_type',
			't1.petition_number',
			't1.member_id',
			't1.loan_amount',
			't1.loan_interest_amount',
			't1.loan_amount_total',
			't1.loan_amount_balance',
			't1.loan_amount_total_balance',
			't1.loan_reason',
			't1.interest_per_year',
			't1.money_per_period',
			't1.period_amount',
			't1.date_start_period',
			't1.date_period_1',
			't1.money_period_1',
			't1.date_period_2',
			't1.money_period_2',
			't1.createdatetime',
			't1.updatetimestamp',
			't1.loan_status',
			't1.period_type',
			't1.day_start',
			't1.month_start',
			't1.year_start',
			't1.pay_type',
			't1.pay_amount',
			't1.contract_number',
			't1.cancel_date',
			't1.salary',
			't1.id',
			't1.guarantee_for_id',
			't1.period_now',
			't1.deduct_status',
			't1.type_deduct',
			't1.deduct_receipt_id',
			't1.transfer_type',
			't1.transfer_bank_account_id',
			't1.approve_date as loan_approve_date',
			't1.transfer_bank_id',
			't1.transfer_account_id',
			't1.transfer_status',
			't1.date_last_interest',
			't1.transfer_prompt_pay',
			't1.loan_type_input',
			't1.loan_application_id',
			't1.lastupdate_by',
			't1.lastupdate_datetime',
			't1.loan_reason as loan_reason_id',
			't2.*',
			't3.prename_short',
			't4.district_name',
			't5.amphur_name',
			't6.province_name',
			't7.mem_group_name',
			't8.loan_reason',
			't9.date_transfer',
            't10.mem_group_name as faction_name',
            't11.position_name',
            't12.district_name as c_district_name',
            't13.amphur_name as c_amphur_name',
            't14.province_name as c_province_name'
		));
		$this->db->from('coop_loan as t1');
		$this->db->join("coop_mem_apply as t2","t2.member_id = t1.member_id","inner");
		$this->db->join("coop_prename as t3","t3.prename_id = t2.prename_id","left");
		$this->db->join("coop_district as t4","t2.district_id = t4.district_id","left");
		$this->db->join("coop_amphur as t5","t2.amphur_id = t5.amphur_id","left");
		$this->db->join("coop_province as t6","t2.province_id = t6.province_id","left");
		$this->db->join("coop_mem_group as t7","t2.level = t7.id","left");
		$this->db->join("coop_loan_reason as t8","t1.loan_reason = t8.loan_reason_id","left");
		$this->db->join("coop_loan_transfer as t9","t1.id = t9.loan_id","left");
        $this->db->join("coop_mem_group as t10","t2.faction = t10.id","left");
        $this->db->join("coop_mem_position as t11","t2.position_id = t11.position_id","left");
        $this->db->join("coop_district as t12","t2.c_district_id = t12.district_id","left");
        $this->db->join("coop_amphur as t13","t2.c_amphur_id = t13.amphur_id","left");
        $this->db->join("coop_province as t14","t2.c_province_id = t14.province_id","left");
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

		$arr_data['share_collect_value'] = $this->db->order_by("share_bill_date", "desc")->limit(1)->get_where("coop_mem_share", "share_bill_date <= '{$arr_data['data']['createdatetime']}' AND member_id = {$arr_data['data']['member_id']}")->row_array();


		$loan_contract = $this->db->select("loan_type_id, member_id, loan_amount, loan_amount_balance, contract_number")->from("coop_loan t1")->join("coop_loan_name t2", "t1.loan_type=t2.loan_name_id")->where("t1.member_id='{$arr_data['data']['member_id']}' AND loan_amount_balance > 0")->get()->result_array();

		$loan = array();
		foreach($loan_contract as $key => $val){
			$loan[$val['loan_type_id']]['loan_balance'] += $val['loan_amount_balance'];
		}

		$arr_data['loan'] = $loan;
		if($_GET['dev'] == 'dev'){
            echo '<pre>';print_r($arr_data);exit;
        }

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

        $loan_fee = 0;

		$this->db->select(array('share_collect','share_collect_value'));
		$this->db->from('coop_mem_share');
		$this->db->where("member_id = '".$member_id."' AND share_status = '1'");
		$this->db->order_by('share_date DESC, share_id DESC');
		$this->db->limit(1);
		$row_share = $this->db->get()->result_array();


		//$data['percent_guarantee'] = number_format($percent_guarantee,2);
		$data['percent_guarantee'] = (@$percent_guarantee > 0)?number_format($percent_guarantee):0; //ปัดเศษ
		$data['loan_fee'] = ($loan_fee>0)?number_format($loan_fee,2):0;
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
	   't4.date_receive_money',
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
	   }else if($key == 'estimate_receive_money') {
		   $estimate_receive_money = @$value - @$total_institutions_amount;
		   $data[$key] = number_format($estimate_receive_money, 2);
	   }else if($key == 'date_receive_money'){
	   		$data[$key] = $this->center_function->mydate2date($value);
	   }else{
			$data[$key] = @$value;
	   }
	  }

	  $inst = $this->installment->getInstallment($_POST['loan_id'],  $_POST['seq']);
	  $data['seq'] = $inst['seq'];

	  $data['coop_loan_cheque'] = $cheque = $this->db->get_where('coop_loan_cheque', array('loan_id' => $_POST['loan_id'], 'installment_seq' => $_POST['seq']))->result_array();

	  $data['amount_transfer'] = $cheque[0]['amount'];
	  $data['estimate_receive_money'] = number_format($cheque[0]['amount'], 2);
	  $data['transfer_balance'] = $cheque[0]['amount'];
	  $data['transfer_real_amount'] = $cheque[0]['amount'];
	  $data['transfer_period'] = $inst['seq'];

	  $this->db->select(array('balance_transfer','amount_transfer', 'period'));
	  $transfer = $this->db->get_where('coop_loan_transfer',array('loan_id' => $_POST['loan_id']))->row_array();
	  if($transfer['balance_transfer'] > 0){
			$data['balance_transfer'] = $transfer['balance_transfer'];
	  }else{
		  $data['balance_transfer'] = number_format($cheque[0]['amount'], 2);
	  }

	  if((int)$inst['seq'] > 0){
	  		$data['period'] = $inst['seq'];
	  }else{
	  		$data['period'] = 1;
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
		$salary = (@$_POST['salary'] > 0)?str_replace(',','',@$_POST['salary']):0;
		$other_income = (@$_POST['other_income'] > 0)?str_replace(',','',@$_POST['other_income']):'';
		$salary_rule = @$salary + @$other_income;
		$member_id = @$_POST['member_id'];
		
		$this->db->select(array('mem_type_id','share_month','salary','other_income'));
		$this->db->from('coop_mem_apply');
		$this->db->where("member_id = '".$member_id ."'");
		$this->db->limit(1);
		$row_mem_apply = $this->db->get()->result_array();
		$mem_type_id = @$row_mem_apply[0]['mem_type_id'];
		$share_month_old = @$row_mem_apply[0]['share_month'];
		$salary_old = @$row_mem_apply[0]['salary'];
		$other_income_old = @$row_mem_apply[0]['other_income'];
		
		$this->db->select(array('share_salary'));
		$this->db->from('coop_share_rule');
		$this->db->where("mem_type_id = '".$mem_type_id ."' AND salary_rule <= '".$salary_rule."'");
		$this->db->order_by('salary_rule DESC');
		$this->db->limit(1);
		$row_share_rule = $this->db->get()->result_array();
		
		$this->db->select(array('setting_value'));
		$this->db->from('coop_share_setting');
		$this->db->limit(1);
		$row_setting_value = $this->db->get()->result_array();		
		$share_month = @$row_share_rule[0]['share_salary']*@$row_setting_value[0]['setting_value'];

		$data_insert = array();
		$data_insert['salary'] = @$salary;
		$data_insert['other_income'] = @$other_income;
		
		$data_history = array();
		$process_timestamp = date('Y-m-d H:i:s');
		/*if(@$share_month > @$share_month_old){
			$data_insert['share_month'] = @$share_month;
			
			if(@$share_month_old != @$share_month) {
				$data_history = array();
				$data_history["member_id"] = @$member_id;
				$data_history["input_name"] = "share_month";
				$data_history["old_value"] = $share_month_old;
				$data_history["new_value"] = $share_month;
				$data_history["user_id"] = $_SESSION['USER_ID'];
				$data_history["created_at"] = $process_timestamp;
				$data_historys[] = $data_history;
			}
		}
		*/
		$this->db->where('member_id',$member_id);
		$this->db->update('coop_mem_apply',$data_insert);
		
		if(@$salary_old != @$salary) {
			$data_history["member_id"] = @$member_id;
			$data_history["input_name"] = "salary";
			$data_history["old_value"] = $salary_old;
			$data_history["new_value"] = $salary;
			$data_history["user_id"] = $_SESSION['USER_ID'];
			$data_history["created_at"] = $process_timestamp;
			$data_historys[] = $data_history;
		}
		
		if(@$other_income_old != @$other_income) {
			$data_history["member_id"] = @$member_id;
			$data_history["input_name"] = "other_income";
			$data_history["old_value"] = $other_income_old;
			$data_history["new_value"] = $other_income;
			$data_history["user_id"] = $_SESSION['USER_ID'];
			$data_history["created_at"] = $process_timestamp;
			$data_historys[] = $data_history;
		}
	
		if(!empty($data_historys)) $this->db->insert_batch("coop_member_data_history", $data_historys);
		
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
		if(@$_GET['dev'] == 'dev'){
			echo '-------loan_data---------<br>';
			echo $this->db->last_query();
			echo '<hr>';
		}

		$this->db->select(array('t1.receipt_id', 't1.payment_date', 't1.principal_payment', 't1.interest', 't1.loan_amount_balance','t2.finance_month_profile_id','t2.receipt_status'));
		$this->db->from('coop_finance_transaction as t1');
		$this->db->join('coop_receipt as t2','t1.receipt_id = t2.receipt_id','inner');
		$this->db->where("t1.loan_id = '".$loan_id."'");
		$this->db->order_by('payment_date ASC');
		$row_transaction = $this->db->get()->result_array();
// echo $this->db->last_query(); echo '<br>';
		//ฟิก วันที่เป็นยอดยกมาวันที่ 31 ส.ค. 61 ถ้าหากสัญญา เกิดก่อน 31 ส.ค. 61  ให้ UNION
		$where_createdatetime = "AND createdatetime <= '2018-08-31'";
		$where_date_bring = "2018-08-31 00:00:00";
		$sql = "SELECT t1.receipt_id, t1.payment_date, t1.principal_payment,IF(t1.loan_interest_remain IS NULL,t1.interest,t1.interest+t1.loan_interest_remain) AS interest, t1.loan_amount_balance,t2.finance_month_profile_id, '' ref_id, t1.finance_transaction_id , null as receipt_status, t1.transaction_text
					FROM 
						coop_finance_transaction AS t1 
					LEFT JOIN coop_receipt as t2 ON t1.receipt_id = t2.receipt_id
					WHERE t1.loan_id = '{$loan_id}'
					UNION ALL
						SELECT 
							t1.receipt_id,
							t1.transaction_datetime,
							t1.principal_payment,
							t1.interest,
							t1.loan_amount_balance,
							t1.finance_month_profile_id,
							t1.ref_id,
							t1.finance_transaction_id,
							t1.receipt_status,
							t1.transaction_text
						FROM (
						SELECT
						 coop_loan_transaction.loan_id,
						 coop_loan_transaction.receipt_id,
						 coop_loan_transaction.transaction_datetime,
						 coop_loan_transaction.loan_amount_balance,
						 IF(t5.update_time != '', 'กู้เงินเพิ่ม',IF(coop_loan_transaction.transaction_datetime = '{$where_date_bring}','ยอดยกมาวันที่ 31 สิงหาคม 2561','')) AS transaction_text,
						 t5.loan_request AS principal_payment,
						 '' AS interest,
						 '' AS finance_month_profile_id,
						 '' AS ref_id,
						 '' AS finance_transaction_id,
						 '' AS receipt_status
						FROM
						 coop_loan_transaction
						 LEFT JOIN ( SELECT id, createdatetime FROM coop_loan ) AS coop_loan ON coop_loan_transaction.loan_id = coop_loan.id
						 LEFT JOIN ( SELECT loan_id, update_time, loan_request FROM coop_loan_repayment ) AS t5 ON coop_loan_transaction.transaction_datetime = t5.update_time AND coop_loan_transaction.loan_id = t5.loan_id 
						WHERE
							coop_loan_transaction.loan_id = '{$loan_id}' 
							AND coop_loan_transaction.receipt_id IS NULL 
							 {$where_createdatetime}
						) AS t1
					UNION ALL
					SELECT bill_id receipt_id, DATE(return_time) payment_id
					, CASE WHEN return_type = 5 THEN return_principal WHEN return_type = 3 THEN return_amount * -1 WHEN return_type = 2 THEN CASE WHEN return_principal > 0 THEN return_principal * - 1 ELSE 0 END ELSE CASE WHEN return_principal > 0 THEN return_principal * - 1 ELSE 0 END END principal_payment
					, CASE WHEN return_type = 5 THEN CASE WHEN (return_principal = 0 and return_interest = 0) THEN return_amount ELSE return_interest END WHEN return_type = 2 THEN CASE WHEN (return_principal = 0 and return_interest = 0) THEN return_amount * - 1 ELSE return_interest * -1 END  WHEN return_type <> 3 AND (return_principal = 0 and return_interest = 0)  THEN return_amount * -1 ELSE return_interest*-1 END interest
					, '0' loan_amount_balance
					, '' finance_month_profile_id
					, receipt_id ref_id
					, 99999999
					, '' receipt_status
					, CASE WHEN return_desc != '' THEN return_desc WHEN return_type = 2 AND receipt_id is not null THEN concat('เงินคืน ', receipt_id) ELSE CASE WHEN return_type = 5 THEN concat('เงินคืน ', receipt_id) WHEN return_type = 1 THEN concat('เงินคืน ', receipt_id) ELSE '' END END as transaction_text
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
						t1.loan_id = '{$loan_id}' AND t3.receipt_status = 2 GROUP BY 1 ORDER BY payment_date ASC;
		";
		//, CASE WHEN return_type = 3 THEN return_amount * -1 WHEN return_type = 2 THEN return_principal * - 1 ELSE CASE WHEN return_principal > 0 THEN return_principal * - 1 ELSE 0 END END principal_payment
		$rs = $this->db->query($sql);
		$row_transaction = $rs->result_array();
		if(@$_GET['dev'] == 'dev'){
			echo '-------row_transaction---------<br>';
			echo $this->db->last_query();
			echo '<hr>';
		}
		// echo "<pre>";var_dump($row_transaction);exit;
		//ดึงข้อมูลหนี้ ตั้งแต่ 31 ส.ค. 61 เป็นต้นไป เพราะข้อมูลยกมาเป็น  31 ส.ค. 61 จึงดึงข้อมูลมาแค่นี้
		$where_start_month = " AND CONCAT(t1.non_pay_year,RIGHT(CONCAT('00', t1.non_pay_month), 2)) >= '256108'";
		$non_pay_raws = $this->db->select('t1.non_pay_id, t1.non_pay_month as month, t1.non_pay_year as year, t2.non_pay_amount as principal_payment, t3.non_pay_amount as interest, t2.loan_id as l1, t2.loan_id as l2')
							->from("coop_non_pay as t1")
							->join("coop_non_pay_detail as t2", "t1.non_pay_id = t2.non_pay_id AND t2.pay_type = 'principal' && t2.loan_id = '{$loan_id}'", "left")
							->join("coop_non_pay_detail as t3", "t1.non_pay_id = t3.non_pay_id AND t3.pay_type = 'interest' && t3.loan_id = '{$loan_id}'", "left")
							->WHERE("(t2.loan_id = '{$loan_id}' || t3.loan_id = '{$loan_id}') && t1.non_pay_status != '0' {$where_start_month}")
							->get()->result_array();

		$non_pay_datas = array();
		if(@$_GET['dev'] == 'dev'){
			echo '-------non_pay_datas---------<br>';
			echo $this->db->last_query();
			echo '<hr>';
		}
		
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
//		echo '<pre>';print_r($merge_transactions);exit;
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
						//if(@$_GET['dev'] == 'dev'){
						if($transaction_data[array_keys($transaction_data)[sizeof($transaction_data)-2]]['receipt_id'] == '-'){
							@$transaction_data[$receipt_id]['loan_amount_balance'] = $transaction_data[array_keys($transaction_data)[sizeof($transaction_data)-3]]['loan_amount_balance']-$value['principal_payment'];	
						}else{
							@$transaction_data[$receipt_id]['loan_amount_balance'] = $transaction_data[array_keys($transaction_data)[sizeof($transaction_data)-2]]['loan_amount_balance']-$value['principal_payment'];	
						}
						//}else{
						//if($transaction_data[array_keys($transaction_data)[sizeof($transaction_data)-2]]['receipt_id'] == '-'){
						//	@$transaction_data[$receipt_id]['loan_amount_balance'] = $transaction_data[array_keys($transaction_data)[sizeof($transaction_data)-3]]['loan_amount_balance']-$value['principal_payment'];	
						//}else{
						//	@$transaction_data[$receipt_id]['loan_amount_balance'] = $loan_amount_balance - $value['principal_payment'];
						//}
						//}

							
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
			    
				if( $value['ref_id'] || (strpos($value["receipt_id"],'R') !== false)) {
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
//		echo"<pre>";print_r($transaction_data);exit;
		$this->preview_libraries->template_preview('loan/loan_payment_detail',$arr_data);
	}

    public function update_coop_loan_transaction()
    {
        $sql = "SELECT 
                t1.loan_transaction_id, 
                t1.loan_id, 
                t1.transaction_datetime, 
                t2.loan_transaction_id as t2_loan_transaction_id, 
                t1.loan_amount_balance, 
                t2.loan_amount_balance as t2_loan_amount_balance ,
                t3.loan_amount_balance as coop_loan_amount_balance
                FROM coop_loan_transaction as t1
                LEFT JOIN (
                    SELECT t1.loan_transaction_id, t1.loan_amount_balance, t1.loan_id, t1.transaction_datetime FROM coop_loan_transaction as t1
                    LEFT JOIN (SELECT * FROM coop_loan_transaction) as t2 ON t1.loan_id = t2.loan_id AND t2.transaction_datetime >  t1.transaction_datetime
                    WHERE t2.transaction_datetime is null
                ) as t2 ON t1.loan_id = t2.loan_id AND t1.loan_transaction_id > t2.loan_transaction_id AND t1.transaction_datetime < t2.transaction_datetime
                LEFT JOIN coop_loan as t3 ON t1.loan_id = t3.id
                WHERE t2.loan_transaction_id is not null 
                AND t1.loan_amount_balance = t3.loan_amount_balance
                AND t2.loan_amount_balance > 0 
                ORDER BY t1.loan_id";

        $result = $this->db->query($sql)->result_array();
        $loan_ids = array_column($result, 'loan_id');
        $loan_ids = array_unique($loan_ids);
//        echo '<pre>';print_r($loan_ids);exit;
        $i = 0;
        foreach ($loan_ids as $key => $value){
            $sql = "SELECT t1.*
                    ,t2.finance_transaction_id
                    ,t2.payment_date
                    ,t2.loan_amount_balance as finance_amount_balance
                    FROM `coop_loan_transaction` as t1 
                    LEFT JOIN (
                        SELECT finance_transaction_id,loan_id, loan_amount_balance ,payment_date,receipt_id
                        ,YEAR(payment_date) as YEAR 
                        ,MONTH(payment_date) as MONTH
                        ,DAY(payment_date) as DAY
                        FROM coop_finance_transaction WHERE principal_payment > 0
                    ) as t2 ON t1.loan_id = t2.loan_id AND t2.YEAR = YEAR(t1.transaction_datetime) AND t2.MONTH = MONTH(t1.transaction_datetime) AND t2.DAY = DAY(t1.transaction_datetime) AND t1.receipt_id = t2.receipt_id
                    WHERE t1.`loan_id` = '".$value."' ORDER BY `transaction_datetime` DESC 
                    LIMIT 3";
            $row = $this->db->query($sql)->result_array();
//            echo $this->db->last_query();exit;
            $transaction_datetime_0 = substr($row[0]['transaction_datetime'], 0,10);
            $transaction_datetime_1 = substr($row[1]['transaction_datetime'], 0,10);
//            echo $transaction_datetime_0.' '.$transaction_datetime_1;
            if($transaction_datetime_0 == $transaction_datetime_1){
//                echo '<hr>';
//                echo $value;
//                echo '<hr>';
            }else {
                if ($row[0]['loan_amount_balance'] > 0 && $row[1]['loan_amount_balance'] == 0 && $row[2]['loan_amount_balance'] > 0) {
                    $this->db->select('t1.pay_amount');
                    $this->db->from('coop_finance_month_detail as t1');
                    $this->db->where("t1.loan_id = '$value' AND t1.deduct_code = 'LOAN' AND t1.pay_type = 'principal'");
                    $this->db->order_by("t1.run_id DESC");
                    $row2 = $this->db->get()->row_array();
//                echo '<hr>';
//                echo $this->db->last_query();
//                echo '<br>';
//                print_r($value);

                    $sum = $row[2]['loan_amount_balance'] - $row[0]['loan_amount_balance'];
                    $sum2 = $sum - $row2['pay_amount'];
                    if ($sum == $row2['pay_amount'] && $sum2 == 0) {
                        if (empty($row[1]['loan_transaction_id'])) {
                            echo 'stopppppp 1';
                            exit;
                        }
                        if (empty($row[1]['finance_transaction_id'])) {
                            $transaction_datetime = $row[1]['transaction_datetime'];
                            $sql = "SELECT finance_transaction_id,loan_id, loan_amount_balance ,payment_date,receipt_id
FROM coop_finance_transaction
WHERE principal_payment > 0 AND loan_id = '" . $value . "' AND YEAR(payment_date) = YEAR('" . $transaction_datetime . "') AND MONTH(payment_date) = MONTH('" . $transaction_datetime . "') AND DAY(payment_date) = DAY('" . $transaction_datetime . "')
ORDER BY finance_transaction_id DESC limit 1";
                            $new_finance_transaction_id = $this->db->query($sql)->row_array();
//                        echo '<pre>';
//                        print_r($new_finance_transaction_id);
                            $row[1]['finance_transaction_id'] = $new_finance_transaction_id['finance_transaction_id'];

//                        echo '<br>';
//                        echo 'stopppppp 2';exit;
                        }
                        if (empty($row[0]['loan_transaction_id'])) {
                            echo 'stopppppp 3';
                            exit;
                        }
                        if (empty($row[0]['finance_transaction_id'])) {
                            echo 'stopppppp 4';
                            exit;
                        }

//                        $i++;
//                        echo '-- ' . $i . ' ' . $value . '<br>';
//                        echo '<pre>';
//                        print_r($row);
//                        echo '</pre>';
//                        echo "UPDATE `coop_loan_transaction`
//                      SET `loan_amount_balance` = '" . $sum . "'
//                      WHERE `loan_transaction_id` = '" . $row[1]['loan_transaction_id'] . "';";
//                        echo '<br>';
//                        echo "UPDATE `coop_finance_transaction`
//                      SET `loan_amount_balance` = '" . $sum . "'
//                      WHERE `finance_transaction_id` = '" . $row[1]['finance_transaction_id'] . "';";
//                        echo '<br>';
//                        echo "UPDATE `coop_loan_transaction`
//                      SET `loan_amount_balance` = '" . $sum2 . "'
//                      WHERE `loan_transaction_id` = '" . $row[0]['loan_transaction_id'] . "';";
//                        echo '<br>';
//                        echo "UPDATE `coop_finance_transaction`
//                      SET `loan_amount_balance` = '" . $sum2 . "'
//                      WHERE `finance_transaction_id` = '" . $row[0]['finance_transaction_id'] . "';";
//                        echo '<br>';
                    } else {
//                        echo 'NO DATA ' . $value . '<br>';
//                        exit;
                    }
                } else {
//                echo '<pre>';
//                print_r($row);
//                echo '</pre>';
//                echo 'ERROR';
                    print_r($value);
                    echo '<br>';
//                exit;
                }
            }
        }
//        echo '<pre>';print_r($loan_ids);exit;
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
        $arr_data['loan_type'] = $this->db->get('coop_loan_name')->result_array();
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

	public function save_manage_installment()
	{
		// var_dump($data);
		// init set
		//echo "<pre>";
		//print_r($_POST);
		//echo "</pre>";
		//exit;
		$_POST['new_period_per_month'] = implode("", explode(",", $_POST['new_period_per_month']));
		
		$this->db->select(array("coop_loan.*", "DAY(approve_date) AS d", "MONTH(approve_date) AS m", "YEAR(approve_date) AS y",
	"(SELECT id FROM coop_term_of_loan WHERE coop_loan.loan_type = coop_term_of_loan.type_id AND start_date <= CURDATE() ORDER BY start_date DESC limit 1) as term_of_loan_id"));
		$loan_row = $this->db->get_where("coop_loan", array("id" => $_POST['loan_id']) )->result()[0];
		//echo $this->db->last_query(); echo '<hr>';
		if($loan_row!=""){

			$this->db->select_max('seq_no');
			$seq_no = $this->db->get_where("coop_loan_period_history", array("loan_id" => $_POST['loan_id']))->result()[0]->seq_no;
			$data_insert_history['create_date'] = date("Y-m-d H:i:s");
			$data_insert_history['admin_id'] 	= $_SESSION['USER_ID'];
			$data_insert_history['seq_no'] = ($seq_no=="") ? '1' : ($seq_no+1);
			$data_insert_history['pay_type'] = $loan_row->pay_type;
			$data_insert_history['period_amount'] = $loan_row->period_amount;
			$data_insert_history['pay_amount'] = $loan_row->money_per_period;
			$data_insert_history['loan_id'] = $_POST['loan_id'];
			
			$this->db->insert("coop_loan_period_history", $data_insert_history);
			$insert_id = $this->db->insert_id();
			
			$installment = $this->db->get_where("coop_loan_period", array("loan_id" => $_POST['loan_id']) );

			foreach ($installment->result_array() as $key => $value) {
				unset($value['id']);
				$value['history_id']	=	$insert_id;
				$this->db->insert("coop_loan_period_history_list", $value);
			}
			$this->db->where("loan_id", $_POST['loan_id']);
			$this->db->delete("coop_loan_period");
		}
		
		// var_dump($loan);
		$_POST["interest"] 			= 	$this->Interest_modal->get_interest($loan_row->term_of_loan_id, date("Y-m-d"),  $loan_row->id, 'term_of_loan_id');
		$_POST['loan'] 				= 	$loan_row->loan_amount_balance;
		$_POST["period"]			=	($_POST["new_installment_period_type"] == 1) ? $_POST['new_period_amount'] : $_POST['new_period_per_month'];
		$_POST["day"]				=	$loan_row->d;
		$_POST["month"]				=	$loan_row->m-1;
		$_POST["year"]				=	$loan_row->y;
		$_POST["pay_type"]			=	$_POST['new_installment_type'];
		// $_POST["pay_type"]			=	1;
		// init set
		//echo "<pre>"; print_r($_POST); echo "</pre>";
		$get_loan_period = $this->loan_period_models->get_loan_period($_POST);
		//echo "<pre>"; print_r($get_loan_period); echo "</pre>";

		foreach($get_loan_period['peroid_row'] AS $key=>$value){	 
			$peroid_row = array();
			$peroid_row['period_count']			= @$value['period_count'];
			$peroid_row['outstanding_balance']	= @$value['outstanding_balance'];
			$peroid_row['date_period']			= @$value['date_period'];
			$peroid_row['date_count']			= @$value['date_count'];
			$peroid_row['interest']				= @$value['interest'];
			$peroid_row['principal_payment']	= @$value['principal_payment'];
			$peroid_row['total_paid_per_month']	= @$value['total_paid_per_month'];
			$peroid_row['loan_id']				= @$value['loan_id'];
			//echo "<pre>"; print_r($peroid_row); echo "</pre>";
			
			$this->db->insert("coop_loan_period",$peroid_row);
		}
			
		$update_coop_loan['money_per_period']	= $get_loan_period['money_per_period'];
		$update_coop_loan['period_amount']		= $get_loan_period['period_amount'];
		$update_coop_loan['updatetimestamp']	= date("Y-m-d H:i:s");
		$update_coop_loan['pay_type']			= $_POST['new_installment_type'];
		$update_coop_loan['period_type']		= $_POST['new_installment_period_type'];
		$this->db->where("id", $_POST['loan_id']);
		$this->db->update("coop_loan", $update_coop_loan);
		//echo "<pre>"; print_r($_POST); echo "</pre>";
		unset($_POST);

		echo "<script> document.location.href='".base_url(PROJECTPATH.'/loan/manage_installment')."' </script>";
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


        $arr_data['loan_type'] = $this->db->get('coop_loan_name')->result_array();
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
	
	//ระบบประกันชีวิต
	function get_life_insurance(){	
		$data = array();
		$loan_amount = @$_POST['loan_amount'];
		$member_id = @$_POST['member_id'];
		$cremation_all_total = @$_POST['cremation_all_total'];
		$loan_id = @$_POST['loan_id'];
		$date_receive_money = $this->center_function->ConvertToSQLDate(@$_POST['date_receive_money']);
		$year_receive_money = date('Y',strtotime($date_receive_money));
		$month_receive_money = date('m',strtotime($date_receive_money));
		$blue_deposit_loan = @$_POST['blue_deposit_loan'];
		$share_loan = @$_POST['share_loan'];
		$prev_checkbox_array = @$_POST['prev_checkbox_array']; //หักกลบสัญญาเดิม
		$prev_data_type_array = @$_POST['prev_data_type_array']; //หักกลบสัญญาเดิม (ประเภทเงินกู้)
		$loan_type = @$_POST['loan_type'];
		$data['loan_type'] = $loan_type;
		$data['member_id'] = $member_id;
		$data['loan_id'] = $loan_id;
		if($cremation_all_total == ''){
			if(@$loan_id != ''){
				$rs_cremation_type = $this->db->select('import_cremation_type,import_amount_balance')
				->from('coop_life_insurance_cremation')
				->where("member_id = '".$member_id."' AND loan_id = '".$loan_id."'")
				->get()->result_array();
				$data['row_cremation_type'] = @$rs_cremation_type;
				
				foreach($rs_cremation_type AS $key=>$row_cremation_type){
					$cremation_all_total += $row_cremation_type['import_amount_balance'];
				}	
					
				$row_life_insurance = $this->db->select('insurance_year,insurance_date,insurance_amount,insurance_premium')
				->from('coop_life_insurance')
				->where("member_id = '".$member_id."' AND loan_id = '".$loan_id."'")
				->get()->result_array();
				$data['row_life_insurance'] = @$row_life_insurance[0];
			}
		}
		
		//เงินฝากสีน้ำเงิน
		//เช็คสมุดเงินฝากสีน้ำเงิน
		$rs_account = $this->db->select(array(
												'coop_maco_account.account_id',
												'coop_maco_account.mem_id',
												'coop_deposit_type_setting.type_name',
												'(SELECT transaction_balance FROM coop_account_transaction AS t2 WHERE t2.account_id = coop_maco_account.account_id ORDER BY t2.transaction_time DESC LIMIT 1) AS transaction_balance'
										))
		->from('coop_maco_account')
		->join("coop_deposit_type_setting","coop_maco_account.type_id = coop_deposit_type_setting.type_id","inner")
		->where("
			coop_maco_account.mem_id = '{$member_id}'
			 AND coop_maco_account.account_status = '0'
			AND coop_deposit_type_setting.deduct_loan = '1'
		")
		->limit(1)
		->get()->result_array();
		//$data['sql_account']= $this->db->last_query();
		$row_account = @$rs_account[0];

		$blue_deposit_amount = @(@$row_account['transaction_balance']+@$blue_deposit_loan); //เพิ่มเงินฝากสีน้ำเงินจากการกู้
		$data['blue_deposit_loan'] = @$blue_deposit_loan;
		$data['blue_deposit_amount'] = number_format(@$blue_deposit_amount,2);
		
		//หุ้น	
		$row_share = $this->db->select(array('share_collect','share_collect_value'))
		->from('coop_mem_share')
		->where("member_id = '".$member_id."' AND share_status = '1'")
		->order_by('share_date DESC')
		->limit(1)
		->get()->result_array();
		//$data['sql_share']= $this->db->last_query();
		$data['share_collect_value_1'] = @$row_share[0]['share_collect_value'];
		$share_collect_value = @$row_share[0]['share_collect_value']+@$share_loan; //เพิ่มหุ้นจากการกู้	
		$data['share_loan'] = @$share_loan;
		
		$data['share_collect_value'] = number_format(@$share_collect_value,2);
		
		//ทุนประกันเดิม
		if(@$loan_id != ''){
			$where_loan = " AND  (loan_id <> '".$loan_id."' OR loan_id IS NULL)";
		}else{
			$where_loan = "";
		}
		$row_insurance_old = $this->db->select('SUM(insurance_amount) AS insurance_amount')
		->from('coop_life_insurance')
		->where("member_id = '{$member_id}' {$where_loan}  AND insurance_status = '1' AND insurance_year = '".($year_receive_money+543)."'")
		->get()->result_array();
		$data['sql_insurance_old']= $this->db->last_query();
		$insurance_old = @$row_insurance_old[0]['insurance_amount']; 
		$data['insurance_old'] = number_format(@$insurance_old,2);	

		$where_not_loan = "";
		$arr_not_loan = array();
		if(!empty($prev_checkbox_array)){
			foreach(@$prev_checkbox_array AS $key=>$prev_checkbox_data){
				if(@$prev_data_type_array[$key] == 'loan'){
					$arr_not_loan[] = @$prev_checkbox_data;
				}
			}
		}
		if(!empty(@$arr_not_loan)){
			$where_not_loan .= " AND t1.id NOT IN (".implode(",", $arr_not_loan).")";
		}
		//exit;		
		//หนี้สามัญ พิเศษ
		$rs_debt = $this->db->select('
							t1.member_id,
							t1.id,
							t1.contract_number,
							t1.loan_status,
							t1.loan_amount_balance,
							t3.loan_type_code'
						)
		->from('coop_loan AS t1')
		->join("coop_loan_name AS t2","t1.loan_type = t2.loan_name_id","left")
		->join("coop_loan_type AS t3","t2.loan_type_id = t3.id ","left")
		->where("t1.member_id  = '".$member_id."' AND t1.loan_status = '1' AND t3.loan_type_code IN ('normal','special')".$where_not_loan)
		->get()->result_array();
		//$data['sql_debt']= $this->db->last_query();
		$loan_debt_amount = 0;
		foreach($rs_debt AS $key=>$row_debt){
			$loan_debt_amount += $row_debt['loan_amount_balance'];
		}	
		
		//ทุนประกันใหม่
		//การคำนวณทุนประกันชีวิต =   (ยอดกู้+หนี้สามัญ พิเศษ)  – (หุ้นที่มี+เงินฝากสีน้ำเงิน) 		
		//การคำนวณทุนประกันชีวิต =   (ยอดกู้+หนี้สามัญ พิเศษ)  – (หุ้นที่มี+เงินฝากสีน้ำเงิน) -ชสอ.-	สสอค. ที่เลือก (ตอนเลือกค่อยลบออก)
		$insurance_new = (@$loan_amount+@$loan_debt_amount)-(@$share_collect_value+@$blue_deposit_amount)-@$cremation_all_total;
		$data['cal_insurance_new'] = '('.@$loan_amount.'+'.@$loan_debt_amount.')-('.@$share_collect_value.'+'.@$blue_deposit_amount.')-'.@$cremation_all_total;
		//$insurance_new = (@$loan_amount)-(@$share_collect_value+@$blue_deposit_amount)-@$cremation_all_total;
		//$data['cal_insurance_new'] = '('.@$loan_amount.')-('.@$share_collect_value.'+'.@$blue_deposit_amount.')-'.@$cremation_all_total;
		$insurance_new = ceil($insurance_new/100000)*100000;
		$data['loan_amount'] = $loan_amount;
		$data['loan_debt_amount'] = $loan_debt_amount;
		$insurance_new = (@$insurance_new > 0)?@$insurance_new:0;
		$data['insurance_new'] = @$insurance_new;
		
		
		//เบี้ยประกันชีวิต
		/*
		1.	เมื่อสมาชิกมากู้เงินตั้งแต่ 1  -31 ธ.ค. สหกรณ์จะเก็บข้อมูลประกันชีวิตใหม่ของปีถัดไป 
		โดยเวลากู้ จะเก็บเบี้ยประกันส่วนที่คุ้มครอง 1 – 31 ธ.ค. ปีนี้ 1 ยอด และ เก็บเบี้ยประกันส่วนที่
		คุ้มครอง 1 ม.ค. -31 ธ.ค. ปีหน้า 
		ตัวอย่าง
		สมาชิกกู้ไว้ 2,500,000 บาท มีหนี้คงเหลือ 2,000,000บาท หุ้น 600,000บาท ทุนประกันเดิม  2,000,000 บาท
		วันที่ 12 ธันวาคม 2561 มากู้ใหม่ 3,000,000 บาท หุ้นมี 600,000 บาท จะต้องเก็บทุนประกันดังนี้
			1)	ทุนประกันเพื่อคุ้มครอง 1-31 ธ.ค. 61 มีทำทุนประกันไว้ 2,000,000 บาทแล้วต้องทำทุนประกันเพิ่ม 400,000 บาท ดังนั้นต้องเก็บเบี้ยประกันตามส่วนต่างที่เพิ่มขึ้น
			2)	ทุนประกันเพื่อคุ้มครอง 1 ม.ค. -31 ธ.ค. 62  ต้องทำทุนประกัน 2,400,000 บาท 
		2.	หากสมาชิกไม่มีการกู้ในเดือนธันวาคม เราจะนำหนี้คงเหลือมาคำนวณทุนประกันใหม่เพื่อคุ้มครองในปีต่อไป 
		การคำนวณทุนประกันชีวิต =   ยอดกู้หรือยอดหนี้คงเหลือ  – (หุ้นที่มี+เงินฝากสีน้ำเงิน) 
		แต่ถ้าสมาชิกคนไหนมี ฌาปนกิจครูไทย (600,000)  ฌาปนกิจชุมนุมสหกรณ์ (600,000)  จะนำมาหักออกถึงจะได้ทุนประกัน 
		ตัวอย่าง
		สมาชิกกู้ไว้ 2,500,000 บาท มีหนี้คงเหลือ 2,000,000บาท หุ้น 600,000บาท เป็นสมาชิกฌาปนกิจครูไทย
		ทำให้มีทุนประกันเดิม  1,400,000 บาท
		วันที่ 12 ธันวาคม 2561 มากู้ใหม่ 3,000,000 บาท หุ้นมี 600,000 บาท จะต้องเก็บทุนประกันดังนี้
			1)	ทุนประกันเพื่อคุ้มครอง 1-31 ธ.ค. 61 มีทำทุนประกันไว้ 1,400,000 บาทแล้วต้องทำทุนประกันเพิ่ม 400,000 บาท ต้องเก็บเบี้ยประกัน = 115.51 บาท
			2)	ทุนประกันเพื่อคุ้มครอง 1 ม.ค. -31 ธ.ค. 62 บาท ต้องทำทุนประกัน 2,400,000 บาท แต่เนื่องจากเป็นสมาชิกครูไทย ก็นำ 600,000 บาท ไปหักจาก 2,400,000 บาท จะเหลือ 1,800,000 บาท ต้องเก็บเบี้ยประกัน = 6,120 บาท

		*/
		
		$data['date_receive_money'] = $date_receive_money;
		$data['year_receive_money'] = ($year_receive_money+543);
		$data['month_receive_money'] = $month_receive_money;
		
		
		
		//เมื่อสมาชิกมากู้เงินตั้งแต่ 1  -31 ธ.ค. สหกรณ์จะเก็บข้อมูลประกันชีวิตใหม่ของปีถัดไป 
		if((int)$month_receive_money == 12){
			$arr_data_life = array();
			$arr_data_life['start_date_insurance'] = (@$year_receive_money+1).'-01-01';
			$arr_data_life['end_date_insurance'] =  (@$year_receive_money+1).'-12-31';
			$arr_data_life['insurance_new'] = @$insurance_new;
			$arr_data_life['insurance_old'] = @$insurance_old;
			$arr_life_insurance_1 = $this->life_insurance_libraries->get_deduct_insurance(@$arr_data_life,'loan');
			//echo '<pre>'; print_r($arr_life_insurance_1); echo '</pre>';
		}
		
		//กรณีมากู้ตั้งแต่ 1 ม.ค. -30 พ.ย. 
		$arr_data_life = array();
		$arr_data_life['start_date_insurance'] = @$year_receive_money.'-'.sprintf("%02d",@$month_receive_money).'-01';
		$arr_data_life['end_date_insurance'] =  @$year_receive_money.'-12-31';
		$arr_data_life['insurance_new'] = @$insurance_new;
		$arr_data_life['insurance_old'] = @$insurance_old;
		$arr_life_insurance_2 = $this->life_insurance_libraries->get_deduct_insurance(@$arr_data_life,'loan');
		//echo '<pre>'; print_r($arr_life_insurance_2); echo '</pre>';
		
		//เช็คการเปิดใช้งานระบบประกันชีวิต
		$this->db->select('life_insurance');
		$this->db->from("coop_term_of_loan");
		$this->db->where("type_id = '".$loan_type."' AND start_date <= '".date('Y-m-d')."'");
		$this->db->order_by('start_date DESC');
		$this->db->limit(1);
		$row_term_of_loan = $this->db->get()->result_array();
		$data['check_life_insurance'] = @$row_term_of_loan[0]['life_insurance'];
		
		$data['start_date_insurance'] = $arr_life_insurance_2['start_date_insurance'];
		$data['end_date_insurance'] = $arr_life_insurance_2['end_date_insurance'];
		$data['start_date_protection'] = $arr_life_insurance_2['start_date_protection'];
		$data['end_date_protection'] = $arr_life_insurance_2['end_date_protection'];
		$data['num_date_protection'] = $arr_life_insurance_2['num_date_protection'];
		$data['additional_insured'] = $arr_life_insurance_2['additional_insured'];
		$data['s_insurance_life_premium'] =$arr_life_insurance_2['s_insurance_life_premium'];
		$data['cal_life_premium'] = $arr_life_insurance_2['cal_life_premium'];
		$data['cal_life_premium_all'] =$arr_life_insurance_2['cal_life_premium_all'];
		$data['s_insurance_defective_premium'] = $arr_life_insurance_2['s_insurance_defective_premium'];
		$data['cal_defective_premium'] = $arr_life_insurance_2['cal_defective_premium'];
		$data['cal_defective_premium_all'] = $arr_life_insurance_2['cal_defective_premium_all'];
		$data['s_insurance_accident_premium'] = $arr_life_insurance_2['s_insurance_accident_premium'];
		$data['cal_accident_premium'] = $arr_life_insurance_2['cal_accident_premium'];
		$data['cal_accident_premium_all'] = $arr_life_insurance_2['cal_accident_premium_all'];	
		$deduct_insurance = @$arr_life_insurance_2['deduct_insurance']+@$arr_life_insurance_1['deduct_insurance']; 
		$data['deduct_insurance'] = number_format((@$deduct_insurance > 0)?@$deduct_insurance:0,2);
		$data['check_life_insurance'] = @$row_term_of_loan[0]['life_insurance'];
		
		echo json_encode($data);
		exit;
	}

	public function calc_atm_csv(){
		$member_id = sprintf("%06d", @$_GET['member_id']);
		$loan_atm_id = @$this->db->get_where("coop_loan_atm", array(
			"member_id" => $member_id,
			"loan_atm_status" => 1
		))->result_array()[0]['loan_atm_id'];
		if(!$loan_atm_id)
			die("no atm id;");
		
		
		$is_process = false;
		if(@$_GET['is_process']){
			$date_month_end = date('Y-m-t',strtotime((@$_GET['year']-543).'-'.sprintf("%02d",@$_GET['month']).'-01'));
			$is_process = true;
		}else{
			$date_month_end = date('Y-m-d');
			$is_process = false;
		}

		$cal_loan_interest = array();
		$cal_loan_interest['loan_atm_id'] = $loan_atm_id;
		$cal_loan_interest['date_interesting'] = $date_month_end;
		$this->loan_libraries->cal_atm_interest_report_test($cal_loan_interest,"echo", array("month"=>@$_GET['month'], "year" => (@$_GET['year']-543) ), $is_process, !$is_process);
	}
	
	public function step_logic(){
		$this->libraries->template('loan/step_logic');
	}
	
	public function calc_insurance_csv(){
		$obj = json_decode(@$_GET['data_array']);

		header('Content-Encoding: UTF-8');
		header('Content-type: text/csv; charset=UTF-8');
		header('Content-Disposition: attachment; filename="คำนวณเงินเบี้ยประกัน เลขสมาชิก "'.$obj->member_id.'".csv"');
		echo "\xEF\xBB\xBF";
		$fp = fopen('php://output', 'wb+');
		
		$str = ",";

		$str = " , , , , , , , , ,วันเริ่มกรมธรรม์ ,".$obj->start_date_insurance;
		fputcsv($fp, explode(",", $str));
		$str = " , , , , , , , , ,วันสิ้นสุดกรมธรรม์ ,".$obj->end_date_insurance;
		fputcsv($fp, explode(",", $str));
		$str = " , , , , , , , , ,จำนวนวันทั้งปี ,".$obj->num_date_insurance;
		fputcsv($fp, explode(",", $str));
		$str = " , , , , , , , , , ,Life, ,TPD, ,ADD, ,เบี้ยประกัน";		
		fputcsv($fp, explode(",", $str));
		
		$str = " , , ,ทุนประกัน ,เบี้ยประกัน , วันเริ่ม ความคุ้มครอง, วันสิ้นสุด ความคุ้มครอง, จำนวนวันคุ้มครอง, หมายเหตุ,จำนวนวัน ,".
		$obj->s_insurance_life_premium.", ,".$obj->s_insurance_defective_premium.", ,".$obj->s_insurance_accident_premium;						
		fputcsv($fp, explode(",", $str));
		
		$str = " , , , ".$obj->additional_insured.
		",=".$obj->cal_life_premium_all."+".$obj->cal_defective_premium_all."+".$obj->cal_accident_premium_all.
		",".$obj->start_date_protection.",".$obj->end_date_protection.",".$obj->num_date_protection.
		", ,".$obj->num_date_protection." ,".$obj->cal_life_premium.",".$obj->cal_life_premium_all.
		" ,".$obj->cal_defective_premium.",".$obj->cal_defective_premium_all.
		" ,".$obj->cal_accident_premium." ,".$obj->cal_accident_premium_all.
		",=".$obj->cal_life_premium_all."+".$obj->cal_defective_premium_all."+".$obj->cal_accident_premium_all;						
		fputcsv($fp, explode(",", $str));
		fclose($fp);
		exit;	
	}
	
	public function get_period()
	{		
		$data = array();
		$this->db->select(array("coop_loan.*", "DAY(approve_date) AS d", "MONTH(approve_date) AS m", "YEAR(approve_date) AS y",
	"(SELECT id FROM coop_term_of_loan WHERE coop_loan.loan_type = coop_term_of_loan.type_id AND start_date <= CURDATE() ORDER BY start_date DESC limit 1) as term_of_loan_id"));
		$loan_row = $this->db->get_where("coop_loan", array("id" => $_POST['loan_id']) )->result()[0];
		//echo $this->db->last_query(); echo '<hr>';
		// var_dump($loan);
		$_POST["interest"] 			= 	$this->Interest_modal->get_interest($loan_row->term_of_loan_id, date("Y-m-d"),  $loan_row->id, 'term_of_loan_id');
		$_POST['loan'] 				= 	$loan_row->loan_amount_balance;
		$_POST["period"]			=	(@$_POST["new_installment_period_type"] == 1) ? @$_POST['new_period_amount'] : @$_POST['new_period_per_month'];
		$_POST["day"]				=	$loan_row->d;
		$_POST["month"]				=	$loan_row->m-1;
		$_POST["year"]				=	$loan_row->y;
		$_POST["pay_type"]			=	@$_POST['new_installment_type'];

		$get_loan_period = $this->loan_period_models->get_loan_period(@$_POST);
		//echo "<pre>"; print_r($_POST); echo "</pre>";
		//echo "<pre>";
		//print_r(@$get_loan_period);
		//echo "</pre>";		
		$data['text'] = @$get_loan_period['check_period'];
		$data['money_per_period'] = number_format(@$get_loan_period['money_per_period'],2);
		$data['period_amount'] = @$get_loan_period['period_amount'];		
		echo json_encode($data);
		exit;
	}
	
	//เช็คเงินเดือนคงเหลือ
	function check_salary_balance(){	
		$data = array();
		$member_id = @$_POST['member_id'];
		$loan_id = @$_POST['loan_id'];
		$loan_cost_total = @$_POST['loan_cost_total'];
		$date_receive_money = $this->center_function->ConvertToSQLDate(@$_POST['date_receive_money']);
		$year_now = date('Y',strtotime($date_receive_money))+543;
		$month_now = date('n',strtotime($date_receive_money));
		
		$loan_amount = @$_POST['loan_amount'];
		$interest_per_year = @$_POST['interest_per_year'];
		$principal_payment = @$_POST['principal_payment'];
		$total_paid_per_month = @$_POST['total_paid_per_month'];
		$pay_type_id = @$_POST['pay_type_id'];
		$deduct_pay_prev_loan = @$_POST['deduct_pay_prev_loan'];
		$prev_checkbox_array = @$_POST['prev_checkbox_array'];
		$prev_data_type_array = @$_POST['prev_data_type_array'];
		$prev_pay_type_array = @$_POST['prev_pay_type_array'];
		
		//echo $loan_cost_total.'<hr>'; exit;
			
		$this->db->select('coop_mem_apply.salary,coop_mem_apply.other_income,coop_mem_apply.member_id,coop_mem_apply.share_month,coop_mem_apply.apply_type_id');
		$this->db->from('coop_mem_apply');
		$this->db->where("coop_mem_apply.member_id = '".$member_id."'");
		$rs_member = $this->db->get()->result_array();
		$row_member = $rs_member[0];
			
		$salary = @$row_member['salary'];
		$other_income = @$row_member['other_income'];
		$total_income =  @$salary+@$other_income;
		
		//ค่าใช้จ่าย
		$total_expenses = $loan_cost_total;
		$total_pay_external = @$total_expenses;
		
		$date_interesting = date('Y-m-d');
		$list_old_loan = array();
		
		$index = 0;
		if(!empty($prev_checkbox_array )){
			foreach($prev_checkbox_array AS $key=>$val_prev){
				$list_old_loan[$index]['loan_id'] = @$val_prev;
				$index++;
			}
		}
		
		$this->db->select(array(
			't1.*',
			't3.loan_name as loan_type_detail',
			't3.loan_type_id',
			't4.id',
			't5.bank_name',
			't6.account_name',
			't7.user_name AS admin_name'
		));
		$this->db->from('coop_loan as t1');			
		$this->db->join('coop_loan_name as t3','t1.loan_type = t3.loan_name_id','inner');
		$this->db->join("coop_loan_type as t4",'t3.loan_type_id = t4.id','inner');
		$this->db->join("coop_bank as t5",'t1.transfer_bank_id = t5.bank_id','left');
		$this->db->join("coop_maco_account as t6",'t1.transfer_account_id = t6.account_id','left');
		$this->db->join("coop_user AS t7",'t1.admin_id = t7.user_id','left');
		$this->db->where("t1.member_id = '".$member_id."' AND t1.id='".$loan_id."'");
		$this->db->order_by("t1.id DESC");
		$rs_loan = $this->db->get()->result_array();
		$row_loan =  @$rs_loan[0];
		 
		if(@$pay_type_id == '1'){
			$total_paid_per_month = @round(@$principal_payment,-2);
			$pay_type_name = "แบบคงต้น";
			
			//ดอกเบี้ย 30 วัน ของจากยอดกู้เต็ม
			$date_count = 30;
			$interest_30_day = (((@$loan_amount*@$interest_per_year)/100)/365)*@$date_count;
			if(@$_GET['dev'] == 'dev'){
				echo '((('.@$loan_amount.'*'.@$interest_per_year.')/100)/365)*'.@$date_count.'<br>';
			}	
			$interest_30_day = round(@$interest_30_day);
		}else{
			$total_paid_per_month = @round(@$total_paid_per_month,-2);
			$pay_type_name = "แบบคงยอด";
			$interest_30_day  = 0;
		}
		
		$pay_type = @$pay_type_name;			
		$pay_type_id = @$pay_type_id;
			
		//รายการผ่อนชำระสหกรณ์ปัจจุบัน/เดือน
		$date_month_end = date('Y-m-t',strtotime((@$year_now-543).'-'.sprintf("%02d",@$month_now).'-01'));
		
		$this->db->select(array('coop_finance_month_detail.*','coop_finance_month_profile.*','coop_loan_name.loan_type_id'));
		$this->db->from('coop_finance_month_detail');
		$this->db->join('coop_finance_month_profile', 'coop_finance_month_detail.profile_id = coop_finance_month_profile.profile_id', 'left');
		$this->db->join('coop_loan', 'coop_finance_month_detail.loan_id = coop_loan.id', 'left');
		$this->db->join('coop_loan_name', 'coop_loan.loan_type = coop_loan_name.loan_name_id', 'left');
		$this->db->where("
					coop_finance_month_detail.member_id = '".@$member_id."'
					AND coop_finance_month_profile.profile_month = '".$month_now."'
					AND coop_finance_month_profile.profile_year = '".$year_now."'
				");
		$row_finance_month = $this->db->get()->result_array();
		//echo $this->db->last_query(); 
		//echo '<pre>'; print_r($row_finance_month); echo '</pre>';
		if(!empty($row_finance_month)){
			//ออกรายการเรียกเก็บประจำเดือนแล้ว
			$arr_list_loan = array();
			foreach($row_finance_month AS $key_month=>$value_month){
				//echo @$value_month['deduct_code'].'<br>';
				if(@$value_month['deduct_code'] == 'SHARE'){
					//หุ้นหักรายเดือน
					$share_month = @$value_month['pay_amount']; //หุ้นหักรายเดือน(เงินต้น)
					$share_month_interest = 0; //หุ้นหักรายเดือน(ดอกเบี้ย)
				}						
				
				if(@$value_month['deduct_code'] == 'DEPOSIT'){
					//เงินฝากหักรายเดือน
					$deposit_month =  @$value_month['pay_amount']; //เงินฝากหักรายเดือน(เงินต้น)
					$deposit_month_interest = 0;	 //เงินฝากหักรายเดือน(ดอกเบี้ย)			
				}
				
				if(@$value_month['deduct_code'] == 'LOAN'){
					if(@$value_month['pay_type'] == 'principal'){
						$arr_list_loan[@$value_month['loan_type_id']]['loan_principle'] = @$value_month['pay_amount'];//ยอดที่ชำระต่อเดือน เงินต้น
					}
					
					if(@$value_month['pay_type'] == 'interest'){
						$arr_list_loan[@$value_month['loan_type_id']]['loan_interest'] = @$value_month['pay_amount'];//(ดอกเบี้ย)
					}
					$arr_list_loan[@$value_month['loan_type_id']]['loan_id'] = @$value_month['loan_id'];//loan_id
					
				}
				
				if(@$value_month['deduct_code'] == 'ATM'){
					if(@$value_month['pay_type'] == 'principal'){
						@$arr_list_loan[7]['loan_principle'] += @$value_month['pay_amount'];//ยอดที่ชำระต่อเดือน เงินต้น
					}
					
					if(@$value_month['pay_type'] == 'interest'){
						@$arr_list_loan[7]['loan_interest'] += @$value_month['pay_amount'];//(ดอกเบี้ย)
					}
					$arr_list_loan[7]['loan_id'] = @$value_month['loan_atm_id'];//loan_id
				}
				
				
			}			
			
			$this->db->select(array('*'));
			$this->db->from('coop_loan_type');
			$loan_type = $this->db->get()->result_array();
			$list_loan = array();
			$loan_principle_total = 0;
			$loan_interest_total = 0;
			if(!empty($loan_type)){
				foreach($loan_type AS $key=>$value){						
					$list_loan[$value['id']]['loan_name']= $value['loan_type'];//ชื่อเงินกู้หลัก
					$list_loan[$value['id']]['loan_principle']= @$arr_list_loan[$value['id']]['loan_principle'];//ยอดที่ชำระต่อเดือน
					$list_loan[$value['id']]['loan_interest'] = @$arr_list_loan[$value['id']]['loan_interest'];//(ดอกเบี้ย)
					$loan_principle_total += @$arr_list_loan[$value['id']]['loan_principle'];
					$loan_interest_total += @$arr_list_loan[$value['id']]['loan_interest'];
				}
			}
			//$arr_data['list_loan'] = @$list_loan;	
			
		}else{			
			//ยังไม่ได้ออกรายการเรียกเก็บประจำเดือน
			$arr_list_loan = array();
			$this->db->select('setting_value');
			$this->db->from('coop_share_setting');
			$this->db->where("setting_id = '1'");
			$row = $this->db->get()->result_array();
			$row_share_value = $row[0];
			$share_value = $row_share_value['setting_value'];
		
			$this->db->select(array('deduct_id','deduct_code','deduct_detail','deduct_type','deduct_format','deposit_type_id','deposit_amount'));
			$this->db->from('coop_deduct');
			$this->db->order_by('deduct_seq ASC');
			$deduct_list = $this->db->get()->result_array();
			foreach($deduct_list as $key2 => $value2){				
				//หุ้นหักรายเดือน
				if($value2['deduct_code']=='SHARE'){
					//งดหุ้นชั่วคราว
					$check_refrain_share = 0;
					$this->db->select('*');
					$this->db->from('coop_refrain_share');
					$this->db->where("member_id = '".$row_member['member_id']."' AND type_refrain = '2' AND month_refrain = '".@$month_now."' AND year_refrain = '".@$year_now."'");
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
					$this->db->where("member_id = '".$row_member['member_id']."' AND type_refrain = '1'");
					$this->db->order_by('refrain_id DESC');			
					$rs_refrain_permanent = $this->db->get()->result_array();
					if(!empty($rs_refrain_permanent)){
						foreach($rs_refrain_permanent AS $key=>$value){
							$check_refrain_share = 1;
						}
					}				
					
					//ทุนเรือนหุ้น
					if(@$row_member['apply_type_id'] == '1' && $check_refrain_share == 0){															
						$share = @$row_member['share_month'];
						$share_month = @$share; //หุ้นหักรายเดือน(เงินต้น)
						$share_month_interest = 0; //หุ้นหักรายเดือน(ดอกเบี้ย)
					}
				}
				
				//เงินฝากหักรายเดือน
				if($value2['deduct_code']=='DEPOSIT'){							
					//เงินฝาก	
					$sum_deposit = 0;
					$DEPOSIT = 0;						
					$deposit_type_id = @$value2['deposit_type_id'];
					$DEPOSIT = @$value2['deposit_amount'];
					//echo $deposit_type_id.'<hr>';
					$deposit_period_count = 1;
					$deposit_balance = $DEPOSIT;
					
					$this->db->select('*');
					$this->db->from('coop_maco_account');
					$this->db->where("mem_id = '".@$row_member['member_id']."' AND type_id = '".@$deposit_type_id."'");
					$this->db->limit(1);
					$rs_account = $this->db->get()->result_array();
					$account_id = @$rs_account[0]['account_id'];
					if(!empty($account_id)){												
						if($DEPOSIT > 0){						
							$sum_deposit += @$DEPOSIT;
						}
					}

					$deposit_month =  @$sum_deposit; //เงินฝากหักรายเดือน(เงินต้น)
					$deposit_month_interest = 0; //เงินฝากหักรายเดือน(ดอกเบี้ย)						
				}
			
				
				if(@$value2['deduct_code'] == 'LOAN'){										
					$LOAN = array();
					$where = '';
					$where .= " AND (coop_loan.guarantee_for_id = '' OR coop_loan.guarantee_for_id IS NULL) ";
	
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
							'coop_loan.guarantee_for_id',
							'coop_loan_name.loan_type_id'
						)
					);
					$this->db->from('coop_loan');
					$this->db->join('coop_loan_transfer', 'coop_loan_transfer.loan_id = coop_loan.id', 'left');
					$this->db->join('coop_loan_name', 'coop_loan_name.loan_name_id = coop_loan.loan_type', 'inner');
					$this->db->where("
						coop_loan.loan_amount_balance > 0
						AND coop_loan.member_id = '".$row_member['member_id']."'
						AND coop_loan.loan_status = '1'
						AND coop_loan_transfer.transfer_status = '0'
						AND coop_loan.date_start_period <= '".($year_now-543)."-".sprintf("%02d",$month_now)."-".date('t',strtotime(($year_now-543)."-".$month_now."-01"))."' 
					".$where);
					$row_loan_month = $this->db->get()->result_array();
					//echo $this->db->last_query().";";
					$j=0;
					
					foreach($row_loan_month as $key => $row_normal_loan){
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
							//$LOAN[$j]['loan_type_id'] = $row_normal_loan['loan_type'];
							$LOAN[$j]['loan_type_id'] = $row_normal_loan['loan_type_id'];
							$LOAN[$j]['contract_number'] = $row_normal_loan['contract_number'];
							$LOAN[$j]['money_period_1'] = $row_normal_loan['money_period_1'];
							$LOAN[$j]['pay_loan_type'] = $row_normal_loan['pay_type'];
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
							$arr_list_loan[@$value3['loan_type_id']]['loan_principle'] = @$value3['principal_payment'];//ยอดที่ชำระต่อเดือน เงินต้น							
							$arr_list_loan[@$value3['loan_type_id']]['loan_interest'] = @$value3['interest'];//(ดอกเบี้ย)
							$arr_list_loan[@$value3['loan_type_id']]['loan_id'] = @$value3['loan_id'];//loan_id
						}
					}
					
					//echo '<pre>'; print_r($arr_list_loan); echo '</pre>';
				}
				
				if(@$value2['deduct_code'] == 'ATM'){					
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
						t2.member_id = '".$row_member['member_id']."'
						AND t2.loan_atm_status = '1'
						AND t1.date_start_period <= '".$date_month_end."'
						AND t1.loan_status = '0'
					");
					$row_atm = $this->db->get()->result_array();
					$principal_per_month = 0;
					$loan_amount_balance = 0;
					if(!empty($row_atm)){
						foreach($row_atm as $key_atm => $value_atm){
							$loan_atm_id = @$value_atm['loan_atm_id'];
							$principal_per_month += @$value_atm['principal_per_month'];
							$loan_amount_balance += @$value_atm['loan_amount_balance'];
						}
						if(@$principal_per_month < @$loan_atm_setting['min_principal_pay_per_month']){
							$principal_per_month = @$loan_atm_setting['min_principal_pay_per_month'];
						}
						if(@$principal_per_month > @$loan_amount_balance){
							$principal_per_month = @$loan_amount_balance;
						}
						
						$cal_loan_interest = array();
						$cal_loan_interest['loan_atm_id'] = @$loan_atm_id;
						$cal_loan_interest['date_interesting'] = @$date_month_end;
						$interest = $this->loan_libraries->cal_atm_interest(@$cal_loan_interest);
						
						
						$deduct_format = @$value2['deduct_format'];
						if($deduct_format == '2'){
							@$arr_list_loan[7]['loan_principle'] += @$principal_per_month;//ยอดที่ชำระต่อเดือน เงินต้น
						}else{
							@$arr_list_loan[7]['loan_interest'] += @$interest;//(ดอกเบี้ย)
						}
						$arr_list_loan[7]['loan_id'] = @$loan_atm_id;//loan_id
					}
				}
			}
			
				
			$this->db->select(array('*'));
			$this->db->from('coop_loan_type');
			$loan_type = $this->db->get()->result_array();
			$list_loan = array();
			$loan_principle_total = 0;
			$loan_interest_total = 0;
			if(!empty($loan_type)){
				foreach($loan_type AS $key=>$value){						
					$list_loan[$value['id']]['loan_name']= $value['loan_type'];//ชื่อเงินกู้หลัก
					$list_loan[$value['id']]['loan_principle']= @$arr_list_loan[$value['id']]['loan_principle'];//ยอดที่ชำระต่อเดือน
					$list_loan[$value['id']]['loan_interest'] = @$arr_list_loan[$value['id']]['loan_interest'];//(ดอกเบี้ย)
					$loan_principle_total += @$arr_list_loan[$value['id']]['loan_principle'];
					$loan_interest_total += @$arr_list_loan[$value['id']]['loan_interest'];
				}
			}
			//$arr_data['list_loan'] = @$list_loan;	
		}
		$total_month = @$share_month+@$deposit_month+@$loan_principle_total;//รวมทั้งสิ้น รายการผ่อนชำระสหกรณ์ปัจจุบัน/เดือน
		$total_month_interest = @$share_month_interest+@$deposit_month_interest+@$loan_interest_total;//รวมทั้งสิ้น รายการผ่อนชำระสหกรณ์ปัจจุบัน/เดือน(ดอกเบี้ย)
	
		//ถ้าเขาหักกลบก็เอารายเดือนยอดใหม่เลย แต่ถ้าไม่หักกลบก็ต้องเอายอดที่ต้องผ่อนหนี้เก่ามาด้วย
		if(!empty($list_old_loan)){
			$type_offset = 1; //0=ไม่หักกลบ,1=หักลบ  
		}else{
			$type_offset = 0; //0=ไม่หักกลบ,1=หักลบ  
		}
		//echo 'share_month='.$share_month.'<hr>';
		//echo 'deposit_month='.$deposit_month.'<hr>';
		//echo 'loan_principle_total='.$loan_principle_total.'<hr>';
		//echo 'total_month='.$total_month.'<hr>';
		//echo 'total_month_interest='.$total_month_interest.'<hr>';
		//echo 'total_paid_per_month='.$total_paid_per_month.'<hr>';
		//echo 'interest_30_day='.$interest_30_day.'<hr>';
		$data['type_offset'] = @$type_offset;
		if($type_offset == 0){
			//ค่าหุ้น600+งวดสามัญ ต้น10779+งวดสามัญ ดอก12221 และเงินงวดฉุกเฉินที่กู้ใหม่คือ 600+66 เป็นค่าใช้จ่ายภายในสหกรณ์
			$total_pay_in = @$total_month+@$total_month_interest+@$total_paid_per_month+@$interest_30_day;

		}else if($type_offset == 1){
			$loan_list_loan = 0;
			$loan_list_loan_interest = 0;
			foreach($list_old_loan AS $key_old=>$value_old){
				foreach($arr_list_loan AS $key_list=>$value_list){
					if($value_old['loan_id'] == $value_list['loan_id']){
						$loan_list_loan += @$value_list['loan_principle'];		
						$loan_list_loan_interest += @$value_list['loan_interest'];		
					}
				}	
			}
			//ตัวอย่างการคิดยอดหักภายในสหกรณ์( สามัญต้น 5000+ดอก 7228+หุ้น1000+ฉฉที่กู้ใหม่ 4600+520
			$total_pay_in = (@$total_month + $total_month_interest - @$loan_list_loan - @$loan_list_loan_interest)+@$total_paid_per_month+@$interest_30_day;
			$data['total_pay_in'] ='('.@$total_month.' + '.$total_month_interest.' - '.@$loan_list_loan.' - '.@$loan_list_loan_interest.')+'.@$total_paid_per_month.'+'.@$interest_30_day;
			/*
				2019_02_15 แก้ไข เงื่อนไข การณีชำระแบบคงต้น
				ค่าใช้จ่ายภายในสหกรณ์
				1.เงินกู้ใหม่ เงินต้น 4,700 บาท
				2.เงินกู้ใหม่ ดอกเบี้ย 6,837 บาท
				3.หุ้นรายเดือน 600 บาท
				รวมเป็น 12,137 บาท
			*/
		}	
		
								
		
		$total_pay_all = @$total_pay_external+@$total_pay_in;
		$data['total_pay_all'] = @$total_pay_external.'+'.@$total_pay_in;
		$data['total_pay_all_a'] = @$total_pay_external+@$total_pay_in;
		$total_amount_all = @$total_income-@$total_pay_all;
		$percent_amount_all = @(@$total_amount_all*100/@$total_income);
		/*echo 'total_pay_external='.@$total_pay_external.'<hr>';
		echo 'total_pay_in='.@$total_pay_in.'<hr>';
		echo 'total_income='.@$total_income.'<hr>';
		echo 'total_pay_all='.@$total_pay_all.'<hr>';
		echo 'total_amount_all='.@$total_amount_all.'<hr>';
		*/
		$data['salary_balance'] = (@$total_amount_all == 0)?"-":number_format(@$total_amount_all,2);
		$data['percent_salary_balance'] = (@$percent_amount_all <= 0)?"-":number_format(@$percent_amount_all,2);
		
		echo json_encode($data);
		exit;
	}
	
	//หายอดหักกลบ กรณีมีการเปลี่ยนแปลงวันที่ทำรายการได้	
	function check_prev_loan(){		
		$member_id = @$_POST['member_id'];
		$arr_createdatetime = explode('/',@$_POST['createdatetime']);
		$data_createdatetime = ($arr_createdatetime[2]-543)."-".$arr_createdatetime[1]."-".$arr_createdatetime[0];
		$createdatetime = (@$_POST['createdatetime'] != '')?@$data_createdatetime:date('Y-m-d');

		$arr_data = array();
		$prev_loan_active_arr = array();
		$i=0;
		$this->db->select(array(
			'*'
		));
		$this->db->from('coop_loan as t1');
		$this->db->where("t1.member_id = '".$member_id."' AND t1.loan_status = '1' AND t1.loan_amount_balance <> 0");
		$prev_loan_active = $this->db->get()->result_array();
		foreach($prev_loan_active as $key => $value){
			$prev_loan_active_arr[$i]['ref_id'] = $value['id'];
			$prev_loan_active_arr[$i]['contract_number'] = $value['contract_number'];
			$prev_loan_active_arr[$i]['loan_amount_balance'] = $value['loan_amount_balance'];
			$prev_loan_active_arr[$i]['checked'] = "principal";
			$loan_amount = $value['loan_amount_balance'];//เงินกู้
			$loan_id = $value['id'];//ใช้หาเรทดอกเบี้ยใหม่ 26/5/2562
			$date1 = $value['date_last_interest'];//วันคิดดอกเบี้ยล่าสุด
			$date2 = $createdatetime;
			$loan_type = $value['loan_type'];

			$interest_loan = 0;
			$interest_loan = $this->loan_libraries->calc_interest_loan_type_with_loan_and_member_id($loan_amount, $loan_type, $date1, $date2, $member_id, $loan_id);
			$interest_loan = ROUND($interest_loan, 0);

			$fee = 0;
			$prev_loan_active_arr[$i]['fee'] = $fee;
			$prev_loan_active_arr[$i]['interest'] = $interest_loan;
			$prev_loan_active_arr[$i]['type'] = 'loan';
			$prev_loan_active_arr[$i]['prev_loan_total'] = $value['loan_amount_balance'];


			if(date_create($value['date_last_interest']) >= date_create($createdatetime)){

				$prev_loan_active_arr[$i]['principal_without_finance_month'] = $value['loan_amount_balance'];
			}else {

				$this->db->select(array(
					'*'
				));
				$this->db->from('coop_finance_month_detail as t1');
				$this->db->where("
				t1.loan_id = '" . $value['id'] . "'
				AND t1.run_status = '0'
				AND t1.pay_type = 'principal'
			");
				$row = $this->db->get()->result_array();
				$principal_month = 0;
				foreach ($row as $key2 => $value2) {
					$principal_month += $value2['pay_amount'];
				}
				$prev_loan_active_arr[$i]['principal_without_finance_month'] = $value['loan_amount_balance'] - $principal_month;
			}
			$i++;
		}
		
		$arr_data['prev_loan_active'] = $prev_loan_active_arr;
		echo json_encode($arr_data);
		
		exit;
	}

	public function calc_bf_interest(){
	    if($this->input->post()) {
            $loan_amount = $this->input->post('loan_amount');
            $loan_type = $this->input->post('loan_type');
            $date1 = $this->input->post('date_receive_money');
            $date2 = date('Y-m-t', strtotime($date1));
            $interest = $this->loan_libraries->calc_interest_loan_type($loan_amount, $loan_type, $date1, $date2);
            header("content-type: application/json; charset=utf-8;");
            echo json_encode(array('interest' => round($interest, 0)));
            exit;
        }
        header("content-type: application/json; charset=utf-8;");
        echo json_encode(array('interest' => 0));
        exit;

    }

    public function get_loan_type(){
	    header('Content-type:application/json; charset=utf8;');
        $data = array('status' => 'error', 'ref_id' => '', 'id' => '');
	    $term = $this->db->get_where('coop_loan_name' , array('loan_name_id' => $_GET['id']))->row_array();
	    if(!empty($term)){
            $data = array('status' => 'success', 'ref_id' => (int)$term['loan_type_id'], 'id' => (int)$term['loan_name_id']);
        }
	    echo json_encode($data);
        exit;
    }

    public function rollback_loan(){
        $this->db->trans_start();

        $loan_id = $this->uri->segment(3);
        if($loan_id=="") die();

        $token = $this->uri->segment(4);
        if($token=="") die();
        $authen_token = sha1(md5($loan_id));
        if($authen_token != $token) die();

        $this->db->limit(1);
        $loan = $this->db->get_where("coop_loan", array(
            "id" => $loan_id,
            "loan_status != " => "4"
        ))->row_array();
        if(empty($loan)) die();

        //คืนค่า coop_loan
        $this->db->set("loan_status", "0");
        $this->db->set("contract_number", "");
        $this->db->set("approve_date", "");
        $this->db->set("deduct_receipt_id", "");
        $this->db->set("lastupdate_by", $_SESSION['USER_ID']);
        $this->db->set("lastupdate_datetime", date("Y-m-d h:i:s"));
        //$this->db->set("updatetimestamp", date("Y-m-d h:i:s"));

        $contract_prefix = substr($loan['contract_number'], 0, 4);
        $contract_number = (int)substr($loan['contract_number'], strlen($loan['contract_number'])-6, 6);
        $loan_type = $loan['loan_type'];

        $this->db->where("id", $loan_id);
        $this->db->update("coop_loan");

        //ลบเลขรันสัญญาเงินกู้
        $this->db->where(array('contract_year' => $contract_prefix, 'loan_type' => $loan_type, 'run_contract_number' => $contract_number) );
        $this->db->delete("coop_loan_contract_number");

        $this->db->where("loan_id", $loan_id);
        $this->db->delete("coop_loan_transfer");

        $this->db->where("loan_id", $loan_id);
        $this->db->delete("coop_loan_transaction");

        if($loan['deduct_receipt_id']!=""){
            $this->load->model("Receipt_model");
            $this->Receipt_model->cancel_receipt($loan['deduct_receipt_id']);
            $this->db->trans_complete();
        }

        $this->center_function->toast("บันทึกข้อมูลเรียบร้อยแล้ว");
        echo "<script> document.location.href='".base_url(PROJECTPATH.'/loan/loan_approve')."' </script>";
        exit;
    }

    public function check_loan_before_transfer(){
        header('Content-type:application/json; charset=utf8;');
        $id = $this->input->get('id');
        $loan = $this->db->get_where('coop_loan', array(
            'id' => $id
        ))->row_array();
        $result = array('status' => 'failure', 'status_code' => 500);
        if(sizeof($loan)){
            if($loan['loan_status'] == '1'){
                $result = array('status' => 'success', 'status_code' => 200);
            }
        }else{
            $result = array('status' => 'failure', 'status_code' => 404);
        }
        echo json_encode($result);
        exit;

    }

    public function checkYear($year_target){
		$checkYear = date("Y", strtotime($year_target-543));
		$targetYear = date("Y", strtotime($year_target));
		return $checkYear+543 >= 3000 ? $targetYear : $targetYear+543;
	}

    public function loan_contract(){

		$approve_date = $this->input->get('date_approve');
		$id = $this->input->get('loan_id');
		$_approve_date = explode("/",$approve_date);
		$year = $_approve_date[2];

		$loan = $this->db->get_where("coop_loan", "id='{$id}'", 1)->row();

		$deduct = $this->db->get_where('coop_loan_deduct', "loan_id='{$id}'")->result_array();
		$summary_deduct = 0;
		if(sizeof($deduct)){
			$summary_deduct  = array_sum(array_column($deduct,'loan_deduct_amount'));
		}

		$total = round($loan->loan_amount - $summary_deduct, 0);
		$estimate_receive_money = $this->db->get_where('coop_loan_deduct_profile', "loan_id='{$id}'")->row()->estimate_receive_money;

		$data['year'] = $year;
		$data['transfer_total'] = $total;
		$data['estimate_receive_money'] = $estimate_receive_money;
		$data['contract'] = $this->loan_libraries->get_pre_contract_number($year, $loan->loan_type);
		header('Content-type: application/json');
		echo json_encode( $data);
		exit;
	}

	public function loan_transaction_edit(){
		$member_id 	= $this->uri->segment(3);
		$loan_id 	= $this->uri->segment(4);

		$this->db->join("coop_prename", "coop_prename.prename_id = coop_mem_apply.prename_id", "left");
		$member = $this->db->get_where("coop_mem_apply", array(
			"member_id" => $member_id
		))->row_array();
		$loan = $this->db->get_where("coop_loan", array(
			"id" => $loan_id,
			"member_id" => $member_id
		))->row_array();
		$this->db->order_by("transaction_datetime asc, loan_transaction_id asc");
		$loan_transaction = $this->db->get_where("coop_loan_transaction", array(
			"loan_id" => $loan_id
		))->result_array();
		foreach($loan_transaction as $key => $value){
		    if($_GET['dev'] == 'dev'){
                echo '<pre>';print_r($value);echo '</pre>';
            }
			// $this->db->select("SUM(principal_payment) as principal_payment");
			$this->db->select("principal_payment");
			$principal_payment = $this->db->get_where("coop_finance_transaction", array(
				"loan_id" => $loan_id,
				"receipt_id" => $value['receipt_id'],
				"payment_date" => date("Y-m-d", strtotime($value['transaction_datetime'])),
				"loan_amount_balance" => $value['loan_amount_balance'],
				"principal_payment >" => 0
			))->row_array();
            if($_GET['dev'] == 'dev') {
                echo '<pre>';
                print_r($principal_payment);
                echo '</pre>';
                echo $this->db->last_query();
            }

			$this->db->select("interest");
			// $this->db->select("SUM(interest) as interest");
			$interest = $this->db->get_where("coop_finance_transaction", array(
				"loan_id" => $loan_id,
				"receipt_id" => $value['receipt_id'],
				"payment_date" => date("Y-m-d", strtotime($value['transaction_datetime'])),
				"interest >" => 0
			))->row_array();

			$loan_transaction[$key]['principal'] 		= $principal_payment['principal_payment'];
			$loan_transaction[$key]['interest'] 		= $interest['interest'];
		}

		$arr_data 							= array();
		$arr_data['member'] 				= $member;
		$arr_data['loan'] 					= $loan;
		$arr_data['loan_transaction'] 		= $loan_transaction;
        if($_GET['dev'] == 'dev') {
            exit;
            echo '<pre>';
            print_r($arr_data);
            exit;
        }
		$this->libraries->template('loan/loan_transaction_edit',$arr_data);
	}

	public function update_loan_transaction_this_row(){
		$loan_id = $_POST['loan_id'];
		$loan_transaction_id = $_POST['loan_transaction_id'];
		$token = $_POST['token'];
		$balance = $_POST['balance'];

		if($token=="" || $loan_transaction_id=="" || $loan_id=="") die("empty");
		$authen_token = sha1(md5($loan_transaction_id));
		if($authen_token != $token) die("auth");

		$loan_transaction = $this->db->get_where("coop_loan_transaction", array(
			"loan_id" => $loan_id,
			"loan_transaction_id" => $loan_transaction_id
		))->row_array();

		$this->db->set("loan_amount_balance", $balance);
		$this->db->where("loan_transaction_id", $loan_transaction_id);
		$this->db->where("loan_id", $loan_id);
		$this->db->limit("1");
		$this->db->update("coop_loan_transaction");
		$affected_rows = $this->db->affected_rows();

		if($loan_transaction['receipt_id'] != ""){
			$this->db->set("loan_amount_balance", $balance);
			$this->db->where("receipt_id", $loan_transaction['receipt_id']);
			$this->db->where("loan_id", $loan_id);
			$this->db->where('loan_amount_balance', $loan_transaction['loan_amount_balance']);
			$this->db->update("coop_finance_transaction");
		}

		$lastest_transaction = $this->db->select("t1.loan_amount_balance")
										->from("coop_loan_transaction t1")
										->join("coop_loan t2", "t1.loan_id = t2.id", "INNER")
										->where("t1.loan_id = '".$loan_id."'")
										->order_by("transaction_datetime desc, loan_transaction_id desc")
										->get()->row_array();
		if(!empty($lastest_transaction)){
			$this->db->set("loan_amount_balance", $lastest_transaction['loan_amount_balance']);
			$this->db->set("date_last_interest", $lastest_transaction['transaction_datetime']);
			if($lastest_transaction['loan_amount_balance'] <= 0){
				$this->db->set("loan_status", '4');
			}
			$this->db->where("id", $loan_id );
			$this->db->update("coop_loan");
		}

		$data = array(
			"affected_rows" => $affected_rows
		);
		echo json_encode($data);
	}

	public function update_loan_transaction_after_this_row(){
		$loan_id = $_POST['loan_id'];
		$loan_transaction_id = $_POST['loan_transaction_id'];
		$token = $_POST['token'];
		$balance = $_POST['balance'];

		if($token=="" || $loan_transaction_id=="" || $loan_id=="") die("empty");
		$authen_token = sha1(md5($loan_transaction_id));
		if($authen_token != $token) die("auth");

		$this->db->order_by("transaction_datetime asc, loan_transaction_id asc");
		$loan_transaction = $this->db->get_where("coop_loan_transaction", array(
			"loan_id" => $loan_id
		))->result_array();
		$skip = false;
		$first_update = true;
		$affected_rows = 0;
		foreach($loan_transaction as $key => $value){
			if($loan_transaction_id == $value['loan_transaction_id'] && $skip == false){
				$skip = true;
			}

			if($skip){
				if($first_update==false){
					$this->db->select("SUM(principal_payment) as principal_payment");
					$principal_payment = $this->db->get_where("coop_finance_transaction", array(
						"loan_id" => $loan_id,
						"receipt_id" => $value['receipt_id'],
						"principal_payment >" => 0
					))->row_array();
					$balance -= $principal_payment['principal_payment'];
				}else{
					$first_update = false;
				}
				
				//start update
				$this->db->set("loan_amount_balance", $balance);
				$this->db->where("loan_transaction_id", $value['loan_transaction_id']);
				$this->db->where("loan_id", $loan_id);
				$this->db->limit("1");
				$this->db->update("coop_loan_transaction");
				$affected_rows += $this->db->affected_rows();

				if($value['receipt_id'] != ""){
					$this->db->set("loan_amount_balance", $balance);
					$this->db->where("receipt_id", $value['receipt_id']);
					$this->db->where("loan_id", $loan_id);
					$this->db->update("coop_finance_transaction");
				}
			}
		}

		$lastest_transaction = $this->db->select("t1.loan_amount_balance")
										->from("coop_loan_transaction t1")
										->join("coop_loan t2", "t1.loan_id = t2.id", "INNER")
										->where("t1.loan_id = '".$loan_id."'")
										->order_by("transaction_datetime desc, loan_transaction_id desc")
										->get()->row_array();
		if(!empty($lastest_transaction)){
			$this->db->set("loan_amount_balance", $lastest_transaction['loan_amount_balance']);
			if($lastest_transaction['loan_amount_balance'] <= 0){
				$this->db->set("loan_status", '4');
			}
			$this->db->where("id", $loan_id );
			$this->db->update("coop_loan");
		}
		
		$data = array(
			"affected_rows" => $affected_rows
		);
		echo json_encode($data);
	}

	public function delete_trash_transaction(){

		$data = array('status' => 500, 'msg' => 'Do nothing...');
		$loan_id = $_POST['loan_id'];
		$loan_transaction_id = $_POST['loan_transaction_id'];
		$token = $_POST['token'];
		$balance = $_POST['balance'];


		if($token=="" || $loan_transaction_id=="" || $loan_id=="") die("empty");
		$authen_token = sha1(md5($loan_transaction_id));
		if($authen_token != $token) die("auth");

		$this->db->order_by("transaction_datetime asc, loan_transaction_id asc");
		$transaction = $this->db->get_where("coop_loan_transaction", array(
			"loan_id" => $loan_id,
			"loan_transaction_id" => $loan_transaction_id
		))->row_array();

		if(sizeof($transaction)) {
			$payment_date = date('Y-m-d', strtotime($transaction['transaction_datetime']));
			$this->db->trans_begin();
			$this->db->delete('coop_finance_transaction', "loan_id='{$transaction['loan_id']}' AND receipt_id='{$transaction['receipt_id']}' AND payment_date='{$payment_date}' AND loan_amount_balance ='{$transaction['loan_amount_balance']}'");
			$this->db->delete('coop_loan_transaction', "loan_transaction_id='{$loan_transaction_id}'");

			if ($this->db->trans_status() === false) {
				$this->db->trans_rollback();
				$data = ['status' => 400, 'msg' => 'Something wrong?'];
			} else {
				$this->db->trans_commit();
				$data = ['status' => 200, 'msg' => 'Success'];
			}
		}

		header('Content-type: application/json; Charset=utf-8');
		echo json_encode($data);

	}

	public function update_salary_present_in_report_detail(){
		$member_id = $this->input->post('member_id');
		$loan_id = $this->input->post('loan_id');
		if(isset($member_id) && isset($loan_id) && $member_id != "" && $loan_id != "") {
			$data = array();
			$member = $this->db->select("salary")->get_where('coop_mem_apply', "member_id='{$member_id}'")->row();
			$this->db->update('coop_loan_report_detail', array('salary' => $member->salary), "loan_id='{$loan_id}' and member_id='{$member_id}'");
			if($this->db->affected_rows()) {
				$data = array('code' => 200, 'status' => 'ok', 'msg' => 'success');
			}else{
				$data = array('code' => 200, 'status' => 'ok', 'msg' => 'No change');
			}
			header('Content-type: application/json; Charset=utf-8');
			echo json_encode($data);
			exit;
		}else{
			header('Content-type: application/json; Charset=utf-8');
			echo json_encode(array('code' => 500, 'status' => 'error', 'msg' => 'Empty member or loan'));
			exit;
		}
	}
}
