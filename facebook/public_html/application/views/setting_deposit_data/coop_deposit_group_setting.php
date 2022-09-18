<div class="layout-content">
	<div class="layout-content-body">
		<style>
			.indent {
				text-indent: 40px;

			.modal-dialog-data {
				width: 90% !important;
				margin: auto;
				margin-top: 1%;
				margin-bottom: 1%;
			}

			}
			table > thead > tr > th {
				text-align: center;
			}

			table > tbody > tr > td {
				text-align: center;
			}

			label {
				padding-top: 6px;
				text-align: right;
			}

			.text-center {
				text-align: center;
			}

			.bt-add {
				float: none;
			}

			.modal-dialog {
				width: 80%;
			}

			small {
				display: none !important;
			}
		</style>
		<?php
		$act = @$_GET['act'];
		$id = @$_GET['id'];
		?>

		<?php if (@$act != "add") { ?>
			<h1 style="margin-bottom: 0">รายการประเภทเงินฝาก</h1>
			<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 padding-l-r-0">
				<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 padding-l-r-0">
					<?php $this->load->view('breadcrumb'); ?>
				</div>

				<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 " style="padding-right:0px;text-align:right;">
					<button class="btn btn-primary btn-lg bt-add" type="button" onclick="add_type();"><span
							class="icon icon-plus-circle"></span> เพิ่มรายการประเภทเงินฝาก
					</button>
				</div>
			</div>
		<?php } ?>

		<div class="row gutter-xs">
			<div class="col-xs-12 col-md-12">
				<div class="panel panel-body">
					<div class="bs-example" data-example-id="striped-table">
						<table class="table table-striped">
							<input type="hidden" class="form-control" name="id" id="id" value="<?php echo @$_GET['id']; ?>">
							<thead>
							<tr>
								<th>ลำดับ</th>
								<th>รหัส</th>
								<th class="text-center">ชื่อหมวดหมู่เงินฝาก</th>
								<th class="text-left"></th>
							</tr>
							</thead>

							<tbody>
							<?php
							if (!empty($rs)) {
								foreach (@$rs as $key => $row) {
									$createdatetime = @$rs[$row['id']]['createdatetime'];
									?>
									<tr>
										<td>
										<?php echo @$i++; ?></th>
										<td><?php echo @$row['id']; ?></td>
										<td class="text-center"><?php echo @$row['group_name_transaction']; ?></td>
										<td>
											<a href="<?php echo base_url(PROJECTPATH . '/setting_deposit_data/coop_deposit_group_setting_detail?id=' . @$row['id'].'&group_name_transaction='.@$row['group_name_transaction']); ?>">ดูรายละเอียด</a>
											|

											<a style="cursor:pointer;"
											   onclick="edit_type('<?php echo @$row['id']; ?>','<?php echo @$row['group_name_transaction']; ?>');">แก้ไข</a>
											|
											<span class="text-del del"  onclick="del_group('<?php echo @$row['id'] ?>')">ลบ</span>
										</td>
									</tr>
									<?php
								}
							}
							?>

							</tbody>
						</table>
					</div>

				</div>
				<?php echo @$paging; ?>
			</div>
		</div>
	</div>
</div>

<div id="deposit_type_modal" tabindex="-1" role="dialog" class="modal fade">
	<div class="modal-dialog modal-dialog-data">
		<div class="modal-content">
			<div class="modal-header modal-header-confirmSave">
				<button type="button" class="close" data-dismiss="modal">x</button>
				<h2 class="modal-title"><span id="title_1">เพิ่มรายการประเภทเงินฝาก</span></h2>
			</div>
			<div class="modal-body">
				<div class="form-group">
					<form id='form1' data-toggle="validator" novalidate="novalidate"
						  action="<?php echo base_url(PROJECTPATH . '/setting_deposit_data/coop_deposit_group_setting_save'); ?>"
						  method="post">
						<input type="hidden" class="form-control" id="id" name="id" value="">
						<div class="row">
							<label class="col-sm-4 control-label" for="group_name_transaction">รายการประเภทเงินฝาก</label>
							<div class="col-sm-4">
								<input id="group_name_transaction" name="group_name_transaction" class="form-control m-b-1" type="text" value=""
									   required>
							</div>
						</div>
						<div class="row">
							<div class="col-sm-12 m-t-1" style="text-align:center;">
								<button type="button" class="btn btn-primary" onclick="save_type()">บันทึก</button>&nbsp;&nbsp;&nbsp;
								<button type="button" class="btn btn-default" data-dismiss="modal">ปิดหน้าต่าง
								</button>
							</div>
						</div>
				</div>
				</form>
			</div>
		</div>
	</div>
</div>
</div>

<?php
$v = date('YmdHis');
$link = array(
	'src' => PROJECTJSPATH . 'assets/js/coop_deposit_group_setting.js?v=' . $v,
	'type' => 'text/javascript'
);
echo script_tag($link);
?>
