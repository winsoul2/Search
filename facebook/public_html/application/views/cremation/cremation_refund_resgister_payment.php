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
		</style> 
		<h1 style="margin-bottom: 0">คืนเงินค่าสมัคร</h1>
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 padding-l-r-0">
			<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6 padding-l-r-0">
			<?php $this->load->view('breadcrumb'); ?>
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
							<th>เลขที่คำร้อง</th>
							<th>ชื่อสมาชิก</th>
							<th>ประเภทสมาชิก</th>
							<th>ผู้ทำรายการ</th>
							<th style="width:300px;"></th> 
						</tr> 
					 </thead>
					 <tbody id="table_first">
					    <?php 
						foreach($data as $key => $row ){
						?>
                            <tr>
                                <td><?php echo $this->center_function->ConvertToThaiDate($row['createdatetime']); ?></td>
                                <td><?php echo $row['cremation_no']; ?></td>
                                <td class="text-left"><?php echo $row['assoc_firstname']." ".$row['assoc_lastname']; ?></td>
                                <td class="text-center"><?php echo $row['mem_type_id'] == '1' ? 'สามัญ' : 'สมทบ'; ?></td>
                                <td class="text-left"><?php echo $row['user_name']; ?></td> 
                                <td>
                                    <?php
                                        if (empty($row["refund_datetime"])) {
                                    ?>
                                        <a class="btn-radius btn-info btn-refund" id="refund_<?php echo $row['cremation_request_id']; ?>" title="คืนเงิน" data-req-id="<?php echo @$row['cremation_request_id']; ?>"
                                            data-name="<?php echo $row['assoc_firstname']." ".$row['assoc_lastname']; ?>" data-pay-amount="<?php echo $row['cremation_pay_amount'];?>"
                                            data-member_cremation_raw_id="<?php echo $row["member_cremation_raw_id"]?>">
                                            คืนเงิน
                                        </a>
                                    <?php
                                        } else {
                                            echo "คืนเงินแล้ว เมื่อ ".$this->center_function->ConvertToThaiDate($row['refund_datetime']);
                                        }
                                    ?>
                                </td>
                            </tr>
                        <?php } ?>
					  </tbody>
					  </table>
					</div>
			  </div>
		</div>
		<?php echo $paging ?>
	</div>
</div>
<div class="modal fade" id="show_transfer"  tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-account" style="width:80%">
        <div class="modal-content">
            <div class="modal-header modal-header-confirmSave">
                <button type="button" class="close" data-dismiss="modal"></button>
                <h2 class="modal-title">อนุมัติขอรับเงินฌาปนกิจสงเคราะห์</h2>
            </div>
            <div class="modal-body">
				<form data-toggle="validator" method="post" action="<?php echo base_url(PROJECTPATH.'/cremation/cremation_refund_resgister_payment'); ?>" class="g24 form form-horizontal" enctype="multipart/form-data" autocomplete="off" id="from_transfer">
					<input type="hidden" name="cremation_request_id" id="cremation_request_id" class="cremation_request_id" value=""/>
					<div class="g24-col-sm-24 m-t-1">
						<div class="form-group">							
							<label class="g24-col-sm-6 control-label">ชื่อ</label>
							<div class="g24-col-sm-14">
								<div class="form-group" id="cremation_type_name">
									<input type="text" class="form-control name" name="name" id="name" value=""  readonly="readonly">
								</div>
							</div>
						</div>						
						<div class="form-group cremation_receive_amount_div">
							<label class="g24-col-sm-6 control-label">จำนวนเงิน</label>
							<div class="g24-col-sm-6">
								<div class="form-group">
									<input type="text" class="form-control cremation_receive_amount number_int_only" name="amount" id="amount" value="" readonly="readonly">
								</div>
							</div>
							<label class="g24-col-sm-8 control-label text-left">บาท</label>
						</div>					
						<div class="form-group">
							<?php
								if(@$data['bank_type'] == ''){
									$checked_1 = 'checked';
									$checked_2 = '';
								}else{
									if(@$data['bank_type'] == '1'){
										$checked_1 = 'checked';
										$checked_2 = '';
									}else if(@$data['bank_type'] == '2'){
										$checked_1 = '';
										$checked_2 = 'checked';
									}
								}
							?>
							<label class="g24-col-sm-6 control-label">วิธีการชำระเงิน </label>
							<div class=" g24-col-sm-18 m-t-1">
								<div class="form-group">
									<input type="radio" id="bank_choose_1" name="bank_type" value="1" onclick="change_bank_type()" <?php echo $checked_1; ?>> โอนเข้าบัญชีสหกรณ์  
									<input type="radio" id="bank_choose_2" name="bank_type" value="2" onclick="change_bank_type()" <?php echo $checked_2; ?>> โอนเข้าบัญชีธนาคาร 
								</div>
							</div>
						</div>
						<div class="form-group">
							<div id="bank_type_1" style="display:none;">
								<label class="g24-col-sm-6 control-label" for="">ธนาคาร</label>
								<div class="g24-col-sm-18">
									<div class="form-group">
										<select name="account_id" id="account_id" class="form-control" style="width:50%;" onchange="" title="กรุณาเลือก บัญชี" >
											<option value="">เลือกบัญชี</option>
										</select>
									</div>
								</div>
							</div>
							<div id="bank_type_2" style="display:none;">
								<label class="g24-col-sm-6 control-label" for="">ธนาคาร</label>
								<div class="g24-col-sm-2">
									<div class="form-group">
										<input id="bank_id_show" class="form-control group-bank-left" type="text" value="<?php echo @$data["bank_id"]; ?>" readonly>
									</div>
								</div>
								<div class=" g24-col-sm-7">
									<div class="form-group">
										<select id="dividend_bank_id" name="dividend_bank_id" class="form-control group-bank-right" onchange="change_bank()">
											<option value="">เลือกธนาคาร</option>
											<?php foreach($banks as $key => $value) { ?>
											<option value="<?php echo $value["bank_id"]; ?>" <?php if($value["bank_id"]==@$data["bank_id"]) { ?> selected="selected"<?php } ?> > <?php echo $value["bank_name"]; ?>
												</option><?php } ?>
										</select>
									</div>
								</div>
								<div class="g24-col-sm-7" style="height: 40px;">
									&nbsp;
								</div>

								<label class="g24-col-sm-6 control-label" for="">สาขา</label>
								<div class="g24-col-sm-2">
									<div class="form-group">
										<input id="branch_id_show" class="form-control group-bank-left" type="text" value="<?php echo @$data["bank_branch_id"]; ?>" readonly>
									</div>
								</div>
								
								<div class=" g24-col-sm-7">
									<div class="form-group">
										<span id="bank_branch">
											<select id="dividend_bank_branch_id"  name="dividend_bank_branch_id" class="form-control group-bank-right" onchange="change_branch()">
												<option value="">เลือกสาขาธนาคาร</option>
											</select>
										</span>
									</div>
								</div>
								<div class="g24-col-sm-7" style="height: 40px;">
									&nbsp;
								</div>

								<label class="g24-col-sm-6 control-label" for="">เลขที่บัญชี</label>
								<div class=" g24-col-sm-9">
									<div class="form-group">
										<input id="bank_account_no" class="form-control clear_pay" name="bank_account_no"  type="text" value="<?php echo @$data["bank_account_no"]; ?>">
									</div>
								</div>
								<div class="g24-col-sm-7" style="height: 40px;">
									&nbsp;
								</div>

								<div class="g24-col-sm-24 modal_data_input">
									<label class="g24-col-sm-6 control-label " >วันที่โอนเงิน</label>
									<div class="input-with-icon g24-col-sm-5">
										<div class="form-group">
											<input id="date_transfer_picker" name="date_transfer" class="form-control" type="text" value="<?php echo $this->center_function->mydate2date(date('Y-m-d')); ?>" data-date-language="th-th">
											<span class="icon icon-calendar input-icon m-f-1"></span>
										</div>
									</div>
								</div>
								<div class="g24-col-sm-24 modal_data_input">
									<label class="g24-col-sm-6 control-label " >เวลาโอนเงิน</label>
									<div class="input-with-icon g24-col-sm-5">
										<div class="form-group">
											<input id="time_transfer" name="time_transfer" class="form-control" type="text" value="<?php echo date('H:i'); ?>">
											<span class="icon icon-clock-o input-icon m-f-1"></span>
										</div>
									</div>
								</div>

								<label class="g24-col-sm-6 control-label">แนบหลักฐานการโอนเงิน</label>
								<div class="g24-col-sm-6">
									<div class="form-group">
										<label class="fileContainer btn btn-info">
											<span class="icon icon-paperclip"></span> 
											เลือกไฟล์
											<input type="file" class="form-control" name="file_name" id="file_name" value="" multiple aria-invalid="false" onchange="readURL(this);">
										</label>
									</div>
									<span id="register_file_space"></span>
								</div>
								<div class="g24-col-sm-7" style="height: 40px;">
									&nbsp;
								</div>

								<div id="file_show" style="display:none">
									<label class="g24-col-sm-6 control-label"></label>
									<div class="g24-col-sm-6">
										<div class="form-group">											
											<img src="" id="file_transfer" class="m-b-1" width="150px" height="150px">
										</div>
									</div>
								</div>									
							</div>
						</div>
					</div>
				</form>
            </div>

            <div class="text-center m-t-1" style="padding-top:10px;">
				<button class="btn btn-info bt_save" id="bt_save"><span class="icon icon-save"></span> บันทึก</button>
				<button class="btn btn-info" id="bt_close"><span class="icon icon-close"></span> ออก</button>
            </div>
			<div class="text_center m-t-1">&nbsp;</div>
        </div>
    </div>
</div>
<script>
$(document).ready(function(){
    $(".btn-refund").click(function(){
        $("#cremation_request_id").val($(this).attr("data-req-id"));
        $("#name").val($(this).attr("data-name"));
        $("#amount").val($(this).attr("data-pay-amount"));
        member_cremation_raw_id = $(this).attr("data-member_cremation_raw_id");
        $.ajax({
            method: 'GET',
            url: base_url+'cremation/get_cremation_member_bank_account',
            data: {
                member_cremation_raw_id : member_cremation_raw_id
            },
            success: function(result){
                data = JSON.parse(result);
                $("#account_id").html("");
                $("#account_id").append(`<option value="">เลือกบัญชี</option>`);
                for (i = 0; i < data.coop_accounts.length; i++) {
                    $("#account_id").append(`<option value="`+data.coop_accounts[i].account_id+`">`+data.coop_accounts[i].account_id+`:`+data.coop_accounts[i].account_name+`</option>`);
                }
                if(data.bank_account) {
                    $("#bank_id_show").val(data.bank_account.dividend_bank_id);
                    $("#dividend_bank_id").val(data.bank_account.dividend_bank_id)
                    change_bank(data.bank_account.dividend_bank_branch_id);
                    $("#branch_id_show").val(data.bank_account.dividend_bank_branch_id)
                    
                    if(data.bank_account.dividend_bank_branch_id) {
                        $("#bank_choose_2").prop("checked", true);
                    } else {
                        $("#bank_choose_1").prop("checked", true);
                    }
                    $("#bank_account_no").val(data.bank_account.dividend_acc_num)
                } else {
					$("#bank_choose_1").prop("checked", true);
				}
				change_bank_type();
            }
        });
        $('#show_transfer').modal('show');
    });

    $("#bt_close").click(function() {
        $('#show_transfer').modal('hide');
    });

    $("#bt_save").click(function(){
        var bank_type = $('input[name=bank_type]:checked').val()
        if(bank_type == 1 && $("#account_id").val() == "") {
            swal('กรุณาเลือกบัญชี','','warning');
        } else if(bank_type == 2 && ($("#dividend_bank_id").val() == "" || $("#dividend_bank_branch_id").val() == "" || $("#bank_account_no").val() == "")) {
            var wraning_message = "";
            if($("#dividend_bank_id").val() == "") wraning_message += "กรุณาเลือกธนาคาร\n";
            if($("#dividend_bank_branch_id").val() == "") wraning_message += "กรุณาเลือกสาขา\n";
            if($("#bank_account_no").val() == "") wraning_message += "กรุณากรอกเลขบัญชี\n";
            swal(wraning_message,'','warning');
        } else {
            $("#from_transfer").submit();
        }
    });
});

function check_all_page(){
    if($('#chk-all').is(':checked') === true) {
        $('.chk-approve').prop('checked', true);
    }else{
        $('.chk-approve').prop('checked', false);
    }
}

function change_bank_type(){
	if($('#bank_choose_1').is(':checked')){
		$('#bank_type_1').show();
		$('#bank_type_2').hide();
	}else if($('#bank_choose_2').is(':checked')){
		$('#bank_type_1').hide();
		$('#bank_type_2').show();
	}
}
function change_bank(bank_branch_id = ''){
    var bank_id = $('#dividend_bank_id').val();
    $('#bank_id_show').val(bank_id);
    $('#branch_id_show').val('');
	$.ajax({
		method: 'GET',
		url: base_url+'ajax/get_bank_branch_by_bank_id',
		data: {bank_id : bank_id},
        success: function(result){
			data = $.parseJSON(result);
			$("#dividend_bank_branch_id").html("");
			select_text = `<option value="">เลือกสาขาธนาคาร</option>`;
			for (i = 0; i < data.length; i++) {
				if(bank_branch_id == data[i].branch_code) {
					select_text += `<option value="`+data[i].branch_code+`" selected>`+data[i].branch_name+`</option>`;
					$('#branch_id_show').val(bank_branch_id);
				} else {
					select_text += `<option value="`+data[i].branch_code+`">`+data[i].branch_name+`</option>`;
				}
			}
			$("#dividend_bank_branch_id").html(select_text);
        }
	});
}
function change_branch(){
    var branch_id = $('#dividend_bank_branch_id').val();
    $('#branch_id_show').val(branch_id);
}
function readURL(input) {
	var i = 0;
	$('#register_file_space').html('');
	if (input.files && input.files[0]) {
		$.each( input.files, function() {
			$('#register_file_space').append('<img id="img_'+i+'" src="#" style="margin: 5px 0px 5px -7px;" width="150px" height="150px"> ');
			read_file('img_'+i,input.files[i]);
			i++;
		});
	}
}
</script>