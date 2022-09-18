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
		</style>
		<h1 style="margin-bottom: 0">กลุ่มผู้รับ</h1>
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 padding-l-r-0">
			<div class="col-xs-6 col-sm-6 col-md-6 col-lg-7 padding-l-r-0">
				<?php $this->load->view('breadcrumb'); ?>
			</div>
			<div class="col-xs-6 col-sm-6 col-md-6 col-lg-5 padding-l-r-0">
                <div class="col-sm-3 btn-col"></div>
                <div class="col-sm-3 btn-col"></div>
                <div class="col-sm-3 btn-col"></div>
                <div class="col-sm-3 btn-col">
                    <a class="link-line-none" id="btn-add-group">
                        <button class="btn btn-primary btn-lg bt-add" style="width:100% !important;" type="button">
                            เพิ่มกลุ่มผู้รับ
                        </button>
                    </a>
                </div>
			</div>
		</div>
		<div class="row gutter-xs">
			<div class="col-xs-12 col-md-12">
				<div class="panel panel-body" style="padding-top:0px !important;">
					<div class="bs-example" data-example-id="striped-table">
						<div class="col-xs-1 col-md-1"></div>
						<div id="tb_wrap" class="col-xs-10 col-md-10">
							<br/>
							<br/>
							<br/>
							<table class="table table-bordered table-striped table-center">
								<thead> 
									<tr class="bg-primary">
										<th class="text-center" width="5%">ลำดับ</th>
										<th class="text-center"> ชื่อกลุ่ม </th>
										<th class="text-center" width="10%"> จำนวน </th>
                                        <th class="text-center" width="20%"> จัดการ </th>
									</tr> 
								</thead>
								<tbody>
								<?php
									foreach($datas as $data) {
								?>
									<tr>
										<td class="text-center"><?php echo $runno++;?></td>
										<td class="text-left"><?php echo $data["group_name"];?></td>
										<td class="text-center"><?php echo $data["count_member"];?></td>
										<td>
											<a class="link-line-none btn-edit-group" data-id="<?php echo $data["id"];?>" data-name="<?php echo $data["group_name"];?>" id="btn-edit-<?php echo $data["id"];?>"> แก้ไข </a>
											|
											<a class="link-line-none btn-delete-group" data-id="<?php echo $data["id"];?>" id="btn-delete-<?php echo $data["id"];?>"> ลบ </a>
                                            |
                                            <a class="link-line-none" id="btn-manage-<?php echo $data["id"];?>" href="<?php echo base_url(PROJECTPATH.'/setting_electronic_document_data/manage_user_group?id='.$data["id"]);?>"> จัดการกลุ่มผู้รับ </a>
										</td>
									</tr>
								<?php
									}
								?>
								</tbody> 
							</table> 
						</div>
						<div class="col-xs-1 col-md-1"></div>
					</div>
					<div class="col-xs-12 col-md-12 text-center">
						<?php echo @$paging ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<div id="add_group_modal" tabindex="-1" role="dialog" class="modal fade">
	<div class="modal-dialog modal-dialog-data">
		<div class="modal-content">
			<div class="modal-header modal-header-confirmSave">
				<button type="button" class="close" data-dismiss="modal">x</button>
				<h2 class="modal-title"><span id="title_1">เพิ่มกลุ่ม</span></h2>
			</div>
			<div class="modal-body">
				<div class="form-group" style="padding-bottom: 50px;">
				<form id='form_add' data-toggle="validator" novalidate="novalidate" action="<?php echo base_url(PROJECTPATH.'/setting_electronic_document_data'); ?>" method="post">
					<input type="hidden" class="form-control" name="action" value="add">
					<div class="row">
						<label class="col-sm-4 control-label text-right" for="name">ชื่อกลุ่ม</label>
						<div class="col-sm-6">
							<div class="form-group">
								<input id="group-name" name="name" class="form-control m-b-1" type="text" value="" required title="กรุณากรอก ชื่อกลุ่ม">
							</div>
						</div>
						<label class="col-sm-2 control-label">&nbsp;</label>
					</div>
					<div class="form-group">
						<div class="col-sm-12" style="text-align:center;margin-top:20px;margin-bottom:20px;">
							<button type="submit" class="btn btn-primary">บันทึก</button>&nbsp;&nbsp;&nbsp;
							<button type="button" class="btn btn-default" data-dismiss="modal">ปิดหน้าต่าง</button>
						</div>
					</div>
				</form>
				</div>
			</div>
		</div>
	</div>
</div>
<div id="edit_group_modal" tabindex="-1" role="dialog" class="modal fade">
	<div class="modal-dialog modal-dialog-data">
		<div class="modal-content">
			<div class="modal-header modal-header-confirmSave">
				<button type="button" class="close" data-dismiss="modal">x</button>
				<h2 class="modal-title"><span id="title_1">แก้ไขกลุ่ม</span></h2>
			</div>
			<div class="modal-body">
				<div class="form-group" style="padding-bottom: 50px;">
				<form id='form_edit' data-toggle="validator" novalidate="novalidate" action="<?php echo base_url(PROJECTPATH.'/setting_electronic_document_data'); ?>" method="post">
					<input type="hidden" class="form-control" name="action" value="edit">
					<input type="hidden" class="form-control" name="id" id="edit-modal-id" value="">
					<div class="row">
						<label class="col-sm-4 control-label text-right" for="name">ชื่อกลุ่ม</label>
						<div class="col-sm-6">
							<div class="form-group">
								<input id="edit-group-name" name="name" class="form-control m-b-1" type="text" value="" required title="กรุณากรอก ชื่อหมวดเอกสาร">
							</div>
						</div>
						<label class="col-sm-2 control-label">&nbsp;</label>
					</div>
					<div class="form-group">
						<div class="col-sm-12" style="text-align:center;margin-top:20px;margin-bottom:20px;">
							<button type="submit" class="btn btn-primary">บันทึก</button>&nbsp;&nbsp;&nbsp;
							<button type="button" class="btn btn-default" data-dismiss="modal">ปิดหน้าต่าง</button>
						</div>
					</div>
				</form>
				</div>
			</div>
		</div>
	</div>
</div>
<form id='form_delete' data-toggle="validator" novalidate="novalidate" action="<?php echo base_url(PROJECTPATH.'/setting_electronic_document_data'); ?>" method="post">
	<input type="hidden" class="form-control" name="action" value="delete">
	<input type="hidden" class="form-control" name="id" id="delete-id-modal" value="">
</form>
<script>
	$(document).ready(function() {
        $("#btn-add-group").click(function() {
			$('#add_group_modal').modal('show');
		});
		$(".btn-edit-group").click(function() {
			$("#edit-modal-id").val($(this).attr("data-id"));
			$("#edit-group-name").val($(this).attr("data-name"));
			$("#edit_group_modal").modal('show');
		});
		$(".btn-delete-group").click(function() {
			id = $(this).attr("data-id");
			swal({
				title: "ท่านต้องการลบกลุ่มผู้รับใช่หรือไม่?",
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

