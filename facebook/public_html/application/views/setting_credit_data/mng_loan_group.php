<div id="loan_group_modal" tabindex="-1" role="dialog" class="modal fade">
	<div class="modal-dialog modal-dialog-data">
		<div class="modal-content">
			<div class="modal-header modal-header-confirmSave">
				<button type="button" class="close" data-dismiss="modal">x</button>
				<h2 class="modal-title"><span id="title_1">จัดการชื่อกลุ่มเงินกู้</span></h2>
			</div>
			<div class="modal-body">
				<div class="form-group" style="padding-bottom: 30px;">
					<form id='form_group_name' data-toggle="validator" novalidate="novalidate" action="<?php echo base_url(PROJECTPATH.'/setting_credit_data/coop_loan_group_save'); ?>" method="post">
						<input type="hidden" class="form-control" id="loan_group_id" name="loan_group_id" value="">
						<div class="form-group col-sm-12" style="text-align:center;margin-top:10px;">
							<label class="col-sm-4 control-label" for="loan_group_name">กลุ่มเงินกู้</label>
							<div class="col-sm-4">
								<input id="loan_group_name" name="loan_group_name" class="form-control m-b-1" type="text" value="" required>
							</div>
						</div>
						<div class="form-group col-sm-12" style="text-align:center;margin-top:10px;">
							<label class="col-sm-4 control-label" for="description">คำอธิบาย</label>
							<div class="col-sm-4">
								<input id="description" name="description" class="form-control m-b-1" type="text" value="">
							</div>
						</div>
						<div class="form-group col-sm-12" style="text-align:center;margin-top:10px;">
							<label class="col-sm-4 control-label" for="loan_name_description">สถานะ</label>
							<div class="col-sm-4 text-left">
								<label class="custom-control custom-control-primary custom-checkbox" style="padding-top: 9px;">
									<input type="checkbox" id="status" name="status" class="custom-control-input" value="1" checked>
									<span class="custom-control-indicator" style="margin-top: 9px;"></span>
									<span class="custom-control-label">แสดง</span>
								</label>
							</div>
						</div>
						<div class="form-group">
							<div class="col-sm-12" style="text-align:center;margin-top:10px;margin-bottom:10px;">
								<button type="button" class="btn btn-primary" onclick="save_gruop_name()">บันทึก</button>&nbsp;&nbsp;&nbsp;
								<button type="button" class="btn btn-default" data-dismiss="modal">ปิดหน้าต่าง</button>
							</div>
						</div>

						<table id="group_table" class="table table-bordered table-striped table-center">
							<thead>
							<tr class="bg-primary">
								<th width="80px">ลำดับ</th>
								<th>ชื่อกลุ่มเงินกู้</th>
								<th>สถานะ</th>
								<th width="100px"></th>
							</tr>
							</thead>
							<tbody>
							<?php
							$j = 1;
							if(!empty($group_name)){
								foreach(@$group_name as $key => $value){
									?>
									<tr>
										<td><?php echo @$j++ ; ?></td>
										<td style="text-align:left;"><?php echo @$value['loan_group_name']; ?></td>
										<td style="text-align:center;"><?php echo @$arr_group_status[$value['status']]; ?></td>
										<td>
											<a style="cursor:pointer;" onclick="edit_group_name('<?php echo @$value['loan_group_id']; ?>','<?php echo @$value['loan_group_name']; ?>','<?php echo @$value['description']; ?>','<?php echo @$value['status']; ?>');">แก้ไข</a>
											|
											<a style="cursor:pointer;" onclick="del_group_name('<?php echo @$value['loan_group_id']; ?>');" class="text-del">ลบ</a>
										</td>
									</tr>
									<?php
								}
							}
							?>
							</tbody>
						</table>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>
