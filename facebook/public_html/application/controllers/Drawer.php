<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Drawer extends CI_Controller {
    function __construct()
	{
		parent::__construct();

    }
    public function list_drawer()
    {   
	    if(isset($_GET['detail']) &&  !empty($_GET['detail'])  ) { 
            $this->db->select('*');
            $this->db->from('coop_drawer');
            $this->db->join('coop_user' , 'coop_user.user_id = coop_drawer.drawer_user_id');
            $this->db->join('coop_drawer_user' , 'coop_drawer_user.user_drawer = coop_drawer.drawer_user_id');
            $this->db->join('coop_drawer_primary' , 'coop_drawer_user.user_primary = coop_drawer_primary.id');

            $this->db->where('coop_drawer.id = '.$_GET['detail']);
            
            $arr_data['getDrawer'] = $this->db->get()->result_array();

            $this->db->select('coop_user.employee_id,coop_user.user_name,coop_drawer_detail.amount,coop_drawer_user.user_primary,coop_drawer_detail.balance,coop_drawer_primary.primary_name');
            $this->db->from('coop_drawer');
            $this->db->join('coop_drawer_detail' , 'coop_drawer_detail.drawer_id = coop_drawer.id');
            $this->db->join('coop_user' , 'coop_user.user_id = coop_drawer_detail.user_id');
            $this->db->join('coop_drawer_user' , 'coop_drawer_user.user_drawer = coop_drawer_detail.user_id');
            $this->db->join('coop_drawer_primary' , 'coop_drawer_user.user_primary = coop_drawer_primary.id');

            $this->db->where('coop_drawer.id = '.$_GET['detail']);
            $arr_data['getDetail'] = $this->db->get()->result_array();
           
        } else {
            $x=0;
            // $join_arr = array();
            // $join_arr[$x]['table'] = 'coop_user';
            // $join_arr[$x]['condition'] = 'coop_drawer_user.user_drawer = coop_user.user_id';
            // $join_arr[$x]['type'] = 'inner';
            // $x++;
            // $join_arr = array();
            // $join_arr[$x]['table'] = 'coop_drawer';
            // $join_arr[$x]['condition'] = 'coop_drawer_user.user_drawer = coop_user.user_id';
            // $join_arr[$x]['type'] = 'inner';
            $today = date('Y-m-d');
            $this->paginater_all->type(DB_TYPE);
            $this->paginater_all->select('*');
            $this->paginater_all->main_table('coop_drawer');
            // $this->paginater_all->where("user_primary = 1");
            $this->paginater_all->page_now(@$_GET["page"]);
            $this->paginater_all->per_page(10);
            $this->paginater_all->page_link_limit(20);
            $this->paginater_all->order_by('date DESC');
            // $this->paginater_all->join_arr($join_arr);
            $row = $this->paginater_all->paginater_process();
            //echo"<pre>";print_r($row);exit;
            //echo $this->db->last_query();exit;
            $paging = $this->pagination_center->paginating($row['page'], $row['num_rows'], $row['per_page'], $row['page_link_limit'],@$_GET);//$page_now = 1, $row_total = 1, $per_page = 20, $page_limit = 20
            $i = $row['page_start'];
    
    
            $arr_data['num_rows'] = $row['num_rows'];
            $arr_data['paging'] = $paging; 
            $arr_data['data'] = $row['data'];
            $arr_data['i'] = $i;
        }
        
        

		$this->libraries->template('drawer/list_drawer',$arr_data);
    }
    public function list_drawer_user()
    {   
        $this->db->select('*');
        $this->db->from('coop_drawer_user');
        $this->db->join('coop_user' , 'coop_drawer_user.user_drawer = coop_user.user_id');
        $this->db->join('coop_drawer_primary' , 'coop_drawer_user.user_primary = coop_drawer_primary.id');



        if (isset($_GET['edit'])) {
           $this->db->where('user_id !='.$_GET['edit']);
        }
        $arr_data['du'] = $this->db->get()->result_array();


      
        if (isset($_GET['add']) || isset($_GET['edit'])) {
        
            $this->db->select('*');
            $this->db->from('coop_user');
            $user_list = $this->db->get()->result_array();
                foreach ($user_list as $key => $value) {
                    foreach ($arr_data['du'] as $index => $data) {
                        if(isset($_GET['add'])) {
                            if($value['user_id'] == $data['user_id'] || $value['user_id'] == 1) {
                                unset($user_list[$key]);
                            }
                        }
                        if(isset($_GET['edit'])) {
                            if($value['user_id'] == $data['user_id'] || $value['user_id'] == 1) {
                                unset($user_list[$key]);
                            }
                        }
                    }
                }
            


            if(isset($_GET['edit'])) {
                $this->db->select('*');
                $this->db->from('coop_user');
                $this->db->where('user_id = '.$_GET['edit']);
                
                $arr_data['user_data'] = $this->db->get()->row();

                $this->db->select('*');
                $this->db->from('coop_drawer_user');
                $this->db->join('coop_drawer_primary' , 'coop_drawer_user.user_primary = coop_drawer_primary.id');
                $this->db->where('user_drawer = '.$_GET['edit']);
                $arr_data['drawer_user'] = $this->db->get()->row();
            }
            $arr_data['user_list'] = $user_list;
           
        } else {
            $this->db->select('*');
            $this->db->from('coop_drawer_user');
            $this->db->join('coop_drawer_primary' , 'coop_drawer_user.user_primary = coop_drawer_primary.id');

            $this->db->where('user_primary = 2');
            $arr_data['cdu'] = $this->db->get()->row();

            if($_GET['debug'] == "on"){
                echo $this->db->last_query(); exit;
            }



        }
		$this->libraries->template('drawer/list_drawer_user',$arr_data);
    }
    public function edit_user_drawer()
    {
        $this->db->select('user_drawer');
        $this->db->from('coop_drawer_user');
        $this->db->where('user_primary ='.$this->input->post()['drawer_user']);
        $get_ud = $this->db->get()->row();

        $this->db->set('user_drawer', $this->input->post()['add_drawer_user']);
        $this->db->where('user_drawer ='.$this->input->post()['user_primary']);
        $this->db->update('coop_drawer_user');
        $sql = $this->db->last_query();
//        $this->db->set('user_drawer_child' , $this->input->post()['add_drawer_user']);
//        $this->db->where('user_drawer_child ='.$this->input->post()['user_primary']);
//        $this->db->update('coop_drawer_user');
        redirect('drawer/list_drawer_user');
    }
    public function delete_user_drawer()
    {
        $this->db->where('id', $this->input->post()['dataID']);
        $this->db->delete('coop_drawer_user');
        echo json_encode(['status' => true]);
    }
    public function add_user_drawer() 
    {
        $user_primary = $this->input->post()['user_primary'] - 1;
        $this->db->select('*');
        $this->db->from('coop_drawer_user');
        $this->db->where('user_primary = '.$user_primary);
        $du = $this->db->get()->row();
  
        $arr = array(
            'user_drawer'       => $this->input->post()['add_drawer_user'],
            'user_drawer_child' => $du->user_drawer,
            'user_primary'      => $this->input->post()['user_primary']
        );
        
        $this->db->insert('coop_drawer_user' , $arr);
        redirect('drawer/list_drawer_user');
    }
    public function add_money_drawer() 
    {
        $this->db->select('*');
        $this->db->from('coop_drawer');
        $this->db->where('DATE(Date)', date('Y-m-d'));
        $arr_data['getDrawer'] = $this->db->get()->result_array();

        $x=0;
		$join_arr = array();
		$join_arr[$x]['table'] = 'coop_user';
		$join_arr[$x]['condition'] = 'coop_drawer_user.user_drawer = coop_user.user_id';
        $join_arr[$x]['type'] = 'inner';
        // $x++;
		// $join_arr = array();
		// $join_arr[$x]['table'] = 'coop_drawer';
		// $join_arr[$x]['condition'] = 'coop_drawer_user.user_drawer = coop_user.user_id';
        // $join_arr[$x]['type'] = 'inner';
        $today = date('Y-m-d');
		$this->paginater_all->type(DB_TYPE);
		$this->paginater_all->select('*');
		$this->paginater_all->main_table('coop_drawer_user');
		$this->paginater_all->where("user_primary = 1");
		$this->paginater_all->page_now(@$_GET["page"]);
		$this->paginater_all->per_page(10);
		$this->paginater_all->page_link_limit(20);
		$this->paginater_all->order_by('coop_drawer_user.id DESC');
		$this->paginater_all->join_arr($join_arr);
		$row = $this->paginater_all->paginater_process();
		//echo"<pre>";print_r($row);exit;
		//echo $this->db->last_query();exit;
		$paging = $this->pagination_center->paginating($row['page'], $row['num_rows'], $row['per_page'], $row['page_link_limit'],@$_GET);//$page_now = 1, $row_total = 1, $per_page = 20, $page_limit = 20
		$i = $row['page_start'];


		$arr_data['num_rows'] = $row['num_rows'];
		$arr_data['paging'] = $paging; 
		$arr_data['data'] = $row['data'];
        $arr_data['i'] = $i;
        foreach ($arr_data['data'] as $key => $value) {
            foreach ($arr_data['getDrawer'] as $index => $data) {
                if ($value['user_drawer'] == $data['drawer_user_id']) {
                    $arr_data['data'][$key]['budget'] = $data['budget'];
                    $arr_data['data'][$key]['balance'] = $data['balance'];
                    $arr_data['data'][$key]['counter_refund_drawer'] = $data['counter_refund_drawer'];

                }
            
            }
        }
        
		$this->libraries->template('drawer/add_money_drawer' , $arr_data);
    }
    public function get_drawer_user()
    {
        $this->db->select('*');
        $this->db->from('coop_user');
        $this->db->where('user_id != 1');
        $getUser = $this->db->get()->result_array();
      
        echo json_encode(['data' => $getUser]);
    }

    public function save_add_money()
    {
        $arr = array(
            'date'              => date('Y-m-d H:i:s'),
            'budget'            => $this->input->post()['amount'],
            'drawer_user_id'    => $this->input->post()['dataID'],
            'balance'           => $this->input->post()['amount'],
        );
        $this->db->insert('coop_drawer' , $arr);
        echo json_encode(['message' => true]);
    }

    public function save_add_money_adjunct()
    {

        $date_now_check = date("Y-m-d");
        $this->db->select(array('budget','balance','add_money_adjunct'));
        $this->db->from('coop_drawer');
        $this->db->where(" drawer_user_id = '{$_POST['dataID']}' AND  date like '{$date_now_check}%' " );
        $this->db->order_by("date DESC");
        // echo "".$this->db->get_compiled_select(null, false)."<br><br><br><br>";exit;
        $row_balance = $this->db->get()->result_array();
        $balance_drawer  = $row_balance[0]['balance'];
        $amount_adjunct = $_POST['amount'];
        $balance_drawer  = $balance_drawer +  $amount_adjunct ;

        if(!empty($row_balance[0]['add_money_adjunct'])){
                $amount_adjunct = $amount_adjunct+$row_balance[0]['add_money_adjunct'];
        }else{
            echo 'Broke';
            exit;
        }

        if( $amount_adjunct < 0){
            echo 'Broke';
            exit;
        }

        $this->db->set('balance', $balance_drawer);
        $this->db->set('add_money_adjunct', $amount_adjunct);
        $this->db->where("drawer_user_id = '{$_POST['dataID']}' AND  date like '{$date_now_check}%'");
        $this->db->update('coop_drawer');

        $arrange_financial_drawer['status_transfer'] = "0" ;
        $arrange_financial_drawer['principal_payment'] = @$amount_adjunct;
        $arrange_financial_drawer['total_amount'] = @$amount_adjunct;
        $arrange_financial_drawer['payment_date'] = date('Y-m-d H:i:s');
        $arrange_financial_drawer['createdatetime'] = date('Y-m-d H:i:s');
        $arrange_financial_drawer['user_officer_id'] = $_POST['dataID'];
        $arrange_financial_drawer['statement_status'] = 'debit';
        $arrange_financial_drawer['balance_drawer'] = $balance_drawer;
        $arrange_financial_drawer['user_adjunct_id'] =  $_SESSION['USER_ID'];

        $this->db->insert('coop_financial_drawer', $arrange_financial_drawer);


        echo json_encode(['message' => true]);

    }
    public function save_add_money_adjunct_cashier()
    {

        $date_now_check = date("Y-m-d");
        $this->db->select(array('balance','add_money_adjunct'));
        $this->db->from('coop_drawer_detail');
        $this->db->where(" user_id = '{$_SESSION['USER_ID']}' AND  date like '{$date_now_check}%' " );
        $this->db->order_by("date DESC");
            // echo "".$this->db->get_compiled_select(null, false)."<br><br><br><br>";exit;
        $row_balance_manager = $this->db->get()->result_array();
        $amount_adjunct_manager = $_POST['amount'];
        if(!empty($row_balance_manager[0]['balance'])){
            $amount_adjunct_manager = $row_balance_manager[0]['balance'] - $amount_adjunct_manager ;
        }else{
            echo 'Broke';
            exit;
        }

            if( $amount_adjunct_manager < 0){
                echo 'Broke';
                exit;
            }
            if(empty($row_balance_manager)){
                $this->db->select(array('balance','add_money_adjunct'));
                $this->db->from('coop_drawer');
                $this->db->where(" drawer_user_id = '{$_SESSION['USER_ID']}' AND  date like '{$date_now_check}%' " );
                $this->db->order_by("date DESC");
                // echo "".$this->db->get_compiled_select(null, false)."<br><br><br><br>";exit;
                $row_balance_manager = $this->db->get()->result_array();
                $amount_adjunct_manager = $_POST['amount'];
                if(!empty($row_balance_manager[0]['balance'])){
                    $amount_adjunct_manager = $row_balance_manager[0]['balance'] - $amount_adjunct_manager ;
                }

                 $this->db->set('balance', $amount_adjunct_manager);
                 $this->db->where("drawer_user_id = '{$_SESSION['USER_ID']}'  AND  date like '{$date_now_check}%'");
                 $this->db->update('coop_drawer');
            }else{
                $this->db->set('balance', $amount_adjunct_manager);
                $this->db->where("user_id = '{$_SESSION['USER_ID']}'  AND  date like '{$date_now_check}%'");
                $this->db->update('coop_drawer_detail');

            }

//        echo 'user'.$_SESSION['USER_ID'].'เหลือ '.$amount_adjunct_manager.' balance'.$row_balance_manager[0]['balance'].'<br>';

        $date_now_check = date("Y-m-d");
        $this->db->select(array('balance','add_money_adjunct'));
        $this->db->from('coop_drawer_detail');
        $this->db->where(" user_id = '{$_POST['dataID']}' AND  date like '{$date_now_check}%' " );
        $this->db->order_by("date DESC");
            // echo "".$this->db->get_compiled_select(null, false)."<br><br><br><br>";exit;
        $row_balance = $this->db->get()->result_array();
        $balance_drawer  = $row_balance[0]['balance'];
        $amount_adjunct = $_POST['amount'];
        $balance_drawer  = $balance_drawer +  $amount_adjunct ;

        if(!empty($row_balance[0]['add_money_adjunct'])){
            $amount_adjunct = $amount_adjunct+$row_balance[0]['add_money_adjunct'];
        }
//            echo 'user'.$_POST['dataID'].'เพิ่ม '.$amount_adjunct.' balance'.$balance_drawer.'<br>';

        $this->db->set('balance', $balance_drawer);
        $this->db->set('add_money_adjunct', $amount_adjunct);
        $this->db->where("user_id = '{$_POST['dataID']}' AND  date like '{$date_now_check}%'");
        $this->db->update('coop_drawer_detail');


        $arrange_financial_drawer['status_transfer'] = "0" ;
        $arrange_financial_drawer['principal_payment'] = @$amount_adjunct;
        $arrange_financial_drawer['total_amount'] = @$amount_adjunct;
        $arrange_financial_drawer['payment_date'] = date('Y-m-d H:i:s');
        $arrange_financial_drawer['createdatetime'] = date('Y-m-d H:i:s');
        $arrange_financial_drawer['user_officer_id'] = $_POST['dataID'];
        $arrange_financial_drawer['statement_status'] = 'debit';
        $arrange_financial_drawer['balance_drawer'] = $balance_drawer;
        $arrange_financial_drawer['user_adjunct_id'] =  $_SESSION['USER_ID'];
        $this->db->insert('coop_financial_drawer', $arrange_financial_drawer);
//        exit;
        echo json_encode(['message' => true]);
    }

    public function index()
    {
        $this->db->select('user_drawer,user_primary');
        $this->db->from('coop_drawer_user');
        $this->db->where('user_drawer' , $_SESSION['USER_ID']);
        $getUserPrimary = $this->db->get()->row();
        $arr['user_primary'] = $getUserPrimary;

        $this->db->select('user_id');
        $this->db->from('coop_drawer_detail');
        $this->db->where('DATE(Date)', date('Y-m-d'));
        $this->db->where_not_in('user_id', [$getUserPrimary->user_drawer]);
        $getDrawerDetail = $this->db->get()->result_array();
        
        $usedDrawerId = array();
        foreach ($getDrawerDetail as $key => $value) {
            $usedDrawerId[] = $value['user_id'];
        }
        $this->db->select('*');
        $this->db->from('coop_drawer_user');
        $this->db->where('user_drawer_child' , $_SESSION['USER_ID']);
        $this->db->join('coop_user' , 'coop_user.user_id = coop_drawer_user.user_drawer');
        if(count($usedDrawerId) > 0) {
            $this->db->where_not_in('user_id', $usedDrawerId);

        }
        $getChild = $this->db->get()->result_array();
        $arr['user_child'] = $getChild;

        $date = new DateTime("now");
        $curr_date = $date->format('Y-m-d');

        if ($getUserPrimary->user_primary == 1) {
            $this->db->select('*');
            $this->db->from('coop_drawer');
            $this->db->where('drawer_user_id' , $_SESSION['USER_ID']);
            $this->db->where('DATE(Date)', date('Y-m-d'));
            $getDrawer = $this->db->get()->row();
            $arr['drawer'] = $getDrawer; 
        } else {
            $this->db->select('sum(balance) as balance');
            $this->db->from('coop_drawer_detail');
            $this->db->where('user_id' , $_SESSION['USER_ID']);
            $this->db->where('DATE(Date)', date('Y-m-d'));

            $getDrawer = $this->db->get()->row();
          
            $arr['drawer'] = $getDrawer; 
        }
       
      
        $whereDrawer = array();
        $this->db->select('user_drawer');
        $this->db->from('coop_drawer_user');
        $this->db->where('user_drawer_child' , $_SESSION['USER_ID']);
        
        $drawer_id = $this->db->get()->result_array();
        $whereDrawer[] = 0;
        foreach ($drawer_id as $key => $value) {
            $whereDrawer[] = $value['user_drawer'];
        }
        $this->db->select('*');
        $this->db->from('coop_drawer_detail');
        $this->db->join('coop_user' , 'coop_user.user_id = coop_drawer_detail.user_id');
        $this->db->where_in('coop_drawer_detail.user_id' , $whereDrawer);
        $this->db->where('DATE(Date)', date('Y-m-d'));
        $drawer_list = $this->db->get()->result_array();
        $arr['drawer_list'] = $drawer_list;
        
		$this->libraries->template('drawer/index' , $arr);

    }
    public function search_member_by_type_jquery()
	{
		$search_text = @$_POST["search_text"];
		$search_list = @$_POST["search_list"];
		$where = "";
		if(@$_POST['search_list'] == 'employee_id'){
			$where = " employee_id LIKE '%".$search_text."%'";
		}else if(@$_POST['search_list'] == 'user_name'){
			$where = " user_name LIKE '%".$search_text."%'";
		}
		// $where .= "AND member_status <> '3'";
		$this->db->select('*');
        $this->db->from('coop_user');
        $this->db->join('coop_drawer_user' , 'coop_drawer_user.user_drawer = coop_user.user_id');
		$this->db->where($where);
		$row = $this->db->get()->result_array();
        $arr_data['data'] = $row;
       
		$this->load->view('ajax/search_member_drawer_jquery',$arr_data);
	}
    public function submitDrawer()
    {
    
        $data = $this->input->post();
       
        $setFormatArray = array();
        $totalAmount = 0;
        $dateToday = date('Y-m-d'); 
        $idSession = $_SESSION['USER_ID'];
        foreach ($data['amount'] as $key => $value) {
           $totalAmount += $value;
        }
        $this->db->select('user_primary');
        $this->db->from('coop_drawer_user');
        $this->db->where('user_drawer' , $_SESSION['USER_ID']);
        $getUserPrimary = $this->db->get()->row();
        $arr['user_primary'] = $getUserPrimary;

        if ($getUserPrimary->user_primary == 1) {

            $this->db->select('id,balance');
            $this->db->from('coop_drawer');
            $this->db->where('drawer_user_id' , $idSession);
            $this->db->where('DATE(Date)', date('Y-m-d'));
            $budget = $this->db->get()->row();
            $idDrawer = $budget->id;
        } else {
            
           
            $this->db->select('drawer_id,sum(balance) as balance');
            $this->db->from('coop_drawer_detail');
            $this->db->where('user_id' , $idSession);
            $this->db->where('DATE(Date)', date('Y-m-d'));

            $budget = $this->db->get()->row();
         
            $idDrawer = $budget->drawer_id;
            
        }
        
        if ($budget->balance >= $totalAmount) {
            $amount = $budget->balance - $totalAmount;
      
            if ($getUserPrimary->user_primary == 1) {
                $this->db->set('balance', $amount);
                $this->db->where('id', $idDrawer);
                $this->db->where('DATE(Date)', date('Y-m-d'));

                $this->db->update('coop_drawer'); 
               
            } else {
                
                $this->db->set('balance', $amount);
                // $this->db->select('*');
                $this->db->where('drawer_id', $idDrawer);
                $this->db->where('user_id', $idSession);

                $this->db->where('DATE(Date)', date('Y-m-d'));
                // $search = $this->db->get()->result_array();
                // print_r($search);
                $this->db->update('coop_drawer_detail'); 
               
            }
            for ($i=0; $i < count($data['drawer_id']); $i++) { 
                $setFormatArray[$i]['drawer_id']    = $idSession;
                $setFormatArray[$i]['user_id']      = $data['drawer_id'][$i];
                $setFormatArray[$i]['amount']       = $data['amount'][$i];
                
            }
          
            foreach ($setFormatArray as $key => $value) {
                $arr = array(
                    'drawer_id' => $idDrawer,
                    'user_id' => $value['user_id'],
                    'amount' => $value['amount'],
                    'balance' => $value['amount'],
                    'date' => date('Y-m-d H:i:s'),
                );
                $this->db->insert('coop_drawer_detail',$arr);

            }
            echo json_encode([ 'status' => true , 'message' => 'บันทึกสำเร็จ']);

        } else {
            echo json_encode([ 'status' => false , 'message' => 'จำนวนเงินจากที่กำหนด']);
        }

    }

    public function add_money_drawer_cashier()
    {
        //เพิ่มเงินลิ้นชัก ส่วนของรองผู้จัดการเพิ่มให้พนักงานหน้าเค้าเตอรื
        $this->db->select('user_drawer,user_primary');
        $this->db->from('coop_drawer_user');
        $this->db->where('user_drawer' , $_SESSION['USER_ID']);
        $getUserPrimary = $this->db->get()->row();
        $arr['user_primary'] = $getUserPrimary;

        $this->db->select('user_id');
        $this->db->from('coop_drawer_detail');
        $this->db->where('DATE(Date)', date('Y-m-d'));
        $this->db->where_not_in('user_id', [$getUserPrimary->user_drawer]);
        $getDrawerDetail = $this->db->get()->result_array();

        $usedDrawerId = array();
        foreach ($getDrawerDetail as $key => $value) {
            $usedDrawerId[] = $value['user_id'];
        }
        $this->db->select('*');
        $this->db->from('coop_drawer_user');
        $this->db->where('user_drawer_child' , $_SESSION['USER_ID']);
        $this->db->join('coop_user' , 'coop_user.user_id = coop_drawer_user.user_drawer');
        if(count($usedDrawerId) > 0) {
            $this->db->where_not_in('user_id', $usedDrawerId);

        }
        $getChild = $this->db->get()->result_array();
        $arr['user_child'] = $getChild;

        $date = new DateTime("now");
        $curr_date = $date->format('Y-m-d');

        if ($getUserPrimary->user_primary == 1) {
            $this->db->select('*');
            $this->db->from('coop_drawer');
            $this->db->where('drawer_user_id' , $_SESSION['USER_ID']);
            $this->db->where('DATE(Date)', date('Y-m-d'));
            $getDrawer = $this->db->get()->row();
            $arr['drawer'] = $getDrawer;
        } else {
            $this->db->select('sum(balance) as balance');
            $this->db->from('coop_drawer_detail');
            $this->db->where('user_id' , $_SESSION['USER_ID']);
            $this->db->where('DATE(Date)', date('Y-m-d'));

            $getDrawer = $this->db->get()->row();

            $arr['drawer'] = $getDrawer;
        }


        $whereDrawer = array();
        $this->db->select('user_drawer');
        $this->db->from('coop_drawer_user');
        $this->db->where('user_drawer_child' , $_SESSION['USER_ID']);

        $drawer_id = $this->db->get()->result_array();
        $whereDrawer[] = 0;
        foreach ($drawer_id as $key => $value) {
            $whereDrawer[] = $value['user_drawer'];
        }
        $this->db->select('*');
        $this->db->from('coop_drawer_detail');
        $this->db->join('coop_user' , 'coop_user.user_id = coop_drawer_detail.user_id');
        $this->db->where_in('coop_drawer_detail.user_id' , $whereDrawer);
        $this->db->where('DATE(Date)', date('Y-m-d'));
        $drawer_list = $this->db->get()->result_array();
        $arr['drawer_list'] = $drawer_list;


//        exit;
        $this->libraries->template('drawer/add_money_drawer_cashier' , $arr);
    }
    public function add_money_drawer_new()
    {
        $this->db->select('*');
        $this->db->from('coop_drawer');
        $this->db->where('DATE(Date)', date('Y-m-d'));
        $arr_data['getDrawer'] = $this->db->get()->result_array();

        $x=0;
        $join_arr = array();
        $join_arr[$x]['table'] = 'coop_user';
        $join_arr[$x]['condition'] = 'coop_drawer_user.user_drawer = coop_user.user_id';
        $join_arr[$x]['type'] = 'inner';
        // $x++;
        // $join_arr = array();
        // $join_arr[$x]['table'] = 'coop_drawer';
        // $join_arr[$x]['condition'] = 'coop_drawer_user.user_drawer = coop_user.user_id';
        // $join_arr[$x]['type'] = 'inner';
        $today = date('Y-m-d');
        $this->paginater_all->type(DB_TYPE);
        $this->paginater_all->select('*');
        $this->paginater_all->main_table('coop_drawer_user');
        $this->paginater_all->where("user_primary = 1");
        $this->paginater_all->page_now(@$_GET["page"]);
        $this->paginater_all->per_page(10);
        $this->paginater_all->page_link_limit(20);
        $this->paginater_all->order_by('coop_drawer_user.id DESC');
        $this->paginater_all->join_arr($join_arr);
        $row = $this->paginater_all->paginater_process();
        //echo"<pre>";print_r($row);exit;
        //echo $this->db->last_query();exit;
        $paging = $this->pagination_center->paginating($row['page'], $row['num_rows'], $row['per_page'], $row['page_link_limit'],@$_GET);//$page_now = 1, $row_total = 1, $per_page = 20, $page_limit = 20
        $i = $row['page_start'];


        $arr_data['num_rows'] = $row['num_rows'];
        $arr_data['paging'] = $paging;
        $arr_data['data'] = $row['data'];
        $arr_data['i'] = $i;
        foreach ($arr_data['data'] as $key => $value) {
            foreach ($arr_data['getDrawer'] as $index => $data) {
                if ($value['user_drawer'] == $data['drawer_user_id']) {
                    $arr_data['data'][$key]['budget'] = $data['budget'];

                }

            }
        }

//        exit;
        $this->libraries->template('drawer/add_money_drawer_new' , $arr_data);
    }
    public  function  refund_drawer(){
        //ฟังชันการคืนเงิน

        $refund_drawer_amount = 0;
        $refund_drawer_amount = $_GET['drawer'];
        $dateToday = date('Y-m-d');
        $idSession = $_SESSION['USER_ID'];
        //เช็คว่าเป็นเจ้าหน้าที่เค้าเตอรื หรือรองผู้จัดการ
        $this->db->select('drawer_id,sum(balance) as balance');
        $this->db->from('coop_drawer_detail');
        $this->db->where('user_id' , $idSession);
        $this->db->where('DATE(Date)', date('Y-m-d'));
        // echo "".$this->db->get_compiled_select(null, false)."<br><br><br><br>";exit;
        $row = $this->db->get()->result_array();

        $budget = $row[0];
        if(!empty($budget['drawer_id'])){
            //ถ้าเป็นเค้าเตอร์ จะคืนไปให้ผู้จัดการ โดยการอัปเดทยอดคืนเงิน ว่าวันนั้น เจ้าหน้าที่เค้าเตอรืคืนเงินเท่าไร และรวมจำนวนเงินคงเหลือของรองผู้จัดการ

            $this->db->set('balance',0 );
            $this->db->set('refund_drawer', $refund_drawer_amount);
            $this->db->where('user_id' , $idSession);
            $this->db->where('DATE(Date)', date('Y-m-d'));
            $this->db->update('coop_drawer_detail');

            //หาว่าจำนวนเงินของรองผู้จัดการมีเท่าไร เพื่อนำมารวมกับเงินที่คืน
                //หาข้อมูลก่อนว่าลำดับขั้นที่อยู่สูงกว่าเป็นใคร เช่น ถ้าเป็นงานเค้าเตอร์ จะต้องส่งให้รองผู้จัดการ ถ้าเป็นรองผู้จัดการให้ส่งให้ผู้จัดการ
            //หาว่าลำดับสูงกว่าคืนใคร
            $date_now_check = date("Y-m-d");
            $this->db->select(array('t1.*','t2.user_drawer_child'));
            $this->db->from('coop_drawer_detail as t1');
            $this->db->join('coop_drawer_user as t2 ' , 't1.user_id = t2.user_drawer ', 'inner');
            $this->db->where('t1.user_id ' , $idSession);
            $this->db->where(" date like '{$date_now_check}%' " );
            $this->db->order_by("date DESC");
            // echo "".$this->db->get_compiled_select(null, false)."<br><br><br><br>";exit;
            $row_balance_manager_refund = $this->db->get()->result_array();
            $row_balance_manager_refund =  $row_balance_manager_refund[0];
//            echo '<pre>';print_r($row_balance_manager_refund);echo '</pre>';
                //หาข้อมูลของคนที่สูงกว่า
            $this->db->select(array('*'));
            $this->db->from('coop_drawer_detail ');
            $this->db->where('user_id ' , $row_balance_manager_refund['user_drawer_child']);
            $this->db->where(" date like '{$date_now_check}%' " );
            $row_drawer_detail  = $this->db->get()->result_array();
            $row_drawer_detail =  $row_drawer_detail[0];
//            echo '<pre>';print_r($row_drawer_detail);echo '</pre>';

            if(!empty($row_drawer_detail)){
 //กรณีที่เป็น รองผู้จัดการให้ใช้อันนี้ คืออัปเดท balance
                if(!empty($row_drawer_detail['balance'])){
                    $up_balance_drawer = $row_drawer_detail['balance'] + $refund_drawer_amount;
                }

                $this->db->set('balance', $up_balance_drawer);
                $this->db->where('user_id ' , $row_balance_manager_refund['user_drawer_child']);
                $this->db->where('DATE(Date)', date('Y-m-d'));
                $this->db->update('coop_drawer_detail');

            }else{
                //กรณีที่เป็น ผู้จัดการให้ใช้อันนี้ คืออัปเดท balance    และอัปเดทเงิน counter_refund_drawer

                $this->db->select('drawer_user_id,sum(balance) as balance,counter_refund_drawer');
                $this->db->from('coop_drawer');
                $this->db->where('DATE(date)', date('Y-m-d'));
                $row = $this->db->get()->result_array();
                $balance_drawer = $row[0]['counter_refund_drawer'];
                $balance_drawer = $balance_drawer + $refund_drawer_amount;
                    $sumbalance_counter = $balance_drawer + $row[0]['balance'];
                $this->db->set('balance', $sumbalance_counter);
                $this->db->set('counter_refund_drawer', $balance_drawer);
                $this->db->where('DATE(Date)', date('Y-m-d'));
                $this->db->update('coop_drawer');
            }



        }else{
            //ถ้าเป็นผู้จัดการจะสรุปยอดเงินรวมแต่ละวัน ว่ามีเงินในลิ้นชักรายวันเท่าไร
            $this->db->select('drawer_user_id,sum(balance) as balance,counter_refund_drawer');
            $this->db->from('coop_drawer');
            $this->db->where('drawer_user_id' , $idSession);
            $this->db->where('DATE(date)', date('Y-m-d'));
            $row = $this->db->get()->result_array();
            $budget_drawer = $row[0];
            $balance_drawer = $budget_drawer['balance'] + $budget_drawer['counter_refund_drawer'];

            if(!empty($budget_drawer['drawer_user_id'])) {

                $this->db->set('balance',0 );
                $this->db->set('refund_drawer', $balance_drawer);
                $this->db->where('drawer_user_id' , $idSession);
                $this->db->where('DATE(Date)', date('Y-m-d'));
                $this->db->update('coop_drawer');

            }
        }

//        echo '<pre>';print_r($budget);echo '</pre>';
//        echo '<pre>';print_r($_GET);echo '</pre>';exit;
        redirect('drawer/index');
        exit;
    }

    public function add_money_drawer_report_all()
    {

        //แสดงผลข้อมูลเงินลิ้นชัก ยอดเริ่มต้น และยอดสรุปรวมแต่ละวัน
        $this->db->select('*');
        $this->db->from('coop_drawer AS t1');
        $this->db->join('coop_user AS t2 ', 't1.drawer_user_id = t2.user_id', 'inner');
        $this->db->order_by('t1.date DESC');
//        $this->db->where('DATE(Date)', date('Y-m-d'));
        $arr_data['getDrawer'] = $this->db->get()->result_array();

        $today = date('Y-m-d');
        $this->paginater_all->type(DB_TYPE);
        $this->paginater_all->select('*');
        $this->paginater_all->main_table('coop_drawer');
        $this->paginater_all->page_now(@$_GET["page"]);
        $this->paginater_all->per_page(10);
        $this->paginater_all->page_link_limit(20);
        $this->paginater_all->order_by('coop_drawer.id DESC');
        $row = $this->paginater_all->paginater_process();
//        echo"<pre>";print_r($row);exit;
        //echo $this->db->last_query();exit;
        $paging = $this->pagination_center->paginating($row['page'], $row['num_rows'], $row['per_page'], $row['page_link_limit'],@$_GET);//$page_now = 1, $row_total = 1, $per_page = 20, $page_limit = 20

        $i = $row['page_start'];
        $arr_data['num_rows'] = $row['num_rows'];
        $arr_data['paging'] = $paging;
        $arr_data['data'] =  $arr_data['getDrawer'] ;
        $arr_data['i'] = $i;


        $this->libraries->template('drawer/add_money_drawer_report_all' , $arr_data);
    }
}
