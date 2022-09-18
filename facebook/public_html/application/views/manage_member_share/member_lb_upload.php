<?php
	header("Content-Type:text/html;charset=utf-8");
	date_default_timezone_set('Asia/Bangkok');
	define("PATH", $_SERVER["DOCUMENT_ROOT"].PROJECTPATH."/assets");

	function create_file_name($output_dir,$file_name){
		$list_dir = array();
		$cdir = scandir($output_dir);
		foreach ($cdir as $key => $value) {
			if (!in_array($value,array(".",".."))) {
				if (@is_dir(@$dir . DIRECTORY_SEPARATOR . $value)){
					$list_dir[$value] = dirToArray(@$dir . DIRECTORY_SEPARATOR . $value);
				}else{
					if(substr($value,0,8) == date('Ymd')){
						$list_dir[] = $value;
					}
				}
			}
		}
		$explode_arr=array();
		foreach($list_dir as $key => $value){
			$task = explode('.',$value);
			$task2 = explode('_',$task[0]);
			$explode_arr[] = $task2[1];
		}
		$max_run_num = sprintf("%04d",count($explode_arr)+1);
		$explode_old_file = explode('.',$file_name);
		$new_file_name = date('Ymd')."_".$max_run_num.".".$explode_old_file[(count($explode_old_file)-1)];
		return $new_file_name;
	}

	if(@$_GET["do"] == "reupload") {
		setcookie("IMG", "", time() - 3600);
		setcookie("IMG_W", "", time() - 3600);
		setcookie("IMG_H", "", time() - 3600);
		setcookie("is_upload", "", time() - 3600);
		echo "<script type='text/javascript'>document.location.href = window.location.pathname.split( '/' )[window.location.pathname.split( '/' ).length-1]</script>";
		exit();
	}

	if(@$_POST["do"]=="upload") {
		setcookie("IMG", "", time() - 3600);
		setcookie("IMG_W", "", time() - 3600);
		setcookie("IMG_H", "", time() - 3600);
		ini_set('memory_limit', '64M');
		if(!empty($_FILES["request_file"]["name"])) {
			$tmp = pathinfo($_FILES["request_file"]["name"]);
			$ext = $tmp['extension'];
			//if(in_array(strtolower($ext), array("jpg", "gif", "png"))) {
			if(in_array(strtolower($ext), array("jpg"))) {

				//$ints = date('YmdGis').random_char(4);
				//$prefix = $ints;
				//$filename = "{$prefix}.{$ext}";
				$output_dir = $_SERVER["DOCUMENT_ROOT"].PROJECTPATH."/assets/uploads/tmp/";
				$filename = $this->center_function->create_file_name($output_dir, $_FILES["request_file"]["name"]);
				$prefix = explode('.',$filename);
				$prefix = $prefix[0];

				copy($_FILES["request_file"]["tmp_name"], $output_dir.$filename);
				list($width, $height, $type, $attr) = getimagesize($output_dir.$filename);
				//echo"<pre>";print_r(getimagesize(PATH."/uploads/tmp/{$filename}"));echo"</pre>";exit;
				if($width > $height) {
					$set_width = 9999;
					$set_height = 300;
				} else {
					$set_width = 300;
					$set_height = 9999;
				}

				if($width < 300 || $height < 300) {
					echo "<script type='text/javascript'>alert('ไฟล์แนบ ต้องมีขนาด 300 x 300 ขึ้นไป');</script>";
				} else {

					$path = glob($output_dir."*"); // get all file names
					foreach($path as $file){ // iterate files
						$info = pathinfo($file);
						if(is_file($file) && $info["filename"] != $filename) {
							//unlink($file);
						}
					}

					setcookie("is_upload", "", time() - 3600);

					//----------------------------------------- Start
					$srcFile = $_FILES["request_file"]["tmp_name"];
					$destPath = $_SERVER["DOCUMENT_ROOT"].PROJECTPATH."/assets/uploads/tmp/";
					$image = $this->image;
					$image->sizelimit_x = $set_width;
					$image->sizelimit_y = $set_height;
					$image->keep_proportions = true;
					if($image->resize_image($srcFile) === true) {
						$image->save_resizedimage($destPath, $prefix);
					}
					$image->destroy_resizedimage();
					//----------------------------------------- End

					chmod($output_dir.$filename, 0777);

					list($width, $height, $type, $attr) = getimagesize($output_dir.$filename);
					setcookie("IMG", $filename, time() + (60 * 60 * 2));
					setcookie("IMG_W", $width, time() + (60 * 60 * 2));
					setcookie("IMG_H", $height, time() + (60 * 60 * 2));
					//exit;
				}
			} else {
				echo "<script type='text/javascript'>alert('ไฟล์แนบ มีชนิดไฟล์เป็น jpg เท่านั้น');</script>";
			}
		}
		//echo "<script type='text/javascript'>parent.jQuery.fancybox.close();</script>";
		echo "<script type='text/javascript'>document.location.href='?';</script>";
		exit();
	}

	if(@$_POST["do"]=="crop") {
		if(!empty($_POST["img"])) {
			$targ_w = $targ_h = 300;
			$jpeg_quality = 100;

			$src = $_SERVER["DOCUMENT_ROOT"].PROJECTPATH."/assets/uploads/tmp/".$_POST["img"];
			$ext = pathinfo($src, PATHINFO_EXTENSION);

			$img_r = imagecreatefromjpeg($src);
			$dst_r = ImageCreateTrueColor( $targ_w, $targ_h );

			//$ints = date('YmdGis').random_char(4);
			//$prefix = $ints;
			//$filename = "{$prefix}.{$ext}";

			$output_dir = $_SERVER["DOCUMENT_ROOT"].PROJECTPATH."/assets/uploads/tmp/";
			$filename = $this->center_function->create_file_name($output_dir, $_POST["img"]);

			$x = (int) $_POST["x"] ;
			$y = (int) $_POST["y"] ;
			$w = (int) $_POST["w"] ;
			$h = (int) $_POST["h"] ;

			//echo $x . " " . $y . " " . $w . " " . $h ;

			imagecopyresampled($dst_r, $img_r, 0, 0, $x , $y  , $targ_w, $targ_h, $w , $h );

			@imagejpeg($dst_r , $output_dir.$filename,$jpeg_quality);
			setcookie("IMG", $filename, time() + (60 * 60 * 2));
			setcookie("IMG_W", $w, time() + (60 * 60 * 2));
			setcookie("IMG_H", $h, time() + (60 * 60 * 2));
			setcookie("is_upload", 1, time() + (60 * 60 * 2));
			@unlink($src);
		}
		//echo"<pre>";print_r($_COOKIE);echo"</pre>";
		echo "<script type='text/javascript'>parent.jQuery.fancybox.close();</script>";
		exit();
	}
?>
<!DOCTYPE HTML>
<html lang="en">
<head>
	<title></title>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=Edge"/>

	<?php
	$link = array(
		'src' => PROJECTJSPATH.'assets/js/jquery-1.10.2.min.js',
		'type' => 'text/javascript'
	);
	echo script_tag($link);
	$link = array(
		'src' => PROJECTJSPATH.'assets/js/jquery-migrate-1.2.1.js',
		'type' => 'text/javascript'
	);
	echo script_tag($link);
	$link = array(
		'src' => PROJECTJSPATH.'assets/js/jquery.cookies.2.2.0.min.js',
		'type' => 'text/javascript'
	);
	echo script_tag($link);

	$link = array(
		'href' => PROJECTJSPATH.'assets/css/vendor.min.css',
		'rel' => 'stylesheet',
		'type' => 'text/css'
	);
	echo link_tag($link);
	$link = array(
		'href' => PROJECTJSPATH.'assets/css/elephant.min.css',
		'rel' => 'stylesheet',
		'type' => 'text/css'
	);
	echo link_tag($link);

	$link = array(
		'src' => PROJECTJSPATH.'assets/js/jquery.Jcrop.js',
		'type' => 'text/javascript'
	);
	echo script_tag($link);
	$link = array(
		'href' => PROJECTJSPATH.'assets/css/jquery.Jcrop.css',
		'rel' => 'stylesheet',
		'type' => 'text/css'
	);
	echo link_tag($link);
	?>
<style type="text/css">
	body {
		overflow: hidden;
		padding: 15px;
		potition: relative;
	}
	* { font-family: 'THSarabunNew' !important; }
	.jcrop-holder {
		margin: 0 auto !important;
	}
	input[type=file] {
		padding: 8px;
		vertical-align: top;
	}
</style>
<script type="text/javascript">
	$(document).ready(function() {
		function updateCoords(c) {
			$('#x').val(c.x);
			$('#img_x').val(c.x);

			$('#y').val(c.y);
			$('#img_y').val(c.y);

			$('#w').val(c.w);
			$('#h').val(c.h);
		};

		if($("#img").size()>0) {
			var min = $.cookies.get('IMG_W')>$.cookies.get('IMG_H')?$.cookies.get('IMG_H'):$.cookies.get('IMG_W');
			$("#min").val(min);
			$('#cropbox').Jcrop({
				aspectRatio: 1
				, onSelect: updateCoords
				, minSize: [ min, min ]
				, setSelect:   [ 0, 0, min, min ]
				, bgOpacity: 0.3
			});
		}

		$("body").on("click", ".btn-approve", function() {
			$("#frmCrop").submit();
			return true;
		});

		$("body").on("click", "#btn-upload", function() {
			$('.wait').css('display', 'block');
			$("#frm-upload").submit();
			return true;
		});

		$("body").on("click", "#btn-reupload", function() {
			document.location.href = window.location.pathname.split( '/' )[window.location.pathname.split( '/' ).length-1] + '?do=reupload' ;
		});

	});
</script>
</head>
<body>

<div style="text-align: center;">
	<?php if(!empty($_COOKIE["IMG"])) {
		//echo"<pre>";print_r(PATH."/uploads/tmp/".$_COOKIE["IMG"]);echo"</pre>";
		?>
		<form id="frmCrop" name="frmCrop" action="" method="post" enctype="multipart/form-data">
			<img src="<?php echo base_url(PROJECTPATH."/assets/uploads/tmp/".$_COOKIE["IMG"]); ?>" id="cropbox" width="<?php echo $_COOKIE["IMG_W"]; ?>" height="<?php echo $_COOKIE["IMG_H"]; ?>" />
			<input type="hidden" id="img" name="img" value="<?php echo $_COOKIE["IMG"]; ?>" />
			<input type="hidden" id="x" name="x" />
			<input type="hidden" id="y" name="y" />
			<input type="hidden" id="w" name="w" />
			<input type="hidden" id="h" name="h" />
			<input type="hidden" id="min" name="min" />
			<input type="hidden" name="do" value="crop" />
			<div style="clear: both;"></div>
		</form>
	<?php } else { ?>
	<form id="frm-upload" name="frm-upload" action="" method="post" enctype="multipart/form-data">
		<div class="form-group">
			<label></label>
			<input type="file" id="request_file" name="request_file" class="form-control" style="height: auto;" accept=".jpg" />
		</div>
		<div style="margin-top: 5px;">
			<input type="hidden" name="do" value="upload" />
		</div>
		<div class="align-center" style="margin: 3px auto;"><span style="font-weight: bold;">* <span style="text-decoration: underline;">หมายเหตุ</span></span> รูปที่อัพโหลดควรมีขนาด 300 x 300 และเป็นไฟล์นามสกุล  jpg เท่านั้นค่ะ</div>
		<div class="wait" style="display: none;"><img src="<?php echo base_url(PROJECTPATH."/assets/images/wait.gif"); ?>" /></div>
	</form>
	<?php } ?>
</div>
<div class="text-center" style="margin-top: 10px;">
	<input type="button" id="btn-close" name="btn-close" class="btn" onclick="parent.jQuery.fancybox.close();" value="ปิดหน้าต่าง" />
	<?php if(!empty($_COOKIE["IMG"])) { ?>
			<input type="button" id="btn-reupload" name="btn-reupload" class="btn btn-success" value="กลับไปอัพโหลดรูปใหม่" />
			<input type="button" id="btn-submit" name="btn-submit" class="btn btn-primary btn-approve" value="ตกลง" />
	<?php } else { ?>
			<input type="button" id="btn-upload" name="btn-upload" class="btn btn-primary" value="อัพโหลด" />
	<?php } ?>
</div>
</body>
</html>