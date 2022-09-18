<?php 
header("Content-type: application/vnd.ms-excel;charset=utf-8;");
header("Content-Disposition: attachment; filename=รายงานการหักรายการฝากเงินเข้าบัญชีสีชมพู.xls"); 
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
				if($_GET['start_date']){
					$start_date_arr = explode('/',@$_GET['start_date']);
					$start_day = $start_date_arr[0];
					$start_month = $start_date_arr[1];
					$start_year = $start_date_arr[2];
					$start_year -= 543;
					$start_date = $start_year.'-'.$start_month.'-'.$start_day;
				}

				if($_GET['end_date']){
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
					<th class="table_title" colspan="9"><?php echo @$_SESSION['COOP_NAME'];?></th>
				</tr>
				<tr>
					<th class="table_title" colspan="9">รายงานการหักรายการฝากเงินเข้าบัญชีสีชมพู<?php echo $type_name;?></th>
				</tr>
				<tr>
					<th class="table_title" colspan="9">
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
					<td colspan="10" class="table_title_right">
						<span class="title_view">วันที่ <?php echo $this->center_function->ConvertToThaiDate(@date('Y-m-d'),1,0);?></span>
					</td>
				</tr>
				<tr>
					<td colspan="10" class="table_title_right">
						<span class="title_view">เวลา <?php echo date('H:i:s');?></span>
					</td>
				</tr>
				<tr>
					<td colspan="10" class="table_title_right">
						<span class="title_view">ผู้ทำรายการ <?php echo $_SESSION['USER_NAME'];?></span>
					</td>
				</tr>
			</table>
			<table class="table table-bordered">
				<thead>
					<tr>
						<th class="table_header_top" colspan="9" style="text-align: left;">
							ประเภทบัญชี : เงินฝากออมทรัพย์
						</th>
					</tr>
					<tr>
						<th class="table_header_top" style="vertical-align: middle;">ลำดับ</th>
						<th class="table_header_top" style="vertical-align: middle;">วันที่</th>
						<th class="table_header_top" style="vertical-align: middle;">เลขบัญชี</th>
						<th class="table_header_top" style="vertical-align: middle;">ชื่อ-นามสกุล</th>
						<th class="table_header_top" style="vertical-align: middle;">เลขสมาชิก</th>
						<th class="table_header_top" style="vertical-align: middle;">เงินเดือน</th>
						<th class="table_header_top" style="vertical-align: middle;">ส่ง</th>
						<th class="table_header_top" style="vertical-align: middle;">หน่วย</th>
						<th class="table_header_top" style="vertical-align: middle;">ผู้ทำรายการ</th>
					</tr>
				</thead>
				<tbody>
					<?php
						$runno = 0;	
						foreach($datas as $key => $row){
							$runno++;
					?>
					<tr>
						<td class="table_body" style="text-align: center;vertical-align: top;"><?php echo $runno; ?></td>
						<td class="table_body" style="text-align: center;vertical-align: top;"><?php echo ($row['transaction_time'])?$this->center_function->ConvertToThaiDate($row['transaction_time'],1,0):"";?></td>
						<td class="table_body" style="text-align: center;vertical-align: top;mso-number-format:'@';">
							<?php echo @$this->center_function->format_account_number($row['account_id']); ?>
						</td>
						<td class="table_body" style="text-align: left;vertical-align: top;"><?php echo $row['prename_full'].$row["firstname_th"]." ".$row["lastname_th"];?></td>
						<td class="table_body" style="text-align: center;vertical-align: top;mso-number-format:'@';"><?php echo $row['member_id'];?></td>
						<td class="table_body" style="text-align: right;vertical-align: top;"><?php echo number_format($row['salary'],2); ?></td>
						<td class="table_body" style="text-align: right;vertical-align: top;"><?php echo number_format($row['transaction_deposit'],2); ?></td>
						<td class="table_body" style="text-align: left;vertical-align: top;"><?php echo $row["level_name"];?></td>
						<td class="table_body" style="text-align: center;vertical-align: top;"><?php echo $row["user_name"];?></td>
					</tr>
					<?php
						}
					?>
				</tbody>
			</table>
		</body>
	</html>
</pre>