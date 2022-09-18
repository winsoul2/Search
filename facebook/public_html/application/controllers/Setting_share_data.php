<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Setting_share_data extends CI_Controller {
	function __construct()
	{
		parent::__construct();
	}
	
	public function coop_share_rule(){
		$arr_data = array();
		$id = @$_GET['id'];
		$filter = @$_GET['filter'];
		if(@$id){
			$this->db->select(array('*'));
			$this->db->from('coop_share_rule');
			$this->db->where("share_rule_id  = '{$id}' ");
			$rs = $this->db->get()->result_array();
			$arr_data['row'] = @$rs[0];
		}else{
			$where ='';
			if($filter != ''){
				$where = "AND  coop_share_rule.mem_type_id = '{$filter}'";
			}
			
			$x=0;
			$join_arr = array();
			$join_arr[$x]['table'] = 'coop_mem_type';
			$join_arr[$x]['condition'] = 'coop_share_rule.mem_type_id = coop_mem_type.mem_type_id';
			$join_arr[$x]['type'] = 'left';
			
			$this->paginater_all->type(DB_TYPE);
			$this->paginater_all->select('coop_share_rule.*,coop_mem_type.mem_type_name');
			$this->paginater_all->main_table('coop_share_rule');
			$this->paginater_all->where($where);
			$this->paginater_all->page_now(@$_GET["page"]);
			$this->paginater_all->per_page(20);
			$this->paginater_all->page_link_limit(20);
			$this->paginater_all->order_by('coop_share_rule.mem_type_id,coop_share_rule.salary_rule ASC');
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
			$this->db->from('coop_share_setting');
			$this->db->where("setting_id  = '1' ");
			$rs_setting = $this->db->get()->result_array();
			$arr_data['row_setting'] = @$rs_setting[0];
			
		}
		
		$this->db->select(array('*'));
		$this->db->from('coop_mem_type');
		$rs_type = $this->db->get()->result_array();
		$arr_data['rs_type'] = @$rs_type;
		
		$this->libraries->template('setting_share_data/coop_share_rule',$arr_data);
	}
	
	public function coop_share_rule_save(){
		$data_insert = array();
		$data_insert['mem_type_id']	= @$_POST["mem_type_id"];
		$data_insert['salary_rule']	= @$_POST["salary_rule"];
		$data_insert['share_first']	= '';
		$data_insert['share_salary']  = @$_POST["share_salary"];

		$type_add = @$_POST["type_add"] ;
		$id_edit = @$_POST["id"] ;

		$table = "coop_share_rule";

		if ($type_add == 'add') {
			// add
			$this->db->insert($table, $data_insert);
			$this->center_function->toast("บันทึกข้อมูลเรียบร้อยแล้ว");
			// add
		}else{
			// edit
			$this->db->where('share_rule_id', $id_edit);
			$this->db->update($table, $data_insert);
			$this->center_function->toast("แก้ไขข้อมูลเรียบร้อยแล้ว");	
			// edit
		}		
		echo"<script> document.location.href='".PROJECTPATH."/setting_share_data/coop_share_rule' </script>";            
	}
	
	public function coop_share_rule_change(){
		$data_insert = array();
		$data_insert['setting_value']	= @$_POST["share_cost"];
		
		$id_edit = @$_POST["setting_id"] ;

		$table = "coop_share_setting";
		
		// edit
		$this->db->where('setting_id', $id_edit);
		$this->db->update($table, $data_insert);
		$this->center_function->toast("เปลี่ยนมูลค่าหุ้นเรียบร้อยแล้ว");	
		// edit

		echo"<script> document.location.href='".PROJECTPATH."/setting_share_data/coop_share_rule' </script>";            
	}
	
	function del_coop_share_data(){	
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
	
	public function coop_increase_share(){
		$this->db->select(array('*'));
		$this->db->from('coop_increase_share_setting');
		$this->db->order_by('id DESC');
		$rs = $this->db->get()->result_array();
		$arr_data['row'] = @$rs[0];
		
		$this->libraries->template('setting_share_data/coop_increase_share',$arr_data);
	}
	
	public function coop_increase_share_save(){
		$data_insert = array();		
		
		$data_insert['share_increase']	= @$_POST["share_increase"];
		$data_insert['share_decrease']	= @$_POST["share_decrease"];
		$id_edit = @$_POST["id"] ;
		$table = "coop_increase_share_setting";
		
		if(@$id_edit != ''){
			// edit
			$this->db->where('id', $id_edit);
			$this->db->update($table, $data_insert);
		}else{
			//add
			$this->db->insert($table,$data_insert);
		}			
		$this->center_function->toast("บันทึกข้อมูลเรียบร้อยแล้ว");	
		// edit
		echo"<script> document.location.href='".PROJECTPATH."/setting_share_data/coop_increase_share' </script>"; 
	}
	
	public function coop_refrain_share(){
		$this->db->select(array('*'));
		$this->db->from('coop_refrain_share_setting');
		$this->db->order_by('id DESC');
		$rs = $this->db->get()->result_array();
		$arr_data['row'] = @$rs[0];
		
		$this->libraries->template('setting_share_data/coop_refrain_share',$arr_data);
	}
	
	public function coop_refrain_share_save(){
		$data_insert = array();		
		
		$data_insert['min_share_month']	= @$_POST["min_share_month"];
		$data_insert['max_refrain']	= @$_POST["max_refrain"];
		$id_edit = @$_POST["id"] ;
		$table = "coop_refrain_share_setting";
		
		if(@$id_edit != ''){
			// edit
			$this->db->where('id', $id_edit);
			$this->db->update($table, $data_insert);
		}else{
			//add
			$this->db->insert($table,$data_insert);
		}			
		$this->center_function->toast("บันทึกข้อมูลเรียบร้อยแล้ว");	
		// edit
		echo"<script> document.location.href='".PROJECTPATH."/setting_share_data/coop_refrain_share' </script>"; 
	}
	
	public function coop_share_limit_setting() {
		$arr_data = array();
		if(!empty($_POST)) {
			$mem_types = $this->db->select("mem_type_id")->from("coop_mem_type")->where("mem_type_status = 1")->get()->result_array();

			foreach($mem_types as $mem_type) {
				$data_insert = array();
				$data_insert["mem_type_id"] = $mem_type["mem_type_id"];
				$data_insert["limit_per_time"] = $_POST["limit_per_time"][$mem_type["mem_type_id"]];
				$data_insert["limit_per_year"] = $_POST["limit_per_year"][$mem_type["mem_type_id"]];
				$data_insert["updated_at"] = date('Y-m-d H:i:s');
				$limit = $this->db->select("*")->from("coop_setting_share_limit")->where("mem_type_id = '".$mem_type["mem_type_id"]."'")->get()->row();
				if(!empty($limit)) {
					$this->db->where('id', $limit->id);
					$this->db->update("coop_setting_share_limit", $data_insert);
				} else {
					$data_insert["created_at"] = date('Y-m-d H:i:s');
					$this->db->insert("coop_setting_share_limit",$data_insert);

				}
			}
		}

		$mem_types = $this->db->select("t1.mem_type_id,
										t1.mem_type_name,
										t2.limit_per_time,
										t2.limit_per_year")
								->from("coop_mem_type as t1")
								->join("coop_setting_share_limit as t2", "t1.mem_type_id = t2.mem_type_id", "left")
								->where("t1.mem_type_status = 1")->get()->result_array();
		$arr_data['mem_types'] = $mem_types;
		$this->libraries->template('setting_share_data/coop_share_limit_setting',$arr_data);
	}

	public function coop_share_max_setting() {
		$arr_data = array();
		if(!empty($_POST)) {
			$mem_types = $this->db->select("mem_type_id")->from("coop_mem_type")->where("mem_type_status = 1")->get()->result_array();

			foreach($mem_types as $mem_type) {
				$data_insert = array();
				$data_insert["mem_type_id"] = $mem_type["mem_type_id"];
				$data_insert["max"] = $_POST["max"][$mem_type["mem_type_id"]];
				$data_insert["updated_at"] = date('Y-m-d H:i:s');
				$limit = $this->db->select("*")->from("coop_setting_share_limit")->where("mem_type_id = '".$mem_type["mem_type_id"]."'")->get()->row();
				if(!empty($limit)) {
					$this->db->where('id', $limit->id);
					$this->db->update("coop_setting_share_limit", $data_insert);
				} else {
					$data_insert["created_at"] = date('Y-m-d H:i:s');
					$this->db->insert("coop_setting_share_limit",$data_insert);

				}
			}
		}

		$mem_types = $this->db->select("t1.mem_type_id,
										t1.mem_type_name,
										t2.max")
								->from("coop_mem_type as t1")
								->join("coop_setting_share_limit as t2", "t1.mem_type_id = t2.mem_type_id", "left")
								->where("t1.mem_type_status = 1")->get()->result_array();
		$arr_data['mem_types'] = $mem_types;
		$this->libraries->template('setting_share_data/coop_share_max_setting',$arr_data);
	}
}
