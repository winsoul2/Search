<?php
$month_arr = array('1'=>'มกราคม','2'=>'กุมภาพันธ์','3'=>'มีนาคม','4'=>'เมษายน','5'=>'พฤษภาคม','6'=>'มิถุนายน','7'=>'กรกฎาคม','8'=>'สิงหาคม','9'=>'กันยายน','10'=>'ตุลาคม','11'=>'พฤศจิกายน','12'=>'ธันวาคม');
$month_short_arr = array('1'=>'ม.ค.','2'=>'ก.พ.','3'=>'มี.ค.','4'=>'เม.ย.','5'=>'พ.ค.','6'=>'มิ.ย.','7'=>'ก.ค.','8'=>'ส.ค.','9'=>'ก.ย.','10'=>'ต.ค.','11'=>'พ.ย.','12'=>'ธ.ค.');
function U2T($text) { return @iconv("UTF-8", "TIS-620//IGNORE", trim($text)); }

$writer = new XLSXWriter();
$styles_title = array( 'font'=>'AngsanaUPC','font-size'=>18,'font-style'=>'bold', 'halign'=>'center', 'valign'=>'center' );
$styles_header = array( 'font'=>'Cordia New','font-size'=>16,'font-style'=>'bold', 'halign'=>'center', 'valign'=>'center', 'border'=>'left,right,top' ,'border-style'=>'thin');
$styles_header2 = array( 'font'=>'Cordia New','font-size'=>16,'font-style'=>'bold', 'halign'=>'center', 'valign'=>'center', 'border'=>'left,right,bottom' ,'border-style'=>'thin');
$styles_body_left = array( 'font'=>'CordiaUPC','font-size'=>14,'font-style'=>'normal', 'halign'=>'left','valign'=>'center', 'border'=>'left,right,top,bottom');
$styles_body_right = array( 'font'=>'CordiaUPC','font-size'=>14,'font-style'=>'normal', 'halign'=>'right','valign'=>'center', 'border'=>'left,right,top,bottom');
$styles_body_center = array( 'font'=>'CordiaUPC','font-size'=>14,'font-style'=>'normal', 'halign'=>'center','valign'=>'center', 'border'=>'left,right,top,bottom');

$styles_bold_left = array( 'font'=>'CordiaUPC','font-size'=>14,'font-style'=>'bold', 'halign'=>'left','valign'=>'center', 'border'=>'left,right,top,bottom','border-style'=>'medium');
$styles_bold_right = array( 'font'=>'CordiaUPC','font-size'=>14,'font-style'=>'bold', 'halign'=>'right','valign'=>'center', 'border'=>'left,right,top,bottom');
$styles_bold_center = array( 'font'=>'CordiaUPC','font-size'=>14,'font-style'=>'bold', 'halign'=>'center','valign'=>'center', 'border'=>'left,right,top,bottom');

$styles_body = array( $styles_body_left, $styles_body_left, $styles_body_left, $styles_body_right, $styles_body_right, $styles_body_left);
$styles_total = array( $styles_bold_left, $styles_bold_left, $styles_bold_left, $styles_bold_right, $styles_bold_right, $styles_bold_left);
$header = array(''=>'string',''=>'string',''=>'string',''=>'string',''=>'string',''=>'string');
$sheet = 'sheet1';
$count_row=0;
$writer->writeSheetHeader($sheet, $header, $col_options = ['widths'=>[10.30,10.29,45,12.43,12.43,45]] );
$writer->writeSheetRow($sheet, array($_SESSION['COOP_NAME']), $styles_title );
$writer->writeSheetRow($sheet, array('สมุดรายวันทั่วไป'), $styles_title );
if(!empty($_GET['report_date'])){
	$date_arr = explode('/',@$_GET['report_date']);
	$day = (int)@$date_arr[0];
	$month = (int)@$date_arr[1];
	$year = (int)@$date_arr[2];
	$year -= 543;
	$year_be = $year+543;
	$writer->writeSheetRow($sheet, array("ณ วันที่ {$day} {$month_arr[$month]} {$year_be} "), $styles_title );
}else if(!empty($_GET['month'])){
	$year_be = $_GET['year'];
	$writer->writeSheetRow($sheet, array("ประจำเดือน {$month_arr[$month]} {$year_be} "), $styles_title );
}else if(!empty($_GET['year'])){
	$year_be = $_GET['year'];
	$writer->writeSheetRow($sheet, array("ประจำปี  {$year_be} "), $styles_title );
}else{
	// $writer->writeSheetRow($sheet, array("รายงานสมุดรายวันทั่วไป"), $styles_title );
}
$count_row++;
$writer->markMergedCell($sheet, $start_row=$count_row, $start_col=0, $end_row=$count_row, $end_col=5);
$count_row++;
$writer->markMergedCell($sheet, $start_row=$count_row, $start_col=0, $end_row=$count_row, $end_col=5);
$count_row++;
$writer->markMergedCell($sheet, $start_row=$count_row, $start_col=0, $end_row=$count_row, $end_col=5);
$count_row++;
for($i=0;$i<=5;$i++){
	if(!in_array($i,array('2','3','4'))){
		$writer->markMergedCell($sheet, $start_row=$count_row, $start_col=$i, $end_row=($count_row+1), $end_col=$i);
	}
}
$writer->writeSheetRow($sheet, array('ว.ด.ป.','เลขที่บัญชี','รายการ','เดบิต','เครดิต','เลขที่ใบเสร็จ'), $styles_header );
$writer->writeSheetRow($sheet, array('','บัญชี','','','',''), $styles_header2 );

foreach($data as $key_main => $sort) {
	foreach($sort as $row1) {
		foreach ($row1 as $key => $row2) {
			$group_credit_total = 0;
			$group_debit_total = 0;
			$journal_type = '';
			$date_now = date('Y-m-d', strtotime($key_main));
			foreach ($row2 as $key2 => $row_detail) {
				if($row_detail['ref_type'] == "RECEIPT"){
					$ref_id = $row_detail['ref_id'];
				}else{
					$ref_id = '';
				}
				$writer->writeSheetRow($sheet, array(
					$date_now != '' ? $this->center_function->ConvertToThaiDate($date_now, '1', '0') : '',
					$row_detail['account_chart_id'],
					$row_detail['account_type'] == 'debit' ? $row_detail['account_chart'] : $row_detail['account_chart'],
					$row_detail['account_type'] == 'debit' ? " " . number_format($row_detail['account_amount'], 2) : '',
					$row_detail['account_type'] == 'credit' ? " " . number_format($row_detail['account_amount'], 2) : '',
					!empty($ref_id) ? " " . U2T($ref_id) : '-'
				), $styles_body);
				$date_now = '';
				$ref_id = '';
				$date_now = '';
				if($row_detail['account_type'] == 'debit') {
					$group_debit_total += $row_detail['account_amount'];
				} else {
					$group_credit_total += $row_detail['account_amount'];
				}
				$journal_type = $row_detail["journal_type"];
			}
			$txt = $journal_type == "P" ? "          รวมรายการจ่าย" : ($journal_type == "R" ? "          รวมรายการรับ" : ($journal_type == "J" ? "          รวมรายการโอน" : "          รวมรายการปรับปรุง"));
			$writer->writeSheetRow($sheet, array(
				'',
				'',
				$txt,
				number_format($group_debit_total,2),
				number_format($group_credit_total,2),
				''
			), $styles_total);
		}
	}
}
$filename = "สมุดรายวัน.xlsx";
header('Content-disposition: attachment; filename="'.XLSXWriter::sanitize_filename($filename).'"');
header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
header('Content-Transfer-Encoding: binary');
header('Cache-Control: must-revalidate');
header('Pragma: public');
$writer->writeToStdOut();
exit(0);
?>