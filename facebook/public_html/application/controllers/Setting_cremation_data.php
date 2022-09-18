<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Setting_cremation_data extends CI_Controller {
	function __construct()
	{
		parent::__construct();
	}
	
	public function cremation_data()
	{
			$x=0;
			$join_arr = array();
			
			$this->paginater_all->type(DB_TYPE);
			$this->paginater_all->select('*');
			$this->paginater_all->main_table('coop_cremation_data');
			$this->paginater_all->where("");
			$this->paginater_all->page_now(@$_GET["page"]);
			$this->paginater_all->per_page(20);
			$this->paginater_all->page_link_limit(20);
			$this->paginater_all->order_by('cremation_id DESC');
			$this->paginater_all->join_arr($join_arr);
			$row = $this->paginater_all->paginater_process();
			//echo"<pre>";print_r($row);exit;
			$paging = $this->pagination_center->paginating($row['page'], $row['num_rows'], $row['per_page'], $row['page_link_limit'], $_GET);//$page_now = 1, $row_total = 1, $per_page = 20, $page_limit = 20
			
			$i = $row['page_start'];

			$arr_data['num_rows'] = $row['num_rows'];
			$arr_data['paging'] = $paging;
			$arr_data['rs'] = $row['data'];
			$arr_data['i'] = $i;
		
		$this->libraries->template('setting_cremation_data/cremation_data',$arr_data);
	}
	function cremation_data_save(){
		//echo"<pre>";print_r($_POST);echo"</pre>";
		$data_insert = array();			
		$data_insert['cremation_name'] = @$_POST["cremation_name"];
		$data_insert['cremation_name_short'] = @$_POST["cremation_name_short"];
		$data_insert['updatetime'] = date('Y-m-d H:i:s');

		$id_edit = @$_POST["cremation_id"] ;
		$table = "coop_cremation_data";
		
		if (empty($id_edit)) {	
		// add		
			$data_insert['createdatetime'] = date('Y-m-d H:i:s');
			$this->db->insert($table, $data_insert);
			$this->center_function->toast("บันทึกข้อมูลเรียบร้อยแล้ว");

		// add
		}else{
		// edit
			$this->db->where('cremation_id', $id_edit);
			$this->db->update($table, $data_insert);	
			$this->center_function->toast("แก้ไขข้อมูลเรียบร้อยแล้ว");

		// edit
		}
		echo"<script> document.location.href='".PROJECTPATH."/setting_cremation_data/cremation_data' </script>"; 
	}
	function del_cremation_data(){
		$this->db->where('cremation_id', $_GET['cremation_id']);
		$this->db->delete('coop_cremation_data');	
		$this->center_function->toast("ลบข้อมูลเรียบร้อยแล้ว");
		echo"<script> document.location.href='".PROJECTPATH."/setting_cremation_data/cremation_data' </script>"; 
	}
	function check_cremation_data_detail(){
		$this->db->select(array('*'));
		$this->db->from('coop_cremation_data_detail');
		$this->db->where("cremation_id = '".$_POST['cremation_id']."'");
		$row = $this->db->get()->result_array();
		if(empty($row)){
			echo "success";
		}else{
			echo "error";
		}
		exit;
	}
	function cremation_data_detail(){
		//Fix to 2 for spkt cremation
		$_GET['cremation_id'] = 2;
		$arr_data = array();
		$this->db->select(array('*'));
		$this->db->from('coop_setting_cremation');
		$this->db->where("cremation_id = '".$_GET['cremation_id']."'");
		$row = $this->db->get()->result_array();
		$arr_data['row'] = $row[0];
		
		$this->db->select(array('*'));
		$this->db->from('coop_setting_cremation_detail');
		$this->db->where("cremation_id = '".$_GET['cremation_id']."'");
		$this->db->order_by('start_date DESC');
		$rs_detail = $this->db->get()->result_array();
		$arr_data['rs_detail'] = $rs_detail;
		
		$this->db->select(array('*'));
		$this->db->from('coop_setting_cremation_detail');
		$this->db->where("cremation_id = '".$_GET['cremation_id']."' AND start_date <= '".date('Y-m-d')."'");
		$this->db->order_by('start_date DESC');
		$this->db->limit(1);
		$rs_status = $this->db->get()->result_array();
		$row_status = $rs_status[0];
		$arr_data['row_status'] = $row_status;
		
		$this->libraries->template('setting_cremation_data/cremation_data_detail',$arr_data);
	}
	function add_cremation_data_detail(){
		$arr_data = array();
		$arr_data['month_arr'] = array('1'=>'มกราคม','2'=>'กุมภาพันธ์','3'=>'มีนาคม','4'=>'เมษายน','5'=>'พฤษภาคม','6'=>'มิถุนายน','7'=>'กรกฎาคม','8'=>'สิงหาคม','9'=>'กันยายน','10'=>'ตุลาคม','11'=>'พฤศจิกายน','12'=>'ธันวาคม');
		$this->db->select(array('*'));
		$this->db->from('coop_cremation_data');
		$this->db->where("cremation_id = '".@$_GET['cremation_id']."'");
		$row = $this->db->get()->result_array();
		$arr_data['row'] = @$row[0];
		
		$this->db->select(array('*'));
		$this->db->from('coop_cremation_data_detail');
		$this->db->where("cremation_detail_id = '".@$_GET['cremation_detail_id']."'");
		$row = $this->db->get()->result_array();
		$arr_data['row_detail'] = @$row[0];
		
		$this->db->select(array('*'));
		$this->db->from('coop_cremation_data_detail_mem_type');
		$this->db->where("cremation_detail_id = '".@$_GET['cremation_detail_id']."'");
		$row = $this->db->get()->result_array();
		if(!empty($row)){
			foreach($row as $key => $value){
				$arr_data['row_mem_type'][$value['member_type_number']] = $value;
			}
		}
		
		$this->db->select(array('*'));
		$this->db->from('coop_cremation_data_detail_maintenance_fee');
		$this->db->where("cremation_detail_id = '".@$_GET['cremation_detail_id']."'");
		$row = $this->db->get()->result_array();
		if(!empty($row)){
			foreach($row as $key => $value){
				$row_maintenance_fee[$value['maintenance_fee_number']] = $value;
			}
		}
		if($arr_data['row_detail']['maintenance_fee_type']=='1'){
			$arr_data['row_maintenance_fee'] = $row_maintenance_fee;
		}else if($arr_data['row_detail']['maintenance_fee_type']=='2'){
			$arr_data['row_maintenance_fee_2'] = $row_maintenance_fee;
		}
		
		$this->db->select(array('*'));
		$this->db->from('coop_mem_type');
		$this->db->where("mem_type_status = '1'");
		$row = $this->db->get()->result_array();
		$arr_data['mem_type'] = @$row;
		
		$this->libraries->template('setting_cremation_data/add_cremation_data_detail',$arr_data);
	}
	function save_cremation_data_detail(){
		$data = $_POST;
		$data['start_date'] = $this->center_function->ConvertToSQLDate($data['start_date']);
		if($data['pay_type']=='1'){
			$data['pay_per_person_stable'] = '';
		}else if($data['pay_type']=='2'){
			$data['pay_per_person'] = '';
		}
		//echo"<pre>";print_r($data);echo"</pre>";exit;
		$data_insert = array();
		$data_insert['start_date'] = $data['start_date'];
		if($data['cremation_detail_id'] == ''){
			
			$data_insert['cremation_id'] = $data['cremation_id'];
			
			$data_insert['createdatetime'] = date('Y-m-d H:i:s');
			$data_insert['maintenance_fee_type'] = $data['maintenance_fee_type'];
			if($data['maintenance_fee_type'] == '1'){
				$data_insert['maintenance_fee'] = $data['maintenance_fee'];
			}else{
				$data_insert['maintenance_fee'] = $data['maintenance_fee_2'];
			}
			$data_insert['pay_type'] = $data['pay_type'];
			$data_insert['pay_per_person'] = $data['pay_per_person'];
			$data_insert['pay_per_person_stable'] = $data['pay_per_person_stable'];
			$data_insert['action_fee_percent'] = $data['action_fee_percent'];
			$this->db->insert('coop_cremation_data_detail', $data_insert);
			
			$cremation_detail_id = $this->db->insert_id();
		}else{
			$data_insert['updatetime'] = date('Y-m-d H:i:s');
			$data_insert['maintenance_fee_type'] = $data['maintenance_fee_type'];
			if($data['maintenance_fee_type'] == '1'){
				$data_insert['maintenance_fee'] = $data['maintenance_fee'];
			}else{
				$data_insert['maintenance_fee'] = $data['maintenance_fee_2'];
			}
			$data_insert['pay_type'] = $data['pay_type'];
			$data_insert['pay_per_person'] = $data['pay_per_person'];
			$data_insert['pay_per_person_stable'] = $data['pay_per_person_stable'];
			$data_insert['action_fee_percent'] = $data['action_fee_percent'];
			$this->db->where('cremation_detail_id', $data['cremation_detail_id']);
			$this->db->update('coop_cremation_data_detail', $data_insert);
			
			$this->db->where('cremation_detail_id', $data['cremation_detail_id']);
			$this->db->delete('coop_cremation_data_detail_mem_type');	
			
			$this->db->where('cremation_detail_id', $data['cremation_detail_id']);
			$this->db->delete('coop_cremation_data_detail_maintenance_fee');
			
			$cremation_detail_id = $data['cremation_detail_id'];
		}
		
		foreach($data['member'] as $key => $value){
			$data_insert = array();
			$data_insert['cremation_detail_id'] = $cremation_detail_id;
			$data_insert['mem_type_id'] = $value['type'];
			$data_insert['age_limit'] = $value['age_limit'];
			$data_insert['member_type_number'] = $key;
			$this->db->insert('coop_cremation_data_detail_mem_type', $data_insert);
		}
		
		if($data['maintenance_fee_type'] == '1'){
				$maintenance_fee_detail = $data['maintenance_fee_detail'];
			}else{
				$maintenance_fee_detail = $data['maintenance_fee_detail_2'];
			}
		
		foreach($maintenance_fee_detail as $key => $value){
			$data_insert = array();
			$data_insert['cremation_detail_id'] = $cremation_detail_id;
			$data_insert['maintenance_fee_detail'] = $value['detail'];
			$data_insert['maintenance_fee_amount'] = $value['amount'];
			if(!empty($value['start_month'])){
				$data_insert['maintenance_fee_start_month'] = $value['start_month'];
			}
			$data_insert['maintenance_fee_number'] = $key;
			$this->db->insert('coop_cremation_data_detail_maintenance_fee', $data_insert);
		}
		$this->center_function->toast("บันทึกข้อมูลเรียบร้อยแล้ว");
		echo"<script> document.location.href='".PROJECTPATH."/setting_cremation_data/cremation_data_detail?cremation_id=".$data['cremation_id']."' </script>"; 
	}
	
	function delete_cremation_data_detail(){
		$this->db->where('cremation_detail_id', $_GET['cremation_detail_id']);
		$this->db->delete('coop_cremation_data_detail');	
		
		$this->db->where('cremation_detail_id', $_GET['cremation_detail_id']);
		$this->db->delete('coop_cremation_data_detail_mem_type');	
		
		$this->db->where('cremation_detail_id', $_GET['cremation_detail_id']);
		$this->db->delete('coop_cremation_data_detail_maintenance_fee');
		
		$this->center_function->toast("ลบข้อมูลเรียบร้อยแล้ว");
		echo"<script> document.location.href='".PROJECTPATH."/setting_cremation_data/cremation_data_detail?cremation_id=".$_GET['cremation_id']."' </script>"; 
	}

    public function cremation_setting(){
        $this->db->select('*')
            ->from('coop_setting_cremation_detail')
            ->where('cremation_detail_id', $_GET['cremation_detail_id']);
		$arr_data['data'] = (array) $this->db->get()->row();

		$arr_data["setting"] = $this->db->select("*")->from("coop_setting_cremation_control")->get()->row();

        $this->libraries->template('setting_cremation_data/cremation_setting', $arr_data);
    }

    public function save_cremation_setting(){
		$details = $this->db->select('*')
							->from('coop_setting_cremation_detail')
							->where('cremation_detail_id' , $_POST['cremation_detail_id'])->get()->result_array();

        $_POST['start_date'] = date('Y-m-d', strtotime(str_replace('/', '-', $_POST['start_date'])." -543 year"));

        if(count($details) > 0 && !empty($_POST['cremation_detail_id'])) {
			//Show n Hide process menu if check finance period type
			$detail = $details[0];
			if($detail->finance_period_type != $_POST['finance_period_type']) {
				if($_POST['finance_period_type'] == 1) {
					$update_data = array();
					$update_data["menu_active"] = 1;
					$this->db->where("order_by = 84");
					$this->db->update("coop_menu", $update_data);

					$update_data = array();
					$update_data["menu_active"] = 0;
					$this->db->where("order_by = 85");
					$this->db->update("coop_menu", $update_data);
				} else {
					$update_data = array();
					$update_data["menu_active"] = 0;
					$this->db->where("order_by = 84");
					$this->db->update("coop_menu", $update_data);

					$update_data = array();
					$update_data["menu_active"] = 1;
					$this->db->where("order_by = 85");
					$this->db->update("coop_menu", $update_data);
				}
			}
			$_POST["dividend_deduction"] = empty($_POST["dividend_deduction"]) ? 0 : 1;

            $this->db->where('cremation_detail_id', $_POST['cremation_detail_id']);
            unset($_POST['cremation_detail_id']);
            $this->db->update('coop_setting_cremation_detail', $_POST);
            $this->center_function->toast("บันทึกข้อมูลเรียบร้อยแล้ว");
            header('location: ' . base_url('setting_cremation_data/cremation_data_detail?cremation_id=2'));
            exit;

        }else{
			if($_POST['finance_period_type'] == 1) {
				$update_data = array();
				$update_data["menu_active"] = 1;
				$this->db->where("order_by = 84");
				$this->db->update("coop_menu", $update_data);

				$update_data = array();
				$update_data["menu_active"] = 0;
				$this->db->where("order_by = 85");
				$this->db->update("coop_menu", $update_data);
			} else {
				$update_data = array();
				$update_data["menu_active"] = 0;
				$this->db->where("order_by = 84");
				$this->db->update("coop_menu", $update_data);

				$update_data = array();
				$update_data["menu_active"] = 1;
				$this->db->where("order_by = 85");
				$this->db->update("coop_menu", $update_data);
			}

            $_POST['createdatetime'] = date('Y-m-d H:i:s');
            $this->db->insert('coop_setting_cremation_detail', $_POST);
            $this->center_function->toast("บันทึกข้อมูลเรียบร้อยแล้ว");
            header('location: ' . base_url('setting_cremation_data/cremation_data_detail?cremation_id=2'));
            exit;

        }
    }

    public function delete_setting_cremation_detail(){
        $this->db->where('cremation_detail_id', $_GET['cremation_detail_id']);
        $this->db->delete('coop_setting_cremation_detail');
        $this->center_function->toast("ลบข้อมูลเรียบร้อยแล้ว");
        echo"<script> document.location.href='".PROJECTPATH."/setting_cremation_data/cremation_data_detail?cremation_id=".$_GET['cremation_id']."' </script>";
    }


}
