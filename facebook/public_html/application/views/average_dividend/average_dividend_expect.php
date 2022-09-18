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
			$pdf->Image('assets/images/coop_profile/'.@$row_profile['coop_img'],10,10,-700);
				$y_point = 10;
				$pdf->SetXY( 0, $y_point );
				$pdf->SetFont('THSarabunNew', '', 30 );
				$pdf->MultiCell(210, 15, U2T(@$row_profile['coop_name_th']), $border, 'C');
				$y_point = 25;
				$pdf->SetXY( 0, $y_point );
				$pdf->SetFont('THSarabunNew', '', 15 );
				$pdf->MultiCell(210, 5, U2T(@$row_profile['address1']." ".@$row_profile['address2']), $border, 'C');
	
				$pdf->SetFont('THSarabunNew', '', 20 );
				$y_point = 50;
				$pdf->SetXY( 30, $y_point );
				$pdf->MultiCell(30, 5, U2T('กรณีที่ 1'), $border, 1);
				
				$y_point += 10;
				$pdf->SetXY( 60, $y_point );
				$pdf->MultiCell(30, 5, U2T('ปันผล'), $border, 1);
				$pdf->SetXY( 90, $y_point );
				$pdf->MultiCell(15, 5, U2T(@$data_arr[1]['dividend_percent']."%"), $border, 'R');
				$pdf->SetXY( 105, $y_point );
				$pdf->MultiCell(30, 5, U2T('คิดเป็นเงิน'), $border, 1);
				$pdf->SetXY( 130, $y_point );
				$pdf->MultiCell(30, 5, U2T(number_format(@$data_arr[1]['dividend_return'],2)), $border, 'R');
				$pdf->SetXY( 160, $y_point );
				$pdf->MultiCell(15, 5, U2T('บาท'), $border, 1);
				
				$y_point += 10;
				$pdf->SetXY( 60, $y_point );
				$pdf->MultiCell(30, 5, U2T('เฉลี่ยคืน'), $border, 1);
				$pdf->SetXY( 90, $y_point );
				$pdf->MultiCell(15, 5, U2T(@$data_arr[1]['average_percent']."%"), $border, 'R');
				$pdf->SetXY( 105, $y_point );
				$pdf->MultiCell(30, 5, U2T('คิดเป็นเงิน'), $border, 1);
				$pdf->SetXY( 130, $y_point );
				$pdf->MultiCell(30, 5, U2T(number_format(@$data_arr[1]['average_return'],2)), $border, 'R');
				$pdf->SetXY( 160, $y_point );
				$pdf->MultiCell(15, 5, U2T('บาท'), $border, 1);
				
				$y_point += 20;
				$pdf->SetXY( 30, $y_point );
				$pdf->MultiCell(30, 5, U2T('กรณีที่ 2'), $border, 1);
				
				$y_point += 10;
				$pdf->SetXY( 60, $y_point );
				$pdf->MultiCell(30, 5, U2T('ปันผล'), $border, 1);
				$pdf->SetXY( 90, $y_point );
				$pdf->MultiCell(15, 5, U2T(@$data_arr[2]['dividend_percent']."%"), $border, 'R');
				$pdf->SetXY( 105, $y_point );
				$pdf->MultiCell(30, 5, U2T('คิดเป็นเงิน'), $border, 1);
				$pdf->SetXY( 130, $y_point );
				$pdf->MultiCell(30, 5, U2T(number_format(@$data_arr[2]['dividend_return'],2)), $border,'R');
				$pdf->SetXY( 160, $y_point );
				$pdf->MultiCell(15, 5, U2T('บาท'), $border, 1);
				
				$y_point += 10;
				$pdf->SetXY( 60, $y_point );
				$pdf->MultiCell(30, 5, U2T('เฉลี่ยคืน'), $border, 1);
				$pdf->SetXY( 90, $y_point );
				$pdf->MultiCell(15, 5, U2T(@$data_arr[2]['average_percent']."%"), $border, 'R');
				$pdf->SetXY( 105, $y_point );
				$pdf->MultiCell(30, 5, U2T('คิดเป็นเงิน'), $border, 1);
				$pdf->SetXY( 130, $y_point );
				$pdf->MultiCell(30, 5, U2T(number_format(@$data_arr[2]['average_return'],2)), $border, 'R');
				$pdf->SetXY( 160, $y_point );
				$pdf->MultiCell(15, 5, U2T('บาท'), $border, 1);
				
				$y_point += 20;
				$pdf->SetXY( 30, $y_point );
				$pdf->MultiCell(30, 5, U2T('กรณีที่ 3'), $border, 1);
				
				$y_point += 10;
				$pdf->SetXY( 60, $y_point );
				$pdf->MultiCell(30, 5, U2T('ปันผล'), $border, 1);
				$pdf->SetXY( 90, $y_point );
				$pdf->MultiCell(15, 5, U2T(@$data_arr[3]['dividend_percent']."%"), $border, 'R');
				$pdf->SetXY( 105, $y_point );
				$pdf->MultiCell(30, 5, U2T('คิดเป็นเงิน'), $border, 1);
				$pdf->SetXY( 130, $y_point );
				$pdf->MultiCell(30, 5, U2T(number_format(@$data_arr[3]['dividend_return'],2)), $border, 'R');
				$pdf->SetXY( 160, $y_point );
				$pdf->MultiCell(15, 5, U2T('บาท'), $border, 1);
				
				$y_point += 10;
				$pdf->SetXY( 60, $y_point );
				$pdf->MultiCell(30, 5, U2T('เฉลี่ยคืน'), $border, 1);
				$pdf->SetXY( 90, $y_point );
				$pdf->MultiCell(15, 5, U2T(@$data_arr[3]['average_percent']."%"), $border, 'R');
				$pdf->SetXY( 105, $y_point );
				$pdf->MultiCell(30, 5, U2T('คิดเป็นเงิน'), $border, 1);
				$pdf->SetXY( 130, $y_point );
				$pdf->MultiCell(30, 5, U2T(number_format(@$data_arr[3]['average_return'],2)), $border, 'R');
				$pdf->SetXY( 160, $y_point );
				$pdf->MultiCell(15, 5, U2T('บาท'), $border, 1);
				
				$y_point += 20;
				$pdf->SetXY( 0, $y_point );
				$pdf->SetFont('THSarabunNew', '', 15 );
				$pdf->MultiCell(210, 5, U2T('___________________________________________________________________________________'), $border, 'C');
				
				$y_point += 10;
				$pdf->SetXY( 0, $y_point );
				$pdf->SetFont('THSarabunNew', '', 20 );
				$month_arr = array('01'=>'มกราคม', '02'=>'กุมภาพันธ์', '03'=>'มีนาคม', '04'=>'เมษายน', '05'=>'พฤษภาคม', '06'=>'มิถุนายน', '07'=>'กรกฎาคม', '08'=>'สิงหาคม', '09'=>'กันยายน', '10'=>'ตุลาคม', '11'=>'พฤศจิกายน', '12'=>'ธันวาคม');
				$pdf->MultiCell(210, 5, U2T('ข้อมูล ณ '.date('d').' '.$month_arr[date('m')]." ".(date('Y')+543)." เวลา ".date('H:i น.')), $border, 'C');
				
						
	$pdf->Output();