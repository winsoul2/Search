<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Report_facility extends CI_Controller {
	function __construct()
	{
		parent::__construct();
	}
	
	function remain() {
		$arr_data = array();
		
		$arr_data['facility_type'] = $this->db->select("*")->from("coop_facility_type")->order_by("facility_type_id")->get()->result_array();
		$arr_data['facility_status'] = $this->db->select("*")->from("coop_facility_status")->order_by("seq")->get()->result_array();
		
		$this->libraries->template('report_facility/remain',$arr_data);
	}
	
	function remain_check() {
		$where = "";
		if(!empty($_POST['facility_type_id'])) $where .= " AND coop_facility_main.facility_type_id = '".$_POST['facility_type_id']."'";
		if(!empty($_POST['facility_status_id'])) $where .= " AND coop_facility_store.facility_status_id = '".$_POST['facility_status_id']."'";
		
		$rs = $this->db->select("coop_facility_type.facility_type_name, coop_facility_store.facility_main_code, coop_facility_store.store_name, coop_facility_store.budget_year, COUNT(coop_facility_store.store_id) AS qty, MAX(coop_facility_store.store_price) AS store_price, coop_facility_status.facility_status_name")
						->from("coop_facility_store")
						->join("coop_facility_main", "coop_facility_store.facility_main_code = coop_facility_main.facility_main_code", "inner")
						->join("coop_facility_type", "coop_facility_main.facility_type_id = coop_facility_type.facility_type_id", "inner")
						->join("coop_facility_status", "coop_facility_store.facility_status_id = coop_facility_status.facility_status_id", "inner")
						->where("store_status = '0' {$where}")
						->group_by("coop_facility_type.facility_type_name, coop_facility_store.facility_main_code, coop_facility_store.store_name, coop_facility_store.budget_year, coop_facility_status.facility_status_name")
						->limit(1)
						->get()->result_array();
		
		if(!empty($rs)){
			echo "success";
		}else{
			echo "";
		}
	}
	
	function remain_preview() {
		$arr_data = array();
		
		$where = "";
		if(!empty($_GET['facility_type_id'])) $where .= " AND coop_facility_main.facility_type_id = '".$_GET['facility_type_id']."'";
		if(!empty($_GET['facility_status_id'])) $where .= " AND coop_facility_store.facility_status_id = '".$_GET['facility_status_id']."'";
		
		$x=0;
		$join_arr = array();
		$join_arr[$x]['table'] = "coop_facility_main";
		$join_arr[$x]['condition'] = "coop_facility_store.facility_main_code = coop_facility_main.facility_main_code";
		$join_arr[$x]['type'] = "inner";
		$x++;
		$join_arr[$x]['table'] = "coop_facility_type";
		$join_arr[$x]['condition'] = "coop_facility_main.facility_type_id = coop_facility_type.facility_type_id";
		$join_arr[$x]['type'] = "inner";
		$x++;
		$join_arr[$x]['table'] = "coop_facility_status";
		$join_arr[$x]['condition'] = "coop_facility_store.facility_status_id = coop_facility_status.facility_status_id";
		$join_arr[$x]['type'] = "inner";
		
		$this->paginater_all_preview->type(DB_TYPE);
		$this->paginater_all_preview->select("coop_facility_type.facility_type_name, coop_facility_store.facility_main_code, coop_facility_store.store_name, coop_facility_store.budget_year, COUNT(coop_facility_store.store_id) AS qty, MAX(coop_facility_store.store_price) AS store_price, coop_facility_status.facility_status_name, MIN(coop_facility_store.is_alert_remain) AS is_alert_remain, MIN(coop_facility_store.alert_remain) AS alert_remain");
		$this->paginater_all_preview->main_table("coop_facility_store");
		$this->paginater_all_preview->where("store_status = '0' {$where}");
		$this->paginater_all_preview->page_now(@$_GET["page"]);
		$this->paginater_all_preview->per_page(20);
		$this->paginater_all_preview->page_link_limit(34);
		$this->paginater_all_preview->page_limit_first(28);
		$this->paginater_all_preview->join_arr($join_arr);
		$this->paginater_all_preview->group_by("coop_facility_type.facility_type_name, coop_facility_store.facility_main_code, coop_facility_store.store_name, coop_facility_store.budget_year, coop_facility_status.facility_status_name");
		$row = $this->paginater_all_preview->paginater_process();

		$runno = (($row['page'] * 100) - 100) + 1;
		foreach($row['data'] AS $key=>$value){
			foreach($value AS $key2=>$value2){
				$row['data'][$key][$key2]['runno'] = $runno;
				$runno++;
			}
		}
		$arr_data['num_rows'] = $row['num_rows'];
		$arr_data['paging'] = $paging;
		$arr_data['data'] = $row['data'];
		$arr_data['page_all'] = $row['page_all'];
		
		$this->preview_libraries->template_preview('report_facility/remain_preview',$arr_data);
	}
	
	function remain_excel() {
		$arr_data = array();
		
		$where = "";
		if(!empty($_GET['facility_type_id'])) $where .= " AND coop_facility_main.facility_type_id = '".$_GET['facility_type_id']."'";
		if(!empty($_GET['facility_status_id'])) $where .= " AND coop_facility_store.facility_status_id = '".$_GET['facility_status_id']."'";
		
		$rs = $this->db->select("coop_facility_type.facility_type_name, coop_facility_store.facility_main_code, coop_facility_store.store_name, coop_facility_store.budget_year, COUNT(coop_facility_store.store_id) AS qty, MAX(coop_facility_store.store_price) AS store_price, coop_facility_status.facility_status_name, MIN(coop_facility_store.is_alert_remain) AS is_alert_remain, MIN(coop_facility_store.alert_remain) AS alert_remain")
			->from("coop_facility_store")
			->join("coop_facility_main", "coop_facility_store.facility_main_code = coop_facility_main.facility_main_code", "inner")
			->join("coop_facility_type", "coop_facility_main.facility_type_id = coop_facility_type.facility_type_id", "inner")
			->join("coop_facility_status", "coop_facility_store.facility_status_id = coop_facility_status.facility_status_id", "inner")
			->where("store_status = '0' {$where}")
			->group_by("coop_facility_type.facility_type_name, coop_facility_store.facility_main_code, coop_facility_store.store_name, coop_facility_store.budget_year, coop_facility_status.facility_status_name")
			->get()->result_array();
		
		$arr_data['data'] = $rs;
		
		$this->load->view('report_facility/remain_excel',$arr_data);	
	}
	
	function depreciation() {
		$arr_data = array();
		
		$arr_data['facility_type'] = $this->db->select("*")->from("coop_facility_type")->order_by("facility_type_id")->get()->result_array();
		
		$this->libraries->template('report_facility/depreciation',$arr_data);
	}
	
	function depreciation_check() {
		$where = "";
		if(!empty($_POST['facility_type_id'])) $where .= " AND coop_facility_main.facility_type_id = '".$_POST['facility_type_id']."'";
		
		$rs = $this->db->select("coop_facility_store.*")
						->from("coop_facility_store")
						->join("coop_facility_main", "coop_facility_store.facility_main_code = coop_facility_main.facility_main_code", "inner")
						->join("coop_facility_type", "coop_facility_main.facility_type_id = coop_facility_type.facility_type_id", "inner")
						->join("coop_facility_status", "coop_facility_store.facility_status_id = coop_facility_status.facility_status_id", "inner")
						->where("1 = 1 {$where}")
						->limit(1)
						->get()->result_array();
		
		if(!empty($rs)){
			echo "success";
		}else{
			echo "";
		}
	}
	
	function depreciation_preview() {
		$arr_data = array();
		
		$where = "";
		if(!empty($_GET['facility_type_id'])) $where .= " AND coop_facility_main.facility_type_id = '".$_GET['facility_type_id']."'";
		
		$x=0;
		$join_arr = array();
		$join_arr[$x]['table'] = "coop_facility_main";
		$join_arr[$x]['condition'] = "coop_facility_store.facility_main_code = coop_facility_main.facility_main_code";
		$join_arr[$x]['type'] = "inner";
		$x++;
		$join_arr[$x]['table'] = "coop_facility_type";
		$join_arr[$x]['condition'] = "coop_facility_main.facility_type_id = coop_facility_type.facility_type_id";
		$join_arr[$x]['type'] = "inner";
		$x++;
		$join_arr[$x]['table'] = "coop_facility_status";
		$join_arr[$x]['condition'] = "coop_facility_store.facility_status_id = coop_facility_status.facility_status_id";
		$join_arr[$x]['type'] = "inner";
		
		$this->paginater_all->type(DB_TYPE);
		$this->paginater_all->select("coop_facility_store.*, coop_facility_type.facility_type_name, coop_facility_status.facility_status_name");
		$this->paginater_all->main_table("coop_facility_store");
		$this->paginater_all->where("1 = 1 {$where}");
		$this->paginater_all->order_by("coop_facility_store.budget_year, coop_facility_store.store_code");
		$this->paginater_all->page_now(@$_GET["page"]);
		$this->paginater_all->per_page(23);
		$this->paginater_all->page_link_limit(36);
		$this->paginater_all->join_arr($join_arr);
		$row = $this->paginater_all->paginater_process();
		
		$paging = $this->pagination_center->paginating(intval($row['page']), $row['num_rows'], $row['per_page'], $row['page_link_limit'],@$_GET);//$page_now = 1, $row_total = 1, $per_page = 20, $page_limit = 20
		$runno = (($row['page'] * 100) - 100) + 1;
		$year_count = 0;
		foreach($row['data'] AS $key2=>$value2){
			$row['data'][$key2]['runno'] = $runno;
			$runno++;
			
			$row['data'][$key2]['price_years'] = [];
			$_rs = $this->db->select("*")
						->from("coop_facility_depreciation")
						->where("store_id = '{$value2["store_id"]}'")
						->order_by("depreciation_year")
						->get()->result_array();
			foreach($_rs as $_row) {
				$row['data'][$key2]['price_years'][] = $_row["depreciation_price"];
			}
			
			$year_count = count($_rs) > $year_count ? count($_rs) : $year_count;
		}
		$arr_data['num_rows'] = $row['num_rows'];
		$arr_data['paging'] = $paging;
		$arr_data['data'] = $row['data'];
		$arr_data['page_all'] = ceil($row['num_rows']/$row['per_page']);
		$arr_data['page'] = $row['page'];
		$arr_data['year_count'] = $year_count;
		
		if (intval($row['page']) == ceil($row['num_rows']/$row['per_page'])) {
			$rs = $this->db->select("coop_facility_store.*, coop_facility_type.facility_type_name, coop_facility_status.facility_status_name")
				->from("coop_facility_store")
				->join("coop_facility_main", "coop_facility_store.facility_main_code = coop_facility_main.facility_main_code", "inner")
				->join("coop_facility_type", "coop_facility_main.facility_type_id = coop_facility_type.facility_type_id", "inner")
				->join("coop_facility_status", "coop_facility_store.facility_status_id = coop_facility_status.facility_status_id", "inner")
				->where("1 = 1 {$where}")
				->order_by("coop_facility_store.budget_year, coop_facility_store.store_code")
				->get()->result_array();
			
			$total_depreciation_prices = [];
			foreach($rs AS $key2=>$value2){
				$total_depreciation_prices[0] += $value2["store_price"];
				$_rs = $this->db->select("*")
							->from("coop_facility_depreciation")
							->where("store_id = '{$value2["store_id"]}'")
							->order_by("depreciation_year")
							->get()->result_array();
				foreach($_rs as $_row) {
					$total_depreciation_prices[$_row["depreciation_year"]] += $_row["depreciation_price"];
				}
			}
			
			$arr_data['total_depreciation_prices'] = $total_depreciation_prices;
		}
		
		$this->preview_libraries->template_preview('report_facility/depreciation_preview',$arr_data);
	}
	
	function depreciation_excel() {
		$arr_data = array();
		
		$where = "";
		if(!empty($_GET['facility_type_id'])) $where .= " AND coop_facility_main.facility_type_id = '".$_GET['facility_type_id']."'";
		
		$rs = $this->db->select("coop_facility_store.*, coop_facility_type.facility_type_name, coop_facility_status.facility_status_name")
			->from("coop_facility_store")
			->join("coop_facility_main", "coop_facility_store.facility_main_code = coop_facility_main.facility_main_code", "inner")
			->join("coop_facility_type", "coop_facility_main.facility_type_id = coop_facility_type.facility_type_id", "inner")
			->join("coop_facility_status", "coop_facility_store.facility_status_id = coop_facility_status.facility_status_id", "inner")
			->where("1 = 1 {$where}")
			->order_by("coop_facility_store.budget_year, coop_facility_store.store_code")
			->get()->result_array();
		
		$year_count = 0;
		foreach($rs AS $key2=>$value2){
			$rs[$key2]['price_years'] = [];
			$_rs = $this->db->select("*")
						->from("coop_facility_depreciation")
						->where("store_id = '{$value2["store_id"]}'")
						->order_by("depreciation_year")
						->get()->result_array();
			foreach($_rs as $_row) {
				$rs[$key2]['price_years'][] = $_row["depreciation_price"];
			}
			
			$year_count = count($_rs) > $year_count ? count($_rs) : $year_count;
		}
		
		$arr_data['data'] = $rs;
		$arr_data['year_count'] = $year_count;
		
		$this->load->view('report_facility/depreciation_excel',$arr_data);	
	}
	
	function pickup() {
		$arr_data = array();
		
		$arr_data['facility_type'] = $this->db->select("*")->from("coop_facility_type")->order_by("facility_type_id")->get()->result_array();
		$arr_data['facility_status'] = $this->db->select("*")->from("coop_facility_status")->order_by("seq")->get()->result_array();
		
		$this->libraries->template('report_facility/pickup',$arr_data);
	}
	
	function pickup_check() {
		$year = $_POST['year'] - 543;
		
		if($_POST['pickup_type'] == '0') {
			$rs = $this->db->select("coop_facility_type.facility_type_name, coop_facility_store.facility_main_code, coop_facility_store.store_name, coop_facility_store.budget_year, COUNT(coop_facility_store.store_id) AS qty, coop_facility_store.department_name")
				->from("coop_facility_store")
				->join("coop_facility_main", "coop_facility_store.facility_main_code = coop_facility_main.facility_main_code", "inner")
				->join("coop_facility_type", "coop_facility_main.facility_type_id = coop_facility_type.facility_type_id", "inner")
				->where("MONTH(coop_facility_store.receive_date) = '{$_POST['month']}' AND YEAR(coop_facility_store.receive_date) = '{$year}'")
				->group_by("coop_facility_type.facility_type_name, coop_facility_store.facility_main_code, coop_facility_store.store_name, coop_facility_store.budget_year, coop_facility_store.department_name")
				->limit(1)
				->get()->result_array();
		}
		elseif($_POST['pickup_type'] == '1') {
			$rs = $this->db->select("coop_facility_type.facility_type_name, coop_facility_store.facility_main_code, coop_facility_store.store_name, coop_facility_store.budget_year, COUNT(coop_facility_store.store_id) AS qty, coop_facility_store.department_name")
				->from("coop_facility_store")
				->join("coop_facility_main", "coop_facility_store.facility_main_code = coop_facility_main.facility_main_code", "inner")
				->join("coop_facility_type", "coop_facility_main.facility_type_id = coop_facility_type.facility_type_id", "inner")
				->join("coop_facility_take_detail", "coop_facility_store.store_id = coop_facility_take_detail.store_id", "inner")
				->join("coop_facility_take", "coop_facility_take_detail.facility_take_id = coop_facility_take.facility_take_id", "inner")
				->where("MONTH(coop_facility_take.sign_date) = '{$_POST['month']}' AND YEAR(coop_facility_take.sign_date) = '{$year}'")
				->group_by("coop_facility_type.facility_type_name, coop_facility_store.facility_main_code, coop_facility_store.store_name, coop_facility_store.budget_year, coop_facility_store.department_name")
				->limit(1)
				->get()->result_array();
		}
		
		if(!empty($rs)){
			echo "success";
		}else{
			echo "";
		}
	}
	
	function pickup_preview() {
		$arr_data = array();
		$year = $_GET['year'] - 543;
		
		if($_GET['pickup_type'] == '0') {
			$x=0;
			$join_arr = array();
			$join_arr[$x]['table'] = "coop_facility_main";
			$join_arr[$x]['condition'] = "coop_facility_store.facility_main_code = coop_facility_main.facility_main_code";
			$join_arr[$x]['type'] = "inner";
			$x++;
			$join_arr[$x]['table'] = "coop_facility_type";
			$join_arr[$x]['condition'] = "coop_facility_main.facility_type_id = coop_facility_type.facility_type_id";
			$join_arr[$x]['type'] = "inner";
			
			$this->paginater_all_preview->type(DB_TYPE);
			$this->paginater_all_preview->select("coop_facility_type.facility_type_name, coop_facility_store.facility_main_code, coop_facility_store.store_name, coop_facility_store.budget_year, COUNT(coop_facility_store.store_id) AS qty, coop_facility_store.department_name");
			$this->paginater_all_preview->main_table("coop_facility_store");
			$this->paginater_all_preview->where("MONTH(coop_facility_store.receive_date) = '{$_GET['month']}' AND YEAR(coop_facility_store.receive_date) = '{$year}'");
			$this->paginater_all_preview->page_now(@$_GET["page"]);
			$this->paginater_all_preview->per_page(100);
			$this->paginater_all_preview->page_link_limit(36);
			$this->paginater_all_preview->page_limit_first(28);
			$this->paginater_all_preview->join_arr($join_arr);
			$this->paginater_all_preview->group_by("coop_facility_type.facility_type_name, coop_facility_store.facility_main_code, coop_facility_store.store_name, coop_facility_store.budget_year, coop_facility_store.department_name");
			$row = $this->paginater_all_preview->paginater_process();

			$runno = (($row['page'] * 100) - 100) + 1;
			foreach($row['data'] AS $key=>$value){
				foreach($value AS $key2=>$value2){
					$row['data'][$key][$key2]['runno'] = $runno;
					$runno++;
				}
			}
			$arr_data['num_rows'] = $row['num_rows'];
			$arr_data['paging'] = $paging;
			$arr_data['data'] = $row['data'];
			$arr_data['page_all'] = $row['page_all'];
			$arr_data['page'] = $row['page'];
		}
		elseif($_GET['pickup_type'] == '1') {
			$x=0;
			$join_arr = array();
			$join_arr[$x]['table'] = "coop_facility_main";
			$join_arr[$x]['condition'] = "coop_facility_store.facility_main_code = coop_facility_main.facility_main_code";
			$join_arr[$x]['type'] = "inner";
			$x++;
			$join_arr[$x]['table'] = "coop_facility_type";
			$join_arr[$x]['condition'] = "coop_facility_main.facility_type_id = coop_facility_type.facility_type_id";
			$join_arr[$x]['type'] = "inner";
			$x++;
			$join_arr[$x]['table'] = "coop_facility_take_detail";
			$join_arr[$x]['condition'] = "coop_facility_store.store_id = coop_facility_take_detail.store_id";
			$join_arr[$x]['type'] = "inner";
			$x++;
			$join_arr[$x]['table'] = "coop_facility_take";
			$join_arr[$x]['condition'] = "coop_facility_take_detail.facility_take_id = coop_facility_take.facility_take_id";
			$join_arr[$x]['type'] = "inner";
			
			$this->paginater_all_preview->type(DB_TYPE);
			$this->paginater_all_preview->select("coop_facility_type.facility_type_name, coop_facility_store.facility_main_code, coop_facility_store.store_name, coop_facility_store.budget_year, COUNT(coop_facility_store.store_id) AS qty, coop_facility_store.department_name");
			$this->paginater_all_preview->main_table("coop_facility_store");
			$this->paginater_all_preview->where("MONTH(coop_facility_take.sign_date) = '{$_GET['month']}' AND YEAR(coop_facility_take.sign_date) = '{$year}'");
			$this->paginater_all_preview->page_now(@$_GET["page"]);
			$this->paginater_all_preview->per_page(100);
			$this->paginater_all_preview->page_link_limit(36);
			$this->paginater_all_preview->page_limit_first(28);
			$this->paginater_all_preview->join_arr($join_arr);
			$this->paginater_all_preview->group_by("coop_facility_type.facility_type_name, coop_facility_store.facility_main_code, coop_facility_store.store_name, coop_facility_store.budget_year, coop_facility_store.department_name");
			$row = $this->paginater_all_preview->paginater_process();

			$runno = (($row['page'] * 100) - 100) + 1;
			foreach($row['data'] AS $key=>$value){
				foreach($value AS $key2=>$value2){
					$row['data'][$key][$key2]['runno'] = $runno;
					$runno++;
				}
			}
			$arr_data['num_rows'] = $row['num_rows'];
			$arr_data['paging'] = $paging;
			$arr_data['data'] = $row['data'];
			$arr_data['page_all'] = $row['page_all'];
			$arr_data['page'] = $row['page'];
			
			if (intval($row['page']) == ceil($row['num_rows']/$row['per_page'])) {
				$row = $this->db->select("COUNT(coop_facility_store.store_id) AS total_qty")
					->from("coop_facility_store")
					->join("coop_facility_main", "coop_facility_store.facility_main_code = coop_facility_main.facility_main_code", "inner")
					->join("coop_facility_type", "coop_facility_main.facility_type_id = coop_facility_type.facility_type_id", "inner")
					->join("coop_facility_take_detail", "coop_facility_store.store_id = coop_facility_take_detail.store_id", "inner")
					->join("coop_facility_take", "coop_facility_take_detail.facility_take_id = coop_facility_take.facility_take_id", "inner")
					->where("MONTH(coop_facility_take.sign_date) = '{$_GET['month']}' AND YEAR(coop_facility_take.sign_date) = '{$year}'")
					->get()->row_array();
				$arr_data['total_qty'] = $row['total_qty'];
			}
		}
		
		$this->preview_libraries->template_preview('report_facility/pickup_preview',$arr_data);
	}
	
	function pickup_excel() {
		$arr_data = array();
		$year = $_GET['year'] - 543;
		
		if($_GET['pickup_type'] == '0') {
			$rs = $this->db->select("coop_facility_type.facility_type_name, coop_facility_store.facility_main_code, coop_facility_store.store_name, coop_facility_store.budget_year, COUNT(coop_facility_store.store_id) AS qty, coop_facility_store.department_name")
				->from("coop_facility_store")
				->join("coop_facility_main", "coop_facility_store.facility_main_code = coop_facility_main.facility_main_code", "inner")
				->join("coop_facility_type", "coop_facility_main.facility_type_id = coop_facility_type.facility_type_id", "inner")
				->where("MONTH(coop_facility_store.receive_date) = '{$_GET['month']}' AND YEAR(coop_facility_store.receive_date) = '{$year}'")
				->group_by("coop_facility_type.facility_type_name, coop_facility_store.facility_main_code, coop_facility_store.store_name, coop_facility_store.budget_year, coop_facility_store.department_name")
				->get()->result_array();
		}
		elseif($_GET['pickup_type'] == '1') {
			$rs = $this->db->select("coop_facility_type.facility_type_name, coop_facility_store.facility_main_code, coop_facility_store.store_name, coop_facility_store.budget_year, COUNT(coop_facility_store.store_id) AS qty, coop_facility_store.department_name")
				->from("coop_facility_store")
				->join("coop_facility_main", "coop_facility_store.facility_main_code = coop_facility_main.facility_main_code", "inner")
				->join("coop_facility_type", "coop_facility_main.facility_type_id = coop_facility_type.facility_type_id", "inner")
				->join("coop_facility_take_detail", "coop_facility_store.store_id = coop_facility_take_detail.store_id", "inner")
				->join("coop_facility_take", "coop_facility_take_detail.facility_take_id = coop_facility_take.facility_take_id", "inner")
				->where("MONTH(coop_facility_take.sign_date) = '{$_GET['month']}' AND YEAR(coop_facility_take.sign_date) = '{$year}'")
				->group_by("coop_facility_type.facility_type_name, coop_facility_store.facility_main_code, coop_facility_store.store_name, coop_facility_store.budget_year, coop_facility_store.department_name")
				->get()->result_array();
		}
		
		$arr_data['data'] = $rs;
		
		$this->load->view('report_facility/pickup_excel',$arr_data);	
	}
	
	function compare() {
		$arr_data = array();
		
		$arr_data['facility_type'] = $this->db->select("*")->from("coop_facility_type")->order_by("facility_type_id")->get()->result_array();
		$arr_data['facility_status'] = $this->db->select("*")->from("coop_facility_status")->order_by("seq")->get()->result_array();
		
		$this->libraries->template('report_facility/compare',$arr_data);
	}
	
	function compare_check() {
		$where = "";
		if(!empty($_POST['facility_type_id'])) $where .= " AND coop_facility_main.facility_type_id = '".$_POST['facility_type_id']."'";
		
		$rs = $this->db->select("coop_facility_store.*")
						->from("coop_facility_store")
						->join("coop_facility_main", "coop_facility_store.facility_main_code = coop_facility_main.facility_main_code", "inner")
						->join("coop_facility_type", "coop_facility_main.facility_type_id = coop_facility_type.facility_type_id", "inner")
						->join("coop_facility_status", "coop_facility_store.facility_status_id = coop_facility_status.facility_status_id", "inner")
						->where("1 = 1 {$where}")
						->limit(1)
						->get()->result_array();
		
		if(!empty($rs)){
			echo "success";
		}else{
			echo "";
		}
	}
	
	function compare_preview() {
		$arr_data = array();
		
		$where = "";
		if(!empty($_GET['facility_type_id'])) $where .= " AND coop_facility_main.facility_type_id = '".$_GET['facility_type_id']."'";
		$where .= " AND coop_facility_store.budget_year <= '".($_GET['year1'] > $_GET['year2'] ? $_GET['year1'] : $_GET['year2'])."'";
		
		$x=0;
		$join_arr = array();
		$join_arr[$x]['table'] = "coop_facility_main";
		$join_arr[$x]['condition'] = "coop_facility_store.facility_main_code = coop_facility_main.facility_main_code";
		$join_arr[$x]['type'] = "inner";
		$x++;
		$join_arr[$x]['table'] = "coop_facility_type";
		$join_arr[$x]['condition'] = "coop_facility_main.facility_type_id = coop_facility_type.facility_type_id";
		$join_arr[$x]['type'] = "inner";
		
		$this->paginater_all->type(DB_TYPE);
		$this->paginater_all->select("coop_facility_type.facility_type_name, coop_facility_store.facility_main_code, coop_facility_store.store_name, coop_facility_store.budget_year, COUNT(coop_facility_store.store_id) AS qty, MAX(coop_facility_store.store_price) AS store_price");
		$this->paginater_all->main_table("coop_facility_store");
		$this->paginater_all->where("store_status = '0' AND coop_facility_store.facility_status_id = '1' {$where}");
		$this->paginater_all->page_now(@$_GET["page"]);
		$this->paginater_all->per_page(100);
		$this->paginater_all->page_link_limit(36);
		$this->paginater_all->join_arr($join_arr);
		$this->paginater_all->group_by("coop_facility_type.facility_type_name, coop_facility_store.facility_main_code, coop_facility_store.store_name, coop_facility_store.budget_year");
		$row = $this->paginater_all->paginater_process();

		$paging = $this->pagination_center->paginating(intval($row['page']), $row['num_rows'], $row['per_page'], $row['page_link_limit'],@$_GET);//$page_now = 1, $row_total = 1, $per_page = 20, $page_limit = 20
		$runno = (($row['page'] * 100) - 100) + 1;
		foreach($row['data'] AS $key2=>$value2){
			$row['data'][$key2]['runno'] = $runno;
			$runno++;
			
			$row['data'][$key2]['year1'] = $_GET['year1'];
			$row['data'][$key2]['year2'] = $_GET['year2'];
			
			for($i = 1; $i <= 2; $i++) {
				$_row = $this->db->select("coop_facility_store.facility_main_code, COUNT(coop_facility_store.store_id) AS qty, MAX(coop_facility_depreciation.depreciation_price) AS store_price")
							->from("coop_facility_store")
							->join("coop_facility_depreciation", "coop_facility_store.store_id = coop_facility_depreciation.store_id", "inner")
							->where("coop_facility_store.facility_main_code = '{$value2["facility_main_code"]}' AND coop_facility_depreciation.depreciation_year = '".$_GET['year'.$i]."'")
							->group_by("coop_facility_store.facility_main_code")
							->get()->row_array();
				if(!empty($_row)) {
					$row['data'][$key2]['qty'.$i] = $_row["qty"];
					$row['data'][$key2]['store_price'.$i] = $_row["store_price"];
				}
				else {
					$row['data'][$key2]['qty'.$i] = $value2["qty"];
					$row['data'][$key2]['store_price'.$i] = $value2["store_price"];
				}
			}
		}
		$arr_data['num_rows'] = $row['num_rows'];
		$arr_data['paging'] = $paging;
		$arr_data['data'] = $row['data'];
		$arr_data['page_all'] = ceil($row['num_rows']/$row['per_page']);
		$arr_data['page'] = $row['page'];
		
		if (intval($row['page']) == ceil($row['num_rows']/$row['per_page'])) {
			$rs = $this->db->select("coop_facility_type.facility_type_name, coop_facility_store.facility_main_code, coop_facility_store.store_name, coop_facility_store.budget_year, COUNT(coop_facility_store.store_id) AS qty, MAX(coop_facility_store.store_price) AS store_price")
				->from("coop_facility_store")
				->join("coop_facility_main", "coop_facility_store.facility_main_code = coop_facility_main.facility_main_code", "inner")
				->join("coop_facility_type", "coop_facility_main.facility_type_id = coop_facility_type.facility_type_id", "inner")
				->where("store_status = '0' AND coop_facility_store.facility_status_id = '1' {$where}")
				->group_by("coop_facility_type.facility_type_name, coop_facility_store.facility_main_code, coop_facility_store.store_name, coop_facility_store.budget_year")
				->get()->result_array();
			
			$totals = [];
			$totals['qty1'] = 0;
			$totals['store_price1'] = 0;
			$totals['qty2'] = 0;
			$totals['store_price2'] = 0;
			foreach($rs AS $key2=>$value2){
				for($i = 1; $i <= 2; $i++) {
					$_row = $this->db->select("coop_facility_store.facility_main_code, COUNT(coop_facility_store.store_id) AS qty, MAX(coop_facility_depreciation.depreciation_price) AS store_price")
								->from("coop_facility_store")
								->join("coop_facility_depreciation", "coop_facility_store.store_id = coop_facility_depreciation.store_id", "inner")
								->where("coop_facility_store.facility_main_code = '{$value2["facility_main_code"]}' AND coop_facility_depreciation.depreciation_year = '".$_GET['year'.$i]."'")
								->group_by("coop_facility_store.facility_main_code")
								->get()->row_array();
					if(!empty($_row)) {
						$rs[$key2]['qty'.$i] = $_row["qty"];
						$rs[$key2]['store_price'.$i] = $_row["store_price"];
					}
					else {
						$rs[$key2]['qty'.$i] = $value2["qty"];
						$rs[$key2]['store_price'.$i] = $value2["store_price"];
					}
					
					$totals['qty'.$i] += $rs[$key2]['qty'.$i];
					$totals['store_price'.$i] += $rs[$key2]['store_price'.$i];
				}
			}
			
			$arr_data['totals'] = $totals;
		}
		
		$this->preview_libraries->template_preview('report_facility/compare_preview',$arr_data);
	}
	
	function compare_excel() {
		$arr_data = array();
		
		$where = "";
		if(!empty($_GET['facility_type_id'])) $where .= " AND coop_facility_main.facility_type_id = '".$_GET['facility_type_id']."'";
		$where .= " AND coop_facility_store.budget_year <= '".($_GET['year1'] > $_GET['year2'] ? $_GET['year1'] : $_GET['year2'])."'";
		
		$rs = $this->db->select("coop_facility_type.facility_type_name, coop_facility_store.facility_main_code, coop_facility_store.store_name, coop_facility_store.budget_year, COUNT(coop_facility_store.store_id) AS qty, MAX(coop_facility_store.store_price) AS store_price")
			->from("coop_facility_store")
			->join("coop_facility_main", "coop_facility_store.facility_main_code = coop_facility_main.facility_main_code", "inner")
			->join("coop_facility_type", "coop_facility_main.facility_type_id = coop_facility_type.facility_type_id", "inner")
			->where("store_status = '0' AND coop_facility_store.facility_status_id = '1' {$where}")
			->group_by("coop_facility_type.facility_type_name, coop_facility_store.facility_main_code, coop_facility_store.store_name, coop_facility_store.budget_year")
			->get()->result_array();
		
		foreach($rs AS $key2=>$value2){
			$rs[$key2]['year1'] = $_GET['year1'];
			$rs[$key2]['year2'] = $_GET['year2'];
			
			for($i = 1; $i <= 2; $i++) {
				$_row = $this->db->select("coop_facility_store.facility_main_code, COUNT(coop_facility_store.store_id) AS qty, MAX(coop_facility_depreciation.depreciation_price) AS store_price")
							->from("coop_facility_store")
							->join("coop_facility_depreciation", "coop_facility_store.store_id = coop_facility_depreciation.store_id", "inner")
							->where("coop_facility_store.facility_main_code = '{$value2["facility_main_code"]}' AND coop_facility_depreciation.depreciation_year = '".$_GET['year'.$i]."'")
							->group_by("coop_facility_store.facility_main_code")
							->get()->row_array();
				if(!empty($_row)) {
					$rs[$key2]['qty'.$i] = $_row["qty"];
					$rs[$key2]['store_price'.$i] = $_row["store_price"];
				}
				else {
					$rs[$key2]['qty'.$i] = $value2["qty"];
					$rs[$key2]['store_price'.$i] = $value2["store_price"];
				}
			}
		}
		
		$arr_data['data'] = $rs;
		
		$this->load->view('report_facility/compare_excel',$arr_data);	
	}
	
}