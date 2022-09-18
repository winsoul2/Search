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
$line_height = 8;

$col_x_1 = 10;
$col_w_1 = 20;
$col_x_2 = $col_w_1 + $col_x_1;
$col_w_2 = 70+20;
$col_x_3 = $col_w_2 + $col_x_2;
$col_w_3 = 20;
$col_x_4 = $col_w_3 + $col_x_3;
$col_w_4 = 20;
$col_x_5 = $col_w_4 + $col_x_4;
$col_w_5 = 20;
$col_x_6 = $col_w_5 + $col_x_5;
$col_w_6 = 20;
$col_x_7 = $col_w_6 + $col_x_6;
$col_w_7 = 25;

$y_point = 0;

$page_index = 0;
foreach($coop_account_chart as $key => $value){
	if($y_point > 270 || $y_point == 0) {
		$pdf->AddPage();
		$page_index++;

		$pdf->SetMargins(0, 0, 0);
		$border = 0;
		$pdf->SetTextColor(0, 0, 0);
		$pdf->SetAutoPageBreak(true,0);

		$y_point = 17;
		$pdf->SetFont('common', '', 12);
		$pdf->SetXY( 10, $y_point );
		$pdf->MultiCell(190, 6, U2T("วันที่พิมพ์ : ".date("Y/m/d")), 0, "L");
		$pdf->SetXY( 10, $y_point + 6 );
		$pdf->MultiCell(190, 6, U2T("เวลาพิมพ์ : ".date("h:i:s")), 0, "L");
		$y_point = 13;
		$pdf->SetXY( 160, $y_point );
		$pdf->MultiCell(44, 6, U2T("หน้าที่ ".$page_index." / {nb}"), 0, "R");
		$pdf->SetXY( 160, $y_point+6 );
		$pdf->MultiCell(40, 6, U2T("ผู้พิมพ์ ".$_SESSION['USER_NAME']), 0, "R");
		$y_point = 10;

		$pdf->SetFont('bold', '', 20 );
		$pdf->SetXY( 0, $y_point );
		$pdf->MultiCell(210, 10, U2T($_SESSION['COOP_NAME']), 0, "C");

		$pdf->SetFont('bold', '', $font_size );
		$y_point += 10;
		$pdf->SetXY( 0, $y_point );
		$pdf->MultiCell(210, 8, U2T("รายงานผังบัญชี"), 0, "C");
		$y_point += 10;
		$pdf->SetXY($col_x_1, $y_point);
		$pdf->MultiCell($col_w_1, $line_height, U2T("รหัสบัญชี"), 1, "C");
		$pdf->SetXY($col_x_2, $y_point);
		$pdf->MultiCell($col_w_2, $line_height, U2T("ชื่อบัญชี"), 1, "C");
		$pdf->SetXY($col_x_3, $y_point);
		$pdf->MultiCell($col_w_3, $line_height, U2T("หมวดบัญชี"), 1, "C");
		$pdf->SetXY($col_x_4, $y_point);
		$pdf->MultiCell($col_w_4, $line_height, U2T("ดุลบัญชี"), 1, "C");
		$pdf->SetXY($col_x_5, $y_point);
		$pdf->MultiCell($col_w_5, $line_height, U2T("ประเภทบัญชี"), 1, "C");
		$pdf->SetXY($col_x_6, $y_point);
		$pdf->MultiCell($col_w_6, $line_height, U2T("รหัสบัญชีคุม"), 1, "C");
		
		$y_point += 8;
	
	}
	$pdf->SetFont('common', '', $font_size);

	$pdf->SetXY($col_x_1, $y_point);
	$pdf->MultiCell($col_w_1, $line_height, U2T($value['account_chart_id']), 0, "C");
	$h = $pdf->GetY();
	$pdf->SetXY($col_x_2, $y_point);
	$pdf->MultiCell($col_w_2, $line_height, U2T($value['account_chart']), 0, "L");
	$h = $pdf->GetY() > $h ? $pdf->GetY() : $h;
	$pdf->SetXY($col_x_3, $y_point);
	$groups = substr($value['account_chart_id'],0,1);
	$pdf->MultiCell($col_w_3, $line_height, U2T($account_chart_groups[$groups-1]['account_chart']), 0, "C");
	$h = $pdf->GetY() > $h ? $pdf->GetY() : $h;
	$pdf->SetXY($col_x_4, $y_point);
	$pdf->MultiCell($col_w_4, $line_height, U2T($value['entry_type'] == 1 ? "เดบิต" : "เครดิต"), 0, "C");
	$h = $pdf->GetY() > $h ? $pdf->GetY() : $h;
	$pdf->SetXY($col_x_5, $y_point);
	$pdf->MultiCell($col_w_5, $line_height, U2T($value['type'] == 1 || $value["type"] == 2 ? "บัญชีคุม" : "บัญชีย่อย"), 0, "C");
	$h = $pdf->GetY() > $h ? $pdf->GetY() : $h;
	$pdf->SetXY($col_x_6, $y_point);
	$pdf->MultiCell($col_w_6, $line_height, U2T($value['account_parent_id']), 0, "C");
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
}
$pdf->AliasNbPages();
$pdf->Output();