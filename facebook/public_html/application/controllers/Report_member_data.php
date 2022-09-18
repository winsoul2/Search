<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Report_member_data extends CI_Controller {
	function __construct()
	{
		parent::__construct();
		$this->month_arr = array('01'=>'มกราคม','02'=>'กุมภาพันธ์','03'=>'มีนาคม','04'=>'เมษายน','05'=>'พฤษภาคม','06'=>'มิถุนายน','07'=>'กรกฎาคม','08'=>'สิงหาคม','09'=>'กันยายน','10'=>'ตุลาคม','11'=>'พฤศจิกายน','12'=>'ธันวาคม');
		$this->month_short_arr = array('1'=>'ม.ค.','2'=>'ก.พ.','3'=>'มี.ค.','4'=>'เม.ย.','5'=>'พ.ค.','6'=>'มิ.ย.','7'=>'ก.ค.','8'=>'ส.ค.','9'=>'ก.ย.','10'=>'ต.ค.','11'=>'พ.ย.','12'=>'ธ.ค.');
	}
	
	public function coop_report_member_retire(){
		$arr_data = array();
		$this->libraries->template('report_member_data/coop_report_member_retire',$arr_data);
	}
	
	function check_report_member_retire(){	
		if(@$_POST['report_date'] != ''){
			$date_arr = explode('/',@$_POST['report_date']);
			$day = (int)@$date_arr[0];
			$month = (int)@$date_arr[1];
			$year = (int)@$date_arr[2];
			$year -= 543;
			$where = "AND coop_mem_req_resign.resign_date LIKE '".@$year.'-'.sprintf("%02d",@$month)."-".sprintf("%02d",@$day)."%'";
		}else{
			if(@$_POST['month']!='' && @$_POST['year']!=''){
				$day = '';
				$month = @$_POST['month'];
				$year = (@$_POST['year']-543);
				$where = "AND coop_mem_req_resign.resign_date LIKE '".@$year.'-'.sprintf("%02d",@$month)."%'";
			}else{
				$day = '';
				$month = '';
				$year = (@$_POST['year']-543);
				$where = "AND coop_mem_req_resign.resign_date LIKE '".@$year."%'";
			}
		}
		
		$this->db->select('coop_mem_apply.member_id');
		$this->db->from('coop_mem_req_resign');
		$this->db->join("coop_mem_apply", "coop_mem_req_resign.member_id = coop_mem_apply.member_id", "inner");
		$this->db->where("coop_mem_req_resign.req_resign_status = '1' {$where} ");
		$rs_check = $this->db->get()->result_array();
		$row_check = @$rs_check[0];
		//print_r($this->db->last_query());
		if(@$row_check['member_id'] != ''){
			echo "success";
		}		
	}
	
	function coop_report_member_retire_preview(){
		$arr_data = array();
		$this->db->select(array('id','mem_group_name'));
		$this->db->from('coop_mem_group');
		$rs_group = $this->db->get()->result_array();
		$mem_group_arr = array();
		foreach($rs_group as $key => $row_group){
			$mem_group_arr[$row_group['id']] = $row_group['mem_group_name'];
		}
		$arr_data['mem_group_arr'] = $mem_group_arr;
		
		$this->db->select(array('setting_value'));
		$this->db->from('coop_share_setting');
		$this->db->where("setting_id = '1'");
		$row_share_value = $this->db->get()->result_array();
		$share_value = $row_share_value[0]['setting_value'];
		$arr_data['share_value'] = $share_value;
		
		$arr_data['month_arr'] = array('1'=>'มกราคม','2'=>'กุมภาพันธ์','3'=>'มีนาคม','4'=>'เมษายน','5'=>'พฤษภาคม','6'=>'มิถุนายน','7'=>'กรกฎาคม','8'=>'สิงหาคม','9'=>'กันยายน','10'=>'ตุลาคม','11'=>'พฤศจิกายน','12'=>'ธันวาคม');
		$arr_data['month_short_arr'] = array('1'=>'ม.ค.','2'=>'ก.พ.','3'=>'มี.ค.','4'=>'เม.ย.','5'=>'พ.ค.','6'=>'มิ.ย.','7'=>'ก.ค.','8'=>'ส.ค.','9'=>'ก.ย.','10'=>'ต.ค.','11'=>'พ.ย.','12'=>'ธ.ค.');

		$this->preview_libraries->template_preview('report_member_data/coop_report_member_retire_preview',$arr_data);
	}	
	function coop_report_member_retire_excel(){
		$arr_data = array();
		$this->db->select(array('id','mem_group_name'));
		$this->db->from('coop_mem_group');
		$rs_group = $this->db->get()->result_array();
		$mem_group_arr = array();
		foreach($rs_group as $key => $row_group){
			$mem_group_arr[$row_group['id']] = $row_group['mem_group_name'];
		}
		$arr_data['mem_group_arr'] = $mem_group_arr;
		
		$this->db->select(array('setting_value'));
		$this->db->from('coop_share_setting');
		$this->db->where("setting_id = '1'");
		$row_share_value = $this->db->get()->result_array();
		$share_value = $row_share_value[0]['setting_value'];
		$arr_data['share_value'] = $share_value;
		$this->load->view('report_member_data/coop_report_member_retire_excel',$arr_data);
	}
	
	public function coop_report_member_in_out(){
		$arr_data = array();
		$this->libraries->template('report_member_data/coop_report_member_in_out',$arr_data);
	}
	
	function check_report_member_in_out(){
		$where_check = '';
		$where_check2 = '';

		if(@$_POST['start_date']){
			$start_date_arr = explode('/',@$_POST['start_date']);
			$start_day = $start_date_arr[0];
			$start_month = $start_date_arr[1];
			$start_year = $start_date_arr[2];
			$start_year -= 543;
			$start_date = $start_year.'-'.$start_month.'-'.$start_day;
		}
		
		if(@$_POST['end_date']){
			$end_date_arr = explode('/',@$_POST['end_date']);
			$end_day = $end_date_arr[0];
			$end_month = $end_date_arr[1];
			$end_year = $end_date_arr[2];
			$end_year -= 543;
			$end_date = $end_year.'-'.$end_month.'-'.$end_day;
		}
		$where = "1=1";
		
		if(@$_POST['start_date'] != '' AND @$_POST['end_date'] == ''){
			$where_check = " AND t1.apply_date BETWEEN '".$start_date." 00:00:00.000' AND '".$start_date." 23:59:59.000'";
			$where_check2 = " AND t3.resign_date BETWEEN '".$start_date." 00:00:00.000' AND '".$start_date." 23:59:59.000'";
		}else if(@$_POST['start_date'] != '' AND @$_POST['end_date'] != ''){
			$where_check = " AND t1.apply_date BETWEEN '".$start_date." 00:00:00.000' AND '".$end_date." 23:59:59.000'";
			$where_check2 = " AND t3.resign_date BETWEEN '".$start_date." 00:00:00.000' AND '".$end_date." 23:59:59.000'";
		}

		$this->db->select('t1.member_id');
		$this->db->from('coop_mem_apply as t1');
		$this->db->join("coop_prename as t2", "t1.prename_id = t2.prename_id", "left");
		$this->db->where("1=1 {$where_check }");
		$rs_check = $this->db->get()->result_array();
		$row_check = @$rs_check[0];
		
		$this->db->select('t1.member_id');
		$this->db->from('coop_mem_apply as t1');
		$this->db->join("coop_prename as t2 ", "t1.prename_id = t2.prename_id", "left");
		$this->db->join("coop_mem_req_resign as t3 ", "t1.member_id = t3.member_id", "inner");
		$this->db->join("coop_mem_resign_cause as t4 ", "t3.resign_cause_id = t4.resign_cause_id", "left");
		$this->db->where("t3.req_resign_status = '1'  {$where_check2} ");
		$rs_check2 = $this->db->get()->result_array();
		$row_check2 = @$rs_check2[0];
		
		
		if(@$row_check['member_id'] != '' || @$row_check2['member_id'] != ''){
			echo "success";
		}	
	}
	
	function coop_report_member_in_out_preview(){
		$arr_data = array();
		$this->db->select(array('id','mem_group_name'));
		$this->db->from('coop_mem_group');
		$rs_group = $this->db->get()->result_array();
		$mem_group_arr = array();
		foreach($rs_group as $key => $row_group){
			$mem_group_arr[$row_group['id']] = $row_group['mem_group_name'];
		}
		$arr_data['mem_group_arr'] = $mem_group_arr;
		
		$this->db->select(array('id','loan_type'));
		$this->db->from('coop_loan_type');
		$rs_loan_type = $this->db->get()->result_array();
		$loan_type = array();
		foreach($rs_loan_type as $key => $row_loan_type){
			$loan_type[$row_loan_type['id']] = $row_loan_type['loan_type'];
		}
		$arr_data['loan_type'] = $loan_type;

		$this->db->select(array('setting_value'));
		$this->db->from('coop_share_setting');
		$this->db->where("setting_id = '1'");
		$row_share_value = $this->db->get()->result_array();
		$share_value = $row_share_value[0]['setting_value'];
		$arr_data['share_value'] = $share_value;
		
		$arr_data['month_arr'] = array('01'=>'มกราคม','02'=>'กุมภาพันธ์','03'=>'มีนาคม','04'=>'เมษายน','05'=>'พฤษภาคม','06'=>'มิถุนายน','07'=>'กรกฎาคม','08'=>'สิงหาคม','09'=>'กันยายน','10'=>'ตุลาคม','11'=>'พฤศจิกายน','12'=>'ธันวาคม');
		$arr_data['month_short_arr'] = array('1'=>'ม.ค.','2'=>'ก.พ.','3'=>'มี.ค.','4'=>'เม.ย.','5'=>'พ.ค.','6'=>'มิ.ย.','7'=>'ก.ค.','8'=>'ส.ค.','9'=>'ก.ย.','10'=>'ต.ค.','11'=>'พ.ย.','12'=>'ธ.ค.');

		$arr_data['datas'] = $this->get_data_report_member_in_out();


		$this->preview_libraries->template_preview('report_member_data/coop_report_member_in_out_preview',$arr_data);
	}
	
	function coop_report_member_in_out_excel(){
		$arr_data = array();
		$this->db->select(array('id','mem_group_name'));
		$this->db->from('coop_mem_group');
		$rs_group = $this->db->get()->result_array();
		$mem_group_arr = array();
		foreach($rs_group as $key => $row_group){
			$mem_group_arr[$row_group['id']] = $row_group['mem_group_name'];
		}
		$arr_data['mem_group_arr'] = $mem_group_arr;
		
		$this->db->select(array('id','loan_type'));
		$this->db->from('coop_loan_type');
		$rs_loan_type = $this->db->get()->result_array();
		$loan_type = array();
		foreach($rs_loan_type as $key => $row_loan_type){
			$loan_type[$row_loan_type['id']] = $row_loan_type['loan_type'];
		}
		$arr_data['loan_type'] = $loan_type;

		$this->db->select(array('setting_value'));
		$this->db->from('coop_share_setting');
		$this->db->where("setting_id = '1'");
		$row_share_value = $this->db->get()->result_array();
		$share_value = $row_share_value[0]['setting_value'];
		$arr_data['share_value'] = $share_value;

		$arr_data['datas'] = $this->get_data_report_member_in_out();

		$this->load->view('report_member_data/coop_report_member_in_out_excel',$arr_data);
	}

	function get_data_report_member_in_out() {
		if(@$_GET['start_date']){
			$start_date_arr = explode('/',@$_GET['start_date']);
			$start_day = $start_date_arr[0];
			$start_month = $start_date_arr[1];
			$start_year = $start_date_arr[2];
			$start_year -= 543;
			$start_date = $start_year.'-'.$start_month.'-'.$start_day;
		}
		if(@$_GET['end_date']){
			$end_date_arr = explode('/',@$_GET['end_date']);
			$end_day = $end_date_arr[0];
			$end_month = $end_date_arr[1];
			$end_year = $end_date_arr[2];
			$end_year -= 543;
			$end_date = $end_year.'-'.$end_month.'-'.$end_day;
		}

		if(@$_GET['start_date'] != '' AND @$_GET['end_date'] == ''){
			$where_check = " AND t1.apply_date BETWEEN '".$start_date." 00:00:00.000' AND '".$start_date." 23:59:59.000'";
			$where_check2 = " AND t3.resign_date _GET '".$start_date." 00:00:00.000' AND '".$start_date." 23:59:59.000'";
		}else if(@$_GET['start_date'] != '' AND @$_GET['end_date'] != ''){
			$where_check = " AND t1.apply_date BETWEEN '".$start_date." 00:00:00.000' AND '".$end_date." 23:59:59.000'";
			$where_check2 = " AND t3.resign_date BETWEEN '".$start_date." 00:00:00.000' AND '".$end_date." 23:59:59.000'";
		}

		$member_in = $this->db->select('t1.apply_date, t1.member_id, t1.firstname_th, t1.lastname_th,t1.department, t1.level, t1.faction, t2.prename_short, t1.share_month ,t4.member_id as re_register_check, t1.employee_id')
								->from('coop_mem_apply as t1')
								->join('coop_prename as t2','t1.prename_id = t2.prename_id','left')
//								->join('coop_change_share as t3', "t3.member_id = t1.member_id", "left")
								->join("(SELECT * FROM coop_mem_apply WHERE id_card is not null AND id_card != '' group by id_card) as t4", "t4.member_id != t1.member_id AND t4.id_card = t1.id_card AND t4.apply_date > t1.apply_date", "left")
								->where("1=1 {$where_check} AND t1.apply_type_id != '2'")
								->group_by("t1.member_id")
								->order_by('apply_date, t1.member_id')
								->get()->result_array();

		$datas = array();
		$check_year_month = 'x';
		foreach($member_in as $member) {
			$apply_date_arr = explode('-',$member['apply_date']);
			$apply_day = $apply_date_arr[2];
			$apply_month = $apply_date_arr[1];
			$apply_year = $apply_date_arr[0];
            if((int)$apply_day <= 5){
                $month = (int)$apply_month;
                $month--;
                if($month <= 0){
                    $year = (int)$apply_year;
                    $apply_year = $year-1;
                    $month = 12;
                }
                $month = sprintf("%02d",$month);
                $apply_month = $month;
            }
			$datas[$apply_year][$apply_month]['member_in']['members'][] = $member;
			$check_year_month = $apply_year.$apply_month;
		}
        if($_GET['dev']=='dev') {
//            echo '<pre>';print_r($member_in);exit;
            echo '<pre>';print_r($datas);exit;
        }
			
		$member_out = $this->db->select('t1.member_id, t1.firstname_th, t1.lastname_th, t2.prename_full, t1.faction, t3.resign_date, t4.resign_cause_name, t5.sum_share, t6.sum_loan')
								->from('coop_mem_apply as t1')
								->join('coop_prename as t2','t1.prename_id = t2.prename_id','left')
								->join("coop_mem_req_resign as t3 ", "t1.member_id = t3.member_id", "inner")
								->join("coop_mem_resign_cause as t4 ", "t3.resign_cause_id = t4.resign_cause_id", "left")
								->join("(SELECT *, sum(share_early_value) as sum_share FROM coop_mem_share WHERE share_type = 'SRP' group by member_id) as t5", "t1.member_id = t5.member_id AND share_type = 'SRP'", "left")
								->join("(SELECT *, sum(loan_amount_balance) as sum_loan FROM coop_loan group by member_id) as t6", "t1.member_id = t6.member_id", "left")
								->where("t3.req_resign_status = '1' {$where_check2}")
								->order_by("t3.resign_date, t1.member_id")
								->get()->result_array();

		$check_year_month = 'x';
		foreach($member_out as $member) {
			$resign_date_arr = explode('-',$member['resign_date']);
			$resign_day = $resign_date_arr[2];
			$resign_month = $resign_date_arr[1];
			$resign_year = $resign_date_arr[0];
			$datas[$resign_year][$resign_month]['member_out']['members'][] = $member;
			$check_year_month = $resign_year.$resign_month;
		}

		return $datas;
	}
	
	
	function coop_report_member_people_retire_preview(){
		$arr_data = array();	
		
		$arr_data['month_arr'] = array('1'=>'มกราคม','2'=>'กุมภาพันธ์','3'=>'มีนาคม','4'=>'เมษายน','5'=>'พฤษภาคม','6'=>'มิถุนายน','7'=>'กรกฎาคม','8'=>'สิงหาคม','9'=>'กันยายน','10'=>'ตุลาคม','11'=>'พฤศจิกายน','12'=>'ธันวาคม');
		$arr_data['month_short_arr'] = array('1'=>'ม.ค.','2'=>'ก.พ.','3'=>'มี.ค.','4'=>'เม.ย.','5'=>'พ.ค.','6'=>'มิ.ย.','7'=>'ก.ค.','8'=>'ส.ค.','9'=>'ก.ย.','10'=>'ต.ค.','11'=>'พ.ย.','12'=>'ธ.ค.');
		
		$income_details = array();

		$member_id = @$_GET['member_id'];
		if($member_id != ''){
			$this->db->select(array('t1.*',
							't2.mem_group_name AS department_name',
							't3.mem_group_name AS faction_name',
							't4.mem_group_name AS level_name',
							't5.prename_full',
							't6.resign_date',
							't6.receipt_id',
							't6.approve_date',
							't7.resign_cause_name'
							));
			$this->db->from('coop_mem_apply AS t1');			
			$this->db->join("coop_mem_group AS t2","t1.department = t2.id","left");
			$this->db->join("coop_mem_group AS t3","t1.faction = t3.id","left");
			$this->db->join("coop_mem_group AS t4","t1.level = t4.id","left");
			$this->db->join("coop_prename AS t5", "t5.prename_id = t1.prename_id", "left");
			$this->db->join("coop_mem_req_resign AS t6", "t6.member_id = t1.member_id AND t6.req_resign_status = '1'", "left");
			$this->db->join("coop_mem_resign_cause AS t7", "t7.resign_cause_id = t6.resign_cause_id", "left");
			$this->db->where("t1.member_id = '".$member_id."'");
			$rs = $this->db->get()->result_array();
			$row = @$rs[0];
			
			$department = "";
			$department .= @$row['department_name'];
			$department .= (@$row["faction_name"]== 'ไม่ระบุ')?"":"  ".@$row["faction_name"];
			$department .= "  ".@$row["level_name"];
			$row['mem_group_name'] = $department;
			$arr_data['row_member'] = $row;	
			$receipt_id = $row['receipt_id'];
			$arr_data['receipt_id'] = @$receipt_id;
			$approve_date = $row['approve_date'];
			$resign_date = $row['resign_date'];

			if(!empty($receipt_id)) {
                $this->db->select(array('t1.*', 't2.contract_number', 't3.contract_number AS contract_number_atm'));
                $this->db->from('coop_finance_transaction AS t1');
                $this->db->join("coop_loan AS t2", "t1.loan_id = t2.id", "left");
                $this->db->join("coop_loan_atm AS t3", "t1.loan_atm_id = t3.loan_atm_id", "left");
                $this->db->where("t1.receipt_id = '" . $receipt_id . "'");
                $rs_transaction = $this->db->get()->result_array();

                $arr_loan = array();
                foreach ($rs_transaction AS $key => $row_transaction) {
                    //echo '<pre>'; print_r($row_transaction); echo '</pre>';

                    if (@$row_transaction['loan_id'] != '') {
                        $arr_loan[$key]['contract_number'] = @$row_transaction['contract_number'];
                    }

                    if (@$row_transaction['loan_atm_id'] != '') {
                        $arr_loan[$key]['contract_number'] = @$row_transaction['contract_number_atm'];
                    }
                    $arr_loan[$key]['principal_payment'] = @$row_transaction['principal_payment'];
                    $arr_loan[$key]['interest'] = @$row_transaction['interest'];
                    $arr_loan[$key]['total_amount'] = @$row_transaction['total_amount'];
                    $arr_loan[$key]['loan_amount_balance'] = @$row_transaction['loan_amount_balance'];
                    $arr_loan[$key]['loan_interest_remain'] = @$row_transaction['loan_interest_remain'];

                }
                $arr_data['rs_loan'] = @$arr_loan;
            }

			//หุ้น
			$this->db->select('*, sum(share_early_value) as sum');
			$this->db->from('coop_mem_share');
			$this->db->where("member_id = '".$member_id."' AND share_status IN('5')");
			$this->db->order_by('share_date DESC, share_id DESC');
			$this->db->limit(1);
			$row_prev_share = $this->db->get()->result_array();
			$row_prev_share = @$row_prev_share[0];
			$cal_share = @$row_prev_share['sum'];
			$arr_data['cal_share'] = @$cal_share;	
			$income_detail = array();
			$income_detail['income_name'] = 'หุ้น';
			$income_detail['income_amount'] = $cal_share;
			$income_details[] = $income_detail;

			//ใบเสร็จ
			$this->db->select('*');
			$this->db->from('coop_receipt');
			$this->db->where("receipt_id = '".$receipt_id."'");
			$this->db->limit(1);
			$rs_receipt = $this->db->get()->result_array();
			$sum_receipt = @$rs_receipt[0]['sumcount'];
			$arr_data['sum_receipt'] = @$sum_receipt;	
			
			//เงินฝาก
			$this->db->select('*');
			$this->db->from('coop_maco_account');
			$this->db->join('coop_deposit_type_setting', 'coop_deposit_type_setting.type_id = coop_maco_account.type_id', 'left');
			$this->db->where("mem_id = '".$member_id."' AND coop_deposit_type_setting.unique_account != '1'");
			$rs_account = $this->db->get()->result_array();
			$cal_account = 0;

			foreach($rs_account as $key => $value){
				$this->db->select('*');
				$this->db->from('coop_account_transaction');
				$this->db->where("account_id = '".$value['account_id']."' AND transaction_time = '".$resign_date."'");
				$this->db->order_by('transaction_id DESC');
				$this->db->limit(1);
				$row = $this->db->get()->result_array();	
				if(!empty($row)) {		
					$cal_account += @$row[0]['transaction_withdrawal'];

					$this->db->select('*');
					$this->db->from('coop_account_transaction');
					$this->db->where("account_id = '".$value['account_id']."' AND transaction_time = '".$resign_date."' AND transaction_list = 'IN'");
					$this->db->order_by('transaction_id DESC');
					$this->db->limit(1);
					$row_in = $this->db->get()->result_array();

					$this->db->select('*');
					$this->db->from('coop_account_transaction');
					$this->db->where("account_id = '".$value['account_id']."' AND transaction_time = '".$resign_date."' AND transaction_list = 'WTI'");
					$this->db->order_by('transaction_id DESC');
					$this->db->limit(1);
					$row_wti = $this->db->get()->result_array();

					$this->db->select('*');
					$this->db->from('coop_account_transaction');
					$this->db->where("account_id = '".$value['account_id']."' AND transaction_time = '".$resign_date."' AND transaction_list = 'CW'");
					$this->db->order_by('transaction_id DESC');
					$this->db->limit(1);
					$row_cw = $this->db->get()->result_array();

					$income_detail = array();
					$income_detail['income_name'] = $value['type_name'];
					$income_detail['income_amount'] = $row[0]['transaction_withdrawal'];
					$income_detail['income_amount_IN'] = !empty($row_in) ? $row_in[0]['transaction_deposit'] : 0;
					$income_detail['income_amount_WTI'] = !empty($row_wti) ? $row_wti[0]['transaction_withdrawal'] : 0;
					$income_detail['income_amount_CW'] = !empty($row_cw) ? $row_cw[0]['transaction_withdrawal'] : 0;
					$income_details[] = $income_detail;
				}
			}
			
			//บัญชี 21 ที่ถูกปิดไป
			$this->db->select(array('coop_maco_account.account_id','coop_maco_account.mem_id','coop_deposit_type_setting.type_name'));
			$this->db->from('coop_maco_account');
			$this->db->join("coop_deposit_type_setting","coop_maco_account.type_id = coop_deposit_type_setting.type_id","inner");
			$this->db->where("
				coop_maco_account.mem_id = '".$member_id."' 
				AND coop_deposit_type_setting.unique_account = '1'
			");
			$this->db->limit(1);
			$rs_account_21 = $this->db->get()->result_array();
			$row_account_21 = @$rs_account_21[0];
			if(!empty($row_account_21)){		
				$account_id_21 = @$row_account_21['account_id'];
				
				$this->db->select('*');
				$this->db->from('coop_account_transaction');
				$this->db->where("account_id = '".$account_id_21."'"." AND transaction_time = '".$resign_date."'");
				$this->db->order_by('transaction_time DESC, transaction_id DESC');
				$this->db->limit(1);
				$rs_transaction_21 = $this->db->get()->result_array();


				$row_transaction_21 = $rs_transaction_21[0];
				$cal_account += @$row_transaction_21['transaction_withdrawal'];

				$this->db->select('*');
				$this->db->from('coop_account_transaction');
				$this->db->where("account_id = '".$account_id_21."'"." AND transaction_time = '".$resign_date."' AND transaction_list = 'IN'");
				$this->db->order_by('transaction_time DESC, transaction_id DESC');
				$this->db->limit(1);
				$rs_transaction_21_IN = $this->db->get()->result_array();

				$this->db->select('*');
				$this->db->from('coop_account_transaction');
				$this->db->where("account_id = '".$account_id_21."'"." AND transaction_time = '".$resign_date."' AND transaction_list = 'WTI'");
				$this->db->order_by('transaction_time DESC, transaction_id DESC');
				$this->db->limit(1);
				$rs_transaction_21_WTI = $this->db->get()->result_array();

				$this->db->select('*');
				$this->db->from('coop_account_transaction');
				$this->db->where("account_id = '".$account_id_21."'"." AND transaction_time = '".$resign_date."' AND transaction_list = 'CW'");
				$this->db->order_by('transaction_time DESC, transaction_id DESC');
				$this->db->limit(1);
				$rs_transaction_21_CW = $this->db->get()->result_array();

				$income_detail = array();
				$income_detail['income_name'] = $row_account_21['type_name'];
				$income_detail['income_amount'] = $row_transaction_21['transaction_withdrawal'];
				$income_detail['income_amount_IN'] = $rs_transaction_21_IN[0]['transaction_deposit'];
				$income_detail['income_amount_WTI'] = $rs_transaction_21_WTI[0]['transaction_withdrawal'];
				$income_detail['income_amount_CW'] = $rs_transaction_21_CW[0]['transaction_withdrawal'];
				$income_details[] = $income_detail;
			}

			$guarantees = $this->db->select("t1.loan_id, t2.contract_number, t3.firstname_th, t3.lastname_th, t4.prename_full")
											->from("coop_loan_guarantee_person as t1")
											->join("coop_loan as t2", "t1.loan_id = t2.id", "inner")
											->join("coop_mem_apply as t3", "t2.member_id = t3.member_id", "inner")
											->join("coop_prename as t4", "t3.prename_id = t4.prename_id", "left")
											->where("t1.guarantee_person_id = '".$member_id."' AND loan_amount_balance > 0")
											->get()->result_array();
			$arr_data['guarantees'] = $guarantees;

			$arr_data['cal_account'] = @$cal_account;
			$arr_data['income_amount'] = @$cal_share+@$cal_account;
			$arr_data['income_detail'] = $income_details;
				
		}	

		$this->preview_libraries->template_preview('report_member_data/coop_report_member_people_retire_preview',$arr_data);
	}
	function coop_report_member_people_retire_prepare_preview(){
		$arr_data = array();	
		
		$arr_data['month_arr'] = array('1'=>'มกราคม','2'=>'กุมภาพันธ์','3'=>'มีนาคม','4'=>'เมษายน','5'=>'พฤษภาคม','6'=>'มิถุนายน','7'=>'กรกฎาคม','8'=>'สิงหาคม','9'=>'กันยายน','10'=>'ตุลาคม','11'=>'พฤศจิกายน','12'=>'ธันวาคม');
		$arr_data['month_short_arr'] = array('1'=>'ม.ค.','2'=>'ก.พ.','3'=>'มี.ค.','4'=>'เม.ย.','5'=>'พ.ค.','6'=>'มิ.ย.','7'=>'ก.ค.','8'=>'ส.ค.','9'=>'ก.ย.','10'=>'ต.ค.','11'=>'พ.ย.','12'=>'ธ.ค.');
		
		$member_id = @$_GET['member_id'];
		if($member_id != ''){
			$this->db->select(array('t1.*',
							't2.mem_group_name AS department_name',
							't3.mem_group_name AS faction_name',
							't4.mem_group_name AS level_name',
							't5.prename_full'
							));
			$rs = $this->db->from('coop_mem_apply AS t1')
							->join("coop_mem_group AS t2","t1.department = t2.id","left")
							->join("coop_mem_group AS t3","t1.faction = t3.id","left")
							->join("coop_mem_group AS t4","t1.level = t4.id","left")
							->join("coop_prename AS t5", "t5.prename_id = t1.prename_id", "left")
							->where("t1.member_id = '".$member_id."'")
							->get()->result_array();
			$row = $rs[0];
			
			$department = "";
			$department .= $row['department_name'];
			$department .= ($row["faction_name"]== 'ไม่ระบุ')?"":"  ".$row["faction_name"];
			$department .= "  ".$row["level_name"];
			$row['mem_group_name'] = $department;
			$arr_data['row_member'] = $row;	
			$receipt_id = $row['receipt_id'];
			$arr_data['receipt_id'] = "-";
		
			//Loan
			$rs = $this->db->select('*')
							->from('coop_loan')
							->where("member_id = '".$member_id."' AND loan_status = '1'")
							->get()->result_array();
			$row_loan = $rs;

			$loan_principal = 0;
			$loan_interest = 0;
			$date_interesting = date('Y-m-d');
			$process_timestamp = date('Y-m-d H:i:s');
			$rs_loans = array();
			foreach($row_loan as $key => $value){	
				$rs_loan = array();		

				//Get Interest
				$loan_amount = $value['loan_amount_balance'];//เงินกู้
				$loan_type = $value['loan_type'];//ประเภทเงินกู้ใช้หา เรทดอกเบี้ย
				$loan_id = $value['id'];//ใช้หาเรทดอกเบี้ยใหม่ 26/5/2562

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
					$interest_data = 0;
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
					}
				}
				//Get loan interest non pay
				$loan_interest_remain = $this->db->select("loan_id, SUM(non_pay_amount_balance) as sum")
													->from("coop_non_pay_detail")
													->where("loan_id = '".$value['id']."' AND pay_type = 'interest'")
													->get()->row();
				
				$rs_loan['loan_id'] = $value['id'];
				$rs_loan['contract_number'] = $value['contract_number'];
				$rs_loan['principal_payment'] = $value['loan_amount_balance'];
				$rs_loan['interest'] = $interest_data;
				
				$rs_loan['loan_amount_balance'] = $value['loan_amount_balance'];
				$rs_loan['loan_amount_interest_debt'] = !empty($loan_interest_remain) ? $loan_interest_remain->sum : 0;
				$rs_loan['total_amount'] = $value['loan_amount_balance']+$interest_data+$rs_loan['loan_amount_interest_debt'];

				$rs_loans[] = $rs_loan;
			}

			//Loan ATM
			//Status need to change to 1
			$rs = $this->db->select('*')
						->from('coop_loan_atm')
						->where("member_id = '".$member_id."' AND loan_atm_status = '1'")
						->get()->result_array();
			$row_loan = $rs;

			foreach($row_loan as $key => $value){	
				$rs_loan = array();		
				//Get Interest
				$cal_atm_interest = array();
				$cal_atm_interest['loan_atm_id'] = $value['loan_atm_id'];
				$cal_atm_interest['date_interesting'] = date('Y-m-d');
				$interest_data = $this->loan_libraries->cal_atm_interest_report_test($cal_atm_interest,"echo", array("month"=> date("m"), "year" => date("Y") ), false, true )['interest_month'];

				//Get loan interest non pay
				$loan_interest_remain = $this->db->select("loan_atm_id, SUM(non_pay_amount_balance) as sum")
													->from("coop_non_pay_detail")
													->where("loan_atm_id = '".$value['loan_atm_id']."' AND pay_type = 'interest'")
													->get()->row();

				$rs_loan['loan_id'] = $value['id'];
				$rs_loan['contract_number'] = $value['contract_number'];
				$rs_loan['principal_payment'] = @$value['total_amount_approve']-@$value['total_amount_balance'];
				$rs_loan['interest'] = $interest_data;
				
				$rs_loan['loan_amount_balance'] = (@$value['total_amount_approve']-@$value['total_amount_balance']);
				$rs_loan['loan_amount_interest_debt'] = !empty($loan_interest_remain) ? $loan_interest_remain->sum : 0;
				$rs_loan['total_amount'] = (@$value['total_amount_approve']-@$value['total_amount_balance'])+$interest_data+$rs_loan['loan_amount_interest_debt'];

				$rs_loans[] = $rs_loan;
			}

			//Share
			$row_prev_share = $this->db->select('*')
										->from('coop_mem_share')
										->where("member_id = '".$member_id."'")
										->order_by('share_date DESC, share_id DESC')
										->limit(1)
										->get()->result_array();
			$row_prev_share = $row_prev_share[0];
			$cal_share = @$row_prev_share['share_collect_value'];

			//Deposit
			$rs_account = $this->db->select('*')
									->from('coop_maco_account')
									->join('coop_deposit_type_setting', "coop_maco_account.type_id = coop_deposit_type_setting.type_id", "left")
									->where("mem_id = '".$member_id."' AND account_status = '0'")
									->get()->result_array();
			$cal_account = 0;
			$rs_accounts = array();

			foreach($rs_account as $key => $value){
				$row = $this->db->select('*')
								->from('coop_account_transaction')
								->where("account_id = '".$value['account_id']."'")
								->order_by('transaction_id DESC')
								->limit(1)
								->get()->result_array();

				$cal_result = $this->deposit_libraries->cal_deposit_interest_by_acc_date($value['account_id'], $process_timestamp);
				$close_account_interest = $cal_result['interest'];
				$close_account_interest_return = $cal_result['interest_return'];

				$cal_account += $row[0]['transaction_balance'] + $close_account_interest - $close_account_interest_return;

				$rs_accounts[$value['type_id']]['transaction_balance'] += $row[0]['transaction_balance'] + $close_account_interest - $close_account_interest_return;
				$rs_accounts[$value['type_id']]['type_name'] = $value['type_name'];
			}

			$arr_data['cal_share'] = $cal_share;	
			$arr_data['rs_loan'] = $rs_loans;
			$arr_data['accounts'] = $rs_accounts;
			$arr_data['income_amount'] = $cal_share+$cal_account;
				
		}	

		$this->preview_libraries->template_preview('report_member_data/coop_report_member_people_retire_prepare_preview',$arr_data);
	}

	public function coop_report_member_daliy_retrie() {
		$arr_data = array();
		
		$this->libraries->template('report_member_data/coop_report_member_daliy_retrie',$arr_data);
	}

	public function coop_report_member_daliy_retrie_preview() {
		$arr_data = array();

		$result = $this->coop_get_data_member_daliy_retrie();
		$datas = array();
		$page = 0;
		$first_page_size = 18;
		$page_size = 24;
		foreach($result['member_shares'] as $index => $member_share) {
			if($index < $first_page_size) {
				$page = 1;
			} else {
				$page = ceil((($index + 1)-$first_page_size) / $page_size) + 1;
			}
			$datas[$page][] = $member_share;
		}

		$arr_data['datas'] = $datas;
		$arr_data['page_all'] = $page;

		$this->preview_libraries->template_preview('report_member_data/coop_report_member_daliy_retrie_preview',$arr_data);
	}

	public function coop_report_member_daliy_retrie_excel() {
		$arr_data = array();
		$result = $this->coop_get_data_member_daliy_retrie();
		$arr_data['datas'] = $result['member_shares'];
		$this->load->view('report_member_data/coop_report_member_daliy_retrie_excel',$arr_data);
	}

	public function coop_get_data_member_daliy_retrie() {
		$arr_data = array();

		if(@$_GET['start_date']){
			$start_date_arr = explode('/',@$_GET['start_date']);
			$start_day = $start_date_arr[0];
			$start_month = $start_date_arr[1];
			$start_year = $start_date_arr[2];
			$start_year -= 543;
			$start_date = $start_year.'-'.$start_month.'-'.$start_day;
		}

		if(@$_GET['end_date']){
			$end_date_arr = explode('/',@$_GET['end_date']);
			$end_day = $end_date_arr[0];
			$end_month = $end_date_arr[1];
			$end_year = $end_date_arr[2];
			$end_year -= 543;
			$end_date = $end_year.'-'.$end_month.'-'.$end_day;
		}
		$where = "t2.mem_type = 2";

		if(@$_GET['start_date'] != '' AND @$_GET['end_date'] == ''){
			$where .= " AND t1.approve_date BETWEEN '".$start_date." 00:00:00.000' AND '".$start_date." 23:59:59.000'";
		}else if(@$_GET['start_date'] != '' AND @$_GET['end_date'] != ''){
			$where .= " AND t1.approve_date BETWEEN '".$start_date." 00:00:00.000' AND '".$end_date." 23:59:59.000'";
		}

		$member_shares = $this->db->select("t1.member_id, t1.remark, t1.conclusion, t1.approve_date,
											t2.firstname_th, t2.lastname_th, t2.id_card, t2.sex,
											t3.prename_full,
											t4.sum as share_early_value,
											t5.mem_group_name as department_name,
											t6.mem_group_name as faction_name,
											t7.mem_group_name as level_name,
											t8.contract_number, t8.loan_amount_interest, t8.loan_amount_principal, t8.loan_amount_interest_debt,
											t9.income_amount,
											t11.resign_cause_name,
											t12.dividend_acc_num
											")
									->from("coop_mem_req_resign as t1")
									->join("coop_mem_apply as t2", "t1.member_id = t2.member_id", "left")
									->join("coop_prename as t3", "t2.prename_id = t3.prename_id", "left")
									->join("(SELECT *, sum(share_early_value) as sum FROM coop_mem_share WHERE share_type = 'SRP' group by member_id) as t4", "t1.member_id = t4.member_id AND share_type = 'SRP'", "left")
									->join("coop_mem_group AS t5","t2.department = t5.id","left")
									->join("coop_mem_group AS t6","t2.faction = t6.id","left")
									->join("coop_mem_group AS t7","t2.level = t7.id","left")
									->join("coop_resign_loan_detail as t8", "t1.member_id = t8.member_id", "left")
									->join("coop_resign_income_detail as t9", "t1.member_id = t9.member_id AND t9.income_code = 'income_amount'", "left")
									->join("coop_mem_resign_cause as t11", "t11.resign_cause_id = t1.resign_cause_id", "left")
									->join("(SELECT * FROM coop_mem_bank_account GROUP BY member_id) as t12", "t12.member_id = t1.member_id AND t12.dividend_bank_id = '006'", "left")
									->WHERE($where)
									->get()->result_array();
		$member_ids = array_filter(array_unique(array_column($member_shares, 'member_id')));

		$deduct_totals = array();
		foreach($member_shares as $member_share) {
			$deduct_totals[$member_share['member_id']] += $member_share['loan_amount_interest'];
			$deduct_totals[$member_share['member_id']] += $member_share['loan_amount_principal'];
			$deduct_totals[$member_share['member_id']] += $member_share['loan_amount_interest_debt'];
		}

		$account_trans = $this->db->select("t1.mem_id as member_id, t1.account_id as account_id, t3.transaction_withdrawal as balance, t4.transaction_deposit as full_interest, t5.transaction_withdrawal as deduct_interest")
									->from("(SELECT * FROM coop_maco_account WHERE mem_id IN (".implode(',',$member_ids).")) as t1")
									->join("coop_mem_req_resign as t2", "t1.mem_id = t2.member_id", "inner")
									->join("coop_account_transaction as t3", "t3.account_id = t1.account_id AND t3.transaction_time = t2.approve_date AND t3.transaction_list = 'CW'", "inner")
									->join("coop_account_transaction as t4", "t4.account_id = t1.account_id AND t4.transaction_time = t2.approve_date AND t4.transaction_list = 'IN'", "left")
									->join("coop_account_transaction as t5", "t5.account_id = t1.account_id AND t5.transaction_time = t2.approve_date AND t5.transaction_list = 'WTI'", "left")
									->get()->result_array();
		$acc_member_ids = array_column($account_trans, 'member_id');

		$results = array();
		foreach($member_shares as $member_share) {
			$result = array();
			$result = $member_share;
			$result['balance'] = 0;
			$result['interest'] = 0;
			if(in_array($member_share['member_id'],$acc_member_ids)) {
				$member_indexs = array_keys($acc_member_ids,$member_share['member_id']);
				foreach ($member_indexs as $member_index) {
					$tran = $account_trans[$member_index];
					$result['balance'] += $tran['balance'] - ($tran['full_interest'] - $tran['deduct_interest']);
					$result['interest'] += $tran['full_interest'] - $tran['deduct_interest'];
				}
			}
			$result['total'] = $result['balance'] + $result['interest'] + $result['share_early_value'] - $deduct_totals[$member_share['member_id']];
			$results[] = $result;
			$member_prev = $member_share['member_id'];
		}

		$arr_data['member_shares'] = $results;

		return $arr_data;
	}


	function check_coop_report_member_daliy_retrie(){	
		if(@$_POST['start_date']){
			$start_date_arr = explode('/',@$_POST['start_date']);
			$start_day = $start_date_arr[0];
			$start_month = $start_date_arr[1];
			$start_year = $start_date_arr[2];
			$start_year -= 543;
			$start_date = $start_year.'-'.$start_month.'-'.$start_day;
		}
		
		if(@$_POST['end_date']){
			$end_date_arr = explode('/',@$_POST['end_date']);
			$end_day = $end_date_arr[0];
			$end_month = $end_date_arr[1];
			$end_year = $end_date_arr[2];
			$end_year -= 543;
			$end_date = $end_year.'-'.$end_month.'-'.$end_day;
		}
		$where = "t2.mem_type = 2";
		
		if(@$_POST['start_date'] != '' AND @$_POST['end_date'] == ''){
			$where .= " AND t1.resign_date BETWEEN '".$start_date." 00:00:00.000' AND '".$start_date." 23:59:59.000'";
		}else if(@$_POST['start_date'] != '' AND @$_POST['end_date'] != ''){
			$where .= " AND t1.resign_date BETWEEN '".$start_date." 00:00:00.000' AND '".$end_date." 23:59:59.000'";
		}
		$member_shares = $this->db->select("t1.member_id")->from("coop_mem_req_resign as t1")
															->join("coop_mem_apply as t2", "t1.member_id = t2.member_id", "inner")
															->WHERE($where)->get()->result_array();

		//print_r($this->db->last_query());
		if(!empty($member_shares)){
			echo "success";
		}		
	}

	public function coop_report_member_sms_info() {
		$arr_data = array();

		$this->db->select(array('id','mem_group_name'));
		$this->db->from('coop_mem_group');
		$this->db->where("mem_group_type = '1'");
		$row = $this->db->get()->result_array();
		$arr_data['row_mem_group'] = $row;

		$this->libraries->template('report_member_data/coop_report_member_sms_info',$arr_data);
	}

	public function check_coop_report_member_sms_info() {
		if($_POST['level'] != ''){
			$where .= " AND level = '".$_POST['level']."'";
		}else if($_POST['faction'] != ''){
			$where .= " AND faction = '".$_POST['faction']."'";
		}else if($_POST['department'] != ''){
			$where .= " AND department = '".$_POST['department']."'";
		}

		$members = $this->db->select("member_id, mobile")
						->from("coop_mem_apply")
						->where("1=1 ".$where)
						->get()->row();
		if(!empty($members)){
			echo "success";
		}
	}

	public function coop_report_member_sms_info_export() {
		$arr_data = array();

		if($_GET['level'] != '') {
			$where .= " AND level = '".$_GET['level']."'";
		} else if ($_GET['faction'] != '') {
			$where .= " AND faction = '".$_GET['faction']."'";
		} else if ($_GET['department'] != '') {
			$where .= " AND department = '".$_GET['department']."'";
		}

		$members = $this->db->select("t1.member_id,
										t1.mobile,
										t1.address_no,
										t1.address_moo,
										t1.address_village,
										t1.address_road,
										t1.address_soi,
										t1.province_id,
										t1.amphur_id,
										t1.district_id,
										t1.zipcode,
										t2.province_name,
										t2.province_code,
										t3.amphur_name,
										t3.amphur_code,
										t4.district_name,
										t4.district_code
									")
						->from("coop_mem_apply as t1")
						->join("coop_province as t2", "t1.province_id = t2.province_id", "left")
						->join("coop_amphur as t3", "t1.amphur_id = t3.amphur_id", "left")
						->join("coop_district as t4", "t1.district_id = t4.district_id", "left")
						->where("1=1 ".$where)
						->get()->result_array();
		$arr_data['datas'] = $members;

		if($_GET['sms_file_type'] == "csv") {
			$this->load->view('report_member_data/coop_report_member_sms_info_csv',$arr_data);
		} else {
			$this->load->view('report_member_data/coop_report_member_sms_info_excel',$arr_data);
		}
	}

	public function coop_report_member_relinquish() {
		$arr_data = array();
		$this->libraries->template('report_member_data/coop_report_member_relinquish',$arr_data);
	}

	public function coop_report_member_relinquish_preview() {
		$arr_data = array();

		$result = $this->coop_get_data_member_relinquish();

		$datas = array();
		$page = 0;
		$first_page_size = 18;
		$page_size = 24;
		foreach($result['member_shares'] as $index => $member_share) {
			if($index < $first_page_size) {
				$page = 1;
			} else {
				$page = ceil((($index + 1)-$first_page_size) / $page_size) + 1;
			}
			$datas[$page][] = $member_share;
		}

		$arr_data['datas'] = $datas;
		$arr_data['page_all'] = $page;

		$this->preview_libraries->template_preview('report_member_data/coop_report_member_relinquish_preview',$arr_data);
	}

	public function coop_report_member_relinquish_excel() {
		$arr_data = array();
		$result = $this->coop_get_data_member_relinquish();
		$arr_data['datas'] = $result['member_shares'];
		$this->load->view('report_member_data/coop_report_member_relinquish_excel',$arr_data);
	}

	public function coop_get_data_member_relinquish() {
		$arr_data = array();

		if(@$_GET['start_date']){
			$start_date_arr = explode('/',@$_GET['start_date']);
			$start_day = $start_date_arr[0];
			$start_month = $start_date_arr[1];
			$start_year = $start_date_arr[2];
			$start_year -= 543;
			$start_date = $start_year.'-'.$start_month.'-'.$start_day;
		}

		if(@$_GET['end_date']){
			$end_date_arr = explode('/',@$_GET['end_date']);
			$end_day = $end_date_arr[0];
			$end_month = $end_date_arr[1];
			$end_year = $end_date_arr[2];
			$end_year -= 543;
			$end_date = $end_year.'-'.$end_month.'-'.$end_day;
		}
		$where = "t1.req_resign_status = '1' AND (t2.mem_type = 4 OR t2.mem_type = 5)";

		if(@$_GET['start_date'] != '' AND @$_GET['end_date'] == ''){
			$where .= " AND t1.approve_date BETWEEN '".$start_date." 00:00:00.000' AND '".$start_date." 23:59:59.000'";
		}else if(@$_GET['start_date'] != '' AND @$_GET['end_date'] != ''){
			$where .= " AND t1.approve_date BETWEEN '".$start_date." 00:00:00.000' AND '".$end_date." 23:59:59.000'";
		}

		$member_shares = $this->db->select("t1.member_id, t1.remark, t1.conclusion, t1.approve_date,
											t2.firstname_th, t2.lastname_th, t2.id_card, t2.sex,
											t3.prename_full,
											t4.sum as share_early_value,
											t5.mem_group_name as department_name,
											t6.mem_group_name as faction_name,
											t7.mem_group_name as level_name,
											t8.contract_number, t8.loan_amount_interest, t8.loan_amount_principal, t8.loan_amount_interest_debt,
											t9.income_amount,
											t11.resign_cause_name,
											t12.dividend_acc_num
											")
									->from("coop_mem_req_resign as t1")
									->join("coop_mem_apply as t2", "t1.member_id = t2.member_id", "left")
									->join("coop_prename as t3", "t2.prename_id = t3.prename_id", "left")
									->join("(SELECT *, sum(share_early_value) as sum FROM coop_mem_share WHERE share_type = 'SRP' group by member_id) as t4", "t1.member_id = t4.member_id AND share_type = 'SRP'", "left")
									->join("coop_mem_group AS t5","t2.department = t5.id","left")
									->join("coop_mem_group AS t6","t2.faction = t6.id","left")
									->join("coop_mem_group AS t7","t2.level = t7.id","left")
									->join("coop_resign_loan_detail as t8", "t1.member_id = t8.member_id", "left")
									->join("coop_resign_income_detail as t9", "t1.member_id = t9.member_id AND t9.income_code = 'income_amount'", "left")
									->join("coop_mem_resign_cause as t11", "t11.resign_cause_id = t1.resign_cause_id", "left")
									->join("coop_mem_bank_account as t12", "t12.member_id = t1.member_id AND t12.dividend_bank_id = '006' AND t12.id_apply is not null", "left")
									->WHERE($where)
									->get()->result_array();
		$member_ids = array_filter(array_unique(array_column($member_shares, 'member_id')));

		$deduct_totals = array();
		foreach($member_shares as $member_share) {
			$deduct_totals[$member_share['member_id']] += $member_share['loan_amount_interest'];
			$deduct_totals[$member_share['member_id']] += $member_share['loan_amount_principal'];
			$deduct_totals[$member_share['member_id']] += $member_share['loan_amount_interest_debt'];
		}

		$account_trans = $this->db->select("t1.mem_id as member_id, t1.account_id as account_id, t3.transaction_withdrawal as balance, t4.transaction_deposit as full_interest, t5.transaction_withdrawal as deduct_interest")
									->from("(SELECT * FROM coop_maco_account WHERE mem_id IN (".implode(',',$member_ids).")) as t1")
									->join("coop_mem_req_resign as t2", "t1.mem_id = t2.member_id", "inner")
									->join("coop_account_transaction as t3", "t3.account_id = t1.account_id AND t3.transaction_time = t2.approve_date AND t3.transaction_list = 'CW'", "inner")
									->join("coop_account_transaction as t4", "t4.account_id = t1.account_id AND t4.transaction_time = t2.approve_date AND t4.transaction_list = 'IN'", "left")
									->join("coop_account_transaction as t5", "t5.account_id = t1.account_id AND t5.transaction_time = t2.approve_date AND t5.transaction_list = 'WTI'", "left")
									->get()->result_array();
		$acc_member_ids = array_column($account_trans, 'member_id');

		$results = array();
		foreach($member_shares as $member_share) {
			$result = array();
			$result = $member_share;
			$result['balance'] = 0;
			$result['interest'] = 0;
			if(in_array($member_share['member_id'],$acc_member_ids)) {
				$member_indexs = array_keys($acc_member_ids,$member_share['member_id']);
				foreach ($member_indexs as $member_index) {
					$tran = $account_trans[$member_index];
					$result['balance'] += $tran['balance'] - ($tran['full_interest'] - $tran['deduct_interest']);
					$result['interest'] += $tran['full_interest'] - $tran['deduct_interest'];
				}
			}
			$result['total'] = $deduct_totals[$member_share['member_id']] - ($result['balance'] + $result['interest'] + $result['share_early_value']);
			$results[] = $result;
			$member_prev = $member_share['member_id'];
		}

		$arr_data['member_shares'] = $results;

		return $arr_data;
	}

	public function check_coop_report_member_relinquish() {
		if(@$_POST['start_date']){
			$start_date_arr = explode('/',@$_POST['start_date']);
			$start_day = $start_date_arr[0];
			$start_month = $start_date_arr[1];
			$start_year = $start_date_arr[2];
			$start_year -= 543;
			$start_date = $start_year.'-'.$start_month.'-'.$start_day;
		}
		
		if(@$_POST['end_date']){
			$end_date_arr = explode('/',@$_POST['end_date']);
			$end_day = $end_date_arr[0];
			$end_month = $end_date_arr[1];
			$end_year = $end_date_arr[2];
			$end_year -= 543;
			$end_date = $end_year.'-'.$end_month.'-'.$end_day;
		}
		$where = "t1.req_resign_status = '1' AND (t2.mem_type = 4 OR t2.mem_type = 5)";
		
		if(@$_POST['start_date'] != '' AND @$_POST['end_date'] == ''){
			$where .= " AND t1.approve_date BETWEEN '".$start_date." 00:00:00.000' AND '".$start_date." 23:59:59.000'";
		}else if(@$_POST['start_date'] != '' AND @$_POST['end_date'] != ''){
			$where .= " AND t1.approve_date BETWEEN '".$start_date." 00:00:00.000' AND '".$end_date." 23:59:59.000'";
		}
		$member_shares = $this->db->select("t1.member_id")->from("coop_mem_req_resign as t1")
									->join("coop_mem_apply as t2", "t1.member_id = t2.member_id", "inner")
									->WHERE($where)->get()->result_array();



		//print_r($this->db->last_query());
		if(!empty($member_shares)){
			echo "success";
		}	
	}

	public function coop_report_member_data() {
		$arr_data = array();
		$this->libraries->template('report_member_data/coop_report_member_data',$arr_data);
	}

    public function coop_report_member_age_excel(){
        $arr_data = array();

        $members = $this->db->select('t1.firstname_th,
										t1.lastname_th,
										t1.id_card,
										t1.birthday,
										t1.member_date,
										t1.address_no,
										t1.address_moo,
										t1.address_soi,
										t1.address_road,
										t1.mobile,
										t1.zipcode,
										t1.tel,
										t1.member_id,
										t2.prename_full,
										t3.mem_group_name as department_name,
										t4.mem_group_name as faction_name,
										t5.mem_group_name as level_name,
										t6.district_name,
										t7.amphur_name,
										t8.province_name
									')
            ->from('(SELECT * FROM coop_mem_apply WHERE member_status = 1) as t1')
            ->join("coop_prename as t2", "t1.prename_id = t2.prename_id", "left")
            ->join("coop_mem_group as t3","t1.department = t3.id","left")
            ->join("coop_mem_group as t4","t1.faction = t4.id","left")
            ->join("coop_mem_group as t5","t1.level = t5.id","left")
            ->join("coop_district as t6", "t1.district_id = t6.district_id", "left")
            ->join("coop_amphur as t7", "t1.amphur_id = t7.amphur_id", "left")
            ->join("coop_province as t8", "t1.province_id = t8.province_id", "left")
            ->where("t1.member_status = 1")
            ->order_by("t1.member_id ASC")
            ->get()->result_array();
        $arr_data['datas'] = $members;
        $this->load->view('report_member_data/coop_report_member_age_excel',$arr_data);
    }

	public function coop_report_member_age_preview(){
        $arr_data = array();

        $members = $this->db->select('t1.firstname_th,
										t1.lastname_th,
										t1.id_card,
										t1.birthday,
										t1.member_date,
										t1.address_no,
										t1.address_moo,
										t1.address_soi,
										t1.address_road,
										t1.mobile,
										t1.zipcode,
										t1.tel,
										t1.member_id,
										t2.prename_full,
										t3.mem_group_name as department_name,
										t4.mem_group_name as faction_name,
										t5.mem_group_name as level_name,
										t6.district_name,
										t7.amphur_name,
										t8.province_name
									')
            ->from('(SELECT * FROM coop_mem_apply WHERE member_status = 1) as t1')
            ->join("coop_prename as t2", "t1.prename_id = t2.prename_id", "left")
            ->join("coop_mem_group as t3","t1.department = t3.id","left")
            ->join("coop_mem_group as t4","t1.faction = t4.id","left")
            ->join("coop_mem_group as t5","t1.level = t5.id","left")
            ->join("coop_district as t6", "t1.district_id = t6.district_id", "left")
            ->join("coop_amphur as t7", "t1.amphur_id = t7.amphur_id", "left")
            ->join("coop_province as t8", "t1.province_id = t8.province_id", "left")
            ->where("t1.member_status = 1")
            ->order_by("t1.member_id ASC")
            ->get()->result_array();
        $arr_data['datas'] = $members;
        $this->preview_libraries->template_preview('report_member_data/coop_report_member_age_preview',$arr_data);
    }

	public function coop_report_member_address_excel() {
		$arr_data = array();

		$members = $this->db->select('t1.firstname_th,
										t1.lastname_th,
										t1.id_card,
										t1.birthday,
										t1.member_date,
										t1.address_no,
										t1.address_moo,
										t1.address_soi,
										t1.address_road,
										t1.mobile,
										t1.zipcode,
										t1.tel,
										t1.member_id,
										t2.prename_full,
										t3.mem_group_name as department_name,
										t4.mem_group_name as faction_name,
										t5.mem_group_name as level_name,
										t6.district_name,
										t7.amphur_name,
										t8.province_name
									')
								->from('(SELECT * FROM coop_mem_apply WHERE member_status = 1) as t1')
								->join("coop_prename as t2", "t1.prename_id = t2.prename_id", "left")
								->join("coop_mem_group as t3","t1.department = t3.id","left")
								->join("coop_mem_group as t4","t1.faction = t4.id","left")
								->join("coop_mem_group as t5","t1.level = t5.id","left")
								->join("coop_district as t6", "t1.district_id = t6.district_id", "left")
								->join("coop_amphur as t7", "t1.amphur_id = t7.amphur_id", "left")
								->join("coop_province as t8", "t1.province_id = t8.province_id", "left")
								->where("t1.member_status = 1")
								->get()->result_array();
		$arr_data['datas'] = $members;
		$this->load->view('report_member_data/coop_report_member_address_excel',$arr_data);
	}

	public function coop_report_member_department_movement() {
		$arr_data = array();
		$this->libraries->template('report_member_data/coop_report_member_department_movement',$arr_data);
	}

	public function coop_report_member_department_movement_preview() {
		$arr_data = array();

		$arr_data['month_arr'] = $this->month_arr;
		$arr_data['month_short_arr'] = $this->month_short_arr;

		$members = $this->get_data_report_member_department_movement($_GET);

		$datas = array();
		$page = 0;
		$first_page_size = 20;
		$page_size = 28;
		foreach($members as $index => $member) {
			if($index < $first_page_size) {
				$page = 1;
			} else {
				$page = ceil((($index + 1)-$first_page_size) / $page_size) + 1;
			}
			$datas[$page][] = $member;
		}

		$arr_data["datas"] = $datas;
		$arr_data["page_all"] = $page;

		$this->preview_libraries->template_preview('report_member_data/coop_report_member_department_movement_preview',$arr_data);
	}

	public function coop_report_member_department_movement_excel() {
		$arr_data = array();

		$arr_data['month_arr'] = $this->month_arr;
		$arr_data['month_short_arr'] = $this->month_short_arr;
		$arr_data["datas"] = $this->get_data_report_member_department_movement($_GET);

		$this->load->view('report_member_data/coop_report_member_department_movement_excel',$arr_data);
	}

	function get_data_report_member_department_movement($data) {
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
		$where_check = "";
		if(!empty($data['start_date']) && empty($data['end_date'])) {
			$where_check = " AND t1.date_move BETWEEN '".$start_date." 00:00:00.000' AND '".$start_date." 23:59:59.000'";
		}else if(!empty($data['start_date']) && !empty($data['end_date'])) {
			$where_check = " AND t1.date_move BETWEEN '".$start_date." 00:00:00.000' AND '".$end_date." 23:59:59.000'";
		}

		$datas = array();

		$members = $this->db->select("t1.member_id,
										t1.date_move,
										t10.prename_full,
										t2.firstname_th,
										t2.lastname_th,
										t2.salary,
										t3.mem_group_name as department_old_name,
										t4.mem_group_name as faction_old_name,
										t5.mem_group_name as level_old_name,
										t6.mem_group_name as department_name,
										t7.mem_group_name as faction_name,
										t8.mem_group_name as level_name,
										t9.user_name
									")
							->from("coop_mem_group_move as t1")
							->join("coop_mem_apply as t2", "t1.member_id = t2.member_id", "inner")
							->join("coop_mem_group as t3", "t1.department_old = t3.id", "left")
							->join("coop_mem_group as t4", "t1.faction_old = t4.id", "left")
							->join("coop_mem_group as t5", "t1.level_old = t5.id", "left")
							->join("coop_mem_group as t6", "t1.department = t6.id", "left")
							->join("coop_mem_group as t7", "t1.faction = t7.id", "left")
							->join("coop_mem_group as t8", "t1.level = t8.id", "left")
							->join("coop_user as t9", "t1.admin_id = t9.user_id", "left")
							->join("coop_prename as t10", "t2.prename_id = t10.prename_id", "left")
							->where("1=1 AND t1.status_move = 1 ".$where_check)
							->order_by("t1.date_move, t1.member_id")
							->get()->result_array();

		return $members;
	}

	public function check_data_report_member_department_movement() {
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
		$where_check = "";
		if(!empty($data['start_date']) && empty($data['end_date'])) {
			$where_check = " AND t1.date_move BETWEEN '".$start_date." 00:00:00.000' AND '".$start_date." 23:59:59.000'";
		}else if(!empty($data['start_date']) && !empty($data['end_date'])) {
			$where_check = " AND t1.date_move BETWEEN '".$start_date." 00:00:00.000' AND '".$end_date." 23:59:59.000'";
		}
		$members = $this->db->select("t1.member_id")
							->from("coop_mem_group_move as t1")
							->where("1=1 AND t1.status_move = 1 ".$where_check)
							->order_by("t1.date_move, t1.member_id")
							->get()->result_array();

		if($members){
			echo "success";
		}
	}

	public function coop_report_member_name() {
		$arr_data = array();

		$row = $this->db->select(array('id','mem_group_name'))
							->from('coop_mem_group')
							->where("mem_group_type = '1'")
							->get()->result_array();
		$arr_data['row_mem_group'] = $row;

		$row = $this->db->select('mem_type_id, mem_type_name')->from('coop_mem_type')->get()->result_array();
		$arr_data['mem_type'] = $row;

		//Get Loan Type
		$row = $this->db->select('type_id, type_name')->from('coop_term_of_loan')->get()->result_array();
		$arr_data['term_of_loans'] = $row;

		$this->libraries->template('report_member_data/coop_report_member_name',$arr_data);
	}

	public function check_data_report_member_name() {
		$where = "mem_type in (1,7)";
		if (!empty($_POST["mem_type"]) && !in_array("all", $_POST["mem_type"])){
			$where .= " AND mem_type_id IN (".implode(',', $_POST["mem_type"]).")";
		}
		if(!empty($_POST["level"])){
			$where .= " AND level = '".$_POST['level']."'";
		}else if(!empty($_POST["faction"])){
			$where .= " AND faction = '".$_POST['faction']."'";
		}else if(!empty($_POST["department"])){
			$where .= " AND department = '".$_POST['department']."'";
		}
		$members = $this->db->select("member_id")
								->from("coop_mem_apply")
								->where($where)
								->get()->row();
		if(!empty($members)){
			echo "success";
		}
	}

	public function coop_report_member_name_preview() {
		$arr_data = array();

		$members = $this->get_member_name($_GET);

		$datas = array();
		$page = 0;
		$first_page_size = 12;
		$page_size = 30;
		$prev_level = "x";
		$index = 0;
		$first_page_level = 1;
		$page_index = 1;

		foreach($members as $member) {
			if($prev_level != $member["level"] && $prev_level != "x") {
				if(($index + 1) <= $first_page_size) {
					$index = $first_page_size;
				} else {
					$index = (($page-1) * $page_size) + $first_page_size;
				}
				$page++;
				$first_page_level = $page;
				$page_index = 0;
			} else if($index < $first_page_size) {
				$page = 1;
				$page_index = 0;
			} else {
				if(($page_index > $first_page_size && $first_page_level == $page)
					|| ($page_index > $page_size && $first_page_level != $page)) {
					$page++;
					$page_index = 0;
				}
			}
			$datas[$page][] = $member;
			$prev_level = $member["level"];
			$index++;
			$page_index++;
		}

		$arr_data['datas'] = $datas;
		$arr_data['page_all'] = $page;
		$arr_data['max'] = count($members) - 1;
		if(@$_GET['dev2']=='dev2'){
			echo '<pre>'; print_r($datas); echo '</pre>';
		}

		$this->preview_libraries->template_preview('report_member_data/coop_report_member_name_preview',$arr_data);
	}

	public function coop_report_member_name_excel() {
		$arr_data = array();
		$members = $this->get_member_name($_GET);
		$arr_data['datas'] = $members;
		$this->load->view('report_member_data/coop_report_member_name_excel',$arr_data);
	}

	public function get_member_name($data) {
		$where = "t1.mem_type in (1,7)";
		if (!empty($data["mem_type"]) && !in_array("all", $data["mem_type"])){
			$where .= " AND t1.mem_type_id IN (".implode(',', $data["mem_type"]).")";
		}
		if(!empty($data["level"])){
			$where .= " AND t1.level = '".$data['level']."'";
		}else if(!empty($data["faction"])){
			$where .= " AND t1.faction = '".$data['faction']."'";
		}else if(!empty($data["department"])){
			$where .= " AND t1.department = '".$data['department']."'";
		}
		$members = $this->db->select("t1.member_id, t1.firstname_th, t1.lastname_th, t1.level, t2.prename_full, t3.mem_group_name, t3.mem_group_id")
								->from("coop_mem_apply as t1")
								->join("coop_prename as t2", "t1.prename_id = t2.prename_id", "left")
								->join("coop_mem_group as t3", "t1.level = t3.id", "left")
								->where($where)
								->order_by("t1.department, t1.faction, t1.level, t1.member_id")
								->get()->result_array();
		return $members;
	}

	public function coop_report_wait_for_approval_member() {
		$arr_data = array();
		$this->libraries->template('report_member_data/coop_report_wait_for_approval_member',$arr_data);
	}

	public function check_coop_wait_for_approval_info() {
		$where = "t1.apply_date is not null";
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
		if(!empty($_POST['start_date']) && empty($_POST['end_date'])) {
			$where .= " AND ((t1.member_date = '".$start_date."' AND t1.member_status in (1,2))
								OR (t1.member_status not in (1,2) AND t1.apply_date = '".$start_date."')) ";
		}else if(!empty($_POST['start_date']) && !empty($_POST['end_date'])) {
			$where .= " AND ((t1.member_date BETWEEN '".$start_date."' AND '".$end_date."' AND t1.member_status in (1,2))
								OR (t1.member_status not in (1,2) AND t1.apply_date BETWEEN '".$start_date."' AND '".$end_date."')) ";			
		}
		if(!empty($_POST["apply_type_id"])) {
			$where .= $_POST["apply_type_id"] == '2' ? " AND t1.apply_type_id = 2" : " AND t1.apply_type_id != 2";
		}
		if(!empty($_POST["member_status"])) {
			$where .= $_POST["member_status"] == "1" ? " AND t1.member_status in (1,2)" : " AND t1.member_status = '".$_POST["member_status"]."'";
		}
		$members = $this->db->select("t1.mem_apply_id")
							->from("coop_mem_apply as t1")
							->where($where)
							->get()->result_array();
		if(!empty($members)){
			echo "success";
		}
	}

	public function coop_report_wait_for_approval_member_preview() {
		$arr_data = array();
		$members = $this->get_wait_for_approval_member($_GET);

		$page = 0;
		$first_page_size = 30;
		$page_size = 35;
		foreach($members as $index => $member) {
			if($index < $first_page_size) {
				$page = 1;
			} else {
				$page = ceil((($index + 1)-$first_page_size) / $page_size) + 1;
			}
			$datas[$page][] = $member;
		}

		$arr_data['datas'] = $datas;
		$arr_data['page_all'] = $page;
		$arr_data['member_count'] = count($members);
		$status_text_arr = array(1=>"อนุมัติ",3=>"รออนุมัติ",4=>"ไม่อนุมัติ");
		if(!empty($_GET["member_status"])) {
			$arr_data['member_status_text'] = $status_text_arr[$_GET["member_status"]];
		}

		$this->preview_libraries->template_preview('report_member_data/coop_report_wait_for_approval_member_preview',$arr_data);
	}

	public function coop_report_wait_for_approval_member_excel() {
		$arr_data = array();
		$members = $this->get_wait_for_approval_member($_GET);

		$arr_data['datas'] = $members;
		$status_text_arr = array(1=>"อนุมัติ",3=>"รออนุมัติ",4=>"ไม่อนุมัติ");
		if(!empty($_GET["member_status"])) {
			$arr_data['member_status_text'] = $status_text_arr[$_GET["member_status"]];
		} else if(!empty($_GET["approve_page"])) {
			$arr_data['member_status_text'] = "รออนุมัติ";
		}
		$this->load->view('report_member_data/coop_report_wait_for_approval_member_name_excel',$arr_data);
	}

	public function get_wait_for_approval_member($data) {
		$where = "t1.apply_date is not null";
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
		if(!empty($data['start_date']) && empty($data['end_date'])) {
			$where .= " AND ((t1.member_date = '".$start_date."' AND t1.member_status in (1,2))
								OR (t1.member_status not in (1,2) AND t1.apply_date = '".$start_date."')) ";
		}else if(!empty($data['start_date']) && !empty($data['end_date'])) {
			$where .= " AND ((t1.member_date BETWEEN '".$start_date."' AND '".$end_date."' AND t1.member_status in (1,2))
								OR (t1.member_status not in (1,2) AND t1.apply_date BETWEEN '".$start_date."' AND '".$end_date."')) ";
		}
		if(!empty($data["approve_page"])) {
			$where .= " AND t1.member_status IN ('3','4')";
		}
		if(!empty($data["apply_type_id"])) {
			$where .= $data["apply_type_id"] == '2' ? " AND t1.apply_type_id = 2" : " AND t1.apply_type_id != 2";
		}
		if(!empty($data["member_status"])) {
			$where .= $data["member_status"] == "1" ? " AND t1.member_status in (1,2)" : " AND t1.member_status = '".$data["member_status"]."'";
		}
		$members = $this->db->select("t1.mem_apply_id, t1.apply_date, t1.member_date, t1.firstname_th, t1.lastname_th, t1.id_card, t1.position, t1.mobile, t1.birthday, t1.salary, t1.share_month, t1.register_note, t1.member_id,
										t2.prename_full, t3.mem_group_name")
								->from("coop_mem_apply as t1")
								->join("coop_prename as t2", "t2.prename_id = t1.prename_id", "left")
								->join("coop_mem_group as t3", "t1.level = t3.id", "left")
								->order_by("t1.member_date, t1.apply_date")
								->where($where)
								->get()->result_array();
		return $members;
	}

	public function coop_report_member_daliy_fired() {
		$arr_data = array();
		$this->libraries->template('report_member_data/coop_report_member_daliy_fired',$arr_data);
	}

	public function coop_report_member_daliy_fired_preview() {
		$arr_data = array();

		$result = $this->coop_get_data_member_daliy_fired();
		$datas = array();
		$page = 0;
		$first_page_size = 18;
		$page_size = 24;
		foreach($result['member_shares'] as $index => $member_share) {
			if($index < $first_page_size) {
				$page = 1;
			} else {
				$page = ceil((($index + 1)-$first_page_size) / $page_size) + 1;
			}
			$datas[$page][] = $member_share;
		}

		$arr_data['datas'] = $datas;
		$arr_data['page_all'] = $page;

		$this->preview_libraries->template_preview('report_member_data/coop_report_member_daliy_fired_preview',$arr_data);
	}

	public function coop_report_member_daliy_fired_excel() {
		$arr_data = array();
		$result = $this->coop_get_data_member_daliy_fired();
		$arr_data['datas'] = $result['member_shares'];
		$this->load->view('report_member_data/coop_report_member_daliy_fired_excel',$arr_data);
	}

	public function coop_get_data_member_daliy_fired() {
		$arr_data = array();

		if(@$_GET['start_date']){
			$start_date_arr = explode('/',@$_GET['start_date']);
			$start_day = $start_date_arr[0];
			$start_month = $start_date_arr[1];
			$start_year = $start_date_arr[2];
			$start_year -= 543;
			$start_date = $start_year.'-'.$start_month.'-'.$start_day;
		}

		if(@$_GET['end_date']){
			$end_date_arr = explode('/',@$_GET['end_date']);
			$end_day = $end_date_arr[0];
			$end_month = $end_date_arr[1];
			$end_year = $end_date_arr[2];
			$end_year -= 543;
			$end_date = $end_year.'-'.$end_month.'-'.$end_day;
		}
		$where = "t2.mem_type = 5";

		if(@$_GET['start_date'] != '' AND @$_GET['end_date'] == ''){
			$where .= " AND t1.approve_date BETWEEN '".$start_date." 00:00:00.000' AND '".$start_date." 23:59:59.000'";
		}else if(@$_GET['start_date'] != '' AND @$_GET['end_date'] != ''){
			$where .= " AND t1.approve_date BETWEEN '".$start_date." 00:00:00.000' AND '".$end_date." 23:59:59.000'";
		}

		$member_shares = $this->db->select("t1.member_id, t1.remark, t1.conclusion,
											t2.firstname_th, t2.lastname_th, t2.id_card, t2.sex,
											t3.prename_full,
											t4.sum as share_early_value,
											t5.mem_group_name as department_name,
											t6.mem_group_name as faction_name,
											t7.mem_group_name as level_name,
											t8.contract_number, t8.loan_amount_interest, t8.loan_amount_principal, t8.loan_amount_interest_debt,
											t9.income_amount,
											t11.resign_cause_name,
											t12.dividend_acc_num
											")
									->from("coop_mem_req_resign as t1")
									->join("coop_mem_apply as t2", "t1.member_id = t2.member_id", "left")
									->join("coop_prename as t3", "t2.prename_id = t3.prename_id", "left")
									->join("(SELECT *, sum(share_early_value) as sum FROM coop_mem_share WHERE share_type = 'SRP' group by member_id) as t4", "t1.member_id = t4.member_id AND share_type = 'SRP'", "left")
									->join("coop_mem_group AS t5","t2.department = t5.id","left")
									->join("coop_mem_group AS t6","t2.faction = t6.id","left")
									->join("coop_mem_group AS t7","t2.level = t7.id","left")
									->join("coop_resign_loan_detail as t8", "t1.member_id = t8.member_id", "left")
									->join("coop_resign_income_detail as t9", "t1.member_id = t9.member_id AND t9.income_code = 'income_amount'", "left")
									->join("coop_mem_resign_cause as t11", "t11.resign_cause_id = t1.resign_cause_id", "left")
									->join("(SELECT * FROM coop_mem_bank_account GROUP BY member_id) as t12", "t12.member_id = t1.member_id AND t12.dividend_bank_id = '006'", "left")
									->WHERE($where)
									->get()->result_array();
		$member_ids = array_filter(array_unique(array_column($member_shares, 'member_id')));

		$deduct_totals = array();
		foreach($member_shares as $member_share) {
			$deduct_totals[$member_share['member_id']] += $member_share['loan_amount_interest'];
			$deduct_totals[$member_share['member_id']] += $member_share['loan_amount_principal'];
			$deduct_totals[$member_share['member_id']] += $member_share['loan_amount_interest_debt'];
		}

		$account_trans = $this->db->select("t1.mem_id as member_id, t1.account_id as account_id, t3.transaction_withdrawal as balance, t4.transaction_deposit as full_interest, t5.transaction_withdrawal as deduct_interest")
									->from("(SELECT * FROM coop_maco_account WHERE mem_id IN (".implode(',',$member_ids).")) as t1")
									->join("coop_mem_req_resign as t2", "t1.mem_id = t2.member_id", "inner")
									->join("coop_account_transaction as t3", "t3.account_id = t1.account_id AND t3.transaction_time = t2.approve_date AND t3.transaction_list = 'CW'", "inner")
									->join("coop_account_transaction as t4", "t4.account_id = t1.account_id AND t4.transaction_time = t2.approve_date AND t4.transaction_list = 'IN'", "left")
									->join("coop_account_transaction as t5", "t5.account_id = t1.account_id AND t5.transaction_time = t2.approve_date AND t5.transaction_list = 'WTI'", "left")
									->get()->result_array();
		$acc_member_ids = array_column($account_trans, 'member_id');

		$results = array();
		foreach($member_shares as $member_share) {
			$result = array();
			$result = $member_share;
			$result['balance'] = 0;
			$result['interest'] = 0;
			if(in_array($member_share['member_id'],$acc_member_ids)) {
				$member_indexs = array_keys($acc_member_ids,$member_share['member_id']);
				foreach ($member_indexs as $member_index) {
					$tran = $account_trans[$member_index];
					$result['balance'] += $tran['balance'] - ($tran['full_interest'] - $tran['deduct_interest']);
					$result['interest'] += $tran['full_interest'] - $tran['deduct_interest'];
				}
			}
			$result['total'] = $deduct_totals[$member_share['member_id']] - ($result['balance'] + $result['interest'] + $result['share_early_value']);
			$results[] = $result;
			$member_prev = $member_share['member_id'];
		}

		$arr_data['member_shares'] = $results;

		return $arr_data;
	}

	function check_coop_report_member_daliy_fired(){	
		if(@$_POST['start_date']){
			$start_date_arr = explode('/',@$_POST['start_date']);
			$start_day = $start_date_arr[0];
			$start_month = $start_date_arr[1];
			$start_year = $start_date_arr[2];
			$start_year -= 543;
			$start_date = $start_year.'-'.$start_month.'-'.$start_day;
		}
		
		if(@$_POST['end_date']){
			$end_date_arr = explode('/',@$_POST['end_date']);
			$end_day = $end_date_arr[0];
			$end_month = $end_date_arr[1];
			$end_year = $end_date_arr[2];
			$end_year -= 543;
			$end_date = $end_year.'-'.$end_month.'-'.$end_day;
		}
		$where = "t2.mem_type = 5";

		if(@$_POST['start_date'] != '' AND @$_POST['end_date'] == ''){
			$where .= " AND t1.resign_date BETWEEN '".$start_date." 00:00:00.000' AND '".$start_date." 23:59:59.000'";
		}else if(@$_POST['start_date'] != '' AND @$_POST['end_date'] != ''){
			$where .= " AND t1.resign_date BETWEEN '".$start_date." 00:00:00.000' AND '".$end_date." 23:59:59.000'";
		}
		$member_shares = $this->db->select("t1.member_id")->from("coop_mem_req_resign as t1")
															->join("coop_mem_apply as t2", "t1.member_id = t2.member_id", "inner")
															->WHERE($where)->get()->result_array();

		if(!empty($member_shares)){
			echo "success";
		}		
	}

	public function coop_report_member_share() {
		$arr_data = array();
		$this->libraries->template('report_member_data/coop_report_member_share',$arr_data);
	}

	/*
		หากต้องการใช้รายงานนี้ต้องมีข้อมูล
		- coop_profile.coop_member_id
		- coop_profile.coop_province_id
	*/
	public function check_coop_report_member_share() {
		$where = "share_status IN (1,2,5) AND share_collect > 0";
		if($_POST['start_date']){
			$start_date_arr = explode('/',$_POST['start_date']);
			$start_day = $start_date_arr[0];
			$start_month = $start_date_arr[1];
			$start_year = $start_date_arr[2];
			$start_year -= 543;
			$start_date = $start_year.'-'.$start_month.'-'.$start_day;
			$where .= " AND share_date <= '".$start_date."'";
		}
		$share = $this->db->query("SELECT share_id FROM coop_mem_share WHERE ".$where." LIMIT 1")->row();
		if(!empty($share)){
			echo "success";
		}
	}

	public function coop_report_member_share_excel() {
        $arr_data = array();
        $strDate = explode("/", $_POST['start_date']);
        $day = $strDate[0];
        $month = $strDate[1];
        $year = $strDate[2];
        $result = $this->get_mem_share($_POST);
        $arr_data["datas"] = $result["shares"];
        $arr_data["profile"] = $result["profile"];
        $arr_data["period_setting"] = $result["period_setting"];
        $arr_data["date"] = $_POST['start_date'];

        if($month >= $arr_data["period_setting"]["accm_month_ini"]){
            $year++;
        }
        $arr_data["year"] = $year;
        if(!empty($_POST['format_report'] == 'txt')){
            $this->load->view('report_member_data/coop_report_member_share_txt',$arr_data);
        }else{
            $this->load->view('report_member_data/coop_report_member_share_excel',$arr_data);
        }
	}

	public function get_mem_share($param) {
		$result = array();
		$where = "share_status IN (1,2,5) AND share_collect > 0";
		$where_resign = "";
        $member_date = "";
		if($param['start_date']){
			$start_date_arr = explode('/',$param['start_date']);
			$start_day = $start_date_arr[0];
			$start_month = $start_date_arr[1];
			$start_year = $start_date_arr[2];
			$start_year -= 543;
			$start_date = $start_year.'-'.$start_month.'-'.$start_day.' 23:59:59';
			$where .= " AND share_date <= '".$start_date."'";
			$where_resign .= " AND t4.approve_date <= '".$start_date." 23:59:59'";
			$member_date = " AND t1.member_date <='".$start_date." 23:59:59' ";
		}

		$members = $this->db->select("t1.member_id,
										t1.firstname_th,
										t1.lastname_th,
										t1.member_date,
										t1.id_card,
										t1.nationality,
										t1.c_address_no,
										t1.c_address_moo,
										t1.c_address_village,
										t1.c_address_road,
										t1.c_address_soi,
										t5.province_name,
										t6.amphur_name,
										t7.district_name,
										t1.c_zipcode,
										t2.prename_full,
										t3.mem_type_code,
										t4.req_resign_date")
							->from("coop_mem_apply as t1")
							->join("coop_prename as t2", "t1.prename_id = t2.prename_id", "left")
							->join("coop_mem_type as t3", "t1.mem_type_id = t3.mem_type_id", "left")
							->join("coop_mem_req_resign as t4", "t1.member_id = t4.member_id AND t4.req_resign_status = 1".$where_resign, "left")
							->join("coop_province as t5", "t1.c_province_id = t5.province_id", "left")
							->join("coop_amphur as t6", "t1.c_amphur_id = t6.amphur_id", "left")
							->join("coop_district as t7", "t1.c_district_id = t7.district_id", "left")
							->where("1=1 ".$member_date." AND t1.member_status <> 5 ")
//                            ->limit(100)
							->get()->result_array();

		$shares = array();
		foreach($members as $member) {
			$share = $this->db->select("*")->from("coop_mem_share")->where($where." AND member_id = '".$member["member_id"]."'")->order_by("share_date DESC")->get()->row();
			if(!empty($share) && !empty($share->share_collect)) {
				$data = $member;
				$data["share"] = $share->share_collect;
				$data["share_value"] = $share->share_value;
				$shares[] = $data;
			}
		}

		$result["shares"] = $shares;

        $period_setting = $this->db->select("t1.accm_month_ini, t1.accm_month_name")
            ->from("coop_account_period_setting as t1")
            ->get()->row_array();
        $result["period_setting"] = $period_setting;

		$profile = $this->db->select("t1.coop_member_id, t2.province_name")
								->from("coop_profile as t1")
								->join("coop_province as t2", "t1.coop_province_id = t2.province_id", "left")
								->get()->result_array()[0];
		$result["profile"] = $profile;

		return $result;
	}
}
