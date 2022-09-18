<?php
class Script_period extends CI_Controller{

	public function __construct()
	{
		parent::__construct();
		$this->load->model("Interest_Modal", "interest");
		$this->load->model("Condition_loan_model", "condition_model");
	}

	public function index(){

	}

	public function getLoan($contract, $member){
		return $this->db->get_where('coop_loan',
			array( 'contract_number' => $contract,
				'member_id' => $member), 1)->row_array();
	}

	public function getLoanById($id){
		return $this->db->get_where('coop_loan',
			array( 'id' => $id), 1)->row_array();
	}

	function cal_days_in_year($year){
		return 365;

	}
	function force_summonth($summonth,$period){
		return $summonth;
	}

	public function calc_period()
	{
		if(!isset($_GET['loan_id'])) {
			echo "No contract or member";
			exit;
		}else {
			$contract = self::getLoanById($_GET['loan_id']);
		}

		$interest = (double)$contract['interest_per_year']; // อัตราดอกเบี้ย
		$loan = (double)$contract['loan_amount'];; // จำนวนเงินกู้
		$pay_type = 2; // ปรเภท ชำระเท่ากันทุกงวด,ต้นเท่ากันทุกงวด
		$period = $contract['period_amount'];// จำนวน งวด  หรือ เงิน แล้วแต่ type
		$period_old = 0; // จำนวน งวด  หรือ เงิน แล้วแต่ type  เก่า
		$money_period_2 = (double)$contract['money_period_1']; // ยอดผ่อนชำระต่อเดือน  เก่า
		$_day = $day = (double)date('d', strtotime($contract['approve_date']));
		$month = $_month = (double)date('n', strtotime($contract['approve_date']));
		//$year  = (double)$_POST["year"];
		$year = (double)date('Y', strtotime($contract['approve_date']));
		$_period_amt = $contract['period_amount'];
		$loan_type = $contract['loan_type'];

		$opt['force_a'] = $i;
		$opt['optional'] = $loan;
		$opt['required'] = "";
		$opt['ยอดยื่นกู้'] = $loan;
		$opt['งวดยื่นกู้'] = $period;
		$opt['งวดชำระยื่นกู้'] = $i;
		$interest = $this->condition_model->get_value_condition_of_loan($loan_type, "interest_rate", $opt);

		//หาดอกเบี้ยหัก ณ ที่จ่าย
		$payment_interest_current = 0;

		$date_interest = ($year) . "-" . $month . "-" . $day;
		$date_interest_start = date('Y-m-d', strtotime('-1 day', strtotime($date_interest)));
		$date_interest_end = date('Y-m-t', strtotime($date_interest));
		$data_interest_count = date_diff(date_create($date_interest_start), date_create($date_interest_end));
		$date_amt = $data_interest_count->format('%a');
		if ($day >= 6) {
			if ($date_amt) {
				$payment_interest_current = ROUND(($loan * ($interest / 100) / self::cal_days_in_year(($year))) * $date_amt, 0, PHP_ROUND_HALF_UP);
			}
		}

		$day_of_month = 0;
		if ($day >= 6) {
			$day_of_month = date('t', strtotime($date_interest . " +1 month"));
		} else {
			$day_of_month = date('t', strtotime($date_interest));
		}
		if ($day >= 6) {
			$day = date('t', strtotime($day_of_month));
			$month += 1;
		}


		if ($month > 12) {
			$month = 1;
			$year += 1;
		}

		$period_type = 1; // ประเภท งวดหรือจำนวนเงิน
		$loan_type = $contract["loan_type"]; // ประเภทการกู้เงิน


		//echo " period_type: $period_type, pay_type: $pay_type";exit;

		if ($period_type == '1' && $pay_type == '2') {
			$total_per_period = $loan / $period;

			$opt = array();
			$opt['force_a'] = $_period_amt;
			$opt['optional'] = $loan;
			$opt['required'] = "";
			$interest = $this->condition_model->get_value_condition_of_loan($loan_type, "interest_rate", $opt);

			$date_start = ($year) . "-" . $month . "-" . $day;
			$date_period_1 = date('Y-m-t', strtotime('+1 month', strtotime($date_start)));
			$diff = date_diff(date_create($date_start), date_create($date_period_1));
			$date_count = $diff->format("%a");
			$date_count = 31;
			$interest_period_1 = ((($loan * $interest) / 100) / self::cal_days_in_year(($year))) * $date_count;

			//echo "({$loan} * (({$interest} / 100) / 12)) / (1 - pow(1 / (1 + (({$interest} / 100) / 12)), {$period}))\n";
			$per_period = ($loan * (($interest / 100) / 12)) / (1 - pow(1 / (1 + (($interest / 100) / 12)), $period));

			if ($period_old == $period || $money_period_2 > 0) {
				$period = $money_period_2;
			} else {
				$period = $per_period;
			}
			$period_type = 2;
		}
		$date_start = ($year) . "-" . $month . "-" . $day;
		$pay_period = $loan / $period;
		$a = ceil($pay_period / 10) * 10;
		$daydiff = date('t', strtotime(($year) . "-" . $month . "-" . $day)) - $day;

		$loan_remain = $loan;
		$is_last = FALSE;
		$total_loan_pri = 0;
		$total_loan_int = 0;
		$total_loan_pay = 0;
		$d = $period - 1;
		for ($i = 1; $i <= $period; $i++) {
			if ($loan_remain <= 0) {
				break;
			}

			$opt['force_a'] = $i;
			$opt['optional'] = $loan;
			$opt['required'] = "";
			$opt['ยอดยื่นกู้'] = $loan;
			$opt['งวดยื่นกู้'] = $period;
			$opt['งวดชำระยื่นกู้'] = $i;
			$interest = $this->condition_model->get_value_condition_of_loan($loan_type, "interest_rate", $opt);

			if($i == 1 && $day <= 5){
				$day -= 1;
			}

			if ($pay_type == 1) {
				if ($period_type == 1) {
					if ($month > 12) {
						$month = 1;
						$year += 1;
					}

					$loan_pri = ceil($a / 10) * 10;
					$nummonth = cal_days_in_month(CAL_GREGORIAN, $month, ($year));
					$summonth = $nummonth;
					$daydiff = 31 - $day;
					if ($i == 1) {
						if ($daydiff >= 0) {
							//$month += 1;
							if ($month > 12) {
								$month = 1;
								$year += 1;
							}
							$nummonth = cal_days_in_month(CAL_GREGORIAN, $month, ($year));
							$summonth = $nummonth;
							$summonth = $daydiff + 31;
						}
					}

					$summonth = self::force_summonth($summonth, $i);
					//echo "1 :: ".$daydiff." :: ".$summonth." :: ".$date_start; exit;
					$loan_int = round($loan_remain * ($interest / (self::cal_days_in_year(($year)) / $summonth)) / 100, 2);
					if ($loan_pri < 0) {
						$loan_pri = 0;
					}
					$loan_pay = $loan_pri + $loan_int;
					$loan_remain -= ceil($loan_pri / 10) * 10;
				} else if ($period_type == 2) {
					if ($month > 12) {
						$month = 1;
						$year += 1;
					}
					$nummonth = cal_days_in_month(CAL_GREGORIAN, $month, ($year));
					$summonth = $nummonth;
					$daydiff = 31 - $day;
					if ($i == 1) {
						if ($daydiff >= 0) {
							$month += 1;
							if ($month > 12) {
								$month = 1;
								$year += 1;
							}
							$nummonth = cal_days_in_month(CAL_GREGORIAN, $month, ($year));
							$summonth = $nummonth;
							$summonth = $daydiff + 31;
						}
					}

					$summonth = self::force_summonth($summonth, $i);
					//echo "2 :: ".$daydiff." :: ".$summonth; exit;
					$loan_pri = ceil($period / 100) * 100;
					$loan_int = round($loan_remain * ($interest / (self::cal_days_in_year(($year)) / $summonth)) / 100, 2);
					if ($loan_pri < 0) {
						$loan_pri = 0;
					}
					$loan_pay = $loan_pri + $loan_int;
					$loan_remain -= ceil($loan_pri / 10) * 10;
				}
			} else if ($pay_type == 2) {
				if ($period_type == 1) {
					if ($month > 12) {
						$month = 1;
						$year += 1;
					}

					$loan_pri = ceil($a / 100) * 100;
					$nummonth = cal_days_in_month(CAL_GREGORIAN, $month, ($year));
					$summonth = $nummonth;
					$daydiff = date('t', strtotime(($year) . '-' . sprintf('%02d', $month) . '-' . $day)) - $day;
					if ($i == 1) {
						if ($daydiff >= 0) {
							$month += 1;
							if ($month > 12) {
								$month = 1;
								$year += 1;
							}
							$nummonth = cal_days_in_month(CAL_GREGORIAN, $month, ($year));
							$summonth = $nummonth;
							$summonth = $daydiff;
						}
					}
					$summonth = self::force_summonth($summonth, $i);
					//echo "3 :: ".$daydiff." :: ".$summonth; exit;
					$loan_int = round($loan_remain * ($interest / (self::cal_days_in_year(($year)) / $summonth)) / 100, 2);
					$loan_pri = $loan_pri - $loan_int;
					if ($loan_pri < 0) {
						$loan_pri = 0;
					}
					$loan_pay = $loan_pri + $loan_int;

					$loan_remain -= ceil($loan_pri / 10) * 10;
				} else if ($period_type == 2) {
					if ($month > 12) {
						$month = 1;
						$year += 1;

					}

					$nummonth = cal_days_in_month(CAL_GREGORIAN, $month, ($year));
					$summonth = $nummonth;
					if($month == 3 && $_month == 2) {
						$daydiff = date('t', strtotime(($year) . '-' . sprintf('%02d', $_month) . '-' . $day)) - $day;
					}else{
//						$daydiff = date('t', strtotime(($year) . '-' . sprintf('%02d', $month) . '-' . $day)) - $day;
                        if($day == 0){
                            $daydiff = date('t', strtotime(($year - 543) . '-' . sprintf('%02d', $month) . '-' . '01'));
                        }else{
                            $daydiff = date('t', strtotime(($year - 543) . '-' . sprintf('%02d', $month) . '-' . $day)) - $day;
                        }
					}
					if ($i == 1) {
						if ($daydiff >= 0) {
							//$month += 1;

							if ($month > 12) {
								$month = 1;
								$year += 1;
							}
							$nummonth = cal_days_in_month(CAL_GREGORIAN, $month, ($year));
							$summonth = $nummonth;
							if ($_day > 5) {
								$summonth = $daydiff + $nummonth;
							} else {
								$summonth = $daydiff + 1;
                                if($day == 0){
                                    $summonth --;
                                }
							}
						}
					}
					$summonth = self::force_summonth($summonth, $i);
					$loan_pri = ceil($period / 1) * 1;
					$loan_int = round($loan_remain * ($interest / (self::cal_days_in_year(($year)) / $summonth)) / 100, 0);
					$loan_pri = $loan_pri - $loan_int;
					$forcast_remain = round($loan_remain - $loan_pri, 0);

					if (round($forcast_remain, 0) < round($period, 0) && $i > ($_period_amt - 1)) {
						$loan_pri = round($loan_remain, 0);
					}
					if ($loan_pri < 0) {
						$loan_pri = 0;
					}
					$loan_pay = $loan_pri + $loan_int;

					$loan_remain -= ceil($loan_pri / 1) * 1;
				}
			}

			if ($loan_remain <= 0) {
				$loan_pri += $loan_remain;
				$loan_pay = $loan_pri + $loan_int;
				$loan_remain = 0;
				@$count = $count + 1;
			}

			$sumloan = $loan_remain + $loan_pri;
			$sumloanarr[] = $loan_remain + $loan_pri;
			$sumint[] = $loan_int;

			if ($i == $period) {
				$loan_pri = $sumloanarr[$d];
				$loan_pay = $loan_pri + $loan_int;
			}

			@$total_loan_int += $loan_int;
			@$total_loan_pri += $loan_pri;
			@$total_loan_pay += $loan_pay;

			@$total_loan_pri_m += $loan_pri;
			@$total_loan_int_m += $loan_int;
			@$total_loan_pay_m += $loan_pay;

			$data['coop_loan_period'][$i]['loan_id'] = $contract['id'];
			$data['coop_loan_period'][$i]['period_count'] =  $i;
			$data['coop_loan_period'][$i]['outstanding_balance']=  number_format($sumloan,2,".","");
			$data['coop_loan_period'][$i]['date_period']= ($year)."-".sprintf('%02d',$month)."-".$nummonth;
			$data['coop_loan_period'][$i]['date_count']= $summonth;
			$data['coop_loan_period'][$i]['interest']= number_format($loan_int,2,".","");
			$data['coop_loan_period'][$i]['principal_payment']=number_format($loan_pri,2,".","");
			$data['coop_loan_period'][$i]['total_paid_per_month'] = number_format($loan_pay,2,".","");

			if($is_last) {
				break;
			}
			$month++;

			if($month > 12){

				if ($month > 12) {
					$total_loan_int_m = 0;
					$total_loan_pri_m = 0;
					$total_loan_pay_m = 0;
				}

			}else if(($i-1) == $d){
				$is_last = TRUE;
			}

		}

		if(isset($_GET['exec']) && $_GET['exec'] === '200'){

			$this->db->trans_begin();

			$this->db->delete('coop_loan_period', array('loan_id' => $contract['id']));

			$this->db->insert_batch('coop_loan_period', $data['coop_loan_period']);

			if($this->db->trans_status()){
				$this->db->trans_commit();
				header("Content-type:application/json;");
				echo json_encode(array('status' => 'ok', 'code' => 200));
			}else{
				$this->db->trans_rollback();
				header("Content-type:application/json;");
				echo json_encode(array('status' => 'error', 'code' => 400));
			}
		}else{

			header("Content-type:application/json;");
			echo json_encode($data);
			exit;
		}
	}
}
