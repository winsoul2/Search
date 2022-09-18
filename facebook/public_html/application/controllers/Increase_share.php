<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Increase_share extends CI_Controller {
	function __construct()
	{
		parent::__construct();
	}
	public function index()
	{
		$arr_data = array();
		if($this->input->get('member_id')!=''){
			$member_id = $this->input->get('member_id');
		}else{
			$member_id = '';
		}
		$arr_data['member_id'] = $member_id;
		
		$arr_data['count_share'] = 0;
		$arr_data['cal_share'] = 0;
		
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
			$row['mem_group_name'] = @$department;
			$arr_data['row_member'] = @$row;	
			
			//อายุเกษียณ
			$this->db->select(array('retire_age'));
			$this->db->from('coop_profile');
			$rs_retired = $this->db->get()->result_array();
			$arr_data['retire_age'] = @$rs_retired[0]['retire_age'];	
			
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
			$join_arr[$x]['condition'] = 'coop_change_share.admin_id = coop_user.user_id';
			$join_arr[$x]['type'] = 'left';
			
			$this->paginater_all->type(DB_TYPE);
			$this->paginater_all->select('*');
			$this->paginater_all->main_table('coop_change_share');
			$this->paginater_all->where("member_id = '".$member_id."'");
			$this->paginater_all->page_now(@$_GET["page"]);
			$this->paginater_all->per_page(20);
			$this->paginater_all->page_link_limit(20);
			$this->paginater_all->order_by('change_share_id DESC');
			$this->paginater_all->join_arr($join_arr);
			$row = $this->paginater_all->paginater_process();
			//echo"<pre>";print_r($row);exit;
			$paging = $this->pagination_center->paginating($row['page'], $row['num_rows'], $row['per_page'], $row['page_link_limit'],@$_GET);//$page_now = 1, $row_total = 1, $per_page = 20, $page_limit = 20
			$i = $row['page_start'];


			$arr_data['num_rows'] = $row['num_rows'];
			$arr_data['paging'] = $paging;
			$arr_data['data'] = $row['data'];
			$arr_data['i'] = $i;
			
			$this->db->select('*');
			$this->db->from('coop_mem_share');
			$this->db->where("member_id = '" . $member_id . "' AND share_status IN('1','2')");
			$row = $this->db->get()->result_array();
			foreach ($row as $key => $value) {
				$arr_data['count_share'] += @$value['share_early'];
				$arr_data['cal_share'] += @$value['share_early'] * @$value['share_value'];
			}
			
			$this->db->select('*');
			$this->db->from('coop_change_share');
			$this->db->where("member_id = '".$member_id."' AND change_share_status IN('1','2')");
			$this->db->order_by('change_share_id DESC');
			$this->db->limit(1);
			$row = $this->db->get()->result_array();
			
			if(!empty($row)){
				$share_per_month = @$row[0]['change_value'];
			}else{
				/*
				$this->db->select(array('share_salary','salary_rule'));
				$this->db->from('coop_share_rule');
				$this->db->where("salary_rule <= '".$arr_data['row_member']['salary']."' AND mem_type_id='".$arr_data['row_member']['mem_type_id']."'");
				$this->db->order_by('salary_rule DESC');
				$this->db->limit(1);
				$row = $this->db->get()->result_array();				
				$share_per_month = @$row[0]['share_salary'];
				*/
				
				$this->db->select(array('setting_value'));
				$this->db->from('coop_share_setting');
				$this->db->limit(1);
				$row_setting_value = $this->db->get()->result_array();		
				$share_salary_value = @$arr_data['row_member']['share_month']/@$row_setting_value[0]['setting_value'];
				$share_per_month = @$arr_data['row_member']['share_month']/@$row_setting_value[0]['setting_value'];
				
			}
			$arr_data['share_per_month'] = @$share_per_month;
			//exit;
		}else{
			$arr_data['data'] = array();
			$arr_data['paging'] = '';
			$arr_data['share_per_month'] = 0;
		}

		$this->db->select('*');
		$this->db->from('coop_share_setting');
		$this->db->order_by('setting_id DESC');
		$row = $this->db->get()->result_array();
		$arr_data['share_value'] = $row[0]['setting_value'];
		
		$this->libraries->template('increase_share/index',$arr_data);
	}
	
	function save_increase_share(){
		if ($_POST) {
			//echo"<pre>";print_r($_POST);echo"</pre>";exit;
			$data = $this->input->post();
			if(isset($data['cancel_change_share']) && $data['cancel_change_share']=='1'){
				$data_insert = array();
				$data_insert['change_share_status'] = '2';
				$data_insert['cancel_date'] = date('Y-m-d H:i:s');

				$this->db->where('change_share_id', $data['change_share_id']);
				$this->db->update('coop_change_share', $data_insert);
				
				echo "success";exit;
			}else{
				$this->db->select('*');
				$this->db->from('coop_change_share');
				$this->db->where("member_id = '".$data['member_id']."' AND change_share_status IN('1','2')");
				$this->db->order_by('change_share_id DESC');
				$this->db->limit(1);
				$row_prev_share = $this->db->get()->result_array();
				
				if(@$row_prev_share[0]['change_value']==''){
					$change_type = 'increase';
				}else if($data['change_value'] > @$row_prev_share[0]['change_value']){
					$change_type = 'increase';
				}else{
					$change_type = 'decrease';
				}
				
				$this->db->select('*');
				$this->db->from('coop_approval_cycle');
				$this->db->where("id='1'");
				$row_approval = $this->db->get()->result_array();
				if($row_approval[0]['approval_date'] == 'last'){
					$active_date = date('Y-m-t 00:00:00',strtotime('+1 day',strtotime(date('Y-m-t 00:00:00'))));
				}else{				
					if(date('d')>$row_approval[0]['approval_date']){
						$active_date = date('Y-m-d 00:00:00',strtotime('+1 month',strtotime(date('Y-m-'.$row_approval[0]['approval_date'].' 00:00:00'))));
						$active_date = date('Y-m-d 00:00:00',strtotime('+1 day',strtotime($active_date)));
					}else{
						$active_date = date('Y-m-d 00:00:00',strtotime('+1 day',strtotime(date('Y-m-'.$row_approval[0]['approval_date'].' 00:00:00'))));
					}
				}
				
				$data_insert = array();
				$data_insert['member_id'] = $data['member_id'];
				$data_insert['admin_id'] = $_SESSION['USER_ID'];
				$data_insert['change_type'] = $change_type;
				$data_insert['share_value'] = $data['share_value'];
				$data_insert['change_value'] = $data['change_value'];
				$data_insert['change_value_price'] = $data['change_value_price'];
				$data_insert['create_date'] = date('Y-m-d H:i:s');
				$data_insert['active_date'] = $active_date;
				$data_insert['change_share_status'] = '1';
				$this->db->insert('coop_change_share', $data_insert);
				
				//บันทึกที่ข้อมูลสมาชิก
				$data_mem_insert = array();
				$data_mem_insert['share_month'] = $data['change_value_price'];
				$this->db->where('member_id', $data['member_id']);
				$this->db->update('coop_mem_apply', $data_mem_insert);
				
				
				$this->center_function->toast('บันทึกข้อมูลเรียบร้อยแล้ว');
				echo "<script>window.location.href = '".PROJECTPATH."/increase_share?member_id=".$data['member_id']."';</script>";
		
			}
		}
	}
	
	public function check_increase_share(){
		$month_arr = array('1'=>'มกราคม','2'=>'กุมภาพันธ์','3'=>'มีนาคม','4'=>'เมษายน','5'=>'พฤษภาคม','6'=>'มิถุนายน','7'=>'กรกฎาคม','8'=>'สิงหาคม','9'=>'กันยายน','10'=>'ตุลาคม','11'=>'พฤศจิกายน','12'=>'ธันวาคม');
			
			$this->db->select(array('start_date','end_date'));
			$this->db->from('coop_account_year');
			$this->db->where("account_year = '".(date('Y')+543)."' AND is_close='0'");
			$row_year = $this->db->get()->result_array();
			
			$this->db->select(array('*'));
			$this->db->from('coop_increase_share_setting');
			$this->db->limit(1);
			$rs_increase = $this->db->get()->result_array();
			$row_increase = @$rs_increase[0];
			$share_increase = @$row_increase['share_increase'];//จำนวนครั้งที่เพิ่มต่อปี
			$share_decrease = @$row_increase['share_decrease'];//จำนวนครั้งที่ลดต่อปี
			
			$this->db->select(array('create_date','change_type'));
			$this->db->from('coop_change_share');
			$this->db->where("member_id = '".$this->input->post('member_id')."' 
				AND create_date BETWEEN '".$row_year[0]['start_date']."' AND '".$row_year[0]['end_date']."'
				AND change_share_status <> '3'");
			$this->db->order_by('create_date DESC');
			$row = $this->db->get()->result_array();
			$count_increase = 0;
			$count_decrease = 0;
			foreach($row AS $key=>$value){
				if($value['change_type'] == 'increase'){
					$count_increase++;
				}
				
				if($value['change_type'] == 'decrease'){
					$count_decrease++;
				}
			}

			$text_alert = "";
			if($count_increase == $share_increase && $count_decrease == $share_decrease){
				
				$create_date = $this->center_function->mydate2date(@$row[0]['create_date']);
				$text_alert .= "การเพิ่มทำได้เพียงปีละ ".$share_increase." ครั้งเท่านั้น<br>";
				$text_alert .= "การลดหุ้นทำได้เพียงปีละ ".$share_decrease." ครั้งเท่านั้น<br>";
				$text_alert .= "ท่านทำรายการล่าสุดเมื่อ ".$create_date."<br>\n";
				echo $text_alert;
			}else{
				echo 'NOT FOUND';
			}
			/*if(!empty($row)){
				$create_date = $this->center_function->mydate2date(@$row[0]['create_date']);
				echo $create_date;
			}else{
				echo 'NOT FOUND';
			}*/
			exit;
	}
	
	public function check_decrease_share(){
		
		$this->db->select(array('salary','other_income','mem_type_id'));
		$this->db->from('coop_mem_apply');
		$this->db->where("member_id = '".$this->input->post('member_id')."'");
		$rs_member = $this->db->get()->result_array();
		$row_member = @$rs_member[0];
		$min_share_rule = @$row_member['salary']+@$row_member['other_income'];
		
		$this->db->select(array('share_salary','salary_rule','mem_type_id'));
		$this->db->from('coop_share_rule');
		$this->db->where("salary_rule <= '".$min_share_rule."' AND mem_type_id ='".@$row_member['mem_type_id']."'");
		$this->db->order_by('salary_rule DESC');
		$this->db->limit(1);
		$row_rule = $this->db->get()->result_array();
		
		//
		$this->db->select(array('start_date','end_date'));
		$this->db->from('coop_account_year');
		$this->db->where("account_year = '".(date('Y')+543)."' AND is_close='0'");
		$row_year = $this->db->get()->result_array();
			
		$this->db->select(array('*'));
		$this->db->from('coop_increase_share_setting');
		$this->db->limit(1);
		$rs_increase = $this->db->get()->result_array();
		$row_increase = @$rs_increase[0];
		$share_increase = @$row_increase['share_increase'];//จำนวนครั้งที่เพิ่มต่อปี
		$share_decrease = @$row_increase['share_decrease'];//จำนวนครั้งที่ลดต่อปี
		
		$this->db->select(array('create_date','change_type'));
		$this->db->from('coop_change_share');
		$this->db->where("member_id = '".$this->input->post('member_id')."' 
			AND create_date BETWEEN '".$row_year[0]['start_date']."' AND '".$row_year[0]['end_date']."'
			AND change_share_status <> '3'");
		$this->db->order_by('create_date DESC');
		$row = $this->db->get()->result_array();
		$count_increase = 0;
		$count_decrease = 0;
		foreach($row AS $key=>$value){
			if($value['change_type'] == 'increase'){
				$count_increase++;
			}
			
			if($value['change_type'] == 'decrease'){
				$count_decrease++;
			}
		}

		$this->db->select('*');
		$this->db->from('coop_change_share');
		$this->db->where("member_id = '".$this->input->post('member_id')."' AND change_share_status IN('1','2')");
		$this->db->order_by('change_share_id DESC');
		$this->db->limit(1);
		$row_prev_share = $this->db->get()->result_array();	
		if(@$row_prev_share[0]['change_value']==''){
			$change_type = 'increase';
		}else if($this->input->post('change_value') > @$row_prev_share[0]['change_value']){
			$change_type = 'increase';
		}else{
			$change_type = 'decrease';
		}	
		//
		if($this->input->post('change_value') < @$row_rule[0]['share_salary']){
			echo "เงินเดือนไม่น้อยกว่า ".@$row_rule[0]['salary_rule']." บาท ต้องถือหุ้นรายเดือนอย่างน้อย ".@$row_rule[0]['share_salary']." หุ้น";
		}else if($change_type == 'increase' && $share_increase == $count_increase){
			echo "การเพิ่มทำได้เพียงปีละ ".$share_increase." ครั้งเท่านั้น";				
		}else if($change_type == 'decrease' && $share_decrease == $count_decrease){
			echo "การลดหุ้นทำได้เพียงปีละ ".$share_decrease." ครั้งเท่านั้น";
		}else{
			echo "pass";
		}
		
		exit;
	}
	
	function cancel_increase_share(){
		if ($this->input->post()) {
			$data = $this->input->post();
		  if($data['cancel_change_share']=='1'){
				
				$data_insert = array();
				$data_insert['change_share_status'] = $data['status_to'];

				$this->db->where('change_share_id', $data['change_share_id']);
				$this->db->update('coop_change_share', $data_insert);
				
				//update ข้อมูลหุ้นในตาราง  coop_mem_apply.share_month ด้วย
				$this->db->select('member_id');
				$this->db->from('coop_change_share');
				$this->db->where("change_share_id = '".$data['change_share_id']."'");
				$this->db->limit(1);
				$row_member_id = $this->db->get()->result_array();
				$member_id = @$row_member_id[0]['member_id'];
				
				$this->db->select('change_value_price');
				$this->db->from('coop_change_share');
				$this->db->where("member_id = '".$member_id."' AND change_share_status IN('1','2')");
				$this->db->order_by('change_share_id DESC');
				$this->db->limit(1);
				$row_change_share = $this->db->get()->result_array();
				$share_month = @$row_change_share[0]['change_value_price'];
				
				$data_insert = array();
				$data_insert['share_month'] = @$share_month;
				$this->db->where('member_id', $member_id);
				$this->db->update('coop_mem_apply', $data_insert);	
				
				echo "success";exit;
			}
		}
		$arr_data = array();
		
		$x=0;
		$join_arr = array();
		$join_arr[$x]['table'] = 'coop_user';
		$join_arr[$x]['condition'] = 'coop_change_share.admin_id = coop_user.user_id';
		$join_arr[$x]['type'] = 'left';
		$x++;
		$join_arr[$x]['table'] = 'coop_mem_apply';
		$join_arr[$x]['condition'] = 'coop_change_share.member_id = coop_mem_apply.member_id';
		$join_arr[$x]['type'] = 'left';
		$x++;
		$join_arr[$x]['table'] = 'coop_prename';
		$join_arr[$x]['condition'] = 'coop_mem_apply.prename_id = coop_prename.prename_id';
		$join_arr[$x]['type'] = 'left';
		
		$this->paginater_all->type(DB_TYPE);
		$this->paginater_all->select('coop_change_share.*,
			coop_user.user_name,
			coop_mem_apply.firstname_th,
			coop_mem_apply.lastname_th,
			coop_prename.prename_short');
		$this->paginater_all->main_table('coop_change_share');
		$this->paginater_all->where("change_share_status IN('2','3')");
		$this->paginater_all->page_now(@$_GET["page"]);
		$this->paginater_all->per_page(20);
		$this->paginater_all->page_link_limit(20);
		$this->paginater_all->order_by('cancel_date DESC');
		$this->paginater_all->join_arr($join_arr);
		$row = $this->paginater_all->paginater_process();
		//echo"<pre>";print_r($row);exit;
		$paging = $this->pagination_center->paginating($row['page'], $row['num_rows'], $row['per_page'], $row['page_link_limit']);//$page_now = 1, $row_total = 1, $per_page = 20, $page_limit = 20
		$i = $row['page_start'];


		$arr_data['num_rows'] = $row['num_rows'];
		$arr_data['paging'] = $paging;
		$arr_data['data'] = $row['data'];
		$arr_data['i'] = $i;
		
		$this->libraries->template('increase_share/cancel_increase_share',$arr_data);
	}
	
	public function check_min_max_share(){
			
			$member_id = @$_POST['member_id'];
			$value_price = @$_POST['change_value_price'];
			
			$this->db->select(array('coop_mem_apply.member_id','coop_mem_apply_type.type_share','coop_mem_apply_type.amount_min','coop_mem_apply_type.amount_max'));
			$this->db->from('coop_mem_apply');
			$this->db->join("coop_mem_apply_type","coop_mem_apply.apply_type_id = coop_mem_apply_type.apply_type_id","left");
			$this->db->where("coop_mem_apply.member_id='{$member_id}' AND coop_mem_apply_type.type_share = '2'");
			$rs = $this->db->get()->result_array();
			$row = @$rs[0];
			
			if(!empty($row)){
				if($value_price >= @$row['amount_min'] && $value_price <= @$row['amount_max']){
					echo true;
				}else{
					echo false;
				}
			}else{
				echo true;
			}
			exit;
	}
	
	function check_share_rule(){
		$member_id = @$_POST['member_id'];
		$change_value_price = @$_POST['change_value_price'];
		
		$this->db->select(array('coop_mem_apply.member_id',
								'coop_mem_apply.mem_type_id',
								'coop_mem_apply.salary',
								'coop_mem_apply.other_income'));
		$this->db->from('coop_mem_apply');
		$this->db->where("coop_mem_apply.member_id='{$member_id}'");
		$rs = $this->db->get()->result_array();		
		$row = @$rs[0];
		$min_share_rule = @$row['salary']+@$row['other_income'];
		
		$this->db->select(array('*'));
		$this->db->from('coop_share_rule');
		//$this->db->where("coop_share_rule.mem_type_id='".$row['mem_type_id']."' AND coop_share_rule.salary_rule >= '".$min_share_rule."'");
		$this->db->where("coop_share_rule.mem_type_id='".$row['mem_type_id']."' AND coop_share_rule.salary_rule <= '".$min_share_rule."'");
		$this->db->order_by('salary_rule DESC');
		$this->db->limit(1);
		$rs_rule = $this->db->get()->result_array();	
		
		$this->db->select(array('setting_value'));
		$this->db->from('coop_share_setting');
		$this->db->limit(1);
		$row_setting_value = $this->db->get()->result_array();		
		$share_salary_value = @$rs_rule[0]['share_salary']*@$row_setting_value[0]['setting_value'];
		//echo $this->db->last_query();
		//echo '<pre>'; print_r($share_salary_value); echo '</pre>';

		if($change_value_price >= $share_salary_value){
			echo "true|";
		}else{
			echo "false|".number_format($share_salary_value,0);
		}
		exit;
	}
	
}
