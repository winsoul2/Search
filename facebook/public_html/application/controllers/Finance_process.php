<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Finance_process extends CI_Controller {
	function __construct()
	{
		parent::__construct();
	}
	public function finance_month_other()
	{
		$arr_data = array();
		$month_arr = $this->center_function->month_arr();
		$arr_data['month_arr'] = $month_arr;
		$month = (int)date('m');
		$year = date('Y')+543;
		$arr_data['year'] = $year;
		$arr_data['month'] = $month;

		$this->db->select(array('deduct_id','deduct_detail','deduct_code','deduct_format','deduct_type'));
		$this->db->from('coop_deduct');
		$this->db->where("can_not_add_other != '1'");
		$this->db->order_by('deduct_seq ASC');
		$row = $this->db->get()->result_array();
		$arr_data['coop_deduct'] = $row;

		$this->libraries->template('finance_process/finance_month_other',$arr_data);
	}

	public function process_return() {
		$this->libraries->template('finance_process/process_return');
	}

	public function process_return_edit() {
		$this->libraries->template('finance_process/process_return_edit');
	}

	public function process_return_excel() {
		$arr_data = [];
		$section = [
			'total' => 'ทั้งหมด',
			'return' => 'คืนเงินแล้ว',
			'remain' => 'ค้างโอน',
			'surcharge' => 'เก็บเพิ่ม'
		];
		$return_type = 3;
		$date_s = $this->center_function->ConvertToSQLDate($_GET["ds"]);
		$date_e = $this->center_function->ConvertToSQLDate($_GET["de"]);
		$my = sprintf('%s_%s', explode('-', $date_s)[1], explode('-', $date_s)[0]);
		if( $_GET['type'] == 1 ) {
			$arr_data['title'] = sprintf('คืนเงินผ่านรายการเรียกเก็บ %s %s', $section[$_GET['sec']], $my);
			$filter_type = ' AND tb1.finance_month_profile_id IS NOT NULL ';
			$return_type = $_GET['type'];
		} elseif( $_GET['type'] == 2 ) {
			$arr_data['title'] = sprintf('คืนเงินหักกลบ %s %s', $section[$_GET['sec']], $my);
			$filter_type = ' AND tb1.finance_month_profile_id IS NULL ';
			$return_type = $_GET['type'];
		} elseif( $_GET['type'] == 3 ) {
			$arr_data['title'] = sprintf('คืนเงิน ฉATM %s %s', $section[$_GET['sec']], $my);
			$return_type = $_GET['type'];
		} elseif( $_GET['type'] == 4 ) {
			$arr_data['title'] = sprintf('คืนเงิน ATM หลังผ่านรายการ %s %s', $section[$_GET['sec']], $my);
			$return_type = $_GET['type'];
		} elseif( $_GET['type'] == 5 ) {
			$arr_data['title'] = sprintf('คืนเงินลาออก %s %s', $section[$_GET['sec']], $my);
			$return_type = $_GET['type'];
		}

		if( in_array($return_type, [1]) ) {
			/*
			$filter_sec = '';
			if( $_GET['sec'] == 'return' ) {
				$filter_sec = ' AND tb6.ret_id IS NOT NULL ';
			} elseif( $_GET['sec'] == 'remain' ) {
				$filter_sec = ' AND tb6.ret_id IS NULL ';
			}

			$sql = "SELECT *
							FROM (
							SELECT DISTINCT tb2.member_id, tb2.receipt_id, tb2.loan_id,
							SUM(tb2.principal_payment) principal_payment, SUM(tb2.interest) interest
							, SUM(tb2.total_amount) total_amount
							, tb2.payment_date
							, DAYOFYEAR(DATE_FORMAT(NOW(), '%Y-12-31')) num_day_of_year
							, DATEDIFF(LAST_DAY(tb2.payment_date), DATE(tb2.payment_date)) num_datediff
							, CONCAT(tb3.firstname_th, ' ', tb3.lastname_th) member_name
							, tb4.account_id
							, tb5.contract_number
							, ROUND(
									SUM(tb2.principal_payment) *
									(
										(
											SELECT interest_rate
											FROM coop_term_of_loan
											WHERE start_date <= '{$date_s}'
											AND type_id = tb5.loan_type
											ORDER BY start_date DESC
											LIMIT 1
										)
										 / 100 ) *
									CAST(
										DATEDIFF(LAST_DAY(tb2.payment_date), DATE(tb2.payment_date)) /
										DAYOFYEAR(DATE_FORMAT(NOW(), '%Y-12-31'))
									AS DOUBLE )
							) return_interest
							, tb6.ret_id
							, tb6.return_time
							, YEAR(tb2.payment_date) payment_date_year
							, MONTH(tb2.payment_date) payment_date_month
							FROM coop_receipt AS tb1
							INNER JOIN coop_finance_transaction AS tb2 ON tb1.receipt_id = tb2.receipt_id
							INNER JOIN coop_mem_apply tb3 ON tb1.member_id = tb3.member_id
							LEFT OUTER JOIN ( SELECT * FROM coop_maco_account WHERE type_id = '2' AND account_status = '0' ) tb4 ON tb1.member_id = tb4.mem_id
							INNER JOIN coop_loan tb5 ON tb2.loan_id = tb5.id
							LEFT OUTER JOIN (SELECT * FROM coop_process_return WHERE return_type = {$return_type}) tb6 ON tb2.member_id = tb6.member_id
									AND tb2.loan_id = tb6.loan_id
									AND MONTH(tb2.payment_date) = tb6.return_month
									AND YEAR(tb2.payment_date) = tb6.return_year
									AND tb2.receipt_id = tb6.receipt_id
							WHERE tb2.payment_date BETWEEN '{$date_s}' AND '{$date_e}'
									AND DATEDIFF(LAST_DAY(tb2.payment_date), DATE(tb2.payment_date)) > 0
									AND ( tb2.receipt_id NOT LIKE '%C%' OR tb2.receipt_id NOT LIKE '%c%' )
									{$filter_type}
									{$filter_sec}
							GROUP BY tb2.member_id, tb2.receipt_id, tb5.contract_number
							ORDER BY tb2.member_id
							) tmp
							WHERE return_interest > 0
							";
			$rs = $this->db->query($sql);
			$arr_data['datas'] = [];
			$index = 0;
			foreach ($rs->result_array() as $row) {
				$arr_data['datas'][$index++] = [
					'account_id' => $row['account_id'],
					'member_id' => $row['member_id'],
					'account_name' => $row['member_name'],
					'contract_number' => $row['contract_number'],
					'principal' => $row['principal_payment'],
					'interest' => $row['interest'],
					'return_interest_amount' => $row['return_interest'],
					'return_time' => empty($row['return_time']) ? 'ยังไม่ได้ทำการโอนเงิน' : sprintf('โอนเงินแล้วเมื่อ %s', $this->center_function->mydate2date($row['return_time'], true))
				];
			}
			*/
			$tmp = $this->cal_process_return_type_1($date_s, $date_e);
			$data = [ 'return' => [], 'surcharge' => [] ];
			if($tmp['return']) {
				foreach( $tmp['return'] as $key => $row) {
					if( ($row['return_principal'] + $row['return_interest']) ) {
						$data['return'][] = $row;
					}
					if( $row['surcharge'] ) {
						$data['surcharge'][] = $row;
					}
				}
			}
			if($tmp['no_return']) {
				foreach( $tmp['no_return'] as $key => $row) {
					if( ($row['return_principal'] + $row['return_interest']) ) {
						$data['return'][] = $row;
					}
					if( $row['surcharge'] ) {
						$data['surcharge'][] = $row;
					}
				}
			}
			if($tmp['surcharge']) {
				foreach( $tmp['surcharge'] as $key => $row) {
					if( ($row['return_principal'] + $row['return_interest']) ) {
						$data['return'][] = $row;
					}
					if( $row['surcharge'] ) {
						$data['surcharge'][] = $row;
					}
				}
			}

			$arr_data['datas'] = [];
			$index = 0;
			foreach ($data['return'] as $row) {

				if( $_GET['sec'] == 'return' ) {
					if($row['is_return']) {
						$arr_data['datas'][$index++] = [
							'account_id' => $row['account_id'],
							'member_id' => $row['member_id'],
							'account_name' => $row['member_name'],
							'contract_number' => $row['contract_number'],
							'principal' => $row['return_principal'],
							'interest' => $row['return_interest'],
							'return_interest_amount' => $row['return_principal'] + $row['return_interest'],
							'return_time' => empty($row['return_time']) ? 'ยังไม่ได้ทำการโอนเงิน' : sprintf('โอนเงินแล้วเมื่อ %s', $this->center_function->mydate2date($row['return_time'], true))
						];
					}
				} elseif( $_GET['sec'] == 'remain' ) {
					if( !$row['is_return'] ) {
						$arr_data['datas'][$index++] = [
							'account_id' => $row['account_id'],
							'member_id' => $row['member_id'],
							'account_name' => $row['member_name'],
							'contract_number' => $row['contract_number'],
							'principal' => $row['return_principal'],
							'interest' => $row['return_interest'],
							'return_interest_amount' => $row['return_principal'] + $row['return_interest'],
							'return_time' => empty($row['return_time']) ? 'ยังไม่ได้ทำการโอนเงิน' : sprintf('โอนเงินแล้วเมื่อ %s', $this->center_function->mydate2date($row['return_time'], true))
						];
					}
				} elseif( $_GET['sec'] == 'total' ) {
					$arr_data['datas'][$index++] = [
						'account_id' => $row['account_id'],
						'member_id' => $row['member_id'],
						'account_name' => $row['member_name'],
						'contract_number' => $row['contract_number'],
						'principal' => $row['return_principal'],
						'interest' => $row['return_interest'],
						'return_interest_amount' => $row['return_principal'] + $row['return_interest'],
						'return_time' => empty($row['return_time']) ? 'ยังไม่ได้ทำการโอนเงิน' : sprintf('โอนเงินแล้วเมื่อ %s', $this->center_function->mydate2date($row['return_time'], true))
					];
				}


			}

			foreach ($data['surcharge'] as $row) {

				if( $_GET['sec'] == 'surcharge' ) {
					if( $row['surcharge'] ) {
						$arr_data['datas'][$index++] = [
							'account_id' => $row['account_id'],
							'member_id' => $row['member_id'],
							'account_name' => $row['member_name'],
							'contract_number' => $row['contract_number'],
							'principal' => 0,
							'interest' => $row['surcharge'],
							'return_interest_amount' => $row['surcharge'],
							'return_time' => empty($row['return_time']) ? 'ยังไม่ได้ทำการโอนเงิน' : sprintf('โอนเงินแล้วเมื่อ %s', $this->center_function->mydate2date($row['return_time'], true))
						];
					}
				}


			}




		} elseif($return_type == 2) {
			$tmp = $this->cal_process_return_type_2($date_s, $date_e);
			if( in_array($_GET['sec'], ['return', 'remain', 'total']) ) {
				foreach( $tmp['return'] as $key => $row ) {
					$sql = "SELECT tb1.contract_number, CONCAT(tb2.firstname_th, ' ', tb2.lastname_th) member_name
									FROM coop_loan tb1
									INNER JOIN coop_mem_apply tb2 ON tb1.member_id = tb2.member_id
									WHERE tb1.id = '{$row['loan_id']}'";
					$rs_return = $this->db->query($sql);
					$row_return = $rs_return->row_array();

					if( $_GET['sec'] == 'return' ) {
						if( $row['is_return'] ) {
							$arr_data['datas'][$index++] = [
								'account_id' => $row['account_id'],
								'member_id' => $row['member_id'],
								'account_name' => $row_return['member_name'],
								'loan_id' => $row['loan_id'],
								'contract_number' => $row_return['contract_number'],
								'principal' => 0,
								'interest' => $row['return_amount'],
								'receipt_id' => $row['receipt_id'],
								'return_interest_amount' => $row['return_amount'],
								'return_time' => empty($row['ret_id']) ? 'ยังไม่ได้ทำการโอนเงิน' : 'คืนเงินแล้วเมื่อ<br />'.$this->center_function->mydate2date($row['return_time'], true)
							];
						}
					} elseif( $_GET['sec'] == 'remain') {
						if( !$row['is_return'] ) {
							$arr_data['datas'][$index++] = [
								'account_id' => $row['account_id'],
								'member_id' => $row['member_id'],
								'account_name' => $row_return['member_name'],
								'loan_id' => $row['loan_id'],
								'contract_number' => $row_return['contract_number'],
								'principal' => 0,
								'interest' => $row['return_amount'],
								'receipt_id' => $row['receipt_id'],
								'return_interest_amount' => $row['return_amount'],
								'return_time' => empty($row['ret_id']) ? 'ยังไม่ได้ทำการโอนเงิน' : 'คืนเงินแล้วเมื่อ<br />'.$this->center_function->mydate2date($row['return_time'], true)
							];
						}
					} else {
						$arr_data['datas'][$index++] = [
							'account_id' => '',
							'member_id' => $row['member_id'],
							'account_name' => $row_return['member_name'],
							'loan_id' => $row['loan_id'],
							'contract_number' => $row_return['contract_number'],
							'principal' => 0,
							'interest' => $row['return_amount'],
							'receipt_id' => $row['receipt_id'],
							'return_interest_amount' => $row['return_amount'],
							'return_time' => empty($row['ret_id']) ? 'ยังไม่ได้ทำการโอนเงิน' : 'คืนเงินแล้วเมื่อ<br />'.$this->center_function->mydate2date($row['return_time'], true)
						];
					}
				}
			} else {
				foreach( $tmp['no_return'] as $key => $row ) {
					$sql = "SELECT tb1.contract_number, CONCAT(tb2.firstname_th, ' ', tb2.lastname_th) member_name
									FROM coop_loan tb1
									INNER JOIN coop_mem_apply tb2 ON tb1.member_id = tb2.member_id
									WHERE tb1.id = '{$row['loan_id']}'";
					$rs_return = $this->db->query($sql);
					$row_return = $rs_return->row_array();
					$arr_data['datas'][$index++] = [
						'account_id' => $row['account_id'],
						'member_id' => $row['member_id'],
						'account_name' => $row_return['member_name'],
						'loan_id' => $row['loan_id'],
						'contract_number' => $row_return['contract_number'],
						'principal' => 0,
						'interest' => $row['return_amount'],
						'receipt_id' => $row['receipt_id'],
						'return_interest_amount' => $row['return_amount'],
						'return_time' => empty($row['ret_id']) ? 'ยังไม่ได้ทำการโอนเงิน' : 'คืนเงินแล้วเมื่อ<br />'.$this->center_function->mydate2date($row['return_time'], true)
					];
				}

			}
		} elseif($return_type == 3) {
			$arr_data['datas'] = [];
			$filter_sec = '';
			if( $_GET['sec'] == 'return' ) {
				$filter_sec = ' AND tb6.ret_id IS NOT NULL ';
			} elseif( $_GET['sec'] == 'remain' ) {
				$filter_sec = ' AND tb6.ret_id IS NULL ';
			}
			$sql = "SELECT
					t2.receipt_id,
				t2.member_id,
				t2.loan_atm_id,
				t2.principal_payment,
				t2.interest,
				t2.total_amount,
				t2.loan_amount_balance,
				t1.receipt_datetime
				, t4.payment_date
				, DATEDIFF( DATE(t1.receipt_datetime), t4.payment_date ) _payment_diff_
				, tb6.ret_id
				, tb6.return_time
				, CONCAT(tb7.firstname_th, ' ', tb7.lastname_th) member_name
				, tb8.account_id
				FROM coop_receipt AS t1
				INNER JOIN coop_finance_transaction AS t2 ON t1.receipt_id = t2.receipt_id
				LEFT JOIN coop_finance_month_profile AS t3 ON t1.month_receipt = t3.profile_month AND t1.year_receipt = t3.profile_year
				LEFT OUTER JOIN (

					SELECT
					tb1.loan_atm_id,
					tb1.transaction_datetime,
					DATE(tb1.transaction_datetime) AS payment_date,
					tb1.receipt_id,
					tb2.loan_description,
					SUM( IF ( tb2.loan_amount <> '', tb2.loan_amount, tb3.principal_payment ) ) AS principal,
					SUM(tb3.interest) AS interest,
					SUM(tb3.total_amount) AS total_amount,
					IF (
					tb2.loan_description != '',
					tb2.loan_description,

					IF (
					tb4.finance_month_profile_id != '',
					'ชำระเงินรายเดือน',
					'ชำระเงินอื่นๆ'
					)
					) AS data_text
					, tb1.loan_amount_balance
					FROM
					coop_loan_atm_transaction AS tb1
					LEFT JOIN coop_loan_atm_detail AS tb2 ON tb1.loan_atm_id = tb2.loan_atm_id
					AND tb1.transaction_datetime = tb2.loan_date
					LEFT JOIN coop_finance_transaction AS tb3 ON tb1.receipt_id = tb3.receipt_id
					AND tb1.loan_atm_id = tb3.loan_atm_id
					LEFT JOIN coop_receipt AS tb4 ON tb3.receipt_id = tb4.receipt_id
					LEFT JOIN coop_receipt AS t6 ON tb1.receipt_id = t6.receipt_id
					WHERE DATE(tb1.transaction_datetime) BETWEEN '{$date_s}' AND '{$date_e}'
					AND tb4.finance_month_profile_id <> ''
					GROUP BY tb1.loan_atm_id, tb1.transaction_datetime
					ORDER BY tb1.loan_atm_id

				) t4 ON t2.loan_atm_id = t4.loan_atm_id

				LEFT OUTER JOIN (SELECT * FROM coop_process_return WHERE return_type = 3) tb6 ON t2.member_id = tb6.member_id
						AND t2.loan_atm_id = tb6.loan_atm_id
						AND MONTH(t4.payment_date) = tb6.return_month
						AND YEAR(t4.payment_date) = tb6.return_year

				INNER JOIN coop_mem_apply tb7 ON t1.member_id = tb7.member_id
				LEFT OUTER JOIN ( SELECT * FROM coop_maco_account WHERE type_id = '2' AND account_status = '0' ) tb8 ON t2.member_id = tb8.mem_id

				WHERE t1.finance_month_profile_id IS NULL
				AND t2.loan_atm_id <> ''
				AND DATE(t1.receipt_datetime) BETWEEN '{$date_s}' AND '{$date_e}'
				AND t2.loan_amount_balance = 0
				AND t4.payment_date Is NOT NULL
				AND DATEDIFF( DATE(t1.receipt_datetime), t4.payment_date ) <= 0
				{$filter_sec}
				ORDER BY t2.member_id,t2.loan_atm_id";

		$rs = $this->db->query($sql);
		foreach ($rs->result_array() as $row) {
			$return_func = $this->cal_return_atm($row['member_id'], $row['loan_atm_id'], explode('-', $date_s)[0], explode('-', $date_s)[1]);
			$arr_data['datas'][$index++] = [
				'account_id' => $row['account_id'],
				'member_id' => $row['member_id'],
				'account_name' => $row['member_name'],
				'contract_number' => $return_func['contract_number'],
				'principal' => $row['principal_payment'],
				'interest' => $row['interest'],
				'return_interest_amount' => $return_func['return_interest'],
				'return_time' => empty($row['return_time']) ? 'ยังไม่ได้ทำการโอนเงิน' : sprintf('โอนเงินแล้วเมื่อ %s', $this->center_function->mydate2date($row['return_time'], true))
			];
		}
			/*
		foreach ($rs->result_array() as $row) {

		if( $return_func['return_interest'] > 0) {
				$json['data'][] = [
					'return_func' => $return_func,
					'member_id' => $row['member_id'],
					'member_name' => $row['member_name'],
					'loan_id' => $row['loan_atm_id'],
					'contract_number' => $return_func['contract_number'],
					'interest_rate' => $return_func['interest_rate'],
					'receipt_id' => $row['receipt_id'],
					'return_interest' => $return_func['return_interest'],
					'return_status' => empty($row['ret_id']) ? 'ยังไม่ได้โอนเงิน' : 'คืนเงินแล้วเมื่อ<br />'.$this->center_function->mydate2date($row['return_time'], true)
				];
			}
		}
		*/
		} elseif($return_type == 4) {
			$arr_data['datas'] = [];
			$filter_sec = '';
			if( $_GET['sec'] == 'total' ) {
				//ทั้งหมด
				$filter_sec = '';
			} elseif( $_GET['sec'] == 'return' ) {
				//คืนเงินแล้ว
				$filter_sec =" AND return_status = '1'";
			} elseif( $_GET['sec'] == 'remain' ) {
				//ค้างโอน
				$filter_sec = " AND return_status = '0'";
			}
			
			//
			$day = substr($date_s, 8 , 10);
			$sql = "SELECT
						coop_mem_apply.member_id,
						CONCAT(
							firstname_th,
							' ',
							lastname_th
						) AS member_name,
						coop_loan_atm.loan_atm_id,
						coop_loan_atm.contract_number,
						return_principal,
						return_interest,
						return_status,
						interest_rate,
						tb8.account_id
					FROM
						coop_process_return_store
					JOIN coop_mem_apply ON coop_mem_apply.member_id = coop_process_return_store.member_id
					JOIN coop_loan_atm ON coop_loan_atm.loan_atm_id = coop_process_return_store.loan_atm_id
					LEFT OUTER JOIN ( SELECT * FROM coop_maco_account WHERE type_id = '2' AND account_status = '0' ) tb8 ON coop_mem_apply.member_id = tb8.mem_id
					WHERE
						STR_TO_DATE(concat(return_year,'-',return_month,'-','{$day}'), '%Y-%m-%d') BETWEEN '{$date_s}' and '{$date_s}' {$filter_sec}";
			$rs = $this->db->query($sql);
			//echo $this->db->last_query(); exit;
			foreach ($rs->result_array() as $row) {
				$arr_data['datas'][$index++] = [
					'account_id' => $row['account_id'],
					'member_id' => $row['member_id'],
					'account_name' => $row['member_name'],
					'contract_number' => $row['contract_number'],
					'principal' => $row['return_principal'],
					'interest' => $row['return_interest'],
					'return_interest_amount' => $row['return_interest'],
					//'return_time' => empty($row['return_status']) ? 'ยังไม่ได้ทำการโอนเงิน' : sprintf('โอนเงินแล้วเมื่อ %s', $this->center_function->mydate2date($row['return_time'], true))
					'return_time' => empty($row['return_status']) ? 'ยังไม่ได้ทำการโอนเงิน' : 'โอนเงินแล้ว'
				];
			}
		} elseif($return_type == 5) {
			$arr_data['datas'] = [];
			$arr_data['col'] = ["หมายเหตุ"];
			$filter_sec = '';

			$sql = "SELECT t5.* FROM (
				SELECT t1.member_id,t4.approve_date as resign_date,t2.receipt_id,t3.finance_month_profile_id,firstname_th, lastname_th
				FROM coop_mem_apply as t1
				LEFT OUTER JOIN coop_finance_transaction as t2 ON t1.member_id = t2.member_id
				LEFT OUTER JOIN coop_receipt as t3 ON t2.receipt_id = t3.receipt_id
				INNER JOIN coop_mem_req_resign as t4 ON t1.member_id = t4.member_id AND t2.payment_date >= t4.approve_date
				WHERE t1.mem_type in (2,5,6) AND t2.payment_date BETWEEN '".$date_s."' AND '".$date_e."' AND t2.account_list_id IN (15,16,30,31)
				GROUP BY t1.member_id
			) AS t5 WHERE t5.finance_month_profile_id IS NOT NULL
			";
			$row_5 = $this->db->query($sql)->result_array();	
			
			$tmp["remain_count"] = array();
			$tmp["return_count"] = array();
			foreach($row_5 as $member) {
				$sql = "SELECT * FROM (
							SELECT share_id,share_collect_value
							FROM coop_mem_share
							WHERE member_id = '".$member["member_id"]."'
							ORDER BY share_date DESC LIMIT 1
						) AS t1 WHERE t1.share_collect_value > 0
					";
				$share = $this->db->query($sql)->row();	
				//echo $this->db->last_query(); echo ';';
				
				$sql = "SELECT id FROM coop_loan WHERE member_id = '".$member["member_id"]."' AND loan_amount_balance < 0";
				$loan = $this->db->query($sql)->row();	
				//echo $this->db->last_query(); echo ';';
				
				$sql = "SELECT loan_atm_id FROM coop_loan_atm WHERE member_id = '".$member["member_id"]."' AND (total_amount_approve-total_amount_balance) <0";
				$loan_atm = $this->db->query($sql)->row();	
				//echo $this->db->last_query(); echo ';';
				
				//Account not work better re-coding
				$sql_account = "SELECT t2.account_id,t2.mem_id,t2.transaction_balance FROM (
									SELECT
										t1.account_id,
										t1.mem_id,
									(SELECT transaction_balance FROM coop_account_transaction WHERE account_id = t1.account_id ORDER BY transaction_time DESC,transaction_id DESC LIMIT 1) AS transaction_balance
									FROM
										coop_maco_account AS t1
									WHERE
										t1.type_id = '2'
									AND t1.account_status = '1'
									AND t1.mem_id = '".$member["member_id"]."'
								) AS t2 WHERE t2.transaction_balance > 0 
					";
				$account = $this->db->query($sql_account)->row();	
				//echo $this->db->last_query(); echo ';';
				// exit;

				if(!empty($share) || !empty($loan) || !empty($loan_atm) || !empty($account)) {
					array_push($tmp['remain_count'], array(
						'account_id' => "",
						'member_id' => $member['member_id'],
						'account_name' => $member['firstname_th']." ".$member['lastname_th'],
						'contract_number' => "-",
						'principal' => "",
						'interest' => "",
						'return_interest_amount' => "",
						'return_time' => "ยังไม่ได้ทำการโอนเงิน",
					));
				}else{
					$sql = "SELECT * FROM coop_process_return_resign WHERE `member_id` LIKE '".$member["member_id"]."'";
					$return_resign = $this->db->query($sql);
					foreach ($return_resign->result_array() as $return_resign_row) {
						$contract_number = "-";
						$remark = "";
						if($return_resign_row['loan_id']){
							$contract_number = $this->db->get_where("coop_loan", array(
								"id" => $return_resign_row['loan_id']
							))->result_array()[0]['contract_number'];
							$remark = "คืนเงินกู้";
						}else if($return_resign_row['loan_atm_id']){
							$contract_number = $this->db->get_where("coop_atm_loan", array(
								"loan_atm_id" => $return_resign_row['loan_atm_id']
							))->result_array()[0]['contract_number'];
							$remark = "คืนเงินกู้";
						}

						if($remark==""){
							if($return_resign_row['account_id']!=""){
								$remark = "คืนเงินฝาก";
							}else{
								$remark = "คืนหุ้น";
							}
						}


						array_push($tmp['return_count'], array(
							'account_id' => $return_resign_row['account_id'],
							'member_id' => $member['member_id'],
							'account_name' => $member['firstname_th']." ".$member['lastname_th'],
							'contract_number' => $contract_number,
							'principal' => $return_resign_row['return_principal'],
							'interest' => $return_resign_row['return_interest'],
							'return_interest_amount' => $return_resign_row['return_amount'],
							'return_time' => "โอนเงินแล้ว",
							"remark" => $remark
						));
	
					}
				}

			}

			$index = 0;
			if($_GET['sec']=="total"){
				foreach ($tmp['return_count'] as $key => $value) {
					$arr_data['datas'][$index++] = [
						'account_id' => $value['account_id'],
						'member_id' => $value['member_id'],
						'account_name' => $value['account_name'],
						'contract_number' => $value['contract_number'],
						'principal' => $value['principal'],
						'interest' => $value['interest'],
						'return_interest_amount' => $value['return_interest_amount'],
						'return_time' => $value['return_time'],
						"remark" => $value['remark']
					];
				}
				foreach ($tmp['remain_count'] as $key => $value) {
					$arr_data['datas'][$index++] = [
						'account_id' => $value['account_id'],
						'member_id' => $value['member_id'],
						'account_name' => $value['account_name'],
						'contract_number' => $value['contract_number'],
						'principal' => $value['principal'],
						'interest' => $value['interest'],
						'return_interest_amount' => $value['return_interest_amount'],
						'return_time' => $value['return_time'],
						"remark" => $value['remark']
					];
				}
			}else if($_GET['sec']=="return"){
				foreach ($tmp['return_count'] as $key => $value) {
					$arr_data['datas'][$index++] = [
						'account_id' => $value['account_id'],
						'member_id' => $value['member_id'],
						'account_name' => $value['account_name'],
						'contract_number' => $value['contract_number'],
						'principal' => $value['principal'],
						'interest' => $value['interest'],
						'return_interest_amount' => $value['return_interest_amount'],
						'return_time' => $value['return_time'],
						"remark" => $value['remark']
					];
				}
			}else if($_GET['sec']=="remain"){
				foreach ($tmp['remain_count'] as $key => $value) {
					$arr_data['datas'][$index++] = [
						'account_id' => $value['account_id'],
						'member_id' => $value['member_id'],
						'account_name' => $value['account_name'],
						'contract_number' => $value['contract_number'],
						'principal' => $value['principal'],
						'interest' => $value['interest'],
						'return_interest_amount' => $value['return_interest_amount'],
						'return_time' => $value['return_time'],
						"remark" => $value['remark']
					];
				}
			}


			


		}

		$this->load->view('finance_process/process_return_excel', $arr_data);
	}

	function cal_return_atm($member_id, $param_loan_atm_id, $year, $month) {

		$sql = "SELECT interest_rate
						FROM coop_loan_atm_setting_template
						WHERE start_date <= '{$year}-{$month}-01'
						ORDER BY start_date DESC
						LIMIT 1	";
		$rs_interest_rate = $this->db->query($sql);
		$row_interest_rate = $rs_interest_rate->result_array()[0];
		$interest_rate = $row_interest_rate['interest_rate'];

		$sql = "SELECT loan_atm_id, contract_number
						FROM coop_loan_atm
						WHERE member_id = '{$member_id}'
							AND loan_atm_id = '{$param_loan_atm_id}'
							";
		$rs_loan_atm = $this->db->query($sql);
		$total_day_of_month = date_format(date_create("{$year}-{$month}-01"), 't') ;
		foreach ($rs_loan_atm->result_array() as $row_loan_atm) {
			$loan_atm_id = $row_loan_atm['loan_atm_id'];
			$contract_number = $row_loan_atm['contract_number'];
			$sql = "SELECT *
							FROM (

							SELECT CASE WHEN t4.finance_month_profile_id IS NULL THEN 2 ELSE 3 END payment_type
							, IF (
						t2.loan_description != '',
						`t2`.`loan_description`,

					IF (
						t4.finance_month_profile_id != '',
						'ชำระเงินรายเดือน',
						'ชำระเงินอื่นๆ'
					)
					) loan_description
							, SUM(							IF (
								t2.loan_amount <> '',
								`t2`.`loan_amount`,
								t3.principal_payment
							)) loan_amount
							, SUM(t3.interest) interest
							, IF (
								! ISNULL(
								(
								SELECT
									ret_id
								FROM
									coop_process_return
								WHERE
									coop_process_return.return_month = t6.month_receipt AND coop_process_return.return_year = (t6.year_receipt-543) AND coop_process_return.loan_atm_id = t1.loan_atm_id
								LIMIT 1
								)
								),
								SUM(

								IF (
								t2.loan_amount <> '',
								`t2`.`loan_amount`,
								t3.principal_payment
								)
								) * - 1,
								`t1`.`loan_amount_balance`
								) loan_amount_balance
							, DATE(t1.transaction_datetime) loan_date
							FROM
								`coop_loan_atm_transaction` AS `t1`
							LEFT JOIN `coop_loan_atm_detail` AS `t2` ON `t1`.`loan_atm_id` = `t2`.`loan_atm_id`
							AND `t1`.`transaction_datetime` = `t2`.`loan_date`
							LEFT JOIN `coop_finance_transaction` AS `t3` ON `t1`.`receipt_id` = `t3`.`receipt_id`
							AND `t1`.`loan_atm_id` = `t3`.`loan_atm_id`
							LEFT JOIN `coop_receipt` AS `t4` ON `t3`.`receipt_id` = `t4`.`receipt_id`
							LEFT JOIN coop_receipt AS t6 ON t1.receipt_id = t6.receipt_id
							WHERE
								`t1`.`loan_atm_id` = '{$loan_atm_id}'
							GROUP BY
								`t1`.`transaction_datetime`
							) tb
							WHERE loan_date BETWEEN DATE_FORMAT(DATE_ADD('{$year}-{$month}-01', INTERVAL -1 DAY), '%Y-%m-01') AND '{$year}-{$month}-{$total_day_of_month}'
							ORDER BY loan_date ASC";
			$rs = $this->db->query($sql);
			$date_s = null;
			$date_e = null;

			$total_day_of_year = date_format(date_create("{$year}-{$month}-31"), 'z') + 1;
			$is_end = false;
			$loan_amount_balance = 0;
			$interest_acc = [];
			$interest = 0;
			$index = 0;
			$is_close = 0;
			$return = 0;
			$return_arr = [];

			$sum = [];

			foreach ($rs->result_array() as $row) {
				if( !$is_end ) {
					$return = 0;
					if( !$date_s && !$date_e ) {
						$num_of_days = '';
					} elseif( $date_s != null ) {
						$date_e = date_create(explode(' ', $row['loan_date'])[0]);
						$num_of_days = date_diff($date_s, $date_e)->format('%a');
						$date_s = date_create(explode(' ', $row['loan_date'])[0]);
					}

					if( (int)$num_of_days > 0 ) {
						$interest_acc[$index] = $loan_amount_balance * ($interest_rate / 100) * ($num_of_days/$total_day_of_year);
					} else {
						$interest_acc[$index] = 0;
					}

					if( $row['payment_type'] == 2 ) { // กรณีมีการชำระอื่น ๆ เข้ามา
						//  && $row['loan_amount_balance'] ยังไม่ปิด
						//  && !$row['loan_amount_balance'] ปิด

						//echo "<div>{$row['interest']} ::: ".(array_sum($interest_acc))."</div>";
						$interest_acc[] = $row['interest'] ;
						//$return = abs(round($row['interest'] - array_sum($interest_acc) + $row['interest'], 2));
						$return = 0;
						$return_arr[explode(' ', $row['loan_date'])[0]] = $return;
						$interest_acc = [];
					}

					if( $row['loan_amount_balance'] == 0 || $is_close ) {
						$interest_acc = [];
						$is_close = 1;
					}
					if( $is_close && $row['payment_type'] == 3 ) { // กรณีมีปิด และมีการเรียกเก็บรายเดือน
						$interest_acc[] = $row['loan_amount'] + $row['interest'] ;
						$return = abs(round(array_sum($interest_acc), 2));
						$return_arr[explode(' ', $row['loan_date'])[0]] = $return;
					}

					if( !$is_close && $row['payment_type'] == 3 && $num_of_days ) { // กรณีมียังไม่ปิด และมีการเรียกเก็บรายเดือน
						$return = abs(round($row['interest'] - array_sum($interest_acc), 2));
						$return_arr[explode(' ', $row['loan_date'])[0]] = $return;
					}

					$sum['return'] += $return;
					if( $row['payment_type'] == 3 ) {
						$date_s = date_create(explode(' ', $row['loan_date'])[0]);
					}
					$index++;
					$loan_amount_balance = $row['loan_amount_balance'];
				}

				if( date_format(date_create(explode(' ', $row['loan_date'])[0]), 'Y-m') == "{$year}-{$month}" && $row['payment_type'] == 3 ) {
					if( !$is_close ) $interest = $row['interest'];
				}

			}
		}




		return [ 'interest_rate' => $interest_rate, 'contract_number' => $contract_number, 'return_interest' => $sum['return'], 'member_id' => $member_id, 'param_loan_atm_id' => $param_loan_atm_id ];
	}

	function keypress_search_member(){
		$data = array();
		$deduct_id = $_POST['deduct_id'];
		//$member_id = sprintf("%06d",$_POST['member_id']);
		$member_id = $this->center_function->complete_member_id(@$_POST['member_id']);
		$this->db->select(array('t1.*','t2.prename_short'));
		$this->db->from('coop_mem_apply as t1');
		$this->db->join('coop_prename as t2','t1.prename_id = t2.prename_id','left');
		$this->db->where("t1.member_id = '".$member_id."'");
		$row = $this->db->get()->result_array();
		$row_member = $row[0];
		if(!empty($row_member)){
			$data['row_member'] = $row_member;

			$this->db->select(array('*'));
			$this->db->from('coop_deduct');
			$this->db->where("deduct_id = '".$deduct_id."'");
			$row_deduct = $this->db->get()->result_array();
			$row_deduct = $row_deduct[0];
			$ref_arr = array();
			if($row_deduct['deduct_type'] == '1'){
				if($row_deduct['deduct_code']=='ATM'){
					$this->db->select(array('loan_atm_id','contract_number'));
					$this->db->from('coop_loan_atm');
					$this->db->where("member_id = '".$member_id."' AND loan_atm_status = '1'");
					$row_data = $this->db->get()->result_array();
					if(!empty($row_data)){
						$i=0;
						foreach($row_data as $key => $value){
							$ref_arr[$i]['value'] = $value['loan_atm_id'];
							$ref_arr[$i]['text'] = $value['contract_number'];
							$i++;
						}
					}
				}else if($row_deduct['deduct_code']=='LOAN'){
					$this->db->select(array('ref_id'));
					$this->db->from('coop_deduct_detail');
					$this->db->where("deduct_id = '".$row_deduct['deduct_id']."'");
					$row_deduct_detail = $this->db->get()->result_array();
					$i=0;
					foreach($row_deduct_detail as $key_deduct_detail => $value_deduct_detail){
						$this->db->select(array('id','contract_number'));
						$this->db->from('coop_loan');
						$this->db->where("
							member_id = '".$member_id."'
							AND loan_type = '".$value_deduct_detail['ref_id']."'
							AND loan_status = '1'
						");
						$row_data = $this->db->get()->result_array();
						if(!empty($row_data)){
							foreach($row_data as $key => $value){
								$ref_arr[$i]['value'] = $value['id'];
								$ref_arr[$i]['text'] = $value['contract_number'];
								$i++;
							}
						}
					}
				}
			}else if($row_deduct['deduct_type'] == '2'){
				$this->db->select(array('account_id','account_name'));
				$this->db->from('coop_maco_account');
				$this->db->where("mem_id = '".$member_id."' AND account_status = '0'");
				$row_data = $this->db->get()->result_array();
				if(!empty($row_data)){
					$i=0;
					foreach($row_data as $key => $value){
						$ref_arr[$i]['value'] = $value['account_id'];
						$ref_arr[$i]['text'] = $value['account_id'].":".$value['account_name'];
						$i++;
					}
				}
			}
			$ref_data = '';
			if(!empty($ref_arr)){
				$ref_data = '<select class="form-control" id="ref_data_'.$_POST['id'].'" name="data[list_data]['.$_POST['id'].'][ref_data]">';
				$ref_data .= '<option value="">เลือกข้อมูล</option>';
				foreach($ref_arr as $key => $value){
					$ref_data .= '<option value="'.$value['value'].'">'.$value['text'].'</option>';
				}
				$ref_data .= "</select>";
			}

			$data['ref_data'] = $ref_data;
			echo json_encode($data);
		}else{
			echo "error";
		}
	}

	function finance_month_other_save(){
		//echo"<pre>";print_r($_POST);exit;
		$this->db->select('profile_id');
		$this->db->from('coop_finance_month_profile');
		$this->db->where("profile_month = '".(int)$_POST['month']."' AND profile_year = '".$_POST['year']."'");
		$row = $this->db->get()->result_array();
		$row_profile = @$row[0];

		if(@$row_profile['profile_id'] == ''){
			$data_insert = array();
			$data_insert['profile_month'] = (int)$_POST['month'];
			$data_insert['profile_year'] = $_POST['year'];
			$this->db->insert('coop_finance_month_profile', $data_insert);

			$profile_id = $this->db->insert_id();
		}else{
			$profile_id = $row_profile['profile_id'];
		}

		$this->db->select(array('*'));
		$this->db->from('coop_deduct');
		$this->db->where("deduct_id = '".$_POST['deduct_id']."'");
		$row = $this->db->get()->result_array();
		$row_deduct = @$row[0];

		foreach($_POST['data']['list_data'] as $key => $value){
			if($value['member_name']==''){
				continue;
			}
			if(isset($value['ref_data'])){
				if($value['ref_data'] == ''){
					continue;
				}
			}
			if($row_deduct['deduct_format'] == '1'){
				$pay_type = 'interest';
			}else{
				$pay_type = 'principal';
			}

			$data_insert = array();
			$data_insert['profile_id'] = $profile_id;
			$data_insert['member_id'] = $value['member_id'];
			$data_insert['deduct_code'] = $row_deduct['deduct_code'];
			$data_insert['deduct_id'] = $_POST['deduct_id'];
			$data_insert['pay_amount'] = str_replace(',','',$value['pay_amount']);
			$data_insert['real_pay_amount'] = str_replace(',','',$value['pay_amount']);
			$data_insert['pay_type'] = $pay_type;
			$data_insert['run_status'] = '0';
			$data_insert['finance_month_type'] = '1';
			if($row_deduct['deduct_code'] == 'LOAN' || $row_deduct['deduct_code'] == 'GUARANTEE'){
				$data_insert['loan_id'] = $value['ref_data'];
			}else if($row_deduct['deduct_code'] == 'ATM'){
				$data_insert['loan_atm_id'] = $value['ref_data'];
			}else if($row_deduct['deduct_code']=='DEPOSIT'){
				$data_insert['deposit_account_id'] = $value['ref_data'];
			}
			$this->db->insert('coop_finance_month_detail', $data_insert);
		}
		$this->center_function->toast('บันทึกข้อมูลเรียบร้อยแล้ว');
		echo "<script>document.location.href = '".base_url(PROJECTPATH.'/finance_process/finance_month_other')."'</script>";
	}

	function delete_data(){
		$this->db->where('run_id',$_POST['run_id']);
		$this->db->delete('coop_finance_month_detail');
		return 'success';
		exit;
	}

	function get_detail_data(){
		$arr_data = array();
		$this->db->select(array(
			't1.*',
			't3.deduct_detail'
		));
		$this->db->from('coop_finance_month_detail as t1');
		$this->db->join('coop_finance_month_profile as t2','t1.profile_id = t2.profile_id','inner');
		$this->db->join('coop_deduct as t3','t1.deduct_id = t3.deduct_id','inner');
		$this->db->where("
			t1.member_id = '".$_POST['member_id']."'
			AND t1.finance_month_type = '1'
			AND t2.profile_month = '".$_POST['month']."'
			AND t2.profile_year = '".$_POST['year']."'
		");
		$this->db->order_by('run_id ASC');
		$row = $this->db->get()->result_array();
		echo $this->db->last_query();
		foreach($row as $key => $value){
			if($value['loan_id']!=''){
				$this->db->select(array('contract_number'));
				$this->db->from('coop_loan');
				$this->db->where("id = '".$value['loan_id']."'");
				$row_detail = $this->db->get()->result_array();
				$row[$key]['show_text'] = $value['deduct_detail']." เลขที่ ".$row_detail[0]['contract_number'];
			}else if($value['loan_atm_id']!='' && $value['loan_atm_id']!='0'){
				$this->db->select(array('contract_number'));
				$this->db->from('coop_loan_atm');
				$this->db->where("loan_atm_id = '".$value['loan_atm_id']."'");
				$row_detail = $this->db->get()->result_array();
				$row[$key]['show_text'] = $value['deduct_detail']." เลขที่ ".$row_detail[0]['contract_number'];
			}else if($value['deposit_account_id']!=''){
				$row[$key]['show_text'] = $value['deduct_detail']." เลขที่ ".$value['deposit_account_id'];
			}else{
				$row[$key]['show_text'] = $value['deduct_detail'];
			}
		}
		//echo"<pre>";print_r($row);exit;
		$arr_data['row_month_detail_data'] = @$row;
		$this->load->view('finance_process/get_detail_data',$arr_data);
	}

	function get_finance_month_data(){
		$arr_data = array();
		$this->db->select(array(
			't1.*',
			't3.firstname_th',
			't3.lastname_th',
			't4.prename_short',
			't5.deduct_code'
		));
		$this->db->from('coop_finance_month_detail as t1');
		$this->db->join('coop_finance_month_profile as t2','t1.profile_id = t2.profile_id','inner');
		$this->db->join('coop_mem_apply as t3','t1.member_id = t3.member_id','inner');
		$this->db->join('coop_prename as t4','t3.prename_id = t4.prename_id','left');
		$this->db->join('coop_deduct as t5','t1.deduct_id = t5.deduct_id','left');
		$this->db->where("
			t2.profile_month = '".$_POST['month']."'
			AND t2.profile_year = '".$_POST['year']."'
			AND t1.deduct_id = '".$_POST['deduct_id']."'
			AND t1.finance_month_type = '1'
		");
		$this->db->order_by('t1.run_id ASC');
		$row = $this->db->get()->result_array();
		//echo $this->db->last_query();
		foreach($row as $key => $value){
			if($value['deduct_code'] == 'LOAN' || $value['deduct_code'] == 'GUARANTEE'){
				$this->db->select('contract_number');
				$this->db->from('coop_loan');
				$this->db->where("id = '".$value['loan_id']."'");
				$row_detail = $this->db->get()->result_array();
				$row[$key]['ref_data'] = 'เลขที่สัญญา '.$row_detail[0]['contract_number'];
			}else if($value['deduct_code'] == 'ATM'){
				$this->db->select('contract_number');
				$this->db->from('coop_loan_atm');
				$this->db->where("loan_atm_id = '".$value['loan_atm_id']."'");
				$row_detail = $this->db->get()->result_array();
				$row[$key]['ref_data'] = 'เลขที่สัญญา '.$row_detail[0]['contract_number'];
			}else if($value['deduct_code']=='DEPOSIT'){
				$row[$key]['ref_data'] = 'เลขที่บัญชี '.$row_detail[0]['deposit_account_id'];
			}
		}
		$arr_data['data'] = $row;
		$this->load->view('finance_process/get_finance_month_data',$arr_data);
	}
	 	/**************************************
	 * Func คำนวณหักกลบ ( return_type = 1) -- Start
	 *************************************/
	function cal_process_return_type_1($date_s, $date_e) {
		$year_process = explode('-', $date_s)[0];
		$month_process = explode('-', $date_s)[1];
		$days_of_month = date_format(date_create($date_s), 't');
		$days_of_year = date_format(date_create("{$year_process}-12-31"), 'z') + 1 ;

		$interest_rate = [];
		$sql = "SELECT type_id, interest_rate
						FROM coop_term_of_loan
						WHERE start_date <= '{$date_s}'
						ORDER BY type_id, start_date DESC
						";
		$rs = $this->db->query($sql);
		foreach ($rs->result_array() as $row) {
			if( !isset($interest_rate[$row['type_id']]) ) $interest_rate[$row['type_id']] = $row['interest_rate'];
		}
		unset($sql, $rs, $row);


		$data_return = [];
		$sql = "SELECT *
						FROM coop_process_return
						WHERE return_year = {$year_process}
							AND return_month = {$month_process}
							AND return_type IN (1, 5)";
		$rs = $this->db->query($sql);
		foreach ($rs->result_array() as $row) {
			$data_return["{$row['member_id']}#{$row['loan_id']}#{$row['receipt_id']}"] = $row;
		}

		$sql = "SELECT DISTINCT tb2.member_id
						, t
						, tb2.receipt_id
						, tb2.loan_id
						, SUM(tb2.principal_payment) principal_payment
						, SUM(tb2.interest) interest
						, SUM(tb2.total_amount) total_amount
						, SUM(tb2.loan_amount_balance) loan_amount_balance
						, tb2.payment_date
						, DAYOFYEAR(DATE_FORMAT(NOW(), '%Y-12-31')) num_day_of_year
						, DATEDIFF(LAST_DAY(tb2.payment_date), DATE(tb2.payment_date)) num_datediff
						, CONCAT(tb3.firstname_th, ' ', tb3.lastname_th) member_name
						, tb4.account_id
						, tb5.loan_type
						, tb5.contract_number
						, YEAR(tb2.payment_date) payment_date_year
						, MONTH(tb2.payment_date) payment_date_month
						FROM coop_receipt AS tb1
						INNER JOIN coop_finance_transaction AS tb2 ON tb1.receipt_id = tb2.receipt_id
						INNER JOIN coop_mem_apply tb3 ON tb1.member_id = tb3.member_id
						LEFT OUTER JOIN (
							SELECT mem_id, account_id
							FROM coop_maco_account
							WHERE type_id = '2'
								AND account_status = '0'
						) tb4 ON tb1.member_id = tb4.mem_id
						INNER JOIN coop_loan tb5 ON tb2.loan_id = tb5.id
						WHERE tb2.payment_date BETWEEN '{$date_s}' AND '{$date_e}'
							AND (
									( tb1.receipt_code = 'C')
									OR
									( tb1.receipt_code = 'B')
							)
							AND tb1.finance_month_profile_id IS NOT NULL
							AND (tb1.receipt_status IS NULL OR tb1.receipt_status = '')
						GROUP BY tb2.member_id, tb2.receipt_id, tb5.contract_number
						ORDER BY tb2.member_id, tb2.loan_id, tb2.payment_date";
		// AND DATEDIFF(LAST_DAY(tb2.payment_date), DATE(tb2.payment_date)) > 0
		//
		$rs = $this->db->query($sql);
		$data = [];

		foreach ($rs->result_array() as $row) {
				$return_real = 0;
				if(strpos( strtolower($row['receipt_id']), 'b') !== false) { // ผ่านรายการ

						if( $row['loan_amount_balance'] >= 0 ) {
							// คืนเงิน
							$date_payment = DateTime::createFromFormat('Y-m-d', $row['payment_date']);
							$date_of_month = DateTime::createFromFormat('Y-m-d', date('Y-m-t', strtotime($date_s)));
							$date_diff = $date_payment->diff($date_of_month);
							$return = $row['principal_payment'] * ( $interest_rate[$row['loan_type']] / 100 ) * ( $date_diff->format('%a') / $days_of_year ) ;
							$return_real = round($return);
							if( $return_real > 0 ) {
								$data['return'][] = [
									'member_id' => $row['member_id'],
									'member_name' => $row['member_name'],
									'loan_id' => $row['loan_id'],
									'contract_number' => $row['contract_number'],
									'receipt_id' => $row['receipt_id'],
									'account_id' => $row['account_id'],
									'return_principal' => 0,
									'return_interest' => $return_real,
									'surcharge' => 0,
									'is_return' => isset($data_return["{$row['member_id']}#{$row['loan_id']}#{$row['receipt_id']}"]) ? 1 : 0,
									'ret_id' => isset($data_return["{$row['member_id']}#{$row['loan_id']}#{$row['receipt_id']}"]) ? $data_return["{$row['member_id']}#{$row['loan_id']}#{$row['receipt_id']}"]['ret_id'] : 0,
									'return_time' => isset($data_return["{$row['member_id']}#{$row['loan_id']}#{$row['receipt_id']}"]) ? $data_return["{$row['member_id']}#{$row['loan_id']}#{$row['receipt_id']}"]['return_time'] : 0,
									'interest_rate' => $interest_rate[$row['loan_type']],
									'principal_payment' => $row['principal_payment'],
									'interest' => $row['interest'],
									'loan_amount_balance' => $row['loan_amount_balance'],
									'payment_date_year' => $row['payment_date_year'],
									'payment_date_month' => $row['payment_date_month']
								];
							}
						} else {
							// ยอดคงเหลือติดลบ ไม่คืนเงิน
							$data['no_return'][] = [
								'member_id' => $row['member_id'],
								'member_name' => $row['member_name'],
								'loan_id' => $row['loan_id'],
								'contract_number' => $row['contract_number'],
								'receipt_id' => $row['receipt_id'],
								'account_id' => $row['account_id'],
								'return_principal' => $row['principal_payment'],
								'return_interest' => $row['interest'],
								'surcharge' => 0,
								'is_return' => isset($data_return["{$row['member_id']}#{$row['loan_id']}#{$row['receipt_id']}"]) ? 1 : 0,
								'ret_id' => isset($data_return["{$row['member_id']}#{$row['loan_id']}#{$row['receipt_id']}"]) ? $data_return["{$row['member_id']}#{$row['loan_id']}#{$row['receipt_id']}"]['ret_id'] : 0,
								'return_time' => isset($data_return["{$row['member_id']}#{$row['loan_id']}#{$row['receipt_id']}"]) ? $data_return["{$row['member_id']}#{$row['loan_id']}#{$row['receipt_id']}"]['return_time'] : 0,
								'interest_rate' => $interest_rate[$row['loan_type']],
								'principal_payment' => $row['principal_payment'],
								'interest' => $row['interest'],
								'loan_amount_balance' => $row['loan_amount_balance'],
								'payment_date_year' => $row['payment_date_year'],
								'payment_date_month' => $row['payment_date_month']
							];
						}
				} else {
					// จ่ายล่าช้า เก็บเพิ่ม
					$surcharge = $row['principal_payment'] * ( $interest_rate[$row['loan_type']] / 100 ) * ( explode('-', $row['payment_date'])[2] / $days_of_year ) ;
					$surcharge_real = round($surcharge);

					if( ($row['principal_payment'] + $row['loan_amount_balance']) == 0 && $row['loan_amount_balance'] < 0 && $surcharge_real ) {
						$data['no_return'][] = [
							'member_id' => $row['member_id'],
							'member_name' => $row['member_name'],
							'loan_id' => $row['loan_id'],
							'contract_number' => $row['contract_number'],
							'receipt_id' => $row['receipt_id'],
							'account_id' => $row['account_id'],
							'return_principal' => $row['principal_payment'],
							'return_interest' => $row['interest'],
							'surcharge' => 0,
							'is_return' => isset($data_return["{$row['member_id']}#{$row['loan_id']}#{$row['receipt_id']}"]) ? 1 : 0,
							'ret_id' => isset($data_return["{$row['member_id']}#{$row['loan_id']}#{$row['receipt_id']}"]) ? $data_return["{$row['member_id']}#{$row['loan_id']}#{$row['receipt_id']}"]['ret_id'] : 0,
							'return_time' => isset($data_return["{$row['member_id']}#{$row['loan_id']}#{$row['receipt_id']}"]) ? $data_return["{$row['member_id']}#{$row['loan_id']}#{$row['receipt_id']}"]['return_time'] : 0,
							'interest_rate' => $interest_rate[$row['loan_type']],
							'principal_payment' => $row['principal_payment'],
							'interest' => $row['interest'],
							'loan_amount_balance' => $row['loan_amount_balance'],
							'payment_date_year' => $row['payment_date_year'],
							'payment_date_month' => $row['payment_date_month']
						];
					} elseif( $row['loan_amount_balance'] == 0 && $surcharge_real ) {
							$data['surcharge'][] = [
								'member_id' => $row['member_id'],
								'member_name' => $row['member_name'],
								'loan_id' => $row['loan_id'],
								'contract_number' => $row['contract_number'],
								'receipt_id' => $row['receipt_id'],
								'account_id' => $row['account_id'],
								'return_principal' => 0,
								'return_interest' => 0,
								'surcharge' => $surcharge_real,
								'is_return' => isset($data_return["{$row['member_id']}#{$row['loan_id']}#{$row['receipt_id']}"]) ? 1 : 0,
								'ret_id' => isset($data_return["{$row['member_id']}#{$row['loan_id']}#{$row['receipt_id']}"]) ? $data_return["{$row['member_id']}#{$row['loan_id']}#{$row['receipt_id']}"]['ret_id'] : 0,
								'return_time' => isset($data_return["{$row['member_id']}#{$row['loan_id']}#{$row['receipt_id']}"]) ? $data_return["{$row['member_id']}#{$row['loan_id']}#{$row['receipt_id']}"]['return_time'] : 0,
								'interest_rate' => $interest_rate[$row['loan_type']],
								'principal_payment' => $row['principal_payment'],
								'interest' => $row['interest'],
								'loan_amount_balance' => $row['loan_amount_balance'],
								'payment_date_year' => $row['payment_date_year'],
								'payment_date_month' => $row['payment_date_month']
							];
					} elseif( $row['loan_amount_balance'] < 0 ) {
						$return_principal = $row['principal_payment'] + $row['loan_amount_balance'];
						$surcharge = $return_principal * ( $interest_rate[$row['loan_type']] / 100 ) * ( explode('-', $row['payment_date'])[2] / $days_of_year ) ;
						$surcharge_real = round($surcharge);
						if( $surcharge_real > 0 ) {
							$data['surcharge'][] = [
								'member_id' => $row['member_id'],
								'member_name' => $row['member_name'],
								'loan_id' => $row['loan_id'],
								'contract_number' => $row['contract_number'],
								'receipt_id' => $row['receipt_id'],
								'account_id' => $row['account_id'],
								'return_principal' => $return_principal,
								'return_interest' => $row['interest'],
								'surcharge' => $surcharge_real,
								'is_return' => isset($data_return["{$row['member_id']}#{$row['loan_id']}#{$row['receipt_id']}"]) ? 1 : 0,
								'ret_id' => isset($data_return["{$row['member_id']}#{$row['loan_id']}#{$row['receipt_id']}"]) ? $data_return["{$row['member_id']}#{$row['loan_id']}#{$row['receipt_id']}"]['ret_id'] : 0,
								'return_time' => isset($data_return["{$row['member_id']}#{$row['loan_id']}#{$row['receipt_id']}"]) ? $data_return["{$row['member_id']}#{$row['loan_id']}#{$row['receipt_id']}"]['return_time'] : 0,
								'interest_rate' => $interest_rate[$row['loan_type']],
								'principal_payment' => $row['principal_payment'],
								'interest' => $row['interest'],
								'loan_amount_balance' => $row['loan_amount_balance'],
								'payment_date_year' => $row['payment_date_year'],
								'payment_date_month' => $row['payment_date_month']
							];
						}
					}


				}
		}
		return $data;
	}
	 /**************************************
	 * Func คำนวณหักกลบ ( return_type = 1) -- End
	 *************************************/
	 	/**************************************
	 * Func คำนวณหักกลบ ( return_type = 2) -- Start
	 *************************************/
	function cal_process_return_type_2($date_s, $date_e) {
		$month_process = explode('-', $date_s)[1];
		$year_process = explode('-', $date_s)[0];
		$days_of_month = date_format(date_create($date_s), 't');
		$days_of_year = date_format(date_create("{$year_process}-12-31"), 'z') + 1 ;
		$sql = "SELECT tb2.member_id, tb2.receipt_id, tb2.loan_id
					, tb2.principal_payment
					, tb2.interest
					, tb2.total_amount
					, tb2.payment_date
					, tb3.loan_type
					, tb4.account_id
					FROM coop_receipt AS tb1
					INNER JOIN coop_finance_transaction AS tb2 ON tb1.receipt_id = tb2.receipt_id
					INNER JOIN coop_loan tb3 ON tb2.loan_id = tb3.id
					LEFT OUTER JOIN ( SELECT * FROM coop_maco_account WHERE type_id = '2' AND account_status = '0' ) tb4 ON tb1.member_id = tb4.mem_id
					WHERE tb2.payment_date BETWEEN '{$date_s}' AND '{$date_e}'
						AND DATEDIFF(LAST_DAY(tb2.payment_date), DATE(tb2.payment_date)) > 0
						AND ( tb1.receipt_code != 'C')
						AND tb1.finance_month_profile_id IS NULL
						AND tb2.loan_id IS NOT NULL
						AND tb4.account_id IS NOT NULL
						AND (tb1.receipt_status IS NULL OR tb1.receipt_status = '')
					ORDER BY tb2.member_id, tb2.loan_id, tb2.payment_date, tb2.receipt_id";
		$rs = $this->db->query($sql);
		$data = [];
		//echo $sql;
		foreach ($rs->result_array() as $row) {
			/* เช็คว่ามีการผ่านรายการม้ย */
			$sql = "SELECT tb1.member_id, tb1.loan_id, tb1.receipt_id, tb1.payment_date,
				SUM(tb1.principal_payment) principal_payment,
				SUM(tb1.interest) interest,
				SUM(tb1.total_amount) total_amount
				FROM coop_finance_transaction tb1
				INNER JOIN coop_receipt tb2 ON tb1.receipt_id = tb2.receipt_id
				WHERE ( tb2.receipt_code != 'C')
					AND tb1.payment_date BETWEEN '{$date_s}' AND '{$date_e}'
					AND tb1.loan_id IS NOT NULL
					AND tb2.finance_month_profile_id IS NOT NULL
					AND ( tb1.member_id = '{$row['member_id']}' AND tb1.loan_id = '{$row['loan_id']}' )
				GROUP BY tb1.member_id, tb1.loan_id, tb1.receipt_id, tb1.payment_date
			";
			$rs_chk = $this->db->query($sql);
			if( $rs_chk->num_rows() > 0 ) {

				$row_chk = $rs_chk->row_array();
				$date_payment = DateTime::createFromFormat('Y-m-d', $row['payment_date']);
				$date_of_month = DateTime::createFromFormat('Y-m-d', date('Y-m-t', strtotime($date_s)));
				$date_diff = $date_payment->diff($date_of_month);

				$sql = "SELECT interest_rate
								FROM coop_term_of_loan
								WHERE start_date <= '{$date_s}'
									AND type_id = {$row['loan_type']}
								ORDER BY start_date DESC
								LIMIT 1";
				$rs_interest = $this->db->query($sql);
				$interest_rate = $rs_interest->row_array()['interest_rate'];
				$return = $row['principal_payment'] * ( $interest_rate / 100 ) * ( $date_diff->format('%a') / $days_of_year ) ;
				$return_real = round($return);
				$sql = "SELECT *
								FROM coop_process_return
								WHERE return_year = {$year_process}
									AND return_month = {$month_process}
									AND return_type = 2
									AND member_id = '{$row['member_id']}'
									AND loan_id = '{$row['loan_id']}'";
				$rs_return = $this->db->query($sql);
				$row_return = $rs_return->row_array();
				if( strpos($row_chk['receipt_id'], 'B') ) {
					$data['return'][] = [
						'member_id' => $row['member_id'],
						'loan_id' => $row['loan_id'],
						'interest_rate' => $interest_rate,
						'receipt_id' => $row_chk['receipt_id'],
						'account_id' => $row['account_id'],
						'return_amount' => $return_real,
						'ret_id' => $row_return['ret_id'],
						'return_time' => $row_return['return_time'],
						'is_return' => $rs_return->num_rows() ? 1 : 0
					];
				} else {
					$data['no_return'][] = [
						'member_id' => $row['member_id'],
						'loan_id' => $row['loan_id'],
						'interest_rate' => $row['interest_rate'],
						'receipt_id' => $row_chk['receipt_id'],
						'account_id' => $row['account_id'],
						'return_amount' => $return_real,
						'ret_id' => '',
						'return_time' => '',
						'is_return' => $rs_return->num_rows() ? 1 : 0
					];
				}
			}
		}
		return $data;
	}
	 /**************************************
 * Func คำนวณหักกลบ ( return_type = 2) -- End
 *************************************/

	public function customize_refund(){
		$this->load->library('myexcel');
		$objPHPExcel = PHPExcel_IOFactory::load(FCPATH."assets/uploads/tmp_refund/refund_atm270362.xlsx");
		$cell_collection = $objPHPExcel->getActiveSheet()->toArray(null, true,true,true);
		// echo "<pre>";
		// var_dump($cell_collection);exit;
		$data['list'] = $cell_collection;
		$this->libraries->template('finance_process/customize_refund', $data);
	}

	public function save_customize_refund(){
		// echo "<pre>";
		// var_dump($_POST);
		// echo "</pre>";
		if(@$_POST){
			$sql = "SELECT bill_id
							FROM coop_process_return
							WHERE bill_id LIKE 'R256204%'
							ORDER BY bill_id DESC
							LIMIT 1";
			$rs = $this->db->query($sql);
			if( !$rs->num_rows() ) $_bill_id = 1;
			else {
				$row = $rs->result_array()[0];
				$_bill_id = (int)substr($row['bill_id'], 7, 5) + 1;
			}
			$c = 1;
			// var_dump($_POST['member_id']);
			foreach ($_POST['member_id'] as $key => $value) {
				$member_id = $value;
				$return_interest = $_POST['return_interest'][$member_id];
				$contract_number = $_POST['contract_number'][$member_id];
				if($contract_number==""){
					echo "<br>".$member_id;
					// var_dump($_POST['contract_number']);
					// var_dump($_POST['contract_number'][$member_id]);exit;
				}
				$loan_atm_id = @$this->db->get_where("coop_loan_atm", array(
					"contract_number like " => $contract_number,
					"member_id" => $member_id
				))->result_array()[0]['loan_atm_id'];
				$this->db->where('account_id like "00121%"');
				$account_id = @$this->db->get_where("coop_maco_account", array(
					"mem_id" => $member_id
				))->result_array()[0]['account_id'];
				// echo $loan_atm_id." ".$account_id." ".$contract_number." <br>";
				if($loan_atm_id != "" && $account_id != "" && $contract_number != ""){
					

					$sql = "SELECT ret_id
							FROM coop_process_return
							WHERE return_type = 3
								AND member_id = '{$member_id}'
								AND loan_atm_id = '{$loan_atm_id}'
								AND return_year = 2019
								AND return_month = 4
								AND account_id = ''
								";
					$rs_chk = $this->db->query($sql)->result_array();

					$sql = "SELECT account_id
									FROM coop_maco_account
									WHERE type_id = '2'
										AND account_status = '0'
										AND mem_id = '{$member_id}'";
					$rs_account = $this->db->query($sql);

					if(!$rs_chk){
						$sql = "SELECT transaction_balance
						FROM coop_account_transaction
						WHERE account_id = '{$account_id}'
						ORDER BY transaction_time DESC, transaction_id DESC
						LIMIT 1";
						$rs_balance = $this->db->query($sql);
						$row_balance = $rs_balance->result_array()[0];
						$transaction_deposit = $return_interest;
						$transaction_balance = $return_interest + $row_balance['transaction_balance'];
						$sql = "INSERT INTO coop_account_transaction(transaction_time, transaction_list, transaction_withdrawal, transaction_deposit, transaction_balance, account_id, user_id ,transaction_text)
											VALUES(NOW(), 'REVD', 0, {$transaction_deposit}, {$transaction_balance}, '{$account_id}','{$_SESSION['USER_ID']}' , 'process_interest_edit_customize')";
						$this->db->query($sql);	

						$bill_id = sprintf('R%s%s%05d', "2562", "04", $_bill_id++);
						$sql = "INSERT INTO coop_process_return(member_id, loan_atm_id, return_type, account_id, receipt_id, bill_id, return_principal, return_interest, return_amount, return_year, return_month, return_time, user_id)
								VALUES('{$member_id}', '{$loan_atm_id}', 3, '{$account_id}', '{$receipt_id}', '{$bill_id}', 0, {$return_interest}, {$return_interest}, 2019, 4, NOW(), '{$_SESSION['USER_ID']}')";
						@$this->db->query($sql);
					}
				}
			}
		}

		header("location: ".base_url("Finance_process/customize_refund"));
	}

	public function customize_refund_excel(){
		$this->load->library('myexcel');
		$objPHPExcel = PHPExcel_IOFactory::load(FCPATH."assets/uploads/tmp_refund/refund_atm270362.xlsx");
		$cell_collection = $objPHPExcel->getActiveSheet()->toArray(null, true,true,true);
		foreach ($cell_collection as $key => $value) {
			$this->db->limit(1);
			$this->db->where("account_id like '00121%'");
			$account_id = $this->db->get_where("coop_maco_account", array(
				//"mem_id" => sprintf('%06d', $value['A']),
				"mem_id" => $this->center_function->complete_member_id($value['A']),
			))->result_array()[0]['account_id'];
			$member_id = $this->center_function->complete_member_id($value['A']);
			$fullname = $value['C']." ".$value['D'];
			$contract_number = $value['B'];
			$principal = 0;
			$interest = $value['H'];
			$return_interest_amount = $principal + $interest;

			$status = @$this->db->get_where("coop_process_return", array(
				"member_id" => $member_id,
				"return_year" => "2019",
				"return_month" => "4",
				"return_type" => "3",
				"return_amount" => $return_interest_amount
			))->result_array();
			$return_time = empty($status) ? 'ยังไม่ได้ทำการโอนเงิน' : 'โอนเงินแล้ว';

			$arr_data['datas'][$index++] = [
				'account_id' => $account_id,
				'member_id' => $member_id,
				'account_name' => $fullname,
				'contract_number' => $contract_number,
				'principal' => $principal,
				'interest' => $interest,
				'return_interest_amount' => $return_interest_amount,
				'return_time' => $return_time
			];
		}

		$arr_data['title'] = "คืนเงิน ฉ.atm (คำนวณต.ค.61-ม.ค.62)";

		$this->load->view('finance_process/process_return_excel', $arr_data);
	}

}
