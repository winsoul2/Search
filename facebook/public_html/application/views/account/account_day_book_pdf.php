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
$pdf->AddFont('common', '', 'angsa.php');
$pdf->AddFont('bold','','angsab.php');
$font_size = 14;
$line_height = 10;

$col_x_1 = 10;
$col_w_1 = 22.5;
$col_x_2 = $col_w_1 + $col_x_1;
$col_w_2 = 24;
$col_x_3 = $col_w_2 + $col_x_2;
$col_w_3 = 70;
$col_x_4 = $col_w_3 + $col_x_3;
$col_w_4 = 25;
$col_x_5 = $col_w_4 + $col_x_4;
$col_w_5 = 25;
$col_x_6 = $col_w_5 + $col_x_5;
$col_w_6 = 25;
$col_x_7 = $col_w_6 + $col_x_6;
$col_w_7 = 25;

$y_point = 0;

$page_index = 0;

foreach($data as $key_main => $sort) {
	foreach($sort as $row1) {
		foreach ($row1 as $key => $row2) {
            $group_credit_total = 0;
            $group_debit_total = 0;
			foreach ($row2 as $key2 => $row_detail) {
                if($y_point > 260 || $y_point == 0) {
                    $pdf->AddPage();
                    $page_index++;

                    $pdf->SetMargins(0, 0, 0);
                    $border = 0;
                    $pdf->SetTextColor(0, 0, 0);
                    $pdf->SetAutoPageBreak(true,0);

                    $y_point = 20;
                    $pdf->SetFont('common', '', 12);
                    $pdf->SetXY( 10, $y_point - 4 );
                    $pdf->MultiCell(190, 10, U2T("หน้าที่ ".$page_index), 0, "R");

                    $pdf->SetFont('bold', '', 20 );
                    $pdf->SetXY( 0, $y_point );
                    $pdf->MultiCell(210, 10, U2T($_SESSION['COOP_NAME']), 0, "C");

                    $pdf->SetFont('bold', '', $font_size );
                    $y_point += 10;
                    $pdf->SetXY( 0, $y_point );
                    $pdf->MultiCell(210, 8, U2T("สมุดรายวันทั่วไป"), 0, "C");

                    $period = "";
                    if(!empty($_GET['report_date'])){
                        $date_arr = explode('/',@$_GET['report_date']);
                        $day = (int)@$date_arr[0];
                        $month = (int)@$date_arr[1];
                        $year = (int)@$date_arr[2];
                        $year -= 543;
                        $year_be = $year+543;
                        $period = "ณ วันที่ {$day} {$month_arr[$month]} {$year_be} ";
                    }else if(!empty($_GET['month'])){
                        $year_be = $_GET['year'];
                        $period = "ประจำเดือน {$month_arr[$month]} {$year_be} ";
                    }else if(!empty($_GET['year'])){
                        $year_be = $_GET['year'];
                        $period = "ประจำปี  {$year_be} ";
                    }

                    $y_point += 8;
                    $pdf->SetXY( 0, $y_point );
                    $pdf->MultiCell(210, 8, U2T($period), 0, "C");

                    $y_point += 10;
                    $pdf->SetXY($col_x_1, $y_point);
                    $pdf->MultiCell($col_w_1, $line_height, U2T("ว.ด.ป."), 1, "C");
                    $pdf->SetXY($col_x_2, $y_point);
                    $pdf->MultiCell($col_w_2, $line_height, U2T("เลขที่บัญชี"), 1, "C");
                    $pdf->SetXY($col_x_3, $y_point);
                    $pdf->MultiCell($col_w_3, $line_height, U2T("รายการ"), 1, "C");
                    $pdf->SetXY($col_x_4, $y_point);
                    $pdf->MultiCell($col_w_4, $line_height, U2T("เดบิต"), 1, "C");
                    $pdf->SetXY($col_x_5, $y_point);
                    $pdf->MultiCell($col_w_5, $line_height, U2T("เครดิต"), 1, "C");
                    $pdf->SetXY($col_x_6, $y_point);
                    $pdf->MultiCell($col_w_6, $line_height, U2T("เลขที่ใบเสร็จ"), 1, "C");

                    $y_point += $line_height;
                }

                $pdf->SetFont('common', '', $font_size);

                $journal_type = '';
                $date_now = date('Y-m-d', strtotime($key_main));

				if($row_detail['ref_type'] == "RECEIPT"){
					$ref_id = $row_detail['ref_id'];
				}else{
					$ref_id = '';
				}

                $pdf->SetXY($col_x_1, $y_point);
                $pdf->MultiCell($col_w_1, $line_height, U2T($date_now != '' ? $this->center_function->ConvertToThaiDate($date_now, '1', '0') : ''), 0, "C");
                $h = $pdf->GetY();
                $pdf->SetXY($col_x_2, $y_point);
                $pdf->MultiCell($col_w_2, $line_height, U2T($row_detail['account_chart_id']), 0, "C");
                $h = $pdf->GetY() > $h ? $pdf->GetY() : $h;
                $pdf->SetXY($col_x_3, $y_point);
                $pdf->MultiCell($col_w_3, $line_height, U2T($row_detail['account_type'] == 'debit' ? $row_detail['account_chart'] : $row_detail['account_chart']), 0, "L");
                $h = $pdf->GetY() > $h ? $pdf->GetY() : $h;
                $pdf->SetXY($col_x_4, $y_point);
                $pdf->MultiCell($col_w_4, $line_height, $row_detail['account_type'] == 'debit' ? " " . number_format($row_detail['account_amount'], 2) : '', 0, "R");
                $h = $pdf->GetY() > $h ? $pdf->GetY() : $h;
                $pdf->SetXY($col_x_5, $y_point);
                $pdf->MultiCell($col_w_5, $line_height, $row_detail['account_type'] == 'credit' ? " " . number_format($row_detail['account_amount'], 2) : '', 0, "R");
                $h = $pdf->GetY() > $h ? $pdf->GetY() : $h;
                $pdf->SetXY($col_x_6, $y_point);
                $pdf->MultiCell($col_w_6, $line_height, "", 0, "R");
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

                $y_point = $h;

				$date_now = '';
				$ref_id = '';
				$date_now = '';
				if($row_detail['account_type'] == 'debit') {
					$group_debit_total += $row_detail['account_amount'];
				} else {
					$group_credit_total += $row_detail['account_amount'];
				}
				$journal_type = $row_detail["journal_type"];
			}
            $txt = $journal_type == "P" ? "          รวมรายการจ่าย" : ($journal_type == "R" ? "          รวมรายการรับ" : ($journal_type == "J" ? "          รวมรายการโอน" : "          รวมรายการปรับปรุง"));
            $pdf->SetFont('bold', '', $font_size );
            $pdf->SetXY($col_x_1, $y_point);
            $pdf->MultiCell($col_w_1, $line_height, "", 0, "C");
            $h = $pdf->GetY();
            $pdf->SetXY($col_x_2, $y_point);
            $pdf->MultiCell($col_w_2, $line_height, "", 0, "C");
            $h = $pdf->GetY() > $h ? $pdf->GetY() : $h;
            $pdf->SetXY($col_x_3, $y_point);
            $pdf->MultiCell($col_w_3, $line_height, U2T($txt), 0, "L");
            $h = $pdf->GetY() > $h ? $pdf->GetY() : $h;
            $pdf->SetXY($col_x_4, $y_point);
            $pdf->MultiCell($col_w_4, $line_height, number_format($group_debit_total,2), 0, "R");
            $h = $pdf->GetY() > $h ? $pdf->GetY() : $h;
            $pdf->SetXY($col_x_5, $y_point);
            $pdf->MultiCell($col_w_5, $line_height, number_format($group_credit_total,2), 0, "R");
            $h = $pdf->GetY() > $h ? $pdf->GetY() : $h;
            $pdf->SetXY($col_x_6, $y_point);
            $pdf->MultiCell($col_w_6, $line_height, "", 0, "R");
            $h = $pdf->GetY() > $h ? $pdf->GetY() : $h;

            $row_height = $h - $y_point; 
            $pdf->SetLineWidth(0.7);
            $pdf->SetXY($col_x_1, $y_point);
            $pdf->MultiCell(191.5, $row_height, "", 1, "C");
            $pdf->SetLineWidth(0);
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

            $y_point = $h;
		}
	}
}

$pdf->Output();