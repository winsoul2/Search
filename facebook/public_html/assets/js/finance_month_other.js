var base_url = $('#base_url').attr('class');
$( document ).ready(function() {
    	
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
function removeCommas(str) {
    return(str.replace(/,/g,''));
}
function addCommas(x){
  return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}
var i=0;
function add_row(){
	$('#value_null').hide();
	var new_row = "";
	new_row += "<tr class='new_row' id='new_row_"+i+"'>\n";
	new_row += "<td class='num_row' align='center'></td>";
	new_row += "<td>";
		new_row += "<div class='input-group'>";
		new_row += "<input type='text' id='member_id_"+i+"' class='form-control center member_id' name='data[list_data]["+i+"][member_id]' onchange=\"keypress_search_member('"+i+"')\">";
		new_row += "<span class='input-group-btn'>";
		new_row += "<a data-toggle='modal' class='fancybox_share fancybox.iframe' href='#' onclick=\"open_modal('search_member_modal','"+i+"')\">";
		new_row += "<button id='' type='button' class='btn btn-info btn-search'><span class='icon icon-search'></span></button>";
		new_row += "</a>";
		new_row += "</span>";		
		new_row += "</div>";		
	new_row += "</td>";
	new_row += "<td><input type='text' id='member_name_"+i+"' name='data[list_data]["+i+"][member_name]' class='form-control member_name' readonly></td>";
	new_row += "<td id='ref_space_"+i+"'></td>";
	new_row += "<td><input type='text' class='form-control pay_amount' id='pay_amount_"+i+"' name='data[list_data]["+i+"][pay_amount]' onkeyup=\"format_the_number_decimal(this)\"></td>";
	new_row += "<td align='center'><a style='cursor:pointer;' class='icon icon-trash-o' titla='ลบ' onclick=\"del_list('"+i+"')\"></a></td>";
	new_row += "</tr>\n";
	$('#table_data').append(new_row);
	add_num_row();
	i++;
}

function add_num_row(){
	var num_row=1;
	$('.num_row').each(function(){
		$(this).html(num_row);
		num_row++;
	});
}

function del_list(id){
	$('#new_row_'+id).remove();
	add_num_row();
}

function del_data(id){
	swal({
		title: "",
		text: "ท่านต้องการลบยข้อมูลใช่หรือไม่",
		type: "warning",
		showCancelButton: true,
		confirmButtonColor: '#DD6B55',
		confirmButtonText: 'ยืนยัน',
		cancelButtonText: "ยกเลิก",
		closeOnConfirm: true,
		closeOnCancel: true
    },function(isConfirm){
      if (isConfirm) {
		$.post(base_url+"/finance_process/delete_data", 
		{	
			run_id: id
		}
		, function(result){
			$('#prev_row_'+id).remove();
		});
		add_num_row();
	  }
	});
}

function keypress_search_member(id){
	
	var member_id = $('#member_id_'+id).val();
	var deduct_id = $('#deduct_id').val();
	$.post(base_url+"/finance_process/keypress_search_member", 
	{	
		member_id: member_id,
		deduct_id: deduct_id,
		id:id
	}
	, function(result){
		if(result!='error'){
			var obj = JSON.parse(result);
			console.log(obj);
			var member_name = obj.row_member['prename_short']+obj.row_member['firstname_th']+" "+obj.row_member['lastname_th']
			$('#member_id_'+id).val(obj.row_member['member_id']);
			$('#member_name_'+id).val(member_name);
			$('#ref_space_'+id).html(obj.ref_data);
			check_member_id(obj.row_member['member_id'],id);
		}else{
			$('#member_name_'+id).val('');
			$('#ref_space_'+id).html('');
		}
	});
}

function change_deduct_id(){
	$('#table_data').html('');
	if($('#dedcut_id').val()!=''){
		$('#btn_space').show();
	}else{
		$('#btn_space').hide();
	}
	change_data();
}

function check_form(){
	var text_alert = '';
	
	var new_row = 0;
	$('.new_row').each(function(){
			new_row++;
	});
	if(new_row == 0){
		text_alert += '- กรุณาเพิ่มรายการ\n';
	}
	
	var empty_pay_amount = 0;
	$('.pay_amount').each(function(){
		if($(this).val() == ''){
			empty_pay_amount++;
		}
	});
	if(empty_pay_amount>0){
		text_alert += '- จำนวนเงิน\n';
	}
	
	if(text_alert!=''){
		swal('กรุณากรอกข้อมูลต่อไปนี้ให้ครบถ้วน',text_alert,'warning');
	}else{
		$('#form2').submit();
	}
}

function change_data(){
	var year = $('#year_select').val();
	var month = $('#month_select').val();
	var deduct_id = $('#deduct_id').val();
	$.post(base_url+"finance_process/get_finance_month_data", 
	{	
		year: year,
		month: month,
		deduct_id:deduct_id
	}
	, function(result){
		$('#table_data').append(result);
		add_num_row();
	});
}

function get_data(member_id,firstname_th,data_row){
	$('#member_id_'+data_row).val(member_id);
	$('#member_name_'+data_row).val(firstname_th);	
	
	$('#search_member_modal').modal('hide');
	$('#member_search_text').val('');
	$('#member_search_list').val('');
	$('#result_member_search').html('');
	
	check_member_id(member_id,data_row);
}

function check_member_id(member_id,data_row){
	var i=0;
	$('.member_id').each(function(){
		
		if(member_id == $(this).val()){	
			i++;
			if(i>1){
				$('#member_id_'+data_row).val('');
				$('#member_name_'+data_row).val('');
				swal('กรุณาเลือกรหัสสมาชิกใหม่ \nเนื่องจากมีรหัสสมาชิกนี้แล้ว','','warning');
			}			
		}
	});
}

function open_modal(id,data_row){
	$('#'+id).modal('show');
	$('#data_row').val(data_row);
}

$('#member_search').click(function(){
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
				search_list : $('#member_search_list').val(),
				data_row : $('#data_row').val()
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