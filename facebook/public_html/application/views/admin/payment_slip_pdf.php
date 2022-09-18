	<?php
	//define('FPDF_FONTPATH', base_url("fpdf/font/"));
	//echo base_url("fpdf/1.8.1/fpdf.php");exit;
	//include base_url("fpdf/1.8.1/fpdf.php");
	
    function GETVAR($key, $default = null, $prefix = null, $suffix = null) {
        return isset($_GET[$key]) ? $prefix . $_GET[$key] . $suffix : $prefix . $default . $suffix;
    }
	
	$mShort = array(1=>"ม.ค.","ก.พ.","มี.ค.","เม.ย.","พ.ค.","มิ.ย.","ก.ค.","ส.ค.","ก.ย.","ต.ค.","พ.ย.","ธ.ค.");
	$str = "" ;
	$datetime = date("Y-m-d H:i:s");
		
	$tmp = explode(" ",$datetime);
	if( $tmp[0] != "0000-00-00" ) {
		$d = explode( "-" , $tmp[0]);
		$month = array() ;
		
		$month = $mShort ;
		
		$str = $d[2] . " " . $month[(int)$d[1]].  " ".($d[0]>2500?$d[0]:$d[0]+543);
		
		$t = strtotime($datetime);
		$str  = $str. " ".date("H:i" , $t ) . " น." ;	
	}
	
	function U2T($text) { return @iconv("UTF-8", "TIS-620//IGNORE", trim($text)); }
    $font = GETVAR('font','fontawesome-webfont1','','.php');
    
	//$pdf = new FPDF('L','mm',array(228.60,139.7));
	//$pdf = new FPDF('P','mm',array(210,148.5));
	//$pdf = new FPDF('P','mm','A4');
	$pdf = new FPDF('L','mm','A5');	
	//$part = 0;
	for($part=0;$part<2;$part++){
		$pdf->AddPage();
		$pdf->AddFont('H','','angsa.php');
		$pdf->AddFont('FA','',$font);
		$pdf->AddFont('THSarabunNew','','THSarabunNew.php');
		$pdf->AddFont('THSarabunNewB','','THSarabunNew-Bold.php');

		$y = 0;
		$y2 = 0;
		$y3 = 0;
		
		$pdf->SetFont('THSarabunNew','',14);		

		$pdf->Text( 162 , 14+$y , U2T("วันที่"),'R');
		$pdf->Text( 172 , 14+$y , U2T($this->center_function->mydate2date($row_receipt['receipt_datetime'])));
		$pdf->Text( 152 , 21+$y , U2T("เลขที่ใบเสร็จ "),'R');
		$pdf->Text( 172 , 21+$y , U2T(@$row_receipt['receipt_id']));
		
		$pdf->SetFont('THSarabunNewB','',20);
		if($account_list == "12") {
			$pdf->Text( 95,28+$y,U2T("ใบสำคัญจ่าย"),0,1,'C');
		} else {
			$pdf->Text( 95,28+$y,U2T("ใบเสร็จรับเงิน"),0,1,'C');
		}
		$line = "______________________________________________________________________________________________________________";
		$pdf->SetFont('THSarabunNew','',14);
		$pdf->Text( 10 , 33+$y , U2T("ผู้รับเงิน  ")." ".U2T($prename_full.$name));
		$pdf->Text( 154 , 33+$y , U2T("รหัสสมาชิก"),'R');
		$pdf->Text( 172 , 33+$y , U2T($member_id));
		$pdf->Text( 10 , 40+$y , U2T("สังกัด")." ".U2T(@$member_data['mem_group_name']));
	
		/*if(@$pay_for_loan['contract_number']!=''){
			$pdf->Text( 80 , 40+$y , U2T("สัญญาที่ชำระ")." ".U2T(@$pay_for_loan['contract_number']));
		}*/
		

		$pdf->Text( 10,45+$y, U2T($line));
		$pdf->Cell(0, 38+$y2, U2T(""),0,1,'C');
		$pdf->Cell(65, 5, U2T("รายการชำระ"),0,0,'C');
		$pdf->Cell(10, 5, U2T("งวดที่"),0,0,'C');
		$pdf->Cell(23, 5, U2T("เงินต้น"),0,0,'C');
		$pdf->Cell(23, 5, U2T("ดอกเบี้ย"),0,0,'C');
		$pdf->Cell(23, 5, U2T("ดอกคงค้าง"),0,0,'C');
		$pdf->Cell(23, 5, U2T("จำนวนเงิน"),0,0,'C');
		$pdf->Cell(23, 5, U2T("คงเหลือ"),0,1,'C');	
		$pdf->Cell(0, 0, U2T($line),0,1,'C');
		$pdf->Cell(0, 1, U2T($line),0,1,'C');
		$pdf->Cell(0, 3, U2T(""),0,1,'C');

		$i = 0;
		$sum = 0;
			foreach($transaction_data as $key => $value){
				$transaction_text = !empty($value['transaction_text_main']) ? $value['transaction_text_main'] : $value['transaction_text'];
				$total_amount = $value['principal_payment'] + $value['interest'] + $value['loan_interest_remain'];
				$pdf->Cell(65, 5, U2T($transaction_text),0,0,'L');//8
				$pdf->Cell(10, 5, U2T(($value['period_count'] == '')?'':number_format($value['period_count'],0)),0,0,'R');
				$pdf->Cell(23, 5, U2T(number_format($value['principal_payment'],2)),0,0,'R');
				$pdf->Cell(23, 5, U2T(number_format($value['interest'],2)),0,0,'R');
				$pdf->Cell(23, 5, U2T(number_format($value['loan_interest_remain'],2)),0,0,'R');
				$pdf->Cell(23, 5, U2T(number_format($total_amount,2)),0,0,'R');
				$pdf->Cell(23, 5, U2T(number_format($value['loan_amount_balance'],2)),0,1,'R');	
				//$pdf->Text(15,$i, U2T($save));
				//$pdf->Text(175,$i, U2T($count));		
				$sum = $sum + $total_amount;		
				$i++;		
			}
		//}
		$num = 60-(($i*5)+7);
		$pdf->Cell(0, $num, U2T(""),0,1,'C');
		//$use = 135;
		$sum_convert = number_format($sum,2);
		$pdf->Text(10,100, U2T("$line"));
		$pdf->Cell(135, 7, U2T($this->center_function->convert($sum_convert)),1,0,'C');
		$pdf->Cell(25, 7, U2T("รวมเงิน"),0,0,'C');
		$pdf->Cell(25, 7, U2T(number_format($sum,2)),0,0,'R');
		$pdf->Cell(25, 7, U2T(" บาท"),0,1,'L');
		
		$pdf->Text(30, 124+$y, U2T("ลงชื่อ........................................................ผู้ทำรายการ"));
		$pdf->Text(130, 124+$y, U2T("ลงชื่อ........................................................ผู้รับเงิน"));
		$pdf->SetXY(25,128+$y );
		$pdf->Cell(70,0, U2T("( ".$row_receipt["user_name"]." )"),0,0,'C');
		
		$pdf->SetXY( 130,128+$y3 );
		$pdf->Cell(61,0,  U2T("( ".$prename_full.$name." )"),0,0,'C');
		
		$pdf->SetFont('THSarabunNew','',12);
		$pdf->Text(180, 132+$y, U2T("หมายเหตุ : ".@$pay_type));
		
		if($part == 1){
			$pdf->Text( 180 , 136 , U2T("สำเนา"),'R');	
		}
	}	
	

	$pdf->Output();
?>
