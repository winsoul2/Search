<?php
if($_GET['dev'] == "dev"){
	echo "<pre>"; print_r($data); exit;
}
function U2T($text)
{
	return @iconv("UTF-8", "TIS-620//IGNORE", ($text));
}

function num_format($text)
{
	if ($text != '') {
		return number_format($text, 2);
	} else {
		return '';
	}
}

$filename = $_SERVER["DOCUMENT_ROOT"] . PROJECTPATH . "/assets/document/petition_normal_education.pdf";
$checkMark = $_SERVER["DOCUMENT_ROOT"] . PROJECTPATH . "/assets/images/check_mark.png";
$dpi = -280;5;

$pdf = new FPDI();

$pageCount_1 = $pdf->setSourceFile($filename);
for ($pageNo = 1; $pageNo <= $pageCount_1; $pageNo++) {
	if ($_GET['grid'] == 'on') {
		$pdf->grid = true;
	}
	$pdf->AddPage();
	$tplIdx = $pdf->importPage($pageNo);
	$pdf->useTemplate($tplIdx, 0, 0, 0, 0, true);

	$pdf->AddFont('THSarabunNew', '', 'THSarabunNew.php');
	$pdf->SetFont('THSarabunNew', '', 13);

	if ($_GET['grid'] == 'on') {
		$border = 1;
	} else {
		$border = 0;
	}

	$pdf->SetTextColor(0, 0, 0);
	$pdf->SetAutoPageBreak(true, 0);

	$write_at = "สอ.สร.รฟท";
	$borrower_rank = $data['position'] ? $data['position'] : ""; //ตำแหน่ง
	$borrower_address_no = $data['address_no'] ? $data['address_no'] : " - ";
	$borrower_address_moo = $data['address_moo'] ? $data['address_moo'] : " - ";
	$borrower_address_soi = $data['address_soi'] ? $data['address_soi'] : " - ";
	$borrower_address_road = $data['address_road'] ? $data['address_road'] : " - ";
	$borrower_district = $data['district_name'] ? $data['district_name'] : " - ";
	$borrower_amphur = $data['amphur_name'] ? $data['amphur_name'] : " - ";
	$borrower_province = $data['province_name'] ? $data['province_name'] : " - ";
	$borrower_zipcode = $data['zipcode'] ? $data['zipcode'] : " - ";

	$mem_tel_arr = preg_split('/([ก-๛]|\/+|(\s)+|\s\s+|\s{2,})+/', $data['tel']);
//    $borrower_mobile = $data['mobile'] ? $data['mobile'] :  $mem_tel_arr[sizeof($mem_tel_arr)-1];
    $borrower_mobile = $data['mobile'] ? $data['mobile'] :  "-";
    $borrower_work_tel = $data['office_tel'] ? $data['office_tel'] : "-";
	$borrower_home_tel = $mem_tel_arr[0];
	$witness_name = "";//"--- ชื่อพยาน ---";//ชื่อพยาน
	$borrower_spouse_name = "-- ชื่อคู่สมรสของผู้กู้ --";//ชื่อคู่สมรสของผู้กู้
	$percent_y = number_format($data['interest_per_year'], 2);
	$installment_m = number_format($data['money_period_1'], 2);
	$installment_m_th = $this->center_function->convert($data['period_amount']);
	$lastinstallment_m = "";
	$lastinstallment_m_th = "";
	$borrow_because = $data['loan_reason'];
	$amount_m = number_format($data['period_amount']);
	$begin_pay = "เดือนที่เริ่มจ่าย";
	$share_amount = "จำนวนหุ้น";
	$share_amount_th = "จำนวนหุ้นภาษาเขียน";
	$pay_type = array('cash' => 'เงินสด', 'cheque' => 'เช็คธนาคาร', 'transfer' => 'เงินโอน');
	$other = "";
	$location = "สถานที่";
	$number = "";//"เลขที่";
	$bank = "";//ธนาคาร
	$bank_branch = "";//สาขา
	$bank_number = "";//เลขบัญชี
	$salary = "เงินเดือน";
	$signature = "";
	$witness_1 = "ชื่อ สอ.สร.รฟท";
	$reason = "";//เหตุผล
	$approval_name = "";//"--- ชื่ออนุมัติ ---";
	$name_authorities = "";//"ชื่อเจ้าหน้าที่";
	$guarantor_name = "";//"ชื่อผู้ค้ำประกัน";
	$contract_owner = $data['prename_short'] . $data['firstname_th'] . " " . $data['lastname_th'];
//    break;

	if ($pageNo == '1') {
	    $y = 2.2;
		$y_point = 7.9+2.2+1;
		$pdf->SetXY(17.6, $y_point);
		$pdf->MultiCell(2.4, 2.4, U2T(""), $border, 1);

		$y_point = 13.3+$y;
		$pdf->SetXY(22.7, $y_point);
		$pdf->MultiCell(2.4, 2.4, U2T(""), $border, 1);

		$y_point = 18.4+$y;
		$pdf->SetXY(22.8, $y_point);
		$pdf->MultiCell(2.4, 2.4, U2T(""), $border, 1);

		$y_point = 23.9+$y;
		$pdf->SetXY(17.6, $y_point);
		$pdf->MultiCell(2.4, 2.4, U2T(""), $border, 1);

		$y_point = 6.8+$y;
		$pdf->SetXY(140.5, $y_point);
		$pdf->MultiCell(25.5, 5, U2T($location), $border, 1);
		$pdf->SetXY(173.5, $y_point);
		$pdf->MultiCell(27.5, 5, U2T($this->center_function->ConvertToThaiDate($data['createdatetime'], 0, 0)), $border, 'C');

		$y_point = 11.3;
		$pdf->SetXY(52, $y_point+2.2);
		$pdf->MultiCell(32, 5, U2T($number), $border, 1);

		$y_point = 16.7+2.1;
		$pdf->SetXY(52, $y_point);
		$pdf->MultiCell(32, 5, U2T($number), $border, 1);

		$y_point = 21.7+2.2;
		$pdf->SetXY(54, $y_point);
		$pdf->MultiCell(29.5, 5, U2T($bank), $border, 1);

		$y_point = 27+2.1;
		$pdf->SetXY(23, $y_point);
		$pdf->MultiCell(20, 5, U2T($bank_branch), $border, 1);
		$pdf->SetXY(55, $y_point);
		$pdf->MultiCell(28.5, 5, U2T($bank_number), $border, 1);

		$contract_no = $data['contract_number'] ? (int)substr($data['contract_number'], 4, 6) : '';
		$contract_year = substr($data['contract_number'], 2, 2);
		$y_point = 13+2.1;
		$pdf->SetXY(165.5, $y_point);
		$pdf->MultiCell(17.5, 5, U2T($contract_no), $border, 'C');
		$pdf->SetXY(185, $y_point);
		$pdf->MultiCell(16, 5, U2T($contract_year), $border, 'C');


		$y_point = 19+2.3;
		$pdf->SetXY(140, $y_point);
		$pdf->MultiCell(61, 5, U2T($this->center_function->ConvertToThaiDate($data['loan_approve_date'], 0, 0)), $border, 'C');

		$y_point = 54+2.7;
		$pdf->SetXY(150, $y_point);
		$pdf->MultiCell(50, 5, U2T($write_at), $border, 'C');

		$y_point = 60+2.8;
		$pdf->SetXY(125, $y_point);
		$pdf->MultiCell(54, 5, U2T($this->center_function->ConvertToThaiDate($data['createdatetime'], 0, 0)), $border, 'C');

		$y_point = 66.5+2.9;
		$pdf->SetXY(40, $y_point);
		$pdf->MultiCell(58, 5, U2T($contract_owner), $border, 1);
		$y_point = 75+3;
		$pdf->SetXY(45, $y_point);

		$member_no = $data['member_id'];
		$pdf->SetFontSize(11);
		for ($i = 0; $i <= 4; $i++) {
			if ($i > 0) {
				$pdf->SetXY(45 + ($i * 4.9), $y_point);
			}
			$pdf->MultiCell(3.8, 3.8, U2T(substr($member_no, $i, 1)), $border, 'C');
		}

		$employee = $data['employee_id'];
		$pdf->SetXY(84, $y_point);
		for ($i = 0; $i <= 6; $i++) {
			if ($i > 0) {
				$pdf->SetXY(84 + ($i * 4.9), $y_point);
			}
			$pdf->MultiCell(3.8, 3.8, U2T(substr($employee, $i, 1)), $border, 'C');
		}

		$id_card = $data['id_card'];
		$pdf->SetXY(157, $y_point);
		for ($i = 0; $i <= 7; $i++) {
			if ($i > 0) {
				$pdf->SetXY(157 + ($i * 4.9), $y_point);
			}
			$pdf->MultiCell(3.8, 3.8, U2T(substr($id_card, $i, 1)), $border, 'C');
		}

		$y_point = 83+3;
		$pdf->SetXY(18, $y_point);
		for ($i = 0; $i <= 4; $i++) {
			if ($i > 0) {
				$pdf->SetXY(18 + ($i * 4.9), $y_point);
			}
			$pdf->MultiCell(3.8, 3.8, U2T(substr($id_card, $i, 1)), $border, 'C');
		}
		$pdf->SetFontSize(13);

		$y_point = 82.8+2.7;
		$pdf->SetXY(47, $y_point);
		$pdf->MultiCell(11, 5, U2T($this->center_function->cal_age_with_target($data['birthday'],  $data['createdatetime'])), $border, 'C');


		$mem_type_pos = array(67.5, 92.8, 123.8, 155.7);
		if($data['mem_type_id'] == 1|| $data['mem_type_id'] == 2){
			$pdf->Image($checkMark,  $mem_type_pos[$data['mem_type_id']-1], $y_point,$dpi); // พนักงาน
		}else {
			$pdf->Image($checkMark,  $mem_type_pos[3], $y_point,$dpi); // พนักงาน
		}

		$pdf->SetXY(168, $y_point);
		$pdf->MultiCell(32, 5, U2T($other), $border, 1);
		$y_point = 90.8+2.7;

		$member_status_pos = array(32.3, 44, 58.6, 70.6);
        if ($data['marry_status'] > 0) {
            $pdf->Image($checkMark, $member_status_pos[$data['marry_status'] - 1], $y_point, $dpi); //โสด
        }


		$pdf->SetXY(95.5, $y_point);
		$pdf->MultiCell(31, 5, U2T($borrower_rank), $border, 1);
		$pdf->SetXY(134.5, $y_point);
		$pdf->MultiCell(27, 5, U2T($data['position']), $border, 1);
		$pdf->SetXY(174.5, $y_point);
		$pdf->MultiCell(25, 5, U2T($borrower_mobile), $border, 1);

		//ADDRESS!!!! (No data)
		$y_point = 97.2+2.7;
		$pdf->SetXY(45.5, $y_point);
		$pdf->MultiCell(23, 5, U2T(num_format($data['salary'])), $border, 'R');
		$pdf->SetXY(105.5, $y_point);
		$pdf->MultiCell(12, 5, U2T($borrower_address_no), $border, "C");
		$pdf->SetXY(121.5, $y_point);
		$pdf->MultiCell(6, 5, U2T($borrower_address_moo), $border, "C");
		$pdf->SetXY(133, $y_point);
		$pdf->MultiCell(27, 5, U2T($borrower_address_soi), $border, "C");
		$pdf->SetXY(167, $y_point);
		$pdf->MultiCell(32, 5, U2T($borrower_address_road), $border, "C");

		$y_point = 104+2.7;
		$pdf->SetXY(35, $y_point);
		$pdf->MultiCell(31, 5, U2T($borrower_district), $border, "C");
		$pdf->SetXY(81, $y_point);
		$pdf->MultiCell(34, 5, U2T($borrower_amphur), $border, "C");
		$pdf->SetXY(125, $y_point);
		$pdf->MultiCell(38, 5, U2T($borrower_province), $border, "C");
		$pdf->SetXY(181, $y_point);
		$pdf->MultiCell(23, 5, U2T($borrower_zipcode), $border, "C");

		//check phone number
		$phone = preg_replace("/(-|\s)+/", "", $borrower_home_tel);
		$area_code = array('02', '03', '04', '05', '07'); //thai telephone number area code
		$tel_home = in_array(substr($phone, 0, 2), $area_code) ? $borrower_home_tel : " - ";

		$y_point = 110.5+2.7;
		$pdf->SetXY(36, $y_point);
		$pdf->MultiCell(33, 5, U2T($tel_home), $border, "C");
		$pdf->SetXY(82, $y_point);
		$pdf->MultiCell(42, 5, U2T($borrower_work_tel), $border, "C");
		$pdf->SetXY(144.5, $y_point);
		$pdf->MultiCell(42, 5, U2T($borrower_mobile), $border, "C");

		$y_point = 131+9.4;
		$pdf->SetXY(107, $y_point);
		$pdf->MultiCell(23, 5, U2T(num_format($data['loan_amount'])), $border, 'R');
		$pdf->SetXY(138, $y_point);
		$pdf->MultiCell(61, 5, U2T($this->center_function->convert($data['loan_amount'])), $border, 'C');


		$y_point = 181.8-1.8-4.2;
		$pdf->SetXY(163.5, $y_point);
		$pdf->MultiCell(30, 5, U2T($installment_m), $border, "R");

		$y_point = 188.5-6;
		$pdf->SetXY(18, $y_point);
		$pdf->MultiCell(66, 5, U2T($this->center_function->convert($data['money_period_1'])), $border, 'C');

        $pdf->SetXY(148.5, $y_point);
        $pdf->MultiCell(14, 5, U2T($percent_y), $border, "C");
        $pdf->SetXY(177, $y_point);
        $pdf->MultiCell(16, 5, U2T($data['period_amount']), $border, "C");

		$y_point = 195.3-6;
		$pdf->SetXY(54, $y_point);
		$pdf->MultiCell(33, 5, U2T($month_arr[date('n', strtotime($data['date_period_1']))]), $border, "C");
		$pdf->SetXY(95, $y_point);
		$pdf->MultiCell(18, 5, U2T(date('Y', strtotime($data['date_period_1'] . "+ 543 Year"))), $border, "C");

		$y_point = 222-6;
		$pdf->SetXY(116, $y_point);
		$pdf->MultiCell(21.5, 5, U2T(number_format($data['share_collect_value'], 2)), $border, "R");
		$pdf->SetXY(146, $y_point);
		$pdf->MultiCell(52, 5, U2T($this->center_function->convert($data['share_collect_value'])), $border, 'C');


		$y_point = 237-6;
		if(isset($guarantee_type)){

			if ($guarantee_type['person_guarantee'] == 1) {
				$pdf->Image($checkMark, 18+8, $y_point, $dpi);//บุคคลค้ำ
			} else if ($guarantee_type['deposit_guarantee'] == 1) {
				$pdf->Image($checkMark,45.2+8, $y_point, $dpi);//เงินฝากค้ำ
			} else if ($guarantee_type['share_guarantee'] == 1) {
				$pdf->Image($checkMark,96.8+8, $y_point, $dpi);//อื่นๆ
				$y_point = 236.7-6;
				$pdf->SetXY(108+8, $y_point);
				$pdf->MultiCell(92, 5, U2T("ใช้หุ้นค้ำประกัน"), $border, 1);
			} else if ($guarantee_type['share_and_deposit_guarantee'] == 1) {
				$pdf->Image($checkMark,96.8, $y_point, $dpi);//อื่นๆ
				$y_point = 236.7+6;
				$pdf->SetXY(108+8, $y_point);
				$pdf->MultiCell(92, 5, U2T("ใช้หุ้นและสมุดบัญชีเงินฝากค้ำประกัน"), $border, 1);
			}
		}

		$y_point = 271.5-6;
		if (isset($guarantee) && sizeof($guarantee)) {

			foreach ($guarantee as $key => $value) {

				$y_sub_point = $y_point - 2.5;
				$pdf->SetXY(21, $y_point);
				$pdf->MultiCell(39.8, 5, U2T($value['prename_short'] . $value['firstname_th'] . " " . $value['lastname_th']), $border, 1);
				$pdf->SetXY(63.5, $y_point);
				$pdf->MultiCell(13, 5, U2T($value['member_id']), $border, 1);
				$pdf->SetXY(78.5-1, $y_point-2.5);
				$pdf->MultiCell(18+2, 5, U2T($value['position']), $border, 1);
				$pdf->SetXY(98.5, $y_point);
				$pdf->MultiCell(13, 5, U2T($value['salary']), $border, 1);
				$pdf->SetXY(114, $y_point);
				$pdf->MultiCell(15, 5, U2T($value['share_collect_value']), $border, 1);

				if (isset($value['guarantee']) && sizeof($value['guarantee'])) {
					foreach ($value['guarantee'] as $val) {
                        $pdf->SetFont('THSarabunNew', '', 10);
						$pdf->SetXY(131.5-1, $y_sub_point);
						$pdf->MultiCell(29.5+2, 5, U2T($val['guarantee_name']), $border, 1);
						$y_sub_point += 4.5;
                        $pdf->SetFont('THSarabunNew', '', 13);
					}
				}

				$pdf->SetXY(164.5, $y_point);
				$pdf->MultiCell(33.8, 5, U2T(''), $border, 1);
				$y_point += 10;

			}
		}

	} else if ($pageNo == '2') {

	} else if ($pageNo == '3') {
		$y_point = 165.4;
		$pdf->SetXY(61.8, $y_point);
		$pdf->MultiCell(2.5, 2.5, U2T(""), $border, 1);

		$y_point = 171.6;
		$pdf->SetXY(17.8, $y_point);
		$pdf->MultiCell(2.5, 2.5, U2T(""), $border, 1);

		$y_point = 75.5+12;
		$pdf->SetXY(40, $y_point);
		$pdf->MultiCell(45.5, 5, U2T($signature), $border, 'C');
		$pdf->SetXY(135, $y_point);
		$pdf->MultiCell(42.7, 5, U2T($signature), $border, 'C');

		$y_point = 81.7+12;
		$pdf->SetXY(38, $y_point);
		$pdf->MultiCell(48.5, 5, U2T($data['prename_short'] . $data['firstname_th'] . " " . $data['lastname_th']), $border, 'C');
		$pdf->SetXY(131, $y_point);
		$pdf->MultiCell(46.7, 5, U2T($witness_name, 0, 0), $border, 'C');

		$y_point = 92+12;
		$pdf->SetXY(135, $y_point);
		$pdf->MultiCell(42.7, 5, U2T($signature), $border, 'C');

		$y_point = 98.6+12;
		$pdf->SetXY(132, $y_point);
		$pdf->MultiCell(46.7, 5, U2T($witness_name, 0, 0), $border, 'C');

		$y_point = 110.3+12;
		$pdf->SetXY(135.3, $y_point);
		$pdf->MultiCell(42.7, 5, U2T($signature), $border, 'C');

		$y_point = 116.6+12;
		$pdf->SetXY(132, $y_point);
		$pdf->MultiCell(46.7, 5, U2T($witness_1), $border, 'C');

		$y_point = 116.8+12;
		$pdf->SetXY(30, $y_point);
		$pdf->MultiCell(40.5, 5, U2T($signature), $border, 'C');

		$y_point = 123.3+12;
		$pdf->SetXY(30, $y_point);
		$pdf->MultiCell(40.5, 5, U2T($data['marry_name']), $border, 'C');

		$y_point = 169.8+8;
		$pdf->SetXY(47+50, $y_point);
		$pdf->MultiCell(53, 5, U2T($reason), $border, 1);

		$y_point = 176+8;
		$pdf->SetXY(40+44, $y_point);
		$pdf->MultiCell(41.8, 5, U2T($signature), $border, 'C');

		$y_point = 181.8+8;
		$pdf->SetXY(37+44+1.5, $y_point);
		$pdf->MultiCell(46.7, 5, U2T($approval_name), $border, 'C');
		$y_point = 188+8;
		$pdf->SetXY(40+44, $y_point);
		$pdf->MultiCell(43.8, 5, U2T($signature), $border, 'C');

		$y_point = 193.8+8;
		$pdf->SetXY(37+44+1.5, $y_point);
		$pdf->MultiCell(46.7, 5, U2T($approval_name), $border, 'C');

		$y_point = 200+8;
		$pdf->SetXY(40+44, $y_point);
		$pdf->MultiCell(43.8, 5, U2T($signature), $border, 'C');

		$y_point = 205.8+8;
		$pdf->SetXY(37+44+1.5, $y_point);
		$pdf->MultiCell(46.7, 5, U2T($approval_name), $border, 'C');

		$y_point = 211.8+8;
		$pdf->SetXY(40+44, $y_point);
		$pdf->MultiCell(43.8, 5, U2T($signature), $border, 'C');

		$y_point = 217.8+8;
		$pdf->SetXY(37+44+1.5, $y_point);
		$pdf->MultiCell(46.7, 5, U2T($approval_name), $border, 'C');

		$y_point = 223.8+8;
		$pdf->SetXY(40+44, $y_point);
		$pdf->MultiCell(43.8, 5, U2T($signature), $border, 'C');

        $y_point = 229.8+8;
        $pdf->SetXY(37+44+1.5, $y_point);
        $pdf->MultiCell(46.7, 5, U2T($approval_name), $border, 'C');

		$y_point = 271.8-5.0;
		$pdf->SetXY(40.8, $y_point);
		$pdf->MultiCell(46.6, 5, U2T($signature), $border, 'C');
		$pdf->SetXY(120, $y_point);
		$pdf->MultiCell(46.6, 5, U2T($signature), $border, 'C');


		$y_point = 278.3-5.0;
		$pdf->SetXY(38, $y_point);
		$pdf->MultiCell(50.6, 5, U2T($data['prename_short'] . $data['firstname_th'] . " " . $data['lastname_th'], 0, 0), $border, 'C');
		$pdf->SetXY(118, $y_point);
		$pdf->MultiCell(50.6, 5, U2T($name_authorities), $border, 'C');

		$y_point = 285-5.0;
		$pdf->SetXY(48, $y_point);
		$pdf->MultiCell(44.6, 5, U2T($this->center_function->ConvertToThaiDate($data['createdatetime'], 0, 0)), $border, 'C');
		$pdf->SetXY(127, $y_point);
		$pdf->MultiCell(45, 5, U2T($this->center_function->ConvertToThaiDate($data['createdatetime'], 0, 0)), $border, 'C');

	}

}

if (isset($guarantee) && sizeof($guarantee)) {

	$guarantee_number = 1;
	foreach ($guarantee as $key => $row) {

		$guarantee_name = $row['prename_full'].$row['firstname_th']." ".$row['lastname_th'];
		$guarantee_id_card = $row['id_card'];
		$guarantee_id = $row['member_id'];
		$guarantee_salary = number_format($row['salary'], 2);
		$guarantee_rank = $row['position'];
		$guarantee_department = $row['department'];
		$guarantee_amphur = $row['amphur_name'];
		$guarantee_district = $row['district_name'];
		$guarantee_province = $row['province_name'];
		$guarantee_tel_arr = preg_split('/(\/+|(\s)+|\s\s+|\s{2,})+/', $row['tel']);
//		$guarantee_mobile = $row['mobile'] ? $row['mobile'] :  $guarantee_tel_arr[sizeof($guarantee_tel_arr)-1];
        $guarantee_mobile = $row['mobile'] ? $row['mobile'] :  "-";
		$guarantee_tel = $guarantee_tel_arr[0];
		$guarantee_address_village = $row['c_address_village'];
		$guarantee_address_no = $row['address_no'];
		$guarantee_address_moo = $row['address_moo'];
		$guarantee_address_soi = $row['address_soi'];
		$guarantee_address_road = $row['address_road'];
		$guarantee_division = $row['divistion'];
		$guarantee_faction = $row['faction'];
		$guarantee_email = $row['email'];
		$guarantee_member_agey = $row['guarantee_member_agey'];
		$guarantee_member_agem = $row['guarantee_member_agem'];
		$guarantee_spouse_name = $row['marry_name'];
		$guarantee_age = $this->center_function->cal_age_with_target($row['birthday'], $data['createdatetime']) ;
		$guarantee_under = $row['mem_group_name'];
		$guarantee_zipcode = $row['zipcode'];


		$filename = $_SERVER["DOCUMENT_ROOT"] . PROJECTPATH . "/assets/document/petition_normal_education_preson.pdf";
		$pageCount_1 = $pdf->setSourceFile($filename);


		//Page 4
		$pdf->AddPage();
		$tplIdx = $pdf->importPage(1);
		$pdf->useTemplate($tplIdx, 0, 0, 0, 0, true);

		$pdf->AddFont('THSarabunNew', '', 'THSarabunNew.php');
		$pdf->SetFont('THSarabunNew', '', 13);

		$pdf->SetTextColor(0, 0, 0);
		$pdf->SetAutoPageBreak(true, 0);

		$y_point = 9-0.5;
		$pdf->SetXY(160, $y_point);
		$pdf->MultiCell(15, 5, U2T($guarantee_number), $border, "C");

		$y_point = 15.8-0.7;
		$pdf->SetXY(165, $y_point);
		$pdf->MultiCell(35, 5, U2T($guarantee_name), $border, "C");

		$y_point = 22-0.4;
		$pdf->SetXY(186, $y_point);
		$pdf->MultiCell(15, 5, U2T(substr($data['contract_number'], 2, 2)), $border, 'C');

		$y_point = 29-0.6;
		$pdf->SetXY(138, $y_point);
		$pdf->MultiCell(18, 5, U2T($contract_no), $border, 'C');
		$pdf->SetXY(167, $y_point);
		$pdf->MultiCell(34, 5, U2T($this->center_function->ConvertToThaiDate($data['createdatetime'], 0, 0)), $border, 'C');


		$y_point = 36-1;
		$pdf->SetXY(162, $y_point);
		$pdf->MultiCell(39, 5, U2T($contract_owner), $border, 1);

		$y_point = 59-0.6;
		$pdf->SetXY(147, $y_point);
		$pdf->MultiCell(53.5, 5, U2T($write_at), $border, "C");

		$y_point = 65.5-0.4;
		$pdf->SetXY(118, $y_point);
		$pdf->MultiCell(52.4, 5, U2T($this->center_function->ConvertToThaiDate($data['createdatetime'], 0, 0)), $border, 'C');

		$y_point = 71.8-0.2 ;
		$pdf->SetXY(40, $y_point);
		$pdf->MultiCell(75, 5, U2T($guarantee_name), $border, 1);
		$pdf->SetFontSize(11);
		$y_point = 80.3;
		$pdf->SetXY(68, $y_point);
		for ($i = 0; $i <= 4; $i++) {
			if ($i > 0) {
				$pdf->SetXY(68 + ($i * 4.9)+(($i/1.5)/10), $y_point);
			}
			$pdf->MultiCell(3.8, 3.8, U2T(substr($guarantee_id_card, $i, 1)), $border, 'C');
		}

		$pdf->SetXY(136, $y_point);
		for ($i = 0; $i <= 12; $i++) {
			if ($i > 0) {
				$pdf->SetXY(136 + ($i * 4.9)+($i/1.5/10), $y_point);
			}
			$pdf->MultiCell(3.8, 3.8, U2T(substr( $guarantee_id_card,$i, 1)), $border, 'C');
		}

		$pdf->SetFontSize(13);

		$y_point = 88;
		$pdf->SetXY(23, $y_point);
		$pdf->MultiCell(10.5, 5, U2T($guarantee_age, 0, 0), $border, 1);
		$pdf->SetXY(153, $y_point);
		$pdf->MultiCell(47, 5, U2T($other), $border, 1);

		$y_point = 88.3;
		$mem_type_pos = array(46.5, 73.5,107.3, 140.8);
		if($data['mem_type_id'] == 1|| $data['mem_type_id'] == 2){
			$pdf->Image($checkMark,  $mem_type_pos[$data['mem_type_id']-1], $y_point,$dpi); // พนักงาน
		}else {
			$pdf->Image($checkMark,  $mem_type_pos[3], $y_point,$dpi); // พนักงานs
		}

		$y_point = 96.3;

		$member_status_pos = array(32.7, 45.3, 60.3, 72.6);
        if ($data['marry_status'] > 0) {
            $pdf->Image($checkMark, $member_status_pos[$data['marry_status'] - 1], $y_point, $dpi); //โสด
        }

		$y_point = 96;
		$pdf->SetXY(98, $y_point);
		$pdf->MultiCell(47, 5, U2T($guarantee_rank, 0, 0), $border, 1);
		$pdf->SetXY(155, $y_point);
		$pdf->MultiCell(45.3, 5, U2T($guarantee_under, 0, 0), $border, 1);

		$y_point = 102.8;
		$pdf->SetXY(30, $y_point);
		$pdf->MultiCell(40, 5, U2T($guarantee_tel, 0, 0), $border, 1);
		$pdf->SetXY(98, $y_point);
		$pdf->MultiCell(26.8, 5, U2T($guarantee_salary, 0, 0), $border, 'C');
		$pdf->SetXY(161, $y_point);
		$pdf->MultiCell(22, 5, U2T($guarantee_address_no, 0, 0), $border, 'C');
		$pdf->SetXY(190, $y_point);
		$pdf->MultiCell(9.8, 5, U2T($guarantee_address_moo, 0, 0), $border, 'C');

		$y_point = 109.5;
		$pdf->SetXY(23.8, $y_point);
		$pdf->MultiCell(31, 5, U2T($guarantee_address_soi, 0, 0), $border, 'C');
		$pdf->SetXY(62.8, $y_point);
		$pdf->MultiCell(31, 5, U2T($guarantee_address_road, 0, 0), $border, 'C');
		$pdf->SetXY(111, $y_point);
		$pdf->MultiCell(38, 5, U2T($guarantee_district, 0, 0), $border, 'C');
		$pdf->SetXY(164, $y_point);
		$pdf->MultiCell(35.5, 5, U2T($guarantee_amphur, 0, 0), $border, 'C');

		$y_point = 116;
		$pdf->SetXY(27, $y_point);
		$pdf->MultiCell(33, 5, U2T($guarantee_province, 0, 0), $border, 'C');
		$pdf->SetXY(79.8, $y_point);
		$pdf->MultiCell(19.5, 5, U2T($guarantee_zipcode, 0, 0), $border, 'C');
		$pdf->SetXY(112, $y_point);
		$pdf->MultiCell(36, 5, U2T($guarantee_mobile, 0, 0), $border, 'C');

		$y_point = 136.5;
		$pdf->SetXY(65, $y_point);
		$pdf->MultiCell(100, 5, U2T($data['prename_short'] . $data['firstname_th'] . " " . $data['lastname_th'], 0, 0), $border, 1);

		$y_point = 143.3;
		$pdf->SetXY(50+20+17, $y_point);
		$pdf->MultiCell(33, 5, U2T($data['contract_number']), $border, 1);
		$pdf->SetXY(93.5+20+18, $y_point);
		$pdf->MultiCell(45.5, 5, U2T($this->center_function->ConvertToThaiDate($data['createdatetime'], 0, 0)), $border, 1);

		$y_point = 150;
        $pdf->SetXY(17, $y_point);
        $pdf->MultiCell(32.8, 5, U2T(num_format($data['loan_amount'])), $border, 'R');
		$pdf->SetXY(19+15+20+3, $y_point);
		$pdf->MultiCell(86, 5, U2T($this->center_function->convert($data['loan_amount'])), $border, 'C');

		$y_point = 156.5;
        $pdf->SetXY(55, $y_point);
        $pdf->MultiCell(15.5, 5, U2T($percent_y), $border, "C");
		$pdf->SetXY(172, $y_point);
		$pdf->MultiCell(20.5, 5, U2T($installment_m, 0, 0), $border, "R");

		$y_point = 163.3;
        $pdf->SetXY(19, $y_point);
        $pdf->MultiCell(77.8, 5, U2T($installment_m_th, 0, 0), $border, 'C');
		$pdf->SetXY(179, $y_point);
		$pdf->MultiCell(15.5, 5, U2T($amount_m, 0, 0), $border, "C");

		//Page 5
		$pdf->AddPage();
		$tplIdx = $pdf->importPage(2);
		$pdf->useTemplate($tplIdx, 0, 0, 0, 0, true);

		$pdf->AddFont('THSarabunNew', '', 'THSarabunNew.php');
		$pdf->SetFont('THSarabunNew', '', 13);

		$pdf->SetTextColor(0, 0, 0);
		$pdf->SetAutoPageBreak(true, 0);


		$y_point = 160-2.3;
		$pdf->SetXY(40, $y_point);
		$pdf->MultiCell(45.8, 5, U2T($signature), $border, 'C');
		$pdf->SetXY(142, $y_point);
		$pdf->MultiCell(44, 5, U2T($signature), $border, 'C');

		$y_point = 166.8-2.3;
		$pdf->SetXY(40, $y_point);
		$pdf->MultiCell(49, 5, U2T($guarantee_name, 0, 0), $border, 'C');
		$pdf->SetXY(140, $y_point);
		$pdf->MultiCell(48.8, 5, U2T($witness_name, 0, 0), $border, 'C');

		$y_point = 173-2.3;
		$pdf->SetXY(142, $y_point);
		$pdf->MultiCell(45.8, 5, U2T($signature), $border, 'C');

		$y_point = 179.8-2.3;
		$pdf->SetXY(140, $y_point);
		$pdf->MultiCell(48.8, 5, U2T($witness_name, 0, 0), $border, 'C');

		$y_point = 203-2.3;
		$pdf->SetXY(28.8, $y_point);
		$pdf->MultiCell(39.5+5, 5, U2T($signature), $border, 'C');

		$y_point = 209.8-2.3;
		$pdf->SetXY(28.8, $y_point);
		$pdf->MultiCell(40+5, 5, U2T($guarantee_spouse_name, 0, 0), $border, 'C');


	}
}

$filename = $_SERVER["DOCUMENT_ROOT"] . PROJECTPATH . "/assets/document/petition_normal_education_footer.pdf";

$pageCount_1 = $pdf->setSourceFile($filename);
$pdf->AddPage();
$tplIdx = $pdf->importPage(1);
$pdf->useTemplate($tplIdx, 0, 0, 0, 0, true);


//exit;
$pdf->Output();
