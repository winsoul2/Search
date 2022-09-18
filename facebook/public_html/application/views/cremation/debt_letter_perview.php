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
		$img_signature_manager = '<img style="max-height: 35px;" src="'.base_url(PROJECTPATH.'/assets/images/coop_signature/'.@$signature['signature_3']).'" />';
		$manager_name = @$signature['manager_name'];
		$manager_position = "ผู้จัดการ";
		foreach($datas AS $key_letter=>$data){

	?>
	<div class="panel panel-body" style="padding-top:10px !important;min-height: 1200px;">
		<table style="width: 80%;">
			<tr>
				<td style="width:400px;">&nbsp;</td>
				<td style="width:110px;">&nbsp;</td>
				<td style="width:110px;">&nbsp;</td>
					<td style="width:400px;vertical-align: top;" class="text-right">
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
					echo 'ที่ สอ.สป.(ฌาปนกิจ) 4-(5) /'.@$data["non_pay_year"];
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
						echo @$this->center_function->ConvertToThaiDate($data['date'],0,0);
					?>
				</td>
			</tr> 
			<tr>
				<td colspan="4">
					เรื่อง 	แจ้งหนี้ค้างชำระเงินสงเคราะห์ศพ
				</td>
			</tr>
			<tr>
				<td colspan="4">
					เรียน <?php echo $data['prename_full'].$data['firstname_th']." ".$data['lastname_th']." (".$data['member_cremation_id'].")";?>
				</td>
			</tr>
			<tr>
				<td colspan="4">
					<?php if(empty($data["debt_year"])) { ?>
						<?php echo $tab8;?>ตามที่สมาคมฌาปนกิจสหกรณ์ออมทรัพย์สถาบันวิจัยวิทยาศาสตร์และเทคโนโลยีแห่งประเทศไทย จำกัด ได้เรียกเก็บเงินสงเคราะห์ศพสมาชิกเสียชีวิต จำนวน <?php echo count($data['cremation_receivers'])?> ราย
						เป็นเงิน <?php echo number_format($data['total'],2)?> บาท (<?php echo $this->center_function->convert($data['total'])?>) รวมไปกับใบแจ้งหนี้ของสมาคมฌาปนกิจสหกรณ์ออมทรัพย์สถาบันวิจัยวิทยาศาสตร์และเทคโนโลยีแห่งประเทศไทย จำกัด
						ประจำเดือน <?php echo $month_arr[(int) $data["non_pay_month"]]." ".$data["non_pay_year"]?> นั้น ปรากกฎว่าท่านยังไม่ได้ชำระเงินดังกล่าวให้แก่สมาคมฯ
					<?php } else { ?>
						<?php echo $tab8;?>ตามที่สมาคมฌาปนกิจสหกรณ์ออมทรัพย์สถาบันวิจัยวิทยาศาสตร์และเทคโนโลยีแห่งประเทศไทย จำกัด ได้เรียกเก็บเงินสงเคราะห์ศพสมาชิกเสียชีวิต
						เป็นเงิน <?php echo number_format($data['total'],2)?> บาท (<?php echo $this->center_function->convert($data['total'])?>)
						ประจำปี <?php echo $data["debt_year"]?> นั้น ปรากกฎว่าท่านยังไม่ได้ชำระเงินดังกล่าวให้แก่สมาคมฯ
					<?php } ?>
				</td>
			</tr>
			<?php if(!empty($data['cremation_receivers'])) { ?>
			<tr>
				<td colspan="4">&nbsp;</td>
			</tr> 
			<tr>
				<td colspan="4">
				<?php
					foreach($data['cremation_receivers'] as $key => $receiver) {
						echo $key > 0 ? "<br>".$tab8.$tab8.$tab8.$tab8 : $tab8.$tab8.$tab8.$tab8;
						echo ($key + 1).".".$receiver["prename_full"].$receiver["assoc_firstname"]." ".$receiver["assoc_lastname"]." ผู้เสียชีวิต";
					}
				?>
				</td>
			</tr>
			<tr>
				<td colspan="4">&nbsp;</td>
			</tr> 
				<?php } ?>

			<tr>
				<td colspan="4">
					<?php echo $tab8;?>ฉะนั้น ข้าพเจ้าในฐานะผู้รับมอบอำนาจจึงขอให้ท่านนำเงินดังกล่าวมาชำระให้แก่สมาคมฯภายใน 15 วัน นับแต่วันได้รับหนังสือหรือถือว่าได้รับหนังสือฉบับนี้
					หากพ้นกำหนดดังกล่าว สมาคมฯ จะดำเนินการเสนอให้คณะกรรมการพิจารณาท่านพ้นสมาชิกภาพตามข้อบังคับต่อไป
				</td>
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
				<td colspan="4">
					งานสมาคมฌาปนกิจ สสคอ.<br>
					โทร 0-2577-0989
				</td>
			</tr> 
		</table>
	</div>
	<?php } ?>
</div>