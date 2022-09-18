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
		$month = @$_GET['month'];
		$year = (@$_GET['year']-543);
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

$param = '';
if(!empty($_GET)){
	foreach($_GET AS $key=>$val){
		$param .= $key.'='.$val.'&';
	}
}

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

$title_date = $start_date != $end_date ? "ระหว่างวันที่ ".$this->center_function->ConvertToThaiDate($start_date)." ถึง วันที่ ".$this->center_function->ConvertToThaiDate($end_date) : "วันที่ ".$this->center_function->ConvertToThaiDate($start_date);

// $month_array = array();
$year_array = array();

$where = " AND share_date BETWEEN '".$start_date." 00:00:00.000' AND '".$end_date." 23:59:59.000'";

$this->db->select(array('share_id',
						'member_id',
						'employee_id',
						'prename_short',
						'firstname_th',
						'lastname_th',
						'share_early_value',
						'share_date',
						'faction'
						));
$this->db->from('coop_mem_share_report');
$this->db->where("share_status IN ('1', '2') AND share_type = 'SPA' {$where}");
$this->db->order_by('share_date DESC');
$rs = $this->db->get()->result_array();
$data3 = array();
if(!empty($rs)){
	foreach(@$rs as $key => $row){
		$createdatetime = explode(' ',@$row['share_date']);
		$createdate = explode('-',@$createdatetime[0]);
		$create_month = (int)@$createdate[1];
		$create_year = (int)@$createdate[0];
		$data3[$create_year][@$create_month][@$row['share_id']] = @$row;
		if(!array_key_exists($create_year, $year_array)) {
			$year_array[$create_year][] = $create_month;
		} else if (!in_array($create_month, $year_array[$create_year])) {
			$year_array[$create_year][] = $create_month;
		}
	}
}

$i=0;
foreach($year_array as $year => $month_array) {
	foreach($month_array as $m){
		$i++;
			
	//echo"<pre>";print_r($data);print_r($data2);print_r($data3);
	//exit;
// for($m = $month_start; $m <= $month_end; $m++){
	$i=0;
	$objPHPExcel->createSheet($sheet);
	$objPHPExcel->setActiveSheetIndex($sheet);
		$i+=1;
		$objPHPExcel->getActiveSheet()->mergeCells('A'.$i.':H'.$i);
		$objPHPExcel->getActiveSheet()->SetCellValue('A'.$i, "4.2 เรื่องสมาชิกขอซื้อหุ้นพิเศษรายเดือน" ) ;
		$objPHPExcel->getActiveSheet()->getStyle('A'.$i)->applyFromArray($titleStyle);

		$i+=1;
		$i_title = $i;
		$objPHPExcel->getActiveSheet()->mergeCells('A'.$i.':H'.$i);
		$objPHPExcel->getActiveSheet()->getStyle('A'.$i)->applyFromArray($titleStyle);
		$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':H'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

		$i+=1;
		$i_top = $i;
		$objPHPExcel->getActiveSheet()->SetCellValue('A' . $i , "ลำดับ" ) ;
		$objPHPExcel->getActiveSheet()->SetCellValue('B' . $i , "เลขทะเบียน" ) ;
		$objPHPExcel->getActiveSheet()->SetCellValue('C' . $i , "รหัส" ) ;
		$objPHPExcel->getActiveSheet()->mergeCells('D'.$i.':F'.($i+1));
		$objPHPExcel->getActiveSheet()->SetCellValue('D' . $i , "ชื่อ - สกุล" ) ;
		$objPHPExcel->getActiveSheet()->mergeCells('G'.$i.':G'.($i+1));
		$objPHPExcel->getActiveSheet()->SetCellValue('G' . $i , "หน่วยงาน" ) ;
		$objPHPExcel->getActiveSheet()->SetCellValue('H' . $i , "จำนวนเงิน" ) ;

		$i+=1;
		$i_bottom = $i;
		$objPHPExcel->getActiveSheet()->SetCellValue('A' . $i , "ที่" ) ;
		$objPHPExcel->getActiveSheet()->SetCellValue('B' . $i , "สมาชิก" ) ;
		$objPHPExcel->getActiveSheet()->SetCellValue('C' . $i , "พนักงาน" ) ;
		$objPHPExcel->getActiveSheet()->SetCellValue('H' . $i , "(บาท)" ) ;

		$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(4.43);
		$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(11.14);
		$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(11.14);
		$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(4.71);
		$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(10.43);
		$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(11.14);
		$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(26.83);
		$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(9.86);

		foreach(range('A','H') as $columnID) {
			$objPHPExcel->getActiveSheet()->getStyle($columnID.$i_top)->applyFromArray($borderTop);
			$objPHPExcel->getActiveSheet()->getStyle($columnID.$i_top)->applyFromArray($borderLeft);
			$objPHPExcel->getActiveSheet()->getStyle($columnID.$i_top)->applyFromArray($borderRight);

			$objPHPExcel->getActiveSheet()->getStyle($columnID.$i_bottom)->applyFromArray($borderLeft);
			$objPHPExcel->getActiveSheet()->getStyle($columnID.$i_bottom)->applyFromArray($borderRight);
			$objPHPExcel->getActiveSheet()->getStyle($columnID.$i_bottom)->applyFromArray($borderBottom);

			$objPHPExcel->getActiveSheet()->getStyle($columnID.$i_top)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$objPHPExcel->getActiveSheet()->getStyle($columnID.$i_bottom)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		}

		$objPHPExcel->getActiveSheet()->getStyle('A'.$i_top.':H'.$i_bottom)->applyFromArray($headerStyle);
		$objPHPExcel->getActiveSheet()->getStyle('A'.$i_top.':H'.$i_bottom)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
		$j = 1;
		$share1 = 0;
		$share2 = 0;
		$share3 = 0;
		$count = 0;
		if(!empty($data[$year][$m])){
		}


//////////////////////////////////////////////////////////////////////////////


		
		$j = 1;
		$share1 = 0;
		$count = 0;
		if(!empty($data3[$year][$m])){
			foreach($data3[$year][$m] as $key => $row){
				$i+=1;
				$objPHPExcel->getActiveSheet()->SetCellValue('A' . $i , $j++);
				$objPHPExcel->getActiveSheet()->SetCellValue('B' . $i , @$row['member_id']." " );
				$objPHPExcel->getActiveSheet()->SetCellValue('C' . $i , @$row['employee_id']." " );
				$objPHPExcel->getActiveSheet()->SetCellValue('D' . $i , @$row['prename_short'] );
				$objPHPExcel->getActiveSheet()->SetCellValue('E' . $i , @$row['firstname_th'] );
				$objPHPExcel->getActiveSheet()->SetCellValue('F' . $i , @$row['lastname_th'] );
				$objPHPExcel->getActiveSheet()->SetCellValue('G' . $i , @$mem_group_arr[@$row['faction']] );
				$objPHPExcel->getActiveSheet()->SetCellValue('H' . $i , number_format(@$row['share_early_value'],2) );
				
				$share1 += @$row['share_early_value'];
				
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
				$objPHPExcel->getActiveSheet()->getStyle('H'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
				$count++;
			}
		}
		$objPHPExcel->getActiveSheet()->SetCellValue('A'.$i_title, "ในระหว่างเดือน ".$month_arr[$m]."  ".($year+543)."  สมาชิกสหกรณ์ฯขอซื้อหุ้นพิเศษจำนวน  จำนวน ".$count." ราย ดังนี้" ) ;
		$i+=1;
		$objPHPExcel->getActiveSheet()->SetCellValue('H' . $i , number_format($share1,2) );
		$objPHPExcel->getActiveSheet()->getStyle('H'.$i)->applyFromArray($borderBottom);
		$objPHPExcel->getActiveSheet()->getStyle('H'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
	//}
	$objPHPExcel->getActiveSheet()->setTitle($month_short_arr[$m].substr(($year+543),2,2));
	$sheet++;
	}
}
//exit;

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="เรื่องรายงานสมาชิกขอซื้อหุ้นพิเศษรายเดือน_'.$file_name_text.'.xlsx"');
header('Cache-Control: max-age=0');

$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
$objWriter->save('php://output');
exit;	
?>