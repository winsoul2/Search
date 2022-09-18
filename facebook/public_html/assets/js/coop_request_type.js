var base_url = $('#base_url').attr('class');

function check_form(){
	$('#form_save').submit();
}


function del_coop_data(id){	
	swal({
        title: "ท่านต้องการลบข้อมูลใช่หรือไม่",
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
				url: base_url+'/setting_facility_data/del_coop_data',
				method: 'POST',
				data: {
					'table': 'coop_request_type',
					'id': id,
					'field': 'request_type_id'
				},
				success: function(msg){
				   //console.log(msg); return false;
					if(msg == 1){
					  document.location.href = base_url+'setting_facility_data/request_type';
					}else{

					}
				}
			});
        } else {
			
        }
    });
	
}