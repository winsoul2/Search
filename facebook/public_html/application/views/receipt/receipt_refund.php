<?php
	function U2T($text) { return @iconv("UTF-8", "TIS-620//IGNORE", ($text)); }

    $pdf = new FPDF('L','mm','A5');
	$x = 10;
	$full_w = 187;
	for($p = 1; $p < 2; $p++) {
		$pdf->addPage();

		$pdf->AddFont('THSarabunNew', '', 'THSarabunNew.php');
		$pdf->AddFont('THSarabunNew-Bold', '', 'THSarabunNew-Bold.php');
		$pdf->SetFont('THSarabunNew-Bold', '', 13 );

		$border = 0;
		$pdf->SetTextColor(0, 0, 0);
		$pdf->SetAutoPageBreak(true,0);

		$pay_type = array('cash'=>'เงินสด', 'cheque'=>'เช็คธนาคาร', 'transfer'=>'เงินโอน');
		$y_point = 15.5;

		$y_point = 12;
		$pdf->SetFont('THSarabunNew-Bold', '', 16 );
		$y_point += 8;
		$pdf->SetXY(10, $y_point);
		
		$pdf->MultiCell(190, 8, U2T("ใบเสร็จรับเงิน"), 0, 'C');
		

		$pdf->SetFont('THSarabunNew-Bold', '', 13 );
		$y_point += 8;
		$pdf->SetXY( 10, $y_point);

		$y_point += 5;
		$pdf->SetXY( 10, $y_point);
		$pdf->MultiCell(90, 5, U2T("วันที่ ".$this->center_function->ConvertToThaiDate($row['receipt_datetime'],0,0)), $border, 'L');
		$pdf->SetXY( 10, $y_point);
		$pdf->MultiCell($full_w, 5, U2T("เลขที่ ".$row['receipt_refund_id']), $border, 'R');
		
		$y_point += 5;
		$pdf->SetXY( 10, $y_point);
		$pdf->MultiCell(90, 5, U2T('ได้รับเงินจาก : '.$row['pay_for']), $border, 1);
		
		
		$y_point += 5;
		$pdf->SetXY( 10, $y_point);
		$pdf->MultiCell(90, 5, U2T('สมาชิกทะเบียนที่ : '.@$row['member_id']), $border, 1);
		//echo '<pre>'; print_r($row); echo '</pre>';
		
		$y_point += 5;
		$b_x = 10;
		$c_x = 140;
		$d_x = 160;

		$b_w = 150;
		$c_w = 20;
		$d_w = 37;

		$y_point += 2;
		$pdf->SetXY($b_x, $y_point);
		$pdf->MultiCell($b_w, 8, U2T("รายการ"), "TB", "C");
		$pdf->SetXY($d_x, $y_point);
		$pdf->MultiCell($d_w, 8, U2T("จำนวน(บาท)"), "TB", "C");
		$y_point += 8;
		$table_start_point = $y_point;
		for($i = 0; $i <= 3; $i++) {
			$pdf->SetXY($b_x, $y_point);
			$pdf->MultiCell($b_w, 8, U2T(""), 0, 1);
			$pdf->SetXY($d_x, $y_point);
			$pdf->MultiCell($d_w, 8, U2T(""), 0, 1);
			$y_point += 8;
		}

		$index = 1;
		foreach($rs_detail as $key => $row_detail){
			$pdf->SetXY($b_x, $table_start_point);
			$pdf->MultiCell($b_w, 8, U2T($row_detail['pay_description']), 0, 'L');
			$pdf->SetXY($d_x, $table_start_point);
			$pdf->MultiCell($d_w, 8, number_format($row_detail['total_amount'],2), 0, 'R');
			$table_start_point += 8;
		}

        $y_point += 20;
		$pdf->SetXY($b_x, $y_point);
		$pdf->MultiCell(130, 8, U2T($this->center_function->convert($row['sumcount'])), 1, "C");
		
		$pdf->SetXY($c_x, $y_point);		
		$pdf->MultiCell($c_w, 8, U2T("รวมเงิน"), 0, "C");
		$total_amount = number_format($row['sumcount'],2);
		$pdf->SetXY($d_x, $y_point);
		$pdf->MultiCell($d_w, 8, U2T($total_amount.' บาท'), 0, "R");

		$y_point += 14;
		$pdf->SetXY(10, $y_point);
		$pdf->MultiCell(95, 8, U2T("ลงชื่อ..........................................................................เหรัญญิก/ผู้จัดการ"), 0, "C");
		$pdf->SetXY(110, $y_point);
		$pdf->MultiCell(90, 8, U2T("ลงชื่อ..........................................................................เจ้าหน้าที่ผู้รับเงิน"), 0, "C");

		if(!empty($signature['signature_3'])) {
            if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/assets/images/coop_signature/' . $signature['signature_3'])) {
                $pdf->Image($_SERVER['DOCUMENT_ROOT'] . '/assets/images/coop_signature/' . $signature['signature_3'], 45, $y_point - 8, 20, '', '', '');
            }
        }

		$pdf->SetFont('THSarabunNew-Bold', '', 13 );
		$pdf->SetTextColor(0, 0, 0);
		$y_point += 8;
		$pdf->SetXY(10, $y_point);
		$pdf->MultiCell(95, 8, U2T("(".$signature['manager_name'].")"), 0, "C");
		$pdf->SetXY(110, $y_point);
		$pdf->MultiCell(90, 8, U2T("(                                                                  )"), 0, "C");

		$y_point += 0;
		$pdf->SetFont('THSarabunNew','',18);
		$pdf->SetTextColor(0, 0, 204);
		$pdf->SetXY(0, $y_point+5);
		if ($row['pay_type'] == "0"){
            $pdf->MultiCell(210, 10, U2T("เงินสด"), 0, "C",0);
        }else{
			$account_bank_name = "";
			if($row['account_bank'] != "Other") {
				$account_bank_name = str_replace('เงินฝาก',"ฝาก",$row['account_bank_name']);
				$account_bank_name = str_replace(' ออมทรัพย์',"",$account_bank_name);
			}
            $pdf->MultiCell(210, 10, U2T("โอนเงิน".$account_bank_name), 0, "C",0);
        }
	}

	$pdf->Output();
