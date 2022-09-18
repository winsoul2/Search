<?php 
header("Content-type: application/vnd.ms-excel;charset=utf-8;");
header("Content-Disposition: attachment; filename=รายงานการรับเบิก.xls"); 
date_default_timezone_set('Asia/Bangkok');

$month_arr = array('1'=>'มกราคม','2'=>'กุมภาพันธ์','3'=>'มีนาคม','4'=>'เมษายน','5'=>'พฤษภาคม','6'=>'มิถุนายน','7'=>'กรกฎาคม','8'=>'สิงหาคม','9'=>'กันยายน','10'=>'ตุลาคม','11'=>'พฤศจิกายน','12'=>'ธันวาคม');
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
				<table class="table table-bordered">
					<tr>
						<tr>
							<th class="table_title" colspan="6"><?php echo @$_SESSION['COOP_NAME'];?></th>
						</tr>
						<tr>
							<th class="table_title" colspan="6">รายงานการ<?php echo $_GET["pickup_type"] == "0" ? "รับพัสดุ" : "เบิกพัสดุ" ?></th>
						</tr>
						<tr>
							<th class="table_title" colspan="6">
								<h3 class="title_view"><?php echo $month_arr[$_GET["month"]]." ".$_GET["year"];?></h3>
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
							<th class="table_header_top" style="vertical-align: middle;">หมวดพัสดุ</th>
							<th class="table_header_top" style="vertical-align: middle;">วันที่<?php echo $_GET["pickup_type"] == "0" ? "รับ" : "เบิก" ?></th>
							<th class="table_header_top" style="vertical-align: middle;">เลขพัสดุหลัก</th>
							<th class="table_header_top" style="vertical-align: middle;">รายการ</th>
							<th class="table_header_top" style="vertical-align: middle;">จำนวน</th>
							<th class="table_header_top" style="vertical-align: middle;">หน่วยงาน</th>
						</tr>  
					</thead>
					<tbody>
						<?php	
							$runno = 0;
							$total_qty = 0;
							if(!empty($data)){
								foreach(@$data as $key => $row){
									$runno++;
									$total_qty += $row['qty'];
						?>
							<tr> 
							  <td class="table_body" style="text-align: center;vertical-align: top;"><?php echo $runno; ?></td>
							  <td class="table_body" style="text-align: center;vertical-align: top;"><?php echo $row['facility_type_name'];?></td>
							  <td class="table_body" style="text-align: center;vertical-align: top;"><?php echo $this->center_function->ConvertToThaiDate($row[$_GET["pickup_type"] == "0" ? "receive_date" : "sign_date"],true,false);?></td>
							  <td class="table_body" style="text-align: center;vertical-align: top;"><?php echo $row['facility_main_code'];?></td>
							  <td class="table_body" style="text-align: left;vertical-align: top;"><?php echo $row['store_name'];?></td>
							  <td class="table_body" style="text-align: right;vertical-align: top;"><?php echo number_format($row['qty']);?></td>
							  <td class="table_body" style="text-align: center;vertical-align: top;"><?php echo $row['department_name'];?></td>
							</tr>
						<?php
								}
							}
							
							if($page == $page_all) {
						?>
						
							<tr>
								<td class="table_body" colspan="5" style="text-align: center;vertical-align: top;">รวม</td>
								<td class="table_body" style="text-align: right;vertical-align: top;"><?php echo number_format($total_qty);?></td>
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