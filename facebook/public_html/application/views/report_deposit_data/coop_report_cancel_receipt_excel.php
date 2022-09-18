<?php 
header("Content-type: application/vnd.ms-excel;charset=utf-8;");
header("Content-Disposition: attachment; filename=รายการผิดนัดชำระหนี้ประจำ.xls"); 
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
	$title_date = " เดือน ".$month_arr[$month]." ปี ".($year);
}
$last_runno = 0;

	?>
				<table class="table table-bordered">
					<tr>
						<tr>
							<th class="table_title" colspan="11"><?php echo @$_SESSION['COOP_NAME'];?></th>
						</tr>
						<tr>
							<th class="table_title" colspan="11">รายการผิดนัดชำระหนี้ประจำ<?php echo $title_date;?></th>
						</tr>
						<tr>
							<th class="table_title_right" colspan="11">วันที่ <?php echo $this->center_function->ConvertToThaiDate(@date('Y-m-d'),0,0);?></th>
						</tr>
						<tr>
							<th class="table_title_right" colspan="11">ผู้ทำรายการ <?php echo $_SESSION['USER_NAME'];?></th>
						</tr>
					</tr> 
				</table>
				<table class="table table-bordered">
					<thead> 
						<tr>							
							<th class="table_header_top" style="vertical-align: middle;">ลำดับ</th>
							<th class="table_header_top" style="vertical-align: middle;">เลขที่สมาชิก</th>
							<th class="table_header_top" style="vertical-align: middle;">ชื่อสมาชิก</th>
							<th class="table_header_top" style="vertical-align: middle;">หน่วยงานหลัก</th>
							<th class="table_header_top" style="vertical-align: middle;">เหตุผล</th>
							<th class="table_header_top" style="vertical-align: middle;">สัญญา</th>							
							<th class="table_header_top" style="vertical-align: middle;">เรียกเก็บ</th>
							<th class="table_header_top" style="vertical-align: middle;">เก็บได้</th>
							<th class="table_header_top" style="vertical-align: middle;">ผลต่าง</th>
							<th class="table_header_top" style="vertical-align: middle;">ยอดหนี้คงเหลือ</th>
							<th class="table_header_top" style="vertical-align: middle;">ผิดนัดครั้งที่</th>
						</tr>  
					</thead>
					<tbody>
					<?php	
						$runno = $last_runno;
						if(!empty($datas)){
							foreach($datas AS $page=>$data_row){
							foreach($data_row as $key => $row){
					?>
						<tr> 
					<?php
								if($prev_member_id != $row['member_id']) {
									$runno++;	
					?>
							<td class="table_body" style="text-align: center;vertical-align: top;"><?php echo $runno;?></td>
							<td class="table_body" style="text-align: center;vertical-align: top;"><?php echo $row['member_id'];?></td>
							<td class="table_body" style="text-align: left;vertical-align: top;"><?php echo $row['member_name'];?></td>
							<td class="table_body" style="text-align: left;vertical-align: top;"><?php echo $row['department_name'];?></td>
					<?php
								} else {
					?>
							<td class="table_body" style="text-align: center;vertical-align: top;"></td>
							<td class="table_body" style="text-align: center;vertical-align: top;"><?php echo $row['member_id'];?></td>
							<td class="table_body" style="text-align: left;vertical-align: top;"></td>
							<td class="table_body" style="text-align: left;vertical-align: top;"><?php echo $row['department_name'];?></td>
					<?php
								}
					?>
							<td class="table_body" style="text-align: center;vertical-align: top;"></td>
							<td class="table_body" style="text-align: center;vertical-align: top;"><?php echo $row['contract'];?></td>
							<td class="table_body" style="text-align: right;vertical-align: top;"><?php echo number_format($row['pay_amount'],2);?></td>
							<td class="table_body" style="text-align: right;vertical-align: top;"><?php echo number_format($row['real_pay_amount'],2);?></td>
							<td class="table_body" style="text-align: right;vertical-align: top;"><?php echo number_format($row['pay_amount'] - $row['real_pay_amount'],2);?></td>			 
					<?php
							if($prev_member_id != $row['member_id']) {
					?>
							<td class="table_body" style="text-align: right;vertical-align: top;"><?php echo number_format($row['dept_total'],2);?></td>
							<td class="table_body" style="text-align: center;vertical-align: top;"><?php echo number_format($row['num_non_pay'],0);?></td>
					<?php
								$total['dept_total'] += $row['dept_total'];
							} else {
					?>
							<td class="table_body" style="text-align: center;vertical-align: top;"></td>
							<td class="table_body" style="text-align: center;vertical-align: top;"><?php echo number_format($row['num_non_pay'],0);?></td>
					<?php
							}
					?>
						</tr>
					<?php
							$total['pay_amount'] += $row['pay_amount'];
							$total['real_pay_amount'] += $row['real_pay_amount'];
							$prev_member_id = $row['member_id'];
							}
						}
					}
						if($page == $page_all) {
					?>
					
						<tr>
							<td class="table_body" colspan="6" style="text-align: center;vertical-align: top;">ยอดรวม</td>
							<td class="table_body" style="text-align: right;vertical-align: top;"><?php echo number_format($total['pay_amount'],2);?></td>
							<td class="table_body" style="text-align: right;vertical-align: top;"><?php echo number_format($total['real_pay_amount'],2);?></td>
							<td class="table_body" style="text-align: right;vertical-align: top;"><?php echo number_format($total['pay_amount'] - $total['real_pay_amount'],2);?></td>
							<td class="table_body" style="text-align: right;vertical-align: top;"><?php echo number_format($total['dept_total'] ,2);?></td>
							<td class="table_body" style="text-align: center;vertical-align: top;"></td>
						</tr>
					<?php
						}
					?>
					</tbody>    
				</table>
		</body>
	</html>
</pre>