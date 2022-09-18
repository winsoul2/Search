<?php 
header("Content-type: application/vnd.ms-excel;charset=utf-8;");
header("Content-Disposition: attachment; filename=รายงานเงินคืนสมาชิกลาออก".@$title_date.".xls"); 
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

//echo '<pre>'; print_r($row); echo '</pre>';
	?>
				<table class="table table-bordered">	
					<tr>
						<tr>
							<th class="table_title" colspan="8"><?php echo @$_SESSION['COOP_NAME'];?></th>
						</tr>
						<tr>
							<th class="table_title" colspan="8">รายงานเงินคืนสมาชิกลาออก</th>
						</tr>
						<tr>
							<th class="table_title" colspan="8"><?php echo @$title_date;?></th>
						</tr>
					</tr> 
				</table>
			
				<table class="table table-bordered">
					<thead>
						<tr>							
							<th class="table_header_top" style="width: 10px;vertical-align: middle;">ลำดับ</th>
							<th class="table_header_top" style="width: 10px;vertical-align: middle;">รหัสสมาชิก</th>
							<th class="table_header_top" style="width: 100px;vertical-align: middle;">ชื่อ-นามสกุล</th>
							<th class="table_header_top" style="width: 20px;vertical-align: middle;">เลขที่ใบเสร็จ</th>
							<th class="table_header_top" style="width: 20px;vertical-align: middle;">หุ้น</th>
							<th class="table_header_top" style="width: 20px;vertical-align: middle;">เลขที่สัญญา</th>
							<th class="table_header_top" style="width: 20px;vertical-align: middle;">เงินต้น</th>
							<th class="table_header_top" style="width: 20px;vertical-align: middle;">ดอกเบี้ย</th>
							<th class="table_header_top" style="width: 20px;vertical-align: middle;">จำนวนเงินฝาก</th>
							<th class="table_header_top" style="width: 20px;vertical-align: middle;">เลขบัญชีกรุงไทย</th>
							<th class="table_header_top" style="width: 20px;vertical-align: middle;">เบอร์โทร </th>					
						</tr> 
					</thead>
					<tbody>
					<?php
						$total_pay_amount = 0;
						$total_real_pay_amount = 0;
						$run_no=0;
						if(!empty($data)){
							foreach(@$data as $key => $row){
								$run_no++;
								$run_loan = 0;
								if(@$row['loan']){
									foreach(@$row['loan'] AS $key_loan=> $row_loan){
										if(@$run_loan == 0){
											$key_loan_f = $key_loan;									
											$run_loan++;
										}
									}
								}
					?>		
							<tr>
								<td class="table_body" style="text-align: center;"><?php echo $run_no;?></td>
								<td class="table_body" style='text-align: center;mso-number-format:"\@";'><?php echo @$row['member_id']; ?></td>
								<td class="table_body" style="text-align: left;"><?php echo @$row['prename_full'].@$row['firstname_th']." ".@$row['lastname_th'];?></td>
								<td class="table_body" style='text-align: left;mso-number-format:"\@";'><?php echo @$row['receipt_id'];?></td>
								<td class="table_body" style="text-align: right;"><?php echo ($row['share'] != '')?number_format($row['share'],2,'.',','):'';?></td>
								<td class="table_body" style='text-align: left;mso-number-format:"\@";'><?php echo @$row['loan'][$key_loan_f]['contract_number'];?></td>
								<td class="table_body" style="text-align: right;"><?php echo (@$row['loan'][$key_loan_f]['principal'] != '')?number_format(@$row['loan'][$key_loan_f]['principal'],2,'.',','):'';?></td>
								<td class="table_body" style="text-align: right;"><?php echo (@$row['loan'][$key_loan_f]['interest'] != '')?number_format(@$row['loan'][$key_loan_f]['interest'],2,'.',','):'';?></td>
								<td class="table_body" style="text-align: right;"><?php echo (@$row['deposit'] != '')?number_format(@$row['deposit'],2,'.',','):'';?></td>
								<td class="table_body" style='text-align: left;mso-number-format:"\@";'><?php echo  @$row['bank_account'];?></td>
								<td class="table_body" style='text-align: left;mso-number-format:"\@";'><?php echo @$row['mobile'];?></td>
					
							</tr>
					<?php 
					$run_loan_n = 0;
					if(@$row['loan']){
						foreach(@$row['loan'] AS $key_loan_n=> $row_loan){
							if(@$run_loan_n > 0){
				?>
							<tr>
								<td class="table_body" style="text-align: center;"></td>
								<td class="table_body" style="text-align: center;"></td>
								<td class="table_body" style="text-align: left;"></td>
								<td class="table_body" style="text-align: left;"></td>
								<td class="table_body" style="text-align: right;"></td>
								<td class="table_body" style='text-align: left;mso-number-format:"\@";'><?php echo @$row['loan'][$key_loan_n]['contract_number'];?></td>
								<td class="table_body" style="text-align: right;"><?php echo (@$row['loan'][$key_loan_n]['principal'] != '')?number_format(@$row['loan'][$key_loan_n]['principal'],2,'.',','):'';?></td>
								<td class="table_body" style="text-align: right;"><?php echo (@$row['loan'][$key_loan_n]['interest'] != '')?number_format(@$row['loan'][$key_loan_n]['interest'],2,'.',','):'';?></td>
								<td class="table_body" style="text-align: right;"></td>
								<td class="table_body" style="text-align: left;"></td>
								<td class="table_body" style="text-align: center;"></td>
							</tr>
				<?php 
							}
							$run_loan_n++;	
							$total_principal += @$row['loan'][$key_loan_n]['principal'];
							$total_interest += @$row['loan'][$key_loan_n]['interest'];
						} 
					}
				?>
				
					<?php
							$total_share += @$row['share'];
							$total_deposit += @$row['deposit'];
							}
						}
					?>	
						 <tr>
                            <td class="table_body" colspan="4" style="text-align: center;">รวม</td>
                            <td class="table_body" style="text-align: right;"><?php echo number_format($total_share,2,'.',',');?></td>
                            <td class="table_body" style="text-align: right;"> </td>
                            <td class="table_body" style="text-align: right;"><?php echo number_format($total_principal,2,'.',',');?></td>
                            <td class="table_body" style="text-align: right;"><?php echo number_format($total_interest,2,'.',',');?></td>
                            <td class="table_body" style="text-align: right;"><?php echo number_format($total_deposit,2,'.',',');?></td>
                            <td class="table_body" style="text-align: right;"> </td>
                            <td class="table_body" style="text-align: right;"> </td>
                        </tr>	
					</tbody>    
				</table>
		</body>
	</html>
</pre>