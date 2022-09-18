<style>
	.table-view>thead, .table-view>thead>tr>td, .table-view>thead>tr>th {
		font-size: 14px;
	}
	.table {
		color: #000;
	}
	@page { size: landscape; }
</style>
<?php

if(@$_GET['start_date']){
	$start_date_arr = explode('/',@$_GET['start_date']);
	$start_day = $start_date_arr[0];
	$start_month = $start_date_arr[1];
	$start_year = $start_date_arr[2];
	$start_year -= 543;
	$start_date = $start_year.'-'.$start_month.'-'.$start_day;
}

if(@$_GET['end_date']){
	$end_date_arr = explode('/',@$_GET['end_date']);
	$end_day = $end_date_arr[0];
	$end_month = $end_date_arr[1];
	$end_year = $end_date_arr[2];
	$end_year -= 543;
	$end_date = $end_year.'-'.$end_month.'-'.$end_day;
}

$last_runno = 0;
$all_withdrawal = 0;
$all_deposit = 0;
$all_balance = 0;

$emergent_total = 0;
$normal_total = 0;
$special_total = 0;

$totals = array();
$index = 0;
$first_page_size = 12;
$page_size = 15;
if(!empty($datas)){
	foreach($datas AS $member_id => $data_rows){
		foreach($data_rows AS $receipt_id => $data_row){
		// if($data_row['none_pay_all']>0){
			if ($index == 0 || $index == $first_page_size || ( $index > $first_page_size && (($index-$first_page_size) % $page_size) == 0 )) {
	?>

		<div style="width: 1500px;"  class="page-break">
			<div class="panel panel-body" style="padding-top:10px !important;height: 950px;">
				<table style="width: 100%;">
				<?php 
					if($index == 0){
				?>
					<tr>
						<td style="width:100px;vertical-align: top;">

						</td>
						<td class="text-center">
							<img src="<?php echo base_url(PROJECTPATH.'/assets/images/coop_profile/'.$_SESSION['COOP_IMG']); ?>" alt="Logo" style="height: 80px;" />
							 <h3 class="title_view"><?php echo @$_SESSION['COOP_NAME'];?></h3>
							 <h3 class="title_view">รายงานสรุปการรับชำระหนี้คงค้าง<?php echo $date_title?></h3>

							 <h3 class="title_view">
								<?php 
									// echo (@$_GET['start_date'] == @$_GET['end_date'])?"":"";
									echo "ประจำ วันที่ ".$this->center_function->ConvertToThaiDate($start_date);
									echo (@$_GET['start_date'] == @$_GET['end_date'])?"":"  ถึงวันที่  ".$this->center_function->ConvertToThaiDate($end_date);
								?>
							</h3>
						 </td>
						 <td style="width:100px;vertical-align: top;" class="text-right">
							<a class="no_print" onclick="window.print();"><button class="btn btn-perview btn-after-input" type="button"><span class="icon icon-print" aria-hidden="true"></span></button></a>
							<?php
								$get_param = '?';
								foreach(@$_GET as $key => $value){
									//if($key != 'month' && $key != 'year' && $value != ''){
										$get_param .= $key.'='.$value.'&';
									//}
								}
								$get_param = substr($get_param,0,-1);
							?>
							<a class="no_print"  target="_blank" href="<?php echo base_url(PROJECTPATH.'/report_deposit_data/coop_report_pay_non_pay_excel'.$get_param); ?>">
								<button class="btn btn-perview btn-after-input" type="button"><span class="icon icon icon-file-excel-o" aria-hidden="true"></span></button>
							</a>
						</td>
					</tr>
				<?php 
					}else{
				?>
					<tr>
						<td colspan="3" style="text-align: left;">&nbsp;</td>
					</tr>
				<?php
					}
				?>

					<tr>
						<td colspan="3" style="text-align: right;">
							<span class="title_view">วันที่ <?php echo $this->center_function->ConvertToThaiDate(@date('Y-m-d'),1,0);?></span>
							<span class="title_view">   เวลา <?php echo date('H:i:s');?></span>
						</td>
					</tr> 
					<tr>
						<td colspan="3" style="text-align: right;">
							<span class="title_view">หน้าที่ <?php echo @$page.'/'.@$page_all;?></span><br>
						</td>
					</tr>
				</table>

				<table class="table table-view table-center">
					<thead>
						<tr>
							<th rowspan="2" style="width: 40px;vertical-align: middle;">ลำดับ</th>
							<th rowspan="2" style="width: 80px;vertical-align: middle;">รหัส</th>
							<th rowspan="2" style="width: 180px;vertical-align: middle;">ชื่อ-นามสกุล</th>
							<th rowspan="2" style="width: 80px;vertical-align: middle;">เลขที่ใบเสร็จ</th>
							<th rowspan="2" style="width: 80px;vertical-align: middle;">หุ้น</th>
							<?php 
								foreach(@$loan_type AS $key=>$row_loan_type){
							?>
								<th colspan="4" style="width: 80px;vertical-align: middle;"><?php echo str_replace('เงินกู้','',@$row_loan_type['loan_type']);?></th> 
							<?php 
								}
							?>
							<th rowspan="2" style="width: 80px;vertical-align: middle;">ฌสอ สป</th>
							<th rowspan="2" style="width: 80px;vertical-align: middle;">ค่าธรรมเนียมแรกเข้า</th>
							<th rowspan="2" style="width: 80px;vertical-align: middle;">อื่นๆ</th>
							<th rowspan="2" style="width: 80px;vertical-align: middle;">ชำระหนี้ค้ำประกัน</th>
							<th rowspan="2" style="width: 80px;vertical-align: middle;">รวม</th>
						</tr>
						<tr>
						<?php
							foreach(@$loan_type AS $key=>$row_loan_type){
						?>
							<th style="width: 80px;vertical-align: middle;">งวด</th>
							<th style="width: 80px;vertical-align: middle;">เลขที่สัญญา</th>
							<th style="width: 80px;vertical-align: middle;">เงินต้น</th>
							<th style="width: 80px;vertical-align: middle;">ดอกเบี้ย</th>
						<?php
							}
						?>
						</tr>
					</thead>
					<tbody>

	<?php
		}

		$normal_nums = array();
		if(!empty($data_row['normal'])) {
			foreach($data_row['normal'] as $contract_number => $val) {
				$normal_nums[] = $contract_number;
			}
		}
		$emergent_nums = array();
		if(!empty($data_row['emergent'])) {
			foreach($data_row['emergent'] as $contract_number => $val) {
				$emergent_nums[] = $contract_number;
			}
		}
		$special_num = array();
		if(!empty($data_row['special'])) {
			foreach($data_row['special'] as $contract_number => $val) {
				$special_num[] = $contract_number;
			}
		}
		$normalSize = count($normal_nums);
		$emergentSize = count($emergent_nums);
		$specialSize = count($special_num);
		$max_loan_index = max($normalSize, $emergentSize, $specialSize) > 0 ? max($normalSize, $emergentSize, $specialSize) : 1;
		for($i = 0; $i < $max_loan_index; $i++) {
			$runno++;
	?>
							<tr>
								<td style="text-align: center;vertical-align: top;"><?php echo $runno; ?></td>
								<td style="text-align: center;vertical-align: top;"><?php echo $member_id;?></td>
								<td style="text-align: left;vertical-align: top;"><?php echo $data_row['member_name'];?></td>
								<td style="text-align: left;vertical-align: top;"><?php echo $data_row['receipt_id'];?></td>
								<td style="text-align: right;vertical-align: top;">
									<?php
										if(!empty($data_row['share']) && $i == 0) {
											echo number_format($data_row['share'],2);
											$totals['buy_stock'] += $data_row['share'];
										}
									?>
								</td>
							<?php
								if (!empty($emergent_nums[$i])) {
							?>
								<td style="text-align: right;vertical-align: top;"><?php echo $data_row['normal'][$emergent_nums[$i]]['period_count']; ?></td>
								<td style="text-align: right;vertical-align: top;"><?php echo $emergent_nums[$i]; ?></td>
								<td style="text-align: right;vertical-align: top;"><?php echo number_format($data_row['emergent'][$emergent_nums[$i]]['principal'],2); ?></td>
								<td style="text-align: right;vertical-align: top;"><?php echo number_format($data_row['emergent'][$emergent_nums[$i]]['interest'],2); ?></td>

							<?php
									$totals['emergent']['principal_payment'] += $data_row['emergent'][$emergent_nums[$i]]['principal'];
									$totals['emergent']['interest'] += $data_row['emergent'][$emergent_nums[$i]]['interest'];
									$totals['emergent']['none_pay'] += $data_row['emergent'][$emergent_nums[$i]]['none_pay'];
								} else {
							?>
								<td style="text-align: right;vertical-align: top;"></td>
								<td style="text-align: right;vertical-align: top;"></td>
								<td style="text-align: right;vertical-align: top;"></td>
								<td style="text-align: right;vertical-align: top;"></td>
							<?php
								}

								if (!empty($normal_nums[$i])) {
							?>
								<td style="text-align: right;vertical-align: top;"><?php echo $data_row['normal'][$normal_nums[$i]]['period_count']; ?></td>
								<td style="text-align: right;vertical-align: top;"><?php echo $normal_nums[$i]; ?></td>
								<td style="text-align: right;vertical-align: top;"><?php echo number_format($data_row['normal'][$normal_nums[$i]]['principal'],2); ?></td>
								<td style="text-align: right;vertical-align: top;"><?php echo number_format($data_row['normal'][$normal_nums[$i]]['interest'],2); ?></td>
							<?php
									$totals['normal']['principal_payment'] += $data_row['normal'][$normal_nums[$i]]['principal'];
									$totals['normal']['interest'] += $data_row['normal'][$normal_nums[$i]]['interest'];
									$totals['normal']['none_pay'] += $data_row['normal'][$normal_nums[$i]]['none_pay'];
								} else {
							?>
								<td style="text-align: right;vertical-align: top;"></td>
								<td style="text-align: right;vertical-align: top;"></td>
								<td style="text-align: right;vertical-align: top;"></td>
								<td style="text-align: right;vertical-align: top;"></td>
							<?php
								}

								if (!empty($special_num[$i])) {
							?>
								<td style="text-align: right;vertical-align: top;"><?php echo $data_row['normal'][$special_num[$i]]['period_count']; ?></td>
										<td style="text-align: right;vertical-align: top;"><?php echo $special_num[$i]; ?></td>
										<td style="text-align: right;vertical-align: top;"><?php echo number_format($data_row['special'][$special_num[$i]]['principal'],2); ?></td>
										<td style="text-align: right;vertical-align: top;"><?php echo number_format($data_row['special'][$special_num[$i]]['interest'],2); ?></td>
							<?php
											$totals['special']['principal_payment'] += $data_row['special'][$special_num[$i]]['principal'];
											$totals['special']['interest'] += $data_row['special'][$special_num[$i]]['interest'];
											$totals['special']['none_pay'] += $data_row['special'][$special_num[$i]]['none_pay'];
								} else {
							?>
										<td style="text-align: right;vertical-align: top;"></td>
										<td style="text-align: right;vertical-align: top;"></td>
										<td style="text-align: right;vertical-align: top;"></td>
										<td style="text-align: right;vertical-align: top;"></td>
							<?php
								}
							?>
								<td style="text-align: left;vertical-align: top;">
									<?php
										if(!empty($data_row["cremation"])) {
											echo number_format($data_row["cremation"],2);
											$totals["cremation"] += $data_row["cremation"];
										}
									?>
								</td>
								<td style="text-align: left;vertical-align: top;">
									<?php
										if(!empty($data_row["register_fee"])) {
											echo number_format($data_row["register_fee"],2);
											$totals["register_fee"] += $data_row["register_fee"];
										}
									?>
								</td>
								<td style="text-align: left;vertical-align: top;">
									<?php
										if(!empty($data_row["other"])) {
											echo number_format($data_row["other"],2);
											$totals["other"] += $data_row["other"];
										}
									?>
								</td>
								<td style="text-align: left;vertical-align: top;">
									<?php
										if(!empty($data_row["guarantee_amount"])) {
											echo number_format($data_row["guarantee_amount"],2);
											$totals["guarantee_amount"] += $data_row["guarantee_amount"];
										}
									?>
								</td>
								<td style="text-align: left;vertical-align: top;">
									<?php
										if(!empty($data_row["total"])) {
											echo number_format($data_row["total"],2);
											$totals["total"] += $data_row["total"];
										}
									?>
								</td>
							</tr>
					<?php
		}

						if($last_receipt_id == $data_row['receipt_id']){
					?>
							<tr class="foot-border"> 
								<td colspan="4" style="text-align: center;">รวม</td>
								<td style="text-align: right;vertical-align: top;"><?php echo number_format($totals["buy_stock"],2); ?></td>
								<!-- <td style="text-align: right;vertical-align: top;"><?php echo number_format($totals["sell_stock"],2); ?></td>  -->
					<?php
							foreach(@$loan_type AS $key=>$row_loan_type){
					?>
								<td style="text-align: right;vertical-align: top;"></td>
								<td style="text-align: right;vertical-align: top;"></td>
								<td style="text-align: right;vertical-align: top;"><?php echo number_format($totals[$row_loan_type["loan_type_code"]]['principal_payment'],2); ?></td>
								<td style="text-align: right;vertical-align: top;"><?php echo number_format($totals[$row_loan_type["loan_type_code"]]['interest'],2); ?></td>
					<?php
							}
					?>
								<td style="text-align: right;vertical-align: top;"><?php echo number_format($totals["cremation"],2); ?></td>
								<td style="text-align: right;vertical-align: top;"><?php echo number_format($totals["register_fee"],2); ?></td>
								<td style="text-align: right;vertical-align: top;"><?php echo number_format($totals["other"],2); ?></td>
								<td style="text-align: right;vertical-align: top;"><?php echo number_format($totals["guarantee_amount"],2); ?></td>
								<td style="text-align: right;vertical-align: top;"><?php echo number_format($totals["total"],2); ?></td>
							</tr>
					<?php
						}
						if ($last_receipt_id == $data_row['receipt_id'] || $index == ($first_page_size - 1) || ( $index > $first_page_size && (($index-$first_page_size) % $page_size) == ($page_size - 1) )) {
					?>
					</tbody>
				</table>
			</div>
		</div>
<?php
			}
			$index++;
		// }	
		}
	}
}
?>