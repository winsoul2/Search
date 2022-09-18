<div class="col-md-12 ">
	<form data-toggle="validator" novalidate="novalidate" id="frm1" action="<?php echo base_url(PROJECTPATH . '/save_money/save_add_save_money'); ?>" method="post">
		<?php
		if ($account_id != '') {
			$action_type = 'edit';
		} else {
			$action_type = 'add';
		}
		?>
		<input type="hidden" id="action_type" name="action_type" value="<?php echo $action_type; ?>">
		<div class="form-group">
			<?php if ($row['account_id'] != '') { ?>
				<div class="g24-col-sm-24">
					<label class="col-sm-3 control-label" for="form-control-2"> เลขที่บัญชี </label>
					<div class="col-sm-9">
						<div id="form_acc_id" class="form-group input-group">
							<input type="hidden" id="old_account_no" name="old_account_no">
							<input class="form-control m-b-1 has-success" type="text" id="acc_id" name="acc_id" value="<?php echo empty($row['account_id']) ? '' : $row['account_id']; ?>" required readonly>
							<span class="input-group-btn">
								<a class="" href="#">
									<button id="edit_account_no" type="button" class="btn btn-info btn-search"><span class="icon icon-edit"></span></button>
								</a>
							</span>
						</div>
						<!-- <input class="form-control m-b-1" type="text" id="acc_id" name="acc_id" value="<?php echo empty($row['account_id']) ? '' : $row['account_id']; ?>" required readonly> -->
					</div>
				</div>
				<br><br>
			<?php } ?>
			<div class="g24-col-sm-24">
				<label class="col-sm-3 control-label" for="form-control-2">รหัสสมาชิก</label>
				<div class="col-sm-9 m-b-1">
					<div class="input-group">
						<input value="<?php echo empty($row['account_id']) ? '' : $row['mem_id'] ?>" class="form-control m-b-1" type="text" name="mem_id" id="member_id_add" required onkeypress="check_member_id();">
						<span class="input-group-btn">
							<a class="" data-toggle="modal" data-target="#search_member_add_modal" href="#">
								<button id="" type="button" class="btn btn-info btn-search"><span class="icon icon-search"></span></button>
							</a>
						</span>
					</div>
				</div>
			</div>

			<div class="g24-col-sm-24">
				<label class="col-sm-3 control-label" for="form-control-2">ชื่อ - นามสกุล</label>
				<?php
				if ($action_type == 'add') {
				?>
					<div class="col-sm-6">
						<input value="<?php echo empty($row['account_id']) ? '' : $row['member_name'] ?>" class="form-control m-b-1" type="text" name="member_name" id="member_name_add" required readonly>
					</div>
					<div class="col-sm-2">
						<button id="bt_yourself" name="bt_yourself" type="button" class="btn btn-primary" style="width: auto;" onclick="click_bt_yourself();">กำหนดเลขบัญชีเอง</button>
					</div>
				<?php
				} else {
				?>
					<div class="col-sm-9">
						<input value="<?php echo empty($row['account_id']) ? '' : $row['member_name'] ?>" class="form-control m-b-1" type="text" name="member_name" id="member_name_add" required readonly>
					</div>

				<?php
				}
				?>

			</div>

			<div class="g24-col-sm-24 show_acc_id_yourself" style="display:none;">
				<label class="col-sm-3 control-label" for="form-control-2"> เลขที่บัญชี </label>
				<div class="col-sm-9">
					<!--					<input class="form-control m-b-1" type="text" id="acc_id" name="acc_id" value="--><?php //echo $row['account_id'] ? '' : $row['account_id']; 
																																?>
					<!--" required readonly>-->
					<input class="form-control m-b-1" type="text" id="acc_id_yourself" name="acc_id_yourself" value="<?php echo @$row['account_id'] ?>" <?php echo $action_type == 'edit' ? '' : 'readonly'; ?>" required>
				</div>
			</div>

			<div class="g24-col-sm-24">
				<label class="col-sm-3 control-label" for="form-control-2" require>ชื่อบัญชี</label>
				<div class="col-sm-9">
					<input name="acc_name" class="form-control m-b-1" type="text" id="acc_name_add" value="<?php echo @$row['account_name'] ?>" <?php echo $action_type == 'edit' ? '' : 'readonly'; ?> autofocus>
				</div>
			</div>
			<div class="g24-col-sm-24">
				<label class="col-sm-3 control-label" for="form-control-2" require>ชื่อบัญชีภาษาอังกฤษ</label>
				<div class="col-sm-9">
					<input name="account_name_eng" class="form-control m-b-1" type="text" id="account_name_eng" value="<?php echo @$row['account_name_eng'] ?>" <?php echo $action_type == 'edit' ? '' : 'readonly'; ?> autofocus>
				</div>
			</div>
			<div class="g24-col-sm-24">
				<label class="col-sm-3 control-label" for="form-control-2">ประเภทบัญชี</label>
				<div class="col-sm-9">
					<?php if ($action_type == 'edit') { ?>
						<input class="form-control m-b-1" type="text" value="<?php echo @$row['type_name'] ?>" readonly>
						<input type="hidden" id="type_id" name="type_id" value="<?php echo @$row['type_id'] ?>">
					<?php } else { ?>
						<select class="form-control m-b-1" id="type_id" name="type_id" <?php echo $action_type == 'edit' ? '' : 'readonly'; ?> onchange="change_account_type()" require>
							<option value="">เลือกประเภทบัญชี</option>
							<?php foreach ($type_id as $key => $value) { ?>
								<option value="<?php echo $value['type_id']; ?>" unique_account="<?php echo $value['unique_account']; ?>" type_code="<?php echo $value['type_code']; ?>" <?php echo $value['type_id'] == @$row['type_id'] ? 'selected' : ''; ?>><?php echo $value['type_code'] . " : " . $value['type_name']; ?></option>
							<?php } ?>
						</select>
					<?php } ?>
				</div>
			</div>
			<div class="g24-col-sm-24">
				<label class="col-sm-3 control-label" for="form-control-2" require>ระบุยอดเงินเปิดบัญชี</label>
				<div class="col-sm-9">
					<input name="min_first_deposit" class="form-control m-b-1" type="text" id="min_first_deposit" value="" <?php echo $action_type == 'edit' ? 'readonly' : ''; ?> required>
				</div>
			</div>
			<div class="g24-col-sm-24">
				<label class="col-sm-3 control-label" for="form-control-2" require>วิธีการชำระเงิน</label>
				<div class="col-sm-9">
					<input type="radio" name="pay_type_tmp" value="0" onclick="set_bank('');set_branch_code('');show('');" checked=""> เงินสด <i class="fa fa-money"></i> &nbsp;&nbsp;&nbsp;&nbsp;
					<input type="radio" name="pay_type_tmp" value="1" onclick="set_bank('');set_branch_code('');show('opn_xd_sec');"> โอนเงิน <i class="fa fa-credit-card"></i> &nbsp;&nbsp;&nbsp;&nbsp;
					<input type="radio" name="pay_type_tmp" value="2" onclick="set_bank('');set_branch_code('');show('opn_che_sec');"> เช็ค <i class="fa fa-university"></i> &nbsp;&nbsp;&nbsp;&nbsp;
					<input type="radio" name="pay_type_tmp" value="3" onclick="set_bank('');set_branch_code('');show('opn_other_sec');"> อื่นๆ <i class="fa fa-ellipsis-h"></i>
				</div>
			</div>
			<div class="g24-col-sm-24" id="opn_xd_sec" style="display:none;">
				<div class="form-group">
					<label class="control-label g24-col-sm-7 m-b-1"></label>
					<div class="g24-col-sm-17">
						<div id="transfer_deposit">
							<div class="transfer_content">
								<div class="row transfer">
									<div class="g24-col-sm-24">
										<div class="form-group">
											<label class="control-label g24-col-sm-1" for="transfer_bank_account_name"></label>
											<input type="radio" name="xd_bank_id" id="xd_1" onclick="set_bank('006');set_branch_code('0071');"><label for="xd_1"> ธ.กรุงไทย จำกัด สาขาการปิโตรเลียม</label>
										</div>
									</div>
									<div class="g24-col-sm-24">
										<div class="form-group">
											<label class="control-label g24-col-sm-1" for="transfer_bank_account_name"></label>
											<input type="radio" name="xd_bank_id" id="xd_2" onclick="set_bank('002');set_branch_code('1082');"><label for="xd_2"> ธ.กรุงเทพ จำกัด สาขาเอนเนอร์ยี่ คอมเพล็กซ์</label>
										</div>
									</div>
									<div class="g24-col-sm-24">
										<div class="form-group">
											<label class="control-label g24-col-sm-1" for="transfer_bank_account_name"></label>
											<input type="radio" name="xd_bank_id" id="xd_3" onclick="set_bank('011');set_branch_code('0211');"><label for="xd_3"> ธ.ทหารไทย จำกัด สาขาเอนเนอร์ยี่คอมเพล็กซ์ </label>
										</div>
									</div>
								</div>
								<div class="row transfer">
									<div class="g24-col-sm-24">
										<div class="form-group">
											<label class="control-label g24-col-sm-1" for="transfer_bank_account_name"></label>
											<input type="radio" name="xd_bank_id" id="xd_4" onclick="set_bank('');set_branch_code('');"><label for="xd_4"> บัญชีเงินฝาก </label>
											<select class="form-control" name="transfer_bank_account_name" id="transfer_bank_account_name" style="display: initial !important;width: 200px !important;">
												<option value="">เลือกบัญชี</option>
											</select>
										</div>
									</div>
									<div class="g24-col-sm-24">
										<div class="form-group">
											<label class="control-label g24-col-sm-1" for="transfer_bank_account_name"></label>
											<input type="radio" name="xd_bank_id" id="xd_5" onclick="set_bank('');set_branch_code('');"><label for="xd_5"> อื่นๆ </label>
											<input type="text" name="transfer_other" id="transfer_other" class="form-control" style="display: initial !important;width: 200px !important;">
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="g24-col-sm-24" id="opn_che_sec" style="display:none;">
				<div class="form-group">
					<label class="control-label g24-col-sm-7 m-b-1"></label>
					<div class="g24-col-sm-17">
						<div id="cheque_deposit">
							<div class="cheque_content">
								<div class="row cheque">
									<div class="g24-col-sm-24">
										<div class="form-group">
											<label class="control-label g24-col-sm-1" for="transfer_bank_account_name"></label>
											<input type="radio" name="che_bank_id" id="che_1" onclick="set_bank('006');set_branch_code('0071');"><label for="che_1"> ธ.กรุงไทย จำกัด สาขาการปิโตรเลียม</label>
										</div>
									</div>
									<div class="g24-col-sm-24">
										<div class="form-group">
											<label class="control-label g24-col-sm-1" for="transfer_bank_account_name"></label>
											<input type="radio" name="che_bank_id" id="che_2" onclick="set_bank('002');set_branch_code('1082');"><label for="che_2"> ธ.กรุงเทพ จำกัด สาขาเอนเนอร์ยี่ คอมเพล็กซ์</label>
										</div>
									</div>
									<div class="g24-col-sm-24">
										<div class="form-group">
											<label class="control-label g24-col-sm-1" for="transfer_bank_account_name"></label>
											<input type="radio" name="che_bank_id" id="che_3" onclick="set_bank('011');set_branch_code('0211');"><label for="che_3"> ธ.ทหารไทย จำกัด สาขาเอนเนอร์ยี่คอมเพล็กซ์ </label>
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
			<div class="g24-col-sm-24" id="opn_other_sec" style="display:none;">
				<div class="form-group">
					<label class="control-label g24-col-sm-7 m-b-1"></label>
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
			<div class="g24-col-sm-24">
				<label class="col-sm-3 control-label" for="form-control-2" require>วันที่เปิดบัญชี</label>
				<div class="col-sm-9">
					<div class="input-with-icon">
						<div class="form-group">
							<?php
							$opn_date = date('d/m/') . (date('Y') + 543);
							if (@$row['created'] != '') {
								$tmp_opn_date = explode('-', explode(' ', $row['created'])[0]);
								$opn_date = $tmp_opn_date[2] . "/" . $tmp_opn_date[1] . "/" . ($tmp_opn_date[0] + 543);
							}
							?>
							<div id="form_acc_id" class="form-group input-group">
								<input id="opn_date" name="opn_date" class="form-control m-b-1 mydate" style="padding-left: 50px;" type="text" data-date-language="th-th" value="<?= $opn_date ?>" <?= ($action_type == 'edit') ? "readonly" : "" ?>>
								<span class="icon icon-calendar input-icon m-f-1"></span>
								<span class="input-group-btn">
									<a class="" href="#">
										<button id="edit_opn_date" type="button" class="btn btn-info btn-search"><span class="icon icon-edit"></span></button>
									</a>
								</span>
							</div>


						</div>
					</div>
				</div>
			</div>
			<div class="g24-col-sm-24" style="margin: 6px 0px 6px 0px;">
				<label class="col-sm-3 control-label" for="form-control-2" require>บัญชีคู่โอน</label>
				<div class="col-sm-9">
					<?php
					// var_dump($row);
					?>
					<select name="account_transfer" id="account_transfer" class="form-control">
						<option value="">เลือกบัญชีคู่โอน</option>
						<?php
						if ($account_list_transfer) {
							foreach ($account_list_transfer as $key => $value_account_list) {
								if ($row['id_transfer'] == $value_account_list['id']) {
									echo "<option value='" . $value_account_list['id'] . "' selected>" . $value_account_list['text'] . "</option>";
								} else {
									echo "<option value='" . $value_account_list['id'] . "'>" . $value_account_list['text'] . "</option>";
								}
							}
						}
						?>
					</select>
				</div>
			</div>
			<?php if ($action_type == 'edit') { ?>
				<div class="g24-col-sm-24">
					<label class="col-sm-3 control-label" for="form-control-2">อายัดบัญชี</label>
					<div class="col-sm-9">
						<div style="margin-top: 4px;">
							<input type="radio" name="sequester_status" id="sequester_status_0" value="0" onclick="change_type()" <?php echo (@$row['sequester_status'] == '0' || @$row['sequester_status'] == '') ? 'checked' : ''; ?>><label>&nbsp;ไม่อายัด &nbsp;&nbsp;</label>
							<input type="radio" name="sequester_status" id="sequester_status_1" value="1" onclick="change_type()" <?php echo (@$row['sequester_status'] == '1') ? 'checked' : ''; ?>><label>&nbsp;อายัดทั้งหมด &nbsp;&nbsp;</label>
							<input type="radio" name="sequester_status" id="sequester_status_2" value="2" onclick="change_type()" <?php echo (@$row['sequester_status'] == '2') ? 'checked' : ''; ?>><label>&nbsp; อายัดบางส่วน &nbsp;&nbsp;</label>
						</div>
					</div>
				</div>
				<div class="g24-col-sm-24 show_sequester_amount" style="display:none;">
					<label class="col-sm-3 control-label">จำนวนเงินอายัด</label>
					<div class="col-sm-4">
						<input name="sequester_amount" class="form-control m-b-1" type="text" id="sequester_amount" value="<?php echo number_format(@$row['sequester_amount'], 0) ?>" onkeyup="format_the_number(this);">
					</div>
					<label class="col-sm-1 control-label text-left">บาท</label>
				</div>
				<div class="g24-col-sm-24">
					<label class="col-sm-3 control-label" for="form-control-2">อายัด ATM</label>
					<div class="col-sm-9">
						<div style="margin-top: 4px;">
							<?php $sequester_status_atm_disabled = (@$row['sequester_status'] == '1') ? 'disabled' : ''; ?>
							<input type="radio" class="sequester_status_atm" name="sequester_status_atm" id="sequester_status_atm_0" value="0" onclick="check_remark()" <?php echo (@$row['sequester_status_atm'] == '0' || @$row['sequester_status_atm'] == '') ? 'checked' : ''; ?> <?php echo $sequester_status_atm_disabled; ?>><label>&nbsp;ไม่อายัด &nbsp;&nbsp;</label>
							<input type="radio" class="sequester_status_atm" name="sequester_status_atm" id="sequester_status_atm_1" value="1" onclick="check_remark()" <?php echo (@$row['sequester_status_atm'] == '1') ? 'checked' : ''; ?> <?php echo $sequester_status_atm_disabled; ?>><label>&nbsp;อายัด &nbsp;&nbsp;</label>
						</div>
					</div>
				</div>
				<?php
				if ($row['sequester_status'] || @$row['sequester_status_atm']) {
				?>
					<div class="g24-col-sm-24">
						<label class="col-sm-3 control-label" for="form-control-2"></label>
						<div class="col-sm-9">
							<div style="margin-top: 4px;">
								<h4>สาเหตุการอายัด : <?= $row['sequester_remark'] ?> โดย <?= $row['user_name'] ?> เวลา <?= $this->center_function->ConvertToThaiDate($row['sequester_time']); ?></h4>
							</div>
						</div>
					</div>

				<?php
				}
				?>
				<div class="g24-col-sm-24" id="div_remark" style="display:none;">
					<label class="col-sm-3 control-label" for="form-control-2">สาเหตุการอายัด</label>
					<div class="col-sm-9">
						<div style="margin-top: 4px;">
							<input name="remark" class="form-control m-b-1" type="text" id="remark" value="" required placeholder="โปรดระบุสาเหตุการอายัด">
						</div>
					</div>
				</div>
			<?php } else { ?>
				<input type="hidden" name="sequester_status" value='0'>
				<input type="hidden" name="sequester_status_atm" value='0'>
				<input type="hidden" name="sequester_amount" value='0'>
			<?php } ?>
			<!--div class="g24-col-sm-24" id="atm_space" style="display:none;">
				<label class="col-sm-3 control-label" for="form-control-2">เลขบัตร ATM</label>
				<div class="col-sm-9">
					<input name="atm_number" class="form-control m-b-1" type="text" id="atm_number" value="<?php echo @$row['atm_number'] ?>" <?php echo @$row['atm_number'] == '' ? '' : 'readonly'; ?>>
				</div>
			</div-->
			<?php //if(@$row['atm_number']!=''){ 
			?>
			<!--div class="g24-col-sm-24" id="cancel_atm_space">
				<label class="col-sm-3 control-label" for="form-control-2"></label>
				<div class="col-sm-9">
					<input name="cancel_atm_number" class="m-b-1" type="checkbox" id="cancel_atm_number" value="1"> อาญัติบัตร ATM
				</div>
			</div-->
			<?php //} 
			?>

		</div>

		<div></div>
		<div class="g24-col-sm-24">
			<div class="col-sm-9 col-sm-offset-4">
				<input type="hidden" name="bank_id" id="bank_id">
				<input type="hidden" name="branch_code" id="branch_code">
				<button type="button" class="btn btn-primary min-width-100" id="btn_save_add" style="margin-left:20px;" onclick="check_submit()">ตกลง</button>
				<button class="btn btn-danger min-width-100" type="button" onclick="window.parent.parent.location.reload();"> ยกเลิก</button>
			</div>
		</div>
	</form>
</div>
<table>
	<tr>
		<td>&nbsp;</td>
	</tr>
</table>

<script>
	$('#edit_account_no').click(() => {
		$('#acc_id').prop('readOnly', false);

		var value = $("#acc_id").val();

		var format = format_account_no(value);

		$("#acc_id").val(format);

	});

	$('#edit_opn_date').click(() => {
		$('#opn_date').prop('readOnly', false);


	});


	$('input[name=acc_id_yourself]').keyup(function() {
		var value = $(this).val();

		var format = format_account_no(value);

		$(this).val(format);
		// console.log(value_real);
	});

	$('input[name=acc_id]').keyup(function() {
		var value = $(this).val();

		var format = format_account_no(value);

		$(this).val(format);

		if (format.replace(/-/g, '').length == 11) {
			$.ajax({
				url: base_url + "ajax/search_account_no",
				method: "post",
				data: {
					search: $(this).val().replace(/-/g, '')
				},
				dataType: "text",
				success: function(data) {
					// $('#result_add').html(data);
					if (data == 0) {
						$("#form_acc_id").addClass("has-success has-feedback");
						$("#form_acc_id").removeClass("has-error has-feedback");
					} else if (data >= 1) {
						$("#form_acc_id").addClass("has-error has-feedback");
						$("#form_acc_id").removeClass("has-success has-feedback");
						swal('เลขที่บัญชีนี้ซ้ำกับข้อมูลในระบบ', '', 'warning');
					}
					console.log("result", data);
				},
				error: function(xhr) {
					console.log('Request Status: ' + xhr.status + ' Status Text: ' + xhr.statusText + ' ' + xhr.responseText);
				}
			});


		} else {
			$("#form_acc_id").addClass("has-error has-feedback");
			$("#form_acc_id").removeClass("has-success has-feedback");
		}
		// console.log(value_real);
	});


	function format_account_no(value) {
		var value_real = value.replace(/-/g, '');
		var add_symbol = '';
		var str = "";
		var arr_number = value_real.split('');
		for (let i = 0; i < arr_number.length; i++) {
			const element = arr_number[i];
			var add_symbol = '';
			/*if(i==2){
				add_symbol = '-';
			}else if(i==4){
				add_symbol = '-';
			}else if(i==9){
				add_symbol = '-';
			}
			if(i>=11){
				continue;
			}
			*/
			add_symbol = '';
			if (i >= 12) {
				continue;
			}

			str += element + add_symbol;
		}
		return str;
	}

	$(document).ready(function() {
		var value = $("#acc_id").val();

		$(".mydate").datepicker({
			prevText: "ก่อนหน้า",
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

		if ($("#acc_id").val() != undefined) {
			var format = format_account_no(value);

			$("#acc_id").val(format);
			$("#old_account_no").val(format.replace(/-/g, ''));
		}

	});



	$('#min_first_deposit').keyup(function(evt, obj) {
		var value = $(this).val();
		var dotcontains = value.indexOf(".") != -1;
		if (dotcontains) {
			return;
		}
		var number_format = numeral(value).format('0,0');
		$(this).val(number_format);
	});

	$('#min_first_deposit').change(function(evt, obj) {
		var value = $(this).val();
		var number_format = numeral(value).format('0,0.00');
		$(this).val(number_format);
	});

	function change_type() {
		if ($('#sequester_status_2').is(':checked')) {
			$('.show_sequester_amount').show();
		} else {
			$('#sequester_amount').val('0');
			$('.show_sequester_amount').hide();
		}
		check_remark();
	}

	function check_remark() {
		var sequester_status = $('input[name=sequester_status]:checked', '#frm1').val();
		var sequester_status_atm = $('input[name=sequester_status_atm]:checked', '#frm1').val();
		if ((sequester_status != 0 || sequester_status_atm != 0) && !$("input[name='sequester_status_atm']").is(':disabled')) {
			$('#div_remark').show();
		} else {
			$('#div_remark').hide();
		}
	}

	function set_bank(val) {
		$("#bank_id").val(val);
	}

	function set_branch_code(val) {
		$("#branch_code").val(val);
	}

	function show(val) {
		if (val == 'opn_xd_sec') {
			$("#opn_xd_sec").show();
			$("#opn_che_sec").hide();
			$("#opn_other_sec").hide();
		} else if (val == 'opn_che_sec') {
			$("#opn_xd_sec").hide();
			$("#opn_che_sec").show();
			$("#opn_other_sec").hide();
		} else if (val == 'opn_other_sec') {
			$("#opn_xd_sec").hide();
			$("#opn_che_sec").hide();
			$("#opn_other_sec").show();
		} else {
			$("#opn_xd_sec").hide();
			$("#opn_che_sec").hide();
			$("#opn_other_sec").hide();
		}

	}
</script>
<script src="//cdnjs.cloudflare.com/ajax/libs/numeral.js/2.0.6/numeral.min.js"></script>