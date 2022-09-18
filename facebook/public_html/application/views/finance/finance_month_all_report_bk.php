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
		'size'  => 13,
		'name'  => 'Cordia New'
	)
);
$textStyle = array(
  'font'  => array(
		'bold'  => false,
		'size'  => 12,
		'name'  => 'Cordia New'
	)
);
$textStyleRed = array(
  'font'  => array(
		'bold'  => false,
		'size'  => 12,
		'color' => array('rgb' => 'FF0000'),
		'name'  => 'Cordia New'
	)
);
$textStylePink = array(
  'font'  => array(
		'bold'  => false,
		'size'  => 12,
		'color' => array('rgb' => 'FF00FF'),
		'name'  => 'Cordia New'
	)
);
$textStyleBlue = array(
  'font'  => array(
		'bold'  => false,
		'size'  => 12,
		'color' => array('rgb' => '0000FF'),
		'name'  => 'Cordia New'
	)
);
$textStyleGreen = array(
  'font'  => array(
		'bold'  => false,
		'size'  => 12,
		'color' => array('rgb' => '339966'),
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
$BGPink = array(
	'fill' => array(
            'type' => PHPExcel_Style_Fill::FILL_SOLID,
            'color' => array('rgb' => 'FF00FF')
));
$BGYellow = array(
	'fill' => array(
            'type' => PHPExcel_Style_Fill::FILL_SOLID,
            'color' => array('rgb' => 'FFFF00')
));
$objPHPExcel->getActiveSheet()->getStyle('A2:R3')->applyFromArray($headerStyle);

	$i = 2 ;
	$objPHPExcel->getActiveSheet()->SetCellValue('A' . $i , "ลำดับ" ) ; 
	$objPHPExcel->getActiveSheet()->SetCellValue('B' . $i, "เลขทะเบียน" ) ;
	$objPHPExcel->getActiveSheet()->SetCellValue('C' . $i, "รหัส" ) ; 
	$objPHPExcel->getActiveSheet()->SetCellValue('D' . $i, "ชื่อ - สกุล" ) ; 
	$objPHPExcel->getActiveSheet()->SetCellValue('E' . $i, "หน่วย" ) ; 
	$objPHPExcel->getActiveSheet()->SetCellValue('F' . $i, "ส่งเงิน" ) ;
	$objPHPExcel->getActiveSheet()->SetCellValue('G' . $i, "รวมค่าหุ้น" ) ; 
	$objPHPExcel->getActiveSheet()->mergeCells('H2:L2');
	$objPHPExcel->getActiveSheet()->SetCellValue('H' . $i, "เงินกู้สามัญ" ) ;
	$objPHPExcel->getActiveSheet()->mergeCells('M2:Q2');
	$objPHPExcel->getActiveSheet()->SetCellValue('M' . $i, "เงินกู้ฉุกเฉิน" ) ; 
	$objPHPExcel->getActiveSheet()->SetCellValue('R' . $i, "หมายเหตุ" ) ; 
	
	$i = 3 ;
	$objPHPExcel->getActiveSheet()->SetCellValue('A' . $i , "ที่" ) ; 
	$objPHPExcel->getActiveSheet()->SetCellValue('B' . $i, "สมาชิก" ) ;
	$objPHPExcel->getActiveSheet()->SetCellValue('C' . $i, "พนักงาน" ) ;  
	$objPHPExcel->getActiveSheet()->SetCellValue('E' . $i, "งาน" ) ; 
	$objPHPExcel->getActiveSheet()->SetCellValue('F' . $i, "สค่าหุ้น" ) ;
	$objPHPExcel->getActiveSheet()->SetCellValue('G' . $i, "สะสม" ) ; 
	$objPHPExcel->getActiveSheet()->SetCellValue('H' . $i , "สัญญาเลขที่" ) ; 
	$objPHPExcel->getActiveSheet()->SetCellValue('I' . $i , "คงเหลือ" ) ;
	$objPHPExcel->getActiveSheet()->SetCellValue('J' . $i , "เงินต้น" ) ;
	$objPHPExcel->getActiveSheet()->SetCellValue('K' . $i , "ดอกเบี้ย" ) ;
	$objPHPExcel->getActiveSheet()->SetCellValue('L' . $i , "คงเหลือ" ) ;
	$objPHPExcel->getActiveSheet()->SetCellValue('M' . $i , "สัญญาเลขที่" ) ;
	$objPHPExcel->getActiveSheet()->SetCellValue('N' . $i , "คงเหลือ" ) ;
	$objPHPExcel->getActiveSheet()->SetCellValue('O' . $i , "เงินต้น" ) ;
	$objPHPExcel->getActiveSheet()->SetCellValue('P' . $i , "ดอกเบี้ย" ) ;
	$objPHPExcel->getActiveSheet()->SetCellValue('Q' . $i , "คงเหลือ" ) ;
	
	$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(4.86);
	$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(8.43);
	$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(8.43);
	$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(22.14);
	$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(4.57);
	$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(10.43);
	$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(14.43);
	$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(10.57);
	$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(11.29);
	$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(11.57);
	$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(10.43);
	$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(11.29);
	$objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(9.43);
	$objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(9.57);
	$objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(9);
	$objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth(7.71);
	$objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setWidth(8.86);
	$objPHPExcel->getActiveSheet()->getColumnDimension('R')->setWidth(10);
	
foreach(range('A','R') as $columnID) {
	$objPHPExcel->getActiveSheet()->getStyle($columnID.'2')->applyFromArray($borderTop);
	$objPHPExcel->getActiveSheet()->getStyle($columnID.'2')->applyFromArray($borderLeft);
	$objPHPExcel->getActiveSheet()->getStyle($columnID.'2')->applyFromArray($borderRight);
	$objPHPExcel->getActiveSheet()->getStyle($columnID.'3')->applyFromArray($borderLeft);
	$objPHPExcel->getActiveSheet()->getStyle($columnID.'3')->applyFromArray($borderRight);
	$objPHPExcel->getActiveSheet()->getStyle($columnID.'3')->applyFromArray($borderBottom);
	
    $objPHPExcel->getActiveSheet()->getStyle($columnID."2")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$objPHPExcel->getActiveSheet()->getStyle($columnID."3")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
}
$objPHPExcel->getActiveSheet()->getStyle('H2:L2')->applyFromArray($borderBottom);
$objPHPExcel->getActiveSheet()->getStyle('M2:Q2')->applyFromArray($borderBottom);
	
	$k=1;
	$loan_arr = array();
	foreach($rs as $key => $row){ 
	
	$mem_group = $row['level'];
	
	$this->db->select(array('change_value'));
	$this->db->from('coop_change_share');
	$this->db->where("member_id = '".$row['member_id']."' AND change_share_status IN ('1','2')");
	$this->db->order_by('active_date DESC');
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
	
	$this->db->select(array('share_collect_value'));
	$this->db->from('coop_mem_share');
	$this->db->where("member_id = '".$row['member_id']."'");
	$this->db->order_by('share_id DESC');
	$this->db->limit(1);
	$row_share = $this->db->get()->result_array();
	$row_share = @$row_share[0];
	
	$this->db->select(
		array(
			't2.id as loan_id',
			't2.contract_number',
			't2.loan_type',
			't2.loan_amount_balance',
			't2.interest_per_year',
			't5.date_transfer'
		)
	);
	$this->db->from('coop_loan as t2');
	$this->db->join('coop_mem_apply as t3','t2.member_id = t3.member_id','inner');
	$this->db->join('coop_prename as t4','t3.prename_id = t4.prename_id','left');
	$this->db->join('coop_loan_transfer as t5','t2.id = t5.loan_id','inner');
	$this->db->where("
		t2.loan_type IN('1','2','5')
		AND t2.member_id = '".$row['member_id']."'
		AND t2.loan_status = '1'
		AND t2.loan_amount_balance > 0
		AND t2.date_start_period <= '".($_GET['year']-543)."-".sprintf("%02d",$_GET['month'])."-".date('t',strtotime(($_GET['year']-543)."-".sprintf("%02d",$_GET['month'])."-01"))."'
	");
	$rs_normal_loan = $this->db->get()->result_array();
	$normal_loan = array();
	$b=0;
	foreach($rs_normal_loan as $key => $row_normal_loan){
		$normal_loan[$b] = $row_normal_loan;
		
		$this->db->select(array('principal_payment'));
		$this->db->from('coop_loan_period');
		$this->db->where("loan_id = '".$row_normal_loan['loan_id']."'");
		$this->db->limit(1);
		$row_principal_payment = $this->db->get()->result_array();
		$row_principal_payment = $row_principal_payment[0];
		
		$date_interesting = date('Y-m-t',strtotime(($_GET['year']-543)."-".sprintf("%02d",$_GET['month']).'-01'));
		
		$this->db->select(array('payment_date'));
		$this->db->from('coop_finance_transaction');
		$this->db->where("loan_id = '".$row_normal_loan['loan_id']."'");
		$this->db->order_by('payment_date DESC');
		$this->db->limit(1);
		$row_date_prev_paid = $this->db->get()->result_array();
		$row_date_prev_paid = $row_date_prev_paid[0];
		
		$date_prev_paid = $row_date_prev_paid['payment_date']!=''?$row_date_prev_paid['payment_date']:$row_normal_loan['date_transfer'];
		$diff = date_diff(date_create($date_prev_paid),date_create($date_interesting));
		$date_count = $diff->format("%a");
		$date_count = $date_count+1;
		
		$interest = ((($row_normal_loan['loan_amount_balance']*$row_normal_loan['interest_per_year'])/100)/365)*$date_count;
		
		if($row_normal_loan['loan_amount_balance'] > $row_principal_payment['principal_payment']){
			$principal_payment = $row_principal_payment['principal_payment'];
		}else{
			$principal_payment = $row_normal_loan['loan_amount_balance'];
		}
		$loan_arr[$row_normal_loan['loan_type']]['principal_payment'] += $principal_payment;
		
		//if($row['member_id']=='000012'){
			//echo $date_prev_paid;exit;
		//}
		
		$loan_arr[$row_normal_loan['loan_type']]['interest'] += $interest;
		
		$normal_loan[$b]['outstanding_balance'] = $row_normal_loan['loan_amount_balance'];
		$normal_loan[$b]['principal_payment'] = $principal_payment;
		$normal_loan[$b]['interest'] = $interest;
		$b++;
	}
	
	$this->db->select(
		array(
			't2.id as loan_id',
			't2.contract_number',
			't2.loan_type',
			't2.loan_amount_balance',
			't2.interest_per_year',
			't5.date_transfer'
		)
	);
	$this->db->from('coop_loan as t2');
	$this->db->join('coop_mem_apply as t3','t2.member_id = t3.member_id','inner');
	$this->db->join('coop_prename as t4','t3.prename_id = t4.prename_id','left');
	$this->db->join('coop_loan_transfer as t5','t2.id = t5.loan_id','inner');
	$this->db->where("
		t2.loan_type IN('3','4')
		AND t2.member_id = '".$row['member_id']."'
		AND t2.loan_status = '1'
		AND t2.loan_amount_balance > 0
		AND t2.date_start_period <= '".($_GET['year']-543)."-".sprintf("%02d",$_GET['month'])."-".date('t',strtotime(($_GET['year']-543)."-".sprintf("%02d",$_GET['month'])."-01"))."'
	");
	$rs_emergent_loan = $this->db->get()->result_array();
	$emergent_loan = array();
	$b=0;
	$sum_normal_outstanding_balance = 0;
	$sum_normal_principal_payment = 0;
	$sum_normal_interest = 0;
	$sum_normal_balance = 0;
	
	$sum_emergent_outstanding_balance = 0;
	$sum_emergent_principal_payment = 0;
	$sum_emergent_interest = 0;
	$sum_emergent_balance = 0;
	
	$sum_share = 0;
	$sum_share_collect = 0;
	
	foreach($rs_emergent_loan as $key => $row_emergent_loan){
		$emergent_loan[$b] = $row_emergent_loan;
		$loan_arr[$row_emergent_loan['loan_type']]['outstanding_balance'] += $row_emergent_loan['loan_amount_balance'];
		
		$this->db->select(array('principal_payment'));
		$this->db->from('coop_loan_period');
		$this->db->where("loan_id = '".$row_emergent_loan['loan_id']."'");
		$this->db->limit(1);
		$row_principal_payment = $this->db->get()->result_array();
		$row_principal_payment = $row_principal_payment[0];
		
		$date_interesting = date('Y-m-t',strtotime(($_GET['year']-543)."-".sprintf("%02d",$_GET['month']).'-01'));
		
		$this->db->select(array('payment_date'));
		$this->db->from('coop_finance_transaction');
		$this->db->where("loan_id = '".$row_emergent_loan['loan_id']."'");
		$this->db->order_by('payment_date DESC');
		$this->db->limit(1);
		$row_date_prev_paid = $this->db->get()->result_array();
		$row_date_prev_paid = $row_date_prev_paid[0];
		
		$date_prev_paid = $row_date_prev_paid['payment_date']!=''?$row_date_prev_paid['payment_date']:$row_emergent_loan['date_transfer'];
		$diff = date_diff(date_create($date_prev_paid),date_create($date_interesting));
		$date_count = $diff->format("%a");
		$date_count = $date_count+1;
		
		$interest = ((($row_emergent_loan['loan_amount_balance']*$row_emergent_loan['interest_per_year'])/100)/365)*$date_count;
		
		if($row_emergent_loan['loan_amount_balance'] > $row_principal_payment['principal_payment']){
			$principal_payment = $row_principal_payment['principal_payment'];
		}else{
			$principal_payment = $row_emergent_loan['loan_amount_balance'];
		}
		$loan_arr[$row_emergent_loan['loan_type']]['principal_payment'] += $principal_payment;
		
		$loan_arr[$row_emergent_loan['loan_type']]['interest'] += $interest;
		
		$emergent_loan[$b]['outstanding_balance'] = $row_emergent_loan['loan_amount_balance'];
		$emergent_loan[$b]['principal_payment'] = $principal_payment;
		$emergent_loan[$b]['interest'] = $interest;
		$b++;
	}
	$a = 0;
	if(count($normal_loan) == 0 && count($emergent_loan) == 0){
		$a = 0;
	}else{
		if(count($normal_loan)>count($emergent_loan)){
			$a = count($normal_loan)-1;
		}else{
			$a = count($emergent_loan)-1;
		}
	}
	
	for($j=0; $j<=$a; $j++){
		$i++;
		$objPHPExcel->getActiveSheet()->SetCellValue('A' . $i , $k++ ) ;
		$objPHPExcel->getActiveSheet()->SetCellValue('B' . $i , @$row['member_id']." " ) ;
		$objPHPExcel->getActiveSheet()->SetCellValue('C' . $i , @$row['employee_id']." " ) ;
		$objPHPExcel->getActiveSheet()->SetCellValue('D' . $i , @$row['prename_short'].$row['firstname_th']." ".@$row['lastname_th'] ) ;
		$objPHPExcel->getActiveSheet()->SetCellValue('E' . $i , @$mem_group_arr[$mem_group] ) ;
		$objPHPExcel->getActiveSheet()->SetCellValue('F' . $i , number_format($num_share*$share_value,2) ) ;
		$objPHPExcel->getActiveSheet()->SetCellValue('G' . $i , number_format(@$row_share['share_collect_value']+($num_share*$share_value),2) ) ;
		$objPHPExcel->getActiveSheet()->SetCellValue('H' . $i , @$normal_loan[$j]['contract_number']!=''?$normal_loan[$j]['contract_number']:'-' ) ; 
		$objPHPExcel->getActiveSheet()->SetCellValue('I' . $i , @$normal_loan[$j]['contract_number']!=''?number_format($normal_loan[$j]['outstanding_balance'],2):'-' ) ;
		$objPHPExcel->getActiveSheet()->SetCellValue('J' . $i , @$normal_loan[$j]['contract_number']!=''?number_format($normal_loan[$j]['principal_payment'],2):'-' ) ;
		$objPHPExcel->getActiveSheet()->SetCellValue('K' . $i , @$normal_loan[$j]['contract_number']!=''?number_format($normal_loan[$j]['interest'],2):'-' ) ;
		$objPHPExcel->getActiveSheet()->SetCellValue('L' . $i , @$normal_loan[$j]['contract_number']!=''?number_format($normal_loan[$j]['outstanding_balance'] - $normal_loan[$j]['principal_payment'],2):'-' ) ;
		$objPHPExcel->getActiveSheet()->SetCellValue('M' . $i , @$emergent_loan[$j]['contract_number']!=''?$emergent_loan[$j]['contract_number']:'-' ) ;
		$objPHPExcel->getActiveSheet()->SetCellValue('N' . $i , @$emergent_loan[$j]['contract_number']!=''?number_format($emergent_loan[$j]['outstanding_balance'],2):'-' ) ;
		$objPHPExcel->getActiveSheet()->SetCellValue('O' . $i , @$emergent_loan[$j]['contract_number']!=''?number_format($emergent_loan[$j]['principal_payment'],2):'-' ) ;
		$objPHPExcel->getActiveSheet()->SetCellValue('P' . $i , @$emergent_loan[$j]['contract_number']!=''?number_format($emergent_loan[$j]['interest'],2):'-' ) ;
		$objPHPExcel->getActiveSheet()->SetCellValue('Q' . $i , @$emergent_loan[$j]['contract_number']!=''?number_format($emergent_loan[$j]['outstanding_balance'] - $emergent_loan[$j]['principal_payment'],2):'-' ) ;
		$objPHPExcel->getActiveSheet()->SetCellValue('R' . $i , "" ) ;
		
		$sum_normal_outstanding_balance += @$normal_loan[$j]['outstanding_balance'];
		$sum_normal_principal_payment += @$normal_loan[$j]['principal_payment'];
		$sum_normal_interest += @$normal_loan[$j]['interest'];
		$sum_normal_balance += @$normal_loan[$j]['outstanding_balance'] - @$normal_loan[$j]['principal_payment'];
		
		$sum_emergent_outstanding_balance += @$emergent_loan[$j]['outstanding_balance'];
		$sum_emergent_principal_payment += @$emergent_loan[$j]['principal_payment'];
		$sum_emergent_interest += @$emergent_loan[$j]['interest'];
		$sum_emergent_balance += @$emergent_loan[$j]['outstanding_balance'] - @$emergent_loan[$j]['principal_payment'];
		
		$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':R'.$i)->applyFromArray($styleArray);
		$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':C'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('E'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('F'.$i.':G'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
		$objPHPExcel->getActiveSheet()->getStyle('H'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('I'.$i.':L'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
		$objPHPExcel->getActiveSheet()->getStyle('M'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('N'.$i.':Q'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
	 } 
	$sum_share += $num_share*$share_value;
	$sum_share_collect += $row_share['share_collect_value']+($num_share*$share_value);
	
	}
	
		$i+=2;
		$objPHPExcel->getActiveSheet()->SetCellValue('A' . $i , '' ) ;
		$objPHPExcel->getActiveSheet()->SetCellValue('B' . $i , '' ) ;
		$objPHPExcel->getActiveSheet()->SetCellValue('C' . $i , '' ) ;
		$objPHPExcel->getActiveSheet()->SetCellValue('D' . $i , '' ) ;
		$objPHPExcel->getActiveSheet()->SetCellValue('E' . $i , '' ) ;
		$objPHPExcel->getActiveSheet()->SetCellValue('F' . $i , number_format($sum_share,2) ) ;
		$objPHPExcel->getActiveSheet()->SetCellValue('G' . $i , number_format($sum_share_collect,2) ) ;
		$objPHPExcel->getActiveSheet()->SetCellValue('H' . $i , '-' ) ; 
		$objPHPExcel->getActiveSheet()->SetCellValue('I' . $i , number_format($sum_normal_outstanding_balance,2) ) ;
		$objPHPExcel->getActiveSheet()->SetCellValue('J' . $i , number_format($sum_normal_principal_payment,2) ) ;
		$objPHPExcel->getActiveSheet()->SetCellValue('K' . $i , number_format($sum_normal_interest,2) ) ;
		$objPHPExcel->getActiveSheet()->SetCellValue('L' . $i , number_format($sum_normal_balance) ) ;
		$objPHPExcel->getActiveSheet()->SetCellValue('M' . $i , '-' ) ;
		$objPHPExcel->getActiveSheet()->SetCellValue('N' . $i , number_format($sum_emergent_outstanding_balance,2) ) ;
		$objPHPExcel->getActiveSheet()->SetCellValue('O' . $i , number_format($sum_emergent_principal_payment,2) ) ;
		$objPHPExcel->getActiveSheet()->SetCellValue('P' . $i , number_format($sum_emergent_interest,2) ) ;
		$objPHPExcel->getActiveSheet()->SetCellValue('Q' . $i , number_format($sum_emergent_balance) ) ;
		$objPHPExcel->getActiveSheet()->SetCellValue('R' . $i , "" ) ;
		$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':R'.$i)->applyFromArray($textStyle);
		$objPHPExcel->getActiveSheet()->getStyle('F'.$i.':G'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
		$objPHPExcel->getActiveSheet()->getStyle('I'.$i.':L'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
		$objPHPExcel->getActiveSheet()->getStyle('N'.$i.':Q'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
		
		$i++;
		$objPHPExcel->getActiveSheet()->SetCellValue('J' . $i , number_format($sum_normal_principal_payment+$sum_normal_interest,2) ) ;
		$objPHPExcel->getActiveSheet()->SetCellValue('O' . $i , number_format($sum_emergent_principal_payment+$sum_emergent_interest,2) ) ;
		$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':R'.$i)->applyFromArray($textStyle);
		$objPHPExcel->getActiveSheet()->getStyle('J'.$i)->applyFromArray($borderBottom);
		$objPHPExcel->getActiveSheet()->getStyle('O'.$i)->applyFromArray($borderBottom);
		$objPHPExcel->getActiveSheet()->getStyle('J'.$i.':O'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
		
		$i+=2;
		$objPHPExcel->getActiveSheet()->SetCellValue('E' . $i , 'รวมรับ' ) ;
		$objPHPExcel->getActiveSheet()->SetCellValue('F' . $i , number_format($sum_share,2) ) ;
		$objPHPExcel->getActiveSheet()->SetCellValue('I' . $i , 'รวมรับ' ) ;
		$objPHPExcel->getActiveSheet()->SetCellValue('J' . $i , number_format($sum_normal_principal_payment+$sum_normal_interest,2) ) ;
		$objPHPExcel->getActiveSheet()->SetCellValue('N' . $i , 'รวมรับ' ) ;
		$objPHPExcel->getActiveSheet()->SetCellValue('O' . $i , number_format($sum_emergent_principal_payment+$sum_emergent_interest,2) ) ;
		
		$objPHPExcel->getActiveSheet()->SetCellValue('Q' . $i , 'รวมส่งหัก' ) ;
		$objPHPExcel->getActiveSheet()->SetCellValue('R' . $i , number_format($sum_share+$sum_normal_principal_payment+$sum_normal_interest+$sum_emergent_principal_payment+$sum_emergent_interest,2) ) ;
		$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':R'.$i)->applyFromArray($textStyle);
		$objPHPExcel->getActiveSheet()->getStyle('F'.$i)->applyFromArray($textStyleRed);
		$objPHPExcel->getActiveSheet()->getStyle('J'.$i)->applyFromArray($textStyleRed);
		$objPHPExcel->getActiveSheet()->getStyle('O'.$i)->applyFromArray($textStyleRed);
		$objPHPExcel->getActiveSheet()->getStyle('F'.$i)->applyFromArray($borderBottom);
		$objPHPExcel->getActiveSheet()->getStyle('J'.$i)->applyFromArray($borderBottom);
		$objPHPExcel->getActiveSheet()->getStyle('O'.$i)->applyFromArray($borderBottom);
		$objPHPExcel->getActiveSheet()->getStyle('E'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('I'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('N'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('Q'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('F'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
		$objPHPExcel->getActiveSheet()->getStyle('J'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
		$objPHPExcel->getActiveSheet()->getStyle('O'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
		$objPHPExcel->getActiveSheet()->getStyle('R'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
		
		$i+=2;
		$objPHPExcel->getActiveSheet()->SetCellValue('N' . $i , 'คชจ.เบ็ดเตร็ด' ) ;
		$objPHPExcel->getActiveSheet()->SetCellValue('Q' . $i , 'คชจ.เบ็ดเตร็ด' ) ;
		$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':R'.$i)->applyFromArray($textStyle);
		$objPHPExcel->getActiveSheet()->getStyle('N'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('Q'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		
		$i++;
		$objPHPExcel->getActiveSheet()->SetCellValue('I' . $i , 'กู้พิเศษ' ) ;
		$objPHPExcel->getActiveSheet()->SetCellValue('J' . $i , number_format(@$loan_arr[2]['principal_payment'],2) ) ;
		$objPHPExcel->getActiveSheet()->SetCellValue('N' . $i , 'ฉุกเฉิน' ) ;
		$objPHPExcel->getActiveSheet()->SetCellValue('O' . $i , number_format(@$loan_arr[3]['principal_payment'],2) ) ;
		$objPHPExcel->getActiveSheet()->getStyle('I'.$i.':O'.$i)->applyFromArray($textStyle);
		$objPHPExcel->getActiveSheet()->getStyle('I'.$i)->applyFromArray($textStylePink);
		$objPHPExcel->getActiveSheet()->getStyle('N'.$i)->applyFromArray($textStylePink);
		$objPHPExcel->getActiveSheet()->getStyle('J'.$i)->applyFromArray($textStyleRed);
		$objPHPExcel->getActiveSheet()->getStyle('O'.$i)->applyFromArray($textStyleRed);
		$objPHPExcel->getActiveSheet()->getStyle('I'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('N'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('J'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
		$objPHPExcel->getActiveSheet()->getStyle('O'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
		
		$i++;
		$objPHPExcel->getActiveSheet()->SetCellValue('I' . $i , 'ดอกเบี้ย' ) ;
		$objPHPExcel->getActiveSheet()->SetCellValue('J' . $i , number_format(@$loan_arr[2]['interest'],2) ) ;
		$objPHPExcel->getActiveSheet()->SetCellValue('N' . $i , 'ด/บ ฉุกเฉิน' ) ;
		$objPHPExcel->getActiveSheet()->SetCellValue('O' . $i , number_format(@$loan_arr[3]['interest'],2) ) ;
		$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':R'.$i)->applyFromArray($textStyle);
		$objPHPExcel->getActiveSheet()->getStyle('I'.$i)->applyFromArray($textStylePink);
		$objPHPExcel->getActiveSheet()->getStyle('N'.$i)->applyFromArray($textStylePink);
		$objPHPExcel->getActiveSheet()->getStyle('J'.$i)->applyFromArray($textStyleRed);
		$objPHPExcel->getActiveSheet()->getStyle('O'.$i)->applyFromArray($textStyleRed);
		$objPHPExcel->getActiveSheet()->getStyle('I'.$i)->applyFromArray($borderBottom);
		$objPHPExcel->getActiveSheet()->getStyle('J'.$i)->applyFromArray($borderBottom);
		$objPHPExcel->getActiveSheet()->getStyle('N'.$i)->applyFromArray($borderBottom);
		$objPHPExcel->getActiveSheet()->getStyle('O'.$i)->applyFromArray($borderBottom);
		$objPHPExcel->getActiveSheet()->getStyle('I'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('N'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('J'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
		$objPHPExcel->getActiveSheet()->getStyle('O'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
		
		$i++;
		$objPHPExcel->getActiveSheet()->SetCellValue('I' . $i , 'สามัญ' ) ;
		$objPHPExcel->getActiveSheet()->SetCellValue('J' . $i , number_format(@$loan_arr[1]['principal_payment'],2) ) ;
		$objPHPExcel->getActiveSheet()->SetCellValue('N' . $i , 'ฉุกเฉินพิเศษ' ) ;
		$objPHPExcel->getActiveSheet()->SetCellValue('O' . $i , number_format(@$loan_arr[4]['principal_payment'],2) ) ;
		$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':R'.$i)->applyFromArray($textStyle);
		$objPHPExcel->getActiveSheet()->getStyle('I'.$i)->applyFromArray($textStyleBlue);
		$objPHPExcel->getActiveSheet()->getStyle('N'.$i)->applyFromArray($textStyleGreen);
		$objPHPExcel->getActiveSheet()->getStyle('J'.$i)->applyFromArray($textStyleRed);
		$objPHPExcel->getActiveSheet()->getStyle('I'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('N'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('J'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
		$objPHPExcel->getActiveSheet()->getStyle('O'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
		
		$i++;
		$objPHPExcel->getActiveSheet()->SetCellValue('I' . $i , 'ด/บ สามัญ' ) ;
		$objPHPExcel->getActiveSheet()->SetCellValue('J' . $i , number_format(@$loan_arr[1]['interest'],2) ) ;
		$objPHPExcel->getActiveSheet()->SetCellValue('N' . $i , 'ด/บ' ) ;
		$objPHPExcel->getActiveSheet()->SetCellValue('O' . $i , number_format(@$loan_arr[4]['interest'],2) ) ;
		$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':R'.$i)->applyFromArray($textStyle);
		$objPHPExcel->getActiveSheet()->getStyle('I'.$i)->applyFromArray($textStyleBlue);
		$objPHPExcel->getActiveSheet()->getStyle('N'.$i)->applyFromArray($textStyleGreen);
		$objPHPExcel->getActiveSheet()->getStyle('J'.$i)->applyFromArray($textStyleRed);
		$objPHPExcel->getActiveSheet()->getStyle('I'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('N'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('J'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
		$objPHPExcel->getActiveSheet()->getStyle('O'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

$objPHPExcel->getActiveSheet()->setTitle('รายงานสรุป');
$objPHPExcel->getActiveSheet()->mergeCells('A1:R1');
$objPHPExcel->getActiveSheet()->SetCellValue('A1', "รายละเอียดการหักค่าหุ้น เงินกู้ฉุกเฉิน เงินกู้สามัญและเงินกู้พิเศษ เดือน ".$month_arr[(int)$_GET['month']]." ".$_GET['year']." (รวมทั้งสิ้น ".($k-1)." ท่าน)" ) ; 
$titleStyle = array(
		'font'  => array(
			'bold'  => true,
			'size'  => 16,
			'name'  => 'Cordia New'
		));
	$objPHPExcel->getActiveSheet()->getStyle('A1')->applyFromArray($titleStyle);
$objPHPExcel->getActiveSheet()->getStyle('A1:R1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="รายงานสรุปเดือน_'.$month_arr[(int)$_GET['month']].'_'.$_GET['year'].'.xlsx"');
header('Cache-Control: max-age=0');
		
$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
$objWriter->save('php://output');
exit;
?>