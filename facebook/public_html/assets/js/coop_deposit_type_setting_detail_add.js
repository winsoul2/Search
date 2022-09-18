var base_url = $('#base_url').attr('class');
$( document ).ready(function() {
    $("#start_date").datepicker({
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
	
    $("#end_date").datepicker({
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
    
    $("#pay_date1").datepicker({
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
	
    $("#pay_date2").datepicker({
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
	
	change_interest_radio();
	change_pay_radio();
	change_staus_interest();
	change_fee_radio();
	change_staus_withdraw();
	change_staus_maturity();
	change_type_interest();
	change_staus_open_min();
	change_staus_balance_min();
	change_staus_withdraw_min();
	change_is_deposit_num();
});

function submit_form(){
	$('#form1').submit();
}
function go_back(type_id){
	document.location.href = base_url+'setting_deposit_data/coop_deposit_type_setting_detail?type_id='+type_id;
}

function change_interest_radio(){
	if($('#condition_interest_1').is(':checked')){
		$('#show_condition_interest_1').show();
		$('#show_condition_interest_2').hide();
		$('#show_condition_interest_3').hide();
	}else if($('#condition_interest_2').is(':checked')){
		$('#show_condition_interest_1').hide();
		$('#show_condition_interest_2').show();
		$('#show_condition_interest_3').hide();
	}else if($('#condition_interest_3').is(':checked')){
		$('#show_condition_interest_1').hide();
		$('#show_condition_interest_2').hide();
		$('#show_condition_interest_3').show();
	}
}

function change_pay_radio(){
	if($('#pay_interest_1').is(':checked')){
		$('#pay_date1').attr("disabled", true);
		$('#pay_date2').attr("disabled", true);
		$('#num_month_maturity').attr("disabled", true);
	}else if($('#pay_interest_2').is(':checked')){
		$('#pay_date1').attr("disabled", true);
		$('#pay_date2').attr("disabled", true);
		$('#num_month_maturity').attr("disabled", true);
	}else if($('#pay_interest_3').is(':checked')){
		$('#pay_date1').attr("disabled", false);
		$('#pay_date2').attr("disabled", false);
		$('#num_month_maturity').attr("disabled", true);
	}else if($('#pay_interest_4').is(':checked')){
		$('#pay_date1').attr("disabled", true);
		$('#pay_date2').attr("disabled", true);
		$('#num_month_maturity').attr("disabled", false);	
	}else{
		$('#pay_date1').attr("disabled", true);
		$('#pay_date2').attr("disabled", true);
		$('#num_month_maturity').attr("disabled", true);
	}		
}

function change_staus_interest(){
	if($('#staus_interest').is(':checked')){	
		$('#num_month_no_interest').attr("disabled", false);
		$('#amount_min_no_interest').attr("disabled", false);
	}else{
		$('#num_month_no_interest').attr("disabled", true);
		$('#amount_min_no_interest').attr("disabled", true);
	}
}

function change_fee_radio(){
	if($('#type_fee_1').is(':checked')){
		$('#percent_fee').attr("disabled", true);
		$('#num_month_before').attr("disabled", true);
		$('#percent_depositor').attr("disabled", true);
	}else if($('#type_fee_2').is(':checked')){
		$('#percent_fee').attr("disabled", false);
		$('#num_month_before').attr("disabled", true);
		$('#percent_depositor').attr("disabled", true);
	}else if($('#type_fee_3').is(':checked')){
		$('#percent_fee').attr("disabled", true);
		$('#num_month_before').attr("disabled", false);
		$('#percent_depositor').attr("disabled", false);
	}else{
		$('#percent_fee').attr("disabled", true);
		$('#num_month_before').attr("disabled", true);
		$('#percent_depositor').attr("disabled", true);
	}	
}	

function change_staus_withdraw(){
	if($('#staus_withdraw').is(':checked')){	
		$('#withdraw_num').attr("disabled", false);
		$('#withdraw_num_unit').attr("disabled", false);
		$('#withdraw_num_interest').attr("disabled", false);
		$('#withdraw_percent_interest').attr("disabled", false);
		$('#withdraw_percent_min').attr("disabled", false);
	}else{
		$('#withdraw_num').attr("disabled", true);
		$('#withdraw_num_unit').attr("disabled", true);
		$('#withdraw_num_interest').attr("disabled", true);
		$('#withdraw_percent_interest').attr("disabled", true);
		$('#withdraw_percent_min').attr("disabled", true);
	}
}

function change_staus_maturity(){
	if($('#staus_maturity').is(':checked')){	
		$('#maturity_num_year').attr("disabled", false);
	}else{
		$('#maturity_num_year').attr("disabled", true);
	}
}

function change_type_interest(){
	if($('#type_interest_3').is(':checked')){	
		$('#num_month_no_interest').attr("disabled", false);
		$('#amount_min_no_interest').attr("disabled", true);
	}else if($('#type_interest_2').is(':checked')){
		$('#num_month_no_interest').attr("disabled", true);
		$('#amount_min_no_interest').attr("disabled", true);
	}else if($('#type_interest_1').is(':checked')){
		$('#num_month_no_interest').attr("disabled", true);
		$('#amount_min_no_interest').attr("disabled", true);
	}else if($('#type_interest_4').is(':checked')){
		$('#num_month_no_interest').attr("disabled", true);
		$('#amount_min_no_interest').attr("disabled", true);
	}else if($('#type_interest_5').is(':checked')){
		$('#num_month_no_interest').attr("disabled", true);
		$('#amount_min_no_interest').attr("disabled", false);
	}
}

function change_staus_open_min(){
	if($('#is_open_min').is(':checked')){	
		$('#open_min').attr("disabled", false);
	}else{
		$('#open_min').attr("disabled", true);
	}
}

function change_staus_balance_min(){
	if($('#is_balance_min').is(':checked')){	
		$('#balance_min').attr("disabled", false);
	}else{
		$('#balance_min').attr("disabled", true);
	}
}

function change_staus_withdraw_min(){
	if($('#is_withdraw_min').is(':checked')){	
		$('#withdraw_min').attr("disabled", false);
	}else{
		$('#withdraw_min').attr("disabled", true);
	}
}

function change_is_deposit_num(){
	if($('#is_deposit_num').is(':checked')){	
		$('#deposit_num_type').attr("disabled", false);
		$('#deposit_num').attr("disabled", false);
	}else{
		$('#deposit_num_type').attr("disabled", true);
		$('#deposit_num').attr("disabled", true);
	}
}
var is_p = true;
$(document).on("change", "input[name=permission_type]", function(){
	if(is_p){
		is_p = false;
		if($(this).val() === "3"){
			$("#hold_withdraw_month").prop("disabled", false);
		}else{
			$("#hold_withdraw_month").prop("disabled", true);
		}
		is_p = true;
	}
});
