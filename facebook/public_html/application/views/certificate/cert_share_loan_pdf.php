<?php
function U2T($text) { return @iconv("UTF-8", "TIS-620//IGNORE", ($text)); }
function num_format($text) { 
    if($text!=''){
        return number_format($text,2);
    }else{
        return '';
    }
}
	
$pdf = new FPDI();

$pdf->AddFont('THSarabunNew', '', 'THSarabunNew.php');
$pdf->AddFont('THSarabunNew-Bold', '', 'THSarabunNew-Bold.php');

foreach ($datas as $member_id => $member_info) {
    $pdf->AddPage();
    $pdf->SetMargins(0, 0, 0);
    $border = 0;
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetAutoPageBreak(true,0);
    // $pdf->Image('assets/images/coop_profile/'.@$row_profile['coop_img'],91,20,30);
    $pdf->Image(base_url().PROJECTPATH.'assets/images/coop_profile/'.@$row_profile['coop_img'],91,15,30);

    $pdf->SetFont('THSarabunNew-Bold', '', 16 );
    $pdf->SetXY( 0, 10 );
    $title = "หนังสือรับรองหุ้น-หนี้-เงินฝาก";
    if($_GET["type"] == "share") {
        $title = "หนังสือรับรองหุ้น";
    } else if ($_GET["type"] == "loan") {
        $title = "หนังสือรับรองหนี้";
    } else if ($_GET["type"] == "deposit") {
        $title = "หนังสือรับรองเงินฝาก";
    }
    $pdf->MultiCell(210, 100, U2T($title), 0, 'C');

    $pdf->SetXY( 0, 20 );
    $pdf->MultiCell(210, 100, U2T("สหกรณ์ออมทรัพย์ครูสมุทรปราการ จำกัด"), 0, 'C');

    $pdf->SetFont('THSarabunNew', '', 16 );
    $pdf->SetXY( 0, 35 );
    $pdf->MultiCell(210, 100, U2T("ขอรับรองว่า ".$member_info["prename_full"].$member_info["firstname_th"]." ".$member_info["lastname_th"]." สมาชิกเลขทะเบียน ".$member_info["member_id"]), 0, 'C');

    $pdf->SetXY( 0, 45 );
    $pdf->MultiCell(210, 100, U2T("สังกัด ".$member_info["name"]), 0, 'C');

    $pdf->SetXY( 0, 55 );
    $pdf->MultiCell(210, 100, U2T("สถานภาพ ณ วันที่ ".$this->center_function->ConvertToThaiDate($date,'','0')), 0, 'C');

    $pdf->SetFont('THSarabunNew', '', 14 );
    $y_point = 115;
    $pdf->SetXY( 20, $y_point );
    $pdf->MultiCell(50, 8, U2T('รายการ'), 1, 'C');
    $pdf->SetXY( 70, $y_point );
    $pdf->MultiCell(25, 8, U2T("เงินต้น"), 1, 'C');
    $pdf->SetXY( 95, $y_point );
    $pdf->MultiCell(25, 8, U2T('ดอกเบี้ย'), 1, 'C');
    $pdf->SetXY( 120, $y_point );
    $pdf->MultiCell(25, 8, U2T('ดอกเบี้ยคงค้าง'), 1, 'C');
    $pdf->SetXY( 145, $y_point );
    $pdf->MultiCell(25, 8, U2T('ส่งหักต่อเดือน'), 1, 'C');
    $pdf->SetXY( 170, $y_point );
    $pdf->MultiCell(25, 8, U2T('ยอดคงเหลือ'), 1, 'C');

    //Share
    if (!empty($member_info["share_collect_value"])) {
        $y_point += 8;
        $pdf->SetXY( 20, $y_point );
        $pdf->MultiCell(50, 8, U2T('ทุนเรือนหุ้น'), 1, 'L');
        $H = $pdf->GetY();
        $pdf->SetXY( 70, $y_point );
        $pdf->MultiCell(25, $H-$y_point, "0.00", 1, 'R');
        $pdf->SetXY( 95, $y_point );
        $pdf->MultiCell(25, $H-$y_point, "0.00", 1, 'R');
        $pdf->SetXY( 120, $y_point );
        $pdf->MultiCell(25, $H-$y_point, "0.00", 1, 'R');
        $pdf->SetXY( 145, $y_point );
        $pdf->MultiCell(25, $H-$y_point, number_format($member_info["share_month"],2), 1, 'R');
        $pdf->SetXY( 170, $y_point );
        $pdf->MultiCell(25, $H-$y_point, number_format($member_info["share_collect_value"],2), 1, 'R');
    }

    //Loan
    if (!empty($member_info["loan"])) {
        $y_point += 8;
        $h = 0;
        foreach($member_info["loan"] as $id => $loan) {
            $y_point += $h;
            $type = "";
            if ($loan['type'] == "normal") {
                $type = "เงินกู้สามัญ";
            } else if ($loan['type'] == "emergent") {
                $type = "เงินกู้ฉุกเฉิน";
            } else if ($loan['type'] == "special") {
                $type = "เงินกู้พิเศษ";
            } else if ($loan['type'] == "atm") {
                $type = "เงินกู้ฉุกเฉิน ATM";
            }
            $pdf->SetXY( 20, $y_point );
            $pdf->MultiCell(50, 8, U2T($type." ".$loan['contract_number']), 1, 'L');
            $H = $pdf->GetY();
            $pdf->SetXY( 70, $y_point );
            $pdf->MultiCell(25, $H-$y_point, number_format($loan["principal"],2), 1, 'R');
            $pdf->SetXY( 95, $y_point );
            $pdf->MultiCell(25, $H-$y_point, number_format($loan["interest"],2), 1, 'R');
            $pdf->SetXY( 120, $y_point );
            $pdf->MultiCell(25, $H-$y_point, "0.00", 1, 'R');
            $pdf->SetXY( 145, $y_point );
            $pdf->MultiCell(25, $H-$y_point, number_format(($loan["principal"]+$loan["interest"]),2), 1, 'R');
            $pdf->SetXY( 170, $y_point );
            $pdf->MultiCell(25, $H-$y_point, number_format($loan["total"],2), 1, 'R');
            $h = $H-$y_point;
        }
    }

    //Account
    if (!empty($member_info["account"])) {
        $y_point += 8;
        $h = 0;
        foreach($member_info["account"] as $id => $account) {
            $y_point += $h;
            $pdf->SetXY( 20, $y_point );
            $pdf->MultiCell(50, 8, U2T($account['type_name']." ".$account['account_id']), 1, 'L');
            $H = $pdf->GetY();
            $pdf->SetXY( 70, $y_point );
            $pdf->MultiCell(25, $H-$y_point, "0.00", 1, 'R');
            $pdf->SetXY( 95, $y_point );
            $pdf->MultiCell(25, $H-$y_point, "0.00", 1, 'R');
            $pdf->SetXY( 120, $y_point );
            $pdf->MultiCell(25, $H-$y_point, "0.00", 1, 'R');
            $pdf->SetXY( 145, $y_point );
            $pdf->MultiCell(25, $H-$y_point, "0.00", 1, 'R');
            $pdf->SetXY( 170, $y_point );
            $pdf->MultiCell(25, $H-$y_point, number_format($account["balance"],2), 1, 'R');
            $h = $H-$y_point;
        }
        $y_point = $H;
    }

    if ($y_point >= 240) {
        $pdf->AddPage();
        $pdf->SetMargins(0, 0, 0);
        $border = 0;
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetAutoPageBreak(true,0);
        $y_point = 25;
    }

    $pdf->SetFont('THSarabunNew', '', 16 );
    $y_point += 10;
    $pdf->SetXY( 30, $y_point );
    $pdf->MultiCell(0, 10, U2T("อนึ่ง สมาชิกสามารถก่อภาระผูกพันและหนี้สินที่อาจเกิดขึ้นภายหน้า ฉะนั้นก่อนจะอนุมัติเงิน"), 0, 'L');
    $y_point += 10;
    $pdf->SetXY( 20, $y_point );
    $pdf->MultiCell(0, 10, U2T("ให้สอบถามสหกรณ์ฯ อีกครั้งหนึ่ง"), 0, 'L');

    $y_point += 10;
    $pdf->SetXY( 130, $y_point );
    $pdf->MultiCell(0, 10, U2T("ลงชื่อ.................................................."), 0, 'L');
    $y_point += 10;
    $pdf->SetXY( 130, $y_point );
    $pdf->MultiCell(0, 10, U2T("(............................................................)"), 0, 'L');
    $y_point += 10;
    $pdf->SetXY( 138, $y_point );
    $pdf->MultiCell(0, 10, U2T("หัวหน้าฝ่ายทะเบียนสมาชิก"), 0, 'L');
}

$pdf->Output();