<?php
function U2T($text) { return @iconv("UTF-8", "TIS-620//IGNORE", ($text)); }
function num_format($text) {
    if($text!=''){
        return number_format($text,2);
    }else{
        return '';
    }
}

	$filename = $_SERVER["DOCUMENT_ROOT"].PROJECTPATH."/assets/document/petition_normal_borrower_pdf.pdf" ;
	//echo $filename;exit;

	$pdf = new FPDI();

	$pageCount_1 = $pdf->setSourceFile($filename);
	for ($pageNo = 1; $pageNo <= $pageCount_1; $pageNo++) {
	if($_GET['grid'] == 'on'){
		$pdf->grid = true;
	}
	$pdf->AddPage();
		$tplIdx = $pdf->importPage($pageNo); 
		$pdf->useTemplate($tplIdx, 0, 0, 0, 0, true);
		
		$pdf->AddFont('THSarabunNew', '', 'THSarabunNew.php');
		$pdf->SetFont('THSarabunNew', '', 13 );

		if($_GET['grid'] == 'on'){
			$border = 1;
		}else{
			$border = 0;
		}

		$pdf->SetTextColor(0, 0, 0);
		$pdf->SetAutoPageBreak(true,0);

		$write_at = "";
		$borrower_rank = "--- ตำแหน่ง ---";
		$borrower_address_no = "000/00";
		$borrower_address_moo = "00";
		$borrower_address_soi = "00";
		$borrower_address_road = "-- ถนน --";
		$borrower_district = "-- ตำบล --";
		$borrower_amphur = "-- อำเภอ --";
		$borrower_province = "-- จังหวัด --";
		$borrower_zipcode = "-- รหัสไปรษณี --";
		$borrower_mobile  = "000-0000000";
		$borrower_work_tel  = "000-0000000";//เบอร์ที่ทำงาน
		$witness_name  = "--- ชื่อพยาน ---";//ชื่อพยาน
		$borrower_spouse_name = "-- ชื่อคู่สมรสของผู้กู้ --";//ชื่อคู่สมรสของผู้กู้
		$percent_y ="ร้อยละ";
		$installment_m ="ค่างวด";
		$installment_m_th ="ค่างวดภาษาเขียน";
		$lastinstallment_m ="ค่างวดสุดท้าย";
		$lastinstallment_m_th ="ค่างวดสุดท้ายภาษาเขียน";
		$borrow_because ="เหตุผลในการกู้";
		$amount_m ="จำนวนงวด";
		$begin_pay ="เดือนที่เริ่มจ่าย";
		$share_amount ="จำนวนหุ้น";
		$share_amount_th ="จำนวนหุ้นภาษาเขียน";

		$pay_type = array('cash'=>'เงินสด', 'cheque'=>'เช็คธนาคาร', 'transfer'=>'เงินโอน');
		if($pageNo == '1'){
			$y_point = 12.5;
			$pdf->SetXY( 165, $y_point );
			$pdf->MultiCell(20, 5, U2T(substr($data['contract_number'], 0, 4)), $border, 'C');
			$pdf->SetXY( 185, $y_point );
			$pdf->MultiCell(15, 5, U2T(substr($data['contract_number'], 4, 6)), $border, 'C');
			
			$y_point = 18.5;
			$pdf->SetXY( 140, $y_point );
			$pdf->MultiCell(60, 5, U2T($this->center_function->ConvertToThaiDate($data['createdatetime'],0,0)), $border, 'C');
			
			$y_point = 53;
			$pdf->SetXY( 150, $y_point );
			$pdf->MultiCell(50, 5, U2T($write_at), $border, 'C');
			
			$y_point = 59.5;
			$pdf->SetXY( 125, $y_point );
			$pdf->MultiCell(55, 5, U2T($this->center_function->ConvertToThaiDate($data['createdatetime'],0,0)), $border, 'C');
			
			$y_point = 65.6;
			$pdf->SetXY( 40, $y_point );
			$pdf->MultiCell(58, 5, U2T($data['prename_short'].$data['firstname_th']." ".$data['lastname_th']), $border, 1);
			// $pdf->SetXY( 140, $y_point );
			// $pdf->MultiCell(40, 5, U2T($data['member_id']), $border, 1);

			$y_point = 75;
			$pdf->SetXY( 45, $y_point );
			for($i = 0; $i <= 4; $i++) {
				if($i > 0){
					$pdf->SetXY( 45+($i*4.9), $y_point );
				}
				$pdf->MultiCell(3.8, 3.8, U2T($i), $border, 'C');
			}

			$pdf->SetXY( 84, $y_point );
			for($i = 0; $i <= 6; $i++) {
				if($i > 0){
					$pdf->SetXY( 84+($i*4.9), $y_point );
				}
				$pdf->MultiCell(3.8, 3.8, U2T($i), $border, 'C');
			}

			$pdf->SetXY( 157, $y_point );
			for($i = 0; $i <= 7; $i++) {
				if($i > 0){
					$pdf->SetXY( 157+($i*4.9), $y_point );
				}
				$pdf->MultiCell(3.8, 3.8, U2T($i), $border, 'C');
			}

			$y_point = 83;
			$pdf->SetXY( 18, $y_point );
			for($i = 0; $i <= 4; $i++) {
				if($i > 0){
					$pdf->SetXY( 18+($i*4.9), $y_point );
				}
				$pdf->MultiCell(3.8, 3.8, U2T($i), $border, 'C');
			}
			
			$y_point = 82;
			// $pdf->SetXY( 51, $y_point );
			// $pdf->MultiCell(40, 5, U2T($data['id_card']), $border, 1);
			$pdf->SetXY( 47, $y_point );
			$pdf->MultiCell(11, 5, U2T($this->center_function->cal_age($data['birthday'])), $border, 'C');
			// $pdf->SetXY( 158, $y_point );
			// $pdf->MultiCell(20, 5, U2T($data['c_address_no']), $border, 1);			
		
			
			$y_point = 90;
			$pdf->SetXY( 100, $y_point );
			$pdf->MultiCell(60, 5, U2T($borrower_rank), $border, 1);
			$pdf->SetXY( 135, $y_point );
			$pdf->MultiCell(60, 5, U2T($data['position']), $border, 1);
			$pdf->SetXY( 175, $y_point );

			$data['tel'] = str_replace('-', '', $data['tel']);
			$dmp_tel = explode(' ',$data['tel']);
			$data['tel'] = $dmp_tel[0];
			$pdf->MultiCell(30, 5, U2T($data['tel']), $border, 1);

	
		
			
			//ADDRESS!!!! (No data)
			$y_point = 96.5;
			$pdf->SetXY( 30, $y_point );
			$pdf->MultiCell(35, 5, U2T(num_format($data['salary'])), $border, 'R');			
			$pdf->SetXY( 104.5, $y_point );
			$pdf->MultiCell(15, 5, U2T($borrower_address_no), $border, 1);
			$pdf->SetXY( 121, $y_point );
			$pdf->MultiCell(38, 5, U2T($borrower_address_moo), $border, 1);
			$pdf->SetXY(140, $y_point );
			$pdf->MultiCell(38, 5, U2T($borrower_address_soi), $border, 1);
			$pdf->SetXY( 170, $y_point );
			$pdf->MultiCell(38, 5, U2T($borrower_address_road), $border, 1);
			
			$y_point = 103.5;
			$pdf->SetXY( 35, $y_point );
			$pdf->MultiCell(38, 5, U2T($borrower_district), $border, 1);
			$pdf->SetXY( 85, $y_point );
			$pdf->MultiCell(38, 5, U2T($borrower_amphur), $border, 1);
			$pdf->SetXY( 135, $y_point );
			$pdf->MultiCell(38, 5, U2T($borrower_province), $border, 1);
			$pdf->SetXY( 180, $y_point );
			$pdf->MultiCell(38, 5, U2T($borrower_zipcode), $border, 1);
	
	
			$y_point = 110;
			$pdf->SetXY( 36, $y_point );
			$pdf->MultiCell(30, 5, U2T($data['tel']), $border, 1);
			$pdf->SetXY( 85, $y_point );
			$pdf->MultiCell(30, 5, U2T($borrower_work_tel), $border, 1);
			$pdf->SetXY(160, $y_point );
			$pdf->MultiCell(30, 5, U2T($borrower_mobile), $border, 1);
			
			
			$y_point = 130.5;
			$pdf->SetXY( 76, $y_point );
			$pdf->MultiCell(31, 5, U2T(num_format($data['loan_amount'])), $border, 'R');
			$pdf->SetXY( 115, $y_point );
			$pdf->MultiCell(76, 5, U2T($this->center_function->convert($data['loan_amount'])), $border, 'C');
			
			$y_point = 134;
			$pdf->SetXY( 62, $y_point );
			$pdf->MultiCell(120, 5, U2T($data['loan_reason']), $border, 1);


			$y_point = 181.5;
			$pdf->SetXY( 150, $y_point );
			$pdf->MultiCell(120, 5, U2T($installment_m), $border, 1);

			$y_point = 188.5;
			$pdf->SetXY( 30, $y_point );
			$pdf->MultiCell(120, 5, U2T($installment_m_th), $border, 1);
			$pdf->SetXY( 115, $y_point );
			$pdf->MultiCell(120, 5, U2T($lastinstallment_m), $border, 1);
			$pdf->SetXY( 150, $y_point );
			$pdf->MultiCell(120, 5, U2T($lastinstallment_m_th), $border, 1);

			$y_point = 195;
			$pdf->SetXY( 60, $y_point );
			$pdf->MultiCell(120, 5, U2T($percent_y), $border, 1);
			$pdf->SetXY( 85, $y_point );
			$pdf->MultiCell(120, 5, U2T($amount_m), $border, 1);
			$pdf->SetXY( 145, $y_point );
			$pdf->MultiCell(120, 5, U2T($begin_pay), $border, 1);

			$y_point = 221.5;
			$pdf->SetXY( 120, $y_point );
			$pdf->MultiCell(120, 5, U2T($share_amount), $border, 1);
			$pdf->SetXY( 150, $y_point );
			$pdf->MultiCell(120, 5, U2T($share_amount_th), $border, 1);
		}else if($pageNo == '2'){
		
		}else if($pageNo == '3'){
		
		
			
			$y_point = 81;
			$pdf->SetXY( 45, $y_point );
			$pdf->MultiCell(85, 5, U2T($data['prename_short'].$data['firstname_th']." ".$data['lastname_th']), $border, 1);
			$pdf->SetXY( 140, $y_point );
			$pdf->MultiCell(40, 5, U2T($witness_name,0,0), $border, 1);
			
			$y_point = 123;
			$pdf->SetXY( 35, $y_point );
			$pdf->MultiCell(40, 5, U2T($borrower_spouse_name,0,0), $border, 1);

			$y_point = 277.5;
			$pdf->SetXY( 50, $y_point );
			$pdf->MultiCell(40, 5, U2T($data['prename_short'].$data['firstname_th']." ".$data['lastname_th'],0,0), $border, 1);
			 
		}
		
		

		
	}
		
		$filename = $_SERVER["DOCUMENT_ROOT"].PROJECTPATH."/assets/document/petition_normal_guarantee_pdf.pdf" ;
		$pageCount_1 = $pdf->setSourceFile($filename);
		
		$guaranteedata = [
			array(
				"age" => "60",
				"full_name" => "นางอาวรณ์ สุวรรณขจร",
				"member_id" => "000424",
				"salary" => "21,010.00",
				"user_code" => "3-4605-00390-05-4",
				"rank" => "พนักงาน",
				"department"=>"พนักงานรายงานโรค",
				"divistion" => "-- กอง --",
				"faction" => "-- ฝ่าย --",
				"tel" => "057441523",
				"mobile" => "08-9570-4587",
				"address_no" => "000/000",
				"address_moo" => "00",
				"address_soi" => "00",
				"address_road" => "--ชื่อถนน --",
				"district_name" => "-- ตำบล --",
				"amphur_name" => "-- อำเภอ --",
				"province_name" => "-- จังหวัด --",
				"email" => "coopdata@coop.co.th",
				"guarantee_member_agey" => "00",
				"guarantee_member_agem" => "00",
				"spouse_name" => "----- ชื่อคู่สมรส -----",
				"under" => "----- สังกัด ------",
				"zipcode" => "00000"
			)];
			foreach ($guaranteedata as $key => $row) {
				$guarantee_name = $row['full_name'];
				$guarantee_id_card =  $row['user_code'];
				$guarantee_id =  $row['member_id'];
				$guarantee_salary =   $row['salary'];
				$guarantee_rank =  $row['rank'];
				$guarantee_department =  $row['department'];
				$guarantee_amphur =  $row['amphur_name'];
				$guarantee_district =  $row['district_name'];
				$guarantee_province =  $row['province_name'];
				$guarantee_tel =  $row['tel'];
				$guarantee_mobile =  $row['mobile'];
				$guarantee_address_village =  $row['c_address_village'];
				$guarantee_address_no =  $row['address_no'];
				$guarantee_address_moo =  $row['address_moo'];
				$guarantee_address_soi =  $row['address_soi'];
				$guarantee_address_road =  $row['address_road'];
				$guarantee_division =  $row['divistion'];
				$guarantee_faction =  $row['faction'];
				$guarantee_email =  $row['email'];
				$guarantee_member_agey =  $row['guarantee_member_agey'];
				$guarantee_member_agem =  $row['guarantee_member_agem'];
				$guarantee_spouse_name =  $row['spouse_name'];
				$guarantee_age =  $row['age'];
				$guarantee_under =  $row['under'];
				$guarantee_zipcode =  $row['zipcode'];


		//Page 4
		$pdf->AddPage();
		$tplIdx = $pdf->importPage(1); 
		$pdf->useTemplate($tplIdx, 0, 0, 0, 0, true);
	
		$pdf->AddFont('THSarabunNew', '', 'THSarabunNew.php');
		$pdf->SetFont('THSarabunNew', '', 13 );
		
		$border = 0;
		$pdf->SetTextColor(0, 0, 0);
		$pdf->SetAutoPageBreak(true,0);
		
		$y_point = 71.5;
		$pdf->SetXY( 50, $y_point );
		$pdf->MultiCell(40, 5, U2T($guarantee_name,0,0), $border, 1);

		$y_point = 87.5;
		$pdf->SetXY( 25, $y_point );
		$pdf->MultiCell(40, 5, U2T($guarantee_age,0,0), $border, 1);
			
		$y_point = 95.5;
		$pdf->SetXY( 110, $y_point );
		$pdf->MultiCell(40, 5, U2T($guarantee_rank,0,0), $border, 1);	
		$pdf->SetXY(160, $y_point );
		$pdf->MultiCell(40, 5, U2T($guarantee_under,0,0), $border, 1);
		
		$y_point =102.3;
		$pdf->SetXY( 35, $y_point );
		$pdf->MultiCell(40, 5, U2T($guarantee_tel,0,0), $border, 1);
		$pdf->SetXY(102, $y_point );
		$pdf->MultiCell(40, 5, U2T($guarantee_salary,0,0), $border, 1);
		$pdf->SetXY(163, $y_point );
		$pdf->MultiCell(40, 5, U2T($guarantee_address_no,0,0), $border, 1);
		$pdf->SetXY(193, $y_point );
		$pdf->MultiCell(40, 5, U2T($guarantee_address_moo,0,0), $border, 1);
		
		$y_point =109;
		$pdf->SetXY( 30, $y_point );
		$pdf->MultiCell(40, 5, U2T($guarantee_address_soi,0,0), $border, 1);
		$pdf->SetXY( 70, $y_point );
		$pdf->MultiCell(40, 5, U2T($guarantee_address_road,0,0), $border, 1);
		$pdf->SetXY( 115, $y_point );
		$pdf->MultiCell(40, 5, U2T($guarantee_district,0,0), $border, 1);
		$pdf->SetXY( 175, $y_point );
		$pdf->MultiCell(40, 5, U2T($guarantee_amphur,0,0), $border, 1);
		
		$y_point =115.7;
		$pdf->SetXY( 30, $y_point );
		$pdf->MultiCell(40, 5, U2T($guarantee_province,0,0), $border, 1);
		$pdf->SetXY(80, $y_point );
		$pdf->MultiCell(40, 5, U2T($guarantee_zipcode,0,0), $border, 1);
		$pdf->SetXY( 115, $y_point );
		$pdf->MultiCell(40, 5, U2T($guarantee_tel,0,0), $border, 1);

		$y_point =136;
		$pdf->SetXY(70, $y_point );
		$pdf->MultiCell(40, 5, U2T($data['prename_short'].$data['firstname_th']." ".$data['lastname_th'],0,0), $border, 1);
		
		$y_point = 143;
		$pdf->SetXY( 48, $y_point );
		$pdf->MultiCell(40, 5, U2T($data['contract_number']), $border, 1);
		$pdf->SetXY( 100, $y_point );
		$pdf->MultiCell(40, 5, U2T($this->center_function->ConvertToThaiDate($data['createdatetime'],0,0)), $border, 1);
		$pdf->SetXY( 155, $y_point );
		$pdf->MultiCell(31, 5, U2T(num_format($data['loan_amount'])), $border, 'R');

		$y_point = 150;	
		$pdf->SetXY( 10, $y_point );
		$pdf->MultiCell(76, 5, U2T($this->center_function->convert($data['loan_amount'])), $border, 'C');
		$pdf->SetXY( 180, $y_point );
		$pdf->MultiCell(40, 5, U2T($percent_y,0,0), $border, 1);	
		
		$y_point = 156;	
		$pdf->SetXY( 100, $y_point );
		$pdf->MultiCell(40, 5, U2T($installment_m,0,0), $border, 1);	
		$pdf->SetXY( 140, $y_point );
		$pdf->MultiCell(40, 5, U2T($installment_m_th,0,0), $border, 1);	

		$y_point = 163;	
		$pdf->SetXY( 40, $y_point );
		$pdf->MultiCell(40, 5, U2T($lastinstallment_m,0,0), $border, 1);	
		$pdf->SetXY( 80, $y_point );
		$pdf->MultiCell(40, 5, U2T($lastinstallment_m_th,0,0), $border, 1);
		$pdf->SetXY( 180, $y_point );
		$pdf->MultiCell(40, 5, U2T($amount_m,0,0), $border, 1);
		
		$y_point = 170;	
		$pdf->SetXY(50, $y_point );
		$pdf->MultiCell(40, 5, U2T($borrow_because,0,0), $border, 1);

		//Page 5
		$pdf->AddPage();
		$tplIdx = $pdf->importPage(2); 
		$pdf->useTemplate($tplIdx, 0, 0, 0, 0, true);
	
		$pdf->AddFont('THSarabunNew', '', 'THSarabunNew.php');
		$pdf->SetFont('THSarabunNew', '', 13 );
		
		$border = 0;
		$pdf->SetTextColor(0, 0, 0);
		$pdf->SetAutoPageBreak(true,0);
		
		$y_point =166;	
		$pdf->SetXY(50, $y_point );
		$pdf->MultiCell(40, 5, U2T($guarantee_name ,0,0), $border, 1);		
		$pdf->SetXY( 145, $y_point );
		$pdf->MultiCell(40, 5, U2T($witness_name,0,0), $border, 1);	
		
		$y_point =179.5;
		$pdf->SetXY( 145, $y_point );
		$pdf->MultiCell(40, 5, U2T($witness_name,0,0), $border, 1);	
		
		
		$y_point = 209;	
		$pdf->SetXY(33, $y_point );
		$pdf->MultiCell(40, 5, U2T($guarantee_spouse_name,0,0), $border, 1);		
		


		}
	
	
	
	//exit;
	$pdf->Output();
