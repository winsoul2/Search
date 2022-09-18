<div class="layout-content">
    <div class="layout-content-body">
		<h1>ข้อมูลสหกรณ์</h1>
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 m-b-2">
		<?php $this->load->view('breadcrumb'); ?>
		</div>

		<div class="row gutter-xs">
			<div class="col-xs-12 col-md-12">		  
				<form class="form form-horizontal" action="<?php echo base_url(PROJECTPATH.'/setting_basic_data/coop_detail_save'); ?>" method="post" enctype="multipart/form-data">
				<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
						  
						  <div class="row">
							
						  <div class="form-group col-sm-6">
							<h4 class="m-f-3 m-b-2">ข้อมูลสหกรณ์</h4>
							<label class="col-sm-5 control-label" for="form-control-1">ชื่อสหกรณ์ (ภาษาไทย)</label>
							<div class="col-sm-7">
							  <input id="form-control-1" name="coop_name_th" class="form-control" type="text" value="<?php echo @$row['coop_name_th'] ?>">
							  <input name="profile_id" type="hidden" value="<?php echo @$row['profile_id'] ?>">

							</div>
						  </div>


						  <div class="form-group col-sm-6">
							<h4 class="m-f-3 m-b-1">รูปภาพ</h4>
							<label class="col-sm-3 control-label" for="form-control-9">รูปภาพ</label>
							<div class="col-sm-9" style="margin-top: 6px;">
								<label class="fileContainer btn btn-info">
									<span class="icon icon-paperclip"></span> 
									เลือกไฟล์
									<input id="form-control-9" type="file" name="coop_img" onchange="loadFile(event)">
								</label>
							  
							<img style="position: absolute; max-height: 175px;" class="img-responsive m-t-1" src="<?php echo base_url(PROJECTPATH.'/assets/images/coop_profile/'.@$row['coop_img']) ?>"  id="output"/>
							</div>
						  </div>

						  </div>

						  <div class="row">
						  <div class="form-group col-sm-6 col-sm-offset-6">
							<label class="col-sm-5 control-label" for="form-control-2">ชื่อสหกรณ์ (ภาษาอังกฤษ)</label>
							<div class="col-sm-7">
							  <input id="form-control-2" name="coop_name_en" class="form-control" type="text" value="<?php echo @$row['coop_name_en'] ?>">
							</div>
						  </div>
						  </div>
						  
						  <div class="row">
						  <div class="form-group col-sm-6 col-sm-offset-6">
							<label class="col-sm-5 control-label" for="form-control-2">ชื่อสหกรณ์ย่อ (ภาษาอังกฤษ)</label>
							<div class="col-sm-7">
							  <input id="form-control-2" name="coop_short_name_en" class="form-control" type="text" value="<?php echo @$row['coop_short_name_en'] ?>">
							</div>
						  </div>
						  </div>

						  <div class="row">
						  <div class="form-group col-sm-6 col-sm-offset-6">
						   <label class="col-sm-5 control-label" for="form-control-2">ที่อยู่บรรทัดที่ 1</label>
							<div class="col-sm-7">
							  <input id="form-control-2" name="address1" class="form-control" type="text" value="<?php echo @$row['address1'] ?>">
							</div>
						  </div>
						  </div>
		  
						  <div class="row">
						  <div class="form-group col-sm-6 col-sm-offset-6">
						  <label class="col-sm-5 control-label" for="form-control-2">ที่อยู่บรรทัดที่ 2</label>
							<div class="col-sm-7">
							  <input id="form-control-2" name="address2" class="form-control" type="text" value="<?php echo @$row['address2'] ?>">
							</div>
						  </div>
						  </div>

						  <div class="row">
						  <div class="form-group col-sm-6 col-sm-offset-6">
						  <label class="col-sm-5 control-label" for="form-control-2">เบอร์โทรศัพท์</label>
							<div class="col-sm-7">
							  <input id="form-control-2" name="tel" class="form-control" type="text" value="<?php echo @$row['tel'] ?>">
							</div>
						  </div>
						  </div>
		  
						  <div class="row">
						  <div class="form-group col-sm-6 col-sm-offset-6">
						  <label class="col-sm-5 control-label" for="form-control-2">เบอร์โทรสาร</label>
							<div class="col-sm-7">
							  <input id="form-control-2" name="fax" class="form-control" type="text" value="<?php echo @$row['fax'] ?>">
							</div>
						  </div>
						  </div>

						  <div class="row">
						  <div class="form-group col-sm-6 col-sm-offset-6">
						  <label class="col-sm-5 control-label" for="form-control-2">อีเมล์สหกรณ์</label>
							<div class="col-sm-7">
							  <input id="form-control-2" name="email" class="form-control" type="text" value="<?php echo @$row['email'] ?>">
							</div>
						  </div>
						  </div>
						<!--
						  <div class="row">

						   <div class="form-group m-t-3 col-sm-6">
						  <h4 class="m-f-3">ผู้มีอำนาจลงนาม</h4>
						  <label class="col-sm-5 control-label" for="form-control-2">ชื่อประธาน</label>
							<div class="col-sm-7">
							  <input id="form-control-2" name="president_name" class="form-control" type="text" value="<?php echo @$row['president_name'] ?>">
							</div>
						  </div>

						   <div class="form-group m-t-3 col-sm-6">
							<h4 class="m-f-3 m-b-1">ลายเซ็นต์</h4>
							<label class="col-sm-3 control-label" for="form-control-9">ลายเซ็นต์</label>
							<div class="col-sm-9" style="margin-top: 6px;"">
							  <input id="form-control-9" type="file" name="signature_1" accept="image/*" multiple="" onchange="loadFile1(event)">
							<img style="max-height: 100px;" class="img-responsive" src="<?php echo base_url(PROJECTPATH.'/assets/images/coop_profile/'.@$row['signature_1']) ?>" id="output1"/>
							</div>
						  </div>

						  </div>

						  <div class="row">

						  <div class="form-group col-sm-6">
						  <label class="col-sm-5 control-label" for="form-control-2">ชื่อผู้จัดการ</label>
							<div class="col-sm-7">
							  <input id="form-control-2" name="manager_name" class="form-control" type="text" value="<?php echo @$row['manager_name'] ?>">
							</div>
						  </div>

							<div class="form-group col-sm-6">
							<label class="col-sm-3 control-label" for="form-control-9">ลายเซ็นต์</label>
							<div class="col-sm-9" style="margin-top: 6px;"">
							  <input id="form-control-9" type="file" name="signature_2" accept="image/*" multiple="" onchange="loadFile2(event)">
							<img style="max-height: 100px;" class="img-responsive" src="<?php echo base_url(PROJECTPATH.'/assets/images/coop_profile/'.@$row['signature_2']) ?>" id="output2"/>
							</div>
						  </div>

						  </div>


						  <div class="row">
						  <div class="form-group col-sm-6  m-t-1 col-sm-offset-6">
						  <h4 class="m-f-3">ชื่อผู้ตรวจสอบบัญชี</h4>
						  <label class="col-sm-5 control-label" for="form-control-2">ชื่อผู้ตรวจสอบบัญชี</label>
							<div class="col-sm-7">
							  <input id="form-control-2" name="auditor_name" class="form-control" type="text" value="<?php echo @$row['auditor_name'] ?>">
							</div>
						  </div>
						  </div>

						  <div class="row">
						  <div class="form-group m-t-1 col-sm-6 col-sm-offset-6">
						  <h4 class="m-f-3">ผู้รับรองเอกสาร</h4>
						  <label class="col-sm-5 control-label" for="form-control-2">ผู้รับรองเอกสาร</label>
							<div class="col-sm-7">
							  <input id="form-control-2" name="auditor_name" class="form-control" type="text" value="<?php echo @$row['auditor_name'] ?>">
							</div>
						  </div>
						  </div>
						  -->
				  
				</div>

						
					   <div class="form-group text-center m-t-3 col-xs-12 col-sm-12 col-md-12 col-lg-12">
							<button type="submit" class="btn btn-primary min-width-100">ตกลง</button>
						  </div>      

						</form>
			</div>
		 
		</div>
	</div>
</div>

<script>
  var loadFile = function(event) {
    var output = document.getElementById('output');
    output.src = URL.createObjectURL(event.target.files[0]);
  };

  var loadFile1 = function(event) {
    var output = document.getElementById('output1');
    output.src = URL.createObjectURL(event.target.files[0]);
  };

  var loadFile2 = function(event) {
    var output = document.getElementById('output2');
    output.src = URL.createObjectURL(event.target.files[0]);
  };
</script>