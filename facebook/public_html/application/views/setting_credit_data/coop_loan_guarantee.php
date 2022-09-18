<div class="layout-content">
    <div class="layout-content-body">
		<style>
			.center {
				text-align: center;
			}
			.right {
				text-align: right;
			}
			.modal-dialog-account {
				margin:auto;
				margin-top:7%;
			}
			label{
				padding-top:7px;
			}
		</style>

		<style type="text/css">
		  .form-group{
			margin-bottom: 5px;
		  }
		</style>
		<h1 style="margin-bottom: 0">ตั้งค่ากู้เงิน</h1>
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 padding-l-r-0">
		<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6 padding-l-r-0">
		<?php $this->load->view('breadcrumb'); ?>
		</div>
		<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 " style="padding-right:0px;text-align:right;">	
		   <button class="btn btn-primary btn-lg bt-add" type="button" onclick="add_list();"> เพิ่มรายการ </button> 
		</div>
		</div>
		
		<div class="row gutter-xs">
			<div class="col-xs-12 col-md-12">
				<div class="panel panel-body" style="padding-top:0px !important;">
						<div class="g24-col-sm-24 m-t-1 hidden_table" id="table_1">
						<div class="bs-example" data-example-id="striped-table">
							<table class="table table-bordered table-striped table-center">
								<thead> 
									<tr class="bg-primary">
										<th width="5%">#</th>
										<th width="50%">ช่วงเงินเดือน</th>
										<th width="25%">จำนวนสัญญาที่ค้ำได้</th>
										<th>จัดการ</th>
									</tr> 
								</thead>
								<tbody>
								<?php  
								$i = 1;
									if(!empty($row)){
										foreach(@$row as $key => $value){ 
								?>
									<tr> 
										<td><?php echo $i++; ?></td>
										<td><?php echo @$value['salary_start']." - ".@$value['salary_end']; ?></td>
										<td><?php echo @$value['guarantee_count']; ?></td>
										<td>
											<a title="แก้ไข" style="cursor:pointer;padding-left:2px;padding-right:2px" onclick="edit_list('<?php echo @$value['id']?>','<?php echo @$value['salary_start']; ?>','<?php echo @$value['salary_end']; ?>','<?php echo @$value['guarantee_count']; ?>')"><span style="cursor: pointer;" class="icon icon-edit"></span>
											</a>
											|
											<a title="ลบ" style="cursor:pointer;padding-left:2px;padding-right:2px" onclick="del_list('<?php echo @$value['id']?>')"><span style="cursor: pointer;" class="icon icon-trash-o"></span>
											</a>
										</td> 
									</tr>
								<?php 
										}
									} 
								?>
								</tbody> 
							</table> 
						</div>
						<?php echo @$paging ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<div id="list_modal" tabindex="-1" role="dialog" class="modal fade">
	<div class="modal-dialog modal-dialog-data">
		<div class="modal-content">
			<div class="modal-header modal-header-confirmSave">
				<button type="button" class="close" data-dismiss="modal">x</button>
				<h2 class="modal-title"><span id="title_1">จัดการการค้ำประกัน</span></h2>
			</div>
			<div class="modal-body">
				<div class="form-group" style="padding-bottom: 30px;">
				<form id='form2' data-toggle="validator" novalidate="novalidate" action="<?php echo base_url(PROJECTPATH.'/setting_credit_data/coop_loan_guarantee'); ?>" method="post">	
					<input type="hidden" class="form-control list_input" id="id" name="id" value="">
					<div class="form-group col-sm-12">
						<label class="col-sm-4 control-label" for="salary_start">ช่วงเงินเดือนตั้งแต่</label>
						<div class="col-sm-3">
							<input type="text" class="form-control list_input m-b-1" name="salary_start" id="salary_start" onKeyPress="return chkNumber(this)" required>
						</div>
						<label class="col-sm-1 control-label" for="salary_end">ถึง</label>
						<div class="col-sm-3">
							<input type="text" class="form-control list_input m-b-1" name="salary_end" id="salary_end" onKeyPress="return chkNumber(this)"  required>
						</div>
					</div>
					<div class="form-group col-sm-12" style="text-align:center;margin-top:10px;">
						<label class="col-sm-4 control-label" for="guarantee_count">จำนวนสัญญาที่ค้ำได้</label>
						<div class="col-sm-3">
						  <input id="guarantee_count" name="guarantee_count" class="form-control m-b-1 list_input" type="text" value="" onKeyPress="return chkNumber(this)" required>
						</div>
					</div>
					<div class="form-group">
						<div class="col-sm-12" style="text-align:center;margin-top:10px;margin-bottom:10px;">
							<button type="submit" class="btn btn-primary">บันทึก</button>&nbsp;&nbsp;&nbsp;
							<button type="button" class="btn btn-default" data-dismiss="modal">ปิดหน้าต่าง</button>
						</div>
					</div>					
				</form>					
				</div>
&nbsp;		
			</div>
		</div>
	</div>
</div>
<script>
	function add_list(){
		$('.list_input').val('');
		$('#list_modal').modal('show');
	}
	function edit_list(id,salary_start,salary_end,guarantee_count){
		$('#id').val(id);
		$('#salary_start').val(salary_start);
		$('#salary_end').val(salary_end);
		$('#guarantee_count').val(guarantee_count);
		$('#list_modal').modal('show');
	}
	function del_list(id){	
		swal({
			title: "ท่านต้องการลบข้อมูลนี้ใช่หรือไม่",
			text: "",
			type: "warning",
			showCancelButton: true,
			confirmButtonColor: '#DD6B55',
			confirmButtonText: 'ลบ',
			cancelButtonText: "ยกเลิก",
			closeOnConfirm: false,
			closeOnCancel: true
		},
		function(isConfirm) {
			if (isConfirm) {			
				document.location.href = base_url+'setting_credit_data/coop_loan_guarantee?action=del&id='+id;
			} else {
				
			}
		});
		
	}
	function chkNumber(ele){
		var vchar = String.fromCharCode(event.keyCode);
		if ((vchar<'0' || vchar>'9') && (vchar != '.')) return false;
		ele.onKeyPress=vchar;
	}
</script>
