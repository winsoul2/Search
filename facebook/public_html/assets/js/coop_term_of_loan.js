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
		startDate: '+0d',
		autoclose: true,
    });

});
function check_form(){
	var text_alert = '';
	if($.trim($('#type_name').val())== ''){
		text_alert += ' - ประเภทการกู้เงิน\n';
	}
	if($.trim($('#interest_rate').val())== ''){
		text_alert += ' - อัตราดอกเบี้ย\n';
	}
	if($.trim($('#prefix_code').val())== ''){
		text_alert += ' - รหัสนำหน้าสัญญา\n';
	}
	
	if(text_alert != ''){
		swal('กรุณากรอกข้อมูลต่อไปนี้',text_alert,'warning');
	}else{
		$('#form_save').submit();
	}
	
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
				url: base_url+'/setting_credit_data/del_coop_credit_data',
				method: 'POST',
				data: {
					'table': 'coop_term_of_loan',
					'id': id,
					'field': 'id'
				},
				success: function(msg){
				  //console.log(msg); return false;
					if(msg == 1){
					  document.location.href = base_url+'setting_credit_data/coop_term_of_loan';
					}else{

					}
				}
			});
        } else {
			
        }
    });
	
}
function add_type(){
	$('#loan_type_modal').modal('show');
}
function save_type(){
	$('#form1').submit();
}
function edit_type(id,type_name,loan_type_status){
	$('#loan_type_id').val(id);
	$('#loan_type').val(type_name);
	if(loan_type_status == '1'){
		$('#loan_type_status').attr('checked',true);
	}else{
		$('#loan_type_status').removeAttr('checked');
	}
}

function del_type(id){	
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
				url: base_url+'/setting_credit_data/check_use_type',
				method: 'POST',
				data: {
					'id': id
				},
				success: function(msg){
				   //console.log(msg); return false;
					if(msg == 'success'){		
					  document.location.href = base_url+'setting_credit_data/del_loan_type?id='+id;		
					}else{
						swal("ไม่สามารถลบประเภทนี้ได้ \nเนื่องจากได้ตั้งค่าชื่อสินเชื่อสำหรับประเภทนี้แล้ว");
					}
				}
			});		
			
			
        } else {
			
        }
    });
}

function add_loan_name(){
	$('#loan_name_modal').modal('show');
}
function save_loan_name(){
	$('#form2').submit();
}
function edit_loan_name(loan_name_id,loan_type_id, loan_group_id,loan_name,loan_name_description,loan_name_status){
	$('#choose_loan_type_id').val(loan_type_id);
	$('#loan_name_id').val(loan_name_id);
	$('#loan_name').val(loan_name);
	$('#loan_name_description').val(loan_name_description);
	$('#choose_loan_group_id').val(loan_group_id);

	if(loan_name_status == '1'){
		$('#loan_name_status').attr('checked',true);
	}else{
		$('#loan_name_status').removeAttr('checked');
	}
}
function del_loan_name(id){	
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
				url: base_url+'/setting_credit_data/check_use_name',
				method: 'POST',
				data: {
					'id': id
				},
				success: function(msg){
				   //console.log(msg); return false;
					if(msg == 'success'){		
					  document.location.href = base_url+'setting_credit_data/del_loan_name?id='+id;		
					}else{
						swal("ไม่สามารถลบประเภทนี้ได้ \nเนื่องจากได้ตั้งค่าเงื่อนไขการกู้เงินสำหรับชื่อสินเชื่อนี้แล้ว");
					}
				}
			});		
			
			
        } else {
			
        }
    });
}

function change_type(){
	$.ajax({
		url: base_url+'setting_credit_data/change_loan_type',
		method: 'POST',
		data: {
			'type_id': $('#type_id').val()
		},
		success: function(msg){
		   $('#loan_name_id').html(msg);
		}
	});		
	$('#type_name').val($('#type_id :selected').text());
}
function change_loan_name(){
	$('#loan_name').val($('#loan_name_id :selected').text());
}
$( document ).ready(function() {
	$("#various1").fancybox({
	  'titlePosition'		: 'inside',
	  'transitionIn'		: 'none',
	  'transitionOut'		: 'none',
	}); 


	//class for check input number
	$('.check_number').on('input', function () {
		this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');
	});
});
function change_type_id(){
	var type_id = $('#type_id').val();
	document.location.href = '?type_id='+type_id;
}

function change_close_loan(){
	$.ajax({
		url: base_url+'setting_credit_data/change_loan_type',
		method: 'POST',
		data: {
			'type_id': $('#close_loan_type_id').val()
		},
		success: function(msg){
		   $('#close_loan_name_id').html(msg);
		}
	});		
	$('#close_type_name').val($('#close_loan_type_id :selected').text());
}
function change_close_loan_name(){
	$('#close_loan_name').val($('#close_loan_name_id :selected').text());
}

function add_loan_group(){
	$('#loan_group_modal').modal('show');
}

function edit_group_name(loan_group_id, loan_group_name, description, status){
	console.log("descript: "+description);
	$("#loan_group_id").val(loan_group_id);
	$("#loan_group_name").val(loan_group_name);
	$("#description").val(description);
	$("#status").val(status);
}

function save_gruop_name(){
	const groupName = $("#loan_group_name");
	let err = "";
	if(typeof groupName.val() === "undefined" || groupName.val() === ""){
		err = "กรุณาระบุกลุ่มเงินกู้";
	}

	if(err){
		swal(err, "", "warning");
	}else{
		$("#form_group_name").submit();
	}
}

function del_group_name(id){
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
	},function(isConfirm){
		if(isConfirm){
			$.post(base_url+"setting_credit_data/coop_loan_group_delete", {id : id}, function(data){
				if(data.status === 400){
					swal("ลบข้อมูลไม่สำเร็จ", data.msg, "warning");
					return false;
				}else{
					swal("ลบข้อมูลสำเร็จ", "", "warning");
					setTimeout(function(){
						window.location.reload();
					}, 1500);
				}
			});
		}
	});

}
