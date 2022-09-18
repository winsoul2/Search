<div class="layout-content">
    <div class="layout-content-body">
		<style>
			.bt-add{
				float:none;
			}
			.input-with-icon .form-control{
				padding-left: 40px;
			}
			input[type=file]{
				margin-left: -8px;
			}
			.input-with-icon {
				margin-bottom: 5px;
			}
			.input-with-icon .form-control{
				padding-left: 40px;
			}
			.modal_data_input{
				margin-left:-5px;
			}
			.scrollbar {
				height: 360px;
			}
			.modal-footer {
				border-top:0;
			}
			#modal-detail-table tr td {
				border:0;
				padding: 4px;
			}
		</style>
		<h1 style="margin-bottom: 0">ชำระเงินกองทุนช่วยเหลือผู้กู้</h1>
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 padding-l-r-0">
		<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6 padding-l-r-0">
		<?php $this->load->view('breadcrumb'); ?>
		</div>
		<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 " style="padding-right:0px;text-align:right;">
		</div>
		</div>
		<div class="row gutter-xs">
			<div class="col-xs-12 col-md-12">
				<div class="panel panel-body">
					<form data-toggle="validator" method="GET" action="" class="g24 form form-horizontal" enctype="multipart/form-data" autocomplete="off" id="myForm">
						<div class="m-t-1">
							<div class="g24-col-sm-20">
								<div class="form-group">
									<label class="g24-col-sm-4 control-label">รหัสสมาชิก <span id="naja"></span> </label>
									<div class="g24-col-sm-6">
										<div class="form-group">
											<div class="input-group">
												<input id="member_id" name="member_id" class="form-control member_id_input" style="text-align:left;" type="number" value="<?php echo empty($_GET) ? '': $_GET['member_id']; ?>" required title="กรุณาป้อน รหัสสมาชิก" />
												<span class="input-group-btn">
													<a data-toggle="modal" data-target="#myModal" id="modal-search" class="fancybox_share fancybox.iframe" href="#">
														<button id="" type="button" class="btn btn-info btn-search"><span class="icon icon-search"></span>
														</button>
													</a>
												</span>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="m-t-1">
							<div class="g24-col-sm-20">
								<div class="form-group">
									<label class="g24-col-sm-4 control-label">ประเภทการค้นหา</label>
									<div class="g24-col-sm-6">
										<div class="radio">
											<label><input type="radio" name="type" value="1" checked>ผู้กู้</label>
											&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp
											<label><input type="radio" name="type" value="2">ผู้รับภาระหนี้</label>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="m-t-1">
							<div class="g24-col-sm-20">
								<label class="g24-col-sm-4 control-label"></label>
								<div class="g24-col-sm-6">
									<button type="button" class="btn btn-info" id="myForm-submit-btn">ค้นหา</button>
								</div>
							</div>
						</div>
						<div class="g24-col-sm-24"></div>
						<div class="g24-col-sm-24"></div>
						<div class="g24-col-sm-24"></div>
					</form>
					<div class="bs-example" data-example-id="striped-table">
						<div id="tb_wrap">
							<table class="table table-bordered table-striped table-center">
								<thead>
									<tr class="bg-primary">
										<th>ลำดับ</th>
										<th>วันที่ชำระหนี้</th>
										<th>เลขที่สัญญา</th>
										<th>ชื่อสกุลผู้รับภาระหนี้</th>
										<th>ชื่อสกุลผู้กู้</th>
										<th>สถานะ</th>
										<th>วันที่ทำรายการ</th>
										<th>ผู้ทำรายการ</th>
										<th style="width: 130px;">เลขที่ใบเสร็จ</th>
										<th style="width: 130px;"></th>
									</tr>
								</thead>
								<tbody>
								<?php
                                    if(!empty($datas)) {
                                        foreach($datas as $data) {
                                ?>
										<tr>
											<td><?php echo $page_start++;?></td>
                                            <td><?php echo $this->center_function->ConvertToThaiDate($data['payment_date'],1,1);?></td>
                                            <td><?php echo $data["contract_number"];?></td>
                                            <td><?php echo $data["prename"].$data["firstname"]." ".$data["lastname"];?></td>
                                            <td><?php echo $data["loanee_prename"].$data["loanee_firstname"]." ".$data["loanee_lastname"];?></td>
                                            <td><?php echo !empty($data["fund_receipt_id"]) ? "ชำระแล้ว": "ค้างชำระ";?></td>
                                            <td><?php echo $data["user_name"];?></td>
                                            <td><?php echo $this->center_function->ConvertToThaiDate($data['receipt_datetime'],1,1);?></td>
                                            <td>
                                            <?php
                                                if(!empty($data["fund_receipt_id"])) {
                                            ?>
                                                <a href="<?php echo base_url(PROJECTPATH.'/admin/receipt_form_pdf/'.$data["fund_receipt_id"]); ?>" target="_blank"><?php echo $data["fund_receipt_id"];?></a>
                                            <?php
                                                }
                                            ?>
                                            </td>
                                            <td>
                                            <?php
                                                if(empty($data["fund_receipt_id"])) {
                                            ?>
                                                <button name="bt_add" id="bt_add_<?php echo $data["receipt_id"];?>" type="button" class="btn btn-primary bt_add" data-receipt-id="<?php echo $data["receipt_id"];?>">
													<span>ชำระ</span>
												</button>
                                            <?php
                                                }
                                            ?>
                                            </td>
										</tr>
                                <?php
                                        }
                                    } else {
                                ?>
                                    <tr><td colspan="10">ไม่พบข้อมูล</td></tr>
                                <?php
                                    }
                                ?>
								</tbody>
							</table>
						</div>
					</div>
					<?php echo $paging ?>
				</div>
			</div>
		</div>
    </div>
</div>

<div class="modal fade" id="viewDetail"  tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-data" style="width:600px">
        <div class="modal-content">
            <div class="modal-header modal-header-confirmSave">
                <button type="button" class="close" data-dismiss="modal"></button>
                <h2 class="modal-title">ชำระเงินช่วยเหลือ</h2>
            </div>
			<form data-toggle="validator" method="post" action="<?php echo base_url(PROJECTPATH.'/compromise/save_compromise_pay'); ?>" class="g24 form form-horizontal" enctype="multipart/form-data" autocomplete="off" id="from_view">
				<input type="hidden" name="receipt_id" id="modal_receipt_id" value=""/>
            	<div class="modal-body">
					<div class="g24-col-sm-24  m-t-1 text-center">
						<table id="modal-detail-table" class="g24-col-sm-24 table no-border"></table>
					</div>
				</div>
				<div class="modal-footer">
					<div class="g24-col-sm-24  m-t-1 text-center">
						<div class="form-group">
							<button type="button" id="from_view-submit" class="btn btn-info">บันทึก</button>
						</div>
					</div>
				</div>
			</form>
        </div>
    </div>
</div>

<div class="modal fade" id="myModal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">ข้อมูลสมาชิก</h4>
            </div>
            <div class="modal-body">
                <div class="input-with-icon">
					<div class="row">
						<div class="col">
							<label class="col-sm-2 control-label">รูปแบบค้นหา</label>
							<div class="col-sm-4">
								<div class="form-group">
									<select id="search_list" name="search_list" class="form-control m-b-1">
										<option value="">เลือกรูปแบบค้นหา</option>
										<option value="member_id">รหัสสมาชิก</option>
										<option value="employee_id">รหัสพนักงาน</option>
										<option value="id_card">หมายเลขบัตรประชาชน</option>
										<option value="firstname_th">ชื่อสมาชิก</option>
										<option value="lastname_th">นามสกุล</option>
									</select>
								</div>
							</div>
							<label class="col-sm-1 control-label" style="white-space: nowrap;"> ค้นหา </label>
							<div class="col-sm-4">
								<div class="form-group">
									<div class="input-group">
										<input id="search_text" name="search_text" class="form-control m-b-1" type="text" value="<?php echo @$data['id_card']; ?>">
										<span class="input-group-btn">
											<button type="button" id="member_search" class="btn btn-info btn-search"><span class="icon icon-search"></span></button>
										</span>
									</div>
								</div>
							</div>
						</div>
					</div>
                </div>
                <div class="bs-example" data-example-id="striped-table">
                    <table class="table table-striped">
						<tbody id="result_member">
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" id="close" class="btn btn-default" data-dismiss="modal">ปิดหน้าต่าง</button>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
		$(".bt_add").click(function(){
			receipt_id = $(this).attr("data-receipt-id");
			$("#modal_receipt_id").val(receipt_id);
			$.ajax({
				type: "GET",
				url: base_url+'compromise/get_compromise_payment_detail?receipt_id='+receipt_id,
				success: function(result) {
					datas = JSON.parse(result)
					console.log(datas)
					$("#modal-detail-table").html("");
					if(datas.length <= 0) {
						var tr = $("<tr><td>ไม่พบข้อมูล</td></tr>");
						$("#modal-detail-table").append(tr)
					} else {
						for(i = 0; i < datas.length; i++) {
							data = datas[i];
							support_tr = ``;
							first_tr_style = i > 0 ? `style="border-top: 1px solid #eee"` : ``;
							if(!data.fund_support_percent) {
								support_tr = `
												<tr>
													<td colspan="3">กองทุนช่วยเหลือคงเหลือ</td>
												</tr>
												<tr>
													<td>เงินต้น</td>
													<td>`+format_number(data.fund_support_balance)+`</td>
													<td></td>
												</tr>
												<tr>
													<td>ดอกเบี้ย</td>
													<td>`+format_number(data.fund_support_interest_balance)+`</td>
													<td></td>
												</tr>
											  `;
								principal_support = "0";
								interest_support = "0";
								interest_debt_support = "0";
							} else {
								support_tr = `
												<tr>
													<td>กองทุนช่วยเหลือ</td>
													<td>`+data.fund_support_percent+`%</td>
													<td></td>
												</tr>
											`;
								principal_support = (data.principal * data.fund_support_percent) / 100;
								interest_support = (data.interest * data.fund_support_percent) / 100;
								interest_debt_support = (data.interest_remain * data.fund_support_percent) / 100;
							}
							var tr = $(`
										<tr>
											<td `+first_tr_style+`>เลขที่สัญญา</td>
											<td `+first_tr_style+`>`+data["contract_number"]+`</td>
											<td `+first_tr_style+`></td>
										</tr>
										`+support_tr+`
										<tr>
											<td></td>
											<td>ยอดชำระ</td>
											<td>ช่วยเหลือจำนวน</td>
										</tr>
										<tr>
											<td>เงินต้น</td>
											<td>`+format_number(data.principal)+`</td>
											<td>
												<input type="hidden" name="loan_ids[]" value="`+data.loan_id+`"/>
												<input type="text" class="form-control currency-input" name="principals[]" id="pri-`+data.loan_id+`" onkeyup="format_the_number_decimal(this)" value="`+format_number(principal_support)+`"/>
											</td>
										</tr>
										<tr>
											<td>ดอกเบี้ย</td>
											<td>`+format_number(data.interest)+`</td>
											<td>
												<input type="text" class="form-control currency-input" name="interests[]" id="int-`+data.loan_id+`" onkeyup="format_the_number_decimal(this)" value="`+format_number(interest_support)+`"/>
											</td>
										</tr>
										<tr>
											<td>ดอกเบี้ย</td>
											<td>`+format_number(data.interest_remain)+`</td>
											<td>
												<input type="text" class="form-control currency-input" name="interest_debts[]" id="int_debt-`+data.loan_id+`" onkeyup="format_the_number_decimal(this)" value="`+format_number(interest_debt_support)+`"/>
											</td>
										</tr>
										<tr><td></td></tr>
										<tr><td></td></tr>
									   `);
							$("#modal-detail-table").append(tr);
						}
					}
					$('#viewDetail').modal('toggle');
				}
			});
		});

		$('#member_search').click(function(){
			if($('#search_list').val() == '') {
				swal('กรุณาเลือกรูปแบบค้นหา','','warning');
			} else if ($('#search_text').val() == ''){
				swal('กรุณากรอกข้อมูลที่ต้องการค้นหา','','warning');
			} else {
				$.ajax({
					url: base_url+"ajax/search_member_by_type",
					method:"post",
					data: {
						search_text : $('#search_text').val(), 
						search_list : $('#search_list').val()
					},  
					dataType:"text",
					success:function(data) {
						$('#result_member').html(data.replace(new RegExp("href", 'g'), "data").replace(new RegExp("btn-info", 'g'), "btn-info btn-member"));
					},
					error: function(xhr){
						console.log('Request Status: ' + xhr.status + ' Status Text: ' + xhr.statusText + ' ' + xhr.responseText);
					}
				});
			}
		});

		$(document).on("click",".btn-member",function() {
			$("#member_id").val($(this).attr("id"));
			$("#myModal").modal("hide");
		});

		$(".member_id_input").keypress(function(e) {
			if(e.keyCode === 13 ){
				event.preventDefault();
				id = $(this).val();
				type = $('input[name=type]:checked', '#myForm').val();
				submit_search(id, type);
			}
		});
		

		$("#myForm-submit-btn").click(function() {
			event.preventDefault();
			id = $("#member_id").val();
			type = $('input[name=type]:checked', '#myForm').val();
			submit_search(id, type);
		});

		$("#from_view-submit").click(function() {
			$(".currency").each(function() {
				$(this).val($(this).val().replace(/,/g,''));
			});
			$("#from_view").submit()
		})
	});

	function get_search_member_debt(){
		$.ajax({
			type: "POST",
			url: base_url+'debt/get_search_member_debt',
			data: {
				search_text : $("#search_text").val(),
				form_target : 'add'
			},
			success: function(msg) {
				$("#table_data").html(msg);
			}
		});
	}

	function submit_search(id, type) {
		$.ajax({
			type: "GET",
			url: base_url+"compromise/get_member_fund_supports_json?member_id="+id+"&type="+type,
			success: function(result) {
				datas = JSON.parse(result)
				if(datas.length > 0) {
					member_id = "";
					name = "";
					has_compromise = false
					has_support = false
					for(i=0; i < datas.length; i++) {
						data = datas[i];
						member_id = data.member_id
						name = data.prename_full+data.firstname_th+" "+data.lastname_th
						if(data.compromise_id != null) has_compromise = true;
						if(data.fund_support > 0 || data.fund_support_interest > 0) has_support = true;
					}
					$(".member_id_input").val(member_id);
					if(type == 1) {
						type_text = " ในฐานะผู้กู้";
					} else {
						type_text = " ในฐานะผู้รับภาระหนี้";
					}
					if(!has_compromise) {
						swal("สมาชิก "+member_id+" "+name+" ไม่มีข้อมูลการประนอมหนี้"+type_text);
					} else if(!has_support) {
						swal({
							title: "ไม่พบข้อมูลการใช้งานกองทุน ของ "+member_id+" "+name+type_text,
							text: "",
							type: "warning",
							showCancelButton: true,
							confirmButtonColor: '#0288d1',
							confirmButtonText: 'ดูข้อมูลประนอมหนี้',
							cancelButtonText: "ยกเลิก",
							closeOnConfirm: true,
							closeOnCancel: true
						},
						function(isConfirm) {
							if (isConfirm) {
								for(i=0; i < datas.length; i++) {
									data = datas[i];
									var win = window.open(base_url+"compromise/view_compromise?compromise_id="+data.compromise_id, '_blank');
									win.focus();
								}
							}
						});
					} else {
						$("#myForm").submit();
					}
				} else {
					swal("ไม่พบข้อมูลสมาชิกรหัส "+id);
				}
			}
		});
	}

	function format_the_number_decimal(ele){
        var value = $('#'+ele.id).val();
        value = value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');
        var num = value.split(".");
        var decimal = '';
        var num_decimal = '';
        if(typeof num[1] !== 'undefined'){
            if(num[1].length > 2){
                num_decimal = num[1].substring(0, 2);
            }else{
                num_decimal =  num[1];
            }
            decimal =  "."+num_decimal;
        }

        if(value!=''){
            if(value == 'NaN'){
                $('#'+ele.id).val(0);
            }else{
                value = (num[0] == '')?0:parseInt(num[0]);
                value = value.toLocaleString()+decimal;
                $('#'+ele.id).val(value);
            }
        }else{
            $('#'+ele.id).val(0);
        }
	}
	
	function removeCommas(str) {
        return(str.replace(/,/g,''));
    }

</script>
