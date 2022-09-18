<div class="layout-content">
    <div class="layout-content-body">
<style>
label {
    padding-top: 6px;
    text-align: right;
}
.text-center{
	text-align:center;
}
</style> 
<?php
	$transfer_status = array(''=>'ยังไม่ได้โอนเงิน','0'=>'โอนเงินแล้ว');
	//$transfer_status = array('0'=>'โอนเงินแล้ว','1'=>'รออนุมัติยกเลิก','อนุมัติยกเลิกรายการ');
?>


		<div class="row">
			<div class="form-group">
				<div class="col-sm-6">
					<h1 class="title_top">โอนเงินกู้</h1>
					<?php $this->load->view('breadcrumb'); ?>
				</div>
				<div class="col-sm-6">
					<br>
					<div class="g24-col-sm-24" style="text-align:right;padding-right:0px;margin-right:0px;">
						<a class="link-line-none" href="<?=base_url('report_loan_data/loan_already_transfer_report_index')?>">
							<button class="btn btn-primary" style="margin-right:5px;">รายงานโอนเงินกู้</button>
						</a>
					</div>
				</div>
			</div>
		</div>
		<div class="row gutter-xs">
			<div class="g24-col-sm-24">
				<div class="panel panel-body">
					<form method="GET" action="">
						<div class="g24-col-sm-24">
							<label class="g24-col-sm-2 control-label">ประเภทเงินกู้</label>
							<div class="g24-col-sm-3 m-b-1">
								<select class="form-control" name="loan_type" id="loan_type" onchange="change_type()">
									<option value="">เลือกประเภทเงินกู้</option>
									<?php foreach($loan_type as $key => $value){ ?>
										<option value="<?php echo $key; ?>" <?php echo $key == @$_GET['loan_type']?'selected':'';?>><?php echo $value; ?></option>
									<?php } ?>
								</select>
							</div>
							<label class="g24-col-sm-1 control-label">ชื่อเงินกู้</label>
							<div class="g24-col-sm-3 m-b-1">
								<select class="form-control" name="loan_name" id="loan_name">
									<option value="">เลือกชื่อเงินกู้</option>
									<?php foreach($loan_name as $key => $value){ ?>
										<option value="<?php echo $key; ?>" <?php echo $key==@$_GET['loan_name']?'selected':''; ?>><?php echo $value; ?></option>
									<?php } ?>
								</select>
							</div>

							<label class="g24-col-sm-2 control-label datepicker1" for="approve_date">วันที่สั่งจ่ายเงินกู้</label>
							<div class="input-with-icon g24-col-sm-3">
								<div class="form-group">
									<input id="approve_date" name="approve_date" class="form-control m-b-1 form_date_picker" type="text" value="<?php echo (@$_GET['approve_date'] != '')?@$_GET['approve_date']:''; ?>" data-date-language="th-th" autocomplete="off">
									<span class="icon icon-calendar input-icon m-f-1"></span>
								</div>
							</div>
							<label class="g24-col-sm-1 control-label datepicker1 text-center" style="text-align:center" for="thru_date">ถึง</label>
							<div class="input-with-icon g24-col-sm-3">
								<div class="form-group">
									<input id="thru_date" name="thru_date" class="form-control m-b-1 form_date_picker" type="text" value="<?php echo (@$_GET['thru_date'] != '')?@$_GET['thru_date']:''; ?>" data-date-language="th-th" autocomplete="off">
									<span class="icon icon-calendar input-icon m-f-1"></span>
								</div>
							</div>
							<div class="g24-col-sm-1">
								<input type="submit" class="btn btn-primary" value="ค้นหา">
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
		
		<div class="row gutter-xs">
			<div class="col-xs-12 col-md-12">
				<div class="panel panel-body" style="padding-top:0px !important;">
				  <h3></h3>
					 <table class="table table-bordered table-striped table-center">
					 <thead> 
						<tr class="bg-primary">
							<th>วันที่ทำรายการ</th>
							<th>ประเภทเงินกู้</th>
							<th>รหัสสมาชิก</th>
							<th>ชื่อสมาชิก</th>
							<th>ยอดเงิน</th>
							<th>ผู้ทำรายการ</th>
							<th>สถานะ</th>
							<th>จัดการ</th> 
						</tr> 
					 </thead>
						<tbody id="table_first">
						<?php //echo '<pre>'; print_r($data); echo '</pre>';?>
						  <?php 
							if(!empty($data)){
							foreach($data as $key => $row ){ ?>							
							  <tr> 
								  <td><?php echo @$this->center_function->ConvertToThaiDate($row['createdatetime']); ?></td>
								  <td><?php echo $row['loan_type']; ?></td>
								  <td><?php echo @$row['member_id']; ?></td> 
								  <td class="text-left"><?php echo @$row['firstname_th']." ".@$row['lastname_th']; ?></td> 
								  <td class="text-right"><?php echo number_format(@$row['loan_amount'],2); ?></td> 
								  <td><?php echo @$row['user_name']; ?></td> 
								  <td><?php echo @$transfer_status[@$row['transfer_status']]; ?></td> 
								  <td style="font-size: 14px;">
										<a class="btn btn-info" id="" title="จ่ายเงินกู้" onclick="open_transfer_modal('<?php echo @$row['loan_id']; ?>', '<?php echo @$row['seq'];?>');">
											จ่ายเงินกู้
										</a>
								  </td>
							  </tr>
						  <?php } 
							}else{?>
							<tr> 
								  <td colspan="7">ไม่พบข้อมูล</td>
							  </tr>
							<?php } ?>
						  </tbody> 
						</table> 
				  </div>
			</div>
		</div>
		<?php echo @$paging ?>
	</div>
</div>

<div class="modal fade" id="transfer_modal" role="dialog" style="overflow-x: hidden;overflow-y: auto;">
	<div class="modal-dialog modal-lg modal-dialog-file">
		<div class="modal-content data_modal">
			<div class="modal-header modal-header-confirmSave">
				<button type="button" class="close" data-dismiss="modal">x</button>
				<h2 class="modal-title" id="type_name">จ่ายเงินกู้</h2>
			</div>
			<form action="<?php echo base_url(PROJECTPATH.'/loan/loan_transfer_save')?>" method="POST" id="form_loan_transfer" enctype="multipart/form-data">
				<div class="modal-body">
					<input id="loan_id" name="loan_id" type="hidden">
					<input id="installment_seq" name="seq" type="hidden">
					<input id="time_transfer" name="time_transfer" class="form-control m-b-1" type="hidden" value="<?php echo date('H:i'); ?>">
					<div class="g24-col-sm-24 modal_data_input">
						<div class="form-group g24-col-sm-24">
							<label class="g24-col-sm-6 control-label" for="form-control-2">วันโอนเงิน</label>
							<div class="g24-col-sm-8" >
								<div class="input-with-icon g24-col-sm-24" style="margin-left: -8px;">
									<div class="form-group">
										<input id="date_transfer" name="date_transfer" class="form-control m-b-1" style="padding-left: 50px;" type="text" data-date-language="th-th" value="<?php echo $this->center_function->mydate2date(date('Y-m-d'));?>" title="กรุณาป้อน วันที่">
										<span class="icon icon-calendar input-icon m-f-1"></span>
									</div>
								</div>				
							</div>
						</div>
						<div class="form-group g24-col-sm-24">
							<label class="g24-col-sm-6 control-label" for="form-control-2">เลขที่สัญญา</label>
							<div class="g24-col-sm-6" >
								<input id="contract_number" class="form-control" type="text" value="" readonly>					
							</div>
						</div>
						<div class="form-group g24-col-sm-24">
							<label class="g24-col-sm-6 control-label" for="form-control-2">รหัสสมาชิก</label>
							<div class="g24-col-sm-6" >
								<input class="form-control member_id all_input" id="member_id" type="text" value=""  readonly>
							</div>
							<label class="g24-col-sm-3 control-label" for="form-control-2">ชื่อสกุล</label>
							<div class="g24-col-sm-8" >
								<input class="form-control all_input" id="member_name" type="text" value=""  readonly>
							</div>
						</div>
						<div class="form-group g24-col-sm-24">
							<label class="g24-col-sm-6 control-label" for="form-control-2">ยอดเงินกู้</label>
							<div class="g24-col-sm-6" >
								<input class="form-control all_input" id="loan_amount" name="loan_amount"  type="text" value=""  readonly>
							</div>
						</div>
						<div class="form-group g24-col-sm-24">
							<label class="g24-col-sm-6 control-label" for="form-control-2">ยอดเงินที่ได้รับ</label>
							<div class="g24-col-sm-6" >
								<input class="form-control all_input" id="amount_transfer" name="amount_transfer" type="text" value="" required title="กรุณาป้อน ยอดเงินที่ได้รับ"  readonly>
							</div>
						</div>
						<div class="form-group g24-col-sm-24">
							<label class="g24-col-sm-6 control-label" for="transfer_balance">ยอดเงินได้รับคงเหลือ</label>
							<div class="g24-col-sm-6">
								<input class="form-control all_input" id="transfer_balance" name="transfer_balance" type="text" value="" required title="ยอดเงินได้รับคงเหลือ" readonly>
							</div>
							<label class="g24-col-sm-3 control-label" for="transfer_period">งวดที่จ่ายเงิน</label>
							<div class="g24-col-sm-8">
								<input class="form-control all_input" id="transfer_period" name="transfer_period" type="text" value="" required title="งวดที่จ่ายเงิน" readonly>
							</div>
						</div>
						<div class="form-group g24-col-sm-24">
							<label class="g24-col-sm-6 control-label" for="transfer_real_amount">ยอดเงินที่ต้องการโอน</label>
							<div class="g24-col-sm-6">
								<input class="form-control all_input" id="transfer_real_amount" name="transfer_real_amount" type="text" value="" required title="ยอดเงินที่ต้องการโอน" >
							</div>
						</div>
						<div class="form-group g24-col-sm-24">
							<label class="g24-col-sm-6 control-label" for="form-control-2">วิธีการชำระเงิน</label>
							<div class="g24-col-sm-18">
								<span id="show_pay_type2" style="">
									<input type="radio" name="pay_type" id="pay_type_0" value="0" onclick="set_bank('');set_branch_code('');show('');" checked=""> เงินสด &nbsp;&nbsp;
									<input type="radio" name="pay_type" id="pay_type_1" value="1" onclick="set_bank('');set_branch_code('');show('xd_sec');"> เงินโอน &nbsp;&nbsp;
									<input type="radio" name="pay_type" id="pay_type_2" value="2" onclick="set_bank('');set_branch_code('');show('che_sec');"> เช็คเงินสด &nbsp;&nbsp;
<!--									<input type="radio" name="pay_type" id="pay_type_3" value="3" onclick="set_bank('');set_branch_code('');show('other_sec');"> อื่นๆ &nbsp;&nbsp;-->
								</span>
							</div>
						</div>

						<div class="g24-col-sm-24" id="xd_sec" style="display:none;margin-bottom: 20px;">
							<div class="form-group">
								<label class="control-label g24-col-sm-6 m-b-1"></label>
								<div class="g24-col-sm-17">
									<div id="transfer_deposit">
										<div class="transfer_content">
											<div class="row transfer">
												<div class="g24-col-sm-24">
													<div class="form-group">
														<label class="control-label g24-col-sm-1" for="local_account_id"></label>
														<input type="radio" name="xd_bank_id" id="xd_1" onclick="set_bank('006');set_branch_code('0071');"><label for="xd_1"> ธ.กรุงไทย จำกัด สาขาการปิโตรเลียม</label>
													</div>
												</div>
												<div class="g24-col-sm-24">
													<div class="form-group">
														<label class="control-label g24-col-sm-1" for="local_account_id"></label>
														<input type="radio" name="xd_bank_id" id="xd_2" onclick="set_bank('002');set_branch_code('1082');"><label for="xd_2"> ธ.กรุงเทพ จำกัด สาขาเอนเนอร์ยี่ คอมเพล็กซ์</label>
													</div>
												</div>
												<div class="g24-col-sm-24">
													<div class="form-group">
														<label class="control-label g24-col-sm-1" for="local_account_id"></label>
														<input type="radio" name="xd_bank_id" id="xd_3" onclick="set_bank('011');set_branch_code('0211');"><label for="xd_3"> ธ.ทหารไทย จำกัด สาขาเอนเนอร์ยี่ คอมเพล็กซ์ </label>
													</div>
												</div>
											</div>
											<div class="row transfer">
												<div class="g24-col-sm-24">
													<div class="form-group">
														<label class="control-label g24-col-sm-1" for="local_account_id"></label>
														<input type="radio" name="xd_bank_id" id="xd_4" onclick="set_bank('');set_branch_code('');"><label for="xd_4"> บัญชีเงินฝาก </label>
														<select class="form-control" name="local_account_id" id="local_account_id" style="display: initial !important;width: 200px !important;">
															<option value="">เลือกบัญชี</option>
														</select>
													</div>
												</div>
											</div>
											<div class="row transfer">
												<div class="g24-col-sm-24">
													<div class="form-group">
														<label class="control-label g24-col-sm-1" for="transfer_other"></label>
														<input type="radio" name="xd_bank_id" id="xd_5" onclick="set_bank('');set_branch_code('');"><label for="xd_5"> อื่นๆ </label>
														<input type="text" name="transfer_other" id="transfer_other" class="form-control" style="display: initial !important;width: 200px !important;">
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="g24-col-sm-24" id="che_sec" style="display:none;margin-bottom: 20px;">
							<div class="form-group">
								<label class="control-label g24-col-sm-6 m-b-1"></label>
								<div class="g24-col-sm-17">
									<div id="cheque_deposit">
										<div class="cheque_content">
											<div class="row cheque">
												<div class="g24-col-sm-24">
													<div class="form-group">
														<label class="control-label g24-col-sm-1" for="local_account_id"></label>
														<input type="radio" name="che_bank_id" id="che_1" onclick="set_bank('006');set_branch_code('0071');"><label for="che_1"> ธ.กรุงไทย จำกัด สาขาการปิโตรเลียม</label>
													</div>
												</div>
												<div class="g24-col-sm-24">
													<div class="form-group">
														<label class="control-label g24-col-sm-1" for="local_account_id"></label>
														<input type="radio" name="che_bank_id" id="che_2" onclick="set_bank('002');set_branch_code('1082');"><label for="che_2"> ธ.กรุงเทพ จำกัด สาขาเอนเนอร์ยี่ คอมเพล็กซ์</label>
													</div>
												</div>
												<div class="g24-col-sm-24">
													<div class="form-group">
														<label class="control-label g24-col-sm-1" for="local_account_id"></label>
														<input type="radio" name="che_bank_id" id="che_3" onclick="set_bank('011');set_branch_code('0211');"><label for="che_3"> ธ.ทหารไทย จำกัด สาขาการปิโตรเลียม </label>
													</div>
												</div>
											</div>
											<div class="row cheque">
												<div class="g24-col-sm-24">
													<div class="form-group">
														<label class="control-label g24-col-sm-6" for="cheque_no">&nbsp;&nbsp;&nbsp;หมายเลขเช็ค :</label>
														<input class="form-control g24-col-sm-10" name="cheque_no" id="cheque_no" placeholder="ระบุบัญชีเงินฝาก" />
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="g24-col-sm-24" id="cheque_multi">
							<?php $this->load->view('loan/transfer_cheque_dialog');?>
						</div>
						<div class="g24-col-sm-24" id="other_sec" style="display:none;margin-bottom: 20px;">
							<div class="form-group">
								<label class="control-label g24-col-sm-6 m-b-1"></label>
								<div class="g24-col-sm-17">
									<div id="cheque_deposit">
										<div class="cheque_content">
											<div class="row cheque">
												<div class="g24-col-sm-24">
													<div class="form-group">
														<label class="control-label g24-col-sm-6" for="other">&nbsp;&nbsp;&nbsp;อื่นๆ :</label>
														<input class="form-control g24-col-sm-10" name="other" id="other" placeholder="ระบุ" />
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
													
						<div class="text-center">
							<input type="hidden" name="bank_id" id="bank_id">
							<input type="hidden" name="branch_code" id="branch_code">
							<button class="btn btn-primary" type="button" id="bt_loan_transfer" onclick="cash_submit()">จ่ายเงินกู้</button>
						</div>
						
					</div>
					&nbsp;
				</div>
			</form>
		</div>
	</div>
</div>
<div class="modal fade" id="report_filter_modal" role="dialog" style="overflow-x: hidden;overflow-y: auto;">
	<div class="modal-dialog modal-dialog-file">
		<div class="modal-content data_modal">
			<div class="modal-header modal-header-confirmSave">
				<button type="button" class="close" data-dismiss="modal">x</button>
				<h2 class="modal-title" id="type_name">รายงานโอนเงินกู้</h2>
			</div>
			<form action="<?php echo base_url(PROJECTPATH.'/report_loan_data/loan_already_transfer_report')?>" method="POST" id="form_print_report" enctype="multipart/form-data" target="_blank">
				<div class="modal-body">
						<div class="form-group g24-col-sm-24" >						
							<label class="g24-col-sm-6 control-label">วันที่</label>
							<div class="input-with-icon g24-col-sm-7" >
								<div class="form-group">
									<input id="date_start" name="date_start" class="form-control" style="padding-left: 50px;" type="text" value="<?php echo $this->center_function->mydate2date(date('Y-m-d')); ?>" data-date-language="th-th" required title="" >
									<span class="icon icon-calendar input-icon m-f-1"></span>
								</div>	
							</div>
							<label class="g24-col-sm-2 control-label" style="text-align:center;">ถึง</label>
							<div class="input-with-icon g24-col-sm-7" >
								<div class="form-group">
									<input id="date_end" name="date_end" class="form-control" style="padding-left: 50px;" type="text" value="<?php echo $this->center_function->mydate2date(date('Y-m-d')); ?>" data-date-language="th-th" required title="" >
									<span class="icon icon-calendar input-icon m-f-1"></span>
								</div>	
							</div>
						</div>	
						<div class="form-group g24-col-sm-24" >						
							<label class="g24-col-sm-6 control-label">ประเภทเงินกู้</label>
							<div class="g24-col-sm-16" >
								<select class="form-control" name="loan_type" id="loan_type" onchange="change_type()">
									<option value="">เลือกประเภทเงินกู้</option>
									<?php foreach($loan_type as $key => $value){ ?>
										<option value="<?php echo $key; ?>" <?php echo $key == @$_GET['loan_type']?'selected':'';?>><?php echo $value; ?></option>
									<?php } ?>
								</select>				
							</div>
						</div>		
						<div class="form-group g24-col-sm-24" >						
							<label class="g24-col-sm-6 control-label">ชื่อเงินกู้</label>
							<div class="g24-col-sm-16" >
								<select class="form-control" name="loan_name" id="loan_name">
									<option value="">เลือกชื่อเงินกู้</option>
								</select>			
							</div>
						</div>						
						<div class="text-center">
							<button class="btn btn-primary" type="submit">พิมพ์รายงาน</button>
						</div>
					&nbsp;
				</div>
			</form>
		</div>
	</div>
</div>
<?php
$v = date('YmdHis');
$link = array(
    'src' => PROJECTJSPATH.'assets/js/loan_transfer.js?v='.$v,
    'type' => 'text/javascript'
);
echo script_tag($link);
?>
