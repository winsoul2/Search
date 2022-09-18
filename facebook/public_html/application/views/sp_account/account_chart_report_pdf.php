<?php
function U2T($text) { return @iconv("UTF-8", "TIS-620//IGNORE", ($text)); }
function num_format($text) {
    if($text!=''){
        return number_format($text,2);
    }else{
        return '';
    }
}

$pdf = new FPDI('P','mm', "A4");
$pdf->AddFont('THSarabunNew', '', 'cordiau.php');
$pdf->AddFont('THSarabunNewB','','cordiaub.php');
$font_size = 14;

$col_x_1 = 10;
$col_w_1 = 22.5;
$col_x_2 = $col_w_1 + $col_x_1;
$col_w_2 = 24;
$col_x_3 = $col_w_2 + $col_x_2;
$col_w_3 = 43.5;
$col_x_4 = $col_w_3 + $col_x_3;
$col_w_4 = 25;
$col_x_5 = $col_w_4 + $col_x_4;
$col_w_5 = 25;
$col_x_6 = $col_w_5 + $col_x_5;
$col_w_6 = 25;
$col_x_7 = $col_w_6 + $col_x_6;
$col_w_7 = 25;

$page_index = 0;
foreach($account_chart_main as $key => $value){
    if(!empty($data[$key])){
		$pdf->AddPage();
		$page_index++;

		$pdf->SetMargins(0, 0, 0);
		$border = 0;
		$pdf->SetTextColor(0, 0, 0);
		$pdf->SetAutoPageBreak(true,0);

		$y_point = 20;
		$pdf->SetFont('THSarabunNew', '', 12);
		$pdf->SetXY( 10, $y_point - 4 );
		$pdf->MultiCell(190, 10, U2T("หน้าที่ ".$page_index), 0, "R");

		$pdf->SetFont('THSarabunNewB', '', 20 );
		$pdf->SetXY( 0, $y_point );
		$pdf->MultiCell(210, 10, U2T($cremation['full_name']), 0, "C");

		$pdf->SetFont('THSarabunNewB', '', $font_size );
		$y_point += 10;
		$pdf->SetXY( 0, $y_point );
		$pdf->MultiCell(210, 8, U2T("บัญชีแยกประเภททั่วไป"), 0, "C");

		$period = "";
		if (!empty($_GET["from_date"]) && !empty($_GET["thru_date"])) {
			if($_GET["from_date"] != $_GET["thru_date"]) $period .= "ตั้งแต่";
			$period .= 'วันที่ '.$this->center_function->ConvertToThaiDate($s_date,false,'0');
			if($_GET["from_date"] != $_GET["thru_date"]) $period .= ' ถึง วันที่ '.$this->center_function->ConvertToThaiDate($e_date,false,'0');
		} else {
			$period .= "ตั้งแต่";
			$period .= 'วันที่ '.$this->center_function->ConvertToThaiDate($s_date,false,'0');
			$period .= ' ถึง วันที่ '.$this->center_function->ConvertToThaiDate($e_date,false,'0');
		}
		
		$y_point += 8;
		$pdf->SetXY( 0, $y_point );
		$pdf->MultiCell(210, 8, U2T($period), 0, "C");

		$pdf->SetFont('THSarabunNewB', '', $font_size );
		$y_point += 15;
		$pdf->SetXY(20, $y_point);
		$pdf->MultiCell(100, 8, U2T("ชื่อบัญชี ".$account_chart_main[$key]), 0, "L");
		$pdf->SetXY(90, $y_point);
		$pdf->MultiCell(100, 8, U2T("เลขที่บัญชี ".$key), 0, "R");

		$y_point += 10;
		$pdf->SetXY($col_x_1, $y_point);
		$pdf->MultiCell($col_w_1, 12, U2T("ว.ด.ป."), 1, "C");
		$pdf->SetXY($col_x_2, $y_point);
		$pdf->MultiCell($col_w_2, 12, U2T("เลขที่อ้างอิง"), 1, "C");
		$pdf->SetXY($col_x_3, $y_point);
		$pdf->MultiCell($col_w_3, 12, U2T("รายการ"), 1, "C");
		$pdf->SetXY($col_x_4, $y_point);
		$pdf->MultiCell($col_w_4, 12, U2T("เดบิต"), 1, "C");
		$pdf->SetXY($col_x_5, $y_point);
		$pdf->MultiCell($col_w_5, 12, U2T("เครดิต"), 1, "C");
		$pdf->SetXY($col_x_6, $y_point);
		$pdf->MultiCell($col_w_6 + $col_w_7, 6, U2T("คงเหลือ"), 1, "C");
		$pdf->SetXY($col_x_6, $y_point+6);
		$pdf->MultiCell($col_w_6, 6, U2T("เดบิต"), 1, "C");
		$pdf->SetXY($col_x_7, $y_point+6);
		$pdf->MultiCell($col_w_7, 6, U2T("เครดิต"), 1, "C");

		$y_point += 12;
        $debit_date = '';
        $credit_date = '';

        $debit_balance = ($account_balances[$key]['type'] == 'debit' && $account_balances[$key]['amount'] > 0) ? $account_balances[$key]['amount']
                            : ($account_balances[$key]['type'] == 'credit' && $account_balances[$key]['amount'] <  0 ? $account_balances[$key]['amount'] * (-1) : "");
        $credit_balance = ($account_balances[$key]['type'] == 'credit' && $account_balances[$key]['amount'] > 0) ? $account_balances[$key]['amount']
                            : ($account_balances[$key]['type'] == 'debit' && $account_balances[$key]['amount'] <  0 ? $account_balances[$key]['amount'] * (-1) : "");

		$pdf->SetFont('THSarabunNew', '', $font_size );

		//Balance
		$budget_amount_sum = $account_balances[$key]['amount'];
		$type_budget = $account_balances[$key]['type'] ? $account_balances[$key]['type'] : '';

		$pdf->SetXY($col_x_1, $y_point);
		$pdf->MultiCell($col_w_1, 7, U2T($this->center_function->ConvertToThaiDate($s_date,'1','0')), 0, "C");
		$pdf->SetXY($col_x_2, $y_point);
		$pdf->MultiCell($col_w_2, 7, U2T(""), 0, "L");
		$pdf->SetXY($col_x_3, $y_point);
		$pdf->MultiCell($col_w_3, 7, U2T("ยอดยกมา"), 0, "L");
		$h = $pdf->GetY();
		$pdf->SetXY($col_x_4, $y_point);
		$pdf->MultiCell($col_w_4, 7, "", 0, "R");
		$h = $pdf->GetY() > $h ? $pdf->GetY() : $h;
		$pdf->SetXY($col_x_5, $y_point);
		$pdf->MultiCell($col_w_5, 7, "", 0, "R");
		$h = $pdf->GetY() > $h ? $pdf->GetY() : $h;
		$pdf->SetXY($col_x_6, $y_point);
		$pdf->MultiCell($col_w_6, 7, !empty($debit_balance) ? number_format($debit_balance, 2) : ($type_budget == "debit" ? "0.00" : ""), 0, "R");
		$h = $pdf->GetY() > $h ? $pdf->GetY() : $h;
		$pdf->SetXY($col_x_7, $y_point);
		$pdf->MultiCell($col_w_7, 7, !empty($credit_balance) ? number_format($credit_balance, 2) : ($type_budget == "credit" ? "0.00" : ""), 0, "R");
		$h = $pdf->GetY() > $h ? $pdf->GetY() : $h;

		$row_height = $h - $y_point;
		$pdf->SetXY($col_x_1, $y_point);
		$pdf->MultiCell($col_w_1, $row_height, "", 1, "C");
		$pdf->SetXY($col_x_2, $y_point);
		$pdf->MultiCell($col_w_2, $row_height, "", 1, "C");
		$pdf->SetXY($col_x_3, $y_point);
		$pdf->MultiCell($col_w_3, $row_height, "", 1, "L");
		$pdf->SetXY($col_x_4, $y_point);
		$pdf->MultiCell($col_w_4, $row_height, "", 1, "R");
		$pdf->SetXY($col_x_5, $y_point);
		$pdf->MultiCell($col_w_5, $row_height, "", 1, "R");
		$pdf->SetXY($col_x_6, $y_point);
		$pdf->MultiCell($col_w_6, $row_height, "", 1, "R");
		$pdf->SetXY($col_x_7, $y_point);
		$pdf->MultiCell($col_w_7, $row_height, "", 1, "R");

		$y_point = $h;

		$sum_debit_balance = 0;
		$sum_credit_balance = 0;

		//Transaction
		foreach($data[$key] as $value_detail) {
			if(($y_point + 42) > 297) {
				$pdf->AddPage();
				$page_index++;

				$y_point = 20;
				$pdf->SetFont('THSarabunNew', '', 12);
				$pdf->SetXY( 10, $y_point - 4 );
				$pdf->MultiCell(190, 10, U2T("หน้าที่ ".$page_index), 0, "R");
		
				$pdf->SetFont('THSarabunNewB', '', 20 );
				$pdf->SetXY( 0, $y_point );
				$pdf->MultiCell(210, 10, U2T($_SESSION['COOP_NAME']), 0, "C");
		
				$pdf->SetFont('THSarabunNewB', '', 16 );
				$y_point += 10;
				$pdf->SetXY( 0, $y_point );
				$pdf->MultiCell(210, 8, U2T("บัญชีแยกประเภททั่วไป"), 0, "C");

				$period = "";
				if (!empty($_GET["from_date"]) && !empty($_GET["thru_date"])) {
					if($_GET["from_date"] != $_GET["thru_date"]) $period .= "ตั้งแต่";
					$period .= 'วันที่ '.$this->center_function->ConvertToThaiDate($s_date,false,'0');
					if($_GET["from_date"] != $_GET["thru_date"]) $period .= ' ถึง วันที่ '.$this->center_function->ConvertToThaiDate($e_date,false,'0');
				} else {
					$period .= "ตั้งแต่";
					$period .= 'วันที่ '.$this->center_function->ConvertToThaiDate($s_date,false,'0');
					$period .= ' ถึง วันที่ '.$this->center_function->ConvertToThaiDate($e_date,false,'0');
				}

				$y_point += 8;
				$pdf->SetXY( 0, $y_point );
				$pdf->MultiCell(210, 8, U2T($period), 0, "C");

				$pdf->SetFont('THSarabunNewB', '', $font_size );
				$y_point += 15;
				$pdf->SetXY(20, $y_point);
				$pdf->MultiCell(100, 8, U2T("ชื่อบัญชี ".$account_chart_main[$key]), 0, "L");
				$pdf->SetXY(90, $y_point);
				$pdf->MultiCell(100, 8, U2T("เลขที่บัญชี ".$key), 0, "R");

				$y_point += 10;
				$pdf->SetXY($col_x_1, $y_point);
				$pdf->MultiCell($col_w_1, 12, U2T("วันที่"), 1, "C");
				$pdf->SetXY($col_x_2, $y_point);
				$pdf->MultiCell($col_w_2, 12, U2T("เลขที่อ้างอิง"), 1, "C");
				$pdf->SetXY($col_x_3, $y_point);
				$pdf->MultiCell($col_w_3, 12, U2T("รายการ"), 1, "C");
				$pdf->SetXY($col_x_4, $y_point);
				$pdf->MultiCell($col_w_4, 12, U2T("เดบิต"), 1, "C");
				$pdf->SetXY($col_x_5, $y_point);
				$pdf->MultiCell($col_w_5, 12, U2T("เครดิต"), 1, "C");
				$pdf->SetXY($col_x_6, $y_point);
				$pdf->MultiCell($col_w_6 + $col_w_7, 6, U2T("คงเหลือ"), 1, "C");
				$pdf->SetXY($col_x_6, $y_point+6);
				$pdf->MultiCell($col_w_6, 6, U2T("เดบิต"), 1, "C");
				$pdf->SetXY($col_x_7, $y_point+6);
				$pdf->MultiCell($col_w_7, 6, U2T("เครดิต"), 1, "C");
				$y_point += 12;

				$pdf->SetFont('THSarabunNew', '', $font_size );
				$pdf->SetXY($col_x_1, $y_point);
				$pdf->MultiCell($col_w_1, 7, U2T($this->center_function->ConvertToThaiDate($s_date,'1','0')), 0, "C");
				$pdf->SetXY($col_x_2, $y_point);
				$pdf->MultiCell($col_w_2, 7, U2T(""), 0, "L");
				$pdf->SetXY($col_x_3, $y_point);
				$pdf->MultiCell($col_w_3, 7, U2T("ยอดยกมา"), 0, "L");
				$h = $pdf->GetY();
				$pdf->SetXY($col_x_4, $y_point);
				$pdf->MultiCell($col_w_4, 7, "", 0, "R");
				$h = $pdf->GetY() > $h ? $pdf->GetY() : $h;
				$pdf->SetXY($col_x_5, $y_point);
				$pdf->MultiCell($col_w_5, 7, "", 0, "R");
				$h = $pdf->GetY() > $h ? $pdf->GetY() : $h;
				$pdf->SetXY($col_x_6, $y_point);
				$pdf->MultiCell($col_w_6, 7,  $type_budget == 'debit' ? number_format($budget_amount_sum,2) : "", 0, "R");
				$h = $pdf->GetY() > $h ? $pdf->GetY() : $h;
				$pdf->SetXY($col_x_7, $y_point);
				$pdf->MultiCell($col_w_7, 7, $type_budget == 'credit' ? number_format($budget_amount_sum,2) : "", 0, "R");
				$h = $pdf->GetY() > $h ? $pdf->GetY() : $h;

				$row_height = $h - $y_point;
				$pdf->SetXY($col_x_1, $y_point);
				$pdf->MultiCell($col_w_1, $row_height, "", 1, "C");
				$pdf->SetXY($col_x_2, $y_point);
				$pdf->MultiCell($col_w_2, $row_height, "", 1, "C");
				$pdf->SetXY($col_x_3, $y_point);
				$pdf->MultiCell($col_w_3, $row_height, "", 1, "L");
				$pdf->SetXY($col_x_4, $y_point);
				$pdf->MultiCell($col_w_4, $row_height, "", 1, "R");
				$pdf->SetXY($col_x_5, $y_point);
				$pdf->MultiCell($col_w_5, $row_height, "", 1, "R");
				$pdf->SetXY($col_x_6, $y_point);
				$pdf->MultiCell($col_w_6, $row_height, "", 1, "R");
				$pdf->SetXY($col_x_7, $y_point);
				$pdf->MultiCell($col_w_7, $row_height, "", 1, "R");
				$y_point = $h;
			}

			if($type_budget == ''){
                $type_budget = $value_detail['account_type'];
            }
            if($value_detail['account_type'] != $type_budget){
                $budget_amount_sum  =  $budget_amount_sum  - $value_detail['account_amount'];
            }else{
                $budget_amount_sum  =  $budget_amount_sum  + $value_detail['account_amount'];
            }
            if($budget_amount_sum <= 0 ){
                $budget_amount_sum = $budget_amount_sum * (-1);

                if($type_budget == 'credit'){
                    $type_budget = 'debit';
                }else{
                    $type_budget = 'credit';
                }
            }

			$pdf->SetXY($col_x_1, $y_point);
			$pdf->MultiCell($col_w_1, 7, U2T($this->center_function->ConvertToThaiDate($value_detail["account_datetime"],'1','0')), 0, "C");
			$pdf->SetXY($col_x_2, $y_point);
			$pdf->MultiCell($col_w_2, 7, U2T($value_detail["journal_ref"]), 0, "C");
			$pdf->SetXY($col_x_3, $y_point);
			$pdf->MultiCell($col_w_3, 7, U2T($value_detail["account_description"]), 0, "L");
			$h = $pdf->GetY();
			$pdf->SetXY($col_x_4, $y_point);
			$pdf->MultiCell($col_w_4, 7, $value_detail["account_type"] == "debit" ? number_format($value_detail["account_amount"],2) : "", 0, "R");
			$h = $pdf->GetY() > $h ? $pdf->GetY() : $h;
			$pdf->SetXY($col_x_5, $y_point);
			$pdf->MultiCell($col_w_5, 7, $value_detail["account_type"] == "credit" ? number_format($value_detail["account_amount"],2) : "", 0, "R");
			$h = $pdf->GetY() > $h ? $pdf->GetY() : $h;
			$pdf->SetXY($col_x_6, $y_point);
			$pdf->MultiCell($col_w_6, 7, $type_budget == 'debit' ? number_format($budget_amount_sum,2) : "", 0, "R");
			$h = $pdf->GetY() > $h ? $pdf->GetY() : $h;
			$pdf->SetXY($col_x_7, $y_point);
			$pdf->MultiCell($col_w_7, 7, $type_budget == 'credit' ? number_format($budget_amount_sum,2) : "", 0, "R");
			$h = $pdf->GetY() > $h ? $pdf->GetY() : $h;

			if($value_detail["account_type"] == "debit") {
				$sum_debit_balance += $value_detail["account_amount"];
			} else {
				$sum_credit_balance += $value_detail["account_amount"];
			}

			$row_height = $h - $y_point;
			$pdf->SetXY($col_x_1, $y_point);
			$pdf->MultiCell($col_w_1, $row_height, "", 1, "C");
			$pdf->SetXY($col_x_2, $y_point);
			$pdf->MultiCell($col_w_2, $row_height, "", 1, "C");
			$pdf->SetXY($col_x_3, $y_point);
			$pdf->MultiCell($col_w_3, $row_height, "", 1, "L");
			$pdf->SetXY($col_x_4, $y_point);
			$pdf->MultiCell($col_w_4, $row_height, "", 1, "R");
			$pdf->SetXY($col_x_5, $y_point);
			$pdf->MultiCell($col_w_5, $row_height, "", 1, "R");
			$pdf->SetXY($col_x_6, $y_point);
			$pdf->MultiCell($col_w_6, $row_height, "", 1, "R");
			$pdf->SetXY($col_x_7, $y_point);
			$pdf->MultiCell($col_w_7, $row_height, "", 1, "R");

			$y_point = $h;
		}

		$pdf->SetFont('THSarabunNewB', '', $font_size );
		$pdf->SetXY($col_x_1, $y_point);
		$pdf->MultiCell($col_w_1 + $col_w_2 + $col_w_3, 7, U2T("รวม"), 0, "C");
		$h = $pdf->GetY();
		$pdf->SetXY($col_x_4, $y_point);
		$pdf->MultiCell($col_w_4, 7, number_format($sum_debit_balance,2), 0, "R");
		$h = $pdf->GetY() > $h ? $pdf->GetY() : $h;
		$pdf->SetXY($col_x_5, $y_point);
		$pdf->MultiCell($col_w_5, 7, number_format($sum_credit_balance,2), 0, "R");
		$h = $pdf->GetY() > $h ? $pdf->GetY() : $h;
		$pdf->SetXY($col_x_6, $y_point);
		$pdf->MultiCell($col_w_6, 7, "", 0, "R");
		$h = $pdf->GetY() > $h ? $pdf->GetY() : $h;
		$pdf->SetXY($col_x_7, $y_point);
		$pdf->MultiCell($col_w_7, 7, "", 0, "R");
		$h = $pdf->GetY() > $h ? $pdf->GetY() : $h;

		$row_height = $h - $y_point;
		$pdf->SetXY($col_x_1, $y_point);
		$pdf->MultiCell($col_w_1 + $col_w_2 + $col_w_3, $row_height, "", 1, "C");
		$pdf->SetXY($col_x_4, $y_point);
		$pdf->MultiCell($col_w_4, $row_height, "", 1, "R");
		$pdf->SetXY($col_x_5, $y_point);
		$pdf->MultiCell($col_w_5, $row_height, "", 1, "R");
		$pdf->SetXY($col_x_6, $y_point);
		$pdf->MultiCell($col_w_6, $row_height, "", 1, "R");
		$pdf->SetXY($col_x_7, $y_point);
		$pdf->MultiCell($col_w_7, $row_height, "", 1, "R");
		$pdf->SetFont('THSarabunNew', '', $font_size );
		$y_point = $h;

		$pdf->SetFont('THSarabunNewB', '', $font_size );
		$pdf->SetXY($col_x_1, $y_point);
		$pdf->MultiCell($col_w_1 + $col_w_2 + $col_w_3, 7, U2T("ยอดยกไป"), 0, "C");
		$h = $pdf->GetY();
		$pdf->SetXY($col_x_4, $y_point);
		$pdf->MultiCell($col_w_4, 7, "", 0, "R");
		$h = $pdf->GetY() > $h ? $pdf->GetY() : $h;
		$pdf->SetXY($col_x_5, $y_point);
		$pdf->MultiCell($col_w_5, 7, "", 0, "R");
		$h = $pdf->GetY() > $h ? $pdf->GetY() : $h;
		$pdf->SetXY($col_x_6, $y_point);
		$pdf->MultiCell($col_w_6, 7, $type_budget == 'debit' ? number_format($budget_amount_sum,2) : "", 0, "R");
		$h = $pdf->GetY() > $h ? $pdf->GetY() : $h;
		$pdf->SetXY($col_x_7, $y_point);
		$pdf->MultiCell($col_w_7, 7, $type_budget == 'credit' ? number_format($budget_amount_sum,2) : "", 0, "R");
		$h = $pdf->GetY() > $h ? $pdf->GetY() : $h;

		$row_height = $h - $y_point;
		$pdf->SetXY($col_x_1, $y_point);
		$pdf->MultiCell($col_w_1 + $col_w_2 + $col_w_3, $row_height, "", 1, "C");
		$pdf->SetXY($col_x_4, $y_point);
		$pdf->MultiCell($col_w_4, $row_height, "", 1, "R");
		$pdf->SetXY($col_x_5, $y_point);
		$pdf->MultiCell($col_w_5, $row_height, "", 1, "R");
		$pdf->SetXY($col_x_6, $y_point);
		$pdf->MultiCell($col_w_6, $row_height, "", 1, "R");
		$pdf->SetXY($col_x_7, $y_point);
		$pdf->MultiCell($col_w_7, $row_height, "", 1, "R");
		$pdf->SetFont('THSarabunNew', '', $font_size );
		$y_point = $h;
	}
}

$pdf->Output();