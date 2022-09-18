<?php 
header("Content-type: application/vnd.ms-excel;charset=utf-8;");
header("Content-Disposition: attachment; filename=รายงานประมวลผลผ่านรายการ.xls"); 
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
							<th class="table_title" colspan="8">รายงานประมวลผลผ่านรายการ</th>
						</tr>
					</tr> 
				</table>
			
				<table class="table table-bordered">
					<thead>
						<tr>														
							<th class="table_header_top">ลำดับ</th>
							<th class="table_header_top">รหัสสมาชิก</th>
							<th class="table_header_top">ชื่อ - นามสกุล</th>
							<th class="table_header_top">หน่วยงาน</th>
							<th class="table_header_top">จำนวนเงินทั้งหมด</th>
							<th class="table_header_top">จำนวนเงินที่หักได้</th>
							<th class="table_header_top">เลขที่ใบเสร็จ</th>
							<th class="table_header_top">วิธีชำระเงิน</th>
						</tr>
					</thead>
					<tbody>
					<?php
						$total_pay_amount = 0;
						$total_real_pay_amount = 0;
						$i=0;
						if(!empty($data)){
							foreach(@$data as $key => $value){
								$i++;
								$total_pay_amount  += @$value['pay_amount'];
								$total_real_pay_amount  += @$value['real_pay_amount'];
					?>		
							<tr>
								<td class="table_body"><?php echo $i; ?></td>
								<td class="table_body" style='text-align: center;vertical-align: top;mso-number-format:"\@";'><?php echo @$value['member_id']; ?></td>
								<td class="table_body" style="text-align:left;"><?php echo @$value['prename_short'].@$value['firstname_th']." ".@$value['lastname_th']; ?></td>
								<td class="table_body" ><?php echo @$value['mem_group_name']; ?></td>
								<td class="table_body" style="text-align:right;"><?php echo number_format(@$value['pay_amount'],2); ?></td>
								<td class="table_body" style="text-align:right;"><?php echo number_format(@$value['real_pay_amount'],2); ?></td>
								<td class="table_body" style='text-align: center;vertical-align: top;mso-number-format:"\@";' ><?php echo @$value['receipt_id']; ?></td>
								<td class="table_body" ><?php echo @$value['pay_type']; ?></td>
							</tr>	
					<?php									
							}
						}
					?>	
						<tr>
							<td class="table_body" style="text-align:right;font-weight: bold;" colspan="4">ยอดรวม</td>
							<td class="table_body" style="text-align:right;font-weight: bold;"><?php echo number_format(@$total_pay_amount,2); ?></td>
							<td class="table_body" style="text-align:right;font-weight: bold;"><?php echo number_format(@$total_real_pay_amount,2); ?></td>
							<td class="table_body"></td>
							<td class="table_body"></td>
						</tr>
					</tbody>    
				</table>
		</body>
	</html>
</pre>