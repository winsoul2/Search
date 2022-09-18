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
					$month_year_runno = sprintf("%02d",$meeting_month)."-".$meeting_year."/".$meeting_year."อ";
					$loan_runno = $member_id."(1)-".$month_year_runno;
					echo 'ที่ สอ.สป.(สินเชื่อ)    '.$loan_runno;
				?>
				</td>
				<td style="vertical-align: top;padding-left: 20px;">
					<?php
						echo $row_profile['coop_name_th'];
						echo '<br>';
						echo $row_profile['address1']." ".$row_profile['address2'];
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
						$loan_balance = 0;
						$loan_principal_payment = 0;
						foreach($data["loans"] as $contract_number=>$detail) {
							if($loan_index == 1) {
								echo "อ้างถึง ".$loan_index.". สัญญา".$detail['loan_type']."เลขที่ ".$contract_number." ลงวันที่ ".$this->center_function->ConvertToThaiDate($detail['approve_date'],0,0);
							} else {
								echo "<br/>".$tab8.$loan_index." สัญญา".$detail['loan_type']."เลขที่ ".$contract_number." ลงวันที่ ".$this->center_function->ConvertToThaiDate($detail['approve_date'],0,0);
							}
							$loan_balance += $detail["balance"];
							$loan_principal_payment += $detail["principal_payment"];
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
								$loan_debt_period_text = $month_arr[$loan["period"][0]['month']]." ".$loan["period"][0]['year'];
								$loan_debt_period_text .= " - ".$month_arr[$loan["period"][$debt_debt_period_count - 1]['month']]." ".$loan["period"][$debt_debt_period_count - 1]['year'];
								$loan_index++;
							}
						}
						$detail1 = "";
						$detail1 .= $tab8."ตามที่ท่านได้กู้เงินจากสหกรณ์ออมทรัพย์ครูสมุทรปราการ จำกัดตามสัญญาเงินกู้ที่".$loan_ref_text." นั้น";
						$detail1 .= "<br>";
						$detail1 .= $tab8."เนื่องจากท่านได้ผิดนัดการส่งเงินงวดชำระหนี้ ".$debt_debt_period_count." งวดติดต่อกัน (".$loan_debt_period_text.")";

						//Share
						// if(!empty($data["share"]) && !empty($data["share"]["over_limit"])) {
						// 	$debt_period = "";
						// 	$debt_period_count = count($data["share"]["period"]);
						// 	$debt_period = $month_arr[$data["share"]["period"][0]['month']]." ".$data["share"]["period"][0]['year'];
						// 	$debt_period .= " - ".$month_arr[$data["share"]["period"][$debt_period_count - 1]['month']]." ".$data["share"]["period"][$debt_period_count - 1]['year'];
						// 	$detail1 .= " ท่านได้ผิดนัดการส่งเงินค่าหุ้นรายเดือนถึง ".$debt_period_count." งวดติดต่อกัน (".$debt_period.")";
						// }

						$detail1 .= " ซึ่งเกินกว่าระยะเวลาที่ข้อบังคับสหกรณ์ฯ กำหนด";
						$detail1 .= " คณะกรรมการดำเนินการ ชุดที่ ".$committee_group." ในการประชุมครั้งที่ ".$agenda." เมื่อวันที่ ".$this->center_function->ConvertToThaiDate(($meeting_year-543)."-".$meeting_month."-".$meeting_day." 00:00:00",0,0);
						$detail1 .= " จึงมีมติให้ท่านออกจากการเป็นสมาชิกสหกรณ์ฯ โดยให้ดำเนินการโอนหุ้นชำระหนี้";
						$detail1 .= " หนี้ และสหกรณ์ฯ ได้ตรวจสอบข้อมูลแล้ว ปรากฏว่า ณ วันที่ ".$this->center_function->ConvertToThaiDate($data["resign_approve_date"],0,0);
						$detail1 .= " ท่านมีหนี้เงินกู้สามัญจำนวน ".number_format($loan_balance,2)." บาท";
						$detail1 .= " โดยท่านมีหุ้นสะสมจำนวน ".number_format($data["share"]["balance"],2)." บาท";
						$detail1 .= " และบัญชีเงินฝาก ".number_format($data["account"]["balance"],2)." บาท";
						$detail1 .= " โดยชำระหนี้เป็นเงินต้น จำนวน ".number_format($loan_principal_payment,2)." บาท ";
						$detail1 .= " และดอกเบี้ยจำนวน ".number_format($data["interest_paid"],2)." บาท ";
						$detail1 .= " และดอกเบี้ยคงค้างจำนวน ".number_format($data["interest_debt_paid"],2)." บาท ";
						$detail1 .= " ภายหลังจากโอนหุ้นชำระหนี้ท่านมีหนี้คงเหลือจำนวน ".number_format($data["debt"],2)." บาท ";

						$detail1 .= "<br>";
						$detail1 .= $tab8."ดังนั้นสหกรณ์ฯ จึงขอให้ท่านชำระหนี้จำนวน ".number_format($data["debt"],2)." บาท พร้อมดอกเบี้ย ภายใน 20 วัน นับตั้งแต่ได้รับหนังสือถือว่าได้รับหนังสือฉบับนี้  ";
						$detail1 .= "หากท่านยังคงเพิกเฉยหรือพยายามหลีกเลี่ยงไม่ชำระหนี้ตามที่สหกรณ์ฯ เรียกเก็บ สหกรณ์ฯ จะดำเนินการตามกฎหมาย และเรียกเก็บเงินชำระหนี้จากผู้ค้ำประกัน";

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
				โทร. 02-3842493-4 ต่อ 24<br>
				โทรสาร. 02-3842495<br><br>
				<font style="font-size: 12px;">(หากการชำระเงินของท่าน สวนทางกับหนังสือฉบับนี้ ต้องกราบขออภัยเป็นอย่างสูง กรุณาแจ้งหรือส่งหลักฐานการชำระเงินกลับในกรณีที่ได้ชำระเงินแล้ว)</font>
			</td>
			</tr> 
		</table>
	</div>
	<?php
		if(!empty($data["guarantee"])) {
			$n = 1;
			foreach($data["guarantee"] as $guarantee) {
				$contract_number = $guarantee["contract_number"];
				$loan = $data["loans"][$contract_number];
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
					$loan_runno = $member_id."(".++$n.")-".$month_year_runno;
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
					เรียน <?php echo $guarantee['prename_full'].$guarantee['firstname_th']." ".$guarantee["lastname_th"]." (".$guarantee["member_id"].")";?>
				</td>
			</tr>
			<tr>
				<td colspan="4">&nbsp;</td>
			</tr>
			<tr>
				<td colspan="4">
					<?php
						// echo "อ้างถึงสัญญา".$loan['loan_type']."เลขที่ ".$loan['contract_number'];
						echo "อ้างถึง 1. สัญญาค้ำประกัน".$loan['loan_type']."เลขที่ ".$loan['contract_number']." ลงวันที่ ".$this->center_function->ConvertToThaiDate($loan['approve_date'],0,0);
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
						// foreach($data["loans"] as $contract_number=>$loan) {
							if(!empty($loan["over_limit"])) {
								$loan_ref_text .= "เป็นจำนวนเงิน ".number_format($loan['balance'],2)." บาท (".$this->center_function->convert($loan['balance']).") ";
								$debt_debt_period_count = count($loan["period"]);
								$loan_debt_period_text = $month_arr[$loan["period"][0]['month']]." ".$loan["period"][0]['year'];
								$loan_debt_period_text .= " - ".$month_arr[$loan["period"][$debt_debt_period_count - 1]['month']]." ".$loan["period"][$debt_debt_period_count - 1]['year'];
								$loan_index++;
							}
						// }
						$detail1 = "";
						$detail1 .= $tab8."ตามที่ท่านได้ค้ำประกันเงินกู้สามัญราย ".$data["name"]." สมาชิกสหกรณ์ออมทรัพย์ครูสมุทรปราการ จำกัด ตามหนังสือค้ำประกันที่อ้างถึง  ".$loan_ref_text." นั้น";
						$detail1 .= "<br>";
						$detail1 .= $tab8."เนื่องจากผู้กู้ได้ผิดนัดการส่งเงินงวดชำระหนี้ถึง ".$debt_debt_period_count." งวดติดต่อกัน (".$loan_debt_period_text.")";
						$detail1 .= " ซึ่งเกินกว่าระยะเวลาที่ข้อบังคับสหกรณ์ฯ กำหนด";
						$detail1 .= " คณะกรรมการดำเนินการ ชุดที่ ".$committee_group." ในการประชุมครั้งที่ ".$agenda." เมื่อวันที่ ".$this->center_function->ConvertToThaiDate(($meeting_year-543)."-".$meeting_month."-".$meeting_day." 00:00:00",0,0);
						$detail1 .= " จึงมีมติให้ผู้กู้ออกจากการเป็นสมาชิกสหกรณ์ฯ โดยให้ดำเนินการโอนหุ้นชำระหนี้";
						$detail1 .= " หนี้ และสหกรณ์ฯ ได้ตรวจสอบข้อมูลแล้ว ปรากฏว่า ณ วันที่ ".$this->center_function->ConvertToThaiDate($data["resign_approve_date"],0,0);
						$detail1 .= " ผู้กู้มีหนี้เงินกู้สามัญจำนวน ".number_format($loan_balance,2)." บาท";
						$detail1 .= " โดยผู้กู้มีหุ้นสะสมจำนวน ".number_format($data["share"]["balance"],2)." บาท";
						$detail1 .= " และบัญชีเงินฝาก ".number_format($data["account"]["balance"],2)." บาท";
						$detail1 .= " โดยชำระหนี้เป็นเงินต้น จำนวน ".number_format($loan_principal_payment,2)." บาท ";
						$detail1 .= " และดอกเบี้ยจำนวน ".number_format($data["interest_paid"],2)." บาท ";
						$detail1 .= " และดอกเบี้ยคงค้างจำนวน ".number_format($data["interest_debt_paid"],2)." บาท ";
						$detail1 .= " ภายหลังจากโอนหุ้นชำระหนี้ผู้กู้มีหนี้คงเหลือจำนวน ".number_format($data["debt"],2)." บาท ";

						$detail1 .= "<br>";
						$detail1 .= $tab8."ดังนั้น สหกรณ์ฯ จึงขอให้ท่านในฐานะผู้ค้ำประกัน ชำระเงินจำนวน ".number_format($data["debt"],2)." บาท พร้อมดอกเบี้ยให้แก่สหกรณ์ ภายใน 20 วัน";
						$detail1 .= " นับตั้งแต่ได้รับหนังสือหรือถือว่าได้รับหนังสือฉบับนี้ หากพ้นกำหนดดังกล่าว สหกรณ์ฯ มีความจำเป็นต้องดำเนินการกับท่านตามกฎหมาย ต่อไป";

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
					$month_year_runno = sprintf("%02d",$meeting_month)."-".$meeting_year."/".$meeting_year."อ";
					$loan_runno = $member_id."(1)-".$month_year_runno;
					echo 'ที่ สอ.สป.(สินเชื่อ)    '.$loan_runno;
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
					<?php echo $this->center_function->ConvertToThaiDate($print_date,0,0);?>
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
						// foreach($data["share"]["period"] as $period) {
						// 	if($debt_period != "") {
						// 		$debt_period .= " , ";
						// 	}
						// 	$debt_period .= $month_arr[$period['month']]." ".$period['year'];
						// }
						$debt_period = $month_arr[$data["share"]["period"][0]['month']]." ".$data["share"]["period"][0]['year'];
						$debt_period .= " - ".$month_arr[$data["share"]["period"][$debt_period_count - 1]['month']]." ".$data["share"]["period"][$debt_period_count - 1]['year'];

						$detail1 .= $tab8."เนื่องจากท่านได้ผิดนัดการส่งเงินค่าหุ้นรายเดือนถึง ".$debt_period_count." งวดติดต่อกัน (".$debt_period.") ซึ่งเกินกว่าระยะเวลาที่ข้อบังคับสหกรณ์ฯ กำหนด";
						$detail1 .= " คณะกรรมการดำเนินการ ชุดที่ ".$committee_group." ในการประชุมครั้งที่ ".$agenda." เมื่อวันที่ ".$this->center_function->ConvertToThaiDate(($meeting_year-543)."-".$meeting_month."-".$meeting_day." 00:00:00",0,0);
						$detail1 .= " จึงมีมติให้ท่านออกจากการเป็นสมาชิกสหกรณ์ฯ";
						$detail1 .= "<br>";
						$detail1 .= $tab8."ดังนั้น สหกรณ์ฯ จึงขอแจ้งให้ท่านทราบว่า สหกรณ์ฯ ได้ทำการโอนทุนเรือนหุ้นของท่านเข้าบัญชีธนาคารกรุงไทยของท่านแล้ว";

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
				<td colspan="4" class="text-right">
					รอง ผจก..............................................<br><br>
					หน.ฝ....................................................<br><br>
					จนท.....................................................
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