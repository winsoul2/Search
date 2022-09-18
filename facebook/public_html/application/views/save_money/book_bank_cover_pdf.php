<?php
function U2T($text) { return @iconv("UTF-8", "TIS-620//IGNORE", ($text)); }
function num_format($text) {
	if($text!=''){
		return number_format($text,2);
	}else{
		return '';
	}
}
function cal_age($birthday,$type = 'y'){     //รูปแบบการเก็บค่าข้อมูลวันเกิด
	$birthday = date("Y-m-d",strtotime($birthday));
	$today = date("Y-m-d");   //จุดต้องเปลี่ยน
	list($byear, $bmonth, $bday)= explode("-",$birthday);       //จุดต้องเปลี่ยน
	list($tyear, $tmonth, $tday)= explode("-",$today);                //จุดต้องเปลี่ยน
	$mbirthday = mktime(0, 0, 0, $bmonth, $bday, $byear);
	$mnow = mktime(0, 0, 0, $tmonth, $tday, $tyear );
	$mage = ($mnow - $mbirthday);
	//echo "วันเกิด $birthday"."<br>\n";
	//echo "วันที่ปัจจุบัน $today"."<br>\n";
	//echo "รับค่า $mage"."<br>\n";
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

$pdf = new FPDI('P','mm', array(180,155));
$pdf->AddPage();

$pdf->AddFont('THSarabunNew', '', 'THSarabunNew.php');
$pdf->SetFont('THSarabunNew', '', 13 );
$pdf->SetMargins(0, 0, 0);
$border = 0;
$pdf->SetTextColor(0, 0, 0);
$pdf->SetAutoPageBreak(false);

$y_point = 119;
$pdf->SetXY( 40, $y_point );
$pdf->MultiCell(110, 5, U2T($row['account_name']), $border, 1);

$y_point = 129;
$pdf->SetXY( 44, $y_point );
$pdf->MultiCell(65, 5, U2T($this->center_function->format_account_number($account_id)), $border, 1);
$pdf->SetXY( 117, $y_point );
$pdf->MultiCell(35, 5, U2T($row['book_number']), $border, 1);

$y_point = 140;
$pdf->SetXY( 60, $y_point );
$pdf->MultiCell(45, 5, U2T($row['mem_id']), $border, 1);
$pdf->SetXY( 117, $y_point );
$pdf->MultiCell(35, 5, U2T($row_gname['mem_group_name']), $border, 1);

$y_point = 151;
$pdf->SetXY( 52, $y_point );
$pdf->MultiCell(60, 5, U2T(date("d")."/".date("m")."/".(date("Y") +543)), $border, 1);

$pdf->Output();