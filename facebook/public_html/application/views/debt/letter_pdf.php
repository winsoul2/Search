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
			$pdf->AddPage();
			$pdf->AddFont('THSarabunNew', '', 'THSarabunNew.php');
			$pdf->SetFont('THSarabunNew', '', 13 );
			$pdf->SetMargins(0, 0, 0);
			$border = 0;
			$pdf->SetTextColor(0, 0, 0);
			$pdf->SetAutoPageBreak(true,0);
			$pdf->Image('assets/images/coop_profile/'.@$row_profile['coop_img'],90,10,30);
			
			$loan_runno = '007229(1)-04-2561/2561';
			
			$y_point = 20;
			$pdf->SetXY( 20, $y_point );			
			$pdf->MultiCell(72, 6, U2T('ที่ สอ.สป.(สินเชื่อ)    '.@$loan_runno), $border, 'L');
			
			$y_point = 20;
			$pdf->SetXY( 122, $y_point );			
			$pdf->MultiCell(70, 6, U2T(@$row_profile['coop_name_th']), $border, 'L');
			
			$y_point = 26;
			$pdf->SetXY( 122, $y_point );
			$pdf->MultiCell(70, 6, U2T(@$row_profile['address1']." ".@$row_profile['address2']), $border, 'L');
						
			$y_point = 44;
			$pdf->SetXY( 105, $y_point );
			$pdf->MultiCell(70, 6, U2T('31 พฤษภาคม 2561'), $border, 'L');			
			
			$y_point = 56;
			$pdf->SetXY( 20, $y_point );			
			$pdf->MultiCell(72, 6, U2T('เรื่อง แจ้งหนี้ค้างชำระ'), $border, 'L');
			
			$y_point = 68;
			$pdf->SetXY( 20, $y_point );			
			$pdf->MultiCell(72, 6, U2T('เรียน นายโชติอนันต์  พูลทรัพย์ (007229)'), $border, 'L');
			
			$y_point = 80;
			$pdf->SetXY( 20, $y_point );			
			$pdf->MultiCell(72, 6, U2T('อ้างถึง สัญญาเงินกู้สามัญเลขที่ 205701467'), $border, 'L');
			
			$detail1 ='';
			$detail1 .= '        ตามหนังสือที่อ้างอิงถึง ท่านได้กู้เงินสหกรณ์ออมทรัพย์ครูสมุทรปราการ จำกัด ประเภทเงินกู้สามัญจำนวนเงิน 2,630,000.00 บาท (สองล้านหกแสนสามหมื่นบาทถ้วน) ';
			$detail1 .='โดยได้ตกลงชำระหนี้เงินกู้สามัญเป็นระยะเวลา 252 งวดๆละ 18,800.00 บาท  ท่านได้ถือหุ้นเดือนละ 2,000.00 บาท  และท่านได้รับเงินจำนวนดังกล่าวครบถ้วนแล้ว ดังความที่แจ้งอยู่แล้วนั้น';			
			$detail1 .="\n";			
			$detail1 .= '        เนื่องจากการชำระหนี้ในเดือน เมษายน 2561 ซึ่งครบกำหนดชำระหนี้ในวันที่ 30 เมษายน 2561 เป็นเงินจำนวน 20,800.00 บาท (สองหมื่นแปดร้อยบาทถ้วน) ';
			$detail1 .= 'ปรากฏว่าท่านยังไม่ได้ชำระหนี้ดังกล่าวให้แก่สหกรณ์ฯอันเป็นการกระทำที่ผิดสัญญาและทำให้สหกรณ์ฯได้รับความเสียหาย';
			$detail1 .="\n";
			$detail1 .= '         ฉะนั้น ข้าพเจ้าในฐานะผู้รับมอบอำนาจจึงขอให้ท่านนำเงินดังกล่าว พร้อมดอกเบี้ยมาชำระให้แก่สหกรณ์ฯภายใน 20 วัน นับตั้งแต่ได้รับหนังสือหรือถือว่าได้รับหนังสือฉบับนี้ ';
			$detail1 .= 'หากพ้นกำหนดดังกล่าว สหกรณ์ฯมีความจำเป็นต้องดำเนินการตามขั้นตอนของกฏหมายและเสนอให้คณะกรรมการดำเนินการพิจารณาท่านพ้นสมาชิกภาพตามข้อบังคับต่อไป';
			$y_point = 92;
			$pdf->SetXY( 20, $y_point );			
			$pdf->MultiCell(175, 6, U2T(@$detail1), $border, 'L');
			
			$y_point = 158;
			$pdf->SetXY( 40, $y_point );			
			$pdf->MultiCell(155, 6, U2T("จึงเรียนมาเพื่อทราบและดำเนินการต่อไป"), $border, 'L');
			
			$y_point = 176;
			$pdf->SetXY( 20, $y_point );			
			$pdf->MultiCell(175, 6, U2T("ขอแสดงความนับถือ"), $border, 'C');
			
			$y_point = 194;
			$pdf->SetXY( 20, $y_point );			
			$pdf->MultiCell(175, 6, U2T("(นางสาวสุนิษา ยามี)"), $border, 'C');
			
			$y_point = 200;
			$pdf->SetXY( 20, $y_point );			
			$pdf->MultiCell(175, 6, U2T("ผู้จัดการ"), $border, 'C');
			
			$y_point = 206;
			$pdf->SetXY( 20, $y_point );			
			$pdf->MultiCell(175, 6, U2T(@$row_profile['coop_name_th']), $border, 'C');
			
			$y_point = 224;
			$pdf->SetXY( 20, $y_point );			
			$pdf->MultiCell(175, 6, U2T("ฝ่ายสินเชื่อ"), $border, 'L');
			
			$y_point = 230;
			$pdf->SetXY( 20, $y_point );			
			$pdf->MultiCell(175, 6, U2T("โทร. 02-3842493-4 ต่อ 24"), $border, 'L');
			
			$y_point = 236;
			$pdf->SetXY( 20, $y_point );			
			$pdf->MultiCell(175, 6, U2T("โทรสาร. 02-3842495"), $border, 'L');
			
			$pdf->SetFont('THSarabunNew', '', 11 );
			$y_point = 242;
			$pdf->SetXY( 20, $y_point );			
			$pdf->MultiCell(175, 6, U2T("(หากการชำระเงินของท่าน สวนทางกับหนังสือฉบับนี้ ต้องกราบขออภัยเป็นอย่างสูง กรุณาแจ้งหรือส่งหลักฐานการชำระเงินกลับในกรณีที่ได้ชำระเงินแล้ว)"), $border, 'L');
						
	$pdf->Output();