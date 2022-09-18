$( document ).ready(function() {
	$("#account_datetime").datepicker({
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
	$("#btn-add-account-detail").click(function() {
	});
	$(document).on("change",".acc_input",function() {
		cal_acc_input();
	});
});

function open_modal(id){
	$('#'+id).modal('show');
}

function close_modal(id){
	$('#'+id).modal('hide');
}

function clear_modal(id){
	$('#account_description').val('');
	$('#account_data').html('');
	$('#add_account_cash').modal('hide');
	$('#add_account_tran').modal('hide');
	$('#add_account_type').modal('hide');
}

function add_account(){
	open_modal('add_account_type');
}

function tran_modal(type){
	$('#add_account_type').modal('hide');
	var date = new Date();
	var day = date.getDate() < 10 ? "0"+date.getDate() : date.getDate();
	var month = date.getMonth() < 10 ? "0"+(date.getMonth() + 1) : date.getMonth() + 1;
	var year = date.getFullYear() + 543;

	if(type == 1) {
		$(".add-tr").remove();
		$("#account_id_cash").val('');
		$("#account_datetime_cash").val(day+"/"+month+"/"+year);
		$("#account_description_cash").val('');
		$("#account_chart_id_cash_0").val("");
		$("#acc_desc_0").val("");
		$("#acc_0").val("");
		$("#sum_cash").val(0);
		createSelect2("add_account_cash");
		$("#add_account_cash").modal("show");
	} else if (type == 2) {
		$(".add-tr").remove();
		$("#account_id_tran").val('');
		$("#account_datetime").val(day+"/"+month+"/"+year);
		$("#account_description").val('');
		$("#sum_debit").val(0);
		$("#sum_credit").val(0);
		$("#journal_type_tran").val("J");
		createSelect2("add_account_tran");
		$("#add_account_tran").modal("show");
	} else if (type == 3) {
		$(".add-tr").remove();
		$("#account_id_tran").val('');
		$("#account_datetime").val(day+"/"+month+"/"+year);
		$("#account_description").val('');
		$("#sum_debit").val(0);
		$("#sum_credit").val(0);
		$("#journal_type_tran").val("S");
		createSelect2("add_account_tran");
		$("#add_account_tran").modal("show");
	}
}

function call_sum_credit_debit(number,type) {
	var debit_input_now = 0;
	var credit_input_now = 0;
	var i = 0;
	var arr = document.getElementsByName('countnum');
	while (i <= arr.length) {
		//รวมจำนวนเงิน เคดิต เดบิต ของการบันทึกบัญชีในครั้งนั้น
		if($('#debit_input'+i).val() != undefined){
			if(parseFloat(removeCommas($('#debit_input'+i).val())) == NaN || $('#debit_input'+i).val() == ''){
			}else{
				debit_input_now += parseFloat(removeCommas($('#debit_input'+i).val()));
				credit_input_now += 0;
			}
		}
		if($('#credit_input'+i).val() != undefined) {
			if (parseFloat(removeCommas($('#credit_input'+i).val())) == NaN || $('#credit_input'+i).val() == '') {
			} else {
				credit_input_now += parseFloat(removeCommas($('#credit_input'+i).val()));
				debit_input_now += 0;
			}
		}

		i++;
	}

	credit_input_now = credit_input_now.toFixed(2);
	debit_input_now = debit_input_now.toFixed(2);
	//แสดงผลรวมของบัญชีฝั่งเคดิต และเดบิต
	$('#sum_debit').val(debit_input_now);
	$('#sum_credit').val(credit_input_now);
	format_the_number_decimal(document.getElementById("sum_debit"));
	format_the_number_decimal(document.getElementById("sum_credit"));
}

function add_account_detail(type){
	var input_number = $('#input_number').val();
	$('#input_number').val(parseInt(input_number) +1);
	var void_input = 0;
	var debit_input = 0;
	var credit_input = 0;
	$('.account_detail').each(function(){
		if($(this).val()==''){
			void_input++;
		}
	});
	$('.debit_input').each(function(){
		debit_input = parseFloat(debit_input) + parseFloat(removeCommas($(this).val()));
	});
	$('.credit_input').each(function(){
		credit_input = parseFloat(credit_input) + parseFloat(removeCommas($(this).val()));
	});

	if(input_number == 6) {
		modal = $("#add_account_tran").find(".modal-body");
		div_h = modal.height();
		modal.css("height", div_h);
		modal.css("overflow-y", "scroll");
	} else if(input_number == 5) {
		modal = $("#add_account_tran").find(".modal-body");
		div_h = modal.height();
		modal.css("height", "");
		modal.css("overflow-y", "");
	}

	$.post(base_url+"account/ajax_add_account_detail", 
	{	
		type: type,
		input_number : input_number
	}
	, function(result){
		$('#account_data').append(result);
		input_number++;
		createSelect2("add_account_tran");
	});
}

function form_submit(){
	var text_alert = '';
	var void_input = 0;
	var debit_input = 0;
	var credit_input = 0;
	if($('#account_datetime').val()==''){
		text_alert += ' - กรุณาระบุวันที่ของรายการ\n';
	}
	// if($('#account_description').val()==''){
	// 	text_alert += ' - กรุณาระบุรายละเอียดของรายการ\n';
	// }
	$('.account_detail').each(function(){
		if($(this).val()==''){
			void_input++;
		}
	});
	$(".account_detail_sel").each(function() {
		if($(this).val()==''){
			void_input++;
		}
	});
	if(void_input>0){
		text_alert += ' - กรุณาระบุข้อมูล เดบิต เครดิต ให้ครบถ้วน\n';
	}
	$('.debit_input').each(function(){
		debit_input = parseFloat(debit_input) + parseFloat(removeCommas($(this).val()));
	});
	$('.credit_input').each(function(){
		credit_input = parseFloat(credit_input) + parseFloat(removeCommas($(this).val()));
	});
	if((Math.round(credit_input * 100) / 100) != (Math.round(debit_input * 100) / 100)){
		text_alert += ' - กรุณาลงรายการ เดบิต และ เครดิตให้เท่ากัน\n';
	}

	if(text_alert!=''){
		swal('เกิดข้อผิดพลาด',text_alert,'warning');
	}else{
		$(".debit_input").each(function() {
			$(this).val(removeCommas($(this).val()));
		});
		$(".credit_input").each(function() {
			$(this).val(removeCommas($(this).val()));
		});
		$('#form1').submit();
	}
}

function form_cash_submit() {
	var text_alert = '';
	var void_input = 0;

	if(!$('#pay_type_0').is(':checked') && !$('#pay_type_1').is(':checked')) {
		text_alert += ' - กรุณาเลือกประเภทการชำระเงิน\n';
	}
	if($('#account_datetime_cash').val()==''){
		text_alert += ' - กรุณาระบุวันที่ของรายการ\n';
	}
	// if($('#account_description_cash').val()==''){
	// 	text_alert += ' - กรุณาระบุรายละเอียดของรายการ\n';
	// }
	$('.acc_input').each(function(){
		if($(this).val()==''){
			void_input++;
		}
	});
	if(void_input>0){
		text_alert += ' - กรุณาระบุจำนวนให้ครบถ้วน\n';
	}

	if(text_alert!=''){
		swal('เกิดข้อผิดพลาด',text_alert,'warning');
	}else{
		$(".acc_input").each(function( index ) {
			$(this).val(removeCommas($(this).val()));
		});
		$('#form1_cash').submit();
	}
}

function account_excel_tranction_voucher(detail,date,account_detail_id){
	$('#detail').val(detail);
	$('#date').val(date);
	$('#account_detail_id').val(account_detail_id);
	$('#from_excel_day').submit();
}

function account_pdf_tranction_voucher(detail,date,account_detail_id){
	$('#detail_pdf').val(detail);
	$('#date_pdf').val(date);
	$('#account_detail_id_pdf').val(account_detail_id);
	$('#from_pdf_day').submit();
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
			$('#'+ele.id).val('');
		}else{
			value = (num[0] == '')?0:parseInt(num[0]);
			value = value.toLocaleString()+decimal;
			$('#'+ele.id).val(value);
		}
	}else{
		$('#'+ele.id).val('');
	}
}

function createSelect2(id){
	$('.js-data-example-ajax').select2({
		dropdownParent: $("#"+id),
		matcher: matchStart
	});
}

function cal_acc_input() {
	total = 0;
	$('.acc_input').each(function(){
		total += !isNaN(parseFloat(removeCommas($(this).val()))) ? parseFloat(removeCommas($(this).val())) : 0;
	});

	$("#sum_cash").val(total.toFixed(2));
	format_the_number_decimal(document.getElementById("sum_cash"));
}

function removeCommas(str) {
	return(str.replace(/,/g,''));
}

function matchStart(params, data) {
	// If there are no search terms, return all of the data
	if ($.trim(params.term) === '') {
	  return data;
	}

	// Display only term macth with text begin chars
	if(data.text.indexOf(params.term) == 0) {
		return data;
	}

	// Return `null` if the term should not be displayed
	return null;
}
