<?php
$month_arr = array('1'=>'มกราคม','2'=>'กุมภาพันธ์','3'=>'มีนาคม','4'=>'เมษายน','5'=>'พฤษภาคม','6'=>'มิถุนายน','7'=>'กรกฎาคม','8'=>'สิงหาคม','9'=>'กันยายน','10'=>'ตุลาคม','11'=>'พฤศจิกายน','12'=>'ธันวาคม');
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
$styleArray = array(
  'borders' => array(
    'allborders' => array(
      'style' => PHPExcel_Style_Border::BORDER_THIN
    )
  ),
  'font'  => array(
		'bold'  => false,
		'size'  => 14,
		'name'  => 'Cordia New'
	)
);

$headerStyle = array(
	'font'  => array(
		'bold'  => true,
		'size'  => 14,
		'name'  => 'Cordia New'
	),
	'fill' => array(
            'type' => PHPExcel_Style_Fill::FILL_SOLID,
            'color' => array('rgb' => 'CCFFFF')
));
$titleStyle = array(
	'font'  => array(
		'bold'  => true,
		'size'  => 14,
		'name'  => 'Cordia New'
));
$objPHPExcel->getActiveSheet()->setTitle('เรียกเก็บเงินเดือน');
$objPHPExcel->getActiveSheet()->mergeCells('A1:H1');
$objPHPExcel->getActiveSheet()->SetCellValue('A1', @$_SESSION['COOP_NAME'] ) ;
$objPHPExcel->getActiveSheet()->getStyle('A1')->applyFromArray($titleStyle);
$objPHPExcel->getActiveSheet()->getStyle('A1:M1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

$objPHPExcel->getActiveSheet()->mergeCells('A2:H2');
$objPHPExcel->getActiveSheet()->SetCellValue('A2', "สังกัดหน่วยงาน :: ".@$department." ประจำเดือน".$month_arr[(int)$_GET['month']]." ".$_GET['year'] ) ;
$objPHPExcel->getActiveSheet()->getStyle('A2')->applyFromArray($titleStyle);
$objPHPExcel->getActiveSheet()->getStyle('A2:M2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
 
$objPHPExcel->getActiveSheet()->getStyle('A3:H3')->applyFromArray($headerStyle);

foreach(range('A','H') as $columnID) {
	$objPHPExcel->getActiveSheet()->getStyle($columnID.'3')->applyFromArray($borderTop);
	$objPHPExcel->getActiveSheet()->getStyle($columnID.'3')->applyFromArray($borderLeft);
	$objPHPExcel->getActiveSheet()->getStyle($columnID.'3')->applyFromArray($borderRight);
	$objPHPExcel->getActiveSheet()->getStyle($columnID.'3')->applyFromArray($borderBottom);
	$objPHPExcel->getActiveSheet()->getStyle($columnID.'3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
}
	$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(5.5);
	$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(7.38);
	$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(6.75);
	$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(10.38);
	$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(13.38);
	$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(17.25);
	$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(14.25);
	$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(19);

	$i = 3 ;
	$objPHPExcel->getActiveSheet()->SetCellValue('A' . $i , "ลำดับ" ) ; 
	$objPHPExcel->getActiveSheet()->SetCellValue('B' . $i , "รหัสสมาชิก" ) ;
	$objPHPExcel->getActiveSheet()->SetCellValue('C' . $i , "คำนำหน้าชื่อ" ) ;
	$objPHPExcel->getActiveSheet()->SetCellValue('D' . $i , "ชื่อ" ) ;
	$objPHPExcel->getActiveSheet()->SetCellValue('E' . $i , "นามสกุล" ) ;
	$objPHPExcel->getActiveSheet()->SetCellValue('F' . $i , "เลขบัตรประชาชน" ) ;
	$objPHPExcel->getActiveSheet()->SetCellValue('G' . $i , "จำนวนเงิน" ) ;
	$objPHPExcel->getActiveSheet()->SetCellValue('H' . $i , "สังกัด" ) ;
	
	
	$j=1;
	$sum_all = 0;
	//echo"<pre>";print_r($row_member);exit;
	foreach($row_member as $key => $row){
		$i++;
		$objPHPExcel->getActiveSheet()->SetCellValue('A' . $i , $j++ ) ; 
		$objPHPExcel->getActiveSheet()->SetCellValue('B' . $i , $row['member_id']." " ) ;
		$objPHPExcel->getActiveSheet()->SetCellValue('C' . $i , $row['prename_short'] ) ;
		$objPHPExcel->getActiveSheet()->SetCellValue('D' . $i , $row['firstname_th'] ) ;
		$objPHPExcel->getActiveSheet()->SetCellValue('E' . $i , $row['lastname_th'] ) ;
		$objPHPExcel->getActiveSheet()->SetCellValue('F' . $i , @$row['id_card']." " ) ;
		$objPHPExcel->getActiveSheet()->SetCellValue('G' . $i , number_format($row['sum_total'],2)." " ) ;
		$objPHPExcel->getActiveSheet()->SetCellValue('H' . $i , $row['mem_group_name'] ) ;
		
		$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':H'.$i)->applyFromArray($styleArray);
		$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':C'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('F'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('G'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
		
		$sum_all += $row['sum_total'];
	}
		$i++;
		$objPHPExcel->getActiveSheet()->mergeCells('A'.$i.":".'F'.$i);
		$objPHPExcel->getActiveSheet()->SetCellValue('A' . $i , 'รวมทั้งสิ้น' ) ;
		$objPHPExcel->getActiveSheet()->SetCellValue('G' . $i , number_format($sum_all,2) ) ;
		$objPHPExcel->getActiveSheet()->getStyle('G'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
		$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':H'.$i)->applyFromArray($styleArray);
	
				//exit;
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="รายการเรียกเก็บประจำเดือน_'.$month_arr[(int)$_GET['month']].'_'.$_GET['year'].'.xlsx"');
		header('Cache-Control: max-age=0');
				
		$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
		$objWriter->save('php://output');
exit;
?>