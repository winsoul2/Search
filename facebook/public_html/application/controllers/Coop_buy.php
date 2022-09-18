<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Coop_buy extends CI_Controller {
	function __construct()
	{
		parent::__construct();
	}
	public function index()
	{
		$arr_data = array();
		$data_get = $this->input->get();
		$arr_data['data_get'] = @$data_get;
		if(@$data_get['account_buy_id']!=''){
			
			$this->db->select('*');
			$this->db->from('coop_account_buy');
			$this->db->where("account_buy_id = '".$data_get['account_buy_id']."'");
			$row = $this->db->get()->result_array();
			$arr_data['row'] = @$row[0];
			
			$this->db->select('*');
			$this->db->from('coop_account_buy_detail');
			$this->db->where("account_buy_id = '".$data_get['account_buy_id']."'");
			$this->db->order_by('account_buy_detail_id ASC');
			$row = $this->db->get()->result_array();
			$arr_data['rs_detail'] = @$row;
		}
		
		$this->db->select(array('account_id','account_list','amount'));
		$this->db->from('coop_account_buy_list');
		$account_buy_list = $this->db->get()->result_array();
		$arr_data['account_buy_list'] = @$account_buy_list; 
		
		$this->db->select(array('account_bank_id','account_bank_name'));
		$this->db->from('coop_account_bank');
		$rs_bank = $this->db->get()->result_array();
		$arr_data['rs_bank'] = @$rs_bank; 
		
		$this->db->select(array('*'));
		$this->db->from('coop_account_buy');
		$this->db->order_by('account_buy_number DESC');
		$this->db->limit(5);
		$rs_search = $this->db->get()->result_array();
		$arr_data['rs_search'] = @$rs_search; 
		
		$this->libraries->template('coop_buy/index',$arr_data);
	}

	function save_buy() {
		$data_post = $this->input->post();
		$this->db->select(array('account_buy_number'));
		$this->db->from('coop_account_buy');
		$this->db->where("account_buy_number LIKE '".date("Ym")."%'");
		$this->db->order_by('account_buy_number DESC');
		$this->db->limit(1);
		$row = $this->db->get()->result_array();
		$row = @$row[0];
/*		if(@$row['account_buy_number'] != ''){
			$id = (int) substr($row["account_buy_number"], 6);
			$account_buy_number = date("Ym").sprintf("%06d", $id + 1);
		}
		else {
			$account_buy_number = date("Ym")."000001";
		}*/
		$data = $data_post['data'];
		if($data['coop_account_buy']['pay_type'] == 'cash'){
			$pay_type = 0;
		}else{
			$pay_type = 1;
		}
		$buy_date_arr = explode('/',$data['coop_account_buy']['buy_date']);
		$buy_date = ($buy_date_arr[2]-543)."-".$buy_date_arr[1]."-".$buy_date_arr[0];
		$account_buy_number = $this->receipt_model->generate_receipt($buy_date, $pay_type);


		$data_insert = array();
		$data_insert['buy_date'] = $buy_date;
		$data_insert['account_buy_number'] = $account_buy_number;
		$data_insert['pay_for'] = $data['coop_account_buy']['pay_for'];
		$data_insert['pay_type'] = $data['coop_account_buy']['pay_type'];
		if($data['coop_account_buy']['pay_type'] == 'cheque'){
			$cheque_date_arr = explode('/',$data['coop_account_buy']['cheque_date']);
			$cheque_date = ($cheque_date_arr[2]-543)."-".$cheque_date_arr[1]."-".$cheque_date_arr[0];
			$data_insert['cheque_number'] = $data['coop_account_buy']['cheque_number'];
			$data_insert['cheque_date'] = $cheque_date;
		}else if($data['coop_account_buy']['pay_type'] == 'transfer'){
			$data_insert['account_bank_id'] = $data['coop_account_buy']['account_bank_id'];
		}
		$data_insert['account_buy_status'] = '0';
		$data_insert['cashpay_type'] = $data['coop_account_buy']['cashpay_input'];
		$this->db->insert('coop_account_buy', $data_insert);

		$account_buy_id = $this->db->insert_id();

		$total_amount = 0;
		$total = 0;
		$i=0;
		$data_account = array();
		foreach($data['coop_account_buy_detail'] as $key => $value){
			$data_insert = array();
			$data_insert['account_buy_id'] = $account_buy_id;
			$data_insert['account_id'] = $value['account_id'];
			$data_insert['pay_amount'] = $value['pay_amount'];
			$data_insert['pay_description'] = $value['pay_description'];
			$data_insert['bill_number'] = $value['bill_number'];
			$this->db->insert('coop_account_buy_detail', $data_insert);

			$this->db->select(array('account_chart_id'));
			$this->db->from('coop_account_match');
			$this->db->where("match_type = 'account_buy_list' AND match_id = '".$value['account_id']."'");
			$row = $this->db->get()->result_array();
			$row_match = $row[0];
			if(!empty($row_match)) {
				$data_account['coop_account_detail'][$i]['account_type'] = $data["coop_account_buy"]["cashpay_input"] == "payment" ? 'debit' : "credit";
				$data_account['coop_account_detail'][$i]['account_amount'] = $value['pay_amount'];
				$data_account['coop_account_detail'][$i]['account_chart_id'] = $row_match['account_chart_id'];
				$total_amount += $value['pay_amount'];
			}
			$total += $value['pay_amount'];

			$i++;
		}

		if(!empty($total_amount)) {
			$pay_type = $_POST["data"]["coop_account_buy"]["pay_type"] == "cash" ? '0' : '1';

			if($_POST["data"]["coop_account_buy"]["pay_type"] == "cash") {
				$account_cash = $this->db->select("*")->from('coop_account_setting')->where("type = 'cash_chart_id'")->get()->row();
			} else {
				$account_cash = $this->db->select("*")->from('coop_account_setting')->where("type = 'cash_tran_chart_id'")->get()->row();
			}
			$account_description = $data["coop_account_buy"]["cashpay_input"] == "receipt" ?  "รายการรับ จาก ".$data['coop_account_buy']['pay_for'] :  "รายการจ่าย ให้ ".$data['coop_account_buy']['pay_for'];

			$data_account['coop_account_detail'][$i]['account_type'] = $data["coop_account_buy"]["cashpay_input"] == "payment" ? 'credit' : "debit";
			$data_account['coop_account_detail'][$i]['account_amount'] = $total_amount;
			$data_account['coop_account_detail'][$i]['account_chart_id'] = $account_cash->value;

			$data_account['coop_account']['account_description'] = "รายการซื้อ จ่ายให้ ".$data['coop_account_buy']['pay_for'];
			$data_account['coop_account']['account_datetime'] = $buy_date;
			$data_account["coop_account"]['ref'] = $account_buy_id;
			$data_account["coop_account"]["ref_type"] = "account_buy";
			$data_account["coop_account"]["process"] = "process";

			$account_book_id = $this->account_transaction->insert_account_transaction($data_account);
		}

		if(!empty($total)) {
			$data_drawer = array();
			$data_drawer["account_list_id"] = NULL;
			$data_drawer["principal_payment"] = $total;
			$data_drawer["total_amount"] = $total;
			$data_drawer["ref"] = $account_buy_id;
			$statement_status = $_POST["data"]["coop_account_buy"]["cashpay_input"] == "receipt" ? 'debit' : 'credit';   // สถานะการจ่ายเงิน debit = เงินเข้าจากเคาน์เตอร์, credit  = เงินออกจากเคาน์เตอร์,
			$pay_type = $_POST["data"]["coop_account_buy"]["pay_type"] == "cash" ? '0' : '1';
			$this->tranction_financial_drawer->arrange_data_coop_financial_drawer($data_drawer, $pay_type, 86, $statement_status, $_SERVER['REQUEST_URI'], "coop_buy");
		}

		$data_insert = array();
		$data_insert['total_amount'] = @$total;
		$data_insert['account_book_id'] = @$account_book_id;
		$this->db->where('account_buy_id', @$account_buy_id);
		$this->db->update('coop_account_buy', @$data_insert);

		echo"<script> document.location.href='".base_url(PROJECTPATH.'/coop_buy/coop_buy_pdf?account_buy_id='.$account_buy_id)."' </script>";
		exit;
	}

	function coop_buy_pdf(){
		$arr_data = array();
		$data_get = $this->input->get();
		$this->db->select(array(
			't1.account_buy_id',
			't1.total_amount',
			't1.buy_date',
			't1.account_buy_number',
			't1.pay_type',
			't1.cheque_number',
			't1.cheque_date',
			't1.pay_for',
			't1.cashpay_type',
            't1.account_bank_id',
            't2.account_bank_name',
			't2.account_bank')
		);
		$this->db->from('coop_account_buy as t1');
        $this->db->join('coop_account_bank as t2', 't1.account_bank_id = t2.account_bank_id','left');
		$this->db->where("t1.account_buy_id = '".$data_get['account_buy_id']."'");
		$row = $this->db->get()->result_array();
		$arr_data['row'] = @$row[0];
		
		$this->db->select(array(
			't1.account_id',
			't1.pay_amount',
			't1.pay_description',
			't1.bill_number',
			't2.account_chart_id'
			)
		);
		$this->db->from('coop_account_buy_detail as t1');
		$this->db->join('coop_account_match t2', "t1.account_id = t2.match_id AND match_type = 'account_buy_list'", 'left');
		$this->db->where("account_buy_id = '".$data_get['account_buy_id']."'");
		$row = $this->db->get()->result_array();
		$arr_data['rs_detail'] = @$row;
		
		//ลายเซ็นต์
		//$date_signature = $arr_data['row']['buy_date'];
		$date_signature = date("Y-m-d", strtotime($arr_data['row']['buy_date']));
		//echo $date_signature ;exit;
		$this->db->select(array('*'));
		$this->db->from('coop_signature');
		$this->db->where("start_date <= '{$date_signature}'");
		$this->db->order_by('start_date DESC');
		$this->db->limit(1);
		$row = $this->db->get()->result_array();
		$arr_data['signature'] = @$row[0];
		$this->load->view('coop_buy/coop_buy_pdf',$arr_data);
	}
	function search_account_buy(){
			$this->db->select(array('*'));
			$this->db->from('coop_account_buy');
			$this->db->where("account_buy_number LIKE '%".$this->input->post("search")."%'
			OR pay_for LIKE '%".$this->input->post("search")."%'
			");
			$this->db->order_by('account_buy_number DESC');
			$this->db->limit(5);
			$rs = $this->db->get()->result_array();
			$output = '';
           if(!empty($rs)){  
				$i= 1; 
				foreach($rs as $key => $row){
                     $output .= '
						<tr> 
							<th scope="row">'.$row['account_buy_number'].'</th>
							<td>'.$this->center_function->ConvertToThaiDate($row['buy_date'],'1','0').'</td> 
							<td>'.$row['pay_for'].'</td> 
							<td align="right">
								<a href="?account_buy_id='.$row['account_buy_id'].'">
									<button style="padding: 2px 12px;"  id="'.$row['account_buy_id'].'" type="button" class="btn btn-info">เลือก</button>
								</a>
							</td>
						</tr>
                     ';  
               $i++; 
			   }
                echo $output;  
           }else{
                $output .= '  

                          <tr>  
                               <td align="center" colspan="4"><h1>ไม่พบผลการค้นหา!</h1></td>  
                          </tr> 
                     ';
                echo $output;  
           }  
		   exit;
	}
	function cancel_account_buy(){
		$data_get = $this->input->get();
		if($data_get['account_buy_id']!='' && $data_get['action_cancel']!=''){
			$data_insert = array();
			$data_insert['account_buy_status'] = $data_get['action_cancel'];
			$data_insert['cancel_date'] = date('Y-m-d H:i:s');
			$this->db->where('account_buy_id', $data_get['account_buy_id']);
			$this->db->update('coop_account_buy', $data_insert);
			
			echo"<script> document.location.href='".base_url(PROJECTPATH.'/coop_buy?account_buy_id='.$data_get['account_buy_id'])."' </script>";
			exit;
		}
	}
	function coop_buy_cancel(){
		$arr_data = array();
		
		if (@$this->input->get('account_buy_id') != '') {
			$data_insert = array();
			$data_insert['account_buy_status'] = $this->input->get('status_to');
			$this->db->where('account_buy_id', $this->input->get('account_buy_id'));
			$this->db->update('coop_account_buy', $data_insert);
			
			$this->db->select(array('account_book_id'));
			$this->db->from('coop_account_buy');
			$this->db->where("account_buy_id = '".$this->input->get('account_buy_id')."'");
			$row = $this->db->get()->result_array();
			
			$data_insert = array();
			$data_insert['account_status'] = '2';
			$this->db->where('account_id', $row[0]['account_book_id']);
			$this->db->update('coop_account', $data_insert);
			
			echo"<script> document.location.href='".base_url(PROJECTPATH.'/coop_buy/coop_buy_cancel')."'; </script>";
			exit;
		}
		$x=0;
		$join_arr = array();
		
		$this->paginater_all->type(DB_TYPE);
		$this->paginater_all->select('*');
		$this->paginater_all->main_table('coop_account_buy');
		$this->paginater_all->where("account_buy_status IN('1','2')");
		$this->paginater_all->page_now(@$_GET["page"]);
		$this->paginater_all->per_page(10);
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
		
		$this->libraries->template('coop_buy/coop_buy_cancel',$arr_data);
	}

	function coop_buy_preview_pdf(){
		$arr_data = array();
		$row = array();
		$row["cashpay_type"] = $_POST["cashpay_input"];
		$row["pay_for"] = $_POST["pay_for"];

		$buy_date_arr = explode('/',$_POST['buy_date']);
		$row["buy_date"] = ($buy_date_arr[2]-543)."-".$buy_date_arr[1]."-".$buy_date_arr[0];
		$total = 0;
		$rs_detail = array();
		foreach($_POST["data"]["coop_account_buy_detail"] as $buy_detail) {
			$detail = array();
			$detail["pay_description"] = $buy_detail["pay_description"];
			$detail["pay_amount"] = $buy_detail["pay_amount"];
			$total += $buy_detail["pay_amount"];
			$rs_detail[] = $detail;
		}
		$row["total_amount"] = $total;
        $this->db->select('account_bank_name');
        $this->db->from('coop_account_bank');
        $this->db->where('account_bank_id = '.$_POST['account_bank_id']);
        $bank = $this->db->get()->row_array();
        $row["account_bank_name"] = $bank['account_bank_name'];

		$arr_data["row"] = $row;
		$arr_data["rs_detail"] = $rs_detail;

		//ลายเซ็นต์
		$date_signature = date("Y-m-d", strtotime($arr_data['row']['buy_date']));
		$this->db->select(array('*'));
		$this->db->from('coop_signature');
		$this->db->where("start_date <= '{$date_signature}'");
		$this->db->order_by('start_date DESC');
		$this->db->limit(1);
		$row = $this->db->get()->result_array();
		$arr_data['signature'] = @$row[0];
		$this->load->view('coop_buy/coop_buy_pdf',$arr_data);
	}
}
