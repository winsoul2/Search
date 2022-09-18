var base_url = $('#base_url').attr('class');

$( document ).ready(function() {
	$('#department_modal').on('hide.bs.modal', function () {
		$('.form-control').val('');
		$('#parent_group').html('<option value="">เลือกกลุ่มย่อย</option>');
	});
});

function add_group(facility_group_type){
	$('#facility_group_type').val(facility_group_type);
	$('#department_modal').modal('show');
	if(facility_group_type=='3'){
		$('#group_table').hide();
		$('#department_table').hide();
		$('#choose_group').show();
		$('#choose_department').show();
		$('#title_1').html('เพิ่มข้อมูลกลุ่ม');
		$('#title_2').html('รหัสกลุ่ม');
		$('#title_3').html('ชื่อกลุ่ม');
	}else if(facility_group_type=='1'){
		$('#group_table').show();
		$('#department_table').hide();
		$('#choose_group').hide();
		$('#choose_department').hide();
		$('#title_1').html('เพิ่มข้อมูลกลุ่มหลัก');
		$('#title_2').html('รหัสกลุ่มหลัก');
		$('#title_3').html('ชื่อกลุ่มหลัก');
	}else if(facility_group_type=='2'){ 
		$('#group_table').hide();
		$('#department_table').show();
		$('#choose_group').show();
		$('#choose_department').hide();
		$('#title_1').html('เพิ่มข้อมูลกลุ่มย่อย');
		$('#title_2').html('รหัสกลุ่มย่อย');
		$('#title_3').html('ชื่อกลุ่มย่อย');
	}
}

function edit_facility_group(facility_group_id, facility_group_code, facility_group_name, facility_group_type, facility_group_parent_id){
	$('#facility_group_id').val(facility_group_id);
	$('#facility_group_code').val(facility_group_code);
	$('#facility_group_name').val(facility_group_name);
	if(facility_group_type=='3'){
		$('#group_table').hide();
		$('#department_table').hide();
		$('#choose_group').show();
		$('#choose_department').show();
		$('#title_1').html('แก้ไขข้อมูลกลุ่ม');
		$('#title_2').html('รหัสกลุ่ม');
		$('#title_3').html('ชื่อกลุ่ม');
		$.ajax({
		method: 'POST',
		url: base_url+'/setting_facility_data/get_group_parent',
		data: { id : facility_group_parent_id },
		success: function(result){
			$('#main_group').val(result);
			change_group(facility_group_parent_id);
		}
		});
	}else if(facility_group_type=='1'){
		$('#group_table').show();
		$('#department_table').hide();
		$('#choose_group').hide();
		$('#choose_department').hide();
		$('#title_1').html('แก้ไขข้อมูลกลุ่มหลัก');
		$('#title_2').html('รหัสกลุ่มหลัก');
		$('#title_3').html('ชื่อกลุ่มหลัก');
	}else if(facility_group_type=='2'){ 
		$('#group_table').hide();
		$('#department_table').show();
		$('#choose_group').show();
		$('#choose_department').hide();
		$('#main_group').val(facility_group_parent_id);
		$('#title_1').html('แก้ไขข้อมูลกลุ่มย่อย');
		$('#title_2').html('รหัสกลุ่มย่อย');
		$('#title_3').html('ชื่อกลุ่มย่อย');
	}
	$('#department_modal').modal('show');
}

function save_facility_group(){
	var text_alert = '';
	var text_suffix = '';
	if($('#facility_group_type').val() == '1'){
		text_suffix = 'กลุ่มหลัก';
	}else if($('#facility_group_type').val() == '2'){
		text_suffix = 'กลุ่มย่อย';
		if($('#main_group').val()==''){
			 text_alert += '- เลือกกลุ่มหลัก\n';
		}
	}else{
		if($('#main_group').val()==''){
			 text_alert += '- เลือกกลุ่มหลัก\n';
		}
		if($('#parent_group').val()==''){
			 text_alert += '- เลือกกลุ่มย่อย\n';
		}
		text_suffix = 'กลุ่ม';
	}
	
	if($('#facility_group_code').val()==''){
		 text_alert += '- รหัส'+text_suffix+'\n';
	}
	if($('#facility_group_name').val()==''){
		 text_alert += '- ชื่อ'+text_suffix+'\n';
	}
	if(text_alert != ''){
		swal('กรุณากรอกข้อมูลต่อไปนี้',text_alert,'warning');
	}else{
		$('#form1').submit();
	}
}
 
function change_group(default_id=''){
	var group_id = $('#main_group').val();
	$('#main_group_code').val($('#main_group :selected').attr('group_code'));
	$.ajax({
		method: 'POST',
		url: base_url+'/setting_facility_data/get_group_child',
		data: { group_id : group_id },
		success: function(result){
				$('#parent_group_space').html(result);
				$('#parent_group').val(default_id);
				change_parent_group();
		}
	});
}
function change_parent_group(default_id=''){
	$('#parent_group_code').val($('#parent_group :selected').attr('group_code'));
}
	
function delete_facility_group(id){
	$.ajax({
		method: 'POST',
		url: base_url+'/setting_facility_data/check_delete_facility_group',
		data: { id : id },
		success: function(result2){
			if(result2=='success'){
				swal({
				title: "ท่านต้องการลบข้อมูลใช่หรือไม่?",
				text: "",
				type: "warning",
				showCancelButton: true,
				confirmButtonColor: '#DD6B55',
				confirmButtonText: 'ยืนยัน',
				cancelButtonText: "ยกเลิก",
				closeOnConfirm: true,
				closeOnCancel: true
				},
				function(isConfirm) {
					if (isConfirm) {
						$.ajax({
							method: 'POST',
							url: base_url+'/setting_facility_data/delete_facility_group',
							data: { id : id , delete_action : 'delete_action'},
							success: function(result){
								//console.log(result);
								if(result=='error'){
									swal('เกิดข้อผิดพลาด', 'ไม่สามารถบันทึกข้อมูลได้', 'error');
								}else{
									document.location.href = base_url+'setting_facility_data/facility_group';
								}
							}
						});
					} else {
						
					}
				});
			}else{
				swal('ไม่สามารถลบข้อมูลได้' , 'เนื่องจากมีข้อมูลบางอย่างที่มีความสัมพันธ์กันอยู่' , 'warning');
			}
		}
	});
}
