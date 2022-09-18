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
foreach($datas as $year=>$months) {
	foreach($months as $m => $data) {
		$i=0;
		$objPHPExcel->createSheet($sheet);
		$objPHPExcel->setActiveSheetIndex($sheet);
		$i+=1;
		$objPHPExcel->getActiveSheet()->mergeCells('A'.$i.':I'.$i);
		$objPHPExcel->getActiveSheet()->SetCellValue('A'.$i, "5.1 เรื่องการรับสมัครสมาชิกเข้าใหม่" ) ;
		$objPHPExcel->getActiveSheet()->getStyle('A'.$i)->applyFromArray($titleStyle);
		
		$i+=1;
		$i_title = $i;
		$objPHPExcel->getActiveSheet()->mergeCells('A'.$i.':H'.$i);
		$objPHPExcel->getActiveSheet()->getStyle('A'.$i)->applyFromArray($titleStyle);
		$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':H'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        if($m != '12'){
            $next_month = sprintf("%02d",$m+1);
            $next_year = $year;
        }else{
            $next_month = '01';
            $next_year = $year+1;
        }
        $objPHPExcel->getActiveSheet()->SetCellValue('A'.$i_title, "ประจำเดือน ".$month_arr[$m]."  ".($year+543)." เริ่มหักเดือน ".$month_arr[$next_month]." ".($next_year+543) ) ;

		$i+=1;
		$i_top = $i;
		$objPHPExcel->getActiveSheet()->SetCellValue('A' . $i , "ลำดับ" ) ; 
		$objPHPExcel->getActiveSheet()->SetCellValue('B' . $i , "เลขทะเบียน" ) ; 
		$objPHPExcel->getActiveSheet()->SetCellValue('C' . $i , "รหัส" ) ; 
		$objPHPExcel->getActiveSheet()->mergeCells('D'.$i.':F'.($i+1));
		$objPHPExcel->getActiveSheet()->SetCellValue('D' . $i , "ชื่อ - สกุล" ) ;
		$objPHPExcel->getActiveSheet()->mergeCells('G'.$i.':G'.($i+1));
		$objPHPExcel->getActiveSheet()->SetCellValue('G' . $i , "หน่วยงาน" ) ; 
		$objPHPExcel->getActiveSheet()->SetCellValue('H' . $i , "ส่งเงินค่าหุ้น" ) ;
//		$objPHPExcel->getActiveSheet()->mergeCells('I'.$i.':I'.($i+1));
//		$objPHPExcel->getActiveSheet()->SetCellValue('I' . $i , "หมายเหตุ" ) ;
		
		$i+=1;
		$i_bottom = $i;
		$objPHPExcel->getActiveSheet()->SetCellValue('A' . $i , "ที่" ) ; 
		$objPHPExcel->getActiveSheet()->SetCellValue('B' . $i , "สมาชิก" ) ;
		$objPHPExcel->getActiveSheet()->SetCellValue('C' . $i , "พนักงาน" ) ;
		$objPHPExcel->getActiveSheet()->SetCellValue('H' . $i , "สะสม(บาท)" ) ;
		
		$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(3.86);
		$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(8.71);
		$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(7.71);
		$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(4.29);
		$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(10.43);
		$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(12.14);
		$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(30);
		$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(13.71);
        $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(13.71);
		
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

		$members = array();
		$j = 1;
		$share = 0;
		$count_register = 0;
		if(!empty($data['member_in'])) {
			$members = $data['member_in']['members'];
			$member_ids = array_column($members, 'member_id');
			array_multisort($member_ids, SORT_ASC, $members);

			foreach($members as $key => $row){
				$share += $row['share_month'];
				$comment_txt = '';
				if(!empty($row['re_register_check'])){
					$comment_txt = "สมัครครั้งที่ 2";
				}
				$i+=1;
				$objPHPExcel->getActiveSheet()->SetCellValue('A' . $i , $j++);
				$objPHPExcel->getActiveSheet()->SetCellValue('B' . $i , @$row['member_id']."'" );
				$objPHPExcel->getActiveSheet()->SetCellValue('C' . $i , @$row['employee_id']."'" );
				$objPHPExcel->getActiveSheet()->SetCellValue('D' . $i , @$row['prename_short'] );
				$objPHPExcel->getActiveSheet()->SetCellValue('E' . $i , @$row['firstname_th'] );
				$objPHPExcel->getActiveSheet()->SetCellValue('F' . $i , @$row['lastname_th'] );
				$objPHPExcel->getActiveSheet()->SetCellValue('G' . $i , @$mem_group_arr[@$row['faction']] );
				$objPHPExcel->getActiveSheet()->SetCellValue('H' . $i , number_format(@$row['share_month'],2) );
//				$objPHPExcel->getActiveSheet()->SetCellValue('I' . $i , @$comment_txt );
				
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
				$count_register++;
			}
		}
		$i+=1;
		$objPHPExcel->getActiveSheet()->SetCellValue('H' . $i , number_format($share,2) );
		$objPHPExcel->getActiveSheet()->getStyle('H'.$i)->applyFromArray($borderBottom);
		$objPHPExcel->getActiveSheet()->getStyle('H'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

		$i++;
        $objPHPExcel->getActiveSheet()->SetCellValue('A'.$i, "รวมสมาชิกสมัครใหม่เดือน ".$month_arr[$m]."  ".($year+543)." จำนวน  ".$count_register." ราย" ) ;
        $objPHPExcel->getActiveSheet()->mergeCells('A'.$i.':H'.$i);
		
//////////////////////////////////////////////////////////////////////////////		
//		$i+=2;
//		$i_title = $i;
//		$objPHPExcel->getActiveSheet()->mergeCells('A'.$i.':I'.$i);
//
//		$objPHPExcel->getActiveSheet()->getStyle('A'.$i)->applyFromArray($titleStyle);
//		$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':I'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		
//		$i+=1;
//		$i_top = $i;
//		$objPHPExcel->getActiveSheet()->SetCellValue('A' . $i , "ลำดับ" ) ;
//		$objPHPExcel->getActiveSheet()->SetCellValue('B' . $i , "เลขทะเบียน" ) ;
//		$objPHPExcel->getActiveSheet()->SetCellValue('C' . $i , "รหัส" ) ;
//		$objPHPExcel->getActiveSheet()->mergeCells('D'.$i.':F'.($i+1));
//		$objPHPExcel->getActiveSheet()->SetCellValue('D' . $i , "ชื่อ - สกุล" ) ;
//		$objPHPExcel->getActiveSheet()->mergeCells('G'.$i.':G'.($i+1));
//		$objPHPExcel->getActiveSheet()->SetCellValue('G' . $i , "หน่วยงาน" ) ;
//		$objPHPExcel->getActiveSheet()->SetCellValue('H' . $i , "เงินค่าหุ้น" ) ;
//		$objPHPExcel->getActiveSheet()->mergeCells('I'.$i.':I'.($i+1));
//		$objPHPExcel->getActiveSheet()->SetCellValue('I' . $i , "เงินค้างชำระ" ) ;
//
//		$i+=1;
//		$i_bottom = $i;
//		$objPHPExcel->getActiveSheet()->SetCellValue('A' . $i , "ที่" ) ;
//		$objPHPExcel->getActiveSheet()->SetCellValue('B' . $i , "สมาชิก" ) ;
//		$objPHPExcel->getActiveSheet()->SetCellValue('C' . $i , "พนักงาน" ) ;
//		$objPHPExcel->getActiveSheet()->SetCellValue('H' . $i , "สะสม(บาท)" ) ;
//
//		foreach(range('A','I') as $columnID) {
//			$objPHPExcel->getActiveSheet()->getStyle($columnID.$i_top)->applyFromArray($borderTop);
//			$objPHPExcel->getActiveSheet()->getStyle($columnID.$i_top)->applyFromArray($borderLeft);
//			$objPHPExcel->getActiveSheet()->getStyle($columnID.$i_top)->applyFromArray($borderRight);
//
//			$objPHPExcel->getActiveSheet()->getStyle($columnID.$i_bottom)->applyFromArray($borderLeft);
//			$objPHPExcel->getActiveSheet()->getStyle($columnID.$i_bottom)->applyFromArray($borderRight);
//			$objPHPExcel->getActiveSheet()->getStyle($columnID.$i_bottom)->applyFromArray($borderBottom);
//
//			$objPHPExcel->getActiveSheet()->getStyle($columnID.$i_top)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//			$objPHPExcel->getActiveSheet()->getStyle($columnID.$i_bottom)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//		}
//
//		$objPHPExcel->getActiveSheet()->getStyle('A'.$i_top.':I'.$i_bottom)->applyFromArray($headerStyle);
//		$objPHPExcel->getActiveSheet()->getStyle('A'.$i_top.':I'.$i_bottom)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

//		$j = 1;
//		$share = 0;
//		$loan = 0;
//		$members = array();
//		$count_retire = 0;
//		if(!empty($data['member_out'])) {
//			$members = $data['member_out']['members'];
//			$member_ids = array_column($members, 'member_id');
//			array_multisort($member_ids, SORT_ASC, $members);
//			foreach($members as $key => $row){
//				$share += @$row['sum_share'];
//				$loan_amount_balance = $row['sum_loan'];
//				$loan += $loan_amount_balance;
//				$i+=1;
//				$objPHPExcel->getActiveSheet()->SetCellValue('A' . $i , $j++);
//				$objPHPExcel->getActiveSheet()->SetCellValue('B' . $i , @$row['member_id']."'" );
//				$objPHPExcel->getActiveSheet()->SetCellValue('C' . $i , @$row['employee_id']."'" );
//				$objPHPExcel->getActiveSheet()->SetCellValue('D' . $i , @$row['prename_short'] );
//				$objPHPExcel->getActiveSheet()->SetCellValue('E' . $i , @$row['firstname_th'] );
//				$objPHPExcel->getActiveSheet()->SetCellValue('F' . $i , @$row['lastname_th'] );
//				$objPHPExcel->getActiveSheet()->SetCellValue('G' . $i , @$mem_group_arr[@$row['faction']] );
//				$objPHPExcel->getActiveSheet()->SetCellValue('H' . $i , number_format(@$row['sum_share'],2) );
//				$objPHPExcel->getActiveSheet()->SetCellValue('I' . $i , number_format(@$loan_amount_balance,2) );
////				$objPHPExcel->getActiveSheet()->SetCellValue('J' . $i , @$row['resign_cause_name'] );
//
//				$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':I'.$i)->applyFromArray($textStyleArray);
//				$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':I'.$i)->applyFromArray($borderTop);
//
//				foreach(range('A','I') as $columnID) {
//					if(!in_array($columnID, array('D','E','F'))){
//						$objPHPExcel->getActiveSheet()->getStyle($columnID.$i)->applyFromArray($borderLeft);
//						$objPHPExcel->getActiveSheet()->getStyle($columnID.$i)->applyFromArray($borderRight);
//					}
//					$objPHPExcel->getActiveSheet()->getStyle($columnID.$i)->applyFromArray($borderBottom);
//				}
//				$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':C'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//				$objPHPExcel->getActiveSheet()->getStyle('G'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//				$objPHPExcel->getActiveSheet()->getStyle('H'.$i.':I'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
//				$count_retire++;
//			}
//		}
//		$objPHPExcel->getActiveSheet()->SetCellValue('A'.$i_title, "ในระหว่างเดือน ".$month_arr[$m]."  ".($year+543)."  มีพนักงานลาออกจากการเป็นสมาชิกสหกรณ์  จำนวน  ".$count_retire." ราย  ดังนี้" ) ;
//		$i+=1;
//		$objPHPExcel->getActiveSheet()->SetCellValue('H' . $i , number_format($share,2) );
//		$objPHPExcel->getActiveSheet()->SetCellValue('I' . $i , number_format($loan,2) );
//		$objPHPExcel->getActiveSheet()->getStyle('H'.$i.':I'.$i)->applyFromArray($borderBottom);
//		$objPHPExcel->getActiveSheet()->getStyle('H'.$i.':I'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
		
//		$i+=2;
//		$objPHPExcel->getActiveSheet()->mergeCells('B'.$i.':G'.$i);
//		$objPHPExcel->getActiveSheet()->SetCellValue('B' . $i , 'สมาชิกคงเหลือ ณ.วันที่  '.date('t',strtotime($year."-".sprintf("%02d",$m)."-01")).' '.$month_arr[$m].'  '.($year+543) );
//		$i+=1;
//		$objPHPExcel->getActiveSheet()->mergeCells('B'.$i.':E'.$i);
//		if(($m-1)==0){
//			$prev_month = 12;
//			$prev_year = $year-1;
//		}else{
//			$prev_month = $m-1;
//			$prev_year = $year;
//		}
//		$objPHPExcel->getActiveSheet()->SetCellValue('B' . $i , 'ยอดยกมา ('.date('t',strtotime($prev_year."-".sprintf("%02d",($prev_month))."-01")).' '.$month_arr[$prev_month].'  '.($prev_year+543).')' );
		
//		$this->db->select(array('t1.member_id','t2.resign_date','t2.req_resign_id'));
//		$this->db->from("coop_mem_apply as t1");
//		$this->db->join("coop_mem_req_resign as t2","t1.member_id = t2.member_id AND t2.req_resign_status = '1' AND t2.resign_date < '".date('Y-m-t',strtotime($prev_year."-".sprintf("%02d",($prev_month))."-01"))."'","left");
//		$this->db->where("t1.apply_date < '".date('Y-m-t',strtotime($prev_year."-".sprintf("%02d",($prev_month))."-01"))."'");
//		$rs_all_member = $this->db->get()->result_array();
//		$count_all_member = 0;
//		if(!empty($rs_all_member)){
//			foreach($rs_all_member as $key => $row_all_member){
//				if(@$row_all_member['req_resign_id']==''){
//					$count_all_member++;
//				}
//			}
//		}

//		$objPHPExcel->getActiveSheet()->SetCellValue('F' . $i , number_format($count_all_member) );
//		$objPHPExcel->getActiveSheet()->SetCellValue('G' . $i , 'ราย' );
//		$i+=1;
//		$objPHPExcel->getActiveSheet()->mergeCells('B'.$i.':E'.$i);
//		$objPHPExcel->getActiveSheet()->SetCellValue('B' . $i , 'สมาชิกสมัครใหม่' );
//		$objPHPExcel->getActiveSheet()->SetCellValue('F' . $i , number_format($count_register) );
//		$objPHPExcel->getActiveSheet()->SetCellValue('G' . $i , 'ราย' );
//		$i+=1;
//		$objPHPExcel->getActiveSheet()->mergeCells('B'.$i.':E'.$i);
//		$objPHPExcel->getActiveSheet()->SetCellValue('B' . $i , 'สมาชิกลาออก ' );
//		$objPHPExcel->getActiveSheet()->SetCellValue('F' . $i , number_format($count_retire) );
//		$objPHPExcel->getActiveSheet()->SetCellValue('G' . $i , 'ราย' );
//		$i+=1;
//		$objPHPExcel->getActiveSheet()->mergeCells('B'.$i.':E'.$i);
//		$objPHPExcel->getActiveSheet()->SetCellValue('B' . $i , 'จำนวนสมาชิกคงเหลือทั้งสิ้น' );
//		$objPHPExcel->getActiveSheet()->SetCellValue('F' . $i , number_format((($count_all_member+$count_register)-$count_retire)) );
//		$objPHPExcel->getActiveSheet()->SetCellValue('G' . $i , 'ราย' );

//		$objPHPExcel->getActiveSheet()->setTitle($month_short_arr[$m].substr(($year+543),2,2));
		$sheet++;
	}
}

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="รายงานสรุปเข้าออก_'.$file_name_text.'.xlsx"');
header('Cache-Control: max-age=0');
		
$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
$objWriter->save('php://output');
exit;	
?>