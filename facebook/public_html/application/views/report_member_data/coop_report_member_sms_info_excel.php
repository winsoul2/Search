<?php 
header("Content-type: application/vnd.ms-excel;charset=utf-8;");
header("Content-Disposition: attachment; filename=ข้อมูลส่ง SMS.xls"); 
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
			<table class="table table-bordered">
				<tbody>
				<?php
					foreach($datas as $data) {
				?>
					<tr>
						<td class="table_body" style="text-align: left;vertical-align: top;mso-number-format:'@';"><?php echo $data['member_id'];?></td>
						<td class="table_body" style="text-align: left;vertical-align: top;mso-number-format:'@';"><?php echo $data['mobile'];?></td>
						<td class="table_body" style="text-align: left;vertical-align: top;">
							<?php
								$address = "";
								$address .= !empty($data['address_no']) ? $data['address_no']." " : "";
								$address .= !empty($data['address_moo']) ? $data['address_moo']." " : "";
								$address .= !empty($data['address_village']) ? "หมู่บ้าน".$data['address_village']." " : "";
								$address .= !empty($data['address_soi']) ? $data['address_soi']." " : "";
								$address .= !empty($data['address_road']) ? $data['address_road']." " : "";
								if($data['province_code'] == '10') {
									$address .= !empty($data['district_name']) ? $data['district_name']." " : "";
									$address .= !empty($data['amphur_name']) ? $data['amphur_name']." " : "";
									$address .= !empty($data['province_name']) ? $data['province_name']." " : "";
								} else {
									$address .= !empty($data['district_name']) ? "ตำบล".$data['district_name']." " : "";
									$address .= !empty($data['amphur_name']) ? "อำเภอ".$data['amphur_name']." " : "";
									$address .= !empty($data['province_name']) ? "จังหวัด".$data['province_name']." " : "";
								}
								$address .= !empty($data['zipcode']) ? $data['zipcode'] : "";
								echo $address;
							?>
						</td>
					</tr>
				<?php
					}
				?>
				</tbody>    
			</table>
		</body>
	</html>
</pre>