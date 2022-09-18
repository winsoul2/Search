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
			.comment-edit-div {
				display:flex;
				padding-left: 0px;
			}
			.comment-edit-div-pre {
				background-color:#f7a61a;
				color:#FFF;
				margin-right: 7px;
			}
			.comment-edit-div-sub {
				padding-left: 0px;
				padding-right: 0px;
			}
			.comment-div {
				display:flex;
				padding-left: 0px;
			}
			.comment-div-pre {
				background-color:#467542;
				color:#FFF;
				margin-right: 7px;
			}
			.comment-div-sub {
				background-color:#e0e0e0;
				color:#757575;
				display: grid;
			}
			.pointer {
				cursor: pointer;
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
						<form id='form_add' data-toggle="validator" novalidate="novalidate" action="<?php echo base_url(PROJECTPATH.'/elec_doc_draft/review_draft_document'); ?>" method="post"  enctype="multipart/form-data">
							<input type="hidden" name="id" value="<?php echo $_GET["id"]?>"/>
							<input type="hidden" id="comment_id" name="comment_id" value=""/>
							<br/>
							<div class="col-xs-12 col-md-12"><h2 style="margin-bottom: 0">ความคิดเห็น</h2></div>
							<div class="col-xs-1 col-md-1"></div>
							<div id="tb_wrap" class="col-xs-10 col-md-10">
								<br/>
								<br/>
								<div class="form-group g24-col-sm-24">
									<label class="g24-col-sm-3 control-label">ชื่อเอกสาร</label>
									<label class="g24-col-sm-8 control-label text-left"><?php echo $document->name;?></label>
								</div>
								<div class="form-group g24-col-sm-24">
									<label class="g24-col-sm-3 control-label">เอกสารแนบ</label>
									<label class="g24-col-sm-21 control-label text-left">
									<?php
										foreach($files as $key => $file) {
											if($key > 0) {
												echo ", ";
											}
									?>
									<a href="<?php echo base_url(PROJECTPATH."/elec_doc_draft/download_file?id=".$file["id"]);?>"><span><?php echo $file["name"];?></span></a>
									<?php
										}
									?>
									</label>
								</div>
								<div class="form-group g24-col-sm-24">
									<label class="g24-col-sm-3 control-label">ส่งตรวจทาน</label>
									<label class="g24-col-sm-21 control-label text-left">
										<?php
											$max_index = count($review_users);
											foreach($review_users as $key => $user) {
												echo $user["user_name"];
												echo $key + 1 < $max_index ? ", " : "";
											}
										?>
									</label>
								</div>
								<div class="form-group g24-col-sm-24">
									<label class="g24-col-sm-3 control-label">ผู้อนุมัติร่างเอกสาร</label>
									<label class="g24-col-sm-21 control-label text-left">
										<?php
											$max_index = count($approve_draft_users);
											foreach($approve_draft_users as $key => $user) {
												echo $user["user_name"];
												echo $key + 1 < $max_index ? ", " : "";
											}
										?>
									</label>
								</div>
								<div class="form-group g24-col-sm-24">
									<label class="g24-col-sm-3 control-label">ผู้รับเอกสาร</label>
									<label class="g24-col-sm-21 control-label text-left">
										<?php
											$max_index = count($receiver_users);
											foreach($receiver_users as $key => $user) {
												echo $user["user_name"];
												echo $key + 1 < $max_index ? ", " : "";
											}
										?>
									</label>
								</div>
								<div class="form-group g24-col-sm-24">
									<label class="g24-col-sm-3 control-label">ผู้อนุมัติ</label>
									<label class="g24-col-sm-21 control-label text-left">
										<?php
											$max_index = count($approver_users);
											foreach($approver_users as $key => $user) {
												echo $user["user_name"];
												echo $key + 1 < $max_index ? ", " : "";
											}
										?>
									</label>
								</div>
								<?php
									foreach($comments as $key => $comment) {
								?>
									<div class="form-group g24-col-sm-24 comment-div">
										<input type="hidden" id="comment-<?php echo $comment["id"];?>" value="<?php echo $comment['comment'];?>"/>
										<div class="form-group g24-col-sm-6 comment-div-pre">
											<label class="g24-col-sm-24 control-label" id="label-comment-<?php echo $comment["id"];?>">
												ความคิดเห็นที่ <?php echo $key+1;?><br>
												<?php echo $comment["user_name"];?><br>
												<?php echo $this->center_function->ConvertToThaiDate($comment["updated_at"])?>
											</label>
											<?php if($comment["user_id"] == $_SESSION['USER_ID']) {?>
											<label class="g24-col-sm-24 control-label">
												<a class="link-line-none btn-edit-document pointer" data-id="<?php echo $comment["id"];?>" id="btn-edit-<?php echo $comment["id"];?>" style="color:#ffe040;"> แก้ไข </a>
												|
												<a class="link-line-none btn-delete-document pointer" data-id="<?php echo $comment["id"];?>" id="btn-delete-<?php echo $comment["id"];?>" style="color:#ffe040;"> ลบ </a>
											</label>
											<?php } ?>
										</div>
										<div class="form-group g24-col-sm-18 comment-div-sub bg-light">
											<div class="form-group g24-col-sm-24"><?php echo $comment['comment'];?></div>
											<?php
												if(!empty($comment["files"])) {
											?>
											<div class="form-group g24-col-sm-24" style="align-self: flex-end;">
											<?php
												foreach($comment["files"] as $key => $file) {
													if($key > 0) {
														echo ", ";
													}
											?>
											<a class="comment-file-a-<?php echo $comment["id"];?>" data-id="<?php echo $file["id"];?>" data-name="<?php echo $file["name"];?>"  href="<?php echo base_url(PROJECTPATH."/elec_doc_draft/download_file?id=".$file["id"]);?>"><span><?php echo $file["name"];?></span></a>
											<?php
												}
											?>
											</div>
											<?php
												}
											?>
										</div>
									</div>
								<?php
									}
								?>
								<div class="form-group g24-col-sm-24 comment-edit-div">
									<div class="form-group g24-col-sm-6 comment-edit-div-pre">
										<label for="comment" class="g24-col-sm-24 control-label" id="label-comment">ความคิดเห็น</label>
									</div>
									<div class="form-group g24-col-sm-18 comment-edit-div-sub">
										<textarea id="comment" name="comment" ><?php echo @$row['comment']; ?></textarea>
									</div>
								</div>
								<div id="file_upload_div">
									<div class="form-group g24-col-sm-24" id="file_div_1" style="margin-top: 10px;">
										<div class="form-group g24-col-sm-6"></div>
										<div class="g24-col-sm-18">
											<label class="fileContainer btn btn-info">
												<span class="icon icon-paperclip"></span> 
												แนบเอกสาร 1
												<input id="file_1" data-index="1" name="file[]" class="form-control m-b-1 file_upload" type="file" value="" >
											</label>
											<label id="filename_1" style="padding: 7px;"></label>
										</div>
									</div>
								</div>
							</div>
							<div class="form-group g24-col-sm-24 text-center" style="margin-top: 10px;">
								<button type="submit" class="btn btn-primary" style="height: auto;">บันทึก</button>
							</div>
							<div class="col-xs-1 col-md-1"></div>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<input type="hidden" id="max_file_index" value="1"/>
<?php
	$link = array(
		'src' => PROJECTJSPATH.'assets/ckeditor/ckeditor.js',
		'type' => 'text/javascript'
	);
	echo script_tag($link);

	$link = array(
		'src' => PROJECTJSPATH.'assets/ckeditor/adapters/jquery.js',
		'type' => 'text/javascript'
	);
	echo script_tag($link);
?>
<form id='form_delete' data-toggle="validator" novalidate="novalidate" action="<?php echo base_url(PROJECTPATH.'/elec_doc_draft/review_draft_document'); ?>" method="post">
	<input type="hidden"  name="action" value="delete">
	<input type="hidden" name="id" value="<?php echo $_GET["id"]?>"/>
	<input type="hidden" id="delete-id-modal" name="comment_id" value=""/>
</form>
<script>
	$(document).ready(function() {
		if($("#comment").length) {
			$("#comment").ckeditor({ height : 146 , customConfig : '<?php echo PROJECTPATH; ?>/assets/ckeditor/config-admin.js'   });
		}
		$(document).on("change",".file_upload",function() {
			if($(this).attr("data-index") == $("#max_file_index").val()) {
				index = $(this).attr("data-index");
				next_index = parseInt(index)+1;
				$("#max_file_index").val(next_index);
				filename = $(this).val().replace(/.*(\/|\\)/, '');
				$("#filename_"+index).html(filename+`<span class="icon icon-trash text-danger remove_file pointer" style="font-size: large; padding-left:5px;" id="remove_`+index+`" data-id="`+index+`" ></span>`);

				new_upload_row_html = `<div class="form-group g24-col-sm-24" id="file_div_`+next_index+`">
											<div class="form-group g24-col-sm-6"></div>
											<div class="g24-col-sm-16">
												<label class="fileContainer btn btn-info">
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

		$(document).on("click", ".btn-edit-document", function() {
			comment_id = $(this).attr("data-id");
			$("#comment_id").val(comment_id);
			$("#label-comment").html($("#label-comment-"+comment_id).html());
			$("#comment").val($("#comment-"+comment_id).val());
			$("#file_upload_div").html("");
			$has_files = false;
			point_index = 1;
			$(".comment-file-a-"+comment_id).each(function(index) {
				$has_files = true;
				id = $(this).attr("data-id");
				name = $(this).attr("data-name");
				html = `<div class="form-group g24-col-sm-24" id="file_div_`+point_index+`">
							<div class="form-group g24-col-sm-6"></div>
							<div class="g24-col-sm-16">
								<input id="file_`+point_index+`" data-index="`+point_index+`" name="file_ids[]" class="form-control m-b-`+point_index+` file_upload" type="hidden" value="`+id+`" >
								<label id="filename_`+point_index+`" style="padding: 7px;">`+name+`<span class="icon icon-trash pointer text-danger remove_file" style="font-size: large; padding-left:5px;" id="remove_`+point_index+`" data-id="`+point_index+`" ></span></label>
							</div>
						</div>`
				$("#file_upload_div").append(html);
				point_index += 1;
			});

			new_upload_row_html = `<div class="form-group g24-col-sm-24" id="file_div_`+point_index+`">
										<div class="form-group g24-col-sm-6"></div>
										<div class="g24-col-sm-16">
											<label class="fileContainer btn btn-info">
												<span class="icon icon-paperclip"></span> 
												แนบเอกสาร `+point_index+`
												<input id="file_`+point_index+`" data-index="`+point_index+`" name="file[]" class="form-control m-b-1 file_upload" type="file" value="" >
											</label>
											<label id="filename_`+point_index+`" style="padding: 7px;"></label>
										</div>
									</div>`;
			$("#file_upload_div").append(new_upload_row_html);
		})

		$(document).on("click", ".btn-delete-document", function() {
			id = $(this).attr("data-id");
			swal({
				title: "ท่านต้องการลบความคิดเห็นใช่หรือไม่?",
				text: "",
				type: "warning",
				showCancelButton: true,
				confirmButtonColor: '#DD6B55',
				confirmButtonText: 'ยืนยัน',
				cancelButtonText: "ยกเลิก",
				closeOnConfirm: false,
				closeOnCancel: true
			},
			function(isConfirm) {
				if (isConfirm) {
					$("#delete-id-modal").val(id);
					$("#form_delete").submit();
				}
			});
		});
	});
</script>