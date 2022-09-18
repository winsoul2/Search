<?php
header("Content-type: application/vnd.ms-excel;charset=utf-8;");
header("Content-Disposition: attachment; filename=รายการส่งยืนยันยอดหุ้น - หนี้ และเงินฝาก รายบุคคล.xls"); 
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
    $month_arr = array('1'=>'มกราคม','2'=>'กุมภาพันธ์','3'=>'มีนาคม','4'=>'เมษายน','5'=>'พฤษภาคม','6'=>'มิถุนายน','7'=>'กรกฎาคม','8'=>'สิงหาคม','9'=>'กันยายน','10'=>'ตุลาคม','11'=>'พฤศจิกายน','12'=>'ธันวาคม');
	?>
			<table class="table table-bordered">
				<tr>
					<tr>
						<th class="table_title" colspan="7"><?php echo @$_SESSION['COOP_NAME'];?></th>
					</tr>
					<tr>
						<th class="table_title" colspan="7">รายการส่งยืนยันยอดหุ้น - หนี้ และเงินฝาก รายบุคคล</th>
					</tr>
					<tr>
						<th class="table_title" colspan="7">วันที่ <?php echo $this->center_function->ConvertToThaiDate($date,'','0');?></th>
					</tr>
					<tr>
						<th class="table_title_right" colspan="7">วันที่ <?php echo $this->center_function->ConvertToThaiDate(@date('Y-m-d'),0,0);?></th>
					</tr>
					<tr>
						<th class="table_title_right" colspan="7">ผู้ทำรายการ <?php echo $_SESSION['USER_NAME'];?></th>
					</tr>
				</tr>
			</table>

            <table class="table table-bordered">
				<thead>
					<tr>
						<th class="table_header_top" style="width: 40px;vertical-align: middle;">ลำดับ</th>
						<th class="table_header_top" style="width: 200px;vertical-align: middle;">หน่วยคณะ</th>
						<th class="table_header_top" style="width: 60px;vertical-align: middle;">เลขที่สมาชิก</th>
						<th class="table_header_top" style="width: 130px;vertical-align: middle;">ชื่อ-นามสกุล</th>
						<th class="table_header_top" style="width: 80px;vertical-align: middle;">หุ้น</th>
						<th class="table_header_top" style="width: 80px;vertical-align: middle;">หนี้(รวม)</th>
						<th class="table_header_top" style="width: 80px;vertical-align: middle;">เงินฝาก(รวม)</th>
					</tr>
				</thead>
				<tbody>
				<?php
					$runno = 0;
					foreach($datas AS $member_id => $member) {
						$runno++;
				?>
					<tr>
						<td class="table_body" style="text-align: center;"><?php echo $runno; ?></td>
						<td class="table_body" style="text-align: left;"><?php echo $member["department_name"].",".$member["faction_name"].",".$member["level_name"]; ?></td>
						<td class="table_body" style="text-align: center;"><?php echo $member['member_id']; ?></td>
						<td class="table_body" style="text-align: left;"><?php echo $member["prename_full"].$member["firstname_th"]." ".$member["lastname_th"]?></td>
						<td class="table_body" style="text-align: right;"><?php echo number_format($member['share_collect_value'],2); ?></td>
						<td class="table_body" style="text-align: right;"><?php echo number_format($member['loan'],2); ?></td>
						<td class="table_body" style="text-align: right;"><?php echo number_format($member['account'],2); ?></td>
					</tr>
				<?php
					}
				?>
				</tbody>
			</table>
		</body>
	</html>
</pre>