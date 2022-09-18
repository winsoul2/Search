<?php 
header("Content-type: application/vnd.ms-excel;charset=utf-8;");
header("Content-Disposition: attachment; filename=รายงานสรุปทุนเรือนหุ้น-เงินกู้คงเหลือ  ตามหน่วยงานย่อย.xls");
date_default_timezone_set('Asia/Bangkok');

//echo "<pre>"; print_r($data); exit;

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
//echo '<pre>'; print_r($_GET); echo '</pre>';
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
if(!empty($data)){
	foreach(@$data AS $page=>$data_row){
	?>
				<table class="table table-bordered">	
					<tr>
						<tr>
							<th class="table_title" colspan="10"><?php echo @$_SESSION['COOP_NAME'];?></th>
						</tr>
						<tr>
							<th class="table_title" colspan="10">รายงานสรุปทุนเรือนหุ้น-เงินกู้คงเหลือ  ประจำวัน  (ตามหน่วยงานย่อย,ประเภทเงินกู้หลัก)</th>
						</tr>
						<tr>
							<th class="table_title" colspan="10">
								<?php 								
									$title_date = (@$_GET['type_date'] == '1')?'ณ วันที่':'ประจำวันที่';								
									echo @$title_date." ".$this->center_function->ConvertToThaiDate($start_date);
								?>
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
							<th rowspan="3" class="table_header_top" style="width: 40px;vertical-align: middle;">รหัส</th>
							<th rowspan="3" class="table_header_top" style="width: 300px;vertical-align: middle;">หน่วยงาน</th>
                            <th rowspan="2" colspan="4" class="table_header_top">จำนวนสมาชิก</th>
							<th rowspan="3" class="table_header_top" style="width: 80px;vertical-align: middle;">หุ้น</th>
							<?php 
							foreach($loan_type AS $key=>$row_loan_type){
							?>
							<th colspan="1" class="table_header_top" style="width: 80px;vertical-align: middle;"><?php echo str_replace('เงินกู้','',$row_loan_type['loan_type']);?></th>
							<?php }?>
						</tr> 
                        <tr>
                            <th class="table_header_top">สามัญโครงการ</th>
                            <th class="table_header_top">สามัญสะสม</th>
                            <th class="table_header_top">เงินกู้พิเศษเพื่อการเคหะสงเคราะห์</th>
                        </tr>
                        <tr>
                            <th class="table_header_top">ทั้งหมด</th>
                            <th class="table_header_top">มีหนี้</th>
                            <th class="table_header_top">ชาย</th>
                            <th class="table_header_top">หญิง</th>
                            <th class="table_header_top">สามัญเพื่อการศึกษา</th>
                            <th class="table_header_top">สามัญโครงการพิเศษเพื่อช่วยเหลือสมาชิกที่กู้เงิน</th>
                            <th class="table_header_top">&nbsp;</th>
                        </tr>
					</thead>
					<tbody>
					<?php
						$runno = $last_runno;
						$all_share_person = 0;
						$all_share_collect = 0;
						$all_loan_emergent_person = 0;
						$all_loan_emergent_balance = 0;
						$all_loan_normal_person = 0;
						$all_loan_normal_balance = 0;
						$all_loan_special_person = 0;
						$all_loan_special_balance = 0;
						$all_total_loan_balance = 0;
						$all_total_loan_balance = 0;
						$all_share_balance_subdivision = 0;
						$all_loan_balance_subdivision = 0;
						if(!empty($data_row)){
							foreach(@$data_row as $key => $rows){

							    foreach ($rows as $index => $row ) {
                                    $runno++;
                                    $total_share_person += @$row['share_person'];
                                    $total_share_collect += @$row['share_collect'];
                                    $total_loan_emergent_person += @$row['loan_emergent_person'];
                                    $total_loan_emergent_balance += @$row['loan_emergent_balance'];
                                    $total_loan_normal_person += @$row['loan_normal_person'];;
                                    $total_loan_normal_balance += @$row['loan_normal_balance'];
                                    $total_loan_special_person += @$row['loan_special_person'];
                                    $total_loan_special_balance += @$row['loan_special_balance'];

                                    $mem_all_amt += @$row['mem_all_amt'];
                                    $mem_has_debt_amt += @$row['mem_has_debt'];
                                    $mem_mem_amt += @$row['mem_men'];
                                    $mem_women_amt += @$row['mem_wemen'];
                                    ?>
                                    <tr>
                                <td class="table_body"
                                    style='text-align: center;vertical-align: top;mso-number-format:"\@";'><?php echo @$row['mem_group_id']; ?></td>
                                <td class="table_body"
                                    style="text-align: left;vertical-align: top;"><?php echo @$row['mem_group_name']; ?></td>
                                <td class="table_body"
                                    style="text-align: right;vertical-align: top;"><?php echo number_format(@$row['mem_all_amt']); ?></td>
                                <td class="table_body"
                                    style="text-align: right;vertical-align: top;"><?php echo number_format(@$row['mem_has_debt']); ?></td>
                                <td class="table_body"
                                    style="text-align: right;vertical-align: top;"><?php echo number_format(@$row['mem_men']); ?></td>
                                <td class="table_body"
                                    style="text-align: right;vertical-align: top;"><?php echo number_format(@$row['mem_wemen']); ?></td>
                                <td class="table_body"
                                    style="text-align: right;vertical-align: top;"><?php echo number_format(@$row['share_collect'], 2); ?></td>
                                <td class="table_body"
                                    style="text-align: right;vertical-align: top;"><?php echo number_format(@$row['loan_emergent_balance'], 2); ?></td>
                                <td class="table_body"
                                    style="text-align: right;vertical-align: top;"><?php echo number_format(@$row['loan_normal_balance'], 2); ?></td>
                                <td class="table_body"
                                    style="text-align: right;vertical-align: top;"><?php echo number_format(@$row['loan_special_balance'], 2); ?></td>
							</tr>

                                    <?php
                                }
							}
						}
						$last_runno = $runno;
						$all_share_person += @$total_share_person;
						$all_share_collect += @$total_share_collect;
						$all_loan_emergent_person += @$total_loan_emergent_person;
						$all_loan_emergent_balance += @$total_loan_emergent_balance;
						$all_loan_normal_person += @$total_loan_normal_person;
						$all_loan_normal_balance += @$total_loan_normal_balance;
						$all_loan_special_person += @$total_loan_special_person;
						$all_loan_special_balance += @$total_loan_special_balance;
						$all_total_loan_balance += @$total_total_loan_balance;
						$all_share_balance_subdivision += @$total_share_balance_subdivision;
						$all_loan_balance_subdivision += @$total_loan_balance_subdivision;

                        $all_mem_all_amt += $mem_all_amt;
                        $all_mem_has_debt_amt += $mem_has_debt_amt;
                        $all_mem_mem_amt += $mem_mem_amt;
                        $all_mem_women_amt += $mem_women_amt;
						
						if(@$page == @$page_all){	
					?> 
						<tr> 
                            <td rowspan="3" class="table_body" style="text-align: center;vertical-align: top;" colspan="2">รวมทั้งสิ้น</td>
                            <td rowspan="3" class="table_body" style="text-align: right;vertical-align: top;"><?php echo number_format($all_mem_all_amt); ?></td>
                            <td rowspan="3" class="table_body" style="text-align: right;vertical-align: top;"><?php echo number_format($all_mem_has_debt_amt); ?></td>
                            <td rowspan="3" class="table_body" style="text-align: right;vertical-align: top;"><?php echo number_format($all_mem_mem_amt); ?></td>
                            <td rowspan="3" class="table_body" style="text-align: right;vertical-align: top;"><?php echo number_format($all_mem_women_amt); ?></td>
                            <td rowspan="3" class="table_body" style="text-align: right;vertical-align: top;"><?php echo number_format(@$all_share_collect,2); ?></td>
                            <?php foreach (@$summary[0] as $key => $value) { ?>
                                <td style="text-align: right;vertical-align: top;" class="table_body"><?php echo number_format(@$value,2); ?></td>
                            <?php } ?>
						</tr>
                            <tr>
                            <?php foreach (@$summary[1]  as $key => $value) { ?>
                                <td style="text-align: right;vertical-align: top;" class="table_body"><?php echo number_format(@$value,2); ?></td>
                            <?php } ?>
                        </tr>
                            <tr>
                            <?php foreach (@$summary[2]  as $key => $value) { ?>
                                <td style="text-align: right;vertical-align: top;" class="table_body"><?php echo number_format(@$value,2); ?></td>
                            <?php } ?>
                        </tr>
					<?php 
						}
					?>	
					</tbody>    
				</table>
<?php 
	}
} 
?>


		</body>
	</html>
</pre>
