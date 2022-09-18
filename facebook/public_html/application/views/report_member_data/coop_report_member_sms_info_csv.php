<?php 
header("Content-type: application/vnd.ms-excel;charset=utf-8;");
header("Content-Disposition: attachment; filename=SMS สรุปทุนเรือนหุ้น-เงินกู้คงเหลือ ตามรายบุคคล.csv"); 
date_default_timezone_set('Asia/Bangkok');
?>
<?php
	foreach($datas as $data) {
		echo $data['member_id'].",".$data['mobile'];
		echo "\n";
	}
?>
