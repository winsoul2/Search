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
					 <h4 class="title_view">เอกสารการชำระหนี้คงค้างจากการโอนหุ้นตัดหนี้</h4>
				 </td>
			</tr>
			<tr>
				<td colspan="3">
					<?php $full_name = $member->prename_full.$member->firstname_th.'  '.$member->lastname_th; ?>
					<h4>ชื่อ-นามสกุล  <?php echo $full_name ;?> เลขทะเบียน  <?php echo $member->member_id;?></h4>
					<h4>หมวด  <?php echo $member->department_name;?>   กลุ่ม  <?php echo $member->faction_name;?></h4>
					<h4>โรงเรียน  <?php echo $member->level_name;?></h4>
				</td>
			</tr>
			<tr>
				<td colspan="3">
					<table class="table table-view-3 table-center" style="width:100%;">
						<thead>
							<tr>
								<th style="text-align:center;width: 20%;">สัญญา</th>
								<th style="text-align:center;width: 20%;">ปี</th>
								<th style="text-align:center;width: 20%;">เดือน</th>
								<th style="text-align:center;width: 20%;">ดอกเบี้ยคงค้าง</th>
								<th style="text-align:center;width: 20%;">จำนวนที่ชำระ</th>
							</tr>
						</thead>
						<tbody>
							<?php
								$total_payment = 0;
								foreach($datas as $year => $months) {
									foreach($months as $month => $contracts) {
										foreach($contracts as $contract_number=> $data) {
							?>
							<tr>
								<td style="text-align:center;"><?php echo $contract_number;?></td>
								<td style="text-align:center;"><?php echo $year;?></td>
								<td style="text-align:center;"><?php echo $month;?></td>
								<td style="text-align:right;"><?php echo number_format($data['non_pay_amount'],2);?></td>
								<td style="text-align:right;"><?php echo number_format($data['non_pay_amount_balance'],2);?></td>
							</tr>
							<?php
											$total_payment += $data['non_pay_amount_balance'];
										}
									}
								}
							?>
						</tbody>
						<tfoot>
							<tr>
								<td style="text-align:center;" colspan="4">รวม</td>
								<td style="text-align:right;"><?php echo number_format($total_payment,2);?></td>
							</tr>
						</tfoot>
					</table>
				</td>
			</tr>
		</table>
	</div>
</div>