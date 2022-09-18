$( document ).ready(function() {
	$("#date_transfer_picker").datepicker({
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
		  autoclose: true,
	});
	$('#time_transfer').datetimepicker({
		format: 'HH:mm',
		icons: {
			up: 'icon icon-chevron-up',
			down: 'icon icon-chevron-down'
		},
	});
});
function format_the_number(ele){
	var value = $('#'+ele.id).val();
	if(value!=''){
		value = value.replace(',','');
		value = parseInt(value);
		value = value.toLocaleString();
		if(value == 'NaN'){
			$('#'+ele.id).val('');
		}else{
			$('#'+ele.id).val(value);
		}
	}else{
		$('#'+ele.id).val('');
	}
}

function check_submit(){
	var alert_text = '';
	
	if(alert_text!=''){
		swal('กรุณากรอกข้อมูลต่อไปนี้' , alert_text , 'warning');
	}else{
		
	}
}
function chkNumber(ele){
	var vchar = String.fromCharCode(event.keyCode);
	if ((vchar<'0' || vchar>'9') && (vchar != '.')) return false;
	ele.onKeyPress=vchar;
}
 function search_loan(loan_id){
	 var contract_number = $('#contract_number').val();
	 //if(contract_number !=''){
		$.post(base_url+"loan_atm/get_loan_atm_detail_data", 
			{	
				loan_id: loan_id
			}
			, function(result){
				var obj = JSON.parse(result);
				if(obj.result=='not_found'){
					swal('ไม่พบข้อมูล');
					$('#account_list').html('<option value="">เลือกบัญชี</option>')
					$('.all_input').val('');
					$('#file_show').html('');
					$('#btn_cancel_transfer').hide();
					$('#show_pay_type1').show();
					$('#show_pay_type2').hide();
				}else{
					//console.log(obj);
					$('.loan_id').val(obj.coop_loan_atm.loan_id);
					$('#contract_number').val(obj.coop_loan_atm.contract_number);
					$('.member_id').val(obj.coop_loan_atm.member_id);
					$('#member_name').val(obj.coop_loan_atm.firstname_th+" "+obj.coop_loan_atm.lastname_th);
					$('#loan_amount').val(obj.coop_loan_atm.loan_amount);
					$('#loan_date').val(obj.coop_loan_atm.loan_date);
					if(obj.coop_loan_atm.transfer_status == '0'){
						$('#transfer_status').val('ยังไม่ได้โอนเงิน');
						//$('#btn_open_transfer').show();
					}else{
						$('#transfer_status').val('โอนเงินแล้ว');
						$('#date_transfer').val(obj.coop_loan_atm.date_transfer);
						$('#btn_open_transfer').hide();
					}
					
					$('#account_name').val(obj.coop_loan_atm.account_name);
					$('#user_name').val(obj.coop_loan_atm.user_name);
					if(obj.coop_loan_atm.file_name!=null){
						file_link = "<a target='_blank' href='"+base_url+"/assets/uploads/loan_atm_transfer_attach/"+obj.coop_loan_atm.file_name+"'>"+obj.coop_loan_atm.file_name+"</a>";
						$('#file_show').html(file_link);
					}else{
						$('#file_show').html('');
					}
					if(obj.coop_loan_atm.account_id != null){
						var account_id = obj.coop_loan_atm.account_id;
					}else{
						var account_id = '';
					}
					get_account_list(obj.coop_loan_atm.member_id, account_id);
					$('#show_pay_type1').hide();
					$('#show_pay_type2').show();
				}
			});
	 /*}else{
		 swal('กรุณากรอกเลขที่สัญญาที่ต้องการค้นหา');
		 $('#account_list').html('<option value="">เลือกบัญชี</option>')
		$('.all_input').val('');
		$('#file_show').html('');
		$('#btn_cancel_transfer').hide();
		$('#btn_cancel_transfer').attr('onclick',"");
	 }*/
	 $('#transfer_list_modal').modal('hide');
 }
 function get_account_list(member_id, account_id){
	 $.post(base_url+"/ajax/get_account_list", 
			{	
				member_id: member_id,
				account_id : account_id
			}
			, function(result){
					$('#account_list_space').html(result);
			});
 }
 function open_modal(id){
	 if($('#account_list').val() == ''){
		 swal('กรุณาเลือกเลขบัญชีสมาชิก');
	 }else{
		  $('#'+id).modal('show');
	 }
	
 }
 function open_other_modal(id){
	$('#'+id).modal('show');
 }
 
 function readURL(input) {
	if (input.files && input.files[0]) {
		var reader = new FileReader();
		reader.onload = function (e) {
			$('#ImgPreview').attr('src', e.target.result);
		}
		reader.readAsDataURL(input.files[0]);
	}
}

function check_form(){
	if($('#file_attach').val() == ''){
		 swal('กรุณาแนบหลักฐานการโอนเงิน');
	 }else{
		$('#form_loan_transfer').submit();
	 }	
}
function change_account(){
	$('#account_id').val($('#account_list :selected').val());
	$('#account_name').val($('#account_list :selected').attr('account_name'));
}
function cancel_transfer(transfer_id, loan_id){
	swal({
        title: 'ท่านต้องการยกเลิกรายการใช่หรือไม่?',
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
            document.location.href = base_url+'/loan/loan_transfer?transfer_id='+transfer_id+'&action=delete_transfer&loan_id='+loan_id;
        } else {
			
        }
    });
}
function cash_submit(){
	$('#form_loan_transfer').submit();
}
function change_pay_type(){
	if($('#pay_type_0').is(':checked')){
		$('.pay_type_0').show();
		$('.pay_type_1').hide();
	}else if($('#pay_type_1').is(':checked')){
		$('.pay_type_0').hide();
		$('.pay_type_1').show();
	}else{
		$('.pay_type_0').hide();
		$('.pay_type_1').hide();
	}
}