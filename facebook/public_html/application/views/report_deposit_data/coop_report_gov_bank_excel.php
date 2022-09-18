<?php 
header("Content-type: application/vnd.ms-excel;charset=utf-8;");
header("Content-Disposition: attachment; filename=รายงานสรุปเงินฝาก.xls"); 
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
				.title_view{
					font-family: AngsanaUPC, MS Sans Serif;
					font-size: 21px;
					font-weight: bold;
				}
				.title_view_smal{
					font-family: AngsanaUPC, MS Sans Serif;
					font-size: 18px;
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
				<table style="width: 100%;">	
					<tr>
						<td class="text-center" colspan="5">
							<span class="title_view"><?php echo @$_SESSION['COOP_NAME'];?></span><br>
							<span class="title_view">รายงานสรุป<?php echo @$type_name; ?></span><br>
							<?php if(@$_GET['apply_type_id'] != ''){?>
							<span class="title_view">ประเภทสมัครสมาชิก <?php echo @$arr_mem_apply_type[@$_GET['apply_type_id']]; ?></span><br>
							<?php } ?>
							<span class="title_view">
								<?php 
									echo (@$_GET['start_date'] == @$_GET['end_date'])?"":"ตั้งแต่";
									echo "วันที่ ".$this->center_function->ConvertToThaiDate($start_date);
									echo (@$_GET['start_date'] == @$_GET['end_date'] || @$_GET['end_date'] == '')?"":"  ถึงวันที่  ".$this->center_function->ConvertToThaiDate($end_date);
								?>
							</span>
						 </td>
					</tr>					
					<tr>
						<td colspan="5" style="text-align: left;">
							<span class="title_view_smal">วันที่ <?php echo $this->center_function->ConvertToThaiDate(@date('Y-m-d'),1,0);?></span>				
							<span class="title_view_smal">   เวลา <?php echo date('H:i:s');?></span>	
						</td>
					</tr>
				</table>
				
				<table class="table table-bordered">
					<thead> 
						<tr>
							<th class="table_header_top" style="width: 40px;vertical-align: middle;">ลำดับ</th>
							<th class="table_header_top" style="width: 100px;vertical-align: middle;">เลขที่บัญชี</th>
							<th class="table_header_top" style="width: 80px;vertical-align: middle;">ชื่อ-นามสกุล</th>
							<th class="table_header_top" style="width: 60px;vertical-align: middle;">รหัสสมาชิก</th>
							<th class="table_header_top" style="width: 80px;vertical-align: middle;">ยอดเงิน</th>
						</tr>  
					</thead>
					<tbody>
						<?php	
							$runno = $last_runno;
							$total = 0;
							if(!empty($data)){
								foreach(@$data as $key => $row){
									$runno++;
						?>
							<tr> 
								<td class="table_body" style="text-align: center;vertical-align: top;"><?php echo $runno; ?></td>
								<td class="table_body" style="text-align: center;vertical-align: top;">
									<?php echo @$this->center_function->format_account_number($row['account_id']); ?>
								</td>					 
								<td class="table_body" style="text-align: left;vertical-align: top;"><?php echo $row['member_name'];?></td>
								<td class="table_body" style="text-align: center;vertical-align: top;mso-number-format:'\@';"><?php echo $row['member_id'];?></td>
								<td class="table_body" style="text-align: right;vertical-align: top;"><?php echo number_format($row['transaction_balance'],2);?></td>
							</tr>											
						
						<?php	
								$total += $row['transaction_balance'];						
								}
							}
							$last_runno = $runno;
						?>
							<tr>
								<td colspan="4" class="table_body" style="text-align: center;vertical-align: top;">
									รวม
								</td>
								<td class="table_body" style="text-align: center;vertical-align: top;">
									<?php echo number_format($total,2)?>
								</td>
							</tr>
					</tbody>    
				</table>
		</body>
	</html>
</pre>