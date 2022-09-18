<style>
	.table-view>thead, .table-view>thead>tr>td, .table-view>thead>tr>th {
		font-size: 14px;
	}	
	.table {
		color: #000;
	}	
	@page { size: landscape; }
	.underline{
		text-decoration: underline;
	}
	@media print {
		.pagination {
			display: none;
		}
	}
</style>
	<?php
	if(!empty($datas)){
		$first_page_size = 16;
		$page_size = 20;
		$page = 1;
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
					if ($index == 0 || $index == $first_page_size || ( $index > $first_page_size && (($index-$first_page_size) % $page_size) == 0 ) || $group_id_prev != $mem_group_id) {
	?>
			<div style="width: 1500px;"  class="page-break">
				<div class="panel panel-body" style="padding-top:10px !important;min-height: 950px;">
					<table style="width: 100%;">
					<?php 
						if($group_id_prev != $mem_group_id){
					?>	
						<tr>
							<td style="width:100px;vertical-align: top;">
								
							</td>
							<td class="text-center">
								<img src="<?php echo base_url(PROJECTPATH.'/assets/images/coop_profile/'.$_SESSION['COOP_IMG']); ?>" alt="Logo" style="height: 80px;" />	
								 <h3 class="title_view"><?php echo @$_SESSION['COOP_NAME'];?></h3>
								 <h3 class="title_view">รายงานการส่ง-หักเงินเดือน(รายละเอียดรายบุคคล)</h3>
								 <h3 class="title_view">
									<?php echo " ประจำ ".$title_date;?>
								</h3>
							 </td>
							 <td style="width:100px;vertical-align: top;" class="text-right">
								<a class="no_print" onclick="window.print();"><button class="btn btn-perview btn-after-input" type="button"><span class="icon icon-print" aria-hidden="true"></span></button></a>
								<?php
									$get_param = '?';
									// var_dump($_GET['mem_type']);
									foreach(@$_GET as $key => $value){
										if(is_array($value)){
											$value = implode(',', $value);
										}
										$get_param .= $key.'='.$value.'&';
									}
									$get_param = substr($get_param,0,-1);
									// echo $get_param;
								?>
								<a href="<?php echo base_url(PROJECTPATH.'/report_processor_data/coop_report_send_deduction_excel'.$get_param); ?>" class="no_print"><button class="btn btn-perview btn-after-input" type="button"><span>XLS</span></button></a>	
							</td>
						</tr>  
					<?php
						}
					?>
						<tr>
							<td colspan="3" style="text-align: right;">
								<span class="title_view">หน้าที่ <?php echo $page;?></span><br>						
							</td>
						</tr> 
					</table>
				
					<table class="table table-view table-center">
						<thead> 
							<tr>
								<th rowspan="2" style="width: 40px;vertical-align: middle;">ลำดับ</th>
								<th rowspan="2" style="width: 40px;vertical-align: middle;">เลขที่ สมาชิก</th>
								<th rowspan="2" style="width: 300px;vertical-align: middle;">ชื่อ-นามสกุล</th>
								<th rowspan="2" style="width: 80px;vertical-align: middle;">หุ้น</th>
								<?php 
									foreach($loan_type AS $key=>$row_loan_type){
								?>
								<th colspan="4" style="width: 200px;vertical-align: middle;"><?php echo str_replace('เงินกู้','',$row_loan_type['loan_type']);?></th> 
								<?php
									}
								?>
								<th colspan="2" style="width: 80px;vertical-align: middle;">เงินฝาก</th> 
								<th rowspan="2" style="width: 80px;vertical-align: middle;">ฌสอ สป</th> 
								<th rowspan="2" style="width: 80px;vertical-align: middle;">ค่าธรรมเนียมแรกเข้า</th> 
								<th rowspan="2" style="width: 80px;vertical-align: middle;">อื่นๆ</th>
								<th rowspan="2" style="width: 80px;vertical-align: middle;">ชำระหนี้ค้ำประกัน</th> 
								<th rowspan="2" style="width: 80px;vertical-align: middle;">รวม</th> 
							</tr>
							<tr>
								<?php 
									foreach($loan_type AS $key=>$row_loan_type){
								?>
								<th style="width: 15px;vertical-align: middle;">งวด</th>
								<th style="width: 180px;vertical-align: middle;">เลขที่สัญญา</th>
								<th style="width: 80px;vertical-align: middle;">เงินต้น</th> 
								<th style="width: 80px;vertical-align: middle;">ดอกเบี้ย</th> 
								<?php
									}
								?>
								<th style="width: 80px;vertical-align: middle;">เลขที่บัญชี</th> 
								<th style="width: 80px;vertical-align: middle;">จำนวนเงิน</th> 
							</tr> 
						</thead>
						<tbody>
			<?php
				}
				if ($group_id_prev != $mem_group_id) {
					$runno = 0;
					$index = 0;
					$memberCount = 0;
					$totals = array();
			?>
						<tr><td colspan="23" style="text-align: left;"><?php echo $mem_groups['mem_group_name']; ?></td></tr>
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
									<td style="text-align: center;"><?php echo $runno; ?></td>
									<td style="text-align: center;"><?php echo $member['member_id']; ?></td>
									<td style="text-align: left;"><?php echo $member['name']; ?></td>						 
									<td style="text-align: left;"><?php echo $member['SHARE']!='' && $i == 0 ? number_format($member['SHARE'],2):''; ?></td>						 

			<?php
					//Emergent
					if (!empty($emergent_nums[$i])) {
						$totals['principal_7'] += $member['7'][$emergent_nums[$i]]['principal']!='' ? $member['7'][$emergent_nums[$i]]['principal'] : 0;
						$totals['interest_7'] += $member['7'][$emergent_nums[$i]]['interest']!='' ?	$member['7'][$emergent_nums[$i]]['interest'] : 0;
						$totals['count_7']++;
			?>
									<td style="text-align: center;"><?php echo $member['7'][$emergent_nums[$i]]['period']?></td> 					 
									<td style="text-align: center;"><?php echo $member['7'][$emergent_nums[$i]]['contract_number'];?></td> 					 
									<td style="text-align: right;"><?php echo $member['7'][$emergent_nums[$i]]['principal']!=''?number_format($member['7'][$emergent_nums[$i]]['principal'],2):''; ?></td> 					 
									<td style="text-align: right;"><?php echo $member['7'][$emergent_nums[$i]]['interest']!=''?number_format($member['7'][$emergent_nums[$i]]['interest'],2):'';?></td>
			<?php
					} else {
			?>
									<td style="text-align: center;"></td> 					 
									<td style="text-align: center;"></td> 					 
									<td style="text-align: right;"></td> 					 
									<td style="text-align: right;"></td>
			<?php
					}
					//Normal
					if (!empty($normal_nums[$i])) {
						$totals['principal_8'] += $member['8'][$normal_nums[$i]]['principal']!='' ? $member['8'][$normal_nums[$i]]['principal'] : 0;
						$totals['interest_8'] += $member['8'][$normal_nums[$i]]['interest']!='' ?	$member['8'][$normal_nums[$i]]['interest'] : 0;
						$totals['count_8']++;
			?>
									<td style="text-align: center;"><?php echo $member['8'][$normal_nums[$i]]['period']?></td> 					 
									<td style="text-align: center;"><?php echo $member['8'][$normal_nums[$i]]['contract_number'];?></td> 					 
									<td style="text-align: right;"><?php echo $member['8'][$normal_nums[$i]]['principal']!=''?number_format($member['8'][$normal_nums[$i]]['principal'],2):''; ?></td> 					 
									<td style="text-align: right;"><?php echo $member['8'][$normal_nums[$i]]['interest']!=''?number_format($member['8'][$normal_nums[$i]]['interest'],2):'';?></td>
			<?php
					} else {
			?>
									<td style="text-align: center;"></td> 					 
									<td style="text-align: center;"></td> 					 
									<td style="text-align: right;"></td> 					 
									<td style="text-align: right;"></td>
			<?php
					}
					//Special
					if (!empty($special_num[$i])) {
						$totals['principal_9'] += $member['9'][$special_num[$i]]['principal']!='' ? $member['9'][$special_num[$i]]['principal'] : 0;
						$totals['interest_9'] += $member['9'][$special_num[$i]]['interest']!='' ?	$member['9'][$special_num[$i]]['interest'] : 0;
						$totals['count_9']++;
			?> 
									<td style="text-align: center;"><?php echo $member['9'][$special_num[$i]]['period']?></td> 					 
									<td style="text-align: center;"><?php echo $member['9'][$special_num[$i]]['contract_number'];?></td> 					 
									<td style="text-align: right;"><?php echo $member['9'][$special_num[$i]]['principal']!=''?number_format($member['9'][$special_num[$i]]['principal'],2):''; ?></td> 					 
									<td style="text-align: right;"><?php echo $member['9'][$special_num[$i]]['interest']!=''?number_format($member['9'][$special_num[$i]]['interest'],2):'';?></td>
			<?php
					} else {
			?>
									<td style="text-align: center;"></td> 					 
									<td style="text-align: center;"></td> 					 
									<td style="text-align: right;"></td> 					 
									<td style="text-align: right;"></td>
			<?php
					}
					//Deposit
					if (!empty($deposit_num[$i])) {
			?> 
									<td style="text-align: center;"><?php echo$member['DEPOSIT'][$deposit_num[$i]]['account_id'];?></td> 					 
									<td style="text-align: right;"><?php echo $member['DEPOSIT'][$deposit_num[$i]]['pay_amount']!='' && $i == 0?number_format($member['DEPOSIT'][$deposit_num[$i]]['pay_amount'],2):'';?></td>
			<?php
						$totals['DEPOSIT'] += $member['DEPOSIT'][$deposit_num[$i]]['pay_amount']!='' && $i == 0 ? $member['DEPOSIT'][$deposit_num[$i]]['pay_amount'] : 0;
					} else {
			?>
									<td style="text-align: center;"></td> 					 
									<td style="text-align: right;"></td>

			<?php
					}
			?> 
									<td style="text-align: right;"><?php echo $member['CREMATION']!='' && $i == 0?number_format($member['CREMATION'],2):'';?></td> 					 
									<td style="text-align: right;"><?php echo $member['REGISTER_FEE']!='' && $i == 0?number_format($member['REGISTER_FEE'],2):'';?></td> 					 
									<td style="text-align: right;"><?php echo $member['OTHER']!='' && $i == 0?number_format($member['OTHER'],2):'';?></td>					 
									<td style="text-align: right;"><?php echo $member['GUARANTEE_AMOUNT']!='' && $i == 0?number_format($member['GUARANTEE_AMOUNT'],2):'';?></td> 				 
									<td style="text-align: right;"><?php echo $member['sum_all']!='' && $i == 0 ?number_format($member['sum_all'],2):'';?></td> 							 
								</tr>										
			<?php
					$totals['SHARE'] += $member['SHARE']!='' && $i == 0 ? $member['SHARE'] : 0;
					$totals['CREMATION'] += $member['CREMATION']!='' && $i == 0 ? $member['CREMATION'] : 0;
					$totals['REGISTER_FEE'] += $member['REGISTER_FEE']!='' && $i == 0 ? $member['REGISTER_FEE'] : 0;
					$totals['OTHER'] += $member['OTHER']!='' && $i == 0 ? $member['OTHER'] : 0;
					$totals['GUARANTEE_AMOUNT'] += $member['GUARANTEE_AMOUNT']!='' && $i == 0 ? $member['GUARANTEE_AMOUNT'] : 0;
					$totals['sum_all'] += $member['sum_all']!='' && $i == 0 ? $member['sum_all'] : 0;
					

			?>
			<?php
				if($mem_groups['last_member'] == $member['member_id'] && ($max_loan_index == ($i+1))){
			?>
								<tr> 
									<td style="text-align: center;" class="underline" colspan="3"><?php echo "รวม ".number_format($memberCount)." คน";?></td>
									<td style="text-align: center;" class="underline"><?php echo !empty($totals['SHARE']) ? number_format($totals['SHARE'],2) : "";?></td>
									<td style="text-align: center;" class="underline" colspan="2"><?php echo !empty($totals['count_7']) ? number_format($totals['count_7'])." สัญญา": "";?></td>
									<td style="text-align: right;" class="underline"><?php echo !empty($totals['principal_7']) ? number_format($totals['principal_7'],2) : "";?></td>
									<td style="text-align: right;" class="underline"><?php echo !empty($totals['principal_7']) ? number_format($totals['interest_7'],2) : "";?></td>
									<td style="text-align: center;" class="underline" colspan="2"><?php echo !empty($totals['count_8']) ? number_format($totals['count_8'])." สัญญา": "";?></td>
									<td style="text-align: right;" class="underline"><?php echo !empty($totals['principal_8']) ? number_format($totals['principal_8'],2) : "";?></td>
									<td style="text-align: right;" class="underline"><?php echo !empty($totals['principal_8']) ? number_format($totals['interest_8'],2) : "";?></td>
									<td style="text-align: center;" class="underline" colspan="2"><?php echo !empty($totals['count_9']) ? number_format($totals['count_9']." สัญญา"): "";?></td>
									<td style="text-align: right;" class="underline"><?php echo !empty($totals['principal_9']) ? number_format($totals['principal_9'],2) : "";?></td>
									<td style="text-align: right;" class="underline"><?php echo !empty($totals['principal_9']) ? number_format($totals['interest_9'],2) : "";?></td>
									<td style="text-align: right;" class="underline"></td> 					 
									<td style="text-align: right;" class="underline"><?php echo !empty($totals['DEPOSIT']) ? number_format($totals['DEPOSIT'],2) : "";?></td> 					 
									<td style="text-align: right;" class="underline"><?php echo !empty($totals['CREMATION']) ? number_format($totals['CREMATION'],2) : "";?></td> 					 
									<td style="text-align: right;" class="underline"><?php echo !empty($totals['REGISTER_FEE']) ? number_format($totals['REGISTER_FEE'],2) : "";?></td> 					 
									<td style="text-align: right;" class="underline"><?php echo !empty($totals['OTHER']) ? number_format($totals['OTHER'],2) : "";?></td>					 
									<td style="text-align: right;" class="underline"><?php echo !empty($totals['GUARANTEE_AMOUNT']) ? number_format($totals['GUARANTEE_AMOUNT'],2) : "";?></td> 					 
									<td style="text-align: right;" class="underline"><?php echo !empty($totals['sum_all']) ? number_format($totals['sum_all'],2) : "";?></td> 						 
								</tr>
			<?php
				}
				if (($last_member_id == $member['member_id'] && ($max_loan_index == ($i+1))) || $index == ($first_page_size-1) || ( $index > $first_page_size && (($index-$first_page_size) % $page_size) == ($page_size-1) )
							|| ($mem_groups['last_member'] == $member['member_id'] && ($max_loan_index == ($i+1)))) {
					$page++;

			?>						  
						</tbody>    
					</table>
				</div>
			</div>
		<?php
				}
						$index++;
					}
				}
			}
		}
	}
?>