<?php 
header("Content-type: application/vnd.ms-excel;charset=utf-8;");
header("Content-Disposition: attachment; filename=รายงานการรับเงินหักรายเดือนล่วงหน้า.xls"); 
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
							<th class="table_title" colspan="8">รายงานการรับเงินหักรายเดือนล่วงหน้า</th>
						</tr>
						<tr>
							<th class="table_title" colspan="8"><?php echo @$title_date;?></th>
						</tr>
					</tr> 
				</table>
			
				<table class="table table-bordered">
					<thead>
						<tr>
							<th class="table_header_top" style="width: 200px;vertical-align: middle;">หน่วยย่อย</th>
							<th class="table_header_top" style="width: 40px;vertical-align: middle;">รูปแบบสมาชิก</th>
							<th class="table_header_top" style="width: 40px;vertical-align: middle;">รหัสสมาชิก</th>
							<th class="table_header_top" style="width: 100px;vertical-align: middle;">ชื่อ</th>
							<th class="table_header_top" style="width: 20px;vertical-align: middle;">ประเภทการจ่าย</th>
							<th class="table_header_top" style="width: 60px;vertical-align: middle;">จํานวนเงิน</th>
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
					?>		
							<tr>
								<td class="table_body" style="text-align: left;"><?php echo @$row['mem_group_name'] ?></td>
								<td class="table_body" style="text-align: left;"><?php echo !empty($row['mem_type_name']) ? @$row['mem_type_name'] : "ไม่ทราบ"?></td>
								<td class="table_body" style='text-align: center;mso-number-format:"\@";'><?php echo @$row['member_id']; ?></td>
								<td class="table_body" style="text-align: left;"><?php echo @$row['prename_full'].@$row['firstname_th']." ".@$row['lastname_th'];?></td>
								<td class="table_body" style="text-align: center;"><?php if(@$row['pay_type'] == '0'){ echo "เงินสด"; } elseif(@$row['pay_type'] == '1') { echo "เงินโอน";}?></td>
								<td class="table_body" style='text-align: right;mso-number-format:"\@";'><?php echo number_format(@$row["sum"],2);?></td>
							</tr>
					<?php	
								if($row['pay_type'] == '0') $cash_total += @$row["sum"];
								if($row['pay_type'] == '1') $transfer_total += @$row["sum"];
								$total += $row["sum"];
							}
						}
					?>	
						
						<tr>
							<td class="table_body" colspan="5" style="text-align: center;">รวม</td>
							<td class="table_body" colspan="1" style="text-align: right;"><?php echo number_format(@$total,2,'.',',');?></td>
						</tr>
						<tr class="none-border">
							<td colspan="4" style="text-align: center;"></td>
							<td colspan="1" style="text-align: center;">เงินสด</td>
							<td colspan="1" style="text-align: right;"><?php echo number_format(@$cash_total,2,'.',',');?></td>
						</tr>
						<tr class="none-border">
							<td colspan="4" style="text-align: center;"></td>
							<td colspan="1" style="text-align: center;">เงินโอน</td>
							<td colspan="1" style="text-align: right;"> <?php echo number_format(@$transfer_total,2,'.',',');?></td>
						</tr>
					
					</tbody>    
				</table>
		</body>
	</html>
</pre>