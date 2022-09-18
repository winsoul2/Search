<!--<script src="https://code.jquery.com/jquery-1.10.2.js"></script>-->
<?php
 $link = array(
          'src' => '/assets/js/jquery-1.10.2.js',
          'language' => 'javascript',
          'type' => 'text/javascript'
      );
      echo script_tag($link);
?>
<input type="hidden" name="receipt_id" id="receipt_id" class="receipt_id" value="<?php echo $receipt_id?>"/>

<script>
var base_url = '<?php echo PROJECTJSPATH?>';
var id = '<?php echo $_GET['id']?>';
var mode = '<?php echo $_GET['mode']?>';

$( document ).ready(function() {
	var receipt_number = $("#receipt_id").val();
	if(mode == 'pdf'){
		load_pdf(mode);
	}else{		
		window.setTimeout(function() {
			
			
			window.location.href =  base_url+'/cremation/cremation_receipt_pdf?id='+id+'&mode=pdf';	
				
		}, 1000);
	}
});

function load_pdf(mode){	
	var receipt_number = $("#receipt_id").val();
	if(mode == 'pdf'){
		window.location.href =  base_url+'/admin/receipt_form_pdf/'+receipt_number;
	}
}
</script>