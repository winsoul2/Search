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
if(@$_GET['report_date'] != ''){
	$date_arr = explode('/',@$_GET['report_date']);
	$day = (int)$date_arr[0];
	$month = (int)$date_arr[1];
	$year = (int)$date_arr[2];
	$year -= 543;
	$file_name_text = $day."_".$month_arr[$month]."_".($year+543);
}else{
	if(@$_GET['month']!='' && @$_GET['year']!=''){
		$day = '';
		$month = $_GET['month'];
		$year = ($_GET['year']-543);
		$file_name_text = $month_arr[$month]."_".($year+543);
	}else{
		$day = '';
		$month = '';
		$year = (@$_GET['year']-543);
		$file_name_text = ($year+543);
	}
}

if($month!=''){
	$month_start = $month;
	$month_end = $month;
}else{
	$month_start = 1;
	$month_end = 12;
}
$sheet = 0;

for($m = $month_start; $m <= $month_end; $m++){
	$i=0;
		$this->db->select(array('t2.member_id'));
		$this->db->from('coop_mem_req_resign as t1');
		$this->db->join('coop_mem_apply as t2','t1.member_id = t2.member_id','inner');
		$this->db->where("t1.req_resign_status = '1' AND t1.resign_date LIKE '".$year.'-'.sprintf("%02d",$m)."%'");
		$rs_check = $this->db->get()->result_array();
		$row_check = @$rs_check[0];
		//print_r($this->db->last_query()); exit;
		if(@$row_check['member_id']=='' && @$_GET['report_date']=='' && @$_GET['month']!=''){
			$i+=1;
			$objPHPExcel->getActiveSheet()->mergeCells('A'.$i.':I'.$i);
			$objPHPExcel->getActiveSheet()->SetCellValue('A'.$i, " เดือน ".@$month_arr[$m]." ปี ".(@$year+543)." สมาชิกลาออกจากสหกรณ์  จำนวน   0  ราย " ) ;
			$objPHPExcel->getActiveSheet()->getStyle('A'.$i)->applyFromArray($titleStyle);
			$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':I'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			
			$i+=1;
			$i_top = $i;
			$objPHPExcel->getActiveSheet()->SetCellValue('A' . $i , "ลำดับ" ) ; 
			$objPHPExcel->getActiveSheet()->SetCellValue('B' . $i, "เลขทะเบียน" ) ;
			$objPHPExcel->getActiveSheet()->SetCellValue('C' . $i, "ชื่อ - สกุล" ) ;
			$objPHPExcel->getActiveSheet()->mergeCells('C'.$i.':E'.($i+1));		
			$objPHPExcel->getActiveSheet()->SetCellValue('D' . $i, "หน่วยงาน" ) ;
			$objPHPExcel->getActiveSheet()->mergeCells('D'.$i.':F'.($i+1));		
			$objPHPExcel->getActiveSheet()->SetCellValue('G' . $i, "เงินค่าหุ้น" ) ;
			$objPHPExcel->getActiveSheet()->SetCellValue('H' . $i, "เงินค้างชำระ" ) ;
			$objPHPExcel->getActiveSheet()->mergeCells('H'.$i.':H'.($i+1));		
			$objPHPExcel->getActiveSheet()->SetCellValue('I' . $i, "เหตุผลในการลาออก" ) ;
			$objPHPExcel->getActiveSheet()->mergeCells('I'.$i.':I'.($i+1));	
			
			$i+=1;
			$i_bottom = $i;
			$objPHPExcel->getActiveSheet()->SetCellValue('A' . $i , "ที่" ) ; 
			$objPHPExcel->getActiveSheet()->SetCellValue('B' . $i, "สมาชิก" ) ;
			$objPHPExcel->getActiveSheet()->SetCellValue('G' . $i, "สะสม(บาท)" ) ;
			
			$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(6);
			$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(11);
			$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(3.68);
			$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(8.86);
			$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(9.43);
			$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(42.68);
			$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(13.29);
			$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(12.29);
			$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(18.71);
			
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
			continue;
		}else if(@$row_check['member_id']=='' && @$_GET['report_date']=='' && @$_GET['month']==''){
			continue;
		}
	$objPHPExcel->createSheet($sheet);
	$objPHPExcel->setActiveSheetIndex($sheet);
	$objPHPExcel->getActiveSheet()->setTitle($month_short_arr[$m].substr(($year+543),2,2));
	$sheet++;
	
	if($day!=''){
		$day_start = $day;
		$day_end = $day;
	}else{
		$day_start = '1';
		$day_end = date('t',strtotime($year."-".sprintf("%02d",$m)."-01"));
	}

		$this->db->select(array('t2.member_id'));
		$this->db->from('coop_mem_req_resign as t1');
		$this->db->join('coop_mem_apply as t2','t1.member_id = t2.member_id','inner');
		$this->db->where("t1.req_resign_status = '1' AND t1.resign_date LIKE '".$year.'-'.sprintf("%02d",$m)."%'");
		$rs_check = $this->db->get()->result_array();
		$check_num = count($rs_check);
		$i+=1;
		$objPHPExcel->getActiveSheet()->mergeCells('A'.$i.':J'.$i);
		$objPHPExcel->getActiveSheet()->SetCellValue('A'.$i, " เดือน ".$month_arr[$m]." ปี ".($year+543)." สมาชิกลาออกจากสหกรณ์  จำนวน   ".$check_num."   ราย ดังนี้" ) ;
		$objPHPExcel->getActiveSheet()->getStyle('A'.$i)->applyFromArray($titleStyle);
		$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':J'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		
		$i+=1;
		$i_top = $i;
		$objPHPExcel->getActiveSheet()->SetCellValue('A' . $i , "ลำดับ" ) ; 
		$objPHPExcel->getActiveSheet()->SetCellValue('B' . $i , "วันที่ลาออก" ) ; 
		$objPHPExcel->getActiveSheet()->SetCellValue('C' . $i, "เลขทะเบียน" ) ;
		$objPHPExcel->getActiveSheet()->SetCellValue('D' . $i, "ชื่อ - สกุล" ) ;
		$objPHPExcel->getActiveSheet()->mergeCells('D'.$i.':F'.($i+1));		
		$objPHPExcel->getActiveSheet()->SetCellValue('G' . $i, "หน่วยงาน" ) ;
		$objPHPExcel->getActiveSheet()->mergeCells('G'.$i.':G'.($i+1));		
		$objPHPExcel->getActiveSheet()->SetCellValue('H' . $i, "เงินค่าหุ้น" ) ;
		$objPHPExcel->getActiveSheet()->SetCellValue('I' . $i, "เงินค้างชำระ" ) ;
		$objPHPExcel->getActiveSheet()->mergeCells('I'.$i.':I'.($i+1));		
		$objPHPExcel->getActiveSheet()->SetCellValue('J' . $i, "เหตุผลในการลาออก" ) ;
		$objPHPExcel->getActiveSheet()->mergeCells('J'.$i.':J'.($i+1));	
		
		$i+=1;
		$i_bottom = $i;
		$objPHPExcel->getActiveSheet()->SetCellValue('A' . $i , "ที่" ) ; 
		$objPHPExcel->getActiveSheet()->SetCellValue('B' . $i, "สมาชิก" ) ;
		$objPHPExcel->getActiveSheet()->SetCellValue('F' . $i, "สะสม(บาท)" ) ;
		
		$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(6);
		$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(8.86);
		$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(11);
		$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(4.68);
		$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(8.86);
		$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(9.43);
		$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(42.68);
		$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(13.29);
		$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(12.29);
		$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(20);
		
		foreach(range('A','J') as $columnID) {
			$objPHPExcel->getActiveSheet()->getStyle($columnID.$i_top)->applyFromArray($borderTop);
			$objPHPExcel->getActiveSheet()->getStyle($columnID.$i_top)->applyFromArray($borderLeft);
			$objPHPExcel->getActiveSheet()->getStyle($columnID.$i_top)->applyFromArray($borderRight);
			$objPHPExcel->getActiveSheet()->getStyle($columnID.$i_bottom)->applyFromArray($borderLeft);
			$objPHPExcel->getActiveSheet()->getStyle($columnID.$i_bottom)->applyFromArray($borderRight);
			$objPHPExcel->getActiveSheet()->getStyle($columnID.$i_bottom)->applyFromArray($borderBottom);
			
			$objPHPExcel->getActiveSheet()->getStyle($columnID.$i_top)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle($columnID.$i_bottom)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		}
		$objPHPExcel->getActiveSheet()->getStyle('A'.$i_top.':J'.$i_bottom)->applyFromArray($headerStyle);
		$objPHPExcel->getActiveSheet()->getStyle('A'.$i_top.':J'.$i_bottom)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

		$j=1;
		$share_sum = 0;
		$loan_sum = 0;
		for($d = $day_start; $d <= $day_end; $d++){
			$this->db->select(array('t2.member_id'));
			$this->db->from('coop_mem_req_resign as t1');
			$this->db->join('coop_mem_apply as t2','t1.member_id = t2.member_id','inner');
			$this->db->where("t1.req_resign_status = '1' AND t1.resign_date LIKE '".$year.'-'.sprintf("%02d",$m).'-'.sprintf("%02d",$d)."%'");
			$rs_check = $this->db->get()->result_array();
			
			$check_num = 0;
			if(!empty($rs_check)){
				foreach($rs_check as $key => $row_check){
					$check_num++;
				}
			}	
			if(@$check_num == 0  && @$_GET['report_date']==''){
				continue;
			}

		$this->db->select(array('t1.resign_date','t2.member_id','t2.employee_id','t3.prename_short','t2.firstname_th','t2.lastname_th','t2.level','t4.resign_cause_name'));
		$this->db->from('coop_mem_req_resign as t1');
		$this->db->join('coop_mem_apply as t2','t1.member_id = t2.member_id','inner');
		$this->db->join('coop_prename as t3','t2.prename_id = t3.prename_id','left');
		$this->db->join('coop_mem_resign_cause as t4','t1.resign_cause_id = t4.resign_cause_id','left');
		$this->db->where("t1.req_resign_status = '1' AND resign_date LIKE '".$year.'-'.sprintf("%02d",$m).'-'.sprintf("%02d",$d)."%'");
		$rs = $this->db->get()->result_array();

		if(!empty($rs)){
			foreach($rs as $key => $row){
				$share_num = 0;
				$this->db->select(array('share_collect'));
				$this->db->from('coop_mem_share');
				$this->db->where("member_id = '".$row['member_id']."' AND share_status IN('1','2')");
				$this->db->order_by('share_id DESC');
				$this->db->limit(1);
				$rs_share = $this->db->get()->result_array();
				$row_share  = @$rs_share[0];
				$share_num = @$row_share['share_collect']*@$share_value;
				$share_sum += @$share_num;
		
				$loan_num = 0;
				$this->db->select(array('loan_amount_balance'));
				$this->db->from('coop_loan');
				$this->db->where("member_id = '".$row['member_id']."' AND loan_status = '1'");
				$rs_loan = $this->db->get()->result_array();
		
				if(!empty($rs_loan)){
					foreach($rs_loan as $key => $row_loan){
						$loan_num += @$row_loan['loan_amount_balance'];
					}
				}
				$loan_sum += $loan_num;
				
				$i+=1;
				$objPHPExcel->getActiveSheet()->SetCellValue('A' . $i , $j++ );
				$objPHPExcel->getActiveSheet()->SetCellValue('B' . $i , $row['resign_date'] );
				$objPHPExcel->getActiveSheet()->SetCellValue('C' . $i , @$row['member_id']." " );
				$objPHPExcel->getActiveSheet()->SetCellValue('D' . $i , @$row['prename_short'] );
				$objPHPExcel->getActiveSheet()->SetCellValue('E' . $i , @$row['firstname_th'] );
				$objPHPExcel->getActiveSheet()->SetCellValue('F' . $i , @$row['lastname_th'] );
				$objPHPExcel->getActiveSheet()->SetCellValue('G' . $i , @$mem_group_arr[@$row['level']] );
				$objPHPExcel->getActiveSheet()->SetCellValue('H' . $i , number_format(@$share_num,2) );
				$objPHPExcel->getActiveSheet()->SetCellValue('I' . $i , number_format(@$loan_num,2) );
				$objPHPExcel->getActiveSheet()->SetCellValue('J' . $i , @$row['resign_cause_name'] );
				
				$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':B'.$i)->applyFromArray($styleArray);
				$objPHPExcel->getActiveSheet()->getStyle('C'.$i.':E'.$i)->applyFromArray($textStyleArray);
				$objPHPExcel->getActiveSheet()->getStyle('C'.$i.':E'.$i)->applyFromArray($borderBottom);
				$objPHPExcel->getActiveSheet()->getStyle('F'.$i.':J'.$i)->applyFromArray($styleArray);
				$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':C'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$objPHPExcel->getActiveSheet()->getStyle('F'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$objPHPExcel->getActiveSheet()->getStyle('G'.$i.':J'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
				// $objPHPExcel->getActiveSheet()->getStyle('I'.$i.':J'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
			}
		}

	}
	$i+=1;
	$objPHPExcel->getActiveSheet()->SetCellValue('H' . $i , number_format($share_sum,2) );
	$objPHPExcel->getActiveSheet()->SetCellValue('I' . $i , number_format($loan_sum,2) );
	$objPHPExcel->getActiveSheet()->getStyle('H'.$i.':I'.$i)->applyFromArray($sumStyleArray);
	$objPHPExcel->getActiveSheet()->getStyle('H'.$i.':I'.$i)->applyFromArray($borderBottom);
	$objPHPExcel->getActiveSheet()->getStyle('H'.$i.':I'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
	
	$i+=2;
}

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="รายงานสรุปการลาออกจากสหกรณ์_'.$file_name_text.'.xlsx"');
header('Cache-Control: max-age=0');

$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
$objWriter->save('php://output');
exit;	
?>