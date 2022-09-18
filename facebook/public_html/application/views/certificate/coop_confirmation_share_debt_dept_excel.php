<?php
header("Content-type: application/vnd.ms-excel;charset=utf-8;");
header("Content-Disposition: attachment; filename=รายการส่งยืนยันยอดหุ้น - หนี้ และเงินฝาก.xls"); 
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
						<th class="table_title" colspan="9"><?php echo @$_SESSION['COOP_NAME'];?></th>
					</tr>
					<tr>
						<th class="table_title" colspan="9">รายการส่งยืนยันยอดหุ้น - หนี้ และเงินฝาก</th>
					</tr>
					<tr>
						<th class="table_title" colspan="9">วันที่ <?php echo $this->center_function->ConvertToThaiDate($date,1,0);?></th>
					</tr>
					<tr>
						<th class="table_title_right" colspan="9">วันที่ <?php echo $this->center_function->ConvertToThaiDate(@date('Y-m-d'),0,0);?></th>
					</tr>
					<tr>
						<th class="table_title_right" colspan="9">ผู้ทำรายการ <?php echo $_SESSION['USER_NAME'];?></th>
					</tr>
				</tr>
			</table>

            <table class="table table-bordered">
				<thead>
					<tr>
						<th class="table_header_top" style="width: 40px;vertical-align: middle;" rowspan="2">ลำดับ</th>
						<th class="table_header_top" style="width: 80px;vertical-align: middle;" rowspan="2">รหัสหน่วย</th>
						<th class="table_header_top" style="width: 200px;vertical-align: middle;" >หน่วยคณะ</th>
						<th class="table_header_top" style="width: 160px;vertical-align: middle;" colspan="2">หุ้น</th>
						<th class="table_header_top" style="width: 160px;vertical-align: middle;" colspan="2">หนี้(รวม)</th>
						<th class="table_header_top" style="width: 160px;vertical-align: middle;" colspan="2">เงินฝาก(รวม)</th>
					</tr>
					<tr>
						<th class="table_header_top" style="width: 80px;vertical-align: middle;"></th>
						<th class="table_header_top" style="width: 80px;vertical-align: middle;">จำนวนราย</th>
						<th class="table_header_top" style="width: 80px;vertical-align: middle;">จำนวนเงิน</th>
						<th class="table_header_top" style="width: 80px;vertical-align: middle;">จำนวนราย</th>
						<th class="table_header_top" style="width: 80px;vertical-align: middle;">จำนวนเงิน</th>
						<th class="table_header_top" style="width: 80px;vertical-align: middle;">จำนวนราย</th>
						<th class="table_header_top" style="width: 80px;vertical-align: middle;">จำนวนเงิน</th>
					</tr>
				</thead>
				<tbody>
			<?php
					$runno = 0;
					foreach($datas AS $department_id => $factions) {
						foreach($factions AS $faction_id => $levels) {
							foreach($levels AS $level_id => $level) {
								$runno++;
					?>
                        <!-- <tr>
                            <td class="table_body" style="text-align: center;"><?php echo $runno; ?></td>
                            <td class="table_body" style="text-align: center;"><?php echo $level['level_group_id']; ?></td>
                            <td class="table_body" style="text-align: left;"><?php echo $level['name']; ?></td>
                            <td class="table_body" style="text-align: right;"><?php echo number_format($level['SHARE']['mem_count']); ?></td>
                            <td class="table_body" style="text-align: right;"><?php echo number_format($level['SHARE']['total_pay_amount'],2); ?></td>
                            <td class="table_body" style="text-align: right;"><?php echo number_format($level['DEBT']['mem_count']); ?></td>
                            <td class="table_body" style="text-align: right;"><?php echo number_format($level['DEBT']['total_pay_amount'],2); ?></td>
                            <td class="table_body" style="text-align: right;"><?php echo number_format($level['DEPT']['mem_count']); ?></td>
                            <td class="table_body" style="text-align: right;"><?php echo number_format($level['DEPT']['total_pay_amount'],2); ?></td>
						</tr> -->
						<tr>
                            <td class="table_body" style="text-align: center;"><?php echo $runno; ?></td>
                            <td class="table_body" style="text-align: center;"><?php echo $level['level_group_id']; ?></td>
                            <td class="table_body" style="text-align: left;"><?php echo $level['name']; ?></td>
                            <td class="table_body" style="text-align: right;"><?php echo number_format($level['share_count']); ?></td>
                            <td class="table_body" style="text-align: right;"><?php echo number_format($level['share_collect_value'],2); ?></td>
                            <td class="table_body" style="text-align: right;"><?php echo number_format($level['loan_count']); ?></td>
                            <td class="table_body" style="text-align: right;"><?php echo number_format($level['loan'],2); ?></td>
                            <td class="table_body" style="text-align: right;"><?php echo number_format($level['account_count']); ?></td>
                            <td class="table_body" style="text-align: right;"><?php echo number_format($level['account'],2); ?></td>
                        </tr>
                    <?php
                                }
                            }
                        }
                    ?>
				</tbody>
			</table>
		</body>
	</html>
</pre>