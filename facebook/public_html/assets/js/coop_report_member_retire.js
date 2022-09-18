var base_url = $('#base_url').attr('class');

function check_empty(type){
	var report_date = '';
	var month = '';
	var year = '';
	if(type == '1'){
		report_date = $('#report_date').val();
	}else if(type == '2'){
		month = $('#report_month').val();
		year = $('#report_year').val();
	}else{
		year = $('#report_only_year').val();
	}
	$.ajax({
		url: base_url+'/report_member_data/check_report_member_retire',	
		 method:"post",
		 data:{ 
			 report_date: report_date, 
			 month: month,
			 year: year
		 },
		 dataType:"text",
		 success:function(data){
			//console.log(data);
			if(data == 'success'){
				$('#form'+type).submit();
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
