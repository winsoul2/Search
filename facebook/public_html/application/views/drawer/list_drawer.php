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
	
		<h1 style="margin-bottom: 0">รายการเงินลิ้นชัก</h1>
	<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 padding-l-r-0">
		<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 padding-l-r-0">
			<?php $this->load->view('breadcrumb'); ?>
		</div>
		<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 padding-l-r-0">
		
	</div>
	<?php if(isset($_GET['detail']) &&  !empty($_GET['detail'])  ) { ?>

		<div class="row gutter-xs">

			<div class="col-xs-12 col-md-12">
				<div class="panel panel-body">
		<h2 style="margin-bottom: 0">รายละเอียดรายการเงินลิ้นชัก</h2>
		<div class="row m-t-1">
			<div class="g24-col-sm-24">
				<div class="form-group">
					<div class=" g24-col-sm-24">			
							<label class="g24-col-sm-3 control-label font-normal" for="form-control-2">วันที่</label>			
							<div class="g24-col-sm-4 m-b-1">
								<input type="text" class="form-control"  name="date" id="date" value="<?=date('d/m/Y',strtotime($getDrawer[0]['date'])) ?>" disabled readonly>
							</div>
											
					</div>
				</div>
			
			</div>
		</div>
				<div class="bs-example" data-example-id="striped-table">				
					<table class="table table-striped"> 
						<thead> 
							<tr>
								<th style="width: 100px;">ระดับผู้ใช้</th>
								<th>รหัสพนักงาน</th>
								<th>ชื่อสกุล</th>
								<th>จำนวนเงินทั้งหมด</th>
								<th>จำนวนเงินเหลือ</th>

							</tr> 
						</thead>
						
						<tbody>
						<?php foreach ($getDrawer as $key => $value) { 
								
								?>
									<tr>
										<td scope="row"><?=$value['primary_name']?></td>
										<td><?=$value['employee_id']?></td> 
										<td><?=$value['user_name']?></td> 		
										<td style="padding-left: 40px;"><?=number_format($value['budget'])?></td> 								
										<td style="padding-left: 40px;"><?=number_format($value['balance'])?></td> 								

									</tr>
							<?php
								
							} 
							?>
							<?php foreach ($getDetail as $key => $value) { 
								
								?>
									<tr>
										<td scope="row"><?=$value['primary_name']?></td>
										<td ><?=$value['employee_id']?></td> 
										<td><?=$value['user_name']?></td> 	
										<td style="padding-left: 40px;"><?=number_format($value['amount'])?></td> 								
										<td style="padding-left: 40px;"><?=number_format($value['balance'])?></td> 								

									</tr>
							<?php
								
							} 
							?>
								
						
						</tbody> 
					</table>
				</div>
		
				</div>
			</div>
		</div>
	<?php } else { ?>
		<div class="row gutter-xs">
			<div class="col-xs-12 col-md-12">
				<div class="panel panel-body">
				
				<div class="bs-example" data-example-id="striped-table">				
					<table class="table table-striped"> 
						<thead> 
							<tr>
								<th style="width: 200px;">วันที่</th>
								<th>จำนวนเงิน</th>
								<th>ยอดคงเหลือ</th>
								
								<th style="width: 200px;"></th> 
							</tr> 
						</thead>
						
						<tbody>
							<?php foreach ($data as $key => $value) { 
								?>
									<tr>
										<td scope="row"><?=$this->center_function->ConvertToThaiDate($value['date'])?></td>
										<td style="padding-left: 40px;"><?=number_format($value['budget'])?></td> 
										<td style="padding-left: 40px;"><?=number_format($value['balance'])?></td> 								
										<td>
											<a href="?detail=<?=$value['id']?>">รายละเอียด</a>
											
										</td>
									</tr>
							<?php
								
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