<?php
function U2T($text) { return @iconv("UTF-8", "TIS-620//IGNORE", ($text)); }
function num_format($text) {
    if($text!=''){
        return number_format($text,2);
    }else{
        return '';
    }
}

	$filename = $_SERVER["DOCUMENT_ROOT"].PROJECTPATH."/assets/document/petition_emergent_atm_pdf.pdf" ;
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
		
		$pay_type = array('cash'=>'เงินสด', 'cheque'=>'เช็คธนาคาร', 'transfer'=>'เงินโอน');
		if($pageNo == '1'){
			$y_point = 19;
			$pdf->SetXY( 158, $y_point );
			$pdf->MultiCell(40, 5, U2T(@$data['contract_number']), $border, 1);
			
			$y_point = 26;
			$pdf->SetXY( 148, $y_point );
			$pdf->MultiCell(40, 5, U2T($this->center_function->ConvertToThaiDate(@$data['createdatetime'],0,0)), $border, 1);
			
			$y_point = 53;
			$pdf->SetXY( 133, $y_point );
			$pdf->MultiCell(40, 5, U2T(''), $border, 1);
			
			$y_point = 60;
			$pdf->SetXY( 130, $y_point );
			$pdf->MultiCell(40, 5, U2T($this->center_function->ConvertToThaiDate(@$data['createdatetime'],0,0)), $border, 1);
			
			$y_point = 70;
			$pdf->SetXY( 43, $y_point );
			$pdf->MultiCell(85, 5, U2T(@$data['prename_short'].@$data['firstname_th']." ".@$data['lastname_th']), $border, 1);
			
			$y_point = 76;
			$pdf->SetXY( 39, $y_point );
			$pdf->MultiCell(40, 5, U2T(@$data['member_id']), $border, 1);
			$pdf->SetXY( 107, $y_point );
			$pdf->MultiCell(40, 5, U2T(@$data['id_card']), $border, 1);
			$pdf->SetXY( 178, $y_point );
			$pdf->MultiCell(11, 5, U2T($this->center_function->cal_age(@$data['birthday'])), $border, 'C');
			
			$y_point = 83;
			$pdf->SetXY( 74, $y_point );
			$pdf->MultiCell(52, 5, U2T(@$data['position']), $border, 1);
			$pdf->SetXY( 152, $y_point );
			$pdf->MultiCell(60, 5, U2T(@$data['mem_group_name']), $border, 1);
			
			$y_point = 90;
			$pdf->SetXY( 143, $y_point );
			$pdf->MultiCell(42, 5, U2T(num_format(@$data['salary'])), $border, 'R');
			
			$y_point = 97;
			$pdf->SetXY( 50, $y_point );
			$pdf->MultiCell(20, 5, U2T(@$data['c_address_no']), $border, 1);
			$pdf->SetXY( 80, $y_point );
			$pdf->MultiCell(12, 5, U2T(@$data['c_address_moo']), $border, 1);
			$pdf->SetXY( 100, $y_point );
			$pdf->MultiCell(43, 5, U2T(@$data['district_name']), $border, 1);
			$pdf->SetXY( 150, $y_point );
			$pdf->MultiCell(38, 5, U2T(@$data['amphur_name']), $border, 1);
			
			$y_point = 104;
			$pdf->SetXY( 30, $y_point );
			$pdf->MultiCell(38, 5, U2T(@$data['province_name']), $border, 1);
			$pdf->SetXY( 85, $y_point );
			$pdf->MultiCell(20, 5, U2T(@$data['c_zipcode']), $border, 1);
			$pdf->SetXY( 122, $y_point );
			$pdf->MultiCell(30, 5, U2T(@$data['tel']), $border, 1);
			$pdf->SetXY( 166, $y_point );
			$pdf->MultiCell(30, 5, U2T(@$data['mobile']), $border, 1);
			
			
			
			$y_point = 144;
			$pdf->SetXY( 77, $y_point );
			$pdf->MultiCell(44, 5, U2T(num_format(@$data['total_amount_approve'])), $border, 'R');
			$pdf->SetXY( 130, $y_point );
			$pdf->MultiCell(60, 5, U2T($this->center_function->convert(@$data['total_amount_approve'])), $border, 'C');
			
		}else if($pageNo == '2'){
			$y_point = 154;
			$pdf->SetXY( 120, $y_point );
			$pdf->MultiCell(51, 5, U2T($data['firstname_th']." ".$data['lastname_th']), $border, 'C');
		}
	}
	
	$filename_2 = $_SERVER["DOCUMENT_ROOT"].PROJECTPATH."/assets/document/petition_emergent_atm_pdf_2.pdf" ;
	$pageCount_2 = $pdf->setSourceFile($filename_2);
	for ($pageNo = 1; $pageNo <= $pageCount_2; $pageNo++) {	
	$pdf->AddPage();
		$tplIdx = $pdf->importPage($pageNo); 
		$pdf->useTemplate($tplIdx, 0, 0, 0, 0, true);
		
		$pdf->AddFont('THSarabunNew', '', 'THSarabunNew.php');
		$pdf->SetFont('THSarabunNew', '', 13 );
		
		$border = 0;
		$pdf->SetTextColor(0, 0, 0);
		$pdf->SetAutoPageBreak(true,0);
		
		$pay_type = array('cash'=>'เงินสด', 'cheque'=>'เช็คธนาคาร', 'transfer'=>'เงินโอน');
		if($pageNo == '1'){
			
			$y_point = 36;
			$pdf->SetXY( 115, $y_point );
			$pdf->MultiCell(40, 5, U2T($this->center_function->ConvertToThaiDate($data['createdatetime'],0,0)), $border, 1);
			
			$y_point = 48;
			$pdf->SetXY( 38, $y_point );
			$pdf->MultiCell(65, 5, U2T($data['prename_short'].$data['firstname_th']." ".$data['lastname_th']), $border, 1);
			$pdf->SetXY( 106, $y_point );
			$pdf->MultiCell(11, 5, U2T($this->center_function->cal_age($data['birthday'])), $border, 'C');
			$pdf->SetXY( 147, $y_point );
			$pdf->MultiCell(20, 5, U2T($data['c_address_no']), $border, 1);
			$pdf->SetXY( 176, $y_point );
			$pdf->MultiCell(12, 5, U2T($data['c_address_moo']), $border, 1);
			
			$y_point = 56;
			$pdf->SetXY( 137, $y_point );
			$pdf->MultiCell(43, 5, U2T($data['district_name']), $border, 1);
			
			$y_point = 64;
			$pdf->SetXY( 35, $y_point );
			$pdf->MultiCell(38, 5, U2T($data['amphur_name']), $border, 1);
			$pdf->SetXY( 77, $y_point );
			$pdf->MultiCell(38, 5, U2T($data['province_name']), $border, 1);
			$pdf->SetXY( 140, $y_point );
			$pdf->MultiCell(60, 5, U2T($data['mem_group_name']), $border, 1);
			
			$y_point = 71;
			$pdf->SetXY( 110, $y_point );
			$pdf->MultiCell(52, 5, U2T($data['position']), $border, 1);
			
			$y_point = 79;
			$pdf->SetXY( 103, $y_point );
			$pdf->MultiCell(40, 5, U2T($data['member_id']), $border, 1);
			
			$y_point = 248;
			$pdf->SetXY( 110, $y_point );
			$pdf->MultiCell(53, 5, U2T($data['firstname_th']." ".$data['lastname_th']), $border, 'C');
		}
	}
	//exit;
	$pdf->Output();