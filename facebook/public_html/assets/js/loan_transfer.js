$( document ).ready(function() {
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
	
	$(".modal").on("hidden.bs.modal", function(){
		$('#contract_number').val("");
		$('#member_id').val("");
		$('#member_name').val("");
		$('#loan_amount').val("");
		$('#dividend_bank_id').val("");
		$('#dividend_bank_branch_id').val("");
		$('#dividend_acc_num').val("");
		$("input:radio").removeAttr("checked");
		$('.pay_type_1').hide();
		$('.pay_type_2').hide();
	});
	$("#date_start").datepicker({
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
	$("#date_end").datepicker({
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

	$("#date_transfer").datepicker({
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

	$(".form_date_picker").datepicker({
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

	$("#date_approve").datepicker({
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
function format_the_number(ele){
	var value = $('#'+ele.id).val();
	if(value!=''){
		value = value.replace(',','');
		value = parseInt(value);
		value = value.toLocaleString();
		if(value == 'NaN'){
			$('#'+ele.id).val('');
		}else{
			$('#'+ele.id).val(value);
		}
	}else{
		$('#'+ele.id).val('');
	}
}

function check_submit(){
	var alert_text = '';
	
	if(alert_text!=''){
		swal('กรุณากรอกข้อมูลต่อไปนี้' , alert_text , 'warning');
	}else{
		
	}
}
function chkNumber(ele){
	var vchar = String.fromCharCode(event.keyCode);
	if ((vchar<'0' || vchar>'9') && (vchar != '.')) return false;
	ele.onKeyPress=vchar;
}
 function search_loan(){
	 var contract_number = $('#contract_number').val();
	 if(contract_number !=''){
		$.post(base_url+"/ajax/get_loan_data", 
			{	
				contract_number: contract_number
			}
			, function(result){
				if(result=='not_found'){
					swal('ไม่พบข้อมูล');
					$('#account_list').html('<option value="">เลือกบัญชี</option>')
					$('.all_input').val('');
					$('#file_show').html('');
					$('#btn_cancel_transfer').hide();
				}else{
					var obj = JSON.parse(result);
					//console.log(obj);
					if(obj.coop_loan.transfer_status == '0'){
						$('#btn_cancel_transfer').show();
						$('#btn_cancel_transfer').attr('onclick',"cancel_transfer('"+obj.coop_loan.transfer_id+"','"+obj.coop_loan.id+"')");
					}else{
						$('#btn_cancel_transfer').hide();
						$('#btn_cancel_transfer').attr('onclick',"");
					}
					$('.loan_id').val(obj.coop_loan.id);
					$('.member_id').val(obj.coop_loan.member_id);
					$('#member_name').val(obj.coop_mem_apply.firstname_th+" "+obj.coop_mem_apply.lastname_th);
					$('#loan_amount').val(obj.coop_loan.loan_amount);
					$('#loan_type').val(obj.coop_loan.loan_type);
					$('#period_amount').val(obj.coop_loan.period_amount);
					$('#loan_date').val(obj.coop_loan.createdatetime);
					if(obj.coop_loan.transfer_id == null){
						$('#transfer_status').val('ยังไม่ได้โอนเงิน');
						if(obj.coop_loan.loan_status == '1'){
							$('#btn_open_transfer').show();
						}else{
							$('#btn_open_transfer').hide();
						}
					}else{
						if(obj.coop_loan.transfer_status == '0'){
							$('#transfer_status').val('โอนเงินแล้ว');
						}else if(obj.coop_loan.transfer_status == '1'){
							$('#transfer_status').val('รออนุมัติยกเลิก');
						}else if(obj.coop_loan.transfer_status == '2'){
							$('#transfer_status').val('ยกเลิกรายการแล้ว');
						}
						
						$('#date_transfer').val(obj.coop_loan.date_transfer);
						$('#btn_open_transfer').hide();
					}
					
					$('#account_name').val(obj.coop_loan.account_name);
					$('#user_name').val(obj.coop_loan.user_name);
					if(obj.coop_loan.file_name!=null){
						file_link = "<a target='_blank' href='"+base_url+"/assets/uploads/loan_transfer_attach/"+obj.coop_loan.file_name+"'>"+obj.coop_loan.file_name+"</a>";
						$('#file_show').html(file_link);
					}else{
						$('#file_show').html('');
					}
					if(obj.coop_loan.account_id != null){
						var account_id = obj.coop_loan.account_id;
					}else{
						var account_id = '';
					}
					get_account_list(obj.coop_loan.member_id, account_id);
				}
			});
	 }else{
		 swal('กรุณากรอกเลขที่สัญญาที่ต้องการค้นหา');
		 $('#account_list').html('<option value="">เลือกบัญชี</option>')
		$('.all_input').val('');
		$('#file_show').html('');
		$('#btn_cancel_transfer').hide();
		$('#btn_cancel_transfer').attr('onclick',"");
	 }
 }
 function get_account_list(member_id, account_id){
	 $.post(base_url+"/ajax/get_account_list", 
			{	
				member_id: member_id,
				account_id : account_id
			}
			, function(result){
					$('#account_list_space').html(result);
			});
 }
 function open_modal(id){
  $('#'+id).modal('show');
 }
 
 function readURL(input) {
	if (input.files && input.files[0]) {
		var reader = new FileReader();
		reader.onload = function (e) {
			$('#ImgPreview').attr('src', e.target.result);
		}
		reader.readAsDataURL(input.files[0]);
	}
}

function check_form(){
	if($('#file_attach').val() == ''){
		 swal('กรุณาแนบหลักฐานการโอนเงิน');
	 }else{
		$('#form_loan_transfer').submit();
	 }	
}
function change_account(){
	$('#account_id').val($('#account_list :selected').val());
	$('#account_name').val($('#account_list :selected').attr('account_name'));
}
function cancel_transfer(transfer_id, loan_id){
	swal({
        title: 'ท่านต้องการยกเลิกรายการใช่หรือไม่?',
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
            document.location.href = base_url+'/loan/loan_transfer?transfer_id='+transfer_id+'&action=delete_transfer&loan_id='+loan_id;
        } else {
			
        }
    });
}

function removeCommas(str) {
	str = str || "";
	return(str.replace(/,/g,''));
}

var _editor_cheque;
function open_transfer_modal(loan_id, seq){
	$.ajax({
		url:base_url+"/loan/get_loan_data",
		method:"post",
		data:{loan_id:loan_id, seq: seq},
		dataType:"text",
		success:function(data)
		{
			var obj = JSON.parse(data);
			_editor_cheque = obj;

			//console.log(obj);
			$('#loan_id').val(loan_id);
			$('#contract_number').val(obj.contract_number);
			$('#member_id').val(obj.member_id);
			$('#member_name').val(obj.firstname_th+"  "+obj.lastname_th);
			$('#loan_amount').val(obj.loan_amount);			
			$('#amount_transfer').val(obj.estimate_receive_money);			
			$("#date_transfer").val(obj.date_receive_money);
			$("#transfer_balance").val(obj.balance_transfer);
			$("#transfer_real_amount").val(obj.balance_transfer);
			$("#transfer_period").val(obj.period);
			$("#installment_seq").val(obj.seq);

			$('#dividend_bank_id').val(obj.transfer_bank_id);
			//$('#dividend_bank_branch_id').val(obj.dividend_bank_branch_id);
			$('#dividend_acc_num').val(obj.transfer_bank_account_id);

			if(obj.transfer_type == 0){
                $('#pay_type_0').attr('checked', true);
			}else if(obj.transfer_type == 1 || obj.transfer_type == 2){
                $('#pay_type_1').attr('checked', true);
            }else if(obj.transfer_type == 4){
                $('#pay_type_2').attr('checked', true);
            }
			// $('#pay_type_'+obj.transfer_type).attr('checked', true);
			change_pay_type();

			list_account(obj.transfer_account_id);
			$('#transfer_modal').modal('show');

			$.post(base_url + "Save_money/get_account_saving", {
				member_id: obj.member_id
			}
			, function (result) {
				obj = JSON.parse(result);
				$('#local_account_id')
					.empty()
					.append('<option selected="selected" value="">เลือกบัญชี</option>');
				$.each(obj, function (i, item) {
					$('#local_account_id').append($('<option>', { 
						value: item.account_id,
						text : item.account_id+' '+item.account_name
					}));
				});
			});
		}
	});		
}

function change_pay_type(){
	if($('#pay_type_1').is(':checked')){
		$('.pay_type_1').show();
		$('.pay_type_2').hide();
	}else if($('#pay_type_2').is(':checked')){
		$('.pay_type_1').hide();
		$('.pay_type_2').show();
	}else{
		$('.pay_type_1').hide();
		$('.pay_type_2').hide();
	}
}

function cash_submit(){
	var id = $('#loan_id').val();
	$.get(base_url+'loan/check_loan_before_transfer?id='+id, function(res){
		if(res.status !== 'success' && res.status_code !== 200) {
			swal('โอนเงินกู้ไมสำเร็จ', 'กรุณาทำการอนุมัติสัญญาเงินกู้ก่อนทำรายการอีกครั้ง', 'warning');
			setTimeout(function () {
				window.location.reload();
			}, 1000);
		}
	});

	var text_alert = "";
	if($('#amount_transfer').val() == ''){
		text_alert += " - กรุณาป้อนยอดเงินที่ได้รับ \n";
	}
	if($('input[name=pay_type]').is(":checked") == false){
		text_alert += "กรุณาเลือกวิธีการชำระเงิน \n";
	}


	if($('#pay_type_2').is(':checked') && $('.card-item').length > 0) {
		let total = 0;
		$('.card-item').each(function (i) {
			total += $(this).find('.cheque-amount').float();
			if ($(this).find('.cheque-id').val() === "") {
				text_alert += "- กรุณาระบุเช็คธนาคาร \n";
			}
			if ($(this).find('.cheque-number').val() === "") {
				text_alert += `- กรุณาหมายเลขเช็คลำดับที่ ${i+1}\n`;
			}
			if($(this).find('.cheque-amount').float() === 0){
				text_alert += `- กรุณาระบุจำนวนเงินของเช็คลำดับที่ ${i+1}\n`;
			}
		});
		if ($('#transfer_balance').float() !== total){
			text_alert += "- ผลรวมของเช็คไม่ตรงกับยอดโอน \n";
		}
	}

	if(text_alert != ''){
		swal(text_alert);
	}else{
		$("#bt_loan_transfer").attr('disabled','disabled');
		$('#form_loan_transfer').submit();
	}
}


function list_account(account_id=''){
	var member_id = $("#member_id").val();
    $.ajax({
        method: 'POST',
        url: base_url+'loan/get_account_list',
        data: {
            member_id : member_id
        },
        success: function(msg){
			//console.log(msg);
            $('#account_list_space').html(msg);
			if(account_id!=''){
				$('#account_id').val(account_id);
			}
        }
    });	
}
function change_type(){
	$.ajax({
		url: base_url+'loan/change_loan_type',
		method: 'POST',
		data: {
			'type_id': $('#loan_type').val()
		},
		success: function(msg){
		   $('#loan_name').html(msg);
		}
	});		
	$('#type_name').val($('#type_id :selected').text());
}

function set_bank(val) {
	$("#bank_id").val(val);
}

function set_branch_code(val) {
	$("#branch_code").val(val);
}

function show(val) {
	if (val == 'xd_sec') {
		$("#xd_sec").show();
		$("#cheque_multi").hide();
		$("#che_sec").hide();
		$("#other_sec").hide();
	} else if (val == 'che_sec') {
		$("#xd_sec").hide();
		$("#cheque_multi").show();
		$("#che_sec").show();
		$("#other_sec").hide();
	} else if(val == 'other_sec') {
		$("#xd_sec").hide();
		$("#cheque_multi").hide();
		$("#che_sec").hide();
		$("#other_sec").show();
	}else {
		$("#xd_sec").hide();
		$("#cheque_multi").hide();
		$("#che_sec").hide();
		$("#other_sec").hide();
	}
}

$(document).on('blur', "#transfer_real_amount", function () {
	const transferBalance = parseFloat($('#transfer_balance').val().split(",").join(""));
	const value = parseFloat($(this).val().split(",").join(""));
	if(value > transferBalance){
		swal("ยอดเงินเกิน", "ไม่สามารถใส่ยอดเงินเกินกว่ายอดเงินได้รับคงเหลือ", 'error');
		$(this).val(format_number(transferBalance));
		return;
	}
	$(this).val(format_number(value));

});
