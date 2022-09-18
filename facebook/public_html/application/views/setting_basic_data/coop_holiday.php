<div class="layout-content">
    <div class="layout-content-body">
		<style>
			.indent{
				text-indent: 40px;
				.modal-dialog-data {
					width:90% !important;
					margin:auto;
					margin-top:1%;
					margin-bottom:1%;
				}
			}
			table>thead>tr>th{
				text-align: center;
			}
			table>tbody>tr>td{
				text-align: center;
			}

			label {
				padding-top: 6px;
				text-align: right;
			}
			.text-center{
				text-align:center;
			}
			.bt-add{
				float:none;
			}
			.modal-dialog{
				width:80%;
			}
			small{
				display: none !important;
			}
		</style>
		<?php
		$act = @$_GET['act'];
		$id = @$_GET['id'];
		?>

		<?php if (@$act != "add") { ?>
		<h1 style="margin-bottom: 0">วันหยุดสหกรณ์</h1>
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 padding-l-r-0">
		<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 padding-l-r-0">
		<?php $this->load->view('breadcrumb'); ?>
		</div>
		
		<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 " style="padding-right:0px;text-align:right;">	
			<button class="btn btn-primary btn-lg bt-add" type="button" onclick="add_type();"><span class="icon icon-plus-circle"></span> เพิ่มปี</button> 
		</div>
		</div>
		<?php } ?>

		<div class="row gutter-xs">
				<div class="col-xs-12 col-md-12">
	              <div class="panel panel-body">				  
					<div class="bs-example" data-example-id="striped-table">
					 <table class="table table-striped"> 

						 <thead> 
						 	  <tr>
							   	<th style="width:  50%">ปี</th>
							    <th></th> 
							  </tr> 
						 </thead>

					      <tbody>
						   <?php  
							if(!empty($rs)){
								foreach(@$rs as $key => $row){
							?>
									<tr> 
										<td><?php echo @$row['work_year'] + 543; ?></td>
										<td>
											<a href="<?php echo base_url(PROJECTPATH.'/setting_basic_data/coop_holiday?y='.@$row["work_year"]); ?>">กำหนดวันหยุด</a> |
											<span class="text-del del" onclick="del_type('<?php echo @$row['work_year'] ?>')">ลบ</span>
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
				<h2 class="modal-title"><span id="title_1">เพิ่มปี</span></h2>
			</div>
			<div class="modal-body">
				<div class="form-group">
				<form id='form1' data-toggle="validator" novalidate="novalidate" action="<?php echo base_url(PROJECTPATH.'/setting_basic_data/save_coop_holiday'); ?>" method="post">	
					<div class="row">
						<label class="col-sm-4 control-label" for="type_code">ปี</label>
						<div class="col-sm-4">
							<select id="work_year" name="work_year" class="form-control m-b-1" required>
								<?php
								$year = date("Y");
								for($y = $year - 10; $y <= $year + 10; $y++) { ?>
									<option value="<?php echo $y; ?>"<?php if($y == $year) { ?> selected="selected"<?php } ?>><?php echo $y + 543; ?></option>
								<?php } ?>
							</select>
						</div>
					</div>
					<div class="row">
						<div class="col-sm-12 m-t-1" style="text-align:center;">
							<button type="button" class="btn btn-primary" onclick="save_type()">บันทึก</button>&nbsp;&nbsp;&nbsp;
							<button type="button" class="btn btn-default" data-dismiss="modal">ปิดหน้าต่าง</button>
						</div>
					</div>		
				</form>					
				</div>				
			</div>
		</div>
	</div>
</div>

<script>
	var base_url = $('#base_url').attr('class');
	
	$(document).ready(function() {
		
	});	
		
	function add_type(){
		$('#deposit_type_modal').modal('show');
	}
	
	function save_type(){
		$('#form1').submit();
	}
	
	function check_form(){
		$('#form_save').submit();
	}
	
	function edit_type(id,type_code,type_name){
		$('#type_id').val(id);
		$('#type_code').val(type_code);
		$('#type_name').val(type_name);
		$('#deposit_type_modal').modal('show');
	}
	
	function del_type(id){
		swal({
			title: "ท่านต้องการลบข้อมูลใช่หรือไม่",
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
				$.ajax({
					url: base_url+'/setting_basic_data/del_coop_holiday',
					method: 'POST',
					data: {
						'id': id
					},
					success: function(msg){
						if(msg == 1){
							document.location.href = base_url+'setting_basic_data/coop_holiday';
						}
					}
				});
			}
		});
	}
</script>