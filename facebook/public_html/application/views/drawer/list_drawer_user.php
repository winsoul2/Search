<div class="layout-content">
    <div class="layout-content-body">
<style>
	.form-group { margin-bottom: 0; }

	.permission_list { margin: 0 0 20px 0; padding: 0; list-style: none; }
	.permission_list ul { margin: 0 0 0 20px; padding: 0; list-style: none; }
	.mem_pic { float: right; width: 150px; }
    .mem_pic img { width: 100%; border: solid 1px #ccc; }
    .mem_pic button { display: block; width: 100%; }

</style>
<link rel="stylesheet" href="<?=base_url('assets/css/select2.min.css')?>">
<script src="<?=base_url('assets/js/select2.min.js')?>"></script>

	<?php if(isset($_GET['add']) || isset($_GET['edit'])) {?>
		<div class="row gutter-xs">
			<div class="col-xs-12 col-md-12">
			<div class="layout-content-body">


	<h1 class="text-center m-t-1 m-b-2"><?php if(isset($_GET['add'])) { echo 'เพิ่มผู้ใช้งาน'; } if(isset($_GET['edit']) ) { echo 'แก้ไขผู้ใช้งาน'; } ?></h1>
	<form method="post" action="<?php if(isset($_GET['add'])) { echo base_url(PROJECTPATH.'/drawer/add_user_drawer'); } if(isset($_GET['edit']) ) { echo base_url(PROJECTPATH.'/drawer/edit_user_drawer'); } ?>" class="drawer-user-form form form-horizontal" autocomplete="off" enctype="multipart/form-data" novalidate="novalidate">
		<input type="hidden" name="user_primary" value="<?=$_GET['user_primary']?>">
		<?php if (isset($_GET['edit']) && !empty($_GET['edit'])) { ?>
		<input type="hidden" name="drawer_user" value="<?=$drawer_user->id?>">
		<?php } ?>
	<div class="col-xs-4">
				<button class="btn btn-primary btn-lg drawer-user-add bt-add" >
				<span class="icon icon-plus-circle"></span>
					<?php if(isset($_GET['add'])) { echo 'เพิ่มผู้ใช้งาน'; } if(isset($_GET['edit']) ) { echo 'แก้ไขผู้ใช้งาน'; } ?>
				</button>

		</div>
	<div class="col-md-6 col-md-offset-3">

			<table class="table table-bordered table-striped">
				<thead>
					<tr class="bg-primary">
						<th class = "font-normal text-center" style="width: 15%">รหัสสมาชิก</th>
						<th class = "font-normal text-center" style="width: 25%;">ชื่อ-สกุล</th>
					</tr>
				</thead>
				<tbody id="table_data">
					<tr>
						<td>
						<select name='add_drawer_user'  class='form-control select2' >
						<option data-id="" data-name="" data-employee-id=""  value="">กรุณาเลือกผู้ใช้งาน</option>

							<?php foreach ($user_list as $key => $value) { ?>

								<option data-id="<?=$value['user_id']?>" <?php if(isset($_GET['edit'])) { echo ($user_data->user_id == $value['user_id']) ? 'selected' : '' ; } ?> data-name="<?=$value['user_name']?>" data-employee-id="<?=$value['employee_id']?>" name="drawer_user_id" value="<?=$value['user_id']?>"><?=$value['user_name']?></option>
							<?php } ?>

						</select>
						</td>
						<td>

							<input type='text' disabled name="user_name" class='user_name form-control' value="<?php if(isset($_GET['edit'])) { echo $user_data->user_name; }  ?>" readonly>
						</td>

					</tr>

				</tbody>
			</table>

		</form>

	</div>


	</div>
			</div>
		</div>
	<?php } else { ?>
		<h1 style="margin-bottom: 0">กำหนดผู้ใช้งานลิ้นชัก</h1>
	<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 padding-l-r-0">
		<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 padding-l-r-0">
			<?php $this->load->view('breadcrumb'); ?>
		</div>
		<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 padding-l-r-0">
		<?php if (empty($cdu)) { ?>

			<a type="button" class="btn btn-primary btn-lg bt-add" href="?add&user_primary=2">
				<span class="icon icon-plus-circle"></span>
				เพิ่มผู้ใช้งาน
			</a>
		</div>
		<?php } ?>
	</div>
		<div class="row gutter-xs">
			<div class="col-xs-12 col-md-12">
				<div class="panel panel-body">

				<div class="bs-example" data-example-id="striped-table">
					<table class="table table-striped">
						<thead>
							<tr>
								<th style="width: 100px;">ระดับผู้ใช้งาน</th>
								<th>รหัสพนักงาน</th>
								<th>ชื่อสกุล</th>

								<th style="width: 200px;"></th>
							</tr>
						</thead>

						<tbody>
							<?php foreach ($du as $key => $value) {
								if ($value['user_primary'] != 1) {
								?>
									<tr>
										<td scope="row"><?=$value['primary_name']?></td>
										<td><?=$value['employee_id']?></td>
										<td><?=$value['user_name']?></td>
										<td>
											<a href="?edit=<?=$value['user_id']?>">แก้ไข</a>|
											<?php if ($value['user_primary'] == 2) { ?>
												<a href="?add&user_primary=3">เพิ่มผู้ใช้งาน</a>
											<?php } ?>
											<?php if ($value['user_primary'] == 3) { ?>
												<span class="text-danger delete-user" data-id="<?=$value['id']?>" style="cursor: pointer">ลบ</span>
											<?php } ?>
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
			</div>
		</div>
	<?php } ?>

	</div>
</div>

<script>
$(document).on('click' , '.delete-user' , function() {
	var deleteID = $(this).attr('data-id')
	swal({
		title: "ท่านต้องการทำรายการใช่หรือไม่",
		text: "" ,
		type: "warning",
		showCancelButton: true,
		confirmButtonColor: '#DD6B55',
		confirmButtonText: 'ตกลง',
		cancelButtonText: "ยกเลิก",
		closeOnConfirm: false,
		closeOnCancel: true
	},
	function(isConfirm) {
		if (isConfirm) {
			$.ajax({
				type: 'POST',
				url: base_url + 'drawer/delete_user_drawer',
				dataType: "json",
				data: {
						'dataID' : deleteID,
				},
				success: function (msg) {
					if (msg.status) {
						location.reload();
					}

				}
			})
		} else {

		}
	});

})
$('.select2').select2({
	matcher: function(params, data) {
		if ($.trim(params.term) === '') return data;
		if (typeof data.text === 'undefined') return null;
		console.log($(data.element).data("name"));

		// `params.term` should be the term that is used for searching
		// `data.text` is the text that is displayed for the data object
			if (data.text.indexOf(params.term) > -1 || $(data.element).data("name").toString().indexOf(params.term) > -1) {
				var modifiedData = $.extend({}, data, true);
				modifiedData.text += ' (matched)';

				return modifiedData;
			}


		// Return `null` if the term should not be displayed
		return null;
	}
});
$(document).on('click' , '.drawer-user-add' , function(e) {
	e.preventDefault();
	$(this).attr('disabled', true)
	if ($('.select2 option:selected').data('id') == "") {
		$(this).attr('disabled', false)
		swal('กรุณาเลือกผู้ใช้งาน','','warning');
	} else {
		$('.drawer-user-form').submit()
	}

})
$(document).on('change' , '.select2' , function(e) {
	$('.user_name').val($('.select2 option:selected').data('name'))
})
</script>