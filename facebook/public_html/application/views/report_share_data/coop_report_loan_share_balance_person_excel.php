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
$objPHPExcel->getActiveSheet()->SetCellValue('A' . $i , "??????????????????????????????????????????????????????????????????, ?????????????????????????????????????????? ????????????????????????" );
$objPHPExcel->getActiveSheet()->getStyle('A'.$i)->applyFromArray($titleStyle);
$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':S'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$i+=1;
$i_title = $i;
$objPHPExcel->getActiveSheet()->mergeCells('A'.$i.':S'.$i);
$objPHPExcel->getActiveSheet()->SetCellValue('A' . $i , "??????????????????????????????????????? ".$from_date." - ".$thur_date);
$objPHPExcel->getActiveSheet()->getStyle('A'.$i)->applyFromArray($titleStyle);
$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':S'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

$i+=1;
$objPHPExcel->getActiveSheet()->mergeCells('A'.$i.':C'.$i);
$objPHPExcel->getActiveSheet()->SetCellValue('D' . $i , '????????????????????????????????????');
$objPHPExcel->getActiveSheet()->mergeCells('E'.$i.':G'.$i);
$objPHPExcel->getActiveSheet()->SetCellValue('E' . $i , '?????????????????????????????????');
$objPHPExcel->getActiveSheet()->mergeCells('H'.$i.':H'.($i+1));
$objPHPExcel->getActiveSheet()->SetCellValue('H' . $i , '????????????????????????????????????');
$objPHPExcel->getActiveSheet()->mergeCells('I'.$i.':P'.$i);
$objPHPExcel->getActiveSheet()->SetCellValue('I' . $i , '??????????????? ???????????????????????????????????? ??????????????????????????? ????????????????????????????????????2');
$objPHPExcel->getActiveSheet()->mergeCells('Q'.$i.':X'.$i);
$objPHPExcel->getActiveSheet()->SetCellValue('Q' . $i , '?????????????????????????????????????????????????????? ?????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????');

$i++;
$objPHPExcel->getActiveSheet()->SetCellValue('A'.$i , '????????????????????????????????????');
$objPHPExcel->getActiveSheet()->SetCellValue('B'.$i , '????????????-?????????????????????');
$objPHPExcel->getActiveSheet()->SetCellValue('C'.$i , '?????????');
$objPHPExcel->getActiveSheet()->SetCellValue('D'.$i , '????????????');
$objPHPExcel->getActiveSheet()->SetCellValue('E'.$i , '??????????????????');
$objPHPExcel->getActiveSheet()->SetCellValue('F'.$i , '?????????');
$objPHPExcel->getActiveSheet()->SetCellValue('G'.$i , '?????????????????????');
$objPHPExcel->getActiveSheet()->SetCellValue('I'.$i , '?????????????????????????????????');
$objPHPExcel->getActiveSheet()->SetCellValue('J'.$i , '??????????????????');
$objPHPExcel->getActiveSheet()->SetCellValue('K'.$i , '?????????');
$objPHPExcel->getActiveSheet()->SetCellValue('L'.$i , '????????????');
$objPHPExcel->getActiveSheet()->SetCellValue('M'.$i , '?????????????????????');
$objPHPExcel->getActiveSheet()->SetCellValue('N'.$i , '????????????????????????');
$objPHPExcel->getActiveSheet()->SetCellValue('O'.$i , '?????????????????????');
$objPHPExcel->getActiveSheet()->SetCellValue('P'.$i , '??????????????????????????????????????????');
$objPHPExcel->getActiveSheet()->SetCellValue('Q'.$i , '?????????????????????????????????');
$objPHPExcel->getActiveSheet()->SetCellValue('R'.$i , '??????????????????');
$objPHPExcel->getActiveSheet()->SetCellValue('S'.$i , '?????????');
$objPHPExcel->getActiveSheet()->SetCellValue('T'.$i , '????????????');
$objPHPExcel->getActiveSheet()->SetCellValue('U'.$i , '?????????????????????');
$objPHPExcel->getActiveSheet()->SetCellValue('V'.$i , '????????????????????????');
$objPHPExcel->getActiveSheet()->SetCellValue('W'.$i , '?????????????????????');
$objPHPExcel->getActiveSheet()->SetCellValue('X'.$i , '??????????????????????????????????????????');

$objPHPExcel->getActiveSheet()->getStyle('A'.($i-1).':S'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$total = array();
foreach($data as $person) {
	$i++;
	$total["member_count"]++;
	$objPHPExcel->getActiveSheet()->SetCellValue('A'.$i , $person["member_id"]);
	$objPHPExcel->getActiveSheet()->SetCellValue('B'.$i , $person["prename_full"].$person["firstname_th"]." ".$person["lastname_th"]);
	$objPHPExcel->getActiveSheet()->SetCellValue('C'.$i , $person["sex"] == "M" ? "???" : ($person["sex"] == "F" ? "???" : ""));
	if($person["sex"] == "M") {
		$total["member_m"]++;
	} else if ($person["sex"] == "F") {
		$total["member_f"]++;
	}
	if(!empty($person["share_collect"]) && !empty($person["share_transaction"])) {
		$share_prev = $person["share_collect"] - $person["share_transaction"];
		$objPHPExcel->getActiveSheet()->SetCellValue('D'.$i , number_format($share_prev,2));
	} else if(!empty($person["share_collect"])) {
		$objPHPExcel->getActiveSheet()->SetCellValue('D'.$i , number_format($person["share_collect"],2));
	}
	if(!empty($person['share_transaction'])) {
		$objPHPExcel->getActiveSheet()->SetCellValue('E'.$i, $this->center_function->ConvertToThaiDate($person['share_date'],1,0));
		$objPHPExcel->getActiveSheet()->SetCellValue('F'.$i,$person['share_period']);
		$objPHPExcel->getActiveSheet()->SetCellValue('G'.$i, number_format($person['share_transaction']));
	}

	if(!empty($person["share_collect"])) {
		$objPHPExcel->getActiveSheet()->SetCellValue('H'.$i, number_format($person['share_collect']));
		$total["share_collect"] += $person["share_collect"];
	}


	$i_1 = $i;
	$i_2 = $i;
	if(!empty($person["loan"])) {
		$total["member_debt"]++;
		if(!empty($person["loan"][1])) {
			foreach($person["loan"][1] as $key => $loan) {
				$objPHPExcel->getActiveSheet()->SetCellValue('I'.$i_1, $loan["prefix_code"].$loan['contrant_number']);
				if(!empty($loan["payment_date"])) {
					$prev_loan = $loan["balance"] + $loan["principal"];
					$total_loan_payment = $loan["principal"] + $loan["interest"];
					$objPHPExcel->getActiveSheet()->SetCellValue('J'.$i_1, $this->center_function->ConvertToThaiDate($loan['payment_date']));
					$objPHPExcel->getActiveSheet()->SetCellValue('K'.$i_1, $loan['period']);
					$objPHPExcel->getActiveSheet()->SetCellValue('L'.$i_1, number_format($prev_loan,2));
					$objPHPExcel->getActiveSheet()->SetCellValue('M'.$i_1, !empty($loan["principal"]) ? number_format($loan["principal"],2) : "");
					$objPHPExcel->getActiveSheet()->SetCellValue('N'.$i_1, !empty($loan["interest"]) ? number_format($loan["interest"],2) : "");
					$objPHPExcel->getActiveSheet()->SetCellValue('O'.$i_1, !empty($total_loan_payment) ? number_format($total_loan_payment,2) : "");

					$total["loan"][1][$loan["type"]]["principal"] += !empty($loan['principal']) ? $loan['principal'] : 0;
					$total["loan"][1][$loan["type"]]["interest"] += !empty($loan['interest']) ? $loan['interest'] : 0;
				}
				$objPHPExcel->getActiveSheet()->SetCellValue('P'.$i_1, $loan['balance']);
				$total["loan"][1][$loan["type"]]["loan_balance"] += $loan['balance'];

				$i_1++;
			}
		}
		if(!empty($person["loan"][2])) {
			foreach($person["loan"][2] as $key => $loan) {
				$objPHPExcel->getActiveSheet()->SetCellValue('Q'.$i_2, $loan["prefix_code"].$loan['contrant_number']);
				if(!empty($loan["payment_date"])) {
					$prev_loan = $loan["balance"] + $loan["principal"];
					$total_loan_payment = $loan["principal"] + $loan["interest"];
					$objPHPExcel->getActiveSheet()->SetCellValue('R'.$i_2, $this->center_function->ConvertToThaiDate($loan['payment_date']));
					$objPHPExcel->getActiveSheet()->SetCellValue('S'.$i_2, $loan['period']);
					$objPHPExcel->getActiveSheet()->SetCellValue('T'.$i_2, number_format($prev_loan,2));
					$objPHPExcel->getActiveSheet()->SetCellValue('U'.$i_2, !empty($loan["principal"]) ? number_format($loan["principal"],2) : "");
					$objPHPExcel->getActiveSheet()->SetCellValue('V'.$i_2, !empty($loan["interest"]) ? number_format($loan["interest"],2) : "");
					$objPHPExcel->getActiveSheet()->SetCellValue('W'.$i_2, !empty($total_loan_payment) ? number_format($total_loan_payment,2) : "");

					$total["loan"][2][$loan["type"]]["principal"] += !empty($loan['principal']) ? $loan['principal'] : 0;
					$total["loan"][2][$loan["type"]]["interest"] += !empty($loan['interest']) ? $loan['interest'] : 0;
					$total["loan"][2][$loan["type"]]["total_loan_payment"] += !empty($total_loan_payment) ? $total_loan_payment : 0;
				}
				$objPHPExcel->getActiveSheet()->SetCellValue('X'.$i_2,$loan['balance']);
				$total["loan"][2][$loan["type"]]["loan_balance"] += $loan['balance'];

				$i_2++;
			}
		}
	}

	if($i < ($i_1 - 1)) $i = ($i_1 - 1);
	if($i < ($i_2 - 1)) $i = ($i_2 - 1);
}

$i++;
$objPHPExcel->getActiveSheet()->mergeCells('A'.$i.':C'.$i);
$objPHPExcel->getActiveSheet()->SetCellValue('A'.$i , "?????????");
$objPHPExcel->getActiveSheet()->SetCellValue('G'.$i , number_format($total["share_collect"], 2));

if(!empty($total["loan"])) {
	if(!empty($total["loan"][1])) {
		$i_1 = $i;
		foreach($total["loan"][1] as $loan_type_id => $loan) {
			$objPHPExcel->getActiveSheet()->mergeCells('I'.$i_1.':J'.$i_1);
			$objPHPExcel->getActiveSheet()->SetCellValue('I'.$i_1 , $loan_name[$loan_type_id]);
			$objPHPExcel->getActiveSheet()->SetCellValue('K'.$i_1 , "?????????????????????");
			$objPHPExcel->getActiveSheet()->SetCellValue('L'.$i_1 , !empty($loan["principal"]) ? number_format($loan["principal"]) : "");
			$objPHPExcel->getActiveSheet()->SetCellValue('M'.$i_1 , "????????????????????????");
			$objPHPExcel->getActiveSheet()->SetCellValue('N'.$i_1 , !empty($loan["interest"]) ? number_format($loan["interest"]) : "");
			$objPHPExcel->getActiveSheet()->SetCellValue('O'.$i_1 , "?????????????????????????????????");
			$objPHPExcel->getActiveSheet()->SetCellValue('P'.$i_1 , !empty($loan["loan_balance"]) ? number_format($loan["loan_balance"]) : "");
			$i_1++;

			$objPHPExcel->getActiveSheet()->SetCellValue('K'.$i_1 , "?????????????????????");
			$objPHPExcel->getActiveSheet()->SetCellValue('L'.$i_1 , !empty($loan["total_loan_payment"]) ? number_format($loan["total_loan_payment"]) : "");
			$i_1++;
		}
		
	}
	if(!empty($total["loan"][2])) {
		$i_2 = $i;
		foreach($total["loan"][2] as $loan_type_id => $loan) {
			$objPHPExcel->getActiveSheet()->mergeCells('Q'.$i_2.':R'.$i_2);
			$objPHPExcel->getActiveSheet()->SetCellValue('Q'.$i_2 , $loan_name[$loan_type_id]);
			$objPHPExcel->getActiveSheet()->SetCellValue('S'.$i_2 , "?????????????????????");
			$objPHPExcel->getActiveSheet()->SetCellValue('T'.$i_2 , !empty($loan["principal"]) ? number_format($loan["principal"]) : "");
			$objPHPExcel->getActiveSheet()->SetCellValue('U'.$i_2 , "????????????????????????");
			$objPHPExcel->getActiveSheet()->SetCellValue('V'.$i_2 , !empty($loan["interest"]) ? number_format($loan["interest"]) : "");
			$objPHPExcel->getActiveSheet()->SetCellValue('W'.$i_2 , "?????????????????????????????????");
			$objPHPExcel->getActiveSheet()->SetCellValue('X'.$i_2 , !empty($loan["loan_balance"]) ? number_format($loan["loan_balance"]) : "");
			$i_2++;

			$objPHPExcel->getActiveSheet()->SetCellValue('S'.$i_2 , "?????????????????????");
			$objPHPExcel->getActiveSheet()->SetCellValue('T'.$i_2 , !empty($loan["total_loan_payment"]) ? number_format($loan["total_loan_payment"]) : "");
			$i_2++;
		}
	}
}

$i++;
$objPHPExcel->getActiveSheet()->SetCellValue('B'.$i , $total["member_count"]." ?????????");
$objPHPExcel->getActiveSheet()->SetCellValue('C'.$i , "?????????????????? ".$total["member_debt"]." ?????????");
$objPHPExcel->getActiveSheet()->SetCellValue('D'.$i , "????????? ".$total["member_m"]);
$objPHPExcel->getActiveSheet()->SetCellValue('E'.$i , "???????????? ".$total["member_f"]);

$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(17);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(17);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(17);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(17);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(17);
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(17);
$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(17);
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
$objPHPExcel->getActiveSheet()->getColumnDimension('T')->setWidth(17);
$objPHPExcel->getActiveSheet()->getColumnDimension('U')->setWidth(17);
$objPHPExcel->getActiveSheet()->getColumnDimension('V')->setWidth(17);
$objPHPExcel->getActiveSheet()->getColumnDimension('W')->setWidth(17);
$objPHPExcel->getActiveSheet()->getColumnDimension('X')->setWidth(17);
$objPHPExcel->getActiveSheet()->setTitle('sheet',2,2);
$sheet++;

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="??????????????????????????????????????????????????????????????????, ?????????????????????????????????????????? ????????????????????????.xlsx"');
header('Cache-Control: max-age=0');

$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
$objWriter->save('php://output');
exit;
?>
