<?php
/** @noinspection ALL */
defined('BASEPATH') OR exit('No direct script access allowed');

class Manage_member_share extends CI_Controller {
	public $loan_atm_status = array('0'=>'รออนุมัติ', '1'=>'อนุมัติ', '2'=>'ขอยกเลิก', '3'=>'ยกเลิกสัญญา', '4'=>'ปิดสัญญา','5'=>'ไม่อนุมัติ');
	function __construct()
	{
		parent::__construct();
	}
	public function index()
	{
		$arr_data = array();
		
		$where = '';
		if(@$_GET['member_status']!=''){
			$where .= " AND coop_mem_apply.member_status = '".@$_GET['member_status']."'";
		}
		
		$x=0;
		$join_arr = array();
		$join_arr[$x]['table'] = 'coop_prename';
		$join_arr[$x]['condition'] = 'coop_prename.prename_id = coop_mem_apply.prename_id';
		$join_arr[$x]['type'] = 'left';
		
		$this->paginater_all->type(DB_TYPE);
		//$this->paginater_all->select('*');
		$this->paginater_all->select('
										coop_mem_apply.id,
										coop_mem_apply.mem_apply_id,
										coop_mem_apply.member_id,
										coop_mem_apply.firstname_th,
										coop_mem_apply.lastname_th,
										coop_mem_apply.apply_date,
										coop_mem_apply.member_status,
										coop_prename.prename_id,
										coop_prename.prename_short'
									);
		$this->paginater_all->main_table('coop_mem_apply');
		$this->paginater_all->where("1=1 ".$where);
		$this->paginater_all->page_now(@$_GET["page"]);
		$this->paginater_all->per_page(20);
		$this->paginater_all->page_link_limit(20);
		$this->paginater_all->order_by('coop_mem_apply.mem_apply_id DESC');
		$this->paginater_all->join_arr($join_arr);
		$row = $this->paginater_all->paginater_process();

		$paging = $this->pagination_center->paginating($row['page'], $row['num_rows'], $row['per_page'], $row['page_link_limit'],@$_GET);//$page_now = 1, $row_total = 1, $per_page = 20, $page_limit = 20
		$i = $row['page_start'];


		$arr_data['num_rows'] = $row['num_rows'];
		$arr_data['paging'] = $paging;
		$arr_data['row'] = $row['data'];
		$arr_data['i'] = $i;
		
		$arr_data['member_status'] = array('1'=>'ปกติ','2'=>'ลาออก','3'=>'รออนุมัติ','4'=>'ไม่อนุมัติ');

		$this->libraries->template('manage_member_share/index',$arr_data);
	}

	public function add($id=''){
		$arr_data = array();
		$arr_data['mem_bank_list'] = "[]";
		if($id!='' || (@$_GET['id']!='' && @$_GET['action'] == 'use_prev_data')){
			
			if($id == ''){
				$id = $_GET['id'];
			}
			
			$this->db->select('*');
			$this->db->from('coop_mem_apply');
			$this->db->where("id = '".$id."'");
			$row = $this->db->get()->result_array();
			$arr_data['data'] = $row[0];
			
			$this->db->select('*');
			$this->db->from('coop_mem_register_file');
			$this->db->where("mem_apply_id = '".$row[0]['mem_apply_id']."'");
			$row = $this->db->get()->result_array();
			$arr_data['register_file'] = $row;
			
			
			$member_id = @$arr_data['data']['member_id'];			
			if(@$member_id != ''){
				$this->db->from('coop_mem_gain_detail');
				$this->db->where("member_id = '".$member_id ."'");
				$this->db->join('coop_prename', 'coop_prename.prename_id = coop_mem_gain_detail.g_prename_id', 'left');
				$this->db->join('coop_district', 'coop_district.district_id = coop_mem_gain_detail.g_district_id', 'left');
				$this->db->join('coop_amphur', 'coop_amphur.amphur_id = coop_mem_gain_detail.g_amphur_id', 'left');
				$this->db->join('coop_province', 'coop_province.province_id = coop_mem_gain_detail.g_province_id', 'left');
				$this->db->join('coop_mem_relation', 'coop_mem_relation.relation_id = coop_mem_gain_detail.g_relation_id', 'left');
				$this->db->join('coop_user', 'coop_user.user_id = coop_mem_gain_detail.admin_id', 'left');
				$row_gain_detail = $this->db->get()->result_array();
				
				if(!empty($row_gain_detail)){
					$testament = '<span>กำหนดผู้รับพินัยกรรมแล้ว</span>';
				}else{
					$testament = '<span style="color: red">รอพินัยกรรม</span>';
				}				
				$arr_data['testament'] = $testament;			
				
				
			}			
			
			//ข้อมูลบัญชีธนาคารt
			$this->db->join("coop_bank_branch", "dividend_bank_branch_id = branch_code AND dividend_bank_id = bank_id", "LEFT");
			$arr_data['mem_bank_list'] = $this->db->get_where("coop_mem_bank_account", array("id_apply" => $arr_data['data']['mem_apply_id']) )->result();
			$arr_data['mem_bank_list'] = ($arr_data['mem_bank_list']=="") ? "[]" : json_encode($arr_data['mem_bank_list']);

			//Change History
			if(!empty($member_id)) {
				//Set Change label name
				$labels = array("apply_type_id"=>"ประเภทสมัคร",
								"member_pic"=>"รูปภาพสมาชิก",
								"dividend_acc_num"=>"เลขบัญชีสมาชิก",
								"mem_type"=>"สถานะ",
								"mem_type_id"=>"ประเภทสมาชิก",
								"prename_id"=>"คำนำหน้า",
								"sex"=>"เพศ",
								"firstname_th"=>"ชื่อ (ภาษาไทย)",
								"lastname_th"=>"สกุล (ภาษาไทย)",
								"firstname_en"=>"ชื่อ (English)",
								"lastname_en"=>"สกุล (English)",
								"email"=>"E-mail",
								"tel"=>"เบอร์บ้าน",
								"office_tel"=>"เบอร์ที่ทำงาน",
								"mobile"=>"เบอร์มือถือ",
								"address_no"=>"เลขที่(ที่อยู่ตามทะเบียนบ้าน)",
								"address_moo"=>"หมู่(ที่อยู่ตามทะเบียนบ้าน)",
								"address_village"=>"หมู่บ้าน(ที่อยู่ตามทะเบียนบ้าน)",
								"address_soi"=>"ซอย(ที่อยู่ตามทะเบียนบ้าน)",
								"address_road"=>"ถนน(ที่อยู่ตามทะเบียนบ้าน)",
								"province_id"=>"จังหวัด(ที่อยู่ตามทะเบียนบ้าน)",
								"amphur_id"=>"อำเภอ(ที่อยู่ตามทะเบียนบ้าน)",
								"district_id"=>"ตำบล(ที่อยู่ตามทะเบียนบ้าน)",
								"zipcode"=>"รหัสไปรษณีย์(ที่อยู่ตามทะเบียนบ้าน)",
								"c_address_no"=>"เลขที่(ที่อยู่ปัจจุบัน)",
								"c_address_moo"=>"หมู่(ที่อยู่ปัจจุบัน)",
								"c_address_village"=>"หมู่บ้าน(ที่อยู่ปัจจุบัน)",
								"c_address_soi"=>"ซอย(ที่อยู่ปัจจุบัน)",
								"c_address_road"=>"ถนน(ที่อยู่ปัจจุบัน)",
								"c_province_id"=>"จังหวัด(ที่อยู่ปัจจุบัน)",
								"c_amphur_id"=>"อำเภอ(ที่อยู่ปัจจุบัน)",
								"c_district_id"=>"ตำบล(ที่อยู่ปัจจุบัน)",
								"c_zipcode"=>"รหัสไปรษณีย์(ที่อยู่ปัจจุบัน)",
								"marry_status"=>"สถานะสมรส",
								"nationality"=>"สัญชาติ",
								"birthday"=>"วันเกิด",
								"father_name"=>"ชื่อบิดา",
								"mother_name"=>"ชื่อมารดา",
								"position"=>"ตำแหน่ง(ข้อมูลที่ทำงาน)",
								"department"=>"หน่วยงานหลัก(ข้อมูลที่ทำงาน)",
								"faction"=>"อำเภอ(ข้อมูลที่ทำงาน)",
								"level"=>"หน่วยงานย่อย(ข้อมูลที่ทำงาน)",
								"work_date"=>"วันบรรจุ(ข้อมูลที่ทำงาน)",
								"retry_date"=>"เกษียณ(ข้อมูลที่ทำงาน)",
								"salary"=>"เงินเดือน(ข้อมูลที่ทำงาน)",
								"other_income"=>"เงินอื่นๆ(ข้อมูลที่ทำงาน)",
                                "work_district"=>"แขวง(ข้อมูลที่ทำงาน)",
								"marry_name"=>"ชื่อคู่สมรส",
								"m_id_card"=>"เลขบัตรประชาชน(ข้อมูลคู่สมรส)",
								"m_address_no"=>"เลขที่บ้าน(ข้อมูลคู่สมรส)",
								"m_address_moo"=>"หมู่(ข้อมูลคู่สมรส)",
								"m_address_village"=>"หมู่บ้าน(ข้อมูลคู่สมรส)",
								"m_address_soi"=>"ซอย(ข้อมูลคู่สมรส)",
								"m_address_road"=>"ถนน(ข้อมูลคู่สมรส)",
								"m_province_id"=>"จังหวัด(ข้อมูลคู่สมรส)",
								"m_amphur_id"=>"อำเภอ(ข้อมูลคู่สมรส)",
								"m_district_id"=>"ตำบล(ข้อมูลคู่สมรส)",
								"m_zipcode"=>"รหัสไปรษณีย์(ข้อมูลคู่สมรส)",
								"m_tel"=>"โทรศัพท์(ข้อมูลคู่สมรส)",
								"share_month"=>"ส่งค่าหุ้นรายเดือน"
							);
				$historys = $this->db->select("*")
										->from("coop_member_data_history")
										->join("coop_user", "coop_member_data_history.user_id = coop_user.user_id","left")
										->where("member_id = '".$member_id."'")
										->order_by("created_at desc")
										->get()->result_array();
				//Format Data
				$change_history = array();
				$prev_time = "x";
				foreach($historys as $change) {
					if(!empty($labels[$change["input_name"]]) && (count($change_history) < 10 || ($prev_time == $change["created_at"] && count($change_history) == 10))) {
						$change_history[$change["created_at"]]["created_at"] = $change["created_at"];
						$change_history[$change["created_at"]]["user"] = $change["user_name"];
						//Set Change label name
						$label_arr = array();
						$label_arr["name"] = $labels[$change["input_name"]];
						$label_arr["id"] = $change["id"];
						$change_history[$change["created_at"]]["change_list"][] = $label_arr;
						$prev_time = $change["created_at"];
					}
				}
				$arr_data['change_history'] = $change_history;
			}
		}else{
			$arr_data['data'] = array();
		}
		$arr_data['id'] = $id;

		/*$this->db->select('member_id');
		$this->db->from('coop_mem_apply');
		$this->db->order_by('member_id DESC');
		$this->db->limit(1);
		$mem_id = $this->db->get()->result_array();
		$auto_member_id = $mem_id[0]['member_id'] + 1;
		$arr_data['auto_member_id'] = $auto_member_id;*/

		$this->db->select('apply_type_id, apply_type_name, age_limit');
		$this->db->from('coop_mem_apply_type');
		$row = $this->db->get()->result_array();
		$arr_data['mem_apply_type'] = $row;
		
		$this->db->select('mem_type_id, mem_type_name');
		$this->db->from('coop_mem_type');
		$row = $this->db->get()->result_array();
		$arr_data['mem_type'] = $row;

		$this->db->select('prename_id, prename_full');
		$this->db->from('coop_prename');
		$row = $this->db->get()->result_array();
		$arr_data['prename'] = $row;

		$this->db->select('id, mem_group_name');
		$this->db->from('coop_mem_group');
		$this->db->where("mem_group_type='1'");
		$row = $this->db->get()->result_array();
		$arr_data['department'] = $row;

		if(@$arr_data['data']['department'] != ''){
			$this->db->select('id, mem_group_id, mem_group_name');
			$this->db->from('coop_mem_group');
			$this->db->where("mem_group_parent_id = '".@$arr_data['data']['department']."' AND mem_group_type='2'");
			$row = $this->db->get()->result_array();
			$arr_data['faction'] = $row;
		}else{
			$arr_data['faction'] = array();
		}

		if(@$arr_data['data']['faction'] != '') {
			$this->db->select('id, mem_group_id, mem_group_name');
			$this->db->from('coop_mem_group');
			$this->db->where("mem_group_parent_id = '".@$arr_data['data']['faction']."' AND mem_group_type='3'");
			$row = $this->db->get()->result_array();
			$arr_data['level'] = $row;
		}else{
			$arr_data['level'] = array();
		}

		$this->db->select('user_permission_id');
		$this->db->from('coop_user_permission');
		$this->db->where("user_id = '".$_SESSION['USER_ID']."' AND menu_id = '82'");
		$row = $this->db->get()->result_array();
		if($row[0]['user_permission_id']==''){
			$arr_data['salary_display'] = "display:none;";
		}else{
			$arr_data['salary_display'] = "";
		}

		$this->db->select('act_bank_id, act_bank_name');
		$this->db->from('coop_act_bank');
		$row = $this->db->get()->result_array();
		$arr_data['act_bank'] = $row;

		$this->db->select('bank_id, bank_name');
		$this->db->from('coop_bank');
		$row = $this->db->get()->result_array();
		$arr_data['bank'] = $row;

		$this->db->select('branch_id, branch_name');
		$this->db->from('coop_bank_branch');
		$this->db->where("bank_id = '".@$arr_data['data']["dividend_bank_id"]."'");
		$row = $this->db->get()->result_array();
		$arr_data['bank_branch'] = $row;

		$this->db->select('province_id, province_name');
		$this->db->from('coop_province');
		$this->db->order_by('province_name');
		$row = $this->db->get()->result_array();
		$arr_data['province'] = $row;

		if(@$arr_data['data']["province_id"]!=''){
			$this->db->select('amphur_id, amphur_name');
			$this->db->from('coop_amphur');
			$this->db->where("province_id = '".@$arr_data['data']["province_id"]."'");
			$this->db->order_by('amphur_name');
			$row = $this->db->get()->result_array();
			$arr_data['amphur'] = $row;
		}else{
			$arr_data['amphur'] = array();
		}

		if(@$arr_data['data']["amphur_id"]!=''){
			$this->db->select('district_id, district_name');
			$this->db->from('coop_district');
			$this->db->where("amphur_id = '".@$arr_data['data']["amphur_id"]."'");
			$this->db->order_by('district_name');
			$row = $this->db->get()->result_array();
			$arr_data['district'] = $row;
		}else{
			$arr_data['district'] = array();
		}

		if(@$arr_data['data']["c_province_id"]!=''){
			$this->db->select('amphur_id, amphur_name');
			$this->db->from('coop_amphur');
			$this->db->where("province_id = '".@$arr_data['data']["c_province_id"]."'");
			$this->db->order_by('amphur_name');
			$row = $this->db->get()->result_array();
			$arr_data['c_amphur'] = $row;
		}else{
			$arr_data['c_amphur'] = array();
		}

		if(@$arr_data['data']["c_amphur_id"]!=''){
			$this->db->select('district_id, district_name');
			$this->db->from('coop_district');
			$this->db->where("amphur_id = '".@$arr_data['data']["c_amphur_id"]."'");
			$this->db->order_by('district_name');
			$row = $this->db->get()->result_array();
			$arr_data['c_district'] = $row;
		}else{
			$arr_data['c_district'] = array();
		}

		if(@$arr_data['data']["m_province_id"]!=''){
			$this->db->select('amphur_id, amphur_name');
			$this->db->from('coop_amphur');
			$this->db->where("province_id = '".@$arr_data['data']["m_province_id"]."'");
			$this->db->order_by('amphur_name');
			$row = $this->db->get()->result_array();
			$arr_data['m_amphur'] = $row;
		}else{
			$arr_data['m_amphur'] = array();
		}

		if(@$arr_data['data']["m_amphur_id"]!=''){
			$this->db->select('district_id, district_name');
			$this->db->from('coop_district');
			$this->db->where("amphur_id = '".@$arr_data['data']["m_amphur_id"]."'");
			$this->db->order_by('district_name');
			$row = $this->db->get()->result_array();
			$arr_data['m_district'] = $row;
		}else{
			$arr_data['m_district'] = array();
		}
		
		if(@$arr_data['data']['member_id']!=''){
			$this->db->select('*');
			$this->db->from('coop_maco_account');
			$this->db->where("mem_id = '".@$arr_data['data']['member_id']."' AND account_status = '0'");
			$row = $this->db->get()->result_array();
			$arr_data['coop_account'] = @$row;
		}else{
			$arr_data['coop_account'] = array();
		}
		$arr_data['member_status'] = array('1'=>'ปกติ','2'=>'ลาออก','3'=>'รออนุมัติ','4'=>'ไม่อนุมัติ');
		$arr_data['mem_type_status'] = array('1'=>'ปกติ','2'=>'ลาออก','3'=>'รออนุมัติ','4'=>'ประนอมหนี้','5'=>'เงินประกันความเสี่ยง', '6' => 'ขาดจากสมาชิกภาพ', '7' => 'รอโอนย้าย', '8' => 'รอส่งบำนาญ', '9' => 'ไม่หักไปที่เงินเดือน');
		
		$this->db->select(array('year_quite'));
		$this->db->from('coop_quite_setting');
		$this->db->limit(1);
		$row_quite = $this->db->get()->result_array();
		$year_quite = $row_quite[0]['year_quite'];
		$arr_data['year_quite'] = $year_quite;
		
		//ตำแหน่ง
		$this->db->select(array('*'));
		$this->db->from('coop_mem_position');
		$row_position = $this->db->get()->result_array();
		$arr_data['mem_position'] = @$row_position;
		
		$this->libraries->template('manage_member_share/add',$arr_data);
	}

	function member_lb_upload(){
		$this->load->library('image');
		$this->load->view('manage_member_share/member_lb_upload');
	}

	function get_image(){
		if($_COOKIE["is_upload"]) {
			echo base_url(PROJECTPATH."/assets/uploads/tmp/".$_COOKIE["IMG"]);
		}
		exit();
	}

	function save_add(){
		$data = $this->input->post();
		$member_bank_list = $this->input->post();
		unset($data['dividend_bank_id']);
		unset($data['dividend_bank_branch_id']);
		unset($data['dividend_acc_num']);

		//Prevent member id
		if(!empty($data["mem_apply_id"])) {
			$member_info = $this->db->select("*")->from("coop_mem_apply")->where("mem_apply_id = '".$data["mem_apply_id"]."'")->get()->row();
			if(!empty($member_info->member_id)) {
				$data["member_id"] = $member_info->member_id;
			}
		}

		$process_timestamp = date('Y-m-d H:i:s');

		$output_dir = $_SERVER["DOCUMENT_ROOT"].PROJECTPATH."/assets/uploads/members/";

		$data_historys = array();

		if(!empty($_COOKIE["is_upload"]) && !empty($_COOKIE["IMG"])) {
			$member_pic = $this->create_file_name($output_dir,$_COOKIE["IMG"]);
			@copy($_SERVER["DOCUMENT_ROOT"].PROJECTPATH."/assets/uploads/tmp/{$_COOKIE["IMG"]}", $_SERVER["DOCUMENT_ROOT"].PROJECTPATH."/assets/uploads/members/{$member_pic}");
			@unlink($_SERVER["DOCUMENT_ROOT"].PROJECTPATH."/assets/uploads/tmp/{$_COOKIE["IMG"]}");
			setcookie("is_upload", "", time()-3600);
			setcookie("IMG", "", time()-3600);
			$data['member_pic'] = $member_pic;

			$data_history = array();
			$data_history["member_id"] = $data["member_id"];
			$data_history["input_name"] = "member_pic";
			$data_history["old_value"] = "รูปภาพสมาชิก";
			$data_history["new_value"] = "รูปภาพสมาชิก";
			$data_history["created_at"] = $process_timestamp;
			$data_historys[] = $data_history;
		}else{
			if($data['copy_id']!='' && $data['copy_member_pic'] != ''){
				$member_pic = $this->create_file_name($output_dir,$data['copy_member_pic']);
				@copy($_SERVER["DOCUMENT_ROOT"].PROJECTPATH."/assets/uploads/members/".$data['copy_member_pic'], $_SERVER["DOCUMENT_ROOT"].PROJECTPATH."/assets/uploads/members/".$member_pic);
				$data['member_pic'] = $member_pic;
			}
		}
		unset($data['copy_member_pic']);

		$_tmpfile = $_FILES["signature"];
		if(!empty($_tmpfile["tmp_name"]['name'])) {
			$new_file_name = $this->create_file_name($output_dir,$_tmpfile["name"]);
			if(!empty($new_file_name)) {
				copy($_tmpfile["tmp_name"], $output_dir.$new_file_name);
				$signature = $new_file_name;
				$data['signature'] = $signature;
			}
		}else{
			if($data['copy_id']!='' && $data['copy_signature'] != ''){
				$signature = $this->create_file_name($output_dir,$data['copy_signature']);
				@copy($_SERVER["DOCUMENT_ROOT"].PROJECTPATH."/assets/uploads/members/".$data['copy_signature'], $_SERVER["DOCUMENT_ROOT"].PROJECTPATH."/assets/uploads/members/".$signature);
				$data['signature'] = $signature;
			}
		}
		unset($data['copy_signature']);
		
		
		$register_file = array();
		if(!empty($_FILES["register_file"]['name'][0])){
			foreach($_FILES["register_file"]['name'] as $key => $value){
				$new_file_name = $this->create_file_name($output_dir,$_FILES["register_file"]["name"][$key]);
				if(!empty($new_file_name)) {
					copy($_FILES["register_file"]["tmp_name"][$key], $output_dir.$new_file_name);
					$register_file[] = $new_file_name;
				}
			}
		}
		if($data['copy_id']!=''){
			$this->db->select(array('t2.register_file_name'));
			$this->db->from('coop_mem_apply as t1');
			$this->db->join('coop_mem_register_file as t2','t1.mem_apply_id = t2.mem_apply_id','left');
			$this->db->where("t1.id = '".$data['copy_id']."'");
			$row = $this->db->get()->result_array();
			foreach($row as $key => $value){
				$register_file_name = $this->create_file_name($output_dir,$value['register_file_name']);
				@copy($_SERVER["DOCUMENT_ROOT"].PROJECTPATH."/assets/uploads/members/".$value['register_file_name'], $_SERVER["DOCUMENT_ROOT"].PROJECTPATH."/assets/uploads/members/".$register_file_name);
				$register_file[] = $register_file_name;
			}
		}
		unset($data['copy_id']);
		
		//ตำแหน่ง
		$this->db->select(array('*'));
		$this->db->from('coop_mem_position');
		$this->db->where("position_id = '".$data['position_id']."'");
		$this->db->limit(1);
		$row_position = $this->db->get()->result_array();
		$position = @$row_position[0]['position_name'];
		$data['position'] = @$position;
		
		if($data['mem_apply_id']!=''){
			$this->db->select(array('signature','member_pic','member_id','mem_apply_id'));
			$this->db->from('coop_mem_apply');
			$this->db->where("mem_apply_id = '".$data['mem_apply_id']."'");
			$this->db->order_by('mem_apply_id DESC');
			$this->db->limit(1);
			$row = $this->db->get()->result_array();

			$data['apply_date'] = $this->center_function->ConvertToSQLDate($data['apply_date']);
			//$data['member_date'] = $data['apply_date'];
			if(@$data['member_time']==''){
				$data['member_time'] = '1';
			}
			$data['birthday'] = $this->center_function->ConvertToSQLDate($data['birthday']);
			$data['work_date'] = $this->center_function->ConvertToSQLDate($data['work_date']);
			$data['retry_date'] = $this->center_function->ConvertToSQLDate($data['retry_date']);
			if(@$data['member_pic']!=''){
				@unlink($_SERVER["DOCUMENT_ROOT"].PROJECTPATH."/assets/uploads/members/".$row[0]['member_pic']);
			}
			if(@$data['signature']!=''){
				@unlink($_SERVER["DOCUMENT_ROOT"].PROJECTPATH."/assets/uploads/members/".$row[0]['signature']);
			}

			//Collect change history
			if(!empty($data["member_id"])) {
				$not_compare_input = array("member_date","dividend_bank_id","dividend_acc_num","member_status", "id");
				$member_datas = $this->db->select("*")
										->from("coop_mem_apply")
										->where("member_id = '".$data["member_id"]."'")
										->limit(1)
										->get()->result_array();
				$member_data = $member_datas[0];
				foreach($member_data as $key => $input_val) {
					if(!in_array($key, $not_compare_input) && $input_val != $data[$key] &&
							((!empty($data[$key]) && !empty($input_val)) || (!empty($data[$key]) && $input_val != '0000-00-00') || (!empty($input_val) && $input_val != '0000-00-00' && empty($data[$key])))) {
						$data_history = array();
						$data_history["member_id"] = $data["member_id"];
						$data_history["input_name"] = $key;
						$data_history["old_value"] = $input_val;
						if($key == "birthday" || $key == "work_date" || $key == "retry_date") {
							$data_history["new_value"] = $this->center_function->ConvertToSQLDate($data[$key]);
						} else {
							$data_history["new_value"] = $data[$key];
						}
						$data_history["user_id"] = $_SESSION['USER_ID'];
						$data_history["created_at"] = $process_timestamp;
						$data_historys[] = $data_history;
					}
				}
			}

			$this->db->where('mem_apply_id', $data['mem_apply_id']);
			$this->db->update('coop_mem_apply', $data);
			//$member_id = $row[0]['member_id'];
			$mem_apply_id = $data['mem_apply_id'];
		}else{
			$this->db->select('mem_apply_id');
			$this->db->from('coop_mem_apply');
			$this->db->where("mem_apply_id LIKE '".date("Ym")."%'");
			$this->db->order_by('mem_apply_id DESC');
			$this->db->limit(1);
			$row = $this->db->get()->result_array();
			if(!empty($row)) {
				$id = (int)substr($row[0]["mem_apply_id"], 6);
				$mem_apply_id = date("Ym").sprintf("%06d", $id + 1);
			}else {
				$mem_apply_id = date("Ym")."000001";
			}
			$data['mem_apply_id'] = $mem_apply_id;

			/*if(!isset($data['is_fix_member_id']) || $data['is_fix_member_id'] != '1') {
				$this->db->select('member_id');
				$this->db->from('coop_mem_apply');
				$this->db->order_by('member_id DESC');
				$this->db->limit(1);
				$row = $this->db->get()->result_array();
				if(!empty($row)) {
					$id = (int)$row[0]["member_id"];
					$member_id = sprintf("%06d", $id + 1);
				}else {
					$member_id = "000001";
				}
				$data['member_id'] = $member_id;
				$data['is_fix_member_id'] = '0';
			}*/
			$data['apply_date'] = $this->center_function->ConvertToSQLDate($data['apply_date']);
			//$data['member_date'] = $data['apply_date'];
			if(@$data['member_time']==''){
				$data['member_time'] = '1';
			}
			$data['birthday'] = $this->center_function->ConvertToSQLDate($data['birthday']);
			$data['work_date'] = $this->center_function->ConvertToSQLDate($data['work_date']);
			$data['retry_date'] = $this->center_function->ConvertToSQLDate($data['retry_date']);
			$data['member_status'] = '3';
			$data['mem_type'] = '1';
			$data['apply_status'] = '0';
			$data['is_fix_member_date'] = '0';
			$data['member_id'] = '';
			$this->db->insert('coop_mem_apply', $data);
			//$member_id = $data['member_id'];
			$mem_apply_id = $data['mem_apply_id'];
		}
		foreach($register_file as $key => $value){
			$data_insert = array();
			$data_insert['register_file_name'] = $value;
			$data_insert['mem_apply_id'] = $mem_apply_id;
			$data_insert['register_file_number'] = $key+1;
			$this->db->insert('coop_mem_register_file', $data_insert);
		}
		$this->center_function->toast("บันทึกข้อมูลเรียบร้อยแล้ว");

		//เลขบัญชีธนาคาร
		if(!empty($data["member_id"])) {
			$bank_accounts = $this->db->select("*")
										->from("coop_mem_bank_account")
										->where("member_id = '".$data["member_id"]."'")
										->get()->result_array();
			$old_acc_nums = array_column($bank_accounts, 'dividend_acc_num');
		}
		$this->db->where("id_apply", $mem_apply_id);
		$this->db->delete("coop_mem_bank_account");
		foreach ($member_bank_list['dividend_acc_num'] as $key => $value) {
			$data_insert_bank_list['id_apply'] = $mem_apply_id;
			$data_insert_bank_list['member_id'] = @$this->db->get_where("coop_mem_apply", array("mem_apply_id" => $mem_apply_id))->result()[0]->member_id;
			$data_insert_bank_list['dividend_acc_num'] = $value;
			$data_insert_bank_list['dividend_bank_id'] = $member_bank_list['dividend_bank_id'][$key];
			$data_insert_bank_list['dividend_bank_branch_id'] = $member_bank_list['dividend_bank_branch_id'][$key];
			$this->db->insert("coop_mem_bank_account", $data_insert_bank_list);
			$new_acc_nums[] = $value;
		}
		if(!empty($data["member_id"])) {
			$diff_old = array_diff($old_acc_nums, $new_acc_nums);
			$diff_new = array_diff($new_acc_nums, $old_acc_nums);
			if(!empty($diff_old) || !empty($diff_new)) {
				$data_history = array();
				$data_history["member_id"] = $data["member_id"];
				$data_history["input_name"] = "dividend_acc_num";
				$data_history["old_value"] = json_encode($diff_old);
				$data_history["new_value"] = json_encode($diff_new);
				$data_history["user_id"] = $_SESSION['USER_ID'];
				$data_history["created_at"] = $process_timestamp;
				$data_historys[] = $data_history;
			}
		}
		if(!empty($data_historys)) $this->db->insert_batch("coop_member_data_history", $data_historys);
		//echo"<script> document.location.href='".PROJECTPATH."/manage_member_share' </script>";
		echo"<script> document.location.href='".PROJECTPATH."/manage_member_share/add' </script>";
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

	function get_amphur_list(){
			$arr_data = array();
			$this->db->select('amphur_id, amphur_name');
			$this->db->from('coop_amphur');
			$this->db->where("province_id = '".$this->input->post('province_id')."'");
			$this->db->order_by('amphur_name');
			$row = $this->db->get()->result_array();
			$arr_data['amphur'] = $row;

			$arr_data['id_input_amphur'] = $this->input->post('id_input_amphur');
			$arr_data['district_space'] = $this->input->post('district_space');
			$arr_data['id_input_district'] = $this->input->post('id_input_district');

			$this->load->view('manage_member_share/get_amphur_list',$arr_data);
	}

	function get_district_list(){
		$arr_data = array();
		$this->db->select('district_id, district_name');
		$this->db->from('coop_district');
		$this->db->where("amphur_id = '".$this->input->post('amphur_id')."'");
		$this->db->order_by('district_name');
		$row = $this->db->get()->result_array();
		$arr_data['district'] = $row;

		$arr_data['id_input_district'] = $this->input->post('id_input_district');

		$this->load->view('manage_member_share/get_district_list',$arr_data);
	}

	function get_bank_branch_list(){
		$arr_data = array();
		$this->db->select('branch_id, branch_name');
		$this->db->from('coop_bank_branch');
		$this->db->where("bank_id = '".$this->input->post('bank_id')."'");
		$this->db->order_by('branch_name');
		$row = $this->db->get()->result_array();
		$arr_data['bank_branch'] = $row;

		$this->load->view('manage_member_share/get_bank_branch_list',$arr_data);
	}

	function get_mem_group_list(){
		$arr_data = array();
		$this->db->select('id, mem_group_id, mem_group_name');
		$this->db->from('coop_mem_group');
		$this->db->where("mem_group_parent_id = '".$this->input->post('mem_group_id')."'");
		$this->db->order_by('mem_group_id');
		$row = $this->db->get()->result_array();
		$arr_data['mem_group'] = $row;

		$this->load->view('manage_member_share/get_mem_group_list',$arr_data);
	}

	function check_register(){

		$this->db->select('id_card');
		$this->db->from('coop_mem_apply');
		$this->db->where("id_card = '".$this->input->post('id_card')."'");
		$row = $this->db->get()->result_array();

		if(!empty($row) != ''){
			echo "พบข้อมูลเลขประจำตัวประชาชนของท่านในระบบ";
		}else{
			echo 'success';
		}
		exit;
	}

	function get_search_member(){
		$where = "
		 	(member_id LIKE '%".$this->input->post('search_text')."%'
		 	OR firstname_th LIKE '%".$this->input->post('search_text')."%'
			OR lastname_th LIKE '%".$this->input->post('search_text')."%')
		";
		$this->db->select(array('id','member_id','firstname_th','lastname_th','apply_date','mem_apply_id','member_status'));
		$this->db->from('coop_mem_apply');
		$this->db->where($where);
		$this->db->order_by('mem_apply_id DESC');
		$row = $this->db->get()->result_array();
		$arr_data['data'] = $row;
		$arr_data['form_target'] = $this->input->post('form_target');
		$arr_data['member_status'] = array('1'=>'ปกติ','2'=>'ลาออก','3'=>'รออนุมัติ','4'=>'ไม่อนุมัติ');
		//echo"<pre>";print_r($arr_data['data']);exit;
		$this->load->view('manage_member_share/get_search_member',$arr_data);
	}

	function check_resign_date(){
		$this->db->select(array('year_quite'));
		$this->db->from('coop_quite_setting');
		$row = $this->db->get()->result_array();
		$year_quite = $row[0]['year_quite'];

		$this->db->select('coop_mem_req_resign.resign_date');
		$this->db->from('coop_mem_req_resign');
		$this->db->join('coop_mem_apply', 'coop_mem_req_resign.member_id = coop_mem_apply.member_id', 'inner');
		$this->db->where("coop_mem_apply.id = '".$this->input->post('id')."'");
		$row = $this->db->get()->result_array();
		//echo $this->db->last_query();exit;

		$date_now = date('Y-m-d');
		$resign_date = $row[0]['resign_date'];
		$return_date = date('Y-m-d',strtotime('+'.$year_quite.' year',strtotime($resign_date)));
		$date1=date_create($resign_date);
		$date2=date_create($date_now);
		$diff=date_diff($date1,$date2);

		if($diff->days <= (365*$year_quite)){
			echo 'โดยจะครบ '.$year_quite.' ปี ในวันที่ '.$this->center_function->ConvertToThaiDate($return_date);
		}else{
			echo 'success';
		}
		exit;
	}
	function del_img(){
		//echo"<pre>";print_r($_POST);exit;
		$output_dir = $_SERVER["DOCUMENT_ROOT"].PROJECTPATH."/assets/uploads/members/";
		$this->db->select(array('register_file_name'));
		$this->db->from('coop_mem_register_file');
		$this->db->where("register_file_id = '".$_POST['img_id']."'");
		$row = $this->db->get()->result_array();
		$register_file_name = @$row[0]['register_file_name'];
		
		@unlink($output_dir.$register_file_name);
		
		$this->db->where("register_file_id = '".$_POST['img_id']."'");
		$this->db->delete('coop_mem_register_file');
		exit;
	}
	
	function approve_register(){
		$arr_data = array();
		
		$where = "member_status IN ('3','4')";
		if(!empty($_GET["search_member"])) {
			$search_texts = explode(" ", $_GET["search_member"]);
			$where .= "AND (coop_mem_apply.mem_apply_id like '%".$_GET["search_member"]."%'";
			foreach($search_texts as $text) {
				$where .= " OR coop_mem_apply.firstname_th like '%".$text."%'";
				$where .= " OR coop_mem_apply.lastname_th like '%".$text."%'";
			}
			$where .= ")";
		}

		$x=0;
		$join_arr = array();
		$join_arr[$x]['table'] = 'coop_prename';
		$join_arr[$x]['condition'] = 'coop_prename.prename_id = coop_mem_apply.prename_id';
		$join_arr[$x]['type'] = 'left';
		
		$this->paginater_all->type(DB_TYPE);
		$this->paginater_all->select('*');
		$this->paginater_all->main_table('coop_mem_apply');
		$this->paginater_all->page_now(@$_GET["page"]);
		$this->paginater_all->per_page(20);
		$this->paginater_all->page_link_limit(20);
		$this->paginater_all->order_by('mem_apply_id ASC');
		$this->paginater_all->join_arr($join_arr);
		$this->paginater_all->where($where);
		$row = $this->paginater_all->paginater_process();

		$paging = $this->pagination_center->paginating($row['page'], $row['num_rows'], $row['per_page'], $row['page_link_limit']);//$page_now = 1, $row_total = 1, $per_page = 20, $page_limit = 20
		$i = $row['page_start'];


		$arr_data['num_rows'] = $row['num_rows'];
		$arr_data['paging'] = $paging;
		$arr_data['row'] = $row['data'];
		$arr_data['i'] = $i;
		
		$this->libraries->template('manage_member_share/approve_register',$arr_data);
	}
	
	function approve_register_save(){
		//echo"<pre>";print_r($_POST);exit;
		if(!empty($_POST['checkbox'])){
			foreach($_POST['checkbox'] as $key => $value){
				$data_insert = array();
				$data_insert['member_status'] = $_POST['status_to'];
				if($_POST['status_to']=='1'){

					$member_id = self::generete_member_id($_POST['id']);

					$this->db->select('member_id');
					$this->db->from('coop_mem_apply');
					$this->db->where("member_id = '".$member_id."'");
					$this->db->limit(1);
					$check_member_id = $this->db->get()->result_array();
					//exit;
					if($check_member_id[0]['member_id'] != ''){
						$this->center_function->toastDanger("ไม่สามารถบันทึกข้อมูลได้");
						echo"<script> document.location.href='".PROJECTPATH."/manage_member_share/approve_register' </script>";
						exit;
					}
					//echo 'member_id='.$member_id.'<br>';
					//exit;
					$data_insert['member_id'] = $member_id;
					$data_insert['member_date'] = date('Y-m-d');
					$data_insert['loan_atm_account_id'] = "002".sprintf("%08d", $member_id);
				}

				$this->db->where('id',$value);
				$this->db->update('coop_mem_apply', $data_insert);

//				if($_POST['status_to']=='1'){
//
//					$this->auto_create_coop_account($member_id);
//					$mem_apply_id = $this->db->get_where("coop_mem_apply", array( "id" => $value) )->result()[0]->mem_apply_id;
//					$this->db->set("member_id", $member_id);
//					$this->db->where("id_apply", $mem_apply_id);
//					$this->db->update("coop_mem_bank_account");
//				}
			}
		}
		$this->center_function->toast("บันทึกข้อมูลเรียบร้อยแล้ว");
		echo"<script> document.location.href='".PROJECTPATH."/manage_member_share/approve_register' </script>";
	}
	
	function tenpower($n){
		if($n <= 0){
			return 1;
		}else{
			return 10*$this->tenpower($n-1);
		} 
	}
	
	function random_atm_number(){
		$numofdigits = 16;
		$all_amount_of_number = $this->tenpower($numofdigits);
		$min = $all_amount_of_number/10;
		$max = $all_amount_of_number -1;
		$atm_number = rand($min,$max);
		return $this->check_atm_number($atm_number);
	}
	
	function check_atm_number($atm_number){
		$this->db->select('atm_number');
		$this->db->from('coop_atm_card');
		$this->db->where("atm_number = '".$atm_number."'");
		$row = $this->db->get()->result_array();
		if(!empty($row)){
			random_atm_number();
		}else{
			return $atm_number;
		}
	}
	
	function auto_create_coop_account($member_id){
		$this->db->select(array('coop_maco_account.account_id','coop_maco_account.mem_id','coop_deposit_type_setting.type_name'));
		$this->db->from('coop_maco_account');
		$this->db->join("coop_deposit_type_setting","coop_maco_account.type_id = coop_deposit_type_setting.type_id","inner");
		$this->db->where("
			coop_maco_account.mem_id = '".$member_id."' 
			AND coop_maco_account.account_status = '0'
			AND coop_deposit_type_setting.auto_create = '1'
		");
		$rs_account = $this->db->get()->result_array();
		$row_account = @$rs_account[0];
		if(empty($rs_account)){
			$this->db->select(array('type_id','type_name'));
			$this->db->from('coop_deposit_type_setting');
			$this->db->where("auto_create = '1'");
			$rs_auto_create = $this->db->get()->result_array();
			//$row_deduct_loan = @$rs_deduct_loan[0];
			//echo '<pre>'; print_r($row_deduct_loan); echo '</pre>'; 
			foreach($rs_auto_create as $key => $row_auto_create ){
				$account_id = '';
				$this->db->select(array('type_code','type_id'));
				$this->db->from('coop_deposit_type_setting');
				$this->db->where("type_id = '".$row_auto_create['type_id']."'");
				$row = $this->db->get()->result_array();
				$type_code = $row[0];
				
				$this->db->select('account_id');
				$this->db->from('coop_maco_account');
				$this->db->where("type_id = '".$type_code['type_id']."'");
				$this->db->order_by("account_id DESC");
				$this->db->limit(1);
				$row = $this->db->get()->result_array();
				if(!empty($row)) {
					$old_account_id = str_replace("001".$type_code['type_code'],'',$row[0]["account_id"]);
					$old_account_id = (int)$old_account_id;
					$account_id = sprintf("%06d", $old_account_id + 1);
					$account_id = "001".$type_code['type_code'].$account_id;
				}else {
					$account_id = "001".$type_code['type_code']."000001";
				}
				//เปิดอัตโนมัติ แค่ สมัครใหม่(สามัญ)
				$this->db->select('*');
				$this->db->from('coop_mem_apply');
				$this->db->where("member_id = '".$member_id."' AND apply_type_id in (1,3,4,6)");
				$rs_member = $this->db->get()->result_array();
				$row_member = @$rs_member[0];
				if(!empty($row_member)){
					$data_insert = array();
					$data_insert['account_id'] = @$account_id ;
					$data_insert['mem_id'] = $member_id;
					$data_insert['member_name'] = $row_member['firstname_th']." ".$row_member['lastname_th'];
					$data_insert['account_name'] = $row_member['firstname_th']." ".$row_member['lastname_th'];
					$data_insert['account_name_eng'] = $row_member['firstname_en']." ".$row_member['lastname_en'];
					$data_insert['created'] = date('Y-m-d H:i:s');
					$data_insert['account_amount'] = '0';
					$data_insert['book_number'] = '1';
					$data_insert['type_id'] = $type_code['type_id'];
					//$data_insert['atm_number'] = $atm_number;
					$data_insert['account_status'] = '0';
					$this->db->insert('coop_maco_account', $data_insert);
					//$account_id = $this->db->insert_id();
				}
			}
		}
	}
	
	function check_salary_and_share(){
		$salary = @$_POST['salary'];
		$other_income = @$_POST['other_income'];
		$salary_rule = @$_POST['salary'] + @$_POST['other_income'];
		
		$this->db->select(array('share_salary'));
		$this->db->from('coop_share_rule');
		$this->db->where("mem_type_id = '".$_POST['mem_type_id']."' AND salary_rule <= '".$salary_rule."'");
		$this->db->order_by('salary_rule DESC');
		$this->db->limit(1);
		$row = $this->db->get()->result_array();
		
		$this->db->select(array('setting_value'));
		$this->db->from('coop_share_setting');
		$this->db->limit(1);
		$row_setting_value = $this->db->get()->result_array();
		
		echo @$row[0]['share_salary']*@$row_setting_value[0]['setting_value'];
		exit;
	}
	
	function search_from_id_card(){
		$this->db->select(array('year_quite'));
		$this->db->from('coop_quite_setting');
		$this->db->limit(1);
		$row_quite = $this->db->get()->result_array();
		$year_quite = $row_quite[0]['year_quite'];
		
		$date_quite = date('Y-m-d',strtotime('- '.$year_quite.' year'));
		
		$this->db->select(array('*'));
		$this->db->from('coop_mem_apply');
		$this->db->where("firstname_th LIKE '{$_POST['firstname_th']}' AND lastname_th LIKE '{$_POST['lastname_th']}' OR id_card = '".$_POST['id_card']."'");
		$this->db->order_by("member_date DESC");
		$row = $this->db->get()->result_array();
		$data = '';
		$still_member = 0;
		$can_not_register = 0;
		foreach($row as $key => $value){
			$this->db->select(array('*'));
			$this->db->from('coop_mem_req_resign');
			$this->db->where("member_id = '".$value['member_id']."' AND req_resign_status = '1'");
			$row_resign = $this->db->get()->result_array();
			if(sizeof($row_resign)) {
                $row_resign = @$row_resign[0];
                if (empty($row_resign)) {
                    $still_member++;
                } else if ($date_quite < @$row_resign['resign_date']) {
                    $can_not_register++;
                }
                $data .= "<tr>";
                $data .= "<td align='center'>" . $value['mem_apply_id'] . "</td>";
                $data .= "<td align='center'>" . $value['member_id'] . "</td>";
                $data .= "<td align='center'>" . $value['id_card'] . "</td>";
                $data .= "<td>" . $value['firstname_th'] . " " . $value['lastname_th'] . "</td>";
                //$data .= "<td align='center'>".$this->center_function->ConvertToThaiDate($value['apply_date'],1,0)."</td>";
                $data .= "<td align='center'>" . $this->center_function->ConvertToThaiDate($value['member_date'], 1, 0) . "</td>";
                $data .= "<td align='center'>" . $this->center_function->ConvertToThaiDate(@$row_resign['resign_date'], 1, 0) . "</td>";
                $data .= "<td align='center'><button class='btn btn-primary btn_idcard' onclick=\"use_data('" . $value['id'] . "')\">เลือกใช้ข้อมูล</button></td>";
                $data .= "</tr>";
            }else{
                $can_not_register = 0;
            }
		}
		if(!empty($row)){
			if($still_member > 0){
				$result = 'still_member';
			}else if($can_not_register > 0){
				$result = 'can_not_register';
			}else{
				$result = $data;
			}
		}else{
			$result = 'empty';
		}
		echo $result;
		
	}
	function del_member(){
		//echo"<pre>";print_r($_POST);exit;
		$output_dir = $_SERVER["DOCUMENT_ROOT"].PROJECTPATH."/assets/uploads/members/";
		foreach($_POST['del_member'] as $key => $value){
			$this->db->select(array('*'));
			$this->db->from('coop_mem_apply');
			$this->db->where("id = '".$value."'");
			$row = $this->db->get()->result_array();
			$row = $row[0];
			
			@unlink($output_dir.$row['member_pic']);
			@unlink($output_dir.$row['signature']);
			
			$this->db->select(array('register_file_name'));
			$this->db->from('coop_mem_register_file');
			$this->db->where("mem_apply_id = '".$row['mem_apply_id']."'");
			$row_register_file = $this->db->get()->result_array();
			foreach($row_register_file as $key2 => $value2){
				@unlink($output_dir.$value2['register_file_name']);
			}
			
			$this->db->where("mem_apply_id = '".$row['mem_apply_id']."'");
			$this->db->delete('coop_mem_register_file');
			
			$this->db->where("id = '".$value."'");
			$this->db->delete('coop_mem_apply');
		}
		echo"<script> document.location.href='".PROJECTPATH."/manage_member_share' </script>";
		exit;
	}
	
	function member_loan(){
		
		$arr_data = array();		
		if($this->input->get('member_id')!=''){
			$member_id = $this->input->get('member_id');
		}else{
			$member_id = '';
		}
		$arr_data = array();
		$arr_data['member_id'] = $member_id;

		$this->db->select('*');
		$this->db->from('coop_share_setting');
		$this->db->order_by('setting_id DESC');
		$row = $this->db->get()->result_array();
		$arr_data['share_value'] = $row[0]['setting_value'];

		if($member_id != '') {			
			$this->db->select(array('t1.*',
							't2.mem_group_name AS department_name',
							't3.mem_group_name AS faction_name',
							't4.mem_group_name AS level_name',
							't5.prename_short'));
			$this->db->from('coop_mem_apply as t1');			
			$this->db->join("coop_mem_group AS t2","t1.department = t2.id","left");
			$this->db->join("coop_mem_group AS t3","t1.faction = t3.id","left");
			$this->db->join("coop_mem_group AS t4","t1.level = t4.id","left");
			$this->db->join("coop_prename AS t5","t1.prename_id = t5.prename_id","left");
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
			
			//ประเภทสมาชิก
			$this->db->select('mem_type_id, mem_type_name');
			$this->db->from('coop_mem_type');
			$rs_mem_type = $this->db->get()->result_array();
			$mem_type_list = array();
			foreach($rs_mem_type AS $key=>$row_mem_type){
				$mem_type_list[$row_mem_type['mem_type_id']] = $row_mem_type['mem_type_name'];
			}
			
			$arr_data['mem_type_list'] = $mem_type_list;
			
			$this->db->select(array('share_collect','share_collect_value'));
			$this->db->from('coop_mem_share');
			$this->db->where("member_id = '".$member_id."' AND share_status IN('1','2','5')");
			$this->db->order_by('share_date DESC,share_id DESC');
			$this->db->limit(1);
			$row_prev_share = $this->db->get()->result_array();
			$row_prev_share = @$row_prev_share[0];
			
			$arr_data['count_share'] = $row_prev_share['share_collect'];
			$arr_data['cal_share'] = $row_prev_share['share_collect_value'];
			
			
			$this->db->select('share_period');
			$this->db->from('coop_mem_share');
			$this->db->where("member_id = '".$member_id."' AND share_status IN('1','2') AND share_period IS NOT NULL");
			$this->db->order_by('share_date DESC,share_id DESC');
			$this->db->limit(1);
			$row_share_month = $this->db->get()->result_array();
			$row_share_month = @$row_share_month[0];
			$arr_data['share_period'] = @$row_share_month['share_period'];
			
			//รายการซื้อหุ้น
			$this->db->select(array('coop_mem_share.*','coop_user.user_name','coop_user_cancel.user_name AS user_name_cancel'));
			$this->db->from('coop_mem_share');
			$this->db->join("coop_user","coop_mem_share.admin_id = coop_user.user_id","left");
			$this->db->join("coop_receipt","coop_receipt.receipt_id = coop_mem_share.share_bill","left");
			$this->db->join("coop_user AS coop_user_cancel","coop_receipt.cancel_by = coop_user_cancel.user_id","left");
			$this->db->where("coop_mem_share.member_id = '".$member_id."' AND coop_mem_share.share_status IN('1', '3', '5') ");
			$this->db->order_by('coop_mem_share.share_date ASC,coop_mem_share.share_id ASC');
			$rs_mem_share = $this->db->get()->result_array();
			//echo $this->db->last_query(); exit;
			$share_tran_data = array();
			foreach($rs_mem_share as $raw) {

				if($raw['share_status'] == '1' || $raw['share_status'] == '5') {
					$tran = array();
					$tran['share_date'] = $raw['share_date'];
					$tran['share_type'] = $raw['share_type'];
					$tran['share_early_value'] = $raw['share_early_value'];
					$tran['share_collect_value'] = $raw['share_collect_value'];
					$tran['share_collect'] = $raw['share_collect'];
					$tran['share_bill'] = $raw['share_bill'];
					$tran['share_type'] = $raw['share_type'];
					$tran['user_name'] = $raw['user_name'];
					$share_tran_data[] = $tran;
				} else if ($raw['share_status'] == '3') {
					//Add month deduct
					$tran = array();
					$tran['share_date'] = $raw['share_date'];
					$tran['share_type'] = $raw['share_type'];
					$tran['share_early_value'] = $raw['share_early_value'];
					// $tran['share_collect_value'] = $raw['share_collect_value'] + $raw['share_early_value'];
					// $tran['share_collect'] = $raw['share_collect'] + $raw['share_early'];
					$tran['share_collect_value'] = $raw['share_collect_value'];
					$tran['share_collect'] = $raw['share_collect'];
					$tran['share_bill'] = $raw['share_bill'];
					$tran['share_type'] = $raw['share_type'];
					$tran['user_name'] = $raw['user_name'];
					$share_tran_data[] = $tran;

					//Add non pay
					$month = date('m',strtotime($raw['share_date']));
					$year = date('Y',strtotime($raw['share_date'])) + 543;
					$non_pay = $this->db->select("t2.non_pay_amount")
										->from("coop_non_pay as t1")
										->join("coop_non_pay_detail as t2", "t1.non_pay_id = t2.non_pay_id AND t2.deduct_code = 'SHARE'", "inner")
										->where("t1.non_pay_month = '".(int)$month."' AND t1.non_pay_year = '".$year."' AND t1.member_id = '".$raw['member_id']."'")
										->get()->row();
					if(@$_GET['dev']=='dev'){
					echo '<hr>';	
					echo $this->db->last_query();
					}					
					if(!empty($non_pay)) {
						$tran = array();
						$tran['share_date'] = $raw['share_date'];
						$tran['share_type'] = "ERR";
						//$tran['share_early_value'] = "-".$non_pay->non_pay_amount;
						$tran['share_early_value'] = "-".$raw['share_early_value'];
						$tran['share_collect_value'] = $raw['share_collect_value'] - $raw['share_early_value'];
						$tran['share_collect'] = $raw['share_collect'] - $raw['share_early'];
						$tran['share_bill'] = $raw['share_bill'];
						$tran['user_name'] = (@$raw['user_name_cancel'] != '')?@$raw['user_name_cancel']:@$raw['user_name'];
						$share_tran_data[] = $tran;
					} else {
						$tran = array();
						$tran['share_date'] = $raw['share_date'];
						$tran['share_type'] = "ERR";
						$tran['share_early_value'] = "-".$raw['share_early_value'];
						$tran['share_collect_value'] = $raw['share_collect_value'] - $raw['share_early_value'];
						$tran['share_collect'] = $raw['share_collect'] - $raw['share_early'];
						$tran['share_bill'] = $raw['share_bill'];
						$tran['user_name'] = (@$raw['user_name_cancel'] != '')?@$raw['user_name_cancel']:@$raw['user_name'];
						$share_tran_data[] = $tran;
					}
					
				}
			}

			$arr_data['rs_mem_share'] = $share_tran_data;
			
			$this->db->select('*');
			$this->db->from('coop_maco_account');
			$this->db->where("mem_id = '".$member_id."' AND account_status = '0'");
			$rs_account = $this->db->get()->result_array();
			$count_account = 0;
			$cal_account = 0;
			foreach($rs_account as $key => $row_account){
				$this->db->select('*');
				$this->db->from('coop_account_transaction');
				$this->db->where("account_id = '".$row_account['account_id']."'");
				$this->db->order_by('transaction_time DESC, transaction_id DESC');
				$this->db->limit(1);
				$row_account_trans = $this->db->get()->result_array();
				
				$cal_account += @$row_account_trans[0]['transaction_balance'];
				$count_account++;
				
				$rs_account[$key]['transaction_balance'] = @$row_account_trans[0]['transaction_balance'];
			}
			$arr_data['data_account'] = $rs_account;
			$arr_data['count_account'] = $count_account;
			$arr_data['cal_account'] = $cal_account;
			
			$this->db->select(array(
				't2.id',
				't2.petition_number',
				't2.contract_number',
				't2.member_id',
				't2.loan_type',
				't3.firstname_th',
				't3.lastname_th',
				't2.loan_amount',
				't2.loan_amount_balance',
				't8.loan_name as loan_type_detail',
				't8.loan_name_description'
			));
			$this->db->from('coop_loan_guarantee_person as t1');
			$this->db->join('coop_loan as t2','t1.loan_id = t2.id','inner');
			$this->db->join('coop_mem_apply as t3','t2.member_id = t3.member_id','inner');
			$this->db->join('coop_loan_name as t8','t2.loan_type = t8.loan_name_id','inner');
			$this->db->where("t1.guarantee_person_id = '".$member_id."' AND t2.loan_status IN('1','2') AND t2.loan_amount_balance > 0");
			$rs_guarantee = $this->db->get()->result_array();
			
			$arr_data['count_contract'] = 0;
			$arr_data['sum_guarantee_balance'] = 0;
			$arr_data['rs_guarantee'] = @$rs_guarantee;

			foreach($rs_guarantee as $key => $row_count_guarantee){
				@$arr_data['sum_guarantee_balance'] += $row_count_guarantee['loan_amount_balance'];
				$arr_data['count_contract']++;
			}
			
			$this->db->select(array('loan_amount_balance'));
			$this->db->from('coop_loan as t1');
			$this->db->where("t1.member_id = '".$member_id."' AND t1.loan_status IN('1','2')");
			$rs_count_loan = $this->db->get()->result_array();
			
			$arr_data['count_loan'] = 0;
			$arr_data['sum_loan_balance'] = 0;
			foreach($rs_count_loan as $key => $row_count_loan){
				$arr_data['sum_loan_balance'] += $row_count_loan['loan_amount_balance'];
				$arr_data['count_loan']++;
			}

			//Get debt interest
			$interest_debt_balance = $this->db->select("SUM(interest_balance) as sum")->from("coop_loan_interest_debt")->where("interest_status = 0 AND member_id = '".$member_id."'")->get()->row();
			$arr_data['interest_debt_balance'] = $interest_debt_balance->sum;

			//Get Compromise other debt
			$other_debt = $this->db->select("SUM(other_debt_blance) as sum")->from("coop_loan_guarantee_compromise")->where("member_id = '".$member_id."' AND status = 1")->get()->row();
			$arr_data['other_debt'] = $other_debt->sum;

			//หา profile ประมวลผล ล่าสุด
			$this->db->select(array('*'));
			$this->db->from('coop_finance_month_profile');
			$this->db->order_by("profile_year,profile_month DESC");
			$this->db->limit(1);
			$rs_month_profile = $this->db->get()->result_array();
			$month_profile = @$rs_month_profile[0];
			
			$this->db->select(array(
				't1.pay_type',
				't1.approve_date',
				't1.deduct_status',
				't1.createdatetime',
				't1.contract_number',
				't1.date_start_period',
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
				't4.date_transfer',
				't5.petition_file',
				't1.period_amount',
				't1.money_per_period',
				't6.type as compromise_type',
				't1.period_now'
			));
			$this->db->from('coop_loan as t1');
			$this->db->join('coop_loan_name as t3','t1.loan_type = t3.loan_name_id','inner');
			$this->db->join('coop_loan_type as t5','t3.loan_type_id = t5.id','inner');
			$this->db->join('coop_user as t2','t1.admin_id = t2.user_id','left');
			$this->db->join('coop_loan_transfer as t4',"t1.id = t4.loan_id AND t4.transfer_status != '2'",'left');
			$this->db->join('coop_loan_guarantee_compromise as t6', "t1.id = t6.loan_id AND t1.member_id = t6.member_id", "left");
			$this->db->where("t1.member_id = '".$member_id."' AND t1.loan_status = '1' ");
			$this->db->order_by("t1.id DESC");
			$rs_loan = $this->db->get()->result_array();
			$arr_loan = array();
			if(@$_GET['dev']=='on'){
			echo $this->db->last_query();
			}
			foreach($rs_loan AS $key=>$row_loan){
				$arr_loan[$key] = @$row_loan;
				
				$this->db->select(array('*'));
				$this->db->from('coop_loan_period');
				$this->db->where("loan_id='".@$row_loan['id']."' AND date_count = '31'");
				$this->db->limit(1);
				$per_month = $this->db->get()->result_array();
				//echo $this->db->last_query(); exit; 
				if(@$row_loan['pay_type'] == '1'){
					//$total_paid_per_month = @round(@$per_month[0]['principal_payment'],-2);
					$total_paid_per_month = @$per_month[0]['principal_payment'];
				}else{
					//$total_paid_per_month = @round(@$per_month[0]['total_paid_per_month'],-2);
					$total_paid_per_month = @$per_month[0]['total_paid_per_month'];
				}
				$arr_loan[$key]['total_paid_per_month'] = @$total_paid_per_month ;//ชำระต่องวด

				if($arr_loan[$key]['total_paid_per_month']==0)//แกเบัค coop_loan_period วันที่ไม่ถูกต้อง
					$arr_loan[$key]['total_paid_per_month'] = $row_loan['money_per_period'];
				

				
				$this->db->select(array('createdatetime','period_count'));
				$this->db->from('coop_finance_transaction');
				$this->db->where("loan_id='".@$row_loan['id']."'");
				$this->db->order_by("period_count DESC");
				$this->db->limit(1);
				$rs_finance_transaction = $this->db->get()->result_array();
				$row_finance_transaction = @$rs_finance_transaction[0];
				$arr_loan[$key]['date_transaction'] = @$row_finance_transaction['createdatetime'] ;//วันที่ชำระล่าสุด
				$arr_loan[$key]['period_count'] = (@$row_finance_transaction['period_count'] != '')?@$row_finance_transaction['period_count']:@$row_loan['period_now'];//งวดที่
			
				//ยอดเงินหลังประมวลผล
				//month_profile
				$this->db->select(array('t1.pay_amount','t2.profile_month','t2.profile_year'));
				$this->db->from('coop_finance_month_detail AS t1');
				$this->db->join("coop_finance_month_profile AS t2","t1.profile_id = t2.profile_id","left");
				$this->db->where("t1.loan_id = '".@$row_loan['id']."' AND t1.pay_type = 'principal' AND t1.deduct_code = 'LOAN' AND t1.run_status = '0'");
				$this->db->limit(1);
				$rs_finance_process = $this->db->get()->result_array();
				$row_finance_process = @$rs_finance_process[0];
				$arr_loan[$key]['process_balance'] = @$row_loan['loan_amount_balance'] - @$row_finance_process['pay_amount'];

			}
			//echo '<pre>'; print_r($arr_loan); echo '</pre>'; exit;
			$arr_data['rs_loan'] = $arr_loan;
			$arr_data['pay_type_name'] = array('1'=>'คงต้น','2'=>'คงยอด');
			//echo $this->db->last_query();exit;
			
			$this->db->select(array('*'));
			$this->db->from('coop_mem_gain_detail');
			$this->db->where("member_id = '".$member_id ."'");
			$this->db->join('coop_prename', 'coop_prename.prename_id = coop_mem_gain_detail.g_prename_id', 'left');
			$this->db->join('coop_district', 'coop_district.district_id = coop_mem_gain_detail.g_district_id', 'left');
			$this->db->join('coop_amphur', 'coop_amphur.amphur_id = coop_mem_gain_detail.g_amphur_id', 'left');
			$this->db->join('coop_province', 'coop_province.province_id = coop_mem_gain_detail.g_province_id', 'left');
			$this->db->join('coop_mem_relation', 'coop_mem_relation.relation_id = coop_mem_gain_detail.g_relation_id', 'left');
			$this->db->join('coop_user', 'coop_user.user_id = coop_mem_gain_detail.admin_id', 'left');
			$row_gain_detail = $this->db->get()->result_array();
			
			if(!empty($row_gain_detail)){
				$testament = 'กำหนดผู้รับพินัยกรรมแล้ว';
				$style_testament = '';
			}else{
				$testament = 'รอพินัยกรรม';
				$style_testament = 'style="color: red"';
			}				
			$arr_data['testament'] = $testament;				
			$arr_data['style_testament'] = $style_testament;

			//การกู้เงินฉุกเฉิน ATM
			$this->db->select(array('*'));
			$this->db->from('coop_loan_atm');
			//$this->db->where("member_id = '".$member_id."' AND loan_atm_status = '1'");
			$this->db->where("member_id = '".$member_id."'");
			$this->db->order_by("loan_atm_id DESC");
			$row_atm = $this->db->get()->result_array();
			$arr_data['row_loan_atm_all'] = @$row_atm;	
			$arr_data['loan_atm_status'] = $this->loan_atm_status;

            //ปันผลเฉลี่ยคืน
            $this->db->select(array("`m`.`year`",
                    "round((`n`.`average_return_value` + `n`.`dividend_value` + `n`.`gift_varchar` ), 2)AS `average_return`")
            );
            $this->db->from('`coop_dividend_average_master` `m`');
            $this->db->join('`coop_dividend_average` `n`', '`m`.`id` = `n`.`master_id`', 'inner');
            $this->db->where(array('member_id' => $member_id, 'status' => 1));

            $arr_data['dividend_average'] = $this->db->get()->result_array();

			//Get debt interest
			$interest_debt_balance = $this->db->select("SUM(interest_balance) as sum")->from("coop_loan_interest_debt")->where("interest_status = 0 AND member_id = '".$member_id."'")->get()->row();
			$arr_data['interest_debt_balance'] = $interest_debt_balance->sum;

			//Get Compromise other debt
			$other_debt = $this->db->select("SUM(other_debt_blance) as sum")->from("coop_loan_guarantee_compromise")->where("member_id = '".$member_id."' AND status = 1")->get()->row();
			$arr_data['other_debt'] = $other_debt->sum;

			$note = $this->db->select("*")->from("coop_mem_spccomm")->where("member_id = '".$member_id."'")->order_by("start_date DESC")->get()->row_array();
			$arr_data['note'] = $note['note'];
        }
		
		$this->db->select(array(
			'*'
		));
		$this->db->from('coop_term_of_loan');
		$this->db->where("start_date <= '".date('Y-m-d')."'");
		$this->db->order_by("start_date ASC");
		$rs_rule = $this->db->get()->result_array();
		foreach($rs_rule as $key => $value){
			$arr_data['rs_rule'][$value['type_id']] = $value;
		}
		$this->db->select(array(
			'loan_reason_id','loan_reason'
		));
		$this->db->from('coop_loan_reason');
		$rs_loan_reason = $this->db->get()->result_array();
		$arr_data['rs_loan_reason'] = $rs_loan_reason;
		
		$this->db->select(array(
			'id','loan_type'
		));
		$this->db->from('coop_loan_type');
		$rs_loan_type = $this->db->get()->result_array();
		$arr_data['rs_loan_type'] = $rs_loan_type;
		
		$this->db->select(array(
			'loan_deduct_list_code','loan_deduct_list'
		));
		$this->db->from('coop_loan_deduct_list');
		$rs_loan_deduct_list = $this->db->get()->result_array();
		$loan_deduct_list_odd = array();
		$loan_deduct_list_even = array();
		$i=1;
		foreach($rs_loan_deduct_list as $key => $value){
			if(in_array($value['loan_deduct_list_code'],array('deduct_share','deduct_blue_deposit','deduct_pay_prev_loan'))){
				$readonly='readonly';
			}else{
				$readonly='';
			}
			$value['readonly'] = $readonly;
			if($i==1){
				$loan_deduct_list_odd[] = $value;
				$i++;
			}else{
				$loan_deduct_list_even[] = $value;
				$i = 1;
			}
		}
		//echo"<pre>";print_r($loan_deduct_list_odd);print_r($loan_deduct_list_even);exit;
		$arr_data['loan_deduct_list'] = $rs_loan_deduct_list;
		$arr_data['loan_deduct_list_odd'] = $loan_deduct_list_odd;
		$arr_data['loan_deduct_list_even'] = $loan_deduct_list_even;
		
		//ประวัติการผิดนัดชำระ	
		$this->db->select('coop_non_pay.non_pay_month
							,coop_non_pay.non_pay_year
							,coop_non_pay.non_pay_status
							,coop_non_pay.member_id
							,coop_non_pay_detail.loan_id
							,coop_non_pay_detail.deduct_code
							,coop_non_pay.non_pay_id
							,coop_finance_month_detail.profile_id							
							');
		$this->db->from('coop_non_pay');
		$this->db->join("coop_finance_month_profile","coop_non_pay.non_pay_month = coop_finance_month_profile.profile_month
							AND coop_non_pay.non_pay_year = coop_finance_month_profile.profile_year","left");
		$this->db->join("coop_finance_month_detail","coop_finance_month_detail.profile_id = coop_finance_month_profile.profile_id
							AND coop_finance_month_detail.member_id = coop_non_pay.member_id AND coop_finance_month_detail.deduct_code = 'LOAN'","left");
		$this->db->join("coop_non_pay_detail","coop_non_pay.non_pay_id = coop_non_pay_detail.non_pay_id","inner");		
		$this->db->where("coop_non_pay.non_pay_status NOT IN ('0') 
							AND coop_non_pay.member_id = '{$member_id}' 
							AND coop_non_pay_detail.deduct_code IN ('LOAN','ATM')
						");
		$this->db->group_by("coop_non_pay.non_pay_id");
		$rs_debt = $this->db->get()->result_array();
		//echo $this->db->last_query(); echo '<br>'; exit;
							
		$arr_data['count_debt'] = 0;
		$arr_debt = array();
		if(!empty($rs_debt)){
			foreach($rs_debt as $key => $row_count_debt){			
				
				//ข้อมูลเรียกเก็บรายเดือน
				$rs_check_pay_amount = $this->db->select(array('SUM(pay_amount) AS pay_amount'))->from('coop_finance_month_detail')
						->where("member_id = '".$row_count_debt['member_id']."' AND profile_id = '".$row_count_debt['profile_id']."' AND deduct_code IN ('LOAN','ATM')")->limit(1)->get()->result_array();
				$check_pay_amount = @$rs_check_pay_amount[0];
				
				//ข้อมูลหนี้ค้างชำระ
				$rs_check_non_pay_amount = $this->db->select(array('SUM(t2.non_pay_amount) AS non_pay_amount'))
						->from('coop_non_pay AS t1')
						->join("coop_non_pay_detail AS t2","t1.non_pay_id = t2.non_pay_id","left")
						->where("t1.member_id = '".$row_count_debt['member_id']."' AND t1.non_pay_status NOT IN ('0') AND t1.non_pay_month = '".$row_count_debt['non_pay_month']."' AND t1.non_pay_year = '".$row_count_debt['non_pay_year']."' AND t2.deduct_code IN ('LOAN','ATM')")->limit(1)->get()->result_array();
				$check_non_pay_amount = @$rs_check_non_pay_amount[0];	
				$rs_debt[$key]['pay_amount'] = ($check_pay_amount['pay_amount'] > $check_non_pay_amount['non_pay_amount'])?@$check_pay_amount['pay_amount']:@$check_non_pay_amount['non_pay_amount'];
				
				//เลขที่สัญญา
				$rs_contract_number = $this->db->select(array("IF(t2.loan_id != '',t3.contract_number,t4.contract_number) AS contract_number"))
						->from('coop_non_pay AS t1')
						->join("coop_non_pay_detail AS t2","t1.non_pay_id = t2.non_pay_id","left")
						->join("coop_loan AS t3","t2.loan_id = t3.id","left")
						->join("coop_loan_atm AS t4","t2.loan_atm_id = t4.loan_atm_id","left")
						->where("t1.member_id = '".$row_count_debt['member_id']."' AND t1.non_pay_status NOT IN ('0') AND t1.non_pay_month = '".$row_count_debt['non_pay_month']."' AND t1.non_pay_year = '".$row_count_debt['non_pay_year']."' AND t2.deduct_code IN ('LOAN','ATM')")
						->group_by("t2.loan_id,t2.loan_atm_id")
						->get()->result_array();
				//echo $this->db->last_query(); echo '<br>';		
				$contract_number = '';	
				foreach($rs_contract_number AS $key_2=>$value){
					$comma = ($key_2 > 0)?',':''; 
					$contract_number .= $comma.$value['contract_number'];
				}
				$rs_debt[$key]['contract_number'] = @$contract_number;
				
				$this->db->select(array('non_pay_amount_balance'));
				$this->db->from('coop_non_pay_detail');
				$this->db->where("member_id = '{$row_count_debt['member_id']}'
									AND	non_pay_id = '{$row_count_debt['non_pay_id']}'
									AND deduct_code = 'LOAN' AND pay_type = 'principal'");
				$rs_principal = $this->db->get()->result_array();
				$amount_principal = @$rs_principal[0]['non_pay_amount_balance'];  
				
				$this->db->select(array('non_pay_amount_balance'));
				$this->db->from('coop_non_pay_detail');
				$this->db->where("member_id = '{$row_count_debt['member_id']}'
									AND	non_pay_id = '{$row_count_debt['non_pay_id']}'
									AND deduct_code = 'LOAN' AND pay_type = 'interest'");
				$rs_interest = $this->db->get()->result_array();
				$amount_interest = @$rs_interest[0]['non_pay_amount_balance']; 
				
				@$non_pay_amount_balance = @$amount_principal+@$amount_interest;
				
				$non_pay_detail = '';
				if($amount_principal != 0 AND $amount_interest != 0){
					$non_pay_detail = '('.number_format(@$amount_principal,2).' + '.number_format(@$amount_interest,2).')';
				}
															
				@$rs_debt[$key]['non_pay_amount_balance'] = $non_pay_amount_balance;
				@$rs_debt[$key]['non_pay_detail'] = $non_pay_detail;
				@$arr_data['sum_debt_balance'] += $rs_debt[$key]['pay_amount'];
				@$arr_data['sum_debt_all'] += @$rs_debt[$key]['non_pay_amount_balance'];
				@$arr_data['count_debt']++;
			}
		}
		$arr_data['rs_debt'] = 	@$rs_debt;	
		
		if($member_id != ''){
			//รายการเรียกเก็บ
			$this->db->select(array('*'));
			$this->db->from('coop_finance_month_profile');
			$this->db->order_by("profile_year,profile_month DESC");
			$this->db->limit(1);
			$rs_profile_last = $this->db->get()->result_array();
			$rs_profile_last = @$rs_profile_last[0];
			$date_now=date('Y',strtotime('+543 year')); //เปลี่ยนให้ค่าเริ่มต้นแสดงปีปัจจุบัน
			$where = "";
			if(@$_GET['month'] == '' && @$_GET['year'] == ''){
				$profile_month = "";
				$profile_year = @$date_now;				
			}else{
				$profile_month = @$_GET['month'];
				$profile_year = @$_GET['year'];			
			}
			
			if(@$profile_month != ''){
				$where .= " AND profile_month = '".@$profile_month."'";	
			}
			
			if(@$profile_year != ''){
				$where .= " AND profile_year = '".@$profile_year."'";	
			}
			
			$this->db->select(array('*'));
			$this->db->from('coop_finance_month_profile');
			$this->db->where("1=1 {$where}");
			$this->db->order_by("profile_year,profile_month DESC");
			$rs_finance_profile = $this->db->get()->result_array();

			$arr_profile_id = array();
			foreach($rs_finance_profile AS $key=>$row_finance_profile){
				$arr_profile_id[] = $row_finance_profile['profile_id'];
			}			
			
			//echo $this->db->last_query();exit;
			if(!empty($arr_profile_id)){
				
				$this->db->select(array('SUM(coop_finance_month_detail.pay_amount) AS pay_amount',
											'(SELECT non_pay_amount_balance FROM coop_non_pay WHERE member_id = coop_finance_month_detail.member_id AND non_pay_month = coop_finance_month_profile.profile_month AND non_pay_year = coop_finance_month_profile.profile_year LIMIT 1) AS non_pay_amount_balance',	
											'coop_finance_month_profile.profile_month',
											'coop_finance_month_profile.profile_year',
											'coop_finance_month_profile.profile_id',
											'(SELECT non_pay_id FROM coop_non_pay WHERE member_id = coop_finance_month_detail.member_id AND non_pay_month = coop_finance_month_profile.profile_month AND non_pay_year = coop_finance_month_profile.profile_year LIMIT 1) AS non_pay_id'
											));
				$this->db->from('coop_finance_month_detail');
				$this->db->join("coop_finance_month_profile","coop_finance_month_detail.profile_id = coop_finance_month_profile.profile_id","left");
				$this->db->where("coop_finance_month_detail.profile_id IN ('".implode("','",$arr_profile_id)."') AND coop_finance_month_detail.member_id = '".@$member_id."' ");
				$this->db->group_by("coop_finance_month_profile.profile_month,coop_finance_month_profile.profile_year");
				$rs_finance_detail = $this->db->get()->result_array();
				$pay_amount = 0;
				$row_finance_detail = array();
				if(@$_GET['dev']=='dev'){
				echo $this->db->last_query();exit;
				}
				foreach($rs_finance_detail AS $key_detail=>$value_detail){
					$pay_amount += $value_detail['pay_amount'];
					if($value_detail['pay_amount'] != ''){
						$row_finance_detail[$key_detail] = @$value_detail;
					}
					
					//ใบเสร็จจากหนี้คงค้าง
					$rs_pay_receipt = $this->db->select(array('coop_non_pay_receipt.receipt_id', 'receipt_status', 'updatetime', 'user_name', 'coop_mem_req_resign.req_resign_id'))
										->from('coop_non_pay_receipt')
										->join('coop_user', 'coop_user.user_id = coop_non_pay_receipt.user_id','left')
										->join('coop_mem_req_resign', 'coop_non_pay_receipt.receipt_id = coop_mem_req_resign.receipt_id','left')
										->where("non_pay_id = '".@$value_detail['non_pay_id']."' AND coop_non_pay_receipt.member_id = '".@$member_id."'")
										->get()->result_array();
					
					foreach($rs_pay_receipt AS $key_receipt=>$row_pay_receipt){
						$row_finance_detail[$key_detail]['receipt_id'][@$row_pay_receipt['receipt_id']] = @$row_pay_receipt;
					}
					
					//ใบเสร็จจากชำรายรายเดือน
					$rs_pay_receipt = $this->db->select(array('coop_receipt.receipt_id', 'IF( coop_receipt.receipt_id IS NULL AND coop_receipt.receipt_status IS NULL ,NULL,IF(coop_receipt.receipt_id IS NOT NULL AND coop_receipt.receipt_status IS NULL ,0,1)) AS receipt_status'))
										->from('coop_receipt')
										->where("coop_receipt.finance_month_profile_id  = '".@$value_detail['profile_id']."' AND coop_receipt.member_id = '".@$member_id."' AND coop_receipt.receipt_status IS NULL")
										->get()->result_array();											
					foreach($rs_pay_receipt AS $key_receipt=>$row_pay_receipt){
						$row_finance_detail[$key_detail]['receipt_id'][@$row_pay_receipt['receipt_id']] = @$row_pay_receipt;
					}
				}
			}
			
			$arr_data['finance_month'] = @$profile_month;
			$arr_data['finance_year'] = @$profile_year;
			$arr_data['pay_amount_all'] = @$pay_amount;
			$arr_data['rs_finance_detail'] = @$row_finance_detail;
			
			//ประวัติประกันชีวิต
			$rs_life_insurance = $this->db->select('t1.insurance_id,
					t1.member_id,
					t1.insurance_year,
					t1.insurance_date,
					t1.loan_id,
					t1.contract_number,
					t1.insurance_amount,
					t1.insurance_premium,
					t2.insurance_type_name')
			->from('coop_life_insurance AS t1')
			->join("coop_life_insurance_type AS t2","t1.insurance_type = t2.insurance_type_id","left")
			->where("t1.member_id = '".$member_id."' AND insurance_status = '1'")
			->order_by("t1.insurance_year DESC,t1.insurance_date DESC ,t1.insurance_id DESC")
			->get()->result_array();
			//echo $this->db->last_query();exit;	
			$arr_data['rs_life_insurance'] = @$rs_life_insurance;		

			//รายจ่าย
			$this->db->select("id AS loan_id ,member_id,createdatetime")->from("coop_loan")->where("member_id = '".$member_id."'  AND loan_status NOT IN (0,5)")->order_by("createdatetime DESC")->limit(1);
			$loan_last = $this->db->get()->row_array();
			
			$rs_loan_cost_mod = $this->db->select('t1.outgoing_name,t2.loan_cost_amount')
			->from('coop_outgoing AS t1')
			->join("coop_loan_cost_mod AS t2","t1.outgoing_code = t2.loan_cost_code","left")
			->where("t2.loan_id = '".$loan_last['loan_id']."'")
			->order_by("t1.outgoing_id ASC")
			->get()->result_array();
			$arr_data['rs_loan_cost_mod'] = @$rs_loan_cost_mod;		
		}
		
		//ประเภทสมัคร
		$this->db->select('apply_type_id, apply_type_name');
		$this->db->from('coop_mem_apply_type');
		$rs_mem_type = $this->db->get()->result_array();
		$mem_type_list = array();
		foreach($rs_mem_type AS $key=>$row_mem_type){
			$mem_apply_type[$row_mem_type['apply_type_id']] = $row_mem_type['apply_type_name'];
		}
		$arr_data['mem_apply_type'] = @$mem_apply_type;
		//echo $this->db->last_query();exit;	
		$arr_data['month_arr'] = array('1'=>'มกราคม','2'=>'กุมภาพันธ์','3'=>'มีนาคม','4'=>'เมษายน','5'=>'พฤษภาคม','6'=>'มิถุนายน','7'=>'กรกฎาคม','8'=>'สิงหาคม','9'=>'กันยายน','10'=>'ตุลาคม','11'=>'พฤศจิกายน','12'=>'ธันวาคม');
		
		//ประเภทหุ้น
		$this->db->select('share_type_code, share_type_name');
		$this->db->from('coop_share_type');
		$this->db->where("share_type_status ='1'");
		$rs_share_type = $this->db->get()->result_array();
		$arr_share_type = array();
		foreach($rs_share_type AS $key=>$row_share_type){
			$arr_share_type[$row_share_type['share_type_code']] = $row_share_type['share_type_name'];
		}
		$arr_data['share_type'] = @$arr_share_type;

		//permission update share
		$this->db->select('user_permission_id');
		$this->db->from('coop_user_permission');
		$this->db->where("user_id = '".$_SESSION['USER_ID']."' AND menu_id = '332'");
		$row = $this->db->get()->result_array();
		if($row[0]['user_permission_id']==''){
			$arr_data['permission']['332'] = false;
		}else{
			$arr_data['permission']['332'] = true;
		}
		//
		
		$this->libraries->template('manage_member_share/member_loan',$arr_data);
	}
	
	function group_move(){
		
		$arr_data = array();
		
		if($this->input->get('member_id')!=''){
			$member_id = $this->input->get('member_id');
		}else{
			$member_id = '';
		}
		$arr_data = array();
		$arr_data['member_id'] = $member_id;

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
			
			//ประเภทสมาชิก
			$this->db->select('mem_type_id, mem_type_name');
			$this->db->from('coop_mem_type');
			$rs_mem_type = $this->db->get()->result_array();
			$mem_type_list = array();
			foreach($rs_mem_type AS $key=>$row_mem_type){
				$mem_type_list[$row_mem_type['mem_type_id']] = $row_mem_type['mem_type_name'];
			}
			
			$arr_data['mem_type_list'] = $mem_type_list;
			
			$x=0;
			$join_arr = array();
			$join_arr[$x]['table'] = 'coop_user';
			$join_arr[$x]['condition'] = 'coop_user.user_id = coop_mem_group_move.admin_id';
			$join_arr[$x]['type'] = 'left';
			
			$this->paginater_all->type(DB_TYPE);
			$this->paginater_all->select('*');
			$this->paginater_all->main_table('coop_mem_group_move');
			$this->paginater_all->where("1=1 AND member_id = '".$member_id."'");
			$this->paginater_all->page_now(@$_GET["page"]);
			$this->paginater_all->per_page(20);
			$this->paginater_all->page_link_limit(20);
			$this->paginater_all->order_by('createdatetime DESC');
			$this->paginater_all->join_arr($join_arr);
			$row = $this->paginater_all->paginater_process();

			$paging = $this->pagination_center->paginating($row['page'], $row['num_rows'], $row['per_page'], $row['page_link_limit'],@$_GET);//$page_now = 1, $row_total = 1, $per_page = 20, $page_limit = 20
			$i = $row['page_start'];
			//echo $this->db->last_query();exit;	

			$arr_data['num_rows'] = $row['num_rows'];
			$arr_data['paging'] = $paging;
			$arr_data['data'] = $row['data'];
			$arr_data['i'] = $i;
			
		}	
			
		$this->db->select('id, mem_group_name');
		$this->db->from('coop_mem_group');
		$this->db->where("mem_group_type='1'");
		$row = $this->db->get()->result_array();
		$arr_data['department'] = $row;
		
		$arr_department = array();
		foreach($row AS $key=>$value){
			$arr_department[$value['id']] = $value['mem_group_name'];
			$arr_data['department_list'] = @$arr_department;
		}
		
		
		$this->db->select('id, mem_group_name');
		$this->db->from('coop_mem_group');
		$this->db->where("mem_group_type='2'");
		$row = $this->db->get()->result_array();
		$arr_data['faction'] = $row;
		
		$arr_faction = array();
		foreach($row AS $key=>$value){
			$arr_faction[$value['id']] = $value['mem_group_name'];
			$arr_data['faction_list'] = @$arr_faction;
		}
			
		$this->db->select('id, mem_group_name');
		$this->db->from('coop_mem_group');
		$this->db->where("mem_group_type='3'");
		$row = $this->db->get()->result_array();
		$arr_data['level'] = $row;
		
		$arr_level= array();
		foreach($row AS $key=>$value){
			$arr_level[$value['id']] = $value['mem_group_name'];
			$arr_data['level_list'] = @$arr_level;
		}
		
		//echo '<pre>'; print_r($row_finance_profile); echo '</pre>';		
		//echo $this->db->last_query();exit;	
		$arr_data['month_arr'] = array('1'=>'มกราคม','2'=>'กุมภาพันธ์','3'=>'มีนาคม','4'=>'เมษายน','5'=>'พฤษภาคม','6'=>'มิถุนายน','7'=>'กรกฎาคม','8'=>'สิงหาคม','9'=>'กันยายน','10'=>'ตุลาคม','11'=>'พฤศจิกายน','12'=>'ธันวาคม');	
		$this->libraries->template('manage_member_share/group_move',$arr_data);
	}
	
	function get_mem_group_move(){
		$arr_data = array();
		$member_id = @$_POST['member_id'];
		$id_move = @$_POST['id'];
		$arr_return = array();
		if($id_move == ''){
			//echo 'member_id='.$member_id;
			$this->db->select(array(
							't1.id',
							't1.member_id',
							't1.firstname_th',
							't1.lastname_th',
							't1.apply_date',
							't1.mem_apply_id',
							't1.member_status',
							't1.department AS department_old',
							't1.faction AS faction_old',
							't1.level AS level_old',
							't2.mem_group_name AS department_name_old',
							't3.mem_group_name AS faction_name_old',
							't4.mem_group_name AS level_name_old'
							));
			$this->db->from('coop_mem_apply AS t1');
			$this->db->join("coop_mem_group AS t2","t1.department = t2.id","left");
			$this->db->join("coop_mem_group AS t3","t1.faction = t3.id","left");
			$this->db->join("coop_mem_group AS t4","t1.level = t4.id","left");
			$this->db->where("t1.member_id = '{$member_id}'");
			$this->db->order_by('t1.mem_apply_id DESC');
			$rs = $this->db->get()->result_array();			
			$row = @$rs[0];
			$row['date_move'] = $this->center_function->mydate2date(date("Y-m-d"));
			$arr_return = @$row;
		}else{
			$this->db->select(array(	
							't1.*',
							't1.department_old AS department_old',
							't1.faction_old AS faction_old',
							't1.level_old AS level_old',
							't2.mem_group_name AS department_name_old',
							't3.mem_group_name AS faction_name_old',
							't4.mem_group_name AS level_name_old'
							));
			$this->db->from('coop_mem_group_move AS t1');
			$this->db->join("coop_mem_group AS t2","t1.department_old = t2.id","left");
			$this->db->join("coop_mem_group AS t3","t1.faction_old = t3.id","left");
			$this->db->join("coop_mem_group AS t4","t1.level_old = t4.id","left");
			$this->db->where("t1.id = '{$id_move}'");
			$rs = $this->db->get()->result_array();			
			$row = @$rs[0];
			$row['date_move'] = $this->center_function->mydate2date(@$row['date_move']);			
			$arr_return = @$row;
			//echo '<pre>'; print_r($row); echo '</pre>';
		}
		//echo $this->db->last_query();exit;
		echo json_encode($arr_return);	
		exit;
	}
	
	function group_move_save(){
		$data = $this->input->post();
		//echo $data['action'];
		//exit;
		if(@$data['status_move'] == '1'){	
			$data_update = array();
			$data_update['department'] = @$data['department'];
			$data_update['faction'] = @$data['faction'];
			$data_update['level'] = @$data['level'];
			$this->db->where('member_id',@$data['member_id']);
			$this->db->update('coop_mem_apply', $data_update);
			
			$data_update = array();
			$data_update['status_move'] = 0;
			$this->db->where('member_id',@$data['member_id']);
			$this->db->update('coop_mem_group_move', $data_update);
		}
		
		$data_insert = array();
		$data_insert['member_id'] = @$data['member_id'];
		$data_insert['department_old'] = @$data['department_old'];
		$data_insert['faction_old'] = @$data['faction_old'];
		$data_insert['level_old'] = @$data['level_old'];
		$data_insert['department'] = @$data['department'];
		$data_insert['faction'] = @$data['faction'];
		$data_insert['level'] = @$data['level'];
		$data_insert['date_move'] = @$this->center_function->ConvertToSQLDate($data['date_move']);
		$data_insert['status_move'] = (@$data['status_move'] == 1)?@$data['status_move']:0;
		$data_insert['note'] = @$data['note'];
		$data_insert['admin_id'] = @$_SESSION['USER_ID'];			
		$data_insert['updatedate'] = date('Y-m-d H:i:s');
			
		if($data['action'] == 'add'){
			$data_insert['createdatetime'] = date('Y-m-d H:i:s');
			$this->db->insert('coop_mem_group_move', $data_insert);
		}else{
			$this->db->where('id',@$data['id_move']);
			$this->db->update('coop_mem_group_move', $data_insert);
		}
		
		$this->center_function->toast("บันทึกข้อมูลเรียบร้อยแล้ว");
		echo"<script> document.location.href='".PROJECTPATH."/manage_member_share/group_move?member_id=".@$data['member_id']."' </script>";
	}
	
	function del_group_move(){	
		$id = @$_POST['id'];
		
		$this->db->where('id', $id );
		$this->db->delete('coop_mem_group_move');
		$this->center_function->toast("ลบเรียบร้อยแล้ว");
		echo true;
		
	}
	
	function note_save(){	
		$member_id = @$_POST['member_id'];
		$note = @$_POST['note'];
		$tab = @$_POST['tab'];

		$data_insert = array();
		$data_insert['member_id'] = $member_id;
		$data_insert['note'] = $note;
		$this->db->insert('coop_mem_spccomm', $data_insert);

		$this->center_function->toast("บันทึกข้อมูลเรียบร้อยแล้ว");
		echo"<script> document.location.href='".PROJECTPATH."/manage_member_share/member_loan?member_id=".@$member_id."&tab=".@$tab."' </script>";	
	}
	
	function get_finance_month_detail(){
		
		$member_id = @$_POST['member_id'];
		$profile_month = @$_POST['profile_month'];
		$profile_year = @$_POST['profile_year'];
		if($member_id != ''){
			//รายการเรียกเก็บ
			$this->db->select(array('*'));
			$this->db->from('coop_finance_month_profile');
			$this->db->where("1=1  AND profile_month = '".@$profile_month."' AND profile_year = '".@$profile_year."'");
			$this->db->order_by("profile_year,profile_month DESC");
			$rs_finance_profile = $this->db->get()->result_array();
			$row_finance_profile = @$rs_finance_profile[0];
			//echo '<pre>'; print_r($rs_finance_profile); echo '</pre>';	
			//echo $this->db->last_query();exit;			
				
			$this->db->select(array('coop_finance_month_detail.*',
										'coop_finance_month_profile.profile_month',
										'coop_finance_month_profile.profile_year',
										'coop_loan.contract_number',
										'coop_deduct.deduct_detail',
										'coop_loan.loan_type'
										));
			$this->db->from('coop_finance_month_detail');
			$this->db->join("coop_finance_month_profile","coop_finance_month_detail.profile_id = coop_finance_month_profile.profile_id","left");
			$this->db->join("coop_deduct","coop_finance_month_detail.deduct_code = coop_deduct.deduct_code AND coop_deduct.deduct_code NOT IN ('LOAN','GUARANTEE','ATM')","left");		
			$this->db->join("coop_loan","coop_finance_month_detail.loan_id = coop_loan.id","left");
			$this->db->where("coop_finance_month_detail.profile_id = '".@$row_finance_profile['profile_id']."' AND coop_finance_month_detail.member_id = '".@$member_id."'");
			$rs = $this->db->get()->result_array();

			$loan_name = $this->db->get('coop_loan_name')->result_array();
			$_loan_name = array();
			foreach($loan_name as $key => $val){
				$_loan_name[$val['loan_name_id']] = $val['loan_name'];
			}
			unset($loan_name);

			$pay_amount_all = 0;
			$data = '';
			$i = 1;
			if(!empty($rs)){
				foreach(@$rs as $key => $row){
					$text_pay_loan_type = ($row['pay_type'] == 'interest')?'ดอกเบี้ยเงินกู้เลขที่สัญญา':'ต้นเงินกู้เลขที่สัญญา';
					$text_pay_guarantee_type = ($row['pay_type'] == 'interest')?'ดอกเบี้ยสัญญาในฐานะผู้ค้้ำประกัน':'ต้นเงินสัญญาในฐานะผู้ค้้าประกัน';
					$text_pay_atm_type = ($row['pay_type'] == 'interest')?'ดอกเบี้ยเงินกู้ฉุกเฉิน ATM':'ต้นเงินสัญญากู้ฉุกเฉิน ATM';
					$deduct_detail = '';
					if($row['deduct_code'] == 'LOAN'){
						$deduct_detail = $text_pay_loan_type.'  '.$_loan_name[$row['loan_type']].'  '.$row['contract_number'];
					}else if($row['deduct_code'] == 'GUARANTEE'){
						$deduct_detail = $text_pay_guarantee_type.'  '.$row['contract_number'];
					}else if($row['deduct_code'] == 'ATM'){
						$deduct_detail = $text_pay_atm_type;
					}else{
						$deduct_detail = $row['deduct_detail'];		
					}

					
					$data .= '
						<tr> 
							<td>'.@$i.'</td>
							<td class="text-left">
							'.$deduct_detail.'
							</td>
							<td class="text-right">
								'.number_format(@$row['pay_amount'],2).'
							</td>									
						</tr>
						';							
					
					$i++; 
					$pay_amount_all += @$row['pay_amount'];
				}
				$data .= '<tr style="background-color: #e0e0e0;"> 
							<th colspan="2">รวม</th>																	
							<th class="text-right"><span id="pay_amount_all">'.number_format($pay_amount_all,2).'</span></th>									
						  </tr>';
			}else{
				$data .= '<tr><td colspan="4">ไม่พบข้อมูล</td></tr>';
			}		
			
			echo $data;
		}
		exit;
	}

    public function check_member_id(){
		$member_id = $this->center_function->complete_member_id($_POST['member_id']);
		$arr_data = array();
		$this->db->select(array('id','member_id'));
		$this->db->from('coop_mem_apply');
		$this->db->where("member_id LIKE '%".$member_id."%'");
		$this->db->limit(1);
		$rs_member = $this->db->get()->result_array();
		//echo $this->db->last_query();exit;
		$row_member = $rs_member[0];
		if(!empty($row_member)){
			$arr_data = @$row_member;
		}else{
			$arr_data = array();
		}	
		//echo '<pre>'; print_r($arr_data); echo '</pre>';
		echo json_encode($arr_data);	
		exit;
	}	
	
	public function update_transaction_share(){
		$data = $this->input->post();
		//echo '<pre>'; print_r($data); echo '</pre>'; exit;
		if($data==""){
			exit;
		}else{
			$share_start_date = $data['date'];
			$member_id = $data['member_id'];
			$this->update_st->update_share_transaction($member_id,$share_start_date);			
		}

		echo "success";

	}

	public function get_change_detail() {
		$change = $this->db->select("*")
							->from("coop_member_data_history")
							->where("id = ".$_POST["id"])
							->get()->row();
		$result = array();
		$result["id"] = $change->id;
		$result["input_name"] = $change->input_name;
		if($change->input_name == "member_pic") {
			$result["old_value"] = "-";
			$result["new_value"] = "-";
		} elseif($change->input_name == "dividend_acc_num") {
			$old_accounts = json_decode($change->old_value);
			$new_accounts = json_decode($change->new_value);
			$old_text = "ลบบัญชีเลขที่ ";
			$old_count = count($old_accounts);
			$index = 1;
			foreach($old_accounts as $acc) {
				$old_text .= $acc;
				$old_text .= $index != $old_count ? "" : ", ";
				$index++;
			}
			$new_text = "เพิ่มบัญชีเลขที่ ";
			$new_count = count($new_accounts);
			$index = 1;
			foreach($new_count as $acc) {
				$new_text .= $acc;
				$new_text .= $index != $new_count ? "" : ", ";
				$index++;
			}
			$result["old_value"] = $old_count > 0 ? $old_text : "-";
			$result["new_value"] = $new_count > 0 ? $new_text : "-";
		} else if ($change->input_name == "mem_type") {
			$labels = array("1"=>"ปกติ",
							"2"=>"ลาออก",
							"3"=>"รออนุมัติ",
							"4"=>"ประนอมหนี้",
							"5"=>"โอนหุ้นตัดหนี้"
							);
			$result["old_value"] = $labels[$change->old_value];
			$result["new_value"] = $labels[$change->new_value];
		} else if ($change->input_name == "mem_type_id") {
			$mem_type_old = $this->db->select("*")
									->from("coop_mem_type")
									->where("mem_type_id = '".$change->old_value."'")
									->get()->row();
			$mem_type_new = $this->db->select("*")
									->from("coop_mem_type")
									->where("mem_type_id = '".$change->new_value."'")
									->get()->row();
			$result["old_value"] = !empty($mem_type_old) ? $mem_type_old->mem_type_name : "-";
			$result["new_value"] = !empty($mem_type_new) ? $mem_type_new->mem_type_name : "-";
		} else if ($change->input_name == "prename_id") {
			$old = $this->db->select("*")
									->from("coop_prename")
									->where("prename_id = '".$change->old_value."'")
									->get()->row();
			$new = $this->db->select("*")
									->from("coop_prename")
									->where("prename_id = '".$change->new_value."'")
									->get()->row();
			$result["old_value"] = !empty($old) ? $old->prename_full : "-";
			$result["new_value"] = !empty($new) ? $new->prename_full : "-";
		} else if ($change->input_name == "province_id" || $change->input_name == "c_province_id" || $change->input_name == "m_province_id") {
			$old = $this->db->select("*")
									->from("coop_province")
									->where("province_id = '".$change->old_value."'")
									->get()->row();
			$new = $this->db->select("*")
									->from("coop_province")
									->where("province_id = '".$change->new_value."'")
									->get()->row();
			$result["old_value"] = !empty($old) ? $old->province_name : "-";
			$result["new_value"] = !empty($new) ? $new->province_name : "-";
		} else if ($change->input_name == "amphur_id" || $change->input_name == "c_amphur_id" || $change->input_name == "m_amphur_id") {
			$old = $this->db->select("*")
									->from("coop_amphur")
									->where("amphur_id = '".$change->old_value."'")
									->get()->row();
			$new = $this->db->select("*")
									->from("coop_amphur")
									->where("amphur_id = '".$change->new_value."'")
									->get()->row();
			$result["old_value"] = !empty($old) ? $old->amphur_name : "-";
			$result["new_value"] = !empty($new) ? $new->amphur_name : "-";
		} else if ($change->input_name == "district_id" || $change->input_name == "c_district_id" || $change->input_name == "m_district_id") {
			$old = $this->db->select("*")
									->from("coop_district")
									->where("district_id = '".$change->old_value."'")
									->get()->row();
			$new = $this->db->select("*")
									->from("coop_district")
									->where("district_id = '".$change->new_value."'")
									->get()->row();
			$result["old_value"] = !empty($old) ? $old->district_name : "-";
			$result["new_value"] = !empty($new) ? $new->district_name : "-";
		} else if ($change->input_name == "department" || $change->input_name == "faction" || $change->input_name == "level") {
			$old = $this->db->select("*")
									->from("coop_mem_group")
									->where("id = '".$change->old_value."'")
									->get()->row();
			$new = $this->db->select("*")
									->from("coop_mem_group")
									->where("id = '".$change->new_value."'")
									->get()->row();
			$result["old_value"] = !empty($old) ? $old->mem_group_name : "-";
			$result["new_value"] = !empty($new) ? $new->mem_group_name : "-";
		} elseif ($change->input_name == "birthday" || $change->input_name == "work_date" || $change->input_name == "retry_date") {
			$result["old_value"] = !empty($change->old_value) ? $this->center_function->ConvertToThaiDate($change->old_value,'1','0') : "-";
			$result["new_value"] = !empty($change->new_value) ? $this->center_function->ConvertToThaiDate($change->new_value,'1','0') : "-";
		} else {
			$result["old_value"] = !empty($change->old_value) ? $change->old_value : "-";
			$result["new_value"] = !empty($change->new_value) ? $change->new_value : "-";
		}
		echo json_encode($result);
	}

	public function member_data_change_history() {
		$arr_data = array();
		$labels = array("apply_type_id"=>"ประเภทสมัคร",
								"member_pic"=>"รูปภาพสมาชิก",
								"dividend_acc_num"=>"เลขบัญชีสมาชิก",
								"mem_type"=>"สถานะ",
								"mem_type_id"=>"ประเภทสมาชิก",
								"prename_id"=>"คำนำหน้า",
								"sex"=>"เพศ",
								"firstname_th"=>"ชื่อ (ภาษาไทย)",
								"lastname_th"=>"สกุล (ภาษาไทย)",
								"firstname_en"=>"ชื่อ (English)",
								"lastname_en"=>"สกุล (English)",
								"email"=>"E-mail",
								"tel"=>"เบอร์บ้าน",
								"office_tel"=>"เบอร์ที่ทำงาน",
								"mobile"=>"เบอร์มือถือ",
								"address_no"=>"เลขที่(ที่อยู่ตามทะเบียนบ้าน)",
								"address_moo"=>"หมู่(ที่อยู่ตามทะเบียนบ้าน)",
								"address_village"=>"หมู่บ้าน(ที่อยู่ตามทะเบียนบ้าน)",
								"address_soi"=>"ซอย(ที่อยู่ตามทะเบียนบ้าน)",
								"address_road"=>"ถนน(ที่อยู่ตามทะเบียนบ้าน)",
								"province_id"=>"จังหวัด(ที่อยู่ตามทะเบียนบ้าน)",
								"amphur_id"=>"อำเภอ(ที่อยู่ตามทะเบียนบ้าน)",
								"district_id"=>"ตำบล(ที่อยู่ตามทะเบียนบ้าน)",
								"zipcode"=>"รหัสไปรษณีย์(ที่อยู่ตามทะเบียนบ้าน)",
								"c_address_no"=>"เลขที่(ที่อยู่ปัจจุบัน)",
								"c_address_moo"=>"หมู่(ที่อยู่ปัจจุบัน)",
								"c_address_village"=>"หมู่บ้าน(ที่อยู่ปัจจุบัน)",
								"c_address_soi"=>"ซอย(ที่อยู่ปัจจุบัน)",
								"c_address_road"=>"ถนน(ที่อยู่ปัจจุบัน)",
								"c_province_id"=>"จังหวัด(ที่อยู่ปัจจุบัน)",
								"c_amphur_id"=>"อำเภอ(ที่อยู่ปัจจุบัน)",
								"c_district_id"=>"ตำบล(ที่อยู่ปัจจุบัน)",
								"c_zipcode"=>"รหัสไปรษณีย์(ที่อยู่ปัจจุบัน)",
								"marry_status"=>"สถานะสมรส",
								"nationality"=>"สัญชาติ",
								"birthday"=>"วันเกิด",
								"father_name"=>"ชื่อบิดา",
								"mother_name"=>"ชื่อมารดา",
								"position"=>"ตำแหน่ง(ข้อมูลที่ทำงาน)",
								"department"=>"หน่วยงานหลัก(ข้อมูลที่ทำงาน)",
								"faction"=>"อำเภอ(ข้อมูลที่ทำงาน)",
								"level"=>"หน่วยงานย่อย(ข้อมูลที่ทำงาน)",
								"work_date"=>"วันบรรจุ(ข้อมูลที่ทำงาน)",
								"retry_date"=>"เกษียณ(ข้อมูลที่ทำงาน)",
								"salary"=>"เงินเดือน(ข้อมูลที่ทำงาน)",
								"other_income"=>"เงินอื่นๆ(ข้อมูลที่ทำงาน)",
                                "work_district"=>"แขวง(ข้อมูลที่ทำงาน)",
								"marry_name"=>"ชื่อคู่สมรส",
								"m_id_card"=>"เลขบัตรประชาชน(ข้อมูลคู่สมรส)",
								"m_address_no"=>"เลขที่บ้าน(ข้อมูลคู่สมรส)",
								"m_address_moo"=>"หมู่(ข้อมูลคู่สมรส)",
								"m_address_village"=>"หมู่บ้าน(ข้อมูลคู่สมรส)",
								"m_address_soi"=>"ซอย(ข้อมูลคู่สมรส)",
								"m_address_road"=>"ถนน(ข้อมูลคู่สมรส)",
								"m_province_id"=>"จังหวัด(ข้อมูลคู่สมรส)",
								"m_amphur_id"=>"อำเภอ(ข้อมูลคู่สมรส)",
								"m_district_id"=>"ตำบล(ข้อมูลคู่สมรส)",
								"m_zipcode"=>"รหัสไปรษณีย์(ข้อมูลคู่สมรส)",
								"m_tel"=>"โทรศัพท์(ข้อมูลคู่สมรส)",
								"share_month"=>"ส่งค่าหุ้นรายเดือน"
							);

		$this->paginater_all->type(DB_TYPE);
		$this->paginater_all->select('created_at, member_id');
		$this->paginater_all->main_table('coop_member_data_history');
		$this->paginater_all->where("member_id = '".$_GET["member_id"]."'");
		$this->paginater_all->page_now(@$_GET["page"]);
		$this->paginater_all->per_page(50);
		$this->paginater_all->page_link_limit(20);
		$this->paginater_all->order_by('created_at DESC');
		$this->paginater_all->group_by("created_at");
		$row = $this->paginater_all->paginater_process();

		$paging = $this->pagination_center->paginating($row['page'], $row['num_rows'], $row['per_page'], $row['page_link_limit'], $_GET);//$page_now = 1, $row_total = 1, $per_page = 20, $page_limit = 20
		$i = $row['page_start'];

		foreach($row['data'] as $key => $data) {
			$changes = $this->db->select("*")
								->from("coop_member_data_history")
								->join("coop_user", "coop_member_data_history.user_id = coop_user.user_id","left")
								->where("member_id = '".$data["member_id"]."' AND created_at = '".$data["created_at"]."'")
								->get()->result_array();
			foreach($changes as $change) {
				if(!empty($labels[$change["input_name"]])) {
					$row["data"][$key]["user"] = $change["user_name"];
					$label_arr = array();
					$label_arr["name"] = $labels[$change["input_name"]];
					$label_arr["id"] = $change["id"];
					$row["data"][$key]["change_list"][] = $label_arr;
				}
			}
		}

		$arr_data['num_rows'] = $row['num_rows'];
		$arr_data['paging'] = $paging;
		$arr_data['data'] = $row['data'];
		$arr_data['i'] = $i;

		$this->libraries->template('manage_member_share/member_data_change_history',$arr_data);
	}

	public function search_member_add()
	{
		$search_text = @$_POST["search_text"];
		$search_list = @$_POST["search_list"];
		$where = "";
		if(@$_POST['search_list'] == 'member_id'){
			$where = " member_id LIKE '%".$search_text."%'";
		}else if(@$_POST['search_list'] == 'firstname_th'){
			$where = " firstname_th LIKE '%".$search_text."%'";
		}else if(@$_POST['search_list'] == 'lastname_th'){
			$where = " lastname_th LIKE '%".$search_text."%'";
		}else if(@$_POST['search_list'] == 'id_card'){
			$where = " id_card LIKE '%".$search_text."%'";
		}else if(@$_POST['search_list'] == 'employee_id'){
			$where = " employee_id LIKE '%".$search_text."%'";
		}
		$where .= "AND member_status <> '3'";
		$this->db->select('*');
		$this->db->from('coop_mem_apply');
		$this->db->where($where);
		$row = $this->db->get()->result_array();
		$arr_data['data'] = $row;
		$this->load->view('manage_member_share/search_member_add',$arr_data);
	}

	public function request_data_member(){
		header("content-type: application/json; charset=utf8;");
		$result = array("data" => [], "status" => "error", "statusCode" => 400);

		if(isset($_POST['fixed_date'])) {
			$datetime = $_POST['fixed_date'];
			$fixed_date = date("Y-m-d", strtotime(str_replace("/", "-", $datetime) . " -543 year"));
		}else{
			$fixed_date = date('Y-m-d');
		}

		if($_POST['status_to']=='1'){

			$member_id = self::generete_member_id($_POST['id']);

			$this->db->select('member_id');
			$this->db->from('coop_mem_apply');
			$this->db->where("member_id = '".$member_id."'");
			$this->db->limit(1);
			$check_member_id = $this->db->get()->result_array();

			if($check_member_id[0]['member_id'] != ''){
				$result["data"] = array("msg" => "ไม่สามารถบันทึกข้อมูลได้", "member_id" => $member_id);
				echo json_encode($result);
				exit;
			}

			$data_insert['member_status'] = '1';
			$data_insert['member_id'] = $member_id;
			$data_insert['member_date'] = $fixed_date;
			$this->db->where('id', $_POST['id']);
			$this->db->update('coop_mem_apply', $data_insert);

			if($this->db->affected_rows()){
				$result["data"] = array("msg" => "อุมัติสมาชิกสำเร็จ");
				$result["status"] = "success";
				$result["statusCode"] = 200;
				echo json_encode($result);
			}else{
				$result["data"] = array("msg" => "any error!", "res" => $data_insert);
				$result["statusCode"] = 404;
				echo json_encode($result);
			}
			exit;
		}

		$result['data'] = $_POST;
		echo json_encode($result);
		exit;
	}

	public function get_request_data_member(){
		header("content-type: application/json; charset=utf8;");
		if(isset($_POST['id'])) {
			$this->db->select(array(
				't1.firstname_th',
				't1.lastname_th',
				't2.prename_full',
				't1.apply_date'
			));
			$this->db->from('coop_mem_apply as t1');
			$this->db->join('coop_prename as t2', 't2.prename_id = t1.prename_id', 'left');
			$this->db->where("t1.id = '" . $_POST['id'] . "'");
			$this->db->order_by('t1.member_id DESC');
			$this->db->limit(1);
			$res =  $this->db->get()->row_array();
			$data['name'] = $res['prename_full'].$res['firstname_th']." ".$res['lastname_th'];
			$data['apply_date'] = $this->center_function->mydate2date($res['apply_date']);
			echo json_encode(array('status' => 'successs', 'data' => $data, 'statusCode' => 200));
			exit;
		}
		echo json_encode(array('status' => 'error', 'data' => [], 'statusCode' => 400));
		exit;

	}

	public function generete_member_id($id){

		$this->db->select(array(
			'coop_mem_apply.apply_type_id',
			'coop_mem_apply.mem_type_id',
			'coop_mem_apply_type.apply_type_code',
			'coop_mem_type.mem_type_code'
		));
		$this->db->from('coop_mem_apply');
		$this->db->where("coop_mem_apply.id = '".$id."'");
		$this->db->join("coop_mem_apply_type","coop_mem_apply.apply_type_id = coop_mem_apply_type.apply_type_id","left");
		$this->db->join("coop_mem_type","coop_mem_apply.mem_type_id = coop_mem_type.mem_type_id","left");
		$this->db->order_by('coop_mem_apply.member_id DESC');
		$this->db->limit(1);
		$rs_member = $this->db->get()->result_array();
		$row_member  = $rs_member[0];

		//echo $this->db->last_query();

		if($row_member['apply_type_code'] == '' || $row_member['apply_type_code'] == '0'){
			$prefix = $row_member['mem_type_code'];
			$arr_apply_type[] = $row_member['apply_type_id'];
		}else{
			$prefix = $row_member['apply_type_code'];

			$this->db->select('apply_type_id');
			$this->db->from('coop_mem_apply_type');
			$this->db->where("apply_type_code = '".$prefix."'");
			$check_apply_type = $this->db->get()->result_array();
			$arr_apply_type = array_column($check_apply_type, 'apply_type_id');
		}

		$tmp_prefix = array();
		if($row_member['apply_type_code'] == ''){
			$non_prefix = $this->db->select("*")->from("coop_mem_apply_type")->where("apply_type_code <> ''")->get()->result_array();
			foreach ($non_prefix as $val){
				$tmp_prefix[] = $val['apply_type_code'];
			}
		}

		$where = " AND 1=1";
		if(sizeof($tmp_prefix)){
			$where = " AND (".implode(' OR ',array_map(function($val){
					return sprintf(" member_id NOT LIKE '%s%s'", $val, '%');
				}, $tmp_prefix)).") ";
		}

		$this->db->select('member_id');
		$this->db->from('coop_mem_apply');
		$this->db->where("member_id LIKE '" . $prefix . "%' ".$where);
		$this->db->order_by('member_id DESC');
		$this->db->limit(1);
		$row = $this->db->get()->result_array();

		//echo $this->db->last_query();

		if($prefix) {
			if(!empty($row)){
				$last_id  = str_replace(strtoupper($prefix), '', strtoupper($row[0]['member_id']));
				$id = (int)$last_id;
				$member_id = $prefix.sprintf("%04d", $id + 1);
			}else{
				$member_id = $prefix."0001";
			}
		}else{
			if (!empty($row)) {
				$id = (int)$row[0]["member_id"];
				$member_id = sprintf("%05d", $id + 1);
			} else {
				$member_id = "00001";
			}
		}
		return $member_id;

	}

	public function court_writ_note_save(){
		$datainsert = array();
		$datainsert['court_writ_note'] = $this->input->post('court_writ_note');
		$this->db->update('coop_mem_apply', $datainsert, array('member_id' => $this->input->post('member_id')));
		if($this->db->affected_rows()){
			$this->center_function->toast('บันทึกรายการสำเร็จ');
			header('location: '.base_url('/manage_member_share/member_loan?member_id='.$this->input->post('member_id').'&tab=13'));
		}else{
			$this->center_function->toastDanger('บันทึกรายการไม่สำเร็จ');
			header('location: '.base_url('/manage_member_share/member_loan?member_id='.$this->input->post('member_id').'&tab=13'));
		}
	}

	public function address_note_save(){
		$datainsert = array();
		$datainsert['note_address'] = $this->input->post('note_address');
		$this->db->update('coop_mem_apply', $datainsert, array('member_id' => $this->input->post('member_id')));
		if($this->db->affected_rows()){
			$this->center_function->toast('บันทึกรายการสำเร็จ');
			header('location: '.base_url('/manage_member_share/member_loan?member_id='.$this->input->post('member_id').'&tab=14'));
		}else{
			$this->center_function->toastDanger('บันทึกรายการไม่สำเร็จ');
			header('location: '.base_url('/manage_member_share/member_loan?member_id='.$this->input->post('member_id').'&tab=14'));
		}
	}

    public function address_send_doc_save(){
        $datainsert = array();
        $datainsert['address_send_doc'] = $this->input->post('note_address_send_doc');
        $this->db->update('coop_mem_apply', $datainsert, array('member_id' => $this->input->post('member_id')));
        if($this->db->affected_rows()){
            $this->center_function->toast('บันทึกรายการสำเร็จ');
            header('location: '.base_url('/manage_member_share/member_loan?member_id='.$this->input->post('member_id').'&tab=15'));
        }else{
            $this->center_function->toastDanger('บันทึกรายการไม่สำเร็จ');
            header('location: '.base_url('/manage_member_share/member_loan?member_id='.$this->input->post('member_id').'&tab=15'));
        }
    }

	public function gen_test(){
		echo self::generete_member_id($_GET['id']);
	}
}
