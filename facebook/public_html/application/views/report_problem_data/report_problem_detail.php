<div class="layout-content">
    <div class="layout-content-body">
		<style>
			.control_label_detail{
				margin-bottom: 0;
				padding-top: 7px;
			}
		</style>
		<?php 
			$mysqli_upbean = new mysqli("report.upbean.co.th", "upbean_report", "aPY9rD3wL");
			$mysqli_upbean->select_db("upbean_report");
			$mysqli_upbean->set_charset("utf8");
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
						<form class="form-horizontal" id="frm_problem" action="?" method="post"  enctype="multipart/form-data" >
							<input type="hidden" name="problem_id" value="<?php echo @$row['problem_id']; ?>"/>
							<div class="form-group">
								<label for="problem_title" class="col-sm-3 control-label">วันที่แจ้ง</label>
								<div class="col-sm-9 control_label_detail">
									<?php echo $this->center_function->ConvertToThaiDate(@$row['create_date']); ?>
								</div>
							</div>
							<div class="form-group">
								<label for="problem_title" class="col-sm-3 control-label">หัวข้อ</label>
								<div class="col-sm-9 control_label_detail">
									<?php echo $row['problem_title']; ?>
								</div>
							</div>
							<div class="form-group">
								<label for="problem_description" class="col-sm-3 control-label">รายละเอียด</label>
								<div class="col-sm-9 control_label_detail">
									<?php echo @$row['problem_description']; ?>
								</div>
							</div>
							<div class="form-group">
								<label for="problem_file" class="col-sm-3 control-label">แนบไฟล์</label>
								<div class="col-sm-9">
									<div style="padding-top:10px" >
											<div class="row" style="">
											<?php 
												if(!empty($rs_file)){
													foreach(@$rs_file as $key => $row_file){ 
														$file_type = explode('/',@$row_file['problem_file_type']);
														$file_type = @$file_type[0];
														//echo $file_type;
												?>
														<div class="col-xs-2 text-center" id="<?php echo "file_".@$row_file['problem_file_id']; ?>">
															<a class="fancybox" href="<?php echo base_url(PROJECTPATH.'/assets/uploads/report_problem/'.@$row_file['problem_file_name']) ?>" target="_blank" >
																<?php if(@$file_type == 'image'){ ?>
																	<img class="img-responsive img-thumbnail" src="<?php echo base_url(PROJECTPATH.'/timthumb.php') ?>?src=<?php echo base_url(PROJECTPATH.'/assets/uploads/report_problem/'.@$row_file['problem_file_name']) ?>&w=300&h=300" style="margin:auto" />
																<?php }else{ ?>
																	<img class="img-responsive img-thumbnail" src="<?php echo base_url(PROJECTPATH.'/timthumb.php') ?>?src=<?php echo base_url(PROJECTPATH.'/assets/images/video_default.png') ?>&w=300&h=300" style="margin:auto" />
																<?php } ?>
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
								<div class="col-sm-9 control_label_detail">
									<?php 
										$problem_priority = array('1'=>'ปกติ','2'=>'เร่งด่วน','3'=>'เร่งด่วนมาก');
										echo @$problem_priority[@$row['problem_priority']];
									?>
								</div>
							</div>
							<div class="form-group">
								<label for="finish_date" class="col-sm-3 control-label">กำหนดเสร็จ</label>
								<div class="col-sm-3 control_label_detail">
									<?php echo $this->center_function->ConvertToThaiDate(@$row['finish_date'],'1','0'); ?>
								</div>
							</div>
							
							<div class="form-group">
								<label for="finish_date" class="col-sm-3 control-label">ผู้แจ้ง</label>
								<div class="col-sm-3 control_label_detail">
									<?php echo @$row_user['user_name']; ?>
								</div>
							</div>
							<div class="form-group">
								<label for="finish_date" class="col-sm-3 control-label">อีเมล์</label>
								<div class="col-sm-3 control_label_detail">
									<?php echo @$row_user['user_email']; ?>
								</div>
							</div>
							<div class="form-group">
								<label for="finish_date" class="col-sm-3 control-label">เบอร์โทร</label>
								<div class="col-sm-3 control_label_detail">
									<?php echo @$row_user['user_tel']; ?>
								</div>
							</div>
							<?php 
								$sql_upbean = "SELECT * FROM report_problem WHERE problem_id = '".$row['problem_id']."' AND coop_name = 'freetradecoop'";
								$rs_upbean = $mysqli_upbean->query($sql_upbean);
								$row_upbean = $rs_upbean->fetch_assoc();
								$problem_status = array('0'=>'เจ้าหน้าที่กำลังแก้ไข','1'=>'แก้ไขเสร็จสิ้น');
							?>
							<div class="form-group">
								<label for="finish_date" class="col-sm-3 control-label">สถานะ</label>
								<div class="col-sm-3 control_label_detail">
									<?php echo @$problem_status[@$row_upbean['problem_status']]; ?>
								</div>
							</div>
							<div class="form-group">
								<label for="finish_date" class="col-sm-3 control-label">รายละเอียด</label>
								<div class="col-sm-3 control_label_detail">
									<?php echo @$row_upbean['fix_detail']; ?>
								</div>
							</div>
							<div class="form-group">
								<label for="finish_date" class="col-sm-3 control-label"></label>
								<div class="col-sm-9 control_label_detail">
									<div style="padding-top:10px" >
											<div class="row" style="">
											<?php 
												$sql_file = "SELECT * FROM report_problem_fix_file WHERE problem_id = '".$row['problem_id']."' AND coop_name = 'freetradecoop' ORDER BY problem_fix_file_id ASC";
												$rs_file = $mysqli_upbean->query($sql_file);
												echo $mysqli_upbean->error ;
												while($row_file = $rs_file->fetch_assoc()){
													$file_type = explode('/',$row_file['problem_fix_file_type']);
													$file_type = $file_type[0];
													//echo $file_type;
											?>
													<div class="col-xs-2 text-center" id="<?php echo "file_".@$row_file['problem_fix_file_id']; ?>">
														<a class="fancybox" href="<?php echo @$row_file["problem_fix_file_path"].@$row_file["problem_fix_file_name"] ?>" target="_blank" >
															<?php if(@$file_type == 'image'){ ?>
																<img class="img-responsive img-thumbnail" src="<?php echo base_url(PROJECTPATH.'/timthumb.php') ?>?src=<?php echo base_url(PROJECTPATH.'/assets/uploads/report_problem/'.@$row_file["problem_fix_file_path"].@$row_file["problem_fix_file_name"]) ?>&w=300&h=300" style="margin:auto" />																
															<?php }else{ ?>
																<img class="img-responsive img-thumbnail" src="<?php echo base_url(PROJECTPATH.'/timthumb.php') ?>?src=<?php echo base_url(PROJECTPATH.'/assets/images/video_default.png') ?>&w=300&h=300" style="margin:auto" />
															<?php } ?>
														</a>
													</div>
												
											<?php 	
												}
											?>
											</div>
										</div>
								</div>
							</div>
							  
							<div class="form-group">
								<div class="col-sm-offset-3 col-sm-10">
									<a class="btn btn-danger" style="margin-left:5px;" href="<?php echo base_url(PROJECTPATH.'/report_problem_data/report_problem');?>">ย้อนกลับ</a>
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<script>
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
					url: '',
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
