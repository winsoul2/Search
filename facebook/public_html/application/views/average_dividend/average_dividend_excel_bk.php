<?php
$objPHPExcel = new PHPExcel();
//echo"<pre>";print_r($arr_data);exit;
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
		'name'  => 'CordiaUPC'
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
		'name'  => 'AngsanaUPC'
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
		$i+=1;
		$i_title = $i;
		$objPHPExcel->getActiveSheet()->mergeCells('A'.$i.':L'.$i);
		$objPHPExcel->getActiveSheet()->SetCellValue('A' . $i , "รายการปันผลและเฉลี่ยคืน ปี ".(date('Y')+543) ) ; 
		$objPHPExcel->getActiveSheet()->getStyle('A'.$i)->applyFromArray($titleStyle);
		$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':L'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		
		$i+=1;
		$i_top = $i;
		$objPHPExcel->getActiveSheet()->SetCellValue('A' . $i , "ลำดับ" ) ; 
		$objPHPExcel->getActiveSheet()->SetCellValue('B' . $i , "เลขทะเบียน" ) ; 
		$objPHPExcel->getActiveSheet()->SetCellValue('C' . $i , "รหัส" ) ; 
		$objPHPExcel->getActiveSheet()->mergeCells('D'.$i.':D'.($i+1));
		$objPHPExcel->getActiveSheet()->SetCellValue('D' . $i , "สังกัด" ) ; 
		$objPHPExcel->getActiveSheet()->mergeCells('E'.$i.':E'.($i+1));
		$objPHPExcel->getActiveSheet()->SetCellValue('E' . $i , "ชื่อ - สกุล" ) ;
		//$objPHPExcel->getActiveSheet()->mergeCells('H'.$i.':H'.($i+1));
		$objPHPExcel->getActiveSheet()->SetCellValue('F' . $i , "ปันผล" ) ;
		$objPHPExcel->getActiveSheet()->SetCellValue('G' . $i , "เฉลี่ยคืน" ) ;
        $objPHPExcel->getActiveSheet()->SetCellValue('G' . $i , "เฉลี่ยคืน" ) ;
		$objPHPExcel->getActiveSheet()->SetCellValue('H' . $i , "รวมปันผล" ) ;
		$objPHPExcel->getActiveSheet()->mergeCells('I'.$i.':I'.($i+1));
		$objPHPExcel->getActiveSheet()->SetCellValue('I' . $i , "เงินของขวัญ" ) ;
		$i+=1;
		$i_bottom = $i;
		$objPHPExcel->getActiveSheet()->SetCellValue('A' . $i , "ที่" ) ; 
		$objPHPExcel->getActiveSheet()->SetCellValue('B' . $i , "สมาชิก" ) ;
		$objPHPExcel->getActiveSheet()->SetCellValue('C' . $i , "กลุ่ม" ) ;
		$objPHPExcel->getActiveSheet()->SetCellValue('F' . $i , $data[0]['divide_percent']."%" ) ;		
		$objPHPExcel->getActiveSheet()->SetCellValue('G' . $i , $data[0]['return_percent']."%" ) ;
		$objPHPExcel->getActiveSheet()->SetCellValue('H' . $i , "เฉลี่ยคืน" ) ;
		
		$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(4.43);
		$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(11.14);
		$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(8.14);
		$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
		$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
		$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(9.86);
		$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(7.71);
		$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(9.86);

	
		
		foreach(range('A','I') as $columnID) {
			$objPHPExcel->getActiveSheet()->getStyle($columnID.$i_top)->applyFromArray($borderTop);
			$objPHPExcel->getActiveSheet()->getStyle($columnID.$i_top)->applyFromArray($borderLeft);
			$objPHPExcel->getActiveSheet()->getStyle($columnID.$i_top)->applyFromArray($borderRight);
			
			$objPHPExcel->getActiveSheet()->getStyle($columnID.$i_bottom)->applyFromArray($borderLeft);
			$objPHPExcel->getActiveSheet()->getStyle($columnID.$i_bottom)->applyFromArray($borderRight);
			$objPHPExcel->getActiveSheet()->getStyle($columnID.$i_bottom)->applyFromArray($borderBottom);
			
			$objPHPExcel->getActiveSheet()->getStyle($columnID.$i_top)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle($columnID.$i_bottom)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		}
		
		$objPHPExcel->getActiveSheet()->getStyle('A'.$i_top.':I'.$i_bottom)->applyFromArray($headerStyle);
		$objPHPExcel->getActiveSheet()->getStyle('A'.$i_top.':I'.$i_bottom)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
		
		
		$dividend_value = 0;
		$average_return_value = 0;
		$j=1;
		
		foreach($data as $key => $row){
			$i+=1;
		
			$names = $data[$key]['prename_full'].$data[$key]['firstname_th']." ".$data[$key]['lastname_th'];
			$objPHPExcel->getActiveSheet()->SetCellValue('A' . $i , $j++);
			$objPHPExcel->getActiveSheet()->SetCellValue('B' . $i , $data[$key]['member_id']." " );
			$objPHPExcel->getActiveSheet()->SetCellValue('C' . $i , $data[$key]['mem_group_id']." " );
			$objPHPExcel->getActiveSheet()->SetCellValue('D' . $i , $data[$key]['mem_group_name'] );
			$objPHPExcel->getActiveSheet()->SetCellValue('E' . $i , $names );
			$objPHPExcel->getActiveSheet()->SetCellValue('F' . $i , number_format($data[$key]['sum_dividend'],2) );
			$objPHPExcel->getActiveSheet()->SetCellValue('G' . $i , number_format($data[$key]['sum_return'],2) );
			$objPHPExcel->getActiveSheet()->SetCellValue('H' . $i , number_format($data[$key]['sum_divide_return'],2) );

			$all_divi +=  $data[$key]['sum_dividend'];
			$all_return += $data[$key]['sum_return'];
			$all_sum +=$data[$key]['sum_divide_return'];
			
		/*	$dividend_value += $row['dividend_value'];
			$average_return_value += $row['average_return_value'];
			
$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':H'.$i)->applyFromArray($textStyleArray);
			$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':H'.$i)->applyFromArray($borderTop);
			
			foreach(range('A','H') as $columnID) {
				if(!in_array($columnID, array('D','E','F'))){
					$objPHPExcel->getActiveSheet()->getStyle($columnID.$i)->applyFromArray($borderLeft);
					$objPHPExcel->getActiveSheet()->getStyle($columnID.$i)->applyFromArray($borderRight);
				}
				$objPHPExcel->getActiveSheet()->getStyle($columnID.$i)->applyFromArray($borderBottom);
			}
			$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':C'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('G'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle('H'.$i.':L'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);*/
		}
		$i+=1;
		$objPHPExcel->getActiveSheet()->SetCellValue('E' . $i , 'รวม' );
		$objPHPExcel->getActiveSheet()->SetCellValue('F' . $i , number_format($all_divi,2) );
		$objPHPExcel->getActiveSheet()->SetCellValue('G' . $i , number_format($all_return,2) );
		$objPHPExcel->getActiveSheet()->SetCellValue('H' . $i , number_format($all_sum,2) );
		/*$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':L'.$i)->applyFromArray($textStyleArray);
		$objPHPExcel->getActiveSheet()->getStyle('H'.$i.':L'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
		*/
	$objPHPExcel->getActiveSheet()->setTitle('sheet',2,2);
	$sheet++;
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="รายการข้อมูลการปันผลและเฉลี่ยคืน.xlsx"');
header('Cache-Control: max-age=0');
		
$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
$objWriter->save('php://output');

exit;	
?>