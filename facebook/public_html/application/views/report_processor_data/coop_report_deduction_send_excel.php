<?php
header("Content-type: application/vnd.ms-excel;charset=utf-8;");
header("Content-Disposition: attachment; filename=รายงานการหักส่ง.xls"); 
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

	?>
			<table class="table table-bordered">
				<tr>
					<tr>
						<th class="table_title" colspan="10"><?php echo @$_SESSION['COOP_NAME'];?></th>
					</tr>
					<tr>
						<th class="table_title" colspan="10">รายงานการหักส่ง</th>
					</tr>
					<tr>
						<th class="table_title" colspan="10"><?php echo " ประจำ ".$title_date;?></th>
					</tr>
					<tr>
						<th class="table_title_right" colspan="10">วันที่ <?php echo $this->center_function->ConvertToThaiDate(@date('Y-m-d'),0,0);?></th>
					</tr>
					<tr>
						<th class="table_title_right" colspan="10">ผู้ทำรายการ <?php echo $_SESSION['USER_NAME'];?></th>
					</tr>
				</tr>
			</table>

            <table class="table table-bordered">
				<thead> 
                    <tr>
                        <th class="table_header_top" style="vertical-align: middle;">ลำดับ</th>
                        <th class="table_header_top" style="vertical-align: middle;">รหัสสมาชิก</th>
						<th class="table_header_top" style="vertical-align: middle;">ชื่อนามสกุล</th>
                        <th class="table_header_top" style="vertical-align: middle;">เลขที่บัตรประจำตัวประชาชน</th>
                        <th class="table_header_top" style="vertical-align: middle;">จำนวนเงิน</th>
                        <th class="table_header_top" style="vertical-align: middle;">รวม</th>
                        <th class="table_header_top" style="vertical-align: middle;">สังกัด</th>
                        <th class="table_header_top" style="vertical-align: middle;">คำนำหน้า</th>
                        <th class="table_header_top" style="vertical-align: middle;">ชื่อ</th>
                        <th class="table_header_top" style="vertical-align: middle;">นามสกุล</th>                        
                    </tr>
				</thead>
				<tbody>
            <?php
            $runno = 0;
            $mem_count = 0;
            $totals = array();
            $index = 0;
            $group_id_prev = "x";
            $member_id_prev = "x";
            $memberCount = 0;
            foreach($datas AS $mem_group_id => $mem_groups){
				$memcount = count($mem_groups["member"]);
				$mem_index_count = 0;
				$total = 0;
                foreach($mem_groups["member"] AS $member){
					$runno++;
					$mem_index_count++;
					$total += $member['amount'];
			?>
					<tr>
                        <th class="table_body" style="vertical-align: middle;"><?php echo $runno;?></th>
                        <th class="table_body" style="vertical-align: middle;mso-number-format:'\@'"><?php echo $member['member_id'];?></th>
						<th class="table_body" style="vertical-align: middle;"><?php echo $member['name'];?></th>
                        <th class="table_body" style="vertical-align: middle;mso-number-format:'\@'"><?php echo $member['id_card'];?></th>
                        <th class="table_body" style="vertical-align: middle;"><?php echo number_format($member['amount'],2);?></th>
                        <th class="table_body" style="vertical-align: middle;"><?php if($mem_index_count == $memcount)echo number_format($total,2);?></th>
                        <th class="table_body" style="vertical-align: middle;"><?php echo $mem_groups['mem_group_name'];?></th>
                        <th class="table_body" style="vertical-align: middle;"><?php echo $member['prename_short'];?></th>
                        <th class="table_body" style="vertical-align: middle;"><?php echo $member['firstname_th'];?></th>
                        <th class="table_body" style="vertical-align: middle;"><?php echo $member['lastname_th'];?></th>                        
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