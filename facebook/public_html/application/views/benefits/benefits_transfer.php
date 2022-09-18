<div class="layout-content">
    <div class="layout-content-body">
		<style>
			.center {
				text-align: center;
			}
			.modal-dialog-account {
				margin:auto;
				margin-top:7%;
			}
			.input-with-icon {
				margin-bottom: 5px;
			}
			
			.input-with-icon .form-control{
				padding-left: 40px;
			}
			.modal_data_input{
				margin-left:-5px;
			}
		</style> 
		<h1 style="margin-bottom: 0">ชำระสวัสดิการ</h1>
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 padding-l-r-0">
			<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6 padding-l-r-0">
			<?php $this->load->view('breadcrumb'); ?>
			</div>
			<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6 " style="padding-right:0px;text-align:right;">
				<div class="col-sm-9"></div>
				<div class="col-sm-3 btn-col">
					<button name="bt_view" id="pay_checked_btn" type="button" class="btn btn-primary btn-lg bt-add" style="width:100% !important;">
						<span>ชำระเงิน</span>
					</button>
				</div>   
			</div>
		</div>
		<div class="row gutter-xs">
			<div class="col-xs-12 col-md-12">
				<div class="panel panel-body" style="padding-top:0px !important;">
					<h3 >รายการชำระสวัสดิการ</h3>
					 <table class="table table-bordered table-striped table-center">
					 <thead> 
						<tr class="bg-primary">
							<th>
								<input type="checkbox" id="req_check_all" value="">
							</th>
							<th>วันที่ทำรายการ</th>
							<th>ชื่อสมาชิก</th>
							<th>เลขที่คำร้อง</th>
							<th>ยอดเงิน</th>
							<th>ผู้ทำรายการ</th>
							<th>สถานะ / วันที่</th>
							<th>ผู้โอนเงิน</th> 
							<th></th> 
							<th></th> 
						</tr> 
					 </thead>
					 <tbody id="table_first">
					 <form data-toggle="validator" method="post" action="<?php echo base_url(PROJECTPATH.'/benefits/benefits_transfer_save'); ?>" class="g24 form form-horizontal" enctype="multipart/form-data" autocomplete="off" id="form1">
					  <?php 
						foreach($data as $key => $row ){ 
						?>
						  <tr> 
						  	  <td>
								<?php
									if(empty($row['record_date']) || @$row['transfer_status'] == '1'){
								?>
							  	<input type="checkbox" class="req_check" id="req_check_<?php echo $row['benefits_request_id']; ?>" name="benefits_request_id[]" value="<?php echo $row['benefits_request_id']; ?>">
								<?php
									}
								?>
							  </td>
							  <td><?php echo $this->center_function->ConvertToThaiDate($row['createdatetime']); ?></td>
							  <td><?php echo $row['firstname_th']." ".$row['lastname_th']; ?></td> 
							  <td><a class="text-edit" onclick="edit_request('<?php echo @$row['benefits_request_id'] ?>','<?php echo @$row['member_id'] ?>')"><?php echo $row['benefits_no']; ?></a></td> 
							  <td style="text-align: right;"><?php echo number_format($row['benefits_approved_amount'],2); ?></td> 
							  <td><?php echo $row['user_name']; ?></td> 
							  <td>
								<?php echo @$transfer_status[$row['transfer_status']]; ?>
								<?php echo !empty($row["account_id"]) ? "บช.".$row["account_id"] : ""; ?>
								<?php
									$record_date = @$row['record_date'];							
									if($row['transfer_status'] == '1'){
										$record_date = @$row['cancel_date'];	
									}
									echo (!empty($record_date))?'/'.$this->center_function->ConvertToThaiDate(@$record_date):''; 
								?>
							  </td>
							  <td><?php echo $row['user_name_transfer']; ?></td>
							  <td style="font-size: 14px;">
									<?php 
										if(empty($row['record_date']) || @$row['transfer_status'] == '1'){
									?>
										<a class="btn-radius btn-info" id="transfer_<?php echo $row['benefits_request_id']; ?>_1" title="ทำรายการโอน/ชำระ" onclick="transfer_benefits('<?php echo $row['benefits_request_id']; ?>','save')">
											ชำระเงิน
										</a>
									<?php }else{?>
										<a class="btn-radius btn-info" id="transfer_<?php echo $row['benefits_request_id']; ?>_2" title="แสดงรายการโอน/ชำระ" onclick="transfer_benefits('<?php echo $row['benefits_request_id']; ?>','view')">
											แสดงรายการ
										</a>
										<!--<a  id="transfer_<?php echo $row['benefits_request_id']; ?>_3" title="ยกเลิก" style="cursor: pointer;padding-left:2px;padding-right:2px" onclick="transfer_cancel('<?php echo $row['benefits_request_id']; ?>','<?php echo $row['benefits_transfer_id']; ?>')"><span class="icon icon-times-circle-o"></span></a>-->
									<?php }?>
							  </td>
							  <td>
									<?php
										if(!empty($row["receipt_id"])) {
									?>
										<a class="btn-radius btn-info" id="receipt_print_<?php echo $row['benefits_request_id']; ?>_1" title="พิมพ์ใบเสร็จ" href="<?php echo PROJECTPATH."/admin/receipt_form_pdf/".$row["receipt_id"];?>" target="_blank">
											พิมพ์ใบเสร็จ
										</a>
									<?php
										}
									?>
							  </td>
						  </tr>
					  <?php } ?>
					  </form>
					  </tbody> 
					  </table> 
					</div>
			  </div>
		</div>
		<?php echo @$paging ?>
	</div>
</div>

<div class="modal fade" id="myModalRequest"  tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-account" style="width:80%">
        <div class="modal-content">
            <div class="modal-header modal-header-confirmSave">
                <button type="button" class="close" data-dismiss="modal"></button>
                <h2 class="modal-title">คำร้องขอสวัสดิการ</h2>
            </div>
            <div class="modal-body">
				<form data-toggle="validator" method="post" action="" class="g24 form form-horizontal" enctype="multipart/form-data" autocomplete="off" id="from_save">
					<input type="hidden" name="benefits_request_id" id="benefits_request_id" value=""/>
					<div class="g24-col-sm-24 m-t-1">
						<div class="form-group">
							<label class="g24-col-sm-6 control-label">รหัสสมาชิก <span id="naja"></span> </label>
							<div class="g24-col-sm-6">
								<div class="form-group">
									<input id="member_id" name="member_id" class="form-control" style="text-align:left;" type="number" value="" readonly="readonly" required title="กรุณาป้อน รหัสสมาชิก" />
								</div>
							</div>
							<label class="g24-col-sm-3 control-label" for="member_name">ชื่อสกุล</label>
							<div class="g24-col-sm-8">
								<div class="form-group">
									<input type="text" class="form-control" name="member_name" id="member_name" value=""  readonly="readonly">
								</div>
							</div>
						</div>
						<div class="form-group">
							<label class="g24-col-sm-6 control-label" for="birthday"> วันเกิด </label>
							<div class="g24-col-sm-6" id="birthday_con">
								<div class="form-group">
									<input type="text" class="form-control" name="birthday" id="birthday" value=""  readonly="readonly">
								</div>
							</div>
							
							<label class="g24-col-sm-3 control-label">อายุ</label>
							<div class="g24-col-sm-6">
								<div class="form-group" id="birthday_border">
									<input type="text" class="form-control" name="age" id="age" value=""  readonly="readonly">
								</div>
							</div>
						</div>
						
						<div class="form-group">
							<label class="g24-col-sm-6 control-label">วันที่เข้าเป็นสมาชิก </label>
							<div class="g24-col-sm-6">
								<div class="form-group">
									<input type="text" class="form-control" name="apply_date" id="apply_date" value=""  readonly="readonly">
								</div>
							</div>
							<label class="g24-col-sm-3 control-label">อายุสมาชิก </label>
							<div class="g24-col-sm-6">
								<div class="form-group">
									<input type="text" class="form-control" name="apply_age" id="apply_age" value=""  readonly="readonly">
								</div>
							</div>
						</div>
						
						<div class="form-group">
							<label class="g24-col-sm-6 control-label">กำหนดอายุเกษียณ </label>
							<div class="g24-col-sm-4">
								<div class="form-group">
									<input type="text" class="form-control" name="retry_date" id="retry_date" value=""  readonly="readonly">
								</div>
							</div>
							<label class="g24-col-sm-3 control-label">สถานะการเกษียณ </label>
							<div class="g24-col-sm-8">
								<div class="form-group">
									<input type="text" class="form-control" name="retry_status" id="retry_status" value=""  readonly="readonly">
								</div>
							</div>
						</div>
						<div class="form-group">
							<label class="g24-col-sm-6 control-label">เลือกสวัสดิการ </label>
							<div class="g24-col-sm-18">
								<div class="form-group">
									<select name="benefits_type_id" id="benefits_type_id" class="form-control" style="width:50%;" onchange="change_type()" required title="กรุณาเลือก สวัสดิการ" readonly="readonly">
										<option value="">เลือกสวัสดิการ</option>
									<?php 
										if(!empty($benefits_type)){
											foreach($benefits_type as $key => $value){ ?>
											<option value="<?php echo $value['benefits_id']; ?>" <?php echo $value['benefits_id']==@$data['benefits_type_id']?'selected':''; ?>><?php echo $value['benefits_name']; ?></option>
									<?php 
											}
										} 
									?>
									</select>
								</div>
							</div>
						</div>
						
						<div class="form-group">
							<label class="g24-col-sm-6 control-label">รายละเอียดสวัสดิการ </label>
							<div class="g24-col-sm-18">
								<div class="form-group">
									<div id="benefits_request_detail" style="background: #e0e0e0;border: 1px solid #e0e0e0;margin-top: 10px;margin: 5px 0px 5px 0px;width: 100%;height: 300px;padding: 5px;border-radius: 3px; overflow-y: scroll;"><?php echo @$row['benefits_request_detail']; ?></div>
								</div>
							</div>
						</div>
						<div class="form-group">
							<label class="g24-col-sm-6 control-label">แนบไฟล์คำร้อง </label>
							<div class="g24-col-sm-7">
								<!--<div class="g24-col-sm-12">
									<div class="form-group">
										<input type="file" class="form-control" name="benefits_request_file[]" value="" multiple>
									</div>
								</div>-->
								<div class="g24-col-sm-12">
									<button class="btn btn-primary btn-after-input" id="btn_show_file" type="button" onclick="show_file()" style="display:none;"><span>แสดงไฟล์แนบ</span></button>
									<button class="btn btn-danger btn-after-input" id="btn_show_not_file" type="button" style="display:none;"><span>ไม่พบไฟล์แนบ</span></button>
								</div>
							</div>
							<label class="g24-col-sm-4 control-label">ยอดเงินสวัสดิการที่อนุมัติ </label>
							<div class="g24-col-sm-5">
								<div class="form-group">
									<input type="number" class="form-control" name="benefits_approved_amount" id="benefits_approved_amount" value=""  required title="กรุณาป้อน ยอดเงินสวัสดิการที่อนุมัติ" readonly="readonly">
								</div>
							</div>
						</div>
						<div class="form-group">
							<label class="g24-col-sm-6 control-label"> &nbsp;</label>
							<div class="g24-col-sm-18">
								<label class="control-label">
									<input type="checkbox" id="benefits_check_condition" name="benefits_check_condition"  value="1" disabled="disabled">
									<span>ตรวจสอบแล้วผ่านเกณฑ์เงื่อนไข</span>								
									<span style="padding-left: 15px;">ผู้ตรวจสอบและทำรายการ  <span id="user_name"></span></span>
									<ib>
								</label>
								<input type="hidden" class="form-control" name="user_name_session" id="user_name_session" value="<?php echo $_SESSION['USER_NAME'];?>">								 
							</div>
						</div>
					</div>
				</form>
            </div>
			
            <div class="text-center m-t-1" style="padding-top:10px;">
				<button class="btn btn-info" onclick="close_modal('myModalRequest')"><span class="icon icon-close"></span> ออก</button>
            </div>
			<div class="text_center m-t-1">&nbsp;</div>
        </div>
    </div>
</div>

<div class="modal fade" id="show_file_attach" role="dialog">
	<div class="modal-dialog modal-dialog-file">
	  <div class="modal-content data_modal">
		<div class="modal-header modal-header-confirmSave">
		  <button type="button" class="close" onclick="close_modal('show_file_attach')">&times;</button>
		  <h2 class="modal-title">แสดงไฟล์แนบ</h2>
		</div>
		<div class="modal-body" id="show_file_space">
		</div>
	  </div>
	</div>
</div>

<div class="modal fade" id="viewRequest"  tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-account" style="width:80%">
        <div class="modal-content">
            <div class="modal-header modal-header-confirmSave">
                <button type="button" class="close" data-dismiss="modal"></button>
                <h2 class="modal-title">ดูสวัสดิการ</h2>
            </div>
            <div class="modal-body">
				<form data-toggle="validator" method="post" action="" class="g24 form form-horizontal" enctype="multipart/form-data" autocomplete="off" id="from_view">
					<input type="hidden" name="benefits_request_id" id="benefits_request_id" value=""/>
					<div class="g24-col-sm-24 m-t-1">
						<div class="form-group">
							<label class="g24-col-sm-6 control-label">เลือกสวัสดิการ </label>
							<div class="g24-col-sm-10">
								<div class="form-group">
									<select name="benefits_type_id_view" id="benefits_type_id_view" class="form-control" style="" onchange="change_type_view()">
										<option value="">เลือกสวัสดิการ</option>
									<?php 
										if(!empty($benefits_type)){
											foreach($benefits_type as $key => $value){ ?>
											<option value="<?php echo $value['benefits_id']; ?>"><?php echo $value['benefits_name']; ?></option>
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
									<input type="text" class="form-control" name="start_date_view" id="start_date_view" value=""  readonly="readonly">
								</div>
							</div>
						</div>
						
						<div class="form-group">
							<label class="g24-col-sm-6 control-label">รายละเอียดสวัสดิการ </label>
							<div class="g24-col-sm-18">
								<div class="form-group">
									<div id="benefits_request_detail_view" style="border: 1px solid #e0e0e0;margin-top: 10px;margin: 5px 0px 5px 0px;width: 100%;height: 300px;padding: 5px;border-radius: 3px;"><?php echo @$row['benefits_request_detail']; ?></div>
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

<div class="modal fade" id="show_transfer"  tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-account" style="width:80%">
        <div class="modal-content">
            <div class="modal-header modal-header-confirmSave">
                <button type="button" class="close" data-dismiss="modal"></button>
                <h2 class="modal-title">โอนเงิน/ชำระเงิน</h2>
            </div>
            <div class="modal-body">
				<form data-toggle="validator" method="post" action="<?php echo base_url(PROJECTPATH.'/benefits/benefits_transfer_save'); ?>" class="g24 form form-horizontal" enctype="multipart/form-data" autocomplete="off" id="from_transfer">
					<input type="hidden" name="benefits_request_id[]" id="benefits_request_id" class="benefits_request_id" value=""/>
					<input type="hidden" name="action" id="action" value=""/>
					<div class="g24-col-sm-24 m-t-1">
						<div class="form-group">
							<label class="g24-col-sm-6 control-label">รหัสสมาชิก <span id="naja"></span> </label>
							<div class="g24-col-sm-6">
								<div class="form-group">
									<input id="member_id" name="member_id" class="form-control member_id" style="text-align:left;" type="number" value="" readonly="readonly" required title="กรุณาป้อน รหัสสมาชิก" />
								</div>
							</div>
							<label class="g24-col-sm-3 control-label" for="member_name">ชื่อสกุล</label>
							<div class="g24-col-sm-8">
								<div class="form-group">
									<input type="text" class="form-control member_name" name="member_name" id="member_name" value=""  readonly="readonly">
								</div>
							</div>
						</div>
						<div class="form-group">
							<label class="g24-col-sm-6 control-label" for="benefits_no"> เลขที่คำร้อง </label>
							<div class="g24-col-sm-6" id="birthday_con">
								<div class="form-group">
									<input type="text" class="form-control benefits_no" name="benefits_no" id="benefits_no" value=""  readonly="readonly">
								</div>
							</div>
							
							<label class="g24-col-sm-3 control-label">สวัสดิการ</label>
							<div class="g24-col-sm-6">
								<div class="form-group" id="benefits_type_name">
									<input type="text" class="form-control benefits_type_name" name="benefits_type_name" id="benefits_type_name" value=""  readonly="readonly">
								</div>
							</div>
						</div>
						
						<div class="form-group">
							<label class="g24-col-sm-6 control-label">ยอดเงิน </label>
							<div class="g24-col-sm-6">
								<div class="form-group">
									<input type="text" class="form-control benefits_approved_amount" name="benefits_approved_amount" id="benefits_approved_amount" value=""  readonly="readonly">
								</div>
							</div>
							<label class="g24-col-sm-3 control-label">ผู้ทำรายการ </label>
							<div class="g24-col-sm-6">
								<div class="form-group">
									<input type="text" class="form-control admin_request" name="admin_request" id="admin_request" value=""  readonly="readonly">
								</div>
							</div>
						</div>
						
						<!-- <div class="form-group">
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
							<div class="g24-col-sm-6 " style="text-align:right;"><input type="radio" id="bank_choose_1" name="bank_type" value="1" onclick="change_bank_type()" <?php echo $checked_1; ?>></div>
							<div class=" g24-col-sm-18">
								<div class="form-group">
									บัญชีสหกรณ์ <input type="radio" id="bank_choose_2" name="bank_type" value="2" onclick="change_bank_type()" <?php echo $checked_2; ?>> บัญชีอื่นๆ 
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
								<div class="g24-col-sm-7" style="height: 47px;">
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
						</div> -->
						<!---->
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
									<input type="text" class="form-control mobile" name="mobile" id="mobile" value=""  readonly="readonly">
								</div>
							</div>
						</div>
					</div>
				</form>
            </div>
			
            <div class="text-center m-t-1" style="padding-top:10px;">
				<button class="btn btn-info btn-width-auto" onclick="check_form_transfer()" id="bt_save"><span class="icon icon-save"></span> บันทึกการทำรายการโอน/ชำระ</button>
				<button class="btn btn-info" onclick="close_modal('show_transfer')" id="bt_close"><span class="icon icon-close"></span> ออก</button>
            </div>
			<div class="text_center m-t-1">&nbsp;</div>
        </div>
    </div>
</div>
<?php
$link = array(
    'src' => PROJECTJSPATH.'assets/js/coop_benefits_transfer.js',
    'type' => 'text/javascript'
);
echo script_tag($link);
?>