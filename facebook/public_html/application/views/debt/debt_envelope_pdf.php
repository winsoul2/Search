<?php
	function U2T($text) { return @iconv("UTF-8", "TIS-620//IGNORE", ($text)); }
	function num_format($text) {
		if($text!=''){
			return number_format($text,2);
		}else{
			return '';
		}
	}

	$filename = $_SERVER["DOCUMENT_ROOT"].PROJECTPATH."/assets/document/debt_envelope.pdf" ;

	$pdf = new FPDI();
	$pdf->setSourceFile($filename);
	$pdf->SetAutoPageBreak(false);

	foreach($datas as $data) {
		$pdf->AddPage();
		$tplIdx = $pdf->importPage(1);
		$pdf->useTemplate($tplIdx, null, null, 0, 0, true);
		$pdf->AddFont('THSarabunNew', '', 'THSarabunNew.php');
		$pdf->AddFont('THSarabunNew-Bold', '', 'THSarabunNew-Bold.php');
		$pdf->SetFont('THSarabunNew', '', 15 );
		$pdf->SetMargins(0, 0, 0);
		$border = 0;
		$pdf->SetTextColor(0, 0, 0);

		$pdf->SetXY( 14, 14 );
		$pdf->MultiCell(72, 8, U2T('สหกรณ์ออมทรัพย์ครูสมุทรปราการ จำกัด 23/17-20 ถนนสุขุมวิท ตำบลปากน้ำ อำเภอเมือง จังหวัดสมุทรปราการ 10270 โทร. 02-3842493-4'), 0, '');

		$pdf->SetXY( 170, 14 );
		$pdf->MultiCell(42, 8, U2T('ชำระค่าฝากส่งเป็นรายเดือน ใบอนุญาต ที่ 63/2561 ปณจ. สมุทรปราการ'), 1, 'C');

		$pdf->SetFont('THSarabunNew-Bold', '', 15 );
		$pdf->SetXY( 80, 50 );
		$pdf->MultiCell(15, 8, U2T('กรุณาส่ง'), 0, 'L');

		$member_name = $data["prename_full"].$data["firstname_th"]." ".$data["lastname_th"]."\n";
		$member_name .= !empty($data["no"]) ? $data["no"]." " : "";
		$member_name .= !empty($data["moo"]) ? $data["moo"]." " : "";
		$member_name .= !empty($data["village"]) ? $data["village"]." " : "";
		$member_name .= !empty($data["road"]) ? $data["road"]." " : "";
		$member_name .= !empty($data["soi"]) ? $data["soi"]." " : "";
		if($data["province_id"] == 1) {
			$member_name .= !empty($data["district_name"]) ? "แขวง".$data["district_name"]." " : "";
			$member_name .= !empty($data["amphur_name"]) ? "เขต".$data["amphur_name"]." " : "";
		} else {
			$member_name .= !empty($data["district_name"]) ? "ต.".$data["district_name"]." " : "";
			$member_name .= !empty($data["amphur_name"]) ? "อ.".$data["amphur_name"]." " : "";
		}
		$member_name .= !empty($data["province_name"]) ? $data["province_name"]." " : "";
		$member_name .= "".$data["zipcode"];
		$pdf->SetFont('THSarabunNew', '', 15 );
		$pdf->SetXY( 95, 50 );
		$pdf->MultiCell(60, 8, U2T($member_name), 0, 'L');
	}

	$pdf->Output();