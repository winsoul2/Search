<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Debt_dismiss extends CI_Controller {
	function __construct() {
		parent::__construct();
		$this->month_arr = array('1'=>'มกราคม','2'=>'กุมภาพันธ์','3'=>'มีนาคม','4'=>'เมษายน','5'=>'พฤษภาคม','6'=>'มิถุนายน','7'=>'กรกฎาคม','8'=>'สิงหาคม','9'=>'กันยายน','10'=>'ตุลาคม','11'=>'พฤศจิกายน','12'=>'ธันวาคม');

    }

    public function index() {
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

				//Get Interest
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
						$rs_data = $this->db->get_where('coop_loan', array('id' => $value['id']))->row_array();
						$date1 = $rs_data['date_last_interest'];

						if(isset($_GET['date_fix']) && $_GET['date_fix'] != "") {
							$date2 = date("Y-m-d", strtotime($_GET['date_fix'] ));//วันที่คิดดอกเบี้ย now
						}else{
							$date2 = date("Y-m-d");//วันที่คิดดอกเบี้ย now
						}
						$interest_data = $this->loan_libraries->calc_interest_loan($loan_amount, $loan_id, $date1, $date2);
						$interest_data = $interest_data > 0 ? round($interest_data) : 0;
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
			foreach($rs_atm as $key_atm => $row_atm){
				$loan_principal += ($row_atm['total_amount'] - $row_atm['total_amount_balance']);

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
					$interest_data = $this->loan_libraries->cal_atm_interest_report_test($cal_atm_interest,"echo", array("month"=> date("m"), "year" => date("Y") ), false, true)['interest_month'];
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
            $this->db->where('check_debt = 1');
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

		$this->libraries->template('debt/debt_dismiss',$arr_data);
    }

    public function save_dismiss(){
		$data = $this->input->post();
        $this->member_relinquish->save_dismiss($data);
		echo "<script> document.location.href = '".PROJECTPATH."/Debt_dismiss?member_id=".$data['member_id']."' </script>";
		exit;
    }

	public function dismiss_approve(){
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
		$join_arr[$x]['condition'] = 'coop_mem_req_resign.resign_cause_id = coop_mem_resign_cause.resign_cause_id AND coop_mem_resign_cause.check_debt = 1';
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

		$this->libraries->template('debt/dismiss_approve',$arr_data);
	}
}
