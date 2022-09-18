<?php if( ! defined('BASEPATH')) exit('No direct script access allowed');

class Update_stament_libbraries extends CI_Model {
	public function __construct()
	{
		parent::__construct();

		$this->menu_path_stack = array();
		$this->is_menu_path_found = FALSE;
    }

    public function update_deposit_transaction($account_id, $transaction_time){
        $this->db->where("transaction_time < '$transaction_time'");
        $this->db->order_by("transaction_time", "ASC");
        $this->db->order_by("transaction_id", "ASC");
        $query = $this->db->get_where("coop_account_transaction", array("account_id" => $account_id) );
        $num_rows = $query->num_rows();
        if($num_rows > 0) {
            foreach ($query->result() as $key => $value) {
                $sql = "UPDATE coop_account_transaction AS main1
            SET transaction_balance = (
                SELECT
                    transaction_balance
                FROM
                    (
                        SELECT
                            *
                        FROM
                            coop_account_transaction AS tmp1 WHERE account_id = '$account_id'
                    ) AS t1
                WHERE t1.account_id = main1.account_id and t1.transaction_time < main1.transaction_time and t1.transaction_id != main1.transaction_id
                ORDER BY transaction_time DESC, transaction_id DESC
                LIMIT 1
            ) + transaction_deposit - transaction_withdrawal
            WHERE
                (
                    account_id = '$account_id'
                    AND transaction_time >= '$transaction_time'
                )";
                // echo "<br><br>".$sql;
                $this->db->query($sql);
            }
        }
	}

	public function update_share_transaction($member_id, $share_start_date){
		
        $this->db->where("share_date < '$share_start_date'");
        $this->db->where("member_id", $member_id);
        $this->db->order_by("share_date", "DESC");
        $this->db->order_by("share_id", "DESC");
        $last_balance = $this->db->get("coop_mem_share")->result()[0];
        if(!$last_balance){
            return null;
        }

        // SPA + 
        // SPM + 
        // SPL +
        // SRF -
        // SPD -
        $this->db->where("member_id", $member_id);
        $this->db->where("share_date >= '$share_start_date'");
        $this->db->order_by("share_date", 'ASC');
        $this->db->order_by("share_id", 'ASC');
        $query = $this->db->get("coop_mem_share");
		
        foreach ($query->result() as $key => $value) {
            $data_update = array();

            switch ($value->share_type) {
                case 'SPA': 
                    $data_update['share_payable']       = $last_balance->share_payable          +   $value->share_early;
                    $data_update['share_payable_value'] = $last_balance->share_payable_value    +   $value->share_early_value;
                    $data_update['share_collect']       = $last_balance->share_collect          +   $value->share_early;
                    $data_update['share_collect_value'] = $last_balance->share_collect_value    +   $value->share_early_value;
                    break;
                case 'SPM': 
                    $data_update['share_payable']       = $last_balance->share_payable          +   $value->share_early;
                    $data_update['share_payable_value'] = $last_balance->share_payable_value    +   $value->share_early_value;
                    $data_update['share_collect']       = $last_balance->share_collect          +   $value->share_early;
                    $data_update['share_collect_value'] = $last_balance->share_collect_value    +   $value->share_early_value;
                    break;
                case 'SPL': 
                    $data_update['share_payable']       = $last_balance->share_payable          +   $value->share_early;
                    $data_update['share_payable_value'] = $last_balance->share_payable_value    +   $value->share_early_value;
                    $data_update['share_collect']       = $last_balance->share_collect          +   $value->share_early;
                    $data_update['share_collect_value'] = $last_balance->share_collect_value    +   $value->share_early_value;
                    break;
                case 'SRF': 
                    $data_update['share_payable']       = $last_balance->share_payable          -   $value->share_early;
                    $data_update['share_payable_value'] = $last_balance->share_payable_value    -   $value->share_early_value;
                    $data_update['share_collect']       = $last_balance->share_collect          -   $value->share_early;
                    $data_update['share_collect_value'] = $last_balance->share_collect_value    -   $value->share_early_value;
                    break;
                case 'SPD': 
                    $data_update['share_payable']       = $last_balance->share_payable          -   $value->share_early;
                    $data_update['share_payable_value'] = $last_balance->share_payable_value    -   $value->share_early_value;
                    $data_update['share_collect']       = $last_balance->share_collect          -   $value->share_early;
                    $data_update['share_collect_value'] = $last_balance->share_collect_value    -   $value->share_early_value;
                    break;
                case 'SDP': 
                    $data_update['share_payable']       = $last_balance->share_payable          +   $value->share_early;
                    $data_update['share_payable_value'] = $last_balance->share_payable_value    +   $value->share_early_value;
                    $data_update['share_collect']       = $last_balance->share_collect          +   $value->share_early;
                    $data_update['share_collect_value'] = $last_balance->share_collect_value    +   $value->share_early_value;
                    break;
                default:
                    continue;
                    break;
            }
			
            if($value->share_status == 1){
                $last_balance->share_payable            =    $data_update['share_payable'];
                $last_balance->share_payable_value      =    $data_update['share_payable_value'];
                $last_balance->share_collect            =    $data_update['share_collect'];
                $last_balance->share_collect_value      =    $data_update['share_collect_value'];
    
				$this->db->where("share_id", $value->share_id);
                $this->db->update("coop_mem_share", $data_update);
				
            }
            
        }
	}

	public function update_loan_transaction($loan_id, $update_start_time){
        $this->db->where("transaction_datetime < '$update_start_time'");
        $this->db->where("loan_id", $loan_id);
        $this->db->order_by("transaction_datetime", "DESC");
        $this->db->order_by("loan_transaction_id", "DESC");
        $last_balance = $this->db->get("coop_loan_transaction")->result()[0];
        if(!$last_balance){
            return null;
        }

        
        $this->db->where("loan_id", $loan_id);
        $this->db->where("transaction_datetime >= '$update_start_time'");
        $this->db->order_by("transaction_datetime", "ASC");
        $this->db->order_by("loan_transaction_id", "ASC");
        $query = $this->db->get("coop_loan_transaction");
        foreach ($query->result() as $key => $value) {
            $finance_transaction = $this->get_finance_transaction($value->receipt_id, $loan_id, null);
            if($finance_transaction){
                $data_update = array();
                $data_update['loan_amount_balance'] = $last_balance->loan_amount_balance - $finance_transaction->principal_payment;
                $last_balance->loan_amount_balance  = $data_update['loan_amount_balance'];

                $this->db->where("loan_transaction_id", $value->loan_transaction_id);
                $this->db->update("coop_loan_transaction", $data_update);
            }

        }
    }

    public function update_loan_atm_transaction($loan_atm_id, $update_start_time){
        $this->db->where("transaction_datetime < '$update_start_time'");
        $this->db->where("loan_atm_id", $loan_atm_id);
        $this->db->order_by("transaction_datetime", "DESC");
        $this->db->order_by("loan_atm_transaction_id", "DESC");
        $last_balance = $this->db->get("coop_loan_atm_transaction")->result()[0];
        if(!$last_balance){
            return null;
        }

        
        $this->db->where("loan_atm_id", $loan_atm_id);
        $this->db->where("transaction_datetime >= '$update_start_time'");
        $this->db->order_by("transaction_datetime", "ASC");
        $this->db->order_by("loan_atm_transaction_id", "ASC");
        $query = $this->db->get("coop_loan_atm_transaction");
        foreach ($query->result() as $key => $value) {
            $finance_transaction = $this->get_finance_transaction($value->receipt_id, null, $loan_atm_id);
            if($finance_transaction){
                $data_update = array();
                $data_update['loan_amount_balance'] = $last_balance->loan_amount_balance - $finance_transaction->principal_payment;
                $last_balance->loan_amount_balance  = $data_update['loan_amount_balance'];

                $this->db->where("loan_atm_transaction_id", $value->loan_atm_transaction_id);
                $this->db->update("coop_loan_atm_transaction", $data_update);
            }

        }
    }
    
    private function get_finance_transaction($receipt_id, $loan_id, $loan_atm_id){
        // $result = $this->db->get_where("coop_finance_transaction", array("receipt_id" => $receipt_id));
        $where = "receipt_id = '".$receipt_id."'";
        if(!empty($loan_id)) {
            $where .= " AND loan_id = '".$loan_id."'";
        }
        if(!empty($loan_atm_id)) {
            $where .= " AND loan_atm_id = '".$loan_atm_id."'";
        }
        $result = $this->db->select("*, SUM(principal_payment) as principal_payment, SUM(interest) as interest")
                            ->from("coop_finance_transaction")
                            ->where($where)
                            ->get()->row();
        return $result;
    }

    public function update_balance_statement($data = array()){

       if($data=="")
            exit;

        $date = $data['date'];
        $account_id = $data['account_id'];
        $this->db->order_by("transaction_time", "ASC");
        $this->db->order_by("transaction_id", "ASC");
		$this->db->where("transaction_time < '".$date."'");
        $this->db->where("account_id", $account_id);
        $query = $this->db->get("coop_account_transaction");
        if(!$query->result()){
            echo "ไม่อนุญาตให้อัพเดทรายการเริ่มต้นได้";
            exit;
        }
        $this->db->order_by("transaction_time", "ASC");
        $this->db->order_by("transaction_id", "ASC");
        $this->db->where("transaction_time >= '".$date."'");
        $this->db->where("account_id", $account_id);
        $query = $this->db->get("coop_account_transaction");
        $first = false;
        $last_balance = 0;
        if(!$query->result_array()){
                echo "ไม่สามารถอัพเดทได้ ตรวจสอบวันที่ให้ถูกต้อง";
                exit;
        }else{
            $this->db->order_by("transaction_time", "DESC");
            $this->db->order_by("transaction_id", "DESC");
            $this->db->limit(1);
            $sub_query = $this->db->get_where("coop_account_transaction", array(
                "account_id" => $account_id,
                "transaction_time < " => $date
            ));

            $last_transaction = $sub_query->result_array()[0];

            if(!$last_transaction){
                $this->db->order_by("transaction_time", "ASC");
                $this->db->order_by("transaction_id", "ASC");
                $this->db->limit(1);
                $sub_query = $this->db->get_where("coop_account_transaction", array(
                    "account_id" => $account_id,
                ));
                $last_transaction = $sub_query->result_array()[0];
            }
            $last_balance = $last_transaction['transaction_balance'];
            $last_no_on_balance = $last_transaction['transaction_no_in_balance'];
        }


        foreach ($query->result_array() as $key => $row) {

            if($row['transaction_list']=="BF"){
                $last_balance = $row['transaction_balance'];
                $last_no_on_balance = $row['transaction_no_in_balance'];
                $skip = false;
                continue;
            }

            if($skip && $row['transaction_list'] != "OPN"){
                $last_balance = $row['transaction_balance'];
                $last_no_on_balance = ($row['transaction_no_in_balance']=='') ? 0 : $row['transaction_no_in_balance'];
                $skip = false;
                continue;
            }else{
                $skip = false;
            }

            if($row['transaction_list'] == "OPN"){
                $last_balance = 0;
                $last_no_on_balance = 0;
            }



            $transaction_id = $row['transaction_id'];

            $new_balance = 0;
            $new_balance_no_in = 0;
            $deposit = $row['transaction_deposit'];
            $withdrawal = $row['transaction_withdrawal'];



            if($deposit!=0 && $withdrawal!=0){
                $new_balance = $last_balance + $deposit - $withdrawal;
                if(!in_array( $row['transaction_list'], ['IN', 'INT', 'WTI'] )){
                    $new_no_in_balance = $last_no_on_balance + $deposit - $withdrawal;
                }else{
                    $new_no_in_balance = $last_no_on_balance;
                }
            }else if($deposit!=0){
                $new_balance = $last_balance + $deposit;
                if(!in_array( $row['transaction_list'], ['IN', 'INT'] )){
                    $new_no_in_balance = $last_no_on_balance + $deposit;
                }else{
                    $new_no_in_balance = $last_no_on_balance;
                }
            }else if($withdrawal!=0){
                $new_balance = $last_balance - $withdrawal;
                if(!in_array( $row['transaction_list'], ['IN', 'INT', 'WTI'] )){
                    $new_no_in_balance = $last_no_on_balance - $withdrawal;
                }else{
                    $new_no_in_balance = $last_no_on_balance;
                }
            }

            // ---------------------------------------------------


            $this->db->set("transaction_balance", $new_balance);
            $this->db->set("transaction_no_in_balance", $new_no_in_balance);
            $this->db->where("transaction_id", $row['transaction_id']);
            $this->db->update("coop_account_transaction");

            $last_balance = $new_balance ;
            $last_no_on_balance = $new_no_in_balance ;
        }
    }
    
}
