<?php
if(!isset($_GET['debug'])) {
	header("Content-type: application/vnd.ms-excel;charset=utf-8;");
	header("Content-Disposition: attachment; filename=".($title=="" ? "export" : $title).".xls");
	date_default_timezone_set('Asia/Bangkok');
}

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
				<tr>
					<tr>
						<th class="table_title" colspan="8"><?php echo $title; ?></th>
					</tr>
				</tr>
			</table>
			<?php
				if($_GET['sec'] == 'surcharge') $caption_01 = 'เก็บเพิ่ม';
				else  $caption_01 = 'เงินคืน';
			?>
			<table class="table table-bordered">
				<thead>
					<tr>
						<th class="table_header_top" style="vertical-align: middle;">เลขที่บัญชี</th>
						<th class="table_header_top" style="vertical-align: middle;">รหัสสมาชิก</th>
						<th class="table_header_top" style="vertical-align: middle;">ชื่อสมาชิก</th>
						<th class="table_header_top" style="vertical-align: middle;">เลขที่สัญญา</th>
						<th class="table_header_top" style="vertical-align: middle;">เงินต้น</th>
						<th class="table_header_top" style="vertical-align: middle;">ดอกเบี้ย</th>
						<th class="table_header_top" style="vertical-align: middle;"><?php echo $caption_01; ?></th>
						<th class="table_header_top" style="vertical-align: middle;">สถานะ</th>
						<?php
							if( sizeof($col) > 0 ){
								foreach ($col as $key => $value) {
									?>
										<th class="table_header_top" style="vertical-align: middle;"><?=$value?></th>
									<?php
								}
							}
						?>
					</tr>
				</thead>
				<tbody>
          <?php foreach($datas as $key => $row) { ?>
          <tr>
            <td class="table_body"><?php echo $row['account_id']; ?></td>
            <td class="table_body"><?php echo $row['member_id']; ?></td>
            <td class="table_body"><?php echo $row['account_name']; ?></td>
            <td class="table_body"><?php echo $row['contract_number']; ?></td>
            <td class="table_body"><?php echo $row['principal']; ?></td>
            <td class="table_body"><?php echo $row['interest']; ?></td>
            <td class="table_body"><?php echo $row['return_interest_amount']; ?></td>
            <td class="table_body"><?php echo $row['return_time']; ?></td>
			<?php
				if( sizeof($col) > 0 ){
					?>
						<td class="table_body"><?php echo $row['remark']; ?></td>
					<?php
				}
			?>
          </tr>
          <?php } ?>
				</tbody>
			</table>
		</body>
	</html>
</pre>