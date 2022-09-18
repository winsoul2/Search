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
$pdf->AddFont('c', '', 'cordiau.php'); //Common font
$pdf->AddFont('b','','cordiaub.php'); //Common bold
$font_size = 14;
$line_height = 8;

$col_x_1 = 10;
$col_w_1 = 15;
$col_x_2 = $col_w_1 + $col_x_1;
$col_w_2 = 20;
$col_x_3 = $col_w_2 + $col_x_2;
$col_w_3 = 50;
$col_x_4 = $col_w_3 + $col_x_3;
$col_w_4 = 53;
$col_x_5 = $col_w_4 + $col_x_4;
$col_w_5 = 18;
$col_x_6 = $col_w_5 + $col_x_5;
$col_w_6 = 18;
$col_x_7 = $col_w_6 + $col_x_6;
$col_w_7 = 18;

$page_index = 0;
$y_point = 0;
$title_date = "";

foreach($datas as $index=>$data) {
	if($index == 0 || (($y_point + 42) > 297)) {
		$pdf->AddPage();
		$page_index++;

		$pdf->SetMargins(0, 0, 0);
		$border = 0;
		$pdf->SetTextColor(0, 0, 0);
		$pdf->SetAutoPageBreak(true,0);

		$y_point = 15;
		$pdf->SetFont('c', '', 12);
		$pdf->SetXY( 10, $y_point);
		$pdf->MultiCell(190, 8, U2T("หน้าที่ ".$page_index), 0, "R");

		$y_point += 5;
		$pdf->SetFont('b', '', 20 );
		$pdf->SetXY( 0, $y_point );
		$pdf->MultiCell(210, 10, U2T($cremation['full_name']), 0, "C");

		$pdf->SetFont('c', '', 12);
		$y_point += 8;
		$pdf->SetXY( 10, $y_point);
		$pdf->MultiCell(190, 8, U2T("วันที่ ".$this->center_function->ConvertToThaiDate(@date('Y-m-d'),0,0)), 0, "R");
		$y_point += 5;
		$pdf->SetXY( 10, $y_point);
		$pdf->MultiCell(190, 8, U2T("ผู้ทำรายการ ".$_SESSION['USER_NAME']), 0, "R");

        $pdf->SetFont('b', '', $font_size );
		$y_point += 10;
		$pdf->SetXY( 0, $y_point );
        $pdf->MultiCell(210, 8, U2T("รายงานการเรียกเก็บ"), 0, "C");

		$y_point += 12;
		$pdf->SetFont('b', '', $font_size - 2);
		$pdf->SetXY($col_x_1, $y_point);
		$pdf->MultiCell($col_w_1, $line_height, U2T("ลำดับ"), 1, "C");
		$pdf->SetXY($col_x_2, $y_point);
		$pdf->MultiCell($col_w_2, $line_height, U2T("เลขฌาปนกิจ"), 1, "C");
		$pdf->SetXY($col_x_3, $y_point);
		$pdf->MultiCell($col_w_3, $line_height, U2T("ชื่อ-นามสกุล"), 1, "C");
		$pdf->SetXY($col_x_4, $y_point);
		$pdf->MultiCell($col_w_4, $line_height, U2T("รอบสมัคร"), 1, "C");
		$pdf->SetXY($col_x_5, $y_point);
		$pdf->MultiCell($col_w_5, $line_height, U2T("เรียกเก็บ"), 1, "C");
		$pdf->SetXY($col_x_6, $y_point);
		$pdf->MultiCell($col_w_6, $line_height, U2T("ค่าบำรุง"), 1, "C");
		$pdf->SetXY($col_x_7, $y_point);
		$pdf->MultiCell($col_w_7, $line_height, U2T("ปี"), 1, "C");
		$y_point += $line_height;
	}

	$pdf->SetFont('c', '', $font_size);
	$pdf->SetXY($col_x_1, $y_point);
	$pdf->MultiCell($col_w_1, $line_height, $index+1, 0, "C");
	$y = $pdf->GetY();
	$pdf->SetXY($col_x_2, $y_point);
	$pdf->MultiCell($col_w_2, $line_height, $data["cremation_member_id"], 0, "C");
	$y = $y < $pdf->GetY() ? $pdf->GetY() : $y;
	$pdf->SetXY($col_x_3, $y_point);
	$pdf->MultiCell($col_w_3, $line_height, U2T($data["prename_id"].$data["firstname_th"]." ".$data["lastname_th"]), 0, "L");
	$y = $y < $pdf->GetY() ? $pdf->GetY() : $y;
	$pdf->SetXY($col_x_4, $y_point);
	$pdf->MultiCell($col_w_4, $line_height, $data["period_name"], 0, "L");
	$y = $y < $pdf->GetY() ? $pdf->GetY() : $y;
	$pdf->SetXY($col_x_5, $y_point);
	$pdf->MultiCell($col_w_5, $line_height, number_format($data["fee"],2), 0, "R");
	$y = $y < $pdf->GetY() ? $pdf->GetY() : $y;
	$pdf->SetXY($col_x_6, $y_point);
	$pdf->MultiCell($col_w_6, $line_height, number_format($data["assoc_fee"],2), 0, "R");
	$y = $y < $pdf->GetY() ? $pdf->GetY() : $y;
	$pdf->SetXY($col_x_7, $y_point);
	$pdf->MultiCell($col_w_7, $line_height, $data["year"], 0, "C");
	$y = $y < $pdf->GetY() ? $pdf->GetY() : $y;

	$h = $y-$y_point;
	$pdf->SetXY( $col_x_1, $y_point);
	$pdf->MultiCell($col_w_1, $h, "",1,'C',0);
	$pdf->SetXY( $col_x_2, $y_point);
	$pdf->MultiCell($col_w_2, $h, "",1,'C',0);
	$pdf->SetXY( $col_x_3, $y_point);
	$pdf->MultiCell($col_w_3, $h, "",1,'C',0);
	$pdf->SetXY( $col_x_4, $y_point);
	$pdf->MultiCell($col_w_4, $h, "",1,'C',0);
	$pdf->SetXY( $col_x_5, $y_point);
	$pdf->MultiCell($col_w_5, $h, "",1,'C',0);
	$pdf->SetXY( $col_x_6, $y_point);
	$pdf->MultiCell($col_w_6, $h, "",1,'C',0);
	$pdf->SetXY( $col_x_7, $y_point);
	$pdf->MultiCell($col_w_7, $h, "",1,'C',0);
	$y_point += $h;
}

$pdf->Output();
exit;