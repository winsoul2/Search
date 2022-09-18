var base_url = $('#base_url').attr('class');
$( document ).ready(function() {
	var n_id = $("#n_id").val();
	if(n_id != ''){
		$.blockUI({
			message: '',
			css: {
				border: 'none',
				padding: '15px',
				backgroundColor: '#000',
				'-webkit-border-radius': '10px',
				'-moz-border-radius': '10px',
				opacity: .5,
				color: '#fff'
			},
			baseZ: 2000,
			bindEvents: false
		});
		add_quantity();
	}

    $("#receive_date").datepicker({
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

    $("#start_date").datepicker({
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

    $("#btn_store_pic").click(function() {
        $.fancybox({
            'href' : base_url+'facility/store_lb_upload'
            , 'padding' : '10'
            , 'width': 520
            , 'modal' : true
            , 'type' : 'iframe'
            , 'autoScale' : false
            , 'transitionIn' : 'none'
            , 'transitionOut' : 'none'
            , afterClose : function() {
                //console.log($.cookies);
                if($.cookies.get('is_upload'))
                   get_image();
            }
        });

        return false;
	});
	
	$("#check-all").change(function() {
		if($(this).is(':checked')){
			$('.store-id-checkbox').prop('checked','checked');
		}else{
			$('.store-id-checkbox').prop('checked','');
		}
	});
	$(".store-id-checkbox").change(function() {
		if(!$(this).is(':checked')){
			$('#check-all').prop('checked','');
		}
	});

	get_search_facility();
	get_search_department();
});

function get_image() {
    $.ajax({
        type: "POST"
        , url: base_url+'facility/get_image'
        , data: {
            "do" : "get_image"
            , _time : Math.random()
        }
        , success: function(data) {
            $("#store_pic").attr("src", data);
        }
    });
}

function check_form(){
	$('#myForm').submit();
}	
function get_search_facility(){
	$.ajax({
		type: "POST",
		url: base_url+'facility/get_search_facility',
		data: {
			search_text : $("#search_facility_text").val(),
			form_target : 'add'
		},
		success: function(msg) {
			//console.log(msg);
			$("#table_data_facility").html(msg);
		}
	});
}

function get_search_department(){
	$.ajax({
		type: "POST",
		url: base_url+'facility/get_search_department',
		data: {
			search_text : $("#search_department_text").val(),
			form_target : 'add'
		},
		success: function(msg) {
			$("#table_data_department").html(msg);
		}
	});
}

function choose_facility(id,name,unit_id,unit_name,price){
	$("#facility_main_code").val(id);
	$("#store_name").val(name);
	$("#unit_type_id").val(unit_id);
	$("#unit_type_name").val(unit_name);
	$("#store_price").val(price);

	$("#myModal .close").click()
}	


function choose_department(department_id,department_name){
	$("#department_id").val(department_id);
	$("#department_name").val(department_name);

	$("#myModalDepartment .close").click()
}	

function add_quantity(){
	$('#myModalQuantity').modal('show');
}

function check_quantity(){
	var store_id = $("#store_id").val();
	var store_quantity = $("#store_quantity").val();
	swal({
		title: "ท่านต้องเพิ่มจำนวนใช่หรือไม่",
		text: "",
		type: "warning",
		showCancelButton: true,
		confirmButtonColor: '#DD6B55',
		confirmButtonText: 'ตกลง',
		cancelButtonText: "ยกเลิก",
		closeOnConfirm: false,
		closeOnCancel: true
	},
	function(isConfirm) {
		if (isConfirm) {			
			$.ajax({
				url: base_url+'/facility/save_form_quantity',
				method: 'POST',
				data: {
					'store_quantity': store_quantity,
					'store_id': store_id
				},
				success: function(msg){
				   //console.log(msg); //return false;
					if(msg == 1){
					  document.location.href = base_url+'facility/add?s_id='+store_id;
					}else{
						swal('ไม่สามารถเพิ่มจำนวนได้');
					}
				}
			});
		} else {
			
		}
	});	
}	

function add_serial(id,store_id){
	$("#store_id_serial").val(id);
	$("#store_id_s").val(store_id);	

	$.ajax({
		url: base_url+'/facility/get_facility_store_data',
		method: 'POST',
		data: {
			'id': id
		},
		success: function(msg){
		   var obj = JSON.parse(msg);

		   if(obj.status == "TRUE") {
				$("#store_serial").val(obj.data.store_serial);
				$("#facility_status_id").val(obj.data.facility_status_id);
				$("#remark").val(obj.data.remark);

				$('#myModalSerial').modal('show');
		   }
		}
	});
}

function check_serial(){
	var store_id = $("#store_id_s").val();
	var store_id_serial = $("#store_id_serial").val();
	var store_serial = $("#store_serial").val();
	var facility_status_id = $("#facility_status_id").val();
	var remark = $("#remark").val();
			
	$.ajax({
		url: base_url+'/facility/save_form_serial',
		method: 'POST',
		data: {
			'store_serial': store_serial,
			'store_id_serial': store_id_serial,
			'facility_status_id': facility_status_id,
			'remark': remark
		},
		success: function(msg){
		   //console.log(msg); //return false;
			if(msg == 1){
				document.location.href = base_url+'facility/add?s_id='+store_id;
			}else{
				swal('ไม่สามารถเพิ่มจำนวนได้');
			}
		}
	});
}

function del_coop_data(id,store_id){	
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
				url: base_url+'/facility/del_coop_data',
				method: 'POST',
				data: {
					'table': 'coop_facility_store',
					'id': id,
					'field': 'store_id'
				},
				success: function(msg){
				   //console.log(msg); return false;
					if(msg == 1){
					  document.location.href = base_url+'facility/add?s_id='+store_id;
					}else{

					}
				}
			});
		} else {
			
		}
	});		
}

function del_all(){
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
			$('#form_del_all').submit();
		} else {
			
		}
	});	
}	

function print_coop_data(id,store_id){	
	window.open(base_url+'/facility/print_coop_data?id='+id, '_blank'); 	
}

function print_all() {
	$('#form_del_all').attr('action', base_url+'/facility/print_coop_data');
	$('#form_del_all').attr("target", "_blank");
	$('#form_del_all').submit();
	$('#form_del_all').attr('action', base_url+'/facility/print_del_allcoop_data');
	$('#form_del_all').attr("target", "_self");
}
