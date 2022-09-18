var base_url = $('#base_url').attr('class');
$(document).ready(function () {
	$("#start_date").datepicker({
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
		startDate: '+1d',
		autoclose: true,
	});

	$("#filter").change(function () {
		document.location.href = base_url + 'setting_deposit_data/coop_deposit_group_setting?filter=' + $(this).val();
	});

});

function add_type() {
	$('#deposit_type_modal').modal('show');
}
function add_group() {
	$('#deposit_type_modal_add').modal('show');
}
function add_group_type() {
	$('#deposit_type_modal_add_group').modal('show');
}
function save_type() {
	$('#form1').submit();
}

function check_form() {
	$('#form_save').submit();
}


function del_interest(id) {
	swal({
			title: "ท่านต้องการลบข้อมูลใช่หรือไม่",
			text: "",
			type: "warning",
			showCancelButton: true,
			confirmButtonColor: '#DD6B55',
			confirmButtonText: 'ลบ',
			cancelButtonText: "ยกเลิก",
			closeOnConfirm: false,
			closeOnCancel: true
		},
		function (isConfirm) {
			if (isConfirm) {
				$.ajax({
					url: base_url + '/setting_deposit_data/del_coop_deposit_group',
					method: 'POST',
					data: {
						'table': 'coop_interest',
						'id': id,
						'field': 'interest_id'
					},
					success: function (msg) {
						// console.log(msg); return false;
						if (msg == 1) {
							document.location.href = base_url + 'setting_deposit_data/coop_deposit_group_setting';
						} else {

						}
					}
				});
			} else {

			}
		});
}

function edit_type(id,group_name_transaction) {
	$('#id').val(id);
	$('#group_name_transaction').val(group_name_transaction);
	$('#deposit_type_modal').modal('show');

}
function edit_type_add(id,id_type,type_name_transection, createdatetime, updatedatetime,status) {
	$('#id').val(id);
	$('#id_type_name').val(id_type_name);
	$('#id_type').val(id_type);
	$('#group_name_transaction').val(type_name_transection);
	$('#createdatetime').val(createdatetime);
	$('#updatedatetime').val(updatedatetime);
	$('#status').val(status);
	$('#deposit_type_modal_add').modal('show');

}


function del_group(id) {

	swal({
			title: "ท่านต้องการลบข้อมูลใช่หรือไม่",
			text: "",
			type: "warning",
			showCancelButton: true,
			confirmButtonColor: '#DD6B55',
			confirmButtonText: 'ลบ',
			cancelButtonText: "ยกเลิก",
			closeOnConfirm: false,
			closeOnCancel: true
		},
		function (isConfirm) {
			if (isConfirm) {

				$.ajax({
					url: base_url + '/setting_deposit_data/check_use_group',
					method: 'POST',
					data: {
						'id': id
					},
					success: function (msg) {
						//console.log(msg); return false;
						if (msg == 1) {
							$.ajax({
								url: base_url + '/setting_deposit_data/del_coop_deposit_group',
								method: 'POST',
								data: {
									'table': 'coop_report_transaction_type_config',
									'id': id,
									'field': 'id'
								},
								success: function (msg) {
									if (msg == 1) {
										document.location.href = base_url + 'setting_deposit_data/coop_deposit_group_setting';
									} else {

									}
								}
							});
						} else {
							swal("ไม่สามารถลบประเภทนี้ได้ ");
						}
					}
				});


			} else {

			}
		});

}

