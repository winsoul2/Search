<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Setting_facility_data extends CI_Controller {
	function __construct()
	{
		parent::__construct();
	}
	
	function budget_year(){
		if($_POST){
			$data_insert = array();
			$data_insert['month_start'] = $_POST['month_start'];
			$data_insert['date_start'] = $_POST['date_start'];
			$data_insert['month_end'] = $_POST['month_end'];
			$data_insert['date_end'] = $_POST['date_end'];
			$this->db->where('year_id', $_POST['year_id']);
			$this->db->update('coop_budget_year', $data_insert);
			$this->center_function->toast('บันทึกข้อมูลเรียบร้อยแล้ว');
			echo"<script> document.location.href='".PROJECTPATH."/setting_facility_data/budget_year'; </script>";
		}
		$arr_data = array();
		
		$this->db->select('*');
		$this->db->from("coop_budget_year");
		$this->db->limit(1);
		$row = $this->db->get()->result_array();
		$arr_data['data'] = @$row[0];
		
		$arr_data['date_start_limit'] = date('t',strtotime('2018-'.$arr_data['data']['month_start'].'-01'));
		$arr_data['date_end_limit'] = date('t',strtotime('2018-'.$arr_data['data']['month_end'].'-01'));
		
		$month_arr = array('1'=>'มกราคม','2'=>'กุมภาพันธ์','3'=>'มีนาคม','4'=>'เมษายน','5'=>'พฤษภาคม','6'=>'มิถุนายน','7'=>'กรกฎาคม','8'=>'สิงหาคม','9'=>'กันยายน','10'=>'ตุลาคม','11'=>'พฤศจิกายน','12'=>'ธันวาคม');
		$arr_data['month_arr'] = $month_arr;
		
		$this->libraries->template('setting_facility_data/budget_year',$arr_data);
	}
	
	function change_month(){
		$date_limit = date('t',strtotime('2018-'.sprintf('%02d',$_POST['month']).'-01'));
		//echo $_POST['month'];exit;
		$select = "<select name='date_".$_POST['type']."' class='form-control'>";
		for($i=1;$i<=$date_limit;$i++){
			$select .= "<option value='".$i."'>".$i."</option>";
		}
		$select .= "</select>";
		echo $select;
		exit;
	}
	
	public function facility_group(){
		$arr_data = array();
		
		$x=0;
		$join_arr = array();
		$join_arr[$x]['table'] = 'coop_facility_group as t2';
		$join_arr[$x]['condition'] = 'coop_facility_group.facility_group_parent_id = t2.facility_group_id';
		$join_arr[$x]['type'] = 'left';
		$x++;
		$join_arr[$x]['table'] = 'coop_facility_group as t3';
		$join_arr[$x]['condition'] = 't2.facility_group_parent_id = t3.facility_group_id';
		$join_arr[$x]['type'] = 'left';
		
		$this->paginater_all->type(DB_TYPE);
		$this->paginater_all->select('coop_facility_group.*,
									t2.facility_group_name as t2_name,
									t2.facility_group_code as t2_code,
									t3.facility_group_name as t3_name,
									t3.facility_group_code as t3_code');
		$this->paginater_all->main_table('coop_facility_group');
		$this->paginater_all->where("coop_facility_group.facility_group_type = '3'");
		$this->paginater_all->page_now(@$_GET["page"]);
		$this->paginater_all->per_page(20);
		$this->paginater_all->page_link_limit(20);
		$this->paginater_all->order_by('facility_group_code ASC');
		$this->paginater_all->join_arr($join_arr);
		$row = $this->paginater_all->paginater_process();
		//echo $this->db->last_query();exit;
		//echo"<pre>";print_r($row);exit;
		$paging = $this->pagination_center->paginating($row['page'], $row['num_rows'], $row['per_page'], $row['page_link_limit'], $_GET);//$page_now = 1, $row_total = 1, $per_page = 20, $page_limit = 20
		
		$i = $row['page_start'];

		$arr_data['num_rows'] = $row['num_rows'];
		$arr_data['paging'] = $paging;
		$arr_data['rs'] = $row['data'];
		$arr_data['i'] = $i;
		
		$this->db->select(array('*'));
		$this->db->from('coop_facility_group');
		$this->db->where("facility_group_type  = '1' ");
		$rs_group = $this->db->get()->result_array();
		$arr_data['rs_group'] = @$rs_group;
		//echo '<pre>'; print_r($rs_group); echo '</pre>'; exit;
		
		//$this->db->select(array('*'));
		$this->db->select('t1.*, t2.facility_group_name as parent_name, t2.facility_group_code as parent_code');
		$this->db->from('coop_facility_group as t1');
		$this->db->join('coop_facility_group as t2', 't1.facility_group_parent_id = t2.facility_group_id', 'left');		
		$this->db->where("t1.facility_group_type  = '2' ");
		$rs_group2 = $this->db->get()->result_array();
		$arr_data['rs_group2'] = @$rs_group2;
		//print_r($this->db->last_query());exit;

		$this->libraries->template('setting_facility_data/facility_group',$arr_data);
	}
	
	public function facility_group_save(){
		//echo"<pre>";print_r($_POST);exit;
		$data_insert = array();
		$data_insert['facility_group_full_code'] = '';
		if(@$_POST['parent_group']!=''){
			$facility_group_parent_id = @$_POST['parent_group'];
			$data_insert['facility_group_code']	= sprintf('%03d',@$_POST["facility_group_code"]);	
			$data_insert['facility_group_full_code'] = @$_POST['main_group_code'].@$_POST['parent_group_code']."-".$data_insert['facility_group_code'];
		}else if(@$_POST['main_group']!=''){
			$facility_group_parent_id = @$_POST['main_group'];
			$data_insert['facility_group_code']	= sprintf('%02d',@$_POST["facility_group_code"]);	
			$data_insert['facility_group_full_code'] = @$_POST['main_group_code'].$data_insert['facility_group_code'];
		}else{
			$facility_group_parent_id = '';
			$data_insert['facility_group_code']	= sprintf('%02d',@$_POST["facility_group_code"]);	
			$data_insert['facility_group_full_code'] = $data_insert['facility_group_code'];
		}
		
			
		$data_insert['facility_group_parent_id']  = @$facility_group_parent_id;
		$data_insert['facility_group_name']	= @$_POST["facility_group_name"];

		$id_edit = @$_POST["facility_group_id"] ;

		$table = "coop_facility_group";

		if ($id_edit == '') {
			// add
			$data_insert['facility_group_type']	= @$_POST["facility_group_type"];
			
			$this->db->insert($table, $data_insert);
			$this->center_function->toast("บันทึกข้อมูลเรียบร้อยแล้ว");
			// add
		}else{
			// edit
			$this->db->where('facility_group_id', $id_edit);
			$this->db->update($table, $data_insert);
			$this->center_function->toast("แก้ไขข้อมูลเรียบร้อยแล้ว");	
			// edit
		}

		echo"<script> document.location.href='".PROJECTPATH."/setting_facility_data/facility_group' </script>"; 
	}
	
	public function get_group_child(){		
		$this->db->select(array('*'));
		$this->db->from('coop_facility_group');
		$this->db->where("facility_group_parent_id = '".$_POST['group_id']."'");
		$rs = $this->db->get()->result_array();
		$output = '';
		$output .= '  
					<select class="form-control" id="parent_group" name="parent_group" onchange="change_parent_group()">
						<option value="">เลือกกลุ่มย่อย</option>
					';
		if(!empty($rs)){  
			$i= 1; 
			foreach($rs as $key => $row){				
			   $output .= ' <option value="'.@$row['facility_group_id'].'" group_code="'.@$row['facility_group_code'].'">'.@$row['facility_group_code'].'-'.@$row['facility_group_name'].'</option>';									
		    }
			 
		} 
		$output .= '</select>';
		echo $output; 
		exit;
	}
	public function get_group_parent(){		
		$this->db->select(array('*'));
		$this->db->from('coop_facility_group');
		$this->db->where("facility_group_id = '".$_POST['id']."'");
		$rs = $this->db->get()->result_array();
		$output = '';
		if(!empty($rs)){  
			foreach($rs as $key => $row){				
			   $output .= @$row['facility_group_parent_id'];								
		    }
			 
		} 
		echo $output; 
		exit;
	}
	
	public function check_delete_facility_group(){				
		$this->db->select(array('*'));
		$this->db->from('coop_facility_group');
		$this->db->where("facility_group_parent_id = '".@$_POST['id']."'");
		$rs = $this->db->get()->result_array();
		$row = @$rs[0];
		if($row['facility_group_id']!=''){
			echo "error";
		}else{
			echo "success";
		}
		exit;		
	}
	
	public function delete_facility_group(){	
		if(@$_POST['delete_action']=='delete_action'){
			$this->db->where('facility_group_id', @$_POST['id']);
			$this->db->delete('coop_facility_group');
		}
	}
	
	public function department()
	{
		$arr_data = array();
		$id = @$_GET['id'];
		if(!empty($id)){
			$this->db->select(array('*'));
			$this->db->from('coop_department');
			$this->db->where("department_id = '{$id}'");
			$rs = $this->db->get()->result_array();
			$arr_data['row'] = @$rs[0]; 	
		}else{	
			$x=0;
			$join_arr = array();
			
			$this->paginater_all->type(DB_TYPE);
			$this->paginater_all->select('*');
			$this->paginater_all->main_table('coop_department');
			$this->paginater_all->where("");
			$this->paginater_all->page_now(@$_GET["page"]);
			$this->paginater_all->per_page(20);
			$this->paginater_all->page_link_limit(20);
			$this->paginater_all->order_by('department_id DESC');
			$this->paginater_all->join_arr($join_arr);
			$row = $this->paginater_all->paginater_process();
			//echo $this->db->last_query();exit;
			//echo"<pre>";print_r($row);exit;
			$paging = $this->pagination_center->paginating($row['page'], $row['num_rows'], $row['per_page'], $row['page_link_limit'], $_GET);//$page_now = 1, $row_total = 1, $per_page = 20, $page_limit = 20
			
			$i = $row['page_start'];

			$arr_data['num_rows'] = $row['num_rows'];
			$arr_data['paging'] = $paging;
			$arr_data['rs'] = $row['data'];
			$arr_data['i'] = $i;
		}
		$this->libraries->template('setting_facility_data/department',$arr_data);
	}
	
	public function department_save()
	{
		$data_insert = array();			
		$data_insert['department_name']    = @$_POST["department_name"];
		$data_insert['updatetime']    = date('Y-m-d H:i:s');

		$type_add = @$_POST["type_add"] ;
		$id_edit = @$_POST["id"] ;
			

		$table = "coop_department";

		if ($type_add == 'add') {	
		// add		
			$data_insert['createdatetime'] = date('Y-m-d H:i:s');
			$this->db->insert($table, $data_insert);
			$this->center_function->toast("บันทึกข้อมูลเรียบร้อยแล้ว");

		// add
		}else{
		// edit
			$this->db->where('department_id', $id_edit);
			$this->db->update($table, $data_insert);	
			$this->center_function->toast("แก้ไขข้อมูลเรียบร้อยแล้ว");

		// edit
		}
		
		echo"<script> document.location.href='".PROJECTPATH."/setting_facility_data/department' </script>"; 

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

	public function personnel()
	{
		$arr_data = array();
		$id = @$_GET['id'];
		if(!empty($id)){
			$this->db->select(array('*'));
			$this->db->from('coop_personnel');
			$this->db->where("personnel_id = '{$id}'");
			$rs = $this->db->get()->result_array();
			$arr_data['row'] = @$rs[0]; 	
		}else{	
			$x=0;
			$join_arr = array();
			$join_arr[$x]['table'] = 'coop_department';
			$join_arr[$x]['condition'] = 'coop_department.department_id = coop_personnel.department_id';
			$join_arr[$x]['type'] = 'left';
			
			$this->paginater_all->type(DB_TYPE);
			$this->paginater_all->select('coop_personnel.*,coop_department.department_name');
			$this->paginater_all->main_table('coop_personnel');
			$this->paginater_all->where("");
			$this->paginater_all->page_now(@$_GET["page"]);
			$this->paginater_all->per_page(20);
			$this->paginater_all->page_link_limit(20);
			$this->paginater_all->order_by('coop_personnel.personnel_id DESC');
			$this->paginater_all->join_arr($join_arr);
			$row = $this->paginater_all->paginater_process();
			//echo $this->db->last_query();exit;
			//echo"<pre>";print_r($row);exit;
			$paging = $this->pagination_center->paginating($row['page'], $row['num_rows'], $row['per_page'], $row['page_link_limit'], $_GET);//$page_now = 1, $row_total = 1, $per_page = 20, $page_limit = 20
			
			$i = $row['page_start'];

			$arr_data['num_rows'] = $row['num_rows'];
			$arr_data['paging'] = $paging;
			$arr_data['rs'] = $row['data'];
			$arr_data['i'] = $i;
		}
		
		$this->db->select(array('*'));
		$this->db->from('coop_department');
		$rs_department = $this->db->get()->result_array();
		$arr_data['rs_department'] = @$rs_department;
		
		$this->libraries->template('setting_facility_data/personnel',$arr_data);
	}
	
	public function personnel_save()
	{
		$data_insert = array();			
		$data_insert['personnel_name']    = @$_POST["personnel_name"];
		$data_insert['department_id']    = @$_POST["department_id"];
		$data_insert['updatetime']    = date('Y-m-d H:i:s');

		$type_add = @$_POST["type_add"] ;
		$id_edit = @$_POST["id"] ;
			

		$table = "coop_personnel";

		if ($type_add == 'add') {	
		// add		
			$data_insert['createdatetime'] = date('Y-m-d H:i:s');
			$this->db->insert($table, $data_insert);
			$this->center_function->toast("บันทึกข้อมูลเรียบร้อยแล้ว");

		// add
		}else{
		// edit
			$this->db->where('personnel_id', $id_edit);
			$this->db->update($table, $data_insert);	
			$this->center_function->toast("แก้ไขข้อมูลเรียบร้อยแล้ว");

		// edit
		}
		
		echo"<script> document.location.href='".PROJECTPATH."/setting_facility_data/personnel' </script>"; 
	}
	
	public function usage_reason()
	{
		$arr_data = array();
		$id = @$_GET['id'];
		if(!empty($id)){
			$this->db->select(array('*'));
			$this->db->from('coop_usage_reason');
			$this->db->where("reason_id = '{$id}'");
			$rs = $this->db->get()->result_array();
			$arr_data['row'] = @$rs[0]; 	
		}else{	
			
			$x=0;
			$join_arr = array();
			
			$this->paginater_all->type(DB_TYPE);
			$this->paginater_all->select('*');
			$this->paginater_all->main_table('coop_usage_reason');
			$this->paginater_all->where("");
			$this->paginater_all->page_now(@$_GET["page"]);
			$this->paginater_all->per_page(20);
			$this->paginater_all->page_link_limit(20);
			$this->paginater_all->order_by('reason_id DESC');
			$this->paginater_all->join_arr($join_arr);
			$row = $this->paginater_all->paginater_process();
			//echo $this->db->last_query();exit;
			//echo"<pre>";print_r($row);exit;
			$paging = $this->pagination_center->paginating($row['page'], $row['num_rows'], $row['per_page'], $row['page_link_limit'], $_GET);//$page_now = 1, $row_total = 1, $per_page = 20, $page_limit = 20
			
			$i = $row['page_start'];

			$arr_data['num_rows'] = $row['num_rows'];
			$arr_data['paging'] = $paging;
			$arr_data['rs'] = $row['data'];
			$arr_data['i'] = $i;
		}
		
		$this->libraries->template('setting_facility_data/usage_reason',$arr_data);
	}
	
	public function usage_reason_save()
	{
		$data_insert = array();			
		$data_insert['reason_name']    = @$_POST["reason_name"];
		$data_insert['updatetime']    = date('Y-m-d H:i:s');

		$type_add = @$_POST["type_add"] ;
		$id_edit = @$_POST["id"] ;
			

		$table = "coop_usage_reason";

		if ($type_add == 'add') {	
		// add		
			$data_insert['createdatetime'] = date('Y-m-d H:i:s');
			$this->db->insert($table, $data_insert);
			$this->center_function->toast("บันทึกข้อมูลเรียบร้อยแล้ว");

		// add
		}else{
		// edit
			$this->db->where('reason_id', $id_edit);
			$this->db->update($table, $data_insert);	
			$this->center_function->toast("แก้ไขข้อมูลเรียบร้อยแล้ว");

		// edit
		}
		
		echo"<script> document.location.href='".PROJECTPATH."/setting_facility_data/usage_reason' </script>"; 
	}
	
	public function means_purchase()
	{
		$arr_data = array();
		$id = @$_GET['id'];
		if(!empty($id)){
			$this->db->select(array('*'));
			$this->db->from('coop_means_purchase');
			$this->db->where("means_id = '{$id}'");
			$rs = $this->db->get()->result_array();
			$arr_data['row'] = @$rs[0]; 	
		}else{	
			$x=0;
			$join_arr = array();
			
			$this->paginater_all->type(DB_TYPE);
			$this->paginater_all->select('*');
			$this->paginater_all->main_table('coop_means_purchase');
			$this->paginater_all->where("");
			$this->paginater_all->page_now(@$_GET["page"]);
			$this->paginater_all->per_page(20);
			$this->paginater_all->page_link_limit(20);
			$this->paginater_all->order_by('means_id DESC');
			$this->paginater_all->join_arr($join_arr);
			$row = $this->paginater_all->paginater_process();
			//echo $this->db->last_query();exit;
			//echo"<pre>";print_r($row);exit;
			$paging = $this->pagination_center->paginating($row['page'], $row['num_rows'], $row['per_page'], $row['page_link_limit'], $_GET);//$page_now = 1, $row_total = 1, $per_page = 20, $page_limit = 20
			
			$i = $row['page_start'];

			$arr_data['num_rows'] = $row['num_rows'];
			$arr_data['paging'] = $paging;
			$arr_data['rs'] = $row['data'];
			$arr_data['i'] = $i;
		}
		
		$this->libraries->template('setting_facility_data/means_purchase',$arr_data);
	}
	
	public function means_purchase_save()
	{
		$data_insert = array();			
		$data_insert['means_name']    = @$_POST["means_name"];
		$data_insert['updatetime']    = date('Y-m-d H:i:s');

		$type_add = @$_POST["type_add"] ;
		$id_edit = @$_POST["id"] ;
			

		$table = "coop_means_purchase";

		if ($type_add == 'add') {	
		// add		
			$data_insert['createdatetime'] = date('Y-m-d H:i:s');
			$this->db->insert($table, $data_insert);
			$this->center_function->toast("บันทึกข้อมูลเรียบร้อยแล้ว");

		// add
		}else{
		// edit
			$this->db->where('means_id', $id_edit);
			$this->db->update($table, $data_insert);	
			$this->center_function->toast("แก้ไขข้อมูลเรียบร้อยแล้ว");

		// edit
		}
		
		echo"<script> document.location.href='".PROJECTPATH."/setting_facility_data/means_purchase' </script>"; 
	}
	
	public function type_money()
	{
		$arr_data = array();
		$id = @$_GET['id'];
		if(!empty($id)){
			$this->db->select(array('*'));
			$this->db->from('coop_type_money');
			$this->db->where("type_money_id = '{$id}'");
			$rs = $this->db->get()->result_array();
			$arr_data['row'] = @$rs[0]; 	
		}else{	
			$x=0;
			$join_arr = array();
			
			$this->paginater_all->type(DB_TYPE);
			$this->paginater_all->select('*');
			$this->paginater_all->main_table('coop_type_money');
			$this->paginater_all->where("");
			$this->paginater_all->page_now(@$_GET["page"]);
			$this->paginater_all->per_page(20);
			$this->paginater_all->page_link_limit(20);
			$this->paginater_all->order_by('type_money_id DESC');
			$this->paginater_all->join_arr($join_arr);
			$row = $this->paginater_all->paginater_process();
			//echo $this->db->last_query();exit;
			//echo"<pre>";print_r($row);exit;
			$paging = $this->pagination_center->paginating($row['page'], $row['num_rows'], $row['per_page'], $row['page_link_limit'], $_GET);//$page_now = 1, $row_total = 1, $per_page = 20, $page_limit = 20
			
			$i = $row['page_start'];

			$arr_data['num_rows'] = $row['num_rows'];
			$arr_data['paging'] = $paging;
			$arr_data['rs'] = $row['data'];
			$arr_data['i'] = $i;
		}
		
		$this->libraries->template('setting_facility_data/type_money',$arr_data);
	}
	
	public function type_money_save()
	{
		$data_insert = array();			
		$data_insert['type_money_name']    = @$_POST["type_money_name"];
		$data_insert['updatetime']    = date('Y-m-d H:i:s');

		$type_add = @$_POST["type_add"] ;
		$id_edit = @$_POST["id"] ;
			

		$table = "coop_type_money";

		if ($type_add == 'add') {	
		// add		
			$data_insert['createdatetime'] = date('Y-m-d H:i:s');
			$this->db->insert($table, $data_insert);
			$this->center_function->toast("บันทึกข้อมูลเรียบร้อยแล้ว");

		// add
		}else{
		// edit
			$this->db->where('type_money_id', $id_edit);
			$this->db->update($table, $data_insert);	
			$this->center_function->toast("แก้ไขข้อมูลเรียบร้อยแล้ว");

		// edit
		}
		
		echo"<script> document.location.href='".PROJECTPATH."/setting_facility_data/type_money' </script>"; 
	}
	
	public function seller()
	{
		$arr_data = array();
		$id = @$_GET['id'];
		if(!empty($id)){
			$this->db->select(array('*'));
			$this->db->from('coop_seller');
			$this->db->where("seller_id = '{$id}'");
			$rs = $this->db->get()->result_array();
			$arr_data['row'] = @$rs[0]; 	
		}else{	
			$x=0;
			$join_arr = array();
			
			$this->paginater_all->type(DB_TYPE);
			$this->paginater_all->select('*');
			$this->paginater_all->main_table('coop_seller');
			$this->paginater_all->where("");
			$this->paginater_all->page_now(@$_GET["page"]);
			$this->paginater_all->per_page(20);
			$this->paginater_all->page_link_limit(20);
			$this->paginater_all->order_by('seller_id DESC');
			$this->paginater_all->join_arr($join_arr);
			$row = $this->paginater_all->paginater_process();
			//echo $this->db->last_query();exit;
			//echo"<pre>";print_r($row);exit;
			$paging = $this->pagination_center->paginating($row['page'], $row['num_rows'], $row['per_page'], $row['page_link_limit'], $_GET);//$page_now = 1, $row_total = 1, $per_page = 20, $page_limit = 20
			
			$i = $row['page_start'];

			$arr_data['num_rows'] = $row['num_rows'];
			$arr_data['paging'] = $paging;
			$arr_data['rs'] = $row['data'];
			$arr_data['i'] = $i;
		}
		
		$this->libraries->template('setting_facility_data/seller',$arr_data);
	}
	
	public function seller_save()
	{
		$data_insert = array();			
		$data_insert['seller_name']    = @$_POST["seller_name"];
		$data_insert['updatetime']    = date('Y-m-d H:i:s');

		$type_add = @$_POST["type_add"] ;
		$id_edit = @$_POST["id"] ;
			

		$table = "coop_seller";

		if ($type_add == 'add') {	
		// add		
			$data_insert['createdatetime'] = date('Y-m-d H:i:s');
			$this->db->insert($table, $data_insert);
			$this->center_function->toast("บันทึกข้อมูลเรียบร้อยแล้ว");

		// add
		}else{
		// edit
			$this->db->where('seller_id', $id_edit);
			$this->db->update($table, $data_insert);	
			$this->center_function->toast("แก้ไขข้อมูลเรียบร้อยแล้ว");

		// edit
		}
		
		echo"<script> document.location.href='".PROJECTPATH."/setting_facility_data/seller' </script>"; 
	}
	
	public function type_evidence()
	{
		$arr_data = array();
		$id = @$_GET['id'];
		if(!empty($id)){
			$this->db->select(array('*'));
			$this->db->from('coop_type_evidence');
			$this->db->where("evidence_id = '{$id}'");
			$rs = $this->db->get()->result_array();
			$arr_data['row'] = @$rs[0]; 	
		}else{	
			$x=0;
			$join_arr = array();
			
			$this->paginater_all->type(DB_TYPE);
			$this->paginater_all->select('*');
			$this->paginater_all->main_table('coop_type_evidence');
			$this->paginater_all->where("");
			$this->paginater_all->page_now(@$_GET["page"]);
			$this->paginater_all->per_page(20);
			$this->paginater_all->page_link_limit(20);
			$this->paginater_all->order_by('evidence_id DESC');
			$this->paginater_all->join_arr($join_arr);
			$row = $this->paginater_all->paginater_process();
			//echo $this->db->last_query();exit;
			//echo"<pre>";print_r($row);exit;
			$paging = $this->pagination_center->paginating($row['page'], $row['num_rows'], $row['per_page'], $row['page_link_limit'], $_GET);//$page_now = 1, $row_total = 1, $per_page = 20, $page_limit = 20
			
			$i = $row['page_start'];

			$arr_data['num_rows'] = $row['num_rows'];
			$arr_data['paging'] = $paging;
			$arr_data['rs'] = $row['data'];
			$arr_data['i'] = $i;
		}
		
		$this->libraries->template('setting_facility_data/type_evidence',$arr_data);
	}
	
	public function type_evidence_save()
	{
		$data_insert = array();			
		$data_insert['evidence_name']    = @$_POST["evidence_name"];
		$data_insert['updatetime']    = date('Y-m-d H:i:s');

		$type_add = @$_POST["type_add"] ;
		$id_edit = @$_POST["id"] ;
			

		$table = "coop_type_evidence";

		if ($type_add == 'add') {	
		// add		
			$data_insert['createdatetime'] = date('Y-m-d H:i:s');
			$this->db->insert($table, $data_insert);
			$this->center_function->toast("บันทึกข้อมูลเรียบร้อยแล้ว");

		// add
		}else{
		// edit
			$this->db->where('evidence_id', $id_edit);
			$this->db->update($table, $data_insert);	
			$this->center_function->toast("แก้ไขข้อมูลเรียบร้อยแล้ว");

		// edit
		}
		
		echo"<script> document.location.href='".PROJECTPATH."/setting_facility_data/type_evidence' </script>"; 
	}
	
	public function type_guarantee()
	{
		$arr_data = array();
		$id = @$_GET['id'];
		if(!empty($id)){
			$this->db->select(array('*'));
			$this->db->from('coop_type_guarantee');
			$this->db->where("guarantee_id = '{$id}'");
			$rs = $this->db->get()->result_array();
			$arr_data['row'] = @$rs[0]; 	
		}else{	
			$x=0;
			$join_arr = array();
			
			$this->paginater_all->type(DB_TYPE);
			$this->paginater_all->select('*');
			$this->paginater_all->main_table('coop_type_guarantee');
			$this->paginater_all->where("");
			$this->paginater_all->page_now(@$_GET["page"]);
			$this->paginater_all->per_page(20);
			$this->paginater_all->page_link_limit(20);
			$this->paginater_all->order_by('guarantee_id DESC');
			$this->paginater_all->join_arr($join_arr);
			$row = $this->paginater_all->paginater_process();
			//echo $this->db->last_query();exit;
			//echo"<pre>";print_r($row);exit;
			$paging = $this->pagination_center->paginating($row['page'], $row['num_rows'], $row['per_page'], $row['page_link_limit'], $_GET);//$page_now = 1, $row_total = 1, $per_page = 20, $page_limit = 20
			
			$i = $row['page_start'];

			$arr_data['num_rows'] = $row['num_rows'];
			$arr_data['paging'] = $paging;
			$arr_data['rs'] = $row['data'];
			$arr_data['i'] = $i;
		}
		
		$this->libraries->template('setting_facility_data/type_guarantee',$arr_data);
	}
	
	public function type_guarantee_save()
	{
		$data_insert = array();			
		$data_insert['guarantee_name']    = @$_POST["guarantee_name"];
		$data_insert['updatetime']    = date('Y-m-d H:i:s');

		$type_add = @$_POST["type_add"] ;
		$id_edit = @$_POST["id"] ;
			

		$table = "coop_type_guarantee";

		if ($type_add == 'add') {	
		// add		
			$data_insert['createdatetime'] = date('Y-m-d H:i:s');
			$this->db->insert($table, $data_insert);
			$this->center_function->toast("บันทึกข้อมูลเรียบร้อยแล้ว");

		// add
		}else{
		// edit
			$this->db->where('guarantee_id', $id_edit);
			$this->db->update($table, $data_insert);	
			$this->center_function->toast("แก้ไขข้อมูลเรียบร้อยแล้ว");

		// edit
		}
		
		echo"<script> document.location.href='".PROJECTPATH."/setting_facility_data/type_guarantee' </script>"; 
	}
	
	public function depreciation()
	{
		$arr_data = array();
		$id = @$_GET['id'];
		if(!empty($id)){
			$this->db->select(array('*'));
			$this->db->from('coop_depreciation');
			$this->db->where("depreciation_id = '{$id}'");
			$rs = $this->db->get()->result_array();
			$arr_data['row'] = @$rs[0]; 	
		}else{	
			$x=0;
			$join_arr = array();
			
			$this->paginater_all->type(DB_TYPE);
			$this->paginater_all->select('*');
			$this->paginater_all->main_table('coop_depreciation');
			$this->paginater_all->where("");
			$this->paginater_all->page_now(@$_GET["page"]);
			$this->paginater_all->per_page(20);
			$this->paginater_all->page_link_limit(20);
			$this->paginater_all->order_by('depreciation_id DESC');
			$this->paginater_all->join_arr($join_arr);
			$row = $this->paginater_all->paginater_process();
			//echo $this->db->last_query();exit;
			//echo"<pre>";print_r($row);exit;
			$paging = $this->pagination_center->paginating($row['page'], $row['num_rows'], $row['per_page'], $row['page_link_limit'], $_GET);//$page_now = 1, $row_total = 1, $per_page = 20, $page_limit = 20
			
			$i = $row['page_start'];

			$arr_data['num_rows'] = $row['num_rows'];
			$arr_data['paging'] = $paging;
			$arr_data['rs'] = $row['data'];
			$arr_data['i'] = $i;
		}
		
		$this->libraries->template('setting_facility_data/depreciation',$arr_data);
	}
	
	public function depreciation_save()
	{
		$data_insert = array();			
		$data_insert['depreciation_name']    = @$_POST["depreciation_name"];
		$data_insert['depreciation_percent']    = @$_POST["depreciation_percent"];
		$data_insert['updatetime']    = date('Y-m-d H:i:s');

		$type_add = @$_POST["type_add"] ;
		$id_edit = @$_POST["id"] ;
			

		$table = "coop_depreciation";

		if ($type_add == 'add') {	
		// add		
			$data_insert['createdatetime'] = date('Y-m-d H:i:s');
			$this->db->insert($table, $data_insert);
			$this->center_function->toast("บันทึกข้อมูลเรียบร้อยแล้ว");

		// add
		}else{
		// edit
			$this->db->where('depreciation_id', $id_edit);
			$this->db->update($table, $data_insert);	
			$this->center_function->toast("แก้ไขข้อมูลเรียบร้อยแล้ว");

		// edit
		}
		
		echo"<script> document.location.href='".PROJECTPATH."/setting_facility_data/depreciation' </script>"; 
	}
	
	public function request_type()
	{
		$arr_data = array();
		$id = @$_GET['id'];
		if(!empty($id)){
			$this->db->select(array('*'));
			$this->db->from('coop_request_type');
			$this->db->where("request_type_id = '{$id}'");
			$rs = $this->db->get()->result_array();
			$arr_data['row'] = @$rs[0]; 	
		}else{	
			$x=0;
			$join_arr = array();
			
			$this->paginater_all->type(DB_TYPE);
			$this->paginater_all->select('*');
			$this->paginater_all->main_table('coop_request_type');
			$this->paginater_all->where("");
			$this->paginater_all->page_now(@$_GET["page"]);
			$this->paginater_all->per_page(20);
			$this->paginater_all->page_link_limit(20);
			$this->paginater_all->order_by('request_type_id DESC');
			$this->paginater_all->join_arr($join_arr);
			$row = $this->paginater_all->paginater_process();
			//echo $this->db->last_query();exit;
			//echo"<pre>";print_r($row);exit;
			$paging = $this->pagination_center->paginating($row['page'], $row['num_rows'], $row['per_page'], $row['page_link_limit'], $_GET);//$page_now = 1, $row_total = 1, $per_page = 20, $page_limit = 20
			
			$i = $row['page_start'];

			$arr_data['num_rows'] = $row['num_rows'];
			$arr_data['paging'] = $paging;
			$arr_data['rs'] = $row['data'];
			$arr_data['i'] = $i;
		}
		
		$this->libraries->template('setting_facility_data/request_type',$arr_data);
	}
	
	public function request_type_save()
	{
		$data_insert = array();			
		$data_insert['request_type']    = @$_POST["request_type"];

		$type_add = @$_POST["type_add"] ;
		$id_edit = @$_POST["id"] ;
			

		$table = "coop_request_type";

		if ($type_add == 'add') {	
		// add		
			$data_insert['create_date'] = date('Y-m-d H:i:s');
			$this->db->insert($table, $data_insert);
			$this->center_function->toast("บันทึกข้อมูลเรียบร้อยแล้ว");

		// add
		}else{
		// edit
			$this->db->where('request_type_id', $id_edit);
			$this->db->update($table, $data_insert);	
			$this->center_function->toast("แก้ไขข้อมูลเรียบร้อยแล้ว");

		// edit
		}
		
		echo"<script> document.location.href='".PROJECTPATH."/setting_facility_data/request_type' </script>"; 
	}
	
	public function facility_type()
	{
		$arr_data = array();
		$id = @$_GET['id'];
		if(!empty($id)){
			$this->db->select(array('*'));
			$this->db->from('coop_facility_type');
			$this->db->where("facility_type_id = '{$id}'");
			$rs = $this->db->get()->result_array();
			$arr_data['row'] = @$rs[0]; 	
		}else{	
			$x=0;
			$join_arr = array();
			
			$this->paginater_all->type(DB_TYPE);
			$this->paginater_all->select('*');
			$this->paginater_all->main_table('coop_facility_type');
			$this->paginater_all->where("");
			$this->paginater_all->page_now(@$_GET["page"]);
			$this->paginater_all->per_page(20);
			$this->paginater_all->page_link_limit(20);
			$this->paginater_all->order_by('facility_type_id DESC');
			$this->paginater_all->join_arr($join_arr);
			$row = $this->paginater_all->paginater_process();
			//echo $this->db->last_query();exit;
			//echo"<pre>";print_r($row);exit;
			$paging = $this->pagination_center->paginating($row['page'], $row['num_rows'], $row['per_page'], $row['page_link_limit'], $_GET);//$page_now = 1, $row_total = 1, $per_page = 20, $page_limit = 20
			
			$i = $row['page_start'];

			$arr_data['num_rows'] = $row['num_rows'];
			$arr_data['paging'] = $paging;
			$arr_data['rs'] = $row['data'];
			$arr_data['i'] = $i;
		}
		
		$this->libraries->template('setting_facility_data/facility_type',$arr_data);
	}
	
	public function facility_type_save()
	{
		$data_insert = array();			
		$data_insert['facility_type_name']    = @$_POST["facility_type_name"];
		$data_insert['updatetime'] = date('Y-m-d H:i:s');

		$type_add = @$_POST["type_add"] ;
		$id_edit = @$_POST["id"] ;
			

		$table = "coop_facility_type";

		if ($type_add == 'add') {	
		// add		
			$data_insert['createdatetime'] = date('Y-m-d H:i:s');
			$this->db->insert($table, $data_insert);
			$this->center_function->toast("บันทึกข้อมูลเรียบร้อยแล้ว");

		// add
		}else{
		// edit
			$this->db->where('facility_type_id', $id_edit);
			$this->db->update($table, $data_insert);	
			$this->center_function->toast("แก้ไขข้อมูลเรียบร้อยแล้ว");

		// edit
		}
		
		echo"<script> document.location.href='".PROJECTPATH."/setting_facility_data/facility_type' </script>"; 
	}
	
	public function facility_main()
	{
		$arr_data = array();
		$id = @$_GET['id'];
		if(!empty($id)){
			$this->db->select(array('*'));
			$this->db->from('coop_facility_main');
			$this->db->where("facility_main_id = '{$id}'");
			$rs = $this->db->get()->result_array();
			$arr_data['row'] = @$rs[0]; 	
		}else{	
			$x=0;
			$join_arr = array();
			$join_arr[$x]['table'] = 'coop_facility_type';
			$join_arr[$x]['condition'] = 'coop_facility_main.facility_type_id = coop_facility_type.facility_type_id';
			$join_arr[$x]['type'] = 'left';
			$x++;
			$join_arr[$x]['table'] = 'coop_unit_type';
			$join_arr[$x]['condition'] = 'coop_facility_main.unit_type_id = coop_unit_type.unit_type_id';
			$join_arr[$x]['type'] = 'left';
			$x++;
			$join_arr[$x]['table'] = 'coop_depreciation';
			$join_arr[$x]['condition'] = 'coop_facility_main.depreciation_id = coop_depreciation.depreciation_id';
			$join_arr[$x]['type'] = 'left';
			
			$this->paginater_all->type(DB_TYPE);
			$this->paginater_all->select('coop_facility_main.*,coop_facility_type.facility_type_name, 
								coop_unit_type.unit_type_name, 
								coop_depreciation.depreciation_name');
			$this->paginater_all->main_table('coop_facility_main');
			$this->paginater_all->where("");
			$this->paginater_all->page_now(@$_GET["page"]);
			$this->paginater_all->per_page(20);
			$this->paginater_all->page_link_limit(20);
			$this->paginater_all->order_by('coop_facility_main.facility_main_id DESC');
			$this->paginater_all->join_arr($join_arr);
			$row = $this->paginater_all->paginater_process();
			//echo $this->db->last_query();exit;
			//echo"<pre>";print_r($row);exit;
			$paging = $this->pagination_center->paginating($row['page'], $row['num_rows'], $row['per_page'], $row['page_link_limit'], $_GET);//$page_now = 1, $row_total = 1, $per_page = 20, $page_limit = 20
			
			$i = $row['page_start'];

			$arr_data['num_rows'] = $row['num_rows'];
			$arr_data['paging'] = $paging;
			$arr_data['rs'] = $row['data'];
			$arr_data['i'] = $i;
		}
		
		//รหัส >> เลือกจากกลุ่มพัสดุ
		$this->db->select(array('*'));
		$this->db->from('coop_facility_group');
		$this->db->where("facility_group_type = '3'");
		$rs_group = $this->db->get()->result_array();
		$arr_data['rs_group'] = @$rs_group; 
		//print_r($this->db->last_query()); exit;
		//ประเภทพัสดุ
		$this->db->select(array('*'));
		$this->db->from('coop_facility_type');
		$rs_type = $this->db->get()->result_array();
		$arr_data['rs_type'] = @$rs_type;
		
		//หน่วยนับ
		$this->db->select(array('*'));
		$this->db->from('coop_unit_type');
		$rs_unit = $this->db->get()->result_array();
		$arr_data['rs_unit'] = @$rs_unit; 
		
		//ค่าเสื่อม
		$this->db->select(array('*'));
		$this->db->from('coop_depreciation');
		$rs_depreciation = $this->db->get()->result_array();
		$arr_data['rs_depreciation'] = @$rs_depreciation; 
		
		$this->libraries->template('setting_facility_data/facility_main',$arr_data);
	}
	
	public function facility_main_save()
	{
		$data_insert = array();
		$type_add = @$_POST["type_add"] ;
		$id_edit = @$_POST["id"] ;
		$table = "coop_facility_main";
		
		$facility_main_code = @$_POST["facility_main_code"];
		$facility_group_full_code = @$_POST["facility_group_full_code"];
		$facility_main_run = @$_POST["facility_main_run"];		
		
		$this->db->select(array('MAX(facility_main_run) AS last_run'));
		$this->db->from('coop_facility_main');
		$this->db->where("facility_group_full_code = '{$facility_main_code}'");
		$rs = $this->db->get()->result_array();
		$row = @$rs[0]; 
		
		if(!empty($id_edit) && $facility_main_code == $facility_group_full_code){
			$main_run = $facility_main_run;	
			$main_id = sprintf("%04d",$facility_main_run);	
		}else{
			$main_run = $row['last_run']+1;	
			$main_id = sprintf("%04d",$main_run);
		}

		
		$facility_main_code = $_POST["facility_main_code"].'-'.$main_id;
		$data_insert['facility_main_run']  = @$main_run;
		$data_insert['facility_group_full_code']  = @$_POST["facility_main_code"];
		$data_insert['facility_main_code']  = @$facility_main_code;
		$data_insert['facility_type_id']    = @$_POST["facility_type_id"];
		$data_insert['facility_main_name']  = @$_POST["facility_main_name"];
		$data_insert['facility_main_note']  = @$_POST["facility_main_note"];
		$data_insert['facility_main_price'] = @$_POST["facility_main_price"];
		$data_insert['unit_type_id']    = @$_POST["unit_type_id"];
		$data_insert['depreciation_id']    = @$_POST["depreciation_id"];
		$data_insert['updatetime'] = date('Y-m-d H:i:s');			

		if ($type_add == 'add') {	
		// add		
			$data_insert['createdatetime'] = date('Y-m-d H:i:s');
			$this->db->insert($table, $data_insert);
			$this->center_function->toast("บันทึกข้อมูลเรียบร้อยแล้ว");

		// add
		}else{
		// edit
			$this->db->where('facility_main_id', $id_edit);
			$this->db->update($table, $data_insert);	
			$this->center_function->toast("แก้ไขข้อมูลเรียบร้อยแล้ว");

		// edit
		}
		
		echo"<script> document.location.href='".PROJECTPATH."/setting_facility_data/facility_main' </script>"; 
	}

}
