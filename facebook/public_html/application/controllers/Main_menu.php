<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Main_menu extends CI_Controller {
	function __construct()
	{
		parent::__construct();

	}
	public function index()
	{
		$arr_data = array();
		$this->db->select('*');
		$this->db->from('coop_user_permission');
		$this->db->where("user_id = '".$_SESSION['USER_ID']."'");
		$result = $this->db->get()->result_array();
		foreach($result as $key => $value) {
			$arr_data['permissions'][$value["menu_id"]] = TRUE;
		}

		$this->db->select('COUNT(member_id) as _c');
		$this->db->from('coop_mem_apply');
		$this->db->where("member_status = '1'");
		$result = $this->db->get()->result_array();
		$arr_data['num_rows'] = $result[0]['_c'];

		$y = date("Y");
		$m = date("m");
		$this->db->select('COUNT(member_id) as _c');
		$this->db->from('coop_mem_apply');
		$this->db->where("MONTH(apply_date) = ".$m." AND YEAR(apply_date) = ".$y);
		$result = $this->db->get()->result_array();
		$arr_data['num_m'] = $result[0]['_c'];

		$this->db->select('share_early');
		$this->db->from('coop_mem_share');
		$this->db->where("share_date LIKE '".date('Y-m')."%'");
		$result = $this->db->get()->result_array();
		$share = 0;
		foreach($result as $key => $value) {
			$share += $value['share_early'];
		}
		$arr_data['share'] = $share;

		$this->db->select('sum(pay_amount) as finance_amount');
		$this->db->from('coop_finance_month_detail');
		$this->db->where("profile_id = (select profile_id from coop_finance_month_profile order by profile_id desc limit 1)");
		$result = $this->db->get()->result_array();
		$finance_amount = @$result[0]['finance_amount'];
		$arr_data['finance_amount'] = $finance_amount;
		/*$this->db->select(
			array('t2.id as loan_id',
				't2.loan_amount_balance',
				't2.interest_per_year',
				't5.date_transfer')
		);
		$this->db->from('coop_loan t2');
		$this->db->join('coop_mem_apply t3', 't2.member_id = t3.member_id', 'inner');
		$this->db->join('coop_prename t4', 't3.prename_id = t4.prename_id', 'left');
		$this->db->join('coop_loan_transfer t5', 't2.id = t5.loan_id', 'inner');
		$this->db->where("t2.loan_status = '1' AND t2.loan_amount_balance > 0 AND t2.date_start_period <= '".date('Y-m-t')."'");
		$result = $this->db->get()->result_array();
		//echo $sql_normal_loan;
		$pay_loan = 0;
		foreach($result as $key => $value){
			$this->db->select('principal_payment');
			$this->db->from('coop_loan_period');
			$this->db->where("loan_id = '".$value['loan_id']."'");
			$this->db->limit(1);
			$result = $this->db->get()->result_array();
			$row_principal_payment['principal_payment'] = $result[0]['principal_payment'];

			$date_interesting = date('Y-m-t');
            $this->db->select('payment_date');
            $this->db->from('coop_finance_transaction');
            $this->db->where("loan_id = '".$value['loan_id']."'");
            $this->db->order_by("payment_date", "DESC");
            $this->db->limit(1);
            $result = $this->db->get()->result_array();
            $row_date_prev_paid['payment_date'] = @$result[0]['payment_date'];

			$date_prev_paid = $row_date_prev_paid['payment_date']!=''?$row_date_prev_paid['payment_date']:$value['date_transfer'];
			$diff = date_diff(date_create($date_prev_paid),date_create($date_interesting));
			$date_count = $diff->format("%a");

			$interest = ((($value['loan_amount_balance']*$value['interest_per_year'])/100)/365)*$date_count;

			if($value['loan_amount_balance'] > $row_principal_payment['principal_payment']){
				$principal_payment = $row_principal_payment['principal_payment'];
			}else{
				$principal_payment = $value['loan_amount_balance'];
			}
			$pay_loan += $principal_payment+$interest;
		}
		
		$this->db->select(array('setting_value'));
		$this->db->from('coop_share_setting');
		$this->db->where("setting_id = '1'");
		$row_share_value = $this->db->get()->result_array();
		$share_value = $row_share_value[0]['setting_value'];
		
		$this->db->select(array(
			'member_id',
			'salary'
		));
		$this->db->from('coop_mem_apply as t1');
		$this->db->where("member_status = '1'");
		$rs = $this->db->get()->result_array();
		foreach($rs as $key => $row){
			$this->db->select(array(
				'change_value'
			));
			$this->db->from('coop_change_share');
			$this->db->where("member_id = '".$row['member_id']."' AND change_share_status IN ('1','2')");
			$this->db->order_by('change_share_id DESC');
			$this->db->limit(1);
			$row_change_share = $this->db->get()->result_array();
			$row_change_share = @$row_change_share[0];
			if(@$row_change_share['change_value'] != ''){
				$num_share = $row_change_share['change_value'];
			}else{
				$this->db->select(array(
					'share_salary'
				));
				$this->db->from('coop_share_rule');
				$this->db->where("salary_rule <= '".$row['salary']."'");
				$this->db->order_by('salary_rule DESC');
				$this->db->limit(1);
				$row_share_rule = $this->db->get()->result_array();
				$row_share_rule = @$row_share_rule[0];
				$num_share = $row_share_rule['share_salary'];
			}
			$pay_loan += ($num_share*$share_value);
		}*/
        $arr_data['pay_loan'] = @$pay_loan;
		
		$this->libraries->template('main_menu/index',$arr_data);
	}

	function profile(){
		if($this->input->post()){
			//echo"<pre>";print_r($_POST);print_r($_COOKIE);exit;
			$data = array(
				'password' => $this->input->post('password'),
				'user_department' => $this->input->post('user_department')
			);
			$output_dir = $_SERVER["DOCUMENT_ROOT"].PROJECTPATH."/assets/uploads/user_pic/";
			if(@$_POST['user_pic'] != '') {
				$user_pic = explode('/', $_POST['user_pic']); 
				$user_pic_name = $user_pic[(count($user_pic)-1)];
				$member_pic = $this->center_function->create_file_name($output_dir,$user_pic_name);
				@copy($_POST['user_pic'],$output_dir.$member_pic);
				@unlink($_SERVER["DOCUMENT_ROOT"].PROJECTPATH."/assets/uploads/tmp/".$user_pic_name);
				//@unlink($output_dir.$row[0]['user_pic']);
				setcookie("is_upload", "", time()-3600);
				setcookie("IMG", "", time()-3600);
				$data['user_pic'] = $member_pic;
			}

			$this->db->where('user_id', $_SESSION['USER_ID']);
			$this->db->update('coop_user', $data);
			header("location: ".PROJECTPATH."/main_menu/profile");
		}
		$arr_data = array();

		$user_id = (int) $_SESSION["USER_ID"] ;

		$this->db->select('*');
		$this->db->from('coop_user');
		$this->db->where("user_id = '".$user_id."'");
		$user = $this->db->get()->result_array();
		$arr_data['user'] = $user[0];

		$admin_permissions = array();
		$this->db->select('*');
		$this->db->from('coop_user_permission');
		$this->db->where("user_id = '".$user_id."'");
		$user_permission = $this->db->get()->result_array();
		foreach($user_permission as $key => $value) {
			$admin_permissions[$value["menu_id"]] = TRUE;
		}
		$arr_data['admin_permissions'] = $admin_permissions;

		$this->libraries->template('main_menu/profile',$arr_data);
	}
	function logout(){
		$_SESSION["USER_ID"] = "" ;
		session_destroy();
		if(@$_GET['res']){
			$return_url = '/auth?return_url='.urlencode(@$_GET['res']);
		}
		
		header("location: ".base_url(PROJECTPATH.@$return_url));
		exit;
	}
	function member_lb_upload(){
		$this->load->library('image');
		$this->load->view('manage_member_share/member_lb_upload');
	}

	function get_image(){
		if($_COOKIE["is_upload"]) {
			echo base_url(PROJECTPATH."/assets/uploads/tmp/".$_COOKIE["IMG"]);
		}
		exit();
	}
}
