<?php 
header("Content-type: application/vnd.ms-excel;charset=utf-8;");
header("Content-Disposition: attachment; filename=รายงานการทำรายการ ประจำวัน.xls"); 
date_default_timezone_set('Asia/Bangkok');
?>
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
$num_transaction  = 0;
?>
<div style="width: 1000px;" class="page-break">
	<div class="panel panel-body" style="padding-top:10px !important;min-height: 1200px;">
		<table style="width: 100%;">
			<tr>
				<td class="text-center" colspan="11">
					<span class="title_view"><?php echo @$_SESSION['COOP_NAME']; ?></span><br>
					<span class="title_view">รายงานการทำรายการ ประจำวัน</span><br>
					<span class="title_view">
						<?php echo (@$_GET['type_id'] != 'all') ? " ประเภทบัญชี " . @$type_deposit[@$_GET['type_id']] : "" ?>
					</span>
					<span class="title_view">
						<?php
							echo " วันที่ " . $this->center_function->ConvertToThaiDate($start_date);
							echo (@$_GET['start_date'] == @$_GET['end_date']) ? "" : "  ถึง  " . $this->center_function->ConvertToThaiDate($end_date);
						?>
					</span>
				</td>
			</tr>
			<tr>
				<td colspan="11" style="text-align: right;">
					<span class="title_view_smal">วันที่ <?php echo $this->center_function->ConvertToThaiDate(@date('Y-m-d'), 1, 0); ?></span>
				</td>
			</tr>
			<tr>
				<td colspan="11" style="text-align: right;">
					<span class="title_view_smal">เวลา <?php echo date('H:i:s'); ?></span>
				</td>
			</tr>
			<tr>
				<td colspan="11" style="text-align: right;">
					<span class="title_view_smal">ผู้ทำรายการ <?php echo $_SESSION['USER_NAME']; ?></span>
				</td>
			</tr>
		</table>

		<table class="table table-bordered">
			<thead>
			<tr>
				<th class="table_header_top" style="width: 40px;vertical-align: middle;">ลำดับ</th>
				<th class="table_header_top" style="width: 100px;vertical-align: middle;">วันที่ทำรายการ</th>
				<th class="table_header_top" style="width: 60px;vertical-align: middle;">เวลาที่ทำรายการ</th>
				<th class="table_header_top" style="width: 100px;vertical-align: middle;">หมายเลขบัญชี</th>
				<th class="table_header_top" style="width: 180px;vertical-align: middle;">ชื่อบัญชี</th>
				<th class="table_header_top" style="width: 70px;vertical-align: middle;">รายการ</th>
				<th class="table_header_top" style="width: 80px;vertical-align: middle;">ฝาก</th>
				<th class="table_header_top" style="width: 80px;vertical-align: middle;">ถอน</th>
				<th class="table_header_top" style="width: 80px;vertical-align: middle;">ดอกเบี้ย</th>
				<th class="table_header_top" style="width: 80px;vertical-align: middle;">คงเหลือ</th>
				<th class="table_header_top" style="vertical-align: middle;">ผู้บันทึก</th>
			</tr>
			</thead>
			<tbody>
			<?php
			if (!empty($data)) {
				foreach (@$data AS $page => $data_row) {
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
								@$temp_data['transaction_deposit'] = 0;
								@$temp_data['transaction_balance'] = 0;
								@$temp_data['transaction_withdrawal'] = @$data_row[$key + 1]['transaction_withdrawal'] - $row['transaction_deposit'];
								@$temp_data['interest'] = $row['transaction_deposit'];
								$runno--;
								$flag = 1;
								continue;
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
								<td class="table_body" style="text-align: center;vertical-align: top;"><?php echo @$runno; ?></td>
								<td class="table_body" style="text-align: center;vertical-align: top;mso-number-format:'\@';"><?php echo (@$row['transaction_time']) ? $this->center_function->ConvertToThaiDate(@$row['transaction_time'], 1, 0) : ""; ?></td>
								<td class="table_body" style="text-align: center;vertical-align: top;mso-number-format:'\@';"><?php echo (@$row['transaction_time']) ? date(" H:i", strtotime(@$row['transaction_time'])) : "" ?></td>
								<td class="table_body" style="text-align: center;vertical-align: top;mso-number-format:'\@';"><?php echo @$row['account_id']; ?></td>
								<td class="table_body" style="text-align: left;vertical-align: top;"><?php echo @$row['account_name']; ?></td>
								<td class="table_body" style="text-align: center;vertical-align: top;"><?php echo @$row['transaction_list']; ?></td>
								<td class="table_body" style="text-align: right;vertical-align: top;"><?php echo number_format($row['transaction_deposit'], 2); ?></td>
								<td class="table_body" style="text-align: right;vertical-align: top;"><?php echo number_format($row['transaction_withdrawal'], 2); ?></td>
								<td class="table_body" style="text-align: right;vertical-align: top;"><?php echo number_format($row['interest'], 2); ?></td>
								<td class="table_body" style="text-align: right;vertical-align: top;"><?php echo number_format($row['transaction_balance'], 2); ?></td>
								<td class="table_body" style="text-align: center;vertical-align: top;">
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
					}
					$all_withdrawal += @$total_transaction_withdrawal;
					$all_deposit += @$total_transaction_deposit;
					$all_balance += @$total_transaction_balance;
					$all_interest += @$total_interest;
				}
			}
			$num_transaction = $last_runno;
			if (@$page == @$page_all) {
			?>
				<tr class="foot-border">
					<td class="table_body" style="text-align: center;" colspan="4">รวมทั้งหมด <?php echo @$num_transaction; ?> รายการ</td>
					<td class="table_body" style="text-align: center;" colspan="2">จำนวนเงินทั้งหมด</td>
					<td class="table_body" style="text-align: right;"><?php echo number_format(@$all_deposit, 2); ?></td>
					<td class="table_body" style="text-align: right;"><?php echo number_format(@$all_withdrawal, 2); ?></td>
					<td class="table_body" style="text-align: right;"><?php echo number_format(@$all_interest, 2); ?></td>
					<td class="table_body" style="text-align: right;"><?php echo number_format(@$all_balance, 2); ?></td>
					<td class="table_body" style="text-align: center;">บาท</td>
				</tr>
			<?php } ?>
			</tbody>
		</table>
	</div>
</div>