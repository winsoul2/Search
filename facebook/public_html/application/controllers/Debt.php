<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Debt extends CI_Controller {
	function __construct()
	{
		parent::__construct();
		$this->month_arr = array('1'=>'มกราคม','2'=>'กุมภาพันธ์','3'=>'มีนาคม','4'=>'เมษายน','5'=>'พฤษภาคม','6'=>'มิถุนายน','7'=>'กรกฎาคม','8'=>'สิงหาคม','9'=>'กันยายน','10'=>'ตุลาคม','11'=>'พฤศจิกายน','12'=>'ธันวาคม');
		$this->load->model("Deposit_seq_model", "deposit_seq");
	}

	public function index(){
		$arr_data = array();
		$where = "";
		if($_GET){
			foreach($_GET AS $key=>$value){
				$arr_data['post'][$key] = $value;
			}
			
			$year = @$_GET['year'];
			$month = @$_GET['month'];
			$department = @$_GET['department'];
			$faction = @$_GET['faction'];
			$level = @$_GET['level'];
			$search_member = @$_GET['search_member'];
			if(!empty($year)){
				$where .= " AND coop_non_pay.non_pay_year = '{$year}'";
			}
			if(!empty($month)){
				$where .= " AND coop_non_pay.non_pay_month = '{$month}'";
			}
			
			if(!empty($department)){
				$where .= " AND coop_mem_apply.department = '{$department}'";
			}
			if(!empty($faction)){
				$where .= " AND coop_mem_apply.faction = '{$faction}'";
			}
			if(!empty($level)){
				$where .= " AND coop_mem_apply.level = '{$level}'";
			}
			if(!empty($search_member)){
				$where .= " AND (coop_mem_apply.firstname_th LIKE '%{$search_member}%' 
				OR coop_mem_apply.lastname_th LIKE '%{$search_member}%' 
				OR coop_mem_apply.member_id LIKE '%{$search_member}%')";
			}

			$non_pays = $this->db->select("*, coop_non_pay.non_pay_amount as non_pay_amount, coop_non_pay.non_pay_amount_balance as non_pay_amount_balance")
								->from("coop_non_pay")
								->join("coop_mem_apply", "coop_non_pay.member_id = coop_mem_apply.member_id", "left")
								->join("(SELECT * FROM coop_non_pay_detail WHERE deduct_code != 'DEPOSIT' AND deduct_code != 'CREMATION') as coop_non_pay_detail", "coop_non_pay.non_pay_id = coop_non_pay_detail.non_pay_id", "inner")
								->where("non_pay_status != '0' && non_pay_status != '2' {$where} AND coop_mem_apply.mem_type not in (2,4,5)")
								->order_by('coop_non_pay.member_id, coop_non_pay.non_pay_id DESC')
								->group_by('coop_non_pay.non_pay_id')
								->get()->result_array();
			$arr_data['row'] = $non_pays;
		}

		$this->db->select('id, mem_group_name');
		$this->db->from('coop_mem_group');
		$this->db->where("mem_group_type='1'");
		$row = $this->db->get()->result_array();
		$arr_data['department'] = $row;

		if(@$arr_data['data']['department'] != ''){
			$this->db->select('id, mem_group_name');
			$this->db->from('coop_mem_group');
			$this->db->where("mem_group_parent_id = '".@$arr_data['data']['department']."' AND mem_group_type='2'");
			$row = $this->db->get()->result_array();
			$arr_data['faction'] = $row;
		}else{
			$arr_data['faction'] = array();
		}

		if(@$arr_data['data']['faction'] != '') {
			$this->db->select('id, mem_group_name');
			$this->db->from('coop_mem_group');
			$this->db->where("mem_group_parent_id = '".@$arr_data['data']['faction']."' AND mem_group_type='3'");
			$row = $this->db->get()->result_array();
			$arr_data['level'] = $row;
		}else{
			$arr_data['level'] = array();
		}
		
		$arr_data['month_arr'] = array('1'=>'มกราคม','2'=>'กุมภาพันธ์','3'=>'มีนาคม','4'=>'เมษายน','5'=>'พฤษภาคม','6'=>'มิถุนายน','7'=>'กรกฎาคม','8'=>'สิงหาคม','9'=>'กันยายน','10'=>'ตุลาคม','11'=>'พฤศจิกายน','12'=>'ธันวาคม');

		$this->libraries->template('debt/index',$arr_data);
	}
	
	function get_receipt_detail(){
		$non_pay_id = @$_POST['non_pay_id'];
	
		$this->db->select(array('coop_finance_transaction.loan_id',
								'coop_finance_transaction.principal_payment',
								'coop_finance_transaction.interest',
								'coop_finance_transaction.period_count',
								'coop_finance_transaction.total_amount',
								'coop_finance_transaction.transaction_text'));
		$this->db->from('coop_non_pay');
		$this->db->join("coop_receipt","coop_receipt.member_id = coop_non_pay.member_id
						AND coop_receipt.month_receipt = coop_non_pay.non_pay_month
						AND coop_receipt.year_receipt = coop_non_pay.non_pay_year","left");
		$this->db->join("coop_finance_transaction","coop_receipt.receipt_id = coop_finance_transaction.receipt_id","inner");
		$this->db->where("coop_non_pay.non_pay_id = {$non_pay_id}");
		$this->db->order_by('coop_finance_transaction.receipt_id ASC');
		$rs_transaction = $this->db->get()->result_array();
		//echo $this->db->last_query(); exit;
		$i=1;
		$receipt_amount_all = 0;
		$data = '';
		//echo '<pre>'; print_r($rs_transaction); echo '</pre>';
		if(!empty($rs_transaction)){			
			foreach(@$rs_transaction as $key => $row_receipt){	
				$data .= '
					<tr> 
						<td>'.@$i.'</td>
						<td class="text-left">'.$row_receipt['transaction_text'].'</td>
						<td class="text-right">'.number_format($row_receipt['total_amount'],2).'</td>										
					</tr>
					';							
		
				$i++; 
				$receipt_amount_all += @$row_receipt['total_amount'];
			}
		}else{
			$data .= '<tr><td colspan="3">ไม่พบข้อมูล</td></tr>';
		}
					   
			$data .= '<tr style="background-color: #e0e0e0;"> 
							<th colspan="2">รวม</th>									
							<th class="text-right">'.number_format(@$receipt_amount_all,2).'</th>									
				   </tr>';
		
		echo $data;
		exit();
	}
	
	public function letter(){
		$arr_data = array();
		
		$where = "";
		$where_non_pay = "";
		if($_GET){
			foreach($_GET AS $key=>$value){
				$arr_data['get'][$key] = $value;
			}
			
			$year = @$_GET['year'];
			$month = @$_GET['month'];
			$department = @$_GET['department'];
			$arr_data['data']['department'] = $department;
			$faction = @$_GET['faction'];
			$arr_data['data']['faction'] = $faction;
			$level = @$_GET['level'];
			$arr_data['data']['level'] = $level;
			$search_member = @$_GET['search_member'];
			if(!empty($year)){
				// $where .= " AND cast(coop_non_pay.non_pay_year as int) <= '{$year}'";
				$where .= " AND coop_non_pay.non_pay_year = '{$year}'";
			}
			if(!empty($month)){
				// $where .= " AND cast(coop_non_pay.non_pay_month as int) <= '{$month}'";
				$where .= " AND coop_non_pay.non_pay_month = '{$month}'";
			}
			
			if(!empty($department)){
				$where .= " AND coop_mem_apply.department = '{$department}'";
			}
			if(!empty($faction)){
				$where .= " AND coop_mem_apply.faction = '{$faction}'";
			}
			if(!empty($level)){
				$where .= " AND coop_mem_apply.level = '{$level}'";
			}
			if(!empty($search_member)){
				$where .= " AND (coop_mem_apply.firstname_th LIKE '%{$search_member}%' 
				OR coop_mem_apply.lastname_th LIKE '%{$search_member}%' 
				OR coop_mem_apply.member_id LIKE '%{$search_member}%')";
			}

			$non_pays = $this->db->select("coop_non_pay.non_pay_id,
											coop_non_pay.non_pay_year,
											coop_non_pay.non_pay_month,
											coop_non_pay.member_id,
											coop_non_pay.non_pay_amount as non_pay_amount,
											SUM(coop_non_pay_detail.non_pay_amount_balance) as non_pay_amount_balance,
											coop_mem_apply.firstname_th,
											coop_mem_apply.lastname_th
										")
								->from("coop_non_pay")
								->join("coop_mem_apply", "coop_non_pay.member_id = coop_mem_apply.member_id", "left")
								->join("(SELECT * FROM coop_non_pay_detail WHERE deduct_code != 'DEPOSIT' AND deduct_code != 'CREMATION') as coop_non_pay_detail", "coop_non_pay.non_pay_id = coop_non_pay_detail.non_pay_id", "inner")
								->where("coop_non_pay.non_pay_status != '0' AND coop_non_pay.non_pay_status != '2' {$where} AND coop_mem_apply.mem_type not in (2,4,5)")
								->order_by('ABS(coop_non_pay.non_pay_year), ABS(coop_non_pay.non_pay_month), coop_non_pay.member_id')
								->group_by('coop_non_pay_detail.non_pay_id')
								->having('SUM(coop_non_pay_detail.non_pay_amount_balance) > 0')
								->get()->result_array();
			$arr_data['row'] = $non_pays;
		}

		$this->db->select('id, mem_group_name');
		$this->db->from('coop_mem_group');
		$this->db->where("mem_group_type='1'");
		$row = $this->db->get()->result_array();
		$arr_data['department'] = $row;

		if(@$arr_data['data']['department'] != ''){
			$this->db->select('id, mem_group_name');
			$this->db->from('coop_mem_group');
			$this->db->where("mem_group_parent_id = '".@$arr_data['data']['department']."' AND mem_group_type='2'");
			$row = $this->db->get()->result_array();
			$arr_data['faction'] = $row;
		}else{
			$arr_data['faction'] = array();
		}

		if(@$arr_data['data']['faction'] != '') {
			$this->db->select('id, mem_group_name');
			$this->db->from('coop_mem_group');
			$this->db->where("mem_group_parent_id = '".@$arr_data['data']['faction']."' AND mem_group_type='3'");
			$row = $this->db->get()->result_array();
			$arr_data['level'] = $row;
		}else{
			$arr_data['level'] = array();
		}
		
		$arr_data['month_arr'] = array('1'=>'มกราคม','2'=>'กุมภาพันธ์','3'=>'มีนาคม','4'=>'เมษายน','5'=>'พฤษภาคม','6'=>'มิถุนายน','7'=>'กรกฎาคม','8'=>'สิงหาคม','9'=>'กันยายน','10'=>'ตุลาคม','11'=>'พฤศจิกายน','12'=>'ธันวาคม');
		
		
		$this->db->select(array('num_letter'));
		$this->db->from('coop_debt_letter_setting');
		$this->db->where('setting_code = "debt_letter_time"');
		$this->db->limit(1);
		$rs = $this->db->get()->result_array();		
		$arr_data['num_letter'] = $rs[0]['num_letter'];//ออกจดหมายติดตามหนี้จำนวน ……. ครั้ง
		
		$this->libraries->template('debt/letter',$arr_data);
	}
	
	function save_debt_letter_backup_03_12_2018(){
		
		$where = "";
		if(@$_POST['non_pay_id']){
			$where = " AND non_pay_id = '{$_POST['non_pay_id']}'";
		}
		
		if(@$_POST['print_non_pay']){
			$where = " AND non_pay_id IN('".implode("','",@$_POST['print_non_pay'])."')";
		 
		}
		//echo $where.'<br>';
		//echo '<pre>'; print_r(@$_POST); echo '</pre>';
		//exit;
		$this->db->select(array('num_letter'));
		$this->db->from('coop_debt_letter_setting');
		$this->db->limit(1);
		$rs_num = $this->db->get()->result_array();		
		$num_letter = $rs_num[0]['num_letter'];//ออกจดหมายติดตามหนี้จำนวน ……. ครั้ง
		
		$this->db->select(array('*'));
		$this->db->from('coop_non_pay');
		$this->db->where("non_pay_status NOT IN ('0','2') {$where}");
		$rs = $this->db->get()->result_array();
		$runno = 0;
		$print_ref = 0;
		
		//หาเลขอ้างอิงการปริ้น
		$this->db->select(array('MAX(print_ref) AS last_print_ref'));
		$this->db->from('coop_debt_letter');
		$rs_max = $this->db->get()->result_array();
		$row_max = @$rs_max[0]; 			
		$print_ref = $row_max['last_print_ref']+1;

		$letter_month = date('n');
		$letter_year = date('Y')+543;
		$letter_date = date('Y-m-d H:i:s');
		$print_date = date('Y-m-d H:i:s');

		$i = 0;
		if(!empty($rs)){
			foreach($rs AS $key=>$row){
				//echo '<pre>'; print_r($row); echo '</pre>';
				$non_pay_id = @$row['non_pay_id'];			
				$member_id = @$row['member_id'];			
				
				//หาเลขรันครั้งที่
				$this->db->select(array('MAX(runno) AS last_run'));
				$this->db->from('coop_debt_letter');
				$this->db->where("non_pay_id = '{$non_pay_id}' AND letter_status = '1'");
				$rs_max = $this->db->get()->result_array();
				$row_max = @$rs_max[0]; 
			
				$runno = $row_max['last_run']+1;	
				if($runno <= $num_letter){
					$i++;

					/*
					//$letter_date = "2018-07-31";
					//$print_date = "2018-07-31";
					
					//$letter_date = "2018-08-31";
					//$print_date = "2018-08-31";
					
					$letter_date = "2018-09-30";
					$print_date = "2018-09-30";					
					$letter_month =  9;
					$letter_year = 2561;
					*/					
					
					$this->db->select(array('*'));
					$this->db->from('coop_non_pay');
					$this->db->where("non_pay_id = '{$non_pay_id}'");
					$rs_non_pay = $this->db->get()->result_array();	
					$row_non_pay = @$rs_non_pay[0];
					$non_pay_month = @$row_non_pay['non_pay_month'];
					$non_pay_year = @$row_non_pay['non_pay_year'];
				
					$this->db->select(array('*'));
					$this->db->from('coop_debt_letter');
					$this->db->where("non_pay_id = '{$non_pay_id}' AND letter_month = '{$letter_month}' AND letter_year = '{$letter_year}' AND letter_status = '1'");
					$row_check = $this->db->get()->result_array();
					if(empty($row_check) && ($letter_month >= $non_pay_month) && ($letter_year >= $non_pay_year)){
						
						$this->db->where("non_pay_id = '{$non_pay_id}' AND letter_month = '{$letter_month}' AND letter_year='{$letter_year}' AND letter_status = '0'");
						$this->db->delete("coop_debt_letter");	
						
						$data_insert = array();
						$data_insert['runno'] = @$runno;
						$data_insert['letter_date'] = @$letter_date;
						$data_insert['print_date'] = @$print_date ;
						$data_insert['non_pay_id'] = @$non_pay_id;
						$data_insert['letter_month'] = @$letter_month;
						$data_insert['letter_year'] = @$letter_year;
						$data_insert['print_ref'] = @$print_ref;
						$data_insert['member_id'] = @$member_id;
						$data_insert['letter_status'] = (@$_POST['action'] == 'save')?1:0;	
						//echo 'A';
						//echo '<pre>'; print_r($data_insert); echo '</pre>';	
						$this->db->insert('coop_debt_letter', $data_insert);
						$letter_id = @$this->db->insert_id();						
						
						$data_update = array();
						$non_pay_status = '3';
						$data_update['non_pay_status'] = @$non_pay_status;		
						if(@$_POST['action'] == 'save'){	
							$this->db->where('non_pay_id',$non_pay_id);
							$this->db->update('coop_non_pay', $data_update);
						}
					}							
					
				}
			}
		}	
		//exit;	
		
		if(!empty($letter_id)){
			if($i == 1){					
				$param = "";
				$param .= base64_encode("id=".@$letter_id);
				//พิมพ์จดหมาย 1 รายการ
				echo"<script> window.open('".PROJECTPATH."/debt/letter_perview?".@$param."','_blank') </script>";
			}else{
				$param = "";
				$param .= base64_encode("print_ref=".@$print_ref);
				//มากกว่า 1 รายการ			
				echo"<script> window.open('".PROJECTPATH."/debt/letter_perview?".@$param."','_blank') </script>";
			}
		}else{
			$param = "";
			$param .= base64_encode("non_pay=".implode("~",@$_POST['print_non_pay']));
			$param .= "&".base64_encode("letter_month=".@$letter_month);	
			$param .= "&".base64_encode("letter_year=".@$letter_year);
			$param .= "&".base64_encode("action=".@$_POST['action']);
			echo"<script> window.open('".PROJECTPATH."/debt/letter_perview?".@$param."','_blank') </script>";	
		}

		$get_param = '';
		if(!empty($_GET)){
			foreach($_GET AS $key=>$val){
				$get_param .= $key.'='.$val.'&';
			}
		}
		
		echo"<script> document.location.href='".PROJECTPATH."/debt/letter?".$get_param."' </script>";
		exit;
	}

	function save_debt_letter(){

		$where = "";
		if(@$_POST['non_pay_id']){
			$where = " AND non_pay_id = '{$_POST['non_pay_id']}'";
		}

		if(@$_POST['print_non_pay']){
			$where = " AND non_pay_id IN('".implode("','",@$_POST['print_non_pay'])."')";
		}

		$this->db->select(array('num_letter'));
		$this->db->from('coop_debt_letter_setting');
		$this->db->limit(1);
		$rs_num = $this->db->get()->result_array();
		$num_letter = $rs_num[0]['num_letter'];//ออกจดหมายติดตามหนี้จำนวน ……. ครั้ง

		$this->db->select(array('*'));
		$this->db->from('coop_non_pay');
		$this->db->where("non_pay_status NOT IN ('0','2') {$where}");
		$rs = $this->db->get()->result_array();
		$runno = 0;
		$print_ref = 0;

		//หาเลขอ้างอิงการปริ้น
		$this->db->select(array('MAX(print_ref) AS last_print_ref'));
		$this->db->from('coop_debt_letter');
		$rs_max = $this->db->get()->result_array();
		$row_max = @$rs_max[0];
		$print_ref = $row_max['last_print_ref']+1;

		$letter_month = date('n');
		$letter_year = date('Y')+543;
		$letter_date = date('Y-m-d H:i:s');
		$print_date = date('Y-m-d H:i:s');

		$i = 0;
		if(!empty($rs)){
			foreach($rs AS $key=>$row){
				$non_pay_id = @$row['non_pay_id'];
				$member_id = @$row['member_id'];

				$profile_month = $this->db->select("*")
									->from("coop_finance_month_profile")
									->where("profile_month = '".$row["non_pay_month"]."' AND profile_year = '".$row["non_pay_year"]."'")
									->get()->row();
				$profile_id = $profile_month->profile_id;

				//หาเลขรันครั้งที่
				$this->db->select(array('MAX(runno) AS last_run'));
				$this->db->from('coop_debt_letter');
				$this->db->where("non_pay_id = '{$non_pay_id}' AND letter_status = '1'");
				$rs_max = $this->db->get()->result_array();
				$row_max = @$rs_max[0];

				$runno = $row_max['last_run']+1;
				if($runno <= $num_letter){
					$i++;
					$this->db->select(array('*'));
					$this->db->from('coop_non_pay');
					$this->db->where("non_pay_id = '{$non_pay_id}'");
					$rs_non_pay = $this->db->get()->result_array();
					$row_non_pay = @$rs_non_pay[0];
					$non_pay_month = @$row_non_pay['non_pay_month'];
					$non_pay_year = @$row_non_pay['non_pay_year'];

					// $this->db->select(array('*'));
					// $this->db->from('coop_debt_letter');
					// $this->db->where("non_pay_id = '{$non_pay_id}' AND letter_month = '{$letter_month}' AND letter_year = '{$letter_year}' AND letter_status = '1'");
					// $row_check = $this->db->get()->result_array();
					if(empty($row_check) && ($letter_month >= $non_pay_month || $letter_year > $non_pay_year) && ($letter_year >= $non_pay_year)){

						$letter = $this->db->select("*")
											->from("coop_debt_letter")
											->where("non_pay_id = '{$non_pay_id}' AND letter_month = '{$letter_month}' AND letter_year='{$letter_year}' AND letter_status = '0'")
											->get()->row();
						if(!empty($letter)) {
							$this->db->where("non_pay_id = '{$non_pay_id}' AND letter_month = '{$letter_month}' AND letter_year='{$letter_year}' AND letter_status = '0'");
							$this->db->delete("coop_debt_letter");

							$this->db->where("letter_id = '".$letter->letter_id."'");
							$this->db->delete("coop_debt_letter_detail");
						}

						$data_insert = array();
						$data_insert['runno'] = @$runno;
						$data_insert['letter_date'] = @$letter_date;
						$data_insert['print_date'] = @$print_date ;
						$data_insert['non_pay_id'] = @$non_pay_id;
						$data_insert['letter_month'] = @$letter_month;
						$data_insert['letter_year'] = @$letter_year;
						$data_insert['print_ref'] = @$print_ref;
						$data_insert['member_id'] = @$member_id;
						$data_insert['letter_status'] = (@$_POST['action'] == 'save')?1:0;
						$this->db->insert('coop_debt_letter', $data_insert);
						$letter_id = @$this->db->insert_id();

						//Collect debt data for letter
						$non_pay_loans = $this->db->select(array('coop_non_pay_detail.non_pay_amount_balance',
																	'coop_non_pay_detail.member_id',
																	'coop_loan.contract_number',
																	'coop_loan.loan_amount',
																	'coop_loan.period_amount',
																	'coop_loan_type.loan_type',
																	'principal_detail.non_pay_amount as principal_debt',
																	'principal_detail.non_pay_amount_balance as principal_left',
																	'interest_detail.non_pay_amount as interest_debt',
																	'interest_detail.non_pay_amount_balance as interest_left',
																	'"LOAN" as type',
																	'coop_non_pay_detail.loan_id',
																	'coop_non_pay_detail.loan_atm_id',
																	'coop_non_pay.non_pay_month',
																	'coop_non_pay.non_pay_year',
																))
													->from('coop_non_pay_detail')
													->join('coop_non_pay_detail as principal_detail', 'coop_non_pay_detail.non_pay_id = principal_detail.non_pay_id AND
																										coop_non_pay_detail.loan_id = principal_detail.loan_id AND
																										principal_detail.pay_type = "principal"', "left")
													->join('coop_non_pay_detail as interest_detail', 'coop_non_pay_detail.non_pay_id = interest_detail.non_pay_id AND
																										coop_non_pay_detail.loan_id = interest_detail.loan_id AND
																										interest_detail.pay_type = "interest"', "left")
													->join("coop_loan","coop_non_pay_detail.loan_id = coop_loan.id","inner")
													->join("coop_loan_name","coop_loan.loan_type = coop_loan_name.loan_name_id","inner")
													->join("coop_loan_type","coop_loan_name.loan_type_id = coop_loan_type.id","inner")
													->join("coop_non_pay","coop_non_pay.non_pay_id = coop_non_pay_detail.non_pay_id","inner")
													->where("(coop_non_pay.non_pay_month < ".$row["non_pay_month"]." OR coop_non_pay.non_pay_year < ".$row["non_pay_year"].") AND coop_non_pay.non_pay_year <= ".$row["non_pay_year"]." AND coop_non_pay_detail.member_id = '".$member_id."'
																AND coop_non_pay_detail.deduct_code = 'LOAN' AND coop_non_pay_detail.non_pay_amount_balance > 0")
													->group_by('coop_non_pay_detail.loan_id, coop_non_pay.non_pay_month, coop_non_pay.non_pay_year')
													->get()->result_array();

						$non_pay_loan_atms = $this->db->select("coop_non_pay_detail.non_pay_amount_balance,
																		coop_non_pay_detail.loan_id,
																		coop_non_pay_detail.loan_atm_id,
																		coop_non_pay_detail.member_id,
																		coop_loan_atm.contract_number,
																		'เงินกู้ฉุกเฉิน' as loan_type,
																		principal_detail.non_pay_amount as principal_debt,
																		principal_detail.non_pay_amount_balance as principal_left,
																		interest_detail.non_pay_amount as interest_debt,
																		interest_detail.non_pay_amount_balance as interest_left,
																		'ATM' as type,
																		coop_non_pay.non_pay_month,
																	    coop_non_pay.non_pay_year"
																)
														->from('coop_non_pay_detail')
														->join('coop_non_pay_detail as principal_detail', 'coop_non_pay_detail.non_pay_id = principal_detail.non_pay_id AND
																											coop_non_pay_detail.loan_atm_id = principal_detail.loan_atm_id AND
																											principal_detail.pay_type = "principal"', "left")
														->join('coop_non_pay_detail as interest_detail', 'coop_non_pay_detail.non_pay_id = interest_detail.non_pay_id AND
																											coop_non_pay_detail.loan_atm_id = interest_detail.loan_atm_id AND
																											interest_detail.pay_type = "interest"', "left")
														->join("coop_loan_atm","coop_non_pay_detail.loan_atm_id = coop_loan_atm.loan_atm_id","left")
														->join("coop_non_pay","coop_non_pay.non_pay_id = coop_non_pay_detail.non_pay_id","inner")
														->where("(coop_non_pay.non_pay_month < ".$row["non_pay_month"]." OR coop_non_pay.non_pay_year < ".$row["non_pay_year"].") AND coop_non_pay.non_pay_year <= ".$row["non_pay_year"]." AND coop_non_pay_detail.member_id = '".$member_id."'
																AND coop_non_pay_detail.deduct_code = 'ATM' AND coop_non_pay_detail.non_pay_amount_balance > 0")
														->group_by('coop_non_pay_detail.loan_atm_id, coop_non_pay.non_pay_month, coop_non_pay.non_pay_year')
														->get()->result_array();

						$non_pays = array_merge($non_pay_loans, $non_pay_loan_atms);

						foreach($non_pays as $non_pay) {
							$where = "";
							$on = "";
							if($non_pay['type'] == "LOAN") {
								$where .= "t1.loan_id = '".$non_pay['loan_id']."'";
								$on .= "t1.loan_id = t2.loan_id";
							} else {
								$where .= "t1.loan_atm_id = '".$non_pay['loan_atm_id']."'";
								$on .= "t1.loan_atm_id = t2.loan_atm_id";
							}

							$month_detail = $this->db->select("t1.member_id,
																t1.pay_amount as principal_pay_amount,
																t1.real_pay_amount as principal_paid,
																t2.pay_amount as interest_pay_amount,
																t2.real_pay_amount as interest_paid")
														->from("(SELECT * FROM coop_finance_month_profile WHERE profile_month = '".$non_pay['non_pay_month']."' AND profile_year = '".$non_pay['non_pay_year']."') as t3")
														->join("coop_finance_month_detail as t1", "t1.profile_id = t3.profile_id", "inner")
														->join("coop_finance_month_detail as t2", $on." AND t1.profile_id = t2.profile_id AND t2.pay_type = 'interest'", "left")
														->where($where." AND t1.pay_type = 'principal'")
														->get()->row();

							$principal = 0;
							$interest = 0;
							if(!empty($month_detail)) {
								$principal = $month_detail->principal_pay_amount;
								$interest = $month_detail->interest_pay_amount;
							} else {
								$principal = $non_pay['principal_debt'];
								$interest = $non_pay['interest_debt'];
							}

							$data_insert = array();
							$data_insert['letter_id'] = $letter_id;
							$data_insert['type'] = $non_pay['type'];
							$data_insert['loan_id'] = $non_pay['loan_id'];
							$data_insert['loan_atm_id'] = $non_pay['loan_atm_id'];
							$data_insert['principal_debt'] = $non_pay['principal_left'];
							$data_insert['interest_debt'] = $non_pay['interest_left'];
							$data_insert['principal_paid'] = $principal - $non_pay['principal_left'];
							$data_insert['interest_paid'] = $interest - $non_pay['interest_left'];
							$data_insert['principal'] = $principal;
							$data_insert['interest'] = $interest;
							$data_insert['month'] = $non_pay['non_pay_month'];
							$data_insert['year'] = $non_pay['non_pay_year'];
							$this->db->insert('coop_debt_letter_detail', $data_insert);
						}

						//collect data for share
						$share_non_pays = $this->db->select("t1.non_pay_id, t1.non_pay_month, t1.non_pay_year, t2.non_pay_amount, t2.non_pay_amount_balance, t4.pay_amount")
													->from("coop_non_pay as t1")
													->join("coop_non_pay_detail as t2", "t1.non_pay_id = t2.non_pay_id AND t2.deduct_code = 'SHARE'", "inner")
													->join("coop_finance_month_profile as t3", "t1.non_pay_month = t3.profile_month AND t1.non_pay_year = t3.profile_year", "left")
													->join("coop_finance_month_detail as t4", "t3.profile_id = t4.profile_id AND t1.member_id = t4.member_id AND t4.deduct_code = 'SHARE'", "left")
													->where("(t1.non_pay_month < ".$row["non_pay_month"]." OR t1.non_pay_year < ".$row["non_pay_year"].") AND t1.non_pay_year <= ".$row["non_pay_year"]." AND t1.member_id = '".$member_id."'
																AND t2.non_pay_amount_balance > 0")
													->get()->result_array();

						foreach($share_non_pays as $share_non_pay) {
							$principal = 0;
							if(!empty($share_non_pay['pay_amount'])) {
								$principal = $share_non_pay['pay_amount'];
							} else {
								$principal = $share_non_pay['non_pay_amount'];
							}
							$data_insert = array();
							$data_insert['letter_id'] = $letter_id;
							$data_insert['type'] = "SHARE";
							$data_insert['principal_debt'] = $share_non_pay['non_pay_amount_balance'];
							$data_insert['principal_paid'] = $principal - $share_non_pay['non_pay_amount_balance'];
							$data_insert['principal'] = $principal;
							$data_insert['month'] = $share_non_pay['non_pay_month'];
							$data_insert['year'] = $share_non_pay['non_pay_year'];
							$this->db->insert('coop_debt_letter_detail', $data_insert);
						}

						//Collect data for non pay month
						$finance_months = $this->db->select("t1.deduct_code,
																t1.loan_id,
																t1.loan_atm_id,
																t1.pay_amount as principal,
																t1.real_pay_amount as principal_paid,
																t2.pay_amount as interest,
																t2.real_pay_amount as interest_paid")
													->from("coop_finance_month_detail as t1")
													->join("coop_finance_month_detail as t2", "t1.profile_id = t2.profile_id AND t1.deduct_code = t2.deduct_code AND t1.member_id = t2.member_id AND t2.pay_type = 'interest'", "left")
												 	->where("t1.profile_id = '".$profile_id."' AND t1.member_id = '".$member_id."' AND t1.deduct_code IN ('ATM', 'LOAN', 'SHARE') AND t1.pay_type = 'principal'")
													->get()->result_array();

						foreach($finance_months as $finance_month) {
							$data_insert = array();
							$data_insert['letter_id'] = $letter_id;
							$data_insert['type'] = $finance_month['deduct_code'];
							$data_insert['loan_id'] = $finance_month['loan_id'];
							$data_insert['loan_atm_id'] = $finance_month['loan_atm_id'];
							$data_insert['principal_debt'] = $finance_month['principal'] - $finance_month['principal_paid'];
							$data_insert['interest_debt'] = !empty($finance_month['interest']) ? $finance_month['interest'] - $finance_month['interest_paid'] : 0;
							$data_insert['principal_paid'] = $finance_month['principal_paid'];
							$data_insert['interest_paid'] = $finance_month['interest_paid'];
							$data_insert['principal'] = $finance_month['principal'];
							$data_insert['interest'] = $finance_month['interest'];
							$data_insert['month'] = $row['non_pay_month'];
							$data_insert['year'] = $row['non_pay_year'];
							$this->db->insert('coop_debt_letter_detail', $data_insert);
						}

						$data_update = array();
						$non_pay_status = '3';
						$data_update['non_pay_status'] = @$non_pay_status;
						if(@$_POST['action'] == 'save'){
							$this->db->where('non_pay_id',$non_pay_id);
							$this->db->update('coop_non_pay', $data_update);
						}
					}
				}
			}
		}

		if(!empty($letter_id)){
			if($i == 1){
				$param = "";
				$param .= base64_encode("id=".@$letter_id);
				//พิมพ์จดหมาย 1 รายการ
				echo"<script> window.open('".PROJECTPATH."/debt/debt_envelope?".@$param."','_blank') </script>";
				echo"<script> window.open('".PROJECTPATH."/debt/letter_perview?".@$param."','_blank') </script>";
			}else{
				$param = "";
				$param .= base64_encode("print_ref=".@$print_ref);
				//มากกว่า 1 รายการ
				echo"<script> window.open('".PROJECTPATH."/debt/debt_envelope?".@$param."','_blank') </script>";
				echo"<script> window.open('".PROJECTPATH."/debt/letter_perview?".@$param."','_blank') </script>";
			}
		}else{
			$param = "";
			$param .= base64_encode("non_pay=".implode("~",@$_POST['print_non_pay']));
			$param .= "&".base64_encode("letter_month=".@$letter_month);
			$param .= "&".base64_encode("letter_year=".@$letter_year);
			$param .= "&".base64_encode("action=".@$_POST['action']);
			echo"<script> window.open('".PROJECTPATH."/debt/debt_envelope?".@$param."','_blank') </script>";
			echo"<script> window.open('".PROJECTPATH."/debt/letter_perview?".@$param."','_blank') </script>";
		}

		$get_param = '';
		if(!empty($_GET)){
			foreach($_GET AS $key=>$val){
				$get_param .= $key.'='.$val.'&';
			}
		}

		echo"<script> document.location.href='".PROJECTPATH."/debt/letter?".$get_param."' </script>";
		exit;
	}

	function save_debt_resignation(){			
			$data_insert = array();
			$non_pay_id = @$_POST['non_pay_id'];
			$member_id = @$_POST['member_id'];
			
			$this->db->select('req_resign_no');
			$this->db->from('coop_mem_req_resign');
			$this->db->order_by('req_resign_id DESC');
			$this->db->limit(1);
			$row = $this->db->get()->result_array();
			if(!empty($row)){
				$req_resign_no = (int)$row[0]['req_resign_no']+1;
			}else{
				$req_resign_no = 1;
			}
			$req_resign_id = '';
			$data_insert['user_id'] = $_SESSION['USER_ID'];
			$data_insert['member_id'] = @$member_id;
			$data_insert['resign_cause_id'] = 7; //ให้ออกตามมติที่ประชุม = 7
			$data_insert['req_resign_no'] = sprintf('% 06d',$req_resign_no);
			$data_insert['req_resign_status'] = '1';
			$data_insert['req_resign_date'] = date('Y-m-d H:i:s');
			$data_insert['resign_date'] = date('Y-m-d H:i:s');
			$data_insert['approve_date'] = date('Y-m-d H:i:s');
			$data_insert['approve_user_id'] = $_SESSION['USER_ID'];			
			$this->db->insert('coop_mem_req_resign', $data_insert);
			$req_resign_id = $this->db->insert_id();
			//echo '<pre>'; print_r($data_insert); echo '</pre>';

			$data_member = array();
			$data_member['member_status'] = '2';
			$data_member['mem_type'] = '2';
			$this->db->where('member_id', @$member_id);
			$this->db->update('coop_mem_apply', $data_member);
			//echo '<pre>'; print_r($data_member); echo '</pre>';
			
			$data_update = array();
			$data_update['non_pay_status'] = 6;					
			$this->db->where('non_pay_id',$non_pay_id);
			$this->db->update('coop_non_pay', $data_update);
			
			if(!empty($req_resign_id)){
				$this->db->select(array('*'));
				$this->db->from('coop_mem_req_resign');
				$this->db->where("req_resign_id = '".$req_resign_id."'");
				$row_resign = $this->db->get()->result_array();
				$resign_date = @$row_resign[0]['resign_date'];
				$data = "ให้ออกจากการเป็นสมาชิกแล้วเมื่อวันที่ ".$this->center_function->ConvertToThaiDate($resign_date,1,0);
			}else{
				$data = "";
			}
			echo $data;
	}
	
	function check_debt_resignation(){	
			$member_id = @$_POST['member_id'];
			
			$this->db->select('resign_date');
			$this->db->from('coop_mem_req_resign');
			$this->db->where("member_id = '".$member_id."'");
			$this->db->order_by('resign_date DESC');
			$this->db->limit(1);
			$row_resign = $this->db->get()->result_array();
			$resign_date = @$row_resign[0]['resign_date'];
			if(empty($resign_date)){
				$data = "";
			}else{
				$data = " ให้ออกจากการเป็นสมาชิกแล้วเมื่อวันที่ ".$this->center_function->ConvertToThaiDate($resign_date,1,0);
			}
			echo $data;
	}
	
	public function debt_pay(){
		$arr_data = array();
		$where = "";
		$member_id = @$_GET['id'];
		if(@$member_id){
			$where = " AND coop_non_pay.member_id = '{$member_id}'";
		}	
			$this->db->select(array('*'));
			$this->db->from('coop_mem_apply');
			$this->db->where("member_id = '{$member_id}'");
			$rs = $this->db->get()->result_array();
			$arr_data['row_member'] = @$rs[0];
		
			$x=0;
			$join_arr = array();
			$join_arr[$x]['table'] = 'coop_mem_apply';
			$join_arr[$x]['condition'] = 'coop_non_pay.member_id = coop_mem_apply.member_id';
			$join_arr[$x]['type'] = 'left';
			
			$x++;
			$join_arr[$x]['table'] = 'coop_user';
			$join_arr[$x]['condition'] = 'coop_non_pay.pay_admin_id = coop_user.user_id';
			$join_arr[$x]['type'] = 'left';	

			//$this->paginater_all->debug = true;
			$this->paginater_all->field_count("coop_non_pay.non_pay_id");
			$this->paginater_all->type(DB_TYPE);
			$this->paginater_all->select(array('coop_non_pay.*','coop_mem_apply.firstname_th','coop_mem_apply.lastname_th','coop_user.user_name'));
			$this->paginater_all->main_table('coop_non_pay');
			$this->paginater_all->where("non_pay_status <> '0' {$where}");
			$this->paginater_all->page_now(@$_GET["page"]);
			$this->paginater_all->per_page(50);
			$this->paginater_all->page_link_limit(20);
			$this->paginater_all->order_by('non_pay_id DESC');
			$this->paginater_all->join_arr($join_arr);
			$row = $this->paginater_all->paginater_process();
			//echo"<pre>";print_r($row);exit;
			$paging = $this->pagination_center->paginating($row['page'], $row['num_rows'], $row['per_page'], $row['page_link_limit'],$_GET);//$page_now = 1, $row_total = 1, $per_page = 20, $page_limit = 20
			$i = $row['page_start'];
			// echo $this->db->last_query();exit;
			foreach($row['data'] as $key => $value){
				
				$rs_pay_receipt = $this->db->select(array('coop_non_pay_receipt.receipt_id', 'receipt_status', 'updatetime', 'user_name', 'coop_mem_req_resign.req_resign_id'))

										->from('coop_non_pay_receipt')
										->join('coop_user', 'coop_user.user_id = coop_non_pay_receipt.user_id','left')
										->join('coop_mem_req_resign', 'coop_non_pay_receipt.receipt_id = coop_mem_req_resign.receipt_id','left')
										->where("non_pay_id = '".@$value['non_pay_id']."' AND coop_non_pay_receipt.member_id = '".@$value['member_id']."'")
										->get()->result_array();

				foreach($rs_pay_receipt AS $key_receipt=>$row_pay_receipt){
					$row['data'][$key]['receipt_id'][] = @$row_pay_receipt;
				}
			}
			
		
			$arr_data['num_rows'] = $row['num_rows'];
			$arr_data['paging'] = $paging;
			$arr_data['row'] = $row['data'];
			$arr_data['i'] = $i;
		//}
		
		$arr_data['month_arr'] = array('1'=>'มกราคม','2'=>'กุมภาพันธ์','3'=>'มีนาคม','4'=>'เมษายน','5'=>'พฤษภาคม','6'=>'มิถุนายน','7'=>'กรกฎาคม','8'=>'สิงหาคม','9'=>'กันยายน','10'=>'ตุลาคม','11'=>'พฤศจิกายน','12'=>'ธันวาคม');
		
		$this->libraries->template('debt/debt_pay',$arr_data);
	}
	
	function get_search_member_debt(){
		$where = "
		 	(coop_mem_apply.member_id LIKE '%".$this->input->post('search_text')."%'
		 	OR coop_mem_apply.firstname_th LIKE '%".$this->input->post('search_text')."%'
			OR coop_mem_apply.lastname_th LIKE '%".$this->input->post('search_text')."%') 
			AND coop_mem_apply.member_status = '1' AND coop_non_pay.non_pay_status <> '0'
		";
		$this->db->select(array('coop_mem_apply.id','coop_mem_apply.member_id','coop_mem_apply.firstname_th','coop_mem_apply.lastname_th','coop_mem_apply.apply_date','coop_mem_apply.mem_apply_id'));
		$this->db->from('coop_non_pay');
		$this->db->join("coop_mem_apply","coop_non_pay.member_id = coop_mem_apply.member_id","left");		
		$this->db->where($where);
		$this->db->group_by('coop_mem_apply.member_id');
		$this->db->order_by('coop_mem_apply.mem_apply_id DESC');
		$row = $this->db->get()->result_array();
		$arr_data['data'] = $row;
		$arr_data['form_target'] = $this->input->post('form_target');
		//echo $this->db->last_query(); exit;
		//echo"<pre>";print_r($arr_data['data']);exit;
		$this->load->view('debt/get_search_member_debt',$arr_data);
	}
	
	
	function get_non_pay_detail(){
		$non_pay_id = @$_POST['non_pay_id'];
		
		$this->db->select(array('coop_non_pay_detail.*','coop_deduct.deduct_detail','coop_loan.contract_number'));
		$this->db->from('coop_non_pay_detail');
		//$this->db->join("coop_deduct","coop_non_pay_detail.deduct_code = coop_deduct.deduct_code AND coop_deduct.deduct_code <> 'LOAN'","left");
		$this->db->join("coop_deduct","coop_non_pay_detail.deduct_code = coop_deduct.deduct_code AND coop_deduct.deduct_code NOT IN ('LOAN','GUARANTEE','ATM')","left");
		$this->db->join("coop_loan","coop_non_pay_detail.loan_id = coop_loan.id","left");
		$this->db->where("coop_non_pay_detail.non_pay_id = {$non_pay_id} AND non_pay_amount_balance <> 0");
		$this->db->order_by('coop_non_pay_detail.run_id ASC');
		$rs = $this->db->get()->result_array();

		//echo $this->db->last_query(); exit;
		$row = @$rs;
		$i=1;
		$pay_amount_balance_all = 0;
		$pay_amount_all = 0;
		$data = '';
		if(!empty($row)){
			foreach(@$row as $key => $row_debt){
				$text_pay_loan_type = ($row_debt['pay_type'] == 'interest')?'ดอกเบี้ยเงินกู้เลขที่สัญญา':'ต้นเงินกู้เลขที่สัญญา';
				$text_pay_guarantee_type = ($row_debt['pay_type'] == 'interest')?'ดอกเบี้ยสัญญาในฐานะผู้ค้้ำประกัน':'ต้นเงินสัญญาในฐานะผู้ค้้าประกัน';
				$text_pay_atm_type = ($row_debt['pay_type'] == 'interest')?'ดอกเบี้ยเงินกู้ฉุกเฉิน ATM':'ต้นเงินสัญญากู้ฉุกเฉิน ATM';
				
				$deduct_detail = '';
				if($row_debt['deduct_code'] == 'LOAN'){
					$deduct_detail = $text_pay_loan_type.'  '.$row_debt['contract_number'];
				}else if($row_debt['deduct_code'] == 'GUARANTEE'){
					$deduct_detail = $text_pay_guarantee_type.'  '.$row_debt['contract_number'];
				}else if($row_debt['deduct_code'] == 'ATM'){
						$deduct_detail = $text_pay_atm_type;
				}else{
					$deduct_detail = $row_debt['deduct_detail'];	
				}	
			
			$data .= '
					<tr> 
						<td>'.@$i.'</td>
						<td class="text-left">
						<input type="hidden" class="form-control" name="deduct_detail['.$row_debt['run_id'].']" id="deduct_detail['.$row_debt['run_id'].']" value="'.$deduct_detail.'">
						'.$deduct_detail.'
						</td>
						<td class="text-right">
							'.number_format($row_debt['non_pay_amount_balance'],2).'
							<input type="hidden" class="form-control" name="debt_amount['.$row_debt['run_id'].']" id="pay_debt_amount_'.$row_debt['run_id'].'_old" value="'.number_format($row_debt['non_pay_amount_balance'],2).'" >
						</td>										
						<td class="text-right">
							<input type="text" class="form-control" name="pay_debt_amount['.$row_debt['run_id'].']" id="pay_debt_amount_'.$row_debt['run_id'].'" value="'.number_format($row_debt['non_pay_amount_balance'],2).'"  data="'.number_format($row_debt['non_pay_amount_balance'],2).'"  onkeyup="sum_pay_amount_all(this)">
						</td>										
					</tr>
					';							
		
				$i++; 
				$pay_amount_balance_all += @$row_debt['non_pay_amount_balance'];
				$pay_amount_all += @$row_debt['non_pay_amount_balance'];
			}
		}else{
			$data .= '<tr><td colspan="4">ไม่พบข้อมูล</td></tr>';
		}
					   
			$data .= '<tr style="background-color: #e0e0e0;"> 
							<th colspan="2">รวม</th>									
							<th class="text-right">'.number_format($pay_amount_balance_all,2).'</th>									
							<th class="text-right"><span id="pay_amount_all">'.number_format($pay_amount_all,2).'</span></th>									
				   </tr>';
		echo $data;
		exit();
	}
	
	function save_debt_pay(){
		$this->db->select(array('deduct_code','account_list_id'));
		$this->db->from('coop_deduct');
		$this->db->group_by("deduct_code,account_list_id");
		$rs_deduct = $this->db->get()->result_array();
		$arr_deduct = array();
		foreach($rs_deduct AS $key=>$row_deduct){	
			$arr_deduct[$row_deduct['deduct_code']] = $row_deduct['account_list_id'];
		}
		
		$non_pay_id = @$_POST['non_pay_id'];
		$pay_debt_amount = @$_POST['pay_debt_amount'];
		$transaction_text = @$_POST['deduct_detail'];
		//echo '<pre>'; print_r($_POST); echo '</pre>';
		//exit;
		$non_pay_amount_balance = 0;
		$pay_amount_balance = 0;
		$non_pay_amount = 0;
		$pay_amount_balance_total = 0;
		$arr_pay_debt_amount = array();
		$arr_insert_account_transaction = array();
		foreach($pay_debt_amount AS $key=>$value){			
			$run_id = @$key;
			$pay_amount = str_replace(',','',@$value);
			
			$this->db->select(array('non_pay_amount_balance','deduct_code','member_id','loan_id','loan_atm_id','pay_type','finance_month_profile_id', 'deposit_account_id', 'cremation_type_id'));
			$this->db->from('coop_non_pay_detail');
			$this->db->where("run_id = '{$run_id}'");
			$rs_detail = $this->db->get()->result_array();
			$row_detail = @$rs_detail[0];
			//echo $this->db->last_query();
			if(!empty($row_detail)){
				$pay_amount_balance =  @$row_detail['non_pay_amount_balance'] - @$pay_amount;
				
				$data_update_non_detail = array();
				$data_update_non_detail['non_pay_amount_balance'] = @$pay_amount_balance;
				$this->db->where("run_id = '{$run_id}'");
				$this->db->update('coop_non_pay_detail', $data_update_non_detail);	
				$non_pay_amount_balance += $pay_amount_balance;
				$pay_amount_balance_total += @$pay_amount;
				$finance_month_profile_id = @$row_detail['finance_month_profile_id'];
				
				//ADD บัญชีเงินฝาก-------------------------
				$account_id									= $row_detail['deposit_account_id'];
				// $this->db->where("account_id", );		
				// $lastTransaction							= $this->db->get_where("coop_account_transaction", array("account_id" => $account_id) )->result_array()[0];
				if($account_id!=""){
					$data_insert 								= array();
					$data_insert['account_id'] 					= $account_id;
					$data_insert['transaction_time'] 			= @date("Y-m-d H:i:s");	
					$data_insert['transaction_list'] 			= "DEPP";
					$data_insert['transaction_withdrawal'] 		= '0';
					$data_insert['transaction_deposit'] 		= @$pay_amount;
					$data_insert['user_id'] 					= $_SESSION['USER_ID'];
					array_push($arr_insert_account_transaction, $data_insert);
				}
				
				//ADD บัญชีเงินฝาก-------------------------
			}
			
			$arr_pay_debt_amount[$key]['deduct_code'] = $row_detail['deduct_code'];
			$arr_pay_debt_amount[$key]['member_id'] = $row_detail['member_id'];
			$arr_pay_debt_amount[$key]['amount'] = $pay_amount;
			$arr_pay_debt_amount[$key]['loan_id'] = $row_detail['loan_id'];
			$arr_pay_debt_amount[$key]['loan_atm_id'] = $row_detail['loan_atm_id'];
			$arr_pay_debt_amount[$key]['pay_type'] = $row_detail['pay_type'];
			$arr_pay_debt_amount[$key]['principal_payment'] = ($row_detail['pay_type']=='principal')?$pay_amount:0;
			$arr_pay_debt_amount[$key]['interest'] = ($row_detail['pay_type']=='interest')?$pay_amount:0;
			$arr_pay_debt_amount[$key]['transaction_text'] = $transaction_text[$key];
			$arr_pay_debt_amount[$key]['cremation_type_id'] = $row_detail['cremation_type_id'];
			$arr_pay_debt_amount[$key]['deposit_account_id'] = $row_detail['deposit_account_id'];
		}
		
		$data_update_non = array();
		$data_update_non['non_pay_amount_balance'] = @$non_pay_amount_balance;
		$data_update_non['updatetimestamp'] = date('Y-m-d H:i:s');
		$data_update_non['pay_admin_id'] = $_SESSION['USER_ID'];

		$this->db->where('non_pay_id', $non_pay_id);
		$this->db->update('coop_non_pay', $data_update_non);	
		
		$this->db->select(array('*'));
		$this->db->from('coop_non_pay');
		$this->db->where("non_pay_id = '{$non_pay_id}'");
		$rs = $this->db->get()->result_array();
		$row = @$rs[0];
		
		$member_id = @$row['member_id'];
		$non_pay_month = @$row['non_pay_month'];
		$non_pay_year = @$row['non_pay_year'];
		if(@$row['non_pay_amount_balance'] == '0'){
			$data_update = array();
			$data_update['non_pay_status'] = '2';
			$this->db->where('non_pay_id', $non_pay_id);
			$this->db->update('coop_non_pay', $data_update);
		}
		
		$yymm_check = (date("Y")+543).date("m");
		$yymm_check = date("m");
		$yy_check = (date("Y")+543);
		// $yy_full = (date("Y")+543);
		// $yy_check = substr($yy_check,2);
		$last_date_of_month = date("Y-m-t", strtotime(date(($non_pay_year - 543).'-'.sprintf('%02d', $non_pay_month).'-01')));

		/* 2021-03-12 change receipt format to use current month as first 2 letter. */
		// $yymm = sprintf("%02d", $non_pay_month);
		$yymm = sprintf("%02d", date("m"));
		$yy_full = (date("Y")+543);
		$yy = substr($non_pay_year,2);

		$receipt_number = "";
		$pay_type = $_POST['pay_type'] == "cash" ? 0 : 1;
		$receipt_number = $this->receipt_model->generate_receipt(date('Y-m-d H:i:s'), $pay_type);
		$text = "C";

		$data_insert = array();
		$data_insert['member_id'] = $member_id;
		$data_insert['non_pay_id'] = $non_pay_id;
		$data_insert['receipt_id'] = $receipt_number;
		$data_insert['createdatetime'] = date('Y-m-d H:i:s');
		$this->db->insert('coop_non_pay_receipt', $data_insert);
		
		//add saving
		if(count($arr_insert_account_transaction)>=1){
			foreach ($arr_insert_account_transaction as $key => $value) {
				$account_id = $value['account_id'];
				$pay_amount = $value['transaction_deposit'];
				$this->db->set("receipt_id", $receipt_number);
				$this->db->set("transaction_balance", "(SELECT transaction_balance+{$pay_amount} FROM coop_account_transaction AS t1 WHERE account_id = '{$account_id}' ORDER BY transaction_time DESC, transaction_id DESC LIMIT 1)", false);
				$this->db->set("transaction_no_in_balance", "(SELECT transaction_no_in_balance+{$pay_amount} FROM coop_account_transaction AS t2 WHERE account_id = '{$account_id}' ORDER BY transaction_time DESC, transaction_id DESC LIMIT 1)", false);
				
				//ดึงข้อมูลลำดับรายการ ของรายการถัดไป
				$arr_seq = array(); 
				$arr_seq['account_id'] = $account_id;
				$arr_seq['transaction_list'] = $value['transaction_list']; 
				$seq_no = $this->deposit_seq->gen_seq_account_transaction($arr_seq);
				$this->db->set("seq_no", @$seq_no);
				
				$this->db->insert("coop_account_transaction", $value);
			}
		}
		//add saving
		if($_POST["pay_type"]=="cash"){
            $_POST["pay_type"] = 0;
        }else if($_POST["pay_type"]=="transfer"){
            $_POST["pay_type"] = 1;
        }else if($_POST["pay_type"]=="cheque"){
            $_POST["pay_type"] = 2;
        }else{
            $_POST["pay_type"] = 3;
        }
		$data_insert = array();
		$data_insert['receipt_id'] = $receipt_number;
		$data_insert['member_id'] = $member_id;
		$total = @$pay_amount_balance_total;

		$data_insert['receipt_code'] = $text;
		$data_insert['pay_type'] = @$_POST['pay_type'];
		$data_insert['sumcount'] = number_format($total,2,'.','');
		$data_insert['receipt_datetime'] = date("Y-m-d H:i:s");
		$data_insert['admin_id'] = $_SESSION['USER_ID'];
		$data_insert['month_receipt'] = $non_pay_month;
		$data_insert['year_receipt'] = $non_pay_year;
		$data_insert['order_by'] = @$order_by_id;
		$data_insert['finance_month_profile_id'] = $finance_month_profile_id;
        $data_insert['cheque_no'] = $_POST["cheque_no"];
        $data_insert['bank_id'] = $_POST["bank_id"];
        $data_insert['branch_code'] = $_POST["branch_code"];
        $data_insert['local_account_id'] = $_POST["local_account_id"];
        $data_insert['other'] = $_POST["other"];
        $data_insert['transfer_other'] = $_POST["transfer_other"];
		$this->db->insert('coop_receipt', $data_insert);

		$data = array();
		$data['coop_account']['account_description'] = "รายการรับชำระเงิน";
		$data['coop_account']['account_datetime'] = date('Y-m-d H:i:s');
		
		$data['coop_account_detail'][10100]['account_type'] = 'debit';
		$data['coop_account_detail'][10100]['account_amount'] = $total;
		$data['coop_account_detail'][10100]['account_chart_id'] = '10100';
		
		$loan_amount_balance = 0;
		//echo '<pre>'; print_r($arr_pay_debt_amount); echo '</pre>';
		foreach($arr_pay_debt_amount AS $key=>$data_post){	
			if(@$data_post['amount'] > 0 && !empty($data_post['amount'])){
				$account_list_id = $arr_deduct[$data_post['deduct_code']];
				$data_insert = array();
				$data_insert['receipt_id'] = $receipt_number;
				$data_insert['receipt_list'] = $account_list_id;
				$data_insert['receipt_count'] = number_format($data_post['amount'],2,'.','');
				$this->db->insert('coop_receipt_detail', $data_insert);
				
				if($data_post['deduct_code']=='LOAN' && $data_post['pay_type'] == 'principal'){
					$this->db->select('loan_amount_balance');
					$this->db->from('coop_loan');
					$this->db->where("id = '".$data_post['loan_id']."'");
					$row = $this->db->get()->result_array();
					$row_loan = @$row[0];
					
					$loan_amount_balance = @$row_loan['loan_amount_balance'] - $data_post['principal_payment'];
					if($loan_amount_balance<=0){
						// $loan_amount_balance = 0;
						$data_insert = array();
						$data_insert['loan_amount_balance'] = $loan_amount_balance;
						$data_insert['loan_status'] = '4';
						$this->db->where('id', $data_post['loan_id']);
						$this->db->update('coop_loan', $data_insert);
					}else{
						$data_insert = array();
						$data_insert['loan_amount_balance'] = number_format($loan_amount_balance,2,'.','');
						$this->db->where('id', $data_post['loan_id']);
						$this->db->update('coop_loan', $data_insert);
					}

					//Calculate non_pay_amount_balance of relate loan_id
					$non_pay_details = $this->db->select("run_id, non_pay_id")
												->from("coop_non_pay_detail")
												->where("loan_id = '".$data_post['loan_id']."' AND pay_type = 'principal' AND non_pay_amount_balance > '".$loan_amount_balance."'")
												->get()->result_array();

					foreach($non_pay_details as $non_pay_detail) {
						$data_insert = array();
						$data_insert['non_pay_amount_balance'] = $loan_amount_balance;
						$this->db->where('run_id', $non_pay_detail["run_id"]);
						$this->db->update('coop_non_pay_detail', $data_insert);

						$details = $this->db->select("sum(non_pay_amount_balance) as sum")
											->from("coop_non_pay_detail")
											->where("non_pay_id = '".$non_pay_detail["non_pay_id"]."'")
											->get()->result_array();
						$data_insert = array();
						if($details[0]['sum'] == 0) {
							$data_insert["non_pay_status"] = 2;
						}
						$data_insert["non_pay_amount_balance"] = $details[0]['sum'];
						$this->db->where('non_pay_id', $non_pay_detail["non_pay_id"]);
						$this->db->update('coop_non_pay', $data_insert);
					}

					$data_insert = array();
					$data_insert['loan_id'] = $data_post['loan_id'];
					$data_insert['loan_amount_balance'] = number_format($loan_amount_balance,2,'.','');

					$data_insert['transaction_datetime'] = date("Y-m-d H:i:s");
					$data_insert['receipt_id'] = $receipt_number;
					$this->db->insert('coop_loan_transaction', $data_insert);
				}else if($data_post['deduct_code']=='ATM' && $data_post['pay_type'] == 'principal'){
					$this->db->select(array(
						'loan_id',
						'loan_amount_balance'
					));
					$this->db->from('coop_loan_atm_detail');
					$this->db->where("loan_atm_id = '".$data_post['loan_atm_id']."' AND loan_status = '0'");
					$this->db->order_by('loan_id ASC');
					$row = $this->db->get()->result_array();
					$principal_payment = $data_post['principal_payment'];
					foreach($row as $key_atm => $value_atm){
						if($principal_payment > 0){
							if($principal_payment >= $value_atm['loan_amount_balance']){
								$data_insert = array();
								$data_insert['loan_amount_balance'] = 0;
								$data_insert['loan_status'] = '1';
								$data_insert['date_last_pay'] = date("Y-m-d H:i:s");
								$this->db->where('loan_id', $value_atm['loan_id']);
								$this->db->update('coop_loan_atm_detail', $data_insert);
								$principal_payment = $principal_payment - $value_atm['loan_amount_balance'];
							}else{
								$data_insert = array();
								$data_insert['loan_amount_balance'] = $value_atm['loan_amount_balance']-$principal_payment;
								$data_insert['date_last_pay'] = date("Y-m-d H:i:s");
								$this->db->where('loan_id', $value_atm['loan_id']);
								$this->db->update('coop_loan_atm_detail', $data_insert);
								$principal_payment = 0;
							}
						}
					}
					$this->db->select(array(
						'total_amount_approve',
						'total_amount_balance',
						'contract_number'
					));
					$this->db->from('coop_loan_atm');
					$this->db->where("loan_atm_id = '".$data_post['loan_atm_id']."'");
					$row = $this->db->get()->result_array();
					$row_loan_atm = $row[0];
					
					$total_amount_balance = $row_loan_atm['total_amount_balance']+$data_post['principal_payment'];
					$data_insert = array();
					$data_insert['total_amount_balance'] = $total_amount_balance;
					$this->db->where('loan_atm_id', $data_post['loan_atm_id']);
					$this->db->update('coop_loan_atm', $data_insert);
					
					$loan_amount_balance = $row_loan_atm['total_amount_approve'] - $total_amount_balance;
					
					$atm_transaction = array();
					$atm_transaction['loan_atm_id'] = $data_post['loan_atm_id'];
					$atm_transaction['loan_amount_balance'] = $loan_amount_balance;
					$atm_transaction['transaction_datetime'] = date("Y-m-d H:i:s");
					$atm_transaction['receipt_id'] = $receipt_number;
					$this->loan_libraries->atm_transaction($atm_transaction);

					//Calculate non_pay_amount_balance of relate loan_atm_id
					$non_pay_details = $this->db->select("run_id, non_pay_id")
												->from("coop_non_pay_detail")
												->where("loan_atm_id = '".$data_post['loan_atm_id']."' AND pay_type = 'principal' AND non_pay_amount_balance > '".$loan_amount_balance."'")
												->get()->result_array();

					foreach($non_pay_details as $non_pay_detail) {
						$data_insert = array();
						$data_insert['non_pay_amount_balance'] = $loan_amount_balance;
						$this->db->where('run_id', $non_pay_detail["run_id"]);
						$this->db->update('coop_non_pay_detail', $data_insert);

						$details = $this->db->select("sum(non_pay_amount_balance) as sum")
											->from("coop_non_pay_detail")
											->where("non_pay_id = '".$non_pay_detail["non_pay_id"]."'")
											->get()->result_array();
						$data_insert = array();
						if($details[0]['sum'] == 0) {
							$data_insert["non_pay_status"] = 2;
						}
						$data_insert["non_pay_amount_balance"] = $details[0]['sum'];
						$this->db->where('non_pay_id', $non_pay_detail["non_pay_id"]);
						$this->db->update('coop_non_pay', $data_insert);
					}
				}else if($data_post['deduct_code']=='SHARE'){
					$this->db->select(array('share_collect','share_collect_value'));
					$this->db->from('coop_mem_share');
					$this->db->where("member_id = '".$data_post['member_id']."' AND share_status = '1'");
					$this->db->order_by("share_date DESC,share_id DESC");
					$this->db->limit(1);
					$row = $this->db->get()->result_array();
					$row_share = $row[0];
					
					$this->db->select('*');
					$this->db->from('coop_share_setting');
					$this->db->order_by('setting_id DESC');
					$row = $this->db->get()->result_array();
					$share_value = $row[0]['setting_value'];
					
					$data_insert = array();
					$data_insert['member_id'] = $data_post['member_id'];
					$data_insert['admin_id'] = $_SESSION['USER_ID'];
					$data_insert['share_type'] = 'SDP';//ชำระหนี้คงค้าง
					$data_insert['share_date'] = date("Y-m-d H:i:s");
					$data_insert['share_status'] = '1';
					$data_insert['share_payable'] = @$row_share['share_collect'];
					$data_insert['share_payable_value'] = @$row_share['share_collect_value'];
					$data_insert['share_early'] = @$data_post['principal_payment']/10;
					$data_insert['share_early_value'] = @$data_post['principal_payment'];
					$data_insert['share_collect'] = @$row_share['share_collect']+(@$data_post['principal_payment']/10);
					$data_insert['share_collect_value'] = @$row_share['share_collect_value']+@$data_post['principal_payment'];
					$data_insert['share_bill'] = $receipt_number;
					$data_insert['share_bill_date'] = date("Y-m-d H:i:s");;
					$data_insert['share_value'] = $share_value;
					$data_insert['pay_type'] = '0';
					
					$this->db->insert('coop_mem_share', $data_insert);
					$loan_amount_balance = @$row_share['share_collect_value']+@$data_post['principal_payment'];
				} else if ($data_post['deduct_code']=='CREMATION'){
					if($data_post['cremation_type_id'] == '2') {
						$cremation_2_detail = $this->db->select("*")
														->from("coop_setting_cremation_detail")
														->where("start_date <= '".date("Y-m-d H:i:s")."' AND cremation_id = '2'")
														->order_by("start_date")
														->limit(1)
														->get()->result_array();
						$cremation_2_detail = $cremation_2_detail[0];
						$pay_amount = $data_post['amount'];
						$cremations = $this->db->select("t1.cremation_request_id, t1.member_cremation_id, t2.adv_id, t2.adv_payment_balance")
												->from("coop_cremation_request as t1")
												->join("coop_cremation_advance_payment as t2", "t1.member_cremation_id = t2.member_cremation_id", "inner")
												->where("t1.member_id = '".$member_id."' AND t1.cremation_status in ('6')")
												->get()->result_array();
						$advance_diff = 0;
						foreach($cremations as $cremation) {
							if($cremation["adv_payment_balance"] < $cremation_2_detail["advance_pay"] && $pay_amount > 0) {
								$data_insert = array();
								$deduct_cremation = $cremation_2_detail["advance_pay"] - $cremation["adv_payment_balance"];
								if ($deduct_cremation <= $pay_amount) {
										$data_insert['adv_payment_balance'] = $cremation_2_detail["advance_pay"];
										$transaction_amount = $deduct_cremation;
								} else {
										$data_insert['adv_payment_balance'] = $cremation["adv_payment_balance"] + $pay_amount;
										$transaction_amount = $pay_amount;
								}
								$pay_amount = $pay_amount - $deduct_cremation;
								$this->db->where('adv_id', $cremation['adv_id']);
								$this->db->update('coop_cremation_advance_payment', $data_insert);

								$transaction_insert = array();
								$transaction_insert["non_pay_id"] = $$non_pay_id;
								$transaction_insert["member_cremation_id"] = $cremation["member_cremation_id"];
								$transaction_insert["type"] = "CNP";
								$transaction_insert["total"] = $data_insert['adv_payment_balance'];
								$transaction_insert["amount"] = $transaction_amount;
								$transaction_insert["status"] = 1;
								$transaction_insert["created_at"] = date("Y-m-d H:i:s");
								$transaction_insert["updated_at"] = date("Y-m-d H:i:s");
								$this->db->insert('coop_cremation_advance_payment_transaction', $transaction_insert);
								$cremation_transaction_id = $this->db->insert_id();

								//Insert cremation receipt transaction
								$transaction_receipt_insert = array();
								$transaction_receipt_insert["transaction_id"] = $cremation_transaction_id;
								$transaction_receipt_insert["receipt_id"] = $receipt_number;
								$transaction_receipt_insert["status"] = 1;
								$transaction_receipt_insert["amount"] = $transaction_amount;
								$transaction_receipt_insert["created_at"] = date("Y-m-d H:i:s");
								$transaction_receipt_insert["updated_at"] = date("Y-m-d H:i:s");
								$this->db->insert('coop_cremation_advance_payment_receipt', $transaction_receipt_insert);

								//reduce debt
								$cremation_debts = $this->db->select("id, debt")
															->from("coop_cremation_advance_payment_transaction")
															->where("member_cremation_id = '".$cremation["member_cremation_id"]."' AND status = 1 AND debt > 0")
															->order_by("created_at")
															->get()->result_array();
								$transaction_updates = array();
								foreach($cremation_debts as $cremation_debt) {
									$transaction_update = array();
									$transaction_update["id"] = $cremation_debt["id"];
									$debt_balance = 0;
									if($transaction_amount >= $cremation_debt["debt"]) {
										$transaction_amount -= $cremation_debt["debt"];
									} else {
										$debt_balance = $cremation_debt["debt"] - $transaction_amount;
										$transaction_amount = 0;
									}
									$transaction_update["debt"] = $debt_balance;
									$transaction_update["updated_at"] = date("Y-m-d H:i:s");
									$transaction_updates[] = $transaction_update;

									//Insert cremation receipt transaction
									$transaction_receipt_insert = array();
									$transaction_receipt_insert["transaction_id"] = $cremation_debt["id"];
									$transaction_receipt_insert["receipt_id"] = $receipt_number;
									$transaction_receipt_insert["status"] = 1;
									$transaction_receipt_insert["amount"] = $transaction_amount;
									$transaction_receipt_insert["created_at"] = date("Y-m-d H:i:s");
									$transaction_receipt_insert["updated_at"] = date("Y-m-d H:i:s");
									$this->db->insert('coop_cremation_advance_payment_receipt', $transaction_receipt_insert);
								}
								if(!empty($transaction_updates)) {
									$this->db->update_batch('coop_member_cremation', $transaction_updates, 'id');
								}
								$advance_diff += $cremation_2_detail["advance_pay"] > $data_insert['adv_payment_balance'] ? $cremation_2_detail["advance_pay"] - $data_insert['adv_payment_balance'] : 0;
							} else {
								$advance_diff += $cremation_2_detail["advance_pay"] > $cremation['adv_payment_balance'] ? $cremation_2_detail["advance_pay"] - $cremation['adv_payment_balance'] : 0;
							}
						}
						$non_pay_details = $this->db->select("run_id, non_pay_id")
													->from("coop_non_pay_detail")
													->where("member_id = '".$member_id."' AND cremation_type_id = '2' AND non_pay_amount_balance > '".$advance_diff."'")
													->get()->result_array();

						foreach($non_pay_details as $non_pay_detail) {
							$data_insert = array();
							$data_insert['non_pay_amount_balance'] = $advance_diff;
							$this->db->where('run_id', $non_pay_detail["run_id"]);
							$this->db->update('coop_non_pay_detail', $data_insert);

							$details = $this->db->select("sum(non_pay_amount_balance) as sum")
											->from("coop_non_pay_detail")
											->where("non_pay_id = '".$non_pay_detail["non_pay_id"]."'")
											->get()->result_array();
							$data_insert = array();
							if($details[0]['sum'] == 0) {
								$data_insert["non_pay_status"] = 2;
							}
							$data_insert["non_pay_amount_balance"] = $details[0]['sum'];
							$this->db->where('non_pay_id', $non_pay_detail["non_pay_id"]);
							$this->db->update('coop_non_pay', $data_insert);
						}
					}
				}else if ($data_post['deduct_code']=='DEPOSIT') {
					$account_transaction = $this->db->select("*")
													->from("coop_account_transaction")
													->where("account_id = '".$data_post['deposit_account_id']."' AND receipt_id = '".$receipt_number."'")
													->order_by("transaction_time desc, transaction_id desc")
													->get()->row_array();
					if(!empty($account_transaction)) {
						$loan_amount_balance = $account_transaction['transaction_balance'];
					} else {
						$loan_amount_balance = 0;
					}
				}
				
				$data_insert = array();
				$data_insert['member_id'] = $member_id;
				$data_insert['receipt_id'] = $receipt_number;
				$data_insert['loan_id'] = $data_post['loan_id'];
				$data_insert['loan_atm_id'] = $data_post['loan_atm_id'];
				$data_insert['account_list_id'] = $account_list_id;
				$data_insert['principal_payment'] = number_format($data_post['principal_payment'],2,'.','');
				$data_insert['interest'] = number_format($data_post['interest'],2,'.','');
				$data_insert['total_amount'] = $data_post['amount'];
				$data_insert['loan_amount_balance'] = number_format($loan_amount_balance,2,'.','');
				$data_insert['payment_date'] = date("Y-m-d H:i:s");
				$data_insert['createdatetime'] = date("Y-m-d H:i:s");
				$data_insert['transaction_text'] = $data_post['transaction_text'];
				$this->db->insert('coop_finance_transaction', $data_insert);
				
				$data['coop_account_detail'][40100]['account_type'] = 'credit';
				@$data['coop_account_detail'][40100]['account_amount'] += number_format($data_post['interest'],2,'.','');
				$data['coop_account_detail'][40100]['account_chart_id'] = '40100';
				
				if($data_post['loan_id'] == ''){
					$this->db->select('account_chart_id');
					$this->db->from('coop_account_match');
					$this->db->where("match_id = '".$account_list_id."' AND match_type = 'account_list'");
					$row = $this->db->get()->result_array();
					$row_account_chart = @$row[0];
					$account_chart_id = @$row_account_chart['account_chart_id'];
				}else{
					$this->db->select('coop_account_match.account_chart_id');
					$this->db->from('coop_account_match');
					$this->db->join('coop_loan', 'coop_account_match.match_id = coop_loan.loan_type', 'left');
					$this->db->where("coop_loan.id = '".$data_post['loan_id']."' AND coop_account_match.match_type = 'loan'");
					$row = $this->db->get()->result_array();
					$row_account_chart = @$row[0];
					$account_chart_id = @$row_account_chart['account_chart_id'];
				}
				
				$data['coop_account_detail'][$key]['account_type'] = 'credit';
				$data['coop_account_detail'][$key]['account_amount'] = number_format($data_post['principal_payment'],2,'.','');
				$data['coop_account_detail'][$key]['account_chart_id'] = $account_chart_id;
			}
		}
		$this->account_transaction->account_process($data);
		
		echo"<script> document.location.href='".PROJECTPATH."/debt/debt_pay?id={$member_id}' </script>";
		exit;
	}
	
	//ส่งsmsแจ้งเตือนทุกวันที่ 1 ของเดือน สำหรับรายการที่ยังไม่ถูกชำระ
	function script_send_sms(){	
		$this->db->select(array('coop_non_pay.*','coop_mem_apply.mobile','coop_mem_apply.firstname_th','coop_mem_apply.lastname_th'));
		$this->db->from('coop_non_pay');
		$this->db->join("coop_mem_apply","coop_non_pay.member_id = coop_mem_apply.member_id","left");
		$this->db->where("non_pay_status NOT IN ('0','2')");		
		$rs = $this->db->get()->result_array();
		
		$date_now = date('j');
		foreach($rs AS $key=>$row){
			$mobile = $row['mobile'];
			$msg = "แจ้งชำระหนี้";
			if($date_now  == '1'){
				//$status_sms = $this->center_function->send_sms($mobile, $msg);
			}
		}
		
		exit;
	}
	
	public function letter_pdf(){		
		$data_arr = array();
		
		$this->db->from('coop_profile');
		$this->db->limit(1);
		$row_profile = $this->db->get()->result_array();
		$data_arr['row_profile'] = $row_profile[0];
		
		$this->load->view('debt/letter_pdf',$data_arr);
	}
	
	public function letter_perview_backup_24_11_2018(){	
		$data_arr = array();
		$data_arr['month_arr'] = array('1'=>'มกราคม','2'=>'กุมภาพันธ์','3'=>'มีนาคม','4'=>'เมษายน','5'=>'พฤษภาคม','6'=>'มิถุนายน','7'=>'กรกฎาคม','8'=>'สิงหาคม','9'=>'กันยายน','10'=>'ตุลาคม','11'=>'พฤศจิกายน','12'=>'ธันวาคม');
		$month_arr_2 = array('1'=>'มกราคม','2'=>'กุมภาพันธ์','3'=>'มีนาคม','4'=>'เมษายน','5'=>'พฤษภาคม','6'=>'มิถุนายน','7'=>'กรกฎาคม','8'=>'สิงหาคม','9'=>'กันยายน','10'=>'ตุลาคม','11'=>'พฤศจิกายน','12'=>'ธันวาคม');
		
		$param = '';
		foreach($_GET as $key => $value){
			$param .= @$key."&";
			$decode = base64_decode(@$key);
			$decode = explode('=',@$decode);
			$_GET[$decode[0]] = @$decode[1];
		}
		
		$print_ref = @$_GET['print_ref'];
		$non_pay = @$_GET['non_pay'];
		$letter_month = @$_GET['letter_month'];
		$letter_year = @$_GET['letter_year'];
		$action = @$_GET['action'];
		$arr_letter = array();
		if(!empty($print_ref)){
			$rs_ref = $this->db->from('coop_debt_letter')
								->where("print_ref = '{$print_ref}'")
								->order_by("member_id,letter_month,letter_year")
								->get()->result_array();
			//echo $this->db->last_query();
			foreach($rs_ref AS $key=>$row_ref){
				if(!empty($row_ref['letter_id'])){
					$arr_letter[] = $row_ref['letter_id'];
				}
			}	
		}else if(!empty($non_pay)){
			$arr_non_pay = explode("~",$non_pay);
			$rs2 = $this->db->from('coop_debt_letter')
							->where("non_pay_id IN ('".implode("','",@$arr_non_pay)."') AND letter_month = '{$letter_month }' AND letter_year = '{$letter_year}'")
							->order_by("member_id,letter_month,letter_year")
							->get()->result_array();
			foreach($rs2 AS $key2=>$row2){
				$arr_letter[] = $row2['letter_id'];
			}
		}else{	
			$arr_letter[] = @$_GET['id'];
		}
		
		if($action=='view'){
			
		}

		$row_profile = $this->db->from('coop_profile')
								->limit(1)
								->get()->result_array();
		$data_arr['row_profile'] = $row_profile[0];

		//ครั้งของการส่งจดหมาย		
		//if(@$letter_id){
		if(@$arr_letter){
			foreach($arr_letter AS $key_letter=>$letter_id){				
				$rs_letter = $this->db->select(array('coop_debt_letter.*','coop_finance_month_profile.profile_id'))
										->from('coop_debt_letter')
										->join("coop_non_pay", "coop_debt_letter.non_pay_id = coop_non_pay.non_pay_id", "inner")
										->join("coop_finance_month_profile","coop_non_pay.non_pay_month = coop_finance_month_profile.profile_month AND coop_debt_letter.letter_year = coop_non_pay.non_pay_year","left")
										->where("coop_debt_letter.letter_id = '{$letter_id}'")
										->limit(1)
										->get()->result_array();
				$row_letter = @$rs_letter[0];
				$data_arr['data'][$key_letter]['letter_runno'] = @$row_letter['runno'];
				$data_arr['data'][$key_letter]['letter'] = @$row_letter;
				$non_pay_id = @$row_letter['non_pay_id'];	
				
				$rs_member = $this->db->select(array('coop_non_pay.non_pay_month','coop_non_pay.non_pay_year','coop_mem_apply.member_id',
										'coop_prename.prename_full',
										'coop_prename.prename_short',
										'coop_mem_apply.firstname_th',
										'coop_mem_apply.lastname_th',
										'coop_mem_apply.share_month'
										))
										->from('coop_non_pay')
										->join("coop_mem_apply","coop_mem_apply.member_id = coop_non_pay.member_id","inner")
										->join("coop_prename","coop_prename.prename_id = coop_mem_apply.prename_id","left")
										->where("coop_non_pay.non_pay_id = '{$non_pay_id}'")
										->limit(1)
										->get()->result_array();
				$row_member= @$rs_member[0];
				$row_member['full_name']= @$row_member['prename_full']. @$row_member['firstname_th']."  ".@$row_member['lastname_th'];
				$data_arr['data'][$key_letter]['row_member'] = $row_member;

				//echo $this->db->last_query();
				if(@$row_member['member_id']){
					$rs_non_pay = $this->db->select(array('coop_non_pay_detail.*','coop_loan.*','coop_loan_type.loan_type'))
											->from('coop_non_pay_detail')
											->join("coop_loan","coop_non_pay_detail.loan_id = coop_loan.id","inner")
											->join("coop_loan_name","coop_loan.loan_type = coop_loan_name.loan_name_id","inner")
											->join("coop_loan_type","coop_loan_name.loan_type_id = coop_loan_type.id","inner")
											->where("coop_non_pay_detail.non_pay_id = '{$non_pay_id}' AND coop_non_pay_detail.deduct_code = 'LOAN'")
											->limit(1)
											->get()->result_array();
					$row_non_pay = @$rs_non_pay[0];			
					$data_arr['data'][$key_letter]['row_non_pay'] = $row_non_pay;
					//echo '<pre>'; print_r($row_non_pay); echo '</pre>';
					//echo $this->db->last_query();

					if(!empty($row_non_pay)){
						//ชำระเงินกู้ต่องวด ใช่งวดแรก
						$rs_loan_pay = $this->db->select(array('total_paid_per_month'))
												->from('coop_loan_period')
												->where("loan_id = '{$row_non_pay['loan_id']}'")
												->order_by("period_count ASC")
												->limit(1)
												->get()->result_array();
						$row_loan_pay = @$rs_loan_pay[0];
						$data_arr['data'][$key_letter]['row_loan_pay'] = @$row_loan_pay['total_paid_per_month']; 

						//หาเดือนที่เป็นหนี้
						$rs_letter_all = $this->db->select(array('coop_debt_letter.*',
															'coop_non_pay.non_pay_month',
															'coop_non_pay.non_pay_year',
															'coop_non_pay.non_pay_amount_balance'))
													->from('coop_debt_letter')
													->join("coop_non_pay","coop_debt_letter.non_pay_id = coop_non_pay.non_pay_id","join")
													->where("coop_debt_letter.member_id = '{$row_member['member_id']}' 
																AND coop_debt_letter.letter_month = '".@$row_letter['letter_month']."'
																AND coop_debt_letter.letter_year = '{$row_letter['letter_year']}' 
															")
													->get()->result_array();
						$arr_letter_mm_yy = array();
						$arr_letter_mm = array();
						$check_month_balance = 0;
						foreach($rs_letter_all AS $key=>$row_letter_all){
							if(@$row_letter_all['non_pay_month'] < @$row_letter['letter_month']){
								$rs_check_non = $this->db->select(array('non_pay_amount_balance'))
															->from('coop_non_pay_detail')
															->where("non_pay_id = '{$row_letter_all['non_pay_id']}' AND deduct_code = 'LOAN'")
															->get()->result_array();
								foreach($rs_check_non AS $key_check_non=>$row_check_non){	
									$check_month_balance += @$row_check_non['non_pay_amount_balance'];
								}
							}

							$arr_letter_mm_yy[] = @$month_arr_2[@$row_letter_all['non_pay_month']]." ".@$row_letter_all['non_pay_year'];
							if($check_month_balance != 0){	
								if(@$row_letter_all['non_pay_month'] < @$row_letter['letter_month']){
									$arr_letter_mm[] = @$row_letter_all['non_pay_month'];	
								}								
							}
						}
						$data_arr['data'][$key_letter]['letter_mm_yy'] = @$arr_letter_mm_yy;
						$data_arr['data'][$key_letter]['check_month_balance'] = (@$check_month_balance == 0)?0:1;

						$this->db->select(array('*'));
						$this->db->from('coop_finance_month_detail');
						$this->db->where("loan_id = '{$row_non_pay['loan_id']}' AND member_id = '{$row_non_pay['member_id']}' 
										AND profile_id = '{$row_letter['profile_id']}'");
						$rs_dep_pay = $this->db->get()->result_array();
						$pay_amount = 0;
						$real_pay_amount = 0;
						$real_pay_amount_all = 0;

						foreach($rs_dep_pay  AS $key=>$row_dep_pay){
							$pay_amount += @$row_dep_pay['pay_amount'];
							$real_pay_amount += @$row_dep_pay['real_pay_amount'];
						}			
						$data_arr['data'][$key_letter]['pay_amount'] = @$pay_amount; //จำนวนเงินที่ต้องชำระ กรณีแจ้งเตือนครั้ง ที่ 1	

						$real_pay_amount_all = @$pay_amount - @$row_non_pay['non_pay_amount_balance'];
						$data_arr['data'][$key_letter]['real_pay_amount'] = @$real_pay_amount_all; //ยอดที่ชำระบางส่วน

						//ชำระเงินกู้ ยอดที่ยังไม่ได้ชำระ
						$this->db->select(array('coop_non_pay.*',
											'coop_non_pay_detail.non_pay_amount',
											'coop_non_pay_detail.non_pay_amount_balance',
											'coop_non_pay_detail.loan_id',
											'coop_non_pay_detail.pay_type',
											'coop_non_pay_detail.finance_month_profile_id'));
						$this->db->from('coop_non_pay');
						$this->db->join("coop_non_pay_detail","coop_non_pay.non_pay_id = coop_non_pay_detail.non_pay_id AND deduct_code = 'LOAN' AND coop_non_pay_detail.pay_type ='interest' ","left");
						$this->db->where("coop_non_pay.member_id = '{$row_non_pay['member_id']}' AND coop_non_pay.non_pay_month IN ('".implode("','",@$arr_letter_mm)."')");
						$rs_non_pay_milti = $this->db->get()->result_array();
						$pay_now = 0;
						$non_pay_now = 0;
						$total_pay_now = 0;
						$arr_profile_id = array();

						if(!empty($rs_non_pay_milti)){
							foreach($rs_non_pay_milti AS $key=>$row_non_pay_milti){
								//ยอดที่ค้างชำระ
								$non_pay_now += @$row_non_pay_milti['non_pay_amount_balance'];
							}
						}

						//ยอดที่ค้างชำระ
						$data_arr['data'][$key_letter]['non_pay_now'] = @$non_pay_now;
						//รวมทั้งสิ้น
						$total_pay_now = @$pay_amount+@$non_pay_now;
						$data_arr['data'][$key_letter]['total_pay_now'] = @$total_pay_now;
						$data_arr['data'][$key_letter]['no_pay_amount'] = $total_pay_now-$real_pay_amount_all; //ยอดที่ค้าง ชำระ					

						//คนค้ำ
						$this->db->select(array('coop_loan_guarantee_person.*',
												'coop_prename.prename_full',
												'coop_prename.prename_short',
												'coop_mem_apply.firstname_th',
												'coop_mem_apply.lastname_th',
												'coop_mem_apply.share_month'));
						$this->db->from('coop_loan_guarantee_person');
						$this->db->join("coop_mem_apply","coop_mem_apply.member_id = coop_loan_guarantee_person.guarantee_person_id","inner");
						$this->db->join("coop_prename","coop_prename.prename_id = coop_mem_apply.prename_id","left");
						$this->db->where("coop_loan_guarantee_person.loan_id = '{$row_non_pay['loan_id']}'");
						$rs_guarantee_person = $this->db->get()->result_array();
						$data_arr['data'][$key_letter]['rs_guarantee_person'] = @$rs_guarantee_person;	
					}

					//หุ้น  การค้างชำระค่าหุ้นรายเดือน
					$this->db->select(array('coop_debt_letter.*',
											'coop_non_pay.non_pay_month',
											'coop_non_pay.non_pay_year',
											'coop_non_pay.non_pay_amount_balance'));
					$this->db->from('coop_debt_letter');
					$this->db->join("coop_non_pay","coop_debt_letter.non_pay_id = coop_non_pay.non_pay_id","join");
					$this->db->where("coop_debt_letter.member_id = '{$row_member['member_id']}' 
										AND coop_debt_letter.letter_month = '".@$row_letter['letter_month']."'
										AND coop_debt_letter.letter_year = '{$row_letter['letter_year']}' 
									");
					$rs_letter_all = $this->db->get()->result_array();
					$arr_share_letter_mm_yy = array();
					$arr_share_letter_mm = array();
					$check_month_balance = 0;
					$share_num_period = 1;
					$n = 0;
					foreach($rs_letter_all AS $key=>$row_letter_all){
						//echo '<pre>'; print_r($row_letter_all); echo '</pre>';
						if(@$row_letter_all['non_pay_month'] <= @$row_letter['letter_month']){
							$this->db->select(array('non_pay_amount_balance'));
							$this->db->from('coop_non_pay_detail');
							$this->db->where("non_pay_id = '{$row_letter_all['non_pay_id']}' AND deduct_code = 'SHARE'");
							$rs_check_non = $this->db->get()->result_array();
							
							foreach($rs_check_non AS $key_check_non=>$row_check_non){	
								$check_month_balance += @$row_check_non['non_pay_amount_balance'];
							}
						}
						
						
						$arr_share_letter_mm_yy[] = @$month_arr_2[@$row_letter_all['non_pay_month']]." ".@$row_letter_all['non_pay_year'];
						if($check_month_balance != 0){
							if(@$row_letter_all['non_pay_month'] < @$row_letter['letter_month']){
								$arr_share_letter_mm[] = @$row_letter_all['non_pay_month'];	
								$share_num_period++;
							}
							/////
							$non_share = @$rs_check_non[0]['non_pay_amount_balance'];
							$arr_non_share[$n]['mm_yy'] = @$month_arr_2[@$row_letter_all['non_pay_month']]." ".@$row_letter_all['non_pay_year'];
							$arr_non_share[$n]['non_pay_amount_balance'] = @$non_share;
							$n++;	
						}
					}
					//echo '<pre>'; print_r($arr_share_letter_mm_yy); echo '</pre>';
					$data_arr['data'][$key_letter]['share_num_period'] = @$share_num_period;
					$data_arr['data'][$key_letter]['row_non_share'] = @$arr_non_share;
						
					$this->db->select(array('coop_non_pay_detail.*'));
					$this->db->from('coop_non_pay_detail');
					$this->db->where("coop_non_pay_detail.finance_month_profile_id = '{$row_letter['profile_id']}' 
										AND coop_non_pay_detail.deduct_code = 'SHARE' 
										AND coop_non_pay_detail.member_id = '{$row_member['member_id']}'");
					$this->db->limit(1);
					$rs_non_pay_share = $this->db->get()->result_array();
					$row_non_pay_share = @$rs_non_pay_share[0];	
					//echo $this->db->last_query(); exit;
					$this->db->select(array(
									'coop_finance_month_detail.*',
									'coop_finance_month_profile.profile_month',
									'coop_finance_month_profile.profile_year'
					));
					$this->db->from('coop_finance_month_detail');
					$this->db->join("coop_finance_month_profile","coop_finance_month_detail.profile_id = coop_finance_month_profile.profile_id","left");
					$this->db->where("coop_finance_month_detail.member_id = '{$row_member['member_id']}' 
									AND coop_finance_month_detail.deduct_code = 'SHARE' 
									AND coop_finance_month_detail.profile_id = '{$row_letter['profile_id']}'");
					$rs_share_pay = $this->db->get()->result_array();
					$share_amount = 0;
					$real_share_amount = 0;
					$real_pay_amount_all = 0;

					foreach($rs_share_pay  AS $key=>$row_share_pay){
						$share_amount += @$row_share_pay['pay_amount'];
						$real_share_amount += @$row_share_pay['real_pay_amount'];
					}			
					
					$real_pay_amount_all = @$share_amount - @$row_non_pay_share['non_pay_amount_balance'];
					$data_arr['data'][$key_letter]['real_share_amount'] = @$real_share_amount; //ยอดที่ชำระบางส่วน
					
					//หุ้น ยอดที่ยังไม่ได้ชำระ
					$this->db->select(array('coop_non_pay.*',
										'coop_non_pay_detail.non_pay_amount',
										'coop_non_pay_detail.non_pay_amount_balance',
										'coop_non_pay_detail.loan_id',
										'coop_non_pay_detail.pay_type',
										'coop_non_pay_detail.finance_month_profile_id'));
					$this->db->from('coop_non_pay');
					$this->db->join("coop_non_pay_detail","coop_non_pay.non_pay_id = coop_non_pay_detail.non_pay_id AND deduct_code = 'SHARE'","left");
					$this->db->where("coop_non_pay.member_id = '{$row_member['member_id']}' AND coop_non_pay.non_pay_month IN ('".implode("','",@$arr_share_letter_mm)."')");
					$rs_non_pay_milti = $this->db->get()->result_array();
					//$share_amount = 0;
					$no_share_amount = 0;
					$total_pay_now_share = 0;
					$arr_profile_id = array();
					
					if(!empty($rs_non_pay_milti)){
						foreach($rs_non_pay_milti AS $key=>$row_non_pay_milti){
							//ยอดที่ค้างชำระ
							$no_share_amount += @$row_non_pay_milti['non_pay_amount_balance'];
						}
					}
							
					//ยอดที่ค้างชำระ
					$data_arr['data'][$key_letter]['no_share_amount'] = @$no_share_amount;
					//รวมทั้งสิ้น
					$total_pay_now_share = @$share_amount+@$no_share_amount;
					//$data_arr['total_pay_now_share'] = @$total_pay_now_share;
					$data_arr['data'][$key_letter]['share_amount'] = @$total_pay_now_share;
					$data_arr['data'][$key_letter]['no_share_amount'] = $total_pay_now_share-$real_pay_amount_all; //ยอดที่ค้าง ชำระ
					$data_arr['data'][$key_letter]['non_pay_month_share'] = implode(",",@$arr_share_letter_mm_yy); //เดือนที่ไม่ได้ชำระ
						
					$data_arr['data'][$key_letter]['check_dept'] = (@$real_pay_amount == 0)?0:1; 	//เช็คการชำระหนี้ 0 = ยังไม่ได้ชำระ ,1=ชำระบางส่วน				
					$data_arr['data'][$key_letter]['check_share'] = (@$real_share_amount == 0)?0:1;   //การชำระค่าแชร์ 0 = ยังไม่ได้ชำระ ,1=ชำระบางส่วน
				}
			}
		}
		
		//ลายเซ็นต์
		$date_signature = date('Y-m-d');
		$this->db->select(array('*'));
		$this->db->from('coop_signature');
		$this->db->where("start_date <= '{$date_signature}'");
		$this->db->order_by('start_date DESC');
		$this->db->limit(1);
		$row = $this->db->get()->result_array();
		$data_arr['signature'] = @$row[0];	

		//exit;
		$this->preview_libraries->template_preview('debt/letter_perview',$data_arr);
	}

	public function letter_perview_backup_03_12_2018(){	
		$data_arr = array();
		$data_arr['month_arr'] = array('1'=>'มกราคม','2'=>'กุมภาพันธ์','3'=>'มีนาคม','4'=>'เมษายน','5'=>'พฤษภาคม','6'=>'มิถุนายน','7'=>'กรกฎาคม','8'=>'สิงหาคม','9'=>'กันยายน','10'=>'ตุลาคม','11'=>'พฤศจิกายน','12'=>'ธันวาคม');
		$month_arr_2 = array('1'=>'มกราคม','2'=>'กุมภาพันธ์','3'=>'มีนาคม','4'=>'เมษายน','5'=>'พฤษภาคม','6'=>'มิถุนายน','7'=>'กรกฎาคม','8'=>'สิงหาคม','9'=>'กันยายน','10'=>'ตุลาคม','11'=>'พฤศจิกายน','12'=>'ธันวาคม');

		$param = '';
		foreach($_GET as $key => $value){
			$param .= @$key."&";
			$decode = base64_decode(@$key);
			$decode = explode('=',@$decode);
			$_GET[$decode[0]] = @$decode[1];
		}

		$print_ref = @$_GET['print_ref'];
		$non_pay = @$_GET['non_pay'];
		$letter_month = @$_GET['letter_month'];
		$letter_year = @$_GET['letter_year'];
		$action = @$_GET['action'];
		$arr_letter = array();
		if(!empty($print_ref)){
			$rs_ref = $this->db->from('coop_debt_letter')
								->where("print_ref = '{$print_ref}'")
								->order_by("member_id,letter_month,letter_year")
								->get()->result_array();

			foreach($rs_ref AS $key=>$row_ref){
				if(!empty($row_ref['letter_id'])){
					$arr_letter[] = $row_ref['letter_id'];
				}
			}	
		}else if(!empty($non_pay)){
			$arr_non_pay = explode("~",$non_pay);
			$rs2 = $this->db->from('coop_debt_letter')
							->where("non_pay_id IN ('".implode("','",@$arr_non_pay)."') AND letter_month = '{$letter_month }' AND letter_year = '{$letter_year}'")
							->order_by("member_id,letter_month,letter_year")
							->get()->result_array();
			foreach($rs2 AS $key2=>$row2){
				$arr_letter[] = $row2['letter_id'];
			}
		}else{	
			$arr_letter[] = @$_GET['id'];
		}

		$row_profile = $this->db->from('coop_profile')
								->limit(1)
								->get()->result_array();
		$data_arr['row_profile'] = $row_profile[0];

		//ครั้งของการส่งจดหมาย		
		if(@$arr_letter){
			foreach($arr_letter AS $key_letter=>$letter_id){
				$rs_letter = $this->db->select(array('coop_debt_letter.*','coop_finance_month_profile.profile_id'))
										->from('coop_debt_letter')
										->join("coop_non_pay", "coop_debt_letter.non_pay_id = coop_non_pay.non_pay_id", "inner")
										->join("coop_finance_month_profile","coop_non_pay.non_pay_month = coop_finance_month_profile.profile_month AND coop_debt_letter.letter_year = coop_non_pay.non_pay_year","left")
										->where("coop_debt_letter.letter_id = '{$letter_id}'")
										->limit(1)
										->get()->result_array();
				$row_letter = @$rs_letter[0];
				$data_arr['data'][$key_letter]['letter_runno'] = @$row_letter['runno'];
				$data_arr['data'][$key_letter]['letter'] = @$row_letter;
				$non_pay_id = @$row_letter['non_pay_id'];	

				$rs_member = $this->db->select(array('coop_non_pay.non_pay_month','coop_non_pay.non_pay_year','coop_mem_apply.member_id',
											'coop_prename.prename_full',
											'coop_prename.prename_short',
											'coop_mem_apply.firstname_th',
											'coop_mem_apply.lastname_th',
											'coop_mem_apply.share_month'
										))
										->from('coop_non_pay')
										->join("coop_mem_apply","coop_mem_apply.member_id = coop_non_pay.member_id","inner")
										->join("coop_prename","coop_prename.prename_id = coop_mem_apply.prename_id","left")
										->where("coop_non_pay.non_pay_id = '{$non_pay_id}'")
										->limit(1)
										->get()->result_array();
				$row_member= @$rs_member[0];
				$row_member['full_name'] = @$row_member['prename_full']. @$row_member['firstname_th']."  ".@$row_member['lastname_th'];
				$data_arr['data'][$key_letter]['row_member'] = $row_member;

				if(@$row_member['member_id']){
					$total_principal = 0;
					$total_interest = 0;
					$total_principal_debt = 0;
					$total_interest_debt = 0;

					$rs_non_pay_loan = $this->db->select(array('coop_non_pay_detail.non_pay_amount_balance',
															'coop_non_pay_detail.member_id',
															'coop_non_pay_detail.loan_id',
															'coop_loan.contract_number',
															'coop_loan.loan_amount',
															'coop_loan.period_amount',
															'coop_loan_type.loan_type',
															'coop_non_pay_detail.pay_type'))
											->from('coop_non_pay_detail')
											->join("coop_loan","coop_non_pay_detail.loan_id = coop_loan.id","inner")
											->join("coop_loan_name","coop_loan.loan_type = coop_loan_name.loan_name_id","inner")
											->join("coop_loan_type","coop_loan_name.loan_type_id = coop_loan_type.id","inner")
											->where("coop_non_pay_detail.non_pay_id = '{$non_pay_id}' AND coop_non_pay_detail.deduct_code = 'LOAN'")
											->get()->result_array();

					$rs_non_pay_loan_atm = $this->db->select("coop_non_pay_detail.non_pay_amount_balance,
																coop_non_pay_detail.member_id,
																coop_non_pay_detail.loan_atm_id,
																coop_loan_atm.contract_number,
																'เงินกู้ฉุกเฉิน' as loan_type,
																coop_non_pay_detail.pay_type")
												->from('coop_non_pay_detail')
												->join("coop_loan_atm","coop_non_pay_detail.loan_atm_id = coop_loan_atm.loan_atm_id","left")
												->where("coop_non_pay_detail.non_pay_id = '{$non_pay_id}' AND coop_non_pay_detail.deduct_code = 'ATM'")
												->get()->result_array();

					$rs_non_pay = array_merge($rs_non_pay_loan, $rs_non_pay_loan_atm);

					// echo "<pre>";
					// print_r($rs_non_pay);
					// echo "</pre>";
					// exit;

					$data_arr['data'][$key_letter]['row_non_pay'] = $rs_non_pay[0];
					$data_arr['data'][$key_letter]['row_non_pays'] = $rs_non_pay;

					$loan_details = array();
					foreach($rs_non_pay as $row_non_pay) {

						if($row_non_pay['pay_type'] == 'principal') {
							$total_principal_debt += $row_non_pay['non_pay_amount_balance'];
						} else {
							$total_interest_debt += $row_non_pay['non_pay_amount_balance'];
						}

						//ชำระเงินกู้ต่องวด ใช่งวดแรก
						$rs_loan_pay = $this->db->select(array('total_paid_per_month'))
												->from('coop_loan_period')
												->where("loan_id = '{$row_non_pay['loan_id']}'")
												->order_by("period_count ASC")
												->limit(1)
												->get()->result_array();
						$row_loan_pay = @$rs_loan_pay[0];

						$data_arr['data'][$key_letter]['row_loan_pay'] = @$row_loan_pay['total_paid_per_month']; 

						//หาเดือนที่เป็นหนี้
						$rs_letter_all = $this->db->select(array('coop_debt_letter.*',
															'coop_non_pay.non_pay_month',
															'coop_non_pay.non_pay_year',
															'coop_non_pay.non_pay_amount_balance'))
													->from('coop_debt_letter')
													->join("coop_non_pay","coop_debt_letter.non_pay_id = coop_non_pay.non_pay_id","join")
													->where("coop_debt_letter.member_id = '{$row_member['member_id']}' 
																AND coop_debt_letter.letter_month = '".@$row_letter['letter_month']."'
																AND coop_debt_letter.letter_year = '{$row_letter['letter_year']}'
															")
													->get()->result_array();
						$arr_letter_mm_yy = array();
						$arr_letter_mm = array();
						$check_month_balance = 0;
						foreach($rs_letter_all AS $key=>$row_letter_all){
							if(@$row_letter_all['non_pay_month'] < @$row_letter['letter_month']){
								$rs_check_non = $this->db->select(array('non_pay_amount_balance'))
															->from('coop_non_pay_detail')
															->where("non_pay_id = '{$row_letter_all['non_pay_id']}' AND deduct_code = 'LOAN'")
															->get()->result_array();
								foreach($rs_check_non AS $key_check_non=>$row_check_non){	
									$check_month_balance += @$row_check_non['non_pay_amount_balance'];
								}
							}

							$arr_letter_mm_yy[] = @$month_arr_2[@$row_letter_all['non_pay_month']]." ".@$row_letter_all['non_pay_year'];
							if($check_month_balance != 0){	
								if(@$row_letter_all['non_pay_month'] < @$row_letter['letter_month']){
									$arr_letter_mm[] = @$row_letter_all['non_pay_month'];	
								}
							}
						}
						$data_arr['data'][$key_letter]['letter_mm_yy'] = @$arr_letter_mm_yy;
						$data_arr['data'][$key_letter]['check_month_balance'] = (@$check_month_balance == 0)?0:1;

						$rs_dep_pay = array();
						if (!empty($row_non_pay['loan_id'])) {
							$rs_dep_pay = $this->db->select(array('*'))
													->from('coop_finance_month_detail')
													->where("loan_id = '{$row_non_pay['loan_id']}' AND member_id = '{$row_non_pay['member_id']}' 
																AND profile_id = '{$row_letter['profile_id']}'")
													->get()->result_array();
						} else if (!empty($row_non_pay['loan_atm_id'])) {
							$rs_dep_pay = $this->db->select(array('*'))
													->from('coop_finance_month_detail')
													->where("loan_atm_id = '{$row_non_pay['loan_atm_id']}' AND member_id = '{$row_non_pay['member_id']}' 
																AND profile_id = '{$row_letter['profile_id']}'")
													->get()->result_array();	
						}
						$pay_amount = 0;
						$real_pay_amount = 0;
						$real_pay_amount_all = 0;

						$current_principal = 0;
						$current_interest = 0;

						foreach($rs_dep_pay  AS $key=>$row_dep_pay){
							$pay_amount += @$row_dep_pay['pay_amount'];
							$real_pay_amount += @$row_dep_pay['real_pay_amount'];
							if($row_non_pay['pay_type'] == 'principal' && $row_dep_pay['pay_type'] == 'principal') {
								$total_principal += $row_dep_pay['pay_amount'];
								
							} else if ($row_non_pay['pay_type'] == 'interest' && $row_dep_pay['pay_type'] == 'interest') {
								$total_interest += $row_dep_pay['pay_amount'];
								
							}

							if($row_dep_pay['pay_type'] == 'principal') {
								$current_principal = $row_dep_pay['pay_amount'];
								$current_principal_paid = $row_dep_pay['real_pay_amount'];
							} else if ($row_dep_pay['pay_type'] == 'interest') {
								$current_interest = $row_dep_pay['pay_amount'];
								$current_interest_paid = $row_dep_pay['real_pay_amount'];
							}
						}

						$data_arr['data'][$key_letter]['pay_amount'] = @$pay_amount; //จำนวนเงินที่ต้องชำระ กรณีแจ้งเตือนครั้ง ที่ 1

						$real_pay_amount_all = @$pay_amount - @$row_non_pay['non_pay_amount_balance'];
						$data_arr['data'][$key_letter]['real_pay_amount'] = @$real_pay_amount_all; //ยอดที่ชำระบางส่วน

						//ชำระเงินกู้ ยอดที่ยังไม่ได้ชำระ
						$rs_non_pay_milti = $this->db->select(array('coop_non_pay.*',
																		'coop_non_pay_detail.non_pay_amount',
																		'coop_non_pay_detail.non_pay_amount_balance',
																		'coop_non_pay_detail.loan_id',
																		'coop_non_pay_detail.pay_type',
																		'coop_non_pay_detail.finance_month_profile_id'))
														->from('coop_non_pay')
														->join("coop_non_pay_detail","coop_non_pay.non_pay_id = coop_non_pay_detail.non_pay_id AND deduct_code = 'LOAN' AND coop_non_pay_detail.pay_type ='interest' ","left")
														->where("coop_non_pay.member_id = '{$row_non_pay['member_id']}' AND coop_non_pay.non_pay_month IN ('".implode("','",@$arr_letter_mm)."')")
														->get()->result_array();
						$pay_now = 0;
						$non_pay_now = 0;
						$total_pay_now = 0;
						$arr_profile_id = array();

						if(!empty($rs_non_pay_milti)){
							foreach($rs_non_pay_milti AS $key=>$row_non_pay_milti){
								//ยอดที่ค้างชำระ
								$non_pay_now += @$row_non_pay_milti['non_pay_amount_balance'];
							}
						}

						//ยอดที่ค้างชำระ
						$data_arr['data'][$key_letter]['non_pay_now'] = @$non_pay_now;
						//รวมทั้งสิ้น
						$total_pay_now = @$pay_amount+@$non_pay_now;
						$data_arr['data'][$key_letter]['total_pay_now'] = @$total_pay_now;
						$data_arr['data'][$key_letter]['no_pay_amount'] = $total_pay_now-$real_pay_amount_all; //ยอดที่ค้าง ชำระ

						//คนค้ำ
						$rs_guarantee_person = $this->db->select(array('coop_loan_guarantee_person.*',
																		'coop_prename.prename_full',
																		'coop_prename.prename_short',
																		'coop_mem_apply.firstname_th',
																		'coop_mem_apply.lastname_th',
																		'coop_mem_apply.share_month'))
														->from('coop_loan_guarantee_person')
														->join("coop_mem_apply","coop_mem_apply.member_id = coop_loan_guarantee_person.guarantee_person_id","inner")
														->join("coop_prename","coop_prename.prename_id = coop_mem_apply.prename_id","left")
														->where("coop_loan_guarantee_person.loan_id = '{$row_non_pay['loan_id']}'")
														->get()->result_array();
						$data_arr['data'][$key_letter]['rs_guarantee_person'] = @$rs_guarantee_person;

						foreach($rs_guarantee_person as $row_guarantee_person) {
							if(!empty($row_guarantee_person['guarantee_person_id'])) {
								$data_arr['data'][$key_letter]['guarantee_persons'][$row_non_pay['contract_number']][$row_guarantee_person['guarantee_person_id']]['member_id'] = $row_guarantee_person['guarantee_person_id'];
								$data_arr['data'][$key_letter]['guarantee_persons'][$row_non_pay['contract_number']][$row_guarantee_person['guarantee_person_id']]['full_name'] = $row_guarantee_person['prename_full'].$row_guarantee_person['firstname_th']."  ".$row_guarantee_person['lastname_th'];
								$data_arr['data'][$key_letter]['guarantee_persons'][$row_non_pay['contract_number']][$row_guarantee_person['guarantee_person_id']]['total_principal_debt'] = $current_principal;
								$data_arr['data'][$key_letter]['guarantee_persons'][$row_non_pay['contract_number']][$row_guarantee_person['guarantee_person_id']]['total_interest_debt'] = $current_interest;
								$data_arr['data'][$key_letter]['guarantee_persons'][$row_non_pay['contract_number']][$row_guarantee_person['guarantee_person_id']]['total_principal_debt_paid'] = $current_principal_paid;
								$data_arr['data'][$key_letter]['guarantee_persons'][$row_non_pay['contract_number']][$row_guarantee_person['guarantee_person_id']]['total_interest_debt_paid'] = $current_interest_paid;
								// $data_arr['data'][$key_letter]['guarantee_persons'][$row_non_pay['contract_number']][$row_guarantee_person['guarantee_person_id']]['total_debt_none_paid']
								// if($row_non_pay['pay_type'] == 'principal') {
								// 	$data_arr['data'][$key_letter]['guarantee_persons'][$row_non_pay['contract_number']][$row_guarantee_person['guarantee_person_id']]['principal_debt_none_paid'] += $row_non_pay['non_pay_amount_balance'];
								// } else {
								// 	$data_arr['data'][$key_letter]['guarantee_persons'][$row_non_pay['contract_number']][$row_guarantee_person['guarantee_person_id']]['interest_debt_none_paid'] += $row_non_pay['non_pay_amount_balance'];
								// }
							}
						}


						$loan_details[$row_non_pay['contract_number']]['row_loan_pay'] = $row_loan_pay['total_paid_per_month'];
						$loan_details[$row_non_pay['contract_number']]['period_amount'] = $row_non_pay['period_amount'];
						$loan_details[$row_non_pay['contract_number']]['loan_type'] = $row_non_pay['loan_type'];
						$loan_details[$row_non_pay['contract_number']]['loan_amount'] = $row_non_pay['loan_amount'];
						$loan_details[$row_non_pay['contract_number']]['contract_number'] = $row_non_pay['contract_number'];
					}
					$data_arr['data'][$key_letter]['loan_details'] = $loan_details;

					//หุ้น การค้างชำระค่าหุ้นรายเดือน
					$rs_letter_all = $this->db->select(array('coop_debt_letter.*',
																'coop_non_pay.non_pay_month',
																'coop_non_pay.non_pay_year',
																'coop_non_pay.non_pay_amount_balance'))
												->from('coop_debt_letter')
												->join("coop_non_pay","coop_debt_letter.non_pay_id = coop_non_pay.non_pay_id","join")
												->where("coop_debt_letter.member_id = '{$row_member['member_id']}' 
															AND coop_debt_letter.letter_month = '".@$row_letter['letter_month']."'
															AND coop_debt_letter.letter_year = '{$row_letter['letter_year']}' 
														")
												->get()->result_array();
					$arr_share_letter_mm_yy = array();
					$arr_share_letter_mm = array();
					$check_month_balance = 0;
					$share_num_period = 1;
					$total_principal_share_debt = 0;
					$n = 0;
					foreach($rs_letter_all AS $key=>$row_letter_all){
						if(@$row_letter_all['non_pay_month'] <= @$row_letter['letter_month']){
							$rs_check_non = $this->db->select(array('non_pay_amount_balance'))
														->from('coop_non_pay_detail')
														->where("non_pay_id = '{$row_letter_all['non_pay_id']}' AND deduct_code = 'SHARE'")
														->get()->result_array();

							foreach($rs_check_non AS $key_check_non=>$row_check_non){	
								$check_month_balance += @$row_check_non['non_pay_amount_balance'];
							}
						}

						$arr_share_letter_mm_yy[] = @$month_arr_2[@$row_letter_all['non_pay_month']]." ".@$row_letter_all['non_pay_year'];
						if($check_month_balance != 0){
							if(@$row_letter_all['non_pay_month'] < @$row_letter['letter_month']){
								$arr_share_letter_mm[] = @$row_letter_all['non_pay_month'];	
								$share_num_period++;
							}

							$non_share = @$rs_check_non[0]['non_pay_amount_balance'];
							$arr_non_share[$n]['mm_yy'] = @$month_arr_2[@$row_letter_all['non_pay_month']]." ".@$row_letter_all['non_pay_year'];
							$arr_non_share[$n]['non_pay_amount_balance'] = @$non_share;
							$total_principal_share_debt += $non_share;
							$n++;	
						}
					}
					$data_arr['data'][$key_letter]['share_num_period'] = @$share_num_period;
					$data_arr['data'][$key_letter]['row_non_share'] = @$arr_non_share;

					$rs_non_pay_share = $this->db->select(array('coop_non_pay_detail.*'))
													->from('coop_non_pay_detail')
													->where("coop_non_pay_detail.finance_month_profile_id = '{$row_letter['profile_id']}' 
																AND coop_non_pay_detail.deduct_code = 'SHARE' 
																AND coop_non_pay_detail.member_id = '{$row_member['member_id']}'")
													->limit(1)
													->get()->result_array();
					$row_non_pay_share = @$rs_non_pay_share[0];	

					$rs_share_pay = $this->db->select(array(
															'coop_finance_month_detail.*',
															'coop_finance_month_profile.profile_month',
															'coop_finance_month_profile.profile_year'
															))
												->from('coop_finance_month_detail')
												->join("coop_finance_month_profile","coop_finance_month_detail.profile_id = coop_finance_month_profile.profile_id","left")
												->where("coop_finance_month_detail.member_id = '{$row_member['member_id']}' 
															AND coop_finance_month_detail.deduct_code = 'SHARE' 
															AND coop_finance_month_detail.profile_id = '{$row_letter['profile_id']}'")
												->get()->result_array();
					$share_amount = 0;
					$real_share_amount = 0;
					$real_pay_amount_all = 0;

					foreach($rs_share_pay  AS $key=>$row_share_pay){
						$share_amount += @$row_share_pay['pay_amount'];
						$real_share_amount += @$row_share_pay['real_pay_amount'];
					}

					$real_pay_amount_all = @$share_amount - @$row_non_pay_share['non_pay_amount_balance'];
					$data_arr['data'][$key_letter]['real_share_amount'] = @$real_share_amount; //ยอดที่ชำระบางส่วน

					//หุ้น ยอดที่ยังไม่ได้ชำระ
					$rs_non_pay_milti = $this->db->select(array('coop_non_pay.*',
																'coop_non_pay_detail.non_pay_amount',
																'coop_non_pay_detail.non_pay_amount_balance',
																'coop_non_pay_detail.loan_id',
																'coop_non_pay_detail.pay_type',
																'coop_non_pay_detail.finance_month_profile_id'))
														->from('coop_non_pay')
														->join("coop_non_pay_detail","coop_non_pay.non_pay_id = coop_non_pay_detail.non_pay_id AND deduct_code = 'SHARE'","left")
														->where("coop_non_pay.member_id = '{$row_member['member_id']}' AND coop_non_pay.non_pay_month IN ('".implode("','",@$arr_share_letter_mm)."')")
														->get()->result_array();

					//$share_amount = 0;
					$no_share_amount = 0;
					$total_pay_now_share = 0;
					$arr_profile_id = array();

					if(!empty($rs_non_pay_milti)){
						foreach($rs_non_pay_milti AS $key=>$row_non_pay_milti){
							//ยอดที่ค้างชำระ
							$no_share_amount += @$row_non_pay_milti['non_pay_amount_balance'];
						}
					}

					//ยอดที่ค้างชำระ
					$data_arr['data'][$key_letter]['no_share_amount'] = @$no_share_amount;
					//รวมทั้งสิ้น
					$total_pay_now_share = @$share_amount+@$no_share_amount;
					$data_arr['data'][$key_letter]['share_amount'] = @$total_pay_now_share;
					$data_arr['data'][$key_letter]['no_share_amount'] = $total_pay_now_share-$real_pay_amount_all; //ยอดที่ค้าง ชำระ
					$data_arr['data'][$key_letter]['non_pay_month_share'] = implode(",",@$arr_share_letter_mm_yy); //เดือนที่ไม่ได้ชำระ

					$data_arr['data'][$key_letter]['check_dept'] = (@$real_pay_amount == 0)?0:1; 	//เช็คการชำระหนี้ 0 = ยังไม่ได้ชำระ ,1=ชำระบางส่วน				
					$data_arr['data'][$key_letter]['check_share'] = (@$real_share_amount == 0)?0:1;   //การชำระค่าแชร์ 0 = ยังไม่ได้ชำระ ,1=ชำระบางส่วน
				}
			}
		}

		//ลายเซ็นต์
		$date_signature = date('Y-m-d');
		$row = $this->db->select(array('*'))
						->from('coop_signature')
						->where("start_date <= '{$date_signature}'")
						->order_by('start_date DESC')
						->limit(1)
						->get()->result_array();
		$data_arr['signature'] = @$row[0];

		$data_arr['total_principal'] = $total_principal;
		$data_arr['total_interest'] = $total_interest;
		$data_arr['total_principal_debt'] = $total_principal_debt;
		$data_arr['total_interest_debt'] = $total_interest_debt;
		$data_arr['total_principal_share_debt'] = $total_principal_share_debt;

		$this->preview_libraries->template_preview('debt/letter_perview',$data_arr);
	}

	public function letter_perview(){
		$data_arr = array();
		$data_arr['month_arr'] = array('1'=>'มกราคม','2'=>'กุมภาพันธ์','3'=>'มีนาคม','4'=>'เมษายน','5'=>'พฤษภาคม','6'=>'มิถุนายน','7'=>'กรกฎาคม','8'=>'สิงหาคม','9'=>'กันยายน','10'=>'ตุลาคม','11'=>'พฤศจิกายน','12'=>'ธันวาคม');
		$month_arr_2 = array('1'=>'มกราคม','2'=>'กุมภาพันธ์','3'=>'มีนาคม','4'=>'เมษายน','5'=>'พฤษภาคม','6'=>'มิถุนายน','7'=>'กรกฎาคม','8'=>'สิงหาคม','9'=>'กันยายน','10'=>'ตุลาคม','11'=>'พฤศจิกายน','12'=>'ธันวาคม');

		$atm_setting = $this->db->select("*")->from("coop_loan_atm_setting")->get()->row();
		$atm_max_period = $atm_setting->max_period;
		$param = '';
		foreach($_GET as $key => $value){
			$param .= @$key."&";
			$decode = base64_decode(@$key);
			$decode = explode('=',@$decode);
			$_GET[$decode[0]] = @$decode[1];
		}

		$print_ref = @$_GET['print_ref'];
		$non_pay = @$_GET['non_pay'];
		$letter_month = @$_GET['letter_month'];
		$letter_year = @$_GET['letter_year'];
		$action = @$_GET['action'];
		$arr_letter = array();
		if(!empty($print_ref)){
			$rs_ref = $this->db->from('coop_debt_letter')
								->where("print_ref = '{$print_ref}'")
								->order_by("member_id,letter_month,letter_year")
								->get()->result_array();

			foreach($rs_ref AS $key=>$row_ref){
				if(!empty($row_ref['letter_id'])){
					$arr_letter[] = $row_ref['letter_id'];
				}
			}
		}else if(!empty($non_pay)){
			$arr_non_pay = explode("~",$non_pay);
			$rs2 = $this->db->from('coop_debt_letter')
							->where("non_pay_id IN ('".implode("','",@$arr_non_pay)."') AND letter_month = '{$letter_month }' AND letter_year = '{$letter_year}'")
							->order_by("member_id,letter_month,letter_year")
							->get()->result_array();
			foreach($rs2 AS $key2=>$row2){
				$arr_letter[] = $row2['letter_id'];
			}
		}else{
			$arr_letter[] = @$_GET['id'];
		}

		$row_profile = $this->db->from('coop_profile')
								->limit(1)
								->get()->result_array();
		$data_arr['row_profile'] = $row_profile[0];

		//ครั้งของการส่งจดหมาย
		if(@$arr_letter){
			foreach($arr_letter AS $key_letter=>$letter_id){
				$rs_letter = $this->db->select(array('coop_debt_letter.*','coop_finance_month_profile.profile_id', 'coop_non_pay.non_pay_month', 'coop_non_pay.non_pay_year'))
										->from('coop_debt_letter')
										->join("coop_non_pay", "coop_debt_letter.non_pay_id = coop_non_pay.non_pay_id", "inner")
										->join("coop_finance_month_profile","coop_non_pay.non_pay_month = coop_finance_month_profile.profile_month
																				AND coop_finance_month_profile.profile_year = coop_non_pay.non_pay_year","left")
										->where("coop_debt_letter.letter_id = '{$letter_id}'")
										->limit(1)
										->get()->result_array();
				$row_letter = @$rs_letter[0];

				$data_arr['data'][$key_letter]['letter_runno'] = @$row_letter['runno'];
				$data_arr['data'][$key_letter]['letter'] = @$row_letter;

				$non_pay_id = @$row_letter['non_pay_id'];

				$rs_member = $this->db->select(array('coop_non_pay.non_pay_month',
											'coop_non_pay.non_pay_year',
											'coop_non_pay.non_pay_amount_balance',
											'coop_mem_apply.member_id',
											'coop_prename.prename_full',
											'coop_prename.prename_short',
											'coop_mem_apply.firstname_th',
											'coop_mem_apply.lastname_th',
											'coop_mem_apply.share_month'
										))
										->from('coop_non_pay')
										->join("coop_mem_apply","coop_mem_apply.member_id = coop_non_pay.member_id","inner")
										->join("coop_prename","coop_prename.prename_id = coop_mem_apply.prename_id","left")
										->where("coop_non_pay.non_pay_id = '{$non_pay_id}'")
										->limit(1)
										->get()->result_array();
				$row_member= @$rs_member[0];
				$row_member['full_name'] = @$row_member['prename_full']. @$row_member['firstname_th']."  ".@$row_member['lastname_th'];
				$data_arr['data'][$key_letter]['row_member'] = $row_member;

				if(@$row_member['member_id']){
					$letter_details = $this->db->select("t1.*,
															t2.contract_number as loan_contract_number,
															t2.loan_amount,
															t2.loan_amount_balance,
															t2.period_amount,
															t2.loan_type,
															t3.contract_number as atm_contract_number,
															t3.total_amount as atm_amount,
															t3.total_amount_balance as atm_amount_balance,
															t3.max_period as atm_max_period,
															t5.loan_type")
												->from("coop_debt_letter_detail as t1")
												->join("coop_loan as t2", "t1.loan_id = t2.id", "left")
												->join("coop_loan_atm as t3", "t1.loan_atm_id = t3.loan_atm_id", "left")
												->join("coop_loan_name as t4","t2.loan_type = t4.loan_name_id","left")
												->join("coop_loan_type as t5","t4.loan_type_id = t5.id","left")
												->where("t1.letter_id = '".$letter_id."'")
												->order_by("t1.year, t1.month")
												->get()->result_array();

					$ym_check = array();
					foreach($letter_details as $letter_detail) {
						if($letter_detail["type"] == "LOAN") {
							$loan_pay = $this->db->select(array('total_paid_per_month'))
													->from('coop_loan_period')
													->where("loan_id = '{$letter_detail['loan_id']}'")
													->order_by("period_count ASC")
													->get()->row();

							$data_arr['data'][$key_letter]['loan_details'][$letter_detail['loan_contract_number']]['is_atm'] = 0;
							$data_arr['data'][$key_letter]['loan_details'][$letter_detail['loan_contract_number']]['principal'] = $letter_detail['principal'];
							$data_arr['data'][$key_letter]['loan_details'][$letter_detail['loan_contract_number']]['interest'] = $letter_detail['interest'];
							$data_arr['data'][$key_letter]['loan_details'][$letter_detail['loan_contract_number']]['principal_debt'] += $letter_detail['principal_debt'];
							$data_arr['data'][$key_letter]['loan_details'][$letter_detail['loan_contract_number']]['interest_debt'] += $letter_detail['interest_debt'];
							$data_arr['data'][$key_letter]['loan_details'][$letter_detail['loan_contract_number']]['principal_paid'] += $letter_detail['principal_paid'];
							$data_arr['data'][$key_letter]['loan_details'][$letter_detail['loan_contract_number']]['interest_paid'] += $letter_detail['interest_paid'];
							$data_arr['data'][$key_letter]['loan_details'][$letter_detail['loan_contract_number']]['loan_type'] = $letter_detail['loan_type'];
							$data_arr['data'][$key_letter]['loan_details'][$letter_detail['loan_contract_number']]['loan_amount'] = $letter_detail['loan_amount'];
							$data_arr['data'][$key_letter]['loan_details'][$letter_detail['loan_contract_number']]['period_amount'] = $letter_detail['period_amount'];
							$data_arr['data'][$key_letter]['loan_details'][$letter_detail['loan_contract_number']]['row_loan_pay'] = !empty($letter_detail['row_loan_pay']) ? $letter_detail['row_loan_pay'] : $letter_detail['principal'];
							$data_arr['data'][$key_letter]['loan_details'][$letter_detail['loan_contract_number']]['row_loan_pay'] = $letter_detail['loan_type'] == 1 ? $letter_detail['principal'] : $letter_detail['principal'] + $letter_detail['interest'];
							$data_arr['data'][$key_letter]['loan_details'][$letter_detail['loan_contract_number']]['mm_yy'][] = array('month'  => $letter_detail['month'],'year' => $letter_detail['year']);

							$data_arr['data'][$key_letter]['total_principal'] += $letter_detail['principal'];
							$data_arr['data'][$key_letter]['total_interest'] += $letter_detail['interest'];
							$data_arr['data'][$key_letter]['total_debt_principal'] += $letter_detail['principal_debt'];
							$data_arr['data'][$key_letter]['total_debt_interest'] += $letter_detail['interest_debt'];
							//Check if has payment
							$y_check = !empty($ym_check[$letter_detail['loan_contract_number']]["year"]) ? $ym_check[$letter_detail['loan_contract_number']]["year"] : 0;
							$m_check = !empty($ym_check[$letter_detail['loan_contract_number']]["month"]) ? $ym_check[$letter_detail['loan_contract_number']]["month"] : 0;
							if($y_check < $letter_detail['year'] || ($y_check == $letter_detail['year'] && $m_check < $letter_detail['month'])) {
								if(!empty($letter_detail['principal_paid']) || !empty($letter_detail['interest_paid'])) {
									$data_arr['data'][$key_letter]['check_dept'] = 1;
									$data_arr['data'][$key_letter]['loan_details'][$letter_detail['loan_contract_number']]['check_dept'] = 1;
								} else {
									// $data_arr['data'][$key_letter]['check_dept'] = 0;
									//make it same
									$data_arr['data'][$key_letter]['check_dept'] = 1;
									$data_arr['data'][$key_letter]['loan_details'][$letter_detail['loan_contract_number']]['check_dept'] = 1;
								}
								$ym_check[$letter_detail['loan_contract_number']]["year"] = $letter_detail['year'];
								$ym_check[$letter_detail['loan_contract_number']]["month"] = $letter_detail['month'];
							}

							if($row_letter["non_pay_year"] == $letter_detail['year'] && $row_letter["non_pay_month"] == $letter_detail['month']) {
								$data_arr['data'][$key_letter]['loan_details'][$letter_detail['loan_contract_number']]['principal_last'] = $letter_detail['principal'];
								$data_arr['data'][$key_letter]['loan_details'][$letter_detail['loan_contract_number']]['interest_last'] = $letter_detail['interest'];
								$data_arr['data'][$key_letter]['loan_details'][$letter_detail['loan_contract_number']]['principal_debt_last'] = $letter_detail['principal_debt'];
								$data_arr['data'][$key_letter]['loan_details'][$letter_detail['loan_contract_number']]['interest_debt_last'] = $letter_detail['interest_debt'];
								$data_arr['data'][$key_letter]['loan_details'][$letter_detail['loan_contract_number']]['principal_paid_last'] = $letter_detail['principal_paid'];
								$data_arr['data'][$key_letter]['loan_details'][$letter_detail['loan_contract_number']]['interest_paid_last'] = $letter_detail['interest_paid'];
							}

							$data_arr['data'][$key_letter]['paid_amount'] += $letter_detail['principal_paid'] + $letter_detail['interest_paid'];

							$guarantees = $this->db->select(array('coop_loan_guarantee_person.*',
																			'coop_prename.prename_full',
																			'coop_prename.prename_short',
																			'coop_mem_apply.firstname_th',
																			'coop_mem_apply.lastname_th',
																			'coop_mem_apply.share_month'))
															->from('coop_loan_guarantee_person')
															->join("coop_mem_apply","coop_mem_apply.member_id = coop_loan_guarantee_person.guarantee_person_id","inner")
															->join("coop_prename","coop_prename.prename_id = coop_mem_apply.prename_id","left")
															->where("coop_loan_guarantee_person.loan_id = '{$letter_detail['loan_id']}'")
															->get()->result_array();
							$data_arr['data'][$key_letter]['guarantee_persons'][$letter_detail['loan_contract_number']] = $guarantees;

						} elseif($letter_detail["type"] == "ATM") {
							$data_arr['data'][$key_letter]['loan_details'][$letter_detail['atm_contract_number']]['is_atm'] = 1;
							$data_arr['data'][$key_letter]['loan_details'][$letter_detail['atm_contract_number']]['principal'] = $letter_detail['principal'];
							$data_arr['data'][$key_letter]['loan_details'][$letter_detail['atm_contract_number']]['interest'] = $letter_detail['interest'];
							$data_arr['data'][$key_letter]['loan_details'][$letter_detail['atm_contract_number']]['principal_debt'] = $letter_detail['principal_debt'];
							$data_arr['data'][$key_letter]['loan_details'][$letter_detail['atm_contract_number']]['interest_debt'] = $letter_detail['interest_debt'];
							$data_arr['data'][$key_letter]['loan_details'][$letter_detail['atm_contract_number']]['principal_paid'] = $letter_detail['principal_paid'];
							$data_arr['data'][$key_letter]['loan_details'][$letter_detail['atm_contract_number']]['interest_paid'] = $letter_detail['interest_paid'];
							$data_arr['data'][$key_letter]['loan_details'][$letter_detail['atm_contract_number']]['loan_type'] = 'เงินกู้ฉุกเฉิน';
							$data_arr['data'][$key_letter]['loan_details'][$letter_detail['atm_contract_number']]['loan_amount'] = $letter_detail['atm_amount'];
							$data_arr['data'][$key_letter]['loan_details'][$letter_detail['atm_contract_number']]['period_amount'] = !empty($letter_detail['atm_max_period']) ? $letter_detail['atm_max_period'] : $atm_max_period;
							$data_arr['data'][$key_letter]['loan_details'][$letter_detail['atm_contract_number']]['row_loan_pay'] = $letter_detail['principal'] + $letter_detail['interest'];
							$data_arr['data'][$key_letter]['loan_details'][$letter_detail['atm_contract_number']]['mm_yy'][] = array('month'  => $letter_detail['month'],'year' => $letter_detail['year']);

							$data_arr['data'][$key_letter]['total_principal'] += $letter_detail['principal'];
							$data_arr['data'][$key_letter]['total_interest'] += $letter_detail['interest'];
							$data_arr['data'][$key_letter]['total_debt_principal'] += $letter_detail['principal_debt'];
							$data_arr['data'][$key_letter]['total_debt_interest'] += $letter_detail['interest_debt'];
							//Check if has payment
							$y_check = !empty($ym_check[$letter_detail['atm_contract_number']]["year"]) ? $ym_check[$letter_detail['atm_contract_number']]["year"] : 0;
							$m_check = !empty($ym_check[$letter_detail['atm_contract_number']]["month"]) ? $ym_check[$letter_detail['atm_contract_number']]["month"] : 0;
							if($y_check < $letter_detail['year'] || ($y_check == $letter_detail['year'] && $m_check < $letter_detail['month'])) {
								if(!empty($letter_detail['principal_paid']) || !empty($letter_detail['interest_paid'])) {
									$data_arr['data'][$key_letter]['check_dept'] = 1;
									$data_arr['data'][$key_letter]['loan_details'][$letter_detail['atm_contract_number']]['check_dept'] = 1;
								} else {
									// $data_arr['data'][$key_letter]['check_dept'] = 0;
									//make it same
									$data_arr['data'][$key_letter]['check_dept'] = 1;
									$data_arr['data'][$key_letter]['loan_details'][$letter_detail['atm_contract_number']]['check_dept'] = 1;
								}
								$ym_check[$letter_detail['atm_contract_number']]["year"] = $letter_detail['year'];
								$ym_check[$letter_detail['atm_contract_number']]["month"] = $letter_detail['month'];
							}
							if($row_letter["non_pay_year"] == $letter_detail['year'] && $row_letter["non_pay_month"] == $letter_detail['month']) {
								$data_arr['data'][$key_letter]['loan_details'][$letter_detail['atm_contract_number']]['principal_last'] = $letter_detail['principal'];
								$data_arr['data'][$key_letter]['loan_details'][$letter_detail['atm_contract_number']]['interest_last'] = $letter_detail['interest'];
								$data_arr['data'][$key_letter]['loan_details'][$letter_detail['atm_contract_number']]['principal_debt_last'] = $letter_detail['principal_debt'];
								$data_arr['data'][$key_letter]['loan_details'][$letter_detail['atm_contract_number']]['interest_debt_last'] = $letter_detail['interest_debt'];
								$data_arr['data'][$key_letter]['loan_details'][$letter_detail['atm_contract_number']]['principal_paid_last'] = $letter_detail['principal_paid'];
								$data_arr['data'][$key_letter]['loan_details'][$letter_detail['atm_contract_number']]['interest_paid_last'] = $letter_detail['interest_paid'];
							}
							$data_arr['data'][$key_letter]['paid_amount'] += $letter_detail['principal_paid'] + $letter_detail['interest_paid'];
						} elseif($letter_detail["type"] == "SHARE") {
							$data_arr['data'][$key_letter]['share']['share'] += $letter_detail['principal'];
							$data_arr['data'][$key_letter]['share']['share_dept'] += $letter_detail['principal_debt'];
							$data_arr['data'][$key_letter]['share']['share_paid'] += $letter_detail['principal_paid'];
							$data_arr['data'][$key_letter]['share']['mm_yy'][] = array('month'  => $letter_detail['month'],'year' => $letter_detail['year']);

							$letter_detail['mm_yy'] = $month_arr_2[$letter_detail['month']]." ".$letter_detail['year'];
							$data_arr['data'][$key_letter]['share_list'][] = $letter_detail;

							if(((int)$row_letter["non_pay_month"]) == ((int)$letter_detail["month"]) && $row_letter["non_pay_year"] == $letter_detail["year"]) {
								$data_arr['data'][$key_letter]['share']['share_last'] = $letter_detail['principal'];
								$data_arr['data'][$key_letter]['share']['share_dept_last'] = $letter_detail['principal_debt'];
								$data_arr['data'][$key_letter]['share']['share_paid_last'] = $letter_detail['principal_paid'];
							} else if (empty($data_arr['data'][$key_letter]['share']['share_last'])) {
								$data_arr['data'][$key_letter]['share']['share_last'] = 0;
								$data_arr['data'][$key_letter]['share']['share_dept_last'] = 0;
								$data_arr['data'][$key_letter]['share']['share_paid_last'] = 0;
							}

							//Check if has payment
							if(!empty($letter_detail['principal_paid'])) {
								$data_arr['data'][$key_letter]['check_share_dept'] = 1;
							}
						}

					}
				}
			}
		}

		//ลายเซ็นต์
		$date_signature = date('Y-m-d');
		$row = $this->db->select(array('*'))
						->from('coop_signature')
						->where("start_date <= '{$date_signature}'")
						->order_by('start_date DESC')
						->limit(1)
						->get()->result_array();
		$data_arr['signature'] = @$row[0];

		$data_arr['total_principal'] = $total_principal;
		$data_arr['total_interest'] = $total_interest;
		$data_arr['total_principal_debt'] = $total_principal_debt;
		$data_arr['total_interest_debt'] = $total_interest_debt;
		$data_arr['total_principal_share_debt'] = $total_principal_share_debt;

		$this->preview_libraries->template_preview('debt/letter_perview',$data_arr);
	}

	function check_month_letter(){	
			$non_pay_id = @$_POST['non_pay_id'];		
			$letter_month =  date('n');
			$letter_year = date('Y')+543;
			//$letter_month =  9;
			//$letter_year = 2561;
			
			
			$this->db->select(array('*'));
			$this->db->from('coop_non_pay');
			$this->db->where("non_pay_id = '{$non_pay_id}'");
			$rs_non_pay = $this->db->get()->result_array();	
			$row_non_pay = @$rs_non_pay[0];
			$non_pay_month = @$row_non_pay['non_pay_month'];
			$non_pay_year = @$row_non_pay['non_pay_year'];
			
			$this->db->select(array('*'));
			$this->db->from('coop_debt_letter');
			$this->db->where("non_pay_id = '{$non_pay_id}' AND letter_month = '{$letter_month}' AND letter_year = '{$letter_year}' AND letter_status = '1'");
			$row_check = $this->db->get()->result_array();

			if(empty($row_check) && ($letter_month >= $non_pay_month || $letter_year > $non_pay_year) && ($letter_year >= $non_pay_year)){
				$data = "ok";
			}else{
				$data = "no";
			}			
			echo $data;
	}

	function coop_non_pay_receipt_cancel() {
		$arr_data = array();
		// $this->db->trans_start();
		//Cancel Receipt If has receipt_id
		if ($_GET['receipt_id'] != '') {
			$receipt_id = $_GET['receipt_id'];

			$non_pay_details = $this->db->select("*")
										->from("coop_non_pay_receipt as t1")
										->join("coop_non_pay_detail as t2", "t2.non_pay_id = t1.non_pay_id", "inner")
										->where("t1.receipt_id = '".$receipt_id."'")
										->get()->result_array();
			$non_pay_id = '';
			foreach($non_pay_details as $non_pay_detail) {
				// echo "<pre>";var_dump($non_pay_detail);echo "</pre>";
				$non_pay_id = $non_pay_detail['non_pay_id'];
				if($non_pay_detail['deduct_code'] == "LOAN") {
					if($non_pay_detail['pay_type'] == 'principal') {
						$where_transaction = " AND principal_payment > 0";
					} else if ($non_pay_detail['pay_type'] == 'interest') {
						$where_transaction = " AND interest > 0";
					}
					$transaction = $this->db->select("*")
											->from("coop_finance_transaction")
											->where("loan_id = '".$non_pay_detail['loan_id']."' AND receipt_id = '".$receipt_id."'".$where_transaction)
											->get()->row();

					$newBalance = $non_pay_detail['non_pay_amount'];
					if (!empty($transaction)) {
						if($non_pay_detail['pay_type'] == 'principal') {
							$newBalance = $non_pay_detail['non_pay_amount_balance'] + $transaction->principal_payment;
						} else if ($non_pay_detail['pay_type'] == 'interest') {
							$newBalance = $non_pay_detail['non_pay_amount_balance'] + $transaction->interest;
						}
					}
					$detailData = array(
						'non_pay_amount_balance' => $newBalance,
					);
					$this->db->where('run_id', $non_pay_detail['run_id'])
								->update('coop_non_pay_detail', $detailData);
					
					//update rollback loan
					if($non_pay_detail['pay_type'] == 'principal') {
						$loan_id = $non_pay_detail['loan_id'];
						// $amount = $transaction->principal_payment;
						$amount = $non_pay_detail['non_pay_amount'] - $non_pay_detail['non_pay_amount_balance'];
						$temp_amount = $amount;
						if($amount==0)
							continue;
						$this->db->set("loan_amount_balance", "(SELECT loan_amount_balance + $amount FROM (SELECT * FROM coop_loan WHERE id = ".$loan_id.") AS t1 )", false);
						$this->db->where("id", $loan_id);
						$this->db->update("coop_loan");

						$this->db->where("receipt_id", $non_pay_detail['receipt_id']);
						$loan_transaction = $this->db->get("coop_loan_transaction")->result()[0];

						$this->db->where("receipt_id", $non_pay_detail['receipt_id']);
						$this->db->delete("coop_loan_transaction");

						// $this->db->where("non_pay_id", $non_pay_detail['non_pay_id']);
						// $this->db->where("loan_id", $loan_id);
						// $this->db->where("pay_type", "principal");
						// $this->db->set("non_pay_amount_balance", "non_pay_amount_balance + $temp_amount", false);
						// $this->db->update("coop_non_pay_detail");

						//รัน อัพเดท statement
						$this->update_st->update_loan_transaction($loan_id, $loan_transaction->transaction_datetime);
					}
					
				} else if ($non_pay_detail['deduct_code'] == "SHARE") {
					$transaction = $this->db->select("*")
											->from("coop_finance_transaction")
											->where("account_list_id = '16' AND receipt_id = '".$receipt_id."'")
											->get()->row();

					$newBalance = $non_pay_detail['non_pay_amount'];
					if (!empty($transaction)) {
						$newBalance = $non_pay_detail['non_pay_amount_balance'] + $transaction->principal_payment;
					}
					$detailData = array(
						'non_pay_amount_balance' => $newBalance,
					);
					$this->db->where('run_id', $non_pay_detail['run_id'])
								->update('coop_non_pay_detail', $detailData);
					//update rollback share
					if($receipt_id!=""){
						$this->db->where("share_bill", $receipt_id);
						$temp_share_transacrion = $this->db->get("coop_mem_share")->result()[0];
						$this->db->set("share_status", 3);//ยกเลิกใบเสร็จ
						$this->db->where("share_bill", $receipt_id);
						$this->db->update("coop_mem_share");
						//รัน อัพเดท statement
						$this->update_st->update_share_transaction($temp_share_transacrion->member_id, $temp_share_transacrion->share_date);
					}
					
				} else if ($non_pay_detail['deduct_code'] == "DEPOSIT") {
					$transaction = $this->db->select("*")
											->from("coop_finance_transaction")
											->where("account_list_id = '30' AND receipt_id = '".$receipt_id."' AND transaction_text LIKE '%".$non_pay_detail['deposit_account_id']."%'")
											->get()->row();
					$newBalance = $non_pay_detail['non_pay_amount'];
					if (!empty($transaction)) {
						$newBalance = $non_pay_detail['non_pay_amount_balance'] + $transaction->principal_payment;
					}
					$detailData = array(
						'non_pay_amount_balance' => $newBalance,
					);
					$this->db->where('run_id', $non_pay_detail['run_id'])
								->update('coop_non_pay_detail', $detailData);
					//update rollback DEPOSIT
					if($receipt_id!=""){
						$this->db->order_by("transaction_time", "ASC");
						$this->db->order_by("transaction_id", "ASC");
						$temp_account_transaction = $this->db->get_where("coop_account_transaction", array("receipt_id" => $receipt_id) )->result()[0];

						$data_insert['transaction_time'] 			= date("Y-m-d H:i:s");
						$data_insert['transaction_list'] 			= "CANCEL";
						$data_insert['transaction_withdrawal']		= 0;
						$data_insert['transaction_deposit']			= $temp_account_transaction->transaction_deposit * -1;
						$data_insert['transaction_balance']			= $temp_account_transaction->transaction_balance + $data_insert['transaction_deposit'];
						$data_insert['transaction_no_in_balance']	= $temp_account_transaction->transaction_no_in_balance + $data_insert['transaction_deposit'];
						$data_insert['account_id']					= $temp_account_transaction->account_id;
						$data_insert['user_id']						= $_SESSION['USER_ID'];

						//ดึงข้อมูลลำดับรายการ ของรายการถัดไป
						$arr_seq = array(); 
						$arr_seq['account_id'] = $data_insert['account_id'];
						$arr_seq['transaction_list'] = $data_insert['transaction_list']; 
						$seq_no = $this->deposit_seq->gen_seq_account_transaction($arr_seq);
						$data_insert['seq_no'] =  @$seq_no;

						$this->db->insert("coop_account_transaction", $data_insert);

						//รัน อัพเดท statement
						$this->update_st->update_deposit_transaction($temp_account_transaction->account_id, $temp_account_transaction->transaction_time);
					}
				} else if ($non_pay_detail['deduct_code'] == "CREMATION") {
					$transaction = $this->db->select("*")
											->from("coop_finance_transaction")
											->where("account_list_id = '28' AND receipt_id = '".$receipt_id."'")
											->get()->row();
					$newBalance = $non_pay_detail['non_pay_amount'];
					if (!empty($transaction)) {
						$newBalance = $non_pay_detail['non_pay_amount_balance'] + $transaction->principal_payment;
					}
					$detailData = array(
						'non_pay_amount_balance' => $newBalance,
					);
					$this->db->where('run_id', $non_pay_detail['run_id'])
								->update('coop_non_pay_detail', $detailData);
				} else if ($non_pay_detail['deduct_code'] == "ATM") {
					$transaction = $this->db->select("*")
											->from("coop_finance_transaction")
											->where("loan_atm_id = '".$non_pay_detail['loan_atm_id']."' AND receipt_id = '".$receipt_id."'")
											->get()->row();
					$newBalance = $non_pay_detail['non_pay_amount'];
					if (!empty($transaction)) {
						if($non_pay_detail['pay_type'] == 'principal') {
							$newBalance = $non_pay_detail['non_pay_amount_balance'] + $transaction->principal_payment;
						} else if ($non_pay_detail['pay_type'] == 'interest') {
							$newBalance = $non_pay_detail['non_pay_amount_balance'] + $transaction->interest;
						}
					}
					$detailData = array(
						'non_pay_amount_balance' => $newBalance,
					);
					$this->db->where('run_id', $non_pay_detail['run_id'])
								->update('coop_non_pay_detail', $detailData);
					//update rollback loan
					if($non_pay_detail['pay_type'] == 'principal') {
						$loan_atm_id = $non_pay_detail['loan_atm_id'];
						
						$amount = $non_pay_detail['non_pay_amount'] - $non_pay_detail['non_pay_amount_balance'];
						$temp_amount = $amount;
						$this->db->set("total_amount_balance", "(SELECT total_amount_balance - $amount FROM (SELECT * FROM coop_loan_atm WHERE loan_atm_id = ".$loan_atm_id.") AS t1 )", false);
						$this->db->where("loan_atm_id", $loan_atm_id);
						$this->db->update("coop_loan_atm");

						$this->db->where("loan_amount != loan_amount_balance");
						$this->db->order_by("loan_atm_id", "DESC");
						$loan_atm_detail = $this->db->get_where("coop_loan_atm_detail", array("loan_atm_id" =>  $loan_atm_id) );
						foreach ($loan_atm_detail->result() as $key => $value) {
							if($amount <= 0)
								break;
							if($value->loan_amount_balance + $amount <= $value->loan_amount){
								$this->db->set("loan_amount_balance", ($value->loan_amount_balance + $amount));
							}else{
								$this->db->set("loan_amount_balance", $value->loan_amount);
								$amount = $amount - ($value->loan_amount - $value->loan_amount_balance);
							}
							$this->db->where("loan_id", $value->loan_id);
							$this->db->update("coop_loan_atm_detail");
						}

						$this->db->where("non_pay_id", $non_pay_detail['non_pay_id']);
						$this->db->where("loan_atm_id", $loan_atm_id);
						$this->db->where("pay_type", "principal");
						$this->db->set("non_pay_amount_balance", "non_pay_amount_balance + $temp_amount", false);
						$this->db->update("coop_non_pay_detail");

						$this->db->where("receipt_id", $non_pay_detail['receipt_id']);
						$loan_transaction = $this->db->get("coop_loan_atm_transaction")->result()[0];

						$this->db->where("receipt_id", $non_pay_detail['receipt_id']);
						$this->db->delete("coop_loan_atm_transaction");
						//รัน อัพเดท statement
						$this->update_st->update_loan_atm_transaction($loan_atm_id, $loan_transaction->transaction_datetime);
					}
				} else if ($non_pay_detail['deduct_code'] == "GUARANTEE_AMOUNT") {
					$transaction = $this->db->select("*")
											->from("coop_finance_transaction")
											->where("account_list_id = '36' AND receipt_id = '".$receipt_id."'")
											->get()->row();
					$newBalance = $non_pay_detail['non_pay_amount'];
					if (!empty($transaction)) {
						$newBalance = $non_pay_detail['non_pay_amount_balance'] + $transaction->principal_payment;
					}
					$detailData = array(
						'non_pay_amount_balance' => $newBalance,
					);
					$this->db->where('run_id', $non_pay_detail['run_id'])
								->update('coop_non_pay_detail', $detailData);
				}
			}


			$new_non_pay_details = $this->db->query("SELECT *, sum(non_pay_amount_balance) as sum_balance FROM coop_non_pay_detail WHERE non_pay_id = '".$non_pay_id."'")->row();
            $new_balance = $new_non_pay_details->sum_balance;
			$nonPayData = array();
            if($new_balance <= 0) {
                $new_balance = 0;
				$nonPayData["non_pay_status"] = "2";
				$nonPayData["updatetimestamp"] = date('Y-m-d H:i:s');
				$nonPayData["non_pay_amount_balance"] = $new_balance;
            } else {
				$nonPayData["non_pay_status"] = "1";
				$nonPayData["updatetimestamp"] = date('Y-m-d H:i:s');
				$nonPayData["non_pay_amount_balance"] = $new_balance;
			}

			$this->db->where('non_pay_id', $non_pay_id)
				->update('coop_non_pay', $nonPayData);

			$this->db->set("receipt_status", 1);
			$this->db->set("updatetime", date("Y-m-d H:i:s") );
			$this->db->set("user_id", $_SESSION['USER_ID']);
			$this->db->where("non_pay_id", $non_pay_id);
			$this->db->where("receipt_id", $receipt_id);
			$this->db->update("coop_non_pay_receipt");

			$receiptData = 	array(
								'cancel_by' => $_SESSION['USER_ID'],
								'receipt_status' => '2',
								'cancel_date' =>date('Y-m-d H:i:s')
							);
			$this->db->where('receipt_id', $receipt_id)
					->update('coop_receipt', $receiptData);
		}
		

		$arr_data['month_arr'] = array('1'=>'มกราคม','2'=>'กุมภาพันธ์','3'=>'มีนาคม','4'=>'เมษายน','5'=>'พฤษภาคม','6'=>'มิถุนายน','7'=>'กรกฎาคม','8'=>'สิงหาคม','9'=>'กันยายน','10'=>'ตุลาคม','11'=>'พฤศจิกายน','12'=>'ธันวาคม');

		$x=0;
		$join_arr = array();
		$join_arr[$x]['table'] = 'coop_non_pay as t2';
		$join_arr[$x]['condition'] = 't1.non_pay_id = t2.non_pay_id';
		$join_arr[$x]['type'] = 'inner';
		$x++;
		$join_arr[$x]['table'] = 'coop_receipt as t3';
		$join_arr[$x]['condition'] = 't1.receipt_id = t3.receipt_id';
		$join_arr[$x]['type'] = 'inner';
		$x++;
		$join_arr[$x]['table'] = 'coop_mem_apply as t4';
		$join_arr[$x]['condition'] = 't2.member_id = t4.member_id';
		$join_arr[$x]['type'] = 'inner';
		$x++;
		$join_arr[$x]['table'] = 'coop_user as t5';
		$join_arr[$x]['condition'] = 't2.pay_admin_id = t5.user_id';
		$join_arr[$x]['type'] = 'left';	
		$x++;
		$join_arr[$x]['table'] = 'coop_prename as t6';
		$join_arr[$x]['condition'] = 't4.prename_id = t6.prename_id';
		$join_arr[$x]['type'] = 'left';

		$where = "1=1";

		if($_GET['search_list'] == 'member_id'){
			$where .= " AND t4.member_id LIKE '%".$_GET['search_text']."%'";
		}else if($_GET['search_list'] == 'firstname_th'){
			$where .= " AND t4.firstname_th LIKE '%".$_GET['search_text']."%'";
		}else if($_GET['search_list'] == 'lastname_th'){
			$where .= " AND t4.lastname_th LIKE '%".$_GET['search_text']."%'";
		}else if($_GET['search_list'] == 'receipt_id'){
			$where .= " AND t1.receipt_id LIKE '%".$_GET['search_text']."%'";
		}else if($_GET['search_list'] == 'employee_id'){
			$where .= " AND t4.employee_id LIKE '%".$_GET['search_text']."%'";
		}

		$this->paginater_all->type(DB_TYPE);
		$this->paginater_all->select('*');
		$this->paginater_all->main_table('coop_non_pay_receipt as t1');
		$this->paginater_all->page_now(@$_GET["page"]);
		$this->paginater_all->per_page(20);
		$this->paginater_all->page_link_limit(20);
		$this->paginater_all->where($where);
		$this->paginater_all->order_by('t2.updatetimestamp DESC');
		$this->paginater_all->join_arr($join_arr);
		$row = $this->paginater_all->paginater_process();

		$paging = $this->pagination_center->paginating($row['page'], $row['num_rows'], $row['per_page'], $row['page_link_limit'],$_GET);//$page_now = 1, $row_total = 1, $per_page = 20, $page_limit = 20
		$i = $row['page_start'];

		$arr_data['num_rows'] = $row['num_rows'];
		$arr_data['paging'] = $paging;
		$arr_data['data'] = $row['data'];
		$arr_data['i'] = $i;
		$this->db->trans_complete();
		$this->libraries->template('debt/coop_non_pay_receipt_cancel',$arr_data);
	}

	
	public function debt_dismiss_member() {
		$arr_data = array();
		$share_debt_limit = $this->db->select("*")
										->from("coop_debt_letter_setting")
										->where("setting_code = 'share_debt'")
										->get()->row();
		$num_share_month_limit = $share_debt_limit->num_letter;

		$loan_debt_limit = $this->db->select("*")
										->from("coop_debt_letter_setting")
										->where("setting_code = 'loan_debt'")
										->get()->row();
		$num_loan_month_limit = $loan_debt_limit->num_letter;

		$where = "";
		if(!empty($_GET['search_member'])){
			$where .= " AND (t2.firstname_th LIKE '%{$_GET['search_member']}%'
			OR t2.lastname_th LIKE '%{$_GET['search_member']}%'
			OR t2.member_id LIKE '%{$_GET['search_member']}%')";
		}
		if(!empty($_GET['department'])){
			$where .= " AND t2.department = '{$_GET['department']}'";
		}
		if(!empty($_GET['faction'])){
			$where .= " AND t2.faction = '{$_GET['faction']}'";
		}
		if(!empty($_GET['level'])){
			$where .= " AND t2.level = '{$_GET['level']}'";
		}

		// $non_pays = $this->db->select("t1.member_id, t1.deduct_code, count(DISTINCT t1.non_pay_id) as count, t3.prename_full, t2.firstname_th, t2.lastname_th")
		// 						->from("coop_non_pay_detail as t1")
		// 						->join("coop_mem_apply as t2", "t1.member_id = t2.member_id", "left")
		// 						->join("coop_prename as t3", "t2.prename_id = t3.prename_id", "left")
		// 						->where("non_pay_amount_balance > 0 AND t2.mem_type in (1,3) AND deduct_code in ('SHARE', 'LOAN', 'ATM')".$where)
		// 						->group_by("t1.member_id, t1.deduct_code, t1.loan_id, t1.loan_atm_id")
		// 						->having("count(DISTINCT t1.non_pay_id) > ".$month_limit)
		// 						->get()->result_array();
		$non_pays = $this->db->select("t1.member_id, t1.deduct_code, t3.prename_full, t2.firstname_th, t2.lastname_th, t1.loan_id, t1.loan_atm_id")
								->from("coop_non_pay_detail as t1")
								->join("coop_mem_apply as t2", "t1.member_id = t2.member_id", "left")
								->join("coop_prename as t3", "t2.prename_id = t3.prename_id", "left")
								->where("non_pay_amount_balance > 0 AND t2.mem_type not in (2,4,5) AND deduct_code in ('SHARE', 'LOAN', 'ATM')".$where)
								->group_by("t1.member_id, t1.deduct_code, t1.loan_id, t1.loan_atm_id, t1.non_pay_id")
								->get()->result_array();
		$count = array();
		$datas = array();
		foreach($non_pays as $non_pay) {
			if ($non_pay["deduct_code"] == "SHARE") {
				$count[$non_pay["member_id"]]["SHARE"] += 1;
				if($count[$non_pay["member_id"]]["SHARE"] >= $num_share_month_limit) {
					$datas[$non_pay["member_id"]] = $non_pay;
				}
			} elseif ($non_pay["deduct_code"] == "LOAN") {
				$count[$non_pay["member_id"]]["LOAN"][$non_pay["loan_id"]] += 1;
				if($count[$non_pay["member_id"]]["LOAN"][$non_pay["loan_id"]] >= $num_loan_month_limit) {
					$datas[$non_pay["member_id"]] = $non_pay;
				}
			} elseif ($non_pay["deduct_code"] == "ATM") {
				$count[$non_pay["member_id"]]["ATM"][$non_pay["loan_atm_id"]] += 1;
				if($count[$non_pay["member_id"]]["ATM"][$non_pay["loan_atm_id"]] >= $num_loan_month_limit) {
					$datas[$non_pay["member_id"]] = $non_pay;
				}
			}
			
			
		}
		$arr_data['datas'] = $datas;

		$this->libraries->template('debt/debt_dismiss_member',$arr_data);
	}

	public function print_warning_dismiss_letter() {
		$arr_data = array();

		$member_ids = array();
		if(!empty($_POST['member_ids'])) {
			$member_ids = $_POST['member_ids'];
		} else {
			$member_ids[] = $_GET["member_id"];
		}

		//Save data
		// if(!empty($_GET['member_id'])) {
		// 	$member_id = $_GET['member_id'];
		// 	$letter_id = $this->save_warning_dismiss_letter($member_id);
		// } else if(!empty($_POST['member_ids'])) {
		// 	foreach($_POST['member_ids'] as $member_id) {
		// 		$letter_id = $this->save_warning_dismiss_letter($member_id);
		// 	}
		// }

		//Get Raw Data
		$share_debt_limit = $this->db->select("*")
										->from("coop_debt_letter_setting")
										->where("setting_code = 'share_debt'")
										->get()->row();
		$num_share_month_limit = $share_debt_limit->num_letter;
		$arr_data["num_share_month_limit"] = $num_share_month_limit;

		$loan_debt_limit = $this->db->select("*")
										->from("coop_debt_letter_setting")
										->where("setting_code = 'loan_debt'")
										->get()->row();
		$num_loan_month_limit = $loan_debt_limit->num_letter;
		$arr_data["num_loan_month_limit"] = $num_loan_month_limit;

		$non_pays = $this->db->select("t1.member_id,
										t1.deduct_code,
										t1.loan_id,
										t1.loan_atm_id,
										t3.prename_full,
										t2.firstname_th,
										t2.lastname_th,
										t4.contract_number,
										t4.loan_amount,
										t5.contract_number as atm_contract_number,
										t5.total_amount as atm_total_amount,
										t6.non_pay_month,
										t6.non_pay_year,
										coop_loan_type.loan_type")
								->from("coop_non_pay_detail as t1")
								->join("coop_mem_apply as t2", "t1.member_id = t2.member_id", "left")
								->join("coop_prename as t3", "t2.prename_id = t3.prename_id", "left")
								->join("coop_loan as t4", "t1.loan_id = t4.id", "left")
								->join("coop_loan_atm as t5", "t1.loan_atm_id = t5.loan_atm_id", "left")
								->join("coop_non_pay as t6", "t1.non_pay_id = t6.non_pay_id", "inner")
								->join("coop_loan_name","t4.loan_type = coop_loan_name.loan_name_id","left")
								->join("coop_loan_type","coop_loan_name.loan_type_id = coop_loan_type.id","left")
								->where("t1.non_pay_amount_balance > 0 AND t1.deduct_code in ('SHARE', 'LOAN', 'ATM') AND t1.member_id IN (".implode(',',$member_ids).") AND t2.mem_type not in (2,4,5)")
								->group_by("t1.member_id, t1.deduct_code, t1.loan_id, t1.loan_atm_id, t1.non_pay_id")
								->get()->result_array();

		//Get guarantee person
		$loan_ids = array_filter(array_column($non_pays, 'loan_id'));
		if(!empty($loan_ids)) {
			$guarantees = $this->db->select("t1.loan_id,
												t1.guarantee_person_id as member_id,
												t3.prename_full,
												t2.firstname_th,
												t2.lastname_th")
									->from("coop_loan_guarantee_person as t1")
									->join("coop_mem_apply as t2", "t1.guarantee_person_id = t2.member_id", "left")
									->join("coop_prename as t3", "t2.prename_id = t3.prename_id", "left")
									->WHERE("t1.loan_id IN (".implode(',',$loan_ids).")")
									->get()->result_array();
			$gua_loan_ids = array_column($guarantees, 'loan_id');
		}

		//Render data
		$datas = array();
		$first_letter = "x";
		foreach($non_pays as $non_pay) {
			$datas[$non_pay["member_id"]]["name"] = $non_pay["prename_full"].$non_pay["firstname_th"]." ".$non_pay["lastname_th"];
			if($non_pay["deduct_code"] == "SHARE") {
				$period = array();
				$period["month"] = $non_pay["non_pay_month"];
				$period["year"] = $non_pay["non_pay_year"];
				$datas[$non_pay["member_id"]]["share"]["period"][] = $period;
				$datas[$non_pay["member_id"]]["share"]["period_count"] += 1;
				if($datas[$non_pay["member_id"]]["share"]["period_count"] >= $num_share_month_limit) {
					$datas[$non_pay["member_id"]]["share"]["over_limit"] = 1;
				} else {
					$datas[$non_pay["member_id"]]["share"]["over_limit"] = 0;
				}
			} else if ($non_pay["deduct_code"] == "LOAN") {
				$period = array();
				$period["month"] = $non_pay["non_pay_month"];
				$period["year"] = $non_pay["non_pay_year"];
				$datas[$non_pay["member_id"]]["loans"][$non_pay["contract_number"]]["period"][] = $period;
				$datas[$non_pay["member_id"]]["loans"][$non_pay["contract_number"]]["loan_id"] = $non_pay["loan_id"];
				$datas[$non_pay["member_id"]]["loans"][$non_pay["contract_number"]]["balance"] = $non_pay["loan_amount"];
				$datas[$non_pay["member_id"]]["loans"][$non_pay["contract_number"]]["loan_type"] = $non_pay["loan_type"];
				$datas[$non_pay["member_id"]]["loans"][$non_pay["contract_number"]]["period_count"] += 1;
				if($datas[$non_pay["member_id"]]["loans"][$non_pay["contract_number"]]["period_count"] >= $num_loan_month_limit) {
					$datas[$non_pay["member_id"]]["has_loan"] = 1;
					$datas[$non_pay["member_id"]]["loans"][$non_pay["contract_number"]]["over_limit"] = 1;
					if(!empty($guarantees)) {
						$gua_indexs = array_keys($gua_loan_ids, $non_pay["loan_id"]);
						$loan_guarantees = array();
						foreach($gua_indexs as $index) {
							$guarantee = $guarantees[$index];
							$loan_guarantees["name"] = $guarantee["prename_full"].$guarantee["firstname_th"]." ".$guarantee["lastname_th"];
							$loan_guarantees["member_id"] = $guarantee["member_id"];
							$loan_guarantees["contract_number"] = $non_pay["contract_number"];
							$loan_guarantees["loan_id"] = $non_pay["loan_id"];
							$loan_guarantees["balance"] = $non_pay["loan_amount"];
							$loan_guarantees["loan_type"] = $non_pay["loan_type"];
						}
						$datas[$non_pay["member_id"]]["guarantee"][$non_pay["contract_number"]] = $loan_guarantees;
					}
				} else {
					$datas[$non_pay["member_id"]]["loans"][$non_pay["contract_number"]]["over_limit"] = 0;
				}
			} else if ($non_pay["deduct_code"] == "ATM") {
				$period = array();
				$period["month"] = $non_pay["non_pay_month"];
				$period["year"] = $non_pay["non_pay_year"];
				$datas[$non_pay["member_id"]]["loans"][$non_pay["atm_contract_number"]]["period"][] = $period;
				$datas[$non_pay["member_id"]]["loans"][$non_pay["atm_contract_number"]]["loan_id"] = $non_pay["loan_atm_id"];
				$datas[$non_pay["member_id"]]["loans"][$non_pay["atm_contract_number"]]["balance"] = $non_pay["atm_total_amount"];
				$datas[$non_pay["member_id"]]["loans"][$non_pay["atm_contract_number"]]["loan_type"] = "เงินกู้ฉุกเฉิน";
				$datas[$non_pay["member_id"]]["loans"][$non_pay["atm_contract_number"]]["period_count"] += 1;
				if($datas[$non_pay["member_id"]]["loans"][$non_pay["atm_contract_number"]]["period_count"] >= $num_loan_month_limit) {
					$datas[$non_pay["member_id"]]["has_loan"] = 1;
					$datas[$non_pay["member_id"]]["loans"][$non_pay["atm_contract_number"]]["over_limit"] = 1;
				} else {
					$datas[$non_pay["member_id"]]["loans"][$non_pay["atm_contract_number"]]["over_limit"] = 0;
				}
			}
		}

		$date_signature = date('Y-m-d');
		$row = $this->db->select(array('*'))
						->from('coop_signature')
						->where("start_date <= '{$date_signature}'")
						->order_by('start_date DESC')
						->limit(1)
						->get()->result_array();
		$data_arr['signature'] = @$row[0];
		$row_profile = $this->db->from('coop_profile')
								->limit(1)
								->get()->result_array();
		$data_arr['row_profile'] = $row_profile[0];

		$data_arr['month_arr'] = $this->month_arr;
		$data_arr['datas'] = $datas;

		$this->preview_libraries->template_preview('debt/warning_letter',$data_arr);
	}

	public function save_warning_dismiss_letter($member_id) {
		$result = array();
		$process_date = date('Y-m-d H:i:s');
		$share_debt_limit = $this->db->select("*")
										->from("coop_debt_letter_setting")
										->where("setting_code = 'share_debt'")
										->get()->row();
		$month_limit = $share_debt_limit->num_letter;

		$non_pays = $this->db->select("t1.member_id,
										t1.deduct_code,
										t1.loan_id,
										t1.loan_atm_id,
										count(DISTINCT t1.non_pay_id) as count,
										t3.prename_full,
										t2.firstname_th,
										t2.lastname_th,
										t4.contract_number,
										t4.loan_amount,
										t5.contract_number as atm_contract_number,
										t5.total_amount as atm_total_amount")
								->from("coop_non_pay_detail as t1")
								->join("coop_mem_apply as t2", "t1.member_id = t2.member_id", "left")
								->join("coop_prename as t3", "t2.prename_id = t3.prename_id", "left")
								->join("coop_loan as t4", "t1.loan_id = t4.id", "left")
								->join("coop_loan_atm as t5", "t1.loan_atm_id = t5.loan_atm_id", "left")
								->where("non_pay_amount_balance > 0 AND deduct_code in ('SHARE', 'LOAN', 'ATM') AND t1.member_id = '{$member_id}'")
								->group_by("t1.member_id, t1.deduct_code, t1.loan_id, t1.loan_atm_id")
								->having("count(DISTINCT t1.non_pay_id) > ".$month_limit)
								->get()->result_array();

		$data_insert = array();
		$data_insert['print_date'] = $process_date;
		$data_insert['member_id'] = $member_id;
		$this->db->insert('coop_debt_warning_letter', $data_insert);
		$letter_id = $this->db->insert_id();

		$detail_results = array();
		$detail_inserts = array();
		foreach($non_pays as $non_pay) {
			$non_pay_details = $this->db->select("t2.non_pay_month, t2.non_pay_year, t1.loan_id, t1.loan_atm_id")
										->from("coop_non_pay_detail as t1")
										->join("coop_non_pay as t2", "t1.non_pay_id = t2.non_pay_id", "inner")
										->where("t1.member_id = '{$member_id}' AND t1.deduct_code = '".$non_pay["deduct_code"]."' AND t1.non_pay_amount_balance > 0")
										->group_by("t1.non_pay_id")
										->get()->result_array();

			foreach($non_pay_details as $detail) {
				$data_insert = array();
				$data_insert['letter_id'] = $letter_id;
				$data_insert['month'] = $detail['non_pay_month'];
				$data_insert['year'] = $detail['non_pay_year'];
				$data_insert['loan_id'] = !empty($detail['loan_id']) ? $detail['loan_id'] : null;
				$data_insert['loan_atm_id'] = !empty($detail['loan_atm_id']) ? $detail['loan_atm_id'] : null;
				$detail_inserts[] = $data_insert;
			}
		}
		$this->db->insert_batch('coop_debt_warning_letter_detail', $detail_inserts);

		return $letter_id;
	}

	public function print_dismiss_letter_backup() {
		$member_ids = array();
		if(!empty($_POST['member_ids'])) {
			$member_ids = $_POST['member_ids'];
		} else {
			$member_ids = $_POST["member_id"];
		}

		$meeting_date_arr = explode('/',$_POST["meeting_date"]);
		$meeting_day = stripslashes($meeting_date_arr[0]);
		$meeting_month = stripslashes($meeting_date_arr[1]);
		$meeting_year = stripslashes($meeting_date_arr[2]) - 543;
		$meeting_date = $meeting_year."-".$meeting_month."-".$meeting_day;

		$process_date = date('Y-m-d H:i:s');
		
		$agenda = $_POST["agenda"];
		$committee_group = $_POST["committee_group"];

		$non_pays = $this->db->select("t1.member_id,
										t1.deduct_code,
										t1.loan_id,
										t1.loan_atm_id,
										t3.prename_full,
										t2.firstname_th,
										t2.lastname_th,
										t4.contract_number,
										t4.loan_amount,
										t4.approve_date,
										t5.contract_number as atm_contract_number,
										t5.total_amount as atm_total_amount,
										t5.approve_date as atm_approve_date,
										t6.non_pay_month,
										t6.non_pay_year,
										coop_loan_type.loan_type")
								->from("coop_non_pay_detail as t1")
								->join("coop_mem_apply as t2", "t1.member_id = t2.member_id", "left")
								->join("coop_prename as t3", "t2.prename_id = t3.prename_id", "left")
								->join("coop_loan as t4", "t1.loan_id = t4.id", "left")
								->join("coop_loan_atm as t5", "t1.loan_atm_id = t5.loan_atm_id", "left")
								->join("coop_non_pay as t6", "t1.non_pay_id = t6.non_pay_id", "inner")
								->join("coop_loan_name","t4.loan_type = coop_loan_name.loan_name_id","left")
								->join("coop_loan_type","coop_loan_name.loan_type_id = coop_loan_type.id","left")
								->where("t1.non_pay_amount_balance > 0 AND t1.deduct_code in ('SHARE', 'LOAN', 'ATM') AND t1.member_id IN ({$member_ids}) AND t2.mem_type not in (2,4,5)")
								->group_by("t1.member_id, t1.deduct_code, t1.loan_id, t1.loan_atm_id, t1.non_pay_id")
								->get()->result_array();

		//Get Shares
		// $shares = $this->db->select("member_id, share_collect_value")
		// 					->from("coop_mem_share")
		// 					->where("member_id IN ({$member_ids}) AND share_status NOT IN (0,3)")
		// 					->order_by("share_date DESC, share_id DESC")
		// 					->get()->result_array();
		// $share_members = array_column($shares, 'member_id');

		//GET Bank Account balance
		// $account_balances = $this->db->select("t1.mem_id as member_id, t1.account_id, t2.transaction_balance")
		// 								->from("coop_maco_account as t1")
		// 								->join("coop_account_transaction as t2", "t1.account_id = t2.account_id", "left")
		// 								->where("t1.mem_id IN ({$member_ids}) AND t1.account_status = 0 AND t2.cancel_status IS NULL")
		// 								->order_by("t2.transaction_time DESC, t2.transaction_id DESC")
		// 								->get()->result_array();
		// $account_members = array_column($account_balances, 'member_id');
		// $account_ids = array_column($account_balances, 'account_id');

		//Get guarantee person
		// $loan_ids = array_filter(array_column($non_pays, 'loan_id'));
		// if(!empty($loan_ids)) {
		// 	$guarantees = $this->db->select("t1.loan_id,
		// 										t1.guarantee_person_id as member_id,
		// 										t3.prename_full,
		// 										t2.firstname_th,
		// 										t2.lastname_th")
		// 							->from("coop_loan_guarantee_person as t1")
		// 							->join("coop_mem_apply as t2", "t1.guarantee_person_id = t2.member_id", "left")
		// 							->join("coop_prename as t3", "t2.prename_id = t3.prename_id", "left")
		// 							->WHERE("t1.loan_id IN (".implode(',',$loan_ids).")")
		// 							->get()->result_array();
		// 	$gua_loan_ids = array_column($guarantees, 'loan_id');
		// }

		//Render data
		$datas = array();
		
		$first_letter = "x";
		foreach($non_pays as $non_pay) {
			$datas[$non_pay["member_id"]]["name"] = $non_pay["prename_full"].$non_pay["firstname_th"]." ".$non_pay["lastname_th"];
			if($non_pay["deduct_code"] == "SHARE") {
				$period = array();
				$period["month"] = $non_pay["non_pay_month"];
				$period["year"] = $non_pay["non_pay_year"];
				$datas[$non_pay["member_id"]]["share"]["period"][] = $period;
				$datas[$non_pay["member_id"]]["share"]["period_count"] += 1;
				if($datas[$non_pay["member_id"]]["share"]["period_count"] >= $num_share_month_limit) {
					$datas[$non_pay["member_id"]]["share"]["over_limit"] = 1;
				} else {
					$datas[$non_pay["member_id"]]["share"]["over_limit"] = 0;
				}
			} else if ($non_pay["deduct_code"] == "LOAN") {
				$period = array();
				$period["month"] = $non_pay["non_pay_month"];
				$period["year"] = $non_pay["non_pay_year"];
				$datas[$non_pay["member_id"]]["loans"][$non_pay["contract_number"]]["period"][] = $period;
				$datas[$non_pay["member_id"]]["loans"][$non_pay["contract_number"]]["loan_id"] = $non_pay["loan_id"];
				$datas[$non_pay["member_id"]]["loans"][$non_pay["contract_number"]]["balance"] = $non_pay["loan_amount"];
				$datas[$non_pay["member_id"]]["loans"][$non_pay["contract_number"]]["approve_date"] = $non_pay["approve_date"];
				$datas[$non_pay["member_id"]]["loans"][$non_pay["contract_number"]]["loan_type"] = $non_pay["loan_type"];
				$datas[$non_pay["member_id"]]["loans"][$non_pay["contract_number"]]["is_atm"] = 0;
				$datas[$non_pay["member_id"]]["loans"][$non_pay["contract_number"]]["period_count"] += 1;
				if($datas[$non_pay["member_id"]]["loans"][$non_pay["contract_number"]]["period_count"] >= $num_loan_month_limit) {
					$datas[$non_pay["member_id"]]["has_loan"] = 1;
					$datas[$non_pay["member_id"]]["loans"][$non_pay["contract_number"]]["over_limit"] = 1;
					// if(!empty($guarantees)) {
					// 	$gua_indexs = array_keys($gua_loan_ids, $non_pay["loan_id"]);
					// 	$loan_guarantees = array();
					// 	foreach($gua_indexs as $index) {
					// 		$guarantee = $guarantees[$index];
					// 		$loan_guarantees["name"] = $guarantee["prename_full"].$guarantee["firstname_th"]." ".$guarantee["lastname_th"];
					// 		$loan_guarantees["member_id"] = $guarantee["member_id"];
					// 		$loan_guarantees["contract_number"] = $non_pay["contract_number"];
					// 		$loan_guarantees["loan_id"] = $non_pay["loan_id"];
					// 		$loan_guarantees["balance"] = $non_pay["loan_amount"];
					// 		$loan_guarantees["loan_type"] = $non_pay["loan_type"];
					// 	}
					// 	$datas[$non_pay["member_id"]]["guarantee"][$non_pay["contract_number"]] = $loan_guarantees;
					// }
				} else {
					$datas[$non_pay["member_id"]]["loans"][$non_pay["contract_number"]]["over_limit"] = 0;
				}
			} else if ($non_pay["deduct_code"] == "ATM") {
				$period = array();
				$period["month"] = $non_pay["non_pay_month"];
				$period["year"] = $non_pay["non_pay_year"];
				$datas[$non_pay["member_id"]]["loans"][$non_pay["atm_contract_number"]]["period"][] = $period;
				$datas[$non_pay["member_id"]]["loans"][$non_pay["atm_contract_number"]]["loan_id"] = $non_pay["loan_atm_id"];
				$datas[$non_pay["member_id"]]["loans"][$non_pay["atm_contract_number"]]["balance"] = $non_pay["atm_total_amount"];
				$datas[$non_pay["member_id"]]["loans"][$non_pay["atm_contract_number"]]["loan_type"] = "เงินกู้ฉุกเฉิน";
				$datas[$non_pay["member_id"]]["loans"][$non_pay["atm_contract_number"]]["approve_date"] = $non_pay["atm_approve_date"];
				$datas[$non_pay["member_id"]]["loans"][$non_pay["atm_contract_number"]]["is_atm"] = 1;
				$datas[$non_pay["member_id"]]["loans"][$non_pay["atm_contract_number"]]["period_count"] += 1;
				if($datas[$non_pay["member_id"]]["loans"][$non_pay["atm_contract_number"]]["period_count"] >= $num_loan_month_limit) {
					$datas[$non_pay["member_id"]]["has_loan"] = 1;
					$datas[$non_pay["member_id"]]["loans"][$non_pay["atm_contract_number"]]["over_limit"] = 1;
				} else {
					$datas[$non_pay["member_id"]]["loans"][$non_pay["atm_contract_number"]]["over_limit"] = 0;
				}
			}
		}

		$dismiss_letter_details = array();
		$letter_ids = array();
		foreach($datas as $member_id=>$data) {
			//Collect Letter Data
			$letter = $this->db->select("id")
								->from("coop_debt_dismiss_letter")
								->where("status = 1 AND member_id = '{$member_id}'")
								->get()->row();

			if(!empty($letter)) {
				$letter_ids[] = $letter->id;
			} else {
				$dismiss_letters = array();
				$dismiss_letter["member_id"] = $member_id;
				$dismiss_letter["print_date"] = $process_date;
				$dismiss_letter["meeting_date"] = $meeting_date;
				$dismiss_letter["agenda"] = $agenda;
				$dismiss_letter["committee_group"] = $committee_group;
				$dismiss_letter["status"] = 1;
				$this->db->insert('coop_debt_dismiss_letter', $dismiss_letter);
				$letter_id = $this->db->insert_id();
				$letter_ids[] = $letter_id;

				if(!empty($data["share"]) && !empty($data["share"]["over_limit"])) {
					foreach($data["share"]["period"] as $period) {
						$dismiss_letter_detail = array();
						$dismiss_letter_detail["letter_id"] = $letter_id;
						$dismiss_letter_detail["loan_id"] = null;
						$dismiss_letter_detail["loan_atm_id"] = null;
						$dismiss_letter_detail["month"] = $period["month"];
						$dismiss_letter_detail["year"] = $period["year"];
						$dismiss_letter_details[] = $dismiss_letter_detail;
					}
				}
				if(!empty($data["loans"])) {
					foreach($data["loans"] as $loan) {
						if(!empty($loan["over_limit"])) {
							if(empty($loan["is_atm"])) {
								foreach($loan["period"] as $period) {
									$dismiss_letter_detail = array();
									$dismiss_letter_detail["letter_id"] = $letter_id;
									$dismiss_letter_detail["loan_id"] = $loan["loan_id"];
									$dismiss_letter_detail["loan_atm_id"] = null;
									$dismiss_letter_detail["month"] = $period["month"];
									$dismiss_letter_detail["year"] = $period["year"];
									$dismiss_letter_details[] = $dismiss_letter_detail;
								}
							} else {
								foreach($loan["period"] as $period) {
									$dismiss_letter_detail = array();
									$dismiss_letter_detail["letter_id"] = $letter_id;
									$dismiss_letter_detail["loan_id"] = null;
									$dismiss_letter_detail["loan_atm_id"] = $loan["loan_id"];
									$dismiss_letter_detail["month"] = $period["month"];
									$dismiss_letter_detail["year"] = $period["year"];
									$dismiss_letter_details[] = $dismiss_letter_detail;
								}
							}
						}
					}
				}
				

				// $cal_loan_interest = array();
				// $cal_loan_interest['loan_id'] = @$value['id'];
				// $cal_loan_interest['date_interesting'] = $this->center_function->ConvertToSQLDate(@$date_interesting);
				// $interest_data = $this->loan_libraries->cal_loan_interest($cal_loan_interest);


			}
		}

		if(!empty($dismiss_letter_details)) {
			$this->db->insert_batch('coop_debt_dismiss_letter_detail', $dismiss_letter_details);
		}

		$date_signature = date('Y-m-d');
		$row = $this->db->select(array('*'))
						->from('coop_signature')
						->where("start_date <= '{$date_signature}'")
						->order_by('start_date DESC')
						->limit(1)
						->get()->result_array();
		$data_arr['signature'] = @$row[0];
		$row_profile = $this->db->from('coop_profile')
								->limit(1)
								->get()->result_array();
		$data_arr['row_profile'] = $row_profile[0];

		// echo "<pre>";
		// print_r($datas);
		// echo "</pre>";
		// exit;
		// $data_arr['month_arr'] = $this->month_arr;
		// $data_arr['datas'] = $datas;
		// $data_arr["meeting_date"] = $meeting_date;
		// $data_arr["agenda"] = $agenda;
		// $data_arr["committee_group"] = $committee_group;
		// $data_arr["print_date"] = $process_date;
		
		// print_r($meeting_date);
		// exit;

		$get_param = '';
		if(!empty($_GET)){
			foreach($_GET AS $key=>$val){
				$get_param .= $key.'='.$val.'&';
			}
		}
		$param = "";
		$param .= base64_encode("letter_ids=".implode("~",$letter_ids));
		$param .= "&".base64_encode("meeting_date=".$meeting_date);
		$param .= "&".base64_encode("agenda=".$agenda);
		$param .= "&".base64_encode("committee_group=".$committee_group);
		echo"<script> window.open('".PROJECTPATH."/debt/dismiss_letter_preview?".@$param."','_blank'); window.focus();</script>";
		echo"<script> document.location.href='".PROJECTPATH."/debt/dismiss_member?".$get_param."' </script>";
	}
	
	public function dismiss_letter_preview() {
		foreach($_GET as $key => $value){
			$param .= @$key."&";
			$decode = base64_decode(@$key);
			$decode = explode('=',@$decode);
			$_GET[$decode[0]] = @$decode[1];
		}
		$letter_ids = explode("~",$_GET["letter_ids"]);

		$letters = $this->db->select("t1.member_id,
										t1.meeting_date,
										t1.agenda,
										t1.committee_group,
										t1.print_date,
										t2.month,
										t2.year,
										t2.loan_id,
										t2.loan_atm_id,
										t3.firstname_th,
										t3.lastname_th,
										t4.prename_full,
										t5.contract_number,
										t5.loan_amount,
										t5.approve_date,
										t7.loan_type,
										t8.contract_number as atm_contract_number,
										t8.total_amount as atm_total_amount,
										t8.approve_date as atm_approve_date
									")
								->from("coop_debt_dismiss_letter as t1")
								->join("coop_debt_dismiss_letter_detail as t2", "t1.id = t2.letter_id")
								->join("coop_mem_apply as t3", "t1.member_id = t3.member_id", "left")
								->join("coop_prename as t4", "t4.prename_id = t3.prename_id", "left")
								->join("coop_loan as t5", "t2.loan_id = t5.id", "left")
								->join("coop_loan_name as t6","t5.loan_type = t6.loan_name_id","left")
								->join("coop_loan_type as t7","t6.loan_type_id = t7.id","left")
								->join("coop_loan_atm as t8", "t2.loan_atm_id = t8.loan_atm_id", "left")
								->where("t1.id IN ('".implode("','",$letter_ids)."')")
								->order_by("t2.month, t2.year")
								->get()->result_array();

		$member_ids = implode("','",array_column($letters, 'member_id'));

		//Get Shares
		$shares = $this->db->select("member_id, share_collect_value")
							->from("coop_mem_share")
							->where("member_id IN ('{$member_ids}') AND share_status NOT IN (0,3)")
							->order_by("share_date DESC, share_id DESC")
							->get()->result_array();
		$share_members = array_column($shares, 'member_id');

		//GET Bank Account balance
		$account_balances = $this->db->select("t1.mem_id as member_id, t1.account_id, t2.transaction_balance")
										->from("coop_maco_account as t1")
										->join("coop_account_transaction as t2", "t1.account_id = t2.account_id", "left")
										->where("t1.mem_id IN ('{$member_ids}') AND t1.account_status = 0 AND t2.cancel_status IS NULL")
										->order_by("t2.transaction_time DESC, t2.transaction_id DESC")
										->get()->result_array();
		$account_members = array_column($account_balances, 'member_id');

		$datas = array();
		foreach($letters as $letter) {
			$datas[$letter["member_id"]]["name"] = $letter["prename_full"].$letter["firstname_th"]." ".$letter["lastname_th"];
			$datas[$letter["member_id"]]["meeting_date"] = $letter["meeting_date"];
			$datas[$letter["member_id"]]["agenda"] = $letter["agenda"];
			$datas[$letter["member_id"]]["committee_group"] = $letter["committee_group"];
			$datas[$letter["member_id"]]["print_date"] = $letter["print_date"];

			if (!empty($letter["loan_id"])) {
				$period = array();
				$period["month"] = $letter["month"];
				$period["year"] = $letter["year"];
				$datas[$letter["member_id"]]["loans"][$letter["contract_number"]]["period"][] = $period;
				$datas[$letter["member_id"]]["loans"][$letter["contract_number"]]["loan_id"] = $letter["loan_id"];
				$datas[$letter["member_id"]]["loans"][$letter["contract_number"]]["balance"] = $letter["loan_amount"];
				$datas[$letter["member_id"]]["loans"][$letter["contract_number"]]["approve_date"] = $letter["approve_date"];
				$datas[$letter["member_id"]]["loans"][$letter["contract_number"]]["loan_type"] = $letter["loan_type"];
				$datas[$letter["member_id"]]["loans"][$letter["contract_number"]]["is_atm"] = 0;
				$datas[$letter["member_id"]]["loans"][$letter["contract_number"]]["period_count"] += 1;
				if($datas[$letter["member_id"]]["loans"][$letter["contract_number"]]["period_count"] >= $num_loan_month_limit) {
					$datas[$letter["member_id"]]["has_loan"] = 1;
					$datas[$letter["member_id"]]["loans"][$letter["contract_number"]]["over_limit"] = 1;
				} else {
					$datas[$letter["member_id"]]["loans"][$letter["contract_number"]]["over_limit"] = 0;
				}
			} else if (!empty($letter["loan_atm_id"])) {
				$period = array();
				$period["month"] = $letter["month"];
				$period["year"] = $letter["year"];
				$datas[$letter["member_id"]]["loans"][$letter["atm_contract_number"]]["period"][] = $period;
				$datas[$letter["member_id"]]["loans"][$letter["atm_contract_number"]]["loan_id"] = $letter["loan_atm_id"];
				$datas[$letter["member_id"]]["loans"][$letter["atm_contract_number"]]["balance"] = $letter["atm_total_amount"];
				$datas[$letter["member_id"]]["loans"][$letter["atm_contract_number"]]["loan_type"] = "เงินกู้ฉุกเฉิน";
				$datas[$letter["member_id"]]["loans"][$letter["atm_contract_number"]]["approve_date"] = $letter["atm_approve_date"];
				$datas[$letter["member_id"]]["loans"][$letter["atm_contract_number"]]["is_atm"] = 1;
				$datas[$letter["member_id"]]["loans"][$letter["atm_contract_number"]]["period_count"] += 1;
				if($datas[$letter["member_id"]]["loans"][$letter["atm_contract_number"]]["period_count"] >= $num_loan_month_limit) {
					$datas[$letter["member_id"]]["has_loan"] = 1;
					$datas[$letter["member_id"]]["loans"][$letter["atm_contract_number"]]["over_limit"] = 1;
				} else {
					$datas[$letter["member_id"]]["loans"][$letter["atm_contract_number"]]["over_limit"] = 0;
				}
			} else {
				$period = array();
				$period["month"] = $letter["month"];
				$period["year"] = $letter["year"];
				$datas[$letter["member_id"]]["share"]["period"][] = $period;
				$datas[$letter["member_id"]]["share"]["period_count"] += 1;
				if($datas[$letter["member_id"]]["share"]["period_count"] >= $num_share_month_limit) {
					$datas[$letter["member_id"]]["share"]["over_limit"] = 1;
				} else {
					$datas[$letter["member_id"]]["share"]["over_limit"] = 0;
				}
			}
		}

		foreach($datas as $member_id => $data) {
			//Set Share Info
			$datas[$member_id]["share_balance"] = $shares[array_search($member_id,$share_members)]['share_collect_value'];

			//Set bank account info
			// $account_id_prev = array();
			// $account_indexs = array_keys($account_members, $member_id);
			// foreach($account_indexs as $index) {
			// 	if(!in_array($account_balances[$index]["account_id"], $account_id_prev)) {
			// 		$account_id_prev[] = $account_balances[$index]["account_id"];
			// 		// $cal_result = $this->deposit_libraries->cal_deposit_interest_by_acc_date($account['account_id'], $process_timestamp);
			// 		// $close_account_interest = $cal_result['interest'];
			// 		// $close_account_interest_return = $cal_result['interest_return'];
			// 	}
			// }
		}

		// echo "<pre>";
		// print_r($datas);
		// echo "</pre>";
		// exit;
		$date_signature = date('Y-m-d');
		$row = $this->db->select(array('*'))
						->from('coop_signature')
						->where("start_date <= '{$date_signature}'")
						->order_by('start_date DESC')
						->limit(1)
						->get()->result_array();
		$data_arr['signature'] = @$row[0];
		$row_profile = $this->db->from('coop_profile')
								->limit(1)
								->get()->result_array();
		$data_arr['row_profile'] = $row_profile[0];

		$data_arr['month_arr'] = $this->month_arr;
		$data_arr["meeting_date"] = $_GET["meeting_date"];
		$data_arr["agenda"] = $_GET["agenda"];
		$data_arr["committee_group"] = $_GET["committee_group"];
		$data_arr["datas"] = $datas;
		$this->preview_libraries->template_preview('debt/dismiss_letter',$data_arr);
	}

	public function dismiss_member() {
		$data_arr = array();

		$where = "t1.mem_type = 5";
		if(!empty($_GET['search_member'])){
			$where .= " AND (t1.firstname_th LIKE '%{$_GET['search_member']}%'
			OR t1.lastname_th LIKE '%{$_GET['search_member']}%'
			OR t1.member_id LIKE '%{$_GET['search_member']}%')";
		}
		if(!empty($_GET['department'])){
			$where .= " AND t1.department = '{$_GET['department']}'";
		}
		if(!empty($_GET['faction'])){
			$where .= " AND t1.faction = '{$_GET['faction']}'";
		}
		if(!empty($_GET['level'])){
			$where .= " AND t1.level = '{$_GET['level']}'";
		}

		$members = $this->db->select("t1.member_id,
										t1.firstname_th,
										t1.lastname_th,
										t2.prename_full
									")
							->from("coop_mem_apply as t1")
							->join("coop_prename as t2", "t1.prename_id = t2.prename_id", "left")
							->where($where)
							->get()->result_array();
		// echo "<pre>";
		// print_r($members);
		// echo "</pre>";
		// exit;
		$arr_data["datas"] = $members;
		$this->libraries->template('debt/dismiss_member',$arr_data);
	}

	public function print_dismiss_letter() {
		$arr_data = array();
		$data_arr['month_arr'] = $this->month_arr;

		$share_debt_limit = $this->db->select("*")
										->from("coop_debt_letter_setting")
										->where("setting_code = 'share_debt'")
										->get()->row();
		$num_share_month_limit = $share_debt_limit->num_letter;
		$arr_data["num_share_month_limit"] = $num_share_month_limit;

		$loan_debt_limit = $this->db->select("*")
										->from("coop_debt_letter_setting")
										->where("setting_code = 'loan_debt'")
										->get()->row();
		$num_loan_month_limit = $loan_debt_limit->num_letter;
		$arr_data["num_loan_month_limit"] = $num_loan_month_limit;

		$member_ids = array();
		if(!empty($_POST['member_ids'])) {
			$member_ids = $_POST['member_ids'];
		} else {
			$member_ids = $_POST["member_id"];
		}
		$where = "t1.member_id IN ({$member_ids})";

		//Get data
		$raw_datas = $this->db->select("t1.member_id,
										t1.firstname_th,
										t1.lastname_th,
										t2.prename_full,
										t3.loan_id,
										t3.loan_atm_id,
										t3.month,
										t3.year,
										t4.contract_number as contract_number,
										t4.createdatetime as approve_date,
										t4.loan_amount as loan_amount,
										t5.contract_number as atm_contract_number,
										t5.approve_date as atm_approve_date,
										t5.total_amount as atm_loan_amount,
										t6.approve_date as resign_approve_date,
										t8.loan_type,
										t9.principal_payment as principal_payment,
										t10.principal_payment as atm_principal_payment
									")
							->from("coop_mem_apply as t1")
							->join("coop_prename as t2", "t1.prename_id = t2.prename_id", "left")
							->join("coop_resign_non_pay_detail as t3", "t1.member_id = t3.member_id", "left")
							->join("coop_loan as t4", "t3.loan_id = t4.id", "left")
							->join("coop_loan_atm as t5", "t3.loan_atm_id = t5.loan_atm_id", "left")
							->join("coop_mem_req_resign as t6", "t1.member_id = t6.member_id AND t6.req_resign_status = '1'")
							->join("coop_loan_name as t7","t4.loan_type = t7.loan_name_id","left")
							->join("coop_loan_type as t8","t7.loan_type_id = t8.id","left")
							->join("coop_finance_transaction as t9","t9.loan_id = t4.id AND t6.approve_date = t9.createdatetime","left")
							->join("coop_finance_transaction as t10","t10.loan_atm_id = t5.loan_atm_id AND t6.approve_date = t10.createdatetime","left")
							->where($where)
							->order_by("t3.year, t3.month")
							->get()->result_array();
		$loan_ids = implode(',',array_filter(array_column($raw_datas, 'loan_id')));

		$resign_loans = $this->db->select("member_id,
											sum(loan_amount_principal) as principal,
											sum(loan_amount_interest) as interest,
											sum(loan_amount_interest_debt) as interest_debt,
											sum(loan_amount_debt) as debt")
									->from("coop_resign_loan_detail")
									->where("member_id IN ({$member_ids})")
									->group_by("member_id")
									->get()->result_array();
		$resign_loan_member_ids = array_column($resign_loans, 'member_id');

		$shares_raw = $this->db->select("member_id, share_payable_value")
								->from("coop_mem_share")
								->where("member_id IN ({$member_ids}) AND share_status = 5")
								->get()->result_array();
		$shares_member_ids = array_column($shares_raw, 'member_id');
		$shares = array();
		foreach($shares_raw as $share) {
			$shares[$share["member_id"]]["share_balance"] += $share["share_payable_value"];
		}

		$accounts = $this->db->select("*")
								->from("coop_resign_income_detail")
								->where("member_id IN ({$member_ids}) AND income_code = 'income_deposit'")
								->get()->result_array();
		$accounts_member_ids = array_column($accounts, 'member_id');

		if(!empty($loan_ids)) {
			$guarantees = $this->db->select(array('coop_loan_guarantee_person.*',
													'coop_prename.prename_full',
													'coop_prename.prename_short',
													'coop_mem_apply.firstname_th',
													'coop_mem_apply.lastname_th',
													'coop_mem_apply.share_month',
													'coop_mem_apply.member_id'
												))
									->from('coop_loan_guarantee_person')
									->join("coop_mem_apply","coop_mem_apply.member_id = coop_loan_guarantee_person.guarantee_person_id","inner")
									->join("coop_prename","coop_prename.prename_id = coop_mem_apply.prename_id","left")
									->where("coop_loan_guarantee_person.loan_id IN ({$loan_ids})")
									->get()->result_array();
			$guarantees_loan_ids = array_column($guarantees, 'loan_id');
		}

		$data_arr['data'][$key_letter]['guarantee_persons'][$letter_detail['loan_contract_number']] = $guarantees;

		//Format data
		$datas = array();
		foreach ($raw_datas as $data) {
			$datas[$data["member_id"]]["name"] = $data["prename_full"].$data["firstname_th"]." ".$data["lastname_th"];
			$datas[$data["member_id"]]["resign_approve_date"] = $data["resign_approve_date"];
			if(empty($datas[$data["member_id"]]["principal_paid"]) && in_array($data["member_id"], $resign_loan_member_ids)) {
				$resign_loan = $resign_loans[array_search($data["member_id"], $resign_loan_member_ids)];
				$datas[$data["member_id"]]["principal_paid"] = $resign_loan["principal"];
				$datas[$data["member_id"]]["interest_paid"] = $resign_loan["interest"];
				$datas[$data["member_id"]]["interest_debt_paid"] = $resign_loan["interest_debt"];
				$datas[$data["member_id"]]["debt"] = $resign_loan["debt"];
			}

			if(empty($datas[$data["member_id"]]["share"]["balance"])) {
				$datas[$data["member_id"]]["share"]["balance"] = $shares[$data["member_id"]]["share_balance"];
			}

			if(empty($datas[$data["member_id"]]["account"]["balance"]) && in_array($data["member_id"], $accounts_member_ids)) {
				$account = $accounts[array_search($data["member_id"], $accounts_member_ids)];
				$datas[$data["member_id"]]["account"]["balance"] = $account["income_amount"];
			}

			$period = array();
			$period["month"] = $data["month"];
			$period["year"] = $data["year"];

			if (!empty($data["loan_id"])) {
				$datas[$data["member_id"]]["loans"][$data["contract_number"]]["approve_date"] = $data["approve_date"];
				$datas[$data["member_id"]]["loans"][$data["contract_number"]]["balance"] = $data["loan_amount"];
				$datas[$data["member_id"]]["loans"][$data["contract_number"]]["period"][] = $period;
				$datas[$data["member_id"]]["loans"][$data["contract_number"]]["loan_type"] = $data["loan_type"];
				$datas[$data["member_id"]]["loans"][$data["contract_number"]]["principal_payment"] = $data["principal_payment"];
				$datas[$data["member_id"]]["loans"][$data["contract_number"]]["debt_count"] += 1;
				if ($datas[$data["member_id"]]["loans"][$data["contract_number"]]["debt_count"] > $num_loan_month_limit) {
					$datas[$data["member_id"]]["has_loan"] = 1;
					$datas[$data["member_id"]]["loans"][$data["contract_number"]]["over_limit"] = 1;

					if (in_array($data["loan_id"],$guarantees_loan_ids)) {
						$guarantee_arr = array();
						$gua_indexs = array_keys($guarantees_loan_ids, $data["loan_id"]);
						foreach($gua_indexs as $gua_index) {
							$guarantee = $guarantees[$gua_index];
							$guarantee["contract_number"] = $data["contract_number"];
							$guarantee_arr[] = $guarantee;
						}
						$datas[$data["member_id"]]["guarantee"] = $guarantee_arr;
					}
				}

			} elseif (!empty($data["loan_atm_id"])) {
				$datas[$data["member_id"]]["loans"][$data["atm_contract_number"]]["approve_date"][] = $data["atm_approve_date"];
				$datas[$data["member_id"]]["loans"][$data["contract_number"]]["balance"] = $data["atm_loan_amount"];
				$datas[$data["member_id"]]["loans"][$data["atm_contract_number"]]["period"][] = $period;
				$datas[$data["member_id"]]["loans"][$data["atm_contract_number"]]["loan_type"] = "เงินกู้ฉุกเฉิน";
				$datas[$data["member_id"]]["loans"][$data["atm_contract_number"]]["principal_payment"] = $data["atm_principal_payment"];
				$datas[$data["member_id"]]["loans"][$data["atm_contract_number"]]["debt_count"] += 1;
				if ($datas[$data["member_id"]]["loans"][$data["atm_contract_number"]]["debt_count"] > $num_loan_month_limit) {
					$datas[$data["member_id"]]["has_loan"] = 1;
					$datas[$data["member_id"]]["loans"][$data["atm_contract_number"]]["over_limit"] = 1;
				}
			} else {
				$datas[$data["member_id"]]["share"]["period"][] = $period;
				$datas[$data["member_id"]]["share"]["debt_count"] += 1;
				if ($datas[$data["member_id"]]["share"]["debt_count"] >= $num_share_month_limit) {
					$datas[$data["member_id"]]["has_share"] = 1;
				}
			}
		}

		$meeting_date_arr = explode('/',$_POST["meeting_date"]);
		$data_arr["meeting_day"] = stripslashes($meeting_date_arr[0]);
		$data_arr["meeting_month"] = stripslashes($meeting_date_arr[1]);
		$data_arr["meeting_year"] = stripslashes($meeting_date_arr[2]) - 543;
		$data_arr["meeting_date"] = $meeting_year."-".$meeting_month."-".$meeting_day;

		$data_arr["agenda"] = $agenda;
		$data_arr["print_date"] = $print_date;
		$data_arr["committee_group"] = $committee_group;

		$date_signature = date('Y-m-d');
		$row = $this->db->select(array('*'))
						->from('coop_signature')
						->where("start_date <= '{$date_signature}'")
						->order_by('start_date DESC')
						->limit(1)
						->get()->result_array();
		$data_arr['signature'] = @$row[0];
		$row_profile = $this->db->from('coop_profile')
								->limit(1)
								->get()->result_array();
		$data_arr['row_profile'] = $row_profile[0];

		$data_arr["datas"] = $datas;

		$this->preview_libraries->template_preview('debt/dismiss_letter',$data_arr);
	}

	public function debt_envelope() {
		$data_arr = array();

		$param = '';
		foreach($_GET as $key => $value){
			$param .= @$key."&";
			$decode = base64_decode(@$key);
			$decode = explode('=',@$decode);
			$_GET[$decode[0]] = @$decode[1];
		}

		$letters = array();
		if(!empty($_GET["id"])) {
			$letters = $this->db->select("*")
								->from("coop_debt_letter")
								->where("letter_id = '".$_GET["id"]."'")
								->get()->result_array();
		} else if(!empty($print_ref)){
			$letters = $this->db->select("*")
								->from('coop_debt_letter')
								->where("print_ref = '".$_GET["print_ref"]."'")
								->get()->result_array();
		}else if(!empty($non_pay)){
			$arr_non_pay = explode("~",$_GET["non_pay"]);
			$letter_month = $_GET['letter_month'];
			$letter_year = $_GET['letter_year'];
			$arr_non_pay = explode("~",$_GET["non_pay"]);
			$letters = $this->db->select("*")
							->from('coop_debt_letter')
							->where("non_pay_id IN ('".implode("','",@$arr_non_pay)."') AND letter_month = '{$letter_month }' AND letter_year = '{$letter_year}'")
							->order_by("member_id,letter_month,letter_year")
							->get()->result_array();
		}

		$datas = array();
		$index = 0;
		foreach($letters as $letter) {
			$member = $this->db->select("t2.prename_full,
											t1.member_id,
											t1.firstname_th,
											t1.lastname_th,
											t1.address_no as no,
											t1.address_moo as moo,
											t1.address_village as village,
											t1.address_road as road,
											t1.address_soi as soi,
											t1.zipcode,
											t3.province_id,
											t3.province_name,
											t4.amphur_id,
											t4.amphur_name,
											t5.district_id,
											t5.district_name
										")
								->from("coop_mem_apply as t1")
								->join("coop_prename as t2", "t1.prename_id = t2.prename_id", "left")
								->join("coop_province t3","t1.province_id = t3.province_id","left")
								->join("coop_amphur t4","t1.amphur_id = t4.amphur_id","left")
								->join("coop_district t5","t1.district_id = t5.district_id","left")
								->where("member_id = '".$letter["member_id"]."'")
								->get()->result_array()[0];

			$datas[$index++] = $member;

			$guarantees = $this->db->select("t4.prename_full,
											t3.member_id,
											t3.firstname_th,
											t3.lastname_th,
											t3.address_no as no,
											t3.address_moo as moo,
											t3.address_village as village,
											t3.address_road as road,
											t3.address_soi as soi,
											t3.zipcode,
											t5.province_id,
											t5.province_name,
											t6.amphur_id,
											t6.amphur_name,
											t7.district_id,
											t7.district_name
											")
									->from("coop_debt_letter_detail as t1")
									->join("coop_loan_guarantee_person as t2", "t1.loan_id = t2.loan_id", "inner")
									->join("coop_mem_apply as t3", "t2.guarantee_person_id = t3.member_id", "inner")
									->join("coop_prename as t4", "t3.prename_id = t4.prename_id", "left")
									->join("coop_province as t5", "t3.province_id = t5.province_id", "left")
									->join("coop_amphur as t6", "t3.amphur_id = t6.amphur_id", "left")
									->join("coop_district as t7", "t3.district_id = t7.district_id", "left")
									->where("t1.letter_id = '".$letter["letter_id"]."' AND t1.loan_id is not null")
									->group_by("t3.member_id")
									->get()->result_array();
			foreach($guarantees as $guarantee) {
				$datas[$index++] = $guarantee;
			}
		}

		$data_arr["datas"] = $datas;
		$this->load->view('debt/debt_envelope_pdf',$data_arr);
	}
}
