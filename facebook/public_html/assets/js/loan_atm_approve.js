 function loan_atm_not_approve(id, status_to){
	 swal({
        title: 'ไม่อนุมัติการกู้เงิน',
        text: "",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: '#0288d1',
        confirmButtonText: 'ยืนยัน',
        cancelButtonText: "ปิดหน้าต่าง",
        closeOnConfirm: false,
        closeOnCancel: true
    },
    function(isConfirm) {
        if (isConfirm) {
            document.location.href = base_url+'loan_atm/loan_atm_not_approve?loan_atm_id='+id+'&status_to='+status_to;
        } else {
			
        }
    });
 }
function approve_loan_save(loan_atm_id){
	$.ajax({
		url:base_url+"/loan_atm/get_loan_atm_data",
		method:"post",
		data:{loan_atm_id:loan_atm_id},
		dataType:"text",
		success:function(data)
		{
			var obj = JSON.parse(data);
			console.log(obj);
			$('#loan_atm_id').val(obj.loan_atm_data.loan_atm_id);
			$('#petition_number').val(obj.loan_atm_data.petition_number);
			$('#member_id').val(obj.loan_atm_data.member_id);
			$('#member_name').val(obj.loan_atm_data.prename_short+obj.loan_atm_data.firstname_th+' '+obj.loan_atm_data.lastname_th);
			$('#total_amount').val(obj.loan_atm_data.total_amount);
			$('#total_amount_approve').val(obj.loan_atm_data.total_amount);
			//$('#atm_card_number').val(obj.loan_atm_data.atm_number);
			if(parseInt(obj.loan_atm_data.prev_loan) > 0){
				$('#prev_loan').val(obj.loan_atm_data.prev_loan);
				$('#prev_loan_number').val(obj.loan_atm_data.prev_loan_number);
			}else{
				$('#prev_loan').val(obj.loan_atm_data.prev_loan_atm);
				$('#prev_loan_number').val(obj.loan_atm_data.prev_loan_atm_number);
			}
			$('#total_amount_approve_balance').val(obj.loan_atm_data.total_amount_balance);
			open_modal('loan_approve_modal');
		}
	});
}
function open_modal(id){
	$('#'+id).modal('show');
}
function cal_balance(){
	var prev_loan = removeCommas($('#prev_loan').val());
	var total_amount_approve = removeCommas($('#total_amount_approve').val());
	var diff_amount = parseInt(total_amount_approve) - parseInt(prev_loan);
	if(diff_amount>0){
		$('#total_amount_approve_balance').val(addCommas(diff_amount));
	}else{
		$('#total_amount_approve_balance').val(0);
	}
}
function removeCommas(str) {
    return(str.replace(/,/g,''));
}
function addCommas(x){
  return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}
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
	var text_alert = '';
	var prev_loan = removeCommas($('#prev_loan').val());
	var total_amount = removeCommas($('#total_amount').val());
	var total_amount_approve = removeCommas($('#total_amount_approve').val());
	if($('#total_amount_approve').val() == ''){
		text_alert += '- กรุณากรอกวงเงินที่อนุมัติ';
	}else if(parseInt(total_amount) < parseInt(total_amount_approve)){
		text_alert += '- ไม่สามารถอนุมัติวงเงินมากกว่าวงเงินที่ขอกู้ได้';
	}else if(parseInt(prev_loan) > parseInt(total_amount_approve)){
		text_alert += '- ไม่สามารถอนุมัติวงเงินน้อยกว่าจำนวนเงินหักกลบได้';
	}
	if(text_alert != ''){
		swal('เกิดข้อผิดพลาด', text_alert, 'warning');
	}else{
		$('#form_approve').submit();
	}
}