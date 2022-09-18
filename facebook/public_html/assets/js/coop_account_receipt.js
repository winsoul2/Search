var base_url = $('#base_url').attr('class');

function check_form(){
	var text_alert = '';
	if($.trim($('#account_chart_id').val())== ''){
		text_alert += ' - รหัสผังบัญชี\n';
	}
	if($.trim($('#account_list').val())== ''){
		text_alert += ' - รายการชำระเงิน\n';
	}
	if($.trim($('#amount').val())== ''){
		text_alert += ' - จำนวนเงิน\n';
	}
	
	if(text_alert != ''){
		swal('กรุณากรอกข้อมูลต่อไปนี้',text_alert,'warning');
	}else{
		$('#form_save').submit();
	}
	
}

function del_coop_account_data(id){	
	swal({
        title: "ท่านต้องการลบข้อมูลนี้ใช่หรือไม่",
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
				url: base_url+'/setting_account_data2/del_coop_account_receipt_data',
				method: 'POST',
				data: {
					'table': 'coop_account_list',
					'id': id,
					'field': 'account_id'
				},
				success: function(msg){
				  // console.log(msg); return false;
					if(msg == 1){
					  document.location.href = base_url+'setting_account_data2/coop_account_receipt';
					}else{

					}
				}
			});
        } else {
			
        }
    });
	
}

$("#various1").fancybox({
  'titlePosition'		: 'inside',
  'transitionIn'		: 'none',
  'transitionOut'		: 'none',
});
