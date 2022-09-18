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
	
	$(".modal").on("hidden.bs.modal", function(){
		$('#return_profile_id').val("");
		$('#member_id').val("");
		$('#member_name').val("");
		$('#total_return_amount').val("");
		$('#dividend_bank_id').val("");
		$('#dividend_bank_branch_id').val("");
		$('#dividend_acc_num').val("");
		$("input:radio").removeAttr("checked");
		$('.pay_type_1').hide();
		$('.pay_type_2').hide();
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

function chkNumber(ele){
	var vchar = String.fromCharCode(event.keyCode);
	if ((vchar<'0' || vchar>'9') && (vchar != '.')) return false;
	ele.onKeyPress=vchar;
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

function open_transfer_modal(return_profile_id){
	$.ajax({
		url:base_url+"/finance/get_return_data",
		method:"post",
		data:{return_profile_id:return_profile_id},
		dataType:"text",
		success:function(data)
		{
			var obj = JSON.parse(data);
			//console.log(obj);
			$('#return_profile_id').val(return_profile_id);
			$('#member_id').val(obj.member_id);
			$('#member_name').val(obj.prename_short+obj.firstname_th+" "+obj.lastname_th);
			$('#total_return_amount').val(addCommas(obj.total_return_amount));
			
			$('#dividend_bank_id').val(obj.dividend_bank_id);
			$('#dividend_bank_branch_id').val(obj.dividend_bank_branch_id);
			$('#dividend_acc_num').val(obj.dividend_acc_num);

			list_account();	
			$('#transfer_modal').modal('show');
		}
	});		
}

function change_pay_type(){
	if($('#pay_type_1').is(':checked')){
		$('.pay_type_1').show();
		$('.pay_type_2').hide();
	}else if($('#pay_type_2').is(':checked')){
		$('.pay_type_1').hide();
		$('.pay_type_2').show();
	}else{
		$('.pay_type_1').hide();
		$('.pay_type_2').hide();
	}
}

function cash_submit(){ 
	var text_alert = "";
	if($('input[name=pay_type]').is(":checked") == false){
		text_alert += "กรุณาเลือกวิธีการชำระเงิน \n";
	}
	
	if(text_alert != ''){
		swal(text_alert);
	}else{	
		$('#form_return_transfer').submit();
	}
}


function list_account(){
	var member_id = $("#member_id").val();
    $.ajax({
        method: 'POST',
        url: base_url+'loan/get_account_list',
        data: {
            member_id : member_id
        },
        success: function(msg){
			//console.log(msg);
            $('#account_list_space').html(msg);
        }
    });	
}
function removeCommas(str) {
    return(str.replace(/,/g,''));
}
function addCommas(x){
  return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}