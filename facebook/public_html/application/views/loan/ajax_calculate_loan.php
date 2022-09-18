<?php
ini_set('precision', '16');
$this->load->model("Interest_modal", "interest");
$this->load->model("Condition_loan_model", "condition_model");
function cal_days_in_year($year){
    return 365;
//    $days=0;
//    for($month=1;$month<=12;$month++){
//        $days = $days + cal_days_in_month(CAL_GREGORIAN,$month,$year);
//    }
//    return $days;
}
function force_summonth($summonth,$period){
	//if($period=='1'){
	//	$summonth = $summonth-1;
	//}else{
	//	$summonth = $summonth;
	//}
	return $summonth;
}

if(!empty($_POST["ajax"])) {
	if($_POST["do"] == "cal") {
		$interest = (double)$_POST["interest"]; // อัตราดอกเบี้ย
		$loan = (double)$_POST["loan"]; // จำนวนเงินกู้
		$pay_type = $_POST["pay_type"]; // ปรเภท ชำระเท่ากันทุกงวด,ต้นเท่ากันทุกงวด
		$period = ($_POST["period_type"] == '1')?@$_POST["period"]:@$_POST["period_amount_bath"]; // จำนวน งวด  หรือ เงิน แล้วแต่ type
		$period_old = (double)$_POST["period_old"]; // จำนวน งวด  หรือ เงิน แล้วแต่ type  เก่า 
		$money_period_2 = (double)$_POST["money_period_2"]; // ยอดผ่อนชำระต่อเดือน  เก่า 		
		$_day = $day = (double)$_POST["day"];
		$month = $_month = (double)$_POST["month"];
		//$year  = (double)$_POST["year"];
		$year  = (double)$_POST["year"];
        $_period_amt = $_POST['period'];
        $loan_type = $_POST['loan_type'];

		$opt['force_a'] = $i;
		$opt['optional'] = $loan;
		$opt['required'] = "";
		$opt['ยอดยื่นกู้'] = $loan;
		$opt['งวดยื่นกู้'] = $period;
		$opt['งวดชำระยื่นกู้'] = $i;
		$interest = $this->condition_model->get_value_condition_of_loan($loan_type, "interest_rate", $opt);

        //หาดอกเบี้ยหัก ณ ที่จ่าย
        $payment_interest_current = 0;

            $date_interest = ($year - 543) . "-" . $month . "-" . $day;
            $date_interest_start = date('Y-m-d', strtotime('-1 day', strtotime($date_interest)));
            $date_interest_end = date('Y-m-t', strtotime($date_interest));
            $data_interest_count = date_diff(date_create($date_interest_start), date_create($date_interest_end));
            $date_amt = $data_interest_count->format('%a');
        if($day >= 6) {
            if ($date_amt) {
				$raw_payment_interest_curr = (double)($loan * ($interest / 100) / cal_days_in_year(($year - 543))) * $date_amt;
				if($raw_payment_interest_curr >= 0.01 && $raw_payment_interest_curr < 0.50 ) {
					$payment_interest_current = CEIL($raw_payment_interest_curr);
				}else{
					$payment_interest_current = ROUND($raw_payment_interest_curr, 0, PHP_ROUND_HALF_UP);
				}
            }
        }

        $day_of_month = 0;
        if($day >= 6){
            $day_of_month = date('t', strtotime($date_interest." +1 month"));
        }else{
            $day_of_month = date('t', strtotime($date_interest));
        }
		if($day >= 6){
		    $day = date('t', strtotime($day_of_month));
		    $month += 1;
        }


		if($month > 12){
		    $month = 1;
		    $year += 1;
        }

		$period_type= (double)$_POST["period_type"]; // ประเภท งวดหรือจำนวนเงิน
		$loan_type= $_POST["loan_type"]; // ประเภทการกู้เงิน
		//echo '<pre>'; print_r($_POST); echo '</pre>';
		//echo '---------------------------<br>';
		//echo $day.'<hr>';
		//echo $month.'<hr>';
		//echo $year.'<hr>';


		if($period_type == '1' && $pay_type=='2'){
			$total_per_period = $loan/$period;

			$opt = array();
			$opt['force_a'] = $_period_amt;
			$opt['optional'] = $loan;
			$opt['required'] = "";
			$interest = $this->condition_model->get_value_condition_of_loan($loan_type, "interest_rate", $opt);

			$date_start = ($year-543)."-".sprintf("%02d",$month)."-".$day;
			$date_period_1 = date('Y-m-t',strtotime('+1 month',strtotime($date_start)));
			$diff = date_diff(date_create($date_start),date_create($date_period_1));
			$date_count = $diff->format("%a");
			$date_count = 31;

			$optional = array();
			$optional['operation'] ="interest_rate_array";


			$interest_period_1 = ((($loan*$interest)/100)/cal_days_in_year(($year-543)))*$date_count;
            $per_period_cal1 = round((( 1-pow(1/(1+( ($interest/100) /12)),$period))),5);
            if($this->interest->getGroupId($loan_type) == 3) {
				$rules = $this->interest->get_interest($loan_type, $date_start, $optional, "", $period);
				$per_period = $this->interest->calStepInt($rules, $loan, $date_start);
			}else{
				$per_period = round(($loan_amount * ( ($interest/100) / 12 ))/( 1-pow(1/(1+( ($interest/100) /12)),$period)), 0);
			}

			if($period_old == $period && !empty($money_period_2)){
				$period = $money_period_2;
			}else{
				$period = $per_period;
			}
			$period_type = 2;
		}
        $date_start = ($year-543)."-".$month."-".$day;
		$pay_period = $loan / $period;

		$a = ceil($pay_period/10)*10;
		$daydiff = date('t', strtotime(($year-543)."-".$month."-".$day)) - $day;

		ob_start(); ?>
		<div id="cal_table">
		<table class="table table-condensed">
			<thead>
				<tr>
					<th class="text-center" style="width: 8%;">งวดที่</th>
					<th class="text-right"  style="width: 12%;">เงินต้นคงเหลือ</th>
					<th class="text-right"  style="width: 15%;">วันที่หัก</th>
					<th class="text-right"  style="width: 14%;">จำนวนวัน</th>
					<th class="text-right"  style="width: 9%;">ดอกเบี้ย</th>
					<th class="text-right"  style="width: 14%;">เงินต้นชำระ</th>
					<th class="text-right"  style="width: 15%;">รวมชำระต่อเดือน</th>
				</tr>
			</thead>
			<tbody>
				<?php
				$loan_remain = $loan;
				$is_last = FALSE;
				$total_loan_pri = 0;
				$total_loan_int = 0;
				$total_loan_pay = 0;
				$d = $period - 1;
				for ($i=1; $i <= $period; $i++) {
					if($loan_remain <= 0 ){ break; }

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

					if($pay_type == 1) {						
						if ($period_type == 1) {					
									if ($month > 12) {
											$month = 1;
											$year += 1;
									}

									$loan_pri = ceil($a/10)*10;
									$nummonth = cal_days_in_month(CAL_GREGORIAN, $month , ($year-543));
									$summonth = $nummonth;
									$daydiff = 31 - $day;
									if ($i == 1) {
										if ($daydiff >= 0) {
											//$month += 1;
											if ($month > 12) {
													$month = 1;
													$year += 1;
											}
											$nummonth = cal_days_in_month(CAL_GREGORIAN, $month , ($year-543));
											$summonth = $nummonth;
											$summonth = $daydiff + 31;
										 }
									}

									$summonth = force_summonth($summonth,$i);
                                    //echo "1 :: ".$daydiff." :: ".$summonth." :: ".$date_start; exit;
									$loan_int = round($loan_remain * ($interest / (cal_days_in_year(($year-543)) / $summonth)) / 100,2);
									if($loan_pri < 0){
										$loan_pri = 0;
									}
									$loan_pay = $loan_pri + $loan_int;
									$loan_remain -= ceil($loan_pri/10)*10;
						} else if ($period_type == 2) {
							if ($month > 12) {
									$month = 1;
									$year += 1;
							}
							$nummonth = cal_days_in_month(CAL_GREGORIAN, $month , ($year-543));
							$summonth = $nummonth;
							$daydiff = 31 - $day;
							if ($i == 1) {
								if ($daydiff >= 0) {
									$month += 1;
									if ($month > 12) {
										$month = 1;
										$year += 1;
									}
									$nummonth = cal_days_in_month(CAL_GREGORIAN, $month , ($year-543));
									$summonth = $nummonth;
									$summonth = $daydiff + 31;
								 }
							}

							$summonth = force_summonth($summonth,$i);
                            //echo "2 :: ".$daydiff." :: ".$summonth; exit;
							$loan_pri = ceil($period/100)*100;
							$loan_int = round($loan_remain * ($interest / (cal_days_in_year(($year-543)) / $summonth)) / 100,2);
							if($loan_pri < 0){
								$loan_pri = 0;
							}
							$loan_pay = $loan_pri + $loan_int;
							$loan_remain -= ceil($loan_pri/10)*10;
					}
				}else if($pay_type == 2) {
						if ($period_type == 1) {
									if ($month > 12) {
											$month = 1;
											$year += 1;
									}

									$loan_pri = ceil($a/100)*100;
									$nummonth = cal_days_in_month(CAL_GREGORIAN, $month , ($year-543));
									$summonth = $nummonth;
									$daydiff = date('t', strtotime(($year-543).'-'.sprintf('%02d', $month).'-'.$day)) - $day;
									if ($i == 1) {
										if ($daydiff >= 0) {
											$month += 1;
											if ($month > 12) {
													$month = 1;
													$year += 1;
											}
											$nummonth = cal_days_in_month(CAL_GREGORIAN, $month , ($year-543));
											$summonth = $nummonth;
											$summonth = $daydiff;
										 }
									}
									$summonth = force_summonth($summonth,$i);
                                    //echo "3 :: ".$daydiff." :: ".$summonth; exit;
									$loan_int = round($loan_remain * ($interest / (cal_days_in_year(($year-543)) / $summonth)) / 100,2);
									$loan_pri = $loan_pri - $loan_int;
									if($loan_pri < 0){
										$loan_pri = 0;
									}
									$loan_pay = $loan_pri + $loan_int;

									$loan_remain -= ceil($loan_pri/10)*10;
						} else if ($period_type == 2) {
							if ($month > 12) {
									$month = 1;
									$year += 1;
								
							}

							$nummonth = cal_days_in_month(CAL_GREGORIAN, $month , ($year-543));
							$summonth = $nummonth;
							if($month == 3 && $_month == 2){
								$daydiff = date('t', strtotime(($year - 543) . '-' . sprintf('%02d', $_month) . '-' . $day)) - $day;
							}else {
								//echo date('t', strtotime(($year - 543) . '-' . sprintf('%02d', $month) . '-' . $day)) ." - ". $day; exit;
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
									$nummonth = cal_days_in_month(CAL_GREGORIAN, $month , ($year-543));
									$summonth = $nummonth;
									//echo $daydiff; exit;
									if($_day > 5) {
                                        $summonth = $daydiff + $nummonth;
                                    }else{
                                        $summonth = $daydiff + 1;
                                        if($day == 0){
                                            $summonth --;
                                        }
                                    }
								 }
							}
							$summonth = force_summonth($summonth,$i);
							//echo $summonth; exit;
							$loan_pri = ceil($period/1)*1;
							$loan_int = round($loan_remain * ($interest / (cal_days_in_year(($year-543)) / $summonth)) / 100,0);
							$loan_pri = $loan_pri - $loan_int;
                            $forcast_remain = round($loan_remain - $loan_pri, 0);

                            if(round($forcast_remain, 0) < round($period, 0) && $i >  ($_period_amt-1)){
                                $loan_pri = round($loan_remain, 0 );
                            }
							if($loan_pri < 0){
								$loan_pri = 0;
							}
							$loan_pay = $loan_pri + $loan_int;

							$loan_remain -= ceil($loan_pri/1)*1;
					}
				}

					if($loan_remain <= 0) {
						$loan_pri += $loan_remain;
						$loan_pay = $loan_pri + $loan_int;
						$loan_remain = 0;
						@$count = $count + 1;
					}

					$sumloan = $loan_remain;
					$sumloanarr[] = $loan_remain;
					$sumint[] = $loan_int;
					
					if ($i == $period) {
						$loan_pri = $sumloanarr[$d];
						$loan_pay = $loan_pri + $loan_int;
					}
					
					@$total_loan_int += $loan_int;
					@$total_loan_pri += $loan_pri;
					@$total_loan_pay += $loan_pay;

					@$total_loan_pri_m +=  $loan_pri;
					@$total_loan_int_m += $loan_int;
					@$total_loan_pay_m += $loan_pay;
					?>

					<tr>
						<td class="text-center">
							<?php echo $i; ?>
							<input type="hidden" name="data[coop_loan_period][<?php echo $i; ?>][period_count]" value="<?php echo $i; ?>">
						</td>
						<td class="text-right">
							<?php echo number_format(($sumloan) , 2); ?>
							<input type="hidden" name="data[coop_loan_period][<?php echo $i; ?>][outstanding_balance]" value="<?php echo number_format($sumloan,2,".",""); ?>">
						</td>
						<td class="text-right">
							<?php
							echo $nummonth." / ".$month." / ".$year;
							if($i==1){ ?>
								<input type="hidden" id="first_date_period_label" value="<?php echo $nummonth."/".$month."/".$year; ?>">
								<input type="hidden" id="first_date_period" value="<?php echo ($year-543)."-".sprintf('%02d',$month)."-".$nummonth; ?>">
								<input type="hidden" id="first_pay" value="<?php echo number_format($loan_pay,2); ?>">
								<input type="hidden" id="first_interest_amount" value="<?php echo number_format($loan_int,2); ?>">
								<input type="hidden" id="first_summonth" value="<?php echo $summonth; ?>">
							<?php }
							if($i==2){ ?>
								<input type="hidden" id="second_date_period_label" value="<?php echo $nummonth."/".$month."/".$year; ?>">
								<input type="hidden" id="second_date_period" value="<?php echo ($year-543)."-".sprintf('%02d',$month)."-".$nummonth; ?>">
								<input type="hidden" id="second_pay" value="<?php echo number_format($loan_pay,2); ?>">
								<input type="hidden" id="second_summonth" value="<?php echo $summonth; ?>">
							<?php } ?>
							
							<input type="hidden" name="data[coop_loan_period][<?php echo $i; ?>][date_period]" value="<?php echo ($year-543)."-".sprintf('%02d',$month)."-".$nummonth; ?>">
						</td>
						<th class="text-right">
							<?php echo $summonth?>
							<input type="hidden" name="data[coop_loan_period][<?php echo $i; ?>][date_count]" value="<?php echo $summonth; ?>">
						</th>
						<td class="text-right">
							<?php echo number_format($loan_int, 2); ?>
							<input type="hidden" name="data[coop_loan_period][<?php echo $i; ?>][interest]" value="<?php echo number_format($loan_int,2,".",""); ?>">
						</td>
						<td class="text-right">
							<?php echo number_format($loan_pri, 2); ?>						
							<input type="hidden" name="data[coop_loan_period][<?php echo $i; ?>][principal_payment]" value="<?php echo number_format($loan_pri,2,".",""); ?>">
						</td>
						<td class="text-right">
							<?php echo number_format($loan_pay, 2); ?>
							<input type="hidden" name="data[coop_loan_period][<?php echo $i; ?>][total_paid_per_month]" value="<?php echo number_format($loan_pay,2,".",""); ?>">
						</td>
					</tr>

					<?php

					if($is_last) {
						break;
					}
					$month++;
					?>
					<?php if ($month > 12) { ?>
					<tr style="font-weight: bold;">
						<td class="text-center"></td>
						<td class="text-center"></td>
						<td class="text-center">รวมปี</td>
						<td class="text-center"><?php echo $year?></td>
						<td class="text-right"><?php echo number_format($total_loan_int_m, 0); ?></td>
						<td class="text-right"><?php echo number_format($total_loan_pri_m, 2); ?></td>
						<td class="text-right"><?php echo number_format($total_loan_pay_m, 2); ?></td>
					</tr>
					<?php if ($month > 12) { $total_loan_int_m = 0;  $total_loan_pri_m = 0; $total_loan_pay_m = 0; } ?>

					<?php } else if (($i-1) == $d) { ?>
						<tr style="font-weight: bold;">
							<td class="text-center"></td>
							<td class="text-center"></td>
							<td class="text-center">รวมปี</td>
							<td class="text-center"><?php echo $year?></td>
							<td class="text-right"><?php echo number_format($total_loan_int_m, 0); ?></td>
							<td class="text-right"><?php echo number_format($total_loan_pri_m, 2); ?></td>
							<td class="text-right"><?php echo number_format($total_loan_pay_m, 2); ?></td>
						</tr>
						<?php $is_last = TRUE; } ?>

			<?php } ?>
				<input type="hidden" id="last_period" value="<?php echo date('Y-m-t',strtotime('-1 month',strtotime(($year-543)."-".$month."-".$nummonth))); ?>">
				<tr style="font-weight: bold;">
					<td class="text-center"></td>
					<td class="text-center"></td>
					<td class="text-right"></td>
					<td class="text-right"> รวม </td>
					<td class="text-right">
						<?php echo number_format($total_loan_int, 0); ?>
						<input type="hidden" name="data[coop_loan][loan_interest_amount]" value="<?php echo number_format($total_loan_int,0,".",""); ?>">
					</td>
					<td class="text-right">
						<?php echo number_format($total_loan_pri, 2); ?>
						<input type="hidden" name="data[coop_loan][loan_amount_balance]" value="<?php echo number_format($total_loan_pri,2,".",""); ?>">
					</td>
					<td class="text-right">
						<?php echo number_format($total_loan_pay, 2); ?>
						<input type="hidden" name="data[coop_loan][loan_amount_total]" value="<?php echo number_format($total_loan_pay,2,".",""); ?>">
						<input type="hidden" name="data[coop_loan][loan_amount_total_balance]" value="<?php echo number_format($total_loan_pay,2,".",""); ?>">
					</td>
				</tr>
			</tbody>
		</table>
		</div>
		<input type="hidden" id="max_period" value="<?php echo $i-1; ?>">
		<input type="hidden" id="already_cal" value="1">
        <input type="hidden" id="interest_current_value" value="<?php echo number_format($payment_interest_current, 0); ?>">
		<?php
		$loan_table = ob_get_contents();
		ob_end_clean();
		$is_error = FALSE;
		?>
		<?php
		if(!$is_error) { ?>
			<?php
			echo $loan_table;
			?>
			<div class="text-center p-v-xxl hidden-print">
			
			</div>
			<?php
		}
	}
	exit;
}
##### END AJAX #####
