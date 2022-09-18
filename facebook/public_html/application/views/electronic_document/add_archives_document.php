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
			.pointer {
				cursor: pointer;
			}
		</style>
		<h1 style="margin-bottom: 0">คลังเอกสาร</h1>
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 padding-l-r-0">
			<div class="col-xs-6 col-sm-6 col-md-6 col-lg-7 padding-l-r-0">
				<?php $this->load->view('breadcrumb'); ?>
			</div>
		</div>
		<div class="row gutter-xs">
			<div class="col-xs-12 col-md-12">
				<div class="panel panel-body" style="padding-top:0px !important;">
					<div class="bs-example" data-example-id="striped-table">
						<form id='form_add' data-toggle="validator" novalidate="novalidate" action="<?php echo base_url(PROJECTPATH.'/elec_doc_archives/add_archives_document'); ?>" method="post"  enctype="multipart/form-data">
							<input type="hidden" name="document_id" value="<?php echo $_GET["id"]?>"/>
							<br/>
							<div class="col-xs-12 col-md-12"><h2 style="margin-bottom: 0">เพิ่มเอกสาร</h2></div>
							<div class="col-xs-1 col-md-1"></div>
							<div id="tb_wrap" class="col-xs-10 col-md-10">
								<br/>
								<br/>
								<div class="form-group g24-col-sm-24">
									<label class="g24-col-sm-8 control-label">หมวดเอกสาร</label>
									<div class="g24-col-sm-6">
										<div class="form-group">
											<select id="category_id" name="category_id" class="form-control">
												<option value="" >กรุณาเลือกหมวดเอกสาร</option>
												<?php foreach($categories as $category){ ?>
													<option value="<?php echo $category["id"]; ?>" <?php echo !empty($document) && $document->category_id == $category["id"] ? "selected" : "";?>><?php echo $category["name"]; ?></option>
												<?php } ?>
											</select>
										</div>
									</div>
								</div>
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
								<div class="form-group g24-col-sm-24" style="padding-top: 20px;">
									<label class="g24-col-sm-8 control-label"></label>
									<div class="g24-col-sm-6">
									<button type="submit" class="btn btn-primary" style="height: auto; width:80%;">บันทึก</button>
									</div>
								</div>
							</div>
							<div class="col-xs-1 col-md-1"></div>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<script>
	$(document).ready(function() {
		$(document).on("change",".file_upload",function() {
			if($(this).attr("data-index") == $("#max_file_index").val()) {
				index = $(this).attr("data-index");
				next_index = parseInt(index)+1;
				$("#max_file_index").val(next_index);
				filename = $(this).val().replace(/.*(\/|\\)/, '');
				$("#filename_"+index).html(filename+`<span class="icon icon-trash text-danger pointer remove_file" style="font-size: large; padding-left:5px;" id="remove_`+index+`" data-id="`+index+`" ></span>`);

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
				$("#filename_"+index).html(filename+`<span class="icon icon-trash text-danger pointer remove_file" style="font-size: large; padding-left:5px;" id="remove_`+index+`" data-id="`+index+`" ></span>`);
			}
		});

		$(document).on("click", ".remove_file",function() {
			index = $(this).attr("data-id");
			$("#file_div_"+index).remove()
		});
	});
</script>
