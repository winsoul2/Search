<?php
$month_arr = array('1'=>'มกราคม','2'=>'กุมภาพันธ์','3'=>'มีนาคม','4'=>'เมษายน','5'=>'พฤษภาคม','6'=>'มิถุนายน','7'=>'กรกฎาคม','8'=>'สิงหาคม','9'=>'กันยายน','10'=>'ตุลาคม','11'=>'พฤศจิกายน','12'=>'ธันวาคม');
$month_short_arr = array('1'=>'ม.ค.','2'=>'ก.พ.','3'=>'มี.ค.','4'=>'เม.ย.','5'=>'พ.ค.','6'=>'มิ.ย.','7'=>'ก.ค.','8'=>'ส.ค.','9'=>'ก.ย.','10'=>'ต.ค.','11'=>'พ.ย.','12'=>'ธ.ค.');

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
		'size'  => 14,
		'name'  => 'Cordia New'
	)
);
$textStyleArray = array(
  'font'  => array(
		'bold'  => false,
		'size'  => 14,
		'name'  => 'CordiaUPC'
	)
);
$headerStyle = array(
	'font'  => array(
		'bold'  => true,
		'size'  => 16,
		'name'  => 'Cordia New'
	)
);
$titleStyle = array(
	'font'  => array(
		'bold'  => true,
		'size'  => 24,
		'name'  => 'AngsanaUPC'
	)
);
$footerStyle = array(
	'font'  => array(
		'bold'  => true,
		'size'  => 22,
		'name'  => 'AngsanaUPC'
	)
);
if(@$_GET['report_date'] != ''){
	$date_arr = explode('/',@$_GET['report_date']);
	$day = (int)@$date_arr[0];
	$month = (int)@$date_arr[1];
	$year = (int)@$date_arr[2];
	$year -= 543;
	$file_name_text = $day."_".$month_arr[$month]."_".($year+543);
}else{
	if(@$_GET['month']!='' && @$_GET['year']!=''){
		$day = '';
		$month = @@$_GET['month'];
		$year = (@$_GET['year']-543);
		$file_name_text = @$month_arr[@$month]."_".(@$year+543);
	}else{
		$day = '';
		$month = '';
		$year = (@$_GET['year']-543);
		$file_name_text = (@$year+543);
	}
}

if($month!=''){
	$month_start = @$month;
	$month_end = @$month;
}else{
	if(@$_GET['second_half']=='1'){
		$month_start = 7;
		$month_end = 12;
		$file_name_text .= "(ก.ค.-ธ.ค.)";
	}else{
		$month_start = 1;
		$month_end = 6;
		$file_name_text .= "(ม.ค.-มิ.ย.)";
	}
}
$sheet = 0;
$where = '';
if(@$day != '' && @$month != ''){
	$s_date = $year.'-'.sprintf("%02d",@$month).'-'.sprintf("%02d",@$day).' 00:00:00.000';
	$e_date = $year.'-'.sprintf("%02d",@$month).'-'.sprintf("%02d",@$day).' 23:59:59.000';
	$where .= " AND createdatetime BETWEEN '".$s_date."' AND '".$e_date."'";
}else if($day == '' && $month != ''){
	$s_date = $year.'-'.sprintf("%02d",@$month).'-01'.' 00:00:00.000';
	$e_date = date('Y-m-t',strtotime($s_date)).' 23:59:59.000';
	$where .= " AND createdatetime BETWEEN '".$s_date."' AND '".$e_date."'";
}else{
	$where .= " AND createdatetime BETWEEN '".$year."-01-01 00:00:00.000' AND '".$year."-12-31 23:59:59.000' ";
}

$this->db->select(array('loan_id','contract_number','createdatetime','member_id','employee_id','prename_short','firstname_th','lastname_th','level','period_amount','loan_amount','money_period_1','loan_reason'));
$this->db->from('coop_report_loan_normal_excel_1');
$this->db->where("loan_type = '".@$_GET['loan_type']."' AND loan_status IN ('1','2','4') {$where}");
$this->db->order_by('createdatetime ASC');
$rs = $this->db->get()->result_array();
$data = array();
$i = 0;
if(!empty($rs)){
	foreach(@$rs as $key => $row){
		$i+=1;
		$this->db->select(array('date_period'));
		$this->db->from('coop_loan_period');
		$this->db->where("loan_id = '".@$row['loan_id']."'");
		$this->db->order_by('period_count ASC');
		$this->db->limit(1);
		$rs_period = $this->db->get()->result_array();
		$row_period  = @$rs_period[0];
		$first_period = @$row_period['date_period'];
		
		$this->db->select(array('date_period'));
		$this->db->from('coop_loan_period');
		$this->db->where("loan_id = '".@$row['loan_id']."'");
		$this->db->order_by('period_count DESC');
		$this->db->limit(1);
		$rs_period2 = $this->db->get()->result_array();
		$row_period2  = @$rs_period2[0];
		$last_period = @$row_period2['date_period'];
		
		$createdatetime = explode(' ',@$row['createdatetime']);
		$createdate = explode('-',@$createdatetime[0]);
		$create_month = (int)@$createdate[1];
		$data[@$create_month][@$row['loan_id']] = @$row;
		$data[@$create_month][@$row['loan_id']]['first_period'] = @$first_period;
		$data[@$create_month][@$row['loan_id']]['last_period'] = @$last_period;
		
		$this->db->select(array('*'));
		$this->db->from('coop_loan_guarantee');
		$this->db->where("loan_id = '".@$row['loan_id']."'");
		$this->db->order_by('guarantee_type ASC');
		$rs_guarantee = $this->db->get()->result_array();

		$loan_guarantee = array();
		if(!empty($rs_guarantee)){
			foreach($rs_guarantee as $key => $row_guarantee){
				$loan_guarantee[] = @$row_guarantee;
			}
		}
		$data[@$create_month][@$row['loan_id']]['loan_guarantee'] = @$loan_guarantee;
	}
}
//echo"<pre>";print_r($data);exit;
for($m = $month_start; $m <= $month_end; $m++){
	$i=0;
	$objPHPExcel->createSheet($sheet);
	$objPHPExcel->setActiveSheetIndex($sheet);
		$i+=1;
		$objPHPExcel->getActiveSheet()->mergeCells('A'.$i.':S'.$i);
		$objPHPExcel->getActiveSheet()->SetCellValue('A'.$i, "ทะเบียน".@$loan_type[@$_GET['loan_type']]."  เดือน  ".@$month_arr[$m]." ".(@$year+543) ) ;
		$objPHPExcel->getActiveSheet()->getStyle('A'.$i)->applyFromArray($titleStyle);
		$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':S'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		
		$i+=1;
		$i_top = $i;
		$objPHPExcel->getActiveSheet()->SetCellValue('A' . $i , "หนังสือกู้สำหรับ" ) ; 
		$objPHPExcel->getActiveSheet()->SetCellValue('B' . $i, "วันที่" ) ;
		$objPHPExcel->getActiveSheet()->mergeCells('B'.$i.':B'.($i+2));	
		$objPHPExcel->getActiveSheet()->SetCellValue('C' . $i, "" ) ;
		$objPHPExcel->getActiveSheet()->SetCellValue('D' . $i, "" ) ;
		$objPHPExcel->getActiveSheet()->SetCellValue('E' . $i, "ผู้กู้" ) ;
		$objPHPExcel->getActiveSheet()->mergeCells('E'.$i.':G'.($i));	
		$objPHPExcel->getActiveSheet()->SetCellValue('H' . $i, "" ) ;	
		$objPHPExcel->getActiveSheet()->SetCellValue('I' . $i, "ระยะ" ) ;
		$objPHPExcel->getActiveSheet()->SetCellValue('J' . $i, "จำนวน" ) ;
		$objPHPExcel->getActiveSheet()->SetCellValue('K' . $i, "การส่งเงินงวดชำระหนี้" ) ;
		$objPHPExcel->getActiveSheet()->mergeCells('K'.$i.':M'.$i);	
		$objPHPExcel->getActiveSheet()->SetCellValue('N' . $i, "หนังสือค้ำประกัน" ) ;
		$objPHPExcel->getActiveSheet()->mergeCells('N'.$i.':O'.$i);	
		$objPHPExcel->getActiveSheet()->SetCellValue('P' . $i, "ผู้ค้ำประกัน" ) ;
		$objPHPExcel->getActiveSheet()->SetCellValue('Q' . $i, "" ) ;	
		$objPHPExcel->getActiveSheet()->SetCellValue('R' . $i, "" ) ;	
		$objPHPExcel->getActiveSheet()->SetCellValue('S' . $i, "หมายเหตุ" ) ;	
		
		$i+=1;
		$i_middle = $i;
		$objPHPExcel->getActiveSheet()->SetCellValue('A' . $i , $loan_type[@$_GET['loan_type']] ) ; 
		$objPHPExcel->getActiveSheet()->SetCellValue('C' . $i, "สมาชิกเลข" ) ;
		$objPHPExcel->getActiveSheet()->SetCellValue('D' . $i, "รหัส" ) ;
		$objPHPExcel->getActiveSheet()->SetCellValue('E' . $i, "ชื่อ-สกุล" ) ;
		$objPHPExcel->getActiveSheet()->mergeCells('E'.$i.':G'.($i));	
		$objPHPExcel->getActiveSheet()->SetCellValue('H' . $i, "หน่วย" ) ;	
		$objPHPExcel->getActiveSheet()->SetCellValue('I' . $i, "การชำระ" ) ;
		$objPHPExcel->getActiveSheet()->SetCellValue('J' . $i, "เงินกู้" ) ;
		$objPHPExcel->getActiveSheet()->SetCellValue('K' . $i, "งวดละ" ) ;
		$objPHPExcel->getActiveSheet()->SetCellValue('L' . $i, "ตั้งแต่" ) ;
		$objPHPExcel->getActiveSheet()->SetCellValue('M' . $i, "ถึง" ) ;
		$objPHPExcel->getActiveSheet()->SetCellValue('N' . $i, "ที่" ) ;
		$objPHPExcel->getActiveSheet()->SetCellValue('O' . $i, "วันที่" ) ;
		$objPHPExcel->getActiveSheet()->SetCellValue('P' . $i, "ชื่อ-สกุล" ) ;
		$objPHPExcel->getActiveSheet()->SetCellValue('Q' . $i, "สมาชิกเลข" ) ;
		$objPHPExcel->getActiveSheet()->SetCellValue('R' . $i, "หน่วย" ) ;
		
		$i+=1;
		$i_bottom = $i;
		$objPHPExcel->getActiveSheet()->SetCellValue('A' . $i , "ที่" ) ; 
		$objPHPExcel->getActiveSheet()->SetCellValue('C' . $i, "ทะเบียน" ) ;
		$objPHPExcel->getActiveSheet()->SetCellValue('D' . $i, "พนักงาน" ) ;
		$objPHPExcel->getActiveSheet()->SetCellValue('E' . $i, "" ) ;
		$objPHPExcel->getActiveSheet()->mergeCells('E'.$i.':G'.($i));	
		$objPHPExcel->getActiveSheet()->SetCellValue('H' . $i, "งาน" ) ;	
		$objPHPExcel->getActiveSheet()->SetCellValue('I' . $i, "(งวด)" ) ;
		$objPHPExcel->getActiveSheet()->SetCellValue('J' . $i, "(บาท)" ) ;
		$objPHPExcel->getActiveSheet()->SetCellValue('Q' . $i, "ทะเบียน" ) ;
		$objPHPExcel->getActiveSheet()->SetCellValue('R' . $i, "งาน" ) ;
		
		$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(17.43);
		$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(11.14);
		$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(12);
		$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(10.86);
		$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(6.14);
		$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(9.86);
		$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(13.86);
		$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(10);
		$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(10.86);
		$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(13.29);
		$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(11.86);
		$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(10.43);
		$objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(11.43);
		$objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(12);
		$objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(10);
		$objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth(37.86);
		$objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setWidth(13.29);
		$objPHPExcel->getActiveSheet()->getColumnDimension('R')->setWidth(8.29);
		$objPHPExcel->getActiveSheet()->getColumnDimension('S')->setWidth(26.43);
		
		foreach(range('A','S') as $columnID) {
			$objPHPExcel->getActiveSheet()->getStyle($columnID.$i_top)->applyFromArray($borderTop);
			$objPHPExcel->getActiveSheet()->getStyle($columnID.$i_top)->applyFromArray($borderLeft);
			$objPHPExcel->getActiveSheet()->getStyle($columnID.$i_top)->applyFromArray($borderRight);
			
			$objPHPExcel->getActiveSheet()->getStyle($columnID.$i_middle)->applyFromArray($borderLeft);
			$objPHPExcel->getActiveSheet()->getStyle($columnID.$i_middle)->applyFromArray($borderRight);
			
			$objPHPExcel->getActiveSheet()->getStyle($columnID.$i_bottom)->applyFromArray($borderLeft);
			$objPHPExcel->getActiveSheet()->getStyle($columnID.$i_bottom)->applyFromArray($borderRight);
			$objPHPExcel->getActiveSheet()->getStyle($columnID.$i_bottom)->applyFromArray($borderBottom);
			
			$objPHPExcel->getActiveSheet()->getStyle($columnID.$i_top)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle($columnID.$i_middle)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle($columnID.$i_bottom)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		}
		$objPHPExcel->getActiveSheet()->getStyle('K'.$i_top.':O'.$i_top)->applyFromArray($borderBottom);
		$objPHPExcel->getActiveSheet()->getStyle('A'.$i_top.':S'.$i_bottom)->applyFromArray($headerStyle);
		$objPHPExcel->getActiveSheet()->getStyle('A'.$i_top.':S'.$i_bottom)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
		$count_loan = 0;
		$loan_amount=0;
		if(!empty($data[$m])){
			foreach($data[$m] as $key => $value){
				$i+=1;
				$loan_amount += @$value['loan_amount'];
				
				$objPHPExcel->getActiveSheet()->SetCellValue('A' . $i , @$value['contract_number'] );
				$objPHPExcel->getActiveSheet()->SetCellValue('B' . $i , $this->center_function->mydate2date(@$row['createdatetime']) );
				$objPHPExcel->getActiveSheet()->SetCellValue('C' . $i , @$value['member_id']." " );
				$objPHPExcel->getActiveSheet()->SetCellValue('D' . $i , @$value['employee_id']." " );
				$objPHPExcel->getActiveSheet()->SetCellValue('E' . $i , @$value['prename_short'] );
				$objPHPExcel->getActiveSheet()->SetCellValue('F' . $i , @$value['firstname_th'] );
				$objPHPExcel->getActiveSheet()->SetCellValue('G' . $i , @$value['lastname_th'] );
				$objPHPExcel->getActiveSheet()->SetCellValue('H' . $i , @$mem_group_arr[@$value['level']] );
				$objPHPExcel->getActiveSheet()->SetCellValue('I' . $i , @$value['period_amount'] );
				$objPHPExcel->getActiveSheet()->SetCellValue('J' . $i , number_format(@$value['loan_amount'],2) );
				$objPHPExcel->getActiveSheet()->SetCellValue('K' . $i , number_format(@$value['money_period_1'],2) );
				$objPHPExcel->getActiveSheet()->SetCellValue('L' . $i , @$month_short_arr[(int)date('m',strtotime(@$value['first_period']))]." ".substr((date('Y',strtotime(@$value['first_period']))+543),2,2) );
			$objPHPExcel->getActiveSheet()->SetCellValue('M' . $i , @$month_short_arr[(int)date('m',strtotime(@$value['last_period']))]." ".substr((date('Y',strtotime(@$value['last_period']))+543),2,2) );
				$objPHPExcel->getActiveSheet()->SetCellValue('S' . $i , $value['loan_reason'] );
				$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':S'.$i)->applyFromArray($textStyleArray);
				$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':S'.$i)->applyFromArray($borderTop);
				
				foreach(range('A','S') as $columnID) {
					if(!in_array($columnID, array('E','F','G'))){
						$objPHPExcel->getActiveSheet()->getStyle($columnID.$i)->applyFromArray($borderLeft);
						$objPHPExcel->getActiveSheet()->getStyle($columnID.$i)->applyFromArray($borderRight);
					}
				}
				$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':D'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$objPHPExcel->getActiveSheet()->getStyle('H'.$i.':I'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$objPHPExcel->getActiveSheet()->getStyle('J'.$i.':K'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
				$objPHPExcel->getActiveSheet()->getStyle('L'.$i.':O'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$objPHPExcel->getActiveSheet()->getStyle('Q'.$i.':R'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				
				foreach(@$value['loan_guarantee'] as $key2 => $value2){
					if(@$value2['guarantee_type'] == '1'){
						
						$loan_guarantee = array();
						$this->db->select(array('guarantee_person_id','prename_short','firstname_th','lastname_th','level','guarantee_person_contract_number'));
						$this->db->from('guarantee_person_view');
						$this->db->where("loan_id = '".@$value['loan_id']."'");
						$this->db->order_by('id ASC');
						$rs_guarantee = $this->db->get()->result_array();					
						if(!empty($rs_guarantee_person)){
							foreach(@$rs_guarantee_person as $key => $row_guarantee_person){	
								$objPHPExcel->getActiveSheet()->SetCellValue('N' . $i , @$row_guarantee_person['guarantee_person_contract_number'] );
								$objPHPExcel->getActiveSheet()->SetCellValue('O' . $i , $this->center_function->mydate2date(@$value['createdatetime']) );
								$objPHPExcel->getActiveSheet()->SetCellValue('P' . $i , @$row_guarantee_person['prename_short'].@$row_guarantee_person['firstname_th']." ".@$row_guarantee_person['lastname_th'] );
								$objPHPExcel->getActiveSheet()->SetCellValue('Q' . $i , @$row_guarantee_person['guarantee_person_id'] );
								$objPHPExcel->getActiveSheet()->SetCellValue('R' . $i , @$mem_group_arr[@$row_guarantee_person['level']] );
								
								
								$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':S'.$i)->applyFromArray($textStyleArray);
								foreach(range('A','S') as $columnID) {
									if(!in_array($columnID, array('E','F','G'))){
										$objPHPExcel->getActiveSheet()->getStyle($columnID.$i)->applyFromArray($borderLeft);
										$objPHPExcel->getActiveSheet()->getStyle($columnID.$i)->applyFromArray($borderRight);
									}
								}
								$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':D'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
								$objPHPExcel->getActiveSheet()->getStyle('H'.$i.':I'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
								$objPHPExcel->getActiveSheet()->getStyle('J'.$i.':K'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
								$objPHPExcel->getActiveSheet()->getStyle('L'.$i.':O'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
								$objPHPExcel->getActiveSheet()->getStyle('Q'.$i.':R'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
								$i++;
							}
						}
						$i--;
					}else if(@$value2['guarantee_type'] == '2'){
						$i++;
						if(@$value2['other_price']==''){
							$objPHPExcel->getActiveSheet()->SetCellValue('P' . $i , 'ใช้หุ้นค้ำประกัน' );
						}else{
							$objPHPExcel->getActiveSheet()->SetCellValue('P' . $i , 'ใช้หุ้น+กองทุนฯส่วนของพนักงานค้ำประกัน' );
						}
					}
				}
				
				$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':S'.$i)->applyFromArray($textStyleArray);
				$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':S'.$i)->applyFromArray($borderBottom);
				foreach(range('A','S') as $columnID) {
					if(!in_array($columnID, array('E','F','G'))){
						$objPHPExcel->getActiveSheet()->getStyle($columnID.$i)->applyFromArray($borderLeft);
						$objPHPExcel->getActiveSheet()->getStyle($columnID.$i)->applyFromArray($borderRight);
					}
				}
				$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':D'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$objPHPExcel->getActiveSheet()->getStyle('H'.$i.':I'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$objPHPExcel->getActiveSheet()->getStyle('J'.$i.':K'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
				$objPHPExcel->getActiveSheet()->getStyle('L'.$i.':O'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$objPHPExcel->getActiveSheet()->getStyle('Q'.$i.':R'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				
				$count_loan++;
			}
		}
		$i+=2;
		$objPHPExcel->getActiveSheet()->mergeCells('D'.$i.':G'.($i));	
		$objPHPExcel->getActiveSheet()->SetCellValue('D' . $i , "เดือน ".$month_arr[$m] );
		$objPHPExcel->getActiveSheet()->SetCellValue('H' . $i , "รวม " );
		$objPHPExcel->getActiveSheet()->SetCellValue('I' . $i , number_format($count_loan) );
		$objPHPExcel->getActiveSheet()->SetCellValue('J' . $i , "สัญญา " );
		$objPHPExcel->getActiveSheet()->mergeCells('K'.$i.':L'.($i));
		$objPHPExcel->getActiveSheet()->SetCellValue('K' . $i , "เป็นเงินจำนวน " );
		$objPHPExcel->getActiveSheet()->SetCellValue('M' . $i , number_format($loan_amount) );
		$objPHPExcel->getActiveSheet()->SetCellValue('N' . $i , "บาท " );
		$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':S'.$i)->applyFromArray($footerStyle);
	//$objPHPExcel->getActiveSheet()->getStyle('H'.$i.':I'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
		
	//}
	$objPHPExcel->getActiveSheet()->setTitle($month_short_arr[$m].substr(($year+543),2,2));
	$sheet++;
}

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="รายงานเงินกู้แยกประเภท_'.$loan_type[$_GET['loan_type']].'_'.$file_name_text.'.xlsx"');
header('Cache-Control: max-age=0');
		
$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
$objWriter->save('php://output');
exit;	
?>