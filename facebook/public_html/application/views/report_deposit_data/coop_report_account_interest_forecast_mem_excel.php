<?php 
header("Content-type: application/vnd.ms-excel;charset=utf-8;");
header("Content-Disposition: attachment; filename=รายงานดอกเบี้ยเงินฝากล่วงหน้า.xls"); 
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
							<th class="table_title" colspan="5"><?php echo @$_SESSION['COOP_NAME'];?></th>
						</tr>
						<tr>
							<th class="table_title" colspan="5">รายงานดอกเบี้ยเงินฝากล่วงหน้า <?php echo $type_name;?></th>
						</tr>
						<tr>
							<th class="table_title" colspan="5">
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
							<th class="table_title_right" colspan="5">วันที่ <?php echo $this->center_function->ConvertToThaiDate(@date('Y-m-d'),0,0);?></th>
						</tr>
						<tr>
							<th class="table_title_right" colspan="5">ผู้ทำรายการ <?php echo $_SESSION['USER_NAME'];?></th>
						</tr>
					</tr> 
				</table>
				
				<?php
				if(!empty($master)){
					foreach($master as $key => $mas) { ?>
						<table class="table table-bordered">
							<tr>
								<th class="table_title" colspan="5"><?php echo $mas["type_code"]." ".$mas["type_name"]; ?></th>
							</tr>
						</table>
						
						<table class="table table-bordered">
							<thead> 
								<tr>
									<th class="table_header_top" style="vertical-align: middle;">ลำดับ</th>
									<th class="table_header_top" style="vertical-align: middle;">เลขบัญชี</th>
									<th class="table_header_top" style="vertical-align: middle;">รหัสสมาชิก</th>
									<th class="table_header_top" style="vertical-align: middle;">ชื่อสกุล</th>
									<th class="table_header_top" style="vertical-align: middle;">ดอกเบี้ย</th>
								</tr>  
							</thead>
							<tbody>
								<?php	
									$runno = 0;
									$total = 0;
									$total_deposit = 0;
									$total_withdrawal = 0;
									if(!empty($data[$key])){
										foreach(@$data[$key] as $key2 => $row){
								?>
									<tr> 
									  <td class="table_body" style="text-align: center;vertical-align: top;"><?php echo ++$runno; ?></td>
									  <td class="table_body" style="text-align: center;vertical-align: top;"><?php echo $row['account_id'];?></td>
									  <td class="table_body" style="text-align: center;vertical-align: top;"><?php echo $row['mem_id'];?></td>
									  <td class="table_body" style="text-align: left;vertical-align: top;"><?php echo $row['prename_short'].$row['firstname_th'].' '.$row['lastname_th'];?></td>
									  <td class="table_body" style="text-align: right;vertical-align: top;"><?php echo number_format($row['transaction_deposit'],2);?></td>
									</tr>											
								
								<?php	
											$total_deposit += $row['transaction_deposit'];
										}
									}
								?>
									<tr>
										<td colspan="4" class="table_body" style="text-align: center;vertical-align: top;">
											รวม
										</td>
										<td class="table_body" style="text-align: center;vertical-align: top;">
											<?php echo number_format($total_deposit,2)?>
										</td>
									</tr>
							</tbody>    
						</table>
					<?php }
				}
				?>
		</body>
	</html>
</pre>