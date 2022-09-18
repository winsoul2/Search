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
$objPHPExcel->getActiveSheet()->getStyle('A1:H3')->applyFromArray($headerStyle);
$objPHPExcel->getActiveSheet()->mergeCells('A1:H1');
$objPHPExcel->getActiveSheet()->SetCellValue('A1',$_SESSION['COOP_NAME']);
$objPHPExcel->getActiveSheet()->SetCellValue('A2',"วันที่พิมพ์ :".date("Y/m/d"));
$objPHPExcel->getActiveSheet()->SetCellValue('A3',"เวลาพิมพ์ :".date("h:i:s"));
$objPHPExcel->getActiveSheet()->mergeCells('C2:F2');
$objPHPExcel->getActiveSheet()->SetCellValue('C2',"รายงานผังบัญชี ");
$objPHPExcel->getActiveSheet()->SetCellValue('H3',"ผู้พิมพ์".$_SESSION['USER_NAME']);
$i = 4;
$objPHPExcel->getActiveSheet()->SetCellValue('A' . $i, "รหัสบัญชี");
$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(17.57);
$objPHPExcel->getActiveSheet()->SetCellValue('B' . $i, "ชื่อบัญชี");
$objPHPExcel->getActiveSheet()->mergeCells('B'.$i.':D'.$i);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(21.71);
$objPHPExcel->getActiveSheet()->SetCellValue('E' . $i, "หมวดบัญชี");
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(13.86);
$objPHPExcel->getActiveSheet()->SetCellValue('F' . $i, "ดุลบัญชี");
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(12.29);
$objPHPExcel->getActiveSheet()->SetCellValue('G' . $i, "ประเภทบัญชี");
$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(14.43);
$objPHPExcel->getActiveSheet()->SetCellValue('H' . $i, "รหัสบัญชีคุม");
$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(18.29);
foreach (range('A', 'H') as $columnID) {
	$objPHPExcel->getActiveSheet()->getStyle($columnID . '4')->applyFromArray($borderTop);
	$objPHPExcel->getActiveSheet()->getStyle($columnID . '4')->applyFromArray($borderLeft);
	$objPHPExcel->getActiveSheet()->getStyle($columnID . '4')->applyFromArray($borderRight);
	$objPHPExcel->getActiveSheet()->getStyle($columnID . '4')->applyFromArray($borderLeft);
	$objPHPExcel->getActiveSheet()->getStyle($columnID . '4')->applyFromArray($borderRight);
	$objPHPExcel->getActiveSheet()->getStyle($columnID . '4')->applyFromArray($borderBottom);

	$objPHPExcel->getActiveSheet()->getStyle($columnID . "4")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$objPHPExcel->getActiveSheet()->getStyle($columnID . "6")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$objPHPExcel->getActiveSheet()->getStyle($columnID . "9")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
}
$i = 5;

$runno = $last_runno;
if (!empty($coop_account_chart)) {
	foreach (@$coop_account_chart as $key => $value) {
		$runno++;
		$groups = substr($value['account_chart_id'],0,1);

		$objPHPExcel->getActiveSheet()->getStyle('A' . $i . ':A' . ($i ))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('B' . $i . ':B' . ($i ))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
		$objPHPExcel->getActiveSheet()->mergeCells('B' . $i . ':D' . ($i ));
		$objPHPExcel->getActiveSheet()->getStyle('E' . $i . ':E' . ($i ))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('F' . $i . ':F' . ($i ))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('G' . $i . ':G' . ($i ))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('A' . $i . ':H' . $i)->applyFromArray($styleArray);
		$objPHPExcel->getActiveSheet()->SetCellValue('A' . $i,$value['account_chart_id']);//รหัสบัญชี
		$objPHPExcel->getActiveSheet()->SetCellValue('B' . $i,$value['account_chart']);//ชื่อบัญชี
		$objPHPExcel->getActiveSheet()->SetCellValue('E' . $i,$account_chart_groups[$groups-1]['account_chart']);//หมวดบัญชี
		$objPHPExcel->getActiveSheet()->SetCellValue('F' . $i,$value['entry_type'] == 1 ? "เดบิต" : "เครดิต");//ดุลบัญชี
		$objPHPExcel->getActiveSheet()->SetCellValue('G' . $i,$value['type'] == 1 || $value["type"] == 2 ? "บัญชีคุม" : "บัญชีย่อย");//ประเภทบัญชี
		$objPHPExcel->getActiveSheet()->SetCellValue('H' . $i,$value['account_parent_id']);//รหัสบัญชีคุม
		$i++;

	}
}

$titleStyle = array(
	'font' => array(
		'bold' => true,
		'size' => 16,
		'name' => 'Cordia New'
	));
$objPHPExcel->getActiveSheet()->getStyle('A1')->applyFromArray($titleStyle);
$objPHPExcel->getActiveSheet()->getStyle('A1:H1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle('A2:H2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle('A3:H3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="รายงานรายการผังบัญชี.xlsx"');
header('Cache-Control: max-age=0');
$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
$objWriter->save('php://output');
exit;
?>
