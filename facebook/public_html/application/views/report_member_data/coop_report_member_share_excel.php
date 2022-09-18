<?php
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
$textStyleArray = array(
  'font'  => array(
		'bold'  => false,
		'size'  => 15,
		'name'  => 'Angsana New'
	)
);
$headerStyle = array(
	'font'  => array(
		'bold'  => true,
		'size'  => 15,
		'name'  => 'Angsana New'
	)
);
$titleStyle = array(
	'font'  => array(
		'bold'  => true,
		'size'  => 15,
		'name'  => 'Angsana New'
	)
);
$footerStyle = array(
	'font'  => array(
		'bold'  => true,
		'size'  => 14,
		'name'  => 'AngsanaUPC'
	)
);

$styleArray = array(
	'borders' => array(
		'allborders' => array(
			'style' => PHPExcel_Style_Border::BORDER_THIN
		)
	),
	'font' => array(
		'bold' => false,
		'size'  => 14,
		'name'  => 'AngsanaUPC'
	)
);
$styleBordArray = array(
	'borders' => array(
		'allborders' => array(
			'style' => PHPExcel_Style_Border::BORDER_THIN
		)
	),
	'font' => array(
		'bold' => true,
		'size'  => 14,
		'name'  => 'AngsanaUPC'
	)
);

$sheet = 0;
$i=0;
$objPHPExcel->createSheet($sheet);
$objPHPExcel->setActiveSheetIndex($sheet);
$i+=1;
$objPHPExcel->getActiveSheet()->mergeCells('A'.$i.':I'.$i);
$objPHPExcel->getActiveSheet()->SetCellValue('A'.$i , "แบบฟอร์มข้อมูลทะเบียนสมาชิกสหกรณ์และการถือหุ้น");
$objPHPExcel->getActiveSheet()->getStyle('A'.$i)->applyFromArray($titleStyle);
$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':I'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

$i+=1;
$objPHPExcel->getActiveSheet()->mergeCells('A'.$i.':C'.$i);
$objPHPExcel->getActiveSheet()->SetCellValue('A'.$i , "สหกรณ์ ".$_SESSION['COOP_NAME']);
$objPHPExcel->getActiveSheet()->mergeCells('D'.$i.':F'.$i);
$objPHPExcel->getActiveSheet()->SetCellValue('D'.$i , "เลขทะเบียนสหกรณ์ ".$profile['coop_member_id']);
$objPHPExcel->getActiveSheet()->mergeCells('G'.$i.':I'.$i);
$objPHPExcel->getActiveSheet()->SetCellValue('G'.$i , "จังหวัด ".$profile['province_name']);
$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':I'.$i)->applyFromArray($titleStyle);
$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':I'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

$i+=1;
$objPHPExcel->getActiveSheet()->SetCellValue('A'.$i , "ลำดับ");
$objPHPExcel->getActiveSheet()->SetCellValue('B'.$i , "เลข ๑๓ หลักสหกรณ์");
$objPHPExcel->getActiveSheet()->SetCellValue('C'.$i , "ปีบัญชี");
$objPHPExcel->getActiveSheet()->SetCellValue('D'.$i , "หมายเลขประจำตัว\nประชาชน");
$objPHPExcel->getActiveSheet()->SetCellValue('E'.$i , "คำนำหน้าชื่อ");
$objPHPExcel->getActiveSheet()->SetCellValue('F'.$i , "ชื่อ");
$objPHPExcel->getActiveSheet()->SetCellValue('G'.$i , "สกุล");
$objPHPExcel->getActiveSheet()->SetCellValue('H'.$i , "สัญชาติ\n(รหัส ISO)");
$objPHPExcel->getActiveSheet()->SetCellValue('I'.$i , "จำนวนหุ้น");
$objPHPExcel->getActiveSheet()->SetCellValue('J'.$i , "มูลค่าต่อหุ้น");
$objPHPExcel->getActiveSheet()->SetCellValue('K'.$i , "วัน เดือน\nปี ที่เข้า\nเป็นสมาชิก");
$objPHPExcel->getActiveSheet()->SetCellValue('L'.$i , "ประเภทสมาชิก");
$objPHPExcel->getActiveSheet()->SetCellValue('M'.$i , "วัน เดือน\nปี ที่ออกจาก\nสมาชิก");
$objPHPExcel->getActiveSheet()->SetCellValue('N'.$i , "ที่อยู่");
$objPHPExcel->getActiveSheet()->SetCellValue('O'.$i , "ตำบล/แขวง");
$objPHPExcel->getActiveSheet()->SetCellValue('P'.$i , "อำเภอ/เขต");
$objPHPExcel->getActiveSheet()->SetCellValue('Q'.$i , "จังหวัด");
$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':Q'.$i)->applyFromArray($styleBordArray);
$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':Q'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':Q'.$i)->getAlignment()->setWrapText(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(8);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(8);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(12.5);
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(75);
$objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setWidth(20);

foreach($datas as $key => $data) {
    $i+=1;
    $member_date = "";
    if(!empty($data["member_date"])) {
        $date_arr = explode( '-', $data["member_date"]);
        $member_date = $date_arr[2]."/".$date_arr["1"]."/".($date_arr["0"]+543);
        if(!empty($data['req_resign_date'])){
            $date_arr = explode( '-', $data["req_resign_date"]);
            $req_resign_date = $date_arr[2]."/".$date_arr["1"]."/".($date_arr["0"]+543);
        }else{
            $req_resign_date = '';
        }
    }

    $address = "";

    if(@$data['c_address_no']) {
        $address .= @$data['c_address_no'];
    }
    if(@$data['c_address_moo']) {
        $address .= " หมู่ ".@$data['c_address_moo'];
    }
    if(@$data['c_address_village']) {
        $address .= @$data['c_address_village'];
    }
    if(@$data['c_address_road']) {
        $address .= " ถ.".@$data['c_address_road'];
    }
    if(@$data['c_address_soi']) {
        $address .= " ซ. ".@$data['c_address_soi'];
    }

    $objPHPExcel->getActiveSheet()->SetCellValue('A'.$i , $key+1);
    $objPHPExcel->getActiveSheet()->setCellValueExplicit('B'.$i , '1020000625356', PHPExcel_Cell_DataType::TYPE_STRING);
    $objPHPExcel->getActiveSheet()->SetCellValue('C'.$i , $year);
    $objPHPExcel->getActiveSheet()->setCellValueExplicit('D'.$i, $data["id_card"], PHPExcel_Cell_DataType::TYPE_STRING);
    $objPHPExcel->getActiveSheet()->SetCellValue('E'.$i , $data["prename_full"]);
    $objPHPExcel->getActiveSheet()->SetCellValue('F'.$i , $data["firstname_th"]);
    $objPHPExcel->getActiveSheet()->SetCellValue('G'.$i , $data["lastname_th"]);
    $objPHPExcel->getActiveSheet()->SetCellValue('H'.$i , !empty($data["nationality"]) ? $data["nationality"] : "TH");
    $objPHPExcel->getActiveSheet()->SetCellValue('I'.$i , $data["share"]);
    $objPHPExcel->getActiveSheet()->SetCellValue('J'.$i , $data["share_value"]);
    $objPHPExcel->getActiveSheet()->SetCellValue('K'.$i , $member_date);
    $objPHPExcel->getActiveSheet()->SetCellValue('L'.$i , !empty($data["mem_type_code"]) ? 2 : 1);
    $objPHPExcel->getActiveSheet()->SetCellValue('M'.$i , $req_resign_date);
    $objPHPExcel->getActiveSheet()->SetCellValue('N'.$i , $address);
    $objPHPExcel->getActiveSheet()->SetCellValue('O'.$i , $data['district_name']);
    $objPHPExcel->getActiveSheet()->SetCellValue('P'.$i , $data['amphur_name']);
    $objPHPExcel->getActiveSheet()->SetCellValue('Q'.$i , $data['province_name']);
    $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':Q'.$i)->applyFromArray($styleArray);
}

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="แบบฟอร์มข้อมูลทะเบียนสมาชิกสหกรณ์และการถือหุ้น.xlsx"');
header('Cache-Control: max-age=0');

$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
$objWriter->save('php://output');
exit;
?>