<?php
include_once("../xlsxwriter.class.php");
ini_set('display_errors', 0);
ini_set('log_errors', 1);
error_reporting(E_ALL & ~E_NOTICE);

//set_include_path( get_include_path().PATH_SEPARATOR."..");
//include_once("xlsxwriter.class.php");

$chars = 'abcdefgh';

$writer = new XLSXWriter();
$writer->writeSheetHeader('Sheet1', array('c1'=>'string','c2'=>'integer','c3'=>'integer','c4'=>'integer','c5'=>'integer'), ['freeze_rows'=>1, 'freeze_columns'=>1] );
for($i=0; $i<250; $i++)
{
    $writer->writeSheetRow('Sheet1', array(
        str_shuffle($chars),
        rand()%10000,
        rand()%10000,
        rand()%10000,
        rand()%10000
    ));
}
//$writer->writeToFile('xlsx-freeze-rows-columns.xlsx');
//echo '#'.floor((memory_get_peak_usage())/1024/1024)."MB"."\n";
$filename = "xlsx-freeze-rows-columns.xlsx";
header('Content-disposition: attachment; filename="'.XLSXWriter::sanitize_filename($filename).'"');
header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
header('Content-Transfer-Encoding: binary');
header('Cache-Control: must-revalidate');
header('Pragma: public');
$writer->writeToStdOut();
exit(0);