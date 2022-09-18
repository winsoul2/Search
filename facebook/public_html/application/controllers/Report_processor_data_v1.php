<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Report_processor_data extends CI_Controller {
	public $month_arr = array('1'=>'มกราคม','2'=>'กุมภาพันธ์','3'=>'มีนาคม','4'=>'เมษายน','5'=>'พฤษภาคม','6'=>'มิถุนายน','7'=>'กรกฎาคม','8'=>'สิงหาคม','9'=>'กันยายน','10'=>'ตุลาคม','11'=>'พฤศจิกายน','12'=>'ธันวาคม');
	public $month_short_arr = array('1'=>'ม.ค.','2'=>'ก.พ.','3'=>'มี.ค.','4'=>'เม.ย.','5'=>'พ.ค.','6'=>'มิ.ย.','7'=>'ก.ค.','8'=>'ส.ค.','9'=>'ก.ย.','10'=>'ต.ค.','11'=>'พ.ย.','12'=>'ธ.ค.');

	function __construct()
	{
		parent::__construct();
		$this->month_arr = array('1'=>'มกราคม','2'=>'กุมภาพันธ์','3'=>'มีนาคม','4'=>'เมษายน','5'=>'พฤษภาคม','6'=>'มิถุนายน','7'=>'กรกฎาคม','8'=>'สิงหาคม','9'=>'กันยายน','10'=>'ตุลาคม','11'=>'พฤศจิกายน','12'=>'ธันวาคม');
	}
	public function coop_report_charged_department(){
		$arr_data = array();

		$this->db->select(array('id','mem_group_name'));
		$this->db->from('coop_mem_group');
		$this->db->where("mem_group_type = '1'");
		$row = $this->db->get()->result_array();
		$arr_data['row_mem_group'] = $row;

		$this->db->select('mem_type_id, mem_type_name');
		$this->db->from('coop_mem_type');
		$row = $this->db->get()->result_array();
		$arr_data['mem_type'] = $row;

		//Get Loan Type
		$row = $this->db->select('type_id, type_name')->from('coop_term_of_loan')->get()->result_array();
		$arr_data['term_of_loans'] = $row;

		$this->libraries->template('report_processor_data/coop_report_charged_department',$arr_data);
	}

	function coop_report_charged_department_preview(){
		set_time_limit(180);
		if (!empty($_GET["mem_type"]) && in_array("all", $_GET["mem_type"])){
			$_GET['mem_type'] = '';
		}
		if (empty($_GET['department'])) $_GET['department'] = '';
		if (empty($_GET['faction'])) $_GET['faction'] = '';
		if (empty($_GET['level'])) $_GET['level'] = '';

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

		$this->db->select(array('id','loan_type','loan_type_code'));
		$this->db->from('coop_loan_type');
		$this->db->order_by("order_by");
		$row = $this->db->get()->result_array();
		$arr_data['loan_type'] = $row;

		$arr_data['month_arr'] = array('1'=>'มกราคม','2'=>'กุมภาพันธ์','3'=>'มีนาคม','4'=>'เมษายน','5'=>'พฤษภาคม','6'=>'มิถุนายน','7'=>'กรกฎาคม','8'=>'สิงหาคม','9'=>'กันยายน','10'=>'ตุลาคม','11'=>'พฤศจิกายน','12'=>'ธันวาคม');
		$arr_data['month_short_arr'] = array('1'=>'ม.ค.','2'=>'ก.พ.','3'=>'มี.ค.','4'=>'เม.ย.','5'=>'พ.ค.','6'=>'มิ.ย.','7'=>'ก.ค.','8'=>'ส.ค.','9'=>'ก.ย.','10'=>'ต.ค.','11'=>'พ.ย.','12'=>'ธ.ค.');
		//echo"<pre>";print_r($arr_data['loan_type']);exit;
		if($_GET['type_department'] == '1'){
			$this->preview_libraries->template_preview('report_processor_data/coop_report_charged_department_preview',$arr_data);
		} else if ($_GET['type_department'] == '3') {
			// $this->preview_libraries->template_preview('report_processor_data/coop_report_charged_person_preview',$arr_data);
			$this->coop_report_charged_person_preview($arr_data);
		}else{
			$this->preview_libraries->template_preview('report_processor_data/coop_report_charged_level_preview',$arr_data);
		}

	}

	function coop_report_charged_person_preview($arr_data) {
		//Set condition from URL
		$member_where = "";
		if (!empty($_GET["mem_type"]) && !in_array("all", $_GET["mem_type"])){
			$member_where .= " AND mem_type_id IN (".implode(',', $_GET["mem_type"]).")";
		}
		if(@$_GET['department']!=''){
			$member_where .= " AND IF (
				t4.department_old IS NULL,
				t3.department,
				t4.department_old
			) = '".$_GET['department']."'";
		}
		if(@$_GET['faction']!=''){
			$member_where .= " AND faction = '".$_GET['faction']."'";
		}
		if(@$_GET['level']!=''){
			$member_where .= " AND level = '".$_GET['level']."'";
		}

		$loan_where = "1=1";
		if ($_GET['term_of_loan']) {
			$loan_where .= " AND t1.loan_type = ".$_GET['term_of_loan'];
		}

		$month = $_GET['month'];
		$year = $_GET['year'];

		//Get Loan type info
		$loan_types = $this->db->select("coop_loan_type.id, coop_loan_type.loan_type_code, coop_loan_name.loan_name_id")
								->from("coop_loan_name")
								->join("coop_loan_type", "coop_loan_name.loan_type_id = coop_loan_type.id", "inner")
								->get()->result_array();
		$loanNameIds = array_column($loan_types, 'loan_name_id');

		/*
		$member_groups = $this->db->select(array('t1.mem_group_id'
										,'t1.mem_group_name'
										,'t1.id'
										,'t1.mem_group_parent_id'
										,'t2.member_id'
										,'t2.firstname_th'
										,'t2.lastname_th'
										,'t3.prename_full'
									))
					->from('coop_mem_group as t1')
					->join('(SELECT member_id, firstname_th, lastname_th, prename_id, department, faction, level FROM coop_mem_apply WHERE 1=1 AND member_status <> 3 '.$member_where.') as t2', 't1.id = t2.level', "inner")
					->join('coop_prename as t3', 't2.prename_id = t3.prename_id', "left")
					->order_by('t1.mem_group_parent_id, t1.id, t2.member_id')
					->get()->result_array();
		*/
		$date_start = ($year-543)."-".sprintf("%02d",@$month)."-01";
		$member_groups = $this->db->select(array('t1.mem_group_id'
										,'t1.mem_group_name'
										,'t1.id'
										,'t1.mem_group_parent_id'
										,'t2.member_id'
										,'t2.firstname_th'
										,'t2.lastname_th'
										,'t3.prename_full'
									))
					->from('coop_mem_group as t1')
					->join("(SELECT
									t3.member_id,
									IF(t4.level_old IS NULL, t3.LEVEL, t4.level_old) AS LEVEL,
									t3.firstname_th,
									t3.lastname_th,
									t3.prename_id,
									IF (
										t4.department_old IS NULL,
										t3.department,
										t4.department_old
									) AS department
								FROM
									coop_mem_apply AS t3
							LEFT JOIN (
								SELECT
									member_id,
									department_old,
									faction_old,
									level_old,
									date_move
								FROM
									coop_mem_group_move
								WHERE date_move >= '".$date_start."'
								GROUP BY member_id
								ORDER BY date_move ASC
							) AS t4 ON t3.member_id = t4.member_id
							WHERE 1=1 AND member_status <> 3 ".$member_where.") as t2", "t1.id = t2.level", "inner")
					->join('coop_prename as t3', 't2.prename_id = t3.prename_id', "left")
					->order_by('t1.mem_group_parent_id, t1.id, t2.member_id')
					->get()->result_array();
		$member_ids = array_column($member_groups, 'member_id');

		// echo $this->db->last_query();
		$infoDatas = $this->db->select(array(
												't1.loan_type',
												't1.contract_number',
												't3.member_id',
												't3.deduct_code',
												't3.loan_id',
												't3.loan_atm_id',
												't3.pay_type',
												't3.deposit_account_id',
												't4.contract_number as contract_number_atm',
												"t3.pay_amount as sum_pay_amount"
											))
								->from("(SELECT * FROM coop_finance_month_detail WHERE member_id IN (".implode(',', $member_ids).")) as t3")
								->join("coop_finance_month_profile as t2","t2.profile_month = '".$month."' AND t2.profile_year = '".$year."' AND t3.profile_id = t2.profile_id","inner")
								->join("(SELECT * FROM coop_loan WHERE ".$loan_where.") as t1","t1.id = t3.loan_id", "left")
								->join("coop_loan_atm as t4","t4.loan_atm_id = t3.loan_atm_id", "left")
								->group_by("t3.deduct_code, t3.member_id, t3.pay_type, t3.loan_id, t3.loan_atm_id")
								->order_by("t3.member_id")
								->get()->result_array();
								//"SUM(t3.pay_amount) as sum_pay_amount"

		$info_members = array_column($infoDatas, 'member_id');
		//echo $this->db->last_query(); exit;
		$total_data = array();
		$datas = array();

		foreach($member_groups as $key => $member_group){
			$member_indexs = array_keys($info_members,$member_group['member_id']);
			foreach($member_indexs AS $member_index){
				$datas[$member_group['member_id']]['member_name'] = $member_group['prename_full'].$member_group['firstname_th']." ".$member_group['lastname_th'];
				$datas[$member_group['member_id']]['mem_group_name'] = $member_group['mem_group_name'];
				$infoData = $infoDatas[$member_index];

				if($infoData['deduct_code']=='LOAN'){
					$loan_type_code = $loan_types[array_search($infoData['loan_type'],$loanNameIds)]['loan_type_code'];
					if (empty($datas[$member_group['member_id']][$loan_type_code."_ids"]) || !in_array($infoData['loan_id'], $datas[$member_group['member_id']][$loan_type_code."_ids"])) {
						$datas[$member_group['member_id']][$loan_type_code."_ids"][] = $infoData['loan_id'];
					}
					$datas[$member_group['member_id']][$loan_type_code][$infoData['loan_id']]['contract_number'] = $infoData['contract_number'];
					if($infoData['pay_type']=='principal'){
						$datas[$member_group['member_id']][$loan_type_code][$infoData['loan_id']]['principal'] = $infoData['sum_pay_amount'];
						$total_data[$loan_type_code.'_principal'] += $infoData['sum_pay_amount'];
					} else {
						$datas[$member_group['member_id']][$loan_type_code][$infoData['loan_id']]['interest'] = $infoData['sum_pay_amount'];
						$total_data[$loan_type_code.'_interest'] += $infoData['sum_pay_amount'];
					}
					$datas[$member_group['member_id']]['total'] += $infoData['sum_pay_amount'];
					$total_data['total_amount'] += $infoData['sum_pay_amount'];
				}else if($infoData['deduct_code']=='ATM' && empty($_GET['term_of_loan'])){
					$loan_type_code = "emergent";
					if (empty($datas[$member_group['member_id']][$loan_type_code."_ids"]) || !in_array($infoData['loan_atm_id'], $datas[$member_group['member_id']][$loan_type_code."_ids"])) {
						$datas[$member_group['member_id']][$loan_type_code."_ids"][] = $infoData['loan_atm_id'];
					}
					$datas[$member_group['member_id']][$loan_type_code][$infoData['loan_atm_id']]['contract_number'] = $infoData['contract_number_atm'];
					if($infoData['pay_type']=='principal'){
						$datas[$member_group['member_id']][$loan_type_code][$infoData['loan_atm_id']]['principal'] = $infoData['sum_pay_amount'];
						$total_data[$loan_type_code.'_principal'] += $infoData['sum_pay_amount'];
					} else {
						$datas[$member_group['member_id']][$loan_type_code][$infoData['loan_atm_id']]['interest'] = $infoData['sum_pay_amount'];
						$total_data[$loan_type_code.'_interest'] += $infoData['sum_pay_amount'];
					}
					$datas[$member_group['member_id']]['total'] += $infoData['sum_pay_amount'];
					$total_data['total_amount'] += $infoData['sum_pay_amount'];
				} else if ($infoData['deduct_code']=='SHARE' && empty($_GET['term_of_loan'])) {
					$datas[$member_group['member_id']][$infoData['deduct_code']] = $infoData['sum_pay_amount'];
					$datas[$member_group['member_id']]['total'] += $infoData['sum_pay_amount'];
					$total_data[$infoData['deduct_code']] += $infoData['sum_pay_amount'];
					$total_data['total_amount'] += $infoData['sum_pay_amount'];
				} else if ($infoData['deduct_code']=='DEPOSIT' && empty($_GET['term_of_loan'])) {
					$datas[$member_group['member_id']][$infoData['deduct_code']][] = $infoData['sum_pay_amount'];
					$datas[$member_group['member_id']]['deposit_account_id'][] = $infoData['deposit_account_id'];
					$datas[$member_group['member_id']]['total'] += $infoData['sum_pay_amount'];
					$total_data[$infoData['deduct_code']] += $infoData['sum_pay_amount'];
					$total_data['total_amount'] += $infoData['sum_pay_amount'];
				} else if(empty($_GET['term_of_loan'])){
					$datas[$member_group['member_id']][$infoData['deduct_code']] = $infoData['sum_pay_amount'];
					$datas[$member_group['member_id']]['total'] += $infoData['sum_pay_amount'];
					$total_data[$infoData['deduct_code']] += $infoData['sum_pay_amount'];
					$total_data['total_amount'] += $infoData['sum_pay_amount'];
				}

			}
		}

		$arr_data['datas'] = $datas;
		$arr_data['total_data'] = $total_data;
		$this->preview_libraries->template_preview('report_processor_data/coop_report_charged_person_preview',$arr_data);
	}


	function coop_report_charged_person_excel() {
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

		$this->db->select(array('id','loan_type','loan_type_code'));
		$this->db->from('coop_loan_type');
		$this->db->order_by("order_by");
		$row = $this->db->get()->result_array();
		$arr_data['loan_type'] = $row;

		$arr_data['month_arr'] = array('1'=>'มกราคม','2'=>'กุมภาพันธ์','3'=>'มีนาคม','4'=>'เมษายน','5'=>'พฤษภาคม','6'=>'มิถุนายน','7'=>'กรกฎาคม','8'=>'สิงหาคม','9'=>'กันยายน','10'=>'ตุลาคม','11'=>'พฤศจิกายน','12'=>'ธันวาคม');
		$arr_data['month_short_arr'] = array('1'=>'ม.ค.','2'=>'ก.พ.','3'=>'มี.ค.','4'=>'เม.ย.','5'=>'พ.ค.','6'=>'มิ.ย.','7'=>'ก.ค.','8'=>'ส.ค.','9'=>'ก.ย.','10'=>'ต.ค.','11'=>'พ.ย.','12'=>'ธ.ค.');

		//Set condition from URL
		$member_where = "";
		if (!empty($_GET["mem_type"]) && !in_array("all", $_GET["mem_type"])){
			$member_where .= " AND mem_type_id IN (".implode(',', $_GET["mem_type"]).")";
		}
		if(@$_GET['department']!=''){
			$member_where .= " AND IF (
				t4.department_old IS NULL,
				t3.department,
				t4.department_old
			) = '".$_GET['department']."'";
		}
		if(@$_GET['faction']!=''){
			$member_where .= " AND faction = '".$_GET['faction']."'";
		}
		if(@$_GET['level']!=''){
			$member_where .= " AND level = '".$_GET['level']."'";
		}
		$loan_where = "1=1";
		if ($_GET['term_of_loan']) {
			$loan_where .= " AND t1.loan_type = ".$_GET['term_of_loan'];
		}

		$month = $_GET['month'];
		$year = $_GET['year'];

		//Get Loan type info
		$loan_types = $this->db->select("coop_loan_type.id, coop_loan_type.loan_type_code, coop_loan_name.loan_name_id")
								->from("coop_loan_name")
								->join("coop_loan_type", "coop_loan_name.loan_type_id = coop_loan_type.id", "inner")
								->get()->result_array();
		$loanNameIds = array_column($loan_types, 'loan_name_id');

		/*
		$member_groups = $this->db->select(array('t1.mem_group_id'
										,'t1.mem_group_name'
										,'t1.id'
										,'t1.mem_group_parent_id'
										,'t2.member_id'
										,'t2.firstname_th'
										,'t2.lastname_th'
										,'t3.prename_full'
									))
					->from('coop_mem_group as t1')
					->join('(SELECT member_id, level, firstname_th, lastname_th, prename_id  FROM coop_mem_apply WHERE 1=1 '.$member_where.') as t2', 't1.id = t2.level', "inner")
					->join('coop_prename as t3', 't2.prename_id = t3.prename_id', "left")
					->order_by('t1.mem_group_parent_id, t1.id, t2.member_id')
					->get()->result_array();
		*/
		$date_start = ($year-543)."-".sprintf("%02d",@$month)."-01";
		$member_groups = $this->db->select(array('t1.mem_group_id'
										,'t1.mem_group_name'
										,'t1.id'
										,'t1.mem_group_parent_id'
										,'t2.member_id'
										,'t2.firstname_th'
										,'t2.lastname_th'
										,'t3.prename_full'
									))
					->from('coop_mem_group as t1')
					->join("(SELECT
									t3.member_id,
									IF(t4.level_old IS NULL, t3.LEVEL, t4.level_old) AS LEVEL,
									t3.firstname_th,
									t3.lastname_th,
									t3.prename_id,
									IF (
										t4.department_old IS NULL,
										t3.department,
										t4.department_old
									) AS department
								FROM
									coop_mem_apply AS t3
							LEFT JOIN (
								SELECT
									member_id,
									department_old,
									faction_old,
									level_old,
									date_move
								FROM
									coop_mem_group_move
								WHERE date_move >= '".$date_start."'
								GROUP BY member_id
								ORDER BY date_move ASC
							) AS t4 ON t3.member_id = t4.member_id
							WHERE 1=1 AND member_status <> 3 ".$member_where.") as t2", "t1.id = t2.level", "inner")
					->join('coop_prename as t3', 't2.prename_id = t3.prename_id', "left")
					->order_by('t1.mem_group_parent_id, t1.id, t2.member_id')
					->get()->result_array();

		$member_ids = array_column($member_groups, 'member_id');

//echo $this->db->last_query();
//echo '<br>';
//exit;
		$infoDatas = $this->db->select(array(
												't1.loan_type',
												't1.contract_number',
												't3.member_id',
												't3.deduct_code',
												't3.loan_id',
												't3.loan_atm_id',
												't3.pay_type',
												't3.deposit_account_id',
												't4.contract_number as contract_number_atm',
												"t3.pay_amount as sum_pay_amount"
											))
								->from("(SELECT * FROM coop_finance_month_detail WHERE member_id IN (".implode(',', $member_ids).")) as t3")
								->join("coop_finance_month_profile as t2","t2.profile_month = '".$month."' AND t2.profile_year = '".$year."' AND t3.profile_id = t2.profile_id","inner")
								->join("(SELECT * FROM coop_loan WHERE ".$loan_where.") as t1","t1.id = t3.loan_id", "left")
								->join("coop_loan_atm as t4","t4.loan_atm_id = t3.loan_atm_id", "left")
								->group_by("t3.deduct_code, t3.member_id, t3.pay_type, t3.loan_id, t3.loan_atm_id")
								->order_by("t3.member_id")
								->get()->result_array();
								//"SUM(t3.pay_amount) as sum_pay_amount"
		//echo $this->db->last_query();exit;
		$info_members = array_column($infoDatas, 'member_id');

		$total_data = array();
		$datas = array();
		foreach($member_groups as $key => $member_group){
			$member_indexs = array_keys($info_members,$member_group['member_id']);
			foreach($member_indexs AS $member_index){
				$datas[$member_group['member_id']]['member_name'] = $member_group['prename_full'].$member_group['firstname_th']." ".$member_group['lastname_th'];
				$datas[$member_group['member_id']]['mem_group_name'] = $member_group['mem_group_name'];
				$infoData = $infoDatas[$member_index];
				if($infoData['deduct_code']=='LOAN'){
					$loan_type_code = $loan_types[array_search($infoData['loan_type'],$loanNameIds)]['loan_type_code'];
					if (empty($datas[$member_group['member_id']][$loan_type_code."_ids"]) || !in_array($infoData['loan_id'], $datas[$member_group['member_id']][$loan_type_code."_ids"])) {
						$datas[$member_group['member_id']][$loan_type_code."_ids"][] = $infoData['loan_id'];
					}
					$datas[$member_group['member_id']][$loan_type_code][$infoData['loan_id']]['contract_number'] = $infoData['contract_number'];
					if($infoData['pay_type']=='principal'){
						$datas[$member_group['member_id']][$loan_type_code][$infoData['loan_id']]['principal'] = $infoData['sum_pay_amount'];
						$total_data[$loan_type_code.'_principal'] += $infoData['sum_pay_amount'];
					} else {
						$datas[$member_group['member_id']][$loan_type_code][$infoData['loan_id']]['interest'] = $infoData['sum_pay_amount'];
						$total_data[$loan_type_code.'_interest'] += $infoData['sum_pay_amount'];
					}
					$datas[$member_group['member_id']]['total'] += $infoData['sum_pay_amount'];
					$total_data['total_amount'] += $infoData['sum_pay_amount'];
				}else if($infoData['deduct_code']=='ATM' && empty($_GET['term_of_loan'])){
					$loan_type_code = "emergent";
					if (empty($datas[$member_group['member_id']][$loan_type_code."_ids"]) || !in_array($infoData['loan_atm_id'], $datas[$member_group['member_id']][$loan_type_code."_ids"])) {
						$datas[$member_group['member_id']][$loan_type_code."_ids"][] = $infoData['loan_atm_id'];
					}
					$datas[$member_group['member_id']][$loan_type_code][$infoData['loan_atm_id']]['contract_number'] = $infoData['contract_number_atm'];
					if($infoData['pay_type']=='principal'){
						$datas[$member_group['member_id']][$loan_type_code][$infoData['loan_atm_id']]['principal'] = $infoData['sum_pay_amount'];
						$total_data[$loan_type_code.'_principal'] += $infoData['sum_pay_amount'];
					} else {
						$datas[$member_group['member_id']][$loan_type_code][$infoData['loan_atm_id']]['interest'] = $infoData['sum_pay_amount'];
						$total_data[$loan_type_code.'_interest'] += $infoData['sum_pay_amount'];
					}
					$datas[$member_group['member_id']]['total'] += $infoData['sum_pay_amount'];
					$total_data['total_amount'] += $infoData['sum_pay_amount'];
				} else if ($infoData['deduct_code']=='SHARE' && empty($_GET['term_of_loan'])) {
					$datas[$member_group['member_id']][$infoData['deduct_code']] = $infoData['sum_pay_amount'];
					$datas[$member_group['member_id']]['total'] += $infoData['sum_pay_amount'];
					$total_data[$infoData['deduct_code']] += $infoData['sum_pay_amount'];
					$total_data['total_amount'] += $infoData['sum_pay_amount'];
				} else if ($infoData['deduct_code']=='DEPOSIT' && empty($_GET['term_of_loan'])) {
					$datas[$member_group['member_id']][$infoData['deduct_code']][] = $infoData['sum_pay_amount'];
					$datas[$member_group['member_id']]['deposit_account_id'][] = $infoData['deposit_account_id'];
					$datas[$member_group['member_id']]['total'] += $infoData['sum_pay_amount'];
					$total_data[$infoData['deduct_code']] += $infoData['sum_pay_amount'];
					$total_data['total_amount'] += $infoData['sum_pay_amount'];
				} else if(empty($_GET['term_of_loan'])){
					$datas[$member_group['member_id']][$infoData['deduct_code']] = $infoData['sum_pay_amount'];
					$datas[$member_group['member_id']]['total'] += $infoData['sum_pay_amount'];
					$total_data[$infoData['deduct_code']] += $infoData['sum_pay_amount'];
					$total_data['total_amount'] += $infoData['sum_pay_amount'];
				}
			}
		}
		//exit;
		$arr_data['datas'] = $datas;
		$arr_data['total_data'] = $total_data;
		$this->load->view('report_processor_data/coop_report_charged_person_excel',$arr_data);
	}

	public function coop_report_charged_level(){
		$arr_data = array();

		$this->db->select(array('id','mem_group_name'));
		$this->db->from('coop_mem_group');
		$this->db->where("mem_group_type = '1'");
		$row = $this->db->get()->result_array();
		$arr_data['row_mem_group'] = $row;

		$this->libraries->template('report_processor_data/coop_report_charged_level',$arr_data);
	}

	function coop_report_charged_level_preview(){
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

		$this->db->select(array('id','loan_type'));
		$this->db->from('coop_loan_type');
		$this->db->order_by("order_by");
		$row = $this->db->get()->result_array();
		$arr_data['loan_type'] = $row;

		$arr_data['month_arr'] = array('1'=>'มกราคม','2'=>'กุมภาพันธ์','3'=>'มีนาคม','4'=>'เมษายน','5'=>'พฤษภาคม','6'=>'มิถุนายน','7'=>'กรกฎาคม','8'=>'สิงหาคม','9'=>'กันยายน','10'=>'ตุลาคม','11'=>'พฤศจิกายน','12'=>'ธันวาคม');
		$arr_data['month_short_arr'] = array('1'=>'ม.ค.','2'=>'ก.พ.','3'=>'มี.ค.','4'=>'เม.ย.','5'=>'พ.ค.','6'=>'มิ.ย.','7'=>'ก.ค.','8'=>'ส.ค.','9'=>'ก.ย.','10'=>'ต.ค.','11'=>'พ.ย.','12'=>'ธ.ค.');

		$this->preview_libraries->template_preview('report_processor_data/coop_report_charged_level_preview',$arr_data);
	}

	public function coop_report_send_deduction(){
		$arr_data = array();

		$this->db->select(array('id','mem_group_name'));
		$this->db->from('coop_mem_group');
		$this->db->where("mem_group_type = '1'");
		$row = $this->db->get()->result_array();
		$arr_data['row_mem_group'] = $row;

		//Get Member Type
		$this->db->select('mem_type_id, mem_type_name');
		$this->db->from('coop_mem_type');
		$row = $this->db->get()->result_array();
		$arr_data['mem_type'] = $row;

		$this->libraries->template('report_processor_data/coop_report_send_deduction',$arr_data);
	}

	function coop_report_send_deduction_preview_backup_26_Oct_2018(){
		set_time_limit ( 180 );
		//$this->db->save_queries = FALSE;
		$arr_data = array();

		$this->db->select(array('id','loan_type'));
		$this->db->from('coop_loan_type');
		$this->db->order_by("order_by");
		$row = $this->db->get()->result_array();
		$arr_data['loan_type'] = $row;

		$month_arr = $this->center_function->month_arr();
		if(@$_GET['month']!='' && @$_GET['year']!=''){
			$day = '';
			$month = @$_GET['month'];
			$year = (@$_GET['year']-543);
			$title_date = " เดือน ".@$month_arr[$month]." ปี ".(@$year+543);
		}else{
			$day = '';
			$month = '';
			$year = (@$_GET['year']-543);
			$title_date = " ปี ".(@$year+543);
		}
		$arr_data['title_date'] = $title_date;

		$this->db->select(array('profile_id'));
		$this->db->from('coop_finance_month_profile');
		$this->db->where("profile_month = '".$month."' AND profile_year = '".($year+543)."'");
		$row = $this->db->get()->result_array();
		$profile_id = @$row[0]['profile_id'];

		//Declare condition
		$where_group = "";
		if (@$_GET['search_type'] == 'id') {
			//รูปแบบหน่วยงาน
			if(@$_GET['level'] != ''){
				$where_group .= " AND t1.id = '".$_GET['level']."'";
			}else if(@$_GET['faction'] != ''){
				$where_group .= " AND t2.id = '".$_GET['faction']."'";
			}else if(@$_GET['department'] != ''){
				$where_group .= " AND t3.id = '".$_GET['department']."'";
			}
		} else if ($_GET['search_type'] == "code") {
			if (!empty($_GET['department_id_from'])) $where_group .= " AND CAST(t3.mem_group_id AS INTEGER) >= CAST(".$_GET['department_id_from']." AS INTEGER)";
			if (!empty($_GET['department_id_to'])) $where_group .= " AND CAST(t3.mem_group_id AS INTEGER) <= CAST(".$_GET['department_id_to']." AS INTEGER)";
		}

		$member_where = "";
		if (!empty($_GET["mem_type"])){
			if (is_array($_GET["mem_type"]) && !in_array("all", $_GET["mem_type"])){
				$member_where .= " AND coop_mem_apply.mem_type_id IN (".implode(',', $_GET["mem_type"]).")";
			} else if(!is_array($_GET["mem_type"]) && strpos($_GET["mem_type"], "all") === false){
				$member_where .= " AND coop_mem_apply.mem_type_id IN ".str_replace(']',')',str_replace('[','(',$_GET["mem_type"]));
			}
		}

		$department_order = "t1.id";
		if(!empty($_GET['department_sort'])) {
			if($_GET['department_sort'] == '1') $department_order = "t1.id";
			else if($_GET['department_sort'] == '2') $department_order = "t1.mem_group_name, t1.id";
			else if($_GET['department_sort'] == '3') $department_order = "t2.id, t1.id";
			else if($_GET['department_sort'] == '4') $department_order = "t2.mem_group_name, t1.id";
			else if($_GET['department_sort'] == '5') $department_order = "t3.id, t1.id";
			else if($_GET['department_sort'] == '6') $department_order = "t3.mem_group_name, t1.id";
		}
		$this->db->select(array(
			't1.id',
			't1.mem_group_id',
			't1.mem_group_name as level_name',
			't2.mem_group_name as faction_name',
			't3.mem_group_name as department_name'
		));
		$this->db->from('coop_mem_group as t1');
		$this->db->join('coop_mem_group as t2','t1.mem_group_parent_id = t2.id','inner');
		$this->db->join('coop_mem_group as t3','t2.mem_group_parent_id = t3.id','inner');
		$this->db->where("t1.mem_group_type = '3'".$where_group);
		$this->db->order_by($department_order);
		$row_mem_group = $this->db->get()->result_array();

		//echo $this->db->last_query();exit;
		$row_data = array();
		$total_data = array();
		$page_all_arr = array();

		$member_data = array();
		$mem_group_names = array();

		$page_get = !empty($_GET['page']) ? $_GET['page'] : 1;
		$all_page = 0;
		foreach($row_mem_group as $key_mem_group => $value_mem_group){
			$row_data[$value_mem_group['id']]['mem_group_name'] = $value_mem_group['department_name'].":".$value_mem_group['faction_name'].":".$value_mem_group['level_name']."(".$value_mem_group['mem_group_id'].")";
			$mem_group_names[$value_mem_group['id']] = $value_mem_group['department_name'].":".$value_mem_group['faction_name'].":".$value_mem_group['level_name']."(".$value_mem_group['mem_group_id'].")";
			$where = " AND level = '".$value_mem_group['id']."'";

			$this->db->select(array('coop_mem_apply.member_id'));
			$this->db->from('coop_mem_apply');
			$this->db->where("coop_mem_apply.mem_type = '1' {$where}".$member_where);
			$rs_count = $this->db->get()->result_array();

			$num_rows = count($rs_count);
			$page_limit = 100;
			$page_all = @ceil($num_rows/$page_limit);
			$page_all_arr[$value_mem_group['id']] = $page_all;

			$count_group_page = 0;
			$runno = 0;
			for($page = 1;$page<=$page_all;$page++){
				$all_page++;

				if($all_page == $page_get) {
					$count_member = $count_group_page * $page_limit;
					$page_start = (($page-1)*$page_limit);
					$per_page = $page*$page_limit;

					//Page Data
					$member_sort = "coop_mem_apply.member_id ASC";
					if(!empty($_GET['member_sort'])) {
						if($_GET['member_sort'] == "1") $member_sort = "coop_mem_apply.member_id ASC";
						else if($_GET['member_sort'] == "2") $member_sort = "coop_mem_apply.id_card ASC";
						else if($_GET['member_sort'] == "3") $member_sort = "coop_mem_apply.firstname_th ASC";
						else if($_GET['member_sort'] == "4") $member_sort = "coop_mem_apply.lastname_th ASC";
					}
					$this->db->select(array('coop_mem_apply.member_id','coop_mem_apply.prename_id','coop_mem_apply.firstname_th','coop_mem_apply.lastname_th','coop_prename.prename_short'));
					$this->db->from('(SELECT * FROM coop_mem_apply where mem_type = "1" and level = "'.$value_mem_group['id'].'" LIMIT '.$page_start.', '.$page_limit.') as coop_mem_apply');
					$this->db->join("coop_prename","coop_mem_apply.prename_id = coop_prename.prename_id","left");
					$this->db->where("1=1".$member_where);
					$this->db->order_by($member_sort);
					$rs = $this->db->get()->result_array();

					if(!empty($rs)){
						foreach(@$rs as $key => $row){
							$runno++;
							$full_name = @$row['prename_short'].@$row['firstname_th'].' '.@$row['lastname_th'];
							$row_data[$value_mem_group['id']]['data'][$page][$row['member_id']]['runno'] = $runno;
							$row_data[$value_mem_group['id']]['data'][$page][$row['member_id']]['full_name'] = $full_name;
							$this->db->select(array(
								't1.*',
								't2.contract_number',
								't2.loan_amount_balance',
								't2.period_now',
								't3.loan_type_id',
								't4.contract_number AS contract_number_atm'
							));

							$this->db->from('(SELECT loan_id, deduct_code, pay_type, pay_amount, deposit_account_id,loan_atm_id FROM coop_finance_month_detail WHERE member_id = "'.$row['member_id'].'" AND profile_id = "'.$profile_id.'") as t1');
							$this->db->join("coop_loan as t2","t2.id = t1.loan_id","left");
							$this->db->join("coop_loan_name as t3","t3.loan_name_id = t2.loan_type","left");
							$this->db->join("coop_loan_atm as t4","t4.loan_atm_id = t1.loan_atm_id","left");
							$rs_month_detail = $this->db->get()->result_array();
							//echo $this->db->last_query();

							foreach($rs_month_detail as $key_month_detail => $value_month_detail){
								if($value_month_detail['deduct_code'] == 'LOAN'){
									$this->db->select(array('t2.loan_amount_balance','t2.period_count'));
									$this->db->from('coop_receipt as t1');
									$this->db->join("coop_finance_transaction as t2","t1.receipt_id = t2.receipt_id","inner");
									$this->db->where("t1.member_id = '".$row['member_id']."' AND t1.finance_month_profile_id = '".$profile_id."' AND t2.principal_payment > 0 AND loan_id = '".$value_month_detail['loan_id']."'");
									$rs_receipt = $this->db->get()->result_array();

									$receipt_balance = @$rs_receipt[0]['loan_amount_balance'];
									$receipt_period = @$rs_receipt[0]['period_count'];

									@$row_data[$value_mem_group['id']]['data'][$page][$row['member_id']][$value_month_detail['deduct_code']][$value_month_detail['loan_type_id']]['contract_number'] = $value_month_detail['contract_number'];
									@$row_data[$value_mem_group['id']]['data'][$page][$row['member_id']][$value_month_detail['deduct_code']][$value_month_detail['loan_type_id']][$value_month_detail['pay_type']] = $value_month_detail['pay_amount'];
									@$row_data[$value_mem_group['id']]['data'][$page][$row['member_id']][$value_month_detail['deduct_code']][$value_month_detail['loan_type_id']]['balance'] = $receipt_balance!=''?$receipt_balance:$value_month_detail['loan_amount_balance'];
									@$row_data[$value_mem_group['id']]['data'][$page][$row['member_id']][$value_month_detail['deduct_code']][$value_month_detail['loan_type_id']]['period'] = $receipt_period!=''?$receipt_period:($value_month_detail['period_now']+1);
								}else if($value_month_detail['deduct_code'] == 'ATM'){
									$loan_type_id_atm = '7';
									$this->db->select(array('t2.loan_amount_balance','t2.period_count'));
									$this->db->from('coop_receipt as t1');
									$this->db->join("coop_finance_transaction as t2","t1.receipt_id = t2.receipt_id","inner");
									$this->db->where("t1.member_id = '".$row['member_id']."' AND t1.finance_month_profile_id = '".$profile_id."' AND t2.principal_payment > 0 AND loan_atm_id = '".$value_month_detail['loan_atm_id']."'");
									$rs_receipt = $this->db->get()->result_array();

									$receipt_balance = @$rs_receipt[0]['loan_amount_balance'];
									$receipt_period = @$rs_receipt[0]['period_count'];

									$row_data[$value_mem_group['id']]['data'][$page][$row['member_id']]['LOAN'][$loan_type_id_atm]['contract_number'] = $value_month_detail['contract_number_atm'];
									@$row_data[$value_mem_group['id']]['data'][$page][$row['member_id']]['LOAN'][$loan_type_id_atm][$value_month_detail['pay_type']] = $value_month_detail['pay_amount'];
									@$row_data[$value_mem_group['id']]['data'][$page][$row['member_id']]['LOAN'][$loan_type_id_atm]['balance'] = $receipt_balance!=''?$receipt_balance:$value_month_detail['loan_amount_balance'];
									@$row_data[$value_mem_group['id']]['data'][$page][$row['member_id']]['LOAN'][$loan_type_id_atm]['period'] = $receipt_period!=''?$receipt_period:($value_month_detail['period_now']+1);
								}else if($value_month_detail['deduct_code'] == 'DEPOSIT'){
									@$row_data[$value_mem_group['id']]['data'][$page][$row['member_id']][$value_month_detail['deduct_code']]['pay_amount'] = $value_month_detail['pay_amount'];
									@$row_data[$value_mem_group['id']]['data'][$page][$row['member_id']][$value_month_detail['deduct_code']]['account_id'] = $value_month_detail['deposit_account_id'];
								}else{
									@$row_data[$value_mem_group['id']]['data'][$page][$row['member_id']][$value_month_detail['deduct_code']] += $value_month_detail['pay_amount'];
								}
								@$row_data[$value_mem_group['id']]['data'][$page][$row['member_id']]['sum_all'] += $value_month_detail['pay_amount'];
							}
						}
					}

					//รวมคน
					$sum_person = $this->db->select(array(
						'count(coop_mem_apply.member_id) AS sum_person'
					))
					->from('coop_mem_apply')
					->where(" mem_type = '1' AND level = '".$value_mem_group['id']."'")
					->limit(1)
					->get()->result_array();
					@$total_data[$value_mem_group['id']]['total']['count_member'] = @$sum_person[0]['sum_person'];

					//ผลรวม
					$sum_month_detail = $this->db->select(array(
						'SUM(t1.pay_amount) AS pay_amount',
						't5.loan_type_code',
						't5.id',
						't1.pay_type',
						't1.deduct_code'
					))
					->from('coop_finance_month_detail AS t1')
					->join("coop_mem_apply AS t2","t1.member_id = t2.member_id","left")
					->join("coop_loan AS t3","t1.loan_id = t3.id","left")
					->join("coop_loan_name AS t4","t3.loan_type = t4.loan_name_id","left")
					->join("coop_loan_type AS t5","t4.loan_type_id = t5.id","left")
					->where(" t2.level = '".$value_mem_group['id']."'AND  t1.profile_id = '".$profile_id."'")
					->group_by("t1.deduct_code,t5.loan_type_code,t1.pay_type")
					->get()->result_array();

					foreach($sum_month_detail AS $key=>$value){
						if($value['deduct_code'] == 'LOAN'){
							@$total_data[$value_mem_group['id']]['total'][$value['deduct_code']][$value['id']][$value['pay_type']] = @$value['pay_amount'];
						}else if($value['deduct_code'] == 'ATM'){
							@$total_data[$value_mem_group['id']]['total']['LOAN']['7'][$value['pay_type']] += @$value['pay_amount'];
						}else{
							@$total_data[$value_mem_group['id']]['total'][$value['deduct_code']] = @$value['pay_amount'];
						}

						@$total_data[$value_mem_group['id']]['total']['sum_all'] += @$value['pay_amount'];
					}

					//ผลรวมเลขที่สัญญา
					$sum_contract = $this->db->select(array(
						't5.loan_type_code',
						't5.id',
						't1.deduct_code',
						't1.loan_id',
						't1.loan_atm_id'
					))
					->from('coop_finance_month_detail AS t1')
					->join("coop_mem_apply AS t2","t1.member_id = t2.member_id","left")
					->join("coop_loan AS t3","t1.loan_id = t3.id","left")
					->join("coop_loan_name AS t4","t3.loan_type = t4.loan_name_id","left")
					->join("coop_loan_type AS t5","t4.loan_type_id = t5.id","left")
					->where(" t2.level = '".$value_mem_group['id']."'AND  t1.profile_id = '".$profile_id."' AND t1.deduct_code IN('LOAN','ATM')")
					->group_by("t1.deduct_code,t5.loan_type_code,t1.loan_id,t1.loan_atm_id")
					->get()->result_array();

					foreach($sum_contract AS $key_contract=>$value_contract){
						if($value_contract['deduct_code'] == 'LOAN'){
							@$total_data[$value_mem_group['id']]['total'][$value_contract['deduct_code']][$value_contract['id']]['count_contract_number']++;
						}else if($value_contract['deduct_code'] == 'ATM'){
							@$total_data[$value_mem_group['id']]['total']['LOAN']['7']['count_contract_number']++;
						}
					}
					//echo '<pre>'; print_r($total_data); echo '</pre>';
				}
				$runno += $page_limit;
				$count_group_page++;
			}
		}

		$paging = $this->pagination_center->paginating(intval($page_get), $all_page, 1, 20,$_GET);//$page_now = 1, $row_total = 1, $per_page = 20, $page_limit = 20

		$arr_data['row_data'] = $row_data;
		$arr_data['total_data'] = $total_data;
		$arr_data['page_all_arr'] = $page_all_arr;
		$arr_data['paging'] = $paging;
		$arr_data['page_get'] = $page_get;
		$arr_data['all_page'] = $all_page;
		$this->preview_libraries->template_preview('report_processor_data/coop_report_send_deduction_preview',$arr_data);
	}


	function coop_report_send_deduction_preview(){
		set_time_limit ( 180 );
		$this->db->save_queries = FALSE;
		$arr_data = array();

		$row = $this->db->select(array('id','loan_type'))
						->from('coop_loan_type')
						->order_by("order_by")
						->get()->result_array();
		$arr_data['loan_type'] = $row;

		$month_arr = $this->center_function->month_arr();
		if($_GET['month']!='' && $_GET['year']!=''){
			$day = '';
			$month = $_GET['month'];
			$year = ($_GET['year']-543);
			$title_date = " เดือน ".$month_arr[$month]." ปี ".($year+543);
		}else{
			$day = '';
			$month = '';
			$year = ($_GET['year']-543);
			$title_date = " ปี ".($year+543);
		}
		$arr_data['title_date'] = $title_date;

		$row = $this->db->select(array('profile_id'))
						->from('coop_finance_month_profile')
						->where("profile_month = '".$month."' AND profile_year = '".($year+543)."'")
						->get()->result_array();
		$profile_id = $row[0]['profile_id'];

		//Declare condition
		$where_group = "";
		if ($_GET['search_type'] == 'id') {
			if(!empty($_GET['level'])){
				$where_group .= " AND t1.id = '".$_GET['level']."'";
			}else if(!empty($_GET['faction'])){
				$where_group .= " AND t2.id = '".$_GET['faction']."'";
			}else if(!empty($_GET['department'])){
				$where_group .= " AND t3.id = '".$_GET['department']."'";
			}
		} else if ($_GET['search_type'] == "code") {
			if (!empty($_GET['department_id_from'])) $where_group .= " AND CAST(t3.mem_group_id AS INTEGER) >= CAST(".$_GET['department_id_from']." AS INTEGER)";
			if (!empty($_GET['department_id_to'])) $where_group .= " AND CAST(t3.mem_group_id AS INTEGER) <= CAST(".$_GET['department_id_to']." AS INTEGER)";
		}

		$member_where = "";
		if (!empty($_GET["mem_type"])){
			if (is_array($_GET["mem_type"]) && !in_array("all", $_GET["mem_type"])){
				$member_where .= " AND t4.mem_type_id IN (".implode(',', $_GET["mem_type"]).")";
			} else if(!is_array($_GET["mem_type"]) && strpos($_GET["mem_type"], "all") === false){
				$member_where .= " AND t4.mem_type_id IN ".str_replace(']',')',str_replace('[','(',$_GET["mem_type"]));
			}
		}

		$department_order = "t1.id";
		if(!empty($_GET['department_sort'])) {
			if($_GET['department_sort'] == '1') $department_order = "t1.id";
			else if($_GET['department_sort'] == '2') $department_order = "t1.mem_group_name, t1.id";
			else if($_GET['department_sort'] == '3') $department_order = "t2.id, t1.id";
			else if($_GET['department_sort'] == '4') $department_order = "t2.mem_group_name, t1.id";
			else if($_GET['department_sort'] == '5') $department_order = "t3.id, t1.id";
			else if($_GET['department_sort'] == '6') $department_order = "t3.mem_group_name, t1.id";
		}
		$member_sort = "t4.member_id ASC";
		if(!empty($_GET['member_sort'])) {
			if($_GET['member_sort'] == "1") $member_sort = "t4.member_id ASC";
			else if($_GET['member_sort'] == "2") $member_sort = "t4.id_card ASC";
			else if($_GET['member_sort'] == "3") $member_sort = "t4.firstname_th ASC";
			else if($_GET['member_sort'] == "4") $member_sort = "t4.lastname_th ASC";
		}
		// $row_mem_group = $this->db->select(array(
		// 										't1.id',
		// 										't1.mem_group_id',
		// 										't1.mem_group_name as level_name',
		// 										't2.mem_group_name as faction_name',
		// 										't3.mem_group_name as department_name',
		// 										't4.member_id',
		// 										't4.prename_id',
		// 										't4.firstname_th',
		// 										't4.lastname_th',
		// 										't5.prename_short'
		// 									))
		// 							->from('coop_mem_group as t1')
		// 							->join('coop_mem_group as t2','t1.mem_group_parent_id = t2.id','inner')
		// 							->join('coop_mem_group as t3','t2.mem_group_parent_id = t3.id','inner')
		// 							->join('coop_mem_apply as t4','t1.id = t4.level','inner')
		// 							->join("coop_prename as t5","t4.prename_id = t5.prename_id","left")
		// 							->where("t1.mem_group_type = '3'".$where_group.$member_where)
		// 							->order_by($department_order.",".$member_sort)
		// 							->get()->result_array();

		$date_range['start'] 	= date("Y-m", strtotime($year."-".$month."-01")	) . "-01";
		$date_range['end'] 		= date("Y-m-t", strtotime($year."-".$month."-01")	)." 23:59:59";
		$row_mem_group = $this->db->select(array(
												't1.id',
												't1.mem_group_id',
												't1.mem_group_name as level_name',
												't2.mem_group_name as faction_name',
												't3.mem_group_name as department_name',
												't4.member_id',
												't4.prename_id',
												't4.firstname_th',
												't4.lastname_th',
												't4.id_card',
												't5.prename_short'
											))
									->from('coop_mem_apply as t4')
									->join('coop_mem_group as t1','t1.id = IF( (select level_old from coop_mem_group_move where date_move >= "'.$date_range['start'].'" and coop_mem_group_move.member_id = t4.member_id ORDER BY date_move ASC LIMIT 1),
									(select level_old from coop_mem_group_move where date_move >= "'.$date_range['start'].'" and coop_mem_group_move.member_id = t4.member_id ORDER BY date_move ASC LIMIT 1),
									t4.`level`)','left', false)
									->join('coop_mem_group as t2','t1.mem_group_parent_id = t2.id','left')
									->join('coop_mem_group as t3','t2.mem_group_parent_id = t3.id','left')
									->join("coop_prename as t5","t4.prename_id = t5.prename_id","left")
									->where("(select sum(pay_amount) from coop_finance_month_detail where profile_id = $profile_id and member_id = t4.member_id group by profile_id,member_id LIMIT 1)  > 0 ".$where_group.$member_where)
									->order_by($department_order.",".$member_sort)
									->get()->result_array();

		$mem_group_ids = array_column($row_mem_group, 'id');
		$member_ids = array_column($row_mem_group, 'member_id');

		$where = "";
		if(sizeof($member_ids)!=0){
			$where .= "AND member_id IN (".implode(',', $member_ids).")";
		}else{
			echo "ไม่มีข้อมูลที่เลือก";
		}
		$finance_month_details = $this->db->query("SELECT * FROM coop_finance_month_detail WHERE profile_id = '{$profile_id}' $where")
											->result_array();
		$finance_member_ids = array_column($finance_month_details, 'member_id');

		$loan_ids = array_filter(array_column($finance_month_details, 'loan_id'));
		if(!empty($loan_ids)) {
			$loans = $this->db->query("SELECT t1.id, t1.contract_number, t1.id, t2.loan_type_id  FROM coop_loan as t1
										LEFT JOIN coop_loan_name as t2 ON t2.loan_name_id = t1.loan_type
										WHERE id IN (".implode(',', $loan_ids).") AND t1.loan_status NOT IN ('5')")
								->result_array();
			$loan_members = array_column($loans, 'id');

			$receipts = $this->db->query("SELECT t2.loan_id, t2.period_count FROM coop_receipt as t1
											INNER JOIN coop_finance_transaction as t2 ON t1.receipt_id = t2.receipt_id
											WHERE t2.loan_id IN (".implode(',', $loan_ids).") AND t1.finance_month_profile_id = (select profile_id from coop_finance_month_profile where profile_id < $profile_id order by profile_id desc limit 1)")
											->result_array();
			$loan_receipt_members = array_column($receipts, 'loan_id');
		}

		$loan_atm_ids = array_filter(array_column($finance_month_details, 'loan_atm_id'));
		if(!empty($loan_atm_ids)) {
			$loan_atms = $this->db->query("SELECT t1.loan_atm_id, t1.contract_number, t1.loan_atm_id  FROM coop_loan_atm as t1
										WHERE loan_atm_id IN (".implode(',', $loan_atm_ids).") AND t1.loan_atm_status NOT IN ('5')")
								->result_array();
			$loan_atm_members = array_column($loan_atms, 'loan_atm_id');

			$receipt_atms = $this->db->query("SELECT t2.loan_atm_id, t2.period_count FROM coop_receipt as t1
											INNER JOIN coop_finance_transaction as t2 ON t1.receipt_id = t2.receipt_id
											WHERE t2.loan_atm_id IN (".implode(',', $loan_atm_ids).") AND t1.finance_month_profile_id = (select profile_id from coop_finance_month_profile where profile_id < $profile_id order by profile_id desc limit 1) AND t2.principal_payment > 0")
									->result_array();
			$loan_atm_receipt_members = array_column($receipt_atms, 'loan_atm_id');
		}

		$datas = array();
		$total_data = array();
		$last_member_id = null;
		foreach($row_mem_group as $mem_group) {
			$datas[$mem_group['id']]['mem_group_name'] = !empty($mem_group['level_name']) ? $mem_group['department_name'].":".$mem_group['faction_name'].":".$mem_group['level_name']."(".$mem_group['mem_group_id'].")" : "";
			$datas[$mem_group['id']]['member'][$mem_group['member_id']]['member_id'] = $mem_group['member_id'];
			$datas[$mem_group['id']]['member'][$mem_group['member_id']]['name'] = $mem_group['prename_short'].$mem_group['firstname_th']." ".$mem_group['lastname_th'];

			$finance_indexs = array_keys($finance_member_ids,$mem_group['member_id']);

			foreach($finance_indexs as $finance_index) {
				$finance_month_detail = $finance_month_details[$finance_index];
				if($finance_month_detail["deduct_code"] == "LOAN") {
					if (in_array($finance_month_detail['loan_id'], $loan_members)) {
						$loan = $loans[array_search($finance_month_detail['loan_id'], $loan_members)];
						$receipt = $receipts[array_search($finance_month_detail['loan_id'], $loan_receipt_members)];
						$datas[$mem_group['id']]['member'][$mem_group['member_id']][$loan['loan_type_id']][$finance_month_detail['loan_id']]['contract_number'] = $loan["contract_number"];
						$datas[$mem_group['id']]['member'][$mem_group['member_id']][$loan['loan_type_id']][$finance_month_detail['loan_id']][$finance_month_detail["pay_type"]] = $finance_month_detail['pay_amount'];
						$datas[$mem_group['id']]['member'][$mem_group['member_id']][$loan['loan_type_id']][$finance_month_detail['loan_id']]['period'] = !empty($receipt) ? $receipt["period_count"] : ($finance_month_detail["period_now"] + 1);
						$datas[$mem_group['id']]['last_member'] = $mem_group['member_id'];
						$last_member_id = $mem_group['member_id'];
						$datas[$mem_group['id']]['member'][$mem_group['member_id']]['sum_all'] += $finance_month_detail['pay_amount'];
					}
				}else if($finance_month_detail['deduct_code'] == 'ATM'){
					if (in_array($finance_month_detail['loan_atm_id'], $loan_atm_members)) {
						$loan_type_id_atm = '7';
						$loan = $loan_atms[array_search($finance_month_detail['loan_atm_id'], $loan_atm_members)];
						$receipt = $receipt_atms[array_search($finance_month_detail['loan_atm_id'], $loan_atm_receipt_members)];
						$datas[$mem_group['id']]['member'][$mem_group['member_id']][$loan_type_id_atm][$finance_month_detail['loan_atm_id']]['contract_number'] = $loan["contract_number"];
						$datas[$mem_group['id']]['member'][$mem_group['member_id']][$loan_type_id_atm][$finance_month_detail['loan_atm_id']][$finance_month_detail["pay_type"]] = $finance_month_detail['pay_amount'];
						$datas[$mem_group['id']]['member'][$mem_group['member_id']][$loan_type_id_atm][$finance_month_detail['loan_atm_id']]['period'] = !empty($receipt) ? $receipt["period_count"] : ($finance_month_detail["period_now"] + 1);
						$datas[$mem_group['id']]['last_member'] = $mem_group['member_id'];
						$last_member_id = $mem_group['member_id'];
						$datas[$mem_group['id']]['member'][$mem_group['member_id']]['sum_all'] += $finance_month_detail['pay_amount'];
					}
				}else if($finance_month_detail['deduct_code'] == 'DEPOSIT'){
					$datas[$mem_group['id']]['member'][$mem_group['member_id']][$finance_month_detail['deduct_code']][$finance_month_detail['deposit_account_id']]['pay_amount'] = $finance_month_detail['pay_amount'];
					$datas[$mem_group['id']]['member'][$mem_group['member_id']][$finance_month_detail['deduct_code']][$finance_month_detail['deposit_account_id']]['account_id'] = $finance_month_detail['deposit_account_id'];
					$datas[$mem_group['id']]['last_member'] = $mem_group['member_id'];
					$last_member_id = $mem_group['member_id'];
					$datas[$mem_group['id']]['member'][$mem_group['member_id']]['sum_all'] += $finance_month_detail['pay_amount'];
				}else{
					$datas[$mem_group['id']]['member'][$mem_group['member_id']][$finance_month_detail['deduct_code']] += $finance_month_detail['pay_amount'];
					$datas[$mem_group['id']]['last_member'] = $mem_group['member_id'];
					$last_member_id = $mem_group['member_id'];
					$datas[$mem_group['id']]['member'][$mem_group['member_id']]['sum_all'] += $finance_month_detail['pay_amount'];
				}

			}

		}

		// echo "<pre>";
		// print_r($datas);
		// exit;

		$arr_data['datas'] = $datas;
		$arr_data['last_member_id'] = $last_member_id;
		$this->preview_libraries->template_preview('report_processor_data/coop_report_send_deduction_preview',$arr_data);
	}

	function coop_report_send_deduction_excel(){
		set_time_limit ( 180 );
		$this->db->save_queries = FALSE;
		$arr_data = array();

		$row = $this->db->select(array('id','loan_type'))
						->from('coop_loan_type')
						->order_by("order_by")
						->get()->result_array();
		$arr_data['loan_type'] = $row;

		$month_arr = $this->center_function->month_arr();
		if($_GET['month']!='' && $_GET['year']!=''){
			$day = '';
			$month = $_GET['month'];
			$year = ($_GET['year']-543);
			$title_date = " เดือน ".$month_arr[$month]." ปี ".($year+543);
		}else{
			$day = '';
			$month = '';
			$year = ($_GET['year']-543);
			$title_date = " ปี ".($year+543);
		}
		$arr_data['title_date'] = $title_date;

		$row = $this->db->select(array('profile_id'))
						->from('coop_finance_month_profile')
						->where("profile_month = '".$month."' AND profile_year = '".($year+543)."'")
						->get()->result_array();
		$profile_id = $row[0]['profile_id'];

		//Declare condition
		$where_group = "";
		if ($_GET['search_type'] == 'id') {
			if(!empty($_GET['level'])){
				$where_group .= " AND t1.id = '".$_GET['level']."'";
			}else if(!empty($_GET['faction'])){
				$where_group .= " AND t2.id = '".$_GET['faction']."'";
			}else if(!empty($_GET['department'])){
				$where_group .= " AND t3.id = '".$_GET['department']."'";
			}
		} else if ($_GET['search_type'] == "code") {
			if (!empty($_GET['department_id_from'])) $where_group .= " AND CAST(t3.mem_group_id AS INTEGER) >= CAST(".$_GET['department_id_from']." AS INTEGER)";
			if (!empty($_GET['department_id_to'])) $where_group .= " AND CAST(t3.mem_group_id AS INTEGER) <= CAST(".$_GET['department_id_to']." AS INTEGER)";
		}

		$member_where = "";
		if (!empty($_GET["mem_type"])){
			// if (is_array($_GET["mem_type"]) && !in_array("all", $_GET["mem_type"])){
			// 	$member_where .= " AND t4.mem_type_id IN (".$_GET["mem_type"].")";

			// } else if(!is_array($_GET["mem_type"]) && strpos($_GET["mem_type"], "all") === false){
			// 	$member_where .= " AND t4.mem_type_id IN ".str_replace(']',')',str_replace('[','(',$_GET["mem_type"]));
			// }
			$temp = array();
			$temp_mem_type = explode(',', $_GET['mem_type']);
			foreach ($temp_mem_type as $key => $value) {
				array_push($temp, '"'.$value.'"');
			}
			$member_where .= " AND t4.mem_type_id IN (".implode(',', $temp).")";

			// $this->db->where($member_where1, '', false);
		}


		$department_order = "t1.id";
		if(!empty($_GET['department_sort'])) {
			if($_GET['department_sort'] == '1') $department_order = "t1.id";
			else if($_GET['department_sort'] == '2') $department_order = "t1.mem_group_name, t1.id";
			else if($_GET['department_sort'] == '3') $department_order = "t2.id, t1.id";
			else if($_GET['department_sort'] == '4') $department_order = "t2.mem_group_name, t1.id";
			else if($_GET['department_sort'] == '5') $department_order = "t3.id, t1.id";
			else if($_GET['department_sort'] == '6') $department_order = "t3.mem_group_name, t1.id";
		}
		$member_sort = "t4.member_id ASC";
		if(!empty($_GET['member_sort'])) {
			if($_GET['member_sort'] == "1") $member_sort = "t4.member_id ASC";
			else if($_GET['member_sort'] == "2") $member_sort = "t4.id_card ASC";
			else if($_GET['member_sort'] == "3") $member_sort = "t4.firstname_th ASC";
			else if($_GET['member_sort'] == "4") $member_sort = "t4.lastname_th ASC";
		}
		// $row_mem_group = $this->db->select(array(
		// 										't1.id',
		// 										't1.mem_group_id',
		// 										't1.mem_group_name as level_name',
		// 										't2.mem_group_name as faction_name',
		// 										't3.mem_group_name as department_name',
		// 										't4.member_id',
		// 										't4.prename_id',
		// 										't4.firstname_th',
		// 										't4.lastname_th',
		// 										't4.id_card',
		// 										't5.prename_short'
		// 									))
		// 							->from('coop_mem_group as t1')
		// 							->join('coop_mem_group as t2','t1.mem_group_parent_id = t2.id','left')
		// 							->join('coop_mem_group as t3','t2.mem_group_parent_id = t3.id','left')
		// 							->join('coop_mem_apply as t4','t1.id = t4.level','left')
		// 							->join("coop_prename as t5","t4.prename_id = t5.prename_id","left")
		// 							->where("t1.mem_group_type = '3'".$where_group.$member_where)
		// 							->order_by($department_order.",".$member_sort)
		// 							->get()->result_array();
		$date_range['start'] 	= date("Y-m", strtotime($year."-".$month."-01")	) . "-01";
		$date_range['end'] 		= date("Y-m-t", strtotime($year."-".$month."-01")	)." 23:59:59";
		$row_mem_group = $this->db->select(array(
													't1.id',
													't1.mem_group_id',
													't1.mem_group_name as level_name',
													't2.mem_group_name as faction_name',
													't3.mem_group_name as department_name',
													't4.member_id',
													't4.prename_id',
													't4.firstname_th',
													't4.lastname_th',
													't4.id_card',
													't5.prename_short'
												))
										->from('coop_mem_apply as t4')
										->join('coop_mem_group as t1','t1.id = IF( (select level_old from coop_mem_group_move where date_move >= "'.$date_range['start'].'" and coop_mem_group_move.member_id = t4.member_id ORDER BY date_move ASC LIMIT 1),
									(select level_old from coop_mem_group_move where date_move >= "'.$date_range['start'].'" and coop_mem_group_move.member_id = t4.member_id ORDER BY date_move ASC LIMIT 1),
									t4.`level`)','left', false)
										->join('coop_mem_group as t2','t1.mem_group_parent_id = t2.id','left')
										->join('coop_mem_group as t3','t2.mem_group_parent_id = t3.id','left')
										->join("coop_prename as t5","t4.prename_id = t5.prename_id","left")
										->where("(select sum(pay_amount) from coop_finance_month_detail where profile_id = $profile_id and member_id = t4.member_id group by profile_id,member_id LIMIT 1)  > 0 ".$where_group.$member_where)
										->order_by($department_order.",".$member_sort)
										->get()->result_array();


		$mem_group_ids = array_column($row_mem_group, 'id');
		$member_ids = array_filter(array_column($row_mem_group, 'member_id'));

		// echo "<pre>";
		// print_r($member_ids);
		// echo "</pre>";
		// exit;

		$finance_month_details = $this->db->query("SELECT * FROM coop_finance_month_detail WHERE member_id IN (".implode(',', $member_ids).") AND profile_id = '{$profile_id}'")
											->result_array();
		$finance_member_ids = array_column($finance_month_details, 'member_id');

		// echo "<pre>";
		// print_r($finance_month_details);
		// echo "</pre>";
		// exit;

		$loan_ids = array_filter(array_column($finance_month_details, 'loan_id'));
		if(!empty($loan_ids)) {
			$loans = $this->db->query("SELECT t1.id, t1.contract_number, t1.id, t2.loan_type_id  FROM coop_loan as t1
										LEFT JOIN coop_loan_name as t2 ON t2.loan_name_id = t1.loan_type
										WHERE id IN (".implode(',', $loan_ids).") AND t1.loan_status NOT IN ('5')")
								->result_array();
			$loan_members = array_column($loans, 'id');

			$receipts = $this->db->query("SELECT t2.loan_id, t2.period_count FROM coop_receipt as t1
											INNER JOIN coop_finance_transaction as t2 ON t1.receipt_id = t2.receipt_id
											WHERE t2.loan_id IN (".implode(',', $loan_ids).") AND t1.finance_month_profile_id = '{$profile_id}'")
											->result_array();
			$loan_receipt_members = array_column($receipts, 'loan_id');
		}

		$loan_atm_ids = array_filter(array_column($finance_month_details, 'loan_atm_id'));
		if(!empty($loan_atm_ids)) {
			$loan_atms = $this->db->query("SELECT t1.loan_atm_id, t1.contract_number, t1.loan_atm_id  FROM coop_loan_atm as t1
										WHERE loan_atm_id IN (".implode(',', $loan_atm_ids).") AND t1.loan_atm_status NOT IN ('5')")
								->result_array();
			$loan_atm_members = array_column($loan_atms, 'loan_atm_id');

			$receipt_atms = $this->db->query("SELECT t2.loan_atm_id, t2.period_count FROM coop_receipt as t1
											INNER JOIN coop_finance_transaction as t2 ON t1.receipt_id = t2.receipt_id
											WHERE t2.loan_atm_id IN (".implode(',', $loan_atm_ids).") AND t1.finance_month_profile_id = '{$profile_id}' AND t2.principal_payment > 0")
									->result_array();
			$loan_atm_receipt_members = array_column($receipt_atms, 'loan_atm_id');
		}

		$datas = array();
		$total_data = array();
		$last_member_id = null;
		foreach($row_mem_group as $mem_group) {
			$datas[$mem_group['id']]['mem_group_name'] = !empty($mem_group['level_name']) ? $mem_group['department_name'].":".$mem_group['faction_name'].":".$mem_group['level_name']."(".$mem_group['mem_group_id'].")" : "";
			$datas[$mem_group['id']]['member'][$mem_group['member_id']]['member_id'] = $mem_group['member_id'];
			$datas[$mem_group['id']]['member'][$mem_group['member_id']]['name'] = $mem_group['prename_short'].$mem_group['firstname_th']." ".$mem_group['lastname_th'];
			$datas[$mem_group['id']]['member'][$mem_group['member_id']]['prename_short'] = $mem_group['prename_short'];
			$datas[$mem_group['id']]['member'][$mem_group['member_id']]['firstname_th'] = $mem_group['firstname_th'];
			$datas[$mem_group['id']]['member'][$mem_group['member_id']]['lastname_th'] = $mem_group['lastname_th'];
			$datas[$mem_group['id']]['member'][$mem_group['member_id']]['id_card'] = $mem_group['id_card'];

			$finance_indexs = array_keys($finance_member_ids,$mem_group['member_id']);

			foreach($finance_indexs as $finance_index) {
				$finance_month_detail = $finance_month_details[$finance_index];
				if($finance_month_detail["deduct_code"] == "LOAN") {
					if (in_array($finance_month_detail['loan_id'], $loan_members)) {
						$loan = $loans[array_search($finance_month_detail['loan_id'], $loan_members)];
						$receipt = $receipts[array_search($finance_month_detail['loan_id'], $loan_receipt_members)];
						$datas[$mem_group['id']]['member'][$mem_group['member_id']][$loan['loan_type_id']][$finance_month_detail['loan_id']]['contract_number'] = $loan["contract_number"];
						$datas[$mem_group['id']]['member'][$mem_group['member_id']][$loan['loan_type_id']][$finance_month_detail['loan_id']][$finance_month_detail["pay_type"]] = $finance_month_detail['pay_amount'];
						$datas[$mem_group['id']]['member'][$mem_group['member_id']][$loan['loan_type_id']][$finance_month_detail['loan_id']]['period'] = !empty($receipt) ? $receipt["period_count"] : ($finance_month_detail["period_now"] + 1);
						$datas[$mem_group['id']]['last_member'] = $mem_group['member_id'];
						$last_member_id = $mem_group['member_id'];
						$datas[$mem_group['id']]['member'][$mem_group['member_id']]['sum_all'] += $finance_month_detail['pay_amount'];
					}
				}else if($finance_month_detail['deduct_code'] == 'ATM'){
					if (in_array($finance_month_detail['loan_atm_id'], $loan_atm_members)) {
						$loan_type_id_atm = '7';
						$loan = $loan_atms[array_search($finance_month_detail['loan_atm_id'], $loan_atm_members)];
						$receipt = $receipt_atms[array_search($finance_month_detail['loan_atm_id'], $loan_atm_receipt_members)];
						$datas[$mem_group['id']]['member'][$mem_group['member_id']][$loan_type_id_atm][$finance_month_detail['loan_atm_id']]['contract_number'] = $loan["contract_number"];
						$datas[$mem_group['id']]['member'][$mem_group['member_id']][$loan_type_id_atm][$finance_month_detail['loan_atm_id']][$finance_month_detail["pay_type"]] = $finance_month_detail['pay_amount'];
						$datas[$mem_group['id']]['member'][$mem_group['member_id']][$loan_type_id_atm][$finance_month_detail['loan_atm_id']]['period'] = !empty($receipt) ? $receipt["period_count"] : ($finance_month_detail["period_now"] + 1);
						$datas[$mem_group['id']]['last_member'] = $mem_group['member_id'];
						$last_member_id = $mem_group['member_id'];
						$datas[$mem_group['id']]['member'][$mem_group['member_id']]['sum_all'] += $finance_month_detail['pay_amount'];
					}
				}else if($finance_month_detail['deduct_code'] == 'DEPOSIT'){
					$datas[$mem_group['id']]['member'][$mem_group['member_id']][$finance_month_detail['deduct_code']][$finance_month_detail['deposit_account_id']]['pay_amount'] = $finance_month_detail['pay_amount'];
					$datas[$mem_group['id']]['member'][$mem_group['member_id']][$finance_month_detail['deduct_code']][$finance_month_detail['deposit_account_id']]['account_id'] = $finance_month_detail['deposit_account_id'];
					$datas[$mem_group['id']]['last_member'] = $mem_group['member_id'];
					$last_member_id = $mem_group['member_id'];
					$datas[$mem_group['id']]['member'][$mem_group['member_id']]['sum_all'] += $finance_month_detail['pay_amount'];
				}else{
					$datas[$mem_group['id']]['member'][$mem_group['member_id']][$finance_month_detail['deduct_code']] += $finance_month_detail['pay_amount'];
					$datas[$mem_group['id']]['last_member'] = $mem_group['member_id'];
					$last_member_id = $mem_group['member_id'];
					$datas[$mem_group['id']]['member'][$mem_group['member_id']]['sum_all'] += $finance_month_detail['pay_amount'];
				}

			}

		}

		// echo "<pre>";
		// print_r($datas);
		// exit;

		$arr_data['datas'] = $datas;
		$arr_data['last_member_id'] = $last_member_id;
		$this->load->view('report_processor_data/coop_report_send_deduction_excel',$arr_data);
	}

	function coop_report_send_deduction_preview_2(){
		set_time_limit ( 180 );
		//$this->db->save_queries = FALSE;
		$arr_data = array();

		$this->db->select(array('id','loan_type'));
		$this->db->from('coop_loan_type');
		$this->db->order_by("order_by");
		$row = $this->db->get()->result_array();
		$arr_data['loan_type'] = $row;

		$month_arr = $this->center_function->month_arr();
		if(@$_GET['month']!='' && @$_GET['year']!=''){
			$day = '';
			$month = @$_GET['month'];
			$year = (@$_GET['year']-543);
			$title_date = " เดือน ".@$month_arr[$month]." ปี ".(@$year+543);
		}else{
			$day = '';
			$month = '';
			$year = (@$_GET['year']-543);
			$title_date = " ปี ".(@$year+543);
		}
		$arr_data['title_date'] = $title_date;

		$this->db->select(array('profile_id'));
		$this->db->from('coop_finance_month_profile');
		$this->db->where("profile_month = '".$month."' AND profile_year = '".($year+543)."'");
		$row = $this->db->get()->result_array();
		$profile_id = @$row[0]['profile_id'];

		//Declare condition
		$where_group = "";
		if (@$_GET['search_type'] == 'id') {
			//รูปแบบหน่วยงาน
			if(@$_GET['level'] != ''){
				$where_group .= " AND t1.id = '".$_GET['level']."'";
			}else if(@$_GET['faction'] != ''){
				$where_group .= " AND t2.id = '".$_GET['faction']."'";
			}else if(@$_GET['department'] != ''){
				$where_group .= " AND t3.id = '".$_GET['department']."'";
			}
		} else if ($_GET['search_type'] == "code") {
			if (!empty($_GET['department_id_from'])) $where_group .= " AND CAST(t3.mem_group_id AS INTEGER) >= CAST(".$_GET['department_id_from']." AS INTEGER)";
			if (!empty($_GET['department_id_to'])) $where_group .= " AND CAST(t3.mem_group_id AS INTEGER) <= CAST(".$_GET['department_id_to']." AS INTEGER)";
		}

		$member_where = "";
		if (!empty($_GET["mem_type"])){
			if(is_array($_GET["mem_type"])) {
				$member_where .= " AND coop_mem_apply.mem_type_id IN (".implode(',', $_GET["mem_type"]).")";
			} else {
				$member_where .= " AND coop_mem_apply.mem_type_id IN ".str_replace(']',')',str_replace('[','(',$_GET["mem_type"]));
			}
		}

		$department_order = "t1.id";
		if(!empty($_GET['department_sort'])) {
			if($_GET['department_sort'] == '1') $department_order = "t1.id";
			else if($_GET['department_sort'] == '2') $department_order = "t1.mem_group_name, t1.id";
			else if($_GET['department_sort'] == '3') $department_order = "t2.id, t1.id";
			else if($_GET['department_sort'] == '4') $department_order = "t2.mem_group_name, t1.id";
			else if($_GET['department_sort'] == '5') $department_order = "t3.id, t1.id";
			else if($_GET['department_sort'] == '6') $department_order = "t3.mem_group_name, t1.id";
		}
		$this->db->select(array(
			't1.id',
			't1.mem_group_id',
			't1.mem_group_name as level_name',
			't2.mem_group_name as faction_name',
			't3.mem_group_name as department_name'
		));
		$this->db->from('coop_mem_group as t1');
		$this->db->join('coop_mem_group as t2','t1.mem_group_parent_id = t2.id','inner');
		$this->db->join('coop_mem_group as t3','t2.mem_group_parent_id = t3.id','inner');
		$this->db->where("t1.mem_group_type = '3'".$where_group);
		$this->db->order_by($department_order);
		$row_mem_group = $this->db->get()->result_array();

		//echo $this->db->last_query();exit;
		$row_data = array();
		$total_data = array();
		$page_all_arr = array();

		$member_data = array();
		$mem_group_names = array();

		$page_get = !empty($_GET['page']) ? $_GET['page'] : 1;
		$all_page = 0;
		foreach($row_mem_group as $key_mem_group => $value_mem_group){
			$row_data[$value_mem_group['id']]['mem_group_name'] = $value_mem_group['department_name'].":".$value_mem_group['faction_name'].":".$value_mem_group['level_name']."(".$value_mem_group['mem_group_id'].")";
			$mem_group_names[$value_mem_group['id']] = $value_mem_group['department_name'].":".$value_mem_group['faction_name'].":".$value_mem_group['level_name']."(".$value_mem_group['mem_group_id'].")";
			$where = " AND level = '".$value_mem_group['id']."'";

			$this->db->select(array('coop_mem_apply.member_id'));
			$this->db->from('coop_mem_apply');
			$this->db->where("coop_mem_apply.mem_type = '1' {$where}");
			$rs_count = $this->db->get()->result_array();

			$num_rows = count($rs_count);
			$page_limit = 18;
			$page_all = @ceil($num_rows/$page_limit);
			$page_all_arr[$value_mem_group['id']] = $page_all;

			$count_group_page = 0;
			$runno = 0;
			for($page = 1;$page<=$page_all;$page++){
				$all_page++;

				if($all_page == $page_get) {
					$count_member = $count_group_page * $page_limit;
					$page_start = (($page-1)*$page_limit);
					$per_page = $page*$page_limit;

					//Page Data
					$member_sort = "coop_mem_apply.member_id ASC";
					if(!empty($_GET['member_sort'])) {
						if($_GET['member_sort'] == "1") $member_sort = "coop_mem_apply.member_id ASC";
						else if($_GET['member_sort'] == "2") $member_sort = "coop_mem_apply.id_card ASC";
						else if($_GET['member_sort'] == "3") $member_sort = "coop_mem_apply.firstname_th ASC";
						else if($_GET['member_sort'] == "4") $member_sort = "coop_mem_apply.lastname_th ASC";
					}
					$this->db->select(array('coop_mem_apply.member_id','coop_mem_apply.prename_id','coop_mem_apply.firstname_th','coop_mem_apply.lastname_th','coop_prename.prename_short'));
					$this->db->from('(SELECT * FROM coop_mem_apply where mem_type = "1" and level = "'.$value_mem_group['id'].'" LIMIT '.$page_start.', '.$page_limit.') as coop_mem_apply');
					$this->db->join("coop_prename","coop_mem_apply.prename_id = coop_prename.prename_id","left");
					$this->db->where("1=1".$member_where);
					$this->db->order_by($member_sort);
					$rs = $this->db->get()->result_array();

					if(!empty($rs)){
						foreach(@$rs as $key => $row){
							$runno++;
							$full_name = @$row['prename_short'].@$row['firstname_th'].' '.@$row['lastname_th'];
							$row_data[$value_mem_group['id']]['data'][$page][$row['member_id']]['runno'] = $runno;
							$row_data[$value_mem_group['id']]['data'][$page][$row['member_id']]['full_name'] = $full_name;
							$this->db->select(array(
								't1.*',
								't2.contract_number',
								't2.loan_amount_balance',
								't2.period_now',
								't3.loan_type_id',
								't4.contract_number AS contract_number_atm'
							));

							$this->db->from('(SELECT loan_id, deduct_code, pay_type, pay_amount, deposit_account_id,loan_atm_id FROM coop_finance_month_detail WHERE member_id = "'.$row['member_id'].'" AND profile_id = "'.$profile_id.'") as t1');
							$this->db->join("coop_loan as t2","t2.id = t1.loan_id","left");
							$this->db->join("coop_loan_name as t3","t3.loan_name_id = t2.loan_type","left");
							$this->db->join("coop_loan_atm as t4","t4.loan_atm_id = t1.loan_atm_id","left");
							$rs_month_detail = $this->db->get()->result_array();
							//echo $this->db->last_query();

							foreach($rs_month_detail as $key_month_detail => $value_month_detail){
								if($value_month_detail['deduct_code'] == 'LOAN'){
									$this->db->select(array('t2.loan_amount_balance','t2.period_count'));
									$this->db->from('coop_receipt as t1');
									$this->db->join("coop_finance_transaction as t2","t1.receipt_id = t2.receipt_id","inner");
									$this->db->where("t1.member_id = '".$row['member_id']."' AND t1.finance_month_profile_id = '".$profile_id."' AND t2.principal_payment > 0 AND loan_id = '".$value_month_detail['loan_id']."'");
									$rs_receipt = $this->db->get()->result_array();

									$receipt_balance = @$rs_receipt[0]['loan_amount_balance'];
									$receipt_period = @$rs_receipt[0]['period_count'];

									@$row_data[$value_mem_group['id']]['data'][$page][$row['member_id']][$value_month_detail['deduct_code']][$value_month_detail['loan_type_id']]['contract_number'] = $value_month_detail['contract_number'];
									@$row_data[$value_mem_group['id']]['data'][$page][$row['member_id']][$value_month_detail['deduct_code']][$value_month_detail['loan_type_id']][$value_month_detail['pay_type']] = $value_month_detail['pay_amount'];
									@$row_data[$value_mem_group['id']]['data'][$page][$row['member_id']][$value_month_detail['deduct_code']][$value_month_detail['loan_type_id']]['balance'] = $receipt_balance!=''?$receipt_balance:$value_month_detail['loan_amount_balance'];
									@$row_data[$value_mem_group['id']]['data'][$page][$row['member_id']][$value_month_detail['deduct_code']][$value_month_detail['loan_type_id']]['period'] = $receipt_period!=''?$receipt_period:($value_month_detail['period_now']+1);
								}else if($value_month_detail['deduct_code'] == 'ATM'){
									$loan_type_id_atm = '7';
									$this->db->select(array('t2.loan_amount_balance','t2.period_count'));
									$this->db->from('coop_receipt as t1');
									$this->db->join("coop_finance_transaction as t2","t1.receipt_id = t2.receipt_id","inner");
									$this->db->where("t1.member_id = '".$row['member_id']."' AND t1.finance_month_profile_id = '".$profile_id."' AND t2.principal_payment > 0 AND loan_atm_id = '".$value_month_detail['loan_atm_id']."'");
									$rs_receipt = $this->db->get()->result_array();

									$receipt_balance = @$rs_receipt[0]['loan_amount_balance'];
									$receipt_period = @$rs_receipt[0]['period_count'];

									$row_data[$value_mem_group['id']]['data'][$page][$row['member_id']]['LOAN'][$loan_type_id_atm]['contract_number'] = $value_month_detail['contract_number_atm'];
									@$row_data[$value_mem_group['id']]['data'][$page][$row['member_id']]['LOAN'][$loan_type_id_atm][$value_month_detail['pay_type']] = $value_month_detail['pay_amount'];
									@$row_data[$value_mem_group['id']]['data'][$page][$row['member_id']]['LOAN'][$loan_type_id_atm]['balance'] = $receipt_balance!=''?$receipt_balance:$value_month_detail['loan_amount_balance'];
									@$row_data[$value_mem_group['id']]['data'][$page][$row['member_id']]['LOAN'][$loan_type_id_atm]['period'] = $receipt_period!=''?$receipt_period:($value_month_detail['period_now']+1);
								}else if($value_month_detail['deduct_code'] == 'DEPOSIT'){
									@$row_data[$value_mem_group['id']]['data'][$page][$row['member_id']][$value_month_detail['deduct_code']]['pay_amount'] = $value_month_detail['pay_amount'];
									@$row_data[$value_mem_group['id']]['data'][$page][$row['member_id']][$value_month_detail['deduct_code']]['account_id'] = $value_month_detail['deposit_account_id'];
								}else{
									@$row_data[$value_mem_group['id']]['data'][$page][$row['member_id']][$value_month_detail['deduct_code']] += $value_month_detail['pay_amount'];
								}
								@$row_data[$value_mem_group['id']]['data'][$page][$row['member_id']]['sum_all'] += $value_month_detail['pay_amount'];
							}
						}
					}

					//รวมคน
					$sum_person = $this->db->select(array(
						'count(coop_mem_apply.member_id) AS sum_person'
					))
					->from('coop_mem_apply')
					->where(" mem_type = '1' AND level = '".$value_mem_group['id']."'")
					->limit(1)
					->get()->result_array();
					@$total_data[$value_mem_group['id']]['total']['count_member'] = @$sum_person[0]['sum_person'];

					//ผลรวม
					$sum_month_detail = $this->db->select(array(
						'SUM(t1.pay_amount) AS pay_amount',
						't5.loan_type_code',
						't5.id',
						't1.pay_type',
						't1.deduct_code'
					))
					->from('coop_finance_month_detail AS t1')
					->join("coop_mem_apply AS t2","t1.member_id = t2.member_id","left")
					->join("coop_loan AS t3","t1.loan_id = t3.id","left")
					->join("coop_loan_name AS t4","t3.loan_type = t4.loan_name_id","left")
					->join("coop_loan_type AS t5","t4.loan_type_id = t5.id","left")
					->where(" t2.level = '".$value_mem_group['id']."'AND  t1.profile_id = '".$profile_id."'")
					->group_by("t1.deduct_code,t5.loan_type_code,t1.pay_type")
					->get()->result_array();

					foreach($sum_month_detail AS $key=>$value){
						if($value['deduct_code'] == 'LOAN'){
							@$total_data[$value_mem_group['id']]['total'][$value['deduct_code']][$value['id']][$value['pay_type']] = @$value['pay_amount'];
						}else if($value['deduct_code'] == 'ATM'){
							@$total_data[$value_mem_group['id']]['total']['LOAN']['7'][$value['pay_type']] += @$value['pay_amount'];
						}else{
							@$total_data[$value_mem_group['id']]['total'][$value['deduct_code']] = @$value['pay_amount'];
						}

						@$total_data[$value_mem_group['id']]['total']['sum_all'] += @$value['pay_amount'];
					}

					//ผลรวมเลขที่สัญญา
					$sum_contract = $this->db->select(array(
						't5.loan_type_code',
						't5.id',
						't1.deduct_code',
						't1.loan_id',
						't1.loan_atm_id'
					))
					->from('coop_finance_month_detail AS t1')
					->join("coop_mem_apply AS t2","t1.member_id = t2.member_id","left")
					->join("coop_loan AS t3","t1.loan_id = t3.id","left")
					->join("coop_loan_name AS t4","t3.loan_type = t4.loan_name_id","left")
					->join("coop_loan_type AS t5","t4.loan_type_id = t5.id","left")
					->where(" t2.level = '".$value_mem_group['id']."'AND  t1.profile_id = '".$profile_id."' AND t1.deduct_code IN('LOAN','ATM')")
					->group_by("t1.deduct_code,t5.loan_type_code,t1.loan_id,t1.loan_atm_id")
					->get()->result_array();

					foreach($sum_contract AS $key_contract=>$value_contract){
						if($value_contract['deduct_code'] == 'LOAN'){
							@$total_data[$value_mem_group['id']]['total'][$value_contract['deduct_code']][$value_contract['id']]['count_contract_number']++;
						}else if($value_contract['deduct_code'] == 'ATM'){
							@$total_data[$value_mem_group['id']]['total']['LOAN']['7']['count_contract_number']++;
						}
					}
					//echo '<pre>'; print_r($total_data); echo '</pre>';
				}
				$runno += $page_limit;
				$count_group_page++;
			}
		}
		$paging = $this->pagination_center->paginating(intval($page_get), $all_page, 1, 20,$_GET);//$page_now = 1, $row_total = 1, $per_page = 20, $page_limit = 20

		$arr_data['row_data'] = $row_data;
		$arr_data['total_data'] = $total_data;
		$arr_data['page_all_arr'] = $page_all_arr;
		$arr_data['paging'] = $paging;
		$arr_data['page_get'] = $page_get;
		$arr_data['all_page'] = $all_page;
		$this->preview_libraries->template_preview('report_processor_data/coop_report_send_deduction_preview_2',$arr_data);
	}

	public function coop_report_deduction(){
		$arr_data = array();

		$this->db->select(array('id','mem_group_name'));
		$this->db->from('coop_mem_group');
		$this->db->where("mem_group_type = '1'");
		$row = $this->db->get()->result_array();
		$arr_data['row_mem_group'] = $row;

		$this->db->select('mem_type_id, mem_type_name');
		$this->db->from('coop_mem_type');
		$row = $this->db->get()->result_array();
		$arr_data['mem_type'] = $row;

		$this->libraries->template('report_processor_data/coop_report_deduction',$arr_data);
	}

	function coop_report_deduction_preview(){
		set_time_limit(180);
		$arr_data = array();
		$this->db->select(array('id','mem_group_name'));
		$this->db->from('coop_mem_group');
		$rs_group = $this->db->get()->result_array();
		$mem_group_arr = array();
		foreach($rs_group as $key => $row_group){
			$mem_group_arr[$row_group['id']] = $row_group['mem_group_name'];
		}
		$arr_data['mem_group_arr'] = $mem_group_arr;

		$arr_data['month_arr'] = array('1'=>'มกราคม','2'=>'กุมภาพันธ์','3'=>'มีนาคม','4'=>'เมษายน','5'=>'พฤษภาคม','6'=>'มิถุนายน','7'=>'กรกฎาคม','8'=>'สิงหาคม','9'=>'กันยายน','10'=>'ตุลาคม','11'=>'พฤศจิกายน','12'=>'ธันวาคม');
		$arr_data['month_short_arr'] = array('1'=>'ม.ค.','2'=>'ก.พ.','3'=>'มี.ค.','4'=>'เม.ย.','5'=>'พ.ค.','6'=>'มิ.ย.','7'=>'ก.ค.','8'=>'ส.ค.','9'=>'ก.ย.','10'=>'ต.ค.','11'=>'พ.ย.','12'=>'ธ.ค.');

		$this->preview_libraries->template_preview('report_processor_data/coop_report_deduction_preview',$arr_data);
	}

	function check_coop_report_deduction() {
		$where = " AND t4.profile_month = '".$_POST['month']."' AND t4.profile_year = '".$_POST['year']."'";
		$where_mem_type = "";
		if (!empty($_POST["mem_type"]) && !in_array("all", $_POST["mem_type"])){
			$where_mem_type .= " AND t1.mem_type_id IN (".implode(',', $_POST["mem_type"]).")";
		}
		$finance_profile = $this->db->select("*")
									->from("coop_finance_month_profile")
									->where("profile_month = '".$_POST['month']."' AND profile_year = '".$_POST['year']."'")
									->get()->row();

		$this->db->select(array(
			"t1.member_id", "t1.firstname_th", "t1.lastname_th", "t1.id_card", "t1.level", "t1.prename_id"
		));
		$rs = $this->db->from('coop_mem_apply as t1')
								->join("(SELECT profile_id, member_id FROM coop_finance_month_detail GROUP BY member_id) as t3","t1.member_id = t3.member_id","inner")
								->join("coop_finance_month_profile as t4","t3.profile_id = t4.profile_id","inner")
								->join("(SELECT * FROM coop_receipt WHERE finance_month_profile_id = '".$finance_profile->profile_id."') as t5","t4.profile_id = t5.finance_month_profile_id AND t3.member_id = t5.member_id","inner")
								->where("t1.mem_type = '1' {$where} {$where_mem_type}")
								->group_by('t1.member_id')
								->get()->result_array();
		if(!empty($rs)){
			echo "success";
		}else{
			echo "";
		}
	}

	function coop_report_deduction_excel() {
		set_time_limit(180);
		$arr_data = array();
		$this->db->select(array('id','mem_group_name'));
		$this->db->from('coop_mem_group');
		$rs_group = $this->db->get()->result_array();
		$mem_group_arr = array();
		foreach($rs_group as $key => $row_group){
			$mem_group_arr[$row_group['id']] = $row_group['mem_group_name'];
		}
		$arr_data['mem_group_arr'] = $mem_group_arr;

		$arr_data['month_arr'] = array('1'=>'มกราคม','2'=>'กุมภาพันธ์','3'=>'มีนาคม','4'=>'เมษายน','5'=>'พฤษภาคม','6'=>'มิถุนายน','7'=>'กรกฎาคม','8'=>'สิงหาคม','9'=>'กันยายน','10'=>'ตุลาคม','11'=>'พฤศจิกายน','12'=>'ธันวาคม');
		$arr_data['month_short_arr'] = array('1'=>'ม.ค.','2'=>'ก.พ.','3'=>'มี.ค.','4'=>'เม.ย.','5'=>'พ.ค.','6'=>'มิ.ย.','7'=>'ก.ค.','8'=>'ส.ค.','9'=>'ก.ย.','10'=>'ต.ค.','11'=>'พ.ย.','12'=>'ธ.ค.');

		$this->load->view('report_processor_data/coop_report_deduction_excel',$arr_data);
	}

	public function coop_report_pay_month(){
		$arr_data = array();

		$this->db->select(array('id','mem_group_name'));
		$this->db->from('coop_mem_group');
		$this->db->where("mem_group_type = '1'");
		$row = $this->db->get()->result_array();
		$arr_data['row_mem_group'] = $row;

		$this->libraries->template('report_processor_data/coop_report_pay_month',$arr_data);
	}

	function coop_report_pay_month_preview(){
		set_time_limit ( 180 );
		$arr_data = array();
		$rs_group = $this->db->select(array('id','mem_group_name'))
							->from('coop_mem_group')
							->get()->result_array();
		$mem_group_arr = array();
		foreach($rs_group as $key => $row_group){
			$mem_group_arr[$row_group['id']] = $row_group['mem_group_name'];
		}
		$arr_data['mem_group_arr'] = $mem_group_arr;

		$arr_data['month_arr'] = array('1'=>'มกราคม','2'=>'กุมภาพันธ์','3'=>'มีนาคม','4'=>'เมษายน','5'=>'พฤษภาคม','6'=>'มิถุนายน','7'=>'กรกฎาคม','8'=>'สิงหาคม','9'=>'กันยายน','10'=>'ตุลาคม','11'=>'พฤศจิกายน','12'=>'ธันวาคม');
		$arr_data['month_short_arr'] = array('1'=>'ม.ค.','2'=>'ก.พ.','3'=>'มี.ค.','4'=>'เม.ย.','5'=>'พ.ค.','6'=>'มิ.ย.','7'=>'ก.ค.','8'=>'ส.ค.','9'=>'ก.ย.','10'=>'ต.ค.','11'=>'พ.ย.','12'=>'ธ.ค.');

		$this->preview_libraries->template_preview('report_processor_data/coop_report_pay_month_preview',$arr_data);
	}
	function coop_report_pay_month_preview_old(){
		set_time_limit ( 180 );
		$arr_data = array();
		$this->db->select(array('id','mem_group_name'));
		$this->db->from('coop_mem_group');
		$rs_group = $this->db->get()->result_array();
		$mem_group_arr = array();
		foreach($rs_group as $key => $row_group){
			$mem_group_arr[$row_group['id']] = $row_group['mem_group_name'];
		}
		$arr_data['mem_group_arr'] = $mem_group_arr;

		$arr_data['month_arr'] = array('1'=>'มกราคม','2'=>'กุมภาพันธ์','3'=>'มีนาคม','4'=>'เมษายน','5'=>'พฤษภาคม','6'=>'มิถุนายน','7'=>'กรกฎาคม','8'=>'สิงหาคม','9'=>'กันยายน','10'=>'ตุลาคม','11'=>'พฤศจิกายน','12'=>'ธันวาคม');
		$arr_data['month_short_arr'] = array('1'=>'ม.ค.','2'=>'ก.พ.','3'=>'มี.ค.','4'=>'เม.ย.','5'=>'พ.ค.','6'=>'มิ.ย.','7'=>'ก.ค.','8'=>'ส.ค.','9'=>'ก.ย.','10'=>'ต.ค.','11'=>'พ.ย.','12'=>'ธ.ค.');

		$this->preview_libraries->template_preview('report_processor_data/coop_report_pay_month_preview_old',$arr_data);
	}

	function coop_report_pay_month_excel(){
		set_time_limit(180);
		$arr_data = array();
		$this->db->select(array('id','mem_group_name'));
		$this->db->from('coop_mem_group');
		$rs_group = $this->db->get()->result_array();
		$mem_group_arr = array();
		foreach($rs_group as $key => $row_group){
			$mem_group_arr[$row_group['id']] = $row_group['mem_group_name'];
		}
		$arr_data['mem_group_arr'] = $mem_group_arr;

		$arr_data['month_arr'] = array('1'=>'มกราคม','2'=>'กุมภาพันธ์','3'=>'มีนาคม','4'=>'เมษายน','5'=>'พฤษภาคม','6'=>'มิถุนายน','7'=>'กรกฎาคม','8'=>'สิงหาคม','9'=>'กันยายน','10'=>'ตุลาคม','11'=>'พฤศจิกายน','12'=>'ธันวาคม');
		$arr_data['month_short_arr'] = array('1'=>'ม.ค.','2'=>'ก.พ.','3'=>'มี.ค.','4'=>'เม.ย.','5'=>'พ.ค.','6'=>'มิ.ย.','7'=>'ก.ค.','8'=>'ส.ค.','9'=>'ก.ย.','10'=>'ต.ค.','11'=>'พ.ย.','12'=>'ธ.ค.');

		$this->preview_libraries->template_preview('report_processor_data/coop_report_pay_month_excel',$arr_data);
	}

	function check_coop_report_pay_month() {
		// $where = " AND t4.profile_month = '".$_POST['month']."' AND t4.profile_year = '".$_POST['year']."' AND t3.run_status = '1'";

		$where_member = '1=1';
		if(!empty($array_post['department'])){
			$where_member .= " AND department = '".$array_post['department']."'";
		}
		if(!empty($array_post['faction'])){
			$where_member .= " AND faction = '".$array_post['faction']."'";
		}
		if(!empty($array_post['level'])){
			$where_member .= " AND level = '".$array_post['level']."'";
		}
		$members = $this->db->select(array('member_id'))
							->from("coop_mem_apply")
							->where($where_member)
							->get()->result_array();
		$member_ids = array_column($members, 'member_id');
		$monthProfile = $this->db->select("*")
									->from("coop_finance_month_profile")
									->where("profile_month = '".$_POST['month']."' AND profile_year = '".$_POST['year']."'")
									->get()->row();
		$rs = $this->db->select("t1.member_id")
						->from("(SELECT member_id,profile_id  FROM coop_finance_month_detail WHERE member_id IN (".implode(',',$member_ids).") AND profile_id = '".$monthProfile->profile_id."' AND run_status = '1') as t1")
						->join("coop_receipt as t2", "t1.member_id = t2.member_id AND t1.profile_id = t2.finance_month_profile_id", "inner")
						->get()->result_array();
		// $rs = $this->db->select(array(
		// 							't1.member_id'
		// 							))
		// 			->from('(SELECT member_id,mem_type FROM coop_mem_apply WHERE '.$where_member.') as t1')
		// 			->join("(SELECT * FROM coop_finance_month_profile WHERE profile_month = '".$_POST['month']."' AND profile_year = '".$_POST['year']."') as t4","1=1","inner")
		// 			->join("(SELECT run_status, profile_id, member_id FROM coop_finance_month_detail GROUP BY member_id) as t3","t1.member_id = t3.member_id AND t3.profile_id = t4.profile_id","inner")
		// 			->join("coop_receipt as t5","t4.profile_id = t5.finance_month_profile_id","inner")
		// 			->where("t1.mem_type = '1' {$where}")
		// 			->group_by('t1.member_id')
		// 			->get()->result_array();
		if(!empty($rs)){
			echo "success";
		}else{
			echo "";
		}
	}

	function coop_report_non_pay(){
		$arr_data = array();

		$this->db->select(array('id','mem_group_name'));
		$this->db->from('coop_mem_group');
		$this->db->where("mem_group_type = '1'");
		$row = $this->db->get()->result_array();
		$arr_data['row_mem_group'] = $row;

		$this->libraries->template('report_processor_data/coop_report_non_pay',$arr_data);
	}

	function coop_report_non_pay_preview(){
		$array_post = $_POST;
		if($_POST['type_report'] == '0'){
			$this->coop_report_non_pay_by_department($array_post);
		}else if($_POST['type_report'] == '1'){
			$this->coop_report_non_pay_by_member_detail($array_post);
		}else if($_POST['type_report'] == '2'){
			$this->coop_report_non_pay_by_member($array_post);
		}else{
			echo "<script>document.location.href='".base_url(PROJECTPATH.'/report_processor_data/coop_report_non_pay')."';</script>";
		}
	}

	function coop_report_non_pay_by_department($array_post){
		$arr_data = array();
		//echo"<pre>";print_r($array_post);exit;
		$this->db->select(array('id','loan_type','loan_type_code'));
		$this->db->from('coop_loan_type');
		$this->db->order_by("order_by");
		$row = $this->db->get()->result_array();
		$loan_type = $row;
		$arr_data['loan_type'] = $loan_type;

		$where_data = '';
		if($array_post['department']!=''){
			$where_data .= " AND t3.department = '".$array_post['department']."'";
		}

		$this->db->select(array('t1.*','t3.member_id','t3.department'));
		$this->db->from('coop_non_pay_detail as t1');
		$this->db->join('coop_non_pay as t2','t1.non_pay_id = t2.non_pay_id','inner');
		$this->db->join('coop_mem_apply as t3','t2.member_id = t3.member_id','inner');
		$this->db->where("t2.non_pay_month = '".$array_post['month']."' AND t2.non_pay_year = '".$array_post['year']."'");
		$row_data = $this->db->get()->result_array();
		$array_data = array();
		$total_data = array();
		foreach($row_data as $key => $value){
			if($value['deduct_code']=='LOAN'){
				$this->db->select(array('t3.id as loan_type_id'));
				$this->db->from('coop_loan as t1');
				$this->db->join('coop_loan_name as t2','t1.loan_type = t2.loan_name_id','inner');
				$this->db->join('coop_loan_type as t3','t2.loan_type_id = t3.id','inner');
				$this->db->where("t1.id = '".$value['loan_id']."'");
				$rs_loan = $this->db->get()->result_array();
				if($value['pay_type']=='principal'){
					$array_data[$value['department']][$value['deduct_code']][$rs_loan[0]['loan_type_id']]['principal'] += $value['non_pay_amount'];
					$total_data[$value['deduct_code']][$rs_loan[0]['loan_type_id']]['principal'] += $value['non_pay_amount'];
				}else{
					$array_data[$value['department']][$value['deduct_code']][$rs_loan[0]['loan_type_id']]['interest'] += $value['non_pay_amount'];
					$total_data[$value['deduct_code']][$rs_loan[0]['loan_type_id']]['interest'] += $value['non_pay_amount'];
				}
			}else if($value['deduct_code']=='ATM'){
				if($value['pay_type']=='principal'){
					$array_data[$value['department']][$value['deduct_code']]['principal'] += $value['non_pay_amount'];
					$total_data[$value['deduct_code']]['principal'] += $value['non_pay_amount'];
				}else{
					$array_data[$value['department']][$value['deduct_code']]['interest'] += $value['non_pay_amount'];
					$total_data[$value['deduct_code']]['interest'] += $value['non_pay_amount'];
				}
			}else{
				$array_data[$value['department']][$value['deduct_code']] += $value['non_pay_amount'];
				$total_data[$value['deduct_code']] += $value['non_pay_amount'];
			}
			$array_data[$value['department']]['total'] += $value['non_pay_amount'];
			$total_data['total'] += $value['non_pay_amount'];
		}
		//echo"<pre>";print_r($array_data);exit;

		$where = " AND t1.mem_group_type = '1'";
		if($array_post['department']!=''){
			$where .= " AND t1.id = '".$array_post['department']."'";
		}
		$x=0;
		$join_arr = array();

		$this->paginater_all_preview->type(DB_TYPE);
		$this->paginater_all_preview->select(array('t1.id','t1.mem_group_id','t1.mem_group_name'));
		$this->paginater_all_preview->main_table('coop_mem_group as t1');
		$this->paginater_all_preview->where("1=1 ".$where);
		$this->paginater_all_preview->page_now(@$_GET["page"]);
		$this->paginater_all_preview->per_page(20);
		$this->paginater_all_preview->page_link_limit(28);
		$this->paginater_all_preview->page_limit_first(24);
		$this->paginater_all_preview->order_by('t1.mem_group_id');
		$this->paginater_all_preview->join_arr($join_arr);
		$row = $this->paginater_all_preview->paginater_process();
		foreach($row['data'] as $key => $value){
			foreach($value as $key2 => $value2){
				$row['data'][$key][$key2]['non_pay_data'] = $array_data[$value2['id']];
			}
		}
		//echo"<pre>";print_r($row['data']); exit;


		$arr_data['num_rows'] = $row['num_rows'];
		$arr_data['paging'] = $paging;
		$arr_data['data'] = $row['data'];
		$arr_data['page_all'] = $row['page_all'];
		$arr_data['month_text'] = $this->month_arr[$array_post['month']];
		$arr_data['year'] = $array_post['year'];
		$arr_data['total_data'] = $total_data;
		$this->preview_libraries->template_preview('report_processor_data/coop_report_non_pay_by_department',$arr_data);
	}

	function coop_report_non_pay_by_department_excel(){
		$arr_data = array();
		//echo"<pre>";($array_post);exit;
		$this->db->select(array('id','loan_type','loan_type_code'));
		$this->db->from('coop_loan_type');
		$this->db->order_by("order_by");
		$row = $this->db->get()->result_array();
		$loan_type = $row;
		$arr_data['loan_type'] = $loan_type;

		$where_data = '';
		if($_GET['department']!=''){
			$where_data .= " AND t3.department = '".$_GET['department']."'";
		}

		$this->db->select(array('t1.*','t3.member_id','t3.department'));
		$this->db->from('coop_non_pay_detail as t1');
		$this->db->join('coop_non_pay as t2','t1.non_pay_id = t2.non_pay_id','inner');
		$this->db->join('coop_mem_apply as t3','t2.member_id = t3.member_id','inner');
		$this->db->where("t2.non_pay_month = '".$_GET['month']."' AND t2.non_pay_year = '".$_GET['year']."'");
		$row_data = $this->db->get()->result_array();
		$array_data = array();
		$total_data = array();
		foreach($row_data as $key => $value){
			if($value['deduct_code']=='LOAN'){
				$this->db->select(array('t3.id as loan_type_id'));
				$this->db->from('coop_loan as t1');
				$this->db->join('coop_loan_name as t2','t1.loan_type = t2.loan_name_id','inner');
				$this->db->join('coop_loan_type as t3','t2.loan_type_id = t3.id','inner');
				$this->db->where("t1.id = '".$value['loan_id']."'");
				$rs_loan = $this->db->get()->result_array();
				if($value['pay_type']=='principal'){
					$array_data[$value['department']][$value['deduct_code']][$rs_loan[0]['loan_type_id']]['principal'] += $value['non_pay_amount'];
					$total_data[$value['deduct_code']][$rs_loan[0]['loan_type_id']]['principal'] += $value['non_pay_amount'];
				}else{
					$array_data[$value['department']][$value['deduct_code']][$rs_loan[0]['loan_type_id']]['interest'] += $value['non_pay_amount'];
					$total_data[$value['deduct_code']][$rs_loan[0]['loan_type_id']]['interest'] += $value['non_pay_amount'];
				}
			}else if($value['deduct_code']=='ATM'){
				if($value['pay_type']=='principal'){
					$array_data[$value['department']][$value['deduct_code']]['principal'] += $value['non_pay_amount'];
					$total_data[$value['deduct_code']]['principal'] += $value['non_pay_amount'];
				}else{
					$array_data[$value['department']][$value['deduct_code']]['interest'] += $value['non_pay_amount'];
					$total_data[$value['deduct_code']]['interest'] += $value['non_pay_amount'];
				}
			}else{
				$array_data[$value['department']][$value['deduct_code']] += $value['non_pay_amount'];
				$total_data[$value['deduct_code']] += $value['non_pay_amount'];
			}
			$array_data[$value['department']]['total'] += $value['non_pay_amount'];
			$total_data['total'] += $value['non_pay_amount'];
		}
		//echo"<pre>";print_r($array_data);exit;

		$where = " AND t1.mem_group_type = '1'";
		if($_GET['department']!=''){
			$where .= " AND t1.id = '".$_GET['department']."'";
		}
		$x=0;
		$join_arr = array();

		$this->paginater_all_preview->type(DB_TYPE);
		$this->paginater_all_preview->select(array('t1.id','t1.mem_group_id','t1.mem_group_name'));
		$this->paginater_all_preview->main_table('coop_mem_group as t1');
		$this->paginater_all_preview->where("1=1 ".$where);
		$this->paginater_all_preview->page_now(@$_GET["page"]);
		$this->paginater_all_preview->per_page(20);
		$this->paginater_all_preview->page_link_limit(28);
		$this->paginater_all_preview->page_limit_first(24);
		$this->paginater_all_preview->order_by('t1.mem_group_id');
		$this->paginater_all_preview->join_arr($join_arr);
		$row = $this->paginater_all_preview->paginater_process();
		foreach($row['data'] as $key => $value){
			foreach($value as $key2 => $value2){
				$row['data'][$key][$key2]['non_pay_data'] = $array_data[$value2['id']];
			}
		}
		//echo"<pre>";print_r($row['data']); exit;


		$arr_data['num_rows'] = $row['num_rows'];
		$arr_data['paging'] = $paging;
		$arr_data['data'] = $row['data'];
		$arr_data['page_all'] = $row['page_all'];
		$arr_data['month_text'] = $this->month_arr[$_GET['month']];
		$arr_data['year'] = $_GET['year'];
		$arr_data['total_data'] = $total_data;
		$arr_data['month_arr'] = $this->month_arr;
		$this->load->view('report_processor_data/coop_report_non_pay_by_department_excel',$arr_data);
	}

	function coop_report_non_pay_by_member_detail_backup_07_12_2018($array_post){
		$arr_data = array();

		$this->db->select(array('id','loan_type','loan_type_code'));
		$this->db->from('coop_loan_type');
		$this->db->order_by("order_by");
		$row = $this->db->get()->result_array();
		$loan_type = $row;
		$arr_data['loan_type'] = $loan_type;

		$this->db->select(array('t1.loan_name_id','t1.loan_type_id','t1.loan_name','t1.loan_name_description'));
		$this->db->from('coop_loan_name as t1');
		$this->db->join('coop_loan_type as t2','t1.loan_type_id = t2.id','inner');
		$this->db->where("t2.loan_type_code = 'normal'");
		$row = $this->db->get()->result_array();
		$arr_data['loan_name_normal'] = $row;

		$where_data = '';
		if(!empty($array_post['department'])){
			$where_data .= " AND t3.department = '".$array_post['department']."'";
		}
		if(!empty($array_post['faction'])){
			$where_data .= " AND t3.faction = '".$array_post['faction']."'";
		}
		if(!empty($array_post['level'])){
			$where_data .= " AND t3.level = '".$array_post['level']."'";
		}

		$this->db->select(array('t1.*','t3.member_id','t3.level','t3.firstname_th','t3.lastname_th','t4.prename_short'));
		$this->db->from('coop_non_pay_detail as t1');
		$this->db->join('coop_non_pay as t2','t1.non_pay_id = t2.non_pay_id','inner');
		$this->db->join('coop_mem_apply as t3','t2.member_id = t3.member_id','inner');
		$this->db->join('coop_prename as t4','t3.prename_id = t4.prename_id','left');
		$this->db->where("t2.non_pay_month = '".$array_post['month']."' AND t2.non_pay_year = '".$array_post['year']."'".$where_data);
		$row_data = $this->db->get()->result_array();
		$array_data = array();
		$total_data = array();
		$total_all_data = array();

		foreach($row_data as $key => $value){
			$array_data[$value['level']][$value['member_id']]['member_id'] = $value['member_id'];
			$array_data[$value['level']][$value['member_id']]['member_name'] = $value['prename_short'].$value['firstname_th']." ".$value['lastname_th'];
			if($value['deduct_code']=='LOAN'){
				$this->db->select(array('t3.loan_type_code','t2.loan_name_id'));
				$this->db->from('coop_loan as t1');
				$this->db->join('coop_loan_name as t2','t1.loan_type = t2.loan_name_id','inner');
				$this->db->join('coop_loan_type as t3','t2.loan_type_id = t3.id','inner');
				$this->db->where("t1.id = '".$value['loan_id']."'");
				$rs_loan = $this->db->get()->result_array();
				if($rs_loan[0]['loan_type_code'] == 'emergent'){
					if($value['pay_type']=='principal'){
						$array_data[$value['level']][$value['member_id']][$value['deduct_code']]['emergent']['principal'] += $value['non_pay_amount'];
						$total_data[$value['level']][$value['deduct_code']]['emergent']['principal'] += $value['non_pay_amount'];
						$total_all_data[$value['deduct_code']]['emergent']['principal'] += $value['non_pay_amount'];
					}else{
						$array_data[$value['level']][$value['member_id']][$value['deduct_code']]['emergent']['interest'] += $value['non_pay_amount'];
						$total_data[$value['level']][$value['deduct_code']]['emergent']['interest'] += $value['non_pay_amount'];
						$total_all_data[$value['deduct_code']]['emergent']['interest'] += $value['non_pay_amount'];
					}
				}else if($rs_loan[0]['loan_type_code'] == 'normal'){
					if($value['pay_type']=='principal'){
						$array_data[$value['level']][$value['member_id']][$value['deduct_code']]['normal']['principal'] += $value['non_pay_amount'];
						$total_data[$value['level']][$value['deduct_code']]['normal']['principal'] += $value['non_pay_amount'];
						$total_all_data[$value['deduct_code']]['normal']['principal'] += $value['non_pay_amount'];
					}else{
						$array_data[$value['level']][$value['member_id']][$value['deduct_code']]['normal']['interest'] += $value['non_pay_amount'];
						$total_data[$value['level']][$value['deduct_code']]['normal']['interest'] += $value['non_pay_amount'];
						$total_all_data[$value['deduct_code']]['normal']['interest'] += $value['non_pay_amount'];
					}
				}else if($rs_loan[0]['loan_type_code'] == 'special'){
					if($value['pay_type']=='principal'){
						$array_data[$value['level']][$value['member_id']][$value['deduct_code']]['special']['principal'] += $value['non_pay_amount'];
						$total_data[$value['level']][$value['deduct_code']]['special']['principal'] += $value['non_pay_amount'];
						$total_all_data[$value['deduct_code']]['special']['principal'] += $value['non_pay_amount'];
					}else{
						$array_data[$value['level']][$value['member_id']][$value['deduct_code']]['special']['interest'] += $value['non_pay_amount'];
						$total_data[$value['level']][$value['deduct_code']]['special']['interest'] += $value['non_pay_amount'];
						$total_all_data[$value['deduct_code']]['special']['interest'] += $value['non_pay_amount'];
					}
				}
			}else if($value['deduct_code']=='ATM'){
				if($value['pay_type']=='principal'){
					$array_data[$value['level']][$value['member_id']]["LOAN"]['emergent']['principal'] += $value['non_pay_amount'];
					$total_data[$value['level']]["LOAN"]['emergent']['principal'] += $value['non_pay_amount'];
					$total_all_data["LOAN"]['emergent']['principal'] += $value['non_pay_amount'];
				}else{
					$array_data[$value['level']][$value['member_id']]["LOAN"]['emergent']['interest'] += $value['non_pay_amount'];
					$total_data[$value['level']]["LOAN"]['emergent']['interest'] += $value['non_pay_amount'];
					$total_all_data["LOAN"]['emergent']['interest'] += $value['non_pay_amount'];
				}
			}else{
				$array_data[$value['level']][$value['member_id']][$value['deduct_code']] += $value['non_pay_amount'];
				$total_data[$value['level']][$value['deduct_code']] += $value['non_pay_amount'];
				$total_all_data[$value['deduct_code']] += $value['non_pay_amount'];
			}
			$array_data[$value['level']][$value['member_id']]['total'] += $value['non_pay_amount'];
			$total_data[$value['level']]['total'] += $value['non_pay_amount'];
			$total_all_data['total'] += $value['non_pay_amount'];
		}

		$array_data_paginate = array();
		$page_all = 0;
		
		$key_mem_counts = array();

		foreach($array_data as $key => $value){
			$page=1;
			$num_row=1;
			$is_end_department = 0;
			foreach($value as $key2 => $value2){
				$array_data_paginate[$key][$page][$key2] = $value2;
				$array_data_paginate[$key][$page][$key2]['num_row'] = $num_row;
				$num_row++;
				$key_mem_counts[$key] += 1;
				if($num_row == '21'){
					$page++;
					$page_all++;
					$num_row=1;
					$is_end_department = 1;
				} else {
					$is_end_department = 0;
				}
			}
			if (empty($is_end_department)) {
				$page_all++;
			}
		}

		$where = " AND t1.mem_group_type = '3'";
		if(!empty($array_post['department'])){
			$where .= " AND t3.id = '".$array_post['department']."'";
		}
		if(!empty($array_post['faction'])){
			$where .= " AND t2.id = '".$array_post['faction']."'";
		}
		if(!empty($array_post['level'])){
			$where .= " AND t1.id = '".$array_post['level']."'";
		}
		$this->db->select(array(
			't1.id',
			't1.mem_group_id',
			't1.mem_group_name',
			't2.mem_group_name as faction_name',
			't3.mem_group_name as department_name'
		));
		$this->db->from('coop_mem_group as t1');
		$this->db->join('coop_mem_group as t2','t1.mem_group_parent_id = t2.id','inner');
		$this->db->join('coop_mem_group as t3','t2.mem_group_parent_id = t3.id','inner');
		$this->db->where("1=1 ".$where);
		$this->db->order_by("t3.mem_group_id ASC");
		$row_group = $this->db->get()->result_array();
		foreach($row_group as $key => $value){
			$row_group[$key]['non_pay_data'] = $array_data_paginate[$value['id']];
		}

		$arr_data['month_text'] = $this->month_arr[$array_post['month']];
		$arr_data['year'] = $array_post['year'];
		$arr_data['total_data'] = $total_data;
		$arr_data['total_all_data'] = $total_all_data;
		$arr_data['row_group'] = $row_group;
		$arr_data['page_all'] = $page_all;
		$arr_data['key_mem_counts'] = $key_mem_counts;

		$this->preview_libraries->template_preview('report_processor_data/coop_report_non_pay_by_member_detail',$arr_data);
	}

	function coop_report_non_pay_by_member_detail($array_post){
		$arr_data = array();

		$this->db->select(array('id','loan_type','loan_type_code'));
		$this->db->from('coop_loan_type');
		$this->db->order_by("order_by");
		$row = $this->db->get()->result_array();
		$loan_type = $row;
		$arr_data['loan_type'] = $loan_type;

		$this->db->select(array('t1.loan_name_id','t1.loan_type_id','t1.loan_name','t1.loan_name_description'));
		$this->db->from('coop_loan_name as t1');
		$this->db->join('coop_loan_type as t2','t1.loan_type_id = t2.id','inner');
		$this->db->where("t2.loan_type_code = 'normal'");
		$row = $this->db->get()->result_array();
		$arr_data['loan_name_normal'] = $row;

		$where_data = '';
		if(!empty($array_post['department'])){
			$where_data .= " AND t3.department = '".$array_post['department']."'";
		}
		if(!empty($array_post['faction'])){
			$where_data .= " AND t3.faction = '".$array_post['faction']."'";
		}
		if(!empty($array_post['level'])){
			$where_data .= " AND t3.level = '".$array_post['level']."'";
		}

		$this->db->select(array('t1.*',
								't3.member_id',
								't3.level',
								't3.firstname_th',
								't3.lastname_th',
								't4.prename_short',
								't5.contract_number as contract_number',
								't6.contract_number as atm_contract_number'));
		$this->db->from('coop_non_pay_detail as t1');
		$this->db->join('coop_non_pay as t2','t1.non_pay_id = t2.non_pay_id','inner');
		$this->db->join('coop_mem_apply as t3','t2.member_id = t3.member_id','inner');
		$this->db->join('coop_prename as t4','t3.prename_id = t4.prename_id','left');
		$this->db->join('coop_loan as t5', 't1.loan_id = t5.id', "left");
		$this->db->join('coop_loan_atm as t6', 't1.loan_atm_id = t6.loan_atm_id', "left");
		$this->db->where("t2.non_pay_month = '".$array_post['month']."' AND t2.non_pay_year = '".$array_post['year']."'".$where_data);
		$row_data = $this->db->get()->result_array();
		$array_data = array();
		$total_data = array();
		$total_all_data = array();

		foreach($row_data as $key => $value){
			$array_data[$value['level']][$value['member_id']]['member_id'] = $value['member_id'];
			$array_data[$value['level']][$value['member_id']]['member_name'] = $value['prename_short'].$value['firstname_th']." ".$value['lastname_th'];
			$array_data[$value['level']][$value['member_id']]['fee'] = ($value['deduct_code']=="REGISTER_FEE") ? $value['non_pay_amount_balance'] : 0;
			if($value['deduct_code']=='LOAN'){
				$this->db->select(array('t3.loan_type_code','t2.loan_name_id'));
				$this->db->from('coop_loan as t1');
				$this->db->join('coop_loan_name as t2','t1.loan_type = t2.loan_name_id','inner');
				$this->db->join('coop_loan_type as t3','t2.loan_type_id = t3.id','inner');
				$this->db->where("t1.id = '".$value['loan_id']."'");
				$rs_loan = $this->db->get()->result_array();
				if($rs_loan[0]['loan_type_code'] == 'emergent'){
					if($value['pay_type']=='principal'){
						$array_data[$value['level']][$value['member_id']][$value['deduct_code']]['emergent'][$value['contract_number']]['principal'] = $value['non_pay_amount'];
						$array_data[$value['level']][$value['member_id']][$value['deduct_code']]['emergent'][$value['contract_number']]['contract_number'] = $value['contract_number'];
						$total_data[$value['level']][$value['deduct_code']]['emergent']['principal'] += $value['non_pay_amount'];
						$total_all_data[$value['deduct_code']]['emergent']['principal'] += $value['non_pay_amount'];
					}else{
						$array_data[$value['level']][$value['member_id']][$value['deduct_code']]['emergent'][$value['contract_number']]['interest'] = $value['non_pay_amount'];
						$array_data[$value['level']][$value['member_id']][$value['deduct_code']]['emergent'][$value['contract_number']]['contract_number'] = $value['contract_number'];
						$total_data[$value['level']][$value['deduct_code']]['emergent']['interest'] += $value['non_pay_amount'];
						$total_all_data[$value['deduct_code']]['emergent']['interest'] += $value['non_pay_amount'];
					}
				}else if($rs_loan[0]['loan_type_code'] == 'normal'){
					if($value['pay_type']=='principal'){
						$array_data[$value['level']][$value['member_id']][$value['deduct_code']]['normal'][$value['contract_number']]['principal'] = $value['non_pay_amount'];
						$array_data[$value['level']][$value['member_id']][$value['deduct_code']]['normal'][$value['contract_number']]['contract_number'] = $value['contract_number'];
						$total_data[$value['level']][$value['deduct_code']]['normal']['principal'] += $value['non_pay_amount'];
						$total_all_data[$value['deduct_code']]['normal']['principal'] += $value['non_pay_amount'];
					}else{
						$array_data[$value['level']][$value['member_id']][$value['deduct_code']]['normal'][$value['contract_number']]['interest'] = $value['non_pay_amount'];
						$array_data[$value['level']][$value['member_id']][$value['deduct_code']]['normal'][$value['contract_number']]['contract_number'] = $value['contract_number'];
						$total_data[$value['level']][$value['deduct_code']]['normal']['interest'] += $value['non_pay_amount'];
						$total_all_data[$value['deduct_code']]['normal']['interest'] += $value['non_pay_amount'];
					}
				}else if($rs_loan[0]['loan_type_code'] == 'special'){
					if($value['pay_type']=='principal'){
						$array_data[$value['level']][$value['member_id']][$value['deduct_code']]['special'][$value['contract_number']]['principal'] = $value['non_pay_amount'];
						$array_data[$value['level']][$value['member_id']][$value['deduct_code']]['special'][$value['contract_number']]['contract_number'] = $value['contract_number'];
						$total_data[$value['level']][$value['deduct_code']]['special']['principal'] += $value['non_pay_amount'];
						$total_all_data[$value['deduct_code']]['special']['principal'] += $value['non_pay_amount'];
					}else{
						$array_data[$value['level']][$value['member_id']][$value['deduct_code']]['special'][$value['contract_number']]['interest'] = $value['non_pay_amount'];
						$array_data[$value['level']][$value['member_id']][$value['deduct_code']]['special'][$value['contract_number']]['contract_number'] = $value['contract_number'];
						$total_data[$value['level']][$value['deduct_code']]['special']['interest'] += $value['non_pay_amount'];
						$total_all_data[$value['deduct_code']]['special']['interest'] += $value['non_pay_amount'];
					}
				}
			}else if($value['deduct_code']=='ATM'){
				if($value['pay_type']=='principal'){
					$array_data[$value['level']][$value['member_id']]["LOAN"]['emergent'][$value['atm_contract_number']]['principal'] = $value['non_pay_amount'];
					$array_data[$value['level']][$value['member_id']]["LOAN"]['emergent'][$value['atm_contract_number']]['contract_number'] = $value['atm_contract_number'];
					$total_data[$value['level']]["LOAN"]['emergent']['principal'] += $value['non_pay_amount'];
					$total_all_data["LOAN"]['emergent']['principal'] += $value['non_pay_amount'];
				}else{
					$array_data[$value['level']][$value['member_id']]["LOAN"]['emergent'][$value['atm_contract_number']]['interest'] = $value['non_pay_amount'];
					$array_data[$value['level']][$value['member_id']]["LOAN"]['emergent'][$value['atm_contract_number']]['contract_number'] = $value['atm_contract_number'];
					$total_data[$value['level']]["LOAN"]['emergent']['interest'] += $value['non_pay_amount'];
					$total_all_data["LOAN"]['emergent']['interest'] += $value['non_pay_amount'];
				}
			}else{
				$array_data[$value['level']][$value['member_id']][$value['deduct_code']] += $value['non_pay_amount'];
				$total_data[$value['level']][$value['deduct_code']] += $value['non_pay_amount'];
				$total_all_data[$value['deduct_code']] += $value['non_pay_amount'];
			}
			$array_data[$value['level']][$value['member_id']]['total'] += $value['non_pay_amount'];
			$total_data[$value['level']]['total'] += $value['non_pay_amount'];
			$total_all_data['total'] += $value['non_pay_amount'];
		}

		$index = 0;
		$format_datas = array();

		foreach($array_data as $group_id => $members) {
			foreach($members as $member_id => $member) {

				$max_loan_count = max(count($member["LOAN"]["normal"]), count($member["LOAN"]["emergent"]), count($member["LOAN"]["special"]));
				if (!empty($max_loan_count)) {
					$normal_array = !empty($member["LOAN"]["normal"]) ? array_values($member["LOAN"]["normal"]) : array();
					$emergent_array = !empty($member["LOAN"]["emergent"]) ? array_values($member["LOAN"]["emergent"]) : array();
					$special_array = !empty($member["LOAN"]["special"]) ? array_values($member["LOAN"]["special"]) : array();
					for($i = 0; $i < $max_loan_count; $i++) {
						$format_datas[$group_id][$member_id][$i] = $member;

						if(!empty($normal_array[$i])) {
							$format_datas[$group_id][$member_id][$i]["normal"]["principal"] = $normal_array[$i]["principal"];
							$format_datas[$group_id][$member_id][$i]["normal"]["interest"] = $normal_array[$i]["interest"];
							$format_datas[$group_id][$member_id][$i]["normal"]["contract_number"] = $normal_array[$i]["contract_number"];
						}
						if(!empty($emergent_array[$i])) {
							$format_datas[$group_id][$member_id][$i]["emergent"]["principal"] = $emergent_array[$i]["principal"];
							$format_datas[$group_id][$member_id][$i]["emergent"]["interest"] = $emergent_array[$i]["interest"];
							$format_datas[$group_id][$member_id][$i]["emergent"]["contract_number"] = $emergent_array[$i]["contract_number"];
						}
						if(!empty($special_array[$i])) {
							$format_datas[$group_id][$member_id][$i]["special"]["principal"] = $special_array[$i]["principal"];
							$format_datas[$group_id][$member_id][$i]["special"]["interest"] = $special_array[$i]["interest"];
							$format_datas[$group_id][$member_id][$i]["special"]["contract_number"] = $special_array[$i]["contract_number"];
						}
					}
				} else {
					$format_datas[$group_id][0][$member_id] = $member;
				}
			}
		}

		$array_data_paginate = array();
		$page_all = 0;
		
		$key_mem_counts = array();

		foreach($format_datas as $key => $value){
			$page=1;
			$num_row=1;
			$is_end_department = 0;
			foreach($value as $key2 => $value2){
				foreach($value2 as $key3 => $value3){
					$array_data_paginate[$key][$page][$key2][$key3] = $value3;
					$array_data_paginate[$key][$page][$key2][$key3]['num_row'] = $num_row;
					$num_row++;
					
					if($num_row == '21'){
						$page++;
						$page_all++;
						$num_row=1;
						$is_end_department = 1;
					} else {
						$is_end_department = 0;
					}
					$key_mem_counts[$key] += 1;
				}
				
			}
			if (empty($is_end_department)) {
				$page_all++;
			}
		}

		$where = " AND t1.mem_group_type = '3'";
		if(!empty($array_post['department'])){
			$where .= " AND t3.id = '".$array_post['department']."'";
		}
		if(!empty($array_post['faction'])){
			$where .= " AND t2.id = '".$array_post['faction']."'";
		}
		if(!empty($array_post['level'])){
			$where .= " AND t1.id = '".$array_post['level']."'";
		}
		$this->db->select(array(
			't1.id',
			't1.mem_group_id',
			't1.mem_group_name',
			't2.mem_group_name as faction_name',
			't3.mem_group_name as department_name'
		));
		$this->db->from('coop_mem_group as t1');
		$this->db->join('coop_mem_group as t2','t1.mem_group_parent_id = t2.id','inner');
		$this->db->join('coop_mem_group as t3','t2.mem_group_parent_id = t3.id','inner');
		$this->db->where("1=1 ".$where);
		$this->db->order_by("t3.mem_group_id ASC");
		$row_group = $this->db->get()->result_array();
		foreach($row_group as $key => $value){
			$row_group[$key]['non_pay_data'] = $array_data_paginate[$value['id']];
		}

		$arr_data['month_text'] = $this->month_arr[$array_post['month']];
		$arr_data['year'] = $array_post['year'];
		$arr_data['total_data'] = $total_data;
		$arr_data['total_all_data'] = $total_all_data;
		$arr_data['row_group'] = $row_group;
		$arr_data['page_all'] = $page_all;
		$arr_data['key_mem_counts'] = $key_mem_counts;

		$this->preview_libraries->template_preview('report_processor_data/coop_report_non_pay_by_member_detail',$arr_data);
	}

	function coop_report_non_pay_by_member_detail_excel_backup_07_12_2018(){
		$arr_data = array();

		$this->db->select(array('id','loan_type','loan_type_code'));
		$this->db->from('coop_loan_type');
		$this->db->order_by("order_by");
		$row = $this->db->get()->result_array();
		$loan_type = $row;
		$arr_data['loan_type'] = $loan_type;

		$this->db->select(array('t1.loan_name_id','t1.loan_type_id','t1.loan_name','t1.loan_name_description'));
		$this->db->from('coop_loan_name as t1');
		$this->db->join('coop_loan_type as t2','t1.loan_type_id = t2.id','inner');
		$this->db->where("t2.loan_type_code = 'normal'");
		$row = $this->db->get()->result_array();
		$arr_data['loan_name_normal'] = $row;

		$where_data = '';
		if($_GET['department']!=''){
			$where_data .= " AND t3.department = '".$_GET['department']."'";
		}
		if($_GET['faction']!=''){
			$where .= " AND t3.faction = '".$_GET['faction']."'";
		}
		if($_GET['level']!=''){
			$where .= " AND t3.level = '".$_GET['level']."'";
		}

		$this->db->select(array('t1.*','t3.member_id','t3.level','t3.firstname_th','t3.lastname_th','t4.prename_short'));
		$this->db->from('coop_non_pay_detail as t1');
		$this->db->join('coop_non_pay as t2','t1.non_pay_id = t2.non_pay_id','inner');
		$this->db->join('coop_mem_apply as t3','t2.member_id = t3.member_id','inner');
		$this->db->join('coop_prename as t4','t3.prename_id = t4.prename_id','left');
		$this->db->where("t2.non_pay_month = '".$_GET['month']."' AND t2.non_pay_year = '".$_GET['year']."'");
		$row_data = $this->db->get()->result_array();
		$array_data = array();
		$total_data = array();
		$total_all_data = array();

		foreach($row_data as $key => $value){
			$array_data[$value['level']][$value['member_id']]['member_id'] = $value['member_id'];
			$array_data[$value['level']][$value['member_id']]['member_name'] = $value['prename_short'].$value['firstname_th']." ".$value['lastname_th'];
			if($value['deduct_code']=='LOAN'){
				$this->db->select(array('t3.loan_type_code','t2.loan_name_id'));
				$this->db->from('coop_loan as t1');
				$this->db->join('coop_loan_name as t2','t1.loan_type = t2.loan_name_id','inner');
				$this->db->join('coop_loan_type as t3','t2.loan_type_id = t3.id','inner');
				$this->db->where("t1.id = '".$value['loan_id']."'");
				$rs_loan = $this->db->get()->result_array();
				if($rs_loan[0]['loan_type_code'] == 'emergent'){
					if($value['pay_type']=='principal'){
						$array_data[$value['level']][$value['member_id']][$value['deduct_code']]['emergent']['principal'] += $value['non_pay_amount'];
						$total_data[$value['level']][$value['deduct_code']]['emergent']['principal'] += $value['non_pay_amount'];
						$total_all_data[$value['deduct_code']]['emergent']['principal'] += $value['non_pay_amount'];
					}else{
						$array_data[$value['level']][$value['member_id']][$value['deduct_code']]['emergent']['interest'] += $value['non_pay_amount'];
						$total_data[$value['level']][$value['deduct_code']]['emergent']['interest'] += $value['non_pay_amount'];
						$total_all_data[$value['deduct_code']]['emergent']['interest'] += $value['non_pay_amount'];
					}
				}else if($rs_loan[0]['loan_type_code'] == 'normal'){
					if($value['pay_type']=='principal'){
						$array_data[$value['level']][$value['member_id']][$value['deduct_code']]['normal']['principal'] += $value['non_pay_amount'];
						$total_data[$value['level']][$value['deduct_code']]['normal']['principal'] += $value['non_pay_amount'];
						$total_all_data[$value['deduct_code']]['normal']['principal'] += $value['non_pay_amount'];
					}else{
						$array_data[$value['level']][$value['member_id']][$value['deduct_code']]['normal']['interest'] += $value['non_pay_amount'];
						$total_data[$value['level']][$value['deduct_code']]['normal']['interest'] += $value['non_pay_amount'];
						$total_all_data[$value['deduct_code']]['normal']['interest'] += $value['non_pay_amount'];
					}
				}else if($rs_loan[0]['loan_type_code'] == 'special'){
					if($value['pay_type']=='principal'){
						$array_data[$value['level']][$value['member_id']][$value['deduct_code']]['special']['principal'] += $value['non_pay_amount'];
						$total_data[$value['level']][$value['deduct_code']]['special']['principal'] += $value['non_pay_amount'];
						$total_all_data[$value['deduct_code']]['special']['principal'] += $value['non_pay_amount'];
					}else{
						$array_data[$value['level']][$value['member_id']][$value['deduct_code']]['special']['interest'] += $value['non_pay_amount'];
						$total_data[$value['level']][$value['deduct_code']]['special']['interest'] += $value['non_pay_amount'];
						$total_all_data[$value['deduct_code']]['special']['interest'] += $value['non_pay_amount'];
					}
				}
			}else if($value['deduct_code']=='ATM'){
				if($value['pay_type']=='principal'){
					$array_data[$value['level']][$value['member_id']]["LOAN"]['emergent']['principal'] += $value['non_pay_amount'];
					$total_data[$value['level']]["LOAN"]['emergent']['principal'] += $value['non_pay_amount'];
					$total_all_data["LOAN"]['emergent']['principal'] += $value['non_pay_amount'];
				}else{
					$array_data[$value['level']][$value['member_id']]["LOAN"]['emergent']['interest'] += $value['non_pay_amount'];
					$total_data[$value['level']]["LOAN"]['emergent']['interest'] += $value['non_pay_amount'];
					$total_all_data["LOAN"]['emergent']['interest'] += $value['non_pay_amount'];
				}
			}else{
				$array_data[$value['level']][$value['member_id']][$value['deduct_code']] += $value['non_pay_amount'];
				$total_data[$value['level']][$value['deduct_code']] += $value['non_pay_amount'];
				$total_all_data[$value['deduct_code']] += $value['non_pay_amount'];
			}
			$array_data[$value['level']][$value['member_id']]['total'] += $value['non_pay_amount'];
			$total_data[$value['level']]['total'] += $value['non_pay_amount'];
			$total_all_data['total'] += $value['non_pay_amount'];
		}

		$array_data_paginate = array();
		$page_all = 1;
		foreach($array_data as $key => $value){
			$page=1;
			$num_row=1;
			foreach($value as $key2 => $value2){
				$array_data_paginate[$key][$page][$key2] = $value2;
				$array_data_paginate[$key][$page][$key2]['num_row'] = $num_row;
				$num_row++;
				if($num_row == '21'){
					$page++;
					$page_all++;
				}
			}
		}
		$where = " AND t1.mem_group_type = '3'";
		if($_GET['department']!=''){
			$where .= " AND t3.id = '".$_GET['department']."'";
		}
		if($_GET['faction']!=''){
			$where .= " AND t2.id = '".$_GET['faction']."'";
		}
		if($_GET['level']!=''){
			$where .= " AND t1.id = '".$_GET['level']."'";
		}
		$this->db->select(array(
			't1.id',
			't1.mem_group_id',
			't1.mem_group_name',
			't2.mem_group_name as faction_name',
			't3.mem_group_name as department_name'
		));
		$this->db->from('coop_mem_group as t1');
		$this->db->join('coop_mem_group as t2','t1.mem_group_parent_id = t2.id','inner');
		$this->db->join('coop_mem_group as t3','t2.mem_group_parent_id = t3.id','inner');
		$this->db->where("1=1 ".$where);
		$this->db->order_by("t3.mem_group_id ASC");
		$row_group = $this->db->get()->result_array();
		foreach($row_group as $key => $value){
			$row_group[$key]['non_pay_data'] = $array_data_paginate[$value['id']];
		}
		//echo"<pre>";print_r($row_group);echo"</pre>";exit;
		$arr_data['month_text'] = $this->month_arr[$_GET['month']];
		$arr_data['year'] = $_GET['year'];
		$arr_data['total_data'] = $total_data;
		$arr_data['total_all_data'] = $total_all_data;
		$arr_data['row_group'] = $row_group;
		$arr_data['page_all'] = $page_all;
		$arr_data['month_arr'] = $this->month_arr;
		$this->load->view('report_processor_data/coop_report_non_pay_by_member_detail_excel',$arr_data);
	}

	function coop_report_non_pay_by_member_detail_excel(){
		$arr_data = array();

		$this->db->select(array('id','loan_type','loan_type_code'));
		$this->db->from('coop_loan_type');
		$this->db->order_by("order_by");
		$row = $this->db->get()->result_array();
		$loan_type = $row;
		$arr_data['loan_type'] = $loan_type;

		$this->db->select(array('t1.loan_name_id','t1.loan_type_id','t1.loan_name','t1.loan_name_description'));
		$this->db->from('coop_loan_name as t1');
		$this->db->join('coop_loan_type as t2','t1.loan_type_id = t2.id','inner');
		$this->db->where("t2.loan_type_code = 'normal'");
		$row = $this->db->get()->result_array();
		$arr_data['loan_name_normal'] = $row;

		$where_data = '';
		if($_GET['department']!=''){
			$where_data .= " AND t3.department = '".$_GET['department']."'";
		}
		if($_GET['faction']!=''){
			$where .= " AND t3.faction = '".$_GET['faction']."'";
		}
		if($_GET['level']!=''){
			$where .= " AND t3.level = '".$_GET['level']."'";
		}

		$this->db->select(array('t1.*',
								't3.member_id',
								't3.level',
								't3.firstname_th',
								't3.lastname_th',
								't4.prename_short',
								't5.contract_number as contract_number',
								't6.contract_number as atm_contract_number'
							));
		$this->db->from('coop_non_pay_detail as t1');
		$this->db->join('coop_non_pay as t2','t1.non_pay_id = t2.non_pay_id','inner');
		$this->db->join('coop_mem_apply as t3','t2.member_id = t3.member_id','inner');
		$this->db->join('coop_prename as t4','t3.prename_id = t4.prename_id','left');
		$this->db->join('coop_loan as t5', 't1.loan_id = t5.id', "left");
		$this->db->join('coop_loan_atm as t6', 't1.loan_atm_id = t6.loan_atm_id', "left");
		$this->db->where("t2.non_pay_month = '".$_GET['month']."' AND t2.non_pay_year = '".$_GET['year']."'");
		$row_data = $this->db->get()->result_array();
		$array_data = array();
		$total_data = array();
		$total_all_data = array();

		foreach($row_data as $key => $value){
			$array_data[$value['level']][$value['member_id']]['member_id'] = $value['member_id'];
			$array_data[$value['level']][$value['member_id']]['member_name'] = $value['prename_short'].$value['firstname_th']." ".$value['lastname_th'];
			$array_data[$value['level']][$value['member_id']]['fee'] = ($value['deduct_code']=="REGISTER_FEE") ? $value['non_pay_amount_balance'] : 0;
			if($value['deduct_code']=='LOAN'){
				$this->db->select(array('t3.loan_type_code','t2.loan_name_id'));
				$this->db->from('coop_loan as t1');
				$this->db->join('coop_loan_name as t2','t1.loan_type = t2.loan_name_id','inner');
				$this->db->join('coop_loan_type as t3','t2.loan_type_id = t3.id','inner');
				$this->db->where("t1.id = '".$value['loan_id']."'");
				$rs_loan = $this->db->get()->result_array();
				if($rs_loan[0]['loan_type_code'] == 'emergent'){
					if($value['pay_type']=='principal'){
						$array_data[$value['level']][$value['member_id']][$value['deduct_code']]['emergent'][$value['contract_number']]['principal'] = $value['non_pay_amount'];
						$array_data[$value['level']][$value['member_id']][$value['deduct_code']]['emergent'][$value['contract_number']]['contract_number'] = $value['contract_number'];
						$total_data[$value['level']][$value['deduct_code']]['emergent']['principal'] += $value['non_pay_amount'];
						$total_all_data[$value['deduct_code']]['emergent']['principal'] += $value['non_pay_amount'];
					}else{
						$array_data[$value['level']][$value['member_id']][$value['deduct_code']]['emergent'][$value['contract_number']]['interest'] = $value['non_pay_amount'];
						$array_data[$value['level']][$value['member_id']][$value['deduct_code']]['emergent'][$value['contract_number']]['contract_number'] = $value['contract_number'];
						$total_data[$value['level']][$value['deduct_code']]['emergent']['interest'] += $value['non_pay_amount'];
						$total_all_data[$value['deduct_code']]['emergent']['interest'] += $value['non_pay_amount'];
					}
				}else if($rs_loan[0]['loan_type_code'] == 'normal'){
					if($value['pay_type']=='principal'){
						$array_data[$value['level']][$value['member_id']][$value['deduct_code']]['normal'][$value['contract_number']]['principal'] = $value['non_pay_amount'];
						$array_data[$value['level']][$value['member_id']][$value['deduct_code']]['normal'][$value['contract_number']]['contract_number'] = $value['contract_number'];
						$total_data[$value['level']][$value['deduct_code']]['normal']['principal'] += $value['non_pay_amount'];
						$total_all_data[$value['deduct_code']]['normal']['principal'] += $value['non_pay_amount'];
					}else{
						$array_data[$value['level']][$value['member_id']][$value['deduct_code']]['normal'][$value['contract_number']]['interest'] = $value['non_pay_amount'];
						$array_data[$value['level']][$value['member_id']][$value['deduct_code']]['normal'][$value['contract_number']]['contract_number'] = $value['contract_number'];
						$total_data[$value['level']][$value['deduct_code']]['normal']['interest'] += $value['non_pay_amount'];
						$total_all_data[$value['deduct_code']]['normal']['interest'] += $value['non_pay_amount'];
					}
				}else if($rs_loan[0]['loan_type_code'] == 'special'){
					if($value['pay_type']=='principal'){
						$array_data[$value['level']][$value['member_id']][$value['deduct_code']]['special'][$value['contract_number']]['principal'] = $value['non_pay_amount'];
						$array_data[$value['level']][$value['member_id']][$value['deduct_code']]['special'][$value['contract_number']]['contract_number'] = $value['contract_number'];
						$total_data[$value['level']][$value['deduct_code']]['special']['principal'] += $value['non_pay_amount'];
						$total_all_data[$value['deduct_code']]['special']['principal'] += $value['non_pay_amount'];
					}else{
						$array_data[$value['level']][$value['member_id']][$value['deduct_code']]['special'][$value['contract_number']]['interest'] = $value['non_pay_amount'];
						$array_data[$value['level']][$value['member_id']][$value['deduct_code']]['special'][$value['contract_number']]['contract_number'] = $value['contract_number'];
						$total_data[$value['level']][$value['deduct_code']]['special']['interest'] += $value['non_pay_amount'];
						$total_all_data[$value['deduct_code']]['special']['interest'] += $value['non_pay_amount'];
					}
				}
			}else if($value['deduct_code']=='ATM'){
				if($value['pay_type']=='principal'){
					$array_data[$value['level']][$value['member_id']]["LOAN"]['emergent'][$value['atm_contract_number']]['principal'] = $value['non_pay_amount'];
					$array_data[$value['level']][$value['member_id']]["LOAN"]['emergent'][$value['atm_contract_number']]['contract_number'] = $value['atm_contract_number'];
					$total_data[$value['level']]["LOAN"]['emergent']['principal'] += $value['non_pay_amount'];
					$total_all_data["LOAN"]['emergent']['principal'] += $value['non_pay_amount'];
				}else{
					$array_data[$value['level']][$value['member_id']]["LOAN"]['emergent'][$value['atm_contract_number']]['interest'] = $value['non_pay_amount'];
					$array_data[$value['level']][$value['member_id']]["LOAN"]['emergent'][$value['atm_contract_number']]['contract_number'] = $value['atm_contract_number'];
					$total_data[$value['level']]["LOAN"]['emergent']['interest'] += $value['non_pay_amount'];
					$total_all_data["LOAN"]['emergent']['interest'] += $value['non_pay_amount'];
				}
			}else{
				$array_data[$value['level']][$value['member_id']][$value['deduct_code']] += $value['non_pay_amount'];
				$total_data[$value['level']][$value['deduct_code']] += $value['non_pay_amount'];
				$total_all_data[$value['deduct_code']] += $value['non_pay_amount'];
			}
			$array_data[$value['level']][$value['member_id']]['total'] += $value['non_pay_amount'];
			$total_data[$value['level']]['total'] += $value['non_pay_amount'];
			$total_all_data['total'] += $value['non_pay_amount'];
		}

		$index = 0;
		$format_datas = array();
		foreach($array_data as $group_id => $members) {
			foreach($members as $member_id => $member) {

				$max_loan_count = max(count($member["LOAN"]["normal"]), count($member["LOAN"]["emergent"]), count($member["LOAN"]["special"]));
				if (!empty($max_loan_count)) {
					$normal_array = !empty($member["LOAN"]["normal"]) ? array_values($member["LOAN"]["normal"]) : array();
					$emergent_array = !empty($member["LOAN"]["emergent"]) ? array_values($member["LOAN"]["emergent"]) : array();
					$special_array = !empty($member["LOAN"]["special"]) ? array_values($member["LOAN"]["special"]) : array();
					for($i = 0; $i < $max_loan_count; $i++) {
						$format_datas[$group_id][$member_id][$i] = $member;

						if(!empty($normal_array[$i])) {
							$format_datas[$group_id][$member_id][$i]["normal"]["principal"] = $normal_array[$i]["principal"];
							$format_datas[$group_id][$member_id][$i]["normal"]["interest"] = $normal_array[$i]["interest"];
							$format_datas[$group_id][$member_id][$i]["normal"]["contract_number"] = $normal_array[$i]["contract_number"];
						}
						if(!empty($emergent_array[$i])) {
							$format_datas[$group_id][$member_id][$i]["emergent"]["principal"] = $emergent_array[$i]["principal"];
							$format_datas[$group_id][$member_id][$i]["emergent"]["interest"] = $emergent_array[$i]["interest"];
							$format_datas[$group_id][$member_id][$i]["emergent"]["contract_number"] = $emergent_array[$i]["contract_number"];
						}
						if(!empty($special_array[$i])) {
							$format_datas[$group_id][$member_id][$i]["special"]["principal"] = $special_array[$i]["principal"];
							$format_datas[$group_id][$member_id][$i]["special"]["interest"] = $special_array[$i]["interest"];
							$format_datas[$group_id][$member_id][$i]["special"]["contract_number"] = $special_array[$i]["contract_number"];
						}
					}
				} else {
					$format_datas[$group_id][$member_id][0] = $member;
				}
			}
		}

		$array_data_paginate = array();
		$page_all = 1;
		foreach($format_datas as $key => $value){
			$page=1;
			$num_row=1;
			$is_end_department = 0;
			foreach($value as $key2 => $value2){
				foreach($value2 as $key3 => $value3){
					$array_data_paginate[$key][$page][$key2][$key3] = $value3;
					$array_data_paginate[$key][$page][$key2][$key3]['num_row'] = $num_row;
					$num_row++;
					
					if($num_row == '21'){
						$page++;
						$page_all++;
					}
				}
			}
		}
		$where = " AND t1.mem_group_type = '3'";
		if($_GET['department']!=''){
			$where .= " AND t3.id = '".$_GET['department']."'";
		}
		if($_GET['faction']!=''){
			$where .= " AND t2.id = '".$_GET['faction']."'";
		}
		if($_GET['level']!=''){
			$where .= " AND t1.id = '".$_GET['level']."'";
		}
		$this->db->select(array(
			't1.id',
			't1.mem_group_id',
			't1.mem_group_name',
			't2.mem_group_name as faction_name',
			't3.mem_group_name as department_name'
		));
		$this->db->from('coop_mem_group as t1');
		$this->db->join('coop_mem_group as t2','t1.mem_group_parent_id = t2.id','inner');
		$this->db->join('coop_mem_group as t3','t2.mem_group_parent_id = t3.id','inner');
		$this->db->where("1=1 ".$where);
		$this->db->order_by("t3.mem_group_id ASC");
		$row_group = $this->db->get()->result_array();
		foreach($row_group as $key => $value){
			$row_group[$key]['non_pay_data'] = $array_data_paginate[$value['id']];
		}
		//echo"<pre>";print_r($row_group);echo"</pre>";exit;
		$arr_data['month_text'] = $this->month_arr[$_GET['month']];
		$arr_data['year'] = $_GET['year'];
		$arr_data['total_data'] = $total_data;
		$arr_data['total_all_data'] = $total_all_data;
		$arr_data['row_group'] = $row_group;
		$arr_data['page_all'] = $page_all;
		$arr_data['month_arr'] = $this->month_arr;
		// echo "<pre>";
		// print_r($row_group);
		// echo "</pre>";
		// exit;
		$this->load->view('report_processor_data/coop_report_non_pay_by_member_detail_excel',$arr_data);
	}

	function coop_report_non_pay_by_member($array_post){
		$arr_data = array();

		$where_data = '';
		if($array_post['department']!=''){
			$where_data .= " AND t3.department = '".$array_post['department']."'";
		}
		if($array_post['faction']!=''){
			$where .= " AND t3.faction = '".$array_post['faction']."'";
		}
		if($array_post['level']!=''){
			$where .= " AND t3.level = '".$array_post['level']."'";
		}

		$this->db->select(array(
			't1.member_id',
			'SUM(t1.non_pay_amount) as non_pay_amount',
			't1.finance_month_profile_id',
			't3.department',
			't3.firstname_th',
			't3.lastname_th',
			't4.prename_full',
			't5.mem_group_name'
		));
		$this->db->from('coop_non_pay_detail as t1');
		$this->db->join('coop_non_pay as t2','t1.non_pay_id = t2.non_pay_id','inner');
		$this->db->join('coop_mem_apply as t3','t2.member_id = t3.member_id','inner');
		$this->db->join('coop_prename as t4','t3.prename_id = t4.prename_id','left');
		$this->db->join('coop_mem_group as t5','t3.level = t5.id','left');
		$this->db->where("t2.non_pay_month = '".$array_post['month']."' AND t2.non_pay_year = '".$array_post['year']."'");
		$this->db->group_by('t1.member_id');
		$row_data = $this->db->get()->result_array();
		$array_data = array();
		$total_data = array();
		$total_all_data = array();
		foreach($row_data as $key => $value){
			$array_data[$value['department']][$value['member_id']] = $value;
			$this->db->select(array(
				'SUM(t1.pay_amount) as pay_amount'
			));
			$this->db->from('coop_finance_month_detail as t1');
			$this->db->where("t1.member_id = '".$value['member_id']."' AND profile_id = '".$value['finance_month_profile_id']."'");
			$this->db->group_by('t1.member_id');
			$row = $this->db->get()->result_array();
			$array_data[$value['department']][$value['member_id']]['pay_amount'] = $row[0]['pay_amount'];
			$array_data[$value['department']][$value['member_id']]['balance'] = $row[0]['pay_amount'] - $value['non_pay_amount'];
			$array_data[$value['department']][$value['member_id']]['member_name'] = $value["prename_full"].$value["firstname_th"]." ".$value["lastname_th"];

			$array_data[$value['department']][$value['member_id']]['non_pay_reason'] = 'เงินเดือนไม่พอหัก';

			$total_data[$value['department']]['non_pay_amount'] += $value['non_pay_amount'];
			$total_data[$value['department']]['pay_amount'] += $row[0]['pay_amount'];
			$total_data[$value['department']]['balance'] += $row[0]['pay_amount'] - $value['non_pay_amount'];

			$total_all_data['non_pay_amount'] += $value['non_pay_amount'];
			$total_all_data['pay_amount'] += $row[0]['pay_amount'];
			$total_all_data['balance'] += $row[0]['pay_amount'] - $value['non_pay_amount'];
		}

		$array_data_paginate = array();
		$page_all = 0;
		foreach($array_data as $key => $value){
			$page=1;
			$num_row=1;
			$is_end_department = 0;
			foreach($value as $key2 => $value2){
				$array_data_paginate[$key][$page][$key2] = $value2;
				$array_data_paginate[$key][$page][$key2]['num_row'] = $num_row;
				$num_row++;
				$key_mem_counts[$key] += 1;
				if($num_row == '21'){
					$page++;
					$page_all++;
					$num_row=1;
					$is_end_department = 1;
				} else {
					$is_end_department = 0;
				}
			}
			if (empty($is_end_department)) {
				$page_all++;
			}
		}

		$where = " AND t1.mem_group_type = '1'";
		if($array_post['department']!=''){
			$where .= " AND t1.id = '".$array_post['department']."'";
		}
		$this->db->select(array(
			't1.id',
			't1.mem_group_id',
			't1.mem_group_name',
		));
		$this->db->from('coop_mem_group as t1');
		$this->db->where("1=1 ".$where);
		$this->db->order_by("t1.mem_group_id ASC");
		$row_group = $this->db->get()->result_array();
		foreach($row_group as $key => $value){
			$row_group[$key]['non_pay_data'] = $array_data_paginate[$value['id']];
		}
		//echo"<pre>";print_r($row_group);exit;
		$arr_data['month_text'] = $this->month_arr[$array_post['month']];
		$arr_data['year'] = $array_post['year'];
		$arr_data['total_data'] = $total_data;
		$arr_data['total_all_data'] = $total_all_data;
		$arr_data['row_group'] = $row_group;
		$arr_data['page_all'] = $page_all;
		$this->preview_libraries->template_preview('report_processor_data/coop_report_non_pay_by_member',$arr_data);
	}

	function coop_report_non_pay_by_member_excel(){
		$arr_data = array();

		$where_data = '';
		if($_GET['department']!=''){
			$where_data .= " AND t3.department = '".$_GET['department']."'";
		}
		if($_GET['faction']!=''){
			$where .= " AND t3.faction = '".$_GET['faction']."'";
		}
		if($_GET['level']!=''){
			$where .= " AND t3.level = '".$_GET['level']."'";
		}

		$this->db->select(array(
			't1.member_id',
			'SUM(t1.non_pay_amount) as non_pay_amount',
			't1.finance_month_profile_id',
			't3.department',
			't5.mem_group_name'
		));
		$this->db->from('coop_non_pay_detail as t1');
		$this->db->join('coop_non_pay as t2','t1.non_pay_id = t2.non_pay_id','inner');
		$this->db->join('coop_mem_apply as t3','t2.member_id = t3.member_id','inner');
		$this->db->join('coop_prename as t4','t3.prename_id = t4.prename_id','left');
		$this->db->join('coop_mem_group as t5','t3.level = t5.id','inner');
		$this->db->where("t2.non_pay_month = '".$_GET['month']."' AND t2.non_pay_year = '".$_GET['year']."'");
		$this->db->group_by('t1.member_id');
		$row_data = $this->db->get()->result_array();
		$array_data = array();
		$total_data = array();
		$total_all_data = array();
		foreach($row_data as $key => $value){
			$array_data[$value['department']][$value['member_id']] = $value;
			$this->db->select(array(
				'SUM(t1.pay_amount) as pay_amount'
			));
			$this->db->from('coop_finance_month_detail as t1');
			$this->db->where("t1.member_id = '".$value['member_id']."' AND profile_id = '".$value['finance_month_profile_id']."'");
			$this->db->group_by('t1.member_id');
			$row = $this->db->get()->result_array();
			$array_data[$value['department']][$value['member_id']]['pay_amount'] = $row[0]['pay_amount'];
			$array_data[$value['department']][$value['member_id']]['balance'] = $row[0]['pay_amount'] - $value['non_pay_amount'];

			$array_data[$value['department']][$value['member_id']]['non_pay_reason'] = 'เงินเดือนไม่พอหัก';

			$total_data[$value['department']]['non_pay_amount'] += $value['non_pay_amount'];
			$total_data[$value['department']]['pay_amount'] += $row[0]['pay_amount'];
			$total_data[$value['department']]['balance'] += $row[0]['pay_amount'] - $value['non_pay_amount'];

			$total_all_data['non_pay_amount'] += $value['non_pay_amount'];
			$total_all_data['pay_amount'] += $row[0]['pay_amount'];
			$total_all_data['balance'] += $row[0]['pay_amount'] - $value['non_pay_amount'];
		}
		//echo"<pre>";print_r($array_data);echo"</pre>";exit;

		$array_data_paginate = array();
		$page_all = 1;
		foreach($array_data as $key => $value){
			$page=1;
			$num_row=1;
			foreach($value as $key2 => $value2){
				$array_data_paginate[$key][$page][$key2] = $value2;
				$array_data_paginate[$key][$page][$key2]['num_row'] = $num_row;
				$num_row++;
				if($num_row == '21'){
					$page++;
					$page_all++;
				}
			}
		}

		$where = " AND t1.mem_group_type = '1'";
		if($array_post['department']!=''){
			$where .= " AND t1.id = '".$array_post['department']."'";
		}
		$this->db->select(array(
			't1.id',
			't1.mem_group_id',
			't1.mem_group_name',
		));
		$this->db->from('coop_mem_group as t1');
		$this->db->where("1=1 ".$where);
		$this->db->order_by("t1.mem_group_id ASC");
		$row_group = $this->db->get()->result_array();
		foreach($row_group as $key => $value){
			$row_group[$key]['non_pay_data'] = $array_data_paginate[$value['id']];
		}
		//echo"<pre>";print_r($row_group);exit;
		$arr_data['month_text'] = $this->month_arr[$_GET['month']];
		$arr_data['month_arr'] = $this->month_arr;
		$arr_data['month'] = $_GET['month'];
		$arr_data['year'] = $_GET['year'];
		$arr_data['total_data'] = $total_data;
		$arr_data['total_all_data'] = $total_all_data;
		$arr_data['row_group'] = $row_group;
		$arr_data['page_all'] = $page_all;
		$this->load->view('report_processor_data/coop_report_non_pay_by_member_excel',$arr_data);
	}

	function check_coop_non_pay() {
		if($_POST['type_report'] == '1'){
			$where_data = '';
			if($_POST['department']!=''){
				$where_data .= " AND t3.department = '".$_POST['department']."'";
			}
			if($_POST['faction']!=''){
				$where .= " AND t3.faction = '".$_POST['faction']."'";
			}
			if($_POST['level']!=''){
				$where .= " AND t3.level = '".$_POST['level']."'";
			}

			$this->db->select(array('t1.*','t3.member_id','t3.level','t3.firstname_th','t3.lastname_th'));
			$this->db->from('coop_non_pay_detail as t1');
			$this->db->join('coop_non_pay as t2','t1.non_pay_id = t2.non_pay_id','inner');
			$this->db->join('coop_mem_apply as t3','t2.member_id = t3.member_id','inner');
			$this->db->where("t2.non_pay_month = '".$_POST['month']."' AND t2.non_pay_year = '".$_POST['year']."'");
			$row_data = $this->db->get()->result_array();
			if(!empty($row_data)){
				echo "success";
			}else{
				echo "";
			}
		}else if($_POST['type_report'] == '2'){
			$where_data = '';
			if($_POST['department']!=''){
				$where_data .= " AND t3.department = '".$_POST['department']."'";
			}
			if($_POST['faction']!=''){
				$where .= " AND t3.faction = '".$_POST['faction']."'";
			}
			if($_POST['level']!=''){
				$where .= " AND t3.level = '".$_POST['level']."'";
			}

			$this->db->select(array(
				't1.member_id',
				'SUM(t1.non_pay_amount) as non_pay_amount',
				't1.finance_month_profile_id',
				't3.department',
				't5.mem_group_name'
			));
			$this->db->from('coop_non_pay_detail as t1');
			$this->db->join('coop_non_pay as t2','t1.non_pay_id = t2.non_pay_id','inner');
			$this->db->join('coop_mem_apply as t3','t2.member_id = t3.member_id','inner');
			$this->db->join('coop_mem_group as t5','t3.level = t5.id','inner');
			$this->db->where("t2.non_pay_month = '".$_POST['month']."' AND t2.non_pay_year = '".$_POST['year']."'");
			$this->db->group_by('t1.member_id');
			$row_data = $this->db->get()->result_array();
			if(!empty($row_data)){
				echo "success";
			}else{
				echo "";
			}
		} else {
			echo "success";
		}
	}

	function coop_report_refund(){
		$arr_data = array();

		$this->db->select(array('id','mem_group_name'));
		$this->db->from('coop_mem_group');
		$this->db->where("mem_group_type = '1'");
		$row = $this->db->get()->result_array();
		$arr_data['row_mem_group'] = $row;

		//Get loan type
		$this->db->select(array('loan_name_id','loan_name'));
		$this->db->from('coop_loan_name');
		$row = $this->db->get()->result_array();
		$arr_data['loan_names'] = $row;

		$this->libraries->template('report_processor_data/coop_report_refund',$arr_data);
	}

	function coop_report_refund_preview(){
		$array_post = $_POST;
		if($_POST['type_report'] == '0'){
			$this->coop_report_refund_by_department($array_post);
		}else if($_POST['type_report'] == '1'){
			$this->coop_report_refund_by_member($array_post);
		}else{
			echo "<script>document.location.href='".base_url(PROJECTPATH.'/report_processor_data/coop_report_non_pay')."';</script>";
		}
	}

	function coop_report_refund_by_department($array_post){
		set_time_limit(180);
		$arr_data = array();
		// department
		$title_date = "";
		if($array_post['month']!='' && $array_post['year']!='') {
			$day = '';
			$month = $array_post['month'];
			$year = ($array_post['year']);
			$title_date = " เดือน ".$this->month_arr[$month]." ปี ".($year);
		} else {
			$day = '';
			$month = '';
			$year = ($array_post['year']);
			$title_date = " ปี ".($year);
		}
		$arr_data["title_date"] = $title_date;

		//Declare condition
		$where = "t1.mem_group_type = '1'";
		if (!empty($array_post['department'])) {
			$where .= " AND t1.id = '".$array_post['department']."'";
		}

		//Get Value
		$this->paginater_all_preview->type(DB_TYPE);
		$this->paginater_all_preview->select(array('t1.id','t1.mem_group_name'));
		$this->paginater_all_preview->main_table('coop_mem_group as t1');
		$this->paginater_all_preview->where($where);
		$this->paginater_all_preview->page_now(@$_GET["page"]);
		$this->paginater_all_preview->per_page(20);
		$this->paginater_all_preview->page_link_limit(17);
		$this->paginater_all_preview->page_limit_first(12);
		$this->paginater_all_preview->order_by('t1.mem_group_id');
		$row = $this->paginater_all_preview->paginater_process();

		$header_type = array();
		$body_data = array();

		$where_return = "";
		if($array_post['return_status'] != "") {
			$where_return = " WHERE return_status = '".$array_post['return_status']."'";
		}

		foreach($row['data'] as $datas){
			foreach($datas as $data){
				$this->db->select("*");
				$this->db->from('coop_mem_group as t1');
				$this->db->join("coop_mem_group as t2","t2.mem_group_parent_id = t1.id","inner");
				$this->db->join("coop_mem_apply as t3","t3.level = t2.id","inner");
				$this->db->join("(SELECT * FROM coop_return_interest {$where_return}) as t4","t4.member_id = t3.member_id AND t4.return_month = '".$array_post['month']."' AND t4.return_year = '".$array_post['year']."'","inner");
				if($array_post['transfer_type'] != "") {
					$where_type = " WHERE transfer_type = '".$array_post['transfer_type']."'";
					$this->db->join("(SELECT * FROM coop_return_interest_profile {$where_type}) as t5","t4.return_profile_id = t5.return_profile_id","inner");
				}
				$this->db->where("t1.mem_group_parent_id = '".$data['id']."'");
				$return_interests = $this->db->get()->result_array();
				foreach($return_interests as $return_interest){
					if (!empty($return_interest['loan_atm_id'])) {
						$header_type['emergent']['atm']['id'] = 'atm';
						$header_type['emergent']['atm']['name'] = 'ฉุกเฉิน ATM';
						$body_data[$data['id']]['emergent']['atm'] += $return_interest['return_interest_amount'];
					}
					if (!empty($return_interest['loan_id'])) {
						$this->db->select(array('coop_loan.id','coop_loan_type.loan_type_code', 'coop_loan_name.loan_name_id', 'coop_loan_name.loan_name'));
						$this->db->from('coop_loan');
						$this->db->join("coop_loan_name","coop_loan_name.loan_name_id = coop_loan.loan_type","inner");
						$this->db->join("coop_loan_type","coop_loan_type.id = coop_loan_name.loan_type_id","inner");
						$this->db->where("coop_loan.id = '".$return_interest['loan_id']."'");
						$loan = $this->db->get()->row();
						$body_data[$data['id']][$loan->loan_type_code][$loan->loan_name_id][$return_interest['pay_type']] += $return_interest['return_interest_amount'];
						$header_type[$loan->loan_type_code][$loan->loan_name_id]['id'] = $loan->loan_name_id;
						$header_type[$loan->loan_type_code][$loan->loan_name_id]['name'] = $loan->loan_name;
					}
				}
			}
		}

		$arr_data['num_rows'] = $row['num_rows'];
		$arr_data['paging'] = $paging;
		$arr_data['data'] = $row['data'];
		$arr_data['page_all'] = $row['page_all'];
		$arr_data["body_data"] = $body_data;
		$arr_data["header_type"] = $header_type;
		$this->preview_libraries->template_preview('report_processor_data/coop_report_refund_by_department',$arr_data);
	}

	function coop_report_refund_by_member($array_post){
		$arr_data = array();
		// department
		$title_date = "";
		if($array_post['month']!='' && $array_post['year']!='') {
			$day = '';
			$month = $array_post['month'];
			$year = ($array_post['year']);
			$title_date = " เดือน ".$this->month_arr[$month]." ปี ".($year);
		} else {
			$day = '';
			$month = '';
			$year = ($array_post['year']);
			$title_date = " ปี ".($year);
		}
		$arr_data["title_date"] = $title_date;

		//Join Table
		$x = 0;
		$join_arr = array();
		$join_arr[$x]['table'] = 'coop_mem_group as t2';
		$join_arr[$x]['condition'] = 't1.mem_group_parent_id = t2.id';
		$join_arr[$x]['type'] = 'inner';
		$x++;
		$join_arr[$x]['table'] = 'coop_mem_group as t3';
		$join_arr[$x]['condition'] = 't2.mem_group_parent_id = t3.id';
		$join_arr[$x]['type'] = 'inner';
		$x++;
		$join_arr[$x]['table'] = 'coop_mem_apply as t4';
		$join_arr[$x]['condition'] = 't1.id = t4.level';
		$join_arr[$x]['type'] = 'inner';
		$x++;
		$join_arr[$x]['table'] = 'coop_prename as t5';
		$join_arr[$x]['condition'] = 't5.prename_id = t4.prename_id';
		$join_arr[$x]['type'] = 'left';
		$x++;
		$where_return = "";
		if($array_post['return_status'] != "") {
			$where_return = " WHERE return_status = '".$array_post['return_status']."'";
		}
		$join_arr[$x]['table'] = '(SELECT * FROM coop_return_interest '.$where_return.') as t6';
		$join_arr[$x]['condition'] = "t6.member_id = t4.member_id AND t6.return_month = '".$array_post['month']."' AND t6.return_year = '".$array_post['year']."'";
		$join_arr[$x]['type'] = 'inner';
		if($array_post['transfer_type'] != "") {
			$x++;
			$where_type = " WHERE transfer_type = '".$array_post['transfer_type']."'";
			$join_arr[$x]['table'] = '(SELECT * FROM coop_return_interest_profile '.$where_type.') as t7';
			$join_arr[$x]['condition'] = "t6.return_profile_id = t7.return_profile_id";
			$join_arr[$x]['type'] = 'inner';
		}

		//Declare condition
		$where = "t1.mem_group_type = '3'";

		//Department Type
		$department_id = null;
		if($array_post['level'] != ''){
			$where .= " AND t1.id = '".$array_post['level']."'";
			$department_id = $array_post['level'];
		}else if($array_post['faction'] != ''){
			$where .= " AND t2.id = '".$array_post['faction']."'";
			$department_id = $array_post['faction'];
		}else if($array_post['department'] != ''){
			$where .= " AND t3.id = '".$array_post['department']."'";
			$department_id = $array_post['department'];
		}

		//Set Value
		$this->paginater_all_preview->type(DB_TYPE);
		$this->paginater_all_preview->select(array('t1.id','t1.mem_group_name','t4.member_id', 't4.firstname_th', 't4.lastname_th', 't5.prename_full', 't6.return_interest_amount', 't6.loan_id', 't6.loan_atm_id', 't6.pay_type'));
		$this->paginater_all_preview->main_table('coop_mem_group as t1');
		$this->paginater_all_preview->where($where);
		$this->paginater_all_preview->page_now(@$_GET["page"]);
		$this->paginater_all_preview->per_page(20);
		$this->paginater_all_preview->page_link_limit(17);
		$this->paginater_all_preview->page_limit_first(13);
		$this->paginater_all_preview->order_by('t1.mem_group_id');
		$this->paginater_all_preview->group_by('t4.member_id');
		$this->paginater_all_preview->join_arr($join_arr);
		$row = $this->paginater_all_preview->paginater_process();
		$arr_data['num_rows'] = $row['num_rows'];
		$arr_data['paging'] = $paging;
		$arr_data['data'] = $row['data'];
		$arr_data['page_all'] = $row['page_all'];

		$body_data = array();
		$header_type = array();
		foreach($row['data'] as $datas){
			foreach($datas as $data){
				$this->db->select('*');
				$this->db->from('coop_return_interest');
				$this->db->where("member_id = '".$data['member_id']."' AND return_month = '".$array_post['month']."' AND return_year = '".$array_post['year']."'");
				$returns = $this->db->get()->result_array();
				foreach($returns as $return){
					if (!empty($return['loan_atm_id'])) {
						$header_type['emergent']['atm']['id'] = 'atm';
						$header_type['emergent']['atm']['name'] = 'ฉุกเฉิน ATM';
						$body_data[$data['member_id']]['emergent']['atm'] = $return['return_interest_amount'];
					}
					if (!empty($return['loan_id'])) {
						$this->db->select(array('coop_loan.id','coop_loan_type.loan_type_code', 'coop_loan_name.loan_name_id', 'coop_loan_name.loan_name'));
						$this->db->from('coop_loan');
						$this->db->join("coop_loan_name","coop_loan_name.loan_name_id = coop_loan.loan_type","inner");
						$this->db->join("coop_loan_type","coop_loan_type.id = coop_loan_name.loan_type_id","inner");
						$this->db->where("coop_loan.id = '".$return['loan_id']."'");
						$loan = $this->db->get()->row();
						$body_data[$data['member_id']][$loan->loan_type_code][$loan->loan_name_id][$data['pay_type']] = $return['return_interest_amount'];
						$header_type[$loan->loan_type_code][$loan->loan_name_id]['id'] = $loan->loan_name_id;
						$header_type[$loan->loan_type_code][$loan->loan_name_id]['name'] = $loan->loan_name;
					}
				}
			}
		}

		//Get Department Name
		$department_name = "";
		$queryDB = $this->db->query("SELECT mem_group_name FROM coop_mem_group WHERE id = '".$department_id."'");
		$query = $queryDB->row();

		$arr_data["department_name"] = $query->mem_group_name;
		$arr_data["body_data"] = $body_data;
		$arr_data["header_type"] = $header_type;

		$this->preview_libraries->template_preview('report_processor_data/coop_report_refund_by_member',$arr_data);
	}

	function check_coop_report_refund() {
		//Declare condition
		$where = "t1.mem_group_type = '3'";

		//Department Type
		$department_id = null;
		if($_POST['level'] != ''){
			$where .= " AND t1.id = '".$_POST['level']."'";
			$department_id = $_POST['level'];
		}else if($array_post['faction'] != ''){
			$where .= " AND t2.id = '".$_POST['faction']."'";
			$department_id = $_POST['faction'];
		}else if($array_post['department'] != ''){
			$where .= " AND t3.id = '".$_POST['department']."'";
			$department_id = $_POST['department'];
		}

		$where_return = "";
		if($_POST['return_status'] != "") {
			$where_return = " WHERE return_status = '".$_POST['return_status']."'";
		}

		//Get Data
		$this->db->select("t1.id");
		$this->db->from('coop_mem_group as t1');
		$this->db->join("coop_mem_group as t2","t1.mem_group_parent_id = t2.id","inner");
		$this->db->join("coop_mem_group as t3","t2.mem_group_parent_id = t3.id","inner");
		$this->db->join("coop_mem_apply as t4","t1.id = t4.level","inner");
		$this->db->join("(SELECT * FROM coop_return_interest ".$where_return.") as t6","t6.member_id = t4.member_id AND t6.return_month = '".$_POST['month']."' AND t6.return_year = '".$_POST['year']."'","inner");
		if($_POST['transfer_type'] != "") {
			$where_type = " WHERE transfer_type = '".$_POST['transfer_type']."'";
			$this->db->join("(SELECT * FROM coop_return_interest_profile {$where_type}) as t7","t6.return_profile_id = t7.return_profile_id","inner");
		}
		$this->db->where($where);
		$rs = $this->db->get()->result_array();

		//Return
		if(!empty($rs)){
			echo "success";
		}else{
			echo "";
		}
	}

	public function coop_report_receive_advance_month(){
		$arr_data = array();

		$this->db->select(array('id','mem_group_name'));
		$this->db->from('coop_mem_group');
		$this->db->where("mem_group_type = '1'");
		$row = $this->db->get()->result_array();
		$arr_data['row_mem_group'] = $row;

		$this->db->select('mem_type_id, mem_type_name');
		$this->db->from('coop_mem_type');
		$row = $this->db->get()->result_array();
		$arr_data['mem_type'] = $row;

		$this->libraries->template('report_processor_data/coop_report_receive_advance_month',$arr_data);
	}

	/*function coop_report_charged_department_preview(){
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

		$this->db->select(array('id','loan_type','loan_type_code'));
		$this->db->from('coop_loan_type');
		$this->db->order_by("order_by");
		$row = $this->db->get()->result_array();
		$arr_data['loan_type'] = $row;

		$arr_data['month_arr'] = array('1'=>'มกราคม','2'=>'กุมภาพันธ์','3'=>'มีนาคม','4'=>'เมษายน','5'=>'พฤษภาคม','6'=>'มิถุนายน','7'=>'กรกฎาคม','8'=>'สิงหาคม','9'=>'กันยายน','10'=>'ตุลาคม','11'=>'พฤศจิกายน','12'=>'ธันวาคม');
		$arr_data['month_short_arr'] = array('1'=>'ม.ค.','2'=>'ก.พ.','3'=>'มี.ค.','4'=>'เม.ย.','5'=>'พ.ค.','6'=>'มิ.ย.','7'=>'ก.ค.','8'=>'ส.ค.','9'=>'ก.ย.','10'=>'ต.ค.','11'=>'พ.ย.','12'=>'ธ.ค.');
		//echo"<pre>";print_r($arr_data['loan_type']);exit;
		if(@$_GET['type_department'] == '1'){
			$this->preview_libraries->template_preview('report_processor_data/coop_report_charged_department_preview',$arr_data);
		}else{
			$this->preview_libraries->template_preview('report_processor_data/coop_report_charged_level_preview',$arr_data);
		}

	}
*/
	function check_coop_report_receive_advance_month() {
		$receipt_con = 't8.profile_id = t9.finance_month_profile_id AND t8.member_id = t9.member_id';
		if (!empty($_POST['start_date'])) {
			$start_date_arr = explode("/", $_POST['start_date']);
			$start_date = ($start_date_arr[2] - 543)."-".$start_date_arr[1]."-".$start_date_arr[0]." 00:00:00";
			$receipt_con .= " AND t9.receipt_datetime >= '{$start_date}'";
		}
		if (!empty($_POST['end_date'])) {
			$end_date_arr = explode("/", $_POST['end_date']);
			$end_date = ($end_date_arr[2] - 543)."-".$end_date_arr[1]."-".$end_date_arr[0]." 23:59:59";
			$receipt_con .= " AND t9.receipt_datetime <= '{$end_date}'";
		}

		//Declare condition
		$where = "t1.mem_group_type = '3'";

		if(@$_POST['level'] != '') {
			$where .= " AND t1.id = '".$_POST['level']."'";
		} else if(@$_POST['faction'] != '') {
			$where .= " AND t2.id = '".$_POST['faction']."'";
		} else if(@$_POST['department'] != '') {
			$where .= " AND t3.id = '".$_POST['department']."'";
		}

		//รูปแบบสมาชิก
		if (!empty($_GET["mem_type"]) && !in_array("all", $_GET["mem_type"])){
			$where .= " AND t4.mem_type_id IN (".implode(',', $_GET["mem_type"]).")";
		}

		//Query Datas
		$datas = $this->db->select(array(
										't1.id',
										't1.mem_group_id',
										't1.mem_group_name',
										't4.member_id',
										't4.firstname_th',
										't4.lastname_th',
										't4.prename_id',
										't7.profile_id'
									))
						->from("(SELECT id, mem_group_id, mem_group_name, mem_group_parent_id, mem_group_type FROM coop_mem_group) as t1")
						->join("(SELECT id, mem_group_parent_id FROM coop_mem_group) as t2","t1.mem_group_parent_id = t2.id","inner")
						->join("(SELECT id, mem_group_parent_id FROM coop_mem_group) as t3","t2.mem_group_parent_id = t3.id","inner")
						->join("(SELECT level, member_id, firstname_th, lastname_th, mem_type_id, prename_id FROM coop_mem_apply) as t4","t1.id = t4.level","inner")
						->join("coop_finance_month_profile as t7","t7.profile_month = '".$_POST['month']."' AND t7.profile_year = '".$_POST['year']."'","inner")
						->where($where)
						->order_by('t1.mem_group_id, t4.member_id')
						->group_by('t4.member_id')
						->get()->result_array();

		// $member_ids = array_column($datas, 'member_id');

		// $details = $this->db->select(array(
		// 								't8.member_id',
		// 								'SUM(t8.pay_amount) as sum',
		// 								't9.pay_type',
		// 								't9.receipt_datetime'
		// 							))
		// 					->from("(SELECT member_id, profile_id, pay_amount FROM coop_finance_month_detail WHERE member_id IN (".implode(',',$member_ids).") AND profile_id = '".$datas[0]['profile_id']."') as t8")
		// 					->join("coop_receipt as t9",$receipt_con,"inner")
		// 					->group_by('t8.member_id , t9.pay_type')
		// 					->get()->result_array();

		$hasData = false;
		foreach($datas as $data) {
			$details = $this->db->select(array(
								't8.member_id',
								't9.pay_type',
								't9.receipt_datetime'
							))
					->from("(SELECT member_id, profile_id, pay_amount FROM coop_finance_month_detail WHERE member_id = '".$data["member_id"]."' AND profile_id = '".$data['profile_id']."') as t8")
					->join("coop_receipt as t9",$receipt_con,"inner")
					->get()->result_array();
			if(!empty($details)) {
				$hasData = true;
				break;
			}
		}

		if($hasData){
			echo "success";
		}else{
			echo "";
		}
	}

	//Backup
	function coop_report_receive_advance_month_preview_old() {
		// set_time_limit ( 180 );
		$arr_data = array();
		$title_date = "";
		if(@$_GET['month']!='' && @$_GET['year']!='') {
			$day = '';
			$month = @$_GET['month'];
			$year = (@$_GET['year']);
			$title_date = " เดือน ".$this->month_arr[$month]." ปี ".(@$year);
		} else {
			$day = '';
			$month = '';
			$year = (@$_GET['year']);
			$title_date = " ปี ".(@$year);
		}
		$arr_data["title_date"] = $title_date;

		//Join Table
		$x = 0;
		$join_arr = array();
		$join_arr[$x]['table'] = 'coop_mem_group as t2';
		$join_arr[$x]['condition'] = 't1.mem_group_parent_id = t2.id';
		$join_arr[$x]['type'] = 'inner';
		$x++;
		$join_arr[$x]['table'] = 'coop_mem_group as t3';
		$join_arr[$x]['condition'] = 't2.mem_group_parent_id = t3.id';
		$join_arr[$x]['type'] = 'inner';
		$x++;
		$join_arr[$x]['table'] = 'coop_mem_apply as t4';
		$join_arr[$x]['condition'] = 't1.id = t4.level';
		$join_arr[$x]['type'] = 'inner';
		$x++;
		$join_arr[$x]['table'] = 'coop_prename as t5';
		$join_arr[$x]['condition'] = 't5.prename_id = t4.prename_id';
		$join_arr[$x]['type'] = 'left';
		$x++;
		$join_arr[$x]['table'] = 'coop_mem_type as t6';
		$join_arr[$x]['condition'] = 't4.mem_type_id = t6.mem_type_id';
		$join_arr[$x]['type'] = 'left';
		$x++;
		$join_arr[$x]['table'] = 'coop_finance_month_profile as t7';
		$join_arr[$x]['condition'] = "t7.profile_month = ".$_GET['month']." AND t7.profile_year = ".$_GET['year'];
		$join_arr[$x]['type'] = 'inner';
		$x++;
		$join_arr[$x]['table'] = 'coop_finance_month_detail as t8';
		$join_arr[$x]['condition'] = 't4.member_id = t8.member_id AND t7.profile_id = t8.profile_id';
		$join_arr[$x]['type'] = 'inner';
		$x++;
		$join_arr[$x]['table'] = 'coop_receipt as t9';
		$receipt_con = 't8.profile_id = t9.finance_month_profile_id AND t8.member_id = t9.member_id';
		// $receipt_con = 't8.member_id = t9.member_id';
		if (!empty($_GET['start_date'])) {
			$start_date_arr = explode("/", $_GET['start_date']);
			$start_date = ($start_date_arr[2] - 543)."-".$start_date_arr[1]."-".$start_date_arr[0]." 00:00:00";
			$receipt_con .= " AND t9.receipt_datetime >= '{$start_date}'";
		}
		if (!empty($_GET['end_date'])) {
			$end_date_arr = explode("/", $_GET['end_date']);
			$end_date = ($end_date_arr[2] - 543)."-".$end_date_arr[1]."-".$end_date_arr[0]." 23:59:59";
			$receipt_con .= " AND t9.receipt_datetime <= '{$end_date}'";
		}
		$join_arr[$x]['condition'] = $receipt_con;
		$join_arr[$x]['type'] = 'inner';

		//Declare condition
		$where = "t1.mem_group_type = '3'";

		if(@$_GET['level'] != '') {
			$where .= " AND t1.id = '".$_GET['level']."'";
		} else if(@$_GET['faction'] != '') {
			$where .= " AND t2.id = '".$_GET['faction']."'";
		} else if(@$_GET['department'] != '') {
			$where .= " AND t3.id = '".$_GET['department']."'";
		}

		//รูปแบบสมาชิก
		if (!empty($_GET["mem_type"])) $where .= " AND t4.mem_type_id IN (".implode(',', $_GET["mem_type"]).")";

		//Set Value
		$this->paginater_all_preview->type(DB_TYPE);
		$this->paginater_all_preview->select(array('t1.id','t1.mem_group_id','t1.mem_group_name','t4.member_id', 't4.firstname_th', 't4.lastname_th', 't5.prename_full', 'SUM(t8.pay_amount) as sum', 't9.pay_type', 't9.receipt_datetime'));
		$this->paginater_all_preview->main_table('coop_mem_group as t1');
		$this->paginater_all_preview->where($where);
		$this->paginater_all_preview->page_now(@$_GET["page"]);
		$this->paginater_all_preview->per_page(20);
		$this->paginater_all_preview->page_link_limit(42);
		$this->paginater_all_preview->page_limit_first(35);
		$this->paginater_all_preview->order_by('t9.pay_type, t1.mem_group_id, t4.member_id');
		$this->paginater_all_preview->group_by('member_id , pay_type');

		$this->paginater_all_preview->join_arr($join_arr);
		$row = $this->paginater_all_preview->paginater_process();
		$arr_data['num_rows'] = $row['num_rows'];
		$arr_data['paging'] = $paging;
		$arr_data['data'] = $row['data'];
		$arr_data['page_all'] = $row['page_all'];
		// echo"<pre>";print_r($_GET['start_date']);exit;
		$this->preview_libraries->template_preview('report_processor_data/coop_report_receive_advance_month_preview',$arr_data);
	}

	function coop_report_receive_advance_month_preview() {
		set_time_limit ( 180 );

		$arr_data = array();
		$title_date = "";
		if(@$_GET['month']!='' && @$_GET['year']!='') {
			$day = '';
			$month = @$_GET['month'];
			$year = (@$_GET['year']);
			$title_date = " เดือน ".$this->month_arr[$month]." ปี ".(@$year);
		} else {
			$day = '';
			$month = '';
			$year = (@$_GET['year']);
			$title_date = " ปี ".(@$year);
		}
		$arr_data["title_date"] = $title_date;

		$receipt_con = 't8.profile_id = t9.finance_month_profile_id AND t8.member_id = t9.member_id';
		if (!empty($_GET['start_date'])) {
			$start_date_arr = explode("/", $_GET['start_date']);
			$start_date = ($start_date_arr[2] - 543)."-".$start_date_arr[1]."-".$start_date_arr[0]." 00:00:00";
			$receipt_con .= " AND t9.receipt_datetime >= '{$start_date}'";
		}
		if (!empty($_GET['end_date'])) {
			$end_date_arr = explode("/", $_GET['end_date']);
			$end_date = ($end_date_arr[2] - 543)."-".$end_date_arr[1]."-".$end_date_arr[0]." 23:59:59";
			$receipt_con .= " AND t9.receipt_datetime <= '{$end_date}'";
		}

		//Declare condition
		$where = "t1.mem_group_type = '3'";

		if(@$_GET['level'] != '') {
			$where .= " AND t1.id = '".$_GET['level']."'";
		} else if(@$_GET['faction'] != '') {
			$where .= " AND t2.id = '".$_GET['faction']."'";
		} else if(@$_GET['department'] != '') {
			$where .= " AND t3.id = '".$_GET['department']."'";
		}

		//รูปแบบสมาชิก
		if (!empty($_GET["mem_type"]) && !in_array("all", $_GET["mem_type"])){
			$where .= " AND t4.mem_type_id IN (".implode(',', $_GET["mem_type"]).")";
		}

		$date_range['start'] 	= date("Y-m", strtotime(($year-543)."-".$month."-01")	) . "-01";
		
		//Query Datas
		$datas = $this->db->select(array(
										't1.id',
										't1.mem_group_id',
										't1.mem_group_name',
										't4.member_id',
										't4.firstname_th',
										't4.lastname_th',
										't4.lastname_th',
										't4.mem_type_id',
										't10.mem_type_name',
										't7.profile_id'
									))
						->from("(SELECT id, mem_group_id, mem_group_name, mem_group_parent_id, mem_group_type FROM coop_mem_group) as t1")
						->join("(SELECT id, mem_group_parent_id FROM coop_mem_group) as t2","t1.mem_group_parent_id = t2.id","inner")
						->join("(SELECT id, mem_group_parent_id FROM coop_mem_group) as t3","t2.mem_group_parent_id = t3.id","inner")
						->join("(SELECT IF (
											(
												SELECT
													level_old
												FROM
													coop_mem_group_move
												WHERE
													date_move >= '".$date_range['start']."'
												AND coop_mem_group_move.member_id = coop_mem_apply.member_id
												ORDER BY
													date_move ASC
												LIMIT 1
											),
											(
												SELECT
													level_old
												FROM
													coop_mem_group_move
												WHERE
													date_move >= '".$date_range['start']."'
												AND coop_mem_group_move.member_id = coop_mem_apply.member_id
												ORDER BY
													date_move ASC
												LIMIT 1
											),
											coop_mem_apply. level
										) AS level, member_id, firstname_th, lastname_th, mem_type_id, prename_id FROM coop_mem_apply WHERE member_status != '3') as t4","t1.id = t4.level","inner")
						->join("coop_finance_month_profile as t7","t7.profile_month = '".$_GET['month']."' AND t7.profile_year = '".$_GET['year']."'","inner")
						->join("coop_mem_type as t10", "t4.mem_type_id = t10.mem_type_id", "left")
						->where($where)
						->order_by('t1.mem_group_id, t4.member_id')
						->group_by('t4.member_id')
						->get()->result_array();
		//echo $this->db->last_query(); exit;
		$member_ids = array_column($datas, 'member_id');

		$details = $this->db->select(array(
										't8.member_id',
										'SUM(t8.real_pay_amount) as sum',
										't9.pay_type',
										't9.receipt_datetime'
									))
							->from("(SELECT member_id, profile_id, pay_amount,real_pay_amount FROM coop_finance_month_detail WHERE member_id IN (".implode(',',$member_ids).") AND profile_id = '".$datas[0]['profile_id']."') as t8")
							->join("coop_receipt as t9",$receipt_con,"inner")
							->group_by('t8.member_id , t9.pay_type')
							->get()->result_array();

		$num_rows = count($details);

		$page_num = 1;
		$all_page = ceil($num_rows/100);

		$rawArray = array();
		foreach($details as $index => $detail) {
			$memberData = $datas[array_search($detail['member_id'], $member_ids)];
			$prename = $this->db->select('prename_full')->from('coop_prename')->where('prename_id = '.$memberData['prename_id'])->get()->row();
			$memberData["prename_full"] = $prename->prename_full;
			$rawArray[] = array_merge($detail,$memberData);
		}

		$page_get = !empty($_GET['page']) ? $_GET['page'] : 1;
		$paging = $this->pagination_center->paginating(intval($page_get), $all_page, 1, 20,@$_GET);//$page_now = 1, $row_total = 1, $per_page = 20, $page_limit = 20
		$pay_types = array_column($rawArray, 'pay_type');
		$mem_group_ids = array_column($rawArray, 'mem_group_id');
		$member_ids = array_column($rawArray, 'member_id');
		array_multisort($pay_types, SORT_ASC, $mem_group_ids, SORT_ASC, $member_ids, SORT_ASC, $rawArray);

		foreach($rawArray as $index => $data) {
			if ($index == 35 || (($index - 35)%42) == 0) {
				$page_num++;
			}
			$row['data'][$page_num][] = $data;
		}

		$arr_data['num_rows'] = $num_rows;
		$arr_data['data'] = $row['data'];
		$arr_data['page_all'] = $page_num;
		// $arr_data['paging'] = $paging;
		$this->preview_libraries->template_preview('report_processor_data/coop_report_receive_advance_month_preview',$arr_data);
	}

	function coop_report_return_receipt() {
		$arr_data = array();

		$this->db->select(array('id','mem_group_name'));
		$this->db->from('coop_mem_group');
		$this->db->where("mem_group_type = '1'");
		$row = $this->db->get()->result_array();
		$arr_data['row_mem_group'] = $row;

		$this->db->select('mem_type_id, mem_type_name');
		$this->db->from('coop_mem_type');
		$row = $this->db->get()->result_array();
		$arr_data['mem_type'] = $row;

		$this->libraries->template('report_processor_data/coop_report_return_receipt',$arr_data);
	}

	function coop_report_return_receipt_preview() {
		$arr_data = array();
		$title_date = "";
		if(@$_GET['month']!='' && @$_GET['year']!='') {
			$day = '';
			$month = @$_GET['month'];
			$year = (@$_GET['year']);
			$title_date = " เดือน ".$this->month_arr[$month]." ปี ".(@$year);
		} else {
			$day = '';
			$month = '';
			$year = (@$_GET['year']);
			$title_date = " ปี ".(@$year);
		}
		$arr_data["title_date"] = $title_date;

		$profile_id = $this->db->get_where("coop_finance_month_profile", array(
			"profile_month" => $month,
			"profile_year" => $year,
		))->result()[0]->profile_id;

		$date_range['start'] = date("Y-m", strtotime(($year-543)."-".$month."-01")	) . "-01";
		
		//Join Table
		$x = 0;
		$join_arr = array();
		$join_arr[$x]['table'] = 'coop_mem_group as t2';
		$join_arr[$x]['condition'] = 't1.mem_group_parent_id = t2.id';
		$join_arr[$x]['type'] = 'inner';
		$x++;
		$join_arr[$x]['table'] = 'coop_mem_group as t3';
		$join_arr[$x]['condition'] = 't2.mem_group_parent_id = t3.id';
		$join_arr[$x]['type'] = 'inner';
		//$x++;
		//$join_arr[$x]['table'] = 'coop_mem_apply as t4';
		//$join_arr[$x]['condition'] = 't1.id = t4.level';
		//$join_arr[$x]['type'] = 'inner';
		$x++;
		$join_arr[$x]['table'] = "(SELECT IF ((SELECT level_old FROM coop_mem_group_move WHERE date_move >= '".$date_range['start']."' AND coop_mem_group_move.member_id = coop_mem_apply.member_id ORDER BY date_move ASC LIMIT 1 ),(SELECT level_old FROM coop_mem_group_move WHERE date_move >= '".$date_range['start']."' AND coop_mem_group_move.member_id = coop_mem_apply.member_id ORDER BY date_move ASC LIMIT 1 ),coop_mem_apply. level ) AS level, member_id, firstname_th, lastname_th, mem_type_id, prename_id,mem_type FROM coop_mem_apply WHERE member_status != '3') as t4";
		$join_arr[$x]['condition'] = 't1.id = t4.level';
		$join_arr[$x]['type'] = 'inner';
		$x++;
		$join_arr[$x]['table'] = 'coop_prename as t5';
		$join_arr[$x]['condition'] = 't5.prename_id = t4.prename_id';
		$join_arr[$x]['type'] = 'left';
		$x++;
		$join_arr[$x]['table'] = '(select member_id, run_status, sum(pay_amount) as non_pay_amount_balance from coop_finance_month_detail where profile_id = '.$profile_id.' group BY member_id) as t6';
		$none_pay_cond = "";
		// if (!empty($_GET['month'])) $none_pay_cond .= " AND t6.non_pay_month = ".$_GET['month'];
		// if (!empty($_GET['year'])) $none_pay_cond .= " AND t6.non_pay_year = ".$_GET['year'];
		$join_arr[$x]['condition'] = 't6.member_id = t4.member_id AND t6.run_status = "0"'.$none_pay_cond;
		$join_arr[$x]['type'] = 'inner';

		//Declare condition
		$where = "t1.mem_group_type = '3'";
		//$where .= "AND t4.mem_type = 1";
		$where .= "AND t4.mem_type <> 3";
		// $where .= "AND t4.mem_type = 1 and t6.non_pay_amount_balance > 1 ";
		if (@$_GET['search_type'] == 'id') {
			//รูปแบบหน่วยงาน
			if(@$_GET['level'] != ''){
				$where .= " AND t1.id = '".$_GET['level']."'";
			}else if(@$_GET['faction'] != ''){
				$where .= " AND t2.id = '".$_GET['faction']."'";
			}else if(@$_GET['department'] != ''){
				$where .= " AND t3.id = '".$_GET['department']."'";
			}
		} else if ($_GET['search_type'] == "code") {
			if (!empty($_GET['department_id_from'])) $where .= " AND CAST(t4.member_id AS INTEGER) >= CAST(".$_GET['department_id_from']." AS INTEGER)";
			if (!empty($_GET['department_id_to'])) $where .= " AND CAST(t4.member_id AS INTEGER) <= CAST(".$_GET['department_id_to']." AS INTEGER)";
		}
		//รูปแบบสมาชิก
		if (!empty($_GET["mem_type"]) && !in_array("all", $_GET["mem_type"])) $where .= " AND t4.mem_type_id IN (".implode(',', $_GET["mem_type"]).")";

		//Set Value
		$this->paginater_all_preview->type(DB_TYPE);
		$this->paginater_all_preview->select(array('t1.id','t1.mem_group_id','t1.mem_group_name','t4.member_id', 't4.firstname_th', 't4.lastname_th', 't5.prename_full', 't6.non_pay_amount_balance'));
		$this->paginater_all_preview->main_table('coop_mem_group as t1');
		$this->paginater_all_preview->where($where);
		$this->paginater_all_preview->page_now(@$_GET["page"]);
		$this->paginater_all_preview->per_page(20);
		$this->paginater_all_preview->page_link_limit(43);
		$this->paginater_all_preview->page_limit_first(37);
		$this->paginater_all_preview->order_by('t1.mem_group_id ASC,t4.member_id ASC');
		$this->paginater_all_preview->join_arr($join_arr);
		$row = $this->paginater_all_preview->paginater_process();
		$arr_data['num_rows'] = $row['num_rows'];
		$arr_data['paging'] = $paging;
		$arr_data['data'] = $row['data'];
		$arr_data['page_all'] = $row['page_all'];
		//echo $this->db->last_query(); exit;
		$this->preview_libraries->template_preview('report_processor_data/coop_report_return_receipt_preview',$arr_data);
	}

	function check_coop_report_return_receipt() {
		$arr_data = array();

		//Declare condition
		$where = "t1.mem_group_type = '3'";
		$where .= "AND t4.mem_type = 1";
		if (@$_POST['search_type'] == 'id') {
			//department type
			if(@$_POST['level'] != ''){
				$where .= " AND t1.id = '".$_POST['level']."'";
			}else if(@$_POST['faction'] != ''){
				$where .= " AND t2.id = '".$_POST['faction']."'";
			}else if(@$_POST['department'] != ''){
				$where .= " AND t3.id = '".$_POST['department']."'";
			}
		} else if ($_POST['search_type'] == "code") {
			if (!empty($_POST['department_id_from'])) $where .= " AND CAST(t4.member_id AS INTEGER) >= CAST(".$_POST['department_id_from']." AS INTEGER)";
			if (!empty($_POST['department_id_to'])) $where .= " AND CAST(t4.member_id AS INTEGER) <= CAST(".$_POST['department_id_to']." AS INTEGER)";
		}

		$profile_id = $this->db->get_where("coop_finance_month_profile", array(
			"profile_month" => $_POST['month'],
			"profile_year" => $_POST['year'],
		))->result()[0]->profile_id;

		//member type
		if (!empty($_POST["mem_type"]) && !in_array("all", $_POST["mem_type"])) $where .= " AND t4.mem_type_id IN (".implode(',', $_POST["mem_type"]).")";

		//Get Data
		$this->db->select('t1.id');
		$this->db->from('coop_mem_group as t1');
		$this->db->join("coop_mem_group as t2","t1.mem_group_parent_id = t2.id","inner");
		$this->db->join("coop_mem_group as t3","t2.mem_group_parent_id = t3.id","inner");
		$this->db->join("coop_mem_apply as t4","t1.id = t4.level","inner");
		$none_pay_cond = "";
		// if (!empty($_POST['month'])) $none_pay_cond .= " AND t6.non_pay_month = ".$_POST['month'];
		// if (!empty($_POST['year'])) $none_pay_cond .= " AND t6.non_pay_year = ".$_POST['year'];
		$this->db->join("(select member_id, run_status, sum(pay_amount) as non_pay_amount_balance from coop_finance_month_detail where profile_id = ".$profile_id." group BY member_id) as t6", "t6.member_id = t4.member_id ".$none_pay_cond, "inner");
		$this->db->where($where);
		$this->db->where("t6.run_status = '0'");
		// $this->db->where("t6.non_pay_amount_balance > 0");
		$rs = $this->db->get()->result_array();
		if(!empty($rs)){
			echo "success";
		}else{
			echo "";
		}
	}

	function coop_report_return_receipt_excel() {
		$arr_data = array();
		$title_date = "";
		if(@$_GET['month']!='' && @$_GET['year']!='') {
			$day = '';
			$month = @$_GET['month'];
			$year = (@$_GET['year']);
			$title_date = " เดือน ".$this->month_arr[$month]." ปี ".(@$year);
		} else {
			$day = '';
			$month = '';
			$year = (@$_GET['year']);
			$title_date = " ปี ".(@$year);
		}
		$arr_data["title_date"] = $title_date;

		$profile_id = $this->db->get_where("coop_finance_month_profile", array(
			"profile_month" => $month,
			"profile_year" => $year,
		))->result()[0]->profile_id;
		
		$date_range['start'] = date("Y-m", strtotime(($year-543)."-".$month."-01")	) . "-01";
		
		$where = "t1.mem_group_type = '3'";
		//$where .= "AND t4.mem_type = 1";
		$where .= "AND t4.mem_type <> 3";
		if (@$_GET['search_type'] == 'id') {
			//department type
			if(@$_GET['level'] != ''){
				$where .= " AND t1.id = '".$_GET['level']."'";
			}else if(@$_GET['faction'] != ''){
				$where .= " AND t2.id = '".$_GET['faction']."'";
			}else if(@$_GET['department'] != ''){
				$where .= " AND t3.id = '".$_GET['department']."'";
			}
		} else if ($_GET['search_type'] == "code") {
			if (!empty($_GET['department_id_from'])) $where .= " AND CAST(t4.member_id AS INTEGER) >= CAST(".$_GET['department_id_from']." AS INTEGER)";
			if (!empty($_GET['department_id_to'])) $where .= " AND CAST(t4.member_id AS INTEGER) <= CAST(".$_GET['department_id_to']." AS INTEGER)";
		}

		//member type
		if (!empty($_POST["mem_type"]) && !in_array("all", $_POST["mem_type"])) $where .= " AND t4.mem_type_id IN (".implode(',', $_POST["mem_type"]).")";

		//Get Data
		$this->db->select(array('t1.id','t1.mem_group_id','t1.mem_group_name','t4.member_id', 't4.firstname_th', 't4.lastname_th', 't5.prename_full', 't6.non_pay_amount_balance'));
		$this->db->from("coop_mem_group as t1");
		$this->db->join("coop_mem_group as t2","t1.mem_group_parent_id = t2.id","inner");
		$this->db->join("coop_mem_group as t3","t2.mem_group_parent_id = t3.id","inner");
		$this->db->join("(SELECT IF ((SELECT level_old FROM coop_mem_group_move WHERE date_move >= '".$date_range['start']."' AND coop_mem_group_move.member_id = coop_mem_apply.member_id ORDER BY date_move ASC LIMIT 1 ),(SELECT level_old FROM coop_mem_group_move WHERE date_move >= '".$date_range['start']."' AND coop_mem_group_move.member_id = coop_mem_apply.member_id ORDER BY date_move ASC LIMIT 1 ),coop_mem_apply. level ) AS level, member_id, firstname_th, lastname_th, mem_type_id, prename_id,mem_type FROM coop_mem_apply WHERE member_status != '3') as t4","t1.id = t4.level","inner");
		$this->db->join("coop_prename as t5","t5.prename_id = t4.prename_id","left");
		//$none_pay_cond = "";
		//if (!empty($_GET['month'])) $none_pay_cond .= " AND t6.non_pay_month = ".$_GET['month'];
		//if (!empty($_GET['year'])) $none_pay_cond .= " AND t6.non_pay_year = ".$_GET['year'];
		//$this->db->join("coop_non_pay as t6","t6.member_id = t4.member_id AND t6.non_pay_status = '1'".$none_pay_cond,"inner");
		$this->db->join("(select member_id, run_status, sum(pay_amount) as non_pay_amount_balance from coop_finance_month_detail where profile_id = '".$profile_id."' group BY member_id) as t6","t6.member_id = t4.member_id AND t6.run_status = '0'".$none_pay_cond,"inner");
		
		$this->db->where($where);
		$this->db->order_by('t1.mem_group_id ASC,t4.member_id ASC');
		$rs = $this->db->get()->result_array();
		//echo $this->db->last_query(); exit;
		$arr_data['data'] = $rs;
		$this->load->view('report_processor_data/coop_report_return_receipt_excel',$arr_data);
	}

	function coop_report_receive_advance_month_excel() {
		$arr_data = array();
		$title_date = "";
		if(@$_GET['month']!='' && @$_GET['year']!='') {
			$day = '';
			$month = @$_GET['month'];
			$year = (@$_GET['year']);
			$title_date = " เดือน ".$this->month_arr[$month]." ปี ".(@$year);
		} else {
			$day = '';
			$month = '';
			$year = (@$_GET['year']);
			$title_date = " ปี ".(@$year);
		}
		$arr_data["title_date"] = $title_date;

		$receipt_con = 't8.profile_id = t9.finance_month_profile_id AND t8.member_id = t9.member_id';
		if (!empty($_GET['start_date'])) {
			$start_date_arr = explode("/", $_GET['start_date']);
			$start_date = ($start_date_arr[2] - 543)."-".$start_date_arr[1]."-".$start_date_arr[0]." 00:00:00";
			$receipt_con .= " AND t9.receipt_datetime >= '{$start_date}'";
		}
		if (!empty($_GET['end_date'])) {
			$end_date_arr = explode("/", $_GET['end_date']);
			$end_date = ($end_date_arr[2] - 543)."-".$end_date_arr[1]."-".$end_date_arr[0]." 23:59:59";
			$receipt_con .= " AND t9.receipt_datetime <= '{$end_date}'";
		}

		//Declare condition
		$where = "t1.mem_group_type = '3'";

		if(@$_GET['level'] != '') {
			$where .= " AND t1.id = '".$_GET['level']."'";
		} else if(@$_GET['faction'] != '') {
			$where .= " AND t2.id = '".$_GET['faction']."'";
		} else if(@$_GET['department'] != '') {
			$where .= " AND t3.id = '".$_GET['department']."'";
		}

		//รูปแบบสมาชิก
		if (!empty($_GET["mem_type"]) && !in_array("all", $_GET["mem_type"])){
			$where .= " AND t4.mem_type_id IN (".implode(',', $_GET["mem_type"]).")";
		}

		$date_range['start'] 	= date("Y-m", strtotime(($year)."-".$month."-01")	) . "-01";
		
		//Query Datas
		$datas = $this->db->select(array(
										't1.id',
										't1.mem_group_id',
										't1.mem_group_name',
										't4.member_id',
										't4.firstname_th',
										't4.lastname_th',
										't4.prename_id',
										't10.mem_type_name',
										't7.profile_id'
									))
						->from("(SELECT id, mem_group_id, mem_group_name, mem_group_parent_id, mem_group_type FROM coop_mem_group) as t1")
						->join("(SELECT id, mem_group_parent_id FROM coop_mem_group) as t2","t1.mem_group_parent_id = t2.id","inner")
						->join("(SELECT id, mem_group_parent_id FROM coop_mem_group) as t3","t2.mem_group_parent_id = t3.id","inner")
						->join("(SELECT IF (
											(
												SELECT
													level_old
												FROM
													coop_mem_group_move
												WHERE
													date_move >= '".$date_range['start']."'
												AND coop_mem_group_move.member_id = coop_mem_apply.member_id
												ORDER BY
													date_move ASC
												LIMIT 1
											),
											(
												SELECT
													level_old
												FROM
													coop_mem_group_move
												WHERE
													date_move >= '".$date_range['start']."'
												AND coop_mem_group_move.member_id = coop_mem_apply.member_id
												ORDER BY
													date_move ASC
												LIMIT 1
											),
											coop_mem_apply. level
										) AS level, member_id, firstname_th, lastname_th, mem_type_id, prename_id FROM coop_mem_apply) as t4","t1.id = t4.level","inner")
						->join("coop_finance_month_profile as t7","t7.profile_month = '".$_GET['month']."' AND t7.profile_year = '".$_GET['year']."'","inner")
						->join("coop_mem_type as t10", "t4.mem_type_id = t10.mem_type_id", "left")
						->where($where)
						->order_by('t1.mem_group_id, t4.member_id')
						->group_by('t4.member_id')
						->get()->result_array();

		$member_ids = array_filter(array_column($datas, 'member_id'));

		$details = $this->db->select(array(
										't8.member_id',
										'SUM(t8.real_pay_amount) as sum',
										't9.pay_type',
										't9.receipt_datetime'
									))
							->from("(SELECT member_id, profile_id, pay_amount,real_pay_amount FROM coop_finance_month_detail WHERE member_id IN (".implode(',',$member_ids).") AND profile_id = '".$datas[0]['profile_id']."') as t8")
							->join("coop_receipt as t9",$receipt_con,"inner")
							->group_by('t8.member_id , t9.pay_type')
							->get()->result_array();

		$num_rows = count($details);

		$page_num = 1;
		$all_page = ceil($num_rows/100);

		$rawArray = array();
		foreach($details as $index => $detail) {
			$memberData = $datas[array_search($detail['member_id'], $member_ids)];
			$prename = $this->db->select('prename_full')->from('coop_prename')->where('prename_id = '.$memberData['prename_id'])->get()->row();
			$memberData["prename_full"] = $prename->prename_full;
			$rawArray[] = array_merge($detail,$memberData);
		}

		$page_get = !empty($_GET['page']) ? $_GET['page'] : 1;
		$paging = $this->pagination_center->paginating(intval($page_get), $all_page, 1, 20,@$_GET);//$page_now = 1, $row_total = 1, $per_page = 20, $page_limit = 20
		$pay_types = array_column($rawArray, 'pay_type');
		$mem_group_ids = array_column($rawArray, 'mem_group_id');
		$member_ids = array_column($rawArray, 'member_id');
		array_multisort($pay_types, SORT_ASC, $mem_group_ids, SORT_ASC, $member_ids, SORT_ASC, $rawArray);

		$arr_data['data'] = $rawArray;
		$this->load->view('report_processor_data/coop_report_receive_advance_month_excel',$arr_data);
	}

	public function coop_report_deduction_send(){
		$arr_data = array();

		$this->db->select(array('id','mem_group_name'));
		$this->db->from('coop_mem_group');
		$this->db->where("mem_group_type = '1'");
		$row = $this->db->get()->result_array();
		$arr_data['row_mem_group'] = $row;

		//Get Member Type
		$this->db->select('mem_type_id, mem_type_name');
		$this->db->from('coop_mem_type');
		$row = $this->db->get()->result_array();
		$arr_data['mem_type'] = $row;

		$this->libraries->template('report_processor_data/coop_report_deduction_send',$arr_data);
	}


	function coop_report_deduction_send_excel(){
		set_time_limit ( 180 );
		$this->db->save_queries = FALSE;
		$arr_data = array();

		$row = $this->db->select(array('id','loan_type'))
						->from('coop_loan_type')
						->order_by("order_by")
						->get()->result_array();
		$arr_data['loan_type'] = $row;

		$month_arr = $this->center_function->month_arr();
		if($_GET['month']!='' && $_GET['year']!=''){
			$day = '';
			$month = $_GET['month'];
			$year = ($_GET['year']-543);
			$title_date = " เดือน ".$month_arr[$month]." ปี ".($year+543);
		}else{
			$day = '';
			$month = '';
			$year = ($_GET['year']-543);
			$title_date = " ปี ".($year+543);
		}
		$arr_data['title_date'] = $title_date;

		$row = $this->db->select(array('profile_id'))
						->from('coop_finance_month_profile')
						->where("profile_month = '".$month."' AND profile_year = '".($year+543)."'")
						->get()->result_array();
		$profile_id = $row[0]['profile_id'];

		//Declare condition
		$where_group = "";
		if ($_GET['search_type'] == 'id') {
			if(!empty($_GET['level'])){
				$where_group .= " AND t1.id = '".$_GET['level']."'";
			}else if(!empty($_GET['faction'])){
				$where_group .= " AND t2.id = '".$_GET['faction']."'";
			}else if(!empty($_GET['department'])){
				$where_group .= " AND t3.id = '".$_GET['department']."'";
			}
		} else if ($_GET['search_type'] == "code") {
			if (!empty($_GET['department_id_from'])) $where_group .= " AND CAST(t3.mem_group_id AS INTEGER) >= CAST(".$_GET['department_id_from']." AS INTEGER)";
			if (!empty($_GET['department_id_to'])) $where_group .= " AND CAST(t3.mem_group_id AS INTEGER) <= CAST(".$_GET['department_id_to']." AS INTEGER)";
		}

		$member_where = "";
		if (!empty($_GET["mem_type"])){
			if (is_array($_GET["mem_type"]) && !in_array("all", $_GET["mem_type"])){
				$member_where .= " AND t4.mem_type_id IN (".implode(',', $_GET["mem_type"]).")";
			} else if(!is_array($_GET["mem_type"]) && strpos($_GET["mem_type"], "all") === false){
				$member_where .= " AND t4.mem_type_id IN ".str_replace(']',')',str_replace('[','(',$_GET["mem_type"]));
			}
		}

		$department_order = "t1.id";
		if(!empty($_GET['department_sort'])) {
			if($_GET['department_sort'] == '1') $department_order = "t1.id";
			else if($_GET['department_sort'] == '2') $department_order = "t1.mem_group_name, t1.id";
			else if($_GET['department_sort'] == '3') $department_order = "t2.id, t1.id";
			else if($_GET['department_sort'] == '4') $department_order = "t2.mem_group_name, t1.id";
			else if($_GET['department_sort'] == '5') $department_order = "t3.id, t1.id";
			else if($_GET['department_sort'] == '6') $department_order = "t3.mem_group_name, t1.id";
		}
		$member_sort = "t4.member_id ASC";
		if(!empty($_GET['member_sort'])) {
			if($_GET['member_sort'] == "1") $member_sort = "t4.member_id ASC";
			else if($_GET['member_sort'] == "2") $member_sort = "t4.id_card ASC";
			else if($_GET['member_sort'] == "3") $member_sort = "t4.firstname_th ASC";
			else if($_GET['member_sort'] == "4") $member_sort = "t4.lastname_th ASC";
		}
		$date_range['start'] 	= date("Y-m", strtotime($year."-".$month."-01")	) . "-01";
		$date_range['end'] 		= date("Y-m-t", strtotime($year."-".$month."-01")	)." 23:59:59";
		$row_mem_group = $this->db->select(array(
												't1.id',
												't1.mem_group_id',
												't1.mem_group_name as level_name',
												't2.mem_group_name as faction_name',
												't3.mem_group_name as department_name',
												't4.member_id',
												't4.prename_id',
												't4.firstname_th',
												't4.lastname_th',
												't4.id_card',
												't5.prename_short'
											))
									->from('coop_mem_apply as t4')
									->join('coop_mem_group as t1','t1.id = IF( (select level_old from coop_mem_group_move where date_move between "'.$date_range['start'].'" and "'.$date_range['end'].'" and coop_mem_group_move.member_id = t4.member_id ORDER BY date_move DESC LIMIT 1),
									(select level_old from coop_mem_group_move where date_move between "'.$date_range['start'].'" and "'.$date_range['end'].'" and coop_mem_group_move.member_id = t4.member_id ORDER BY date_move DESC LIMIT 1),
									t4.`level`)','left', false)
									->join('coop_mem_group as t2','t1.mem_group_parent_id = t2.id','left')
									->join('coop_mem_group as t3','t2.mem_group_parent_id = t3.id','left')
									->join("coop_prename as t5","t4.prename_id = t5.prename_id","left")
									->where("(select sum(pay_amount) from coop_finance_month_detail where profile_id = $profile_id and member_id = t4.member_id group by profile_id,member_id LIMIT 1)  > 0 ".$where_group.$member_where)
									->order_by($department_order.",".$member_sort)
									->get()->result_array();



		$mem_group_ids = array_column($row_mem_group, 'id');
		$member_ids = array_column($row_mem_group, 'member_id');

		$finance_month_details = $this->db->query("SELECT * FROM coop_finance_month_detail WHERE member_id IN (".implode(',', $member_ids).") AND profile_id = '{$profile_id}'")
											->result_array();
		$finance_member_ids = array_column($finance_month_details, 'member_id');

		$loan_ids = array_filter(array_column($finance_month_details, 'loan_id'));
		if(!empty($loan_ids)) {
			$loans = $this->db->query("SELECT t1.id, t1.contract_number, t1.id, t2.loan_type_id  FROM coop_loan as t1
										LEFT JOIN coop_loan_name as t2 ON t2.loan_name_id = t1.loan_type
										WHERE id IN (".implode(',', $loan_ids).") AND t1.loan_status NOT IN ('3','5')")
								->result_array();
			$loan_members = array_column($loans, 'id');

			$receipts = $this->db->query("SELECT t2.loan_id, t2.period_count FROM coop_receipt as t1
											INNER JOIN coop_finance_transaction as t2 ON t1.receipt_id = t2.receipt_id
											WHERE t2.loan_id IN (".implode(',', $loan_ids).") AND t1.finance_month_profile_id = '{$profile_id}'")
											->result_array();
			$loan_receipt_members = array_column($receipts, 'loan_id');
		}

		$loan_atm_ids = array_filter(array_column($finance_month_details, 'loan_atm_id'));
		if(!empty($loan_atm_ids)) {
			$loan_atms = $this->db->query("SELECT t1.loan_atm_id, t1.contract_number, t1.loan_atm_id  FROM coop_loan_atm as t1
										WHERE loan_atm_id IN (".implode(',', $loan_atm_ids).") AND t1.loan_atm_status NOT IN ('3','5')")
								->result_array();
			$loan_atm_members = array_column($loan_atms, 'loan_atm_id');

			$receipt_atms = $this->db->query("SELECT t2.loan_atm_id, t2.period_count FROM coop_receipt as t1
											INNER JOIN coop_finance_transaction as t2 ON t1.receipt_id = t2.receipt_id
											WHERE t2.loan_atm_id IN (".implode(',', $loan_atm_ids).") AND t1.finance_month_profile_id = '{$profile_id}' AND t2.principal_payment > 0")
									->result_array();
			$loan_atm_receipt_members = array_column($receipt_atms, 'loan_atm_id');
		}

		$datas = array();
		$total_data = array();
		$last_member_id = null;
		foreach($row_mem_group as $mem_group) {
			//$datas[$mem_group['id']]['mem_group_name'] = $mem_group['department_name'].":".$mem_group['faction_name'].":".$mem_group['level_name']."(".$mem_group['mem_group_id'].")";
			$datas[$mem_group['id']]['mem_group_name'] = $mem_group['level_name'];
			$datas[$mem_group['id']]['member'][$mem_group['member_id']]['member_id'] = $mem_group['member_id'];
			$datas[$mem_group['id']]['member'][$mem_group['member_id']]['name'] = $mem_group['prename_short'].$mem_group['firstname_th']." ".$mem_group['lastname_th'];
			$datas[$mem_group['id']]['member'][$mem_group['member_id']]['prename_short'] = $mem_group['prename_short'];
			$datas[$mem_group['id']]['member'][$mem_group['member_id']]['firstname_th'] = $mem_group['firstname_th'];
			$datas[$mem_group['id']]['member'][$mem_group['member_id']]['lastname_th'] = $mem_group['lastname_th'];
			$datas[$mem_group['id']]['member'][$mem_group['member_id']]['id_card'] = $mem_group['id_card'];
			$datas[$mem_group['id']]['last_member'] = $mem_group['member_id'];
			$datas[$mem_group['id']]['total'] += $finance_month_detail['pay_amount'];

			$finance_indexs = array_keys($finance_member_ids,$mem_group['member_id']);

			foreach($finance_indexs as $finance_index) {
				$finance_month_detail = $finance_month_details[$finance_index];
				$datas[$mem_group['id']]['member'][$mem_group['member_id']]['amount'] += $finance_month_detail['pay_amount'];
			}
			// 	if($finance_month_detail["deduct_code"] == "LOAN") {
			// 		if (in_array($finance_month_detail['loan_id'], $loan_members)) {
			// 			$loan = $loans[array_search($finance_month_detail['loan_id'], $loan_members)];
			// 			$receipt = $receipts[array_search($finance_month_detail['loan_id'], $loan_receipt_members)];
			// 			$datas[$mem_group['id']]['member'][$mem_group['member_id']][$loan['loan_type_id']][$finance_month_detail['loan_id']]['contract_number'] = $loan["contract_number"];
			// 			$datas[$mem_group['id']]['member'][$mem_group['member_id']][$loan['loan_type_id']][$finance_month_detail['loan_id']][$finance_month_detail["pay_type"]] = $finance_month_detail['pay_amount'];
			// 			$datas[$mem_group['id']]['member'][$mem_group['member_id']][$loan['loan_type_id']][$finance_month_detail['loan_id']]['period'] = !empty($receipt) ? $receipt["period_count"] : ($finance_month_detail["period_now"] + 1);
			// 			$datas[$mem_group['id']]['last_member'] = $mem_group['member_id'];
			// 			$last_member_id = $mem_group['member_id'];
			// 			$datas[$mem_group['id']]['member'][$mem_group['member_id']]['sum_all'] += $finance_month_detail['pay_amount'];
			// 		}
			// 	}else if($finance_month_detail['deduct_code'] == 'ATM'){
			// 		if (in_array($finance_month_detail['loan_atm_id'], $loan_atm_members)) {
			// 			$loan_type_id_atm = '7';
			// 			$loan = $loan_atms[array_search($finance_month_detail['loan_atm_id'], $loan_atm_members)];
			// 			$receipt = $receipt_atms[array_search($finance_month_detail['loan_atm_id'], $loan_atm_receipt_members)];
			// 			$datas[$mem_group['id']]['member'][$mem_group['member_id']][$loan_type_id_atm][$finance_month_detail['loan_atm_id']]['contract_number'] = $loan["contract_number"];
			// 			$datas[$mem_group['id']]['member'][$mem_group['member_id']][$loan_type_id_atm][$finance_month_detail['loan_atm_id']][$finance_month_detail["pay_type"]] = $finance_month_detail['pay_amount'];
			// 			$datas[$mem_group['id']]['member'][$mem_group['member_id']][$loan_type_id_atm][$finance_month_detail['loan_atm_id']]['period'] = !empty($receipt) ? $receipt["period_count"] : ($finance_month_detail["period_now"] + 1);
			// 			$datas[$mem_group['id']]['last_member'] = $mem_group['member_id'];
			// 			$last_member_id = $mem_group['member_id'];
			// 			$datas[$mem_group['id']]['member'][$mem_group['member_id']]['sum_all'] += $finance_month_detail['pay_amount'];
			// 		}
			// 	}else if($finance_month_detail['deduct_code'] == 'DEPOSIT'){
			// 		$datas[$mem_group['id']]['member'][$mem_group['member_id']][$finance_month_detail['deduct_code']][$finance_month_detail['deposit_account_id']]['pay_amount'] = $finance_month_detail['pay_amount'];
			// 		$datas[$mem_group['id']]['member'][$mem_group['member_id']][$finance_month_detail['deduct_code']][$finance_month_detail['deposit_account_id']]['account_id'] = $finance_month_detail['deposit_account_id'];
			// 		$datas[$mem_group['id']]['last_member'] = $mem_group['member_id'];
			// 		$last_member_id = $mem_group['member_id'];
			// 		$datas[$mem_group['id']]['member'][$mem_group['member_id']]['sum_all'] += $finance_month_detail['pay_amount'];
			// 	}else{
			// 		$datas[$mem_group['id']]['member'][$mem_group['member_id']][$finance_month_detail['deduct_code']] += $finance_month_detail['pay_amount'];
			// 		$datas[$mem_group['id']]['last_member'] = $mem_group['member_id'];
			// 		$last_member_id = $mem_group['member_id'];
			// 		$datas[$mem_group['id']]['member'][$mem_group['member_id']]['sum_all'] += $finance_month_detail['pay_amount'];
			// 	}

			// }

		}


		$arr_data['datas'] = $datas;
		$arr_data['last_member_id'] = $last_member_id;
		$this->load->view('report_processor_data/coop_report_deduction_send_excel',$arr_data);
	}

}
