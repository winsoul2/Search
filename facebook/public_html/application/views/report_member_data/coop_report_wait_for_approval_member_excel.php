<?php 
header("Content-type: application/vnd.ms-excel;charset=utf-8;");
header("Content-Disposition: attachment; filename=รายงานกลุ่มรออนุมัติ.xls"); 
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
			<table class="table table-bordered">
				<tr>
					<th class="table_title" colspan="7"><?php echo @$_SESSION['COOP_NAME'];?></th>
				</tr>
				<tr>
					<th class="table_title" colspan="7">รายงานการรับสมัครสมาชิก</th>
				</tr>
				<tr>
					<td colspan="7" class="table_title_right">
						<span class="title_view">วันที่ <?php echo $this->center_function->ConvertToThaiDate(@date('Y-m-d'),1,0);?></span>
					</td>
				</tr>
				<tr>
					<td colspan="7" class="table_title_right">
						<span class="title_view">เวลา <?php echo date('H:i:s');?></span>
					</td>
				</tr>
				<tr>
					<td colspan="7" class="table_title_right">
						<span class="title_view">ผู้ทำรายการ <?php echo $_SESSION['USER_NAME'];?></span>
					</td>
				</tr>
			</table>
			<table class="table table-bordered">
				<thead>
					<tr>
						<th class="table_header_top" style="vertical-align: middle;">ลำดับที่</th>
						<th class="table_header_top" style="vertical-align: middle;">เลขทะเบียนสมาชิก</th>
						<th class="table_header_top" style="vertical-align: middle;">เลขที่คำร้อง</th>
						<th class="table_header_top" style="vertical-align: middle;">วันที่อนุมัติ</th>
						<th class="table_header_top" style="vertical-align: middle;">ชื่อ - สกุล</th> 
						<th class="table_header_top" style="vertical-align: middle;">หน่วยงาน</th> 
						<th class="table_header_top" style="vertical-align: middle;">ค่าหุ้น(บาท)</th> 
						<th class="table_header_top" style="vertical-align: middle;">หมายเหตุ</th> 
					</tr>
				</thead>
				<tbody>
					<?php
						$runno = 0;	
						foreach($datas as $key => $row){
					?>
					<tr>
						<td class="table_body" style="text-align: center;"><?php echo ++$j;?></td>
						<td class="table_body" style="text-align: center;mso-number-format:'@';"><?php echo $row['member_id']; ?></td>
						<td class="table_body" style="text-align: center;mso-number-format:'@';"><?php echo  $row["mem_apply_id"]?></td>
						<td class="table_body" style="text-align: center;"><?php echo $this->center_function->ConvertToThaiDate($row["member_date"]); ?></td>				 
						<td class="table_body" style="text-align: left;"><?php echo $row['prename_full'].$row['firstname_th'].'  '.$row['lastname_th']; ?></td>						 
						<td class="table_body" style="text-align: left;"><?php echo $row["mem_group_name"]; ?></td> 							 
						<td class="table_body" style="text-align: right;"><?php echo number_format($row['share_month'],2); ?></td> 						 
						<td class="table_body" style="text-align: left;"><?php echo $row["register_note"]; ?></td> 		
					</tr>
					<?php
						}
					?>
				</tbody>
			</table>
		</body>
	</html>
</pre>