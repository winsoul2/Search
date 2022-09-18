<?php
header("Content-type: application/vnd.ms-excel;charset=utf-8;");
header("Content-Disposition: attachment; filename=รายงานหุ้น หนี้ และเงินฝากของสมาชิก.xls");
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

?>
				<table class="table table-bordered">
					<tr>
						<tr>
							<th class="table_title" colspan="23"><?php echo @$_SESSION['COOP_NAME'];?></th>
						</tr>
						<tr>
							<th class="table_title" colspan="23">ายงานหุ้น หนี้ และเงินฝากของสมาชิก</th>
						</tr>
						<tr>
							<th class="table_title" colspan="23">
								<?php
								$title_date = (@$_GET['type_date'] == '1')?'ณ วันที่':'ประจำวันที่';
								echo @$title_date." ".$this->center_function->ConvertToThaiDate($start_date);
								?>
							</th>
						</tr>
						<tr>
							<th class="table_title_right" colspan="23">วันที่ <?php echo $this->center_function->ConvertToThaiDate(@date('Y-m-d'),0,0);?></th>
						</tr>
						<tr>
							<th class="table_title_right" colspan="23">ผู้ทำรายการ <?php echo $_SESSION['USER_NAME'];?></th>
						</tr>
					</tr>
				</table>

				<table class="table table-bordered">
					<thead>
						<tr>
							<th class="table_header_top" rowspan="2" style="width: 40px;vertical-align: middle;">เลขที่สมาชิก</th>
							<th class="table_header_top" rowspan="2" style="width: 160px;vertical-align: middle;">ชื่อ - นามสกุล</th>
							<th class="table_header_top" rowspan="2" style="width: 70px;vertical-align: middle;">รหัสที่อยู่จัดส่ง</th>
							<th class="table_header_top" rowspan="2" style="vertical-align: middle;">หน่วยงานหลัก::หน่วยงานรอง</th>
							<th class="table_header_top" rowspan="2" style="width: 150px;vertical-align: middle;">หน่วยงานย่อย</th>
							<th class="table_header_top" rowspan="2" style="width: 50px;vertical-align: middle;">ที่อยู่ในการจัดส่งเอกสาร รฟท.</th>
							<th class="table_header_top" rowspan="2" style="width: 40px;vertical-align: middle;">ลำดับ</th>
							<th class="table_header_top" rowspan="2" style="width: 40px;vertical-align: middle;">งวดหุ้น</th>
							<th class="table_header_top" rowspan="2" style="width: 40px;vertical-align: middle;">ทุนเรือนหุ้น</th>
							<?php
							foreach($loan_type AS $key=>$row_loan_type){
								?>
								<th class="table_header_top" colspan="3" style="width: 80px;vertical-align: middle;"><?php echo str_replace('เงินกู้','',$row_loan_type['loan_type']);?></th>
							<?php }?>
								<th class="table_header_top" colspan="2" style="width: 80px;vertical-align: middle;">เงินฝาก</th>
						</tr>
						<tr>
							<?php
							foreach($loan_type AS $key=>$row_loan_type){
								?>
								<th class="table_header_top" style="width: 40px;vertical-align: middle;">งวด</th>
								<th class="table_header_top" style="width: 60px;vertical-align: middle;">เลขที่สัญญา</th>
								<th class="table_header_top" style="width: 60px;vertical-align: middle;">เงินคงเหลือ</th>
							<?php }?>
							<th class="table_header_top" style="width: 60px;vertical-align: middle;">เลขที่บัญชีเงินฝาก</th>
							<th class="table_header_top" style="width: 60px;vertical-align: middle;">เงินคงเหลือ</th>
						</tr>
					</thead>
					<tbody>
					<?php
                    $run_data = 0;
					$runno = $last_runno;
                    $sub_no = $last_runno;
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
                    $sum_transaction = 0;
                    $all_sum_share = 0;
                    $all_sum_emergent = 0;
                    $all_sum_normal = 0;
                    $all_sum_special = 0;
                    $all_sum_transaction = 0;
					$tmp['mem_group_id'] = array();
					$tmp['member_id'] = array();
					$member_count = 0;
					$sum_share = 0;
					$sum_emergent = 0;
					$sum_normal = 0;
					$sum_special = 0;
					$member_id_past = "xx";
					if(!empty($data)){
						foreach(@$data as $da) {
                            $run_data++;
							foreach(@$da as $key => $row){
								if (!empty($row['share_collect']) || !empty($row['loan_emergent_balance']) || !empty($row['loan_normal_balance']) || !empty($row['loan_special_balance'])) {
									if($member_id_past != $row['member_id']) {
                                        $sub_no = 0;
                                        $runno++;
									}
                                    $sub_no++;

									if(!empty($tmp['mem_group_id']) && $tmp['mem_group_id'] <> $row['mem_group_id'] ) {
										?>
                                        <tr>
                                          <td class="table_body" style='text-align: center;vertical-align: top;mso-number-format:"\@";' colspan="7"> รวม </td>
                                          <td class="table_body" style="text-align: center;vertical-align: top;"></td>
                                          <td class="table_body" style="text-align: right;vertical-align: top;"><?php echo number_format(@$sum_share, 2); ?></td>
                                          <td class="table_body" style="text-align: center;vertical-align: top;"></td>
                                          <td class="table_body" style='text-align: right;vertical-align: top;mso-number-format:"\@";'></td>
                                          <td class="table_body" style="text-align: right;vertical-align: top;"><?php echo number_format(@$sum_emergent, 2); ?></td>
                                          <td class="table_body" style="text-align: center;vertical-align: top;"></td>
                                          <td class="table_body" style='text-align: right;vertical-align: top;mso-number-format:"\@";'></td>
                                          <td class="table_body" style="text-align: right;vertical-align: top;"><?php echo number_format(@$sum_normal, 2); ?></td>
                                          <td class="table_body" style="text-align: center;vertical-align: top;"></td>
                                          <td class="table_body" style='text-align: right;vertical-align: top;mso-number-format:"\@";'></td>
                                          <td class="table_body" style="text-align: right;vertical-align: top;"><?php echo number_format(@$sum_special, 2); ?></td>
                                          <td class="table_body" style="text-align: center;vertical-align: top;"></td>
                                          <td class="table_body" style='text-align: right;vertical-align: top;mso-number-format:"\@";'></td>
                                          <td class="table_body" style="text-align: right;vertical-align: top;"><?php echo number_format(@$sum_covid, 2); ?></td>
                                          <td class="table_body" style='text-align: right;vertical-align: top;mso-number-format:"\@";'></td>
                                          <td class="table_body" style="text-align: right;vertical-align: top;"><?php echo number_format(@$sum_transaction, 2); ?></td>
							            </tr>
										<?php
										$sum_share = 0;
										$sum_emergent = 0;
										$sum_normal = 0;
										$sum_special = 0;
                                        $sum_transaction = 0;
										$member_count = 0;
										$index++;
									}
									$tmp['mem_group_id'] = $row['mem_group_id'];
									if(empty($tmp['member_id']) || $tmp['member_id'] <> $row['member_id']){
										$member_count += 1;
									}
                                    if ($sub_no > 1) {
                                        $row['transaction_balance'] = 0;
                                    }
									$sum_share += (int)$row['share_collect'];
									$sum_emergent += (int)$row['loan_emergent_balance'];
									$sum_normal += (int)$row['loan_normal_balance'];
									$sum_special += (int)$row['loan_special_balance'];
                                    $sum_covid += (int)$row['loan_covid_balance'];
                                    $sum_transaction += (int)$row['transaction_balance'];

                                    $all_sum_share += (int)$row['share_collect'];
                                    $all_sum_emergent += (int)$row['loan_emergent_balance'];
                                    $all_sum_normal += (int)$row['loan_normal_balance'];
                                    $all_sum_special += (int)$row['loan_special_balance'];
                                    $all_sum_covid += (int)$row['loan_covid_balance'];
                                    $all_sum_transaction += (int)$row['transaction_balance'];
									$tmp['member_id'] = $row['member_id'];
									?>

							<tr>
							  <td class="table_body" style='text-align: center;vertical-align: top;mso-number-format:"\@";'><?php echo @$row['member_id'];?></td>
							  <td class="table_body" style="text-align: left;vertical-align: top; "><?php echo @$row['prename_full'].@$row['firstname_th']."  ".@$row['lastname_th'];?></td>
							  <td class="table_body" style='text-align: center;vertical-align: top;mso-number-format:"\@";'><?php echo @$row['mem_group_id'];?></td>
							  <td class="table_body" style="text-align: left;vertical-align: top; "><?php echo (@$row['mem_group_name_main'] != '')?@$row['mem_group_name_main'].' :: '.@$row['mem_group_name_sub']:'';?></td>
							  <td class="table_body" style="text-align: left;vertical-align: top;"><?php echo @$row['mem_group_name_level'];?></td>
							  <td class="table_body" style="text-align: left;vertical-align: top;"><?php echo @$row['address_send_doc'];?></td>
							  <td class="table_body" style="text-align: right;vertical-align: top;"><?php echo @$runno; ?></td>
							  <?php
							  if($runno == '1') {
								  ?>
								  <td class="table_body" style="text-align: center;vertical-align: top;"><?php echo (@$row['share_period'] !='')?number_format(@$row['share_period'],0):''; ?></td>
								  <td class="table_body" style="text-align: right;vertical-align: top;"><?php echo (@$row['share_collect'] !='')?number_format(@$row['share_collect'],2):''; ?></td>
								  <?php
							  } else {
								  ?>
								  <td class="table_body" style="text-align: center;vertical-align: top;"></td>
								  <td class="table_body" style="text-align: right;vertical-align: top;"></td>
								  <?php
							  }
							  ?>
							  <td class="table_body" style="text-align: center;vertical-align: top;"><?php echo (@$row['loan_emergent_period_now'] !='')?number_format(@$row['loan_emergent_period_now'],0):''; ?></td>
							  <td class="table_body" style='text-align: right;vertical-align: top;mso-number-format:"\@";'><?php echo @$row['loan_emergent_contract_number']; ?></td>
							  <td class="table_body" style="text-align: right;vertical-align: top;"><?php echo (@$row['loan_emergent_balance'] !='')?number_format(@$row['loan_emergent_balance'],2):''; ?></td>
							  <td class="table_body" style="text-align: center;vertical-align: top;"><?php echo (@$row['loan_normal_period_now'] !='')?number_format(@$row['loan_normal_period_now'],0):''; ?></td>
							  <td class="table_body" style='text-align: right;vertical-align: top;mso-number-format:"\@";'><?php echo @$row['loan_normal_contract_number']; ?></td>
							  <td class="table_body" style="text-align: right;vertical-align: top;"><?php echo (@$row['loan_normal_balance'] !='')?number_format(@$row['loan_normal_balance'],2):''; ?></td>
							  <td class="table_body" style="text-align: center;vertical-align: top;"><?php echo (@$row['loan_special_period_now'] !='')?number_format(@$row['loan_special_period_now'],0):''; ?></td>
							  <td class="table_body" style='text-align: right;vertical-align: top;mso-number-format:"\@";'><?php echo @$row['loan_special_contract_number']; ?></td>
							  <td class="table_body" style="text-align: right;vertical-align: top;"><?php echo (@$row['loan_special_balance'] !='')?number_format(@$row['loan_special_balance'],2):''; ?></td>
							  <td class="table_body" style="text-align: center;vertical-align: top;"><?php echo (@$row['loan_special_period_now'] !='')?number_format(@$row['loan_special_period_now'],0):''; ?></td>
							  <td class="table_body" style='text-align: right;vertical-align: top;mso-number-format:"\@";'><?php echo @$row['loan_covid_contract_number']; ?></td>
							  <td class="table_body" style="text-align: right;vertical-align: top;"><?php echo (@$row['loan_covid_balance'] !='')?number_format(@$row['loan_covid_balance'],2):''; ?></td>
                              <?php if ($sub_no == '1') { ?>
                                <td class="table_body" style='text-align: right;vertical-align: top;mso-number-format:"\@";'><?php echo @$row['account_id']; ?></td>
                                <td class="table_body" style="text-align: right;vertical-align: top;"><?php echo (@$row['transaction_balance'] !='')?number_format(@$row['transaction_balance'],2):''; ?></td>
                              <?php } else { ?>
                                <td class="table_body" style='text-align: right;vertical-align: top;mso-number-format:"\@";'></td>
                                <td class="table_body" style="text-align: right;vertical-align: top;"></td>
                              <?php } ?>
							</tr>
									<?php
									$member_id_past = $row['member_id'];
								}

							}
						}
					}
					$last_runno = $runno;
                    if ($run_data == $max_data){ ?>
                        <tr>
                          <td class="table_body" style='text-align: center;vertical-align: top;mso-number-format:"\@";' colspan="7"> รวม </td>
                          <td class="table_body" style="text-align: center;vertical-align: top;"></td>
                          <td class="table_body" style="text-align: right;vertical-align: top;"><?php echo number_format(@$sum_share, 2); ?></td>
                          <td class="table_body" style="text-align: center;vertical-align: top;"></td>
                          <td class="table_body" style='text-align: right;vertical-align: top;mso-number-format:"\@";'></td>
                          <td class="table_body" style="text-align: right;vertical-align: top;"><?php echo number_format(@$sum_emergent, 2); ?></td>
                          <td class="table_body" style="text-align: center;vertical-align: top;"></td>
                          <td class="table_body" style='text-align: right;vertical-align: top;mso-number-format:"\@";'></td>
                          <td class="table_body" style="text-align: right;vertical-align: top;"><?php echo number_format(@$sum_normal, 2); ?></td>
                          <td class="table_body" style="text-align: center;vertical-align: top;"></td>
                          <td class="table_body" style='text-align: right;vertical-align: top;mso-number-format:"\@";'></td>
                          <td class="table_body" style="text-align: right;vertical-align: top;"><?php echo number_format(@$sum_special, 2); ?></td>
                          <td class="table_body" style="text-align: center;vertical-align: top;"></td>
                          <td class="table_body" style='text-align: right;vertical-align: top;mso-number-format:"\@";'></td>
                          <td class="table_body" style="text-align: right;vertical-align: top;"><?php echo number_format(@$sum_covid, 2); ?></td>
                          <td class="table_body" style='text-align: right;vertical-align: top;mso-number-format:"\@";'></td>
                          <td class="table_body" style="text-align: right;vertical-align: top;"><?php echo number_format(@$sum_transaction, 2); ?></td>
                        </tr>
                        <tr>
                          <td class="table_body" style='text-align: center;vertical-align: top;mso-number-format:"\@";' colspan="7"> รวมทั้งหมด </td>
                          <td class="table_body" style="text-align: center;vertical-align: top;"></td>
                          <td class="table_body" style="text-align: right;vertical-align: top;"><?php echo number_format(@$all_sum_share, 2); ?></td>
                          <td class="table_body" style="text-align: center;vertical-align: top;"></td>
                          <td class="table_body" style='text-align: right;vertical-align: top;mso-number-format:"\@";'></td>
                          <td class="table_body" style="text-align: right;vertical-align: top;"><?php echo number_format(@$all_sum_emergent, 2); ?></td>
                          <td class="table_body" style="text-align: center;vertical-align: top;"></td>
                          <td class="table_body" style='text-align: right;vertical-align: top;mso-number-format:"\@";'></td>
                          <td class="table_body" style="text-align: right;vertical-align: top;"><?php echo number_format(@$all_sum_normal, 2); ?></td>
                          <td class="table_body" style="text-align: center;vertical-align: top;"></td>
                          <td class="table_body" style='text-align: right;vertical-align: top;mso-number-format:"\@";'></td>
                          <td class="table_body" style="text-align: right;vertical-align: top;"><?php echo number_format(@$all_sum_special, 2); ?></td>
                          <td class="table_body" style="text-align: center;vertical-align: top;"></td>
                          <td class="table_body" style='text-align: right;vertical-align: top;mso-number-format:"\@";'></td>
                          <td class="table_body" style="text-align: right;vertical-align: top;"><?php echo number_format(@$all_sum_covid, 2); ?></td>
                          <td class="table_body" style='text-align: right;vertical-align: top;mso-number-format:"\@";'></td>
                          <td class="table_body" style="text-align: right;vertical-align: top;"><?php echo number_format(@$all_sum_transaction, 2); ?></td>
                        </tr>

                    <?php }
					?>
					</tbody>
				</table>
		</body>
	</html>
</pre>
