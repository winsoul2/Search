<?php
error_reporting(E_ERROR);
/** PHPExcel */ 
require_once dirname(__FILE__) . '/../Classes/PHPExcel.php';
// Create new PHPExcel object
$objPHPExcel = new PHPExcel();
// Set properties
$objPHPExcel->getProperties()->setCreator("test");
// Add some data
$txt = range('A','D');
for($i=0; $i< 8; $i++){
	foreach($txt as $key => $val)
	{
	// Set column widths
	$objPHPExcel->getActiveSheet()->getColumnDimension(''.$val.'')->setWidth(10);
	 
	$objPHPExcel->getActiveSheet()->getRowDimension(''.$i.'')->setRowHeight(23);
	 
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue(''.$val.''.$i.'', ''.$val.'');
	 
	$objPHPExcel->getActiveSheet()->getStyle(''.$val.''.$i.'')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
	$objPHPExcel->getActiveSheet()->getStyle(''.$val.''.$i.'')->getFill()->getStartColor()->setARGB('5cb3fc');
	}
}
// Rename sheet
$objPHPExcel->getActiveSheet()->setTitle('test');
 
// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);
 
// Create a new worksheet, after the default sheet
$objPHPExcel->createSheet();
 
/** PHPExcel_IOFactory */
require_once './Classes/PHPExcel/IOFactory.php';
 
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save(''.iconv('UTF-8','TIS-620','ทดสอบ').'.xls');
