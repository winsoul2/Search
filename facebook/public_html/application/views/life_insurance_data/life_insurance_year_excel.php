<?php 
//header("Content-type: application/vnd.ms-excel;charset=utf-8;");
//header("Content-Disposition: attachment; filename=ประมาณการประกันชีวิต.xls"); 
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
							<th class="table_title" colspan="10">ระยะเวลาความคุ้มครอง <?php echo @$title_date;?></th>
						</tr>
						<tr>
							<th class="table_title" colspan="10"><?php echo @$_SESSION['COOP_NAME'];?></th>
						</tr>
					</tr> 
				</table>
			
				<table class="table table-bordered">
					<thead>
						<tr>
							<th class="table_header_top" style="width: 10px;vertical-align: middle;">ลำดับ</th>
							<th class="table_header_top" style="width: 100px;vertical-align: middle;">รหัสสมาชิก</th>
							<th class="table_header_top" style="width: 100px;vertical-align: middle;">รหัสกลุ่ม</th>
							<th class="table_header_top" style="width: 200px;vertical-align: middle;">สังกัด</th>
							<th class="table_header_top" style="width: 200px;vertical-align: middle;">ชื่อ - สกุล</th>
							<th class="table_header_top" style="width: 100px;vertical-align: middle;">เลขบัตร 13 หลัก</th>
							<th class="table_header_top" style="width: 100px;vertical-align: middle;">วัน เดือน ปี (คศ.เกิด)</th>
							<th class="table_header_top" style="width: 100px;vertical-align: middle;">อายุ</th>
							<th class="table_header_top" style="width: 100px;vertical-align: middle;">ทุนประกัน</th>
							<th class="table_header_top" style="width: 100px;vertical-align: middle;">เบี้ยประกัน</th>
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
								<td class="table_body" style="text-align: center;"><?php echo @$run_no?></td>
								<td class="table_body" style='text-align: center;mso-number-format:"\@";'><?php echo @$row['member_id']; ?></td>
								<td class="table_body" style='text-align: center;mso-number-format:"\@";'><?php echo @$row['mem_group_id']; ?></td>
								<td class="table_body" style="text-align: left;"><?php echo @$row['mem_group_name'] ?></td>
								<td class="table_body" style="text-align: left;"><?php echo @$row['prename_full'].@$row['firstname_th']." ".@$row['lastname_th'];?></td>
								<td class="table_body" style="text-align: left;"><?php echo @$row['id_card'];;?></td>
								<td class="table_body" style="text-align: left;"><?php echo @$row['birthday'];;?></td>
								<td class="table_body" style="text-align: center;"><?php echo @$row['birthday'];;?></td>
								<td class="table_body" style='text-align: right;mso-number-format:"\@";'><?php echo number_format(@$row["insurance_amount"],2);?></td>
								<td class="table_body" style='text-align: right;mso-number-format:"\@";'><?php echo number_format(@$row["insurance_premium"],2);?></td>
							</tr>
					<?php	
							}
						}
					?>	
						
						<tr>
							<td class="table_body" colspan="5" style="text-align: center;">รวม</td>
							<td class="table_body" colspan="1" style="text-align: right;">&nbsp;</td>
							<td class="table_body" colspan="1" style="text-align: right;">&nbsp;</td>
							<td class="table_body" colspan="1" style="text-align: right;">&nbsp;</td>
							<td class="table_body" colspan="1" style="text-align: right;"><?php echo number_format(@$total,2,'.',',');?></td>
							<td class="table_body" colspan="1" style="text-align: right;"><?php echo number_format(@$total,2,'.',',');?></td>
						</tr>					
					</tbody>    
				</table>
		</body>
	</html>
</pre>