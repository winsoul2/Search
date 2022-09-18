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
ob_start();
header('Content-Type: text/html; charset=utf-8');
session_start();
if($_SESSION['exportData'] == 'on'){
    header("Content-Type: ".$_SESSION['apptype']);
    header("content-disposition: attachment;filename=".$_SESSION['filename'].".".$_SESSION['filetype']);
}
?>
<html xmlns:o='urn:schemas-microsoft-com:office:office'
 xmlns:w='urn:schemas-microsoft-com:office:word'
 xmlns:x='urn:schemas-microsoft-com:office:excel'
 xmlns='http://www.w3.org/TR/REC-html40'> 
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php echo $_SESSION['htmlHead'] ?>

</head>
<style>
	@page
    {
        mso-page-border-surround-header: no;
        mso-page-border-surround-footer: no;
    }

    @page Section1
    {
        size:841.9pt 595.3pt;
        mso-page-orientation:landscape;
        margin: 0.7cm 0.7cm 0.7cm 0.7cm;
        mso-header-margin: 42.55pt;
        mso-footer-margin: 49.6pt;
        mso-paper-source: 0;
        layout-grid: 18.0pt;
		font-family:"TH SarabunPSK";
    }
	
	.Section1 {
		page: Section1;
	}
</style>
<body class="Section1">
<font face='TH SarabunPSK'>
<?php echo $_SESSION['htmlBody'] ?>
</font>
</body>
</html>
<?php
if($_SESSION['logFile'] == 'on'){
	$out1 = ob_get_contents();
	$header = '\<?php header("Content-type: application/vnd.ms-excel"); header("Content-Disposition: attachment; filename='.$_SESSION['filename'].'.'.$_SESSION['filetype'].'"); ?>';
	file_put_contents($_SESSION['logPathFile'].$_SESSION['filename'].'.php', $header.$out1);
}
?>