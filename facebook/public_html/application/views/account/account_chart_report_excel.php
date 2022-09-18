<?php
function U2T($text) { return @iconv("UTF-8", "TIS-620//IGNORE", trim($text)); }

$writer = new XLSXWriter();
$styles_title = array( 'font'=>'AngsanaUPC','font-size'=>18,'font-style'=>'bold', 'halign'=>'center', 'valign'=>'middle' );
$styles_header = array( 'font'=>'Cordia New','font-size'=>16,'font-style'=>'bold', 'halign'=>'center', 'valign'=>'middle', 'border'=>'left,right,top,bottom' ,'border-style'=>'thin');
$styles_header2 = array( 'font'=>'Cordia New','font-size'=>16,'font-style'=>'bold', 'halign'=>array('center','left','center','right','center','right','right'), 'valign'=>'middle');

$styles_body_left = array( 'font'=>'CordiaUPC','font-size'=>14,'font-style'=>'normal', 'halign'=>'left','valign'=>'center', 'border'=>'left,right,top,bottom');
$styles_body_right = array( 'font'=>'CordiaUPC','font-size'=>14,'font-style'=>'normal', 'halign'=>'right','valign'=>'center', 'border'=>'left,right,top,bottom');
$styles_body_center = array( 'font'=>'CordiaUPC','font-size'=>14,'font-style'=>'normal', 'halign'=>'center','valign'=>'center', 'border'=>'left,right,top,bottom');
$styles_body = array( $styles_body_center, $styles_body_left, $styles_body_left, $styles_body_right, $styles_body_left, $styles_body_left , $styles_body_left );
$month_short_arr = array('1'=>'ม.ค.','2'=>'ก.พ.','3'=>'มี.ค.','4'=>'เม.ย.','5'=>'พ.ค.','6'=>'มิ.ย.','7'=>'ก.ค.','8'=>'ส.ค.','9'=>'ก.ย.','10'=>'ต.ค.','11'=>'พ.ย.','12'=>'ธ.ค.');

$header = array(''=>'string',' '=>'string','  '=>'string','   '=>'string','    '=>'string','     '=>'string');

//$end_co = จำนวน คอลัมภ์ ที่ Merge
$data_filter = array();
$data_filter_fi = array();

$number_count = 0;
$i_all = 1;
$count_row=0;

foreach($account_chart_main as $key => $value){
    if($data[$key]){
        $key_sheet = 'Main';
        $writer->writeSheetHeader($key_sheet, $header, $col_options = ['widths'=>[14,20,60,20,20,20,20]] );
        if($i_all == 1) {
            $period = "";
            if(!empty($_GET["from_date"])) {
                if($_GET["from_date"] != $_GET["thru_date"]) $period .= "ตั้งแต่";
                $period .= 'วันที่ '.$this->center_function->ConvertToThaiDate($s_date,false,'0');
                if($_GET["from_date"] != $_GET["thru_date"]) $period .= ' ถึง วันที่ '.$this->center_function->ConvertToThaiDate($e_date,false,'0');
            } else {
                $period .= "ตั้งแต่";
                $period .= 'วันที่ '.$this->center_function->ConvertToThaiDate($s_date,false,'0');
                $period .= ' ถึง วันที่ '.$this->center_function->ConvertToThaiDate($e_date,false,'0');
            }

            $writer->writeSheetRow($key_sheet, array($_SESSION['COOP_NAME']), $styles_title);
            $writer->writeSheetRow($key_sheet, array('บัญชีแยกประเภททั่วไป'), $styles_title);
            $writer->writeSheetRow($key_sheet, array($period), $styles_title);
            $writer->writeSheetRow($key_sheet, array(''), $styles_title);
            $writer->writeSheetRow($key_sheet, array("ชื่อบัญชี".$account_chart_main[$key],'','','','','','เลขที่บัญชี '.$key), $styles_header2 );
            $count_row++;
            $writer->markMergedCell($key_sheet, $start_row=$count_row, $start_col=0, $end_row=$count_row, $end_col=6);
            $count_row++;
            $writer->markMergedCell($key_sheet, $start_row=$count_row, $start_col=0, $end_row=$count_row, $end_col=6);
            $count_row++;
            $writer->markMergedCell($key_sheet, $start_row=$count_row, $start_col=0, $end_row=$count_row, $end_col=6);
            $writer->writeSheetRow($key_sheet, array('','','','','','',''), $styles_header2 );
            $count_row++;
            $writer->markMergedCell($key_sheet, $start_row=$count_row, $start_col=1, $end_row=$count_row, $end_col=1);
            $count_row++;
            $writer->markMergedCell($key_sheet, $start_row=$count_row, $start_col=1, $end_row=$count_row, $end_col=1);
            $count_row++;
            $writer->markMergedCell($key_sheet, $start_row=$count_row, $start_col=1, $end_row=$count_row, $end_col=1);
            $count_row++;
        }else{
            $writer->writeSheetRow($key_sheet, array(''), $styles_title);
            $writer->writeSheetRow($key_sheet, array("ชื่อบัญชี".$account_chart_main[$key], '','','','','','เลขที่ '.$key), $styles_header2 );
            $writer->writeSheetRow($key_sheet, array('','','','','','',''), $styles_header2 );
            $count_row++;
            $count_row++;
            $count_row++;
        }
        $i_all++;

        $writer->writeSheetRow($key_sheet, array('ว.ด.ป.','เลขที่อ้างอิง','รายการ','เดบิต','เครดิต','คงเหลือ',''), $styles_header );
        $writer->writeSheetRow($key_sheet, array('','','','','','เดบิต','เครดิต'), $styles_header );
        $writer->markMergedCell($key_sheet, $start_row=$count_row, $start_col=5, $end_row=$count_row, $end_col=6);
        $writer->markMergedCell($key_sheet, $start_row=$count_row, $start_col=0, $end_row=$count_row+1, $end_col=0);
        $writer->markMergedCell($key_sheet, $start_row=$count_row, $start_col=1, $end_row=$count_row+1, $end_col=1);
        $writer->markMergedCell($key_sheet, $start_row=$count_row, $start_col=2, $end_row=$count_row+1, $end_col=2);
        $writer->markMergedCell($key_sheet, $start_row=$count_row, $start_col=3, $end_row=$count_row+1, $end_col=3);
        $writer->markMergedCell($key_sheet, $start_row=$count_row, $start_col=4, $end_row=$count_row+1, $end_col=4);
        $count_row++;
        $count_row++;

        $debit_date = '';
        $credit_date = '';

        $debit_balance = ($account_balances[$key]['type'] == 'debit' && $account_balances[$key]['amount'] > 0) ? $account_balances[$key]['amount']
                            : ($account_balances[$key]['type'] == 'credit' && $account_balances[$key]['amount'] <  0 ? $account_balances[$key]['amount'] * (-1) : "");
        $credit_balance = ($account_balances[$key]['type'] == 'credit' && $account_balances[$key]['amount'] > 0) ? $account_balances[$key]['amount']
                            : ($account_balances[$key]['type'] == 'debit' && $account_balances[$key]['amount'] <  0 ? $account_balances[$key]['amount'] * (-1) : "");

        $type_budget = $account_balances[$key]['type'] ? $account_balances[$key]['type'] : '';

        $writer->writeSheetRow($key_sheet, array(
            '',
            '',
            'ยอดยกมา',
            '',
            '',
            !empty($debit_balance) ? number_format($debit_balance,2) : ($type_budget == "debit" ? "0.00" : ""),
            !empty($credit_balance) ? number_format($credit_balance,2) : ($type_budget == "credit" ? "0.00" : ""),
        ), $styles_body);
        $count_row++;

        $budget_amount_sum = $account_balances[$key]['amount'];

        $date_last_next  = '';
        $sum_debit = 0;
        $sum_credit = 0;
        foreach($data[$key] as $key_main => $value_detail) {
            if($type_budget == ''){
                $type_budget = $value_detail['account_type'];
            }
            if($value_detail['account_type'] != $type_budget){
                $budget_amount_sum  =  $budget_amount_sum  - $value_detail['account_amount'];
            }else{
                $budget_amount_sum  =  $budget_amount_sum  + $value_detail['account_amount'];
            }
            if($budget_amount_sum <= 0 ){
                $budget_amount_sum = $budget_amount_sum * (-1);

                if($type_budget == 'credit'){
                    $type_budget = 'debit';
                }else{
                    $type_budget = 'credit';
                }
            }
            $debit_date_show = date('Y-m-d', strtotime(@$value_detail['account_datetime']));

            $writer->writeSheetRow($key_sheet, array(
                $debit_date_show != '' ? $this->center_function->ConvertToThaiDate($debit_date_show, '1', '0') : '',
                $value_detail["journal_ref"]."-".$value_detail['seq_no'],
                !empty($value_detail['account_chart']) ? $value_detail['account_chart'] : '',
                ($value_detail['account_type'] == 'debit' ) ? number_format($value_detail['account_amount'],2) : '',
                ($value_detail['account_type'] == 'credit') ? number_format($value_detail['account_amount'],2) : '',
                $type_budget == 'debit' ? number_format($budget_amount_sum,2) : '',
                $type_budget == 'credit' ? number_format($budget_amount_sum,2) : '',
            ), $styles_body);
            $count_row++;

            if($value_detail['account_type'] == 'debit') {
                $sum_debit += $value_detail['account_amount'];
            } else {
                $sum_credit += $value_detail['account_amount'];
            }

            $date_last_next  = $debit_date_show;

        }
        if($_GET['month'] == 12 ){
            $next_m = 1;
            $next_y =  $year+1;
        }else{
            $next_m = $_GET['month']+1;
            $next_y =  $year;
        }
        $writer->writeSheetRow($key_sheet, array(
            'รวม',
            '',
            '',
            number_format($sum_debit,2),
             number_format($sum_credit,2),
            '',
            '',
        ), $styles_body );
        $writer->markMergedCell($key_sheet, $start_row=$count_row, $start_col=0, $end_row=$count_row, $end_col=2);
        $count_row++;

        $debit_budget = "";
		$credit_budget = "";
		if(!empty($budget_amount_sum)) {
			$debit_budget = $type_budget == 'debit' ? number_format($budget_amount_sum,2) : "";
			$credit_budget = $type_budget == 'credit' ? number_format($budget_amount_sum,2) : "";
		} else {
			$group_id = substr($key,0,1);
			if($group_id == 1 || $group_id == 5) {
				$debit_budget = "0.00";
			} else {
				$credit_budget = "0.00";
			}
        }

        $writer->writeSheetRow($key_sheet, array(
            'ยอดยกไป',
            '',
            '',
            '',
            '',
            $debit_budget,
            $credit_budget,
        ), $styles_body );
        $writer->markMergedCell($key_sheet, $start_row=$count_row, $start_col=0, $end_row=$count_row, $end_col=2);
        $count_row++;

    }
}

$filename = "รายงานบัญชีแยกประเภท.xlsx";
header('Content-disposition: attachment; filename="'.XLSXWriter::sanitize_filename($filename).'"');
header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
header('Content-Transfer-Encoding: binary');
header('Cache-Control: must-revalidate');
header('Pragma: public');
$writer->writeToStdOut();
exit(0);