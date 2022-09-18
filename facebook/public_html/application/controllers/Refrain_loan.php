<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Refrain_loan extends CI_Controller {
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
			$join_arr[$x]['condition'] = 'coop_refrain_loan.admin_id = coop_user.user_id';
			$join_arr[$x]['type'] = 'left';
			
			$this->paginater_all->type(DB_TYPE);
			$this->paginater_all->select('*');
			$this->paginater_all->main_table('coop_refrain_loan');
			$this->paginater_all->where("coop_refrain_loan.member_id = '".$member_id."' AND coop_refrain_loan.status != 2");
			$this->paginater_all->page_now(@$_GET["page"]);
			$this->paginater_all->per_page(20);
			$this->paginater_all->page_link_limit(20);
			$this->paginater_all->order_by('refrain_loan_id DESC');
			$this->paginater_all->join_arr($join_arr);
			$row = $this->paginater_all->paginater_process();
			//echo"<pre>";print_r($row);exit;
			$paging = $this->pagination_center->paginating($row['page'], $row['num_rows'], $row['per_page'], $row['page_link_limit']);//$page_now = 1, $row_total = 1, $per_page = 20, $page_limit = 20
			$i = $row['page_start'];


			$arr_data['num_rows'] = $row['num_rows'];
			$arr_data['paging'] = $paging;
			$arr_data['data'] = $row['data'];
			$arr_data['i'] = $i;
			
			//ข้อมูลเงินกู้สามัญ
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
			$this->db->where("t1.member_id = '".$member_id."' AND loan_status = '1' AND t5.loan_type_code IN('normal','emergent','special')");
			$this->db->order_by("t1.id DESC");
			$rs_loan = $this->db->get()->result_array();
			$arr_data['rs_loan'] = $rs_loan;
			if(@$_GET['dev']=='dev'){
				echo $this->db->last_query(); exit;
			}
			//echo '<pre>'; print_r($rs_loan); echo '</pre>';
			//exit;
			
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
		
		$month_arr = array('1'=>'มกราคม','2'=>'กุมภาพันธ์','3'=>'มีนาคม','4'=>'เมษายน','5'=>'พฤษภาคม','6'=>'มิถุนายน','7'=>'กรกฎาคม','8'=>'สิงหาคม','9'=>'กันยายน','10'=>'ตุลาคม','11'=>'พฤศจิกายน','12'=>'ธันวาคม');
		$arr_data['month_arr'] = $month_arr;
		
		$this->libraries->template('refrain_loan/index',$arr_data);
	}
	
	function save_refrain_loan(){
		// echo "<pre>"; print_r($_POST); exit;
		$member_id = @$_POST['member_id'];
		$data_insert = array();
		foreach($_POST as $key => $value){
			if($key != 'refrain_loan_id' && !($_POST["period_type"] == 2 && ($key == "year_end" || $key == "month_end"))){
				if($key == 'id'){
					$id = $value;
				}else{
					$data_insert[$key] = $value;	
				}
			}
		}			
		
		$data_insert['updatetime'] = date('Y-m-d H:i:s');
		if(@$_POST['refrain_loan_id'] != ''){
			$this->db->where('refrain_loan_id', @$_POST['refrain_loan_id']);
			$this->db->update('coop_refrain_loan', $data_insert);
		}else{
			$data_insert['status'] = 1;
			$data_insert['admin_id'] = @$_SESSION['USER_ID'];
			$data_insert['createdatetime'] = date('Y-m-d H:i:s');
			$this->db->insert('coop_refrain_loan', $data_insert);
		}	
		echo "<script> document.location.href='".base_url(PROJECTPATH.'/refrain_loan?member_id='.@$member_id)."' </script>";
		exit;
	}
	
	
	function get_loan_amount_balance(){
		$loan_id = @$_POST['loan_id'];
		$this->db->select(array(
			't1.deduct_status',
			't1.createdatetime',
			't1.contract_number',
			't1.petition_number',
			't1.loan_amount',
			't1.loan_amount_balance',
			't1.guarantee_for_id',
			't1.id',
			't1.member_id'
		));
		$this->db->from('coop_loan as t1');
		$this->db->where("t1.id = '".$loan_id."'");
		$this->db->order_by("t1.id DESC");
		$this->db->limit(1);
		$rs_loan = $this->db->get()->result_array();
		$row_loan = @$rs_loan[0];
		$row_loan['loan_amount_balance'] = @number_format(@$row_loan['loan_amount_balance'],2);
		
		echo json_encode($row_loan);
		exit;
	}
	
	function check_refrain_loan(){
		$month_arr = array('1'=>'มกราคม','2'=>'กุมภาพันธ์','3'=>'มีนาคม','4'=>'เมษายน','5'=>'พฤษภาคม','6'=>'มิถุนายน','7'=>'กรกฎาคม','8'=>'สิงหาคม','9'=>'กันยายน','10'=>'ตุลาคม','11'=>'พฤศจิกายน','12'=>'ธันวาคม');
		
		$loan_id = @$_POST['loan_id'];
		$year_start = @$_POST['year_start'];
		$month_start = @$_POST['month_start'];
		$year_end = @$_POST['year_end'];
		$month_end = @$_POST['month_end'];
		$refrain_loan_id = @$_POST['refrain_loan_id'];
		
		$this->db->select(array('*'));
		$this->db->from('coop_refrain_loan_setting');
		$this->db->limit(1);
		$rs = $this->db->get()->result_array();
		$row = @$rs[0];
		$max_period_month = $row['max_period_month']; //สัญญางดได้ไม่เกิน x เดือน
		$max_time_month = $row['max_time_month']; //ครั้งละไม่เกิน x เดือน
		$max_year_time = $row['max_year_time']; //ปีละไม่เกิน x ครั้ง
		
		//ครั้งละไม่เกิน x เดือน
		$start = ($year_start-543)."-".$month_start."-01";
		$end = ($year_end-543)."-".$month_end."-01";		
		$count_num_month = $this->diff_month_interval($start,$end);	
		
		if(@$refrain_loan_id != ''){
			$where_refrain = "AND refrain_loan_id NOT IN ('".@$refrain_loan_id."')";
		}else{
			$where_refrain = "";
		}
		
		//สัญญางดได้ไม่เกิน x เดือน
		$this->db->select(array('*'));
		$this->db->from('coop_refrain_loan');
		$this->db->where("loan_id = '".$loan_id."' {$where_refrain}");
		$rs_period = $this->db->get()->result_array();
		//echo $this->db->last_query();exit;
		$num_period = 0;
		$diff_month = 0;
		$num_diff = 0;
		$arr_month_refrain = array();
		$n = 0;
		if(!empty($rs_period)){
			foreach($rs_period AS $key=>$row_period){
				//echo '<pre>'; print_r($row_period); echo '</pre>';				
				$start = ($row_period['year_start']-543)."-".$row_period['month_start']."-01";
				$end = ($row_period['year_end']-543)."-".$row_period['month_end']."-01";		
				$num_period += $this->diff_month_interval($start,$end);
				
				//หาเดือน ปีที่บันทึกแล้ว
				$diff_month = $this->diff_month_interval($start,$end);
				$num_diff = $row_period['month_start']+$diff_month;
				for($mm = $row_period['month_start'];$mm<$num_diff;$mm++){
					if($mm > 12){
						$n++;
						$refrain_year = $row_period['year_start']+1;
						$refrain_month = $n;
					}else{
						$refrain_year = $row_period['year_start'];
						$refrain_month = $mm;
					}						
					$arr_month_refrain[$refrain_year][$refrain_month] = $refrain_month;
				}
			}
		}
		
		//เช็คไม่ให้เลือกเดือน ปีซ้ำ 
		$chek_month_start = 0;
		$chek_month_end = 0;
		foreach($arr_month_refrain AS $key=>$value){
			foreach($value AS $key2=>$value2){
				if($month_start == $key2 AND $year_start == $key){
					$chek_month_start = 1;
				}
				
				if($month_end == $key2 AND $year_end == $key){
					$chek_month_end = 1;
				}
			}
		}
			
		$count_num_period = @$num_period+@$count_num_month;
		//ปีละไม่เกิน x ครั้ง
		$this->db->select(array('COUNT(year_start) AS count_num_year'));
		$this->db->from('coop_refrain_loan');
		$this->db->where("loan_id = '".$loan_id."' AND year_start = '".$year_start."' {$where_refrain}");
		$this->db->limit(1);
		$rs_year = $this->db->get()->result_array();
		$row_year = @$rs_year[0];
		$count_num_year = @$row_year['count_num_year'];
		
		$start_refrain = ($year_start-543)."-".sprintf("%02d",$month_start)."-01";
		$end_refrain = ($year_end-543)."-".sprintf("%02d",$month_end)."-01";	
		if($start_refrain > $end_refrain){
			//เช็ควันเริ่ม ต้องมากกว่า วันที่สิ้นสุด
			$result = array("action" => "break", "msg" => "เดือนที่เริ่มต้นต้องน้อยกว่าเดือนสุดท้าย");
		}else if($chek_month_start == 1 && $chek_month_end == 1){			
			$text_alert = "";
			$mmyy_start = $month_start.$year_start;
			$mmyy_end = $month_end.$year_end;
			$msg = "";
			if($mmyy_start != $mmyy_end){
				$msg = "ไม่สามารถเลือกเดือน ".$month_arr[$month_start]." ".$year_start." \r\nและเดือน ".$month_arr[$month_end]." ".$year_end." ได้ \r\nเนื่องจากมีในระบบแล้ว";
			}else{
				$msg = "ไม่สามารถเลือกเดือน ".$month_arr[$month_start]." ".$year_start."  ได้ \r\nเนื่องจากมีในระบบแล้ว";
			}
			$result = array("action" => "break", "msg" => $msg);
		}else if($chek_month_start == 1 && $chek_month_end == 0){
			$msg = "ไม่สามารถเลือกเดือน ".$month_arr[$month_start]." ".$year_start." ได้ \r\nเนื่องจากมีในระบบแล้ว";
			$result = array("action" => "break", "msg" => $msg);
		}else if($chek_month_start == 0 && $chek_month_end == 1){
			$msg = "ไม่สามารถเลือกเดือน ".$month_arr[$month_end]." ".$year_end." ได้ \r\nเนื่องจากมีในระบบแล้ว";
			$result = array("action" => "break", "msg" => $msg);
		}else if(@$count_num_month > @$max_time_month){
			$msg = "สามารถงดต้นได้ครั้งละไม่เกิน ".$max_time_month." เดือน \r\n กดยืนยันเพื่อทำต่อ";
			$result = array("action" => "warning", "msg" => $msg);
		}else if(@$count_num_period > @$max_period_month){
			$msg = "สัญญางดได้ไม่เกิน ".$max_period_month." เดือน";
			$result = array("action" => "break", "msg" => $msg);
		}else if(@$count_num_year > @$max_year_time){
			$msg =  "ปีละไม่เกิน ".$max_year_time." ครั้ง";
			$result = array("action" => "break", "msg" => $msg);
		}else{
			$msg = "ok";
			$result = array("action" => "ok", "msg" => $msg);
		}		
		echo json_encode($result, true);
		exit;
	}
	
	function del_coop_refrain_loan(){	
		$data_insert = array();
		$data_insert["status"] = 2;
		$data_insert["updatetime"] = date('Y-m-d H:i:s');
		$this->db->where('refrain_loan_id', $_POST['id']);
		$this->db->update('coop_refrain_loan', $data_insert);
		$this->center_function->toast("ลบเรียบร้อยแล้ว");
		echo true;
		
	}
	
	function diff_month_interval($start,$end){	
		$datetime1 = date_create($start);
		$datetime2 = date_create($end);		
		
		$diff =  $datetime1->diff($datetime2);
		$months = $diff->y * 12 + $diff->m + $diff->d / 30;
		return (int) round($months)+1;
	}
	
	function get_refrain_loan(){
		$refrain_loan_id = @$_POST['refrain_loan_id'];
		$this->db->select(array('t1.*'));
		$this->db->from('coop_refrain_loan as t1');
		$this->db->where("t1.refrain_loan_id = '".$refrain_loan_id."'");
		$this->db->limit(1);
		$rs_refrain = $this->db->get()->result_array();
		$row_refrain = @$rs_refrain[0];		
		echo json_encode($row_refrain);
		exit;
	}
	
	public function report_refrain_loan() {
		$arr_data = array();
		$arr_data['month_arr'] = $this->center_function->month_arr();
		$this->libraries->template('refrain_loan/report_refrain_loan',$arr_data);
	}
	
	public function check_report_refrain_loan_preview() {
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

		$members = $this->get_data_report_refrain_loan($_POST);

		if(!empty($members)){
			echo "success";
		}else{
			echo "";
		}	
	}
	
	public function report_refrain_loan_preview() {
		$arr_data = array();

		$arr_data['month_arr'] = $this->center_function->month_arr();
		$arr_data['month_short_arr'] = $this->center_function->month_short_arr();
		$arr_data['refrain_status'] = ["", "งดต้น", "งดดอกเบี้ย", "งดต้นและดอกเบี้ย"];

		$data_report = $this->get_data_report_refrain_loan($_GET);
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
			$this->load->view('refrain_loan/report_refrain_loan_preview',$arr_data);
		}else{	
			$this->preview_libraries->template_preview('refrain_loan/report_refrain_loan_preview',$arr_data);
		}
	}
	
	public function get_data_report_refrain_loan($data) {
		$results = array();
		if($data['start_date']){
			$start_date_arr = explode('/',$data['start_date']);
			$start_day = $start_date_arr[0];
			$start_month = $start_date_arr[1];
			$start_year = $start_date_arr[2];
			$start_year -= 543;
			//$start_date = $start_year.'-'.$start_month.'-'.$start_day;
			$start_date = $start_year.'-'.$start_month.'-01';
		}
		if($data['end_date']){
			$end_date_arr = explode('/',$data['end_date']);
			$end_day = $end_date_arr[0];
			$end_month = $end_date_arr[1];
			$end_year = $end_date_arr[2];
			$end_year -= 543;
			//$end_date = $end_year.'-'.$end_month.'-'.$end_day;
			$end_date = $end_year.'-'.$end_month.'-01';
		}

		$sql = "SELECT m.* FROM (
					SELECT
						t1.*,
						t2.user_name,
						CONCAT( t4.prename_full, t3.firstname_th, ' ', t3.lastname_th ) AS full_name,
						CONCAT( ( t1.year_start - 543 ), '-', RIGHT ( CONCAT( '00', t1.month_start ), 2 ), '-01' ) AS date_refrain_start,
						CASE 
							WHEN CONCAT( ( t1.year_end - 543 ), '-', RIGHT ( CONCAT( '00', t1.month_end ), 2 ), '-01') IS NULL THEN 
								CONCAT( ( t1.year_start - 543 ), '-', RIGHT ( CONCAT( '00', t1.month_start ), 2 ), '-01' )
							ELSE
								CONCAT( ( t1.year_end - 543 ), '-', RIGHT ( CONCAT( '00', t1.month_end ), 2 ), '-01')
							END AS date_refrain_end
					FROM
						coop_refrain_loan AS t1
						LEFT JOIN coop_user AS t2 ON t1.admin_id = t2.user_id
						LEFT JOIN coop_mem_apply AS t3 ON t1.member_id = t3.member_id
						LEFT JOIN coop_prename AS t4 ON t3.prename_id = t4.prename_id 
					WHERE
						t1.status != 2
					ORDER BY t1.refrain_loan_id ASC	
					) AS m
					WHERE (m.date_refrain_start <= '{$start_date}' AND m.date_refrain_end >= '{$start_date}') 
					OR ( m.date_refrain_start <= '{$end_date}' AND m.date_refrain_end >= '{$end_date}' )";	
		$row = $this->db->query($sql)->result_array();
		//echo $this->db->last_query();						
		if(!empty($row)){
			$results = $row;
		}
		
		return $results;
	}
	
}
