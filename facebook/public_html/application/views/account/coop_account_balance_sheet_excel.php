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
$objPHPExcel->getActiveSheet()->SetCellValue('A' . $i , "งบแสดงฐานะการเงิน" ) ; 
$objPHPExcel->getActiveSheet()->getStyle('A'.$i)->applyFromArray($titleStyle);
$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':H'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$i+=1;
$i_title = $i;
$objPHPExcel->getActiveSheet()->mergeCells('A'.$i.':H'.$i);
$objPHPExcel->getActiveSheet()->SetCellValue('A' . $i , "ณ วันที่ ".$this->center_function->ConvertToThaiDate($thur_date,'0','0')." และ วันที่ ".$this->center_function->ConvertToThaiDate($prev_date,'0','0')); 
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
$group_total = 0;
$group_all_total = 0;
$p_group_total = 0;
$p_group_all_total = 0;
foreach($account_charts as $chart) {
    $amount = $year_budgets[$chart["account_chart_id"]];
    $prev_amount = $prev_year_budgets[$chart["account_chart_id"]];

    if($index > 1 && $group_id != substr($chart['account_chart_id'],0,1)) {
        // headerDUStyle.
        // $headerUStyle
        if($group_id == 1) {
            $i++;
            $objPHPExcel->getActiveSheet()->mergeCells('A'.$i.':C'.$i);
            $objPHPExcel->getActiveSheet()->SetCellValue('A' . $i , "รวมสินทรัพย์") ;
            $objPHPExcel->getActiveSheet()->SetCellValue('D' . $i , "") ;
            $objPHPExcel->getActiveSheet()->SetCellValue('F' . $i , !empty($group_total) ? number_format($group_total,2) : "0.00" ) ;
            $objPHPExcel->getActiveSheet()->SetCellValue('H' . $i , !empty($p_group_total) ? number_format($p_group_total,2) : "0.00" ) ;
            $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':D'.$i)->applyFromArray($headerStyle);
            $objPHPExcel->getActiveSheet()->getStyle('F'.$i.':H'.$i)->applyFromArray($headerUStyle);
            $objPHPExcel->getActiveSheet()->getStyle('D'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

            $i++;
            $objPHPExcel->getActiveSheet()->mergeCells('A'.$i.':C'.$i);
            $objPHPExcel->getActiveSheet()->SetCellValue('A' . $i , "") ;
            $objPHPExcel->getActiveSheet()->SetCellValue('D' . $i , "") ;
            $objPHPExcel->getActiveSheet()->SetCellValue('F' . $i , !empty($group_all_total) ? number_format($group_all_total,2) : "0.00" ) ;
            $objPHPExcel->getActiveSheet()->SetCellValue('H' . $i , !empty($p_group_all_total) ? number_format($p_group_all_total,2) : "0.00" ) ;
            $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':H'.$i)->applyFromArray($headerDUStyle);
            $objPHPExcel->getActiveSheet()->getStyle('D'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

            $group_total = 0;
            $group_all_total = 0;
            $p_group_total = 0;
            $p_group_all_total = 0;
        } else if ($group_id == 2) {
            $i++;
            $objPHPExcel->getActiveSheet()->mergeCells('A'.$i.':C'.$i);
            $objPHPExcel->getActiveSheet()->SetCellValue('A' . $i , "รวมหนี้สิน") ;
            $objPHPExcel->getActiveSheet()->SetCellValue('D' . $i , "") ;
            $objPHPExcel->getActiveSheet()->SetCellValue('F' . $i , !empty($group_total) ? number_format($group_total,2) : "0.00" ) ;
            $objPHPExcel->getActiveSheet()->SetCellValue('H' . $i , !empty($p_group_total) ? number_format($p_group_total,2) : "0.00" ) ;
            $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':D'.$i)->applyFromArray($headerStyle);
            $objPHPExcel->getActiveSheet()->getStyle('F'.$i.':H'.$i)->applyFromArray($headerUStyle);
            $objPHPExcel->getActiveSheet()->getStyle('D'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

            $group_total = 0;
            $p_group_total = 0;
        } else if ($group_id == 3) {
            $i++;
            $objPHPExcel->getActiveSheet()->mergeCells('A'.$i.':C'.$i);
            $objPHPExcel->getActiveSheet()->SetCellValue('A' . $i , "รวมทุน") ;
            $objPHPExcel->getActiveSheet()->SetCellValue('D' . $i , "") ;
            $objPHPExcel->getActiveSheet()->SetCellValue('F' . $i , !empty($group_total) ? number_format($group_total,2) : "0.00" ) ;
            $objPHPExcel->getActiveSheet()->SetCellValue('H' . $i , !empty($p_group_total) ? number_format($p_group_total,2) : "0.00" ) ;
            $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':D'.$i)->applyFromArray($headerStyle);
            $objPHPExcel->getActiveSheet()->getStyle('F'.$i.':H'.$i)->applyFromArray($headerUStyle);
            $objPHPExcel->getActiveSheet()->getStyle('D'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

            $i++;
            $objPHPExcel->getActiveSheet()->mergeCells('A'.$i.':C'.$i);
            $objPHPExcel->getActiveSheet()->SetCellValue('A' . $i , "รวมหนี้สินและทุน") ;
            $objPHPExcel->getActiveSheet()->SetCellValue('D' . $i , "") ;
            $objPHPExcel->getActiveSheet()->SetCellValue('F' . $i , !empty($group_all_total) ? number_format($group_all_total,2) : "0.00" ) ;
            $objPHPExcel->getActiveSheet()->SetCellValue('H' . $i , !empty($p_group_all_total) ? number_format($p_group_all_total,2) : "0.00" ) ;
            $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':D'.$i)->applyFromArray($headerStyle);
            $objPHPExcel->getActiveSheet()->getStyle('F'.$i.':H'.$i)->applyFromArray($headerDUStyle);
            $objPHPExcel->getActiveSheet()->getStyle('D'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

            $group_total = 0;
            $group_all_total = 0;
            $p_group_total = 0;
            $p_group_all_total = 0;
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

        $group_total += $amount;
        $group_all_total += $amount;
        $p_group_total += $prev_amount;
        $p_group_all_total += $prev_amount;
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

if($group_id == 1) {
    $i++;
    $objPHPExcel->getActiveSheet()->mergeCells('A'.$i.':C'.$i);
    $objPHPExcel->getActiveSheet()->SetCellValue('A' . $i , "รวมทรัพย์สิน") ;
    $objPHPExcel->getActiveSheet()->SetCellValue('D' . $i , "") ;
    $objPHPExcel->getActiveSheet()->SetCellValue('F' . $i , !empty($group_total) ? number_format($group_total,2) : "0.00" ) ;
    $objPHPExcel->getActiveSheet()->SetCellValue('H' . $i , !empty($p_group_total) ? number_format($p_group_total,2) : "0.00" ) ;
    $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':D'.$i)->applyFromArray($headerStyle);
    $objPHPExcel->getActiveSheet()->getStyle('F'.$i.':H'.$i)->applyFromArray($headerUStyle);
    $objPHPExcel->getActiveSheet()->getStyle('D'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

    $i++;
    $objPHPExcel->getActiveSheet()->mergeCells('A'.$i.':C'.$i);
    $objPHPExcel->getActiveSheet()->SetCellValue('A' . $i , "") ;
    $objPHPExcel->getActiveSheet()->SetCellValue('D' . $i , "") ;
    $objPHPExcel->getActiveSheet()->SetCellValue('F' . $i , !empty($group_all_total) ? number_format($group_all_total,2) : "0.00" ) ;
    $objPHPExcel->getActiveSheet()->SetCellValue('H' . $i , !empty($p_group_all_total) ? number_format($p_group_all_total,2) : "0.00" ) ;
    $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':H'.$i)->applyFromArray($headerDUStyle);
    $objPHPExcel->getActiveSheet()->getStyle('D'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

    $group_total = 0;
    $group_all_total = 0;
    $p_group_total = 0;
    $p_group_all_total = 0;
} else if ($group_id == 2) {
    $i++;
    $objPHPExcel->getActiveSheet()->mergeCells('A'.$i.':C'.$i);
    $objPHPExcel->getActiveSheet()->SetCellValue('A' . $i , "รวมหนี้สิน") ;
    $objPHPExcel->getActiveSheet()->SetCellValue('D' . $i , "") ;
    $objPHPExcel->getActiveSheet()->SetCellValue('F' . $i , !empty($group_total) ? number_format($group_total,2) : "0.00" ) ;
    $objPHPExcel->getActiveSheet()->SetCellValue('H' . $i , !empty($p_group_total) ? number_format($p_group_total,2) : "0.00" ) ;
    $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':D'.$i)->applyFromArray($headerStyle);
    $objPHPExcel->getActiveSheet()->getStyle('F'.$i.':H'.$i)->applyFromArray($headerUStyle);
    $objPHPExcel->getActiveSheet()->getStyle('D'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

    $group_total = 0;
    $p_group_total = 0;
} else if ($group_id == 3) {
    $i++;
    $objPHPExcel->getActiveSheet()->mergeCells('A'.$i.':C'.$i);
    $objPHPExcel->getActiveSheet()->SetCellValue('A' . $i , "รวมทุน") ;
    $objPHPExcel->getActiveSheet()->SetCellValue('D' . $i , "") ;
    $objPHPExcel->getActiveSheet()->SetCellValue('F' . $i , !empty($group_total) ? number_format($group_total,2) : "0.00" ) ;
    $objPHPExcel->getActiveSheet()->SetCellValue('H' . $i , !empty($p_group_total) ? number_format($p_group_total,2) : "0.00" ) ;
    $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':D'.$i)->applyFromArray($headerStyle);
    $objPHPExcel->getActiveSheet()->getStyle('F'.$i.':H'.$i)->applyFromArray($headerUStyle);
    $objPHPExcel->getActiveSheet()->getStyle('D'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

    $i++;
    $objPHPExcel->getActiveSheet()->mergeCells('A'.$i.':C'.$i);
    $objPHPExcel->getActiveSheet()->SetCellValue('A' . $i , "รวมหนี้สินและทุน") ;
    $objPHPExcel->getActiveSheet()->SetCellValue('D' . $i , "") ;
    $objPHPExcel->getActiveSheet()->SetCellValue('F' . $i , !empty($group_all_total) ? number_format($group_all_total,2) : "0.00" ) ;
    $objPHPExcel->getActiveSheet()->SetCellValue('H' . $i , !empty($p_group_all_total) ? number_format($p_group_all_total,2) : "0.00" ) ;
    $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':D'.$i)->applyFromArray($headerStyle);
    $objPHPExcel->getActiveSheet()->getStyle('F'.$i.':H'.$i)->applyFromArray($headerDUStyle);
    $objPHPExcel->getActiveSheet()->getStyle('D'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

    $group_total = 0;
    $group_all_total = 0;
    $p_group_total = 0;
    $p_group_all_total = 0;
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
header('Content-Disposition: attachment;filename="รายงานงบแสดงฐานะทางการเงิน.xlsx"');
header('Cache-Control: max-age=0');
		
$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
$objWriter->save('php://output');
exit;	
?>