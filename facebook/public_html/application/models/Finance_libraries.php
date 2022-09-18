<?php if( ! defined('BASEPATH')) exit('No direct script access allowed');

class Finance_libraries extends CI_Model {
	public function __construct() {
		parent::__construct();
    }

    public function generate_cashier_receipt_id($receipt_format, $date) {
        if(empty($date)) {
            $date = date('Y-m-d H:i:s');
        }

        $mm = date('m',strtotime($date));
        $yy = (date('Y',strtotime($date)) + 543);
        $yymm = $receipt_format == 2 || $receipt_format == 3 ? $yy : $yy . $mm;
        $this->db->select('*');
        $this->db->from('coop_receipt');
        if($receipt_format == 3) {
            $this->db->where("receipt_id LIKE '" . $yy . "02%'");
        } else {
            $this->db->where("receipt_id LIKE '" . $yymm . "%'");
        }
        $this->db->order_by("receipt_id DESC");
        $this->db->limit(1);
        $row = $this->db->get()->result_array();
        if($receipt_format == 2) {
            if (!empty($row)) {
                $id = (int)substr($row[0]["receipt_id"], 4);
                $receipt_number = $yymm . sprintf("%07d", $id + 1);
            } else {
                $receipt_number = $yymm . "0000001";
            }
        } else if($receipt_format == 3) {
            if (!empty($row)) {
                $id = (int)substr($row[0]["receipt_id"], -5);
                $receipt_number = $yymm . "02" . sprintf("%05d", $id + 1);
            } else {
                $receipt_number = $yymm . "0200001";
            }
        } else {
            if (!empty($row)) {
                $id = (int)substr($row[0]["receipt_id"], 6);
                $receipt_number = $yymm . sprintf("%06d", $id + 1);
            } else {
                $receipt_number = $yymm . "000001";
            }
        }

        return $receipt_number;
    }

    public function generate_non_pay_receipt_id($receipt_format, $date, $non_pay_month, $non_pay_year) {
        if(empty($date)) {
            $date = date('Y-m-d H:i:s');
        }

        if($receipt_format == 1) {		
            $yymm_check = (date('Y',strtotime($date))+543).date('m',strtotime($date));
            $yymm_check = date('m',strtotime($date));
            $yy_check = (date('Y',strtotime($date))+543);

            $yymm = sprintf("%02d", $non_pay_month);
            $yy_full = (date('Y',strtotime($date))+543);
            $yy = substr($non_pay_year,2);
            $text = 'C';
            $this->db->select('*');
            $this->db->from('coop_receipt');
            $this->db->where("receipt_id LIKE '".'%__C__'.$yy_check."%'");
            $this->db->order_by("order_by DESC");
            $this->db->limit(1);
            $row = $this->db->get()->result_array();
            if(!empty($row)) {
                $id = (int) substr($row[0]["receipt_id"], 9);
                $receipt_number = $yymm.''.$text.''.$yy.$yy_check.sprintf("%06d", $id + 1);
            } else {
                $receipt_number = $yymm.''.$text.''.$yy.$yy_check."000001";
            }
        } else {
            $mm = date('m',strtotime($date));
            $yy = (date('Y',strtotime($date)) + 543);
            $yymm = $receipt_format == 2 || $receipt_format == 3 ? $yy : $yy . $mm;
            $this->db->select('*');
            $this->db->from('coop_receipt');
            if($receipt_format == 3) {
                $this->db->where("receipt_id LIKE '" . $yy . "02%'");
            } else {
                $this->db->where("receipt_id LIKE '" . $yymm . "%'");
            }
            $this->db->order_by("receipt_id DESC");
            $this->db->limit(1);
            $row = $this->db->get()->result_array();
            if($receipt_format == 2) {
                if (!empty($row)) {
                    $id = (int)substr($row[0]["receipt_id"], 4);
                    $receipt_number = $yymm . sprintf("%07d", $id + 1);
                } else {
                    $receipt_number = $yymm . "0000001";
                }
            } else if($receipt_format == 3) {
                if (!empty($row)) {
                    $id = (int)substr($row[0]["receipt_id"], -5);
                    $receipt_number = $yymm . "02" . sprintf("%05d", $id + 1);
                } else {
                    $receipt_number = $yymm . "0200001";
                }
            } else {
                if (!empty($row)) {
                    $id = (int)substr($row[0]["receipt_id"], 6);
                    $receipt_number = $yymm . sprintf("%06d", $id + 1);
                } else {
                    $receipt_number = $yymm . "000001";
                }
            }
        }

        return $receipt_number;
    }

    public function generate_finance_month_receipt_id($receipt_format, $text, $date) {
        if(empty($date)) {
            $date = date('Y-m-d H:i:s');
        }

        if($receipt_format == 1) {
            $yymm = date("m", strtotime($date));
            $yy = (date("Y", strtotime($date))+543);
            $yy_full = (date("Y", strtotime($date))+543);
            $yy = substr($yy,2);

            if(empty($text)) {
                $text = 'B';
            }

            $this->db->select(array('*'));
            $this->db->from('coop_receipt');
            $this->db->where("receipt_code ='B' OR receipt_code = 'F'");
            $this->db->order_by("order_by DESC");
            $this->db->limit(1);

            $row_receipt = $this->db->get()->result_array();
            $row_receipt = @$row_receipt[0];

            if($row_receipt['receipt_id'] != '') {
                $id = (int) substr($row_receipt["receipt_id"], 6);
                $receipt_number = $yymm.''.$text.''.$yy.sprintf("%06d", $id + 1);
            }else {
                $receipt_number = $yymm.''.$text.''.$yy."000001";
            }

            $order_by_id =  $row_receipt["order_by"]+1 ;

			$sql = "SELECT receipt_id
					FROM coop_receipt
					WHERE receipt_id = '".$receipt_number."'";
			$rs_chk_receipt = $this->db->query($sql);
			if($rs_chk_receipt->num_rows() == 1){
				$this->db->select(array('*'));
				$this->db->from('coop_receipt');
				$this->db->where("receipt_id = 'B' OR receipt_id  = 'F' ");
				$this->db->order_by("order_by DESC");
				$this->db->limit(1);
				$row_receipt = $this->db->get()->result_array();
				$row_receipt = @$row_receipt[0];
				if($row_receipt['receipt_id'] != '') {
					$id = (int) substr($row_receipt["receipt_id"], 6);
					$receipt_number = $yymm.''.$text.''.$yy.sprintf("%06d", $id + 1);
				}else {
					$receipt_number = $yymm.''.$text.''.$yy."000001";
				}
				$order_by_id =  $row_receipt["order_by"]+1 ;
			}
        } else {
            $mm = date('m',strtotime($date));
            $yy = (date('Y',strtotime($date)) + 543);
            $yymm = $receipt_format == 2 || $receipt_format == 3 ? $yy : $yy . $mm;
            $this->db->select('*');
            $this->db->from('coop_receipt');
            if($receipt_format == 3) {
                $this->db->where("receipt_id LIKE '" . $yy . "01%'");
            } else {
                $this->db->where("receipt_id LIKE '" . $yymm . "%'");
            }
            $this->db->order_by("receipt_id DESC");
            $this->db->limit(1);
            $row = $this->db->get()->result_array();
            if($receipt_format == 2) {
                if (!empty($row)) {
                    $id = (int)substr($row[0]["receipt_id"], 4);
                    $receipt_number = $yymm . sprintf("%07d", $id + 1);
                } else {
                    $receipt_number = $yymm . "0000001";
                }
            } else if($receipt_format == 3) {
                if (!empty($row)) {
                    $id = (int)substr($row[0]["receipt_id"],-5);
                    $receipt_number = $yymm . "01" . sprintf("%05d", $id + 1);
                } else {
                    $receipt_number = $yymm . "0119657";//For bbcoop please remove after run financemonth 2020-10.
                }
            } else {
                if (!empty($row)) {
                    $id = (int)substr($row[0]["receipt_id"], 6);
                    $receipt_number = $yymm . sprintf("%06d", $id + 1);
                } else {
                    $receipt_number = $yymm . "000001";
                }
            }
        }

        return $receipt_number;
    }

    public function check_receipt_id($receipt_id) {
        $result = array();
        $receipt = $this->db->select("receipt_status, receipt_id")->from("coop_receipt")->where("receipt_id = '".$receipt_id."'")->get()->row_array();
        if(!empty($receipt)) {
            $result['receipt_id'] = $receipt['receipt_id'];
            $result['status'] = empty($receipt['receipt_status']) ? 0 : $receipt['receipt_status'];
            $result['status_name'] = empty($receipt['receipt_status']) ? "ปกติ" : ($receipt['receipt_status'] == 1 ? "รอการยืนยัน" : "ถูกยกเลิกแล้ว");
			//$result['encode_receipt_id'] = jwt::urlsafeB64Encode($this->center_function->encrypt_text($receipt['receipt_id']));
            $result['encode_receipt_id'] = $receipt['receipt_id'];
        } else {
            $result['receipt_id'] = NULL;
        }

        return $result;
    }

    public function check_receipt_id_with_coop_buy($receipt_id) {
        $result = array();
        $receipt = $this->db->select("receipt_status, receipt_id")->from("coop_receipt")->where("receipt_id = '".$receipt_id."'")->get()->row_array();
        $account_buy_number = $this->db->select("account_buy_id, account_buy_status, account_buy_number")
            ->from("coop_account_buy")
            ->where("account_buy_number ='".$receipt_id."'")->get()->row_array();
        if(!empty($receipt)) {
            $result['receipt_id'] = $receipt['receipt_id'];
            $result['status'] = empty($receipt['receipt_status']) ? 0 : $receipt['receipt_status'];
            $result['status_name'] = empty($receipt['receipt_status']) ? "ปกติ" : ($receipt['receipt_status'] == 1 ? "รอการยืนยัน" : "ถูกยกเลิกแล้ว");
			//$result['encode_receipt_id'] = jwt::urlsafeB64Encode($this->center_function->encrypt_text($receipt['receipt_id']));
            $result['encode_receipt_id'] = $receipt['receipt_id'];

            $result['receipt_route'] = 'receipt';
        }
        else if(!empty($account_buy_number)){
            $result['receipt_id'] = $account_buy_number['account_buy_id'];
            $result['status'] = empty($account_buy_number['account_buy_status']) ? 0 : $account_buy_number['account_buy_status'];
            $result['status_name'] = empty($account_buy_number['account_buy_status']) ? "ปกติ" : ($account_buy_number['account_buy_status'] == 1 ? "รอการยืนยัน" : "ถูกยกเลิกแล้ว");
            $result['encode_receipt_id'] = $account_buy_number['account_buy_id'];

            $result['receipt_route'] = 'account_buy_number';
        }
        else {
            $result['receipt_id'] = NULL;
        }

        return $result;
    }

    public function change_receipt_id($receipt_id, $new_receipt_id) {
        $result = array();
        $check_receipt_id = $this->db->select("receipt_status, receipt_id")->from("coop_receipt")->where("receipt_id = '".$receipt_id."'")->get()->row_array();
        $check_new_receipt_id = $this->db->select("receipt_id")->from("coop_receipt")->where("receipt_id = '".$new_receipt_id."'")->get()->row_array();
        if (empty($check_receipt_id)) {
            $result['status'] = 0;
            $result['message'] = "ไม่พบใบเสร็จเดิมในระบบ";
        } else if(!empty($check_new_receipt_id)) {
            $result['status'] = 0;
            $result['message'] = "ไม่สามารถเปลี่ยนเป็นเลขที่ใบเสร็จที่มีอยู่ในระบบได้";
        } else {
            $data_insert = array();
            $data_insert['receipt_id'] = $new_receipt_id;
            $this->db->where('receipt_id', $receipt_id);
            $this->db->update('coop_receipt', $data_insert);

            $data_insert = array();
            $data_insert['receipt_id'] = $new_receipt_id;
            $this->db->where('receipt_id', $receipt_id);
            $this->db->update('coop_receipt_detail', $data_insert);

            $data_insert = array();
            $data_insert['receipt_id'] = $new_receipt_id;
            $this->db->where('receipt_id', $receipt_id);
            $this->db->update('coop_finance_transaction', $data_insert);

            $data_insert = array();
            $data_insert['receipt_id'] = $new_receipt_id;
            $this->db->where('receipt_id', $receipt_id);
            $this->db->update('coop_loan_transaction', $data_insert);

            $data_insert = array();
            $data_insert['receipt_id'] = $new_receipt_id;
            $this->db->where('receipt_id', $receipt_id);
            $this->db->update('coop_loan_atm_transaction', $data_insert);

            $data_insert = array();
            $data_insert['share_bill'] = $new_receipt_id;
            $this->db->where('share_bill', $receipt_id);
            $this->db->update('coop_mem_share', $data_insert);

            $data_insert = array();
            $data_insert['deduct_receipt_id'] = $new_receipt_id;
            $this->db->where('deduct_receipt_id', $receipt_id);
            $this->db->update('coop_loan', $data_insert);

            $data_insert = array();
            $data_insert['receipt_id'] = $new_receipt_id;
            $this->db->where('receipt_id', $receipt_id);
            $this->db->update('coop_process_return', $data_insert);

            $data_insert = array();
            $data_insert['receipt_id'] = $new_receipt_id;
            $this->db->where('receipt_id', $receipt_id);
            $this->db->update('coop_dividend_average_receipt', $data_insert);

            $data_insert = array();
            $data_insert['ref_id'] = $new_receipt_id;
            $this->db->where('ref_id', $receipt_id);
            $this->db->update('coop_account', $data_insert);

            $data_insert = array();
            $data_insert['old_receipt_id'] = $receipt_id;
            $data_insert['new_receipt_id'] = $new_receipt_id;
            $data_insert['user_id'] = $_SESSION['USER_ID'];
            $data_insert['created_at'] = date('Y-m-d H:i:s');
            $this->db->insert('coop_receipt_id_change_history', $data_insert);

            $result['status'] = 1;
            $result['message'] = "ทำรายการสำเร็จ";
            //$result['encode_receipt_id'] = jwt::urlsafeB64Encode($this->center_function->encrypt_text($new_receipt_id));
            $result['encode_receipt_id'] = $new_receipt_id;
        }
        return $result;
    }

    public function generate_loan_receipt_text_cashier($loan_id, $account_list_id) {
        $result = "";
        //Get receipt format setting.
        $format_setting = $this->db->select("value")->from("coop_setting_finance")->where("name = 'cashier_receipt_loan_text' AND status = 1")->order_by("created_at DESC")->get()->row_array();
        $format_type = !empty($format_setting) ? $format_setting['value'] : 1; // Default 1.

        if($format_type == 2) {
            // รูปแบบสำนักงบ ex [coop_loan_name.type_name] [coop_loan.contract_number]
            $loan = $this->db->select("t1.contract_number, t2.loan_name")
                                ->from("coop_loan as t1")
                                ->join("coop_loan_name as t2", "t1.loan_type = t2.loan_name_id", "left")
                                ->where("t1.id = '".$loan_id."'")
                                ->get()->row_array();
            $result = $loan['loan_name']." ".$loan['contract_number'];
        } else {
            //For format_type == 1 or empty.
            //รูปแบบตั้งต้น ex. [coop_account_list.account_list]เลขที่สัญญา [coop_loan.contract_number]
            $loan = $this->db->select("contract_number")->from("coop_loan")->where("id = '".$loan_id."'")->get()->row_array();
            $account_list = $this->db->select("account_list")->from("coop_account_list")->where("account_id = '".$account_list_id."'")->get()->row_array();
            $result = $account_list['account_list']."เลขที่สัญญา ".$loan['contract_number'];
        }

        return $result;
    }

    public function generate_loan_receipt_text_finance_month($loan_id, $account_list_id, $deduct_type) {
        $result = "";
        //Get receipt format setting.
        $format_setting = $this->db->select("value")->from("coop_setting_finance")->where("name = 'cashier_receipt_loan_text' AND status = 1")->order_by("created_at DESC")->get()->row_array();
        $format_type = !empty($format_setting) ? $format_setting['value'] : 1; // Default 1.

        if($format_type == 2) {
            // รูปแบบสำนักงบ ex [coop_loan_name.type_name] [coop_loan.contract_number]
            $loan = $this->db->select("t1.contract_number, t2.loan_name")
                                ->from("coop_loan as t1")
                                ->join("coop_loan_name as t2", "t1.loan_type = t2.loan_name_id", "left")
                                ->where("t1.id = '".$loan_id."'")
                                ->get()->row_array();
            $result = $loan['loan_name']." ".$loan['contract_number'];
        } else {
            //For format_type == 1 or empty.
            //รูปแบบตั้งต้น ex. [coop_account_list.account_list]เลขที่สัญญา [coop_loan.contract_number]
            $loan = $this->db->select("contract_number")->from("coop_loan")->where("id = '".$loan_id."'")->get()->row_array();
            $result = $deduct_type == "interest" ? "ดอกเบี้ยเงินกู้เลขที่สัญญา ".$loan['contract_number'] : "ต้นเงินกู้เลขที่สัญญา ".$loan['contract_number'];
        }

        return $result;
    }

    public function generate_loan_receipt_text_deduct($loan_id) {
        $result = "";
        //Get receipt format setting.
        $format_setting = $this->db->select("value")->from("coop_setting_finance")->where("name = 'cashier_receipt_loan_text' AND status = 1")->order_by("created_at DESC")->get()->row_array();
        $format_type = !empty($format_setting) ? $format_setting['value'] : 1; // Default 1.

        if($format_type == 2) {
            // รูปแบบสำนักงบ ex [coop_loan_name.type_name] [coop_loan.contract_number]
            $loan = $this->db->select("t1.contract_number, t2.loan_name")
                                ->from("coop_loan as t1")
                                ->join("coop_loan_name as t2", "t1.loan_type = t2.loan_name_id", "left")
                                ->where("t1.id = '".$loan_id."'")
                                ->get()->row_array();
            $result = $loan['loan_name']." ".$loan['contract_number'];
        } else {
            //For format_type == 1 or empty.
            //รูปแบบตั้งต้น ex. [coop_account_list.account_list]เลขที่สัญญา [coop_loan.contract_number]
            $loan = $this->db->select("contract_number")->from("coop_loan")->where("id = '".$loan_id."'")->get()->row_array();
            $result = 'หักกลบเงินกู้เลขที่สัญญา '.$loan['contract_number'];
        }

        return $result;
    }
}
