<?php
header("Content-type: application/vnd.ms-excel;charset=utf-8;");
header("Content-Disposition: attachment; filename=รายงานการส่ง-หักเงินเดือน(รายละเอียดรายบุคคล).xls"); 
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
						<th class="table_title" colspan="27"><?php echo @$_SESSION['COOP_NAME'];?></th>
					</tr>
					<tr>
						<th class="table_title" colspan="27">รายงานการส่ง-หักเงินเดือน(รายละเอียดรายบุคคล)</th>
					</tr>
					<tr>
						<th class="table_title" colspan="27"><?php echo " ประจำ ".$title_date;?></th>
					</tr>
					<tr>
						<th class="table_title_right" colspan="27">วันที่ <?php echo $this->center_function->ConvertToThaiDate(@date('Y-m-d'),0,0);?></th>
					</tr>
					<tr>
						<th class="table_title_right" colspan="27">ผู้ทำรายการ <?php echo $_SESSION['USER_NAME'];?></th>
					</tr>
				</tr>
			</table>

            <table class="table table-view table-center">
                <thead> 
                    <tr>
                        <th class="table_header_top" rowspan="2" style="width: 40px;vertical-align: middle;">ลำดับ</th>
                        <th class="table_header_top" rowspan="2" style="width: 40px;vertical-align: middle;">หน่วยงานย่อย</th>
                        <th class="table_header_top" rowspan="2" style="width: 40px;vertical-align: middle;">เลขที่บัตรประจำตัวประชาชน</th>
                        <th class="table_header_top" rowspan="2" style="width: 40px;vertical-align: middle;">เลขที่สมาชิก</th>
                        <th class="table_header_top" rowspan="2" style="width: 300px;vertical-align: middle;">คำนำหน้า</th>
                        <th class="table_header_top" rowspan="2" style="width: 300px;vertical-align: middle;">ชื่อ</th>
                        <th class="table_header_top" rowspan="2" style="width: 300px;vertical-align: middle;">นามสกุล</th>
                        <th class="table_header_top" rowspan="2" style="width: 80px;vertical-align: middle;">หุ้น</th>
                        <?php 
                            foreach($loan_type AS $key=>$row_loan_type){
                        ?>
                        <th class="table_header_top" colspan="4" style="width: 200px;vertical-align: middle;"><?php echo str_replace('เงินกู้','',$row_loan_type['loan_type']);?></th> 
                        <?php
                            }
                        ?>
                        <th class="table_header_top" colspan="2" style="width: 80px;vertical-align: middle;">เงินฝาก</th> 
                        <th class="table_header_top" rowspan="2" style="width: 80px;vertical-align: middle;">ฌสอ สป</th> 
                        <th class="table_header_top" rowspan="2" style="width: 80px;vertical-align: middle;">ค่าธรรมเนียมแรกเข้า</th> 
                        <th class="table_header_top" rowspan="2" style="width: 80px;vertical-align: middle;">อื่นๆ</th> 
                        <th class="table_header_top" rowspan="2" style="width: 80px;vertical-align: middle;">ชำระหนี้ค้ำประกัน</th> 
                        <th class="table_header_top" rowspan="2" style="width: 80px;vertical-align: middle;">รวม</th> 
                    </tr>
                    <tr>
                        <?php 
                            foreach($loan_type AS $key=>$row_loan_type){
                        ?>
                        <th class="table_header_top" style="width: 15px;vertical-align: middle;">งวด</th>
                        <th class="table_header_top" style="width: 180px;vertical-align: middle;">เลขที่สัญญา</th>
                        <th class="table_header_top" style="width: 80px;vertical-align: middle;">เงินต้น</th> 
                        <th class="table_header_top" style="width: 80px;vertical-align: middle;">ดอกเบี้ย</th> 
                        <?php
                            }
                        ?>
                        <th class="table_header_top" style="width: 80px;vertical-align: middle;">เลขที่บัญชี</th> 
                        <th class="table_header_top" style="width: 80px;vertical-align: middle;">จำนวนเงิน</th> 
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
                foreach($mem_groups["member"] AS $member){
                    
                    $normal_nums = array();
                    if(!empty($member['8'])) {
                        foreach($member['8'] as $contract_number => $val) {
                            $normal_nums[] = $contract_number;
                        }
                    }
                    $emergent_nums = array();
                    if(!empty($member['7'])) {
                        foreach($member['7'] as $contract_number => $val) {
                            $emergent_nums[] = $contract_number;
                        }
                    }
                    $special_num = array();
                    if(!empty($member['9'])) {
                        foreach($member['9'] as $contract_number => $val) {
                            $special_num[] = $contract_number;
                        }
                    }
                    $deposit_num = array();
                    if(!empty($member['DEPOSIT'])) {
                        foreach($member['DEPOSIT'] as $account_id => $val) {
                            $deposit_num[] = $account_id;
                        }
                    }
                    $normalSize = count($normal_nums);
                    $emergentSize = count($emergent_nums);
                    $specialSize = count($special_num);
                    $depositSize = count($deposit_num);
                    $max_loan_index = max($normalSize, $emergentSize, $specialSize, $depositSize) > 0 ? max($normalSize, $emergentSize, $specialSize,$depositSize) : 1;
					if(max($normalSize, $emergentSize, $specialSize, $depositSize) > 0 || $member['SHARE']!='' || $member['REGISTER_FEE']!='' || $member['CREMATION']!='' || $member['OTHER']!=''
						|| $member['GUARANTEE_AMOUNT']!='') {
                    for($i = 0; $i < $max_loan_index; $i++) {
                        $mem_count++;

            if ($group_id_prev != $mem_group_id) {
                // $runno = 0;
                // $index = 0;
                // $memberCount = 0;
                // $totals = array();
			?>
						<!-- <tr><td colspan="23" style="text-align: left;"><?php echo $mem_groups['mem_group_name']; ?></td></tr> -->
			<?php
					// $index++;
				}
				if($member_id_prev != $member['member_id']) {
					$memberCount++;
					$member_id_prev = $member['member_id'];
				}
				$runno++;
				
				$group_id_prev = $mem_group_id;
				$totals['member_count']++;


			?>
								<tr> 
									<td class="table_body" style="text-align: center;"><?php echo $runno; ?></td>
									<td class="table_body" style="text-align: center;"><?php echo $mem_groups['mem_group_name']; ?></td>
									<td class="table_body" style="text-align: center;"><?php echo $member['id_card']; ?></td>
									<td class="table_body" style="text-align: center;"><?php echo $member['member_id']; ?></td>
									<td class="table_body" style="text-align: left;"><?php echo $member['prename_short']; ?></td>						 
									<td class="table_body" style="text-align: left;"><?php echo $member['firstname_th']; ?></td>						 
									<td class="table_body" style="text-align: left;"><?php echo $member['lastname_th']; ?></td>						 
									<td class="table_body" style="text-align: left;"><?php echo $member['SHARE']!='' && $i == 0 ? number_format($member['SHARE'],2):''; ?></td>						 

			<?php
					//Emergent
					if (!empty($emergent_nums[$i])) {
						$totals['principal_7'] += $member['7'][$emergent_nums[$i]]['principal']!='' ? $member['7'][$emergent_nums[$i]]['principal'] : 0;
						$totals['interest_7'] += $member['7'][$emergent_nums[$i]]['interest']!='' ?	$member['7'][$emergent_nums[$i]]['interest'] : 0;
						$totals['count_7']++;
			?>
									<td class="table_body" style="text-align: center;"><?php echo $member['7'][$emergent_nums[$i]]['period']?></td> 					 
									<td class="table_body" style="text-align: center;"><?php echo $member['7'][$emergent_nums[$i]]['contract_number'];?></td> 					 
									<td class="table_body" style="text-align: right;"><?php echo $member['7'][$emergent_nums[$i]]['principal']!=''?number_format($member['7'][$emergent_nums[$i]]['principal'],2):''; ?></td> 					 
									<td class="table_body" style="text-align: right;"><?php echo $member['7'][$emergent_nums[$i]]['interest']!=''?number_format($member['7'][$emergent_nums[$i]]['interest'],2):'';?></td>
			<?php
					} else {
			?>
									<td class="table_body" style="text-align: center;"></td> 					 
									<td class="table_body" style="text-align: center;"></td> 					 
									<td class="table_body" style="text-align: right;"></td> 					 
									<td class="table_body" style="text-align: right;"></td>
			<?php
					}
					//Normal
					if (!empty($normal_nums[$i])) {
						$totals['principal_8'] += $member['8'][$normal_nums[$i]]['principal']!='' ? $member['8'][$normal_nums[$i]]['principal'] : 0;
						$totals['interest_8'] += $member['8'][$normal_nums[$i]]['interest']!='' ?	$member['8'][$normal_nums[$i]]['interest'] : 0;
						$totals['count_8']++;
			?>
									<td class="table_body" style="text-align: center;"><?php echo $member['8'][$normal_nums[$i]]['period']?></td> 					 
									<td class="table_body" style="text-align: center;"><?php echo $member['8'][$normal_nums[$i]]['contract_number'];?></td> 					 
									<td class="table_body" style="text-align: right;"><?php echo $member['8'][$normal_nums[$i]]['principal']!=''?number_format($member['8'][$normal_nums[$i]]['principal'],2):''; ?></td> 					 
									<td class="table_body" style="text-align: right;"><?php echo $member['8'][$normal_nums[$i]]['interest']!=''?number_format($member['8'][$normal_nums[$i]]['interest'],2):'';?></td>
			<?php
					} else {
			?>
									<td class="table_body" style="text-align: center;"></td> 					 
									<td class="table_body" style="text-align: center;"></td> 					 
									<td class="table_body" style="text-align: right;"></td> 					 
									<td class="table_body" style="text-align: right;"></td>
			<?php
					}
					//Special
					if (!empty($special_num[$i])) {
						$totals['principal_9'] += $member['9'][$special_num[$i]]['principal']!='' ? $member['9'][$special_num[$i]]['principal'] : 0;
						$totals['interest_9'] += $member['9'][$special_num[$i]]['interest']!='' ?	$member['9'][$special_num[$i]]['interest'] : 0;
						$totals['count_9']++;
			?> 
									<td class="table_body" style="text-align: center;"><?php echo $member['9'][$special_num[$i]]['period']?></td> 					 
									<td class="table_body" style="text-align: center;"><?php echo $member['9'][$special_num[$i]]['contract_number'];?></td> 					 
									<td class="table_body" style="text-align: right;"><?php echo $member['9'][$special_num[$i]]['principal']!=''?number_format($member['9'][$special_num[$i]]['principal'],2):''; ?></td> 					 
									<td class="table_body" style="text-align: right;"><?php echo $member['9'][$special_num[$i]]['interest']!=''?number_format($member['9'][$special_num[$i]]['interest'],2):'';?></td>
			<?php
					} else {
			?>
									<td class="table_body" style="text-align: center;"></td> 					 
									<td class="table_body" style="text-align: center;"></td> 					 
									<td class="table_body" style="text-align: right;"></td> 					 
									<td class="table_body" style="text-align: right;"></td>
			<?php
					}
					//Deposit
					if (!empty($deposit_num[$i])) {
			?> 
									<td class="table_body" style="text-align: center;"><?php echo$member['DEPOSIT'][$deposit_num[$i]]['account_id'];?></td> 					 
									<td class="table_body" style="text-align: right;"><?php echo $member['DEPOSIT'][$deposit_num[$i]]['pay_amount']!='' && $i == 0?number_format($member['DEPOSIT'][$deposit_num[$i]]['pay_amount'],2):'';?></td>
			<?php
						$totals['DEPOSIT'] += $member['DEPOSIT'][$deposit_num[$i]]['pay_amount']!='' && $i == 0 ? $member['DEPOSIT'][$deposit_num[$i]]['pay_amount'] : 0;
					} else {
			?>
									<td class="table_body" style="text-align: center;"></td> 					 
									<td class="table_body" style="text-align: right;"></td>

			<?php
					}
			?> 

									<td class="table_body" style="text-align: right;"><?php echo $member['CREMATION']!='' && $i == 0?number_format($member['CREMATION'],2):'';?></td> 					 
									<td class="table_body" style="text-align: right;"><?php echo $member['REGISTER_FEE']!='' && $i == 0?number_format($member['REGISTER_FEE'],2):'';?></td> 					 
									<td class="table_body" style="text-align: right;"><?php echo $member['OTHER']!='' && $i == 0?number_format($member['OTHER'],2):'';?></td> 					 
									<td class="table_body" style="text-align: right;"><?php echo $member['GUARANTEE_AMOUNT']!='' && $i == 0?number_format($member['GUARANTEE_AMOUNT'],2):'';?></td> 				 
									<td class="table_body" style="text-align: right;"><?php echo $member['sum_all']!='' && $i == 0 ?number_format($member['sum_all'],2):'';?></td> 							 
								</tr>										
			<?php
					$totals['SHARE'] += $member['SHARE']!='' && $i == 0 ? $member['SHARE'] : 0;
					$totals['CREMATION'] += $member['CREMATION']!='' && $i == 0 ? $member['CREMATION'] : 0;
					$totals['REGISTER_FEE'] += $member['REGISTER_FEE']!='' && $i == 0 ? $member['REGISTER_FEE'] : 0;
					$totals['OTHER'] += $member['OTHER']!='' && $i == 0 ? $member['OTHER'] : 0;
					$totals['GUARANTEE_AMOUNT'] += $member['GUARANTEE_AMOUNT']!='' && $i == 0 ? $member['GUARANTEE_AMOUNT'] : 0;
					$totals['sum_all'] += $member['sum_all']!='' && $i == 0 ? $member['sum_all'] : 0;

                                $index++;
                            }
                        }
                    }
            }
			?>
                    <tr>
                        <td class="table_body" style="text-align: center;" colspan="7"><?php echo "รวม ".number_format($memberCount)." คน";?></td>
                        <td class="table_body" style="text-align: center;"><?php echo !empty($totals['SHARE']) ? number_format($totals['SHARE'],2) : "";?></td>
                        <td class="table_body" style="text-align: center;" colspan="2"><?php echo !empty($totals['count_7']) ? number_format($totals['count_7'])." สัญญา": "";?></td>
                        <td class="table_body" style="text-align: right;"><?php echo !empty($totals['principal_7']) ? number_format($totals['principal_7'],2) : "";?></td>
                        <td class="table_body" style="text-align: right;"><?php echo !empty($totals['principal_7']) ? number_format($totals['interest_7'],2) : "";?></td>
                        <td class="table_body" style="text-align: center;" colspan="2"><?php echo !empty($totals['count_8']) ? number_format($totals['count_8'])." สัญญา": "";?></td>
                        <td class="table_body" style="text-align: right;"><?php echo !empty($totals['principal_8']) ? number_format($totals['principal_8'],2) : "";?></td>
                        <td class="table_body" style="text-align: right;"><?php echo !empty($totals['principal_8']) ? number_format($totals['interest_8'],2) : "";?></td>
                        <td class="table_body" style="text-align: center;" colspan="2"><?php echo !empty($totals['count_9']) ? number_format($totals['count_9']." สัญญา"): "";?></td>
                        <td class="table_body" style="text-align: right;"><?php echo !empty($totals['principal_9']) ? number_format($totals['principal_9'],2) : "";?></td>
                        <td class="table_body" style="text-align: right;"><?php echo !empty($totals['principal_9']) ? number_format($totals['interest_9'],2) : "";?></td>
                        <td class="table_body" style="text-align: right;"></td> 					 
                        <td class="table_body" style="text-align: right;"><?php echo !empty($totals['DEPOSIT']) ? number_format($totals['DEPOSIT'],2) : "";?></td> 					 
                        <td class="table_body" style="text-align: right;"><?php echo !empty($totals['CREMATION']) ? number_format($totals['CREMATION'],2) : "";?></td> 					 
                        <td class="table_body" style="text-align: right;"><?php echo !empty($totals['REGISTER_FEE']) ? number_format($totals['REGISTER_FEE'],2) : "";?></td> 					 
                        <td class="table_body" style="text-align: right;"><?php echo !empty($totals['OTHER']) ? number_format($totals['OTHER'],2) : "";?></td> 					 
                        <td class="table_body" style="text-align: right;"><?php echo !empty($totals['GUARANTEE_AMOUNT']) ? number_format($totals['GUARANTEE_AMOUNT'],2) : "";?></td> 					 
                        <td class="table_body" style="text-align: right;"><?php echo !empty($totals['sum_all']) ? number_format($totals['sum_all'],2) : "";?></td> 						 
                    </tr>
                </tbody>
			</table>
		</body>
	</html>
</pre>