<?php
    function U2T($text) { return @iconv("UTF-8", "TIS-620//IGNORE", ($text)); }
	function num_format($text) {
		if($text!=''){
			return number_format($text,2);
		}else{
			return '0.00';
		}
    }
    function convert($number) {
        $txtnum1 = array('ศูนย์','หนึ่ง','สอง','สาม','สี่','ห้า','หก','เจ็ด','แปด','เก้า','สิบ');
        $txtnum2 = array('','สิบ','ร้อย','พัน','หมื่น','แสน','ล้าน');
        $number = str_replace(",","",$number);
        $number = str_replace(" ","",$number);
        $number = str_replace("บาท","",$number);
        $number = explode(".",$number);
        if(sizeof($number) > 2) {
              return 'ทศนิยมหลายตัวนะจ๊ะ';
              exit;
        }
        $strlen = strlen($number[0]);
        $convert = '';
        for($i=0;$i<$strlen;$i++){
              $n = substr($number[0], $i,1);
              if($n!=0){
                  if($i==($strlen-1) AND $n==1){ $convert .= 'เอ็ด'; }
                  elseif($i==($strlen-2) AND $n==2){ $convert .= 'ยี่'; }
                  elseif($i==($strlen-2) AND $n==1){ $convert .= ''; }
                  else{ $convert .= $txtnum1[$n]; }
                  $convert .= $txtnum2[$strlen-$i-1];
              }
        }
          if(!isset($number[1])) $number[1] = 0;
        $convert .= 'บาท';
        if($number[1]=='0' || $number[1]=='00' || $number[1]==''){
          $convert .= 'ถ้วน';
        }else{
          $strlen = strlen($number[1]);
          for($i=0;$i<$strlen;$i++){
            $n = substr($number[1], $i,1);
            if($n!=0){
              if($number[1] == 01){$convert .= 'หนึ่ง';}
              elseif($i==($strlen-1) AND $n==1 ){$convert .= 'เอ็ด';}
              elseif($i==($strlen-2) AND $n==2){$convert .= 'ยี่';}
              elseif($i==($strlen-2) AND $n==1){$convert .= '';}
              else{ $convert .= $txtnum1[$n];}
              $convert .= $txtnum2[$strlen-$i-1];
            }
          }
          $convert .= 'สตางค์';
        }
        return $convert;
      }

    $pdf = new FPDI('L','mm', array(139.6,204));

    foreach($transaction_data_all as $key => $row){
        // echo 'receipt_id'.$row['receipt_id']; exit;

        if(@$row['receipt_id'] != '' && $key == 1){

            $pdf->AddPage();
			$pdf->AddFont('THSarabunNew', '', 'THSarabunNew.php');
			$pdf->SetFont('THSarabunNew', '', 12 );
			$pdf->SetMargins(0, 0, 0);
			$border = 0;
			$pdf->SetTextColor(0, 0, 0);
            $pdf->SetAutoPageBreak(false,0);
    


            $y_point = 14;
            $pdf->SetXY( 105, $y_point );
            $pdf->MultiCell(30, 5, U2T(@$row['receipt_id']), $border, 1); // เลขที่
            $pdf->SetXY( 140, $y_point );
            $pdf->MultiCell(25, 5, U2T('ดอกเบี้ยสะสม'), $border, 1);//ดอกเบี้ยสะสม

            // $pdf->MultiCell(25, 5, number_format('56582',2), $border, 1);//ดอกเบี้ยสะสม
            $pdf->SetXY( 165, $y_point );
            // $pdf->MultiCell(30, 5, $row['receipt_datetime'], $border, 1); // วันที่ออกใบเสร็จ
            $pdf->MultiCell(30, 5, U2T($this->center_function->mydate2date($row['receipt_datetime'])), $border, 1);

            $y_point = 21;
            $pdf->SetXY( 26, $y_point );
            $pdf->MultiCell(50, 5, U2T(@$member_data['mem_group_name']), $border, 1);// หน่วย


            $y_point = 27;
            $pdf->SetXY( 26, $y_point );
            $pdf->MultiCell(50, 5, U2T($prename_full.$name), $border, 1); // ชื่อ-นามสกุล
            $pdf->SetXY( 175, $y_point );
			$pdf->MultiCell(25, 5, $member_data['member_id'], $border);//รหัสสมาชิก
			
            //รายการ
            $y_point = 33;
            $pdf->SetXY( 10, $y_point );
            $pdf->Cell(20, 5, U2T("รายการ"),1,0,'C');
            $pdf->Cell(20, 5, U2T("งวดที่"),1,0,'C');
            $pdf->Cell(20, 5, U2T("เงินต้น"),1,0,'C');
            $pdf->Cell(20, 5, U2T("ดอกเบี้ย"),1,0,'C');
            $pdf->Cell(20, 5, U2T("จำนวนเงิน"),1,0,'C');
            $pdf->Cell(20, 5, U2T("เงินคงเหลือ"),1,0,'C');
            $y_point = 33;
            $pdf->SetXY( 10, $y_point );
            $pdf->Cell(20, 36, U2T(""),1,0,'C');
            $pdf->Cell(20, 36, U2T(""),1,0,'C');
            $pdf->Cell(20, 36, U2T(""),1,0,'C');
            $pdf->Cell(20, 36, U2T(""),1,0,'C');
            $pdf->Cell(20, 36, U2T(""),1,0,'C');
            $pdf->Cell(20, 36, U2T(""),1,0,'C');
            $sum=0;
            //รายการ
            $pdf->SetFont('THSarabunNew', '', 10 );
            
            foreach($transaction_data_plus as $key => $value_plus){
                $principal_payment += $value_plus['principal_payment'];
                $interest += $value_plus['interest'];
                $total_amount += $value_plus['total_amount'];
                $loan_amount_balance += $value_plus['loan_amount_balance'];
                $period_count_num = $value['period_count'];

            }

			foreach($transaction_data as $key => $value){
               
                	$y_point += 3.3;
                    $pdf->SetXY( 10, $y_point );
                    if($value['account_list_id'] == 16){
                        $pdf->MultiCell(20, 8, U2T('หุ้น'), $border, 'C');
                    }else{
                        $pdf->MultiCell(20, 8, $value['contract_number_2'], $border, 'C');

                    }
                	$pdf->SetXY( 30, $y_point );
                	$pdf->MultiCell(20, 7, U2T($value['period_count']), $border, 'C');
                    if($value['account_list_id'] == 16){
                         $pdf->SetXY( 50, $y_point );
                         $pdf->MultiCell(20, 8, U2T(number_format($value['principal_payment'])), $border, 'C');
                         $pdf->SetXY( 70, $y_point );
                         $pdf->MultiCell(20, 8, U2T(number_format($value['interest'])), $border, 'C');
                         $pdf->SetXY( 90, $y_point );
                         $pdf->MultiCell(20, 8, U2T(number_format($value['total_amount'])), $border, 'C');
                         $pdf->SetXY( 110, $y_point );
                         $pdf->MultiCell(20, 8, U2T(number_format($value['loan_amount_balance'])), $border, 'C');
                         $total_amount_sum = $value['total_amount'];
                    }else{
                        $pdf->SetXY( 50, $y_point );
                        $pdf->MultiCell(20, 8, U2T(number_format($principal_payment)), $border, 'C');
                        $pdf->SetXY( 70, $y_point );
                        $pdf->MultiCell(20, 8, U2T(number_format($interest)), $border, 'C');
                        $pdf->SetXY( 90, $y_point );
                        $pdf->MultiCell(20, 8, U2T(number_format($total_amount)), $border, 'C');
                        $pdf->SetXY( 110, $y_point );
                        $pdf->MultiCell(20, 8, U2T(number_format($loan_amount_balance)), $border, 'C');
                        $total_amount_sum = $total_amount;

                    }
                 


                	$sum = $sum + $total_amount_sum;
                	$i++;
			}
            $y_point = 69;
            $pdf->SetXY( 10, $y_point );
            $pdf->Cell(80, 5, U2T('รวม '.convert($sum)),1,0,'C');
            $pdf->Cell(20, 5, number_format($sum,2),1,0,'C');
            $pdf->Cell(20, 5, U2T("บาท"),1,0,'C');
            $y_point = 74;
            $pdf->SetXY( 10, $y_point );
            $pdf->Cell(20, 5, U2T('เงินคืน'),1,0,'C');
            $pdf->Cell(40, 5, number_format(2,2),1,0,'C');
            //รายการ
            //เงินฝากหลักประกัน
            $y_point = 35;
            $pdf->SetXY( 135, $y_point );
            $pdf->Cell(65, 5, U2T("เงินฝากหลักประกัน"),1,1,'C');
            // $y_point = 40;
        if(!empty($transaction_data_account_balance)){
            foreach($transaction_data_account_balance as $key => $value_accounce_balance){

                $y_point += 5;  
                $pdf->SetXY( 135, $y_point );
                $pdf->Cell(25, 5, U2T($value_accounce_balance['account_id']),1,0,'C');
                $pdf->Cell(40, 5,  number_format($value_accounce_balance['transaction_balance']),1,0,'C');
            }
        }else{
            $y_point = 40;
            $pdf->SetXY( 135, $y_point );
            $pdf->Cell(25, 5, U2T(""),1,0,'C');
            $pdf->Cell(40, 5, U2T(""),1,0,'C');
            $y_point = 45;
            $pdf->SetXY( 135, $y_point );
            $pdf->Cell(25, 5, U2T(""),1,0,'C');
            $pdf->Cell(40, 5, U2T(""),1,0,'C');
            $y_point = 50;
            $pdf->SetXY( 135, $y_point );
            $pdf->Cell(25, 5, U2T(""),1,0,'C');
            $pdf->Cell(40, 5, U2T(""),1,0,'C');

        }
            // $pdf->SetXY( 135, $y_point );
            // $pdf->Cell(25, 5, U2T("00127002066"),1,0,'C');
            // $pdf->Cell(40, 5,  number_format(16571.68,2),1,0,'C');
            // $y_point = 45;
            // $pdf->SetXY( 135, $y_point );
            // $pdf->Cell(25, 5, U2T(""),1,0,'C');
            // $pdf->Cell(40, 5, U2T(""),1,0,'C');
            // $y_point = 50;
            // $pdf->SetXY( 135, $y_point );
            // $pdf->Cell(25, 5, U2T(""),1,0,'C');
            // $pdf->Cell(40, 5, U2T(""),1,0,'C');



            $y_point = 60;
            $pdf->SetXY( 135, $y_point );
            $pdf->Cell(65, 5, U2T("ยอดทุนเรือนหุ้นสะสม"),1,1,'C');
            $y_point = 65;
            $pdf->SetXY( 135, $y_point );
            $pdf->Cell(65, 5, number_format($share_collect_value['share_collect_value']),1,0,'C');
            //เงินฝากหลักประกัน


            //ผู้ค้ำประกัน
            $y_point = 81;
            $pdf->SetXY( 10, $y_point );
            $pdf->Cell(12, 12, U2T(''),1,0,'C');
            $y_point = 81;
            $pdf->SetXY( 10, $y_point );
            $pdf->Cell(12, 6, U2T('ผู้ค้ำ'),0,0,'C');
            $pdf->Cell(38, 6,  U2T(''),1,0,'C');
            $pdf->Cell(38, 6,  U2T(''),1,0,'C');
            $pdf->Cell(38, 6,  U2T(''),1,0,'C');
            foreach($transaction_data_guarantee_person as $key => $value_guarantee_person){
                if($key < 3){
                    if($key == 0){
                        $y_point = 81;
                        $pdf->SetXY( 10, $y_point );
                        $pdf->Cell(12, 6, U2T(''),0,0,'C');
                        $pdf->Cell(38, 6,  U2T($value_guarantee_person['member_id']).' '.U2T($value_guarantee_person['firstname_th']).' '.U2T($value_guarantee_person['lastname_th']),1,0,'C');
                    }else{
                        $pdf->Cell(38, 6,  U2T($value_guarantee_person['member_id']).' '.U2T($value_guarantee_person['firstname_th']).' '.U2T($value_guarantee_person['lastname_th']),1,0,'C');
                    }
                }else{
                    if($key == 3){
                        $y_point = 87;
                        $pdf->SetXY( 10, $y_point );

                        $pdf->Cell(12, 6, U2T(''),0,0,'C');
                        $pdf->Cell(38, 6,  U2T($value_guarantee_person['member_id']).' '.U2T($value_guarantee_person['firstname_th']).' '.U2T($value_guarantee_person['lastname_th']),1,0,'C');
                    }else{
                        $pdf->Cell(38, 6,  U2T($value_guarantee_person['member_id']).' '.U2T($value_guarantee_person['firstname_th']).' '.U2T($value_guarantee_person['lastname_th']),1,0,'C');                    }
                }

            }

            $y_point = 87;
            $pdf->SetXY( 10, $y_point );
            $pdf->Cell(12, 6, U2T('ประกัน'),0,0,'C');
            // $pdf->Cell(38, 6,  U2T('012221 นายชัยเนตร ไวยคณี'),1,0,'C');
            $pdf->Cell(38, 6,  U2T(''),1,0,'C');
            $pdf->Cell(38, 6,  U2T(''),1,0,'C');
            $pdf->Cell(38, 6,  U2T(''),1,0,'C');

            //ผู้ค้ำประกัน

            //ลายเซ็น
			$pdf->SetFont('THSarabunNew', '', 12 );
            $y_point = 81;
            $pdf->SetXY( 135, $y_point );
            $pdf->MultiCell(20, 5, U2T("ลงชื่อ"), $border, 'C');
            $pdf->SetXY( 170, $y_point );
            $pdf->MultiCell(30, 5, U2T("ผู้จัดการ"), $border, 'R');
            $y_point = 90;
            $pdf->SetXY( 135, $y_point );
            $pdf->MultiCell(20, 5, U2T("ลงชื่อ"), $border, 'C');
            $pdf->SetXY( 170, $y_point );
            $pdf->MultiCell(30, 5, U2T("หัวหน้าฝ่ายสินเชื่อ"), $border, 'R');


            $pdf->SetXY( 30, $y_point );
            // $pdf->Image('images/S__8486997.PNG',155,78,15,'','','');
            // $pdf->Image('images/S__8503368.PNG',155,84,15,'','','');
            // //ลายเซ็น
            $pdf->Image(base_url().PROJECTPATH.'/assets/images/coop_signature/'.$signature['signature_3'],155,80,17,'','','');
            $pdf->Image(base_url().PROJECTPATH.'/assets/images/coop_signature/'.$signature['signature_2'],155,86,17,'','','');
            $y_point = 110;
            $pdf->SetXY( 10, $y_point );
            $pdf->Cell(40, 12, U2T(''),1,0,'C');
            $y_point = 110;
            $pdf->SetXY( 10, $y_point );
            $pdf->Cell(40, 6, U2T('ใบแจ้งหนี้ '),0,0,'C');
            $y_point = 116;
            $monut_now_th = $mount_receipt;
            $pdf->SetXY( 10, $y_point );
            $pdf->Cell(40, 6, U2T('ประจำเดือน '.$monut_now_th.' 2561'),0,0,'C');


            $y_point = 100;// กรอบ
            $pdf->SetXY( 50, $y_point );
            $pdf->Cell(5, 6, U2T(''),0,0,'C');
            $pdf->Cell(30, 33,  U2T(''),1,0,'C');
            $pdf->Cell(20, 33,  U2T(''),1,0,'C');
            $pdf->Cell(30, 33,  U2T(''),1,0,'C');
            $pdf->Cell(30, 33,  U2T(''),1,0,'C');
            $pdf->Cell(30, 33,  U2T(''),1,0,'C');

            $y_point = 100;// หัวข้อ
            $pdf->SetXY( 50, $y_point );
            $pdf->Cell(5, 6, U2T(''),0,0,'C');
            $pdf->Cell(30, 6,  U2T('รายการ'),1,0,'C');
            $pdf->Cell(20, 6,  U2T('งวดที่'),1,0,'C');
            $pdf->Cell(30, 6, U2T('เงินต้น'),1,0,'C');
            $pdf->Cell(30, 6, U2T('ดอกเบี้ย'),1,0,'C');
            $pdf->Cell(30, 6, U2T('จำนวนเงิน'),1,0,'C');
            $sum2 =0;
            $pdf->SetFont('THSarabunNew', '', 10 );
            
            foreach($row_next_mount_list as $key => $value_plus_mount_list){
                $interest_per_year = 6;
                $day_count = 30;
                if($value_plus_mount_list['deduct_code'] == 'LOAN'){

                    $principal_payment2 = $value_plus_mount_list['pay_amount'];
                    $loan_amount_balance = $value_plus_mount_list['loan_amount_balance'];
                    $interest2 = (((( $loan_amount_balance * $interest_per_year)/100)/365)*$day_count);
                    $total_amount2 = $principal_payment2 + $interest2;

                }
                // $principal_payment += $value_plus_mount_list['principal_payment'];
                // $interest += $value_plus_mount_list['interest'];
                // $total_amount += $value_plus_mount_list['total_amount'];
                // $loan_amount_balance += $value_plus_mount_list['loan_amount_balance'];

            }

			foreach($row_next_mount_list as $key => $value_next_mount_list){
               
                	$y_point += 3.3;
                    $pdf->SetXY( 60, $y_point );
                    if($value_next_mount_list['deduct_code'] == 'SHARE'){
                        $pdf->MultiCell(20, 8, U2T('หุ้น'), $border, 'C');
                        $period_next_mount = $value_next_mount_list['share_period']+1;

                    }else{
                        $pdf->MultiCell(20, 8, $value_next_mount_list['contract_number'], $border, 'C');
                        $period_next_mount =  $value_next_mount_list['period_count']+1;


                    }
                	
                    if($value_next_mount_list['deduct_code'] == 'SHARE'){
                       
                         $pdf->SetXY( 90, $y_point );
                         $pdf->MultiCell(20, 8, U2T(number_format($period_next_mount)), $border, 'C');
                         $pdf->SetXY( 110, $y_point );
                         $pdf->MultiCell(20, 8, U2T(number_format($value_next_mount_list['pay_amount'],2)), $border, 'C');
                         $pdf->SetXY( 140, $y_point );
                         $pdf->MultiCell(20, 8, U2T(number_format($value_next_mount_list['total_amount'],2)), $border, 'C');
                         $pdf->SetXY( 170, $y_point );
                         $pdf->MultiCell(20, 8, U2T(number_format($value_next_mount_list['pay_amount'],2)), $border, 'C');
                         $total_amount_sum2 = $value_next_mount_list['pay_amount'];
                    }else{
                      
                        $pdf->SetXY( 90, $y_point );
                        $pdf->MultiCell(20, 8, U2T(number_format($period_next_mount)), $border, 'C');
                        $pdf->SetXY( 110, $y_point );
                        $pdf->MultiCell(20, 8, U2T(number_format($principal_payment2,2)), $border, 'C');
                        $pdf->SetXY( 140, $y_point );
                        $pdf->MultiCell(20, 8, U2T(number_format($interest2,2)), $border, 'C');
                        $pdf->SetXY( 170, $y_point );
                        $pdf->MultiCell(20, 8, U2T(number_format($total_amount2,2)), $border, 'C');
                        $total_amount_sum2 = $total_amount2;

                    }
                 


                    $sum2 = $sum2 + $total_amount_sum2;
                    $sum_all =  number_format($sum2,2);
                	$i++;
			}

            
            // for($i=1;$i < 10;$i++){
            //     $y_point += 3;
            //     $pdf->SetXY( 60, $y_point );
            //     $money2 = 4000;
            //     $pdf->MultiCell(20, 8, U2T("รายการ".$i), $border, 'C');
            //     $pdf->SetXY( 90, $y_point );
            //     $pdf->MultiCell(20, 8, U2T("งวดที่".$i), $border, 'C');
            //     $pdf->SetXY( 110, $y_point );
            //     $pdf->MultiCell(20, 8, number_format($money2,2), $border, 'C');
            //     $pdf->SetXY( 140, $y_point );
            //     $pdf->MultiCell(20, 8, U2T("ดอกเบี้ย".$i), $border, 'C');
            //     $pdf->SetXY( 170, $y_point );
            //     $pdf->MultiCell(20, 8, U2T("จำนวนเงิน".$i), $border, 'C');

            //     $sum2 += $money2;
            // }

            $y_point = 133;
            $pdf->SetXY( 55, $y_point );
            $pdf->Cell(80, 5, U2T('รวม '.convert($sum_all)),1,0,'C');
            $pdf->Cell(30, 5, number_format($sum2,2),1,0,'C');
            $pdf->Cell(30, 5, U2T("บาท"),1,0,'C');
        }
    }
            $pdf->Output();
?>
