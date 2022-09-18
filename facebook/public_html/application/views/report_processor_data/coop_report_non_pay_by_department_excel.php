<?php
header("Content-type: application/vnd.ms-excel;charset=utf-8;");
header("Content-Disposition: attachment; filename=รายงานเก็บไม่ได้ (แยกตามหน่วยงาน).xls"); 
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
						<th class="table_title" colspan="13"><?php echo @$_SESSION['COOP_NAME'];?></th>
					</tr>
					<tr>
						<th class="table_title" colspan="13">รายงานเก็บไม่ได้ (แยกตามหน่วยงาน)</th>
					</tr>
					<tr>
						<th class="table_title" colspan="13"><?php echo " ประจำ ".$title_date;?></th>
					</tr>
					<tr>
						<th class="table_title_right" colspan="13">วันที่ <?php echo $this->center_function->ConvertToThaiDate(@date('Y-m-d'),0,0);?></th>
					</tr>
					<tr>
						<th class="table_title_right" colspan="13">ผู้ทำรายการ <?php echo $_SESSION['USER_NAME'];?></th>
					</tr>
				</tr>
			</table>

			<table class="table table-bordered">
				<thead>
					<tr>
						<th class="table_header_top" style="width: 40px;vertical-align: middle;" rowspan="2">ลำดับ</th>
						<th class="table_header_top" style="width: 100px;vertical-align: middle;" rowspan="2">หน่วยงาน</th>
						<th class="table_header_top" style="width: 80px;vertical-align: middle;" rowspan="2">ค่าธรรมเนียมแรกเข้า</th>
						<?php foreach($loan_type as $key => $value){ ?>
							<th class="table_header_top" style="width: 100px;vertical-align: middle;" colspan="2"><?php echo $value['loan_type']; ?></th>
						<?php } ?>
						<th class="table_header_top" style="width: 100px;vertical-align: middle;" rowspan="2">หุ้น</th>
						<th class="table_header_top" style="width: 100px;vertical-align: middle;" rowspan="2">เงินฝาก</th>
						<th class="table_header_top" style="width: 100px;vertical-align: middle;" rowspan="2">ณสอ สป</th>
						<th class="table_header_top" style="width: 100px;vertical-align: middle;" rowspan="2">อื่นๆ</th>
						<th class="table_header_top" style="width: 100px;vertical-align: middle;" rowspan="2">ชำระหนี้ค้ำประกัน</th>
						<th class="table_header_top" style="width: 100px;vertical-align: middle;" rowspan="2">รวม</th>
					</tr>  
					<tr>
						<?php foreach($loan_type as $key => $value){ ?>
							<th class="table_header_top" style="width: 100px;vertical-align: middle;">เงินต้น</th>
							<th class="table_header_top" style="width: 100px;vertical-align: middle;">ดอกเบี้ย</th>
						<?php } ?>
					</tr> 
				</thead>
				<tbody>
					
				<?php	
					$runno = $last_runno;
					if(!empty($data)){
						foreach(@$data AS $page=>$data_row){
							foreach(@$data_row as $key => $row){
								$runno++;
				?>
					<tr> 
						<td class="table_body" style="text-align: center;"><?php echo @$runno; ?></td>
						<td class="table_body" style="text-align: center;"><?php echo $row['mem_group_name']; ?></td>
						<td class="table_body" style="text-align: right;"><?=number_format(@$row['non_pay_data']['REGISTER_FEE'],2)?></td>						 
						<?php foreach($loan_type as $key_loan_type => $value_loan_type){
							if($value_loan_type['loan_type_code'] == 'emergent'){
								$principal = $row['non_pay_data']['LOAN'][$value_loan_type['id']]['principal'] + $row['non_pay_data']['ATM']['principal'];
								$interest = $row['non_pay_data']['LOAN'][$value_loan_type['id']]['interest'] + $row['non_pay_data']['ATM']['interest'];
							}else{
								$principal = $row['non_pay_data']['LOAN'][$value_loan_type['id']]['principal'];
								$interest = $row['non_pay_data']['LOAN'][$value_loan_type['id']]['interest'];
							}
						?>
							<td class="table_body" style="text-align: right;"><?php echo number_format($principal,2); ?></td>
							<td class="table_body" style="text-align: right;"><?php echo number_format($interest,2); ?></td>
						<?php } ?>								
						<td class="table_body" style="text-align: center;"><?php echo number_format($row['non_pay_data']['SHARE'],2); ?></td> 		
						<td class="table_body" style="text-align: center;"><?php echo number_format($row['non_pay_data']['DEPOSIT'],2); ?></td> 		
						<td class="table_body" style="text-align: center;"><?php echo number_format($row['non_pay_data']['CREMATION'],2); ?></td> 
						<td class="table_body" style="text-align: center;"><?php echo number_format($row['non_pay_data']['OTHER'],2); ?></td> 			
						<td class="table_body" style="text-align: center;"><?php echo number_format($row['non_pay_data']['GUARANTEE_AMOUNT'],2); ?></td> 			
						<td class="table_body" style="text-align: center;"><?php echo number_format($row['non_pay_data']['total'],2); ?></td> 									  
					</tr>										
					<?php									
							}
							$last_runno = $runno;
						}
					}
					?>
					<tr class="foot-border"> 
						<td class="table_body" style="text-align: center;" colspan="2">รวมทั้งสิ้น</td>
						<td class="table_body" style="text-align: right;"><?php echo number_format($total_data['REGISTER_FEE'],2); ?></td>						 
						<?php foreach($loan_type as $key_loan_type => $value_loan_type){
							if($value_loan_type['loan_type_code'] == 'emergent'){
								$principal = $total_data['LOAN'][$value_loan_type['id']]['principal'] + $total_data['ATM']['principal'];
								$interest = $total_data['LOAN'][$value_loan_type['id']]['interest'] + $total_data['ATM']['interest'];
							}else{
								$principal = $total_data['LOAN'][$value_loan_type['id']]['principal'];
								$interest = $total_data['LOAN'][$value_loan_type['id']]['interest'];
							}
						?>
							<td class="table_body" style="text-align: right;"><?php echo number_format($principal,2); ?></td>
							<td class="table_body" style="text-align: right;"><?php echo number_format($interest,2); ?></td>
						<?php } ?>								
						<td class="table_body" style="text-align: center;"><?php echo number_format($total_data['SHARE'],2); ?></td> 		
						<td class="table_body" style="text-align: center;"><?php echo number_format($total_data['DEPOSIT'],2); ?></td> 		
						<td class="table_body" style="text-align: center;"><?php echo number_format($total_data['CREMATION'],2); ?></td> 	
						<td class="table_body" style="text-align: center;"><?php echo number_format($total_data['OTHER'],2); ?></td> 	
						<td class="table_body" style="text-align: center;"><?php echo number_format($total_data['GUARANTEE_AMOUNT'],2); ?></td> 	
						<td class="table_body" style="text-align: center;"><?php echo number_format($total_data['total'],2); ?></td>							  
					</tr>
				</tbody>
			</table>
		</body>
	</html>
</pre>