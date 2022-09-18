<?php
/**
* @comment ส่งออกไฟล์ text
* @projectCode 58PTY01
* @tor 3.1.4
* @package core
* @author Kiatisak  Chansawang
* @access public/private
* @created 31/05/2016
*/
ob_start();
header('Content-Type: text/html; charset=utf-8');
session_start();
if($_SESSION['exportData'] == 'on'){
    header("Content-Type: ".$_SESSION['apptype']);
    header("content-disposition: attachment;filename=".$_SESSION['filename'].".".$_SESSION['filetype']);
}
$tempFile = nl2br($_SESSION['htmlBody']);
$arrTempFile = explode("<br />",$tempFile);
/*echo $_SESSION['filename'].".".$_SESSION['filetype'];
$myfile = fopen($_SESSION['filename'].".".$_SESSION['filetype'], "w") or die("Unable to open file!");*/

foreach($arrTempFile as $text){
	echo $text." \r\n";
	fwrite($myfile, $txt);
}
//fclose($myfile);
?>
