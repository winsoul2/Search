<div class="modal fade" id="show_request_receive"  tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-account" style="width:80%">
        <div class="modal-content">
            <div class="modal-header modal-header-confirmSave">
				<button type="button" class="close" onclick="close_modal('show_request_receive')">×</button>
                <h2 class="modal-title">ขอรับเงินฌาปนกิจสงเคราะห์</h2>
				
            </div>
            <div class="modal-body">
				<form data-toggle="validator" method="post" action="<?php echo base_url(PROJECTPATH.'/cremation/cremation_receive_save'); ?>" class="g24 form form-horizontal" enctype="multipart/form-data" autocomplete="off" id="from_receive">
					<input type="hidden" name="cremation_request_id" id="cremation_request_id" class="cremation_request_id" value=""/>
					<input type="hidden" name="member_id" id="member_id" class="member_id" value=""/>
					<input type="hidden" name="cremation_receive_id" id="cremation_receive_id" class="cremation_receive_id" value=""/>
					<input type="hidden" name="cremation_type_id" id="cremation_type_id" class="cremation_type_id" value=""/>
					<input type="hidden" name="pay_type" id="pay_type" class="pay_type" value=""/>
					<input type="hidden" class="cremation_detail_id" name="cremation_detail_id" id="cremation_detail_id" class="cremation_detail_id" value=""/>
					<input type="hidden" name="action" id="action" value=""/>
					<div class="g24-col-sm-24 m-t-1">
						<div class="form-group">							
							<label class="g24-col-sm-8 control-label">ชื่อฌาปนกิจสงเคราะห์</label>
							<div class="g24-col-sm-14">
								<div class="form-group" id="cremation_type_name">
									<input type="text" class="form-control cremation_type_name" name="cremation_type_name" id="cremation_type_name" value=""  readonly="readonly">
								</div>
							</div>
						</div>						
						<div class="form-group">
							<label class="g24-col-sm-8 control-label">จำนวนเงินที่ได้รับเมื่อเสียชีวิต </label>
							<div class="g24-col-sm-6">
								<div class="form-group">
									<input type="text" class="form-control cremation_receive_amount number_int_only" name="cremation_receive_amount" id="cremation_receive_amount" value=""  required readonly="readonly">
								</div>
							</div>
							<label class="g24-col-sm-8 control-label text-left">บาทต่อคน </label>
						</div>					
						<div class="form-group">
							<label class="g24-col-sm-8 control-label">ค่าดำเนินการสมาคม </label>
							<div class="g24-col-sm-6">
								<div class="form-group">
									<input type="text" class="form-control action_fee_percent  number_int_only" name="action_fee_percent" id="action_fee_percent" value=""  required readonly="readonly">
								</div>
							</div>
							<label class="g24-col-sm-8 control-label text-left">% ของเงินสงเคราะห์สมาชิก </label>
						</div>					
						<div class="form-group">
							<label class="g24-col-sm-8 control-label">คงเหลือที่จะได้รับ </label>
							<div class="g24-col-sm-6">
								<div class="form-group">
									<input type="text" class="form-control cremation_balance_amount number_int_only" name="cremation_balance_amount" id="cremation_balance_amount" value="" required readonly="readonly">
								</div>
							</div>
							<label class="g24-col-sm-8 control-label text-left">บาท </label>
						</div>
											
						<div class="form-group">
							<label class="g24-col-sm-8 control-label">แนบไฟล์คำร้อง </label>
							<div class="g24-col-sm-9 show_att">
								<label class="fileContainer btn btn-info">
									<span class="icon icon-paperclip"></span> 
									เลือกไฟล์
									<input type="file" class="form-control" name="cremation_request_file[]" value="" multiple>
								</label>
							</div>
							<div class="g24-col-sm-9">
								<button class="btn btn-primary btn-after-input btn_show_file" id="btn_show_file" type="button" onclick="show_file()" style="display:none;"><span>แสดงไฟล์แนบ</span></button>
								<button class="btn btn-danger btn-after-input btn_show_not_file" id="btn_show_not_file" type="button" style="display:none;"><span>ไม่พบไฟล์แนบ</span></button>
							</div>
						</div>
					</div>
				</form>
            </div>
			
            <div class="text-center m-t-1" style="padding-top:10px;">
				<button class="btn btn-info" onclick="check_form_request_receive();" id="bt_save" style="width: 160px;"><span class="icon icon-save"></span> บันทึก</button>
				<button class="btn btn-info" onclick="close_modal('show_request_receive')"><span class="icon icon-close"></span> ออก</button>
			</div>
			<div class="text_center m-t-1">&nbsp;</div>
        </div>
    </div>
</div>