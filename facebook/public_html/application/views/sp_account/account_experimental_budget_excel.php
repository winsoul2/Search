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
		'size'  => 16,
		'name'  => 'Cordia New'
	)
);
$styleBoldarray = array(
  'borders' => array(
    'allborders' => array(
      'style' => PHPExcel_Style_Border::BORDER_THIN
    )
  ),
  'font'  => array(
		'bold'  => true,
		'size'  => 16,
		'name'  => 'Cordia New'
	)
);
$styleTotalarray = array(
  'borders' => array(
    'allborders' => array(
      'style' => PHPExcel_Style_Border::BORDER_MEDIUM
    )
  ),
  'font'  => array(
		'bold'  => true,
		'size'  => 16,
		'name'  => 'Cordia New'
	)
);
$textStyleArray = array(
  'font'  => array(
		'bold'  => false,
		'size'  => 16,
		'name'  => 'Angsana New'
	)
);
$headerStyle = array(
	'font'  => array(
		'bold'  => false,
		'size'  => 16,
		'name'  => 'Angsana New'
	)
);
$titleStyle = array(
	'font'  => array(
		'bold'  => true,
		'size'  => 18,
		'name'  => 'TH Sarabun New'
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
$objPHPExcel->getActiveSheet()->mergeCells('A'.$i.':I'.$i);
$objPHPExcel->getActiveSheet()->SetCellValue('A' . $i , $cremation['full_name'] ) ;
$objPHPExcel->getActiveSheet()->getStyle('A'.$i)->applyFromArray($titleStyle);
$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':I'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

$i+=1;
$objPHPExcel->getActiveSheet()->mergeCells('A'.$i.':I'.$i);
$objPHPExcel->getActiveSheet()->SetCellValue('A' . $i , "งบทดลอง" ) ;
$objPHPExcel->getActiveSheet()->getStyle('A'.$i)->applyFromArray($titleStyle);
$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':I'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

$i+=1;
$objPHPExcel->getActiveSheet()->mergeCells('A'.$i.':I'.$i);
$objPHPExcel->getActiveSheet()->SetCellValue('A' . $i , $textTitle ) ;
$objPHPExcel->getActiveSheet()->getStyle('A'.$i)->applyFromArray($titleStyle);
$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':I'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

$i+=1;
$i_top = $i;
$objPHPExcel->getActiveSheet()->mergeCells('A'.$i.':A'.($i+1));
$objPHPExcel->getActiveSheet()->SetCellValue('A'.$i, "ลำดับที่" );
$objPHPExcel->getActiveSheet()->mergeCells('B'.$i.':B'.($i+1));
$objPHPExcel->getActiveSheet()->SetCellValue('B'.$i, "เลขที่บัญชี" );
$objPHPExcel->getActiveSheet()->mergeCells('C'.$i.':C'.($i+1));
$objPHPExcel->getActiveSheet()->SetCellValue('C'.$i, "ชื่อบัญชี" );

$objPHPExcel->getActiveSheet()->mergeCells('D'.$i.':E'.$i);
$objPHPExcel->getActiveSheet()->SetCellValue('D'.$i , "ยอดยกมาเดือนก่อนคงเหลือ");
$objPHPExcel->getActiveSheet()->mergeCells('F'.$i.':G'.$i);
$objPHPExcel->getActiveSheet()->SetCellValue('F'.$i , "รายการระหว่างเดือน");
$objPHPExcel->getActiveSheet()->mergeCells('H'.$i.':I'.$i);
$objPHPExcel->getActiveSheet()->SetCellValue('H'.$i , "ยอดคงเหลือยกไป");
$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':I'.$i)->applyFromArray($headerStyle);

$i+=1;
$i_bottom = $i;
$objPHPExcel->getActiveSheet()->SetCellValue('D'.$i, "เดบิต" ) ;
$objPHPExcel->getActiveSheet()->SetCellValue('E'.$i, "เครดิต" ) ;
$objPHPExcel->getActiveSheet()->SetCellValue('F'.$i, "เดบิต" ) ;
$objPHPExcel->getActiveSheet()->SetCellValue('G'.$i, "เครดิต" ) ;
$objPHPExcel->getActiveSheet()->SetCellValue('H'.$i, "เดบิต" ) ;
$objPHPExcel->getActiveSheet()->SetCellValue('I'.$i, "เครดิต" ) ;

$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(13.43);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(10);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(57.86);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(19.29);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(19.29);
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(19.29);
$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(19.29);
$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(19.29);
$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(19.29);

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

$objPHPExcel->getActiveSheet()->getStyle('A'.$i_top.':I'.$i_bottom)->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('A'.$i_top.':I'.$i_bottom)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

$data = array();
$data_sum = array();

$sum_group_amount_debit  = 0;
$sum_group_amount_credit = 0;
$sum_group_amount_credit_ledger  = 0;
$sum_group_amount_debit_ledger  = 0;
$sum_group_amount_credit_budget  = 0;
$sum_group_amount_debit_budget  = 0;
$index = 0;
$prev_group_account_id = "";
foreach(@$data_chart as $key => $value){
    $debit_hirtorical = ($prev_budgets[$value['account_chart_id']]['budget_type'] == 'debit' && $prev_budgets[$value['account_chart_id']]['budget_amount'] > 0)
                            || ($prev_budgets[$value['account_chart_id']]['budget_type'] == 'credit' && $prev_budgets[$value['account_chart_id']]['budget_amount'] < 0)
                            ? abs($prev_budgets[$value['account_chart_id']]['budget_amount']) : 0;
    $credit_hirtorical = ($prev_budgets[$value['account_chart_id']]['budget_type'] == 'credit' && $prev_budgets[$value['account_chart_id']]['budget_amount'] > 0)
                            || ($prev_budgets[$value['account_chart_id']]['budget_type'] == 'debit' && $prev_budgets[$value['account_chart_id']]['budget_amount'] < 0)
                            ? abs($prev_budgets[$value['account_chart_id']]['budget_amount']) : 0;
    $debit_current = $rs[$value['account_chart_id']]['debit'] != 0 ? $rs[$value['account_chart_id']]['debit'] : 0;
    $credit_current = $rs[$value['account_chart_id']]['credit'] != 0 ? $rs[$value['account_chart_id']]['credit'] : 0;

    $diff_current = $debit_current - $credit_current;
    $debit_balance = $prev_budgets[$value['account_chart_id']]['budget_type'] == 'debit' && $debit_hirtorical - $credit_hirtorical + $debit_current - $credit_current > 0 ? $debit_hirtorical - $credit_hirtorical + $debit_current - $credit_current
                        : ($prev_budgets[$value['account_chart_id']]['budget_type'] == 'credit' && $credit_hirtorical - $debit_hirtorical - $debit_current + $credit_current < 0 ? ($credit_hirtorical - $debit_hirtorical - $debit_current + $credit_current) * (-1)
                        : 0);
    $credit_balance = $prev_budgets[$value['account_chart_id']]['budget_type'] == 'credit' && $credit_hirtorical - $debit_hirtorical - $debit_current + $credit_current > 0 ? $credit_hirtorical - $debit_hirtorical - $debit_current + $credit_current
                        : ($prev_budgets[$value['account_chart_id']]['budget_type'] == 'debit' && $debit_hirtorical - $credit_hirtorical + $debit_current - $credit_current < 0 ? ($debit_hirtorical - $credit_hirtorical + $debit_current - $credit_current) * (-1)
                        : 0);

    if(!empty($debit_hirtorical) || !empty($credit_hirtorical) || !empty($debit_current) || !empty($credit_current) || $value["type"] == 1) {
        if($group_account_id != substr($value['account_chart_id'],0,1)){
            if($group_account_id == 1) {
                $i += 1;
                $objPHPExcel->getActiveSheet()->SetCellValue('A' . $i, '');
                $objPHPExcel->getActiveSheet()->SetCellValue('B' . $i, '');
                $objPHPExcel->getActiveSheet()->SetCellValue('C' . $i, 'รวมสินทรัพย์');
                $objPHPExcel->getActiveSheet()->SetCellValue('D' . $i, !empty($sum_group_amount_debit) ? number_format($sum_group_amount_debit,2) : "-");
                $objPHPExcel->getActiveSheet()->SetCellValue('E' . $i, !empty($sum_group_amount_credit) ? number_format($sum_group_amount_credit,2) : "-");
                $objPHPExcel->getActiveSheet()->SetCellValue('F' . $i, !empty($sum_group_amount_debit_ledger) ? number_format($sum_group_amount_debit_ledger,2) : "-");
                $objPHPExcel->getActiveSheet()->SetCellValue('G' . $i, !empty($sum_group_amount_credit_ledger) ? number_format($sum_group_amount_credit_ledger,2) : "-");
                $objPHPExcel->getActiveSheet()->SetCellValue('H' . $i, !empty($sum_group_amount_debit_budget) ? number_format($sum_group_amount_debit_budget,2) : "-");
                $objPHPExcel->getActiveSheet()->SetCellValue('I' . $i, !empty($sum_group_amount_credit_budget) ? number_format($sum_group_amount_credit_budget,2) : "-");
                $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':I'.$i)->applyFromArray($styleTotalarray);
                $objPHPExcel->getActiveSheet()->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $objPHPExcel->getActiveSheet()->getStyle('D'.$i.':I'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                $sum_group_amount_debit  = 0;
                $sum_group_amount_credit = 0;
                $sum_group_amount_credit_ledger  = 0;
                $sum_group_amount_debit_ledger  = 0;
                $sum_group_amount_credit_budget  = 0;
                $sum_group_amount_debit_budget  = 0;
            }else if($group_account_id == 2){
                $i += 1;
                $objPHPExcel->getActiveSheet()->SetCellValue('A' . $i, '');
                $objPHPExcel->getActiveSheet()->SetCellValue('B' . $i, '');
                $objPHPExcel->getActiveSheet()->SetCellValue('C' . $i, 'รวมหนี้สิน');
                $objPHPExcel->getActiveSheet()->SetCellValue('D' . $i, !empty($sum_group_amount_debit) ? number_format($sum_group_amount_debit,2) : "-");
                $objPHPExcel->getActiveSheet()->SetCellValue('E' . $i, !empty($sum_group_amount_credit) ? number_format($sum_group_amount_credit,2) : "-");
                $objPHPExcel->getActiveSheet()->SetCellValue('F' . $i, !empty($sum_group_amount_debit_ledger) ? number_format($sum_group_amount_debit_ledger,2) : "-");
                $objPHPExcel->getActiveSheet()->SetCellValue('G' . $i, !empty($sum_group_amount_credit_ledger) ? number_format($sum_group_amount_credit_ledger,2) : "-");
                $objPHPExcel->getActiveSheet()->SetCellValue('H' . $i, !empty($sum_group_amount_debit_budget) ? number_format($sum_group_amount_debit_budget,2) : "-");
                $objPHPExcel->getActiveSheet()->SetCellValue('I' . $i, !empty($sum_group_amount_credit_budget) ? number_format($sum_group_amount_credit_budget,2) : "-");
                $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':I'.$i)->applyFromArray($styleTotalarray);
                $objPHPExcel->getActiveSheet()->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $objPHPExcel->getActiveSheet()->getStyle('D'.$i.':I'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                $sum_group_amount_debit  = 0;
                $sum_group_amount_credit = 0;
                $sum_group_amount_credit_ledger  = 0;
                $sum_group_amount_debit_ledger  = 0;
                $sum_group_amount_credit_budget  = 0;
                $sum_group_amount_debit_budget  = 0;
            }else if($group_account_id == 3){
                $i += 1;
                $objPHPExcel->getActiveSheet()->SetCellValue('A' . $i, '');
                $objPHPExcel->getActiveSheet()->SetCellValue('B' . $i, '');
                $objPHPExcel->getActiveSheet()->SetCellValue('C' . $i, 'รวมทุน');
                $objPHPExcel->getActiveSheet()->SetCellValue('D' . $i, !empty($sum_group_amount_debit) ? number_format($sum_group_amount_debit,2) : "-");
                $objPHPExcel->getActiveSheet()->SetCellValue('E' . $i, !empty($sum_group_amount_credit) ? number_format($sum_group_amount_credit,2) : "-");
                $objPHPExcel->getActiveSheet()->SetCellValue('F' . $i, !empty($sum_group_amount_debit_ledger) ? number_format($sum_group_amount_debit_ledger,2) : "-");
                $objPHPExcel->getActiveSheet()->SetCellValue('G' . $i, !empty($sum_group_amount_credit_ledger) ? number_format($sum_group_amount_credit_ledger,2) : "-");
                $objPHPExcel->getActiveSheet()->SetCellValue('H' . $i, !empty($sum_group_amount_debit_budget) ? number_format($sum_group_amount_debit_budget,2) : "-");
                $objPHPExcel->getActiveSheet()->SetCellValue('I' . $i, !empty($sum_group_amount_credit_budget) ? number_format($sum_group_amount_credit_budget,2) : "-");
                $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':I'.$i)->applyFromArray($styleTotalarray);
                $objPHPExcel->getActiveSheet()->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $objPHPExcel->getActiveSheet()->getStyle('D'.$i.':I'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                $sum_group_amount_debit  = 0;
                $sum_group_amount_credit = 0;
                $sum_group_amount_credit_ledger  = 0;
                $sum_group_amount_debit_ledger  = 0;
                $sum_group_amount_credit_budget  = 0;
                $sum_group_amount_debit_budget  = 0;
            }else if($group_account_id == 4){
                $i += 1;
                $objPHPExcel->getActiveSheet()->SetCellValue('A' . $i, '');
                $objPHPExcel->getActiveSheet()->SetCellValue('B' . $i, '');
                $objPHPExcel->getActiveSheet()->SetCellValue('C' . $i, 'รวมรายได้');
                $objPHPExcel->getActiveSheet()->SetCellValue('D' . $i, !empty($sum_group_amount_debit) ? number_format($sum_group_amount_debit,2) : "-");
                $objPHPExcel->getActiveSheet()->SetCellValue('E' . $i, !empty($sum_group_amount_credit) ? number_format($sum_group_amount_credit,2) : "-");
                $objPHPExcel->getActiveSheet()->SetCellValue('F' . $i, !empty($sum_group_amount_debit_ledger) ? number_format($sum_group_amount_debit_ledger,2) : "-");
                $objPHPExcel->getActiveSheet()->SetCellValue('G' . $i, !empty($sum_group_amount_credit_ledger) ? number_format($sum_group_amount_credit_ledger,2) : "-");
                $objPHPExcel->getActiveSheet()->SetCellValue('H' . $i, !empty($sum_group_amount_debit_budget) ? number_format($sum_group_amount_debit_budget,2) : "-");
                $objPHPExcel->getActiveSheet()->SetCellValue('I' . $i, !empty($sum_group_amount_credit_budget) ? number_format($sum_group_amount_credit_budget,2) : "-");
                $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':I'.$i)->applyFromArray($styleTotalarray);
                $objPHPExcel->getActiveSheet()->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $objPHPExcel->getActiveSheet()->getStyle('D'.$i.':I'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                $sum_group_amount_debit  = 0;
                $sum_group_amount_credit = 0;
                $sum_group_amount_credit_ledger  = 0;
                $sum_group_amount_debit_ledger  = 0;
                $sum_group_amount_credit_budget  = 0;
                $sum_group_amount_debit_budget  = 0;
            }else if($group_account_id == 5){
                $i += 1;
                $objPHPExcel->getActiveSheet()->SetCellValue('A' . $i, '');
                $objPHPExcel->getActiveSheet()->SetCellValue('B' . $i, '');
                $objPHPExcel->getActiveSheet()->SetCellValue('C' . $i, 'รวมค่าใช้จ่าย');
                $objPHPExcel->getActiveSheet()->SetCellValue('D' . $i, !empty($sum_group_amount_debit) ? number_format($sum_group_amount_debit,2) : "-");
                $objPHPExcel->getActiveSheet()->SetCellValue('E' . $i, !empty($sum_group_amount_credit) ? number_format($sum_group_amount_credit,2) : "-");
                $objPHPExcel->getActiveSheet()->SetCellValue('F' . $i, !empty($sum_group_amount_debit_ledger) ? number_format($sum_group_amount_debit_ledger,2) : "-");
                $objPHPExcel->getActiveSheet()->SetCellValue('G' . $i, !empty($sum_group_amount_credit_ledger) ? number_format($sum_group_amount_credit_ledger,2) : "-");
                $objPHPExcel->getActiveSheet()->SetCellValue('H' . $i, !empty($sum_group_amount_debit_budget) ? number_format($sum_group_amount_debit_budget,2) : "-");
                $objPHPExcel->getActiveSheet()->SetCellValue('I' . $i, !empty($sum_group_amount_credit_budget) ? number_format($sum_group_amount_credit_budget,2) : "-");
                $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':I'.$i)->applyFromArray($styleTotalarray);
                $objPHPExcel->getActiveSheet()->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $objPHPExcel->getActiveSheet()->getStyle('D'.$i.':I'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                $sum_group_amount_debit  = 0;
                $sum_group_amount_credit = 0;
                $sum_group_amount_credit_ledger  = 0;
                $sum_group_amount_debit_ledger  = 0;
                $sum_group_amount_credit_budget  = 0;
                $sum_group_amount_debit_budget  = 0;
            }
        }

        $group_account_id = substr($value['account_chart_id'],0,1);

        $i+=1;
        if($value["type"] == 1) {
            $objPHPExcel->getActiveSheet()->SetCellValue('A' . $i , "");
            $objPHPExcel->getActiveSheet()->SetCellValue('B' . $i , "");
            $objPHPExcel->getActiveSheet()->SetCellValue('C' . $i , $value['account_chart'] );
            $objPHPExcel->getActiveSheet()->SetCellValue('D' . $i , "");
            $objPHPExcel->getActiveSheet()->SetCellValue('E' . $i , "");
            $objPHPExcel->getActiveSheet()->SetCellValue('F' . $i , "");
            $objPHPExcel->getActiveSheet()->SetCellValue('G' . $i , "");
            $objPHPExcel->getActiveSheet()->SetCellValue('H' . $i , "");
            $objPHPExcel->getActiveSheet()->SetCellValue('I' . $i , "");
            $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':I'.$i)->applyFromArray($styleBoldarray);
            $objPHPExcel->getActiveSheet()->getStyle('C' . $i)->getFont()->setUnderline(true);
            $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':B'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $objPHPExcel->getActiveSheet()->getStyle('D'.$i.':I'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
        } else {
            $objPHPExcel->getActiveSheet()->SetCellValue('A' . $i , ++$index );
            $objPHPExcel->getActiveSheet()->SetCellValue('B' . $i , $value['account_chart_id'] );
            $objPHPExcel->getActiveSheet()->SetCellValue('C' . $i , $value['account_chart'] );
            $objPHPExcel->getActiveSheet()->SetCellValue('D' . $i , $debit_hirtorical > 0 ? number_format($debit_hirtorical, 2) : '-' );
            $objPHPExcel->getActiveSheet()->SetCellValue('E' . $i , $credit_hirtorical > 0 ? number_format($credit_hirtorical, 2) : '-' );
            $objPHPExcel->getActiveSheet()->SetCellValue('F' . $i , $debit_current != 0 ? number_format($debit_current, 2): '-' );
            $objPHPExcel->getActiveSheet()->SetCellValue('G' . $i , $credit_current != 0 ? number_format($credit_current, 2): '-' );
            $objPHPExcel->getActiveSheet()->SetCellValue('H' . $i , $debit_balance != 0 ? number_format($debit_balance, 2): '-' );
            $objPHPExcel->getActiveSheet()->SetCellValue('I' . $i , $credit_balance != 0 ? number_format($credit_balance, 2): '-' );
            $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':I'.$i)->applyFromArray($styleArray);
            $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':B'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $objPHPExcel->getActiveSheet()->getStyle('D'.$i.':I'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

            $sum_group_amount_debit += $debit_hirtorical;
            $sum_group_amount_credit += $credit_hirtorical;
            $sum_group_amount_debit_ledger  += $debit_current;
            $sum_group_amount_credit_ledger  += $credit_current;
            $sum_group_amount_credit_budget  += $credit_balance;
            $sum_group_amount_debit_budget  += $debit_balance;

            $data_sum['debit_hirtorical'] += $debit_hirtorical;
            $data_sum['credit_hirtorical'] += $credit_hirtorical;
            $data_sum['debit'] += $debit_current;
            $data_sum['credit'] += $credit_current;
            $data_sum['carryfordard_debit'] += $debit_balance;
            $data_sum['carryfordard_credit'] += $credit_balance;
        }
    }
}
if($group_account_id == 5){
    $i += 1;
    $objPHPExcel->getActiveSheet()->SetCellValue('A' . $i, '');
    $objPHPExcel->getActiveSheet()->SetCellValue('B' . $i, '');
    $objPHPExcel->getActiveSheet()->SetCellValue('C' . $i, 'รวมค่าใช้จ่าย');
    $objPHPExcel->getActiveSheet()->SetCellValue('D' . $i, !empty($sum_group_amount_debit) ? number_format($sum_group_amount_debit,2) : "-");
    $objPHPExcel->getActiveSheet()->SetCellValue('E' . $i, !empty($sum_group_amount_credit) ? number_format($sum_group_amount_credit,2) : "-");
    $objPHPExcel->getActiveSheet()->SetCellValue('F' . $i, !empty($sum_group_amount_debit_ledger) ? number_format($sum_group_amount_debit_ledger,2) : "-");
    $objPHPExcel->getActiveSheet()->SetCellValue('G' . $i, !empty($sum_group_amount_credit_ledger) ? number_format($sum_group_amount_credit_ledger,2) : "-");
    $objPHPExcel->getActiveSheet()->SetCellValue('H' . $i, !empty($sum_group_amount_debit_budget) ? number_format($sum_group_amount_debit_budget,2) : "-");
    $objPHPExcel->getActiveSheet()->SetCellValue('I' . $i, !empty($sum_group_amount_credit_budget) ? number_format($sum_group_amount_credit_budget,2) : "-");
    $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':I'.$i)->applyFromArray($styleTotalarray);
    $objPHPExcel->getActiveSheet()->getStyle('A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $objPHPExcel->getActiveSheet()->getStyle('D'.$i.':I'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
    $sum_group_amount_debit  = 0;
    $sum_group_amount_credit = 0;
    $sum_group_amount_credit_ledger  = 0;
    $sum_group_amount_debit_ledger  = 0;
    $sum_group_amount_credit_budget  = 0;
    $sum_group_amount_debit_budget  = 0;
}

$i+=1;
$objPHPExcel->getActiveSheet()->SetCellValue('C' . $i , 'รวม' );
$objPHPExcel->getActiveSheet()->SetCellValue('D' . $i , $data_sum['debit_hirtorical'] != 0 ? number_format($data_sum['debit_hirtorical'],2) : '-' );
$objPHPExcel->getActiveSheet()->SetCellValue('E' . $i , $data_sum['credit_hirtorical'] != 0 ? number_format($data_sum['credit_hirtorical'],2) : '-' );
$objPHPExcel->getActiveSheet()->SetCellValue('F' . $i , $data_sum['debit'] !=0 ? number_format($data_sum['debit'],2) : '-' );
$objPHPExcel->getActiveSheet()->SetCellValue('G' . $i , $data_sum['credit'] !=0 ? number_format($data_sum['credit'],2) : '-' );
$objPHPExcel->getActiveSheet()->SetCellValue('H' . $i , $data_sum['carryfordard_debit'] != 0 ? number_format($data_sum['carryfordard_debit'],2) : '-' );
$objPHPExcel->getActiveSheet()->SetCellValue('I' . $i , $data_sum['carryfordard_credit'] !=0 ? number_format($data_sum['carryfordard_credit'],2) : '-' );
$objPHPExcel->getActiveSheet()->getStyle('A'.$i.':I'.$i)->applyFromArray($styleTotalarray);
$objPHPExcel->getActiveSheet()->getStyle('B'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle('D'.$i.':I'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

$objPHPExcel->getActiveSheet()->setTitle('sheet',2,2);
$sheet++;

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="รายงานงบทดลอง.xlsx"');
header('Cache-Control: max-age=0');

$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
$objWriter->save('php://output');
exit;
?>