var base_url = $('#base_url').attr('class');
$(document).ready(function(){  
	$('#result').hide();  
	$('#search_text').keyup(function(){  
	   var txt = $(this).val();  
	   if(txt != '')  
	   {  
			$.ajax({  
				 url:base_url+"/setting_basic_data/search_coop_address",  
				 method:"post",  
				 data:{search:txt},  
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

function del_coop_address(id){	
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
				url: base_url+'/setting_basic_data/del_coop_address',
				method: 'POST',
				data: {
					'id_zipcode': id
				},
				success: function(msg){
				   console.log(msg); 
					if(msg == 1){
					  document.location.href = base_url+'setting_basic_data/coop_address';
					  //return false;
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