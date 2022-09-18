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
		</style>
		<h1 style="margin-bottom: 0">เอกสาร</h1>
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 padding-l-r-0">
			<div class="col-xs-6 col-sm-6 col-md-6 col-lg-7 padding-l-r-0">
				<?php $this->load->view('breadcrumb'); ?>
			</div>
		</div>
		<div class="row gutter-xs">
			<div class="col-xs-12 col-md-12">
				<div class="panel panel-body" style="padding-top:0px !important;">
					<div class="bs-example" data-example-id="striped-table">
						<input type="hidden" name="id" value="<?php echo $_GET["id"]?>"/>
						<input type="hidden" id="comment_id" name="comment_id" value=""/>
						<br/>
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
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<script>
	$(document).ready(function() {

	});
</script>