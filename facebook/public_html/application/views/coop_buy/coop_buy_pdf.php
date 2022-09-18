<?php
	function U2T($text) { return @iconv("UTF-8", "TIS-620//IGNORE", ($text)); }
	function num_format($text) {
		if($text!=''){
			return number_format($text,2);
		}else{
			return '';
		}
	}
	function cal_age($birthday,$type = 'y'){//รูปแบบการเก็บค่าข้อมูลวันเกิด
		$birthday = date("Y-m-d",strtotime($birthday)); 
		$today = date("Y-m-d");//จุดต้องเปลี่ยน
		list($byear, $bmonth, $bday)= explode("-",$birthday);//จุดต้องเปลี่ยน
		list($tyear, $tmonth, $tday)= explode("-",$today);//จุดต้องเปลี่ยน
		$mbirthday = mktime(0, 0, 0, $bmonth, $bday, $byear);
		$mnow = mktime(0, 0, 0, $tmonth, $tday, $tyear );
		$mage = ($mnow - $mbirthday);
		$u_y=date("Y", $mage)-1970;
		$u_m=date("m",$mage)-1;
		$u_d=date("d",$mage)-1;
		if($type=='y'){
			return $u_y;
		}else if($type=='m'){
			return $u_m;
		}else{
			return $u_d;
		}
	}

//	$pdf = new FPDI('L','mm', array(140,227));
    $pdf = new FPDF('L','mm','A5');
	$x = 10;
	$full_w = 187;
	for($p = 1; $p < 2; $p++) {
		$pdf->addPage();

		$pdf->AddFont('THSarabunNew', '', 'THSarabunNew.php');
		$pdf->AddFont('THSarabunNew-Bold', '', 'THSarabunNew-Bold.php');
		$pdf->SetFont('THSarabunNew-Bold', '', 13 );

		$border = 0;
		$pdf->SetTextColor(0, 0, 0);
		$pdf->SetAutoPageBreak(true,0);

		$pay_type = array('cash'=>'เงินสด', 'cheque'=>'เช็คธนาคาร', 'transfer'=>'เงินโอน');
		$y_point = 15.5;
		// $pdf->SetXY( 10, $y_point);
		// $pdf->MultiCell($full_w, 5, U2T($row['account_buy_number']), $border, 'R');

		$y_point = 12;
		$pdf->SetFont('THSarabunNew-Bold', '', 16 );
		$y_point += 8;
		$pdf->SetXY(10, $y_point);
		if($row["cashpay_type"] == "receipt") {
			$pdf->MultiCell(190, 8, U2T("ใบเสร็จรับเงิน"), 0, 'C');
		} else {
			$pdf->MultiCell(190, 8, U2T("ใบสำคัญจ่ายเงิน"), 0, 'C');
		}

		$pdf->SetFont('THSarabunNew-Bold', '', 13 );
		$y_point += 8;
		$pdf->SetXY( 10, $y_point);

		$y_point += 5;
		$pdf->SetXY( 10, $y_point);
		if($row["cashpay_type"] == "receipt") {
			$pdf->MultiCell(90, 5, U2T('รับเงินจาก : '.$row['pay_for']), $border, 1);
		} else {
			$pdf->MultiCell(90, 5, U2T('จ่ายให้ : '.$row['pay_for']), $border, 1);
		}

		if($row["cashpay_type"] == "receipt") {
			$pdf->SetXY( 10, $y_point - 5);
			$pdf->MultiCell($full_w, 5, U2T("วันที่ ".$this->center_function->ConvertToThaiDate($row['buy_date'],0,0)), $border, 'R');
			$pdf->SetXY( 10, $y_point);
			$pdf->MultiCell($full_w, 5, U2T("เลขที่ใบเสร็จ ".$row['account_buy_number']), $border, 'R');
		} else {
			$pdf->SetXY( 10, $y_point);
			$pdf->MultiCell($full_w, 5, U2T("วันที่ ".$this->center_function->ConvertToThaiDate($row['buy_date'],0,0)), $border, 'R');
		}

		$y_point += 5;
		// $a_x = 10;
		$b_x = 10;
		$c_x = 140;
		$d_x = 160;

		// $a_w = 20;
		$b_w = 150;
		$c_w = 20;
		$d_w = 37;

		$y_point += 2;
		// $pdf->SetXY($a_x, $y_point);
		// $pdf->MultiCell($a_w, 8, U2T("ลำดับที่"), 1, "C");
		$pdf->SetXY($b_x, $y_point);
		$pdf->MultiCell($b_w, 8, U2T("รายการ"), "TB", "C");
		$pdf->SetXY($d_x, $y_point);
		$pdf->MultiCell($d_w, 8, U2T("จำนวน(บาท)"), "TB", "C");
		$y_point += 8;
		$table_start_point = $y_point;
		for($i = 0; $i <= 3; $i++) {
			// $pdf->SetXY($a_x, $y_point);
			// $pdf->MultiCell($a_w, 8, U2T(""), 1, 1);
			$pdf->SetXY($b_x, $y_point);
			$pdf->MultiCell($b_w, 8, U2T(""), 0, 1);
			$pdf->SetXY($d_x, $y_point);
			$pdf->MultiCell($d_w, 8, U2T(""), 0, 1);
			$y_point += 8;
		}

		$index = 1;
		foreach($rs_detail as $key => $row_detail){
			// $pdf->SetXY($a_x, $table_start_point);
			// $pdf->MultiCell($a_w, 8, $index++, 1, 'C');
			$pdf->SetXY($b_x, $table_start_point);
			$pdf->MultiCell($b_w, 8, U2T($row_detail['pay_description']), 0, 'L');
			$pdf->SetXY($d_x, $table_start_point);
			$pdf->MultiCell($d_w, 8, number_format($row_detail['pay_amount'],2), 0, 'R');
			$table_start_point += 8;
		}

        $y_point += 20;
		$pdf->SetXY($b_x, $y_point);
		$pdf->MultiCell($b_w, 8, U2T($this->center_function->convert($row['total_amount'])), "T", "C");
		$pdf->SetXY($c_x, $y_point);
		$pdf->MultiCell($c_w, 8, U2T("รวม"), "T", "C");
		$total_amount = number_format($row['total_amount'],2);
		$pdf->SetXY($d_x, $y_point);
		$pdf->MultiCell($d_w, 8, U2T($total_amount.' บาท'), "T", "R");

		$y_point += 14;
		$pdf->SetXY(10, $y_point);
		$pdf->MultiCell(95, 8, U2T("ลงชื่อ..........................................................................ผู้จัดการ"), 0, "C");
		$pdf->SetXY(110, $y_point);
		$pdf->MultiCell(90, 8, U2T("ลงชื่อ..........................................................................ผู้รับเงิน"), 0, "C");

		if(!empty($signature['signature_3'])) {
            if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/assets/images/coop_signature/' . $signature['signature_3'])) {
                $pdf->Image($_SERVER['DOCUMENT_ROOT'] . '/assets/images/coop_signature/' . $signature['signature_3'], 45, $y_point - 8, 20, '', '', '');
            }
        }
		// $pdf->Image(base_url().PROJECTPATH.'/assets/images/coop_signature/'.$signature['signature_2'],150,117,25,'','','');

		$pdf->SetFont('THSarabunNew-Bold', '', 13 );
		$pdf->SetTextColor(0, 0, 0);
		$y_point += 8;
		$pdf->SetXY(10, $y_point);
		$pdf->MultiCell(95, 8, U2T("(".$signature['manager_name'].")"), 0, "C");
		$pdf->SetXY(110, $y_point);
		$pdf->MultiCell(90, 8, U2T("(                                                                  )"), 0, "C");

		$y_point += 0;
		$pdf->SetFont('THSarabunNew','',18);
		$pdf->SetTextColor(0, 0, 204);
		$pdf->SetXY(0, $y_point+5);
		if ($row['pay_type'] == "cash"){
            $pdf->MultiCell(210, 10, U2T("เงินสด"), 0, "C",0);
        }else{
			$account_bank_name = "";
			if($row['account_bank'] != "Other") {
				$account_bank_name = str_replace('เงินฝาก',"ฝาก",$row['account_bank_name']);
				$account_bank_name = str_replace(' ออมทรัพย์',"",$account_bank_name);
			}
            $pdf->MultiCell(210, 10, U2T("รายการโอน".$account_bank_name), 0, "C",0);
        }
	}

	$pdf->Output();
