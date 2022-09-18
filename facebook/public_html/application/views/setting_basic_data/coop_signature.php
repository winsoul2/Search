<div class="layout-content">
    <div class="layout-content-body">
	<?php
	$act = @$_GET['act'];
	$id = @$_GET['id'];
	?>

	<?php if (@$act != "add") { ?>
	<h1 style="margin-bottom: 0">ข้อมูลลายเซ็นต์</h1>
	<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 padding-l-r-0">
	<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 padding-l-r-0">
	<?php $this->load->view('breadcrumb'); ?>
	</div>
	<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 padding-l-r-0">
	<a class="link-line-none" href="?act=add">
	<button class="btn btn-primary btn-lg bt-add" type="button">
	<span class="icon icon-plus-circle"></span>
	เพิ่มลายเซ็นต์
	</button>
	</a>
	</div>
	</div>
	<?php } ?>

	<?php if (@$act != "add") { ?>
		<div class="row gutter-xs">
			<div class="col-xs-12 col-md-12">
			  <div class="panel panel-body">	                
				<div class="bs-example" data-example-id="striped-table">
					 <table class="table table-striped"> 

						 <thead> 
							  <tr>
								<th>ลำดับ</th>
								<th>เริ่มใช้วันที่</th>
								<th style="width: 20%;">เจ้าหน้าที่การเงิน</th>
								<th style="width: 20%;">หัวหน้าสินเชื่อ</th> 
								<th style="width: 20%;">ผู้จัดการ</th> 
								<th style="width: 20%;">ประธาน</th>
								<th></th>
							  </tr> 
						 </thead>

						<tbody>
					   <?php  
						if(!empty($rs)){
							foreach(@$rs as $key => $row){ ?>
								<tr> 
								<th scope="row"><?php echo @$i++; ?></th>
								<td><?php echo $this->center_function->ConvertToThaiDate(@$row['start_date'],false,false); ?></td> 
								<td><?php echo @$row['finance_name']; ?></td> 
								<td><?php echo @$row['receive_name']; ?></td> 
								<td><?php echo @$row['manager_name']; ?></td> 
								<td><?php echo @$row['president_name']; ?></td>
								<td>
									<a href="?act=add&id=<?php echo @$row["signature_id"] ?>">แก้ไข</a> | 
									<span class="text-del del"  onclick="del_coop_basic_data('<?php echo @$row['signature_id'] ?>')">ลบ</span>
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
	<?php }else{ ?>

			<div class="col-md-6 col-md-offset-3">

				<h1 class="text-center m-t-1 m-b-2"><?php echo  (!empty($id)) ? "แก้ไขลายเซ็นต์" : "เพิ่มลายเซ็นต์" ; ?></h1>

				<form id='form_save' data-toggle="validator" novalidate="novalidate" action="<?php echo base_url(PROJECTPATH.'/setting_basic_data/coop_signature_save'); ?>" method="post"  enctype="multipart/form-data">	
					<?php if (!empty($id)) { ?>
					<input name="type_add"  type="hidden" value="edit" required>
					<input id="id" name="id"  type="hidden" value="<?php echo $id; ?>" required>
					<?php }else{ ?>
					<input name="type_add"  type="hidden" value="add" required>
					<?php } ?>	
					
					<div class="row">
						<label class="col-sm-3 control-label" for="form-control-2">เริ่มใช้วันที่</label>
						<div class="col-sm-9">
						  <div class="form-group">
							  <input id="start_date" name="start_date" class="form-control m-b-1" style="padding-left: 50px;" type="text" value="<?php echo $this->center_function->mydate2date(empty($row['start_date']) ? '' : @$row['start_date']); ?>" data-date-language="th-th" required title="กรุณาเลือก เริ่มใช้วันที่">
							  <span class="icon icon-calendar input-icon m-f-1"></span>
							</div>
						</div>
					</div>
					<div class="row">
						<label class="col-sm-3 control-label" for="form-control-2">เจ้าหน้าที่การเงิน</label>
						<div class="col-sm-9">
							<div class="form-group">
							  <input id="finance_name" name="finance_name" class="form-control m-b-1" type="text" value="<?php echo @$row['finance_name'] ?>" required title="กรุณากรอก เจ้าหน้าที่การเงิน">
							</div>
						</div>
					</div>
					<div class="row">
						<label class="col-sm-3 control-label" for="form-control-2">ลายเซ็นต์</label>
						<div class="col-sm-9">
							<div class="col-sm-9" style="margin-left: -13px;">
								<label class="fileContainer btn btn-info">
									<span class="icon icon-paperclip"></span> 
									เลือกไฟล์
									<input id="signature_1" type="file" name="signature_1" accept="image/*" multiple="" onchange="loadFile1(event)">
								</label>
								
								<?php if(@$row['signature_1']){?>
								<img style="max-height: 100px;" class="img-responsive" src="<?php echo base_url(PROJECTPATH.'/assets/images/coop_signature/'.@$row['signature_1']) ?>" id="output1"/>
								<?php }else{ ?>
								<img style="max-height: 100px;" class="img-responsive" src="<?php echo base_url(PROJECTPATH.'/assets/images/default.jpg') ?>" id="output1"/>
								<?php } ?>
								<p>&nbsp;</p>
							</div>
						</div>
					</div>
					<div class="row">
						<label class="col-sm-3 control-label" for="form-control-2">หัวหน้าสินเชื่อ</label>
						<div class="col-sm-9">
							<div class="form-group">
							  <input id="receive_name" name="receive_name" class="form-control m-b-1" type="text" value="<?php echo @$row['receive_name'] ?>" required title="กรุณากรอก หัวหน้าสินเชื่อ">
							</div>
						</div>
					</div>
					<div class="row">
						<label class="col-sm-3 control-label" for="form-control-2">ลายเซ็นต์</label>
						<div class="col-sm-9">
							<div class="col-sm-9" style="margin-left: -13px;">
								<label class="fileContainer btn btn-info">
									<span class="icon icon-paperclip"></span> 
									เลือกไฟล์
									<input id="signature_2" type="file" name="signature_2" accept="image/*" multiple="" onchange="loadFile2(event)">
								</label>
								
								<?php if(@$row['signature_2']){?>
								<img style="max-height: 100px;" class="img-responsive" src="<?php echo base_url(PROJECTPATH.'/assets/images/coop_signature/'.@$row['signature_2']) ?>" id="output2"/>
								<?php }else{ ?>
								<img style="max-height: 100px;" class="img-responsive" src="<?php echo base_url(PROJECTPATH.'/assets/images/default.jpg') ?>" id="output2"/>
								<?php } ?>
								<p>&nbsp;</p>
							</div>
						</div>
					</div>
					<div class="row">
						<label class="col-sm-3 control-label" for="form-control-2">ผู้จัดการ</label>
						<div class="col-sm-9">
							<div class="form-group">
							  <input id="manager_name" name="manager_name" class="form-control m-b-1" type="text" value="<?php echo @$row['manager_name'] ?>" required title="กรุณากรอก ผู้จัดการ">
							</div>
						</div>
					</div>
					<div class="row">
						<label class="col-sm-3 control-label" for="form-control-2">ลายเซ็นต์</label>
						<div class="col-sm-9">
							<div class="col-sm-9" style="margin-left: -13px;">
								<label class="fileContainer btn btn-info">
									<span class="icon icon-paperclip"></span> 
									เลือกไฟล์
									<input id="signature_3" type="file" name="signature_3" accept="image/*" multiple="" onchange="loadFile3(event)">
								</label>
								
								<?php if(@$row['signature_3']){?>
								<img style="max-height: 100px;" class="img-responsive" src="<?php echo base_url(PROJECTPATH.'/assets/images/coop_signature/'.@$row['signature_3']) ?>" id="output3"/>
								<?php }else{ ?>
								<img style="max-height: 100px;" class="img-responsive" src="<?php echo base_url(PROJECTPATH.'/assets/images/default.jpg') ?>" id="output3"/>
								<?php } ?>
								<p>&nbsp;</p>
							</div>
						</div>
					</div>
                    <div class="row">
                        <label class="col-sm-3 control-label" for="form-control-2">ผู้จัดการ</label>
                        <div class="col-sm-9">
                            <div class="form-group">
                                <input id="president_name" name="president_name" class="form-control m-b-1" type="text" value="<?php echo @$row['president_name'] ?>" required title="กรุณากรอก ผู้จัดการ">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <label class="col-sm-3 control-label" for="form-control-2">ลายเซ็นต์</label>
                        <div class="col-sm-9">
                            <div class="col-sm-9" style="margin-left: -13px;">
                                <label class="fileContainer btn btn-info">
                                    <span class="icon icon-paperclip"></span>
                                    เลือกไฟล์
                                    <input id="signature_4" type="file" name="signature_4" accept="image/*" multiple="" onchange="loadFile4(event)">
                                </label>

                                <?php if(@$row['signature_4']){?>
                                    <img style="max-height: 100px;" class="img-responsive" src="<?php echo base_url(PROJECTPATH.'/assets/images/coop_signature/'.@$row['signature_4']) ?>" id="output4"/>
                                <?php }else{ ?>
                                    <img style="max-height: 100px;" class="img-responsive" src="<?php echo base_url(PROJECTPATH.'/assets/images/default.jpg') ?>" id="output4"/>
                                <?php } ?>
                                <p>&nbsp;</p>
                            </div>
                        </div>
                    </div>
					<div class="form-group text-center">
						<button type="button"  onclick="check_form()" class="btn btn-primary min-width-100">ตกลง</button>
						<a href="?"><button class="btn btn-danger min-width-100" type="button">ยกเลิก</button></a>
					</div>
			  </form>
			</div>

<?php } ?>


	</div>
</div>
<script>
	$(document).ready(function() {
		$("#start_date").datepicker({
		  prevText : "ก่อนหน้า",
		  nextText: "ถัดไป",
		  currentText: "Today",
		  changeMonth: true,
		  changeYear: true,
		  isBuddhist: true,
		  monthNamesShort: ['ม.ค.', 'ก.พ.', 'มี.ค.', 'เม.ย.', 'พ.ค.', 'มิ.ย.', 'ก.ค.', 'ส.ค.', 'ก.ย.', 'ต.ค.', 'พ.ย.', 'ธ.ค.'],
		  dayNamesMin: ['อา', 'จ', 'อ', 'พ', 'พฤ', 'ศ', 'ส'],
		  constrainInput: true,
		  dateFormat: "dd/mm/yy",
		  yearRange: "c-50:c+10",
		  autoclose: true,
		});
	});
	
	function check_form(){
		$.ajax({
			url: base_url+'/setting_basic_data/check_date_signature',
			method: 'POST',
			data: {
				'start_date': $("#start_date").val(),
				'id': $("#id").val()
			},
			success: function(msg){
			   //console.log(msg); return false;
				if(msg == 1){
				  $('#form_save').submit();
				}else{
					swal('วันที่เริ่มใช้ซ้ำ กรุณาเลือกวันที่เริ่มใช้ใหม่');
				}
			}
		});		 
	}	
	

	var loadFile1 = function(event) {
		var output = document.getElementById('output1');
		output.src = URL.createObjectURL(event.target.files[0]);
	};

	var loadFile2 = function(event) {
		var output = document.getElementById('output2');
		output.src = URL.createObjectURL(event.target.files[0]);
	};

	var loadFile3 = function(event) {
		var output = document.getElementById('output3');
		output.src = URL.createObjectURL(event.target.files[0]);
	};

    var loadFile4 = function(event) {
        var output = document.getElementById('output4');
        output.src = URL.createObjectURL(event.target.files[0]);
    };
	
	function del_coop_basic_data(id){	
		swal({
			title: "คุณต้องการที่จะลบ",
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
					url: base_url+'/setting_basic_data/del_coop_basic_data',
					method: 'POST',
					data: {
						'table': 'coop_signature',
						'id': id,
						'field': 'signature_id'
					},
					success: function(msg){
					  // console.log(msg); return false;
						if(msg == 1){
						  document.location.href = base_url+'setting_basic_data/coop_signature';
						}else{

						}
					}
				});
			} else {
				
			}
		});
		
	}
</script>	
    