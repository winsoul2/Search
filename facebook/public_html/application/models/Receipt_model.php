<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Receipt_model extends CI_Model
{

	public function __construct()
	{
		parent::__construct();
		$this->load->model('Setting_model', 'setting');
	}

	public function cancel_receipt($receipt_id)
	{
		// echo "Cancel::" . $receipt_id . "<br>";
		$receipt = $this->db->get_where("coop_finance_transaction", array(
			"receipt_id" => $receipt_id
		))->result_array();

		if ($this->is_non_pay_receipt($receipt_id)) {
			echo "IS_NON_PAY<br>";
			die("Err");
			$non_pay_receipt = $this->db->get_where("coop_non_pay_receipt", array(
				"receipt_id" => $receipt_id
			))->result_array()[0];
			$non_pay_detail = $this->db->get_where("coop_non_pay_detail", array(
				"non_pay_id" => $non_pay_receipt['non_pay_id']
			))->result_array();
			$non_pay_id = $non_pay_receipt['non_pay_id'];
			foreach ($receipt as $key => $value) {
				if ($this->is_loan($value)) {
					// $this->rollback_loan($value);
					$this->rollback_non_pay_loan($value, $non_pay_id);
					echo "IS_LOAN";
					echo "<br>";
				} else if ($this->is_loan_atm($value)) {
					$this->rollback_loan_atm($value);
					echo "IS_LOAN_ATM";
					echo "<br>";
				} else if ($this->is_share($value)) {
					$this->rollback_share($value);
					$this->rollback_non_pay_share($value, $non_pay_id);

					echo "IS_SHARE";
					echo "<br>";
				} else if ($this->is_fund($value)) {
					$this->rollback_fund($value);
					echo "IS_FUND";
					echo "<br>";
				}
			}

			foreach ($non_pay_detail as $key => $value) {
				$deduct_code = $value['deduct_code'];
				$pay_type = $value['pay_type'];
			}
		} else if ($this->is_finance_month_receipt($receipt_id)) {
			echo "IS_FINANCE_MONTH<br>";
			die("Err");
		} else {
			// echo "IS_OTHER_RECEIPT<br>";
			foreach ($receipt as $key => $value) {
				if ($this->is_loan($value)) {
					$this->rollback_loan($value);
					// echo "IS_LOAN";
					// echo "<br>";
				} else if ($this->is_loan_atm($value)) {
					$this->rollback_loan_atm($value);
					// echo "IS_LOAN_ATM";
					// echo "<br>";
				} else if ($this->is_share($value)) {
					$this->rollback_share($value);
					// echo "IS_SHARE";
					// echo "<br>";
				} else if ($this->is_fund($value)) {
					$this->rollback_fund($value);
					// echo "IS_FUND";
					// echo "<br>";
				}
			}
		}

		$this->db->set("cancel_date", date("Y-m-d h:i:s"));
		$this->db->set("order_by", "");
		$this->db->set("cancel_by", $_SESSION['USER_ID']);
		$this->db->set("receipt_status", 2);
		$this->db->where("receipt_id", $receipt_id);
		$this->db->update("coop_receipt");
		echo "success";
	}

	public function is_non_pay_receipt($receipt_id)
	{
		$non_pay = $this->db->get_where("coop_non_pay_receipt", array(
			"receipt_id" => $receipt_id
		))->result_array();
		if (!empty($non_pay)) return true;
		return false;
	}

	public function is_finance_month_receipt($receipt_id)
	{
		$finance_month_receipt = $this->db->select("sum(period_count) as count")
			->from("coop_finance_transaction")
			->where("receipt_id = '" . $receipt_id . "'")
			->get()
			->result_array()[0];
		if ($finance_month_receipt['count'] > 0) return true;
		return false;
	}

	public function rollback_share($finance_transaction_row = array())
	{
		$share_transaction = $this->db->get_where("coop_mem_share", array(
			"share_bill" => $finance_transaction_row['receipt_id']
		))->result_array()[0];
		$latest_transaction = $this->db->select("*")
			->from("coop_mem_share")
			->where("member_id = '" . $finance_transaction_row['member_id'] . "'")
			->order_by("share_date desc, share_id desc")
			->limit(1)
			->get()
			->result_array()[0];
		if ($share_transaction['share_id']) {
			if ($share_transaction['share_id'] == $latest_transaction['share_id']) {
				//is latest
				$this->db->where("share_id", $share_transaction['share_id']);
				$this->db->where("share_bill", $finance_transaction_row['receipt_id']);
				$this->db->delete("coop_mem_share");
			} else {
				//is not latest
				die("Err share");
			}
		}
	}

	public function rollback_deposit($finance_transaction_row = array())
	{
		$this->db->join("coop_maco_account as t2", "t1.account_id = t2.account_id", "inner");
		$deposit_transaction = $this->db->get_where("coop_account_transaction as t1", array(
			"mem_id" => $finance_transaction_row['member_id'],
			"receipt_id" => $finance_transaction_row['receipt_id']
		))->result_array()[0];
		$this->db->order_by("transaction_time desc, transaction_id desc");
		$this->db->limit(1);
		$latest_transaction = $this->db->get_where("coop_account_transaction", array(
			"account_id" => $deposit_transaction['account_id']
		))->result_array()[0];
		if ($deposit_transaction['transaction_id']) {
			if ($deposit_transaction['transaction_id'] == $latest_transaction['transaction_id']) {
				//is latest
				$this->db->where("account_id", $deposit_transaction['account_id']);
				$this->db->where("transaction_id", $deposit_transaction['transaction_id']);
				$this->db->where("receipt_id", $finance_transaction_row['receipt_id']);
				$this->db->delete("coop_account_transaction");
			} else {
				//is not latest
				die("Err deposit");
			}
		}
	}

	public function rollback_loan($finance_transaction_row = array())
	{
		$loan_id = $finance_transaction_row['loan_id'];
		$principal_payment = $finance_transaction_row['principal_payment'];
		$interest = $finance_transaction_row['interest'];
		$loan_transaction = $this->db->get_where("coop_loan_transaction", array(
			"loan_id" => $loan_id,
			"receipt_id" => $finance_transaction_row['receipt_id']
		))->result_array()[0];
		$loan_transaction_id = $loan_transaction['loan_transaction_id'];
		if ($loan_transaction_id) {
			//check is last transaction
			$latest_transaction = $this->db->select("*")
				->from("coop_loan_transaction")
				->where("loan_id = " . $loan_id)
				->order_by("transaction_datetime desc, loan_transaction_id desc")
				->limit(1)
				->get()
				->result_array()[0];
			if ($loan_transaction['loan_transaction_id'] == $latest_transaction['loan_transaction_id']) {
				//is latest
				$this->db->where("loan_transaction_id", $loan_transaction['loan_transaction_id']);
				$this->db->limit(1);
				$this->db->delete("coop_loan_transaction");

				//หาค่าสำหรับนำมาอัพเดท coop_loan
				$previous_transaction = $this->db->select("*")
					->from("coop_loan_transaction")
					->where("loan_id = " . $loan_id)
					->order_by("transaction_datetime desc, loan_transaction_id desc")
					->limit(1)
					->get()
					->result_array()[0];

				// echo "<pre>";
				// var_dump($previous_transaction);
				// exit;
				if ($finance_transaction_row['period_count'] != "") {
					$this->db->set("period_now = (period_now-1)");
				}
				if ($previous_transaction['loan_amount_balance'] > 0) {
					$this->db->set("loan_status", 1);
				}
				$this->db->set("updatetimestamp", date("Y-m-d h:i:s"));
				// $this->db->set("updatetime", date("Y-m-d h:i:s"));
				// $this->db->set("lastupdate_datetime", date("Y-m-d h:i:s"));
				// $this->db->set("lastupdate_by", @$_SESSION['USER_ID']);
				$this->db->set("date_last_interest", $previous_transaction['transaction_datetime']);
				$this->db->set("loan_amount_balance", $previous_transaction['loan_amount_balance']);
				$this->db->where("id", $loan_id);
				$this->db->update("coop_loan");
			} else {
				//is not latest
				die("Err loan" . $loan_transaction['loan_transaction_id'] . " || " . $latest_transaction['loan_transaction_id']);
			}
		}

		// var_dump($latest_transaction);
	}

	public function rollback_loan_atm($finance_transaction_row = array())
	{
	}

	public function rollback_fund($finance_transaction_row = array())
	{
	}

	public function rollback_non_pay_loan($finance_transaction_row = array(), $non_pay_id = "")
	{
		$principal = $finance_transaction_row['principal'];
		$interest = $finance_transaction_row['interest'];
		if ($principal > 0) {
			$non_pay_detail = $this->db->get_where("coop_non_pay_detail", array(
				"non_pay_id" => $non_pay_id,
				"loan_id" => $finance_transaction_row['loan_id'],
				"pay_type" => "principal",
				"deduct_code" => "LOAN"
			))->result_array()[0];
			$this->db->set("non_pay_amount_balance = (non_pay_amount_balance+" . $principal . ")");
			$this->db->where("run_id", $non_pay_detail['run_id']);
			$this->db->where("non_pay_id", $non_pay_detail['non_pay_id']);
			$this->db->update("coop_non_pay_detail");
		}

		if ($interest > 0) {
			$non_pay_detail = $this->db->get_where("coop_non_pay_detail", array(
				"non_pay_id" => $non_pay_id,
				"loan_id" => $finance_transaction_row['loan_id'],
				"pay_type" => "interest",
				"deduct_code" => "LOAN"
			))->result_array()[0];
			$this->db->set("non_pay_amount_balance = (non_pay_amount_balance+" . $interest . ")");
			$this->db->where("run_id", $non_pay_detail['run_id']);
			$this->db->where("non_pay_id", $non_pay_detail['non_pay_id']);
			$this->db->update("coop_non_pay_detail");
		}
	}

	public function rollback_non_pay_share($finance_transaction_row = array(), $non_pay_id = "")
	{
		$principal = $finance_transaction_row['principal_payment'];
		$non_pay_detail = $this->db->get_where("coop_non_pay_detail", array(
			"non_pay_id" => $non_pay_id,
			"pay_type" => "principal",
			"deduct_code" => "SHARE"
		))->result_array()[0];
		$this->db->set("non_pay_amount_balance", "(non_pay_amount_balance+" . $principal . ")", false);
		$this->db->where("run_id", $non_pay_detail['run_id']);
		$this->db->where("non_pay_id", $non_pay_detail['non_pay_id']);
		$this->db->update("coop_non_pay_detail");
	}

	public function is_loan($finance_transaction_row = array())
	{
		if ($finance_transaction_row['loan_id'] != "") return true;
		return false;
	}

	public function is_loan_atm($finance_transaction_row = array())
	{
		if ($finance_transaction_row['loan_atm_id'] != "") return true;
		return false;
	}

	public function is_share($finance_transaction_row = array())
	{
		$share = $this->db->get_where("coop_mem_share", array(
			"share_bill" => $finance_transaction_row['receipt_id'],
			"share_early_value" => $finance_transaction_row['principal_payment']
		))->result_array();
		if (!empty($share)) return true;
		return false;
	}

	public function is_deposit($finance_transaction_row = array())
	{
		$this->db->join("coop_maco_account as t2", "t1.account_id = t2.account_id", "inner");
		$deposit = $this->db->get_where("coop_account_transaction as t1", array(
			"mem_id" => $finance_transaction_row['member_id'],
			"receipt_id" => $finance_transaction_row['receipt_id']
		))->result_array()[0];
		if (!empty($deposit)) return true;
		return false;
	}

	public function is_fund($finance_transaction_row = array())
	{
		if ($finance_transaction_row['account_list_id'] == 34) return true;
		return false;
	}

	/**
	 * สร้างเลขใบเสร็จ
	 * create by:    adisak sununtha
	 * create date: 2020-06-09
	 * description:     ปรับการสร้างเลขใบเสร็จตามคำขอของ รฟท ด้วยการเพิ่ม T, H เพื่อแยกเลขใบเสร็จ
	 * TX คือ โอนเงิน, CH คือ เงินสด
	 * @param string $date_now yyyy-mm-dd
	 * @param int $payType 0 คือเงินสด, 1 คือ เงินโอน
	 * @param bool $new
	 * @return string
	 */
	public function generate_receipt($date_now, $payType = 0, $new = true)
	{
		if ($this->setting->get('receipt_format') == '1' && $new == true) {
			$receipt_number = self::create_receipt($date_now, $payType);
		} else {

			$time = strtotime($date_now);
			$yymm = (date("Y", $time) + 543) . date("m", $time);
			$this->db->select('*');
			$this->db->from('coop_receipt');
			$this->db->where("receipt_id LIKE '" . $yymm . "%'");
			$this->db->order_by("receipt_id DESC");
			$this->db->limit(1);
			$row = $this->db->get()->result_array();
			if (!empty($row)) {
				$id = (int)substr($row[0]["receipt_id"], 6);
				$receipt_number = $yymm . sprintf("%06d", $id + 1);
			} else {
				$receipt_number = $yymm . "000001";
			}
		}
		return $receipt_number;
	}



	public function create_receipt($date = '', $payType = 0)
	{
		if (empty($date)) {
			$date = date('Y-m-d');
		}

		$format = self::generate_pattern($payType, $date);
		$receipt = self::findReceipt($format);
		if(strlen($receipt)){
			$number = ((int)substr($receipt, 6, 6))+1;
		}else{
			$number = 1;
		}

		return self::verifyReceipt($payType, $date, $number);
	}

	/**
	 *เลือกประเภทใบเสร็จ
	 * @param $payType
	 * @param $date
	 * @return array|mixed
	 */
	public function getReceiptByType($payType, $date){
		return $this->db->order_by("startdate", "desc")->get_where("coop_receipt_setting",
			array("pay_type" => $payType, "startdate <=" => $date))
			->row_array();
	}

	public function generate_pattern($pay_type, $date, $without = false, $number = 1){
		$month = date("n", strtotime($date));
		$year = date("Y", strtotime($date))+543;

		$setting = self::getReceiptByType($pay_type, $date);
		if(empty($setting)){
			echo "find not found.";
			exit;
		}

		$setting = (object) $setting;
		$prefix = $setting->prefix;
		$pattern = $setting->format;

		if(!$without && strpos( $pattern, "RUNNO")){
			$pattern = substr($pattern, 0, strlen($pattern)-6);
		}

		$str = preg_replace('/PREFIX/u', $prefix, $pattern);
		$str = preg_replace('/YTH/u', ($year), $str);
		$str = preg_replace('/yth/u', substr(($year),2,4), $str);
		$str = preg_replace('/Y/u', ($year-543), $str);
		$str = preg_replace('/y/u', substr(($year-543),2,4), $str);
		$str = preg_replace('/MM/u', sprintf("%02d",$month), $str);
		$str = preg_replace('/n/u', (int)$month, $str);
		if($without) {
			$str = preg_replace('/RUNNO1/u', sprintf("%01d", $number), $str);
			$str = preg_replace('/RUNNO2/u', sprintf("%02d", $number), $str);
			$str = preg_replace('/RUNNO3/u', sprintf("%03d", $number), $str);
			$str = preg_replace('/RUNNO4/u', sprintf("%04d", $number), $str);
			$str = preg_replace('/RUNNO5/u', sprintf("%05d", $number), $str);
			$str = preg_replace('/RUNNO6/u', sprintf("%06d", $number), $str);
		}


		return $str;
	}

	/**
	 * ตรวจสอบกรณีใบเสร็จซ้ำในระบบ
	 * @param $format
	 * @param int $number
	 * @param bool $flag
	 * @return string
	 */
	private function verifyReceipt( $pay_type, $date, $number = 0)
	{
		$receipt = self::generate_pattern($pay_type, $date, true,  $number);
		//$receipt = sprintf("%s%06d", $format, $number);
		$chk_receipt = self::findReceipt($receipt);
		if ($chk_receipt) {
			$number++;
			return self::verifyReceipt($pay_type, $date, $number);
		} else {
			return $receipt;
		}
	}

	private function findReceipt($format = ""){
		return $this->db->query("SELECT fn_get_receipt_number('{$format}') as receipt_id")
			->row_array()['receipt_id'];
	}
		
	//ข้อมูลใบเสร็จคืนเงิน
	public function save_receipt_refund($data){
		$yymm = (date("Y") + 543) . date("m");
		$mm = date("m");
		$yy = (date("Y") + 543);
		$yy_full = (date("Y") + 543);
		$yy = substr($yy, 2);
		$this->db->select('*');
		$this->db->from('coop_receipt_refund');
		$this->db->where("receipt_refund_id LIKE '" . $yy_full . $mm . "%'");
		$this->db->order_by("receipt_refund_id DESC");
		$this->db->limit(1);
		$row = $this->db->get()->row_array();

		if (!empty($row)) {
			$id = (int)substr($row["receipt_refund_id"], 6);
			$receipt_number = 'RD'.$yymm . sprintf("%06d", $id + 1);
		} else {
			$receipt_number = 'RD'.$yymm . "000001";
		}

		$pay_principal = number_format($data['pay_principal'], 2, '.', '');
		$pay_interest = number_format($data['pay_interest'], 2, '.', '');
		$pay_amount = @$pay_principal+@$pay_interest;

        $data_insert = array();
        $data_insert['receipt_refund_id'] = $receipt_number;
        $data_insert['member_id'] = $data['member_id'];
        $data_insert['sumcount'] = $pay_amount;
        $data_insert['receipt_datetime'] = $data['date_transaction'];
        $data_insert['admin_id'] = $_SESSION['USER_ID'];
        $data_insert['pay_type'] = $data["pay_type"];
        $data_insert['pay_for'] = $data['pay_for'];
		$data_insert['createdatetime'] = date("Y-m-d h:i:s");
        $this->db->insert('coop_receipt_refund', $data_insert);
		if($this->db->affected_rows()){
			$data_insert = array();
            $data_insert['member_id'] = $data['member_id'];
            $data_insert['receipt_refund_id'] = $receipt_number;
            $data_insert['account_list_id'] = $data['account_list_id'];
            $data_insert['principal_payment'] = $pay_principal;
            $data_insert['interest_payment'] = $pay_interest;
            $data_insert['total_amount'] = $pay_amount;
            $data_insert['payment_date'] = $data['date_transaction'];
            $data_insert['createdatetime'] = date("Y-m-d h:i:s");
            $data_insert['pay_description'] = $data['pay_description'];
            $data_insert['process'] = $data['process'];
            $this->db->insert('coop_receipt_detail_refund', $data_insert);
			
			$arr_data['status'] = 'success';
		}else{
			$arr_data['status'] = 'error';
		}
		
		$arr_data['receipt_refund_id'] = $receipt_number;
		return $arr_data;
	}
	
	//หาเลขที่ใบเสร็จจากการคืนเงินฝาก
	public function get_account_receipt_refund_id($arr_account){
		$arr_data = array();
		if(!empty($arr_account)){
			$row = $this->db->select("account_id,receipt_refund_id")->from("coop_account_transaction")
				->where("account_id IN  (".implode(',', $arr_account).")  AND receipt_refund_id IS NOT NULL")
				->get()->result_array();
			$arr_data = array_column($row, 'receipt_refund_id', 'account_id');
		}		
		return $arr_data;
	}

	public function add_receipt($datetime = "", $type = 0, $text = ""){
		if(empty($datetime)){
			$datetime = date("Y-m-d H:i:s");
		}
		$receipt_id = self::generate_receipt($datetime, $type);
		if($text != ""){
			$text = self::getReceiptByType($datetime, $type);
		}
		$this->db->trans_begin();
		$data['receipt_id'] = $receipt_id;
		$data['receipt_code'] = $text;
		$data['receipt_datetime'] = $datetime;
		$this->db->insert("coop_receipt", $data);
		if($this->db->trans_status() === false){
			$this->db->trans_rollback();
			return $this->add_receipt($datetime, $type);
		}else{
			$this->db->trans_commit();
			return $receipt_id;
		}
	}

	public function getPayTypeByKeepingReceiptGroup($mem_type_id){
		return $this->db->get_where("coop_keeping_group", array("mem_type_id" => $mem_type_id))
			->row()->pay_type;
	}

}
