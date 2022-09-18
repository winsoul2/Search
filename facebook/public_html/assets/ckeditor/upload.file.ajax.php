<?php
	unset($error);
	if(empty($_FILES)) {
		echo "{";
		echo				"error: 'กรุณาเลือกไฟล์',\n";
		echo				"msg: ''\n";
		echo "}";
	} else {


		$pathname = "uploads/contents";
		if(!empty($_FILES["file"])) {
			$x = pathinfo($_FILES["file"]["name"]);
			$ext = $x["extension"];
			$filename = date('YmdGis').".{$ext}";

			copy($_FILES["file"]["tmp_name"],$_SERVER['DOCUMENT_ROOT']."/{$pathname}/{$filename}");
			$returnvalue = "http://".$_SERVER["HTTP_HOST"]."/{$pathname}/{$filename}";
			//$data["error"] = $error;
			//$data["msg"] = $returnvalue;
			//echo json_encode($data);

				echo "{";
				echo				"error: '{$error}',\n";
				echo				"msg: '{$returnvalue}'\n";
				echo "}";

		}
	}
?>