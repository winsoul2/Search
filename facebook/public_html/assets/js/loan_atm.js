$('#search_member_loan').keyup(function(){
   var txt = $(this).val();
   if(txt != ''){
		$.ajax({
			 url:base_url+"/ajax/search_member_jquery",
			 method:"post",
			 data:{search:txt, member_id_not_allow: $('#member_id').val()},
			 dataType:"text",
			 success:function(data)
			 {
			 //console.log(data);
			  $('#result_member_search').html(data);
			 }
		});
   }else{
	   
   }
});
function addCommas(x){
  return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}
function open_modal(id,loan_atm_id=''){
	$('#'+id).modal('show');
	if(id == 'loan_contract_modal'){
		$('.loan_atm_id').val(loan_atm_id);	
		$.post( base_url+"/loan_atm/ajax_get_loan_atm_prev_deduct",
			{	
				loan_atm_id: loan_atm_id
			}
			, function(result){
				if(result != 'not_found'){
					var obj = JSON.parse(result);
					//console.log(obj);	
					if(obj.coop_loan_atm != null){	
						$('#loan_atm_id_n').val(obj.coop_loan_atm.loan_atm_id);	
						$('#petition_number').val(obj.coop_loan_atm.petition_number);
						$('#member_id').val(obj.coop_loan_atm.member_id);
						$('#total_amount').val(obj.coop_loan_atm.total_amount);
						
						
						$('.prev_loan_checkbox').attr('checked',false);
						$('.prev_loan_amount').val('');
						for(var key in obj.coop_loan_atm_prev_deduct){
							//console.log(obj.coop_loan_atm_prev_deduct[key]);
							console.log(obj.coop_loan_atm_prev_deduct[key].ref_id);
							console.log($(this).attr('ref_id'));
							$('.prev_loan_checkbox').each(function(){
								if(obj.coop_loan_atm_prev_deduct[key].ref_id == $(this).attr('ref_id') && obj.coop_loan_atm_prev_deduct[key].data_type == $(this).attr('data_type')){
									var index = $(this).attr('attr_index');
									$(this).attr('checked',true);
									if(obj.coop_loan_atm_prev_deduct[key].pay_type == 'principal'){
										$('#prev_loan_pay_type_1_'+index).attr('checked',true);
										$('#prev_loan_pay_type_2_'+index).attr('checked',false);
									}else if(obj.coop_loan_atm_prev_deduct[key].pay_type == 'all'){
										$('#prev_loan_pay_type_1_'+index).attr('checked',false);
										$('#prev_loan_pay_type_2_'+index).attr('checked',true);
									}
									$('#prev_loan_amount_'+index).val(addCommas(obj.coop_loan_atm_prev_deduct[key].pay_amount));
								}
							});
						}
						change_prev_loan_pay_type('loan_contract_modal');
					}
				}
				
			});				
	}
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
	var min_loan_amount = removeCommas($('#min_loan_amount').val());
	var loan_amount = removeCommas($('#loan_amount').val());
	var total_amount_balance = removeCommas($('#total_amount_balance').val());
		
	if($('#loan_amount').val() == ''){
		text_alert += '- กรุณากรอกจำนวนเงิน\n';
	}
	else if(parseInt(loan_amount) > parseInt(total_amount_balance)){
		text_alert += '- จำนวนเงินสูงสุดที่สามารถทำรายการได้คือ '+addCommas(total_amount_balance)+' บาท\n';
	}
	if($('#pay_type_3').is(':checked')){
		if($('#account_id').val()==''){
			text_alert += '- กรุณาเลือกบัญชี\n';
		}
	}
	if($('#pay_type_1').is(':checked')){
		if($('#bank_id').val()==''){
			text_alert += '- กรุณาเลือกธนาคาร\n';
		}
		if($('#bank_account_id').val()==''){
			text_alert += '- กรุณากรอกเลขบัญชี\n';
		}
	}
	/*
	else if(parseInt(min_loan_amount) > parseInt(loan_amount)){
		text_alert += '- จำนวนเงินต่ำสุดที่สามารถทำรายการได้คือ '+addCommas(min_loan_amount)+' บาท\n';
	}*/
	
	if(text_alert != ''){
		swal('เกิดข้อผิดพลาด',text_alert,'warning');
	}else{
		loan_amount = Math.ceil(loan_amount/100)*100;
		swal({
			title: "จำนวนเงินที่สามารถทำรายการได้คือ "+addCommas(loan_amount)+" บาท",
			text: "",
			type: "warning",
			showCancelButton: true,
			//confirmButtonColor: '#DD6B55',
			confirmButtonColor: '#d50000',
			confirmButtonText: 'ยืนยัน',
			cancelButtonText: "ยกเลิก",
			closeOnConfirm: true,
			closeOnCancel: true
		},
		function(isConfirm) {
			if (isConfirm) {
				$('#loan_amount').val(loan_amount);
				$('#form_normal_loan').submit();				
			}else{
				
			}
		});
	}
}
function check_submit_contract(){ 
	var text_alert = '';	
	
	if($('#total_amount').val() == ''){
		text_alert += '- กรุณากรอกวงเงินขอกู้\n';
	}else if(parseInt(removeCommas($('#total_amount').val())) > parseInt(removeCommas($('#max_loan_amount').val()))){
		//text_alert += '- วงเงินขอกู้สูงสุดคือ '+$('#max_loan_amount').val()+' บาท\n';
		text_alert += '- ไม่สามารถอนุมัติวงเงินเกินกว่าจำนวนทุนเรือนที่มีได้\n';
	}else if(parseInt(removeCommas($('#total_amount').val())) < parseInt($('#total_amount_prev_deduct').val())){
		text_alert += '- ไม่สามารถอนุมัติวงเงินน้อยกว่ายอดปิดสัญญาเก่าที่มีได้\n';
	}
	if(text_alert != ''){
		swal('เกิดข้อผิดพลาด',text_alert,'warning');
	}else{
		$('#form_contract').submit();
	}
}
function removeCommas(str) {
    return(str.replace(/,/g,''));
}

function del_file(id){
	swal({
        title: "ท่านต้องการลบไฟล์ใช่หรือไม่?",
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
			$.post( base_url+"/loan_atm/ajax_delete_loan_file_attach", 
			{	
				id: id
			}
			, function(result){
				if(result=='success'){
					$('#file_'+id).remove();
					
					var i=0;
					$('.file_row').each(function(index){
						i++;
						//console.log(i);
					});
					
					if(i<=0){
						$('#show_file_attach').modal('hide');
						$('#btn_show_file').hide();
					}
				}else{
					swal('ไม่สามารถลบไฟล์ได้');
				}
			});
			
		}else{
			
		}
	});
}
function close_modal(id){
	$('#'+id).modal('hide');
}
function cancel_contract(principal_amount){
	if(principal_amount=='0'){
		swal({
        title: "ท่านต้องการยกเลิกสัญญาใช่หรือไม่?",
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
			$('#form_cancel_contract').submit();
		}else{
			
		}
	});
	}else{
		swal('','ไม่สามารถยกเลิกสัญญาได้เนื่องจากท่านทียอดเงินค้างชำระ','warning');
	}
}
function check_submit_change_amount(){ 
	var text_alert = '';
	if($('#total_amount_c').val() == ''){
		text_alert += '- กรุณากรอกวงเงินขอกู้\n';
	}else if(parseInt(removeCommas($('#total_amount_c').val())) > parseInt(removeCommas($('#max_loan_amount_c').val()))){
		text_alert += '- วงเงินขอกู้สูงสุดคือ '+$('#max_loan_amount_c').val()+' บาท\n';
	}
	if(parseInt(removeCommas($('#total_amount_c').val())) < parseInt(removeCommas($('#deduct_amount').val()))){
		text_alert += '- ไม่สามารถเปลี่ยนแปลงวงเงินน้อยกว่ายอดหักกลบได้\n';
	}
	if(text_alert != ''){
		swal('เกิดข้อผิดพลาด',text_alert,'warning');
	}else{
		$('#form_change_amount').submit();
	}
}
// function change_prev_loan_pay_type(){
// 	$('.prev_loan_checkbox').each(function(){
// 		var index = $(this).attr('attr_index');
// 		if($(this).is(':checked')){
// 			if($('#prev_loan_pay_type_1_'+index).is(':checked')){
// 				$('#prev_loan_amount_'+index).val($('#principal_without_finance_month_'+index).val());
// 			}else if($('#prev_loan_pay_type_2_'+index).is(':checked')){
// 				$('#prev_loan_amount_'+index).val($('#prev_loan_total_'+index).val());
// 			}
// 		}else{
// 			$('#prev_loan_amount_'+index).val('');
// 		}
// 	});
// 	cal_prev_loan();
// 	// check_deduct_person_guarantee();
// }
function cal_prev_loan(){
	var deduct_pay_prev_loan = 0;
	$('.prev_loan_amount').each(function(){
		var index = $(this).attr('attr_index');
		if($('#prev_loan_checkbox_'+index).is(':checked') && $(this).val()!=''){
			deduct_pay_prev_loan += parseFloat(removeCommas($(this).val()));
		}
	});
	deduct_pay_prev_loan = addCommas(deduct_pay_prev_loan);
	$('#deduct_pay_prev_loan').val(deduct_pay_prev_loan);
	cal_estimate_money();
}
$( document ).ready(function() {
	$(".modal").on("hidden.bs.modal", function(){
		var total_amount_val = $('#total_amount_val').val();
		var loan_reason_val = $('#loan_reason_val').val();
		$('#total_amount').val(total_amount_val);
		$('#loan_reason').val(loan_reason_val);
	});
});

function loan_atm_lock(loan_atm_id,member_id){
	swal({
		title: "ท่านต้องการระงับสัญญาใช่หรือไม่?",
		text: "ท่านจะไม่สามารถทำรายการใดๆได้จนกว่าจะปลดระงับสัญญา",
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
			document.location.href = base_url+'loan_atm/loan_atm_lock/'+loan_atm_id+'/'+member_id;
		}else{
			
		}
	});
}
function loan_atm_unlock(loan_atm_id,member_id){
	swal({
		title: "ท่านต้องการปลดระงับสัญญาใช่หรือไม่?",
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
			document.location.href = base_url+'loan_atm/loan_atm_unlock/'+loan_atm_id+'/'+member_id;
		}else{
			
		}
	});
}


function check_modal(id){
	var activate_status = $('#activate_status').val();
	if(activate_status == '1'){
		swal('ไม่สามารถทำรายการได้ เนื่องจากถูกระงับสัญญา','ท่านจะไม่สามารถทำรายการใดๆได้จนกว่าจะปลดระงับสัญญา','warning');
	}else{
		open_modal(id);
	}
}
function change_pay_type(){
	$('#account_choose_space').hide();
	$('.coop_account').hide();
	$('.bank_account').hide();
	if($('#pay_type_3').is(':checked')){
		$('#account_choose_space').show();
		$('.coop_account').show();
	}else if($('#pay_type_1').is(':checked')){
		$('#account_choose_space').show();
		$('.bank_account').show();
	}
}

function change_prev_loan_pay_type(loan_modal){
	var total_amount = removeCommas($("#total_amount").val());
	var prev_loan_amount_all = 0;
	var prev_loan_total = 0;
	var deduct_amount = 0;
	$('.prev_loan_checkbox').each(function(){
		var index = $(this).attr('attr_index');
		if($(this).is(':checked')){
			var prev_loan_total_c  = '';
			if($('#prev_loan_pay_type_1_'+index).is(':checked')){
				$('#prev_loan_amount_'+index).val($('#principal_without_finance_month_'+index).val());				
				prev_loan_total_c = $("#"+loan_modal).find('.modal-body #principal_without_finance_month_'+index).val();
				
			}else if($('#prev_loan_pay_type_2_'+index).is(':checked')){
				$('#prev_loan_amount_'+index).val($('#prev_loan_total_'+index).val());
				prev_loan_total_c = $("#"+loan_modal).find('.modal-body #prev_loan_total_'+index).val();
			}
			prev_loan_total = parseFloat(removeCommas(prev_loan_total_c));
			
			prev_loan_amount_all += prev_loan_total;
			//console.log(prev_loan_amount_all);
			//console.log(total_amount);
			if(prev_loan_amount_all > total_amount){	
				prev_loan_amount_all -= prev_loan_total;
				$("#"+loan_modal).find('.modal-body #prev_loan_checkbox_'+index).removeAttr('checked');
				swal("","ไม่สามารถหักกลบสัญญานี้ได้ เนื่องจากยอดเงินหักลบเกินวงเงินที่ขอกู้","warning");
				$('#prev_loan_checkbox_'+index).prop('checked', false); 
				//console.log('OVER ID #prev_loan_checkbox_'+index);
			}else if(prev_loan_amount_all==0 || prev_loan_amount_all==undefined){
				swal("","ไม่สามารถหักกลบสัญญานี้ได้","warning");
				$("#"+loan_modal).find('.modal-body #prev_loan_checkbox_'+index).removeAttr('checked');
			}else{
				deduct_amount += prev_loan_total;
				//$('#deduct_amount').val(deduct_amount);
				$("#"+loan_modal).find('.modal-body #deduct_amount').val(deduct_amount);
			}	
		}else{
			//$('#deduct_amount').val('');
			$("#"+loan_modal).find('.modal-body #deduct_amount').val();
			$('#prev_loan_amount_'+index).val('');
		}
	});
	$("#deduct_amount").val( addCommas(deduct_amount) );
	var total_amount = removeCommas( $("#total_amount").val() );
	var net = total_amount - prev_loan_amount_all;
	$("#net_amount").val( addCommas(net) );
}