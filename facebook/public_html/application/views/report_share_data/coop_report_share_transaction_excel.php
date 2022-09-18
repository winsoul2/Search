<?php
header("Content-type: application/vnd.ms-excel;charset=utf-8;");
header("Content-Disposition: attachment; filename=รายงาน การซื้อหุ้น-ถอนหุ้น (Statement).xls"); 
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
if(@$_GET['month']!='' && @$_GET['year']!=''){
	$day = '';
	$month = @$_GET['month'];
	$year = @$_GET['year'];
	$title_date = " เดือน ".@$month_arr[$month]." ปี ".(@$year);
}else{
	$day = '';
	$month = '';
	$year = @$_GET['year'];
	$title_date = " ปี ".(@$year);
}
$last_runno = 0;

	?>
				<table class="table table-bordered">
					<tr>
						<tr>
							<th class="table_title" colspan="10"><?php echo @$_SESSION['COOP_NAME'];?></th>
						</tr>
						<tr>
							<th class="table_title" colspan="10">รายงาน การซื้อหุ้น-ถอนหุ้น (Statement)</th>
						</tr>
						<tr>
							<th class="table_title" colspan="10">
								<?php
									if (!empty($start_date)) {
										echo " ณ วันที่ ".$this->center_function->ConvertToThaiDate($start_date);
										echo (@$_GET['start_date'] == @$_GET['end_date'])?"":"  ถึง  ".$this->center_function->ConvertToThaiDate($end_date);
									} else {
										echo " ณ วันที่ ".$this->center_function->ConvertToThaiDate($end_date);
									}
								?>
							</th>
						</tr>
						<tr>
							<th colspan="3" style="text-align: center;">
								<span class="title_view_small">เลขที่สมาชิก <?php echo $_GET['member_id'];?></span>
							</th>
							<th colspan="4" style="text-align: center;">
								<span class="title_view_small">ชื่อ-นามสกุล <?php echo $member_name;?></span>
							</th>
							<th colspan="3" style="text-align: center;">
								<span class="title_view_small">ทุนเรือนหุ้นคงเหลือ <?php echo number_format($total,2);?></span>
							</th>
						</tr>
						<tr>
							<th colspan="10" style="text-align: right;">
								<span class="title_view_small">วันที่ <?php echo $this->center_function->ConvertToThaiDate(@date('Y-m-d'),0,0);?></span>
							</th>
						</tr>
						<tr>
							<th colspan="10" style="text-align: right;">
								<span class="title_view_small">ผู้ทำรายการ <?php echo $_SESSION['USER_NAME'];?></span>
							</th>
						</tr>
					</tr>
				</table>

				<table class="table table-bordered">
					<thead>
						<tr>
							<th class="table_header_top" style="vertical-align: middle;">ลำดับ</th>
							<th class="table_header_top" style="vertical-align: middle;">เลขที่สมาชิก</th>
							<th class="table_header_top" style="vertical-align: middle;">วันที่</th>
							<th class="table_header_top" style="vertical-align: middle;">เลขที่ใบเสร็จ</th>
							<th class="table_header_top" style="vertical-align: middle;">สถานะ</th>
							<th class="table_header_top" style="vertical-align: middle;">จำนวนหุ้น</th>
							<th class="table_header_top" style="vertical-align: middle;">ทุนเรือนหุ้น</th>
							<th class="table_header_top" style="vertical-align: middle;">จำนวนหุ้นคงเหลือ</th>
							<th class="table_header_top" style="vertical-align: middle;">ทุนเรือนหุ้นคงเหลือ</th>
							<th class="table_header_top" style="vertical-align: middle;">ผู้บันทึก</th>
						</tr>
					</thead>
					<tbody>					
					<?php
						$runno = 0;
						if(!empty($data)){
							foreach(@$data AS $page=>$data_row){
								foreach(@$data_row as $key => $row){
									$runno++;
					?>
							<tr>
								<td class="table_body" style="vertical-align: middle; text-align: center;"><?php echo $runno;?></td>
								<td class="table_body" style="vertical-align: middle;mso-number-format:'\@'; text-align: center;"><?php echo $row['member_id'];?></td>
								<td class="table_body" style="vertical-align: middle; text-align: center;"><?php echo $this->center_function->ConvertToThaiDate(substr($row['share_date'],0,10));?></td>
								<td class="table_body" style="vertical-align: middle; text-align: center;mso-number-format:'\@"><?php echo $row['share_bill'];?></td>
								<td class="table_body" style="vertical-align: middle; text-align: center;"><?php echo @$share_type[$row['share_type']];?></td>					
								<td class="table_body" style="vertical-align: middle; text-align: right;"><?php echo number_format($row['share_early'],0);?></td>
								<td class="table_body" style="vertical-align: middle; text-align: right;"><?php echo number_format($row['share_early_value'],2);?></td>
								<td class="table_body" style="vertical-align: middle; text-align: right;"><?php echo number_format($row['share_collect'],0);?></td>
								<td class="table_body" style="vertical-align: middle; text-align: right;"><?php echo number_format($row['share_collect_value'],2);?></td>
								<td class="table_body" style="vertical-align: middle; text-align: center;"><?php echo $row['user_name'];?></td>
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