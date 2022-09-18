<style>
	fieldset.scheduler-border {
		border: solid 2px #cccccc !important;
		padding: 0 10px 10px 10px;
		border-bottom: none;
	}

	legend.scheduler-border {
		width: auto !important;
		padding: 0 10px;
		border-bottom: none;
		font-size: 16px;
	}
</style>
<div class="modal fade" id="modal_transfer_detail" role="dialog">
	<div class="modal-dialog modal-lg">
		<div class="modal-content ">
			<div class="modal-header modal-bg-primary">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h3 class="modal-title">รายละเอียดยอดโอนเงิน</h3>
			</div>
			<div class="modal-body" style="height: 72vh">
				<fieldset class="scheduler-border">
					<legend class="scheduler-border">เงินกู้</legend>
					<div class="col-sm-12" style="margin-top: 2px; margin-bottom: 2px">
						<div class="form-group col-sm-6">
							<label class="col-sm-6 text-right control-label" for="loan_real_amount">
								จำนวนเงินที่จ่าย
							</label>
							<div class="col-sm-6">
								<input class="form-control" type="text" id="loan_real_amount">
							</div>
						</div>
						<div class="form-group col-sm-6">
							<label class="col-sm-4 text-right control-label" for="date_transfer">
								วันที่จ่ายเงินกู้
							</label>
							<div class="col-sm-6">
								<div class="input-with-icon g24-col-sm-24">
									<div class="form-group">
										<input id="date_transfer" class="form-control m-b-1 datepicker required"
											   style="padding-left: 50px;" type="text" data-date-language="th-th">
										<span class="icon icon-calendar input-icon m-f-1"></span>
									</div>
								</div>
							</div>

						</div>
					</div>
					<div class="col-sm-12" style="margin-top: 2px; margin-bottom: 2px">
						<div class="form-group col-sm-6">
							<label class="col-sm-6 text-right control-label" for="deduct_before_interest">
								 ดอกเบี้ย ณ วันจ่ายเงิน
							</label>
							<div class="col-sm-6">
								<input class="form-control decrease" type="text" id="deduct_before_interest">
							</div>
					</div>
				</fieldset>
				<fieldset class="scheduler-border">
					<legend class="scheduler-border">ค่าทำเนียม</legend>
					<?php for($i=0; $i < ceil(sizeof(@$deduct_list)/2); $i++ ){ ?>
					<div class="col-sm-12" style="margin-top: 2px; margin-bottom: 2px">
						<div class="form-group col-sm-6">
							<label class="col-sm-6 text-right control-label" for="<?php echo $deduct_list[($i*2)]['loan_deduct_list_code']; ?>">
								<?php echo $deduct_list[($i*2)]['loan_deduct_list']; ?>
							</label>
							<div class="col-sm-6">
								<input class="form-control decrease" type="text" id="<?php echo $deduct_list[($i*2)]['loan_deduct_list_code'];?>">
							</div>
						</div>
						<div class="form-group col-sm-6">
							<?php if(isset($deduct_list[($i*2)+1])){?>
							<label class="col-sm-6 text-right control-label" for="<?php echo $deduct_list[($i*2)+1]['loan_deduct_list_code']; ?>">
								<?php echo $deduct_list[($i*2)+1]['loan_deduct_list']; ?>
							</label>
							<div class="col-sm-6">
								<input class="form-control decrease" type="text" id="<?php echo $deduct_list[($i*2)+1]['loan_deduct_list_code'];?>">
							</div>
							<?php }else{
								echo "&nbsp;";
							} ?>
						</div>
					</div>
					<?php } ?>
<!--					<div class="col-sm-12" style="margin-top: 5px; margin-bottom: 5px">-->
<!--						<div class="form-group col-sm-6">-->
<!--							<label class="col-sm-6 text-right control-label" for="deduct_survey_fee">-->
<!--								ค่าทำเนียมตรวจสอบที่ดิน-->
<!--							</label>-->
<!--							<div class="col-sm-6">-->
<!--								<input class="form-control decrease" type="text" id="deduct_survey_fee">-->
<!--							</div>-->
<!--						</div>-->
<!--						<div class="form-group col-sm-6">-->
<!--							<label class="col-sm-4 text-right control-label" for="deduct_law">-->
<!--								ค่าทำนิติกรรม-->
<!--							</label>-->
<!--							<div class="col-sm-6">-->
<!--								<input class="form-control decrease" type="text" id="deduct_law">-->
<!--							</div>-->
<!--						</div>-->
<!--					</div>-->
				</fieldset>
				<fieldset class="scheduler-border">
					<legend class="scheduler-border">สรุปรายการ</legend>
					<div class="col-sm-12" style="margin-top: 2px; margin-bottom: 2px">
						<div class="form-group col-sm-6">
							<label class="col-sm-6 text-right control-label" for="total_all_receiver">
								จำนวนเงินจ่ายจริง
							</label>
							<div class="col-sm-6">
								<input class="form-control" type="text" id="total_all_receiver">
							</div>
						</div>
<!--						<div class="form-group col-sm-6">-->
<!--							<label class="col-sm-4 text-right control-label" for="fullname_th">-->
<!--								ชื่อผู้รับเงิน-->
<!--							</label>-->
<!--							<div class="col-sm-6">-->
<!--								<input class="form-control" type="text" id="fullname_th">-->
<!--							</div>-->
<!--						</div>-->
					</div>
					<input type="hidden" id="deduct_all_total">
				</fieldset>
				<fieldset class="scheduler-border cheque-container">
					<legend class="scheduler-border">รายการเช็คเงินสด</legend>
				</fieldset>
			</div>
			<div class="modal-footer">
				<input type="hidden" id="ref_line_number">
				<button type="button" class="btn btn-primary" onclick="confirmBtnDeduct(this)">ตกลง</button>
				<button type="button" class="btn btn-default" data-dismiss="modal">ยกเลิก</button>
			</div>
		</div>
	</div>
</div>
