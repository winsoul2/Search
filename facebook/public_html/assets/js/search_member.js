var base_url = $('#base_url').attr('class');
$('#search_mem').keyup(function(){
   var txt = $(this).val();  
   if(txt != '')  
   {  
		$.ajax({  
			 url: base_url+"ajax/search_member",
			 method:"post",  
			 data:{search:txt},  
			 dataType:"text",  
			 success:function(data)  
			 {  
			 //console.log(data); 
			  $('#result_member').html(data);  
			 }  
		});  
   }else{
	
   }
});