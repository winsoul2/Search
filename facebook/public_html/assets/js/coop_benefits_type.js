var base_url = $('#base_url').attr('class');
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
		//startDate: '+1d', //ใสวันที่เริ่มต้นแสดงในปฎิทิน
		autoclose: true,
    });
});	

function add_type(){
	$('#benefits_type_modal').modal('show');
}

function check_form(){
	var start_date = $("#start_date").val();
	var id = $("#id").val();
	var detail_id = $("#detail_id").val();
	var sp_condition = $('input[name=sp_condition]:checked').val()
	if (sp_condition == "scholarship" && !$("#scholarship_period_date_start").val()) {
		swal("กรุณาเลือกวันที่เริ่มต้น");
	} else {
		$.ajax({
			url: base_url+'/setting_benefits_data/check_date_detail',
			method: 'POST',
			data: {
				'start_date': start_date,
				'id': id,
				'detail_id': detail_id
			},
			success: function(msg){
				if(msg == 1){
					$.ajax({
						url: base_url+'/setting_benefits_data/check_request_detail_exists',
						method: 'POST',
						data: {
							'detail_id': detail_id
						},
						success: function(result){
							if(result == 0) {
								$('#form_save').submit();
							} else {
								swal({
									title: "สวัสดิการณ์นี้มีการยืนคำร้องแล้ว ต้องการแก้ไขใช่หรือไม่",
									text: "",
									type: "warning",
									showCancelButton: true,
									confirmButtonText: 'ยืนยัน',
									cancelButtonText: "ยกเลิก",
									closeOnConfirm: false,
									closeOnCancel: true
								},
								function(isConfirm) {
									if (isConfirm) {
										$('#form_save').submit();
									}
								});
							}
						}
					});
				}else{
					swal("ไม่สามารถเลือกวันที่นี้ได้\nเนื่องจากมีวันที่นี้แล้ว");
				}
			}
		});
	}
}



function save_type(){
	$('#form1').submit();
}

function edit_type(benefits_id,benefits_name,start_date){
	$('#benefits_id').val(benefits_id);
	$('#benefits_name').val(benefits_name);
	//$('#start_date').val(start_date);
	$('#benefits_type_modal').modal('show');
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
				url: base_url+'/setting_benefits_data/check_benefits_type_detail',
				method: 'POST',
				data: {
					'id': id
				},
				success: function(msg){
				   //console.log(msg); return false;
					if(msg == 1){					  
					  $.ajax({
							url: base_url+'/setting_benefits_data/del_coop_data',
							method: 'POST',
							data: {
								'table': 'coop_benefits_type',
								'id': id,
								'field': 'benefits_id'
							},
							success: function(msg){
							  // console.log(msg); return false;
								if(msg == 1){
								  document.location.href = base_url+'setting_benefits_data/benefits_type';
								}else{

								}
							}
						});
					}else{
						swal("ไม่สามารถลบสวัสดิการนี้ได้ \nเนื่องจากมีรายละเอียดอยู่แล้วในสวัสดิการนี้");
					}
				}
			});
        } else {
			
        }
    });
}

function del_coop_detail_data(id,id_detail){	
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
				url: base_url+'/setting_benefits_data/del_coop_data',
				method: 'POST',
				data: {
					'table': 'coop_benefits_type_detail',
					'id': id_detail,
					'field': 'id'
				},
				success: function(msg){
				  // console.log(msg); return false;
					if(msg == 1){
					  document.location.href = base_url+'setting_benefits_data/benefits_type?act=detail&id='+id;
					}else{

					}
				}
			});
        } else {
			
        }
    });
}