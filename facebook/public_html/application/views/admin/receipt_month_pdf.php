<?php
function U2T($text) { return @iconv("UTF-8", "TIS-620//IGNORE", ($text)); }
	function num_format($text) { 
		if($text!=''){
			return number_format($text,2);
		}else{
			return '';
		}
	}
	function cal_age($birthday,$date_now,$type = 'y'){     //รูปแบบการเก็บค่าข้อมูลวันเกิด
		$birthday = date("Y-m-d",strtotime($birthday)); 
		$today = date("Y-m-d");   //จุดต้องเปลี่ยน
		list($byear, $bmonth, $bday)= explode("-",$birthday);       //จุดต้องเปลี่ยน
		list($tyear, $tmonth, $tday)= explode("-",$today);                //จุดต้องเปลี่ยน
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
	
	
	$pdf = new FPDI('L','mm', array(140,203));
	$month = @$_GET['month'];
	$year = @$_GET['year'];
    foreach($data as $key => $row){
		//echo $row['receipt_id']; exit;
		if(@$row['receipt_id'] != ''){
			$pdf->AddPage();
			$pdf->AddFont('THSarabunNew', '', 'THSarabunNew.php');
			$pdf->SetFont('THSarabunNew', '', 13 );
			$pdf->SetMargins(0, 0, 0);
			$border = 0;
			$pdf->SetTextColor(0, 0, 0);
			$pdf->SetAutoPageBreak(true,0);
				$y_point = 16;
				$pdf->SetXY( 172, $y_point );
				$pdf->MultiCell(30, 5, U2T(@$row['receipt_id']), $border, 1);
				$y_point = 23;
				$pdf->SetXY( 172, $y_point );
				$pdf->MultiCell(30, 5, U2T($this->center_function->mydate2date($row['receipt_datetime'])), $border, 1);
				$y_point = 31;
				$pdf->SetXY( 26, $y_point );
				$pdf->MultiCell(100, 5, U2T(@$row['firstname_th']." ".@$row['lastname_th']), $border, 1);
				$pdf->SetXY( 125, $y_point );
				$pdf->MultiCell(30, 5, U2T(@$row['member_id']), $border, 1);
				$pdf->SetXY( 172, $y_point );
				$pdf->MultiCell(30, 5, U2T(@$row['employee_id']), $border, 1);
				$y_point = 38;
				$pdf->SetXY( 26, $y_point );
				$pdf->MultiCell(100, 5, U2T(@$mem_group_arr[@$row['level']]), $border, 1);
				$pdf->SetXY( 125, $y_point );
				$pdf->MultiCell(100, 5, U2T(@$mem_group_arr[@$row['faction']]), $border, 1);
				
				$y_point = 46;
				$sum = 0;
				$this->db->select(array('t1.*','t2.account_list','t4.loan_type'));
				$this->db->from('coop_finance_transaction as t1');
				$this->db->join('coop_account_list as t2', "t1.account_list_id = t2.account_id", 'left');
				$this->db->join('coop_loan as t3', "t1.loan_id = t3.id", 'left');
				$this->db->join('coop_loan_type as t4', "t3.loan_type = t4.id", 'left');
				$this->db->where("t1.receipt_id = '".$row['receipt_id']."'");
				$rs_receipt = $this->db->get()->result_array();
				
				foreach($rs_receipt as $key2 => $row_receipt){
				$y_point += 7;
					$pdf->SetXY( 7, $y_point );
					$pdf->MultiCell(70, 5, U2T($row_receipt['transaction_text']), $border, 1);
					$pdf->SetXY( 77, $y_point );
					$pdf->MultiCell(15, 5, U2T($row_receipt['period_count']), $border, 'C');
					$pdf->SetXY( 90, $y_point );
					$pdf->MultiCell(27, 5, U2T(num_format($row_receipt['principal_payment'])), $border, 'R');
					$pdf->SetXY( 118, $y_point );
					$pdf->MultiCell(26, 5, U2T(num_format($row_receipt['interest'])), $border, 'R');
					$pdf->SetXY( 144, $y_point );
					$pdf->MultiCell(26, 5, U2T(num_format($row_receipt['total_amount'])), $border, 'R');
					$pdf->SetXY( 169, $y_point );
					$pdf->MultiCell(30, 5, U2T(num_format($row_receipt['loan_amount_balance'])), $border, 'R');
					$sum += $row_receipt['total_amount'];
				}
				$y_point += 7;
				$pdf->SetXY( 7, $y_point );
				$pdf->MultiCell(70, 5, U2T(@$pay_type[@$row['pay_type']]), $border, 1);
				
				$y_point = 109;
				$pdf->SetXY( 7, $y_point );
				$pdf->MultiCell(135, 5, U2T($this->center_function->convert(str_replace(',','',num_format($sum)))), $border, 'R');
				$pdf->SetXY( 144, $y_point );
				$pdf->MultiCell(26, 5, U2T(num_format($sum)), $border, 'R');
				
				$pdf->Image(base_url().PROJECTPATH.'/assets/images/coop_signature/'.$signature['signature_1'],25,125,25,'','','');
				$pdf->Image(base_url().PROJECTPATH.'/assets/images/coop_signature/'.$signature['signature_2'],120,125,25,'','','');
		}
	}
	$pdf->Output();