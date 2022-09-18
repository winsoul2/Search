<?php 
header("Content-type: application/vnd.ms-excel;charset=utf-8;");
header("Content-Disposition: attachment; filename=รายงานการชำระเงินกลุ่มประนอมหนี้.xls"); 
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
							<th class="table_title" colspan="12"><?php echo @$_SESSION['COOP_NAME'];?></th>
						</tr>
						<tr>
							<th class="table_title" colspan="12">รายงานการชำระเงินกลุ่มประนอมหนี้ <?php echo $type_name;?></th>
						</tr>
						<tr>
							<th class="table_title" colspan="12">
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
							<th class="table_title_right" colspan="12">วันที่ <?php echo $this->center_function->ConvertToThaiDate(@date('Y-m-d'),0,0);?></th>
						</tr>
						<tr>
							<th class="table_title_right" colspan="12">ผู้ทำรายการ <?php echo $_SESSION['USER_NAME'];?></th>
						</tr>
					</tr>
				</table>
				<table class="table table-bordered">
					<thead>
						<tr>
							<th class="table_header_top" rowspan="2" style="vertical-align: middle;">ลำดับ</th>
							<th class="table_header_top" rowspan="2" style="vertical-align: middle;">วันที่ชำระ</th>
							<th class="table_header_top" rowspan="2" style="vertical-align: middle;">รหัส</th>
							<th class="table_header_top" rowspan="2" style="vertical-align: middle;">ชื่อ-นามสกุล</th>
							<th class="table_header_top" rowspan="2" style="vertical-align: middle;">เลขสัญญา</th>
							<th class="table_header_top" rowspan="2" style="vertical-align: middle;">เลขที่ใบเสร็จ</th>
							<th class="table_header_top" colspan="3" style="vertical-align: middle;">ยอดชำระ</th>
							<th class="table_header_top" rowspan="2" style="vertical-align: middle;">รวม</th>
							<th class="table_header_top" rowspan="2" style="vertical-align: middle;">กองทุน(บาท)</th>
							<th class="table_header_top" rowspan="2" style="vertical-align: middle;">สถานะ</th>
						</tr>
						<tr>
							<th class="table_header_top">
								เงินต้น
							</th>
							<th class="table_header_top">
								ดอกเบี้ย
							</th>
							<th class="table_header_top">
								ดอกเบี้ยคงค้าง
							</th>
						</tr>
					</thead>
					<tbody>
					<?php
						$loaner = 0;
						$guarantor = 0;
						foreach($datas as $key => $row){
							$runno++;
							if($row["type"] == 1) {
								$guarantor++;
							} else {
								$loaner++;
							}
					?>
						<tr>
							<td class="table_body" style="text-align: center;vertical-align: top;"><?php echo $runno; ?></td>
							<td class="table_body" style="text-align: center;vertical-align: top;"><?php echo ($row['payment_date'])?$this->center_function->ConvertToThaiDate($row['payment_date'],1,0):"";?></td>
							<td class="table_body" style="text-align: center;vertical-align: top;"><?php echo $row["member_id"]; ?></td>
							<td class="table_body" style="text-align: left;vertical-align: top;"><?php echo $row["prename_full"].$row["firstname_th"]." ".$row["lastname_th"]; ?></td>
							<td class="table_body" style="text-align: center;vertical-align: top;"><?php echo $row["contract_number"]; ?></td>
							<td class="table_body" style="text-align: center;vertical-align: top;"><?php echo $row["receipt_id"]; ?></td>
							<td class="table_body" style="text-align: right;vertical-align: top;"><?php echo number_format($row["principal_payment"],2); ?></td>
							<td class="table_body" style="text-align: right;vertical-align: top;"><?php echo number_format($row["interest"],2); ?></td>
							<td class="table_body" style="text-align: right;vertical-align: top;"><?php echo number_format($row["loan_interest_remain"],2); ?></td>
							<td class="table_body" style="text-align: right;vertical-align: top;"><?php echo number_format($row["principal_payment"] + $row["interest"] + $row["loan_interest_remain"],2); ?></td>					
							<td class="table_body" style="text-align: right;vertical-align: top;"><?php echo number_format($row["fund_support"],2); ?></td>
							<td class="table_body" style="text-align: center;vertical-align: top;"><?php echo $loan_status[$row['loan_status']]; ?></td>
						</tr>
					<?php
						}
					?>
						<tr class="table_body">
							<td></td>
							<td colspan="2">หมายเหตุ เลือกรายการเป็น</td>
							<td>ทั้งหมด</td>
							<td><?php echo $guarantor+$loaner;?></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
						</tr>
						<tr class="table_body">
							<td></td>
							<td></td>
							<td></td>
							<td>ผู็กู้</td>
							<td><?php echo $loaner;?></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
						</tr>
						<tr class="table_body">
							<td></td>
							<td></td>
							<td></td>
							<td>ผู้ค้ำ</td>
							<td><?php echo $guarantor;?></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
						</tr>
					</tbody>
				</table>
		</body>
	</html>
</pre>