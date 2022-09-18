var base_url = $('#base_url').attr('class');
$( document ).ready(function() {
	//class for check input number
	$('.number_int_only').on('input', function () {
		this.value = this.value.replace(/[^0-9]/g, '');
	});
});

function pay_cremation(id, action){
	$('#show_pay').modal('show');
	$.ajax({
		type: "POST",
		url: base_url+'cremation/get_cremation_pay',
		data: {
			id : id
		},
		success: function(msg) {
			//console.log(msg);
			response = $.parseJSON(msg);
			//console.log(response);
			$(".cremation_request_id").val(id);
			$(".member_id").val(response.member_id);
			$(".cremation_pay_amount").val(response.cremation_pay_amount);	
			$(".cremation_type_name").val(response.cremation_type_name);
			$(".cremation_type_id").val(response.cremation_id);
			$("#receipt_number").val(response.receipt_id);			
			
			$("#action").val(action);
			if(action == 'view'){
				$('#bt_save').hide();
				$('#bt_print').show();
				$('#cremation_pay_amount').attr("disabled", true);
			}else{
				$('#bt_save').show();
				$('#bt_print').hide();
				$('#cremation_pay_amount').attr("disabled", false);
			}	
		}
	});	
}

function close_modal(id){
	$('#'+id).modal('hide');
	$("#cremation_pay_amount").val('');
}

function check_form_pay(){
	$('#from_pay').submit();
}

function check_form_print(){
	var id = $(".cremation_request_id").val();
	var url = base_url+'cremation/receipt_form_pdf/'+$("#receipt_number").val();
	//console.log(url);
	window.open(url,'_blank');
}
