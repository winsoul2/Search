<?php 
header("Content-type: application/vnd.ms-excel;charset=utf-8;");
header("Content-Disposition: attachment; filename=รายงานพัสดุเปรียบเทียบ.xls"); 
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
				<table class="table table-bordered">
					<tr>
						<tr>
							<th class="table_title" colspan="10"><?php echo @$_SESSION['COOP_NAME'];?></th>
						</tr>
						<tr>
							<th class="table_title" colspan="10">รายงานพัสดุเปรียบเทียบ</th>
						</tr>
						<tr>
							<th class="table_title" colspan="10">
								<h3 class="title_view"></h3>
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
							<th class="table_header_top" style="vertical-align: middle;">หมวดพัสดุ</th>
							<th class="table_header_top" style="vertical-align: middle;">เลขพัสดุหลัก</th>
							<th class="table_header_top" style="vertical-align: middle;">รายการ</th>
							<th class="table_header_top" style="vertical-align: middle;">ปี</th>
							<th class="table_header_top" style="vertical-align: middle;">จำนวน</th>
							<th class="table_header_top" style="vertical-align: middle;">ราคา</th>
							<th class="table_header_top" style="vertical-align: middle;">ปี</th>
							<th class="table_header_top" style="vertical-align: middle;">จำนวน</th>
							<th class="table_header_top" style="vertical-align: middle;">ราคา</th>
						</tr>  
					</thead>
					<tbody>
						<?php	
							$runno = 0;
							$totals['qty1'] = 0;
							$totals['store_price1'] = 0;
							$totals['qty2'] = 0;
							$totals['store_price2'] = 0;
							if(!empty($data)){
								foreach(@$data as $key => $row){
									$runno++;
									$totals['qty1'] += $row['qty1'];
									$totals['store_price1'] += $row['store_price1'];
									$totals['qty2'] += $row['qty2'];
									$totals['store_price2'] += $row['store_price2'];
						?>
							<tr> 
							  <td class="table_body" style="text-align: center;vertical-align: top;"><?php echo $runno; ?></td>
							  <td class="table_body" style="text-align: center;vertical-align: top;"><?php echo $row['facility_type_name'];?></td>
							  <td class="table_body" style="text-align: center;vertical-align: top;"><?php echo $row['facility_main_code'];?></td>
							  <td class="table_body" style="text-align: left;vertical-align: top;"><?php echo $row['store_name'];?></td>
							  <td class="table_body" style="text-align: center;vertical-align: top;"><?php echo $row['year1'];?></td>
							  <td class="table_body" style="text-align: right;vertical-align: top;"><?php echo number_format($row['qty1']);?></td>
							  <td class="table_body" style="text-align: right;vertical-align: top;"><?php echo number_format($row['store_price1'],2);?></td>
							  <td class="table_body" style="text-align: center;vertical-align: top;"><?php echo $row['year2'];?></td>
							  <td class="table_body" style="text-align: right;vertical-align: top;"><?php echo number_format($row['qty2']);?></td>
							  <td class="table_body" style="text-align: right;vertical-align: top;"><?php echo number_format($row['store_price2'],2);?></td>
							</tr>
						<?php
								}
							}
							
							if($page == $page_all) {
						?>
						
							<tr>
								<td class="table_body" colspan="5" style="text-align: center;vertical-align: top;">รวม</td>
								<td class="table_body" style="text-align: right;vertical-align: top;"><?php echo number_format($totals['qty1']);?></td>
								<td class="table_body" style="text-align: right;vertical-align: top;"><?php echo number_format($totals['store_price1'],2);?></td>
								<td class="table_body" style="text-align: center;vertical-align: top;"></td>
								<td class="table_body" style="text-align: right;vertical-align: top;"><?php echo number_format($totals['qty2']);?></td>
								<td class="table_body" style="text-align: right;vertical-align: top;"><?php echo number_format($totals['store_price2'],2);?></td>
							</tr>
						<?php
							}
						?>
					</tbody>    
				</table>
		</body>
	</html>
</pre>