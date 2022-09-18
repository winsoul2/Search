<style>
	@media (min-width: 768px) {
		.modal-dialog {
			width: 1200px;
		}
	}
</style>
<div id="WithdrawalChooses" tabindex="-1" role="dialog" class="modal fade">
	<div class="modal-dialog modal-dialog-account">
		<div class="modal-content">
			<div class="modal-header modal-header-withdrawal">
				<h2 class="modal-title">ถอนเงิน</h2>
			</div>
			<div class="modal-body scrollbar" style="height: 600px;overflow-y: auto;">
				<!--<form action="?" method="POST">-->
				<form action="<?php echo base_url(PROJECTPATH . '/save_money/save_transaction_chooses'); ?>" id="save_transaction_chooses" method="POST">
					<input type="hidden" name="do" value="withdrawal">
					<input type="hidden" name="account_id" value="" id="account_id">
					<input type="hidden" name="transaction_list" value="<?php echo $row_with['money_type_name_short']; ?>" id="transaction_list">
					<input type="hidden" name="date_fixed_transaction" id="date_fixed_transaction" value="<?php echo $this->center_function->mydate2date(date('Y-m-d'));?>">
					<div class="g24-col-sm-24">
						<div class="form-group">
							<div class="g24-col-sm-24">
								<table class="table table-bordered table-striped table-center" id="table">
									<thead>
										<tr class="bg-primary">
											<th class="font-normal" style="width: 50px">เงินต้น</th>
											<th class="font-normal" style="width: 100px">วันที่ฝาก</th>
											<th class="font-normal" style="width: 100px">ครบกำหนด</th>
											<th class="font-normal" style="width: 100px">ยอดเดิม</th>
											<th class="font-normal" style="width: 100px">ระยะเวลาฝาก</th>
											<th class="font-normal" style="width: 100px">จำนวนเงินที่ถอน</th>
											<th class="font-normal" style="width: 100px">ยอดคงเหลือ</th>
											<th class="font-normal" style="width: 100px">ดอกเบี้ย</th>
											<th class="font-normal" style="width: 100px">ภาษี</th>
											<th class="font-normal" style="width: 100px">ถอนดอกเบี้ยหักภาษีเมื่อครบกำหนด</th>
											<th class="font-normal" style="width: 100px">จำนวนเงินที่ถอนของดอกเบี้ยหักภาษี</th>
										</tr>
									</thead>
									<tbody>
										<?php
										if (!empty($data_chooses)) {
											$i = 0;
											foreach ($data_chooses as $key => $row) {
												if(@$row['balance_deposit']>0){
													$i++;
										?>
													<tr>
														<td><?php echo @$row['account_no']; ?></td>
														<td><?php echo @$this->center_function->mydate2date($row['transaction_time']); ?></td>
														<td><?php echo @$this->center_function->mydate2date($row['date_due']); ?></td>
														<td>
															<?php echo number_format(@$row['balance_deposit'], 2); ?>
															<input type="hidden" name="balance_deposit[<?php echo $key; ?>]" class="form-control m-b-1" value="<?php echo @$row['balance_deposit']; ?>" id="balance_deposit_<?php echo $key; ?>">
															<input type="hidden" name="ref_account_no[<?php echo $key; ?>]" class="form-control m-b-1" value="<?php echo @$row['account_no']; ?>" id="ref_account_no_<?php echo $key; ?>">
														</td>
														<td>
															<?php echo @$row['long_time']; ?> วัน
															<input type="hidden" name="long_time[<?php echo $key; ?>]" class="form-control m-b-1" value="<?php echo @$row['long_time']; ?>" id="long_time_<?php echo $key; ?>">
														</td>
														<td>
															<input type="hidden" name="transaction_id[<?php echo $key; ?>]" class="form-control m-b-1" value="<?php echo @$row['transaction_id']; ?>" id="transaction_id_<?php echo $key; ?>">
															<input type="text" name="money_withdrawal[<?php echo $key; ?>]" class="form-control m-b-1" value="" id="money_withdrawal_<?php echo $key; ?>" onkeyup="format_the_number_decimal(this);check_tax(this);" data-key="<?php echo $key; ?>" maxlength="20">
														</td>
														<td><input type="text" name="amount_balance[<?php echo $key; ?>]" class="form-control m-b-1" value="" id="amount_balance_<?php echo $key; ?>" readonly></td>
														<td><input type="text" name="amount_int[<?php echo $key; ?>]" class="form-control m-b-1" value="" id="amount_int_<?php echo $key; ?>" readonly></td>
														<td><input type="text" name="amount_tax[<?php echo $key; ?>]" class="form-control m-b-1" value="" id="amount_tax_<?php echo $key; ?>" readonly></td>
														<td>
															<?php if (@$row['chk_date_count_due'] == '1') { ?>
																<label class="custom-control custom-control-primary custom-checkbox " style="">
																	<input type="checkbox" class="custom-control-input check_int" name="check_withdrawal_int[<?php echo $key; ?>]" id="check_withdrawal_int_<?php echo $key; ?>" value="<?php echo @$row['transaction_id']; ?>" data-key="<?php echo $key; ?>">
																	<span class="custom-control-indicator" style="height: 20px; width: 20px;"></span>
																</label>
															<?php } ?>
														</td>

														<td>
															<input type="text" name="money_withdrawal_int[<?php echo $key; ?>]" class="form-control m-b-1" value="" id="money_withdrawal_int_<?php echo $key; ?>" onkeyup="format_the_number_decimal(this);withdrawal_int(this);" maxlength="20" disabled="disabled" data-key-int="<?php echo $key; ?>">
														</td>

												<?php 
													}
												}	
												?>
										<?php } else { ?>
											<tr>
												<td colspan='11' align='center'> ยังไม่พบรายการ</td>
											</tr>
										<?php } ?>
									</tbody>
								</table>
							</div>
						</div>

						<div class="form-group">
							<div class="row">
								<label for="c_amount_received" class="control-label g24-col-sm-4">รับจริง </label>
								<div class="g24-col-sm-4">
									<input type="text" name="c_amount_received" class="form-control m-b-1" value="" id="c_amount_received" readonly>
								</div>
								<label for="c_amount_withdrawal" class="control-label g24-col-sm-4">รวมเงินถอน </label>
								<div class="g24-col-sm-4">
									<input type="text" name="c_amount_withdrawal" class="form-control m-b-1" value="" id="c_amount_withdrawal" readonly>
								</div>
								<label for="c_amount_balance" class="control-label g24-col-sm-4">ยอดเงินคงเหลือ </label>
								<div class="g24-col-sm-4">
									<input type="text" name="c_amount_balance" class="form-control m-b-1" value="" id="c_amount_balance" readonly>
								</div>
							</div>
						</div>
						<div class="form-group">
							<div class="row">
								<label for="c_amount_int" class="control-label g24-col-sm-4">ดอกเบี้ย </label>
								<div class="g24-col-sm-4">
									<input type="text" name="c_amount_int" class="form-control m-b-1" value="" id="c_amount_int" readonly>
								</div>
								<label for="c_amount_tax" class="control-label g24-col-sm-4">ภาษี </label>
								<div class="g24-col-sm-4">
									<input type="text" name="c_amount_tax" class="form-control m-b-1" value="" id="c_amount_tax" readonly>
								</div>
							</div>
						</div>
						<div class="form-group">
							<div class="row">
								<label for="date_fixed" class="control-label g24-col-sm-4">ระบุวันถอนเงิน</label>
								<div class="input-with-icon g24-col-sm-4">
									<div class="form-group">
										<input id="date_fixed" class="form-control m-b-1" style="padding-left: 50px;" type="text" data-date-language="th-th" value="<?php echo $this->center_function->mydate2date(date('Y-m-d'));?>" title="กรุณาป้อน วันที่">
										<span class="icon icon-calendar input-icon m-f-1"></span>
									</div>
								</div>
								<label class="g24-col-sm-4 control-label">ระบุเวลา</label>
								<div class="g24-col-sm-4">
									<div class="input-with-icon">
										<div class="form-group">
											<input id="time_fixed" name="time_fixed" class="form-control m-b-1 timepicker" type="text" value="<?php echo date('H:i:s'); ?>">
											<span class="icon icon-clock-o input-icon"></span>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="form-group">
							<div class="row">
								<label class="control-label g24-col-sm-4 control-label" require>วิธีการชำระเงิน</label>
								<div class="g24-col-sm-10">
									<input type="radio" name="pay_type" id="pay_type" value="0" onclick="on_cash_deposit(true);set_bank_choose('');set_branch_code_choose('');show('');" checked=""> เงินสด <i class="fa fa-money"></i> &nbsp;&nbsp;&nbsp;&nbsp;
									<input type="radio" name="pay_type" id="pay_type" value="1" onclick="on_cash_deposit(false);set_bank_choose('');set_branch_code_choose('');show('withdrawal_xd_sec');"> โอนเงิน <i class="fa fa-credit-card"></i> &nbsp;&nbsp;&nbsp;&nbsp;
									<input type="radio" name="pay_type" id="pay_type" value="22" onclick="on_cash_deposit(false);set_bank_choose('');set_branch_code_choose('');show('withdrawal_che_sec');"> เช็ค <i class="fa fa-university"></i> &nbsp;&nbsp;&nbsp;&nbsp;
									<input type="radio" name="pay_type" id="pay_type" value="3" onclick="on_cash_deposit(false);set_bank_choose('');set_branch_code_choose('');show('withdrawal_other_sec');"> อื่นๆ <i class="fa fa-ellipsis-h"></i>
								</div>
							</div>
						</div>

						<div class="form-group">
							<div class="row">
								<div class="g24-col-sm-24" id="withdrawal_xd_sec" style="display:none;">
									<div class="form-group">
										<label class="control-label g24-col-sm-4 m-b-1"></label>
										<div class="g24-col-sm-17">
											<div id="transfer_deposit">
												<div class="transfer_content">
													<div class="row transfer">
														<div class="g24-col-sm-24">
															<div class="form-group">
																<label class="control-label g24-col-sm-1" for="transfer_bank_account_name"></label>
																<input type="radio" name="xd_bank_id" id="xd_1" onclick="set_bank_choose('006');set_branch_code_choose('0071');"><label for="xd_1"> ธ.กรุงไทย จำกัด สาขาการปิโตรเลียม</label>
															</div>
														</div>
														<div class="g24-col-sm-24">
															<div class="form-group">
																<label class="control-label g24-col-sm-1" for="transfer_bank_account_name"></label>
																<input type="radio" name="xd_bank_id" id="xd_2" onclick="set_bank_choose('002');set_branch_code_choose('1082');"><label for="xd_2"> ธ.กรุงเทพ จำกัด สาขาเอนเนอร์ยี่ คอมเพล็กซ์</label>
															</div>
														</div>
														<div class="g24-col-sm-24">
															<div class="form-group">
																<label class="control-label g24-col-sm-1" for="transfer_bank_account_name"></label>
																<input type="radio" name="xd_bank_id" id="xd_3" onclick="set_bank_choose('011');set_branch_code_choose('0211');"><label for="xd_3"> ธ.ทหารไทย จำกัด สาขาเอนเนอร์ยี่คอมเพล็กซ์ </label>
															</div>
														</div>
													</div>
													<div class="row transfer">
														<div class="g24-col-sm-24">
															<div class="form-group">
																<label class="control-label g24-col-sm-1" for="transfer_bank_account_name"></label>
																<input type="radio" name="xd_bank_id" id="xd_4" onclick="set_bank_choose('');set_branch_code_choose('');"><label for="xd_4"> บัญชีเงินฝาก </label>
																<select class="form-control" name="transfer_bank_account_name" id="transfer_bank_account_name" style="display: initial !important;width: 200px !important;">
																	<option value="">เลือกบัญชี</option>
																	<?php foreach ($maco_account as $key => $value){ ?>
																		<option value="<?php echo $value['account_id']; ?>"><?php echo $value['account_id']; ?></option>
																	<?php } ?>
																</select>
															</div>
														</div>
														<div class="g24-col-sm-24">
															<div class="form-group">
																<label class="control-label g24-col-sm-1" for="transfer_bank_account_name"></label>
																<input type="radio" name="xd_bank_id" id="xd_5" onclick="set_bank_choose('');set_branch_code_choose('');"><label for="xd_5"> อื่นๆ </label>
																<input type="text" name="transfer_other" id="transfer_other" class="form-control" style="display: initial !important;width: 200px !important;">
															</div>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="form-group">
							<div class="row">
								<div class="g24-col-sm-24" id="withdrawal_che_sec" style="display:show;">
									<div class="form-group">
										<label class="control-label g24-col-sm-4 m-b-1"></label>
										<div class="g24-col-sm-17">
											<div id="cheque_deposit">
												<div class="cheque_content">
													<div class="row cheque">
														<div class="g24-col-sm-24">
															<div class="form-group">
																<label class="control-label g24-col-sm-1" for="transfer_bank_account_name"></label>
																<input type="radio" name="che_bank_id" id="che_1" onclick="set_bank_choose('006');set_branch_code_choose('0071');"><label for="che_1"> ธ.กรุงไทย จำกัด สาขาการปิโตรเลียม</label>
															</div>
														</div>
														<div class="g24-col-sm-24">
															<div class="form-group">
																<label class="control-label g24-col-sm-1" for="transfer_bank_account_name"></label>
																<input type="radio" name="che_bank_id" id="che_2" onclick="set_bank_choose('002');set_branch_code_choose('1082');"><label for="che_2"> ธ.กรุงเทพ จำกัด สาขาเอนเนอร์ยี่ คอมเพล็กซ์</label>
															</div>
														</div>
														<div class="g24-col-sm-24">
															<div class="form-group">
																<label class="control-label g24-col-sm-1" for="transfer_bank_account_name"></label>
																<input type="radio" name="che_bank_id" id="che_3" onclick="set_bank_choose('011');set_branch_code_choose('0211');"><label for="che_3"> ธ.ทหารไทย จำกัด สาขาเอนเนอร์ยี่คอมเพล็กซ์ </label>
															</div>
														</div>
													</div>
													<div class="row cheque">
														<div class="g24-col-sm-24">
															<div class="form-group">
																<label class="control-label g24-col-sm-4" for="cheque_number">&nbsp;&nbsp;&nbsp;หมายเลขเช็ค :</label>
																<input class="form-control g24-col-sm-10" name="cheque_number" id="cheque_number" placeholder="ระบุบัญชีเงินฝาก" />
															</div>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="form-group">
							<div class="row">
								<div class="g24-col-sm-24" id="withdrawal_other_sec" style="display:none;">
									<div class="form-group">
										<label class="control-label g24-col-sm-4 m-b-1"></label>
										<div class="g24-col-sm-17">
											<div id="cheque_deposit">
												<div class="cheque_content">
													<div class="row cheque">
														<div class="g24-col-sm-24">
															<div class="form-group">
																<label class="control-label g24-col-sm-4" for="other">&nbsp;&nbsp;&nbsp;อื่นๆ :</label>
																<input class="form-control g24-col-sm-10" name="other" id="other" placeholder="ระบุ" />
															</div>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>

						<div class="form-group">
							<div class="row">
								<div class="g24-col-sm-24 text-center m-t-2 chooses_footer">
									<input type="hidden" name="bank_id" id="bank_id">
									<input type="hidden" name="branch_code" id="branch_code">
									<button class="btn btn-danger" type="button" id="Wd_chooses">ถอนเงิน</button>
									<button class="btn btn-default bt_close" data-dismiss="modal" type="button">ยกเลิก </button>
								</div>
							</div>
						</div>
					</div>
			</div>
			</form>
		</div>
	</div>
</div>

<div class="modal fade" id="alertWithdrawalChooses" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-dialog-account">
		<div class="modal-content">
			<div class="modal-header modal-header-withdrawal">
				<button type="button" class="close" data-dismiss="modal"></button>
				<h2 class="modal-title">ยืนยันการถอนเงิน</h2>
			</div>
			<div class="modal-body center">
				<p><span class="icon icon-arrow-circle-o-up" style="font-size:75px;"></span></p>
				<p style="font-size:18px;">ถอนเงินจำนวน <span id="deposit_chooses_text"> </span> <span id="deposit_chooses_account"> </span> บาท</p>
			</div>
			<div class="modal-footer center">
				<!--<form action="<?php echo base_url(PROJECTPATH . '/save_money/save_transaction'); ?>" method="POST">-->
				<form action="" method="POST">
					<input type="hidden" name="do" value="withdrawal">
					<input type="hidden" name="account_id" value="" id="account_id">
					<input type="hidden" name="money" value="" id="money">
					<input type="hidden" name="commission_fee" value="" id="commission_fee_c">
					<input type="hidden" name="total_amount" value="" id="total_amount_c">
					<input type="hidden" name="pay_type" value="" id="pay_type_c">
					<input type="hidden" name="transaction_list" value="<?php echo $row_with['money_type_name_short']; ?>" id="transaction_list">
					<input type="hidden" name="fix_withdrawal_status" value="" id="fix_withdrawal_status_c">
					<input type="hidden" name="custom_by_user_id" class="custom_by_user_id" value="">
					<button class="btn btn-danger" type="button" id="bt_confirm_wd_chooses">ยืนยันถอนเงิน</button>
					<button type="button" class="btn btn-default bt_close" data-dismiss="modal">ยกเลิก</button>
				</form>
			</div>
		</div>
	</div>
</div>


<script>
	var base_url = $('#base_url').attr('class');

	$(document).ready(function(){
		$("#date_fixed").datepicker({
			prevText : "ก่อนหน้า",
			nextText: "ถัดไป",
			currentText: "Today",
			changeMonth: true,
			changeYear: true,
			isBuddhist: true,
			monthNamesShort: ['ม.ค.', 'ก.พ.', 'มี.ค.', 'เม.ย.', 'พ.ค.', 'มิ.ย.', 'ก.ค.', 'ส.ค.', 'ก.ย.', 'ต.ค.', 'พ.ย.', 'ธ.ค.'],
			dayNamesMin: ['อา', 'จ', 'อ', 'พ', 'พฤ', 'ศ', 'ส'],
			constrainInput: true,
			dateFormat: "dd/mm/yy",
			yearRange: "c-50:c+10",
			autoclose: true,
		});
	});

	function check_tax(ele) {
		var balance_deposit = 0;
		var money_withdrawal = 0;
		var amount_int = 0;
		var amount_tax = 0;
		var account_id = $("#WithdrawalChooses").find('.modal-body #account_id').val();

		$("#" + ele.id).each(function() {
			var data_key = $(this).attr("data-key");
			balance_deposit = parseFloat(removeCommas($('#balance_deposit_' + data_key).val()));
			money_withdrawal = parseFloat(removeCommas($('#money_withdrawal_' + data_key).val()));
			transaction_id = $('#transaction_id_' + data_key).val();
			amount_balance = parseFloat(removeCommas($('#amount_balance_' + data_key).val()));

			if (isNaN(balance_deposit)) {
				balance_deposit = 0;
			}

			if (isNaN(money_withdrawal)) {
				money_withdrawal = 0;
			}
			//console.log('money_withdrawal='+money_withdrawal);
			amount_balance = (balance_deposit - money_withdrawal);

			var data = {
				transaction_id: transaction_id,
				account_id: account_id,
				money_withdrawal: money_withdrawal
			};
			var entry_date = $('#entry_date');
			if (typeof entry_date.val() !== "undefined" && entry_date.val() !== "") {
				data.date = entry_date.val();
			}
			$.ajax({
				method: 'POST',
				url: base_url + 'save_money/check_deposit_interest',
				data: data,
				success: function(result) {
					var obj = JSON.parse(result);
					//คำนวณดอกเบี้ย
					amount_int = 0;
					if(obj.detail.length) {
						for(i=0; i < obj.detail.length; i++) {
							amount_int = amount_int + parseFloat(obj.detail[i].interest);
						}
					} else {
						amount_int = obj.interest;
					}

					//คำนวณภาษี
					amount_tax = obj.tax;
					$('#amount_int_' + data_key).val(addCommas(amount_int));
					$('#amount_tax_' + data_key).val(addCommas(amount_tax));

					if ($('#check_withdrawal_int_' + data_key).attr('checked') == "checked") {
						var money_withdrawal_int = parseFloat(amount_int - amount_tax);
						$('#money_withdrawal_int_' + data_key).val(addCommas(money_withdrawal_int.toFixed(2))); //จำนวนเงินที่ถอนของดอกเบี้ย
					}

					check_withdrawal(ele);
				}
			});
		});
	}

	function check_withdrawal(ele) {
		var balance_deposit = 0;
		var money_withdrawal = 0;
		var amount_withdrawal = 0;
		var amount_int = 0;
		var amount_int_all = 0;
		var amount_tax = 0;
		var amount_tax_all = 0;
		var amount_balance = 0;
		var amount_balance_all = 0;
		var amount_received_all = 0;
		var amount_withdrawal_all = 0;
		var account_id = $("#WithdrawalChooses").find('.modal-body #account_id').val();
		//console.log(ele);
		$("input[name^=money_withdrawal]").each(function() {
			var data_key = $(this).attr("data-key");
			var data_id = $(this).attr("id");
			//console.log('data_id='+data_id);			
			balance_deposit = parseFloat(removeCommas($('#balance_deposit_' + data_key).val()));
			money_withdrawal = parseFloat(removeCommas($('#money_withdrawal_' + data_key).val()));
			transaction_id = $('#transaction_id_' + data_key).val();
			amount_balance = parseFloat(removeCommas($('#amount_balance_' + data_key).val()));

			if (isNaN(balance_deposit)) {
				balance_deposit = 0;
			}

			if (isNaN(money_withdrawal)) {
				money_withdrawal = 0;
			}

			if (money_withdrawal > balance_deposit) {
				money_withdrawal = 0;
				amount_int = 0;
				amount_tax = 0;
				$('#money_withdrawal_' + data_key).val('');
				$('#amount_int_' + data_key).val('');
				$('#amount_tax_' + data_key).val('');
				swal('จำนวนเงินที่ถอนต้องไม่เกินยอดเดิม');
			}

			if ($('#check_withdrawal_int_' + data_key).attr('checked') == "checked") {
				var money_withdrawal_int = parseFloat(removeCommas($('#money_withdrawal_int_' + data_key).val()));
				console.log(money_withdrawal_int);
				amount_int_all = money_withdrawal_int;
				amount_tax_all = 0;
				amount_withdrawal = 0;
				amount_balance = (balance_deposit - money_withdrawal);
				amount_balance_all += money_withdrawal;
			} else {
				amount_withdrawal += money_withdrawal;
				amount_balance = (balance_deposit - money_withdrawal);
				amount_balance_all += amount_balance;

				//คำนวณดอกเบี้ย
				amount_int = parseFloat(removeCommas($('#amount_int_' + data_key).val()));
				if (isNaN(amount_int)) {
					amount_int = 0;
				}
				amount_int_all += amount_int;
				//คำนวณภาษี
				amount_tax = parseFloat(removeCommas($('#amount_tax_' + data_key).val()));

				if (isNaN(amount_tax)) {
					amount_tax = 0;
				}

				amount_tax_all += amount_tax;
			}

			$('#amount_balance_' + data_key).val(addCommas(amount_balance.toFixed(2)));
		})

		amount_received_all = (amount_withdrawal + amount_int_all) - amount_tax_all;
		$('#c_amount_withdrawal').val(addCommas(amount_withdrawal.toFixed(2)));
		$('#c_amount_balance').val(addCommas(amount_balance_all.toFixed(2)));
		$('#c_amount_int').val(addCommas(amount_int_all.toFixed(2)));
		$('#c_amount_tax').val(addCommas(amount_tax_all.toFixed(2)));
		$('#c_amount_received').val(addCommas(amount_received_all.toFixed(2)));
	}


	$("#Wd_chooses").on('click', function() {
		var staus_close_principal = $("#staus_close_principal").val();
		var total_amount = $("#total_amount").val();
		var total_amount_account = $("#total_amount_account").val();
		var total_amount_account_val = removeCommas(total_amount_account);
		var sequester_status = $('#sequester_status').val();
		var sequester_amount = $('#sequester_amount').val();
		var sequester_amount_val = removeCommas(sequester_amount);
		var withdrawal_amount = total_amount_account_val - sequester_amount_val; //ยอดเงินที่ถอนได้


		if (staus_close_principal == 1) {
			console.log("111");
			$("#confirm_wd_modal").modal("show");
		} else if (parseInt(total_amount) > parseInt(total_amount_account_val)) {
			console.log("333");
			swal("ยอดเงินของท่านมีไม่เพียงพอสำหรับการถอน  \nกรุณากรอกจำนวนเงินไม่เกิน   " + total_amount_account + " บาท");
		} else if (sequester_status == '2' && parseInt(total_amount) > parseInt(withdrawal_amount)) {
			console.log("444");
			swal("ไม่สามารถถอนเงินได้เนื่องจาก\nบัญชีนี้ถูกอายัดยอดเงิน " + sequester_amount + " บาท \nสามารถถอนเงินได้ " + addCommas(withdrawal_amount) + " บาท");
		} else {
			console.log("555");
			check_wd_chooses();
		}
	});

	function check_wd_chooses() {
		var total_amount = $("#total_amount").val();
		var total_amount_account = $("#total_amount_account").val();
		var total_amount_account_val = removeCommas(total_amount_account);
		var sequester_status = $('#sequester_status').val();
		var sequester_amount = $('#sequester_amount').val();
		var sequester_amount_val = removeCommas(sequester_amount);
		var withdrawal_amount = total_amount_account_val - sequester_amount_val; //ยอดเงินที่ถอนได้
		var fix_withdrawal_status = $('#fix_withdrawal_status').val();
		var money_withdrawal = removeCommas($('#money_withdrawal').val());
		var type_id = $("#type_id").val();
		var account_id = $("#WithdrawalChooses").find('.modal-body #account_id').val();

		$.ajax({
			method: 'POST',
			url: base_url + 'save_money/check_max_min_withdrawal',
			data: {
				money: money_withdrawal,
				type_id: type_id,
				account_id: account_id
			},
			success: function(msg) {
				if (msg == 'Y') {
					$('#WithdrawalChooses').find('.modal-body #alert').hide();
					var account = $("#WithdrawalChooses").find('.modal-body #account_id').val();
					var deposit = $("#WithdrawalChooses").find('.modal-body #c_amount_received').val();
					var modal = $('#alertWithdrawal');
					var commission_fee_c = $("#WithdrawalChooses").find('.modal-body #commission_fee').val();
					var total_amount_c = $("#WithdrawalChooses").find('.modal-body #total_amount').val();
					if ($("#WithdrawalChooses").find('.modal-body #pay_type_withdraw_0').is(':checked')) {
						var pay_type = '0';
					} else {
						var pay_type = '1';
					}
					//console.log('deposit='+deposit);
					$('#deposit_chooses_text').html(deposit);
					$('#alertWithdrawalChooses').modal("show");

				} else {
					swal(msg);
				}
			}
		});
	}

	$("#bt_confirm_wd_chooses").on('click', function() {
		$("#save_transaction_chooses").submit();
	});

	$(".check_int").change(function() {
		var data_key = $(this).attr("data-key");
		var balance_deposit_n = $('#balance_deposit_' + data_key).val();
		if ($(this).attr('checked') == "checked") {
			$('#money_withdrawal_' + data_key).val(addCommas(balance_deposit_n));
			$('#money_withdrawal_' + data_key).attr('readonly', true);
			$('#money_withdrawal_int_' + data_key).removeAttr("disabled");
			check_tax(this);
		} else {
			$('#money_withdrawal_' + data_key).val('');
			$('#amount_int_' + data_key).val('');
			$('#amount_tax_' + data_key).val('');
			$('#money_withdrawal_int_' + data_key).val('');
			balance_deposit_n = parseFloat(balance_deposit_n);
			$('#amount_balance_' + data_key).val(addCommas(balance_deposit_n.toFixed(2)));
			$('#money_withdrawal_' + data_key).attr('readonly', false);
			$('#money_withdrawal_int_' + data_key).attr('disabled', true);
			$('#c_amount_withdrawal').val('');
			$('#c_amount_balance').val('');
			$('#c_amount_int').val('');
			$('#c_amount_tax').val('');
			$('#c_amount_received').val('');
		}
	});

	function withdrawal_int(ele) {
		$("#" + ele.id).each(function() {
			var data_key = $(this).attr("data-key-int");
			var money_withdrawal_int = parseFloat(removeCommas($('#money_withdrawal_int_' + data_key).val()));
			var amount_int = parseFloat(removeCommas($('#amount_int_' + data_key).val()));

			if (money_withdrawal_int > amount_int) {
				money_withdrawal = 0;
				amount_int = 0;
				amount_tax = 0;
				$('#money_withdrawal_int_' + data_key).val('');
				swal('จำนวนเงินที่ถอนของดอกเบี้ยต้องไม่เกินดอกเบี้ย');
			}
			check_withdrawal(ele);
		});
	}

	$('#WithdrawalChooses').on('hidden.bs.modal', function(e) {
		$("input[name^=money_withdrawal]").each(function() {
			var data_key = $(this).attr("data-key");
			$('#amount_balance_' + data_key).val('');
			$('#money_withdrawal_' + data_key).val('');
			$('#amount_int_' + data_key).val('');
			$('#amount_tax_' + data_key).val('');
			$('#money_withdrawal_int_' + data_key).val('');
			$('#check_withdrawal_int_' + data_key).attr('checked', false);
			$('#money_withdrawal_' + data_key).attr('readonly', false);
			$('#money_withdrawal_int_' + data_key).attr('disabled', true);
		});
		$('#c_amount_withdrawal').val('');
		$('#c_amount_balance').val('');
		$('#c_amount_int').val('');
		$('#c_amount_tax').val('');
		$('#c_amount_received').val('');
	})

	function format_the_number_decimal(ele) {
		var value = $('#' + ele.id).val();
		value = value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');
		var num = value.split(".");
		var decimal = '';
		var num_decimal = '';
		if (typeof num[1] !== 'undefined') {
			if (num[1].length > 2) {
				num_decimal = num[1].substring(0, 2);
			} else {
				num_decimal = num[1];
			}
			decimal = "." + num_decimal;

		}

		if (value != '') {
			if (value == 'NaN') {
				$('#' + ele.id).val('');
			} else {
				value = (num[0] == '') ? 0 : parseInt(num[0]);
				value = value.toLocaleString() + decimal;
				$('#' + ele.id).val(value);
			}
		} else {
			$('#' + ele.id).val('');
		}
	}

	function set_bank_choose(val) {
		var modal = $('#WithdrawalChooses');
        modal.find('.chooses_footer #bank_id').val(val);
	}

	function set_branch_code_choose(val) {
		var modal = $('#WithdrawalChooses');
        modal.find('.chooses_footer #branch_code').val(val);
	}

	function show(val) {
		
		// var modal = $('#WithdrawalChooses');
		// modal.find('.modal-footer #withdrawal_xd_sec').show();
		console.log("show", val);
		if (val == 'withdrawal_xd_sec') {
			$("#withdrawal_xd_sec").show();
			$("#withdrawal_che_sec").hide();
			$("#withdrawal_other_sec").hide();
		} else if (val == 'withdrawal_che_sec') {
			$("#withdrawal_xd_sec").hide();
			$("#withdrawal_che_sec").show();
			$("#withdrawal_other_sec").hide();
		} else if (val == 'withdrawal_other_sec') {
			$("#withdrawal_xd_sec").hide();
			$("#withdrawal_che_sec").hide();
			$("#withdrawal_other_sec").show();
		} else {
			$("#withdrawal_xd_sec").hide();
			$("#withdrawal_che_sec").hide();
			$("#withdrawal_other_sec").hide();
		}

	}

	$('#date_fixed').on('change', function(){
		$('#date_fixed_transaction').val($(this).val());
	})

</script>
