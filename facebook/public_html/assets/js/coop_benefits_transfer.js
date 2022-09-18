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
	$("#pay_checked_btn").click(function() {
		swal({
			title: "ต้องการทำรายการชำระเงินใช่หรือไม่",
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
				$("#form1").submit();
			} else {
			}
		});
	});
});

function transfer_benefits(id, action){
	$('#show_transfer').modal('show');
	$.ajax({
		type: "POST",
		url: base_url+'benefits/get_benefits_transfer',
		data: {
			id : id
		},
		success: function(msg) {
			//console.log(msg);
			response = $.parseJSON(msg);
			//console.log(response);
			$(".benefits_request_id").val(id);
			$(".member_id").val(response.member_id);
			$(".member_name").val(response.firstname_th+' '+response.lastname_th);
			$(".admin_request").val(response.admin_request);
			$(".benefits_no").val(response.benefits_no);
			$(".benefits_type_name").val(response.benefits_type_name);
			$(".benefits_approved_amount").val(response.benefits_approved_amount);
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
				$("#file_transfer").attr('src',base_url+'assets/uploads/benefits_transfer/'+response.file_name);
				$('#file_show').show()
				$('.fileContainer').hide()
			}else{
				$('.fileContainer').show()
			}
			
			list_account();	
			change_bank_type();
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
	var benefits_request_id = $(".benefits_request_id").val();
    $.ajax({
        method: 'POST',
        url: base_url+'benefits/get_account_list',
        data: {
            member_id : member_id,
            benefits_request_id : benefits_request_id
        },
        success: function(msg){
            $('#account_id').html(msg);
        }
    });	
}

function change_type_view(){
	var benefits_type_id = $("#benefits_type_id_view").val();
	$.ajax({
		type: "POST",
		url: base_url+'benefits/get_benefits_type',
		data: {
			id : benefits_type_id
		},
		success: function(msg) {
			response = $.parseJSON(msg);
			//console.log(response);
			if(response){
				$("#benefits_request_detail_view").html(response.benefits_detail);
				$("#start_date_view").val(response.start_date);
			}else{
				$("#benefits_request_detail_view").html('');
				$("#start_date_view").val('');
			}
		}
	});	
}

function close_modal(id){
	$('#'+id).modal('hide');
}

function show_file(){
	 $('#show_file_attach').modal('show');
}

function edit_request(benefits_request_id,member_id){
	$('#btn_show_file').hide();
	$('#btn_show_not_file').hide();
	$.ajax({
		type: "POST",
		url: base_url+'benefits/get_benefits_request',
		data: {
			id : benefits_request_id
		},
		success: function(msg) {
			response = $.parseJSON(msg);
			//console.log(response);						
			$("#benefits_request_id").val(response.benefits_request_id);
			$("#benefits_type_id").val(response.benefits_type_id);
			$("#benefits_approved_amount").val(response.benefits_approved_amount);
			$("#benefits_request_detail").html(response.benefits_detail);
			$("#user_name").html(response.user_name);
			$("#member_id").val(response.member_id);
			$("#member_name").val(response.firstname_th+' '+response.lastname_th);
			$("#birthday").val(response.birthday);
			$("#age").val(response.age);
			$("#apply_date").val(response.apply_date);
			$("#apply_age").val(response.apply_age);
			$("#retry_date").val(response.retry_date);
			$("#retry_status").val(response.retry_status);
			
			if(response.benefits_check_condition == '1'){
				$('#benefits_check_condition').prop('checked', true);
			}else{
				$('#benefits_check_condition').prop('checked', false);
			}	
			
			var txt_file_attach = '<table width="100%">';
			var i=1;
			console.log(response.coop_file_attach);
			for(var key in response.coop_file_attach){
				txt_file_attach += '<tr class="file_row" id="file_'+response.coop_file_attach[key].id+'">\n';
				txt_file_attach += '<td><a href="'+base_url+'/assets/uploads/benefits_request/'+response.coop_file_attach[key].file_name+'" target="_blank">'+response.coop_file_attach[key].file_old_name+'</a></td>\n';
				txt_file_attach += '<td style="color:red;font-size: 20px;cursor:pointer;" align="center" width="10%"></td>\n';
				//txt_file_attach += '<td style="color:red;font-size: 20px;cursor:pointer;" align="center" width="10%"><span class="icon icon-ban" onclick="del_file(\''+response.coop_file_attach[key].id+'\')"></span></td>\n';
				txt_file_attach += '</tr>\n';
				i++;
			}
			txt_file_attach += '</table>';
			$('#show_file_space').html(txt_file_attach);
			if(i>1){
				$('#btn_show_file').show();
			}else{
				$('#btn_show_not_file').show();				
			}
		}
	});
	$('#myModalRequest').modal('show');
}

function check_form_transfer(){
	$('#from_transfer').submit();
	$('#bt_save').prop('disabled',true);
}

function transfer_cancel(benefits_request_id,benefits_transfer_id){
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
				url: base_url+'/benefits/coop_transfer_cancel',
				method: 'GET',
				data: {
					'benefits_request_id': benefits_request_id,
					'benefits_transfer_id': benefits_transfer_id,
					'status_to': status_to
				},
				success: function(msg){
				  // console.log(msg); return false;
					if(msg == 1){
					  document.location.href = base_url+'benefits/benefits_transfer';
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
    var bank_id = $('#dividend_bank_id').val();
    $('#bank_id_show').val(bank_id);
    $('#branch_id_show').val('');
    $.ajax({
        method: 'POST',
        url: base_url+'manage_member_share/get_bank_branch_list',
        data: {
            bank_id : bank_id
        },
        success: function(msg){
            $('#bank_branch').html(msg);
			if(bank_branch_id != ''){
				$("#dividend_bank_branch_id").val(bank_branch_id);
				if($("#action").val() == 'view'){
					$('#dividend_bank_branch_id').attr("disabled", true);
				}else{
					$('#dividend_bank_branch_id').attr("disabled", false);
				}
			}
        }
    });
}
function change_branch(){
    var branch_id = $('#dividend_bank_branch_id').val();
    $('#branch_id_show').val(branch_id);
}
