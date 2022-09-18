<?php
function U2T($text) { return @iconv("UTF-8", "TIS-620//IGNORE", ($text)); }
function num_format($text) {
    if($text!=''){
        return number_format($text,2);
    }else{
        return '';
    }
}

$pdf = new FPDI('L','mm', array(140,203));

$pdf->AddPage();
$pdf->AddFont('THSarabunNew', '', 'THSarabunNew.php');
$pdf->SetFont('THSarabunNew', '', 13 );
$pdf->SetMargins(0, 0, 0);
$border = 0;
$pdf->SetTextColor(0, 0, 0);
$pdf->SetAutoPageBreak(true,0);

$y_point = 16;
$pdf->SetXY( 172, $y_point );
$pdf->MultiCell(30, 5, U2T($receipt_id), $border, 1);
$y_point = 23;
$pdf->SetXY( 172, $y_point );
$pdf->MultiCell(30, 5, U2T($this->center_function->mydate2date(date('Y-m-d'))), $border, 1);

$y_point = 31;
$pdf->SetXY( 26, $y_point );
$pdf->MultiCell(90, 5, U2T($name), $border, 1);
$pdf->SetXY( 125, $y_point );
$pdf->MultiCell(30, 5, U2T($member_id), $border, 1);
$pdf->SetXY( 172, $y_point );
$pdf->MultiCell(30, 5, U2T($member_data['employee_id']), $border, 1);
$y_point = 38;
$pdf->SetXY( 26, $y_point );
$pdf->MultiCell(30, 5, U2T(@$mem_group_arr[$member_data['level']]), $border, 1);
$pdf->SetXY( 125, $y_point );
$pdf->MultiCell(30, 5, U2T(@$mem_group_arr[$member_data['faction']]), $border, 1);

$y_point = 46;
$sum = 0;
foreach($transaction_data as $key => $value){
    $y_point += 7;

    $pdf->SetXY( 7, $y_point );
    $pdf->MultiCell(70, 5, U2T($value['loan_type']!=''?$value['loan_type']:$value['account_list']), $border, 1);
    $pdf->SetXY( 77, $y_point );
    $pdf->MultiCell(15, 5, U2T(''), $border, 'C');
    $pdf->SetXY( 90, $y_point );
    $pdf->MultiCell(27, 5, U2T(num_format($value['principal_payment'])), $border, 'R');
    $pdf->SetXY( 118, $y_point );
    $pdf->MultiCell(26, 5, U2T(num_format($value['interest'])), $border, 'R');
    $pdf->SetXY( 144, $y_point );
    $pdf->MultiCell(26, 5, U2T(num_format($value['total_amount'])), $border, 'R');
    $pdf->SetXY( 169, $y_point );
    $pdf->MultiCell(30, 5, U2T(num_format($value['loan_amount_balance'])), $border, 'R');
    $sum += $value['total_amount'];
}

$y_point = 109;
$pdf->SetXY( 7, $y_point );
$pdf->MultiCell(135, 5, U2T($this->center_function->convert(number_format($sum,2,'.',''))), $border, 'R');
$pdf->SetXY( 144, $y_point );
$pdf->MultiCell(26, 5, U2T(num_format($sum)), $border, 'R');

$pdf->Image(base_url().PROJECTPATH.'/assets/images/coop_signature/'.$signature['signature_1'],25,125,25,'','','');
$pdf->Image(base_url().PROJECTPATH.'/assets/images/coop_signature/'.$signature['signature_2'],120,125,25,'','','');

$pdf->Output();