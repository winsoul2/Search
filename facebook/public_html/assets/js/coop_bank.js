var base_url = $('#base_url').attr('class');
$( document ).ready(function() {
 $(".fancybox")
    .fancybox({
        openEffect  : 'none',
        closeEffect : 'none',
        nextEffect  : 'none',
        prevEffect  : 'none',
        padding     : 0,
        margin      : [20, 60, 20, 60] // Increase left/right margin
    });
});

function check_form(){
	var text_alert = '';
	if($.trim($('#bank_id').val())== ''){
		text_alert += ' - รหัสธนาคาร\n';
	}
	if($.trim($('#bank_name').val())== ''){
		text_alert += ' - ชื่อธนาคาร\n';
	}
	if($.trim($('#bank_code').val())== ''){
		text_alert += ' - ตัวย่อ\n';
	}
	
	if(text_alert != ''){
		swal('กรุณากรอกข้อมูลต่อไปนี้',text_alert,'warning');
	}else{
		$('#form_save').submit();
	}
}


function del_coop_basic_data(id){	
	swal({
        title: "คุณต้องการที่จะลบ",
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
				url: base_url+'/setting_basic_data/del_coop_basic_data',
				method: 'POST',
				data: {
					'table': 'coop_bank',
					'table_sub': 'coop_bank_branch',
					'id': id,
					'field': 'bank_id'
				},
				success: function(msg){
				  // console.log(msg); return false;
					if(msg == 1){
					  document.location.href = base_url+'setting_basic_data/coop_bank';
					}else{

					}
				}
			});
        } else {
			
        }
    });
	
}