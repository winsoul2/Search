<div class="layout-content">
    <div class="layout-content-body">
		<?php
		$mysqli_upbean = new mysqli("report.upbean.co.th", "upbean_report", "aPY9rD3wL");
		$mysqli_upbean->select_db("upbean_report");
		$mysqli_upbean->set_charset("utf8");

		$problem_priority = array('1'=>'ปกติ','2'=>'เร่งด่วน','3'=>'เร่งด่วนมาก');
		?>
	
		<h1 style="margin-bottom: 0">แจ้งปัญหาและข้อเสนอแนะ</h1>
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 padding-l-r-0">
			<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 padding-l-r-0">
				<?php $this->load->view('breadcrumb'); ?>
			</div>
		</div>
		<div class="row gutter-xs">
			<div class="col-xs-12 col-md-12">
				<div class="panel panel-body">
					<div class="panel-body">
						<form class="form-horizontal" id="frm_problem" action="<?php echo base_url(PROJECTPATH.'/report_problem_data/report_problem_save');?>" method="post"  enctype="multipart/form-data" >
							<input type="hidden" name="problem_id" value="<?php echo @$row['problem_id']; ?>"/>
							<div class="form-group">
								<label for="problem_title" class="col-sm-3 control-label">หัวข้อ</label>
								<div class="col-sm-9">
									<input type="text"  class="form-control" id="problem_title" name="problem_title" placeholder="" value="<?php echo @$row['problem_title']; ?>" required >
								</div>
							</div>
							<div class="form-group">
								<label for="problem_description" class="col-sm-3 control-label">รายละเอียด</label>
								<div class="col-sm-9">
									<textarea id="problem_description" name="problem_description" ><?php echo @$row['problem_description']; ?></textarea>
								</div>
							</div>
							<div class="form-group">																	
								<label for="problem_file" class="col-sm-3 control-label">แนบไฟล์</label>
								<div class="col-sm-9">
									<label class="fileContainer btn btn-info">
										<span class="icon icon-paperclip"></span> 
										เลือกไฟล์
										<input type="file" id="problem_file" name="problem_file[]" multiple>
									</label>								
									
									<div style="padding-top:10px" >
											<div class="row" style="">
											<?php 
												if(!empty($rs_file)){
													foreach(@$rs_file as $key => $row_file){ 
														$file_type = explode('/',$row_file['problem_file_type']);
														$file_type = $file_type[0];
														//echo $file_type;
												?>
														<div class="col-xs-2 text-center" id="<?php echo "file_".@$row_file['problem_file_id']; ?>">
															<a class="fancybox" href="<?php echo base_url(PROJECTPATH.'/assets/uploads/report_problem/'.@$row_file['problem_file_name']) ?>" target="_blank" >
																<?php if($file_type == 'image'){ ?>
																	<img class="img-responsive img-thumbnail" src="<?php echo base_url(PROJECTPATH.'/timthumb.php') ?>?src=<?php echo base_url(PROJECTPATH.'/assets/uploads/report_problem/'.@$row_file['problem_file_name']) ?>&w=300&h=300" style="margin:auto" />
																<?php }else{ ?>
																	<img class="img-responsive img-thumbnail" src="<?php echo base_url(PROJECTPATH.'/timthumb.php') ?>?src=<?php echo base_url(PROJECTPATH.'/assets/images/video_default.png') ?>&w=300&h=300" style="margin:auto" />
																<?php } ?>
															</a>
															
															<a title="ลบข้อมูล" onclick="delete_file('<?php echo @$row_file['problem_file_id']; ?>','<?php echo @$row_file['problem_file_name']; ?>');" style="cursor:pointer;">
																<i class="icon icon-trash-o"></i>
															</a>
														</div>
												
											<?php 
													}
												}
											?>
											</div>
										</div>
								</div>
							</div>
							<div class="form-group">
								<label for="problem_description" class="col-sm-3 control-label">ความเร่งด่วน</label>
								<div class="col-sm-3">
									<select class="form-control" id="problem_priority" name="problem_priority">
										<option value="">เลือกข้อมูล</option>
										<?php
											foreach($problem_priority as $key => $value){ ?>
												<option value="<?php echo @$key; ?>" <?php echo @$row['problem_priority']==@$key?'selected':''; ?>><?php echo @$value; ?></option>
											<?php }
										?>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label for="finish_date" class="col-sm-3 control-label">วันที่ต้องการให้แก้ไขเสร็จ</label>
								<div class="col-sm-3">
								<input id="finish_date" name="finish_date" class="form-control m-b-1" style="padding-left: 30px;" type="text" value="<?php echo $this->center_function->mydate2date(empty($row['finish_date']) ? date("Y-m-d") : $row['finish_date']); ?>" data-date-language="th-th" required title="กรุณาป้อน วันที่ต้องการให้แก้ไขเสร็จ">
								<span class="icon icon-calendar input-icon m-f-1"></span>
								</div>
							</div>
							  
							<div class="form-group">
								<div class="col-sm-offset-3 col-sm-10">
									<button type="submit" class="btn btn-primary" style="margin-left:5px;" value="save">บันทึกข้อมูล</button>
									<a class="btn btn-danger" style="margin-left:5px;" href="<?php echo base_url(PROJECTPATH.'/report_problem_data/report_problem');?>">ออก</a>
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<?php
$link = array(
    'src' => PROJECTJSPATH.'assets/ckeditor/ckeditor.js',
    'type' => 'text/javascript'
);
echo script_tag($link);

$link = array(
    'src' => PROJECTJSPATH.'assets/ckeditor/adapters/jquery.js',
    'type' => 'text/javascript'
);
echo script_tag($link);

?>
<script>
	$(document).ready(function() {
		
		if($("#problem_description").length) {
			$("#problem_description").ckeditor({ height : 146 , customConfig : '<?php echo PROJECTPATH; ?>/assets/ckeditor/config-admin.js'   });
		}
		
		$("#finish_date").datepicker({
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

	
	
	function delete_file(file_id,file_name){
		swal({
			title: "",
			text: "ท่านต้องการลบไฟล์ใช่หรือไม่?",
			type: "warning",
			showCancelButton: true,
			confirmButtonColor: '#DD6B55',
			confirmButtonText: 'ยืนยัน',
			cancelButtonText: "ยกเลิก",
			closeOnConfirm: true,
			closeOnCancel: true
		},
		function(isConfirm) {
			if (isConfirm) {
				$.ajax({
					method: 'POST',
					url: base_url+'/report_problem_data/delete_file',
					data: { problem_file_id : file_id, problem_file_name: file_name},
					success: function(msg){
						$('#file_'+file_id).hide();
					}
				});
			} else {
				
			}
		});
	}
</script>