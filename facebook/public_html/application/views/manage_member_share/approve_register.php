<div class="layout-content">
	<div class="layout-content-body">
		<style>
			.form-group {
				margin-bottom: 0;
			}

			.border1 {
				border: solid 1px #ccc;
				padding: 0 15px;
			}

			.mem_pic {
				float: right;
				width: 150px;
			}

			.mem_pic img {
				width: 100%;
				border: solid 1px #ccc;
			}

			.mem_pic button {
				display: block;
				width: 100%;
			}

			.hide_error {
				color: inherit;
				border-color: inherit;
			}

			.has-error {
				color: #d50000;
				border-color: #d50000;
			}

			input::-webkit-outer-spin-button,
			input::-webkit-inner-spin-button {
				-webkit-appearance: none;
				margin: 0;
			}

			.alert-danger {
				background-color: #F2DEDE;
				border-color: #e0b1b8;
				color: #B94A48;
			}

			.modal-backdrop.in {
				opacity: 0;
			}

			.modal-backdrop {
				position: relative;
				top: 0;
				right: 0;
				bottom: 0;
				left: 0;
				z-index: 1040;
				background-color: #000;
			}

			.modal.fade {
				z-index: 10000000 !important;
			}

			th {
				text-align: center;
			}

			.modal-body .row {
				margin-top: 5px;
				margin-bottom: 5px;
			}
		</style>
		<h1 style="margin-bottom: 0">อนุมัติสมัครสมาชิก</h1>
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 padding-l-r-0">
			<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 padding-l-r-0">
				<?php $this->load->view('breadcrumb'); ?>
			</div>
			<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 padding-l-r-0">
				<button type="button" class="btn btn-danger btn-lg bt-del" style="margin-left:5px;"
						onclick="submit_form('4')">
					ไม่อนุมัติ
				</button>
				<button type="button" class="btn btn-primary btn-lg bt-add" style="margin-left:5px;"
						onclick="submit_form('1')">
					อนุมัติ
				</button>
				<a class=""
				   href="<?php echo base_url(PROJECTPATH . '/report_member_data/coop_report_wait_for_approval_member_excel?approve_page=1') ?>">
					<button type="button" class="btn btn-primary btn-lg bt-add" style="margin-left:5px; float: right;">
						Export Excel
					</button>
				</a>
			</div>
		</div>
		<div class="row gutter-xs">
			<div class="col-xs-12 col-md-12">
				<div class="panel panel-body">
					<div class="bs-example" data-example-id="striped-table">
						<div id="tb_wrap">
							<h3></h3>
							<h3></h3>
							<h3></h3>
							<form action="" method="GET" id="form_search" autocomplete="off">
								<div class="g24-col-sm-24">
									<div class="form-group g24-col-sm-24">
										<label class="g24-col-sm-1 control-label" for="faction">ค้นหา</label>
										<div class="g24-col-sm-8">
											<div class="form-group">
												<input class="form-control" type="text"
													   placeholder="ป้อนชื่อสกุล หรือ เลขที่คำร้อง" name="search_member"
													   id="search_member"
													   value="<?php echo !empty($_GET['search_member']) ? $_GET['search_member'] : "" ?>">
											</div>
										</div>
										<div class="g24-col-sm-11">
											<div class="form-group">
												<button name="search_btn" id="search_btn" type="submit"
														class="btn btn-primary" style="width: 90px;" onclick="">
													<span>แสดง</span>
												</button>
											</div>
										</div>
									</div>
								</div>
							</form>
							<div class="g24-col-sm-24">&nbsp</div>
							<form id="form1"
								  action="<?php echo base_url(PROJECTPATH . '/manage_member_share/approve_register_save') ?>"
								  method="post">
								<input type="hidden" name="status_to" id="status_to">
								<table class="table table-striped">
									<thead>
									<tr>
										<th><input type="checkbox" id="check_all" onclick="check_it_all()"></th>
										<th>ลำดับ</th>
										<th>เลขที่คำร้อง</th>
										<th>ชื่อสกุล</th>
										<th>วันที่สมัคร</th>
										<th>สถานะ</th>
										<th>เครื่องมือ</th>
									</tr>
									</thead>
									<tbody id="table_data">
									<?php $member_status = array('1' => 'ปกติ', '2' => 'ลาออก', '3' => 'รออนุมัติ', '4' => 'ไม่อนุมัติ') ?>
									<?php foreach ($row as $key => $value) { ?>
										<tr data-request-id="<?php echo $value['id'];?>">
											<td align="center"><input class="check_box" type="checkbox"
																	  name="checkbox[]"
																	  value="<?php echo $value['id']; ?>"></td>
											<td align="center"><?php echo $i++; ?></td>
											<td align="center">
												<a href="<?php echo base_url(PROJECTPATH . '/manage_member_share/add/' . $value["id"]); ?>">
													<?php echo $value['mem_apply_id']; ?>
												</a>
											</td>
											<td><?php echo $value['prename_short'] . $value['firstname_th'] . " " . $value['lastname_th']; ?></td>
											<td align="center"><?php echo $this->center_function->mydate2date($value['apply_date']); ?></td>
											<td align="center"><?php echo @$member_status[$value['member_status']]; ?></td>
											<td align="center">
												<?php if ($value['member_status'] == "3") { ?>
													<button type="button" class="btn btn-primary"
															onclick="dialog_approve_fixed('<?php echo $value["id"]; ?>')">
														อนุมัติ
													</button>
												<?php } ?>
											</td>
										</tr>
									<?php } ?>
									</tbody>
								</table>
							</form>
						</div>
					</div>
				</div>
				<div id="page_wrap">
					<?php echo $paging ?>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="modal fade" id="approve_fixed_date" role="dialog" data-keyboard="false" data-backdrop="static">
	<div class="modal-dialog">
		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">อนุมัติสมัครสมาชิก</h4>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="g24-col-sm-24">
						<div class="form-group">
							<label for="request-name" class="control-label g24-col-sm-8">ชื่อ - สกุล</label>
							<div class="g24-col-sm-11">
									<input type="text" class="form-control" readonly="readonly" id="request-name" value="">
							</div>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="g24-col-sm-24">
						<div class="form-group">
							<label for="request-apply-date" class="control-label g24-col-sm-8">วันที่สมัคร</label>
							<div class="g24-col-sm-11">
								<input type="text" class="form-control" readonly="readonly" id="request-apply-date" value="">
							</div>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="g24-col-sm-24">
						<div class="form-group">
							<label for="fixed_date" class="control-label g24-col-sm-8">วันที่อนุมัติสมัครสมาชิก</label>
							<div class="input-with-icon g24-col-sm-11">
								<div class="form-group">
									<input type="hidden" id="request_id" value="">
									<input id="fixed" name="fixed" class="form-control m-b-1"
										   style="padding-left: 50px;" type="text"
										   value="<?php echo $this->center_function->mydate2date(date('Y-m-d')); ?>"
										   data-date-language="th-th" required title="">
									<span class="icon icon-calendar input-icon m-f-1"></span>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer text-center">
				<button type="button" class="btn btn-default" data-dismiss="modal">ปิดหน้าต่าง</button>
				<button type="button" class="btn btn-primary" onclick="approve_set_date()">อนุมัติ</button>
			</div>
		</div>
	</div>
</div>
<script>

	$(document).ready(function(){

		$("#fixed").datepicker({
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

	var base_url = $('#base_url').attr('class');

	function get_search_member() {
		$.ajax({
			type: "POST",
			url: base_url + 'manage_member_share/get_search_member',
			data: {
				search_text: $("#search_text").val(),
				form_target: 'index'
			},
			success: function (msg) {
				$("#table_data").html(msg);
			}
		});
	}

	function check_it_all() {
		if ($('#check_all').is(':checked')) {
			$('.check_box').prop('checked', true);
		} else {
			$('.check_box').prop('checked', false);
		}
	}

	function submit_form(status_to) {
		$('#status_to').val(status_to);
		$('#form1').submit();
	}

	function dialog_approve_fixed(id) {
		$.post(base_url+"manage_member_share/get_request_data_member", {'id': id}, function(res){
			if(res.statusCode === 200) {
				$("#request_id").val(id);
				$("#request-name").val(res.data.name);
				$("#request-apply-date").val(res.data.apply_date);
				$("#approve_fixed_date").modal("show");
			}else{
				$("#request_id").val("");
				$("#request-name").val("");
				$("#request-apply-date").val("");
				swal("ไม่พบข้อมูลผู้สมัครสมาชิก", "", "info")
			}
		})
	}

	function approve_set_date() {
		$("#approve_fixed_date").modal("hide");
		var id = $("#request_id").val();
		var fixed_date = $("#fixed").val();
		swal({
			title: "",
			text: "ท่านต้องการอนุมัติสมาชิกท่านนี้ใช่หรือไม่",
			type: "warning",
			showCancelButton: true,
			confirmButtonColor: '#DD6B55',
			confirmButtonText: 'ยืนยัน',
			cancelButtonText: "ยกเลิก",
			closeOnConfirm: true,
			closeOnCancel: true
		}, function (isConfirm) {
			if (isConfirm) {
				$.post(base_url + "manage_member_share/request_data_member", {
					'id': id,
					'fixed_date': fixed_date,
					'status_to' : '1'
				}, function (res) {
					if (res.statusCode === 404) {
						console.log(res);
						swal("ไม่สำเร็จ", "อนุมัติสมาชิกไม่สำเร็จ", "error");
					} else if (res.statusCode === 400) {
						console.log(res);
						swal("ไม่สำเร็จ", "อนุมัติสมาชิกไม่สำเร็จ", "error");
					} else {
						swal("สำเร็จ", "อนุมัติสมาชิกแล้ว", "success");
						setTimeout(function(){
							window.location.reload();
						}, 1800);
					}
				});
			}
		});


	}
</script>
