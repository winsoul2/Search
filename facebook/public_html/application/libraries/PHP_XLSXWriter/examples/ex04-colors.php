<?php
include_once("../xlsxwriter.class.php");
ini_set('display_errors', 0);
ini_set('log_errors', 1);
error_reporting(E_ALL & ~E_NOTICE);

//set_include_path( get_include_path().PATH_SEPARATOR."..");
//include_once("xlsxwriter.class.php");

$writer = new XLSXWriter();
$colors = array('ff','cc','99','66','33','00');
foreach($colors as $b) {
	foreach($colors as $g) {
		$rowdata = array();
		$rowstyle = array();
		foreach($colors as $r) {
			$rowdata[] = "#$r$g$b";
			$rowstyle[] = array('fill'=>"#$r$g$b");
		}
		$writer->writeSheetRow('Sheet1', $rowdata, $rowstyle );
	}
}
//$writer->writeToFile('xlsx-colors.xlsx');

$filename = "xlsx-colors.xlsx";
header('Content-disposition: attachment; filename="'.XLSXWriter::sanitize_filename($filename).'"');
header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
header('Content-Transfer-Encoding: binary');
header('Cache-Control: must-revalidate');
header('Pragma: public');
$writer->writeToStdOut();
exit(0);