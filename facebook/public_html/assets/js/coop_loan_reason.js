var base_url = $('#base_url').attr('class');

function submit_form(){
	if($('#loan_reason').val()==''){
		swal('กรุณากรอกเหตุผลการกู้เงิน');
	}else{
		$('#form1').submit();
	}
}

function edit_loan_reason(loan_reason_id,loan_reason){
	$('#loan_reason_id').val(loan_reason_id);
	$('#loan_reason').val(loan_reason);
}

function del_coop_credit_data(id){	
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
				url: base_url+'/setting_credit_data/del_coop_reason_data',
				method: 'POST',
				data: {
					'table': 'coop_loan_reason',
					'id': id,
					'field': 'loan_reason_id'
				},
				success: function(msg){
				  //console.log(msg); return false;
					if(msg == 1){
					  document.location.href = base_url+'setting_credit_data/coop_loan_reason';
					}else{

					}
				}
			});
        } else {
			
        }
    });
	
}

