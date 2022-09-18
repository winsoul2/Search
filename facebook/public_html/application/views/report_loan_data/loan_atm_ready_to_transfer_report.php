<?php 
header("Content-type: application/vnd.ms-excel;charset=utf-8;");
header("Content-Disposition: attachment; filename=export.xls"); 
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
			<table class="table table-bordered">
				<thead>
					<tr>
						<th class="table_title" colspan="21"><?php echo $_SESSION['COOP_NAME']; ?></th>
					</tr>
					<tr>
						<th class="table_title" colspan="21">รายงานการสั่งจ่ายเงินกู้</th>
					</tr>
					<tr>
						<th class="table_header_top">ลำดับ</th>
						<th class="table_header_top">วันที่สั่งจ่าย</th>
						<th class="table_header_top">เลขที่สัญญา</th>
						<th class="table_header_top">เลขสมาชิก</th>
						<th class="table_header_top">ชื่อ-นามสกุล</th>
						<th class="table_header_top">จำนวนเงินกู้</th>
						<th class="table_header_top" rowspan="2" colspan="4">หักเงินกู้เดิม</th>
						<th class="table_header_top">เงินฝาก</th>
						<th class="table_header_top">เบี้ยประกันชีวิต</th>
						<th class="table_header_top">รายการซื้อ</th>
						<th class="table_header_top">เงินฝาก</th>
						<th class="table_header_top">ค่าธรรมเนียม</th>
						<th class="table_header_top">ชำระหนี้</th>
						<th class="table_header_top">รวมยอดหัก</th>
						<th class="table_header_top">คงรับ</th>
						<th class="table_header_top">เลขบัญชี</th>
						<th class="table_header_top">เบอร์โทร</th>
					</tr>
					<tr>
						<th class="table_header_mid"></th>
						<th class="table_header_mid"></th>
						<th class="table_header_mid"></th>
						<th class="table_header_mid"></th>
						<th class="table_header_mid"></th>
						<th class="table_header_mid"></th>
						<th class="table_header_mid">เล่มน้ำเงิน</th>
						<th class="table_header_mid"></th>
						<th class="table_header_mid"></th>
						<th class="table_header_mid">หลักประกัน</th>
						<th class="table_header_mid"></th>
						<th class="table_header_mid">อื่นๆ</th>
						<th class="table_header_mid"></th>
						<th class="table_header_mid"></th>
						<th class="table_header_mid"></th>
						<th class="table_header_mid"></th>
					</tr>
					<tr>
						<th class="table_header_bot"></th>
						<th class="table_header_bot"></th>
						<th class="table_header_bot"></th>
						<th class="table_header_bot"></th>
						<th class="table_header_bot"></th>
						<th class="table_header_bot"></th>
						<th class="table_header_bot2">เลขที่สัญญา</th>
						<th class="table_header_bot2">เงินต้น</th>
						<th class="table_header_bot2">ดอกเบี้ย</th>
						<th class="table_header_bot2">ค่าธรรมเนียม</th>
						<th class="table_header_bot"></th>
						<th class="table_header_bot"></th>
						<th class="table_header_bot"></th>
						<th class="table_header_bot">เงินกู้</th>
						<th class="table_header_bot"></th>
						<th class="table_header_bot"></th>
						<th class="table_header_bot"></th>
						<th class="table_header_bot"></th>
						<th class="table_header_bot"></th>
						<th class="table_header_bot"></th>
					</tr>
				</thead>
				<tbody>
					<?php 
					$i=1;
					$loan_amount = 0;
					$prev_loan_contract_number = 0;
					$prev_loan_principal = 0;
					$prev_loan_interest = 0;
					$prev_loan_fee = 0;
					$deduct_share = 0;
					$deduct_blue_deposit = 0;
					$deduct_insurance = 0;
					$deduct_person_guarantee = 0;
					$deduct_loan_fee = 0;
					$financial_institutions_amount = 0;
					$sum_deduct = 0;
					$estimate_receive_money = 0;
					foreach($row_loan as $key => $value){
						$loan_amount += $value['loan_amount'];
						if(@$value['prev_loan'][0]['contract_number']!=''){
							$prev_loan_contract_number++;
							$prev_loan_principal += @$value['prev_loan'][0]['principal'];
							$prev_loan_interest += @$value['prev_loan'][0]['interest'];
							$prev_loan_fee += 0;
						}
						$deduct_share += 0;
						$deduct_blue_deposit += 0;
						$deduct_insurance += 0;
						$deduct_person_guarantee += 0;
						$deduct_loan_fee += 0;
						$financial_institutions_amount += 0;
						$sum_deduct += @$value['prev_loan'][0]['deduct_loan'];
						$estimate_receive_money += (@$value['loan_amount']-@$value['prev_loan'][0]['deduct_loan']);
					?>
						<tr>
							<td class="table_body"><?php echo $i++; ?></td>
							<td class="table_body"><?php echo (@$value['approve_date']!='')?$this->center_function->ConvertToThaiDate(@$value['approve_date']):''; ?></td>
							<td class="table_body"><?php echo $value['contract_number']; ?></td>
							<td class="table_body" style='mso-number-format:"\@";'><?php echo $value['member_id']; ?></td>
							<td class="table_body"><?php echo $value['prename_short'].$value['firstname_th']." ".$value['lastname_th']; ?></td>
							<td class="table_body_right"><?php echo number_format($value['loan_amount'],2); ?></td>
							<td class="table_body"><?php echo @$value['prev_loan'][0]['contract_number']; ?></td>
							<td class="table_body_right"><?php echo number_format(@$value['prev_loan'][0]['principal'],2); ?></td>
							<td class="table_body_right"><?php echo number_format(@$value['prev_loan'][0]['interest'],2); ?></td>
							<td class="table_body_right"><?php echo number_format(0,2); ?></td>
							<td class="table_body_right"><?php echo number_format(@$value['loan_deduct']['deduct_blue_deposit'],2); ?></td>
							<td class="table_body_right"><?php echo number_format(0,2); ?></td>
							<td class="table_body_right"><?php echo number_format(0,2); ?></td>
							<td class="table_body_right"><?php echo number_format(0,2); ?></td>
							<td class="table_body_right"><?php echo number_format(0,2); ?></td>
							<td class="table_body_right"><?php echo number_format(0,2); ?></td>
							<td class="table_body_right"><?php echo number_format(@$value['prev_loan'][0]['deduct_loan'],2); ?></td>
							<td class="table_body_right"><?php echo number_format((@$value['loan_amount']-@$value['prev_loan'][0]['deduct_loan']),2); ?></td>
							<td class="table_body"><?php echo $value['transfer_text']; ?></td>
							<td class="table_body" style='mso-number-format:"\@";'><?php echo $value['mobile']; ?></td>
						</tr>
				
						<?php 
						$count_prev_loan = @count(@$value['prev_loan']);
						if($count_prev_loan > 1){
							for($j=1;$j<$count_prev_loan;$j++){
								if(@$value['prev_loan'][$j]['contract_number']!=''){
									$prev_loan_contract_number++;
									$prev_loan_principal += @$value['prev_loan'][$j]['principal'];
									$prev_loan_interest += @$value['prev_loan'][$j]['interest'];
									$prev_loan_fee += 0;
								}
						?>
								<tr>
									<td class="table_body"></td>
									<td class="table_body"></td>
									<td class="table_body"></td>
									<td class="table_body"></td>
									<td class="table_body"></td>
									<td class="table_body"></td>
									<td class="table_body"><?php echo @$value['prev_loan'][$j]['contract_number']; ?></td>
									<td class="table_body_right"><?php echo number_format(@$value['prev_loan'][$j]['principal'],2); ?></td>
									<td class="table_body_right"><?php echo number_format(@$value['prev_loan'][$j]['interest'],2); ?></td>
									<td class="table_body_right"><?php echo '0.00'; ?></td>
									<td class="table_body"></td>
									<td class="table_body"></td>
									<td class="table_body"></td>
									<td class="table_body"></td>
									<td class="table_body"></td>
									<td class="table_body"></td>
									<td class="table_body"></td>
									<td class="table_body"></td>
									<td class="table_body"></td>
									<td class="table_body"></td>
									<td class="table_body"></td>
								</tr>		
				<?php 
							}
						}
					} ?>
				</tbody>
				<tfoot>
					<tr>
						<td class="table_body" colspan="5">รวม</td>
						<td class="table_body_right"><?php echo number_format(@$loan_amount,2); ?></td>
						<td class="table_body_right"><?php echo number_format(@$prev_loan_contract_number); ?></td>
						<td class="table_body_right"><?php echo number_format(@$prev_loan_principal,2); ?></td>
						<td class="table_body_right"><?php echo number_format(@$prev_loan_interest,2); ?></td>
						<td class="table_body_right"><?php echo number_format(@$prev_loan_fee,2); ?></td>
						<td class="table_body_right"><?php echo number_format(@$deduct_blue_deposit,2); ?></td>
						<td class="table_body_right"><?php echo number_format(@$deduct_insurance,2); ?></td>
						<td class="table_body_right"><?php echo number_format(0,2); ?></td>
						<td class="table_body_right"><?php echo number_format(@$deduct_person_guarantee,2); ?></td>
						<td class="table_body_right"><?php echo number_format(@$deduct_loan_fee,2); ?></td>
						<td class="table_body_right"><?php echo number_format(@$financial_institutions_amount,2); ?></td>
						<td class="table_body_right"><?php echo number_format(@$sum_deduct,2); ?></td>
						<td class="table_body_right"><?php echo number_format(@$estimate_receive_money,2); ?></td>
						<td class="table_body"></td>
						<td class="table_body"></td>
					</tr>
				</tfoot>
			</table>



		</body>
	</html>
</pre>