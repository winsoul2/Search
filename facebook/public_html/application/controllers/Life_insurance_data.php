<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Life_insurance_data extends CI_Controller {
	function __construct()
	{
		parent::__construct();
	}
	
	
	public function index()
	{
		$arr_data = array();
		
		$this->libraries->template('life_insurance_data/index',$arr_data);
	}
	
	function life_insurance_year_excel() {
		$arr_data = array();
		$month_arr = $this->center_function->month_arr();
		$title_date = "";
		
		$year_th = @$_GET['year'];
		$start_date_protection = '1 '.$month_arr[1].' '.$year_th;
		$end_date_protection = '31 '.$month_arr[12].' '.$year_th;
		$title_date = '1 '.$month_arr[1].' - 31 '.$month_arr[12].' '.$year_th;
		$arr_data['title_date'] = $title_date;
		
		$sql = "SELECT
					t1.member_id,
					t4.mem_group_id,
					t4.mem_group_name,
					t3.prename_full,
					t2.firstname_th,
					t2.lastname_th,
					t2.id_card,
					t2.birthday,
					t1.insurance_amount,
					t1.insurance_premium
				FROM
					coop_life_insurance AS t1
				INNER JOIN (
							SELECT IF (
										(
											SELECT
												level_old
											FROM
												coop_mem_group_move
											WHERE
												date_move >= '2019-02-26'
											AND coop_mem_group_move.member_id = coop_mem_apply.member_id
											ORDER BY
												date_move ASC
											LIMIT 1
										),
										(
											SELECT
												level_old
											FROM
												coop_mem_group_move
											WHERE
												date_move >= '2019-02-26'
											AND coop_mem_group_move.member_id = coop_mem_apply.member_id
											ORDER BY
												date_move ASC
											LIMIT 1
										),
										coop_mem_apply. level
									) AS level, member_id, firstname_th, lastname_th, mem_type_id, prename_id,id_card,birthday FROM coop_mem_apply
				) AS t2 ON t1.member_id = t2.member_id
				LEFT JOIN coop_prename AS t3 ON t2.prename_id = t3.prename_id
				LEFT JOIN (
				(SELECT id, mem_group_id, mem_group_name, mem_group_parent_id, mem_group_type FROM coop_mem_group) 
				) AS t4 ON t2.level = t4.id
				WHERE
					t1.insurance_year = '".$year_th."'
				AND t1.insurance_type = '1'
				AND t1.insurance_status = '1'
				ORDER BY
					t1.member_id ASC;";
		$rs = $this->db->query($sql);
		$row_life_insurance = $rs->result_array();
		//echo '<pre>'; print_r($row_life_insurance); echo '</pre>';
		if(!empty($row_life_insurance)){
			$arr_data['data'] = $row_life_insurance;
		}
		//
		$this->load->view('life_insurance_data/life_insurance_year_excel',$arr_data);
	}
		
	function cremation_deduct(){
		$arr_data = array();
		
		$where = '';
		$where_finance_month = '';
		if(@$_GET['year']!=''){
			$where .= " AND non_pay_year = '".@$_GET['year']."'";
			$where .= " AND non_pay_month = '".@$_GET['month']."'";
			$where_finance_month = "WHERE t2.profile_month = '".(int)@$_GET['month']."' AND t2.profile_year = '".@$_GET['year']."'";
		}else{
			$where_finance_month = "WHERE t2.profile_month = '".(int)date('m')."' AND t2.profile_year = '".(date('Y')+543)."'";
		}
		
		$x=0;
		$join_arr = array();
		$join_arr[$x]['table'] = 'coop_mem_apply';
		$join_arr[$x]['condition'] = 'coop_non_pay.member_id = coop_mem_apply.member_id';
		$join_arr[$x]['type'] = 'inner';
		$x++;
		$join_arr[$x]['table'] = 'coop_prename';
		$join_arr[$x]['condition'] = 'coop_mem_apply.prename_id = coop_prename.prename_id';
		$join_arr[$x]['type'] = 'left';
		$x++;
		$join_arr[$x]['table'] = "(SELECT
									SUM(t1.pay_amount) AS pay_amount,
									t2.profile_month,
									t2.profile_year,
									t1.member_id
								FROM
									coop_finance_month_detail AS t1
								LEFT JOIN coop_finance_month_profile AS t2 ON t1.profile_id = t2.profile_id
								{$where_finance_month}
								GROUP BY t1.member_id
								) AS t3";
		$join_arr[$x]['condition'] = "coop_non_pay.non_pay_year = t3.profile_year AND coop_non_pay.non_pay_month = t3.profile_month AND coop_non_pay.member_id = t3.member_id";
		$join_arr[$x]['type'] = 'left';
		
		$this->paginater_all->type(DB_TYPE);
		$this->paginater_all->select('
			coop_non_pay.*, 
			coop_mem_apply.firstname_th, 
			coop_mem_apply.lastname_th,
			coop_prename.prename_short,
			t3.pay_amount
		');
		$this->paginater_all->main_table('coop_non_pay');
		$this->paginater_all->where("non_pay_status = '0'".$where);
		$this->paginater_all->page_now(@$_GET["page"]);
		$this->paginater_all->per_page(10);
		$this->paginater_all->page_link_limit(20);
		$this->paginater_all->order_by('non_pay_year DESC, non_pay_month DESC, member_id ASC');
		$this->paginater_all->join_arr($join_arr);
		$row = $this->paginater_all->paginater_process();
		//echo"<pre>";print_r($row);exit;
		//echo $this->db->last_query();exit;
		$paging = $this->pagination_center->paginating($row['page'], $row['num_rows'], $row['per_page'], $row['page_link_limit'],@$_GET);//$page_now = 1, $row_total = 1, $per_page = 20, $page_limit = 20
		$i = $row['page_start'];
		

		$arr_data['num_rows'] = $row['num_rows'];
		$arr_data['paging'] = $paging; 
		$arr_data['data'] = $row['data'];
		$arr_data['i'] = $i;
		
		$month_arr = array('1'=>'มกราคม','2'=>'กุมภาพันธ์','3'=>'มีนาคม','4'=>'เมษายน','5'=>'พฤษภาคม','6'=>'มิถุนายน','7'=>'กรกฎาคม','8'=>'สิงหาคม','9'=>'กันยายน','10'=>'ตุลาคม','11'=>'พฤศจิกายน','12'=>'ธันวาคม');
		
		$arr_data['month_arr'] = $month_arr;
		
		$this->libraries->template('life_insurance_data/cremation_deduct',$arr_data);
	}
	
	public function life_insurance_buy()
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
			$row['mem_group_name'] = $department;
			$arr_data['row_member'] = $row;	
			
			//อายุเกษียณ
			$this->db->select(array('retire_age'));
			$this->db->from('coop_profile');
			$rs_retired = $this->db->get()->result_array();
			$arr_data['retire_age'] = $rs_retired[0]['retire_age'];	
			$arr_data['data'] = array();
			
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
			$join_arr[$x]['table'] = 'coop_life_insurance_type AS t2';
			$join_arr[$x]['condition'] = 't1.insurance_type = t2.insurance_type_id';
			$join_arr[$x]['type'] = 'left';
			
			$this->paginater_all->type(DB_TYPE);
			$this->paginater_all->select('t1.insurance_id,
					t1.member_id,
					t1.insurance_year,
					t1.insurance_date,
					t1.loan_id,
					t1.contract_number,
					t1.insurance_amount,
					t1.insurance_premium,
					t2.insurance_type_name,
					t1.receipt_id'
					);
			$this->paginater_all->main_table('coop_life_insurance AS t1');
			$this->paginater_all->where("t1.member_id = '".$member_id."' AND insurance_status = '1'");
			$this->paginater_all->page_now(@$_GET["page"]);
			$this->paginater_all->per_page(20);
			$this->paginater_all->page_link_limit(20);
			$this->paginater_all->order_by('t1.insurance_year DESC,insurance_date DESC ,insurance_id DESC');
			$this->paginater_all->join_arr($join_arr);
			$row = $this->paginater_all->paginater_process();
			//echo"<pre>";print_r($row);exit;
			//echo $this->db->last_query(); exit;
			$paging = $this->pagination_center->paginating($row['page'], $row['num_rows'], $row['per_page'], $row['page_link_limit']);//$page_now = 1, $row_total = 1, $per_page = 20, $page_limit = 20
			$i = $row['page_start'];


			$arr_data['num_rows'] = $row['num_rows'];
			$arr_data['paging'] = $paging;
			$arr_data['data'] = $row['data'];
			$arr_data['i'] = $i;
			
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
		
		$month_arr = $this->center_function->month_arr();
		$arr_data['month_arr'] = $month_arr;
		
		$this->libraries->template('life_insurance_data/life_insurance_buy',$arr_data);
	}
	
	function get_life_insurance(){	
		$data = array();
		
		$member_id = @$_POST['member_id'];
		$insurance_new = @$_POST['insurance_amount'];
		$date_insurance= $this->center_function->ConvertToSQLDate(@$_POST['insurance_date']);
		$year_insurance = date('Y',strtotime($date_insurance));
		$month_insurance = date('m',strtotime($date_insurance));
		
		$data['date_insurance'] = $date_insurance;
		$data['year_insurance'] = ($year_insurance+543);
		$data['month_insurance'] = $month_insurance;		
	
		$arr_data_life = array();
		$arr_data_life['start_date_insurance'] = @$year_insurance.'-'.sprintf("%02d",@$month_insurance).'-01';
		$arr_data_life['end_date_insurance'] =  @$year_insurance.'-12-31';
		$arr_data_life['insurance_new'] = @$insurance_new;
		$arr_data_life['insurance_old'] = 0;
		$arr_life_insurance = $this->life_insurance_libraries->get_deduct_insurance(@$arr_data_life,'loan');
		//echo '<pre>'; print_r($arr_life_insurance); echo '</pre>';
		$data['data'] = $arr_life_insurance;

		$deduct_insurance = @$arr_life_insurance['deduct_insurance']; 
		$data['deduct_insurance'] = number_format((@$deduct_insurance > 0)?@$deduct_insurance:0,2);		
		
		echo json_encode($data);
		exit;
	}
	
	function life_insurance_buy_save(){
		//echo"<pre>";print_r($_POST);exit;
		$member_id = @$_POST['member_id'];		
		
		//start บันทึกการชำระเงิน
		$yymm = (date("Y")+543).date("m");
		$mm = date("m");
		$yy = (date("Y")+543);
		$yy_full = (date("Y")+543);
		$yy = substr($yy,2);
		$this->db->select('*');
		$this->db->from('coop_receipt');
		$this->db->where("receipt_id LIKE '".$yy_full.$mm."%'");
		$this->db->order_by("receipt_id DESC");
		$this->db->limit(1);
		$row = $this->db->get()->result_array();
		
		if(!empty($row)) {
			$id = (int) substr($row[0]["receipt_id"], 6);
			$receipt_number = $yymm.sprintf("%06d", $id + 1);
		}
		else {
			$receipt_number = $yymm."000001";
		}
		$order_by_id =  $row[0]["order_by"]+1 ; 

		$insurance_premium = str_replace(',','',@$_POST['insurance_premium']); //เบี้ยประกัน	
		
		$data_insert = array();
		$data_insert['receipt_id'] = $receipt_number;
		$data_insert['member_id'] = @$member_id;
		$data_insert['order_by'] = @$order_by_id;			
		$data_insert['sumcount'] = $insurance_premium;
		$data_insert['receipt_datetime'] = date('Y-m-d H:i:s');
		$data_insert['admin_id'] = $_SESSION['USER_ID'];
		$data_insert['pay_type'] = "0";
		$this->db->insert('coop_receipt', $data_insert);
		
		$data_insert = array();
		$data_insert['receipt_id'] = $receipt_number;
		$data_insert['receipt_list'] = '33';//ประเภทเบี้ยประกัน
		$data_insert['receipt_count'] = $insurance_premium;
		$this->db->insert('coop_receipt_detail', $data_insert);
		
		$data_insert = array();
		$data_insert['receipt_id'] = $receipt_number;
		$data_insert['member_id'] = @$member_id;
		$data_insert['account_list_id'] = '33';//ประเภทเบี้ยประกัน
		$data_insert['principal_payment'] = @$insurance_premium;
		$data_insert['interest'] = 0;
		$data_insert['total_amount'] = @$insurance_premium;
		$data_insert['payment_date'] = $this->center_function->ConvertToSQLDate(@$_POST['insurance_date']);
		$data_insert['loan_amount_balance'] ='';
		$data_insert['createdatetime'] = $this->center_function->ConvertToSQLDate(@$_POST['insurance_date']);
		$data_insert['transaction_text'] = 'เบี้ยประกันชีวิต';
		$this->db->insert('coop_finance_transaction', $data_insert);
		//end บันทึกการชำระเงิน;
	
		$data_insert = array();
		$data_insert['member_id'] = @$member_id;
		$data_insert['insurance_year'] = @$_POST['insurance_year'];
		$data_insert['insurance_date'] = $this->center_function->ConvertToSQLDate(@$_POST['insurance_date']);
		$data_insert['contract_number'] = @$member_id;;
		$data_insert['insurance_amount'] = str_replace(',','',@$_POST['insurance_amount']);
		$data_insert['insurance_premium'] = str_replace(',','',@$_POST['insurance_premium']);
		$data_insert['insurance_type'] = '3';//ซื้อเพิ่ม
		$data_insert['admin_id'] = @$_SESSION['USER_ID'];
		$data_insert['createdatetime'] = date('Y-m-d H:i:s');
		$data_insert['insurance_new'] = str_replace(',','',@$_POST['insurance_amount']);
		$data_insert['insurance_status'] = '1';
		$data_insert['receipt_id'] = @$receipt_number;
		$this->db->insert('coop_life_insurance', $data_insert);

		$this->center_function->toast('บันทึกข้อมูลเรียบร้อยแล้ว');
		echo "<script>document.location.href='".base_url(PROJECTPATH.'/life_insurance_data/life_insurance_buy?member_id='.$member_id)."'</script>";
	}
}
