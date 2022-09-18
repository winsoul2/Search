var base_url = $('#base_url').attr('class');
$( document ).ready(function() {
	change_bank_type();
	$("#date_transfer_picker").datepicker({
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
	$('#time_transfer').datetimepicker({
		format: 'HH:mm',
		icons: {
			up: 'icon icon-chevron-up',
			down: 'icon icon-chevron-down'
		},
	});
});

function transfer_cremation(id, action){
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
		baseZ: 2000,
		bindEvents: false
	});

	$('#show_transfer').modal('show');
	$.ajax({
		type: "POST",
		url: base_url+'cremation/get_cremation_transfer',
		data: {
			id : id
		},
		success: function(msg) {
			//console.log(msg);
			response = $.parseJSON(msg);
			console.log(response);
			$(".cremation_receive_amount_div").show();
			$(".action_fee_percent_div").show();
			$(".cremation_transfer_id").val(response.cremation_transfer_id);
			$(".cremation_receive_id").val(response.cremation_receive_id);
			$(".cremation_request_id").val(response.cremation_request_id);
			$(".cremation_resign_id").val('');
			$(".member_id").val(response.member_id);	
			$(".cremation_type_id").val(response.cremation_id);
			$(".cremation_type_name").val(response.cremation_type_name);
			$(".cremation_detail_id").val(response.cremation_detail_id);
			$("#pay_type").val(response.pay_type);
			$(".cremation_receive_amount").val(format_number(response.cremation_receive_amount));		
			$(".action_fee_percent").val(format_number(response.action_fee_percent));
			$(".cremation_balance_amount").val(format_number(response.cremation_balance_amount));

			$(".admin_transfer").val(response.admin_transfer);
			$(".mobile").val(response.heir_phone);			
			$("#createdatetime").val(response.createdatetime);
			$("#date_transfer_picker").val(response.date_transfer);
			$("#time_transfer").val(response.time_transfer);
			$("#bank_choose_1").prop("checked", false);
			$("#bank_choose_2").prop("checked", false);
			$("#bank_choose_"+response.bank_type).attr('checked','checked');
			$("#bank_id_show").val(response.bank_id);
			$("#dividend_bank_id").val(response.bank_id);
			change_bank(response.bank_branch_id);
			$("#branch_id_show").val(response.bank_branch_id);
			$("#bank_account_no").val(response.bank_account_no);
			$('#file_show').hide();
			if(response.file_name != '' && response.file_name != null){
				$("#file_transfer").attr('src',base_url+'assets/uploads/cremation_transfer/'+response.file_name);
				$('#file_show').show()
				$('.fileContainer').hide()
			}else{
				$('.fileContainer').show()
			}

			list_account();
			if(response.bank_type == null && response.bank_account != null) {
				account = response.bank_account;
				$("#bank_choose_2").prop("checked", true);
				change_bank_type();
				$("#bank_id_show").val(account.dividend_bank_id);
				$('#dividend_bank_id').val(account.dividend_bank_id);
				$("#bank_account_no").val(account.dividend_acc_num)
				change_bank(account.dividend_bank_branch_id)
			} else {
				change_bank_type();
			}

			$("#action").val(action);
			if(action == 'view'){
				$('#bt_save').hide();
				$('#account_id').attr("disabled", true);
				$('#dividend_bank_id').attr("disabled", true);
				$('#dividend_bank_branch_id').attr("disabled", true);
				$('#bank_account_no').attr("disabled", true);
				$('#date_transfer_picker').attr("disabled", true);
				$('#time_transfer').attr("disabled", true);
				$('#file_name').hide();
				$('input[name="bank_type"]').attr('disabled', true);
			}else{
				$('#bt_save').show();
				$('#account_id').attr("disabled", false);
				$('#dividend_bank_id').attr("disabled", false);
				$('#dividend_bank_branch_id').attr("disabled", false);
				$('#bank_account_no').attr("disabled", false);
				$('#date_transfer_picker').attr("disabled", false);
				$('#time_transfer').attr("disabled", false);
				$('#file_name').show();
				$('input[name="bank_type"]').attr('disabled', false);
			}
			$.unblockUI();
		}
	});	
}

function transfer_cremation_1(id, action){
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
			$(".evidence-label").html(`<label id="filename" style="padding: 7px;">
										<a class="comment-file-a-18" href="`+base_url+`cremation/download_file?id=`+id+`&type=evidence">
											<span>
												`+data.evidence+`
											</span>
										</a>
									</label>`);
		} else {
			$(".evidence-label").html("");
		}
		if(data.testament) {
			$(".testament-label").html(`<label id="filename" style="padding: 7px;">
										<a class="comment-file-a-18" href="`+base_url+`cremation/download_file?id=`+id+`&type=testament">
											<span>
												`+data.testament+`
											</span>
										</a>
									</label>`);
		} else {
			$(".testament-label").html("");
		}

		$.get(base_url+"cremation/get_cremation_info?member_cremation_id="+data.member_cremation_id, 
		function(result) {
			data_1 = JSON.parse(result);
			$("#member_cremation_id_input").val(data_1.member_cremation_id);
			$("#member_id").val(data_1.member_id);
			$("#member_cremation_id").val(data_1.member_cremation_id);
			$("#cremation_request_id").val(data_1.cremation_request_id);
			$("#name").val(data_1.prename_full+data_1.assoc_firstname+" "+ data_1.assoc_lastname);

			$('#receiver').html(`<option value="">เลือกผู้รับเงินฌาปนกิจ</option>`);
			if(data_1.receiver_1) {
				$('#receiver').append($("<option></option>").attr("value",1).text(data_1.receiver_1));
			}
			if(data_1.receiver_2) {
				$('#receiver').append($("<option></option>").attr("value",2).text(data_1.receiver_2));
			}
			if(data_1.receiver_3) {
				$('#receiver').append($("<option></option>").attr("value",3).text(data_1.receiver_3));
			}

			if(data_1.death_date) {
				date_split = data_1.death_date.split("-");
				year = parseInt(date_split[0]) + 543;
				monthNamesShort = ['ม.ค.', 'ก.พ.', 'มี.ค.', 'เม.ย.', 'พ.ค.', 'มิ.ย.', 'ก.ค.', 'ส.ค.', 'ก.ย.', 'ต.ค.', 'พ.ย.', 'ธ.ค.']
				$("#death_date").val(date_split[2]+" "+monthNamesShort[parseInt(date_split[1])]+" "+year);
			}
		});

		$("#action").val(action);
		if(action == 'view'){
			$('#bt_save').hide();
		}else{
			$('#bt_save').show();
		}
	});

	$('#show_transfer_1').modal('show');
}

function transfer_resign_cremation(id, action) {
	$('#show_transfer').modal('show');
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
		baseZ: 2000,
		bindEvents: false
	});
	$.ajax({
		type: "POST",
		url: base_url+'cremation/get_cremation_resign_transfer',
		data: {
			id : id
		},
		success: function(msg) {
			response = $.parseJSON(msg);
			console.log(response)
			$(".cremation_receive_amount_div").hide();
			$(".action_fee_percent_div").hide();
			$(".cremation_transfer_id").val(response.cremation_transfer_id);
			$(".cremation_receive_id").val('');
			$(".cremation_request_id").val(response.cremation_request_id);
			$(".cremation_resign_id").val(response.cremation_resign_id);
			$(".member_id").val(response.member_id);	
			$(".cremation_type_id").val(response.cremation_id);
			$(".cremation_type_name").val(response.cremation_type_name);
			$(".cremation_detail_id").val(response.cremation_detail_id);
			$("#pay_type").val(response.pay_type);
			$(".cremation_receive_amount").val('');		
			$(".action_fee_percent").val('');
			$(".cremation_balance_amount").val(format_number(response.cremation_balance_amount));
			$(".admin_transfer").val(response.admin_transfer);
			$(".mobile").val(response.mobile);			
			$("#createdatetime").val(response.createdatetime);
			$("#date_transfer_picker").val(response.date_transfer);
			$("#time_transfer").val(response.time_transfer);
			$("#bank_choose_1").prop("checked", false);
			$("#bank_choose_2").prop("checked", false);
			$("#bank_choose_"+response.bank_type).attr('checked','checked');
			$("#bank_id_show").val(response.bank_id);
			$("#dividend_bank_id").val(response.bank_id);
			change_bank(response.bank_branch_id);
			$("#branch_id_show").val(response.bank_branch_id);
			$("#bank_account_no").val(response.bank_account_no);
			$('#file_show').hide();
			if(response.file_name != '' && response.file_name != null){
				$("#file_transfer").attr('src',base_url+'assets/uploads/cremation_transfer/'+response.file_name);
				$('#file_show').show()
				$('.fileContainer').hide()
			}else{
				$('.fileContainer').show()
			}
			
			// list_account();
			// change_bank_type();
			list_account();
			if(response.bank_type == null && response.bank_account != null) {
				account = response.bank_account;
				$("#bank_choose_2").prop("checked", true);
				change_bank_type();
				$("#bank_id_show").val(account.dividend_bank_id);
				$('#dividend_bank_id').val(account.dividend_bank_id);
				$("#bank_account_no").val(account.dividend_acc_num)
				change_bank(account.dividend_bank_branch_id)
			} else {
				change_bank_type();
				$.unblockUI();
			}

			$("#action").val(action);
			if(action == 'view'){
				$('#bt_save').hide();
				$('#account_id').attr("disabled", true);
				$('#dividend_bank_id').attr("disabled", true);
				$('#dividend_bank_branch_id').attr("disabled", true);
				$('#bank_account_no').attr("disabled", true);
				$('#date_transfer_picker').attr("disabled", true);
				$('#time_transfer').attr("disabled", true);
				$('#file_name').hide();
				$('input[name="bank_type"]').attr('disabled', true);
			}else{
				$('#bt_save').show();
				$('#account_id').attr("disabled", false);
				$('#dividend_bank_id').attr("disabled", false);
				$('#dividend_bank_branch_id').attr("disabled", false);
				$('#bank_account_no').attr("disabled", false);
				$('#date_transfer_picker').attr("disabled", false);
				$('#time_transfer').attr("disabled", false);
				$('#file_name').show();
				$('input[name="bank_type"]').attr('disabled', false);
			}
		}
	});
}

function readURL(input) {
	var i = 0;
	//console.log(input.files);
	$('#register_file_space').html('');
	if (input.files && input.files[0]) {
		$.each( input.files, function() {
			$('#register_file_space').append('<img id="img_'+i+'" src="#" style="margin: 5px 0px 5px -7px;" width="150px" height="150px"> ');
			read_file('img_'+i,input.files[i]);
			i++;
		});
	}
}

function read_file(target,input){
	var reader = new FileReader();
	reader.onload = function (e) {
		$('#'+target).attr('src', e.target.result);
	}
	reader.readAsDataURL(input);
}

function view_request(){
	$('#viewRequest').modal('show');
}

function list_account(){
	var member_id = $(".member_id").val();
	var cremation_receive_id = $(".cremation_receive_id").val();
	var cremation_request_id = $(".cremation_request_id").val();
    $.ajax({
        method: 'POST',
        url: base_url+'cremation/get_account_list',
        data: {
            member_id : member_id,
            cremation_receive_id : cremation_receive_id,
            cremation_request_id : cremation_request_id
        },
        success: function(msg){
			console.log(msg)
            $('#account_id').html(msg);
        }
    });	
}

function change_type_view(){
	var cremation_type_id = $("#cremation_type_id_view").val();
	$.ajax({
		type: "POST",
		url: base_url+'cremation/get_cremation_type',
		data: {
			id : cremation_type_id
		},
		success: function(msg) {
			response = $.parseJSON(msg);
			//console.log(response);
			if(response){
				$("#cremation_request_detail_view").html(response.cremation_detail);
				$("#start_date_view").val(response.start_date);
			}else{
				$("#cremation_request_detail_view").html('');
				$("#start_date_view").val('');
			}
		}
	});	
}

function close_modal(id){
	$('#'+id).modal('hide');
}

function check_form_transfer(){
	$('#from_transfer').submit();
}

function check_form_transfer_1(){
	$('#from_transfer_1').submit();
}


function transfer_cancel(cremation_receive_id,cremation_transfer_id){
	var status_to = '1'; //ยกเลิกโอนเงิน
	var title = 'ท่านต้องการยกเลิกการโอนเงินใช่หรือไม่';

	 swal({
        title: title,
        text: "",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: '#DD6B55',
        confirmButtonText: 'ยืนยัน',
        cancelButtonText: "ยกเลิก",
        closeOnConfirm: false,
        closeOnCancel: true
    },
    function(isConfirm) {
        if (isConfirm) {
			$.ajax({
				url: base_url+'/cremation/coop_transfer_cancel',
				method: 'GET',
				data: {
					'cremation_receive_id': cremation_receive_id,
					'cremation_transfer_id': cremation_transfer_id,
					'status_to': status_to
				},
				success: function(msg){
				  // console.log(msg); return false;
					if(msg == 1){
					  document.location.href = base_url+'cremation/cremation_transfer';
					}else{

					}
				}
			});
        } else {
			
        }
    });
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
		baseZ: 2000,
		bindEvents: false
	});

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
			$.unblockUI();
        }
	});
}
function change_branch(){
    var branch_id = $('#dividend_bank_branch_id').val();
    $('#branch_id_show').val(branch_id);
}