var base_url = $('#base_url').attr('class');
$(document).ready(function(){ 
    $(".container").addClass('container-fluid');
    $(".layout-header").hide();
    $(".layout-sidebar-backdrop").hide();
    $(".layout-sidebar-body").hide();
    $(".layout-footer").hide();

      $('#result').hide();  
      $('#search_text').keyup(function(){  
           var txt = $(this).val();  
           var txt_bank_id = $("#bank_id").val();
           if(txt != '')  
           {  
                $.ajax({  
                     //url:"/ajax/ajax_search_bank.php",  
					 url:base_url+"/setting_basic_data/search_bank",  
                     method:"post",  
                     data:{search:txt,bank_id:txt_bank_id},  
                     dataType:"text",  
                     success:function(data)  
                     {  
                     // console.log(data); 
                          if (data == "") {
                            $("#table_first").show();
                            $('#result').hide(); 
                            $(".pagination").show();
                          }else{
                            $("#table_first").hide();
                            $(".pagination").hide();
                            $("#result").show();
                            $('#result').html(data);  
                          }
                     }  
                });  
           }else{
                  $("#table_first").show();
                  $(".pagination").show();
                  $('#result').hide();  
           }
            
      });  
	
 });


 
function del_coop_basic_data(id,bank_id){	
	swal({
        title: "คุณต้องการที่จะลบ",
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
				url: base_url+'/setting_basic_data/del_coop_basic_data',
				method: 'POST',
				data: {
					'table': 'coop_bank_branch',
					'id': id,
					'field': 'branch_id'
				},
				success: function(msg){
				  // console.log(msg); return false;
					if(msg == 1){
					  document.location.href = base_url+'setting_basic_data/coop_bank_branch?bank_id='+bank_id;
					}else{

					}
				}
			});
        } else {
			
        }
    });
	
}


$('.province').change(function(){
	var province_id = $(this).val();
	$.ajax({
		method: 'POST',
		url: base_url+'/setting_basic_data/select_coop_address',
		data: { "province_id": province_id },
		success: function(msg){
		  // console.log(msg); return false;
		   $(".amphur").html(msg)
		}
	});
});

function check_form(){
	var text_alert = '';
	if($.trim($('#branch_code').val())== ''){
		text_alert += ' - รหัสสาขา\n';
	}
	if($.trim($('#branch_name').val())== ''){
		text_alert += ' - ชื่อสาขา\n';
	}
	if($.trim($('#province_id').val())== ''){
		text_alert += ' - จังหวัด\n';
	}
	if($.trim($('#amphur_id').val())== ''){
		text_alert += ' - อำเภอ\n';
	}
	
	if(text_alert != ''){
		swal('กรุณากรอกข้อมูลต่อไปนี้',text_alert,'warning');
	}else{
		$('#form_save').submit();
	}
	
}



