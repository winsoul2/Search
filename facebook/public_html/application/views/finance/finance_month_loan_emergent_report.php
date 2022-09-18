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
		'size'  => 14,
		'name'  => 'Cordia New'
	)
);
$textStyle = array(
  'font'  => array(
		'bold'  => false,
		'size'  => 14,
		'name'  => 'Cordia New'
	)
);
$textStyleBold = array(
  'font'  => array(
		'bold'  => true,
		'size'  => 14,
		'name'  => 'Cordia New'
	)
);
$textStyleRed = array(
  'font'  => array(
		'bold'  => false,
		'size'  => 14,
		'color' => array('rgb' => 'FF0000'),
		'name'  => 'Cordia New'
	)
);
$textStyleGreen = array(
  'font'  => array(
		'bold'  => false,
		'size'  => 14,
		'color' => array('rgb' => '339966'),
		'name'  => 'Cordia New'
	)
);
$textStyleResult = array(
  'font'  => array(
		'bold'  => false,
		'size'  => 14,
		'color' => array('rgb' => '3366FF'),
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
            'color' => array('rgb' => 'FFFF99')
));
$objPHPExcel->getActiveSheet()->getStyle('A2:M3')->applyFromArray($headerStyle);
	$i = 2 ;
	$objPHPExcel->getActiveSheet()->SetCellValue('A' . $i , "ลำดับ" ) ; 
	$objPHPExcel->getActiveSheet()->SetCellValue('A' . ($i+1) , "ที่" ) ;
	$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(6.57);
	$objPHPExcel->getActiveSheet()->SetCellValue('B' . $i , "เลขทะเบียน" ) ;
	$objPHPExcel->getActiveSheet()->SetCellValue('B' . ($i+1) , "สมาชิก" ) ;
	$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(10.86);
	$objPHPExcel->getActiveSheet()->SetCellValue('C' . $i , "รหัส" ) ;
	$objPHPExcel->getActiveSheet()->SetCellValue('C' . ($i+1) , "พนักงาน" ) ;
	$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(10.43);
	$objPHPExcel->getActiveSheet()->SetCellValue('D' . $i , "ชื่อ - สกุล" ) ;
	$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(25.71);
	$objPHPExcel->getActiveSheet()->SetCellValue('E' . $i , "หน่วย" ) ;
	$objPHPExcel->getActiveSheet()->SetCellValue('E' . ($i+1) , "งาน" ) ;
	$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(8.29);
	$objPHPExcel->getActiveSheet()->SetCellValue('F' . $i , "หนังสือเงินกู้" ) ;
	$objPHPExcel->getActiveSheet()->SetCellValue('F' . ($i+1) , "เลขที่" ) ;
	$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(12.14);
	$objPHPExcel->getActiveSheet()->SetCellValue('G' . $i , "จำนวนเงินต้น" ) ;
	$objPHPExcel->getActiveSheet()->SetCellValue('G' . ($i+1) , "ที่กู้" ) ;
	$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(12.71);
	$objPHPExcel->getActiveSheet()->SetCellValue('H' . $i , "จำนวนเงินต้น" ) ;
	$objPHPExcel->getActiveSheet()->SetCellValue('H' . ($i+1) , "ที่ส่งคืน" ) ;
	$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(13.71);
	$objPHPExcel->getActiveSheet()->SetCellValue('I' . $i , "จำนวนวัน" ) ;
	$objPHPExcel->getActiveSheet()->SetCellValue('I' . ($i+1) , "ที่คิดดอกเบี้ย" ) ;
	$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(13);
	$objPHPExcel->getActiveSheet()->SetCellValue('J' . $i , "อัตราดอกเบี้ย" ) ;
	$objPHPExcel->getActiveSheet()->SetCellValue('J' . ($i+1) , $interest_rate." % ต่อปี" ) ;
	$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(11.14);
	$objPHPExcel->getActiveSheet()->SetCellValue('K' . $i , "ค่าปรับ" ) ;
	$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(11.14);
	$objPHPExcel->getActiveSheet()->SetCellValue('L' . $i , "รวมเงินต้น" ) ;
	$objPHPExcel->getActiveSheet()->SetCellValue('L' . ($i+1) , "กับดอกเบี้ย" ) ;
	$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(12.29);
	$objPHPExcel->getActiveSheet()->SetCellValue('M' . $i , "จำนวนเงินต้น" ) ;
	$objPHPExcel->getActiveSheet()->SetCellValue('M' . ($i+1) , "คงเหลือ" ) ;
	$objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(13.71);
	
	
foreach(range('A','M') as $columnID) {
	$objPHPExcel->getActiveSheet()->getStyle($columnID.'2')->applyFromArray($borderTop);
	$objPHPExcel->getActiveSheet()->getStyle($columnID.'2')->applyFromArray($borderLeft);
	$objPHPExcel->getActiveSheet()->getStyle($columnID.'2')->applyFromArray($borderRight);
	$objPHPExcel->getActiveSheet()->getStyle($columnID.'3')->applyFromArray($borderLeft);
	$objPHPExcel->getActiveSheet()->getStyle($columnID.'3')->applyFromArray($borderRight);
	$objPHPExcel->getActiveSheet()->getStyle($columnID.'3')->applyFromArray($borderBottom);
	
    $objPHPExcel->getActiveSheet()->getStyle($columnID."2")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$objPHPExcel->getActiveSheet()->getStyle($columnID."3")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
}
	
	$j=1;
	$principal_payment = 0;
	$interest = 0;
	$balance = 0;
	$sum_diff = array();
	$i=3;
	foreach($rs as $key => $row){ 
		$mem_group = $row['level'];
		
		$this->db->select(array('principal_payment'));
		$this->db->from('coop_loan_period');
		$this->db->where("loan_id = '".$row['loan_id']."'");
		$this->db->limit(1);
		$row_principal_payment = $this->db->get()->result_array();
		$row_principal_payment = $row_principal_payment[0];
		
		$date_interesting = date('Y-m-t',strtotime(($_GET['year']-543)."-".sprintf("%02d",$_GET['month']).'-01'));
		
		$this->db->select(array('payment_date'));
		$this->db->from('coop_finance_transaction');
		$this->db->where("loan_id = '".$row['loan_id']."'");
		$this->db->order_by('payment_date DESC');
		$this->db->limit(1);
		$row_date_prev_paid = $this->db->get()->result_array();
		$row_date_prev_paid = $row_date_prev_paid[0];
		
		$date_prev_paid = $row_date_prev_paid['payment_date']!=''?$row_date_prev_paid['payment_date']:$row['date_transfer'];
		$diff = date_diff(date_create($date_prev_paid),date_create($date_interesting));
		$date_count = $diff->format("%a");
		$date_count = $date_count+1;
		$interest = ((($row['loan_amount_balance']*$row['interest_per_year'])/100)/365)*$date_count;
		
		if($row_principal_payment['principal_payment'] > $row['loan_amount_balance']){
			$principal_payment_now = $row['loan_amount_balance'];
		}else{
			$principal_payment_now = $row_principal_payment['principal_payment'];
		}
		
		$row['interest'] = $interest;
		$i++ ;
		$objPHPExcel->getActiveSheet()->SetCellValue('A' . $i , $j++ ) ; 
		$objPHPExcel->getActiveSheet()->SetCellValue('B' . $i , $row['member_id']." " ) ;
		$objPHPExcel->getActiveSheet()->SetCellValue('C' . $i , $row['employee_id']." " ) ;
		$objPHPExcel->getActiveSheet()->SetCellValue('D' . $i , $row['prename_short'].$row['firstname_th']." ".$row['lastname_th'] ) ;
		$objPHPExcel->getActiveSheet()->SetCellValue('E' . $i , $mem_group_arr[$mem_group] ) ;
		$objPHPExcel->getActiveSheet()->SetCellValue('F' . $i , $row['contract_number'] ) ;
		$objPHPExcel->getActiveSheet()->SetCellValue('G' . $i , number_format($row['loan_amount_balance'],2) ) ;
		$objPHPExcel->getActiveSheet()->SetCellValue('H' . $i , number_format($principal_payment_now,2) ) ;
		$objPHPExcel->getActiveSheet()->SetCellValue('I' . $i , $row['date_count'] ) ;
		$objPHPExcel->getActiveSheet()->SetCellValue('J' . $i , number_format($row['interest'],2) ) ;
		$objPHPExcel->getActiveSheet()->SetCellValue('K' . $i , '' ) ;
		$objPHPExcel->getActiveSheet()->SetCellValue('L' . $i , number_format($principal_payment_now+$row['interest'],2) ) ;
		$objPHPExcel->getActiveSheet()->SetCellValue('M' . $i , number_format($row['loan_amount_balance']-$principal_payment_now,2) ) ;
		
		$principal_payment += $principal_payment_now;
		$interest += $row['interest'];
		$balance += $row['loan_amount_balance']-$principal_payment_now;
		$sum_diff[$row['loan_type']]['principal_payment'] += $principal_payment_now;
		$sum_diff[$row['loan_type']]['interest'] += $row['interest'];
		
		$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':C'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('E'.$i.':F'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('G'.$i.':H'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
		$objPHPExcel->getActiveSheet()->getStyle('I'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('J'.$i.':M'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
		$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':M'.$i)->applyFromArray($styleArray);
	}
	$i+=2 ; 
	$objPHPExcel->getActiveSheet()->SetCellValue('H' . $i , number_format($principal_payment,2) ) ;
	$objPHPExcel->getActiveSheet()->SetCellValue('J' . $i , number_format($interest,2) ) ;
	$objPHPExcel->getActiveSheet()->SetCellValue('L' . $i , number_format($principal_payment+$interest,2) ) ;
	$objPHPExcel->getActiveSheet()->SetCellValue('M' . $i , number_format($balance,2) ) ;
	$objPHPExcel->getActiveSheet()->getStyle('H'.$i)->applyFromArray($borderBottom);
	$objPHPExcel->getActiveSheet()->getStyle('J'.$i)->applyFromArray($borderBottom);
	$objPHPExcel->getActiveSheet()->getStyle('L'.$i)->applyFromArray($borderBottomDouble);
	$objPHPExcel->getActiveSheet()->getStyle('H'.$i.':K'.$i)->applyFromArray($textStyleRed);
	$objPHPExcel->getActiveSheet()->getStyle('L'.$i)->applyFromArray($textStyleResult);
	$objPHPExcel->getActiveSheet()->getStyle('H'.$i)->applyFromArray($textStyle);
	$objPHPExcel->getActiveSheet()->getStyle('H'.$i.':M'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
	
	$i+=2 ; 
	$objPHPExcel->getActiveSheet()->SetCellValue('F' . $i , 'ฉุกเฉิน' ) ;
	$objPHPExcel->getActiveSheet()->SetCellValue('G' . $i , number_format(@$sum_diff['3']['principal_payment'],2) ) ;
	$objPHPExcel->getActiveSheet()->getStyle('F'.$i)->applyFromArray($textStyle);
	$objPHPExcel->getActiveSheet()->getStyle('G'.$i)->applyFromArray($textStyle);
	$objPHPExcel->getActiveSheet()->getStyle('F'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$objPHPExcel->getActiveSheet()->getStyle('G'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
	$i++; 
	$objPHPExcel->getActiveSheet()->SetCellValue('F' . $i , 'ด/บ' ) ;
	$objPHPExcel->getActiveSheet()->SetCellValue('G' . $i , number_format(@$sum_diff['3']['interest'],2) ) ;
	$objPHPExcel->getActiveSheet()->getStyle('F'.$i)->applyFromArray($textStyle);
	$objPHPExcel->getActiveSheet()->getStyle('G'.$i)->applyFromArray($textStyle);
	$objPHPExcel->getActiveSheet()->getStyle('F'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$objPHPExcel->getActiveSheet()->getStyle('G'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
	
	$i+=2 ; 
	$objPHPExcel->getActiveSheet()->SetCellValue('F' . $i , 'ฉ.พ.' ) ;
	$objPHPExcel->getActiveSheet()->SetCellValue('G' . $i , number_format(@$sum_diff['4']['principal_payment'],2) ) ;
	$objPHPExcel->getActiveSheet()->getStyle('F'.$i)->applyFromArray($textStyleGreen);
	$objPHPExcel->getActiveSheet()->getStyle('G'.$i)->applyFromArray($textStyle);
	$objPHPExcel->getActiveSheet()->getStyle('F'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$objPHPExcel->getActiveSheet()->getStyle('G'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
	$i++; 
	$objPHPExcel->getActiveSheet()->SetCellValue('F' . $i , 'ด/บ' ) ;
	$objPHPExcel->getActiveSheet()->SetCellValue('G' . $i , number_format(@$sum_diff['4']['interest'],2) ) ;
	$objPHPExcel->getActiveSheet()->getStyle('F'.$i)->applyFromArray($textStyleGreen);
	$objPHPExcel->getActiveSheet()->getStyle('G'.$i)->applyFromArray($textStyle);
	$objPHPExcel->getActiveSheet()->getStyle('F'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$objPHPExcel->getActiveSheet()->getStyle('G'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
	$i++; 
	$objPHPExcel->getActiveSheet()->SetCellValue('F' . $i , 'ปรับ' ) ;
	$objPHPExcel->getActiveSheet()->SetCellValue('G' . $i , '0' ) ;
	$objPHPExcel->getActiveSheet()->getStyle('F'.$i)->applyFromArray($textStyleGreen);
	$objPHPExcel->getActiveSheet()->getStyle('G'.$i)->applyFromArray($textStyle);
	$objPHPExcel->getActiveSheet()->getStyle('F'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$objPHPExcel->getActiveSheet()->getStyle('G'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
	$i++; 
	$objPHPExcel->getActiveSheet()->SetCellValue('G' . $i , number_format(@$sum_diff['3']['principal_payment']+@$sum_diff['3']['interest']+@$sum_diff['4']['principal_payment']+@$sum_diff['4']['interest'],2) ) ;
	$objPHPExcel->getActiveSheet()->getStyle('G'.$i)->applyFromArray($borderBottom);
	$objPHPExcel->getActiveSheet()->getStyle('G'.$i)->applyFromArray($textStyle);
	$objPHPExcel->getActiveSheet()->getStyle('G'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
	
	$objPHPExcel->getActiveSheet()->setTitle('เงินกู้ฉุกเฉิน');
	$objPHPExcel->getActiveSheet()->mergeCells('A1:M1');
	$objPHPExcel->getActiveSheet()->SetCellValue('A1', "รายงานการหักเงินกู้ฉุกเฉินและดอกเบี้ยประจำงวดค่าจ้างเงินเดือน ".$month_arr[(int)$_GET['month']]." ".$_GET['year']." จำนวน ".($j-1)." ท่าน" ) ; 
	$titleStyle = array(
		'font'  => array(
			'bold'  => true,
			'size'  => 14,
			'name'  => 'Cordia New'
		));
	$objPHPExcel->getActiveSheet()->getStyle('A1')->applyFromArray($titleStyle);
	$objPHPExcel->getActiveSheet()->getStyle('A1:M1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	header('Content-Disposition: attachment;filename="รายงานเงินกู้ฉุกเฉินเดือน_'.$month_arr[(int)$_GET['month']].'_'.$_GET['year'].'.xlsx"');
	header('Cache-Control: max-age=0');
			
	$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
	$objWriter->save('php://output');
exit;
?>