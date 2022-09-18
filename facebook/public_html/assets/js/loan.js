var condition_garantor_id = "";
var value_check = "";
var garantor_condition = [];
var not_condition = [];
var template_garantor = [];
var operator = {
	'>': function (x, y) { return x > y },
	'>=': function (x, y) { return x >= y },
	'<': function (x, y) { return x < y },
	'<=': function (x, y) { return x <= y },
	'=': function (x, y) { return x == y },
	'!=': function (x, y) { return x != y }
}


var numberFormatOptions = { minimumFractionDigits: 2, maximumFractionDigits: 2};

$(document).on('change', '.validation', function(e) { 
	// alert("Change to " + this.value, this.id);
	blockUI();
	var thisValue = parseInt(removeCommas(this.value));
	var meta = $(this).data("meta");
	var key_optional = $(this).data("optional");
	var optional = removeCommas($("#"+key_optional).val());
	console.log("key_optional", key_optional)
	console.log("optional", optional)

	let ele = new Validation(e)
	ele.meta = meta;
	ele.loanTypeId = $("#loan_type_select").val();
	ele.memberId = $("#member_id").val();
	
	console.log(ele.meta);
	var value = ele.rule(optional);
	$(this).prop('max', value.valueMax);

	if(thisValue > value.valueMax){
		swal("ระบบแจ้งเตือน", "ไม่สามารถกรอกค่า ได้มากกว่า "+ (ele.is_numeric( value.valueMax ) ? addCommas(value.valueMax) : "" ) );
		if(ele.is_numeric(value.valueMax))
			$(this).val(addCommas(value.valueMax));
		else
			$(this).val(value.valueMax);
	}
	if(value.message)
		swal("ระบบแจ้งเตือน", (value.valueMax!="" ? "" : "เงื่อนไขไม่ถูกต้อง \n")+"\t\t"+value.message);
	console.log(thisValue);
	console.log("value=======: ", value);
});
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
function search_member_modal(id){
	$('#input_id').val(id);
	$('#search_member_loan_modal').modal('show');
}
function get_guarantee_person_data(member_id){
	$.ajax({
		 url:base_url+"/ajax/ajax_get_guarantee_person_data",
		 method:"post",
		 data:{ member_id: member_id},
		 dataType:"text",
		 success:function(data)
		 {
			$('#guarantee_person_data').html(data);
			$('#guarantee_person_data_modal').modal('show');
		 }
	});
}
function get_data(member_id, member_name , member_group){
	console.log("selected : ", member_id);
	// return;
	//var num_guarantee = $('#loan_rule_'+$('#loan_type').val()).attr('num_guarantee');
	$.post(base_url+"/ajax/get_member", 
			{	
				member_id: member_id,
				for_loan:'1',
				loan_type: $('#loan_type').val(),
				value_check: member_id,
				not_condition: () => {
					var arr = [];
					not_condition.forEach(element => {
						console.log(element)
						arr.push(element.value);
					});
					return arr.join(",");
				},
				condition_garantor_id: condition_garantor_id
			}
			, function(result){
				var obj = JSON.parse(result);
				console.log(obj);
				if(typeof obj === 'object' && obj !== null){
					console.log("is object");
					if(obj.check_id!=null){
						not_condition.push({key: member_id, value: obj.check_id});
					}
					
					result = obj.message;
				}

				var garantee_amount = obj.garantee_amount;

				if(result.text!=''){
					swal({
					  title: result.title,
					  text: result.text,
					  type: "warning",
					  showCancelButton: false,
					  closeOnConfirm: true
					},
					function(){
						
					});
				}else{
					var dupp_count = 0;
					$('.guarantee_person_id').each(function(){
						if($(this).val() == member_id){
							dupp_count++;
						}
					});
					if(dupp_count>0){
						swal({
						  title: "เกิดข้อผิดพลาด",
						  text: "ท่านไม่สามารถเลือกผู้ค้ำประกันซ้ำกันได้",
						  type: "warning",
						  showCancelButton: false,
						  closeOnConfirm: true
						},
						function(){
							
						});
					}else{
						var id = $('#input_id').val();
						$('#guarantee_person_id_'+id).val(member_id);
						$('#guarantee_person_name_'+id).val(member_name);
						$('#guarantee_person_dep_'+id).val(member_group);
						if(garantee_amount == '0'){
							var text_count_guarantee = garantee_amount;
						}else{
							var text_count_guarantee = '<a style="cursor:pointer" onclick="get_guarantee_person_data(\''+member_id+'\')">'+garantee_amount+'</a>';
						}
						$('#count_guarantee_'+id).html(text_count_guarantee);
						$('#btn_delete_'+id).show();
						$('#search_member_loan_modal').modal('hide');
						$('.guarantee_person_'+id).removeAttr('disabled');
						cal_guarantee_person();
					}
				}
			});
}
function cal_guarantee_person(){
	var count_guarantee_person = 0;
	$('.guarantee_person_id').each(function(){
		if($(this).val()!=''){
			count_guarantee_person++;
		}
	});
	var loan_amount = $('#loan_amount').val();
	loan_amount = removeCommas(loan_amount);
	loan_amount = parseInt(loan_amount);
	var per_person = loan_amount/count_guarantee_person;
	per_person = per_person.toFixed(2);
	per_person = parseFloat(per_person);
	per_person = per_person.toLocaleString();
	$('.guarantee_person_id').each(function(){
		var guarantee_person_id = $(this).attr('guarantee_person_id');
		if($(this).val()!='' && $('#loan_amount').val()!=''){
			$('#guarantee_person_amount_'+guarantee_person_id).val(per_person);
		}else{
			$('#guarantee_person_amount_'+guarantee_person_id).val('');
		}
	});
}

function create_input_garantor(){
	$("#sec_garantor").empty();
	if(garantor_condition == null) return false;
	template_garantor = [];
	var loan_amount = $('#loan_amount').val();
	loan_amount = removeCommas(loan_amount);
	loan_amount = parseInt(loan_amount);
	garantor_condition.forEach(element => {
		// console.log(element);
		var condition = element.condition
		var check = true;
		var count_condition = 0;
		condition.forEach(cond => {
			var operator = {
				'>': function (x, y) { return x > y },
				'>=': function (x, y) { return x >= y },
				'<': function (x, y) { return x < y },
				'<=': function (x, y) { return x <= y },
				'=': function (x, y) { return x == y },
				'!=': function (x, y) { return x != y }
			}
			console.log("cond: ",cond);
			// console.log(loan_amount, cond.operation, cond.value);
			if(cond.meta_condition_id==="0"){
				console.log("true");
			}else{
				if(operator[cond.operation](loan_amount, parseInt(cond.value))){
					console.log("true");
				}else{
					console.log("false");
					check = false;
				}
			}
			
		});

		if(check){
			count_condition++;
			console.log("USE : ", element.detail_text);
			condition_garantor_id = element.col_id;
			var garantor = element.garantor_condition;
			console.log(garantor);
			var j = 1;
			var tmp = "";
			garantor.forEach(ele_garantor => {
				var html = blue_print.replace(/no\[\]/g, j);
				j++;
				tmp += html;
				
			});
			template_garantor.push({
				html: tmp,
				title: element.detail_text
			});

		}

	});

	if(template_garantor.length == 1){
		$("#sec_garantor").append(template_garantor[0].html);
	}else if(template_garantor.length > 1){
		console.log("over");
		swal("โปรดเลือกเงื่อนไขผู้ค้ำ");
		$('#modal_select_garantor').modal('show');

		var html = "<ul>";
		var c = 0;
		template_garantor.forEach(element => {
			console.log(element);
			html += `
				<li><a href=`+"#"+` data-template_garantor_id = '`+c+`' onclick='selected_garantor(`+c+`)'>`+element.title+`</a></li>
			`;
			c++;
		});
		html += '</ul>';
		$("#modal_select_garantor_body").html(html);
	}else{

	}
	console.log("template: ",template_garantor);
	// $("#sec_garantor").append(html);
}

function selected_garantor(key){
	console.log("click", key);
	console.log(template_garantor[key]);
	$("#sec_garantor").html(template_garantor[key].html);
}

function delete_guarantee_person(id){
	var member_id = $('#guarantee_person_id_'+id).val();
	console.log("delete", member_id);
	for( var i = not_condition.length-1; i--;){
		if ( not_condition[i].key == member_id) not_condition.splice(i, 1);
	}
	console.log(not_condition);
	$('#guarantee_person_id_'+id).val('');
	$('#guarantee_person_name_'+id).val('');
	$('#guarantee_person_dep_'+id).val('');
	$('#count_guarantee_'+id).html('');
	$('#btn_delete_'+id).hide();
	$('.guarantee_person_'+id).attr('disabled','true');
	cal_guarantee_person();
}

function format_the_number(ele){
	var value = $(ele).val();
	value = value.replace(/[^0-9]/g, '');	
	if(value!=''){
		if(value == 'NaN'){
			$(ele).val('');
		}else{		
			value = parseInt(value);
			value = value.toLocaleString();
			$(ele).val(value);
		}			
	}else{
		$(ele).val('');
	}
}

function format_the_number_decimal(ele){
	var value = $(ele).val();
	if(value === "undefined" || value === "" || value === "NaN"){
		$(ele).val(format_number(0));
		return;
	}
	console.log(value);
	value = value.split(",").join("");
	$(ele).val(format_number(value));
}

function check_trem_of_loan(){
	 var member_id = $('#member_id').val();
	 var loan_type = $('#loan_type').val();
	 var loan_amount = $('#loan_amount').val();
	 var share_total = $('#share_total').val();
	 var share_amount = $('#guarantee_amount_2').val();
	 var period_amount = $('#period_amount').val();
	 var fund_total = $('#guarantee_other_price_2').val();
	 var last_date_period = $('#last_date_period').val();
	 var first_pay = $('#money_period_1').val();
	 //console.log(last_date_period);
	 if($('#guarantee_1').is(':checked')){
		 var person_guarantee = '1';
	 }else{
		 var person_guarantee = '';
	 }
	 if($('#guarantee_2').is(':checked')){
		 var share_guarantee = '1';
	 }else{
		 var share_guarantee = '';
	 }
		
		 $.post(base_url+"/loan/ajax_check_term_of_loan", 
			{	
				member_id: member_id,
				loan_type: loan_type,
				loan_amount: loan_amount,
				share_amount: share_amount,
				share_total: share_total,
				period_amount:period_amount,
				fund_total:fund_total,
				person_guarantee:person_guarantee,
				share_guarantee:share_guarantee,
				last_date_period:last_date_period,
				first_pay:first_pay
			}
			, function(result){
				obj = JSON.parse(result);
				if(obj.result=='success'){
					//console.log(result);
					submit_form();
				}else{
					//swal('ท่านไม่สามารถกู้เงินได้เนื่องจาก', result , 'warning');
					$('#check_term_of_loan_result').html(obj.text_return);
					$('#check_term_of_loan_result_modal').modal('show');
				}
			});
 }
 function submit_form(){
	 $("#submit_button").attr('disabled','disabled');
	 $('#form_normal_loan').submit();
 }
function check_submit(){
	var alert_text = '';
	if($('#loan_amount').val()==''){
		alert_text += '- จำนวนเงินที่ขอกู้\n';
	}
	if($('#loan_reason').val()==''){
		alert_text += '- เหตุผลการกู้\n';
	}
	//if($('#salary').val()==''){
		//alert_text += '- เงินเดือน\n';
	//}
	if($('#guarantee_1').is(':checked')){
		if($('#guarantee_person_name_1').val() == '' && $('#guarantee_person_name_2').val() == '' && $('#guarantee_person_name_3').val() == '' && $('#guarantee_person_name_4').val() == ''){
			alert_text += '- ผู้ค้ำประกัน\n';
		}
	}
	if($('#guarantee_2').is(':checked')){
		if($('#guarantee_amount_2').val() == ''){
			alert_text += '- จำนวนหุ้นสะสม\n';
		}
	}
	if($('#guarantee_3').is(':checked')){
		if($('#guarantee_amount_3').val() == ''){
			alert_text += '- จำนวนกองทุนสำรองเลี้ยงชีพ\n';
		}
	}
	if($('#already_cal').val()!='1'){
		alert_text += '- กรุณาคำนวณการส่งค่างวด\n';
	}
	
	if($('#petition_number').val().trim()==''){
		alert_text += '- เลขที่คำร้อง\n';
	}

	if($('#transfer_type_4').is(':checked')){
		let amount = 0;

		const estimate = parseFloat(removeCommas($('#estimate_receive_money').val()));
		$('.multi-cheque-contain .card-item').each(function(i){
			if($(this).find('.cheque-receiver').val() === "") {
				alert_text += `- กรุณาระบุชื่อผู้รับเงิน/สถาบันการเงิน ลำดับที่ ${i + 1} \n`;
			}
			if($(this).find('.cheque-amount').val() === "0.00" || $(this).find('.cheque-amount').val() === ""){
				alert_text += `- กรุณาระบุจำนวนเงินที่ต้องการของเช็ค ลำดับที่ ${i + 1} \n`;
			}
			amount += parseFloat(removeCommas($(this).find('.cheque-amount').val()));
		});

		console.log(amount, estimate);
		if(amount !== estimate){
			alert_text += `- รายการเช็คจ่ายเงินกู้ไม่ตรงกับยอดเงินที่จะได้รับโดยประมาณ\n`;
		}
	}

	if(alert_text!=''){
		$("#submit_button").removeAttr('disabled');
		swal('กรุณากรอกข้อมูลต่อไปนี้' , alert_text , 'warning');
	}else{
		check_trem_of_loan();
	}
}
function re_already_cal(){
	$('#already_cal').val('');
}

function chkNumber(ele){
	var vchar = String.fromCharCode(event.keyCode);
	if ((vchar<'0' || vchar>'9') && (vchar != '.')) return false;
	ele.onKeyPress=vchar;
}
function change_table(id){
	//$('.hidden_table').hide();
	//$('.btn_show').attr('class','btn btn-primary btn_show');
	if($('#show_status_'+id).val()==''){
		$('#table_'+id).show();
		$('#button_'+id).attr('class','btn btn-success btn_show');
		$('#show_status_'+id).val('1');
	}else{
		$('#table_'+id).hide();
		$('#button_'+id).attr('class','btn btn-primary btn_show');
		$('#show_status_'+id).val('');
	}
	
} 
function printContent(el){
    var restorepage = document.body.innerHTML;
    var printcontent = document.getElementById(el).innerHTML;
    document.body.innerHTML = printcontent;
    window.print();
    document.body.innerHTML = restorepage;
}
function printElem(divId) {
    var content = document.getElementById(divId).innerHTML;
    var mywindow = window.open('', 'Print', 'height=600,width=800');

    mywindow.document.write('<html><head><title>Print</title>');
    mywindow.document.write('</head><body ><center>');
    mywindow.document.write(content);
    mywindow.document.write('</center></body></html>');

    mywindow.document.close();
    mywindow.focus()
    mywindow.print();
    mywindow.close();
    return true;
}
function copy_value(from_ele, to_ele){
	$('#'+to_ele).val($('#'+from_ele).val());
}
function close_modal(id){
	$('#'+id).modal('hide');
}
function cal_share_result(share_num){
	 var share_value = $('#share_value').val();
	 if(share_num!=''){
		$('.share_price').val(parseFloat(share_num)*parseFloat(share_value));
	 }else{
		$('.share_price').val('');
	 }
 }
 function change_modal(loan_type_edit = null){
	 if(loan_type_edit == null){
		var loan_type = $('#loan_type_select').val();
		$('#loan_id').val('');
		$('#petition_number').val('');
		$('#loan_amount').val('');
		$('#loan').val('');
		$('#salary').val('');
		$('#loan_reason').val('');
		$('#interest_per_year').val('');
		$('#period_amount').val('');
		$('#date_start_period').val('');
		$('#date_start_period_label').val('');
		$('#date_period_1').val('');
		$('#money_period_1').val('');
		$('#date_period_2').val('');
		$('#money_period_2').val('');
		$('#period_type').val('1');
		$('#period').val('');
		$('#period_old').val('');
		$('#day').val('');
		$('#month').val('');
		$('#year').val('');
		$('#contract_number').val('');
		$('.contract_number').hide();
		// $('#school_benefits').val('');
		// $('#saving').val('');
		// $('#ch_p_k').val('');
		// $('#pension').val('');
		//$('.loan_cost').val('');
		$('.loan_deduct').val('');
		$('.prev_loan_amount').val('');
		$('.estimate_value').val('');

		$('#guarantee_1').removeAttr('checked');
		$('#guarantee_2').removeAttr('checked');
		$('.guarantee_2').attr('disabled','disabled');
		//$('#guarantee_amount_2').val('');
		//$('#guarantee_price_2').val('');
		$('#guarantee_other_price_2').val('');
			
		for (i = 1; i <= 4; i++) {
			$('#guarantee_person_id_'+i).val('');
			$('#guarantee_person_name_'+i).val('');
			$('#guarantee_person_dep_'+i).val('');
			$('#guarantee_person_contract_number_'+i).val('');
			$('#guarantee_person_amount_'+i).val('');
		}	
		$('#result_wrap').html('');
		$('#btn_show_file').hide();
		$('#submit_button').html('บันทึกคำร้อง');
		
		$('#real_estate_position_1').val('');
		$('#real_estate_position_2').val('');
		$('#province_id').val('');
		change_province('province_id','amphure','amphur_id','district','district_id','','');
		//$('#amphur_id').val(obj.coop_loan_guarantee_real_estate.amphur_id);
		//$('#district_id').val(obj.coop_loan_guarantee_real_estate.district_id);
		$('#land_number').val('');
		$('#survey_page').val('');
		$('#deed_number').val('');
		$('#deed_book').val('');
		$('#deed_page').val('');
		$('#rai').val('');
		$('#ngan').val('');
		$('#tarangwah').val('');
		$('.prev_loan_checkbox').attr('checked',false);
		$('.prev_loan_amount').val('');
		$('input[name="guarantee_saving[]"').attr('checked',false);
	 }else{
		 var loan_type = loan_type_edit;
	 }
	 
	 if(loan_type == ''){
		 swal('กรุณาเลือกชื่อสินเชื่อ');
	 }else{
		 var share_total = $('#share_total').val();
		 var member_id = $('#member_id').val();
		 var createdatetime = $('#createdatetime').val();
		 $.post(base_url+"/loan/ajax_check_term_of_loan_before", 
			{	
				member_id:member_id,
				loan_type: loan_type,
				share_total:share_total,
				createdatetime: createdatetime
			}
			, function(result){
				obj = JSON.parse(result);
				garantor_condition = obj.condition_garantor;
				if(obj.share_guarantee == '1' || obj.person_guarantee == '1' || obj.real_estate_guarantee == '1' || obj.deposit_guarantee == '1'){
					 $('#type_1').show();
					 $('#type_2').hide();
					 if( obj.person_guarantee == '1'){
						 $('#type_1_1').show();
						 if(loan_type_edit==null){
							$('#guarantee_1').attr('checked',true);
							choose_guarantee('guarantee_1');
						 }
					 }else{
						 $('#type_1_1').hide();
						 $('#guarantee_1').attr('checked',false);
					 }
					 if( obj.share_guarantee == '1'){
						 $('#type_1_2').show();
						 if(loan_type_edit==null){
							$('#guarantee_2').attr('checked',true);
							choose_guarantee('guarantee_2');
						 }
						
					 }else{
						 $('#type_1_2').hide();
						 $('#guarantee_2').attr('checked',false);
					 }
					 
					 if( obj.deposit_guarantee == '1'){
						 $('#type_1_3').show();
						 if(loan_type_edit==null){
							$('#guarantee_3').attr('checked',true);
							choose_guarantee('guarantee_3');
						 }
						
					 }else{
						 $('#type_1_3').hide();
						 $('#guarantee_3').attr('checked',false);
					 }

					 if( obj.real_estate_guarantee == '1'){
						$('#type_1_4').show();
						if(loan_type_edit==null){
						   $('#guarantee_4').attr('checked',true);
						   choose_guarantee('guarantee_4');
						}

					}else{
						$('#type_1_4').hide();
						$('#guarantee_4').attr('checked',false);
					}
				 }
				 $('#loan_type').val(loan_type);
				 $('#type_name').html($('#loan_rule_'+loan_type).attr('type_name'));
				 $('.interest_rate').val($('#loan_rule_'+loan_type).attr('interest_rate'));
				 //$('#deduct_share').val(obj.loan_deduct_share); 
				 
				 //console.log("loan_deduct_share="+obj.loan_deduct_share);
				 //console.log("debug="+obj.debug);
				 
				 //$('#deduct_pay_prev_loan').val(obj.prev_loan_amount_balance); 
				if(obj.result=='success'){
					open_modal('normal_loan');
					guarantee_editor();
				}else{
					//swal('ท่านไม่สามารถกู้เงินได้เนื่องจาก', obj.text_return,'warning');
					if(loan_type_edit == null){
						$('#check_term_of_loan_before_result').html(obj.text_return);
						$('#check_term_of_loan_before_result_modal').modal('show');
					}
				}
			});
	 }
 }
 function open_modal(id){
	 $('#'+id).modal('show');
	 $('#check_term_of_loan_before_result_modal').modal('hide');
 }
 function search_member(id){
	 var member_id = $('#guarantee_person_id_'+id).val();
	  var loan_type = $('#loan_type').val();
	 $('.btn_search_member').hide();
	 $('.loading_icon').show();
	 if(member_id !=''){
		 $.post(base_url+"/ajax/get_member", 
			{	
				member_id: member_id,
				for_loan:'1',
				loan_type:loan_type
			}
			, function(result){
				if(result=='over_guarantee'){
					swal('ไม่สามารถใช้สมาชิกท่านนี้ค้ำประกันได้ เนื่องจาก' , 'สมาชิกที่ท่านเลือกได้ค้ำประกันเงินกู้เต็มจำนวนที่กำหนดแล้ว', 'warning');
				}else{
					var obj = JSON.parse(result);
					//console.log(obj);
					if(obj.member_name == ' '){
						swal('ไม่พบข้อมูล');
					}
					$('#guarantee_person_id_'+id).val(obj.member_id);
					$('#guarantee_person_name_'+id).val(obj.member_name);
					$('#guarantee_person_dep_'+id).val(obj.member_group_name);
					$('.btn_search_member').show();
					$('.loading_icon').hide();
				}
			});
	 }else{
		 swal('กรุณากรอกรหัสสมาชิกที่ต้องการค้นหา');
		 $('.btn_search_member').show();
		$('.loading_icon').hide();
	 }
 }

 function choose_guarantee(id){
	 if($('#'+id).is(':checked')){
		 $('.'+id).removeAttr('disabled');
	 }else{
		 $('.'+id).attr('disabled','true');
	 }
	 
	check_share();
 }
 function del_loan(loan_id, member_id, status_to){
	 if(status_to=='1'){
		 var title = 'ท่านต้องการยกเลิกการยกเลิกรายการใช่หรือไม่';
	 }else{
		 var title = 'ท่านต้องการยกเลิกคำขอกู้เงินใช่หรือไม่';
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
			$.ajax({
				url: base_url+'/loan/coop_loan_delete',
				method: 'GET',
				data: {
					'loan_id': loan_id,
					'status_to': status_to,
					'member_id': member_id
				},
				success: function(msg){
				  // console.log(msg); return false;
					if(msg == 1){
					  document.location.href = base_url+'loan?member_id='+member_id;
					}else{

					}
				}
			});
        } else {
			
        }
    });
 }
 function show_period_table(loan_id){
	$.post( base_url+"/loan/ajax_coop_loan_period_table", 
	{	
		loan_id: loan_id
	}
	, function(result){
		$('.period_table').html(result);
		$('#period_table').modal('show');
	});
}
function show_file(){
	 $('#show_file_attach').modal('show');
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
			$.post( base_url+"/loan/ajax_delete_loan_file_attach", 
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

function change_type(){
	$.ajax({
		url: base_url+'loan/change_loan_type',
		method: 'POST',
		data: {
			'type_id': $('#loan_type_choose').val()
		},
		success: function(msg){
		   $('#loan_type_select').html(msg);
		}
	});		
	$('#type_name').val($('#type_id :selected').text());
}

var edit_data = {};
function edit_loan(loan_id , loan_type){
	 $('#deduct_insurance').prop('readonly', true);
	 selected_loan_type(loan_type);
	 $.post( base_url+"/loan/ajax_get_loan_data",
			{	
				loan_id: loan_id
			}
			, function(result){selected_loan_type(loan_type);
				var obj = JSON.parse(result);
				edit_data = obj;
				var type_deduct = obj.coop_loan.type_deduct;
				if(type_deduct == '2'){
					$('#type_deduct_2').attr('checked', true);
				}else{
					$('#type_deduct_1').attr('checked', true);
				}
				var transfer_type = obj.coop_loan.transfer_type;
				//console.log(transfer_type);
				//console.log(obj);
				//console.log('period_amount='+obj.coop_loan.period_amount);
				//console.log('period_amount_bath='+obj.coop_loan.period_amount_bath);
				$('.transfer_type').attr('checked', false);
				$('#transfer_type_'+transfer_type).attr('checked', true);
				$('#transfer_bank_account_id').val(obj.coop_loan.transfer_bank_account_id);
				$('#transfer_bank_id').val(obj.coop_loan.transfer_bank_id);
				$('#transfer_account_id').val(obj.coop_loan.transfer_account_id);
				choose_transfer_type();
				
				$('#loan_id').val(loan_id);
				$('#petition_number').val(obj.coop_loan.petition_number);
			 	$('#loan_amount').val(obj.coop_loan.loan_amount).trigger('click');
				$('#loan').val(obj.coop_loan.loan_amount);
				$('#salary').val(obj.coop_loan.salary);
				$('#loan_reason').val(obj.coop_loan.loan_reason);
				$('#interest_per_year').val(obj.coop_loan.interest_per_year);
				$('#period_amount').val(obj.coop_loan.period_amount);
				$('#period_amount_bath').val(obj.coop_loan.period_amount_bath);
				$('#date_start_period').val(obj.coop_loan.date_start_period);
				$('#date_start_period_label').val(obj.coop_loan.date_start_period);
				$('#date_period_1').val(obj.coop_loan.date_period_1);
				$('#money_period_1').val(obj.coop_loan.money_period_1);
				$('#date_period_2').val(obj.coop_loan.date_period_2);
				$('#money_period_2').val(obj.coop_loan.money_period_2);
				$('#period_type').val(obj.coop_loan.period_type);
				$('#period').val(obj.coop_loan.period_amount);
				$('#period_old').val(obj.coop_loan.period_amount);
				$('#day').val(obj.coop_loan.day_start);
				$('#month').val(obj.coop_loan.month_start);
				$('#year').val(obj.coop_loan.year_start);
				$('#pay_type').val(obj.coop_loan.pay_type);
				$('#contract_number').val(obj.coop_loan.contract_number);
				$('#interest').val(obj.coop_loan.interest_per_year);
				$('#createdatetime').val(obj.coop_loan.createdatetime);

				var key = Object.keys(obj.coop_loan_cost);
				const map = new Map(Object.entries(obj.coop_loan_cost));
				for(var index in key){
					$('#'+key[index]).val(map.get(key[index]));
					//console.log(key[index], map.get(key[index]));
				}

			  /*$('#school_benefits').val(obj.coop_loan_cost.school_benefits);
				$('#saving').val(obj.coop_loan_cost.saving);
				$('#ch_p_k').val(obj.coop_loan_cost.ch_p_k);
				$('#pension').val(obj.coop_loan_cost.pension);
				$('#k_b_k').val(obj.coop_loan_cost.k_b_k);
				$('#other').val(obj.coop_loan_cost.other);*/
				$('#is_compromise').val(obj.is_compromise);
				if(obj.coop_loan.contract_number!=''){
					$('.contract_number').show();
				}
				if(obj.coop_loan.loan_status == '1'){
					$('#submit_button').html('บันทึกการกู้เงิน');
				}else{
					$('#submit_button').html('บันทึกคำร้อง');
				}
				var date_receive_money_arr = obj.coop_loan_deduct_profile.date_receive_money.split('-');
				var date_receive_money = date_receive_money_arr[2]+'/'+date_receive_money_arr[1]+'/'+(parseInt(date_receive_money_arr[0])+543);
				$('#date_receive_money').val(date_receive_money);
				$('#date_first_period').val(obj.coop_loan_deduct_profile.date_first_period);
				$('#first_interest').val(obj.coop_loan_deduct_profile.first_interest);
				$('#estimate_receive_money').val(obj.coop_loan_deduct_profile.estimate_receive_money);
				//$('#estimate_receive_money').val(addCommas(obj.coop_loan_deduct_profile.estimate_receive_money));
			 	$('.display#estimate-money').val(addCommas(parseFloat(obj.coop_loan_deduct_profile.estimate_receive_money)));
				
				for(var key in obj.coop_loan_deduct){
					var loan_deduct_amount = obj.coop_loan_deduct[key].loan_deduct_amount;
					$('#'+obj.coop_loan_deduct[key].loan_deduct_list_code).val(loan_deduct_amount.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","));
					//$('#'+obj.coop_loan_deduct[key].loan_deduct_list_code).val(obj.coop_loan_deduct[key].loan_deduct_amount);
				}
				
				for(var key in obj.coop_loan_financial_institutions){
					$('#financial_institutions_name_'+key).val(obj.coop_loan_financial_institutions[key].financial_institutions_name);
					$('#financial_institutions_amount_'+key).val(addCommas(obj.coop_loan_financial_institutions[key].financial_institutions_amount));
				}
				$('.prev_loan_checkbox').attr('checked',false);
				$('.prev_loan_amount').val('');
			 	var interest = 0, principal = 0;

			 	//console.log('deduct', obj.coop_loan_prev_deduct);

				for(var key in obj.coop_loan_prev_deduct){
					var loan_prev_deduct = obj.coop_loan_prev_deduct[key];
					principal += parseFloat(loan_prev_deduct.pay_amount) - parseFloat(loan_prev_deduct.interest_amount);
					interest += parseFloat(loan_prev_deduct.interest_amount);

					$('.prev_loan_checkbox').each(function(){
						if(obj.coop_loan_prev_deduct[key].ref_id == $(this).attr('ref_id') && obj.coop_loan_prev_deduct[key].data_type == $(this).attr('data_type')){
							var index = $(this).attr('attr_index');
							$(this).attr('checked',true);
							if(obj.coop_loan_prev_deduct[key].pay_type == 'principal'){
								$('#prev_loan_pay_type_1_'+index).attr('checked',true);
								$('#prev_loan_pay_type_2_'+index).attr('checked',false);
							}else if(obj.coop_loan_prev_deduct[key].pay_type == 'all'){
								$('#prev_loan_pay_type_1_'+index).attr('checked',false);
								$('#prev_loan_pay_type_2_'+index).attr('checked',true);
							}
							$('#prev_loan_amount_'+index).val(addCommas(obj.coop_loan_prev_deduct[key].pay_amount));
						}
					});
				}
				 $("#joker-interest").val(interest.toLocaleString('en', numberFormatOptions));
				 $("#joker-principal").val(principal.toLocaleString('en', numberFormatOptions));
				
				for(var key in obj.coop_loan_guarantee){
					//console.log(obj.coop_loan_guarantee[key].guarantee_type);
					$('#guarantee_'+obj.coop_loan_guarantee[key].guarantee_type).attr('checked','checked');
					$('.guarantee_'+obj.coop_loan_guarantee[key].guarantee_type).removeAttr('disabled');
					$('#guarantee_amount_'+obj.coop_loan_guarantee[key].guarantee_type).val(obj.coop_loan_guarantee[key].amount);
					$('#guarantee_price_'+obj.coop_loan_guarantee[key].guarantee_type).val(obj.coop_loan_guarantee[key].price);
					$('#guarantee_other_price_'+obj.coop_loan_guarantee[key].guarantee_type).val(obj.coop_loan_guarantee[key].other_price);
				}

			 	guarantee_editor().then(r => {
			 		console.debug(r);
				});
				
				if(obj.coop_loan_guarantee_real_estate != null){
					$('#real_estate_position_1').val(obj.coop_loan_guarantee_real_estate.real_estate_position_1);				
					$('#real_estate_position_2').val(obj.coop_loan_guarantee_real_estate.real_estate_position_2);
					$('#province_id').val(obj.coop_loan_guarantee_real_estate.province_id);
					change_province('province_id','amphure','amphur_id','district','district_id',obj.coop_loan_guarantee_real_estate.amphur_id,obj.coop_loan_guarantee_real_estate.district_id);
					//$('#amphur_id').val(obj.coop_loan_guarantee_real_estate.amphur_id);
					//$('#district_id').val(obj.coop_loan_guarantee_real_estate.district_id);
					$('#land_number').val(obj.coop_loan_guarantee_real_estate.land_number);
					$('#survey_page').val(obj.coop_loan_guarantee_real_estate.survey_page);
					$('#deed_number').val(obj.coop_loan_guarantee_real_estate.deed_number);
					$('#deed_book').val(obj.coop_loan_guarantee_real_estate.deed_book);
					$('#deed_page').val(obj.coop_loan_guarantee_real_estate.deed_page);
					$('#rai').val(obj.coop_loan_guarantee_real_estate.rai);
					$('#ngan').val(obj.coop_loan_guarantee_real_estate.ngan);
					$('#tarangwah').val(obj.coop_loan_guarantee_real_estate.tarangwah);
				}
				var txt_file_attach = '<table width="100%">';
				var i=1;
				for(var key in obj.coop_loan_file_attach){
					txt_file_attach += '<tr class="file_row" id="file_'+obj.coop_loan_file_attach[key].id+'">\n';
					//txt_file_attach += '<td align="center" width="10%">'+i+'. </td>\n';
					txt_file_attach += '<td><a href="'+base_url+'/assets/uploads/loan_attach/'+obj.coop_loan_file_attach[key].file_name+'" target="_blank">'+obj.coop_loan_file_attach[key].file_old_name+'</a></td>\n';
					txt_file_attach += '<td style="color:red;font-size: 20px;cursor:pointer;" align="center" width="10%"><span class="icon icon-ban" onclick="del_file(\''+obj.coop_loan_file_attach[key].id+'\')"></span></td>\n';
					txt_file_attach += '</tr>\n';
					i++;
				}
				txt_file_attach += '</table>';
				$('#show_file_space').html(txt_file_attach);
				if(i>1){
					$('#btn_show_file').show();
				}

				// guarantee_saving
				if(obj.guarantee_saving != null){
					obj.guarantee_saving.forEach(element => {
						$("#guarantee_saving_"+element.account_id).prop('checked', true);
					});
				}
				cal();				
			});
	 //alert(loan_type);		
	 change_modal(loan_type);
	 $('#normal_loan').modal('show');
 }
 function send_debt_settlement(loan_id){
	 swal({
        title: "",
        text: "ท่านต้องการสร้างสัญญากู้ใหม่ให้แก่ผู้ค้ำประกันใช่หรือไม่?",
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
			document.location.href = base_url+'loan/debt_settlement/'+loan_id;
        } else {
			
        }
    });
 }
 function print_estimate(){
	var member_id = $('#member_id').val();
	var loan_amount = $('#loan').val();
	var deduct_pay_prev_loan = $('#deduct_pay_prev_loan').val();
	var deduct_share = $('#deduct_share').val();
	var deduct_blue_deposit = $('#deduct_blue_deposit').val();
	var deduct_insurance = $('#deduct_insurance').val();
	var deduct_person_guarantee = $('#deduct_person_guarantee').val();
	var deduct_loan_fee = $('#deduct_loan_fee').val();

	var second_summonth = $('#second_summonth').val();
	var pay_per_month = $('#first_pay').val();
	if(second_summonth  = '31'){
		pay_per_month =  $('#second_pay').val();
	 }else{
		pay_per_month =   $('#first_pay').val();
	 }
	var date_receive_money = $('#date_receive_money').val();
	var date_first_period_label = $('#date_first_period_label').val();
	var first_interest = $('#first_interest').val();
	var estimate_receive_money = $('#estimate_receive_money').val();
	var pay_type = $('#pay_type').val();
	var buy_s_s_o_k = $('#buy_s_s_o_k').val();
	var buy_ch_s_o = $('#buy_ch_s_o').val();
	var buy_s_o_s_p = $('#buy_s_o_s_p').val();
	var buy_share = $('#buy_share').val();
	var deduct_loan_other_buy = $('#deduct_loan_other_buy').val();
	var param = "member_id="+member_id+"&loan_amount="+loan_amount+"&deduct_pay_prev_loan="+deduct_pay_prev_loan+"&deduct_share="+deduct_share+"&deduct_blue_deposit="+deduct_blue_deposit+"&deduct_insurance="+deduct_insurance+"&deduct_person_guarantee="+deduct_person_guarantee+"&deduct_loan_fee="+deduct_loan_fee+"&pay_per_month="+pay_per_month+"&date_receive_money="+date_receive_money+"&date_first_period_label="+date_first_period_label+"&first_interest="+first_interest+"&estimate_receive_money="+estimate_receive_money+"&pay_type="+pay_type+"&buy_s_s_o_k="+buy_s_s_o_k+"&buy_ch_s_o="+buy_ch_s_o+"&buy_s_o_s_p="+buy_s_o_s_p+"&buy_share="+buy_share+"&deduct_loan_other_buy="+deduct_loan_other_buy
	window.open(base_url+'report_loan_data/coop_report_loan_deduct_tmp?'+param, '_blank');
 }
 function removeCommas(str) {
	str = str || "";
    return(str.replace(/,/g,''));
}
function addCommas(x){
	x = x || "";
  	return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}
function check_share(){
	
}
function check_loan_deduct(){
		var loan_type = $('#loan_type').val();
		var member_id = $('#member_id').val();
		var loan_amount = removeCommas($('#loan_amount').val());
		var deduct_pay_prev_loan = removeCommas($('#deduct_pay_prev_loan').val());
		var prev_checkbox_array = [];
		var prev_data_type_array = [];
		var prev_pay_type_array = [];
		$('.prev_loan_checkbox').each(function(){
			var index = $(this).attr('attr_index');
			var data_type = $(this).attr('data_type');
			if($(this).is(':checked')){
				prev_checkbox_array.push($('#prev_loan_checkbox_'+index).val());
				prev_data_type_array.push(data_type);
				if($('#prev_loan_pay_type_1_'+index).is(':checked')){
					prev_pay_type_array.push($('#prev_loan_pay_type_1_'+index).val());
				}else if($('#prev_loan_pay_type_2_'+index).is(':checked')){
					prev_pay_type_array.push($('#prev_loan_pay_type_2_'+index).val());
				}
			}
		});
	
		$.post(base_url+"/loan/get_loan_deduct", 
		{	
			loan_type:loan_type,
			member_id:member_id,
			loan_amount:loan_amount,
			deduct_pay_prev_loan:deduct_pay_prev_loan,
			prev_checkbox_array:prev_checkbox_array,
			prev_pay_type_array:prev_pay_type_array,
			prev_data_type_array:prev_data_type_array
		}
		, function(result){
			obj = JSON.parse(result);
			//console.log(obj);
			$('#deduct_person_guarantee').val(obj.percent_guarantee);
			// $('#deduct_loan_fee').val(obj.loan_fee);
			$('#guarantee_amount_2').val(obj.share_collect);
			$('#guarantee_price_2').val(obj.share_collect_value);
			
				if($('#guarantee_2').is(':checked')){
					$.post(base_url+"/loan/get_share_or_blue_acc", 
					{	
						loan_type:loan_type,
						member_id:member_id,
						loan_amount:loan_amount
					}
					, function(result){
						if($('#type_deduct_1').is(':checked')){
							$("#deduct_share").val(addCommas(result));
							$("#deduct_blue_deposit").val("");
						}else{
							$("#deduct_share").val("");
							$("#deduct_blue_deposit").val(addCommas(result));
						}
						change_prev_loan_pay_type();
						cal_estimate_money();
						check_life_insurance();
					});
				}else{
					$('#deduct_share').val('');
					change_prev_loan_pay_type();
					cal_estimate_money();
					check_life_insurance();
				}
		});
		$('#updatetimestamp').val($('#datenow_is').val());
}
function update_salary(){
	$.post(base_url+"/loan/update_salary", 
	{	
		member_id: $('#member_id').val(),
		salary: $('#update_salary').val(),
		other_income: $('#update_other_income').val()
	}
	, function(result){
		close_modal('update_salary_modal');
	});
}

function change_prev_loan_pay_type(){
	//@start หายอดหักกลบ กรณีมีการเปลี่ยนแปลงวันที่ทำรายการได้	

	$.post(base_url+"/loan/check_prev_loan", 
	{	
		member_id: $('#member_id').val(),
		createdatetime: $('#date_receive_money').val()
	}
	, function(result){
		var obj = JSON.parse(result);
		var principal = 0;
		var interest = 0;
		for(var key in obj.prev_loan_active){
			$('.prev_loan_checkbox').each(function(){
				if(obj.prev_loan_active[key].ref_id == $(this).attr('ref_id')){
					var index = $(this).attr('attr_index');
						var prev_loan_total = parseFloat(obj.prev_loan_active[key].prev_loan_total);
						var prev_loan_interest = parseFloat(obj.prev_loan_active[key].interest);
						var principal_without_finance_month =  parseFloat(obj.prev_loan_active[key].principal_without_finance_month);

						if ($(this).is(':checked')) {
							if ($('#prev_loan_pay_type_1_' + index).is(':checked')) {
								$('#prev_loan_amount_' + index).val(addCommas(principal_without_finance_month));
								principal += principal_without_finance_month;
							} else if ($('#prev_loan_pay_type_2_' + index).is(':checked')) {
								$('#prev_loan_amount_' + index).val(addCommas(prev_loan_total+prev_loan_interest));
								$("input[name='prev_loan[" + index + "][interest]']").val(obj.prev_loan_active[key].interest);
								$("#joker-interest-"+index).val(parseFloat(prev_loan_interest).toLocaleString('en', numberFormatOptions));
								$("#joker-principal-"+index).val(parseFloat(prev_loan_total).toLocaleString('en', numberFormatOptions));
								$('#prev_loan_total_'+index).val(addCommas(prev_loan_total));

								principal += prev_loan_total;
								interest  += prev_loan_interest;
							}
						} else {
							$('#prev_loan_amount_' + index).val('');
						}
				}
			});

			$("#joker-principal").val(principal.toLocaleString("en", numberFormatOptions));
			$("#joker-interest").val(interest.toLocaleString("en", numberFormatOptions));
		}
		
	});
	//@end หายอดหักกลบ กรณีมีการเปลี่ยนแปลงวันที่ทำรายการได้	
	
	$('.prev_loan_checkbox').each(prev_loan_checkbox);

	cal_prev_loan();
	check_deduct_person_guarantee();
	check_life_insurance();
}



function prev_loan_checkbox(n, obj){
	var index = $(this).attr('attr_index');
	var deduct_person_guarantee = parseFloat( removeCommas($("#deduct_person_guarantee").val()) );
	if($(this).is(':checked')){
		var principal = parseFloat( removeCommas($('#prev_loan_total_'+index).val()) );
		var interest = parseFloat( removeCommas($("input[name='prev_loan["+index+"][interest]']").val()) );
		var total = principal+interest;
		var principal_without_finance_month = $("#principal_without_finance_month_"+index).val();
		var paytype = $("input[name='prev_loan["+n+"][pay_type]']:checked");
		if(paytype.val() === 'all'){
			$('#prev_loan_amount_'+index).val(addCommas(total));
		}else{
			$('#prev_loan_amount_'+index).val(addCommas(principal_without_finance_month));
		}
		var loan_old_amount = principal;
		var loan_new_amount = parseFloat(removeCommas( $("#loan").val() ));
		var percent_fee = 0.1;
		var fee = window.fee(loan_old_amount, loan_new_amount, percent_fee);
		$("input[name='prev_loan["+index+"][fee]']").val(fee);
		// $("#deduct_loan_fee").val(fee)

		var tmp_deduct_person_guarantee = parseFloat( removeCommas($("#tmp_deduct_person_guarantee_"+index).val()) );
		var deduct_person_guarantee_diff = tmp_deduct_person_guarantee - deduct_person_guarantee;
		// console.log("deduct_person_guarantee_diff", deduct_person_guarantee_diff);
		if(deduct_person_guarantee_diff <= 0){
			$("#tmp_deduct_person_guarantee_input_"+index).val(addCommas(Math.abs(deduct_person_guarantee_diff)));
			$("#tmp_deduct_person_guarantee_text_"+index).html("เก็บเพิ่ม ");
			// $("#tmp_deduct_person_guarantee_input_"+index).addClass( "loan_deduct" );
			$("#tmp_deduct_person_guarantee_"+index).addClass( "deduct_return" );
			deduct_person_guarantee = 0;
		}else{
			$("#tmp_deduct_person_guarantee_input_"+index).val(addCommas(Math.abs(deduct_person_guarantee_diff)));
			$("#tmp_deduct_person_guarantee_text_"+index).html("คืน ");
			// $("#tmp_deduct_person_guarantee_input_"+index).removeClass( "loan_deduct" );
			$("#tmp_deduct_person_guarantee_"+index).removeClass( "deduct_return" );
			deduct_person_guarantee -= tmp_deduct_person_guarantee;
		}

	}else{
		$('#prev_loan_amount_'+index).val('');
		$("input[name='prev_loan["+index+"][fee]']").val("");
	}

	var total = 0;
	$('.deduct_loan_fee').each(function(){
		console.log("deduct_loan_fee", $(this).val());
		total += numeral($(this).val()).value();
	})
	var val = numeral(total).format('0,0.00');
	$("#deduct_loan_fee").val(val);
	cal_estimate_money();

}

function fee(loan_old_amount, loan_new_amount, percent_fee){
	loan_new_amount = loan_new_amount || 0;
	var fee = Math.round((Math.abs((loan_new_amount-loan_old_amount))*percent_fee/100))
	return (fee < 20) ? 20 : fee;
}

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
function choose_transfer_type(){

	if($('#transfer_type_4').is(':checked')) {
		$('.transfer_cheque_multi').show();
		$('.transfer_account_id, .transfer_bank_id').hide();
	}else if($('#transfer_type_2').is(':checked')){
		$('.transfer_bank_id').show();
		$('.transfer_account_id, .transfer_cheque_multi').hide();
	}else if($('#transfer_type_1').is(':checked')){
		$('.transfer_bank_id, .transfer_cheque_multi').hide();
		$('.transfer_account_id').show();
	}else{
		$('.transfer_bank_id').hide();
		$('.transfer_account_id, .transfer_cheque_multi').hide();
	}

	$(".transfer_type:checked").trigger('change');
}
function change_province(id, id_to, id_input_amphur, district_space, id_input_district, amphur_value='',district_value=''){
    var province_id = $('#'+id).val();
    $.ajax({
        method: 'POST',
        url: base_url+'manage_member_share/get_amphur_list',
        data: {
            province_id : province_id,
            id_input_amphur : id_input_amphur,
            district_space : district_space,
            id_input_district : id_input_district
        },
        success: function(msg){
            $('#'+id_to).html(msg);
			if(amphur_value != ''){
				$('#'+id_input_amphur).val(amphur_value);
				change_amphur(id_input_amphur,district_space,id_input_district,district_value);
			}
        }
    });
}
function change_amphur(id, id_to, id_input_district, district_value=''){
    var amphur_id = $('#'+id).val();
    $.ajax({
        method: 'POST',
        url: base_url+'manage_member_share/get_district_list',
        data: {
            amphur_id : amphur_id,
            id_input_district : id_input_district
        },
        success: function(msg){
            $('#'+id_to).html(msg);
			if(district_value != ''){
				$('#'+id_input_district).val(district_value);
			}
        }
    });
}

function check_deduct_person_guarantee(){
	var loan_type = $('#loan_type').val();
	var member_id = $('#member_id').val();
	var loan_amount = removeCommas($('#loan_amount').val());
	var deduct_pay_prev_loan = removeCommas($('#deduct_pay_prev_loan').val());
	var prev_checkbox_array = [];
	var prev_data_type_array = [];
	var prev_pay_type_array = [];
	$('.prev_loan_checkbox').each(function(){
		var index = $(this).attr('attr_index');
		var data_type = $(this).attr('data_type');
		if($(this).is(':checked')){
			prev_checkbox_array.push($('#prev_loan_checkbox_'+index).val());
			prev_data_type_array.push(data_type);
			if($('#prev_loan_pay_type_1_'+index).is(':checked')){
				prev_pay_type_array.push($('#prev_loan_pay_type_1_'+index).val());
			}else if($('#prev_loan_pay_type_2_'+index).is(':checked')){
				prev_pay_type_array.push($('#prev_loan_pay_type_2_'+index).val());
			}
		}
	});

	$.post(base_url+"/loan/get_loan_deduct", 
	{	
		loan_type:loan_type,
		member_id:member_id,
		loan_amount:loan_amount,
		deduct_pay_prev_loan:deduct_pay_prev_loan,
		prev_checkbox_array:prev_checkbox_array,
		prev_pay_type_array:prev_pay_type_array,
		prev_data_type_array:prev_data_type_array
	}
	, function(result){
		obj = JSON.parse(result);
		//console.log(obj);
		$('#deduct_person_guarantee').val(obj.percent_guarantee);
		// $('#deduct_loan_fee').val(obj.loan_fee);
		$('#guarantee_amount_2').val(obj.share_collect);
		$('#guarantee_price_2').val(obj.share_collect_value);
		
	});
}

$('#member_loan_search').click(function(){
	if($('#member_search_list').val() == '') {
		swal('กรุณาเลือกรูปแบบค้นหา','','warning');
	} else if ($('#member_search_text').val() == ''){
		swal('กรุณากรอกข้อมูลที่ต้องการค้นหา','','warning');
	} else {
		$.ajax({
			url: base_url+"ajax/search_member_by_type_jquery",
			method:"post",  
			data: {
				search_text : $('#member_search_text').val(), 
				search_list : $('#member_search_list').val()
			},  
			dataType:"text",  
			success:function(data) {
				$('#result_member_search').html(data);  
			}  ,
			error: function(xhr){
				console.log('Request Status: ' + xhr.status + ' Status Text: ' + xhr.statusText + ' ' + xhr.responseText);
			}
		});  
	}
	
});

$(document).ready(function(){
	$(".maxlength_number_decimal").attr('maxlength','15');
});

//ระบบประกันชีวิต
function check_life_insurance(excel=''){
		var member_id = $('#member_id').val();
		var loan_amount = removeCommas($('#loan_amount').val());
		var date_receive_money = $('#date_receive_money').val();
		var cremation_all_total = $('#cremation_all_total').val();
		var loan_id = $('#loan_id').val();
		var blue_deposit_loan = ($('#deduct_blue_deposit').val() == '')?0:removeCommas($('#deduct_blue_deposit').val());
		var buy_share =  ($('#buy_share').val() == '')?0:removeCommas($('#buy_share').val());
		var deduct_share =  ($('#deduct_share').val() == '')?0:removeCommas($('#deduct_share').val());
		var share_loan = parseFloat(buy_share)+parseFloat(deduct_share);
		var loan_type = $('#loan_type').val();
		//var loan_type = $("#normal_loan").find('.modal-body #loan_type').val();
		//alert(loan_type);	
		//alert($('#loan_type').val());	
		var prev_checkbox_array = [];
		var prev_data_type_array = [];
		$('.prev_loan_checkbox').each(function(){
			var index = $(this).attr('attr_index');
			var data_type = $(this).attr('data_type');
			if($(this).is(':checked')){
				prev_checkbox_array.push($('#prev_loan_checkbox_'+index).val());
				prev_data_type_array.push(data_type);
			}
		});		
		
		$.post(base_url+"/loan/get_life_insurance", 
		{	
			member_id:member_id,
			loan_amount:loan_amount,
			date_receive_money:date_receive_money,
			cremation_all_total:cremation_all_total,
			loan_id:loan_id,
			blue_deposit_loan:blue_deposit_loan,
			share_loan:share_loan,
			prev_checkbox_array:prev_checkbox_array,
			prev_data_type_array:prev_data_type_array,
			excel:excel,
			loan_type: loan_type
		}
		, function(result){
			obj = JSON.parse(result);
			//console.log(obj);			
			if(obj.check_life_insurance != '1'){
				$('.cremation_show').hide();
				$('#insurance_old').html('');	
				$('#insurance_new').html('');						
				$('#insurance_new_input').val('');		
				$('#insurance_old_input').val('');						
				$('#deduct_insurance').val('');
				$('#insurance_year').val('');
				$('#insurance_date').val('');
				$('#insurance_amount').val('');
				$('#insurance_premium').val('');
			}else{
				$('.cremation_show').show();
				$('#insurance_old').html(addCommas(obj.insurance_old));	
				$('#insurance_new').html(addCommas(obj.insurance_new));						
				$('#insurance_new_input').val(addCommas(obj.insurance_new));		
				$('#insurance_old_input').val(addCommas(obj.insurance_old));						
				$('#deduct_insurance').val(addCommas(obj.deduct_insurance));
				$('#insurance_year').val(obj.year_receive_money);
				$('#insurance_date').val(obj.start_date_protection);
				$('#insurance_amount').val(obj.additional_insured);
				$('#insurance_premium').val(obj.deduct_insurance);
			}
			//console.log(obj);
			if(excel == 'excel'){
				var data_array = JSON.stringify(obj);
				document.location.href = base_url+'loan/calc_insurance_csv?data_array='+data_array;
			}
			
		});
}

function change_cremation_type(){
	var cremation_all = 0;
	$('.cremation_checkbox').each(function(){
		var index = $(this).attr('attr_index');		
		var attr_data = $(this).attr('attr_data');		
		if($(this).is(':checked')){
			cremation_all += parseFloat(attr_data);
		}else{
			
		}		
		
	});
	
	$('#cremation_all_total').val(cremation_all);
	check_life_insurance();
}

function edit_life_insurance(){
		var member_id = $('#member_id').val();
		var loan_amount = removeCommas($('#loan_amount').val());
		var date_receive_money = $('#date_receive_money').val();
		var cremation_all_total = $('#cremation_all_total').val();
		var loan_id = $('#loan_id').val();
		var blue_deposit_loan = ($('#deduct_blue_deposit').val() == '')?0:removeCommas($('#deduct_blue_deposit').val());
		var buy_share =  ($('#buy_share').val() == '')?0:removeCommas($('#buy_share').val());
		var deduct_share =  ($('#deduct_share').val() == '')?0:removeCommas($('#deduct_share').val());
		var share_loan = parseFloat(buy_share)+parseFloat(deduct_share);
		var loan_type = $('#loan_type').val();
		//alert(loan_type);
		//console.log(loan_type);

		var prev_checkbox_array = [];
		var prev_data_type_array = [];
		$('.prev_loan_checkbox').each(function(){
			var index = $(this).attr('attr_index');
			var data_type = $(this).attr('data_type');
			if($(this).is(':checked')){
				prev_checkbox_array.push($('#prev_loan_checkbox_'+index).val());
				prev_data_type_array.push(data_type);
			}
		});		
		
		$.post(base_url+"/loan/get_life_insurance", 
		{	
			member_id:member_id,
			loan_amount:loan_amount,
			date_receive_money:date_receive_money,
			cremation_all_total:cremation_all_total,
			loan_id:loan_id,
			blue_deposit_loan:blue_deposit_loan,
			share_loan:share_loan,
			prev_checkbox_array:prev_checkbox_array,
			prev_data_type_array:prev_data_type_array,
			loan_type: loan_type
		}
		, function(result){
			obj = JSON.parse(result);
			//console.log(obj);
			
			$('#insurance_old').html(addCommas(obj.insurance_old));	
			$('#insurance_new').html(addCommas(obj.insurance_new));	
			$('#insurance_new_input').val(addCommas(obj.insurance_new));		
			$('#insurance_old_input').val(addCommas(obj.insurance_old));			
			if(loan_id != ''){
				if(obj.check_life_insurance != '1'){
					$('.cremation_show').hide();
					$('#deduct_insurance').val('');
					$('#insurance_year').val('');
					$('#insurance_date').val('');
					$('#insurance_amount').val('');
					$('#insurance_premium').val('');
				}else{
					$('.cremation_show').show();
					$('#deduct_insurance').val(addCommas(obj.deduct_insurance));
					if(obj.row_life_insurance != null){
						$('#insurance_year').val(obj.row_life_insurance.insurance_year);
						$('#insurance_date').val(obj.row_life_insurance.insurance_date);
						$('#insurance_amount').val(obj.row_life_insurance.insurance_amount);
						$('#insurance_premium').val(obj.row_life_insurance.insurance_premium);
					}
					
					for(var key in obj.row_cremation_type){
						$('.cremation_checkbox').each(function(){
							var index = $(this).attr('attr_index');	
							if(index == obj.row_cremation_type[key].import_cremation_type){
								$('#cremation_type_'+index).attr('checked',true);									
							}		
						})
					}
					check_ch_s_o();
					check_s_s_o_k();
				}	
			}
			
		});
}

$(".btn-excel").click(function(e){
	check_life_insurance('excel');
});	

$("#cal_period_btn").click(function(e){
	var loan_id = $('#loan_id').val();
	if(loan_id != ''){
		edit_life_insurance();
	}else{
		check_life_insurance();
	}
	var str = " "; 
	if($("#period_type").val() == '1'){
		str += "งวด";  
		$("#period_amount_bath").hide();
		$("#period").show();
	}else{
		str += "บาท";		
		$("#period_amount_bath").show();
		$("#period").hide();	
	}
	$("#type_period").text(str);
	$('#deduct_insurance').prop('readonly', true);
});	

$("#buy_s_s_o_k").blur(function(e){
	var buy_s_s_o_k = parseFloat(removeCommas($(this).val()));
	check_s_s_o_k();
});

$("#buy_ch_s_o").blur(function(e){
	var buy_ch_s_o = parseFloat(removeCommas($(this).val()));	
	check_ch_s_o();
});

function check_s_s_o_k(){
	var cremation_2 = parseFloat($('#cremation_import_2').val());
	var buy_s_s_o_k = parseFloat(removeCommas($("#buy_s_s_o_k").val()));
	if(cremation_2 > 0){
		cremation_amount_2 = cremation_2;
	}else if(buy_s_s_o_k > 0){
		cremation_amount_2 = 600000;
		
	}else{
		cremation_amount_2 = 0;
	}
	$('#cremation_amount_2').val(cremation_amount_2);
	$('#text_s_s_o_k').html(addCommas(cremation_amount_2));
	$('input').attr('attr_data', cremation_amount_2);
}

function check_ch_s_o(){
	var cremation_1 = parseFloat($('#cremation_import_1').val());
	var buy_ch_s_o = parseFloat(removeCommas($("#buy_ch_s_o").val()));
	if(cremation_1 > 0){
		cremation_amount_1 = cremation_1;
	}else if(buy_ch_s_o > 0){
		cremation_amount_1 = 600000;		
	}else{
		cremation_amount_1 = 0;	
	}
	$('#cremation_amount_1').val(cremation_amount_1);
	$('#text_ch_s_o').html(addCommas(cremation_amount_1));
	$('input').attr('attr_data', cremation_amount_1);
}	

$("#date_receive_money").change(function(e){
	check_life_insurance();
	change_prev_loan_pay_type()
});

function check_salary_balance(){
	var member_id = $('#member_id').val();
	var loan_id = $('#loan_id').val();
	var date_receive_money = $('#date_receive_money').val();
	var loan_amount = removeCommas($('#loan').val());
	var interest_per_year = $('#interest_per_year').val();
	var principal_payment = (parseInt(removeCommas($('#first_pay').val())) - parseInt(removeCommas($('#first_interest_amount').val())));
	var total_paid_per_month = removeCommas($('#first_pay').val());
	var pay_type_id = $('#pay_type').val();

	var deduct_pay_prev_loan = removeCommas($('#deduct_pay_prev_loan').val());
	var prev_checkbox_array = [];
	var prev_data_type_array = [];
	var prev_pay_type_array = [];
	$('.prev_loan_checkbox').each(function(){
		var index = $(this).attr('attr_index');
		var data_type = $(this).attr('data_type');
		if($(this).is(':checked')){
			prev_checkbox_array.push($('#prev_loan_checkbox_'+index).val());
			prev_data_type_array.push(data_type);
			if($('#prev_loan_pay_type_1_'+index).is(':checked')){
				prev_pay_type_array.push($('#prev_loan_pay_type_1_'+index).val());
			}else if($('#prev_loan_pay_type_2_'+index).is(':checked')){
				prev_pay_type_array.push($('#prev_loan_pay_type_2_'+index).val());
			}
		}
	});
		
	//console.log('total_paid_per_month='+total_paid_per_month);
	var loan_cost_total = 0;
	var loan_cost_val = 0;
	var loan_cost_val_a = 0;
	$('.loan_cost').each(function(){
		var id = $(this).attr("id");
		loan_cost_val = isNaN(parseInt($('#'+id).val()))?0:parseInt($('#'+id).val());
		if(id != ''){
			loan_cost_total += loan_cost_val;
		}
	})
		
	$.post(base_url+"/loan/check_salary_balance", 
	{	
		member_id:member_id,
		loan_id:loan_id,
		date_receive_money:date_receive_money,
		loan_cost_total:loan_cost_total,
		loan_amount:loan_amount,
		interest_per_year:interest_per_year,
		principal_payment:principal_payment,
		total_paid_per_month:total_paid_per_month,
		pay_type_id:pay_type_id,
		deduct_pay_prev_loan:deduct_pay_prev_loan,
		prev_checkbox_array:prev_checkbox_array,
		prev_data_type_array:prev_data_type_array,
		prev_pay_type_array:prev_pay_type_array
	}
	, function(result){
		obj = JSON.parse(result);	
		//console.log(obj);		
		$('#salary_balance').val(obj.salary_balance);
		$('#percent_salary_balance').val(obj.percent_salary_balance);		
	});	
};
$("body").on('change', '.is_numeric', function(){    // 2nd (B)
	var val = numeral($(this).val()).value();

	val = numeral(val).format('0,0.00');
	console.log("is_numeric", val);
	$(this).val(val);
});

$("body").on('change', '.deduct_loan_fee', function(){
	var total = 0;
	$('.deduct_loan_fee').each(function(){
		console.log("deduct_loan_fee", $(this).val());
		total += numeral($(this).val()).value();
	})
	var val = numeral(total).format('0,0.00');
	$("#deduct_loan_fee").val(val);
	cal_estimate_money();
});

$(document).on('change', '#createdatetime', function(){
	if(typeof $(this).val() !== "undefined") {
		$('#date_receive_money').val($(this).val());
	}
});

// $(document).on('change', '#date_receive_money', function(){
// 	if(typeof $(this).val() !== "undefined") {
// 		$('#createdatetime').val($(this).val());
// 	}
// });

$(document).on('change', '.transfer_type', function(){
	var deduct_cheque = $("#deduct_cheque");

	$(this).each(function(){
		if($(this).prop('checked')){
			if($(this).attr('id') === "transfer_type_2" || $(this).attr('id') === "transfer_type_4"){
				deduct_cheque.val(20);
			}else{
				deduct_cheque.val('');
			}
		}
	});
});

function selected_loan_type(id){
	$.get(base_url+"loan/get_loan_type", {id : id}, function(res){
		console.log(res);
		console.log(res.ref_id, res.id);
		$('#loan_type_choose').val(res.ref_id).trigger('change');
		console.log("val : ", $('#loan_type_choose').val());
		setTimeout(function(){
			$('#loan_type_select').val(res.id);
			console.log("val : ", $('#loan_type_select').val());
		}, 800);
	});
}

async function guarantee_editor() {
	console.log('guarantee_editor : processing....');
	if ($('#guarantee_person_id_1').length == 0) {
		await create_input_garantor();
		await guarantee_person_fetch()
	}

}

function guarantee_person_fetch(){
	var i=1;
	console.log('data size : ', Object.keys(edit_data).length);
	if(Object.keys(edit_data).length) {
		var obj = edit_data;
		for (var key in obj.coop_loan_guarantee_person) {
			//console.log(obj.coop_loan_guarantee[key].guarantee_type);
			$('#guarantee_person_id_' + i).val(obj.coop_loan_guarantee_person[key].guarantee_person_id);
			$('#guarantee_person_name_' + i).val(obj.coop_loan_guarantee_person[key].firstname_th + " " + obj.coop_loan_guarantee_person[key].lastname_th);
			$('#guarantee_person_dep_' + i).val(obj.coop_loan_guarantee_person[key].mem_group_name);
			$('#guarantee_person_contract_number_' + i).val(obj.coop_loan_guarantee_person[key].guarantee_person_contract_number);
			$('#guarantee_person_amount_' + i).val(obj.coop_loan_guarantee_person[key].guarantee_person_amount);
			if (obj.coop_loan_guarantee_person[key].count_guarantee == '0') {
				var text_count_guarantee = obj.coop_loan_guarantee_person[key].count_guarantee;
			} else {
				var text_count_guarantee = '<a style="cursor:pointer" onclick="get_guarantee_person_data(\'' + obj.coop_loan_guarantee_person[key].guarantee_person_id + '\')">' + obj.coop_loan_guarantee_person[key].count_guarantee + '</a>';
			}
			$('#count_guarantee_' + i).html(text_count_guarantee);
			choose_guarantee(i)
			$('#guarantee_1').attr('checked', true);

			$('#btn_delete_' + i).show();
			$('#search_member_loan_modal').modal('hide');
			$('.guarantee_person_' + i).removeAttr('disabled');
			$('.guarantee_1').removeAttr('disabled');

			i++;
		}
	}
}

function updateSalaryDetail(memberId, loanId){
	swal({
		title: "คำเตือน",
		text: "ท่านต้องการอัพเดทข้อมูลเงินเดือนในเอกสารข้อกู้เงินใช่หรือไม่\nข้อมูลเงินเดือนในเอกสารพิจารณาเงินกู้จะถูกเปลี่ยนแปลงหลังจากการอัพเดท",
		type: "warning",
		showCancelButton: true,
		confirmButtonColor: '#DD6B55',
		confirmButtonText: 'ยืนยัน',
		cancelButtonText: "ยกเลิก",
		closeOnConfirm: false,
		closeOnCancel: true
	},function(isConfirm){
		if(isConfirm) {
			try {
				new Promise((resolve, reject) => {
					$.post(base_url + 'loan/update_salary_present_in_report_detail', {
						member_id: memberId,
						loan_id: loanId
					}, function (res) {
						if (res.code === 200 && res.status === 'ok') {
							resolve(res);
							return;
						} else {
							reject(res);
							return;
						}
					})
				}).then((data) => {
					swal('สำเร็จ', '', 'success');
				}).catch((err) => {
					swal('ไม่สำเร็จ', '', 'error');
					console.error(err.msg);
				});

			}catch (err) {
				console.error(err);
			}
		}
	});
}
