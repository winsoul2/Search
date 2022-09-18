<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Finance_month_detail extends CI_Controller {
    function __construct()
	{
		parent::__construct();
    }
    
    public function index()
	{
        $arr_data = array();

			$where = '';

			if ($_GET['year'] != '' && $_GET['month'] != '') {
				$member_id = $_GET['member_id'];
				$year = $_GET['year'];
				$month = $_GET['month'];
			}else{
                $_GET['year'] = date('Y')+543;
                $_GET['month'] = date('n');
				$year = date('Y')+543;
				$month = date('n');
			}
			$where .= "t2.profile_year='{$year}' AND t2.profile_month='{$month}' ";

			if($_GET['member_id'] != ''){
//				$where .= " AND t1.member_id='{$this->center_function->complete_member_id($_GET['member_id'])}' ";
                $where .= " AND t1.member_id='".$_GET['member_id']."' ";
			}

			$x = 0;
			$join_arr[$x]['table'] = 'coop_finance_month_profile as t2';
			$join_arr[$x]['condition'] = 't1.profile_id = t2.profile_id';
			$join_arr[$x]['type'] = 'inner';

			$x++;
			$join_arr[$x]['table'] = 'coop_mem_apply as t3';
			$join_arr[$x]['condition'] = 't3.member_id = t1.member_id';
			$join_arr[$x]['type'] = 'inner';

			$x++;
			$join_arr[$x]['table'] = 'coop_prename as t4';
			$join_arr[$x]['condition'] = 't4.prename_id = t3.prename_id';
			$join_arr[$x]['type'] = 'left';

			$x++;
			$join_arr[$x]['table'] = 'coop_deduct as t5';
			$join_arr[$x]['condition'] = 't5.deduct_id = t1.deduct_id';
			$join_arr[$x]['type'] = 'left';

			$x++;
			$join_arr[$x]['table'] = 'coop_loan as t6';
			$join_arr[$x]['condition'] = 't1.loan_id = t6.id';
			$join_arr[$x]['type'] = 'left';

			$this->paginater_all->type(DB_TYPE);
			$this->paginater_all->select("t1.*, concat(t4.prename_short,t3.firstname_th, ' ',t3.lastname_th) as fullname_th, t5.deduct_detail, t6.contract_number");
			$this->paginater_all->main_table('coop_finance_month_detail as t1');
			$this->paginater_all->page_now(@$_GET["page"]);
			$this->paginater_all->per_page(20);
			$this->paginater_all->page_link_limit(20);
			$this->paginater_all->where($where);
			$this->paginater_all->order_by('t1.profile_id, t1.member_id, t1.loan_id DESC');
			$this->paginater_all->join_arr($join_arr);
			$row = $this->paginater_all->paginater_process();
			// echo $where;exit;

			$paging = $this->pagination_center->paginating($row['page'], $row['num_rows'], $row['per_page'], $row['page_link_limit'], $_GET);//$page_now = 1, $row_total = 1, $per_page = 20, $page_limit = 20
			$i = $row['page_start'];
			// echo $row['page'].' '.$row['num_rows'].' '.$row['per_page'].' '.$row['page_link_limit'];exit;
			$arr_data['num_rows'] = $row['num_rows'];
			$arr_data['paging'] = $paging;
			$arr_data['data'] = $row['data'];
			$arr_data['i'] = $i;
			$arr_data['total'] = $limit;

        //
		$this->db->distinct();
		$this->db->select('profile_year');
		$this->db->from('coop_finance_month_profile');
		$coop_finance_month_profile_arr = $this->db->get()->result_array();
		$arr_data['coop_finance_month_profile_arr'] = $coop_finance_month_profile_arr;

        $this->db->select('profile_id');
        $this->db->from('coop_finance_month_profile');
        $this->db->where("profile_month = '".$_GET["month"]."' AND profile_year = '".$_GET["year"]."'");
        $profile = $this->db->get()->row_array();
        $arr_data['profile_id'] = $profile['profile_id'];

		$this->load->library('Center_function');
		$month_arr = $this->center_function->month_arr();
		$month_short_arr = $this->center_function->month_short_arr();
		$arr_data['month_arr'] = $month_arr;
		$arr_data['month_short_arr'] = $month_short_arr;


        if($_GET['debug'] == "on") {
//            echo $this->db->last_query();
            echo '<pre>';print_r($arr_data);exit;
            exit;
        }

        $this->libraries->template('finance_month_detail/index',$arr_data);
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    function check_member_id(){
		$member_id = sprintf("%06d", @$_POST['member_id']);
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
    
    function get_search_member(){
		$where = "
		 	(member_id LIKE '%".$this->input->post('search_text')."%'
		 	OR firstname_th LIKE '%".$this->input->post('search_text')."%'
			OR lastname_th LIKE '%".$this->input->post('search_text')."%')
		";
		$this->db->select(array('id','member_id','firstname_th','lastname_th','apply_date','mem_apply_id','member_status'));
		$this->db->from('coop_mem_apply');
		$this->db->where($where);
		$this->db->order_by('mem_apply_id DESC');
		$row = $this->db->get()->result_array();
		$arr_data['data'] = $row;
		$arr_data['form_target'] = $this->input->post('form_target');
		$arr_data['member_status'] = array('1'=>'ปกติ','2'=>'ลาออก','3'=>'รออนุมัติ','4'=>'ไม่อนุมัติ');
		//echo"<pre>";print_r($arr_data['data']);exit;
		$this->load->view('finance_month_detail/get_search_member',$arr_data);
    }
    
    function update_finance_month_detail(){
		$arr_data = array();
        if($_POST['form_target'] == 'checkupdate'){

            if($_POST['member_id'] != '' && $_POST['year']!='' && $_POST['month']!=''){
                $member_id = $_POST['member_id'];
                $year = $_POST['year'];
                $month = $_POST['month'];
                
                $this->load->view('finance_month_detail/update_finance_month_detail',$arr_data);
            }else{
                echo 'ส่งค่าไม่ครบ';exit;
            }
        }else if($_POST['form_target'] == 'delete'){
            $run_id = $_POST['run_id'];
            $user_update = $_POST['user_id'];
            $this->db->select('*');
            $this->db->from('coop_finance_month_detail');
            $this->db->where("run_id = '$run_id'");
            $row = $this->db->get()->row_array();

            
            $data_insert = array(   "profile_id"=>$row['profile_id'],
                                    "member_id"=>$row['member_id'],
                                    "deduct_code"=>$row['deduct_code'],
                                    "pay_amount"=>$row['pay_amount'],
                                    "real_pay_amount"=>$row['real_pay_amount'],
                                    "pay_type"=>$row['pay_type'],
                                    "loan_id"=>$row['loan_id'],
                                    "deduct_id"=>$row['deduct_id'],
                                    "deposit_account_id"=>$row['deposit_account_id'],
                                    "status" => '3',
                                    "user_update"=>$user_update,
                                    "create_datetime"=> date("Y-m-d h:i:sa"));
            $this->db->insert('log_coop_finance_month_detail', $data_insert);

            $this->db->WHERE("run_id = $run_id");
            $this->db->delete('coop_finance_month_detail');
            echo 'ลบข้อมูล';
        }else if($_POST['form_target'] == 'insert'){
            $user_update = $_POST['user_id'];
            if($_POST['deposit_account_id'] == '' || $_POST['deposit_account_id'] == ''){
                $deposit_account_id = null;
            }else{
                $deposit_account_id = $_POST['deposit_account_id'];
            }
            if($_POST['loan_id'] == '' || $_POST['loan_id'] == null){
                $loan_id = null;
            }else{
                $loan_id = $_POST['loan_id'];
                $this->db->select('t2.loan_type_id');
                $this->db->from('coop_loan as t1');
                $this->db->join("coop_loan_name as t2","t2.loan_name_id = t1.loan_type","left");
                $this->db->WHERE("t1.id = '$loan_id'");
                $coop_loan_arr = $this->db->get()->row_array();
                $loan_type_id = $coop_loan_arr['loan_type_id'];
            }
            if($_POST['search_type'] == 1){
                if($_POST['search_deduct']==1){
                    if ($loan_type_id == 7){
                        $deduct_id = 1;//1 ฉุกเฉิน

                    }else if ($loan_type_id == 8){
                        $deduct_id = 3;//3 สามัญ
                    }else if ($loan_type_id == 9){
                        $deduct_id = 2;//2 พิเศษ
                    }else if ($loan_type_id == 10){
                        $deduct_id = 16;//16 โควิท
                    }
                    // echo 'ดอกเบี้ย';
                    $deduct_code = 'LOAN';
                    $pay_type = 'interest';
                }else if($_POST['search_deduct']==2){
                    if ($loan_type_id == 7){
                        $deduct_id = 5;//5 ฉุกเฉิน

                    }else if ($loan_type_id == 8){
                        $deduct_id = 6;//6 สามัญ
                    }else if ($loan_type_id == 9){
                        $deduct_id = 7;//7 พิเศษ
                    }else if ($loan_type_id == 10){
                        $deduct_id = 17;//17 โควิท
                    }
                    // echo 'เงินต้น';
                    $deduct_code = 'LOAN';
                    $pay_type = 'principal';
                }
            }else if($_POST['search_type'] == 2){
                $deduct_code = 'DEPOSIT';
                $pay_type = 'principal';
                $deduct_id = 15;//15
            }else if($_POST['search_type'] == 3){
                $deduct_code = 'SHARE';
                $pay_type = 'principal';
                $deduct_id = 14;//14
            }else if($_POST['search_type'] == "OTHER"){
                $deduct_code = 'OTHER';
                $pay_type = 'principal';
                $deduct_id = 48;//46
            }
            $loan_deduct = array( 1,2,3,5,6,7,16,17 );
            $status = true;
            
            foreach ($loan_deduct as $value){
                if ($deduct_id == $value){
                    $this->db->select('COUNT(run_id) as count_deduct');
                    $this->db->from('coop_finance_month_detail');
                    $this->db->WHERE("member_id = '{$_POST['member_id']}' AND loan_id = '$loan_id' AND deduct_id = '$deduct_id' AND profile_id = '{$_POST['profile_id']}'" );
                    $num_deduct_id = $this->db->get()->row_array();
                    // print_r ($num_deduct_id);
                    if ($num_deduct_id['count_deduct'] > 0) {
                        echo 'เงินต้นหรือดอกเบี้ยเงินกู้ซ้ำ';
                        $status = false;
                    }
                }
            }
            if ($deduct_id == 14){ //14
                $this->db->select('COUNT(run_id) as count_deduct');
                $this->db->from('coop_finance_month_detail');
                $this->db->WHERE("member_id = '{$_POST['member_id']}' AND deduct_id = '$deduct_id' AND profile_id = '{$_POST['profile_id']}'");
                $num_deduct_id = $this->db->get()->row_array();
                if ($num_deduct_id['count_deduct'] > 0) {
                    echo 'หุ้นซ้ำ';
                    $status = false;
                }
            }
            if ($deduct_id == 15){//15
                $this->db->select('COUNT(run_id) as count_deduct');
                $this->db->from('coop_finance_month_detail');
                $this->db->WHERE("member_id = '{$_POST['member_id']}' AND deposit_account_id = '$deposit_account_id' AND deduct_id = '$deduct_id' AND profile_id = '{$_POST['profile_id']}'");
                $num_deduct_id = $this->db->get()->row_array();
                if ($num_deduct_id['count_deduct'] > 0) {
                    echo 'เงินฝากซ้ำ';
                    $status = false;
                }
            }
            if ($deduct_id == 46){//46
                $this->db->select('COUNT(run_id) as count_deduct');
                $this->db->from('coop_finance_month_detail');
                $this->db->WHERE("member_id = '{$_POST['member_id']}' AND deduct_id = '$deduct_id' AND profile_id = '{$_POST['profile_id']}'");
                $num_deduct_id = $this->db->get()->row_array();
                if ($num_deduct_id['count_deduct'] > 0) {
                    echo 'อื่นๆ ซ้ำ';
                    $status = false;
                }
            }
//            echo $this->db->last_query();
//            $status = false;
            if($status == true){
                $this->db->select('department,faction,level');
                $this->db->from('coop_mem_apply');
                $this->db->WHERE("member_id = '{$_POST['member_id']}'");
                $coop_mem_apply = $this->db->get()->row_array();
                $department = $coop_mem_apply['department'];
                $faction = $coop_mem_apply['faction'];
                $level = $coop_mem_apply['level'];
                $date_now = date("Y-m-d h:i:s");
                $data = array(
                    'member_id' => $_POST['member_id'],
                    'profile_id' => $_POST['profile_id'],
                    'deduct_id' => $_POST['deduct_id'],
                    'pay_amount' => $_POST['pay_amount'],
                    'real_pay_amount' => $_POST['real_pay_amount'],
                    'deduct_code' => $deduct_code,
                    'pay_type' => $pay_type,
                    'deduct_id' => $deduct_id,
                    'deposit_account_id' => $deposit_account_id,
                    'run_status' => 0,
                    'finance_month_type' => 0,
                    'create_datetime' => $date_now,
                    'loan_id'=> $loan_id,
                    'department' => $department,
                    'faction' => $faction,
                    'level' => $level,
                    'user_update' => $user_update,
                );
                
                $this->db->insert('coop_finance_month_detail', $data);
                $data_insert = array(   "profile_id"=>$_POST['profile_id'],
                                        "member_id"=>$_POST['member_id'],
                                        "deduct_code"=>$deduct_code,
                                        "pay_amount"=>$_POST['pay_amount'],
                                        "real_pay_amount"=>$_POST['real_pay_amount'],
                                        "pay_type"=>$pay_type,
                                        "loan_id"=>$loan_id,
                                        "deduct_id"=>$deduct_id,
                                        "deposit_account_id"=>$deposit_account_id,
                                        "status" => '2',
                                        "user_update"=>$user_update,
                                        "create_datetime"=> date("Y-m-d h:i:sa"));
                $this->db->insert('log_coop_finance_month_detail', $data_insert);
                echo 'true';
            }
        }
            
    }
    
    function save_finance_month_detail(){
		$arr_data = array();
        $save_edit = $_POST['save_edit'];
        $user_id = $_POST['user_id'];
        $date_now = date("Y-m-d h:i:s");
        foreach ($save_edit as $key => $value){
            if($value != ''){
                // เก็บ log การแก้ไขข้อมูล
                $this->db->select('*');
                $this->db->from('coop_finance_month_detail');
                $this->db->where("run_id = '{$value['run_id']}'");
                $row = $this->db->get()->row_array();

                
                $data_insert = array(   "profile_id"=>$row['profile_id'],
                                        "member_id"=>$row['member_id'],
                                        "deduct_code"=>$row['deduct_code'],
                                        "pay_amount"=>$row['pay_amount'],
                                        "real_pay_amount"=>$row['pay_amount'],
                                        "pay_type"=>$row['pay_type'],
                                        "loan_id"=>$row['loan_id'],
                                        "deduct_id"=>$row['deduct_id'],
                                        "deposit_account_id"=>$row['deposit_account_id'],
                                        "status" => '1',
                                        "user_update"=>$user_id,
                                        "create_datetime"=> date("Y-m-d h:i:sa"));
                $this->db->insert('log_coop_finance_month_detail', $data_insert);
                // จบเก็บ log การแก้ไขข้อมูล
                // update coop_finance_month_detail
                $data_update = array(  "member_id"=>$value['member_id'],
                                "pay_amount"=>$value['pay_amount'],
                                "real_pay_amount"=>$value['pay_amount'],
                                'update_datetime' => $date_now,
                                "user_update"=>$user_id);
                if ($value['run_id'] != null){
                    $this->db->set($data_update);
                    $this->db->where('run_id', $value['run_id']);
                    $this->db->update('coop_finance_month_detail');

                }
                // จบ update coop_finance_month_detail
            }
        }
    }
    
    public function search_member_add()
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
		$this->load->view('finance_month_detail/search_member_add',$arr_data);
    }
    
    public function get_contract_list(){
        $arr_data = array();
        $member_id = $_POST['member_id'];
        $profile_id = $_POST['profile_id'];
        $this->db->select('profile_month,profile_year');
        $this->db->from('coop_finance_month_profile');
        $this->db->WHERE("profile_id = '$profile_id'");
        $finance_month_profile = $this->db->get()->row_array();
        $month = $finance_month_profile['profile_month'];
        $year = $finance_month_profile['profile_year'];
        if ($_POST['search_type']==1) {
            $this->db->select('t1.id,t1.contract_number,t2.loan_name');
            $this->db->from('coop_loan as t1');
            $this->db->join("coop_loan_name as t2","t2.loan_name_id = t1.loan_type","left");
            $this->db->WHERE("t1.member_id LIKE '$member_id' AND t1.loan_status LIKE '1' AND t1.loan_amount_balance > 0");
            $lish = $this->db->get()->result_array();

        }else if ($_POST['search_type']==2){
            $this->db->select('*');
            $this->db->from('coop_maco_account as t1');
            $this->db->WHERE("mem_id = '$member_id'");
            $this->db->group_by("t1.account_id");
            $lish = $this->db->get()->result_array();
            $new_lish = array();
            foreach ($lish as $key => $value){
                array_push($new_lish, $value);
            }
            $lish = $new_lish;
        }else if ($_POST['search_type']==3){ 
        }else if ($_POST['search_type']=="OTHER"){

        }
        $arr_data['lish'] = $lish;
        $this->load->view('finance_month_detail/get_contract_list',$arr_data);
    }

    function history_edit_month_detail(){
        $arr_data = array();
        if ($_GET['date_start'] != '' && $_GET['date_end'] != ''){
            $arr_data['date_start'] = $_GET['date_start'];
            $arr_data['date_end'] = $_GET['date_end'];
        }else {
            $year_month = date("Y-m");
            $arr_data['date_start'] = $year_month."-01";
            $last_date = date('t',strtotime('today'));
            $arr_data['date_end'] = $year_month."-".$last_date;
        }
        $where = "create_datetime BETWEEN '{$arr_data['date_start']} 00:00:00' AND '{$arr_data['date_end']} 23:59:59'";
        $this->db->select('t1.*,t2.profile_month,t2.profile_year,t3.firstname_th,t3.lastname_th,t4.prename_short,t5.deduct_detail,t6.user_name
                            ,t7.contract_number,t8.loan_name');
        $this->db->from('log_coop_finance_month_detail as t1');
        $this->db->join("coop_finance_month_profile as t2","t1.profile_id = t2.profile_id","left");
        $this->db->join("coop_deduct as t5","t5.deduct_id = t1.deduct_id","left");
        $this->db->join("coop_mem_apply as t3","t3.member_id = t1.member_id","left");
        $this->db->join("coop_prename as t4","t4.prename_id = t3.prename_id","left");
        $this->db->join("coop_user as t6","t6.user_id = t1.user_update","left");
        $this->db->join("coop_loan as t7","t7.id = t1.loan_id","left");
        $this->db->join("coop_loan_name as t8","t8.loan_name_id = t7.loan_type","left");
        $this->db->WHERE($where);
        $this->db->limit(20,0);
        $log_month_detail_arr  = $this->db->get()->result_array();
        // print_r ($log_month_detail_arr);
        foreach ($log_month_detail_arr as $key => $value){
            $log_month_detail_arr[$key]['fullname_th'] = $value['prename_short'].$value['firstname_th'].' '.$value['lastname_th'];
        }
        $arr_data['log_month_detail_arr'] = $log_month_detail_arr; 

        $this->load->library('Center_function');
		$month_arr = $this->center_function->month_arr();
		$month_short_arr = $this->center_function->month_short_arr();
		$arr_data['month_arr'] = $month_arr;
        $arr_data['month_short_arr'] = $month_short_arr;
        $this->libraries->template('finance_month_detail/history_edit_month_detail',$arr_data);
    }
}
