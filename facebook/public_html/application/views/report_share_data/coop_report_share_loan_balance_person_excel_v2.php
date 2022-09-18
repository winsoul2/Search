<?php
$writer = new XLSXWriter();
$styles_title = array( 'font'=>'AngsanaUPC','font-size'=>18,'font-style'=>'bold', 'halign'=>'center', 'valign'=>'middle' );
$styles_header = array( 'font'=>'Cordia New','font-size'=>16,'font-style'=>'bold', 'halign'=>'center', 'valign'=>'middle', 'border'=>'left,right,top,bottom' ,'border-style'=>'thin');
$styles_header2 = array( 'font'=>'Cordia New','font-size'=>16,'font-style'=>'bold', 'halign'=>array('center','left','center','right','center','right','right','right'), 'valign'=>'middle');

$styles_body_left = array( 'font'=>'CordiaUPC','font-size'=>14,'font-style'=>'normal', 'halign'=>'left','valign'=>'center', 'border'=>'left,right,top,bottom');
$styles_body_right = array( 'font'=>'CordiaUPC','font-size'=>14,'font-style'=>'normal', 'halign'=>'right','valign'=>'center', 'border'=>'left,right,top,bottom');
$styles_body_center = array( 'font'=>'CordiaUPC','font-size'=>14,'font-style'=>'normal', 'halign'=>'center','valign'=>'center', 'border'=>'left,right,top,bottom');
$styles_body = array( $styles_body_center, $styles_body_left, $styles_body_center, $styles_body_right, $styles_body_center, $styles_body_left , $styles_body_center , $styles_body_right);

$header = array(''=>'string',''=>'string',''=>'string',''=>'string',''=>'string',''=>'string',''=>'string',''=>'string');
//$end_co = จำนวน คอลัมภ์ ที่ Merge
foreach($data as $key => $value){
	$count_row=0;
	$writer->writeSheetHeader($key, $header, $col_options = ['widths'=>[10.57,33.43,9.57,11.71,10.57,33.43,9.57,11.71]] );
	
	$writer->writeSheetRow($key, array($_SESSION['COOP_NAME']), $styles_title );
	$writer->writeSheetRow($key, array('แยกประเภท'), $styles_title );
	
	$count_row++;
	$writer->markMergedCell($key, $start_row=$count_row, $start_col=0, $end_row=$count_row, $end_col=7);
	$count_row++;
	$writer->markMergedCell($key, $start_row=$count_row, $start_col=0, $end_row=$count_row, $end_col=7);
	$writer->writeSheetRow($key, array('', $value['account_chart'],'','','','เลขที่', $key,''), $styles_header2 );
	$count_row++;
	$writer->markMergedCell($key, $start_row=$count_row, $start_col=1, $end_row=$count_row, $end_col=2);
	$count_row++;
	for($i=0;$i<=7;$i++){
		if(!in_array($i,array('2','3','6','7'))){
			$writer->markMergedCell($key, $start_row=$count_row, $start_col=$i, $end_row=($count_row+1), $end_col=$i);
		}
	}
	$writer->writeSheetRow($key, array('วันที่','รายการ','หน้า','เดบิต','วันที่','รายการ','หน้า','เครดิต'), $styles_header );
	$writer->writeSheetRow($key, array('','','บัญชี','บาท','','','บัญชี','บาท'), $styles_header );
	$count_k=0;
	if(count(@$value['debit'])>count(@$value['credit'])){
		$count_k = count($value['debit']);
	}else{
		$count_k = count($value['credit']);
	}
	$debit_date = '';
	$credit_date = '';
	for($k=0;$k<$count_k;$k++){
		if($debit_date == date('Y-m-d',strtotime(@$value['debit'][$k]['account_datetime']))){
			$debit_date_show = '';
		}else{
			$debit_date_show = date('Y-m-d',strtotime(@$value['debit'][$k]['account_datetime']));
		}
		$debit_date = date('Y-m-d',strtotime(@$value['debit'][$k]['account_datetime']));
		if($credit_date == date('Y-m-d',strtotime(@$value['credit'][$k]['account_datetime']))){
			$credit_date_show = '';
		}else{
			$credit_date_show = date('Y-m-d',strtotime(@$value['credit'][$k]['account_datetime']));
		}
		$credit_date = date('Y-m-d',strtotime(@$value['credit'][$k]['account_datetime']));
		$writer->writeSheetRow($key, array(
			!empty($value['debit'][$k])?$debit_date_show!=''?$this->center_function->ConvertToThaiDate($debit_date_show,'1','0'):'':'',
			!empty($value['debit'][$k])?$value['debit'][$k]['account_chart']:'',
			'',
			!empty($value['debit'][$k])?" ".number_format($value['debit'][$k]['account_amount'],2):'',
			!empty($value['credit'][$k])?$credit_date_show!=''?$this->center_function->ConvertToThaiDate($credit_date_show,'1','0'):'':'',
			!empty($value['credit'][$k])?$value['credit'][$k]['account_chart']:'',
			'',
			!empty($value['credit'][$k])?" ".number_format($value['credit'][$k]['account_amount'],2):''
		), $styles_body );
	}
	$writer->writeSheetRow($key, array(
			'',
			'',
			'',
			$sum_debit[$key]!='0'?" ".number_format($sum_debit[$key],2):'',
			'',
			'',
			'',
			$sum_credit[$key]!='0'?" ".number_format($sum_credit[$key],2):''
		), $styles_body );
}

//$writer->writeToFile('xlsx-styles.xlsx');
//exit;
$filename = "รายงานสรุปทุนเรือนหุ้น-เงินกู้คงเหลือ  ตามรายบุคคล.xlsx";
header('Content-disposition: attachment; filename="'.XLSXWriter::sanitize_filename($filename).'"');
header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
header('Content-Transfer-Encoding: binary');
header('Cache-Control: must-revalidate');
header('Pragma: public');
$writer->writeToStdOut();
exit(0);