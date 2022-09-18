<?php 
header("Content-type: application/vnd.ms-excel;charset=utf-8;");
header("Content-Disposition: attachment; filename=รายงานสรุปการรับชำระหุ้น-สินเชื่อ.xls"); 
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
if(@$_GET['month']!='' && @$_GET['year']!=''){
	$day = '';
	$month = @$_GET['month'];
	$year = @$_GET['year'];
	$title_date = " เดือน ".@$month_arr[$month]." ปี ".(@$year);
}else{
	$day = '';
	$month = '';
	$year = @$_GET['year'];
	$title_date = " ปี ".(@$year);
}
$last_runno = 0;

	?>
				<table class="table table-bordered">
					<tr>
						<tr>
							<th class="table_title" colspan="24"><?php echo @$_SESSION['COOP_NAME'];?></th>
						</tr>
						<tr>
							<th class="table_title" colspan="24">รายงานสรุปการรับชำระหุ้น-สินเชื่อ</th>
						</tr>
						<tr>
							<th class="table_title" colspan="24">
								<?php 
									echo "ประจำ วันที่ ".$this->center_function->ConvertToThaiDate($start_date);
									echo (@$_GET['start_date'] == @$_GET['end_date'])?"":"  ถึงวันที่  ".$this->center_function->ConvertToThaiDate($end_date);
								?>
							</th>
						</tr>
						<tr>
							<th class="table_title_right" colspan="24">วันที่ <?php echo $this->center_function->ConvertToThaiDate(@date('Y-m-d'),0,0);?></th>
						</tr>
						<tr>
							<th class="table_title_right" colspan="24">ผู้ทำรายการ <?php echo $_SESSION['USER_NAME'];?></th>
						</tr>
					</tr> 
				</table>
			
				<table class="table table-bordered">
					<thead> 
						<tr>
							<th class="table_header_top" rowspan="2" style="width: 40px;vertical-align: middle;">ลำดับ</th>
							<th class="table_header_top" rowspan="2" style="width: 70px;vertical-align: middle;">รหัส</th>
							<th class="table_header_top" rowspan="2" style="width: 160px;vertical-align: middle;">ชื่อ - นามสกุล</th>
							<th class="table_header_top" rowspan="2" style="vertical-align: middle;">วันที่</th> 
							<th class="table_header_top" rowspan="2" style="vertical-align: middle;">การโอนเงิน</th> 
							<th class="table_header_top" rowspan="2" style="vertical-align: middle;">เลขที่ใบเสร็จ</th> 
							<th class="table_header_top" rowspan="2" style="width: 40px;vertical-align: middle;">ซื้อหุ้น</th> 
							<th class="table_header_top" rowspan="2" style="width: 40px;vertical-align: middle;">ถอนหุ้น</th> 
							<?php 
								foreach(@$loan_type AS $key=>$row_loan_type){
							?>
								<th colspan="3" class="table_header_top" style="width: 80px;vertical-align: middle;"><?php echo str_replace('เงินกู้','',@$row_loan_type['loan_type']);?></th> 
							<?php 
								}
							?>
							<th class="table_header_top" rowspan="2" style="width: 80px;vertical-align: middle;">เงินฝาก</th>
							<th class="table_header_top" rowspan="2" style="width: 80px;vertical-align: middle;">เงินฝาก<br>บช.69</th>
							<th class="table_header_top" rowspan="2" style="width: 80px;vertical-align: middle;">ดอกเบี้ย<br>คงค้าง</th>
							<th class="table_header_top" rowspan="2" style="width: 80px;vertical-align: middle;">เบี้ย<br>ประกันชีวิต</th>
							<th class="table_header_top" rowspan="2" style="width: 80px;vertical-align: middle;">ฌาปนกิจ</th>
							<th class="table_header_top" rowspan="2" style="width: 80px;vertical-align: middle;">หลักประกัน<br>ผู้ค้ำประกัน</th>
							<th class="table_header_top" rowspan="2" style="width: 80px;vertical-align: middle;">ค่าธรรมเนียม<br>การกู้ 0.01%</th>	
						</tr> 
						<tr>
							<?php 
								foreach(@$loan_type AS $key=>$row_loan_type){
							?>
								<th class="table_header_top" style="width: 80px;vertical-align: middle;">เลขที่สัญญา</th>
								<th class="table_header_top" style="width: 80px;vertical-align: middle;">เงินต้น</th>
								<th class="table_header_top" style="width: 80px;vertical-align: middle;">ดอกเบี้ย</th> 
							<?php 
								}
							?>

						</tr> 
					</thead>
					<tbody>
					<?php
						$runno = $last_runno;
						$totals = array();
						if(!empty($datas)){
							foreach($datas AS $member_id => $data_rows){
								foreach($data_rows AS $receipt_id => $data_row){
									$normal_nums = array();
									if(!empty($data_row['normal'])) {
										foreach($data_row['normal'] as $contract_number => $val) {
											$normal_nums[] = $contract_number;
										}
									}
									$emergent_nums = array();
									if(!empty($data_row['emergent'])) {
										foreach($data_row['emergent'] as $contract_number => $val) {
											$emergent_nums[] = $contract_number;
										}
									}
									$special_num = array();
									if(!empty($data_row['special'])) {
										foreach($data_row['special'] as $contract_number => $val) {
											$special_num[] = $contract_number;
										}
									}
									$normalSize = count($normal_nums);
									$emergentSize = count($emergent_nums);
									$specialSize = count($special_num);
									$max_loan_index = max($normalSize, $emergentSize, $specialSize) > 0 ? max($normalSize, $emergentSize, $specialSize) : 1;
							
									for($i = 0; $i < $max_loan_index; $i++) {
										$runno++;
					?>
							<tr>
								<td class="table_body" style="text-align: center;vertical-align: top;"><?php echo $runno; ?></td>
								<td class="table_body" style="text-align: center;vertical-align: top;"><?php echo $member_id;?></td>
								<td class="table_body" style='text-align: left;vertical-align: top;mso-number-format:"\@";'><?php echo $data_row['member_name'];?></td>
								<td class="table_body" style="text-align: left;vertical-align: top;"><?php echo $this->center_function->ConvertToThaiDate(@$data_row['receipt_datetime'],1,0);?></td>
								<td class="table_body" style='text-align: center;vertical-align: top;'><?php echo $arr_pay_type[$data_row['pay_type']];?></td>
								<td class="table_body" style='text-align: left;vertical-align: top;mso-number-format:"\@";'><?php echo strpos($data_row['receipt_id'], 'SHARE_') === false ? $data_row['receipt_id'] : "";?></td>
								<td class="table_body" style="text-align: right;vertical-align: top;">
									<?php
										if(!empty($data_row['share']) && $i == 0) {
											echo number_format($data_row['share'],2);
											$totals['buy_stock'] += $data_row['share'];
										}
									?>
								</td>
								<td class="table_body" style="text-align: right;vertical-align: top;">
									<?php
										if(!empty($data_row['share_withdraw']) && $i == 0) {
											echo (@$data_row['share_withdraw'] != '')?number_format(@$data_row['share_withdraw'],2):'';
											$totals['share_withdraw'] += @$data_row['share_withdraw'];
										}
									?>
								</td>
							<?php
								if (!empty($emergent_nums[$i])) {
							?>
								<td class="table_body" style="text-align: right;vertical-align: top;"><?php echo $emergent_nums[$i]; ?></td>
								<td class="table_body" style="text-align: right;vertical-align: top;"><?php echo number_format($data_row['emergent'][$emergent_nums[$i]]['principal'],2); ?></td>
								<td class="table_body" style="text-align: right;vertical-align: top;"><?php echo number_format($data_row['emergent'][$emergent_nums[$i]]['interest'],2); ?></td>
								
							<?php
									$totals['emergent']['principal_payment'] += $data_row['emergent'][$emergent_nums[$i]]['principal'];
									$totals['emergent']['interest'] += $data_row['emergent'][$emergent_nums[$i]]['interest'];
									$totals['emergent']['none_pay'] += $data_row['emergent'][$emergent_nums[$i]]['none_pay'];
								} else {
							?>
								<td class="table_body" style="text-align: right;vertical-align: top;"></td>
								<td class="table_body" style="text-align: right;vertical-align: top;"></td>
								<td class="table_body" style="text-align: right;vertical-align: top;"></td>
							<?php
								}

								if (!empty($normal_nums[$i])) {
							?>
								<td class="table_body" style="text-align: right;vertical-align: top;"><?php echo $normal_nums[$i]; ?></td>
								<td class="table_body" style="text-align: right;vertical-align: top;"><?php echo number_format($data_row['normal'][$normal_nums[$i]]['principal'],2); ?></td>
								<td class="table_body" style="text-align: right;vertical-align: top;"><?php echo number_format($data_row['normal'][$normal_nums[$i]]['interest'],2); ?></td>
							<?php
									$totals['normal']['principal_payment'] += $data_row['normal'][$normal_nums[$i]]['principal'];
									$totals['normal']['interest'] += $data_row['normal'][$normal_nums[$i]]['interest'];
									$totals['normal']['none_pay'] += $data_row['normal'][$normal_nums[$i]]['none_pay'];
								} else {
							?>
								<td class="table_body" style="text-align: right;vertical-align: top;"></td>
								<td class="table_body" style="text-align: right;vertical-align: top;"></td>
								<td class="table_body" style="text-align: right;vertical-align: top;"></td>
							<?php
								}

								if (!empty($special_num[$i])) {
							?>
										<td class="table_body" style="text-align: right;vertical-align: top;"><?php echo $special_num[$i]; ?></td>
										<td class="table_body" style="text-align: right;vertical-align: top;"><?php echo number_format($data_row['special'][$special_num[$i]]['principal'],2); ?></td>
										<td class="table_body" style="text-align: right;vertical-align: top;"><?php echo number_format($data_row['special'][$special_num[$i]]['interest'],2); ?></td>
							<?php
											$totals['special']['principal_payment'] += $data_row['special'][$special_num[$i]]['principal'];
											$totals['special']['interest'] += $data_row['special'][$special_num[$i]]['interest'];
											$totals['special']['none_pay'] += $data_row['special'][$special_num[$i]]['none_pay'];
								} else {
							?>
										<td class="table_body" style="text-align: right;vertical-align: top;"></td>
										<td class="table_body" style="text-align: right;vertical-align: top;"></td>
										<td class="table_body" style="text-align: right;vertical-align: top;"></td>
							<?php
								}
							?>
								<td class="table_body" style="text-align: right;vertical-align: top;">
									<?php
										if(!empty($data_row['deposit']) && $i == 0) {
											echo (@$data_row['deposit'] != '')?number_format(@$data_row['deposit'],2):'';
											$totals['deposit'] += @$data_row['deposit'];
										}
									?>
								</td>
								<td class="table_body" style="text-align: right;vertical-align: top;">
									<?php
										if(!empty($data_row['deposit_blue']) && $i == 0) {
											echo (@$data_row['deposit_blue'] != '')?number_format(@$data_row['deposit_blue'],2):'';
											$totals['deposit_blue'] += @$data_row['deposit_blue'];
										}
									?>
								</td>
								<td class="table_body" style="text-align: right;vertical-align: top;">
									<?php
										if(!empty($data_row['loan_interest_remain']) && $i == 0) {
											echo (@$data_row['loan_interest_remain'] != '')?number_format(@$data_row['loan_interest_remain'],2):'';
											$totals['loan_interest_remain'] += @$data_row['loan_interest_remain'];
										}
									?>
								</td>
								<td class="table_body" style="text-align: right;vertical-align: top;">
									<?php
										if(!empty($data_row['life_insurance']) && $i == 0) {
											echo (@$data_row['life_insurance'] != '')?number_format(@$data_row['life_insurance'],2):'';
											$totals['life_insurance'] += @$data_row['life_insurance'];
										}
									?>
								</td>
								<td class="table_body" style="text-align: right;vertical-align: top;">
									<?php
										if(!empty($data_row['cremation']) && $i == 0) {
											echo (@$data_row['cremation'] != '')?number_format(@$data_row['cremation'],2):'';
											$totals['cremation'] += @$data_row['cremation'];
										}
									?>
								</td>
								<td class="table_body" style="text-align: right;vertical-align: top;">
									<?php
										if(!empty($data_row['person_guarantee']) && $i == 0) {
											echo (@$data_row['person_guarantee'] != '')?number_format(@$data_row['person_guarantee'],2):'';
											$totals['person_guarantee'] += @$data_row['person_guarantee'];
										}
									?>
								</td>
								<td class="table_body" style="text-align: right;vertical-align: top;">
									<?php
										if(!empty($data_row['loan_fee']) && $i == 0) {
											echo (@$data_row['loan_fee'] != '')?number_format(@$data_row['loan_fee'],2):'';
											$totals['loan_fee'] += @$data_row['loan_fee'];
										}
									?>
								</td>								
							</tr>
					<?php
								}}
							}
						}
					?>
							<tr> 
								<td class="table_body" colspan="6" style="text-align: center;">รวม</td>
								<td class="table_body" style="text-align: right;vertical-align: top;"><?php echo number_format($totals["buy_stock"],2); ?></td>
								<td class="table_body" style="text-align: right;vertical-align: top;"><?php echo number_format($totals["share_withdraw"],2); ?></td> 
					<?php
							foreach(@$loan_type AS $key=>$row_loan_type){
					?>
								<td class="table_body" style="text-align: right;vertical-align: top;"></td>
								<td class="table_body" style="text-align: right;vertical-align: top;"><?php echo number_format($totals[$row_loan_type["loan_type_code"]]['principal_payment'],2); ?></td>
								<td class="table_body" style="text-align: right;vertical-align: top;"><?php echo number_format($totals[$row_loan_type["loan_type_code"]]['interest'],2); ?></td>
					<?php
							}
					?>
								<td class="table_body" style="text-align: right;vertical-align: top;"><?php echo (@$totals['deposit'] != '')?number_format(@$totals['deposit'],2):''; ?></td>
								<td class="table_body" style="text-align: right;vertical-align: top;"><?php echo (@$totals['deposit_blue'] != '')?number_format(@$totals['deposit_blue'],2):''; ?></td>
								<td class="table_body" style="text-align: right;vertical-align: top;"><?php echo (@$totals['loan_interest_remain'] != '')?number_format(@$totals['loan_interest_remain'],2):''; ?></td>
								<td class="table_body" style="text-align: right;vertical-align: top;"><?php echo (@$totals['life_insurance'] != '')?number_format(@$totals['life_insurance'],2):''; ?></td>
								<td class="table_body" style="text-align: right;vertical-align: top;"><?php echo (@$totals['cremation'] != '')?number_format(@$totals['cremation'],2):''; ?></td>
								<td class="table_body" style="text-align: right;vertical-align: top;"><?php echo (@$totals['person_guarantee'] != '')?number_format(@$totals['person_guarantee'],2):''; ?></td>
								<td class="table_body" style="text-align: right;vertical-align: top;"><?php echo (@$totals['loan_fee'] != '')?number_format(@$totals['loan_fee'],2):''; ?></td>
							</tr>
					</tbody>    
				</table>
		</body>
	</html>
</pre>