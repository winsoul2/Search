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
			.top-menu {
				padding-left:5px;
				padding-right:5px;
			}
		</style>
		<h1 style="margin-bottom: 0">รออนุมัติ</h1>
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 padding-l-r-0">
			<div class="col-xs-6 col-sm-6 col-md-6 col-lg-7 padding-l-r-0">
				<?php $this->load->view('breadcrumb'); ?>
			</div>
			<div class="col-xs-6 col-sm-6 col-md-6 col-lg-5 padding-l-r-0">
                <div class="col-md-9 btn-col"></div>
				<div class="col-sm-3 btn-col top-menu">
                    <a class="link-line-none" id="btn-del-list-document">
                        <button class="btn btn-primary btn-lg bt-add" style="width:100% !important;" type="button">
                            ลบเอกสาร
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
										<th class="text-center" width="5%">
											<input type="checkbox" class="del-check-all" id="del-check-all" value="">
										</th>
										<th class="text-center" width="5%">ลำดับ</th>
										<th class="text-center" width="15%"> วันที่แก้ไขร่างเอกสาร </th>
										<th class="text-center"> ชื่อเอกสาร </th>
										<th class="text-center" width="12%"> ผู้ร่างเอกสาร </th>
										<th class="text-center" width="12%"> สถานะ </th>
                                        <th class="text-center" width="15%"></th>
									</tr> 
								</thead>
								<tbody>
								<form data-toggle="validator" method="post" action="" class="g24 form form-horizontal" enctype="multipart/form-data" autocomplete="off" id="from_1">
									<input type="hidden" class="form-control" name="action" value="delete_list">
								<?php
									$status_text = array('2'=>'รอตรวจสอบ', '5'=>'อนุมัติ', '4'=>'รออนุมัติ', '6'=>'ไม่อนุมัติ');
									foreach($datas as $data) {
								?>
									<tr>
										<td class="text-center">
											<input type="checkbox" class="del-check" id="del-check-<?php echo $data['id']; ?>" name="ids[]" value="<?php echo $data['id']; ?>">
										</td>
										<td class="text-center"><?php echo $runno++;?></td>
										<td class="text-center"><?php echo $this->center_function->ConvertToThaiDate($data["updated_at"]);?></td>
										<td class="text-left">
											<a class="link-line-none btn-comment-document" href="<?php echo base_url(PROJECTPATH.'/elec_doc_approve/review_approve_document?id='.$data["id"]);?>" id="btn-f-comment-<?php echo $data["id"];?>"> <?php echo $data["name"];?> </a>
										</td>
										<td class="text-left"><?php echo $data["user_name"];?></td>
										<td class="text-center"><?php echo $status_text[$data["status"]];?></td>
										<td>
											<?php
												if($data["status"] == 4) {
											?>
											<button class="btn btn-primary btn-approve" style="padding:0px 4px; width:unset;height:unset;" type="button" data-id="<?php echo $data["id"];?>" id="btn-approve-<?php echo $data["id"];?>">อนุมัติ</button>
											<button class="btn btn-danger btn-unapprove" style="padding:0px 4px; width:unset;height:unset;" type="button" data-id="<?php echo $data["id"];?>" id="btn-unapprove-<?php echo $data["id"];?>">ไม่อนุมัติ</button>
											<?php
												}
											?>
										</td>
									</tr>
								<?php
									}
								?>
								</form>
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
<form id='form_change_status' data-toggle="validator" novalidate="novalidate" action="<?php echo base_url(PROJECTPATH.'/elec_doc_approve'); ?>" method="post">
	<input type="hidden" class="form-control" name="action" value="change_status">
	<input type="hidden" class="form-control" name="id" id="id-modal" value="">
	<input type="hidden" class="form-control" name="status" id="status-modal" value="">
</form>
<script>
	$(document).ready(function() {
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
		$(".btn-unapprove").click(function() {
			id = $(this).attr("data-id");
			swal({
				title: "ท่านไม่อนุมัติเอกสารใช่หรือไม่?",
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
					$("#status-modal").val(6);
					$("#form_change_status").submit();
				}
			});
		});
		$("#btn-del-list-document").click(function() {
			swal({
				title: "ท่านต้องการลบเอกสารที่เลือกใช่หรือไม่?",
				text: "",
				type: "warning",
				showCancelButton: true,
				confirmButtonColor: '#DD6B55',
				confirmButtonText: 'ยืนยัน',
				cancelButtonText: "ยกเลิก",
				closeOnConfirm: false,
				closeOnCancel: true,
				customId:"cs",
				customClass:"sc"
			},
			function(isConfirm) {
				if (isConfirm) {
					$("#from_1").submit();
				}
			});
		});
		$("#del-check-all").change(function() {
			if($(this).is(':checked')){
				$('.del-check').prop('checked','checked');
			}else{
				$('.del-check').prop('checked','');
			}
		});
		$(".del-check").change(function() {
			if(!$(this).is(':checked')){
				$('#del-check-all').prop('checked','');
			}
		});
	});
</script>
