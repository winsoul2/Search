<?php
function U2T($text) { return @iconv("UTF-8", "TIS-620//IGNORE", ($text)); }
function num_format($text) {
    if($text!=''){
        return number_format($text,2);
    }else{
        return '';
    }
}

	$filename = $_SERVER["DOCUMENT_ROOT"].PROJECTPATH."/assets/document/petition_emergent_pdf.pdf" ;
	//echo $filename;exit;
	$checkMark = $_SERVER["DOCUMENT_ROOT"] . PROJECTPATH . "/assets/images/check_mark.png";

	$pdf = new FPDI();
	
	$pageCount_1 = $pdf->setSourceFile($filename);
	for ($pageNo = 1; $pageNo < $pageCount_1; $pageNo++) {
	if($_GET['grid'] == 'on'){
		$pdf->grid = true;
		$border = 1;
	}else{
		$border = 0;
	}
	$pdf->AddPage();
		$tplIdx = $pdf->importPage($pageNo); 
		$pdf->useTemplate($tplIdx, 0, 0, 0, 0, true);
		
		$pdf->AddFont('THSarabunNew', '', 'THSarabunNew.php');
		$pdf->SetFont('THSarabunNew', '', 13 );
		

		$pdf->SetTextColor(0, 0, 0);
		$pdf->SetAutoPageBreak(true,0);

	$borrower_name = $data['prename_short'].$data['firstname_th']." ".$data['lastname_th'];
	$borrower_member_id = "รหัสสมาชิกของผู้กู้";
	$peper_number = "เลขที่หนังสือกู้";
	$borrower_tel = "เบอร์โทรศัพท์บ้าน";
	$borrower_mobile = "เบอร์โทรศัพท์มือถือของผู้กู้";
	$borrower_salary = $data['salary'];
	$borrower_age = "อายุของผู้กู้";
	$borrower_id_card = $data['id_card'];//เลขประจำตัวบัตรประชาชน
	$borrow_money = number_format($data['loan_amount'], 2);
	$borrow_moneyth = $this->center_function->convert($data['loan_amount']);
	$borrow_because = $data['loan_reason'];
	$borrower_district = $data['district_name'];
	$borrower_amphur = $data['amphur_name'];
	$borrower_province = $data['province_name'];
	$borrower_address_no = $data['address_no'];
	$borrower_address_village = $data['address_village'];
	$borrower_address_road = $data['address_road'];
	$borrower_address_soi = $data['address_soi'];
	$borrower_address_moo = $data['address_moo'];
	$borrower_rank = $data['position_name'];
	$borrower_group = "-";
	$borrower_pile ="-";
	$borrower_cotton = "-";
	$borrower_department = "-";
	$borrower_zipcode = $data['zipcode'];
	$borrower_birthday_day = date('j', strtotime($data['birthday']));
	$borrower_birthday_month = $month_arr[date('n', strtotime($data['birthday']))];
	$borrower_birthday_years = date('Y', strtotime($data['birthday']))+543;
	$borrower_period_balance = "4";
	$borrower_division = "-";
	$borrower_faction = "-";
	$borrower_member_agey = "00";//อายุของทะเบียนสมาชิก(ปี)
	$borrower_member_agem = "00";//อายุของทะเบียนสมาชิก(เดือน)
	$borrower_deposit_type = "ประเภทเงินฝาก";//ประเภทเงินฝาก 
	$borrower_bank_acc_num = "-";//เลขบัญชีธนาคาร
	$borrower_other_income =  "-";//รายได้จากที่อื่น
	$borrower_email = "-";//อีเมล์
	$borrower_baby = "";//จำนวนบุตร
	$borrower_spouse = $data['marry_name'];
	$borrower_spouse_age = "";
	$borrower_career = "";
	$borrower_rate_salary = $data['salary'];
	$percen_y = $data['interest_per_year'];//ดอกเบี้ยร้อยละ
	$amount_m = number_format($data['period_amount']);//จำนวนงวด
	$installment_m = number_format($data['money_period_1'], 2);//ค่างวดรายเดือน
	$installment_m_th = $this->center_function->convert($data['money_period_1']);
	$lastinstallment_m ="";
	$lastinstallment_m_th ="";
	$begin_pay = $month_arr[date('n', strtotime($data['date_start_period']))];
	$begin_pay_year = date('Y', strtotime($data['date_start_period']." + 543 Year"));
	$agent_name = "";
	$agent_member_id = "";
	$agent_position = "";
	$agent_under = "";
	$witness_name = "";//"---- ชื่อพยาน ----";
	$location = "สอ.สร.รฟท";
	$the_date = "วันที่";
	$month = "เดือน";
	$year = "ปี";
	$number_tsc = $data["employee_id"];
	$zipcode = $data['zipcode'];
	$signature = "";//"ลายเซ็นต์";
	$words_read_limit = "";//"คำอ่านวงเงิน";
	$explanation = "";//"ข้อชี้แจง";
	$signature_authorities = "";//"ลายเซ็นเจ้าหน้าที่";
	$president = "";//"ชื่อประธาน/รองฯ";

	$mem_tel_arr = preg_split('/([ก-๛]|\/+|(\s)+|\s\s+|\s{2,})+/', $data['tel']);
	$borrower_mobile = $data['mobile'] ? $data['mobile'] :  $mem_tel_arr[sizeof($mem_tel_arr)-1];
	$borrower_work_tel = $data['office_tel'] ? $data['office_tel'] : "-";
	$borrower_home_tel = $mem_tel_arr[0];

	//check phone number
	$phone = preg_replace("/(-|\s)+/", "", $data['tel']);
	$area_code = array('02', '03', '04', '05', '07'); //thai telephone number area code
	$tel_home = in_array(substr($phone, 0, 2), $area_code) ?  $borrower_home_tel : " - ";

	// -------------------------------------------------------
		
		$pay_type = array('cash'=>'เงินสด', 'cheque'=>'เช็คธนาคาร', 'transfer'=>'เงินโอน');
		if($pageNo == '1'){
			$y_point = 6.2;
			$pdf->SetXY( 26, $y_point );
			$pdf->MultiCell(32.3, 5, U2T($location), $border, "C");

			$y_point = 12;
			$pdf->SetXY( 26, $y_point );
			$pdf->MultiCell(32.3, 5, U2T($this->center_function->ConvertToThaiDate($data['createdatetime'],0,0)), $border, "C");

			$y_point = 19.3;
			$pdf->SetXY( 168.5, $y_point );
			$pdf->MultiCell(26.5, 5, U2T($data['contract_number']), $border, 1);
			$y_point = 26.2;
			$pdf->SetXY( 156.5, $y_point );
			$pdf->MultiCell(38.5, 5, U2T($this->center_function->ConvertToThaiDate( $data['date_transfer'],0,0)), $border, 1);
			
			$y_point = 57;
			$pdf->SetXY( 152.3, $y_point );
			$pdf->MultiCell(45, 5, U2T($location), $border, "C");
//            $border = 1;
			$y_point = 63.8;
			$pdf->SetXY( 138.3, $y_point );
			$pdf->MultiCell(7.8, 5, U2T(date('j',strtotime( $data['createdatetime']))), $border, "C");
			$pdf->SetXY( 154, $y_point );
			$pdf->MultiCell(25, 5, U2T($month_arr[date('n',strtotime( $data['createdatetime']))]), $border, "C");
			$pdf->SetXY( 185, $y_point );
			$pdf->MultiCell(12, 5, U2T(date('Y',strtotime( $data['createdatetime']." + 543 Year"))), $border, "C");
			
			$y_point = 84.6;
			$pdf->SetXY( 38, $y_point );
			$pdf->MultiCell(69.5, 5, U2T($borrower_name), $border, 1);
			$pdf->SetXY( 137, $y_point );
			$pdf->MultiCell(21, 5, U2T($data['member_id']), $border, "C");
			
			$y_point = 91.3;
			$pdf->SetXY( 15, $y_point );
			$pdf->MultiCell(50.7, 5, U2T($borrower_id_card), $border, "C");
			$pdf->SetXY( 76, $y_point );
			$pdf->MultiCell(7, 5, U2T($this->center_function->cal_age_with_target($data['birthday'], $data['createdatetime'])), $border, 'C');
			$pdf->SetXY( 100, $y_point );
			$pdf->MultiCell(6, 5, U2T($borrower_birthday_day), $border, "C");
			$pdf->SetXY( 114.6, $y_point );
			$pdf->MultiCell(23.5, 5, U2T($borrower_birthday_month), $border, "C");
			$pdf->SetXY( 144.3, $y_point );
			$pdf->MultiCell(13.5, 5, U2T($borrower_birthday_years), $border, "C");
			$pdf->SetXY( 174.6, $y_point );
			$pdf->MultiCell(22.8, 5, U2T($number_tsc), $border, 1);

			$y_point = 97.9;
			$pdf->SetXY( 34.1, $y_point );
			$pdf->MultiCell(22.8, 5, U2T(num_format($data['salary'])), $border, 'R');
			$pdf->SetXY( 76.1, $y_point );
			$pdf->MultiCell(40, 5, U2T($borrower_rank), $border, "C");
			$pdf->SetXY( 125, $y_point );
			$pdf->MultiCell(35, 5, U2T($borrower_group), $border, "C");
			$pdf->SetXY( 169, $y_point );
			$pdf->MultiCell(28, 5, U2T($borrower_department), $border, "C");
			
			$y_point = 104.5;
			$pdf->SetXY( 21, $y_point );
			$pdf->MultiCell(48, 5, U2T($borrower_pile), $border, "C");
			$pdf->SetXY( 75, $y_point );
			$pdf->MultiCell(48, 5, U2T($data['faction_name']), $border, "C");
			$pdf->SetXY( 147, $y_point );
			$pdf->MultiCell(50, 5, U2T($borrower_mobile), $border, "C");
			

			$y_point = 118;
			$pdf->SetXY( 41, $y_point );
			$pdf->MultiCell(13, 5, U2T($data['c_address_no']), $border, 1);
			$pdf->SetXY( 59, $y_point );
			$pdf->MultiCell(6, 5, U2T($data['c_address_moo']), $border, 1);
			$pdf->SetXY( 80, $y_point );
			$pdf->MultiCell(32, 5, U2T($data['c_address_soi']), $border, 1);
			$pdf->SetXY( 119, $y_point );
			$pdf->MultiCell(35, 5, U2T($data['c_address_road']), $border, 1);
			$pdf->SetXY( 170, $y_point );
			$pdf->MultiCell(27.1, 5, U2T($data['c_district_name']), $border, 1);

			$y_point = 124.7;
			$pdf->SetXY( 29, $y_point );
			$pdf->MultiCell(32, 5, U2T($data['c_amphur_name']), $border, 1);
			$pdf->SetXY( 71.5, $y_point );
			$pdf->MultiCell(35.5, 5, U2T($data['c_province_name']), $border, 1);
			$pdf->SetXY( 161, $y_point );
			$pdf->MultiCell(36, 5, U2T($borrower_mobile), $border, 1);

			$y_point = 126;
			$pdf->SetXY( 126.8, $y_point );
			$pdf->MultiCell(3.7, 3.7, U2T(substr($zipcode, 0,1)), $border, 1);
			$pdf->SetXY( 130.5, $y_point );
			$pdf->MultiCell(3.7, 3.7, U2T(substr($zipcode, 1,1)), $border, 1);
			$pdf->SetXY( 134.8, $y_point );
			$pdf->MultiCell(3.7, 3.7, U2T(substr($zipcode, 2,1)), $border, 1);
			$pdf->SetXY( 139, $y_point );
			$pdf->MultiCell(3.7, 3.7, U2T(substr($zipcode, 3,1)), $border, 1);
			$pdf->SetXY( 143, $y_point );
			$pdf->MultiCell(3.7, 3.7, U2T(substr($zipcode, 4,1)), $border, 1);

			$y_point = 138.3;
			$pdf->SetXY( 41, $y_point );
			$pdf->MultiCell(13, 5, U2T($borrower_address_no), $border, 1);
			$pdf->SetXY( 59, $y_point );
			$pdf->MultiCell(6, 5, U2T($borrower_address_moo), $border, 1);
			$pdf->SetXY( 80, $y_point );
			$pdf->MultiCell(32, 5, U2T($borrower_address_soi), $border, 1);
			$pdf->SetXY( 119, $y_point );
			$pdf->MultiCell(35, 5, U2T($borrower_address_road), $border, 1);
			$pdf->SetXY( 170, $y_point );
			$pdf->MultiCell(27.1, 5, U2T($borrower_district), $border, 1);
			
			$y_point = 145;
			$pdf->SetXY( 29.5, $y_point );
			$pdf->MultiCell(28, 5, U2T($borrower_amphur), $border, 1);
			$pdf->SetXY( 68, $y_point );
			$pdf->MultiCell(33, 5, U2T($borrower_province), $border, 1);
			$pdf->SetXY( 155, $y_point );
			$pdf->MultiCell(42, 5, U2T($borrower_mobile), $border, 1);

			$y_point = 146.3;
			$pdf->SetXY( 120, $y_point );
			$pdf->MultiCell(3.7, 3.7, U2T(substr($zipcode, 0,1)), $border, 1);
			$pdf->SetXY( 124, $y_point );
			$pdf->MultiCell(3.7, 3.7, U2T(substr($zipcode, 0,1)), $border, 1);
			$pdf->SetXY( 128.5, $y_point );
			$pdf->MultiCell(3.7, 3.7, U2T(substr($zipcode, 0,1)), $border, 1);
			$pdf->SetXY( 132.5, $y_point );
			$pdf->MultiCell(3.7, 3.7, U2T(substr($zipcode, 0,1)), $border, 1);
			$pdf->SetXY( 136.5, $y_point );
			$pdf->MultiCell(3.7, 3.7, U2T(substr($zipcode, 0,1)), $border, 1);
			
			$y_point = 151.9;
			$pdf->SetXY( 97.3, $y_point );
			$pdf->MultiCell(8, 5, U2T($borrower_baby), $border, 1);
			$pdf->SetXY( 125, $y_point );
			$pdf->MultiCell(54, 5, U2T($borrower_spouse), $border, 1);
			$pdf->SetXY( 185, $y_point );
			$pdf->MultiCell(10, 5, U2T($borrower_spouse_age), $border, 1);


			$y_point = 153.3;
			$member_status_pos = array( 42.3, 30.2, 55.9, 69.7);
			if ($data['marry_status'] > 0){
                $pdf->Image( $checkMark,$member_status_pos[$data['marry_status']-1], $y_point,  -280);
            }

			$y_point = 158.4;
			$pdf->SetXY( 23, $y_point );
			$pdf->MultiCell(31, 5, U2T($borrower_career), $border, 1);
			$pdf->SetXY( 73.9, $y_point );
			$pdf->MultiCell(21, 5, U2T(""), $border, 1);
			$pdf->SetXY( 170, $y_point );
			$pdf->MultiCell(27.5, 5, U2T(""), $border, 1);
			

			$y_point = 179;
			$pdf->SetXY( 105, $y_point );
			$pdf->MultiCell(28, 5, U2T(num_format($data['loan_amount'])), $border, 'C');
			$pdf->SetXY( 141.5, $y_point );
			$pdf->MultiCell(54.7, 5, U2T($this->center_function->convert($data['loan_amount'])), $border, 'C');
			
			$y_point = 185.5;
			$pdf->SetXY( 40, $y_point );
			$pdf->MultiCell(100, 5, U2T($borrow_because), $border, 1);
			
			$y_point = 199.3;
			$pdf->SetXY(90.5, $y_point );
			$pdf->MultiCell(17, 5, U2T($percen_y), $border, "C");
			
			$y_point = 212.8;
			$pdf->SetXY(148, $y_point );
			$pdf->MultiCell(7, 5, U2T($amount_m), $border, "C");
			$pdf->SetXY(171, $y_point );
			$pdf->MultiCell(19.5, 5, U2T($installment_m ), $border, 'C');

			$y_point = 219.5;
			$pdf->SetXY(16, $y_point );
			$pdf->MultiCell(50, 5, U2T($installment_m_th), $border, 'C');
			$pdf->SetXY(102 ,$y_point );
			$pdf->MultiCell(24.5, 5, U2T($lastinstallment_m), $border, 'C');
			$pdf->SetXY(135,$y_point );
			$pdf->MultiCell(61, 5, U2T($lastinstallment_m_th), $border, 'C');
			
			$y_point = 226;
			$pdf->SetXY(52, $y_point );
			$pdf->MultiCell(33, 5, U2T($begin_pay), $border, 'C');
			$pdf->SetXY(91, $y_point );
			$pdf->MultiCell(14, 5, U2T($begin_pay_year), $border, 'C');
			
		}else if($pageNo == '2'){
			$y_point = 62.8;
			$pdf->SetXY( 36, $y_point );
			$pdf->MultiCell(43, 5, U2T($signature), $border, 'C');
			$pdf->SetXY( 125, $y_point );
			$pdf->MultiCell(43, 5, U2T($signature), $border, 'C');

			$y_point = 69.3;
			$pdf->SetXY( 34, $y_point );
			$pdf->MultiCell(46, 5, U2T($data['firstname_th']." ".$data['lastname_th']), $border, 'C');
			$pdf->SetXY( 123, $y_point );
			$pdf->MultiCell(46, 5, U2T($data['firstname_th']." ".$data['lastname_th']), $border, 'C');
			
			$y_point = 76.3;
			$pdf->SetXY( 36, $y_point );
			$pdf->MultiCell(43, 5, U2T($signature), $border, 'C');
			$pdf->SetXY( 125, $y_point );
			$pdf->MultiCell(43, 5, U2T($signature), $border, 'C');

			$y_point = 82.8;
			$pdf->SetXY( 34, $y_point );
			$pdf->MultiCell(46, 5, U2T($witness_name), $border, 'C');
			$pdf->SetXY( 123, $y_point );
			$pdf->MultiCell(46, 5, U2T($witness_name), $border, 'C');

			$y_point = 93.4;
			$pdf->SetXY( 139.3, $y_point );
			$pdf->MultiCell(58, 5, U2T($agent_name), $border, 1);

			$y_point = 100;
			$pdf->SetXY( 44.2, $y_point );
			$pdf->MultiCell(22, 5, U2T($agent_member_id), $border, 1);
			$pdf->SetXY( 80.5, $y_point );
			$pdf->MultiCell(49, 5, U2T($agent_position), $border, 1);
			$pdf->SetXY( 138.5, $y_point );
			$pdf->MultiCell(59, 5, U2T($agent_under), $border, 1);

			$y_point = 122.8;
			$pdf->SetXY( 36, $y_point );
			$pdf->MultiCell(45, 5, U2T($signature), $border, 'C');
			$pdf->SetXY( 126, $y_point );
			$pdf->MultiCell(44, 5, U2T($signature), $border, 'C');

			$y_point = 129.5;
			$pdf->SetXY( 34, $y_point );
			$pdf->MultiCell(47, 5, U2T($borrower_name), $border, 'C');
			$pdf->SetXY( 122, $y_point );
			$pdf->MultiCell(47, 5, U2T($agent_name), $border, 'C');

			$y_point = 136.4;
			$pdf->SetXY( 36, $y_point );
			$pdf->MultiCell(45, 5, U2T($signature), $border, 'C');
			$pdf->SetXY( 126, $y_point );
			$pdf->MultiCell(44, 5, U2T($signature), $border, 'C');

			$y_point = 143;
			$pdf->SetXY( 34, $y_point );
			$pdf->MultiCell(46, 5, U2T($witness_name), $border, 'C');
			$pdf->SetXY( 123, $y_point );
			$pdf->MultiCell(46, 5, U2T($witness_name), $border, 'C');
			$y_point = 152;
			$pdf->SetXY( 38, $y_point );
			$pdf->MultiCell(75, 5, U2T($borrower_name), $border, 1);
			$pdf->SetXY( 135, $y_point );
			$pdf->MultiCell(56, 5, U2T(num_format($data['loan_amount'])), $border, 'C');

			$y_point = 158.5;
			$pdf->SetXY( 17, $y_point );
			$pdf->MultiCell(92, 5, U2T($this->center_function->convert($data['loan_amount'])), $border, 1);
			$pdf->SetXY( 150, $y_point );
			$pdf->MultiCell(47, 5, U2T($this->center_function->ConvertToThaiDate($data['createdatetime'],0,0)), $border, 1);

			$y_point = 171;
			$pdf->SetXY( 127, $y_point );
			$pdf->MultiCell(43, 5, U2T($signature), $border, 'C');

			$y_point = 177;
			$pdf->SetXY( 123, $y_point );
			$pdf->MultiCell(46, 5, U2T($borrower_name), $border, 'C');

			$y_point = 189.8;
			$pdf->SetXY( 126, $y_point );
			$pdf->MultiCell(45, 5, U2T($signature), $border, 'C');

			$y_point = 196.5;
			$pdf->SetXY( 125, $y_point );
			$pdf->MultiCell(49, 5, U2T($date), $border, 'C');

			$y_point = 218.8;
			$pdf->SetXY( 58, $y_point );
			$pdf->MultiCell(34, 5, U2T($borrow_money), $border, 'C');
			$pdf->SetXY( 101, $y_point );
			$pdf->MultiCell(68, 5, U2T($borrow_moneyth), $border, 'C');

			$y_point = 238.5;
			$pdf->SetXY( 16, $y_point );
			$pdf->MultiCell(24, 5, U2T(number_format($borrower_salary)), $border, 'C');
			$pdf->SetXY( 41, $y_point );
			$pdf->MultiCell(29, 5, U2T(number_format(@$share_collect_value['share_collect'])), $border, 'C');
			$pdf->SetXY( 71, $y_point );
			$pdf->MultiCell(31, 5, U2T(number_format($loan['8']['loan_balance'], 2)), $border, 'C');
			$pdf->SetXY( 104, $y_point );
			$pdf->MultiCell(40.3, 5, U2T(number_format($loan['7']['loan_balance'], 2)), $border, 'C');
			$pdf->SetXY( 146.5, $y_point );
			$pdf->MultiCell(20.5, 5, U2T($date), $border, 'C');
			$pdf->SetXY( 169, $y_point );
			$pdf->MultiCell(27, 5, U2T($date), $border, 'C');

			$y_point = 263;
			$pdf->SetXY( 50, $y_point );
			$pdf->MultiCell(137, 5, U2T($explanation), $border, 1);

			$y_point = 269.5;
			$pdf->SetXY( 117, $y_point );
			$pdf->MultiCell(60, 5, U2T($signature_authorities), $border, 'C');

			$y_point = 275.5;
			$pdf->SetXY( 117, $y_point );
			$pdf->MultiCell(60, 5, U2T($signature_authorities), $border, 'C');

			$y_point = 258;
			$pdf->SetXY( 53.8, $y_point );
			$pdf->MultiCell(4, 3.9, U2T(""), $border, 1);
			$pdf->SetXY( 92, $y_point );
			$pdf->MultiCell(4, 3.9, U2T(""), $border, 1);

			$y_point = 270.2;
			$pdf->SetXY( 53.8, $y_point );
			$pdf->MultiCell(4, 3.9, U2T(""), $border, 1);
			$pdf->SetXY( 92, $y_point );
			$pdf->MultiCell(4, 3.9, U2T(""), $border, 1);

			$y_point = 276.4;
			$pdf->SetXY( 53.8, $y_point );
			$pdf->MultiCell(4, 3.9, U2T(""), $border, 1);
			$pdf->SetXY( 92, $y_point );
			$pdf->MultiCell(4, 3.9, U2T(""), $border, 1);
		}
	}
	//exit;
	$pdf->Output();
