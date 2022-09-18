<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Setting_member_data extends CI_Controller {
	function __construct()
	{
		parent::__construct();
	}
	
	public function coop_prename(){
		$arr_data = array();
		$id = @$_GET['id'];
		if(@$id){
			$this->db->select(array('*'));
			$this->db->from('coop_prename');
			$this->db->where("prename_id  = '{$id}' ");
			$rs = $this->db->get()->result_array();
			$arr_data['row'] = @$rs[0];
		}else{	
			$x=0;
			$join_arr = array();
			
			$this->paginater_all->type(DB_TYPE);
			$this->paginater_all->select('*');
			$this->paginater_all->main_table('coop_prename');
			$this->paginater_all->where("");
			$this->paginater_all->page_now(@$_GET["page"]);
			$this->paginater_all->per_page(20);
			$this->paginater_all->page_link_limit(20);
			$this->paginater_all->order_by('prename_id ASC');
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
		$this->libraries->template('setting_member_data/coop_prename',$arr_data);
	}
	
	public function coop_prename_save(){
		$data_insert = array();
		$data_insert['prename_full']	= @$_POST["prename_full"];
		$data_insert['prename_short']	= @$_POST["prename_short"];
		$data_insert['sex']  = @$_POST["sex"];

		$type_add = @$_POST["type_add"] ;
		$id_edit = @$_POST["id"] ;

		$table = "coop_prename";

		if ($type_add == 'add') {
			// add
			$this->db->insert($table, $data_insert);
			$this->center_function->toast("บันทึกข้อมูลเรียบร้อยแล้ว");
			// add
		}else{
			// edit
			$this->db->where('prename_id', $id_edit);
			$this->db->update($table, $data_insert);
			$this->center_function->toast("แก้ไขข้อมูลเรียบร้อยแล้ว");	
			// edit
		}

		echo"<script> document.location.href='".PROJECTPATH."/setting_member_data/coop_prename' </script>";            
	}
	
	function del_coop_member_data(){	
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
	
	public function coop_group(){
		$arr_data = array();

		$x=0;
		$join_arr = array();
		$join_arr[$x]['table'] = 'coop_mem_group as department_group';
		$join_arr[$x]['condition'] = 'coop_mem_group.mem_group_parent_id = department_group.id';
		$join_arr[$x]['type'] = 'left';
		
		$x++;
		$join_arr[$x]['table'] = 'coop_mem_group as parent_group';
		$join_arr[$x]['condition'] = 'department_group.mem_group_parent_id = parent_group.id';
		$join_arr[$x]['type'] = 'left';	
		
		$this->paginater_all->type(DB_TYPE);
		$this->paginater_all->select(array('coop_mem_group.*',
									'department_group.mem_group_name as department_name',
									'department_group.mem_group_id as department_id',
									'parent_group.mem_group_name as parent_name',
									'parent_group.mem_group_id as parent_id'));
		$this->paginater_all->main_table('coop_mem_group');
		$this->paginater_all->where("coop_mem_group.mem_group_type = 3");
		$this->paginater_all->page_now(@$_GET["page"]);
		$this->paginater_all->per_page(20);
		$this->paginater_all->page_link_limit(20);
		$this->paginater_all->order_by('parent_group.mem_group_id,department_group.mem_group_id ASC');
		$this->paginater_all->join_arr($join_arr);
		$row = $this->paginater_all->paginater_process();
		//echo"<pre>";print_r($row);exit;
		$paging = $this->pagination_center->paginating($row['page'], $row['num_rows'], $row['per_page'], $row['page_link_limit'],$_GET);//$page_now = 1, $row_total = 1, $per_page = 20, $page_limit = 20
		$i = $row['page_start'];


		$arr_data['num_rows'] = $row['num_rows'];
		$arr_data['paging'] = $paging;
		$arr_data['rs'] = $row['data'];
		$arr_data['i'] = $i;
		
		$this->db->select(array('*'));
		$this->db->from('coop_mem_group');
		$this->db->where("mem_group_type  = '1' ");
		$rs_group = $this->db->get()->result_array();
		$arr_data['rs_group'] = @$rs_group;
		//echo '<pre>'; print_r($rs_group); echo '</pre>'; exit;
		
		//$this->db->select(array('*'));
		$this->db->select('coop_mem_group.*, group_parent.mem_group_name as parent_name ');
		$this->db->from('coop_mem_group');
		$this->db->join('coop_mem_group as group_parent', 'coop_mem_group.mem_group_parent_id = group_parent.id', 'left');		
		$this->db->where("coop_mem_group.mem_group_type  = '2' ");
		$rs_group2 = $this->db->get()->result_array();
		$arr_data['rs_group2'] = @$rs_group2;
		//print_r($this->db->last_query());exit;

		$this->libraries->template('setting_member_data/coop_group',$arr_data);
	}
	
	public function coop_group_save(){
		$data_insert = array();
		if(@$_POST['department_parent']!=''){
			$mem_group_parent_id = @$_POST['department_parent'];
		}else if(@$_POST['group_parent']!=''){
			$mem_group_parent_id = @$_POST['group_parent'];
		}else{
			$mem_group_parent_id = '';
		}
		
		$data_insert['mem_group_id']	= @$_POST["mem_group_id"];		
		$data_insert['mem_group_parent_id']  = @$mem_group_parent_id;
		$data_insert['mem_group_name']	= @$_POST["mem_group_name"];
		$data_insert['mem_group_full_name']	= @$_POST["mem_group_full_name"];

		$id_edit = @$_POST["id"] ;

		$table = "coop_mem_group";

		if ($id_edit == '') {
			// add
			$data_insert['mem_group_type']	= @$_POST["mem_group_type"];
			
			$this->db->insert($table, $data_insert);
			$this->center_function->toast("บันทึกข้อมูลเรียบร้อยแล้ว");
			// add
		}else{
			// edit
			$this->db->where('id', $id_edit);
			$this->db->update($table, $data_insert);
			$this->center_function->toast("แก้ไขข้อมูลเรียบร้อยแล้ว");	
			// edit
		}

		echo"<script> document.location.href='".PROJECTPATH."/setting_member_data/coop_group' </script>"; 
	}
	
	public function get_group_child(){		
		$this->db->select(array('*'));
		$this->db->from('coop_mem_group');
		$this->db->where("mem_group_parent_id = '".$_POST['group_id']."'");
		$rs = $this->db->get()->result_array();
		$output = '';
		$output .= '  
					<select class="form-control" id="department_parent" name="department_parent">
						<option value="">เลือกแผนก</option>
					';
		if(!empty($rs)){  
			$i= 1; 
			foreach($rs as $key => $row){				
			   $output .= ' <option value="'.@$row['id'].'">'.@$row['mem_group_name'].'</option>';									
		    }
			 
		} 
		$output .= '</select>';
		echo $output; 
		exit;
	}
	
	public function get_group_parent(){		
		$this->db->select(array('*'));
		$this->db->from('coop_mem_group');
		$this->db->where("id = '".$_POST['id']."'");
		$rs = $this->db->get()->result_array();
		$output = '';
		if(!empty($rs)){  
			foreach($rs as $key => $row){				
			   $output .= @$row['mem_group_parent_id'];								
		    }
			 
		} 
		echo $output; 
		exit;
	}
	
	public function check_delete_mem_group(){				
		$this->db->select(array('*'));
		$this->db->from('coop_mem_group');
		$this->db->where("mem_group_parent_id = '".@$_POST['id']."'");
		$rs = $this->db->get()->result_array();
		$row = @$rs[0];
		if($row['id']!=''){
			echo "error";
		}else{
			echo "success";
		}
		exit;		
	}
	
	public function save_mem_group(){		
	
		if(@$_POST['delete_action']=='delete_action'){
			$this->db->where('id', @$_POST['id']);
			$this->db->delete('coop_mem_group');
		}else{
			$data_insert = array();
			$data_insert['mem_group_id']	= @$_POST["mem_group_id"];
			$data_insert['mem_group_name']	= @$_POST["mem_group_name"];
			$data_insert['mem_group_full_name']	= @$_POST["mem_group_full_name"];
			if(@$_POST['id']==''){
				// add
				$data_insert['mem_group_type']	= @$_POST["mem_group_type"];
				$data_insert['mem_group_parent_id']	= @$_POST["mem_group_parent_id"];
				
				$this->db->insert('coop_mem_group', $data_insert);
				
			}else{
				// edit
				$this->db->where('id', $id);
				$this->db->update('coop_mem_group', $data_insert);
			}
		}
			$group_type[1] = 'หน่วยงาน';
			$group_type[2] = 'ฝ่าย';
			$group_type[3] = 'แผนก';
			//if($mysqli->query($sql)){
				$this->db->select(array('*'));
				$this->db->from('coop_mem_group');
				$this->db->where("mem_group_type = '1'");
				$rs_group = $this->db->get()->result_array();
		
				$table="";
				
				if(!empty($rs_group)){  
					foreach($rs_group as $key => $row_group){	
					$table .= "<tr>";
					$table .= "<td>".@$row_group['mem_group_id']."</td>";
					$table .= "<td>".@$row_group['mem_group_name']."</td>";
					$table .= "<td></td>";
					$table .= "<td></td>";
					$table .= "<td>";
					if($row_group['mem_group_type']=='1'){
						$table .= "<a style=\"cursor:pointer;\" onclick=\"add_group('2','".@$row_group['id']."');\">เพิ่มฝ่าย</a> | ";
					}else if($row_group['mem_group_type']=='2'){ 
						$table .= "<a style=\"cursor:pointer;\" onclick=\"add_group('3','".@$row_group['id']."');\">เพิ่มแผนก</a> | ";
					} 
					$table .= "<a style=\"cursor:pointer;\" onclick=\"edit_mem_group('".@$row_group['id']."','".@$row_group['mem_group_id']."','".@$row_group['mem_group_name']."','".@$row_group['mem_group_full_name']."');\">แก้ไข</a> | <a style=\"cursor:pointer;\" class=\"text-del\" onclick=\"delete_mem_group('".@$row_group['id']."');\">ลบ</a></td>";
					$table .= "</tr>";
						
						$this->db->select(array('*'));
						$this->db->from('coop_mem_group');
						$this->db->where("mem_group_parent_id = '".@$row_group['id']."'");
						$rs_group2 = $this->db->get()->result_array();
						if(!empty($rs_group2)){  
							foreach($rs_group2 as $key => $row_group2){							
								$table .= "<tr>";
								$table .= "<td>".@$row_group2['mem_group_id']."</td>";
								$table .= "<td>".@$row_group['mem_group_name']."</td>";
								$table .= "<td>".@$row_group2['mem_group_name']."</td>";
								$table .= "<td></td>";
								$table .= "<td>";
								if(@$row_group2['mem_group_type']=='1'){
								$table .= "<a style=\"cursor:pointer;\" onclick=\"add_group('2','".@$row_group2['id']."');\">เพิ่มฝ่าย</a> | ";
								}else if(@$row_group2['mem_group_type']=='2'){ 
								$table .= "<a style=\"cursor:pointer;\" onclick=\"add_group('3','".@$row_group2['id']."');\">เพิ่มแผนก</a> | ";
								} 
								$table .= "<a style=\"cursor:pointer;\" onclick=\"edit_mem_group('".@$row_group2['id']."','".@$row_group2['mem_group_id']."','".@$row_group2['mem_group_name']."','".@$row_group2['mem_group_full_name']."');\">แก้ไข</a> | <a style=\"cursor:pointer;\" class=\"text-del\" onclick=\"delete_mem_group('".@$row_group2['id']."');\">ลบ</a></td>";
								$table .= "</tr>";	
								
								$this->db->select(array('*'));
								$this->db->from('coop_mem_group');
								$this->db->where("mem_group_parent_id = '".@$row_group2['id']."'");
								$rs_group3 = $this->db->get()->result_array();
								if(!empty($rs_group3)){  
									foreach($rs_group3 as $key => $row_group3){	
										$table .= "<tr>";
										$table .= "<td>".@$row_group3['mem_group_id']."</td>";
										$table .= "<td>".@$row_group['mem_group_name']."</td>";
										$table .= "<td>".@$row_group2['mem_group_name']."</td>";
										$table .= "<td>".@$row_group3['mem_group_name']."</td>";
										$table .= "<td>";
										if(@$row_group3['mem_group_type']=='1'){
										$table .= "<a style=\"cursor:pointer;\" onclick=\"add_group('2','".@$row_group3['id']."');\">เพิ่มฝ่าย</a> | ";
										}else if($row_group3['mem_group_type']=='2'){ 
										$table .= "<a style=\"cursor:pointer;\" onclick=\"add_group('3','".@$row_group3['id']."');\">เพิ่มแผนก</a> | ";
										} 
										$table .= "<a style=\"cursor:pointer;\" onclick=\"edit_mem_group('".@$row_group3['id']."','".@$row_group3['mem_group_id']."','".@$row_group3['mem_group_name']."','".@$row_group3['mem_group_full_name']."');\">แก้ไข</a> | <a style=\"cursor:pointer;\" class=\"text-del\" onclick=\"delete_mem_group('".@$row_group3['id']."');\">ลบ</a></td>";
										$table .= "</tr>";
									}
								}
							}
					}	
					}	
				}
				echo $table;
			//}else{
			//	echo "error";
			//}
		exit;
	}
		
	public function coop_register_type(){
		$arr_data = array();
		$id = @$_GET['id'];
		if(@$id){
			$this->db->select(array('*'));
			$this->db->from('coop_mem_apply_type');
			$this->db->where("apply_type_id  = '{$id}' ");
			$rs = $this->db->get()->result_array();
			$arr_data['row'] = @$rs[0];
		}else{	
			$x=0;
			$join_arr = array();
			
			$this->paginater_all->type(DB_TYPE);
			$this->paginater_all->select('*');
			$this->paginater_all->main_table('coop_mem_apply_type');
			$this->paginater_all->where("");
			$this->paginater_all->page_now(@$_GET["page"]);
			$this->paginater_all->per_page(20);
			$this->paginater_all->page_link_limit(20);
			$this->paginater_all->order_by('apply_type_id ASC');
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
		//print_r($this->db->last_query());exit;

		$this->libraries->template('setting_member_data/coop_register_type',$arr_data);
	}
	
	public function coop_register_type_save(){
		$data_insert = array();		
		
		$data_insert['apply_type_name']	= @$_POST["apply_type_name"];
		$data_insert['fee']	= @$_POST["fee"];
		$data_insert['type_age'] = @$_POST["type_age"];
		$data_insert['age_limit'] = @$_POST["age_limit"];
		$data_insert['type_share'] = @$_POST["type_share"];
		$data_insert['amount_min'] = @$_POST["amount_min"];
		$data_insert['amount_max'] = @$_POST["amount_max"];

		$id_edit = @$_POST["id"] ;
		$type_add = @$_POST["type_add"] ;
		$table = "coop_mem_apply_type";

		if ($type_add == 'add') {
			// add			
			$this->db->insert($table, $data_insert);
			$this->center_function->toast("บันทึกข้อมูลเรียบร้อยแล้ว");
			// add
		}else{
			// edit
			$this->db->where('apply_type_id', $id_edit);
			$this->db->update($table, $data_insert);
			$this->center_function->toast("แก้ไขข้อมูลเรียบร้อยแล้ว");	
			// edit
		}

		echo"<script> document.location.href='".PROJECTPATH."/setting_member_data/coop_register_type' </script>"; 
	}
	
	public function coop_cause_quite(){
		$arr_data = array();
		$id = @$_GET['id'];
		if(@$id){
			$this->db->select(array('*'));
			$this->db->from('coop_mem_resign_cause');
			$this->db->where("resign_cause_id  = '{$id}' ");
			$rs = $this->db->get()->result_array();
			$arr_data['row'] = @$rs[0];
		}else{	
			$x=0;
			$join_arr = array();
			
			$this->paginater_all->type(DB_TYPE);
			$this->paginater_all->select('*');
			$this->paginater_all->main_table('coop_mem_resign_cause');
			$this->paginater_all->where("");
			$this->paginater_all->page_now(@$_GET["page"]);
			$this->paginater_all->per_page(20);
			$this->paginater_all->page_link_limit(20);
			$this->paginater_all->order_by('resign_cause_id ASC');
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
		//print_r($this->db->last_query());exit;

		$this->libraries->template('setting_member_data/coop_cause_quite',$arr_data);
	}
	
	public function coop_cause_quite_save(){
		$data_insert = array();		
		
		$data_insert['resign_cause_name']	= @$_POST["resign_cause_name"];
		$data_insert['apply_type_id']	= @$_POST["apply_type_id"];
		$data_insert['mem_type_id']	= @$_POST["mem_type_id"];
		if(!empty($_POST['check_debt'])) {
			$data_insert['check_debt']	= 1;
		} else {
			$data_insert['check_debt']	= 0;
		}

		if(!empty($_POST['cal_interest_af'])) {
			$data_insert['cal_interest_af']	= 1;
		} else {
			$data_insert['cal_interest_af']	= 0;
		}

		$id_edit = @$_POST["id"] ;
		$type_add = @$_POST["type_add"] ;
		$table = "coop_mem_resign_cause";

		if ($type_add == 'add') {
			// add			
			$this->db->insert($table, $data_insert);
			$this->center_function->toast("บันทึกข้อมูลเรียบร้อยแล้ว");
			// add
		}else{
			// edit
			$this->db->where('resign_cause_id', $id_edit);
			$this->db->update($table, $data_insert);
			$this->center_function->toast("แก้ไขข้อมูลเรียบร้อยแล้ว");	
			// edit
		}

		echo"<script> document.location.href='".PROJECTPATH."/setting_member_data/coop_cause_quite' </script>"; 
	}
	
	public function coop_mem_relation(){
		$arr_data = array();
		$id = @$_GET['id'];
		if(@$id){
			$this->db->select(array('*'));
			$this->db->from('coop_mem_relation');
			$this->db->where("relation_id  = '{$id}' ");
			$rs = $this->db->get()->result_array();
			$arr_data['row'] = @$rs[0];
		}else{	
			$x=0;
			$join_arr = array();
			
			$this->paginater_all->type(DB_TYPE);
			$this->paginater_all->select('*');
			$this->paginater_all->main_table('coop_mem_relation');
			$this->paginater_all->where("");
			$this->paginater_all->page_now(@$_GET["page"]);
			$this->paginater_all->per_page(20);
			$this->paginater_all->page_link_limit(20);
			$this->paginater_all->order_by('relation_id ASC');
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
		//print_r($this->db->last_query());exit;

		$this->libraries->template('setting_member_data/coop_mem_relation',$arr_data);
	}
	
	public function coop_mem_relation_save(){
		$data_insert = array();		
		
		$data_insert['relation_name']	= @$_POST["relation_name"];

		$id_edit = @$_POST["id"] ;
		$type_add = @$_POST["type_add"] ;
		$table = "coop_mem_relation";

		if ($type_add == 'add') {
			// add			
			$this->db->insert($table, $data_insert);
			$this->center_function->toast("บันทึกข้อมูลเรียบร้อยแล้ว");
			// add
		}else{
			// edit
			$this->db->where('relation_id', $id_edit);
			$this->db->update($table, $data_insert);
			$this->center_function->toast("แก้ไขข้อมูลเรียบร้อยแล้ว");	
			// edit
		}
		
		echo"<script> document.location.href='".PROJECTPATH."/setting_member_data/coop_mem_relation' </script>"; 
	}
	
	public function coop_member(){
		$arr_data = array();
		$id = @$_GET['id'];
		if(@$id){
			$this->db->select(array('*'));
			$this->db->from('coop_mem_type');
			$this->db->where("mem_type_id  = '{$id}' ");
			$rs = $this->db->get()->result_array();
			$arr_data['row'] = @$rs[0];
		}else{	
			$x=0;
			$join_arr = array();
			
			$this->paginater_all->type(DB_TYPE);
			$this->paginater_all->select('*');
			$this->paginater_all->main_table('coop_mem_type');
			$this->paginater_all->where("");
			$this->paginater_all->page_now(@$_GET["page"]);
			$this->paginater_all->per_page(20);
			$this->paginater_all->page_link_limit(20);
			$this->paginater_all->order_by('mem_type_id ASC');
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

		$this->libraries->template('setting_member_data/coop_member',$arr_data);
	}
	
	public function coop_member_save(){
		$data_insert = array();		
		
		$data_insert['mem_type_name']	= @$_POST["mem_type_name"];
		$data_insert['mem_type_status']	= '1';
		$data_insert['mem_type_group']	= @$_POST["mem_type_group"];
		$data_insert['loan_status']	= @$_POST["loan_status"];
		$data_insert['grt_status']	= @$_POST["grt_status"];
		$data_insert['checklngrt_status']	= @$_POST["checklngrt_status"];

		$id_edit = @$_POST["id"] ;
		$type_add = @$_POST["type_add"] ;
		$table = "coop_mem_type";

		if ($type_add == 'add') {
			// add			
			$this->db->insert($table, $data_insert);
			$this->center_function->toast("บันทึกข้อมูลเรียบร้อยแล้ว");
			// add
		}else{
			// edit
			$this->db->where('mem_type_id', $id_edit);
			$this->db->update($table, $data_insert);
			$this->center_function->toast("แก้ไขข้อมูลเรียบร้อยแล้ว");	
			// edit
		}
		
		echo"<script> document.location.href='".PROJECTPATH."/setting_member_data/coop_member' </script>"; 
	}
	
	public function coop_quite(){
		$this->db->select(array('*'));
		$this->db->from('coop_quite_setting');
		$this->db->order_by('id DESC');
		$rs = $this->db->get()->result_array();
		$arr_data['row'] = @$rs[0];
		$this->libraries->template('setting_member_data/coop_quite',$arr_data);
	}
	
	public function coop_quite_save(){
		$data_insert = array();		
		
		$data_insert['year_quite']	= @$_POST["year_quite"];
		$id_edit = @$_POST["id"] ;
		$table = "coop_quite_setting";
		// edit
		$this->db->where('id', $id_edit);
		$this->db->update($table, $data_insert);
		$this->center_function->toast("บันทึกข้อมูลเรียบร้อยแล้ว");	
		// edit
		echo"<script> document.location.href='".PROJECTPATH."/setting_member_data/coop_quite' </script>"; 
	}

	public function coop_retire(){
		$this->db->select(array('*'));
		$this->db->from('coop_profile');
		$rs = $this->db->get()->result_array();
		$arr_data['row'] = @$rs[0];
		$this->libraries->template('setting_member_data/coop_retire',$arr_data);
	}
	
	public function coop_retire_save(){
		$data_insert = array();		
		
		$data_insert['retire_age']	= @$_POST["retire_age"];
		$data_insert['retire_month']	= @$_POST["retire_month"];
		$id_edit = @$_POST["profile_id"] ;
		
		$table = "coop_profile";
		// edit
		$this->db->where('profile_id', $id_edit);
		$this->db->update($table, $data_insert);
		$this->center_function->toast("บันทึกข้อมูลเรียบร้อยแล้ว");	
		// edit
		echo"<script> document.location.href='".PROJECTPATH."/setting_member_data/coop_retire' </script>"; 
	}	
	

	public function coop_approval_cycle(){
		$this->db->select(array('*'));
		$this->db->from('coop_approval_cycle');
		$this->db->where("active_status  = '1' ");
		$rs = $this->db->get()->result_array();
		$arr_data['rs'] = @$rs;
		$this->libraries->template('setting_member_data/coop_approval_cycle',$arr_data);
	}
	
	public function coop_approval_cycle_save(){
		$data_insert = array();	
		
		$table = "coop_approval_cycle";
		foreach(@$_POST['approval_id'] as $key => $value){
			$data_insert['approval_date']	= @$value;
			$id_edit = @$key ;
			
			// edit
			$this->db->where('id', $id_edit);
			$this->db->update($table, $data_insert);
			$this->center_function->toast("บันทึกข้อมูลเรียบร้อยแล้ว");	
			// edit	
		}
	
		
		echo"<script> document.location.href='".PROJECTPATH."/setting_member_data/coop_approval_cycle' </script>"; 
	}	
	public function coop_mem_position(){
		if($_POST){
			//echo"<pre>";print_r($_POST);exit;
			if($_POST['id']!=''){
				$data_insert = array();
				$data_insert['position_name'] = $_POST['position_name'];
				$this->db->where('position_id',$_POST['id']);
				$this->db->update('coop_mem_position',$data_insert);
			}else{
				$data_insert = array();
				$data_insert['position_name'] = $_POST['position_name'];
				$this->db->insert('coop_mem_position',$data_insert);
			}
			$this->center_function->toast('บันทึกข้อมูลเรียบร้อยแล้ว');
			echo"<script> document.location.href='".PROJECTPATH."/setting_member_data/coop_mem_position' </script>"; 
		}
		$arr_data = array();
		$id = @$_GET['id'];
		if(@$id){
			$this->db->select(array('*'));
			$this->db->from('coop_mem_position');
			$this->db->where("position_id  = '{$id}' ");
			$rs = $this->db->get()->result_array();
			$arr_data['row'] = @$rs[0];
		}else{	
			$x=0;
			$join_arr = array();
			
			$this->paginater_all->type(DB_TYPE);
			$this->paginater_all->select('*');
			$this->paginater_all->main_table('coop_mem_position');
			$this->paginater_all->where("");
			$this->paginater_all->page_now(@$_GET["page"]);
			$this->paginater_all->per_page(20);
			$this->paginater_all->page_link_limit(20);
			$this->paginater_all->order_by('position_id ASC');
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
		$this->libraries->template('setting_member_data/coop_mem_position',$arr_data);
	}
}
