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
$objPHPExcel->getActiveSheet()->getStyle('A2:G3')->applyFromArray($headerStyle);

foreach(range('A','G') as $columnID) {
	$objPHPExcel->getActiveSheet()->getStyle($columnID.'2')->applyFromArray($borderTop);
	$objPHPExcel->getActiveSheet()->getStyle($columnID.'2')->applyFromArray($borderLeft);
	$objPHPExcel->getActiveSheet()->getStyle($columnID.'2')->applyFromArray($borderRight);
	$objPHPExcel->getActiveSheet()->getStyle($columnID.'3')->applyFromArray($borderLeft);
	$objPHPExcel->getActiveSheet()->getStyle($columnID.'3')->applyFromArray($borderRight);
	$objPHPExcel->getActiveSheet()->getStyle($columnID.'3')->applyFromArray($borderBottom);
	
	$objPHPExcel->getActiveSheet()->getStyle($columnID.'2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$objPHPExcel->getActiveSheet()->getStyle($columnID.'3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
}
	$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(7.43);
	$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(13.57);
	$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(12);
	$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(6.29);
	$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(24.86);
	$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(12.71);
	$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(13.71);

	$i = 2 ;
	$objPHPExcel->getActiveSheet()->SetCellValue('A' . $i , "ลำดับ" ) ; 
	$objPHPExcel->getActiveSheet()->SetCellValue('B' . $i , "เลขทะเบียน" ) ;
	$objPHPExcel->getActiveSheet()->SetCellValue('C' . $i , "รหัส" ) ;
	$objPHPExcel->getActiveSheet()->SetCellValue('D' . $i , "คำนำ" ) ;
	$objPHPExcel->getActiveSheet()->SetCellValue('E' . $i , "ชื่อ - สกุล" ) ;
	$objPHPExcel->getActiveSheet()->SetCellValue('F' . $i , "หน่วย" ) ;
	$objPHPExcel->getActiveSheet()->SetCellValue('G' . $i , "ส่งเงินค่าหุ้น" ) ;
	
	$i = 3 ;
	$objPHPExcel->getActiveSheet()->SetCellValue('A' . $i , "ที่" ) ; 
	$objPHPExcel->getActiveSheet()->SetCellValue('B' . $i , "สมาชิก" ) ;
	$objPHPExcel->getActiveSheet()->SetCellValue('C' . $i , "พนักงาน" ) ;
	$objPHPExcel->getActiveSheet()->SetCellValue('D' . $i , "หน้า" ) ;
	$objPHPExcel->getActiveSheet()->SetCellValue('E' . $i , "" ) ;
	$objPHPExcel->getActiveSheet()->SetCellValue('F' . $i , "งาน" ) ;
	$objPHPExcel->getActiveSheet()->SetCellValue('G' . $i , "รายเดือน" ) ;
	
	
	$j=1;
	$sum_share = 0;
	foreach($rs as $key => $row){
		$i++;
		$mem_group = $row['level'];
		
		$this->db->select(array('change_value'));
		$this->db->from('coop_change_share');
		$this->db->where("member_id = '".$row['member_id']."' AND change_share_status IN ('1','2')");
		$this->db->order_by('change_share_id DESC');
		$this->db->limit(1);
		$row_change_share = $this->db->get()->result_array();
		$row_change_share = @$row_change_share[0];
		
		if($row_change_share['change_value'] != ''){
			$num_share = $row_change_share['change_value'];
		}else{
			$this->db->select(array('share_salary'));
			$this->db->from('coop_share_rule');
			$this->db->where("salary_rule <= '".$row['salary']."'");
			$this->db->order_by('salary_rule DESC');
			$this->db->limit(1);
			$row_share_rule = $this->db->get()->result_array();
		
			$num_share = $row_share_rule[0]['share_salary'];
		}
		$objPHPExcel->getActiveSheet()->SetCellValue('A' . $i , $j++ ) ; 
		$objPHPExcel->getActiveSheet()->SetCellValue('B' . $i , $row['member_id']." " ) ;
		$objPHPExcel->getActiveSheet()->SetCellValue('C' . $i , $row['employee_id']." " ) ;
		$objPHPExcel->getActiveSheet()->SetCellValue('D' . $i , $row['prename_short'] ) ;
		$objPHPExcel->getActiveSheet()->SetCellValue('E' . $i , $row['firstname_th']." ".$row['lastname_th'] ) ;
		$objPHPExcel->getActiveSheet()->SetCellValue('F' . $i , @$mem_group_arr[$mem_group] ) ;
		$objPHPExcel->getActiveSheet()->SetCellValue('G' . $i , number_format($num_share*$share_value,2) ) ;
		
		$sum_share += $num_share*$share_value;
		$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':G'.$i)->applyFromArray($styleArray);
		$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':D'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('F'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('G'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
	}
		$i+=2;
		$objPHPExcel->getActiveSheet()->SetCellValue('F' . $i , 'ยอดรวม' ) ;
		$objPHPExcel->getActiveSheet()->SetCellValue('G' . $i , number_format($sum_share,2) ) ;
		$objPHPExcel->getActiveSheet()->getStyle('G'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
		$summaryStyle = array(
			'font'  => array(
				'bold'  => true,
				'color' => array('rgb' => 'FF0000'),
				'size'  => 14,
				'name'  => 'Cordia New'
			),
			'borders' => array(
				'bottom' => array(
					'style' => PHPExcel_Style_Border::BORDER_MEDIUM
				)
			)
		);
		$objPHPExcel->getActiveSheet()->getStyle('G' . $i )->applyFromArray($summaryStyle);

		$objPHPExcel->getActiveSheet()->setTitle('ค่าหุ้น');
		$objPHPExcel->getActiveSheet()->mergeCells('A1:G1');
		$objPHPExcel->getActiveSheet()->SetCellValue('A1', "รายงานสรุป ค่าหุ้นของสมาชิก".$_SESSION['COOP_NAME']." ส่งหัก ระหว่างเดือน ".$month_arr[(int)$_GET['month']]." ".$_GET['year']." จำนวน ".($j-1)." ท่าน" ) ; 
		
		$titleStyle = array(
		'font'  => array(
			'bold'  => true,
			'size'  => 14,
			'name'  => 'Cordia New'
		));
		$objPHPExcel->getActiveSheet()->getStyle('A1')->applyFromArray($titleStyle);
		$objPHPExcel->getActiveSheet()->getStyle('A1:G1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	
				//exit;
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="รายงานค่าหุ้นเดือน_'.$month_arr[(int)$_GET['month']].'_'.$_GET['year'].'.xlsx"');
		header('Cache-Control: max-age=0');
				
		$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
		$objWriter->save('php://output');
exit;
?>