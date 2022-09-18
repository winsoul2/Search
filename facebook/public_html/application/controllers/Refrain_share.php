<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Refrain_share extends CI_Controller {
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
		$arr_data['member_id'] = $member_id;
		
		$arr_data['count_share'] = 0;
		$arr_data['cal_share'] = 0;
		
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
			$arr_data['data'] = array();
			
			//ประเภทสมาชิก
			$this->db->select('mem_type_id, mem_type_name');
			$this->db->from('coop_mem_type');
			$rs_mem_type = $this->db->get()->result_array();
			$mem_type_list = array();
			foreach($rs_mem_type AS $key=>$row_mem_type){
				$mem_type_list[$row_mem_type['mem_type_id']] = $row_mem_type['mem_type_name'];
			}
			
			$arr_data['mem_type_list'] = $mem_type_list;
			
			//ส่งหุ้นแล้ว
			$this->db->select('*');
			$this->db->from('coop_mem_share');
			$this->db->where("member_id = '".$member_id."' AND share_status IN('1','2')");
			$this->db->order_by('share_date DESC');
			$this->db->limit(1);
			$row_prev_share = $this->db->get()->result_array();
			$row_prev_share = @$row_prev_share[0];
			$arr_data['cal_share'] = $row_prev_share['share_collect_value'];
			//echo $this->db->last_query();exit;			
			
			//ภาระหนี้รวม
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
			$sum_debt_balance = 0;
			if(!empty($rs_debt)){
				foreach($rs_debt as $key => $row_count_debt){
					if($row_count_debt['profile_id'] != '' ){
						$sum_debt_balance += $row_count_debt['pay_amount'];
					}
				}
			}
			$arr_data['sum_debt_balance'] = @$sum_debt_balance;
			
			//ภาระค้ำประกัน
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
			$sum_balance = 0;
			foreach($rs_guarantee as $key => $row_count_guarantee){
				@$sum_balance += $row_count_guarantee['loan_amount_balance'];
			}
			$arr_data['sum_guarantee_balance'] = @$sum_balance;
			
			//สถานะ ปกติ/งดถาวร/งดชั่วคราว
			$this->db->select('*');
			$this->db->from('coop_refrain_share');
			$this->db->where("member_id = '".$member_id."'");
			$this->db->order_by('refrain_id DESC');
			$this->db->limit(1);			
			$rs_refrain = $this->db->get()->result_array();
			$type_refrain = @$rs_refrain[0]['type_refrain'];
			if($type_refrain == ''){
				$type_refrain = 0;
			}
			
			//echo '<pre>'; print_r($row_refrain); echo '</pre>'; exit;
			$type_refrain_list = array('0'=>'ปกติ','1'=>'งดถาวร', '2'=>'งดชั่วคราว');
			$status_refrain = $type_refrain_list[$type_refrain];
			$arr_data['type_refrain'] = $type_refrain;
			$arr_data['status_refrain'] = $status_refrain;
			$arr_data['type_refrain_list'] = @$type_refrain_list;
			
			
			$x=0;
			$join_arr = array();
			$join_arr[$x]['table'] = 'coop_user';
			$join_arr[$x]['condition'] = 'coop_refrain_share.admin_id = coop_user.user_id';
			$join_arr[$x]['type'] = 'left';
			
			$this->paginater_all->type(DB_TYPE);
			$this->paginater_all->select('*');
			$this->paginater_all->main_table('coop_refrain_share');
			$this->paginater_all->where("coop_refrain_share.member_id = '".$member_id."'");
			$this->paginater_all->page_now(@$_GET["page"]);
			$this->paginater_all->per_page(20);
			$this->paginater_all->page_link_limit(20);
			$this->paginater_all->order_by('refrain_id DESC');
			$this->paginater_all->join_arr($join_arr);
			$row = $this->paginater_all->paginater_process();
			//echo"<pre>";print_r($row);exit;
			$paging = $this->pagination_center->paginating($row['page'], $row['num_rows'], $row['per_page'], $row['page_link_limit']);//$page_now = 1, $row_total = 1, $per_page = 20, $page_limit = 20
			$i = $row['page_start'];


			$arr_data['num_rows'] = $row['num_rows'];
			$arr_data['paging'] = $paging;
			$arr_data['data'] = $row['data'];
			$arr_data['i'] = $i;
			
		}else{
			$arr_data['data'] = array();
			$arr_data['paging'] = '';
			$arr_data['share_per_month'] = 0;
		}

		$this->db->select('*');
		$this->db->from('coop_share_setting');
		$this->db->order_by('setting_id DESC');
		$row = $this->db->get()->result_array();
		$arr_data['share_value'] = $row[0]['setting_value'];
		
		$month_arr = $this->center_function->month_arr();
		$arr_data['month_arr'] = $month_arr;
		
		$this->libraries->template('refrain_share/index',$arr_data);
	}
	
	function save_refrain_permanent(){
		$member_id = @$_POST['member_id'];
		//ตั้งค่าการงดหุ้น
		$this->db->select('*');
		$this->db->from('coop_refrain_share_setting');
		$this->db->limit(1);
		$rs_refrain = $this->db->get()->result_array();
		$min_share_month = @$rs_refrain[0]['min_share_month']; //ส่งค่าหุ้นแล้วไม่น้อยกว่า x เดือนและไม่มีหนี้ ไม่ติดค้ำประกัน
		$max_refrain = @$rs_refrain[0]['max_refrain']; //ไม่เกิน x ครั้งต่อปี
		
		//จำนวนงวดที่ส่งหุ้น
		$this->db->select('*');
		$this->db->from('coop_mem_share');
		$this->db->where("member_id = '".$member_id."' AND share_type = 'SPM' ");
		$this->db->order_by('share_period DESC');
		$this->db->limit(1);
		$rs_share = $this->db->get()->result_array();
		$row_share = @$rs_share[0];
		$share_period = $row_share['share_period'];
		//echo $this->db->last_query();exit;			
		
		//ภาระหนี้รวม
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
		$sum_debt_balance = 0;
		if(!empty($rs_debt)){
			foreach($rs_debt as $key => $row_count_debt){
				if($row_count_debt['profile_id'] != '' ){
					$sum_debt_balance += $row_count_debt['pay_amount'];
				}
			}
		}
		
		//ภาระค้ำประกัน
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
		$sum_guarantee_balance = 0;
		foreach($rs_guarantee as $key => $row_count_guarantee){
			@$sum_guarantee_balance += $row_count_guarantee['loan_amount_balance'];
		}
		
		//
		$this->db->select(array('t1.*'));
		$this->db->from('coop_mem_apply as t1');			
		$this->db->where("t1.member_id = '".$member_id."'");
		$rs = $this->db->get()->result_array();
		$row_member = @$rs[0];
			
		$this->db->select('*');
		$this->db->from('coop_change_share');
		$this->db->where("member_id = '".$member_id."' AND change_share_status IN('1','2')");
		$this->db->order_by('change_share_id DESC');
		$this->db->limit(1);
		$row = $this->db->get()->result_array();		
		if(!empty($row)){
			$share_per_month = $row[0]['change_value'];
		}else{
			$this->db->select(array('share_salary','salary_rule'));
			$this->db->from('coop_share_rule');
			$this->db->where("salary_rule <= '".$row_member['salary']."' AND mem_type_id='".$row_member['mem_type_id']."'");
			$this->db->order_by('salary_rule DESC');
			$this->db->limit(1);
			$row = $this->db->get()->result_array();
			
			$share_per_month = $row[0]['share_salary'];
		}
		
		$this->db->select('*');
		$this->db->from('coop_share_setting');
		$this->db->order_by('setting_id DESC');
		$row = $this->db->get()->result_array();
		$share_value = $row[0]['setting_value'];
		$total_amount = @$share_per_month*@$share_value;			
		//
		
		if($share_period < $min_share_month){
			echo 'no';
		}else if($sum_debt_balance > 0){
			echo 'no';
		}else if($sum_guarantee_balance > 0){
			echo 'no';		
		}else{
			$data_insert = array();			
			$data_insert['member_id'] = @$member_id;
			$data_insert['type_refrain'] = "1";
			$data_insert['admin_id'] = @$_SESSION['USER_ID'];
			$data_insert['updatetime'] = date('Y-m-d H:i:s');
			$data_insert['createdatetime'] = date('Y-m-d H:i:s');
			$data_insert['total_amount'] = @$total_amount;
			$this->db->insert('coop_refrain_share', $data_insert);
			echo 'ok';
		}
		exit;
	}
	
	function check_refrain_temporary(){
		$member_id = @$_POST['member_id'];
		$from_month_refrain = $_POST['from_month_refrain'];
		$from_year_refrain = $_POST['from_year_refrain'];
		$to_month_refrain = $_POST['to_month_refrain'];
		$to_year_refrain = $_POST['to_year_refrain'];

		$where = "";
		if($from_year_refrain == $to_year_refrain) {
			$where .= " AND (month_refrain between '".$from_month_refrain."' AND '".$to_month_refrain."') AND (year_refrain between '".$from_year_refrain."' AND '".$to_year_refrain."') ";
		} else {
			$where .= " AND (year_refrain between '".$from_year_refrain."' AND '".$to_year_refrain."')";
			$where .= " AND ((year_refrain = '".$from_year_refrain."' AND month_refrain >= '".$from_month_refrain."') OR (year_refrain > '".$from_year_refrain."'))";
			$where .= " AND ((year_refrain = '".$to_year_refrain."' AND month_refrain <= '".$to_month_refrain."') OR (year_refrain < '".$to_year_refrain."'))";
		}

		$this->db->select('*');
		$this->db->from('coop_refrain_share');
		$this->db->where("member_id = '".$member_id ."'".$where);
		$this->db->order_by('refrain_id DESC');			
		$rs_refrain_share = $this->db->get()->result_array();
		$count_refrain_share = 0;
		if(!empty($rs_refrain_share)){
			foreach($rs_refrain_share AS $key=>$value){
				$count_refrain_share++;
			}
		}

		if($count_refrain_share != 0){
			echo 'no';
		}else{	
			echo 'ok';
		}
		exit;
	}
	
	function save_refrain_temporary(){
		$member_id = @$_POST['member_id'];
		$from_month_refrain = $_POST['from_month_refrain'];
		$from_year_refrain = $_POST['from_year_refrain'];
		$to_month_refrain = $_POST['to_month_refrain'];
		$to_year_refrain = $_POST['to_year_refrain'];
		$can_refrain = true;
		//ตั้งค่าการงดหุ้น
		$this->db->select('*');
		$this->db->from('coop_refrain_share_setting');
		$this->db->limit(1);
		$rs_refrain = $this->db->get()->result_array();
		$max_refrain = @$rs_refrain[0]['max_refrain']; //ไม่เกิน x ครั้งต่อปี

		$this->db->select('*');
		$this->db->from('coop_refrain_share');
		$this->db->where("member_id = '".$member_id ."' AND year_refrain = '".$from_year_refrain."'");
		$this->db->group_by("createdatetime");
		$this->db->order_by('refrain_id DESC');			
		$rs_refrain_share = $this->db->get()->result_array();
		if(count($rs_refrain_share) >= $max_refrain) {
			$can_refrain = false;
		}

		$this->db->select('*');
		$this->db->from('coop_refrain_share');
		$this->db->where("member_id = '".$member_id ."' AND year_refrain = '".$to_year_refrain."'");
		$this->db->group_by("createdatetime");
		$this->db->order_by('refrain_id DESC');			
		$rs_refrain_share = $this->db->get()->result_array();
		if(count($rs_refrain_share) >= $max_refrain) {
			$can_refrain = false;
		}

		if(!$can_refrain) {
			echo 'no';
		} else{
			//
			$this->db->select(array('t1.*'));
			$this->db->from('coop_mem_apply as t1');			
			$this->db->where("t1.member_id = '".$member_id."'");
			$rs = $this->db->get()->result_array();
			$row_member = @$rs[0];

			$periods = array();
			if($to_year_refrain == $from_year_refrain) {
				$year = $from_year_refrain - 543;
				for($i = $from_month_refrain; $i <= $to_month_refrain; $i++) {
					$periods[$year][] = $i;
				}
			} else {
				$year = $from_year_refrain - 543;
				for($i = $from_month_refrain; $i <= 12; $i++) {
					$periods[$year][] = $i;
				}
				$year = $to_year_refrain - 543;
				for($i = 1; $i <= $to_month_refrain; $i++) {
					$periods[$year][] = $i;
				}
			}
			$process_date = date('Y-m-d H:i:s');

			foreach($periods as $year => $months) {
				foreach($months as $month) {
					$start_month = $year.'-'.sprintf('%02d', $month).'-01 00:00:00';
					$share_per_month = 0;
					$this->db->select('*');
					$this->db->from('coop_change_share');
					$this->db->where("member_id = '".$member_id."' AND change_share_status IN('1','2') AND active_date <= '".$start_month."'");
					$this->db->order_by('change_share_id DESC');
					$this->db->limit(1);
					$row = $this->db->get()->result_array();		
					if(!empty($row)){
						$share_per_month = $row[0]['change_value'];
					}else{
						$this->db->select(array('share_salary','salary_rule'));
						$this->db->from('coop_share_rule');
						$this->db->where("salary_rule <= '".$row_member['salary']."' AND mem_type_id='".$row_member['mem_type_id']."'");
						$this->db->order_by('salary_rule DESC');
						$this->db->limit(1);
						$row = $this->db->get()->result_array();
						
						$share_per_month = $row[0]['share_salary'];
					}

					$this->db->select('*');
					$this->db->from('coop_share_setting');
					$this->db->order_by('setting_id DESC');
					$row = $this->db->get()->result_array();
					$share_value = $row[0]['setting_value'];
					$total_amount = @$share_per_month*@$share_value;			
					//

					$data_insert = array();			
					$data_insert['member_id'] = @$member_id;
					$data_insert['month_refrain'] = $month;
					$data_insert['year_refrain'] = $year+543;
					$data_insert['type_refrain'] = "2";
					$data_insert['admin_id'] = @$_SESSION['USER_ID'];
					$data_insert['updatetime'] = $process_date;
					$data_insert['createdatetime'] = $process_date;
					$data_insert['total_amount'] = @$total_amount;
					$this->db->insert('coop_refrain_share', $data_insert);
				}
			}
			echo 'ok';
		}
		exit;
	}
	function save_refrain_confirm_temporary(){
		$member_id = @$_POST['member_id'];
		$month_refrain = @$_POST['month_refrain'];
		$year_refrain = @$_POST['year_refrain'];

		//
		$this->db->select(array('t1.*'));
		$this->db->from('coop_mem_apply as t1');			
		$this->db->where("t1.member_id = '".$member_id."'");
		$rs = $this->db->get()->result_array();
		$row_member = @$rs[0];
			
		$this->db->select('*');
		$this->db->from('coop_change_share');
		$this->db->where("member_id = '".$member_id."' AND change_share_status IN('1','2')");
		$this->db->order_by('change_share_id DESC');
		$this->db->limit(1);
		$row = $this->db->get()->result_array();		
		if(!empty($row)){
			$share_per_month = $row[0]['change_value'];
		}else{
			$this->db->select(array('share_salary','salary_rule'));
			$this->db->from('coop_share_rule');
			$this->db->where("salary_rule <= '".$row_member['salary']."' AND mem_type_id='".$row_member['mem_type_id']."'");
			$this->db->order_by('salary_rule DESC');
			$this->db->limit(1);
			$row = $this->db->get()->result_array();
			
			$share_per_month = $row[0]['share_salary'];
		}
		
		$this->db->select('*');
		$this->db->from('coop_share_setting');
		$this->db->order_by('setting_id DESC');
		$row = $this->db->get()->result_array();
		$share_value = $row[0]['setting_value'];
		$total_amount = @$share_per_month*@$share_value;			
		//
		$data_insert = array();			
		$data_insert['member_id'] = @$member_id;
		$data_insert['month_refrain'] = @$month_refrain;
		$data_insert['year_refrain'] = @$year_refrain;
		$data_insert['type_refrain'] = "2";
		$data_insert['admin_id'] = @$_SESSION['USER_ID'];
		$data_insert['updatetime'] = date('Y-m-d H:i:s');
		$data_insert['createdatetime'] = date('Y-m-d H:i:s');
		$data_insert['total_amount'] = @$total_amount;
		$this->db->insert('coop_refrain_share', $data_insert);
		echo "<script>document.location.href = '".base_url(PROJECTPATH.'/refrain_share?member_id='.$member_id)."'</script>";
		exit;		
	}
	
	function get_refrain(){
		$member_id = @$_POST['member_id'];
		$x=0;
		$join_arr = array();
		$join_arr[$x]['table'] = 'coop_user';
		$join_arr[$x]['condition'] = 'coop_refrain_share.admin_id = coop_user.user_id';
		$join_arr[$x]['type'] = 'left';
		
		$this->paginater_all->type(DB_TYPE);
		$this->paginater_all->select('*');
		$this->paginater_all->main_table('coop_refrain_share');
		$this->paginater_all->where("coop_refrain_share.member_id = '".$member_id."'");
		$this->paginater_all->page_now(@$_GET["page"]);
		$this->paginater_all->per_page(20);
		$this->paginater_all->page_link_limit(20);
		$this->paginater_all->order_by('refrain_id DESC');
		$this->paginater_all->join_arr($join_arr);
		$row = $this->paginater_all->paginater_process();
		//echo"<pre>";print_r($row);exit;
		$paging = $this->pagination_center->paginating($row['page'], $row['num_rows'], $row['per_page'], $row['page_link_limit']);//$page_now = 1, $row_total = 1, $per_page = 20, $page_limit = 20
		$i = $row['page_start'];


		$arr_data['num_rows'] = $row['num_rows'];
		$arr_data['paging'] = $paging;
		$arr_data['data'] = $row['data'];
		$arr_data['i'] = $i;
		
		$month_arr = $this->center_function->month_arr();
		$arr_data['month_arr'] = $month_arr;
		
		$type_refrain_list = array('0'=>'ปกติ','1'=>'งดถาวร', '2'=>'งดชั่วคราว');
		$arr_data['type_refrain_list'] = @$type_refrain_list;
		
		$this->load->view('refrain_share/get_refrain',$arr_data);
	}
	
	function del_coop_refrain_share(){	
		$id = @$_POST['id'];
		$this->db->where("refrain_id", $id );
		$this->db->delete("coop_refrain_share");
		$this->center_function->toast("ลบเรียบร้อยแล้ว");
		echo true;
		
	}
	
	public function report_refrain() {
		$arr_data = array();
		$arr_data['month_arr'] = $this->center_function->month_arr();
		$this->libraries->template('refrain_share/report_refrain',$arr_data);
	}
	
	public function check_report_refrain_preview() {
		if($_POST['start_date']){
			$start_date_arr = explode('/',$_POST['start_date']);
			$start_day = $start_date_arr[0];
			$start_month = $start_date_arr[1];
			$start_year = $start_date_arr[2];
			$start_year -= 543;
			$start_date = $start_year.'-'.$start_month.'-'.$start_day;
		}
		if($_POST['end_date']){
			$end_date_arr = explode('/',$_POST['end_date']);
			$end_day = $end_date_arr[0];
			$end_month = $end_date_arr[1];
			$end_year = $end_date_arr[2];
			$end_year -= 543;
			$end_date = $end_year.'-'.$end_month.'-'.$end_day;
		}
		$where = "";
		if(!empty($_POST['start_date']) && empty($_POST['end_date'])) {
			$where = " AND t1.createdatetime BETWEEN '".$start_date." 00:00:00.000' AND '".$start_date." 23:59:59.000'";
		}else if(!empty($_POST['start_date']) && !empty($_POST['end_date'])) {
			$where = " AND t1.createdatetime BETWEEN '".$start_date." 00:00:00.000' AND '".$end_date." 23:59:59.000'";
		}

		$members = $this->get_data_report_refrain($_POST);

		if(!empty($members)){
			echo "success";
		}else{
			echo "";
		}	
	}
	
	public function report_refrain_preview() {
		$arr_data = array();

		$arr_data['month_arr'] = $this->center_function->month_arr();
		$arr_data['month_short_arr'] = $this->center_function->month_short_arr();
		$arr_data['type_refrain_list'] = array('0'=>'ปกติ','1'=>'งดถาวร', '2'=>'งดชั่วคราว');

		$data_report = $this->get_data_report_refrain($_GET);
		//echo '<pre>'; print_r($data_report); echo '</pre>'; exit;
		$datas = array();
		$page = 0;
		$first_page_size = 16;
		$page_size = 24;
		foreach($data_report as $index => $data) {
			if($index < $first_page_size) {
				$page = 1;
			} else {
				$page = ceil((($index + 1)-$first_page_size) / $page_size) + 1;
			}
			$datas[$page][] = $data;
		}
		$arr_data["datas"] = $datas;
		$arr_data["page_all"] = $page;
		
		$start_date = $this->center_function->ConvertToSQLDate($_GET['start_date']);
		$end_date = $this->center_function->ConvertToSQLDate($_GET['end_date']);
		$text_date = " ประจำวันที่ ".$this->center_function->ConvertToThaiDate($start_date);
		$text_date .= (@$_GET['start_date'] == @$_GET['end_date'])?"":"  ถึง  ".$this->center_function->ConvertToThaiDate($end_date);
		$arr_data["text_date"] = $text_date;
		
		if(@$_GET['download']=="excel"){
			$this->load->view('refrain_share/report_refrain_preview',$arr_data);
		}else{	
			$this->preview_libraries->template_preview('refrain_share/report_refrain_preview',$arr_data);
		}
	}
	
	public function get_data_report_refrain($data) {
		$results = array();
		if($data['start_date']){
			$start_date_arr = explode('/',$data['start_date']);
			$start_day = $start_date_arr[0];
			$start_month = $start_date_arr[1];
			$start_year = $start_date_arr[2];
			$start_year -= 543;
			$start_date = $start_year.'-'.$start_month.'-'.$start_day;
		}
		if($data['end_date']){
			$end_date_arr = explode('/',$data['end_date']);
			$end_day = $end_date_arr[0];
			$end_month = $end_date_arr[1];
			$end_year = $end_date_arr[2];
			$end_year -= 543;
			$end_date = $end_year.'-'.$end_month.'-'.$end_day;
		}
		$where = "1=1";
		if(!empty($data['start_date']) && empty($data['end_date'])) {
			$where = " CONCAT((t1.year_refrain-543),'-',RIGHT(CONCAT('00', t1.month_refrain), 2),'-01') BETWEEN '".$start_date."' AND '".$start_date."'";
		}else if(!empty($data['start_date']) && !empty($data['end_date'])) {
			$where = " CONCAT((t1.year_refrain-543),'-',RIGHT(CONCAT('00', t1.month_refrain), 2),'-01') BETWEEN '".$start_date."' AND '".$end_date."'";
		}
	
		$row = $this->db->select("t1.*,t2.user_name,
								CONCAT(t4.prename_full, t3.firstname_th,'  ',	 t3.lastname_th) AS full_name,
								CONCAT((t1.year_refrain-543),'-',RIGHT(CONCAT('00', t1.month_refrain), 2),'-01') AS date_refrain")
								->from("coop_refrain_share AS t1")
								->join("coop_user AS t2","t1.admin_id = t2.user_id","left")
								->join("coop_mem_apply AS t3","t1.member_id = t3.member_id","left")
								->join("coop_prename AS t4","t3.prename_id = t4.prename_id","left")
								->where("{$where}")
								->order_by("t1.refrain_id ASC")
								->get()->result_array();
		//echo $this->db->last_query();						
		if(!empty($row)){
			$results = $row;
		}
		
		return $results;
	}
	
}
