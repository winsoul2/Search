<?php
header("Content-type: application/vnd.ms-excel;charset=utf-8;");
header("Content-Disposition: attachment; filename=รายงานรายชื่อสมาชิก.xls"); 
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
					<tr>
						<th class="table_title" colspan="4"><?php echo @$_SESSION['COOP_NAME'];?></th>
					</tr>
					<tr>
						<th class="table_title" colspan="4">รายงานรายชื่อสมาชิก</th>
					</tr>
					<tr>
						<th class="table_title_right" colspan="4">วันที่ <?php echo $this->center_function->ConvertToThaiDate(@date('Y-m-d'),0,0);?></th>
					</tr>
					<tr>
						<th class="table_title_right" colspan="4">ผู้ทำรายการ <?php echo $_SESSION['USER_NAME'];?></th>
					</tr>
				</tr>
			</table>

			<table class="table table-bordered">
				<tbody>
				<?php
					$runno = 0;
					$prev_level = "x";
					$max = count($datas) - 1;
					foreach($datas as $key => $row){
						if($prev_level != $row["level"]) {
							if($key != 0) {
				?>
						<td colspan="4" class="table_body">
							รวมทั้งสิ้น <?php echo $runno;?> คน
						</td>
				<?php
							}
				?>
					<tr>
						<td colspan="4" class="table_body">
							หน่วยงาน :: <?php echo !empty($row["level"]) ? $row["mem_group_name"]."(".$row["mem_group_id"].")" : "";?>
						</td>
					</tr>
					<tr>
						<th style="vertical-align: middle;" class="table_body">ลำดับ</th>
						<th style="vertical-align: middle;" class="table_body">เลขสมาชิก</th>
						<th style="vertical-align: middle;" class="table_body">ชื่อ-นามสกุล</th>
						<th style="vertical-align: middle;" class="table_body">ลายเซ็น</th>
					</tr>
				<?php
							$runno = 0;
						}
				?>
					<tr>
						<td style="text-align: center;vertical-align: top;" class="table_body"><?php echo ++$runno; ?></td>
						<td style="text-align: center;vertical-align: top;mso-number-format:'@';" class="table_body"><?php echo $row['member_id'];?></td>
						<td style="text-align: left;vertical-align: top;" class="table_body"><?php echo $row['prename_full'].$row["firstname_th"]." ".$row["lastname_th"];?></td>
						<td style="text-align: center;vertical-align: top;" class="table_body"></td>
					</tr>
				<?php
						if($max == $key) {
				?>
						<td colspan="4" class="table_body">
							รวมทั้งสิ้น <?php echo $runno;?> คน
						</td>
				<?php
						}
						$prev_level = $row["level"];
					}
				?>
				</tbody>
			</table>
		</body>
	</html>
</pre>