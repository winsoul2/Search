<?php 
header("Content-type: application/vnd.ms-excel;charset=utf-8;");
header("Content-Disposition: attachment; filename=รายงานดอกเบี้ย.xls"); 
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
							<th class="table_title" colspan="6"><?php echo @$_SESSION['COOP_NAME'];?></th>
						</tr>
						<tr>
							<th class="table_title" colspan="6">รายงานดอกเบี้ย <?php echo $type_name;?></th>
						</tr>
						<tr>
							<th class="table_title" colspan="6">
								<h3 class="title_view">
									<?php 
										echo (@$_GET['start_date'] == @$_GET['end_date'])?"":"ตั้งแต่";
										echo "วันที่ ".$this->center_function->ConvertToThaiDate($start_date);
										echo (@$_GET['start_date'] == @$_GET['end_date'])?"":"  ถึงวันที่  ".$this->center_function->ConvertToThaiDate($end_date);
									?>
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
							<th class="table_header_top" style="vertical-align: middle;">ลำดับ</th>
							<th class="table_header_top" style="vertical-align: middle;">วันที่</th>
							<th class="table_header_top" style="vertical-align: middle;">เลขที่บัญชี</th>
							<th class="table_header_top" style="vertical-align: middle;">ชื่อบัญชี</th>
							<th class="table_header_top" style="vertical-align: middle;">ดอกเบี้ย</th>
							<th class="table_header_top" style="vertical-align: middle;">คงเหลือ</th>
						</tr>  
					</thead>
					<tbody>
						<?php	
							$runno = $last_runno;
							$total = 0;
							$total_deposit = 0;
							$total_withdrawal = 0;
							if(!empty($data)){
								foreach(@$data as $key => $row){
									$runno++;
									$transaction_time = strtotime($row['transaction_time']);
									$date = date('Y-m-d', $transaction_time);
									$date_format = $this->center_function->ConvertToThaiDate($date);
									$time_format = date('H:i', $transaction_time);
						?>
							<tr> 
							  <td class="table_body" style="text-align: center;vertical-align: top;"><?php echo $runno; ?></td>
							  
							  <td class="table_body" style="text-align: center;vertical-align: top;"><?php echo $date_format." ".$time_format." น";?></td>
							  <td class="table_body" style="text-align: center;vertical-align: top;">
								<?php echo @$this->center_function->format_account_number($row['account_id']); ?>
							  </td>
							  <td class="table_body" style="text-align: left;vertical-align: top;"><?php echo $row['account_name'];?></td>
							  <td class="table_body" style="text-align: right;vertical-align: top;"><?php echo number_format($row['transaction_deposit'],2);?></td>
							  <td class="table_body" style="text-align: right;vertical-align: top;"><?php echo number_format($row['transaction_balance'],2);?></td>
							</tr>											
						
						<?php	
									$total += $row['transaction_balance'];	
									$total_deposit += $row['transaction_deposit'];
									$total_balance += $row['transaction_balance'];
								}
							}
							
							$last_runno = $runno;
						?>
							<tr>
								<td colspan="4" class="table_body" style="text-align: center;vertical-align: top;">
									รวม
								</td>
								<td class="table_body" style="text-align: center;vertical-align: top;">
									<?php echo number_format($total_deposit,2)?>
								</td>
								<td class="table_body" style="text-align: center;vertical-align: top;">
									<?php echo number_format($total_balance,2)?>
								</td>
							</tr>
					</tbody>    
				</table>
		</body>
	</html>
</pre>