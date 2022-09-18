<?php
$month_arr = array('1'=>'มกราคม','2'=>'กุมภาพันธ์','3'=>'มีนาคม','4'=>'เมษายน','5'=>'พฤษภาคม','6'=>'มิถุนายน','7'=>'กรกฎาคม','8'=>'สิงหาคม','9'=>'กันยายน','10'=>'ตุลาคม','11'=>'พฤศจิกายน','12'=>'ธันวาคม');
$month_short_arr = array('1'=>'ม.ค.','2'=>'ก.พ.','3'=>'มี.ค.','4'=>'เม.ย.','5'=>'พ.ค.','6'=>'มิ.ย.','7'=>'ก.ค.','8'=>'ส.ค.','9'=>'ก.ย.','10'=>'ต.ค.','11'=>'พ.ย.','12'=>'ธ.ค.');

$objPHPExcel = new PHPExcel();

$borderRight = array(
    'borders' => array(
        'right' => array(
            'style' => PHPExcel_Style_Border::BORDER_THIN
        )
    )
);
$borderLeft = array(
    'borders' => array(
        'left' => array(
            'style' => PHPExcel_Style_Border::BORDER_THIN
        )
    )
);
$borderTop = array(
    'borders' => array(
        'top' => array(
            'style' => PHPExcel_Style_Border::BORDER_THIN
        )
    )
);
$borderBottom = array(
    'borders' => array(
        'bottom' => array(
            'style' => PHPExcel_Style_Border::BORDER_THIN
        )
    )
);
$borderBottomDouble = array(
    'borders' => array(
        'bottom' => array(
            'style' => PHPExcel_Style_Border::BORDER_DOUBLE
        )
    )
);
$styleArray = array(
    'borders' => array(
        'allborders' => array(
            'style' => PHPExcel_Style_Border::BORDER_THIN
        )
    ),
    'font'  => array(
        'bold'  => false,
        'size'  => 14,
        'name'  => 'Cordia New'
    )
);
$textStyleArray = array(
    'font'  => array(
        'bold'  => false,
        'size'  => 14,
        'name'  => 'CordiaUPC'
    )
);
$headerStyle = array(
    'font'  => array(
        'bold'  => true,
        'size'  => 16,
        'name'  => 'Cordia New'
    )
);
$titleStyle = array(
    'font'  => array(
        'bold'  => true,
        'size'  => 20,
        'name'  => 'AngsanaUPC'
    )
);
$footerStyle = array(
    'font'  => array(
        'bold'  => true,
        'size'  => 18,
        'name'  => 'Cordia New'
    )
);
if(@$_GET['report_date'] != ''){
    $date_arr = explode('/',@$_GET['report_date']);
    $day = (int)@$date_arr[0];
    $month = (int)@$date_arr[1];
    $year = (int)@$date_arr[2];
    $year -= 543;
    $file_name_text = $day."_".$month_arr[$month]."_".($year+543);
}else{
    if(@$_GET['month']!='' && $_GET['year']!=''){
        $day = '';
        $month = @$_GET['month'];
        $year = (@$_GET['year']-543);
        $file_name_text = $month_arr[$month]."_".($year+543);
    }else{
        $day = '';
        $month = '';
        $year = (@$_GET['year']-543);
        $file_name_text = ($year+543);
    }
}

if($month!=''){
    $month_start = $month;
    $month_end = $month;
}else{
    $month_start = 1;
    $month_end = 12;
}
$sheet = 0;
for($m = $month_start; $m <= $month_end; $m++){
    $s_date = $year.'-'.sprintf("%02d",@$m).'-01'.' 00:00:00.000';
    $e_date = date('Y-m-t',strtotime($s_date)).' 23:59:59.000';
    $where_check = " AND t1.approve_date BETWEEN '".$s_date."' AND '".$e_date."'";
    $this->db->select(array('t1.id as loan_id', 't1.contract_number'));
    $this->db->from('coop_loan as t1');
    $this->db->join('coop_mem_apply as t2','t1.member_id = t2.member_id','inner');
    $this->db->join("coop_prename as t3 ", "t2.prename_id = t3.prename_id", "left");
    $this->db->join("coop_loan_reason as t4 ", "t1.loan_reason = t4.loan_reason_id", "inner");
    $this->db->join("coop_loan_name as t5", "t1.loan_type = t5.loan_name_id", "left");
    $this->db->join("coop_loan_type as t6", "t5.loan_type_id = t6.id", "left");
    $this->db->where("t6.id = '".@$_GET['loan_type']."' AND t1.loan_status IN ('1','4') {$where_check}");
    //$this->db->where("t1.loan_type = '".@$_GET['loan_type']."' AND t1.loan_status IN ('1','4') {$where_check}");
    $this->db->where("t1.contract_number NOT LIKE '%/%'");
    $this->db->order_by('t1.contract_number ASC,t1.approve_date ASC');
    $rs_check = $this->db->get()->result_array();
    $row_check = @$rs_check[0];
    if($_GET['dev'] == 'dev'){
        echo $this->db->last_query();exit;
        echo '<pre>';print_r($rs_check);exit;
    }
//    exit;

    if(@$row_check['loan_id']=='' && @$_GET['report_date']==''){
        continue;
    }

    if(empty($_GET['loan_name'])){
        $loan_name = array($loan_type[$_GET['loan_type']]);
        $prefix_loan = array($loan_type[$_GET['loan_type']]);
    }else{
        $this->db->order_by("order_by asc");
        $this->db->where_in('loan_name_id', $_GET['loan_name']);
        $coop_loan_name = $this->db->get("coop_loan_name")->result_array();
        $loan_name = array_column($coop_loan_name, 'loan_name');
        $prefix_loan = array();
        foreach ($coop_loan_name as $key => $value) {
            $this->db->where("start_date <=", $s_date);
            $this->db->where("type_id", $value['loan_name_id']);
            $tmp_prefix_loan = $this->db->get("coop_term_of_loan")->row_array()['prefix_code'];
            $loan_name[$key] .= " (".$tmp_prefix_loan.")";
            array_push($prefix_loan, $tmp_prefix_loan);
        }
    }

    $i=0;
    $objPHPExcel->createSheet($sheet);
    $objPHPExcel->setActiveSheetIndex($sheet);
    $i+=1;
    $objPHPExcel->getActiveSheet()->mergeCells('A'.$i.':S'.$i);
    $objPHPExcel->getActiveSheet()->SetCellValue('A'.$i, "ทะเบียน ". implode(", ", $loan_name) ."  เดือน  ".@$month_arr[$m]." ".(@$year+543) ) ;
    $objPHPExcel->getActiveSheet()->getStyle('A'.$i)->applyFromArray($titleStyle);
    $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':S'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

    $i+=1;
    $i_top = $i;
    $objPHPExcel->getActiveSheet()->SetCellValue('A' . $i , "หนังสือกู้สำหรับ" ) ;
    $objPHPExcel->getActiveSheet()->mergeCells('A'.$i.':B'.$i);
    $objPHPExcel->getActiveSheet()->SetCellValue('C' . $i , "ผู้กู้" ) ;
    $objPHPExcel->getActiveSheet()->mergeCells('C'.$i.':G'.$i);
    $objPHPExcel->getActiveSheet()->SetCellValue('H' . $i , "จำนวนเงินกู้" ) ;
    $objPHPExcel->getActiveSheet()->mergeCells('H'.$i.':H'.($i+2));
    $objPHPExcel->getActiveSheet()->SetCellValue('I' . $i , "การส่งเงินงวดชำระหนี้" ) ;
    $objPHPExcel->getActiveSheet()->mergeCells('I'.$i.':K'.$i);
    $objPHPExcel->getActiveSheet()->SetCellValue('L' . $i , "ผู้ค้ำประกัน" ) ;
    $objPHPExcel->getActiveSheet()->mergeCells('L'.$i.':P'.$i);
    $objPHPExcel->getActiveSheet()->SetCellValue('Q' . $i , "เหตุผลในการขอกู้" ) ;
    $objPHPExcel->getActiveSheet()->mergeCells('Q'.$i.':Q'.($i+2));

    $i+=1;
    $i_middle = $i;
    $objPHPExcel->getActiveSheet()->SetCellValue('A' . $i , implode(", ", $prefix_loan) ) ;
    $objPHPExcel->getActiveSheet()->mergeCells('A'.$i.':B'.$i);
    $objPHPExcel->getActiveSheet()->SetCellValue('C' . $i , "ทะเบียน" ) ;
    $objPHPExcel->getActiveSheet()->SetCellValue('D' . $i , "ชื่อ -สกุล" ) ;
    $objPHPExcel->getActiveSheet()->mergeCells('D'.$i.':F'.($i+1));
    $objPHPExcel->getActiveSheet()->SetCellValue('G' . $i , "หน่วยงาน" ) ;
    $objPHPExcel->getActiveSheet()->mergeCells('G'.$i.':G'.($i+1));
    $objPHPExcel->getActiveSheet()->SetCellValue('I' . $i , "งวดชำระที่ระบุในสัญญาเฉพาะเงินต้น" ) ;
    $objPHPExcel->getActiveSheet()->mergeCells('I'.$i.':I'.($i+1));
    $objPHPExcel->getActiveSheet()->SetCellValue('J' . $i , "ตั้งแต่" ) ;
    $objPHPExcel->getActiveSheet()->mergeCells('J'.$i.':J'.($i+1));
    $objPHPExcel->getActiveSheet()->SetCellValue('K' . $i , "ถึง" ) ;
    $objPHPExcel->getActiveSheet()->mergeCells('K'.$i.':K'.($i+1));
    $objPHPExcel->getActiveSheet()->SetCellValue('L' . $i , "ทะเบียน" ) ;
    $objPHPExcel->getActiveSheet()->SetCellValue('M' . $i , "ชื่อ -สกุล" ) ;
    $objPHPExcel->getActiveSheet()->mergeCells('M'.$i.':O'.($i+1));
    $objPHPExcel->getActiveSheet()->SetCellValue('P' . $i , "หน่วยงาน" ) ;
    $objPHPExcel->getActiveSheet()->mergeCells('P'.$i.':P'.($i+1));

    $i+=1;
    $i_bottom = $i;
    $objPHPExcel->getActiveSheet()->SetCellValue('A' . $i , "ที่" ) ;
    $objPHPExcel->getActiveSheet()->SetCellValue('B' . $i , "วันที่" ) ;
    $objPHPExcel->getActiveSheet()->SetCellValue('C' . $i , "สมาชิก" ) ;
    $objPHPExcel->getActiveSheet()->SetCellValue('L' . $i , "สมาชิก" ) ;

    $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(17.43);
    $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(11.14);
    $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(10);
    $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(10);
    $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
    $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
    $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(30);
    $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(15);
    $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(15);
    $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(13.29);
    $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(11.86);
    $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(10);
    $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(10);
    $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(15);
    $objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(15);
    $objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth(30);
    $objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setWidth(30);

    foreach(range('A','Q') as $columnID) {
        $objPHPExcel->getActiveSheet()->getStyle($columnID.$i_top)->applyFromArray($borderTop);
        $objPHPExcel->getActiveSheet()->getStyle($columnID.$i_top)->applyFromArray($borderLeft);
        $objPHPExcel->getActiveSheet()->getStyle($columnID.$i_top)->applyFromArray($borderRight);

        $objPHPExcel->getActiveSheet()->getStyle($columnID.$i_middle)->applyFromArray($borderLeft);
        $objPHPExcel->getActiveSheet()->getStyle($columnID.$i_middle)->applyFromArray($borderRight);

        $objPHPExcel->getActiveSheet()->getStyle($columnID.$i_bottom)->applyFromArray($borderLeft);
        $objPHPExcel->getActiveSheet()->getStyle($columnID.$i_bottom)->applyFromArray($borderRight);
        $objPHPExcel->getActiveSheet()->getStyle($columnID.$i_bottom)->applyFromArray($borderBottom);

        $objPHPExcel->getActiveSheet()->getStyle($columnID.$i_top)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle($columnID.$i_middle)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle($columnID.$i_bottom)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    }
    $objPHPExcel->getActiveSheet()->getStyle('C'.$i_top.':G'.$i_top)->applyFromArray($borderBottom);
    $objPHPExcel->getActiveSheet()->getStyle('I'.$i_top.':L'.$i_top)->applyFromArray($borderBottom);
    $objPHPExcel->getActiveSheet()->getStyle('M'.$i_top.':P'.$i_top)->applyFromArray($borderBottom);
    $objPHPExcel->getActiveSheet()->getStyle('A'.$i_middle.':B'.$i_middle)->applyFromArray($borderBottom);

    $objPHPExcel->getActiveSheet()->getStyle('A'.$i_top.':Q'.$i_bottom)->applyFromArray($headerStyle);
    $objPHPExcel->getActiveSheet()->getStyle('A'.$i_top.':Q'.$i_bottom)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
    $where = '';
    if($day != ''){
        $s_date = $year.'-'.sprintf("%02d",@$m).'-'.sprintf("%02d",@$day).' 00:00:00.000';
        $e_date = $year.'-'.sprintf("%02d",@$m).'-'.sprintf("%02d",@$day).' 23:59:59.000';
        $where .= " AND t1.createdatetime BETWEEN '".$s_date."' AND '".$e_date."'";
    }else{
        $s_date = $year.'-'.sprintf("%02d",@$m).'-01'.' 00:00:00.000';
        $e_date = date('Y-m-t',strtotime($s_date)).' 23:59:59.000';
        $where .= " AND t1.createdatetime BETWEEN '".$s_date."' AND '".$e_date."'";
    }
    $this->db->select(array('t1.id as loan_id',
        't1.contract_number',
        't1.createdatetime',
        't2.member_id',
        't2.employee_id',
        't3.prename_short',
        't2.firstname_th',
        't2.lastname_th',
        't2.level',
        't1.period_amount',
        't1.loan_amount',
        't1.money_period_1',
        't4.loan_reason',
        't7.guarantee_person_id',
        't9.prename_short AS guarantee_person_prename',
        't8.firstname_th AS guarantee_person_firstname_th',
        't8.lastname_th AS guarantee_person_lastname_th',
        't8.level AS guarantee_person_level'
    ));
    $this->db->from('coop_loan as t1');
    $this->db->join('coop_mem_apply as t2','t1.member_id = t2.member_id','inner');
    $this->db->join("coop_prename as t3 ", "t2.prename_id = t3.prename_id", "left");
    $this->db->join("coop_loan_reason as t4 ", "t1.loan_reason = t4.loan_reason_id", "inner");
    $this->db->join("coop_loan_name as t5", "t1.loan_type = t5.loan_name_id", "left");
    $this->db->join("coop_loan_type as t6", "t5.loan_type_id = t6.id", "left");
    $this->db->join("coop_loan_guarantee_person AS t7", "t1.id = t7.loan_id AND t7.guarantee_person_id <> ''", "left");
    $this->db->join("coop_mem_apply AS t8", "t7.guarantee_person_id = t8.member_id", "left");
    $this->db->join("coop_prename AS t9", "t8.prename_id = t9.prename_id", "left");
    $this->db->where("t6.id = '".@$_GET['loan_type']."' AND t1.loan_status IN ('1','4') {$where}");
    if(@$_GET['loan_name']!=""){
        $this->db->where("t5.loan_name_id in (".$_GET['loan_name'].")");
    }
    $this->db->where("t1.contract_number NOT LIKE '%/%'");
    //$this->db->where("t1.loan_type = '".@$_GET['loan_type']."' AND t1.loan_status IN ('1','4') {$where}");
    $this->db->order_by('t1.contract_number ASC, t1.approve_date ASC');
    $rs = $this->db->get()->result_array();
    $count_loan = 0;
    $loan_amount=0;
    // print_r($this->db->last_query());exit;
    $save_loan_id = 'a45as';
    if(!empty($rs)){
        foreach($rs as $key => $row){
            $i+=1;
            $this->db->select(array('period_count','date_period'));
            $this->db->from('coop_loan_period');
            $this->db->where("loan_id = '".@$row['loan_id']."'");
            $this->db->order_by('period_count ASC');
            $rs_period = $this->db->get()->result_array();;

            $first_period = '';
            $last_period = '';
            if(!empty($rs_period)){
                foreach($rs_period as $key => $row_period){
                    if(@$row_period['period_count'] == '1'){
                        $first_period = @$row_period['date_period'];
                    }
                    $last_period = @$row_period['date_period'];
                }
            }

            if(@$row['contract_number'] != $chk_contract_number){
                $loan_amount += @$row['loan_amount'];
                $count_loan++;
                $chk_contract_number = @$row['contract_number'];
            }
//            if($save_loan_id != @$row['loan_id']){
                $objPHPExcel->getActiveSheet()->SetCellValue('A' . $i , @$row['contract_number'] );
                $objPHPExcel->getActiveSheet()->SetCellValue('B' . $i , $this->center_function->mydate2date(@$row['createdatetime']));
                $objPHPExcel->getActiveSheet()->setCellValueExplicit('C' . $i  , @$row['member_id'], PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->SetCellValue('D' . $i , @$row['prename_short'] );
                $objPHPExcel->getActiveSheet()->SetCellValue('E' . $i , @$row['firstname_th'] );
                $objPHPExcel->getActiveSheet()->SetCellValue('F' . $i , @$row['lastname_th'] );
                $objPHPExcel->getActiveSheet()->SetCellValue('G' . $i , @$mem_group_arr[@$row['level']] );
                $objPHPExcel->getActiveSheet()->SetCellValue('H' . $i , number_format(@$row['loan_amount'],2) );
                $objPHPExcel->getActiveSheet()->SetCellValue('I' . $i , number_format(@$row['money_period_1'],2) );
                $objPHPExcel->getActiveSheet()->SetCellValue('J' . $i , ($first_period)?@$this->center_function->ConvertToThaiDateMMYY($first_period,1,1):'' );
                $objPHPExcel->getActiveSheet()->SetCellValue('K' . $i , ($last_period)?@$this->center_function->ConvertToThaiDateMMYY($last_period,1,1):'' );
                $save_loan_id = @$row['loan_id'];
//            }else{
//                $objPHPExcel->getActiveSheet()->SetCellValue('A' . $i , @$row['contract_number'] );
//                $objPHPExcel->getActiveSheet()->SetCellValue('B' . $i , $this->center_function->mydate2date(@$row['createdatetime']));
//                $objPHPExcel->getActiveSheet()->setCellValueExplicit('C' . $i  , @$row['member_id'], PHPExcel_Cell_DataType::TYPE_STRING);
//                $objPHPExcel->getActiveSheet()->SetCellValue('D' . $i , @$row['prename_short'] );
//                $objPHPExcel->getActiveSheet()->SetCellValue('E' . $i , @$row['firstname_th'] );
//                $objPHPExcel->getActiveSheet()->SetCellValue('F' . $i , @$row['lastname_th'] );
//                $objPHPExcel->getActiveSheet()->SetCellValue('G' . $i , @$mem_group_arr[@$row['level']] );
//                $objPHPExcel->getActiveSheet()->SetCellValue('H' . $i , number_format(@$row['loan_amount'],2) );
//                $objPHPExcel->getActiveSheet()->SetCellValue('I' . $i , number_format(@$row['money_period_1'],2) );
//                $objPHPExcel->getActiveSheet()->SetCellValue('J' . $i , ($first_period)?@$this->center_function->ConvertToThaiDateMMYY($first_period,1,1):'' );
//                $objPHPExcel->getActiveSheet()->SetCellValue('K' . $i , ($last_period)?@$this->center_function->ConvertToThaiDateMMYY($last_period,1,1):'' );
                $save_loan_id = @$row['loan_id'];
//            }
            $objPHPExcel->getActiveSheet()->setCellValueExplicit('L' . $i  , @$row['guarantee_person_id'], PHPExcel_Cell_DataType::TYPE_STRING);
            $objPHPExcel->getActiveSheet()->SetCellValue('M' . $i , @$row['guarantee_person_prename'] );
            $objPHPExcel->getActiveSheet()->SetCellValue('N' . $i , @$row['guarantee_person_firstname_th'] );
            $objPHPExcel->getActiveSheet()->SetCellValue('O' . $i , @$row['guarantee_person_lastname_th'] );
            $objPHPExcel->getActiveSheet()->SetCellValue('P' . $i , @$mem_group_arr[@$row['guarantee_person_level']] );
            $objPHPExcel->getActiveSheet()->SetCellValue('Q' . $i , @$row['loan_reason'] );

            $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':Q'.$i)->applyFromArray($textStyleArray);
            $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':Q'.$i)->applyFromArray($borderTop);

            foreach(range('A','Q') as $columnID) {
                //if(!in_array($columnID, array('E','F','G'))){
                $objPHPExcel->getActiveSheet()->getStyle($columnID.$i)->applyFromArray($borderLeft);
                $objPHPExcel->getActiveSheet()->getStyle($columnID.$i)->applyFromArray($borderRight);
                //}
                $objPHPExcel->getActiveSheet()->getStyle($columnID.$i)->applyFromArray($borderBottom);
            }
            $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':C'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $objPHPExcel->getActiveSheet()->getStyle('H'.$i.':I'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
            $objPHPExcel->getActiveSheet()->getStyle('J'.$i.':L'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        }
    }
    $i+=2;
    $objPHPExcel->getActiveSheet()->mergeCells('B'.$i.':D'.($i));
    $objPHPExcel->getActiveSheet()->SetCellValue('B' . $i , "เดือน ".$month_arr[$m] );
    $objPHPExcel->getActiveSheet()->SetCellValue('E' . $i , "รวม " );
    $objPHPExcel->getActiveSheet()->SetCellValue('F' . $i , number_format($count_loan) );
    $objPHPExcel->getActiveSheet()->SetCellValue('G' . $i , "สัญญา " );
    $objPHPExcel->getActiveSheet()->mergeCells('H'.$i.':I'.($i));
    $objPHPExcel->getActiveSheet()->SetCellValue('H' . $i , "เป็นเงินจำนวน " );
    $objPHPExcel->getActiveSheet()->SetCellValue('J' . $i , number_format($loan_amount) );
    $objPHPExcel->getActiveSheet()->SetCellValue('K' . $i , "บาท " );
    $objPHPExcel->getActiveSheet()->getStyle('A'.$i.':Q'.$i)->applyFromArray($footerStyle);
    //$objPHPExcel->getActiveSheet()->getStyle('H'.$i.':I'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

    //}
    $objPHPExcel->getActiveSheet()->setTitle($month_short_arr[$m].substr(($year+543),2,2));
    $sheet++;
}
//exit;	
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="รายงานเงินกู้แยกประเภท_'.$loan_type[@$_GET['loan_type']].'_'.$file_name_text.'.xlsx"');
header('Cache-Control: max-age=0');

$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
$objWriter->save('php://output');
exit;
?>