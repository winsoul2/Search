var base_url = $('#base_url').attr('class');
function del_coop_member_data(id){	
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
				url: base_url+'/setting_member_data/del_coop_member_data',
				method: 'POST',
				data: {
					'table': 'coop_prename',
					'id': id,
					'field': 'prename_id'
				},
				success: function(msg){
				  // console.log(msg); return false;
					if(msg == 1){
					  document.location.href = base_url+'setting_member_data/coop_prename';
					}else{

					}
				}
			});
		} else {
			
		}
	});
	
}

function check_form(){
	var text_alert = '';
	if($.trim($('#prename_full').val())== ''){
		text_alert += ' - ชื่อคำนำหน้า\n';
	}
	if($.trim($('#prename_short').val())== ''){
		text_alert += ' - คำย่อ\n';
	}
	if(!$('#sex1').is(':checked') && !$('#sex2').is(':checked')){
		text_alert += ' - เพศ\n';
	}
	
	if(text_alert != ''){
		swal('กรุณากรอกข้อมูลต่อไปนี้',text_alert,'warning');
	}else{
		$('#form_save').submit();
	}
}