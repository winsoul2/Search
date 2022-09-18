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
					'table': 'coop_facility_type',
					'id': id,
					'field': 'facility_type_id'
				},
				success: function(msg){
				   //console.log(msg); return false;
					if(msg == 1){
					  document.location.href = base_url+'setting_facility_data/facility_type';
					}else{

					}
				}
			});
        } else {
			
        }
    });
	
}