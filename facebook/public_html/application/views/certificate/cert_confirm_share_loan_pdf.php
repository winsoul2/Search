<?php
function U2T($text) { return @iconv("UTF-8", "TIS-620//IGNORE", ($text)); }
function num_format($text) { 
    if($text!=''){
        return number_format($text,2);
    }else{
        return '';
    }
}
function SetDash($pdf, $black=null, $white=null) {
    if($black!==null)
        $s=sprintf('[%.3F %.3F] 0 d',$black*$pdf->k,$white*$pdf->k);
    else
        $s='[] 0 d';
        $pdf->_out($s);
}

$pdf = new FPDI();

$pdf->AddFont('THSarabunNew', '', 'THSarabunNew.php');
$pdf->AddFont('THSarabunNew-Bold', '', 'THSarabunNew-Bold.php');
foreach ($datas as $member_id => $member_info) {
    if($member_id = '00924'){ // รถไฟ สมาชิกหมายเลข 00924 ใช้กระดาษ F14
        $pdf->AddPage('P' ,array(210 , 356)); //F14
    }else{
        $pdf->AddPage();
    }
    $pdf->SetMargins(0, 0, 0);
    $border = 0;
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetAutoPageBreak(true,0);
//    $pdf->Image(base_url().PROJECTPATH.'assets/images/coop_profile/'.@$row_profile['coop_img'],15,12,-700);
     $pdf->Image('assets/images/coop_profile/'.@$row_profile['coop_img'],15,12,-700);

    $y = 15;
    $pdf->SetFont('THSarabunNew-Bold', '', 14 );
    $pdf->SetXY(55, $y);
    $pdf->Cell(140, 8, U2T($row_profile['coop_name_th']), 0, 'C');
    $y += 8;
    $pdf->SetXY(70, $y);
    $pdf->Cell(140, 8, U2T("หนังสือยืนยันยอดลูกหนี้ เงินฝาก และทุนเรือนหุ้น"), 0, 'C');

    $pdf->SetFont('THSarabunNew', '', 14 );
    $y += 8;
    $pdf->SetXY( 0, $y );
    $pdf->MultiCell(205, 8, U2T("วันที่ ".$this->center_function->ConvertToThaiDate($date,'','0')), 0, 'R');
    $pdf->SetFont('THSarabunNew', '', 15 );
    $y += 8;
    $pdf->SetXY( 0, $y );
    $pdf->MultiCell(210, 8, U2T("เรียน ".$member_info["prename_full"].$member_info["firstname_th"]." ".$member_info["lastname_th"]." สมาชิกเลขทะเบียน ".$member_info["member_id"]." สังกัด ".$member_info['address_send_doc']), 0, 'C');
    $pdf->SetFont('THSarabunNew', '', 14 );
    $y += 8;
    $pdf->SetXY( 0, $y );
    $pdf->MultiCell(210, 8, U2T($row_profile['coop_name_th']." ขอเรียนว่า ณ วันที่ ".$this->center_function->ConvertToThaiDate($date,'','0')), 0, 'C');

    $pdf->SetFont('THSarabunNew', '', 13 );
    $count = 0;
    $y = 47;
    $line_height = 6;
    //Loan
    if (!empty($member_info["loan"])) {
        $y += 8;
        $count++;
        $pdf->SetXY( 20, $y );
        $pdf->MultiCell(105, $line_height, U2T($count.". ท่านเป็นหนี้ต่อสหกรณ์ฯ เป็นจำนวนเงินทั้งสิ้น"), 0, 'L');
        $pdf->SetXY( 125, $y );
        $pdf->MultiCell(30, $line_height, number_format($total_data[$member_id]["loan"],2).U2T(" บาท"), 0, 'R');
        $pdf->SetXY( 155, $y );
        $pdf->MultiCell(40, $line_height, U2T(" ตามรายการดังต่อไปนี้"), 0, 'R');
        $sub_count = 0;
        foreach($member_info["loan"] as $id => $loan) {
            $sub_count++;
            $y += 6;
            $pdf->SetXY( 30, $y );
            $pdf->MultiCell(105, $line_height, U2T($count.".".$sub_count." ".$loan['prefix_code'].$loan['contract_number']), 0, 'L');
            $pdf->SetXY( 125, $y );
            $pdf->MultiCell(30, $line_height, U2T("หนี้คงเหลือ"), 0, 'R');
            $pdf->SetXY( 155, $y );
            $pdf->MultiCell(40, $line_height, number_format(!empty($loan["total"]) ? $loan["total"] : 0,2).U2T(" บาท"), 0, 'R');
        }
    }

    //Account
    if (!empty($member_info["account"])) {
        $count++;
        $y += 8;
        $pdf->SetXY( 20, $y );
        $pdf->MultiCell(105, $line_height, U2T($count.". มีเงินฝากไว้กับสหกรณ์ฯ เป็นจำนวนเงินทั้งสิ้น"), 0, 'L');
        $pdf->SetXY( 125, $y );
        $pdf->MultiCell(30, $line_height, number_format($total_data[$member_id]["account"],2).U2T(" บาท"), 0, 'R');
        $pdf->SetXY( 155, $y );
        $pdf->MultiCell(40, $line_height, U2T(" ตามรายการดังต่อไปนี้"), 0, 'R');
        $sub_count = 0;
        foreach($member_info["account"] as $id => $account) {
            $sub_count++;
            $y += 6;
            $pdf->SetXY( 30, $y );
            $pdf->MultiCell(105, $line_height, U2T($count.".".$sub_count." ".$account['type_name']." ".$account['account_id']), 0, 'L');
            $H = $pdf->GetY()-$y;
            $pdf->SetXY( 125, $y );
            $pdf->MultiCell(30, $line_height, U2T("จำนวนเงิน"),0, 'R');
            $pdf->SetXY( 155, $y );
            $pdf->MultiCell(40, $line_height, number_format($account["balance"],2).U2T(" บาท"), 0, 'R');
            $y = $H+$y-7;
        }
    }
    
    //Share
    if (!empty($member_info["share_collect_value"])) {
        $count++;
        $y += 8;
        $pdf->SetXY( 20, $y );
        $pdf->MultiCell(105, $line_height, U2T($count.". ทุนเรือนหุ้น เป็นจำนวน"), 0, 'L');
        $pdf->SetXY( 125, $y );
        $pdf->MultiCell(30, $line_height, number_format($member_info["share_collect_value"],2).U2T(" บาท"), 0, 'R');
        $pdf->SetXY( 155, $y );
        $pdf->MultiCell(40, $line_height, "", 0, 'R');
    }

    // $y += 8;
    $y = 105;
    if($member_id = '00924'){
        $y = 120;
    }
    $pdf->SetFont('THSarabunNew', '', 13 );
    $pdf->SetXY( 140, $y+3);
    $pdf->MultiCell(0, 8, U2T("ขอแสดงความนับถือ"), 0, 'C');
    $y += 8;
    $pdf->SetXY( 140, $y);
//    $pdf->Image(base_url().PROJECTPATH.'assets/images/coop_signature/'.$signature->signature_4,164,$y+2,20);
    $pdf->Image('assets/images/coop_signature/'.$signature->signature_4,164,$y+2,20);
    $y += 8;
    $pdf->SetXY( 140, $y-0.5);
    $pdf->MultiCell(0, 8, U2T("( ".$signature->president_name." )"), 0, 'C');
    SetDash($pdf, 0.5, 0.5);
    $pdf->Line(162, $y+5.5, 188, $y+5.5);
    $y += 6;
    $pdf->SetXY( 140, $y);
    $pdf->MultiCell(0, 6, U2T("ประธานกรรมการ"), 0, 'C');
    $y += 6;
    // $y = 190;
    $pdf->SetXY( 140, $y);
//    $pdf->MultiCell(0, 6, U2T($row_profile['coop_name_th']), 0, 'C');

    // if ($y >= 190) {
    //     $pdf->AddPage();
    //     $pdf->SetMargins(0, 0, 0);
    //     $border = 0;
    //     $pdf->SetTextColor(0, 0, 0);
    //     $pdf->SetAutoPageBreak(true,15);
    //     $y = 20;
    // }

    $y += 0;
    $pdf->SetFont('THSarabunNew-Bold', '', 13 );
    $pdf->SetXY( 0, $y);
    $pdf->MultiCell(0, 6, U2T("หมายเหตุ ถ้าตรวจสอบแล้วโปรดส่งกลับคืนหาผู้สอบบัญชีของสหกรณ์ภายใน 7 วันหลังได้รับหนังสือนี้ เพื่อประโยชน์ในการตรวจสอบต่อไป"), 0, 'C');
    $y += 8;
    SetDash($pdf, 2, 2);
    $pdf->Line(10, $y, 200, $y);
    SetDash($pdf, 0, 0);

//    $pdf->Image(base_url().PROJECTPATH.'assets/images/icon_scissors_right.png',200,$y-2.5,5);
    $pdf->Image('assets/images/icon_scissors_right.png',200,$y-2.5,5);
//    $pdf->Image(base_url().PROJECTPATH.'assets/images/icon_scissors_left.png',5,$y-2.5,5);
    $pdf->Image('assets/images/icon_scissors_left.png',5,$y-2.5,5);

    $pdf->SetFont('THSarabunNew-Bold', '', 14 );
    $y += 5;
    $pdf->SetXY( 0, $y);
    $pdf->MultiCell(0, 6, U2T("หนังสือตอบยืนยันยอด"), 0, 'C');
    $y += 8;
    $pdf->SetFont('THSarabunNew', '', 13 );
    $pdf->SetXY( 20, $y);
    $pdf->MultiCell(0, 6, U2T("เรียน ".$_GET['account_name']." ผู้สอบบัญชี".$row_profile['coop_name_th']), 0, 'L');
    $y += 8;
    $pdf->SetXY( 20, $y);
    $pdf->MultiCell(0, 6, U2T("           ข้าพเจ้าขอยืนยันจำนวนเงินที่เป็นหนี้ เงินรับฝาก และทุนเรือนหุ้น ระหว่างข้าพเจ้า "), 0, 'L');
    $y += 6;
    $pdf->SetXY( 20, $y);
    $pdf->Cell(100, 6, U2T("กับ ".$row_profile['coop_name_th']." ณ วันที่ "),0, 0, 'L');
    $pdf->SetFont('THSarabunNew', 'U', 13 );
    $pdf->SetXY( 123, $y);
    $pdf->Cell(25, 6, U2T($this->center_function->ConvertToThaiDate($date,'','0')),0, 0, 'L');
    $pdf->SetFont('THSarabunNew', '', 13 );
    $pdf->SetXY( 150, $y);
    $pdf->Cell(20, 6, U2T(" ดังนี้"),0, 0, 'L');
    $pdf->SetFont('THSarabunNew', '', 13 );
    $count = 0;
    $y += 0;
    $line_height = 6;
    //Loan
    if (!empty($member_info["loan"])) {
        if ($total_data[$member_id]['loan'] > 0) {
            $y += 8;
            $count++;
            $pdf->SetXY(20, $y);
            $pdf->MultiCell(0, $line_height, U2T($count . ". จำนวนเงินเป็นหนี้ต่อสหกรณ์ ทั้งสิ้น " . number_format($total_data[$member_id]["loan"], 2) . " บาท (" . $this->center_function->convert($total_data[$member_id]["loan"]) . ")    ตามรายการดังต่อไปนี้"), 0, 'L');
            $sub_count = 0;
            foreach ($member_info["loan"] as $id => $loan) {
                if($loan['total'] > 0) {
                    $sub_count++;
                    $y += 6;
                    $pdf->SetXY(30, $y);
                    $pdf->Cell(65, $line_height, U2T($count . "." . $sub_count . " หนังสือเงินกู้" . $loan['loan_name'] . "  ที่ " . $loan['prefix_code'].$loan['contract_number'] . "      " . "คงเหลือ " . number_format(!empty($loan["total"]) ? $loan["total"] : 0, 2) . "  บาท"), $border, 0, 'L');

                    $y += 5;
                    $pdf->SetLineWidth(0.08);
                    $pdf->Rect(40, $y + 1, 4, 4);
                    $pdf->SetXY(47, $y);
                    $pdf->Cell(30, $line_height, U2T("ถูกต้อง"), 0, 0, 'L');
                    $pdf->SetLineWidth(0.08);
                    $pdf->Rect(70, $y + 1, 4, 4);
                    $pdf->SetXY(77, $y);
                    $pdf->Cell(30, $line_height, U2T("ไม่ถูกต้อง"), 0, 0, 'L');
                }
            }
        }
    }

    //Account
    if (!empty($member_info["account"])) {
        $count++;
        $y += 6;
        $pdf->SetXY( 20, $y );
        $pdf->MultiCell(170, $line_height, U2T($count.". จำนวนเงินฝากไว้กับสหกรณ์ ".number_format($total_data[$member_id]["account"],2)." บาท (".$this->center_function->convert(number_format($total_data[$member_id]["account"],2)).")   ตามรายการดังต่อไปนี้"), $border, 'L');
        $sub_count = 0;
        foreach($member_info["account"] as $id => $account) {
            $sub_count++;
            $y += 6;
            $pdf->SetXY( 30, $y );
            $pdf->MultiCell(105, $line_height, U2T($count.".".$sub_count." ".$account['type_name']." เลขที่ ".$account['account_id']."  ชื่อบัญชี ".$account['account_name']), $border, 'L');
            $H = $pdf->GetY()-$y;
            $pdf->SetXY( 110, $y );
            $pdf->MultiCell(30, $line_height, U2T("จำนวนเงิน"),0, 'R');
            $pdf->SetXY( 125, $y );
            $pdf->MultiCell(40, $line_height, number_format($account["balance"],2).U2T(" บาท"), $border, 'R');
            $y = $H+$y-7;

            $y += 6;
            $pdf->SetLineWidth(0.08);
            $pdf->Rect(40, $y+1, 4, 4);
            $pdf->SetXY( 47, $y );
            $pdf->Cell(30, $line_height, U2T("ถูกต้อง"),0, 0, 'L');
            $pdf->SetLineWidth(0.08);
            $pdf->Rect(70, $y+1, 4, 4);
            $pdf->SetXY( 77, $y );
            $pdf->Cell(30, $line_height, U2T("ไม่ถูกต้อง"),0, 0, 'L');
        }
    }

    //Share
    if (!empty($member_info["share_collect_value"])) {
        $count++;
        $y += 6;
        $pdf->SetXY( 20, $y );
        $pdf->MultiCell(130, $line_height, U2T($count.". จำนวนเงินค่าหุ้น ").number_format($member_info["share_collect_value"],2).U2T(" บาท (".$this->center_function->convert($member_info["share_collect_value"]).")"), $border, 'L');
        $y += 6;
        $pdf->SetLineWidth(0.08);
        $pdf->Rect(40, $y+1, 4, 4);
        $pdf->SetXY( 47, $y );
        $pdf->Cell(30, $line_height, U2T("ถูกต้อง"),0, 0, 'L');
        $pdf->SetLineWidth(0.08);
        $pdf->Rect(70, $y+1, 4, 4);
        $pdf->SetXY( 77, $y );
        $pdf->Cell(30, $line_height, U2T("ไม่ถูกต้อง"),0, 0, 'L');
    }
    $y += 6;
    $pdf->SetXY( 30, $y);
    $pdf->MultiCell(40, 6, U2T("(ถ้าไม่ถูกต้อง) สาเหตุเพราะ"), 0, 'L');
    $pdf->Line(70, $y+5, 190, $y+5);
//    $y += 6;
//    $pdf->Line(70, $y+5, 190, $y+5);
    $y += 10;
    $pdf->SetXY( 30, $y);
    $pdf->MultiCell(40, 6, U2T("จึงเรียนมาเพื่อทราบ"), 0, 'L');
    $y += 0;
    $pdf->SetXY( 130, $y);
    $pdf->MultiCell(10, 6, U2T("ลงชื่อ"), 0, 'L');
    SetDash($pdf, 0.5, 0.5);
    $pdf->Line(140, $y+5, 188, $y+5);
    $y += 6;
    $pdf->SetXY( 137, $y);
    $pdf->MultiCell(1, 6, U2T("("), 0, 'L');
    $pdf->SetXY( 139, $y);
    $pdf->MultiCell(50,6, U2T($member_info["prename_full"].$member_info["firstname_th"]." ".$member_info["lastname_th"]), 0, "C");
    $pdf->SetXY( 188, $y);
    $pdf->MultiCell(1, 6, U2T(")"), 0, 'L');
    $pdf->Line(140, $y+6, 188, $y+6);
    $y += 6;
    $pdf->SetXY( 139, $y);
    $pdf->MultiCell(50,6, U2T("เลขทะเบียนสมาชิก ".$member_info["member_id"]), 0, "C");
    $pdf->SetFont('THSarabunNew', '', 20 );
    $y = 275;
    if($member_id = '00924'){
        $y = 345;
    }
    $pdf->SetXY( 10, $y);
    $pdf->MultiCell(50,6, U2T("(ส่งคืนสหกรณ์)"), 0, "C");
}

$pdf->Output();