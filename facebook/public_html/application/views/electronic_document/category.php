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
		<h1 style="margin-bottom: 0">หมวดเอกสาร</h1>
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 padding-l-r-0">
			<div class="col-xs-6 col-sm-6 col-md-6 col-lg-7 padding-l-r-0">
				<?php $this->load->view('breadcrumb'); ?>
			</div>
			<div class="col-xs-6 col-sm-6 col-md-6 col-lg-5 padding-l-r-0">
                <div class="col-sm-3 btn-col"></div>
                <div class="col-sm-3 btn-col"></div>
                <div class="col-sm-3 btn-col"></div>
                <div class="col-sm-3 btn-col">
                    <a class="link-line-none" id="btn-add-category">
                        <button class="btn btn-primary btn-lg bt-add" style="width:100% !important;" type="button">
                            เพิ่มหมวดเอกสาร
                        </button>
                    </a>
                </div>
			</div>
		</div>
		<div class="row gutter-xs">
			<div class="col-xs-12 col-md-12">
				<div class="panel panel-body" style="padding-top:0px !important;">
					<div class="bs-example" data-example-id="striped-table">
						<div class="col-xs-2 col-md-2"></div>
						<div id="tb_wrap" class="col-xs-8 col-md-8">
							<br/>
							<br/>
							<br/>
							<table class="table table-bordered table-striped table-center">
								<thead> 
									<tr class="bg-primary">
										<th class="text-center" width="5%">ลำดับ</th>
										<th class="text-center"> ชื่อหมวด </th>
                                        <th class="text-center" width="15%"></th>
									</tr> 
								</thead>
								<tbody>
								<?php
									foreach($datas as $data) {
								?>
									<tr>
										<td class="text-center"><?php echo $runno++;?></td>
										<td class="text-left"><?php echo $data["name"];?></td>
										<td>
											<a class="link-line-none btn-edit-category pointer" data-id="<?php echo $data["id"];?>" data-name="<?php echo $data["name"];?>" id="btn-edit-<?php echo $data["id"];?>"> แก้ไข </a>
											|
											<a class="link-line-none btn-delete-category pointer" data-id="<?php echo $data["id"];?>" id="btn-delete-<?php echo $data["id"];?>"> ลบ </a>
										</td>
									</tr>
								<?php
									}
								?>
								</tbody> 
							</table> 
						</div>
						<div class="col-xs-2 col-md-2"></div>
					</div>
					<div class="col-xs-12 col-md-12 text-center">
						<?php echo @$paging ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<div id="add_category_modal" tabindex="-1" role="dialog" class="modal fade">
	<div class="modal-dialog modal-dialog-data">
		<div class="modal-content">
			<div class="modal-header modal-header-confirmSave">
				<button type="button" class="close" data-dismiss="modal">x</button>
				<h2 class="modal-title"><span id="title_1">เพิ่มหมวดเอกสาร</span></h2>
			</div>
			<div class="modal-body">
				<div class="form-group" style="padding-bottom: 50px;">
				<form id='form_add' data-toggle="validator" novalidate="novalidate" action="<?php echo base_url(PROJECTPATH.'/elec_doc_archives/category'); ?>" method="post">
					<input type="hidden" class="form-control" name="action" value="add">
					<div class="row">
						<label class="col-sm-4 control-label text-right" for="name">ชื่อหมวด</label>
						<div class="col-sm-6">
							<div class="form-group">
								<input id="category-name" name="name" class="form-control m-b-1" type="text" value="" required title="กรุณากรอก ชื่อหมวดเอกสาร">
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
<div id="edit_category_modal" tabindex="-1" role="dialog" class="modal fade">
	<div class="modal-dialog modal-dialog-data">
		<div class="modal-content">
			<div class="modal-header modal-header-confirmSave">
				<button type="button" class="close" data-dismiss="modal">x</button>
				<h2 class="modal-title"><span id="title_1">แก้ไขหมวด</span></h2>
			</div>
			<div class="modal-body">
				<div class="form-group" style="padding-bottom: 50px;">
				<form id='form_edit' data-toggle="validator" novalidate="novalidate" action="<?php echo base_url(PROJECTPATH.'/elec_doc_archives/category'); ?>" method="post">
					<input type="hidden" class="form-control" name="action" value="edit">
					<input type="hidden" class="form-control" name="id" id="edit-modal-id" value="">
					<div class="row">
						<label class="col-sm-4 control-label text-right" for="name">ชื่อหมวด</label>
						<div class="col-sm-6">
							<div class="form-group">
								<input id="edit-category-name" name="name" class="form-control m-b-1" type="text" value="" required title="กรุณากรอก ชื่อหมวดเอกสาร">
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
<form id='form_delete' data-toggle="validator" novalidate="novalidate" action="<?php echo base_url(PROJECTPATH.'/elec_doc_archives/category'); ?>" method="post">
	<input type="hidden" class="form-control" name="action" value="delete">
	<input type="hidden" class="form-control" name="id" id="delete-id-modal" value="">
</form>
<script>
	$(document).ready(function() {
        $("#btn-add-category").click(function() {
			$('#add_category_modal').modal('show');
		});
		$(".btn-edit-category").click(function() {
			$("#edit-modal-id").val($(this).attr("data-id"));
			$("#edit-category-name").val($(this).attr("data-name"));
			$("#edit_category_modal").modal('show');
		});
		$(".btn-delete-category").click(function() {
			id = $(this).attr("data-id");
			swal({
				title: "ท่านต้องการลบหมวดเอกสารใช่หรือไม่?",
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
					$.ajax({
						url:base_url+"elec_doc_archives/check_delete_category?id="+$("#delete-id-modal").val(id), 
						method:"get",
						success:function(data){
							if(data == 'success'){
								$("#delete-id-modal").val(id);
								$("#form_delete").submit();
							}else{
								swal('ไม่สามารถลบได้ เนื่องจากมีเอกสารอยู่ในระบบ');
							}
						}
					});

				}
			});
		});
	});
</script>

