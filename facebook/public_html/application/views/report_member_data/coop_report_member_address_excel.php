<?php 
header("Content-type: application/vnd.ms-excel;charset=utf-8;");
header("Content-Disposition: attachment; filename=รายงานที่อยู่ของสมาชิก.xls"); 
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
					<th class="table_title" colspan="11">รายงานที่อยู่ของสมาชิก</th>
				</tr>
			</table>
			<table class="table table-bordered">
				<thead> 
					<tr>
						<th class="table_header_top" style="vertical-align: middle;">ลำดับ</th>
						<th class="table_header_top" style="vertical-align: middle;">หน่วยงานหลัก</th>
						<th class="table_header_top" style="vertical-align: middle;">หน่วยงานรอง</th>
						<th class="table_header_top" style="vertical-align: middle;">หน่วยงานย่อย</th>
						<th class="table_header_top" style="vertical-align: middle;">เลขที่สมาชิก</th>
						<th class="table_header_top" style="vertical-align: middle;">ชื่อ - นามสกุล</th>
						<th class="table_header_top" style="vertical-align: middle;">เลขบัตรประชาชน</th>
						<th class="table_header_top" style="vertical-align: middle;">วัน/เดือน/ปี เกิด</th>
						<th class="table_header_top" style="vertical-align: middle;">วัน/เดือน/ปี ที่เป็นสมาชิก</th>
						<th class="table_header_top" style="vertical-align: middle;">เลขที่</th>
						<th class="table_header_top" style="vertical-align: middle;">หมู่</th>
						<th class="table_header_top" style="vertical-align: middle;">ซอย</th>
						<th class="table_header_top" style="vertical-align: middle;">ถนน</th>
						<th class="table_header_top" style="vertical-align: middle;">ตำบล</th>
						<th class="table_header_top" style="vertical-align: middle;">อำเภอ</th>
						<th class="table_header_top" style="vertical-align: middle;">จังหวัด</th>
						<th class="table_header_top" style="vertical-align: middle;">รหัสไปรษณีย์</th>
						<th class="table_header_top" style="vertical-align: middle;">โทรศัพท์มือถือ</th>
						<th class="table_header_top" style="vertical-align: middle;">โทรศัพท์บ้าน</th>
						<th class="table_header_top" style="vertical-align: middle;">สถานะ</th>
					</tr>
				</thead>
				<tbody>
				<?php
					$i = 0;
					foreach($datas as $data) {
						$i++;
				?>
					<tr>
						<td class="table_body" style="text-align: left;vertical-align: top;mso-number-format:'@';"><?php echo $i;?></td>
						<td class="table_body" style="text-align: left;vertical-align: top;"><?php echo $data['department_name'];?></td>
						<td class="table_body" style="text-align: left;vertical-align: top;"><?php echo $data['faction_name'];?></td>
						<td class="table_body" style="text-align: left;vertical-align: top;"><?php echo $data['level_name'];?></td>
						<td class="table_body" style="text-align: left;vertical-align: top;mso-number-format:'@';"><?php echo $data['member_id'];?></td>
						<td class="table_body" style="text-align: left;vertical-align: top;"><?php echo $data['prename_full'].$data['firstname_th']." ".$data['lastname_th'];?></td>
						<td class="table_body" style="text-align: left;vertical-align: top;mso-number-format:'@';"><?php echo $data['id_card'];?></td>
						<td class="table_body" style="text-align: left;vertical-align: top;"><?php echo $data['birthday'];?></td>
						<td class="table_body" style="text-align: left;vertical-align: top;"><?php echo $data['member_date'];?></td>
						<td class="table_body" style="text-align: left;vertical-align: top;mso-number-format:'@';"><?php echo $data['address_no'];?></td>
						<td class="table_body" style="text-align: left;vertical-align: top;"><?php echo $data['address_moo'];?></td>
						<td class="table_body" style="text-align: left;vertical-align: top;"><?php echo $data['address_soi'];?></td>
						<td class="table_body" style="text-align: left;vertical-align: top;"><?php echo $data['address_road'];?></td>
						<td class="table_body" style="text-align: left;vertical-align: top;"><?php echo $data['district_name'];?></td>
						<td class="table_body" style="text-align: left;vertical-align: top;"><?php echo $data['amphur_name'];?></td>
						<td class="table_body" style="text-align: left;vertical-align: top;"><?php echo $data['province_name'];?></td>
						<td class="table_body" style="text-align: left;vertical-align: top;mso-number-format:'@';"><?php echo $data['zipcode'];?></td>
						<td class="table_body" style="text-align: left;vertical-align: top;mso-number-format:'@';"><?php echo $data['mobile'];?></td>
						<td class="table_body" style="text-align: left;vertical-align: top;mso-number-format:'@';"><?php echo $data['tel'];?></td>
						<td class="table_body" style="text-align: left;vertical-align: top;">ปกติ</td>
					</tr>
				<?php
					}
				?>
				</tbody>    
			</table>
		</body>
	</html>
</pre>