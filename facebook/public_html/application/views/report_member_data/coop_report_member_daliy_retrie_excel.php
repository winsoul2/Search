<?php 
header("Content-type: application/vnd.ms-excel;charset=utf-8;");
header("Content-Disposition: attachment; filename=รายงานสมาชิกลาออกรายวัน.xls"); 
date_default_timezone_set('Asia/Bangkok');
?>
<pre>
	<html>
		<head>
			<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
			<style>
				.num {
				  mso-number-format:General;
				}
				.text{
				  mso-number-format:"\@";/*force text*/ 
				}
				.text-center{
					text-align: center;
				}
				.text-left{
					text-align: left;
				}
				.table_title{
					font-family: AngsanaUPC, MS Sans Serif;
					font-size: 22px;
					font-weight: bold;
					text-align:center;
				}
				.table_title_right{
					font-family: AngsanaUPC, MS Sans Serif;
					font-size: 16px;
					font-weight: bold;
					text-align:right;
				}
				.table_header_top{
					font-family: AngsanaUPC, MS Sans Serif;
					font-size: 19px;
					font-weight: bold;
					text-align:center;
					border-top: thin solid black;
					border-left: thin solid black;
					border-right: thin solid black;
				}
				.table_header_mid{
					font-family: AngsanaUPC, MS Sans Serif;
					font-size: 19px;
					font-weight: bold;
					text-align:center;
					border-left: thin solid black;
					border-right: thin solid black;
				}
				.table_header_bot{
					font-family: AngsanaUPC, MS Sans Serif;
					font-size: 19px;
					font-weight: bold;
					text-align:center;
					border-bottom: thin solid black;
					border-left: thin solid black;
					border-right: thin solid black;
				}
				.table_header_bot2{
					font-family: AngsanaUPC, MS Sans Serif;
					font-size: 19px;
					font-weight: bold;
					text-align:center;
					border: thin solid black;
				}
				.table_body{
					font-family: AngsanaUPC, MS Sans Serif;
					font-size: 21px;
					border: thin solid black;
				}
				.table_body_right{
					font-family: AngsanaUPC, MS Sans Serif;
					font-size: 21px;
					border: thin solid black;
					text-align:right;
				}
			</style>
		</head>
		<body>
<?php
	if(@$_GET['start_date']){
		$start_date_arr = explode('/',@$_GET['start_date']);
		$start_day = $start_date_arr[0];
		$start_month = $start_date_arr[1];
		$start_year = $start_date_arr[2];
		$start_year -= 543;
		$start_date = $start_year.'-'.$start_month.'-'.$start_day;
	}

	if(@$_GET['end_date']){
		$end_date_arr = explode('/',@$_GET['end_date']);
		$end_day = $end_date_arr[0];
		$end_month = $end_date_arr[1];
		$end_year = $end_date_arr[2];
		$end_year -= 543;
		$end_date = $end_year.'-'.$end_month.'-'.$end_day;
	}
?>
			<table class="table table-bordered">	
				<tr>
					<tr>
						<th class="table_title" colspan="12"><?php echo @$_SESSION['COOP_NAME'];?></th>
					</tr>
					<tr>
						<th class="table_title" colspan="12">รายงานสมาชิกลาออกรายวัน</th>
					</tr>
					<tr>
						<th class="table_title" colspan="12">
							<?php 
								echo (@$_GET['start_date'] == @$_GET['end_date'])?"":"ตั้งแต่";
								echo "วันที่ ".$this->center_function->ConvertToThaiDate($start_date);
								echo (@$_GET['start_date'] == @$_GET['end_date'])?"":"  ถึงวันที่  ".$this->center_function->ConvertToThaiDate($end_date);
							?>
						</th>
					</tr>
					<tr>
						<th class="table_title_right" colspan="12">
							<span class="title_view">วันที่ <?php echo $this->center_function->ConvertToThaiDate(@date('Y-m-d'),1,0);?></span>				
							<span class="title_view">   เวลา <?php echo date('H:i:s');?></span>	
						</th>
					</tr>
				</tr> 
			</table>
		
			<table class="table table-bordered">
				<thead> 
					<tr>
						<th class="table_header_top" style="vertical-align: middle;">ลำดับ</th>
						<th class="table_header_top" style="vertical-align: middle;">รหัสสมาชิก</th>
						<th class="table_header_top" style="vertical-align: middle;">ชื่อ-นามสกุล</th>
						<th class="table_header_top" style="vertical-align: middle;">สังกัด</th>
						<th class="table_header_top" style="vertical-align: middle;">วันที่ลาออก</th>
						<th class="table_header_top" style="vertical-align: middle;">ทุนเรือนหุ้น</th>
						<th class="table_header_top" style="vertical-align: middle;">สัญญา</th>
						<th class="table_header_top" style="vertical-align: middle;">เงินต้น</th>
						<th class="table_header_top" style="vertical-align: middle;">ดอกเบี้ย</th>
						<th class="table_header_top" style="vertical-align: middle;">ดอกเบี้ยคงค้าง</th>
						<th class="table_header_top" style="vertical-align: middle;">เงินฝาก</th>
						<th class="table_header_top" style="vertical-align: middle;">ดอกเบี้ยเงินฝาก</th>
						<th class="table_header_top" style="vertical-align: middle;">จ่ายคืน</th>
						<th class="table_header_top" style="vertical-align: middle;">หมายเหตุ</th>
						<th class="table_header_top" style="vertical-align: middle;">หมายเลขบัญชีสมาชิก</th>
					</tr> 
				</thead>
				<tbody>
				<?php
						foreach($datas as $row) {
					?>
					<tr>
						<?php
							if($prev_member != $row['member_id']) {
								$runno++;
								$total['share_early_value'] += $row['share_early_value'];
						?>
							<td class="table_body" style="text-align: right;"><?php echo $runno;?></td>
							<td class="table_body" style="text-align: right;mso-number-format:'@';"><?php echo $row["member_id"];?></td>
							<td class="table_body" style="text-align: left;"><?php echo $row['prename_full'].$row['firstname_th']." ".$row['lastname_th'];?></td> 
							<td class="table_body" style="text-align: left;"><?php echo $row['faction_name']."/".$row['level_name'];?></td>
							<td class="table_body" style="text-align: left;"><?php echo $this->center_function->ConvertToThaiDate($row["approve_date"]);?></td>
							<td class="table_body" style="text-align: right;"><?php echo number_format($row['share_early_value'],2);?></td>
						<?php
							} else {
						?>
							<td class="table_body" style="text-align: right;"></td>
							<td class="table_body" style="text-align: right;"></td>
							<td class="table_body" style="text-align: right;"></td>
							<td class="table_body" style="text-align: right;"></td>
							<td class="table_body" style="text-align: right;"></td>
							<td class="table_body" style="text-align: right;"></td>
						<?php
							}

							if(!empty($row['contract_number'])) {
						?>
						<td class="table_body" style="text-align: center;"><?php echo $row['contract_number'];?></td>
						<td class="table_body" style="text-align: right;"><?php echo number_format($row['loan_amount_principal'],2);?></td>
						<td class="table_body" style="text-align: right;"><?php echo number_format($row['loan_amount_interest'],2);?></td>
						<td class="table_body" style="text-align: right;"><?php echo number_format($row['loan_amount_interest_debt'],2);?></td>
						<?php
							} else {
						?>
						<td class="table_body" style="text-align: center;">-</td>
						<td class="table_body" style="text-align: right;">-</td>
						<td class="table_body" style="text-align: right;">-</td>
						<td class="table_body" style="text-align: right;">-</td>
						<?php
							}
							if($prev_member != $row['member_id']) {
								$total['income_amount'] += $row['total'];
						?>
							<td class="table_body" style="text-align: right;"><?php echo number_format($row['balance'],2);?></td>
							<td class="table_body" style="text-align: right;"><?php echo number_format($row['interest'],2);?></td>
							<td class="table_body" style="text-align: right;"><?php echo number_format($row['total'],2);?></td>
							<td class="table_body" style="text-align: left;"><?php echo $row['resign_cause_name'];?></td>
						<?php
							} else {
						?>
							<td class="table_body" style="text-align: right;"></td>
							<td class="table_body" style="text-align: right;"></td>
							<td class="table_body" style="text-align: right;"></td>
							<td class="table_body" style="text-align: left;"></td>
						<?php
							}
							if($prev_member != $row['member_id']) {
								if ($row['sex'] == "M") {
									$male_count++;
								} elseif ($row['sex'] == "F") {
									$female_count++;
								} else {
									$unknow_sex_count++;
								}
								$total['acc_balance'] += $row['balance'];
								$total['acc_interest'] += $row['interest'];
						?>
							<td class="table_body" style="text-align: center;mso-number-format:'@';"><?php echo $row['dividend_acc_num'];?></td>
						<?php
							} else {
						?>
							<td class="table_body" style="text-align: left;"></td>
						<?php
							}

							$total['principal'] += $row['loan_amount_principal'];
							$total['interest'] += $row['loan_amount_interest'];
							$total['interest_debt'] += $row['loan_amount_interest_debt'];

							$prev_member = $row['member_id'];
						?>
					</tr>
					<?php
						}
					?>
					<tr>
						<td class="table_body" colspan="5" style="text-align: center;">
							<?php
								$text = "";
								if(!empty($male_count)) {
									$text .= "เป็นชายจำนวน :: ".$male_count." ";
								}
								if(!empty($female_count)) {
									$text .= "เป็นหญิงจำนวน :: ".$female_count." ";
								}
								if(!empty($unknow_sex_count)) {
									$text .= "ระบุไม่ได้จำนวน :: ".$unknow_sex_count." ";
								}
								echo $text;
							?>
						</td>
						<td class="table_body" style="text-align: right;"><?php echo number_format($total['share_early_value'],2);?></td>
						<td class="table_body" style="text-align: left;"></td>
						<td class="table_body" style="text-align: right;"><?php echo number_format($total['principal'],2);?></td>
						<td class="table_body" style="text-align: right;"><?php echo number_format($total['interest'],2);?></td>
						<td class="table_body" style="text-align: right;"><?php echo number_format($total['interest_debt'],2);?></td>
						<td class="table_body" style="text-align: right;"><?php echo number_format($total['acc_balance'],2);?></td>
						<td class="table_body" style="text-align: right;"><?php echo number_format($total['acc_interest'],2);?></td>
						<td class="table_body" style="text-align: right;"><?php echo number_format($total['income_amount'],2);?></td>
						<td class="table_body" colspan="2" style="text-align: right;"></td>
					</tr>
				</tbody>    
			</table>
		</body>
	</html>
</pre>