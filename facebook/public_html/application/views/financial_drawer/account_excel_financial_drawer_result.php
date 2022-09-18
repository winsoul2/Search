<?php
$month_arr = array('1'=>'มกราคม','2'=>'กุมภาพันธ์','3'=>'มีนาคม','4'=>'เมษายน','5'=>'พฤษภาคม','6'=>'มิถุนายน','7'=>'กรกฎาคม','8'=>'สิงหาคม','9'=>'กันยายน','10'=>'ตุลาคม','11'=>'พฤศจิกายน','12'=>'ธันวาคม');
$month_short_arr = array('1'=>'ม.ค.','2'=>'ก.พ.','3'=>'มี.ค.','4'=>'เม.ย.','5'=>'พ.ค.','6'=>'มิ.ย.','7'=>'ก.ค.','8'=>'ส.ค.','9'=>'ก.ย.','10'=>'ต.ค.','11'=>'พ.ย.','12'=>'ธ.ค.');
function U2T($text) { return @iconv("UTF-8", "TIS-620//IGNORE", trim($text)); }

$writer = new XLSXWriter();
$styles_title = array( 'font'=>'AngsanaUPC','font-size'=>18,'font-style'=>'bold', 'halign'=>'center', 'valign'=>'center' );
$styles_header = array( 'font'=>'Cordia New','font-size'=>16,'font-style'=>'bold', 'halign'=>'center', 'valign'=>'center', 'border'=>'left,right,top' ,'border-style'=>'thin');
$styles_header2 = array( 'font'=>'Cordia New','font-size'=>16,'font-style'=>'bold', 'halign'=>'center', 'valign'=>'center', 'border'=>'left,right,bottom' ,'border-style'=>'thin');
$styles_body_left_main = array( 'font'=>'Cordia New','font-size'=>16,'font-style'=>'bold', 'halign'=>'left', 'valign'=>'center', 'border'=>'multiple' ,'border-style'=>'medium');
$styles_body_left = array( 'font'=>'CordiaUPC','font-size'=>14,'font-style'=>'normal', 'halign'=>'left','valign'=>'center', 'border'=>'left,right,top,bottom');
$styles_body_right = array( 'font'=>'CordiaUPC','font-size'=>14,'font-style'=>'normal', 'halign'=>'right','valign'=>'center', 'border'=>'left,right,top,bottom');
$styles_body_center = array( 'font'=>'CordiaUPC','font-size'=>14,'font-style'=>'normal', 'halign'=>'center','valign'=>'center', 'border'=>'left,right,top,bottom');
$styles_body = array( $styles_body_center, $styles_body_center, $styles_body_center, $styles_body_left, $styles_body_center, $styles_body_center, $styles_body_left, $styles_body_center, $styles_body_center);

    $header = array(''=>'string',''=>'string',''=>'string',''=>'string',''=>'string',''=>'string',''=>'string',''=>'string',''=>'string');
    $sheet = 'sheet1';
	$count_row=0;
	$writer->writeSheetHeader($sheet, $header, $col_options = ['widths'=>[15,25,40,40,20,45,45,30,30]] );

	$writer->writeSheetRow($sheet, array($_SESSION['COOP_NAME']), $styles_title );
	$writer->writeSheetRow($sheet, array('รายงานเคาน์เตอร์ ลิ้นชัก'), $styles_title );

		$date_arr = explode('-',@$data[0]['payment_date']);
		$day = (int)@$date_arr[2];
		$month = (int)@$date_arr[1];
		$year = (int)@$date_arr[0];

	$writer->writeSheetRow($sheet, array("รายงานเคาน์เตอร์ ลิ้นชัก  ณ วันที่ {$day} {$month_arr[$month]} {$year} ของ {$user_name} "), $styles_title );


	$count_row++;
	$writer->markMergedCell($sheet, $start_row=$count_row, $start_col=0, $end_row=$count_row, $end_col=7);
	$count_row++;
	$writer->markMergedCell($sheet, $start_row=$count_row, $start_col=0, $end_row=$count_row, $end_col=7);
	$count_row++;
	$writer->markMergedCell($sheet, $start_row=$count_row, $start_col=0, $end_row=$count_row, $end_col=7);
	$count_row++;
    $writer->markMergedCell($sheet, $start_row=$count_row, $start_col=1, $end_row=$count_row, $end_col=1);
    $count_row++;
    $writer->markMergedCell($sheet, $start_row=$count_row, $start_col=1, $end_row=$count_row, $end_col=1);
    $count_row++;
    $writer->markMergedCell($sheet, $start_row=$count_row, $start_col=1, $end_row=$count_row, $end_col=1);
    $count_row++;
    $writer->markMergedCell($sheet, $start_row=$count_row, $start_col=1, $end_row=$count_row, $end_col=1);
    $count_row++;
    $writer->markMergedCell($sheet, $start_row=$count_row, $start_col=1, $end_row=$count_row, $end_col=1);
    $count_row++;


    $writer->writeSheetRow($sheet, array('ลำดับ','วันที่บันทึกบัญชี','เมนูที่ทำรายการ','สถานะการจ่ายเงิน','รายรับ','รายจ่าย'), $styles_header );
    //	$writer->writeSheetRow($sheet, array('','บัญชี','','','','','',''), $styles_header2 );
    //  echo '<pre>';print_r($drawer_detail);echo '</pre>';exit;

        $writer->writeSheetRow($sheet, array(
            '', $data[0]['payment_date'] != '' ? $this->center_function->ConvertToThaiDate($data[0]['payment_date'], '1', '0') : '',
            'ยอดเงินเคาน์เตอร์รายวัน', "เงินสด ", $drawer_detail ? " " . number_format($drawer_detail, 2).'   บาท' : '-',
            '',) , $styles_body);


    foreach($data as $key => $row) {

     $account_list_id = $row['account_list_id'] == '15' ? 'สัญญาเงินกู้ '.$coop_loan[$row['loan_id']]:' ';
     $account_list_id = $account[$row['account_list_id']]['account_list'].$account_list_id;

     $receipt_id = ' '.U2T($row['receipt_id']);
     $menu_name = $coop_menu[$row['permission_id']]['menu_name'] ;

            if($row['permission_id'] == '47'){
                //ในกรณีที่ทำที่รายการฝากถอนเงิน
                $menu_name = 'ข้อมูลบัญชีเงินฝาก/ถอน';
                if($row['statement_withdrawal_deposit'] == 'deposit' ){
                    $account_list_id = 'ฝากเงินเข้าบัญชี  '.$row['account_number'];
                }else{
                    $account_list_id = 'ถอนเงินจากบัญชี  '.$row['account_number'];
                }
            }
                    $writer->writeSheetRow($sheet, array(
                        ($key+1),
                        $row['payment_date'] != '' ? $this->center_function->ConvertToThaiDate($row['payment_date'], '1', '0') : '',
                        $menu_name ? $menu_name : '-',
                        $row['status_transfer'] == '0' ? "เงินสด " : 'เงินโอน',
                        $row['statement_status'] == 'debit' ? $row['total_amount'] ? " " . number_format($row['total_amount'], 2).'   บาท' : '-'  : '-',
                        $row['statement_status'] == 'credit' ? $row['total_amount'] ? " " . number_format($row['total_amount'], 2).'   บาท' : '-' : '-',
                    ), $styles_body);
                    $date_now = '';
                    $ref_id = '';
                    $date_now = '';

        $sum_total_amount_debit_cash   += $row['statement_status'] == 'debit' ? $row['total_amount']  : 0;
        $sum_total_amount_credit_cash  += $row['statement_status'] == 'credit' ? $row['total_amount']  : 0;
            $balance_drawer =  $row['balance_drawer'] ;
        }


        $writer->writeSheetRow($sheet, array(
            '',
            '',
            '',
            'รวม',
            $sum_total_amount_debit_cash != '' ? number_format($sum_total_amount_debit_cash, 2).'   บาท'  : '0',
            $sum_total_amount_credit_cash  != '' ? number_format($sum_total_amount_credit_cash , 2).'   บาท'  : '0',

        ), $styles_body);

        $writer->writeSheetRow($sheet, array(
            '',
            '',
            '',
            'คงเหลือเงินสด',
            $balance_drawer != '' ? number_format($balance_drawer, 2).'   บาท'  : '0',
            '',
        ), $styles_body);


$filename = "รายงานเคาน์เตอร์ ลิ้นชัก ของ  {$user_name} วันที่ {$day} {$month_arr[$month]} {$year} .xlsx";
header('Content-disposition: attachment; filename="'.XLSXWriter::sanitize_filename($filename).'"');
header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
header('Content-Transfer-Encoding: binary');
header('Cache-Control: must-revalidate');
header('Pragma: public');
$writer->writeToStdOut();
exit(0);
?>