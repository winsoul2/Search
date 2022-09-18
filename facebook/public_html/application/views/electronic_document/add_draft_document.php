<div class="layout-content">
	<div class="layout-content-body">
		<style type="text/css">
			.form-group{
				margin-bottom: 5px;
			}
			.modal-dialog-account {
				margin:auto;
				margin-top:7%;
			}
			.user-list-div{
				padding-bottom: 14px;
			}
			.pointer {
				cursor: pointer;
			}
			.add-user-btn {
				width: 80%;
			}
			@media (min-width: 1600px) {
				.add-user-btn {
					width: 60%;
				}
			}
		</style>
		<h1 style="margin-bottom: 0">ร่างเอกสาร</h1>
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 padding-l-r-0">
			<div class="col-xs-6 col-sm-6 col-md-6 col-lg-7 padding-l-r-0">
				<?php $this->load->view('breadcrumb'); ?>
			</div>
		</div>
		<div class="row gutter-xs">
			<div class="col-xs-12 col-md-12">
				<div class="panel panel-body" style="padding-top:0px !important;">
					<div class="bs-example" data-example-id="striped-table">
						<form id='form_add' data-toggle="validator" novalidate="novalidate" action="<?php echo base_url(PROJECTPATH.'/elec_doc_draft/add_draft_document'); ?>" method="post"  enctype="multipart/form-data">
							<input type="hidden" name="document_id" value="<?php echo $_GET["id"]?>"/>
							<br/>
							<div class="col-xs-12 col-md-12"><h2 style="margin-bottom: 0">เพิ่มร่างเอกสาร</h2></div>
							<div id="tb_wrap" class="col-xs-12 col-md-12">
								<br/>
								<br/>
								<div class="form-group g24-col-sm-24">
									<label class="g24-col-sm-8 control-label">ชื่อเอกสาร</label>
									<div class="g24-col-sm-13">
										<div class="form-group">
											<input type="text" id="document_name" name="document_name" value="<?php echo !empty($document) ? $document->name : ""?>" class="form-control"/>
										</div>
									</div>
								</div>
								<div id="file_upload_div">
									<?php if(empty($files)) {?>
									<input type="hidden" id="max_file_index" value="1"/>
									<div class="form-group g24-col-sm-24" id="file_div_1">
										<label class="g24-col-sm-8 control-label">เอกสารแนบ</label>
										<div class="g24-col-sm-16">
											<label class="fileContainer btn btn-info g24-col-sm-7">
												<span class="icon icon-paperclip"></span> 
												แนบเอกสาร 1
												<input id="file_1" data-index="1" name="file[]" class="form-control m-b-1 file_upload" type="file" value="" >
											</label>
											<label id="filename_1" style="padding: 7px;"></label>
										</div>
									</div>
									<?php
										} else {
									?>
									<input type="hidden" id="max_file_index" value="<?php echo count($files)+1;?>"/>
									<?php
											foreach($files as $key => $file) {
									?>
									<div class="form-group g24-col-sm-24" id="file_div_<?php echo $key + 1;?>">
										<label class="g24-col-sm-8 control-label"><?php echo $key == 0 ? 'เอกสารแนบ' : "";?></label>
										<div class="g24-col-sm-16">
											<input id="file_<?php echo $key + 1;?>" data-index="<?php echo $key + 1;?>" name="file_ids[]" class="form-control m-b-<?php echo $key + 1;?> file_upload" type="hidden" value="<?php echo $file["id"];?>" >
											<label id="filename_<?php echo $key + 1;?>" style="padding: 7px;"><?php echo $file["name"];?><span class="icon icon-trash text-danger remove_file pointer" style="font-size: large; padding-left:5px;" id="remove_<?php echo $key + 1;?>" data-id="<?php echo $key + 1;?>" ></span></label>
										</div>
									</div>
									<?php
											}
									?>
									<div class="form-group g24-col-sm-24" id="file_div_<?php echo $key + 2;?>">
										<label class="g24-col-sm-8 control-label"></label>
										<div class="g24-col-sm-16">
											<label class="fileContainer btn btn-info g24-col-sm-7">
												<span class="icon icon-paperclip"></span> 
												แนบเอกสาร <?php echo $key + 2;?>
												<input id="file_<?php echo $key + 2;?>" data-index="<?php echo $key + 2;?>" name="file[]" class="form-control m-b-<?php echo $key + 2;?> file_upload" type="file" value="" >
											</label>
											<label id="filename_<?php echo $key + 2;?>" style="padding: 7px;"></label>
										</div>
									</div>
									<?php
										}
									?>
								</div>
								<div class="form-group g24-col-sm-24">
									<label class="g24-col-sm-8 control-label">ส่งตรวจทาน</label>
									<div class="g24-col-sm-3 control-label" style="padding-top:0;">
										<label class="g24-col-sm-24 control-label text-left">เลือกกลุ่ม</label>
									</div>
									<div class="g24-col-sm-6">
										<div class="form-group">
											<select id="group_id" name="group_id" class="form-control">
												<option value="">กรุณาเลือกกลุ่ม</option>
												<?php foreach($groups as $group){ ?>
													<option value="<?php echo $group["id"]; ?>" <?php echo $review_group_id == $group["id"] ? "selected" : "" ;?>><?php echo $group["group_name"]; ?></option>
												<?php } ?>
											</select>
										</div>
									</div>
									<div class="g24-col-sm-6">
										<button type="button" class="btn btn-primary add-user-btn" id="add-group-reviewer">เพิ่มกลุ่มผู้ตรวจทาน</button>
									</div>
								</div>
								<div class="form-group g24-col-sm-24">
									<label class="g24-col-sm-8 control-label"></label>
									<div class="g24-col-sm-3 control-label" style="padding-top:0;">
										<label class="g24-col-sm-24 control-label text-left">เลือกรายคน</label>
									</div>
									<div class="g24-col-sm-6">
										<div class="form-group">
											<select id="user-select" class="form-control">
												<option value="">กรุณาเลือกสมาชิก</option>
												<?php
													foreach($users as $user){
														if($_SESSION['USER_ID'] != $user["user_id"]) {
												?>
													<option value="<?php echo $user["user_id"]; ?>"><?php echo $user["user_name"]; ?></option>
												<?php
														}
													}
												?>
											</select>
										</div>
									</div>
									<div class="g24-col-sm-6">
										<button type="button" class="btn btn-primary add-user-btn" id="add-reviewer">เพิ่มผู้ตรวจทาน</button>
									</div>
								</div>
								<div class="form-group g24-col-sm-24 user-list-div">
									<label class="g24-col-sm-8 control-label"></label>
									<label class="g24-col-sm-3 control-label text-left"></label>
									<div class="g24-col-sm-13 control-label text-left" id="reviewer-div">
									<?php
										if(!empty($review_users)) {
											foreach($review_users as $user) {
									?>
											<label class="g24-col-sm-24 control-label text-left" id="review-label-<?php echo $user["user_id"];?>">
												<?php echo $user["user_name"];?>
												<input type="hidden" class="user_ids" name="user_ids[]" value="<?php echo $user["user_id"];?>"/>
												<span class="icon icon-trash text-danger remove_reviewer pointer" style="font-size: large; padding-left:5px;" id="remove_<?php echo $user["user_id"];?>" data-id="<?php echo $user["user_id"];?>" ></span>
											</label>
									<?php
											}
										}
									?>
									</div>
								</div>

								<div class="form-group g24-col-sm-24">
									<label class="g24-col-sm-8 control-label">ผู้อนุมัติเอกสารร่าง</label>
									<div class="g24-col-sm-3 control-label" style="padding-top:0;">
										<label class="g24-col-sm-24 control-label text-left">เลือกกลุ่ม</label>
									</div>
									<div class="g24-col-sm-6">
										<div class="form-group">
											<select id="approve_draft_group_id" name="approve_draft_group_id" class="form-control">
												<option value="">กรุณาเลือกกลุ่ม</option>
												<?php foreach($groups as $group){ ?>
													<option value="<?php echo $group["id"]; ?>"><?php echo $group["group_name"]; ?></option>
												<?php } ?>
											</select>
										</div>
									</div>
									<div class="g24-col-sm-6">
										<button type="button" class="btn btn-primary add-user-btn" id="add-group-approve-draft">เพิ่มกลุ่มผู้อนุมัติเอกสารร่าง</button>
									</div>
								</div>
								<div class="form-group g24-col-sm-24">
									<label class="g24-col-sm-8 control-label"></label>
									<div class="g24-col-sm-3 control-label" style="padding-top:0;">
										<label class="g24-col-sm-24 control-label text-left">เลือกรายคน</label>
									</div>
									<div class="g24-col-sm-6">
										<div class="form-group">
											<select id="user-approve-draft-select" class="form-control">
												<option value="">กรุณาเลือกสมาชิก</option>
												<?php
													foreach($users as $user){
														if($_SESSION['USER_ID'] != $user["user_id"]) {
												?>
													<option value="<?php echo $user["user_id"]; ?>"><?php echo $user["user_name"]; ?></option>
												<?php
														}
													}
												?>
											</select>
										</div>
									</div>
									<div class="g24-col-sm-6">
										<button type="button" class="btn btn-primary add-user-btn" id="add-approve-draft">เพิ่มผู้อนุมัติเอกสารร่าง</button>
									</div>
								</div>
								<div class="form-group g24-col-sm-24 user-list-div">
									<label class="g24-col-sm-8 control-label"></label>
									<label class="g24-col-sm-3 control-label text-left"></label>
									<div class="g24-col-sm-13 control-label text-left" id="approve-draft-div">
									<?php
										if(!empty($approve_draft_users)) {
											foreach($approve_draft_users as $user) {
									?>
											<label class="g24-col-sm-24 control-label text-left" id="approve-draft-label-<?php echo $user["user_id"];?>">
												<?php echo $user["user_name"];?>
												<input type="hidden" class="approve_draft_user_ids" name="approve_draft_user_ids[]" value="<?php echo $user["user_id"];?>"/>
												<span class="icon icon-trash text-danger remove_approve-draft pointer" style="font-size: large; padding-left:5px;" id="approve_draft_remove_<?php echo $user["user_id"];?>" data-id="<?php echo $user["user_id"];?>" ></span>
											</label>
									<?php
											}
										}
									?>
									</div>
								</div>

								<div class="form-group g24-col-sm-24">
									<label class="g24-col-sm-8 control-label">ผู้อนุมัติ</label>
									<div class="g24-col-sm-3 control-label" style="padding-top:0;">
										<label class="g24-col-sm-24 control-label text-left">เลือกกลุ่ม</label>
									</div>
									<div class="g24-col-sm-6">
										<div class="form-group">
											<select id="approve_group_id" name="approve_group_id" class="form-control">
												<option value="">กรุณาเลือกกลุ่ม</option>
												<?php foreach($groups as $group){ ?>
													<option value="<?php echo $group["id"]; ?>"><?php echo $group["group_name"]; ?></option>
												<?php } ?>
											</select>
										</div>
									</div>
									<div class="g24-col-sm-6">
										<button type="button" class="btn btn-primary add-user-btn" id="add-group-approve">เพิ่มกลุ่มผู้อนุมัติ</button>
									</div>
								</div>
								<div class="form-group g24-col-sm-24">
									<label class="g24-col-sm-8 control-label"></label>
									<div class="g24-col-sm-3 control-label" style="padding-top:0;">
										<label class="g24-col-sm-24 control-label text-left">เลือกรายคน</label>
									</div>
									<div class="g24-col-sm-6">
										<div class="form-group">
											<select id="user-approve-select" class="form-control">
												<option value="">กรุณาเลือกสมาชิก</option>
												<?php
													foreach($users as $user){
														if($_SESSION['USER_ID'] != $user["user_id"]) {
												?>
													<option value="<?php echo $user["user_id"]; ?>"><?php echo $user["user_name"]; ?></option>
												<?php
														}
													}
												?>
											</select>
										</div>
									</div>
									<div class="g24-col-sm-6">
										<button type="button" class="btn btn-primary add-user-btn" id="add-approve">เพิ่มผู้อนุมัติ</button>
									</div>
								</div>
								<div class="form-group g24-col-sm-24 user-list-div">
									<label class="g24-col-sm-8 control-label"></label>
									<label class="g24-col-sm-3 control-label text-left"></label>
									<div class="g24-col-sm-13 control-label text-left" id="approve-div">
									<?php
										if(!empty($approve_users)) {
											foreach($approve_users as $user) {
									?>
											<label class="g24-col-sm-24 control-label text-left" id="approve-label-<?php echo $user["user_id"];?>">
												<?php echo $user["user_name"];?>
												<input type="hidden" class="approve_user_ids" name="approve_user_ids[]" value="<?php echo $user["user_id"];?>"/>
												<span class="icon icon-trash text-danger remove_approve pointer" style="font-size: large; padding-left:5px;" id="approve_remove_<?php echo $user["user_id"];?>" data-id="<?php echo $user["user_id"];?>" ></span>
											</label>
									<?php
											}
										}
									?>
									</div>
								</div>

								<div class="form-group g24-col-sm-24">
									<label class="g24-col-sm-8 control-label">ผู้รับเอกสาร</label>
									<div class="g24-col-sm-3 control-label" style="padding-top:0;">
										<label class="g24-col-sm-24 control-label text-left">เลือกกลุ่ม</label>
									</div>
									<div class="g24-col-sm-6">
										<div class="form-group">
											<select id="receive_group_id" name="receive_group_id" class="form-control">
												<option value="">กรุณาเลือกกลุ่ม</option>
												<?php foreach($groups as $group){ ?>
													<option value="<?php echo $group["id"]; ?>"><?php echo $group["group_name"]; ?></option>
												<?php } ?>
											</select>
										</div>
									</div>
									<div class="g24-col-sm-6">
										<button type="button" class="btn btn-primary add-user-btn" id="add-group-receive">เพิ่มกลุ่มผู้รับเอกสาร</button>
									</div>
								</div>
								<div class="form-group g24-col-sm-24">
									<label class="g24-col-sm-8 control-label"></label>
									<div class="g24-col-sm-3 control-label" style="padding-top:0;">
										<label class="g24-col-sm-24 control-label text-left">เลือกรายคน</label>
									</div>
									<div class="g24-col-sm-6">
										<div class="form-group">
											<select id="user-receive-select" class="form-control">
												<option value="">กรุณาเลือกสมาชิก</option>
												<?php
													foreach($users as $user){
														if($_SESSION['USER_ID'] != $user["user_id"]) {
												?>
													<option value="<?php echo $user["user_id"]; ?>"><?php echo $user["user_name"]; ?></option>
												<?php
														}
													}
												?>
											</select>
										</div>
									</div>
									<div class="g24-col-sm-6">
										<button type="button" class="btn btn-primary add-user-btn" id="add-receive">เพิ่มผู้รับเอกสาร</button>
									</div>
								</div>
								<div class="form-group g24-col-sm-24 user-list-div">
									<label class="g24-col-sm-8 control-label"></label>
									<label class="g24-col-sm-3 control-label text-left"></label>
									<div class="g24-col-sm-13 control-label text-left" id="receive-div">
									<?php
										if(!empty($receive_users)) {
											foreach($receive_users as $user) {
									?>
											<label class="g24-col-sm-24 control-label text-left" id="receive-label-<?php echo $user["user_id"];?>">
												<?php echo $user["user_name"];?>
												<input type="hidden" class="receive_user_ids" name="receive_user_ids[]" value="<?php echo $user["user_id"];?>"/>
												<span class="icon icon-trash text-danger remove_receive pointer" style="font-size: large; padding-left:5px;" id="receive_remove_<?php echo $user["user_id"];?>" data-id="<?php echo $user["user_id"];?>" ></span>
											</label>
									<?php
											}
										}
									?>
									</div>
								</div>

								<div class="form-group g24-col-sm-24">
									<label class="g24-col-sm-8 control-label">เอกสารสำเนาถึง</label>
									<div class="g24-col-sm-3 control-label" style="padding-top:0;">
										<label class="g24-col-sm-24 control-label text-left">เลือกกลุ่ม</label>
									</div>
									<div class="g24-col-sm-6">
										<div class="form-group">
											<select id="cc_group_id" name="c_group_id" class="form-control">
												<option value="">กรุณาเลือกกลุ่ม</option>
												<?php foreach($groups as $group){ ?>
													<option value="<?php echo $group["id"]; ?>"><?php echo $group["group_name"]; ?></option>
												<?php } ?>
											</select>
										</div>
									</div>
									<div class="g24-col-sm-6">
										<button type="button" class="btn btn-primary add-user-btn" id="add-group-cc">เพิ่มกลุ่มผู้รับสำเนา</button>
									</div>
								</div>
								<div class="form-group g24-col-sm-24">
									<label class="g24-col-sm-8 control-label"></label>
									<div class="g24-col-sm-3 control-label" style="padding-top:0;">
										<label class="g24-col-sm-24 control-label text-left">เลือกรายคน</label>
									</div>
									<div class="g24-col-sm-6">
										<div class="form-group">
											<select id="user-cc-select" class="form-control">
												<option value="">กรุณาเลือกสมาชิก</option>
												<?php
													foreach($users as $user){
														if($_SESSION['USER_ID'] != $user["user_id"]) {
												?>
													<option value="<?php echo $user["user_id"]; ?>"><?php echo $user["user_name"]; ?></option>
												<?php
														}
													}
												?>
											</select>
										</div>
									</div>
									<div class="g24-col-sm-6">
										<button type="button" class="btn btn-primary add-user-btn" id="add-cc">เพิ่มผู้รับสำเนา</button>
									</div>
								</div>
								<div class="form-group g24-col-sm-24 user-list-div">
									<label class="g24-col-sm-8 control-label"></label>
									<label class="g24-col-sm-3 control-label text-left"></label>
									<div class="g24-col-sm-13 control-label text-left" id="cc-div">
									<?php
										if(!empty($cc_users)) {
											foreach($cc_users as $user) {
									?>
											<label class="g24-col-sm-24 control-label text-left" id="cc-label-<?php echo $user["user_id"];?>">
												<?php echo $user["user_name"];?>
												<input type="hidden" class="cc_user_ids" name="cc_user_ids[]" value="<?php echo $user["user_id"];?>"/>
												<span class="icon icon-trash text-danger remove_cc pointer" style="font-size: large; padding-left:5px;" id="cc_remove_<?php echo $user["user_id"];?>" data-id="<?php echo $user["user_id"];?>" ></span>
											</label>
									<?php
											}
										}
									?>
									</div>
								</div>

								<div class="form-group g24-col-sm-24" style="padding-top:10px;">
									<label class="g24-col-sm-8 control-label"></label>
									<div class="g24-col-sm-6">
										<button type="submit" class="btn btn-primary" style="height: auto; width:80%;">บันทึก</button>
									</div>
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<input type="hidden" id="user_login_id" value="<?php echo $_SESSION['USER_ID'];?>"/>
<script>
	$(document).ready(function() {
		$(document).on("change",".file_upload",function() {
			if($(this).attr("data-index") == $("#max_file_index").val()) {
				index = $(this).attr("data-index");
				next_index = parseInt(index)+1;
				$("#max_file_index").val(next_index);
				filename = $(this).val().replace(/.*(\/|\\)/, '');
				$("#filename_"+index).html(filename+`<span class="icon icon-trash text-danger remove_file pointer" style="font-size: large; padding-left:5px;" id="remove_`+index+`" data-id="`+index+`" ></span>`);

				new_upload_row_html = `<div class="form-group g24-col-sm-24" id="file_div_`+next_index+`">
											<label class="g24-col-sm-8 control-label"></label>
											<div class="g24-col-sm-16">
												<label class="fileContainer btn btn-info g24-col-sm-7">
													<span class="icon icon-paperclip"></span> 
													แนบเอกสาร `+next_index+`
													<input id="file_`+next_index+`" data-index="`+next_index+`" name="file[]" class="form-control m-b-1 file_upload" type="file" value="" >
												</label>
												<label id="filename_`+next_index+`" style="padding: 7px;"></label>
											</div>
										</div>`;
				$("#file_upload_div").append(new_upload_row_html);
			} else {
				index = $(this).attr("data-index");
				filename = $(this).val().replace(/.*(\/|\\)/, '');
				$("#filename_"+index).html(filename+`<span class="icon icon-trash text-danger remove_file pointer" style="font-size: large; padding-left:5px;" id="remove_`+index+`" data-id="`+index+`" ></span>`);
			}
		});

		$(document).on("click", ".remove_file",function() {
			index = $(this).attr("data-id");
			$("#file_div_"+index).remove();
		});

		$("#add-reviewer").click(function() {
			if(!$("#user-select").val()) {
				swal('กรุณาเลือกสมาชิก');
			} else {
				user_id = $("#user-select").val();
				data_exist = false;
				$(".user_ids").each(function(index) {
					if($(this).val() == user_id) {
						data_exist = true;
					}
				});
				if(!data_exist) {
					
					name = $("#user-select option:selected").text();
					html = `<label class="g24-col-sm-24 control-label text-left" id="review-label-`+user_id+`">`+name+`
								<input type="hidden" class="user_ids" name="user_ids[]" value="`+user_id+`"/>
								<span class="icon icon-trash text-danger remove_reviewer pointer" style="font-size: large; padding-left:5px;" id="remove_`+user_id+`" data-id="`+user_id+`" ></span>
							</label>`;
					$("#reviewer-div").append(html);
				}
			}
		});
		$(document).on("click", ".remove_reviewer",function() {
			user_id = $(this).attr("data-id");
			$("#review-label-"+user_id).remove();
		});
		$("#add-group-reviewer").click(function() {
			if(!$("#group_id").val()) {
				swal('กรุณาเลือกกลุ่ม');
			} else {
				$.get(base_url+"elec_doc_draft/get_user_group_members?id="+$("#group_id").val(), function(result) {
					data = JSON.parse(result);
					for (i = 0; i < data.length; i++) {
						user_id = data[i].user_id
						name = data[i].user_name
						data_exist = false;
						$(".user_ids").each(function(index) {
							if($(this).val() == user_id) {
								data_exist = true;
							}
						});
						if(!data_exist && user_id != $("#user_login_id").val()) {
							html = `<label class="g24-col-sm-24 control-label text-left" id="review-label-`+user_id+`">`+name+`
										<input type="hidden" class="user_ids" name="user_ids[]" value="`+user_id+`"/>
										<span class="icon icon-trash text-danger remove_reviewer pointer" style="font-size: large; padding-left:5px;" id="remove_`+user_id+`" data-id="`+user_id+`" ></span>
									</label>`;
							$("#reviewer-div").append(html);
						}
					}
				});
			}
		});

		$("#add-approve-draft").click(function() {
			if(!$("#user-approve-draft-select").val()) {
				swal('กรุณาเลือกสมาชิก');
			} else {
				user_id = $("#user-approve-draft-select").val();
				data_exist = false;
				$(".approve_draft_user_ids").each(function(index) {
					if($(this).val() == user_id) {
						data_exist = true;
					}
				});
				if(!data_exist) {
					
					name = $("#user-approve-draft-select option:selected").text();
					html = `<label class="g24-col-sm-24 control-label text-left" id="approve-draft-label-`+user_id+`">`+name+`
								<input type="hidden" class="approve_draft_user_ids" name="approve_draft_user_ids[]" value="`+user_id+`"/>
								<span class="icon icon-trash text-danger remove_approve-draft pointer" style="font-size: large; padding-left:5px;" id="remove_`+user_id+`" data-id="`+user_id+`" ></span>
							</label>`;
					$("#approve-draft-div").append(html);
				}
			}
		});
		$(document).on("click", ".remove_approve-draft",function() {
			user_id = $(this).attr("data-id");
			$("#approve-draft-label-"+user_id).remove();
		});
		$("#add-group-approve-draft").click(function() {
			if(!$("#approve_draft_group_id").val()) {
				swal('กรุณาเลือกกลุ่ม');
			} else {
				$.get(base_url+"elec_doc_draft/get_user_group_members?id="+$("#approve_draft_group_id").val(), function(result) {
					data = JSON.parse(result);
					for (i = 0; i < data.length; i++) {
						user_id = data[i].user_id
						name = data[i].user_name
						data_exist = false;
						$(".approve_draft_user_ids").each(function(index) {
							if($(this).val() == user_id) {
								data_exist = true;
							}
						});
						if(!data_exist && user_id != $("#user_login_id").val()) {
							html = `<label class="g24-col-sm-24 control-label text-left" id="approve-draft-label-`+user_id+`">`+name+`
										<input type="hidden" class="approve_draft_user_ids" name="approve_draft_user_ids[]" value="`+user_id+`"/>
										<span class="icon icon-trash text-danger remove_approve-draft pointer" style="font-size: large; padding-left:5px;" id="approve_draft_remove_`+user_id+`" data-id="`+user_id+`" ></span>
									</label>`;
							$("#approve-draft-div").append(html);
						}
					}
				});
			}
		});

		$("#add-approve").click(function() {
			if(!$("#user-approve-select").val()) {
				swal('กรุณาเลือกสมาชิก');
			} else {
				user_id = $("#user-approve-select").val();
				data_exist = false;
				$(".approve_user_ids").each(function(index) {
					if($(this).val() == user_id) {
						data_exist = true;
					}
				});
				if(!data_exist) {
					
					name = $("#user-approve-select option:selected").text();
					html = `<label class="g24-col-sm-24 control-label text-left" id="approve-label-`+user_id+`">`+name+`
								<input type="hidden" class="approve_user_ids" name="approve_user_ids[]" value="`+user_id+`"/>
								<span class="icon icon-trash text-danger remove_approve pointer" style="font-size: large; padding-left:5px;" id="remove_`+user_id+`" data-id="`+user_id+`" ></span>
							</label>`;
					$("#approve-div").append(html);
				}
			}
		});
		$(document).on("click", ".remove_approve",function() {
			user_id = $(this).attr("data-id");
			$("#approve-label-"+user_id).remove();
		});
		$("#add-group-approve").click(function() {
			if(!$("#approve_group_id").val()) {
				swal('กรุณาเลือกกลุ่ม');
			} else {
				$.get(base_url+"elec_doc_draft/get_user_group_members?id="+$("#approve_group_id").val(), function(result) {
					data = JSON.parse(result);
					for (i = 0; i < data.length; i++) {
						user_id = data[i].user_id
						name = data[i].user_name
						data_exist = false;
						$(".approve_user_ids").each(function(index) {
							if($(this).val() == user_id) {
								data_exist = true;
							}
						});
						if(!data_exist && user_id != $("#user_login_id").val()) {
							html = `<label class="g24-col-sm-24 control-label text-left" id="approve-label-`+user_id+`">`+name+`
										<input type="hidden" class="approve_user_ids" name="approve_user_ids[]" value="`+user_id+`"/>
										<span class="icon icon-trash text-danger remove_approve pointer" style="font-size: large; padding-left:5px;" id="approve_remove_`+user_id+`" data-id="`+user_id+`" ></span>
									</label>`;
							$("#approve-div").append(html);
						}
					}
				});
			}
		});

		$("#add-receive").click(function() {
			if(!$("#user-receive-select").val()) {
				swal('กรุณาเลือกสมาชิก');
			} else {
				user_id = $("#user-receive-select").val();
				data_exist = false;
				$(".receive_user_ids").each(function(index) {
					if($(this).val() == user_id) {
						data_exist = true;
					}
				});
				if(!data_exist) {
					name = $("#user-receive-select option:selected").text();
					html = `<label class="g24-col-sm-24 control-label text-left" id="receive-label-`+user_id+`">`+name+`
								<input type="hidden" class="receive_user_ids" name="receive_user_ids[]" value="`+user_id+`"/>
								<span class="icon icon-trash text-danger remove_receive pointer" style="font-size: large; padding-left:5px;" id="remove_`+user_id+`" data-id="`+user_id+`" ></span>
							</label>`;
					$("#receive-div").append(html);
				}
			}
		});
		$(document).on("click", ".remove_receive",function() {
			user_id = $(this).attr("data-id");
			$("#receive-label-"+user_id).remove();
		});
		$("#add-group-receive").click(function() {
			if(!$("#receive_group_id").val()) {
				swal('กรุณาเลือกกลุ่ม');
			} else {
				$.get(base_url+"elec_doc_draft/get_user_group_members?id="+$("#receive_group_id").val(), function(result) {
					data = JSON.parse(result);
					for (i = 0; i < data.length; i++) {
						user_id = data[i].user_id
						name = data[i].user_name
						data_exist = false;
						$(".receive_user_ids").each(function(index) {
							if($(this).val() == user_id) {
								data_exist = true;
							}
						});
						if(!data_exist && user_id != $("#user_login_id").val()) {
							html = `<label class="g24-col-sm-24 control-label text-left" id="receive-label-`+user_id+`">`+name+`
										<input type="hidden" class="receive_user_ids" name="receive_user_ids[]" value="`+user_id+`"/>
										<span class="icon icon-trash text-danger remove_receive pointer" style="font-size: large; padding-left:5px;" id="receive_remove_`+user_id+`" data-id="`+user_id+`" ></span>
									</label>`;
							$("#receive-div").append(html);
						}
					}
				});
			}
		});

		$("#add-cc").click(function() {
			if(!$("#user-cc-select").val()) {
				swal('กรุณาเลือกสมาชิก');
			} else {
				user_id = $("#user-cc-select").val();
				data_exist = false;
				$(".cc_user_ids").each(function(index) {
					if($(this).val() == user_id) {
						data_exist = true;
					}
				});
				if(!data_exist) {
					name = $("#user-cc-select option:selected").text();
					html = `<label class="g24-col-sm-24 control-label text-left" id="cc-label-`+user_id+`">`+name+`
								<input type="hidden" class="cc_user_ids" name="cc_user_ids[]" value="`+user_id+`"/>
								<span class="icon icon-trash text-danger remove_cc pointer" style="font-size: large; padding-left:5px;" id="remove_`+user_id+`" data-id="`+user_id+`" ></span>
							</label>`;
					$("#cc-div").append(html);
				}
			}
		});
		$(document).on("click", ".cc",function() {
			user_id = $(this).attr("data-id");
			$("#c-label-"+user_id).remove();
		});
		$("#add-group-cc").click(function() {
			if(!$("#cc_group_id").val()) {
				swal('กรุณาเลือกกลุ่ม');
			} else {
				$.get(base_url+"elec_doc_draft/get_user_group_members?id="+$("#cc_group_id").val(), function(result) {
					data = JSON.parse(result);
					for (i = 0; i < data.length; i++) {
						user_id = data[i].user_id
						name = data[i].user_name
						data_exist = false;
						$(".cc_user_ids").each(function(index) {
							if($(this).val() == user_id) {
								data_exist = true;
							}
						});
						if(!data_exist && user_id != $("#user_login_id").val()) {
							html = `<label class="g24-col-sm-24 control-label text-left" id="cc-label-`+user_id+`">`+name+`
										<input type="hidden" class="cc_user_ids" name="cc_user_ids[]" value="`+user_id+`"/>
										<span class="icon icon-trash text-danger remove_cc pointer" style="font-size: large; padding-left:5px;" id="cc_remove_`+user_id+`" data-id="`+user_id+`" ></span>
									</label>`;
							$("#cc-div").append(html);
						}
					}
				});
			}
		});
	});
</script>
