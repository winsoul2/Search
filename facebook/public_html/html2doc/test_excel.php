<?php
/**
* @comment ส่งออกไฟล์ doc
* @projectCode 58PTY01
* @tor 3.1.4
* @package core
* @author Kiatisak  Chansawang
* @access public/private
* @created 31/05/2016
*/
# ตัวอย่างไฟล์การส่งออก ms word + จัด format file เข้ารหัสไฟล์แบบ utf-8
# รูปแบบ พรีวิวก่อนทำการกดส่งออก
header('Content-Type: text/html; charset=utf-8');
include('define_config_db.php');
?>
<html>
<head>
<!-- ไม่ต้องระบุ charset -->
<link rel="stylesheet" type="text/css" href="http://<?php echo $_SERVER['SERVER_NAME']?>/html2doc/common/global/css/bootstrap3.min.css" />
<link rel="stylesheet" type="text/css" href="http://<?php echo $_SERVER['SERVER_NAME']?>/html2doc/common/global/css/table_style.css" />
<link rel="stylesheet" type="text/css" href="http://<?php echo $_SERVER['SERVER_NAME']?>/html2doc/common/global/css/font_style.css" />
<link rel="stylesheet" type="text/css" href="http://<?php echo $_SERVER['SERVER_NAME']?>/html2doc/common/global/css/AllFont.css" />
<link rel="stylesheet" type="text/css" href="http://<?php echo $_SERVER['SERVER_NAME']?>/html2doc/common/global/css/page_loading.css" />
<link rel="stylesheet" type="text/css" href="http://<?php echo $_SERVER['SERVER_NAME']?>/html2doc/common/global/css/sweetalert.css" />
<link rel="stylesheet" type="text/css" href="http://<?php echo $_SERVER['SERVER_NAME']?>/html2doc/common/global/css/main.css" />
<!-- ไม่ต้องระบุ charset -->
</head>
<script type="application/javascript" src="common/global/js/jquery-1.10.2.js"></script>
<script>
$(document).ready(function(){
	$('#export').click(function(){
		var headContent = $('head').html();
		var bodyContent = $('#exportData').html();
		$.ajax({
			method: "POST",
			url: "html2doc_setContent.php",
			data: {htmlHead: headContent, htmlBody: bodyContent, exportData : 'on',apptype : 'application/vnd.ms-excel', filetype : 'xls'} 
			//data: {htmlBody: bodyContent, exportData : 'on', apptype : 'application/vnd.ms-excel', filetype : 'application/vnd.ms-excel'}
		}) .done(function( msg ) {
			if($.trim(msg) == 'ok'){
				window.open('html2doc.php','_blank');
			}
		});
	});
});
</script>
<style>
	@page Section1 {
		size: 8.5in 11.0in;
		margin: 1.0in 1.25in 1.0in 1.25in ;
		mso-header-margin: .5in;
		mso-footer-margin: .5in;
		mso-paper-source: 0;
	}

	#exportData {
		float:left;
		width:auto;
		height:auto;
	}
</style>
<body>
<input type="button" id="export" value="ส่งออก">
<div id="exportData">
<div style="width:595px; height:842px; margin:0px;">
	<table botder="1">
	<?php
	$rs = array();
	$max = 15000;
		for($i=0;$i<$max;$i++){
			$rs[$i]['c1'] = 'ส.001/2561';				
			$rs[$i]['c2'] = '16/03/2561';				
			$rs[$i]['c3'] = '13';				
			$rs[$i]['c4'] = '12345';				
			$rs[$i]['c5'] = 'นาย';				
			$rs[$i]['c6'] = 'ภิญญา';				
			$rs[$i]['c7'] = 'ธนากรถิรพร';				
			$rs[$i]['c8'] = 'CTEST13';				
			$rs[$i]['c9'] = '72';				
			$rs[$i]['c10'] = '20000';	
		}
		$rs = @$rs;
		$i = 0;
		if(!empty($rs)){
			foreach(@$rs as $key => $row){
				$i++;
				echo '
					<tr>
					<td>'.$i.'</td>
					<td>'.$row['c1'].'</td>
					<td>'.$row['c2'].'</td>
					<td>'.$row['c3'].'</td>
					<td>'.$row['c4'].'</td>
					<td>'.$row['c5'].'</td>
					<td>'.$row['c6'].'</td>
					<td>'.$row['c7'].'</td>
					<td>'.$row['c8'].'</td>
					<td>'.$row['c9'].'</td>
					<td>'.$row['c10'].'</td>
					</tr>	
				';
			}
		}
	?>

	</table>
</div>
</body>
</html>
