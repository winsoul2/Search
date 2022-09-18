<?php
$month_arr = array('1' => 'มกราคม', '2' => 'กุมภาพันธ์', '3' => 'มีนาคม', '4' => 'เมษายน', '5' => 'พฤษภาคม', '6' => 'มิถุนายน', '7' => 'กรกฎาคม', '8' => 'สิงหาคม', '9' => 'กันยายน', '10' => 'ตุลาคม', '11' => 'พฤศจิกายน', '12' => 'ธันวาคม');

$objPHPExcel = new PHPExcel();
$objPHPExcel->setActiveSheetIndex(0);

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
	'font' => array(
		'bold' => false,
		'size' => 16,
		'name' => 'Cordia New'
	)
);
$textStyle = array(
	'font' => array(
		'bold' => false,
		'size' => 16,
		'name' => 'Cordia New'
	)
);
$textStyleBold = array(
	'font' => array(
		'bold' => true,
		'size' => 16,
		'name' => 'Cordia New'
	)
);
$textStyleRed = array(
	'font' => array(
		'bold' => false,
		'size' => 16,
		'color' => array('rgb' => 'FF0000'),
		'name' => 'Cordia New'
	)
);
$textStyleGreen = array(
	'font' => array(
		'bold' => false,
		'size' => 16,
		'color' => array('rgb' => '339966'),
		'name' => 'Cordia New'
	)
);
$textStyleResult = array(
	'font' => array(
		'bold' => false,
		'size' => 16,
		'color' => array('rgb' => '3366FF'),
		'name' => 'Cordia New'
	)
);
$headerStyle = array(
	'font' => array(
		'bold' => true,
		'size' => 16,
		'name' => 'Cordia New',
		'margin'=> 10
	),
	'fill' => array(
		'type' => PHPExcel_Style_Fill::FILL_SOLID,
	));
$runno = 1;
$row_2 = 0;
$objPHPExcel->getActiveSheet()->getStyle('A1:E6')->applyFromArray($headerStyle);
$objPHPExcel->getActiveSheet()->mergeCells('A1:E1');
$objPHPExcel->getActiveSheet()->SetCellValue('A1',$text_report);
$objPHPExcel->getActiveSheet()->mergeCells('A2:E2');
$objPHPExcel->getActiveSheet()->SetCellValue('A2',$deposit_type);
$objPHPExcel->getActiveSheet()->mergeCells('A3:E3');
$objPHPExcel->getActiveSheet()->SetCellValue('A3',$text_interest);
$objPHPExcel->getActiveSheet()->SetCellValue('A4'," ");
$i = 5;
$objPHPExcel->getActiveSheet()->SetCellValue('A' . $i, "ลำดับ");
$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(7.57);
$objPHPExcel->getActiveSheet()->SetCellValue('B' . $i, "ชื่อ - สกุล");
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(26.71);
$objPHPExcel->getActiveSheet()->SetCellValue('C' . $i, "ทะเบียน");
$objPHPExcel->getActiveSheet()->SetCellValue('C' . ($i + 1), "สมาชิก");
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(12.86);
$objPHPExcel->getActiveSheet()->SetCellValue('D' . $i, "เลขที่บัญชี");
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(16.43);
$objPHPExcel->getActiveSheet()->SetCellValue('E' . $i, "ดอกเบี้ยค้างจ่าย");
$objPHPExcel->getActiveSheet()->SetCellValue('E' . ($i + 1), "ณ ".$text_end_date);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(18.29);
foreach (range('A', 'E') as $columnID) {
	$objPHPExcel->getActiveSheet()->getStyle($columnID . '5')->applyFromArray($borderTop);
	$objPHPExcel->getActiveSheet()->getStyle($columnID . '5')->applyFromArray($borderLeft);
	$objPHPExcel->getActiveSheet()->getStyle($columnID . '5')->applyFromArray($borderRight);
	$objPHPExcel->getActiveSheet()->getStyle($columnID . '6')->applyFromArray($borderLeft);
	$objPHPExcel->getActiveSheet()->getStyle($columnID . '6')->applyFromArray($borderRight);
	$objPHPExcel->getActiveSheet()->getStyle($columnID . '6')->applyFromArray($borderBottom);

	$objPHPExcel->getActiveSheet()->getStyle($columnID . "5")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$objPHPExcel->getActiveSheet()->getStyle($columnID . "6")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$objPHPExcel->getActiveSheet()->getStyle($columnID . "9")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
}
$i = 7;

$runno = $last_runno;
if (!empty($data)) {
	foreach (@$data as $key => $row) {
		$runno++;

		$objPHPExcel->getActiveSheet()->getStyle('A' . $i . ':A' . ($i ))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('B' . $i . ':B' . ($i ))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
		$objPHPExcel->getActiveSheet()->getStyle('C' . $i . ':C' . ($i ))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('C' . $i . ':C' . ($i ))->getNumberFormat()->setFormatCode('00000');
		$objPHPExcel->getActiveSheet()->getStyle('D' . $i . ':D' . ($i ))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('E' . $i . ':E' . ($i ))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
		$objPHPExcel->getActiveSheet()->getStyle('A' . $i . ':E' . $i)->applyFromArray($styleArray);
		$objPHPExcel->getActiveSheet()->SetCellValue('A' . $i,$runno);//ลำดับที่
		$objPHPExcel->getActiveSheet()->SetCellValue('B' . $i,$row['full_member_name']);//ชื่อ-สกุล
		$objPHPExcel->getActiveSheet()->SetCellValue('C' . $i,$row['member_id']);//ทะเบียน
		$objPHPExcel->getActiveSheet()->SetCellValue('D' . $i,$row['account_id']);//เลขที่บัญชี
		$objPHPExcel->getActiveSheet()->SetCellValue('E' . $i,number_format($row['deposit_interest'],2));//ดอกเบี้ยค้างจ่าย
		$i++;
		$row_2 = $row_2+$row['deposit_interest'];
	}
}
$objPHPExcel->getActiveSheet()->getStyle('A' . $i . ':A' . ($i ))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle('B' . $i . ':B' . ($i ))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$objPHPExcel->getActiveSheet()->getStyle('C' . $i . ':C' . ($i ))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle('E' . $i . ':E' . ($i ))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
$objPHPExcel->getActiveSheet()->getStyle('A' . $i . ':E' . $i)->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->mergeCells('A'.$i.':D'.$i);
$objPHPExcel->getActiveSheet()->SetCellValue('A'. $i , "รวมเป็นเงิน" );
$objPHPExcel->getActiveSheet()->SetCellValue('E'. $i ,number_format($row_2,2));
$titleStyle = array(
	'font' => array(
		'bold' => true,
		'size' => 16,
		'name' => 'Cordia New'
	));
$objPHPExcel->getActiveSheet()->getStyle('A1')->applyFromArray($titleStyle);
$objPHPExcel->getActiveSheet()->getStyle('A1:D1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle('A2:D2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle('A3:D3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="รายงานดอกเบี้ยคค้างจ่าย_' . $month_arr[(int)$_GET['month']] . '_' . $_GET['year'] . '.xlsx"');
header('Cache-Control: max-age=0');
$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
$objWriter->save('php://output');
exit;
?>
