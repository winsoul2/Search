	<?php
	//define('FPDF_FONTPATH', base_url("fpdf/font/"));
	//echo base_url("fpdf/1.8.1/fpdf.php");exit;
	//include base_url("fpdf/1.8.1/fpdf.php");
	
    function GETVAR($key, $default = null, $prefix = null, $suffix = null) {
        return isset($_GET[$key]) ? $prefix . $_GET[$key] . $suffix : $prefix . $default . $suffix;
    }

    function DateTimeDiff($strDateTime1,$strDateTime2)
    {
        return (strtotime($strDateTime2) - strtotime($strDateTime1))/  ( 60 * 60 ); // 1 Hour =  60*60
    }
	
	$mShort = array(1=>"ม.ค.","ก.พ.","มี.ค.","เม.ย.","พ.ค.","มิ.ย.","ก.ค.","ส.ค.","ก.ย.","ต.ค.","พ.ย.","ธ.ค.");
	$str = "" ;
	//$datetime = date("Y-m-d H:i:s");
	$datetime = $share_date;
		
	$tmp = explode(" ",$datetime);
	if( $tmp[0] != "0000-00-00" ) {
		$d = explode( "-" , $tmp[0]);
		$month = array() ;
		
		$month = $mShort ;
		
		$str = $d[2] . " " . $month[(int)$d[1]].  " ".($d[0]>2500?$d[0]:$d[0]+543);
		
		// $t = strtotime($datetime);
		// $str  = $str. " ".date("H:i" , $t ) . " น." ;	
	}
	
	function U2T($text) { return @iconv("UTF-8", "TIS-620//IGNORE", trim($text)); }
	
    $font = GETVAR('font','fontawesome-webfont1','','.php');
    
	//$pdf = new FPDF('L','mm',array(228.60,139.7));
	//$pdf = new FPDF('P','mm',array(210,148.5));
	$pdf = new FPDF('L','mm','A5');		
	//$part = 0;
	for($part=0;$part<1;$part++){
        $pdf->SetAutoPageBreak('true',0);
		$pdf->AddPage();
		$pdf->AddFont('H','','angsa.php');
		$pdf->AddFont('FA','',$font);
		$pdf->AddFont('THSarabunNew','','THSarabunNew.php');
		$pdf->AddFont('THSarabunNewB','','THSarabunNew-Bold.php');

		$y = 0;
		$y2 = 0;
		$y3 = 0;	
		
		$pdf->SetFont('THSarabunNew','',14);		
		$pdf->Text( 162 , 14+$y+12 , U2T("วันที่"),'R');
		$pdf->Text( 172 , 14+$y+12 , U2T("$str"));
		$pdf->Text( 152 , 21+$y+12 , U2T("เลขที่ใบเสร็จ "),'R');
		$pdf->Text( 172 , 21+$y+12 , U2T(@$receipt_id));
        $pdf->Text( 154 , 28+$y+12 , U2T("รหัสสมาชิก"),'R');
        $pdf->Text( 172 , 28+$y+12 , U2T($member_id));
		
		$pdf->SetFont('THSarabunNewB','',20);
		$pdf->Text( 95,28+$y,U2T("ใบเสร็จรับเงิน"),0,1,'C');
		$line = "______________________________________________________________________________________________________________";
		$pdf->SetFont('THSarabunNew','',14);
		$pdf->Text( 10 , 33+$y , U2T("ได้รับเงินจาก ")." ".U2T($prename_full.$name));
		$pdf->Text( 10 , 40+$y , U2T("สังกัด")." ".U2T(@$mem_group_name));
		$pdf->Text( 10,45+$y, U2T("$line"));
		$pdf->Cell(0, 38+$y2, U2T(""),0,1,'C');
		$pdf->Cell(70, 5, U2T("รายการชำระ"),0,0,'C');
		$pdf->Cell(25, 5, U2T("งวดที่"),0,0,'C');
		$pdf->Cell(25, 5, U2T("เงินต้น"),0,0,'C');
		$pdf->Cell(25, 5, U2T("ดอกเบี้ย"),0,0,'C');
		$pdf->Cell(25, 5, U2T("จำนวนเงิน"),0,0,'C');
		$pdf->Cell(25, 5, U2T("คงเหลือ"),0,1,'C');	
		$pdf->Cell(0, 0, U2T("$line"),0,1,'C');
		$pdf->Cell(0, 1, U2T("$line"),0,1,'C');
		$pdf->Cell(0, 3, U2T(""),0,1,'C');

		$i = 0;
		$sum = 0;

			if(@$period == 1) {
				$save = "ค่าหุ้นแรกเข้า";
			}else{
                if(DateTimeDiff("2021-05-13 17:00:00",$share_date) > 0 )
                {
                    $save = "ซื้อหุ้นครั้งเดียว จำนวนหุ้น " . number_format($num_share, 0) . " หุ้น";
                }else{
                    $save = "ซื้อหุ้นเพิ่มพิเศษ จำนวนหุ้น " . number_format($num_share, 0) . " หุ้น";
				}
			}
				$count = $value;

			
			$pdf->Cell(85, 5, U2T($save),0,0,'L');//8
			$pdf->Cell(25, 5, U2T(""),0,0,'C');
			$pdf->Cell(25, 5, U2T(""),0,0,'C');
			$pdf->Cell(25, 5, U2T(""),0,0,'C');
			$pdf->Cell(25, 5, U2T(number_format($count,2)),0,0,'R');
			$pdf->Cell(25, 5, U2T(""),0,1,'C');	
			//$pdf->Text(15,$i, U2T($save));
			//$pdf->Text(175,$i, U2T($count));		
			$sum = $sum + $count;		
			$i++;		
		//}
		$num = 60-(($i*5)+10);
		$pdf->Cell(0, $num, U2T(""),0,1,'C');
		//$use = 135;
		$pdf->Text(10,102, U2T("$line"));
		$pdf->Cell(135, 7, U2T($this->center_function->convert($sum)),1,0,'C');
		$pdf->Cell(25, 7, U2T("รวมเงิน"),0,0,'C');
		$pdf->Cell(25, 7, U2T(number_format($sum,2)),0,0,'R');
		$pdf->Cell(25, 7, U2T(" บาท"),0,1,'L');


		$pdf->Text(15, 124+$y+5, U2T("ลงชื่อ........................................................ผู้จัดการ"));
		$pdf->Text(130, 124+$y+5, U2T("ลงชื่อ........................................................ผู้รับเงิน"));
		$pdf->SetXY(25,128+$y+5 );
		$pdf->Cell(45,0, U2T("( ".$signature['manager_name']." )"),0,0,'C');

		$pdf->SetXY( 130,128+$y3+5 );
		$pdf->Cell(61,0,  U2T("(                                          )"),0,0,'C');
		
		$pdf->SetFont('THSarabunNew','',20);
        if(file_exists($_SERVER['DOCUMENT_ROOT'].'/assets/images/coop_signature/'.$signature['signature_3'])) {
            $pdf->Image($_SERVER['DOCUMENT_ROOT'] . '/assets/images/coop_signature/' . $signature['signature_3'], 38, 119+$y, 15, '', '', '');
        }
        $pdf->SetTextColor(0, 0, 204);
        if($pay_type == 3){
            $pdf->SetXY(75,132 );
            $pdf->Cell(55, 0, U2T(@$other),0,1,'C');
        }else{
            $pdf->Text(95, 129, U2T(@$pay_type_text));
            $pdf->SetXY(75,132 );
            $pdf->Cell(55, 0, U2T(@$bank_name),0,1,'C');
        }
//        $pdf->SetXY(80,132 );
//        $pdf->Cell(55, 0, U2T(@$bank_name),0,1,'C');
        $pdf->SetTextColor(0, 0, 0);
		if($part == 1){
			$pdf->Text( 180 , 136+5 , U2T("สำเนา"),'R');
		}
	}	
	
    
	$pdf->Output();

	if ( $is_downloan ) {
		$pdf->Output("{$member_id}{$receipt_id}.pdf", "D");
	} else {
		$pdf->Output();
	}

?>
