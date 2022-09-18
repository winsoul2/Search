<style>
	td{
		/*
		font-family: upbean;
		font-size: 20px;
		padding: 6px;
		*/
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
		$key_letter = 0;
		$img_signature_manager = '<img style="max-height: 35px;" src="'.base_url(PROJECTPATH.'/assets/images/coop_signature/'.@$signature['signature_3']).'" />';
		$manager_name = @$signature['manager_name'];
		$manager_position = "ผู้จัดการ";

		foreach($datas as $member_id => $data) {
			if (!empty($data["has_loan"])) {

	?>
	<!--Loan-->
	<div class="panel panel-body" style="padding-top:10px !important;min-height: 1200px;">
		<table style="width: 80%;">
			<tr>
				<td style="width:400px;">&nbsp;</td>
				<td style="width:110px;">&nbsp;</td>
				<td style="width:110px;">&nbsp;</td>
				<td style="width:400px;vertical-align: top;" class="text-right">
				<?php if($key_letter==0){?>
					<a class="no_print" onclick="window.print();"><button class="btn btn-perview btn-after-input" type="button"><span class="icon icon-print" aria-hidden="true"></span></button></a>
				<?php } ?>
				</td>
			</tr>
			<tr>
				<td style="vertical-align: top;height: 55px;"></td>
				<td style="width:150px;vertical-align: top;text-align: center;"  colspan="2" rowspan="2">
					<img src="<?php echo base_url(PROJECTPATH.'/assets/images/coop_profile/'.$row_profile['coop_img']); ?>" alt="Logo" style="width: 150px;" />
				</td>
				<td></td>
			</tr>
			<tr>
				<td style="vertical-align: top;">
				<?php
					$month_year_runno = sprintf("%02d",date("m"))."-".(date(Y) + 543)."/".(date(Y) + 543);
					$loan_runno = $member_id."(1)-".$month_year_runno;
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
				<td></td>
				<td></td>
				<td colspan="2">
					<?php echo $this->center_function->ConvertToThaiDate(date('Y-m-d H:i:s'),0,0);?>
				</td>
			</tr>
			<tr>
				<td colspan="4">&nbsp;</td>
			</tr> 
			<tr>
				<td colspan="4">
					เรื่อง แจ้งหนี้ค้างชำระ	
				</td>
			</tr>
			<tr>
				<td colspan="4">&nbsp;</td>
			</tr> 
			<tr>
				<td colspan="4">
					เรียน <?php echo $data['name']." (".$member_id.")";?>
				</td>
			</tr>
			<tr>
				<td colspan="4">&nbsp;</td>
			</tr> 
			<tr>
				<td colspan="4">
					<?php
						$loan_index = 1;
						foreach($data["loans"] as $contract_number=>$detail) {
							if($loan_index == 1) {
								echo "อ้างถึง ".$loan_index.". สัญญา".@$detail['loan_type']."เลขที่ ".@$contract_number;
							} else {
								echo "<br/>".$tab8.$loan_index." สัญญา".@$detail['loan_type']."เลขที่ ".@$contract_number;
							}
							$loan_index++;
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

						$loan_ref_text = "";
						$loan_debt_period_text = "";
						$debt_debt_period_count = 0;
						$loan_index = 1;
						foreach($data["loans"] as $contract_number=>$loan) {
							if(!empty($loan["over_limit"])) {
								$loan_ref_text .= "อ้างถึง ".$loan_index." เป็นจำนวนเงิน ".number_format($loan['balance'],2)." บาท (".$this->center_function->convert($loan['balance']).") ";
								$debt_debt_period_count = count($loan["period"]);
								foreach($loan["period"] as $period) {
									if($loan_debt_period_text != "") {
										$loan_debt_period_text .= " , ";
									}
									$loan_debt_period_text .= $month_arr[$period['month']]." ".$period['year'];
								}
								$loan_index++;
							}
						}
						$detail1 = "";
						$detail1 .= $tab8."ตามที่ท่านได้กู้เงินจากสหกรณ์ออมทรัพย์ครูสมุทรปราการ จำกัดตามสัญญาเงินกู้ที่".$loan_ref_text." นั้น";
						$detail1 .= "<br>";
						$detail1 .= $tab8."เนื่องจากท่านได้ผิดนัดการส่งเงินงวดชำระหนี้ ".$debt_debt_period_count." งวดติดต่อกัน (".$loan_debt_period_text.")";

						if(!empty($data["share"]) && !empty($data["share"]["over_limit"])) {
							$debt_period = "";
							$debt_period_count = count($data["share"]["period"]);
							foreach($data["share"]["period"] as $period) {
								if($debt_period != "") {
									$debt_period .= " , ";
								}
								$debt_period .= $month_arr[$period['month']]." ".$period['year'];
							}
							$detail1 .= " ท่านได้ผิดนัดการส่งเงินค่าหุ้นรายเดือนถึง ".$debt_period_count." งวดติดต่อกัน (".$debt_period.")";
						}

						$detail1 .= " ซึ่งเกินกว่าระยะเวลาที่ข้อบังคับสหกรณ์ฯ กำหนด";

						$detail1 .= "<br>";
						$detail1 .= $tab8."ดังนั้นคณะกรรมการดำเนินการได้มีมติให้ท่านออกจากการเป็นสมาชิก โดยท่านมีสิทธิ์ยื่นอุทธรณ์ต่อคณะกรรมการดำเนินการภายใน 30 วัน นับแต่วันที่ท่านได้รับหนังสือ  ";
						$detail1 .= "หรือเสมือนได้รับหนังสือฉบับนี้ หากพ้นกำหนดดังกล่าวสหกรณ์ฯ มีความจำเป็นต้องเสนอให้คณะกรรมการดำเนินการพิจารณาท่านพ้นสมาชิกภาพตามข้อบังคับและดำเนินการตามกฎหมายกับท่านต่อไป";

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
				<td colspan="4">&nbsp;</td>
			</tr> 
			<tr>
				<td colspan="4">
					ฝ่ายนิติกร<br>
					โทร 02-3842493-4 ต่อ24<br>
				</td>
			</tr> 
		</table>
	</div>
	<?php
		foreach($data["guarantee"] as $contract_number => $guarantee) {
	?>
	<div class="panel panel-body" style="padding-top:10px !important;min-height: 1200px;">
		<table style="width: 80%;">
			<tr>
				<td style="width:400px;">&nbsp;</td>
				<td style="width:110px;">&nbsp;</td>
				<td style="width:110px;">&nbsp;</td>
				<td style="width:400px;vertical-align: top;" class="text-right">
				</td>
			</tr>
			<tr>
				<td style="vertical-align: top;height: 55px;"></td>
				<td style="width:150px;vertical-align: top;text-align: center;"  colspan="2" rowspan="2">
					<img src="<?php echo base_url(PROJECTPATH.'/assets/images/coop_profile/'.$row_profile['coop_img']); ?>" alt="Logo" style="width: 150px;" />
				</td>
				<td></td>
			</tr>
			<tr>
				<td style="vertical-align: top;">
				<?php
					$month_year_runno = sprintf("%02d",date("m"))."-".(date(Y) + 543)."/".(date(Y) + 543);
					$loan_runno = $member_id."(2)-".$month_year_runno;
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
				<td></td>
				<td></td>
				<td colspan="2">
					<?php echo $this->center_function->ConvertToThaiDate(date('Y-m-d H:i:s'),0,0);?>
				</td>
			</tr>
			<tr>
				<td colspan="4">&nbsp;</td>
			</tr>
			<tr>
				<td colspan="4">
					เรื่อง แจ้งมติที่ประชุมคณะกรรมการดำเนินการให้สมาชิกออกจากสหกรณ์
				</td>
			</tr>
			<tr>
				<td colspan="4">&nbsp;</td>
			</tr>
			<tr>
				<td colspan="4">
					เรียน <?php echo $guarantee['name']." (".$guarantee["member_id"].")";?>
				</td>
			</tr>
			<tr>
				<td colspan="4">&nbsp;</td>
			</tr>
			<tr>
				<td colspan="4">
					<?php
						echo "อ้างถึงสัญญา".$guarantee['loan_type']."เลขที่ ".$guarantee['contract_number'];
					?>
				</td>
			</tr> 
			<tr>
				<td colspan="4">&nbsp;</td>
			</tr>
			<tr>
				<td colspan="4">
					<?php
						$loan_ref_text .= "อ้างถึง 1 เป็นจำนวนเงิน ".number_format($loan['balance'],2)." บาท (".$this->center_function->convert($loan['balance']).") ";
						$debt_debt_period_count = count($data["loans"][$guarantee["contract_number"]]["period"]);
						foreach($data["loans"][$guarantee["contract_number"]]["period"] as $period) {
							if($loan_debt_period_text != "") {
								$loan_debt_period_text .= " , ";
							}
							$loan_debt_period_text .= $month_arr[$period['month']]." ".$period['year'];
						}

						$detail1 = "";
						$detail1 .= $tab8."ตามหนังสือที่อ้างถึง ท่านได้ตกลงยอมผูกพันตนค้ำประกันการกู้เงินของ ".$data["name"]." กับสหกรณ์ออมทรัพย์ครูสมุทรปราการ จำกัด";
						$detail1 .= " ตามสัญญาเงินกู้ที่อ้างถึงจำนวน ".number_format($guarantee["balance"],2)." บาท (".$this->center_function->convert($guarantee["balance"]).") นั้น";
						$detail1 .= "<br>";
						$detail1 .= $tab8."เนื่องจากผู้กู้ได้ผิดนัดการส่งเงินงวดชำระหนี้ถึง ".$debt_debt_period_count." งวด (".$loan_debt_period_text.")";
						$detail1 .= " ซึ่งเกินกว่าระยะเวลาที่ข้อบังคับสหกรณ์ฯกำหนด คณะกรรมการดำเนินการจึงมีมติให้ผู้กู้ออกจากการเป็นสมาชิกสหกรณ์ฯ";
						$detail1 .= " โดยให้ผู้กู้มีสิทธิ์ยื่นอุทธรณ์ต่อคณะกรรมการดำเนินการภายใน 30 วัน นับแต่วันที่ผู้กู้ได้รับหนังสือ หากพ้นกำหนดดังกล่าวแล้ว สหกรณ์จะดำเนินการโอนหุ้นตัดหนี้ผู้กู้และเรียกเก็บเงินกับท่านในฐานะผู้ค้ำประกันต่อไป";

						$detail1 .= "<br>";
						$detail1 .= $tab8."ดังนั้น สหกรณ์ จึงขอให้ท่านในฐานะผู้ค้ำประกัน ติดต่อสหกรณ์ฯ ภายใน 30 วัน นับตั้งแต่ได้รับหนังสือหรือถือว่าได้รับหนังสือฉบับนี้ หากพ้นกำหนดดังกล่าว ";
						$detail1 .= "สหกรณ์ฯมีความจำเป็นต้องเรียกเก็บเงินกับท่านและดำเนินการกับท่านตามกฎหมาย ต่อไป";
						$detail1 .= "<br>";
						$detail1 .= $tab8."อนึ่ง หากท่านไม่โต้แย้งภายในระยะเวลาตามที่กำหนด สหกรณ์ถือว่าท่านมิใช้สิทธิ์โต้แย้งในการเรียกเก็บเงินกับท่านในฐานะผู้ค้ำประกัน";
						$detail1 .= " และทางสหกรณ์จะดำเนินการหักเงินท่านเพื่อชำระหนี้ในฐานะผู้ค้ำประกันในเดือนถัดไป จนกว่าจะชำระเสร็จสิ้น";

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
				<td colspan="4">&nbsp;</td>
			</tr>
			<tr>
				<td colspan="4">
					ฝ่ายนิติกร<br>
					โทร 02-3842493-4 ต่อ24<br>
				</td>
			</tr> 
		</table>
	</div>
	<?php
				}
			} else {
	?>
	<div class="panel panel-body" style="padding-top:10px !important;min-height: 1200px;">
		<table style="width: 80%;">
			<tr>
				<td style="width:400px;">&nbsp;</td>
				<td style="width:110px;">&nbsp;</td>
				<td style="width:110px;">&nbsp;</td>
				<td style="width:400px;vertical-align: top;" class="text-right">
				<?php if($key_letter==0){?>
					<a class="no_print" onclick="window.print();"><button class="btn btn-perview btn-after-input" type="button"><span class="icon icon-print" aria-hidden="true"></span></button></a>
				<?php } ?>
				</td>
			</tr>
			<tr>
				<td style="vertical-align: top;height: 55px;"></td>
				<td style="width:150px;vertical-align: top;text-align: center;"  colspan="2" rowspan="2">
					<img src="<?php echo base_url(PROJECTPATH.'/assets/images/coop_profile/'.$row_profile['coop_img']); ?>" alt="Logo" style="width: 150px;" />
				</td>
				<td></td>
			</tr>
			<tr>
				<td style="vertical-align: top;">
				<?php
					$month_year_runno = sprintf("%02d",date("m"))."-".(date(Y) + 543)."/".(date(Y) + 543);
					$loan_runno = $member_id."(1)-".$month_year_runno;
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
				<td></td>
				<td></td>
				<td colspan="2">
					<?php echo $this->center_function->ConvertToThaiDate(date('Y-m-d H:i:s'),0,0);?>
				</td>
			</tr>
			<tr>
				<td colspan="4">&nbsp;</td>
			</tr>
			<tr>
				<td colspan="4">
					เรื่อง แจ้งค้างชำระค่าหุ้น
				</td>
			</tr>
			<tr>
				<td colspan="4">&nbsp;</td>
			</tr>
			<tr>
				<td colspan="4">
					เรียน <?php echo $data['name']." (".$member_id.")";?>
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
					<?php

						$detail1 = "";
						$detail1 .= $tab8."ตามระเบียบสหกรณ์ออมทรัพย์ครูสมุทรปราการ จำกัด ว่าด้วยคุณสมบัติวิธีรับสมาชิกและขาดสมาชิกภาพ พ.ศ.2555 หมวด 3 การขาดสมาชิกภาพ ข้อ 13 (5) ";
						$detail1 .= "สมาชิกที่ค้างส่งเงินงวดชำระหนี้ไม่ว่าเงินต้น ดอกเบี้ยหรือหุ้นรายเดือน ติดต่อกันเป็นระยะเวลาสามเดือน นั้นถือว่าท่านได้ผิดคุณสมบัติของสหกรณ์ที่กำหนดไว้";
						$detail1 .= "<br>";

						$debt_period = "";
						$debt_period_count = count($data["share"]["period"]);
						foreach($data["share"]["period"] as $period) {
							if($debt_period != "") {
								$debt_period .= " , ";
							}
							$debt_period .= $month_arr[$period['month']]." ".$period['year'];
						}

						$detail1 .= $tab8."เนื่องจากท่านได้ผิดนัดการส่งเงินค่าหุ้นรายเดือนถึง ".$debt_period_count." งวดติดต่อกัน (".$debt_period.") ซึ่งเกินกว่าระยะเวลาที่ข้อบังคับสหกรณ์ฯ กำหนด";
						$detail1 .= "<br>";
						$detail1 .= $tab8."ดังนั้นคณะกรรมการดำเนินการได้มีมติให้ท่านออกจากการเป็นสมาชิก โดยท่านมีสิทธิ์ยื่นอุทธรณ์ต่อคณะกรรมการดำเนินการภายใน 30 วัน นับแต่วันที่ท่านได้รับหนังสือ  ";
						$detail1 .= "หรือเสมือนได้รับหนังสือฉบับนี้ หากพ้นกำหนดดังกล่าวสหกรณ์ฯ มีความจำเป็นต้องเสนอให้คณะกรรมการดำเนินการพิจารณาท่านพ้นสมาชิกภาพตามข้อบังคับและดำเนินการตามกฎหมายกับท่านต่อไป";

						echo $detail1;
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
					ฝ่ายนิติกร<br>
					โทร 02-3842493-4 ต่อ24<br>
				</td>
			</tr>
		</table>
	</div>
	<?php
				$key_letter++;
			}
		}
	?>
</div>