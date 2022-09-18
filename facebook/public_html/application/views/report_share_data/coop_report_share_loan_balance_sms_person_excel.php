<?php 
header("Content-type: application/vnd.ms-excel;charset=utf-8;");
header("Content-Disposition: attachment; filename=SMS สรุปทุนเรือนหุ้น-เงินกู้คงเหลือ ตามรายบุคคล.xls"); 
date_default_timezone_set('Asia/Bangkok');
?>
<pre>
	<html>
		<head>
			<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
			<style>
				.num {
				  mso-number-format:General;
				}
				.text{
				  mso-number-format:"\@";/*force text*/ 
				}
				.text-center{
					text-align: center;
				}
				.text-left{
					text-align: left;
				}
				.table_title{
					font-family: AngsanaUPC, MS Sans Serif;
					font-size: 22px;
					font-weight: bold;
					text-align:center;
				}
				.table_title_right{
					font-family: AngsanaUPC, MS Sans Serif;
					font-size: 16px;
					font-weight: bold;
					text-align:right;
				}
				.table_header_top{
					font-family: AngsanaUPC, MS Sans Serif;
					font-size: 19px;
					font-weight: bold;
					text-align:center;
					border-top: thin solid black;
					border-left: thin solid black;
					border-right: thin solid black;
				}
				.table_header_mid{
					font-family: AngsanaUPC, MS Sans Serif;
					font-size: 19px;
					font-weight: bold;
					text-align:center;
					border-left: thin solid black;
					border-right: thin solid black;
				}
				.table_header_bot{
					font-family: AngsanaUPC, MS Sans Serif;
					font-size: 19px;
					font-weight: bold;
					text-align:center;
					border-bottom: thin solid black;
					border-left: thin solid black;
					border-right: thin solid black;
				}
				.table_header_bot2{
					font-family: AngsanaUPC, MS Sans Serif;
					font-size: 19px;
					font-weight: bold;
					text-align:center;
					border: thin solid black;
				}
				.table_body{
					font-family: AngsanaUPC, MS Sans Serif;
					font-size: 21px;
					border: thin solid black;
				}
				.table_body_right{
					font-family: AngsanaUPC, MS Sans Serif;
					font-size: 21px;
					border: thin solid black;
					text-align:right;
				}
			</style>
		</head>
		<body>
<?php
if(@$_GET['start_date']){
	$start_date_arr = explode('/',@$_GET['start_date']);
	$start_day = $start_date_arr[0];
	$start_month = $start_date_arr[1];
	$start_year = $start_date_arr[2];
	$monthtext = $month_short_arr[(int)$start_month];
}

	?>
				<table class="table table-bordered">
					<!-- <thead>
						<tr>
							<th class="table_header_top" style="width: 40px;vertical-align: middle;">หมายเลขโทรศัพท์</th>
							<th class="table_header_top" style="width: 160px;vertical-align: middle;">ข้อความ</th>
						</tr>
					</thead> -->
					<tbody>
					<?php
						foreach($datas as $data) {
							if(!empty($data['share_collect']) || !empty($data['normal']) || !empty($data['emergent']) || !empty($data['special'])) {
					?>
						<tr>
							<td class="table_body" style="text-align: left;vertical-align: top;mso-number-format:'@';"><?php echo $data['mobile']?></td>
							<td class="table_body" style="text-align: left;vertical-align: top; width: 500px">
								<?php echo $start_day.$monthtext.$start_year." เลขสมาชิก".(int)$data['member_id']." มีหุ้น".number_format(!empty($data['share_collect']) ? $data['share_collect']: 0,2)."บาท";
									if(!empty($data['normal'])) {
										foreach($data['normal'] as $loan) {
											echo ' '."หนี้สัญญา".$loan['loan_emergent_contract_number']."คงเหลือ".number_format($loan['loan_emergent_balance'],2)."บาท";
										}
									}
									if(!empty($data['special'])) {
										foreach($data['special'] as $loan) {
											echo ' '."หนี้สัญญา".$loan['loan_emergent_contract_number']."คงเหลือ".number_format($loan['loan_emergent_balance'],2)."บาท";
										}
									}
									if(!empty($data['emergent'])) {
										foreach($data['emergent'] as $loan) {
											echo ' '."หนี้สัญญา".$loan['loan_emergent_contract_number']."คงเหลือ".number_format($loan['loan_emergent_balance'],2)."บาท";
										}
									}
								?>
							</td>
						</tr>
					<?php
							}
						}
					?>
					</tbody>    
				</table>
		</body>
	</html>
</pre>