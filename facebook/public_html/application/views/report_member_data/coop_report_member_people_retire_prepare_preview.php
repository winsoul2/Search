<style>
	.border-bottom-dotted{border-bottom: 1px dotted #75758a;}
	.group-box{
		text-align: left;display: -webkit-inline-box;
		font-family: upbean;
		font-size: 16px;
	}
	span{
		font-size: 16px;
	}
	
	.table-view-3>thead>tr>th{
	    border-top: 0px solid #000 !important;
		border-bottom: 0px solid #000 !important;
		font-size: 16px;
		padding: 0px 8px 0px 8px;
	}
	.table-view-3>tbody>tr>td{
	    border: 0px !important;
		font-family: upbean;
		font-size: 16px;
		padding: 0px 8px 0px 8px;
	}
</style>
<div style="width: 1000px;" class="page-break">
	<div class="panel panel-body" style="padding-top:10px !important;min-height: 1420px;">
		<table style="width: 900px;">
			<tr>
				<td style="width:200px;vertical-align: top;">
					&nbsp;
				</td>
				<td class="text-center">&nbsp;</td>
				<td style="width:200px;vertical-align: top;" class="text-right">
					<a class="no_print" onclick="window.print();"><button class="btn btn-perview btn-after-input" type="button"><span class="icon icon-print" aria-hidden="true"></span></button></a>
				</td>
			</tr> 
			<tr>
				<td style="vertical-align: top;"  class="text-center" colspan="3">
					<img src="<?php echo base_url(PROJECTPATH.'/assets/images/coop_profile/'.$_SESSION['COOP_IMG']); ?>" alt="Logo" style="height: 80px;" />
				</td>
			</tr>
			<tr>
				<td class="text-center" colspan="3">
					 <h3 class="title_view"><?php echo @$_SESSION['COOP_NAME'];?></h3>
					 <h3 class="title_view">เอกสารการขาดการสมาชิกภาพ(ประสงค์ลาออก)</h3>
				 </td>
			</tr> 
			<tr>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td style="width:100px;vertical-align: top;" class="text-right">
					<!-- <h4>วันที่ <?php echo @$this->center_function->ConvertToThaiDate(@$row_member['resign_date'],'0','0');?></h4> -->
				</td>
			</tr> 
			<tr>
				<td colspan="3">
					<?php $full_name = @$row_member['prename_full'].@$row_member['firstname_th'].'  '.@$row_member['lastname_th']; ?>
					<h4>ชื่อ-นามสกุล : <?php echo @$full_name ;?> เลขทะเบียน : <?php echo @$row_member['member_id'];?></h4>
					<h4>หมวด : <?php echo @$row_member['department_name'];?>   กลุ่ม : <?php echo @$row_member['faction_name'];?></h4>
					<h4>โรงเรียน : <?php echo @$row_member['mem_group_name'];?></h4>
					<h4>หุ้น : <?php echo number_format(@$cal_share,0)?> บาท</h4>
					<?php
						foreach($accounts as $account) {
					?>
						<h4><?php echo $account["type_name"];?> : <?php echo number_format($account["transaction_balance"],2)?> บาท</h4>
					<?php
						}
					?>
					<!-- <h4>เงินฝาก : <?php echo number_format(@$cal_account,0)?> บาท</h4> -->
					<h4>รวมรายได้ : <?php echo number_format(@$income_amount,0)?> บาท</h4>
					<!-- <h4>ใบเสร็จเงินโอนเลขที่ <?php echo $receipt_id;?> จำนวนเงิน <?php echo number_format(@$sum_receipt,0)?> บาท</h4> -->
				</td>
			</tr>
			<tr>
				<td colspan="3">
					<table class="table table-view-3 table-center" style="width:90%;">
						<thead>
							<tr>
								<th style="text-align:center;width: 16%;">เลขที่สัญญา</th>
								<th style="text-align:right;width: 16%;">เงินต้นคงเหลือ</th>
								<th style="text-align:right;width: 16%;">ดอกเบี้ย</th>
								<th style="text-align:right;width: 16%;">ดอกเบี้ยคงค้าง</th>
								<th style="text-align:right;width: 16%;">รวม</th>
								<th style="text-align:right;width: 16%;">คงเหลือ</th>							
							</tr>
						</thead>
						<tbody>
							<?php
								$principal_payment = 0;
								$interest = 0;
								$total_amount = 0;
								$loan_amount_balance = 0;
								if(!empty($rs_loan)){
									foreach($rs_loan AS $key=>$row_loan){
							?>
							<tr>
								<td><?php echo $row_loan['contract_number'];?></td>
								<td style="text-align:right;"><?php echo number_format($row_loan['principal_payment'],2);?></td>
								<td style="text-align:right;"><?php echo number_format($row_loan['interest'],2);?></td>
								<td style="text-align:right;"><?php echo number_format($row_loan['loan_amount_interest_debt'],2);?></td>
								<td style="text-align:right;"><?php echo number_format($row_loan['total_amount'],2);?></td>
								<td style="text-align:right;"><?php echo number_format($row_loan['loan_amount_balance'],2);?></td>							
							</tr>
							
							<?php
										$principal_payment += $row_loan['principal_payment'];
										$interest += $row_loan['interest'];
										$loan_amount_interest_debt += $row_loan['loan_amount_interest_debt'];
										$total_amount += $row_loan['total_amount'];
										$loan_amount_balance += $row_loan['loan_amount_balance'];
									}
								}
							?>
							<tr>
								<td>รวม</td>
								<td style="text-align:right;"><?php echo number_format($principal_payment,2);?></td>
								<td style="text-align:right;"><?php echo number_format($interest,2);?></td>
								<td style="text-align:right;"><?php echo number_format($loan_amount_interest_debt,2);?></td>
								<td style="text-align:right;"><?php echo number_format($total_amount,2);?></td>
								<td style="text-align:right;"><?php echo number_format($loan_amount_balance,2);?></td>							
							</tr>
							<tr>
								<?php
									$pay_amount = @$income_amount - @$total_amount;
								?>
								<td colspan="5" style="font-weight: bold;"><?php echo $pay_amount < 0 ? 'จำนวนเงินที่ต้องชำระเพิ่ม' : 'ต้องจ่ายคืนสมาชิก';?> <?php echo number_format(abs($pay_amount),2);?> บาท</td>						
							</tr>
						</tbody>
					</table>
				</td>
			</tr>
			<tr>
				<td colspan="2">
					<h4 class="title_view">สมาชิกท่านนี้ไม่มีภาระค้ำประกัน</h4>
					
					<div class="group-box">
						<span>ลงชื่อ</span>	
						<div class="border-bottom-dotted" style="width: 200px;text-align: center;">&nbsp;</div>	
						<span>ผู้ออกเอกสาร</span>	
					</div>
					<br>
					<div class="group-box">
						<span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(</span>	
						<div class="border-bottom-dotted" style="width: 200px;text-align: center;"><?php echo @$full_name;?></div>	
						<span>)</span>	
					</div>
				</td>
				<td colspan="1" style="padding-right:40px;">
					<h4 class="title_view">&nbsp</h4>
					
					<div class="group-box">
						<span>ลงชื่อ</span>	
						<div class="border-bottom-dotted" style="width: 200px;text-align: center;">&nbsp;</div>	
						<span>ผู้อนุมัติ</span>	
					</div>
					<br>
					<div class="group-box">
						<span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(</span>	
						<div class="border-bottom-dotted" style="width: 200px;text-align: center;"></div>	
						<span>)</span>	
					</div>
				</td>
			</tr> 
		</table>		
	</div>
</div>