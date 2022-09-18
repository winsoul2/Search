<?php
function U2T($text) { return @iconv("UTF-8", "TIS-620//IGNORE", ($text)); }
function num_format($text) {
    if($text!=''){
        return number_format($text,2);
    }else{
        return '';
    }
}

$pdf = new FPDI('L','mm', array(297, 210));
$pdf->AddFont('common', '', 'angsa.php');
$pdf->AddFont('bold','','angsab.php');

$page_index = 0;
$full_w = 297;
$full_f = 265;
$line_height = 7;

$font_size = 16;

$col_x = 16;
$col_w = 14;
$col_a_x = $col_x + $col_w;
$col_a_w = 18;
$col_b_x = $col_a_x + $col_a_w;
$col_b_w = 59;
$col_c_x = $col_b_x + $col_b_w;
$col_c_w = 29;
$col_d_x = $col_c_x + $col_c_w;
$col_d_w = 29;
$col_e_x = $col_d_x + $col_d_w;
$col_e_w = 29;
$col_f_x = $col_e_x + $col_e_w;
$col_f_w = 29;
$col_g_x = $col_f_x + $col_f_w;
$col_g_w = 29;
$col_h_x = $col_g_x + $col_g_w;
$col_h_w = 29;

$index = 0;
$pdf->AddPage();
$y_point = 20;
$page_index++;

$pdf->SetFont('common', '', 12 );
$pdf->SetXY(0, 10);
$pdf->MultiCell(287, 8, U2T("หน้าที่ ".$page_index),0,'R',0);

// $pdf->Image(base_url().PROJECTPATH.'assets/images/coop_profile/'.$_SESSION['COOP_IMG'],138,10,-1000);

$pdf->SetFont('bold', '', 18 );
$pdf->SetXY( 0, $y_point);
$pdf->MultiCell($full_w, 8, U2T($cremation['full_name']),0,'C',0);
$y_point += 10;
$pdf->SetXY( 0, $y_point);
$pdf->MultiCell($full_w, 8, U2T("งบทดลอง"),0,'C',0);
$y_point += 10;
$pdf->SetXY( 0, $y_point);
$pdf->MultiCell($full_w, 8, U2T($textTitle),0,'C',0);
$y_point += 10;

$pdf->SetFont('common', '', $font_size );

//Header
$pdf->SetXY( $col_x, $y_point);
$pdf->MultiCell($col_w, $line_height * 2, U2T("ลำดับที่"),1,'C',0);
$pdf->SetXY( $col_a_x, $y_point);
$pdf->MultiCell($col_a_w, $line_height * 2, U2T("เลขที่บัญชี"),1,'C',0);
$pdf->SetXY( $col_b_x, $y_point);
$pdf->MultiCell($col_b_w, $line_height * 2, U2T("ชื่อบัญชี"),1,'C',0);
$pdf->SetXY( $col_c_x, $y_point);
$pdf->MultiCell($col_c_w * 2, $line_height, U2T("ยอดยกมาเดือนก่อน"),1,'C',0);
$pdf->SetXY( $col_e_x, $y_point);
$pdf->MultiCell($col_e_w *2 , $line_height, U2T("รายการระหว่างเดือน"),1,'C',0);
$pdf->SetXY( $col_g_x, $y_point);
$pdf->MultiCell($col_g_w * 2, $line_height, U2T("ยอดคงเหลือยกไป"),1,'C',0);

$y_point += $line_height;
$pdf->SetXY( $col_c_x, $y_point);
$pdf->MultiCell($col_c_w, $line_height, U2T("เดบิต"),1,'C',0);
$pdf->SetXY( $col_d_x, $y_point);
$pdf->MultiCell($col_d_w, $line_height, U2T("เครดิต"),1,'C',0);
$pdf->SetXY( $col_e_x, $y_point);
$pdf->MultiCell($col_e_w , $line_height, U2T("เดบิต"),1,'C',0);
$pdf->SetXY( $col_f_x, $y_point);
$pdf->MultiCell($col_f_w, $line_height, U2T("เครดิต"),1,'C',0);
$pdf->SetXY( $col_g_x, $y_point);
$pdf->MultiCell($col_g_w, $line_height, U2T("เดบิต"),1,'C',0);
$pdf->SetXY( $col_h_x, $y_point);
$pdf->MultiCell($col_h_w, $line_height, U2T("เครดิต"),1,'C',0);
$y_point += $line_height;

foreach($data_charts as $value) {
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
        //Add page if overlap
        if($y_point >= 175) {
            $pdf->AddPage();

            $page_index++;
            $pdf->SetFont('common', '', 12 );
            $pdf->SetXY(0, 10);
            $pdf->MultiCell(287, 8, U2T("หน้าที่ ".$page_index),0,'R',0);

            $y_point = 20;

            // $pdf->Image(base_url().PROJECTPATH.'assets/images/coop_profile/'.$_SESSION['COOP_IMG'],138,10,-1000);

            $pdf->SetFont('bold', '', 18 );
            $pdf->SetXY( 0, $y_point);
            $pdf->MultiCell($full_w, 8, U2T($_SESSION['COOP_NAME']),0,'C',0);
            $y_point += 10;
            $pdf->SetXY( 0, $y_point);
            $pdf->MultiCell($full_w, 8, U2T("งบทดลอง"),0,'C',0);
            $y_point += 10;
            $pdf->SetXY( 0, $y_point);
            $pdf->MultiCell($full_w, 8, U2T($textTitle),0,'C',0);
            $y_point += 10;

            $pdf->SetFont('common', '', $font_size );

            //Header
            $pdf->SetXY( $col_x, $y_point);
            $pdf->MultiCell($col_w, $line_height * 2, U2T("ลำดับที่"),1,'C',0);
            $pdf->SetXY( $col_a_x, $y_point);
            $pdf->MultiCell($col_a_w, $line_height * 2, U2T("เลขที่บัญชี"),1,'C',0);
            $pdf->SetXY( $col_b_x, $y_point);
            $pdf->MultiCell($col_b_w, $line_height * 2, U2T("ชื่อบัญชี"),1,'C',0);
            $pdf->SetXY( $col_c_x, $y_point);
            $pdf->MultiCell($col_c_w * 2, $line_height, U2T("ยอดยกมาเดือนก่อน"),1,'C',0);
            $pdf->SetXY( $col_e_x, $y_point);
            $pdf->MultiCell($col_e_w *2 , $line_height, U2T("รายการระหว่างเดือน"),1,'C',0);
            $pdf->SetXY( $col_g_x, $y_point);
            $pdf->MultiCell($col_g_w * 2, $line_height, U2T("ยอดคงเหลือยกไป"),1,'C',0);

            $y_point += $line_height;
            $pdf->SetXY( $col_c_x, $y_point);
            $pdf->MultiCell($col_c_w, $line_height, U2T("เดบิต"),1,'C',0);
            $pdf->SetXY( $col_d_x, $y_point);
            $pdf->MultiCell($col_d_w, $line_height, U2T("เครดิต"),1,'C',0);
            $pdf->SetXY( $col_e_x, $y_point);
            $pdf->MultiCell($col_e_w , $line_height, U2T("เดบิต"),1,'C',0);
            $pdf->SetXY( $col_f_x, $y_point);
            $pdf->MultiCell($col_f_w, $line_height, U2T("เครดิต"),1,'C',0);
            $pdf->SetXY( $col_g_x, $y_point);
            $pdf->MultiCell($col_g_w, $line_height, U2T("เดบิต"),1,'C',0);
            $pdf->SetXY( $col_h_x, $y_point);
            $pdf->MultiCell($col_h_w, $line_height, U2T("เครดิต"),1,'C',0);
            $y_point += $line_height;
        }

        $pdf->SetFont('common', '', $font_size );

        if($group_account_id != substr($value['account_chart_id'],0,1) && $index > 0){
            $pdf->SetLineWidth(0.5);
            $pdf->SetFont('bold', '', $font_size );
            $desc = $group_account_id == 1 ? "รวมสินทรัพย์"
                    : ($group_account_id == 2 ? "รวมหนี้สิน"
                    : ($group_account_id == 3 ? "รวมทุน"
                    : ($group_account_id == 4 ? "รวมรายได้"
                    : ($group_account_id == 5 ? "รวมค่าใช้จ่าย" : ""))));
            $group_account_id = substr($value['account_chart_id'],0,1);
            $pdf->SetXY( $col_x, $y_point);
            $pdf->MultiCell($col_w, $line_height, "",0,'C',0);
            $pdf->SetXY( $col_a_x, $y_point);
            $pdf->MultiCell($col_a_w, $line_height, "",0,'C',0);
            $pdf->SetXY( $col_b_x, $y_point);
            $pdf->MultiCell($col_b_w, $line_height, U2T($desc),0,'L',0);
            $y = $pdf->GetY();
            $pdf->SetXY( $col_c_x, $y_point);
            $pdf->MultiCell($col_c_w, $line_height, !empty($sum_group_amount_debit) ? number_format($sum_group_amount_debit,2) : "-",0,'R',0);
            $y = $y < $pdf->GetY() ? $pdf->GetY() : $y;
            $pdf->SetXY( $col_d_x, $y_point);
            $pdf->MultiCell($col_d_w, $line_height, !empty($sum_group_amount_credit) ? number_format($sum_group_amount_credit,2) : "-",0,'R',0);
            $y = $y < $pdf->GetY() ? $pdf->GetY() : $y;
            $pdf->SetXY( $col_e_x, $y_point);
            $pdf->MultiCell($col_e_w , $line_height, !empty($sum_group_amount_debit_ledger) ? number_format($sum_group_amount_debit_ledger,2) : "-",0,'R',0);
            $y = $y < $pdf->GetY() ? $pdf->GetY() : $y;
            $pdf->SetXY( $col_f_x, $y_point);
            $pdf->MultiCell($col_f_w, $line_height, !empty($sum_group_amount_credit_ledger) ? number_format($sum_group_amount_credit_ledger,2) : "-",0,'R',0);
            $y = $y < $pdf->GetY() ? $pdf->GetY() : $y;
            $pdf->SetXY( $col_g_x, $y_point);
            $pdf->MultiCell($col_g_w, $line_height, !empty($sum_group_amount_debit_budget) ? number_format($sum_group_amount_debit_budget,2) : "-",0,'R',0);
            $y = $y < $pdf->GetY() ? $pdf->GetY() : $y;
            $pdf->SetXY( $col_h_x, $y_point);
            $pdf->MultiCell($col_h_w, $line_height, !empty($sum_group_amount_credit_budget) ? number_format($sum_group_amount_credit_budget,2) : "-",0,'R',0);
            $y = $y < $pdf->GetY() ? $pdf->GetY() : $y;

            $h = $y-$y_point;
            $pdf->SetXY( $col_x, $y_point);
            $pdf->MultiCell($col_w, $h, "",1,'C',0);
            $pdf->SetXY( $col_a_x, $y_point);
            $pdf->MultiCell($col_a_w, $h, "",1,'C',0);
            $pdf->SetXY( $col_b_x, $y_point);
            $pdf->MultiCell($col_b_w, $h, "",1,'L',0);
            $pdf->SetXY( $col_c_x, $y_point);
            $pdf->MultiCell($col_c_w, $h, "",1,'C',0);
            $pdf->SetXY( $col_d_x, $y_point);
            $pdf->MultiCell($col_d_w, $h, U2T(""),1,'C',0);
            $pdf->SetXY( $col_e_x, $y_point);
            $pdf->MultiCell($col_e_w , $h, U2T(""),1,'C',0);
            $pdf->SetXY( $col_f_x, $y_point);
            $pdf->MultiCell($col_f_w, $h, U2T(""),1,'C',0);
            $pdf->SetXY( $col_g_x, $y_point);
            $pdf->MultiCell($col_g_w, $h, U2T(""),1,'C',0);
            $pdf->SetXY( $col_h_x, $y_point);
            $pdf->MultiCell($col_h_w, $h, U2T(""),1,'C',0);
            $y_point += $h;
            $sum_group_amount_debit  = 0;
            $sum_group_amount_credit = 0;
            $sum_group_amount_credit_ledger  = 0;
            $sum_group_amount_debit_ledger  = 0;
            $sum_group_amount_credit_budget  = 0;
            $sum_group_amount_debit_budget  = 0;
            $pdf->SetLineWidth(0.1);
            $pdf->SetFont('common', '', $font_size );
        }

        if ($value["type"] == 1) {
            //Add page if overlap
            if($y_point >= 175) {
                $pdf->AddPage();

                $page_index++;
                $pdf->SetFont('common', '', $font_size );
                $pdf->SetXY(0, 10);
                $pdf->MultiCell(287, 8, U2T("หน้าที่ ".$page_index),0,'R',0);

                $y_point = 20;
    
                // $pdf->Image(base_url().PROJECTPATH.'assets/images/coop_profile/'.$_SESSION['COOP_IMG'],138,10,-1000);
    
                $pdf->SetFont('bold', '', 18 );
                $pdf->SetXY( 0, $y_point);
                $pdf->MultiCell($full_w, 8, U2T($_SESSION['COOP_NAME']),0,'C',0);
                $y_point += 10;
                $pdf->SetXY( 0, $y_point);
                $pdf->MultiCell($full_w, 8, U2T("งบทดลอง"),0,'C',0);
                $y_point += 10;
                $pdf->SetXY( 0, $y_point);
                $pdf->MultiCell($full_w, 8, U2T($textTitle),0,'C',0);
                $y_point += 10;
    
                $pdf->SetFont('common', '', $font_size );
    
                //Header
                $pdf->SetXY( $col_x, $y_point);
                $pdf->MultiCell($col_w, $line_height * 2, U2T("ลำดับที่"),1,'C',0);
                $pdf->SetXY( $col_a_x, $y_point);
                $pdf->MultiCell($col_a_w, $line_height * 2, U2T("เลขที่บัญชี"),1,'C',0);
                $pdf->SetXY( $col_b_x, $y_point);
                $pdf->MultiCell($col_b_w, $line_height * 2, U2T("ชื่อบัญชี"),1,'C',0);
                $pdf->SetXY( $col_c_x, $y_point);
                $pdf->MultiCell($col_c_w * 2, $line_height, U2T("ยอดยกมาเดือนก่อน"),1,'C',0);
                $pdf->SetXY( $col_e_x, $y_point);
                $pdf->MultiCell($col_e_w *2 , $line_height, U2T("รายการระหว่างเดือน"),1,'C',0);
                $pdf->SetXY( $col_g_x, $y_point);
                $pdf->MultiCell($col_g_w * 2, $line_height, U2T("ยอดคงเหลือยกไป"),1,'C',0);
    
                $y_point += $line_height;
                $pdf->SetXY( $col_c_x, $y_point);
                $pdf->MultiCell($col_c_w, $line_height, U2T("เดบิต"),1,'C',0);
                $pdf->SetXY( $col_d_x, $y_point);
                $pdf->MultiCell($col_d_w, $line_height, U2T("เครดิต"),1,'C',0);
                $pdf->SetXY( $col_e_x, $y_point);
                $pdf->MultiCell($col_e_w , $line_height, U2T("เดบิต"),1,'C',0);
                $pdf->SetXY( $col_f_x, $y_point);
                $pdf->MultiCell($col_f_w, $line_height, U2T("เครดิต"),1,'C',0);
                $pdf->SetXY( $col_g_x, $y_point);
                $pdf->MultiCell($col_g_w, $line_height, U2T("เดบิต"),1,'C',0);
                $pdf->SetXY( $col_h_x, $y_point);
                $pdf->MultiCell($col_h_w, $line_height, U2T("เครดิต"),1,'C',0);
                $y_point += $line_height;
            }
    
            $group_account_id = substr($value['account_chart_id'],0,1);
            $pdf->SetXY( $col_x, $y_point);
            $pdf->MultiCell($col_w, $line_height, "",0,'C',0);
            $pdf->SetXY( $col_a_x, $y_point);
            $pdf->MultiCell($col_a_w, $line_height, "",0,'C',0);
            $pdf->SetXY( $col_b_x, $y_point);
            $pdf->SetFont('bold', 'U', $font_size );
            $pdf->MultiCell($col_b_w, $line_height, U2T($value["account_chart"]),0,'L',0);
            $y = $pdf->GetY();
            $pdf->SetFont('common', '', $font_size );
            $pdf->SetXY( $col_c_x, $y_point);
            $pdf->MultiCell($col_c_w, $line_height, "",0,'R',0);
            $pdf->SetXY( $col_d_x, $y_point);
            $pdf->MultiCell($col_d_w, $line_height, "",0,'R',0);
            $pdf->SetXY( $col_e_x, $y_point);
            $pdf->MultiCell($col_e_w , $line_height, "",0,'R',0);
            $pdf->SetXY( $col_f_x, $y_point);
            $pdf->MultiCell($col_f_w, $line_height, "",0,'R',0);
            $pdf->SetXY( $col_g_x, $y_point);
            $pdf->MultiCell($col_g_w, $line_height, "",0,'R',0);
            $pdf->SetXY( $col_h_x, $y_point);
            $pdf->MultiCell($col_h_w, $line_height, "",0,'R',0);
    
            $h = $y-$y_point;
            $pdf->SetXY( $col_x, $y_point);
            $pdf->MultiCell($col_w, $h, "",1,'C',0);
            $pdf->SetXY( $col_a_x, $y_point);
            $pdf->MultiCell($col_a_w, $h, "",1,'C',0);
            $pdf->SetXY( $col_b_x, $y_point);
            $pdf->MultiCell($col_b_w, $h, "",1,'L',0);
            $pdf->SetXY( $col_c_x, $y_point);
            $pdf->MultiCell($col_c_w, $h, "",1,'C',0);
            $pdf->SetXY( $col_d_x, $y_point);
            $pdf->MultiCell($col_d_w, $h, U2T(""),1,'C',0);
            $pdf->SetXY( $col_e_x, $y_point);
            $pdf->MultiCell($col_e_w , $h, U2T(""),1,'C',0);
            $pdf->SetXY( $col_f_x, $y_point);
            $pdf->MultiCell($col_f_w, $h, U2T(""),1,'C',0);
            $pdf->SetXY( $col_g_x, $y_point);
            $pdf->MultiCell($col_g_w, $h, U2T(""),1,'C',0);
            $pdf->SetXY( $col_h_x, $y_point);
            $pdf->MultiCell($col_h_w, $h, U2T(""),1,'C',0);
            $y_point += $h;
        } else {
            //Add page if overlap
            if($y_point >= 175) {
                $pdf->AddPage();

                $page_index++;
                $pdf->SetFont('common', '', 12 );
                $pdf->SetXY(0, 10);
                $pdf->MultiCell(287, 8, U2T("หน้าที่ ".$page_index),0,'R',0);

                $y_point = 20;
    
                // $pdf->Image(base_url().PROJECTPATH.'assets/images/coop_profile/'.$_SESSION['COOP_IMG'],138,10,-1000);
    
                $pdf->SetFont('bold', '', 18 );
                $pdf->SetXY( 0, $y_point);
                $pdf->MultiCell($full_w, 8, U2T($_SESSION['COOP_NAME']),0,'C',0);
                $y_point += 10;
                $pdf->SetXY( 0, $y_point);
                $pdf->MultiCell($full_w, 8, U2T("งบทดลอง"),0,'C',0);
                $y_point += 10;
                $pdf->SetXY( 0, $y_point);
                $pdf->MultiCell($full_w, 8, U2T($textTitle),0,'C',0);
                $y_point += 10;
    
                $pdf->SetFont('common', '', $font_size );
    
                //Header
                $pdf->SetXY( $col_x, $y_point);
                $pdf->MultiCell($col_w, $line_height * 2, U2T("ลำดับที่"),1,'C',0);
                $pdf->SetXY( $col_a_x, $y_point);
                $pdf->MultiCell($col_a_w, $line_height * 2, U2T("เลขที่บัญชี"),1,'C',0);
                $pdf->SetXY( $col_b_x, $y_point);
                $pdf->MultiCell($col_b_w, $line_height * 2, U2T("ชื่อบัญชี"),1,'C',0);
                $pdf->SetXY( $col_c_x, $y_point);
                $pdf->MultiCell($col_c_w * 2, $line_height, U2T("ยอดยกมาเดือนก่อน"),1,'C',0);
                $pdf->SetXY( $col_e_x, $y_point);
                $pdf->MultiCell($col_e_w *2 , $line_height, U2T("รายการระหว่างเดือน"),1,'C',0);
                $pdf->SetXY( $col_g_x, $y_point);
                $pdf->MultiCell($col_g_w * 2, $line_height, U2T("ยอดคงเหลือยกไป"),1,'C',0);
    
                $y_point += $line_height;
                $pdf->SetXY( $col_c_x, $y_point);
                $pdf->MultiCell($col_c_w, $line_height, U2T("เดบิต"),1,'C',0);
                $pdf->SetXY( $col_d_x, $y_point);
                $pdf->MultiCell($col_d_w, $line_height, U2T("เครดิต"),1,'C',0);
                $pdf->SetXY( $col_e_x, $y_point);
                $pdf->MultiCell($col_e_w , $line_height, U2T("เดบิต"),1,'C',0);
                $pdf->SetXY( $col_f_x, $y_point);
                $pdf->MultiCell($col_f_w, $line_height, U2T("เครดิต"),1,'C',0);
                $pdf->SetXY( $col_g_x, $y_point);
                $pdf->MultiCell($col_g_w, $line_height, U2T("เดบิต"),1,'C',0);
                $pdf->SetXY( $col_h_x, $y_point);
                $pdf->MultiCell($col_h_w, $line_height, U2T("เครดิต"),1,'C',0);
                $y_point += $line_height;
            }

            $group_account_id = substr($value['account_chart_id'],0,1);
            $pdf->SetXY( $col_x, $y_point);
            $pdf->MultiCell($col_w, $line_height, U2T(++$index),0,'C',0);
            $pdf->SetXY( $col_a_x, $y_point);
            $pdf->MultiCell($col_a_w, $line_height, U2T($value["account_chart_id"]),0,'C',0);
            $pdf->SetXY( $col_b_x, $y_point);
            $pdf->MultiCell($col_b_w, $line_height, U2T($value["account_chart"]),0,'L',0);
            $y = $pdf->GetY();
            $pdf->SetXY( $col_c_x, $y_point);
            $pdf->MultiCell($col_c_w, $line_height, !empty($debit_hirtorical) ? number_format($debit_hirtorical, 2) : "-",0,'R',0);
            $y = $y < $pdf->GetY() ? $pdf->GetY() : $y;
            $pdf->SetXY( $col_d_x, $y_point);
            $pdf->MultiCell($col_d_w, $line_height, !empty($credit_hirtorical) ? number_format($credit_hirtorical, 2) : "-",0,'R',0);
            $y = $y < $pdf->GetY() ? $pdf->GetY() : $y;
            $pdf->SetXY( $col_e_x, $y_point);
            $pdf->MultiCell($col_e_w , $line_height, !empty($debit_current) ? number_format($debit_current, 2) : "-",0,'R',0);
            $y = $y < $pdf->GetY() ? $pdf->GetY() : $y;
            $pdf->SetXY( $col_f_x, $y_point);
            $pdf->MultiCell($col_f_w, $line_height, !empty($credit_current) ? number_format($credit_current, 2) : "-",0,'R',0);
            $y = $y < $pdf->GetY() ? $pdf->GetY() : $y;
            $pdf->SetXY( $col_g_x, $y_point);
            $pdf->MultiCell($col_g_w, $line_height, !empty($debit_balance) ? number_format($debit_balance, 2) : "-",0,'R',0);
            $y = $y < $pdf->GetY() ? $pdf->GetY() : $y;
            $pdf->SetXY( $col_h_x, $y_point);
            $pdf->MultiCell($col_h_w, $line_height, !empty($credit_balance) ? number_format($credit_balance, 2) : "-",0,'R',0);
            $y = $y < $pdf->GetY() ? $pdf->GetY() : $y;

            $h = $y-$y_point;
            $pdf->SetXY( $col_x, $y_point);
            $pdf->MultiCell($col_w, $h, "",1,'C',0);
            $pdf->SetXY( $col_a_x, $y_point);
            $pdf->MultiCell($col_a_w, $h, "",1,'C',0);
            $pdf->SetXY( $col_b_x, $y_point);
            $pdf->MultiCell($col_b_w, $h, "",1,'L',0);
            $pdf->SetXY( $col_c_x, $y_point);
            $pdf->MultiCell($col_c_w, $h, "",1,'C',0);
            $pdf->SetXY( $col_d_x, $y_point);
            $pdf->MultiCell($col_d_w, $h, U2T(""),1,'C',0);
            $pdf->SetXY( $col_e_x, $y_point);
            $pdf->MultiCell($col_e_w , $h, U2T(""),1,'C',0);
            $pdf->SetXY( $col_f_x, $y_point);
            $pdf->MultiCell($col_f_w, $h, U2T(""),1,'C',0);
            $pdf->SetXY( $col_g_x, $y_point);
            $pdf->MultiCell($col_g_w, $h, U2T(""),1,'C',0);
            $pdf->SetXY( $col_h_x, $y_point);
            $pdf->MultiCell($col_h_w, $h, U2T(""),1,'C',0);
            $y_point += $h;

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

//Add page if overlap
if($y_point >= 175) {
    $pdf->AddPage();

    $page_index++;
    $pdf->SetFont('common', '', 12 );
    $pdf->SetXY(0, 10);
    $pdf->MultiCell(287, 8, U2T("หน้าที่ ".$page_index),0,'R',0);

    $y_point = 20;

    // $pdf->Image(base_url().PROJECTPATH.'assets/images/coop_profile/'.$_SESSION['COOP_IMG'],138,10,-1000);

    $pdf->SetFont('bold', '', 18 );
    $pdf->SetXY( 0, $y_point);
    $pdf->MultiCell($full_w, 8, U2T($_SESSION['COOP_NAME']),0,'C',0);
    $y_point += 10;
    $pdf->SetXY( 0, $y_point);
    $pdf->MultiCell($full_w, 8, U2T("งบทดลอง"),0,'C',0);
    $y_point += 10;
    $pdf->SetXY( 0, $y_point);
    $pdf->MultiCell($full_w, 8, U2T($textTitle),0,'C',0);
    $y_point += 10;

    $pdf->SetFont('common', '', $font_size );

    //Header
    $pdf->SetXY( $col_x, $y_point);
    $pdf->MultiCell($col_w, $line_height * 2, U2T("ลำดับที่"),1,'C',0);
    $pdf->SetXY( $col_a_x, $y_point);
    $pdf->MultiCell($col_a_w, $line_height * 2, U2T("เลขที่บัญชี"),1,'C',0);
    $pdf->SetXY( $col_b_x, $y_point);
    $pdf->MultiCell($col_b_w, $line_height * 2, U2T("ชื่อบัญชี"),1,'C',0);
    $pdf->SetXY( $col_c_x, $y_point);
    $pdf->MultiCell($col_c_w * 2, $line_height, U2T("ยอดยกมาเดือนก่อน"),1,'C',0);
    $pdf->SetXY( $col_e_x, $y_point);
    $pdf->MultiCell($col_e_w *2 , $line_height, U2T("รายการระหว่างเดือน"),1,'C',0);
    $pdf->SetXY( $col_g_x, $y_point);
    $pdf->MultiCell($col_g_w * 2, $line_height, U2T("ยอดคงเหลือยกไป"),1,'C',0);

    $y_point += $line_height;
    $pdf->SetXY( $col_c_x, $y_point);
    $pdf->MultiCell($col_c_w, $line_height, U2T("เดบิต"),1,'C',0);
    $pdf->SetXY( $col_d_x, $y_point);
    $pdf->MultiCell($col_d_w, $line_height, U2T("เครดิต"),1,'C',0);
    $pdf->SetXY( $col_e_x, $y_point);
    $pdf->MultiCell($col_e_w , $line_height, U2T("เดบิต"),1,'C',0);
    $pdf->SetXY( $col_f_x, $y_point);
    $pdf->MultiCell($col_f_w, $line_height, U2T("เครดิต"),1,'C',0);
    $pdf->SetXY( $col_g_x, $y_point);
    $pdf->MultiCell($col_g_w, $line_height, U2T("เดบิต"),1,'C',0);
    $pdf->SetXY( $col_h_x, $y_point);
    $pdf->MultiCell($col_h_w, $line_height, U2T("เครดิต"),1,'C',0);
    $y_point += $line_height;
}

$pdf->SetFont('common', '', $font_size );
if (!empty($sum_group_amount_debit) || !empty($sum_group_amount_credit) || !empty($sum_group_amount_debit_budget) || !empty($sum_group_amount_credit_budget)) {
    $pdf->SetLineWidth(0.5);
    $pdf->SetFont('bold', '', $font_size );
    $desc = $group_account_id == 1 ? "รวมสินทรัพย์"
            : ($group_account_id == 2 ? "รวมหนี้สิน"
            : ($group_account_id == 3 ? "รวมทุน"
            : ($group_account_id == 4 ? "รวมรายได้"
            : ($group_account_id == 5 ? "รวมค่าใช้จ่าย" : ""))));
    $pdf->SetXY( $col_x, $y_point);
    $pdf->MultiCell($col_w, $line_height, "",0,'C',0);
    $pdf->SetXY( $col_a_x, $y_point);
    $pdf->MultiCell($col_a_w, $line_height, "",0,'C',0);
    $pdf->SetXY( $col_b_x, $y_point);
    $pdf->MultiCell($col_b_w, $line_height, U2T($desc),0,'L',0);
    $y = $pdf->GetY();
    $pdf->SetXY( $col_c_x, $y_point);
    $pdf->MultiCell($col_c_w, $line_height, !empty($sum_group_amount_debit) ? number_format($sum_group_amount_debit,2) : "-",0,'R',0);
    $y = $y < $pdf->GetY() ? $pdf->GetY() : $y;
    $pdf->SetXY( $col_d_x, $y_point);
    $pdf->MultiCell($col_d_w, $line_height, !empty($sum_group_amount_credit) ? number_format($sum_group_amount_credit,2) : "-",0,'R',0);
    $y = $y < $pdf->GetY() ? $pdf->GetY() : $y;
    $pdf->SetXY( $col_e_x, $y_point);
    $pdf->MultiCell($col_e_w , $line_height, !empty($sum_group_amount_debit_ledger) ? number_format($sum_group_amount_debit_ledger,2) : "-",0,'R',0);
    $y = $y < $pdf->GetY() ? $pdf->GetY() : $y;
    $pdf->SetXY( $col_f_x, $y_point);
    $pdf->MultiCell($col_f_w, $line_height, !empty($sum_group_amount_credit_ledger) ? number_format($sum_group_amount_credit_ledger,2) : "-",0,'R',0);
    $y = $y < $pdf->GetY() ? $pdf->GetY() : $y;
    $pdf->SetXY( $col_g_x, $y_point);
    $pdf->MultiCell($col_g_w, $line_height, !empty($sum_group_amount_debit_budget) ? number_format($sum_group_amount_debit_budget,2) : "-",0,'R',0);
    $y = $y < $pdf->GetY() ? $pdf->GetY() : $y;
    $pdf->SetXY( $col_h_x, $y_point);
    $pdf->MultiCell($col_h_w, $line_height, !empty($sum_group_amount_credit_budget) ? number_format($sum_group_amount_credit_budget,2) : "-",0,'R',0);
    $y = $y < $pdf->GetY() ? $pdf->GetY() : $y;

    $h = $y-$y_point;
    $pdf->SetXY( $col_x, $y_point);
    $pdf->MultiCell($col_w, $h, "",1,'C',0);
    $pdf->SetXY( $col_a_x, $y_point);
    $pdf->MultiCell($col_a_w, $h, "",1,'C',0);
    $pdf->SetXY( $col_b_x, $y_point);
    $pdf->MultiCell($col_b_w, $h, "",1,'L',0);
    $pdf->SetXY( $col_c_x, $y_point);
    $pdf->MultiCell($col_c_w, $h, "",1,'C',0);
    $pdf->SetXY( $col_d_x, $y_point);
    $pdf->MultiCell($col_d_w, $h, U2T(""),1,'C',0);
    $pdf->SetXY( $col_e_x, $y_point);
    $pdf->MultiCell($col_e_w , $h, U2T(""),1,'C',0);
    $pdf->SetXY( $col_f_x, $y_point);
    $pdf->MultiCell($col_f_w, $h, U2T(""),1,'C',0);
    $pdf->SetXY( $col_g_x, $y_point);
    $pdf->MultiCell($col_g_w, $h, U2T(""),1,'C',0);
    $pdf->SetXY( $col_h_x, $y_point);
    $pdf->MultiCell($col_h_w, $h, U2T(""),1,'C',0);
    $y_point += $h;
    $sum_group_amount_debit  = 0;
    $sum_group_amount_credit = 0;
    $sum_group_amount_credit_ledger  = 0;
    $sum_group_amount_debit_ledger  = 0;
    $sum_group_amount_credit_budget  = 0;
    $sum_group_amount_debit_budget  = 0;
}

//Add page if overlap
if($y_point >= 175) {
    $pdf->AddPage();
    $y_point = 20;

    // $pdf->Image(base_url().PROJECTPATH.'assets/images/coop_profile/'.$_SESSION['COOP_IMG'],138,10,-1000);

    $pdf->SetFont('bold', '', 18 );
    $pdf->SetXY( 0, $y_point);
    $pdf->MultiCell($full_w, 8, U2T($_SESSION['COOP_NAME']),0,'C',0);
    $y_point += 10;
    $pdf->SetXY( 0, $y_point);
    $pdf->MultiCell($full_w, 8, U2T("งบทดลอง"),0,'C',0);
    $y_point += 10;
    $pdf->SetXY( 0, $y_point);
    $pdf->MultiCell($full_w, 8, U2T($textTitle),0,'C',0);
    $y_point += 10;

    $pdf->SetFont('common', '', $font_size );

    //Header
    $pdf->SetXY( $col_x, $y_point);
    $pdf->MultiCell($col_w, $line_height * 2, U2T("ลำดับที่"),1,'C',0);
    $pdf->SetXY( $col_a_x, $y_point);
    $pdf->MultiCell($col_a_w, $line_height * 2, U2T("เลขที่บัญชี"),1,'C',0);
    $pdf->SetXY( $col_b_x, $y_point);
    $pdf->MultiCell($col_b_w, $line_height * 2, U2T("ชื่อบัญชี"),1,'C',0);
    $pdf->SetXY( $col_c_x, $y_point);
    $pdf->MultiCell($col_c_w * 2, $line_height, U2T("ยอดยกมาเดือนก่อน"),1,'C',0);
    $pdf->SetXY( $col_e_x, $y_point);
    $pdf->MultiCell($col_e_w *2 , $line_height, U2T("รายการระหว่างเดือน"),1,'C',0);
    $pdf->SetXY( $col_g_x, $y_point);
    $pdf->MultiCell($col_g_w * 2, $line_height, U2T("ยอดคงเหลือยกไป"),1,'C',0);

    $y_point += $line_height;
    $pdf->SetXY( $col_c_x, $y_point);
    $pdf->MultiCell($col_c_w, $line_height, U2T("เดบิต"),1,'C',0);
    $pdf->SetXY( $col_d_x, $y_point);
    $pdf->MultiCell($col_d_w, $line_height, U2T("เครดิต"),1,'C',0);
    $pdf->SetXY( $col_e_x, $y_point);
    $pdf->MultiCell($col_e_w , $line_height, U2T("เดบิต"),1,'C',0);
    $pdf->SetXY( $col_f_x, $y_point);
    $pdf->MultiCell($col_f_w, $line_height, U2T("เครดิต"),1,'C',0);
    $pdf->SetXY( $col_g_x, $y_point);
    $pdf->MultiCell($col_g_w, $line_height, U2T("เดบิต"),1,'C',0);
    $pdf->SetXY( $col_h_x, $y_point);
    $pdf->MultiCell($col_h_w, $line_height, U2T("เครดิต"),1,'C',0);
    $y_point += $line_height;
}

$pdf->SetFont('bold', '', $font_size );
$pdf->SetXY( $col_x, $y_point);
$pdf->MultiCell($col_w, $line_height, "",0,'C',0);
$pdf->SetXY( $col_a_x, $y_point);
$pdf->MultiCell($col_a_w, $line_height, "",0,'C',0);
$pdf->SetXY( $col_b_x, $y_point);
$pdf->MultiCell($col_b_w, $line_height, U2T("รวมทั้งสิ้น"),0,'L',0);
$y = $pdf->GetY();
$pdf->SetXY( $col_c_x, $y_point);
$pdf->MultiCell($col_c_w, $line_height, $data_sum['debit_hirtorical'] != 0 ? number_format($data_sum['debit_hirtorical'],2) : '-',0,'R',0);
$y = $y < $pdf->GetY() ? $pdf->GetY() : $y;
$pdf->SetXY( $col_d_x, $y_point);
$pdf->MultiCell($col_d_w, $line_height, $data_sum['credit_hirtorical'] != 0 ? number_format($data_sum['credit_hirtorical'],2) : '-',0,'R',0);
$y = $y < $pdf->GetY() ? $pdf->GetY() : $y;
$pdf->SetXY( $col_e_x, $y_point);
$pdf->MultiCell($col_e_w , $line_height, $data_sum['debit'] !=0 ? number_format($data_sum['debit'],2) : '-',0,'R',0);
$y = $y < $pdf->GetY() ? $pdf->GetY() : $y;
$pdf->SetXY( $col_f_x, $y_point);
$pdf->MultiCell($col_f_w, $line_height, $data_sum['credit'] !=0 ? number_format($data_sum['credit'],2) : '-',0,'R',0);
$y = $y < $pdf->GetY() ? $pdf->GetY() : $y;
$pdf->SetXY( $col_g_x, $y_point);
$pdf->MultiCell($col_g_w, $line_height, $data_sum['carryfordard_debit'] != 0 ? number_format($data_sum['carryfordard_debit'],2) : '-',0,'R',0);
$y = $y < $pdf->GetY() ? $pdf->GetY() : $y;
$pdf->SetXY( $col_h_x, $y_point);
$pdf->MultiCell($col_h_w, $line_height, $data_sum['carryfordard_credit'] !=0 ? number_format($data_sum['carryfordard_credit'],2) : '-',0,'R',0);
$y = $y < $pdf->GetY() ? $pdf->GetY() : $y;

$h = $y-$y_point;
$pdf->SetLineWidth(0.5);
$pdf->SetXY( $col_x, $y_point);
$pdf->MultiCell($col_w, $h, "",1,'C',0);
$pdf->SetXY( $col_a_x, $y_point);
$pdf->MultiCell($col_a_w, $h, "",1,'C',0);
$pdf->SetXY( $col_b_x, $y_point);
$pdf->MultiCell($col_b_w, $h, "",1,'L',0);
$pdf->SetXY( $col_c_x, $y_point);
$pdf->MultiCell($col_c_w, $h, "",1,'C',0);
$pdf->SetXY( $col_d_x, $y_point);
$pdf->MultiCell($col_d_w, $h, U2T(""),1,'C',0);
$pdf->SetXY( $col_e_x, $y_point);
$pdf->MultiCell($col_e_w , $h, U2T(""),1,'C',0);
$pdf->SetXY( $col_f_x, $y_point);
$pdf->MultiCell($col_f_w, $h, U2T(""),1,'C',0);
$pdf->SetXY( $col_g_x, $y_point);
$pdf->MultiCell($col_g_w, $h, U2T(""),1,'C',0);
$pdf->SetXY( $col_h_x, $y_point);
$pdf->MultiCell($col_h_w, $h, U2T(""),1,'C',0);
$y_point += $h;

$pdf->SetLineWidth(0.1);
$pdf->SetFont('common', '', $font_size );
$pdf->Output();
