<?php 
header("Content-type: application/vnd.ms-excel;charset=utf-8;");
header("Content-Disposition: attachment; filename=รายงานรายการเรียกเก็บล่วงหน้า.xls"); 
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
	if($_GET['month']!='' && $_GET['year']!=''){
		$day = '';
		$month = $_GET['month'];
		$year = $_GET['year'];
		$title_date = " เดือน ".$this->month_arr[$month]." ปี ".($year);
	}else{
		$day = '';
		$month = '';
		$year = ($_GET['year']);
		$title_date = " ปี ".($year);
	}
?>
			<table class="table table-bordered">
				<tr>
					<tr>
						<th class="table_title" colspan="6"><?php echo @$_SESSION['COOP_NAME'];?></th>
					</tr>
					<tr>
						<th class="table_title" colspan="6">รายงานรายการเรียกเก็บล่วงหน้า<?php echo $type_name;?></th>
					</tr>
					<tr>
						<th class="table_title" colspan="6">
							<h3 class="title_view">
							<?php echo " ประจำ ".@$title_date;?>
							</h3>
						</th>
					</tr>
					<tr>
						<th class="table_title_right" colspan="6">วันที่ <?php echo $this->center_function->ConvertToThaiDate(@date('Y-m-d'),0,0);?></th>
					</tr>
					<tr>
						<th class="table_title_right" colspan="6">ผู้ทำรายการ <?php echo $_SESSION['USER_NAME'];?></th>
					</tr>
				</tr>
			</table>
			<table class="table table-bordered">
				<thead>
					<tr>
						<th class="table_header_top" style="vertical-align: middle; width:5%;">ลำดับ</th>
						<th class="table_header_top" style="vertical-align: middle;">วันที่</th>
						<th class="table_header_top" style="vertical-align: middle; width:25%;">เลขฌาปนกิจ</th>
						<th class="table_header_top" style="vertical-align: middle;">ชื่อสกุล</th>
						<th class="table_header_top" style="vertical-align: middle;">รหัสสมาชิก</th>
						<th class="table_header_top" style="vertical-align: middle;">ยอดเรียกเก็บ</th>
					</tr>
				</thead>
				<tbody>
				<?php
					$status = array('1'=>'โอนเงินแล้ว');
					$runno = 0;
					$charged_total = 0;
					$paid_total = 0;
					$debt_total = 0;
					foreach($datas as $key => $row){
						$charged_total += $row["pay_amount"];
						$paid_total += $row["real_pay_amount"];
						$debt_total += $row["pay_amount"] - $row["real_pay_amount"];
				?>
					<tr>
						<td class="table_body" style="vertical-align: middle;text-align:center;"><?php echo ++$runno;?></td>
						<td class="table_body" style="vertical-align: middle;"><?php echo $this->center_function->ConvertToThaiDate($row["create_datetime"]);?></td>
						<td class="table_body" style="vertical-align: middle;"><?php echo $row["member_cremation_id"];?></td>
						<td class="table_body" style="vertical-align: middle;"><?php echo $row["prename_full"].$row["firstname_th"]." ".$row["lastname_th"];?></td>
						<td class="table_body" style="vertical-align: middle;mso-number-format:'@';text-align:center;"><?php echo $row["member_id"];?></td>
						<td class="table_body" style="vertical-align: middle;"><?php echo number_format($row["pay_amount"],2);?></td>
					</tr>
				<?php
					}
				?>
					<tr>
						<td colspan="5" class="table_body" style="vertical-align: middle; text-align:center;">รวม</td>
						<td class="table_body" style="vertical-align: middle;"><?php echo number_format($charged_total,2);?></td>
					</tr>
				</tbody>
			</table>
		</body>
	</html>
</pre>