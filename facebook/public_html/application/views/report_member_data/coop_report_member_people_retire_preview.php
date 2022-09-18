<style>
	h1, h2, h3, h4, h5, h6 {
		font-family: THSarabunNew;
	}
	.border-bottom-dotted{border-bottom: 1px dotted #75758a;}
	.group-box{
		text-align: left;display: -webkit-inline-box;
		font-family: THSarabunNew;
		font-size: 16px;
	}
	span{
		font-size: 16px;
		font-family: THSarabunNew;
	}
	
	.table-view-3>thead>tr>th{
		border-top: 1px solid #000 !important;
		border-bottom: 4px double #000 !important;	
		font-family: THSarabunNew;
		font-size: 16px;
		padding: 10px 5px 10px 5px;
	}
	.table-view-3>tbody>tr>td{
	    border: 0px !important;
		font-family: THSarabunNew;
		font-size: 16px;
		padding: 5px 5px 5px 5px;
	}
	
	.table-view-3>tfoot>tr>td{
	    border-top: 1px solid #000 !important;
		font-family: THSarabunNew;
		font-size: 16px;
		padding: 10px 5px 10px 5px;
		background-color: #ffffff;
	}
</style>
<div style="width: 1000px;">
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
					 <h4 class="title_view"><?php echo @$_SESSION['COOP_NAME'];?></h4>
					 <h4 class="title_view">เอกสารการขาดการสมาชิกภาพ(<?php echo $row_member['resign_cause_name'];?>)</h4>
				 </td>
			</tr> 
			<tr>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td style="width:100px;vertical-align: top;" class="text-right">
					<h4>วันที่ <?php echo @$this->center_function->ConvertToThaiDate(@$row_member['resign_date'],'0','0');?></h4>
				</td>
			</tr> 
			<tr>
				<td colspan="3">
					<?php $full_name = @$row_member['prename_full'].@$row_member['firstname_th'].'  '.@$row_member['lastname_th']; ?>
					<h4>ชื่อ-นามสกุล  <?php echo @$full_name ;?> เลขทะเบียน  <?php echo @$row_member['member_id'];?></h4>
					<h4>หมวด  <?php echo @$row_member['department_name'];?>   กลุ่ม  <?php echo @$row_member['faction_name'];?></h4>
					<h4>โรงเรียน  <?php echo @$row_member['mem_group_name'];?></h4>
					
					<?php
					if(!empty($income_detail)){					
						foreach($income_detail AS $key=>$value){									
							$extra_detail = "";
							if(!empty($value["income_amount_IN"])) {
								$balance = $value["income_amount"] - $value["income_amount_IN"];
								$balance += !empty($value["income_amount_WTI"]) ? $value["income_amount_WTI"] : 0;
								$extra_detail = "(";
								$extra_detail .= "เงินต้น ".number_format($balance,2)." บาท ดอกเบี้ย ".number_format($value["income_amount_IN"],2)." บาท";
								$extra_detail .= !empty($value["income_amount_WTI"]) ? " ดอกเบี้ยหักคืนสหกรณ์ ".number_format($value["income_amount_WTI"],2)." บาท" : "";
								$extra_detail .= ")";
							}
							echo "<h4>".@$value['income_name']."  ".number_format(@$value['income_amount'],2)." บาท ".$extra_detail."</h4>";
						} 
					}
					?>
					<h4>ใบเสร็จเลขที่ <?php echo $receipt_id;?> </h4>
				</td>
			</tr>
			<tr>
				<td colspan="3">
	
					<table class="table table-view-3 table-center" style="width:100%;">
						<thead>
							<tr>
								<th>สัญญา</th>
								<th style="text-align:right;width: 25%;">เงินต้น</th>
								<th style="text-align:right;width: 25%;">ดอกเบี้ย</th>
								<th style="text-align:right;width: 25%;">ดอกเบี้ยคงค้าง</th>
								<th style="text-align:right;width: 25%;">หนี้คงค้าง</th>							
							</tr>
						</thead>
						<tbody>
							<?php
								$loan_amount_principal = 0;
								$loan_amount_interest = 0;
								$loan_amount_interest_debt = 0;
								$loan_amount_all = 0;
								$total_amount = 0;
								if(!empty($rs_loan)){
									foreach($rs_loan AS $key=>$row_loan){
							?>
							<tr>
								<td style="text-align:left;"><?php echo @$row_loan['contract_number'];?></td>
								<td style="text-align:right;"><?php echo number_format(@$row_loan['principal_payment'],2);?></td>
								<td style="text-align:right;"><?php echo number_format(@$row_loan['interest'],2);?></td>
								<td style="text-align:right;"><?php echo number_format(@$row_loan['loan_interest_remain'],2);?></td>
								<td style="text-align:right;"><?php echo number_format(@$row_loan['loan_amount_balance'],2);?></td>							
							</tr>
							
							<?php
										$loan_amount_principal += @$row_loan['principal_payment'];
										$loan_amount_interest += @$row_loan['interest'];
										$loan_amount_interest_debt += @$row_loan['loan_amount_balance'];
										$total_amount += ($row_loan['total_amount'] + $row_loan['loan_amount_balance']);
									}
								}
							?>
						</tbody>
						<tfoot>	
							<tr>
								<?php
									$loan_amount_all = @$loan_amount_principal + @$loan_amount_interest + @$loan_amount_interest_debt;
									$pay_amount = @$income_amount - @$total_amount;
									if ($loan_amount_interest_debt == 0) {
								?>
								<td colspan="5" style="font-weight: bold;">ต้องจ่ายคืนสมาชิก <?php echo number_format(@$pay_amount,2);?> บาท</td>						
								<?php
									} else {
								?>
								<td colspan="5" style="font-weight: bold;">จำนวนที่ต้องชำระเพิ่ม <?php echo number_format(@$loan_amount_interest_debt,2);?> บาท</td>		
								<?php
									}
								?>
							</tr>
						</tfoot> 
					</table>
				</td>
			</tr>
			<tr>
				<td colspan="3">
					<?php
						if(empty($guarantees)) {
					?>
					<span class="title_view">สมาชิกท่านนี้ไม่มีภาระค้ำประกัน</span>
					<br>
					<br>
					<?php
						} else {
					?>
					<span class="title_view">ภาระค้ำประกัน</span>
					<?php
							foreach($guarantees as $guarantee) {
					?>
					<h4> - <?php echo $guarantee['prename_full'].$guarantee['firstname_th']." ".$guarantee['lastname_th'];?></h4>
					<?php
							}
						}
					?>
					<br>
					<br>
					<div class="group-box">						
						<span>ลงชื่อ</span>	
						<div class="border-bottom-dotted" style="width: 200px;text-align: center;">&nbsp;</div>	
						<span>ผู้ออกเอกสาร</span>	
					</div>
					<br>
					<div class="group-box">
						<span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(</span>	
						<div class="border-bottom-dotted" style="width: 200px;text-align: center;">  </div>	
						<!--<div class="border-bottom-dotted" style="width: 200px;text-align: center;"><?php echo @$full_name;?></div>-->	
						<span>)</span>	
					</div>
				</td>
			</tr> 
		</table>		
	</div>
</div>