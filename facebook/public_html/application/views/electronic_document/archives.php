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
			.top-menu {
				padding-left:5px;
				padding-right:5px;
			}
		</style>
		<h1 style="margin-bottom: 0">คลังเอกสาร</h1>
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 padding-l-r-0">
			<div class="col-xs-6 col-sm-6 col-md-6 col-lg-7 padding-l-r-0">
				<?php $this->load->view('breadcrumb'); ?>
			</div>
			<div class="col-xs-6 col-sm-6 col-md-6 col-lg-5 padding-l-r-0">
                <div class="col-sm-3 btn-col"></div>
                <div class="col-sm-3 btn-col"></div>
                <div class="col-sm-3 btn-col top-menu">
                    <a class="link-line-none" id="btn-add-document" href="<?php echo base_url(PROJECTPATH.'/elec_doc_archives/add_archives_document');?>">
                        <button class="btn btn-primary btn-lg bt-add" style="width:100% !important;" type="button">
                            เพิ่มเอกสาร
                        </button>
                    </a>
                </div>
                <div class="col-sm-3 btn-col top-menu">
                    <a class="link-line-none" id="btn-add-category" href="<?php echo base_url(PROJECTPATH.'/elec_doc_archives/category');?>">
                        <button class="btn btn-primary btn-lg bt-add" style="width:100% !important;" type="button">
                            หมวดเอกสาร
                        </button>
                    </a>
                </div>
			</div>
		</div>
		<div class="row gutter-xs">
			<div class="col-xs-12 col-md-12">
				<div class="panel panel-body" style="padding-top:0px !important;">
					<div class="bs-example" data-example-id="striped-table">
						<!-- <div class="col-xs-1 col-md-1"></div> -->
						<div id="tb_wrap" class="col-xs-12 col-md-12">
							<br/>
							<br/>
							<br/>
							<table class="table table-bordered table-striped table-center">
								<thead> 
									<tr class="bg-primary">
										<th class="text-center" width="5%">ลำดับ</th>
										<th class="text-center" width="12.5%"> หมวดเอกสาร </th>
										<th class="text-center" width="30%"> ชื่อเอกสาร </th>
										<th class="text-center" width="22.5%"> ดาวน์โหลด </th>
										<th class="text-center" width="20%"> แก้ไขล่าสุด </th>
                                        <th class="text-center" width="10%"></th>
									</tr> 
								</thead>
								<tbody>
								<?php
									foreach($datas as $data) {
								?>
									<tr>
										<td class="text-center"><?php echo $runno++;?></td>
										<td class="text-left"><?php echo $data["category_name"];?></td>
										<td class="text-left"><?php echo $data["name"];?></td>
										<td>
											<?php
												foreach($data["files"] as $key => $file) {
													if($key > 0) {
														echo ", ";
													}
											?>
											<a href="<?php echo base_url(PROJECTPATH."/elec_doc_archives/download_file?id=".$file["id"]);?>"><span><?php echo $file["name"];?></span></a>
											<?php
												}
											?>
										</td>
										<td class="text-center"><?php echo $this->center_function->ConvertToThaiDate($data["updated_at"])?></td>
										<td>
											<a class="link-line-none btn-edit-document" href="<?php echo base_url(PROJECTPATH.'/elec_doc_archives/add_archives_document?id='.$data["id"]);?>" id="btn-edit-<?php echo $data["id"];?>"> แก้ไข </a>
											|
											<a class="link-line-none btn-delete-document" style="cursor: pointer;" data-id="<?php echo $data["id"];?>" id="btn-delete-<?php echo $data["id"];?>"> ลบ </a>
										</td>
									</tr>
								<?php
									}
								?>
								</tbody>
							</table>
						</div>
						<!-- <div class="col-xs-1 col-md-1"></div> -->
					</div>
					<div class="col-xs-12 col-md-12 text-center">
						<?php echo @$paging ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<form id='form_delete' data-toggle="validator" novalidate="novalidate" action="<?php echo base_url(PROJECTPATH.'/elec_doc_archives'); ?>" method="post">
	<input type="hidden" class="form-control" name="action" value="delete">
	<input type="hidden" class="form-control" name="id" id="delete-id-modal" value="">
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
	});
</script>
