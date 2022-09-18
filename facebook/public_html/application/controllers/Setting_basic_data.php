<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Setting_basic_data extends CI_Controller {
	function __construct()
	{
		parent::__construct();
	}
	public function coop_detail()
	{
		$arr_data = array();
		$data_get = $this->input->get();
		$arr_data['data_get'] = $data_get;

		$this->db->select(array('*'));
		$this->db->from('coop_profile');
		$this->db->limit(1);
		$row = $this->db->get()->result_array();
		$arr_data['row'] = @$row[0]; 
		
		$this->libraries->template('setting_basic_data/coop_detail',$arr_data);
	}
	
	public function coop_detail_save()
	{
		$data_insert = array();
		$profile_id = @$_POST["profile_id"];
		
		$this->db->select(array('*'));
		$this->db->from('coop_profile');
		$this->db->limit(1);
		$row = $this->db->get()->result_array();
		$row = @$row[0]; 
			
		if($_FILES["coop_img"]["name"]){			
			@unlink( PATH . "/images/coop_profile/{$row['coop_img']}");	
			
			$output_dir = $_SERVER["DOCUMENT_ROOT"].PROJECTPATH."/assets/images/coop_profile/";
			$new_file_name = $this->center_function->create_file_name($output_dir,$_FILES["coop_img"]['name']);
			copy($_FILES["coop_img"]['tmp_name'], $output_dir.$new_file_name);
			$data_insert['coop_img'] = @$new_file_name;
			
		}
		/*if(!empty($_FILES["signature_1"]["tmp_name"])){
			@unlink( PATH . "/images/coop_profile/{$row['signature_1']}");	
			
			$output_dir = $_SERVER["DOCUMENT_ROOT"].PROJECTPATH."/assets/images/coop_profile/";
			$new_file_name = $this->center_function->create_file_name($output_dir,$_FILES["signature_1"]['name']);
			copy($_FILES["signature_1"]['tmp_name'], $output_dir.$new_file_name);
			$data_insert['signature_1'] = @$new_file_name;
		}

		if(!empty($_FILES["signature_2"]["tmp_name"])){
			@unlink( PATH . "/images/coop_profile/{$row['signature_2']}");	
			
			$output_dir = $_SERVER["DOCUMENT_ROOT"].PROJECTPATH."/assets/images/coop_profile/";
			$new_file_name = $this->center_function->create_file_name($output_dir,$_FILES["signature_2"]['name']);
			copy($_FILES["signature_2"]['tmp_name'], $output_dir.$new_file_name);
			$data_insert['signature_2'] = @$new_file_name;			
		}
		*/
		
		$data_insert['coop_name_th'] = @$_POST["coop_name_th"];
		$data_insert['coop_name_en'] = @$_POST["coop_name_en"];
		$data_insert['coop_short_name_en'] = @$_POST["coop_short_name_en"];
		$data_insert['address1'] = @$_POST["address1"];
		$data_insert['address2'] = @$_POST["address2"];
		$data_insert['tel'] = @$_POST["tel"];
		$data_insert['fax'] = @$_POST["fax"];
		$data_insert['email'] = @$_POST["email"];
		//$data_insert['president_name'] = @$_POST["president_name"];
		//$data_insert['manager_name'] = @$_POST["manager_name"];
		//$data_insert['auditor_name'] = @$_POST["auditor_name"];
		$data_insert['updatedate']= date("Y-m-d H:i:s");
		
		$this->db->where('profile_id', $profile_id);
		$this->db->update('coop_profile', $data_insert);
		echo"<script> document.location.href='".PROJECTPATH."/setting_basic_data/coop_detail' </script>";            
	}
	
	public function coop_address()
	{
		$arr_data = array();

		$this->db->select('COUNT(coop_province.province_id) as _c');
		$this->db->from('coop_province');
		$this->db->join('coop_amphur', 'coop_province.province_id = coop_amphur.province_id', 'inner');
		$this->db->join('coop_district', 'coop_amphur.amphur_id = coop_district.amphur_id', 'inner');
		$this->db->join('coop_zipcode', 'coop_district.district_code = coop_zipcode.district_code', 'left');
		$count = $this->db->get()->result_array();

		$num_rows = $count[0]["_c"] ;
		$per_page = 20 ;
		$page = isset($_GET["page"]) ? ((int) $_GET["page"]) : 1;
		$paging = $this->pagination_center->paginating($page, $num_rows, $per_page, 20, $_GET);//$page_now = 1, $row_total = 1, $per_page = 20, $page_limit = 20

		$page_start = (($per_page * $page) - $per_page)+1;
		if($page_start==1){ $page_start = 0;}

		$this->db->select('d1.*,d2.amphur_id,d2.amphur_name,d3.district_id,d3.district_name,d4.id,d4.zipcode');
		$this->db->from('coop_province AS d1');
		$this->db->join('coop_amphur AS d2','d1.province_id = d2.province_id','inner');
		$this->db->join('coop_district AS d3','d2.amphur_id = d3.amphur_id','inner');
		$this->db->join('coop_zipcode AS d4','d3.district_code = d4.district_code','inner');
		$this->db->limit($per_page, $page_start);
		$this->db->order_by('d1.province_id DESC');
		$rs = $this->db->get()->result_array();
		//print_r($this->db->last_query());exit;

		$i = $page_start;
		if($i==0){ $i = 1;}

		$arr_data['num_rows'] = $num_rows;
		$arr_data['paging'] = $paging;
		$arr_data['rs'] = $rs;
		$arr_data['i'] = $i;
		
		
		$this->db->select(array('*'));
		$this->db->from('coop_province');
		$this->db->order_by('province_name ASC');
		$rs_province = $this->db->get()->result_array();
		$arr_data['rs_province'] = $rs_province;
		
		if(@$_GET['district_id']){
			$this->db->select(array('*'));
			$this->db->from('coop_district');
			$this->db->where("district_id = '{$_GET['district_id']}'");
			$this->db->limit(1);
			$row_district = $this->db->get()->result_array();
			$arr_data['row_district'] = @$row_district[0]; 
			//print_r($this->db->last_query());exit;
		}
		
		$this->db->select(array('*'));
		$this->db->from('coop_amphur');
		$rs_amphur = $this->db->get()->result_array();
		$arr_data['rs_amphur'] = @$rs_amphur; 		
		
		if(@$_GET['zipcode_id']){
			$this->db->select(array('*'));
			$this->db->from('coop_zipcode');
			$this->db->where("id = '{$_GET['zipcode_id']}'");
			$this->db->limit(1);
			$row_zipcode = $this->db->get()->result_array();
			$arr_data['row_zipcode'] = @$row_zipcode[0]; 
		}
		
		$this->libraries->template('setting_basic_data/coop_address',$arr_data);
	}
	
	function search_coop_address(){
			$this->db->select(array('*'));
			$this->db->from('coop_province');
			$this->db->join('coop_amphur', 'coop_province.province_id = coop_amphur.province_id', 'inner');
			$this->db->join('coop_district', 'coop_amphur.amphur_id = coop_district.amphur_id', 'inner');
			$this->db->join('coop_zipcode', 'coop_district.district_code = coop_zipcode.district_code', 'left');
		
			$this->db->where("
				(
					coop_province.province_name LIKE '%".$this->input->post("search")."%'
					OR coop_province.province_name LIKE '".$this->input->post("search")."%'
					OR coop_province.province_name LIKE '%".$this->input->post("search")."'
				 )

			  OR 
				 (
					coop_amphur.amphur_name LIKE '%".$this->input->post("search")."%'
					OR coop_amphur.amphur_name LIKE '".$this->input->post("search")."%'
					OR coop_amphur.amphur_name LIKE '%".$this->input->post("search")."'
				 )
			  OR 
				 (
					coop_district.district_name LIKE '%".$_POST["search"]."%'
					OR coop_district.district_name LIKE '".$_POST["search"]."%'
					OR coop_district.district_name LIKE '%".$_POST["search"]."'
				 )
			");
			$this->db->order_by('coop_province.province_id DESC');
			//$this->db->limit(5);
			$rs = $this->db->get()->result_array();
			$output = '';
           if(!empty($rs)){  
				$i= 1; 
				foreach($rs as $key => $row){
                     $output .= '  

							<tr>  
								 <td scope="row">'.$i.'</td>  
								 <td>'.$row["province_name"].'</td>  
								 <td>'.$row['amphur_name'].'</td>  
								 <td>'.$row['district_name'].'</td>  
								 <td>'.$row['zipcode'].'</td>
								 <td><a href="?act=add&province_id='.$row["province_id"].'&amphur_id='.$row['amphur_id'].'&district_id='.$row["district_id"].'&zipcode_id='.$row["id"].'">แก้ไข</a> | <span class="text-del del"  onclick="del_coop_address('.@$row['id'].')">ลบ</span></td>
							</tr>  
					   '; 
               $i++; 
			   }
                echo $output;  
           }else{
                $output .= '  

                          <tr>  
								 <td align="center" colspan="6"><h1>ไม่มีพบผลการค้นหา!</h1></td>  
							</tr>  
                     ';
                echo $output;  
           }  
		   exit;
	}
	
	function del_coop_address(){	
		$table = @$_POST['table'];
		$table_sub = @$_POST['table_sub'];
		$id = @$_POST['id'];
		$field = @$_POST['field'];

		// del จังหวัด
		$id_zipcode = @$_POST['id_zipcode'];
		// del จังหวัด
        
        if (!empty($id_zipcode)) {	
			$this->db->select(array('*'));
			$this->db->from('coop_zipcode');
			$this->db->where("id = '{$id_zipcode}'");
			$rs_zipcode = $this->db->get()->result_array();
			$district_code = @$rs_zipcode[0]['district_code'];
			
			$this->db->select(array('*'));
			$this->db->from('coop_district');
			$this->db->where("district_code = '{$district_code}'");
			$rs_district = $this->db->get()->result_array();
			$amphur_id = @$rs_district[0]['amphur_id'];
			
			$this->db->select('COUNT(amphur_id) as _c');
			$this->db->from('coop_district');
			$this->db->where("amphur_id = '{$amphur_id}'");
			$count = $this->db->get()->result_array();
			$count_amphur = @$count[0]["_c"];			

			$this->db->select(array('*'));
			$this->db->from('coop_district');
			$this->db->where("amphur_id = '{$amphur_id}'");
			$rs_amphur = $this->db->get()->result_array();
			$province_id = @$rs_amphur[0]['province_id'];
			
			$this->db->select('COUNT(province_id) as _c');
			$this->db->from('coop_district');
			$this->db->where("province_id = '{$province_id}'");
			$count = $this->db->get()->result_array();
			$count_province = @$count[0]["_c"];
			
			//DELETE
			$this->db->where('id', $id_zipcode );
			$this->db->delete('coop_zipcode');
			
			$this->db->where('district_code', $district_code );
			$this->db->delete('coop_district');					
			
			if($count_amphur <= 1){
				$this->db->where('amphur_id', $amphur_id );
				$this->db->delete('coop_amphur');
			}		
			
			if($count_province <= 1){
				$this->db->where('province_id', $province_id );
				$this->db->delete('coop_province');
			}
			
			$this->center_function->toast("ลบเรียบร้อยแล้ว");
			echo true;
        }else{
			echo false;
		}	
		
	}
	
	function select_coop_address(){
		
		// จังหวัด
		$province_id = $_POST['province_id'];

		if ($province_id) {
			$this->db->select(array('*'));
			$this->db->from('coop_amphur');
			$this->db->where("province_id = '{$province_id}'");
			$rs = $this->db->get()->result_array();
			$arr_data['rs'] = @$rs; 	
		
	        echo "<option value=''> - เลือกอำเภอ - </option>";
	        if(!empty($rs)){
				foreach(@$rs as $key => $row){
					echo "<option value=".$row['amphur_id'].">".$row['amphur_name']."</option>";
				}
			}
		}
		// จังหวัด


         // อำเภอ
		$amphur_id = $_POST['amphur_id'];
		if ($amphur_id) {
			$this->db->select(array('*'));
			$this->db->from('coop_district');
			$this->db->where("amphur_id = '{$amphur_id}'");
			$rs = $this->db->get()->result_array();
			$arr_data['rs'] = @$rs; 
			
			echo "<option value=''> - เลือกตำบล - </option>";
			if(!empty($rs)){
				foreach(@$rs as $key => $row){
					echo "<option  value=".$row['district_id'].">".$row['district_name']."</option>";
				}
			}
		}
         // อำเภอ

		// ตำบล
		$district_id = $_POST['district_id'];
		if ($district_id) {
			$this->db->select(array('*'));
			$this->db->from('coop_district');
			$this->db->where("district_id = '{$district_id}'");
			$rs = $this->db->get()->result_array();
			$district_code = @$rs[0]['district_code']; 
			
			
			$this->db->select(array('*'));
			$this->db->from('coop_zipcode');
			$this->db->where("district_code = '{$district_code}'");
			$rs = $this->db->get()->result_array();
			$arr_data['rs'] = @$rs; 
			
			if(!empty($rs)){
				foreach(@$rs as $key => $row){
					 echo $row['zipcode'];
				}
			}
		}
		// ตำบล
			
	}
	
	public function coop_address_save()
	{
		$type = @$_POST["type"] ;
		$type_add = @$_POST["type_add"] ;
		$id_edit = @$_POST["id"] ;
		
		$data_insert_province = array();
		$data_insert_district = array();
		$data_insert_zipcode = array();
		// post จังหวัด
		$data_insert_province['province_name'] = @$_POST["province_name"];
		// post จังหวัด

		// post อำเภอ
		$data_insert_amphur['province_id'] = @$_POST["province_id"];
		$data_insert_amphur['amphur_name'] = @$_POST["amphur_name"];
		// post อำเภอ

		// post ตำบล
		$data_insert_district['amphur_id'] = @$_POST["amphur_id"];
		$data_insert_district['district_name'] = @$_POST["district_name"];
		$data_insert_zipcode['zipcode'] = @$_POST["zipcode"];
		// post ตำบล


		// edit
		$district_id = @$_POST["district_id"];
		$zipcode_id = @$_POST["zipcode_id"];

		$type = @$_POST["type"] ;
		$type_add = @$_POST["type_add"] ;
		$id_edit = @$_POST["id"] ;
		
		if($type_add == 'add'){
			if ($type == "province") {			
				$this->db->select('MAX(province_code) as _max');
				$this->db->from('coop_province');
				$max = $this->db->get()->result_array();
				$province_code = @$max[0]["_max"] + 1 ;
			
			  
				$data_insert_province['province_code'] = @$province_code;				
				$this->db->insert('coop_province', $data_insert_province);
				//print_r($this->db->last_query());exit;			
			}elseif ($type == "amphur") {

				$this->db->select('MAX(amphur_code) as _max');
				$this->db->from('coop_amphur');
				$max = $this->db->get()->result_array();
				$amphur_code = @$max[0]["_max"] + 1 ;
				
				$data_insert_amphur['amphur_code'] = @$amphur_code;				
				$this->db->insert('coop_amphur', $data_insert_amphur);
			}else{

				$this->db->select('MAX(district_code) as _max');
				$this->db->from('coop_district');
				$max = $this->db->get()->result_array();
				$district_code = @$max[0]["_max"] + 1 ;
				
				$data_insert_district['district_code'] = @$district_code;
				$data_insert_district['province_id'] = @$_POST["province_id"];					
				$this->db->insert('coop_district', $data_insert_district);
				
				$district_id = $this->db->insert_id();
			}
			

			if (!empty($data_insert_zipcode['zipcode'])) {
				
				$this->db->select('district_code');
				$this->db->from('coop_district');
				$this->db->where("district_id = '{$district_id}'");
				$rs = $this->db->get()->result_array();
				$district_code = @$rs[0]['district_code'];
				
				$data_insert_zipcode['district_code'] = @$district_code;				
				$this->db->insert('coop_zipcode', $data_insert_zipcode);
				
			}
		}else{
			
			$this->db->where('district_id', $district_id);
			$this->db->update('coop_district', $data_insert_district);	
		
			$this->db->where('id', $zipcode_id);
			$this->db->update('coop_zipcode', $data_insert_zipcode);
			//print_r($this->db->last_query());exit;	
			
		}

		echo"<script> document.location.href='".PROJECTPATH."/setting_basic_data/coop_address' </script>";      
	}

	public function coop_account_month()
	{
		$arr_data = array();
		if($_POST)
		{
			$list=explode("|",$_POST['month']);
			$data_insert['accm_month_ini'] = $list[0];
			$data_insert['accm_month_name'] = $list[1];
			$data_insert['accm_date_modified'] =  date('Y-m-d H:i:s');

			$this->db->update("coop_account_period_setting", $data_insert);
			$this->center_function->toast("บันทึกข้อมูลเรียบร้อยแล้ว");
		}

			$this->db->select(array('*'));
			$this->db->from('coop_account_period_setting');
			$rs = $this->db->get()->result_array();
			$arr_data['row'] = @$rs[0];

		$this->libraries->template('setting_basic_data/coop_account_month',$arr_data);
	}

	public function coop_bank()
	{
		$arr_data = array();
		$id = @$_GET['id'];
		if(!empty($id)){
			$this->db->select(array('*'));
			$this->db->from('coop_bank');
			$this->db->where("bank_id = '{$id}'");
			$rs = $this->db->get()->result_array();
			$arr_data['row'] = @$rs[0]; 	
		}else{	
			
			$x=0;
			$join_arr = array();
			$join_arr[$x]['table'] = '(SELECT coop_bank_branch.bank_id,count(coop_bank_branch.bank_id) as total from coop_bank_branch 
								GROUP BY coop_bank_branch.bank_id) AS coop_bank_branch';
			$join_arr[$x]['condition'] = 'coop_bank.bank_id = coop_bank_branch.bank_id';
			$join_arr[$x]['type'] = 'left';
			
			$this->paginater_all->type(DB_TYPE);
			$this->paginater_all->select('coop_bank.*,coop_bank_branch.total');
			$this->paginater_all->main_table('coop_bank');
			$this->paginater_all->where("");
			$this->paginater_all->page_now(@$_GET["page"]);
			$this->paginater_all->per_page(20);
			$this->paginater_all->page_link_limit(20);
			$this->paginater_all->order_by('coop_bank.bank_id DESC');
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
		$this->libraries->template('setting_basic_data/coop_bank',$arr_data);
	}
	
	public function coop_bank_save()
	{
		$data_insert = array();
		$bank_id      = @$_POST["bank_id"];
		
		$data_insert['bank_id']      = @$_POST["bank_id"];		
		$data_insert['bank_name']    = @$_POST["bank_name"];
		$data_insert['bank_code']    = @$_POST["bank_code"];

		$type_add = @$_POST["type_add"] ;
		$id_edit = @$_POST["id"] ;

		// เช็คซ้ำ		
		$this->db->select('*');
		$this->db->from('coop_bank');
		$this->db->where("bank_id = '{$bank_id}' AND bank_id != '{$id_edit}'");
		$rs = $this->db->get()->result_array();
		$obj = @$rs[0];
		
		//print_r($this->db->last_query());exit;
		// เช็คซ้ำ

		if ($obj) {
			  //toastDanger("รหัสสาขานี้มีอยู่ในระบบอยู่แล้วกรุณาเปลี่ยนใหม่");
			  $this->center_function->toastDanger("รหัสสาขานี้มีอยู่ในระบบอยู่แล้วกรุณาเปลี่ยนใหม่");
			  echo"<script> document.location.href='".PROJECTPATH."/setting_basic_data/coop_bank' </script>";  
			  exit();
		}else{
			// add

			$table = "coop_bank";

			if ($type_add == 'add') {			
				$this->db->insert($table, $data_insert);
				$this->center_function->toast("บันทึกข้อมูลเรียบร้อยแล้ว");

			// add
			}else{
			// edit
				$this->db->where('bank_id', $id_edit);
				$this->db->update($table, $data_insert);	
				$this->center_function->toast("แก้ไขข้อมูลเรียบร้อยแล้ว");

			// edit
			}

		}
		echo"<script> document.location.href='".PROJECTPATH."/setting_basic_data/coop_bank' </script>"; 

	}
	
	function del_coop_basic_data(){	
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
	
	public function coop_bank_branch()
	{
		$arr_data = array();
		$bank_id = @$_GET['bank_id'];
		$id = @$_GET['id'];
		if(!empty($bank_id)){	
			$this->db->select('*');
			$this->db->from('coop_bank');
			$this->db->where("bank_id  = '{$bank_id}' ");
			$rs_name = $this->db->get()->result_array();
			$bank_name = $rs_name[0]['bank_name'];
			
			$x=0;
			$join_arr = array();
			$join_arr[$x]['table'] = 'coop_amphur';
			$join_arr[$x]['condition'] = 'coop_bank_branch.amphur_id = coop_amphur.amphur_id';
			$join_arr[$x]['type'] = 'left';
			$x++;
			$join_arr[$x]['table'] = 'coop_province';
			$join_arr[$x]['condition'] = 'coop_bank_branch.province_id = coop_province.province_id';
			$join_arr[$x]['type'] = 'left';
			
			$this->paginater_all->type(DB_TYPE);
			$this->paginater_all->select('coop_bank_branch.*,coop_amphur.amphur_name,coop_province.province_name');
			$this->paginater_all->main_table('coop_bank_branch');
			$this->paginater_all->where("bank_id  = '{$bank_id}'");
			$this->paginater_all->page_now(@$_GET["page"]);
			$this->paginater_all->per_page(20);
			$this->paginater_all->page_link_limit(20);
			$this->paginater_all->order_by('branch_id DESC');
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
			
			
			if (!empty($id)) {				
				$this->db->select(array('*'));
				$this->db->from('coop_bank_branch');
				$this->db->where("branch_id  = '{$id}' ");
				$rs = $this->db->get()->result_array();
				$arr_data['row'] = @$rs[0];
			}	
			$this->db->select(array('*'));
			$this->db->from('coop_province');
			$this->db->order_by('province_name ASC');
			$rs_province = $this->db->get()->result_array();
			$arr_data['rs_province'] = @$rs_province;

			$this->db->select(array('*'));
			$this->db->from('coop_amphur');
			$rs_amphur = $this->db->get()->result_array();
			$arr_data['rs_amphur'] = @$rs_amphur; 
			
		}
		$this->libraries->template('setting_basic_data/coop_bank_branch',$arr_data);
	}
	
	public function coop_bank_branch_save()
	{
		$data_insert = array();

		$bank_id =  @$_POST["bank_id"];
		$data_insert['bank_id'] =  @$_POST["bank_id"];
		$data_insert['branch_name'] = @$_POST["branch_name"];
		$data_insert['branch_code'] =  @$_POST["branch_code"];
		$data_insert['province_id'] = @$_POST["province_id"];
		$data_insert['amphur_id'] = @$_POST["amphur_id"];

		$type_add = @$_POST["type_add"] ;
		$id_edit = @$_POST["id"];

		$table = "coop_bank_branch";

		if ($type_add == 'add') {			
			$this->db->insert($table, $data_insert);
			$this->center_function->toast("บันทึกข้อมูลเรียบร้อยแล้ว");

		// add
		}else{
		// edit
			$this->db->where('branch_id', $id_edit);
			$this->db->update($table, $data_insert);	
			$this->center_function->toast("แก้ไขข้อมูลเรียบร้อยแล้ว");

		// edit
		}
		echo"<script> document.location.href='".PROJECTPATH."/setting_basic_data/coop_bank_branch?bank_id=$bank_id' </script>";      
	}
	
	function search_bank(){
		$this->db->select(array('*'));
		$this->db->from('coop_bank_branch');
	
		$this->db->where("
				(  bank_id = '".$this->input->post("bank_id")."'
							  AND branch_name LIKE '%".$this->input->post("search")."%'
					)
			  OR
					(
							  bank_id = '".$this->input->post("bank_id")."'
							  AND branch_name LIKE '%".$this->input->post("search")."%'
					)
			  OR
					(
							bank_id = '".$this->input->post("bank_id")."'
							AND branch_name LIKE '%".$this->input->post("search")."%'
					
					)	
			");
		$rs = $this->db->get()->result_array();
		$output = '';
		if(!empty($rs)){  
			$i= 1; 
			foreach($rs as $key => $row){
				$this->db->select('*');
				$this->db->from('coop_amphur');
				$this->db->where("amphur_id  = '{$row["amphur_id"]}' ");
				$rs_amphur = $this->db->get()->result_array();
				$row_amphur = @$rs_amphur[0];
				
				$this->db->select('*');
				$this->db->from('coop_province');
				$this->db->where("province_id  = '{$row["province_id"]}' ");
				$rs_province = $this->db->get()->result_array();
				$row_province = @$rs_province[0];
				
			   $output .= '  

					 <tr> 
					  <th scope="row">'.@$row['branch_code'].'</th>
					  <td>'.@$row['branch_name'].'</td> 
					  <td>'.@$row_amphur['amphur_name'].'</td>
					  <td>'.@$row_province['province_name'].'</td> 
					  <td>
					  <a href="?act=add&id="'.@$row["branch_id"].'&bank_id='.@$row['bank_id'].'">แก้ไข</a> | 
					  <span class="text-del del"  onclick="del_coop_basic_data(\''.@$row['branch_id'].'\',\''.@$row['bank_id'].'\')">ลบ</span>
					  </td> 

					  </tr>
			   '; 
		   $i++; 
		   }
			echo $output;  
		}else{
			$output .= '  

					  <tr>  
							 <td align="center" colspan="6"><h1>ไม่มีพบผลการค้นหา!</h1></td>  
						</tr>  
				 ';
			echo $output;  
		}  
		exit;
	}
	
	public function coop_user()
	{
		$arr_data = array();
		
		$user_id = @$_GET["id"] ; 
		if($user_id){
			$this->db->select(array('*'));
			$this->db->from('coop_user');
			$this->db->where("user_id  = '{$user_id}' ");
			$rs = $this->db->get()->result_array();
			$arr_data['row'] = @$rs[0];
			
			
			$this->db->select(array('*'));
			$this->db->from('coop_user_permission');
			$this->db->where("user_id  = '{$user_id}' ");
			$rs2 = $this->db->get()->result_array();
			if(!empty($rs2)){
				foreach(@$rs2 as $key => $row2){
					$admin_permissions[$row2["menu_id"]] = TRUE;
				}
			}
			$arr_data['admin_permissions'] = @$admin_permissions;
			
			$this->db->select(array('*'));
			$this->db->from('coop_notification_setting');
			$rs = $this->db->get()->result_array();
			$arr_data['row_notification'] = $rs;
			
			$this->db->select(array('*'));
			$this->db->from('coop_user_notification');
			$this->db->where("user_id  = '".$user_id."' ");
			$rs = $this->db->get()->result_array();
			if(!empty($rs)){
				foreach(@$rs as $key => $row){
					$user_notification[$row["notification_id"]] = '1';
				}
			}
			$arr_data['user_notification'] = @$user_notification;
			
		}else{
			$x=0;
			$join_arr = array();
			
			$this->paginater_all->type(DB_TYPE);
			$this->paginater_all->select('*');
			$this->paginater_all->main_table('coop_user');
			$this->paginater_all->where("");
			$this->paginater_all->page_now(@$_GET["page"]);
			$this->paginater_all->per_page(20);
			$this->paginater_all->page_link_limit(20);
			$this->paginater_all->order_by('user_type_id, user_id DESC');
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
				
		
		$this->libraries->template('setting_basic_data/coop_user',$arr_data);
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
	
	public function coop_user_save()
	{	
	//echo"<pre>";print_r($_POST);print_r($_COOKIE);exit;
		$data_insert = array();

		$user_id = @$_POST["user_id"] ; 
		
		$data_insert['username'] = @$_POST["username"] ;
		$data_insert['password'] = @$_POST["password"];
		$data_insert['user_name'] = @$_POST["user_name"];
		$data_insert['user_department'] = @$_POST["user_department"];
		$data_insert['user_status'] = @$_POST["user_status"] ;
		$data_insert['employee_id'] =  @$_POST["employee_id"] ;
		$data_insert['user_email'] =  @$_POST["user_email"] ;
		$data_insert['user_tel'] =  @$_POST["user_tel"] ;
		$data_insert['updatedate'] =  date("Y-m-d H:i:s");
		
		$output_dir = $_SERVER["DOCUMENT_ROOT"].PROJECTPATH."/assets/uploads/user_pic/";
		
		if(@$_POST['user_pic'] != '') {
			$user_pic = explode('/', $_POST['user_pic']); 
			$user_pic_name = $user_pic[(count($user_pic)-1)];
			$member_pic = $this->center_function->create_file_name($output_dir,$user_pic_name);
			@copy($_SERVER["DOCUMENT_ROOT"].PROJECTPATH."/assets/uploads/tmp/{$_COOKIE["IMG"]}", $_SERVER["DOCUMENT_ROOT"].PROJECTPATH."/assets/uploads/user_pic/{$member_pic}");
			@unlink($_SERVER["DOCUMENT_ROOT"].PROJECTPATH."/assets/uploads/tmp/".$user_pic_name);
			//@unlink($output_dir.$row[0]['user_pic']);
			setcookie("is_upload", "", time()-3600);
			setcookie("IMG", "", time()-3600);
			$data_insert['user_pic'] = $member_pic;
		}
		
		if(empty($user_id))
		{
			$data_insert['user_type_id'] =  '2';
			$data_insert['createdate'] =  date("Y-m-d H:i:s");
			$this->db->insert('coop_user', $data_insert);
			$this->center_function->toast("บันทึกข้อมูลเรียบร้อยแล้ว");
			
			$user_id = $this->db->insert_id();
			
			/*$sql = "SELECT * FROM coop_user WHERE user_id = '{$user_id}'";
			$rs = $mysqli->query($sql);
			$rowNew = $rs->fetch_assoc();
			$accesslog->add($_SESSION["USER_ID"], "insert", $_SERVER["PHP_SELF"], "coop_user", "", $rowNew, $user_id);
			*/
		}
		else
		{
			$this->db->where('user_id', $user_id);
			$this->db->update('coop_user', $data_insert);	
						
		
			$this->db->where('user_id', $user_id );
			$this->db->delete('coop_user_permission');
			
			$this->db->where('user_id', $user_id );
			$this->db->delete('coop_user_notification');

			$this->center_function->toast("แก้ไขข้อมูลเรียบร้อยแล้ว");	
			
			/*
			$sql = "SELECT * FROM coop_user WHERE user_id = '{$user_id}'";
			$rs = $mysqli->query($sql);
			$rowOld = $rs->fetch_assoc();
			
			$sql = "SELECT * FROM coop_user WHERE user_id = '{$user_id}'";
			$rs = $mysqli->query($sql);
			$rowNew = $rs->fetch_assoc();
			$accesslog->add($_SESSION["USER_ID"], "update", $_SERVER["PHP_SELF"], "coop_user", $rowOld, $rowNew, $user_id);
			*/
		}
	
		if(!empty($_POST["user_permissions"])) {
			$data_insert_permission = array();
			foreach($_POST["user_permissions"] as $key => $value) {
				$data_insert_permission = array();
				$data_insert_permission['user_id'] = @$user_id;				
				$data_insert_permission['menu_id'] = @$key;
				$this->db->insert('coop_user_permission', $data_insert_permission);
			}
		}
		if(!empty($_POST["user_notification"])) {
			$data_insert = array();
			foreach($_POST["user_notification"] as $key => $value) {
				$data_insert = array();
				$data_insert['user_id'] = @$user_id;				
				$data_insert['notification_id'] = @$value;
				$this->db->insert('coop_user_notification', $data_insert);
			}
		}
		
		echo"<script> document.location.href='".PROJECTPATH."/setting_basic_data/coop_user' </script>";      
	}
	
	public function search_employee()
	{
		$employee_id = @$_POST['employee_id'];
		$this->db->select('*');
		$this->db->from('coop_mem_apply');
		$this->db->where("employee_id  = '{$employee_id}' ");
		$rs = $this->db->get()->result_array();
		$row = @$rs[0];
		//print_r($this->db->last_query());
		if(!empty($row)){
			echo @$row['firstname_th']." ".@$row['lastname_th'];
		}else{
			echo "error";
		}
		exit;
	}

	function del_coop_user(){	
	
		$id = @$_POST["id"];
			
		$this->db->where('user_id', $id );
		$this->db->delete('coop_user');	
        

		$this->db->where('user_id', $id );
		$this->db->delete('coop_user_permission');
		$this->center_function->toast("ลบข้อมูลเรียบร้อยแล้ว");
		echo true;
		
	}
	
	public function coop_transactions_new()
	{
		$arr_data = array();
		$this->db->select(array('*'));
		$this->db->from('coop_money_type');
		$this->db->order_by('id ASC');
		$rs = $this->db->get()->result_array();
		$arr_data['rs']= @$rs;
			
		$this->libraries->template('setting_basic_data/coop_transactions_new',$arr_data);
	}

	public function coop_transactions_new_save()
	{
		$data_insert = array();	
		
		foreach(@$_POST['data'] as $key => $value){
			$data_insert['money_type_name_short'] = @$value['money_type_name_short'];
			$data_insert['money_type_name_eng'] = @$value['money_type_name_eng'];
			$data_insert['money_type_name_th'] = @$value['money_type_name_th'];
			$data_insert['money_type_name_th_short'] = @$value['money_type_name_th_short'];
				
			$this->db->where('id', $key);
			$this->db->update('coop_money_type', $data_insert);	
		}
			
		$this->center_function->toast("บันทึกข้อมูลเรียบร้อยแล้ว");	
			
		echo"<script> document.location.href='".PROJECTPATH."/setting_basic_data/coop_transactions_new' </script>";  
	}		
	
	public function chk_user(){
		if(@$_GET["username"] !=@$_GET["old_username"]) {
			$this->db->select('*');
			$this->db->from('coop_user');
			$this->db->where("username = '".@$_GET["username"]."'");
			$rs = $this->db->get()->result_array();
			$row = @$rs[0];

			if(@$row != '') {
				echo json_encode("Username ซ้ำ");
			}
			else {
				echo json_encode(TRUE);
			}
		}
		else {
			echo json_encode(TRUE);
		}
	}	
	
	public function coop_signature()
	{
		$arr_data = array();
		$id = @$_GET['id'];
		if(!empty($id)){
			$this->db->select(array('*'));
			$this->db->from('coop_signature');
			$this->db->where("signature_id = '{$id}'");
			$rs = $this->db->get()->result_array();
			$arr_data['row'] = @$rs[0]; 	
		}else{	
			$x=0;
			$join_arr = array();
			
			$this->paginater_all->type(DB_TYPE);
			$this->paginater_all->select('*');
			$this->paginater_all->main_table('coop_signature');
			$this->paginater_all->where("");
			$this->paginater_all->page_now(@$_GET["page"]);
			$this->paginater_all->per_page(20);
			$this->paginater_all->page_link_limit(20);
			$this->paginater_all->order_by('signature_id DESC');
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
		$this->libraries->template('setting_basic_data/coop_signature',$arr_data);
	}
	
	public function coop_signature_save()
	{
		$data_insert = array();
		$type_add = @$_POST["type_add"] ;
		$id_edit = @$_POST["id"];

		$this->db->select(array('*'));
		$this->db->from('coop_signature');
		$this->db->where("signature_id = '{$id_edit}'");
		$row = $this->db->get()->result_array();
		$row = @$row[0]; 
		
		
		if(!empty($_FILES["signature_1"]["tmp_name"])){
			if(@$row['signature_1']){
				@unlink( PATH . "/images/coop_signature/{$row['signature_1']}");	
			}
			
			$output_dir = $_SERVER["DOCUMENT_ROOT"].PROJECTPATH."/assets/images/coop_signature/";
			$new_file_name = $this->center_function->create_file_name($output_dir,$_FILES["signature_1"]['name']);
			copy($_FILES["signature_1"]['tmp_name'], $output_dir.$new_file_name);
			$data_insert['signature_1'] = @$new_file_name;
		}

		if(!empty($_FILES["signature_2"]["tmp_name"])){
			if(@$row['signature_2']){
				@unlink( PATH . "/images/coop_signature/{$row['signature_2']}");
			}			
			
			$output_dir = $_SERVER["DOCUMENT_ROOT"].PROJECTPATH."/assets/images/coop_signature/";
			$new_file_name = $this->center_function->create_file_name($output_dir,$_FILES["signature_2"]['name']);
			copy($_FILES["signature_2"]['tmp_name'], $output_dir.$new_file_name);
			$data_insert['signature_2'] = @$new_file_name;			
		}

		if(!empty($_FILES["signature_3"]["tmp_name"])){
			if(@$row['signature_3']){
				@unlink( PATH . "/images/coop_signature/{$row['signature_3']}");
			}			
			
			$output_dir = $_SERVER["DOCUMENT_ROOT"].PROJECTPATH."/assets/images/coop_signature/";
			$new_file_name = $this->center_function->create_file_name($output_dir,$_FILES["signature_3"]['name']);
			copy($_FILES["signature_3"]['tmp_name'], $output_dir.$new_file_name);
			$data_insert['signature_3'] = @$new_file_name;			
		}

        if(!empty($_FILES["signature_4"]["tmp_name"])){
            if(@$row['signature_4']){
                @unlink( PATH . "/images/coop_signature/{$row['signature_4']}");
            }

            $output_dir = $_SERVER["DOCUMENT_ROOT"].PROJECTPATH."/assets/images/coop_signature/";
            $new_file_name = $this->center_function->create_file_name($output_dir,$_FILES["signature_4"]['name']);
            copy($_FILES["signature_4"]['tmp_name'], $output_dir.$new_file_name);
            $data_insert['signature_4'] = @$new_file_name;
        }
		
		$data_insert['start_date']    = $this->center_function->ConvertToSQLDate(@$_POST["start_date"]);
		$data_insert['finance_name']    = @$_POST["finance_name"];
		$data_insert['receive_name']    = @$_POST["receive_name"];		
		$data_insert['manager_name']    = @$_POST["manager_name"];
        $data_insert['president_name']    = @$_POST["president_name"];
		$data_insert['updatetime']    = date('Y-m-d H:i:s');

		$table = "coop_signature";

//		exit;

		if ($type_add == 'add') {	
		// add
			$data_insert['createdatetime']  = date('Y-m-d H:i:s');
			$this->db->insert($table, $data_insert);
			$this->center_function->toast("บันทึกข้อมูลเรียบร้อยแล้ว");

		// add
		}else{
		// edit
			$this->db->where('signature_id', $id_edit);
			$this->db->update($table, $data_insert);	
			$this->center_function->toast("แก้ไขข้อมูลเรียบร้อยแล้ว");
			//print_r($this->db->last_query());exit;
		// edit
		}
		
		echo"<script> document.location.href='".PROJECTPATH."/setting_basic_data/coop_signature' </script>"; 

	}
	
	function check_date_signature(){
		$start_date = $this->center_function->ConvertToSQLDate(@$_POST["start_date"]);
		$id = @$_POST["id"];
		if(@$id){
			$where = " AND signature_id <> {$id}";
		}else{
			$where = "";
		}
		
		$this->db->select(array('*'));
		$this->db->from('coop_signature');
		$this->db->where("start_date = '{$start_date}' {$where}");
		$row = $this->db->get()->result_array();
		$row = @$row[0]; 
		if(@$row['start_date']){
			echo false;
		}else{
			echo true;
		}
		exit;
	}
	
	public function coop_deduct()
	{
		$arr_data = array();
		$id = @$_GET['id'];
		if(!empty($id)){
			if(@$_GET['act'] == 'order'){
				$this->db->select('t1.deduct_detail_id,t1.deduct_id,t1.ref_id,t1.deduct_detail_seq,t2.loan_name');
				$this->db->from('coop_deduct_detail AS t1');
				$this->db->join("coop_loan_name AS t2","t1.ref_id = t2.loan_name_id","inner");
				$this->db->where("t1.deduct_id = '{$id}'");
				$this->db->order_by("t1.deduct_detail_seq ASC");
				$rs_deduct_detail = $this->db->get()->result_array();
				$count_detail = 0;
				if(!empty($rs_deduct_detail)){
					foreach(@$rs_deduct_detail as $key => $detail){
						$count_detail++;
					}
				}				
				$arr_deduct_detail = array();		
				$arr_data['rs_detail'] = @$rs_deduct_detail;
				$arr_data['num_rows_detail'] = @$count_detail;
				
			}else{
				$this->db->select(array('*'));
				$this->db->from('coop_deduct');
				$this->db->where("deduct_id = '{$id}'");
				$rs = $this->db->get()->result_array();
				$arr_data['row'] = @$rs[0]; 	
				
				$this->db->select(array('ref_id'));
				$this->db->from('coop_deduct_detail');
				$this->db->where("deduct_id = '{$id}'");
				$rs_deduct_detail = $this->db->get()->result_array();
				$arr_deduct_detail = array();
				if(!empty($rs_deduct_detail)){
					foreach(@$rs_deduct_detail as $key => $detail){
						$arr_deduct_detail[$detail['ref_id']] = @$detail['ref_id'];
					}
				}			
				$arr_data['deduct_detail'] = @$arr_deduct_detail;
			}	
			
		}else{	
			$x=0;
			$join_arr = array();
			
			$this->paginater_all->type(DB_TYPE);
			$this->paginater_all->select('*');
			$this->paginater_all->main_table('coop_deduct');
			$this->paginater_all->where("");
			$this->paginater_all->page_now(@$_GET["page"]);
			$this->paginater_all->per_page(20);
			$this->paginater_all->page_link_limit(20);
			$this->paginater_all->order_by('deduct_seq ASC');
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
		$this->db->from('coop_account_list');
		$row = $this->db->get()->result_array();
		$arr_data['account_list'] = $row;
		
		$this->db->select(array('t1.type_id','t1.type_name'));
		$this->db->from('coop_deposit_type_setting as t1');
		$row = $this->db->get()->result_array();
		$arr_data['type_id'] = $row;
		
		$this->db->select(array('coop_loan_type.loan_type','coop_loan_type.id','coop_loan_name.loan_type_id','coop_loan_name.loan_name_id','coop_loan_name.loan_name'));
		$this->db->from('coop_loan_type');
		$this->db->join("coop_loan_name","coop_loan_name.loan_type_id=coop_loan_type.id","left");
		$rs_loan_type = $this->db->get()->result_array();
		$j = 0;
		if(!empty($rs_loan_type)){
			foreach(@$rs_loan_type as $key => $row3){
				$arr_loan_name[$row3["id"]]['id'] = $row3["id"];
				$arr_loan_name[$row3["id"]]['name'] = $row3["loan_type"];
				
				if($row3["loan_type_id"] == $row3["id"]){
					$arr_loan_name[$row3["id"]]['submenus'][$j]['id'] = $row3["loan_name_id"];
					$arr_loan_name[$row3["id"]]['submenus'][$j]['name'] = $row3["loan_name"];
					$j++;
				}
			}
		}
		$arr_data['rs_loan_name'] = @$arr_loan_name;
		
		$this->libraries->template('setting_basic_data/coop_deduct',$arr_data);
	}
	
	public function coop_deduct_save()
	{
		$data_insert = array();
		$type_add = @$_POST["type_add"] ;
		$id_edit = @$_POST["id"];

		$this->db->select(array('*'));
		$this->db->from('coop_deduct');
		$this->db->where("deduct_id = '{$id_edit}'");
		$row = $this->db->get()->result_array();
		$row = @$row[0]; 
		
		
		$data_insert['deduct_detail']    = @$_POST["deduct_detail"];
		$data_insert['deduct_type']    = @$_POST["deduct_type"];		
		$data_insert['deduct_format']    = @$_POST["deduct_format"];		
		$data_insert['deposit_type_id']  = @$_POST["deposit_type_id"];		
		$data_insert['deposit_amount']  = @$_POST["deposit_amount"];		
		$data_insert['updatetime']    = date('Y-m-d H:i:s');
		$data_insert['account_list_id']    = @$_POST["account_list_id"];		
		$table = "coop_deduct";
		
		if ($type_add == 'add') {	
		// add
			$this->db->select('MAX(deduct_seq) as _max');
			$this->db->from('coop_deduct');
			$max = $this->db->get()->result_array();
			$deduct_seq = @$max[0]["_max"] + 1 ;
			
			$data_insert['deduct_seq']    = @$deduct_seq;	
			$data_insert['createdatetime']  = date('Y-m-d H:i:s');
			$this->db->insert($table, $data_insert);
			$deduct_id = $this->db->insert_id();
			$this->center_function->toast("บันทึกข้อมูลเรียบร้อยแล้ว");

		// add
		}else{
		// edit
			$this->db->where('deduct_id', $id_edit);
			$this->db->update($table, $data_insert);	
			$deduct_id = @$_POST["id"];
			$this->center_function->toast("แก้ไขข้อมูลเรียบร้อยแล้ว");
			//print_r($this->db->last_query());exit;
		// edit
		}
		
		if(@$deduct_id){
			$this->db->where('deduct_id', $deduct_id );
			$this->db->delete('coop_deduct_detail');
				
			if(!empty($_POST['loan_type'])){
				foreach(@$_POST['loan_type'] AS $key=>$value){
					$data_insert = array();
					$data_insert['deduct_id']    = @$deduct_id;	
					$data_insert['ref_id']    = @$key;	
					$this->db->insert('coop_deduct_detail', $data_insert);
				}
			}
		}
		
		echo"<script> document.location.href='".PROJECTPATH."/setting_basic_data/coop_deduct' </script>"; 

	}
	
	public function coop_order_seq(){
		//echo '<pre>'; print_r($_GET); echo '</pre>'; exit;
		if(@in_array(@$_GET["do"],array("down","up"))) {
			$this->db->select(array('deduct_id','deduct_seq'));
			$this->db->from('coop_deduct');
			$this->db->order_by('deduct_seq ASC');
			$rs = $this->db->get()->result_array();
	
			$i = 0;
			if(!empty($rs)){
				foreach(@$rs as $key => $row){	
					if($row["deduct_id"]== @$_GET["id"]) {
						$pos = $i;
					} else{
						$array[$i++] = $row["deduct_id"];
					}
				}
			}

			if(@$_GET["do"]=="down") {
				$pos++;
			} else {
				$pos--;
			}
				

			$count = count($array)+1;
			
			for($i=0;$i<$count;$i++) {
				if($i == $pos) {
					$tmp[$i] = @$_GET["id"];
				} else {
					$tmp[$i] = @$array[0];
					array_shift($array);
				}
			}
			$data_insert = array();
			for($i = 0 ; $i<count($tmp) ;$i++) {
				$index = $i + 1 ; 
				$data_insert['deduct_seq']    = @$index;
				$this->db->where('deduct_id', @$tmp[$i]);
				$this->db->update('coop_deduct', $data_insert);	
				$this->center_function->toast("แก้ไขข้อมูลเรียบร้อยแล้ว");
			}
			echo"<script> document.location.href='".PROJECTPATH."/setting_basic_data/coop_deduct' </script>"; 
			exit();
		}
	}
	
	public function coop_holiday() {
		$arr_data = array();
		$y = @$_GET['y'];
		
		if(!empty($y)){
			$this->db->select(array('*'));
			$this->db->from('coop_calendar_work');
			$this->db->where("work_year = '{$y}'");
			$arr_data['row'] = $this->db->get()->row_array();
			
			$this->libraries->template('setting_basic_data/coop_holiday_detail',$arr_data);
		}else{
			$x=0;
			$join_arr = array();			
			
			$this->paginater_all->type(DB_TYPE);
			$this->paginater_all->select('*');
			$this->paginater_all->main_table('coop_calendar_work');
			$this->paginater_all->page_now(@$_GET["page"]);
			$this->paginater_all->per_page(20);
			$this->paginater_all->page_link_limit(20);
			$this->paginater_all->order_by('work_year DESC');
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
			
			$this->libraries->template('setting_basic_data/coop_holiday',$arr_data);
		}
	}
	
	public function save_coop_holiday() {
		$data_insert = array();
		$data_insert['work_year'] = @$_POST["work_year"];
		
		$table = "coop_calendar_work";
		$this->db->insert($table, $data_insert);
		
		$this->center_function->toast("บันทึกข้อมูลเรียบร้อยแล้ว");
		
		echo"<script> document.location.href='".PROJECTPATH."/setting_basic_data/coop_holiday' </script>";
	}
	
	function del_coop_holiday() {
		$id = @$_POST['id'];
		
		$this->db->where("work_year", $id);
		$this->db->delete("coop_calendar_work");
		
		$this->db->where("work_year", $id);
		$this->db->delete("coop_calendar_holiday");
		
		$this->center_function->toast("ลบเรียบร้อยแล้ว");
		echo true;
	}
	
	function get_coop_holiday() {
		$this->db->select(array("*"));
		$this->db->from("coop_calendar_holiday");
		$this->db->where("work_year = '".date("Y", strtotime($_GET["start"]))."' AND holiday_date BETWEEN '".$_GET["start"]."' AND '".$_GET["end"]."'");
		$rs = $this->db->get()->result_array();
		$data = array();
		foreach($rs as $key => $value) {
			array_push($data, array(
				"id" => $value["holiday_id"],
				"title" => $value["holiday_title"],
				"start" => $value["holiday_date"],
				"end" => $value["holiday_date"]
			));
		}
		
		echo json_encode($data);
		exit;
	}
	
	public function save_coop_holiday_detail() {
		if($_POST["type"] == 1) {
			$holidays = "";
			if(!empty($_POST["holidays"])) {
				$holidays = implode(",", $_POST["holidays"]);
			}
			
			$table = "coop_calendar_work";
			$data = array();
			$data["holidays"] = $holidays;
			$this->db->where("work_year = '{$_POST["work_year"]}'");
			$this->db->update($table, $data);
			
			$this->center_function->toast("บันทึกข้อมูลเรียบร้อยแล้ว");
			echo"<script> document.location.href='".PROJECTPATH."/setting_basic_data/coop_holiday?y={$_POST["work_year"]}' </script>";
		}
		elseif($_POST["type"] == 2) {
			$work_year = date("Y", strtotime($_POST["holiday_date"]));
			$data = array();
			$data['holiday_date'] = $_POST["holiday_date"];
			$data['holiday_title'] = $_POST["holiday_title"];
			$table = "coop_calendar_holiday";
			
			$this->db->select(array("*"));
			$this->db->from("coop_calendar_holiday");
			$this->db->where("work_year = '{$work_year}' AND holiday_date = '{$data['holiday_date']}'");
			if($row = $this->db->get()->row_array()) {
				$this->db->where("work_year = '{$work_year}' AND holiday_date = '{$data['holiday_date']}'");
				$this->db->update($table, $data);
			}
			else {
				$data['work_year'] = $work_year;
				$this->db->insert($table, $data);
			}
		}
	}
	
	public function del_coop_holiday_detail() {
		$work_year = date("Y", strtotime($_POST["holiday_date"]));
		$table = "coop_calendar_holiday";
		$this->db->where("work_year = '{$work_year}' AND holiday_date = '{$_POST["holiday_date"]}'");
		$this->db->delete($table);
	}
	
	function get_coop_holiday_list() {
		$this->db->select(array("*"));
		$this->db->from("coop_calendar_holiday");
		$this->db->where("work_year = '".$_POST["y"]."'");
		$this->db->order_by("holiday_date");
		$rs = $this->db->get()->result_array();
		$html = '<table class="table table-striped"> 
						<tbody>';
		foreach($rs as $key => $row) {
			$html .= '<tr><td class="text-left" width="15">'.($key + 1).'.</td><td class="text-left" width="100">'.$this->center_function->ConvertToThaiDate($row["holiday_date"]).'</td><td class="text-left">'.$row["holiday_title"].'</td></tr>';
		}
		$html .= '	</tbody> 
					</table>';
		
		echo json_encode([
			"html" => $html
		]);
		exit;
	}
	
	public function coop_order_detail_seq(){
		//echo '<pre>'; print_r($_GET); echo '</pre>'; exit;
		if(@in_array(@$_GET["do"],array("down","up"))) {
			$this->db->select(array('deduct_detail_id','deduct_detail_seq'));
			$this->db->from('coop_deduct_detail');
			$this->db->order_by('deduct_detail_seq ASC');
			$rs = $this->db->get()->result_array();
	
			$i = 0;
			if(!empty($rs)){
				foreach(@$rs as $key => $row){	
					if($row["deduct_detail_id"]== @$_GET["id_detail"]) {
						$pos = $i;
					} else{
						$array[$i++] = $row["deduct_detail_id"];
					}
				}
			}

			if(@$_GET["do"]=="down") {
				$pos++;
			} else {
				$pos--;
			}
				

			$count = count($array)+1;
			
			for($i=0;$i<$count;$i++) {
				if($i == $pos) {
					$tmp[$i] = @$_GET["id_detail"];
				} else {
					$tmp[$i] = @$array[0];
					array_shift($array);
				}
			}
			$data_insert = array();
			for($i = 0 ; $i<count($tmp) ;$i++) {
				$index = $i + 1 ; 
				$data_insert['deduct_detail_seq']    = @$index;
				$this->db->where('deduct_detail_id', @$tmp[$i]);
				$this->db->update('coop_deduct_detail', $data_insert);	
				$this->center_function->toast("แก้ไขข้อมูลเรียบร้อยแล้ว");
			}
			$param = "?act=".@$_GET['act']."&id=".@$_GET['id'];
			echo"<script> document.location.href='".PROJECTPATH."/setting_basic_data/coop_deduct".$param."' </script>"; 
			exit();
		}
	}
	
	function check_loan_type(){
		$loan_type_id = @$_POST['loan_type_id'];
		$deduct_format = @$_POST['deduct_format'];
		$deduct_id = @$_POST['deduct_id'];
		
		$this->db->select('t1.deduct_id,t1.deduct_detail,t2.ref_id');
		$this->db->from('coop_deduct AS t1');
		$this->db->join("coop_deduct_detail AS t2","t1.deduct_id = t2.deduct_id","left");
		$this->db->where("t1.deduct_format = '".$deduct_format."' AND t2.ref_id = '".$loan_type_id."' AND t1.deduct_id <> '".$deduct_id."'");
		$this->db->limit(1);
		$rs = $this->db->get()->result_array();
		$row = $rs[0];
		//echo '<pre>'; print_R($rs); echo '</pre>';
		if(@$row['ref_id'] != ''){
			echo false;
		}else{
			echo true;
		}			
		exit;
	}

    public function setting_boards() {
        $arr_data = array();

        $x=0;
        $join_arr = array();
        $join_arr[$x]['table'] = 'coop_loan_board_member t2';
        $join_arr[$x]['condition'] = 't1.id = t2.board_id AND t2.level = "manager"';
        $join_arr[$x]['type'] = 'left';
        $x++;
        $join_arr[$x]['table'] = 'coop_loan_board_member t3';
        $join_arr[$x]['condition'] = 't1.id = t3.board_id AND t3.level = "vice_manager"';
        $join_arr[$x]['type'] = 'left';

        $this->paginater_all->type(DB_TYPE);
        $this->paginater_all->select('t1.id, t1.start_at, t2.name as manager, t3.name as vice_manager');
        $this->paginater_all->main_table('coop_loan_board t1');
        $this->paginater_all->page_now(@$_GET["page"]);
        $this->paginater_all->per_page(20);
        $this->paginater_all->page_link_limit(20);
        $this->paginater_all->order_by('t1.start_at DESC, t1.created_at DESC');
        $this->paginater_all->join_arr($join_arr);
        $row = $this->paginater_all->paginater_process();

        $paging = $this->pagination_center->paginating($row['page'], $row['num_rows'], $row['per_page'], $row['page_link_limit'], $_GET);//$page_now = 1, $row_total = 1, $per_page = 20, $page_limit = 20

        $arr_data['num_rows'] = $row['num_rows'];
        $arr_data['paging'] = $paging;
        $arr_data['datas'] = $row['data'];
        $arr_data['i'] = $row['page_start'];

        $this->libraries->template('setting_basic_data/setting_boards',$arr_data);
    }

    public function ajax_get_board_data_by_id() {
        $id = $_GET['id'];
        $boards = $this->db->select("t1.id, t1.start_at, t2.level, t2.name")
                            ->from("coop_loan_board t1")
                            ->join("coop_loan_board_member t2", "t1.id = t2.board_id", "left")
                            ->where("t1.id = '".$id."'")
                            ->get()->result_array();
        $result = array();
        foreach($boards as $board) {
            $result['start_at'] = $this->center_function->mydate2date($board['start_at']);
            if($board['level'] == "board") {
                $result['boards'][] = $board['name'];
            } else {
                $result[$board['level']] = $board['name'];
            }
        }

        echo json_encode($result);
    }

    public function ajax_save_boards() {
        $process_timestamp = date("Y-m-d H:i:s");
        $board_id = $_POST['id'];
        if(empty($board_id)) {
            $data_insert = array();
            $data_insert['start_at'] = empty($_POST['start_at']) ? $this->center_function->ConvertToSQLDate($_POST['start_at']) : date("Y-m-d");
            $data_insert['created_at'] = $process_timestamp;
            $data_insert['updated_at'] = $process_timestamp;
            $this->db->insert('coop_loan_board', $data_insert);
            $board_id = $this->db->insert_id();
        } else {
            $data_update = array();
            $data_update['start_at'] = empty($_POST['start_at']) ? $this->center_function->ConvertToSQLDate($_POST['start_at']) : date("Y-m-d");
            $data_update['updated_at'] = $process_timestamp;
            $this->db->where("id = '".$board_id."'");
            $this->db->update('coop_loan_board', $data_update);

            $this->db->where('board_id', $board_id );
            $this->db->delete('coop_loan_board_member');
        }

        $data_inserts = array();
        if(!empty($_POST['manager'])) {
            $data_insert = array();
            $data_insert['board_id'] = $board_id;
            $data_insert['level'] = 'manager';
            $data_insert['name'] = $_POST['manager'];
            $data_insert['created_at'] = $process_timestamp;
            $data_insert['updated_at'] = $process_timestamp;
            $data_inserts[] = $data_insert;
        }

        if(!empty($_POST['vice_manager'])) {
            $data_insert = array();
            $data_insert['board_id'] = $board_id;
            $data_insert['level'] = 'vice_manager';
            $data_insert['name'] = $_POST['vice_manager'];
            $data_insert['created_at'] = $process_timestamp;
            $data_insert['updated_at'] = $process_timestamp;
            $data_inserts[] = $data_insert;
        }

        if(!empty($_POST['boards'])) {
            foreach($_POST['boards'] as $board) {
                $data_insert = array();
                $data_insert['board_id'] = $board_id;
                $data_insert['level'] = 'board';
                $data_insert['name'] = $board;
                $data_insert['created_at'] = $process_timestamp;
                $data_insert['updated_at'] = $process_timestamp;
                $data_inserts[] = $data_insert;
            }
		}

        $this->db->insert_batch('coop_loan_board_member', $data_inserts);
        echo "success";
    }
}
