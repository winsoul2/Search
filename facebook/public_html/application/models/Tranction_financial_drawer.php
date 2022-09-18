<?php if( ! defined('BASEPATH')) exit('No direct script access allowed');

class Tranction_financial_drawer extends CI_Model {
    public function __construct()
    {
        parent::__construct();
        //$this->load->database();
        # Load libraries
        //$this->load->library('parser');
        $this->load->helper(array('html', 'url'));
    }
    public function arrange_data_coop_financial_drawer($data_process,$pay_typ,$permission_id,$statement_status,$request_url){



        $arrange_financial_drawer = array();

        $date_now_check = date("Y-m-d");
        $this->db->select(array('amount','balance'));
        $this->db->from('coop_drawer_detail');
        $this->db->where(" user_id = '{$_SESSION['USER_ID']}' AND  date like '{$date_now_check}%' " );
        $this->db->order_by("date DESC");
        $row_balance = $this->db->get()->result_array();
        $balance_drawer  = $row_balance[0]['balance'];

        $balance_drawer_total  = 0 ;

        if($permission_id == '47' ) {
            if(!empty($data_process['transaction_withdrawal']) || $data_process['transaction_withdrawal'] != 0){
                $principal_payment_money =  $data_process['transaction_withdrawal'];
            }else{
                $principal_payment_money =  $data_process['transaction_deposit'];
            }
            if($pay_typ != '1' && $pay_typ != 'transfer') {
                if ($statement_status == 'debit') {
                    $balance_drawer_total = $balance_drawer + $principal_payment_money;
                } else {
                    $balance_drawer_total = $balance_drawer - $principal_payment_money;
                }
            }else{
                $balance_drawer_total = $balance_drawer + 0;
            }
        }else{
            if($pay_typ != '1' && $pay_typ != 'transfer'){
                if ($statement_status == 'debit') {
                    $balance_drawer_total = $balance_drawer + $data_process['total_amount'];
                } else {
                    $balance_drawer_total = $balance_drawer - $data_process['total_amount'];
                }
            }else{
                $balance_drawer_total = $balance_drawer + 0;
            }

        }

        $this->db->set("balance", $balance_drawer_total );
        $this->db->where(" user_id = '{$_SESSION['USER_ID']}' AND  date like '{$date_now_check}%' " );
        $this->db->update('coop_drawer_detail');


        if($permission_id == '47' ) {

            $this->db->select(array('mem_id'));
            $this->db->from('coop_maco_account');
            $this->db->where(" account_id = '{$data_process['account_id']}' " );
            $row_detail = $this->db->get()->result_array();
            $coop_maco_account_mem_id = $row_detail[0]['mem_id'];

                if(!empty($data_process['transaction_withdrawal']) || $data_process['transaction_withdrawal'] != 0){
                    $principal_payment_money =  $data_process['transaction_withdrawal'];
                    $statement_withdrawal_deposit = 'withdrawal';
                }else{
                    $principal_payment_money =  $data_process['transaction_deposit'];
                    $statement_withdrawal_deposit = 'deposit';
                }

            $arrange_financial_drawer['member_id'] = @$coop_maco_account_mem_id;
            $arrange_financial_drawer['receipt_id'] ='';
            $arrange_financial_drawer['loan_id'] = '';
            $arrange_financial_drawer['account_list_id'] = '30';
            $arrange_financial_drawer['loan_atm_id'] = '';
            $arrange_financial_drawer['status_transfer'] = @$pay_typ == "transfer" ? "1" : @$pay_typ == "1" ? "1" : "0";
            $arrange_financial_drawer['principal_payment'] = @$principal_payment_money;
            $arrange_financial_drawer['interest'] = '';
            $arrange_financial_drawer['total_amount'] = @$principal_payment_money;
            $arrange_financial_drawer['account_number'] = @$data_process['account_id'];
            $arrange_financial_drawer['statement_withdrawal_deposit'] = @$statement_withdrawal_deposit;

        }else{

            $arrange_financial_drawer['member_id'] = @$data_process['member_id'];
            $arrange_financial_drawer['receipt_id'] = @$data_process['receipt_id'];
            $arrange_financial_drawer['loan_id'] = @$data_process['loan_id'];
            $arrange_financial_drawer['account_list_id'] = @$data_process['account_list_id'];
            $arrange_financial_drawer['loan_atm_id'] = @$data_process['loan_atm_id'];
            $arrange_financial_drawer['status_transfer'] = @$pay_typ == "transfer" ? "1" : @$pay_typ == "1" ? "1" : "0";
            $arrange_financial_drawer['principal_payment'] = @$data_process['principal_payment'];
            $arrange_financial_drawer['interest'] = @$data_process['interest'];
            $arrange_financial_drawer['total_amount'] = @$data_process['total_amount'];
            $arrange_financial_drawer['account_number'] = '';
            $arrange_financial_drawer['statement_withdrawal_deposit'] = '';

        }
        $arrange_financial_drawer['payment_date'] = date('Y-m-d H:i:s');
        $arrange_financial_drawer['createdatetime'] = date('Y-m-d H:i:s');
        $arrange_financial_drawer['permission_id'] = @$permission_id;
        $arrange_financial_drawer['user_officer_id'] = @$_SESSION['USER_ID'];
        $arrange_financial_drawer['statement_status'] = @$statement_status;
        $arrange_financial_drawer['url_path'] = @$request_url;
        $arrange_financial_drawer['balance_drawer'] = $balance_drawer_total;

        $this->db->insert('coop_financial_drawer', $arrange_financial_drawer);

    }

}
