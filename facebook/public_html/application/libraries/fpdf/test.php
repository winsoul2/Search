<?php
	define('FPDF_FONTPATH',"{$_SERVER["DOCUMENT_ROOT"]}/class/fpdf/font/");
	include "{$_SERVER["DOCUMENT_ROOT"]}/class/fpdf/1.8.1/fpdf.php";

    // Establish / Get variables

    function GETVAR($key, $default = null, $prefix = null, $suffix = null) {
        return isset($_GET[$key]) ? $prefix . $_GET[$key] . $suffix : $prefix . $default . $suffix;
    }

    $font = GETVAR('font','fontawesome-webfont1','','.php');
		//$font = GETVAR('font','fontawesome-webfont2','','.php');
		//$font = GETVAR('font','fontawesome-webfont3','','.php');
		//$font = GETVAR('font','fontawesome','','.php');
    $pdf = new FPDF('L','mm',array(268.33,415.3));

    $pdf->AddPage();
    $pdf->SetMargins(0,0,0);
    $pdf->SetAutoPageBreak(0,0);
    // add custom fonts

    //$pdf->AddFont('H','','helvetica.php');
		$pdf->AddFont('H','','angsa.php');
    $pdf->AddFont('FA','',$font);

    $pdf->SetFillColor(200,200,200);

    $pdf->SetXY(9,9);

    for ($i = 32; $i <= 256; $i++) {

        $y = $pdf->GetY();
        $x = $pdf->GetX();

        $pdf->SetX($x);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('FA','',14);
        $pdf->Cell(12,12,chr($i),1,0,'C');

        $pdf->SetXY($x,$y+12);

        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('H','',14);
        $pdf->Cell(12,12,$i,1,0,'C',1);

        $y = $pdf->GetY();
        $x = $pdf->GetX();

        $pdf->SetXY($x,$y-12);

        if ($x > 400) {
         $pdf->SetXY(9,$y+14);       
        }

        if ($i == 328){
            $pdf->AddPage();
        }

    }

    $pdf->Output();