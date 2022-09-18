<?php
$month_arr = array('01'=>'มกราคม','02'=>'กุมภาพันธ์','03'=>'มีนาคม','04'=>'เมษายน','05'=>'พฤษภาคม','06'=>'มิถุนายน','07'=>'กรกฎาคม','08'=>'สิงหาคม','09'=>'กันยายน','10'=>'ตุลาคม','11'=>'พฤศจิกายน','12'=>'ธันวาคม');
$month_short_arr = array('01'=>'ม.ค.','02'=>'ก.พ.','03'=>'มี.ค.','04'=>'เม.ย.','05'=>'พ.ค.','06'=>'มิ.ย.','07'=>'ก.ค.','08'=>'ส.ค.','09'=>'ก.ย.','10'=>'ต.ค.','11'=>'พ.ย.','12'=>'ธ.ค.');

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
foreach($data as $keys =>$datas) {
	
		$i=0;
		$objPHPExcel->createSheet($sheet);
		$objPHPExcel->setActiveSheetIndex($sheet);
		$objPHPExcel->getActiveSheet()->setTitle($keys);
		
		$i+=1;
		$objPHPExcel->getActiveSheet()->mergeCells('A'.$i.':I'.$i);
		$objPHPExcel->getActiveSheet()->SetCellValue('A'.$i, "รายงานรับจ่ายเงินฝาก".' '.$datas['row_head']['type_name'] ) ;
		$objPHPExcel->getActiveSheet()->getStyle('A'.$i)->applyFromArray($titleStyle);
	
		$i+=1;
		$i_title = $i;
		$objPHPExcel->getActiveSheet()->mergeCells('A'.$i.':I'.$i);
		$objPHPExcel->getActiveSheet()->getStyle('A'.$i)->applyFromArray($titleStyle);
		// $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':I'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        if($m != '12'){
            $next_month = sprintf("%02d",$m+1);
            $next_year = $year;
        }else{
            $next_month = '01';
            $next_year = $year+1;
        }
		$objPHPExcel->getActiveSheet()->SetCellValue('A'.$i,$this->center_function->ConvertToThaiDate(@$datas['row_head']['transaction_time'],0,0) ) ;

		$i+=1;
		$i_top = $i;
		$objPHPExcel->getActiveSheet()->SetCellValue('A' . $i , "ลำดับ" ) ; 
		$objPHPExcel->getActiveSheet()->SetCellValue('B' . $i , "เลขที่" ) ; 
		$objPHPExcel->getActiveSheet()->mergeCells('C'.$i.':E'.($i+1));
		$objPHPExcel->getActiveSheet()->SetCellValue('C' . $i , "ชื่อบัญชี" ) ; 
		$objPHPExcel->getActiveSheet()->mergeCells('F'.$i.':F'.($i+1));
		$objPHPExcel->getActiveSheet()->SetCellValue('F' . $i , "ลำดับ" ) ;
		$objPHPExcel->getActiveSheet()->mergeCells('G'.$i.':G'.($i+1));
		$objPHPExcel->getActiveSheet()->SetCellValue('G' . $i , "วันที่" ) ; 
		$objPHPExcel->getActiveSheet()->mergeCells('H'.$i.':H'.($i+1));
		$objPHPExcel->getActiveSheet()->SetCellValue('H' . $i , "คำย่อ" ) ;
		$objPHPExcel->getActiveSheet()->SetCellValue('I' . $i , "ถอน" ) ;
		$objPHPExcel->getActiveSheet()->SetCellValue('J' . $i , "ฝาก" ) ;
		$objPHPExcel->getActiveSheet()->SetCellValue('K' . $i , "ดอกเบี้ย" ) ;
		$objPHPExcel->getActiveSheet()->SetCellValue('L' . $i , "เงินฝากคงเหลือ" ) ;

		
		$i+=1;
		$i_bottom = $i;
		$objPHPExcel->getActiveSheet()->SetCellValue('A' . $i , "ที่" ) ; 
		$objPHPExcel->getActiveSheet()->SetCellValue('B' . $i , "บัญชี" ) ;
		$objPHPExcel->getActiveSheet()->SetCellValue('C' . $i , "พนักงาน" ) ;
		$objPHPExcel->getActiveSheet()->SetCellValue('I' . $i , "(บาท)" ) ;
		$objPHPExcel->getActiveSheet()->SetCellValue('J' . $i , "(บาท)" ) ;
		$objPHPExcel->getActiveSheet()->SetCellValue('K' . $i , "(บาท)" ) ;
		$objPHPExcel->getActiveSheet()->SetCellValue('L' . $i , "(บาท)" ) ;
		
		$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(3.86);
		$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(13.86);
		$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(6.86);
		$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(13.00);
		$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(13.00);
		$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(8.29);
		$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(10.71);
		$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(7);
        $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(13);
		$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(13);
		$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(13);
		$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(13.71);
		
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
		$sum_transaction_withdrawal = 0;
		$sum_transaction_deposit = 0;
		$sum_interest = 0;
		
		if(!empty($datas['row_detail'])) {
			

			foreach($datas['row_detail'] as $key => $row){
				@$sum_transaction_withdrawal+=@$row['transaction_withdrawal'];
				@$sum_transaction_deposit+=@$row['transaction_deposit'];	
				@$sum_interest+=@$row['interest'];
				@$rest = substr(@$row['transaction_no'],-5);
				
				
				$i+=1;
				$objPHPExcel->getActiveSheet()->SetCellValue('A' . $i , $j++);
				$objPHPExcel->getActiveSheet()->SetCellValue('B' . $i , @$this->center_function->format_account_number(@$row['account_id']) );
				$objPHPExcel->getActiveSheet()->mergeCells('C'.$i.':E'.($i));
				$objPHPExcel->getActiveSheet()->SetCellValue('C' . $i , @$row['account_name']);
				$objPHPExcel->getActiveSheet()->SetCellValue('F' . $i , @$rest);
				$objPHPExcel->getActiveSheet()->SetCellValue('G' . $i , @date('d/m/y', strtotime("+543 year", strtotime($row['transaction_time']))) );
				$objPHPExcel->getActiveSheet()->SetCellValue('H' . $i , @$row['transaction_list'] );
				$objPHPExcel->getActiveSheet()->SetCellValue('I' . $i , @number_format(@$row['transaction_withdrawal'],2)==0 ? "": number_format(@$row['transaction_withdrawal'],2) );
				$objPHPExcel->getActiveSheet()->SetCellValue('J' . $i , @number_format(@$row['transaction_deposit'],2)==0 ? "": number_format(@$row['transaction_deposit'],2) );
				$objPHPExcel->getActiveSheet()->SetCellValue('K' . $i ,  @number_format(@$row['interest'],2)==0 ? "": number_format(@$row['interest'],2) );
				$objPHPExcel->getActiveSheet()->SetCellValue('L' . $i , number_format(@$row['transaction_balance'],2) );		

				$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':H'.$i)->applyFromArray($textStyleArray);
				$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':H'.$i)->applyFromArray($borderTop);
				
				foreach(range('A','L') as $columnID) {
					if(!in_array($columnID, array('C','D','E'))){
						$objPHPExcel->getActiveSheet()->getStyle($columnID.$i)->applyFromArray($borderLeft);
						$objPHPExcel->getActiveSheet()->getStyle($columnID.$i)->applyFromArray($borderRight);
					}
					$objPHPExcel->getActiveSheet()->getStyle($columnID.$i)->applyFromArray($borderBottom);
				}

				$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':B'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$objPHPExcel->getActiveSheet()->getStyle('C'.$i.':E'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
				$objPHPExcel->getActiveSheet()->getStyle('F'.$i.':H'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$objPHPExcel->getActiveSheet()->getStyle('I'.$i.':L'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
				$count_register++;
			}
			$i+=1;
			$objPHPExcel->getActiveSheet()->SetCellValue('B' . $i , count($datas['row_detail']));
			$objPHPExcel->getActiveSheet()->getStyle('B'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->SetCellValue('C' . $i , "เงินรวม");
			$objPHPExcel->getActiveSheet()->mergeCells('D'.$i.':F'.$i);
			$objPHPExcel->getActiveSheet()->SetCellValue('D' . $i , @$this->center_function->mydate2date(@$row['transaction_time']));
			$objPHPExcel->getActiveSheet()->SetCellValue('I' . $i , @number_format(@$sum_transaction_withdrawal,2));
			$objPHPExcel->getActiveSheet()->SetCellValue('K' . $i , @number_format(@$sum_interest,2));
			$objPHPExcel->getActiveSheet()->getStyle('F'.$i.':L'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$i+=1;
			$objPHPExcel->getActiveSheet()->SetCellValue('J' . $i ,  @number_format(@$sum_transaction_deposit,2));
			$objPHPExcel->getActiveSheet()->getStyle('B'.$i.':L'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$i+=1;
			$objPHPExcel->getActiveSheet()->SetCellValue('B' . $i , count($datas['row_detail']));
			$objPHPExcel->getActiveSheet()->getStyle('B'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->SetCellValue('C' . $i , "เงินรวม");
			$objPHPExcel->getActiveSheet()->mergeCells('D'.$i.':F'.$i);
			$objPHPExcel->getActiveSheet()->SetCellValue('D' . $i , @$datas['row_head']['type_name']);
			$objPHPExcel->getActiveSheet()->SetCellValue('I' . $i , @number_format(@$sum_transaction_withdrawal,2));
			$objPHPExcel->getActiveSheet()->SetCellValue('K' . $i , @number_format(@$sum_interest,2));
			$objPHPExcel->getActiveSheet()->getStyle('F'.$i.':L'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$i+=1;
			$objPHPExcel->getActiveSheet()->SetCellValue('J' . $i ,  @number_format(@$sum_transaction_deposit,2));
			$objPHPExcel->getActiveSheet()->getStyle('B'.$i.':L'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$sheet++;
	}
}

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="รายงานรับจ่ายเงินฝาก_'.$file_name_text.'.xlsx"');
header('Cache-Control: max-age=0');
		
$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
$objWriter->save('php://output');
exit;	
?>