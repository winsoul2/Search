<?php
function U2T($text) { return @iconv("UTF-8", "TIS-620//IGNORE", ($text)); }


// echo"<pre>";print_r($rows);exit;

$pdf = new FPDI('L','mm', array($style['width_page'], $style['height_page']));
foreach ($rows as $key => $value) {
    $pdf->SetAutoPageBreak(true, 0);
    $pdf->AddPage();
    $pdf->AddFont('THSarabunNew', '', 'THSarabunNew.php');
    $pdf->AddFont('THSarabunNewB','','THSarabunNew-Bold.php');
    $pdf->SetMargins($style['left_margin'], $style['top_margin'], $style['right_margin']);   
    $border = 0;
    $pdf->SetTextColor(0, 0, 0);
    foreach ($value as $keys => $values) {

        $pdf->SetXY( $values['x'], $values['y'] );
        $pdf->SetFont('THSarabunNewB', '', $values['font_size'] );
        $pdf->cell($values['width'], 8, U2T($values['text']), $border, 1, $values['align']);

       
    }
    // $pdf->SetXY( 120, 120 );
    // $pdf->SetFont('THSarabunNewB', '', $values['font_size'] );
    // $pdf->cell(120, 8, U2T($point), $border, 1, 20);
}
$pdf->Output();

// echo"<pre>";print_r($a);
