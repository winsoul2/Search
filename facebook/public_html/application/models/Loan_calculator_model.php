<?php defined('BASEPATH') OR exit('No direct script access allowed');

 class Loan_calculator_model extends CI_Model {

    private $_setting = null;
    private $_item_type = null;
    private $_calc = null;
    private $_interest_round = null;

    public function __construct()
    {
        parent::__construct();
        ini_set("precision", 17);
    }

    private function _getPrecision(){
        if($this->_interest_round != null){
            return $this->_interest_round;
        }
        return $this->_interest_round = $this->settingModel->get('round_interest');
    }

    public function calc($type_code, $entry_data = array()){

        if( $this->_item_type[$type_code] == "-1"){
            return self::calcPayment($type_code, $entry_data);
        }else if($this->_item_type[$type_code] == "1"){
            return self::calcEntry($type_code, $entry_data);
        }else {
            return self::calcNoop($type_code, $entry_data);
        }
    }

    private function calcPayment($type_code, $entry_data = array()){
        if(@$_GET['dev']=='dev'){ echo '<pre>'; print_r($entry_data); echo '</pre>'; exit;}
		$loan_type = $entry_data['loan_type'];
        $last_stm = self::getStmBeforePayment($entry_data['loan_id'], $entry_data['limit']);
        $start =  date('Y-m-d', strtotime($last_stm['transaction_datetime']));
        $end = date('Y-m-d', strtotime($entry_data['entry_date']));
        $cal_interest = array();
        $cal_interest['loan_id'] = $entry_data['loan_id'];
        $cal_interest['date_interesting'] = $end;
        $interest = round(self::getInterest($last_stm['loan_amount_balance'], $loan_type, $start, $end), 2, 1);
		$interest = self::mb_round($interest,self::_getPrecision());
		$int_arrears_bal = round(self::mb_round($last_stm['interest_arrear_bal']+$interest, self::_getPrecision()), 2, 1);
        $result = array();

        //calc interest arrears total
        $interest_not_pay = 0;

        if(in_array($type_code, array('PM'))) {
            //calc not pay
            if (isset($entry_data['action']) && $entry_data['action'] == 'payment') {
                if ($entry_data['interest']) {
                    $arrears_balance = $int_arrears_bal - $entry_data['interest'];
                    $interest_not_pay =  ($entry_data['interest']) - $int_arrears_bal;
                }
                //calc arrears balances
                $start = $entry_data['start_date'] ? $entry_data['start_date'] : self::getStmtPaymentLast($entry_data['loan_atm_id'], $entry_data['entry_date'])['transaction_datetime'];
            }else{
                $arrears_balance = $int_arrears_bal;
            }

        }else{

            if (isset($entry_data['action']) && $entry_data['action'] == 'payment') {
                if(isset($entry_data['interest']) && $entry_data['interest']){
                    $interest_not_pay = $entry_data['interest'] - $int_arrears_bal;
                    $arrears_balance = $int_arrears_bal - $entry_data['interest'];
                }else {
                    $arrears_balance = $interest_not_pay;
                }
            }else{
                $arrears_balance = $int_arrears_bal;
            }
        }

        $result['loan_amount_balance'] = $last_stm['loan_amount_balance'];
        $result['loan_type_code'] = $type_code;
        $result['interest_from'] = $start;
        $result['interest_to'] = $end;
        $result['interest_arrears'] = self::mb_round($last_stm['interest_arrear_bal'], self::_getPrecision());
        $result['interest_calculate_arrears'] = self::mb_round($interest, self::_getPrecision());
        $result['interest_arrear_bal'] = self::mb_round($arrears_balance, self::_getPrecision());
        $result['interest_notpay'] = self::mb_round((float)$interest_not_pay, self::_getPrecision());
        return $result;
    }

    private function calcEntry( $type_code, $entry_data = array()){
		$loan_type = $entry_data['loan_type'];
        $last_stm = self::getStmBeforePayment($entry_data['loan_id'], $entry_data['limit']);


        $start =  date('Y-m-d', strtotime($last_stm['transaction_datetime']));
        $end = date('Y-m-d', strtotime($entry_data['entry_date']));
        $interest = round(self::getInterest($last_stm['loan_amount_balance'], $loan_type, $start, $end), 2, 1);
        $interest = self::mb_round($interest, self::_getPrecision());
        $x = 0;
        if($type_code == "RM" && !empty($entry_data['principal'])) {
            $int_arrears_bal = $last_stm['interest_arrear_bal'] + $entry_data['interest'] + $interest;
            $notpay = $x - $int_arrears_bal; //$x ยังไม่ทราบค่าที่แน่ชัด
        }else if($type_code == "AD" && !empty($entry_data['action']) && $entry_data['action'] === "add"){
            $int_arrears_bal = $last_stm['interest_arrear_bal'] + $entry_data['interest'] + $interest;
            $notpay = $int_arrears_bal*(-1);
        }else {
            $int_arrears_bal = self::mb_round($last_stm['interest_arrear_bal'] + $entry_data['interest'] + $interest, self::_getPrecision());
            $notpay = $entry_data['interest']-$int_arrears_bal;
        }

        if($type_code == "RM" && !empty($entry_data['principal'])){
            $last_stm['loan_amount_balance'] = self::mb_round($last_stm['loan_amount_balance']+$entry_data['principal'], self::_getPrecision());
        }
        if($type_code == "AD" && !empty($entry_data['action']) && $entry_data['action'] === "add"){
            $last_stm['loan_amount_balance'] += $entry_data['add'];
        }
        $result = array();
        $result['loan_amount_balance'] = $last_stm['loan_amount_balance'];
        $result['loan_type_code'] = $type_code;
        $result['interest_from'] = $start;
        $result['interest_to'] = $end;
        $result['interest_arrears'] = self::mb_round($last_stm['interest_arrear_bal'], self::_getPrecision());
        $result['interest_calculate_arrears'] = self::mb_round($interest, self::_getPrecision());
        $result['interest_arrear_bal'] = self::mb_round($int_arrears_bal, self::_getPrecision());
        $result['interest_notpay'] = self::mb_round($notpay, self::_getPrecision());
        if($type_code == "RM") {
            $result['transaction_datetime'] = $last_stm['transaction_datetime'];
        }
        return $result;
    }

    private function getInterest($loan_amount_balance, $loan_type, $start, $end){
        return $this->loan_libraries->calc_interest_loan_multi_rate($loan_amount_balance, $loan_type, $start,$end);
    }

    private function calcNoop($type_code, $entry_data = array()){

        $last_stm = self::getStmBeforePayment($entry_data['loan_id'], $entry_data['limit']);
        if(sizeof($last_stm)){
            $result['interest_arrear_bal'] = $last_stm['interest_arrear_bal'];
        }else{
            $result['interest_arrear_bal'] = "";
        }

        $result = array();
        $result['loan_amount_balance'] = "";
        $result['loan_type_code'] = $type_code;
        $result['interest_from'] = "";
        $result['interest_to'] = "";
        $result['interest_arrears'] = "";
        $result['interest_calculate_arrears'] = "";
        $result['interest_notpay'] = "";
        return $result;
    }

    private function calcSpecial($type_code, $entry_data = array()){
        return array();
    }

    public function getStmBeforePayment($loan_id, $limit = array()){
        if(!empty($limit)){
            $this->db->where($limit);
        }
        $this->db->order_by('loan_transaction_id, transaction_datetime', 'desc');
        $this->db->where(array('loan_id' => $loan_id));
        return $this->db->get('coop_loan_transaction', 1)->row_array();
    }

    public function getStmPreventDate($loan_id, $entry_date){
        $this->db->order_by('loan_transaction_id', 'desc');
        $this->db->where(array('loan_id' => $loan_id, 'transaction_datetime <=' => $entry_date ));
        return $this->db->get('coop_loan_transaction', 1)->row_array();
    }

    public function  getStmScopeTarget($loan_id, $prev_date){
        $this->db->order_by('loan_transaction_id', 'desc');
        $this->db->where(array('loan_id' => $loan_id, 'transaction_datetime >=' => $prev_date ));
        return $this->db->get('coop_loan_transaction', 1)->row_array();
    }

    public function truncate($x, $digits) {
         return round($x - 5 * pow(10, -($digits + 1)), $digits);
    }

    public function mb_round($x, $f = 0){
        if($f-1 >= 1){
            $fx = explode(".", $x)[1];
            /*if(!empty($fx) && $fx == "99"){
                $x += 0.01;
            }*/
        }
        return number_format($x, $f, '.', '');
    }
	
	public function get_loan_type($loan_id){
        $loan_type = $this->db->select(array('loan_type'))->from("coop_loan")
					->where("id = '".$loan_id."'")->get()->row_array()['loan_type'];
		return $loan_type;
    }
	
	//หาวันที่ได้รับเงินประมาณ 
	public function get_date_receive_money($loan_id=''){
		if($loan_id != ''){
			$date_receive_money = $this->db->select(array('date_receive_money'))->from("coop_loan_deduct_profile")
					->where("loan_id = '".$loan_id."'")->get()->row_array()['date_receive_money'];
		}else{
			$date_receive_money = date('Y-m-d');
		}
		return $date_receive_money;
    }

     public function getStmtPaymentLast($loan_id, $entry_date){
         $this->db->order_by('loan_transaction_id', 'desc');
         $this->db->where(array('loan_id' => $loan_id, 'transaction_datetime <=' => $entry_date));
         $this->db->where_in('loan_type_code', self::getPaymentCode());
         return $this->db->get('coop_loan_transaction', 1)->row_array();
     }

     private function getPaymentCode(){
         if($this->_setting) {
             $arr = array();
             foreach ($this->_setting as $key => $val){
                 if($val['atm_sign_mode'] == '-1'){
                     $arr[] = $val['atm_type_code'];
                 }
             }
             return $arr;
         }
         return array();
     }

}
