var base_url = $('#base_url').attr('class');
$( document ).ready(function() {
	$("#compromise_id").change(function() {
		$("#other_payment_compromise").val($('option:selected',this).attr("debt"))
	});
	$("#cremation_year").change(function() {
		$.ajax({
			url: base_url+'/cashier/get_cremation_debt_info_from_year?year='+$(this).val()+"&member_id="+$("#member_id").val(),
			method: 'GET',
			async:false,
			success: function(result){
				data = JSON.parse(result);
				for (i = 0; i < data.length; i++) {
					$('#cremation_month').append('<option value="'+data[i].month+'" id="cremation_month_'+i+'" data-debt="'+data[i].debt_total+'">'+data[i].month_text+'</option>');
				}
			}
		});
	});
	$("#cremation_month").change(function() {
		$("#cremation_debt").val(format_number($('option:selected', this).attr("data-debt")));
	});

	$("#fix_date").datepicker({
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

	$("#fix_date").change(function() {
		console.log( $(this).val() );
		var member_id = getUrlParameter('member_id');
		var fix_date = getUrlParameter('fix_date');

		var url = window.location.origin+window.location.pathname+'?member_id='+getUrlParameter('member_id')+'&from_member_id='+getUrlParameter('from_member_id')+"&fix_date="+$(this).val()
		window.location.href = url;
	});
});

var getUrlParameter = function getUrlParameter(sParam) {
    var sPageURL = window.location.search.substring(1),
        sURLVariables = sPageURL.split('&'),
        sParameterName,
        i;

    for (i = 0; i < sURLVariables.length; i++) {
        sParameterName = sURLVariables[i].split('=');

        if (sParameterName[0] === sParam) {
            return sParameterName[1] === undefined ? true : decodeURIComponent(sParameterName[1]);
        }
    }
};

function change_type(){
	$('#loan_id').val('');
	$('#loan_atm_id').val('');
	$('#principal_amount').val('');
	$('#interest_amount').val('');
	if($('#account_list').val() == '15'){
		$('.loan_data').show();
		$('.loan_normal_data').show();
		$('.loan_atm_data').hide();
		$('.loan_all_data').show();
		$('.loan_deduct_type').show();
		$('.other_payment_compromise').hide();
		$('.creamation_payment').hide();
	}else if($('#account_list').val() == '31'){
		$('.loan_data').show();
		$('.loan_normal_data').hide();
		$('.loan_atm_data').show();
		$('.loan_all_data').show();
		$('.loan_deduct_type').hide();
		$('.other_payment_compromise').hide();
		$('.creamation_payment').hide();
	}else if($('#account_list').val() == '47'){
		$('.loan_data').hide();
		$('.loan_normal_data').show();
		$('.loan_atm_data').hide();
		$('.loan_all_data').hide();
		$('.loan_deduct_type').hide();
		$('.other_payment_compromise').show();
		$('.creamation_payment').hide();
	}else if($('#account_list').val() == '28'){
		$('.loan_data').hide();
		$('.loan_normal_data').show();
		$('.loan_atm_data').hide();
		$('.loan_all_data').hide();
		$('.loan_deduct_type').hide();
		$('.other_payment_compromise').hide();
		$('.creamation_payment').show();
	}else{
		$('.loan_data').hide();
		$('.loan_normal_data').show();
		$('.loan_atm_data').hide();
		$('.loan_all_data').hide();
		$('.loan_deduct_type').show();
		$('.other_payment_compromise').hide();
		$('.creamation_payment').hide();
	}
}
var i=0;
function check_form(){
	if($("#account_list").val() == '15' || $("#account_list").val() == '31') {
		type = "loan"
		id = $("#loan_id").val()
		if($("#account_list").val() == '31') {
			type = "loan_atm"
			id = $("#loan_atm_id").val()
		}
		$.ajax({
			url: base_url+'/cashier/get_non_pay_balance_by_loan_id?id='+id+"&type="+type,
			method: 'GET',
			async:false,
			success: function(result){
				console.log(format_number(result))
				if(result != 0) {
					swal({
						title: "",
						text: "สัญญาเงินกู้มีหนี้ค้างชำระอยู่ "+format_number(result)+" บาท ต้องการดำเนินการต่อหรือไม่?",
						type: "warning",
						showCancelButton: true,
						confirmButtonColor: '#DD6B55',
						confirmButtonText: 'ยืนยัน',
						cancelButtonText: "ยกเลิก",
						closeOnConfirm: true,
						closeOnCancel: true
					},
					function(isConfirm){
						if (isConfirm){
							check_form_2()
						}
					})
				} else {
					check_form_2()
				}
			}
		})
	} else {
		check_form_2()
	}
}

function check_form_2(){
	var text_alert = '';
	var amount_chk = removeCommas($('#amount').val());
	var interest_amount_chk = removeCommas($('#interest_amount').val());
	if($('#account_list').val()==''){
		text_alert += ' - รายละเอียดการชำระเงิน\n';
	}else if($('#account_list').val()=='15'){
		if($('#loan_id').val()==''){
			text_alert += ' - เลขที่สัญญา\n';
		}
	}
	if($('#amount').val()==''){
		text_alert += ' - จำนวนเงิน\n';
	}
	
	// if(parseFloat(amount_chk) < parseFloat(interest_amount_chk) && $("#deduct_type_all").is(":checked")){
	// 	text_alert += ' - จำนวนเงินต้องมากกว่าดอกเบี้ย\n';
	// }
	
	if(text_alert != ''){
		swal('กรุณากรอกข้อมูลต่อไปนี้',text_alert,'warning');
	}else{
		//Refrain
		var loan_principal_refrain = $("#loan_principal_refrain").val();
		var loan_interest_refrain = $("#loan_interest_refrain").val();

		var account_list = $('#account_list').val();
		var account_list_text = $('#account_list :selected').text();
		var loan_id = $('#loan_id').val();
		if(account_list === '46'){
			account_list_text += ': '+$('#other_desc').val()
		}
		if(loan_id!=''){
			account_list_text += 'เลขที่สัญญา '+$('#loan_id :selected').text();
		}
		var loan_atm_id = $('#loan_atm_id').val();
		if(loan_atm_id!=''){
			account_list_text += 'เลขที่สัญญา '+$('#loan_atm_id :selected').text();
		}
		var amount = loan_principal_refrain != "" ? 0 : removeCommas($('#amount').val());
		var interest_amount = loan_interest_refrain != "" ? 0 : removeCommas($('#interest_amount').val());
		var interest_amount_all = removeCommas($('#interest_amount_all').val());
		var interest_debt = $('#interest_debt').val() ? removeCommas($('#interest_debt').val()) : 0;
		var interest_non_pay = $('#interest_non_pay').val() ? removeCommas($('#interest_non_pay').val()) : 0;
		var fix_date = $("#fix_date").val();
		var from_member_id = $("#from_member_id").val();
		var cheque_no = $("#cheque_no").val();
		var bank_id = $("#bank_id").val();
		var branch_code = $("#branch_code").val();
		var local_account_id = $("#local_account_id").val();
		var other = $("#other").val();
		var transfer_other = $("#transfer_other").val();

		if(interest_amount == ''){
			interest_amount = 0;
		}
		var compromise_id = $('#compromise_id').val();
		if(compromise_id!='' && compromise_id!== 'undefined' && compromise_id!="NaN"){
			account_list_text += ' '+$('#compromise_id :selected').text();
		}

		// Cremation
		var cremation_year = $("#cremation_year").val();
		var cremation_month = $("#cremation_month").val();
		
		interest_text = 0;
		interest_payment = 0;
		interest_debt_payment = 0;
		total_interest = parseFloat(interest_debt)+parseFloat(interest_amount);
		total_pay = 0;
		if($('#deduct_type_principal').is(':checked')){
			
			var principal_amount = parseFloat(amount);
			interest_amount = 0;
			interest_debt = 0;
			deduct_type = 'principal';
			
		}else if($('#deduct_type_all').is(':checked')){
			//var principal_amount = (parseFloat(amount)-parseFloat(total_interest)) > 0 ? parseFloat(amount)-parseFloat(total_interest) : 0;
			var principal_amount = (parseFloat(amount)-parseFloat(interest_amount)) > 0 ? parseFloat(amount)-parseFloat(interest_amount) : 0;
			//interest_text = parseFloat(total_interest) <= parseFloat(amount) ? total_interest : amount;
			//interest_payment = parseFloat(interest_amount) <= parseFloat(interest_text) ? interest_amount : interest_text;
			//interest_debt_payment = parseFloat(interest_amount) <= parseFloat(interest_text) ? parseFloat(interest_text) - parseFloat(interest_amount) : 0;
			interest_text = parseFloat(interest_amount);
			interest_payment = parseFloat(interest_amount);			
			interest_debt_payment = parseFloat(interest_non_pay);
			deduct_type = 'all';
		}else{
			var principal_amount = 0;
			deduct_type = 'interest';
			//interest_text = parseFloat(total_interest) <= parseFloat(amount) ? total_interest : amount;
			interest_text = parseFloat(interest_amount);
			interest_payment = parseFloat(interest_amount) <= parseFloat(interest_text) ? interest_amount : interest_text;
			//interest_debt_payment = parseFloat(interest_amount) <= parseFloat(interest_text) ? parseFloat(interest_text) - parseFloat(interest_amount) : 0;
			interest_debt_payment = parseFloat(interest_non_pay);
		}
		
		total_pay  = principal_amount+interest_text;

		var table_data = '';
			table_data = '<tr class="table_data" id="list_'+i+'">';
			table_data += '<td align="left">'+account_list_text+'</td>';
			table_data += '<td align="right">'+addCommas(principal_amount)+'</td>';
			// table_data += '<td align="right">'+addCommas(interest_amount)+'</td>';
			table_data += '<td align="right">'+addCommas(interest_text)+'</td>';
			//table_data += '<td align="right">'+addCommas(amount)+'</td>';
			table_data += '<td align="right">'+addCommas(total_pay)+'</td>';
			table_data += '<td align="center"><a style="cursor:pointer" onclick="delete_list(\''+i+'\')">ลบ</a></td>';

			table_data += '<input type="hidden" name="fix_date" value="'+fix_date+'">';
			table_data += '<input type="hidden" name="account_list['+i+']" value="'+account_list+'">';
			table_data += '<input type="hidden" name="loan_id['+i+']" value="'+loan_id+'">';
			table_data += '<input type="hidden" name="loan_atm_id['+i+']" value="'+loan_atm_id+'">';
			table_data += '<input type="hidden" name="principal_payment['+i+']" value="'+principal_amount+'">';
			table_data += '<input type="hidden" name="interest_all['+i+']" value="'+interest_amount_all+'">';
			table_data += '<input type="hidden" name="interest['+i+']" value="'+interest_payment+'">';
			table_data += '<input type="hidden" name="interest_debt['+i+']" value="'+interest_debt_payment+'">';
			//table_data += '<input type="hidden" class="amount" name="amount['+i+']" value="'+amount+'">';
			table_data += '<input type="hidden" class="amount" name="amount['+i+']" value="'+total_pay+'">';
			table_data += '<input type="hidden" name="deduct_type['+i+']" value="'+deduct_type+'">';
			table_data += '<input type="hidden" name="compromise_id['+i+']" value="'+compromise_id+'">';
			table_data += '<input type="hidden" name="cremation_month['+i+']" value="'+cremation_month+'">';
			table_data += '<input type="hidden" name="cremation_year['+i+']" value="'+cremation_year+'">';
			table_data += '<input type="hidden" name="pay_type" value="'+$('input[name=pay_type]:checked').val()+'">';
			table_data += '<input type="hidden" name="loan_principal_refrain" value="'+loan_principal_refrain+'">';
			table_data += '<input type="hidden" name="loan_interest_refrain" value="'+loan_interest_refrain+'">';
			
			table_data += '<input type="hidden" name="cheque_no" value="'+cheque_no+'">';
			table_data += '<input type="hidden" name="bank_id" value="'+bank_id+'">';
			table_data += '<input type="hidden" name="branch_code" value="'+branch_code+'">';
			table_data += '<input type="hidden" name="local_account_id" value="'+local_account_id+'">';
			table_data += '<input type="hidden" name="other" value="'+other+'">';
			table_data += '<input type="hidden" name="transfer_other" value="'+transfer_other+'">';
			table_data += '<input type="hidden" name="other_text_desc['+i+']" value="'+account_list_text+'">';
		table_data += '</tr>';
		$('#table_data').append(table_data);
		$('#value_null').hide();
		$('.table_footer').show();
		var sum_amount = 0;
		$('.amount').each(function(){
			sum_amount += parseFloat($(this).val());
		});
		$('#sum_amount').html(addCommas(sum_amount));
		i++;
		$('#account_list').val('');
		$('#amount').val('');
		$('#loan_id').val('');
		$('#loan_atm_id').val('');
		$('#pay_all').attr('checked',false);
		$('#principal_amount').val('');
		$('#interest_amount').val('');
		$('#interest_amount_all').val('');
		$('.loan_all_data').hide();
		$('.loan_data').hide();
		$('#other_desc').val('')
		$('.other_desc').hide();
	  
	}
}
/*function check_form_bk(){
	var text_alert = '';
	if($('#account_list').val()==''){
		text_alert += ' - รายละเอียดการชำระเงิน\n';
	}else if($('#account_list').val()=='15'){
		if($('#loan_id').val()==''){
			text_alert += ' - เลขที่สัญญา\n';
		}
	}
	if($('#amount').val()==''){
		text_alert += ' - จำนวนเงิน\n';
	}
	if(text_alert != ''){
		swal('กรุณากรอกข้อมูลต่อไปนี้',text_alert,'warning');
	}else{
		var account_list = $('#account_list').val();
		var account_list_text = $('#account_list :selected').text();
		var loan_id = $('#loan_id').val();
		var amount = $('#amount').val();
		$.ajax({  
			 url: base_url+"cashier/cal_receipt",
			 method:"post",  
			 data:{account_list:account_list, loan_id:loan_id, amount:amount, account_list_text:account_list_text},  
			 dataType:"text",  
			 success:function(result)  
			 {  
				
				obj = JSON.parse(result);	
				//console.log(obj);
				if(obj.result == 'error'){
					swal(obj.error_msg);
				}else{
					var table_data = '';
					table_data = '<tr class="table_data" id="list_'+i+'">';
						table_data += '<td align="left">'+obj.account_list_text+'</td>';
						table_data += '<td align="right">'+format_number(obj.principal_payment)+'</td>';
						table_data += '<td align="right">'+format_number(obj.interest)+'</td>';
						table_data += '<td align="right">'+format_number(obj.amount)+'</td>';
						table_data += '<td align="center"><a style="cursor:pointer" onclick="delete_list(\''+i+'\')">ลบ</a></td>';

						table_data += '<input type="hidden" name="account_list['+i+']" value="'+obj.account_list+'">';
						table_data += '<input type="hidden" name="loan_id['+i+']" value="'+obj.loan_id+'">';
						table_data += '<input type="hidden" name="loan_atm_id['+i+']" value="'+obj.loan_atm_id+'">';
						table_data += '<input type="hidden" name="principal_payment['+i+']" value="'+obj.principal_payment+'">';
						table_data += '<input type="hidden" name="interest['+i+']" value="'+obj.interest+'">';
						table_data += '<input type="hidden" class="amount" name="amount['+i+']" value="'+obj.amount+'">';
					table_data += '</tr>';
					$('#table_data').append(table_data);
					$('#value_null').hide();
					$('.table_footer').show();
					var sum_amount = 0;
					$('.amount').each(function(){
						sum_amount += parseFloat($(this).val());
					});
					$('#sum_amount').html(format_number(sum_amount));
					i++;
					$('#account_list').val('');
					$('#amount').val('');
				}
			 }  
		});  
	}
}*/
function delete_list(account_list){
	swal({
		title: "",
		text: "ท่านต้องการลบข้อมูลใช่หรือไม่?",
		type: "warning",
		showCancelButton: true,
		confirmButtonColor: '#DD6B55',
		confirmButtonText: 'ยืนยัน',
		cancelButtonText: "ยกเลิก",
		closeOnConfirm: true,
		closeOnCancel: true
	},
	function(isConfirm){
		if (isConfirm){
			$('#list_'+account_list).remove();
			var sum_amount = 0;
			$('.amount').each(function(){
				sum_amount += parseFloat($(this).val());
			});
			$('#sum_amount').html(addCommas(sum_amount));
			var j=0;
			$('.table_data').each(function(){
				j++;
			});
			if(j==0){
				$('#value_null').show();
				$('.table_footer').hide();
			}
		} 
	});
}
function after_submit(){
	$('#form2').submit();
	$('.table_data').remove();
	$('#sum_amount').html('0');
	$('#value_null').show();
	$('.table_footer').hide();
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

function choose_loan(id){
	var principal = $('#'+id+' :selected').attr('principal');
	var interest = $('#'+id+' :selected').attr('interest');
	var loan_interest_debt_total = $('#'+id+' :selected').attr('loan_interest_debt_total');
	var interest_non_pay = $('#'+id+' :selected').attr('interest_non_pay');
	var loan_principal_refrain = $('#'+id+' :selected').attr('data-principal-refrain');
	var loan_interest_refrain = $('#'+id+' :selected').attr('data-interest-refrain');
	$('#principal_amount').val(addCommas(principal));
	$('#interest_amount').val(addCommas(interest));
	$('#interest_debt').val(addCommas(loan_interest_debt_total));
	$('#interest_amount_all').val(addCommas(interest));
	$('#interest_non_pay').val(addCommas(interest_non_pay));
	$("#loan_principal_refrain").val(loan_principal_refrain);
	$("#loan_interest_refrain").val(loan_interest_refrain);

	if($('#deduct_type_all').is(':checked')) {
		$('#amount').val(addCommas(parseFloat(principal)+parseFloat(interest)))
	}

	if (loan_interest_refrain != "") {
		$('#interest_debt').val(0);
		$('#interest_debt').attr("disabled",true);
	} else {
		$('#interest_debt').attr("disabled",false);
	}

	check_pay_all();
}

function check_pay_all(){
	var amount = 0;
	var principal = removeCommas($('#principal_amount').val());
	var interest = removeCommas($('#interest_amount').val());
	if($('#deduct_type_all').is(':checked')){
		if(parseFloat(principal) > 0){
			if($('#deduct_type_all').is(':checked')){
				amount = parseFloat(principal) + parseFloat(interest);
			}else{
				amount = parseFloat(principal);
			}
			$('#amount').val(addCommas(amount));
			//$('#amount').attr('readonly',true);
		}else{
			$('#amount').val('0');
			//$('#amount').attr('readonly',false);
			$('#pay_all').attr('checked',false);
		}
	}else{
		if($('#deduct_type_interest').is(':checked')){
			$('#amount').val(addCommas(interest));
		} else {
			$('#amount').val(addCommas(principal));
		}
		//$('#amount').attr('readonly',false);
	}
	
}
function removeCommas(str) {
    return(str.replace(/,/g,''));
}
function addCommas(x){
	var val = numeral(x).value();
	val = numeral(val).format('0,0.00');
	console.log("is_numeric", val);
 	return val;
}
function open_modal(id){
	$('#'+id).modal('show');
}
function search_receipt(){
	var search_receipt_list = $('#search_receipt_list').val();
	var search_receipt_text = $('#search_receipt_text').val();
	var text_alert = '';
	if(search_receipt_list == ''){
		text_alert += '- เลือกข้อมูลที่ต้องการค้นหา\n';
	}
	if(search_receipt_text == ''){
		text_alert += '- กรอกข้อมูลที่ต้องการค้นหา\n';
	}
	if(text_alert == ''){
		$.ajax({  
			url: base_url+"cashier/search_receipt",
			method:"post",  
			data:{
				search_receipt_list:search_receipt_list, 
				search_receipt_text:search_receipt_text
			},  
			dataType:"text",  
			success:function(result)  
			{  
				$('#search_receipt_result').html(result);
			}
		});
	}else{
		swal('กรุณาระบุข้อมูลต่อไปนี้',text_alert,'warning');
	}
}

function format_the_number_decimal(ele){
	var value = $('#'+ele.id).val();
	value = value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');
	var num = value.split(".");
	var decimal = '';	
	var num_decimal = '';	
	if(typeof num[1] !== 'undefined'){
		if(num[1].length > 2){
			num_decimal = num[1].substring(0, 2);
		}else{
			num_decimal =  num[1];
		}
		decimal =  "."+num_decimal;
		
	}
	
	if(value!=''){
		if(value == 'NaN'){
			$('#'+ele.id).val('');
		}else{		
			value = (num[0] == '')?0:parseInt(num[0]);
			value = value.toLocaleString()+decimal;
			$('#'+ele.id).val(value);
		}			
	}else{
		$('#'+ele.id).val('');
	}
}
