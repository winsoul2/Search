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
		'size'  => 13,
		'name'  => 'Cordia New'
	)
);
$textStyleArray = array(
  'font'  => array(
		'bold'  => false,
		'size'  => 13,
		'name'  => 'Cordia New'
	)
);
$sumStyleArray = array(
  'font'  => array(
		'bold'  => true,
		'size'  => 14,
		'name'  => 'Cordia New'
	)
);
$headerStyle = array(
	'font'  => array(
		'bold'  => true,
		'size'  => 13,
		'name'  => 'Cordia New'
	)
);
$titleStyle = array(
	'font'  => array(
		'bold'  => true,
		'size'  => 14,
		'name'  => 'Cordia New'
	)
);

if(@$_GET['start_date']){
	$start_date_arr = explode('/',@$_GET['start_date']);
	$start_day = $start_date_arr[0];
	$start_month = $start_date_arr[1];
	$start_year = $start_date_arr[2];
	$start_year -= 543;
	$start_date = $start_year.'-'.$start_month.'-'.$start_day;
}

if(@$_GET['end_date']){
	$end_date_arr = explode('/',@$_GET['end_date']);
	$end_day = $end_date_arr[0];
	$end_month = $end_date_arr[1];
	$end_year = $end_date_arr[2];
	$end_year -= 543;
	$end_date = $end_year.'-'.$end_month.'-'.$end_day;
}

$sheet = 0;
//echo '<pre>'; print_r($datas); echo '</pre>';
//exit;
$titile_date = "วันที่ ".$this->center_function->ConvertToThaiDate($start_date);
$titile_date.= (@$_GET['start_date'] == @$_GET['end_date'])?"":"  ถึง  ".$this->center_function->ConvertToThaiDate($end_date);

foreach($datas as $key_sheet=>$data) {
	$i=0;
	$objPHPExcel->createSheet($sheet);
	$objPHPExcel->setActiveSheetIndex($sheet);
	$objPHPExcel->getActiveSheet()->setTitle(@$data['type_name']);
	$sheet++;
	
		$i+=1;
		$objPHPExcel->getActiveSheet()->mergeCells('A'.$i.':L'.$i);
		$objPHPExcel->getActiveSheet()->SetCellValue('A'.$i, @$_SESSION['COOP_NAME'] ) ;
		$objPHPExcel->getActiveSheet()->getStyle('A'.$i)->applyFromArray($titleStyle);
		$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':L'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		
		$i+=1;
		$objPHPExcel->getActiveSheet()->mergeCells('A'.$i.':L'.$i);
		$objPHPExcel->getActiveSheet()->SetCellValue('A'.$i, @$data['type_name'] ) ;
		$objPHPExcel->getActiveSheet()->getStyle('A'.$i)->applyFromArray($titleStyle);
		$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':L'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		
		$i+=1;
		$objPHPExcel->getActiveSheet()->mergeCells('A'.$i.':L'.$i);
		$objPHPExcel->getActiveSheet()->SetCellValue('A'.$i, @$titile_date ) ;
		$objPHPExcel->getActiveSheet()->getStyle('A'.$i)->applyFromArray($titleStyle);
		$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':L'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		
		$i+=1;
		$objPHPExcel->getActiveSheet()->mergeCells('A'.$i.':L'.$i);
		$objPHPExcel->getActiveSheet()->SetCellValue('A'.$i, 'รายชื่อที่ทำประกันกับบริษัท : บริษัท เอ็ม บี เค ไลฟ์ ประกันชีวิต จำกัด (มหาชน)' ) ;
		$objPHPExcel->getActiveSheet()->getStyle('A'.$i)->applyFromArray($titleStyle);
		$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':L'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		
		$i+=1;
		$i_top = $i;
		$objPHPExcel->getActiveSheet()->SetCellValue('A' . $i , "ลำดับ" ) ;
		$objPHPExcel->getActiveSheet()->mergeCells('A'.$i.':A'.($i+1));		
		$objPHPExcel->getActiveSheet()->SetCellValue('B' . $i , "เลขทะเบียนสมาชิก" ) ; 
		$objPHPExcel->getActiveSheet()->mergeCells('B'.$i.':B'.($i+1));	
		$objPHPExcel->getActiveSheet()->SetCellValue('C' . $i, "เลขที่บัตรประชาชน" ) ;
		$objPHPExcel->getActiveSheet()->mergeCells('C'.$i.':C'.($i+1));	
		$objPHPExcel->getActiveSheet()->SetCellValue('D' . $i, "ชื่อ  -  นามสกุล" ) ;	
		$objPHPExcel->getActiveSheet()->mergeCells('D'.$i.':D'.($i+1));	
		$objPHPExcel->getActiveSheet()->SetCellValue('E' . $i, "เพศ" ) ;	
		$objPHPExcel->getActiveSheet()->mergeCells('E'.$i.':E'.($i+1));	
		$objPHPExcel->getActiveSheet()->SetCellValue('F' . $i, "วันเดือนปีเกิด" ) ;
		$objPHPExcel->getActiveSheet()->mergeCells('F'.$i.':F'.($i+1));	
		$objPHPExcel->getActiveSheet()->SetCellValue('G' . $i, "อายุ" ) ;	
		$objPHPExcel->getActiveSheet()->mergeCells('G'.$i.':G'.($i+1));	
		$objPHPExcel->getActiveSheet()->SetCellValue('H' . $i, "สมาชิกเดิม" ) ;
		$objPHPExcel->getActiveSheet()->mergeCells('H'.$i.':I'.$i);	
		$objPHPExcel->getActiveSheet()->SetCellValue('J' . $i, "ทุนประกันใหม่" ) ;
		$objPHPExcel->getActiveSheet()->mergeCells('J'.$i.':J'.($i+1));	
		$objPHPExcel->getActiveSheet()->SetCellValue('K' . $i, "เบี้ยประกันปรับทุนประกัน" ) ;
		$objPHPExcel->getActiveSheet()->mergeCells('K'.$i.':K'.($i+1));	
		$objPHPExcel->getActiveSheet()->SetCellValue('L' . $i, "วันเริ่มความคุ้มครอง" ) ;
		$objPHPExcel->getActiveSheet()->mergeCells('L'.$i.':L'.($i+1));	
		
		$i+=1;
		$i_bottom = $i;
		$objPHPExcel->getActiveSheet()->SetCellValue('H' . $i , "ทุนเดิม" ) ; 
		$objPHPExcel->getActiveSheet()->SetCellValue('I' . $i, "เพิ่ม/ลด ทุน" ) ;
		$objPHPExcel->getActiveSheet()->getStyle('H'.$i.':I'.$i)->applyFromArray($borderTop);
		
		$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(6);
		$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
		$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
		$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(40);
		$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(10);
		$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
		$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(10);
		$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(15);
		$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(15);
		$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(15);
		$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(20);
		$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(20);
		
		foreach(range('A','L') as $columnID) {
			$objPHPExcel->getActiveSheet()->getStyle($columnID.$i_top)->applyFromArray($borderTop);
			$objPHPExcel->getActiveSheet()->getStyle($columnID.$i_top)->applyFromArray($borderLeft);
			$objPHPExcel->getActiveSheet()->getStyle($columnID.$i_top)->applyFromArray($borderRight);
			$objPHPExcel->getActiveSheet()->getStyle($columnID.$i_bottom)->applyFromArray($borderLeft);
			$objPHPExcel->getActiveSheet()->getStyle($columnID.$i_bottom)->applyFromArray($borderRight);
			$objPHPExcel->getActiveSheet()->getStyle($columnID.$i_bottom)->applyFromArray($borderBottom);
			
			$objPHPExcel->getActiveSheet()->getStyle($columnID.$i_top)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle($columnID.$i_bottom)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		}
		$objPHPExcel->getActiveSheet()->getStyle('A'.$i_top.':L'.$i_bottom)->applyFromArray($headerStyle);
		$objPHPExcel->getActiveSheet()->getStyle('A'.$i_top.':L'.$i_bottom)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
		$j = 1;
		$sum_insurance_premium = 0;
		if(!empty($data['data'])){
			foreach($data['data'] as $key => $row){
				
				$i+=1;
				$objPHPExcel->getActiveSheet()->SetCellValue('A' . $i , $j++ );
				$objPHPExcel->getActiveSheet()->SetCellValue('B' . $i , $row['member_id'] );
				$objPHPExcel->getActiveSheet()->SetCellValue('C' . $i , @$row['id_card'] );
				$objPHPExcel->getActiveSheet()->SetCellValue('D' . $i , @$row['full_name'] );
				$objPHPExcel->getActiveSheet()->SetCellValue('E' . $i , @$row['sex'] );
				$objPHPExcel->getActiveSheet()->SetCellValue('F' . $i , @$this->center_function->ConvertToThaiDate(@$row["birthday"],0) );
				$objPHPExcel->getActiveSheet()->SetCellValue('G' . $i , @$this->center_function->cal_age(@$row["birthday"]) );
				$objPHPExcel->getActiveSheet()->SetCellValue('H' . $i , number_format(@$row["insurance_old"],2) );
				$objPHPExcel->getActiveSheet()->SetCellValue('I' . $i , number_format(@$row["insurance_amount"],2) );
				$objPHPExcel->getActiveSheet()->SetCellValue('J' . $i , number_format(@$row["insurance_new"],2) );
				$objPHPExcel->getActiveSheet()->SetCellValue('K' . $i , number_format(@$row["insurance_premium"],2) );
				$objPHPExcel->getActiveSheet()->SetCellValue('L' . $i , @$this->center_function->ConvertToThaiDate(@$row["insurance_date"],1,0) );
				
				$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':E'.$i)->applyFromArray($styleArray);
				$objPHPExcel->getActiveSheet()->getStyle('F'.$i.':L'.$i)->applyFromArray($styleArray);
				
				$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':C'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$objPHPExcel->getActiveSheet()->getStyle('E'.$i.':G'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$objPHPExcel->getActiveSheet()->getStyle('H'.$i.':K'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
				$objPHPExcel->getActiveSheet()->getStyle('L'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$objPHPExcel->getActiveSheet()->getStyle('B'.$i)->getNumberFormat()->setFormatCode('000000');
				$objPHPExcel->getActiveSheet()->getStyle('C'.$i)->getNumberFormat()->setFormatCode('0');
				$sum_insurance_premium += @$row["insurance_premium"];
			}
		}
	$i+=1;
	
	$objPHPExcel->getActiveSheet()->SetCellValue('A' . $i , 'รวม' );
	$objPHPExcel->getActiveSheet()->mergeCells('A'.$i.':D'.$i);	
	$objPHPExcel->getActiveSheet()->SetCellValue('E' . $i , '' );
	$objPHPExcel->getActiveSheet()->SetCellValue('F' . $i , '' );
	$objPHPExcel->getActiveSheet()->SetCellValue('G' . $i , '' );
	$objPHPExcel->getActiveSheet()->SetCellValue('H' . $i , '' );
	$objPHPExcel->getActiveSheet()->SetCellValue('I' . $i , '' );
	$objPHPExcel->getActiveSheet()->SetCellValue('J' . $i , '' );
	$objPHPExcel->getActiveSheet()->SetCellValue('K' . $i , number_format(@$sum_insurance_premium,2) );
	$objPHPExcel->getActiveSheet()->SetCellValue('L' . $i , '' );
	
	$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':L'.$i)->applyFromArray($styleArray);
	$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':L'.$i)->applyFromArray($titleStyle);
	$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':D'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$objPHPExcel->getActiveSheet()->getStyle('K'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
	
	$i+=2;
	
}

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="รายงานประกันชีวิต.xlsx"');
header('Cache-Control: max-age=0');

$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
$objWriter->save('php://output');
exit;	
?>