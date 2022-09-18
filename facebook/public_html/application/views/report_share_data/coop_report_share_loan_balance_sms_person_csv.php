<?php 
header("Content-type: application/vnd.ms-excel;charset=utf-8;");
header("Content-Disposition: attachment; filename=SMS สรุปทุนเรือนหุ้น-เงินกู้คงเหลือ ตามรายบุคคล.csv"); 
date_default_timezone_set('Asia/Bangkok');
?>
<?php
	if(@$_GET['start_date']){
		$start_date_arr = explode('/',@$_GET['start_date']);
		$start_day = $start_date_arr[0];
		$start_month = $start_date_arr[1];
		$start_year = $start_date_arr[2];
		$monthtext = $month_short_arr[(int)$start_month];
	}
	foreach($datas as $data) {
		if(!empty($data['share_collect']) || !empty($data['normal']) || !empty($data['emergent']) || !empty($data['special'])) {
			echo $data['mobile'].",".'"'.$start_day.$monthtext.$start_year." เลขสมาชิก".(int)$data['member_id']." มีหุ้น".number_format(!empty($data['share_collect']) ? $data['share_collect']: 0,2)."บาท".'"';
			if(!empty($data['normal'])) {
				foreach($data['normal'] as $loan) {
					echo '"'."หนี้สัญญา".$loan['loan_emergent_contract_number']."คงเหลือ".number_format($loan['loan_emergent_balance'],2)."บาท".'"';
				}
			}
			if(!empty($data['special'])) {
				foreach($data['special'] as $loan) {
					echo '"'."หนี้สัญญา".$loan['loan_emergent_contract_number']."คงเหลือ".number_format($loan['loan_emergent_balance'],2)."บาท".'"';
				}
			}
			if(!empty($data['emergent'])) {
				foreach($data['emergent'] as $loan) {
					echo '"'."หนี้สัญญา".$loan['loan_emergent_contract_number']."คงเหลือ".number_format($loan['loan_emergent_balance'],2)."บาท".'"';
				}
			}
			echo "\n";
		}
	}
?>
