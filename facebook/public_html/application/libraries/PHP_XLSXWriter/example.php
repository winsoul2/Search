<?php
include_once("xlsxwriter.class.php");
ini_set('display_errors', 0);
ini_set('log_errors', 1);
error_reporting(E_ALL & ~E_NOTICE);
/*
$rows = array(
    array('2003','1','-50.5','2010-01-01 23:00:00','2012-12-31 23:00:00'),
    array('2003','=B1', '23.5','2010-01-01 00:00:00','2012-12-31 00:00:00'),
);

*/


$rows = array();
$max = 10;
for($i=0;$i<$max;$i++){
	$rows[$i]['c1'] = 'ส.001/2561';				
	$rows[$i]['c2'] = '16/03/2561';				
	$rows[$i]['c3'] = '13';				
	$rows[$i]['c4'] = '12345';				
	$rows[$i]['c5'] = 'นาย';				
	$rows[$i]['c6'] = 'ภิญญา';				
	$rows[$i]['c7'] = 'ธนากรถิรพร';				
	$rows[$i]['c8'] = 'CTEST13';				
	$rows[$i]['c9'] = '72';				
	$rows[$i]['c10'] = '20000';	
	$rows[$i]['c11'] = 'ธนากรถิรพร';	
	$rows[$i]['c12'] = 'ธนากรถิรพร';	
	$rows[$i]['c13'] = 'ธนากรถิรพร';	
	//$rows[$i]['c14'] = 'ธนากรถิรพร';	
	//$rows[$i]['c15'] = 'ธนากรถิรพร';	
	//$rows[$i]['c16'] = 'ธนากรถิรพร';	
	//$rows[$i]['c17'] = 'ธนากรถิรพร';	
	//$rows[$i]['c18'] = 'ธนากรถิรพร';	
	//$rows[$i]['c19'] = 'ธนากรถิรพร';	
	//$rows[$i]['c20'] = 'ธนากรถิรพร';	
}

//echo '<pre>'; print_r($rows); echo '</pre>';
//exit;



$writer = new XLSXWriter();
$writer->setAuthor('Some Author'); 
$i = 0;
foreach($rows as $row){

		$writer->writeSheetRow('Sheet1', $row);
		$writer->writeSheetRow('Sheet2', $row);

	
}	

$filename = "example.xlsx";
header('Content-disposition: attachment; filename="'.XLSXWriter::sanitize_filename($filename).'"');
header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
header('Content-Transfer-Encoding: binary');
header('Cache-Control: must-revalidate');
header('Pragma: public');
$writer->writeToStdOut();

//$writer->writeToFile('example.xlsx');
//echo $writer->writeToString();
exit(0);


