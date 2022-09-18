var base_url = $('#base_url').attr('class');
$( document ).ready(function() {
    $("#is_fix_member_id").click(function() {
        var mem = $(this).data('mem');
        if($(this).prop('checked')) {
            $("#member_id").val("");
            $("#member_id").prop("readonly", false);
            $("#member_id").focus();
        }
        else {
            $("#member_id").val(mem);
            $("#member_id").prop("readonly", true);
        }
    });

    $("#apply_date").datepicker({
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

    $("#birthday").datepicker({
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
		viewMode : "years",
		startView: "years",
    });

    $("#birthday").change(function() {
        var arr_date = $(this).val().split("/");
		var age = getAge(new Date((arr_date[2] - 543), arr_date[1], arr_date[0]));
        $("#age").val(age);
        $("#birthday").css("border-color", "#757575");
        $("#birthday_con small").remove();
        $("#birthday_border").removeClass("has-error");
		
		check_age_limit();

    });

    $("#btn_member_pic").click(function() {
        $.fancybox({
            'href' : base_url+'manage_member_share/member_lb_upload'
            , 'padding' : '10'
            , 'width': 520
            , 'modal' : true
            , 'type' : 'iframe'
            , 'autoScale' : false
            , 'transitionIn' : 'none'
            , 'transitionOut' : 'none'
            , afterClose : function() {
                console.log($.cookies);
                if($.cookies.get('is_upload'))
                    get_image();
            }
        });

        return false;
    });
	//change_bank_type();
	check_age_limit();
});

function change_bank_type(){
	if($('#bank_choose_1').is(':checked')){
		$('#bank_type_1').show();
		$('#bank_type_2').hide();
	}else if($('#bank_choose_2').is(':checked')){
		$('#bank_type_1').hide();
		$('#bank_type_2').show();
	}
}

function getAge(d1, d2){
    d2 = d2 || new Date();
    var diff = d2.getTime() - d1.getTime();
    var age = Math.floor(diff / (1000 * 60 * 60 * 24 * 365.25));
    return isNaN(diff) ? "" : (age < 0 ? 0 : age);
}

function change_province(id, id_to, id_input_amphur, district_space, id_input_district){
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
        }
    });
}

function change_amphur(id, id_to, id_input_district){
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
        }
    });
}

function change_bank(setHtmlId){
    var bank_id = $('#dividend_bank_id_'+setHtmlId).val();
    $('#bank_id_show_'+setHtmlId).val(bank_id);
    $('#branch_id_show_'+setHtmlId).val('');
    $.ajax({
        method: 'POST',
        url: base_url+'manage_member_share/get_bank_branch_list',
        data: {
            bank_id : bank_id
        },
        success: function(msg){
            // console.log("set", setHtmlId);
            // msg = msg.replace('change_branch()', 'change_branch('+setHtmlId+', this)');
            // $('#bank_branch_'+setHtmlId).html(msg);
        }
    });
}
function change_branch(setHtmlId, val){

    var branch_id = $(val).val();
    console.log("branch_id_show_"+setHtmlId, branch_id);
    $('#branch_id_show_'+setHtmlId).val(branch_id);
    
}

function change_mem_group(id, id_to){
    var mem_group_id = $('#'+id).val();
	$('#level').html('<option value="">เลือกข้อมูล</option>');
    $.ajax({
        method: 'POST',
        url: base_url+'manage_member_share/get_mem_group_list',
        data: {
            mem_group_id : mem_group_id
        },
        success: function(msg){
            $('#'+id_to).html(msg);
        }
    });
}

function dupp_address(ele){
    if($(ele).prop('checked')) {
        $('#c_address_no').val($('#address_no').val());
        $('#c_address_moo').val($('#address_moo').val());
        $('#c_address_village').val($('#address_village').val());
        $('#c_address_soi').val($('#address_soi').val());
        $('#c_address_road').val($('#address_road').val());
        $('#c_province_id').val($('#province_id').val());
        $('#c_amphur_id').html($('#amphur_id').html());
        $('#c_amphur_id').val($('#amphur_id').val());
        $('#c_district_id').html($('#district_id').html());
        $('#c_district_id').val($('#district_id').val());
        $('#c_zipcode').val($('#zipcode').val());
    }
}

function get_image() {
    $.ajax({
        type: "POST"
        , url: base_url+'manage_member_share/get_image'
        , data: {
            "do" : "get_image"
            , _time : Math.random()
        }
        , success: function(data) {
            $("#member_pic").attr("src", data);
        }
    });
}

function change_mem_type(id){
    if(id!=''){
        $.ajax({
            method: 'POST',
            url: base_url+'manage_member_share/check_resign_date',
            data: { id : id},
            success: function(msg){
                if(msg == 'success'){
                    $('#member_time_select').val('2');
                    $('#member_time').val('2');
                }else{
                    $('#mem_type').val('2');
                    swal('ไม่สามารถเข้าใหม่ได้ ',msg,'warning');

                }
            }
        });
    }else{
        $('#mem_type').val('1');
    }
}

function check_form(){
    var text_alert = '';
    var text_id_card_alert = '';
    var id_card = $('#id_card').val();
    var old_id_card = $('#old_id_card').val();
    var id_to_focus = [];
    var i=0;
    if(id_card != old_id_card){
        $.ajax({
            method: 'POST',
            url: base_url+'manage_member_share/check_register',
            data: { id_card : id_card},
            success: function(msg){
                if(msg == 'success'){
                    //$('#myForm').submit();
                }else{
                    text_id_card_alert = msg;
                }
            }
        });
    }
    if($('#apply_date').val() == ''){
        text_alert += 'วันที่สมัครสมาชิก\n';
        id_to_focus[i] = 'apply_date';
        i++;
    }
    if($('#employee_id').val() == ''){
        text_alert += 'รหัสพนักงาน\n';
        id_to_focus[i] = 'employee_id';
        i++;
    }
    if($('#firstname_th').val() == ''){
        text_alert += 'ชื่อ(ภาษาไทย)\n';
        id_to_focus[i] = 'firstname_th';
        i++;
    }
    if($('#lastname_th').val() == ''){
        text_alert += 'สกุล(ภาษาไทย)\n';
        id_to_focus[i] = 'lastname_th';
        i++;
    }
    if($('#mobile').val() == ''){
        text_alert += 'เบอร์มือถือ\n';
        id_to_focus[i] = 'mobile';
        i++;
    }
    if($('#address_no').val() == ''){
        text_alert += 'บ้านเลขที่(ทะเบียนบ้าน)\n';
        id_to_focus[i] = 'address_no';
        i++;
    }
    if($('#province_id').val() == ''){
        text_alert += 'จังหวัด(ทะเบียนบ้าน)\n';
        id_to_focus[i] = 'province_id';
        i++;
    }
    if($('#amphur_id').val() == ''){
        text_alert += 'อำเภอ(ทะเบียนบ้าน)\n';
        id_to_focus[i] = 'amphur_id';
        i++;
    }
    if($('#district_id').val() == ''){
        text_alert += 'ตำบล(ทะเบียนบ้าน)\n';
        id_to_focus[i] = 'district_id';
        i++;
    }
    if($('#c_address_no').val() == ''){
        text_alert += 'บ้านเลขที่(ปัจจุบัน)\n';
        id_to_focus[i] = 'c_address_no';
        i++;
    }
    if($('#c_province_id').val() == ''){
        text_alert += 'จังหวัด(ปัจจุบัน)\n';
        id_to_focus[i] = 'c_province_id';
        i++;
    }
    if($('#c_amphur_id').val() == ''){
        text_alert += 'อำเภอ(ปัจจุบัน)\n';
        id_to_focus[i] = 'c_amphur_id';
        i++;
    }
    if($('#c_district_id').val() == ''){
        text_alert += 'ตำบล(ปัจจุบัน)\n';
        id_to_focus[i] = 'c_district_id';
        i++;
    }
    if($('#id_card').val() == ''){
        text_alert += 'เลขบัตรประชาชน\n';
        id_to_focus[i] = 'id_card';
        i++;
    }
    if($('#nationality').val() == ''){
        text_alert += 'สัญชาติ\n';
        id_to_focus[i] = 'nationality';
        i++;
    }
    if($('#birthday').val() == ''){
        text_alert += 'วันเกิด\n';
        id_to_focus[i] = 'birthday';
        i++;
    }
    if($('#father_name').val() == ''){
        text_alert += 'ชื่อบิดา\n';
        id_to_focus[i] = 'father_name';
        i++;
    }
    if($('#mother_name').val() == ''){
        text_alert += 'ชื่อมารดา\n';
        id_to_focus[i] = 'mother_name';
        i++;
    }
    if($('#position').val() == ''){
        text_alert += 'ตำแหน่ง\n';
        id_to_focus[i] = 'position';
        i++;
    }
    if($('#department').val() == ''){
        text_alert += 'ฝ่าย\n';
        id_to_focus[i] = 'department';
        i++;
    }
    if($('#faction').val() == ''){
        text_alert += 'แผนก\n';
        id_to_focus[i] = 'faction';
        i++;
    }
    if($('#level').val() == ''){
        text_alert += 'หน่วยงาน\n';
        id_to_focus[i] = 'level';
        i++;
    }
    if($('#work_id_card').val() == ''){
        text_alert += 'เลขพนักงาน\n';
        id_to_focus[i] = 'work_id_card';
        i++;
    }
    if($('#salary').val() == ''){
        text_alert += 'เงินเดือน\n';
        id_to_focus[i] = 'salary';
        i++;
    }
    if($('#other_income').val() == ''){
        text_alert += 'เงินอื่นๆ\n';
        id_to_focus[i] = 'other_income';
        i++;
    }

    text_alert = '';
    if(text_id_card_alert != '') {
        $('#id_card').focus();
        swal('ไม่สามารถสมัครสมาชิกได้ ',text_id_card_alert,'warning');
    }else if(text_alert != ''){
        $('#'+ id_to_focus[0]).focus();
        swal('กรุณากรอกข้อมูลต่อไปนี้',text_alert,'warning');

    }else{
        $('#myForm').submit();
    }
}
function readURL(input) {
	var i = 0;
	//console.log(input.files);
	$('#register_file_space').html('');
	if (input.files && input.files[0]) {
		$.each( input.files, function() {
			$('#register_file_space').append('<img id="img_'+i+'" src="#" style="border:1px solid" width="100px" height="100px"> ');
			read_file('img_'+i,input.files[i]);
			i++;
		});
		console.log(i);
	}
}

function read_file(target,input){
	var reader = new FileReader();
	reader.onload = function (e) {
		//console.log(e.target.result);
		$('#'+target).attr('src', e.target.result);
	}
	reader.readAsDataURL(input);
}
function del_img(img_id){
	swal({
		title: "",
		text: "ท่านต้องการลบหลักฐานการสมัครสมาชิกใช่หรือไม่",
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
			$.ajax({
				type: "POST"
				, url: base_url+'manage_member_share/del_img'
				, data: {
					img_id : img_id
				}
				, success: function(data) {
					$("#prev_img_"+img_id).html("");
				}
			});
		} else {
			
		}
	 });
	
}
function check_salary_and_share(){
	var salary = $('#salary').val();
	var other_income = $('#other_income').val();
	var mem_type_id = $('#mem_type_id').val();
	var share_month = $('#share_month').val();
	var mem_apply_id = $('#mem_apply_id').val();
	var member_id = $('#member_id').val();
	$.ajax({
		type: "POST"
		, url: base_url+'manage_member_share/check_salary_and_share'
		, data: {
			mem_type_id : mem_type_id,
			salary : salary,
			other_income : other_income
		}
		, success: function(data) {
			//console.log(data);
			if(mem_apply_id == ''){			
				if(parseInt(data) > parseInt(share_month) || share_month == ''){
					$('#share_month_text').html(data);
					$("#share_month").val(data);
				}
				if($("#share_month").val()!=''){
					$('#share_show').show();
				}else{
					$('#share_show').hide();
				}
			}
		}
	});
}
function search_idcard(type){
	var firstname_th = $('#firstname_th').val();
	var lastname_th = $('#lastname_th').val();
	var id_card = $('#id_card').val();
	var year_quite = $('#year_quite').val();
	if(id_card == '') {
        swal('กรุณากรอกเลขบัตรประชาชนที่ต้องการค้นหา');
    }else if(firstname_th == ''){
        swal('กรุณากรอกชื่อ(ภาษาไทย)ที่ต้องการค้นหา');
    }else if(lastname_th == ''){
        swal('กรุณากรอกสกุล (ภาษาไทย)ที่ต้องการค้นหา');
	}else{
		$.ajax({
			type: "POST"
			, url: base_url+'manage_member_share/search_from_id_card'
			, data: {
				id_card : id_card,
                firstname_th: firstname_th,
                lastname_th: lastname_th
			}
			, success: function(data) {
				if(data == 'empty'){
					if(type=='click'){
						swal('ท่านสามารถใช้เลขประจำตัวประชาชนได้','ไม่พบข้อมูลการยื่นคำร้อง หรือสมัครสมาชิก','success');
					}
				}else if(data == 'still_member'){
					swal('ไม่สามารถสมัครสมาชิกได้','เนื่องจากท่านยังเป็นสมาชิกสหกรณ์อยู่','warning');
					$('#id_card').val('');
				}else if(data == 'can_not_register'){
					swal('ไม่สามารถสมัครสมาชิกได้','เนื่องจากท่านได้ทำการลาออก และยังไม่ครบกำหนด '+year_quite+' ปี','warning');
					$('#id_card').val('');
				}else{
					$('#table_idcard_data').html(data);
					$('#search_idcard_modal').modal('show');
				}
			}
		});
	}
}

function use_data(id){
	document.location.href = base_url+'manage_member_share/add?id='+id+'&action=use_prev_data';
}

function check_age_limit(){
	var mem_apply_id = $('#mem_apply_id').val();
	if(mem_apply_id  == ''){		
		var age_limit = $('#apply_type_id :selected').attr('age_limit');
		var age = $('#age').val();
		var birthday = $('#birthday').val();
		if(age_limit != '' && age != '' && birthday.length == 10){
			if(parseInt(age) > parseInt(age_limit)){
				swal('การสมัครประเภท '+$('#apply_type_id :selected').text()+' ต้องอายุไม่เกิน '+age_limit+' ปี');
			}
		}
	}
}

function check_member_id(){
	var member_id = $('#member_id').val();
	var keycode = (event.keyCode ? event.keyCode : event.which);
	var id = '';
	if(keycode == '13'){
		$.post(base_url+"manage_member_share/check_member_id", 
		{	
			member_id: member_id
		}
		, function(result){
			obj = JSON.parse(result);
			console.log(obj.id);
			id = obj.id;
			if(id != undefined){
				document.location.href = base_url+'manage_member_share/add/'+id	
			}else{					
				swal('ไม่พบรหัสสมาชิกที่ท่านเลือก','','warning'); 
			}
		});		
	}	
}

$(document).on('change', '#apply_type_id', function(){
	if($(this).val() === '2'){
		$('#mem_type_id').val(3);
	}
});
