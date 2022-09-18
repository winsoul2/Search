<?php 
include '../PHPExcelReader/Classes/PHPExcel/IOFactory.php';
$objPHPExcel = PHPExcel_IOFactory::load('assets/document/department.xls');
$month_arr = array('มกราคม'=>'01','กุมภาพันธ์'=>'02','มีนาคม'=>'03','เมษายน'=>'04','พฤษภาคม'=>'05','มิถุนายน'=>'06','กรกฎาคม'=>'07','สิงหาคม'=>'08','กันยายน'=>'09','ตุลาคม'=>'10','พฤศจิกายน'=>'11','ธันวาคม'=>'12');
$month_short_arr = array('ม.ค.'=>'01','ก.พ.'=>'02','มี.ค.'=>'03','เม.ย.'=>'04','พ.ค.'=>'05','มิ.ย.'=>'06','ก.ค.'=>'07','ส.ค.'=>'08','ก.ย.'=>'09','ต.ค.'=>'10','พ.ย.'=>'11','ธ.ค.'=>'12');
$month_short_arr_eng = array('Jan'=>'01','Feb'=>'02','Mar'=>'03','Apr'=>'04','May'=>'05','Jun'=>'06','Jul'=>'07','Aug'=>'08','Sep'=>'09','Oct'=>'10','Nov'=>'11','Dec'=>'12');
	$sheetData = $objPHPExcel->setActiveSheetIndex(0);
	$yeartitle = $objPHPExcel->getActiveSheet()->getTitle();
	//echo $yeartitle."<br>";
	$sheetData = $objPHPExcel->getActiveSheet()->toArray(null,true,true,true);
	echo"<pre>";print_r($sheetData);exit;
?>