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

$page_index = 0;
$full_w = 210;
$full_f = 178;
$line_height = 8;
$font_size = 14;

$col_a_x = 16;
$col_a_w = 30;
$col_b_x = $col_a_x + $col_a_w;
$col_b_w = 70;
$col_c_x = $col_b_x + $col_b_w;
$col_c_w = 39;
$col_d_x = $col_c_x + $col_c_w;
$col_d_w = 39;

$index = 0;
$pdf->AddPage();
$y_point = 20;
$page_index++;

// $pdf->Image(base_url().PROJECTPATH.'assets/images/coop_profile/'.$_SESSION['COOP_IMG'],95,10,-1000);

$pdf->SetFont('bold', '', 18 );
$pdf->SetXY( $col_d_x, 10);
$pdf->MultiCell($col_d_w, 8,$page_index,0,'R',0);

$pdf->SetXY( 0, $y_point);
$pdf->MultiCell($full_w, 8, U2T($_SESSION['COOP_NAME']),0,'C',0);
$y_point += 10;
$pdf->SetXY( 0, $y_point);
$pdf->MultiCell($full_w, 8, U2T("งบแสดงฐานะการเงิน"),0,'C',0);
$y_point += 10;
$pdf->SetXY( 0, $y_point);
$pdf->MultiCell($full_w, 8, U2T("ณ วันที่ ".$this->center_function->ConvertToThaiDate($thur_date,'0','0')." และ วันที่ ".$this->center_function->ConvertToThaiDate($prev_date,'0','0')),0,'C',0);
$pdf->SetFont('bold', '', $font_size );
$pdf->SetXY( $col_d_x, $y_point + 4);
$pdf->MultiCell($col_d_w, 8, U2T("(บาท)"),0,'R',0);
$y_point += 10;

//Header
$pdf->SetFont('bold', 'U', $font_size );
$pdf->SetXY( $col_a_x, $y_point);
$pdf->MultiCell($col_a_w, $line_height, U2T("เลขที่บัญชี"),0,'C',0);
$pdf->SetXY( $col_b_x, $y_point);
$pdf->MultiCell($col_b_w, $line_height, U2T("ชื่อบัญชี"),0,'C',0);
$pdf->SetXY( $col_c_x, $y_point);
$pdf->MultiCell($col_c_w, $line_height, U2T($thur_date_header),0,'C',0);
$pdf->SetXY( $col_d_x, $y_point);
$pdf->MultiCell($col_d_w, $line_height, U2T($prev_date_header),0,'C',0);
$y_point += $line_height;

$total_group_amount = 0;
$total_group_prev_amount = 0;
$total_amount = 0;
$total_prev_amount = 0;
foreach($account_charts as $index => $chart) {
    $amount = $year_budgets[$chart["account_chart_id"]];
    $prev_amount = $prev_year_budgets[$chart["account_chart_id"]];

    if($y_point > 250) {
        $pdf->AddPage();
        $y_point = 20;
        $page_index++;

        $pdf->SetFont('bold', '', 18 );
        $pdf->SetXY( $col_d_x, 10);
        $pdf->MultiCell($col_d_w, 8,$page_index,0,'R',0);
        // $pdf->Image(base_url().PROJECTPATH.'assets/images/coop_profile/'.$_SESSION['COOP_IMG'],95,10,-1000);

        $pdf->SetFont('bold', '', 18 );
        $pdf->SetXY( 0, $y_point);
        $pdf->MultiCell($full_w, 8, U2T($_SESSION['COOP_NAME']),0,'C',0);
        $y_point += 10;
        $pdf->SetXY( 0, $y_point);
        $pdf->MultiCell($full_w, 8, U2T("งบแสดงฐานะทางการเงิน"),0,'C',0);
        $y_point += 10;
        $pdf->SetXY( 0, $y_point);
        $pdf->MultiCell($full_w, 8, U2T("ณ วันที่ ".$this->center_function->ConvertToThaiDate($thur_date,'0','0')." และ ".$this->center_function->ConvertToThaiDate($prev_date,'0','0')),0,'C',0);
        $pdf->SetFont('bold', '', 12 );
        $pdf->SetXY( $col_d_x, $y_point + 4);
        $pdf->MultiCell($col_d_w, 8, U2T("(บาท)"),0,'R',0);
        $y_point += 10;

        //Header
        $pdf->SetFont('bold', 'U', $font_size );
        $pdf->SetXY( $col_a_x, $y_point);
        $pdf->MultiCell($col_a_w, $line_height, U2T("เลขที่บัญชี"),0,'C',0);
        $pdf->SetXY( $col_b_x, $y_point);
        $pdf->MultiCell($col_b_w, $line_height, U2T("ชื่อบัญชี"),0,'C',0);
        $pdf->SetXY( $col_c_x, $y_point);
        $pdf->MultiCell($col_c_w, $line_height, U2T($thur_date_header),0,'C',0);
        $pdf->SetXY( $col_d_x, $y_point);
        $pdf->MultiCell($col_d_w, $line_height, U2T($prev_date_header),0,'C',0);
        $y_point += $line_height;
    }

    if(round($amount,2) != 0 || round($prev_amount,2) != 0) {
        $pdf->SetFont('common', '', $font_size );
        $pdf->SetXY( $col_a_x, $y_point);
        $pdf->MultiCell($col_a_w, $line_height, $chart["account_chart_id"],0,'C',0);
        $pdf->SetXY( $col_b_x, $y_point);
        $pdf->MultiCell($col_b_w, $line_height, U2T($chart["account_chart"]),0,'L',0);
        $y = $pdf->GetY();
        $pdf->SetXY( $col_c_x, $y_point);
        $pdf->MultiCell($col_c_w, $line_height, !empty($amount) ? $this->center_function->format_signed_number($amount, 2): "0.00" ,0,'R',0);
        $pdf->SetXY( $col_d_x, $y_point);
        $pdf->MultiCell($col_d_w, $line_height, !empty($prev_amount) ? $this->center_function->format_signed_number($prev_amount, 2): "0.00" ,0,'R',0);
        $h = $y-$y_point;
        $total_amount += $amount;
        $total_prev_amount += $prev_amount;
        $total_group_amount += $amount;
        $total_group_prev_amount += $prev_amount;
        $y_point += $h;
    } else if ($chart["type"] == 1) {
        $pdf->SetFont('bold', '', $font_size );
        $pdf->SetXY( $col_a_x, $y_point);
        $pdf->MultiCell($col_a_w, $line_height, "",0,'C',0);
        $pdf->SetXY( $col_b_x, $y_point);
        $pdf->SetFont('bold', 'U', $font_size );
        $pdf->MultiCell($col_b_w, $line_height, U2T($chart["account_chart"]),0,'L',0);
        $pdf->SetXY( $col_c_x, $y_point);
        $pdf->SetFont('bold', '', $font_size );
        $pdf->MultiCell($col_c_w, $line_height, "",0,'C',0);
        $pdf->SetXY( $col_d_x, $y_point);
        $pdf->MultiCell($col_d_w, $line_height, "",0,'C',0);
        $y_point += $line_height;
    }

    if($y_point > 250) {
        $pdf->AddPage();
        $y_point = 20;
        $page_index++;

        $pdf->SetFont('bold', '', 18 );
        $pdf->SetXY( $col_d_x, 10);
        $pdf->MultiCell($col_d_w, 8,$page_index,0,'R',0);
        // $pdf->Image(base_url().PROJECTPATH.'assets/images/coop_profile/'.$_SESSION['COOP_IMG'],95,10,-1000);

        $pdf->SetFont('bold', '', 18 );
        $pdf->SetXY( 0, $y_point);
        $pdf->MultiCell($full_w, 8, U2T($_SESSION['COOP_NAME']),0,'C',0);
        $y_point += 10;
        $pdf->SetXY( 0, $y_point);
        $pdf->MultiCell($full_w, 8, U2T("งบแสดงฐานะทางการเงิน"),0,'C',0);
        $y_point += 10;
        $pdf->SetXY( 0, $y_point);
        $pdf->MultiCell($full_w, 8, U2T("ณ วันที่ ".$this->center_function->ConvertToThaiDate($thur_date,'0','0')." และ ".$this->center_function->ConvertToThaiDate($prev_date,'0','0')),0,'C',0);
        $pdf->SetFont('bold', '', $font_size );
        $pdf->SetXY( $col_d_x, $y_point + 4);
        $pdf->MultiCell($col_d_w, 8, U2T("(บาท)"),0,'R',0);
        $y_point += 10;

        //Header
        $pdf->SetFont('bold', 'U', $font_size );
        $pdf->SetXY( $col_a_x, $y_point);
        $pdf->MultiCell($col_a_w, $line_height, U2T("เลขที่บัญชี"),0,'C',0);
        $pdf->SetXY( $col_b_x, $y_point);
        $pdf->MultiCell($col_b_w, $line_height, U2T("ชื่อบัญชี"),0,'C',0);
        $pdf->SetXY( $col_c_x, $y_point);
        $pdf->MultiCell($col_c_w, $line_height, U2T($thur_date_header),0,'C',0);
        $pdf->SetXY( $col_d_x, $y_point);
        $pdf->MultiCell($col_d_w, $line_height, U2T($prev_date_header),0,'C',0);
        $y_point += $line_height;
    }

    $group_account_id = substr($chart['account_chart_id'],0,1);
    if($group_account_id != substr($account_charts[$index+1]['account_chart_id'],0,1)) {
        if($group_account_id == 1) {
            $pdf->Line($col_c_x + 5, $y_point+ 0.1, $col_d_x, $y_point+0.1);
            $pdf->Line($col_d_x + 5, $y_point+ 0.1, $col_d_x + $col_d_w, $y_point+0.1);
            $pdf->SetFont('bold', '', $font_size );
            $pdf->SetXY( $col_a_x, $y_point);
            $pdf->MultiCell($col_a_w, $line_height, "",0,'C',0);
            $pdf->SetXY( $col_b_x, $y_point);
            $pdf->MultiCell($col_b_w, $line_height, U2T("รวมสินทรัพย์"),0,'L',0);
            $y = $pdf->GetY();
			$pdf->SetFont('bold', '', $font_size );
            $pdf->SetXY( $col_c_x, $y_point);
            $pdf->MultiCell($col_c_w, $line_height, !empty($total_amount) ? $this->center_function->format_signed_number($total_amount, 2) : "0.00" ,0,'R',0);
            $pdf->SetXY( $col_d_x, $y_point);
            $pdf->MultiCell($col_d_w, $line_height, !empty($total_prev_amount) ? $this->center_function->format_signed_number($total_prev_amount, 2) : "0.00" ,0,'R',0);
            $pdf->Line($col_c_x + 5, $y_point + $line_height + 0.1, $col_d_x, $y_point + $line_height + 0.1);
            $pdf->Line($col_d_x + 5, $y_point + $line_height + 0.1, $col_d_x + $col_d_w, $y_point + $line_height + 0.1);
			$pdf->Line($col_c_x + 5, $y_point + $line_height + 1, $col_d_x, $y_point + $line_height + 1);
			$pdf->Line($col_d_x + 5, $y_point + $line_height + 1, $col_d_x + $col_d_w, $y_point + $line_height + 1);

            // $h = $y-$y_point;
            // $y_point += $h;
            // $pdf->SetXY( $col_a_x, $y_point);
            // $pdf->MultiCell($col_a_w, $line_height, "",0,'C',0);
            // $pdf->SetXY( $col_b_x, $y_point);
            // $pdf->MultiCell($col_b_w, $line_height, "",0,'L',0);
            // $y = $pdf->GetY();
            // $pdf->SetXY( $col_c_x, $y_point);
            // $pdf->MultiCell($col_c_w, $line_height, !empty($total_amount) ? number_format($total_group_amount,2) : "0.00" ,0,'R',0);
            // $pdf->SetXY( $col_d_x, $y_point);
            // $pdf->MultiCell($col_d_w, $line_height, !empty($total_prev_amount) ? number_format($total_group_prev_amount,2) : "0.00" ,0,'R',0);
            // $pdf->Line($col_c_x + 5, $y_point + $line_height + 0.1, $col_d_x, $y_point + $line_height + 0.1);
            // $pdf->Line($col_d_x + 5, $y_point + $line_height + 0.1, $col_d_x + $col_d_w, $y_point + $line_height + 0.1);
            // $pdf->Line($col_c_x + 5, $y_point + $line_height + 1, $col_d_x, $y_point + $line_height + 1);
            // $pdf->Line($col_d_x + 5, $y_point + $line_height + 1, $col_d_x + $col_d_w, $y_point + $line_height + 1);

            $h = $y-$y_point;
            $y_point += $h;
            $total_amount = 0;
            $total_prev_amount = 0;
            $total_group_amount = 0;
            $total_group_prev_amount = 0;
        } else if ($group_account_id == 2) {
            $pdf->Line($col_c_x + 5, $y_point+ 0.1, $col_d_x, $y_point+0.1);
            $pdf->Line($col_d_x + 5, $y_point+ 0.1, $col_d_x + $col_d_w, $y_point+0.1);
            $pdf->SetFont('bold', '', $font_size );
            $pdf->SetXY( $col_a_x, $y_point);
            $pdf->MultiCell($col_a_w, $line_height, "",0,'C',0);
            $pdf->SetXY( $col_b_x, $y_point);
            $pdf->MultiCell($col_b_w, $line_height, U2T("รวมหนี้สิน"),0,'L',0);
            $y = $pdf->GetY();
			$pdf->SetFont('bold', '', $font_size );
            $pdf->SetXY( $col_c_x, $y_point);
            $pdf->MultiCell($col_c_w, $line_height, !empty($total_amount) ? $this->center_function->format_signed_number($total_amount, 2): "0.00" ,0,'R',0);
            $pdf->SetXY( $col_d_x, $y_point);
            $pdf->MultiCell($col_d_w, $line_height, !empty($total_prev_amount) ? $this->center_function->format_signed_number($total_prev_amount, 2) : "0.00" ,0,'R',0);
			$pdf->Line($col_c_x + 5, $y_point + $line_height + 0.1, $col_d_x, $y_point + $line_height + 0.1);
			$pdf->Line($col_d_x + 5, $y_point + $line_height + 0.1, $col_d_x + $col_d_w, $y_point + $line_height + 0.1);
			//$pdf->Line($col_c_x + 5, $y_point + $line_height + 1, $col_d_x, $y_point + $line_height + 1);
			//$pdf->Line($col_d_x + 5, $y_point + $line_height + 1, $col_d_x + $col_d_w, $y_point + $line_height + 1);
            $h = $y-$y_point;
            $y_point += $h;
            $total_amount = 0;
            $total_prev_amount = 0;
        } else if ($group_account_id == 3) {
            $pdf->Line($col_c_x + 5, $y_point+ 0.1, $col_d_x, $y_point+0.1);
            $pdf->Line($col_d_x + 5, $y_point+ 0.1, $col_d_x + $col_d_w, $y_point+0.1);
            $pdf->SetFont('bold', '', $font_size );
            $pdf->SetXY( $col_a_x, $y_point);
            $pdf->MultiCell($col_a_w, $line_height, "",0,'C',0);
            $pdf->SetXY( $col_b_x, $y_point);
            $pdf->MultiCell($col_b_w, $line_height, U2T("รวมทุน"),0,'L',0);
            $y = $pdf->GetY();
			$pdf->SetFont('bold', '', $font_size );
            $pdf->SetXY( $col_c_x, $y_point);
            $pdf->MultiCell($col_c_w, $line_height, !empty($total_amount) ? $this->center_function->format_signed_number($total_amount, 2) : "0.00" ,0,'R',0);
            $pdf->SetXY( $col_d_x, $y_point);
            $pdf->MultiCell($col_d_w, $line_height, !empty($total_prev_amount) ? $this->center_function->format_signed_number($total_prev_amount, 2) : "0.00" ,0,'R',0);
            $pdf->Line($col_c_x + 5, $y_point + $line_height + 0.1, $col_d_x, $y_point + $line_height + 0.1);
            $pdf->Line($col_d_x + 5, $y_point + $line_height + 0.1, $col_d_x + $col_d_w, $y_point + $line_height + 0.1);
			//$pdf->Line($col_c_x + 5, $y_point + $line_height + 1, $col_d_x, $y_point + $line_height + 1);
			//$pdf->Line($col_d_x + 5, $y_point + $line_height + 1, $col_d_x + $col_d_w, $y_point + $line_height + 1);
            $h = $y-$y_point;
            $y_point += $h;
            $pdf->SetFont('bold', '', $font_size );
            $pdf->SetXY( $col_a_x, $y_point);
            $pdf->MultiCell($col_a_w, $line_height, "",0,'C',0);
            $pdf->SetXY( $col_b_x, $y_point);
            $pdf->MultiCell($col_b_w, $line_height, U2T("รวมหนี้สินและทุน"),0,'L',0);
            $y = $pdf->GetY();
			$pdf->SetFont('bold', '', $font_size );
            $pdf->SetXY( $col_c_x, $y_point);
            $pdf->MultiCell($col_c_w, $line_height, !empty($total_amount) ? $this->center_function->format_signed_number($total_group_amount, 2) : "0.00" ,0,'R',0);
            $pdf->SetXY( $col_d_x, $y_point);
            $pdf->MultiCell($col_d_w, $line_height, !empty($total_prev_amount) ? $this->center_function->format_signed_number($total_group_prev_amount, 2) : "0.00" ,0,'R',0);
            $pdf->Line($col_c_x + 5, $y_point + $line_height + 0.1, $col_d_x, $y_point + $line_height + 0.1);
            $pdf->Line($col_d_x + 5, $y_point + $line_height + 0.1, $col_d_x + $col_d_w, $y_point + $line_height + 0.1);
            $pdf->Line($col_c_x + 5, $y_point + $line_height + 1, $col_d_x, $y_point + $line_height + 1);
            $pdf->Line($col_d_x + 5, $y_point + $line_height + 1, $col_d_x + $col_d_w, $y_point + $line_height + 1);
            $h = $y-$y_point;
            $y_point += $h;
            $total_amount = 0;
            $total_prev_amount = 0;
            $total_group_amount = 0;
            $total_group_prev_amount = 0;
        }
    }
}

$pdf->SetLineWidth(0.1);
$pdf->SetFont('common', '', $font_size );
$pdf->Output();
