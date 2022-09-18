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
$headerUStyle = array(
	'font'  => array(
		'bold'  => true,
		'size'  => 15,
        'name'  => 'Angsana New',
        'underline' => 'single'
	)
);
$headerDUStyle = array(
	'font'  => array(
		'bold'  => true,
		'size'  => 15,
        'name'  => 'Angsana New',
        'underline' => 'double'
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
$sheet = 0;
$i=0;
$objPHPExcel->createSheet($sheet);
$objPHPExcel->setActiveSheetIndex($sheet);

$i+=1;
$i_title = $i;
$objPHPExcel->getActiveSheet()->mergeCells('A'.$i.':S'.$i);
$objPHPExcel->getActiveSheet()->SetCellValue('A' . $i , $_SESSION['COOP_NAME'] );
$objPHPExcel->getActiveSheet()->getStyle('A'.$i)->applyFromArray($titleStyle);
$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':S'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$i+=1;
$i_title = $i;
$objPHPExcel->getActiveSheet()->mergeCells('A'.$i.':S'.$i);
$objPHPExcel->getActiveSheet()->SetCellValue('A' . $i , "รายงานสรุปทุนเรือนหุ้น, เงินกู็คงเหลือ" );
$objPHPExcel->getActiveSheet()->getStyle('A'.$i)->applyFromArray($titleStyle);
$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':S'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$i+=1;
$i_title = $i;
$objPHPExcel->getActiveSheet()->mergeCells('A'.$i.':S'.$i);
$objPHPExcel->getActiveSheet()->SetCellValue('A' . $i , "ระหว่างวันที่ ".$from_date." - ".$thur_date);
$objPHPExcel->getActiveSheet()->getStyle('A'.$i)->applyFromArray($titleStyle);
$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':S'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

$i+=1;
$objPHPExcel->getActiveSheet()->mergeCells('A'.$i.':E'.($i+1));
$objPHPExcel->getActiveSheet()->SetCellValue('A' . $i , 'ชื่อหน่วยงาน');
$objPHPExcel->getActiveSheet()->SetCellValue('F' . $i , 'จำนวนทั้งหมด');
$objPHPExcel->getActiveSheet()->mergeCells('G'.$i.':G'.($i+1));
$objPHPExcel->getActiveSheet()->SetCellValue('G' . $i , 'ทุนเรือนหุ้น');
$objPHPExcel->getActiveSheet()->SetCellValue('H' . $i , 'เข้าใหม่');
$objPHPExcel->getActiveSheet()->SetCellValue('I' . $i , 'ประจำเดือน');
$objPHPExcel->getActiveSheet()->mergeCells('J'.$i.':M'.$i);
$objPHPExcel->getActiveSheet()->SetCellValue('J' . $i , 'สามัญ สามัญโครงการ สามัญสะสม สามัญโครงการ2');
$objPHPExcel->getActiveSheet()->mergeCells('N'.$i.':Q'.$i);
$objPHPExcel->getActiveSheet()->SetCellValue('N' . $i , 'สามัญเพื่อการศึกษา สามัญโครงการพิเศษเพื่อช่วยเหลือสมาชิกที่กู้เงิน');
$objPHPExcel->getActiveSheet()->mergeCells('R'.$i.':S'.$i);
$objPHPExcel->getActiveSheet()->SetCellValue('R' . $i , 'ผลรวม');

$i++;
$objPHPExcel->getActiveSheet()->SetCellValue('F' . $i , 'จำนวนที่มีหนี้');
$objPHPExcel->getActiveSheet()->SetCellValue('H' . $i , 'ลาออก');
$objPHPExcel->getActiveSheet()->SetCellValue('I' . $i , 'ซื้อเพิ่ม');
$objPHPExcel->getActiveSheet()->SetCellValue('J' . $i , '');
$objPHPExcel->getActiveSheet()->SetCellValue('K' . $i , 'จ/น ฉบับ');
$objPHPExcel->getActiveSheet()->SetCellValue('L' . $i , 'จ/น เงินให้กู้');
$objPHPExcel->getActiveSheet()->SetCellValue('M' . $i , 'จ/น เงินคงเหลือ');
$objPHPExcel->getActiveSheet()->SetCellValue('N' . $i , '');
$objPHPExcel->getActiveSheet()->SetCellValue('O' . $i , 'จ/น ฉบับ');
$objPHPExcel->getActiveSheet()->SetCellValue('P' . $i , 'จ/น เงินให้กู้');
$objPHPExcel->getActiveSheet()->SetCellValue('Q' . $i , 'จ/น เงินคงเหลือ');
$objPHPExcel->getActiveSheet()->SetCellValue('R' . $i , 'จ/น เงินให้กู้');
$objPHPExcel->getActiveSheet()->SetCellValue('S' . $i , 'จ/น เงินคงเหลือ');

$objPHPExcel->getActiveSheet()->getStyle('A'.($i-1).':S'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$total = array();
foreach($data as $factions) {
	foreach($factions as $faction) {
		$i++;
		$objPHPExcel->getActiveSheet()->mergeCells('A'.$i.':E'.$i);
		$objPHPExcel->getActiveSheet()->SetCellValue('A'.$i , $faction["mem_group_id"]."-".$faction["mem_group_name"]);
		$objPHPExcel->getActiveSheet()->SetCellValue('B'.($i+1) , "ชาย");
		$objPHPExcel->getActiveSheet()->SetCellValue('C'.($i+1) , !empty($faction["sex"]["m"]) ? $faction["sex"]["m"] : 0);
		$objPHPExcel->getActiveSheet()->SetCellValue('D'.($i+1) , "หญิง");
		$objPHPExcel->getActiveSheet()->SetCellValue('E'.($i+1) , !empty($faction["sex"]["f"]) ? $faction["sex"]["f"] : 0);

		$objPHPExcel->getActiveSheet()->SetCellValue('F'.$i , !empty($faction["share_person"]) ? $faction["share_person"] : 0);
		$objPHPExcel->getActiveSheet()->SetCellValue('F'.($i+1) , !empty( $faction["debt_person"]) ? $faction["debt_person"] : 0);

		$objPHPExcel->getActiveSheet()->SetCellValue('G'.$i, !empty($faction["share_collect"]) ? number_format($faction["share_collect"]) : "0.00");

		$objPHPExcel->getActiveSheet()->SetCellValue('H'.$i , !empty($faction["new_mem"]) ? $faction["new_mem"] : 0);
		$objPHPExcel->getActiveSheet()->SetCellValue('H'.($i+1) , !empty($faction["resign_mem"]) ? $faction["resign_mem"] : 0);

		$objPHPExcel->getActiveSheet()->SetCellValue('I'.$i , !empty($faction["share_month"]) ? number_format($faction["share_month"]) : "0.00");
		$objPHPExcel->getActiveSheet()->SetCellValue('I'.($i+1) , !empty($faction["share_spec"]) ? number_format($faction["share_spec"]) : "0.00");

		$total["male"] += $faction["sex"]["m"];
		$total["female"] += $faction["sex"]["f"];
		$total["member"] += $faction["sex"]["m"] + $faction["sex"]["f"];

		$total["share_person"] += $faction["share_person"];
		$total["debt_person"] += $faction["debt_person"];
		$total["share_collect"] += $faction["share_collect"];
		$total["new_mem"] += $faction["new_mem"];
		$total["resign_mem"] += $faction["resign_mem"];
		$total["share_month"] += $faction["share_month"];
		$total["share_spec"] += $faction["share_spec"];

		$total_amount = 0;
		$total_balance = 0;

		$i_1 = $i;
		$i_2 = $i;
		if(!empty($faction["loan"])) {
			if(!empty($faction["loan"][1])) {
				foreach($faction["loan"][1] as $key => $loan) {
					$objPHPExcel->getActiveSheet()->SetCellValue('J'.$i_1 , $loan["prefix"]);
					$objPHPExcel->getActiveSheet()->SetCellValue('K'.$i_1 , $loan["loan_person"]);
					$objPHPExcel->getActiveSheet()->SetCellValue('L'.$i_1 , number_format($loan["amount"]));
					$objPHPExcel->getActiveSheet()->SetCellValue('M'.$i_1 , number_format($loan["loan_balance"]));
					$total_amount += $loan["amount"];
					$total_balance += $loan["loan_balance"];

					$total["loan"][1][$key]["prefix"] = $loan["prefix"];
					$total["loan"][1][$key]["loan_person"] += $loan["loan_person"];
					$total["loan"][1][$key]["amount"] += $loan["amount"];
					$total["loan"][1][$key]["loan_balance"] += $loan["loan_balance"];
					$i_1++;
				}
			}

			if(!empty($faction["loan"][2])) {
				foreach($faction["loan"][2] as $key => $loan) {
					$objPHPExcel->getActiveSheet()->SetCellValue('N'.$i_2 , $loan["prefix"]);
					$objPHPExcel->getActiveSheet()->SetCellValue('O'.$i_2 , $loan["loan_person"]);
					$objPHPExcel->getActiveSheet()->SetCellValue('P'.$i_2 , number_format($loan["amount"]));
					$objPHPExcel->getActiveSheet()->SetCellValue('Q'.$i_2 , number_format($loan["loan_balance"]));
					$total_amount += $loan["amount"];
					$total_balance += $loan["loan_balance"];

					$total["loan"][2][$key]["prefix"] = $loan["prefix"];
					$total["loan"][2][$key]["loan_person"] += $loan["loan_person"];
					$total["loan"][2][$key]["amount"] += $loan["amount"];
					$total["loan"][2][$key]["loan_balance"] += $loan["loan_balance"];
					$i_2++;
				}
			}
		} else  {
			$i_1++;
		}

		$objPHPExcel->getActiveSheet()->SetCellValue('R'.$i , number_format($total_amount));
		$objPHPExcel->getActiveSheet()->SetCellValue('S'.$i , number_format($total_balance));

		$total["total_amount"] += $total_amount;
		$total["total_balance"] += $total_balance;

		if($i < $i_1) $i = $i_1;
		if($i < $i_2) $i = $i_2;
	}
}

//Total
$i++;
$objPHPExcel->getActiveSheet()->SetCellValue('A'.$i , "ยอดรวม");
$objPHPExcel->getActiveSheet()->SetCellValue('B'.($i) , "ชาย");
$objPHPExcel->getActiveSheet()->SetCellValue('C'.($i) , !empty($total["male"]) ? $total["male"] : 0);
$objPHPExcel->getActiveSheet()->SetCellValue('D'.($i) , "หญิง");
$objPHPExcel->getActiveSheet()->SetCellValue('E'.($i) , !empty($total["female"]) ? $total["female"] : 0);
$objPHPExcel->getActiveSheet()->SetCellValue('F'.$i , !empty($total["share_person"]) ? $total["share_person"] : 0);
$objPHPExcel->getActiveSheet()->SetCellValue('F'.($i+1) , !empty( $total["debt_person"]) ? $total["debt_person"] : 0);
$objPHPExcel->getActiveSheet()->SetCellValue('G'.$i, !empty($total["share_collect"]) ? number_format($total["share_collect"]) : "0.00");
$objPHPExcel->getActiveSheet()->SetCellValue('H'.$i , !empty($total["new_mem"]) ? $total["new_mem"] : 0);
$objPHPExcel->getActiveSheet()->SetCellValue('H'.($i+1) , !empty($total["resign_mem"]) ? $total["resign_mem"] : 0);
$objPHPExcel->getActiveSheet()->SetCellValue('I'.$i , !empty($total["share_month"]) ? number_format($faction["share_month"]) : "0.00");
$objPHPExcel->getActiveSheet()->SetCellValue('I'.($i+1) , !empty($total["share_spec"]) ? number_format($total["share_spec"]) : "0.00");

$i_1 = $i;
$i_2 = $i;

if(!empty($total["loan"])) {
	if(!empty($total["loan"][1])) {
		foreach($total["loan"][1] as $key => $loan) {
			$objPHPExcel->getActiveSheet()->SetCellValue('J'.$i_1 , $loan["prefix"]);
			$objPHPExcel->getActiveSheet()->SetCellValue('K'.$i_1 , $loan["loan_person"]);
			$objPHPExcel->getActiveSheet()->SetCellValue('L'.$i_1 , number_format($loan["amount"]));
			$objPHPExcel->getActiveSheet()->SetCellValue('M'.$i_1 , number_format($loan["loan_balance"]));

			$i_1++;
		}
	}

	if(!empty($total["loan"][2])) {
		foreach($total["loan"][2] as $key => $loan) {
			$objPHPExcel->getActiveSheet()->SetCellValue('N'.$i_2 , $loan["prefix"]);
			$objPHPExcel->getActiveSheet()->SetCellValue('O'.$i_2 , $loan["loan_person"]);
			$objPHPExcel->getActiveSheet()->SetCellValue('P'.$i_2 , number_format($loan["amount"]));
			$objPHPExcel->getActiveSheet()->SetCellValue('Q'.$i_2 , number_format($loan["loan_balance"]));
			$i_2++;
		}
	}
}

$objPHPExcel->getActiveSheet()->SetCellValue('R'.$i , number_format($total["total_amount"]));
$objPHPExcel->getActiveSheet()->SetCellValue('S'.$i , number_format($total["total_balance"]));

$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(10);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(10);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(10);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(10);
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(17);
$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(17);
$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(17);
$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(17);
$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(17);
$objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(17);
$objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(17);
$objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(17);
$objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth(17);
$objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setWidth(17);
$objPHPExcel->getActiveSheet()->getColumnDimension('R')->setWidth(17);
$objPHPExcel->getActiveSheet()->getColumnDimension('S')->setWidth(17);
$objPHPExcel->getActiveSheet()->setTitle('sheet',2,2);
$sheet++;

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="รายงานสรุปทุนเรือนหุ้น, เงินกู็คงเหลือ.xlsx"');
header('Cache-Control: max-age=0');

$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
$objWriter->save('php://output');
exit;
?>
