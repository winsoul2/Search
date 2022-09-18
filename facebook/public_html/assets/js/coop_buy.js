var base_url = $('#base_url').attr('class');
$( document ).ready(function() {
	
	$("#buy_date").datepicker({
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
	
	// Toast
	 $('#search_buy').keyup(function(){
           var txt = $(this).val();
           if(txt != '')
           {
                search_buy(txt);
           }else{

           }

      });
});
function search_buy(txt){
	$.ajax({
		 url:base_url+"coop_buy/search_account_buy",
		 method:"post",
		 data:{search:txt},
		 dataType:"text",
		 success:function(data)
		 {
		 console.log(data);
		  $('#result_account_buy').html(data);
		 }
	});
}
function check_form(){
	var text_alert = '';
	if($('#buy_date').val()==''){
		text_alert += ' - วันที่\n';
	}
	if($('#account_id').val()==''){
		text_alert += ' - เลือกรายการ\n';
	}
	if($('#pay_for').val()==''){
		text_alert += ' - จ่ายให้\n';
	}
	if(!$('#pay_type').is(':checked') && !$('#pay_type2').is(':checked') && !$('#pay_type3').is(':checked')){
		text_alert += ' - วิธีชำระเงิน\n';
	}
	if($('#pay_type2').is(':checked')){
		if($('#cheque_number').val()==''){
			text_alert += ' - เลขที่เช็ค\n';
		}
		if($('#cheque_date').val()==''){
			text_alert += ' - วันที่เช็ค\n';
		}
	}
	// if($('#pay_type3').is(':checked')){
	// 	if($('#account_bank_id').val()==''){
	// 		text_alert += ' - ธนาคารที่โอนเงิน\n';
	// 	}
	// }
	if($('#pay_amount').val()==''){
		text_alert += ' - จำนวนเงิน\n';
	}
	if($('#pay_description').val()==''){
		text_alert += ' - รายละเอียดการซื้อ\n';
	}
	// if($('#bill_number').val()==''){
	// 	text_alert += ' - เลขที่บิล\n';
	// }
	if(text_alert != ''){
		swal('กรุณากรอกข้อมูลต่อไปนี้',text_alert,'warning');
	}else{
		get_data();
	}
}
function get_data(){
	var add_data = "";
	var number_input = parseInt($('#number_input').val()) + 1;
	$('#buy_date_input').val($('#buy_date').val());
	$('#pay_for_input').val($('#pay_for').val());
    $('#cashpay_input').val($('#cashpay').val());

    if($('#pay_type').is(':checked')){
		$('#pay_type_input').val($('#pay_type').val());
	}
	if($('#pay_type2').is(':checked')){
		$('#pay_type_input').val($('#pay_type2').val());
	}
	if($('#pay_type3').is(':checked')){
		$('#pay_type_input').val($('#pay_type3').val());
	}
	$('#cheque_number_input').val($('#cheque_number').val());
	$('#cheque_date_input').val($('#cheque_date').val());
	$('#account_bank_id_input').val($('#account_bank_id').val());

	$('#buy_date_preview').val($('#buy_date').val());
	$('#pay_for_preview').val($('#pay_for').val());
    $('#cashpay_preview').val($('#cashpay').val());

    if($('#pay_type').is(':checked')){
		$('#pay_type_preview').val($('#pay_type').val());
	}
	if($('#pay_type2').is(':checked')){
		$('#pay_type_preview').val($('#pay_type2').val());
	}
	if($('#pay_type3').is(':checked')){
		$('#pay_type_preview').val($('#pay_type3').val());
	}
	$('#cheque_number_preview').val($('#cheque_number').val());
	$('#cheque_date_preview').val($('#cheque_date').val());
	$('#account_bank_id_preview').val($('#account_bank_id').val());
	
	add_data += '<div id="input_'+number_input+'">';
	add_data += '<input type="hidden" name="data[coop_account_buy_detail]['+number_input+'][account_id]" value="'+$('#account_id').val()+'">';
	add_data += '<input type="hidden" class="pay_amount" name="data[coop_account_buy_detail]['+number_input+'][pay_amount]" value="'+$('#pay_amount').val()+'">';
	add_data += '<input type="hidden" name="data[coop_account_buy_detail]['+number_input+'][pay_description]" value="'+$('#pay_description').val()+'">';
	add_data += '<input type="hidden" name="data[coop_account_buy_detail]['+number_input+'][bill_number]" value="'+$('#bill_number').val()+'">';
	add_data += '</div">';
	$('#hidden_space').append(add_data);
	$('#hidden_space_preview').append(add_data);
	
	var add_table = "";
	add_table += '<tr id="tr_'+number_input+'">';
	add_table += '<td>'+$('#bill_number').val()+'</td>';
	add_table += '<td>'+$('#pay_description').val()+'</td>';
	add_table += '<td>'+number_format($('#pay_amount').val(),2)+'</td>';
	add_table += '<td><a style="color:red;cursor:pointer;" onclick="del_data(\''+number_input+'\')">ลบ</a></td>';
	add_table += '</tr">';
	$('#table_space').append(add_table);
	$('.table_footer').show();
	
	$('#number_input').val(number_input);
	$('#account_id').val('');
	$('#pay_amount').val('');
	$('#pay_description').val('');
	$('#bill_number').val('');
	var total_amount = 0;
	$("#hidden_space .pay_amount").each(function(){
		total_amount += parseFloat($(this).val());
	});
	$('#total_space').html(number_format(total_amount,2));
}
function del_data(id){
	$('#input_'+id).remove();
	$('#tr_'+id).remove();
	
	var total_amount = 0;
	var count_list = 0;
	$('.pay_amount').each(function(){
		total_amount += parseFloat($(this).val());
		count_list++
	});
	$('#total_space').html(number_format(total_amount,2));
	if(count_list == 0){
		$('.table_footer').hide();
	}
}
function submit_form(){
	$('#form2').submit();
	$('#hidden_space').html('');
	$('#hidden_space_preview').html('');
	$('#table_space').html('');
	$('.type_input').val('');
	$('#pay_for').val('');
	$('.table_footer').hide();
}

function preview() {
	$('#form3').submit();
}

function change_pay_type(type){
	if(type=='2'){
		$('.cheque_space').show();
		$('.transfer_space').hide();
	}else if(type=='3'){
		$('.cheque_space').hide();
		$('.transfer_space').show();
	}else{
		$('.cheque_space').hide();
		$('.transfer_space').hide();
	}
}
function chkNumber(ele){
	var vchar = String.fromCharCode(event.keyCode);
	if ((vchar<'0' || vchar>'9') && (vchar != '.')) return false;
	ele.onKeyPress=vchar;
}
function number_format (number, decimals, dec_point, thousands_sep) {
    // Strip all characters but numerical ones.
    number = (number + '').replace(/[^0-9+\-Ee.]/g, '');
    var n = !isFinite(+number) ? 0 : +number,
        prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
        sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
        dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
        s = '',
        toFixedFix = function (n, prec) {
            var k = Math.pow(10, prec);
            return '' + Math.round(n * k) / k;
        };
    // Fix for IE parseFloat(0.55).toFixed(0) = 0;
    s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
    if (s[0].length > 3) {
        s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
    }
    if ((s[1] || '').length < prec) {
        s[1] = s[1] || '';
        s[1] += new Array(prec - s[1].length + 1).join('0');
    }
    return s.join(dec);
}
function change_account_list(){
	$('#pay_description').val($('#account_id :selected').attr('account_list'));
	$('#pay_amount').val($('#account_id :selected').attr('amount'));
}
function cancel_account_buy(status_to, account_buy_id){
	if(status_to=='0'){
		var title = "ท่านต้องการยกเลิกการยกเลิกรายการซื้อใช่หรือไม่?";
	}else{
		var title = "ท่านต้องการยกเลิกรายการซื้อใช่หรือไม่?";
	}
	swal({
        title: title,
        text: "",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: '#DD6B55',
        confirmButtonText: 'ยืนยัน',
        cancelButtonText: "ยกเลิก",
        closeOnConfirm: false,
        closeOnCancel: true
    },
    function(isConfirm) {
        if (isConfirm) {
            document.location.href = base_url+'coop_buy/cancel_account_buy?account_buy_id='+account_buy_id+'&action_cancel='+status_to;
        } else {
			
        }
    });
}