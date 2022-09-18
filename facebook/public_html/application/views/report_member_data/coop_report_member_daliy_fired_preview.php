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
	 error_reporting(0);
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

		$runno = 0;
		$prev_member = "x";
		$total = array();
		$male_count = 0;
		$female_count = 0;
		$unknow_sex_count = 0;
		
		foreach($datas as $page => $data) {

	?>
		
		<div style="width: 1500px;"  class="page-break">
			<div class="panel panel-body" style="padding-top:10px !important;height: 950px;">
				<table style="width: 100%;">
				<?php 
					if($page == 1){
				?>	
					<tr>
						<td style="width:100px;vertical-align: top;">
							
						</td>
						<td class="text-center">
							<img src="<?php echo base_url(PROJECTPATH.'/assets/images/coop_profile/'.$_SESSION['COOP_IMG']); ?>" alt="Logo" style="height: 80px;" />	
							 <h3 class="title_view"><?php echo @$_SESSION['COOP_NAME'];?></h3>
							 <h3 class="title_view">รายงานสมาชิกให้ออก</h3>
							 <h3 class="title_view">
								<?php 
									echo (@$_GET['start_date'] == @$_GET['end_date'])?"":"ตั้งแต่";
									echo "วันที่ ".$this->center_function->ConvertToThaiDate($start_date);
									echo (@$_GET['start_date'] == @$_GET['end_date'])?"":"  ถึงวันที่  ".$this->center_function->ConvertToThaiDate($end_date);
								?>
							</h3>
						 </td>
						 <td style="width:100px;vertical-align: top;" class="text-right">
							<a class="no_print" onclick="window.print();"><button class="btn btn-perview btn-after-input" type="button"><span class="icon icon-print" aria-hidden="true"></span></button></a>
							<?php
								$get_param = '?';
								foreach(@$_GET as $key => $value){
										$get_param .= $key.'='.$value.'&';
								}
								$get_param = substr($get_param,0,-1);
							?>
							<a class="no_print"  target="_blank" href="<?php echo base_url(PROJECTPATH.'/report_member_data/coop_report_member_daliy_fired_excel'.$get_param); ?>">
								<button class="btn btn-perview btn-after-input" type="button"><span class="icon icon icon-file-excel-o" aria-hidden="true"></span></button>
							</a>
						</td>
					</tr> 
					<tr>
						<td colspan="3" style="text-align: right;">
							<span class="title_view">วันที่ <?php echo $this->center_function->ConvertToThaiDate(@date('Y-m-d'),0,0);?></span>				
						</td>
					</tr>  
					<tr>
						<td colspan="3" style="text-align: right;">
							<span class="title_view">ผู้ทำรายการ <?php echo $_SESSION['USER_NAME'];?></span>
						</td>
					</tr>  
				<?php } ?>
					<tr>
						<td colspan="3" style="text-align: right;">
							<span class="title_view">หน้าที่ <?php echo $page.'/'.$page_all;?></span><br>						
						</td>
					</tr> 
				</table>
			
				<table class="table table-view table-center">
					<thead> 
						<tr>
							<th style="vertical-align: middle;">ลำดับ</th>
							<th style="vertical-align: middle;">ชื่อ-นามสกุล</th>
							<th style="vertical-align: middle;">สังกัด</th>
							<th style="vertical-align: middle;">ทุนเรือนหุ้น</th>
							<th style="vertical-align: middle;">สัญญา</th>
							<th style="vertical-align: middle;">เงินต้น</th>
							<th style="vertical-align: middle;">ดอกเบี้ย</th>
							<th style="vertical-align: middle;">ดอกเบี้ยคงค้าง</th>
							<th style="vertical-align: middle;">เงินฝาก</th>
							<th style="vertical-align: middle;">ดอกเบี้ยเงินฝาก</th>
							<th style="vertical-align: middle;">คงค้าง</th>
							<th style="vertical-align: middle;">หมายเหตุ</th>
							<th style="vertical-align: middle;">หมายเลขบัญชีสมาชิก</th>
						</tr> 
					</thead>
					<tbody>
						<?php
							foreach($data as $row) {
						?>
						<tr>
							<?php
								if($prev_member != $row['member_id']) {
									$total['share_early_value'] += $row['share_early_value'];
							?>
								<td style="text-align: center;"><?php echo ++$runno;?></td>
								<td style="text-align: left;"><?php echo $row['prename_full'].$row['firstname_th']." ".$row['lastname_th'];?></td> 
								<td style="text-align: left;"><?php echo $row['faction_name']."/".$row['level_name'];?></td>
								<td style="text-align: right;"><?php echo number_format($row['share_early_value'],2);?></td>
							<?php
								} else {
							?>
								<td style="text-align: right;"></td>
								<td style="text-align: right;"></td>
								<td style="text-align: right;"></td>
								<td style="text-align: right;"></td>
							<?php
								}

								if(!empty($row['contract_number'])) {
							?>
							<td style="text-align: center;"><?php echo $row['contract_number'];?></td>
							<td style="text-align: right;"><?php echo number_format($row['loan_amount_principal'],2);?></td>
							<td style="text-align: right;"><?php echo number_format($row['loan_amount_interest'],2);?></td>
							<td style="text-align: right;"><?php echo number_format($row['loan_amount_interest_debt'],2);?></td>
							<?php
								} else {
							?>
							<td style="text-align: center;">-</td>
							<td style="text-align: right;">-</td>
							<td style="text-align: right;">-</td>
							<td style="text-align: right;">-</td>
							<?php
								}
								if($prev_member != $row['member_id']) {
									$total['income_amount'] += $row['total'];
							?>
								<td style="text-align: right;"><?php echo number_format($row['balance'],2);?></td>
								<td style="text-align: right;"><?php echo number_format($row['interest'],2);?></td>
								<td style="text-align: right;"><?php echo number_format($row['total'],2);?></td>
								<td style="text-align: left;"><?php echo $row['resign_cause_name'];?></td>
							<?php
								} else {
							?>
								<td style="text-align: right;"></td>
								<td style="text-align: right;"></td>
								<td style="text-align: right;"></td>
								<td style="text-align: left;"></td>
							<?php
								}

								if($prev_member != $row['member_id']) {
									if ($row['sex'] == "M") {
										$male_count++;
									} elseif ($row['sex'] == "F") {
										$female_count++;
									} else {
										$unknow_sex_count++;
									}
								}
								$total['principal'] += $row['loan_amount_principal'];
								$total['interest'] += $row['loan_amount_interest'];
								$total['interest_debt'] += $row['loan_amount_interest_debt'];
								$total['acc_balance'] += $row['balance'];
								$total['acc_interest'] += $row['interest'];
								$prev_member = $row['member_id'];
							?>
							<td style="text-align: center;"><?php echo $row['dividend_acc_num'];?></td>
						</tr>
						<?php
							}
							if($page == $page_all) {
						?>
						<tr>
							<td colspan="3" style="text-align: center;">
								<?php
									$text = "";
									if(!empty($male_count)) {
										$text .= "เป็นชายจำนวน :: ".$male_count." ";
									}
									if(!empty($female_count)) {
										$text .= "เป็นหญิงจำนวน :: ".$female_count." ";
									}
									if(!empty($unknow_sex_count)) {
										$text .= "ระบุไม่ได้จำนวน :: ".$unknow_sex_count." ";
									}
									echo $text;
								?>
							</td>
							<td style="text-align: right;"><?php echo number_format($total['share_early_value'],2);?></td>
							<td style="text-align: left;"></td>
							<td style="text-align: right;"><?php echo number_format($total['principal'],2);?></td>
							<td style="text-align: right;"><?php echo number_format($total['interest'],2);?></td>
							<td style="text-align: right;"><?php echo number_format($total['interest_debt'],2);?></td>
							<td style="text-align: right;"><?php echo number_format($total['acc_balance'],2);?></td>
							<td style="text-align: right;"><?php echo number_format($total['acc_interest'],2);?></td>
							<td style="text-align: right;"><?php echo number_format($total['income_amount'],2);?></td>
							<td colspan="2" style="text-align: right;"></td>
						</tr>
						<?php
							}
						?>
					</tbody>    
				</table>
			</div>
		</div>
<?php } ?>