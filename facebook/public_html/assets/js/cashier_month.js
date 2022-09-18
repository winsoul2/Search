var base_url = $('#base_url').attr('class');
$(document).ready(function(){	
	$('.member_id').attr('disabled','true');
	$('.employee_id').attr('disabled','true');

	// function get mem_group
	var getMemGroup = function(id, inHtml) {
		$.ajax({
			method: 'POST',
			url: base_url+'manage_member_share/get_mem_group_list',
			data: {
				mem_group_id : id
			},
			success: function(msg){
				inHtml.html(msg);
			}
		});
	}
	
	// get sub department when on change department
	$('body').on('change', '#department',function(){		
		// clear when change to empty(); if has value to append on sub level
		if($(this).val() === ''){
			var defaultOption = '<option value="">เลือกข้อมูล</option>'
			$('#faction').html(defaultOption);
			$('#level').html(defaultOption);
		} else {
			getMemGroup($(this).val(), $('#faction'));
		}
	});

	// get level when on change faction
	$('body').on('change', '#faction', function() {
		getMemGroup($(this).val(), $('#level'));
	});

	// onload page then disable not selected
	setTimeout(function(){
		toggleMember(false);
		toggleDepartment(true);
		toggleMemberRange(false);
	}, 500);

	// radio condition when selected
	$('body').on('click', 'input[name=choose_receipt]', function(){
		var onChecked = $(this).val();
		if(onChecked == '2'){
			toggleMember(true);
			toggleDepartment(false);
			toggleMemberRange(false);
		}else if(onChecked == '3'){
			toggleMember(false);
			toggleDepartment(false);
			toggleMemberRange(true);
		}else{
			toggleMember(false);
			toggleDepartment(true);
			toggleMemberRange(false);
		}
	});

	// function disabled
	var toggleDepartment = function(onToggle) {
		if(onToggle){
			$('#department').removeAttr('disabled');
			$('#faction').removeAttr('disabled');
			$('#level').removeAttr('disabled');
			$('#page_number').removeAttr('disabled');
		} else {
			$('#department').attr('disabled', true);
			$('#faction').attr('disabled', true);
			$('#level').attr('disabled', true);
			$('#page_number').attr('disabled', true);
		}
	}

	var toggleMember = function(onToggle) {
		if(onToggle){
			$('input[name=member_id]').removeAttr('disabled');
		} else {
			$('input[name=member_id]').attr('disabled', true);
		}
	}

	var toggleMemberRange = function(onToggle) {
		if(onToggle) {
			$('input[name=member_id_begin]').removeAttr('disabled');
			$('input[name=member_id_end]').removeAttr('disabled');
		} else {
			$('input[name=member_id_begin]').attr('disabled', true);
			$('input[name=member_id_end]').attr('disabled', true);
		}
	}

	// validation
	var validateForm = function(){
		var chooseReceipt = $('input[name=choose_receipt]:checked').val();
		var chkErr = true;		
		if(chooseReceipt == 2) {
			chkErr = ($('input[name=member_id]').val() != '') ? true : false;			
		} else if(chooseReceipt == 3) {
			chkErr = ($('input[name=member_id_begin]').val() != '' && $('input[name=member_id_end]').val() != '') ? true : false;
		}		
		if(!chkErr) {
			swal({
				type: 'error',
				title: 'แจ้งเตือน',
				text: 'กรุณาระบุข้อมูลให้ครบถ้วน!'				
			  });
			return false;
		}
		return true;
	}

	// submit form
	submit_form = function(action_type){
		var checkValid = validateForm();
		if(!checkValid) return;
		
		$('#action_type').val(action_type);
		$('#receiptForm').submit();
	}
});

function open_modal(id){
	$('#'+id).modal('show');
}
function change_month_year(){
	var month = $('#month_choose').val();
	var year = $('#year_choose').val();
	$('#month').val(month);
	$('#year').valyear
}
function radio_check(value){
	if(value == '2'){
		$('.member_id').removeAttr('disabled');
		$('.employee_id').attr('disabled','true');
	}else if(value == '3'){
		$('.member_id').attr('disabled','true');
		$('.employee_id').removeAttr('disabled');
	}else{
		$('.member_id').attr('disabled','true');
		$('.employee_id').attr('disabled','true');
	}
}
// function submit_form(action_type){
// 	validateForm();
// 	$('#action_type').val(action_type);
// 	$('#receiptForm').submit();
// }