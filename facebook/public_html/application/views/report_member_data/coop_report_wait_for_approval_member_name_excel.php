<?php 
header("Content-type: application/vnd.ms-excel;charset=utf-8;");
header("Content-Disposition: attachment; filename=รายงานการรับสมัครสมาชิก".$member_status_text.".xls"); 
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
					<th class="table_title" colspan="10"><?php echo @$_SESSION['COOP_NAME'];?></th>
				</tr>
				<tr>
					<th class="table_title" colspan="10">รายงานการรับสมัครสมาชิก<?php echo !empty($member_status_text) ? $member_status_text : "";?></th>
				</tr>
				<tr>
					<td colspan="10" class="table_title_right">
						<span class="title_view">วันที่ <?php echo $this->center_function->ConvertToThaiDate(@date('Y-m-d'),1,0);?></span>
					</td>
				</tr>
				<tr>
					<td colspan="10" class="table_title_right">
						<span class="title_view">เวลา <?php echo date('H:i:s');?></span>
					</td>
				</tr>
				<tr>
					<td colspan="10" class="table_title_right">
						<span class="title_view">ผู้ทำรายการ <?php echo $_SESSION['USER_NAME'];?></span>
					</td>
				</tr>
			</table>
			<table class="table table-bordered">
				<thead>
					<tr>
						<th class="table_header_top" style="vertical-align: middle;">ที่</th>
						<th class="table_header_top" style="vertical-align: middle;">ชื่อ-นามสกุล</th>
						<th class="table_header_top" style="vertical-align: middle;">หมายเลขบัตร</th>
						<th class="table_header_top" style="vertical-align: middle;">เบอร์โทร</th>
						<th class="table_header_top" style="vertical-align: middle;">ตำแหน่ง</th>
						<th class="table_header_top" style="vertical-align: middle;">วันเดือนปีเกิด</th>
						<th class="table_header_top" style="vertical-align: middle;">อายุ</th>
						<th class="table_header_top" style="vertical-align: middle;">เงินเดือน</th>
						<th class="table_header_top" style="vertical-align: middle;">ส่งหุ้น/เดือน</th>
						<th class="table_header_top" style="vertical-align: middle;">หน่วยงาน</th>
					</tr>
				</thead>
				<tbody>
					<?php
						$runno = 0;	
						foreach($datas as $key => $row){
					?>
					<tr>
						<td class="table_body" style="text-align: center;vertical-align: top;"><?php echo ++$runno; ?></td>
						<td class="table_body" style="text-align: left;vertical-align: top;"><?php echo $row['prename_full'].$row["firstname_th"]." ".$row["lastname_th"];?></td>
						<td class="table_body" style="text-align: center;vertical-align: top;mso-number-format:'@';"><?php echo $row['id_card'];?></td>
						<td class="table_body" style="text-align: center;vertical-align: top;mso-number-format:'@';"><?php echo $row['mobile'];?></td>
						<td class="table_body" style="text-align: center;vertical-align: top;"><?php echo $row['position'];?></td>
						<td class="table_body" style="text-align: center;vertical-align: top;mso-number-format:'@';"><?php echo !empty($row['birthday']) ? $this->center_function->ConvertToThaiDate($row['birthday'],1,0) : "";?></td>
						<td class="table_body" style="text-align: center;vertical-align: top;"><?php echo !empty($row['birthday']) ? $this->center_function->cal_age($row['birthday']) : "";?></td>
						<td class="table_body" style="text-align: center;vertical-align: top;"><?php echo number_format($row['salary'],2);?></td>
						<td class="table_body" style="text-align: center;vertical-align: top;"><?php echo number_format($row['share_month'],2);?></td>
						<td class="table_body" style="text-align: center;vertical-align: top;"><?php echo $row['mem_group_full_name'];?></td>
					</tr>
					<?php
						}
					?>
				</tbody>
			</table>
		</body>
	</html>
</pre>