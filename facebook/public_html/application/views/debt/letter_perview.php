<style>
	td{
		font-family: THSarabunNew;
		font-size: 14px;
		padding: 4px;
	}	

	@media print {		
		td{
			font-family: THSarabunNew !important;
			font-size: 15px;
		}
	}	
</style>	
		
		<div style="width: 980px;">
			<?php
				$tab8 = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";	

				if(!empty($data)) {
				foreach($data AS $key_letter=>$row_letter){

					$row_member = @$row_letter['row_member'];
					$row_non_pay = @$row_letter['row_non_pay'];
					$letter_runno = @$row_letter['letter_runno'];
					
					$row_loan_pay = @$row_letter['row_loan_pay'];
					$letter_mm_yy = @$row_letter['letter_mm_yy'];
					$pay_amount = @$row_letter['pay_amount'];
					
					$non_pay_now = @$row_letter['non_pay_now'];
					$total_pay_now = @$row_letter['total_pay_now'];
					
					$rs_guarantee_person = @$row_letter['rs_guarantee_person'];
					$row_non_share = @$row_letter['row_non_share'];
					$real_share_amount = @$row_letter['real_share_amount'];
					$no_share_amount = @$row_letter['no_share_amount'];
					$share_amount = @$row_letter['share_amount'];
					$non_pay_month_share = @$row_letter['non_pay_month_share'];
					
					$check_share = @$row_letter['check_share'];					
					$check_month_balance = @$row_letter['check_month_balance'];					
					$share_num_period = @$row_letter['share_num_period'];




					$loan_details = $row_letter['loan_details'];
					$guarantee_persons = $row_letter['guarantee_persons'];
					$total_principal = $row_letter['total_principal'];
					$total_interest = $row_letter['total_interest'];
					$total_debt_principal = $row_letter['total_debt_principal'];
					$total_debt_interest = $row_letter['total_debt_interest'];
					$share = $row_letter['share'];
					$check_dept = @$row_letter['check_dept'];
					$real_pay_amount = @$row_letter['paid_amount'];
					$no_pay_amount = ($total_principal + $total_interest + $share['share']) - $real_pay_amount;
					$letter = @$row_letter['letter'];
					$share_list = $row_letter['share_list'];
					$share_dept = 0;
					if(!empty($share)) {
						$share_dept = !empty($share["share_dept"]) ? $share["share_dept"] : 0;
					}
			?>
			<!--หน้า 1-->
			<?php 
				$img_signature_manager = '<img style="max-height: 35px;" src="'.base_url(PROJECTPATH.'/assets/images/coop_signature/'.@$signature['signature_3']).'" />';
				$manager_name = @$signature['manager_name'];
				$manager_position = "ผู้จัดการ";
			if(!empty($loan_details)){	
			?>
					<div class="panel panel-body" style="padding-top:10px !important;min-height: 1200px;">
						<table style="width: 80%;">
							<tr>
								<td style="width:40%;">&nbsp;</td>
								<td style="width:10%;">&nbsp;</td>
								<td style="width:10%;">&nbsp;</td>
								<td style="width:40%;vertical-align: top;" class="text-right">
									<?php if(@$key_letter==0){?>
									<a class="no_print" onclick="window.print();"><button class="btn btn-perview btn-after-input" type="button"><span class="icon icon-print" aria-hidden="true"></span></button></a>
									<?php } ?>
								 </td>
							</tr>
							<tr>
								<td style="vertical-align: top;height: 55px;"></td>
								<td style="width:150px;vertical-align: top;text-align: center;"  colspan="2" rowspan="2">
									<img src="<?php echo base_url(PROJECTPATH.'/assets/images/coop_profile/'.@$row_profile['coop_img']); ?>" alt="Logo" style="width: 150px;" />
								</td>
								<td></td>
							</tr> 
							<tr>
								<td style="vertical-align: top;">
									<?php 
									$month_year_runno = sprintf("%02d",@$row_member['non_pay_month'])."-".@$row_member['non_pay_year']."/".@$row_member['non_pay_year'];
									$loan_runno = @$row_member['member_id']."(1)-".$month_year_runno;
									echo 'ที่ สอ.สป.(สินเชื่อ)    '.@$loan_runno;
									?>
									
								</td>
								<td style="vertical-align: top;padding-left: 20px;">
									<?php
										echo @$row_profile['coop_name_th'];
										echo '<br>';
										echo @$row_profile['address1']." ".@$row_profile['address2'];
									?> 
								 </td>
							</tr> 
							<tr>
								<td colspan="4">&nbsp;</td>
							</tr> 
							<tr>
								<td class="text-center" colspan="4">
									<?php
										$mature_pay_date = "";
										$mature_pay_date .= date('t',strtotime((@$letter['non_pay_year']-543)."-".sprintf("%02d",@$letter['non_pay_month'])."-01"));
										$mature_pay_date .= " ".@$month_arr[@$letter['non_pay_month']];
										$mature_pay_date .= " ".@$letter['non_pay_year'];
										echo $mature_pay_date;
									?>
								</td>
							</tr> 
							<tr>
								<td colspan="4">
									เรื่อง แจ้งหนี้ค้างชำระ	
								</td>
							</tr>
							<tr>
								<td colspan="4">
									เรียน <?php echo @$row_member['full_name']." (".@$row_member['member_id'].")";?>
								</td>
							</tr>
							<tr>
								<td colspan="4">
									<?php
										$loan_index = 0;
										$mm_yy_array = array();
										$total_principal_last_month = 0;
										$total_interest_last_month = 0;
										$real_pay_amount_last_month = 0;
										foreach($loan_details as $contract_number=>$detail) {
											if($loan_index == 0) {
												echo "อ้างถึง สัญญา".@$detail['loan_type']."เลขที่ ".@$contract_number;
											} else {
												echo "<br/>".$tab8."สัญญา".@$detail['loan_type']."เลขที่ ".@$contract_number;
											}

											foreach($detail["mm_yy"] as $mm_yy) {
												$mm_yy_array[$mm_yy["year"].sprintf("%02d",$mm_yy["month"])] = $mm_yy;
											}
											foreach($share["mm_yy"] as $mm_yy) {
												$mm_yy_array[$mm_yy["year"].sprintf("%02d",$mm_yy["month"])] = $mm_yy;
											}

											$total_principal_last_month += $detail['principal_last'];
											$total_interest_last_month += $detail['interest_last'];
											$real_pay_amount_last_month += $detail['principal_paid_last'] + $detail['interest_paid_last'];

											$loan_index++;
										}

										$real_pay_amount_last_month += $share['share_paid_last'];

										$no_pay_amount_last_month = ($total_principal_last_month + $total_interest_last_month + $share['share_last']) - $real_pay_amount_last_month;
										$no_pay_amount_all = ($total_debt_principal + $total_debt_interest + $share_dept);
										$no_pay_amount_except_last = $no_pay_amount_all - $no_pay_amount_last_month;
										ksort($mm_yy_array);
										$mmyy_index = 0;
										$loan_debt_period = "";
										foreach($mm_yy_array as $mm_yy) {
											if(!($letter['non_pay_month'] == $mm_yy['month'] && $letter['non_pay_year'] == $mm_yy['year'])) {
												if($mmyy_index == 0) {
													$loan_debt_period .= $month_arr[$mm_yy['month']]." ".$mm_yy['year'];
												} else {
													$loan_debt_period .= " ,".$month_arr[$mm_yy['month']]." ".$mm_yy['year'];
												}
												$mmyy_index++;
											}
										}
									?>
								</td>
							</tr> 
							<tr>
								<td colspan="4">&nbsp;</td>
							</tr> 
							<tr>
								<td colspan="4">
									<?php
										if(!empty($check_dept)) {
											$text_amount_dept = " ท่านมียอดค้างชำระเป็นเงินจำนวน ".number_format($total_principal_last_month+$total_interest_last_month+$share['share_dept_last'],2)." บาท (".$this->center_function->convert($total_principal_last_month+$total_interest_last_month+$share['share_dept_last']).") ";
											$text_amount_dept .= !empty($no_pay_amount_except_last) ? " และมียอดค้างชำระในเดือนก่อนจำนวน ".number_format($no_pay_amount_except_last,2)." บาท (".$this->center_function->convert($no_pay_amount_except_last).") " : "";
											$text_repayment = !empty($real_pay_amount_last_month) ? "ปรากฏว่าทางสหกรณ์ฯได้รับชำระหนี้จากท่านบางส่วน เป็นจำนวน ".number_format(@$real_pay_amount_last_month,2)." บาท (".$this->center_function->convert($real_pay_amount_last_month).") ท่านยังไม่ได้ชำระหนี้อีกจำนวน ".number_format($no_pay_amount_all,2)." บาท (".$this->center_function->convert($no_pay_amount_all).") " : "";
											$text_repayment .= empty($real_pay_amount_last_month) && !empty($no_pay_amount_except_last) ? "รวมเป็นเงิน ".number_format($no_pay_amount_all,2)." บาท (".$this->center_function->convert($no_pay_amount_all).") ปรากฎว่าท่านยังไม่ได้ชำระหนี้ดังกล่าวให้แก่สหกรณ์ฯ" : "";
											$text_repayment .= empty($real_pay_amount_last_month) && empty($no_pay_amount_except_last) ? "ปรากฎว่าท่านยังไม่ได้ชำระหนี้ดังกล่าวให้แก่สหกรณ์ฯ" : "";
											$month_dept = $loan_debt_period;
										}else {
											if($letter_runno == 1){
												$month_dept = $loan_debt_period;
												$text_amount_dept = " เป็นเงินจำนวน ".number_format(@$total_debt_principal+$total_debt_interest+$share['share'],2)." บาท (".$this->center_function->convert(@$total_debt_principal+$total_debt_interest+$share['share']).") ";
											} else {
												if(@$check_month_balance == 0){
													$month_dept = $loan_debt_period;
													$text_amount_dept = " เป็นเงินจำนวน ".number_format(@$total_debt_principal+$total_debt_interest+$share['share'],2)." บาท (".$this->center_function->convert(@$total_debt_principal+$total_debt_interest+$share['share']).") ";
												}else{
													$month_dept = $loan_debt_period; //มากกว่า 1 งวด
													$text_amount_dept = " เป็นเงินจำนวน ".number_format(@$total_debt_principal+$share['share'],2)." บาท (".$this->center_function->convert(@$total_debt_principal+$share['share']).") และเป็นดอกเบี้ยคงค้างจำนวน ".number_format(@$total_debt_interest,2)." บาท รวมทั้งสิ้น ".number_format(@$total_debt_interest+$total_debt_principal+$share['share'],2)." บาท";
												}
											}
											$text_repayment = "ท่านยังไม่ได้ชำระหนี้ดังกล่าว";
										}	

										//กำหนดชำระหนี้
										$mature_pay_date = "";
										$mature_pay_date .= date('t',strtotime(($letter['non_pay_year']-543)."-".sprintf("%02d",$letter['non_pay_month'])."-01"));
										$mature_pay_date .= " ".$month_arr[$letter['non_pay_month']];
										$mature_pay_date .= " ".$letter['non_pay_year'];							

										$detail1 = "";
										$detail1 .= $tab8."ตามหนังสือที่อ้างอิงถึง ท่านได้กู้เงินสหกรณ์ออมทรัพย์ครูสมุทรปราการ จำกัด ";

										foreach($loan_details as $detail) {
											$detail1 .= "ประเภท".@$detail['loan_type'];
											$detail1 .= "จำนวนเงิน ".number_format(@$detail['loan_amount'],2)."บาท ";
											$detail1 .= "(".$this->center_function->convert(@$detail['loan_amount']).") ";
											$detail1 .= "โดยได้ตกลงชำระหนี้".@$detail['loan_type'];
											if(empty($detail["is_atm"])) {
												$detail1 .= !empty($detail['period_amount']) ? "เป็นระยะเวลา ".number_format(@$detail['period_amount'],0)." งวด" : "";
											} else {
												$detail1 .= !empty($detail['period_amount']) ? "เป็นระยะเวลาสูงสุด ".number_format(@$detail['period_amount'],0)." งวด" : "";
											}
											$detail1 .= !empty($detail['row_loan_pay']) ? " งวดละ ".number_format(@$detail['row_loan_pay'],2)." บาท  " : "  ";
										}
										$detail1 .= "ซึงท่านได้รับเงินจำนวนดังกล่าวครบถ้วนแล้ว และท่านได้ถือหุ้นเดือนละ ".number_format(@$row_member['share_month'],2)." บาท ดังความที่แจ้งอยู่แล้วนั้น";
										$detail1 .= "<br>";
										$prev_month_text = !empty($month_dept) ? "หนี้ในเดือน ".$month_dept." โดยใน" : "";
										$detail1 .= $tab8."เนื่องจากท่านมียอดค้างชำระ".$prev_month_text."เดือน ".$month_arr[$letter['non_pay_month']]." ".$letter['non_pay_year']." ซึ่งครบกำหนดชำระหนี้ในวันที่ ".$mature_pay_date.$text_amount_dept;
										$detail1 .= "".$text_repayment." อันเป็นการกระทำที่ผิดสัญญาและทำให้สหกรณ์ฯได้รับความเสียหาย";
										$detail1 .= "<br>";
										$detail1 .= $tab8."ฉะนั้น ข้าพเจ้าในฐานะผู้รับมอบอำนาจจึงขอให้ท่านนำเงินดังกล่าว พร้อมดอกเบี้ยมาชำระให้แก่สหกรณ์ฯภายใน 20 วัน นับตั้งแต่ได้รับหนังสือหรือถือว่าได้รับหนังสือฉบับนี้ ";
										$detail1 .= "หากพ้นกำหนดดังกล่าว สหกรณ์ฯมีความจำเป็นต้องดำเนินการตามขั้นตอนของกฏหมายและเสนอให้คณะกรรมการดำเนินการพิจารณาท่านพ้นสมาชิกภาพตามข้อบังคับต่อไป";

										echo @$detail1;
									?>
								</td> 
							</tr> 
							<tr>
								<td colspan="4">&nbsp;</td>
							</tr>
							<tr>
								<td colspan="4">
									<?php echo $tab8.$tab8; ?>
									จึงเรียนมาเพื่อทราบและดำเนินการต่อไป
								</td>
							</tr>
							<tr>
								<td colspan="4">&nbsp;</td>
							</tr> 
							<tr>
								<td colspan="4" style="text-align: center;">
									ขอแสดงความนับถือ
								</td>
							</tr> 
							<tr>
								<td colspan="4">&nbsp;</td>
							</tr> 
							<tr>
								<td colspan="4"  style="text-align: center;">
									<?php echo @$img_signature_manager;?>
								</td>
							</tr>
							<tr>
								<td colspan="4" style="text-align: center;">
									<?php echo "(".@$manager_name.")";?>
								</td>
							</tr> 
							<tr>
								<td colspan="4" style="text-align: center;">
									<?php echo @$manager_position;?>
								</td>
							</tr> 
							<tr>
								<td colspan="4" style="text-align: center;">
									<?php echo @$row_profile['coop_name_th'];?>
								</td>
							</tr>
							<tr>
								<td colspan="4">&nbsp;</td>
							</tr> 
							<tr>
								<td colspan="1" >
									<font style="font-size: 12px;">
										หมายเหตุ หากท่านมีประวัติผิดนัด<br/>
										ชำระหนี้ ท่านจะไม่สามารถขอรับ<br/>
										สวัสดิการต่างๆจากสหกรณ์ได้ จนกว่า<br/>
										ท่านจะชำระหนี้ครบถ้วน
									</font>
								</td>
							</tr> 
							<tr>
								<td colspan="4">
									ฝ่ายนิติกร<br>
									โทร. 02-3842493-4 ต่อ 24<br>
									โทรสาร. 02-3842495<br><br>
									<font style="font-size: 12px;">(หากการชำระเงินของท่าน สวนทางกับหนังสือฉบับนี้ ต้องกราบขออภัยเป็นอย่างสูง กรุณาแจ้งหรือส่งหลักฐานการชำระเงินกลับในกรณีที่ได้ชำระเงินแล้ว)</font>
								</td>
							</tr> 
						</table>
					</div>

			<!--หน้า 2 ผู้ค้ำประกัน-->
			<?php
				$n = 1;
				foreach($guarantee_persons as $contract_number=>$row_guarantee_contract) {
					foreach($row_guarantee_contract as $row_guarantee_person) {
						$n++;
						if(!empty($row_guarantee_person['guarantee_person_id'])) {
							$guarantee_person_id = $row_guarantee_person['guarantee_person_id'];
							$member_id = $guarantee_person_id;
							$guarantee_person_name = $row_guarantee_person['prename_full'].$row_guarantee_person['firstname_th']."  ".$row_guarantee_person['lastname_th'];
			?>
					<div class="panel panel-body page-break" style="padding-top:10px !important;min-height: 1200px;">
						<table style="width: 80%;">
							<tr>
								<td style="width:40%;">&nbsp;</td>
								<td style="width:10%;">&nbsp;</td>
								<td style="width:10%;">&nbsp;</td>
								<td style="width:40%;vertical-align: top;" class="text-right">&nbsp;</td>
							</tr>
							<tr>
								<td style="vertical-align: top;height: 55px;"></td>
								<td style="width:150px;vertical-align: top;text-align: center;"  colspan="2" rowspan="2">
									<img src="<?php echo base_url(PROJECTPATH.'/assets/images/coop_profile/'.@$row_profile['coop_img']); ?>" alt="Logo" style="width: 150px;" />
								</td>
								<td></td>
							</tr> 
							<tr>
								<td style="vertical-align: top;">
									<?php 
									$month_year_runno = sprintf("%02d",@$row_member['non_pay_month'])."-".@$row_member['non_pay_year']."/".@$row_member['non_pay_year'];
									$loan_runno = @$row_member['member_id']."(".$n.")-".$month_year_runno;
									echo 'ที่ สอ.สป.(สินเชื่อ)    '.@$loan_runno;
									?>
									
								</td>
								<td style="vertical-align: top;padding-left: 20px;">
									<?php
										echo @$row_profile['coop_name_th'];
										echo '<br>';
										echo @$row_profile['address1']." ".@$row_profile['address2'];
									?> 
								 </td>
							</tr> 
							<tr>
								<td colspan="4">&nbsp;</td>
							</tr> 
							<tr>
								<td class="text-center" colspan="4">
									<?php
										$mature_pay_date = "";
										$mature_pay_date .= date('t',strtotime((@$letter['non_pay_year']-543)."-".sprintf("%02d",@$letter['non_pay_month'])."-01"));
										$mature_pay_date .= " ".@$month_arr[@$letter['non_pay_month']];
										$mature_pay_date .= " ".@$letter['non_pay_year'];
										echo $mature_pay_date;
									?>
								</td>
							</tr> 
							<tr>
								<td colspan="4">&nbsp;</td>
							</tr> 
							<tr>
								<td colspan="4">
									เรื่อง บอกกล่าวผู้ค้ำประกัน
								</td>
							</tr>
							<tr>
								<td colspan="4">
									เรียน <?php echo @$guarantee_person_name." (".$guarantee_person_id.")";?>
								</td>
							</tr>
							<tr>
								<td colspan="4">
									อ้างถึง สัญญาค้ำประกัน เลขที่ <?php echo $contract_number;?>
								</td>
							</tr> 
							<tr>
								<td colspan="4">
									<?php
										$loan_debt_period = "";
										$mmyy_index = 0;
										foreach($loan_details[$contract_number]["mm_yy"] as $mm_yy) {
											if(!($letter['non_pay_month'] == $mm_yy['month'] && $letter['non_pay_year'] == $mm_yy['year'])) {
												if($mmyy_index == 0) {
													$loan_debt_period .= $month_arr[$mm_yy['month']]." ".$mm_yy['year'];
												} else {
													$loan_debt_period .= " ,".$month_arr[$mm_yy['month']]." ".$mm_yy['year'];
												}
												$mmyy_index++;
											}
										}

										$tab8 = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";	
										if(!empty($loan_details[$contract_number]['check_dept'])){
											$month_dept = $loan_debt_period;
											$text_amount_dept = " ผู้กู้มียอดค้างชำระเป็นจำนวนเงิน ".number_format($loan_details[$contract_number]['principal_last']+$loan_details[$contract_number]['interest_last'],2)
																		." บาท (".$this->center_function->convert($loan_details[$contract_number]['principal_last']+$loan_details[$contract_number]['interest_last']).") ";
											$gua_prev_debt = $loan_details[$contract_number]['principal_debt']+$loan_details[$contract_number]['interest_debt']-$loan_details[$contract_number]['principal_debt_last']-$loan_details[$contract_number]['interest_debt_last'];
											$text_amount_dept .= !empty($gua_prev_debt) ? "และมียอดค้างชำระเดือนก่อนจำนวน ".number_format($gua_prev_debt,2)." บาท (".$this->center_function->convert($gua_prev_debt).")" : "";
											$gua_total_debt = $loan_details[$contract_number]['principal_paid_last']+$loan_details[$contract_number]['interest_paid_last'];
											$text_repayment = !empty($gua_total_debt) ? " ปรากฎว่าทางสหกรณ์ได้รับชำระจากผู้กู้บางส่วนจำนวน "
																	.number_format($gua_total_debt,2)
																	." บาท (".$this->center_function->convert($gua_total_debt).") คงค้างชำระจำนวน "
																	.number_format(($loan_details[$contract_number]['principal_debt']+$loan_details[$contract_number]['interest_debt']),2)
																	." บาท"
																	." (".$this->center_function->convert($loan_details[$contract_number]['principal_debt']+$loan_details[$contract_number]['interest_debt']).") " : "";
											$text_repayment .=  empty($gua_total_debt) && !empty($gua_prev_debt) ? 
																	" รวมเป็นเงิน ".number_format($loan_details[$contract_number]['principal_debt']+$loan_details[$contract_number]['interest_debt'],2)
																	." (".$this->center_function->convert($loan_details[$contract_number]['principal_debt']+$loan_details[$contract_number]['interest_debt']).") ปรากฎว่าผู้กู้ยังไม่ได้ชำระหนี้ดังกล่าวให้แก่สหกรณ์ฯอันเป็นการกระทำที่ผิดสัญญาและทำให้สหกรณ์ได้รับความเสียหาย" : "";
											$text_repayment .=  empty($gua_total_debt) && empty($gua_prev_debt) ? "ปรากฎว่าผู้กู้ยังไม่ได้ชำระหนี้ดังกล่าวให้แก่สหกรณ์ฯอันเป็นการกระทำที่ผิดสัญญาและทำให้สหกรณ์ได้รับความเสียหาย" : "";
										}else {
											if($letter_runno == 1){
												$month_dept = $loan_debt_period;
												$text_amount_dept = " เป็นเงินจำนวน ".number_format($loan_details[$contract_number]['principal_debt']+$loan_details[$contract_number]['interest_debt'],2)
																		." บาท (".$this->center_function->convert($loan_details[$contract_number]['principal_debt']+$loan_details[$contract_number]['interest_debt']).") ";
												$text_last = "";
											} else {
												if(@$check_month_balance == 0){
													$month_dept = $loan_debt_period;
													$text_amount_dept = " เป็นเงินจำนวน ".number_format($loan_details[$contract_number]['principal_debt']+$loan_details[$contract_number]['interest_debt'],2)
																			." บาท (".$this->center_function->convert($loan_details[$contract_number]['principal_debt']+$loan_details[$contract_number]['interest_debt']).") ";
													$text_last = "";
												}else{
													$month_dept = $loan_debt_period;
													$text_amount_dept = " เป็นเงินจำนวน ".number_format($loan_details[$contract_number]['principal_debt'],2).
																			" บาท (".$this->center_function->convert($loan_details[$contract_number]['principal_debt']).") และเป็นดอกเบี้ยคงค้างจำนวน "
																				.number_format(@$loan_details[$contract_number]['interest_debt'],2)." บาท รวมทั้งสิ้น "
																				.number_format($loan_details[$contract_number]['interest_debt']+$loan_details[$contract_number]['principal_debt'],2)." บาท";
												}
											}
											$text_repayment = "ปรากฎว่าผู้กู้ไม่ชำระหนี้ดังกล่าวให้แก่สหกรณ์อันเป็นการกระทำที่ผิดสัญญาและทำให้สหกรณ์ฯได้รับความเสียหาย";
										}

										if($letter_runno > 1) {
											$text_last = "<br>".$tab8."ฉะนั้น ข้าพเจ้าในฐานะผู้รับมอบอำนาจ จึงขอให้ท่านในฐานะผู้ค้ำประกัน ชำระเงินจำนวน "
																		.number_format($loan_details[$contract_number]['interest_debt']+$loan_details[$contract_number]['principal_debt'],2)
																		." บาท พร้อมดอกเบี้ยให้แก่สหกรณ์ ภายใน 20 วัน นับตั้งแต่ได้รับหนังสือหรือถือว่าได้รับหนังสือฉบับนี้ หากพ้นกำหนดดังกล่าว สหกรณ์ มีความจำเป็นต้องดำเนินการกับท่านตามกฎหมายต่อไป";
										}

										//กำหนดชำระหนี้
										$mature_pay_date = "";
										$mature_pay_date .= date('t',strtotime(($letter['non_pay_year']-543)."-".sprintf("%02d",$letter['non_pay_month'])."-01"));
										$mature_pay_date .= " ".$month_arr[$letter['non_pay_month']];
										$mature_pay_date .= " ".$letter['non_pay_year'];
										// $data_arr['data'][$key_letter]['loan_details'][$letter_detail['loan_contract_number']]['loan_amount']
										$detail2 = "";
										$detail2 .= $tab8."ตามหนังสือที่อ้างอิงถึง ท่านตกลงยอมผูกพันตนค้ำประกันการกู้เงินของ ".@$row_member['full_name'];
										$detail2 .= " เป็นจำนวนเงิน ".number_format($loan_details[$contract_number]['loan_amount'],2)." บาท (".$this->center_function->convert(@$loan_details[$contract_number]['loan_amount']).")";
										$detail2 .= "จาก สหกรณ์ออมทรัพย์ครูสมุทรปราการ จำกัด ";
										$detail2 .= !empty($loan_details[$contract_number]['period_amount']) || !empty($loan_details[$contract_number]['row_loan_pay']) ? "โดยส่งชำระหนี้ " : "";
										$detail2 .= !empty($loan_details[$contract_number]['period_amount']) ? "เป็นระยะเวลา ".number_format(@$loan_details[$contract_number]['period_amount'],0)." งวด" : "";
										$detail2 .= !empty($loan_details[$contract_number]['row_loan_pay']) ? " งวดละ ".number_format(@$loan_details[$contract_number]['row_loan_pay'],2)." บาท  " : "  ";
										$detail2 .= "และตกลงว่าหากผู้กู้ไม่ชำระหนี้ดังกล่าว ท่านจะเป็นผู้ชำระแทนดังความแจ้งแล้วนั้น";
										$detail2 .= "<br>";
										// $detail2 .= $tab8."เนื่องจาก การชำระหนี้ในเดือน ".$month_dept." ครบกำหนดชำระหนี้ในวันที่ ".$mature_pay_date.$text_amount_dept;
										$prev_month_text = !empty($month_dept) ? "หนี้ในเดือน ".$month_dept." โดยใน" : "";
										$detail2 .= $tab8."เนื่องจาก ผู้กู้มียอดค้างชำระ".$prev_month_text."เดือน ".$month_arr[$letter['non_pay_month']]." ".$letter['non_pay_year']." ครบกำหนดชำระหนี้ในวันที่ ".$mature_pay_date.$text_amount_dept;
										$detail2 .= $text_repayment;
										$detail2 .= $text_last;

										echo @$detail2;
									?>
								</td>
							</tr> 
							<tr>
								<td colspan="4">&nbsp;</td>
							</tr> 
							<tr>
								<td colspan="4">
									<?php echo $tab8.$tab8; ?>
									จึงเรียนมาเพื่อทราบ
								</td>
							</tr>
							<tr>
								<td colspan="4">&nbsp;</td>
							</tr> 
							<tr>
								<td colspan="4" style="text-align: center;">
									ขอแสดงความนับถือ
								</td>
							</tr> 
							<tr>
								<td colspan="4">&nbsp;</td>
							</tr> 
							<tr>
								<td colspan="4"  style="text-align: center;">							
									<?php echo @$img_signature_manager;?>
								</td>
							</tr> 
							<tr>
								<td colspan="4" style="text-align: center;">
									<?php echo "(".@$manager_name.")";?>
								</td>
							</tr> 
							<tr>
								<td colspan="4" style="text-align: center;">
									<?php echo @$manager_position;?>
								</td>
							</tr> 
							<tr>
								<td colspan="4" style="text-align: center;">
									<?php echo @$row_profile['coop_name_th'];?>
								</td>
							</tr>
							<tr>
								<td colspan="4">&nbsp;</td>
							</tr>
							<tr>
								<td colspan="4">
									ฝ่ายนิติกร<br>
									โทร. 02-3842493-4 ต่อ 24<br>
									โทรสาร. 02-3842495<br><br>
									<font style="font-size: 12px;">(หากการชำระเงินของท่าน สวนทางกับหนังสือฉบับนี้ ต้องกราบขออภัยเป็นอย่างสูง กรุณาแจ้งหรือส่งหลักฐานการชำระเงินกลับในกรณีที่ได้ชำระเงินแล้ว)</font>
								</td>
							</tr>
						</table>			
					</div>
			<?php
						}
					}
				}
			}
			?>
			<!--หุ้น-->
			<?php
				if(empty($loan_details)) {
			?>
			<div class="panel panel-body page-break" style="padding-top:10px !important;min-height: 1200px;">
				<table style="width: 80%;">
					<tr>
						<td style="width:40%;">&nbsp;</td>
						<td style="width:10%;">&nbsp;</td>
						<td style="width:10%;">&nbsp;</td>
						<td style="width:40%;vertical-align: top;" class="text-right">
							<?php if(@$key_letter==0){?>
							<a class="no_print" onclick="window.print();"><button class="btn btn-perview btn-after-input" type="button"><span class="icon icon-print" aria-hidden="true"></span></button></a>
							<?php } ?>
						</td>
					</tr>
					<tr>
						<td style="vertical-align: top;height: 55px;"></td>
						<td style="width:150px;vertical-align: top;text-align: center;"  colspan="2" rowspan="2">
							<img src="<?php echo base_url(PROJECTPATH.'/assets/images/coop_profile/'.@$row_profile['coop_img']); ?>" alt="Logo" style="width: 150px;" />
						</td>
						<td></td>
					</tr> 
					<tr>
						<td style="vertical-align: top;">
							<?php 
							$month_year_runno = sprintf("%02d",@$row_member['non_pay_month'])."-".@$row_member['non_pay_year']."/".@$row_member['non_pay_year'];
							$loan_runno = @$row_member['member_id']."(1)-".$month_year_runno;
							echo 'ที่ สอ.สป.(สินเชื่อ)    '.@$loan_runno;
							?>
							
						</td>
						<td style="vertical-align: top;padding-left: 20px;">
							<?php
								echo @$row_profile['coop_name_th'];
								echo '<br>';
								echo @$row_profile['address1']." ".@$row_profile['address2'];
							?> 
						 </td>
					</tr> 
					<tr>
						<td colspan="4">&nbsp;</td>
					</tr> 
					<tr>
						<td class="text-center" colspan="4">
							<?php
								$non_pay_year = $row_member['non_pay_year'] - 543;
								$letter_date_title = date("Y-m-t", strtotime($non_pay_year."-".$row_member['non_pay_month']."-01"));
								echo @$this->center_function->ConvertToThaiDate(@$letter_date_title,0,0);
							?>
						</td>
					</tr> 
					<tr>
						<td colspan="4">&nbsp;</td>
					</tr> 
					<tr>
						<td colspan="4">
							เรื่อง การค้างชำระค่าหุ้นรายเดือน	
						</td>
					</tr>
					<tr>
						<td colspan="4">&nbsp;</td>
					</tr> 
					<tr>
						<td colspan="4">
							เรียน <?php echo @$row_member['full_name']." (".@$row_member['member_id'].")";?>
						</td>
					</tr>
					<tr>
						<td colspan="4">&nbsp;</td>
					</tr> 
					<tr>
						<td colspan="4">
							<?php echo "อ้างถึง ข้อบังคับสหกรณ์ออมทรัพย์ครูสมุทรปราการ จำกัด พ.ศ.2555 หมวด 5 ข้อ 42 (2)";?>
						</td>
					</tr> 
					<tr>
						<td colspan="4">&nbsp;</td>
					</tr> 
					<tr>
						<td colspan="4">
							<?php

								$tab8 = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";	
								$tab4 = "&nbsp;&nbsp;&nbsp;&nbsp;";	

								if(!empty($check_share_dept)){
									$end_month = "วันสิ้นเดือน ".$non_pay_month_share." นั้น";
									$text_reason = "<br><br>".$tab8."เนื่องจากทางสหกรณ์ฯได้รับชำระค่าหุ้นจากท่านบางส่วน เป็นจำนวน ".number_format($share['share_paid'],2)
													."บาท และยังขาดอีกเป็นจำนวน ".number_format($share['share'] - $share['share_paid'],2)." บาท";
								}else {
									$month_debt = '';
									$index = 0;
									foreach($share_list AS $key=>$value){
										if($index > 0) {
											$month_debt .=", ";
										}
										$month_debt .= $value['mm_yy'];
										$index++;
									}
									$end_month = "วันสิ้นเดือนนั้น";
									$text_reason = $tab4."เนื่องจากท่านได้ผิดนัดการส่งหุ้นรายเดือนในเดือน".@$month_debt." รวม "
													.$index." งวด เป็นจำนวนเงิน "
													.number_format($share['share'] - $share['share_paid'],2)." บาท รายละเอียดปรากฏดังนี้";
								}
								
								$detail3 = "";
								$detail3 .= $tab8."ตามที่ท่านสมาชิกราย ".@$row_member['full_name'];
								$detail3 .= " ถือหุ้นรายเดือนในอัตราเดือนละ ".number_format(@$row_member['share_month'],2)." บาทโดยมีกำหนดชำระเป็นงวดรายเดือนภายใน".@$end_month;
								$detail3 .= @$text_reason;
								$detail3 .= "<br>";	
								
								if(empty($check_share_dept)){
									$detail3 .= "<br>";	
									//ตาราง
									$detail3 .= '
											<table border="1" width="100%">
												<tr>
													<td style="text-align: center;">เดือน</td>
													<td style="text-align: center;width: 10%;">ค่าหุ้น</td>
													<td style="text-align: center;width: 12%;">ค่าธรรมเนียม</td>
													<td style="text-align: center;width: 10%;">เงินต้นสามัญ</td>
													<td style="text-align: center;width: 10%;">ดอกเบี้ยสามัญ</td>
													<td style="text-align: center;width: 10%;">เงินต้นฉุกเฉิน</td>
													<td style="text-align: center;width: 10%;">ดอกเบี้ยฉุกเฉิน</td>
													<td style="text-align: center;width: 10%;">รวม</td>
												</tr>';
												
									if(!empty($share_list)){		
										foreach($share_list AS $key=>$value){
											$detail3 .= '<tr>
															<td style="text-align: center;">'.@$value['mm_yy'].'</td>
															<td style="text-align: right;">'.number_format($value['principal_debt'],2).'</td>
															<td style="text-align: center;">-</td>
															<td style="text-align: center;">-</td>
															<td style="text-align: center;">-</td>
															<td style="text-align: center;">-</td>
															<td style="text-align: center;">-</td>
															<td style="text-align: right;">'.number_format($value['principal_debt'],2).'</td>
														</tr>';
										}
									}			
									$detail3 .= '</table>';						
								
								}
								
								$detail3 .= "<br>";	
								$detail3 .= $tab8."ดังนั้นเพื่อให้การดำเนินงานเป็นไปตามข้อบังคับของสหกรณ์ฯจึงขอให้ท่านรีบชำระค่าหุ้นรายเดือนจำนวนดังกล่าวภายใน 15 วันนับตั้งแต่ได้รับหนังสือหรือถือว่าได้รับหนังสือฉบับนี้ ";
								$detail3 .= "เพื่อรักษาประวัติสมาชิก หากท่านขาดชำระค่าหุ้นรายเดือนถึง 3 งวดติดต่อกันหรือขาดชำระรวมถึง 6 งวดโดยไม่ได้รับอนุญาตจากคณะกรรมการดำเนินการ ";
								$detail3 .= "อาจมีผลให้ท่านออกจากการเป็นสมาชิกสหกรณ์ฯตามข้อบังคับที่อ้างถึง";
								echo @$detail3;
							?>
						</td>
					</tr> 
					<tr>
						<td colspan="4">&nbsp;</td>
					</tr> 
					<tr>
						<td colspan="4">
							<?php echo $tab8.$tab8; ?>
							จึงเรียนมาเพื่อทราบและดำเนินการต่อไป
						</td>
					</tr>
					<tr>
						<td colspan="4">&nbsp;</td>
					</tr> 
					<tr>
						<td colspan="4" style="text-align: center;">
							ขอแสดงความนับถือ
						</td>
					</tr> 
					<tr>
						<td colspan="4">&nbsp;</td>
					</tr> 
					<tr>
						<td colspan="4"  style="text-align: center;">							
							<?php echo @$img_signature_manager;?>
						</td>
					</tr> 
					<tr>
						<td colspan="4" style="text-align: center;">
							<?php echo "(".@$manager_name.")";?>
						</td>
					</tr> 
					<tr>
						<td colspan="4" style="text-align: center;">
							<?php echo @$manager_position;?>
						</td>
					</tr> 
					<tr>
						<td colspan="4" style="text-align: center;">
							<?php echo @$row_profile['coop_name_th'];?>
						</td>
					</tr>
					<tr>
						<td colspan="4">&nbsp;</td>
					</tr> 
					<tr>
						<td colspan="4">&nbsp;</td>
					</tr> 
					<tr>
						<td colspan="4">
							ฝ่ายสินเชื่อ<br>
							โทร. 02-3842493-4 ต่อ 24<br>
							โทรสาร. 02-3842495<br><br>
							<font style="font-size: 12px;">(หากการชำระเงินของท่าน สวนทางกับหนังสือฉบับนี้ ต้องกราบขออภัยเป็นอย่างสูง กรุณาแจ้งหรือส่งหลักฐานการชำระเงินกลับในกรณีที่ได้ชำระเงินแล้ว)</font>
						</td>
					</tr> 
				</table>			
			</div>
			<?php
					}
				} 
				}
			?>
		</div>