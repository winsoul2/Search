<style>
	.table {
		font-size: 10px;
		font-family: THSarabunNew;
		color: #000;
	}

	.table-view > thead, .table-view > thead > tr > td, .table-view > thead > tr > th {
		font-size: 10px;
		font-family: THSarabunNew;
		color: #000;
	}

	.title_view {
		font-size: 16px;
		font-family: THSarabunNew;
		margin-bottom: 10px;
		/*color: #000;	*/
	}

	.title_view_small {
		font-size: 10px;
		font-family: THSarabunNew;
		/*color: #000;*/
	}

	@page {
		size: landscape;
	}

	.border-bottom {
		border-bottom: 1px solid #000 !important;
		font-weight: bold;
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

?>

<?php
$index = 0;
$run_data = 0;
$all_index = 1;
$page_num = 0;
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
$all_loan_covid_person = 0;
$all_loan_covid_balance = 0;
$all_total_loan_balance = 0;
$all_total_loan_balance = 0;
$all_share_balance_subdivision = 0;
$all_loan_balance_subdivision = 0;
$member_id_past = "xx";
//echo '<pre>'; print_r($data); echo '</pre>';
$tmp['mem_group_id'] = array();
$tmp['member_id'] = array();
$member_count = 0;
$sum_share = 0;
$sum_emergent = 0;
$sum_normal = 0;
$sum_special = 0;
$sum_transaction = 0;
$all_sum_share = 0;
$all_sum_emergent = 0;
$all_sum_normal = 0;
$all_sum_special = 0;
$all_sum_transaction = 0;
$de_row = -3;
$status = false;
$loan_type_balance_pase = array();
if (!empty($data)) {
	foreach (@$data as $da) {
        $run_data ++;

		foreach (@$da as $key => $row) {


			if (!empty($row['share_collect']) || !empty($row['loan_emergent_balance']) || !empty($row['loan_normal_balance']) || !empty($row['loan_special_balance'])) {

				$status = true;
				if ($member_id_past != $row['member_id']) {
                    $sub_no = 0;
                    $runno++;
				}
                $sub_no++;
				if ($index == 0 || $index == (24-$de_row) || ($index > (24-$de_row) && (($index - (24-$de_row)) % (30-$de_row)) == 0)){
					$new_index = 1;
					?>

					<div style="width: 1500px;"  class="page-break">
					<div class="panel panel-body" style="padding-top:10px !important;min-height: 950px;">
					<table style="width: 100%;">
						<?php
						$sum_share_page = 0;
						$sum_emergent_page = 0;
						$sum_normal_page = 0;
						$sum_special_page = 0;
						$sum_covid_page = 0;
						$member_count_page = 0;

						if ($index == 0) {
							?>
							<tr>
								<td style="width:100px;vertical-align: top;">

								</td>
								<td class="text-center">
									<!-- <img src="<?php echo base_url(PROJECTPATH . '/assets/images/coop_profile/' . $_SESSION['COOP_IMG']); ?>" alt="Logo" style="height: 80px;" />	 -->
									<img
										src="<?php echo base_url('/assets/images/coop_profile/' . $_SESSION['COOP_IMG']); ?>"
										alt="Logo" style="height: 80px;"/>
									<h3 class="title_view"><?php echo @$_SESSION['COOP_NAME']; ?></h3>
									<h3 class="title_view">รายงานหุ้น หนี้ และเงินฝากของสมาชิก</h3>
									<h3 class="title_view">
										<?php
										$title_date = (@$_GET['type_date'] == '1') ? 'ณ วันที่' : 'ประจำวันที่';
										echo @$title_date . " " . $this->center_function->ConvertToThaiDate($start_date);
										?>
									</h3>
								</td>
								<td style="width:100px;vertical-align: top;" class="text-right">
									<a class="no_print" onclick="window.print();">
										<button class="btn btn-perview btn-after-input" type="button"><span
												class="icon icon-print" aria-hidden="true"></span></button>
									</a>
									<?php
									$get_param = '?';
									foreach (@$_GET as $key => $value) {
										//if($key != 'month' && $key != 'year' && $value != ''){
										$get_param .= $key . '=' . $value . '&';
										//}
									}
									$get_param = substr($get_param, 0, -1);
									?>
									<a class="no_print" target="_blank"
									   href="<?php echo base_url(PROJECTPATH . '/report_share_debt_deposit/report_share_debt_deposit_excel' . $get_param); ?>">
										<button class="btn btn-perview btn-after-input" type="button"><span
												class="icon icon icon-file-excel-o" aria-hidden="true"></span></button>
									</a>
								</td>
							</tr>
							<tr>
								<td colspan="3" style="text-align: right;">
									<span
										class="title_view_small">วันที่ <?php echo $this->center_function->ConvertToThaiDate(@date('Y-m-d'), 0, 0); ?></span>
								</td>
							</tr>
							<tr>
								<td colspan="3" style="text-align: right;">
									<span
										class="title_view_small">ผู้ทำรายการ <?php echo $_SESSION['USER_NAME']; ?></span>
								</td>
							</tr>
							<?php
						}
						?>
						<!-- <tr>
						<td colspan="3" style="text-align: right;">
							<span class="title_view_small">หน้าที่ <?php echo $index == 0 ? 1 : $index == 24 ? 2 : (($index - 24) / 30) + 2 ?></span><br>
						</td>
					</tr>  -->
					</table>

					<table class="table table-view table-center">
					<thead>
					<tr>
						<th rowspan="2" style="width: 40px;vertical-align: middle;">เลขที่สมาชิก</th>
						<th rowspan="2" style="width: 130px;vertical-align: middle;overflow: hidden;">ชื่อ - นามสกุล</th>
						<th rowspan="2" style="width: 40px;vertical-align: middle;">รหัสที่อยู่จัดส่ง</th>
						<th rowspan="2" style="width: 120px;vertical-align: middle;overflow: hidden;">หน่วยงานหลัก::หน่วยงานรอง</th>
						<th rowspan="2" style="width: 90px;vertical-align: middle;overflow: hidden;">หน่วยงานย่อย</th>
						<th rowspan="2" style="width: 120px;vertical-align: middle;overflow: hidden;">ที่อยู่ในการจัดส่งเอกสาร รฟท.</th>
						<th rowspan="2" style="width: 20px;vertical-align: middle;">ลำดับ</th>
						<th rowspan="2" style="width: 20px;vertical-align: middle;">งวดหุ้น</th>
						<th rowspan="2" style="width: 50px;vertical-align: middle;">ทุนเรือนหุ้น</th>
						<?php
						foreach ($loan_type AS $key => $row_loan_type) {
							?>
							<th colspan="3"
								style="width: 70px;vertical-align: middle;"><?php echo str_replace('เงินกู้', '', $row_loan_type['loan_type']); ?></th>
						<?php } ?>
						<th colspan="2"
							style="width:70px;vertical-align: middle;">เงินฝาก</th>
					</tr>
					<tr>
						<?php
						foreach ($loan_type AS $key => $row_loan_type) {
							?>
							<th style="width: 20px;vertical-align: middle;">งวด</th>
							<th style="width: 30px;vertical-align: middle;">เลขที่สัญญา</th>
							<th style="width: 30px;vertical-align: middle;">เงินคงเหลือ</th>
						<?php } ?>
						<th style="width: 40px;vertical-align: middle;">เลขที่บัญชีเงินฝาก</th>
						<th style="width: 40px;vertical-align: middle;">เงินคงเหลือ</th>
					</tr>
					</thead>
					<tbody>
					<?php
				}
				if (!empty($tmp['mem_group_id']) && $tmp['mem_group_id'] <> $row['mem_group_id']) {
				    ?>
                    <tr>
                        <td style="text-align: center;vertical-align: top;" colspan="7"> รวม </td>
                        <td style="text-align: center;vertical-align: top;"></td>
                        <td style="text-align: right;vertical-align: top;"><?php echo number_format(@$sum_share, 2); ?></td>
                        <td style="text-align: center;vertical-align: top;"></td>
                        <td style="text-align: right;vertical-align: top;"></td>
                        <td style="text-align: right;vertical-align: top;"><?php echo number_format(@$sum_emergent, 2); ?></td>
                        <td style="text-align: center;vertical-align: top;"></td>
                        <td style="text-align: right;vertical-align: top;"></td>
                        <td style="text-align: right;vertical-align: top;"><?php echo number_format(@$sum_normal, 2); ?></td>
                        <td style="text-align: center;vertical-align: top;"></td>
                        <td style="text-align: right;vertical-align: top;"></td>
                        <td style="text-align: right;vertical-align: top;"><?php echo number_format(@$sum_special, 2); ?></td>
                        <td style="text-align: center;vertical-align: top;"></td>
                        <td style="text-align: right;vertical-align: top;"></td>
                        <td style="text-align: right;vertical-align: top;"><?php echo number_format(@$sum_covid, 2); ?></td>
                        <td style="text-align: right;vertical-align: top;"></td>
                        <td style="text-align: right;vertical-align: top;"><?php echo number_format(@$sum_transaction, 2); ?></td>
                    </tr>
                    <?php
					$sum_share = 0;
					$sum_emergent = 0;
					$sum_normal = 0;
					$sum_special = 0;
					$sum_covid = 0;
                    $sum_transaction = 0;
					$member_count = 0;
//					$index++;

				}
//				if(!empty($tmp['mem_group_id'])) {
//					$index++;
//				}

				$tmp['mem_group_id'] = $row['mem_group_id'];
				if (empty($tmp['member_id']) || $tmp['member_id'] <> $row['member_id']) {
					$member_count += 1;
					$member_count_page += 1;
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

				$sum_share_page += (int)$row['share_collect'];
				$sum_emergent_page += (int)$row['loan_emergent_balance'];
				$sum_normal_page += (int)$row['loan_normal_balance'];
				$sum_special_page += (int)$row['loan_special_balance'];
				$sum_covid_page += (int)$row['loan_covid_balance'];

				if(!empty($row['loan_normal_balance'])){
					$loan_type_balance_pase[$row['loan_type_normal']] += $row['loan_normal_balance'];
					$loan_type_normal[$row['loan_type_normal']]['amount_balance'] += $row['loan_normal_balance'];
				}
				//<td style="text-align: center;vertical-align: top;"><?php echo @$new_index;<td style="text-align: center;vertical-align: top;"><?php echo @$index;</td>

				$tmp['member_id'] = $row['member_id'];
				?>
				<tr>
					<td style="text-align: center;vertical-align: top;"><?php echo @$row['member_id']; ?></td>
					<td style="text-align: left;vertical-align: top; "><?php echo @$row['prename_full'] . @$row['firstname_th'] . "  " . @$row['lastname_th']; ?></td>
					<td style="text-align: center;vertical-align: top;"><?php echo @$row['mem_group_id']; ?></td>
					<td style="text-align: left;vertical-align: top; "><?php echo (@$row['mem_group_name_main'] != '') ? @$row['mem_group_name_main'] . ' :: ' . @$row['mem_group_name_sub'] : ''; ?></td>
					<td style="text-align: left;vertical-align: top; "><?php echo @$row['mem_group_name_level']; ?></td>
					<td style="text-align: right;vertical-align: top;"><?php echo @$row['address_send_doc'] ; ?></td>
					<?php
					$new_index++;
					if ($sub_no == '1') {
						?>
                        <td style="text-align: right;vertical-align: top;"><?php echo @$runno; ?></td>
						<td style="text-align: center;vertical-align: top;"><?php echo (@$row['share_period'] != '') ? number_format(@$row['share_period'], 0) : ''; ?></td>
						<td style="text-align: right;vertical-align: top;"><?php echo (@$row['share_collect'] != '') ? number_format(@$row['share_collect'], 2) : ''; ?></td>
						<?php
					} else {
						?>
                        <td style="text-align: right;vertical-align: top;"></td>
						<td style="text-align: center;vertical-align: top;"></td>
						<td style="text-align: right;vertical-align: top;"></td>
						<?php
					}
					?>
					<td style="text-align: center;vertical-align: top;"><?php echo (@$row['loan_emergent_period_now'] != '') ? number_format(@$row['loan_emergent_period_now'], 0) : ''; ?></td>
					<td style="text-align: right;vertical-align: top;"><?php echo @$row['loan_emergent_contract_number']; ?></td>
					<td style="text-align: right;vertical-align: top;"><?php echo (@$row['loan_emergent_balance'] != '') ? number_format(@$row['loan_emergent_balance'], 2) : ''; ?></td>
					<td style="text-align: center;vertical-align: top;"><?php echo (@$row['loan_normal_period_now'] != '') ? number_format(@$row['loan_normal_period_now'], 0) : ''; ?></td>
					<td style="text-align: right;vertical-align: top;"><?php echo @$row['loan_normal_contract_number']; ?></td>
					<td style="text-align: right;vertical-align: top;"><?php echo (@$row['loan_normal_balance'] != '') ? number_format(@$row['loan_normal_balance'], 2) : ''; ?></td>
					<td style="text-align: center;vertical-align: top;"><?php echo (@$row['loan_special_period_now'] != '') ? number_format(@$row['loan_special_period_now'], 0) : ''; ?></td>
					<td style="text-align: right;vertical-align: top;"><?php echo @$row['loan_special_contract_number']; ?></td>
					<td style="text-align: right;vertical-align: top;"><?php echo (@$row['loan_special_balance'] != '') ? number_format(@$row['loan_special_balance'], 2) : ''; ?></td>
					<td style="text-align: center;vertical-align: top;"><?php echo (@$row['loan_covid_period_now'] != '') ? number_format(@$row['loan_covid_period_now'], 0) : ''; ?></td>
					<td style="text-align: right;vertical-align: top;"><?php echo @$row['loan_covid_contract_number']; ?></td>
					<td style="text-align: right;vertical-align: top;"><?php echo (@$row['loan_covid_balance'] != '') ? number_format(@$row['loan_covid_balance'], 2) : ''; ?></td>
                    <?php if ($sub_no == '1') { ?>
                        <td style="text-align: right;vertical-align: top;"><?php echo @$row['account_id']; ?></td>
                        <td style="text-align: right;vertical-align: top;"><?php echo (@$row['transaction_balance'] != '') ? number_format(@$row['transaction_balance'], 2) : ''; ?></td>
                    <?php } else { ?>
                        <td style="text-align: right;vertical-align: top;"></td>
                        <td style="text-align: right;vertical-align: top;"></td>
                    <?php } ?>
<!--                    <td style="text-align: right;vertical-align: top;">--><?php //echo number_format(@$all_sum_transaction, 2); ?><!--</td>-->
				</tr>
				<?php
				if ($data_count != $all_index && ($index == (23-$de_row) || ($index > (24-$de_row) && (($index - (24-$de_row)) % (30-$de_row)) == (29-$de_row)))) {
					// ยอดรวมแต่ละหน้า
					$status = false;
					foreach ($loan_type_normal as $loan_name_id => $loan_type_data){
						$i++;
					}
					?>
					</tbody>
					</table>
					</div>
					</div>
					<?php
				}
				$member_id_past = $row['member_id'];
				$index++;
			}
			if ($data_count == $all_index && $status) {
				// ยอดรวมทุกหน้า
				?>
					<?php
					$max_column = 5;
					$i = 0;
					?>
				</td>
				<?php
				foreach ($loan_type_normal as $loan_name_id => $loan_type_data){
					?>
					<?php
					if($i >= $max_column-1){
						$max_column += 5?>
						</tr>
						<tr>
					<?php }
					$i++;
				}
				if ($run_data == $max_data){ ?>
                    <tr>
                        <td style="text-align: center;vertical-align: top;" colspan="7"> รวม </td>
                        <td style="text-align: center;vertical-align: top;"></td>
                        <td style="text-align: right;vertical-align: top;"><?php echo number_format(@$sum_share, 2); ?></td>
                        <td style="text-align: center;vertical-align: top;"></td>
                        <td style="text-align: right;vertical-align: top;"></td>
                        <td style="text-align: right;vertical-align: top;"><?php echo number_format(@$sum_emergent, 2); ?></td>
                        <td style="text-align: center;vertical-align: top;"></td>
                        <td style="text-align: right;vertical-align: top;"></td>
                        <td style="text-align: right;vertical-align: top;"><?php echo number_format(@$sum_normal, 2); ?></td>
                        <td style="text-align: center;vertical-align: top;"></td>
                        <td style="text-align: right;vertical-align: top;"></td>
                        <td style="text-align: right;vertical-align: top;"><?php echo number_format(@$sum_special, 2); ?></td>
                        <td style="text-align: center;vertical-align: top;"></td>
                        <td style="text-align: right;vertical-align: top;"></td>
                        <td style="text-align: right;vertical-align: top;"><?php echo number_format(@$sum_covid, 2); ?></td>
                        <td style="text-align: right;vertical-align: top;"></td>
                        <td style="text-align: right;vertical-align: top;"><?php echo number_format(@$sum_transaction, 2); ?></td>
                    </tr>
                    <tr>
                        <td style="text-align: center;vertical-align: top;" colspan="7"> รวมทั้งหมด </td>
                        <td style="text-align: center;vertical-align: top;"></td>
                        <td style="text-align: right;vertical-align: top;"><?php echo number_format(@$all_sum_share, 2); ?></td>
                        <td style="text-align: center;vertical-align: top;"></td>
                        <td style="text-align: right;vertical-align: top;"></td>
                        <td style="text-align: right;vertical-align: top;"><?php echo number_format(@$all_sum_emergent, 2); ?></td>
                        <td style="text-align: center;vertical-align: top;"></td>
                        <td style="text-align: right;vertical-align: top;"></td>
                        <td style="text-align: right;vertical-align: top;"><?php echo number_format(@$all_sum_normal, 2); ?></td>
                        <td style="text-align: center;vertical-align: top;"></td>
                        <td style="text-align: right;vertical-align: top;"></td>
                        <td style="text-align: right;vertical-align: top;"><?php echo number_format(@$all_sum_special, 2); ?></td>
                        <td style="text-align: center;vertical-align: top;"></td>
                        <td style="text-align: right;vertical-align: top;"></td>
                        <td style="text-align: right;vertical-align: top;"><?php echo number_format(@$all_sum_covid, 2); ?></td>
                        <td style="text-align: right;vertical-align: top;"></td>
                        <td style="text-align: right;vertical-align: top;"><?php echo number_format(@$all_sum_transaction, 2); ?></td>
                    </tr>

				<?php }
				?>
				</tr>
				</tbody>
				</table>
				</div>
				</div>
				<?php
			}
			$all_index++;
		}
	}
}
$last_runno = $runno;
?>
