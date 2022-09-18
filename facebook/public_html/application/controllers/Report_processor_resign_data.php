<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Report_processor_resign_data extends CI_Controller {
	function __construct()
	{
		parent::__construct();
	}

	public function coop_report_refund_resign_month(){
		$arr_data = array();

		$this->db->select(array('id','mem_group_name'));
		$this->db->from('coop_mem_group');
		$this->db->where("mem_group_type = '1'");
		$row = $this->db->get()->result_array();
		$arr_data['row_mem_group'] = $row;

		$this->db->select('mem_type_id, mem_type_name');
		$this->db->from('coop_mem_type');
		$row = $this->db->get()->result_array();
		$arr_data['mem_type'] = $row;
		
		$month_arr = $this->center_function->month_arr();

		$this->libraries->template('report_processor_resign_data/coop_report_refund_resign_month',$arr_data);
	}
	
	function check_coop_report_refund_resign_month() {
		if (!empty($_POST['start_date'])) {
			$start_date_arr = explode("/", $_POST['start_date']);
			$start_date = ($start_date_arr[2] - 543)."-".$start_date_arr[1]."-".$start_date_arr[0];
			//$receipt_con .= " AND t9.receipt_datetime >= '{$start_date}'";
		}
		if (!empty($_POST['end_date'])) {
			$end_date_arr = explode("/", $_POST['end_date']);
			$end_date = ($end_date_arr[2] - 543)."-".$end_date_arr[1]."-".$end_date_arr[0];
			//$receipt_con .= " AND t9.receipt_datetime <= '{$end_date}'";
		}

		$sql = "SELECT
					t1.*,
					t3.prename_full,
					t2.firstname_th,
					t2.lastname_th,
					t2.mobile,
					IF(t1.loan_id <> '',t4.contract_number,IF( t1.loan_atm_id <> '',t5.contract_number,'')) AS contract_number	
				FROM
					coop_process_return_resign AS t1
				INNER JOIN coop_mem_apply AS t2 ON t1.member_id = t2.member_id
				LEFT JOIN coop_prename AS t3 ON t2.prename_id = t3.prename_id				
				LEFT JOIN coop_loan AS t4 ON t1.loan_id = t4.id
				LEFT JOIN coop_loan_atm AS t5 ON t1.loan_atm_id = t5.loan_atm_id
				WHERE
					t1.return_time BETWEEN '".$start_date." 00:00:00' AND '".$end_date." 23:59:59'
				ORDER BY
					t1.member_id ASC,
					t1.return_time ASC";
		$rs_row = $this->db->query($sql)->result_array();			
			
		$hasData = false;
		foreach($rs_row as $data) {			
			if(!empty($rs_row)) {
				$hasData = true;
				break;
			}
		}

		if($hasData){
			echo "success";
		}else{
			echo "";
		}
	}
	
	function coop_report_refund_resign_month_preview() {
		set_time_limit ( 180 );

		$title_date = "";
		$arr_data = array();
		if (!empty($_GET['start_date'])) {
			$start_date_arr = explode("/", $_GET['start_date']);
			$start_date = ($start_date_arr[2] - 543)."-".$start_date_arr[1]."-".$start_date_arr[0];
			//$receipt_con .= " AND t9.receipt_datetime >= '{$start_date}'";
			$title_date .= "วันที่ ".@$this->center_function->ConvertToThaiDate($start_date,1,0).' ';
		}
		if (!empty($_GET['end_date'])) {
			$end_date_arr = explode("/", $_GET['end_date']);
			$end_date = ($end_date_arr[2] - 543)."-".$end_date_arr[1]."-".$end_date_arr[0];
			//$receipt_con .= " AND t9.receipt_datetime <= '{$end_date}'";
			$title_date .= "ถึงวันที่ ".@$this->center_function->ConvertToThaiDate($end_date,1,0).' ';
		}

		
		$sql = "SELECT
					t1.*,
					t3.prename_full,
					t2.firstname_th,
					t2.lastname_th,
					t2.mobile,
					IF(t1.loan_id <> '',t4.contract_number,IF( t1.loan_atm_id <> '',t5.contract_number,'')) AS contract_number	
				FROM
					coop_process_return_resign AS t1
				INNER JOIN coop_mem_apply AS t2 ON t1.member_id = t2.member_id
				LEFT JOIN coop_prename AS t3 ON t2.prename_id = t3.prename_id				
				LEFT JOIN coop_loan AS t4 ON t1.loan_id = t4.id
				LEFT JOIN coop_loan_atm AS t5 ON t1.loan_atm_id = t5.loan_atm_id
				WHERE
					t1.return_time BETWEEN '".$start_date." 00:00:00' AND '".$end_date." 23:59:59'
				ORDER BY
					t1.member_id ASC,
					t1.return_time ASC";
		$rs_row = $this->db->query($sql)->result_array();		
		$member_ids = array_column($rs_row, 'member_id');
		//echo '<pre>'; print_r($rs_row); echo '</pre>';
		$num_rows = count($rs_row);

		$page_num = 1;
		$all_page = ceil($num_rows/100);		
		$rawArray = array();
		$prosess_data = array();
		
		foreach($rs_row as $index => $detail) {
			$prosess_data[$detail['member_id']]["mobile"] = @$detail['mobile'];
			$prosess_data[$detail['member_id']]["receipt_id"] = @$detail['receipt_id'];
			$prosess_data[$detail['member_id']]["prename_full"] = @$detail['prename_full'];
			$prosess_data[$detail['member_id']]["firstname_th"] = @$detail['firstname_th'];
			$prosess_data[$detail['member_id']]["lastname_th"] = @$detail['lastname_th'];
			$prosess_data[$detail['member_id']]["member_id"] = @$detail['member_id'];
			
			if(@$detail['account_list_id'] == '16'){
				$prosess_data[$detail['member_id']]["share"] = @$detail['return_amount'];
			}
			
			if(@$detail['account_list_id'] == '30'){
				$prosess_data[$detail['member_id']]["deposit"] = @$detail['return_amount'];
			}
						
			if(@$detail['account_list_id'] == '15'){				
				$prosess_data[$detail['member_id']]['loan'][@$detail['loan_id']]["principal"] = @$detail['return_principal'];
				$prosess_data[$detail['member_id']]['loan'][@$detail['loan_id']]["interest"] = @$detail['return_interest'];
				$prosess_data[$detail['member_id']]['loan'][@$detail['loan_id']]["contract_number"] = @$detail['contract_number'];
			}	
				
			if(@$detail['account_list_id'] == '31'){				
				$prosess_data[$detail['member_id']]['loan'][@$detail['loan_atm_id']]["principal"] = @$detail['return_principal'];
				$prosess_data[$detail['member_id']]['loan'][@$detail['loan_atm_id']]["interest"] = @$detail['return_interest'];
				$prosess_data[$detail['member_id']]['loan'][@$detail['loan_atm_id']]["contract_number"] = @$detail['contract_number'];
			}	
			
			$data_bank = "SELECT
							t2.bank_name,
							t2.bank_code,
							t1.dividend_acc_num
						FROM
							coop_mem_bank_account AS t1
						LEFT JOIN coop_bank AS t2 ON t1.dividend_bank_id = t2.bank_id
						WHERE
							t1.member_id = '".$detail['member_id']."'
						AND t2.bank_code = 'KTB'";
			$rs_row_bank = $this->db->query($data_bank)->result_array();
			$taxt_bank = '';
			foreach($rs_row_bank as $key_bank => $rs_row_bank) {
				$taxt_bank .= $rs_row_bank['dividend_acc_num'].' ';
			}
			$prosess_data[$detail['member_id']]["bank_account"] = $taxt_bank;
		}		
		//echo '<pre>'; print_r($prosess_data); echo '</pre>';
		$page_get = !empty($_GET['page']) ? $_GET['page'] : 1;
		$paging = $this->pagination_center->paginating(intval($page_get), $all_page, 1, 20,@$_GET);//$page_now = 1, $row_total = 1, $per_page = 20, $page_limit = 20

		
		foreach($prosess_data as $index => $data) {
			/*echo (($index - 35)%42).'<br>';
			echo 'index='.$index.'<br>';
			echo 'page_num='.$page_num.'<br>';
			if ($index == 35 || (($index - 35)%42) == 0) {
				$page_num++;
			}
			*/
			$row['data'][$page_num][] = $data;
		}
		
		
		$arr_data['num_rows'] = $num_rows;
		$arr_data['data'] = $row['data'];
		$arr_data['page_all'] = $page_num;
		$arr_data['title_date'] = $title_date;
		//echo '<pre>'; print_r($arr_data); echo '</pre>';
		// $arr_data['paging'] = $paging;
		$this->preview_libraries->template_preview('report_processor_resign_data/coop_report_refund_resign_month_preview',$arr_data);
	}
	
	function coop_report_refund_resign_month_excel() {
		$title_date = "";
		$arr_data = array();
		if (!empty($_GET['start_date'])) {
			$start_date_arr = explode("/", $_GET['start_date']);
			$start_date = ($start_date_arr[2] - 543)."-".$start_date_arr[1]."-".$start_date_arr[0];
			//$receipt_con .= " AND t9.receipt_datetime >= '{$start_date}'";
			$title_date .= "วันที่ ".@$this->center_function->ConvertToThaiDate($start_date,1,0).' ';
		}
		if (!empty($_GET['end_date'])) {
			$end_date_arr = explode("/", $_GET['end_date']);
			$end_date = ($end_date_arr[2] - 543)."-".$end_date_arr[1]."-".$end_date_arr[0];
			//$receipt_con .= " AND t9.receipt_datetime <= '{$end_date}'";
			$title_date .= "ถึงวันที่ ".@$this->center_function->ConvertToThaiDate($end_date,1,0).' ';
		}

		
		$sql = "SELECT
					t1.*,
					t3.prename_full,
					t2.firstname_th,
					t2.lastname_th,
					t2.mobile,
					IF(t1.loan_id <> '',t4.contract_number,IF( t1.loan_atm_id <> '',t5.contract_number,'')) AS contract_number	
				FROM
					coop_process_return_resign AS t1
				INNER JOIN coop_mem_apply AS t2 ON t1.member_id = t2.member_id
				LEFT JOIN coop_prename AS t3 ON t2.prename_id = t3.prename_id				
				LEFT JOIN coop_loan AS t4 ON t1.loan_id = t4.id
				LEFT JOIN coop_loan_atm AS t5 ON t1.loan_atm_id = t5.loan_atm_id
				WHERE
					t1.return_time BETWEEN '".$start_date." 00:00:00' AND '".$end_date." 23:59:59'
				ORDER BY
					t1.member_id ASC,
					t1.return_time ASC";
		$rs_row = $this->db->query($sql)->result_array();		
		$member_ids = array_column($rs_row, 'member_id');
		
		$num_rows = count($rs_row);

		$page_num = 1;
		$all_page = ceil($num_rows/100);		
		$rawArray = array();
		$prosess_data = array();
		
		foreach($rs_row as $index => $detail) {
			$prosess_data[$detail['member_id']]["mobile"] = @$detail['mobile'];
			$prosess_data[$detail['member_id']]["receipt_id"] = @$detail['receipt_id'];
			$prosess_data[$detail['member_id']]["prename_full"] = @$detail['prename_full'];
			$prosess_data[$detail['member_id']]["firstname_th"] = @$detail['firstname_th'];
			$prosess_data[$detail['member_id']]["lastname_th"] = @$detail['lastname_th'];
			$prosess_data[$detail['member_id']]["member_id"] = @$detail['member_id'];
			
			if(@$detail['account_list_id'] == '16'){
				$prosess_data[$detail['member_id']]["share"] = @$detail['return_amount'];
			}
			
			if(@$detail['account_list_id'] == '30'){
				$prosess_data[$detail['member_id']]["deposit"] = @$detail['return_amount'];
			}
						
			if(@$detail['account_list_id'] == '15'){				
				$prosess_data[$detail['member_id']]['loan'][@$detail['loan_id']]["principal"] = @$detail['return_principal'];
				$prosess_data[$detail['member_id']]['loan'][@$detail['loan_id']]["interest"] = @$detail['return_interest'];
				$prosess_data[$detail['member_id']]['loan'][@$detail['loan_id']]["contract_number"] = @$detail['contract_number'];
			}	
				
			if(@$detail['account_list_id'] == '31'){				
				$prosess_data[$detail['member_id']]['loan'][@$detail['loan_atm_id']]["principal"] = @$detail['return_principal'];
				$prosess_data[$detail['member_id']]['loan'][@$detail['loan_atm_id']]["interest"] = @$detail['return_interest'];
				$prosess_data[$detail['member_id']]['loan'][@$detail['loan_atm_id']]["contract_number"] = @$detail['contract_number'];
			}	
			
			$data_bank = "SELECT
							t2.bank_name,
							t2.bank_code,
							t1.dividend_acc_num
						FROM
							coop_mem_bank_account AS t1
						LEFT JOIN coop_bank AS t2 ON t1.dividend_bank_id = t2.bank_id
						WHERE
							t1.member_id = '".$detail['member_id']."'
						AND t2.bank_code = 'KTB'";
			$rs_row_bank = $this->db->query($data_bank)->result_array();
			$taxt_bank = '';
			foreach($rs_row_bank as $key_bank => $rs_row_bank) {
				$taxt_bank .= $rs_row_bank['dividend_acc_num'].' ';
			}
			$prosess_data[$detail['member_id']]["bank_account"] = $taxt_bank;
		}	

		$arr_data['data'] = $prosess_data;
		$arr_data['title_date'] = $title_date;
		$this->load->view('report_processor_resign_data/coop_report_refund_resign_month_excel',$arr_data);
	}

}
