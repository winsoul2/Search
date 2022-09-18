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
		</div>
		<div class="row gutter-xs">
			<div class="col-xs-12 col-md-12">
				<div class="panel panel-body" style="padding-top:0px !important;">
					<form id='form_add' data-toggle="validator" novalidate="novalidate" action="<?php echo base_url(PROJECTPATH.'/setting_electronic_document_data/manage_user_group'); ?>" method="post">
						<input type="hidden" id="group_id" name="id" value="<?php echo $_GET["id"];?>"/>
						<div class="bs-example" data-example-id="striped-table">
							<div class="col-xs-1 col-md-1"></div>
							<br/>
							<div class="col-xs-12 col-md-12"><h2 style="margin-bottom: 0"><?php echo $group_name;?></h2></div>
							<div class="col-xs-3 col-md-3"></div>
							<div id="tb_wrap" class="col-xs-6 col-md-6">
								<br/>
								<br/>
								<table class="table table-bordered table-striped table-center">
									<thead> 
										<tr class="bg-primary">
											<th class="text-center" width="10%"> เลือก </th>
											<th class="text-center"> ชื่อกลุ่มผู้ใช้งานระบบ </th>
										</tr> 
									</thead>
									<tbody>
									<?php
										foreach($datas as $data) {
									?>
										<tr>
											<td class="text-center"><input type="checkbox" name="user_ids[]" id="checkbox_<?php echo $data["user_id"];?>" value="<?php echo $data["user_id"];?>" <?php echo !empty($data["id"]) ? "checked" : "";?>></td>
											<td class="text-left"><?php echo $data["user_name"];?></td>
										</tr>
									<?php
										}
									?>
									</tbody>
								</table>
							</div>
							<div class="col-xs-3 col-md-3"></div>
						</div>
						<div class="col-xs-12 col-md-12 text-center">
							<button type="submit" class="btn btn-primary">บันทึก</button>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>
<script>
	$(document).ready(function() {
	});
</script>
