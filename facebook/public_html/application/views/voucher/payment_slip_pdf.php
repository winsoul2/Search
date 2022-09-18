<?php
    function GETVAR($key, $default = null, $prefix = null, $suffix = null) {
        return isset($_GET[$key]) ? $prefix . $_GET[$key] . $suffix : $prefix . $default . $suffix;
    }

	$mShort = array(1=>"ม.ค.","ก.พ.","มี.ค.","เม.ย.","พ.ค.","มิ.ย.","ก.ค.","ส.ค.","ก.ย.","ต.ค.","พ.ย.","ธ.ค.");
	$str = "";
	$datetime = date("Y-m-d H:i:s");

	$tmp = explode(" ",$datetime);
	if( $tmp[0] != "0000-00-00" ) {
		$d = explode( "-" , $tmp[0]);
		$month = array();
		$month = $mShort;
		$str = $d[2] . " " . $month[(int)$d[1]].  " ".($d[0]>2500?$d[0]:$d[0]+543);
		$t = strtotime($datetime);
		$str  = $str. " ".date("H:i" , $t ) . " น.";
	}
	
	function U2T($text) { return @iconv("UTF-8", "TIS-620//IGNORE", trim($text)); }
    $font = GETVAR('font','fontawesome-webfont1','','.php');

	$pdf = new FPDF('L','mm','A5');	
	for($part=0;$part<1;$part++){
		$pdf->AddPage();
		$pdf->AddFont('H','','angsa.php');
		$pdf->AddFont('FA','',$font);
		$pdf->AddFont('THSarabunNew','','THSarabunNew.php');
		$pdf->AddFont('THSarabunNewB','','THSarabunNew-Bold.php');

        $y = 15;
		$y2 = 0;
		$y3 = 0;
		$y4 = 13;
		
		$pdf->SetFont('THSarabunNew','',14);		

        if(!empty($voucher['no'])) {
            $pdf->Text( 162 , 14+$y , U2T("วันที่"),'R');
            $pdf->Text( 172 , 14+$y , U2T($this->center_function->mydate2date($voucher['transaction_time'])));
            $pdf->Text( 152 , 21+$y , U2T("เลขที่ใบเสร็จ "),'R');
            $pdf->Text( 172 , 21+$y , U2T($voucher['no']));
        } else {
            $pdf->Text( 162 , 21+$y , U2T("วันที่"),'R');
            $pdf->Text( 172 , 21+$y , U2T($this->center_function->mydate2date($voucher['transaction_time'])));
        }
		
		$pdf->SetFont('THSarabunNewB','',20);
		$pdf->Text( 95,13+$y,U2T("ใบสำคัญจ่าย"),0,1,'C');

		$line = "_____________________________________________________________________________________________________________";
		$lineTb = "_____________________________________________________________________________________________________________";
		$pdf->SetFont('THSarabunNew','',14);
		$pdf->Text( 10 , 21+$y , U2T("ผู้รับเงิน  ")." ".U2T($member['prename_full'].$member['firstname_th']." ".$member['lastname_th']));
		$pdf->Text( 154-5 , 28+$y , U2T("ทะเบียนสมาชิก"),'R');
		$pdf->Text( 172 , 28+$y , U2T($voucher['member_id']));
		$pdf->Text( 10 , 28+$y , U2T("สังกัด")." ".U2T(@$member['mem_group_name']));

        $border = 0;

		$pdf->Text( 10,29+$y, U2T($line));
		$pdf->Cell(0, 37, U2T(""),0,1,'C');
		$pdf->Cell(65, 5, U2T("รายการชำระ"),0,0,'C');
		$pdf->Cell(10, 5, U2T("งวดที่"),0,0,'C');
		$pdf->Cell(23, 5, U2T("เงินต้น"),0,0,'C');
		$pdf->Cell(23, 5, U2T("ดอกเบี้ย"),0,0,'C');
		$pdf->Cell(23, 5, U2T("ดอกคงค้าง"),0,0,'C');
		$pdf->Cell(23, 5, U2T("จำนวนเงิน"),0,0,'C');
		$pdf->Cell(23, 5, U2T("คงเหลือ"),0,1,'C');
		$pdf->Cell(0, 0, U2T($line),$border,1,'C');
		$pdf->Cell(0, 1, U2T($line),$border,1,'C');
		$pdf->Cell(0, 3, U2T(""),$border,1,'C');

		$i = 0;
        $sum = 0;
        foreach($details as $key => $value){
            $transaction_text = $value['detail'];
            $total_amount = $value['principal'] + $value['interest'];
            $pdf->Cell(65, 5, U2T($transaction_text),0,0,'L');//8
            $pdf->Cell(10, 5, "",0,0,'R');
            $pdf->Cell(23, 5, U2T(number_format($value['principal'],2)),0,0,'R');
            $pdf->Cell(23, 5, U2T(number_format($value['interest'],2)),0,0,'R');
            $pdf->Cell(23, 5, "",0,0,'R');
            $pdf->Cell(23, 5, U2T(number_format($total_amount,2)),0,0,'R');
            $pdf->Cell(23, 5, U2T(number_format($value['balance'],2)),0,1,'R');
            $sum = $sum + $total_amount;
            $i++;
        }

        $num = 60-((($i)*5)+7)+2;
		$pdf->Cell(0, $num, U2T(""),0,1,'C');

		$sum_convert = number_format($sum,2);
		$pdf->Text(10, 110-2, U2T($line));
		$pdf->Cell(135, 7, U2T($this->center_function->convert($sum_convert)),1,0,'C');
		$pdf->Cell(20, 7, U2T("รวมเงิน"),0,0,'C');
		$pdf->Cell(25, 7, U2T(number_format($sum,2)),0,0,'R');
		$pdf->Cell(8, 7, U2T(" บาท"),0,1,'L');
		
		$ab = 17;
		$pdf->Text(15, 104-7+$ab+$y, U2T("ลงชื่อ........................................................ผู้จัดการ"));
		$pdf->Text(30, 109-7+$ab+$y, U2T("(".$signature['manager_name'].")"));
		$pdf->Text(130, 104-7+$ab+$y, U2T("ลงชื่อ........................................................ผู้รับเงิน"));
		$pdf->Text(153, 104-8+$ab+$y, U2T("รายการโอน"));
		$pdf->Text(140, 109-7+$ab+$y, U2T("(เข้าบัญชีเงินประกันความเสี่ยง)"));
		$pdf->SetXY(25,130+$y );

		// $pdf->SetFont('THSarabunNew','',20);
		// $pdf->SetTextColor(0, 0, 204);
        // if($voucher['pay_type'] == 3){
        //     $pdf->SetXY(75,132 );
        //     $pdf->Cell(55, 0, U2T($voucher['other']),0,1,'C');
		// }else{
        //     $pdf->Text(95, 104-7+$ab+$y, U2T($pay_type));
        //     $pdf->SetXY(75,132 );
        //     // $pdf->Cell(55, 0, U2T($voucher['bank_name']),0,1,'C');
		// }

		if(file_exists($_SERVER['DOCUMENT_ROOT'].'/assets/images/coop_signature/'.$signature['signature_3'])) {
			$pdf->Image($_SERVER['DOCUMENT_ROOT'] . '/assets/images/coop_signature/' . $signature['signature_3'], 38, 104 - 5 + $ab + $y - 11, 15, '', '', '');
		}
		
		$pdf->SetTextColor(0, 0, 0);
		if($part == 1){
			$pdf->SetFont('THSarabunNew','',12);
			$pdf->SetTextColor(0, 0, 0);
			$pdf->Text( 180 , 136 , U2T("สำเนา"),'R');
		}
	}	

	$pdf->Output();
?>
