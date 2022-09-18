<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Finance_loan extends CI_Controller {
	public $loan_atm_status = array('0'=>'รออนุมัติ', '1'=>'อนุมัติ', '2'=>'ขอยกเลิก', '3'=>'ยกเลิกสัญญา', '4'=>'ปิดสัญญา','5'=>'ไม่อนุมัติ');
	public $loan_detail_status = array('0'=>'ทำรายการแล้ว', '1'=>'จ่ายครบแล้ว');
	public $transaction_at = array('0'=>'ทำรายการที่สหกรณ์', '1'=>'ทำรายการทีตู้ ATM');
	public $pay_type = array('0'=>'เงินสด', '1'=>'โอนเงิน' ,'2'=>'ATM');
	public $transfer_status = array('0'=>'ยังไม่ได้โอนเงิน','1'=>'โอนเงินแล้ว');
	
	function __construct()
	{
		parent::__construct();
	}
	function loan_atm_transfer(){		
		$arr_data = array();
		
		$arr_data['pay_type'] = $this->pay_type;
		$arr_data['transfer_status'] = $this->transfer_status;
		///////////		
		$x=0;
		$join_arr = array();
		$join_arr[$x]['table'] = 'coop_loan_atm as t2';
		$join_arr[$x]['condition'] = 't2.loan_atm_id = t1.loan_atm_id';
		$join_arr[$x]['type'] = 'inner';
		$x++;
		$join_arr[$x]['table'] = 'coop_mem_apply as t3';
		$join_arr[$x]['condition'] = 't2.member_id = t3.member_id';
		$join_arr[$x]['type'] = 'left';
		$x++;
		$join_arr[$x]['table'] = 'coop_user as t4';
		$join_arr[$x]['condition'] = 't1.admin_id = t4.user_id';
		$join_arr[$x]['type'] = 'left';
		
		$this->paginater_all->type(DB_TYPE);
		$this->paginater_all->select(array('t1.loan_id','t1.loan_date','t1.loan_amount','t2.contract_number','t2.createdatetime','t2.loan_atm_id','t1.transfer_status ','t3.member_id','t3.firstname_th','t3.lastname_th','t4.user_name'));
		$this->paginater_all->main_table('coop_loan_atm_detail as t1');
		$this->paginater_all->where("t1.transfer_status = '0'");
		$this->paginater_all->page_now(@$_GET["page"]);
		$this->paginater_all->per_page(20);
		$this->paginater_all->page_link_limit(20);
		$this->paginater_all->order_by('t1.loan_date ASC');
		$this->paginater_all->join_arr($join_arr);
		$row = $this->paginater_all->paginater_process();
		//echo"<pre>";print_r($row);exit;
		$paging = $this->pagination_center->paginating($row['page'], $row['num_rows'], $row['per_page'], $row['page_link_limit']);//$page_now = 1, $row_total = 1, $per_page = 20, $page_limit = 20
		$i = $row['page_start'];
		//echo $this->db->last_query(); exit;	

		$arr_data['num_rows'] = $row['num_rows'];
		$arr_data['paging'] = $paging;
		$arr_data['data'] = $row['data'];
		$arr_data['i'] = $i;
		
		
		$this->libraries->template('finance_loan/loan_atm_transfer',$arr_data);
	}
	
	function loan_atm_transfer_open(){
		echo '<pre>'; print_r($_POST); echo '</pre>';
		if(@$_POST['start_date']){
			$start_date_arr = explode('/',@$_POST['start_date']);
			$start_day = $start_date_arr[0];
			$start_month = $start_date_arr[1];
			$start_year = $start_date_arr[2];
			$start_year -= 543;
			$start_date = $start_year.'-'.$start_month.'-'.$start_day;
		}
		
		if(@$_POST['end_date']){
			$end_date_arr = explode('/',@$_POST['end_date']);
			$end_day = $end_date_arr[0];
			$end_month = $end_date_arr[1];
			$end_year = $end_date_arr[2];
			$end_year -= 543;
			$end_date = $end_year.'-'.$end_month.'-'.$end_day;
		}
		
		$transfer_type = (@$_POST['transfer_type'] == '')?"all":@$_POST['transfer_type'];
		$param = "/".$transfer_type."/".$start_date."/".$end_date ;
		
		echo"<script> window.open('".PROJECTPATH."/finance_loan/loan_atm_transfer_view".@$param."','_blank') </script>";
		echo"<script> document.location.href='".PROJECTPATH."/finance_loan/loan_atm_transfer' </script>";		
		exit;
	}
	
	function loan_atm_transfer_view($transfer_type='all',$start_date='',$end_date=''){
		$arr_data = array();
		
		$where = "";
		if(@$transfer_type != '' && @$transfer_type != 'all'){
			$where .= " AND t1.pay_type = '".@$transfer_type."'";
		}

		if(@$start_date != '' AND @$end_date == ''){
			$where .= " AND t1.date_transfer BETWEEN '".@$start_date." 00:00:00.000' AND '".@$start_date." 23:59:59.000'";
		}else if(@$start_date != '' AND @$end_date != ''){
			$where .= " AND t1.date_transfer BETWEEN '".@$start_date." 00:00:00.000' AND '".@$end_date." 23:59:59.000'";
		}
		
		$this->db->select(array(								
			't2.loan_amount',
			't1.*',
			't4.member_id',
			't4.firstname_th',
			't4.lastname_th'
		));
		$this->db->from('coop_loan_atm_transfer AS t1');
		$this->db->join('coop_loan_atm_detail AS t2','t1.loan_id = t2.loan_id','inner');
		$this->db->join('coop_loan_atm AS t3','t3.loan_atm_id = t2.loan_atm_id','inner');
		$this->db->join("coop_mem_apply AS t4","t3.member_id = t4.member_id",'inner');
		$this->db->where("1=1 {$where}");
		$this->db->order_by("t2.loan_id DESC");
		$row_transaction = $this->db->get()->result_array();
		$arr_data['row_transaction'] = @$row_transaction;
		$arr_data['start_date'] = @$start_date;
		$arr_data['end_date'] = @$end_date;
		$arr_data['pay_type'] = $this->pay_type;
		//echo $this->db->last_query(); exit;						
		//echo '<pre>'; print_r($row_transaction); echo '</pre>';
		$this->preview_libraries->template_preview('finance_loan/loan_atm_transfer_view',$arr_data);
	}
	
	function get_loan_atm_data(){
		$data = array();
		$this->db->select(array(
			't2.contract_number',
			't3.member_id',
			't3.firstname_th',
			't3.lastname_th',
			't1.loan_amount',
			't1.bank_id',
			't1.bank_account_id',
			't1.account_id',
			't1.pay_type',
			't3.dividend_acc_num',
			't3.dividend_bank_id'
		));
		$this->db->from("coop_loan_atm_detail t1");
		$this->db->join("coop_loan_atm t2",'t2.loan_atm_id = t1.loan_atm_id','inner');
		$this->db->join("coop_mem_apply t3",'t2.member_id = t3.member_id','left');
		$this->db->where("t1.loan_id = '".$_POST['loan_id']."'");
		$row = $this->db->get()->result_array();
		$coop_loan_atm = $row[0];
		$data = array();
		foreach($row[0] as $key => $value){
			if($key == 'loan_amount'){
				$data[$key] = number_format($value);
			}else{
				$data[$key] = $value;
			}
		}
	
		echo json_encode($data);
		exit;
	}
	
	function loan_atm_transfer_save(){
		
		$this->db->select(array(
			'loan_amount'
		));
		$this->db->from('coop_loan_atm_detail');
		$this->db->where("loan_id = '".$_POST['loan_id']."'");
		$row_loan = $this->db->get()->result_array();
		$row_loan = $row_loan[0];
		
		//$date_arr = explode('/',$_POST['date_transfer']);
		//$date_transfer = ($date_arr[2]-543)."-".$date_arr[1]."-".$date_arr[0]." ".$_POST['time_transfer'];
		$date_transfer = date('Y-m-d H:i:s');		
		$data_insert = array();
		$data_insert['loan_id'] = @$_POST['loan_id'];
		//$data_insert['account_id'] = $_POST['account_id'];
		$data_insert['date_transfer'] = $date_transfer;
		$data_insert['createdatetime'] = date('Y-m-d H:i:s');
		$data_insert['admin_id'] = $_SESSION['USER_ID'];
		$data_insert['transfer_status'] = '0';
		$data_insert['pay_type'] = @$_POST['pay_type'];
		$data_insert['dividend_bank_id'] = @$_POST['dividend_bank_id'];
		//$data_insert['dividend_bank_branch_id'] = @$_POST['dividend_bank_branch_id'];
		$data_insert['dividend_acc_num'] = @$_POST['dividend_acc_num'];
		$data_insert['account_id'] = @$_POST['account_id'];
		$this->db->insert('coop_loan_atm_transfer', $data_insert);	
		//$last_id = $this->db->insert_id();
		
		if(@$_POST['pay_type'] == '3' && @$_POST['account_id']!=''){
			$this->db->select('transaction_balance');
			$this->db->from('coop_account_transaction');
			$this->db->where("account_id = '".@$_POST['account_id']."'");
			$this->db->order_by('transaction_time DESC, transaction_id DESC');
			$this->db->limit(1);
			$row_balance = $this->db->get()->result_array();
			$transaction_balance = $row_balance[0]['transaction_balance']+$row_loan['loan_amount'];
			$data_insert = array();
			$data_insert['account_id'] = @$_POST['account_id'];
			$data_insert['transaction_time'] = date('Y-m-d H:i:s');
			$data_insert['transaction_list'] = 'XD';
			$data_insert['transaction_withdrawal'] = '0';
			$data_insert['transaction_deposit'] = $row_loan['loan_amount'];
			$data_insert['transaction_balance'] = $transaction_balance;
			$data_insert['transaction_no_in_balance'] = $transaction_balance;
			$data_insert['user_id'] = $_SESSION['USER_ID'];
			$this->db->insert('coop_account_transaction', $data_insert);	
		}
		
		$data_insert = array();
		$data_insert['transfer_status'] = '1';
		$this->db->where('loan_id', $_POST['loan_id']);
		$this->db->update('coop_loan_atm_detail', $data_insert);
	
		$account_chart_id = '10103004';
		$account_chart = 'ลูกหนี้เงินกู้เพื่อเหตุฉุกเฉิน(ATM)';
		if(!empty($_POST['account_id'])){
			$account_id_transfer = $_POST['account_id'];
		  }else if(!empty($_POST['dividend_acc_num'])){
			$account_id_transfer = $_POST['dividend_acc_num'];
		  }else{
			$account_id_transfer = '';
		  }

		$i=0;

        $process = 'loan_transfer';
        $money = @$row_loan['loan_amount'];
        $ref = $_POST['loan_id'];
        if(@$_POST['pay_type'] == 1){
            $match_type = 'main';
            $match_id = '4';
            $statement = 'debit';
        }else{
            $match_type = 'main';
            $match_id = '1';
            $statement = 'credit';
        }

        $data_process[] =   $this->account_transaction->set_data_account_trancetion_detail($match_id,$statement,$match_type,$ref,$money,$process);

        $process = 'loan_transfer';
        $money = @$row_loan['loan_amount'];
        $ref = $_POST['loan_id'];
        $match_type = 'account_list';
        $match_id = '31';
        if(@$_POST['pay_type'] == 1){
            $statement = 'credit';
        }else{
            $statement = 'debit';
        }

        $data_process[] =   $this->account_transaction->set_data_account_trancetion_detail($match_id,$statement,$match_type,$ref,$money,$process);

//        echo"<pre>";print_r($data_process);

        $this->account_transaction->add_account_trancetion_detail($data_process);



		$this->center_function->toast("บันทึกข้อมูลเรียบร้อยแล้ว");
		echo "<script> document.location.href='".base_url(PROJECTPATH.'/finance_loan/loan_atm_transfer')."' </script>";
		exit;
	}
}
