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
		startDate: '+1d',
		autoclose: true,
    });
	
	$("#filter").change(function() {
		document.location.href = base_url+'setting_deposit_data/coop_deposit_type_setting?filter='+ $(this).val();
	});

});	
	
function add_type(){
	$('#deposit_type_modal').modal('show');
}

function save_type(){
	$('#form1').submit();
}

function check_form(){
	$('#form_save').submit();
}


function del_interest(id){	
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
				url: base_url+'/setting_deposit_data/del_coop_deposit_type',
				method: 'POST',
				data: {
					'table': 'coop_interest',
					'id': id,
					'field': 'interest_id'
				},
				success: function(msg){
				  // console.log(msg); return false;
					if(msg == 1){
					  document.location.href = base_url+'setting_deposit_data/coop_deposit_type_setting';
					}else{

					}
				}
			});
        } else {
			
        }
    });
}

function edit_type(id,type_code,type_name,type_prefix,format_account_number,unique_account){
	$('#type_id').val(id);
	$('#type_code').val(type_code);
	$('#type_name').val(type_name);
	$('#type_prefix').val(type_prefix);
	$('#format_account_number').val(format_account_number);
	if(unique_account == '1'){
		$('#unique_account').attr("checked", true);
	}else{
		$('#unique_account').attr("checked", false);
	}
	$('#deposit_type_modal').modal('show');
}

function del_type(id){	
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
				url: base_url+'/setting_deposit_data/check_use_type',
				method: 'POST',
				data: {
					'id': id
				},
				success: function(msg){
				   //console.log(msg); return false;
					if(msg == 1){					  
					  $.ajax({
							url: base_url+'/setting_deposit_data/del_coop_deposit_type',
							method: 'POST',
							data: {
								'table': 'coop_deposit_type_setting',
								'id': id,
								'field': 'type_id'
							},
							success: function(msg){
								if(msg == 1){
								  document.location.href = base_url+'setting_deposit_data/coop_deposit_type_setting';
								}else{

								}
							}
						});			
					}else{
						swal("ไม่สามารถลบประเภทนี้ได้ \nเนื่องจากมีรายการอัตราดอกเบี้ยอยู่แล้วในประเภทนี้");
					}
				}
			});		
			
			
        } else {
			
        }
    });
}