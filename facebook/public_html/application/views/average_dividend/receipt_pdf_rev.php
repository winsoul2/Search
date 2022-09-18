	<?php
	//define('FPDF_FONTPATH', base_url("fpdf/font/"));
	//echo base_url("fpdf/1.8.1/fpdf.php");exit;
	//include base_url("fpdf/1.8.1/fpdf.php");
	
//    function GETVAR($key, $default = null, $prefix = null, $suffix = null) {
//        return isset($_GET[$key]) ? $prefix . $_GET[$key] . $suffix : $prefix . $default . $suffix;
//    }

    $receipt = $receipts[0];

	
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
	
	function U2T($text) {
	    //return @iconv("UTF-8", "TIS-620//IGNORE", trim($text));
        return $text;
	}

    function utf8_strlen($s) {

        $c = strlen($s); $l = 0;
        for ($i = 0; $i < $c; ++$i) if ((ord($s[$i]) & 0xC0) != 0x80) ++$l;
        return $l;
    }

    //$font = GETVAR('font','fontawesome-webfont1','','.php');

	$pdf = new tFPDF('P','mm','A5');
	$pdf->SetFillColor(255,255,255);

    $logo = 'assets/images/coop_profile/20180406_0001.png';
    $watermark = 'assets/images/coop_profile/20180406_0001_alpha.png';

    //หัว pdf
    $pdf->AddPage();
    $pdf->AddFont('THSarabunNew','','thsarabunnew-webfont.ttf', 1);
    $pdf->AddFont('THSarabunNew','B','thsarabunnew_bold-webfont.ttf', 1);
    $pdf->SetMargins(0, 0, 0);
    $border = 1;
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetAutoPageBreak(false,0);
    $pdf->Image($watermark, 74-32, 115-32, 64, '', '', '', '', false, -300);

    $_font = 'THSarabunNew';
    //Logo

    $pdf->Image($logo, 10, 5, 20, '', '', '', '', false, 300);


    //Text Color
    $pdf->SetTextColor(71, 115, 57);
    $headerY = 5;
    $subHeaderY = $headerY+6;
    $headerX = 32;

    //Header
    $pdf->SetFont($_font, '', 12);
    $pdf->SetXY($headerX, $headerY);
    $pdf->Cell(100, 10, U2T('สหกรณ์ออมทรัพย์ครูสมุทรปราการ จำกัด'), 0, 0,"L");

    //Sub Header
    $pdf->SetFont($_font, '', 6);
    $pdf->SetXY($headerX, $subHeaderY);
    $pdf->Cell(100, 8, U2T('SAMUTPRAKARN TEACHER SAVING AND CREDIT COOPERATIVE LIMITED.'), 0, "L");

    //Title
    $pdf->SetFont($_font, '', 16);
    $pdf->SetXY(57, $subHeaderY+6);
    $pdf->Cell(148, 12, U2T('ใบเสร็จรับเงิน'), 0, 0);



    //Descripttion
    $descriptY = $subHeaderY + 20;
    $descriptX = 12;
    $pdf->SetFont($_font, '', 9);

        $pdf->SetXY($descriptX, $descriptY);
        $pdf->SetTextColor(71,155,57);
        $pdf->Cell(20, 8, U2T("เลขที่ใบเสร็จ"),0,0,'L');
        $pdf->SetTextColor(0,0,0);
        $pdf->Cell(65, 8, U2T(@$receipt['receipt_id']),0,0,'L');
        $pdf->SetTextColor(71,155,57);
        $pdf->Cell(20, 8, U2T("วันที่"),0,0,'L');
        $pdf->SetTextColor(0,0,0);
        $pdf->Cell(40, 8, U2T($this->center_function->mydate2date($receipt['receipt_datetime'])),0,0,'L');

        $pdf->SetXY($descriptX, $descriptY+=7);
        $pdf->SetTextColor(71,155,57);
        $pdf->Cell(20, 8, U2T("ได้รับเงินจาก"),0,0,'L');
        $pdf->SetTextColor(0,0,0);
        $pdf->Cell(65, 8, U2T($receipt['fullname']),0,0,'L');
        $pdf->SetTextColor(71,155,57);
        $pdf->Cell(20, 8, U2T("รหัสสมาชิก"),0,0,'L');
        $pdf->SetTextColor(0,0,0);
        $pdf->Cell(40, 8, U2T(@$receipt['member_id']),0,0,'L');

        $pdf->SetXY($descriptX, $descriptY+=7);
        $pdf->SetTextColor(71,155,57);
        $pdf->Cell(20, 8, U2T("หน่วยงาน"),0,0,'L');
        $pdf->SetTextColor(0,0,0);
        $pdf->Cell(120, 8, U2T(@$receipt['mem_group_name']),0,0,'L');

        $pdf->SetTextColor(71,155,57);
        $pdf->SetFont('THSarabunNew','',14);

        //รายการ
        $y_point = $descriptY+12;
        $pdf->SetXY( $x = 9, $y_point );
        $pdf->SetFillColor(71, 115, 57);
        $pdf->SetDrawColor(71, 115, 57);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->SetFont($_font, '', 9);
        $pdf->Cell(40, 6, U2T("        รายการชำระ"),1,0,'', 1);
        $pdf->Cell(10, 6, U2T(" งวดที่"),1,0,'', 1);;
        $pdf->Cell(25, 6, U2T("      เงินต้น"),1,0,'', 1);
        $pdf->Cell(15, 6, U2T(" ดอกเบี้ย"),1,0,'', 1);
        $pdf->Cell(25, 6, U2T("      เป็นเงิน"),1,0,'', 1);
        $pdf->Cell(15, 6, U2T(" คงเหลือ"),1,0,'', 1);
        $sum=0;
        $pdf->SetFont($_font, '', 9);
        $pdf->SetTextColor(71, 115, 57);
        //หัว pdf

		$i = 0;
		$sum = 0;
        $interest = 0;
        $boarder = "LR";
        $first = 0;
        $pdf->SetTextColor(0,0,0);
		foreach($receipt['transaction'] as $key => $value){

			$transaction_text = !empty($value['transaction_text_main']) ? $value['transaction_text_main'] : $value['transaction_text'];
			$total_amount = $value['principal_payment'] + $value['interest'] + $value['loan_interest_remain'];
			if($first) {
                $y_point += 8;
            }else{
				$first = 1;
				$y_point +=6;
			}

            $text = [];
			$len = mb_strlen($transaction_text, 'utf-8');

			if($len > 21) {
                $text = explode(' ', $transaction_text);
            }else{
			    $text[] = $transaction_text;
            }

            $pdf->SetXY(9, $y_point);
            $pdf->Cell(40, 8, U2T($text[0]), $boarder, 0, 'L');//8
            $pdf->Cell(10, 8, U2T(($value['period_count'] == '') ? '' : number_format($value['period_count'], 0)), $boarder, 0, 'C');
            $pdf->Cell(25, 8, U2T(number_format($value['principal_payment'], 2)), $boarder, 0, 'R');
            $pdf->Cell(15, 8, U2T(number_format($value['interest'], 2)), $boarder, 0, 'R');
            //$pdf->Cell(20, 8, U2T(number_format($value['loan_interest_remain'],2)),$boarder,0,'R');
            $pdf->Cell(25, 8, U2T(number_format($total_amount, 2)), 0, 0, 'R');
            $pdf->Cell(15, 8, U2T(number_format($value['loan_amount_balance'], 2)), $boarder, 1, 'R');

            $interest += $value['interest'];
            $sum = $sum + $total_amount;
            $i++;

			if(sizeof($text) > 1) {
                $temp = $text;
                array_shift($temp);
			    $line_mx = 21;
			    $chk = mb_strlen(join($temp), 'utf-8') <= $line_mx;
                if($chk){

                    $y_point += 8;

                    $pdf->SetXY(9, $y_point);
                    $pdf->Cell(40, 8, U2T(join(' ',$temp)), $boarder, 0, 'L');//8
                    $pdf->Cell(10, 8, "", $boarder, 0, 'C');
                    $pdf->Cell(25, 8, "", $boarder, 0, 'R');
                    $pdf->Cell(15, 8, "", $boarder, 0, 'R');
                    //$pdf->Cell(20, 8, U2T(number_format($value['loan_interest_remain'],2)),$boarder,0,'R');
                    $pdf->Cell(25, 8, "", 0, 0, 'R');
                    $pdf->Cell(15, 8, "", $boarder, 1, 'R');

                    $i++;

                }else {
                    for ($r = 1; $r < sizeof($text); $r++) {

                        $y_point += 8;

                        $pdf->SetXY(9, $y_point);
                        $pdf->Cell(40, 8, U2T($text[$r]), $boarder, 0, 'L');//8
                        $pdf->Cell(10, 8, "", $boarder, 0, 'C');
                        $pdf->Cell(25, 8, "", $boarder, 0, 'R');
                        $pdf->Cell(15, 8, "", $boarder, 0, 'R');
                        //$pdf->Cell(20, 8, U2T(number_format($value['loan_interest_remain'],2)),$boarder,0,'R');
                        $pdf->Cell(25, 8, "", 0, 0, 'R');
                        $pdf->Cell(15, 8, "", $boarder, 1, 'R');

                        $i++;
                    }
                }
            }
		}

        for($x=0; $x <= (12-$i); $x++){
            $pdf->SetXY(9, $y_point+=8);
            $pdf->Cell(40, 8, U2T(""), $boarder, 0,'R');
            $pdf->Cell(10, 8, U2T(""), $boarder, 0,'R');
            $pdf->Cell(25, 8, U2T(""), $boarder, 0,'R');
            $pdf->Cell(15, 8, U2T(""), $boarder, 0,'R');
            $pdf->Cell(25, 8, U2T(""), $boarder, 0,'R');
            $pdf->Cell(15, 8, U2T(""), $boarder, 0,'R');
        }

        //36 max character
        $sum_convert = number_format($sum,2);
        $y_point += 8;
        $pdf->SetTextColor(254,254,254);
        $pdf->SetXY( 9, $y_point );
        //$pdf->Cell(70, 8,U2T('       '.$this->center_function->convert($sum_convert)),1,0,'', 1);

        //18
        $lens = 36;
        $txt_len = round(mb_strlen($this->center_function->convert($sum_convert), 'utf-8'));
        $sp = ' ' ;
        if($lens > $txt_len) {
            for ($i = 0; $i < ($lens - $txt_len) + 3; $i++) {
                $sp .= ' ';
            }
        }

        $pdf->Cell(75, 8,U2T($sp.$this->center_function->convert($sum_convert)),1,0,'', 1);
        $pdf->Cell(15, 8, "","T",0,'R');
        $pdf->Cell(25, 8, number_format($sum,2),1,0,'C', 1);
        $pdf->Cell(15, 8, "","T",0,'C');

        $pdf->SetTextColor(71,155,57);

        //ลายเซ็น
        $pdf->SetTextColor(71,155,51);
        $pdf->SetFont('THSarabunNew', '', 9 );
        $y_point =190;

        $pdf->SetXY( 20, $y_point+5 );
        $pdf->Cell(35, 8, "", "T");
        $pdf->SetXY( 32, $y_point+5 );
        $pdf->Cell(40, 8, U2T("ผู้จัดการ "), 0, 1, "J");

        $pdf->SetXY( 93, $y_point+5 );
        $pdf->Cell(35, 8, "", "T");
        $pdf->SetXY( 98, $y_point+5 );
        $pdf->Cell(40, 8, U2T("หัวหน้าฝ่ายสินเชื่อ"), 0, 1, "J");

        ///ลายเซ็น
        $pdf->Image(base_url(PROJECTPATH.'/assets/images/coop_signature/'.$signature['signature_3']),26+5,$y_point-5,15,'','','');
        $pdf->Image(base_url(PROJECTPATH.'/assets/images/coop_signature/'.$signature['signature_2']),95+10,$y_point-5,15,'','','');


    if($_GET['is_download'] == 'true') {
        $pdf->Output('file_print.pdf', 'D');

    }else if($_GET['is_image'] == 'true'){

        $_source = rtrim($_SERVER['DOCUMENT_ROOT'], DIRECTORY_SEPARATOR).'/assets/document/'.@$receipt_id_run.'.pdf';
        $_target = '/assets/document/'.@$receipt_id_run.'.pdf';
        $receipt_id = @$receipt_id_run;

        $pdf->Output($_source,'F');
        header('Location: '.base_url('admin/pdf_to_image').'?_target='.$receipt_id);
        exit;

    }else if($_GET['is_base64'] == 'true'){

        $receipt_id_run = @$receipt_id_run;

        $_source = rtrim($_SERVER['DOCUMENT_ROOT'], DIRECTORY_SEPARATOR).'/assets/document/'.@$receipt_id_run.'.pdf';
        $_target = '/assets/document/'.@$receipt_id_run.'.pdf';

        echo '<pre>';
        var_dump($_source, $_target, $receipt_id_run);
        $receipt_id = @$receipt_id_run;
        exit;

        $pdf->Output($_source,'F');

        $_source = rtrim($_SERVER['DOCUMENT_ROOT'], DIRECTORY_SEPARATOR).'/assets/document/'.$receipt_id_run.'.pdf[0]';
        $_target = rtrim($_SERVER['DOCUMENT_ROOT'], DIRECTORY_SEPARATOR).'/assets/images/templete_img/receipt';
        $image = new Imagick($_source);

        $image->setResolution( 150, 150 );
        $image->readImage($_source);
        $num_pages = $image->getNumberImages();
        $image->setImageCompressionQuality(100);

        for ($i = 0; $i < $num_pages; $i++) {
            $image->setIteratorIndex($i);
            $image->setImageFormat('png');
            $image->writeImage($_target . '/' . $receipt_id_run .'_'. $i .'.png');
        }

        $image->clear();
        $image->destroy();

        ob_clean();
        header('content-type: application/json;charset=utf-8');
        header("Access-Control-Allow-Origin: *");
        header('Access-Control-Allow-Headers: X-Requested-With, content-type, access-control-allow-origin, access-control-allow-methods, access-control-allow-headers');
        header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
        echo json_encode(array('data'=> $receipt_id));
        exit;

    }else{
        $pdf->SetTitle('ใบเสร็จรับเงิน', 1);
        $pdf->Output();
    }
?>
