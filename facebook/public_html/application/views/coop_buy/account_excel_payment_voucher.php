<?php
$month_arr = array('1'=>'มกราคม','2'=>'กุมภาพันธ์','3'=>'มีนาคม','4'=>'เมษายน','5'=>'พฤษภาคม','6'=>'มิถุนายน','7'=>'กรกฎาคม','8'=>'สิงหาคม','9'=>'กันยายน','10'=>'ตุลาคม','11'=>'พฤศจิกายน','12'=>'ธันวาคม');
$month_short_arr = array('1'=>'ม.ค.','2'=>'ก.พ.','3'=>'มี.ค.','4'=>'เม.ย.','5'=>'พ.ค.','6'=>'มิ.ย.','7'=>'ก.ค.','8'=>'ส.ค.','9'=>'ก.ย.','10'=>'ต.ค.','11'=>'พ.ย.','12'=>'ธ.ค.');
function U2T($text) { return @iconv("UTF-8", "TIS-620//IGNORE", trim($text)); }

$writer = new XLSXWriter();
$styles_title = array( 'font'=>'AngsanaUPC','font-size'=>18,'font-style'=>'bold', 'halign'=>'center', 'valign'=>'center' );
$styles_title_copy = array( 'font'=>'AngsanaUPC','font-size'=>16,'font-style'=>'normal', 'halign'=>'right', 'valign'=>'right' );

$styles_header = array( 'font'=>'Cordia New','font-size'=>16,'font-style'=>'bold', 'halign'=>'center', 'valign'=>'center', 'border'=>'left,right,top' ,'border-style'=>'thin');
$styles_header2 = array( 'font'=>'Cordia New','font-size'=>16,'font-style'=>'bold', 'halign'=>'center', 'valign'=>'center', 'border'=>'left,right,bottom' ,'border-style'=>'thin');
$styles_body_left_main = array( 'font'=>'Cordia New','font-size'=>16,'font-style'=>'bold', 'halign'=>'left', 'valign'=>'center', 'border'=>'multiple' ,'border-style'=>'medium');
$styles_body_left = array( 'font'=>'CordiaUPC','font-size'=>14,'font-style'=>'normal', 'halign'=>'left','valign'=>'center', 'border'=>'left,right,top,bottom');
$styles_body_right = array( 'font'=>'CordiaUPC','font-size'=>14,'font-style'=>'normal', 'halign'=>'right','valign'=>'center', 'border'=>'left,right,top,bottom');
$styles_body_center = array( 'font'=>'CordiaUPC','font-size'=>14,'font-style'=>'normal', 'halign'=>'center','valign'=>'center', 'border'=>'left,right,top,bottom');
$styles_body = array( $styles_body_center, $styles_body_center, $styles_body_center, $styles_body_left, $styles_body_left, $styles_body_left);
$styles_body_bottom = array( $styles_body_center, $styles_body_center, $styles_body_center, $styles_body_left, $styles_body_left, $styles_body_left);
$styles_sum_all = array( 'font'=>'AngsanaUPC','font-size'=>14,'font-style'=>'bold', 'halign'=>'center', 'valign'=>'center' );

    $header = array(''=>'string',''=>'string',''=>'string',''=>'string',''=>'string',''=>'string');
    $sheet = 'sheet1';
	$count_row=0;
	for($i=0;$i < 2;$i++) {

        $writer->writeSheetHeader($sheet, $header, $col_options = ['widths' => [15, 45, 45, 45, 12.43, 45]]);
        if($i == 1){
            $count_row++;
            $writer->writeSheetRow($sheet, array('', '', ''), $styles_title);
            $count_row++;
            $writer->writeSheetRow($sheet, array('', '', ''), $styles_title);
            $count_row++;
            $writer->markMergedCell($sheet, $start_row = $count_row, $start_col = 0, $end_row = $count_row, $end_col = 2);
            $writer->writeSheetRow($sheet, array("สำเนา"), $styles_title_copy);
        }

        $writer->writeSheetRow($sheet, array($_SESSION['COOP_NAME']), $styles_title);
        $writer->writeSheetRow($sheet, array('สมุดรายวันทั่วไป'), $styles_title);
        //echo '<pre>';print_r($data);echo '</pre>';exit;
        if(@$data[0]['cashpay_type'] == 'payment'){
            $writer->writeSheetRow($sheet, array("ใบสำคัญจ่าย"), $styles_title);
        }else{
            $writer->writeSheetRow($sheet, array("ใบสําคัญรับ"), $styles_title);
        }



        $count_row++;
        $writer->markMergedCell($sheet, $start_row = $count_row, $start_col = 0, $end_row = $count_row, $end_col = 2);
        $count_row++;
        $writer->markMergedCell($sheet, $start_row = $count_row, $start_col = 0, $end_row = $count_row, $end_col = 2);
        $count_row++;
        $writer->markMergedCell($sheet, $start_row = $count_row, $start_col = 0, $end_row = $count_row, $end_col = 2);
        $count_row++;
        $writer->markMergedCell($sheet, $start_row = $count_row, $start_col = 1, $end_row = $count_row, $end_col = 1);
        $count_row++;
        $writer->markMergedCell($sheet, $start_row = $count_row, $start_col = 1, $end_row = $count_row, $end_col = 1);
        $count_row++;
        $writer->markMergedCell($sheet, $start_row = $count_row, $start_col = 1, $end_row = $count_row, $end_col = 1);
        $count_row++;
        $writer->markMergedCell($sheet, $start_row = $count_row, $start_col = 1, $end_row = $count_row, $end_col = 1);
        $count_row++;
        $writer->markMergedCell($sheet, $start_row = $count_row, $start_col = 1, $end_row = $count_row, $end_col = 1);
        $count_row++;
        $date_now = date('Y-m-d', strtotime($data[0]['buy_date']));

        $writer->writeSheetRow($sheet, array('เลขที่รายการ', @$data[0]['bill_number'], '', '', ''), $styles_body_left_main);
        $writer->writeSheetRow($sheet, array('วันที่', @$date_now, '', '', ''), $styles_body_left_main);
        $writer->writeSheetRow($sheet, array('อ้างอิงเอกสาร', '', '', '', '', ''), $styles_body_left_main);
        if(@$data[0]['cashpay_type'] == 'payment'){
            $writer->writeSheetRow($sheet, array('จ่ายให้', @$data[0]['pay_for'], '', '', ''), $styles_body_left_main);
        }else{
            $writer->writeSheetRow($sheet, array('ได้รับเงินจาก', @$data[0]['pay_for'], '', '', ''), $styles_body_left_main);
        }
        $writer->writeSheetRow($sheet, array('', '', '', '', '', ''), $styles_body_left_main);
        $count_row++;
        $writer->writeSheetRow($sheet, array('ลำดับที่', 'ชื่อรายการ', 'จำนวน (บาท)'), $styles_header);

        $count_num = 1;
        $summoney = 0;

        foreach ($data as $key_main => $row) {
            $count_row++;
            $writer->writeSheetRow($sheet, array(
                $count_num,
                $row['pay_description'],
                number_format( $row['pay_amount'],2)
            ), $styles_body);
            $count_num++;

            $summoney += $row['pay_amount'];
        }
        $writer->writeSheetRow($sheet, array('', '', ''), $styles_body);
        $count_row++;
        $writer->writeSheetRow($sheet, array('', '(รวม)'. $this->center_function->convert($summoney), number_format($summoney ,2)), $styles_body);
        $count_row++;
        $writer->writeSheetRow($sheet, array('', '', ''), $styles_body);

        $count_row++;
        $writer->markMergedCell($sheet, $start_row = $count_row, $start_col = 0, $end_row = $count_row, $end_col = 2);
        if(@$data[0]['cashpay_type'] == 'payment'){
            $writer->writeSheetRow($sheet, array('ลงชื่อ...........................................................................ผู้จ่ายเงิน'), $styles_sum_all);
        }else{
            $writer->writeSheetRow($sheet, array('ลงชื่อ...........................................................................ผู้รับเงิน'), $styles_sum_all);
        }
        $count_row++;
        $writer->markMergedCell($sheet, $start_row = $count_row, $start_col = 0, $end_row = $count_row, $end_col = 2);
        $writer->writeSheetRow($sheet, array('(นางกัญญ์ชิสา  สุขเจริญ)'), $styles_sum_all);
    }

//exit;
$filename = "สมุดรายวัน.xlsx";
header('Content-disposition: attachment; filename="'.XLSXWriter::sanitize_filename($filename).'"');
header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
header('Content-Transfer-Encoding: binary');
header('Cache-Control: must-revalidate');
header('Pragma: public');
$writer->writeToStdOut();
exit(0);
?>