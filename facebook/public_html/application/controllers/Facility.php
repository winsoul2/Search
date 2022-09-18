<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Facility extends CI_Controller {
	function __construct()
	{
		parent::__construct();
	}
	public function index()
	{
		$arr_data = array();

		$x=0;
		$join_arr = array();
		$join_arr[$x]['table'] = 'coop_facility_status';
		$join_arr[$x]['condition'] = 'coop_facility_store.facility_status_id = coop_facility_status.facility_status_id';
		$join_arr[$x]['type'] = 'left';
		
		$this->paginater_all->type(DB_TYPE);
		$this->paginater_all->select('coop_facility_store.*, coop_facility_status.facility_status_name');
		$this->paginater_all->main_table('coop_facility_store');
		$this->paginater_all->where("store_status = '0'");
		$this->paginater_all->page_now(@$_GET["page"]);
		$this->paginater_all->per_page(10);
		$this->paginater_all->page_link_limit(20);
		$this->paginater_all->order_by('store_id DESC');
		$this->paginater_all->join_arr($join_arr);
		$row = $this->paginater_all->paginater_process();
		//echo"<pre>";print_r($row);exit;
		$paging = $this->pagination_center->paginating($row['page'], $row['num_rows'], $row['per_page'], $row['page_link_limit']);//$page_now = 1, $row_total = 1, $per_page = 20, $page_limit = 20
		$i = $row['page_start'];

		foreach($row['data'] as $key => $value) {
			$this->db->select('*');
			$this->db->from('coop_facility_depreciation');
			$this->db->where("store_id = '".$value['store_id']."'");
			$this->db->order_by('depreciation_year DESC');
			$this->db->limit(1);
			$_row = $this->db->get()->row_array();
			$row['data'][$key]['depreciation_price'] = empty($_row['depreciation_price']) ? $value['store_price'] : $_row['depreciation_price'];
		}

		$arr_data['num_rows'] = $row['num_rows'];
		$arr_data['paging'] = $paging;
		$arr_data['row'] = $row['data'];
		$arr_data['i'] = $i;


		$this->libraries->template('facility/index',$arr_data);
	}

	public function add(){
		$arr_data = array();
		$id = @$_GET['s_id'];
		if($id!=''){
			$this->db->select('*');
			$this->db->from('coop_facility_store');
			$this->db->where("store_id = '".$id."'");
			$rs = $this->db->get()->result_array();
			$row= @$rs[0];
			$arr_data['data'] = @$row;
			
			$facility_main_code = $row['facility_main_code'];
			$this->db->select('coop_facility_store.*, coop_facility_status.facility_status_name');
			$this->db->from('coop_facility_store');
			$this->db->join('coop_facility_status', 'coop_facility_store.facility_status_id = coop_facility_status.facility_status_id', 'left');
			$this->db->where("facility_main_code = '".$facility_main_code."' AND store_status = '0'");
			$rs = $this->db->get()->result_array();
			$arr_data['row'] = @$rs;
			//exit;
			
		}else{
			$arr_data['data'] = array();
		}
		$arr_data['id'] = $id;

		//จัดซื้อโดย
		$this->db->select('*');
		$this->db->from('coop_personnel');
		$row = $this->db->get()->result_array();
		$arr_data['personnel'] = @$row;
		
		//ประเภทการจัดซื้อ
		$this->db->select('*');
		$this->db->from('coop_means_purchase');
		$row = $this->db->get()->result_array();
		$arr_data['means'] = @$row;
		
		//ประเภทเงิน
		$this->db->select('*');
		$this->db->from('coop_type_money');
		$row = $this->db->get()->result_array();
		$arr_data['type_money'] = @$row;
		
		//หลักฐาน
		$this->db->select('*');
		$this->db->from('coop_type_evidence');
		$row = $this->db->get()->result_array();
		$arr_data['type_evidence'] = @$row;
		
		//ผู้ขาย
		$this->db->select(array('seller_id','seller_name'));
		$this->db->from('coop_seller');
		$row = $this->db->get()->result_array();
		$arr_data['seller'] = @$row;
		//print_r($this->db->last_query());exit;
		
		//หน่วยนับ
		$this->db->select(array('unit_type_id','unit_type_name'));
		$this->db->from('coop_unit_type');
		$rs_type = $this->db->get()->result_array();
		$arr_unit_type = array();
		if(!empty($rs_type)){
			foreach($rs_type AS $row_type){
				$arr_unit_type[$row_type['unit_type_id']] = $row_type['unit_type_name'];
			}
		}
		$arr_data['arr_unit_type'] = @$arr_unit_type;
		
		//สถานะ
		$this->db->select("*");
		$this->db->from("coop_facility_status");
		$this->db->order_by("seq");
		$row = $this->db->get()->result_array();
		$arr_data['facility_status'] = @$row;
		
		$this->libraries->template('facility/add',$arr_data);
	}

	function store_lb_upload(){
		$this->load->library('image');
		$this->load->view('facility/store_lb_upload');
	}

	function get_image(){
		if($_COOKIE["is_upload"]) {
			echo base_url(PROJECTPATH."/assets/uploads/tmp/".$_COOKIE["IMG"]);
		}
		exit();
	}

	function save_add(){
		$data_insert = array();
		$data = $this->input->post();
		$table = "coop_facility_store";
		$id_edit = @$data["store_id"] ;
		
		$store_code = @$data["store_code"];
		$facility_main_code_old = @$data["facility_main_code_old"];
		$store_run = @$data["store_run"];
		$facility_main_code = @$data["facility_main_code"];
		
		$this->db->select(array('MAX(store_run) AS last_run'));
		$this->db->from('coop_facility_store');
		$this->db->where("facility_main_code = '{$facility_main_code}'");
		$rs = $this->db->get()->result_array();
		$row = @$rs[0]; 
		
		if(!empty($id_edit) && $facility_main_code == $facility_main_code_old){
			$store_run_now = $store_run;	
			$now_id = sprintf("%03d",$store_run);	
		}else{
			$store_run_now = $row['last_run']+1;	
			$now_id = sprintf("%03d",$store_run_now);
		}		
		$store_code_now = $facility_main_code.'-'.$now_id;
	
		$data_insert['facility_main_code'] = $facility_main_code;
		$data_insert['store_run'] = $store_run_now;
		$data_insert['budget_year'] = $data['budget_year'];
		$data_insert['store_name'] = $data['store_name'];
		$data_insert['unit_type_id'] = $data['unit_type_id'];
		$data_insert['store_price'] = $data['store_price'];
		$data_insert['department_id'] = $data['department_id'];
		$data_insert['department_name'] = $data['department_name'];
		$data_insert['receive_date'] = $this->center_function->ConvertToSQLDate($data['receive_date']);
		$data_insert['personnel_id'] = $data['personnel_id'];
		$data_insert['means_id'] = $data['means_id'];
		$data_insert['type_money_id'] = $data['type_money_id'];
		$data_insert['certificate_no'] = $data['certificate_no'];
		$data_insert['type_evidence_id'] = $data['type_evidence_id'];
		$data_insert['start_date'] = $this->center_function->ConvertToSQLDate($data['start_date']);
		$data_insert['seller_id'] = $data['seller_id'];
		$data_insert['store_code'] = $store_code_now;
		//$data_insert['store_serial'] = $data['store_serial'];
		$data_insert['store_status'] = '0';
		$data_insert['updatetime'] = date('Y-m-d H:i:s');
		$data_insert['is_alert_remain'] = (int)$data['is_alert_remain'];
		$data_insert['alert_remain'] = $data['alert_remain'];
		
		if($id_edit!=''){
			$this->db->select(array('store_pic'));
			$this->db->from('coop_facility_store');
			$this->db->where("store_id = '".$data["store_id"]."'");
			$this->db->order_by('store_id DESC');
			$this->db->limit(1);
			$row = $this->db->get()->result_array();

			$output_dir = $_SERVER["DOCUMENT_ROOT"].PROJECTPATH."/assets/uploads/facility/";

			if(!empty($_COOKIE["is_upload"]) && !empty($_COOKIE["IMG"])) {
				$store_pic = $this->create_file_name($output_dir,$_COOKIE["IMG"]);
				@unlink($_SERVER["DOCUMENT_ROOT"].PROJECTPATH."/assets/uploads/facility/".$row[0]['store_pic']);
				@copy($_SERVER["DOCUMENT_ROOT"].PROJECTPATH."/assets/uploads/tmp/{$_COOKIE["IMG"]}", $_SERVER["DOCUMENT_ROOT"].PROJECTPATH."/assets/uploads/facility/{$store_pic}");
				@unlink($_SERVER["DOCUMENT_ROOT"].PROJECTPATH."/assets/uploads/tmp/{$_COOKIE["IMG"]}");
				
				setcookie("is_upload", "", time()-3600);
				setcookie("IMG", "", time()-3600);
				
				$data_insert['store_pic'] = $store_pic;
			}
			
			$this->db->where('store_id', $id_edit);
			$this->db->update($table, $data_insert);
			$store_id = $id_edit;
			$new = '';
			$this->db->select('*');
			$this->db->from('coop_facility_store');
			$this->db->where("store_id = '".$id_edit."'");
			$_row = $this->db->get()->row_array();
			$this->db->where('facility_main_code', $_row['facility_main_code']);
			$this->db->update($table, [
				'is_alert_remain' => $_row['is_alert_remain'],
				'alert_remain' => $_row['alert_remain']
			]);
		}else{
			$output_dir = $_SERVER["DOCUMENT_ROOT"].PROJECTPATH."/assets/uploads/facility/";

			if(!empty($_COOKIE["is_upload"]) && !empty($_COOKIE["IMG"])) {
				$store_pic = $this->create_file_name($output_dir,$_COOKIE["IMG"]);
				@copy($_SERVER["DOCUMENT_ROOT"].PROJECTPATH."/assets/uploads/tmp/{$_COOKIE["IMG"]}", $_SERVER["DOCUMENT_ROOT"].PROJECTPATH."/assets/uploads/facility/{$store_pic}");
				@unlink($_SERVER["DOCUMENT_ROOT"].PROJECTPATH."/assets/uploads/tmp/{$_COOKIE["IMG"]}");
				setcookie("is_upload", "", time()-3600);
				setcookie("IMG", "", time()-3600);
				$data_insert['store_pic'] = $store_pic;
			}
			
			$data_insert['createdatetime'] = date('Y-m-d H:i:s');
			$data_insert['facility_status_id'] = '1';
			$this->db->insert($table, $data_insert);
			$store_id = $this->db->insert_id();
			$new = '&n_id=1'; //รายการที่เพิ่มใหม่

		}
		
		$this->center_function->toast("บันทึกข้อมูลเรียบร้อยแล้ว");
		
		echo"<script> document.location.href='".PROJECTPATH."/facility/add?s_id={$store_id}{$new}' </script>";
		exit;
	}
	
	function save_form_quantity(){
		$data = $this->input->post();
		$data_insert = array();		
		$table = "coop_facility_store";
		$id_edit = @$data["store_id"] ;		
		//echo '<pre>'; print_r($data); echo '</pre>';
		
		$this->db->select('*');
		$this->db->from('coop_facility_store');
		$this->db->where("store_id = '".$data['store_id']."'");
		$rs = $this->db->get()->result_array();
		$row= @$rs[0];
		$facility_main_code = @$row['facility_main_code'];
		$data_insert['facility_main_code'] = @$facility_main_code;		
		$data_insert['budget_year'] = @$row['budget_year'];
		$data_insert['store_name'] = @$row['store_name'];
		$data_insert['unit_type_id'] = @$row['unit_type_id'];
		$data_insert['store_price'] = @$row['store_price'];
		$data_insert['department_id'] = @$row['department_id'];
		$data_insert['department_name'] = @$row['department_name'];
		$data_insert['store_no'] = @$row['store_no'];
		$data_insert['receive_date'] = @$row['receive_date'];
		$data_insert['personnel_id'] = @$row['personnel_id'];
		$data_insert['means_id'] = @$row['means_id'];
		$data_insert['type_money_id'] = @$row['type_money_id'];
		$data_insert['certificate_no'] = @$row['certificate_no'];
		$data_insert['type_evidence_id'] = @$row['type_evidence_id'];
		$data_insert['start_date'] = @$row['start_date'];
		$data_insert['seller_id'] = @$row['seller_id'];
		$data_insert['store_status'] = '0';
		$data_insert['updatetime'] = date('Y-m-d H:i:s');
		$data_insert['createdatetime'] = date('Y-m-d H:i:s');
		$data_insert['store_pic'] = @$row['store_pic'];
		$data_insert['facility_status_id'] = '1';
		$data_insert['is_alert_remain'] = @$row['is_alert_remain'];
		$data_insert['alert_remain'] = @$row['alert_remain'];
		
		//หาเลขรันพัสดุที่มากสุด
		$this->db->select(array('MAX(store_run) AS last_run'));
		$this->db->from('coop_facility_store');
		$this->db->where("facility_main_code = '{$facility_main_code}'");
		$rs_max = $this->db->get()->result_array();
		$row_max = @$rs_max[0]; 
	
		$store_run_now = $row_max['last_run'];	
		for($i=0;$i<$data['store_quantity'];$i++){
			
			$store_run_now ++;
			$now_id = sprintf("%03d",$store_run_now);	
			$store_code_now = $facility_main_code.'-'.$now_id;
			
			$data_insert['store_run'] = @$store_run_now;
			$data_insert['store_code'] = @$store_code_now;
			$this->db->insert($table, $data_insert);
			//echo '<pre>'; print_r($data_insert); echo '</pre>';	
		}
		echo true;
		exit;
	}
	
	function save_form_serial(){
		$data = $this->input->post();
		$data_insert = array();		
		$table = "coop_facility_store";
		$id_edit = @$data["store_id_serial"] ;		
	
		$data_insert['store_serial'] = @$data['store_serial'];
		$data_insert['facility_status_id'] = @$data['facility_status_id'];
		$data_insert['remark'] = @$data['remark'];
		
		$this->db->where('store_id', $id_edit);
		$this->db->update($table, $data_insert);
		echo true;
		exit;
	}

	function create_file_name($output_dir,$file_name){
		$list_dir = array();
		$cdir = scandir($output_dir);
		foreach ($cdir as $key => $value) {
			if (!in_array($value,array(".",".."))) {
				if (@is_dir(@$dir . DIRECTORY_SEPARATOR . $value)){
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
		$explode_old_file = explode('.',$file_name);
		$new_file_name = date('Ymd')."_".$max_run_num.".".$explode_old_file[(count($explode_old_file)-1)];
		return $new_file_name;
	}

	function get_search_facility(){
		$where = "
		 	(facility_main_id LIKE '%".$this->input->post('search_text')."%'
		 	OR facility_main_name LIKE '%".$this->input->post('search_text')."%')
		";
		$this->db->select(array('coop_facility_main.*','coop_unit_type.unit_type_name'));
		$this->db->from('coop_facility_main');
		$this->db->join('coop_unit_type', 'coop_unit_type.unit_type_id = coop_facility_main.unit_type_id', 'left');
		$this->db->where($where);
		$this->db->order_by('facility_main_id DESC');
		$row = $this->db->get()->result_array();
		$arr_data['data'] = @$row;
		$arr_data['form_target'] = $this->input->post('form_target');
		//echo"<pre>";print_r($arr_data['data']);exit;
		$this->load->view('facility/get_search_facility',$arr_data);
	}
	
	function get_search_department(){
		$where = "(department_name LIKE '%".$this->input->post('search_text')."%')";
		$this->db->select(array('department_id','department_name'));
		$this->db->from('coop_department');
		$this->db->where($where);
		$this->db->order_by('department_id DESC');
		$row = $this->db->get()->result_array();
		$arr_data['data'] = @$row;
		$arr_data['form_target'] = $this->input->post('form_target');
		//echo"<pre>";print_r($arr_data['data']);exit;
		$this->load->view('facility/get_search_department',$arr_data);
	}
	
	function get_search_store(){
		$where = "
		 	(store_no LIKE '%".$this->input->post('search_text')."%'
		 	OR facility_main_code LIKE '%".$this->input->post('search_text')."%' 
		 	OR store_name LIKE '%".$this->input->post('search_text')."%' 
		 	OR store_price LIKE '%".$this->input->post('search_text')."%' 
		 	OR department_name LIKE '%".$this->input->post('search_text')."%')
		";
		$this->db->select(array('store_id','store_no','store_code','facility_main_code','store_name','store_price','department_name','facility_status_name'));
		$this->db->from('coop_facility_store');
		$this->db->join('coop_facility_status', 'coop_facility_store.facility_status_id = coop_facility_status.facility_status_id', 'left');
		$this->db->where($where);
		$this->db->order_by('store_id DESC');
		$row = $this->db->get()->result_array();

		foreach($row as $key => $value) {
			$this->db->select('*');
			$this->db->from('coop_facility_depreciation');
			$this->db->where("store_id = '".$value['store_id']."'");
			$this->db->order_by('depreciation_year DESC');
			$this->db->limit(1);
			$_row = $this->db->get()->row_array();
			$row[$key]['depreciation_price'] = empty($_row['depreciation_price']) ? $value['store_price'] : $_row['depreciation_price'];
		}

		$arr_data['data'] = @$row;
		$arr_data['form_target'] = $this->input->post('form_target');
		//echo"<pre>";print_r($arr_data['data']);exit;
		$this->load->view('facility/get_search_store',$arr_data);
	}	

	function del_coop_data(){	
		$table = @$_POST['table'];
		$table_sub = @$_POST['table_sub'];
		$id = @$_POST['id'];
		$field = @$_POST['field'];


		if (!empty($table_sub)) {
			$this->db->where($field, $id );
			$this->db->delete($table_sub);	
        }

		$this->db->where($field, $id );
		$this->db->delete($table);
		$this->center_function->toast("ลบเรียบร้อยแล้ว");
		echo true;
		
	}
	
	function del_all(){
		$type_del = @$this->input->post('type_del');	
		$store_id = @$this->input->post('store_id');	
		
		$data = @$this->input->post('store_id');		
		foreach($data AS $value){
			$this->db->where('store_id', $value );
			$this->db->delete('coop_facility_store');
		}
		$this->center_function->toast("ลบเรียบร้อยแล้ว");

		echo"<script> document.location.href='".PROJECTPATH."/facility' </script>";
		exit;
	}
	
	function take_facility(){
		$arr_data = array();
		$id = @$_GET['id'];
		if($id!=''){
			$this->db->select('*');
			$this->db->from('coop_facility_take');
			$this->db->where("facility_take_id = '".$id."'");
			$rs = $this->db->get()->result_array();
			$row= @$rs[0];
			$arr_data['data'] = @$row;
			
			$this->db->select(array('coop_facility_take_detail.facility_take_id','coop_facility_store.*'));
			$this->db->from('coop_facility_take_detail');
			$this->db->join('coop_facility_store', 'coop_facility_store.store_id = coop_facility_take_detail.store_id', 'left');
			$this->db->where("facility_take_id = '".$id."'");
			$row = $this->db->get()->result_array();
			$arr_data['detail'] = @$row;
		
		}else{
			$arr_data['data'] = array();

			$x=0;
			$join_arr = array();
			$join_arr[$x]['table'] = 'coop_department';
			$join_arr[$x]['condition'] = 'coop_facility_take.department_id = coop_department.department_id';
			$join_arr[$x]['type'] = 'left';
			$x++;
			$join_arr[$x]['table'] = 'coop_personnel';
			$join_arr[$x]['condition'] = 'coop_facility_take.receiver_id = coop_personnel.personnel_id';
			$join_arr[$x]['type'] = 'left';
			
			$this->paginater_all->type(DB_TYPE);
			$this->paginater_all->select('coop_facility_take.*,coop_department.department_name,coop_personnel.personnel_name as receive_name');
			$this->paginater_all->main_table('coop_facility_take');
			$this->paginater_all->where("");
			$this->paginater_all->page_now(@$_GET["page"]);
			$this->paginater_all->per_page(10);
			$this->paginater_all->page_link_limit(20);
			$this->paginater_all->order_by('facility_take_id DESC');
			$this->paginater_all->join_arr($join_arr);
			$row = $this->paginater_all->paginater_process();
			//echo"<pre>";print_r($row);exit;
			$paging = $this->pagination_center->paginating($row['page'], $row['num_rows'], $row['per_page'], $row['page_link_limit']);//$page_now = 1, $row_total = 1, $per_page = 20, $page_limit = 20
			$i = $row['page_start'];


			$arr_data['num_rows'] = $row['num_rows'];
			$arr_data['paging'] = $paging;
			$arr_data['row'] = $row['data'];
			$arr_data['i'] = $i;
		}		
		
		$this->db->select('*');
		$this->db->from('coop_type_evidence');
		$row = $this->db->get()->result_array();
		$arr_data['type_evidence'] = $row;
		
		$this->db->select('*');
		$this->db->from('coop_department');
		$row = $this->db->get()->result_array();
		$arr_data['department'] = $row;

		$users = $this->db->select("*")->from("coop_user")->where("user_status = 1")->get()->result_array();
		$arr_data['users'] = $users;

		$this->libraries->template('facility/take_facility',$arr_data);
	}
	
	function get_department() {
		$this->db->select('*');
		$this->db->from('coop_personnel');
		$this->db->where("department_id = '".$_POST['id']."'");
		$row = $this->db->get()->result_array();
		$result = '<select id="receiver_id" name="receiver_id" class="form-control">
							<option value="">เลือกผู้เบิก</option>';
		foreach($row as $key => $value){
			$result .= '<option value="'.$value["personnel_id"].'"'.($value["personnel_id"] == $_POST["select_id"] ? ' selected' : '').'>'.$value["personnel_name"].'</option>';
		}
		$result .= '</select>';
		echo $result;
		exit;
	}

	function get_store(){
		$result = '';
		if($_POST["keyword"] != "") {
			$this->db->select('coop_facility_store.*, coop_facility_status.facility_status_name');
			$this->db->from('coop_facility_store');
			$this->db->join('coop_facility_status', 'coop_facility_store.facility_status_id = coop_facility_status.facility_status_id', 'left');
			$this->db->where("coop_facility_store.store_status = '0' AND (coop_facility_store.store_code LIKE '%{$_POST["keyword"]}%' OR coop_facility_store.store_name LIKE '%{$_POST["keyword"]}%')");
			$row = $this->db->get()->result_array();
			$i=1;
			foreach($row as $key => $value){
				$result .= "<tr class='tr_choose_store' id='tr_choose_id_".$value['store_id']."' store_id='".$value['store_id']."'>";
					$result .= "<td>".($value['facility_status_id'] == 1 ? "<input type='checkbox' id='store_chk_".$value['store_id']."' store_id='".$value['store_id']."' store_code='".$value['store_code']."' store_name='".$value['store_name']."' store_price='".$value['store_price']."' store_price_label='".number_format($value['store_price'],2)."' class='store_chk'>" : "")."</td>";
					//$result .= "<td>".$i++."</td>";
					$result .= "<td>".$value['store_code']."</td>";
					$result .= "<td>".$value['store_name']."</td>";
					$result .= "<td>".number_format($value['store_price'],2)."</td>";
					$result .= "<td>".$value['facility_status_name']."</td>";
				$result .= "</tr>";
			}
		}
		echo $result;
		exit;
	}
	
	function chk_facility_status() {
		$is_error = false;
		$err_msg = "";

		if(!empty($_POST["store_ids"])) {
			foreach($_POST["store_ids"] as $key => $store_id) {
				$this->db->select('coop_facility_store.facility_status_id, coop_facility_status.facility_status_name');
				$this->db->from('coop_facility_store');
				$this->db->join('coop_facility_status', 'coop_facility_store.facility_status_id = coop_facility_status.facility_status_id', 'left');
				$this->db->where("coop_facility_store.store_id = '{$store_id}'");
				$row = $this->db->get()->row_array();
				if($row["facility_status_id"] != 1) {
					$is_error = true;
					$err_msg = $row["facility_status_name"];
					break;
				}
			}
		}

		if($is_error) {
			echo json_encode([
				"status" => "FALSE",
				"error" => $err_msg
			]);
		}
		else {
			echo json_encode([
				"status" => "TRUE",
				"error" => ""
			]);
		}
	}

	function take_facility_save(){
		//echo"<pre>";print_r($_POST);exit;
		$data_insert = array();
		$budget_year = date("Y") + 543;
		$id_edit = @$_POST["facility_take_id"] ;
		
		$this->db->select(array('MAX(receive_run) AS last_run'));
		$this->db->from('coop_facility_take');
		$this->db->where("budget_year = '{$budget_year}'");
		$rs = $this->db->get()->result_array();
		$row = @$rs[0]; 
		
		$run_now = 0;
		if(empty($id_edit)){
			$run_now = $row['last_run']+1;	
			$receive_no = $budget_year.'-'.sprintf("%04d",$run_now);

			$this->db->select(array('MAX(voucher_no) AS last_run'));
			$this->db->from('coop_facility_take');
			$this->db->where("voucher_no LIKE '".$budget_year.date("m")."%'");
			$_row = $this->db->get()->row_array();
			$_run_now = substr($_row['last_run'], 6) + 1;
			$voucher_no = $budget_year.date("m").sprintf("%03d",$_run_now);
		}
		
		$data_insert['receive_no'] = $receive_no;
		$data_insert['receive_run'] = $run_now;
		$data_insert['receive_date'] = $this->center_function->ConvertToSQLDate($_POST['receive_date']);
		$data_insert['budget_year'] = $budget_year;
		$data_insert['voucher_no'] = $voucher_no;
		$data_insert['type_evidence_id'] = $_POST['type_evidence_id'];
		$data_insert['sign_date'] = $this->center_function->ConvertToSQLDate($_POST['sign_date']);
		$data_insert['department_id'] = $_POST['department_id'];
		// $data_insert['receive_name'] = $_POST['receive_name'];
		$data_insert['receiver_id'] = $_POST['receiver_id'];
		$this->db->insert('coop_facility_take', $data_insert);
		
		$facility_take_id = $this->db->insert_id();
		
		foreach($_POST['store_id'] as $key => $value){
			$data_insert = array();
			$data_insert['facility_take_id'] = $facility_take_id;
			$data_insert['store_id'] = $value;
			$this->db->insert('coop_facility_take_detail', $data_insert);
			
			$data_insert = array();
			$data_insert['store_status'] = '1';
			$this->db->where('store_id', $value);
			$this->db->update('coop_facility_store', $data_insert);
		}
		$this->center_function->toast('บันทึกข้อมูลเรียบร้อยแล้ว');
		echo "<script> document.location.href='".base_url(PROJECTPATH.'/facility/take_facility')."' </script>";
	}
	
	function get_search_take(){
		$where = "
		 	(receive_no LIKE '%".$this->input->post('search_text')."%'
		 	OR department_name LIKE '%".$this->input->post('search_text')."%')
		";
		
		$this->db->select('coop_facility_take.*,coop_department.department_name,coop_personnel.personnel_name as receive_name');
		$this->db->from('coop_facility_take');
		$this->db->join('coop_department', 'coop_facility_take.department_id = coop_department.department_id', 'left');
		$this->db->join('coop_personnel', 'coop_facility_take.receiver_id = coop_personnel.personnel_id', 'left');
		$this->db->where($where);
		$this->db->order_by('facility_take_id DESC');
		$row = $this->db->get()->result_array();
		$arr_data['data'] = @$row;
		$arr_data['form_target'] = $this->input->post('form_target');
		//echo"<pre>";print_r($arr_data['data']);exit;
		$this->load->view('facility/get_search_take',$arr_data);
	}

	public function print_coop_data() {
		$arr_data = array();
		$store = array();
		if(!empty($_GET["id"])) {
			$store = $this->db->select("store_id, store_name, store_code, budget_year")->from("coop_facility_store")->where("store_id = '".$_GET["id"]."'")->get()->result_array();
		} elseif(!empty($_POST["store_id"])) {
			$store = $this->db->select("store_id, store_name, store_code, budget_year")->from("coop_facility_store")->where("store_id in (".implode(',',$_POST["store_id"]).")")->get()->result_array();
		}

		$datas = array();
		$page = 0;
		$first_page_size = 39;
		$page_size = 39;
		foreach($store as $index => $data) {
			if($index < $first_page_size) {
				$page = 1;
			} else {
				$page = ceil((($index + 1)-$first_page_size) / $page_size) + 1;
			}
			$datas[$page][] = $data;
		}
		$arr_data["datas"] = $datas;

		$this->preview_libraries->template_preview('facility/supplies_label_preview',$arr_data);
	}

	public function qrcode_generate() {
		$this->load->library('ciqrcode');
		header("Content-Type: image/png");
		$params['data'] = $_GET["text"];
		$this->ciqrcode->generate($params);
	}

	public function supplies_info() {
		$arr_data = array();
		$id = $_GET["id"];
		$store = $this->db->select("t1.store_code, t1.store_name, t1.budget_year, t1.receive_date, t1.store_price, t1.store_pic, t2.seller_name, t2.address, t2.phone_number")
							->from("coop_facility_store as t1")
							->join("coop_seller as t2", "t1.seller_id = t2.seller_id")
							->where("t1.store_id = '".$id."'")
							->get()->row();
		$arr_data["data"] = $store;
		$this->preview_libraries->template_preview_non_auth('facility/supplies_info',$arr_data);
	}

	function get_facility_store_data(){
		$this->db->select('*');
		$this->db->from('coop_facility_store');
		$this->db->where("store_id = '{$_POST["id"]}'");
		$row = $this->db->get()->row_array();
		echo json_encode([
			"status" => "TRUE",
			"data" => $row
		]);
		exit;
	}

	function repair_facility(){
		$arr_data = array();
		$id = @$_GET['id'];
		if($id!=''){
			$this->db->select('coop_facility_repair.*, coop_facility_store.store_code, coop_facility_store.store_name');
			$this->db->from('coop_facility_repair');
			$this->db->join('coop_facility_store', 'coop_facility_repair.store_id = coop_facility_store.store_id', 'inner');
			$this->db->where("coop_facility_repair.repair_id = '".$id."'");
			$rs = $this->db->get()->result_array();
			$row= @$rs[0];
			$arr_data['data'] = @$row;
		}else{
			$arr_data['data'] = array();

			$x=0;
			$join_arr = array();
			$join_arr[$x]['table'] = 'coop_facility_store';
			$join_arr[$x]['condition'] = 'coop_facility_repair.store_id = coop_facility_store.store_id';
			$join_arr[$x]['type'] = 'inner';

			$this->paginater_all->type(DB_TYPE);
			$this->paginater_all->select('coop_facility_repair.*, coop_facility_store.store_code, coop_facility_store.store_name');
			$this->paginater_all->main_table('coop_facility_repair');
			$this->paginater_all->where("");
			$this->paginater_all->page_now(@$_GET["page"]);
			$this->paginater_all->per_page(10);
			$this->paginater_all->page_link_limit(20);
			$this->paginater_all->order_by('repair_date DESC, repair_id DESC');
			$this->paginater_all->join_arr($join_arr);
			$row = $this->paginater_all->paginater_process();
			//echo"<pre>";print_r($row);exit;
			$paging = $this->pagination_center->paginating($row['page'], $row['num_rows'], $row['per_page'], $row['page_link_limit']);//$page_now = 1, $row_total = 1, $per_page = 20, $page_limit = 20
			$i = $row['page_start'];

			$arr_data['num_rows'] = $row['num_rows'];
			$arr_data['paging'] = $paging;
			$arr_data['row'] = $row['data'];
			$arr_data['i'] = $i;
		}		

		$this->libraries->template('facility/repair_facility',$arr_data);
	}

	function get_facility_store(){
		$this->db->select('*');
		$this->db->from('coop_facility_store');
		$this->db->where("store_code = '{$_POST["id"]}'");
		$row = $this->db->get()->row_array();
		if(!empty($row)) {
			$data = [
				"store_id" => $row["store_id"],
				"store_name" => $row["store_name"]
			];

			echo json_encode([
				"status" => "TRUE",
				"data" => $data
			]);
		}
		else {
			echo json_encode([
				"status" => "FALSE"
			]);
		}
		exit;
	}

	function get_facility_store_list(){
		$where = "
		 	(store_code LIKE '%".$this->input->post('search_text')."%'
		 	OR facility_main_code LIKE '%".$this->input->post('search_text')."%' 
		 	OR store_name LIKE '%".$this->input->post('search_text')."%')
		";
		$this->db->select(array('store_id','store_code','facility_main_code','store_name','store_price','department_name'));
		$this->db->from('coop_facility_store');
		$this->db->where($where);
		$this->db->order_by('store_id DESC');
		$row = $this->db->get()->result_array();
		$arr_data['data'] = @$row;
		$this->load->view('facility/get_facility_store_list',$arr_data);
	}

	function repair_facility_save(){
		$year = date("Y") + 543;

		$this->db->select(array('MAX(repair_code) AS last_run'));
		$this->db->from('coop_facility_repair');
		$this->db->where("repair_code LIKE '{$year}-%'");
		$rs = $this->db->get()->result_array();
		$row = @$rs[0]; 

		$repair_code = $year."-0001";
		if(!empty($row["last_run"])){
			$run_now = substr($row['last_run'], 5, 4)+1;
			$repair_code = $year.'-'.sprintf("%04d",$run_now);
		}

		$data_insert = array();
		$data_insert['repair_code'] = $repair_code;
		$data_insert['store_id'] = $_POST['store_id'];
		$data_insert['repair_date'] = $this->center_function->ConvertToSQLDate($_POST['repair_date']);
		$data_insert['problem'] = $_POST['problem'];
		$data_insert['company'] = $_POST['company'];
		$data_insert['company_address'] = $_POST['company_address'];
		$data_insert['contact_tel'] = $_POST['contact_tel'];
		$data_insert['repair_status'] = 0;
		$data_insert['result_status'] = 0;
		$this->db->insert('coop_facility_repair', $data_insert);

		$data_insert = array();
		$data_insert['facility_status_id'] = 2;
		$this->db->where('store_id', $_POST['store_id']);
		$this->db->update('coop_facility_store', $data_insert);

		$this->center_function->toast('บันทึกข้อมูลเรียบร้อยแล้ว');
		echo "<script> document.location.href='".base_url(PROJECTPATH.'/facility/repair_facility')."' </script>";
	}

	function get_facility_repair() {
		$this->db->select('coop_facility_repair.*, coop_facility_store.store_code, coop_facility_store.store_name');
		$this->db->from('coop_facility_repair');
		$this->db->join('coop_facility_store', 'coop_facility_repair.store_id = coop_facility_store.store_id', 'inner');
		$this->db->where("repair_id = '{$_POST["id"]}'");
		$row = $this->db->get()->row_array();
		if(!empty($row)) {
			$data = [
				"repair_id" => $row["repair_id"],
				"repair_code" => $row["repair_code"],
				"store_id" => $row["store_id"],
				"store_code" => $row["store_code"],
				"repair_date" => $this->center_function->mydate2date(empty($row["repair_date"]) || $row["repair_date"] == "0000-00-00 00:00:00" ? date("Y-m-d") : date("Y-m-d", strtotime($row["repair_date"]))),
				"store_name" => $row["store_name"],
				"company" => $row["company"],
				"return_date" => $this->center_function->mydate2date(empty($row["return_date"]) || $row["return_date"] == "0000-00-00 00:00:00" ? date("Y-m-d") : date("Y-m-d", strtotime($row["return_date"]))),
				"result_status" => $row["result_status"],
				"repair_price" => empty($row["repair_price"]) ? "" : $row["repair_price"],
				"remark" => $row["remark"]
			];

			echo json_encode([
				"status" => "TRUE",
				"data" => $data
			]);
		}
		else {
			echo json_encode([
				"status" => "FALSE"
			]);
		}
		exit;
	}

	function repair_return_save() {
		$repair_status = $_POST['result_status'] == 0 ? 2 : 1;

		$data_insert = array();
		$data_insert['return_date'] = $this->center_function->ConvertToSQLDate($_POST['return_date']);
		$data_insert['repair_status'] = $repair_status;
		$data_insert['result_status'] = $_POST['result_status'];
		$data_insert['repair_price'] = $_POST['repair_price'];
		$data_insert['remark'] = $_POST['remark'];
		$this->db->where('repair_id', $_POST['repair_id']);
		$this->db->update('coop_facility_repair', $data_insert);

		$data_insert = array();
		$data_insert['facility_status_id'] = $_POST['result_status'] == 0 ? 1 : 4;
		$this->db->where('store_id', $_POST['store_id']);
		$this->db->update('coop_facility_store', $data_insert);

		$data = [
			"repair_id" => $_POST['repair_id'],
			"repair_status" => $repair_status
		];

		echo json_encode([
			"status" => "TRUE",
			"data" => $data
		]);
	}

	function get_search_repair(){
		$where = "
		 	(repair_code LIKE '%".$this->input->post('search_text')."%'
			OR store_code LIKE '%".$this->input->post('search_text')."%'
		 	OR store_name LIKE '%".$this->input->post('search_text')."%')
		";

		$this->db->select('coop_facility_repair.*, coop_facility_store.store_code, coop_facility_store.store_name');
		$this->db->from('coop_facility_repair');
		$this->db->join('coop_facility_store', 'coop_facility_repair.store_id = coop_facility_store.store_id', 'inner');
		$this->db->where($where);
		$this->db->order_by('repair_date DESC, repair_id DESC');
		$row = $this->db->get()->result_array();
		$arr_data['data'] = @$row;
		$arr_data['form_target'] = $this->input->post('form_target');
		//echo"<pre>";print_r($arr_data['data']);exit;
		$this->load->view('facility/get_search_repair',$arr_data);
	}

	function get_transfer_form(){
		$this->db->select('coop_facility_take.*, coop_department.department_name');
		$this->db->from('coop_facility_take');
		$this->db->join('coop_department', 'coop_facility_take.department_id = coop_department.department_id', 'left');
		$this->db->where("facility_take_id = '{$_POST["id"]}'");
		$row = $this->db->get()->row_array();
		if(!empty($row)) {
			$arr_data['data'] = $row;

			$this->db->select('*');
			$this->db->from('coop_department');
			$rs = $this->db->get()->result_array();
			$arr_data['department'] = $rs;

			$html = $this->load->view('facility/get_transfer_form',$arr_data,true);

			echo json_encode([
				"status" => "TRUE",
				"html" => $html
			]);
		}
		else {
			echo json_encode([
				"status" => "FALSE"
			]);
		}

		exit;
	}

	function transfer_save(){
		$data_insert = array();
		$data_insert['department_id'] = $_POST['department_id'];
		$data_insert['receiver_id'] = $_POST['receiver_id'];
		$data_insert['sign_date'] = date('Y-m-d');
		$this->db->where('facility_take_id', $_POST['facility_take_id']);
		$this->db->update('coop_facility_take', $data_insert);

		$this->db->select('coop_facility_take.*, coop_department.department_name, coop_personnel.personnel_name as receive_name');
		$this->db->from('coop_facility_take');
		$this->db->join('coop_department', 'coop_facility_take.department_id = coop_department.department_id', 'left');
		$this->db->join('coop_personnel', 'coop_facility_take.receiver_id = coop_personnel.personnel_id', 'left');
		$this->db->where("facility_take_id = '{$_POST["facility_take_id"]}'");
		$row = $this->db->get()->row_array();

		$data = [
			"facility_take_id" => $_POST['facility_take_id'],
			"department_name" => $row['department_name'],
			"receive_name" => $row['receive_name'],
			"sign_date" => $this->center_function->ConvertToThaiDate($row['sign_date'],true,false)
		];

		echo json_encode([
			"status" => "TRUE",
			"data" => $data
		]);
	}

	function cal_depreciation() {
		$this->db->select("*");
		$this->db->from("coop_budget_year");
		$this->db->where("date_start = DAY(NOW()) AND month_start = MONTH(NOW())");
		$row = $this->db->get()->row_array();
		if(!empty($row)) {
			echo "Start cal depreciation...<br>";

			$sql = "INSERT INTO coop_facility_depreciation (store_id, depreciation_year, depreciation_price)
						SELECT coop_facility_store.store_id, '2562' AS depreciation_year, coop_facility_store.store_price - ((coop_facility_store.store_price * coop_depreciation.depreciation_percent / 100) * (2562 - coop_facility_store.budget_year)) AS depreciation_price
						FROM coop_facility_store
							INNER JOIN coop_facility_main ON coop_facility_store.facility_main_code = coop_facility_main.facility_main_code
							INNER JOIN coop_depreciation ON coop_facility_main.depreciation_id = coop_depreciation.depreciation_id
						WHERE coop_facility_store.facility_status_id IN (1, 2)";
			$rs = $this->db->query($sql);
			echo "Complete.";
		}
		exit;
	}
}
