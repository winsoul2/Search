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
		'size'  => 12,
		'name'  => 'Cordia New'
	)
);
$styleArray2 = array(
  'font'  => array(
		'bold'  => false,
		'size'  => 12,
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
$headerStyle = array(
	'font'  => array(
		'bold'  => true,
		'size'  => 11,
		'name'  => 'Cordia New'
	)
);

$year = @$_GET['year'];

$objPHPExcel->getActiveSheet()->mergeCells('A1:X1');
$objPHPExcel->getActiveSheet()->mergeCells('A2:X2');
$objPHPExcel->getActiveSheet()->mergeCells('A3:X3');
$objPHPExcel->getActiveSheet()->SetCellValue('A1', @$_SESSION['COOP_NAME'] ) ; 
$objPHPExcel->getActiveSheet()->SetCellValue('A2', " รายละเอียดยอดลูกหนี้คงเหลือ" ) ; 
$objPHPExcel->getActiveSheet()->SetCellValue('A3', " สำหรับสิ้นสุดวันที่ 31 ธันวาคม ".@$year ) ; 
$titleStyle = array(
		'font'  => array(
			'bold'  => true,
			'size'  => 18,
			'name'  => 'Cordia New'
		));
$objPHPExcel->getActiveSheet()->getStyle('A1')->applyFromArray($titleStyle);
$objPHPExcel->getActiveSheet()->getStyle('A2')->applyFromArray($titleStyle);
$objPHPExcel->getActiveSheet()->getStyle('A3')->applyFromArray($titleStyle);
$objPHPExcel->getActiveSheet()->getStyle('A1:X3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

$objPHPExcel->getActiveSheet()->getStyle('A4:X8')->applyFromArray($headerStyle);

	$i = 4 ;
	$objPHPExcel->getActiveSheet()->mergeCells('A4:A8');
	$objPHPExcel->getActiveSheet()->SetCellValue('A' . $i , "ที่" ) ; 
	$objPHPExcel->getActiveSheet()->mergeCells('C4:E8');
	$objPHPExcel->getActiveSheet()->SetCellValue('C' . $i, "ชื่อ - สกุล" ) ; 
	$objPHPExcel->getActiveSheet()->mergeCells('M4:P4');
	$objPHPExcel->getActiveSheet()->SetCellValue('M' . $i, "เงินกู้สามัญ" ) ; 
	$objPHPExcel->getActiveSheet()->mergeCells('Q4:S4');
	$objPHPExcel->getActiveSheet()->SetCellValue('Q' . $i, "เงินกู้พิเศษ" ) ; 
	$objPHPExcel->getActiveSheet()->mergeCells('T4:W4');
	$objPHPExcel->getActiveSheet()->SetCellValue('T' . $i, "ดอกเบี้ยเงินให้กู้ค้างรับ" ) ; 
	
	$i = 5 ;
	$objPHPExcel->getActiveSheet()->SetCellValue('B' . $i, "เลขที่" ) ; 
	$objPHPExcel->getActiveSheet()->SetCellValue('H' . $i, "ทุนเรือนหุ้น" ) ; 
	$objPHPExcel->getActiveSheet()->SetCellValue('I' . $i, "เงินฝาก" ) ; 
	$objPHPExcel->getActiveSheet()->SetCellValue('J' . $i, "เงินกู้ฉุกเฉิน" ) ; 
	$objPHPExcel->getActiveSheet()->SetCellValue('K' . $i, "เงินกู้ฉุกเฉิน" ) ; 
	$objPHPExcel->getActiveSheet()->SetCellValue('L' . $i, "เลขที่" ) ; 
	$objPHPExcel->getActiveSheet()->mergeCells('M5:N5');
	$objPHPExcel->getActiveSheet()->SetCellValue('M' . $i, "ลูกหนี้" ) ; 
	$objPHPExcel->getActiveSheet()->SetCellValue('O' . $i, "ลูกหนี้" ) ; 
	$objPHPExcel->getActiveSheet()->SetCellValue('P' . $i, "เงินกู้สามัญ" ) ; 
	$objPHPExcel->getActiveSheet()->SetCellValue('Q' . $i, "ลูกหนี้" ) ; 
	$objPHPExcel->getActiveSheet()->SetCellValue('R' . $i, "ลูกหนี้" ) ; 
	$objPHPExcel->getActiveSheet()->SetCellValue('S' . $i, "เงินกู้พิเศษ" ) ;
	$objPHPExcel->getActiveSheet()->SetCellValue('X' . $i, "ลงชื่อ" ) ; 
	
	$i = 6 ;
	$objPHPExcel->getActiveSheet()->SetCellValue('B' . $i, "สมาชิก" ) ; 
	$objPHPExcel->getActiveSheet()->SetCellValue('G' . $i, "จำนวน" ) ; 
	$objPHPExcel->getActiveSheet()->SetCellValue('H' . $i, "จำนวนเงิน" ) ; 
	$objPHPExcel->getActiveSheet()->SetCellValue('I' . $i, "ออมทรัพย์" ) ; 
	$objPHPExcel->getActiveSheet()->SetCellValue('J' . $i, "คงเหลือ" ) ; 
	$objPHPExcel->getActiveSheet()->SetCellValue('K' . $i, "กรณีพิเศษ" ) ;
	$objPHPExcel->getActiveSheet()->SetCellValue('L' . $i, "สัญญา" ) ;
	$objPHPExcel->getActiveSheet()->SetCellValue('M' . $i, "ชำระคืน" ) ;
	$objPHPExcel->getActiveSheet()->SetCellValue('N' . $i, "ระยะสั้น" ) ;
	$objPHPExcel->getActiveSheet()->SetCellValue('O' . $i, "ระยะยาว" ) ;
	$objPHPExcel->getActiveSheet()->SetCellValue('P' . $i, "คงเหลือ" ) ;
	$objPHPExcel->getActiveSheet()->SetCellValue('Q' . $i, "ระยะสั้น" ) ;
	$objPHPExcel->getActiveSheet()->SetCellValue('R' . $i, "ระยะยาว" ) ;
	$objPHPExcel->getActiveSheet()->SetCellValue('S' . $i, "คงเหลือ" ) ;
	$objPHPExcel->getActiveSheet()->SetCellValue('T' . $i, "ฉุกเฉิน" ) ;
	$objPHPExcel->getActiveSheet()->SetCellValue('U' . $i, "ฉุกเฉิน" ) ;
	$objPHPExcel->getActiveSheet()->SetCellValue('V' . $i, "สามัญ" ) ;
	$objPHPExcel->getActiveSheet()->SetCellValue('W' . $i, "พิเศษ" ) ;
	$objPHPExcel->getActiveSheet()->SetCellValue('X' . $i, "ยืนยันยอด" ) ;
	
	$i = 7 ;
	$objPHPExcel->getActiveSheet()->SetCellValue('G' . $i, "หุ้น" ) ;
	$objPHPExcel->getActiveSheet()->SetCellValue('H' . $i, "(บาท)" ) ;
	$objPHPExcel->getActiveSheet()->SetCellValue('I' . $i, "พิเศษ" ) ;
	$objPHPExcel->getActiveSheet()->SetCellValue('K' . $i, "คงเหลือ" ) ;
	$objPHPExcel->getActiveSheet()->SetCellValue('L' . $i, "เงินกู้สามัญ" ) ;
	$objPHPExcel->getActiveSheet()->SetCellValue('M' . $i, "งวดละ" ) ;
	$objPHPExcel->getActiveSheet()->SetCellValue('U' . $i, "พิเศษ" ) ;
	
	$i = 8 ; 
	
	$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(3.71);
	$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(11.86);
	$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(3.29);
	$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(9.57);
	$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(10.29);
	$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(10.29);
	$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(9.14);
	$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(11.71);
	$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(10.86);
	$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(10.29);
	$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(11.57);
	$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(10);
	$objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(10.57);
	$objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(11.29);
	$objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(11.29);
	$objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth(11.57);
	$objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setWidth(8.86);
	$objPHPExcel->getActiveSheet()->getColumnDimension('R')->setWidth(10.43);
	$objPHPExcel->getActiveSheet()->getColumnDimension('S')->setWidth(10.71);
	$objPHPExcel->getActiveSheet()->getColumnDimension('T')->setWidth(8.29);
	$objPHPExcel->getActiveSheet()->getColumnDimension('U')->setWidth(9.14);
	$objPHPExcel->getActiveSheet()->getColumnDimension('V')->setWidth(10.14);
	$objPHPExcel->getActiveSheet()->getColumnDimension('W')->setWidth(9.86);
	$objPHPExcel->getActiveSheet()->getColumnDimension('X')->setWidth(9.86);
	
foreach(range('A','X') as $columnID) {
	$objPHPExcel->getActiveSheet()->getStyle($columnID.'4')->applyFromArray($borderTop);
	$objPHPExcel->getActiveSheet()->getStyle($columnID.'4')->applyFromArray($borderLeft);
	$objPHPExcel->getActiveSheet()->getStyle($columnID.'4')->applyFromArray($borderRight);
	$objPHPExcel->getActiveSheet()->getStyle($columnID.'5')->applyFromArray($borderLeft);
	$objPHPExcel->getActiveSheet()->getStyle($columnID.'5')->applyFromArray($borderRight);
	$objPHPExcel->getActiveSheet()->getStyle($columnID.'6')->applyFromArray($borderLeft);
	$objPHPExcel->getActiveSheet()->getStyle($columnID.'6')->applyFromArray($borderRight);
	$objPHPExcel->getActiveSheet()->getStyle($columnID.'7')->applyFromArray($borderLeft);
	$objPHPExcel->getActiveSheet()->getStyle($columnID.'7')->applyFromArray($borderRight);
	$objPHPExcel->getActiveSheet()->getStyle($columnID.'8')->applyFromArray($borderLeft);
	$objPHPExcel->getActiveSheet()->getStyle($columnID.'8')->applyFromArray($borderRight);
	$objPHPExcel->getActiveSheet()->getStyle($columnID.'8')->applyFromArray($borderBottom);
}
	$objPHPExcel->getActiveSheet()->getStyle("M4:W4")->applyFromArray($borderBottom);
	$objPHPExcel->getActiveSheet()->getStyle("M5:N5")->applyFromArray($borderBottom);
	$objPHPExcel->getActiveSheet()->getStyle('A4:X8')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$objPHPExcel->getActiveSheet()->getStyle('A4:X8')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
	
	$this->db->select(array('t1.*','t2.prename_short'));
	$this->db->from('coop_mem_apply as t1');
	$this->db->join('coop_prename as t2','t1.prename_id = t2.prename_id','left');
	$rs = $this->db->get()->result_array();
		
	$k=1;
	if(!empty($rs)){
		foreach(@$rs as $key => $row){	
			//if(@$row['level']!=''){
				$mem_group = @$row['level'];
			//}else if(@$row['faction']!=''){
				//$mem_group = @$row['faction'];
			//}else{
				//$mem_group = @$row['department'];
			//}
			$this->db->select(array('share_collect','share_collect_value'));
			$this->db->from('coop_mem_share');			
			$this->db->where("member_id = '".@$row['member_id']."'");
			$this->db->order_by('share_id DESC');
			$this->db->limit(1);		
			$rs_share = $this->db->get()->result_array();
			$row_share = @$rs_share[0];
			
			$this->db->select(array('t1.transaction_balance'));
			$this->db->from('coop_account_transaction as t1');
			$this->db->join('coop_maco_account as t2','t1.account_id = t2.account_id','inner');
			$this->db->where("t2.mem_id = '".@$row['member_id']."'");
			$this->db->order_by('t1.transaction_id DESC');
			$this->db->limit(1);
			$rs_deposit = $this->db->get()->result_array();
			$row_deposit = @$rs_deposit[0];
			
			$this->db->select(array('*','coop_loan.id'));
			$this->db->from('coop_loan');
			$this->db->join('coop_loan_transfer','coop_loan.id = coop_loan_transfer.loan_id','inner');
			$this->db->where("member_id = '".@$row['member_id']."' AND loan_status = '1'");
			$rs_loan = $this->db->get()->result_array();
			
			$loan_arr = array();
			if(!empty($rs_loan)){
				foreach(@$rs_loan as $key => $row_loan){	
					@$loan_arr[@$row_loan['loan_type']]['contract_number'] = @$row_loan['contract_number'];
					@$loan_arr[@$row_loan['loan_type']]['loan_amount_balance'] += @$row_loan['loan_amount_balance'];
					@$loan_amount_balance = @$row_loan['loan_amount_balance'];
					
					$this->db->select(array('principal_payment'));
					$this->db->from('coop_loan_period');			
					$this->db->where("loan_id = '".@$row_loan['id']."' AND date_period LIKE '".(@$year-543)."%'");		
					$rs_period = $this->db->get()->result_array();
					$row_period = @$rs_period[0];
					
					$loan_arr[@$row_loan['loan_type']]['money_per_period'] = @$row_period['principal_payment'];
					$principal_payment_in_year = 0;
					for($a=1;$a<=12;$a++){
						if(@$row_period['principal_payment'] < @$loan_amount_balance){
							$principal_payment_in_year += @$row_period['principal_payment'];
						}else{
							$principal_payment_in_year += $loan_amount_balance;
						}
						$loan_amount_balance -= @$row_period['principal_payment'];
						if($loan_amount_balance <= 0){
							break;
						}
					}
					@$loan_arr[@$row_loan['loan_type']]['principal_payment_in_year'] += @$principal_payment_in_year;
				}
			}
			
			
			$this->db->select(array('t1.loan_amount_balance',
									't1.loan_type',
									't2.date_transfer',
									't1.id',
									't1.interest_per_year'));
			$this->db->from('coop_loan as t1');
			$this->db->join('coop_loan_transfer as t2','t1.id = t2.loan_id','inner');
			$this->db->where("t1.member_id = '".@$row['member_id']."' AND t1.loan_status = '1' AND t1.loan_amount_balance > 0 ");
			$rs_loan_residue = $this->db->get()->result_array();
			
			$loan_residue = array();
			if(!empty($rs_loan_residue)){
				foreach(@$rs_loan_residue as $key => $row_loan_residue){
					//if($row_loan_residue['loan_type'] == '3' || $row_loan_residue['loan_type'] == '4' && date('d',strtotime($row_loan_residue['date_transfer'])) >= '16'){
						//continue;
					//}
					$date_interesting = ($year-543)."-12-31";
					
					$this->db->select(array('payment_date'));
					$this->db->from('coop_finance_transaction');			
					$this->db->where("loan_id = '".@$row_loan_residue['id']."'");	
					$this->db->order_by("payment_date DESC");	
					$rs_date_prev_paid = $this->db->get()->result_array();
					$row_date_prev_paid = @$rs_date_prev_paid[0];
					
					$date_prev_paid = @$row_date_prev_paid['payment_date']!=''?@$row_date_prev_paid['payment_date']:@$row_loan_residue['date_transfer'];
					$diff = date_diff(date_create($date_prev_paid),date_create($date_interesting));
					$date_count = $diff->format("%a");
					$date_count = $date_count+1;
					
					$interest = (((@$row_loan_residue['loan_amount_balance']*@$row_loan_residue['interest_per_year'])/100)/365)*@$date_count;
					@$loan_residue[@$row_loan_residue['loan_type']] += @$interest;
				}
			}
		
		/*if($row['member_id'] == '000012'){
			echo $sql_text;
			echo"<pre>";print_r($loan_arr);echo"</pre>";
			exit;
		}*/
		$i++;
		$objPHPExcel->getActiveSheet()->SetCellValue('A' . $i , $k++ ) ;
		$objPHPExcel->getActiveSheet()->SetCellValue('B' . $i , @$row['member_id'] ) ;
		$objPHPExcel->getActiveSheet()->SetCellValue('C' . $i , @$row['prename_short'] ) ;
		$objPHPExcel->getActiveSheet()->SetCellValue('D' . $i , @$row['firstname_th'] ) ;
		$objPHPExcel->getActiveSheet()->SetCellValue('E' . $i , @$row['lastname_th'] ) ;
		$objPHPExcel->getActiveSheet()->SetCellValue('F' . $i , @$mem_group_arr[@$mem_group] ) ;
		$objPHPExcel->getActiveSheet()->SetCellValue('G' . $i , number_format(@$row_share['share_collect'],2) ) ;
		$objPHPExcel->getActiveSheet()->SetCellValue('H' . $i , number_format(@$row_share['share_collect']*@$share_value,2) ) ;
		$objPHPExcel->getActiveSheet()->SetCellValue('I' . $i , number_format(@$row_deposit['transaction_balance'],2) ) ;
		$objPHPExcel->getActiveSheet()->SetCellValue('J' . $i , number_format(@$loan_arr['3']['loan_amount_balance'],2) ) ;
		$objPHPExcel->getActiveSheet()->SetCellValue('K' . $i , number_format(@$loan_arr['4']['loan_amount_balance'],2) ) ;
		if(@$loan_arr['1']['contract_number']!=''){
			$objPHPExcel->getActiveSheet()->SetCellValue('L' . $i , @$loan_arr['1']['contract_number'] ) ;
			$objPHPExcel->getActiveSheet()->SetCellValue('M' . $i , number_format(@$loan_arr['1']['money_per_period'],2) ) ;
		}else{
			$objPHPExcel->getActiveSheet()->SetCellValue('L' . $i , @$loan_arr['2']['contract_number'] ) ;
			$objPHPExcel->getActiveSheet()->SetCellValue('M' . $i , number_format(@$loan_arr['2']['money_per_period'],2) ) ;
		}
		$objPHPExcel->getActiveSheet()->SetCellValue('N' . $i , number_format(@$loan_arr['1']['principal_payment_in_year'],2) ) ;
		$objPHPExcel->getActiveSheet()->SetCellValue('O' . $i , number_format(@$loan_arr['1']['loan_amount_balance']-@$loan_arr['1']['principal_payment_in_year'],2) ) ;
		$objPHPExcel->getActiveSheet()->SetCellValue('P' . $i , number_format(@$loan_arr['1']['loan_amount_balance'],2) ) ;
		$objPHPExcel->getActiveSheet()->SetCellValue('Q' . $i , number_format(@$loan_arr['2']['principal_payment_in_year'],2) ) ;
		$objPHPExcel->getActiveSheet()->SetCellValue('R' . $i , number_format(@$loan_arr['2']['loan_amount_balance']-@$loan_arr['2']['principal_payment_in_year'],2) ) ;
		$objPHPExcel->getActiveSheet()->SetCellValue('S' . $i , number_format(@$loan_arr['2']['loan_amount_balance'],2) ) ;
		
		$objPHPExcel->getActiveSheet()->SetCellValue('T' . $i , number_format(@$loan_residue['3'],2) ) ;
		$objPHPExcel->getActiveSheet()->SetCellValue('U' . $i , number_format(@$loan_residue['4'],2) ) ;
		$objPHPExcel->getActiveSheet()->SetCellValue('V' . $i , number_format(@$loan_residue['1'],2) ) ;
		$objPHPExcel->getActiveSheet()->SetCellValue('W' . $i , number_format(@$loan_residue['2'],2) ) ;
		
		
		$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':B'.$i)->applyFromArray($styleArray);
		$objPHPExcel->getActiveSheet()->getStyle('C'.$i.':E'.$i)->applyFromArray($styleArray2);
		$objPHPExcel->getActiveSheet()->getStyle('C'.$i.':E'.$i)->applyFromArray($borderBottom);
		$objPHPExcel->getActiveSheet()->getStyle('F'.$i.':X'.$i)->applyFromArray($styleArray);
		
		$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':B'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('F'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('G'.$i.':K'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
		$objPHPExcel->getActiveSheet()->getStyle('L'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('M'.$i.':X'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
		}
	}

$objPHPExcel->getActiveSheet()->setTitle('รายงานสรุปรายปี');
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="รายงานสรุปปี_'.$year.'.xlsx"');
header('Cache-Control: max-age=0');
		
$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
$objWriter->save('php://output');
exit;	
?>