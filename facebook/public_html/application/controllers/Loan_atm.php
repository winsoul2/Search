<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Loan_atm extends CI_Controller {
	public $loan_atm_status = array('0'=>'รออนุมัติ', '1'=>'อนุมัติ', '2'=>'ขอยกเลิก', '3'=>'ยกเลิกสัญญา', '4'=>'ปิดสัญญา','5'=>'ไม่อนุมัติ');
	public $loan_atm_activate_status = array('0'=>'ปกติ', '1'=>'อายัดบัญชี');
	public $loan_detail_status = array('0'=>'ทำรายการแล้ว', '1'=>'จ่ายครบแล้ว');
	public $transaction_at = array('0'=>'ทำรายการที่สหกรณ์', '1'=>'ทำรายการทีตู้ ATM');
	public $pay_type = array('0'=>'เงินสด', '1'=>'โอนเงิน', '2'=>'ATM');
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

		$this->db->select('*');
		$this->db->from('coop_share_setting');
		$this->db->order_by('setting_id DESC');
		$row = $this->db->get()->result_array();
		$arr_data['share_value'] = $row[0]['setting_value'];
		
		$this->db->select(array('*'));
		$this->db->from('coop_loan_atm_setting');
		$row = $this->db->get()->result_array();
		$arr_data['loan_atm_setting'] = @$row[0];

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
			
			$this->db->select('*');
			$this->db->from('coop_mem_share');
			$this->db->where("member_id = '".$member_id."' AND share_status IN('1','2')");
			$this->db->order_by('share_date DESC');
			$this->db->limit(1);
			$row_prev_share = $this->db->get()->result_array();
			$row_prev_share = @$row_prev_share[0];
			
			$arr_data['count_share'] = $row_prev_share['share_collect'];
			$arr_data['cal_share'] = $row_prev_share['share_collect_value'];
			
			$this->db->select(array('*'));
			$this->db->from('coop_loan_atm');
			$this->db->where("member_id = '".$member_id."' AND loan_atm_status = '1'");
			$row = $this->db->get()->result_array();
			if(empty($row)){
				$this->db->select(array('*'));
				$this->db->from('coop_loan_atm');
				$this->db->where("member_id = '".$member_id."' AND loan_atm_status = '0'");
				$row = $this->db->get()->result_array();
				$prev_loan_deduct = $this->db->get_where("coop_loan_atm_prev_deduct", array("loan_atm_id" => $row[0]['loan_atm_id']))->result_array();
				if($prev_loan_deduct)
					$row[0]['prev_loan_deduct'] = $prev_loan_deduct;
				$arr_data['row_loan_atm'] = @$row[0];	
				//หักกลบ ฉฉ
				$arr_data['deduct_amount'] = 0;
				$arr_data['principal_amount'] = 0;
				$this->db->select(array(
					't1.id',
					't1.contract_number',
					't1.loan_amount_balance',
					't1.loan_type'
				));
				$this->db->from("coop_loan t1");
				$this->db->join("coop_loan_name t2",'t1.loan_type = t2.loan_name_id','inner');
				$this->db->join("coop_loan_type t3",'t2.loan_type_id = t3.id','inner');
				$this->db->where("t1.loan_status = '1' AND member_id = '".@$member_id."' AND t3.loan_type_code = 'emergent'");
				$row = $this->db->get()->result_array();
				foreach($row as $key => $value){
					$cal_loan_interest = array();
					$cal_loan_interest['loan_id'] = $value['id'];
					$cal_loan_interest['date_interesting'] = date('Y-m-d');
					// $interest_amount = $this->loan_libraries->cal_loan_interest($cal_loan_interest);
					$loan_amount = $value['loan_amount_balance'];//เงินกู้
					$loan_type = $value['loan_type'];//ประเภทเงินกู้ใช้หา เรทดอกเบี้ย
					$loan_id = $value['id'];//ใช้หาเรทดอกเบี้ยใหม่ 26/5/2562
					$this->db->select("date(transaction_datetime) as transaction_datetime");
					$this->db->order_by("transaction_datetime desc");
					$this->db->limit(1);
					$date1 = $this->db->get_where("coop_loan_transaction", array(
						"loan_id" => $value['id']
					))->result_array()[0]['transaction_datetime'];//วันคิดดอกเบี้ยล่าสุด
					$tmp_date1 = date("Y-m", strtotime($date1) );
					if($tmp_date1 != date("Y-m"))//ใช้ออกเรียกเก็บ
						$date1 = date("Y-m-t", strtotime("last month"));
					
					$date2 = date("Y-m-d");//วันที่คิดดอกเบี้ย now
					$interest_amount = $this->loan_libraries->calc_interest_loan($loan_amount, $loan_id, $date1, $date2);
					
					$arr_data['loan_id'] 				= $value['id'];
					$arr_data['principal_amount'] 		= @$value['loan_amount_balance'];
					$arr_data['interest_amount']	 	= @$interest_amount;
					$arr_data['deduct_amount'] 			= @$value['loan_amount_balance']+@$interest_amount;
					// echo "<hr>";
					// echo $date1." ". $date2." <br>";
					// echo $value['loan_amount_balance']." * 5.75 /100 * /365";
					// echo $interest_amount."<br>";
					// exit;
				}
				//หักกลบ ฉฉ
			
			}else{
				//เช็คยอดหักกลบ
				//
				/*$this->db->select(array('*'));
				$this->db->from('coop_loan_atm');
				$this->db->where("member_id = '".$member_id."' AND loan_atm_status = '0'");
				$row = $this->db->get()->result_array();
				$arr_data['row_loan_atm'] = @$row[0];	
				*/
				//
				// $this->db->select(array('*'));
				// $this->db->from('coop_loan_atm');
				// $this->db->where("member_id = '".$member_id."' AND loan_atm_status = '0'");
				// $row = $this->db->get()->result_array();
				$arr_data['row_loan_atm'] = @$row[0];
				$arr_data['deduct_amount'] = 0;
				$arr_data['principal_amount'] = 0;
				//echo '<pre>'; print_r($arr_data['row_loan_atm']); echo '</pre>'; exit;
				$this->db->select(array('*'));
				$this->db->from('coop_loan_atm');
				$this->db->where("member_id = '".$member_id."' AND loan_atm_status = '1'");
				$row = $this->db->get()->result_array();
				$principal_amount = 0;
				$interest_amount = 0;
				$total_amount_balance = 0;
				foreach($row as $key => $value){

					$arr_data['principal_amount'] += @$value['total_amount_approve'] - $value['total_amount_balance']; //ต้นคงเหลือ
					$total_amount_balance = @$value['total_amount_approve'] - @$value['total_amount_balance'];//ยอดหนี้คงเหลือ
					
					$cal_loan_interest = array();
					$cal_loan_interest['loan_atm_id'] = $arr_data['row_loan_atm']['loan_atm_id'];
					$cal_loan_interest['date_interesting'] = date('Y-m-d');
					// $interest_amount = $this->loan_libraries->cal_atm_interest($cal_loan_interest);
					if($total_amount_balance == 0){
						$interest_amount = 0;
					}else{
						$interest_amount = $this->loan_libraries->cal_atm_interest_report_test($cal_loan_interest,"echo", array("month"=> date("m"), "year" => date("Y") ), false , true)['interest_month'];
						// if($interest_amount<=0){
						// 	$interest_amount = $this->loan_libraries->cal_atm_interest_report_test($cal_loan_interest,"echo", array("month"=> date("m"), "year" => date("Y") ), false , true)['interest_month'];
						// }
					}
					
					$arr_data['interest_amount'] = @$interest_amount; //ดอกเบี้ย
					// echo "<hr>";
					// var_dump($interest_amount);
					// echo @$interest_amount;
					// exit;
					$arr_data['deduct_amount'] += ((@$value['total_amount_approve'] - $value['total_amount_balance']) + @$interest_amount); //ยอดรวม
				}

				
				$arr_data['row_loan_atm_detail'] = @$row;
				
				$this->db->select(array('*'));
				$this->db->from('coop_loan_atm_file_attach');
				$this->db->where("loan_atm_id = '".$arr_data['row_loan_atm']['loan_atm_id']."'");
				$row = $this->db->get()->result_array();
				$arr_data['row_loan_atm_file'] = @$row;
				
				$this->db->select(array('loan_id'));
				$this->db->from('coop_loan_atm_detail');
				$this->db->where("
					member_id = '".$member_id."' 
					AND loan_atm_id = '".$arr_data['row_loan_atm']['loan_atm_id']."'
					AND transfer_status = '0'
				");
				$this->db->order_by('loan_id ASC');
				$row = $this->db->get()->result_array();
				if(!empty($row)){
					$arr_data['loan_atm_detail_id_no_transfer'] = $row[0]['loan_id'];
				}
			}
			$this->db->select(array('*'));
			$this->db->from('coop_loan_atm');
			$this->db->where("member_id = '".$member_id."'");
			$this->db->order_by("loan_atm_id DESC");
			$row = $this->db->get()->result_array();
			foreach($row as $key => $value){
				$this->db->select(array('loan_id'));
				$this->db->from('coop_loan_atm_detail');
				$this->db->where("loan_atm_id = '".$value['loan_atm_id']."' AND transfer_status = '0'");
				$this->db->order_by("loan_id ASC");
				$row_detail_no_transfer = $this->db->get()->result_array();
				if(!empty($row_detail_no_transfer)){
					$row[$key]['loan_atm_detail_id_no_transfer'] = $row_detail_no_transfer[0]['loan_id'];
				}
			}
			$arr_data['row_loan_atm_all'] = $row;
			


			$this->db->select(array(
				'account_id','account_name'
			));
			$this->db->from('coop_maco_account');
			$this->db->where("mem_id = '".$member_id."'");
			$row_account = $this->db->get()->result_array();
			$arr_data['account_id'] = $row_account;	

			///////////////////////////////รายการสัญญาเดิมที่หักลบ////////////////////////////////
			$prev_loan_active_arr = array();
			$i=0;
			$this->db->select(array(
				'*'
			));
			$this->db->from('coop_loan as t1');
			$this->db->where("t1.member_id = '".$member_id."' AND t1.loan_status = '1' AND t1.loan_amount_balance <> 0");
			$prev_loan_active = $this->db->get()->result_array();
			
			foreach($prev_loan_active as $key => $value){
				$prev_loan_active_arr[$i]['id'] = $value['id'];
				$prev_loan_active_arr[$i]['contract_number'] = $value['contract_number'];
				$prev_loan_active_arr[$i]['loan_amount_balance'] = $value['loan_amount_balance'];
				
				$rs_type_prev_loan_atm = $this->db->select(array('pay_type'))
				->from('coop_loan_atm_prev_deduct')
				->where("ref_id = '".$value['id']."' AND loan_atm_id = '".$arr_data['row_loan_atm']['loan_atm_id']."'")
				->get()->result_array();			
				$check_type_prev_loan_atm = $rs_type_prev_loan_atm[0]['pay_type'];			
				//echo $this->db->last_query(); echo '<hr>';
				//echo '<pre>'; print_r($check_type_prev_loan_atm ); echo '</pre>';
			
				$prev_loan_active_arr[$i]['checked'] = (@$check_type_prev_loan_atm != '')?$check_type_prev_loan_atm:"principal";
				$cal_loan_interest = array();
				$cal_loan_interest['loan_id'] = $value['id'];
				$cal_loan_interest['date_interesting'] = date('Y-m-d');

				$loan_amount = $value['loan_amount_balance'];//เงินกู้
				$loan_type = $value['loan_type'];//ประเภทเงินกู้ใช้หา เรทดอกเบี้ย
				$loan_id = $value['id'];//ใช้หาเรทดอกเบี้ยใหม่ 26/5/2562
				$this->db->select("date(transaction_datetime) as transaction_datetime");
				$this->db->order_by("transaction_datetime desc");
				$this->db->limit(1);
				$date1 = $this->db->get_where("coop_loan_transaction", array(
					"loan_id" => $value['id']
				))->result_array()[0]['transaction_datetime'];//วันคิดดอกเบี้ยล่าสุด
				$tmp_date1 = date("Y-m", strtotime($date1) );
				if($tmp_date1 != date("Y-m"))//ใช้ออกเรียกเก็บ
					$date1 = date("Y-m-t", strtotime("last month"));
				
				$date2 = date("Y-m-d");//วันที่คิดดอกเบี้ย now
				$interest_loan = $this->loan_libraries->calc_interest_loan($loan_amount, $loan_id, $date1, $date2);
				$interest_loan = round($interest_loan);
				// $interest_loan = $this->loan_libraries->cal_loan_interest($cal_loan_interest);

				$prev_loan_active_arr[$i]['principal'] = $loan_amount;
				$prev_loan_active_arr[$i]['interest'] = $interest_loan;
				$prev_loan_active_arr[$i]['type'] = 'loan';
				$prev_loan_active_arr[$i]['prev_loan_total'] = $value['loan_amount_balance'];
				
				$ref_loan_deduct = $this->db->get_where("coop_loan_atm_prev_deduct", array(
					"ref_id" => $value['id'],
					"loan_atm_id" => $arr_data['row_loan_atm']['loan_atm_id']
				))->result_array()[0];
				if($ref_loan_deduct){
					$prev_loan_active_arr[$i]['ref_loan_deduct'] = $ref_loan_deduct;
				}
				
				$this->db->select(array(
					'*'
				));
				$this->db->from('coop_finance_month_detail as t1');
				$this->db->where("
					t1.loan_id = '".$value['id']."' 
					AND t1.run_status = '0' 
					AND t1.pay_type = 'principal'
				");
				$row = $this->db->get()->result_array();
				$principal_month = 0;
				foreach($row as $key2 => $value2){
					$principal_month += $value2['pay_amount'];
				}
				$prev_loan_active_arr[$i]['principal_without_finance_month'] = $value['loan_amount_balance'] - $principal_month;
				$i++;
			}
			$this->db->select(array(
				'*'
			));
			$this->db->from('coop_loan_atm as t1');
			$this->db->where("t1.member_id = '".$member_id."' AND t1.loan_atm_status = '1' AND (t1.total_amount_approve - t1.total_amount_balance) <> 0");
			$prev_loan_active = $this->db->get()->result_array();
			//echo $this->db->last_query(); echo '<hr>';
			foreach($prev_loan_active as $key => $value){
				$prev_loan_active_arr[$i]['id'] = $value['loan_atm_id'];
				$prev_loan_active_arr[$i]['contract_number'] = $value['contract_number'];
				$prev_loan_active_arr[$i]['loan_amount_balance'] = $value['total_amount_approve'] - $value['total_amount_balance'];
				$prev_loan_active_arr[$i]['checked'] = "all";
				$cal_loan_interest = array();
				$cal_loan_interest['loan_atm_id'] = $value['loan_atm_id'];
				$cal_loan_interest['date_interesting'] = date('Y-m-d');
				//$cal_loan_interest['date_interesting'] = date('2018-09-26');
				//$interest_atm = $this->loan_libraries->cal_atm_interest_after_process($cal_loan_interest);
				$cal_loan_interest = array();
				$cal_loan_interest['loan_atm_id'] = $arr_data['row_loan_atm']['loan_atm_id'];
				$cal_loan_interest['date_interesting'] = date('Y-m-d');
				// $interest_amount = $this->loan_libraries->cal_atm_interest($cal_loan_interest);
				$interest_atm = $this->loan_libraries->cal_atm_interest_report_test($cal_loan_interest,"echo", array("month"=> date("m"), "year" => date("Y") ), false , true)['interest_month'];

				//$interest_atm = $this->loan_libraries->cal_atm_check_interest($cal_loan_interest);
				//000222 ประมวลผลผ่านรายการแล้ว
				//echo '<pre>'; print_r($interest_atm); echo '</pre>';
				$prev_loan_active_arr[$i]['principal'] = $prev_loan_active_arr[$i]['loan_amount_balance'];
				$prev_loan_active_arr[$i]['interest'] = $interest_atm;
				$prev_loan_active_arr[$i]['type'] = 'atm';
				$prev_loan_active_arr[$i]['prev_loan_total'] = $prev_loan_active_arr[$i]['loan_amount_balance']+$interest_atm;
				
				$ref_loan_deduct = $this->db->get_where("coop_loan_atm_prev_deduct", array(
					"ref_id" => $value['loan_atm_id'],
					"loan_atm_id" => $arr_data['row_loan_atm']['loan_atm_id']
				))->result_array()[0];
				if($ref_loan_deduct){
					$prev_loan_active_arr[$i]['ref_loan_deduct'] = $ref_loan_deduct;
				}
				
				$this->db->select(array(
					'*'
				));
				$this->db->from('coop_finance_month_detail as t1');
				$this->db->where("
					t1.loan_atm_id = '".$value['loan_atm_id']."' 
					AND t1.run_status = '0' 
					AND t1.pay_type = 'principal'
				");
				$row = $this->db->get()->result_array();
				$principal_month = 0;
				foreach($row as $key2 => $value2){
					$principal_month += $value2['pay_amount'];
				}
				$prev_loan_active_arr[$i]['principal_without_finance_month'] = $prev_loan_active_arr[$i]['loan_amount_balance'] - $principal_month;
				$i++;
			}
			
			//exit;
			//echo '<pre>'; print_r($prev_loan_active_arr); echo '</pre>'; exit;
			$arr_data['prev_loan_active'] = $prev_loan_active_arr;
			////////////////////////////รายการสัญญาเดิมที่หักลบ///////////////////////////////////	
		}
		
		$this->db->select(array(
			'loan_reason_id','loan_reason'
		));
		$this->db->from('coop_loan_reason');
		$rs_loan_reason = $this->db->get()->result_array();
		$arr_data['rs_loan_reason'] = $rs_loan_reason;
			
		$arr_data['loan_atm_status'] = $this->loan_atm_status;
		$arr_data['loan_atm_activate_status'] = $this->loan_atm_activate_status;
		$arr_data['loan_detail_status'] = $this->loan_detail_status;
		
		$this->db->select('share_collect_value');
		$this->db->from('coop_mem_share');
		$this->db->where("member_id = '".$member_id."' AND share_status IN('1')");
		$this->db->order_by('share_date DESC');
		$this->db->limit(1);
		$row_prev_share = $this->db->get()->result_array();
		$arr_data['share_collect_value'] = @$row_prev_share[0]['share_collect_value'];
		
		$this->db->select(array(
			'bank_id','bank_name'
		));
		$this->db->from('coop_bank');
		$row = $this->db->get()->result_array();
		$arr_data['coop_bank'] = $row;
		$this->libraries->template('loan_atm/index',$arr_data);
	}
	function loan_atm_save(){
		//echo"<pre>";print_r($_POST);echo"</pre>";exit;
		
		$this->db->select('*');
		$this->db->from("coop_loan_atm_setting");
		$this->db->limit(1);
		$row = $this->db->get()->result_array();
		$row_setting = @$row[0];
		
		$data_insert = array();
		$data_insert['loan_atm_id'] = $_POST['loan_atm_id'];
		$data_insert['member_id'] = $_POST['member_id'];
		$data_insert['loan_amount'] = str_replace(',','',$_POST['loan_amount']);
		$data_insert['loan_amount_balance'] = $data_insert['loan_amount'];
		$data_insert['loan_date'] = date('Y-m-d H:i:s');
		$data_insert['loan_status'] = '0';
		$data_insert['loan_description'] = 'ทำรายการกู้ATM';
		$data_insert['date_start_period'] = date('Y-m-t',strtotime('+1 month'));
		$data_insert['transaction_at'] = '0';
		$data_insert['transfer_status'] = '0';
		$data_insert['admin_id'] = $_SESSION['USER_ID'];
		
		$principal_per_month = $data_insert['loan_amount']/$row_setting['max_period'];
		$data_insert['principal_per_month'] = ceil($principal_per_month);
		//echo"<pre>";print_r($data_insert);exit;
		
		$this->db->select(array('petition_number'));
		$this->db->from('coop_loan_atm_detail');
		$this->db->order_by('petition_number DESC');
		$this->db->limit(1);
		$row_petition_number = $this->db->get()->result_array();
		if(!empty($row_petition_number)){
			$petition_number = (int)$row_petition_number[0]['petition_number']+1;
			$petition_number = sprintf('%06d',$petition_number);
		}else{
			$petition_number = sprintf('%06d',1);
		}
		$data_insert['petition_number'] = $petition_number;
		$data_insert['pay_type'] = $_POST['pay_type'];
		$data_insert['account_id'] = $_POST['account_id'];
		$data_insert['bank_id'] = $_POST['bank_id'];
		$data_insert['bank_account_id'] = $_POST['bank_account_id'];
		$this->db->insert('coop_loan_atm_detail',$data_insert);
		
		$this->db->select(array('total_amount_approve','total_amount_balance'));
		$this->db->from('coop_loan_atm');
		$this->db->where("loan_atm_id = '".$_POST['loan_atm_id']."'");
		$row_loan_atm = $this->db->get()->result_array();
		$total_amount_balance = $row_loan_atm[0]['total_amount_balance'] - str_replace(',','',$_POST['loan_amount']);
		
		$loan_amount_balance = $row_loan_atm[0]['total_amount_approve'] - $total_amount_balance;
		
		$data_insert = array();
		$data_insert['total_amount_balance'] = $total_amount_balance;
		$this->db->where('loan_atm_id',$_POST['loan_atm_id']);
		$this->db->update('coop_loan_atm',$data_insert);
		
		$atm_transaction = array();
		$atm_transaction['loan_atm_id'] = $_POST['loan_atm_id'];
		$atm_transaction['loan_amount_balance'] = $loan_amount_balance;
		$atm_transaction['transaction_datetime'] = date('Y-m-d H:i:s');
		$this->loan_libraries->atm_transaction($atm_transaction);
		
		$this->center_function->toast("บันทึกข้อมูลเรียบร้อยแล้ว");
		echo "<script> document.location.href='".base_url(PROJECTPATH.'/loan_atm?member_id='.@$_POST['member_id'])."' </script>";
		exit;
	}
	
	function loan_contract_save(){
		// echo"<pre>";print_r($_POST);print_r($_FILES);exit;
		// echo"<pre>";print_r($_POST);//exit;
		//echo"<pre>";print_r($_POST);
		//echo"<pre>";print_r($_POST['prev_loan']);exit;
		
		$data_insert = array();
		$data_insert['member_id'] = $_POST['member_id'];
		$data_insert['total_amount'] = str_replace(',','',$_POST['total_amount']);
		$data_insert['total_amount_balance'] = $data_insert['total_amount'];
		$data_insert['createdatetime'] = date('Y-m-d H:i:s');
		$data_insert['loan_atm_status'] = '0';
		$data_insert['loan_reason'] = $_POST['loan_reason'];
		$data_insert['admin_id'] = $_SESSION['USER_ID'];
		$data_insert['activate_status'] = '0';
		//echo $_POST['loan_atm_id'].'<br>';
		//exit;
		if(@$_POST['loan_atm_id']!=''){
			$this->db->where('loan_atm_id',$_POST['loan_atm_id']);
			$this->db->update('coop_loan_atm',$data_insert);

			$loan_atm_id = $_POST['loan_atm_id'];

			$this->db->where('loan_atm_id',$loan_atm_id);
			$this->db->delete('coop_loan_atm_prev_deduct');
		}else{

			$this->db->select(array('petition_number'));
			$this->db->from('coop_loan_atm');
			$this->db->order_by('petition_number DESC');
			$this->db->limit(1);
			$row_petition_number = $this->db->get()->result_array();
			if(!empty($row_petition_number)){
				$petition_number = (int)$row_petition_number[0]['petition_number']+1;
				$petition_number = sprintf('%06d',$petition_number);
			}else{
				$petition_number = sprintf('%06d',1);
			}
			$data_insert['petition_number'] = $petition_number;
			
			$this->db->insert('coop_loan_atm',$data_insert);
			
			$loan_atm_id = $this->db->insert_id();

		}

		foreach ($_POST['prev_loan'] as $key => $value) {
			if(@$value['id']!=''){
				//$pay_type = isset($_POST['interest_amount']) && $_POST['interest_amount'] < 0 ? 'principal' : 'all';
				$pay_type = $value['pay_type'];
				if($pay_type == 'all'){
					$principal = $value['principal'];
					$interest = $value['interest'];
				}else{
					$principal = str_replace(',','',$value['amount']);
					$interest = 0;
				}
				
				$data_insert = array();
				$data_insert['loan_atm_id'] = $loan_atm_id;
				$data_insert['ref_id'] = $value['id'];
				$data_insert['data_type'] = @$value['type'];
				$data_insert['pay_type'] = $pay_type;
				$data_insert['pay_amount'] = str_replace(',','',$value['amount']);
				$data_insert['principal_amount'] = @$principal;
				$data_insert['interest_amount'] = @$interest;
				$this->db->insert('coop_loan_atm_prev_deduct', $data_insert);
			}
		}
		
		if($loan_atm_id!=''){
			$output_dir = $_SERVER["DOCUMENT_ROOT"].PROJECTPATH."/assets/uploads/loan_atm_attach/";
			if($_FILES['file_attach']['name'][0]!=''){
				foreach($_FILES['file_attach']['name'] as $key => $value){
					$new_file_name = $this->center_function->create_file_name($output_dir,$_FILES['file_attach']['name'][$key]);
					@copy($_FILES["file_attach"]["tmp_name"][$key],$output_dir.$new_file_name);
					
					$data_insert = array();
					$data_insert['loan_atm_id'] = $loan_atm_id;
					$data_insert['file_old_name'] = $_FILES['file_attach']['name'][$key];
					$data_insert['file_name'] = $new_file_name;
					$data_insert['file_path'] = $output_dir.$new_file_name;
					$data_insert['file_type'] = $_FILES['file_attach']['type'][$key];
					$this->db->insert('coop_loan_atm_file_attach', $data_insert);
				}
			}
		}
		

		// echo"<pre>";print_r($_POST['prev_loan']);echo"</pre>";
		// if(!empty($_POST['prev_loan'])){
		// 	foreach($_POST['prev_loan'] as $key => $value){
		// 		if(@$value['id']!=''){
		// 			if($value['pay_type'] == 'all'){
		// 				if(@$value['type'] == 'loan'){
		// 					$cal_loan_interest = array();
		// 					$cal_loan_interest['loan_id'] = $value['id'];
		// 					$cal_loan_interest['date_interesting'] = date('Y-m-d');
		// 					$interest_amount = $this->loan_libraries->cal_loan_interest($cal_loan_interest);
		// 				}
						
		// 				if(@$value['type'] == 'atm'){
		// 					$cal_loan_interest = array();
		// 					$cal_loan_interest['loan_atm_id'] = @$row_prev_atm['loan_atm_id'];
		// 					$cal_loan_interest['date_interesting'] = date('Y-m-d');
		// 					$interest_amount = $this->loan_libraries->cal_atm_interest($cal_loan_interest);
		// 				}
		// 			}else{
		// 				$interest_amount = 0;
		// 			}
			
		// 			$data_insert = array();
		// 			$data_insert['loan_atm_id'] = $loan_atm_id;
		// 			$data_insert['ref_id'] = $value['id'];
		// 			$data_insert['data_type'] = $value['type'];
		// 			$data_insert['pay_type'] = $value['pay_type'];
		// 			$data_insert['pay_amount'] = str_replace(',','',$value['amount']);
		// 			$data_insert['principal_amount'] = $value['principal_amount'];
		// 			$data_insert['interest_amount'] = $interest_amount;
		// 			$this->db->insert('coop_loan_atm_prev_deduct', $data_insert);
		// 			//echo"<pre>";print_r($data_insert);echo"</pre>";
		// 		}
		// 	}
		// }
		$this->center_function->toast("บันทึกข้อมูลเรียบร้อยแล้ว");
		echo "<script> document.location.href='".base_url(PROJECTPATH.'/loan_atm?member_id='.@$_POST['member_id'])."' </script>";
	}
	
	function ajax_delete_loan_file_attach(){
		$this->db->select(array('*'));
		$this->db->from("coop_loan_atm_file_attach");
		$this->db->where("id = '".@$_POST['id']."'");
		$rs = $this->db->get()->result_array();
		$row = @$rs[0];

		//$attach_path = "../uploads/loan_attach/";
		$attach_path = $_SERVER["DOCUMENT_ROOT"].PROJECTPATH."/assets/uploads/loan_atm_attach/";
		$file = @$attach_path.@$row['file_name'];
		unlink($file);

		$this->db->where("id", @$_POST['id'] );
		$this->db->delete("coop_loan_atm_file_attach");	
		if(@$rs){
			echo "success";
		}else{
			echo "error";
		}
		exit;
	}	
	
	public function loan_atm_approve()
	{
		$arr_data = array();
		
		$where = '1=1';
		if(@$_GET['loan_status']!=''){
			if(@$_GET['loan_status'] == '1'){
				$where .= " AND coop_loan_atm.loan_atm_status IN('1','4') ";
			}else{
				$where .= " AND coop_loan_atm.loan_atm_status = '".$_GET['loan_status']." '";
			}
		}else{
			$where .= " AND coop_loan_atm.loan_atm_status IN('0','1','4','5') ";
		}
		
		$order_by = 'createdatetime DESC ,loan_atm_id DESC';
		if($_GET['approve_date']!=''){
			$approve_date_arr = explode('/',$_GET['approve_date']);
			$approve_day = stripslashes($approve_date_arr[0]);
			$approve_month = stripslashes($approve_date_arr[1]);
			$approve_year = stripslashes($approve_date_arr[2]);
			$_GET['approve_date'] = $approve_day."/".$approve_month."/".$approve_year;
			$approve_year -= 543;
			$approve_date = $approve_year.'-'.$approve_month.'-'.$approve_day;
			$where .= " AND coop_loan_atm.approve_date >= '".$approve_date." 00:00:00.000'";
			$order_by = 'coop_loan_atm.approve_date ASC ,loan_atm_id DESC';
		}
		if($_GET['thru_date']!=''){
			$thru_date_arr = explode('/',$_GET['thru_date']);
			$thru_day = stripslashes($thru_date_arr[0]);
			$thru_month = stripslashes($thru_date_arr[1]);
			$thru_year = stripslashes($thru_date_arr[2]);
			$_GET['thru_date'] = $thru_day."/".$thru_month."/".$thru_year;
			$thru_year -= 543;
			$thru_date = $thru_year.'-'.$thru_month.'-'.$thru_day;
			$where .= " AND coop_loan_atm.approve_date <= '".$thru_date." 23:59:59.000'";
			$order_by = 'coop_loan_atm.approve_date ASC ,loan_atm_id DESC';
		}

		$x=0;
		$join_arr = array();
		/*$join_arr[$x]['table'] = 'coop_mem_apply';
		$join_arr[$x]['condition'] = 'coop_mem_apply.member_id = coop_loan_atm.member_id';
		$join_arr[$x]['type'] = 'left';
		$x++;
		*/
		$join_arr[$x]['table'] = 'coop_user';
		$join_arr[$x]['condition'] = 'coop_loan_atm.admin_id = coop_user.user_id';
		$join_arr[$x]['type'] = 'left';
		
		$this->paginater_all->type(DB_TYPE);
		//$this->paginater_all->select('coop_loan_atm.*, coop_mem_apply.firstname_th, coop_mem_apply.lastname_th, coop_user.user_name');
		$this->paginater_all->select('
										coop_loan_atm.createdatetime,
										coop_loan_atm.loan_atm_id,
										coop_loan_atm.loan_atm_status,
										coop_loan_atm.total_amount,
										coop_loan_atm.deduct_receipt_id,
										coop_loan_atm.member_id,
										coop_loan_atm.petition_number,
										coop_loan_atm.approve_date,
										coop_user.user_name
									');
		$this->paginater_all->main_table('coop_loan_atm');
		$this->paginater_all->where("{$where}");
		$this->paginater_all->page_now(@$_GET["page"]);
		$this->paginater_all->per_page(100);
		$this->paginater_all->page_link_limit(20);
		$this->paginater_all->order_by($order_by);
		$this->paginater_all->join_arr($join_arr);
		$row = $this->paginater_all->paginater_process();
		//echo"<pre>";print_r($row);exit;
		$paging = $this->pagination_center->paginating($row['page'], $row['num_rows'], $row['per_page'], $row['page_link_limit'],$_GET);//$page_now = 1, $row_total = 1, $per_page = 20, $page_limit = 20
		$i = $row['page_start'];
		if(@$_GET['dev'] == 'dev'){
			echo $this->db->last_query();exit;
		}
		foreach($row['data'] AS $key=>$value){
			$this->db->select('coop_mem_apply.firstname_th, coop_mem_apply.lastname_th');
			$this->db->from("coop_mem_apply");
			$this->db->where("member_id = '".@$value['member_id']."'");
			$rs_member = $this->db->get()->result_array();
			$row_member = @$rs_member[0];
			$row['data'][$key]['firstname_th'] = $row_member['firstname_th'];
			$row['data'][$key]['lastname_th'] = $row_member['lastname_th'];
		}

		$arr_data['num_rows'] = $row['num_rows'];
		$arr_data['paging'] = $paging;
		$arr_data['data'] = $row['data'];
		$arr_data['i'] = $i;
		
		$loan_type = array();
		$this->db->select('*');
		$this->db->from("coop_loan_type");
		$rs_type = $this->db->get()->result_array();
		foreach($rs_type as $key => $row_type){
			$loan_type[$row_type['id']] = $row_type['loan_type'];
		}
		$arr_data['loan_type'] = $loan_type; 
		$arr_data['loan_atm_status'] = $arr_data['loan_atm_status'] = $this->loan_atm_status;
		$this->libraries->template('loan_atm/loan_atm_approve',$arr_data);
	}
	
	function loan_atm_not_approve(){
			$this->db->select('*');
			$this->db->from("coop_loan_atm");
			$this->db->where("loan_atm_id = '".@$_GET['loan_atm_id']."'");
			$rs_loan = $this->db->get()->result_array();
			$rs_loan = @$rs_loan[0];
			
			$data_insert = array();
			
			$data_insert['loan_atm_status'] = @$_GET['status_to'];
			$this->db->where('loan_atm_id', @$_GET['loan_atm_id']);
			$this->db->update('coop_loan_atm', $data_insert);
			
			if($rs_loan['change_from']!=''){
				$data_insert = array();
				$data_insert['change_amount_status'] = '';
				$this->db->where('loan_atm_id', @$rs_loan['change_from']);
				$this->db->update('coop_loan_atm', $data_insert);
			}
			
			$this->center_function->toast('บันทึกข้อมูลเรียบร้อยแล้ว');
			echo "<script> document.location.href='".base_url(PROJECTPATH.'/loan_atm/loan_atm_approve')."' </script>";
			exit;
	}
	
	function get_loan_atm_data(){
		$this->db->select(array(
			't1.loan_atm_id',
			't1.petition_number',
			't1.member_id',
			't1.total_amount',
			't2.firstname_th',
			't2.lastname_th',
			't3.prename_short',
			't1.change_from'
		));
		$this->db->from("coop_loan_atm t1");
		$this->db->join("coop_mem_apply t2",'t1.member_id = t2.member_id','inner');
		$this->db->join("coop_prename t3",'t2.prename_id = t3.prename_id','left');
		$this->db->where("loan_atm_id = '".$_POST['loan_atm_id']."'");
		$row = $this->db->get()->result_array();
		$coop_loan_atm = $row[0];
		$data = array();
		foreach($row[0] as $key => $value){
			if($key == 'total_amount'){
				$data['loan_atm_data'][$key] = number_format($value);
			}else{
				$data['loan_atm_data'][$key] = $value;
			}
		}
		
		//หักกลบแบบเก่า
		$this->db->select(array(
			't1.atm_number'
		));
		$this->db->from("coop_atm_card t1");
		$this->db->where("member_id = '".$coop_loan_atm['member_id']."' AND atm_card_status = '0'");
		$row = $this->db->get()->result_array();
		$data['loan_atm_data']['atm_number'] = @$row[0]['atm_number'];
		
		$this->db->select(array(
			't1.id',
			't1.contract_number',
			't1.loan_amount_balance',
		));
		$this->db->from("coop_loan t1");
		$this->db->join("coop_loan_name t2",'t1.loan_type = t2.loan_name_id','inner');
		$this->db->join("coop_loan_type t3",'t2.loan_type_id = t3.id','inner');
		$this->db->where("t1.loan_status = '1' AND member_id = '".$data['loan_atm_data']['member_id']."' AND t3.loan_type_code = 'emergent'");
		$row = $this->db->get()->result_array();
		$loan_amount_balance = 0;
		$prev_loan_number = '';
		$process_balance = 0;
		@$data['loan_atm_data']['sql'] = $this->db->last_query();
		foreach($row as $key => $value){
			//echo '<pre>'; print_r($value); echo '</pre>';
			$cal_loan_interest = array();
			$cal_loan_interest['loan_id'] = @$value['id'];
			$cal_loan_interest['date_interesting'] = date('Y-m-d');
			$interest_amount = $this->loan_libraries->cal_loan_interest($cal_loan_interest);
			//$loan_amount_balance += ($value['loan_amount_balance']+$interest_amount);
			$loan_amount_balance += @$value['loan_amount_balance'];
			$prev_loan_number .= $value['contract_number'].",";
			
			//ยอดเงินหลังประมวลผล
			//month_profile
			/*$this->db->select(array('t1.pay_amount','t2.profile_month','t2.profile_year'));
			$this->db->from('coop_finance_month_detail AS t1');
			$this->db->join("coop_finance_month_profile AS t2","t1.profile_id = t2.profile_id","left");
			$this->db->where("t1.loan_id = '".@$value['id']."' AND t1.pay_type = 'principal' AND t1.deduct_code = 'LOAN' AND t1.run_status = '0'");
			$this->db->limit(1);
			$rs_finance_process = $this->db->get()->result_array();
			$row_finance_process = @$rs_finance_process[0];
			$process_balance += @$row_finance_process['pay_amount'];
			*/
		}
		
		$prev_loan_number = substr($prev_loan_number,0,-1);
		//@$data['loan_atm_data']['prev_loan'] += $loan_amount_balance;
		//$loan_amount_balance = @$loan_amount_balance-@$process_balance;
		$data['loan_atm_data']['prev_loan'] = @$loan_amount_balance;
		
		/*
		$this->db->select(array(
			't1.loan_atm_id',
			't1.contract_number',
			't1.total_amount_approve',
			't1.total_amount_balance',
		));
		$this->db->from("coop_loan_atm t1");
		$this->db->where("t1.loan_atm_status = '1' AND member_id = '".$data['loan_atm_data']['member_id']."'");
		$row = $this->db->get()->result_array();
		$row_prev_atm = @$row[0];
		$loan_amount_balance_atm = 0;
		$prev_loan_atm_number = '';
		if(!empty($row_prev_atm)){
			$loan_amount_balance_atm = @$row_prev_atm['total_amount_approve'] - @$row_prev_atm['total_amount_balance'];
			$prev_loan_atm_number = @$row_prev_atm['contract_number'];
			$cal_loan_interest = array();
			$cal_loan_interest['loan_atm_id'] = @$row_prev_atm['loan_atm_id'];
			$cal_loan_interest['date_interesting'] = date('Y-m-d');
			$interest_amount = $this->loan_libraries->cal_atm_interest($cal_loan_interest);
			$loan_amount_balance_atm += $interest_amount;
			
			@$data['loan_atm_data']['prev_loan'] += $loan_amount_balance_atm;
		}
		*/
		
		
		
		//หักกลบแบบใหม่
		$this->db->select(array('*'));
		$this->db->from("coop_loan_atm_prev_deduct");
		$this->db->where("loan_atm_id = '".@$_POST['loan_atm_id']."'");
		$rs_prev_deduct = $this->db->get()->result_array();		
		$loan_atm_prev_pay_amount = 0;
		$loan_atm_prev_pay_interest_amount = 0;
		foreach($rs_prev_deduct as $key => $value){
			$loan_atm_prev_pay_amount += @$value['principal_amount'];
			$loan_atm_prev_pay_interest_amount += @$value['interest_amount'];			
		}
		$loan_amount_balance_atm = @$loan_atm_prev_pay_amount+@$loan_atm_prev_pay_interest_amount;
		@$data['loan_atm_data']['prev_loan'] = $loan_amount_balance_atm;
		
		@$data['loan_atm_data']['t1'] = number_format(@$loan_atm_prev_pay_amount,2);
		@$data['loan_atm_data']['t2'] = number_format(@$loan_atm_prev_pay_interest_amount,2);
		@$data['loan_atm_data']['t3'] = number_format(@$loan_amount_balance_atm,2);
			
		//$data['loan_atm_data']['total_amount_balance'] = number_format($coop_loan_atm['total_amount'] - @$loan_amount_balance - @$loan_amount_balance_atm,2);
		$data['loan_atm_data']['total_amount_balance'] = number_format($coop_loan_atm['total_amount'] - @$loan_amount_balance_atm,2);
		echo json_encode($data);
		exit;
	}
	
	function loan_approve_save_bk1(){
		
		//echo"<pre>";print_r($_POST);exit;
		//ปีในการ gen เลขสัญญา		
		$rs_month_account = $this->db->select('accm_month_ini')
		->from("coop_account_period_setting")
		->limit(1)
		->get()->result_array();
		$month_account = $rs_month_account[0]['accm_month_ini'];
		$month_now = date('m');
		if((int)$month_now >= (int)$month_account){
			$year = (date('Y')+543)+1;
		}else{
			$year = (date('Y')+543);		
		}
		$year_short = substr($year,2,2);
		$new_contact_number = '';
		$this->db->select('*');
		$this->db->from("coop_loan_atm_setting");
		$this->db->limit(1);
		$rs_term_of_loan = $this->db->get()->result_array();
		$row_term_of_loan = @$rs_term_of_loan[0];
		$new_contact_number = $row_term_of_loan['prefix_code'].$year_short;
		
		$contact_number_now = $this->loan_libraries->get_contract_number($year);
		
		$new_contact_number .= sprintf("% 05d",$contact_number_now);
		//$new_contact_number = $new_contact_number."/".(date('Y')+543);
		
		$this->db->select('loan_atm_account_id');
		$this->db->from("coop_mem_apply");
		$this->db->where("member_id = '".$_POST['member_id']."'");
		$row_loan_atm_account_id = $this->db->get()->result_array();
		$account_id = @$row_loan_atm_account_id[0]['loan_atm_account_id'];
		
		if(@$account_id==''){
			$account_id = '002'.sprintf('%08d',$_POST['member_id']);
			$data_insert = array();
			$data_insert['loan_atm_account_id'] = $account_id;
			$this->db->where('member_id',$_POST['member_id']);
			$this->db->update('coop_mem_apply',$data_insert);
		}
		
		$data_insert = array();
		$data_insert['contract_number'] = @$new_contact_number;
		$data_insert['loan_atm_status'] = '1';
		$data_insert['total_amount_approve'] = str_replace(',','',$_POST['total_amount_approve']);
		$data_insert['total_amount_balance'] = str_replace(',','',$_POST['total_amount_approve']);
		$data_insert['prev_loan'] = str_replace(',','',$_POST['prev_loan']);
		$data_insert['approve_date'] = date('Y-m-d H:i:s');
		$data_insert['atm_card_number'] = @$_POST['atm_card_number'];
		$data_insert['account_id'] = @$account_id;
		$data_insert['activate_status'] = '0';
		$this->db->where('loan_atm_id', @$_POST['loan_atm_id']);
		$this->db->update('coop_loan_atm', $data_insert);
		
		$this->db->select(array('*'));
		$this->db->from('coop_loan_atm');
		$this->db->where("loan_atm_id = '".@$_POST['loan_atm_id']."'");
		$row = $this->db->get()->result_array();
		$row_atm = @$row[0];
		
/////////////////////////////////////////////////////////// หักกลบ ////////////////////////////////////////////////////////////////
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
		
		$loan_amount_balance = 0;
		$receipt_arr = array();
		$r=0;
		$this->db->select(array(
			't1.id',
			't1.contract_number',
			't1.loan_amount_balance'
		));
		$this->db->from("coop_loan t1");
		$this->db->join("coop_loan_name t2",'t1.loan_type = t2.loan_name_id','inner');
		$this->db->join("coop_loan_type t3",'t2.loan_type_id = t3.id','inner');
		$this->db->where("t1.loan_status = '1' AND member_id = '".@$_POST['member_id']."' AND t3.loan_type_code = 'emergent'");
		$row = $this->db->get()->result_array();
		foreach($row as $key => $value){
			$cal_loan_interest = array();
			$cal_loan_interest['loan_id'] = $value['id'];
			$cal_loan_interest['date_interesting'] = date('Y-m-d');
			$interest_amount = $this->loan_libraries->cal_loan_interest($cal_loan_interest);
			
			/*
			$data_insert = array();
			$data_insert['loan_atm_id'] = @$_POST['loan_atm_id'];
			$data_insert['ref_id'] = @$value['id'];
			$data_insert['data_type'] = 'loan';
			$data_insert['pay_type'] = 'all';
			$data_insert['pay_amount'] = ($value['loan_amount_balance']+$interest_amount);
			$data_insert['principal_amount'] = $value['loan_amount_balance'];
			$data_insert['interest_amount'] = $interest_amount;
			$this->db->insert('coop_loan_atm_prev_deduct', $data_insert);
			*/
			
			$data_insert = array();
			$data_insert['loan_atm_id'] = @$_POST['loan_atm_id'];
			$data_insert['member_id'] = @$_POST['member_id'];
			$data_insert['loan_amount'] = ($value['loan_amount_balance']+$interest_amount);
			$data_insert['loan_amount_balance'] = ($value['loan_amount_balance']+$interest_amount);
			$data_insert['loan_date'] = date('Y-m-d H:i:s');
			$this->db->select(array('petition_number'));
			$this->db->from('coop_loan_atm_detail');
			$this->db->order_by('petition_number DESC');
			$this->db->limit(1);
			$row_petition_number = $this->db->get()->result_array();
			if(!empty($row_petition_number)){
				$petition_number = (int)$row_petition_number[0]['petition_number']+1;
				$petition_number = sprintf('%06d',$petition_number);
			}else{
				$petition_number = sprintf('%06d',1);
			}
			$data_insert['petition_number'] = $petition_number;
			$data_insert['loan_status'] = '0';
			$data_insert['loan_description'] = 'หักกลบสัญญาเก่า '.$value['contract_number'];
			$data_insert['date_start_period'] = date('Y-m-t',strtotime('+1 month'));
			$data_insert['transaction_at'] = '0';
			$data_insert['transfer_status'] = '1';
			$this->db->insert('coop_loan_atm_detail', $data_insert);
			
			$loan_amount_balance += ($value['loan_amount_balance']+$interest_amount);
			$atm_transaction = array();
			$atm_transaction['loan_atm_id'] = $_POST['loan_atm_id'];
			$atm_transaction['loan_amount_balance'] = $loan_amount_balance;
			$atm_transaction['transaction_datetime'] = date('Y-m-d H:i:s');
			$this->loan_libraries->atm_transaction($atm_transaction);
			
			$receipt_arr[$r]['receipt_id'] = $receipt_id;
			$receipt_arr[$r]['member_id'] = @$_POST['member_id'];
			$receipt_arr[$r]['loan_id'] = $value['id'];
			$receipt_arr[$r]['account_list_id'] = '15';
			$receipt_arr[$r]['principal_payment'] = $value['loan_amount_balance'];
			$receipt_arr[$r]['interest'] = $interest_amount;
			$receipt_arr[$r]['total_amount'] = $value['loan_amount_balance']+$interest_amount;
			$receipt_arr[$r]['payment_date'] = date('Y-m-d');
			$receipt_arr[$r]['createdatetime'] = date('Y-m-d H:i:s');
			$receipt_arr[$r]['loan_amount_balance'] = '0';
			$receipt_arr[$r]['transaction_text'] = 'หักกลบเงินกู้เลขที่สัญญา '.$value['contract_number'];
			$receipt_arr[$r]['deduct_type'] = 'all';
			$r++;
			
			$data_insert = array();
			$data_insert['loan_amount_balance'] = '0';
			$data_insert['loan_status'] = '4';
			$this->db->where('id', $value['id']);
			$this->db->update('coop_loan', $data_insert);
			
			$loan_transaction = array();
			$loan_transaction['loan_id'] = $value['id'];
			$loan_transaction['loan_amount_balance'] = '0';
			$loan_transaction['transaction_datetime'] = date('Y-m-d H:i:s');
			$this->loan_libraries->loan_transaction($loan_transaction);
		}
			
			$this->db->select(array(
				't1.loan_atm_id',
				't1.total_amount_approve',
				't1.total_amount_balance',
				't1.contract_number'
			));
			$this->db->from("coop_loan_atm t1");
			$this->db->where("t1.loan_atm_status = '1' AND loan_atm_id != '".$_POST['loan_atm_id']."' AND member_id = '".@$_POST['member_id']."'");
			$row = $this->db->get()->result_array();
			foreach($row as $key => $value){
				$cal_loan_interest = array();
				$cal_loan_interest['loan_atm_id'] = $value['loan_atm_id'];
				$cal_loan_interest['date_interesting'] = date('Y-m-d');
				$interest_amount = $this->loan_libraries->cal_atm_interest($cal_loan_interest);
				
				/*
				$data_insert = array();
				$data_insert['loan_atm_id'] = @$_POST['loan_atm_id'];
				$data_insert['ref_id'] = @$value['loan_atm_id'];
				$data_insert['data_type'] = 'atm';
				$data_insert['pay_type'] = 'all';
				$data_insert['pay_amount'] = (($value['total_amount_approve']-$value['total_amount_balance'])+$interest_amount);
				$data_insert['principal_amount'] = ($value['total_amount_approve']-$value['total_amount_balance']);
				$data_insert['interest_amount'] = $interest_amount;
				$this->db->insert('coop_loan_atm_prev_deduct', $data_insert);
				*/
				
				$data_insert = array();
				$data_insert['loan_atm_id'] = @$_POST['loan_atm_id'];
				$data_insert['member_id'] = @$_POST['member_id'];
				$data_insert['loan_amount'] = (($value['total_amount_approve']-$value['total_amount_balance'])+$interest_amount);
				$data_insert['loan_amount_balance'] = (($value['total_amount_approve']-$value['total_amount_balance'])+$interest_amount);
				$data_insert['loan_date'] = date('Y-m-d H:i:s');
				$this->db->select(array('petition_number'));
				$this->db->from('coop_loan_atm_detail');
				$this->db->order_by('petition_number DESC');
				$this->db->limit(1);
				$row_petition_number = $this->db->get()->result_array();
				if(!empty($row_petition_number)){
					$petition_number = (int)$row_petition_number[0]['petition_number']+1;
					$petition_number = sprintf('%06d',$petition_number);
				}else{
					$petition_number = sprintf('%06d',1);
				}
				$data_insert['petition_number'] = $petition_number;
				$data_insert['loan_status'] = '0';
				$data_insert['loan_description'] = 'หักกลบสัญญาเก่า '.$value['contract_number'];
				$data_insert['date_start_period'] = date('Y-m-t',strtotime('+1 month'));
				$data_insert['transaction_at'] = '0';
				$data_insert['transfer_status'] = '1';
				$this->db->insert('coop_loan_atm_detail', $data_insert);
				
				$loan_amount_balance += (($value['total_amount_approve']-$value['total_amount_balance'])+$interest_amount);
				$atm_transaction = array();
				$atm_transaction['loan_atm_id'] = $_POST['loan_atm_id'];
				$atm_transaction['loan_amount_balance'] = $loan_amount_balance;
				$atm_transaction['transaction_datetime'] = date('Y-m-d H:i:s');
				$this->loan_libraries->atm_transaction($atm_transaction);
				
				$receipt_arr[$r]['receipt_id'] = $receipt_id;
				$receipt_arr[$r]['member_id'] = @$_POST['member_id'];
				$receipt_arr[$r]['loan_atm_id'] = $value['loan_atm_id'];
				$receipt_arr[$r]['account_list_id'] = '31';
				$receipt_arr[$r]['principal_payment'] = ($value['total_amount_approve']-$value['total_amount_balance']);
				$receipt_arr[$r]['interest'] = $interest_amount;
				$receipt_arr[$r]['total_amount'] = (($value['total_amount_approve']-$value['total_amount_balance'])+$interest_amount);
				$receipt_arr[$r]['payment_date'] = date('Y-m-d');
				$receipt_arr[$r]['createdatetime'] = date('Y-m-d H:i:s');
				$receipt_arr[$r]['loan_amount_balance'] = '0';
				$receipt_arr[$r]['transaction_text'] = 'หักกลบเงินกู้เลขที่สัญญา '.$value['contract_number'];
				$receipt_arr[$r]['deduct_type'] = 'all';
				$r++;
				
				$data_insert = array();
				$data_insert['total_amount_balance'] = $value['total_amount_approve'];
				$data_insert['loan_atm_status'] = '3';
				$this->db->where('loan_atm_id', $value['loan_atm_id']);
				$this->db->update('coop_loan_atm', $data_insert);
				
				$data_insert = array();
				$data_insert['loan_amount_balance'] = '0';
				$data_insert['loan_status'] = '1';
				$this->db->where('loan_atm_id', $value['loan_atm_id']);
				$this->db->update('coop_loan_atm_detail', $data_insert);
				
				$atm_transaction = array();
				$atm_transaction['loan_atm_id'] = $value['loan_atm_id'];
				$atm_transaction['loan_amount_balance'] = '0';
				$atm_transaction['transaction_datetime'] = date('Y-m-d H:i:s');
				$this->loan_libraries->atm_transaction($atm_transaction);
				
			}
			
			$sum_count = 0;
			foreach($receipt_arr as $key => $value){
				$data_insert = array();
				$data_insert['receipt_id'] = $value['receipt_id'];
				$data_insert['receipt_list'] = $value['account_list_id'];
				$data_insert['receipt_count'] = $value['total_amount'];
				$this->db->insert('coop_receipt_detail', $data_insert);
				
				//บันทึกการชำระเงิน
				$data_insert = array();
				$data_insert['receipt_id'] = $value['receipt_id'];
				$data_insert['member_id'] = @$value['member_id'];
				$data_insert['loan_id'] = @$value['loan_id'];
				$data_insert['loan_atm_id'] = @$value['loan_atm_id'];
				$data_insert['account_list_id'] = $value['account_list_id'];
				$data_insert['principal_payment'] = @$value['principal_payment'];
				$data_insert['interest'] = @$value['interest'];
				$data_insert['total_amount'] = @$value['total_amount'];
				$data_insert['payment_date'] = @$value['payment_date'];
				$data_insert['loan_amount_balance'] = @$value['loan_amount_balance'];
				$data_insert['createdatetime'] = @$value['createdatetime'];
				$data_insert['transaction_text'] = @$value['transaction_text'];
				$data_insert['deduct_type'] = @$value['deduct_type'];
				$this->db->insert('coop_finance_transaction', $data_insert);
				$sum_count += @$value['total_amount'];
			}
			if($sum_count>0){
				$data_insert = array();
				$data_insert['receipt_id'] = $receipt_id;
				$data_insert['member_id'] = @$_POST['member_id'];
				$data_insert['admin_id'] = @$_SESSION['USER_ID'];
				$data_insert['sumcount'] = $sum_count;
				$data_insert['receipt_datetime'] = date('Y-m-d H:i:s');
				$this->db->insert('coop_receipt', $data_insert);
				
				$data_insert = array();
				$data_insert['deduct_receipt_id'] = $receipt_id;
				$data_insert['total_amount_balance'] = $row_atm['total_amount_approve'] - $sum_count;
				$this->db->where('loan_atm_id',$_POST['loan_atm_id']);
				$this->db->update('coop_loan_atm',$data_insert);
				//echo '<pre>'; print_r($data_insert); echo '</pre>';
			}
/////////////////////////////////////////////////////////// หักกลบ ////////////////////////////////////////////////////////////////

		//exit;
		$this->center_function->toast("บันทึกข้อมูลเรียบร้อยแล้ว");
		echo "<script> document.location.href='".base_url(PROJECTPATH.'/loan_atm/loan_atm_approve')."' </script>";
		exit;
	}
	
	function loan_approve_save(){
		//echo"<pre>";print_r($_POST);exit;
		//ปีในการ gen เลขสัญญา		
		$rs_month_account = $this->db->select('accm_month_ini')
		->from("coop_account_period_setting")
		->limit(1)
		->get()->result_array();
		$month_account = $rs_month_account[0]['accm_month_ini'];
		$month_now = date('m');
		if((int)$month_now >= (int)$month_account){
			$year = (date('Y')+543)+1;
		}else{
			$year = (date('Y')+543);		
		}
		$year_short = substr($year,2,2);
		$new_contact_number = '';
		$this->db->select('*');
		$this->db->from("coop_loan_atm_setting");
		$this->db->limit(1);
		$rs_term_of_loan = $this->db->get()->result_array();
		$row_term_of_loan = @$rs_term_of_loan[0];
		$new_contact_number = $row_term_of_loan['prefix_code'].$year_short;
		
		$contact_number_now = $this->loan_libraries->get_contract_number($year);
		
		$new_contact_number .= sprintf("% 05d",$contact_number_now);
		//$new_contact_number = $new_contact_number."/".(date('Y')+543);
		
		$this->db->select('loan_atm_account_id');
		$this->db->from("coop_mem_apply");
		$this->db->where("member_id = '".$_POST['member_id']."'");
		$row_loan_atm_account_id = $this->db->get()->result_array();
		$account_id = @$row_loan_atm_account_id[0]['loan_atm_account_id'];
		
		if(@$account_id==''){
			$account_id = '002'.sprintf('%08d',$_POST['member_id']);
			$data_insert = array();
			$data_insert['loan_atm_account_id'] = $account_id;
			$this->db->where('member_id',$_POST['member_id']);
			$this->db->update('coop_mem_apply',$data_insert);
		}
		
		$data_insert = array();
		$data_insert['contract_number'] = @$new_contact_number;
		$data_insert['loan_atm_status'] = '1';
		$data_insert['total_amount_approve'] = str_replace(',','',$_POST['total_amount_approve']);
		$data_insert['total_amount_balance'] = str_replace(',','',$_POST['total_amount_approve']);
		$data_insert['prev_loan'] = str_replace(',','',$_POST['prev_loan']);
		$data_insert['approve_date'] = date('Y-m-d H:i:s');
		$data_insert['atm_card_number'] = @$_POST['atm_card_number'];
		$data_insert['account_id'] = @$account_id;
		$data_insert['activate_status'] = '0';
		$this->db->where('loan_atm_id', @$_POST['loan_atm_id']);
		$this->db->update('coop_loan_atm', $data_insert);
		
		$this->db->select(array('*'));
		$this->db->from('coop_loan_atm');
		$this->db->where("loan_atm_id = '".@$_POST['loan_atm_id']."'");
		$row = $this->db->get()->result_array();
		$row_atm = @$row[0];
		
/////////////////////////////////////////////////////////// หักกลบ ////////////////////////////////////////////////////////////////
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
		
		$loan_amount_balance = 0;
		$loan_amount_balance_all = 0;
		$receipt_arr = array();
		$r=0;		
		$date_interesting = date('Y-m-d');		
		//////////////////	
		$this->db->select(array('t1.*',
								't2.contract_number',
								't2.loan_amount_balance',
								't2.id',
								't2.createdatetime',
								't2.member_id'
								));
		$this->db->from("coop_loan_atm_prev_deduct t1");
		$this->db->join("coop_loan t2",'t1.ref_id = t2.id','inner');
		$this->db->where("t1.loan_atm_id = '".@$_POST['loan_atm_id']."' AND t1.data_type = 'loan'");
		$row = $this->db->get()->result_array();
		$sum_count = 0;

		foreach($row as $key => $value){				
			//check หนี้ห้อย
			$extra_debt_amount	= 0;//หนี้ห้อย
			if(date("Y-m", strtotime($value['createdatetime']) ) != date("Y-m") ){
				$month = date("m");
				$year = (date("Y")+543);
				
				$this->db->select('profile_id');
				$this->db->from('coop_finance_month_profile');
				$this->db->where("profile_month = '".(int)$month."' AND profile_year = '".$year."' ");
				$profile_id = $this->db->get()->result_array()[0]['profile_id'];
				//echo $this->db->last_query(); echo '<hr>';
				$this->db->select("sum(pay_amount) as sum_of_pay_amount");
				$finance_month_detail = $this->db->get_where("coop_finance_month_detail", array(
					"profile_id" => $profile_id,
					"member_id" => $value['member_id'],
					"loan_id" => $value['ref_id'],
					"pay_type" => "principal",
					"run_status" => 0
				))->result_array()[0];
				//echo $this->db->last_query(); echo '<hr>';
				if($finance_month_detail){
					$extra_debt['total_princical'] += $finance_month_detail['sum_of_pay_amount'];
					$extra_debt_amount	= $finance_month_detail['sum_of_pay_amount'];
				}
					
			}
			//check หนี้ห้อย
			
			$contract_number = @$value['contract_number'];
			$pay_type = @$value['pay_type'];
			if($value['pay_type'] == 'principal'){					
				$loan_id = @$value['id'];
				$loan_amount_balance = @$value['principal_amount'];
				$interest_amount = 0;
			}else{
				$loan_id = @$value['id'];
				$loan_amount_balance = @$value['principal_amount'];
				$interest_amount = @$value['interest_amount'];
			}
			
			$data_insert = array();
			$data_insert['loan_atm_id'] = @$_POST['loan_atm_id'];
			$data_insert['member_id'] = @$_POST['member_id'];
			$data_insert['loan_amount'] = ($loan_amount_balance+$interest_amount);
			$data_insert['loan_amount_balance'] = ($loan_amount_balance+$interest_amount);
			$data_insert['loan_date'] = date('Y-m-d H:i:s');
			$this->db->select(array('petition_number'));
			$this->db->from('coop_loan_atm_detail');
			$this->db->order_by('petition_number DESC');
			$this->db->limit(1);
			$row_petition_number = $this->db->get()->result_array();
			if(!empty($row_petition_number)){
				$petition_number = (int)$row_petition_number[0]['petition_number']+1;
				$petition_number = sprintf('%06d',$petition_number);
			}else{
				$petition_number = sprintf('%06d',1);
			}
			$data_insert['petition_number'] = $petition_number;
			$data_insert['loan_status'] = '0';
			$data_insert['loan_description'] = 'หักกลบสัญญาเก่า '.$contract_number;
			$data_insert['date_start_period'] = date('Y-m-t',strtotime('+1 month'));
			$data_insert['transaction_at'] = '0';
			$data_insert['transfer_status'] = '1';
			$this->db->insert('coop_loan_atm_detail', $data_insert);
			
			$loan_amount_balance_all += ($loan_amount_balance+$interest_amount);
			$atm_transaction = array();
			$atm_transaction['loan_atm_id'] = $_POST['loan_atm_id'];
			$atm_transaction['receipt_id'] = $receipt_id;
			$atm_transaction['loan_amount_balance'] = $loan_amount_balance_all;
			$atm_transaction['transaction_datetime'] = date('Y-m-d H:i:s');
			$this->loan_libraries->atm_transaction($atm_transaction);
			
			$receipt_arr[$r]['receipt_id'] = $receipt_id;
			$receipt_arr[$r]['member_id'] = @$_POST['member_id'];
			$receipt_arr[$r]['loan_id'] = $loan_id;
			$receipt_arr[$r]['account_list_id'] = '15';
			$receipt_arr[$r]['principal_payment'] = $loan_amount_balance;
			$receipt_arr[$r]['interest'] = $interest_amount;
			$receipt_arr[$r]['total_amount'] = $loan_amount_balance+$interest_amount;
			$receipt_arr[$r]['payment_date'] = date('Y-m-d');
			$receipt_arr[$r]['createdatetime'] = date('Y-m-d H:i:s');
			$receipt_arr[$r]['loan_amount_balance'] = @$extra_debt_amount;
			$receipt_arr[$r]['transaction_text'] = 'หักกลบเงินกู้เลขที่สัญญา '.$contract_number;
			$receipt_arr[$r]['deduct_type'] = @$pay_type;
			$r++;
			
			$data_insert = array();
			
			if(@$extra_debt_amount>=1){
				$data_insert['loan_status'] = '1';
				$data_insert['loan_amount_balance'] = $extra_debt_amount;//คงค้างหนี้ห้อยไว้ รอการผ่านรายการ
			}else{
				$data_insert['loan_status'] = '4';
				$data_insert['loan_amount_balance'] = '0';
			}
			$this->db->where('id', $loan_id);
			$this->db->update('coop_loan', $data_insert);
			
			$loan_transaction = array();
			$loan_transaction['loan_id'] = $loan_id;
			$loan_transaction['receipt_id'] = $receipt_id;
			if(@$extra_debt_amount>=1){
				$loan_transaction['loan_amount_balance'] = @$extra_debt_amount;
			}else{
				$loan_transaction['loan_amount_balance'] = '0';
			}				
			$loan_transaction['transaction_datetime'] = date('Y-m-d H:i:s');
			$this->loan_libraries->loan_transaction($loan_transaction);
			
			$sum_count += @$loan_amount_balance+@$interest_amount;
		}
		//echo '<pre>'; print_r($receipt_arr); echo '</pre>';
		//exit;
		
		
			$this->db->select(array('t1.*',
									't2.contract_number',
									't2.total_amount_approve',
									't2.total_amount_balance',
									't2.loan_atm_id'
									));
			$this->db->from("coop_loan_atm_prev_deduct t1");
			$this->db->join("coop_loan_atm t2",'t1.ref_id = t2.loan_atm_id','inner');
			$this->db->where("t1.loan_atm_id = '".@$_POST['loan_atm_id']."' AND t1.data_type = 'atm'");
			$row = $this->db->get()->result_array();
			//echo '<pre>'; print_r($row); echo '</pre>';
			
			foreach($row as $key => $value){				
				$contract_number = @$value['contract_number'];
				//echo $contract_number.'<hr>';
				$total_amount_approve = @$value['total_amount_approve'];
				$pay_type = @$value['pay_type'];
				if($value['pay_type'] == 'principal'){					
					$loan_id = @$value['loan_atm_id'];
					$loan_amount_balance = @$value['principal_amount'];
					$interest_amount = 0;
				}else{
					$loan_id = @$value['loan_atm_id'];
					$loan_amount_balance = @$value['principal_amount'];
					$interest_amount = @$value['interest_amount'];
				}
				
				$data_insert = array();
				$data_insert['loan_atm_id'] = @$_POST['loan_atm_id'];
				$data_insert['member_id'] = @$_POST['member_id'];
				$data_insert['loan_amount'] = ($loan_amount_balance +$interest_amount);
				$data_insert['loan_amount_balance'] = ($loan_amount_balance +$interest_amount);
				$data_insert['loan_date'] = date('Y-m-d H:i:s');
				$this->db->select(array('petition_number'));
				$this->db->from('coop_loan_atm_detail');
				$this->db->order_by('petition_number DESC');
				$this->db->limit(1);
				$row_petition_number = $this->db->get()->result_array();
				if(!empty($row_petition_number)){
					$petition_number = (int)$row_petition_number[0]['petition_number']+1;
					$petition_number = sprintf('%06d',$petition_number);
				}else{
					$petition_number = sprintf('%06d',1);
				}
				$data_insert['petition_number'] = $petition_number;
				$data_insert['loan_status'] = '0';
				$data_insert['loan_description'] = 'หักกลบสัญญาเก่า '.$contract_number;
				$data_insert['date_start_period'] = date('Y-m-t',strtotime('+1 month'));
				$data_insert['transaction_at'] = '0';
				$data_insert['transfer_status'] = '1';
				$this->db->insert('coop_loan_atm_detail', $data_insert);
				
				$loan_amount_balance_all += ($loan_amount_balance+$interest_amount);
				$atm_transaction = array();
				$atm_transaction['loan_atm_id'] = $_POST['loan_atm_id'];
				$atm_transaction['loan_amount_balance'] = $loan_amount_balance_all;
				$atm_transaction['transaction_datetime'] = date('Y-m-d H:i:s');
				$this->loan_libraries->atm_transaction($atm_transaction);
				
				$receipt_arr[$r]['receipt_id'] = $receipt_id;
				$receipt_arr[$r]['member_id'] = @$_POST['member_id'];
				$receipt_arr[$r]['loan_atm_id'] = $loan_id;
				$receipt_arr[$r]['account_list_id'] = '31';
				$receipt_arr[$r]['principal_payment'] = $loan_amount_balance;
				$receipt_arr[$r]['interest'] = $interest_amount;
				$receipt_arr[$r]['total_amount'] = ($loan_amount_balance+$interest_amount);
				$receipt_arr[$r]['payment_date'] = date('Y-m-d');
				$receipt_arr[$r]['createdatetime'] = date('Y-m-d H:i:s');
				$receipt_arr[$r]['loan_amount_balance'] = '0';
				$receipt_arr[$r]['transaction_text'] = 'หักกลบเงินกู้เลขที่สัญญา '.$contract_number;
				$receipt_arr[$r]['deduct_type'] = $pay_type;
				$r++;
				
				$data_insert = array();
				$data_insert['total_amount_balance'] = $total_amount_approve;
				$data_insert['loan_atm_status'] = '4';
				$this->db->where('loan_atm_id', $loan_id);
				$this->db->update('coop_loan_atm', $data_insert);
					
				$data_insert = array();
				$data_insert['loan_amount_balance'] = '0';
				$data_insert['loan_status'] = '1';
				$this->db->where('loan_atm_id', $loan_id);
				$this->db->update('coop_loan_atm_detail', $data_insert);
				
				$atm_transaction = array();
				$atm_transaction['loan_atm_id'] = $loan_id;
				$atm_transaction['receipt_id'] = $receipt_id;
				$atm_transaction['loan_amount_balance'] = '0';
				$atm_transaction['transaction_datetime'] = date('Y-m-d H:i:s');
				$this->loan_libraries->atm_transaction($atm_transaction);
				
				$sum_count += @$loan_amount_balance+@$interest_amount;
			}
			
			
			if($sum_count>0){
				$data_insert = array();
				$data_insert['receipt_id'] = $receipt_id;
				$data_insert['member_id'] = @$_POST['member_id'];
				$data_insert['admin_id'] = @$_SESSION['USER_ID'];
				$data_insert['sumcount'] = $sum_count;
				$data_insert['receipt_datetime'] = date('Y-m-d H:i:s');
				if($this->db->insert('coop_receipt', $data_insert)){
					foreach($receipt_arr as $key => $value){
						$data_insert = array();
						$data_insert['receipt_id'] = $value['receipt_id'];
						$data_insert['receipt_list'] = $value['account_list_id'];
						$data_insert['receipt_count'] = $value['total_amount'];
						$this->db->insert('coop_receipt_detail', $data_insert);
						
						//บันทึกการชำระเงิน
						$data_insert = array();
						$data_insert['receipt_id'] = $value['receipt_id'];
						$data_insert['member_id'] = @$value['member_id'];
						$data_insert['loan_id'] = @$value['loan_id'];
						$data_insert['loan_atm_id'] = @$value['loan_atm_id'];
						$data_insert['account_list_id'] = $value['account_list_id'];
						$data_insert['principal_payment'] = @$value['principal_payment'];
						$data_insert['interest'] = @$value['interest'];
						$data_insert['total_amount'] = @$value['total_amount'];
						$data_insert['payment_date'] = @$value['payment_date'];
						$data_insert['loan_amount_balance'] = @$value['loan_amount_balance'];
						$data_insert['createdatetime'] = @$value['createdatetime'];
						$data_insert['transaction_text'] = @$value['transaction_text'];
						$data_insert['deduct_type'] = @$value['deduct_type'];
						$this->db->insert('coop_finance_transaction', $data_insert);
						
						if(@$value['loan_id'] !='' && @$value['interest'] > 0){
							$data_insert = array();
							$data_insert['date_last_interest'] = date('Y-m-d H:i:s');
							$this->db->where('id',@$value['loan_id']);
							$this->db->update('coop_loan',$data_insert);
						}
						if(@$value['loan_atm_id'] !='' && @$value['interest'] > 0){
							$data_insert = array();
							$data_insert['date_last_interest'] = date('Y-m-d H:i:s');
							$this->db->where('loan_atm_id',@$value['loan_atm_id']);
							$this->db->update('coop_loan_atm',$data_insert);
						}
					}
					
					$data_insert = array();
					$data_insert['deduct_receipt_id'] = $receipt_id;
					$data_insert['total_amount_balance'] = $row_atm['total_amount_approve'] - $sum_count;
					$this->db->where('loan_atm_id',$_POST['loan_atm_id']);
					$this->db->update('coop_loan_atm',$data_insert);				
				}
			}

/////////////////////////////////////////////////////////// หักกลบ ////////////////////////////////////////////////////////////////
		$this->center_function->toast("บันทึกข้อมูลเรียบร้อยแล้ว");
		echo "<script> document.location.href='".base_url(PROJECTPATH.'/loan_atm/loan_atm_approve')."' </script>";
		exit;
	}
	
	function loan_approve_save_new(){
		//echo"<pre>";print_r($_POST);exit;
		//ปีในการ gen เลขสัญญา		
		$rs_month_account = $this->db->select('accm_month_ini')
		>from("coop_account_period_setting")
		->limit(1)
		->get()->result_array();
		$month_account = $rs_month_account[0]['accm_month_ini'];
		$month_now = date('m');
		if((int)$month_now >= (int)$month_account){
			$year = (date('Y')+543)+1;
		}else{
			$year = (date('Y')+543);		
		}
		$year_short = substr($year,2,2);
		$new_contact_number = '';
		$this->db->select('*');
		$this->db->from("coop_loan_atm_setting");
		$this->db->limit(1);
		$rs_term_of_loan = $this->db->get()->result_array();
		$row_term_of_loan = @$rs_term_of_loan[0];
		$new_contact_number = $row_term_of_loan['prefix_code'].$year_short;
		
		$contact_number_now = $this->loan_libraries->get_contract_number($year);
		
		$new_contact_number .= sprintf("% 05d",$contact_number_now);
		//$new_contact_number = $new_contact_number."/".(date('Y')+543);
		
		$this->db->select('loan_atm_account_id');
		$this->db->from("coop_mem_apply");
		$this->db->where("member_id = '".$_POST['member_id']."'");
		$row_loan_atm_account_id = $this->db->get()->result_array();
		$account_id = @$row_loan_atm_account_id[0]['loan_atm_account_id'];
		
		if(@$account_id==''){
			$account_id = '002'.sprintf('%08d',$_POST['member_id']);
			$data_insert = array();
			$data_insert['loan_atm_account_id'] = $account_id;
			$this->db->where('member_id',$_POST['member_id']);
			$this->db->update('coop_mem_apply',$data_insert);
		}
		
		$data_insert = array();
		$data_insert['contract_number'] = @$new_contact_number;
		$data_insert['loan_atm_status'] = '1';
		$data_insert['total_amount_approve'] = str_replace(',','',$_POST['total_amount_approve']);
		$data_insert['total_amount_balance'] = str_replace(',','',$_POST['total_amount_approve']);
		$data_insert['prev_loan'] = str_replace(',','',$_POST['prev_loan']);
		$data_insert['approve_date'] = date('Y-m-d H:i:s');
		$data_insert['atm_card_number'] = @$_POST['atm_card_number'];
		$data_insert['account_id'] = @$account_id;
		$data_insert['activate_status'] = '0';
		$this->db->where('loan_atm_id', @$_POST['loan_atm_id']);
		$this->db->update('coop_loan_atm', $data_insert);
		
		$this->db->select(array('*'));
		$this->db->from('coop_loan_atm');
		$this->db->where("loan_atm_id = '".@$_POST['loan_atm_id']."'");
		$row = $this->db->get()->result_array();
		$row_atm = @$row[0];
		
/////////////////////////////////////////////////////////// หักกลบ ////////////////////////////////////////////////////////////////
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
		
		$loan_amount_balance = 0;
		$loan_amount_balance_all = 0;
		$receipt_arr = array();
		$r=0;		
		$date_interesting = date('Y-m-d');		
		//////////////////	
		$this->db->select(array('t1.*',
								't2.contract_number',
								't2.loan_amount_balance',
								't2.id'
								));
		$this->db->from("coop_loan_atm_prev_deduct t1");
		$this->db->join("coop_loan t2",'t1.ref_id = t2.id','inner');
		$this->db->where("t1.loan_atm_id = '".@$_POST['loan_atm_id']."'");
		$row = $this->db->get()->result_array();

		//print_r($this->db->last_query());
		//exit;
		foreach($row as $key => $value){			
			$contract_number = @$value['contract_number'];
			$pay_type = @$value['pay_type'];
			if($value['pay_type'] == 'principal'){					
				$loan_id = @$value['id'];
				$loan_amount_balance = @$value['pay_amount'];
				$interest_amount = 0;
			}else{
				$loan_id = @$value['id'];
				$loan_amount_balance = @$value['loan_amount_balance'];
				
				$cal_loan_interest = array();
				$cal_loan_interest['loan_id'] = @$value['ref_id'];
				$cal_loan_interest['date_interesting'] = $this->center_function->ConvertToSQLDate(@$date_interesting);
				$interest_amount = $this->loan_libraries->cal_loan_interest($cal_loan_interest);
			}
			
			$data_insert = array();
			$data_insert['loan_atm_id'] = @$_POST['loan_atm_id'];
			$data_insert['member_id'] = @$_POST['member_id'];
			$data_insert['loan_amount'] = ($loan_amount_balance+$interest_amount);
			$data_insert['loan_amount_balance'] = ($loan_amount_balance+$interest_amount);
			$data_insert['loan_date'] = date('Y-m-d H:i:s');
			$this->db->select(array('petition_number'));
			$this->db->from('coop_loan_atm_detail');
			$this->db->order_by('petition_number DESC');
			$this->db->limit(1);
			$row_petition_number = $this->db->get()->result_array();
			if(!empty($row_petition_number)){
				$petition_number = (int)$row_petition_number[0]['petition_number']+1;
				$petition_number = sprintf('%06d',$petition_number);
			}else{
				$petition_number = sprintf('%06d',1);
			}
			$data_insert['petition_number'] = $petition_number;
			$data_insert['loan_status'] = '0';
			$data_insert['loan_description'] = 'หักกลบสัญญาเก่า '.$contract_number;
			$data_insert['date_start_period'] = date('Y-m-t',strtotime('+1 month'));
			$data_insert['transaction_at'] = '0';
			$data_insert['transfer_status'] = '1';
			$this->db->insert('coop_loan_atm_detail', $data_insert);
			
			$loan_amount_balance_all += ($loan_amount_balance+$interest_amount);
			$atm_transaction = array();
			$atm_transaction['loan_atm_id'] = $_POST['loan_atm_id'];
			$atm_transaction['loan_amount_balance'] = $loan_amount_balance_all;
			$atm_transaction['transaction_datetime'] = date('Y-m-d H:i:s');
			$this->loan_libraries->atm_transaction($atm_transaction);
			
			$receipt_arr[$r]['receipt_id'] = $receipt_id;
			$receipt_arr[$r]['member_id'] = @$_POST['member_id'];
			$receipt_arr[$r]['loan_id'] = $loan_id;
			$receipt_arr[$r]['account_list_id'] = '15';
			$receipt_arr[$r]['principal_payment'] = $loan_amount_balance;
			$receipt_arr[$r]['interest'] = $interest_amount;
			$receipt_arr[$r]['total_amount'] = $loan_amount_balance+$interest_amount;
			$receipt_arr[$r]['payment_date'] = date('Y-m-d');
			$receipt_arr[$r]['createdatetime'] = date('Y-m-d H:i:s');
			$receipt_arr[$r]['loan_amount_balance'] = '0';
			$receipt_arr[$r]['transaction_text'] = 'หักกลบเงินกู้เลขที่สัญญา '.$contract_number;
			$receipt_arr[$r]['deduct_type'] = @$pay_type;
			$r++;
			
			$data_insert = array();
			$data_insert['loan_amount_balance'] = '0';
			$data_insert['loan_status'] = '4';
			$this->db->where('id', $loan_id);
			$this->db->update('coop_loan', $data_insert);
			
			$loan_transaction = array();
			$loan_transaction['loan_id'] = $loan_id;
			$loan_transaction['loan_amount_balance'] = '0';
			$loan_transaction['transaction_datetime'] = date('Y-m-d H:i:s');
			$this->loan_libraries->loan_transaction($loan_transaction);
		}
		
		
			$this->db->select(array('t1.*',
									't2.contract_number',
									't2.total_amount_approve',
									't2.total_amount_balance',
									't2.loan_atm_id'
									));
			$this->db->from("coop_loan_atm_prev_deduct t1");
			$this->db->join("coop_loan_atm t2",'t1.ref_id = t2.loan_atm_id','inner');
			$this->db->where("t1.loan_atm_id = '".@$_POST['loan_atm_id']."'");
			$row = $this->db->get()->result_array();
			//echo '<pre>'; print_r($row); echo '</pre>';

			foreach($row as $key => $value){				
				$contract_number = @$value['contract_number'];
				//echo $contract_number.'<hr>';
				$total_amount_approve = @$value['total_amount_approve'];
				$pay_type = @$value['pay_type'];
				if($value['pay_type'] == 'principal'){					
					$loan_id = @$value['loan_atm_id'];
					$loan_amount_balance = @$value['total_amount_approve'] - @$value['total_amount_balance'];
					$interest_amount = 0;
				}else{
					$loan_id = @$value['loan_atm_id'];
					$loan_amount_balance = @$value['loan_amount_balance'];
					$cal_loan_interest = array();
					$cal_loan_interest['loan_id'] = @$value['ref_id'];
					$cal_loan_interest['date_interesting'] = $this->center_function->ConvertToSQLDate(@$date_interesting);
					$interest_amount = $this->loan_libraries->cal_atm_interest($cal_loan_interest);
				}
				
				$data_insert = array();
				$data_insert['loan_atm_id'] = @$_POST['loan_atm_id'];
				$data_insert['member_id'] = @$_POST['member_id'];
				$data_insert['loan_amount'] = ($loan_amount_balance +$interest_amount);
				$data_insert['loan_amount_balance'] = ($loan_amount_balance +$interest_amount);
				$data_insert['loan_date'] = date('Y-m-d H:i:s');
				$this->db->select(array('petition_number'));
				$this->db->from('coop_loan_atm_detail');
				$this->db->order_by('petition_number DESC');
				$this->db->limit(1);
				$row_petition_number = $this->db->get()->result_array();
				if(!empty($row_petition_number)){
					$petition_number = (int)$row_petition_number[0]['petition_number']+1;
					$petition_number = sprintf('%06d',$petition_number);
				}else{
					$petition_number = sprintf('%06d',1);
				}
				$data_insert['petition_number'] = $petition_number;
				$data_insert['loan_status'] = '0';
				$data_insert['loan_description'] = 'หักกลบสัญญาเก่า '.$contract_number;
				$data_insert['date_start_period'] = date('Y-m-t',strtotime('+1 month'));
				$data_insert['transaction_at'] = '0';
				$data_insert['transfer_status'] = '1';
				$this->db->insert('coop_loan_atm_detail', $data_insert);
				
				$loan_amount_balance_all += ($loan_amount_balance+$interest_amount);
				$atm_transaction = array();
				$atm_transaction['loan_atm_id'] = $_POST['loan_atm_id'];
				$atm_transaction['loan_amount_balance'] = $loan_amount_balance_all;
				$atm_transaction['transaction_datetime'] = date('Y-m-d H:i:s');
				$this->loan_libraries->atm_transaction($atm_transaction);
				
				$receipt_arr[$r]['receipt_id'] = $receipt_id;
				$receipt_arr[$r]['member_id'] = @$_POST['member_id'];
				$receipt_arr[$r]['loan_atm_id'] = $loan_id;
				$receipt_arr[$r]['account_list_id'] = '31';
				$receipt_arr[$r]['principal_payment'] = $loan_amount_balance;
				$receipt_arr[$r]['interest'] = $interest_amount;
				$receipt_arr[$r]['total_amount'] = ($loan_amount_balance+$interest_amount);
				$receipt_arr[$r]['payment_date'] = date('Y-m-d');
				$receipt_arr[$r]['createdatetime'] = date('Y-m-d H:i:s');
				$receipt_arr[$r]['loan_amount_balance'] = '0';
				$receipt_arr[$r]['transaction_text'] = 'หักกลบเงินกู้เลขที่สัญญา '.$contract_number;
				$receipt_arr[$r]['deduct_type'] = $pay_type;
				$r++;
				
				$data_insert = array();
				$data_insert['total_amount_balance'] = $total_amount_approve;
				$data_insert['loan_atm_status'] = '3';
				$this->db->where('loan_atm_id', $loan_id);
				$this->db->update('coop_loan_atm', $data_insert);
					
				$data_insert = array();
				$data_insert['loan_amount_balance'] = '0';
				$data_insert['loan_status'] = '1';
				$this->db->where('loan_atm_id', $loan_id);
				$this->db->update('coop_loan_atm_detail', $data_insert);
				
				$atm_transaction = array();
				$atm_transaction['loan_atm_id'] = $loan_id;
				$atm_transaction['loan_amount_balance'] = '0';
				$atm_transaction['transaction_datetime'] = date('Y-m-d H:i:s');
				$this->loan_libraries->atm_transaction($atm_transaction);
			}
			
			$sum_count = 0;
			foreach($receipt_arr as $key => $value){
				$data_insert = array();
				$data_insert['receipt_id'] = $value['receipt_id'];
				$data_insert['receipt_list'] = $value['account_list_id'];
				$data_insert['receipt_count'] = $value['total_amount'];
				$this->db->insert('coop_receipt_detail', $data_insert);
				
				//บันทึกการชำระเงิน
				$data_insert = array();
				$data_insert['receipt_id'] = $value['receipt_id'];
				$data_insert['member_id'] = @$value['member_id'];
				$data_insert['loan_id'] = @$value['loan_id'];
				$data_insert['loan_atm_id'] = @$value['loan_atm_id'];
				$data_insert['account_list_id'] = $value['account_list_id'];
				$data_insert['principal_payment'] = @$value['principal_payment'];
				$data_insert['interest'] = @$value['interest'];
				$data_insert['total_amount'] = @$value['total_amount'];
				$data_insert['payment_date'] = @$value['payment_date'];
				$data_insert['loan_amount_balance'] = @$value['loan_amount_balance'];
				$data_insert['createdatetime'] = @$value['createdatetime'];
				$data_insert['transaction_text'] = @$value['transaction_text'];
				$data_insert['deduct_type'] = @$value['deduct_type'];
				$this->db->insert('coop_finance_transaction', $data_insert);
				$sum_count += @$value['total_amount'];
				
				if(@$value['loan_id'] !='' && @$value['interest'] > 0){
					$data_insert = array();
					$data_insert['date_last_interest'] = date('Y-m-d H:i:s');
					$this->db->where('id',@$value['loan_id']);
					$this->db->update('coop_loan',$data_insert);
				}
				if(@$value['loan_atm_id'] !='' && @$value['interest'] > 0){
					$data_insert = array();
					$data_insert['date_last_interest'] = date('Y-m-d H:i:s');
					$this->db->where('loan_atm_id',@$value['loan_atm_id']);
					$this->db->update('coop_loan_atm',$data_insert);
				}
			}
			if($sum_count>0){
				$data_insert = array();
				$data_insert['receipt_id'] = $receipt_id;
				$data_insert['member_id'] = @$member_id;
				$data_insert['admin_id'] = @$_SESSION['USER_ID'];
				$data_insert['sumcount'] = $sum_count;
				$data_insert['receipt_datetime'] = date('Y-m-d H:i:s');
				$this->db->insert('coop_receipt', $data_insert);
				
				$data_insert = array();
				$data_insert['deduct_receipt_id'] = $receipt_id;
				$data_insert['total_amount_balance'] = $row_atm['total_amount_approve'] - $sum_count;
				$this->db->where('loan_atm_id',$_POST['loan_atm_id']);
				$this->db->update('coop_loan_atm',$data_insert);
			}
/////////////////////////////////////////////////////////// หักกลบ ////////////////////////////////////////////////////////////////
		$this->center_function->toast("บันทึกข้อมูลเรียบร้อยแล้ว");
		echo "<script> document.location.href='".base_url(PROJECTPATH.'/loan_atm/loan_atm_approve')."' </script>";
		exit;
	}
	
	function show_loan_atm_detail($loan_atm_id=''){
		$arr_data = array();
		
		$this->db->select(array('t1.*','t2.firstname_th','t2.lastname_th','t3.prename_short'));
		$this->db->from('coop_loan_atm as t1');
		$this->db->join("coop_mem_apply as t2",'t1.member_id = t2.member_id','inner');
		$this->db->join("coop_prename as t3",'t2.prename_id = t3.prename_id','left');
		$this->db->where("loan_atm_id = '".$loan_atm_id."'");
		$row = $this->db->get()->result_array();
		$row_loan_atm = @$row[0];
		$arr_data['row_loan_atm'] = $row_loan_atm;
		
		$this->db->select(array('t1.*','t2.pay_type'));
		$this->db->from('coop_loan_atm_detail as t1');
		$this->db->join("coop_loan_atm_transfer as t2",'t1.loan_id = t2.loan_id','left');
		$this->db->where("loan_atm_id = '".$loan_atm_id."'");
		$this->db->order_by("loan_date ASC");
		$row = $this->db->get()->result_array();
		$arr_data['row_loan_atm_detail'] = $row;
		
		$arr_data['transaction_at'] = $this->transaction_at;
		$arr_data['pay_type'] = $this->pay_type;
		
		$this->preview_libraries->template_preview('loan_atm/show_loan_atm_detail',$arr_data);
	}
	
	function loan_atm_transfer(){
		$arr_data = array();
			$this->db->select(array(
				't1.loan_id', 
				't1.loan_atm_id', 
				't1.loan_date',
				't1.loan_amount',
				't1.transfer_status',
				't2.contract_number',
				't2.approve_date',
				't3.member_id',
				't3.firstname_th',
				't3.lastname_th',
				't4.id as transfer_id',
				't4.file_name',
				't4.date_transfer',
				't4.pay_type'
			));
			$this->db->from('coop_loan_atm_detail as t1');
			$this->db->join('coop_loan_atm as t2','t2.loan_atm_id = t1.loan_atm_id','inner');
			$this->db->join('coop_mem_apply as t3','t2.member_id = t3.member_id','inner');
			$this->db->join("coop_loan_atm_transfer as t4","t1.loan_id = t4.loan_id",'left');
			$this->db->join('coop_maco_account as t5','t4.account_id = t5.account_id','left');
			$this->db->join('coop_user as t6','t6.user_id = t4.admin_id','left');
			$this->db->where("t1.loan_id = '".@$_GET['loan_id']."'");
			$this->db->order_by("t1.loan_id DESC");
			$this->db->limit(1);
			$row = $this->db->get()->result_array();
			//echo $this->db->last_query();exit;
			$row = @$row[0];
			$arr_data['row'] = $row;
			//echo"<pre>";print_r($arr_data['row']);echo"</pre>";exit;
			if(@$row['member_id']!=''){
				$this->db->select(array('*'));
				$this->db->from('coop_maco_account');
				$this->db->where("mem_id = '".$row['member_id']."' AND account_status = '0'");
				$rs_account = $this->db->get()->result_array();
				$arr_data['rs_account'] = @$rs_account;
			}
		
		$arr_data['pay_type'] = $this->pay_type;
		
		$this->db->select(array('t1.loan_id','t1.loan_amount','t2.contract_number'));
		$this->db->from('coop_loan_atm_detail as t1');
		$this->db->join('coop_loan_atm as t2','t2.loan_atm_id = t1.loan_atm_id','inner');
		$this->db->where("t1.transfer_status = '0'");
		$row = $this->db->get()->result_array();
		$arr_data['transfer_list'] = $row;
		
		$this->libraries->template('loan_atm/loan_atm_transfer',$arr_data);
	}
	function get_loan_atm_detail_data(){
		$this->db->select(array(
			't1.loan_id', 
			't1.loan_atm_id', 
			't1.loan_date',
			't1.loan_amount',
			't1.transfer_status',
			't2.contract_number',
			't2.approve_date',
			't3.member_id',
			't3.firstname_th',
			't3.lastname_th',
			't4.id as transfer_id',
			't4.file_name',
			't4.date_transfer',
			't4.pay_type'
		));
		$this->db->from('coop_loan_atm_detail as t1');
		$this->db->join('coop_loan_atm as t2','t2.loan_atm_id = t1.loan_atm_id','inner');
		$this->db->join('coop_mem_apply as t3','t2.member_id = t3.member_id','inner');
		$this->db->join("coop_loan_atm_transfer as t4","t1.loan_id = t4.loan_id",'left');
		$this->db->join('coop_maco_account as t5','t4.account_id = t5.account_id','left');
		$this->db->join('coop_user as t6','t6.user_id = t4.admin_id','left');
		$this->db->where("t1.loan_id = '".@$_POST['loan_id']."' AND t1.transfer_status = '0'");
		$this->db->order_by("t1.loan_id DESC");
		$this->db->limit(1);
		$row = $this->db->get()->result_array();
		//echo $this->db->last_query();
		if(!empty($row)){
			$data = array();
			$data['result'] = 'success';
			$data['coop_loan_atm'] = $row[0];
			$data['coop_loan_atm']['loan_amount'] = number_format($data['coop_loan_atm']['loan_amount']);
			$data['coop_loan_atm']['loan_date'] = $this->center_function->mydate2date($data['coop_loan_atm']['loan_date'],true);
		}else{
			$data['result'] = 'not_found';
		}
		echo json_encode($data);	  
		
		exit;
	}
	
	function loan_atm_transfer_save(){
		//echo"<pre>";print_r($_POST);echo"</pre>";exit;
		
		$this->db->select(array(
			'loan_amount'
		));
		$this->db->from('coop_loan_atm_detail');
		$this->db->where("loan_id = '".$_POST['loan_id']."'");
		$row_loan = $this->db->get()->result_array();
		$row_loan = $row_loan[0];
		
		$date_arr = explode('/',$_POST['date_transfer']);
		$date_transfer = ($date_arr[2]-543)."-".$date_arr[1]."-".$date_arr[0]." ".$_POST['time_transfer'];
		if($_POST['account_id']!=''){
			$this->db->select(array(
				'transaction_balance'
			));
			$this->db->from('coop_account_transaction');
			$this->db->where("account_id = '".$_POST['account_id']."'");
			$this->db->order_by('transaction_id DESC');
			$this->db->limit(1);
			$row_prev_trans = $this->db->get()->result_array();
			$row_prev_trans = $row_prev_trans[0];
			
			$transaction_balance = $row_prev_trans['transaction_balance'] + $row_loan['loan_amount'];
			
			$data_insert = array();
			$data_insert['transaction_time'] = $date_transfer;
			$data_insert['transaction_list'] = 'XD';
			$data_insert['transaction_withdrawal'] = '0';
			$data_insert['transaction_deposit'] = $row_loan['loan_amount'];
			$data_insert['transaction_balance'] = $transaction_balance;
			$data_insert['user_id'] = $_SESSION['USER_ID'];
			$data_insert['account_id'] = $_POST['account_id'];
			$this->db->insert('coop_account_transaction', $data_insert);
			$pay_type = '1';
		}else{
			$pay_type = '0';
		}
		
		$data_insert = array();
		$data_insert['loan_id'] = $_POST['loan_id'];
		$data_insert['account_id'] = $_POST['account_id'];
		$data_insert['date_transfer'] = $date_transfer;
		$data_insert['createdatetime'] = date('Y-m-d H:i:s');
		$data_insert['admin_id'] = $_SESSION['USER_ID'];
		$data_insert['transfer_status'] = '0';
		$data_insert['pay_type'] = $pay_type;
		$this->db->insert('coop_loan_atm_transfer', $data_insert);
		
		$last_id = $this->db->insert_id();
		
		$output_dir = $_SERVER["DOCUMENT_ROOT"].PROJECTPATH."/assets/uploads/loan_atm_transfer_attach/";
		
		if($_FILES['file_attach']['name']!=''){
			$new_file_name = $this->center_function->create_file_name($output_dir,$_FILES['file_attach']['name']);
			@copy($_FILES["file_attach"]["tmp_name"],$output_dir.$new_file_name);
			
			$data_insert = array();
			$data_insert['file_name'] = $new_file_name;
			$this->db->where('id', $last_id);
			$this->db->update('coop_loan_atm_transfer', $data_insert);
		}
		
		$data_insert = array();
		$data_insert['transfer_status'] = '1';
		$this->db->where('loan_id', $_POST['loan_id']);
		$this->db->update('coop_loan_atm_detail', $data_insert);
		
		/*$this->db->select(array(
			't1.account_chart_id',
			't2.account_chart'
		));
		$this->db->from('coop_account_match as t1');
		$this->db->join('coop_account_chart as t2','t1.account_chart_id = t2.account_chart_id','left');
		$this->db->where("
			t1.match_type = 'loan'
			AND t1.match_id = '".$row_loan['loan_type']."'
		");
		$row_account_match = $this->db->get()->result_array();
		$row_account_match = @$row_account_match[0];
		
		$data = array();
		$data['coop_account']['account_description'] = "โอนเงินให้".$row_account_match['account_chart'];
		$data['coop_account']['account_datetime'] = $date_transfer;
		
		$i=0;
		$data['coop_account_detail'][$i]['account_type'] = 'debit';
		$data['coop_account_detail'][$i]['account_amount'] = $row_loan['loan_amount'];
		$data['coop_account_detail'][$i]['account_chart_id'] = $row_account_match['account_chart_id'];
		$i++;
		$data['coop_account_detail'][$i]['account_type'] = 'credit';
		$data['coop_account_detail'][$i]['account_amount'] = $row_loan['loan_amount'];
		$data['coop_account_detail'][$i]['account_chart_id'] = '10100';
		$this->account_transaction->account_process($data);*/
	
		$this->center_function->toast("บันทึกข้อมูลเรียบร้อยแล้ว");
		echo "<script> document.location.href='".base_url(PROJECTPATH.'/loan_atm/loan_atm_transfer?loan_id='.$_POST['loan_id'])."' </script>";
		exit;
	}
	
	public function loan_atm_all()
	{
		$arr_data = array();

		$x=0;
		$join_arr = array();
		/*$join_arr[$x]['table'] = 'coop_mem_apply';
		$join_arr[$x]['condition'] = 'coop_mem_apply.member_id = coop_loan_atm.member_id';
		$join_arr[$x]['type'] = 'left';		
		$x++;
		
		$join_arr[$x]['table'] = 'coop_prename';
		$join_arr[$x]['condition'] = 'coop_prename.prename_id = coop_mem_apply.prename_id';
		$join_arr[$x]['type'] = 'left';
		*/
		
		$this->paginater_all->type(DB_TYPE);
		//$this->paginater_all->select('coop_loan_atm.*,coop_mem_apply.*,coop_prename.prename_short');
		$this->paginater_all->select('
										coop_loan_atm.contract_number,
										coop_loan_atm.member_id,
										coop_loan_atm.total_amount_approve,
										coop_loan_atm.total_amount_balance,
										coop_loan_atm.loan_atm_id
										');
		$this->paginater_all->main_table('coop_loan_atm');
		$this->paginater_all->where("coop_loan_atm.loan_atm_status = '1'");
		$this->paginater_all->page_now(@$_GET["page"]);
		$this->paginater_all->per_page(20);
		$this->paginater_all->page_link_limit(20);
		//$this->paginater_all->order_by('coop_mem_apply.mem_apply_id DESC');
		$this->paginater_all->join_arr($join_arr);
		$row = $this->paginater_all->paginater_process();

		$paging = $this->pagination_center->paginating($row['page'], $row['num_rows'], $row['per_page'], $row['page_link_limit']);//$page_now = 1, $row_total = 1, $per_page = 20, $page_limit = 20
		$i = $row['page_start'];

		foreach($row['data'] AS $key=>$value){
			$this->db->select('coop_mem_apply.firstname_th, coop_mem_apply.lastname_th,coop_prename.prename_short');
			$this->db->join("coop_prename","coop_prename.prename_id = coop_mem_apply.prename_id","left");
			$this->db->from("coop_mem_apply");
			$this->db->where("member_id = '".@$value['member_id']."'");
			$rs_member = $this->db->get()->result_array();
			$row_member = @$rs_member[0];
			$row['data'][$key]['prename_short'] = $row_member['prename_short'];
			$row['data'][$key]['firstname_th'] = $row_member['firstname_th'];
			$row['data'][$key]['lastname_th'] = $row_member['lastname_th'];
		}
		
		$arr_data['num_rows'] = $row['num_rows'];
		$arr_data['paging'] = $paging;
		$arr_data['row'] = $row['data'];
		$arr_data['i'] = $i;

		$this->libraries->template('loan_atm/loan_atm_all',$arr_data);
	}
	
	function get_search_loan(){
		$where = "
		 	 AND (coop_mem_apply.member_id LIKE '%".$this->input->post('search_text')."%'
		 	OR coop_mem_apply.firstname_th LIKE '%".$this->input->post('search_text')."%'
			OR coop_mem_apply.lastname_th LIKE '%".$this->input->post('search_text')."%'
			OR coop_loan_atm.contract_number LIKE '%".$this->input->post('search_text')."%')
		";
		$this->db->select(array(
					'coop_mem_apply.id',
					'coop_mem_apply.member_id',
					'coop_mem_apply.firstname_th',
					'coop_mem_apply.lastname_th',
					'coop_loan_atm.loan_atm_id',
					'coop_loan_atm.contract_number',
					'coop_loan_atm.total_amount_approve',
					'coop_loan_atm.total_amount_balance'
		));
		$this->db->from('coop_loan_atm');
		$this->db->join("coop_mem_apply","coop_mem_apply.member_id = coop_loan_atm.member_id","left");
		$this->db->where("loan_atm_status = '1'".$where);
		$this->db->order_by('coop_mem_apply.mem_apply_id DESC');
		$row = $this->db->get()->result_array();
		$arr_data['data'] = $row;
		$arr_data['form_target'] = $this->input->post('form_target');
		//echo"<pre>";print_r($arr_data['data']);exit;
		$this->load->view('loan_atm/get_search_loan',$arr_data);
	}
	
	function loan_cancel_contract(){
		//echo"<pre>";print_r($_POST);echo"</pre>";exit;
		$data_insert = array();
		$data_insert['loan_atm_status'] = '3';
		$this->db->where('loan_atm_id',$_POST['loan_atm_id']);
		$this->db->update('coop_loan_atm',$data_insert);
		
		$this->center_function->toast("บันทึกข้อมูลเรียบร้อยแล้ว");
		echo "<script> document.location.href='".base_url(PROJECTPATH.'/loan_atm?member_id='.$_POST['member_id'])."' </script>";
		exit;
	}
	
	function petition_emergent_atm_pdf($loan_atm_id){
		$arr_data = array();
		
		$this->db->select(array(
			't1.*',
			't2.*',
			't3.prename_short',
			't4.district_name',
			't5.amphur_name',
			't6.province_name',
			't7.mem_group_name',
			't8.loan_reason'
		));
		$this->db->from('coop_loan_atm as t1');
		$this->db->join("coop_mem_apply as t2","t2.member_id = t1.member_id","inner");
		$this->db->join("coop_prename as t3","t3.prename_id = t2.prename_id","left");
		$this->db->join("coop_district as t4","t2.c_district_id = t4.district_id","left");
		$this->db->join("coop_amphur as t5","t2.c_amphur_id = t5.amphur_id","left");
		$this->db->join("coop_province as t6","t2.c_province_id = t6.province_id","left");
		$this->db->join("coop_mem_group as t7","t2.level = t7.id","left");
		$this->db->join("coop_loan_reason as t8","t1.loan_reason = t8.loan_reason_id","left");
		$this->db->where("t1.loan_atm_id = '".$loan_atm_id."'");
		$row = $this->db->get()->result_array();
		$arr_data['data'] = $row[0];
		
		$this->load->view('loan_atm/petition_emergent_atm_pdf',$arr_data);
	}
	
	function loan_change_amount(){
		// echo"<pre>";print_r($_POST);echo"</pre>";exit;
		$total_amount = str_replace(',','',$_POST['total_amount']);
		$this->db->select(array('total_amount_approve','total_amount_balance'));
		$this->db->from('coop_loan_atm');
		$this->db->where("loan_atm_id = '".$_POST['loan_atm_id']."'");
		$row = $this->db->get()->result_array();
		$row_loan_atm = @$row[0];
		$loan_amount_balance = $row_loan_atm['total_amount_approve'] - $row_loan_atm['total_amount_balance'];
		//echo $total_amount.":".$loan_amount_balance;exit; 
		$data_insert = array();
		$data_insert['change_amount_status'] = '1';
		$this->db->where('loan_atm_id',$_POST['loan_atm_id']);
		$this->db->update('coop_loan_atm', $data_insert);
		
		$data_insert = array();
		$data_insert['member_id'] = $_POST['member_id'];
		$data_insert['total_amount'] = $total_amount;
		$data_insert['total_amount_balance'] = $data_insert['total_amount'];
		$data_insert['createdatetime'] = date('Y-m-d H:i:s');
		$data_insert['loan_atm_status'] = '0';
		$data_insert['loan_reason'] = $_POST['loan_reason'];
		$data_insert['admin_id'] = $_SESSION['USER_ID'];
		$data_insert['change_from'] = $_POST['loan_atm_id'];
		
		$this->db->select(array('petition_number'));
		$this->db->from('coop_loan_atm');
		$this->db->order_by('petition_number DESC');
		$this->db->limit(1);
		$row_petition_number = $this->db->get()->result_array();
		if(!empty($row_petition_number)){
			$petition_number = (int)$row_petition_number[0]['petition_number']+1;
			$petition_number = sprintf('%06d',$petition_number);
		}else{
			$petition_number = sprintf('%06d',1);
		}
		$data_insert['petition_number'] = $petition_number;
		
		$this->db->insert('coop_loan_atm',$data_insert);
		
		$loan_atm_id = $this->db->insert_id();

		//prev loan atm

		$pay_type = isset($_POST['interest_amount']) && $_POST['interest_amount'] < 0 ? 'principal' : 'all';

		$data_prev_loan_atm = array(
			'loan_atm_id' => $loan_atm_id,
			'ref_id' => $_POST['loan_atm_id'],
			'data_type' => 'atm',
			'pay_type' => $pay_type,
			'principal_amount' => $_POST['principal_amount'],
			'interest_amount' => $_POST['interest_amount'],
			'pay_amount' => $_POST['pay_amount']
		);
		$this->db->insert('coop_loan_atm_prev_deduct', $data_prev_loan_atm);

		
		
		if($loan_atm_id!=''){
			$output_dir = $_SERVER["DOCUMENT_ROOT"].PROJECTPATH."/assets/uploads/loan_atm_attach/";
			if($_FILES['file_attach']['name'][0]!=''){
				foreach($_FILES['file_attach']['name'] as $key => $value){
					$new_file_name = $this->center_function->create_file_name($output_dir,$_FILES['file_attach']['name'][$key]);
					@copy($_FILES["file_attach"]["tmp_name"][$key],$output_dir.$new_file_name);
					
					$data_insert = array();
					$data_insert['loan_atm_id'] = $loan_atm_id;
					$data_insert['file_old_name'] = $_FILES['file_attach']['name'][$key];
					$data_insert['file_name'] = $new_file_name;
					$data_insert['file_path'] = $output_dir.$new_file_name;
					$data_insert['file_type'] = $_FILES['file_attach']['type'][$key];
					$this->db->insert('coop_loan_atm_file_attach', $data_insert);
				}
			}
		}

		$this->center_function->toast("บันทึกข้อมูลเรียบร้อยแล้ว");
		echo "<script> document.location.href='".base_url(PROJECTPATH.'/loan_atm?member_id='.@$_POST['member_id'])."' </script>";
	}
	
	function loan_atm_transfer_view($transfer_type='',$start_date='',$end_date=''){
		$arr_data = array();
		
		/*$this->db->select(array('t1.*','t2.firstname_th','t2.lastname_th','t3.prename_short'));
		$this->db->from('coop_loan_atm as t1');
		$this->db->join("coop_mem_apply as t2",'t1.member_id = t2.member_id','inner');
		$this->db->join("coop_prename as t3",'t2.prename_id = t3.prename_id','left');
		$this->db->where("loan_atm_id = '".$loan_atm_id."'");
		$row = $this->db->get()->result_array();
		$row_loan_atm = @$row[0];
		$arr_data['row_loan_atm'] = $row_loan_atm;
		
		$this->db->select(array('*'));
		$this->db->from('coop_loan_atm_detail');
		$this->db->where("loan_atm_id = '".$loan_atm_id."'");
		$this->db->order_by("loan_date ASC");
		$row = $this->db->get()->result_array();
		$arr_data['row_loan_atm_detail'] = $row;
		
		$arr_data['transaction_at'] = $this->transaction_at;
		*/
		
		$this->preview_libraries->template_preview('loan_atm/loan_atm_transfer_view',$arr_data);
	}
	
	function payment_slip($loan_atm_detail_id){
		$arr_data = array();
		
		$this->db->select(array(
			't1.*',
			't2.contract_number',
			't2.total_amount_approve',
			't2.total_amount_balance',
			't3.firstname_th',
			't3.lastname_th',
			't4.prename_short',
			't3.dividend_acc_num',
			't5.mem_group_name as level_name',
			't6.mem_group_name as faction_name',
			't7.mem_group_name as department_name'
		));
		$this->db->from('coop_loan_atm_detail as t1');
		$this->db->join('coop_loan_atm as t2','t1.loan_atm_id = t2.loan_atm_id','inner');
		$this->db->join('coop_mem_apply as t3','t2.member_id = t3.member_id','inner');
		$this->db->join('coop_prename as t4','t3.prename_id = t4.prename_id','left');
		$this->db->join('coop_mem_group as t5','t3.level = t5.id','left');
		$this->db->join('coop_mem_group as t6','t3.faction = t6.id','left');
		$this->db->join('coop_mem_group as t7','t3.department = t7.id','left');
		$this->db->where("t1.loan_id = '".$loan_atm_detail_id."'");
		$row = $this->db->get()->result_array();
		//echo"<pre>";print_r($row[0]);echo"</pre>";
		$arr_data['data'] = @$row[0];
		$this->preview_libraries->template_preview('loan_atm/payment_slip',$arr_data);
	}
	
	function loan_atm_payment_detail($loan_atm_id){
		$arr_data = array();
		
		$this->db->select(array(
			't1.*',
			't2.firstname_th',
			't2.lastname_th',
			't3.prename_short'
		));
		$this->db->from('coop_loan_atm as t1');
		$this->db->join('coop_mem_apply as t2','t1.member_id = t2.member_id','inner');
		$this->db->join('coop_prename as t3','t2.prename_id = t3.prename_id','left');
		$this->db->where("t1.loan_atm_id = '".$loan_atm_id."'");
		$loan_data = $this->db->get()->result_array();
		$arr_data['loan_data'] = $loan_data[0];
		
		/*
		$this->db->select(array('t1.*','t2.finance_month_profile_id'));
		$this->db->from('coop_finance_transaction as t1');
		$this->db->join('coop_receipt as t2','t1.receipt_id = t2.receipt_id','inner');
		$this->db->where("t1.loan_atm_id = '".$loan_atm_id."'");
		$this->db->order_by('payment_date ASC');
		*/
		// $this->db->select("
		// 					t1.transaction_datetime,
		// 					DATE(t1.transaction_datetime) AS payment_date,
		// 					t1.receipt_id,
		// 					t2.loan_description,
		// 					SUM(IF(t2.loan_amount <> '',t2.loan_amount,t3.principal_payment)) AS principal,
		// 					SUM(t3.interest) AS interest,
		// 					SUM(t3.total_amount) AS total_amount,
		// 					t1.loan_amount_balance,
		// 					IF(t2.loan_description != '',t2.loan_description,IF(t4.finance_month_profile_id != '','ชำระเงินรายเดือน','ชำระเงินอื่นๆ')) AS data_text
		// 				");
		// $this->db->from('coop_loan_atm_transaction AS t1');
		// $this->db->join('coop_loan_atm_detail AS t2','t1.loan_atm_id = t2.loan_atm_id AND t1.transaction_datetime = t2.loan_date ','left');
		// $this->db->join('coop_finance_transaction AS t3','t1.receipt_id = t3.receipt_id AND t1.loan_atm_id = t3.loan_atm_id','left');
		// $this->db->join('coop_receipt AS t4','t3.receipt_id = t4.receipt_id','left');
		// $this->db->where("t1.loan_atm_id = '".$loan_atm_id."'");
		// $this->db->group_by('t1.transaction_datetime');
		// $this->db->order_by('t1.transaction_datetime ASC');
		/*$query = $this->db->query("select * from (SELECT
						`t1`.`transaction_datetime`,
						DATE(t1.transaction_datetime) AS payment_date,
						`t1`.`receipt_id`,
						`t2`.`loan_description`,
						SUM(

							IF (
								t2.loan_amount <> '',
								`t2`.`loan_amount`,
								IF (
									t7.text_error != '',
									t7.loan_amount,
									t3.principal_payment
								)
							)
						) AS principal,
						SUM(t3.interest) AS interest,
						SUM(t3.total_amount) AS total_amount,
						IF (
						! ISNULL(
							(
								SELECT
									ret_id
								FROM
									coop_process_return
								WHERE
									coop_process_return.return_month = t4.month_receipt
								AND coop_process_return.return_year = (t4.year_receipt - 543)
								AND coop_process_return.loan_atm_id = t1.loan_atm_id
								LIMIT 1
							)
						),
						
						IF (
								t2.loan_amount <> '',
								`t2`.`loan_amount`,
								IF( 
										t1.loan_amount_balance <> 0,
										 t1.loan_amount_balance,
										(	
											SELECT
												IF(t9.return_principal > 0,(t9.return_principal) * - 1,0) AS return_principal
											FROM
												coop_finance_transaction AS t7
											LEFT JOIN coop_receipt AS t8 ON t7.receipt_id = t8.receipt_id
											LEFT JOIN coop_process_return AS t9 ON t9.return_month = t8.month_receipt AND t9.return_year = (t8.year_receipt-543) AND t7.loan_atm_id = t9.loan_atm_id
											WHERE
												t7.principal_payment <> 0	AND t7.receipt_id = t3.receipt_id	AND t7.loan_atm_id = t3.loan_atm_id
											LIMIT 1	
										)	
								)
							),
						`t1`.`loan_amount_balance`
					) AS loan_amount_balance,

					IF (
						t2.loan_description != '',
						`t2`.`loan_description`,

					IF (
						t4.finance_month_profile_id != '',
						'ชำระเงินรายเดือน',
						IF (
							t7.text_error != '',
							t7.text_error,
							'ชำระเงินอื่นๆ'
						)
					)
					) AS data_text
					FROM
						`coop_loan_atm_transaction` AS `t1`
					LEFT JOIN `coop_loan_atm_detail` AS `t2` ON `t1`.`loan_atm_id` = `t2`.`loan_atm_id`
					AND `t1`.`transaction_datetime` = `t2`.`loan_date`
					LEFT JOIN `coop_finance_transaction` AS `t3` ON `t1`.`receipt_id` = `t3`.`receipt_id`
					AND `t1`.`loan_atm_id` = `t3`.`loan_atm_id`
					LEFT JOIN `coop_receipt` AS `t4` ON `t3`.`receipt_id` = `t4`.`receipt_id`
					LEFT JOIN coop_receipt AS t6 ON t1.receipt_id = t6.receipt_id
					LEFT JOIN coop_loan_atm_transaction_error AS t7 ON t1.loan_atm_transaction_id = t7.loan_atm_transaction_id
					WHERE
						`t1`.`loan_atm_id` = '".$loan_atm_id."'
					GROUP BY
						t1.transaction_datetime,t1.loan_atm_transaction_id
					UNION ALL
					SELECT return_time,date(return_time),null,CONCAT('คืนเงิน ',receipt_id),IF(return_principal>0,(return_principal)* - 1,(return_amount) * - 1),IF(return_interest>0,(return_interest)* - 1,0),IF(return_amount>0,(return_amount)* - 1,0),null,CONCAT('คืนเงิน ',receipt_id)FROM coop_process_return WHERE return_type = 3 and loan_atm_id = '".$loan_atm_id."'
					) as m
					ORDER BY
					m.`transaction_datetime` ASC"
		);
		*/
		
		$query = $this->db->query(
			"select DISTINCT * from (SELECT
						IF (
							! ISNULL(
								(
									SELECT
										ret_id
									FROM
										coop_process_return
									WHERE
										coop_process_return.loan_atm_id = t1.loan_atm_id
									AND coop_process_return.return_time = t1.transaction_datetime
									LIMIT 1
								)
							),
							'99999999999',
							t1.loan_atm_transaction_id
					) AS loan_atm_transaction_id,	
						t1.transaction_datetime,
						DATE(t1.transaction_datetime) AS payment_date,
						IF(t1.receipt_id = '',
								NULL,
								t1.receipt_id
								
						) AS receipt_id,
						IF (
							! ISNULL(
								(
									SELECT
										ret_id
									FROM
										coop_process_return
									WHERE
										coop_process_return.loan_atm_id = t1.loan_atm_id
										AND  coop_process_return.return_time = t1.transaction_datetime
									LIMIT 1
								)
							),
							CASE WHEN t8.return_desc != '' THEN t8.return_desc ELSE CONCAT('คืนเงิน ',t8.receipt_id ) END,
							t2.loan_description
							) AS loan_description,
						IF (
							! ISNULL(
								(
									SELECT
										ret_id
									FROM
										coop_process_return
									WHERE
										coop_process_return.loan_atm_id = t1.loan_atm_id
										AND  coop_process_return.return_time = t1.transaction_datetime
									LIMIT 1
								)
							),
							(SELECT
									IF (
										t9.return_principal > 0,
										(t9.return_principal) * - 1,
										0
									) AS return_principal
								FROM
									coop_loan_atm_transaction AS t8
								LEFT JOIN coop_process_return AS t9 ON t8.loan_atm_id = t9.loan_atm_id AND t8.transaction_datetime = t9.return_time
								WHERE
									t9.return_time = t1.transaction_datetime 
									AND t8.loan_atm_id = t1.loan_atm_id 
								LIMIT 1),
								SUM(
									IF (
										t2.loan_amount <> '',
										`t2`.`loan_amount`,
									IF (
										t7.text_error != '',
										t7.loan_amount,
										t3.principal_payment
									)
									)
								)
							) AS principal,
						IF (
							! ISNULL(
								(
									SELECT
										ret_id
									FROM
										coop_process_return
									WHERE
										coop_process_return.loan_atm_id = t1.loan_atm_id
										AND  coop_process_return.return_time = t1.transaction_datetime
									LIMIT 1
								)
							),
							(SELECT
									IF (
										t9.return_interest > 0,
										(t9.return_interest) * - 1,
										0
									) AS return_interest
								FROM
									coop_loan_atm_transaction AS t8
								LEFT JOIN coop_process_return AS t9 ON t8.loan_atm_id = t9.loan_atm_id AND t8.transaction_datetime = t9.return_time
								WHERE
									t9.return_time = t1.transaction_datetime 
									AND t8.loan_atm_id = t1.loan_atm_id 
								LIMIT 1),
								SUM(IF(t3.loan_interest_remain IS NULL,t3.interest,t3.interest+t3.loan_interest_remain))
							) AS interest,
						IF (
							! ISNULL(
								(
									SELECT
										ret_id
									FROM
										coop_process_return
									WHERE
										coop_process_return.loan_atm_id = t1.loan_atm_id
										AND  coop_process_return.return_time = t1.transaction_datetime
									LIMIT 1
								)
							),
							(SELECT
									IF (return_amount > 0,(return_amount) * - 1,0) AS return_principal
								FROM
									coop_loan_atm_transaction AS t8
								LEFT JOIN coop_process_return AS t9 ON t8.loan_atm_id = t9.loan_atm_id AND t8.transaction_datetime = t9.return_time
								WHERE
									t9.return_time = t1.transaction_datetime 
									AND t8.loan_atm_id = t1.loan_atm_id 
								LIMIT 1),
							SUM(t3.total_amount)
							) AS total_amount,
						IF (
						! ISNULL(
							(
								SELECT
									ret_id
								FROM
									coop_process_return
								WHERE
									coop_process_return.loan_atm_id = t1.loan_atm_id
									AND  coop_process_return.return_time = t1.transaction_datetime
								LIMIT 1
							)
						),
						
					IF (
						t2.loan_amount <> '',
						t2.loan_amount,

					IF (
						t1.loan_amount_balance <> 0,
						t1.loan_amount_balance,
						(
							IF(
									(SELECT
										IF (
											t9.return_principal > 0,
											(t9.return_principal) * - 1,
											0
										) AS return_principal
									FROM
										coop_finance_transaction AS t7
									LEFT JOIN coop_receipt AS t8 ON t7.receipt_id = t8.receipt_id
									LEFT JOIN coop_process_return AS t9 ON t9.return_month = t8.month_receipt
									AND t9.return_year = (t8.year_receipt - 543)
									AND t7.loan_atm_id = t9.loan_atm_id
									WHERE
										t7.principal_payment <> 0
									AND t7.receipt_id = t3.receipt_id
									AND t7.loan_atm_id = t3.loan_atm_id
									LIMIT 1
								) > 0,
								(SELECT
										IF (
											t9.return_principal > 0,
											(t9.return_principal) * - 1,
											0
										) AS return_principal
									FROM
										coop_finance_transaction AS t7
									LEFT JOIN coop_receipt AS t8 ON t7.receipt_id = t8.receipt_id
									LEFT JOIN coop_process_return AS t9 ON t9.return_month = t8.month_receipt
									AND t9.return_year = (t8.year_receipt - 543)
									AND t7.loan_atm_id = t9.loan_atm_id
									WHERE
										t7.principal_payment <> 0
									AND t7.receipt_id = t3.receipt_id
									AND t7.loan_atm_id = t3.loan_atm_id
									LIMIT 1)
								,
								0
							)
						)
					)
					),
					 `t1`.`loan_amount_balance`
					) AS loan_amount_balance,

					IF (
						t2.loan_description != '',
						`t2`.`loan_description`,

					IF (
						t4.finance_month_profile_id != '',
						'ชำระเงินรายเดือน',
						IF (
							t7.text_error != '',
							t7.text_error,
							IF(
								! ISNULL(
									(
										SELECT
											ret_id
										FROM
											coop_process_return
										WHERE
											coop_process_return.loan_atm_id = t1.loan_atm_id
											AND  coop_process_return.return_time = t1.transaction_datetime
										LIMIT 1
									)
								),
							CASE WHEN t8.return_desc != '' THEN t8.return_desc ELSE CONCAT('คืนเงิน ',t8.receipt_id ) END,
							'ชำระเงินอื่นๆ'
							)
						)
					)
					) AS data_text
					FROM
						`coop_loan_atm_transaction` AS `t1`
					LEFT JOIN `coop_loan_atm_detail` AS `t2` ON `t1`.`loan_atm_id` = `t2`.`loan_atm_id`
					AND `t1`.`transaction_datetime` = `t2`.`loan_date`
					LEFT JOIN `coop_finance_transaction` AS `t3` ON `t1`.`receipt_id` = `t3`.`receipt_id`
					AND `t1`.`loan_atm_id` = `t3`.`loan_atm_id`
					LEFT JOIN `coop_receipt` AS `t4` ON `t3`.`receipt_id` = `t4`.`receipt_id`
					LEFT JOIN coop_receipt AS t6 ON t1.receipt_id = t6.receipt_id
					LEFT JOIN coop_loan_atm_transaction_error AS t7 ON t1.loan_atm_transaction_id = t7.loan_atm_transaction_id
					LEFT JOIN coop_process_return AS t8 ON t1.loan_atm_id = t8.loan_atm_id AND t1.transaction_datetime = t8.return_time
					WHERE
						`t1`.`loan_atm_id` = '".$loan_atm_id."'
					GROUP BY
						t1.transaction_datetime,t1.loan_atm_transaction_id
					UNION ALL
					SELECT '99999999999' AS loan_atm_transaction_id,return_time,date(return_time),IF(bill_id = '',NULL ,bill_id),CASE WHEN return_desc != '' THEN return_desc ELSE  CONCAT('คืนเงิน ',receipt_id) END,IF(return_principal>0,(return_principal)* - 1,0),IF(return_interest>0,(return_interest)* - 1,0),IF(return_amount>0,(return_amount)* - 1,0),(SELECT t6.loan_amount_balance FROM coop_loan_atm_transaction AS t6 WHERE t6.receipt_id = coop_process_return.bill_id AND t6.loan_atm_id = coop_process_return.loan_atm_id),CASE WHEN return_desc != '' THEN return_desc ELSE  CONCAT('คืนเงิน ',receipt_id) END FROM coop_process_return WHERE return_type IN (3,4) and loan_atm_id = '".$loan_atm_id."'
					) as m
					ORDER BY
					m.transaction_datetime,m.loan_atm_transaction_id ASC"		
		);
		
		$row_transaction = $query->result_array();
		if(@$_GET['dev'] == 'dev'){
			echo $this->db->last_query(); exit;
		}
		$arr_data['transaction_data'] = $row_transaction;
		// echo"<pre>";print_r($transaction_data);exit;
		$this->preview_libraries->template_preview('loan_atm/loan_atm_payment_detail',$arr_data);
	}
	
	function loan_atm_lock($loan_atm_id,$member_id){
		$data_insert = array();
		$data_insert['activate_status'] = '1';
		$this->db->where('loan_atm_id',$loan_atm_id);
		$this->db->update('coop_loan_atm',$data_insert);
		$this->center_function->toast("ระงับสัญญาเรียบร้อยแล้ว");
		echo "<script> document.location.href='".base_url(PROJECTPATH.'/loan_atm?member_id='.@$member_id)."' </script>";
	}
	function loan_atm_unlock($loan_atm_id,$member_id){
		$data_insert = array();
		$data_insert['activate_status'] = '0';
		$this->db->where('loan_atm_id',$loan_atm_id);
		$this->db->update('coop_loan_atm',$data_insert);
		$this->center_function->toast("ปลดระงับสัญญาเรียบร้อยแล้ว");
		echo "<script> document.location.href='".base_url(PROJECTPATH.'/loan_atm?member_id='.@$member_id)."' </script>";
	}
	
	function ajax_get_loan_atm_prev_deduct(){
		$loan_atm_id = isset($_POST['loan_atm_id']) ? trim(@$_POST['loan_atm_id']) : "";
		
		$arr_data = array();
		$this->db->select(array('*'));
		$this->db->from('coop_loan_atm');
		$this->db->where("loan_atm_id = '".$loan_atm_id."' AND loan_atm_status = '0'");
		$rs_loan = $this->db->get()->result_array();
		$arr_data['coop_loan_atm'] = @$rs_loan[0];	
		//echo '<pre>'; print_r($arr_data['coop_loan_atm']); echo '</pre>';
		//รายการที่หักกลบ
		$this->db->select(array('*'));
		$this->db->from("coop_loan_atm_prev_deduct");
		$this->db->where("loan_atm_id = '".@$loan_atm_id."'");
		$rs_prev_deduct = $this->db->get()->result_array();
		$arr_data['coop_loan_atm_prev_deduct'] = @$rs_prev_deduct;	
		//echo '<pre>'; print_r($rs_prev_deduct); echo '</pre>';
		//if(!empty($rs_prev_deduct)){
		if(!empty($arr_data)){
			echo json_encode($arr_data);
		}else{
			echo 'not_found';
		}
		exit;
	}
}

