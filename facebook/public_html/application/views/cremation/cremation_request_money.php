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
            .modal-body{
                padding: 10px;
            }
            .modal-dialog {
                width: 700px;
            }
            .pointer {
				cursor: pointer;
			}
		</style> 
		<h1 style="margin-bottom: 0">ขอรับเงินฌาปนกิจสงเคราะห์</h1>
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 padding-l-r-0">
			<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6 padding-l-r-0">
			<?php $this->load->view('breadcrumb'); ?>
			</div>
			<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 " style="padding-right:0px;text-align:right;">
                <button name="btn_approve" id="btn_add_request" type="button" class="btn btn-primary btn-lg btn-position-top" >
                    <span>เพิ่มการขอรับเงิน</span>
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
							<th>ชื่อสกุล</th>
							<th>ผู้ข้อรับเงิน</th>
							<th>ยอดเงิน</th>
							<th>สถานะ</th>
							<th>ผู้ทำรายการ</th>
							<th style="width:150px;"></th> 
						</tr> 
					 </thead>
					 <tbody id="table_first">
                        <?php 
                            $cremation_status = array('0'=>'รอการอนุมัติ', '1'=>'อนุมัติ', '3'=>'ไม่อนุมัติ');
                            
                            foreach($data as $key => $row ){
                            ?>
                                <tr>
                                    <td><?php echo $this->center_function->ConvertToThaiDate(@$row['createdatetime']); ?></td>
                                    <td><?php echo @$row['member_cremation_id']; ?></td>
                                    <td class="text-left"><?php echo $row["prename_full"].$row['assoc_firstname']." ".$row['assoc_lastname']; ?></td>
                                    <td class="text-left"><?php echo $row[$row["receiver"]]; ?></td>
                                    <td class="text-right"><?php echo number_format($row["cremation_balance_amount"],2)?></td>
                                    <td><?php echo @$cremation_status[$row['cremation_receive_status']]; ?></td>
                                    <td class="text-left"><?php echo $row['user_name']; ?></td> 
                                    <td>
                                        <a class="pointer edit-btn" data-member-cremation-id="<?php echo $row['member_cremation_id'];?>" data-cremation-receive-id="<?php echo $row['cremation_receive_id'];?>" id="edit_<?php echo @$row['cremation_receive_id']; ?>" title="แก้ไข">
                                            แก้ไข
                                        </a>
                                        <a class="pointer text-danger del-btn" data-member-cremation-id="<?php echo $row['member_cremation_id'];?>" data-cremation-receive-id="<?php echo $row['cremation_receive_id'];?>" id="delete_<?php echo @$row['cremation_receive_id']; ?>" title="ลบ">
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
                <input id="cremation_receive_id" name="cremation_receive_id" type="hidden" value=""/>       
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">ขอรับเงินฌาปนกิจสงเคราะห์</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group  g24-col-sm-24">
                        <label class="g24-col-sm-6 control-label">เลขฌาปนกิจสงเคราะห์</label>
                        <div class="g24-col-sm-6">
                            <div class="form-group">
                                <div class="input-group">
                                    <input id="member_cremation_id_input" class="form-control" style="text-align:left;" type="text" value=""/>
                                    <input id="member_cremation_id" name="member_cremation_id" type="hidden" value=""/>
                                    <input id="cremation_request_id" name="cremation_request_id" type="hidden" value=""/>
                                    <span class="input-group-btn">
                                        <a data-toggle="modal" id="member_cremation_id_modal_btn" class="fancybox_share fancybox.iframe" href="#">
                                            <button id="" type="button" class="btn btn-info btn-search"><span class="icon icon-search"></span></button>
                                        </a>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group  g24-col-sm-24">
                        <label class="g24-col-sm-6 control-label">ชื่อสกุล</label>
                        <div class="g24-col-sm-18">
                            <input id="name" class="form-control" style="text-align:left;" type="text" value="" readonly/>
                        </div>
                    </div>
                    <div class="form-group  g24-col-sm-24">
                        <label class="g24-col-sm-6 control-label">สาเหตุการเสียชีวิต</label>
                        <div class="g24-col-sm-18">
                            <input id="reason" name="reason" class="form-control" style="text-align:left;" type="text" value=""/>
                        </div>
                    </div>
                    <div class="form-group g24-col-sm-24">
                        <label class="g24-col-sm-6 control-label">วันที่เสียชีวิต</label>
                        <div class="g24-col-sm-6">
                            <input id="death_date" name="death_date" class="form-control m-b-1 mydate cre-input" data-mask="00/00/0000" style="padding-left: 40px;" type="text" value="" data-date-language="th-th" maxlength="10">
                        </div>
                    </div>
                    <div class="form-group g24-col-sm-24">
                        <label class="g24-col-sm-6 control-label">พินัยกรรม</label>
                        <div class="g24-col-sm-6 testament-div">
                            <div class="form-group">
                                <label class="fileContainer btn btn-info">
                                    <span class="icon icon-paperclip"></span> 
                                    เลือกไฟล์
                                    <input id="testament" name="testament" class="form-control m-b-1" type="file" value="" style="height: auto;">
                                </label>
                            </div>
                        </div>
                        <div class="g24-col-sm-6 testament-label">
                        </div>
                    </div>
                    <div class="form-group g24-col-sm-24">
                        <label class="g24-col-sm-6 control-label">หลักฐานการเสียชีวิต</label>
                        <div class="g24-col-sm-6 evidence-btn">
                            <div class="form-group">
                                <label class="fileContainer btn btn-info">
                                    <span class="icon icon-paperclip"></span> 
                                    เลือกไฟล์
                                    <input id="evidence" name="evidence" class="form-control m-b-1" type="file" value="" style="height: auto;">
                                </label>
                            </div>
                        </div>
                        <div class="g24-col-sm-6 evidence-label">
                        </div>
                    </div>
                    <div class="form-group g24-col-sm-24">
                        <label class="g24-col-sm-6 control-label">ผู้รับเงินฌาปนกิจ</label>
                        <div class="g24-col-sm-18">
                            <div class="form-group">
                                <select id="receiver" name="receiver" class="form-control m-b-1">
                                    <option value="">เลือกผู้รับเงินฌาปนกิจ</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <?php
                        $cremation_receive_amount = $setting["money_received_per_member"] * $count_members;
                        $action_fee_percent = ($cremation_receive_amount * $setting["action_fee_percent"])/100;
                        $cremation_balance_left = $cremation_receive_amount - $action_fee_percent;
                    ?>
                    <div class="form-group g24-col-sm-24">
                        <label class="g24-col-sm-6 control-label"><span id="formula_span"><?php echo number_format($setting["money_received_per_member"],2);?>*<?php echo $count_members;?></span></label>
                        <input type="hidden" id="money_received_per_member" name="money_received_per_member" value="<?php echo $setting["money_received_per_member"];?>"/>
                        <input type="hidden" id="member_amount" name="member_amount" value="<?php echo $count_members?>"/>
                        <div class="g24-col-sm-6">
                            <div class="form-group">
                                <input id="cremation_receive_amount" name="cremation_receive_amount" class="form-control text-right" style="text-align:left;" data-default="<?php echo number_format($cremation_receive_amount,2);?>" type="text" value="<?php echo number_format($cremation_receive_amount,2);?>" readonly/>
                            </div>
                        </div>
                        <label class="g24-col-sm-6 control-label">ค่าดำเนินการ <?php echo $setting["action_fee_percent"];?> %</label>
                        <div class="g24-col-sm-6">
                            <div class="form-group">
                                <input id="action_fee_percent" name="action_fee_percent" class="form-control text-right" style="text-align:left;" data-default="<?php echo number_format($action_fee_percent,2);?>" type="text" value="<?php echo number_format($action_fee_percent,2);?>" readonly/>
                            </div>
                        </div>
                    </div>
                    <div class="form-group g24-col-sm-24">
                        <label class="g24-col-sm-6 control-label">คงเหลือ</label>
                        <div class="g24-col-sm-6">
                            <input id="cremation_balance_left" name="cremation_balance_left" class="form-control text-right" style="text-align:left;" data-default="<?php echo number_format($cremation_balance_left,2);?>" type="text" value="<?php echo number_format($cremation_balance_left,2);?>" readonly/>
                        </div>
                    </div>
                    <div class="form-group g24-col-sm-24">
                        <label class="g24-col-sm-6 control-label">เงินสงเคราะห์ล่วงหน้า</label>
                        <div class="g24-col-sm-6">
                                <input id="adv_payment_balance" name="adv_payment_balance" class="form-control text-right" style="text-align:left;" type="text" value="" readonly/>
                        </div>
                    </div>
                    <div class="form-group g24-col-sm-24">
                        <label class="g24-col-sm-6 control-label">รวมเงินที่จะได้รับ</label>
                        <div class="g24-col-sm-6">
                            <input id="cremation_balance_amount" name="cremation_balance_amount" class="form-control text-right" style="text-align:left;" type="text" value="" readonly/>
                        </div>
                    </div>
                    <div class="form-group g24-col-sm-24">
                        <br>
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
				<button type="button" id="cremation-search-close" class="btn btn-default" data-dismiss="modal">ปิดหน้าต่าง</button>
			</div>
		</div>
	</div>
</div>
<script>
$(document).ready(function() {
    $(".mydate").datepicker({
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
    $("#btn_add_request").click(function(){
        $("#cremation_receive_amount").val($("#cremation_receive_amount").attr("data-default"));
        $("#action_fee_percent").val($("#action_fee_percent").attr("data-default"));
        $("#cremation_balance_left").val($("#cremation_balance_left").attr("data-default"));
        $("#adv_payment_balance").val('');
        $("#cremation_balance_amount").val('');
        $("#member_cremation_id_input").val('');
        $("#member_cremation_id").val('');
        $("#cremation_request_id").val('');
        $("#name").val('');
        $("#reason").val('');
        $("#death_date").val('');
        $('#receiver').html(`<option value="">เลือกผู้รับเงินฌาปนกิจ</option>`);
        $("#cremation_receive_id").val("");
        $("#evidence").val("");
        $("#testament").val("");
        $(".evidence-btn").show();
        $(".evidence-label").hide();
        $(".testament-div").show();
        $(".testament-label").hide();
        $("#add-request-modal").modal('toggle');
    });
    $("#member_cremation_id_modal_btn").click(function() {
        $('#cremation-search-modal').modal('toggle');
    });
    $(document).on('click','.cre-modal-btn',function(){
        $("#member_cremation_id_input").val($(this).attr("data-member-cremation-id"));
        $("#cremation_request_id").val($(this).attr("data-member-cremation-id"));
        $("#name").val($(this).attr("data-cremation-member-name"));
        $('#cremation-search-modal').modal('hide');
        get_cremation_info($(this).attr("data-member-cremation-id"), false, null);
    });
    $("#cremation-search-close").click(function() {
        $('#cremation-search-modal').modal('hide');
    });
    $("#cremation_search").click(function() {
        if($('#cre_search_list').val() == '') {
            swal('กรุณาเลือกรูปแบบค้นหา','','warning');
        } else if ($('#cre_search_text').val() == ''){
            swal('กรุณากรอกข้อมูลที่ต้องการค้นหา','','warning');
        } else {
            $.ajax({
                url: base_url+"cremation/search_cremation_by_type_jquery",
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
    $("#cre_req_money_submit").click(function() {
        if ($("#member_cremation_id").val() == '') {
            swal('ไม่สามารถบันทึกคำร้องขอได้'," - กรุณาเลือกเลขฌาปนกิจสงเคราะห์",'warning');
        } else if(!$("#cremation_receive_id").val()) {
            $.post(base_url+"cremation/check_request_receive", {id: $("#member_cremation_id_input").val()}, function(result){
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
        get_cremation_info($(this).attr("data-member-cremation-id"), true, $(this).attr("data-cremation-receive-id"));
    });
    $(".del-btn").click(function(){
        $.post(base_url+"cremation/delete_cremation_request_receive", {id: $(this).attr("data-cremation-receive-id")}, function(result){
            window.location.href = window.location.href;
        });
    });
    $(document).on("click","#remove_file_evidence",function(){
        $(".evidence-btn").show();
        $(".evidence-label").hide();
    });
    $(document).on("click","#remove_file_testament",function(){
        $(".testament-div").show();
        $(".testament-label").hide();
    });
});
function get_cremation_info(id, is_get_req_receive, req_receive_id) {
    $.get(base_url+"cremation/get_cremation_info?member_cremation_id="+id, 
    function(result) {
        data = JSON.parse(result);
        if (data.member_cremation_id) {
            if(data.cremation_status == '6' || data.cremation_status == '7') {
                $("#member_cremation_id_input").val(data.member_cremation_id);
                $("#member_cremation_id").val(data.member_cremation_id);
                $("#cremation_request_id").val(data.cremation_request_id);
                $("#name").val(data.prename_full+data.assoc_firstname+" "+ data.assoc_lastname);

                $('#receiver').html(`<option value="">เลือกผู้รับเงินฌาปนกิจ</option>`);
                if(data.receiver_1) {
                    $('#receiver').append($("<option></option>").attr("value",1).text(data.receiver_1));
                }
                if(data.receiver_2) {
                    $('#receiver').append($("<option></option>").attr("value",2).text(data.receiver_2));
                }
                if(data.receiver_3) {
                    $('#receiver').append($("<option></option>").attr("value",3).text(data.receiver_3));
                }
                $("#adv_payment_balance").val(format_number(data.adv_payment_balance));
                var cremation_balance_amount = parseFloat(data.adv_payment_balance)+parseFloat(removeCommas($("#cremation_balance_left").val()))
                $("#cremation_balance_amount").val(format_number(cremation_balance_amount));

                if(data.death_date) {
                    date_split = data.death_date.split("-");
                    year = parseInt(date_split[0]) + 543;
                    $("#death_date").val(date_split[2]+"/"+date_split[1]+"/"+year);
                }
                if(is_get_req_receive) get_cremation_request_receive(req_receive_id);
            } else {
                $("#member_cremation_id_input").val("");
                swal('สมาชิกท่านนี้ไม่อยู่ในสถานะที่ทำรายการได้','','warning'); 
            }
        } else {
            swal('ไม่พบเลขฌาปนกิจสงเคราะห์ท่านเลือก','','warning'); 
        }
    });
}
function get_cremation_request_receive(id) {
    $.get(base_url+"cremation/get_cremation_request_receive?id="+id, 
        function(result) {
            data = JSON.parse(result);
            $("#cremation_receive_amount").val(format_number(data.cremation_receive_amount));
            $("#action_fee_percent").val(format_number(data.action_fee_percent));
            $("#cremation_balance_left").val(format_number(parseFloat(data.cremation_receive_amount) - parseFloat(data.action_fee_percent)));
            $("#adv_payment_balance").val(format_number(data.adv_payment_balance));
            $("#cremation_balance_amount").val(format_number(data.cremation_balance_amount));
            $("#cremation_receive_id").val(data.cremation_receive_id);
            $("#reason").val(data.reason);
            $(".evidence").val('');
            $(".testament").val('');
            member_amount = data.member_amount != null ? data.member_amount : $("#member_amount").val();
            money_received_per_member = data.money_received_per_member != null ? data.money_received_per_member : $("#money_received_per_member").val();
            $("#formula_span").html(format_number(money_received_per_member) + "*" + member_amount);
            if(data.receiver) {
                $("#receiver").val(data.receiver.substr(data.receiver.length - 1));
            }
            if(data.evidence) {
                $(".evidence-label").show();
                $(".evidence-btn").hide();
                $(".evidence-label").html(`<label id="filename" style="padding: 7px;">
                                            <a class="comment-file-a-18" href="`+base_url+`cremation/download_file?id=`+id+`&type=evidence">
                                                <span>
                                                    `+data.evidence+`
                                                </span>
                                            </a>
                                            <span class="icon icon-trash text-danger remove_file pointer" style="font-size: large; padding-left:5px;" id="remove_file_evidence">
                                            </span>
                                        </label>`);
            } else {
                $(".evidence-btn").show();
                $(".evidence-label").hide();
            }
            if(data.testament) {
                $(".testament-label").show();
                $(".testament-div").hide();
                $(".testament-label").html(`<label id="filename" style="padding: 7px;">
                                            <a class="comment-file-a-18" href="`+base_url+`cremation/download_file?id=`+id+`&type=testament">
                                                <span>
                                                    `+data.testament+`
                                                </span>
                                            </a>
                                            <span class="icon icon-trash text-danger remove_file pointer" style="font-size: large; padding-left:5px;" id="remove_file_testament">
                                            </span>
                                        </label>`);
            } else {
                $(".testament-div").show();
                $(".testament-label").hide();
            }
            $("#add-request-modal").modal('toggle');
        });
}
function removeCommas(str) {
    return(str.replace(/,/g,''));
}
</script>