<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ajax extends CI_Controller {
	function __construct()
	{
		parent::__construct();
		$this->load->model("Interest_modal", "Interest_modal");
		$this->load->model("Return_model", 'Return');
	}
	public function index()
	{
		exit;
	}
	public function search_member()
	{
		$where = "
			(member_id LIKE '%".$this->input->post('search')."%'
			OR firstname_th LIKE '%".$this->input->post('search')."%'
			OR lastname_th LIKE '%".$this->input->post('search')."%')
			AND member_status = '1'
		";
		$this->db->select('*');
		$this->db->from('coop_mem_apply');
		$this->db->where($where);
		$row = $this->db->get()->result_array();
		$arr_data['data'] = $row;

		$this->load->view('ajax/search_member',$arr_data);
	}
	public function search_member_jquery()
	{
		$where = "
			(member_id LIKE '%".$this->input->post('search')."%'
			OR firstname_th LIKE '%".$this->input->post('search')."%'
			OR lastname_th LIKE '%".$this->input->post('search')."%')
			AND member_status = '1'
		";
		if(@$_POST['member_id_not_allow']!=''){
			$where .= " AND member_id != '".$_POST['member_id_not_allow']."' ";
		}
		$this->db->select('*');
		$this->db->from('coop_mem_apply');
		$this->db->where($where);
		$row = $this->db->get()->result_array();
		$arr_data['data'] = $row;

		$this->load->view('ajax/search_member_jquery',$arr_data);
	}
	public function get_loan_data(){
		$member_id = isset($_POST['member_id']) ? trim($_POST['member_id']) : "";
		//$member_id = sprintf("%06d",$member_id);
		$member_id = $this->center_function->complete_member_id($member_id);

		$where = ' 1=1 ';
		if(@$_POST['loan_id']!=''){
			$where .= " AND coop_loan.id = '".$_POST['loan_id']."' ";
		}
		if(@$_POST['contract_number']!=''){
			$where .= " AND contract_number = '".$_POST['contract_number']."' ";
		}
				$this->db->select(array('*',
					'coop_loan.id',
					'coop_loan.createdatetime',
					'coop_loan_name.loan_name as loan_type',
					'coop_loan_transfer.id as transfer_id',
					'coop_loan_transfer.account_id',
					'coop_loan_transfer.file_name',
					'coop_maco_account.account_name',
					'transfer_user.user_name'));
				$this->db->from("coop_loan");
				$this->db->join("coop_loan_name", "coop_loan.loan_type = coop_loan_name.loan_name_id", "inner");
				$this->db->join("coop_loan_transfer", "coop_loan.id = coop_loan_transfer.loan_id AND transfer_status <> '2'", "left");
				$this->db->join("coop_maco_account", "coop_loan_transfer.account_id = coop_maco_account.account_id", "left");
				$this->db->join("coop_user as transfer_user", "transfer_user.user_id = coop_loan_transfer.admin_id", "left");
				$this->db->where($where);
				$row = $this->db->get()->result_array();
				$row1 = @$row[0];

				if($row1['id']!=''){
				foreach($row1 as $key => $value){
					if($key == 'date_period_1' || $key == 'date_period_2' || $key == 'createdatetime' || $key == 'date_transfer'){
					$value = $this->center_function->mydate2date($value,true);
					}
					if($key == 'loan_amount' || $key == 'salary'){
						$value = number_format($value);
					}
					$data['coop_loan'][$key] = $value;
				}

				$loan_id = $row1['id'];
				$this->db->select(array(
					'*'
				));
				$this->db->from('coop_loan_guarantee');
				$this->db->where("loan_id = '".$loan_id."'");
				$rs2 = $this->db->get()->result_array();
				$i=0;
				foreach($rs2 as $key2 => $row2){
					foreach($row2 as $key => $value){
						if($key == 'amount' || $key == 'price' || $key == 'other_price'){
							$value = number_format($value);
						}
						$data['coop_loan_guarantee'][$i][$key] = $value;
					}
					$i++;
				}

				$this->db->select(array(
					'*',
					'coop_mem_group.mem_group_name'
				));
				$this->db->from('coop_loan_guarantee_person');
				$this->db->join('coop_mem_apply','coop_loan_guarantee_person.guarantee_person_id = coop_mem_apply.member_id','inner');
				$this->db->join('coop_mem_group','coop_mem_apply.level = coop_mem_group.id','left');
				$this->db->where("loan_id = '".$loan_id."'");
				$rs3 = $this->db->get()->result_array();
				$a = 0;
				foreach($rs3 as $key => $row3){
					$data['coop_loan_guarantee_person'][$a] = $row3;
					$this->db->select(array(
						'*'
					));
					$this->db->from('coop_loan_guarantee_person as t1');
					$this->db->join('coop_loan as t2','t1.loan_id = t2.id ','inner');
					$this->db->where("
						t1.guarantee_person_id = '".$row3['member_id']."'
						AND t2.loan_status = '1'
					");
					$rs_count_guarantee = $this->db->get()->result_array();
					$count_guarantee=0;
					foreach($rs_count_guarantee as $key2 => $row_count_guarantee){
						$count_guarantee++;
					}
					$data['coop_loan_guarantee_person'][$a]['count_guarantee'] = $count_guarantee;
					$a++;
				}
				if(!empty($data['coop_loan_guarantee_person'])){
					foreach($data['coop_loan_guarantee_person'] as $key => $value){
							$data['coop_loan_guarantee_person'][$key]['guarantee_person_amount'] = number_format($data['coop_loan_guarantee_person'][$key]['guarantee_person_amount']);
					}
				}

				$this->db->select(array(
					'*'
				));
				$this->db->from('coop_loan_period');
				$this->db->where("
					loan_id = '".$loan_id."'
				");
				$rs4 = $this->db->get()->result_array();
				foreach($rs4 as $key => $row4){
					$data['coop_loan_period'][] = $row4;
				}

				$this->db->select(array(
					'*'
				));
				$this->db->from('coop_loan_file_attach');
				$this->db->where("
					loan_id = '".$loan_id."'
				");
				$rs5 = $this->db->get()->result_array();
				foreach($rs5 as $key => $row5){
					$data['coop_loan_file_attach'][] = $row5;
				}

				$this->db->select(array(
					'*'
				));
				$this->db->from('coop_mem_apply');
				$this->db->where("
					member_id = '".$row1['member_id']."'
				");
				$row6 = $this->db->get()->result_array();
				$data['coop_mem_apply'] = $row6[0];
				//echo"<pre>";print_r($data);echo"</pre>";
				echo json_encode($data);
				}else{
					echo 'not_found';
				}
		exit;
	}
	function get_account_list(){
		$arr_data = array();

		$this->db->select(array(
			'*'
		));
		$this->db->from('coop_maco_account');
		$this->db->where("
			mem_id = '".$_POST['member_id']."'
			AND t2.account_status = '0'
		");
		$rs = $this->db->get()->result_array();
		$arr_data['rs'] = $rs;

		$this->load->view('ajax/get_account_list',$arr_data);
	}

	function get_member(){
		$where = '';
		if(@$_POST['member_id'] != ''){
			$member_id = isset($_POST['member_id']) ? trim($_POST['member_id']) : "";
			//$member_id = sprintf("%06d",$member_id);
			$member_id = $this->center_function->complete_member_id($member_id);
			$where .= " AND member_id = '".$member_id."' ";
		}
		if(@$_POST['id'] != ''){
			$where .= " AND coop_mem_apply.id = '".$_POST['id']."' ";
		}

		$this->db->select(array(
			'coop_mem_apply.*',
			'coop_mem_group.mem_group_name',
			'coop_prename.prename_full'
		));
		$this->db->from('coop_mem_apply');
		$this->db->join('coop_mem_group','coop_mem_apply.mem_group_id = coop_mem_group.mem_group_id','left');
		$this->db->join('coop_prename','coop_prename.prename_id = coop_mem_apply.prename_id','left');
		$this->db->where("
			1=1 ".$where."
		");
		$row = $this->db->get()->result_array();
		$row = @$row[0];

		$data = array();
		$data = $row;
		$data['member_id'] = $row['member_id'];
		$data['member_name'] = $row['prename_full'].$row['firstname_th']." ".$row['lastname_th'];
		$data['member_group_name'] = $row['mem_group_name'];
		$data['account_list_transfer'] = array();
		$this->db->select(array("CONCAT('1|', '', '|', '', '|', account_id) AS id", "CONCAT(account_id, '  ',account_name) AS text"));
		$maco_account = $this->db->get_where("coop_maco_account", array("mem_id" =>  $row['member_id']))->result_array();

		$this->db->select(array("CONCAT('2|', dividend_bank_id, '|', dividend_bank_branch_id, '|', dividend_acc_num) AS id", "CONCAT('|', dividend_acc_num, '  ', coop_bank.bank_name, ' ', coop_bank_branch.branch_name) AS text"));
		$this->db->join("coop_bank", "coop_bank.bank_id = coop_mem_bank_account.dividend_bank_id");
		$this->db->join("coop_bank_branch", "coop_mem_bank_account.dividend_bank_branch_id = coop_bank_branch.branch_code AND coop_mem_bank_account.dividend_bank_id = coop_bank_branch.bank_id", "LEFT");
		$data_mem_bank_account = $this->db->get_where("coop_mem_bank_account", array("member_id" => $row['member_id']) )->result_array();
		if($maco_account){
			foreach ($maco_account as $key => $value) {
				array_push($data['account_list_transfer'], $value);
			}
		}

		if($data_mem_bank_account){
			foreach ($data_mem_bank_account as $key => $value) {
				array_push($data['account_list_transfer'], $value);
			}
		}



		if(@$_POST['for_loan']=='1'){
			$data = array();
			/*$this->db->select(array(
				'num_guarantee'
			));
			$this->db->from('coop_term_of_loan');
			$this->db->where("
				type_id = '".$_POST['loan_type']."'
				AND start_date <= '".date('Y-m-d')."'
			");
			$this->db->order_by('start_date DESC');
			$this->db->limit(1);
			$row_term_of_loan = $this->db->get()->result_array();
			$row_term_of_loan = @$row_term_of_loan[0];*/
			$not_condition  = explode(",", $_POST['not_condition']);
			// var_dump($not_condition);exit;
			$value_check 	= explode(",", $_POST['value_check']);
			// $return_text_garantor = "เงื่อนไขการค้ำของสมาชิกไม่ถูกต้อง";
			if(isset($_POST['condition_garantor_id'])){
				$col_id = $_POST['condition_garantor_id'];
				if(sizeof($not_condition)>=1){
					$this->db->where_not_in("coop_condition_of_loan_sub_guarantor.id", $not_condition);
				}

				// $this->db->where("coop_condition_of_loan_sub_guarantor.meta_condition_id = ");
				$this->db->select(array(
					"*",
					"coop_condition_of_loan_sub_guarantor.id as conn_garantor_id",
					"coop_meta_condition.detail_text as meta_detail_text"
				));
				$this->db->join("coop_meta_condition", "coop_meta_condition.id = coop_condition_of_loan_sub_guarantor.meta_condition_id");
				$condition = $this->db->get_where("coop_condition_of_loan_sub_guarantor", array(
					"col_id" => $col_id
				))->result_array();
				// echo $this->db->last_query();
				$result_list = [];
				$last_conn = "";
				foreach ($condition as $key => $value) {
					// var_dump($value);
					$return_text_garantor = $value['meta_detail_text']." ".$value['operation']." ".$value['value'];
					if($value['id']=="0"){
						$result = $value["conn_garantor_id"];
						$return_text_garantor = "";
						break;
					}

					$fieldname  	= $value['fieldname'];
					$operation 		= $value['operation'];
					$val 			= $value['value'];


					if($fieldname!=""){
						$sql = $fieldname;
						$rs_check = $this->db->query($sql, $value_check)->result_array()[0]['value'];
					}else{
						$result = $value["conn_garantor_id"];
						$return_text_garantor = "";
						break;
					}

					// echo "<br>".$rs_check." ".$operation." ".$value."<br>";
					if( $this->center_function->operator($rs_check, $val, $operation) ){
						$return_text_garantor = "";
						$result = $value["conn_garantor_id"];
						break;
					}else{
						$result_list[] = false;
					}


				}
			}
			// echo $return_text_garantor;
			// var_dump($result_list);
			// echo "<hr>";
			$this->db->select(array(
				'guarantee_count'
			));
			$this->db->from('coop_guarantee_setting');
			$this->db->where("
				salary_start <= '".$row['salary']."' AND salary_end >= '".$row['salary']."'
			");
			$this->db->limit(1);
			$row_term_of_loan = $this->db->get()->result_array();
			$row_term_of_loan = @$row_term_of_loan[0];
			$guarantee_count = $row_term_of_loan['guarantee_count'];
			$this->db->select(array(
				'*'
			));
			$this->db->from('coop_loan_guarantee_person as t1');
			$this->db->join('coop_loan as t2','t1.loan_id = t2.id ','inner');
			$this->db->where("
				t1.guarantee_person_id = '".$row['member_id']."'
				AND t2.loan_status IN('1','2') AND t2.loan_amount_balance > 0
			");
			$rs_count_guarantee = $this->db->get()->result_array();
			$i=0;
			foreach($rs_count_guarantee as $key => $row_count_guarantee){
				$i++;
			}
			$data['check_id'] = $result;
			$data['message']['title'] = "ไม่สามารถใช้สมาชิกท่านนี้ค้ำประกันได้ เนื่องจาก";
			if($i>=$guarantee_count && $guarantee_count > 0){
				// echo 'over_guarantee';
				// exit;
				$data['message']['text'] = "สมาชิกที่เลือกได้ค้ำประกันครบกำหนดแล้ว";


			}else{
				// echo $i;
				// exit;
				if($return_text_garantor!=""){
					$data['message']['text'] = $return_text_garantor;
			}else{
					$data['message']['text'] = "";
					$data['garantee_amount'] = $i;
			}

			}


		}
		echo json_encode($data);
		exit;
	}

	function ajax_get_guarantee_person_data(){
        $member_id = $this->input->post('member_id');
		$this->db->select(array(
		    '(select loan_name from coop_loan_name where loan_name_id = t2.loan_type) as loan_name',
			't2.contract_number',
			't2.member_id',
			't4.prename_short',
			't3.firstname_th',
			't3.lastname_th',
			't2.loan_amount',
			'(select loan_amount_balance from coop_loan where id = t1.loan_id) as guarantee_person_amount_balance'
		));
		$this->db->from('coop_loan_guarantee_person as t1');
		$this->db->join('coop_loan as t2','t1.loan_id = t2.id','inner');
		$this->db->join('coop_mem_apply as t3','t2.member_id = t3.member_id','inner');
		$this->db->join('coop_prename as t4','t3.prename_id = t4.prename_id','left');
		$this->db->where("t1.guarantee_person_id = '".$member_id."' AND t2.loan_status = '1'");
		$rs = $this->db->get()->result_array();
		$arr_data['rs'] = @$rs;

		$this->load->view('ajax/ajax_get_guarantee_person_data',$arr_data);
	}

	public function search_account()
	{
		$this->load->model("Receipt_model", "receipt_model");	
		$arr_data = array();
		$search_text = @$_POST["search_text"];
		$search_list = @$_POST["search_list"];
		$where = "";
		if(@$_POST['search_list'] == 'member_id'){
			//$member_id = sprintf("%06d", @$search_text);
			$where = " AND t1.mem_id LIKE '%".$search_text."%'";
		}else if(@$_POST['search_list'] == 'firstname_th'){
			$where = " AND t3.firstname_th LIKE '%".$search_text."%'";
		}else if(@$_POST['search_list'] == 'lastname_th'){
			$where = " AND t3.lastname_th LIKE '%".$search_text."%'";
		}else if(@$_POST['search_list'] == 'id_card'){
			$where = " AND t3.id_card LIKE '%".$search_text."%'";
		}else if(@$_POST['search_list'] == 'account_id'){
			$where = " AND (concat(t2.type_prefix,lpad(right(t1.account_id, 6), 6, 0)) LIKE '%".$search_text."%' OR t1.account_id LIKE '%{$search_text}%') ";
		}else if(@$_POST['search_list'] == 'employee_id'){
			$where = " AND t3.employee_id LIKE '%".$search_text."%'";
		}else{
			//$where = " AND t1.account_id LIKE '%".$search_text."%' OR t1.mem_id LIKE '%".$search_text."%' OR t1.account_name LIKE '%".$search_text."%'";
		}

		$this->db->select(array('t1.*','t2.type_code'));
		$this->db->from('coop_maco_account as t1');
		$this->db->join('coop_deposit_type_setting as t2','t1.type_id = t2.type_id','inner');
		$this->db->join('coop_mem_apply as t3','t1.mem_id = t3.member_id','left');
		$this->db->where("1=1 {$where}");
		$this->db->order_by("t1.account_status ASC,t1.created DESC");
		$rs = $this->db->get()->result_array();
		$arr_data['rs'] = @$rs;
		//echo $this->db->last_query();
		
		$account_ids = array_column($rs, 'account_id');
		$get_account_receipt_refund_id = $this->receipt_model->get_account_receipt_refund_id($account_ids);
		$arr_data['arr_receipt_refund'] = @$get_account_receipt_refund_id;

		$this->load->view('ajax/search_account',$arr_data);
	}

	public function search_member_by_type()
	{
		$search_text = @$_POST["search_text"];
		$search_list = @$_POST["search_list"];
		$where = "";
		if(@$_POST['search_list'] == 'member_id'){
			$where = " member_id LIKE '%".$search_text."%'";
		}else if(@$_POST['search_list'] == 'firstname_th'){
			$where = " firstname_th LIKE '%".$search_text."%'";
		}else if(@$_POST['search_list'] == 'lastname_th'){
			$where = " lastname_th LIKE '%".$search_text."%'";
		}else if(@$_POST['search_list'] == 'id_card'){
			$where = " id_card LIKE '%".$search_text."%'";
		}else if(@$_POST['search_list'] == 'employee_id'){
			$where = " employee_id LIKE '%".$search_text."%'";
		}
		$where .= "AND member_status <> '3'";
		$this->db->select('*');
		$this->db->from('coop_mem_apply');
		$this->db->where($where);
		$row = $this->db->get()->result_array();
		$arr_data['data'] = $row;
		$this->load->view('ajax/search_member',$arr_data);
	}
	public function search_member_by_type_jquery()
	{
		$search_text = @$_POST["search_text"];
		$search_list = @$_POST["search_list"];
		$where = "";
		if(@$_POST['search_list'] == 'member_id'){
			$where = " member_id LIKE '%".$search_text."%'";
		}else if(@$_POST['search_list'] == 'firstname_th'){
			$where = " firstname_th LIKE '%".$search_text."%'";
		}else if(@$_POST['search_list'] == 'lastname_th'){
			$where = " lastname_th LIKE '%".$search_text."%'";
		}else if(@$_POST['search_list'] == 'id_card'){
			$where = " id_card LIKE '%".$search_text."%'";
		}
		$where .= "AND member_status <> '3'";
		$this->db->select('*');
		$this->db->from('coop_mem_apply');
		$this->db->where($where);
		$row = $this->db->get()->result_array();
		$arr_data['data'] = $row;

		$this->load->view('ajax/search_member_jquery',$arr_data);
	}

	public function search_loan_repayment()
	{
		$loan_type = 17;
		$search_text = trim(@$_POST["search_text"]);
		$search_list = trim(@$_POST["search_list"]);
		$where = "";
		if(@$_POST['search_list'] == 'member_id'){
			$where = " coop_mem_apply.member_id LIKE '%".$search_text."%'";
		}else if(@$_POST['search_list'] == 'firstname_th'){
			$where = " coop_mem_apply.firstname_th LIKE '%".$search_text."%'";
		}else if(@$_POST['search_list'] == 'lastname_th'){
			$where = " coop_mem_apply.lastname_th LIKE '%".$search_text."%'";
		}else if(@$_POST['search_list'] == 'id_card'){
			$where = " coop_mem_apply.id_card LIKE '%".$search_text."%'";
		}else if(@$_POST['search_list'] == 'loan_prefix'){
			$where = " contract_number LIKE '%".$search_text."%'";
		}
		$where .= "AND member_status = 1 AND loan_type = {$loan_type}";
		$this->db->select("*, coop_loan.id as l_id, TIMESTAMPDIFF(YEAR, birthday, CURDATE()) AS age");
		$this->db->from('coop_loan');
		$this->db->join("coop_mem_apply", "coop_mem_apply.member_id = coop_loan.member_id");
		$this->db->where($where);
		$row = $this->db->get()->result_array();
		$arr_data['data'] = $row;

		$this->load->view('ajax/search_loan_repayment_jquery',$arr_data);
	}

	public function search_loan_repayment_by_contract_number()
	{
		$loan_type = 17;
		$search_text = trim(@$_POST["search_text"]);
		$where = " contract_number = '".$search_text."'";
		$where .= "AND member_status = 1 AND loan_type = {$loan_type}";
		$this->db->select("*, coop_loan.id as l_id, TIMESTAMPDIFF(YEAR, birthday, CURDATE()) AS age");
		$this->db->from('coop_loan');
		$this->db->join("coop_mem_apply", "coop_mem_apply.member_id = coop_loan.member_id");
		$this->db->where($where);
		$this->db->limit(1);
		$row = $this->db->get()->result_array();
		if($row){
			echo json_encode($row[0]);
		}else{
			echo "FALSE";
		}
		// $arr_data['data'] = $row;

		// $this->load->view('ajax/search_loan_repayment_jquery',$arr_data);
	}

	public function get_coop_maco(){
		$member_id = trim(@$_POST["member_id"]);

		$query = $this->db->get_where("coop_maco_account", array(
			"mem_id" => $member_id
		));

		if($query->result()){
			foreach ($query->result() as $key => $value) {
				echo "<option value='".$value->account_id."'>".$value->account_name." ".$value->account_id."</option>";
			}
		}else{
			echo "<option value=''>ไม่พบข้อมูล</option>";
		}
	}

	public function get_bank_branch(){
		$bank_id = trim(@$_POST["bank"]);
		$query = $this->db->get_where("coop_bank_branch", array(
			"bank_id" => $bank_id
		));
		if($query->result()){
			foreach ($query->result() as $key => $value) {
				echo "<option value='".$value->branch_code."'>".$value->branch_name."</option>";
			}
		}else{
			echo "<option value=''>ไม่พบข้อมูล</option>";
		}
	}

	public function get_loan_repayment(){
		$loan_id = trim(@$_POST["loan_id"]);
		$this->db->join("coop_bank", "coop_bank.bank_id = coop_loan_repayment.bank_id", "LEFT");
		$this->db->join("coop_user", "coop_user.user_id = coop_loan_repayment.admin_id", "LEFT");

		$this->db->order_by("seq", "desc");
		$query = $this->db->get_where("coop_loan_repayment", array(
			"loan_id" => $loan_id
		));
		$data['loan_repayment'] = $query->result();
		$this->load->view("ajax/loan_repayment_table", $data);
	}

	public function search_account_no(){
		$search = trim(@$_POST["search"]);
		$this->db->where("account_id", $search);
		$query = $this->db->get("coop_maco_account");
		$result = $query->result();
		echo json_encode(count($result));
	}

	public function search_member_by_type_to_input()
	{
		$search_text = @$_POST["search_text"];
		$search_list = @$_POST["search_list"];
		$where = "";
		if(@$_POST['search_list'] == 'member_id'){
			$where = " member_id LIKE '%".$search_text."%'";
		}else if(@$_POST['search_list'] == 'firstname_th'){
			$where = " firstname_th LIKE '%".$search_text."%'";
		}else if(@$_POST['search_list'] == 'lastname_th'){
			$where = " lastname_th LIKE '%".$search_text."%'";
		}else if(@$_POST['search_list'] == 'id_card'){
			$where = " id_card LIKE '%".$search_text."%'";
		}
		$where .= "AND member_status <> '3'";
		$this->db->select('*');
		$this->db->from('coop_mem_apply');
		$this->db->where($where);
		$row = $this->db->get()->result_array();
		$arr_data['data'] = $row;

		$this->load->view('ajax/search_member_to_input',$arr_data);
	}

	public function get_branch_json(){
		if(@$_GET['id']!=''){
			$this->db->where("branch_code" , @$_GET['id']);
			$result = $this->db->get_where("coop_bank_branch", array() )->result_array();
		}else if(@$_GET['bank_id']!=''){
			$search = $_GET['q'];
			$this->db->select(array("branch_code as id", "branch_name as text"));
			$this->db->where("bank_id" , @$_GET['bank_id']);

			$this->db->like("branch_name", $search);
			$result = $this->db->get_where("coop_bank_branch", array() )->result_array();
		}
		header('Content-Type: application/json');
		echo json_encode(array("items" => $result, "incomplete_results" => true, "total_count" => count($result)));
	}


	public function search_loan_by_type()
	{
		$search_text = trim(@$_POST["search_text"]);
		// $where = " contract_number = '".$search_text."'";
		if(@$_POST['search_list'] == 'member_id'){
			$where = " coop_loan.member_id LIKE '%".$search_text."%'";
		}else if(@$_POST['search_list'] == 'firstname_th'){
			$where = " firstname_th LIKE '%".$search_text."%'";
		}else if(@$_POST['search_list'] == 'lastname_th'){
			$where = " lastname_th LIKE '%".$search_text."%'";
		}else if(@$_POST['search_list'] == 'id_card'){
			$where = " id_card LIKE '%".$search_text."%'";
		}else if(@$_POST['search_list'] == 'contract_number'){
			$where = " contract_number LIKE '".$search_text."'";
		}

		//เพิ่มการค้นหาประเภทเงินกู้
		if(isset($_POST['loan_type'])){
		    $where .= " AND coop_loan.loan_type = '{$_POST['loan_type']}' ";
        }

		$this->db->select("*, coop_loan.id as l_id, IF(coop_prename.prename_short is not null, coop_prename.prename_short, '') as prename_short,  coop_term_of_loan.type_name, coop_loan.id as loan_id");
		$this->db->from('coop_loan');
		$this->db->join("coop_mem_apply", "coop_mem_apply.member_id = coop_loan.member_id");
		$this->db->join("coop_prename", "coop_prename.prename_id = coop_mem_apply.prename_id", "LEFT");
		$this->db->join("coop_term_of_loan", "coop_term_of_loan.type_id = coop_loan.loan_type");
		$this->db->where($where);
		$this->db->group_by("coop_loan.id");
		$this->db->group_by("coop_loan.member_id");
		$this->db->limit(100);
		$row_data = $this->db->get()->result_array();
		if(count($row_data)>=1){
			foreach ($row_data as $key => $row) {
				$row_data[$key]['loan_amount'] = number_format($row['loan_amount'], 2);
				$row_data[$key]['loan_amount_balance'] = number_format($row['loan_amount_balance'], 2);
				$row_data[$key]['date_start_period'] = $this->center_function->ConvertToThaiDate(@$row['date_start_period']);

				$this->db->select(array("coop_loan_guarantee_person.*", "CONCAT(coop_mem_apply.firstname_th, ' ', coop_mem_apply.lastname_th) AS fullname", "FORMAT(guarantee_person_amount, 2) as guarantee_person_amount"));
				$this->db->join("coop_mem_apply", "coop_mem_apply.member_id = coop_loan_guarantee_person.guarantee_person_id");
				$this->db->where("guarantee_person_id != ''");
				$row_data[$key]['guarantor'] = $this->db->get_where("coop_loan_guarantee_person", array("loan_id" => $row['l_id']) )->result_array();

				$this->db->select(array("coop_loan_guarantee_person_history.*", "coop_user.user_name"));
				$this->db->order_by("seq_no", "DESC");
				$this->db->limit(1);
				$this->db->join("coop_user", "coop_user.user_id = coop_loan_guarantee_person_history.admin_id");
				$tmp_history = $this->db->get_where("coop_loan_guarantee_person_history", array("loan_id" => $row['l_id']))->result()[0];
				if($tmp_history!=""){
					$tmp_history->create_date = $this->center_function->ConvertToThaiDate($tmp_history->create_date);
				}
				$row_data[$key]['latest'] = $tmp_history;
			}

            $row_data['status'] = 'success';
			echo json_encode($row_data);
		}else{
			//echo "FALSE ";
            $row_data['status'] = 'empty';
            echo json_encode($row_data);
		}
	}

	public function search_member_json(){
		$where = "
			(member_id LIKE '".$this->input->post('search')."'
			AND member_status = '1')
		";
		$this->db->select(array("CONCAT(firstname_th, ' ', lastname_th) as fullname", "member_id"));
		$this->db->from('coop_mem_apply');
		$this->db->where($where);
		$row = $this->db->get()->result_array()[0];
		if(empty($row)){
			echo "FALSE";
		}else{
			echo json_encode($row);
		}

	}

	public function get_current_loan_installment(){
		$loan_id = $this->input->post("loan_id");
		if($loan_id != ""){
			$select = array("coop_loan.money_per_period", "coop_loan.period_amount", "coop_loan.pay_type", "coop_loan.id as loan_id", "coop_user.user_name", "c.create_date", "c.seq_no");
			$this->db->select($select);
			$this->db->join("(SELECT *, MAX(seq_no) FROM coop_loan_period_history WHERE loan_id = {$loan_id}) AS c", "coop_loan.id = c.loan_id", "LEFT");
			$this->db->join("coop_user", "coop_user.user_id = c.admin_id", "LEFT");
			$loan = $this->db->get_where("coop_loan", array("coop_loan.id" => $loan_id))->result_array()[0];
			$loan['money_per_period'] = number_format($loan['money_per_period']);
			$loan['create_date'] = $this->center_function->ConvertToThaiDate($loan['create_date']);
			$loan['installment'] = $this->db->get_where("coop_loan_period", array("loan_id" => $loan_id ))->result_array();
		}
		echo json_encode(@$loan);
	}

	public function process_return() {
		$json = [];
		$date_s = $this->center_function->ConvertToSQLDate($_POST['date_s']);
		$date_e = $this->center_function->ConvertToSQLDate($_POST['date_e']);

		/************************
		 * Type 1
		*/
		$tmp = $this->cal_process_return_type_1($date_s, $date_e);
		$total = 0;
		$return = 0;
		$surcharge = 0;
		if($tmp['return']) {
			foreach( $tmp['return'] as $key => $row) {
				if( ($row['return_principal'] + $row['return_interest']) ) {
					$total++;
					if( $row['is_return'] == 1 ) { $return++; }
				}
				if( $row['surcharge'] ) { $surcharge++; }
			}
		}

		if($tmp['no_return']) {
			foreach( $tmp['no_return'] as $key => $row) {
				if( ($row['return_principal'] + $row['return_interest']) ) {
					$total++;
					if( $row['is_return'] == 1 ) { $return++; }
				}
				if( $row['surcharge'] ) { $surcharge++; }
			}
		}
		if($tmp['surcharge']) {
			foreach( $tmp['surcharge'] as $key => $row) {
				if( ($row['return_principal'] + $row['return_interest']) ) {
					$total++;
					if( $row['is_return'] == 1 ) { $return++; }
				}
				if( $row['surcharge'] ) { $surcharge++; }
			}
		}

		/*$json['data'][] = [
			'type' => 1,
			'title' => 'คืนเงินผ่านรายการเรียกเก็บ',
			'total' => $total,
			'no_return' => 0,
			'return' => $return,
			'surcharge' => $surcharge,
			'remain' => ($total - $return)
		]; // รายเดือน
		*/
		/************************
		 * Type 2
		*/

		$tmp = $this->cal_process_return_type_2($date_s, $date_e);
		$num_return = 0;
		if( $tmp['return'] ) {
			foreach( $tmp['return'] as $key => $row) {
				if( $row['is_return'] ) $num_return++;
			}
		}

		$total = count($tmp['return']) + count($tmp['no_return']);
		$json['data'][] = [
			'type' => 2,
			'title' => 'คืนเงินหักกลบ',
			'total' => $total,
			'no_return' => count($tmp['no_return']),
			'return' => $num_return,
			'surcharge' => 0,
			'remain' => count($tmp['return']) - $num_return
		]; // หักกลบ
		/*************************
		 *  Type 3
		 */
		/*$sql = "SELECT COUNT(receipt_id) as num_total,
		IFNULL(SUM( CASE WHEN ret_id IS NULL THEN 0 ELSE 1 END), 0) num_return
		FROM (

		SELECT
			t2.receipt_id,
		 t2.member_id,
		 t2.loan_atm_id,
		 t2.principal_payment,
		 t2.interest,
		 t2.total_amount,
		t2.loan_amount_balance,
		 t1.receipt_datetime
		, t4.payment_date
		, DATEDIFF( DATE(t1.receipt_datetime), t4.payment_date ) _payment_diff_
		, tb6.ret_id
		FROM coop_receipt AS t1
		INNER JOIN coop_finance_transaction AS t2 ON t1.receipt_id = t2.receipt_id
		LEFT JOIN coop_finance_month_profile AS t3 ON t1.month_receipt = t3.profile_month AND t1.year_receipt = t3.profile_year
		LEFT OUTER JOIN (

			SELECT
			tb1.loan_atm_id,
			tb1.transaction_datetime,
			DATE(tb1.transaction_datetime) AS payment_date,
			tb1.receipt_id,
			tb2.loan_description,
			SUM( IF ( tb2.loan_amount <> '', tb2.loan_amount, tb3.principal_payment ) ) AS principal,
			SUM(tb3.interest) AS interest,
			SUM(tb3.total_amount) AS total_amount,
			IF (
			tb2.loan_description != '',
			tb2.loan_description,

			IF (
			tb4.finance_month_profile_id != '',
			'ชำระเงินรายเดือน',
			'ชำระเงินอื่นๆ'
			)
			) AS data_text
			, tb1.loan_amount_balance
			FROM
			coop_loan_atm_transaction AS tb1
			LEFT JOIN coop_loan_atm_detail AS tb2 ON tb1.loan_atm_id = tb2.loan_atm_id
			AND tb1.transaction_datetime = tb2.loan_date
			LEFT JOIN coop_finance_transaction AS tb3 ON tb1.receipt_id = tb3.receipt_id
			AND tb1.loan_atm_id = tb3.loan_atm_id
			LEFT JOIN coop_receipt AS tb4 ON tb3.receipt_id = tb4.receipt_id
			LEFT JOIN coop_receipt AS t6 ON tb1.receipt_id = t6.receipt_id
			WHERE DATE(tb1.transaction_datetime) BETWEEN '{$date_s}' AND '{$date_e}'
			AND tb4.finance_month_profile_id <> ''
			GROUP BY tb1.loan_atm_id, tb1.transaction_datetime
			ORDER BY tb1.loan_atm_id

		) t4 ON t2.loan_atm_id = t4.loan_atm_id

		LEFT OUTER JOIN (SELECT * FROM coop_process_return WHERE return_type = 3) tb6 ON t2.member_id = tb6.member_id
			AND t2.loan_atm_id = tb6.loan_atm_id
			AND MONTH(t4.payment_date) = tb6.return_month
			AND YEAR(t4.payment_date) = tb6.return_year

		WHERE t1.finance_month_profile_id IS NULL
		AND t2.loan_atm_id <> ''
		AND DATE(t1.receipt_datetime) BETWEEN '{$date_s}' AND '{$date_e}'
		AND t2.loan_amount_balance = 0
		AND t4.payment_date Is NOT NULL
		AND DATEDIFF( DATE(t1.receipt_datetime), t4.payment_date ) <= 0
		) tmp";
		$rs_3 = $this->db->query($sql);
		$row_3 = $rs_3->result_array()[0];
		$json['data'][] = [
			'type' => 3,
			'title' => 'คืนเงิน ฉATM',
			'total' => $row_3['num_total'],
			'no_return' => 0,
			'return' => $row_3['num_return'],
			'surcharge' => 0,
			'remain' => ($row_3['num_total'] - $row_3['num_return'])
		]; // ฉATM
		*/
		// coop_process_return_store
		/*************************
		*  Type 4
		*/
		/*$day = substr($date_s, 8, 10);
		$sql = "SELECT
		(select count(*) from coop_process_return_store as s1 where s1.return_status=0 and STR_TO_DATE(concat(s1.return_year,'-',s1.return_month,'-','{$day}'), '%Y-%m-%d') BETWEEN '{$date_s}' and '{$date_e}') as num_unreturn,
		(select count(*) from coop_process_return_store as s2 where s2.return_status=1 and STR_TO_DATE(concat(s2.return_year,'-',s2.return_month,'-','{$day}'), '%Y-%m-%d') BETWEEN '{$date_s}' and '{$date_e}') as num_return";
		$row_4 = $this->db->query($sql)->result_array()[0];
		$json['data'][] = [
			'type' => 4,
			'title' => 'คืนเงิน ATM หลังผ่านรายการ',
			'total' => $row_4['num_unreturn']+$row_4['num_return'],
			'no_return' => 0,
			'return' => $row_4['num_return'],
			'remain' => ($row_4['num_unreturn'])
		];
		*/
		//---------

		// Process return for resign member
		/*************************
		*  Type 5
		*/		
		/*$sql = "SELECT t5.* FROM (
					SELECT t1.member_id,t3.approve_date as resign_date,t2.receipt_id,t3.finance_month_profile_id
					FROM coop_mem_apply as t1
					LEFT OUTER JOIN (
						SELECT t2.member_id,t2.approve_date,t1.receipt_id,t1.finance_month_profile_id FROM coop_receipt AS t1 
						INNER JOIN coop_mem_req_resign AS t2 ON t1.member_id = t2.member_id 	AND t1.receipt_datetime >=DATE(t2.approve_date)
						GROUP BY t2.member_id
						ORDER BY t1.receipt_datetime DESC
					)AS t3 ON t1.member_id = t3.member_id
					LEFT OUTER JOIN coop_finance_transaction  AS t2 ON t3.receipt_id = t2.receipt_id
					WHERE t1.mem_type in (2,5,6) AND t2.payment_date BETWEEN '".$date_s."' AND '".$date_e."' AND t2.account_list_id IN (15,16,30,31)
					GROUP BY t1.member_id
				) AS t5 WHERE t5.finance_month_profile_id IS NOT NULL
				";		
		$row_5 = $this->db->query($sql)->result_array();		
		
		$return_count = 0;
		$remain_count = 0;
		//echo $this->db->last_query(); echo '<hr>'; exit;	
		//echo '<pre>'; print_r($row_5); echo '</pre>';
		foreach($row_5 as $member) {
			$sql = "SELECT * FROM (
						SELECT share_id,share_collect_value
						FROM coop_mem_share
						WHERE member_id = '".$member["member_id"]."'
						ORDER BY share_date DESC LIMIT 1
					) AS t1 WHERE t1.share_collect_value > 0
				";
			$share = $this->db->query($sql)->row();	
			//echo $this->db->last_query(); echo ';';
			
			$sql = "SELECT id FROM coop_loan WHERE member_id = '".$member["member_id"]."' AND loan_amount_balance <0";
			$loan = $this->db->query($sql)->row();	
			//echo $this->db->last_query(); echo ';';
			
			$sql = "SELECT loan_atm_id FROM coop_loan_atm WHERE member_id = '".$member["member_id"]."' AND (total_amount_approve-total_amount_balance) <0";
			$loan_atm = $this->db->query($sql)->row();	
			//echo $this->db->last_query(); echo ';';
			
			//Account not work better re-coding
			$sql_account = "SELECT t2.account_id,t2.mem_id,t2.transaction_balance FROM (
								SELECT
									t1.account_id,
									t1.mem_id,
								 (SELECT transaction_balance FROM coop_account_transaction WHERE account_id = t1.account_id ORDER BY transaction_time DESC,transaction_id DESC LIMIT 1) AS transaction_balance
								FROM
									coop_maco_account AS t1
								WHERE
									t1.type_id = '2'
								AND t1.account_status = '1'
								AND t1.mem_id = '".$member["member_id"]."'
							) AS t2 WHERE t2.transaction_balance > 0 
				";
			$account = $this->db->query($sql_account)->row();	
			//echo $this->db->last_query(); echo ';';
			// exit;
			
			if(!empty($share) || !empty($loan) || !empty($loan_atm) || !empty($account)) {
				$remain_count++;
			} else {
				$return_count++;
			}
		}
		
		$json['data'][] = [
			'type' => 5,
			'title' => 'คืนเงินลาออก',
			'total' => count($row_5),
			'no_return' => 0,
			'return' => $return_count,
			'remain' => $remain_count
		];
		*/
		//---------

		echo json_encode($json);
	}

	public function process_return_exec() {
		$return_type = (int)$_POST['ret_type'];
		$date_s = $this->center_function->ConvertToSQLDate($_POST['date_s']);
		$date_e = $this->center_function->ConvertToSQLDate($_POST['date_e']);
		$year = date('Y') + 543 ;
		$month = explode('-', $date_s)[1];
		$sql = "SELECT bill_id
						FROM coop_process_return
						WHERE bill_id LIKE 'R{$year}{$month}%'
						ORDER BY bill_id DESC
						LIMIT 1";
		$rs = $this->db->query($sql);
		if( !$rs->num_rows() ) $_bill_id = 1;
		else {
			$row = $rs->result_array()[0];
			$_bill_id = (int)substr($row['bill_id'], 7, 5) + 1;
		}
		if( $return_type == 1 ) {

			$tmp = $this->cal_process_return_type_1($date_s, $date_e);
			$data = [ 'return' => [], 'surcharge' => [] ];
			if($tmp['return']) {
				foreach( $tmp['return'] as $key => $row) {
					if( ($row['return_principal'] + $row['return_interest']) ) {
						$data['return'][] = $row;
					}
					if( $row['surcharge'] ) {
						$data['surcharge'][] = $row;
					}
				}
			}
			if($tmp['no_return']) {
				foreach( $tmp['no_return'] as $key => $row) {
					if( ($row['return_principal'] + $row['return_interest']) ) {
						$data['return'][] = $row;
					}
					if( $row['surcharge'] ) {
						$data['surcharge'][] = $row;
					}
				}
			}
			if($tmp['surcharge']) {
				foreach( $tmp['surcharge'] as $key => $row) {
					if( ($row['return_principal'] + $row['return_interest']) ) {
						$data['return'][] = $row;
					}
					if( $row['surcharge'] ) {
						$data['surcharge'][] = $row;
					}
				}
			}


			foreach ($data['return'] as $row) {
				$sql = "SELECT ret_id
								FROM coop_process_return
								WHERE return_type = 1
									AND member_id = '{$row['member_id']}'
									AND receipt_id = '{$row['receipt_id']}'
									AND loan_id = '{$row['loan_id']}'
									AND return_year = {$row['payment_date_year']}
									AND return_month = {$row['payment_date_month']}
									";
				$rs_chk = $this->db->query($sql);
				$return_amount = $row['return_principal'] + $row['return_interest'];
				if( $rs_chk->num_rows() == 0 && $row['account_id'] && $return_amount ) {
					$sql = "SELECT transaction_balance
									FROM coop_account_transaction
									WHERE account_id = '{$row['account_id']}'
									ORDER BY transaction_time DESC, transaction_id DESC
									LIMIT 1";
					$rs_balance = $this->db->query($sql);
					$row_balance = $rs_balance->result_array()[0];
					$transaction_deposit = $return_amount;
					$transaction_balance = $return_amount + $row_balance['transaction_balance'];
					$receipt_id = $row['receipt_id'];
					$bill_id = sprintf('R%s%s%05d', $year, $month, $_bill_id++);

					/*$sql = "INSERT INTO coop_account_transaction(transaction_time, transaction_list, transaction_withdrawal, transaction_deposit, transaction_balance, account_id, user_id ,transaction_text)
									VALUES(NOW(), 'REVD', 0, {$transaction_deposit}, {$transaction_balance}, '{$row['account_id']}', '{$_SESSION['USER_ID']}', 'process_interest')";
					$this->db->query($sql);
					*/

					$sql = "INSERT INTO coop_process_return(member_id, loan_id, return_type, account_id, receipt_id, bill_id, return_principal, return_interest, return_amount, return_year, return_month, return_time, user_id)
									VALUES('{$row['member_id']}', '{$row['loan_id']}', 1, '{$row['account_id']}', '{$receipt_id}', '{$bill_id}', {$row['return_principal']}, {$row['return_interest']}, {$return_amount}, {$row['payment_date_year']}, {$row['payment_date_month']}, NOW(),'{$_SESSION['USER_ID']}')";
					$this->db->query($sql);
				}
			}

			foreach ($data['surcharge'] as $row) {
				$sql = "SELECT ret_id
								FROM coop_process_return
								WHERE return_type = 5
									AND member_id = '{$row['member_id']}'
									AND receipt_id = '{$row['receipt_id']}'
									AND loan_id = '{$row['loan_id']}'
									AND return_year = {$row['payment_date_year']}
									AND return_month = {$row['payment_date_month']}
									";
				$rs_chk = $this->db->query($sql);
				$return_amount = $row['surcharge'];
				if( $rs_chk->num_rows() == 0 && $row['account_id'] && $return_amount ) {
					$sql = "SELECT transaction_balance
									FROM coop_account_transaction
									WHERE account_id = '{$row['account_id']}'
									ORDER BY transaction_time DESC, transaction_id DESC
									LIMIT 1";
					$rs_balance = $this->db->query($sql);
					$row_balance = $rs_balance->result_array()[0];
					$transaction_withdrawal = $return_amount;
					$transaction_balance = $row_balance['transaction_balance'] - $transaction_withdrawal;
					$receipt_id = $row['receipt_id'];
					$bill_id = sprintf('I%s%s%05d', $year, $month, $_bill_id++);

					/*$sql = "INSERT INTO coop_account_transaction(transaction_time, transaction_list, transaction_withdrawal, transaction_deposit, transaction_balance, account_id, user_id ,transaction_text)
									VALUES(NOW(), 'REVD', {$transaction_withdrawal}, 0, {$transaction_balance}, '{$row['account_id']}', '{$_SESSION['USER_ID']}', 'process_interest')";
					$this->db->query($sql);
					*/


					$sql = "INSERT INTO coop_process_return(member_id, loan_id, return_type, account_id, receipt_id, bill_id, return_principal, return_interest, return_amount, return_year, return_month, return_time ,user_id)
									VALUES('{$row['member_id']}', '{$row['loan_id']}', 5, '{$row['account_id']}', '{$receipt_id}', '{$bill_id}', 0, {$row['surcharge']}, {$row['surcharge']}, {$row['payment_date_year']}, {$row['payment_date_month']}, NOW(), '{$_SESSION['USER_ID']}')";
					$this->db->query($sql);
				}
			}

		} elseif( $return_type == 2 ) {
			$tmp = $this->cal_process_return_type_2($date_s, $date_e);
			$month_process = explode('-', $date_s)[1];
			$year_process = explode('-', $date_s)[0];
			foreach( $tmp['return'] as $key => $row ) {
				$row['payment_date_year'] = $year_process;
				$row['payment_date_month'] = $month_process;
				$sql = "SELECT ret_id
								FROM coop_process_return
								WHERE return_type = 2
									AND member_id = '{$row['member_id']}'
									AND loan_id = '{$row['loan_id']}'
									AND return_year = {$row['payment_date_year']}
									AND return_month = {$row['payment_date_month']}
									AND receipt_id = '{$row['receipt_id']}'
									";
				$rs_chk = $this->db->query($sql);

				if( $rs_chk->num_rows() == 0 && $row['account_id'] && $row['return_amount'] ) {
					/*$sql = "SELECT transaction_balance
									FROM coop_account_transaction
									WHERE account_id = '{$row['account_id']}'
									ORDER BY transaction_time DESC, transaction_id DESC
									LIMIT 1";
					$rs_balance = $this->db->query($sql);
					$row_balance = $rs_balance->result_array()[0];
					$transaction_deposit = $row['return_amount'];
					$transaction_balance = $row['return_amount'] + $row_balance['transaction_balance'];
					$receipt_id = $row['receipt_id'];
					$bill_id = sprintf('R%s%s%05d', $year, $month, $_bill_id++);

					$sql = "INSERT INTO coop_account_transaction(transaction_time, transaction_list, transaction_withdrawal, transaction_deposit, transaction_balance, account_id, user_id ,transaction_text)
									VALUES(NOW(), 'REVD', 0, {$transaction_deposit}, {$transaction_balance}, '{$row['account_id']}', '{$_SESSION['USER_ID']}', 'process_interest')";
					$this->db->query($sql);
					*/

					$sql = "INSERT INTO coop_process_return(member_id, loan_id, return_type, account_id, receipt_id, bill_id, return_amount, return_year, return_month, return_time ,user_id)
									VALUES('{$row['member_id']}', '{$row['loan_id']}', 2, '{$row['account_id']}', '{$receipt_id}', '{$bill_id}', {$row['return_amount']}, {$row['payment_date_year']}, {$row['payment_date_month']}, NOW() ,'{$_SESSION['USER_ID']}')";
					$this->db->query($sql);
				}
			}


		}
			/*
			$sql = "SELECT DISTINCT tb2.member_id, tb2.receipt_id, tb2.loan_id,
						SUM(tb2.principal_payment) principal_payment, SUM(tb2.interest) interest
						, SUM(tb2.total_amount) total_amount
						, tb2.payment_date
						, DAYOFYEAR(DATE_FORMAT(NOW(), '%Y-12-31')) num_day_of_year
						, DATEDIFF(LAST_DAY(tb2.payment_date), DATE(tb2.payment_date)) num_datediff
						, CONCAT(tb3.firstname_th, ' ', tb3.lastname_th) member_name
						, tb4.account_id
						, tb5.contract_number
						, ROUND(
								SUM(tb2.principal_payment) *
								(
									(
										SELECT interest_rate
										FROM coop_term_of_loan
										WHERE start_date <= '{$date_s}'
										AND type_id = tb5.loan_type
										ORDER BY start_date DESC
										LIMIT 1
									)
									 / 100 ) *
								CAST(
									DATEDIFF(LAST_DAY(tb2.payment_date), DATE(tb2.payment_date)) /
									DAYOFYEAR(DATE_FORMAT(tb2.payment_date, '%Y-12-31'))
								AS DOUBLE )
						) return_interest
						, tb6.ret_id
						, YEAR(tb2.payment_date) payment_date_year
						, MONTH(tb2.payment_date) payment_date_month
						FROM coop_receipt AS tb1
						INNER JOIN coop_finance_transaction AS tb2 ON tb1.receipt_id = tb2.receipt_id
						INNER JOIN coop_mem_apply tb3 ON tb1.member_id = tb3.member_id
						LEFT OUTER JOIN ( SELECT * FROM coop_maco_account WHERE type_id = '2' AND account_status = '0' ) tb4 ON tb1.member_id = tb4.mem_id
						INNER JOIN coop_loan tb5 ON tb2.loan_id = tb5.id
						LEFT OUTER JOIN (SELECT * FROM coop_process_return WHERE return_type = 2) tb6 ON tb2.member_id = tb6.member_id
								AND (tb2.loan_id) = tb6.loan_id
								AND MONTH(tb2.payment_date) = tb6.return_month
								AND YEAR(tb2.payment_date) = tb6.return_year
						WHERE tb2.payment_date BETWEEN '{$date_s}' AND '{$date_e}'
								AND DATEDIFF(LAST_DAY(tb2.payment_date), DATE(tb2.payment_date)) > 0
								AND ( tb2.receipt_id NOT LIKE '%C%' OR tb2.receipt_id NOT LIKE '%c%' )
								AND tb1.finance_month_profile_id IS NULL
						GROUP BY tb2.member_id, tb2.receipt_id, tb5.contract_number
						ORDER BY tb2.member_id
						";
			$rs = $this->db->query($sql);
			foreach ($rs->result_array() as $row) {

				$sql = "SELECT ret_id
								FROM coop_process_return
								WHERE return_type = 2
									AND member_id = '{$row['member_id']}'
									AND loan_id = '{$row['loan_id']}'
									AND return_year = {$row['payment_date_year']}
									AND return_month = {$row['payment_date_month']}
									AND receipt_id = '{$row['receipt_id']}'
									";
				$rs_chk = $this->db->query($sql);

				if( $rs_chk->num_rows() == 0 && $row['account_id'] && $row['return_interest'] ) {
					$sql = "SELECT transaction_balance
									FROM coop_account_transaction
									WHERE account_id = '{$row['account_id']}'
									ORDER BY transaction_time DESC, transaction_id DESC
									LIMIT 1";
					$rs_balance = $this->db->query($sql);
					$row_balance = $rs_balance->result_array()[0];
					$transaction_deposit = $row['return_interest'];
					$transaction_balance = $row['return_interest'] + $row_balance['transaction_balance'];
					$receipt_id = $row['receipt_id'];
					$bill_id = sprintf('R%s%s%05d', $year, $month, $_bill_id++);

					$sql = "INSERT INTO coop_account_transaction(transaction_time, transaction_list, transaction_withdrawal, transaction_deposit, transaction_balance, account_id, user_id)
									VALUES(NOW(), 'REVD', 0, {$transaction_deposit}, {$transaction_balance}, '{$row['account_id']}', 'process_interest')";
					$this->db->query($sql);

					$sql = "INSERT INTO coop_process_return(member_id, loan_id, return_type, account_id, receipt_id, bill_id, return_amount, return_year, return_month, return_time)
									VALUES('{$row['member_id']}', '{$row['loan_id']}', 2, '{$row['account_id']}', '{$receipt_id}', '{$bill_id}', {$row['return_interest']}, {$row['payment_date_year']}, {$row['payment_date_month']}, NOW())";
					@$this->db->query($sql);
				}
			}
		}
		*/
	}

	/**************************************
	 * Func process_return_edit -- Start
	 *************************************/
	public function process_return_edit() {
		$return_type = (int)$_POST['ret_type'];
		$date_s = $this->center_function->ConvertToSQLDate($_POST['date_s']);
		$date_e = $this->center_function->ConvertToSQLDate($_POST['date_e']);
		$json = [ 'data' => [] ];
		$limit = ($_POST['limit']=="") ? 100 : $_POST['limit'];
		$page = ($_POST['page']=="") ? 1 : $_POST['page'];
		if( in_array($return_type, [1]) ) {
			$tmp = $this->cal_process_return_type_1($date_s, $date_e);
			$total = 0;
			$return = 0;
			$surcharge = 0;
			if($tmp['return']) {
				foreach( $tmp['return'] as $key => $row) {
					if( ($row['return_principal'] + $row['return_interest']) && empty($row['is_return']) ) {
						$json['data'][] = [
							'member_id' => $row['member_id'],
							'member_name' => $row['member_name'],
							'loan_id' => $row['loan_id'],
							'contract_number' => $row['contract_number'],
							'interest_rate' => $row['interest_rate'],
							'receipt_id' => $row['receipt_id'],
							'return_principal' => $row['return_principal'],
							'return_interest' => $row['return_interest'],
							'return_status' => empty($row['is_return']) ? 'ยังไม่ได้โอนเงิน' : 'คืนเงินแล้วเมื่อ<br />'.$this->center_function->mydate2date($row['return_time'], true)
						];
					}
				}
			}

			if($tmp['no_return']) {
				foreach( $tmp['no_return'] as $key => $row) {
					if( ($row['return_principal'] + $row['return_interest']) && empty($row['is_return']) ) {
						$json['data'][] = [
							'member_id' => $row['member_id'],
							'member_name' => $row['member_name'],
							'loan_id' => $row['loan_id'],
							'contract_number' => $row['contract_number'],
							'interest_rate' => $row['interest_rate'],
							'receipt_id' => $row['receipt_id'],
							'return_principal' => $row['return_principal'],
							'return_interest' => $row['return_interest'],
							'return_status' => empty($row['is_return']) ? 'ยังไม่ได้โอนเงิน' : 'คืนเงินแล้วเมื่อ<br />'.$this->center_function->mydate2date($row['return_time'], true)
						];
					}
				}
			}
			if($tmp['surcharge']) {
				foreach( $tmp['surcharge'] as $key => $row) {
					if( ($row['return_principal'] + $row['return_interest'] + $row['surcharge']) && empty($row['is_return'])  ) {
						$json['data'][] = [
							'member_id' => $row['member_id'],
							'member_name' => $row['member_name'],
							'loan_id' => $row['loan_id'],
							'contract_number' => $row['contract_number'],
							'interest_rate' => $row['interest_rate'],
							'receipt_id' => $row['receipt_id'],
							'return_principal' => $row['return_principal'],
							'return_interest' => $row['return_interest'],
							'surcharge' => $row['surcharge'],
							'return_status' => empty($row['is_return']) ? 'ยังไม่ได้โอนเงิน' : 'คืนเงินแล้วเมื่อ<br />'.$this->center_function->mydate2date($row['return_time'], true)
						];
					}
				}
			}
		} elseif($return_type == 2) {
			$tmp = $this->cal_process_return_type_2($date_s, $date_e);
			if(!empty($tmp['return'])){
			foreach( $tmp['return'] as $key => $row ) {
				if( !$row['is_return'] ) {
					$sql = "SELECT tb1.contract_number, CONCAT(tb2.firstname_th, ' ', tb2.lastname_th) member_name
									FROM coop_loan tb1
									INNER JOIN coop_mem_apply tb2 ON tb1.member_id = tb2.member_id
									WHERE tb1.id = '{$row['loan_id']}'";
					$rs_return = $this->db->query($sql);
					$row_return = $rs_return->row_array();
					$json['data'][] = [
						'member_id' => $row['member_id'],
						'member_name' => $row_return['member_name'],
						'loan_id' => $row['loan_id'],
						'contract_number' => $row_return['contract_number'],
						'interest_rate' => $row['interest_rate'],
						'receipt_id' => $row['receipt_id'],
							'return_principal' => $row['return_principal'],
							'return_interest' => $row['return_interest'],
						'return_status' => empty($row['ret_id']) ? 'ยังไม่ได้โอนเงิน' : 'คืนเงินแล้วเมื่อ<br />'.$this->center_function->mydate2date($row['return_time'], true)
					];
				}
			}
			}
		} elseif($return_type == 3) {
			$sql = "SELECT
								t2.receipt_id,
							t2.member_id,
							t2.loan_atm_id,
							t2.principal_payment,
							t2.interest,
							t2.total_amount,
							t2.loan_amount_balance,
							t1.receipt_datetime
							, t4.payment_date
							, DATEDIFF( DATE(t1.receipt_datetime), t4.payment_date ) _payment_diff_
							, tb6.ret_id
							, tb6.return_time
							, CONCAT(tb7.firstname_th, ' ', tb7.lastname_th) member_name
							FROM coop_receipt AS t1
							INNER JOIN coop_finance_transaction AS t2 ON t1.receipt_id = t2.receipt_id
							LEFT JOIN coop_finance_month_profile AS t3 ON t1.month_receipt = t3.profile_month AND t1.year_receipt = t3.profile_year
							LEFT OUTER JOIN (

								SELECT
								tb1.loan_atm_id,
								tb1.transaction_datetime,
								DATE(tb1.transaction_datetime) AS payment_date,
								tb1.receipt_id,
								tb2.loan_description,
								SUM( IF ( tb2.loan_amount <> '', tb2.loan_amount, tb3.principal_payment ) ) AS principal,
								SUM(tb3.interest) AS interest,
								SUM(tb3.total_amount) AS total_amount,
								IF (
								tb2.loan_description != '',
								tb2.loan_description,

								IF (
								tb4.finance_month_profile_id != '',
								'ชำระเงินรายเดือน',
								'ชำระเงินอื่นๆ'
								)
								) AS data_text
								, tb1.loan_amount_balance
								FROM
								coop_loan_atm_transaction AS tb1
								LEFT JOIN coop_loan_atm_detail AS tb2 ON tb1.loan_atm_id = tb2.loan_atm_id
									AND tb1.transaction_datetime = tb2.loan_date
								LEFT JOIN coop_finance_transaction AS tb3 ON tb1.receipt_id = tb3.receipt_id
									AND tb1.loan_atm_id = tb3.loan_atm_id
								LEFT JOIN coop_receipt AS tb4 ON tb3.receipt_id = tb4.receipt_id
								LEFT JOIN coop_receipt AS t6 ON tb1.receipt_id = t6.receipt_id
								WHERE DATE(tb1.transaction_datetime) BETWEEN '{$date_s}' AND '{$date_e}'
									AND tb4.finance_month_profile_id <> ''
								GROUP BY tb1.loan_atm_id, tb1.transaction_datetime
								ORDER BY tb1.loan_atm_id

							) t4 ON t2.loan_atm_id = t4.loan_atm_id

							LEFT OUTER JOIN (SELECT * FROM coop_process_return WHERE return_type = 3) tb6 ON t2.member_id = tb6.member_id
									AND t2.loan_atm_id = tb6.loan_atm_id
									AND MONTH(t4.payment_date) = tb6.return_month
									AND YEAR(t4.payment_date) = tb6.return_year

							INNER JOIN coop_mem_apply tb7 ON t1.member_id = tb7.member_id

							WHERE t2.loan_atm_id <> '' AND
							(t1.finance_month_profile_id IS NULL ) AND
							DATE(t1.receipt_datetime) BETWEEN '{$date_s}' AND '{$date_e}' AND
							t2.loan_amount_balance <= 0 AND
							t4.payment_date Is NOT NULL AND
							DATEDIFF( DATE(t1.receipt_datetime), t4.payment_date ) <= 0
							ORDER BY t2.member_id,t2.loan_atm_id";
			$rs = $this->db->query($sql);
			$tmp_member = [];
			foreach ($rs->result_array() as $row) {
				$tmp_member[] = "'{$row["member_id"]}'";
				$return_func = $this->cal_return_atm($row['member_id'], $row['loan_atm_id'], explode('-', $date_s)[0], explode('-', $date_s)[1]);
				if( ($return_func['return_interest'] + $return_func['return_principal']) > 0) {
					$json['data'][] = [
						'return_func' => $return_func,
						'member_id' => $row['member_id'],
						'member_name' => $row['member_name'],
						'loan_id' => $row['loan_atm_id'],
						'contract_number' => $return_func['contract_number'],
						'interest_rate' => $return_func['interest_rate'],
						'receipt_id' => $row['receipt_id'],
						'return_principal' => $return_func['return_principal'],
						'return_interest' => $return_func['return_interest'],
						'return_status' => empty($row['ret_id']) ? 'ยังไม่ได้โอนเงิน' : 'คืนเงินแล้วเมื่อ<br />'.$this->center_function->mydate2date($row['return_time'], true)
					];
				}
			}
			//$json['data'] = [];
		} elseif($return_type == 4){
			$day = substr($date_s, 8 , 10);
			$this->db->select(array(
				"coop_mem_apply.member_id",
				"CONCAT(firstname_th, ' ', lastname_th) as member_name",
				"coop_loan_atm.loan_atm_id",
				"coop_loan_atm.contract_number",
				"return_interest",
				"return_status",
				"interest_rate"
			));
			$this->db->join("coop_mem_apply", "coop_mem_apply.member_id = coop_process_return_store.member_id");
			$this->db->join("coop_loan_atm", "coop_loan_atm.loan_atm_id = coop_process_return_store.loan_atm_id");
			$this->db->where("STR_TO_DATE(concat(return_year,'-',return_month,'-','{$day}'), '%Y-%m-%d') BETWEEN '{$date_s}' and '{$date_s}'");
			$query = $this->db->get_where("coop_process_return_store", array(
				"return_status" => "0",
			));
			foreach ($query->result_array() as $key => $value) {
				$json['data'][] = [
					'return_func' => '',
					'member_id' => $value['member_id'],
					'member_name' => $value['member_name'],
					'loan_id' => $value['loan_atm_id'],
					'contract_number' => $value['contract_number'],
					'interest_rate' => $value['interest_rate'],
					'receipt_id' => ($value['receipt_id']=="") ? "" : $value['receipt_id'],
					'return_interest' => round($value['return_interest']),
					'return_status' => empty($value['retturn_status']===0) ? 'ยังไม่ได้โอนเงิน' : 'คืนเงินแล้วเมื่อ<br />'.$this->center_function->mydate2date(@$row['return_time'], true)
				];
			}

		} elseif($return_type == 5){
			$day = substr($date_s, 8 , 10);
				$sql = "SELECT t5.* FROM (
					SELECT t1.member_id,CONCAT(t1.firstname_th, ' ', t1.lastname_th) as member_name,t2.receipt_id,t3.finance_month_profile_id,
					(SELECT share_collect_value FROM coop_mem_share WHERE member_id = t1.member_id ORDER BY share_date DESC LIMIT 1) AS share_collect_value,
					(SELECT loan_amount_balance FROM coop_loan WHERE member_id = t1.member_id AND id = t2.loan_id LIMIT 1) AS loan_amount_balance, 
					(SELECT (total_amount_approve-total_amount_balance) AS loan_atm_amount_balance FROM coop_loan_atm WHERE member_id = t1.member_id AND loan_atm_id = t2.loan_atm_id LIMIT 1) AS loan_atm_amount_balance,
					(SELECT
						 (SELECT transaction_balance FROM coop_account_transaction WHERE account_id = t1.account_id ORDER BY transaction_time DESC,transaction_id DESC  LIMIT 1) AS transaction_balance
							FROM
								coop_maco_account AS t1
							WHERE
								t1.type_id = '2'
							AND t1.account_status = '1'
							AND t1.mem_id = t1.member_id
					) AS transaction_balance
					FROM coop_mem_apply as t1
					LEFT OUTER JOIN (
						SELECT t2.member_id,t1.receipt_id,t1.finance_month_profile_id FROM coop_receipt AS t1 
						INNER JOIN coop_mem_req_resign AS t2 ON t1.member_id = t2.member_id 	AND t1.receipt_datetime >=DATE(t2.approve_date)
						GROUP BY t2.member_id
						ORDER BY t1.receipt_datetime DESC
					)AS t3 ON t1.member_id = t3.member_id
					LEFT OUTER JOIN coop_finance_transaction  AS t2 ON t3.receipt_id = t2.receipt_id
					WHERE t1.mem_type in (2,5,6) AND t2.payment_date BETWEEN '".$date_s."' AND '".$date_e."' AND t2.account_list_id IN (15,16,30,31)
					GROUP BY t1.member_id
				) AS t5 WHERE t5.finance_month_profile_id IS NOT NULL AND (t5.share_collect_value > 0 OR t5.loan_amount_balance < 0 OR t5.loan_atm_amount_balance < 0 OR t5.transaction_balance > 0)
				";	
					
			$row_5 = $this->db->query($sql)->result_array();			
			//echo $this->db->last_query(); echo '<hr>'; exit;
			
			//echo '<pre>'; print_r($row_5); echo '</pre>';
			$arr_data = array();	
			foreach ($row_5 as $key => $value) {				
				$sql_detail = "SELECT
								t1.receipt_id,
								t1.principal_payment,
								t1.interest,
								t1.total_amount,
								t1.member_id,
								t1.loan_id,
								t1.loan_atm_id,
								t1.account_list_id,
								t1.transaction_text,
								t2.account_list,
								t3.contract_number,
								t4.contract_number AS contract_number_atm,
								t5.account_id
							FROM
								coop_finance_transaction AS t1
							LEFT OUTER JOIN coop_account_list AS t2 ON t1.account_list_id = t2.account_id
							LEFT OUTER JOIN coop_loan AS t3 ON t1.loan_id = t3.id
							LEFT OUTER JOIN coop_loan_atm AS t4 ON t1.loan_atm_id = t4.loan_atm_id
							LEFT OUTER JOIN (SELECT account_id,mem_id FROM coop_maco_account AS t1 WHERE t1.type_id = '2' AND t1.account_status = '1') AS t5 ON t1.member_id = t5.mem_id AND t1.account_list_id = '30'
							WHERE
								t1.receipt_id = '".$value['receipt_id']."' AND t1.account_list_id IN (15,16,30,31)";
				
				$row_5_detail = $this->db->query($sql_detail)->result_array();
				$arr_data_detail = array();
				foreach ($row_5_detail as $key_detail => $value_detail) {
					$arr_data_detail[$key_detail]['loan_id'] = $value_detail['loan_id'];
					$arr_data_detail[$key_detail]['loan_atm_id'] = $value_detail['loan_atm_id'];
					$arr_data_detail[$key_detail]['contract_number'] = $value_detail['contract_number'];
					$arr_data_detail[$key_detail]['contract_number_atm'] = $value_detail['contract_number_atm'];
					$arr_data_detail[$key_detail]['return_principal'] = $value_detail['principal_payment'];
					$arr_data_detail[$key_detail]['return_interest'] = $value_detail['interest'];
					$arr_data_detail[$key_detail]['account_id'] = $value_detail['account_id'];
					$arr_data_detail[$key_detail]['transaction_text'] = $value_detail['transaction_text'];
				}
				$arr_data[$key]['return_func'] = '';
				$arr_data[$key]['member_id'] = $value['member_id'];
				$arr_data[$key]['member_name'] = $value['member_name'];
				$arr_data[$key]['receipt_id'] = ($value['receipt_id']=="") ? "" : $value['receipt_id'];
				$arr_data[$key]['return_status'] = empty($value['retturn_status']===0) ? 'ยังไม่ได้โอนเงิน' : 'คืนเงินแล้วเมื่อ<br />'.$this->center_function->mydate2date(@$row['return_time'], true);
				$arr_data[$key]['data_detail'] = $arr_data_detail;				
			}
			$json['data'] = $arr_data;

		}
		/*********************************************************************
		 * กรณีชำระครบแล้ว แต่มีรายเดือนเรียกเก็บ
		 */
		if( count( $tmp_member ) ) {
			$year = explode('-', $date_s)[0];
			$month = explode('-', $date_s)[1];
			$sql = "SELECT interest_rate
						FROM coop_loan_atm_setting_template
						WHERE start_date <= '{$year}-{$month}-01'
						ORDER BY start_date DESC
						LIMIT 1	";
			$rs_interest_rate = $this->db->query($sql);
			$row_interest_rate = $rs_interest_rate->result_array()[0];
			$interest_rate = $row_interest_rate['interest_rate'];

			$tmp_member = implode(',', $tmp_member);
			$sql = "SELECT
			t2.receipt_id,
		t2.member_id,
		t2.loan_atm_id,
		t2.principal_payment,
		t2.interest,
		t2.total_amount,
		t2.loan_amount_balance,
		t1.receipt_datetime
		, t4.payment_date
		, DATEDIFF( DATE(t1.receipt_datetime), t4.payment_date ) _payment_diff_
		, tb6.ret_id
		, tb6.return_time
		, CONCAT(tb7.firstname_th, ' ', tb7.lastname_th) member_name
		FROM coop_receipt AS t1
		INNER JOIN coop_finance_transaction AS t2 ON t1.receipt_id = t2.receipt_id
		LEFT JOIN coop_finance_month_profile AS t3 ON t1.month_receipt = t3.profile_month AND t1.year_receipt = t3.profile_year
		LEFT OUTER JOIN (

			SELECT
			tb1.loan_atm_id,
			tb1.transaction_datetime,
			DATE(tb1.transaction_datetime) AS payment_date,
			tb1.receipt_id,
			tb2.loan_description,
			SUM( IF ( tb2.loan_amount <> '', tb2.loan_amount, tb3.principal_payment ) ) AS principal,
			SUM(tb3.interest) AS interest,
			SUM(tb3.total_amount) AS total_amount,
			IF (
			tb2.loan_description != '',
			tb2.loan_description,

			IF (
			tb4.finance_month_profile_id != '',
			'ชำระเงินรายเดือน',
			'ชำระเงินอื่นๆ'
			)
			) AS data_text
			, tb1.loan_amount_balance
			FROM
			coop_loan_atm_transaction AS tb1
			LEFT JOIN coop_loan_atm_detail AS tb2 ON tb1.loan_atm_id = tb2.loan_atm_id
				AND tb1.transaction_datetime = tb2.loan_date
			LEFT JOIN coop_finance_transaction AS tb3 ON tb1.receipt_id = tb3.receipt_id
				AND tb1.loan_atm_id = tb3.loan_atm_id
			LEFT JOIN coop_receipt AS tb4 ON tb3.receipt_id = tb4.receipt_id
			LEFT JOIN coop_receipt AS t6 ON tb1.receipt_id = t6.receipt_id
			WHERE DATE(tb1.transaction_datetime) BETWEEN '{$date_s}' AND '{$date_e}'
				AND tb4.finance_month_profile_id <> ''
			GROUP BY tb1.loan_atm_id, tb1.transaction_datetime
			ORDER BY tb1.loan_atm_id

		) t4 ON t2.loan_atm_id = t4.loan_atm_id

		LEFT OUTER JOIN (SELECT * FROM coop_process_return WHERE return_type = 3) tb6 ON t2.member_id = tb6.member_id
				AND t2.loan_atm_id = tb6.loan_atm_id
				AND MONTH(t4.payment_date) = tb6.return_month
				AND YEAR(t4.payment_date) = tb6.return_year

		INNER JOIN coop_mem_apply tb7 ON t1.member_id = tb7.member_id

		WHERE t2.loan_atm_id <> '' AND
		( t1.finance_month_profile_id IS NOT NULL AND t2.loan_amount_balance < 0 ) AND
		DATE(t1.receipt_datetime) BETWEEN '{$date_s}' AND '{$date_e}' AND
		t2.loan_amount_balance <= 0 AND
		t4.payment_date Is NOT NULL AND
		DATEDIFF( DATE(t1.receipt_datetime), t4.payment_date ) <= 0
		AND t2.member_id NOT IN ( {$tmp_member} )
		ORDER BY t2.member_id,t2.loan_atm_id";
			$rs = $this->db->query($sql);
			foreach ($rs->result_array() as $row) {

				$sql = "SELECT loan_atm_id, contract_number
				FROM coop_loan_atm
				WHERE member_id = '{$row['member_id']}'
					AND loan_atm_id = '{$row['loan_atm_id']}'
					";
				$rs_loan_atm = $this->db->query($sql);
				$row_loan_atm = $rs_loan_atm->result_array()[0];

				if( abs( $row['loan_amount_balance'] ) ) {
					$json['data'][] = [
						'return_func' => [],
						'member_id' => $row['member_id'],
						'member_name' => $row['member_name'],
						'loan_id' => $row['loan_atm_id'],
						'contract_number' => $row_loan_atm['contract_number'],
						'interest_rate' => $interest_rate,
						'receipt_id' => $row['receipt_id'],
						'return_interest' => abs( $row['loan_amount_balance'] ),
						'return_status' => empty($row['ret_id']) ? 'ยังไม่ได้โอนเงิน' : 'คืนเงินแล้วเมื่อ<br />'.$this->center_function->mydate2date($row['return_time'], true)
					];
				}
			}
		}
		// var_dump($json);
		$tmp_json 	= $json['data'];
		$json		= array();
		$json['data'] = [];
		$start 		= $page * $limit - ($limit);
		$end 		= $page * $limit - 1;
		foreach ($tmp_json as $key => $value) {
			if($key >= $start && $key <= $end){
				array_push($json['data'], $tmp_json[$key]);
			}
		}
		
		echo json_encode($json);
	}

	function cal_return_atm($member_id, $param_loan_atm_id, $year, $month) {

		$sql = "SELECT interest_rate
						FROM coop_loan_atm_setting_template
						WHERE start_date <= '{$year}-{$month}-01'
						ORDER BY start_date DESC
						LIMIT 1	";
		$rs_interest_rate = $this->db->query($sql);
		$row_interest_rate = $rs_interest_rate->result_array()[0];
		$interest_rate = $row_interest_rate['interest_rate'];

		$sql = "SELECT loan_atm_id, contract_number
						FROM coop_loan_atm
						WHERE member_id = '{$member_id}'
							AND loan_atm_id = '{$param_loan_atm_id}'
							";
		$rs_loan_atm = $this->db->query($sql);
		$total_day_of_month = date_format(date_create("{$year}-{$month}-01"), 't') ;
		foreach ($rs_loan_atm->result_array() as $row_loan_atm) {
			$loan_atm_id = $row_loan_atm['loan_atm_id'];
			$contract_number = $row_loan_atm['contract_number'];
			$sql = "SELECT *
							FROM (

							SELECT CASE WHEN t4.finance_month_profile_id IS NULL THEN 2 ELSE 3 END payment_type
							, IF (
						t2.loan_description != '',
						`t2`.`loan_description`,

					IF (
						t4.finance_month_profile_id != '',
						'ชำระเงินรายเดือน',
						'ชำระเงินอื่นๆ'
					)
					) loan_description
							, SUM(							IF (
								t2.loan_amount <> '',
								`t2`.`loan_amount`,
								t3.principal_payment
							)) loan_amount
							, SUM(t3.interest) interest
							, IF (
								! ISNULL(
								(
								SELECT
									ret_id
								FROM
									coop_process_return
								WHERE
									coop_process_return.return_month = t6.month_receipt AND
									coop_process_return.return_year = (t6.year_receipt-543) AND
									coop_process_return.loan_atm_id = t1.loan_atm_id
								LIMIT 1
								)
								),
								SUM(

								IF (
								t2.loan_amount <> '',
								`t2`.`loan_amount`,
								t3.principal_payment
								)
								) * - 1,
								`t1`.`loan_amount_balance`
								) loan_amount_balance
							, DATE(t1.transaction_datetime) loan_date
							FROM
								`coop_loan_atm_transaction` AS `t1`
							LEFT JOIN `coop_loan_atm_detail` AS `t2` ON `t1`.`loan_atm_id` = `t2`.`loan_atm_id`
							AND `t1`.`transaction_datetime` = `t2`.`loan_date`
							LEFT JOIN `coop_finance_transaction` AS `t3` ON `t1`.`receipt_id` = `t3`.`receipt_id`
							AND `t1`.`loan_atm_id` = `t3`.`loan_atm_id`
							LEFT JOIN `coop_receipt` AS `t4` ON `t3`.`receipt_id` = `t4`.`receipt_id`
							LEFT JOIN coop_receipt AS t6 ON t1.receipt_id = t6.receipt_id
							WHERE
								`t1`.`loan_atm_id` = '{$loan_atm_id}'
							GROUP BY
								`t1`.`transaction_datetime`
							) tb
							WHERE loan_date BETWEEN DATE_FORMAT(DATE_ADD('{$year}-{$month}-01', INTERVAL -1 DAY), '%Y-%m-01') AND '{$year}-{$month}-{$total_day_of_month}'
							ORDER BY loan_date ASC";
			$rs = $this->db->query($sql);
			$date_s = null;
			$date_e = null;

			$total_day_of_year = date_format(date_create("{$year}-{$month}-31"), 'z') + 1;
			$is_end = false;
			$loan_amount_balance = 0;
			$interest_acc = [];
			$interest = 0;
			$index = 0;
			$is_close = 0;
			$return = 0;
			$return_arr = [];

			$sum = [];

			foreach ($rs->result_array() as $row) {
				if( !$is_end ) {
					$return = 0;
					$return_principal = 0 ;
					if( !$date_s && !$date_e ) {
						$num_of_days = '';
					} elseif( $date_s != null ) {
						$date_e = date_create(explode(' ', $row['loan_date'])[0]);
						$num_of_days = date_diff($date_s, $date_e)->format('%a');
						$date_s = date_create(explode(' ', $row['loan_date'])[0]);
					}

					if( (int)$num_of_days > 0 ) {
						$interest_acc[$index] = $loan_amount_balance * ($interest_rate / 100) * ($num_of_days/$total_day_of_year);
					} else {
						$interest_acc[$index] = 0;
					}

					if( $row['payment_type'] == 2 ) { // กรณีมีการชำระอื่น ๆ เข้ามา
						//  && $row['loan_amount_balance'] ยังไม่ปิด
						//  && !$row['loan_amount_balance'] ปิด

						//echo "<div>{$row['interest']} ::: ".(array_sum($interest_acc))."</div>";
						$interest_acc[] = $row['interest'] ;
						//$return = abs(round($row['interest'] - array_sum($interest_acc) + $row['interest'], 2));
						$return = 0;
						$return_arr[explode(' ', $row['loan_date'])[0]] = $return;
						$interest_acc = [];
					}

					if( $row['loan_amount_balance'] == 0 || $is_close ) {
						$interest_acc = [];
						$is_close = 1;
					}
					if( $is_close && $row['payment_type'] == 3 ) { // กรณีมีปิด และมีการเรียกเก็บรายเดือน
						/* Edit - แยกคืนต้น คืนดอก */
						$return_principal = $row['loan_amount'];
						$interest_acc[] = $row['interest'] ;
						//$interest_acc[] = $row['loan_amount'] + $row['interest'] ;

						$return = abs(round(array_sum($interest_acc), 2));
						$return_arr[explode(' ', $row['loan_date'])[0]] = $return;
					}

					if( !$is_close && $row['payment_type'] == 3 && $num_of_days ) { // กรณีมียังไม่ปิด และมีการเรียกเก็บรายเดือน
						$return = abs(round($row['interest'] - array_sum($interest_acc), 2));
						$return_arr[explode(' ', $row['loan_date'])[0]] = $return;
					}

					$sum['return_principal'] += $return_principal;
					$sum['return'] += $return;
					if( $row['payment_type'] == 3 ) {
						$date_s = date_create(explode(' ', $row['loan_date'])[0]);
					}
					$index++;
					$loan_amount_balance = $row['loan_amount_balance'];
				}

				if( date_format(date_create(explode(' ', $row['loan_date'])[0]), 'Y-m') == "{$year}-{$month}" && $row['payment_type'] == 3 ) {
					if( !$is_close ) $interest = $row['interest'];
				}

			}
		}




		return [
				'interest_rate' => $interest_rate,
				'contract_number' => $contract_number,
				'return_principal' => $sum['return_principal'],
				'return_interest' => $sum['return'],
				'member_id' => $member_id,
				'param_loan_atm_id' => $param_loan_atm_id
			];
	}
	/**************************************
	 * Func process_return_edit -- End
	 *************************************/
	/**************************************
	 * Func process_return_edit_exec -- Start
	 *************************************/
	public function process_return_edit_exec() {
		$json = [];
		$return_type = (int)$_POST['ret_type'];
		$date_s = $this->center_function->ConvertToSQLDate($_POST['date_s']);
		$date_e = $this->center_function->ConvertToSQLDate($_POST['date_e']);
		//echo '<pre>'; print_r($_POST); echo '</pre>';
		//var_dump($return_type);
		//exit;
		//var_dump($_POST);
		//exit;
		$json['post'] = $_POST;
		//$json['date_e'] = $_POST['date_e'];
		$year = date('Y') + 543 ;
		$month = explode('-', $date_s)[1];
		$sql = "SELECT bill_id
						FROM coop_process_return
						WHERE bill_id LIKE 'R{$year}{$month}%'
						ORDER BY bill_id DESC
						LIMIT 1";
		$rs = $this->db->query($sql);
		if( !$rs->num_rows() ) $_bill_id = 1;
		else {
			$row = $rs->result_array()[0];
			$_bill_id = (int)substr($row['bill_id'], 7, 5) + 1;
		}

		foreach( $_POST['check_return'] as $key => $val ) {
			$member_id = $_POST['member_id'][$key];
			$loan_id = $_POST['loan_id'][$key];
			$receipt_id = $_POST['receipt_id'][$key];
			$return_principal = (double)$_POST['return_principal'][$key];
			$return_interest = (double)$_POST['return_interest'][$key];
			$surcharge = (double)$_POST['surcharge'][$key];

			$sql = "SELECT ret_id
							FROM coop_process_return
							WHERE return_type = {$return_type}
								AND member_id = '{$member_id}'
								AND loan_id = '{$loan_id}'
								AND return_year = YEAR('{$date_e}')
								AND return_month = MONTH('{$date_e}')
								AND receipt_id = '{$receipt_id}'
								AND account_id = ''
								";
			$rs_chk = $this->db->query($sql);

			$sql = "SELECT account_id
							FROM coop_maco_account
							WHERE type_id = '2'
								AND account_status = '0'
								AND mem_id = '{$member_id}'";
			$rs_account = $this->db->query($sql);

			if( $rs_chk->num_rows() == "0") {
				$account_id = $rs_account->result_array()[0];
				$account_id = $account_id['account_id'];
				$bill_id = sprintf('R%s%s%05d', $year, $month, $_bill_id++);
				$return_time = date('Y-m-d H:i:s');

					$return_amount = $return_principal + $return_interest;
					if( $return_amount ) {
						$sql = "SELECT transaction_balance
										FROM coop_account_transaction
										WHERE account_id = '{$account_id}'
										ORDER BY transaction_time DESC, transaction_id DESC
										LIMIT 1";
						$rs_balance = $this->db->query($sql);
						$row_balance = $rs_balance->result_array()[0];
						$transaction_deposit = $return_amount;
						$transaction_balance = $return_amount + $row_balance['transaction_balance'];

					/*$sql = "INSERT INTO coop_account_transaction(transaction_time, transaction_list, transaction_withdrawal, transaction_deposit, transaction_balance, account_id, user_id ,transaction_text)
										VALUES(NOW(), 'REVD', 0, {$transaction_deposit}, {$transaction_balance}, '{$account_id}','{$_SESSION['USER_ID']}' , 'process_interest_edit')";
						$this->db->query($sql);					
					*/

						if( $return_type == 3) {
							$sql = "INSERT INTO coop_process_return(member_id, loan_atm_id, return_type, account_id, receipt_id, bill_id, return_principal, return_interest, return_amount, return_year, return_month, return_time, user_id)
							VALUES('{$member_id}', '{$loan_id}', {$return_type}, '{$account_id}', '{$receipt_id}', '{$bill_id}', {$return_principal}, {$return_interest}, {$return_amount}, YEAR('{$date_e}'), MONTH('{$date_e}'), NOW(), '{$_SESSION['USER_ID']}')";
							@$this->db->query($sql);

							if( $return_principal ) {
								$sql = "SELECT loan_amount_balance
												FROM coop_loan_atm_transaction
												WHERE loan_atm_id = '{$loan_id}'
												ORDER BY loan_atm_transaction_id DESC
												LIMIT 1";
								$rs_trans_atm = $this->db->query($sql);
								$row_trans_atm = $rs_trans_atm->result_array()[0];
								$loan_amount_balance = (double)$row_trans_atm['loan_amount_balance'];

								if( $loan_amount_balance < 0 ) {
									$loan_amount_balance += $return_principal;
									$sql = "INSERT INTO coop_loan_atm_transaction(loan_atm_id, loan_amount_balance, transaction_datetime, receipt_id)
													VALUES('{$loan_id}', {$loan_amount_balance}, '{$return_time}', '{$bill_id}')";
									@$this->db->query($sql);
									$sql = "UPDATE coop_loan_atm SET
													total_amount_balance = total_amount_approve - {$loan_amount_balance}
													WHERE loan_atm_id = '{$loan_id}'";
									@$this->db->query($sql);
								}


							}
						
						}else if( $return_type == 4 ){
							
							$sql = "INSERT INTO coop_process_return(member_id, loan_atm_id, return_type, account_id, receipt_id, bill_id, return_principal, return_interest, return_amount, return_year, return_month, return_time, user_id)
							VALUES('{$member_id}', '{$loan_id}', 4, '{$account_id}', '{$receipt_id}', '{$bill_id}', {$return_principal}, {$return_interest}, {$return_amount}, YEAR('{$date_e}'), MONTH('{$date_e}'), NOW(), '{$_SESSION['USER_ID']}')";
							@$this->db->query($sql);

							$this->db->set("return_status", "1");
							$this->db->set("updatetime", date("Y-m-d H:i:s"));
							$this->db->where("return_status = 0");
							$this->db->where("member_id", $member_id);
							$this->db->where("loan_atm_id", $loan_id);
							$this->db->where("return_month", $month);
							$this->db->where("return_year", $year-543);
							$this->db->update("coop_process_return_store");

						} else {
							$sql = "INSERT INTO coop_process_return(member_id, loan_id, return_type, account_id, receipt_id, bill_id, return_principal, return_interest, return_amount, return_year, return_month, return_time, user_id)
							VALUES('{$member_id}', '{$loan_id}', {$return_type}, '{$account_id}', '{$receipt_id}', '{$bill_id}', {$return_principal}, {$return_interest}, {$return_amount}, YEAR('{$date_e}'), MONTH('{$date_e}'), NOW() ,'{$_SESSION['USER_ID']}')";
							@$this->db->query($sql);
							
							//ตัดยอดเงินกู้สามัญที่ติดลบ
							if( $return_principal ) {
								$sql = "SELECT loan_amount_balance
												FROM coop_loan_transaction
												WHERE loan_id = '{$loan_id}'
												ORDER BY loan_transaction_id DESC
												LIMIT 1";
								$rs_trans_loan = $this->db->query($sql);
								$row_trans_loan = $rs_trans_loan->result_array()[0];
								$loan_amount_balance = (double)$row_trans_loan['loan_amount_balance'];

								if( $loan_amount_balance < 0 ) {
									$loan_amount_balance += $return_principal;
									$sql = "INSERT INTO coop_loan_transaction(loan_id, loan_amount_balance, transaction_datetime ,receipt_id)
													VALUES('{$loan_id}', {$loan_amount_balance}, '{$return_time}', '{$bill_id}')";
									@$this->db->query($sql);
									$sql = "UPDATE coop_loan SET
													loan_amount_balance = {$loan_amount_balance}
													WHERE id = '{$loan_id}'";
									@$this->db->query($sql);
								}
							}
						}

					}

					if( $surcharge ) {

						$sql = "SELECT transaction_balance
										FROM coop_account_transaction
										WHERE account_id = '{$account_id}'
										ORDER BY transaction_time DESC, transaction_id DESC
										LIMIT 1";
						$rs_balance = $this->db->query($sql);
						$row_balance = $rs_balance->result_array()[0];
						$transaction_withdrawal = $surcharge;
						$transaction_balance = $row_balance['transaction_balance'] - $transaction_withdrawal;
					/*$sql = "INSERT INTO coop_account_transaction(transaction_time, transaction_list, transaction_withdrawal, transaction_deposit, transaction_balance, account_id, user_id, transaction_text)
										VALUES(NOW(), 'REVD', {$transaction_withdrawal}, 0, {$transaction_balance}, '{$account_id}', '{$_SESSION['USER_ID']}', 'process_interest_edit')";
						$this->db->query($sql);
					*/

						$sql = "INSERT INTO coop_process_return(member_id, loan_id, return_type, account_id, receipt_id, bill_id, return_principal, return_interest, return_amount, return_year, return_month, return_time, user_id)
						VALUES('{$member_id}', '{$loan_id}', 5, '{$account_id}', '{$receipt_id}', '{$bill_id}', 0, {$surcharge}, {$surcharge}, YEAR('{$date_e}'), MONTH('{$date_e}'), NOW(), '{$_SESSION['USER_ID']}')";
						@$this->db->query($sql);
					}
			}
			//$json['sql'][] = $sql;
		}
		
		////คืนเงินลาออก
		if( $return_type == 5 ){
			$this->cal_process_return_type_5($_POST);
		}
		echo json_encode($json);
	}
	/**************************************
	 * Func process_return_edit_exec -- End
	 *************************************/
	 	/**************************************
	 * Func คำนวณหักกลบ ( return_type = 1) -- Start
	 *************************************/
	function cal_process_return_type_1($date_s, $date_e) {
		$year_process = explode('-', $date_s)[0];
		$month_process = explode('-', $date_s)[1];
		$days_of_month = date_format(date_create($date_s), 't');
		$days_of_year = date_format(date_create("{$year_process}-12-31"), 'z') + 1 ;

		$interest_rate = [];
		$sql = "SELECT type_id, interest_rate
						FROM coop_term_of_loan
						WHERE start_date <= '{$date_s}'
						ORDER BY type_id, start_date DESC
						";
		$rs = $this->db->query($sql);
		foreach ($rs->result_array() as $row) {
			if( !isset($interest_rate[$row['type_id']]) ) $interest_rate[$row['type_id']] = $row['interest_rate'];
		}
		unset($sql, $rs, $row);


		$data_return = [];
		$sql = "SELECT *
						FROM coop_process_return
						WHERE return_year = {$year_process}
							AND return_month = {$month_process}
							AND return_type IN (1, 5)";
		$rs = $this->db->query($sql);
		foreach ($rs->result_array() as $row) {
			$data_return["{$row['member_id']}#{$row['loan_id']}#{$row['receipt_id']}"] = $row;
		}

		$sql = "SELECT DISTINCT tb2.member_id, tb2.receipt_id, tb2.loan_id,
						SUM(tb2.principal_payment) principal_payment, SUM(tb2.interest) interest
						, SUM(tb2.total_amount) total_amount
						, SUM(tb2.loan_amount_balance) loan_amount_balance
						, tb2.payment_date
						, DAYOFYEAR(DATE_FORMAT(NOW(), '%Y-12-31')) num_day_of_year
						, DATEDIFF(LAST_DAY(tb2.payment_date), DATE(tb2.payment_date)) num_datediff
						, CONCAT(tb3.firstname_th, ' ', tb3.lastname_th) member_name
						, tb4.account_id
						, tb5.loan_type
						, tb5.contract_number
						, YEAR(tb2.payment_date) payment_date_year
						, MONTH(tb2.payment_date) payment_date_month
						FROM coop_receipt AS tb1
						INNER JOIN coop_finance_transaction AS tb2 ON tb1.receipt_id = tb2.receipt_id
						INNER JOIN coop_mem_apply tb3 ON tb1.member_id = tb3.member_id AND tb3.mem_type NOT IN (2,5,6)
						LEFT OUTER JOIN (
							SELECT mem_id, account_id
							FROM coop_maco_account
							WHERE type_id = '2'
								AND account_status = '0'
						) tb4 ON tb1.member_id = tb4.mem_id
						INNER JOIN coop_loan tb5 ON tb2.loan_id = tb5.id
						WHERE tb2.payment_date BETWEEN '{$date_s}' AND '{$date_e}'
							AND (
									( tb2.receipt_id LIKE '%C%' OR tb2.receipt_id LIKE '%c%' )
									OR
									( tb2.receipt_id LIKE '%B%' OR tb2.receipt_id LIKE '%b%' )
							)
							AND tb1.finance_month_profile_id IS NOT NULL
							AND (tb1.receipt_status IS NULL OR tb1.receipt_status = '')
						GROUP BY tb2.member_id, tb2.receipt_id, tb5.contract_number
						ORDER BY tb2.member_id, tb2.loan_id, tb2.payment_date";
		// AND DATEDIFF(LAST_DAY(tb2.payment_date), DATE(tb2.payment_date)) > 0
		//
		$rs = $this->db->query($sql);
		$data = [];

		foreach ($rs->result_array() as $row) {
				$return_real = 0;
				if($row['loan_amount_balance'] < 0){

							$data['no_return'][] = [
								'member_id' => $row['member_id'],
								'member_name' => $row['member_name'],
								'loan_id' => $row['loan_id'],
								'contract_number' => $row['contract_number'],
								'receipt_id' => $row['receipt_id'],
								'account_id' => $row['account_id'],
								'return_principal' => $row['principal_payment'],
								'return_interest' => $row['interest'],
								'surcharge' => 0,
								'is_return' => isset($data_return["{$row['member_id']}#{$row['loan_id']}#{$row['receipt_id']}"]) ? 1 : 0,
								'ret_id' => isset($data_return["{$row['member_id']}#{$row['loan_id']}#{$row['receipt_id']}"]) ? $data_return["{$row['member_id']}#{$row['loan_id']}#{$row['receipt_id']}"]['ret_id'] : 0,
								'return_time' => isset($data_return["{$row['member_id']}#{$row['loan_id']}#{$row['receipt_id']}"]) ? $data_return["{$row['member_id']}#{$row['loan_id']}#{$row['receipt_id']}"]['return_time'] : 0,
								'interest_rate' => $interest_rate[$row['loan_type']],
								'principal_payment' => $row['principal_payment'],
								'interest' => $row['interest'],
								'loan_amount_balance' => $row['loan_amount_balance'],
								'payment_date_year' => $row['payment_date_year'],
								'payment_date_month' => $row['payment_date_month']
							];

				} else {
						$data['no_return'][] = [
							'member_id' => $row['member_id'],
							'member_name' => $row['member_name'],
							'loan_id' => $row['loan_id'],
							'contract_number' => $row['contract_number'],
							'receipt_id' => $row['receipt_id'],
							'account_id' => $row['account_id'],
							'return_principal' => $row['principal_payment'],
							'return_interest' => $row['interest'],
							'surcharge' => 0,
							'is_return' => isset($data_return["{$row['member_id']}#{$row['loan_id']}#{$row['receipt_id']}"]) ? 1 : 0,
							'ret_id' => isset($data_return["{$row['member_id']}#{$row['loan_id']}#{$row['receipt_id']}"]) ? $data_return["{$row['member_id']}#{$row['loan_id']}#{$row['receipt_id']}"]['ret_id'] : 0,
							'return_time' => isset($data_return["{$row['member_id']}#{$row['loan_id']}#{$row['receipt_id']}"]) ? $data_return["{$row['member_id']}#{$row['loan_id']}#{$row['receipt_id']}"]['return_time'] : 0,
							'interest_rate' => $interest_rate[$row['loan_type']],
							'principal_payment' => $row['principal_payment'],
							'interest' => $row['interest'],
							'loan_amount_balance' => $row['loan_amount_balance'],
							'payment_date_year' => $row['payment_date_year'],
							'payment_date_month' => $row['payment_date_month']
						];
					}

				// if(strpos( strtolower($row['receipt_id']), 'b') !== false) { // ผ่านรายการ


				// }
		}
		return $data;
	}
	 /**************************************
	 * Func คำนวณหักกลบ ( return_type = 1) -- End
	 *************************************/
	 	/**************************************
	 * Func คำนวณหักกลบ ( return_type = 2) -- Start
	 *************************************/
		function cal_process_return_type_2($date_s, $date_e) {
			$month_process = explode('-', $date_s)[1];
			$year_process = explode('-', $date_s)[0];
			$days_of_month = date_format(date_create($date_s), 't');
			$days_of_year = date_format(date_create("{$year_process}-12-31"), 'z') + 1 ;
			$sql = "SELECT tb2.member_id, tb2.receipt_id, tb2.loan_id
						, tb2.principal_payment
						, tb2.interest
						, tb2.total_amount
						, tb2.loan_amount_balance
						, tb2.payment_date
						, tb3.loan_type
						, tb4.account_id
						FROM coop_receipt AS tb1
						INNER JOIN coop_finance_transaction AS tb2 ON tb1.receipt_id = tb2.receipt_id
						INNER JOIN coop_loan tb3 ON tb2.loan_id = tb3.id
						LEFT OUTER JOIN ( SELECT * FROM coop_maco_account WHERE type_id = '2' AND account_status = '0' ) tb4 ON tb1.member_id = tb4.mem_id
						WHERE tb2.payment_date BETWEEN '{$date_s}' AND '{$date_e}'
							AND DATEDIFF(LAST_DAY(tb2.payment_date), DATE(tb2.payment_date)) > 0
							AND ( tb2.receipt_id NOT LIKE '%C%' OR tb2.receipt_id NOT LIKE '%c%' )
							AND tb1.finance_month_profile_id IS NULL
							AND (tb1.receipt_status IS NULL OR tb1.receipt_status = '')
							AND tb2.loan_id IS NOT NULL
						ORDER BY tb2.member_id, tb2.loan_id, tb2.payment_date, tb2.receipt_id";
			$rs = $this->db->query($sql);
			$data = [];
			//echo $sql;
			foreach ($rs->result_array() as $row) {
				/* เช็คว่ามีการผ่านรายการม้ย */
				$sql = "SELECT tb1.member_id, tb1.loan_id, tb1.receipt_id, tb1.payment_date,
					SUM(tb1.principal_payment) principal_payment,
					SUM(tb1.interest) interest,
					SUM(tb1.total_amount) total_amount,
					SUM(tb1.loan_amount_balance) AS loan_amount_balance
					FROM coop_finance_transaction tb1
					INNER JOIN coop_receipt tb2 ON tb1.receipt_id = tb2.receipt_id
					WHERE ( tb1.receipt_id NOT LIKE '%C%' OR tb1.receipt_id NOT LIKE '%c%' )
						AND tb1.payment_date BETWEEN '{$date_s}' AND '{$date_e}'
						AND tb1.loan_id IS NOT NULL
						AND tb2.finance_month_profile_id IS NOT NULL
						AND ( tb1.member_id = '{$row['member_id']}' AND tb1.loan_id = '{$row['loan_id']}' )
					GROUP BY tb1.member_id, tb1.loan_id, tb1.receipt_id, tb1.payment_date
				";
				$rs_chk = $this->db->query($sql);

				$sql = "SELECT *
								FROM coop_receipt
								WHERE receipt_status = 2
									AND receipt_id = '{$row['receipt_id']}'";
				$rs_chk_error = $this->db->query($sql);
				if( $rs_chk->num_rows() > 0 && $rs_chk_error->num_rows() == 0 ) {
					//เช็คมีชำระอื่นๆแล้วมีผ่านรายการ
					$row_chk = $rs_chk->row_array();
					$date_payment = DateTime::createFromFormat('Y-m-d', $row['payment_date']);
					$date_of_month = DateTime::createFromFormat('Y-m-d', $row_chk['payment_date']);
					$date_diff = $date_payment->diff($date_of_month);

					//echo $sql.'<br>';
					$loan_id = $row['loan_id'];
					$sql = "select loan_type from coop_loan where id = ".$loan_id." LIMIT 1";
					$loan_type = @$this->db->query($sql)->result_array()[0]['loan_type'];
					$loan = @$this->db->get_where("coop_loan", array("id" => $loan_id))->result_array()[0];
					$arg = array("member_id" => $loan['member_id'], "loan_id" => $loan_id);
					$interest_rate = $this->Interest_modal->get_interest($loan_type, $row_chk['payment_date'], $arg);

					$return = $row['loan_amount_balance'] * ( $interest_rate / 100 ) * ( $date_diff->format('%a') / $days_of_year ) ;
					$return_real = round($return);
					$sql = "SELECT *
									FROM coop_process_return
									WHERE return_year = {$year_process}
										AND return_month = {$month_process}
										AND return_type = 2
										AND member_id = '{$row['member_id']}'
										AND loan_id = '{$row['loan_id']}'";
					$rs_return = $this->db->query($sql);
					$row_return = $rs_return->row_array();
					if( strpos($row_chk['receipt_id'], 'B') ) {
						$return_principal = ($row_chk['loan_amount_balance']<0)?$row_chk['principal_payment']:0;
						$return_interest = ($row_chk['interest']- $return_real);
						$return_amount = $return_principal+$return_interest;

						$data['return']["{$row['member_id']}#{$row['loan_id']}"] = [
							'member_id' => $row['member_id'],
							'loan_id' => $row['loan_id'],
							'interest_rate' => $interest_rate,
							'receipt_id' => $row_chk['receipt_id'],
							'account_id' => $row['account_id'],
							'return_principal' => $return_principal,
							'return_interest' => $return_interest,
							'return_amount' => $return_amount,
							'ret_id' => $row_return['ret_id'],
							'return_time' => $row_return['return_time'],
							'is_return' => $rs_return->num_rows() ? 1 : 0
						];
					} else {
						$data['no_return']["{$row['member_id']}#{$row['loan_id']}"] = [
							'member_id' => $row['member_id'],
							'loan_id' => $row['loan_id'],
							'interest_rate' => $row['interest_rate'],
							'receipt_id' => $row_chk['receipt_id'],
							'account_id' => $row['account_id'],
							'return_amount' => $return_real + $data['no_return']["{$row['member_id']}#{$row['loan_id']}"]['return_amount'],
							'ret_id' => '',
							'return_time' => '',
							'is_return' => $rs_return->num_rows() ? 1 : 0
						];
					}
				}
			}
			return $data;
		}
	 	/**************************************
	 * Func คำนวณหักกลบ ( return_type = 2) -- End
	 *************************************/

	public function search_loan_guarantee()
	{
		$search_text = trim(@$_POST["search_text"]);
		$search_list = trim(@$_POST["search_list"]);
		$where = "";
		if(@$_POST['search_list'] == 'member_id'){
			$where = " coop_mem_apply.member_id LIKE '%".$search_text."%'";
		}else if(@$_POST['search_list'] == 'firstname_th'){
			$where = " coop_mem_apply.firstname_th LIKE '%".$search_text."%'";
		}else if(@$_POST['search_list'] == 'lastname_th'){
			$where = " coop_mem_apply.lastname_th LIKE '%".$search_text."%'";
		}else if(@$_POST['search_list'] == 'id_card'){
			$where = " coop_mem_apply.id_card LIKE '%".$search_text."%'";
		}else if(@$_POST['search_list'] == 'loan_prefix'){
			$where = " contract_number LIKE '%".$search_text."%'";
		}else if(@$_POST['search_list'] == 'employee_id'){
			$where = " employee_id LIKE '%".$search_text."%'";
		}
		$where .= "AND member_status = 1";
		$this->db->select(array(
			"coop_mem_apply.member_id",
			"firstname_th",
			"lastname_th",
			"t1.mem_group_name as department_name",
			"t2.mem_group_name as faction_name",
			"t3.mem_group_name as level_name",
		));
		$this->db->from('coop_loan');
		$this->db->join("coop_mem_apply", "coop_mem_apply.member_id = coop_loan.member_id");
		$this->db->join("coop_mem_group as t1", "t1.id = coop_mem_apply.department");
		$this->db->join("coop_mem_group as t2", "t2.id = coop_mem_apply.faction");
		$this->db->join("coop_mem_group as t3", "t3.id = coop_mem_apply.level");
		$this->db->where($where);
		$this->db->group_by("member_id");
		$row = $this->db->get()->result_array();
		$arr_data['data'] = $row;

		$this->load->view('ajax/search_loan_guarantee_jquery',$arr_data);
	}

	public function search_loan_guarantee_by_member_id()
	{
		$search_text = @$_POST["search_text"];
		// echo $search_text;
		// exit;
		$this->db->where(" coop_mem_apply.member_id = '".str_pad($search_text,6 ,0,STR_PAD_LEFT)."'");
		// $where =
		// echo $where;
		// exit;
		$where .= "AND member_status = 1";
		$this->db->select(array(
			"coop_mem_apply.member_id",
			"firstname_th",
			"lastname_th",
			"t1.mem_group_name as department_name",
			"t2.mem_group_name as faction_name",
			"t3.mem_group_name as level_name",
			"coop_loan.id as loan_id",
			"coop_loan_guarantee_person.id as gua_id"
		));
		$this->db->from('coop_mem_apply');
		$this->db->join("coop_mem_group as t1", "t1.id = coop_mem_apply.department");
		$this->db->join("coop_mem_group as t2", "t2.id = coop_mem_apply.faction");
		$this->db->join("coop_mem_group as t3", "t3.id = coop_mem_apply.level");
		$this->db->join("coop_loan", "coop_mem_apply.member_id = coop_loan.member_id", "left");
		$this->db->join("coop_loan_guarantee_person", "coop_mem_apply.member_id = coop_loan_guarantee_person.guarantee_person_id", "left");
		$this->db->where("member_status = 1");
		$this->db->limit(1);
		$row = $this->db->get()->result_array();
		if ($row && (!empty($row[0]["loan_id"]) || !empty($row[0]["gua_id"]))) {
			echo json_encode($row[0]);
		}else{
			echo "FALSE";
		}
	}

	public function get_loan_guarantee(){
		$search_text = trim(@$_POST["member_id"]);
		$where = " member_id = '".$search_text."' AND loan_status in (1,2,6)";
		// $where .= "AND member_status = 1";
		$status = ["รออนุมัติ", "อนุมัติ", "ขอยกเลิก", "อนุมัติยกเลิก", "ชำระเงินครบถ้วน", "ไม่อนุมัติ", "เบี้ยวหนี้"];
		$this->db->select(array(
			"id",
			"contract_number",
			"FORMAT(loan_amount, 0) as loan_amount",
			"loan_status",
			"loan_type",
			"t8.loan_name as loan_type_detail",
			"t8.loan_name_description"
		));
		$this->db->from('coop_loan');
		$this->db->join('coop_loan_name as t8','coop_loan.loan_type = t8.loan_name_id','inner');
		$this->db->where($where);
		$row = $this->db->get()->result_array();
		foreach ($row as $key => $value) {
			$loan_name_short = $this->center_function->convert_loan_short(@$row[$key]['loan_type']);
			$this->db->select(array(
				"concat(firstname_th, ' ', lastname_th) as name",
				"guarantee_person_amount",
				"guarantee_person_amount_balance",
				"coop_loan_guarantee_person.guarantee_person_id as member_id",
				"coop_loan_guarantee_person.id"
			));
			$this->db->join("coop_mem_apply", "coop_loan_guarantee_person.guarantee_person_id = coop_mem_apply.member_id");
			$this->db->where("member_id != ''");
			$guarantee = $this->db->get_where("coop_loan_guarantee_person", array(
				"loan_id" => $value['id'],
			))->result_array();
			$row[$key]['have_guarantee'] = $guarantee;
			$row[$key]['status'] = $status[$value['loan_status']];
			$row[$key]['contract_number'] = $loan_name_short.$value['contract_number'];
		}



		$this->db->select(array(
			"loan_id",
			"coop_loan.member_id",
			"coop_loan.loan_type"
		));
		$this->db->join("coop_loan", "coop_loan.id = coop_loan_guarantee_person.loan_id and coop_loan.loan_status in (1,2,6)");
		$row2 = $this->db->get_where("coop_loan_guarantee_person", array(
			"guarantee_person_id" => $search_text,
		))->result_array();
		$total_guarantee = 0;
		foreach ($row2 as $key => $value) {
			$guarantee_person_id = $value['member_id'];
			$share = $this->db->query("select share_collect_value from coop_mem_share where coop_mem_share.member_id = '$guarantee_person_id' and share_status not in (0,3) order by share_date desc limit 1 ")->result_array()[0]['share_collect_value'];
			$share = ($share == "") ? 0 : $share;
			$deposit_balance = @$this->db->query("select transaction_balance from coop_account_transaction where account_id = (select coop_maco_account.account_id from coop_maco_account where account_status = '0' AND mem_id = '$guarantee_person_id' and coop_maco_account.type_id = (select coop_deposit_type_setting.type_id from coop_deposit_type_setting where deduct_loan = 1 limit 1) )")->result_array()[0]['transaction_balance'];
			$deposit_balance = ($deposit_balance == "") ? 0 : $deposit_balance;
			$this->db->select(array(
				"concat(firstname_th, ' ', lastname_th) as name",
				"contract_number",
				"coop_loan.member_id",
				"coop_loan.loan_type",
				"t8.loan_name as loan_type_detail",
				"t8.loan_name_description",
				"FORMAT(loan_amount, 0) loan_amount" ,
				"FORMAT(loan_amount_balance, 0) as loan_amount_balance",
				"FORMAT(ABS((loan_amount_balance - $share + $deposit_balance)) / (select count(*) from coop_loan_guarantee_person where loan_id = coop_loan.id group by loan_id), 0) as guarantee_person_amount",
				"IF(loan_status=0, 'รออนุมัติ', IF(loan_status=1, 'อนุมัติ', IF(loan_status=2, 'ขอยกเลิก', IF(loan_status=3, 'อนุมัติยกเลิก', IF(loan_status=4, 'ชำระเงินครบถ้วน', IF(loan_status=5, 'ไม่อนุมัติ', IF(loan_status=6, 'เบี้ยวหนี้', ''))))))) as status",
				"(select count(*) from coop_loan_guarantee_person where loan_id = coop_loan.id group by loan_id) as count"
			));
			$this->db->join("coop_mem_apply", "coop_loan.member_id = coop_mem_apply.member_id");
			$this->db->join('coop_loan_name as t8','coop_loan.loan_type = t8.loan_name_id','inner');
			$guarantee = $this->db->get_where("coop_loan", array(
				"coop_loan.id" => $value['loan_id'],
			))->result_array();

			foreach ($guarantee as $k1 => $v1) {
				$loan_name_short = $this->center_function->convert_loan_short(@$v1['loan_type']);
				$guarantee[$k1]['loan_name_short']=$loan_name_short;
			}
			$row2[$key]['guarantee'] = $guarantee;
			foreach ($guarantee as $key_1 => $value_1) {

				$total_guarantee += implode("", explode(",", $value_1['guarantee_person_amount']));
			}
		}

		$this->db->select("(salary + IF(other_income, other_income, 0) ) as income");
		$income = @$this->db->get_where("coop_mem_apply", array(
			"member_id" => $search_text
		))->result_array()[0]['income'];

		$row3 = array(
			"income" => number_format($income * 100, 2),
			"total_guarantee" => number_format($total_guarantee, 2),
			"amount_total_guarantee" => number_format(($income * 100) - $total_guarantee,2)
		);

		header('Content-Type: application/json');
		echo json_encode(array("name" => $name, "guarantee_1" => $row, "guarantee_2" => $row2, "guarantee_3" => $row3));
	}

/****************************************************************
   * คืนเงินด้วยตัวเอง
   */
	public function fmr_get_member_desc() {
		//$member_id = sprintf('%06d', $_POST['member_id']);
		if($this->input->post('date')){
            $date = $this->center_function->ConvertToSQLDate($this->input->post('date'));
        }else{
            $date = date("Y-m-t");
        }
		$member_id = $this->center_function->complete_member_id($_POST['member_id']);
		$json = [
							'is_found' => 0,
							'member' => [
								'member_id' => '',
								'member_name' => ''
							],
							'loan' => [],
							'loan_atm' => []
						];

		$sql = "SELECT member_id, CONCAT(firstname_th, ' ', lastname_th) member_name
						FROM coop_mem_apply
						WHERE member_id = '{$member_id}'";
		$rs = $this->db->query($sql);
		if( $rs->num_rows() > 0 ) {
			$json['is_found'] = 1;
			$row = $rs->result_array()[0];
			$json['member'] = [
				'member_id' => $row['member_id'],
				'member_name' => $row['member_name'],
			];
			$loan = $this->Return->getLoanList($member_id, date('Y-m-d', strtotime($date." -2 month")), date('Y-m-d', strtotime($date)) );
			$loan_list = array_unique(array_filter(array_column($loan, 'loan_id')));
            $loan_where = "";
            if(sizeof($loan_list)){
                $loan_where = " AND (coop_loan.loan_status=1 OR coop_loan.id in (".implode(",", $loan_list)."))";
            }

			unset($sql, $rs, $row, $loan_list);
			$sql = "SELECT 	coop_loan.id loan_id,coop_loan.contract_number,coop_term_of_loan.prefix_code
							FROM coop_loan
							LEFT JOIN (SELECT type_id, prefix_code FROM coop_term_of_loan GROUP BY type_id) as coop_term_of_loan ON coop_loan.loan_type = coop_term_of_loan.type_id
							WHERE coop_loan.member_id = '{$member_id}' ".$loan_where."
							ORDER BY coop_loan.contract_number";
							
			$rs = $this->db->query($sql);
			foreach ($rs->result_array() as $row) {
				$json['loan'][] = [
					'loan_id' => $row['loan_id'],
					'contract_number' => $row['prefix_code'].$row['contract_number']
				
				];
			}
			
			unset($sql, $rs, $row);
			$sql = "SELECT loan_atm_id, contract_number
							FROM coop_loan_atm
							WHERE member_id = '{$member_id}'
							ORDER BY contract_number";
			$rs = $this->db->query($sql);
			foreach ($rs->result_array() as $row) {
				$json['loan_atm'][] = [
					'loan_atm_id' => $row['loan_atm_id'],
					'contract_number' => $row['contract_number']
				];
			}
			
			unset($sql, $rs, $row);
			// บัญชีเงินฝาก
			$begin=date('Y-m-d', strtotime($date." -2 month"));
			$end=date('Y-m-d', strtotime($date));
			$deposit = "SELECT t4.account_id,t1.receipt_id,t1.principal_payment
				FROM coop_finance_transaction AS t1
				INNER JOIN coop_receipt AS t2 ON t1.receipt_id = t2.receipt_id
				INNER JOIN coop_finance_month_detail AS t3 ON t2.finance_month_profile_id = t3.profile_id AND t1.member_id = t3.member_id  AND t3.deduct_code = 'DEPOSIT'  AND t3.run_status = '1'
				INNER JOIN coop_maco_account AS t4 ON t3.deposit_account_id = t4.account_id
				WHERE t1.member_id='".$member_id."' AND t1.account_list_id = '30' AND t1.payment_date BETWEEN '".$begin."' AND '".$end."' AND t2.finance_month_profile_id IS NOT NULL " ;
			$deposit_list = $this->db->query($deposit)->result_array();
			$deposit_list_account_id = array_unique(array_filter(array_column($deposit_list, 'account_id')));
				if(sizeof($deposit_list_account_id)) {
					foreach ($deposit_list_account_id as $row_deposit) {
						$json['deposit'][] = [
							'account_id' => $row_deposit,
							'account_id_show' =>$this->center_function->format_account_number($row_deposit),
						];
					}
				}else{
					$json['deposit'] = [];
				}
		
			///เช็คเงินฝากบัญชี 21 ที่เปิดใช้งาน
			$sql = "SELECT account_id
						FROM coop_maco_account
						WHERE type_id = '2'
							AND account_status = '0'
							AND mem_id = '{$member_id}'";
			$rs = $this->db->query($sql);
			$row = $rs->result_array()[0];
			$json['account'] = [
				'account_id' => $row['account_id']
			];
		}

		echo json_encode( $json );
	}
	public function receipt_list(){
	    $res = array();

	    $_id = $this->input->post('id');
	    $arr = explode("#", $_id);
	    $member_id = $this->input->post('member_id');
	
        $where = "";
	    if(strtoupper($arr[0]) == "LOAN_ATM"){
	        $where = "AND loan_atm_id='{$arr[1]}'";
        }else if(strtoupper($arr[0]) == "LOAN"){
            $where = "AND loan_id='{$arr[1]}'";
        }else if(strtoupper($arr[0]) == "SHARE"){
            $where = "AND account_list_id IN (14,16) AND member_id='{$arr[1]}'";
        }else if(strtoupper($arr[0]) == "DEPOSIT"){
            $where = "AND account_list_id ='30' ";
        }
	
	    $_date = $this->input->post('date');
	    $date = $this->center_function->ConvertToSQLDate($_date);
		$start_date=date('Y-m-d', strtotime($date." -2 month"));
		$end_date=date('Y-m-t', strtotime($date));

	    $sql = "SELECT receipt_id,SUM(principal_payment) principal_payment, SUM(interest) interest, SUM(total_amount) total_amount FROM coop_finance_transaction 
		WHERE member_id='{$member_id}' AND payment_date BETWEEN '{$start_date}' AND '{$end_date}' ".$where.'GROUP BY member_id,receipt_id,loan_id'; 
	    $result = $this->db->query($sql)->result_array();
		if(sizeof($result)){
	        $res['status'] = 200;
	        $res['status_code'] = "success";
	        $res['msg'] = $sql;
	        foreach ($result as $key => $item){
	            $res['receipt'][] = [
	                'receipt_id' => $item['receipt_id'],
	                'principal' => $item['principal_payment'],
	                'interest' => $item['interest'],
	                'total' => $item['total_amount'],
                ];
            }
        }else{
            $res['status'] = 400;
            $res["msg"] = $sql;
            $res['status_code'] = "empty";
        }
        $this->output->set_content_type("application/json", "utf8")->_display();
        echo json_encode($res);
        exit;
    }
	public function fmr_exec() {
		//echo 'AAAA'; exit;
		$json = [
			'success' => 0,
			'loan_id' => '',
			'account_id' => '',
			'link_account' => '',
			'link_loan' => '',
			'error_msg' => ''
		];
		//$member_id = sprintf('%06d', $_POST['member_id']);
		$member_id = $this->center_function->complete_member_id($_POST['member_id']);
		$loan_type = explode('#', $_POST['loan_id'], 2)[0];
		$pay_type = @$_POST['pay_type'];
		// pay_type -> 0 = เงินสด ,1 =เงินโอน

		if( $loan_type == 'loan' ) {
			$loan_id = explode('#', $_POST['loan_id'], 2)[1];
			$loan_atm_id = '';
			$json['link_loan'] = "/loan/loan_payment_detail/{$loan_id}";

		} elseif($loan_type == 'loan_atm') {
			$loan_id = '';
			$loan_atm_id = explode('#', $_POST['loan_id'], 2)[1];
			$json['link_loan'] = "/loan_atm/loan_atm_payment_detail/{$loan_atm_id}";
		}elseif($loan_type == 'deposit') {
			$loan_id = '';
			$loan_atm_id = '';
			$account_post = explode('#', $_POST['loan_id'], 2)[1];
			// $json['link_loan'] = "/loan_atm/loan_atm_payment_detail/{$loan_atm_id}";
		}
		
		$return_type = (int)$_POST['return_type'];
		$return_desc = $_POST['return_desc'];
		$return_principal = (double)$_POST['return_principal'];
		$return_interest = (double)$_POST['return_interest'];

		$checke_account = 0 ;
		$account_id = '';
		if($pay_type == '0') {
			$checke_account = 1;	
		}else{
			$sql = "SELECT account_id
							FROM coop_maco_account
							WHERE type_id = '2'
								AND account_status = '0'
								AND mem_id = '{$member_id}'";
			$rs_account = $this->db->query($sql);
			if( $rs_account->num_rows() ) {
				$checke_account = 1;
				$account_id = $rs_account->result_array()[0];
				$account_id = $account_id['account_id'];
			}
		}

		if($loan_type == 'deposit' && $account_id == ''){
			$account_id = $account_post;
		}

		if($loan_type == 'deposit'){
			$pay_description = $this->center_function->format_account_number($account_id);
		}elseif($loan_type == 'loan'){
			$loan =  $this->db->select(array('prefix_code', 'contract_number'))->from('coop_term_of_loan t1')
			->join('coop_loan t2', 't1.type_id=t2.loan_type', 'inner')
			->where('t2.id', $loan_id)
			->limit(1)->get()->row_array();
			$pay_description = $loan['prefix_code'].$loan['contract_number'];
		}
		
		if($checke_account == 1) {
			$year = date('Y') + 543 ;
			$month = date('m');
			$sql = "SELECT bill_id
							FROM coop_process_return
							WHERE bill_id LIKE 'R{$year}{$month}%'
							ORDER BY bill_id DESC
							LIMIT 1";
			$rs = $this->db->query($sql);
			if( !$rs->num_rows() ) $_bill_id = 1;
			else {
				$row = $rs->result_array()[0];
				$_bill_id = (int)substr($row['bill_id'], 7, 5) + 1;
			}

			$sql = "SELECT transaction_balance
							FROM coop_account_transaction
							WHERE account_id = '{$account_id}'
							ORDER BY transaction_time DESC, transaction_id DESC
							LIMIT 1";
			$rs_balance = $this->db->query($sql);
			$row_balance = $rs_balance->result_array()[0];
			$return_amount = $return_principal + $return_interest;
			$transaction_deposit = $return_amount;
			$transaction_balance = $return_amount + $row_balance['transaction_balance'];

			$this->load->model("Receipt_model", "receipt_model");	
			$return_by_self_date = date('Y-m-d');
			$pay_type = '8';
			$bill_id = $this->receipt_model->create_receipt($return_by_self_date, $pay_type);
			// $bill_id = sprintf('R%s%s%05d', $year, $month, $_bill_id++);
			$return_time = date('Y-m-d H:i:s');

			$data_insert = array();
			$data_insert['total_amount'] = $return_amount;
			$data_insert['buy_date'] = $return_time;
			$data_insert['account_buy_number'] = $bill_id;
			$data_insert['pay_for'] = 'เงินคืน';
			$data_insert['pay_type'] = $pay_type == '0' ? 'cash' : 'transfer' ;
			$data_insert['account_buy_status'] = '0';
			$data_insert['cashpay_type'] = 'receipt';
			$this->db->insert('coop_account_buy', $data_insert);
			$account_buy_id = $this->db->insert_id();
			$data_inserts['account_buy_id'] = $account_buy_id;
			$data_inserts['pay_amount'] = $return_amount;
			$data_inserts['pay_description'] = 'เงินจ่ายคืน '.$pay_description;
			$data_inserts['bill_number'] = $bill_id;
			$this->db->insert('coop_account_buy_detail', $data_inserts);
	
			
			
			
			/*$sql = "INSERT INTO coop_account_transaction(transaction_time, transaction_list, transaction_withdrawal, transaction_deposit, transaction_balance, account_id, user_id, transaction_text)
							VALUES(NOW(), 'REVD', 0, {$transaction_deposit}, {$transaction_balance}, '{$account_id}', '{$_SESSION['USER_ID']}', 'return_interest_manual')";
			$this->db->query($sql);
			*/
			//$json['sql'][] = $sql;
			$sql = "INSERT INTO coop_process_return(member_id, loan_id, loan_atm_id, return_type, account_id, receipt_id, bill_id, return_principal, return_interest, return_amount, return_year, return_month, return_time, return_desc, user_id)
							VALUES('{$member_id}', '{$loan_id}', '{$loan_atm_id}', {$return_type}, '{$account_id}', '', '{$bill_id}', {$return_principal}, {$return_interest}, {$return_amount}, YEAR(CURDATE()), MONTH(CURDATE()), NOW(), '{$return_desc}', '{$_SESSION['USER_ID']}')";
			$this->db->query($sql);
			//$json['sql'][] = $sql;
			
			if( $loan_atm_id && $return_principal ) {
				$sql = "SELECT loan_amount_balance
								FROM coop_loan_atm_transaction
								WHERE loan_atm_id = '{$loan_atm_id}'
								ORDER BY loan_atm_transaction_id DESC
								LIMIT 1";
				$rs_trans_atm = $this->db->query($sql);
				$row_trans_atm = $rs_trans_atm->result_array()[0];
				$loan_amount_balance = (double)$row_trans_atm['loan_amount_balance'];

				if( $loan_amount_balance < 0 ) {
					$loan_amount_balance += $return_principal;
					$sql = "INSERT INTO coop_loan_atm_transaction(loan_atm_id, loan_amount_balance, transaction_datetime, receipt_id)
									VALUES('{$loan_atm_id}', {$loan_amount_balance}, '{$return_time}', '{$bill_id}')";
					@$this->db->query($sql);
					$sql = "UPDATE coop_loan_atm SET
									total_amount_balance = total_amount_approve - {$loan_amount_balance}
									WHERE loan_atm_id = '{$loan_atm_id}'";
					@$this->db->query($sql);
				}
			}
			
			//ตัดยอดเงินกู้สามัญที่ติดลบ
			if($loan_id &&  $return_principal ) {
				$sql = "SELECT loan_amount_balance
								FROM coop_loan_transaction
								WHERE loan_id = '{$loan_id}'
								ORDER BY loan_transaction_id DESC
								LIMIT 1";
				$rs_trans_loan = $this->db->query($sql);
				$row_trans_loan = $rs_trans_loan->result_array()[0];
				$loan_amount_balance = (double)$row_trans_loan['loan_amount_balance'];

				if( $loan_amount_balance < 0 ) {
					$loan_amount_balance += $return_principal;
					$sql = "INSERT INTO coop_loan_transaction(loan_id, loan_amount_balance, transaction_datetime ,receipt_id)
									VALUES('{$loan_id}', {$loan_amount_balance}, '{$return_time}', '{$bill_id}')";
					@$this->db->query($sql);
					$sql = "UPDATE coop_loan SET
									loan_amount_balance = {$loan_amount_balance}
									WHERE id = '{$loan_id}'";
					@$this->db->query($sql);
				}
			}
			//exit;
			$json['loan_id'] = $loan_id;
			$json['account_id'] = $account_id;
			$json['link_account'] = "/save_money/account_detail?account_id={$account_id}";
			$json['receipt_return_self'] = "/admin/receipt_return_self?member_id={$member_id}&&loan_id={$loan_id}&&bill_id={$bill_id}";
			$json['success'] = 1;
		} else {
			$json['error_msg'] = 'ไม่พบบัญชี 21';
		}

		echo json_encode( $json );
	}
		/****************************************************************
 		* แก้ไข Statement
		*/
		public function fse_get_return_statement() {
			$member_id = $this->center_function->complete_member_id($_POST['member_id']);
			$loan_id = explode('#', $_POST['loan_id'], 2)[1];
			$return_type = (int)$_POST['return_type'];
			$json = [
								'is_found' => 0,
								'statement' => []
							];

			if( in_array($return_type, [1, 2]) ) $where_loan_id = " AND loan_id = '{$loan_id}' ";
			else  $where_loan_id = " AND loan_atm_id = '{$loan_id}' ";
			$sql = "SELECT *, DATE(return_time) return_date
							FROM coop_process_return
							WHERE member_id = '{$member_id}'
								{$where_loan_id}
						AND return_type = {$return_type}
						ORDER BY return_time DESC";
			$rs = $this->db->query($sql);
			if( $rs->num_rows() ) {
				$json['is_found'] = 1;
				foreach ($rs->result_array() as $row) {
					$json['statement'][] = [
						'ret_id' => $row['ret_id'],
						'return_date' => $this->center_function->ConvertToThaiDate($row['return_date']),
						'bill_id' => $row['bill_id'],
						'receipt_id' => $row['receipt_id'],
						'account_id' => $row['account_id'],
						'return_principal' => $row['return_principal'],
						'return_interest' => $row['return_interest'],
						'return_amount' => $row['return_amount'],
						'return_desc' => (empty($row['receipt_id']) ? 'ชำระเงินอื่นๆ' : "เงินคืน {$row['receipt_id']}")
					];
				}
			}

			echo json_encode( $json );
		}

		public function fse_confirm_del() {
			$ret_id = (int)$_POST['ret_id'];
			$return_amount = (double)$_POST['return_amount'];
			$account_id = $_POST['account_id'];

			$sql = "SELECT transaction_balance
					FROM coop_account_transaction
					WHERE account_id = '{$account_id}'
					ORDER BY transaction_time DESC, transaction_id DESC
					LIMIT 1";
			$rs_balance = $this->db->query($sql);
			$row_balance = $rs_balance->result_array()[0];
			$transaction_deposit = 0;
			$transaction_withdrawal = $return_amount;
			$transaction_balance = $row_balance['transaction_balance'] - $transaction_withdrawal;
			$bill_id = sprintf('R%s%s%05d', $year, $month, $_bill_id++);


			$sql = "INSERT INTO coop_account_transaction(transaction_time, transaction_list, transaction_withdrawal, transaction_deposit, transaction_balance, account_id, user_id ,transaction_text)
					VALUES(NOW(), 'ERR', {$transaction_withdrawal}, {$transaction_deposit}, {$transaction_balance}, '{$account_id}', '{$_SESSION['USER_ID']}', 'statement_edit')";
			$this->db->query($sql);

			$sql = "DELETE FROM coop_process_return WHERE ret_id = {$ret_id}";
			$this->db->query($sql);

		}
		
		function cal_process_return_type_5($data){
			$return_type = (int)$data['ret_type'];
			$date_s = $this->center_function->ConvertToSQLDate($data['date_s']);
			$date_e = $this->center_function->ConvertToSQLDate($data['date_e']);
			$year = date('Y') + 543 ;
			$month = explode('-', $date_s)[1];
			$sql = "SELECT bill_id
						FROM coop_process_return
						WHERE bill_id LIKE 'R{$year}{$month}%'
						ORDER BY bill_id DESC
						LIMIT 1";
			$rs = $this->db->query($sql);
			if( !$rs->num_rows() ){
				$_bill_id = 1;
			}else {
				$row = $rs->result_array()[0];
				$_bill_id = (int)substr($row['bill_id'], 7, 5) + 1;
			}
			
			$arr_data = array();
			$arr_receipt_id = array();
			foreach($data['check_return'] as $key => $value ) {
				$member_id = $data['member_id'][$key];
				$receipt_id = $data['receipt_id'][$key];
				$arr_data[$key]['member_id']  = $data['member_id'][$key];
				$arr_data[$key]['receipt_id']  = $data['receipt_id'][$key];
				$arr_receipt_id[] =  $data['receipt_id'][$key];
			}				
			
			//คืนเงินลาออก ถอนเงินออกจากบัญชีเงินฝาก							
			$sql_detail = "SELECT
							t1.receipt_id,
							SUM(t1.principal_payment) principal_payment,
							SUM(t1.interest) interest,
							SUM(t1.total_amount) total_amount,
							t1.member_id,
							t1.loan_id,
							t1.loan_atm_id,
							t1.account_list_id,
							t1.transaction_text,
							t2.account_list,
							t3.contract_number,
							t4.contract_number AS contract_number_atm,
							t5.account_id
						FROM
							coop_finance_transaction AS t1
						LEFT OUTER JOIN coop_account_list AS t2 ON t1.account_list_id = t2.account_id
						LEFT OUTER JOIN coop_loan AS t3 ON t1.loan_id = t3.id
						LEFT OUTER JOIN coop_loan_atm AS t4 ON t1.loan_atm_id = t4.loan_atm_id
						LEFT OUTER JOIN (SELECT account_id,mem_id FROM coop_maco_account AS t1 WHERE t1.type_id = '2' AND t1.account_status = '1') AS t5 ON t1.member_id = t5.mem_id AND t1.account_list_id = '30'
						WHERE
							t1.receipt_id IN ('".implode("','", $arr_receipt_id)."') AND t1.account_list_id IN (15,16,30,31)
						GROUP BY t1.member_id,t1.receipt_id,t1.account_list_id,t1.loan_id,t1.loan_atm_id ";		
				//echo '<pre>'; print_r($arr_receipt_id); echo '</pre>';			
				//echo $sql_detail.'<hr>'; //exit;
				$row_5_detail = $this->db->query($sql_detail)->result_array();
				$arr_data_detail = array();
				foreach ($row_5_detail as $key_detail => $value_detail) {
					$receipt_id = $value_detail['receipt_id'];
					$member_id = $value_detail['member_id'];
					//echo '<pre>'; print_r($value_detail); echo '</pre>';
					$account_id = @$value_detail['account_id'];					
					$return_principal = @$value_detail['principal_payment'];
					$return_interest = @$value_detail['interest'];
					$return_amount = @$value_detail['total_amount'];
					$loan_id = @$value_detail['loan_id'];
					$loan_atm_id = @$value_detail['loan_atm_id'];
					$bill_id = sprintf('R%s%s%05d', $year, $month, $_bill_id++);
					$return_time = date('Y-m-d H:i:s');
					//coop_account_list
					
					//15 = ชำระเงินกู้  loan
					if($value_detail['account_list_id'] == '15' ){
						$sql = "INSERT INTO coop_process_return(member_id, loan_id, return_type, account_id, receipt_id, bill_id, return_principal, return_interest, return_amount, return_year, return_month, return_time, user_id)
							VALUES('{$member_id}', '{$loan_id}', {$return_type}, '{$account_id}', '{$receipt_id}', '{$bill_id}', {$return_principal}, {$return_interest}, {$return_amount}, YEAR('{$date_e}'), MONTH('{$date_e}'), NOW() ,'{$_SESSION['USER_ID']}')";
						@$this->db->query($sql);
						//echo $sql.'<hr>';
						
						$sql = "INSERT INTO coop_process_return_resign(member_id, loan_id, return_type, account_id, receipt_id, return_principal, return_interest, return_amount, return_year, return_month, return_time, user_id, account_list_id)
											VALUES('{$member_id}', '{$loan_id}', {$return_type}, '{$account_id}', '{$receipt_id}', {$return_principal}, {$return_interest}, {$return_amount}, YEAR('{$date_e}'), MONTH('{$date_e}'), NOW(), '{$_SESSION['USER_ID']}', '{$value_detail['account_list_id']}')";
						@$this->db->query($sql);
						
						//ตัดยอดเงินกู้สามัญที่ติดลบ
						if( $return_principal ) {
							$sql = "SELECT loan_amount_balance
											FROM coop_loan_transaction
											WHERE loan_id = '{$loan_id}'
											ORDER BY loan_transaction_id DESC
											LIMIT 1";
							$rs_trans_loan = $this->db->query($sql);
							$row_trans_loan = $rs_trans_loan->result_array()[0];
							$loan_amount_balance = (double)$row_trans_loan['loan_amount_balance'];

							if( $loan_amount_balance < 0 ) {
								$loan_amount_balance += $return_principal;
								$sql = "INSERT INTO coop_loan_transaction(loan_id, loan_amount_balance, transaction_datetime ,receipt_id)
												VALUES('{$loan_id}', {$loan_amount_balance}, '{$return_time}', '{$bill_id}')";
								@$this->db->query($sql);
								$sql = "UPDATE coop_loan SET
												loan_amount_balance = {$loan_amount_balance}
												WHERE id = '{$loan_id}'";
								@$this->db->query($sql);
							}
						}
					}
					
					//16 = ชำระเงินค่าหุ้นรายเดือน
					if($value_detail['account_list_id'] == '16'){
						$sql = "INSERT INTO coop_process_return_resign(member_id, return_type, account_id, receipt_id, return_principal, return_interest, return_amount, return_year, return_month, return_time, user_id, account_list_id)
											VALUES('{$member_id}', {$return_type}, '{$account_id}', '{$receipt_id}', {$return_principal}, {$return_interest}, {$return_amount}, YEAR('{$date_e}'), MONTH('{$date_e}'), NOW(), '{$_SESSION['USER_ID']}', '{$value_detail['account_list_id']}')";
						@$this->db->query($sql);
									
						$this->db->select('setting_value');
						$this->db->from('coop_share_setting');
						$this->db->order_by('setting_id DESC');
						$row = $this->db->get()->result_array();
						$share_setting = $row[0];
						
						$this->db->select('share_collect,share_collect_value');
						$this->db->from('coop_mem_share');
						$this->db->where("member_id = '".$member_id."' AND share_status = '1'");
						$this->db->order_by('share_date DESC, share_id DESC');
						$this->db->limit(1);
						$row_share = $this->db->get()->result_array();
						$row_share = @$row_share[0];
						//echo '<pre>'; print_r($row_share); echo '</pre>';
						
						$data_insert = array();
						$data_insert['member_id'] = $member_id;
						$data_insert['admin_id'] = $_SESSION['USER_ID'];
						$data_insert['share_type'] = 'REVD';
						$data_insert['share_date'] = @$return_time;
						$data_insert['share_payable'] = @$row_share['share_collect'];
						$data_insert['share_payable_value'] = @$row_share['share_collect_value'];
						$data_insert['share_early'] = @$return_amount/@$share_setting['setting_value'];
						$data_insert['share_early_value'] = @$return_amount;
						$data_insert['share_collect'] = @$row_share['share_collect'] - (@$return_amount/@$share_setting['setting_value']);
						$data_insert['share_collect_value'] = @$row_share['share_collect_value']-@$return_amount;
						$data_insert['share_value'] = @$share_setting['setting_value'];
						$data_insert['share_status'] = '1';
						$data_insert['pay_type'] = '0';
						$data_insert['share_bill'] = @$bill_id;						
						$this->db->insert('coop_mem_share', $data_insert);
						//echo '<pre>'; print_r($data_insert); echo '</pre>';
						//exit;
					}
					
					//30 = เงินฝาก
					if($value_detail['account_list_id'] == '30'){
						//คืนเงิน โดยการถอนออกจาบัญชี
						$sql = "SELECT account_id
							FROM coop_maco_account
							WHERE type_id = '2'
								AND account_id = '{$account_id}'";
						$rs_account = $this->db->query($sql);

						if( $rs_account->num_rows() ) {
							$account_id = $rs_account->result_array()[0];
							$account_id = $account_id['account_id'];
							
							if( $account_id ) {
								$return_amount = $return_amount;
								if( $return_amount ) {
									
									$sql = "INSERT INTO coop_process_return_resign(member_id, return_type, account_id, receipt_id, return_principal, return_interest, return_amount, return_year, return_month, return_time, user_id, account_list_id)
											VALUES('{$member_id}', {$return_type}, '{$account_id}', '{$receipt_id}', {$return_principal}, {$return_interest}, {$return_amount}, YEAR('{$date_e}'), MONTH('{$date_e}'), NOW(), '{$_SESSION['USER_ID']}', '{$value_detail['account_list_id']}')";
									@$this->db->query($sql);
						
									$sql = "SELECT transaction_balance
													FROM coop_account_transaction
													WHERE account_id = '{$account_id}'
													ORDER BY transaction_time DESC, transaction_id DESC
													LIMIT 1";
									$rs_balance = $this->db->query($sql);
									$row_balance = $rs_balance->result_array()[0];
									$transaction_withdrawal = $return_amount;
									$transaction_deposit = 0;
									$transaction_balance = $row_balance['transaction_balance']-$return_amount;

									$sql = "INSERT INTO coop_account_transaction(transaction_time, transaction_list, transaction_withdrawal, transaction_deposit, transaction_balance, account_id, user_id ,transaction_text)
													VALUES(NOW(), 'REVD', {$transaction_withdrawal}, {$transaction_deposit}, {$transaction_balance}, '{$account_id}','{$_SESSION['USER_ID']}' , 'process_interest_edit')";
									$this->db->query($sql);				
								}
							}
						}
					}		
					
					//31 = ชำระเงินกู้ฉุกเฉินATM loan_atm
					if($value_detail['account_list_id'] == '31'){
						$sql = "INSERT INTO coop_process_return(member_id, loan_atm_id, return_type, account_id, receipt_id, bill_id, return_principal, return_interest, return_amount, return_year, return_month, return_time, user_id)
							VALUES('{$member_id}', '{$loan_atm_id}', {$return_type}, '{$account_id}', '{$receipt_id}', '{$bill_id}', {$return_principal}, {$return_interest}, {$return_amount}, YEAR('{$date_e}'), MONTH('{$date_e}'), NOW(), '{$_SESSION['USER_ID']}')";
						@$this->db->query($sql);
						
						$sql = "INSERT INTO coop_process_return_resign(member_id, loan_atm_id, return_type, account_id, receipt_id, return_principal, return_interest, return_amount, return_year, return_month, return_time, user_id, account_list_id)
							VALUES('{$member_id}', '{$loan_atm_id}', {$return_type}, '{$account_id}', '{$receipt_id}', {$return_principal}, {$return_interest}, {$return_amount}, YEAR('{$date_e}'), MONTH('{$date_e}'), NOW(), '{$_SESSION['USER_ID']}', '{$value_detail['account_list_id']}')";
						@$this->db->query($sql);
						
						if( $return_principal ) {
							$sql = "SELECT loan_amount_balance
											FROM coop_loan_atm_transaction
											WHERE loan_atm_id = '{$loan_atm_id}'
											ORDER BY loan_atm_transaction_id DESC
											LIMIT 1";
							$rs_trans_atm = $this->db->query($sql);
							$row_trans_atm = $rs_trans_atm->result_array()[0];
							$loan_amount_balance = (double)$row_trans_atm['loan_amount_balance'];

							if( $loan_amount_balance < 0 ) {
								$loan_amount_balance += $return_principal;
								$sql = "INSERT INTO coop_loan_atm_transaction(loan_atm_id, loan_amount_balance, transaction_datetime, receipt_id)
												VALUES('{$loan_atm_id}', {$loan_amount_balance}, '{$return_time}', '{$bill_id}')";
								@$this->db->query($sql);
								$sql = "UPDATE coop_loan_atm SET
												total_amount_balance = total_amount_approve - {$loan_amount_balance}
												WHERE loan_atm_id = '{$loan_atm_id}'";
								@$this->db->query($sql);
							}
						}	
					}
				}	
			//var_dump($_POST);
			//exit;			
		}

	public function get_bank_branch_by_bank_id() {
		$bank_id = $_GET["bank_id"];
		$branchs = $this->db->select("*")->from("coop_bank_branch")->where("bank_id = '".$_GET["bank_id"]."'")->get()->result_array();
		echo json_encode($branchs);
		exit;
	}
}
