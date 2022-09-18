var base_url = $('#base_url').attr('class');

function check_empty(){

	var start_date = $('#start_date').val();
	var end_date = $('#end_date').val();
	$.ajax({
		url: base_url+'/report_member_data/check_report_member_in_out',	
		// url: '/spkt/report_member_data/check_report_member_in_out',	
		method:"post",
		data:{ 
			start_date: start_date, 
			end_date: end_date
		},
		dataType:"text",
		success:function(data){
			if(data == 'success'){
				$('#form1').submit();
			}else{
				$('#alertNotFindModal').appendTo("body").modal('show');
			}
		}
	});
}
$( document ).ready(function() {
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
});
