<div id="update_interest" tabindex="-1" role="dialog" class="modal fade">
	<div class="modal-dialog modal-dialog-account">
		<div class="modal-content">
			<div class="modal-header modal-header-deposit">
				<h2 class="modal-title">เพิ่มดอกเบี้ยด้วยตนเอง</h2>
			</div>
			<div class="modal-body" style="padding: 50px 0 !important;">
					<div class="g24-col-sm-24">
						<div class="form-group">
							<label class="control-label g24-col-sm-8">กำหนดวันที่คิดดอกเบี้ย</label>
							<div class="g24-col-sm-6" style="margin-bottom: 5px;padding-top: 5px;">
								<div class="input-with-icon g24-col-sm-24">
									<div class="form-group">
										<input id="account_id" type="hidden" name="account_id"
											   value="<?php echo $_GET['account_id']; ?>">
										<input id="date_interesting" name="date_interesting" class="form-control m-b-1"
											   style="padding-left: 50px;" type="text" data-date-language="th-th"
											   title="กรุณาป้อน วันที่" value="<?php echo date('d/m/Y', strtotime('+543 Year'))?>">
										<span class="icon icon-calendar input-icon m-f-1"></span>
									</div>
								</div>
							</div>
							<label class="g24-col-sm-1 control-label">เวลา</label>
							<div class="g24-col-sm-5">
								<div class="input-with-icon">
									<div class="form-group">
										<input id="time_interesting" name="time_interesting" class="form-control m-b-1 timepicker" type="text" value="<?php echo "04:00"; ?>">
										<span class="icon icon-clock-o input-icon"></span>
									</div>
								</div>
							</div>
						</div>
					</div>
			</div>
			<div class="modal-footer" style=" border-top: none;">
				<div class="g24-col-sm-24 text-center m-t-2">
					<button class="btn btn-primary" type="button" onclick="call_interesting()">ยืนยัน</button>
					<button class="btn btn-default" data-dismiss="modal" type="button">ปิดหน้าต่าง</button>
				</div>
			</div>
		</div>
	</div>
</div>
<script type="application/javascript">
	$(document).ready(function () {
		$("#date_interesting").datepicker({
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
	});

	function call_interesting() {

		if (typeof $('#date_interesting').val() === "undefined" || $('#date_interesting').val() === "") {
			swal('กรถณาเลือกวันที่', '', 'warning');
			return false;
		}

		swal({
			title: "ท่านต้องการเพิ่มรายการดอกเบี้ยเงินฝากด้วยตัวเองใช่หรือไม่?",
			text: "การเพิ่มรายการดอกเบี้ยเงินฝากด้วยตัวเองจะเป็นการเพิ่มรายการดอกเบี้ยในรายการเงินฝากบัญชีนี้",
			type: "warning",
			showCancelButton: true,
			confirmButtonColor: '#DD6B55',
			confirmButtonText: 'ยืนยัน',
			cancelButtonText: "ยกเลิก",
			closeOnConfirm: true,
			closeOnCancel: true,
		}, function (isConfirm) {
			if (isConfirm) {

				const v_account = $('#update_interest #account_id').val();
				const v_date_interesting = typeof $('#date_interesting').val() !== "undefined" ? $('#date_interesting').val().split('/').reverse().map((v, k) => {
					return k === 0 ? (parseInt(v) - 543).toString() : v;
				}).join('-') : moment().format('L').split('/').reverse().join('-');
				const v_time_interesting = $('#time_interesting').val();
				let data = {account_id: v_account, date_interesting: v_date_interesting, time_interesting: v_time_interesting};
				$.post(base_url + '/save_money/udpate_interest', data, function (res) {
					console.log(res);
					$('#update_interest').modal('hide');
					swal('สำเร็จ', '', 'success');
					setTimeout(function () {
						$.get(window.location.href, function (data) {
							$('.bs-example #table').replaceWith($(data).find('.bs-example #table'));
						})
					})
				})
			}
		});
	}
</script>
