<div class="modal fade" id="show_transfer"  tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-account" style="width:80%">
        <div class="modal-content">
            <div class="modal-header modal-header-confirmSave">
                <button type="button" class="close" data-dismiss="modal"></button>
                <h2 class="modal-title">อนุมัติขอรับเงินฌาปนกิจสงเคราะห์</h2>
            </div>
            <div class="modal-body">
				<form data-toggle="validator" method="post" action="<?php echo base_url(PROJECTPATH.'/cremation/cremation_transfer_save'); ?>" class="g24 form form-horizontal" enctype="multipart/form-data" autocomplete="off" id="from_transfer">
					<input type="hidden" name="cremation_transfer_id" id="cremation_transfer_id" class="cremation_transfer_id" value=""/>
					<input type="hidden" name="cremation_request_id" id="cremation_request_id" class="cremation_request_id" value=""/>
					<input type="hidden" name="cremation_receive_id" id="cremation_receive_id" class="cremation_receive_id" value=""/>
					<input type="hidden" name="cremation_resign_id" id="cremation_resign_id" class="cremation_resign_id" value=""/>
					<input type="hidden" name="action" id="action" value=""/>
					<input type="hidden" name="member_id" id="member_id" class="member_id" value=""/>
					<div class="g24-col-sm-24 m-t-1">
						<div class="form-group">							
							<label class="g24-col-sm-6 control-label">ชื่อฌาปนกิจสงเคราะห์</label>
							<div class="g24-col-sm-14">
								<div class="form-group" id="cremation_type_name">
									<input type="text" class="form-control cremation_type_name" name="cremation_type_name" id="cremation_type_name" value=""  readonly="readonly">
								</div>
							</div>
						</div>						
						<div class="form-group cremation_receive_amount_div">
							<label class="g24-col-sm-6 control-label">จำนวนเงินที่ได้รับเมื่อเสียชีวิต </label>
							<div class="g24-col-sm-6">
								<div class="form-group">
									<input type="text" class="form-control cremation_receive_amount number_int_only" name="cremation_receive_amount" id="cremation_receive_amount" value=""  required readonly="readonly">
								</div>
							</div>
							<label class="g24-col-sm-8 control-label text-left">บาทต่อคน </label>
						</div>					
						<div class="form-group action_fee_percent_div">
							<label class="g24-col-sm-6 control-label">ค่าดำเนินการสมาคม </label>
							<div class="g24-col-sm-6">
								<div class="form-group">
									<input type="text" class="form-control action_fee_percent  number_int_only" name="action_fee_percent" id="action_fee_percent" value=""  required readonly="readonly">
								</div>
							</div>
							<label class="g24-col-sm-8 control-label text-left">% ของเงินสงเคราะห์สมาชิก </label>
						</div>					
						<div class="form-group">
							<label class="g24-col-sm-6 control-label">คงเหลือที่จะได้รับ </label>
							<div class="g24-col-sm-6">
								<div class="form-group">
									<input type="text" class="form-control cremation_balance_amount number_int_only" name="cremation_balance_amount" id="cremation_balance_amount" value="" required readonly="readonly">
								</div>
							</div>
							<label class="g24-col-sm-8 control-label text-left">บาท </label>
						</div>
						
						<div class="form-group">
							<?php
								if(@$data['bank_type'] == ''){
									$checked_1 = 'checked';
									$checked_2 = '';
								}else{
									if(@$data['bank_type'] == '1'){
										$checked_1 = 'checked';
										$checked_2 = '';
									}else if(@$data['bank_type'] == '2'){
										$checked_1 = '';
										$checked_2 = 'checked';
									}
								}
							?>
							<label class="g24-col-sm-6 control-label">วิธีการชำระเงิน </label>
							<div class=" g24-col-sm-18 m-t-1">
								<div class="form-group">
									<input type="radio" id="bank_choose_1" name="bank_type" value="1" onclick="change_bank_type()" <?php echo $checked_1; ?>> โอนเข้าบัญชีสหกรณ์  
									<input type="radio" id="bank_choose_2" name="bank_type" value="2" onclick="change_bank_type()" <?php echo $checked_2; ?>> โอนเข้าบัญชีธนาคาร 
								</div>
							</div>
						</div>
						<div class="form-group">
							<div id="bank_type_1" style="display:none;">
								<label class="g24-col-sm-6 control-label" for="">ธนาคาร</label>
								<div class="g24-col-sm-18">
									<div class="form-group">
										<select name="account_id" id="account_id" class="form-control" style="width:50%;" onchange="" required title="กรุณาเลือก บัญชี" >
											<option value="">เลือกบัญชี</option>
										</select>
									</div>
								</div>
							</div>
							<div id="bank_type_2" style="display:none;">
								<label class="g24-col-sm-6 control-label" for="">ธนาคาร</label>
								<div class="g24-col-sm-2">
									<div class="form-group">
										<input id="bank_id_show" class="form-control group-bank-left" type="text" value="<?php echo @$data["bank_id"]; ?>" readonly>
									</div>
								</div>
								<div class=" g24-col-sm-7">
									<div class="form-group">
										<select id="dividend_bank_id" name="dividend_bank_id" class="form-control group-bank-right" onchange="change_bank()">
											<option value="">เลือกธนาคาร</option>
											<?php foreach($bank as $key => $value) { ?>
											<option value="<?php echo $value["bank_id"]; ?>" <?php if($value["bank_id"]==@$data["bank_id"]) { ?> selected="selected"<?php } ?> > <?php echo $value["bank_name"]; ?>
												</option><?php } ?>
										</select>
									</div>
								</div>
								<div class="g24-col-sm-7" style="height: 40px;">
									&nbsp;
								</div>

								<label class="g24-col-sm-6 control-label" for="">สาขา</label>
								<div class="g24-col-sm-2">
									<div class="form-group">
										<input id="branch_id_show" class="form-control group-bank-left" type="text" value="<?php echo @$data["bank_branch_id"]; ?>" readonly>
									</div>
								</div>
								
								<div class=" g24-col-sm-7">
									<div class="form-group">
										<span id="bank_branch">
											<select id="dividend_bank_branch_id"  name="dividend_bank_branch_id" class="form-control group-bank-right" onchange="change_branch()">
												<option value="">เลือกสาขาธนาคาร</option>
												<?php foreach($bank_branch as $key => $value) { ?>
													<option value="<?php echo $value["branch_id"]; ?>" <?php if($value["branch_id"] == @$data["bank_branch_id"]) { ?> selected="selected"<?php } ?>><?php echo $value["branch_name"]; ?></option>
												<?php } ?>
											</select>
										</span>
									</div>
								</div>
								<div class="g24-col-sm-7" style="height: 40px;">
									&nbsp;
								</div>

								<label class="g24-col-sm-6 control-label" for="">เลขที่บัญชี</label>
								<div class=" g24-col-sm-9">
									<div class="form-group">
										<input id="bank_account_no" class="form-control clear_pay" name="bank_account_no"  type="text" value="<?php echo @$data["bank_account_no"]; ?>">
									</div>
								</div>
								<div class="g24-col-sm-7" style="height: 40px;">
									&nbsp;
								</div>
								
								<div class="g24-col-sm-24 modal_data_input">
									<label class="g24-col-sm-6 control-label " >วันที่โอนเงิน</label>
									<div class="input-with-icon g24-col-sm-5">
										<div class="form-group">
											<input id="date_transfer_picker" name="date_transfer" class="form-control" type="text" value="<?php echo $this->center_function->mydate2date(date('Y-m-d')); ?>" data-date-language="th-th">
											<span class="icon icon-calendar input-icon m-f-1"></span>
										</div>
									</div>
								</div>
								<div class="g24-col-sm-24 modal_data_input">
									<label class="g24-col-sm-6 control-label " >เวลาโอนเงิน</label>
									<div class="input-with-icon g24-col-sm-5">
										<div class="form-group">
											<input id="time_transfer" name="time_transfer" class="form-control" type="text" value="<?php echo date('H:i'); ?>">
											<span class="icon icon-clock-o input-icon m-f-1"></span>
										</div>
									</div>
								</div>
								
								<label class="g24-col-sm-6 control-label">แนบหลักฐานการโอนเงิน</label>
								<div class="g24-col-sm-6">
									<div class="form-group">
										<label class="fileContainer btn btn-info">
											<span class="icon icon-paperclip"></span> 
											เลือกไฟล์
											<input type="file" class="form-control" name="file_name" id="file_name" value="" multiple aria-invalid="false" onchange="readURL(this);">
										</label>
									</div>
									<span id="register_file_space">
												
									</span>
								</div>
								<div class="g24-col-sm-7" style="height: 40px;">
									&nbsp;
								</div>
								
								<div id="file_show" style="display:none">
									<label class="g24-col-sm-6 control-label"></label>
									<div class="g24-col-sm-6">
										<div class="form-group">											
											<img src="" id="file_transfer" class="m-b-1" width="150px" height="150px">
										</div>
									</div>
								</div>									
							</div>
						</div>
						<div class="form-group">
							<label class="g24-col-sm-6 control-label">ผู้ทำรายการโอนเงิน/ชำระเงิน </label>
							<div class="g24-col-sm-6">
								<div class="form-group">
									<input type="text" class="form-control admin_transfer" name="admin_transfer" id="admin_transfer" value=""  readonly="readonly">
								</div>
							</div>
							<label class="g24-col-sm-3 control-label">วันที่ทำรายการ </label>
							<div class="g24-col-sm-6">
								<div class="form-group">
									<input type="text" class="form-control" name="createdatetime" id="createdatetime" value=""  readonly="readonly">
								</div>
							</div>
						</div>
						<div class="form-group">
							<label class="g24-col-sm-6 control-label">ส่ง SMS ไปที่เบอร์ </label>
							<div class="g24-col-sm-6">
								<div class="form-group">
									<input type="text" class="form-control mobile" name="mobile" id="mobile" value="" >
								</div>
							</div>
						</div>
					</div>
				</form>
            </div>
			
            <div class="text-center m-t-1" style="padding-top:10px;">
				<button class="btn btn-info bt_save" onclick="check_form_transfer()" id="bt_save"><span class="icon icon-save"></span> บันทึก</button>
				<button class="btn btn-info" onclick="close_modal('show_transfer')" id="bt_close"><span class="icon icon-close"></span> ออก</button>
            </div>
			<div class="text_center m-t-1">&nbsp;</div>
        </div>
    </div>
</div>