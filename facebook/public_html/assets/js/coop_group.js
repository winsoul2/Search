var base_url = $('#base_url').attr('class');

$( document ).ready(function() {
	$('#department_modal').on('hide.bs.modal', function () {
		$('.form-control').val('');
		$('#department_parent').html('<option value="">เลือกอำเภอ</option>');
	});
});

function add_group(mem_group_type){
	$('#mem_group_type').val(mem_group_type);
	$('#department_modal').modal('show');
	if(mem_group_type=='3'){
		$('#group_table').hide();
		$('#department_table').hide();
		$('#choose_group').show();
		$('#choose_department').show();
		$('#title_1').html('เพิ่มข้อมูลหน่วยงานย่อย');
		$('#title_2').html('รหัสหน่วยงานย่อย');
		$('#title_3').html('ชื่อหน่วยงานย่อย');
	}else if(mem_group_type=='1'){
		$('#group_table').show();
		$('#department_table').hide();
		$('#choose_group').hide();
		$('#choose_department').hide();
		$('#title_1').html('เพิ่มข้อมูลหน่วยงานหลัก');
		$('#title_2').html('รหัสหน่วยงานหลัก');
		$('#title_3').html('ชื่อหน่วยงานหลัก');
	}else if(mem_group_type=='2'){ 
		$('#group_table').hide();
		$('#department_table').show();
		$('#choose_group').show();
		$('#choose_department').hide();
		$('#title_1').html('เพิ่มข้อมูลอำเภอ');
		$('#title_2').html('รหัสอำเภอ');
		$('#title_3').html('ชื่ออำเภอ');
	}
}

function edit_mem_group(id, mem_group_id, mem_group_name, mem_group_full_name, mem_group_type, mem_group_parent_id){
	$('#id').val(id);
	$('#mem_group_id').val(mem_group_id);
	$('#mem_group_name').val(mem_group_name);
	$('#mem_group_full_name').val(mem_group_full_name);
	if(mem_group_type=='3'){
		$('#group_table').hide();
		$('#department_table').hide();
		$('#choose_group').show();
		$('#choose_department').show();
		$('#title_1').html('แก้ไขข้อมูลหน่วยงานย่อย');
		$('#title_2').html('รหัสหน่วยงานย่อย');
		$('#title_3').html('ชื่อหน่วยงานย่อย');
		$.ajax({
		method: 'POST',
		url: base_url+'/setting_member_data/get_group_parent',
		data: { id : mem_group_parent_id },
		success: function(result){
			$('#group_parent').val(result);
			change_group(mem_group_parent_id);
		}
		});
	}else if(mem_group_type=='1'){
		$('#group_table').show();
		$('#department_table').hide();
		$('#choose_group').hide();
		$('#choose_department').hide();
		$('#title_1').html('แก้ไขข้อมูลหน่วยงานหลัก');
		$('#title_2').html('รหัสหน่วยงานหลัก');
		$('#title_3').html('ชื่อหน่วยงานหลัก');
	}else if(mem_group_type=='2'){ 
		$('#group_table').hide();
		$('#department_table').show();
		$('#choose_group').show();
		$('#choose_department').hide();
		$('#group_parent').val(mem_group_parent_id);
		$('#title_1').html('แก้ไขข้อมูลอำเภอ');
		$('#title_2').html('รหัสอำเภอ');
		$('#title_3').html('ชื่ออำเภอ');
	}
	$('#department_modal').modal('show');
}

function save_mem_group(){
	var text_alert = '';
	var text_suffix = '';
	if($('#mem_group_type').val() == '1'){
		text_suffix = 'หน่วยงานหลัก';
	}else if($('#mem_group_type').val() == '2'){
		text_suffix = 'อำเภอ';
		if($('#group_parent').val()==''){
			 text_alert += '- เลือกหน่วยงานหลัก\n';
		}
	}else{
		if($('#group_parent').val()==''){
			 text_alert += '- เลือกหน่วยงานหลัก\n';
		}
		if($('#department_parent').val()==''){
			 text_alert += '- เลือกอำเภอ\n';
		}
		text_suffix = 'หน่วยงานย่อย';
	}
	
	if($('#mem_group_id').val()==''){
		 text_alert += '- รหัส'+text_suffix+'\n';
	}
	if($('#mem_group_name').val()==''){
		 text_alert += '- ชื่อ'+text_suffix+'\n';
	}
	if(text_alert != ''){
		swal('กรุณากรอกข้อมูลต่อไปนี้',text_alert,'warning');
	}else{
		$('#form1').submit();
	}
}
 
function change_group(default_id=''){
	var group_id = $('#group_parent').val();
	$.ajax({
		method: 'POST',
		url: base_url+'/setting_member_data/get_group_child',
		data: { group_id : group_id },
		success: function(result){
				$('#department_parent_space').html(result);
				$('#department_parent').val(default_id);
		}
	});
}
	
function delete_mem_group(id){
	$.ajax({
		method: 'POST',
		url: base_url+'/setting_member_data/check_delete_mem_group',
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
							url: base_url+'/setting_member_data/save_mem_group',
							data: { id : id , delete_action : 'delete_action'},
							success: function(result){
								//console.log(result);
								if(result=='error'){
									swal('เกิดข้อผิดพลาด', 'ไม่สามารถบันทึกข้อมูลได้', 'error');
								}else{
									document.location.href = base_url+'setting_member_data/coop_group';
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
