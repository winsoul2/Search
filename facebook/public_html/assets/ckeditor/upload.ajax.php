<?php
	unset($error);
	date_default_timezone_set('Asia/Bangkok');
	
	if(empty($_FILES)) {
		echo "{";
		echo				"error: 'กรุณาเลือกไฟล์ก่อนค่ะ',\n";
		echo				"msg: ''\n";
		echo "}";
	} else {
		$error = "" ; 
		$pathname = "uploads/contents";
		if(!empty($_FILES["file"])) {
			$ints = date('YmdGis');
			if($_FILES["file"]["type"]=="image/png"||$_FILES["file"]["type"]=="image/x-png")
				$imgsn = $ints.".png";
			if($_FILES["file"]["type"]=="image/gif")
				$imgsn = $ints.".gif";
			elseif($_FILES["file"]["type"]=="image/pjpeg"||$_FILES["file"]["type"]=="image/jpeg")
				$imgsn = $ints.".jpg";
			if(!empty($imgsn)) {
				copy($_FILES["file"]["tmp_name"],$_SERVER['DOCUMENT_ROOT']."/{$pathname}/{$imgsn}");
				$returnvalue="http://".$_SERVER["HTTP_HOST"]."/{$pathname}/{$imgsn}";
					echo "{";
					echo				"error: '{$error}',\n";
					echo				"msg: '{$returnvalue}'\n";
					echo "}";
			}
		}
	}
?>