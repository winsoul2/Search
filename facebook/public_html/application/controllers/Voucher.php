<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Voucher extends CI_Controller {
	function __construct() {
		parent::__construct();
	}
	
	public function index(){
        $data_arr = array();
        $params = array();
		foreach($_GET as $key => $value){
			$decode = base64_decode(@$key);
            $decode = explode('=',@$decode);
			$params[$decode[0]] = @$decode[1];
        }
        $voucher_id = $params['id'];

        //GEt voucher data.
        $voucher = $this->db->select("t1.no, t1.transaction_time, t1.member_id, t1.pay_type, t2.user_name")
                            ->from("coop_vouchers as t1")->where("t1.id= '".$voucher_id."'")
                            ->join("coop_user as t2", "t1.user_id = t2.user_id", "left")
                            ->get()->row_array();
        $data_arr['voucher'] = $voucher;
        $details = $this->db->select("t1.account_list_id, t1.principal, t1.interest, t1.balance, t1.detail , t2.account_list")
                            ->from("coop_voucher_detail as t1")
                            ->join("coop_account_list as t2", "t1.account_list_id = t2.run_id", "LEFT")
                            ->where("t1.voucher_id = '".$voucher_id."' AND t1.status = 1")
                            ->get()->result_array();
        $data_arr['details'] = $details;

        //Get member data.
        $member = $this->db->select(array('t1.firstname_th', 't1.lastname_th','t2.mem_group_name','t3.prename_full'))
                            ->from("(SELECT IF (
										(
											SELECT
												level_old
											FROM
												coop_mem_group_move
											WHERE
												date_move >= '".$voucher['transaction_time']."'
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
												date_move >= '".$voucher['transaction_time']."'
											AND coop_mem_group_move.member_id = coop_mem_apply.member_id
											ORDER BY
												date_move ASC
											LIMIT 1
										),
										coop_mem_apply. level
									) AS level, member_id, firstname_th, lastname_th, mem_type_id, prename_id FROM coop_mem_apply) as t1")
		                    ->join("coop_mem_group as t2",'t1.level = t2.id','left')
		                    ->join("coop_prename as t3",'t1.prename_id = t3.prename_id','left')
		                    ->where("member_id ='".$voucher['member_id']."'")
                            ->get()->row_array();
        $data_arr['member'] = $member;

        //Get signature data.
		$date_signature = date('Y-m-d');
		$signature = $this->db->select(array('*'))->from('coop_signature')->where("start_date <= '".$date_signature."'")->order_by('start_date DESC')->get()->row_array();
        $data_arr['signature'] = $signature;

        $arr_pay_type = array('0'=>'เงินสด','1'=>'โอนเงิน','2'=>'เช็คเงินสด','3'=>'อื่นๆ');
		$data_arr['pay_type'] =  $arr_pay_type[$voucher['pay_type']];

        $this->load->view('voucher/payment_slip_pdf',$data_arr);
    }
}