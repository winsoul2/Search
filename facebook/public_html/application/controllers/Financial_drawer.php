<?php

defined('BASEPATH') OR exit('No direct script access allowed');



class Financial_drawer extends CI_Controller {

    function __construct()

    {

        parent::__construct();

    }

    public function index()

    {
        $row = '';
        $this->db->select(array('t1.user_officer_id ','t1.payment_date','t2.user_name'));
        $this->db->from('coop_financial_drawer as t1 ');
        $this->db->join('coop_user as t2','t2.user_id = t1.user_officer_id','inner');
        $this->db->group_by("t1.user_officer_id,t1.payment_date");
        $this->db->order_by("t1.payment_date ASC ");
        $row_detail = $this->db->get()->result_array();
        $row['data'] = $row_detail;

        $arr_data['data'] = $row['data'];


        $this->libraries->template('financial_drawer/index',$arr_data);
    }
    public function account_excel_financial_drawer()
    {
        $user_officer_id = $_POST['user_officer_id'];
        $payment_date = $_POST['payment_date'];
        $arr_data = array();

        $this->db->select(array('user_name'));
        $this->db->from('coop_user');
        $this->db->where(" user_id = '{$user_officer_id}' " );
        $row_detail = $this->db->get()->result_array();
        $row['user_name'] = $row_detail[0]['user_name'];
        $arr_data['user_name'] = $row['user_name'];

        $this->db->select(array('*'));
        $this->db->from('coop_menu');
        $row_detail = $this->db->get()->result_array();
        $row['coop_menu'] = $row_detail;
        foreach ($row['coop_menu'] as $key => $val ){
            $arr_data['coop_menu'][$val['menu_id']] = $val;
        }

        $this->db->select(array('*'));
        $this->db->from('coop_account_list');
        $row_detail = $this->db->get()->result_array();
        $row['account'] = $row_detail;
        foreach ($row['account'] as $key => $val ){
            $arr_data['account'][$val['account_id']] = $val;
        }

        $this->db->select(array('id','contract_number'));
        $this->db->from('coop_loan');
        $row_detail = $this->db->get()->result_array();
        $row['coop_loan'] = $row_detail;
        foreach ($row['coop_loan'] as $key => $val ){
            $arr_data['coop_loan'][$val['id']] = $val['contract_number'];
        }

        $row = '';
        $this->db->select(array('*'));
        $this->db->from('coop_financial_drawer');
        $this->db->where(" user_officer_id = '{$user_officer_id}' and payment_date = '{$payment_date}' " );
        $this->db->order_by("payment_date ASC ");
        $row_detail = $this->db->get()->result_array();
        $row['data'] = $row_detail;


        $arr_data['data'] = $row['data'];

//        echo '<pre>';print_r($arr_data);echo '</pre>';exit;
        $this->load->view('financial_drawer/account_excel_financial_drawer',$arr_data);
    }

    public function account_excel_financial_drawer_result()
    {



        $user_officer_id = $_POST['user_officer_id_result'];
        $payment_date = $_POST['payment_date_result'];
        $arr_data = array();

        $this->db->select(array('user_name'));
        $this->db->from('coop_user');
        $this->db->where(" user_id = '{$user_officer_id}' " );
        $row_detail = $this->db->get()->result_array();


        $row['user_name'] = $row_detail[0]['user_name'];
        $arr_data['user_name'] = $row['user_name'];

        $this->db->select(array('*'));
        $this->db->from('coop_menu');
        $row_detail = $this->db->get()->result_array();
        $row['coop_menu'] = $row_detail;
        foreach ($row['coop_menu'] as $key => $val ){
            $arr_data['coop_menu'][$val['menu_id']] = $val;
        }

        $this->db->select(array('*'));
        $this->db->from('coop_account_list');
        $row_detail = $this->db->get()->result_array();
        $row['account'] = $row_detail;
        foreach ($row['account'] as $key => $val ){
            $arr_data['account'][$val['account_id']] = $val;
        }

        $this->db->select(array('id','contract_number'));
        $this->db->from('coop_loan');
        $row_detail = $this->db->get()->result_array();
        $row['coop_loan'] = $row_detail;
        foreach ($row['coop_loan'] as $key => $val ){
            $arr_data['coop_loan'][$val['id']] = $val['contract_number'];
        }

        $row = '';
        $this->db->select(array('*'));
        $this->db->from('coop_financial_drawer');
        $this->db->where(" user_officer_id = '{$user_officer_id}' and payment_date = '{$payment_date}' and status_transfer = '0' " );
        $this->db->order_by("payment_date ASC ");
        $row_detail = $this->db->get()->result_array();
        $row['data'] = $row_detail;


        $arr_data['data'] = $row['data'];

        $this->db->select(array('amount','balance'));
        $this->db->from('coop_drawer_detail');
        $this->db->where(" user_id = '{$user_officer_id}' AND  date like '{$payment_date}%' " );
//         echo "".$this->db->get_compiled_select(null, false)."<br><br><br><br>";exit;
        $row_drawer_detail = $this->db->get()->result_array();
        $arr_data['drawer_detail']   = $row_drawer_detail[0]['amount'];

//        echo '<pre>';print_r($arr_data);echo '</pre>';exit;
        $this->load->view('financial_drawer/account_excel_financial_drawer_result',$arr_data);
    }
}