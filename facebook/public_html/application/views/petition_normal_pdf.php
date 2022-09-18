<?php
function U2T($text) { return @iconv("UTF-8", "TIS-620//IGNORE", ($text)); }
function num_format($text) {
    if($text!=''){
        return number_format($text,2);
    }else{
        return '';
    }
}

	$filename = $_SERVER["DOCUMENT_ROOT"].PROJECTPATH."/assets/document/petition_normal_pdf.pdf" ;
	//echo $filename;exit;
	
	$pdf = new FPDI();
	
	$pageCount_1 = $pdf->setSourceFile($filename);
	for ($pageNo = 1; $pageNo <= $pageCount_1; $pageNo++) {	
	$pdf->AddPage();
		$tplIdx = $pdf->importPage($pageNo); 
		$pdf->useTemplate($tplIdx, 0, 0, 0, 0, true);
		
		$pdf->AddFont('THSarabunNew', '', 'THSarabunNew.php');
		$pdf->SetFont('THSarabunNew', '', 13 );
		
		$border = 0;
		$pdf->SetTextColor(0, 0, 0);
		$pdf->SetAutoPageBreak(true,0);
		
		
		$borrower_rank = "--- ตำแหน่ง ---";

		$pay_type = array('cash'=>'เงินสด', 'cheque'=>'เช็คธนาคาร', 'transfer'=>'เงินโอน');
		if($pageNo == '1'){
			$y_point = 12.5;
			$pdf->SetXY( 164, $y_point );
			$pdf->MultiCell(40, 5, U2T($data['contract_number']), $border, 1);
			
			$y_point = 18.5;
			$pdf->SetXY( 157, $y_point );
			$pdf->MultiCell(40, 5, U2T($this->center_function->ConvertToThaiDate($data['createdatetime'],0,0)), $border, 1);
			
			$y_point = 49;
			$pdf->SetXY( 127, $y_point );
			$pdf->MultiCell(40, 5, U2T(''), $border, 1);
			
			$y_point = 59.5;
			$pdf->SetXY( 130, $y_point );
			$pdf->MultiCell(40, 5, U2T($this->center_function->ConvertToThaiDate($data['createdatetime'],0,0)), $border, 1);
			
			$y_point = 66.3;
			$pdf->SetXY( 43, $y_point );
			$pdf->MultiCell(85, 5, U2T($data['prename_short'].$data['firstname_th']." ".$data['lastname_th']), $border, 1);
			// $pdf->SetXY( 140, $y_point );
			// $pdf->MultiCell(40, 5, U2T($data['member_id']), $border, 1);
			
			$y_point = 82;
			// $pdf->SetXY( 51, $y_point );
			// $pdf->MultiCell(40, 5, U2T($data['id_card']), $border, 1);
			$pdf->SetXY( 47, $y_point );
			$pdf->MultiCell(11, 5, U2T($this->center_function->cal_age($data['birthday'])), $border, 'C');
			// $pdf->SetXY( 158, $y_point );
			// $pdf->MultiCell(20, 5, U2T($data['c_address_no']), $border, 1);			
		
			
			$y_point = 90;
			$pdf->SetXY( 115, $y_point );
			$pdf->MultiCell(60, 5, U2T($borrower_rank), $border, 1);
			$pdf->SetXY( 135, $y_point );
			$pdf->MultiCell(60, 5, U2T($data['position']), $border, 1);
			$pdf->SetXY( 175, $y_point );
			$pdf->MultiCell(30, 5, U2T($data['tel']), $border, 1);

	
			$y_point = 97;
			$pdf->SetXY( 47, $y_point );
			$pdf->MultiCell(35, 5, U2T(num_format($data['salary'])), $border, 'R');			
			
			//ADDRESS!!!! (No data)
			// $pdf->SetXY( 24, $y_point );
			// $pdf->MultiCell(15, 5, U2T($data['c_address_moo']), $border, 1);
			// $pdf->SetXY( 42, $y_point );
			// $pdf->MultiCell(38, 5, U2T($data['district_name']), $border, 1);
			// $pdf->SetXY( 77, $y_point );
			// $pdf->MultiCell(38, 5, U2T($data['amphur_name']), $border, 1);
			// $pdf->SetXY( 114, $y_point );
			// $pdf->MultiCell(38, 5, U2T($data['province_name']), $border, 1);
			// $pdf->SetXY( 171, $y_point );
			// $pdf->MultiCell(38, 5, U2T($data['c_zipcode']), $border, 1);
			
			$y_point = 100.5;
			$pdf->SetXY( 36, $y_point );
			$pdf->MultiCell(30, 5, U2T($data['mobile']), $border, 1);
			
			
			$y_point = 127;
			$pdf->SetXY( 76, $y_point );
			$pdf->MultiCell(31, 5, U2T(num_format($data['loan_amount'])), $border, 'R');
			$pdf->SetXY( 115, $y_point );
			$pdf->MultiCell(76, 5, U2T($this->center_function->convert($data['loan_amount'])), $border, 'C');
			
			$y_point = 134;
			$pdf->SetXY( 62, $y_point );
			$pdf->MultiCell(120, 5, U2T($data['loan_reason']), $border, 1);
		}else if($pageNo == '2'){
			$y_point = 128;
			$pdf->SetXY( 123, $y_point );
			$pdf->MultiCell(63, 5, U2T($data['firstname_th']." ".$data['lastname_th']), $border, 'C');
			
			$y_point = 258;
			$pdf->SetXY( 98, $y_point );
			$pdf->MultiCell(53, 5, U2T($data['firstname_th']." ".$data['lastname_th']), $border, 'C');
		}else if($pageNo == '3'){
			$y_point = 47;
			$pdf->SetXY( 117, $y_point );
			$pdf->MultiCell(40, 5, U2T($this->center_function->ConvertToThaiDate($data['createdatetime'],0,0)), $border, 1);
			
			$y_point = 62;
			$pdf->SetXY( 57, $y_point );
			$pdf->MultiCell(85, 5, U2T($data['prename_short'].$data['firstname_th']." ".$data['lastname_th']), $border, 1);
			$pdf->SetXY( 152, $y_point );
			$pdf->MultiCell(40, 5, U2T($data['member_id']), $border, 1);
			
			$y_point = 69;
			$pdf->SetXY( 55, $y_point );
			$pdf->MultiCell(40, 5, U2T($data['id_card']), $border, 1);
			$pdf->SetXY( 115, $y_point );
			$pdf->MultiCell(11, 5, U2T($this->center_function->cal_age($data['birthday'])), $border, 'C');
			$pdf->SetXY( 162, $y_point );
			$pdf->MultiCell(20, 5, U2T($data['c_address_no']), $border, 1);
			
			$y_point = 76;
			$pdf->SetXY( 23, $y_point );
			$pdf->MultiCell(10, 5, U2T($data['c_address_moo']), $border, 'C');
			$pdf->SetXY( 42, $y_point );
			$pdf->MultiCell(40, 5, U2T($data['district_name']), $border, 1);
			$pdf->SetXY( 93, $y_point );
			$pdf->MultiCell(40, 5, U2T($data['amphur_name']), $border, 1);
			$pdf->SetXY( 145, $y_point );
			$pdf->MultiCell(40, 5, U2T($data['province_name']), $border, 1);
			
			$y_point = 83;
			$pdf->SetXY( 37, $y_point );
			$pdf->MultiCell(25, 5, U2T($data['c_zipcode']), $border, 1);
			$pdf->SetXY( 113, $y_point );
			$pdf->MultiCell(80, 5, U2T($data['position']), $border, 1);
			
			$y_point = 90;
			$pdf->SetXY( 40, $y_point );
			$pdf->MultiCell(60, 5, U2T($data['mem_group_name']), $border, 1);
			
			$y_point = 97;
			$pdf->SetXY( 37, $y_point );
			$pdf->MultiCell(40, 5, U2T($data['tel']), $border, 1);
			$pdf->SetXY( 93, $y_point );
			$pdf->MultiCell(40, 5, U2T($data['mobile']), $border, 1);
			$pdf->SetXY( 140, $y_point );
			$pdf->MultiCell(40, 5, U2T($data['email']), $border, 1);
			
			$y_point = 112;
			$pdf->SetXY( 56, $y_point );
			$pdf->MultiCell(31, 5, U2T(num_format($data['loan_amount'])), $border, 'R');
			$pdf->SetXY( 95, $y_point );
			$pdf->MultiCell(73, 5, U2T($this->center_function->convert($data['loan_amount'])), $border, 'C');
			
			$y_point = 119;
			$pdf->SetXY( 42, $y_point );
			$pdf->MultiCell(31, 5, U2T($data['contract_number']), $border, 1);
			
			$y_point = 126;
			$pdf->SetXY( 96, $y_point );
			$pdf->MultiCell(31, 5, U2T(num_format($data['loan_amount'])), $border, 'R');
			$pdf->SetXY( 135, $y_point );
			$pdf->MultiCell(53, 5, U2T($this->center_function->convert($data['loan_amount'])), $border, 'C');
			
			$y_point = 190.5;
			$pdf->SetXY( 52, $y_point );
			$pdf->MultiCell(20, 5, U2T($data['period_amount']), $border, 'R');
			
			$y_point = 219;
			$pdf->SetXY( 103, $y_point );
			$pdf->MultiCell(30, 5, U2T($data_period_1['principal_payment']), $border, 'R');
			
			$y_point = 265;
			$pdf->SetXY( 26, $y_point );
			$pdf->MultiCell(67, 5, U2T($data['firstname_th']." ".$data['lastname_th']), $border, 'C');
			
		}
	}
	//exit;
	$pdf->Output();