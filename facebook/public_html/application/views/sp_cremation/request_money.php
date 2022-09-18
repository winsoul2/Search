<div class="layout-content">
    <div class="layout-content-body">
        <style>
            input[type=number]::-webkit-inner-spin-button,
            input[type=number]::-webkit-outer-spin-button {
                -webkit-appearance: none;
                margin: 0;
            }
            th, td {
                text-align: center;
            }
            .modal-dialog-delete {
                margin:0 auto;
                width: 350px;
                margin-top: 8%;
            }
            .modal-dialog-account {
                margin:auto;
                width: 70%;
                margin-top:7%;
            }
            .control-label {
                text-align:right;
                padding-top:5px;
            }
            .text_left {
                text-align:left;
            }
            .text_right {
                text-align:right;
            }
            .top-btn {
                margin-bottom: 1em;
            }
            .modal-footer {
                border-top: 0;
            }
        </style>
        <h1 style="margin-bottom: 0">ขอรับเงินฌาปนกิจสงเคราะห์</h1>
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 padding-l-r-0">
            <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6 padding-l-r-0">
                <?php $this->load->view('breadcrumb'); ?>
            </div>
            <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 padding-l-r-0 text-right">
                <button class="btn btn-primary btn-lg btn-add top-btn" type="button" id="add_resign">
                    เพิ่มคำขอ
                </button>
            </div>
        </div>

        <div class="row gutter-xs">
            <div class="col-xs-12 col-md-12">
                <div class="panel panel-body">
                    <div class="bs-example" data-example-id="striped-table">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th class="font-normal">วันที่ยื่นคำร้อง</th>
                                    <th class="font-normal">รหัสสมาชิก</th>
                                    <th class="font-normal">ชื่อ-นามสกุล</th>
                                    <th class="font-normal">สถานะ</th>
                                    <th class="font-normal"></th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php
                                $status_arr = array(0=>"ยื่นคำร้อง", 1=>"อนุมัติ", 2=>"ไม่อนุมัติ", 3=>"ยกเลิก");
                                foreach($datas as $request) {
                            ?>
                                <tr>
                                    <td class="text-center"><?php echo $this->center_function->ConvertToThaiDate($request["request_date"],'1','0'); ?></td>
                                    <td class="text-center">
                                        <form action="<?php echo base_url(PROJECTPATH.'/sp_cremation/'.$path); ?>" method="post" id="form_register_<?php echo $request["register_id"];?>" target="_blank">
                                            <input type="hidden" name="register_id" value="<?php echo $request["register_id"];?>"/>
                                        </form>
                                        <a id="member_btn_<?php echo $request["register_id"];?>" class="member_btn" data-id="<?php echo $request["register_id"];?>" href="#"><?php echo $request["cremation_member_no"]?></a>
                                    </td>
                                    <td class="text-left"><?php echo $request["prename_full"].$request["firstname_th"]." ".$request["lastname_th"];?></td>
                                    <td class="text-center"><?php echo $status_arr[$request["resign_status"]];?></td>
                                    <?php
                                        if(!empty($request["receipt_id"])) {
                                    ?>
                                        <td class="text-center">
                                            <a id="receipt_btn_<?php echo $request["id"];?>" class="receipt_btn" target="_blank" href="<?php echo base_url(PROJECTPATH.'/sp_cremation/'.$path."/receipt?receipt_id=".$request["receipt_id"]); ?>"><?php echo $request["receipt_no"]?></a>
                                        </td>
                                    <?php
                                        } else {
                                    ?>
                                        <td class="text-right">
                                            <input type="button" class="btn btn-default btn_edit" id="edit_btn_<?php echo $request["req_id"]?>" data-id="<?php echo $request["req_id"];?>" data-reason="<?php echo $request["reason"]?>"
                                                    data-member-id="<?php echo $request["cremation_member_no"];?>" data-name="<?php echo $request["prename_full"].$request["firstname_th"]." ".$request["lastname_th"];?>" value="แก้ไข"/>
                                            <input type="button" class="btn btn-primary btn_approve" id="approve_btn_<?php echo $request["req_id"]?>" data-id="<?php echo $request["req_id"];?>" data-reason="<?php echo $request["reason"]?>"
                                                    data-member-id="<?php echo $request["cremation_member_no"];?>" data-name="<?php echo $request["prename_full"].$request["firstname_th"]." ".$request["lastname_th"];?>" value="อนุมัติ"/>
                                            <input type="button" class="btn btn-danger btn_disapprove" id="disapprove_btn_<?php echo $request["req_id"]?>" data-id="<?php echo $request["req_id"];?>" value="ไม่อนุมัติ"/>
                                        </td>
                                    <?php
                                        }
                                    ?>
                                </tr>
                            <?php
                                }
                            ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <?php echo @$paging ?>
            </div>
        </div>
    </div>
</div>
<div id="add_madal" tabindex="-1" role="dialog" class="modal fade">
    <div class="modal-dialog modal-dialog-account">
        <div class="modal-content">
            <div class="modal-header modal-header-confirmSave">
                <h2 class="modal-title">บันทึก</h2>
            </div>
            <div class="modal-body">
                <form action="<?php echo base_url(PROJECTPATH.'/sp_cremation/'.$path.'/save_request_money'); ?>" method="post" id="form1" enctype="multipart/form-data">
                    <input id="req_id" name="req_id" type="hidden" class="type_input" value="">
                    <div class="row">
                        <label class="col-sm-4 control-label">เลขฌาปนกิจ</label>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <div class="input-group">
                                    <input id="member_cremation_id" name="member_cremation_id" class="form-control" style="text-align:left;" type="text" value=""/>
                                    <span class="input-group-btn">
                                        <a data-toggle="modal" id="member_cremation_id_modal_btn" class="fancybox_share fancybox.iframe" href="#">
                                            <button id="" type="button" class="btn btn-info btn-search"><span class="icon icon-search"></span></button>
                                        </a>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group">
                            <label class="col-sm-4 control-label">ชื่อ-นามสกุล</label>
                            <div class="col-sm-3">
                                <input id="member_name" class="form-control m-b-1 type_input" type="text" value="" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group">
                            <label class="col-sm-4 control-label">สาเหตุการเสียชีวิต</label>
                            <div class="col-sm-7">
                                <input id="reason" name="reason" class="form-control m-b-1 type_input" type="text" value="">
                            </div>
                        </div>
                    </div>
                    <div>
                        <div id="file_upload_div">
                            <input type="hidden" id="max_file_index" value="1"/>
                            <div class="form-group g24-col-sm-24" id="file_div_1">
                                <label class="g24-col-sm-8 control-label">เอกสารแนบ</label>
                                <div class="g24-col-sm-16">
                                    <label class="fileContainer btn btn-info g24-col-sm-7">
                                        <span class="icon icon-paperclip"></span> 
                                        แนบเอกสาร 1
                                        <input id="file_1" data-index="1" name="file[]" class="form-control m-b-1 file_upload" type="file" value="" >
                                    </label>
                                    <label id="filename_1" style="padding: 7px;"></label>
                                </div>
                            </div>
                        </div>
                        <div id="file_uploaded_div">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <div class="row form-group">
                    <label class="col-sm-4 control-label"></label>
                    <div class="col-sm-6 text-left">
                        <button type="button" class="btn btn-primary min-width-100" id="submit_btn">ตกลง</button>
                        <button type="button" class="btn btn-danger min-width-100" id="cancel_btn">ยกเลิก</button>
                    </div>
                </div>
			</div>
        </div>
    </div>
</div>
<div id="approve_madal" tabindex="-1" role="dialog" class="modal fade">
    <div class="modal-dialog modal-dialog-account">
        <div class="modal-content">
            <div class="modal-header modal-header-confirmSave">
                <h2 class="modal-title">บันทึก</h2>
            </div>
            <div class="modal-body">
                <form action="" method="post" id="form2">
                    <input id="approve_req_id" name="req_id" type="hidden" class="type_input" value="">
                    <div class="row">
                        <label class="col-sm-4 control-label">เลขฌาปนกิจ</label>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <input id="approve_member_cremation_id" class="form-control type_input" style="text-align:left;" type="text" value="" readonly/>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group">
                            <label class="col-sm-4 control-label">ชื่อ-นามสกุล</label>
                            <div class="col-sm-3">
                                <input id="approve_member_name" class="form-control m-b-1 type_input" type="text" value="" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group">
                            <label class="col-sm-4 control-label">สาเหตุการเสียชีวิต</label>
                            <div class="col-sm-7">
                                <input id="approve_reason" class="form-control m-b-1 type_input" type="text" value="" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group">
                            <label class="col-sm-4 control-label">เงินสงเคราะห์ที่ได้รับ</label>
                            <div class="col-sm-3">
                                <input id="total_payment" name="total_payment" class="form-control m-b-1 number_input" type="text" value="0.00" onKeyUp="format_the_number_decimal(this)">
                            </div>
                            <div class="col-sm-3">
                                <button type="button" class="btn btn-primary min-width-100" id="cal_payment_btn">คำนวน</button>
                            </div>
                        </div>
                    </div>
                    <?php
                        foreach($commissions as $commission) {
                    ?>
                    <div class="row">
                        <div class="form-group">
                            <label class="col-sm-4 control-label"><?php echo $commission["description"]?></label>
                            <div class="col-sm-3">
                                <input id="<?php echo $commission["code"]?>" name="commissions[<?php echo $commission["code"]?>]" data-percent="<?php echo $commission["value"]?>" class="form-control m-b-1 number_input com_input payment_input" type="text" value="0.00" onKeyUp="format_the_number_decimal(this)">
                            </div>
                        </div>
                    </div>   
                    <?php
                        }
                    ?>
                    <div class="row">
                        <div class="form-group">
                            <label class="col-sm-4 control-label">เงินสงเคราะห์คงเหลือจ่ายผู้รับเงินสงเคราะห์</label>
                            <div class="col-sm-3">
                                <input id="receive_payment" name="receive_payment" class="form-control m-b-1 number_input payment_input" type="text" value="0.00" onKeyUp="format_the_number_decimal(this)">
                            </div>
                        </div>
                    </div>
                    <div>
                        <div id="file_uploaded_div_approve">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <div class="row form-group">
                    <label class="col-sm-4 control-label"></label>
                    <div class="col-sm-6 text-left">
                        <button type="button" class="btn btn-primary min-width-100" id="approve_submit_btn">ตกลง</button>
                        <button type="button" class="btn btn-danger min-width-100" id="approve_cancel_btn">ยกเลิก</button>
                    </div>
                </div>
			</div>
        </div>
    </div>
</div>
<div class="modal fade" id="cremation-search-modal" role="dialog">
	<div class="modal-dialog" style="width: 80%;">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">ฌาปนกิจสงเคราะห์</h4>
			</div>
			<div class="modal-body">
				<div class="input-with-icon">
					<div class="row">
						<div class="col">
							<label class="col-sm-2 control-label">รูปแบบค้นหา</label>
							<div class="col-sm-4">
								<div class="form-group">
									<select id="cre_search_list" name="search_list" class="form-control m-b-1">
										<option value="">เลือกรูปแบบค้นหา</option>
										<option value="cremation_no">เลขที่คำร้อง</option>
										<option value="member_cremation_id">เลขฌาปนกิจสงเคราะห์</option>
										<option value="member_id">รหัสสมาชิก</option>
										<option value="id_card">หมายเลขบัตรประชาชน</option>
										<option value="firstname_th">ชื่อสมาชิก</option>
									</select>
								</div>
							</div>
							<label class="col-sm-1 control-label" style="white-space: nowrap;"> ค้นหา </label>
							<div class="col-sm-4">
								<div class="form-group">
									<div class="input-group">
										<input id="cre_search_text" name="search_text" class="form-control m-b-1" type="text" value="">
										<span class="input-group-btn">
											<button type="button" id="cremation_search" class="btn btn-info btn-search"><span class="icon icon-search"></span></button>
										</span>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="bs-example" data-example-id="striped-table">
					<table class="table table-striped">
						<thead>
							<th class="text-center">เลขที่คำร้อง</th>
							<th class="text-center">เลขฌาปนกิจสงเคราะห์</th>
							<th class="text-center">รหัสสมาชิก</th>
							<th class="text-center">ชื่อสมาชิก</th>
							<th></th>
						</thead>
						<tbody id="cre-table_data">
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
        $(".date_time").datepicker({
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

        $("#add_resign").click(function() {
            $('#add_madal').modal('toggle');
        });
        $("#cancel_btn").click(function() {
            $("#req_id").val("");
            $("#member_name").val("");
            $("#reason").val("");
            $("#member_cremation_id").val("");
            $("#file_uploaded_div").html("");
            $("#file_upload_div").html(`<input type="hidden" id="max_file_index" value="1"/>
                                            <div class="form-group g24-col-sm-24" id="file_div_1">
                                                <label class="g24-col-sm-8 control-label">เอกสารแนบ</label>
                                                <div class="g24-col-sm-16">
                                                    <label class="fileContainer btn btn-info g24-col-sm-7">
                                                        <span class="icon icon-paperclip"></span> 
                                                        แนบเอกสาร 1
                                                        <input id="file_1" data-index="1" name="file[]" class="form-control m-b-1 file_upload" type="file" value="" >
                                                    </label>
                                                    <label id="filename_1" style="padding: 7px;"></label>
                                                </div>
                                            </div>`);
            $('#add_madal').modal('hide');
        });

        //Event function for search cremation mebmer modal
        $("#member_cremation_id_modal_btn").click(function() {
			$("#modal-type").val(1);
			$('#cremation-search-modal').modal('toggle');
		});
        $("#cremation_search").click(function() {
			if($('#cre_search_list').val() == '') {
				swal('กรุณาเลือกรูปแบบค้นหา','','warning');
			} else if ($('#cre_search_text').val() == ''){
				swal('กรุณากรอกข้อมูลที่ต้องการค้นหา','','warning');
			} else {
				is_member = $("#modal-type").val() == '2' ? 1 : 0;
				$.ajax({
					url: base_url+"sp_cremation/<?php echo $path;?>/search_cremation_by_type_jquery",
					method:"post",
					data: {
						search_text : $('#cre_search_text').val(), 
						search_list : $('#cre_search_list').val(),
						is_member : is_member
					},
					dataType:"text",
					success:function(data) {
						$('#cre-table_data').html(data);
					},
					error: function(xhr){
						console.log('Request Status: ' + xhr.status + ' Status Text: ' + xhr.statusText + ' ' + xhr.responseText);
					}
				});
			}
		});
        $(document).on('click','.cre-modal-btn',function(){
            $.ajax({
                url: base_url+"sp_cremation/<?php echo $path;?>/get_cremation_member_info",
                method:"post",
                data: {
                    cremation_member_raw_id : $(this).attr("data-member-cremation-raw-id"), 
                },
                dataType:"text",
                success:function(result) {
                    data = $.parseJSON(result);
                    $("#req_id").val(data.req_id);
                    $("#member_name").val(data.firstname_th+" "+data.lastname_th);
                    $("#reason").val(data.resign_reason);
                    $("#member_cremation_id").val(data.cremation_member_id);
                    $("#cremation-search-modal").modal("hide");
                },
                error: function(xhr){
                    console.log('Request Status: ' + xhr.status + ' Status Text: ' + xhr.statusText + ' ' + xhr.responseText);
                    $("#cremation-search-modal").modal("hide");
                }
            });
        });
		$("#member_cremation_id").keypress(function() {
			var keycode = (event.keyCode ? event.keyCode : event.which);
			if(keycode == '13'){
				var member_id = $(this).val();
				$.post(base_url+"sp_cremation/<?php echo $path;?>/check_cremation_member_id", {
				    cremation_member_id: member_id
				}
				, function(result) {
					obj = JSON.parse(result);
					if(obj.status == "success"){
                        $.ajax({
                            url: base_url+"sp_cremation/<?php echo $path;?>/get_cremation_member_info",
                            method:"post",
                            data: {
                                cremation_member_raw_id : obj.id, 
                            },
                            dataType:"text",
                            success:function(result) {
                                data = $.parseJSON(result);
                                $("#req_id").val(data.req_id);
                                $("#member_name").val(data.firstname_th+" "+data.lastname_th);
                                $("#reason").val(data.resign_reason);
                                $("#member_cremation_id").val(data.cremation_member_id);
                                $("#cremation-search-modal").modal("hide");
                            },
                            error: function(xhr){
                                console.log('Request Status: ' + xhr.status + ' Status Text: ' + xhr.statusText + ' ' + xhr.responseText);
                                $("#cremation-search-modal").modal("hide");
                            }
                        });
					}else{
						swal('ไม่พบเลขฌาปนกิจที่ท่านเลือก','','warning'); 
					}
				});
			}
		});

        $(".btn_edit").click(function() {
            $("#req_id").val($(this).attr("data-id"));
            $("#member_name").val($(this).attr("data-name"));
            $("#reason").val($(this).attr("data-reason"));
            $("#member_cremation_id").val($(this).attr("data-member-id"));

            $.post(base_url+"sp_cremation/<?php echo $path;?>/get_money_request_file", {
                request_id: $(this).attr("data-id")
            }
            , function(result) {
                obj = JSON.parse(result);
                for(index = 1; index <= obj.files.length; index++) {
                    file = obj.files[index - 1];
                    filename = file.name;
                    new_upload_row_html = `<div class="form-group g24-col-sm-24" id="file_uploaded_div_`+index+`">
                                                <label class="g24-col-sm-8 control-label"></label>
                                                <div class="g24-col-sm-16">
                                                <a href="<?php echo base_url(PROJECTPATH."/sp_cremation/".$path."/download_file?id=");?>`+file.id+`">`+filename+`</a><span class="icon icon-trash text-danger remove_uploaded_file pointer" style="font-size: large; padding-left:5px;" id="remove_uploaded_`+index+`" data-id="`+index+`" ></span>
                                                                <input type="hidden" name="file_ids[]" value="`+file.id+`"/>
                                                </div>
                                            </div>`;
				    $("#file_uploaded_div").append(new_upload_row_html);
                }
            });
        
            $("#add_madal").modal("toggle");
        });

        // $(".remove_uploaded_file").click(function() {
        $(document).on("click",".remove_uploaded_file",function() {
            index = $(this).attr("data-id");
            console.log(index)
            $("#file_uploaded_div_"+index).remove();
        });

        //Submit request form
        $("#submit_btn").click(function() {
            $.blockUI({
				message: 'กรุณารอสักครู่...',
				css: {
					border: 'none',
					padding: '15px',
					backgroundColor: '#000',
					'-webkit-border-radius': '10px',
					'-moz-border-radius': '10px',
					opacity: .5,
					color: '#fff'
				},
				baseZ: 5000,
				bindEvents: false
			});
            var cremation_member_id = $("#member_cremation_id").val();
            if(cremation_member_id == '') {
                $.unblockUI();
                swal('ไม่สามารถทำรายการได้','กรุณากรอกเลขฌาปนกิจ','warning'); 
            } else {
                if($("#req_id").val() == "") {
                    $.post(base_url+"sp_cremation/<?php echo $path;?>/check_cremation_member_id", {
                        cremation_member_id: cremation_member_id
                    }
                    , function(result) {
                        obj = JSON.parse(result);
                        if(obj.status == "success"){
                            $.ajax({
                                url: base_url+"sp_cremation/<?php echo $path;?>/check_save_request_money",
                                method:"post",
                                data:$("#form1").serialize(),
                                dataType:"text",
                                success:function(result) {
                                    data = JSON.parse(result);
                                    if(data.status == "success"){
                                        $("#form1").submit()
                                    } else {
                                        $.unblockUI();
                                        swal('ไม่สามารถทำรายการได้',data.message,'warning'); 
                                    }
                                },
                                error: function(xhr){
                                    $.unblockUI();
                                    console.log('Request Status: ' + xhr.status + ' Status Text: ' + xhr.statusText + ' ' + xhr.responseText);
                                    $("#cremation-search-modal").modal("hide");
                                }
                            });
                        }else{
                            $.unblockUI();
                            swal('ไม่สามารถทำรายการได้','ไม่พบเลขฌาปนกิจที่ท่านเลือก','warning'); 
                        }
                    });
                } else {
                    $.post(base_url+"sp_cremation/<?php echo $path;?>/check_cremation_member_id", {
                        cremation_member_id: cremation_member_id
                    }
                    , function(result) {
                        obj = JSON.parse(result);
                        if(obj.status == "success"){
                            $("#form1").submit()
                        }else{
                            $.unblockUI();
                            swal('ไม่สามารถทำรายการได้','ไม่พบเลขฌาปนกิจที่ท่านเลือก','warning'); 
                        }
                    });
                }
                
            }
        });

        $(".btn_disapprove").click(function() {
            req_id = $(this).attr("data-id");
            swal({
                title: "ท่านต้องการทำรายการไม่อนุมัติคำขอใช่หรือไม่?",
                text: "",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: '#DD6B55',
                confirmButtonText: 'ยืนยัน',
                cancelButtonText: "ยกเลิก",
                closeOnConfirm: true,
                closeOnCancel: true
            },
            function(isConfirm) {
                if (isConfirm) {
                    $.blockUI({
                        message: 'กรุณารอสักครู่...',
                        css: {
                            border: 'none',
                            padding: '15px',
                            backgroundColor: '#000',
                            '-webkit-border-radius': '10px',
                            '-moz-border-radius': '10px',
                            opacity: .5,
                            color: '#fff'
                        },
                        baseZ: 5000,
                        bindEvents: false
                    });
                    $.ajax({
                        url: base_url+"sp_cremation/<?php echo $path;?>/disapprove_request_money",
                        method:"post",
                        data: {request_id : req_id},
                        dataType:"text",
                        success:function(result) {
                            url = window.location.href+"<?php echo !empty($_GET["page"]) ? "?page=".$_GET["page"] : "";?>";
                            window.location.href = url;
                        },
                        error: function(xhr){
                            $.unblockUI();
                            console.log('Request Status: ' + xhr.status + ' Status Text: ' + xhr.statusText + ' ' + xhr.responseText);
                        }
                    });
                }
            });
        });

        $(".btn_approve").click(function() {
            $("#approve_req_id").val($(this).attr("data-id"));
            $("#approve_member_name").val($(this).attr("data-name"));
            $("#approve_reason").val($(this).attr("data-reason"));
            $("#approve_member_cremation_id").val($(this).attr("data-member-id"));
            $.post(base_url+"sp_cremation/<?php echo $path;?>/get_money_request_file", {
                request_id: $(this).attr("data-id")
            }
            , function(result) {
                obj = JSON.parse(result);
                for(index = 1; index <= obj.files.length; index++) {
                    file = obj.files[index - 1];
                    filename = file.name;
                    new_upload_row_html = `<div class="form-group g24-col-sm-24" id="file_uploaded_div_`+index+`">
                                                <label class="g24-col-sm-8 control-label"></label>
                                                <div class="g24-col-sm-16">
                                                    <a href="<?php echo base_url(PROJECTPATH."/sp_cremation/".$path."/download_file?id=");?>`+file.id+`">`+filename+`</a>
                                                </div>
                                            </div>`;
				    $("#file_uploaded_div_approve").append(new_upload_row_html);
                }
            });

            $("#approve_madal").modal("toggle");
        });

        $("#approve_cancel_btn").click(function() {
            $("#approve_req_id").val("");
            $("#approve_member_name").val("");
            $("#approve_reason").val("");
            $("#approve_member_cremation_id").val("");
            $(".number_input").val('0.00');
            $("#file_uploaded_div_approve").html("");
            $("#approve_madal").modal("toggle");
        });

        $("#approve_submit_btn").click(function() {
            warning_message = "";
            total_com = 0;
            $(".com_input").each(function(index) {
                total_com += to_num($(this).val());
            });
            if(to_num($("#total_payment").val()) != (total_com + to_num($("#receive_payment").val()))) {
                warning_message = " - จำนวนเงินสงเคราะห์ที่ได้รับไม่ตรงกับค่าใช้จ่ายรวมกับเงินสงเคราะห์คงเหลือ";
            }

            swal({
                title: "ท่านต้องการทำรายการอนุมัติคำขอใช่หรือไม่?",
                text: warning_message,
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: '#DD6B55',
                confirmButtonText: 'ยืนยัน',
                cancelButtonText: "ยกเลิก",
                closeOnConfirm: true,
                closeOnCancel: true
            },
            function(isConfirm) {
                if (isConfirm) {
                    $.blockUI({
                        message: 'กรุณารอสักครู่...',
                        css: {
                            border: 'none',
                            padding: '15px',
                            backgroundColor: '#000',
                            '-webkit-border-radius': '10px',
                            '-moz-border-radius': '10px',
                            opacity: .5,
                            color: '#fff'
                        },
                        baseZ: 5000,
                        bindEvents: false
                    });
                    $.ajax({
                        url: base_url+"sp_cremation/<?php echo $path;?>/request_money_approve",
                        method:"post",
                        data: $("#form2").serialize(),
                        dataType:"text",
                        success:function(result) {
                            console.log(result)
                            data = JSON.parse(result);
                            if(data.status == "success"){
                                var win = window.open(base_url+"sp_cremation/<?php echo $path;?>/receipt?receipt_id="+data.receipt_id, '_blank');
                                win.focus();
                                location.reload();
                            } else {
                                $.unblockUI();
                                swal('ไม่สามารถทำรายการได้',data.message,'warning'); 
                            }
                        },
                        error: function(xhr){
                            $.unblockUI();
                            console.log('Request Status: ' + xhr.status + ' Status Text: ' + xhr.statusText + ' ' + xhr.responseText);
                        }
                    });
                }
            });
        });

        $(".member_btn").click(function() {
            id = $(this).attr("data-id");
            $("#form_register_"+id).submit();
        });

        $(document).on("change",".file_upload",function() {
			if($(this).attr("data-index") == $("#max_file_index").val()) {
				index = $(this).attr("data-index");
				next_index = parseInt(index)+1;
				$("#max_file_index").val(next_index);
				filename = $(this).val().replace(/.*(\/|\\)/, '');
				$("#filename_"+index).html(filename+`<span class="icon icon-trash text-danger remove_file pointer" style="font-size: large; padding-left:5px;" id="remove_`+index+`" data-id="`+index+`" ></span>`);

				new_upload_row_html = `<div class="form-group g24-col-sm-24" id="file_div_`+next_index+`">
											<label class="g24-col-sm-8 control-label"></label>
											<div class="g24-col-sm-16">
												<label class="fileContainer btn btn-info g24-col-sm-7">
													<span class="icon icon-paperclip"></span> 
													แนบเอกสาร `+next_index+`
													<input id="file_`+next_index+`" data-index="`+next_index+`" name="file[]" class="form-control m-b-1 file_upload" type="file" value="" >
												</label>
												<label id="filename_`+next_index+`" style="padding: 7px;"></label>
											</div>
										</div>`;
				$("#file_upload_div").append(new_upload_row_html);
			} else {
				index = $(this).attr("data-index");
				filename = $(this).val().replace(/.*(\/|\\)/, '');
				$("#filename_"+index).html(filename+`<span class="icon icon-trash text-danger remove_file pointer" style="font-size: large; padding-left:5px;" id="remove_`+index+`" data-id="`+index+`" ></span>`);
			}
		});

        $(document).on("click", ".remove_file",function() {
			index = $(this).attr("data-id");
			$("#file_div_"+index).remove();
		});

        $("#cal_payment_btn").click(function() {
            total = to_num($("#total_payment").val());
            total_com = 0;
            $(".com_input").each(function(index) {
                var percent = to_num($(this).attr("data-percent"));
                var val = total * percent / 100;
                total_com += val;
                $(this).val(val);
                format_the_number_decimal(document.getElementById($(this).attr("id")))
            });

            $("#receive_payment").val(total - total_com);
            format_the_number_decimal(document.getElementById("receive_payment"));
        });
    });

    function to_num(str) {
        return parseFloat(str.replace(/,/g,''));
    }
    function removeCommas(str) {
        return(str.replace(/,/g,''));
    }
    function format_the_number_decimal(ele){
        var startPosition = ele.selectionStart;
        var value = $('#'+ele.id).val();
        b_count = value.length;
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
                $('#'+ele.id).val('');
            }else{
                value = (num[0] == '') ? 0 : parseInt(num[0]);
                value = value.toLocaleString()+decimal;
                $('#'+ele.id).val(value);
            }
        }else{
            $('#'+ele.id).val('');
        }
        a_count = value.length;
        diff_lenght = 0;
        if(b_count >= a_count) {
            diff_lenght = b_count - a_count
        } else {
            diff_lenght = (b_count - a_count) * (-1)
        }
        $('#'+ele.id).setCursorPosition(startPosition + diff_lenght);
    }
    $.fn.setCursorPosition = function(pos) {
        this.each(function(index, elem) {
            if (elem.setSelectionRange) {
                elem.setSelectionRange(pos, pos);
            } else if (elem.createTextRange) {
                var range = elem.createTextRange();
                range.collapse(true);
                range.moveEnd('character', pos);
                range.moveStart('character', pos);
                range.select();
            }
        });
        return this;
    };
</script>