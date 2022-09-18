<?php
header("Content-type: application/vnd.ms-excel;charset=utf-8;");
header("Content-Disposition: attachment; filename=รายงานเก็บไม่ได้ (รายละเอียดรายบุคคล).xls"); 
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
	if(@$_GET['month']!='' && @$_GET['year']!=''){
		$day = '';
		$month = @$_GET['month'];
		$year = (@$_GET['year']);
		$title_date = " เดือน ".@$month_arr[$month]." ปี ".(@$year);
	}else{
		$day = '';
		$month = '';
		$year = (@$_GET['year']);
		$title_date = " ปี ".(@$year);
	}

	?>
			<table class="table table-bordered">
				<tr>
					<tr>
						<th class="table_title" colspan="19"><?php echo @$_SESSION['COOP_NAME'];?></th>
					</tr>
					<tr>
						<th class="table_title" colspan="19">รายงานเก็บไม่ได้ (รายละเอียดรายบุคคล)</th>
					</tr>
					<tr>
						<th class="table_title" colspan="19"><?php echo " ประจำ ".$title_date;?></th>
					</tr>
					<tr>
						<th class="table_title_right" colspan="19">วันที่ <?php echo $this->center_function->ConvertToThaiDate(@date('Y-m-d'),0,0);?></th>
					</tr>
					<tr>
						<th class="table_title_right" colspan="19">ผู้ทำรายการ <?php echo $_SESSION['USER_NAME'];?></th>
					</tr>
				</tr>
			</table>

			<table class="table table-bordered">
				<thead>
					<tr>
						<th class="table_header_top" style="width: 40px;vertical-align: middle;" rowspan="2">ลำดับ</th>
						<th class="table_header_top" style="width: 100px;vertical-align: middle;" rowspan="2">เลขที่สมาชิก</th>
						<th class="table_header_top" style="width: 100px;vertical-align: middle;" rowspan="2">ชื่อ-นามสกุล</th>
						<th class="table_header_top" style="width: 80px;vertical-align: middle;" rowspan="2">ค่าธรรมเนียมแรกเข้า</th>
						<?php foreach($loan_type as $key => $value){ ?>
							<th class="table_header_top" style="width: 100px;vertical-align: middle;" colspan="3"><?php echo $value['loan_type']; ?></th>
						<?php } ?>
						<th class="table_header_top" style="width: 100px;vertical-align: middle;" rowspan="2">หุ้น</th>
						<th class="table_header_top" style="width: 100px;vertical-align: middle;" rowspan="2">เงินฝาก</th>
						<th class="table_header_top" style="width: 100px;vertical-align: middle;" rowspan="2">ณสอ สป</th>
						<th class="table_header_top" style="width: 100px;vertical-align: middle;" rowspan="2">ชำระหนี้ค้ำประกัน</th>
						<th class="table_header_top" style="width: 100px;vertical-align: middle;" rowspan="2">รวม</th>
					</tr>  
					<tr>
						<?php foreach($loan_type as $key => $value){ ?>
							<th class="table_header_top" style="width: 100px;vertical-align: middle;">เลขที่สัญญา</th>
							<th class="table_header_top" style="width: 100px;vertical-align: middle;">เงินต้น</th>
							<th class="table_header_top" style="width: 100px;vertical-align: middle;">ดอกเบี้ย</th>
						<?php } ?>
					</tr> 
				</thead>
				<tbody>
				<?php
					//class="page-break"
					if(!empty($row_group)){
						$page=1;
						foreach(@$row_group AS $key_group => $value_group){
							if(!empty($value_group['non_pay_data'])){
							$runno = 0;
				?>
					<tr>
						<th class="table_header_top" colspan="17" style="text-align:left;"><?php echo $value_group['department_name']."::".$value_group['faction_name']."::".$value_group['mem_group_name']; ?></th>
					</tr> 
					<?php
					$member_id_check = "x";
					for($i=1;$i<=count($value_group['non_pay_data']);$i++){
						foreach(@$value_group['non_pay_data'][$i] as $key3 => $row_parent){
							foreach(@$row_parent as $key2 => $row){
								$runno++;
					?>
						<tr> 
							<td class="table_body" style="text-align: center;"><?php echo @$runno; ?></td>
							<td class="table_body" style="text-align: center;"><?php echo @$row['member_id']; ?></td>
							<td class="table_body" style="text-align: left;"><?php echo @$row['member_name']; ?></td>
					<?php
								if($member_id_check != $row['member_id']) {
					?>
							<td class="table_body" style="text-align: right;"><?php echo number_format(@$row['REGISTER_FEE'],2); ?></td>
					<?php
								} else {
					?>
							<td class="table_body" style="text-align: right;"></td>
					<?php
								}

								if (!empty($row['emergent']['contract_number'])) {
					?>
							<td class="table_body" style="text-align: center;"><?php echo $row['emergent']['contract_number']; ?></td>
							<td class="table_body" style="text-align: right;"><?php echo number_format(@$row['emergent']['principal'],2); ?></td>
							<td class="table_body" style="text-align: right;"><?php echo number_format(@$row['emergent']['interest'],2); ?></td>
					<?php
								} else {
					?>
							<td class="table_body" style="text-align: right;"></td>
							<td class="table_body" style="text-align: right;"></td>
							<td class="table_body" style="text-align: right;"></td>
					<?php
								}
								if (!empty($row['normal']['contract_number'])) {
					?>
							<td class="table_body" style="text-align: center;"><?php echo $row['normal']['contract_number']; ?></td>
							<td class="table_body" style="text-align: right;"><?php echo number_format(@$row['normal']['principal'],2); ?></td>
							<td class="table_body" style="text-align: right;"><?php echo number_format(@$row['normal']['interest'],2); ?></td>
					<?php
								} else {
					?>
							<td class="table_body" style="text-align: right;"></td>
							<td class="table_body" style="text-align: right;"></td>
							<td class="table_body" style="text-align: right;"></td>
					<?php
								}
								if (!empty($row['special']['contract_number'])) {
					?>
							<td class="table_body" style="text-align: center;"><?php echo $row['special']['contract_number']; ?></td>
							<td class="table_body" style="text-align: right;"><?php echo number_format(@$row['special']['principal'],2); ?></td>
							<td class="table_body" style="text-align: right;"><?php echo number_format(@$row['special']['interest'],2); ?></td>
					<?php
								} else {
					?>
							<td class="table_body" style="text-align: right;"></td>
							<td class="table_body" style="text-align: right;"></td>
							<td class="table_body" style="text-align: right;"></td>
					<?php
								}
								if($member_id_check != $row['member_id']) {
					?>
							<td class="table_body" style="text-align: right;"><?php echo number_format(@$row['SHARE'],2); ?></td>
							<td class="table_body" style="text-align: right;"><?php echo number_format(@$row['DEPOSIT'],2); ?></td>
							<td class="table_body" style="text-align: right;"><?php echo number_format(@$row['CREMATION'],2); ?></td>
							<td class="table_body" style="text-align: right;"><?php echo number_format(@$row['GUARANTEE_AMOUNT'],2); ?></td>
							<td class="table_body" style="text-align: right;"><?php echo number_format(@$row['total'],2); ?></td>
					<?php
								} else {
					?>
							<td class="table_body" style="text-align: right;"></td>
							<td class="table_body" style="text-align: right;"></td>
							<td class="table_body" style="text-align: right;"></td>
							<td class="table_body" style="text-align: right;"></td>
							<td class="table_body" style="text-align: right;"></td>
					<?php
								}
					?>
						</tr>	
					<?php
								$member_id_check = $row['member_id'];							
							}
						}
					}
					?>
						<tr> 
							<td class="table_body" style="text-align: center;" colspan="3">รวมทั้งสิ้น</td>
							<td class="table_body" style="text-align: right;"><?php echo number_format(@$total_data[$value_group['id']]['REGISTER_FEE'],2); ?></td>
							<td class="table_body" style="text-align: right;"></td>
							<td class="table_body" style="text-align: right;"><?php echo number_format(@$total_data[$value_group['id']]['LOAN']['emergent']['principal'],2); ?></td>
							<td class="table_body" style="text-align: right;"><?php echo number_format(@$total_data[$value_group['id']]['LOAN']['emergent']['interest'],2); ?></td>
							<td class="table_body" style="text-align: right;"></td>
							<td class="table_body" style="text-align: right;"><?php echo number_format(@$total_data[$value_group['id']]['LOAN']['normal']['principal'],2); ?></td>
							<td class="table_body" style="text-align: right;"><?php echo number_format(@$total_data[$value_group['id']]['LOAN']['normal']['interest'],2); ?></td>
							<td class="table_body" style="text-align: right;"></td>
							<td class="table_body" style="text-align: right;"><?php echo number_format(@$total_data[$value_group['id']]['LOAN']['special']['principal'],2); ?></td>
							<td class="table_body" style="text-align: right;"><?php echo number_format(@$total_data[$value_group['id']]['LOAN']['special']['interest'],2); ?></td>
							<td class="table_body" style="text-align: right;"><?php echo number_format(@$total_data[$value_group['id']]['SHARE'],2); ?></td>
							<td class="table_body" style="text-align: right;"><?php echo number_format(@$total_data[$value_group['id']]['DEPOSIT'],2); ?></td>
							<td class="table_body" style="text-align: right;"><?php echo number_format(@$total_data[$value_group['id']]['CREMATION'],2); ?></td>
							<td class="table_body" style="text-align: right;"><?php echo number_format(@$total_data[$value_group['id']]['GUARANTEE_AMOUNT'],2); ?></td>
							<td class="table_body" style="text-align: right;"><?php echo number_format(@$total_data[$value_group['id']]['total'],2); ?></td>
						</tr>
			<?php		
		
	}
}
} 
			?>
					<tr class="foot-border"> 
						<td class="table_body" style="text-align: center;font-weight:bold;" colspan="3">รวมทั้งหมด</td>
						<td class="table_body" style="text-align: right;"><?php echo number_format(@$total_all_data['REGISTER_FEE'],2); ?></td>
						<td class="table_body" style="text-align: right;"></td>
						<td class="table_body" style="text-align: right;"><?php echo number_format(@$total_all_data['LOAN']['emergent']['principal'],2); ?></td>
						<td class="table_body" style="text-align: right;"><?php echo number_format(@$total_all_data['LOAN']['emergent']['interest'],2); ?></td>
						<td class="table_body" style="text-align: right;"></td>
						<td class="table_body" style="text-align: right;"><?php echo number_format(@$total_all_data['LOAN']['normal']['principal'],2); ?></td>
						<td class="table_body" style="text-align: right;"><?php echo number_format(@$total_all_data['LOAN']['normal']['interest'],2); ?></td>
						<td class="table_body" style="text-align: right;"></td>
						<td class="table_body" style="text-align: right;"><?php echo number_format(@$total_all_data['LOAN']['special']['principal'],2); ?></td>
						<td class="table_body" style="text-align: right;"><?php echo number_format(@$total_all_data['LOAN']['special']['interest'],2); ?></td>
						<td class="table_body" style="text-align: right;"><?php echo number_format(@$total_all_data['SHARE'],2); ?></td>
						<td class="table_body" style="text-align: right;"><?php echo number_format(@$total_all_data['DEPOSIT'],2); ?></td>
						<td class="table_body" style="text-align: right;"><?php echo number_format(@$total_all_data['CREMATION'],2); ?></td>
						<td class="table_body" style="text-align: right;"><?php echo number_format(@$total_all_data['GUARANTEE_AMOUNT'],2); ?></td>
						<td class="table_body" style="text-align: right;"><?php echo number_format(@$total_all_data['total'],2); ?></td>
					</tr>
				</tbody>
			</table>
		</body>
	</html>
</pre>