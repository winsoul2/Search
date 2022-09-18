var base_url = $('#base_url').attr('class');
$( document ).ready(function() {
	$(".showpass").click(function() {
		$(this).html($(this).data("pass"));
		$(this).css("cursor", "text");
	});
	
	$(".permission_item").click(function() {
		$(this).parents("li").parents("li").children("label").children(".permission_item").prop("checked", true);
		$(this).parent("label").parent("li").children("ul").find(".permission_item").prop("checked", $(this).prop("checked"));
	});
	$("#btn_member_pic").click(function() {
        $.fancybox({
            'href' : base_url+'setting_basic_data/member_lb_upload'
            , 'padding' : '10'
            , 'width': 520
            , 'modal' : true
            , 'type' : 'iframe'
            , 'autoScale' : false
            , 'transitionIn' : 'none'
            , 'transitionOut' : 'none'
            , afterClose : function() {
                console.log($.cookies);
                if($.cookies.get('is_upload')){
					 get_image();
				}
            }
        });

        return false;
    });
});

function get_image() {
    $.ajax({
        type: "POST"
        , url: base_url+'setting_basic_data/get_image'
        , data: {
            "do" : "get_image"
            , _time : Math.random()
        }
        , success: function(data) {
            $("#member_pic").attr("src", data);
			$("#user_pic").val(data);
        }
    });
}

function search_employee_id(){
	if($('#employee_id').val()!=''){		
		$.ajax({
			url: base_url+'/setting_basic_data/search_employee',
			method: 'POST',
			data: {
				"employee_id" : $('#employee_id').val()
			},
			success: function(msg){
				//console.log(msg); 
				if(msg == 'error'){
					swal('ไม่พบข้อมูลพนักงาน','','warning');
					$('#employee_id').val('');
					$('#user_name').val('');
				}else{
					$('#user_name').val(msg);
				}
			}
		});	
	}	
}

 
function del_coop_user(id){	
	swal({
        title: " ท่านต้องการลบ User นี้ใช่หรือไม่ !",
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
				url: base_url+'/setting_basic_data/del_coop_user',
				method: 'POST',
				data: {
					'id': id,
				},
				success: function(msg){
				  // console.log(msg); return false;
					if(msg == 1){
					  document.location.href = base_url+'setting_basic_data/coop_user';
					}else{

					}
				}
			});
        } else {
			
        }
    });
	
}


