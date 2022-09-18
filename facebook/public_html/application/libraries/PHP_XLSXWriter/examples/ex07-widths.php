<?php
include_once("../xlsxwriter.class.php");
ini_set('display_errors', 0);
ini_set('log_errors', 1);
error_reporting(E_ALL & ~E_NOTICE);

//set_include_path( get_include_path().PATH_SEPARATOR."..");
//include_once("xlsxwriter.class.php");


$writer = new XLSXWriter();
$writer->writeSheetHeader('Sheet1', $rowdata = array(300,234,456,789), $col_options = ['widths'=>[10,20,30,40]] );
$writer->writeSheetRow('Sheet1', $rowdata = array(300,234,456,789), $row_options = ['height'=>20] );
$writer->writeSheetRow('Sheet1', $rowdata = array(300,234,456,789), $row_options = ['height'=>30] );
$writer->writeSheetRow('Sheet1', $rowdata = array(300,234,456,789), $row_options = ['height'=>40] );
//$writer->writeToFile('xlsx-widths.xlsx');

$filename = "xlsx-widths.xlsx";
header('Content-disposition: attachment; filename="'.XLSXWriter::sanitize_filename($filename).'"');
header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
header('Content-Transfer-Encoding: binary');
header('Cache-Control: must-revalidate');
header('Pragma: public');
$writer->writeToStdOut();
exit(0);


