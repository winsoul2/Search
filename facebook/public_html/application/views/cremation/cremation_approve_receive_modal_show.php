<div class="modal fade" id="show_transfer"  tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-account" style="width:80%">
        <div class="modal-content">
            <div class="modal-header modal-header-confirmSave">
                <button type="button" class="close" data-dismiss="modal"></button>
                <h2 class="modal-title">อนุมัติขอรับเงินฌาปนกิจสงเคราะห์</h2>
            </div>
            <div class="modal-body">
				<form data-toggle="validator" method="post" action="<?php echo base_url(PROJECTPATH.'/cremation/approve_request_money'); ?>" class="g24 form form-horizontal" enctype="multipart/form-data" autocomplete="off" id="from_transfer">
					<input type="hidden" name="cremation_request_id" id="cremation_request_id" class="cremation_request_id" value=""/>
					<input type="hidden" name="cremation_receive_id" id="cremation_receive_id" class="cremation_receive_id" value=""/>
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
						<div class="form-group">
							<label class="g24-col-sm-6 control-label">จำนวนเงินที่ได้รับเมื่อเสียชีวิต </label>
							<div class="g24-col-sm-6">
								<div class="form-group">
									<input type="text" class="form-control cremation_receive_amount number_int_only text-right" name="cremation_receive_amount" id="cremation_receive_amount" value=""  required readonly="readonly">
								</div>
							</div>
							<label class="g24-col-sm-8 control-label text-left">บาท</label>
						</div>					
						<div class="form-group">
							<label class="g24-col-sm-6 control-label">ค่าดำเนินการสมาคม </label>
							<div class="g24-col-sm-6">
								<div class="form-group">
									<input type="text" class="form-control action_fee_percent  number_int_only text-right" name="action_fee_percent" id="action_fee_percent" value=""  required readonly="readonly">
								</div>
							</div>
							<label class="g24-col-sm-8 control-label text-left">บาท</label>
						</div>					
						<div class="form-group">
							<label class="g24-col-sm-6 control-label">คงเหลือที่จะได้รับ </label>
							<div class="g24-col-sm-6">
								<div class="form-group">
									<input type="text" class="form-control cremation_balance_amount number_int_only text-right" name="cremation_balance_amount" id="cremation_balance_amount" value="" required readonly="readonly">
								</div>
							</div>
							<label class="g24-col-sm-8 control-label text-left">บาท </label>
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