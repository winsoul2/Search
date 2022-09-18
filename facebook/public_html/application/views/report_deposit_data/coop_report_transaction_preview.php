<style>
	.table-view > thead, .table-view > thead > tr > td, .table-view > thead > tr > th {
		font-size: 14px;
	}

	.table-view-2 > thead > tr > th {
		border-top: 1px solid #000 !important;
		border-bottom: 1px solid #000 !important;
		font-size: 16px;
	}

	.table-view-2 > tbody > tr > td {
		border: 0px !important;
		/*font-family: upbean;
		font-size: 16px;*/
		font-family: Tahoma;
		font-size: 11px;
	}

	.border-bottom {
		border-bottom: 1px solid #000 !important;
		font-weight: bold;
	}

	.table-view-2 > tbody > tr > td > span {
		font-family: Tahoma;
		font-size: 11px;
	}

	.foot-border {
		border-top: 1px solid #000 !important;
		border-bottom: double !important;
		font-weight: bold;
	}

	.table {
		color: #000;
	}
</style>
<?php

if (@$_GET['start_date']) {
	$start_date_arr = explode('/', @$_GET['start_date']);
	$start_day = $start_date_arr[0];
	$start_month = $start_date_arr[1];
	$start_year = $start_date_arr[2];
	$start_year -= 543;
	$start_date = $start_year . '-' . $start_month . '-' . $start_day;
}

if (@$_GET['end_date']) {
	$end_date_arr = explode('/', @$_GET['end_date']);
	$end_day = $end_date_arr[0];
	$end_month = $end_date_arr[1];
	$end_year = $end_date_arr[2];
	$end_year -= 543;
	$end_date = $end_year . '-' . $end_month . '-' . $end_day;
}

//class="page-break"
//
$last_runno = 0;
$all_withdrawal = 0;
$all_deposit = 0;
$all_balance = 0;
$all_interest = 0;
if (!empty($data)) {
	foreach (@$data AS $page => $data_row) {
		?>
		<div style="width: 1000px;" class="page-break">
			<div class="panel panel-body" style="padding-top:10px !important;min-height: 1200px;">
				<table style="width: 100%;">
					<?php

					if (@$page == 1) {
						?>
						<tr>
							<td style="width:100px;vertical-align: top;">

							</td>
							<td class="text-center">
								<img
									src="<?php echo base_url(PROJECTPATH . '/assets/images/coop_profile/' . $_SESSION['COOP_IMG']); ?>"
									alt="Logo" style="height: 80px;"/>
								<h3 class="title_view"><?php echo @$_SESSION['COOP_NAME']; ?></h3>
								<h3 class="title_view">รายงานการทำรายการ ประจำวัน</h3>
								<h3 class="title_view">
									<?php echo (@$_GET['type_id'] != 'all') ? " ประเภทบัญชี " . @$type_deposit[@$_GET['type_id']] : "" ?>
								</h3>
								<h3 class="title_view">
									<?php
									echo " วันที่ " . $this->center_function->ConvertToThaiDate($start_date);
									echo (@$_GET['start_date'] == @$_GET['end_date']) ? "" : "  ถึง  " . $this->center_function->ConvertToThaiDate($end_date);
									?>
								</h3>
							</td>
							<td style="width:100px;vertical-align: top;" class="text-right">
								<a class="no_print" onclick="export_excel()">
									<button class="btn btn-perview btn-after-input" type="button"><span
											class="fa fa-file-excel-o" aria-hidden="true"></span></button>
								</a>
								<a class="no_print" onclick="window.print();">
									<button class="btn btn-perview btn-after-input" type="button"><span
											class="icon icon-print" aria-hidden="true"></span></button>
								</a>
							</td>
						</tr>
					<?php } ?>
					<tr>
						<td colspan="3" style="text-align: right;">
							<span class="title_view">หน้าที่ <?php echo @$page . '/' . @$page_all; ?></span><br>
						</td>
					</tr>
					<tr>
						<td colspan="3" style="text-align: right;">
							<span
								class="title_view">วันที่ <?php echo $this->center_function->ConvertToThaiDate(@date('Y-m-d'), 1, 0); ?></span>
						</td>
					</tr>
					<tr>
						<td colspan="3" style="text-align: right;">
							<span class="title_view">เวลา <?php echo date('H:i:s'); ?></span>
						</td>
					</tr>
					<tr>
						<td colspan="3" style="text-align: right;">
							<span class="title_view">ผู้ทำรายการ <?php echo $_SESSION['USER_NAME']; ?></span>
						</td>
					</tr>
				</table>

				<table class="table table-view-2 table-center">
					<thead>
					<tr>
						<th style="width: 40px;vertical-align: middle;">ลำดับ</th>
						<th style="width: 100px;vertical-align: middle;">วันที่ทำรายการ</th>
						<th style="width: 60px;vertical-align: middle;">เวลาที่ทำรายการ</th>
						<th style="width: 100px;vertical-align: middle;">หมายเลขบัญชี</th>
						<th style="width: 180px;vertical-align: middle;">ชื่อบัญชี</th>
						<th style="width: 70px;vertical-align: middle;">รายการ</th>
						<th style="width: 80px;vertical-align: middle;">ฝาก</th>
						<th style="width: 80px;vertical-align: middle;">ถอน</th>
						<th style="width: 80px;vertical-align: middle;">ดอกเบี้ย</th>
						<th style="width: 80px;vertical-align: middle;">คงเหลือ</th>
						<th style="vertical-align: middle;">ผู้บันทึก</th>
					</tr>
					</thead>
					<tbody>
					<?php
					$runno = $last_runno;
					$total_transaction_withdrawal = 0;
					$total_transaction_deposit = 0;
					$total_transaction_balance = 0;
					$total_transaction_deposit1 = 0;
					$total_interest = 0;
					$flag = 0;
					$temp_data = array();

					$cw = 0;

					if (!empty($data_row)) {

						foreach (@$data_row as $key => $row) {
							$runno++;

							if (in_array($row['transaction_list'], @$rs_int) && in_array($data_row[$key + 1]['transaction_list'], @$rs_cw)) {
								//in_array($row[$key+1]['transaction_list'], @$rs_cw))
								//$cw = @$row['interest'];

								@$temp_data['transaction_deposit'] = 0;
								@$temp_data['transaction_balance'] = 0;
								@$temp_data['transaction_withdrawal'] = @$data_row[$key + 1]['transaction_withdrawal'] - $row['transaction_deposit'];
								@$temp_data['interest'] = $row['transaction_deposit'];
								$runno--;
								$flag = 1;
								continue;
								//echo "<pre>"; print_r( $data_row[$key+1]); exit;

							} else {

								if (in_array($row['transaction_list'], @$rs_int)) {

									$row['interest'] = $row['transaction_deposit'];
									$row['transaction_deposit'] = 0;
								}

								if ($flag) {
									$row['transaction_balance'] = $temp_data['transaction_balance'];
									$row['transaction_deposit'] = $temp_data['transaction_deposit'];
									$row['transaction_withdrawal'] = $temp_data['transaction_withdrawal'];
									$row['interest'] = $temp_data['interest'];
									unset($temp_data);
									$flag = 0;
								}
							}

							$total_interest += @$row['interest'];
							$total_transaction_deposit += @$row['transaction_deposit'];
							$total_transaction_withdrawal += @$row['transaction_withdrawal'];
							$total_transaction_balance += @$row['transaction_balance'];

							?>
							<tr>
								<td style="text-align: center;vertical-align: top;"><?php echo @$runno; ?></td>
								<td style="text-align: center;vertical-align: top;"><?php echo (@$row['transaction_time']) ? $this->center_function->ConvertToThaiDate(@$row['transaction_time'], 1, 0) : ""; ?></td>
								<td style="text-align: center;vertical-align: top;"><?php echo (@$row['transaction_time']) ? date(" H:i", strtotime(@$row['transaction_time'])) : "" ?></td>
								<td style="text-align: center;vertical-align: top;"><?php echo @$row['account_id']; ?></td>
								<td style="text-align: left;vertical-align: top;"><?php echo @$row['account_name']; ?></td>
								<td style="text-align: center;vertical-align: top;"><?php echo @$row['transaction_list']; ?></td>
								<td style="text-align: right;vertical-align: top;"><?php echo number_format($row['transaction_deposit'], 2); ?></td>
								<td style="text-align: right;vertical-align: top;"><?php echo number_format($row['transaction_withdrawal'], 2); ?></td>
								<td style="text-align: right;vertical-align: top;"><?php echo number_format($row['interest'], 2); ?></td>
								<td style="text-align: right;vertical-align: top;"><?php echo number_format($row['transaction_balance'], 2); ?></td>
								<td style="text-align: center;vertical-align: top;">
									<?php
									if ($row['user_name'] != '') {
										echo $row['user_name'];
									} else if ($row['member_id_atm'] != '') {
										echo @$row['member_name_atm'];
									} else {
										echo "";
									}
									?>
								</td>
							</tr>
							<?php


						}

						$last_runno = $runno;

						?>
						<tr class="border-bottom">
							<td style="text-align: right;" colspan="6">จำนวนเงิน</td>
							<td style="text-align: right;"><span
									style="border-bottom: 1px solid #000;"><?php echo number_format(@$total_transaction_deposit, 2); ?></span>
							</td>
							<td style="text-align: right;"><span
									style="border-bottom: 1px solid #000;"><?php echo number_format(@$total_transaction_withdrawal, 2); ?></span>
							</td>
							<td style="text-align: right;"><span
									style="border-bottom: 1px solid #000;"><?php echo number_format(@$total_interest, 2); ?></span>
							</td>
							<td style="text-align: right;"><span
									style="border-bottom: 1px solid #000;"><?php echo number_format(@$total_transaction_balance, 2); ?></span>
							</td>
							<td style="text-align: center;">บาท</td>
						</tr>
						<?php
					}


					$all_withdrawal += @$total_transaction_withdrawal;
					$all_deposit += @$total_transaction_deposit;
					$all_balance += @$total_transaction_balance;
					$all_interest += @$total_interest;
					?>
					<?php
					if (@$page == @$page_all) {
						$num_transaction = $last_runno;
						?>
						<tr class="foot-border">
							<td style="text-align: center;" colspan="4">รวมทั้งหมด <?php echo @$num_transaction; ?> รายการ</td>
							<td style="text-align: center;" colspan="2">จำนวนเงินทั้งหมด</td>
							<td style="text-align: right;"><?php echo number_format(@$all_deposit, 2); ?></td>
							<td style="text-align: right;"><?php echo number_format(@$all_withdrawal, 2); ?></td>
							<td style="text-align: right;"><?php echo number_format(@$all_interest, 2); ?></td>
							<td style="text-align: right;"><?php echo number_format(@$all_balance, 2); ?></td>
							<td style="text-align: center;">บาท</td>
						</tr>
					<?php } ?>

					</tbody>
				</table>
			</div>
		</div>
		<?php
	}
}
?>
<script>
	function export_excel() {
		var url = window.location.href + "&excel=export";
		window.location = url;
	}
</script>
