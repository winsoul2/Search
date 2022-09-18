<?php if( ! defined('BASEPATH')) exit('No direct script access allowed');

class Loan_period_models extends CI_Model {
	public function __construct()
	{
		parent::__construct();
		//$this->load->database();
		# Load libraries
		//$this->load->library('parser');
		$this->load->helper(array('html', 'url'));
	}

	public function get_loan_period($data){
		$arr_data = array();
		//echo $this->db->last_query(); echo '<hr>';
		$year_now = date('Y');
		$days_of_year = $this->center_function->get_days_of_year($year_now);//จำนวนวันของปี
		
		$interest = (double)$data["interest"]; // อัตราดอกเบี้ย
		$loan = (double)$data["loan"]; // จำนวนเงินกู้
		$pay_type = $data["pay_type"]; // ปรเภท ชำระเท่ากันทุกงวด,ต้นเท่ากันทุกงวด
		$period = (double)$data["period"]; // จำนวน งวด  หรือ เงิน แล้วแต่ type
		$day = (double)$data["day"];
		$month = (double)$data["month"];
		$year  = (double)$data["year"];
		$period_type= $data["new_installment_period_type"]; // ประเภท งวดหรือจำนวนเงิน 
		$new_installment_period_type= $data["new_installment_period_type"]; // ประเภท งวดหรือจำนวนเงิน 
		//1 งวด
		//2 จำนวนเงิน
		$check_period = '';
		if($period > 0){
			if($period_type == '1' && $pay_type=='2'){
				$total_per_period = $loan/$period;

				$date_start = ($year-543)."-".$month."-".$day;
				$date_period_1 = date('Y-m-t',strtotime('+1 month',strtotime($date_start)));
				$diff = date_diff(date_create($date_start),date_create($date_period_1));
				$date_count = $diff->format("%a");
				$date_count = 31;
				$interest_period_1 = ((($loan*$interest)/100)/$days_of_year)*$date_count;
				$first_period = ($period_type == '1' && $pay_type=='2') ? $total_per_period : $loan / $period;
				
				$per_period = ($loan * ( ($interest/100) / 12 ))/( 1-pow(1/(1+( ($interest/100) /12)),$period));
				$period = $per_period;
				$period_type = 2;
				
			}else if($period_type == "2"){
				$first_period = $period;
			}else{
				$first_period = $loan / $period;
			}		
			
			$pay_period = $loan / $period;
			$a = ceil($pay_period/10)*10;

			$daydiff = date('t') - $day;

		// ---------------------------
			$loan_remain = $loan;
			$is_last = FALSE;
			$total_loan_pri = 0;
			$total_loan_int = 0;
			$total_loan_pay = 0;
			$d = $period - 1;

			$peroid_row = array();
			for ($i=1; $i <= $period; $i++) {

					if($loan_remain <= 0 ){ break; }
					if($pay_type == 1) {
						if ($period_type == 1) {
									if ($month > 12) {
											$month = 1;
											$year += 1;
									}
									$loan_pri = ceil($a/100)*100;
									$nummonth = @cal_days_in_month(CAL_GREGORIAN, $month , $year);
									$summonth = $nummonth;
									$daydiff = 31 - $day;
									if ($i == 1) {
										if ($daydiff >= 0) {
											$month += 1;
											if ($month > 12) {
													$month = 1;
													$year += 1;
											}
											$nummonth = @cal_days_in_month(CAL_GREGORIAN, $month , $year);
											$summonth = $nummonth;
											$summonth = $daydiff + 31;
										 }
									}
									$summonth = $this->force_summonth($summonth,$i);
									$loan_int = round($loan_remain * ($interest / ($days_of_year / $summonth)) / 100);
									if($loan_pri < 0){
										$loan_pri = 0;
									}
									$loan_pay = $loan_pri + $loan_int;
									$loan_remain -= ceil($loan_pri/100)*100;
						} else if ($period_type == 2) {
							if ($month > 12) {
									$month = 1;
									$year += 1;
							}
							$nummonth = @cal_days_in_month(CAL_GREGORIAN, $month , $year);
							$summonth = $nummonth;
							$daydiff = 31 - $day;
							if ($i == 1) {
								if ($daydiff >= 0) {
									$month += 1;
									$nummonth = @cal_days_in_month(CAL_GREGORIAN, $month , $year);
									$summonth = $nummonth;
									$summonth = $daydiff + 31;
								 }
							}
							$summonth = $this->force_summonth($summonth,$i);
							$loan_pri = ceil($period/100)*100;
							$loan_int = round($loan_remain * ($interest / ($days_of_year / $summonth)) / 100);
							if($loan_pri < 0){
								$loan_pri = 0;
							}
							$loan_pay = $loan_pri + $loan_int;
							//$loan_remain -= $loan_pri;
							$loan_remain -= ceil($loan_pri/100)*100;
					}
				}else if($pay_type == 2) {
						if ($period_type == 1) {
									if ($month > 12) {
											$month = 1;
											$year += 1;
									}
									//$loan_pri = $a;
									$loan_pri = ceil($a/100)*100;
									$nummonth = @cal_days_in_month(CAL_GREGORIAN, $month , $year);
									$summonth = $nummonth;
									$daydiff = 31 - $day;
									if ($i == 1) {
										if ($daydiff >= 0) {
											$month += 1;
											if ($month > 12) {
													$month = 1;
													$year += 1;
											}
											$nummonth = @cal_days_in_month(CAL_GREGORIAN, $month , $year);
											$summonth = $nummonth;
											$summonth = $daydiff + 31;
										 }
									}
									$summonth = $this->force_summonth($summonth,$i);
									$loan_int = round($loan_remain * ($interest / ($days_of_year / $summonth)) / 100);
									$loan_pri = $loan_pri - $loan_int;
									if($loan_pri < 0){
										$loan_pri = 0;
									}
									$loan_pay = $loan_pri + $loan_int;
									$loan_remain -= ceil($loan_pri/100)*100;
						} else if ($period_type == 2) {
							if ($month > 12) {
									$month = 1;
									$year += 1;
							}
							$nummonth = @cal_days_in_month(CAL_GREGORIAN, $month , $year);
							$summonth = $nummonth;
							$daydiff = 31 - $day;
							if ($i == 1) {
								if ($daydiff >= 0) {
									$month += 1;
									$nummonth = @cal_days_in_month(CAL_GREGORIAN, $month , $year);
									$summonth = $nummonth;
									$summonth = $daydiff + 31;
								 }
							}
							$summonth = $this->force_summonth($summonth,$i);
							$loan_pri = ceil($period/100)*100;
							$loan_int = round($loan_remain * ($interest / ($days_of_year / $summonth)) / 100);
							$loan_pri = $loan_pri - $loan_int;
							if($loan_pri < 0){
								$loan_pri = 0;
							}
							$loan_pay = $loan_pri + $loan_int;
							//$loan_remain -= $loan_pri;
							$loan_remain -= ceil($loan_pri/100)*100;
					}
				}

					if($loan_remain <= 0) {
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
					//@$total_loan_pri += $loan_pri;
					@$total_loan_pri += ceil($loan_pri/100)*100;
					@$total_loan_pay += $loan_pay;

					//@$total_loan_pri_m += $loan_pri;
					@$total_loan_pri_m += ceil($loan_pri/100)*100;
					@$total_loan_int_m += $loan_int;
					@$total_loan_pay_m += $loan_pay;

					if((int)$month == '2'){
						$nummonth = '28';
					}
					$peroid_row[$i-1]['period_count']			= $i;
					$peroid_row[$i-1]['outstanding_balance']	= $sumloan;
					$peroid_row[$i-1]['date_period']			= ($year)."-".sprintf('%02d',$month)."-".$nummonth;
					$peroid_row[$i-1]['date_count']				= $summonth;
					$peroid_row[$i-1]['interest']				= $loan_int;
					$peroid_row[$i-1]['principal_payment']		= $loan_pri;
					$peroid_row[$i-1]['total_paid_per_month']	= $loan_pay;
					$peroid_row[$i-1]['loan_id']				= $data['loan_id'];
					 //echo 'period_type='.$period_type .'<hr>';
					 //echo $loan_int.'>'.$period .'<hr>';
					 //echo "<pre>";
					 //print_r($peroid_row);
					 //echo "</pre>";
					$arr_data['peroid_row'] = $peroid_row;
					
					if($i == '2'){
						$arr_data['money_per_period'] = $loan_pay;
					}
					
					if($is_last) {
						break;
					}
					$month++;
			}
			
			//เช็คจำนวนเงวดทั้งหมดมากกว่า จำนวนเงินผ่อนต่อ งวด
			if($period_amount > $loan_int){
				$check_period = 'ไม่สามารถบันทึกข้อมูลได้ เนื่องจากดอกเบี้ยมากว่ายอดผ่อนชำระต่อเดือน ';
				break;
			}

			$period_amount = $i-1;
			$arr_data['period_amount'] = $period_amount;
			$arr_data['check_period'] = $check_period;
		}else{
			$check_period = 'ไม่สามารถบันทึกข้อมูลได้ ';
			$arr_data['check_period'] = $check_period;
		}
		return $arr_data;
	}

	private function force_summonth($summonth,$period){
		if($period=='1'){
			$summonth = $summonth-1;
		}else{
			$summonth = $summonth;
		}
		return $summonth;
	}
}
