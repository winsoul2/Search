<?php 
header("Content-type: application/vnd.ms-excel;charset=utf-8;");
header("Content-Disposition: attachment; filename=ยอดเงินตืนเข้าเล่มชมพู.xls"); 
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
							<th class="table_title" colspan="5"><?php echo @$_SESSION['COOP_NAME'];?></th>
						</tr>
						<tr>
							<th class="table_title" colspan="5">ยอดเงินคืนเข้าเล่มชมพู</th>
						</tr>
					</tr> 
				</table>
			
				<table class="table table-bordered">
					<thead>
						<tr>														
							<th class="table_header_top">เลขบัญชี</th>
							<th class="table_header_top">ชื่อ</th>
							<th class="table_header_top">ประเภท</th>
							<th class="table_header_top">ฝาก</th>
							<th class="table_header_top">คงเหลือ</th>
						</tr>
					</thead>
					<tbody>
					<?php
						$total_pay_amount = 0;
						$total_real_pay_amount = 0;
						$i=0;
						if(!empty($datas)){
							foreach($datas as $key => $data){
					?>		
							<tr>
								<td class="table_body"><?php echo $data['account_id']; ?></td>
								<td class="table_body" style="text-align:left;"><?php echo $data['account_name']; ?></td>
								<td class="table_body" ><?php echo $data['return_from'] == 'occasional' ? 'การชำระเงินผ่านเคาน์เตอร์' : 'การชำระเงินผ่านรายการเรียกเก็บรายเดือน'; ?></td>
								<td class="table_body" style="text-align:right;"><?php echo number_format($data['transaction_deposit'],2); ?></td>
								<td class="table_body" style="text-align:right;"><?php echo number_format($data['transaction_balance'],2); ?></td>
							</tr>	
					<?php									
							}
						}
					?>
					</tbody>    
				</table>
		</body>
	</html>
</pre>