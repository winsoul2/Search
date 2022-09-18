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
        'size'  => 15,
        'name'  => 'Angsana New'
	)
);
$textStyleArray = array(
    'font'  => array(
		'bold'  => false,
		'size'  => 15,
		'name'  => 'Angsana New'
	)
);
$headerStyle = array(
	'font'  => array(
		'bold'  => true,
		'size'  => 15,
		'name'  => 'Angsana New'
	)
);
$headerUStyle = array(
	'font'  => array(
		'bold'  => true,
		'size'  => 15,
        'name'  => 'Angsana New',
        'underline' => 'single'
	)
);
$headerDUStyle = array(
	'font'  => array(
		'bold'  => true,
		'size'  => 15,
        'name'  => 'Angsana New',
        'underline' => 'double'
	)
);
$titleStyle = array(
	'font'  => array(
		'bold'  => true,
		'size'  => 15,
		'name'  => 'Angsana New'
	)
);
$titleBorderStyle = array(
	'borders' => array(
        'allborders' => array(
        'style' => PHPExcel_Style_Border::BORDER_THIN
        )
    ),
	'font'  => array(
		'bold'  => true,
		'size'  => 15,
		'name'  => 'Angsana New'
	)
);
$footerStyle = array(
	'font'  => array(
		'bold'  => true,
		'size'  => 14,
		'name'  => 'AngsanaUPC'
	)
);
$sheet = 0;
$i=0;
$objPHPExcel->createSheet($sheet);
$objPHPExcel->setActiveSheetIndex($sheet);

$status = array(1=>"ปกติ",2=>"ขอลาออก",3=>"ลาออก",4=>"ขอรับเงินฌาปนกิจ",5=>"เสียชีวิต");

$i++;
$i_title = $i;
$objPHPExcel->getActiveSheet()->mergeCells('A'.$i.':F'.$i);
$objPHPExcel->getActiveSheet()->SetCellValue('A' . $i , $cremation['full_name'] ); 
$objPHPExcel->getActiveSheet()->getStyle('A'.$i)->applyFromArray($titleStyle);
$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':F'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

$i++;
$i_title = $i;
$objPHPExcel->getActiveSheet()->mergeCells('A'.$i.':F'.$i);
$objPHPExcel->getActiveSheet()->SetCellValue('A' . $i , "สมาชิกฌาปนกิจ"); 
$objPHPExcel->getActiveSheet()->getStyle('A'.$i)->applyFromArray($titleStyle);
$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':F'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

$i++;
$objPHPExcel->getActiveSheet()->mergeCells('A'.$i.':F'.$i);
$objPHPExcel->getActiveSheet()->SetCellValue('A' . $i , $this->center_function->ConvertToThaiDate(@date('Y-m-d'),0,0)) ;
$objPHPExcel->getActiveSheet()->getStyle('A'.$i)->applyFromArray($headerStyle);
$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':F'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

$i++;
$objPHPExcel->getActiveSheet()->mergeCells('A'.$i.':F'.$i);
$objPHPExcel->getActiveSheet()->SetCellValue('A' . $i , "ผู้ทำรายการ ".$_SESSION['USER_NAME']);
$objPHPExcel->getActiveSheet()->getStyle('A'.$i)->applyFromArray($headerStyle);
$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':F'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

$i++;
$objPHPExcel->getActiveSheet()->SetCellValue('A' . $i , "เลขฌาปนกิจ");
$objPHPExcel->getActiveSheet()->SetCellValue('B' . $i , "รหัสสมาชิก");
$objPHPExcel->getActiveSheet()->SetCellValue('C' . $i , "ชื่อ-นามสกุล");
$objPHPExcel->getActiveSheet()->SetCellValue('D' . $i , "รอบสมัคร");
$objPHPExcel->getActiveSheet()->SetCellValue('E' . $i , "สถานะ");
$objPHPExcel->getActiveSheet()->SetCellValue('F' . $i , "วันที่เป็นสมาชิก");
$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':F'.$i)->applyFromArray($titleBorderStyle);
$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':F'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getDefaultStyle()->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

$i_start = $i+1;
foreach($datas as $index=>$data) {
	$i++;
	$objPHPExcel->getActiveSheet()->setCellValueExplicit('A' . $i , $data["cremation_no"], PHPExcel_Cell_DataType::TYPE_STRING);
	$objPHPExcel->getActiveSheet()->setCellValueExplicit('B' . $i , $data["member_id"], PHPExcel_Cell_DataType::TYPE_STRING);
	$objPHPExcel->getActiveSheet()->SetCellValue('C' . $i , $data["prename_full"].$data["firstname_th"]." ".$data["lastname_th"]);
	$objPHPExcel->getActiveSheet()->SetCellValue('D' . $i , $data["period_name"]);
	$objPHPExcel->getActiveSheet()->SetCellValue('E' . $i , $status[$data["status"]]);
	$objPHPExcel->getActiveSheet()->SetCellValue('F' . $i , $this->center_function->ConvertToThaiDate($data["member_date"],'1','0'));
}
$objPHPExcel->getActiveSheet()->getStyle('A'.$i_start.':F'.$i)->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('A'.$i_start.':B'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle('C'.$i_start.':E'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$objPHPExcel->getActiveSheet()->getStyle('F'.$i_start.':F'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(40);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(17);

$objPHPExcel->getActiveSheet()->setTitle('sheet',2,2);
$sheet++;

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="สมาชิกฌาปนกิจ '.$cremation["name"].'.xlsx"');
header('Cache-Control: max-age=0');

$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
$objWriter->save('php://output');
exit;	
?>