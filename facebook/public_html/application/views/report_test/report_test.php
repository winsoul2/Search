<?php
$month_arr = array('1'=>'มกราคม','2'=>'กุมภาพันธ์','3'=>'มีนาคม','4'=>'เมษายน','5'=>'พฤษภาคม','6'=>'มิถุนายน','7'=>'กรกฎาคม','8'=>'สิงหาคม','9'=>'กันยายน','10'=>'ตุลาคม','11'=>'พฤศจิกายน','12'=>'ธันวาคม');
$writer = new XLSXWriter();
$writer->setAuthor('Some Author'); 
/*
$rows = array();
$max = 10;
for($i=0;$i<$max;$i++){
	$rows[$i]['0'] = 'ส.001/2561';				
	$rows[$i]['1'] = '16/03/2561';				
	$rows[$i]['2'] = '13';				
	$rows[$i]['3'] = '12345';				
	$rows[$i]['4'] = 'นาย';				
	$rows[$i]['5'] = 'ภิญญา';				
	$rows[$i]['6'] = 'ธนากรถิรพร';				
	$rows[$i]['7'] = 'CTEST13';				
	$rows[$i]['8'] = '72';				
	$rows[$i]['9'] = '20000';	
}


$styles1 = array( 'font'=>'Arial','font-size'=>10,'font-style'=>'bold', 'fill'=>'#eee', 'halign'=>'center', 'border'=>'left,right,top,bottom','widths'=>[50,20,30,40,50,60,70,80]);
$i = 0;
$writer->writeSheetHeader('Sheet1', array('c1'=>'string','c2'=>'string','c3'=>'string','c4'=>'string','c5'=>'string'),$styles1 );
foreach($rows as $row){
	$i++;
	$writer->writeSheetRow('Sheet1', $row );	
}	
*/
$titleStyle = array( 
	'font'=>'Cordia New',
	'font-size'=>16,
	'font-style'=>'bold', 
	'halign'=>'center', 
);

$headerStyle = array( 
	'font'=>'Cordia New',
	'font-size'=>14,
	'font-style'=>'bold', 
	'fill'=>'#FFFF99', 
	'halign'=>'center', 
	'border-style' => 'thin',
	'border'=>'left,right,top,bottom' 
);
$styleT = [
	'font'=>'Cordia New',
	'font-size'=>14,
	'font-style'=>'bold', 
	'fill'=>'#FFFF99', 
	'halign'=>'center', 
	'border-style' => 'thin',
	'border'=>'left,right,top'];
	
$styleB = [
		'font'=>'Cordia New',
		'font-size'=>14,
		'font-style'=>'bold', 
		'fill'=>'#FFFF99', 
		'halign'=>'center', 
		'border-style' => 'thin',
		'border'=>'left,right,bottom'
	];	
	
$styleTB = [
		'font'=>'Cordia New',
		'font-size'=>14,
		'font-style'=>'bold', 
		'fill'=>'#FFFF99', 
		'halign'=>'center', 
		'border-style' => 'thin',
		'border'=>'left,right,top,bottom'
	];
$textNull = [];	
$textLeftBorder = [
		'font'=>'Cordia New',
		'font-size'=>12,
		'halign'=>'left',
		'border-style' => 'thin',
		'border'=>'left,right,top,bottom'
	];	
	
$textRightBorder = [
		'font'=>'Cordia New',
		'font-size'=>12,
		'halign'=>'right',
		'border-style' => 'thin',
		'border'=>'left,right,top,bottom'
	];	
	
$textCenterBorder = [
		'font'=>'Cordia New',
		'font-size'=>12,
		'halign'=>'center',
		'border-style' => 'thin',
		'border'=>'left,right,top,bottom'
	];
$textLeft = [
		'font'=>'Cordia New',
		'font-size'=>12,
		'halign'=>'left'
	];	
	
$textRight = [
		'font'=>'Cordia New',
		'font-size'=>12,
		'halign'=>'right'
	];	
	
$textCenter = [
		'font'=>'Cordia New',
		'font-size'=>12,
		'halign'=>'center'
	];
	

$textRightBorderBottom = [
	'font'=>'Cordia New',
	'font-size'=>12,
	'halign'=>'right',
	'border-style' => 'thin',
	'border'=>'bottom'
];

$textRightBorderBottomRed = [
	'font'=>'Cordia New',
	'font-size'=>12,
	'halign'=>'right',
	'border-style' => 'thin',
	'border'=>'bottom',
	'color'=>'#FF0000',
];
$textRightRed = [
	'font'=>'Cordia New',
	'font-size'=>12,
	'halign'=>'right',
	'color'=>'#FF0000',
];

$textCenterBorderBottomPink = [
	'font'=>'Cordia New',
	'font-size'=>12,
	'halign'=>'center',
	'border-style' => 'thin',
	'border'=>'bottom',
	'color'=>'#FF00FF',
];
$textCenterPink = [
	'font'=>'Cordia New',
	'font-size'=>12,
	'halign'=>'center',
	'color'=>'#FF00FF',
];
$textCenterBorderBottomBlue = [
	'font'=>'Cordia New',
	'font-size'=>12,
	'halign'=>'center',
	'border-style' => 'thin',
	'border'=>'bottom',
	'color'=>'#0000FF',
];
$textCenterBlue = [
	'font'=>'Cordia New',
	'font-size'=>12,
	'halign'=>'center',
	'color'=>'#0000FF',
];
$textCenterBorderBottomGreen  = [
	'font'=>'Cordia New',
	'font-size'=>12,
	'halign'=>'center',
	'border-style' => 'thin',
	'border'=>'bottom',
	'color'=>'#339966',
];
$textCenterGreen  = [
	'font'=>'Cordia New',
	'font-size'=>12,
	'halign'=>'center',
	'color'=>'#339966',
];

$headerStyle1 = array( 
	$styleT,
	$styleT,
	$styleT,
	$styleT,
	$styleT,
	$styleT,
	$styleT,
	$styleT,
	$styleT,
	$styleT,
	$styleT,
	$styleT,
	$styleT,
	$styleT,
	$styleT,
	$styleT,
	$styleT,
	$styleT
);

$headerStyle2 = array( 
	$styleB,
	$styleB,
	$styleB,
	$styleB,
	$styleB,
	$styleB,
	$styleB,
	$styleTB,
	$styleTB,
	$styleTB,
	$styleTB,
	$styleTB,
	$styleTB,
	$styleTB,
	$styleTB,
	$styleTB,
	$styleTB,
	$styleB
);

$textStyle = array( 
	$textCenterBorder,
	$textCenterBorder,
	$textCenterBorder,
	$textLeftBorder,
	$textCenterBorder,
	$textRightBorder,
	$textRightBorder,
	$textCenterBorder,
	$textRightBorder,
	$textRightBorder,
	$textRightBorder,
	$textRightBorder,
	$textCenterBorder,
	$textRightBorder,
	$textRightBorder,
	$textRightBorder,
	$textRightBorder,
	$textLeftBorder
);

$textStyle3 = array( 
	$textNull,
	$textNull,
	$textNull,
	$textNull,
	$textNull,
	$textRight,
	$textRight,
	$textLeft,
	$textRight,
	$textRight,
	$textRight,
	$textRight,
	$textLeft,
	$textRight,
	$textRight,
	$textRight,
	$textRight,
	$textNull
);

$textStyle4 = array( 
	$textNull,
	$textNull,
	$textNull,
	$textNull,
	$textNull,
	$textNull,
	$textNull,
	$textNull,
	$textNull,
	$textRightBorderBottom,
	$textNull,
	$textNull,
	$textNull,
	$textNull,
	$textRightBorderBottom,
	$textNull,
	$textNull,
	$textNull
);

$textStyle6 = array( 
	$textNull,
	$textNull,
	$textNull,
	$textNull,
	$textCenter,
	$textRightBorderBottomRed,
	$textNull,
	$textNull,
	$textCenter,
	$textRightBorderBottomRed,
	$textNull,
	$textNull,
	$textNull,
	$textCenter,
	$textRightBorderBottomRed,
	$textNull,
	$textCenter,
	$textRight
);

$textStyle8 = array( 
	$textNull,
	$textNull,
	$textNull,
	$textNull,
	$textNull,
	$textNull,
	$textNull,
	$textNull,
	$textNull,
	$textNull,
	$textNull,
	$textNull,
	$textNull,
	$textCenter,
	$textNull,
	$textNull,
	$textCenter,
	$textNull
);

$textStyle9 = array( 
	$textNull,
	$textNull,
	$textNull,
	$textNull,
	$textNull,
	$textNull,
	$textNull,
	$textNull,
	$textCenterPink,
	$textRightRed,
	$textNull,
	$textNull,
	$textNull,
	$textCenterPink,
	$textRightRed,
	$textNull,
	$textNull,
	$textNull
);

$textStyle10 = array( 
	$textNull,
	$textNull,
	$textNull,
	$textNull,
	$textNull,
	$textNull,
	$textNull,
	$textNull,
	$textCenterBorderBottomPink,
	$textRightBorderBottomRed,
	$textNull,
	$textNull,
	$textNull,
	$textCenterBorderBottomPink,
	$textRightBorderBottomRed,
	$textNull,
	$textNull,
	$textNull
);

$textStyle11 = array( 
	$textNull,
	$textNull,
	$textNull,
	$textNull,
	$textNull,
	$textNull,
	$textNull,
	$textNull,
	$textCenterBlue,
	$textRightRed,
	$textNull,
	$textNull,
	$textNull,
	$textCenterGreen,
	$textRightRed,
	$textNull,
	$textNull,
	$textNull
);

$textStyle12 = array( 
	$textNull,
	$textNull,
	$textNull,
	$textNull,
	$textNull,
	$textNull,
	$textNull,
	$textNull,
	$textCenterBlue,
	$textRightRed,
	$textNull,
	$textNull,
	$textNull,
	$textCenterGreen,
	$textRightRed,
	$textNull,
	$textNull,
	$textNull
);


$k=1;
$i = 0;
$loan_arr = array();
$data_1 = array();
//echo '<pre>'; print_r($rs); echo '</pre>';
foreach($rs as $key => $row){ 
	
	$mem_group = @$row['level'];
	
	$this->db->select(array('change_value'));
	$this->db->from('coop_change_share');
	$this->db->where("member_id = '".$row['member_id']."' AND change_share_status IN ('1','2')");
	$this->db->order_by('active_date DESC');
	$this->db->limit(1);
	$row_change_share = $this->db->get()->result_array();
	$row_change_share = @$row_change_share[0];
	//echo '<pre>'; print_r($row_change_share); echo '</pre>';
	if($row_change_share['change_value'] != ''){
		$num_share = $row_change_share['change_value'];
	}else{
		$this->db->select(array('share_salary'));
		$this->db->from('coop_share_rule');
		$this->db->where("salary_rule <= '".$row['salary']."'");
		$this->db->order_by('salary_rule DESC');
		$this->db->limit(1);
		$row_share_rule = $this->db->get()->result_array();
	
		$num_share = $row_share_rule[0]['share_salary'];
	}
	
	$this->db->select(array('share_collect_value'));
	$this->db->from('coop_mem_share');
	$this->db->where("member_id = '".$row['member_id']."'");
	$this->db->order_by('share_id DESC');
	$this->db->limit(1);
	$row_share = $this->db->get()->result_array();
	$row_share = @$row_share[0];
	
	$this->db->select(
		array(
			't2.id as loan_id',
			't2.contract_number',
			't2.loan_type',
			't2.loan_amount_balance',
			't2.interest_per_year',
			't5.date_transfer'
		)
	);
	$this->db->from('coop_loan as t2');
	$this->db->join('coop_mem_apply as t3','t2.member_id = t3.member_id','inner');
	$this->db->join('coop_prename as t4','t3.prename_id = t4.prename_id','left');
	$this->db->join('coop_loan_transfer as t5','t2.id = t5.loan_id','inner');
	$this->db->where("
		t2.loan_type IN('1','2','5')
		AND t2.member_id = '".$row['member_id']."'
		AND t2.loan_status = '1'
		AND t2.loan_amount_balance > 0
		AND t2.date_start_period <= '".(@$_GET['year']-543)."-".sprintf("%02d",@$_GET['month'])."-".date('t',strtotime((@$_GET['year']-543)."-".sprintf("%02d",@$_GET['month'])."-01"))."'
	");
	$rs_normal_loan = $this->db->get()->result_array();
	$normal_loan = array();
	$b=0;
	foreach($rs_normal_loan as $key => $row_normal_loan){
		$normal_loan[$b] = $row_normal_loan;
		
		$this->db->select(array('principal_payment'));
		$this->db->from('coop_loan_period');
		$this->db->where("loan_id = '".$row_normal_loan['loan_id']."'");
		$this->db->limit(1);
		$row_principal_payment = $this->db->get()->result_array();
		$row_principal_payment = $row_principal_payment[0];
		
		$date_interesting = date('Y-m-t',strtotime((@$_GET['year']-543)."-".sprintf("%02d",@$_GET['month']).'-01'));
		
		$this->db->select(array('payment_date'));
		$this->db->from('coop_finance_transaction');
		$this->db->where("loan_id = '".$row_normal_loan['loan_id']."'");
		$this->db->order_by('payment_date DESC');
		$this->db->limit(1);
		$row_date_prev_paid = $this->db->get()->result_array();
		$row_date_prev_paid = $row_date_prev_paid[0];
		
		$date_prev_paid = $row_date_prev_paid['payment_date']!=''?$row_date_prev_paid['payment_date']:$row_normal_loan['date_transfer'];
		$diff = date_diff(date_create($date_prev_paid),date_create($date_interesting));
		$date_count = $diff->format("%a");
		$date_count = $date_count+1;
		
		$interest = ((($row_normal_loan['loan_amount_balance']*$row_normal_loan['interest_per_year'])/100)/365)*$date_count;
		
		if($row_normal_loan['loan_amount_balance'] > $row_principal_payment['principal_payment']){
			$principal_payment = $row_principal_payment['principal_payment'];
		}else{
			$principal_payment = $row_normal_loan['loan_amount_balance'];
		}
		$loan_arr[$row_normal_loan['loan_type']]['principal_payment'] += $principal_payment;
		
		//if($row['member_id']=='000012'){
			//echo $date_prev_paid;exit;
		//}
		
		$loan_arr[$row_normal_loan['loan_type']]['interest'] += $interest;
		
		$normal_loan[$b]['outstanding_balance'] = $row_normal_loan['loan_amount_balance'];
		$normal_loan[$b]['principal_payment'] = $principal_payment;
		$normal_loan[$b]['interest'] = $interest;
		$b++;
	}
	
	$this->db->select(
		array(
			't2.id as loan_id',
			't2.contract_number',
			't2.loan_type',
			't2.loan_amount_balance',
			't2.interest_per_year',
			't5.date_transfer'
		)
	);
	$this->db->from('coop_loan as t2');
	$this->db->join('coop_mem_apply as t3','t2.member_id = t3.member_id','inner');
	$this->db->join('coop_prename as t4','t3.prename_id = t4.prename_id','left');
	$this->db->join('coop_loan_transfer as t5','t2.id = t5.loan_id','inner');
	$this->db->where("
		t2.loan_type IN('3','4')
		AND t2.member_id = '".$row['member_id']."'
		AND t2.loan_status = '1'
		AND t2.loan_amount_balance > 0
		AND t2.date_start_period <= '".($_GET['year']-543)."-".sprintf("%02d",$_GET['month'])."-".date('t',strtotime(($_GET['year']-543)."-".sprintf("%02d",$_GET['month'])."-01"))."'
	");
	$rs_emergent_loan = $this->db->get()->result_array();
	$emergent_loan = array();
	$b=0;
	$sum_normal_outstanding_balance = 0;
	$sum_normal_principal_payment = 0;
	$sum_normal_interest = 0;
	$sum_normal_balance = 0;
	
	$sum_emergent_outstanding_balance = 0;
	$sum_emergent_principal_payment = 0;
	$sum_emergent_interest = 0;
	$sum_emergent_balance = 0;
	
	$sum_share = 0;
	$sum_share_collect = 0;
	
	foreach($rs_emergent_loan as $key => $row_emergent_loan){
		$emergent_loan[$b] = $row_emergent_loan;
		$loan_arr[$row_emergent_loan['loan_type']]['outstanding_balance'] += $row_emergent_loan['loan_amount_balance'];
		
		$this->db->select(array('principal_payment'));
		$this->db->from('coop_loan_period');
		$this->db->where("loan_id = '".$row_emergent_loan['loan_id']."'");
		$this->db->limit(1);
		$row_principal_payment = $this->db->get()->result_array();
		$row_principal_payment = $row_principal_payment[0];
		
		$date_interesting = date('Y-m-t',strtotime(($_GET['year']-543)."-".sprintf("%02d",$_GET['month']).'-01'));
		
		$this->db->select(array('payment_date'));
		$this->db->from('coop_finance_transaction');
		$this->db->where("loan_id = '".$row_emergent_loan['loan_id']."'");
		$this->db->order_by('payment_date DESC');
		$this->db->limit(1);
		$row_date_prev_paid = $this->db->get()->result_array();
		$row_date_prev_paid = $row_date_prev_paid[0];
		
		$date_prev_paid = $row_date_prev_paid['payment_date']!=''?$row_date_prev_paid['payment_date']:$row_emergent_loan['date_transfer'];
		$diff = date_diff(date_create($date_prev_paid),date_create($date_interesting));
		$date_count = $diff->format("%a");
		$date_count = $date_count+1;
		
		$interest = ((($row_emergent_loan['loan_amount_balance']*$row_emergent_loan['interest_per_year'])/100)/365)*$date_count;
		
		if($row_emergent_loan['loan_amount_balance'] > $row_principal_payment['principal_payment']){
			$principal_payment = $row_principal_payment['principal_payment'];
		}else{
			$principal_payment = $row_emergent_loan['loan_amount_balance'];
		}
		$loan_arr[$row_emergent_loan['loan_type']]['principal_payment'] += $principal_payment;
		
		$loan_arr[$row_emergent_loan['loan_type']]['interest'] += $interest;
		
		$emergent_loan[$b]['outstanding_balance'] = $row_emergent_loan['loan_amount_balance'];
		$emergent_loan[$b]['principal_payment'] = $principal_payment;
		$emergent_loan[$b]['interest'] = $interest;
		$b++;
	}
	$a = 0;
	if(count($normal_loan) == 0 && count($emergent_loan) == 0){
		$a = 0;
	}else{
		if(count($normal_loan)>count($emergent_loan)){
			$a = count($normal_loan)-1;
		}else{
			$a = count($emergent_loan)-1;
		}
	}
	
	for($j=0; $j<=$a; $j++){
		$i++;
		$data_1[$i][0] = @$k++;
		$data_1[$i][1] =  @$row['member_id'];
		$data_1[$i][2] =  @$row['employee_id'];
		$data_1[$i][3] =  @$row['prename_short'].$row['firstname_th']." ".@$row['lastname_th'];
		$data_1[$i][4] =  @$mem_group_arr[$mem_group];
		$data_1[$i][5] =  number_format($num_share*$share_value,2);
		$data_1[$i][6] =  number_format(@$row_share['share_collect_value']+($num_share*$share_value),2);
		$data_1[$i][7] =  @$normal_loan[$j]['contract_number']!=''?@$normal_loan[$j]['contract_number']:'-';
		$data_1[$i][8] =  @$normal_loan[$j]['contract_number']!=''?number_format(@$normal_loan[$j]['outstanding_balance'],2):'-';
		$data_1[$i][9] =  @$normal_loan[$j]['contract_number']!=''?number_format(@$normal_loan[$j]['principal_payment'],2):'-';
		$data_1[$i][10] =  @$normal_loan[$j]['contract_number']!=''?number_format(@$normal_loan[$j]['interest'],2):'-';
		$data_1[$i][11] =  @$normal_loan[$j]['contract_number']!=''?number_format(@$normal_loan[$j]['outstanding_balance'] - @$normal_loan[$j]['principal_payment'],2):'-';
		$data_1[$i][12] =  @$emergent_loan[$j]['contract_number']!=''?@$emergent_loan[$j]['contract_number']:'-';
		$data_1[$i][13] =  @$emergent_loan[$j]['contract_number']!=''?number_format(@$emergent_loan[$j]['outstanding_balance'],2):'-';
		$data_1[$i][14] =  @$emergent_loan[$j]['contract_number']!=''?number_format(@$emergent_loan[$j]['principal_payment'],2):'-';
		$data_1[$i][15] =  @$emergent_loan[$j]['contract_number']!=''?number_format(@$emergent_loan[$j]['interest'],2):'-';
		$data_1[$i][16] =  @$emergent_loan[$j]['contract_number']!=''?number_format(@$emergent_loan[$j]['outstanding_balance'] - @$emergent_loan[$j]['principal_payment'],2):'-';
		$data_1[$i][17] =  '';
		
		$sum_normal_outstanding_balance += @$normal_loan[$j]['outstanding_balance'];
		$sum_normal_principal_payment += @$normal_loan[$j]['principal_payment'];
		$sum_normal_interest += @$normal_loan[$j]['interest'];
		$sum_normal_balance += @$normal_loan[$j]['outstanding_balance'] - @$normal_loan[$j]['principal_payment'];
		
		$sum_emergent_outstanding_balance += @$emergent_loan[$j]['outstanding_balance'];
		$sum_emergent_principal_payment += @$emergent_loan[$j]['principal_payment'];
		$sum_emergent_interest += @$emergent_loan[$j]['interest'];
		$sum_emergent_balance += @$emergent_loan[$j]['outstanding_balance'] - @$emergent_loan[$j]['principal_payment'];

	 } 
	$sum_share += $num_share*$share_value;
	$sum_share_collect += $row_share['share_collect_value']+($num_share*$share_value);
	
}



$sheet1 = 'รายงานสรุป';
$title = array(''=>'string',''=>'string',''=>'string',''=>'string',''=>'string',''=>'string',''=>'string',''=>'string',''=>'string',''=>'string',''=>'string',''=>'string',''=>'string',''=>'string',''=>'string',''=>'string',''=>'string');
$title1 = array("รายละเอียดการหักค่าหุ้น เงินกู้ฉุกเฉิน เงินกู้สามัญและเงินกู้พิเศษ เดือน ".@$month_arr[(int)@$_GET['month']]." ".@$_GET['year']." (รวมทั้งสิ้น ".($k-1)." ท่าน)");
$header = array("string","string","string","string","string","string","string","string","string","string","string","string","string","string","string","string","string");
$text_top1 = array("ลำดับ","เลขทะเบียน","รหัส","ชื่อ - สกุล","หน่วย","ส่งเงิน","รวมค่าหุ้น","เงินกู้สามัญ","","","","","เงินกู้ฉุกเฉิน","","","","","หมายเหตุ");
$text_top2 = array("ที่","สมาชิก","พนักงาน","","งาน","ค่าหุ้น","สะสม","สัญญาเลขที่","คงเหลือ","เงินต้น","ดอกเบี้ย","คงเหลือ","สัญญาเลขที่","คงเหลือ","เงินต้น","ดอกเบี้ย","คงเหลือ","");
//$writer->writeSheetHeader($sheet1, $header, $col_options = ['suppress_row'=>true] );
$writer->writeSheetHeader($sheet1, $title,$col_options = ['widths'=>[4.86,10.43,8.43,22.14,10.57,10.43,14.43,10.57,11.29,10.43,10.43,11.29,9.43,9.57,9,7.71,8.86,10]]);
$writer->writeSheetRow($sheet1, $title1,$titleStyle);
$writer->writeSheetRow($sheet1, $text_top1,$headerStyle1);
$writer->writeSheetRow($sheet1, $text_top2,$headerStyle2);

foreach($data_1 as $row){
	$writer->writeSheetRow($sheet1, $row,$textStyle);	
}

$data_2[0] =  '';
$data_2[1] =  '';
$data_2[2] =  '';
$data_2[3] =  '';
$data_2[4] =  '';
$data_2[5] = '';
$data_2[6] = '';
$data_2[7] = '';
$data_2[8] = '';
$data_2[9] = '';
$data_2[10] = '';
$data_2[11] = '';
$data_2[12] = '';
$data_2[13] = '';
$data_2[14] = '';
$data_2[15] = '';
$data_2[16] = '';
$data_2[17] = '';

$data_3[0] =  '';
$data_3[1] =  '';
$data_3[2] =  '';
$data_3[3] =  '';
$data_3[4] =  '';
$data_3[5] = number_format($sum_share,2);
$data_3[6] = number_format($sum_share_collect,2);
$data_3[7] = '-'; 
$data_3[8] = number_format($sum_normal_outstanding_balance,2);
$data_3[9] = number_format($sum_normal_principal_payment,2);
$data_3[10] = number_format($sum_normal_interest,2);
$data_3[11] = number_format($sum_normal_balance);
$data_3[12] = '-';
$data_3[13] = number_format($sum_emergent_outstanding_balance,2);
$data_3[14] = number_format($sum_emergent_principal_payment,2);
$data_3[15] = number_format($sum_emergent_interest,2);
$data_3[16] = number_format($sum_emergent_balance);
$data_3[17] = '';

$data_4[0] =  '';
$data_4[1] =  '';
$data_4[2] =  '';
$data_4[3] =  '';
$data_4[4] =  '';
$data_4[5] = '';
$data_4[6] = '';
$data_4[7] = ''; 
$data_4[8] = '';
$data_4[9] = number_format($sum_normal_principal_payment+$sum_normal_interest,2);
$data_4[10] = '';
$data_4[11] = '';
$data_4[12] = '';
$data_4[13] = '';
$data_4[14] = number_format($sum_emergent_principal_payment+$sum_emergent_interest,2);
$data_4[15] = '';
$data_4[16] = '';
$data_4[17] = '';

$data_5[0] =  '';
$data_5[1] =  '';
$data_5[2] =  '';
$data_5[3] =  '';
$data_5[4] =  '';
$data_5[5] = '';
$data_5[6] = '';
$data_5[7] = '';
$data_5[8] = '';
$data_5[9] = '';
$data_5[10] = '';
$data_5[11] = '';
$data_5[12] = '';
$data_5[13] = '';
$data_5[14] = '';
$data_5[15] = '';
$data_5[16] = '';
$data_5[17] = '';

$data_6[0] =  '';
$data_6[1] =  '';
$data_6[2] =  '';
$data_6[3] =  '';
$data_6[4] =  'รวมรับ';
$data_6[5] = number_format($sum_share,2);
$data_6[6] = '';
$data_6[7] = ''; 
$data_6[8] = 'รวมรับ';
$data_6[9] = number_format($sum_normal_principal_payment+$sum_normal_interest,2);
$data_6[10] = '';
$data_6[11] = '';
$data_6[12] = '';
$data_6[13] = 'รวมรับ';
$data_6[14] = number_format($sum_emergent_principal_payment+$sum_emergent_interest,2);
$data_6[15] = '';
$data_6[16] = 'รวมส่งหัก';
$data_6[17] = number_format($sum_share+$sum_normal_principal_payment+$sum_normal_interest+$sum_emergent_principal_payment+$sum_emergent_interest,2);

$data_7[0] =  '';
$data_7[1] =  '';
$data_7[2] =  '';
$data_7[3] =  '';
$data_7[4] =  '';
$data_7[5] = '';
$data_7[6] = '';
$data_7[7] = '';
$data_7[8] = '';
$data_7[9] = '';
$data_7[10] = '';
$data_7[11] = '';
$data_7[12] = '';
$data_7[13] = '';
$data_7[14] = '';
$data_7[15] = '';
$data_7[16] = '';
$data_7[17] = '';

$data_8[0] =  '';
$data_8[1] =  '';
$data_8[2] =  '';
$data_8[3] =  '';
$data_8[4] =  '';
$data_8[5] = '';
$data_8[6] = '';
$data_8[7] = ''; 
$data_8[8] = '';
$data_8[9] = '';
$data_8[10] = '';
$data_8[11] = '';
$data_8[12] = '';
$data_8[13] = 'คชจ.เบ็ดเตร็ด';
$data_8[14] = '';
$data_8[15] = '';
$data_8[16] = 'คชจ.เบ็ดเตร็ด';
$data_8[17] = '';

$data_9[0] =  ''; //A
$data_9[1] =  ''; //B
$data_9[2] =  ''; //C
$data_9[3] =  ''; //D
$data_9[4] =  ''; //E
$data_9[5] = ''; //F
$data_9[6] = ''; //G
$data_9[7] = ''; //H
$data_9[8] = 'กู้พิเศษ'; //I
$data_9[9] = number_format(@$loan_arr[2]['principal_payment'],2); //J
$data_9[10] = ''; //K
$data_9[11] = ''; //L
$data_9[12] = ''; //M
$data_9[13] = 'ฉุกเฉิน'; //N
$data_9[14] = number_format(@$loan_arr[3]['principal_payment'],2); //O
$data_9[15] = ''; //P
$data_9[16] = ''; //Q
$data_9[17] = ''; //R

$data_10[0] =  ''; //A
$data_10[1] =  ''; //B
$data_10[2] =  ''; //C
$data_10[3] =  ''; //D
$data_10[4] =  ''; //E
$data_10[5] = ''; //F
$data_10[6] = ''; //G
$data_10[7] = ''; //H
$data_10[8] = 'ดอกเบี้ย'; //I
$data_10[9] = number_format(@$loan_arr[2]['interest'],2); //J
$data_10[10] = ''; //K
$data_10[11] = ''; //L
$data_10[12] = ''; //M
$data_10[13] = 'ด/บ ฉุกเฉิน'; //N
$data_10[14] = number_format(@$loan_arr[3]['interest'],2); //O
$data_10[15] = ''; //P
$data_10[16] = ''; //Q
$data_10[17] = ''; //R

$data_11[0] =  ''; //A
$data_11[1] =  ''; //B
$data_11[2] =  ''; //C
$data_11[3] =  ''; //D
$data_11[4] =  ''; //E
$data_11[5] = ''; //F
$data_11[6] = ''; //G
$data_11[7] = ''; //H
$data_11[8] = 'สามัญ'; //I
$data_11[9] = number_format(@$loan_arr[1]['principal_payment'],2); //J
$data_11[10] = ''; //K
$data_11[11] = ''; //L
$data_11[12] = ''; //M
$data_11[13] = 'ฉุกเฉินพิเศษ'; //N
$data_11[14] = number_format(@$loan_arr[4]['principal_payment'],2); //O
$data_11[15] = ''; //P
$data_11[16] = ''; //Q
$data_11[17] = ''; //R

$data_12[0] = ''; //A
$data_12[1] = ''; //B
$data_12[2] = ''; //C
$data_12[3] = ''; //D
$data_12[4] = ''; //E
$data_12[5] = ''; //F
$data_12[6] = ''; //G
$data_12[7] = ''; //H
$data_12[8] = 'ด/บ สามัญ'; //I
$data_12[9] = number_format(@$loan_arr[1]['interest'],2); //J
$data_12[10] = ''; //K
$data_12[11] = ''; //L
$data_12[12] = ''; //M
$data_12[13] = 'ด/บ'; //N
$data_12[14] = number_format(@$loan_arr[4]['interest'],2); //O
$data_12[15] = ''; //P
$data_12[16] = ''; //Q
$data_12[17] = ''; //R
		
//foreach($data_2 as $row2){
//	$writer->writeSheetRow($sheet1, $row2);	
//}		
$writer->writeSheetRow($sheet1, $data_2);			
$writer->writeSheetRow($sheet1, $data_3,$textStyle3);			
$writer->writeSheetRow($sheet1, $data_4,$textStyle4);			
$writer->writeSheetRow($sheet1, $data_5);			
$writer->writeSheetRow($sheet1, $data_6,$textStyle6);			
$writer->writeSheetRow($sheet1, $data_7);			
$writer->writeSheetRow($sheet1, $data_8,$textStyle8);			
$writer->writeSheetRow($sheet1, $data_9,$textStyle9);			
$writer->writeSheetRow($sheet1, $data_10,$textStyle10);			
$writer->writeSheetRow($sheet1, $data_11,$textStyle11);			
$writer->writeSheetRow($sheet1, $data_12,$textStyle12);			
//echo '<pre>'; print_r($data_1); echo '</pre>';
//exit;

$writer->markMergedCell($sheet1, $start_row=1, $start_col=0, $end_row=1, $end_col=17);
$writer->markMergedCell($sheet1, $start_row=2, $start_col=7, $end_row=2, $end_col=11);
$writer->markMergedCell($sheet1, $start_row=2, $start_col=12, $end_row=2, $end_col=16);
//$end_co = จำนวน คอลัมภ์ ที่ Merge
//exit;
$filename = "example.xlsx";
header('Content-disposition: attachment; filename="'.XLSXWriter::sanitize_filename($filename).'"');
header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
header('Content-Transfer-Encoding: binary');
header('Cache-Control: must-revalidate');
header('Pragma: public');
$writer->writeToStdOut();

exit(0);
?>