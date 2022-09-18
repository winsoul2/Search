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

$where = '';
// if($day != '' && $month != ''){		
// 	// $s_date = $year.'-'.sprintf("%02d",@$month).'-'.sprintf("%02d",@$day).' 00:00:00.000';
// 	// $e_date = $year.'-'.sprintf("%02d",@$month).'-'.sprintf("%02d",@$day).' 23:59:59.000';
// 	$where .= " AND create_date BETWEEN '".$start_date."' AND '".$end_date."'";
// }else if($day == '' && $month != ''){
// 	// $s_date = $year.'-'.sprintf("%02d",@$month).'-01'.' 00:00:00.000';
// 	// $e_date = date('Y-m-t',strtotime($s_date)).' 23:59:59.000';
// 	$where .= " AND create_date BETWEEN '".$start_date."' AND '".$start_date."'";
// }else{
// 	$where .= " AND create_date BETWEEN '".$year."-01-01 00:00:00.000' AND '".$year."-12-31 23:59:59.000' ";
// }
$where .= " AND create_date BETWEEN '".$start_date." 00:00:00.000' AND '".$end_date." 23:59:59.000'";
$this->db->select(array('t1.member_id',
						'employee_id',
						'prename_short',
						'firstname_th',
						'lastname_th',
						't1.change_value_price',
						't1.change_share_id',
						'salary',
						'create_date',
						't1.faction'
						));
$this->db->from('coop_change_share_report as t1');
$this->db->where("change_share_status IN ('1', '2') {$where}");
$this->db->order_by('change_share_id DESC');
$rs = $this->db->get()->result_array();

// $month_array = array();
$year_array = array();
$data = array();
$data2 = array();
if(!empty($rs)){
	foreach(@$rs as $key => $row){
		$createdatetime = explode(' ',@$row['create_date']);
		$createdate = explode('-',@$createdatetime[0]);
		$create_month = (int)@$createdate[1];
		$create_year = (int)@$createdate[0];
		$prev_change_shares = $this->db->select("*")
										->from("coop_change_share")
										->where("member_id = '".$row["member_id"]."' AND create_date < '".$row["create_date"]."' AND change_share_status IN ('1', '2')")
										->order_by("create_date DESC")
										->get()->result_array();

		if(!empty($prev_change_shares)) $row['prev_change_share'] = $prev_change_shares[0]["change_value_price"];

		if(@$row['prev_change_share'] == ''){
			$prev_shares = $this->db->select("*")
									->from("coop_mem_share")
									->where("member_id = '".$row['member_id']."' AND share_type = 'SPM' AND share_date < '".$row['create_date']."'")
									->order_by("share_date DESC")
									->get()->result_array();
			$prev_share = $prev_shares[0]["share_early_value"];
		}else{
			$prev_share = @$row['prev_change_share'];
		}
		if($prev_share < $row['change_value_price']) {
			$row["prev_share"] = $prev_share;
			$data[$create_year][@$create_month][$row['change_share_id']] = $row;
		} else {
			$row["prev_share"] = $prev_share;
			$data2[$create_year][@$create_month][$row['change_share_id']] = $row;
		}
		// if(!in_array($create_month, $month_array)) $month_array[] = $create_month;
		if(!array_key_exists($create_year, $year_array)) {
			$year_array[$create_year][] = $create_month;
		} else if (!in_array($create_month, $year_array[$create_year])) {
			$year_array[$create_year][] = $create_month;
		}
	}
}

/////////////////////////////////////////////////////////////////////////////////////
// $where = '';
// if($day != '' && $month != ''){
// 	$where .= " AND share_date LIKE '".$year.'-'.sprintf("%02d",$month).'-'.sprintf("%02d",$day)."%'";
// }else if($day == '' && $month != ''){
// 	$where .= " AND share_date LIKE '".$year.'-'.sprintf("%02d",$month)."%'";
// }else{
// 	$where .= " AND share_date LIKE '".$year."%'";
// }
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
$this->db->order_by('share_date ASC');
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
		$objPHPExcel->getActiveSheet()->mergeCells('A'.$i.':J'.$i);
		$objPHPExcel->getActiveSheet()->SetCellValue('A'.$i, "4.2 เรื่องสมาชิกเปลี่ยนแปลงอัตราส่งเงินค่าหุ้นรายเดือน" ) ;
		$objPHPExcel->getActiveSheet()->getStyle('A'.$i)->applyFromArray($titleStyle);
		
		$i+=1;
		$i_title = $i;
		$objPHPExcel->getActiveSheet()->mergeCells('A'.$i.':J'.$i);
		$objPHPExcel->getActiveSheet()->getStyle('A'.$i)->applyFromArray($titleStyle);
		$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':J'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		
		$i+=1;
		$i_top = $i;
		$objPHPExcel->getActiveSheet()->SetCellValue('A' . $i , "ลำดับ" ) ; 
		$objPHPExcel->getActiveSheet()->SetCellValue('B' . $i , "เลขทะเบียน" ) ; 
		$objPHPExcel->getActiveSheet()->SetCellValue('C' . $i , "รหัส" ) ; 
		$objPHPExcel->getActiveSheet()->mergeCells('D'.$i.':F'.($i+1));
		$objPHPExcel->getActiveSheet()->SetCellValue('D' . $i , "ชื่อ - สกุล" ) ;
		$objPHPExcel->getActiveSheet()->mergeCells('G'.$i.':G'.($i+1));
		$objPHPExcel->getActiveSheet()->SetCellValue('G' . $i , "หน่วยงาน" ) ; 
		$objPHPExcel->getActiveSheet()->SetCellValue('H' . $i , "ค่างวดหุ้น" ) ;
		$objPHPExcel->getActiveSheet()->SetCellValue('I' . $i , "ค่างวดหุ้น" ) ; 
		$objPHPExcel->getActiveSheet()->SetCellValue('J' . $i , "ค่างวดหุ้น" ) ;
		
		$i+=1;
		$i_bottom = $i;
		$objPHPExcel->getActiveSheet()->SetCellValue('A' . $i , "ที่" ) ; 
		$objPHPExcel->getActiveSheet()->SetCellValue('B' . $i , "สมาชิก" ) ;
		$objPHPExcel->getActiveSheet()->SetCellValue('C' . $i , "พนักงาน" ) ;
		$objPHPExcel->getActiveSheet()->SetCellValue('H' . $i , "เดิม" ) ;
		$objPHPExcel->getActiveSheet()->SetCellValue('I' . $i , "ใหม่" ) ;
        $objPHPExcel->getActiveSheet()->SetCellValue('J' . $i , "ที่เพิ่ม" ) ;
		
		$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(4.43);
		$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(11.14);
		$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(8.14);
		$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(4.71);
		$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(10.43);
		$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(9.86);
		$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(7.71);
		$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(9.86);
		$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(8.71);
		$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(9.86);
		
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
		$j = 1;
		$share1 = 0;
		$share2 = 0;
		$share3 = 0;
		$count = 0;
		if(!empty($data[$year][$m])){
			foreach($data[$year][$m] as $key => $row){
				$loan_guarantee = array();
				$prev_share = $row['prev_share'];
				
				$i+=1;
				$objPHPExcel->getActiveSheet()->SetCellValue('A' . $i , $j++);
				$objPHPExcel->getActiveSheet()->SetCellValue('B' . $i , @$row['member_id']." " );
				$objPHPExcel->getActiveSheet()->SetCellValue('C' . $i , @$row['employee_id']." " );
				$objPHPExcel->getActiveSheet()->SetCellValue('D' . $i , @$row['prename_short'] );
				$objPHPExcel->getActiveSheet()->SetCellValue('E' . $i , @$row['firstname_th'] );
				$objPHPExcel->getActiveSheet()->SetCellValue('F' . $i , @$row['lastname_th'] );
				$objPHPExcel->getActiveSheet()->SetCellValue('G' . $i , @$mem_group_arr[@$row['faction']]);
				$objPHPExcel->getActiveSheet()->SetCellValue('H' . $i , number_format($prev_share,2) );

				$objPHPExcel->getActiveSheet()->SetCellValue('I' . $i , number_format(@$row['change_value_price'],2) );
                $objPHPExcel->getActiveSheet()->SetCellValue('J' . $i , number_format((@$row['change_value_price']-@$prev_share),2) );
				
				$share1 += $prev_share;
				$share2 += (@$row['change_value_price']-@$prev_share);
				$share3 += @$row['change_value_price'];
				
				$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':J'.$i)->applyFromArray($textStyleArray);
				$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':J'.$i)->applyFromArray($borderTop);
				
				foreach(range('A','J') as $columnID) {
					if(!in_array($columnID, array('D','E','F'))){
						$objPHPExcel->getActiveSheet()->getStyle($columnID.$i)->applyFromArray($borderLeft);
						$objPHPExcel->getActiveSheet()->getStyle($columnID.$i)->applyFromArray($borderRight);
					}
					$objPHPExcel->getActiveSheet()->getStyle($columnID.$i)->applyFromArray($borderBottom);
				}
				$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':C'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$objPHPExcel->getActiveSheet()->getStyle('G'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$objPHPExcel->getActiveSheet()->getStyle('H'.$i.':J'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
				$count++;
			}
		}
		$objPHPExcel->getActiveSheet()->SetCellValue('A'.$i_title, "ในระหว่างเดือน ".$month_arr[$m]." ".($year+543)." สมาชิกสหกรณ์ฯขอเปลี่ยนแปลงอัตราค่าหุ้นเพิ่มขึ้น  จำนวน  ".$count." ราย ดังนี้" ) ;
		
		$i+=1;
		$objPHPExcel->getActiveSheet()->SetCellValue('H' . $i , number_format($share1,2) );
		$objPHPExcel->getActiveSheet()->SetCellValue('I' . $i , number_format($share2,2) );
		$objPHPExcel->getActiveSheet()->SetCellValue('J' . $i , number_format($share3,2) );
		$objPHPExcel->getActiveSheet()->getStyle('H'.$i.':J'.$i)->applyFromArray($borderBottom);
		$objPHPExcel->getActiveSheet()->getStyle('H'.$i.':J'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
		
//////////////////////////////////////////////////////////////////////////////		
		$i+=2;
		$i_title = $i;
		$objPHPExcel->getActiveSheet()->mergeCells('A'.$i.':J'.$i);
		$objPHPExcel->getActiveSheet()->getStyle('A'.$i)->applyFromArray($titleStyle);
		$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':J'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		
		$i+=1;
		$i_top = $i;
		$objPHPExcel->getActiveSheet()->SetCellValue('A' . $i , "ลำดับ" ) ; 
		$objPHPExcel->getActiveSheet()->SetCellValue('B' . $i , "เลขทะเบียน" ) ; 
		$objPHPExcel->getActiveSheet()->SetCellValue('C' . $i , "รหัส" ) ; 
		$objPHPExcel->getActiveSheet()->mergeCells('D'.$i.':F'.($i+1));
		$objPHPExcel->getActiveSheet()->SetCellValue('D' . $i , "ชื่อ - สกุล" ) ;
		$objPHPExcel->getActiveSheet()->mergeCells('G'.$i.':G'.($i+1));
		$objPHPExcel->getActiveSheet()->SetCellValue('G' . $i , "หน่วยงาน" ) ; 
		$objPHPExcel->getActiveSheet()->SetCellValue('H' . $i , "ค่างวดหุ้น" ) ;
		$objPHPExcel->getActiveSheet()->SetCellValue('I' . $i , "ค่างวดหุ้น" ) ; 
		$objPHPExcel->getActiveSheet()->SetCellValue('J' . $i , "ค่างวดหุ้น" ) ;
		
		$i+=1;
		$i_bottom = $i;
		$objPHPExcel->getActiveSheet()->SetCellValue('A' . $i , "ที่" ) ; 
		$objPHPExcel->getActiveSheet()->SetCellValue('B' . $i , "สมาชิก" ) ;
		$objPHPExcel->getActiveSheet()->SetCellValue('C' . $i , "พนักงาน" ) ;
		$objPHPExcel->getActiveSheet()->SetCellValue('H' . $i , "เดิม" ) ;

		$objPHPExcel->getActiveSheet()->SetCellValue('I' . $i , "ใหม่" ) ;
        $objPHPExcel->getActiveSheet()->SetCellValue('J' . $i , "ที่ลด" ) ;
		
		$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(4.43);
		$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(11.14);
		$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(11.14);
		$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(4.71);
		$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(10.43);
		$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(11.14);
		$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(30);
		$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(9.86);
		$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(8.71);
		$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(9.86);
		
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
		$j = 1;
		$share1 = 0;
		$share2 = 0;
		$share3 = 0;
		$count = 0;
		if(!empty($data2[$year][$m])){
			foreach($data2[$year][$m] as $key => $row){
				$prev_share = $row['prev_share'];
				
				$i+=1;
				$objPHPExcel->getActiveSheet()->SetCellValue('A' . $i , $j++);
				$objPHPExcel->getActiveSheet()->SetCellValue('B' . $i , @$row['member_id']." " );
				$objPHPExcel->getActiveSheet()->SetCellValue('C' . $i , @$row['employee_id']." " );
				$objPHPExcel->getActiveSheet()->SetCellValue('D' . $i , @$row['prename_short'] );
				$objPHPExcel->getActiveSheet()->SetCellValue('E' . $i , @$row['firstname_th'] );
				$objPHPExcel->getActiveSheet()->SetCellValue('F' . $i , @$row['lastname_th'] );
				$objPHPExcel->getActiveSheet()->SetCellValue('G' . $i , @$mem_group_arr[@$row['faction']] );
				$objPHPExcel->getActiveSheet()->SetCellValue('H' . $i , number_format($prev_share,2) );

				$objPHPExcel->getActiveSheet()->SetCellValue('I' . $i , number_format(@$row['change_value_price'],2) );
                $objPHPExcel->getActiveSheet()->SetCellValue('J' . $i , number_format((@$prev_share-@$row['change_value_price']),2) );
				
				$share1 += @$prev_share;
				$share2 += (@$prev_share-@$row['change_value_price']);
				$share3 += @$row['change_value_price'];
				
				$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':J'.$i)->applyFromArray($textStyleArray);
				$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':J'.$i)->applyFromArray($borderTop);
				
				foreach(range('A','J') as $columnID) {
					if(!in_array($columnID, array('D','E','F'))){
						$objPHPExcel->getActiveSheet()->getStyle($columnID.$i)->applyFromArray($borderLeft);
						$objPHPExcel->getActiveSheet()->getStyle($columnID.$i)->applyFromArray($borderRight);
					}
					$objPHPExcel->getActiveSheet()->getStyle($columnID.$i)->applyFromArray($borderBottom);
				}
				$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':C'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$objPHPExcel->getActiveSheet()->getStyle('G'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$objPHPExcel->getActiveSheet()->getStyle('H'.$i.':J'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
				$count++;
			}
		}
		$objPHPExcel->getActiveSheet()->SetCellValue('A'.$i_title, "ในระหว่างเดือน ".$month_arr[$m]."  ".($year+543)." สมาชิกสหกรณ์ฯขอเปลี่ยนแปลงอัตราค่าหุ้นลดลง  จำนวน ".$count." ราย ดังนี้" ) ;
		$i+=1;
		$objPHPExcel->getActiveSheet()->SetCellValue('H' . $i , number_format($share1,2) );
		$objPHPExcel->getActiveSheet()->SetCellValue('I' . $i , number_format($share2,2) );
		$objPHPExcel->getActiveSheet()->SetCellValue('J' . $i , number_format($share3,2) );
		$objPHPExcel->getActiveSheet()->getStyle('H'.$i.':J'.$i)->applyFromArray($borderBottom);
		$objPHPExcel->getActiveSheet()->getStyle('H'.$i.':J'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
		

		

	//}
	$objPHPExcel->getActiveSheet()->setTitle($month_short_arr[$m].substr(($year+543),2,2));
	$sheet++;
	}
}


header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="รายงานสมาชิกเปลี่ยนแปลงค่าหุ้น_'.$file_name_text.'.xlsx"');
header('Cache-Control: max-age=0');
		
$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
$objWriter->save('php://output');
exit;	
?>