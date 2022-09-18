<?php
$objPHPExcel = new PHPExcel();

$borderRight = array(
  'borders' => array(
    'right' => array(
      'style' => PHPExcel_Style_Border::BORDER_THIN
    )
  )
);
$borderLeft = array(
  'borders' => array(
    'left' => array(
      'style' => PHPExcel_Style_Border::BORDER_THIN
    )
  )
);
$borderTop = array(
  'borders' => array(
    'top' => array(
      'style' => PHPExcel_Style_Border::BORDER_THIN
    )
  )
);
$borderBottom = array(
  'borders' => array(
    'bottom' => array(
      'style' => PHPExcel_Style_Border::BORDER_THIN
    )
  )
);
$borderBottomDouble = array(
  'borders' => array(
    'bottom' => array(
      'style' => PHPExcel_Style_Border::BORDER_DOUBLE
    )
  )
);
$styleArray = array(
  'borders' => array(
    'allborders' => array(
      'style' => PHPExcel_Style_Border::BORDER_THIN
    )
  ),
  'font'  => array(
		'bold'  => false,
		'size'  => 16,
		'name'  => 'Cordia New'
	)
);
$textStyleArray = array(
  'font'  => array(
		'bold'  => false,
		'size'  => 16,
		'name'  => 'Angsana New'
	)
);
$headerStyle = array(
	'font'  => array(
		'bold'  => false,
		'size'  => 16,
		'name'  => 'Angsana New'
	)
);
$titleStyle = array(
	'font'  => array(
		'bold'  => true,
		'size'  => 18,
		'name'  => 'TH Sarabun New'
	)
);
$footerStyle = array(
	'font'  => array(
		'bold'  => true,
		'size'  => 14,
		'name'  => 'AngsanaUPC'
	)
);
$table = array(
    'borders' => array(
        'allborders' => array(
            'style' => PHPExcel_Style_Border::BORDER_THIN
        )
    ),
);

if(@$_GET['from_date']){
    $start_date_arr = explode('/',@$_GET['from_date']);
    $start_day = $start_date_arr[0];
    $start_month = $start_date_arr[1];
    $start_year = $start_date_arr[2];
    $start_year -= 543;
    $start_date = $start_year.'-'.$start_month.'-'.$start_day;
}

if(@$_GET['thur_date']){
    $end_date_arr = explode('/',@$_GET['thur_date']);
    $end_day = $end_date_arr[0];
    $end_month = $end_date_arr[1];
    $end_year = $end_date_arr[2];
    $end_year -= 543;
    $end_date = $end_year.'-'.$end_month.'-'.$end_day;
}

$date_title .= "ประจำวันที่ ".$this->center_function->ConvertToThaiDate($start_date, false);

$sheet = 0;
$i=0;
$objPHPExcel->createSheet($sheet);
$objPHPExcel->setActiveSheetIndex($sheet);
$i+=1;
$objPHPExcel->getActiveSheet()->mergeCells('A'.$i.':E'.$i);
$objPHPExcel->getActiveSheet()->SetCellValue('A' . $i , $_SESSION['COOP_NAME'] ) ;
$objPHPExcel->getActiveSheet()->getStyle('A'.$i)->applyFromArray($headerStyle);
$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':E'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

$i+=1;
$objPHPExcel->getActiveSheet()->mergeCells('A'.$i.':E'.$i);
$objPHPExcel->getActiveSheet()->SetCellValue('A' . $i , "สมุดรายวันรับ-สมุดรายวันจ่าย" ) ;
$objPHPExcel->getActiveSheet()->getStyle('A'.$i)->applyFromArray($headerStyle);
$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':E'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

$i+=1;
$objPHPExcel->getActiveSheet()->mergeCells('A'.$i.':E'.$i);
$objPHPExcel->getActiveSheet()->SetCellValue('A' . $i , $date_title ) ;
$objPHPExcel->getActiveSheet()->getStyle('A'.$i)->applyFromArray($headerStyle);
$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':E'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

$i+=1;
$objPHPExcel->getActiveSheet()->mergeCells('A'.$i.':E'.$i);
$objPHPExcel->getActiveSheet()->SetCellValue('A' . $i , "รายการสด" ) ;
$objPHPExcel->getActiveSheet()->getStyle('A'.$i)->applyFromArray($headerStyle);
$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':E'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

$i+=1;
$objPHPExcel->getActiveSheet()->mergeCells('A'.$i.':E'.$i);
$objPHPExcel->getActiveSheet()->SetCellValue('A' . $i , $this->center_function->ConvertToThaiDate(@date('Y-m-d'),0,0)) ;
$objPHPExcel->getActiveSheet()->getStyle('A'.$i)->applyFromArray($headerStyle);
$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':E'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
$i+=1;
$objPHPExcel->getActiveSheet()->mergeCells('A'.$i.':E'.$i);
$objPHPExcel->getActiveSheet()->SetCellValue('A' . $i , "ผู้ทำรายการ ".$_SESSION['USER_NAME']) ;
$objPHPExcel->getActiveSheet()->getStyle('A'.$i)->applyFromArray($headerStyle);
$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':E'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

$i+=1;
$i_top = $i;
$objPHPExcel->getActiveSheet()->SetCellValue('A'.$i, "เลขที่บัญชี");
$objPHPExcel->getActiveSheet()->SetCellValue('B'.$i, "รายการ");
$objPHPExcel->getActiveSheet()->SetCellValue('C'.$i, "เลขที่อ้างอิง");
$objPHPExcel->getActiveSheet()->SetCellValue('D'.$i, "เดบิต");
$objPHPExcel->getActiveSheet()->SetCellValue('E'.$i, "เครดิต");
$objPHPExcel->getActiveSheet()->getStyle('A'.$i.":E".$i)->applyFromArray($table);

$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(13.43);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(57.86);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(13.43);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(19.29);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(19.29);

$total_debit = 0;
$total_credit = 0;
$pv_count = 0;
$rv_count = 0;
foreach($datas as $data) {
    $i++;
    if($data["journal_type"] == "P") $pv_count++;
    if($data["journal_type"] == "R") $rv_count++;

    if($rv_count == 1) {
        $objPHPExcel->getActiveSheet()->SetCellValue('A'.$i, "รายจ่าย");
        $objPHPExcel->getActiveSheet()->mergeCells('A'.$i.':B'.$i);
        $objPHPExcel->getActiveSheet()->SetCellValue('C'.$i, "");
        $objPHPExcel->getActiveSheet()->SetCellValue('D'.$i, "");
        $objPHPExcel->getActiveSheet()->SetCellValue('E'.$i, "");
        $objPHPExcel->getActiveSheet()->getStyle('A'.$i.":E".$i)->applyFromArray($table);
        $i++;
    }
    if($pv_count == 1) {
        $objPHPExcel->getActiveSheet()->SetCellValue('A'.$i, "รายรับ");
        $objPHPExcel->getActiveSheet()->mergeCells('A'.$i.':B'.$i);
        $objPHPExcel->getActiveSheet()->SetCellValue('C'.$i, "");
        $objPHPExcel->getActiveSheet()->SetCellValue('D'.$i, "");
        $objPHPExcel->getActiveSheet()->SetCellValue('E'.$i, "");
        $objPHPExcel->getActiveSheet()->getStyle('A'.$i.":E".$i)->applyFromArray($table);
        $i++;
    }

    $journal_ref = substr($data["journal_ref"],0,1)."-".substr($data["journal_ref"],1,7)."-".substr($data["journal_ref"],8,3);

    $objPHPExcel->getActiveSheet()->SetCellValue('A'.$i, $data["account_chart_id"]);
    $objPHPExcel->getActiveSheet()->SetCellValue('B'.$i, $data["account_chart"]);
    $objPHPExcel->getActiveSheet()->SetCellValue('C'.$i, $journal_ref);
    $objPHPExcel->getActiveSheet()->SetCellValue('D'.$i, $data["account_type"] == "debit" ? $data["amount"] : "");
    $objPHPExcel->getActiveSheet()->SetCellValue('E'.$i, $data["account_type"] == "credit" ? $data["amount"] : "");
    $total_debit += $data["debit"];
    $total_credit += $data["credit"];
    $objPHPExcel->getActiveSheet()->getStyle('A'.$i.":E".$i)->applyFromArray($table);

    $total_debit += $data["account_type"] == "debit" ? $data["amount"] : 0;
    $total_credit += $data["account_type"] == "credit" ? $data["amount"] : 0;
}

$i++;
$objPHPExcel->getActiveSheet()->SetCellValue('A'.$i, "รวม : รายรับ-รายจ่าย");
$objPHPExcel->getActiveSheet()->mergeCells('A'.$i.':C'.$i);
$objPHPExcel->getActiveSheet()->SetCellValue('D'.$i, $total_debit);
$objPHPExcel->getActiveSheet()->SetCellValue('E'.$i, $total_credit);
$objPHPExcel->getActiveSheet()->getStyle('A'.$i.":E".$i)->applyFromArray($table);

$diff = $total_debit - $total_credit;
$rv = $cash_balance + $diff_cash;
$pv = $cash_balance - $diff + $diff_cash;

$i++;
$objPHPExcel->getActiveSheet()->SetCellValue('A'.$i, "      รวมเงิน");
$objPHPExcel->getActiveSheet()->mergeCells('A'.$i.':C'.$i);
$objPHPExcel->getActiveSheet()->SetCellValue('D'.$i, "");
$objPHPExcel->getActiveSheet()->SetCellValue('E'.$i, $rv);
$objPHPExcel->getActiveSheet()->getStyle('A'.$i.":E".$i)->applyFromArray($table);

$i++;
$objPHPExcel->getActiveSheet()->SetCellValue('A'.$i, "      หักยอดรวมจ่ายยกไป");
$objPHPExcel->getActiveSheet()->mergeCells('A'.$i.':C'.$i);
$objPHPExcel->getActiveSheet()->SetCellValue('D'.$i, $pv);
$objPHPExcel->getActiveSheet()->SetCellValue('E'.$i, "");
$objPHPExcel->getActiveSheet()->getStyle('A'.$i.":E".$i)->applyFromArray($table);

$i++;
$objPHPExcel->getActiveSheet()->SetCellValue('A'.$i, "ยอดรวมเงินคงเหลือ");
$objPHPExcel->getActiveSheet()->mergeCells('A'.$i.':C'.$i);
$objPHPExcel->getActiveSheet()->SetCellValue('D'.$i, $pv+$total_debit);
$objPHPExcel->getActiveSheet()->SetCellValue('E'.$i, $rv+$total_credit);
$objPHPExcel->getActiveSheet()->getStyle('A'.$i.":E".$i)->applyFromArray($table);

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="สมุดรายวันรับ-สมุดรายวันจ่าย.xlsx"');
header('Cache-Control: max-age=0');

$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
$objWriter->save('php://output');
exit;
?>