<?php 
header("Content-type: application/vnd.ms-excel;charset=utf-8;");
header("Content-Disposition: attachment; filename=รายงานสมาชิกลาออก.xls"); 
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
if(@$_GET['start_date']){
	$start_date_arr = explode('/',@$_GET['start_date']);
	$start_day = $start_date_arr[0];
	$start_month = $start_date_arr[1];
	$start_year = $start_date_arr[2];
	$start_year -= 543;
	$start_date = $start_year.'-'.$start_month.'-'.$start_day;
}

if(@$_GET['end_date']){
	$end_date_arr = explode('/',@$_GET['end_date']);
	$end_day = $end_date_arr[0];
	$end_month = $end_date_arr[1];
	$end_year = $end_date_arr[2];
	$end_year -= 543;
	$end_date = $end_year.'-'.$end_month.'-'.$end_day;
}

	?>
				<table class="table table-bordered">
					<tr>
						<tr>
							<th class="table_title" colspan="10"><?php echo @$_SESSION['COOP_NAME'];?></th>
						</tr>
						<tr>
							<th class="table_title" colspan="10">รายงานสมาชิกลาออก<?php echo $type_name;?></th>
						</tr>
						<tr>
							<th class="table_title" colspan="10">
								<h3 class="title_view">
								<?php
									echo (@$_GET['start_date'] == @$_GET['end_date'])?"":"ระหว่าง";
									echo "วันที่ ".$this->center_function->ConvertToThaiDate($start_date);
									echo (@$_GET['start_date'] == @$_GET['end_date'])?"":"  ถึง  ".$this->center_function->ConvertToThaiDate($end_date);
								?>
								</h3>
							</th>
						</tr>
						<tr>
							<th class="table_title_right" colspan="10">วันที่ <?php echo $this->center_function->ConvertToThaiDate(@date('Y-m-d'),0,0);?></th>
						</tr>
						<tr>
							<th class="table_title_right" colspan="10">ผู้ทำรายการ <?php echo $_SESSION['USER_NAME'];?></th>
						</tr>
					</tr>
				</table>
				<table class="table table-bordered">
					<thead>
						<tr>
							<th class="table_header_top" style="vertical-align: middle;">ลำดับ</th>
							<th class="table_header_top" style="vertical-align: middle;">วันที่ทำรายการ</th>
							<th class="table_header_top" style="vertical-align: middle;">วันที่อนุมัติ</th>
							<th class="table_header_top" style="vertical-align: middle;">เลขฌาปนกิจ</th>
							<th class="table_header_top" style="vertical-align: middle;">ชื่อสกุล</th>
							<th class="table_header_top" style="vertical-align: middle;">รหัสสมาชิก</th>
							<th class="table_header_top" style="vertical-align: middle;">ประเภท</th>
							<th class="table_header_top" style="vertical-align: middle;">เงินสงเคราะห์ล่วงหน้า</th>
							<th class="table_header_top" style="vertical-align: middle;">เหตุผลการลาออก</th>
							<th class="table_header_top" style="vertical-align: middle;">หมายเลขบัญชีสมาชิก</th>
						</tr>
					</thead>
					<tbody>
					<?php
						$runno = 0;
						$cremation_receive_total = 0;
						$cremation_balance_total = 0;
						$adv_payment_total = 0;
						$status = array('1'=>'รอโอนเงิน', '3'=>'โอนเงินแล้ว');
						foreach($datas as $key => $row){
							$adv_payment_total += $row["adv_payment_balance"];

					?>
						<tr>
							<td class="table_body" style="vertical-align: middle;"><?php echo ++$runno;?></td>
							<td class="table_body" style="vertical-align: middle;"><?php echo $this->center_function->ConvertToThaiDate($row["created_at"]);?></td>
							<td class="table_body" style="vertical-align: middle;"><?php echo $this->center_function->ConvertToThaiDate($row["approved_date"]);?></td>
							<td class="table_body" style="vertical-align: middle;mso-number-format:'@';"><?php echo $row["member_cremation_id"];?></td>
							<td class="table_body" style="vertical-align: middle;"><?php echo $row["prename_full"].$row["assoc_firstname"]." ".$row["assoc_lastname"];?></td>
							<td class="table_body" style="vertical-align: middle;mso-number-format:'@';"><?php echo !empty($row["ref_member_id"]) ? $row["ref_member_id"] : $row["member_id"];?></td>
							<td class="table_body" style="vertical-align: middle;"><?php echo $row["mem_type_id"] == 1 ? "สามัญ" : "สมทบ";?></td>
							<td class="table_body" style="vertical-align: middle;"><?php echo number_format($row["adv_payment_balance"],2);?></td>
							<td class="table_body" style="vertical-align: middle;"><?php echo $row["reason"];?></td>
							<td class="table_body" style="vertical-align: middle;mso-number-format:'@';"><?php echo $row["bank_account_no"];?></td>
						</tr>
					<?php
						}
					?>
						<tr>
							<td colspan="7" class="table_body" style="vertical-align: middle;text-align:center;">รวม</td>
							<td class="table_body" style="vertical-align: middle;"><?php echo number_format($adv_payment_total,2);?></td>
							<td colspan="2" class="table_body" style="vertical-align: middle;"></td>
						</tr>
					</tbody>
				</table>
		</body>
	</html>
</pre>