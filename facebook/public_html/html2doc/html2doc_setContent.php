<?php
/**
* @comment ส่งออก doc
* @projectCode 58PTY01
* @tor 3.1.4
* @package core
* @author Kiatisak  Chansawang
* @access public/private
* @created 31/05/2016
*/

header('Content-Type: text/html; charset=utf-8');
session_start();
date_default_timezone_set('Asia/Bangkok');
$_SESSION['exportData'] = $_REQUEST['exportData'];

/* Config Apptype and filetype
.doc      application/msword
.dot      application/msword

.xls      application/vnd.ms-excel
.xlt      application/vnd.ms-excel
.xla      application/vnd.ms-excel

.ppt      application/vnd.ms-powerpoint
.pot      application/vnd.ms-powerpoint
.pps      application/vnd.ms-powerpoint
.ppa      application/vnd.ms-powerpoint
*/
$_SESSION['apptype'] = ($_REQUEST['apptype'] == '') ? 'application/msword' : $_REQUEST['apptype'];
$_SESSION['filetype'] = ($_REQUEST['apptype'] == '') ? 'doc' : $_REQUEST['filetype'];

$_SESSION['filename'] = ($_REQUEST['filename'] == '') ? 'export'.date('YmdHis') : $_REQUEST['filename'];
$_SESSION['htmlHead'] = $_REQUEST['htmlHead'];
$_SESSION['htmlBody'] = $_REQUEST['htmlBody'];
$_SESSION['logFile'] = $_REQUEST['logFile'];
$_SESSION['logPathFile'] = $_REQUEST['logPathFile'];
echo 'ok';
?>