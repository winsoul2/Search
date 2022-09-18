		<style>
		.table-view>tbody>tr>td, .table-view>tbody>tr>th, .table-view>tfoot>tr>td, .table-view>tfoot>tr>th, .table-view>thead, .table-view>thead>tr>td, .table-view>thead>tr>th {
			border: 0px solid #000;
			padding: 5px 8px 5px 8px;
		}
		</style>
		<div style="width: 1000px;" class="page-break">
			<div class="panel panel-body" style="padding-top:10px !important;min-height: 1420px;">
				<table style="width: 100%;">
					<tr>
						<td style="vertical-align: top;" class="text-right">
							<a class="no_print" onclick="window.print();"><button class="btn btn-perview btn-after-input" type="button"><span class="icon icon-print" aria-hidden="true"></span></button></a>
						</td>
					</tr>
				</table>
				<table style="width: 100%;">
					<tr>
						<td style="width:100px;vertical-align: top;">
							<img src="<?php echo base_url(PROJECTPATH.'/assets/images/coop_profile/'.$_SESSION['COOP_IMG']); ?>" alt="Logo" style="height: 80px;" />
						</td>
						<td class="text-left" style="vertical-align: top;">
							 <h3 class="title_view"><?php echo @$_SESSION['COOP_NAME'];?></h3>
							 <h4 class="title_view">รายละเอียดการชำระเงินกู้</h4>
							 <h4 class="title_view">
								รหัสสมาชิก <?php echo @$loan_data['member_id']; ?> ชื่อ-สกุล <?php echo @$loan_data['prename_short'].$loan_data['firstname_th']." ".$loan_data['lastname_th']; ?>
							</h4>
							 <p>&nbsp;</p>
						 </td>
						 <td style="width:100px;vertical-align: top;" class="text-right">
							<h4 class="title_view">สัญญาเลขที่</h4>
							<h4 class="title_view">วันที่ทำสัญญา</h4>
							<h4 class="title_view">วงเงินกู้</h4>
							<h4 class="title_view">เงินต้นคงเหลือ</h4>
						 </td>
						 <td style="width:110px;vertical-align: top;" class="text-right">
							<h4 class="title_view"><?php echo $loan_data['contract_number']; ?>&nbsp;</h4>
							<h4 class="title_view"><?php echo $this->center_function->ConvertToThaiDate($loan_data['approve_date'],1,0); ?>&nbsp;</h4>
							<h4 class="title_view"><?php echo number_format($loan_data['loan_amount'],2); ?>&nbsp;</h4>
							<h4 class="title_view"><?php echo number_format($loan_data['loan_amount_balance'],2); ?>&nbsp;</h4>
						 </td>
					</tr>
				</table>
				<table class="table table-view table-center">
					<thead>
						<tr>
							<th style="width: 10%;vertical-align: middle;">ลำดับ</th>
							<th style="width: 10%;vertical-align: middle;">วันที่</th>
							<th style="width: 20%;vertical-align: middle;">เลขที่ใบเสร็จ</th>
							<th style="vertical-align: middle;">รายการ</th>
							<th style="width: 100px;vertical-align: middle;text-align: right;">เงินต้น</th>
							<th style="vertical-align: middle;text-align: right;">ดอกเบี้ย</th>
							<th style="vertical-align: middle;text-align: right;">รวม</th>
							<th style="vertical-align: middle;text-align: right;">ยอดคงเหลือ</th>
						</tr>
					</thead>
					<tbody id="table_first">
						<?php if(!empty($transaction_data)){ $i=1; ?>
							<?php foreach($transaction_data as $key => $value){ ?>
								<tr>
									<td><?php echo $i++; ?></td>
									<td><?php echo $this->center_function->ConvertToThaiDate($value['payment_date'],1,0); ?></td>
									<td style="text-align:center;">
										<?php
											if (strpos($value['receipt_id'], "dummy") !== false) {
												echo '-';
											} else if (trim($value['receipt_id']) == '') {
												echo '-';
											} else {
												echo $value['receipt_id'];
											}
										?>
									</td>
									<td style="text-align:left;"><?php echo $value['data_text']; ?></td>
									<td style="text-align:right;"><?php echo number_format($value['principal'],2); ?></td>
									<td style="text-align:right;"><?php echo number_format($value['interest'],2); ?></td>
									<td style="text-align:right;"><?php echo number_format($value['principal'] + $value['interest'],2); ?></td>
									<td style="text-align:right;"><?php echo number_format($value['loan_amount_balance'],2); ?></td>
								</tr>
							<?php } ?>
						<?php } ?>
					</tbody>
				</table>
			</div>
		</div>