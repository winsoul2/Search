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
			.blink {
				color : #000;
				animation: blink-animation 1s steps(5, start) infinite;
				-webkit-animation: blink-animation 1s steps(5, start) infinite;
			}
			@keyframes blink-animation {
				to {
					visibility: hidden;
				}
			}
			@-webkit-keyframes blink-animation {
				to {
					visibility: hidden;
				}
			}
		</style>
		<h1 style="margin-bottom: 0">ร่างเอกสาร</h1>
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 padding-l-r-0">
			<div class="col-xs-6 col-sm-6 col-md-6 col-lg-7 padding-l-r-0">
				<?php $this->load->view('breadcrumb'); ?>
			</div>
			<div class="col-xs-6 col-sm-6 col-md-6 col-lg-5 padding-l-r-0">
                <div class="col-sm-3 btn-col"></div>
                <div class="col-sm-3 btn-col"></div>
                <div class="col-sm-2 btn-col"></div>
                <div class="col-sm-4 btn-col">
                    <a class="link-line-none" id="btn-add-document" href="<?php echo base_url(PROJECTPATH.'/elec_doc_draft/add_draft_document');?>">
                        <button class="btn btn-primary btn-lg bt-add" style="width:100% !important;" type="button">
                            เพิ่มร่างเอกสาร
                        </button>
                    </a>
                </div>
			</div>
		</div>
		<div class="row gutter-xs">
			<div class="col-xs-12 col-md-12">
				<div class="panel panel-body" style="padding-top:0px !important;">
					<div class="bs-example" data-example-id="striped-table">
						<div id="tb_wrap" class="col-xs-12 col-md-12">
							<br/>
							<br/>
							<br/>
							<table class="table table-bordered table-striped table-center">
								<thead> 
									<tr class="bg-primary">
										<th class="text-center" width="5%">ลำดับ</th>
										<th class="text-center" width="15%"> วันที่แก้ไขร่างเอกสาร </th>
										<th class="text-center"> ชื่อเอกสาร </th>
										<th class="text-center" width="12%"> ผู้ร่างเอกสาร </th>
										<th class="text-center" width="12%"> สถานะ </th>
                                        <th class="text-center" width="20%"></th>
									</tr> 
								</thead>
								<tbody>
								<?php
									$status_text = array('2'=>'รอตรวจสอบ', '3'=>'ตรวจสอบแล้ว', '4'=>'รออนุมัติ', '7'=>'รออนุมัติร่าง');
									foreach($datas as $data) {
								?>
									<tr>
										<td class="text-center"><?php echo $runno++;?></td>
										<td class="text-center"><?php echo $this->center_function->ConvertToThaiDate($data["updated_at"]);?></td>
										<td class="text-left">
											<!-- <?php echo $data["name"];?> -->
											<a class="link-line-none btn-comment-document" href="<?php echo base_url(PROJECTPATH.'/elec_doc_draft/review_draft_document?id='.$data["id"]);?>" id="btn-f-comment-<?php echo $data["id"];?>"> <?php echo $data["name"];?> </a>
										</td>
										<td class="text-left"><?php echo $data["user_name"];?></td>
										<td class="text-center"><?php echo $status_text[$data["status"]];?></td>
										<td>
											<?php
												if($data["user_id"] == $_SESSION['USER_ID']) {
											?>
											<a class="link-line-none btn-edit-document" href="<?php echo base_url(PROJECTPATH.'/elec_doc_draft/add_draft_document?id='.$data["id"]);?>" id="btn-edit-<?php echo $data["id"];?>"> แก้ไข </a>
											|
											<?php
												}
											?>
											<a class="link-line-none btn-delete-document" data-id="<?php echo $data["id"];?>" id="btn-delete-<?php echo $data["id"];?>" style="cursor: pointer;"> ลบ </a>
											|
											<a class="link-line-none btn-comment-document" href="<?php echo base_url(PROJECTPATH.'/elec_doc_draft/review_draft_document?id='.$data["id"]);?>"  data-id="<?php echo $data["id"];?>" id="btn-comment-<?php echo $data["id"];?>"> ความเห็น(<?php echo $data["comment_count"];?>) </a>
											<?php
												if($data["status"] == 2 && $data["user_id"] == $_SESSION['USER_ID']) {
											?>
											|
											<button class="btn btn-warning btn-send-approve-draft" style="padding:0px 2px; width:unset;height:unset;" type="button" data-id="<?php echo $data["id"];?>" id="btn-approve-draft-<?php echo $data["id"];?>">ส่งอนุมัติ</button>
											<?php
												} else if ($data["status"] == 7 && !empty($data["approve_draft"])) {
											?>
											|
											<button class="btn btn-primary btn-approve-draft" style="padding:0px 2px; width:unset;height:unset;" type="button" data-id="<?php echo $data["id"];?>" id="btn-approve-draft-<?php echo $data["id"];?>">อนุมัติ</button>
											
											<button class="btn btn-danger btn-un-approve-draft" style="padding:0px 2px; width:unset;height:unset;" type="button" data-id="<?php echo $data["id"];?>" id="btn-approve-draft-<?php echo $data["id"];?>">ไม่อนุมัติ</button>
											<?php
												}
											?>
											<?php
												if(empty($data["approve_draft"]) && !empty($data["has_unread"])) {
											?>
											<img width="25px" class="" src="<?php echo base_url(PROJECTPATH.'/assets/images/new_blink.gif') ?>" style="margin:auto;<?php echo !empty($data["has_unread"]) ? "" : "visibility: hidden;";?>" />
											<?php
												}
											?>
										</td>
									</tr>
								<?php
									}
								?>
								</tbody>
							</table>
						</div>
					</div>
					<div class="col-xs-12 col-md-12 text-center">
						<?php echo @$paging ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<form id='form_delete' data-toggle="validator" novalidate="novalidate" action="<?php echo base_url(PROJECTPATH.'/elec_doc_draft'); ?>" method="post">
	<input type="hidden" class="form-control" name="action" value="delete">
	<input type="hidden" class="form-control" name="id" id="delete-id-modal" value="">
</form>
<form id='form_change_status' data-toggle="validator" novalidate="novalidate" action="<?php echo base_url(PROJECTPATH.'/elec_doc_draft'); ?>" method="post">
	<input type="hidden" class="form-control" name="action" value="change_status">
	<input type="hidden" class="form-control" name="id" id="id-modal" value="">
	<input type="hidden" class="form-control" name="status" id="status-modal" value="">
</form>
<script>
	$(document).ready(function() {
        $(".btn-delete-document").click(function() {
			id = $(this).attr("data-id");
			swal({
				title: "ท่านต้องการลบเอกสารใช่หรือไม่?",
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
		$(".btn-approve-draft").click(function() {
			id = $(this).attr("data-id");
			swal({
				title: "ท่านต้องการอนุมัติร่างเอกสารใช่หรือไม่?",
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
					$("#id-modal").val(id);
					$("#status-modal").val(4);
					$("#form_change_status").submit();
				}
			});
		});
		$(".btn-approve").click(function() {
			id = $(this).attr("data-id");
			swal({
				title: "ท่านต้องการอนุมัติเอกสารใช่หรือไม่?",
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
					$("#id-modal").val(id);
					$("#status-modal").val(5);
					$("#form_change_status").submit();
				}
			});
		});
		$(".btn-send-approve-draft").click(function() {
			id = $(this).attr("data-id");
			swal({
				title: "ท่านต้องการส่งอนุมัติเอกสารใช่หรือไม่?",
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
					$("#id-modal").val(id);
					$("#status-modal").val(7);
					$("#form_change_status").submit();
				}
			});
		});
		$(".btn-un-approve-draft").click(function() {
			id = $(this).attr("data-id");
			swal({
				title: "ท่านต้องการไม่อนุมัติร่างเอกสารใช่หรือไม่?",
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
					$("#id-modal").val(id);
					$("#status-modal").val(2);
					$("#form_change_status").submit();
				}
			});
		});
	});
</script>
