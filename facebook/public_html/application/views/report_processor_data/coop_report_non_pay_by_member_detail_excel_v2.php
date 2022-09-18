<?php
header("Content-type: application/vnd.ms-excel;charset=utf-8;");
header("Content-Disposition: attachment; filename=รายงานเก็บรายเดือนไม่ครบ.xls"); 
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
	if(@$_GET['month']!='' && @$_GET['year']!=''){
		$day = '';
		$month = @$_GET['month'];
		$year = (@$_GET['year']);
		$title_date = " เดือน ".@$month_arr[$month]." ปี ".(@$year);
	}else{
		$day = '';
		$month = '';
		$year = (@$_GET['year']);
		$title_date = " ปี ".(@$year);
	}

	?>
			<table class="table table-bordered">
				<tr>
					<tr>
						<th class="table_title" colspan="9"><?php echo @$_SESSION['COOP_NAME'];?></th>
					</tr>
					<tr>
						<th class="table_title" colspan="9">รายงานเก็บรายเดือนไม่ครบ</th>
					</tr>
					<tr>
						<th class="table_title" colspan="9"><?php echo " ประจำ ".$title_date;?></th>
					</tr>
					<tr>
						<th class="table_title_right" colspan="9">วันที่ <?php echo $this->center_function->ConvertToThaiDate(@date('Y-m-d'),0,0);?></th>
					</tr>
					<tr>
						<th class="table_title_right" colspan="9">ผู้ทำรายการ <?php echo $_SESSION['USER_NAME'];?></th>
					</tr>
				</tr>
			</table>
			<?php	
				if(!empty($row_group)){
					$page=1;
					foreach(@$row_group AS $key_group => $value_group){
						if(!empty($value_group['non_pay_data'])){
						$runno = 0;
			?>
			<table class="table table-bordered">
				<thead> 
					<tr> 
						<th class="table_header_top" style="text-align:left;vertical-align: middle;" colspan="9"><?php echo ":: ".$value_group['mem_group_name']; ?></th>
					</tr>
					<tr>
						<th class="table_header_top" style="vertical-align: middle;">ลำดับ</th>
						<th class="table_header_top" style="vertical-align: middle;">หน่วยงานย่อย</th>
						<th class="table_header_top" style="vertical-align: middle;">รูปแบบ</th>
						<th class="table_header_top" style="vertical-align: middle;">เลขที่สมาชิก</th>
						<th class="table_header_top" style="vertical-align: middle;">ชื่อ-นามสกุล</th>
						<th class="table_header_top" style="vertical-align: middle;">เงินเรียกเก็บ</th>
						<th class="table_header_top" style="vertical-align: middle;">เงินเก็บไม่ได้</th>
						<th class="table_header_top" style="vertical-align: middle;">เงินเก็บได้</th>
						<th class="table_header_top" style="vertical-align: middle;">สาเหตุที่เก็บไม่ได้</th>
					</tr>
				</thead>
				<tbody>
				
				<?php
						for($i=1;$i<=count($value_group['non_pay_data']);$i++){
						if(!empty($value_group['non_pay_data'][$i])){
							foreach(@$value_group['non_pay_data'][$i] as $key2 => $row){
								$runno++;
					?>
							<tr> 
								<td class="table_body" style="text-align: center;"><?php echo @$runno; ?></td>
								<td class="table_body" style="text-align: left;"><?php echo @$row['mem_group_name']; ?></td>
								<td class="table_body" style="text-align: left;"><?php echo (@$row['mem_type_id'] != '')?@$row['mem_type_name']:'ไม่ระบุ'; ?></td>
								<td class="table_body" style="text-align: center;"><?php echo @$row['member_id']; ?></td>
								<td class="table_body" style="text-align: left;"><?php echo @$row['member_name']; ?></td>
								<td class="table_body" style="text-align: right;"><?php echo number_format(@$row['pay_amount'],2); ?></td>
								<td class="table_body" style="text-align: right;"><?php echo number_format(@$row['non_pay_amount'],2); ?></td>
								<td class="table_body" style="text-align: right;"><?php echo number_format(@$row['balance'],2); ?></td>
								<td class="table_body" style="text-align: left;"><?php echo @$row['non_pay_reason']; ?></td>
							</tr>										
					
					<?php									
							}
					?>
							<tr> 
								<td class="table_body" style="text-align: center;" colspan="5">รวม <?php echo $value_group['mem_group_name'].":: ".count($value_group['non_pay_data'][$i])." รายการ"; ?></td>
								<td class="table_body" style="text-align: right;"><?php echo number_format(@$total_data[$value_group['id']]['pay_amount'],2); ?></td>
								<td class="table_body" style="text-align: right;"><?php echo number_format(@$total_data[$value_group['id']]['non_pay_amount'],2); ?></td>
								<td class="table_body" style="text-align: right;"><?php echo number_format(@$total_data[$value_group['id']]['balance'],2); ?></td>
								<td class="table_body" style="text-align: right;"></td>
							</tr>
					<?php		
						}
					?>

					<?php 
							}
					?>
					<tr class="foot-border"> 
						<td class="table_body" style="text-align: center;font-weight:bold;" colspan="5">รวมทั้งหมด</td>
						<td class="table_body" style="text-align: right;"><?php echo number_format(@$total_all_data['pay_amount'],2); ?></td>
						<td class="table_body" style="text-align: right;"><?php echo number_format(@$total_all_data['non_pay_amount'],2); ?></td>
						<td class="table_body" style="text-align: right;"><?php echo number_format(@$total_all_data['balance'],2); ?></td>
						<td class="table_body" style="text-align: right;"></td>
					</tr>
				</tbody>
			</table>
			<?php 
							
						}
					}
				} 
			?>
		</body>
	</html>
</pre>