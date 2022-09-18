function change_maintenance_radio(){
	if($('#maintenance_1').is(':checked')){
		$('#maintenance_fee_type_1').show();
		$('#maintenance_fee_type_2').hide();
	}else if($('#maintenance_2').is(':checked')){
		$('#maintenance_fee_type_1').hide();
		$('#maintenance_fee_type_2').show();
	}
}
function change_pay_radio(){
	if($('#pay_1').is(':checked')){
		$('#pay_type_1').show();
		$('#pay_type_2').hide();
	}else if($('#pay_2').is(':checked')){
		$('#pay_type_1').hide();
		$('#pay_type_2').show();
	}
}
function sum_maintenance_fee(type){
	var sum_amount = 0;
	if(type == '1'){
		$('.maintenance_fee_amount').each(function(){
			if($(this).val()!=''){
				sum_amount = parseFloat(sum_amount) + parseFloat($(this).val());
			}
		});
		$('#maintenance_fee').val(sum_amount);
	}else{
		$('.maintenance_fee_amount_2').each(function(){
			if($(this).val()!=''){
				sum_amount = parseFloat(sum_amount) + parseFloat($(this).val());
			}
		});
		$('#maintenance_fee_2').val(sum_amount);
	}
}
function submit_form(){
	$('#form1').submit();
}
function go_back(cremation_id){
	document.location.href = base_url+'setting_cremation_data/cremation_data_detail?cremation_id='+cremation_id;
}
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
	$('.maintenance_fee_amount').keyup(function(){
		sum_maintenance_fee('1');
	});
	$('.maintenance_fee_amount_2').keyup(function(){
		sum_maintenance_fee('2');
	});
	change_maintenance_radio();
	change_pay_radio();
});