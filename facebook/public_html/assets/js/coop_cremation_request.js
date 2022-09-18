var base_url = $('#base_url').attr('class');
$( document ).ready(function() {
	var member_id = $("#member_id").val();
	if(member_id == ''){
		$("#bt_view").prop("disabled", true);
		$("#bt_add").prop("disabled", true);
	}
	
	$('#myModal').on('shown.bs.modal', function () {
		$('#search_text').focus();
	})  
});

function check_form(){
	$('#from_save').submit();
}
	
function get_search_member(){
	$.ajax({
		type: "POST",
		url: base_url+'cremation/get_search_member',
		data: {
			search_text : $("#search_text").val(),
			form_target : 'add'
		},
		success: function(msg) {
			$("#table_data").html(msg);
		}
	});
}

function del_coop_data(id,member_id){	
	swal({
		title: "ท่านต้องการลบข้อมูลใช่หรือไม่",
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
				url: base_url+'/cremation/del_coop_data',
				method: 'POST',
				data: {
					'table': 'coop_cremation_request',
					'id': id,
					'field': 'cremation_request_id'
				},
				success: function(msg){
				   //console.log(msg); return false;
					if(msg == 1){
					  document.location.href = base_url+'cremation/cremation_request?id='+member_id;
					}else{

					}
				}
			});
		} else {
			
		}
	});		
}


function add_request(){
	$('#myModalRequest').modal('show');
	var user_name = $("#user_name_session").val();
	$("#user_name").html(user_name);
	$("#cremation_request_id").val('');
	$("#cremation_type_id").val('');
}

function view_request(){
	$('#viewRequest').modal('show');
}

function change_type(){
	var cremation_type_id = $("#cremation_type_id").val();
	var member_id = $("#member_id").val();
	var cremation_request_id = $("#cremation_request_id").val();

	$.ajax({
		type: "POST",
		url: base_url+'cremation/get_data_cremation_type',
		data: {
			id : cremation_type_id,
			member_id : member_id,
			cremation_request_id : cremation_request_id
		},
		success: function(msg) {
			response = $.parseJSON(msg);
			//console.log(msg);			
			$(".cremation_detail_id").val(response.cremation_detail_id);
			if(response.message_alert == '1'){
				$(".bt_save").attr("disabled", true);
				swal('ไม่สามารถเลือกฌาปนกิจสงเคราะห์นี้ได้ \nเนื่องจากเคยสมัครแล้ว');
			}else{
				$(".bt_save").attr("disabled", false);
			}
		}
	});
	
}

function change_type_view(){
	var cremation_type_id = $("#cremation_type_id_view").val();
	var cremation_detail = '';
	$.ajax({
		type: "POST",
		url: base_url+'cremation/get_cremation_type',
		data: {
			id : cremation_type_id
		},
		success: function(msg) {
			if(msg){
				$("#cremation_request_detail_view").html(msg);
				
				var start_date = $("#start_date").val();
				$("#start_date_view").val(start_date);

			}else{
				$("#cremation_request_detail_view").html('');
				$("#start_date_view").val('');
			}
		}
	});
	
}

function close_modal(id){
	$('#'+id).modal('hide');
	$("#cremation_request_detail_view").html('');
	$("#start_date_view").val('');
	$("#cremation_type_id_view").val('');
}

function show_file(){
	 $('#show_file_attach').modal('show');
}

function edit_request(cremation_request_id,member_id){
	$('#btn_show_file').hide();
	$.ajax({
		type: "POST",
		url: base_url+'cremation/get_cremation_request',
		data: {
			id : cremation_request_id
		},
		success: function(msg) {
			response = $.parseJSON(msg);
			//console.log(response);			
			$("#cremation_request_id").val(response.cremation_request_id);
			$("#cremation_type_id").val(response.cremation_type_id);
			$(".cremation_detail_id").val(response.cremation_detail_id);
			$("#cremation_request_detail").html(response.cremation_detail);
			$("#user_name").html(response.user_name);	
			
			var txt_file_attach = '<table width="100%">';
			var i=1;
			for(var key in response.coop_file_attach){
				txt_file_attach += '<tr class="file_row" id="file_'+response.coop_file_attach[key].id+'">\n';
				txt_file_attach += '<td><a href="'+base_url+'/assets/uploads/cremation_request/'+response.coop_file_attach[key].file_name+'" target="_blank">'+response.coop_file_attach[key].file_old_name+'</a></td>\n';
				txt_file_attach += '<td style="color:red;font-size: 20px;cursor:pointer;" align="center" width="10%"><span class="icon icon-ban" onclick="del_file(\''+response.coop_file_attach[key].id+'\')"></span></td>\n';
				txt_file_attach += '</tr>\n';
				i++;
			}
			txt_file_attach += '</table>';
			$('#show_file_space').html(txt_file_attach);
			if(i>1){
				$('#btn_show_file').show();
			}
		}
	});
	$('#myModalRequest').modal('show');
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
			$.post( base_url+"/cremation/ajax_delete_file_attach", 
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

function transfer_cremation(id, action){
	$('#show_transfer').modal('show');
	$.ajax({
		type: "POST",
		url: base_url+'cremation/get_cremation_transfer',
		data: {
			id : id
		},
		success: function(msg) {
			//console.log(msg);
			response = $.parseJSON(msg);
			//console.log(response);
			$(".cremation_receive_id").val(response.cremation_receive_id);
			$(".cremation_request_id").val(response.cremation_request_id);
			$(".member_id").val(response.member_id);	
			$(".cremation_type_id").val(response.cremation_id);
			$(".cremation_type_name").val(response.cremation_type_name);
			$(".cremation_detail_id").val(response.cremation_detail_id);
			$("#pay_type").val(response.pay_type);
			$(".cremation_receive_amount").val(response.cremation_receive_amount);		
			$(".action_fee_percent").val(response.action_fee_percent);
			$(".cremation_balance_amount").val(response.cremation_balance_amount);

			$(".admin_transfer").val(response.admin_transfer);
			$(".mobile").val(response.mobile);			
			$("#createdatetime").val(response.createdatetime);
			$("#date_transfer_picker").val(response.date_transfer);
			$("#time_transfer").val(response.time_transfer);
			$("#bank_choose_1").prop("checked", false);
			$("#bank_choose_2").prop("checked", false);
			$("#bank_choose_"+response.bank_type).attr('checked','checked');
			$("#bank_id_show").val(response.bank_id);
			$("#dividend_bank_id").val(response.bank_id);
			change_bank(response.bank_branch_id);
			$("#branch_id_show").val(response.bank_branch_id);
			$("#bank_account_no").val(response.bank_account_no);
			$('#file_show').hide();
			if(response.file_name != '' && response.file_name != null){
				$("#file_transfer").attr('src',base_url+'assets/uploads/cremation_transfer/'+response.file_name);
				$('#file_show').show()
			}
			
			list_account();	
			change_bank_type();
			$("#action").val(action);
			if(action == 'view'){
				$('.bt_save').hide();
				$('#account_id').attr("disabled", true);
				$('#dividend_bank_id').attr("disabled", true);
				$('#dividend_bank_branch_id').attr("disabled", true);
				$('#bank_account_no').attr("disabled", true);
				$('#date_transfer_picker').attr("disabled", true);
				$('#time_transfer').attr("disabled", true);
				$('#file_name').hide();
				$('input[name="bank_type"]').attr('disabled', true);
			}else{
				$('.bt_save').show();
				$('#account_id').attr("disabled", false);
				$('#dividend_bank_id').attr("disabled", false);
				$('#dividend_bank_branch_id').attr("disabled", false);
				$('#bank_account_no').attr("disabled", false);
				$('#date_transfer_picker').attr("disabled", false);
				$('#time_transfer').attr("disabled", false);
				$('#file_name').show();
				$('input[name="bank_type"]').attr('disabled', false);
			}
		}
	});	
}

function change_bank_type(){
	if($('#bank_choose_1').is(':checked')){
		$('#bank_type_1').show();
		$('#bank_type_2').hide();
	}else if($('#bank_choose_2').is(':checked')){
		$('#bank_type_1').hide();
		$('#bank_type_2').show();
	}
}

function change_bank(bank_branch_id = ''){
	var bank_id = $('#dividend_bank_id').val();
	$('#bank_id_show').val(bank_id);
	$('#branch_id_show').val('');
	$.ajax({
		method: 'POST',
		url: base_url+'manage_member_share/get_bank_branch_list',
		data: {
			bank_id : bank_id
		},
		success: function(msg){
			$('#bank_branch').html(msg);
			if(bank_branch_id != ''){
				$("#dividend_bank_branch_id").val(bank_branch_id);
				if($("#action").val() == 'view'){
					$('#dividend_bank_branch_id').attr("disabled", true);
				}else{
					$('#dividend_bank_branch_id').attr("disabled", false);
				}
			}
		}
	});
}	

function list_account(){
	var member_id = $(".member_id").val();
	var cremation_request_id = $(".cremation_request_id").val();
	$.ajax({
		method: 'POST',
		url: base_url+'cremation/get_account_list',
		data: {
			member_id : member_id,
			cremation_request_id : cremation_request_id
		},
		success: function(msg){
			$('#account_id').html(msg);
		}
	});	
}

function show_request_receive(cremation_request_id,member_id){
	$('#show_request_receive').modal('show');
	$.ajax({
		type: "POST",
		url: base_url+'cremation/get_cremation_receive',
		data: {
			id : cremation_request_id
		},
		success: function(msg) {
			response = $.parseJSON(msg);
			//console.log(response);
			$(".cremation_request_id").val(cremation_request_id);
			$(".cremation_receive_id").val(response.cremation_receive_id);
			$(".member_id").val(response.member_id);	
			$(".cremation_type_id").val(response.cremation_id);
			$(".cremation_type_name").val(response.cremation_type_name);
			$(".cremation_detail_id").val(response.cremation_detail_id);
			$("#pay_type").val(response.pay_type);
			$(".cremation_receive_amount").val(response.cremation_receive_amount);		
			$(".action_fee_percent").val(response.action_fee_percent);
			$(".cremation_balance_amount").val(response.cremation_balance_amount);
			
				
			
			var txt_file_attach = '<table width="100%">';
			var i=1;
			for(var key in response.coop_file_attach){
				txt_file_attach += '<tr class="file_row" id="file_'+response.coop_file_attach[key].id+'">\n';
				txt_file_attach += '<td><a href="'+base_url+'/assets/uploads/cremation_request/'+response.coop_file_attach[key].file_name+'" target="_blank">'+response.coop_file_attach[key].file_old_name+'</a></td>\n';
				//txt_file_attach += '<td style="color:red;font-size: 20px;cursor:pointer;" align="center" width="10%"><span class="icon icon-ban" onclick="del_file(\''+response.coop_file_attach[key].id+'\')"></span></td>\n';
				txt_file_attach += '</tr>\n';
				i++;
			}
			txt_file_attach += '</table>';
			$('#show_file_space').html(txt_file_attach);
			
			if(response.cremation_receive_id != null){ 
				$('#bt_save').hide();
				if(i>1){
					$('.btn_show_file').show();
					$('.show_att').hide();	
					$('.btn_show_not_file').hide();
				}else{
					$('.btn_show_not_file').show();
					$('.btn_show_file').hide();
					$('.show_att').hide();
				}
			}else{
				$('#bt_save').show();
				$('.show_att').show();
				$('.btn_show_file').hide();
				$('.btn_show_not_file').hide();
			}			
		}
	});
}

function check_form_request_receive (){
	$('#from_receive').submit();
}

function show_pay_all(cremation_id,member_id){
	var url = base_url+'cremation/cremation_pay_all?cremation_id='+cremation_id+'&member_id='+member_id;
	//console.log(url);
	window.open(url,'_blank');
}	
