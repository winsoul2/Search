<div class="layout-content">
    <div class="layout-content-body">
		<style>
			.center {
				text-align: center;
			}
			.modal-dialog-account {
				margin:auto;
				margin-top:7%;
			}
            .form-group{
                margin-bottom: 5px;
            }
            .btn-position-top{
                margin-top:  -1em;
            }
            .modal-footer {
                border-top:0;
            }
		</style>
		<h1 style="margin-bottom: 0">ลาออก</h1>
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 padding-l-r-0">
			<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6 padding-l-r-0">
			<?php $this->load->view('breadcrumb'); ?>
			</div>
			<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 " style="padding-right:0px;text-align:right;">
                <button id="add-btn" type="button" class="btn btn-primary btn-lg btn-position-top">
                    <span class="icon icon-plus-circle"></span>
                    <span>เพิ่มรายการ</span>
                </button>
            </div>
		</div>
		<div class="row gutter-xs">
			<div class="col-xs-12 col-md-12">
				<div class="panel panel-body">
					<h3 ></h3>
                    <table class="table table-bordered table-striped table-center">
                        <thead>
                            <tr class="bg-primary">
                                <th style="width:150px;">วันที่ทำรายการ</th>
                                <th>เลขฌาปนกิจ</th>
                                <th>ชื่อสมาชิก</th>
                                <th>ยอดเงิน</th>
                                <th>สถานะ</th>
                                <th>ผู้ทำรายการ</th>
                                <th style="width:150px;"></th> 
                            </tr>
                        </thead>
                        <tbody id="table_first">
                        <?php
                            $cremation_status = array('1'=>'อนุมัติ', '6'=>'จ่ายเงินแล้ว', '10'=>'ขอลาออก');
                            foreach($data as $key => $row ){
                            ?>
                            <tr>
                                <td><?php echo $this->center_function->ConvertToThaiDate($row['created_at']); ?></td>
                                <td><?php echo $row['member_cremation_id']; ?></td>
                                <td class="text-left"><?php echo $row["prename_full"].$row['assoc_firstname']." ".$row['assoc_lastname']; ?></td>
                                <td class="text-right"><?php echo number_format($row["adv_payment_balance"],2); ?></td>
                                <td><span id="cremation_status_<?php echo $row['cremation_request_id']; ?>" ><?php echo $cremation_status[$row['cremation_status']]; ?></span></td>
                                <td class="text-left"><?php echo $row['user_name']; ?></td>
                                <td>
                                    <a class="btn-radius btn-info edit-btn" data-id="<?php echo $row['resign_request_id'];?>" id="edit_<?php echo $row['resign_request_id']; ?>" title="แก้ไข">
                                        แก้ไข
                                    </a>
                                    <a class="btn-radius btn-danger del-btn" data-id="<?php echo $row['resign_request_id'];?>" id="delete_<?php echo $row['resign_request_id']; ?>" title="ลบ">
                                        ลบ
                                    </a>
                                </td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
		</div>
		<?php echo @$paging ?>
	</div>
</div>
<div class="modal fade" id="add-request-modal" role="dialog">
	<div class="modal-dialog  modal-dialog-info" >
		<div class="modal-content">
            <form action="" id="form1" method="POST" enctype="multipart/form-data">
                <input id="cremation_resign_id" name="cremation_resign_id" type="hidden" value=""/>
                <input id="member_cremation_id" name="member_cremation_id" type="hidden" value=""/>
                <input id="cremation_request_id" name="cremation_request_id" type="hidden" value=""/>
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">ลาออก</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group g24-col-sm-24">
                        <label class="g24-col-sm-7 control-label">เลขฌาปนกิจสงเคราะห์</label>
                        <div class="g24-col-sm-17">
                            <div class="form-group">
                                <div class="input-group">
                                    <input id="member_cremation_id_input" class="form-control" style="text-align:left;" type="text" value=""/>
                                    <span class="input-group-btn">
                                        <a data-toggle="modal" id="member_cremation_id_modal_btn" class="fancybox_share fancybox.iframe" href="#">
                                            <button id="" type="button" class="btn btn-info btn-search"><span class="icon icon-search"></span></button>
                                        </a>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="g24-col-sm-24 form-group">
                        <label class="g24-col-sm-7 control-label">ชื่อสกุล</label>
                        <div class="g24-col-sm-17">
                            <input id="name" class="form-control" style="text-align:left;" type="text" value="" readonly/>
                        </div>
                    </div>
                    <div class="g24-col-sm-24 form-group">
                        <label class="g24-col-sm-7 control-label">สาเหตุการลาออก</label>
                        <div class="g24-col-sm-17">
                            <input id="reason" name="reason" class="form-control" style="text-align:left;" type="text" value=""/>
                        </div>
                    </div>
                    <div class="form-group g24-col-sm-24">
                        <label class="g24-col-sm-7 control-label">แนบเอกสาร</label>
                        <div class="g24-col-sm-6 req-file">
                            <div class="form-group">
                                <label class="fileContainer btn btn-info ">
                                    <span class="icon icon-paperclip"></span> 
                                    เลือกไฟล์
                                    <input id="file" name="file" class="form-control m-b-1" type="file" value="" style="height: auto;">
                                </label>
                            </div>
                        </div>
                        <div class="g24-col-sm-17 label-file">
                        </div>
                    </div>
                    <div class="g24-col-sm-24 form-group">
                        <label class="g24-col-sm-7 control-label">เงินสงเคราะห์ล่วงหน้า</label>
                        <div class="g24-col-sm-8">
                            <input id="adv_payment_balance" name="adv_payment_balance" class="form-control" style="text-align:left;" type="text" value="" readonly/>
                        </div>
                    </div>
                </div>
                <div class="modal-footer text-center">
                    <button type="button" id="cre_req_money_submit" class="btn btn-info" >บันทึก</button>
                </div>
            </form>
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
    $(document).ready(function(){
        $("#add-btn").click(function(){
            $("#adv_payment_balance").val('');
            $("#name").val('');
            $("#member_cremation_id_input").val('');
            $("#member_cremation_id").val('');
            $("#cremation_request_id").val('');
            $("#cremation_resign_id").val('');
            $("#reason").val('');
            $(".req-file").val('');
            $(".label-file").html("");
            $("#add-request-modal").modal('toggle');
        });
        $("#member_cremation_id_modal_btn").click(function() {
            $('#cremation-search-modal').modal('toggle');
        });
        $("#cremation_search").click(function() {
            if($('#cre_search_list').val() == '') {
                swal('กรุณาเลือกรูปแบบค้นหา','','warning');
            } else if ($('#cre_search_text').val() == ''){
                swal('กรุณากรอกข้อมูลที่ต้องการค้นหา','','warning');
            } else {
                $.ajax({
                    url: base_url+"cremation/search_cremation_by_type_jquery",
                    data:{ 
                        status: '1,6'
                    },
                    method:"post",
                    data: {
                        search_text : $('#cre_search_text').val(), 
                        search_list : $('#cre_search_list').val()
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
        $("#member_cremation_id_input").keypress(function() {
            var keycode = (event.keyCode ? event.keyCode : event.which);
            if(keycode == '13'){
                get_cremation_info($('#member_cremation_id_input').val(), false, null);
            }
        });
        $(document).on('click','.cre-modal-btn',function(){
            $('#cremation-search-modal').modal('hide');
            get_cremation_info($(this).attr("data-member-cremation-id"), false, null);
        });
        $("#cre_req_money_submit").click(function(){
            if(!$("#cremation_resign_id").val()) {
                $.post(base_url+"cremation/check_request_resign", {id: $("#member_cremation_id_input").val()}, function(result){
                    if(result == "success") {
                        $("#form1").submit();
                    } else {
                        swal('ไม่สามารถบันทึกคำร้องขอสมาชิกท่านนี้ได้',result,'warning'); 
                    }
                });
            } else {
                $("#form1").submit();
            }
        });
        $(".edit-btn").click(function(){
            id = $(this).attr("data-id");
            $.get(base_url+"cremation/get_cremation_request_resign?id="+id, 
            function(result) {
                data = JSON.parse(result);
                $("#adv_payment_balance").val(format_number(data.adv_payment_balance));
                $("#name").val(data.prename_full+data.assoc_firstname+" "+data.assoc_lastname);
                $("#member_cremation_id_input").val(data.member_cremation_id);
                $("#member_cremation_id").val(data.member_cremation_id);
                $("#cremation_request_id").val(data.cremation_request_id);
                $("#cremation_resign_id").val(data.id);
                $("#reason").val(data.reason);
                if(data.file_name) {
                    $(".label-file").show();
                    $(".req-file").hide();
                    $(".req-file").val('');
                    $(".label-file").html(`<label id="filename" style="padding: 7px;">`+data.file_name+`
                                                <span class="icon icon-trash text-danger remove_file pointer" style="font-size: large; padding-left:5px;" id="remove_file">
                                                </span>
                                            </label>`);
                } else {
                    $(".req-file").show();
                    $(".label-file").hide();
                }
                $("#add-request-modal").modal('toggle');
            });
        });
        $(document).on("click","#remove_file",function(){
            $(".label-file").html("");
            $(".label-file").hide();
            $(".req-file").show();
        });
        $(".del-btn").click(function(){
            id = $(this).attr("data-id");
            swal({
                title: 'ลบคำร้องขอลาออก',
                text: "",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: '#0288d1',
                confirmButtonText: 'ยืนยัน',
                cancelButtonText: "ยกเลิก",
                closeOnConfirm: false,
                closeOnCancel: true
            },
            function(isConfirm) {
                if (isConfirm) {
                    $.post(base_url+"cremation/delete_cremation_request_resign", {id: id}, function(result){
                        window.location.href = window.location.href;
                    });
                }
            });
        });
    });

    function get_cremation_info(id, is_get_req, req_id) {
        $.get(base_url+"cremation/get_cremation_info?member_cremation_id="+id, 
        function(result) {
            data = JSON.parse(result);
            if (data.id_card) {
                $("#adv_payment_balance").val(format_number(data.adv_payment_balance));
                $("#name").val(data.prename_full+data.assoc_firstname+" "+data.assoc_lastname);
                $("#member_cremation_id_input").val(data.member_cremation_id);
                $("#member_cremation_id").val(data.member_cremation_id);
                $("#cremation_request_id").val(data.cremation_request_id);
            } else {
                swal('ไม่พบเลขฌาปนกิจสงเคราะห์ท่านเลือก','','warning'); 
            }
        });
    }
</script>