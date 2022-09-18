<div class="modal fade" id="viewRequest"  tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-account" style="width:80%">
        <div class="modal-content">
            <div class="modal-header modal-header-confirmSave">
                <button type="button" class="close" data-dismiss="modal"></button>
                <h2 class="modal-title">ฌาปนกิจสงเคราะห์</h2>
            </div>
            <div class="modal-body">
				<form data-toggle="validator" method="post" action="" class="g24 form form-horizontal" enctype="multipart/form-data" autocomplete="off" id="from_view">
					<input type="hidden" name="cremation_request_id" id="cremation_request_id" value=""/>
					<div class="g24-col-sm-24 m-t-1">
						<div class="form-group">
							<label class="g24-col-sm-6 control-label">เลือกฌาปนกิจสงเคราะห์</label>
							<div class="g24-col-sm-10">
								<div class="form-group">
									<select name="cremation_type_id_view" id="cremation_type_id_view" class="form-control" style="" onchange="change_type_view()">
										<option value="">เลือกฌาปนกิจสงเคราะห์</option>
									<?php 
										if(!empty($cremation_type)){
											foreach($cremation_type as $key => $value){ ?>
											<option value="<?php echo $value['cremation_id']; ?>"><?php echo $value['cremation_name'].'('.$value['cremation_name_short'].')'; ?></option>
									<?php 
											}
										} 
									?>
									</select>
								</div>
							</div>
							<label class="g24-col-sm-3 control-label">มีผลวันที่ </label>
							<div class="g24-col-sm-5">
								<div class="form-group">
									<input type="text" class="form-control " name="start_date_view" id="start_date_view" value=""  readonly="readonly">
								</div>
							</div>
						</div>
						
						<div class="form-group">
							<label class="g24-col-sm-6 control-label">รายละเอียดฌาปนกิจสงเคราะห์</label>
							<div class="g24-col-sm-18">
								<div class="form-group">
									<div class="bs-example scrollbar" data-example-id="striped-table" id="cremation_request_detail_view"  style="border: 1px solid #e0e0e0;margin-top: 10px;margin: 5px 0px 5px 0px;width: 100%;border-radius: 3px;">
									<?php echo @$row['cremation_request_detail']; ?>
									</div>
								</div>
							</div>
						</div>						
					</div>
				</form>
            </div>
			
            <div class="text-center m-t-1" style="padding-top:10px;">
				<button class="btn btn-info" onclick="close_modal('viewRequest')"><span class="icon icon-close"></span> ออก</button>
            </div>
			<div class="text_center m-t-1">&nbsp;</div>
        </div>
    </div>
</div>