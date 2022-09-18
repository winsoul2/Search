<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Benefits extends CI_Controller {
	function __construct()
	{
		parent::__construct();
	}

	public function benefits_request(){
		$arr_data = array();
		$member_id = @$_GET['id'];
		if($member_id!=''){
			$x=0;
			$join_arr = array();
			$join_arr[$x]['table'] = 'coop_benefits_type';
			$join_arr[$x]['condition'] = 'coop_benefits_request.benefits_type_id = coop_benefits_type.benefits_id';
			$join_arr[$x]['type'] = 'left';
			
			$x++;
			$join_arr[$x]['table'] = 'coop_benefits_transfer';
			$join_arr[$x]['condition'] = 'coop_benefits_request.benefits_request_id = coop_benefits_transfer.benefits_request_id';
			$join_arr[$x]['type'] = 'left';	
			
			$this->paginater_all->type(DB_TYPE);
			$this->paginater_all->select(array('coop_benefits_request.*','coop_benefits_type.benefits_name','coop_benefits_transfer.benefits_transfer_id','coop_benefits_transfer.createdatetime AS record_date','coop_benefits_transfer.transfer_status'));
			$this->paginater_all->main_table('coop_benefits_request');
			$this->paginater_all->where("member_id = '".$member_id."'");
			$this->paginater_all->page_now(@$_GET["page"]);
			$this->paginater_all->per_page(10);
			$this->paginater_all->page_link_limit(20);
			$this->paginater_all->order_by('benefits_request_id DESC');
			$this->paginater_all->join_arr($join_arr);
			$row = $this->paginater_all->paginater_process();
			//echo"<pre>";print_r($row);exit;
			$paging = $this->pagination_center->paginating($row['page'], $row['num_rows'], $row['per_page'], $row['page_link_limit'],$_GET);//$page_now = 1, $row_total = 1, $per_page = 20, $page_limit = 20
			$i = $row['page_start'];


			$arr_data['num_rows'] = $row['num_rows'];
			$arr_data['paging'] = $paging;
			$arr_data['row'] = $row['data'];
			$arr_data['i'] = $i;
			
		}else{
			$arr_data['row'] = array();
		}
		
		
		if($member_id){
			$this->db->select(array('*'));
			$this->db->from('coop_mem_apply');
			$this->db->where("member_id = {$member_id}");
			$rs = $this->db->get()->result_array();
			$arr_data['row_member'] = @$rs[0]; 
			
			$this->db->select('*');
			$this->db->from('coop_mem_req_resign');
			$this->db->where("member_id = '".$member_id."' AND resign_cause_id IN ('9','10') ");
			$this->db->order_by('req_resign_id DESC');
			$rs_resign = $this->db->get()->result_array();
			$row_resign = @$rs_resign[0];	
			
			$arr_data['row_member']['retry_status'] = (empty($row_resign)?'ยังไม่เกษียณ':'เกษียณแล้ว');
		}
		
		//ประเภทสวัสดิการสมาชิก
		$this->db->select(array('*'));
		$this->db->from('coop_benefits_type');
		$row = $this->db->get()->result_array();
		$arr_data['benefits_type'] = @$row;
		//print_r($this->db->last_query());exit;			
		//สถานะ
		$arr_data['benefits_status'] = array('0'=>'รอการอนุมัติ', '1'=>'อนุมัติ', '2'=>'ขอยกเลิก', '3'=>'อนุมัติยกเลิก', '4'=>'ชำระเงินแล้ว', '5'=>'ไม่อนุมัติ');
		$arr_data['status_bg_color'] = array('0'=>'#ff9800', '1'=>'#467542', '2'=>'#d50000', '3'=>'#d50000', '4'=>'#467542', '5'=>'#d50000');
		
		$this->db->select('bank_id, bank_name');
		$this->db->from('coop_bank');
		$row = $this->db->get()->result_array();
		$arr_data['bank'] = $row;
		
		$this->libraries->template('benefits/benefits_request',$arr_data);
	}

	function benefits_request_save(){
		$data_insert = array();
		$data = $this->input->post();
		
		$table = "coop_benefits_request";
		$id_edit = @$data["benefits_request_id"] ;		
		$member_id = @$data['member_id'];
		//print_r($data);
		$form_file_name = $data['file_name'];
		$year_now = (date('Y')+543);
		$this->db->select(array('MAX(runno) AS last_run'));
		$this->db->from('coop_benefits_request');
		$this->db->where("yy = '{$year_now}'");
		$rs = $this->db->get()->result_array();
		$row = @$rs[0]; 
		
		$run_now = 0;
		if(empty($id_edit)){
			if(@$row['last_run']){
				$run_now = $row['last_run']+1;	
			}else{
				$run_now = 1;
			}
			$runno = sprintf("%07d",$run_now);
			$benefits_no = $runno.'/'.$year_now;
			$data_insert['benefits_no'] = @$benefits_no;
			$data_insert['runno'] = @$run_now;
		}
		
		$data_insert['member_id'] = @$data['member_id'];	
		$data_insert['yy'] = @$year_now;
		$data_insert['benefits_type_id'] = @$data['benefits_type_id'];
		$data_insert['benefits_approved_amount'] = str_replace( ',', '',@$data['benefits_approved_amount']);
		$data_insert['benefits_check_condition'] = (@$data['benefits_check_condition'])?@$data['benefits_check_condition']:'0';
		$data_insert['benefits_status'] = '0';
		$data_insert['user_id'] = $_SESSION['USER_ID'];
		$data_insert['user_name'] = $_SESSION['USER_NAME'];
		$data_insert['updatetime'] = date('Y-m-d H:i:s');
		
		if($id_edit!=''){			
			$this->db->where('benefits_request_id', $id_edit);
			$this->db->update($table, $data_insert);
			$request_id = $id_edit;
		}else{			
			$data_insert['createdatetime'] = date('Y-m-d H:i:s');
			$this->db->insert($table, $data_insert);
			$request_id = $this->db->insert_id();
		}
		
		$output_dir = $_SERVER["DOCUMENT_ROOT"].PROJECTPATH."/assets/uploads/benefits_request/";
		//echo $output_dir;
		if(!@mkdir($output_dir,0,true)){
		   chmod($output_dir, 0777);
		}else{
		   chmod($output_dir, 0777);
		}
		if($_FILES['benefits_request_file']['name'][0]!=''){
            //print_r($_FILES['benefits_request_file']);
			foreach($_FILES['benefits_request_file']['name'] as $key_file => $value_file ){
				$fileName=array();
				$list_dir = array(); 
				$cdir = scandir($output_dir); 
				foreach ($cdir as $key => $value) { 
				   if (!in_array($value,array(".",".."))) { 
					  if (@is_dir(@$dir . DIRECTORY_SEPARATOR . @$value)){ 
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
				$explode_old_file = explode('.',$_FILES["benefits_request_file"]["name"][$key_file]);
				$new_file_name = date('Ymd')."_".$max_run_num.".".$explode_old_file[(count($explode_old_file)-1)];
				if(!is_array($_FILES["benefits_request_file"]["name"][$key_file]))
				{
						$fileName['file_name'] = $new_file_name;
						$fileName['file_type'] = $_FILES["benefits_request_file"]["type"][$key_file];
						//$fileName['file_old_name'] = $_FILES["benefits_request_file"]["name"][$key_file];
                        $type = explode(".",$_FILES["benefits_request_file"]["name"][$key_file]);
                        $fileName['file_old_name'] = $form_file_name[$key_file].".".$type[1];
						$fileName['file_path'] = $output_dir.$fileName['file_name'];
						move_uploaded_file($_FILES["benefits_request_file"]["tmp_name"][$key_file],$output_dir.$fileName['file_name']);
						
						$data_insert = array();
						$data_insert['benefits_request_id'] = @$request_id;
						$data_insert['file_name'] = @$fileName['file_name'];
						$data_insert['file_type'] = @$fileName['file_type'];
						$data_insert['file_old_name'] = @$fileName['file_old_name'];
						$data_insert['file_path'] = @$fileName['file_path'];
						//add coop_benefits_file_attach
						$this->db->insert('coop_benefits_file_attach', $data_insert);
				}
			}
		}

		if(!empty($data["card_id"]) || !empty($data["selected_choice"])) {
			$data_insert = array();
			$data_insert['benefits_request_id'] = $request_id;
			$data_insert['card_id'] = $data["card_id"];
			$data_insert['selected_choice'] = $data["selected_choice"];

			$req_detail = $this->db->select("*")
									->from("coop_benefits_request_detail")
									->where("benefits_request_id = '{$request_id}'")
									->get()->row();
			if(empty($req_detail)) {
				$this->db->insert('coop_benefits_request_detail', $data_insert);
			} else {
				$this->db->where('benefits_request_id',$request_id);
				$this->db->update('coop_benefits_request_detail',$data_insert);
			}
		}

		$this->center_function->toast("บันทึกข้อมูลเรียบร้อยแล้ว");
		echo"<script> document.location.href='".PROJECTPATH."/benefits/benefits_request?id={$member_id}' </script>";
		exit;
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
	
	function get_search_member(){
		$where = "
		 	(member_id LIKE '%".$this->input->post('search_text')."%'
		 	OR firstname_th LIKE '%".$this->input->post('search_text')."%'
			OR lastname_th LIKE '%".$this->input->post('search_text')."%') 
			AND member_status = '1'
		";
		$this->db->select(array('id','member_id','firstname_th','lastname_th','apply_date','mem_apply_id'));
		$this->db->from('coop_mem_apply');
		$this->db->where($where);
		$this->db->order_by('mem_apply_id DESC');
		$row = $this->db->get()->result_array();
		$arr_data['data'] = $row;
		$arr_data['form_target'] = $this->input->post('form_target');
		//echo"<pre>";print_r($arr_data['data']);exit;
		$this->load->view('benefits/get_search_member',$arr_data);
	}
	
	function get_benefits_type(){
		$today = date('Y-m-d');
		//
		$id = @$_POST['id'];
		$member_id = @$_POST['member_id'];
		$this->db->select(array('*'));
		$this->db->from('coop_benefits_type');
		$this->db->join("coop_benefits_type_detail","coop_benefits_type.benefits_id = coop_benefits_type_detail.benefits_id","left");
		$this->db->where("coop_benefits_type_detail.benefits_id = {$id} AND coop_benefits_type_detail.start_date <= '{$today}'");
		$this->db->order_by('coop_benefits_type_detail.start_date DESC');
		$this->db->limit(1);
		$rs = $this->db->get()->result_array();
		$row = @$rs[0];
		$row['start_date'] = (!empty($row['start_date'])?$this->center_function->mydate2date($row['start_date']):'');

		//Get Condition
		$benefit_total = 0;
		$row["conditions"]['conditions_text'] = "";
		if(!empty($row["age_grester_status"])) {
			$row["conditions"]['conditions_text'] .= "<label class='g24-col-sm-24 control-label text-left'>อายุตั้งแต่ ".$row["age_grester"]." ปีขึ้นไป</label>";
			$row["conditions"]['conditions_text'] .= "<input type='hidden' name='age_grester' id='age_grester' value='".$row["age_grester"]."'>";
		}
		if(!empty($row["member_age_grester_status"])) {
			$row["conditions"]['conditions_text'] .= "<label class='g24-col-sm-24 control-label text-left'>เป็นสมาชิกไม่น้อยกว่า ".$row["member_age_grester"]." ปีขึ้นไป</label>";
			$row["conditions"]['conditions_text'] .= "<input type='hidden' name='member_age_grester' id='member_age_grester' value='".$row["member_age_grester"]."'>";
		}
		if(!empty($row["request_time_status"])) {
			$row["conditions"]['conditions_text'] .= "<label class='g24-col-sm-24 control-label text-left'>";
			$row["conditions"]['conditions_text'] .= "ขอรับสิทธิ์ได้ไม่เกิน ".$row["request_time"]." ครั้ง";
			$row["conditions"]['conditions_text'] .= "<input type='hidden' name='request_time' id='request_time' value='".$row["request_time"]."'>";
			$row["conditions"]['conditions_text'] .= "<input type='hidden' name='request_time_unit' id='request_time_unit' value='".$row["request_time_unit"]."'>";
			if($row['request_time_unit'] == "per_year") {
				$row["conditions"]['conditions_text'] .= " ต่อปี";
			} elseif($row['request_time_unit'] == "per_person") {
				$row["conditions"]['conditions_text'] .= " ต่อคน";
			}
			$row["conditions"]['conditions_text'] .= "</label>";
		}

		if($row["special_con_selected"] == "payment_receive") {
			$row["conditions"]['conditions_text'] .= "<label class='g24-col-sm-24 control-label text-left'>ได้รับเงินสวัสดิการ ".number_format($row["payment_receive"])." บาท</label>";
			$benefit_total += $row["payment_receive"];
		} else if($row["special_con_selected"] == "retire_member_receive") {
			$row["conditions"]['conditions_text'] .= "<label class='g24-col-sm-24 control-label text-left'>";
			$row["conditions"]['conditions_text'] .= "สมาชิกเกษียณได้ จ่าย ".number_format($row["retire_member_receive"])." บาท คูณจำนวนปีที่เป็นสมาชิก";
			$row["conditions"]['conditions_text'] .= "</label>";

			$member = $this->db->select("*")
								->from("coop_mem_apply")
								->where("member_id = '".$member_id."'")
								->get()->row();
								// member_date
			$date1 = new DateTime($member->member_date);
			$date2 = new DateTime(date("Y-m-d"));
			$interval = date_diff($date1, $date2);
			$row["date1"] = $date1;
			$row["date2"] = $date2;
			$row["interval"] = $interval;
			$member_year = $interval->y;
			if($interval->m >= 6) {
				$member_year += 1;
			}
			$benefit_total += $row["retire_member_receive"] * $member_year;
		} else if($row["special_con_selected"] == "new_heir_receive") {
			$row["conditions"]['conditions_text'] .= "<label class='g24-col-sm-24 control-label text-left'>";
			$row["conditions"]['conditions_text'] .= "รับขวัญทายาทใหม่ จ่าย ".$row["new_heir_receive"]." บาท";
			$row["conditions"]['conditions_text'] .= "</label>";
			$row["conditions"]['conditions_text'] .= "<label class='g24-col-sm-7 control-label text-right'>";
			$row["conditions"]['conditions_text'] .= "เลขบัตรประชาชนทายาทที่ขอรับสวัสดิการ";
			$row["conditions"]['conditions_text'] .= "</label>";
			$row["conditions"]['conditions_text'] .= "<div class='g24-col-sm-5'>";
			$row["conditions"]['conditions_text'] .= "<input type='text' class='form-control' name='card_id' id='card_id' value='' maxlength='13' required title='กรุณาป้อน หมายเลขบัตรประชนของทายาท'>";
			$row["conditions"]["conditions_text"] .= "</div>";
			$benefit_total += $row["new_heir_receive"];
		} else if ($row["special_con_selected"] == "pass_away_default_receive") {
			//Check if have any debts
			$non_pay = $this->db->select("SUM(t2.non_pay_amount_balance) as total_non_pay")
								->from("coop_non_pay as t1")
								->join("coop_non_pay_detail as t2", "t1.non_pay_id = t2.non_pay_id", "inner")
								->where("t1.member_id = '".$member_id."' AND t1.non_pay_status =1 AND t2.deduct_code not in ('ATM', 'LOAN')")
								->get()->row();
			if($non_pay->total_non_pay <= 0) {
				//Add payment if do not have debts
				$benefit_total += $row["pass_away_default_receive"];
			}
			$year_periods = array();
			$row["conditions"]['conditions_text'] .= "<label class='g24-col-sm-24 control-label text-left'>";
			$row["conditions"]['conditions_text'] .= "ถึงแก่กรรม ได้รับค่าปลงศพ ".number_format($row["pass_away_default_receive"])." บาท โดยไม่มีหนี้กับสหกรณ์";
			$row["conditions"]['conditions_text'] .= "</label>";
			for($i = 1; $i <=5; $i++) {
				$row["conditions"]['conditions_text'] .= "<label class='g24-col-sm-24 control-label text-left'>";
				$row["conditions"]['conditions_text'] .= "<label class='g24-col-sm-1 control-label text-left'></label>";
				$row["conditions"]['conditions_text'] .= "เป็นสมาชิกไม่เกิน ".$row["pass_away_year_".$i]." ปี  จ่าย ".number_format($row["pass_away_receive_".$i])." บาท";
				$row["conditions"]['conditions_text'] .= "</label>";
				$year_periods[$i] = $row["pass_away_year_".$i];
			}
			$row["conditions"]['conditions_text'] .= "<label class='g24-col-sm-24 control-label text-left'>";
			$row["conditions"]['conditions_text'] .= "<label class='g24-col-sm-1 control-label text-left'></label>";
			$row["conditions"]['conditions_text'] .= "เป็นสมาชิกเกิน ".$row["pass_away_year_last"]." ปี ขึ้นไป เพิ่มปีละ ".number_format($row["pass_away_receive_last"])." บาท";
			$row["conditions"]['conditions_text'] .= "</label>";

			//Add membership payment
			//Check membership  period
			$member = $this->db->select("*")
								->from("coop_mem_apply")
								->where("member_id = '".$member_id."'")
								->get()->row();
								// member_date
			$date1 = new DateTime($member->member_date);
			$date2 = new DateTime(date("Y-m-d"));
			$interval = date_diff($date1, $date2);
			$row["date1"] = $date1;
			$row["date2"] = $date2;
			$row["interval"] = $interval;
			$member_year = $interval->y;
			if($interval->m >= 6) {
				$member_year += 1;
			}
			$in_period = 0;
			foreach($year_periods as $index=>$year) {
				if($year >= $member_year) {
					$in_period = 1;
					$benefit_total += $row["pass_away_receive_".$index];
					$row["conditions"]['conditions_text'] .= "<input type='hidden' name='selected_choice' id='selected_choice' value='".$index."'>";
					break;
				}
			}
			if(empty($in_period)) {
				$benefit_total += $row["pass_away_receive_5"] + (($member_year - $row["pass_away_year_5"]) * $row["pass_away_receive_last"]);
				$row["conditions"]['conditions_text'] .= "<input type='hidden' name='selected_choice' id='selected_choice' value='last'>";
			}
		} else if ($row["special_con_selected"] == "scholarship") {
			$row["conditions"]['conditions_text'] .= "<label class='g24-col-sm-24 control-label text-left'>";
			$row["conditions"]['conditions_text'] .= "ทุนส่งเสริมการศึกษาบุตร";
			$row["conditions"]['conditions_text'] .= "</label>";
			$row["conditions"]['conditions_text'] .= "<label class='g24-col-sm-24 control-label text-left'>";
			$row["conditions"]['conditions_text'] .= "<label class='g24-col-sm-1 control-label text-left'></label>";
			$row["conditions"]['conditions_text'] .= "<input type='radio' style='margin:5px;' class='scholarship_radio' data-amount='".number_format($row["scholarship_kindergarten"])."' id='kindergarten_radio' name='selected_choice'  value='scholarship_kindergarten'>";
			$row["conditions"]['conditions_text'] .= "อนุบาล ปีละ ".number_format($row["scholarship_kindergarten"])." บาท";
			$row["conditions"]['conditions_text'] .= "</label>";
			$row["conditions"]['conditions_text'] .= "<label class='g24-col-sm-24 control-label text-left'>";
			$row["conditions"]['conditions_text'] .= "<label class='g24-col-sm-1 control-label text-left'></label>";
			$row["conditions"]['conditions_text'] .= "<input type='radio' style='margin:5px;' class='scholarship_radio' data-amount='".number_format($row["scholarship_elementary"])."' id='elementary_radio' name='selected_choice'  value='scholarship_elementary'>";
			$row["conditions"]['conditions_text'] .= "ประถม ปีละ ".number_format($row["scholarship_elementary"])." บาท";
			$row["conditions"]['conditions_text'] .= "</label>";
			$row["conditions"]['conditions_text'] .= "<label class='g24-col-sm-24 control-label text-left'>";
			$row["conditions"]['conditions_text'] .= "<label class='g24-col-sm-1 control-label text-left'></label>";
			$row["conditions"]['conditions_text'] .= "<input type='radio' style='margin:5px;' class='scholarship_radio' data-amount='".number_format($row["scholarship_junior_high"])."' id='junior_high_radio' name='selected_choice'  value='scholarship_junior_high'>";
			$row["conditions"]['conditions_text'] .= "ม.ต้น ปีละ ".number_format($row["scholarship_junior_high"])." บาท";
			$row["conditions"]['conditions_text'] .= "</label>";
			$row["conditions"]['conditions_text'] .= "<label class='g24-col-sm-24 control-label text-left'>";
			$row["conditions"]['conditions_text'] .= "<label class='g24-col-sm-1 control-label text-left'></label>";
			$row["conditions"]['conditions_text'] .= "<input type='radio' style='margin:5px;' class='scholarship_radio' data-amount='".number_format($row["scholarship_senior_high"])."' id='senior_high_radio' name='selected_choice'  value='scholarship_senior_high'>";
			$row["conditions"]['conditions_text'] .= "ม.ปลาย/ปวช. ปีละ ".number_format($row["scholarship_senior_high"])." บาท";
			$row["conditions"]['conditions_text'] .= "</label>";
			$row["conditions"]['conditions_text'] .= "<label class='g24-col-sm-24 control-label text-left'>";
			$row["conditions"]['conditions_text'] .= "<label class='g24-col-sm-1 control-label text-left'></label>";
			$row["conditions"]['conditions_text'] .= "<input type='radio' style='margin:5px;' class='scholarship_radio' data-amount='".number_format($row["scholarship_bachelor"])."' id='bachelor_radio' name='selected_choice'  value='scholarship_bachelor'>";
			$row["conditions"]['conditions_text'] .= "ป.ตรี/ปวส. ปีละ ".number_format($row["scholarship_bachelor"])." บาท";
			$row["conditions"]['conditions_text'] .= "</label>";
			$row["conditions"]['conditions_text'] .= "<label class='g24-col-sm-7 control-label text-right'>";
			$row["conditions"]['conditions_text'] .= "เลขบัตรประชาชนทายาทที่ขอรับสวัสดิการ";
			$row["conditions"]['conditions_text'] .= "</label>";
			$row["conditions"]['conditions_text'] .= "<div class='g24-col-sm-5'>";
			$row["conditions"]['conditions_text'] .= "<input type='text' class='form-control' name='card_id' id='card_id' value='' maxlength='13' required title='กรุณาป้อน หมายเลขบัตรประชนของทายาท'>";
			$row["conditions"]["conditions_text"] .= "</div>";
			$year_periods[$i] = $row["pass_away_year_".$i];
		} else if ($row["special_con_selected"] == "atm_coop") {
			$row["conditions"]['conditions_text'] .= "<label class='g24-col-sm-24 control-label text-left'>";
			$row["conditions"]['conditions_text'] .= "ประสบอุบัติเหตุสำหรับผู้ถือบัตร ATM COOP ได้รับรวมกันสูงสุดไม่เกิน ".number_format($row["atm_coop_max_receive".$i])." บาท";
			$row["conditions"]['conditions_text'] .= "</label>";
			$row["conditions"]['conditions_text'] .= "<label class='g24-col-sm-24 control-label text-left'>";
			$row["conditions"]['conditions_text'] .= "<label class='g24-col-sm-1 control-label text-left'></label>";
			$row["conditions"]['conditions_text'] .= "<input type='radio' style='margin:5px;' class='dismemberment_radio' data-amount='".number_format($row["atm_coop_pass_away"])."' id='atm_coop_pass_away_radio' name='selected_choice'  value='atm_coop_pass_away'>";
			$row["conditions"]['conditions_text'] .= "เสียชีวิต ได้รับ ".number_format($row["atm_coop_pass_away".$i])." บาท";
			$row["conditions"]['conditions_text'] .= "</label>";
			$row["conditions"]['conditions_text'] .= "<label class='g24-col-sm-24 control-label text-left'>";
			$row["conditions"]['conditions_text'] .= "<label class='g24-col-sm-1 control-label text-left'></label>";
			$row["conditions"]['conditions_text'] .= "<input type='radio' style='margin:5px;' class='dismemberment_radio' data-amount='".number_format($row["atm_coop_tpd"])."' id='atm_coop_tpd_radio' name='selected_choice'  value='atm_coop_tpd'>";
			$row["conditions"]['conditions_text'] .= "กรณีทุพพลภาพถาวรสิ้นเชิง จ่าย ".number_format($row["atm_coop_tpd".$i])." บาท";
			$row["conditions"]['conditions_text'] .= "</label>";
			// $row["conditions"]['conditions_text'] .= "<label class='g24-col-sm-24 control-label text-left'>";
			// $row["conditions"]['conditions_text'] .= "<label class='g24-col-sm-1 control-label text-left'></label>";
			// $row["conditions"]['conditions_text'] .= "สูญเสียอวัยวะ";
			// $row["conditions"]['conditions_text'] .= "</label>";
			$row["conditions"]['conditions_text'] .= "<label class='g24-col-sm-24 control-label text-left'>";
			$row["conditions"]['conditions_text'] .= "<label class='g24-col-sm-1 control-label text-left'></label>";
			$row["conditions"]['conditions_text'] .= "<input type='radio' style='margin:5px;' class='dismemberment_radio' data-amount='".number_format($row["atm_coop_e"])."' id='atm_coop_e_radio' name='selected_choice'  value='atm_coop_e'>";
			$row["conditions"]['conditions_text'] .= "สูญเสียสายตาหนึ่งข้าง จ่าย ".number_format($row["atm_coop_e".$i])." บาท";
			$row["conditions"]['conditions_text'] .= "</label>";
			// $row["conditions"]['conditions_text'] .= "<label class='g24-col-sm-24 control-label text-left'>";
			// $row["conditions"]['conditions_text'] .= "<label class='g24-col-sm-2 control-label text-left'></label>";
			// $row["conditions"]['conditions_text'] .= "สูญเสียอวัยวะอื่นๆ";
			// $row["conditions"]['conditions_text'] .= "</label>";
			$row["conditions"]['conditions_text'] .= "<label class='g24-col-sm-24 control-label text-left'>";
			$row["conditions"]['conditions_text'] .= "<label class='g24-col-sm-1 control-label text-left'></label>";
			$row["conditions"]['conditions_text'] .= "<input type='radio' style='margin:5px;' class='dismemberment_radio' data-amount='".number_format($row["atm_coop_hhffee"])."' id='hhffee_radio' name='selected_choice'  value='atm_coop_hhffee'>";
			$row["conditions"]['conditions_text'] .= "มือสองข้างตั้งแต่ข้อมือ หรือเท้าสองข้างตั้งแต่ข้อเท้า หรือสูญเสียสายตาสองข้าง จ่าย ".number_format($row["atm_coop_hhffee"])." บาท";
			$row["conditions"]['conditions_text'] .= "</label>";
			$row["conditions"]['conditions_text'] .= "<label class='g24-col-sm-24 control-label text-left'>";
			$row["conditions"]['conditions_text'] .= "<label class='g24-col-sm-1 control-label text-left'></label>";
			$row["conditions"]['conditions_text'] .= "<input type='radio' style='margin:5px;' class='dismemberment_radio' data-amount='".number_format($row["atm_coop_hf"])."' id='hf_radio' name='selected_choice'  value='atm_coop_hf'>";
			$row["conditions"]['conditions_text'] .= "มือหนึ่งข้างตั้งแต่ข้อมือ และเท้าหนึ่งข้างตั้งแต่ข้อเท้า จ่าย ".number_format($row["atm_coop_hf"])." บาท";
			$row["conditions"]['conditions_text'] .= "</label>";
			$row["conditions"]['conditions_text'] .= "<label class='g24-col-sm-24 control-label text-left'>";
			$row["conditions"]['conditions_text'] .= "<label class='g24-col-sm-1 control-label text-left'></label>";
			$row["conditions"]['conditions_text'] .= "<input type='radio' style='margin:5px;' class='dismemberment_radio' data-amount='".number_format($row["atm_coop_he"])."' id='he_radio' name='selected_choice'  value='atm_coop_he'>";
			$row["conditions"]['conditions_text'] .= "มือหนึ่งข้างตั้งแต่ข้อมือ และสูญเสียสายตาหนึ่งข้าง จ่าย ".number_format($row["atm_coop_he"])." บาท";
			$row["conditions"]['conditions_text'] .= "</label>";
			$row["conditions"]['conditions_text'] .= "<label class='g24-col-sm-24 control-label text-left'>";
			$row["conditions"]['conditions_text'] .= "<label class='g24-col-sm-1 control-label text-left'></label>";
			$row["conditions"]['conditions_text'] .= "<input type='radio' style='margin:5px;' class='dismemberment_radio' data-amount='".number_format($row["atm_coop_fe"])."' id='fe_radio' name='selected_choice'  value='atm_coop_fe'>";
			$row["conditions"]['conditions_text'] .= "เท้าหนึ่งข้างตั้งแต่ข้อเท้า และสูญเสียสายตาหนึ่งข้าง จ่าย ".number_format($row["atm_coop_fe"])." บาท";
			$row["conditions"]['conditions_text'] .= "</label>";
			$row["conditions"]['conditions_text'] .= "<label class='g24-col-sm-24 control-label text-left'>";
			$row["conditions"]['conditions_text'] .= "<label class='g24-col-sm-1 control-label text-left'></label>";
			$row["conditions"]['conditions_text'] .= "<input type='radio' style='margin:5px;' class='dismemberment_radio' data-amount='".number_format($row["atm_coop_h"])."' id='h_radio' name='selected_choice'  value='atm_coop_h'>";
			$row["conditions"]['conditions_text'] .= "มือหนึ่ง ข้างตั้งแต่ข้อมือ จ่าย ".number_format($row["atm_coop_h"])." บาท";
			$row["conditions"]['conditions_text'] .= "</label>";
			$row["conditions"]['conditions_text'] .= "<label class='g24-col-sm-24 control-label text-left'>";
			$row["conditions"]['conditions_text'] .= "<label class='g24-col-sm-1 control-label text-left'></label>";
			$row["conditions"]['conditions_text'] .= "<input type='radio' style='margin:5px;' class='dismemberment_radio' data-amount='".number_format($row["atm_coop_f"])."' id='f_radio' name='selected_choice'  value='atm_coop_f'>";
			$row["conditions"]['conditions_text'] .= "เท้าหนึ่งข้างตั้งแต่ข้อเท้า จ่าย ".number_format($row["atm_coop_f"])." บาท";
			$row["conditions"]['conditions_text'] .= "</label>";
		} else if ($row["special_con_selected"] == "cremation_receive") {
			$row["conditions"]['conditions_text'] .= "<label class='g24-col-sm-24 control-label text-left'>งานศพ บิดา มารดา และบุตร จ่าย ".number_format($row["cremation_receive"])." บาท</label>";
			$row["conditions"]['conditions_text'] .= "<label class='g24-col-sm-5 control-label text-right'>";
			$row["conditions"]['conditions_text'] .= "เลขบัตรประชาชนผู้เสียชีวิต";
			$row["conditions"]['conditions_text'] .= "</label>";
			$row["conditions"]['conditions_text'] .= "<div class='g24-col-sm-6'>";
			$row["conditions"]['conditions_text'] .= "<input type='text' class='form-control' name='card_id' id='card_id' value='".$card_id."' maxlength='13' required title='กรุณาป้อน หมายเลขบัตรประชน'>";
			$row["conditions"]["conditions_text"] .= "</div>";
			$benefit_total += $row["cremation_receive"];
		}

		$row["benefit_total"] = $benefit_total;
        $row["benefit_total"] = 100;
        $sql = "SELECT * FROM coop_mem_apply where member_id = ".$member_id;
        $member_data =  $this->db->query($sql)->result_array();
        $member_data = $member_data[0];

        $apply_age = $this->center_function->diff_year($member_data['apply_date'],date('Y-m-d'));
        $member_age = $this->center_function->diff_year($member_data['birthday'],date('Y-m-d'));
        $sql = "SELECT * FROM coop_benefits_type_choice_cond t1
                INNER JOIN coop_benefits_type_choice t2 ON t1.type_choice_id = t2.id
                WHERE t2.benefit_detail_id = ".$id;
        $cons = $this->db->query($sql)->result_array();
        $amount = 0;
        $min = 0;
        $accept_con = 0;
        foreach ($cons as $con){
            $op = $con['cond_data_operation'];
            $value = $con['cond_data_value'];
            $con_amount = $con['amount'];
            if ($con['cond_data_code'] == "member_age"){
                $data_for_cal = $apply_age;
            } else if ($con['cond_data_code'] == "age"){
                $data_for_cal = $member_age;
            }
            if ($op == "grester_than_or_equa"){
                if ($data_for_cal >= $value){
                    $amount = $con_amount;
                    $accept_con++;
                }
            } else if ($op == "less_than"){
                if ($data_for_cal < $value && $data_for_cal > $min){
                    $amount = $con_amount;
                    $accept_con++;

                }
                $min = $value;
            } else if ($op == 'equal'){
                if ($data_for_cal == $value){
                    $amount = $con_amount;
                    $accept_con++;

                }
            } else if ($op == "grester_than"){
                if ($data_for_cal > $value){
                    $amount = $con_amount;
                    $accept_con++;

                }
            } else if ($op == "less_than_or_equal"){
                if ($data_for_cal <= $value && $data_for_cal > $min){
                    $amount = $con_amount;
                    $accept_con++;
                }
                //$min = $value;
            }
            $min = $value;
        }
        $row['POST'] = $_POST;
        $row['member_id'] = $member_id;
        $row['apply_age'] = $apply_age;
        $row['member_age'] = $member_age;
        $row['$member_data'] = $member_data;
        $row['data_for_cal'] = $data_for_cal;
        $row['cons'] = $cons;
        $row['accept_con'] = $accept_con;
        $row["benefit_total_text"] = number_format($amount,2);

		echo json_encode($row);
		exit();
	}	
	
	function get_benefits_request(){
		$id = @$_POST['id'];
		$today = date('Y-m-d');
		
		$this->db->select(array('coop_benefits_request.*','coop_benefits_type_detail.*','coop_mem_apply.*'));
		$this->db->from('coop_benefits_request');
		$this->db->join("coop_benefits_type_detail","coop_benefits_request.benefits_type_id = coop_benefits_type_detail.benefits_id","left");
		$this->db->join("coop_mem_apply","coop_mem_apply.member_id = coop_benefits_request.member_id","left");
		$this->db->where("coop_benefits_request.benefits_request_id = {$id} AND coop_benefits_type_detail.start_date <= '{$today}'");
		$this->db->order_by('coop_benefits_type_detail.start_date DESC');
		$this->db->limit(1);
		$rs = $this->db->get()->result_array();
		$row = @$rs[0];
		$row['age'] = (!empty($row['birthday']))?$this->center_function->diff_year($row['birthday'],date('Y-m-d')):'';
		$row['apply_age'] = (!empty($row['apply_date']))?$this->center_function->diff_year($row['apply_date'],date('Y-m-d')):'';
		$row['birthday'] = ((!empty($row['birthday']) && $row['birthday'] != '0000-00-00')?$this->center_function->mydate2date($row['birthday']):'');		
		$row['apply_date'] = ((!empty($row['apply_date']) && $row['apply_date'] != '0000-00-00')?$this->center_function->mydate2date($row['apply_date']):'');		
		$row['retry_date'] = ((!empty($row['retry_date']) && $row['retry_date'] != '0000-00-00')?$this->center_function->mydate2date($row['retry_date']):'');
		$row['retry_status'] = '';
		
		$this->db->select(array('*'));
		$this->db->from("coop_benefits_file_attach");
		$this->db->where("benefits_request_id = '".$id."'");
		$rs_file = $this->db->get()->result_array();
		@$row['coop_file_attach'] = array();
		if(!empty($rs_file)){
			foreach(@$rs_file as $key => $row_file){
				@$row['coop_file_attach'][] = @$row_file;
			}
		}

		//Get Condition
		$req_detail = $this->db->select("*")
								->from("coop_benefits_request_detail")
								->where("benefits_request_id = '{$id}'")
								->get()->row();
		// $selected_choice = !empty($req_detail)
		$selected_choice = null;
		$card_id = "";
		if(!empty($req_detail)) {
			$selected_choice = $req_detail->selected_choice;
			$card_id = $req_detail->card_id;
		}
		$benefit_total = 0;
		$row["conditions"]['conditions_text'] = "";
		if(!empty($row["age_grester_status"])) {
			$row["conditions"]['conditions_text'] .= "<label class='g24-col-sm-24 control-label text-left'>อายุตั้งแต่ ".$row["age_grester"]." ปีขึ้นไป</label>";
		}
		if(!empty($row["member_age_grester_status"])) {
			$row["conditions"]['conditions_text'] .= "<label class='g24-col-sm-24 control-label text-left'>เป็นสมาชิกไม่น้อยกว่า ".$row["member_age_grester"]." ปีขึ้นไป</label>";
		}
		if(!empty($row["request_time_status"])) {
			$row["conditions"]['conditions_text'] .= "<label class='g24-col-sm-24 control-label text-left'>";
			$row["conditions"]['conditions_text'] .= "ขอรับสิทธิ์ได้ไม่เกิน ".$row["member_age_grester"]." ครั้ง";
			if($row['request_time_unit'] == "per_year") {
				$row['conditions_text'] .= " ต่อปี";
			} elseif($row["conditions"]['request_time_unit'] == "per_person") {
				$row['conditions_text'] .= " ต่อคน";
			}
			$row["conditions"]['conditions_text'] .= "</label>";
		}

		if($row["special_con_selected"] == "payment_receive") {
			$row["conditions"]['conditions_text'] .= "<label class='g24-col-sm-24 control-label text-left'>ได้รับเงินสวัสดิการ ".number_format($row["payment_receive"])." บาท</label>";
			$benefit_total += $row["payment_receive"];
		} else if($row["special_con_selected"] == "retire_member_receive") {
			$row["conditions"]['conditions_text'] .= "<label class='g24-col-sm-24 control-label text-left'>";
			$row["conditions"]['conditions_text'] .= "สมาชิกเกษียณได้ จ่าย ".number_format($row["retire_member_receive"])." บาท คูณจำนวนปีที่เป็นสมาชิก";
			$row["conditions"]['conditions_text'] .= "</label>";

			$member = $this->db->select("*")
								->from("coop_mem_apply")
								->where("member_id = '".$member_id."'")
								->get()->row();
								// member_date
			$date1 = new DateTime($member->member_date);
			$date2 = new DateTime(date("Y-m-d"));
			$interval = date_diff($date1, $date2);
			$row["date1"] = $date1;
			$row["date2"] = $date2;
			$row["interval"] = $interval;
			$member_year = $interval->y;
			if($interval->m >= 6) {
				$member_year += 1;
			}
			$benefit_total += $row["retire_member_receive"] * $member_year;
		} else if($row["special_con_selected"] == "new_heir_receive") {
			$row["conditions"]['conditions_text'] .= "<label class='g24-col-sm-24 control-label text-left'>";
			$row["conditions"]['conditions_text'] .= "รับขวัญทายาทใหม่ จ่าย ".$row["new_heir_receive"]." บาท";
			$row["conditions"]['conditions_text'] .= "</label>";
			$row["conditions"]['conditions_text'] .= "<label class='g24-col-sm-7 control-label text-right'>";
			$row["conditions"]['conditions_text'] .= "เลขบัตรประชาชนทายาทที่ขอรับสวัสดิการ";
			$row["conditions"]['conditions_text'] .= "</label>";
			$row["conditions"]['conditions_text'] .= "<div class='g24-col-sm-5'>";
			$row["conditions"]['conditions_text'] .= "<input type='text' class='form-control' name='card_id' id='card_id' value='".$card_id."' maxlength='13' required title='กรุณาป้อน หมายเลขบัตรประชนของทายาท'>";
			$row["conditions"]["conditions_text"] .= "</div>";
			$benefit_total += $row["new_heir_receive"];
		} else if ($row["special_con_selected"] == "pass_away_default_receive") {
			//Check if have any debts
			$non_pay = $this->db->select("SUM(t2.non_pay_amount_balance) as total_non_pay")
								->from("coop_non_pay as t1")
								->join("coop_non_pay_detail as t2", "t1.non_pay_id = t2.non_pay_id", "inner")
								->where("t1.member_id = '".$member_id."' AND t1.non_pay_status =1 AND t2.deduct_code not in ('ATM', 'LOAN')")
								->get()->row();
			if($non_pay->total_non_pay <= 0) {
				//Add payment if do not have debts
				$benefit_total += $row["pass_away_default_receive"];
			}
			$year_periods = array();
			$row["conditions"]['conditions_text'] .= "<label class='g24-col-sm-24 control-label text-left'>";
			$row["conditions"]['conditions_text'] .= "ถึงแก่กรรม ได้รับค่าปลงศพ ".number_format($row["pass_away_default_receive"])." บาท โดยไม่มีหนี้กับสหกรณ์";
			$row["conditions"]['conditions_text'] .= "</label>";
			for($i = 1; $i <=5; $i++) {
				$row["conditions"]['conditions_text'] .= "<label class='g24-col-sm-24 control-label text-left'>";
				$row["conditions"]['conditions_text'] .= "<label class='g24-col-sm-1 control-label text-left'></label>";
				$row["conditions"]['conditions_text'] .= "เป็นสมาชิกไม่เกิน ".$row["pass_away_year_".$i]." ปี  จ่าย ".number_format($row["pass_away_receive_".$i])." บาท";
				$row["conditions"]['conditions_text'] .= "</label>";
				$year_periods[$i] = $row["pass_away_year_".$i];
			}
			$row["conditions"]['conditions_text'] .= "<label class='g24-col-sm-24 control-label text-left'>";
			$row["conditions"]['conditions_text'] .= "<label class='g24-col-sm-1 control-label text-left'></label>";
			$row["conditions"]['conditions_text'] .= "เป็นสมาชิกเกิน ".$row["pass_away_year_last"]." ปี ขึ้นไป เพิ่มปีละ ".number_format($row["pass_away_receive_last"])." บาท";
			$row["conditions"]['conditions_text'] .= "</label>";

			//Add membership payment
			//Check membership  period
			$member = $this->db->select("*")
								->from("coop_mem_apply")
								->where("member_id = '".$member_id."'")
								->get()->row();
								// member_date
			$date1 = new DateTime($member->member_date);
			$date2 = new DateTime(date("Y-m-d"));
			$interval = date_diff($date1, $date2);
			$row["date1"] = $date1;
			$row["date2"] = $date2;
			$row["interval"] = $interval;
			$member_year = $interval->y;
			$in_period = 0;
			foreach($year_periods as $index=>$year) {
				if($year >= $member_year) {
					$in_period = 1;
					$benefit_total += $row["pass_away_receive_".$index];
					$checked_text = $selected_choice == $index ? "checked" : "";
					$row["conditions"]['conditions_text'] .= "<input type='hidden' name='selected_choice' id='selected_choice' value='".$index."' {$checked_text}>";
					break;
				}
			}
			if(empty($in_period)) {
				$benefit_total += $row["pass_away_receive_5"] + (($member_year - $row["pass_away_year_5"]) * $row["pass_away_receive_last"]);
				$checked_text = $selected_choice == "last" ? "checked" : "";
				$row["conditions"]['conditions_text'] .= "<input type='hidden' name='selected_choice' id='selected_choice' value='last' {$checked_text}>";
			}
		} else if ($row["special_con_selected"] == "scholarship") {
			$row["conditions"]['conditions_text'] .= "<label class='g24-col-sm-24 control-label text-left'>";
			$row["conditions"]['conditions_text'] .= "ทุนส่งเสริมการศึกษาบุตร";
			$row["conditions"]['conditions_text'] .= "</label>";
			$row["conditions"]['conditions_text'] .= "<label class='g24-col-sm-24 control-label text-left'>";
			$row["conditions"]['conditions_text'] .= "<label class='g24-col-sm-1 control-label text-left'></label>";
			if($selected_choice == "scholarship_kindergarten") {
				$row["conditions"]['conditions_text'] .= "<input type='radio' style='margin:5px;' class='scholarship_radio' data-amount='".number_format($row["scholarship_kindergarten"])."' id='kindergarten_radio' name='selected_choice'  value='scholarship_kindergarten' checked>";
			} else {
				$row["conditions"]['conditions_text'] .= "<input type='radio' style='margin:5px;' class='scholarship_radio' data-amount='".number_format($row["scholarship_kindergarten"])."' id='kindergarten_radio' name='selected_choice'  value='scholarship_kindergarten'>";
			}
			$row["conditions"]['conditions_text'] .= "อนุบาล ปีละ ".number_format($row["scholarship_kindergarten"])." บาท";
			$row["conditions"]['conditions_text'] .= "</label>";
			$row["conditions"]['conditions_text'] .= "<label class='g24-col-sm-24 control-label text-left'>";
			$row["conditions"]['conditions_text'] .= "<label class='g24-col-sm-1 control-label text-left'></label>";
			if($selected_choice == "scholarship_elementary") {
				$row["conditions"]['conditions_text'] .= "<input type='radio' style='margin:5px;' class='scholarship_radio' data-amount='".number_format($row["scholarship_elementary"])."' id='elementary_radio' name='selected_choice'  value='scholarship_elementary' checked>";
			} else {
				$row["conditions"]['conditions_text'] .= "<input type='radio' style='margin:5px;' class='scholarship_radio' data-amount='".number_format($row["scholarship_elementary"])."' id='elementary_radio' name='selected_choice'  value='scholarship_elementary'>";
			}
			$row["conditions"]['conditions_text'] .= "ประถม ปีละ ".number_format($row["scholarship_elementary"])." บาท";
			$row["conditions"]['conditions_text'] .= "</label>";
			$row["conditions"]['conditions_text'] .= "<label class='g24-col-sm-24 control-label text-left'>";
			$row["conditions"]['conditions_text'] .= "<label class='g24-col-sm-1 control-label text-left'></label>";
			if($selected_choice == "scholarship_junior_high") {
				$row["conditions"]['conditions_text'] .= "<input type='radio' style='margin:5px;' class='scholarship_radio' data-amount='".number_format($row["scholarship_junior_high"])."' id='junior_high_radio' name='selected_choice'  value='scholarship_junior_high' checked>";
			} else {
				$row["conditions"]['conditions_text'] .= "<input type='radio' style='margin:5px;' class='scholarship_radio' data-amount='".number_format($row["scholarship_junior_high"])."' id='junior_high_radio' name='selected_choice'  value='scholarship_junior_high'>";
			}
			$row["conditions"]['conditions_text'] .= "ม.ต้น ปีละ ".number_format($row["scholarship_junior_high"])." บาท";
			$row["conditions"]['conditions_text'] .= "</label>";
			$row["conditions"]['conditions_text'] .= "<label class='g24-col-sm-24 control-label text-left'>";
			$row["conditions"]['conditions_text'] .= "<label class='g24-col-sm-1 control-label text-left'></label>";
			if($selected_choice == "scholarship_junior_high") {
				$row["conditions"]['conditions_text'] .= "<input type='radio' style='margin:5px;' class='scholarship_radio' data-amount='".number_format($row["scholarship_senior_high"])."' id='senior_high_radio' name='selected_choice'  value='scholarship_senior_high' checked>";
			} else {
				$row["conditions"]['conditions_text'] .= "<input type='radio' style='margin:5px;' class='scholarship_radio' data-amount='".number_format($row["scholarship_senior_high"])."' id='senior_high_radio' name='selected_choice'  value='scholarship_senior_high'>";
			}
			$row["conditions"]['conditions_text'] .= "ม.ปลาย/ปวช. ปีละ ".number_format($row["scholarship_senior_high"])." บาท";
			$row["conditions"]['conditions_text'] .= "</label>";
			$row["conditions"]['conditions_text'] .= "<label class='g24-col-sm-24 control-label text-left'>";
			$row["conditions"]['conditions_text'] .= "<label class='g24-col-sm-1 control-label text-left'></label>";
			if($selected_choice == "scholarship_bachelor") {
				$row["conditions"]['conditions_text'] .= "<input type='radio' style='margin:5px;' class='scholarship_radio' data-amount='".number_format($row["scholarship_bachelor"])."' id='bachelor_radio' name='selected_choice'  value='scholarship_bachelor' checked>";
			} else {
				$row["conditions"]['conditions_text'] .= "<input type='radio' style='margin:5px;' class='scholarship_radio' data-amount='".number_format($row["scholarship_bachelor"])."' id='bachelor_radio' name='selected_choice'  value='scholarship_bachelor'>";
			}
			$row["conditions"]['conditions_text'] .= "ป.ตรี/ปวส. ปีละ ".number_format($row["scholarship_bachelor"])." บาท";
			$row["conditions"]['conditions_text'] .= "</label>";
			$row["conditions"]['conditions_text'] .= "<label class='g24-col-sm-7 control-label text-right'>";
			$row["conditions"]['conditions_text'] .= "เลขบัตรประชาชนทายาทที่ขอรับสวัสดิการ";
			$row["conditions"]['conditions_text'] .= "</label>";
			$row["conditions"]['conditions_text'] .= "<div class='g24-col-sm-5'>";
			$row["conditions"]['conditions_text'] .= "<input type='text' class='form-control' name='card_id' id='card_id' value='".$card_id."' maxlength='13' required title='กรุณาป้อน หมายเลขบัตรประชนของทายาท'>";
			$row["conditions"]["conditions_text"] .= "</div>";
			$year_periods[$i] = $row["pass_away_year_".$i];
		} else if ($row["special_con_selected"] == "atm_coop") {
			$row["conditions"]['conditions_text'] .= "<label class='g24-col-sm-24 control-label text-left'>";
			$row["conditions"]['conditions_text'] .= "ประสบอุบัติเหตุสำหรับผู้ถือบัตร ATM COOP ได้รับรวมกันสูงสุดไม่เกิน ".number_format($row["atm_coop_max_receive".$i])." บาท";
			$row["conditions"]['conditions_text'] .= "</label>";
			$row["conditions"]['conditions_text'] .= "<label class='g24-col-sm-24 control-label text-left'>";
			$row["conditions"]['conditions_text'] .= "<label class='g24-col-sm-1 control-label text-left'></label>";
			if($selected_choice == "atm_coop_pass_away") {
				$row["conditions"]['conditions_text'] .= "<input type='radio' style='margin:5px;' class='dismemberment_radio' data-amount='".number_format($row["atm_coop_pass_away"])."' id='pass_radio' name='selected_choice'  value='atm_coop_pass_away' checked>";
			} else {
				$row["conditions"]['conditions_text'] .= "<input type='radio' style='margin:5px;' class='dismemberment_radio' data-amount='".number_format($row["atm_coop_pass_away"])."' id='pass_radio' name='selected_choice'  value='atm_coop_pass_away'>";
			}
			$row["conditions"]['conditions_text'] .= "เสียชีวิต ได้รับ ".number_format($row["atm_coop_pass_away".$i])." บาท";
			$row["conditions"]['conditions_text'] .= "</label>";
			$benefit_total += $row["atm_coop_pass_away"];
			$row["conditions"]['conditions_text'] .= "<label class='g24-col-sm-24 control-label text-left'>";
			$row["conditions"]['conditions_text'] .= "<label class='g24-col-sm-1 control-label text-left'></label>";
			if($selected_choice == "atm_coop_tpd") {
				$row["conditions"]['conditions_text'] .= "<input type='radio' style='margin:5px;' class='dismemberment_radio' data-amount='".number_format($row["atm_coop_tpd"])."' id='tpd_radio' name='selected_choice'  value='atm_coop_tpd' checked>";
			} else {
				$row["conditions"]['conditions_text'] .= "<input type='radio' style='margin:5px;' class='dismemberment_radio' data-amount='".number_format($row["atm_coop_tpd"])."' id='tpd_radio' name='selected_choice'  value='atm_coop_tpd'>";
			}
			$row["conditions"]['conditions_text'] .= "กรณีทุพพลภาพถาวรสิ้นเชิง จ่าย ".number_format($row["atm_coop_tpd".$i])." บาท";
			$row["conditions"]['conditions_text'] .= "</label>";
			$benefit_total += $row["atm_coop_pass_away"];
			// $row["conditions"]['conditions_text'] .= "<label class='g24-col-sm-24 control-label text-left'>";
			// $row["conditions"]['conditions_text'] .= "<label class='g24-col-sm-1 control-label text-left'></label>";
			// $row["conditions"]['conditions_text'] .= "สูญเสียอวัยวะ";
			// $row["conditions"]['conditions_text'] .= "</label>";
			$row["conditions"]['conditions_text'] .= "<label class='g24-col-sm-24 control-label text-left'>";
			$row["conditions"]['conditions_text'] .= "<label class='g24-col-sm-1 control-label text-left'></label>";
			if($selected_choice == "atm_coop_e") {
				$row["conditions"]['conditions_text'] .= "<input type='radio' style='margin:5px;' class='dismemberment_radio' data-amount='".number_format($row["atm_coop_e"])."' id='e_radio' name='selected_choice'  value='atm_coop_e' checked>";
			} else {
				$row["conditions"]['conditions_text'] .= "<input type='radio' style='margin:5px;' class='dismemberment_radio' data-amount='".number_format($row["atm_coop_e"])."' id='e_radio' name='selected_choice'  value='atm_coop_e'>";
			}
			$row["conditions"]['conditions_text'] .= "สูญเสียสายตาหนึ่งข้าง จ่าย ".number_format($row["atm_coop_e".$i])." บาท";
			$row["conditions"]['conditions_text'] .= "</label>";
			$benefit_total += $row["atm_coop_e"];
			// $row["conditions"]['conditions_text'] .= "<label class='g24-col-sm-24 control-label text-left'>";
			// $row["conditions"]['conditions_text'] .= "<label class='g24-col-sm-2 control-label text-left'></label>";
			// $row["conditions"]['conditions_text'] .= "สูญเสียอวัยวะอื่นๆ";
			// $row["conditions"]['conditions_text'] .= "</label>";
			$row["conditions"]['conditions_text'] .= "<label class='g24-col-sm-24 control-label text-left'>";
			$row["conditions"]['conditions_text'] .= "<label class='g24-col-sm-1 control-label text-left'></label>";
			if($selected_choice == "atm_coop_hhffee") {
				$row["conditions"]['conditions_text'] .= "<input type='radio' style='margin:5px;' class='dismemberment_radio' data-amount='".number_format($row["atm_coop_hhffee"])."' id='hhffee_radio' name='selected_choice'  value='atm_coop_hhffee' checked>";
			} else {
				$row["conditions"]['conditions_text'] .= "<input type='radio' style='margin:5px;' class='dismemberment_radio' data-amount='".number_format($row["atm_coop_hhffee"])."' id='hhffee_radio' name='selected_choice'  value='atm_coop_hhffee'>";
			}
			$row["conditions"]['conditions_text'] .= "มือสองข้างตั้งแต่ข้อมือ หรือเท้าสองข้างตั้งแต่ข้อเท้า หรือสูญเสียสายตาสองข้าง จ่าย ".number_format($row["atm_coop_hhffee"])." บาท";
			$row["conditions"]['conditions_text'] .= "</label>";
			$row["conditions"]['conditions_text'] .= "<label class='g24-col-sm-24 control-label text-left'>";
			$row["conditions"]['conditions_text'] .= "<label class='g24-col-sm-1 control-label text-left'></label>";
			if($selected_choice == "atm_coop_hf") {
				$row["conditions"]['conditions_text'] .= "<input type='radio' style='margin:5px;' class='dismemberment_radio' data-amount='".number_format($row["atm_coop_hf"])."' id='hf_radio' name='selected_choice'  value='atm_coop_hf' checked>";
			} else {
				$row["conditions"]['conditions_text'] .= "<input type='radio' style='margin:5px;' class='dismemberment_radio' data-amount='".number_format($row["atm_coop_hf"])."' id='hf_radio' name='selected_choice'  value='atm_coop_hf'>";
			}
			$row["conditions"]['conditions_text'] .= "มือหนึ่งข้างตั้งแต่ข้อมือ และเท้าหนึ่งข้างตั้งแต่ข้อเท้า จ่าย ".number_format($row["atm_coop_hf"])." บาท";
			$row["conditions"]['conditions_text'] .= "</label>";
			$row["conditions"]['conditions_text'] .= "<label class='g24-col-sm-24 control-label text-left'>";
			$row["conditions"]['conditions_text'] .= "<label class='g24-col-sm-1 control-label text-left'></label>";
			if($selected_choice == "atm_coop_he") {
				$row["conditions"]['conditions_text'] .= "<input type='radio' style='margin:5px;' class='dismemberment_radio' data-amount='".number_format($row["atm_coop_he"])."' id='he_radio' name='selected_choice'  value='atm_coop_he' checked>";
			} else {
				$row["conditions"]['conditions_text'] .= "<input type='radio' style='margin:5px;' class='dismemberment_radio' data-amount='".number_format($row["atm_coop_he"])."' id='he_radio' name='selected_choice'  value='atm_coop_he'>";
			}
			$row["conditions"]['conditions_text'] .= "มือหนึ่งข้างตั้งแต่ข้อมือ และสูญเสียสายตาหนึ่งข้าง จ่าย ".number_format($row["atm_coop_he"])." บาท";
			$row["conditions"]['conditions_text'] .= "</label>";
			$row["conditions"]['conditions_text'] .= "<label class='g24-col-sm-24 control-label text-left'>";
			$row["conditions"]['conditions_text'] .= "<label class='g24-col-sm-1 control-label text-left'></label>";
			if($selected_choice == "atm_coop_fe") {
				$row["conditions"]['conditions_text'] .= "<input type='radio' style='margin:5px;' class='dismemberment_radio' data-amount='".number_format($row["atm_coop_fe"])."' id='fe_radio' name='selected_choice'  value='atm_coop_fe' checked>";
			} else {
				$row["conditions"]['conditions_text'] .= "<input type='radio' style='margin:5px;' class='dismemberment_radio' data-amount='".number_format($row["atm_coop_fe"])."' id='fe_radio' name='selected_choice'  value='atm_coop_fe'>";
			}
			$row["conditions"]['conditions_text'] .= "เท้าหนึ่งข้างตั้งแต่ข้อเท้า และสูญเสียสายตาหนึ่งข้าง จ่าย ".number_format($row["atm_coop_fe"])." บาท";
			$row["conditions"]['conditions_text'] .= "</label>";
			$row["conditions"]['conditions_text'] .= "<label class='g24-col-sm-24 control-label text-left'>";
			$row["conditions"]['conditions_text'] .= "<label class='g24-col-sm-1 control-label text-left'></label>";
			if($selected_choice == "atm_coop_h") {
				$row["conditions"]['conditions_text'] .= "<input type='radio' style='margin:5px;' class='dismemberment_radio' data-amount='".number_format($row["atm_coop_h"])."' id='้_radio' name='selected_choice'  value='atm_coop_h' checked>";
			} else {
				$row["conditions"]['conditions_text'] .= "<input type='radio' style='margin:5px;' class='dismemberment_radio' data-amount='".number_format($row["atm_coop_h"])."' id='้_radio' name='selected_choice'  value='atm_coop_h'>";
			}
			$row["conditions"]['conditions_text'] .= "มือหนึ่ง ข้างตั้งแต่ข้อมือ จ่าย ".number_format($row["atm_coop_h"])." บาท";
			$row["conditions"]['conditions_text'] .= "</label>";
			$row["conditions"]['conditions_text'] .= "<label class='g24-col-sm-24 control-label text-left'>";
			$row["conditions"]['conditions_text'] .= "<label class='g24-col-sm-1 control-label text-left'></label>";
			if($selected_choice == "atm_coop_f") {
				$row["conditions"]['conditions_text'] .= "<input type='radio' style='margin:5px;' class='dismemberment_radio' data-amount='".number_format($row["atm_coop_f"])."' id='้_radio' name='selected_choice'  value='atm_coop_f' checked>";
			} else {
				$row["conditions"]['conditions_text'] .= "<input type='radio' style='margin:5px;' class='dismemberment_radio' data-amount='".number_format($row["atm_coop_f"])."' id='้_radio' name='selected_choice'  value='atm_coop_f'>";
			}
			$row["conditions"]['conditions_text'] .= "เท้าหนึ่งข้างตั้งแต่ข้อเท้า จ่าย ".number_format($row["atm_coop_f"])." บาท";
			$row["conditions"]['conditions_text'] .= "</label>";
		} else if ($row["special_con_selected"] == "cremation_receive") {
			$row["conditions"]['conditions_text'] .= "<label class='g24-col-sm-24 control-label text-left'>";
			$row["conditions"]['conditions_text'] .= "งานศพ บิดา มารดา และบุตร จ่าย ".$row["cremation_receive"]." บาท";
			$row["conditions"]['conditions_text'] .= "</label>";
			$row["conditions"]['conditions_text'] .= "<label class='g24-col-sm-6 control-label text-right'>";
			$row["conditions"]['conditions_text'] .= "เลขบัตรประชาชนผู้เสียชีวิต";
			$row["conditions"]['conditions_text'] .= "</label>";
			$row["conditions"]['conditions_text'] .= "<div class='g24-col-sm-5'>";
			$row["conditions"]['conditions_text'] .= "<input type='text' class='form-control' name='card_id' id='card_id' value='".$card_id."' maxlength='13' required title='กรุณาป้อน หมายเลขบัตรประชน'>";
			$row["conditions"]["conditions_text"] .= "</div>";
			$benefit_total += $row["cremation_receive"];
		}

		$row["benefit_total"] = $benefit_total;
		$row["benefit_total_text"] = number_format($benefit_total,2);

		echo json_encode($row);
		exit();
	}
	
	function ajax_delete_file_attach(){
		$this->db->select(array('*'));
		$this->db->from("coop_benefits_file_attach");
		$this->db->where("id = '".@$_POST['id']."'");
		$rs = $this->db->get()->result_array();
		$row = @$rs[0];

		//$attach_path = "../uploads/loan_attach/";
		$attach_path = $_SERVER["DOCUMENT_ROOT"].PROJECTPATH."/assets/uploads/benefits_request/";
		$file = @$attach_path.@$row['file_name'];
		unlink($file);

		$this->db->where("id", @$_POST['id'] );
		$this->db->delete("coop_benefits_file_attach");	
		if(@$rs){
			echo "success";
		}else{
			echo "error";
		}
		exit;
	}
	
	public function benefits_approve()
	{
		if($_POST['status_to']) {
			$status_to = $_POST['status_to'];
			foreach($_POST["benefits_request_id"] as $benefits_request_id) {
				$request = $this->db->select("*")
									->from("coop_benefits_request")
									->where("benefits_request_id = '".$benefits_request_id."'")
									->get()->row();
				if(($status_to == 1 && $request->benefits_status == 0)) {
					$data_update = array();
					$data_insert['benefits_status'] = $_POST['status_to'];
					$this->db->where('benefits_request_id', $benefits_request_id);
					$this->db->update('coop_benefits_request', $data_insert);
					$this->center_function->toast('บันทึกข้อมูลเรียบร้อยแล้ว');
				} else if ($status_to == "del" && $request->benefits_status == 5) {
					$this->db->where("benefits_request_id", $benefits_request_id );
					$this->db->delete("coop_benefits_request");
					$this->center_function->toast("ลบเรียบร้อยแล้ว");
				}
			}
		}
		if (@$_GET['status_to']) {
			$data_insert = array();
			$data_insert['benefits_status'] = @$_GET['status_to'];
			$this->db->where('benefits_request_id', @$_GET['id']);
			$this->db->update('coop_benefits_request', $data_insert);
			
			$this->center_function->toast('บันทึกข้อมูลเรียบร้อยแล้ว');
			echo "<script> document.location.href='".base_url(PROJECTPATH.'/benefits/benefits_approve')."' </script>";
		}
		$arr_data = array();

		$where = "";
		if(!empty($_GET["benefits_type_id_search"])) {
			$where .= " AND coop_benefits_request.benefits_type_id = '".$_GET["benefits_type_id_search"]."'";
		}
		if(!empty($_GET["start_date"])) {
			$start_date_arr = explode('/',$_GET['start_date']);
			$start_day = $start_date_arr[0];
			$start_month = $start_date_arr[1];
			$start_year = $start_date_arr[2];
			$start_year -= 543;
			$start_date = $start_year.'-'.$start_month.'-'.$start_day;
			$where .= " AND coop_benefits_request.createdatetime >= '".$start_date." 00:00:00.000'";
		}
		if(!empty($_GET["end_date"])) {
			$end_date_arr = explode('/',$_GET['end_date']);
			$end_day = $end_date_arr[0];
			$end_month = $end_date_arr[1];
			$end_year = $end_date_arr[2];
			$end_year -= 543;
			$end_date = $end_year.'-'.$end_month.'-'.$end_day;
			$where .= " AND coop_benefits_request.createdatetime <= '".$end_date." 23:59:59.000'";
		}

		$x=0;
		$join_arr = array();
		$join_arr[$x]['table'] = 'coop_mem_apply';
		$join_arr[$x]['condition'] = 'coop_mem_apply.member_id = coop_benefits_request.member_id';
		$join_arr[$x]['type'] = 'left';
		$x++;
		$join_arr[$x]['table'] = 'coop_user';
		$join_arr[$x]['condition'] = 'coop_benefits_request.user_id = coop_user.user_id';
		$join_arr[$x]['type'] = 'left';
		$x++;
		$join_arr[$x]['table'] = 'coop_benefits_type';
		$join_arr[$x]['condition'] = 'coop_benefits_request.benefits_type_id = coop_benefits_type.benefits_id';
		$join_arr[$x]['type'] = 'left';
		
		$this->paginater_all->type(DB_TYPE);
		$this->paginater_all->select('coop_benefits_request.*, coop_mem_apply.firstname_th, coop_mem_apply.lastname_th, coop_user.user_name, coop_benefits_type.benefits_name');
		$this->paginater_all->main_table('coop_benefits_request');
		$this->paginater_all->where("benefits_status IN('0','1','5')".$where);
		$this->paginater_all->page_now(@$_GET["page"]);
		$this->paginater_all->per_page(10);
		$this->paginater_all->page_link_limit(20);
		$this->paginater_all->order_by('createdatetime DESC');
		$this->paginater_all->join_arr($join_arr);
		$row = $this->paginater_all->paginater_process();
		//echo"<pre>";print_r($row);exit;
		$paging = $this->pagination_center->paginating($row['page'], $row['num_rows'], $row['per_page'], $row['page_link_limit']);//$page_now = 1, $row_total = 1, $per_page = 20, $page_limit = 20
		$i = $row['page_start'];


		$arr_data['num_rows'] = $row['num_rows'];
		$arr_data['paging'] = $paging;
		$arr_data['data'] = $row['data'];
		$arr_data['i'] = $i;
		
		//ประเภทสวัสดิการสมาชิก
		$this->db->select(array('*'));
		$this->db->from('coop_benefits_type');
		$row = $this->db->get()->result_array();
		$arr_data['benefits_type'] = @$row;
		
		$this->libraries->template('benefits/benefits_approve',$arr_data);
	}

	public function benefits_transfer()
	{
		$arr_data = array();

		$x=0;
		$join_arr = array();
		$join_arr[$x]['table'] = 'coop_mem_apply';
		$join_arr[$x]['condition'] = 'coop_mem_apply.member_id = coop_benefits_request.member_id';
		$join_arr[$x]['type'] = 'left';
		$x++;
		$join_arr[$x]['table'] = 'coop_user';
		$join_arr[$x]['condition'] = 'coop_benefits_request.user_id = coop_user.user_id';
		$join_arr[$x]['type'] = 'left';
		$x++;
		$join_arr[$x]['table'] = 'coop_benefits_transfer';
		$join_arr[$x]['condition'] = 'coop_benefits_request.benefits_request_id = coop_benefits_transfer.benefits_request_id';
		$join_arr[$x]['type'] = 'left';		
		$x++;
		$join_arr[$x]['table'] = 'coop_user AS coop_user_transfer';
		$join_arr[$x]['condition'] = 'coop_benefits_transfer.admin_id = coop_user_transfer.user_id';
		$join_arr[$x]['type'] = 'left';
		
		$this->paginater_all->type(DB_TYPE);
		$this->paginater_all->select('coop_benefits_request.*, coop_mem_apply.firstname_th, coop_mem_apply.lastname_th, coop_user.user_name,
									coop_benefits_transfer.createdatetime AS record_date,coop_benefits_transfer.admin_id,coop_user_transfer.user_name AS user_name_transfer,
									coop_benefits_transfer.transfer_status,coop_benefits_transfer.benefits_transfer_id,coop_benefits_transfer.cancel_date,coop_benefits_transfer.receipt_id,
									coop_benefits_transfer.account_id');
		$this->paginater_all->main_table('coop_benefits_request');
		$this->paginater_all->where("benefits_status IN ('1','4')");
		$this->paginater_all->page_now(@$_GET["page"]);
		$this->paginater_all->per_page(50);
		$this->paginater_all->page_link_limit(20);
		$this->paginater_all->order_by('createdatetime DESC');
		$this->paginater_all->join_arr($join_arr);
		$row = $this->paginater_all->paginater_process();
		//echo"<pre>";print_r($row);exit;
		$paging = $this->pagination_center->paginating($row['page'], $row['num_rows'], $row['per_page'], $row['page_link_limit']);//$page_now = 1, $row_total = 1, $per_page = 20, $page_limit = 20
		$i = $row['page_start'];
		//print_r($this->db->last_query());exit;

		$arr_data['num_rows'] = $row['num_rows'];
		$arr_data['paging'] = $paging;
		$arr_data['data'] = $row['data'];
		$arr_data['i'] = $i;
		
		//ประเภทสวัสดิการสมาชิก
		$this->db->select(array('*'));
		$this->db->from('coop_benefits_type');
		$row = $this->db->get()->result_array();
		$arr_data['benefits_type'] = @$row;
		
		$arr_data['transfer_status'] = array('0'=>'โอนเงินแล้ว', '1'=>'ยกเลิก');
		
		$this->db->select('bank_id, bank_name');
		$this->db->from('coop_bank');
		$row = $this->db->get()->result_array();
		$arr_data['bank'] = $row;

		$this->libraries->template('benefits/benefits_transfer',$arr_data);
	}
	
	public function get_benefits_transfer()
	{
		$id = @$_POST['id'];
		$today = date('Y-m-d');
		
		$this->db->select(array('*'));
		$this->db->from('coop_user');
		$rs_user = $this->db->get()->result_array();
		$rs_user = @$rs_user;
		$arr_user = array();
		foreach($rs_user AS $row_user){
			$arr_user[$row_user['user_id']] = $row_user['user_name'];
		}
		
		$this->db->select(array(
			'coop_benefits_request.benefits_no',
			'coop_benefits_request.benefits_approved_amount',
			'coop_benefits_request.user_id AS admin_request',
			'coop_benefits_type.benefits_name AS benefits_type_name',
			'coop_mem_apply.*','coop_benefits_transfer.account_id',
			'coop_benefits_transfer.admin_id AS admin_transfer',
			'coop_benefits_transfer.createdatetime',
			'coop_benefits_transfer.date_transfer',
			'coop_benefits_transfer.date_transfer AS time_transfer',
			'coop_benefits_transfer.bank_type AS bank_type_transfer',
			'coop_benefits_transfer.bank_id',
			'coop_benefits_transfer.bank_branch_id',
			'coop_benefits_transfer.bank_account_no',
			'coop_benefits_transfer.file_name'
		));
		$this->db->from('coop_benefits_request');
		$this->db->join("coop_benefits_type","coop_benefits_request.benefits_type_id = coop_benefits_type.benefits_id","left");
		$this->db->join("coop_mem_apply","coop_mem_apply.member_id = coop_benefits_request.member_id","left");
		$this->db->join("coop_benefits_transfer","coop_benefits_transfer.benefits_request_id = coop_benefits_request.benefits_request_id","left");
		$this->db->where("coop_benefits_request.benefits_request_id = {$id}");
		$rs = $this->db->get()->result_array();
		$row = @$rs[0];
		
		$row['bank_type'] = (!empty($row['bank_type_transfer']))?$row['bank_type_transfer']:$row['bank_type'];
		$row['bank_id'] = (!empty($row['bank_id']))?$row['bank_id']:$row['dividend_bank_id'];
		$row['bank_branch_id'] = (!empty($row['bank_branch_id']))?$row['bank_branch_id']:$row['dividend_bank_branch_id'];
		$row['bank_account_no'] = (!empty($row['bank_account_no']))?$row['bank_account_no']:$row['dividend_acc_num'];
		$row['admin_request'] = $arr_user[$row['admin_request']];
		$row['admin_transfer'] = $arr_user[(empty($row['admin_transfer']))?$_SESSION['USER_ID']:$row['admin_transfer']];
		$row['createdatetime'] = ((!empty($row['createdatetime']) && $row['retry_date'] != '0000-00-00')?$this->center_function->mydate2date($row['createdatetime']):$this->center_function->mydate2date(date('Y-m-d')));
		$row['date_transfer'] = ((!empty($row['date_transfer']) && $row['retry_date'] != '0000-00-00')?$this->center_function->mydate2date($row['date_transfer']):$this->center_function->mydate2date(date('Y-m-d')));
		$row['time_transfer'] = ((!empty($row['time_transfer']) && $row['retry_date'] != '0000-00-00')?date("H:i", strtotime($row['time_transfer'])):date('H:i'));
		
		echo json_encode($row);
		exit();
	}	
	
	public function get_account_list(){
		$member_id = @$_POST['member_id'];
		$benefits_request_id = @$_POST['benefits_request_id'];
		$arr_data = array();
		
		$this->db->select(array('coop_account_id'));
		$this->db->from('coop_mem_apply');
		$this->db->where("member_id = '".$member_id."'");
		$rs_mem = $this->db->get()->result_array();
		$mem_account_id = @$rs_mem[0]['coop_account_id'];
		
		$this->db->select(array('account_id'));
		$this->db->from('coop_benefits_transfer');
		$this->db->where("benefits_request_id = '".$benefits_request_id."'");
		$rs_transfer = $this->db->get()->result_array();
		$transfer_account_id = @$rs_transfer[0]['account_id'];
		$arr_data['account_id'] = (!empty($transfer_account_id))?$transfer_account_id :$mem_account_id;
		
		$this->db->select(array('*'));
		$this->db->from('coop_maco_account');
		$this->db->where("mem_id = '".$member_id."' AND account_status = '0'");
		$rs_account = $this->db->get()->result_array();
		$arr_data['rs_account'] = @$rs_account;

		$this->load->view('benefits/get_account_list',$arr_data);
	}
	
	function benefits_transfer_save(){
		//echo"<pre>";print_r($_POST);print_r($_FILES);exit;
		$process_timestamp = date('Y-m-d H:i:s');
		$process_date = date('Y-m-d');

		foreach($_POST["benefits_request_id"] as $benefits_request_id) {
			//Get need info
			$benefits_req = $this->db->select("coop_benefits_request.member_id, coop_benefits_request.benefits_approved_amount, coop_benefits_type.benefits_name")
										->from("coop_benefits_request")
										->join("coop_benefits_type" , "coop_benefits_type.benefits_id = coop_benefits_request.benefits_type_id", "left")
										->where("coop_benefits_request.benefits_request_id = '".$benefits_request_id."'")
										->get()->row();
			$member_id = $benefits_req->member_id;
			$benefits_approved_amount = $benefits_req->benefits_approved_amount;
			$benefits_type_name = $benefits_req->benefits_name;

			$account = $this->db->select("coop_maco_account.account_id")
								->from("coop_maco_account")
								->join("coop_deposit_type_setting", "coop_maco_account.type_id = coop_deposit_type_setting.type_id", "inner")
								->where("coop_maco_account.mem_id = '".$member_id."' AND coop_deposit_type_setting.type_code = 21")
								->get()->row();
			$account_id = $account->account_id;

			$member = $this->db->select("mobile")
								->from("coop_mem_apply")
								->where("member_id = '".$member_id."'")
								->get()->row();
			$mobile = $member->mobile;

			$table = "coop_benefits_transfer";
			$data_insert = array();
			//Generate Receipt
			$yymm = (date("Y")+543).date("m");
			$this->db->select(array('*'));
			$this->db->from('coop_receipt');
			$this->db->where("receipt_id LIKE '".$yymm."%'");
			$this->db->order_by("receipt_id DESC");
			$this->db->limit(1);
			$row_receipt = $this->db->get()->result_array();
			$row_receipt = @$row_receipt[0];

			if(@$row_receipt['receipt_id'] != '') {
				$id = (int) substr($row_receipt["receipt_id"], 6);
				$receipt_id = $yymm.sprintf("%06d", $id + 1);
			}else {
				$receipt_id = $yymm."000001";
			}

			$data_insert = array();
			$data_insert['receipt_id'] = $receipt_id;
			$data_insert['member_id'] = $member_id;
			$data_insert['admin_id'] = $_SESSION['USER_ID'];
			$data_insert['sumcount'] = $benefits_approved_amount;
			$data_insert['pay_type'] = 1;
			$data_insert['receipt_datetime'] = $process_timestamp;
			$this->db->insert('coop_receipt', $data_insert);

			$data_insert = array();
			$data_insert['receipt_id'] = $receipt_id;
			$data_insert['receipt_list'] = 12;
			$data_insert['receipt_count'] = $benefits_approved_amount;
			$this->db->insert('coop_receipt_detail', $data_insert);

			//Generate Finance
			$data_insert = array();
			$data_insert['receipt_id'] = $receipt_id;
			$data_insert['member_id'] = $member_id;
			$data_insert['account_list_id'] = 12;
			$data_insert['principal_payment'] = $benefits_approved_amount;
			$data_insert['interest'] = 0;
			$data_insert['total_amount'] = $benefits_approved_amount;
			$data_insert['payment_date'] = $process_date;
			$data_insert['loan_amount_balance'] = 0;
			$data_insert['createdatetime'] = $process_timestamp;
			$data_insert['transaction_text'] = $benefits_type_name;
			$data_insert['deduct_type'] = "all";
			$this->db->insert('coop_finance_transaction', $data_insert);

			$this->db->select(array('benefits_transfer_id'));
			$this->db->from('coop_benefits_transfer');
			$this->db->where("benefits_request_id = '".$benefits_request_id."'");
			$rs = $this->db->get()->result_array();
			$row = @$rs[0];
			$benefits_transfer_id = @$row["benefits_transfer_id"] ;	

			$output_dir = $_SERVER["DOCUMENT_ROOT"].PROJECTPATH."/assets/uploads/benefits_transfer/";
			$_tmpfile = $_FILES["file_name"];
			if(@$_tmpfile["tmp_name"]['name'] != '') {
				$new_file_name = $this->center_function->create_file_name($output_dir,$_tmpfile["name"]);
				if(!empty($new_file_name)) {
					copy($_tmpfile["tmp_name"], $output_dir.$new_file_name);
					@unlink($output_dir.$row['file_name']);
					$file_name = $new_file_name;
					$data['file_name'] = $file_name;
				}
			}

			$data_insert = array();
			$data_insert['benefits_request_id'] = @$benefits_request_id;
			$data_insert['account_id'] = $account_id;
			$data_insert['admin_id'] = $_SESSION['USER_ID'];
			$data_insert['transfer_status'] = '0';
			$data_insert['bank_type'] = @$data['bank_type'];
			$data_insert['bank_id'] = @$data['dividend_bank_id'];
			$data_insert['bank_branch_id'] = @$data['dividend_bank_branch_id'][0];
			$data_insert['bank_account_no'] = @$data['bank_account_no'];
			$data_insert['file_name'] = @$data['file_name']; //ชื่อรูปหลักฐานการโอนเงิน
			$date_transfer = $process_timestamp;
			$data_insert['date_transfer'] = @$date_transfer; //วันที่โอนเงิน
			$data_insert['receipt_id'] = $receipt_id;

			if($benefits_transfer_id == ''){
				$data_insert['createdatetime'] = $process_timestamp;
				$this->db->insert($table, $data_insert);
				$transfer_id = $this->db->insert_id();
			}else{
				$this->db->where('benefits_transfer_id', @$benefits_transfer_id);
				$this->db->update($table, $data_insert);
			
			}
			$data_insert = array();
			$data_insert['benefits_status'] ='4';
			$this->db->where('benefits_request_id', @$benefits_request_id);
			$this->db->update('coop_benefits_request', $data_insert);

			//เงินเข้าบัญชี
			$this->db->select('*');
			$this->db->from('coop_account_transaction');
			$this->db->where("account_id = '".$account_id."'");
			$this->db->order_by('transaction_time DESC');
			$this->db->limit(1);
			$row = $this->db->get()->result_array();
			if(!empty($row)){
				$balance = $row[0]['transaction_balance'];
			}else{
				$balance = 0;
			}
			$sum = $balance + $benefits_approved_amount;

			$data_insert = array();
			$data_insert['transaction_time'] = $date_transfer;
			$data_insert['transaction_list'] = 'BNF';
			$data_insert['transaction_withdrawal'] = '';
			$data_insert['transaction_deposit'] = $benefits_approved_amount;
			$data_insert['transaction_balance'] = $sum;
			$data_insert['user_id'] = $_SESSION['USER_ID'];
			$data_insert['account_id'] = $account_id;

			if ($this->db->insert('coop_account_transaction', $data_insert)) {
				$data_acc['coop_account']['account_description'] = $benefits_type_name." รหัสสมาชิก ".$member_id;
				$data_acc['coop_account']['account_datetime'] = $date_transfer;

				/*$i=0;
				$data_acc['coop_account_detail'][$i]['account_type'] = 'debit';
				$data_acc['coop_account_detail'][$i]['account_amount'] = $data['money'];
				$data_acc['coop_account_detail'][$i]['account_chart_id'] = '10100';
				$i++;
				$data_acc['coop_account_detail'][$i]['account_type'] = 'credit';
				$data_acc['coop_account_detail'][$i]['account_amount'] = $data['money'];
				$data_acc['coop_account_detail'][$i]['account_chart_id'] = '20100';
				$this->account_transaction->account_process($data_acc);*/
			}

			//ส่ง SMS ไปตามเบอร์สมาชิก
			//$mobile = @$data['mobile'];
			if(!empty($mobile)){
				//$bank_account_no = (@$data['bank_type'] == '1')?@$account_id:@$data['bank_account_no'];
				//$bank_account_no = 'xxx'.substr($bank_account_no, 0, 3).'';
			 $bank_account_no = $account_id;
				$msg = "สหกรณ์ทำการโอนเงินสวัสดิการเรียบร้อยแล้ว\n";
				$msg .= "เข้า บช. ".$bank_account_no." จำนวน ".number_format(@$benefits_approved_amount,2)."  บาท\n";
				$msg .= '('.date('j/n/Y H:i').')';
				$status_sms = $this->center_function->send_sms($mobile, $msg);
			}

		}
		$this->center_function->toast("บันทึกข้อมูลเรียบร้อยแล้ว");
		echo"<script> document.location.href='".PROJECTPATH."/benefits/benefits_transfer' </script>";
		exit;
	}
	
	function coop_transfer_cancel(){	
		$benefits_request_id = @$_GET['benefits_request_id'];
		$benefits_transfer_id = @$_GET['benefits_transfer_id'];
		
		$data_insert = array();
		$data_insert['transfer_status'] = @$_GET['status_to'];
		$data_insert['cancel_date'] = date('Y-m-d H:i:s');	 //วันที่ขอยกเลิก	
		$this->db->where('benefits_transfer_id', @$benefits_transfer_id);		
		$this->db->update('coop_benefits_transfer', $data_insert);
		
		$data_insert = array();
		$data_insert['benefits_status'] ='1';
		$this->db->where('benefits_request_id', @$benefits_request_id);
		$this->db->update('coop_benefits_request', $data_insert);

		$this->center_function->toast("ลบข้อมูลเรียบร้อยแล้ว");
		echo true;
		
	}

	public function ajax_check_benefits_req_conditions() {
	 echo $this->check_benefits_req_conditions($_POST);
	}

	public function check_benefits_req_conditions($data) {
		$warning_text = "";
		//Get benefit detail
		$today = date('Y-m-d');
		$id = $data['id'];
		$request_id = $data["benefits_request_id"];
		$this->db->select(array('coop_benefits_type_detail.updatetime', 'coop_benefits_type_detail.benefits_id'));
		$this->db->from('coop_benefits_type');
		$this->db->join("coop_benefits_type_detail","coop_benefits_type.benefits_id = coop_benefits_type_detail.benefits_id","left");
		$this->db->where("coop_benefits_type_detail.benefits_id = {$id} AND coop_benefits_type_detail.start_date <= '{$today}'");
		$this->db->order_by('coop_benefits_type_detail.start_date DESC');
		$this->db->limit(1);
		$rs = $this->db->get()->result_array();
		$row = $rs[0];

		// if($data["lastest_condition_created"] != $row["updatetime"]) {
		// 	$warning_text .= " - มีการเปลี่ยนแปลงเงื่อนไข\n";
		// }

		//Check non pay
		$non_pay = $this->db->select("*")->from("coop_non_pay")->where("non_pay_status IN (1,3,6) AND member_id = '".$data["member_id"]."' AND non_pay_amount_balance > 0")->get()->row();
		if(!empty($non_pay)) {
			$warning_text .= " - สมาชิกมีหนี้คงค้าง\n";
		}

		if($id == "13") {
			$benefits_req = $this->db->select("t1.benefits_request_id")
										->from("coop_benefits_request as t1")
										->where("t1.member_id = '".$data["member_id"]."' AND t1.benefits_type_id in (3,11) AND t1.benefits_status not in (3,5,6)")
										->get()->row();
			if(!empty($benefits_req)) {
				$warning_text .= " - สมาชิกที่เคยขอทุนให้บุตรแล้ว จะขอสวัสดิการสำหรับสมาชิกไม่มีบุตร หรือเป็นโสดไม่ได้\n";
			}
		}

		if(!empty($data["card_id"])) {
			$where_req_id = !empty($request_id) ? " AND t2.benefits_request_id != '".$request_id."'" : "";
			$benefits_req = $this->db->select("t1.benefits_request_id")
										->from("coop_benefits_request as t1")
										->join("coop_benefits_request_detail as t2", "t1.benefits_request_id = t2.benefits_request_id", "inner")
										->where("t1.benefits_type_id = '".$id."' AND t1.benefits_status not in (3,5,6) AND t2.card_id = '".$data["card_id"]."'".$where_req_id)
										->get()->row();

			if(!empty($benefits_req) && $id != 11) {
				$warning_text .= " - หมายเลขบัตรประชาชนนี้ถูกใช้ไปแล้ว\n";
			}

			if(!($this->checkPID($data["card_id"]))) {
				$warning_text .= " - หมายเลขบัตรประชาชนไม่ถูกต้อง\n";
			}
		}

		if(!empty($data["age_grester"])) {
			$member = $this->db->select("*")
								->from("coop_mem_apply")
								->where("member_id = '".$data["member_id"]."'")
								->get()->row();
			$date1 = new DateTime($member->birthday);
			$date2 = new DateTime(date("Y-m-d"));
			$interval = date_diff($date1, $date2);
			$row["date1"] = $date1;
			$row["date2"] = $date2;
			$row["interval"] = $interval;
			$member_age = $interval->y;
			if($member_age < $data["age_grester"]) {
				$warning_text .= " - สมาชิกมีอายุน้อยกว่าที่กำหนด\n";
			}
		}

		if(!empty($data["member_age_grester"])) {
			$member = $this->db->select("*")
								->from("coop_mem_apply")
								->where("member_id = '".$data["member_id"]."'")
								->get()->row();
			$date1 = new DateTime($member->member_date);
			$date2 = new DateTime(date("Y-m-d"));
			$interval = date_diff($date1, $date2);
			$row["date1"] = $date1;
			$row["date2"] = $date2;
			$row["interval"] = $interval;
			$member_year = $interval->y;
			if($interval->m >= 6) {
				$member_year += 1;
			}
			if($member_year < $data["member_age_grester"]) {
				$warning_text .= " - ระยะเวลาการเป็นสมาชิกน้อยกว่าที่กำหนด\n";
			}
		}

		if(!empty($data["request_time"])) {
			if($data["request_time_unit"] == "per_person") {
				$requests = $this->db->select("benefits_request_id")
										->from("coop_benefits_request")
										->where("member_id = '".$data["member_id"]."' AND benefits_type_id = '".$id."' AND benefits_status not in (3,5,6)")
										->get()->result_array();
				if(count($requests) >= $data["request_time"]) {
					$warning_text .= " - ขอรับสิทธิ์มากกว่าที่กำหนด\n";
				}
			} else if ($data["request_time_unit"] == "per_year") {
				if($id == 11) {
					//specific period check fro scholarship
					$current_day = date('Y-m-d');
					$req_details = $this->db->select("*")
											->from("coop_benefits_type_detail as t1")
											->where("t1.benefits_id = '".$id."' AND t1.start_date <= '".$current_day."'")
											->order_by("t1.start_date")
											->get()->result_array();
					$req_detail = $$req_details[0];
					$current_month = date('m');
					$period_year_start = $req_detail->scholarship_period_month_start <= $current_month ? date('Y') : date('Y') - 1;
					$period_start = date($period_year_start.'-'.$req_detail->scholarship_period_month_start.'-'.sprintf('%08d', $req_detail->scholarship_period_date_start).' 00:00:00');
					$requests = $this->db->select("t1.benefits_request_id")
											->from("coop_benefits_request as t1")
											->join("coop_benefits_request_detail as t2", "t1.benefits_request_id = t2.benefits_request_id" ,"inner")
											->where("t1.member_id = '".$data["member_id"]."' AND t1.benefits_type_id = '".$id."' AND t1.benefits_status not in (3,5,6) AND t1.createdatetime >= '".$period_start."'
													AND t2.card_id = '".$data["card_id"]."'")
											->get()->result_array();
					if(count($requests) >= $data["request_time"]) {
						$warning_text .= " - ขอรับสิทธิ์มากกว่าที่กำหนด\n";
					}
				} else {
					$account_period = $this->db->select("*")->from("coop_account_period_setting")->order_by("accm_date_create desc")->get()->row();
					$process_timestamp = date('Y-m-d H:i:s');
					$current_month = date('m');
					$period_year_start = $account_period->accm_month_ini <= $current_month ? date('Y') : date('Y') - 1;
					$period_start = date($period_year_start.'-'.$account_period->accm_month_ini.'-01 00:00:00');
					$requests = $this->db->select("benefits_request_id")
											->from("coop_benefits_request")
											->where("member_id = '".$data["member_id"]."' AND benefits_type_id = '".$id."' AND benefits_status not in (3,5,6) AND createdatetime >= '".$period_start."'")
											->get()->result_array();
					if($id == 3 && count($requests) >= $data["request_time"]) {
						//specific warning message for now heir benefits
						$warning_text .= " - ท่านได้เพิ่มคำร้องขอรับสวัสดิการทายาทใหม่ ในปีนี้เป็นครั้งที่ 2 แล้ว กรุณาตรวจสอบอีกครั้ง\n";
					} else if(count($requests) >= $data["request_time"]) {
						$warning_text .= " - ขอรับสิทธิ์มากกว่าที่กำหนด\n";
					}
				}
			}
		}
		return $warning_text;
	}

	public function auth_confirm_sp_req(){
		if(empty($_SESSION['USER_ID']))
			header('HTTP/1.1 500 Internal Server Error');

		$username = $this->input->post("confirm_user");
		$password = $this->input->post("confirm_pwd");

		$user = $this->db->from("coop_user")->where("username = '{$username}' AND password = '{$password}'")->get()->row();
		if(!empty($user)){
			$premission = $this->db->from("coop_user_permission")->where("user_id = '{$user->user_id}' AND menu_id = 238")->get()->row();
			echo json_encode(array("result" => "true", "permission" => (!empty($premission) || $user->user_type_id == 1) ? "true" : "false" ));
		}else{
			echo json_encode(array("result" => "false"));
		}
	}

	function checkPID($pid) {
		if(strlen($pid) != 13) return false;
		for($i=0, $sum=0; $i<12;$i++) $sum += (int)($pid{$i})*(13-$i);
		if((11-($sum%11))%10 == (int)($pid{12})) return true;
		return false;
	}

	function member_benefit_available() {
		$arr_data = array();
		$member_id = $_GET["member_id"];
		$benefit_details = $this->db->select("*")->from("coop_benefits_type_detail")->where("start_date < '".date("Y-m-d")."'")->order_by("benefits_id, start_date desc")->get()->result_array();

		$benefits = array();
		$prev_benefit_ids = array();
		foreach($benefit_details as $detail) {
			if (!in_array($detail["benefits_id"], $prev_benefit_ids)) {
				$benefits_type = $this->db->select("*")->from("coop_benefits_type")->where("benefits_id = '".$detail["benefits_id"]."'")->get()->row();
				if(!empty($benefits_type)) {
					$data = array();
					$data["member_id"] = $member_id;
					$data["id"] = $detail["benefits_id"];
					if(!empty($detail["age_grester_status"])) $data["age_grester"] = $detail["age_grester"];
					if(!empty($detail["member_age_grester_status"])) $data["member_age_grester"] = $detail["member_age_grester"];
					if(!empty($detail["request_time_status"])) {
						$data["request_time"] = $detail["request_time"];
						$data["request_time_unit"] = $detail["request_time_unit"];
					}
					$result = $this->check_benefits_req_conditions($data);

					$benefit = array();
					$benefit["id"] = $detail["benefits_id"];
					$benefit["name"] = $benefits_type->benefits_name;
					$benefit["available"] = !empty($result) ? 0 : 1;
					$benefits[] = $benefit;
				}
				$prev_benefit_ids[] = $detail["benefits_id"];
			}
		}

		$arr_data["datas"] = $benefits;
		$this->preview_libraries->template_preview_non_auth('benefits/member_benefit_available',$arr_data);
	}
}
