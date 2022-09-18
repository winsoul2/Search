<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Report_processor_pay_some_data extends CI_Controller {
	public $month_arr = array('1'=>'มกราคม','2'=>'กุมภาพันธ์','3'=>'มีนาคม','4'=>'เมษายน','5'=>'พฤษภาคม','6'=>'มิถุนายน','7'=>'กรกฎาคม','8'=>'สิงหาคม','9'=>'กันยายน','10'=>'ตุลาคม','11'=>'พฤศจิกายน','12'=>'ธันวาคม');
	public $month_short_arr = array('1'=>'ม.ค.','2'=>'ก.พ.','3'=>'มี.ค.','4'=>'เม.ย.','5'=>'พ.ค.','6'=>'มิ.ย.','7'=>'ก.ค.','8'=>'ส.ค.','9'=>'ก.ย.','10'=>'ต.ค.','11'=>'พ.ย.','12'=>'ธ.ค.');

	function __construct()
	{
		parent::__construct();
		$this->month_arr = array('1'=>'มกราคม','2'=>'กุมภาพันธ์','3'=>'มีนาคม','4'=>'เมษายน','5'=>'พฤษภาคม','6'=>'มิถุนายน','7'=>'กรกฎาคม','8'=>'สิงหาคม','9'=>'กันยายน','10'=>'ตุลาคม','11'=>'พฤศจิกายน','12'=>'ธันวาคม');
	}
	
	function coop_report_pay_some(){
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

		$this->libraries->template('report_processor_pay_some_data/coop_report_pay_some',$arr_data);
	}

	function coop_report_pay_some_preview(){
		$array_post = $_POST;
		if($_POST['type_report'] == '0'){
			$this->coop_report_pay_some_by_department($array_post);
		}else if($_POST['type_report'] == '1'){
			$this->coop_report_pay_some_by_member_detail($array_post);
		}else if($_POST['type_report'] == '2'){
			$this->coop_report_pay_some_by_member($array_post);
		}else{
			echo "<script>document.location.href='".base_url(PROJECTPATH.'/report_processor_pay_some_data/coop_report_pay_some')."';</script>";
		}
	}

	function coop_report_pay_some_by_department($array_post){
		$arr_data = array();
		//echo"<pre>";print_r($array_post);exit;
		$this->db->select(array('id','loan_type','loan_type_code'));
		$this->db->from('coop_loan_type');
		$this->db->order_by("order_by");
		$row = $this->db->get()->result_array();
		$loan_type = $row;
		$arr_data['loan_type'] = $loan_type;

		$month = $array_post['month'];
		$year = $array_post['year'];
		$date_start = ($year-543)."-".sprintf("%02d",@$month)."-01";

		$sql = "SELECT * FROM (
					SELECT
						t1.member_id,
						t1.profile_id,
						t3.level AS level,
						t3.department AS department,
						t3.firstname_th,
						t3.lastname_th,
						t4.prename_short,
						t5.contract_number AS contract_number,
						t6.contract_number AS atm_contract_number
					FROM
						coop_finance_month_detail AS t1
					LEFT JOIN coop_finance_month_profile AS t2 ON t1.profile_id = t2.profile_id
					INNER JOIN (
						SELECT
							t3.member_id,
							t3.mem_type_id,
							t3.firstname_th,
							t3.lastname_th,
							t3.prename_id,

						IF (
							t4.level_old IS NULL,
							t3. LEVEL,
							t4.level_old
						) AS LEVEL,

					IF (
						t4.faction_old IS NULL,
						t3.faction,
						t4.faction_old
					) AS faction,

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
						WHERE
							date_move >= '".$date_start."'
						GROUP BY
							member_id
						ORDER BY
							date_move ASC
					) AS t4 ON t3.member_id = t4.member_id
					WHERE
						1 = 1
					) AS t3 ON t1.member_id = t3.member_id
					LEFT JOIN coop_prename AS t4 ON t3.prename_id = t4.prename_id
					LEFT JOIN coop_loan AS t5 ON t1.loan_id = t5.id
					LEFT JOIN coop_loan_atm AS t6 ON t1.loan_atm_id = t6.loan_atm_id
					WHERE
						t2.profile_month = '".$array_post['month']."'
					AND t2.profile_year = '".$array_post['year']."'
					AND t1.pay_amount <> t1.real_pay_amount
					AND t1.real_pay_amount <> 0
					GROUP BY t1.member_id
					ORDER BY t1.member_id ASC
					) AS t7
					LEFT OUTER JOIN (
					SELECT
							pay_amount,
							real_pay_amount,
							pay_type,
							member_id,
							profile_id,
							loan_id,
							loan_atm_id,
							deduct_code
						FROM
							coop_finance_month_detail
					) AS t8 ON t7.member_id = t8.member_id AND t7.profile_id = t8.profile_id
					GROUP BY t8.member_id,t8.pay_type,t8.loan_id,t8.loan_atm_id,t8.deduct_code";
		$rs_data = $this->db->query($sql);
		$row_data = $rs_data->result_array();	
		
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
					$array_data[$value['department']][$value['deduct_code']][$rs_loan[0]['loan_type_id']]['principal'] += $value['real_pay_amount'];
					$total_data[$value['deduct_code']][$rs_loan[0]['loan_type_id']]['principal'] += $value['real_pay_amount'];
				}else{
					$array_data[$value['department']][$value['deduct_code']][$rs_loan[0]['loan_type_id']]['interest'] += $value['real_pay_amount'];
					$total_data[$value['deduct_code']][$rs_loan[0]['loan_type_id']]['interest'] += $value['real_pay_amount'];
				}
			}else if($value['deduct_code']=='ATM'){
				if($value['pay_type']=='principal'){
					$array_data[$value['department']][$value['deduct_code']]['principal'] += $value['real_pay_amount'];
					$total_data[$value['deduct_code']]['principal'] += $value['real_pay_amount'];
				}else{
					$array_data[$value['department']][$value['deduct_code']]['interest'] += $value['real_pay_amount'];
					$total_data[$value['deduct_code']]['interest'] += $value['real_pay_amount'];
				}
			}else{
				$array_data[$value['department']][$value['deduct_code']] += $value['real_pay_amount'];
				$total_data[$value['deduct_code']] += $value['real_pay_amount'];
			}
			$array_data[$value['department']]['total'] += $value['real_pay_amount'];
			$total_data['total'] += $value['real_pay_amount'];
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
		//echo $this->db->last_query();

		$arr_data['num_rows'] = $row['num_rows'];
		$arr_data['paging'] = $paging;
		$arr_data['data'] = $row['data'];
		$arr_data['page_all'] = $row['page_all'];
		$arr_data['month_text'] = $this->month_arr[$array_post['month']];
		$arr_data['year'] = $array_post['year'];
		$arr_data['total_data'] = $total_data;
		$this->preview_libraries->template_preview('report_processor_pay_some_data/coop_report_pay_some_by_department',$arr_data);
	}

	function coop_report_pay_some_by_department_excel(){
		$arr_data = array();
		//echo"<pre>";($array_post);exit;
		$this->db->select(array('id','loan_type','loan_type_code'));
		$this->db->from('coop_loan_type');
		$this->db->order_by("order_by");
		$row = $this->db->get()->result_array();
		$loan_type = $row;
		$arr_data['loan_type'] = $loan_type;		
		
		$month = $_GET['month'];
		$year = $_GET['year'];
		$date_start = ($year-543)."-".sprintf("%02d",@$month)."-01";
				
		$sql = "SELECT * FROM (
					SELECT
						t1.member_id,
						t1.profile_id,
						t3.level AS level,
						t3.department AS department,
						t3.firstname_th,
						t3.lastname_th,
						t4.prename_short,
						t5.contract_number AS contract_number,
						t6.contract_number AS atm_contract_number
					FROM
						coop_finance_month_detail AS t1
					LEFT JOIN coop_finance_month_profile AS t2 ON t1.profile_id = t2.profile_id
					INNER JOIN (
						SELECT
							t3.member_id,
							t3.mem_type_id,
							t3.firstname_th,
							t3.lastname_th,
							t3.prename_id,

						IF (
							t4.level_old IS NULL,
							t3. LEVEL,
							t4.level_old
						) AS LEVEL,

					IF (
						t4.faction_old IS NULL,
						t3.faction,
						t4.faction_old
					) AS faction,

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
						WHERE
							date_move >= '".$date_start."'
						GROUP BY
							member_id
						ORDER BY
							date_move ASC
					) AS t4 ON t3.member_id = t4.member_id
					WHERE
						1 = 1
					) AS t3 ON t1.member_id = t3.member_id
					LEFT JOIN coop_prename AS t4 ON t3.prename_id = t4.prename_id
					LEFT JOIN coop_loan AS t5 ON t1.loan_id = t5.id
					LEFT JOIN coop_loan_atm AS t6 ON t1.loan_atm_id = t6.loan_atm_id
					WHERE
						t2.profile_month = '".$_GET['month']."'
					AND t2.profile_year = '".$_GET['year']."'
					AND t1.pay_amount <> t1.real_pay_amount
					AND t1.real_pay_amount <> 0
					GROUP BY t1.member_id
					ORDER BY t1.member_id ASC
					) AS t7
					LEFT OUTER JOIN (
					SELECT
							pay_amount,
							real_pay_amount,
							pay_type,
							member_id,
							profile_id,
							loan_id,
							loan_atm_id,
							deduct_code
						FROM
							coop_finance_month_detail
					) AS t8 ON t7.member_id = t8.member_id AND t7.profile_id = t8.profile_id
					GROUP BY t8.member_id,t8.pay_type,t8.loan_id,t8.loan_atm_id,t8.deduct_code";
		$rs_data = $this->db->query($sql);
		$row_data = $rs_data->result_array();
		//echo $this->db->last_query(); echo '<hr>';
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
					$array_data[$value['department']][$value['deduct_code']][$rs_loan[0]['loan_type_id']]['principal'] += $value['real_pay_amount'];
					$total_data[$value['deduct_code']][$rs_loan[0]['loan_type_id']]['principal'] += $value['real_pay_amount'];
				}else{
					$array_data[$value['department']][$value['deduct_code']][$rs_loan[0]['loan_type_id']]['interest'] += $value['real_pay_amount'];
					$total_data[$value['deduct_code']][$rs_loan[0]['loan_type_id']]['interest'] += $value['real_pay_amount'];
				}
			}else if($value['deduct_code']=='ATM'){
				if($value['pay_type']=='principal'){
					$array_data[$value['department']][$value['deduct_code']]['principal'] += $value['real_pay_amount'];
					$total_data[$value['deduct_code']]['principal'] += $value['real_pay_amount'];
				}else{
					$array_data[$value['department']][$value['deduct_code']]['interest'] += $value['real_pay_amount'];
					$total_data[$value['deduct_code']]['interest'] += $value['real_pay_amount'];
				}
			}else{
				$array_data[$value['department']][$value['deduct_code']] += $value['real_pay_amount'];
				$total_data[$value['deduct_code']] += $value['real_pay_amount'];
			}
			$array_data[$value['department']]['total'] += $value['real_pay_amount'];
			$total_data['total'] += $value['real_pay_amount'];
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
		$this->load->view('report_processor_pay_some_data/coop_report_pay_some_by_department_excel',$arr_data);
	}

	function coop_report_pay_some_by_member_detail($array_post){
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
		if(!empty($array_post['department']) && $array_post['department']!=0){
			$where_data .= " AND t3.department = '".$array_post['department']."'";
		}
		if(!empty($array_post['faction']) && $array_post['faction']!=0){
			$where_data .= " AND t3.faction = '".$array_post['faction']."'";
		}
		if(!empty($array_post['level']) && $array_post['level']!=0){
			$where_data .= " AND t3.level = '".$array_post['level']."'";
		}
		
		if (!empty($array_post["mem_type"]) && !in_array("all", $array_post["mem_type"])){
			$where_data .= " AND t3.mem_type_id IN (".implode(',', $array_post["mem_type"]).")";
		}
		
		
		$month = $array_post['month'];
		$year = $array_post['year'];
		$date_start = ($year-543)."-".sprintf("%02d",@$month)."-01";
		$sql = "SELECT * FROM (
					SELECT
						t1.member_id,
						t1.profile_id,
						t3.level AS level,
						t3.firstname_th,
						t3.lastname_th,
						t4.prename_short,
						t5.contract_number AS contract_number,
						t6.contract_number AS atm_contract_number
					FROM
						coop_finance_month_detail AS t1
					LEFT JOIN coop_finance_month_profile AS t2 ON t1.profile_id = t2.profile_id
					INNER JOIN (
						SELECT
							t3.member_id,
							t3.mem_type_id,
							t3.firstname_th,
							t3.lastname_th,
							t3.prename_id,

						IF (
							t4.level_old IS NULL,
							t3. LEVEL,
							t4.level_old
						) AS LEVEL,

					IF (
						t4.faction_old IS NULL,
						t3.faction,
						t4.faction_old
					) AS faction,

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
						WHERE
							date_move >= '".$date_start."'
						GROUP BY
							member_id
						ORDER BY
							date_move ASC
					) AS t4 ON t3.member_id = t4.member_id
					WHERE
						1 = 1
					) AS t3 ON t1.member_id = t3.member_id
					LEFT JOIN coop_prename AS t4 ON t3.prename_id = t4.prename_id
					LEFT JOIN coop_loan AS t5 ON t1.loan_id = t5.id
					LEFT JOIN coop_loan_atm AS t6 ON t1.loan_atm_id = t6.loan_atm_id
					WHERE
						t2.profile_month = '".$month."'
					AND t2.profile_year = '".$year."'
					AND t1.pay_amount <> t1.real_pay_amount
					AND t1.real_pay_amount <> 0
					GROUP BY t1.member_id
					ORDER BY t1.member_id ASC
					) AS t7
					LEFT OUTER JOIN (
					SELECT
							pay_amount,
							real_pay_amount,
							pay_type,
							member_id,
							profile_id,
							loan_id,
							loan_atm_id,
							deduct_code
						FROM
							coop_finance_month_detail
					) AS t8 ON t7.member_id = t8.member_id AND t7.profile_id = t8.profile_id
					GROUP BY t8.member_id,t8.pay_type,t8.loan_id,t8.loan_atm_id,t8.deduct_code";
		$rs_data = $this->db->query($sql);
		$row_data = $rs_data->result_array();		
		//echo $this->db->last_query(); echo '<hr>'; exit;
		$array_data = array();
		$total_data = array();
		$total_all_data = array();

		foreach($row_data as $key => $value){
			$array_data[$value['level']][$value['member_id']]['member_id'] = $value['member_id'];
			$array_data[$value['level']][$value['member_id']]['member_name'] = $value['prename_short'].$value['firstname_th']." ".$value['lastname_th'];
			$array_data[$value['level']][$value['member_id']]['fee'] = ($value['deduct_code']=="REGISTER_FEE") ? $value['real_pay_amount'] : 0;
			
			$real_pay_amount = @$value['real_pay_amount'];
			$pay_type = @$value['pay_type'];
			
			if($value['deduct_code']=='LOAN'){
				$this->db->select(array('t3.loan_type_code','t2.loan_name_id'));
				$this->db->from('coop_loan as t1');
				$this->db->join('coop_loan_name as t2','t1.loan_type = t2.loan_name_id','inner');
				$this->db->join('coop_loan_type as t3','t2.loan_type_id = t3.id','inner');
				$this->db->where("t1.id = '".$value['loan_id']."'");
				$rs_loan = $this->db->get()->result_array();
				if($rs_loan[0]['loan_type_code'] == 'emergent'){
					if(@$pay_type=='principal'){
						$array_data[$value['level']][$value['member_id']][$value['deduct_code']]['emergent'][$value['contract_number']]['principal'] = @$real_pay_amount;
						$array_data[$value['level']][$value['member_id']][$value['deduct_code']]['emergent'][$value['contract_number']]['contract_number'] = $value['contract_number'];
						$total_data[$value['level']][$value['deduct_code']]['emergent']['principal'] += @$real_pay_amount;
						$total_all_data[$value['deduct_code']]['emergent']['principal'] += @$real_pay_amount;
					}else{
						$array_data[$value['level']][$value['member_id']][$value['deduct_code']]['emergent'][$value['contract_number']]['interest'] = @$real_pay_amount;
						$array_data[$value['level']][$value['member_id']][$value['deduct_code']]['emergent'][$value['contract_number']]['contract_number'] = $value['contract_number'];
						$total_data[$value['level']][$value['deduct_code']]['emergent']['interest'] += @$real_pay_amount;
						$total_all_data[$value['deduct_code']]['emergent']['interest'] += @$real_pay_amount;
					}
				}else if($rs_loan[0]['loan_type_code'] == 'normal'){
					if(@$pay_type=='principal'){
						$array_data[$value['level']][$value['member_id']][$value['deduct_code']]['normal'][$value['contract_number']]['principal'] = @$real_pay_amount;
						$array_data[$value['level']][$value['member_id']][$value['deduct_code']]['normal'][$value['contract_number']]['contract_number'] = $value['contract_number'];
						$total_data[$value['level']][$value['deduct_code']]['normal']['principal'] += @$real_pay_amount;
						$total_all_data[$value['deduct_code']]['normal']['principal'] += @$real_pay_amount;
					}else{
						$array_data[$value['level']][$value['member_id']][$value['deduct_code']]['normal'][$value['contract_number']]['interest'] = @$real_pay_amount;
						$array_data[$value['level']][$value['member_id']][$value['deduct_code']]['normal'][$value['contract_number']]['contract_number'] = $value['contract_number'];
						$total_data[$value['level']][$value['deduct_code']]['normal']['interest'] += @$real_pay_amount;
						$total_all_data[$value['deduct_code']]['normal']['interest'] += @$real_pay_amount;
					}
				}else if($rs_loan[0]['loan_type_code'] == 'special'){
					if(@$pay_type=='principal'){
						$array_data[$value['level']][$value['member_id']][$value['deduct_code']]['special'][$value['contract_number']]['principal'] = @$real_pay_amount;
						$array_data[$value['level']][$value['member_id']][$value['deduct_code']]['special'][$value['contract_number']]['contract_number'] = $value['contract_number'];
						$total_data[$value['level']][$value['deduct_code']]['special']['principal'] += @$real_pay_amount;
						$total_all_data[$value['deduct_code']]['special']['principal'] += @$real_pay_amount;
					}else{
						$array_data[$value['level']][$value['member_id']][$value['deduct_code']]['special'][$value['contract_number']]['interest'] = @$real_pay_amount;
						$array_data[$value['level']][$value['member_id']][$value['deduct_code']]['special'][$value['contract_number']]['contract_number'] = $value['contract_number'];
						$total_data[$value['level']][$value['deduct_code']]['special']['interest'] += @$real_pay_amount;
						$total_all_data[$value['deduct_code']]['special']['interest'] += @$real_pay_amount;
					}
				}
			}else if($value['deduct_code']=='ATM'){
				if(@$pay_type=='principal'){
					$array_data[$value['level']][$value['member_id']]["LOAN"]['emergent'][$value['atm_contract_number']]['principal'] = @$real_pay_amount;
					$array_data[$value['level']][$value['member_id']]["LOAN"]['emergent'][$value['atm_contract_number']]['contract_number'] = $value['atm_contract_number'];
					$total_data[$value['level']]["LOAN"]['emergent']['principal'] += @$real_pay_amount;
					$total_all_data["LOAN"]['emergent']['principal'] += @$real_pay_amount;
				}else{
					$array_data[$value['level']][$value['member_id']]["LOAN"]['emergent'][$value['atm_contract_number']]['interest'] = @$real_pay_amount;
					$array_data[$value['level']][$value['member_id']]["LOAN"]['emergent'][$value['atm_contract_number']]['contract_number'] = $value['atm_contract_number'];
					$total_data[$value['level']]["LOAN"]['emergent']['interest'] += @$real_pay_amount;
					$total_all_data["LOAN"]['emergent']['interest'] += @$real_pay_amount;
				}
			}else{
				$array_data[$value['level']][$value['member_id']][$value['deduct_code']] += @$real_pay_amount;
				$total_data[$value['level']][$value['deduct_code']] += @$real_pay_amount;
				$total_all_data[$value['deduct_code']] += @$real_pay_amount;
			}
			$array_data[$value['level']][$value['member_id']]['total'] += @$real_pay_amount;
			$total_data[$value['level']]['total'] += @$real_pay_amount;
			$total_all_data['total'] += @$real_pay_amount;
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
		//echo '<pre>'; print_r($row_group); echo '</pre>';
		$arr_data['month_text'] = $this->month_arr[$array_post['month']];
		$arr_data['year'] = $array_post['year'];
		$arr_data['total_data'] = $total_data;
		$arr_data['total_all_data'] = $total_all_data;
		$arr_data['row_group'] = $row_group;
		$arr_data['page_all'] = $page_all;
		$arr_data['key_mem_counts'] = $key_mem_counts;

		$this->preview_libraries->template_preview('report_processor_pay_some_data/coop_report_pay_some_by_member_detail',$arr_data);
	}

	function coop_report_pay_some_by_member_detail_excel(){
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
		if(!empty($_GET['department']) && $_GET['department']!=0){
			$where_data .= " AND t3.department = '".$_GET['department']."'";
		}
		if(!empty($_GET['faction']) && $_GET['faction']!=0){
			$where_data .= " AND t3.faction = '".$_GET['faction']."'";
		}
		if(!empty($_GET['level']) && $_GET['level']!=0){
			$where_data .= " AND t3.level = '".$_GET['level']."'";
		}
		
		if (!empty($_GET["mem_type"]) && !in_array("all", $_GET["mem_type"])){
			$where_data .= " AND t3.mem_type_id IN (".implode(',', $_GET["mem_type"]).")";
		}		
		
		$month = $_GET['month'];
		$year = $_GET['year'];
		$date_start = ($year-543)."-".sprintf("%02d",@$month)."-01";
		$sql = "SELECT * FROM (
					SELECT
						t1.member_id,
						t1.profile_id,
						t3.level AS level,
						t3.firstname_th,
						t3.lastname_th,
						t4.prename_short,
						t5.contract_number AS contract_number,
						t6.contract_number AS atm_contract_number
					FROM
						coop_finance_month_detail AS t1
					LEFT JOIN coop_finance_month_profile AS t2 ON t1.profile_id = t2.profile_id
					INNER JOIN (
						SELECT
							t3.member_id,
							t3.mem_type_id,
							t3.firstname_th,
							t3.lastname_th,
							t3.prename_id,

						IF (
							t4.level_old IS NULL,
							t3. LEVEL,
							t4.level_old
						) AS LEVEL,

					IF (
						t4.faction_old IS NULL,
						t3.faction,
						t4.faction_old
					) AS faction,

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
						WHERE
							date_move >= '".$date_start."'
						GROUP BY
							member_id
						ORDER BY
							date_move ASC
					) AS t4 ON t3.member_id = t4.member_id
					WHERE
						1 = 1
					) AS t3 ON t1.member_id = t3.member_id
					LEFT JOIN coop_prename AS t4 ON t3.prename_id = t4.prename_id
					LEFT JOIN coop_loan AS t5 ON t1.loan_id = t5.id
					LEFT JOIN coop_loan_atm AS t6 ON t1.loan_atm_id = t6.loan_atm_id
					WHERE
						t2.profile_month = '".$month."'
					AND t2.profile_year = '".$year."'
					AND t1.pay_amount <> t1.real_pay_amount
					AND t1.real_pay_amount <> 0
					GROUP BY t1.member_id
					ORDER BY t1.member_id ASC
					) AS t7
					LEFT OUTER JOIN (
					SELECT
							pay_amount,
							real_pay_amount,
							pay_type,
							member_id,
							profile_id,
							loan_id,
							loan_atm_id,
							deduct_code
						FROM
							coop_finance_month_detail
					) AS t8 ON t7.member_id = t8.member_id AND t7.profile_id = t8.profile_id
					GROUP BY t8.member_id,t8.pay_type,t8.loan_id,t8.loan_atm_id,t8.deduct_code";
		$rs_data = $this->db->query($sql);
		$row_data = $rs_data->result_array();
		//echo $this->db->last_query(); echo '<hr>'; exit;
		$array_data = array();
		$total_data = array();
		$total_all_data = array();

		foreach($row_data as $key => $value){
			$array_data[$value['level']][$value['member_id']]['member_id'] = $value['member_id'];
			$array_data[$value['level']][$value['member_id']]['member_name'] = $value['prename_short'].$value['firstname_th']." ".$value['lastname_th'];
			$array_data[$value['level']][$value['member_id']]['fee'] = ($value['deduct_code']=="REGISTER_FEE") ? $value['real_pay_amount'] : 0;
			$real_pay_amount = @$value['real_pay_amount'];
			$pay_type = @$value['pay_type'];
			if($value['deduct_code']=='LOAN'){
				$this->db->select(array('t3.loan_type_code','t2.loan_name_id'));
				$this->db->from('coop_loan as t1');
				$this->db->join('coop_loan_name as t2','t1.loan_type = t2.loan_name_id','inner');
				$this->db->join('coop_loan_type as t3','t2.loan_type_id = t3.id','inner');
				$this->db->where("t1.id = '".$value['loan_id']."'");
				$rs_loan = $this->db->get()->result_array();
				if($rs_loan[0]['loan_type_code'] == 'emergent'){
					if(@$pay_type=='principal'){
						$array_data[$value['level']][$value['member_id']][$value['deduct_code']]['emergent'][$value['contract_number']]['principal'] = @$real_pay_amount;
						$array_data[$value['level']][$value['member_id']][$value['deduct_code']]['emergent'][$value['contract_number']]['contract_number'] = $value['contract_number'];
						$total_data[$value['level']][$value['deduct_code']]['emergent']['principal'] += @$real_pay_amount;
						$total_all_data[$value['deduct_code']]['emergent']['principal'] += @$real_pay_amount;
					}else{
						$array_data[$value['level']][$value['member_id']][$value['deduct_code']]['emergent'][$value['contract_number']]['interest'] = @$real_pay_amount;
						$array_data[$value['level']][$value['member_id']][$value['deduct_code']]['emergent'][$value['contract_number']]['contract_number'] = $value['contract_number'];
						$total_data[$value['level']][$value['deduct_code']]['emergent']['interest'] += @$real_pay_amount;
						$total_all_data[$value['deduct_code']]['emergent']['interest'] += @$real_pay_amount;
					}
				}else if($rs_loan[0]['loan_type_code'] == 'normal'){
					if(@$pay_type=='principal'){
						$array_data[$value['level']][$value['member_id']][$value['deduct_code']]['normal'][$value['contract_number']]['principal'] = @$real_pay_amount;
						$array_data[$value['level']][$value['member_id']][$value['deduct_code']]['normal'][$value['contract_number']]['contract_number'] = $value['contract_number'];
						$total_data[$value['level']][$value['deduct_code']]['normal']['principal'] += @$real_pay_amount;
						$total_all_data[$value['deduct_code']]['normal']['principal'] += @$real_pay_amount;
					}else{
						$array_data[$value['level']][$value['member_id']][$value['deduct_code']]['normal'][$value['contract_number']]['interest'] = @$real_pay_amount;
						$array_data[$value['level']][$value['member_id']][$value['deduct_code']]['normal'][$value['contract_number']]['contract_number'] = $value['contract_number'];
						$total_data[$value['level']][$value['deduct_code']]['normal']['interest'] += @$real_pay_amount;
						$total_all_data[$value['deduct_code']]['normal']['interest'] += @$real_pay_amount;
					}
				}else if($rs_loan[0]['loan_type_code'] == 'special'){
					if(@$pay_type=='principal'){
						$array_data[$value['level']][$value['member_id']][$value['deduct_code']]['special'][$value['contract_number']]['principal'] = @$real_pay_amount;
						$array_data[$value['level']][$value['member_id']][$value['deduct_code']]['special'][$value['contract_number']]['contract_number'] = $value['contract_number'];
						$total_data[$value['level']][$value['deduct_code']]['special']['principal'] += @$real_pay_amount;
						$total_all_data[$value['deduct_code']]['special']['principal'] += @$real_pay_amount;
					}else{
						$array_data[$value['level']][$value['member_id']][$value['deduct_code']]['special'][$value['contract_number']]['interest'] = @$real_pay_amount;
						$array_data[$value['level']][$value['member_id']][$value['deduct_code']]['special'][$value['contract_number']]['contract_number'] = $value['contract_number'];
						$total_data[$value['level']][$value['deduct_code']]['special']['interest'] += @$real_pay_amount;
						$total_all_data[$value['deduct_code']]['special']['interest'] += @$real_pay_amount;
					}
				}
			}else if($value['deduct_code']=='ATM'){
				if(@$pay_type=='principal'){
					$array_data[$value['level']][$value['member_id']]["LOAN"]['emergent'][$value['atm_contract_number']]['principal'] = @$real_pay_amount;
					$array_data[$value['level']][$value['member_id']]["LOAN"]['emergent'][$value['atm_contract_number']]['contract_number'] = $value['atm_contract_number'];
					$total_data[$value['level']]["LOAN"]['emergent']['principal'] += @$real_pay_amount;
					$total_all_data["LOAN"]['emergent']['principal'] += @$real_pay_amount;
				}else{
					$array_data[$value['level']][$value['member_id']]["LOAN"]['emergent'][$value['atm_contract_number']]['interest'] = @$real_pay_amount;
					$array_data[$value['level']][$value['member_id']]["LOAN"]['emergent'][$value['atm_contract_number']]['contract_number'] = $value['atm_contract_number'];
					$total_data[$value['level']]["LOAN"]['emergent']['interest'] += @$real_pay_amount;
					$total_all_data["LOAN"]['emergent']['interest'] += @$real_pay_amount;
				}
			}else{
				$array_data[$value['level']][$value['member_id']][$value['deduct_code']] += @$real_pay_amount;
				$total_data[$value['level']][$value['deduct_code']] += @$real_pay_amount;
				$total_all_data[$value['deduct_code']] += @$real_pay_amount;
			}
			$array_data[$value['level']][$value['member_id']]['total'] += @$real_pay_amount;
			$total_data[$value['level']]['total'] += @$real_pay_amount;
			$total_all_data['total'] += @$real_pay_amount;
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
		if($_GET['department']!='' && $_GET['department']!='0'){
			$where .= " AND t3.id = '".$_GET['department']."'";
		}
		if($_GET['faction']!='' && $_GET['faction']!='0'){
			$where .= " AND t2.id = '".$_GET['faction']."'";
		}
		if($_GET['level']!='' && $_GET['level']!='0'){
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
		$this->load->view('report_processor_pay_some_data/coop_report_pay_some_by_member_detail_excel',$arr_data);
	}

	function check_coop_pay_some() {
		//echo '<pre>'; print_r($_POST); echo '</pre>';
		if($_POST['type_report'] == '1'){
			
			$where_data = '';
			if($_POST['department']!='' && $_POST['department']!=0){
				$where_data .= " AND t3.department = '".$_POST['department']."'";
			}			
			if($_POST['faction']!='' && $_POST['faction'] != 0){
				$where_data .= " AND t3.faction = '".$_POST['faction']."'";
			}
			if($_POST['level']!='' && $_POST['level']!=0){
				$where_data .= " AND t3.level = '".$_POST['level']."'";
			}
			
			$month = $_POST['month'];
			$year = $_POST['year'];
			$date_start = ($year-543)."-".sprintf("%02d",@$month)."-01";

			$this->db->select(array('
				t1.member_id,
				t1.deduct_code,
				t1.pay_amount,
				t1.real_pay_amount,
				t1.loan_id,
				t1.loan_atm_id,
				IF (
					t4.department_old IS NULL,
					t3.department,
					t4.department_old
				) AS department,
				IF (
					t4.faction_old IS NULL,
					t3.faction,
					t4.faction_old
				) AS faction,
				IF (
					t4.level_old IS NULL,
					t3.level,
					t4.level_old
				) AS level
			'));
			$this->db->from('coop_finance_month_detail AS t1');
			$this->db->join('coop_finance_month_profile AS t2','t1.profile_id = t2.profile_id','left');
			$this->db->join('coop_mem_apply as t3','t1.member_id = t3.member_id','inner');
			$this->db->join("(
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
							) AS t4","t3.member_id = t4.member_id","left");
			$this->db->where("t2.profile_month = '".$month."' AND t2.profile_year = '".$year."' AND t1.pay_amount <> t1.real_pay_amount AND t1.real_pay_amount <> 0 {$where_data}");
			$row_data = $this->db->get()->result_array();
			//echo $where_data.'<hr>';
			//echo $this->db->last_query(); exit;
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
	
	function coop_report_pay_some_by_member($array_post){
		$arr_data = array();

		$month = (@$array_post['month'] != '')?$array_post['month']:date('m');
		$year = (@$array_post['year'] != '')?$array_post['year']:(date('Y')+543);
		$date_start = ($year-543)."-".sprintf("%02d",@$month)."-01";
		
		$where_data = '';
		if(!empty($array_post['department']) && $array_post['department']!=0){
			$where_data .= " AND t3.department = '".$array_post['department']."'";
		}
		if(!empty($array_post['faction']) && $array_post['faction']!=0){
			$where_data .= " AND t3.faction = '".$array_post['faction']."'";
		}
		if(!empty($array_post['level']) && $array_post['level']!=0){
			$where_data .= " AND t3.level = '".$array_post['level']."'";
		}
		
		if (!empty($array_post["mem_type"]) && !in_array("all", $array_post["mem_type"])){
			$where_data .= " AND t3.mem_type_id IN (".implode(',', $array_post["mem_type"]).")";
		}
		
		
		if (!empty($array_post["mem_type"]) && !in_array("all", $array_post["mem_type"])){
			$where_data .= " AND t3.mem_type_id IN (".implode(',', $array_post["mem_type"]).")";
		}
		
		$this->db->select(array(
			't1.member_id',
			't1.profile_id',
			't3.department',
			't3.firstname_th',
			't3.lastname_th',
			't4.prename_full',
			't5.mem_group_name',
			't3.mem_type_id',
			't6.mem_type_name'
		));
		$this->db->from('coop_finance_month_detail AS t1');
		$this->db->join('coop_finance_month_profile AS t2','t1.profile_id = t2.profile_id','left');
		$this->db->join("(SELECT
									t3.member_id,
									t3.mem_type_id,
									IF(t4.level_old IS NULL, t3.level, t4.level_old) AS level,
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
							WHERE 1=1 ) as t3", "t1.member_id = t3.member_id", "inner");
		$this->db->join('coop_prename as t4','t3.prename_id = t4.prename_id','left');
		$this->db->join('coop_mem_group as t5','t3.level = t5.id','left');
		$this->db->join('coop_mem_type AS t6','t3.mem_type_id = t6.mem_type_id','left');
		$this->db->where("t2.profile_month = '".$array_post['month']."' AND t2.profile_year = '".$array_post['year']."' AND t1.pay_amount <> t1.real_pay_amount AND t1.real_pay_amount <> 0".$where_data);
		$this->db->group_by('t1.member_id');
		$this->db->order_by('t5.mem_group_id ASC,t1.member_id ASC');
		$row_data = $this->db->get()->result_array();
		//echo $this->db->last_query(); echo '<br>';
		//exit;
		$array_data = array();
		$total_data = array();
		$total_all_data = array();
		foreach($row_data as $key => $value){
			$array_data[$value['department']][$value['member_id']] = $value;
			$this->db->select(array(
				'SUM(t1.pay_amount) as pay_amount',
				'SUM(t1.real_pay_amount) as real_pay_amount'
			));
			$this->db->from('coop_finance_month_detail as t1');
			$this->db->where("t1.member_id = '".$value['member_id']."' AND profile_id = '".$value['profile_id']."'");
			$this->db->group_by('t1.member_id');
			$row = $this->db->get()->result_array();
			
			$array_data[$value['department']][$value['member_id']]['non_pay_amount'] = $row[0]['pay_amount'] - $row[0]['real_pay_amount'];
			$array_data[$value['department']][$value['member_id']]['pay_amount'] = $row[0]['pay_amount'];			
			$array_data[$value['department']][$value['member_id']]['balance'] = $row[0]['real_pay_amount'];
			$array_data[$value['department']][$value['member_id']]['member_name'] = $value["prename_full"].$value["firstname_th"]." ".$value["lastname_th"];			

			$array_data[$value['department']][$value['member_id']]['non_pay_reason'] = 'เงินเดือนไม่พอหัก';

			$total_data[$value['department']]['non_pay_amount'] += $row[0]['pay_amount'] - $row[0]['real_pay_amount'];
			$total_data[$value['department']]['pay_amount'] += $row[0]['pay_amount'];	
			$total_data[$value['department']]['balance'] += $row[0]['real_pay_amount'];

			$total_all_data['non_pay_amount'] += $row[0]['pay_amount'] - $row[0]['real_pay_amount'];
			$total_all_data['pay_amount'] += $row[0]['pay_amount'];	
			$total_all_data['balance'] += $row[0]['real_pay_amount'];
		}
		//echo '<pre>'; print_r($array_data); echo '</pre>';

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
		$this->preview_libraries->template_preview('report_processor_pay_some_data/coop_report_pay_some_by_member',$arr_data);
	}

	function coop_report_pay_some_by_member_excel(){
		$arr_data = array();

		$month = (@$_GET['month'] != '')?$_GET['month']:date('m');
		$year = (@$_GET['year'] != '')?$_GET['year']:(date('Y')+543);
		$date_start = ($year-543)."-".sprintf("%02d",@$month)."-01";

		$where_data = '';
		if(!empty($_GET['department']) && $_GET['department']!=0){
			$where_data .= " AND t3.department = '".$_GET['department']."'";
		}
		if(!empty($_GET['faction']) && $_GET['faction']!=0){
			$where_data .= " AND t3.faction = '".$_GET['faction']."'";
		}
		if(!empty($_GET['level']) && $_GET['level']!=0){
			$where_data .= " AND t3.level = '".$_GET['level']."'";
		}
		
		if (!empty($_GET["mem_type"]) && !in_array("all", $_GET["mem_type"])){
			$where_data .= " AND t3.mem_type_id IN (".implode(',', $_GET["mem_type"]).")";
		}
		
		
		if (!empty($_GET["mem_type"]) && !in_array("all", $_GET["mem_type"])){
			$where_data .= " AND t3.mem_type_id IN (".implode(',', $_GET["mem_type"]).")";
		}
		
		$this->db->select(array(
			't1.member_id',
			't1.profile_id',
			't3.department',
			't3.firstname_th',
			't3.lastname_th',
			't4.prename_full',
			't5.mem_group_name',
			't3.mem_type_id',
			't6.mem_type_name'
		));
		$this->db->from('coop_finance_month_detail AS t1');
		$this->db->join('coop_finance_month_profile AS t2','t1.profile_id = t2.profile_id','left');
		$this->db->join("(SELECT
									t3.member_id,
									t3.mem_type_id,
									IF(t4.level_old IS NULL, t3.level, t4.level_old) AS level,
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
							WHERE 1=1 ) as t3", "t1.member_id = t3.member_id", "inner");
		$this->db->join('coop_prename as t4','t3.prename_id = t4.prename_id','left');
		$this->db->join('coop_mem_group as t5','t3.level = t5.id','left');
		$this->db->join('coop_mem_type AS t6','t3.mem_type_id = t6.mem_type_id','left');
		$this->db->where("t2.profile_month = '".$_GET['month']."' AND t2.profile_year = '".$_GET['year']."' AND t1.pay_amount <> t1.real_pay_amount AND t1.real_pay_amount <> 0".$where_data);
		$this->db->group_by('t1.member_id');
		$this->db->order_by('t5.mem_group_id ASC,t1.member_id ASC');
		$row_data = $this->db->get()->result_array();
		//echo $this->db->last_query(); echo '<br>'; exit;
		$array_data = array();
		$total_data = array();
		$total_all_data = array();
		foreach($row_data as $key => $value){
			$array_data[$value['department']][$value['member_id']] = $value;
			$this->db->select(array(
				'SUM(t1.pay_amount) as pay_amount',
				'SUM(t1.real_pay_amount) as real_pay_amount'
			));
			$this->db->from('coop_finance_month_detail as t1');
			$this->db->where("t1.member_id = '".$value['member_id']."' AND profile_id = '".$value['profile_id']."'");
			$this->db->group_by('t1.member_id');
			$row = $this->db->get()->result_array();
			
			$array_data[$value['department']][$value['member_id']]['non_pay_amount'] = $row[0]['pay_amount'] - $row[0]['real_pay_amount'];
			$array_data[$value['department']][$value['member_id']]['pay_amount'] = $row[0]['pay_amount'];			
			$array_data[$value['department']][$value['member_id']]['balance'] = $row[0]['real_pay_amount'];
			$array_data[$value['department']][$value['member_id']]['member_name'] = $value["prename_full"].$value["firstname_th"]." ".$value["lastname_th"];			

			$array_data[$value['department']][$value['member_id']]['non_pay_reason'] = 'เงินเดือนไม่พอหัก';

			$total_data[$value['department']]['non_pay_amount'] += $row[0]['pay_amount'] - $row[0]['real_pay_amount'];
			$total_data[$value['department']]['pay_amount'] += $row[0]['pay_amount'];	
			$total_data[$value['department']]['balance'] += $row[0]['real_pay_amount'];

			$total_all_data['non_pay_amount'] += $row[0]['pay_amount'] - $row[0]['real_pay_amount'];
			$total_all_data['pay_amount'] += $row[0]['pay_amount'];	
			$total_all_data['balance'] += $row[0]['real_pay_amount'];
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
		$this->load->view('report_processor_pay_some_data/coop_report_pay_some_by_member_excel',$arr_data);
	}

	public function coop_report_pay_some_excel() {
		$_GET = $_POST;
		if($_POST['type_report'] == '0'){
			$this->coop_report_pay_some_by_department_excel();
		}else if($_POST['type_report'] == '1'){
			$this->coop_report_pay_some_by_member_detail_excel();
		}else if($_POST['type_report'] == '2'){
			$this->coop_report_pay_some_by_member_excel();
		}else{
			echo "<script>document.location.href='".base_url(PROJECTPATH.'/report_processor_pay_some_data/coop_report_pay_some')."';</script>";
		}
	}
}
