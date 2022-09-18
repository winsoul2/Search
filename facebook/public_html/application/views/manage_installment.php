<div class="layout-content">
	<div class="layout-content-body">
		<style>
			.form-group { margin-bottom: 0; }
    .border1 { border: solid 1px #ccc; padding: 0 15px; }
    .mem_pic { float: right; width: 150px; }
    .mem_pic img { width: 100%; border: solid 1px #ccc; }
    .mem_pic button { display: block; width: 100%; }

    .hide_error{color : inherit;border-color : inherit;}

    .has-error{color : #d50000;border-color : #d50000;}

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
    .modal-backdrop.in{
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
</style>
		<h1 style="margin-bottom: 0">การเปลี่ยนแปลงงวดชำระ</h1>
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 padding-l-r-0">
			<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 padding-l-r-0">
				<?php $this->load->view('breadcrumb'); ?>
			</div>

			<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 padding-l-r-0">
				<a class="btn btn-primary btn-lg bt-add" href="#" onclick="goto()">
					พิมพ์รายงานการเปลี่ยนแปลงงวดชำระ
				</a>
			</div>

		</div>
		<div class="row gutter-xs">
			<div class="col-xs-12 col-md-12">
				<div class="panel panel-body">


					<form action="<?=base_url('loan/manage_installment')?>" method="post" name="myForm" id="myForm" onsubmit="event.preventDefault(); validateMyForm();">
						<div class="g24-col-sm-24" style="margin-bottom: 15px;">
							<div class="form-group g24-col-sm-8">
								<label class="g24-col-sm-10 control-label" for="form-control-2">เลขที่สัญญา</label>
								<div class="g24-col-sm-14">
									<div class="input-group">
										<input id="contract_number" class="form-control" type="text" value="ฉฉ.6100437" required onkeypress="return runScript(event)"
										 autocomplete="off">
										<span class="input-group-btn">
											<a data-toggle="modal" data-target="#search_loan_modal" id="test" class="fancybox_share fancybox.iframe"
											 href="#">
												<button id="" type="button" class="btn btn-info btn-search"><span class="icon icon-search"></span></button>
											</a>
										</span>
									</div>
								</div>
							</div>
						</div>

						<div class="g24-col-sm-24" style="margin-bottom: 15px;">
							<div class="form-group g24-col-sm-8">
								<label class="g24-col-sm-10 control-label " for="form-control-2">รหัสสมาชิก</label>
								<div class="g24-col-sm-14">
									<input class="form-control " type="text" value="" readonly="" id="member_id">
								</div>
							</div>

							<div class="form-group g24-col-sm-16">
								<label class="g24-col-sm-4 control-label " for="form-control-2">ชื่อ - สกุล</label>
								<div class="g24-col-sm-17">
									<input class="form-control " type="text" value="" readonly="" id="name">
								</div>
							</div>
						</div>

						<div class="g24-col-sm-24" style="margin-bottom: 15px;">
							<div class="form-group g24-col-sm-8">
								<label class="g24-col-sm-10 control-label" for="form-control-2">เลขที่สัญญา</label>
								<div class="g24-col-sm-14">
									<input class="form-control " type="text" value="" readonly="" id="contract_number_text">
								</div>
							</div>

							<div class="form-group g24-col-sm-16">
								<label class="g24-col-sm-4 control-label " for="form-control-2">วันที่จ่ายเงินกู้</label>
								<div class="g24-col-sm-6">
									<input class="form-control " type="text" value="" readonly="" id="loan_date">
								</div>

								<label class="g24-col-sm-5 control-label " for="form-control-2">ประเภทสัญญา</label>
								<div class="g24-col-sm-6">
									<input class="form-control " type="text" value="" readonly="" id="loan_type">
								</div>
							</div>
						</div>

						<div class="g24-col-sm-24" style="margin-bottom: 15px;">
							<div class="form-group g24-col-sm-8">
								<label class="g24-col-sm-10 control-label" for="form-control-2">วงเงินอนุมัติ</label>
								<div class="g24-col-sm-14">
									<input class="form-control " type="text" value="" readonly="" id="loan_amount">
								</div>
							</div>

							<div class="form-group g24-col-sm-16">
								<label class="g24-col-sm-4 control-label " for="form-control-2">จำนวนคงเหลือ</label>
								<div class="g24-col-sm-6">
									<input class="form-control " type="text" value="" readonly="" id="loan_amount_balance">
								</div>
							</div>
						</div>

						<div class="g24-col-sm-24" style="margin: 0px 15px 15px 0px;">
							<div class="form-group g24-col-sm-16">
								<div class="form-group g24-col-sm-5">
									<h3 class="control-label" style="font-weight: 800;margin-top: 20px;margin-bottom: 0px;padding-top: 0px;">รูปแบบการชำระเดิม</h3>

								</div>
								<div class="form-group g24-col-sm-18">
									<label class="g24-col-sm-12 control-label" style='text-align: left; margin-top: 20px;  padding-top: 4px;'>อัปเดทวันที่
										<span id="latest"></span></label>
									<label class="g24-col-sm-12 control-label" style='text-align: left; margin-top: 20px;  padding-top: 4px;'>โดย
										<span id="updater"></span></label>
								</div>
							</div>
						</div>

						<div class="g24-col-sm-24" style="margin-bottom: 30px;">
							<div class="form-group g24-col-sm-8">
								<label class="g24-col-sm-10 control-label" for="form-control-2">รูปแบบการชำระ</label>
								<div class="g24-col-sm-14">
									<select name="old_installment_type" id="old_installment_type" class="form-control" disabled="">
										<option value="1">ต้นเท่ากัน</option>
										<option value="2">ยอดเท่ากัน</option>
									</select>
								</div>
							</div>

							<div class="form-group g24-col-sm-16">
								<label class="g24-col-sm-4 control-label " for="form-control-2">ผ่อนชำระต่อเดือน</label>
								<div class="g24-col-sm-6">
									<input class="form-control " type="text" value="" readonly="" id="old_period_per_month">
								</div>

								<label class="g24-col-sm-5 control-label " for="form-control-2">จำนวนงวด</label>
								<div class="g24-col-sm-6">
									<input class="form-control " type="text" value="" readonly="" id="old_period_amount">
								</div>
							</div>
						</div>



						<div class="g24-col-sm-24" style="margin: 0px 15px 15px 0px;">
							<div class="form-group g24-col-sm-16">
								<h3 class="g24-col-sm-5 control-label" style="font-weight: 800;margin-top: 0px;margin-bottom: 0px;padding-top: 0px;">รูปแบบการชำระใหม่</h3>
								<div class="g24-col-sm-18 control-label text-right" style="padding-right: 0px;">
								</div>
							</div>

						</div>

						<div class="g24-col-sm-24" style="margin-bottom: 30px;">
							<div class="form-group g24-col-sm-8">
								<label class="g24-col-sm-10 control-label" for="form-control-2">รูปแบบการชำระ</label>
								<div class="g24-col-sm-14">
									<select name="new_installment_type" id="new_installment_type" class="form-control" name="new_installment_type">
										<option value="1">ต้นเท่ากัน</option>
										<option value="2">ยอดเท่ากัน</option>
									</select>
								</div>
							</div>

							<div class="form-group g24-col-sm-16">
								<label class="g24-col-sm-4 control-label " for="form-control-2">ผ่อนชำระต่อเดือน</label>
								<div class="g24-col-sm-6">
									<input class="form-control " type="text" value="" id="new_period" name="new_period_per_month">
								</div>

								<label class="g24-col-sm-5 control-label " for="form-control-2">จำนวนงวด</label>
								<div class="g24-col-sm-6">
									<input class="form-control " type="text" value="" id="new_payment" name="new_period_amount">
								</div>
							</div>
						</div>





						<div class="row" style="margin-top: 15px;margin-bottom: 15px;">
							<div class="col-md-offset-4 col-md-4 text-center">
								<input type="hidden" id="loan_id" name="loan_id">
								<button class="btn btn-primary" id="submit_button" type="submit">บันทึก</button>
							</div>
						</div>



					</form>




				</div>

			</div>
		</div>
	</div>
</div>






<div class="modal fade" id="search_loan_modal" role="dialog">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">ข้อมูลสมาชิก</h4>
			</div>
			<div class="modal-body">
				<div class="">
					<div class="row">
						<div class="col">
							<label class="col-sm-2 control-label">รูปแบบค้นหา</label>
							<div class="col-sm-4">
								<div class="form-group">
									<select id="member_search_list" name="member_search_list" class="form-control m-b-1">
										<option value="">เลือกรูปแบบค้นหา</option>
										<option value="contract_number">เลขสัญญา</option>
										<option value="member_id">รหัสสมาชิก</option>
										<option value="id_card">หมายเลขบัตรประชาชน</option>
										<option value="firstname_th">ชื่อสมาชิก</option>
										<option value="lastname_th">นามสกุล</option>
									</select>
								</div>
							</div>
							<label class="col-sm-1 control-label" style="white-space: nowrap;"> ค้นหา </label>
							<div class="col-sm-5">
								<div class="form-group">
									<div class="input-group">
										<input id="member_search_text" name="member_search_text" class="form-control m-b-1" type="text" value="<?php echo @$data['id_card']; ?>"
										 autocomplete="off">
										<span class="input-group-btn">
											<button type="button" id="member_loan_search" class="btn btn-info btn-search"><span class="icon icon-search"></span></button>
										</span>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>

				<div class="bs-example" data-example-id="striped-table">
					<table class="table table-striped">
						<tbody id="result_member_search">
						</tbody>
					</table>
				</div>
			</div>
			<div class="modal-footer">
				<input type="hidden" id="input_id">
				<button type="button" id="close" class="btn btn-default" data-dismiss="modal">ปิดหน้าต่าง</button>
			</div>
		</div>
	</div>
</div>

<script>
	var base_url = $('#base_url').attr('class');
	$("#search_account").change(function () {
		var max_loan = numeral($("#loan_full_request").val()).value();
		var amount = numeral($("#loan_request").val()).value();
		if (amount > max_loan) {
			swal('ไม่สามารถขอรับเงินเกิน ' + numeral(max_loan).format('0,0.00') + ' บาท', '', 'warning');
			$("#loan_request").val(numeral(max_loan).format('0,0.00'));
		} else {
			$("#loan_request").val(numeral(amount).format('0,0.00'));
		}
	});


	$('#member_loan_search').click(function () {
		if ($('#member_search_list').val() == '') {
			swal('กรุณาเลือกรูปแบบค้นหา', '', 'warning');
		} else if ($('#member_search_text').val() == '') {
			swal('กรุณากรอกข้อมูลที่ต้องการค้นหา', '', 'warning');
		} else {
			console.log($('#member_search_text').val());
			console.log($('#member_search_list').val());
			$.ajax({
				url: base_url + "ajax/search_loan_by_type",
				method: "post",
				data: {
					search_text: $('#member_search_text').val(),
					search_list: $('#member_search_list').val()
				},
				dataType: "text",
				success: function (data) {
					if (data == "FALSE") {
						$('#result_member_search').html("<div class='text-center'><h3>ไม่พบข้อมูลในระบบ</h3></div>");
					} else {
						var row = JSON.parse(data);
						console.log(row);
						var table = "<table width='100%'><thead><th>เลขที่สัญญา</th><th>ชื่อสกุล</th><th>เลือก</th></thead><tbody>";
						row.forEach(element => {
							table += "<tr>";
							table += "<td>" + element.contract_number + "</td>";
							table += "<td>" + element.prename_short + element.firstname_th + " " + element.lastname_th + "</td>";
							table += "<td><button class='btn btn-success' onclick=\"init('" + element.contract_number +
								"')\">เลือก</button></td>";
							table += "</tr>";
						});
						table += "</tbody></table>";
						$('#result_member_search').html(table);
					}


				},
				error: function (xhr) {
					console.log('Request Status: ' + xhr.status + ' Status Text: ' + xhr.statusText + ' ' + xhr.responseText);
				}
			});
		}

	});

	function validateMyForm() {

		// var input_list= $('inputinput[type=text]').map(function(){
		//     if ( $(this).prop('readonly')==false ) { 
		//         return $(this).val()
		//     }
		// }).get();
		var input_list = {};
		$.each($('input[type=text]').serializeArray(), function (i, field) {
			if (field.name == "member_search_text") {
				return true;
			}

			if (field.value == '') {
				swal('ตรวจสอบข้อมูลให้ถูกต้อง', '', 'warning');
				return false;
			}
			console.log(i, field);
			// values[field.name] = field.value;
		});

		document.getElementById("myForm").submit();
		return true;
	}

	function runScript(e) {
		//See notes about 'which' and 'key'
		if (e.keyCode == 13) {
			var search = $("#contract_number").val();
			if (search != "") {
				$.ajax({
					url: base_url + "ajax/search_loan_by_type",
					method: "post",
					data: {
						search_text: search,
						search_list: 'contract_number'
					},
					dataType: "text",
					success: function (data) {
						// $('#result_member_search').html(data); 
						$("#current_guarantor").empty();
						$("#new_guarantor").empty();
						$("#manage_guarantor").empty();

						$("#member_id").val("");
						$("#name").val("");
						$("#contract_number_text").val("");
						$("#loan_date").val("");
						$("#loan_type").val("");
						$("#loan_amount").val("");
						$("#loan_amount_balance").val("");
						$("#loan_id").val("");

						$("#latest").text("");
						$("#updater").text("");

						if (data == "FALSE") {
							swal('ไม่พบข้อมูลนี้', '', 'warning');
						} else {
							var row = JSON.parse(data);
							setData(row[0])
						}

					},
					error: function (xhr) {
						console.log('Request Status: ' + xhr.status + ' Status Text: ' + xhr.statusText + ' ' + xhr.responseText);
					}
				});

				return false;
			}

		}
	}

	function setData(obj) {
		console.log(obj);
		$("#member_id").val(obj['member_id']);
		$("#name").val(obj['prename_short'] + obj['firstname_th'] + " " + obj['lastname_th']);
		$("#contract_number_text").val(obj['contract_number']);
		$("#loan_date").val(obj['date_start_period']);
		$("#loan_type").val(obj['type_name']);
		$("#loan_amount").val(obj['loan_amount']);
		$("#loan_amount_balance").val(obj['loan_amount_balance']);
		$("#loan_id").val(obj['loan_id']);

		if (obj['loan_id'] != "") {
			get_current_installment(obj['loan_id']);
		}


		// if(obj['guarantor'].length != 0){
		//     $.each( obj['guarantor'], function( key, value ) {
		//         var table = "<tr id='guarantor_"+key+"'>";
		//         table += "<td>"+value.guarantee_person_id+"</td>";
		//         table += "<td>"+value.fullname+"</td>";
		//         table += "<td>"+(value.guarantee_person_amount==null ? "0" : value.guarantee_person_amount)+"</td>";
		//         table += "</tr>";
		//         jQuery('#current_guarantor').append(table);
		//     });

		//     $.each( obj['guarantor'], function( key, value ) {
		//         var table = "<tr class='new_guarantor_"+key+"'>";
		//         var id_new_guarantor = "<input name='id_new_guarantor[]' type='text' id='id_new_guarantor_"+key+"' class='form-control' value='"+value.guarantee_person_id+"' onkeypress=\"return getMember(event, this, '"+key+"')\" readonly>";
		//         var amount_new_guarantor = "<input name='amount_new_guarantor[]' type='text' id='amount_new_guarantor_"+key+"' class='form-control is_numeral' value='"+(value.guarantee_person_amount==null ? "0" : value.guarantee_person_amount)+"'  onkeyup='format_number_1(event, this)' onchange='format_number_2(event, this)' readonly>";
		//         table += "<td>"+id_new_guarantor+"</td>";
		//         table += "<td><input name='name_guarantor[]' type='text' class='form-control' readonly id='fullname_new_guarantor_"+key+"' value='"+value.fullname+"'></td>";
		//         table += "<td>"+amount_new_guarantor+"</td>";
		//         table += "</tr>";

		//         var tableManage = "<tr  style='background-color: white;height: 51px;' class='new_guarantor_"+key+"'>";
		//         tableManage += "<td style='vertical-align: bottom;'><span class='control-label' onclick=\"edit_guarantor('"+key+"')\">แก้ไข</span></td>";
		//         tableManage += "<td style='vertical-align: bottom;'><span class='control-label' onclick=\"delete_guarantor('new_guarantor_"+key+"')\">ลบ</span></td>";
		//         tableManage += "</tr>";
		//         jQuery('#new_guarantor').append(table);
		//         jQuery('#manage_guarantor').append(tableManage);

		//     });
		// }
		// $("#member_id").val(obj['member_id']);
	}

    function get_current_installment(loan_id){
        $.ajax({
				url: base_url + "ajax/get_current_loan_installment",
				method: "post",
				data: {
					loan_id: loan_id
				},
				dataType: "text",
				success: function (data) {



					if (data == "FALSE") {
						swal('ไม่พบข้อมูลนี้', '', 'warning');
					} else {
                        var loan = JSON.parse(data);
                        console.log("get_current_installment", loan);

                        $('#old_installment_type option[value='+loan['pay_type']+']').attr('selected','selected');
                        $("#old_period_amount").val(loan['period_amount']);
                        $("#old_period_per_month").val(loan['money_per_period']);

                        if(loan['create_date']==""){
                            $("#latest").val(loan['create_date']);
                        }
                        if(loan['user_name']==""){
                            $("#updater").val(loan['user_name']);
                        }
					}

				},
				error: function (xhr) {
					console.log('Request Status: ' + xhr.status + ' Status Text: ' + xhr.statusText + ' ' + xhr.responseText);
				}
			});
    }

	function add_guarantor() {
		var new_id = $.now();
		var table = "<tr class='new_guarantor_" + new_id + "'>";
		table += "<td><input name='id_new_guarantor[]' type='text' class='form-control' id='id_new_guarantor_" + new_id +
			"' onkeypress=\"return getMember(event, this, '" + new_id + "')\"></td>";
		table += "<td><input name='name_guarantor[]' type='text' class='form-control' readonly id='fullname_new_guarantor_" +
			new_id + "' value=''></td>";
		table +=
			"<td><input name='amount_new_guarantor[]' type='text' class='form-control is_numeral' onkeyup='format_number_1(event, this)' onchange='format_number_2(event, this)'></td>";
		table += "</tr>";
		$("#new_guarantor").append(table);

		var tableManage = "<tr style='background-color: white;height: 51px;' class='new_guarantor_" + new_id + "'>";
		tableManage +=
			"<td colspan=2 style='vertical-align: bottom;'><span class='control-label' onclick=\"delete_guarantor('new_guarantor_" +
			new_id + "')\">ลบ</span></td>";
		tableManage += "</tr>";
		jQuery('#manage_guarantor').append(tableManage);

		// if($(window).scrollTop() + $(window).height() != $(document).height()) {
		//     $("html, body").animate({ scrollTop: $(document).height()-500 }, "slow");
		// }
		// $("html, body").animate({
		//     scrollTop: $('html, body')[0].scrollHeight - $('html, body')[0].clientHeight
		// }, 1000);
	}

	function edit_guarantor(id) {
		$("#id_new_guarantor_" + id).prop('readonly', false);
		$("#amount_new_guarantor_" + id).prop('readonly', false);
	}

	function delete_guarantor(id) {
		swal({
				title: "คุณต้องการที่จะลบ",
				text: "",
				type: "warning",
				showCancelButton: true,
				confirmButtonColor: '#DD6B55',
				confirmButtonText: 'ลบ',
				cancelButtonText: "ยกเลิก",
				closeOnConfirm: true,
				closeOnCancel: true
			},
			function (isConfirm) {
				if (isConfirm) {
					jQuery("." + id).remove();
				} else {

				}
			});

	}

	function getMember(e, value, id) {
		if (e.keyCode == 13) {
			var search = $(value).val();
			if (search != "") {
				$.ajax({
					url: base_url + "ajax/search_member_json",
					method: "post",
					data: {
						search: search
					},
					dataType: "text",
					success: function (data) {
						// $('#result_member_search').html(data); 
						$("#amount_new_guarantor_" + id).val("");
						$("#fullname_new_guarantor_" + id).text("");

						if (data == "FALSE") {
							swal('ไม่พบข้อมูลนี้', '', 'warning');
						} else {
							var row = JSON.parse(data);
							$("#amount_new_guarantor_" + id).val('0');
							$("#fullname_new_guarantor_" + id).val(row.fullname);
						}

					},
					error: function (xhr) {
						console.log('Request Status: ' + xhr.status + ' Status Text: ' + xhr.statusText + ' ' + xhr.responseText);
					}
				});

				return false;
			}

		}
	}

	function choose_transfer_type(type_id) {
		if (type_id == 1) {
			$("#type_2").hide();
			$("#type_3").hide();
		} else if (type_id == 2) {
			$("#type_2").show();
			$("#type_3").hide();
		} else {
			$("#type_2").hide();
			$("#type_3").show();
		}
	}

	function init(search) {
		$("#contract_number").val(search);
		$.ajax({
			url: base_url + "ajax/search_loan_by_type",
			method: "post",
			data: {
				search_text: search,
				search_list: 'contract_number'
			},
			dataType: "text",
			success: function (data) {
				// $('#result_member_search').html(data); 
				$("#current_guarantor").empty();
				$("#new_guarantor").empty();
				$("#manage_guarantor").empty();

				$("#member_id").val("");
				$("#name").val("");
				$("#contract_number_text").val("");
				$("#loan_date").val("");
				$("#loan_type").val("");
				$("#loan_amount").val("");
				$("#loan_amount_balance").val("");
				$("#loan_id").val();
				if (data == "FALSE") {
					swal('ไม่พบข้อมูลนี้', '', 'warning');
				} else {
					var row = JSON.parse(data);
					setData(row[0])
				}
			},
			error: function (xhr) {
				console.log('Request Status: ' + xhr.status + ' Status Text: ' + xhr.statusText + ' ' + xhr.responseText);
			}
		});
		$('#search_loan_modal').modal('hide');
		return false;
	}

	$("form").keypress(function (e) {
		//Enter key
		if (e.which == 13) {
			return false;
		}
	});

	jQuery("#type_2").hide();
	jQuery("#type_3").hide();



	$(document).ready(function () {
		console.log("ready!");
		// var search = $("#contract_number").val();
		var search = '<?=$cid?>';
		console.log("s: ", search);
		if (search != "") {
			init(search);
			swal("บันทึก!", "ทำรายการสำเร็จแล้ว", "success");
		}
	});

	function format_number_1(evt, obj) {
		// alert(0);
		var value = $(obj).val();
		console.log("format_number_1", value);
		var dotcontains = value.indexOf(".") != -1;
		if (dotcontains) {

			return;
		}
		var number_format = numeral(value).format('0,0');
		$(obj).val(number_format);
	}

	function format_number_2(evt, obj) {
		var value = $(obj).val();
		var number_format = numeral(value).format('0,0.00');
		$(obj).val(number_format);
	}

	function goto() {
		var loan_id = $("#loan_id").val()
		window.open('<?=base_url('loan/manage_guarantor_print/')?>' + loan_id, '_blank');
	}



	<?php
        if($save_status==1){
            ?>
	$(document).ready(function () {
		init('<?=$contract_number?>');
		swal("บันทึก!", "ทำรายการสำเร็จแล้ว", "success");
	});
	<?php
            CI_Input::set_cookie("save_status", '0', 10);
        }
    ?>

</script>


<script src="//cdnjs.cloudflare.com/ajax/libs/numeral.js/2.0.6/numeral.min.js"></script>
