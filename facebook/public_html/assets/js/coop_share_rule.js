var base_url = $('#base_url').attr('class');
$( document ).ready(function() {
	$("#various1").fancybox({
	  'titlePosition'		: 'inside',
	  'transitionIn'		: 'none',
	  'transitionOut'		: 'none',
	});
	
	$("#filter").change(function() {
		document.location.href = base_url+'setting_share_data/coop_share_rule?filter='+ $(this).val();
	});

});
	
function check_form(){
	var text_alert = '';
	if($.trim($(mem_type_id).val())== ''){
		text_alert += ' - ประเภทสมาชิก\n';
	}
	if($.trim($('#salary_rule').val())== ''){
		text_alert += ' - เงินเดือนมากกว่า\n';
	}
	if($.trim($('#share_salary').val())== ''){
		text_alert += ' - หุ้นรายเดือน\n';
	}
	
	if(text_alert != ''){
		swal('กรุณากรอกข้อมูลต่อไปนี้',text_alert,'warning');
	}else{
		$('#form_save').submit();
	}
}	

function check_form_change(){
	var text_alert = '';
	if($.trim($('#share_cost').val())== ''){
		text_alert += ' - มูลค่าหุ้นใหม่\n';
	}
	
	if(text_alert != ''){
		swal('กรุณากรอกข้อมูลต่อไปนี้',text_alert,'warning');
	}else{
		$('#form_change').submit();
	}
}

function del_coop_share_data(id){	
	swal({
		title: "ท่านต้องการลบข้อมูลนี้ใช่หรือไม่ ! ",
		text: "",
		type: "warning",
		showCancelButton: true,
		confirmButtonColor: '#DD6B55',
		confirmButtonText: 'ลบ',
		cancelButtonText: "ยกเลิก",
		closeOnConfirm: false,
		closeOnCancel: true
	},
	function(isConfirm) {
		if (isConfirm) {			
			$.ajax({
				url: base_url+'/setting_share_data/del_coop_share_data',
				method: 'POST',
				data: {
					'table': 'coop_share_rule',
					'id': id,
					'field': 'share_rule_id'
				},
				success: function(msg){
				  // console.log(msg); return false;
					if(msg == 1){
					  document.location.href = base_url+'setting_share_data/coop_share_rule';
					}else{

					}
				}
			});
		} else {
			
		}
	});
	
}