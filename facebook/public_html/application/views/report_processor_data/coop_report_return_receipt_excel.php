<?php
header("Content-type: application/vnd.ms-excel;charset=utf-8;");
header("Content-Disposition: attachment; filename=รายงานคืนใบเสร็จยืนยันการประมวลผล.xls"); 
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

	?>
			<table class="table table-bordered">
				<tr>
					<tr>
						<th class="table_title" colspan="5"><?php echo @$_SESSION['COOP_NAME'];?></th>
					</tr>
					<tr>
						<th class="table_title" colspan="5">รายงานคืนใบเสร็จยืนยันการประมวลผล</th>
					</tr>
					<tr>
						<th class="table_title" colspan="5"><?php echo " ประจำ ".$title_date;?></th>
					</tr>
					<tr>
						<th class="table_title_right" colspan="5">วันที่ <?php echo $this->center_function->ConvertToThaiDate(@date('Y-m-d'),0,0);?></th>
					</tr>
					<tr>
						<th class="table_title_right" colspan="5">ผู้ทำรายการ <?php echo $_SESSION['USER_NAME'];?></th>
					</tr>
				</tr>
			</table>

			<table class="table table-bordered">
				<thead>
					<th class="table_header_top" style="vertical-align: middle;">ลำดับ</th>
					<th class="table_header_top" style="vertical-align: middle;">หน่วยงานย่อย</th>
					<th class="table_header_top" style="vertical-align: middle;">เลขที่สมาชิก</th>
					<th class="table_header_top" style="vertical-align: middle;">ชื่อ-นามสกุล</th>
					<th class="table_header_top" style="vertical-align: middle;">รวม</th>
				</thead>
				<tbody>
					<?php
						foreach($data as $key => $row){
							$run_no++;
					?>
						<tr> 
							<td class="table_body" style="text-align: center;"><?php echo $run_no; ?></td>
							<td class="table_body" style="text-align: center;"><?php echo $row['mem_group_name'] ?></td>
							<td class="table_body" style="text-align: center;"><?php echo $row['member_id']; ?></td>
							<td class="table_body" style="text-align: left;"><?php echo $row['prename_full'].$row['firstname_th']." ".$row['lastname_th'];?></td>
							<td class="table_body" style="text-align: right;"><?php echo number_format($row['non_pay_amount_balance'],2);?></td>
						</tr>
					<?php
						}
					?>
				</tbody>
			</table>
		</body>
	</html>
</pre>