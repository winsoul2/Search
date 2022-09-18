<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Save_money extends CI_Controller {
	function __construct()
	{
		parent::__construct();
		$this->load->model("Deposit_model", "deposit_model");
		$this->load->model("Receipt_model", "receipt_model");
		$this->load->model("Report_accrued_interest_model", "report_accrued");
		$this->load->model("Deposit_seq_model", "deposit_seq");
	}
	public function index()
	{
		$arr_data = array();
		
		$x=0;
		$join_arr = array();
		$join_arr[$x]['table'] = 'coop_deposit_type_setting as t2';
		$join_arr[$x]['condition'] = 't1.type_id = t2.type_id';
		$join_arr[$x]['type'] = 'left';
		
		$this->paginater_all->type(DB_TYPE);
		$this->paginater_all->select(array('t1.account_id','t1.account_name','t1.mem_id','t1.member_name','t1.created','t1.account_status','t2.type_code'));
		$this->paginater_all->main_table('coop_maco_account as t1');
		$this->paginater_all->where("");
		$this->paginater_all->page_now(@$_GET["page"]);
		$this->paginater_all->per_page(20);
		$this->paginater_all->page_link_limit(20);
		$this->paginater_all->order_by('t1.account_status ASC,t1.created DESC');
		$this->paginater_all->join_arr($join_arr);
		$row = $this->paginater_all->paginater_process();
		//echo"<pre>";print_r($row);exit;
		$paging = $this->pagination_center->paginating($row['page'], $row['num_rows'], $row['per_page'], $row['page_link_limit'], @$_GET);//$page_now = 1, $row_total = 1, $per_page = 20, $page_limit = 20
		$i = $row['page_start'];

		$account_ids = array_column($row['data'], 'account_id');
		$get_account_receipt_refund_id = $this->receipt_model->get_account_receipt_refund_id($account_ids);
		$arr_data['arr_receipt_refund'] = @$get_account_receipt_refund_id;

		$arr_data['num_rows'] = $row['num_rows'];
		$arr_data['paging'] = $paging;
		$arr_data['data'] = $row['data'];
		$arr_data['i'] = $i;
		
		$this->libraries->template('save_money/index',$arr_data);
	}
	
	function add_save_money(){
		$arr_data = array();
		$data = $this->input->post();
		if($data['account_id']!=''){
			$account_id = @$data['account_id'] ;
			$arr_data['account_id'] = $account_id;
			
			$this->db->select(array('t1.*','t2.type_name', "CONCAT(transfer_type, '|', dividend_bank_id, '|', dividend_bank_branch_id, '|', dividend_acc_num) AS id_transfer, t3.user_name"));
			$this->db->from('coop_maco_account as t1');
			$this->db->join('coop_deposit_type_setting as t2','t1.type_id = t2.type_id','inner');
			$this->db->join('coop_user as t3','t3.user_id = t1.sequester_by','left');
			$this->db->where("t1.account_id = '".$account_id."'");
			$row = $this->db->get()->result_array();
			$arr_data['auto_account_id'] = '';
			$btitle = "แก้ไขบัญชีเงินฝาก";
			$arr_data['row'] = $row[0];
		}else{
			/*$this->db->select('account_id');
			$this->db->from('coop_maco_account');
			$this->db->order_by("account_id DESC");
			$this->db->limit(1);
			$row = $this->db->get()->result_array();
			if(!empty($row)){
				$auto_account_id = $row[0]['account_id'] + 1;
			}else{
				$auto_account_id = 1;
			}
			$arr_data['auto_account_id'] = $auto_account_id;*/
			$btitle = "เพิ่มบัญชีเงินฝาก";
			$arr_data['row'] = array();
			$arr_data['account_id'] = '';
		}
		$this->db->select(array('t1.type_id','t1.type_code','t1.type_name','t1.unique_account'));
		$this->db->from('coop_deposit_type_setting as t1');
		$row = $this->db->get()->result_array();
		$arr_data['type_id'] = $row;

		$arr_data['account_list_transfer'] = array();
		if($data['member_id']!=""){
			$this->db->select(array("CONCAT('1|', '', '|', '', '|', account_id) AS id", "CONCAT(account_id, '  ',account_name) AS text"));
			$maco_account = $this->db->get_where("coop_maco_account", array("mem_id" =>  $data['member_id']))->result_array();
			
			$this->db->select(array("CONCAT('2|', dividend_bank_id, '|', dividend_bank_branch_id, '|', dividend_acc_num) AS id", "CONCAT('|', dividend_acc_num, '  ', coop_bank.bank_name, ' ', coop_bank_branch.branch_name) AS text"));
			$this->db->join("coop_bank", "coop_bank.bank_id = coop_mem_bank_account.dividend_bank_id");
			$this->db->join("coop_bank_branch", "coop_mem_bank_account.dividend_bank_branch_id = coop_bank_branch.branch_code AND coop_mem_bank_account.dividend_bank_id = coop_bank_branch.bank_id", "LEFT");
			$data_mem_bank_account = $this->db->get_where("coop_mem_bank_account", array("member_id" => $data['member_id']) )->result_array();
			if($maco_account){
				foreach ($maco_account as $key => $value) {
					array_push($arr_data['account_list_transfer'], $value);
				}
			}
	
			if($data_mem_bank_account){
				foreach ($data_mem_bank_account as $key => $value) {
					array_push($arr_data['account_list_transfer'], $value);
				}
			}
		}

		
		$arr_data['btitle'] = $btitle;
		$this->load->view('save_money/add_save_money',$arr_data);
	}
	
	function save_add_save_money(){
		// echo"<pre>";print_r($this->input->post());echo"</pre>";exit;
		$bank_id = @$_POST['bank_id'];
		$branch_code = @$_POST['branch_code'];
		$local_account_id = @$_POST['transfer_bank_account_name'];
		$cheque_number = @$_POST['cheque_number'];
		$other = @$_POST['other'];
		$transfer_other = @$_POST['transfer_other'];
		$data = $this->input->post();
		$sequester_amount = (@$data['sequester_status'] == 2)?str_replace(',','',@$data['sequester_amount']):'';
		if(@$data['sequester_status']=='1'){
			$sequester_status_atm = '1';
		}else{
			$sequester_status_atm = @$data['sequester_status_atm'];
		}
		
		if($data['action_type']=='add'){
			$account_id = '';
			$this->db->select('type_code,type_prefix');
			$this->db->from('coop_deposit_type_setting');
			$this->db->where("type_id = '".$data['type_id']."'");
			$row = $this->db->get()->result_array();


			$type_code = @$row[0]['type_code'];
			$type_prefix = @$row[0]['type_prefix'];


			$this->db->select('deposit_setting_id');
			$this->db->from('coop_deposit_setting');
			$this->db->where("deposit_setting_id = '".$data['deposit_setting_id']."'");
			$row = $this->db->get()->result_array();
			$min_first_deposit = $row[0]['min_first_deposit'];


			$this->db->select('account_id');
			$this->db->from('coop_maco_account');
			$this->db->where("type_id = '".$data['type_id']."'");
			$this->db->order_by("account_id DESC");
			$this->db->limit(1);
			$row = $this->db->get()->result_array();
			//echo $this->db->last_query(); echo '<br>';
			$digit_run_account = $this->center_function->digit_run_account();

			//echo $digit_run_account; exit;
			if(!empty($row)) {
				$c_id = 1;
				$old_account_id = substr($row[0]["account_id"], (int)$digit_run_account*(-1));
				$old_account_id = (int)$old_account_id;
				$account_id = sprintf("%0".$digit_run_account."d", $old_account_id + ($c_id++));
				$account_id = $type_code.$account_id;

				while(true){
					$this->db->select('account_id');
					$this->db->from('coop_account_transaction');
					$this->db->where("account_id = '".$account_id."'");
					$this->db->limit(1);
					$row_account = $this->db->get()->result_array();
					if($row_account){
                        $old_account_id = substr($row[0]["account_id"], (int)$digit_run_account*(-1));
						$old_account_id = (int)$old_account_id;
						$account_id = sprintf("%0".$digit_run_account."d", $old_account_id + ($c_id++));
						$account_id = $type_code.$account_id;
					}else{
						break;
					}

				}

			}else {
				$account_id = $type_code.sprintf("%0".$digit_run_account."d", 1);
			}

			//echo $account_id;exit;
			$tmp_opn_date = explode('/', $data['opn_date']);
			$opn_date = ($tmp_opn_date[2]-543)."-".$tmp_opn_date[1]."-".$tmp_opn_date[0]." ".date('H:i:s');
			
			//เช็ควันที่เวลา เปิดบัญชี
			$row_chk_time = $this->db->select('created')->from('coop_maco_account')->where("created = '{$opn_date}' AND mem_id = '{$data['mem_id']}'")->limit(1)->get()->row_array();
			if(empty($row_chk_time)){
				$data_insert = array();
				//$data_insert['account_id'] = $data['acc_id'];
				$data_insert['account_id'] = ($data['acc_id_yourself'] != '') ? $data['acc_id_yourself'] : $account_id;
				$data_insert['mem_id'] = $data['mem_id'];
				$data_insert['member_name'] = $data['member_name'];
				$data_insert['account_name'] = $data['acc_name'];
				
				$data_insert['account_amount'] = '0';
				$data_insert['book_number'] = '1';
				$data_insert['type_id'] = $data['type_id'];
				$data_insert['atm_number'] = $data['atm_number'];
				$data_insert['account_status'] = '0';
				$data_insert['account_name_eng'] = $data['account_name_eng'];
				$data_insert['sequester_status'] = @$data['sequester_status'];
				$data_insert['sequester_amount'] = @$sequester_amount;
				$data_insert['sequester_status_atm'] = @$sequester_status_atm;
				//$data_insert['min_first_deposit'] = @$min_first_deposit;
				//$tmp_opn_date = explode('/', $data['opn_date']);
				//$data_insert['created'] = ($tmp_opn_date[2]-543)."-".$tmp_opn_date[1]."-".$tmp_opn_date[0]." ".date('H:i:s');
				$data_insert['created'] = $opn_date;
				if($data['account_transfer']!=''){
					$tmp_account_transfer = explode("|", $data['account_transfer']);
					$data_insert['transfer_type'] = $tmp_account_transfer[0];
					$data_insert['dividend_bank_id'] = $tmp_account_transfer[1];
					$data_insert['dividend_bank_branch_id'] = $tmp_account_transfer[2];
					$data_insert['dividend_acc_num'] = $tmp_account_transfer[3];
				}
				//echo '<pre>'; print_r($data_insert); echo '</pre>';
				//exit;
				$this->db->insert('coop_maco_account', $data_insert);

				$opn_pay_type = $data['pay_type_tmp'];
				$min_first_deposit = implode('', explode(',', $data['min_first_deposit']));
				$data_insert_transaction['transaction_time'] 			= $data_insert['created'];
				$data_insert_transaction['transaction_list'] 			= ($opn_pay_type=="1") ? "OPNX" : (($opn_pay_type=="2") ? "OPNC" : "OPN");
				$data_insert_transaction['transaction_withdrawal'] 		= 0;
				$data_insert_transaction['transaction_deposit'] 		= $min_first_deposit;
				$data_insert_transaction['transaction_balance'] 		= $min_first_deposit;
				$data_insert_transaction['user_id'] 					= $_SESSION['USER_ID'];
				$data_insert_transaction['transaction_no_in_balance'] 	= $min_first_deposit;
				$data_insert_transaction['account_id'] 					= $data_insert['account_id'];
				$data_insert_transaction['bank_id'] 					= $bank_id;
				$data_insert_transaction['branch_code'] 				= $branch_code;
				$data_insert_transaction['local_account_id'] 			= $local_account_id;
				$data_insert_transaction['cheque_no'] 					= $cheque_number;
				$data_insert_transaction['other'] 						= $other;
				$data_insert_transaction['transfer_other'] 				= $transfer_other;

				//ดึงข้อมูลลำดับรายการ ของรายการถัดไป
				$arr_seq = array(); 
				$arr_seq['account_id'] = $account_id; 
				$arr_seq['transaction_list'] = $data_insert_transaction['transaction_list'];
				$seq_no = $this->deposit_seq->gen_seq_account_transaction($arr_seq);
				$data_insert_transaction['seq_no'] = @$seq_no;

				$this->db->insert("coop_account_transaction", $data_insert_transaction);
			}
		}else{
			$data_insert = array();
			$data_insert['account_name'] = $data['acc_name'];
			$data_insert['account_name_eng'] = $data['account_name_eng'];
			$data_insert['type_id'] = $data['type_id'];
			$data_insert['atm_number'] = $data['atm_number'];
			$data_insert['sequester_status'] = @$data['sequester_status'];
			if($data['remark']!=""){
				$data_insert['sequester_by'] = @$_SESSION['USER_ID'];
				$data_insert['sequester_remark'] = @$data['remark'];
				$data_insert['sequester_time'] = date("Y-m-d H:i:s");
			}
			$data_insert['sequester_amount'] = @$sequester_amount;
			$data_insert['sequester_status_atm'] = @$sequester_status_atm;
			$tmp_opn_date = explode('/', $data['opn_date']);
			$data_insert['created'] = ($tmp_opn_date[2]-543)."-".$tmp_opn_date[1]."-".$tmp_opn_date[0]." ".date('H:i:s');
			$data_insert['updated']	= date("Y-m-d H:i:s");
			$data_insert['updated_by']	= @$_SESSION['USER_ID'];
			if($data['account_transfer']!=''){
				$tmp_account_transfer = explode("|", $data['account_transfer']);
				$data_insert['transfer_type'] = $tmp_account_transfer[0];
				$data_insert['dividend_bank_id'] = $tmp_account_transfer[1];
				$data_insert['dividend_bank_branch_id'] = $tmp_account_transfer[2];
				$data_insert['dividend_acc_num'] = $tmp_account_transfer[3];
			}

			$data['acc_id'] = implode("", explode("-",  $data['acc_id']) );
			if($data['acc_id'] != $data['old_account_no']){
				//---ค้นหาข้อมูลบัญชีเก่า
				$old_account = $this->db->get_where("coop_maco_account", array(
					"account_id" => $data['old_account_no']
				))->result_array()[0];
				//---เพิ่มเลขบัญชีใหม่
				$old_account['account_name'] = $data['acc_name'];
				$old_account['account_name_eng'] = $data['account_name_eng'];
				$old_account['account_id'] = $data['acc_id'];
				$old_account['created'] = $old_account['created'];
				$old_account['updated'] = date("Y-m-d H:i:s");
				$this->db->insert("coop_maco_account", $old_account);
				//---อัพเดทข้อมูล coop_account_transaction
				$data_update_transaction['account_id'] = $old_account['account_id'];
				$this->db->where("account_id", $data['old_account_no']);
				$this->db->update("coop_account_transaction", $data_update_transaction);
				//---ลบข้อมูล row ใน coop_maco_account
				$this->db->where("account_id", $data['old_account_no']);
				$this->db->delete("coop_maco_account");

			}else{
				
				$this->db->where('account_id', $data['acc_id']);
				$this->db->update('coop_maco_account', $data_insert);
			}
			
		}
		$this->center_function->toast('บันทึกข้อมูลเรียบร้อยแล้ว');
		if(@$data['redirectback']!=''){
			echo "<script> document.location.href = '".PROJECTPATH."/save_money".$data['redirectback'].$data['acc_id']."' </script>";
		}else{
			echo "<script> document.location.href = '".PROJECTPATH."/save_money' </script>";
		}
		exit;
	}
	
	function check_account_save(){
		$data = array();
		$data['error'] = '';
		$today = date("Y-m-d");
		
		$this->db->select(array('*'));
		$this->db->from('coop_deposit_type_setting');
		$this->db->join("coop_deposit_type_setting_detail","coop_deposit_type_setting_detail.type_id = coop_deposit_type_setting.type_id","left");
		$this->db->where("coop_deposit_type_setting.type_id = '{$_POST['type_id']}' AND coop_deposit_type_setting_detail.start_date <= '{$today}'");
		$this->db->order_by('coop_deposit_type_setting_detail.start_date DESC');
		$this->db->limit(1);
		$row_setting = $this->db->get()->row_array();
		
		if($_POST['atm_number']!=''){
			$this->db->select(array('t1.atm_number'));
			$this->db->from('coop_atm_card as t1');
			$this->db->where("atm_number = '".$_POST['atm_number']."' AND member_id != '".$_POST['member_id']."' AND atm_card_status = '0'");
			$row = $this->db->get()->result_array();
			//echo $this->db->last_query();
			if(empty($row)){
				$data['atm_number'] = 'success';
			}else{
				$data['atm_number'] = 'dupplicate';
			}
		}else{
			$data['atm_number'] = 'success';
		}
		
		if($_POST['unique_account']=='1'){
			$this->db->select('*');
			$this->db->from('coop_maco_account');
			$this->db->where("
				type_id = '".$_POST['type_id']."' 
				AND account_id != '".$_POST['account_id']."'
				AND mem_id = '".$_POST['member_id']."'
			");
			$row = $this->db->get()->result_array();

			if(empty($row)){
				$data['unique_account'] = 'success';
			}else{
				$data['unique_account'] = 'dupplicate';
			}

			$account_status = 1;
			foreach($row AS $key=>$val){
				if($val['account_status'] == '0'){
					$account_status = 0;
				}
			}
			$data['account_status'] = $account_status;
		}

		if($_POST['account_id']!=''){
			$this->db->select('*');
			$this->db->from('coop_maco_account');
			$this->db->where("account_id = '".$_POST['account_id']."'");
			$row = $this->db->get()->result_array();

			if(empty($row)){
				$data['acc_number'] = 'success';
			}else{
				$data['acc_number'] = 'dupplicate_account_no';
			}

			$account_status = 1;
			foreach($row AS $key=>$val){
				if($val['account_status'] == '0'){
					$account_status = 0;
				}
			}
			$data['account_status'] = $account_status;
		}
		
		if($_POST["min_first_deposit"] < $row_setting["open_min"] && $row_setting["is_open_min"]) {
			$data['error'] = 'เปิดบัญชีขั้นต่ำ '.number_format($row_setting["open_min"]).' บาท';
		}
		
		echo json_encode($data);
	}
	
	function check_account_delete(){
		$data = $this->input->post();
		$this->db->select('*');
		$this->db->from('coop_maco_account');
		$this->db->where("account_id = '".$data['account_id']."'");
		$row = $this->db->get()->result_array();
		
		if($row[0]['account_amount'] > 0 ){
			echo 'error';
		}else{
			echo'success';
		}
		exit;
	}
	
	function delete_account($account_id){
		$this->db->where('account_id', $account_id);
		$this->db->delete('coop_maco_account');
		$this->center_function->toast('ลบข้อมูลเรียบร้อยแล้ว');
		echo "<script> document.location.href = '".PROJECTPATH."/save_money' </script>";
	}
	
	function close_account(){
		// echo"<pre>";print_r($_POST);exit;
		$account_id = $_POST['account_id'];
		$close_account_principal = str_replace(',','',$_POST['close_account_principal']);
		$close_account_interest = str_replace(',','',$_POST['close_account_interest']);
		$close_account_interest_return = str_replace(',','',$_POST['close_account_interest_return']);
		$close_account_principal_return = str_replace(',','',$_POST['close_account_principal_return']);
		$bank_id = @$_POST['bank_id'];
		$branch_code = @$_POST['branch_code'];
		$local_account_id = @$_POST['transfer_bank_account_name'];
		$cheque_number = @$_POST['cheque_number'];
		$other = @$_POST['other'];
		$transfer_other = @$_POST['transfer_other'];
		if($_POST['pay_type'] == '0'){
			$transaction_list = 'CW';
		}else if($_POST['pay_type'] == '0'){
			$transaction_list = 'XW';
		}else{
			$transaction_list = 'WCHE';
		}

		$close_time = $_POST['close_time'];
		$transaction_time = date("Y-m-d")." ".$close_time;
		if(@$_POST['close_date']){
			$transaction_time = (substr($_POST['close_date'],6,4)-543)."-".substr($_POST['close_date'], 3, 2)."-".substr($_POST['close_date'], 0, 2)." ".$close_time;
		}
		// $this->deposit_libraries->cal_deposit_interest_by_account($account_id,'real');
		$this->db->select(array('transaction_balance','transaction_no_in_balance'));
		$this->db->from('coop_account_transaction');
		$this->db->where("account_id = '".$account_id."'");
		$this->db->order_by('transaction_time DESC, transaction_id DESC');
		$this->db->limit(1);
		$row = $this->db->get()->result_array();
		$row_transaction = $row[0];
		$transaction_balance = $row_transaction['transaction_balance'];
		$transaction_no_in_balance = $row_transaction['transaction_no_in_balance'];
		/*if($row_transaction['transaction_balance'] > $close_account_principal){
			$return_to_coop = $row_transaction['transaction_balance'] - $close_account_principal;
			$transaction_balance = $transaction_balance - $return_to_coop;
			$data_insert = array();
			$data_insert['transaction_time'] = date('Y-m-d H:i:s');
			$data_insert['transaction_list'] = 'RE/COOP';
			$data_insert['transaction_withdrawal'] = $return_to_coop;
			$data_insert['transaction_deposit'] = '0';
			$data_insert['transaction_balance'] = $transaction_balance;
			$data_insert['transaction_no_in_balance'] = $transaction_no_in_balance;
			$data_insert['user_id'] = $_SESSION['USER_ID'];
			$data_insert['account_id'] = $account_id;
			$this->db->insert('coop_account_transaction', $data_insert);
		}*/
		
		if($close_account_interest != 0){
			$transaction_balance = $transaction_balance+$close_account_interest;
			$data_insert_in = array();
			$data_insert_in['transaction_time'] = $transaction_time;
			$data_insert_in['transaction_list'] = 'INC';
			$data_insert_in['transaction_withdrawal'] = '0';
			$data_insert_in['transaction_deposit'] = $close_account_interest;
			$data_insert_in['transaction_balance'] = $transaction_balance;
			$data_insert_in['transaction_no_in_balance'] = $transaction_no_in_balance;
			$data_insert_in['user_id'] = $_SESSION['USER_ID'];
			$data_insert_in['account_id'] = $account_id;
			$data_insert_in['bank_id'] = $bank_id;
			$data_insert_in['branch_code'] = $branch_code;
			$data_insert_in['local_account_id'] = $local_account_id;
			$data_insert_in['cheque_no'] = $cheque_number;
			$data_insert_in['other'] = $other;
			$data_insert_in['transfer_other'] = $transfer_other;
			
			//ดึงข้อมูลลำดับรายการ ของรายการถัดไป
			$arr_seq = array(); 
			$arr_seq['account_id'] = $account_id; 
			$arr_seq['transaction_list'] = $data_insert_in['transaction_list'];
			$seq_no = $this->deposit_seq->gen_seq_account_transaction($arr_seq);
			$data_insert_in['seq_no'] = @$seq_no;

			$this->db->insert('coop_account_transaction', $data_insert_in);
		}
		
		if($close_account_interest_return != 0){
			$transaction_balance = $transaction_balance-$close_account_interest_return;
			$data_insert_in = array();
			$data_insert_in['transaction_time'] = $transaction_time;
			$data_insert_in['transaction_list'] = 'WTI';
			$data_insert_in['transaction_withdrawal'] = $close_account_interest_return;
			$data_insert_in['transaction_deposit'] = '0';
			$data_insert_in['transaction_balance'] = $transaction_balance;
			$data_insert_in['transaction_no_in_balance'] = $transaction_no_in_balance;
			$data_insert_in['user_id'] = $_SESSION['USER_ID'];
			$data_insert_in['account_id'] = $account_id;
			$data_insert_in['bank_id'] = $bank_id;
			$data_insert_in['branch_code'] = $branch_code;
			$data_insert_in['local_account_id'] = $local_account_id;
			$data_insert_in['cheque_no'] = $cheque_number;
			$data_insert_in['other'] = $other;
			$data_insert_in['transfer_other'] = $transfer_other;

			//ดึงข้อมูลลำดับรายการ ของรายการถัดไป
			$arr_seq = array(); 
			$arr_seq['account_id'] = $account_id; 
			$arr_seq['transaction_list'] = $data_insert_in['transaction_list'];
			$seq_no = $this->deposit_seq->gen_seq_account_transaction($arr_seq);
			$data_insert_in['seq_no'] = @$seq_no;

			$this->db->insert('coop_account_transaction', $data_insert_in);
		}
		
		//คืนเงินต้นเงินฝาก ให้สหกรณ์
		if($close_account_principal_return != 0){
			$transaction_balance = $transaction_balance-$close_account_principal_return;			
			
			//ใบเสร็จคืนเงิน
			$arr_receipt_refund = array();
			$get_member_id = $this->deposit_model->get_member_id($account_id);
			$account_member_name = $this->deposit_model->get_account_member_name($account_id);
			//echo '<pre>'; print_r($get_member_id); echo '</pre>';
			//echo 'account_id='.$account_id.'<br>'; exit;
			$text_member = $account_member_name.' รหัสมาชิก '.$get_member_id;
			$arr_receipt_refund['member_id'] = $this->deposit_model->get_member_id($account_id);
			$arr_receipt_refund['account_id'] = $account_id;
			$arr_receipt_refund['pay_principal'] = $close_account_principal_return;
			$arr_receipt_refund['pay_type'] = $_POST['pay_type'];
			$arr_receipt_refund['date_transaction'] = $transaction_time;
			$arr_receipt_refund['pay_for'] = "เงินฝากพนักงาน"; //ได้รับเงินจาก
			$arr_receipt_refund['pay_description'] = "เงินรอจ่ายคืน ".$text_member; //รายการ
			$arr_receipt_refund['account_list_id'] = "30"; //เงินฝาก
			$arr_receipt_refund['process'] = "deposit";
			$save_receipt_refund = $this->receipt_model->save_receipt_refund($arr_receipt_refund);
			$receipt_refund_id = $save_receipt_refund['receipt_refund_id'];
			
			
			$data_insert_in = array();
			$data_insert_in['transaction_time'] = $transaction_time;
			$data_insert_in['transaction_list'] = 'RE/COOP';
			$data_insert_in['transaction_withdrawal'] = $close_account_principal_return;
			$data_insert_in['transaction_deposit'] = '0';
			$data_insert_in['transaction_balance'] = $transaction_balance;
			$data_insert_in['transaction_no_in_balance'] = $transaction_no_in_balance;
			$data_insert_in['user_id'] = $_SESSION['USER_ID'];
			$data_insert_in['account_id'] = $account_id;
			$data_insert_in['bank_id'] = $bank_id;
			$data_insert_in['branch_code'] = $branch_code;
			$data_insert_in['local_account_id'] = $local_account_id;
			$data_insert_in['cheque_no'] = $cheque_number;
			$data_insert_in['other'] = $other;
			$data_insert_in['transfer_other'] = $transfer_other;
			$data_insert_in['receipt_refund_id'] = $receipt_refund_id;

			//ดึงข้อมูลลำดับรายการ ของรายการถัดไป
			$arr_seq = array(); 
			$arr_seq['account_id'] = $account_id; 
			$arr_seq['transaction_list'] = $data_insert_in['transaction_list'];
			$seq_no = $this->deposit_seq->gen_seq_account_transaction($arr_seq);
			$data_insert_in['seq_no'] = @$seq_no;

			$this->db->insert('coop_account_transaction', $data_insert_in);			
		}
		
		if($transaction_balance != 0){
			$data_insert = array();
			$data_insert['transaction_time'] = $transaction_time;
			$data_insert['transaction_list'] = $transaction_list;
			$data_insert['transaction_withdrawal'] = $transaction_balance;
			$data_insert['transaction_deposit'] = '0';
			$data_insert['transaction_balance'] = '0';
			$data_insert['transaction_no_in_balance'] = '0';
			$data_insert['user_id'] = $_SESSION['USER_ID'];
			$data_insert['account_id'] = $account_id;
			$data_insert['bank_id'] = $bank_id;
			$data_insert['branch_code'] = $branch_code;
			$data_insert['local_account_id'] = $local_account_id;
			$data_insert['cheque_no'] = $cheque_number;
			$data_insert['other'] = $other;
			$data_insert['transfer_other'] = $transfer_other;

			//ดึงข้อมูลลำดับรายการ ของรายการถัดไป
			$arr_seq = array(); 
			$arr_seq['account_id'] = $account_id; 
			$arr_seq['transaction_list'] = $data_insert['transaction_list'];
			$seq_no = $this->deposit_seq->gen_seq_account_transaction($arr_seq);
			$data_insert['seq_no'] = @$seq_no;

			$this->db->insert('coop_account_transaction', $data_insert);
		}
		
		$data_insert = array();
		$data_insert['account_amount'] = '0';
		$data_insert['account_status'] = '1';
		$data_insert['sequester_status'] = '1';
		$data_insert['sequester_status_atm'] = '1';
		$data_insert['close_account_date'] = $transaction_time;
		$data_insert['close_account_pay_type'] = $_POST['pay_type'];
		$this->db->where('account_id', $account_id);
		$this->db->update('coop_maco_account',$data_insert);

		$this->db->where("account_id", $account_id);
		$account_guarantee = $this->db->get("coop_account_guarantee_book_saving")->result_array();
		if($account_guarantee && $transaction_balance!=0){
			$this->db->set("status", "1");
			$this->db->set("update_datetime", date("Y-m-d h:i:s"));
			$this->db->set("update_by", $_SESSION['USER_ID']);
			$this->db->where("account_id", $account_id);
			$this->db->update("coop_account_guarantee_book_saving");

			$member_id = $this->db->get_where("coop_maco_account", array(
				"account_id" => $account_id
			))->result_array()[0]['mem_id'];

			$this->db->where("unique_account", 1);
			$this->db->limit("1");
			$type_id = $this->db->get("coop_deposit_type_setting")->result_array()[0]['type_id'];

			$this->db->where("type_id", $type_id);
			$this->db->where("mem_id", $member_id);
			$this->db->where("account_status = 0");
			$this->db->limit(1);
			$transfer_to_account_id = $this->db->get("coop_maco_account")->result_array()[0]['account_id'];

			$this->db->select(array('transaction_balance','transaction_no_in_balance'));
			$this->db->from('coop_account_transaction');
			$this->db->where("account_id = '".$transfer_to_account_id."'");
			$this->db->order_by('transaction_time DESC, transaction_id DESC');
			$this->db->limit(1);
			$row_transfer = $this->db->get()->result_array()[0];
			
			$data_insert = array();
			$data_insert['transaction_time'] = $transaction_time;
			$data_insert['transaction_list'] = "XD";
			$data_insert['transaction_withdrawal'] = "0";
			$data_insert['transaction_deposit'] = $transaction_balance;
			$data_insert['transaction_balance'] = ($row_transfer['transaction_balance'] + $transaction_balance);
			$data_insert['transaction_no_in_balance'] = '0';
			$data_insert['user_id'] = $_SESSION['USER_ID'];
			$data_insert['account_id'] = $transfer_to_account_id;
			$data_insert['bank_id'] = $bank_id;
			$data_insert['branch_code'] = $branch_code;
			$data_insert['local_account_id'] = $local_account_id;
			$data_insert['cheque_number'] = $cheque_number;
			$data_insert['other'] = $other;
			$data_insert['transfer_other'] = $transfer_other;

			//ดึงข้อมูลลำดับรายการ ของรายการถัดไป
			$arr_seq = array(); 
			$arr_seq['account_id'] = $transfer_to_account_id; 
			$arr_seq['transaction_list'] = $data_insert['transaction_list'];
			$seq_no = $this->deposit_seq->gen_seq_account_transaction($arr_seq);
			$data_insert['seq_no'] = @$seq_no;
			
			$this->db->insert('coop_account_transaction', $data_insert);
		}
		if(isset($receipt_refund_id)) {
			echo "<script> window.open('" . PROJECTPATH . "/receipt/receipt_refund/" .@$receipt_refund_id. "','_blank') </script>";
		}
		$this->center_function->toast('ทำรายการปิดบัญชีเรียบร้อยแล้ว');
		echo "<script> document.location.href = '".PROJECTPATH."/save_money' </script>";
	}
	

	public function account_detail()
	{
		$arr_data = array();

		//Set page num if empty
		if (empty($_GET["page"])) $_GET["page"] = 1;
		
		$account_id = $this->input->get('account_id');
		$arr_data['account_id'] = $account_id;
		
		$this->db->select(array('min_first_deposit','month_conclude'));
		$this->db->from('coop_deposit_setting');
		$this->db->order_by('deposit_setting_id DESC');
		$row = $this->db->get()->result_array();
		
		$arr_data['min_first_deposit'] = $row[0]['min_first_deposit'];
		$arr_data['month_conclude'] = $row[0]['month_conclude'];
		
		$this->db->select(array('t1.*','t3.type_id','t3.type_name','t3.deduct_guarantee_id'));
		$this->db->from('coop_maco_account as t1');
		$this->db->join('coop_deposit_type_setting as t3','t1.type_id = t3.type_id','left');
		$this->db->where("account_id = '".$account_id."'");
		$row = $this->db->get()->result_array();
		$arr_data['row_memberall'] = @$row[0];
		
		//$this->db->select('*');
		$this->db->select('member_id,firstname_th,lastname_th');
		$this->db->from('coop_mem_apply');
		$this->db->where("member_id = '".$arr_data['row_memberall']['mem_id']."'");
		$row = $this->db->get()->result_array();
		$arr_data['row_member'] = @$row[0];
		
		$this->db->select(array('transaction_balance'));
		$this->db->from('coop_account_transaction');
		$this->db->where("account_id = '".$account_id."'");
		$this->db->order_by("transaction_time DESC,transaction_id DESC");
		$row = $this->db->get()->result_array();
		$arr_data['last_transaction'] = @$row[0];

		$this->db->where("account_id", $account_id);
		$this->db->join("coop_loan", "coop_loan_guarantee_saving.loan_id = coop_loan.id", "inner");
		$is_guarantee_loan = $this->db->get("coop_loan_guarantee_saving")->row_array();
		$arr_data['is_guarantee_loan'] = (@$is_guarantee_loan['loan_status']=="1") ? true : false;
		
		$show_conclude_checkbox = '0';
		if(@$arr_data['row_memberall']['last_time_print']!=''){
			$diff_last_print = date('Y-m-d',strtotime('- '.$arr_data['month_conclude'].' month'));
			$last_print_date = explode(" ",$arr_data['row_memberall']['last_time_print']);
			$last_print_date = $last_print_date[0];
			$arr_data['last_print_date'] = $last_print_date;
			if($arr_data['row_memberall']['last_time_print'] < $diff_last_print){
				$show_conclude_checkbox = '1';
			}
		}
		$arr_data['show_conclude_checkbox'] = @$show_conclude_checkbox;

		//Count amount of transaction
		$this->db->select('transaction_id');
		$this->db->from('coop_account_transaction_view AS coop_account_transaction');
		$this->db->where("account_id = '".$account_id."'");
		$transactionNum = count($this->db->get()->result_array());
		
		
		//จำนวนแสดงรายการต่อหน้าตามตั้งค่าการพิมพ์ปกสมุดบัญชีเงินฝาก		
		$stagement_row_in_page = $this->db->select('COUNT(row_id) AS c_row')->from('coop_book_bank_stagement_row')->where("style_id = '1'")->get()->row_array()['c_row'];
		if($stagement_row_in_page == 0){
			$stagement_row_in_page = 26;
		}
		
		$maxPage = $transactionNum%$stagement_row_in_page > 0 ? floor(($transactionNum/$stagement_row_in_page)) + 1 : $transactionNum/$stagement_row_in_page;

		$x=0;
		$join_arr = array();
		$join_arr[$x]['table'] = 'coop_user';
		$join_arr[$x]['condition'] = 't1.user_id = coop_user.user_id';
		$join_arr[$x]['type'] = 'left';
		
		$this->paginater_all->type(DB_TYPE);
		$this->paginater_all->select('t1.*, coop_user.user_name');
		$this->paginater_all->main_table('coop_account_transaction_view AS t1');
		$this->paginater_all->where("account_id = '".$account_id."'");

		//Set First Page is last page
		$this->paginater_all->page_now($maxPage - @$_GET["page"] + 1);
		$this->paginater_all->per_page($stagement_row_in_page);
		$this->paginater_all->page_link_limit(20);
		//$this->paginater_all->order_by('t1.seq_no ASC,t1.c_num ASC');
		$this->paginater_all->order_by('t1.transaction_time ASC,t1.transaction_id ASC,t1.c_num ASC');
		$this->paginater_all->join_arr($join_arr);
		$row = $this->paginater_all->paginater_process();
		$paging = $this->pagination_center->paginating(intval($_GET["page"]), $row['num_rows'], $row['per_page'], $row['page_link_limit'], $_GET);//$page_now = 1, $row_total = 1, $per_page = 20, $page_limit = 20
		
		$i = $row['page_start'];

		$arr_data['num_rows'] = $row['num_rows'];
		$arr_data['paging'] = $paging;
		$arr_data['data'] = $row['data'];
		$arr_data['i'] = $i;
		
		$arr_data['arr_run_row'] = $this->report_accrued->get_run_row_transaction($account_id);
		$arr_data['arr_date_due'] = $this->report_accrued->get_date_transaction($account_id);
		
		$this->db->select('transaction_id');
		$this->db->from('coop_account_transaction');
		$this->db->where("account_id = '".$account_id."'");
		$row = $this->db->get()->result_array();
		$num_arr = array();
		$i = 1;
		foreach($row as $key => $value){
			$num_arr[$value['transaction_id']] = $i++;
		}
		
		$arr_data['num_arr'] = $num_arr;
		
		$this->db->select('money_type_name_short');
		$this->db->from('coop_money_type');
		$this->db->where("id='1'");
		$row = $this->db->get()->result_array();
		$arr_data['row_deposit'] = $row[0];
		
		$this->db->select('money_type_name_short');
		$this->db->from('coop_money_type');
		$this->db->where("id='2'");
		$row = $this->db->get()->result_array();
		$arr_data['row_with'] = $row[0];
		
		$this->db->select('user_permission_id');
		$this->db->from('coop_user_permission');
		$this->db->where("user_id = '".$_SESSION['USER_ID']."' AND menu_id = '187'");
		$row = $this->db->get()->result_array();
		if($row[0]['user_permission_id']==''){
			$arr_data['cancel_transaction_display'] = "display:none;";
		}else{
			$arr_data['cancel_transaction_display'] = "";
		}

		//Get total interest balance for case of allow to withdrawal interest before due.
		$int_total = 0;
		$int_trans = $this->db->select("SUM(transaction_deposit) as sum_interest")->from("coop_account_transaction")->where("transaction_list IN ('INT', 'IN') AND account_id = '".$account_id."'")->get()->row_array();
		$int_total += $int_trans['sum_interest'];
		$withdrawal_trans = $this->db->select("SUM(transaction_withdrawal) as sum_withdrawal")->from("coop_account_transaction")->where("account_id = '".$account_id."'")->get()->row_array();
		if(!empty($withdrawal_trans)) $int_total -= $withdrawal_trans['sum_withdrawal'];
		$arr_data['int_total'] = ($int_total < 0)?0:$int_total;

		$this->db->select(array('type_fee','pay_interest','num_month_before','percent_depositor','permission_type', 'staus_close_principal','is_withdrawal_specify','is_separate_withdrawal', 'allow_interest_withdrawal_bf_due'));
		$this->db->from('coop_deposit_type_setting_detail');
		$this->db->where("type_id = '".$arr_data['row_memberall']['type_id']."'");
		$this->db->order_by("type_detail_id DESC");
		$this->db->limit(1);
		$row_setting_detail = $this->db->get()->result_array();
		$row_setting_detail = $row_setting_detail[0];
		$arr_data['type_fee'] = $row_setting_detail['type_fee'];
		$arr_data['permission_type'] = $row_setting_detail['permission_type'];
		$arr_data['staus_close_principal'] = $row_setting_detail['staus_close_principalstaus_close_principal'];
		$arr_data['is_withdrawal_specify'] = $row_setting_detail['is_withdrawal_specify'];
		$arr_data['allow_interest_withdrawal_bf_due'] = $row_setting_detail['allow_interest_withdrawal_bf_due'];
		if($row_setting_detail['type_fee'] == '3'){
			if($row_setting_detail['pay_interest'] == '2'){ //ประเภทเงินฝากที่คิดดอกเบี้ย ตามวันที่ฝาก
				$arr_data['fix_withdrawal_amount'] = 0;
				$this->db->select(array('deposit_interest_balance'));
				$this->db->from('coop_account_transaction');
				$this->db->where("account_id = '".$account_id."' AND interest_period = '".$row_setting_detail['num_month_before']."' AND fixed_deposit_status = '0'");
				$row = $this->db->get()->result_array();
				if(!empty($row)){
					foreach($row as $key => $value){
						$arr_data['fix_withdrawal_amount'] += $value['deposit_interest_balance'];
						$arr_data['fix_withdrawal_status'] = 'success';
					}
				}else{
					$this->db->select(array('deposit_balance','transaction_time'));
					$this->db->from('coop_account_transaction');
					$this->db->where("account_id = '".$account_id."' AND fixed_deposit_status = '0'");
					$row2 = $this->db->get()->result_array();
					if(!empty($row2)){
						foreach($row2 as $key2 => $value2){
							$interest_rate = $row_setting_detail['percent_depositor'];
							$date_start = date('Y-m-d',strtotime($value2['transaction_time']));
							$date_end = date('Y-m-d');
							$diff = @date_diff(date_create($date_start),date_create($date_end));
							$date_count = @$diff->format("%a");
							$date_count = $date_count+1;
							
							$interest = ((($value2['deposit_balance']*@$interest_rate)/100)*$date_count)/365;
							
							$arr_data['fix_withdrawal_amount'] += ($value2['deposit_balance']+$interest); 
						}
						$arr_data['fix_withdrawal_status'] = 'fail';
					}
				}
			}else{
				$create_date = date('Y-m-d',strtotime($arr_data['row_memberall']['created']));
				$end_date = date('Y-m-d',strtotime('+ '.$row_setting_detail['num_month_before'].' month',strtotime($create_date)));
				$date_interest = date('Y-m-d');
				if($date_interest < $end_date){
					$this->db->select(array('transaction_balance','transaction_no_in_balance'));
					$this->db->from('coop_account_transaction');
					$this->db->where("account_id = '".$account_id."'");
					$this->db->order_by('transaction_time DESC, transaction_id DESC');
					$this->db->limit(1);
					$row_transaction = $this->db->get()->result_array();
					
					$interest_rate = $row_setting_detail['percent_depositor'];
					$date_start = $create_date;
					$date_end = $date_interest;
					$diff = @date_diff(date_create($date_start),date_create($date_end));
					$date_count = @$diff->format("%a");
					$date_count = $date_count+1;
					
					$interest = ((($row_transaction[0]['transaction_no_in_balance']*@$interest_rate)/100)*$date_count)/365;
					
					$arr_data['fix_withdrawal_amount'] = ($row_transaction[0]['transaction_no_in_balance']+$interest); 
					$arr_data['fix_withdrawal_status'] = 'fail';
				}
			}
		}
		
		//ถอนเงินแบบเลือกบัญชี
		if(!empty($account_id)){
			$data_withdrawal_chooses = $this->account_withdrawal_chooses($account_id);
			$arr_data['data_chooses'] = $data_withdrawal_chooses['data_chooses'];
			//$arr_data['data_setting_detail'] = $data_withdrawal_chooses['data_setting_detail'];
		}

		$arr_data['maco_account'] = $this->db->get_where("coop_maco_account", array(
			"mem_id" => $arr_data['row_memberall']['mem_id'],
			"account_status" => "0",
			"account_id not like" => $account_id
		))->result_array();

		//permission
		$this->db->select('user_permission_id');
		$this->db->from('coop_user_permission');
		$this->db->where("user_id = '".$_SESSION['USER_ID']."' AND menu_id = '408'");
		$row = $this->db->get()->result_array();
		if($row[0]['user_permission_id']==''){
			$arr_data['permission']['edit_transaction'] = false;
		}else{
			$arr_data['permission']['edit_transaction'] = true;
		}
		//
		
		$this->libraries->template('save_money/account_detail',$arr_data);
	}
	
	public function save_transaction(){
	    //echo"<pre>";print_r($this->input->post());echo"</pre>";exit;
		$data = $this->input->post();
		
		if($data['date_transaction']!="")
			$date_transaction = (explode("/", $data['date_transaction'])[2]-543)."-".(explode("/", $data['date_transaction'])[1])."-".(explode("/", $data['date_transaction'])[0]);

		$time_transaction = ($data['time_transaction'] != '')?$data['time_transaction']:date('H:i:s');
		
		$this->db->select('*');
		$this->db->from('coop_account_transaction');
		$this->db->where("account_id = '".$data['account_id']."'");
		$this->db->order_by('transaction_time DESC, transaction_id DESC');
		$this->db->limit(1);
		$row = $this->db->get()->result_array();
		if(!empty($row)){
			$balance = $row[0]['transaction_balance'];
			$balance_no_in = $row[0]['transaction_no_in_balance'];
		}else{
			$balance = 0;
			$balance_no_in = 0;
		}
		$data['money'] = str_replace(',','',$data['money']);
		$data['total_amount'] = str_replace(',','',$data['total_amount']);
		$data['commission_fee'] = str_replace(',','',$data['commission_fee']);
		
		$this->db->select(array('t1.type_id'));
		$this->db->from('coop_maco_account as t1');
		$this->db->where("t1.account_id = '".$data['account_id']."'");
		$row_account = $this->db->get()->result_array();
		$row_account = $row_account[0];
		
		$this->db->select(array('type_fee','pay_interest','num_month_before','percent_depositor', 'is_withdrawal_specify'));
		$this->db->from('coop_deposit_type_setting_detail');
		$this->db->where("type_id = '".$row_account['type_id']."'");
		$this->db->order_by("type_detail_id DESC");
		$this->db->limit(1);
		$row_setting_detail = $this->db->get()->result_array();
		$row_setting_detail = $row_setting_detail[0];
		
		if($data["do"] == "deposit") {
			$sum = $balance + $data['money'];
			$sum_no_in = $balance_no_in + $data['money'];
			if($data['pay_type']=='0'){
				$transaction_list = $data['have_a_book_acc'];
			}else if($data['pay_type']=='1') {
                $transaction_list = 'XD';
            }else if($data['pay_type'] =='3'){
			    $transaction_list = 'IN';
			}else{
				$transaction_list = 'YPF';
			}
			$data_insert = array();
			$data_insert['transaction_time'] = ($date_transaction!="" && $data['custom_by_user_id']!="") ? $date_transaction." ".$time_transaction : date('Y-m-d')." ".$time_transaction;
			$data_insert['transaction_list'] = $transaction_list;
			$data_insert['transaction_withdrawal'] = '';
			$data_insert['transaction_deposit'] = $data['money'];
			$data_insert['transaction_balance'] = $sum;
			$data_insert['transaction_no_in_balance'] = $sum_no_in;
			$data_insert['user_id'] = $_SESSION['USER_ID'];
			$data_insert['account_id'] = $data['account_id'];
			$data_insert['permission_by_user'] = @$data['custom_by_user_id'];
			$data_insert['createtime'] = date("Y-m-d H:i:s");
			if(isset($data['cheque_number'])) {
                $data_insert['cheque_no'] = $data['cheque_number'];
			}
			if(isset($data['bank_id'])) {
                $data_insert['bank_id'] = $data['bank_id'];
			}
			if(isset($data['branch_code'])) {
                $data_insert['branch_code'] = $data['branch_code'];
			}
			if(isset($data['transfer_bank_account_name'])) {
                $data_insert['local_account_id'] = $data['transfer_bank_account_name'];
			}
			if(isset($data['other'])) {
                $data_insert['other'] = $data['other'];
			}
			if(isset($data['other'])) {
                $data_insert['transfer_other'] = $data['transfer_other'];
			}
			
			
			if($row_setting_detail['type_fee']=='3'){
				$data_insert['deposit_balance'] = $data['money'];
				$data_insert['fixed_deposit_status'] = '0';
				$data_insert['fixed_deposit_type'] = 'principal';
				$data_insert['date_end_saving'] = date('Y-m-d',strtotime('+ 24 month'));
				$data_insert['day_cal_interest'] = date('d');
			}

			//เช็คการถอนเงินแบบระบุยอดถอนเงินตามยอดฝาก แล้วเพิ่มลำดับของเงินฝาก
			if(@$row_setting_detail['is_withdrawal_specify'] == 1){
				$chk_account_no = $this->db->select('ref_account_no')->from('coop_account_transaction')->where("account_id = '".$data['account_id']."'")
					->order_by('ref_account_no DESC')->limit(1)->get()->row_array();
				if(@$chk_account_no['ref_account_no'] == ''){
					$gen_ref_account_no = 1;
				}else{
					$gen_ref_account_no = @$chk_account_no['ref_account_no']+1;
				}
				$data_insert['ref_account_no'] = @$gen_ref_account_no;
				$data_insert['balance_deposit'] = @$data['money'];
			}
			
			//ดึงข้อมูลลำดับรายการ ของรายการถัดไป
			$arr_seq = array(); 
			$arr_seq['account_id'] = $data['account_id']; 
			$arr_seq['transaction_list'] = $data_insert['transaction_list']; 
			$seq_no = $this->deposit_seq->gen_seq_account_transaction($arr_seq);
			$data_insert['seq_no'] = @$seq_no;

            $statement_status = 'debit';   // สถานะการจ่ายเงิน debit = เงินเข้าจากเคาน์เตอร์, credit  = เงินออกจากเคาน์เตอร์,
            $permission_id = $this->permission_model->permission_url($_SERVER['HTTP_REFERER'],$_SERVER['REQUEST_URI']);
            $this->tranction_financial_drawer->arrange_data_coop_financial_drawer($data_insert,$data['pay_type'],$permission_id,$statement_status,$_SERVER['REQUEST_URI']);



            if ($this->db->insert('coop_account_transaction', $data_insert)) {
				$transaction_id = $this->db->insert_id();
				$this->center_function->toast("ทำการฝากเงินเรียบร้อยแล้ว");
				//if($data_insert['permission_by_user']!=""){
					$this->update_st->update_balance_statement(array(
					    'date' => $data_insert['transaction_time'],
                        'account_id' => $data_insert['account_id']
                    ));
				//}
				
				$this->db->select('account_chart_id');
				$this->db->from('coop_account_match');
				$this->db->where("match_id = '".$row_account['type_id']."' AND match_type = 'save_transaction'");
				$this->db->limit(1);
				//echo "".$this->db->get_compiled_select(null, false)."<br><br><br><br>";
				$row = $this->db->get()->result_array();

                $data_process = array();
                // echo"<pre>";print_r($this->input->post());echo"</pre>";
                $process = 'save_money';
                $money = $data['money'];
                $ref = $_POST['account_id'];
                $match_type = 'save_transaction';
                $match_id = $row_account['type_id'];
                if($data['pay_type']=='0') {
                    $statement = 'credit';
                }else{
                    $statement = 'debit';
                }

                $data_process[] =   $this->account_transaction->set_data_account_trancetion_detail($match_id,$statement,$match_type,$ref,$money,$process);

                $process = 'save_money';
                $money = $data['money'];
                $ref = $_POST['account_id'];
                $match_type = 'main';
                $match_id = '1';
                if($data['pay_type']=='0') {
                    $statement = 'debit';
                }else{
                    $statement = 'credit';
                }

                $data_process[] =   $this->account_transaction->set_data_account_trancetion_detail($match_id,$statement,$match_type,$ref,$money,$process);
                $this->account_transaction->add_account_trancetion_detail($data_process);

				//@start 2021_04_09 Maiphrom บันทึกดอกเบี้ยคงค้าง
				//บันทึกแค่เงินฝาก เงินฝากออมทรัพย์พิเศษเกษียณเพิ่มสุข 12 เดือน และ  เงินฝากประจำเพื่อสร้างอนาคต ยกเว้นภาษี 24 เดือน
				//คำนวณไว้สิ้นปีของวันที่ทำรายการ
				$date_interest = date('Y-12-31',strtotime($data_insert['transaction_time']));
				//$date_interest = '2022-04-20';
				//$date_interest = date('Y-m-d',strtotime($data_insert['transaction_time']));
				$this->report_accrued->insert_transaction_acc_int($row_account['type_id'],$date_interest,$data['account_id'],$transaction_id);
				//@end 2021_04_09 Maiphrom บันทึกดอกเบี้ยคงค้าง
			}
			echo "<script> window.location.href = '".base_url(PROJECTPATH.'/save_money/account_detail?account_id='.$data['account_id'])."'</script>"; 
			exit();

		} else if($data["do"] == "withdrawal") {
			if($data['pay_type']=='0'){
				$transaction_list = 'CW';
				$transaction_list = $data['have_a_book_acc'];
			}else if($data['pay_type']=='1'){
				$transaction_list = 'XW';
			}else{
				$transaction_list = 'WCHE';
			}

			
			if($data['fix_withdrawal_status']!=''){
				if($data['fix_withdrawal_status'] == 'success'){
					$sum = $balance - $data['money'];
					$data_insert = array();
					$data_insert['transaction_time'] = ($date_transaction!="" && $data['custom_by_user_id']!="") ? $date_transaction." ".$time_transaction : date('Y-m-d')." ".$time_transaction;
					$data_insert['transaction_list'] = $transaction_list;
					$data_insert['transaction_withdrawal'] = $data['money'];
					$data_insert['transaction_deposit'] = '';
					$data_insert['transaction_balance'] = $sum;
					$data_insert['transaction_no_in_balance'] = $sum;
					$data_insert['user_id'] = $_SESSION['USER_ID'];
					$data_insert['account_id'] = $data['account_id'];

					if(isset($data['cheque_number'])) {
						$data_insert['cheque_no'] = $data['cheque_number'];
					}
					if(isset($data['bank_id'])) {
						$data_insert['bank_id'] = $data['bank_id'];
					}
					if(isset($data['branch_code'])) {
						$data_insert['branch_code'] = $data['branch_code'];
					}
					if(isset($data['transfer_bank_account_name'])) {
						$data_insert['local_account_id'] = $data['transfer_bank_account_name'];
					}
					if(isset($data['other'])) {
						$data_insert['other'] = $data['other'];
					}
					if(isset($data['other'])) {
						$data_insert['transfer_other'] = $data['transfer_other'];
					}
					
                    //ดึงข้อมูลลำดับรายการ ของรายการถัดไป
                    $arr_seq = array(); 
					$arr_seq['account_id'] = $data['account_id']; 
					$arr_seq['transaction_list'] = $data_insert['transaction_list']; 
					$seq_no = $this->deposit_seq->gen_seq_account_transaction($arr_seq);
					$data_insert['seq_no'] = @$seq_no;

					$this->db->insert('coop_account_transaction', $data_insert);
					
					$this->db->select(array('ref_transaction_id'));
					$this->db->from('coop_account_transaction');
					$this->db->where("account_id = '".$data['account_id']."' AND interest_period = '".$row_setting_detail['num_month_before']."' AND fixed_deposit_status = '0'");
					$row = $this->db->get()->result_array();
					foreach($row as $key => $value){
						$data_insert = array();
						$data_insert['fixed_deposit_status'] = '1';
						$data_insert['deposit_balance'] = '0';
						$this->db->where("transaction_id",$value['ref_transaction_id']);
						$this->db->update('coop_account_transaction',$data_insert);
						
						$data_insert = array();
						$data_insert['fixed_deposit_status'] = '1';
						$this->db->where("ref_transaction_id",$value['ref_transaction_id']);
						$this->db->update('coop_account_transaction',$data_insert);
					}
				}else{
					$this->deposit_libraries->cal_deposit_interest_by_account($data['account_id'],'real');
					
					$data_insert = array();
					$data_insert['transaction_time'] = ($date_transaction!="" && $data['custom_by_user_id']!="") ? $date_transaction." ".$time_transaction : date('Y-m-d')." ".$time_transaction;
					$data_insert['transaction_list'] = $transaction_list;
					$data_insert['transaction_withdrawal'] = $data['money'];
					$data_insert['transaction_deposit'] = '';
					$data_insert['transaction_balance'] = '0';
					$data_insert['transaction_no_in_balance'] = '0';
					$data_insert['user_id'] = $_SESSION['USER_ID'];
					$data_insert['account_id'] = $data['account_id'];

					if(isset($data['cheque_number'])) {
						$data_insert['cheque_no'] = $data['cheque_number'];
					}
					if(isset($data['bank_id'])) {
						$data_insert['bank_id'] = $data['bank_id'];
					}
					if(isset($data['branch_code'])) {
						$data_insert['branch_code'] = $data['branch_code'];
					}
					if(isset($data['transfer_bank_account_name'])) {
						$data_insert['local_account_id'] = $data['transfer_bank_account_name'];
					}
					if(isset($data['other'])) {
						$data_insert['other'] = $data['other'];
					}
					if(isset($data['other'])) {
						$data_insert['transfer_other'] = $data['transfer_other'];
					}
					
                    //ดึงข้อมูลลำดับรายการ ของรายการถัดไป
                    $arr_seq = array(); 
					$arr_seq['account_id'] = $data['account_id']; 
					$arr_seq['transaction_list'] = $data_insert['transaction_list']; 
					$seq_no = $this->deposit_seq->gen_seq_account_transaction($arr_seq);
					$data_insert['seq_no'] = @$seq_no;

					$this->db->insert('coop_account_transaction', $data_insert);

                    $statement_status = 'credit';   // สถานะการจ่ายเงิน debit = เงินเข้าจากเคาน์เตอร์, credit  = เงินออกจากเคาน์เตอร์,
                    $permission_id = $this->permission_model->permission_url($_SERVER['HTTP_REFERER'],$_SERVER['REQUEST_URI']);
                    $this->tranction_financial_drawer->arrange_data_coop_financial_drawer($data_insert,$data['pay_type'],$permission_id,$statement_status,$_SERVER['REQUEST_URI']);

                    $this->db->select(array('transaction_id'));
					$this->db->from('coop_account_transaction');
					$this->db->where("
						account_id = '".$data['account_id']."' 
						AND fixed_deposit_type='principal' 
						AND date_end_saving > '".date('Y-m-d H:i:s')."'
					");
					$row = $this->db->get()->result_array();
					foreach($row as $key => $value){
						$data_insert = array();
						$data_insert['fixed_deposit_status'] = '1';
						$data_insert['deposit_balance'] = '0';
						$this->db->where("transaction_id",$value['transaction_id']);
						$this->db->update('coop_account_transaction',$data_insert);
						
						$data_insert = array();
						$data_insert['fixed_deposit_status'] = '1';
						$this->db->where("ref_transaction_id",$value['transaction_id']);
						$this->db->update('coop_account_transaction',$data_insert);
					}
					
				}
			}else{
				$money = (empty($data['total_amount']))?$data['money']:$data['total_amount'];
				$sum = $balance - $money;
				$sum_no_in = $balance_no_in - $money;
				if($sum_no_in <= 0 ){$sum_no_in = 0;}
				if($sum < 0) {
					$this->center_function->toastDanger("ไม่สามารถถอนเงินได้เนื่องจากจำนวนเงินคงเหลือไม่พอ");
				} else {
					$data_insert = array();
					$data_insert['transaction_time'] = ($date_transaction!="" && $data['custom_by_user_id']!="") ? $date_transaction." ".$time_transaction : date('Y-m-d')." ".$time_transaction;
					$data_insert['transaction_list'] = $transaction_list;
					$data_insert['transaction_withdrawal'] = $money;
					$data_insert['transaction_deposit'] = '';
					$data_insert['transaction_balance'] = $sum;
					$data_insert['transaction_no_in_balance'] = $sum_no_in;
					$data_insert['user_id'] = $_SESSION['USER_ID'];
					$data_insert['account_id'] = $data['account_id'];
					if(isset($data['cheque_number'])) {
						$data_insert['cheque_no'] = $data['cheque_number'];
					}
					if(isset($data['bank_id'])) {
						$data_insert['bank_id'] = $data['bank_id'];
					}
					if(isset($data['branch_code'])) {
						$data_insert['branch_code'] = $data['branch_code'];
					}
					if(isset($data['transfer_bank_account_name'])) {
						$data_insert['local_account_id'] = $data['transfer_bank_account_name'];
					}
					if(isset($data['other'])) {
						$data_insert['other'] = $data['other'];
					}
					if(isset($data['other'])) {
						$data_insert['transfer_other'] = $data['transfer_other'];
					}
					
                    //ดึงข้อมูลลำดับรายการ ของรายการถัดไป
                    $arr_seq = array(); 
					$arr_seq['account_id'] = $data['account_id']; 
					$arr_seq['transaction_list'] = $data_insert['transaction_list']; 
					$seq_no = $this->deposit_seq->gen_seq_account_transaction($arr_seq);
					$data_insert['seq_no'] = @$seq_no;

					$this->db->insert('coop_account_transaction', $data_insert);

                    $statement_status = 'credit';   // สถานะการจ่ายเงิน debit = เงินเข้าจากเคาน์เตอร์, credit  = เงินออกจากเคาน์เตอร์,
                    $permission_id = $this->permission_model->permission_url($_SERVER['HTTP_REFERER'],$_SERVER['REQUEST_URI']);
                    $this->tranction_financial_drawer->arrange_data_coop_financial_drawer($data_insert,$data['pay_type'],$permission_id,$statement_status,$_SERVER['REQUEST_URI']);


                    //echo $this->db->last_query();
					//ค่าดำเนินการอื่นๆ
					if(@$data['commission_fee']){
						//echo $data['commission_fee'].'<hr>';
						$sum = $sum - $data['commission_fee'];
						$sum_no_in = $sum_no_in - $data['commission_fee'];
						$data_insert = array();
						$data_insert['transaction_time'] = ($date_transaction!="" && $data['custom_by_user_id']!="") ? $date_transaction." ".$time_transaction : date('Y-m-d')." ".$time_transaction;
						$data_insert['transaction_list'] = 'CM/FE';
						$data_insert['transaction_withdrawal'] = $data['commission_fee'];
						$data_insert['transaction_deposit'] = '';
						$data_insert['transaction_balance'] = $sum;
						$data_insert['transaction_no_in_balance'] = $sum_no_in;
						$data_insert['user_id'] = $_SESSION['USER_ID'];
						$data_insert['account_id'] = $data['account_id'];
						if(isset($data['cheque_number'])) {
							$data_insert['cheque_no'] = $data['cheque_number'];
						}
						if(isset($data['bank_id'])) {
							$data_insert['bank_id'] = $data['bank_id'];
						}
						if(isset($data['branch_code'])) {
							$data_insert['branch_code'] = $data['branch_code'];
						}
						if(isset($data['transfer_bank_account_name'])) {
							$data_insert['local_account_id'] = $data['transfer_bank_account_name'];
						}
						if(isset($data['other'])) {
							$data_insert['other'] = $data['other'];
						}
						if(isset($data['other'])) {
							$data_insert['transfer_other'] = $data['transfer_other'];
						}
						
                        //ดึงข้อมูลลำดับรายการ ของรายการถัดไป
                        $arr_seq = array(); 
						$arr_seq['account_id'] = $data['account_id']; 
						$arr_seq['transaction_list'] = $data_insert['transaction_list']; 
						$seq_no = $this->deposit_seq->gen_seq_account_transaction($arr_seq);
						$data_insert['seq_no'] = @$seq_no;

						$this->db->insert('coop_account_transaction', $data_insert);
					}
					//echo $this->db->last_query();


                    $data_process = array();

                    // echo"<pre>";print_r($this->input->post());echo"</pre>";
                    $process = 'withdraw_money';
                    $money = $data['money'];
                    $ref = $_POST['account_id'];
                    $match_type = 'save_transaction';
                    $match_id = $row_account['type_id'];
                    if($data['pay_type']=='0') {
                        $statement = 'debit';
                    }else{
                        $statement = 'credit';
                    }

                    $data_process[] =   $this->account_transaction->set_data_account_trancetion_detail($match_id,$statement,$match_type,$ref,$money,$process);

                    $process = 'withdraw_money';
                    $money = $data['money'];
                    $ref = $_POST['account_id'];
                    $match_type = 'main';
                    $match_id = '1';
                    if($data['pay_type']=='0') {
                        $statement = 'credit';
                    }else{
                        $statement = 'debit';
                    }

                    $data_process[] =   $this->account_transaction->set_data_account_trancetion_detail($match_id,$statement,$match_type,$ref,$money,$process);
                    $this->account_transaction->add_account_trancetion_detail($data_process);

                    if($date_transaction!="" && $data['custom_by_user_id']!="") {

                        $st_last = $this->db->select('transaction_time')->from("coop_account_transaction")->where(
                            array(
                                "transaction_time <=" => $data_insert['transaction_time'],
                                "account_id" => $data['account_id']
                            ))->order_by('transaction_time, transaction_id', 'DESC')
                            ->limit(1)->get()->row_array();

                        $data_trigger = array(
                            'date' => $st_last['transaction_time'],
                            'account_id' => $data['account_id']
                        );
                        $this->update_st->update_balance_statement($data_trigger);
                    }

					
				}
			}
			//exit();
			echo "<script> window.location.href = '".base_url(PROJECTPATH.'/save_money/account_detail?account_id='.$data['account_id'])."'</script>"; 
			exit();
		}else if($data["do"] == "update_cover") {
			
			$this->db->select('*');
			$this->db->from('coop_maco_account');
			$this->db->where("account_id = '".$data['account_id']."'");
			$row = $this->db->get()->result_array();
			if($row[0]['book_number'] == $data['book_number']){
				$this->center_function->toastDanger("เล่มบัญชีของท่านเป็นเล่มที่ ".$data['book_number']." แล้ว");
			}else{
				$data_insert = array();
				$data_insert['book_number'] = $data['book_number'];
				$data_insert['print_number_point_now'] = '1';
				$this->db->where('account_id', $data['account_id']);
				$this->db->update('coop_maco_account', $data_insert);
				$this->center_function->toast("เพิ่มเล่มบัญชีเรียบร้อยแล้ว");
			}
			echo "<script> window.location.href = '".base_url(PROJECTPATH.'/save_money/account_detail?account_id='.$data['account_id'])."'</script>"; 
			exit();
		}
		
	}
	
	function book_bank_cover_pdf(){
		$arr_data = array();
		$account_id = $this->input->get('account_id');
		$arr_data['account_id'] = $account_id;
		$this->db->select(array('account_name','mem_id','book_number'));
		$this->db->from('coop_maco_account');
		$this->db->where("account_id = '".$account_id."'");
		$row = $this->db->get()->result_array();
		$arr_data['row'] = $row[0];
		
		$this->db->select(array('mem_group_id'));
		$this->db->from('coop_mem_apply');
		$this->db->where("member_id = '".$row[0]['mem_id']."'");
		$row_group = $this->db->get()->result_array();
		$arr_data['row_group'] = $row_group[0];
		
		$this->db->select(array('mem_group_name'));
		$this->db->from('coop_mem_group');
		$this->db->where("mem_group_id = '".$row_group[0]['mem_group_id']."'");
		$row_gname = $this->db->get()->result_array();
		if(!empty($row_gname)){
			$arr_data['row_gname'] = $row_gname[0];
		}else{
			$arr_data['row_gname']['mem_group_name'] = '';
		}
		
		
		$this->load->view('save_money/book_bank_cover_pdf',$arr_data);
	}
	
	function book_bank_page_pdf(){
		$arr_data = array();
		
		
		$this->load->view('save_money/book_bank_page_pdf',$arr_data);
	}
	
	function change_status($transaction_id, $account_id){
		$data_insert = array();
		$data_insert['print_status'] = '';
		$data_insert['print_number_point'] = '';
		$data_insert['book_number'] = '';
		$this->db->where(array('transaction_id >=' => $transaction_id, 'account_id'=>$account_id));
		$this->db->update('coop_account_transaction', $data_insert);
		
		$data_insert = array();
		$data_insert['print_number_point_now'] = '';
		$data_insert['last_time_print'] = '';
		$this->db->where('account_id', $account_id);
		$this->db->update('coop_maco_account', $data_insert);
		
		$this->center_function->toast("ยกเลืกพิมพ์รายการเรียบร้อยแล้ว");
		echo "<script> document.location.href = '".base_url(PROJECTPATH.'/save_money/account_detail?account_id='.$account_id)."'</script>";
		exit();
	}
	
	//เช็คฝาเงินต่ำสุด-สูงสุด
	function check_max_min_deposit(){
		$money_deposit = @$_POST['money_deposit'];
		$type_id = @$_POST['type_id'];
		$account_id = @$_POST['account_id'];
		$today = date('Y-m-d');
		
		$this->db->select(array('*'));
		$this->db->from('coop_deposit_type_setting');
		$this->db->join("coop_deposit_type_setting_detail","coop_deposit_type_setting_detail.type_id = coop_deposit_type_setting.type_id","left");
		$this->db->where("coop_deposit_type_setting.type_id = '{$type_id}' AND coop_deposit_type_setting_detail.start_date <= '{$today}'");
		$this->db->order_by('coop_deposit_type_setting_detail.start_date DESC');
		$this->db->limit(1);
		$rs = $this->db->get()->result_array();
		$row = @$rs[0];
		
		$this->db->select('SUM(transaction_deposit) AS sum_int');
		$this->db->from('coop_account_transaction');
		$this->db->where("account_id = '".$account_id."' AND transaction_list IN ('INT', 'IN')");
		$rs = $this->db->get()->result_array();
		$row_int = @$rs[0];
		
		$this->db->select('*');
		$this->db->from('coop_account_transaction');
		$this->db->where("account_id = '".$account_id."'");
		$this->db->order_by('transaction_time DESC, transaction_id DESC');
		$this->db->limit(1);
		$rs = $this->db->get()->result_array();
		$row_tran = @$rs[0];
		$balance = $row_tran["transaction_balance"] + $money_deposit - (double)$row_int["sum_int"];
		
		if($row['is_deposit_num'] == '1'){
			$this->db->select('money_type_name_short');
			$this->db->from('coop_money_type');
			$this->db->where("id='1'");
			$row_deposit = $this->db->get()->row_array();		
			
			$this->db->select('money_type_name_short');
			$this->db->from('coop_money_type');
			$this->db->where("id='9'");
			$row_error = $this->db->get()->row_array();
			
			//เช็คจำนวนครั้งที่ฝากรายเดือน หรือ ปี
			$check_month = date('Y-m');
			$check_year = date('Y');
			$chk_deposit_num_type = (@$row['deposit_num_type'] == '0')?$check_month:$check_year;
			
			$this->db->select('transaction_time,transaction_list,transaction_deposit');
			$this->db->from('coop_account_transaction');
			$this->db->where("account_id = '".$account_id."' AND transaction_time LIKE '".$chk_deposit_num_type."%'");
			$this->db->order_by('transaction_time DESC, transaction_id DESC');
			$rs_deposit_num = $this->db->get()->result_array();	
			$n_deposit = 0;
			foreach($rs_deposit_num AS $key=>$row_deposit_num){				
				if(@$row_deposit_num['transaction_list'] == @$row_deposit['money_type_name_short']){
					$n_deposit++;
				}
				
				if(@$row_deposit_num['transaction_list'] == @$row_error['money_type_name_short']){
					$n_deposit--;				
				}
			}		
		}
		
		if($money_deposit < $row['amount_min'] && $row['amount_min'] != 0){
			echo 'การฝากเงินต้องฝากเงินต้นขั้นต่ำ '.number_format($row['amount_min']).' บาท';
		}else if($money_deposit > $row['amount_max_time'] && $row['amount_max_time'] != 0){
			echo 'การฝากเงินสูงสุดต่อครั้งต้องไม่เกิน '.number_format($row['amount_max_time']).' บาท';
		}else if($balance > $row['amount_max'] && $row['amount_max'] != 0){
			echo 'การฝากเงินรวมทั้งหมดต้องไม่เกิน '.number_format($row['amount_max']).' บาท';
		}else if(@$n_deposit >= @$row['deposit_num']){
			//ฝากเงินได้ไม่เกิน เดือน หรือ ปี ละ 2 ครั้ง
			$deposit_num_type = (@$row['deposit_num_type'] == '0')?'เดือน':'ปี';
			echo 'ฝากเงินได้ไม่เกิน '.$deposit_num_type.' ละ '.@$row['deposit_num'].'  ครั้ง';
		}else{
			echo 'Y';
		}
		exit;
	}
	
	//เช็คถอนเงินต่ำสุด-สูงสุด
	function check_max_min_withdrawal(){
		$money = (@$_POST['money'] != '')?$_POST['money']:0;
		$type_id = @$_POST['type_id'];
		$account_id = @$_POST['account_id'];
		$today = date('Y-m-d');
		
		$this->db->select(array('*'));
		$this->db->from('coop_deposit_type_setting');
		$this->db->join("coop_deposit_type_setting_detail","coop_deposit_type_setting_detail.type_id = coop_deposit_type_setting.type_id","left");
		$this->db->where("coop_deposit_type_setting.type_id = '{$type_id}' AND coop_deposit_type_setting_detail.start_date <= '{$today}'");
		$this->db->order_by('coop_deposit_type_setting_detail.start_date DESC');
		$this->db->limit(1);
		$rs = $this->db->get()->result_array();
		$row = @$rs[0];
		
		$this->db->select('*');
		$this->db->from('coop_account_transaction');
		$this->db->where("account_id = '".$account_id."'");
		$this->db->order_by('transaction_time DESC, transaction_id DESC');
		$this->db->limit(1);
		$rs = $this->db->get()->result_array();
		$row_tran = @$rs[0];
		$balance = $row_tran["transaction_balance"] - $money;
		//echo $row_tran["transaction_balance"].' - '.$money.'<br>';
		//echo $this->db->last_query();
		
		//เช็คยอดเงินบัญชีค้ำประกัน  คำนวณจาก ยอดเงินกู้คงเหลือ - ทุนเรือนหุ้นทั้งหมด = เงินฝากที่ถอนไม่ได้
		$arr_guarantee_loan=$this->deposit_model->get_withdrawal_guarantee_loan($account_id,$row_tran["transaction_balance"]);
		$is_guarantee_loan = $arr_guarantee_loan['is_guarantee_loan'];
		$guarantee_balance = $arr_guarantee_loan['guarantee_balance'];
		
		if($money < $row['withdraw_min'] && $row['is_withdraw_min']){
			echo 'การถอนเงินต้องถอนขั้นต่ำ '.number_format($row['is_withdraw_min']).' บาท';
		}else if($balance < $row['balance_min'] && $row['is_balance_min']){
			echo 'ต้องมีเงินคงเหลือไม่ต่ำกว่า '.number_format($row['balance_min']).' บาท';
		}else if($guarantee_balance < $money && $is_guarantee_loan){
			echo "ไม่สามารถถอนเงินจำนวน ".number_format($money,2)." บาท \nได้เนื่องจากมีเงินกู้ในทุนเรือนหุ้นบวกเงินฝาก \nยอดเงินสามารถถอนได้ คือ ".number_format($guarantee_balance,2)." บาท";
		}else{
			echo 'Y';
		}
		
		exit;
	}
	
	//เช็คค่าธรรมเนียมการถอน
	function check_fee_withdrawal(){
		$money_withdrawal = @$_POST['money_withdrawal'];
		$type_id = @$_POST['type_id'];
		$account_id = @$_POST['account_id'];
		$today = date('Y-m-d');
		$yymm_now = date('Y-m');
		
		$this->db->select(array('*'));
		$this->db->from('coop_deposit_type_setting');
		$this->db->join("coop_deposit_type_setting_detail","coop_deposit_type_setting_detail.type_id = coop_deposit_type_setting.type_id","left");
		$this->db->where("coop_deposit_type_setting.type_id = '{$type_id}' AND coop_deposit_type_setting_detail.start_date <= '{$today}'");
		$this->db->order_by('coop_deposit_type_setting_detail.start_date DESC');
		$this->db->limit(1);
		$rs = $this->db->get()->result_array();
		$row = @$rs[0];
		
		if($row['type_fee'] == '1'){
			//ไม่มีค่าธรรมเนียมการถอน
			echo '';
		}else if($row['type_fee'] == '2'){
			//มีค่าธรรมเนียมการถอน % ของยอดเงินที่ถอน
			$fee = ($money_withdrawal*$row['percent_fee'])/100;
			echo $fee;
		}else if($row['type_fee'] == '3'){
			//มีค่าธรรมเนียมการถอน เมื่อถอนก่อนกำหนด ผู้ฝากได้รับดอกเบี้ย % ที่เหลือสหกรณ์ได้รับดอกเบี้ย
			//$row['num_month_before'];
			//$row['percent_depositor'];
			echo '';
		}else{
			//ไม่มีค่าธรรมเนียมการถอน
			echo '';
		}
		
		
		if($row['staus_withdraw'] == '1'){
			//ถอนได้เดือน ล่ะ กี่ครั้ง withdraw_num
			//ถ้า ครั้งที่ กำหนด  จะเสีย % ในการถอน  withdraw_num_interest
			// % ที่จะเสียในการถอน withdraw_percent_interest
			$count_withdraw = 0;
			$fee = '';
			$this->db->select("COUNT(*) AS c");
			$this->db->from('coop_account_transaction');
			$this->db->where("account_id = '{$account_id}' AND transaction_list = 'CW' AND YEAR(transaction_time) = YEAR('{$today}')".($row["withdraw_num_unit"] == "1" ? "" : " AND MONTH(transaction_time) = MONTH('{$today}')"));
			$row_transaction = $this->db->get()->row_array();
			$count_withdraw = $row_transaction["c"];

			$this->db->select("COUNT(*) AS err");
			$this->db->from('coop_account_transaction');
			$this->db->where("account_id = '{$account_id}' AND transaction_list = 'ERR' AND YEAR(transaction_time) = YEAR('{$today}')".($row["withdraw_num_unit"] == "1" ? "" : " AND MONTH(transaction_time) = MONTH('{$today}')"));
			$row_transaction = $this->db->get()->row_array();
			$count_err = $row_transaction["err"];
			
			if(($count_withdraw-$count_err) >= $row["withdraw_num"]) {
				$fee = ($money_withdrawal*$row['withdraw_percent_interest'])/100;
				$fee = $fee < $row["withdraw_percent_min"] ? $row["withdraw_percent_min"] : $fee;
			}
			
			echo $fee;
		}	
		//echo '<pre>'; print_r($row); echo '</pre>';
		exit;
	}

	function deposit_cal_interest(){
		$arr_data = array();
		
		$this->db->select(array('*'));
		$this->db->from('coop_maco_account as t1');
		$this->db->where("account_status = '0'");
		$row = $this->db->get()->result_array();
		$arr_data['account_data'] = $row;
		if($_POST){
			$arr_data['interest_data'] = $this->test_deposit_interest($_POST);
			//exit;
		}
		$this->libraries->template('save_money/deposit_cal_interest',$arr_data);
	}
	
	function test_deposit_interest($data_post){
		//echo"<pre>";print_r($data_post);echo"</pre>"; //exit;
		$date_interest = $this->center_function->ConvertToSQLDate($data_post['date_interest']);
		$account_id = $data_post['account_id'];
		$day_interest = date('d',strtotime($date_interest));
		$this->db->select(array(
			't1.member_id',
			't2.account_id',
			't2.type_id',
			't2.created as create_account_date'
		));
		$this->db->from('coop_mem_apply as t1');
		$this->db->join('coop_maco_account as t2','t1.member_id = t2.mem_id','inner');
		$this->db->where("t2.account_id = '".$account_id."'");
		$rs_member = $this->db->get()->result_array();
		//echo"<pre>";print_r($rs_member);echo"</pre>"; //exit;
		foreach($rs_member as $key_member => $row_member){
			$transaction = $this->deposit_libraries->cal_deposit_interest($row_member, 'test_cal_interest', $date_interest, $day_interest);
		}
		//echo"<pre>";print_r($transaction);exit;
		return $transaction;
		//exit;
	}
	
	public function deposit_month()
	{
		if($this->input->get('member_id')!=''){
			//$mem_id = $this->input->get('member_id');
            //$mem_id = sprintf("%06d", $this->input->get('member_id'));
			$mem_id = $this->center_function->complete_member_id($this->input->get('member_id'));
		}else{
			$mem_id = '';
		}
		$arr_data = array();

		if($mem_id != '') {
			$this->db->select(array('salary','other_income','member_id'));
			$this->db->from('coop_mem_apply');
			$this->db->where("member_id LIKE '%".$mem_id."%'");
			$rs_member = $this->db->get()->result_array();
			$row_member = $rs_member[0];
			@$row_member['salary_income'] = @$row_member['salary']+ @$row_member['other_income'];
			$arr_data['row_member'] = @$row_member;
			$member_id = @$row_member['member_id'];
			
			$this->db->select(array('t1.type_id','t1.type_code','t2.account_id','t2.account_name'));
			$this->db->from('coop_deposit_type_setting AS t1');
			$this->db->join("coop_maco_account AS t2","t1.type_id = t2.type_id","left");
			$this->db->where("t2.mem_id = '".@$member_id."'");
			$rs_account = $this->db->get()->result_array();
			$arr_data['row_accounts'] = $rs_account;
			$row_account = $rs_account[0];
			$arr_data['row_account'] = @$row_account;

			$x=0;
			$join_arr = array();
			$join_arr[$x]['table'] = 'coop_user';
			$join_arr[$x]['condition'] = 'coop_deposit_month_transaction.admin_id = coop_user.user_id';
			$join_arr[$x]['type'] = 'left';
			
			$this->paginater_all->type(DB_TYPE);
			$this->paginater_all->select(array('coop_deposit_month_transaction.*','coop_user.user_name'));
			$this->paginater_all->main_table('coop_deposit_month_transaction');
			$this->paginater_all->where("member_id = '".$member_id."'");
			$this->paginater_all->page_now(@$_GET["page"]);
			$this->paginater_all->per_page(20);
			$this->paginater_all->page_link_limit(20);
			$this->paginater_all->order_by('id DESC');
			$this->paginater_all->join_arr($join_arr);
			$row = $this->paginater_all->paginater_process();
			//echo"<pre>";print_r($row);exit;
			$paging = $this->pagination_center->paginating($row['page'], $row['num_rows'], $row['per_page'], $row['page_link_limit']);//$page_now = 1, $row_total = 1, $per_page = 20, $page_limit = 20
			$i = $row['page_start'];


			$arr_data['num_rows'] = $row['num_rows'];
			$arr_data['paging'] = $paging;
			$arr_data['data'] = $row['data'];
			$arr_data['i'] = $i;
		}else{
			$arr_data['data'] = array();
			$arr_data['paging'] = '';
		}
		
		//list เดือน
		$arr_data['month_arr'] = $this->center_function->month_arr();
		$arr_data['month_short_arr'] = $this->center_function->month_short_arr();
		$this->libraries->template('save_money/deposit_month',$arr_data);
	}
	
	function save_deposit_month(){
		$data = $this->input->post();
		
		$data_insert = array();
		$data_insert['member_id'] = @$data['member_id'];
		$data_insert['account_id'] = @$data['account_id'];
		$data_insert['deduction_type'] = @$data['deduction_type'];
		$data_insert['deduction_month'] = @$data['month'];
		$data_insert['deduction_year'] = @$data['year'];
		$data_insert['total_amount'] = str_replace(',','',@$data['total_amount']);
		$data_insert['admin_id'] = @$_SESSION['USER_ID'];		
		$data_insert['updatetime'] = date('Y-m-d H:i:s');	
		
		$this->db->select('*');
		$this->db->from('coop_deposit_month_transaction');
		$this->db->where("member_id = '".@$data['member_id']."' AND deduction_month = '".@$data['month']."' AND deduction_year = '".@$data['year']."'
							AND account_id = '".$data['account_id']."' AND deduction_type ='0'");
		$this->db->order_by('id DESC');			
		$rs_deduction = $this->db->get()->result_array();
		//echo $this->db->last_query();
		if(!empty($rs_deduction)){
			$this->db->where("member_id",@$data['member_id']);
			$this->db->where("deduction_month",@$data['month']);
			$this->db->where("deduction_year",@$data['year']);
			$this->db->update('coop_deposit_month_transaction', $data_insert);

		}else{					
			$data_insert['createdatetime'] = date('Y-m-d H:i:s');
			$this->db->insert('coop_deposit_month_transaction', $data_insert);		
		}
		//exit;
		$this->center_function->toast('บันทึกข้อมูลเรียบร้อยแล้ว');
		
		echo "<script> document.location.href = '".PROJECTPATH."/save_money/deposit_month?member_id=".@$data['member_id']."' </script>";
		exit;
	}
	
	function check_deduction_month(){
		$month_arr = $this->center_function->month_arr();
		$mem_id = @$_POST['member_id'];
		$deduction_month = @$_POST['deduction_month'];
		$deduction_year = @$_POST['deduction_year'];
		$deduction_type = @$_POST['deduction_type'];
		
		$month_now = (int)date('m');
		$year_now = date('Y')+543;
		
		$this->db->select(array('member_id'));
		$this->db->from('coop_mem_apply');
		$this->db->where("member_id LIKE '%".$mem_id."%'");
		$rs_member = $this->db->get()->result_array();
		$row_member = $rs_member[0];
		$member_id = @$row_member['member_id'];
		
		$this->db->select('*');
		$this->db->from('coop_deposit_month_transaction');
		$this->db->where("member_id = '".@$member_id."' AND deduction_month = '".@$deduction_month."' AND deduction_year = '".@$deduction_year."'");
		$this->db->order_by('id DESC');			
		$rs_deduction = $this->db->get()->result_array();
		//echo $this->db->last_query();
		//exit;
		$count_all = 0;
		$count_refrain = 0;
		if(!empty($rs_deduction)){
			foreach($rs_deduction AS $key=>$value){
				if(@$value['deduction_type'] == '1'){
					$count_refrain++;
				}
				$count_all++;
			}
		}		
		
		$deduction_day = ($deduction_year-543)."-".sprintf("%02d",$deduction_month)."-01"; //เดือนที่เลือก
		$now_day = (date('Y'))."-".sprintf("%02d",date('m'))."-01"; //เดือนปัจจุบัน
		
		if($deduction_day < $now_day){
			echo "ไม่สามารถเลือกเดือน ".$month_arr[$deduction_month]." ".$deduction_year." ได้ \nเนื่องจากน้อยกว่าเดือน ปัจจุบัน";
		}else if($count_all != 0 && $deduction_type == '1'){
			echo "ไม่สามารถเลือกเดือน ".$month_arr[$deduction_month]." ".$deduction_year." ได้ \nเนื่องจากมีในระบบแล้ว";
		}else if($count_refrain != 0 && $deduction_type == '0'){
			echo "ไม่สามารถเลือกเดือน ".$month_arr[$deduction_month]." ".$deduction_year." ได้ \nเนื่องจากมีในระบบแล้ว";
		}else{	
			echo 'ok';
		}
		exit;
	}
	
	function check_member_id(){
		//$member_id = sprintf("%06d", @$_POST['member_id']);
		$member_id = $this->center_function->complete_member_id(@$_POST['member_id']);
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
	
	function cancel_transaction($transaction_id){
		//echo $transaction_id;exit;
		$this->db->select(array(
			'account_id',
			'transaction_withdrawal',
			'transaction_deposit'
		));
		$this->db->from('coop_account_transaction');
		$this->db->where("transaction_id = '".$transaction_id."'");
		$row = $this->db->get()->result_array();
		$transaction_data = $row[0];
		if($transaction_data['transaction_withdrawal'] > 0){
			$data_type = 'transaction_withdrawal';
			$return_amount = $transaction_data['transaction_withdrawal']*(-1);
		}else{
			$data_type = 'transaction_deposit';
			$return_amount = $transaction_data['transaction_deposit']*(-1);
		}
		
		$this->db->select(array(
			'transaction_balance',
			'transaction_no_in_balance'
		));
		$this->db->from('coop_account_transaction');
		$this->db->where("account_id = '".$transaction_data['account_id']."'");
		$this->db->order_by('transaction_time DESC, transaction_id DESC');
		$this->db->limit(1);
		$row = $this->db->get()->result_array();
		$last_transaction = @$row[0];
		
		$data_insert = array();
		$data_insert['account_id'] = $transaction_data['account_id'];
		if($data_type == 'transaction_withdrawal'){
			$data_insert['transaction_withdrawal'] = $return_amount;
			$data_insert['transaction_deposit'] = '0';
			$data_insert['transaction_balance'] = $last_transaction['transaction_balance']+$transaction_data['transaction_withdrawal'];
			$data_insert['transaction_no_in_balance'] = $last_transaction['transaction_no_in_balance']+$transaction_data['transaction_withdrawal'];
		}else{
			$data_insert['transaction_deposit'] = $return_amount;
			$data_insert['transaction_withdrawal'] = '0';
			$data_insert['transaction_balance'] = $last_transaction['transaction_balance']-$transaction_data['transaction_deposit'];
			$data_insert['transaction_no_in_balance'] = $last_transaction['transaction_no_in_balance']-$transaction_data['transaction_deposit'];
		}
		$data_insert['transaction_time'] = date('Y-m-d H:i:s');
		$data_insert['transaction_list'] = 'ERR';
		$data_insert['user_id'] = $_SESSION['USER_ID'];
		$data_insert['cancel_ref_transaction_id'] = $transaction_id;

		//ดึงข้อมูลลำดับรายการ ของรายการถัดไป
		$arr_seq = array(); 
		$arr_seq['account_id'] = $data_insert['account_id']; 
		$arr_seq['transaction_list'] = $data_insert['transaction_list'];
		$seq_no = $this->deposit_seq->gen_seq_account_transaction($arr_seq);
		$data_insert['seq_no'] = @$seq_no;

		$this->db->insert('coop_account_transaction',$data_insert);
		
		$data_insert = array();
		$data_insert['cancel_status'] = '1';
		$this->db->where('transaction_id',$transaction_id);
		$this->db->update('coop_account_transaction',$data_insert);
		
		$this->center_function->toast('บันทึกข้อมูลเรียบร้อยแล้ว');
		echo "<script> document.location.href = '".PROJECTPATH."/save_money/account_detail?account_id=".$transaction_data['account_id']."' </script>";
	}

	function book_bank_page_fix_line_pdf(){
		$arr_data = array();
		$this->load->view('save_money/book_bank_page_fix_line_pdf',$arr_data);
	}
	
	function close_account_calculate(){
		if(isset($_POST['fixed_date']) && $_POST['fixed_date'] != "") {
			$_pattern = '/^[0-9]{2}-[0-9]{2}-[0-9]{4}$/';
			$_dateFixed = str_replace("/","-",$_POST['fixed_date']);
			if(preg_match($_pattern, $_dateFixed)){
				$_arr = array_reverse(explode("-", $_dateFixed));
				if($_arr[0]+543 > date('Y')+543){
					$_arr[0] = $_arr[0] - 543;
				}
				$_dateFixed = join("-", $_arr);
			}
			$date_interest = date('Y-m-d', strtotime($_dateFixed));
		}else{
			$date_interest = date('Y-m-d');
		}
		$data = array();
		$account_id = $_POST['account_id'];
		$this->deposit_libraries->close = true; //Close this setting due to it duplicate with coop_deposit_type_setting_detail.is_day_cal_interest setting.
		$cal_data = $this->deposit_libraries->cal_deposit_interest_by_acc_date($account_id, $date_interest);
		$data['interest'] = number_format($cal_data["interest"],2);
		$data['interest_return'] = number_format($cal_data["interest_return"],2);
		$data['tax_return'] = number_format($cal_data["tax_return"],2);

		$this->db->select(array('transaction_balance'));
		$this->db->from('coop_account_transaction');
		$this->db->where("account_id = '".$account_id."'");
		$this->db->order_by("transaction_time DESC, transaction_id DESC");
		$this->db->limit(1);
		$row_transaction = $this->db->get()->row_array();
		
		$principal = $row_transaction['transaction_balance'];
		
		$data['principal'] = number_format($principal,2);
		$data['text_alert'] = "";
		$data['account_name'] = $this->deposit_libraries->get_account_name($account_id);;
		$data['prefix_account_id'] = $this->center_function->format_account_number($account_id);
		echo json_encode($data);
	}
	
	function test_close_account_calculate(){
		// $date_interest = date('Y-m-d');
		$date_interest = '2020-04-01';
		$account_id = "0002000339";
		$this->deposit_libraries->user_id = 'SYSTEM';
		$this->deposit_libraries->debug = true;
		$this->deposit_libraries->testmode = true;
		$data = $this->deposit_libraries->cal_deposit_interest_by_acc_date($account_id, $date_interest);
		echo"<pre>*** RETURN ***</pre>";
		echo"<pre>";print_r($data);echo"</pre>";
		echo"<pre>*** END RETURN ***</pre>";
	}

	public function update_transaction_balance(){
		$data = $this->input->post();
		$this->update_st->update_balance_statement($data);
		echo "success";
	}

	public function print_statement(){
		$arr_data = array();
		$real_account = $this->uri->segment(3);
		$account_id = $real_account;
		$arr_data['account_name'] = $this->db->get_where("coop_maco_account", array(
			"account_id" => $real_account
		))->result()[0]->account_name;

		$arr_data['account_id'] = $account_id;
		$this->libraries->template('save_money/print_statement',$arr_data);
	}

	public function statement_preview(){
		$this->load->helper('cookie');
		$arr_data = array();
		$data = $this->input->post();
		if(count($data)==0){
			$data['start_date'] = get_cookie('start_date');
			$data['end_date'] = get_cookie('end_date');
			$data['account_id'] = get_cookie('account_id');
			$data['select_type'] = get_cookie('select_type');
		}else{
			set_cookie("start_date", $data['start_date'], 600);
			set_cookie("end_date", $data['end_date'], 600);
			set_cookie("account_id", $data['account_id'], 600);
			set_cookie("select_type", $data['select_type'], 600);
		}


		$real_account = implode( "", explode("-", $data['account_id']) );
		$tmp_start_date = explode("/", $data['start_date']);
		$tmp_end_date = explode("/", $data['end_date']);
		$arr_data['account_name'] = $this->db->get_where("coop_maco_account", array(
			"account_id" => $real_account
		))->result()[0]->account_name;

		$arr_data['account_id'] = $data['account_id'];
		$arr_data['account_name'] = $this->db->get_where("coop_maco_account", array(
			"account_id" => $real_account
		))->result()[0]->account_name;
		
		
		$arr_data['st_by_name'] = $this->db->get_where("coop_user", array(
			"user_id" => $_SESSION['USER_ID']
		))->result()[0]->user_name;
		if($data['select_type']=="all"){
			$this->db->order_by("transaction_time, transaction_id");
			$arr_data['st'] = $this->db->get_where("coop_account_transaction", array(
				"transaction_time <= " => ($tmp_end_date[2]-543)."-".$tmp_end_date[1]."-".$tmp_end_date[0]." 23:59:59",
				"account_id" => $real_account
			))->result();
			
			$transaction_time = explode("-", explode(" ", $arr_data['st'][0]->transaction_time)[0]);
			$tmp_start_date[0] = $transaction_time[2];
			$tmp_start_date[1] = $transaction_time[1];
			$tmp_start_date[2] = $transaction_time[0]+543;
			
			$this->db->order_by("transaction_time DESC, transaction_id DESC");
			$this->db->limit(1);
			$arr_data['balance'] = $this->db->get_where("coop_account_transaction", array(
				"transaction_time <= " => ($tmp_end_date[2]-543)."-".$tmp_end_date[1]."-".$tmp_end_date[0]." 23:59:59",
				"account_id" => $real_account
			))->result()[0]->transaction_balance;
		}else{
			$this->db->order_by("transaction_time, transaction_id");
			$arr_data['st'] = $this->db->get_where("coop_account_transaction", array(
				"transaction_time >= " => ($tmp_start_date[2]-543)."-".$tmp_start_date[1]."-".$tmp_start_date[0]." 00:00:00",
				"transaction_time <= " => ($tmp_end_date[2]-543)."-".$tmp_end_date[1]."-".$tmp_end_date[0]." 23:59:59",
				"account_id" => $real_account
			))->result();
	
			$this->db->order_by("transaction_time DESC, transaction_id DESC");
			$this->db->limit(1);
			$arr_data['balance'] = $this->db->get_where("coop_account_transaction", array(
				"transaction_time >= " => ($tmp_start_date[2]-543)."-".$tmp_start_date[1]."-".$tmp_start_date[0]." 00:00:00",
				"transaction_time <= " => ($tmp_end_date[2]-543)."-".$tmp_end_date[1]."-".$tmp_end_date[0]." 23:59:59",
				"account_id" => $real_account
			))->result()[0]->transaction_balance;
		}


		//echo $this->db->last_query(); exit;

		
		$this->db->join("coop_deposit_type_setting", "type_id");
		$arr_data['account_type'] = $this->db->get_where("coop_maco_account", array(
			"account_id" => $real_account
		))->result()[0]->type_name;
		
		$arr_data['start_date'] = $tmp_start_date[0]." ".$this->center_function->month_arr()[(int)$tmp_start_date[1]]." ".$tmp_start_date[2];
		$arr_data['end_date'] = $tmp_end_date[0]." ".$this->center_function->month_arr()[(int)$tmp_end_date[1]]." ".$tmp_end_date[2];
		if(@$_GET['download']!=''){
			$this->load->view("save_money/statement_preview", $arr_data);
		}else{
			$this->preview_libraries->template_preview("save_money/statement_preview" ,$arr_data);
		}
		
		
	}
	
	public function get_account_list_transfer(){
		$member_id = $this->input->post("member_id");
		echo $member_id;
		$temp_coop = $this->db->get_where("coop_maco_account", array("mem_id" => $member_id))->result();
	}

	public function remove_transaction(){
		$transcation_id = $this->uri->segment(3);
		$account_id = $this->uri->segment(4);
		if($transcation_id == ""){
			exit;
		}

		if($_SESSION['USER_ID']!=1){
			// var_dump($_SESSION);
			exit;
		}

		$transaction = $this->db->get_where("coop_account_transaction", array("transaction_id" => $transcation_id) )->result_array()[0];
		$this->db->where("transaction_id", $transcation_id);
		$this->db->delete("coop_account_transaction");
		// var_dump($transaction);
		$this->update_st->update_deposit_transaction($transaction['account_id'], $transaction['transaction_time']);
		header("Location: ".base_url().'/save_money/account_detail?account_id='.$account_id );
	}

	function U2T($text) { return @iconv("UTF-8", "TIS-620//IGNORE", trim($text)); }

	private function GETVAR($key, $default = null, $prefix = null, $suffix = null) {
        return isset($_GET[$key]) ? $prefix . $_GET[$key] . $suffix : $prefix . $default . $suffix;
    }

	public function print_slip_deposit(){
		$transaction_id = $this->uri->segment(3);

		$this->db->select(array("t1.*", "CONCAT(t4.prename_short,t2.firstname_th, ' ', lastname_th) as fullname", "t5.user_name"));
		$this->db->join("coop_maco_account as t3", "t3.account_id = t1.account_id", "inner");
		$this->db->join("coop_mem_apply as t2", "t2.member_id = t3.mem_id", "left");
		$this->db->join("coop_prename as t4", "t4.prename_id = t2.prename_id", "left");
		$this->db->join("coop_user as t5", "t5.user_id = t1.user_id", "left");
		$transaction = $this->db->get_where("coop_account_transaction as t1", array(
			"transaction_id" => $transaction_id
		))->result_array()[0];
		
		$transaction['transaction_time'] = $this->center_function->mydate2date($transaction['transaction_time'], true);
		$transaction['method'] = ($transaction['transaction_withdrawal']!=0) ? $transaction['transaction_withdrawal'] : $transaction['transaction_deposit'];
		// var_dump($transaction);
		
		$account_id = @$this->center_function->format_account_number($transaction['account_id']);
		$font = $this->GETVAR('font','fontawesome-webfont1','','.php');
		$pdf = new FPDF('L','mm','A5');	
		$pdf->AddPage();
		$pdf->AddFont('THSarabunNew','','THSarabunNew.php');
		$pdf->AddFont('THSarabunNewB','','THSarabunNew-Bold.php');
			
		$pdf->SetFont('THSarabunNew','',14);
		$pdf->setY(2);
		$pdf->Cell( 0 , 14 , $transaction['transaction_time']."   ".
		$account_id."    ".
		$this->U2T($transaction['fullname'])."    ".
		$transaction['transaction_list']."    ".
		number_format($transaction['method'],2)."   ".
		number_format($transaction['transaction_balance'],2)."    ".
		$this->U2T(@$transaction['user_name']), 0,0,'R' );	
		$pdf->Output();
	}

	public function authen_confirm_err_transaction(){
		if(empty($_SESSION['USER_ID']))
			header('HTTP/1.1 500 Internal Server Error');

		$user = $this->input->post("confirm_user");
		$password = $this->input->post("confirm_pwd");
		
		$user_db = $this->db->get_where("coop_user", array(
			"username" => $user,
			"password" => $password
		))->result()[0];
		if($user_db){
			$permission = $this->db->get_where("coop_user_permission", array(
				"user_id" => $user_db->user_id,
				"menu_id" => 230,//ยกเลิกรายการ
			));
			echo json_encode(array("result" => "true", "permission" => ($permission->result() || $_SESSION['USER_ID']==1) ? "true" : "false" ));
		}else{
			echo json_encode(array("result" => "false"));
		}
	}

	public function authen_confirm_user(){
		if(empty($_SESSION['USER_ID']))
			header('HTTP/1.1 500 Internal Server Error');

		$user = $this->input->post("confirm_user");
		$password = $this->input->post("confirm_pwd");
		$menu_id = $this->input->post("permission_id");
		
		$user_db = $this->db->get_where("coop_user", array(
			"username" => $user,
			"password" => $password,
			"user_status" => 1
		))->result()[0];
		if($user_db){
			$permission = $this->db->get_where("coop_user_permission", array(
				"user_id" => $user_db->user_id,
				"menu_id" => $menu_id,//เมนูสิทธิ์
			))->result_array();
			echo json_encode(array("result" => "true", "permission" => ($permission || $_SESSION['USER_ID']==1 || $user_db->user_type_id==1) ? "true" : "false", "user_id" => $user_db->user_id, "sql" => $this->db->last_query() ) );
		}else{
			echo json_encode(array("result" => "false"));
		}
	}

	public function hold_withdraw(){
        if($this->center_function->withdraw_permission($_GET['account'])){
            echo "TRUE";
        }else{
            echo "FALSE";
        }
    }

    function test_interest_calculate(){
        //$date_interest = date('Y-m-d');
        $date_interest = '2020-06-26';
        $account_id = "0002000187";
        $this->deposit_libraries->user_id = 'SYSTEM';
        $this->deposit_libraries->debug = true;
        $this->deposit_libraries->testmode = true;
        $this->deposit_libraries->close = true;
        $data = $this->deposit_libraries->cal_deposit_interest_by_acc_date($account_id, $date_interest);
        echo"<pre>*** RETURN ***</pre>";
        echo"<pre>";print_r($data);echo"</pre>";
        echo"<pre>*** END RETURN ***</pre>";
    }

	//เช็คเงินฝากประจำสำหรับการถอนเงินตามยอดเงินฝาก
	public function account_withdrawal_chooses($account_id){
		$arr_data = array();

		$row = $this->db->select('account_id,ref_account_no,transaction_time')->from('coop_account_transaction')->where("account_id = '".$account_id."' AND ref_account_no IS NOT NULL")->group_by("ref_account_no")->order_by("ref_account_no DESC")->get()->result_array();
		//echo $this->db->last_query(); //exit;
		$row_type = $this->db->select('type_id')->from('coop_maco_account')->where("account_id = '".$account_id."'")->limit(1)->get()->row_array();
		$account_type_id = @$row_type['type_id'];

		$date_now = date("Y-m-d");
		$row_setting = $this->db->select('type_id,max_month,start_date')->from('coop_deposit_type_setting_detail')
			->where("type_id = '".$account_type_id."' AND start_date <= '".$date_now."'")->order_by("start_date DESC")->limit(1)->get()->row_array();

		//วันที่ครบกำหนด due ล่าสุด
		$arr_last_due = $this->db->select('ref_account_no,MAX(transaction_time) AS transaction_time')->from('coop_account_transaction')->where("account_id = '".$account_id."' AND transaction_list = 'DFX'")->group_by("ref_account_no")->get()->result_array();
		$arr_last_due_list = array_column($arr_last_due, 'transaction_time', 'ref_account_no');
		
		//วันที่ทำรายการล่าสุด
		/*$arr_last_transaction = $this->db->select('t1.ref_account_no,
													MAX(t1.transaction_time) AS transaction_time,
													MAX(t1.transaction_id) AS transaction_id,
													t2.balance_deposit,
													t2.balance_deposit_int')
									->from('coop_account_transaction AS t1')
									->join("coop_account_transaction AS t2","t1.transaction_id = t2.transaction_id  AND t1.transaction_time = t2.transaction_time","left")
									->where("t1.account_id = '".$account_id."'")
									->group_by("t1.ref_account_no")
									->get()->result_array();
		*/
        $arr_last_transaction = $this->db->select('t1.ref_account_no,
													t1.transaction_time,
													t1.transaction_id,
													t1.balance_deposit,
													t1.balance_deposit_int')
            ->from('coop_account_transaction AS t1')
            ->join("(SELECT account_id,ref_account_no,MAX(transaction_time) AS transaction_time,MAX(transaction_id) AS transaction_id FROM coop_account_transaction WHERE account_id = '".$account_id."' GROUP BY ref_account_no) AS t2","t1.transaction_id = t2.transaction_id  AND t1.transaction_time = t2.transaction_time","inner")
            ->where("t1.account_id = '".$account_id."'")
            ->order_by("t1.ref_account_no")
            ->get()->result_array();
		$arr_ref_account_no = array_column($arr_last_transaction, 'ref_account_no');
		
		$arr_account = array();
		foreach(@$row AS $key=>$value){
			//วันที่ครบกำหนด due ล่าสุด
			//$row_last_due = $this->db->select('transaction_time')->from('coop_account_transaction')->where("account_id = '".$account_id."' AND ref_account_no = '".$value['ref_account_no']."' AND transaction_list = 'DFX'")->order_by("transaction_time DESC")->limit(1)->get()->row_array();
			//echo $this->db->last_query(); echo '<br>';
			//วันที่ทำรายการล่าสุด
			//$row_last_transaction = $this->db->select('transaction_time,balance_deposit,balance_deposit_int,transaction_id')->from('coop_account_transaction')->where("account_id = '".$account_id."' AND ref_account_no = '".$value['ref_account_no']."'")->order_by("transaction_time DESC,transaction_id DESC")->limit(1)->get()->row_array();
			//echo $this->db->last_query(); echo '<br>'; exit;
			//echo 'due='.@$row_last_due['transaction_time'].'|start='.@$value['transaction_time'].'|last='.@$row_last_transaction['transaction_time'].'<br>';
			///$transaction_time_last = (@$row_last_due['transaction_time'] != '')?@$row_last_due['transaction_time']:@$row_last_transaction['transaction_time'];
			$transaction_id = $arr_last_transaction[array_search($value['ref_account_no'],$arr_ref_account_no)]['transaction_id'];
			$balance_deposit = $arr_last_transaction[array_search($value['ref_account_no'],$arr_ref_account_no)]['balance_deposit'];
			$balance_deposit_int = $arr_last_transaction[array_search($value['ref_account_no'],$arr_ref_account_no)]['balance_deposit_int'];
			$transaction_time_last = $arr_last_transaction[array_search($value['ref_account_no'],$arr_ref_account_no)]['transaction_time'];
			
			//$transaction_time_last = (@$arr_last_due_list[$value['ref_account_no']] != '')?@$arr_last_due_list[$value['ref_account_no']]:@$row_last_transaction['transaction_time'];
			$transaction_time_last = (@$arr_last_due_list[$value['ref_account_no']] != '')?@$arr_last_due_list[$value['ref_account_no']]:@$transaction_time_last;
			
			//$arr_account[$key]['transaction_id'] = $row_last_transaction['transaction_id'];
			$arr_account[$key]['transaction_id'] = $transaction_id;
			$arr_account[$key]['transaction_time'] = $transaction_time_last;
			//$arr_account[$key]['balance_deposit'] = @$row_last_transaction['balance_deposit']+@$row_last_transaction['balance_deposit_int'];
			$arr_account[$key]['balance_deposit'] = @$balance_deposit+@$balance_deposit_int;
			$arr_account[$key]['account_no'] = $value['ref_account_no'];
			$arr_account[$key]['date_due'] = date("Y-m-d", strtotime(@$row_setting['max_month']." months", strtotime((@$transaction_time_last))));
			$diff = @date_diff(date_create(date("Y-m-d", strtotime((@$transaction_time_last)))),date_create($date_now));
			$date_count = @$diff->format("%a");
			$arr_account[$key]['long_time'] = @$date_count;

			$date_count_due = @$row_setting['max_month']*30;
			$chk_date_count_due = (@$date_count >= $date_count_due)?'1':'0';
			$arr_account[$key]['chk_date_count_due'] = @$chk_date_count_due;

		}
		//echo 'max_month='.@$row_setting['max_month'];
		//exit;
		$arr_data['data_chooses'] = @$arr_account;
		return $arr_data;
	}

	//บันทึกเงินฝากประจำสำหรับการถอนเงินตามยอดเงินฝาก
	public function save_transaction_chooses(){
		$time_fixed = (@$_POST['time_fixed'] != '')?$_POST['time_fixed']:date('H:i:s');
		if (isset($_POST['date_fixed_transaction']) && !empty($_POST['date_fixed_transaction'])) {
			$arr = array_reverse(explode('/', $_POST['date_fixed_transaction']));
			$arr[0] = $arr[0]-543;
			$transaction_time = join('-', $arr)." ".$time_fixed;
		} else {
			$transaction_time = date('Y-m-d')." ".$time_fixed;
		}
		$account_id = @$_POST['account_id'];
		// var_dump($_POST);exit;
		$chk_account = $this->db->select('transaction_balance')->from('coop_account_transaction')->where("account_id = '".$account_id."'")->order_by("transaction_time DESC ,transaction_id DESC")->limit(1)->get()->row_array();
		$transaction_balance = $chk_account['transaction_balance'];
		//echo '<pre>'; print_r($_POST); echo '</pre>'; //exit;
		if(@$account_id != ''){
			foreach($_POST['money_withdrawal'] AS $key=>$value){
				if($value != ''){
					$transaction_id = @$_POST['transaction_id'][$key];
					$money_withdrawal = @str_replace(',','',@$_POST['money_withdrawal'][$key]);
					$amount_balance = @str_replace(',','',@$_POST['amount_balance'][$key]);
					$amount_int = @str_replace(',','',@$_POST['amount_int'][$key]);
					$amount_tax = @str_replace(',','',@$_POST['amount_tax'][$key]);
					$balance_int = @$amount_int-@$amount_tax;
					$money_withdrawal_int = @str_replace(',','',@$_POST['money_withdrawal_int'][$key]);
					$ref_account_no = @$_POST['ref_account_no'][$key];
					$check_withdrawal_int = @$_POST['check_withdrawal_int'][$key];
					$balance_deposit = @str_replace(',','',@$_POST['balance_deposit'][$key]);
					$balance_deposit_int = 0;
					//ดอกเบี้ย
					if($amount_int > 0){
						$transaction_balance = $transaction_balance+@$amount_int;
						$balance_deposit_int = @$amount_int;
						$data_insert_transaction = array();
						$data_insert_transaction['transaction_time'] 			= $transaction_time;
						$data_insert_transaction['transaction_list'] 			= "INT";
						$data_insert_transaction['transaction_withdrawal'] 		= 0;
						$data_insert_transaction['transaction_deposit'] 		= @$amount_int;
						$data_insert_transaction['transaction_balance'] 		= $transaction_balance;
						$data_insert_transaction['user_id'] 					= $_SESSION['USER_ID'];
						$data_insert_transaction['transaction_no_in_balance'] 	= $transaction_balance;
						$data_insert_transaction['account_id'] 					= @$account_id;
						$data_insert_transaction['ref_account_no'] 				= @$ref_account_no;
						$data_insert_transaction['balance_deposit'] 			= @$balance_deposit;
						$data_insert_transaction['balance_deposit_int'] 		= @$balance_deposit_int;

						//ดึงข้อมูลลำดับรายการ ของรายการถัดไป
						$arr_seq = array(); 
						$arr_seq['account_id'] = $account_id; 
						$arr_seq['transaction_list'] = $data_insert_transaction['transaction_list']; 
						$arr_seq['balance_deposit'] = $data_insert_transaction['balance_deposit']; 
						$seq_no = $this->deposit_seq->gen_seq_account_transaction($arr_seq);
						$data_insert_transaction['seq_no'] = @$seq_no;

						$this->db->insert("coop_account_transaction", $data_insert_transaction);
						//echo '==============INT=============<br>';
						//echo '<pre>'; print_r($data_insert_transaction); echo '</pre>';
					}

					//ภาษี
					if($amount_tax > 0){
						$transaction_balance = $transaction_balance-@$amount_tax;
						$balance_deposit_int = @$amount_int-@$amount_tax;
						$data_insert_transaction = array();
						$data_insert_transaction['transaction_time'] 			= $transaction_time;
						$data_insert_transaction['transaction_list'] 			= "WTX";
						$data_insert_transaction['transaction_withdrawal'] 		= @$amount_tax;
						$data_insert_transaction['transaction_deposit'] 		= 0;
						$data_insert_transaction['transaction_balance'] 		= $transaction_balance;
						$data_insert_transaction['user_id'] 					= $_SESSION['USER_ID'];
						$data_insert_transaction['transaction_no_in_balance'] 	= $transaction_balance;
						$data_insert_transaction['account_id'] 					= @$account_id;
						$data_insert_transaction['ref_account_no'] 				= @$ref_account_no;
						$data_insert_transaction['balance_deposit'] 			= @$balance_deposit;
						$data_insert_transaction['balance_deposit_int'] 		= @$balance_deposit_int;

						//ดึงข้อมูลลำดับรายการ ของรายการถัดไป
						$arr_seq = array(); 
						$arr_seq['account_id'] = $account_id; 
						$arr_seq['transaction_list'] = $data_insert_transaction['transaction_list']; 
						$arr_seq['balance_deposit'] = $data_insert_transaction['balance_deposit']; 
						$seq_no = $this->deposit_seq->gen_seq_account_transaction($arr_seq);
						$data_insert_transaction['seq_no'] = @$seq_no;

						$this->db->insert("coop_account_transaction", $data_insert_transaction);
						//echo '==============TAX=============<br>';
						//echo '<pre>'; print_r($data_insert_transaction); echo '</pre>';
					}

					//ถอนดอกเบี้ยเงินฝาก ที่หักจากภาษี
					if($balance_int > 0 && $amount_tax > 0){
						$transaction_balance = $transaction_balance-@$balance_int;
						$balance_deposit_int = $balance_deposit_int-$balance_int;
						$data_insert_transaction = array();
						$data_insert_transaction['transaction_time'] 			= $transaction_time;
						$data_insert_transaction['transaction_list'] 			= "WCI";
						$data_insert_transaction['transaction_withdrawal'] 		= @$balance_int;
						$data_insert_transaction['transaction_deposit'] 		= 0;
						$data_insert_transaction['transaction_balance'] 		= $transaction_balance;
						$data_insert_transaction['user_id'] 					= $_SESSION['USER_ID'];
						$data_insert_transaction['transaction_no_in_balance'] 	= $transaction_balance;
						$data_insert_transaction['account_id'] 					= @$account_id;
						$data_insert_transaction['ref_account_no'] 				= @$ref_account_no;
						$data_insert_transaction['balance_deposit'] 			= @$balance_deposit;
						$data_insert_transaction['balance_deposit_int'] 		= @$balance_deposit_int;

						//ดึงข้อมูลลำดับรายการ ของรายการถัดไป
						$arr_seq = array(); 
						$arr_seq['account_id'] = $account_id; 
						$arr_seq['transaction_list'] = $data_insert_transaction['transaction_list']; 
						$arr_seq['balance_deposit'] = $data_insert_transaction['balance_deposit']; 
						$seq_no = $this->deposit_seq->gen_seq_account_transaction($arr_seq);
						$data_insert_transaction['seq_no'] = @$seq_no;

						$this->db->insert("coop_account_transaction", $data_insert_transaction);
						//echo '==============WCI=============<br>';
						//echo '<pre>'; print_r($data_insert_transaction); echo '</pre>';
					}

					//ถอนเงิน
					if($money_withdrawal > 0){
						$balance_deposit = @$balance_deposit - @$money_withdrawal;
						$money_withdrawal = @$money_withdrawal + @$balance_deposit_int;
						$transaction_balance = $transaction_balance - @$money_withdrawal;
						$balance_deposit_int = 0;
						$data_insert_transaction = array();
						$data_insert_transaction['transaction_time'] 			= $transaction_time;
						$data_insert_transaction['transaction_list'] 			= ($_POST['pay_type']=="1" ? "WCA" : ($_POST['pay_type']==22 ? "WCT" : ($_POST['pay_type']=="3" ? "WCQ" : "WCA")));
						$data_insert_transaction['transaction_withdrawal'] 		= @$money_withdrawal;
						$data_insert_transaction['transaction_deposit'] 		= 0;
						$data_insert_transaction['transaction_balance'] 		= $transaction_balance;
						$data_insert_transaction['user_id'] 					= $_SESSION['USER_ID'];
						$data_insert_transaction['transaction_no_in_balance'] 	= $transaction_balance;
						$data_insert_transaction['account_id'] 					= @$account_id;
						$data_insert_transaction['ref_account_no'] 				= @$ref_account_no;
						$data_insert_transaction['balance_deposit'] 			= @$balance_deposit;
						$data_insert_transaction['balance_deposit_int'] 		= @$balance_deposit_int;
						$data_insert_transaction['cheque_no'] 					= @$_POST['cheque_number'];

						if(isset($_POST['cheque_number'])) {
							$data_insert_transaction['cheque_no'] = $_POST['cheque_number'];
						}
						if(isset($_POST['bank_id'])) {
							$data_insert_transaction['bank_id'] = $_POST['bank_id'];
						}
						if(isset($_POST['branch_code'])) {
							$data_insert_transaction['branch_code'] = $_POST['branch_code'];
						}
						if(isset($_POST['transfer_bank_account_name'])) {
							$data_insert_transaction['local_account_id'] = $_POST['transfer_bank_account_name'];
						}
						if(isset($_POST['other'])) {
							$data_insert_transaction['other'] = $_POST['other'];
						}
						if(isset($_POST['other'])) {
							$data_insert_transaction['transfer_other'] = $_POST['transfer_other'];
						}
						
						//ดึงข้อมูลลำดับรายการ ของรายการถัดไป
						$arr_seq = array(); 
						$arr_seq['account_id'] = $account_id; 
						$arr_seq['transaction_list'] = $data_insert_transaction['transaction_list']; 
						$arr_seq['balance_deposit'] = $data_insert_transaction['balance_deposit']; 
						$seq_no = $this->deposit_seq->gen_seq_account_transaction($arr_seq);
						$data_insert_transaction['seq_no'] = @$seq_no;
						
						if($data_insert_transaction['balance_deposit'] > 0 && $data_insert_transaction['transaction_list'] == 'WCA'){
							$data_insert_transaction['seq_chk'] = 1;
						}

						$this->db->insert("coop_account_transaction", $data_insert_transaction);
					}

					if($check_withdrawal_int != ''){
						//ฝากเงินเมื่อถอนดอก กรณีครบกำหนดฝากประจำ
						if($money_withdrawal > 0){
							$money_withdrawal = @$money_withdrawal;
							$transaction_balance = $transaction_balance + @$money_withdrawal;
							$balance_deposit = @$money_withdrawal;
							$balance_deposit_int = 0;
							$data_insert_transaction = array();
							$data_insert_transaction['transaction_time'] 			= $transaction_time;
							$data_insert_transaction['transaction_list'] 			= "DFX";
							$data_insert_transaction['transaction_withdrawal'] 		= 0;
							$data_insert_transaction['transaction_deposit'] 		= @$money_withdrawal;
							$data_insert_transaction['transaction_balance'] 		= $transaction_balance;
							$data_insert_transaction['user_id'] 					= $_SESSION['USER_ID'];
							$data_insert_transaction['transaction_no_in_balance'] 	= $transaction_balance;
							$data_insert_transaction['account_id'] 					= @$account_id;
							$data_insert_transaction['ref_account_no'] 				= @$ref_account_no;
							$data_insert_transaction['balance_deposit'] 			= @$balance_deposit;
							$data_insert_transaction['balance_deposit_int'] 		= @$balance_deposit_int;
							if(isset($_POST['cheque_number'])) {
								$data_insert_transaction['cheque_no'] = $_POST['cheque_number'];
							}
							if(isset($_POST['bank_id'])) {
								$data_insert_transaction['bank_id'] = $_POST['bank_id'];
							}
							if(isset($_POST['branch_code'])) {
								$data_insert_transaction['branch_code'] = $_POST['branch_code'];
							}
							if(isset($_POST['transfer_bank_account_name'])) {
								$data_insert_transaction['local_account_id'] = $_POST['transfer_bank_account_name'];
							}
							if(isset($_POST['other'])) {
								$data_insert_transaction['other'] = $_POST['other'];
							}
							if(isset($_POST['other'])) {
								$data_insert_transaction['transfer_other'] = $_POST['transfer_other'];
							}

							//ดึงข้อมูลลำดับรายการ ของรายการถัดไป
							$arr_seq = array(); 
							$arr_seq['account_id'] = $account_id; 
							$arr_seq['transaction_list'] = $data_insert_transaction['transaction_list']; 
							$arr_seq['balance_deposit'] = $data_insert_transaction['balance_deposit']; 
							$seq_no = $this->deposit_seq->gen_seq_account_transaction($arr_seq);
							$data_insert_transaction['seq_no'] = @$seq_no;
							
							$this->db->insert("coop_account_transaction", $data_insert_transaction);
							//echo '==============DFX=============<br>';
							//echo '<pre>'; print_r($data_insert_transaction); echo '</pre>';
						}

						//ถอนดอกเบี้ย กรณีครบกำหนดฝากประจำ
						if($money_withdrawal_int > 0){
							$money_withdrawal = @$money_withdrawal_int;
							$balance_deposit = @$balance_deposit - @$money_withdrawal_int;
							$transaction_balance = $transaction_balance - @$money_withdrawal;
							$balance_deposit_int = 0;
							$data_insert_transaction = array();
							$data_insert_transaction['transaction_time'] 			= $transaction_time;
							$data_insert_transaction['transaction_list'] 			= "WCA";
							$data_insert_transaction['transaction_withdrawal'] 		= @$money_withdrawal;
							$data_insert_transaction['transaction_deposit'] 		= 0;
							$data_insert_transaction['transaction_balance'] 		= $transaction_balance;
							$data_insert_transaction['user_id'] 					= $_SESSION['USER_ID'];
							$data_insert_transaction['transaction_no_in_balance'] 	= $transaction_balance;
							$data_insert_transaction['account_id'] 					= @$account_id;
							$data_insert_transaction['ref_account_no'] 				= @$ref_account_no;
							$data_insert_transaction['balance_deposit'] 			= @$balance_deposit;
							$data_insert_transaction['balance_deposit_int'] 		= @$balance_deposit_int;
							if(isset($_POST['cheque_number'])) {
								$data_insert_transaction['cheque_no'] = $_POST['cheque_number'];
							}
							if(isset($_POST['bank_id'])) {
								$data_insert_transaction['bank_id'] = $_POST['bank_id'];
							}
							if(isset($_POST['branch_code'])) {
								$data_insert_transaction['branch_code'] = $_POST['branch_code'];
							}
							if(isset($_POST['transfer_bank_account_name'])) {
								$data_insert_transaction['local_account_id'] = $_POST['transfer_bank_account_name'];
							}
							if(isset($_POST['other'])) {
								$data_insert_transaction['other'] = $_POST['other'];
							}
							if(isset($_POST['other'])) {
								$data_insert_transaction['transfer_other'] = $_POST['transfer_other'];
							}

							//ดึงข้อมูลลำดับรายการ ของรายการถัดไป
							$arr_seq = array(); 
							$arr_seq['account_id'] = $account_id; 
							$arr_seq['transaction_list'] = $data_insert_transaction['transaction_list']; 
							$arr_seq['balance_deposit'] = $data_insert_transaction['balance_deposit']; 
							$seq_no = $this->deposit_seq->gen_seq_account_transaction($arr_seq);
							$data_insert_transaction['seq_no'] = @$seq_no;
							
							if($data_insert_transaction['balance_deposit'] > 0 && $data_insert_transaction['transaction_list'] == 'WCA'){
								$data_insert_transaction['seq_chk'] = 1;
							}

							$this->db->insert("coop_account_transaction", $data_insert_transaction);
							//echo '==============WCA=============<br>';
							//echo '<pre>'; print_r($data_insert_transaction); echo '</pre>';
						}
					}
				}
			}
		}
		header("Location: ".base_url().'/save_money/account_detail?account_id='.$account_id );
		exit;
	}

	//เช็คดอกเบี้ย  การถอนเงินแบบระบุจำนวนเงินตามยอดเงินฝาก
	function check_deposit_interest(){
		$arr_data = array();
		$transaction_id = @$_POST['transaction_id'];
		$account_id = @$_POST['account_id'];
		$money_withdrawal = @$_POST['money_withdrawal'];
		$date_interest = isset($_POST['date']) ? date('Y-m-d', strtotime($_POST['date'])) : date("Y-m-d");
		$this->deposit_libraries->close = true;
		$cal_data = $this->deposit_chooses_model->cal_deposit_interest_by_acc_date($transaction_id, $account_id, $money_withdrawal, $date_interest);
		//echo '<pre>'; print_r($cal_data); echo '</pre>';
		$arr_data = $cal_data;
		echo json_encode($arr_data);
		exit;
	}

	function test_check_deposit_interest(){
		$arr_data = array();
		$transaction_id = "198964";
		$account_id = "0011000016";
		$money_withdrawal = 778125;
		$date_interest = isset($_POST['date']) ? date('Y-m-d', strtotime($_POST['date'])) : date("Y-m-d");
		$this->deposit_chooses_model->debug = true;
		$cal_data = $this->deposit_chooses_model->cal_deposit_interest_by_acc_date($transaction_id, $account_id, $money_withdrawal, $date_interest);
		//echo '<pre>'; print_r($cal_data); echo '</pre>';
		$arr_data = $cal_data;
		echo json_encode($arr_data);
		exit;
	}

	function get_account_saving(){
		$member_id = @$_POST['member_id'];
		if($member_id != ""){
			$this->db->select("account_id, account_name");
			$data = $this->db->get_where("coop_maco_account", array(
				"mem_id" => $member_id,
				"account_status" => "0"
			))->result_array();
			
			echo json_encode($data);
		}

	}

	public function udpate_interest(){
		$data = $this->input->post();
		header("content-type: application/json; charset: utf-8;");
		if(!empty($data['account_id']) && !empty($data['date_interesting'])) {
			$this->deposit_libraries->user_id = $_SESSION['USER_ID'];
			$this->db->select(array('account_id', 'type_id', 'mem_id', 'created as create_account_date'));
			$account = $this->db->get_where('coop_maco_account', "account_id = '{$data['account_id']}'", 1)->row_array();
			$this->deposit_libraries->cal_deposit_interest($account, 'cal_interest', $data['date_interesting']." ".$data['time_interesting']." :00", '');

			if($this->db->affected_rows()){
				echo json_encode(array('status' => 200, "data" => array(
					'msg' => 'สำเร็จ',
				)));
				exit;
			}else{
				echo json_encode(array('status' => 200, "data" => array(
					'msg' => 'ไม่มีดอกเบี้ย',
				)));
				exit;
			}
		}
		echo json_encode(array('status' => 400, "data" => array(
			'msg' => 'ไม่สำเร็จ',
		)));
		exit;
	}
	
	function cal_interest() {
		$arr_data["data"] = [
			"start_date" => date("d/m/").(date("Y") + 543),
			"end_date" => date("d/m/").(date("Y") + 543),
			"time" => "05:00"
		];
		
		$this->db->select(array('t1.type_id','t1.type_name','t1.type_code'));
		$this->db->from('coop_deposit_type_setting as t1');
		$row = $this->db->get()->result_array();
		$arr_data['type_id'] = $row;
		
		$this->libraries->template('save_money/cal_interest', $arr_data);
	}
	
	function cal_interest_process() {
		$type_id = $_POST["type_id"];
		$time = $_POST["time"];
		$acc_id = $_POST["acc_id"];
		
		$start_date = $_POST["start_date"];
		$start_date_arr = explode('/', $start_date);
		$start_day = $start_date_arr[0];
		$start_month = $start_date_arr[1];
		$start_year = $start_date_arr[2];
		$start_year -= 543;
		$start_date = $start_year.'-'.$start_month.'-'.$start_day.' '.$time;
		
		$end_date = $_POST["end_date"];
		$end_date_arr = explode('/', $end_date);
		$end_day = $end_date_arr[0];
		$end_month = $end_date_arr[1];
		$end_year = $end_date_arr[2];
		$end_year -= 543;
		$end_date = $end_year.'-'.$end_month.'-'.$end_day.' '.$time;
		
		$where = "";
		if(!empty($type_id)) {
			$where .= " AND type_id = '{$type_id}'";
		}
		if(!empty($acc_id)) {
			$where .= " AND account_id = '{$acc_id}'";
		}
		$this->db->select(array(
			'account_id',
			'type_id',
			'mem_id',
			'created as create_account_date'
		));
		$this->db->from('coop_maco_account');
		$this->db->where("account_status = '0'".$where);
		$rs_member = $this->db->get()->result_array();
		
		$rs_date = $this->db->query("SELECT DATEDIFF('{$end_date}', '{$start_date}') AS date_count");
		$row_date = $rs_date->row_array();
		for($i = 0; $i <= $row_date["date_count"]; $i++) {
			$rs_date2 = $this->db->query("SELECT DATE_ADD('".$start_date."', INTERVAL ".$i." DAY) AS date_cal");
			$row_date2 = $rs_date2->row_array();
			
			foreach($rs_member as $key_member => $row_member){
				$this->deposit_libraries->user_id = 'SYSTEM';
				//$this->debug = true;
				//$this->testmode = true;
				$this->deposit_libraries->cal_deposit_interest($row_member, 'cal_interest', $row_date2["date_cal"], date("d", strtotime($row_date2["date_cal"])), '');
			}
		}
		
		echo json_encode(["result" => "true"]);
	}
	
	function book_bank_cover_pdf_customize(){
		$arr_data = array();
		$account_id = $this->input->get('account_id');
		$arr_data['account_id'] = $account_id;
		
		
		$this->db->select(array('mem_group_id'));
		$this->db->from('coop_mem_apply');
		$this->db->where("member_id = '".$row[0]['mem_id']."'");
		$row_group = $this->db->get()->result_array();
		$arr_data['row_group'] = $row_group[0];
		
		$this->db->select(array('mem_group_name'));
		$this->db->from('coop_mem_group');
		$this->db->where("mem_group_id = '".$row_group[0]['mem_group_id']."'");
		$row_gname = $this->db->get()->result_array();
		if(!empty($row_gname)){
			$arr_data['row_gname'] = $row_gname[0];
		}else{
			$arr_data['row_gname']['mem_group_name'] = '';
		}

		$style = $this->db->get_where("coop_book_bank_style", array(
			"style_id" => 1
		))->result_array()[0];
		$arr_data['style'] = $style;

		$rows = $this->db->get_where("coop_book_bank_style_setting", array(
			"style_id" => 1
		))->result_array();
		foreach ($rows as $key => $value) {
			// var_dump($value);
			// echo "<br>";
			$meta = $value['style_value'];
			$text = $meta;
			if($meta == "[account_name]"){
				$this->db->select(array('account_name'));
				$this->db->from('coop_maco_account');
				$this->db->where("account_id = '".$account_id."'");
				$row = $this->db->get()->result_array();
				$text = $row[0]['account_name'];
			}

			if($meta == "[book_number]"){
				$this->db->select(array('book_number'));
				$this->db->from('coop_maco_account');
				$this->db->where("account_id = '".$account_id."'");
				$row = $this->db->get()->result_array();
				$text = $row[0]['book_number'];
			}

			if($meta == "[member_id]"){
				$this->db->select(array('mem_id'));
				$this->db->from('coop_maco_account');
				$this->db->where("account_id = '".$account_id."'");
				$row = $this->db->get()->result_array();
				$text = $row[0]['mem_id'];
			}

			if($meta == "[account_id]"){
				/*$account_id_arr = str_split($account_id, 1);
				$account_id_format = '';
				foreach($account_id_arr as $k => $val){
					if($k == '1' || $k == '4' || $k == '5')
						$account_id_format .= ' - '.$val;
					else
						$account_id_format .= $val;
				}
				*/
				$account_id_format = $this->center_function->format_account_number($account_id);
				$text = $account_id_format;
			}

			if($meta == "[date_now]"){
				$text = date("d/m/").(date("Y")+543);
			}

			if($meta == "[book_name]"){
				$this->db->select(array('coop_deposit_type_setting.type_name'));
				$this->db->from('coop_deposit_type_setting');
				$this->db->where("coop_deposit_type_setting.type_id = (SELECT coop_maco_account.type_id FROM coop_maco_account WHERE coop_maco_account.account_id = '".$account_id."')");
				$row = $this->db->get()->result_array();
				$text = $row[0]['type_name'];
			}

			$rows[$key]['text'] = $text;
			
		}
		// var_dump($rows);
		// exit;
		$arr_data['rows'] = $rows;
		
		$this->load->view('save_money/book_bank_cover_pdf_customize',$arr_data);
	}
	
	function book_bank_page_fix_line_pdf_customize(){
		$arr_data = array();
		$account_id = $this->input->get('account_id');
		$arr_data['account_id'] = $account_id;
		
		
		$this->db->select(array('mem_group_id'));
		$this->db->from('coop_mem_apply');
		$this->db->where("member_id = '".$row[0]['mem_id']."'");
		$row_group = $this->db->get()->result_array();
		$arr_data['row_group'] = $row_group[0];
		
		$this->db->select(array('mem_group_name'));
		$this->db->from('coop_mem_group');
		$this->db->where("mem_group_id = '".$row_group[0]['mem_group_id']."'");
		$row_gname = $this->db->get()->result_array();
		if(!empty($row_gname)){
			$arr_data['row_gname'] = $row_gname[0];
		}else{
			$arr_data['row_gname']['mem_group_name'] = '';
		}

		$style = $this->db->get_where("coop_book_bank_style", array(
			"style_id" => 1
		))->result_array()[0];
		$arr_data['style'] = $style;

		$rows = $this->db->get_where("coop_book_bank_style_setting", array(
			"style_id" => 1
		))->result_array();
		foreach ($rows as $key => $value) {
			// var_dump($value);
			// echo "<br>";
			$meta = $value['style_value'];
			$text = $meta;
			if($meta == "[account_name]"){
				$this->db->select(array('account_name'));
				$this->db->from('coop_maco_account');
				$this->db->where("account_id = '".$account_id."'");
				$row = $this->db->get()->result_array();
				$text = $row[0]['account_name'];
			}

			if($meta == "[book_number]"){
				$this->db->select(array('book_number'));
				$this->db->from('coop_maco_account');
				$this->db->where("account_id = '".$account_id."'");
				$row = $this->db->get()->result_array();
				$text = $row[0]['book_number'];
			}

			if($meta == "[member_id]"){
				$this->db->select(array('mem_id'));
				$this->db->from('coop_maco_account');
				$this->db->where("account_id = '".$account_id."'");
				$row = $this->db->get()->result_array();
				$text = $row[0]['mem_id'];
			}

			if($meta == "[account_id]"){
				$account_id_arr = str_split($account_id, 1);
				$account_id_format = '';
				foreach($account_id_arr as $k => $val){
					if($k == '1' || $k == '4' || $k == '5')
						$account_id_format .= ' - '.$val;
					else
						$account_id_format .= $val;
				}
				$text = $account_id_format;
			}

			if($meta == "[date_now]"){
				$text = date("d/m/").(date("Y")+543);
			}

			$rows[$key]['text'] = $text;
			
		}
		// var_dump($rows);
		// exit;
		$arr_data['rows'] = $rows;

		$arr_data['arr_run_row'] = $this->report_accrued->get_run_row_transaction($account_id);
		$arr_data['arr_date_due'] = $this->report_accrued->get_date_transaction($account_id);
		$this->load->view('save_money/book_bank_page_fix_line_pdf_customize',$arr_data);
	}
	
	public function check_time_transaction(){
		$account = $_POST["account"];
		$date_transaction = ($_POST["date_transaction"] != '')?$this->center_function->ConvertToSQLDate($_POST["date_transaction"]):date('Y-m-d');
		$arr_time = $this->deposit_model->get_time_transaction($account,$date_transaction);
		$time_now = date("H:i:s");
		$arr_time['time_last'] = ($arr_time['time_last'] > $time_now)?$arr_time['time_last']:$time_now;
		echo json_encode($arr_time);
	}
	
	public function inline_update(){
		header('Content-Type: application/json');
		$token = $_POST['token'];
		if($token=="") die();
		$authen_token = sha1(md5(@$_POST['transaction_id']));
		if($authen_token != $token) die();

		//permission
		$this->db->select('user_permission_id');
		$this->db->from('coop_user_permission');
		$this->db->where("user_id = '".$_SESSION['USER_ID']."' AND menu_id = '408'");
		$row = $this->db->get()->result_array();
		if($row[0]['user_permission_id']==''){
			json_encode(array("result" => false, "message" => "No permission"));
		}

		$this->db->select("coop_maco_account.*, coop_account_transaction.transaction_time");
		$this->db->join("coop_maco_account", "coop_account_transaction.account_id = coop_maco_account.account_id", "inner");
		$account = $this->db->get_where("coop_account_transaction", array(
			"transaction_id" => $_POST['transaction_id']
		))->row_array();

		$method = @$_POST['method'];
		if($method=="transaction_time"){
			$tmp_date				= explode("/", $_POST['inline_date']);
			$transaction_id 		= $_POST['transaction_id'];
			$inline_date 			= ($tmp_date[2]-543) . "-" . $tmp_date[1] . "-" . $tmp_date[0]." 00:00:00";
			$status					= true;
			if(checkdate($tmp_date[1], $tmp_date[0], ($tmp_date[2]-543))){
				$this->db->set("transaction_time", $inline_date);
				$this->db->where("transaction_id", $transaction_id);
				$this->db->update("coop_account_transaction");
				$message = $this->center_function->ConvertToThaiDate($inline_date);
				$this->update_st->update_deposit_transaction($account['account_id'], ($tmp_date[2]-543) . "-" . $tmp_date[1] . "-" . $tmp_date[0]." 00:00:00");
			}else{
				$status = false;
				$message = "FAIL!";
			}
		}else if($method=="transaction_withdrawal"){
			$transaction_id 		= $_POST['transaction_id'];
			$transaction_withdrawal = implode("", explode(",", $_POST['inline_withdrawal']));
			$status					= true;
			if(is_numeric($transaction_withdrawal)){
				$this->db->set("transaction_withdrawal", $transaction_withdrawal);
				$this->db->where("transaction_id", $transaction_id);
				$this->db->update("coop_account_transaction");
				$message = $_POST['inline_withdrawal'];
				$this->update_st->update_deposit_transaction($account['account_id'], $account['transaction_time']);
			}else{
				$status = false;
				$message = "FAIL!";
			}
		}else if($method=="transaction_deposit"){
			$transaction_id 		= $_POST['transaction_id'];
			$transaction_deposit 	= implode("", explode(",", $_POST['inline_deposit']));
			$status					= true;
			if(is_numeric($transaction_deposit)){
				$this->db->set("transaction_deposit", $transaction_deposit);
				$this->db->where("transaction_id", $transaction_id);
				$this->db->update("coop_account_transaction");
				$message = $_POST['inline_deposit'];
				$this->update_st->update_deposit_transaction($account['account_id'], $account['transaction_time']);
			}else{
				$status = false;
				$message = "FAIL!";
			}
		}else if($method=="transaction_balance"){
			$transaction_id 		= $_POST['transaction_id'];
			$transaction_balance 	= implode("", explode(",", $_POST['inline_balance']));
			$status					= true;
			if(is_numeric($transaction_balance)){
				$this->db->set("transaction_balance", $transaction_balance);
				$this->db->where("transaction_id", $transaction_id);
				$this->db->update("coop_account_transaction");
				$message = $_POST['inline_balance'];
				$this->update_st->update_deposit_transaction($account['account_id'], $account['transaction_time']);
			}else{
				$status = false;
				$message = "FAIL!";
			}
		}else if($method=="seq_no"){
			$transaction_id 		= $_POST['transaction_id'];
			$seq_no 				= $_POST['seq_no'];
			$status					= true;
			if(is_numeric($seq_no)){
				$this->db->set("seq_no", $seq_no);
				$this->db->where("transaction_id", $transaction_id);
				$this->db->update("coop_account_transaction");
				$message = $_POST['seq_no'];
				$this->deposit_seq->update_seq_account_transaction($account['account_id'], $account['transaction_time'] ,$seq_no, $transaction_id);
			}else{
				$status = false;
				$message = "FAIL!";
			}
		}

		echo json_encode(array("result" => $status, "message" => $message));
	}	
}
