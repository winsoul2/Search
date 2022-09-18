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
$objPHPExcel->getActiveSheet()->mergeCells('A'.$i.':H'.$i);
$objPHPExcel->getActiveSheet()->SetCellValue('A' . $i , $_SESSION['COOP_NAME'] ) ; 
$objPHPExcel->getActiveSheet()->getStyle('A'.$i)->applyFromArray($titleStyle);
$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':H'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$i+=1;
$i_title = $i;
$objPHPExcel->getActiveSheet()->mergeCells('A'.$i.':H'.$i);
$objPHPExcel->getActiveSheet()->SetCellValue('A' . $i , "งบกำไรขาดทุน" ) ; 
$objPHPExcel->getActiveSheet()->getStyle('A'.$i)->applyFromArray($titleStyle);
$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':H'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$i+=1;
$i_title = $i;
$objPHPExcel->getActiveSheet()->mergeCells('A'.$i.':H'.$i);
$objPHPExcel->getActiveSheet()->SetCellValue('A' . $i , "ณ วันที่ ".$this->center_function->ConvertToThaiDate($thur_date,'0','0')." และ วันที่ ".$this->center_function->ConvertToThaiDate($prev_date,'0','0')) ; 
$objPHPExcel->getActiveSheet()->getStyle('A'.$i)->applyFromArray($titleStyle);
$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':H'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

$i+=1;
$objPHPExcel->getActiveSheet()->SetCellValue('F' . $i , 'บาท' ) ;
$objPHPExcel->getActiveSheet()->getStyle('F'.$i)->getFont()->setUnderline(true);
$objPHPExcel->getActiveSheet()->SetCellValue('H' . $i , 'บาท' ) ;
$objPHPExcel->getActiveSheet()->getStyle('H'.$i)->getFont()->setUnderline(true);
$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':H'.$i)->applyFromArray($headerStyle);
$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':H'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

$i+=1;
$objPHPExcel->getActiveSheet()->SetCellValue('F' . $i , $thur_date_header);
$objPHPExcel->getActiveSheet()->getStyle('F'.$i)->getFont()->setUnderline(true);
$objPHPExcel->getActiveSheet()->SetCellValue('H' . $i , $prev_date_header);
$objPHPExcel->getActiveSheet()->getStyle('H'.$i)->getFont()->setUnderline(true);
$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':H'.$i)->applyFromArray($headerStyle);
$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':H'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

$i+=1;
$group_id = null;
$index = 1;
$profit_total = 0;
$p_profit_total = 0;
$loss_total = 0;
$p_loss_total = 0;
foreach($account_charts as $chart) {
    $amount = $year_budgets[$chart["account_chart_id"]];
    $prev_amount = $prev_year_budgets[$chart["account_chart_id"]];

    if($index > 1 && $group_id != substr($chart['account_chart_id'],0,1)) {
        if($group_id == 4) {
            $i++;
            $objPHPExcel->getActiveSheet()->mergeCells('A'.$i.':C'.$i);
            $objPHPExcel->getActiveSheet()->SetCellValue('A' . $i , "รวมรายได้") ;
            $objPHPExcel->getActiveSheet()->SetCellValue('D' . $i , "") ;
            $objPHPExcel->getActiveSheet()->SetCellValue('F' . $i , !empty($profit_total) ? number_format($profit_total,2) : "0.00" ) ;
            $objPHPExcel->getActiveSheet()->SetCellValue('H' . $i , !empty($p_profit_total) ? number_format($p_profit_total,2) : "0.00" ) ;
            $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':D'.$i)->applyFromArray($headerStyle);
            $objPHPExcel->getActiveSheet()->getStyle('F'.$i.':H'.$i)->applyFromArray($headerUStyle);
            $objPHPExcel->getActiveSheet()->getStyle('D'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        } else if ($group_id == 5) {
            $i++;
            $objPHPExcel->getActiveSheet()->mergeCells('A'.$i.':C'.$i);
            $objPHPExcel->getActiveSheet()->SetCellValue('A' . $i , "รวมค่าใช้จ่าย") ;
            $objPHPExcel->getActiveSheet()->SetCellValue('D' . $i , "") ;
            $objPHPExcel->getActiveSheet()->SetCellValue('F' . $i , !empty($loss_total) ? number_format($loss_total,2) : "0.00" ) ;
            $objPHPExcel->getActiveSheet()->SetCellValue('H' . $i , !empty($p_loss_total) ? number_format($p_loss_total,2) : "0.00" ) ;
            $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':D'.$i)->applyFromArray($headerStyle);
            $objPHPExcel->getActiveSheet()->getStyle('F'.$i.':H'.$i)->applyFromArray($headerUStyle);
            $objPHPExcel->getActiveSheet()->getStyle('D'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

			$balance = $profit_total - $loss_total;
			$p_balance = $p_profit_total - $p_loss_total;
            $i++;
            $objPHPExcel->getActiveSheet()->mergeCells('A'.$i.':C'.$i);
            $objPHPExcel->getActiveSheet()->SetCellValue('A' . $i , "กำไรสุทธิ") ;
            $objPHPExcel->getActiveSheet()->SetCellValue('D' . $i , "") ;
            $objPHPExcel->getActiveSheet()->SetCellValue('F' . $i , !empty($balance) ? number_format($balance,2) : "0.00" ) ;
            $objPHPExcel->getActiveSheet()->SetCellValue('H' . $i , !empty($p_balance) ? number_format($p_balance,2) : "0.00" ) ;
            $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':D'.$i)->applyFromArray($headerStyle);
            $objPHPExcel->getActiveSheet()->getStyle('F'.$i.':H'.$i)->applyFromArray($headerDUStyle);
            $objPHPExcel->getActiveSheet()->getStyle('D'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        }
    }

    if($amount != 0 || $prev_amount != 0) {
        $i+=1;
        $lead_space = "";
        for($j = 1; $j < $chart["level"]; $j++) {
            $lead_space .= "        ";
        }
        $group_id = substr($chart['account_chart_id'],0,1);
    
        $objPHPExcel->getActiveSheet()->mergeCells('A'.$i.':C'.$i);
        $objPHPExcel->getActiveSheet()->SetCellValue('A' . $i , $lead_space.$chart["account_chart_id"]."  ".$chart["account_chart"]) ;
        $objPHPExcel->getActiveSheet()->SetCellValue('D' . $i , "") ;
        $objPHPExcel->getActiveSheet()->SetCellValue('F' . $i , !empty($amount) ? number_format($amount,2) : "0.00" ) ;
        $objPHPExcel->getActiveSheet()->SetCellValue('H' . $i , !empty($prev_amount) ? number_format($prev_amount,2) : "0.00" ) ;
        $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':H'.$i)->applyFromArray($textStyleArray);
		$objPHPExcel->getActiveSheet()->getStyle('D'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

		if($group_id == 4) {
			$profit_total += $amount;
			$p_profit_total += $prev_amount;
		} else if ($group_id == 5) {
			$loss_total += $amount;
			$p_loss_total += $prev_amount;
		}

        $index++;
    } else if ($chart["level"] == 1) {
        $i+=1;
        $group_id = substr($chart['account_chart_id'],0,1);
        $objPHPExcel->getActiveSheet()->mergeCells('A'.$i.':C'.$i);
        $objPHPExcel->getActiveSheet()->SetCellValue('A' . $i , $chart["account_chart"]);
        $objPHPExcel->getActiveSheet()->SetCellValue('D' . $i , "");
        $objPHPExcel->getActiveSheet()->SetCellValue('F' . $i , "");
        $objPHPExcel->getActiveSheet()->SetCellValue('H' . $i , "");
        $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':H'.$i)->applyFromArray($headerStyle);
        $objPHPExcel->getActiveSheet()->getStyle('D'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    }
}

if($group_id == 4) {
	$i++;
	$objPHPExcel->getActiveSheet()->mergeCells('A'.$i.':C'.$i);
	$objPHPExcel->getActiveSheet()->SetCellValue('A' . $i , "รวมรายได้") ;
	$objPHPExcel->getActiveSheet()->SetCellValue('D' . $i , "") ;
	$objPHPExcel->getActiveSheet()->SetCellValue('F' . $i , !empty($profit_total) ? number_format($profit_total,2) : "0.00" ) ;
	$objPHPExcel->getActiveSheet()->SetCellValue('H' . $i , !empty($p_profit_total) ? number_format($p_profit_total,2) : "0.00" ) ;
	$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':D'.$i)->applyFromArray($headerStyle);
	$objPHPExcel->getActiveSheet()->getStyle('F'.$i.':H'.$i)->applyFromArray($headerUStyle);
	$objPHPExcel->getActiveSheet()->getStyle('D'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
} else if ($group_id == 5) {
	$i++;
	$objPHPExcel->getActiveSheet()->mergeCells('A'.$i.':C'.$i);
	$objPHPExcel->getActiveSheet()->SetCellValue('A' . $i , "รวมค่าใช้จ่าย") ;
	$objPHPExcel->getActiveSheet()->SetCellValue('D' . $i , "") ;
	$objPHPExcel->getActiveSheet()->SetCellValue('F' . $i , !empty($loss_total) ? number_format($loss_total,2) : "0.00" ) ;
	$objPHPExcel->getActiveSheet()->SetCellValue('H' . $i , !empty($p_loss_total) ? number_format($p_loss_total,2) : "0.00" ) ;
	$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':D'.$i)->applyFromArray($headerStyle);
	$objPHPExcel->getActiveSheet()->getStyle('F'.$i.':H'.$i)->applyFromArray($headerUStyle);
	$objPHPExcel->getActiveSheet()->getStyle('D'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

	$balance = $profit_total - $loss_total;
	$p_balance = $p_profit_total - $p_loss_total;
	$i++;
	$objPHPExcel->getActiveSheet()->mergeCells('A'.$i.':C'.$i);
	$objPHPExcel->getActiveSheet()->SetCellValue('A' . $i , "กำไรสุทธิ") ;
	$objPHPExcel->getActiveSheet()->SetCellValue('D' . $i , "") ;
	$objPHPExcel->getActiveSheet()->SetCellValue('F' . $i , !empty($balance) ? number_format($balance,2) : "0.00" ) ;
	$objPHPExcel->getActiveSheet()->SetCellValue('H' . $i , !empty($p_balance) ? number_format($p_balance,2) : "0.00" ) ;
	$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':D'.$i)->applyFromArray($headerStyle);
	$objPHPExcel->getActiveSheet()->getStyle('F'.$i.':H'.$i)->applyFromArray($headerDUStyle);
	$objPHPExcel->getActiveSheet()->getStyle('D'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
}

$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(3.57);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(3);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(39);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(7.57);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(2.43);
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(16.86);
$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(1.86);
$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(17);

$objPHPExcel->getActiveSheet()->setTitle('sheet',2,2);
$sheet++;

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="รายงานงบกำไรขาดทุน.xlsx"');
header('Cache-Control: max-age=0');
		
$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
$objWriter->save('php://output');
exit;	
?>