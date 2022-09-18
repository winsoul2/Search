<?php 
header("Content-type: application/vnd.ms-excel;charset=utf-8;");
header("Content-Disposition: attachment; filename=รายงานรายได้ค่าดำเนินการ.xls"); 
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
						<th class="table_title" colspan="8"><?php echo @$_SESSION['COOP_NAME'];?></th>
					</tr>
					<tr>
						<th class="table_title" colspan="8">รายงานรายได้ค่าดำเนินการ<?php echo $type_name;?></th>
					</tr>
					<tr>
						<th class="table_title" colspan="8">
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
						<th class="table_title_right" colspan="8">วันที่ <?php echo $this->center_function->ConvertToThaiDate(@date('Y-m-d'),0,0);?></th>
					</tr>
					<tr>
						<th class="table_title_right" colspan="8">ผู้ทำรายการ <?php echo $_SESSION['USER_NAME'];?></th>
					</tr>
				</tr>
			</table>
			<table class="table table-bordered">
				<thead>
					<tr>
						<th class="table_header_top" style="vertical-align: middle;">ลำดับ</th>
						<th class="table_header_top" style="vertical-align: middle;">วันที่ทำรายการ</th>
						<th class="table_header_top" style="vertical-align: middle;">เลขฌาปนกิจ</th>
						<th class="table_header_top" style="vertical-align: middle;">ชื่อสกุล</th>
						<th class="table_header_top" style="vertical-align: middle;">รหัสสมาชิก</th>
						<th class="table_header_top" style="vertical-align: middle;">เงินสงเคราะห์</th>
						<th class="table_header_top" style="vertical-align: middle;">ค่าดำเนินการ</th>
						<th class="table_header_top" style="vertical-align: middle;">สถานะ</th>
					</tr>
				</thead>
				<tbody>
				<?php
					$status = array('1'=>'โอนเงินแล้ว');
					$runno = 0;
					$cremation_receive_total = 0;
					$action_fee_total = 0;
					foreach($datas as $key => $row){
						$cremation_receive_total += $row["cremation_receive_amount"];
						$action_fee_total += $row["action_fee_percent"];

				?>
					<tr>
						<td class="table_body" style="vertical-align: middle;"><?php echo ++$runno;?></td>
						<td class="table_body" style="vertical-align: middle;"><?php echo $this->center_function->ConvertToThaiDate($row["date_transfer"]);?></td>
						<td class="table_body" style="vertical-align: middle;mso-number-format:'@';"><?php echo $row["member_cremation_id"];?></td>
						<td class="table_body" style="vertical-align: middle;"><?php echo $row["prename_full"].$row["assoc_firstname"]." ".$row["assoc_lastname"];?></td>
						<td class="table_body" style="vertical-align: middle;mso-number-format:'@';"><?php echo !empty($row["ref_member_id"]) ? $row["ref_member_id"] : $row["member_id"];?></td>
						<td class="table_body" style="vertical-align: middle;"><?php echo number_format($row["cremation_receive_amount"],2);?></td>
						<td class="table_body" style="vertical-align: middle;"><?php echo number_format($row["action_fee_percent"],2);?></td>
						<td class="table_body" style="vertical-align: middle;"><?php echo $status[$row["transfer_status"]];?></td>
					</tr>
				<?php
					}
				?>
					<tr>
						<td colspan="5" class="table_body" style="vertical-align: middle;text-align:center;">รวม</td>
						<td class="table_body" style="vertical-align: middle;"><?php echo number_format($cremation_receive_total,2);?></td>
						<td class="table_body" style="vertical-align: middle;"><?php echo number_format($action_fee_total,2);?></td>
						<td colspan="1" class="table_body" style="vertical-align: middle;text-align:center;"></td>
					</tr>
				</tbody>
			</table>
		</body>
	</html>
</pre>