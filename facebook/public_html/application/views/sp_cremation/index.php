<div class="layout-content">
    <div class="layout-content-body">
        <style>
            input[type=number]::-webkit-inner-spin-button,
            input[type=number]::-webkit-outer-spin-button {
                -webkit-appearance: none;
                margin: 0;
            }
            th, td {
                text-align: center;
            }
            .modal-dialog-delete {
                margin:0 auto;
                width: 350px;
                margin-top: 8%;
            }
            .modal-dialog-account {
                margin:auto;
                width: 70%;
                margin-top:7%;
            }
            .control-label {
                text-align:right;
                padding-top:5px;
            }
            .text_left {
                text-align:left;
            }
            .text_right {
                text-align:right;
            }
			.display_none {
				display: none;
			}
        </style>
        <h1 style="margin-bottom: 0">ข้อมูลสมาชิก</h1>
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 padding-l-r-0">
            <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6 padding-l-r-0">
                <?php $this->load->view('breadcrumb'); ?>
            </div>
            <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 padding-l-r-0">
                <button class="btn btn-primary btn-lg bt-add" type="button" id="add_btn">
                    <span class="icon icon-plus-circle"></span>
                    เพิ่มคำร้อง
                </button>
            </div>
        </div>

        <div class="row gutter-xs">
            <div class="col-xs-12 col-md-12">
                <div class="panel panel-body">
                    <form action="<?php echo base_url(PROJECTPATH.'/sp_cremation/'.$path.'/save_register_request'); ?>" id="form1" method="POST" target="">
                    	<div class="form-group g24-col-sm-24">
							<label class="g24-col-sm-6 control-label" for="cremation_status">สถานะ</label>
							<div class="g24-col-sm-4">
								<div class="form-group">
									<?php
										$cremation_status = "";
										if($data['cremation_status'] === '0') {
											$cremation_status = "รออนุมัติ";
										} else if ($data['cremation_status'] == 1) {
											$cremation_status = "อนุมัติ";
										} else if ($data['cremation_status'] == 2) {
											$cremation_status = "ขอยกเลิก";
										} else if ($data['cremation_status'] == 3) {
											$cremation_status = "อนุมัติยกเลิก";
										} else if ($data['cremation_status'] == 4) {
											$cremation_status = "ชำระเงินแล้ว";
										} else if ($data['cremation_status'] == 5) {
											$cremation_status = "ไม่อนุมัติ";
										} else if ($data['cremation_status'] == 6) {
											$cremation_status = "ชำระเงินค่าสมัคร";
										} else if ($data['cremation_status'] == 7) {
											$cremation_status = "ขอรับเงิน";
										} else if ($data['cremation_status'] == 8) {
											$cremation_status = "อนุมัติขอรับเงิน";
										} else if ($data['cremation_status'] == 9) {
											$cremation_status = "ลาออก";
										} else if ($data['cremation_status'] == 10) {
											$cremation_status = "ขอลาออก";
										} else if ($data['cremation_status'] == 11) {
											$cremation_status = "ให้ออก";
										}
									?>
									<input id="cremation_status" class="form-control m-b-1" type="text" value="<?php echo $cremation_status; ?>" readonly>
								</div>
							</div>
							<label class="g24-col-sm-3 control-label" for="approve_date" id="approve_date_label">วันที่อนุมัติ</label>
							<div class="g24-col-sm-4">
								<div class="form-group">
									<input id="approve_date" name="approve_date" class="form-control m-b-1" type="text" value="<?php echo @$data['approve_date']; ?>" readonly>
								</div>
							</div>
						</div>
						<div class="form-group  g24-col-sm-24">
							<label class="g24-col-sm-6 control-label right">เลขที่คำร้อง</label>
							<div class="g24-col-sm-4">
								<div class="form-group">
									<div class="input-group">
										<input id="cremation_no" name="cremation_no" class="form-control cremation_no"  style="text-align:left;" type="text" value="<?php echo $data["cremation_no"]; ?>">
										<span class="input-group-btn">
											<a data-toggle="modal" id="cremation_no_modal_btn" class="fancybox_share fancybox.iframe" href="#">
												<button id="" type="button" class="btn btn-info btn-search"><span class="icon icon-search"></span></button>
											</a>
										</span>
									</div>
								</div>
							</div>
							<label class="g24-col-sm-3 control-label right">วันที่ยื่นคำร้อง</label>
							<div class="g24-col-sm-4">
								<div class="form-group req-date-div">
									<input id="request_date" name="request_date" class="form-control m-b-1 mydate" style="padding-left: 50px;" type="text" value="" data-date-language="th-th">
									<input id="request_date_read" name="request_date_read" class="form-control m-b-1 display_none" style="padding-left: 50px;" type="text" value="" readonly>
									<span class="icon icon-calendar input-icon m-f-1"></span>
								</div>
							</div>
						</div>
						<div class="form-group g24-col-sm-24">
							<label class="g24-col-sm-6 control-label">เลขฌาปนกิจสงเคราะห์</label>
                            <div class="g24-col-sm-4">
								<div class="form-group">
									<div class="input-group">
										<input id="member_cremation_id_input" class="form-control" style="text-align:left;" type="text" value="<?php echo $data['member_cremation_id']; ?>"/>
										<input id="member_cremation_id" name="member_cremation_id" type="hidden" value="<?php echo $data['member_cremation_id']; ?>"/>
										<input id="cremation_request_id" name="cremation_request_id" type="hidden" value="<?php echo $data['cremation_request_id']; ?>"/>
										<span class="input-group-btn">
											<a data-toggle="modal" id="member_cremation_id_modal_btn" class="fancybox_share fancybox.iframe" href="#">
												<button id="" type="button" class="btn btn-info btn-search"><span class="icon icon-search"></span></button>
											</a>
										</span>
									</div>
								</div>
                            </div>
                            <label class="g24-col-sm-3 control-label">รหัสสมาชิก</label>
                            <div class="g24-col-sm-4">
								<div class="form-group">
									<div class="input-group">
										<input id="member_id_input" class="form-control" style="text-align:left;" type="number" value="<?php echo $data['member_id']?>" onkeypress="check_member_id();" />
										<input type="hidden" id="member_id" name="member_id" value="<?php echo $data['member_id']?>"/>
										<span class="input-group-btn">
											<a data-toggle="modal" id="member_id_modal_btn" class="fancybox_share fancybox.iframe" href="#">
												<button id="" type="button" class="btn btn-info btn-search"><span class="icon icon-search"></span></button>
											</a>
										</span>	
									</div>
								</div>
                            </div>
                        </div>
						<div class="form-group g24-col-sm-24">
							<label class="g24-col-sm-6 control-label">รอบสมัคร</label>
							<div class="g24-col-sm-4">
								<div class="form-group">
									<select id="period_id" name="period_id" class="form-control" title="">
										<option value=""></option>
                                        <?php foreach($register_periods as $key => $value) { ?>
                                            <option value="<?php echo $value["id"]; ?>"<?php if($value["id"] == @$data["period_id"]) { ?> selected="selected"<?php } ?>><?php echo $value["name"]; ?></option>
                                        <?php } ?>
                                    </select>
								</div>
                            </div>
						</div>
						<div class="form-group g24-col-sm-24">
							<label class="g24-col-sm-6 control-label">คำนำหน้า</label>
                            <div class="g24-col-sm-4">
								<div class="form-group">
									<select id="prename_id" name="prename_id" class="form-control cre-input" title="กรุณาเลือก คำนำหน้า">
										<option value=""></option>
                                        <?php foreach($prenames as $key => $value) { ?>
                                            <option value="<?php echo $value["prename_id"]; ?>"<?php if($value["prename_id"] == @$data["prename_id"]) { ?> selected="selected"<?php } ?>><?php echo $value["prename_full"]; ?></option>
                                        <?php } ?>
                                    </select>
								</div>
                            </div>
						</div>
						<div class="form-group g24-col-sm-24">
							<label class="g24-col-sm-6 control-label">ชื่อ</label>
                            <div class="g24-col-sm-4">
								<div class="form-group">
									<input id="firstname" name="firstname_th" class="form-control cre-input" style="text-align:left;" type="text" value="<?php echo $data['assoc_firstname']; ?>"/>
								</div>
                            </div>
                            <label class="g24-col-sm-3 control-label">นามสกุล</label>
                            <div class="g24-col-sm-4">
								<div class="form-group">
									<input id="lastname" name="lastname_th" class="form-control cre-input" style="text-align:left;" type="text" value="<?php echo $data['assoc_lastname']?>"/>
								</div>
                            </div>
						</div>
						<div class="form-group g24-col-sm-24">
							<label class="g24-col-sm-6 control-label">วันเกิด</label>
                            <div class="g24-col-sm-4">
								<div class="form-group">
									<input id="birthday" name="birthday" class="form-control m-b-1 mydate cre-input" data-mask="00/00/0000" style="padding-left: 40px;" type="text" value="<?php echo $this->center_function->mydate2date(@$data['assoc_birthday']); ?>" data-date-language="th-th"  title="กรุณาเลือก วันเกิด" maxlength="10">
								</div>
                            </div>
                            <label class="g24-col-sm-3 control-label">อายุ</label>
                            <div class="g24-col-sm-4">
								<div class="form-group">
									<input id="age" name="age" class="form-control" style="text-align:left;" type="text" value="" readonly/>
								</div>
                            </div>
						</div>
						<div class="form-group g24-col-sm-24">
							<label class="g24-col-sm-6 control-label">เลขที่บัตรประจำตัวประชาชน</label>
                            <div class="g24-col-sm-4">
								<div class="form-group">
								<input id="personal_id" name="id_card" class="form-control cre-input" style="text-align:left;" type="text" value="<?php echo $data['id_card']; ?>"/>
								</div>
                            </div>
                            <label class="g24-col-sm-3 control-label">คู่สมรสชื่อ</label>
                            <div class="g24-col-sm-4">
								<div class="form-group">
									<input id="marry_name" name="marry_name" class="form-control cre-input" style="text-align:left;" type="text" value="<?php echo $data["marry_name"]?>"/>
								</div>
                            </div>
						</div>
                        <div class="form-group g24-col-sm-24">
							<label class="g24-col-sm-6 control-label">ตำแหน่ง</label>
                            <div class="g24-col-sm-4">
								<div class="form-group">
                                    <input id="position" name="position" class="form-control cre-input" style="text-align:left;" type="text" value="<?php echo $data["position"]?>"/>
								</div>
                            </div>
                            <label class="g24-col-sm-3 control-label">โทรศัพท์ที่ทำงาน</label>
                            <div class="g24-col-sm-4">
								<div class="form-group">
									<input id="office_tel" name="office_tel" class="form-control cre-input" style="text-align:left;" type="text" value="<?php echo $data["office_phone"]?>"/>
								</div>
                            </div>
						</div>

						<div class="form-group g24-col-sm-24">
							<label class="g24-col-sm-6 control-label" for="department">หน่วยงานหลัก</label>
							<div class="g24-col-sm-4">
								<div class="form-group">
									<select class="form-control m-b-1 cre-input" name="department" id="department" required title="กรุณาเลือกหน่วยงานหลัก" onchange="change_mem_group('department', 'faction')" style="pointer-events: none;"> 
										<option value="">เลือกข้อมูล</option>
										<?php
										foreach($departments as $key => $value){ ?>
											<option value="<?php echo $value['id']; ?>" <?php echo @$data['department']==$value['id']?'selected':''; ?>><?php echo $value['mem_group_name']; ?></option>
										<?php } ?>
									</select>
								</div>
							</div>

							<label class="g24-col-sm-3 control-label" for="faction">ฝ่าย</label>
							<div class="g24-col-sm-4">
								<div class="form-group" id="faction_space">
									<select class="form-control m-b-1 cre-input" name="faction" id="faction" required title="กรุณาเลือกอำเภอ" onchange="change_mem_group('faction','level')" style="pointer-events: none;">
										<option value="">เลือกข้อมูล</option>
										<?php foreach($factions as $key => $value){ ?>
												<option value="<?php echo $value['id']; ?>" <?php echo @$data['faction']==$value['id']?'selected':'';?>><?php echo $value['mem_group_name']; ?></option>
										<?php } ?>
									</select>
								</div>
							</div>
						</div>

						<div class="form-group g24-col-sm-24">
							<label class="g24-col-sm-6 control-label" for="level">สังกัด</label>
							<div class="g24-col-sm-4">
								<div class="form-group" id="level_space">
									<select class="form-control m-b-1 cre-input" name="level" id="level" required title="กรุณาเลือกหน่วยงานย่อย" style="pointer-events: none;">
										<option value="">เลือกข้อมูล</option>
										<?php foreach($levels as $key => $value){ ?>
											<option value="<?php echo $value['id']; ?>" <?php echo @$data['level']==$value['id']?'selected':'';?>><?php echo $value['mem_group_name']; ?></option>
										<?php } ?>
									</select>
								</div>
							</div>
						</div>

						<div class="form-group g24-col-sm-24">
							<label class="g24-col-sm-6 control-label">ที่อยู่ตามทะเบียนบ้าน</label>
						</div>
						<div class="form-group g24-col-sm-24">
							<label class="g24-col-sm-6 control-label" for="address_no">เลขที่</label>
							<div class="g24-col-sm-4">
								<div class="form-group">
									<input id="address_no" name="address_no" class="form-control cre-input" type="text" value="<?php echo @$data['addr_no']; ?>"  title="กรุณาป้อน เลขที่อยู่ตามทะเบียนบ้าน">
								</div>
							</div>
							<label class="g24-col-sm-3 control-label" for="address_moo" >หมู่</label>
							<div class="g24-col-sm-4">
								<div class="form-group">
									<input id="address_moo" name="address_moo" class="form-control cre-input" type="text" value="<?php echo @$data['addr_moo']; ?>">
								</div>
							</div>
						</div>
						<div class="form-group g24-col-sm-24">
							<label class="g24-col-sm-6 control-label" for="address_village">หมู่บ้าน</label>
							<div class="g24-col-sm-4">
								<div class="form-group">
									<input id="address_village" name="address_village" class="form-control cre-input" type="text" value="<?php echo @$data['addr_village']; ?>">
								</div>
							</div>
							<label class="g24-col-sm-3 control-label" for="address_soi">ซอย</label>
							<div class="g24-col-sm-4">
								<div class="form-group">
									<input id="address_soi" name="address_soi" class="form-control cre-input" type="text" value="<?php echo @$data['addr_soi']; ?>">
								</div>
							</div>
						</div>
						<div class="form-group g24-col-sm-24">
							<label class="g24-col-sm-6 control-label" for="address_road">ถนน</label>
							<div class="g24-col-sm-4">
								<div class="form-group">
									<input id="address_road" name="address_road" class="form-control cre-input" type="text" value="<?php echo @$data['addr_street']; ?>">
								</div>
							</div>
							<label class="g24-col-sm-3 control-label" for="province_id">จังหวัด</label>
							<div class="g24-col-sm-4">
								<div class="form-group">
									<select name="province_id" id="province_id" class="form-control cre-input" onchange="change_province('province_id','amphure','amphur_id','district','district_id')">
										<option value=""></option>
										<?php foreach($provinces as $key => $value){ ?>
											<option value="<?php echo $value['province_id']; ?>"<?php echo $value['province_id']==@$data['province_id']?'selected':''; ?>><?php echo $value['province_name']; ?></option>
										<?php }?>
									</select>
								</div>
							</div>
						</div>
						<div class="form-group g24-col-sm-24">
							<label class="g24-col-sm-6 control-label" for="amphur_id">อำเภอ</label>
							<div class="g24-col-sm-4">
								<div class="form-group">
									<span id="amphure">
										<select name="amphur_id" id="amphur_id" class="form-control m-b-1 cre-input" onchange="change_amphur('amphur_id','district','district_id')">
											<option value=""></option>
											<?php foreach($amphurs as $key => $value){ ?>
												<option value="<?php echo $value['amphur_id']; ?>"<?php echo $value['amphur_id']==@$data['amphur_id']?'selected':''; ?>><?php echo $value['amphur_name']; ?></option>
											<?php }?>
										</select>
									</span>
								</div>
							</div>
							<label class="g24-col-sm-3 control-label" for="district_id">ตำบล</label>
							<div class="g24-col-sm-4">
								<div class="form-group">
									<span id="district">
										<select name="district_id" id="district_id" class="form-control m-b-1 cre-input">
											<option value=""></option>
											<?php foreach($districts as $key => $value){ ?>
												<option value="<?php echo $value['district_id']; ?>"<?php echo $value['district_id']==@$data['district_id']?'selected':''; ?>><?php echo $value['district_name']; ?></option>
											<?php }?>
										</select>
									</span>
								</div>
							</div>
						</div>
						<div class="form-group g24-col-sm-24">
							<label class="g24-col-sm-6 control-label" for="zipcode">รหัสไปรษณีย์</label>
							<div class="g24-col-sm-4">
								<div class="form-group">
									<input id="zipcode" name="zipcode" class="form-control m-b-1 cre-input" type="text" value="<?php echo @$data['zip_code']; ?>">
								</div>
							</div>
						</div>
						<div class="form-group g24-col-sm-24">
							<label class="g24-col-sm-6 control-label">ที่อยู่ปัจจุบันที่สามารถติดต่อไป</label>
						</div>
						<div class="form-group g24-col-sm-24">
							<label class="g24-col-sm-6 control-label" for="c_address_no" id="c_address_no_label">เลขที่</label>
							<div class="g24-col-sm-4" id="address_no_con">
								<div class="form-group">
									<input id="c_address_no" name="c_address_no" class="form-control m-b-1 cre-input" type="text" value="<?php echo @$data['cur_addr_no']; ?>"  title="กรุณาป้อน เลขที่อยู่ปัจจุบัน">
								</div>
							</div>
							<label class="g24-col-sm-3 control-label" for="c_address_moo">หมู่</label>
							<div class="g24-col-sm-4">
								<div class="form-group">
									<input id="c_address_moo" name="c_address_moo" class="form-control m-b-1 cre-input" type="text" value="<?php echo @$data['cur_addr_moo']; ?>">
								</div>
							</div>
						</div>
						<div class="form-group g24-col-sm-24">
							<label class="g24-col-sm-6 control-label" for="c_address_village">หมู่บ้าน</label>
							<div class="g24-col-sm-4">
								<div class="form-group">
									<input id="c_address_village" name="c_address_village" class="form-control m-b-1 cre-input" type="text" value="<?php echo @$data['cur_addr_village']; ?>">
								</div>
							</div>
							<label class="g24-col-sm-3 control-label" for="c_address_soi">ซอย</label>
							<div class="g24-col-sm-4">
								<div class="form-group">
									<input id="c_address_soi" name="c_address_soi" class="form-control m-b-1 cre-input" type="text" value="<?php echo @$data['cur_addr_soi']; ?>">
								</div>
							</div>
						</div>
						<div class="form-group g24-col-sm-24">
							<label class="g24-col-sm-6 control-label" for="c_address_road">ถนน</label>
							<div class="g24-col-sm-4">
								<div class="form-group">
									<input id="c_address_road" name="c_address_road" class="form-control m-b-1 cre-input" type="text" value="<?php echo @$data['cur_addr_street']; ?>">
								</div>
							</div>
							<label class="g24-col-sm-3 control-label" for="c_province_id" id="c_province_id_label">จังหวัด</label>
							<div class="g24-col-sm-4" id="province_con">
								<div class="form-group">
									<select name="c_province_id" id="c_province_id" class="form-control m-b-1 cre-input" onchange="change_province('c_province_id','c_amphure','c_amphur_id','c_district','c_district_id')">
										<option value=""></option>
										<?php foreach($provinces as $key => $value){ ?>
											<option value="<?php echo $value['province_id']; ?>"<?php echo $value['province_id']==@$data['cur_province_id']?'selected':''; ?>><?php echo $value['province_name']; ?></option>
										<?php }?>
									</select>
								</div>
							</div>
						</div>
						<div class="form-group g24-col-sm-24">
							<label class="g24-col-sm-6 control-label" for="c_amphur_id" id="c_amphur_id_label">อำเภอ</label>
							<div class="g24-col-sm-4">
								<div class="form-group" id="amphure_con">
									<span id="c_amphure">
										<select name="c_amphur_id" id="c_amphur_id" class="form-control m-b-1 cre-input" onchange="change_amphur('c_amphur_id','c_district','c_district_id')">
											<option value=""></option>
											<?php foreach($amphurs as $key => $value){ ?>
												<option value="<?php echo $value['amphur_id']; ?>"<?php echo $value['amphur_id']==@$data['cur_amphur_id']?'selected':''; ?>><?php echo $value['amphur_name']; ?></option>
											<?php }?>
										</select>
									</span>
								</div>
							</div>
							<label class="g24-col-sm-3 control-label" for="c_district_id" id="c_district_id_label">ตำบล</label>
							<div class="g24-col-sm-4">
								<div class="form-group"  id="district_con">
									<span id="c_district">
										<select name="c_district_id" id="c_district_id" class="form-control m-b-1 cre-input">
											<option value=""></option>
											<?php foreach($districts as $key => $value){ ?>
												<option value="<?php echo $value['district_id']; ?>"<?php echo $value['district_id']==@$data['cur_district_id']?'selected':''; ?>><?php echo $value['district_name']; ?></option>
											<?php }?>
										</select>
									</span>
								</div>
							</div>
						</div>
						<div class="form-group g24-col-sm-24">
							<label class="g24-col-sm-6 control-label" for="c_zipcode">รหัสไปรษณีย์</label>
							<div class="g24-col-sm-4">
								<div class="form-group">
									<input id="c_zipcode" name="c_zipcode" class="form-control m-b-1 cre-input" type="text" value="<?php echo @$data['cur_zip_code']; ?>">
								</div>
							</div>
						</div>
                        <div class="form-group g24-col-sm-24">
							<label class="g24-col-sm-6 control-label" for="">ผู้จัดการศพ</label>
						</div>
						<div class="form-group g24-col-sm-24">
							<label class="g24-col-sm-6 control-label" for="">คำนำหน้าชื่อ</label>
							<div class="g24-col-sm-4">
								<div class="form-group">
									<select id="funeral_manage_profile_id" name="funeral_manage_profile_id" class="form-control" title="กรุณาเลือก คำนำหน้า">
										<option value=""></option>
                                        <?php foreach($prenames as $key => $value) { ?>
                                            <option value="<?php echo $value["prename_id"]; ?>"<?php if($value["prename_id"] == @$data["funeral_manage_profile_id"]) { ?> selected="selected"<?php } ?>><?php echo $value["prename_full"]; ?></option>
                                        <?php } ?>
                                    </select>
								</div>
							</div>
						</div>
						<div class="form-group g24-col-sm-24">
							<label class="g24-col-sm-6 control-label" for="">ชื่อ</label>
							<div class="g24-col-sm-4">
								<div class="form-group">
									<input id="funeral_manage_firstname" name="funeral_manage_firstname" class="form-control m-b-1" type="text" value="<?php echo @$data['funeral_manage_firstname']; ?>">
								</div>
							</div>
							<label class="g24-col-sm-3 control-label" for="">นามสกุล</label>
							<div class="g24-col-sm-4">
								<div class="form-group">
									<input id="funeral_manage_lastname" name="funeral_manage_lastname" class="form-control m-b-1" type="text" value="<?php echo @$data['funeral_manage_lastname']; ?>">
								</div>
							</div>
						</div>
						<div class="form-group g24-col-sm-24">
							<label class="g24-col-sm-6 control-label" for="relate_1" id="relate_1_label">เกี่ยวข้องเป็น</label>
							<div class="g24-col-sm-4">
								<div class="form-group">
									<span id="relation">
										<select name="funeral_manage_relate" id="funeral_manage_relate" class="form-control m-b-1">
											<option value=""></option>
											<?php foreach($member_relations as $value) { ?>
												<option value="<?php echo $value['relation_id']; ?>"<?php echo $value['relation_id']==@$data['funeral_manage_relate']?'selected':''; ?>><?php echo $value['relation_name']; ?></option>
											<?php }?>
										</select>
									</span>
								</div>
							</div>
						</div>
                        <div class="form-group g24-col-sm-24">
							<label class="g24-col-sm-6 control-label">ที่อยู่</label>
						</div>
						<div class="form-group g24-col-sm-24">
							<label class="g24-col-sm-6 control-label" for="funeral_manage_address_no" id="funeral_manage_address_no_label">เลขที่</label>
							<div class="g24-col-sm-4" id="address_no_con">
								<div class="form-group">
									<input id="funeral_manage_address_no" name="funeral_manage_address_no" class="form-control m-b-1" type="text" value="<?php echo @$data['funeral_manage_addr_no']; ?>"  title="กรุณาป้อน เลขที่อยู่ปัจจุบัน">
								</div>
							</div>
							<label class="g24-col-sm-3 control-label" for="funeral_manage_address_moo">หมู่</label>
							<div class="g24-col-sm-4">
								<div class="form-group">
									<input id="funeral_manage_address_moo" name="funeral_manage_address_moo" class="form-control m-b-1" type="text" value="<?php echo @$data['funeral_manage_addr_moo']; ?>">
								</div>
							</div>
						</div>
						<div class="form-group g24-col-sm-24">
							<label class="g24-col-sm-6 control-label" for="funeral_manage_address_village">หมู่บ้าน</label>
							<div class="g24-col-sm-4">
								<div class="form-group">
									<input id="funeral_manage_address_village" name="funeral_manage_address_village" class="form-control m-b-1" type="text" value="<?php echo @$data['funeral_manage_addr_village']; ?>">
								</div>
							</div>
							<label class="g24-col-sm-3 control-label" for="funeral_manage_address_soi">ซอย</label>
							<div class="g24-col-sm-4">
								<div class="form-group">
									<input id="funeral_manage_address_soi" name="funeral_manage_address_soi" class="form-control m-b-1" type="text" value="<?php echo @$data['funeral_manage_addr_soi']; ?>">
								</div>
							</div>
						</div>
						<div class="form-group g24-col-sm-24">
							<label class="g24-col-sm-6 control-label" for="funeral_manage_address_road">ถนน</label>
							<div class="g24-col-sm-4">
								<div class="form-group">
									<input id="funeral_manage_address_road" name="funeral_manage_address_road" class="form-control m-b-1" type="text" value="<?php echo @$data['funeral_manage_addr_street']; ?>">
								</div>
							</div>
							<label class="g24-col-sm-3 control-label" for="funeral_manage_province_id" id="funeral_manage_province_id_label">จังหวัด</label>
							<div class="g24-col-sm-4" id="province_con">
								<div class="form-group">
									<select name="funeral_manage_province_id" id="funeral_manage_province_id" class="form-control m-b-1" onchange="change_province('funeral_manage_province_id','funeral_manage_amphure','funeral_manage_amphur_id','funeral_manage_district','funeral_manage_district_id')">
										<option value="">เลือกจังหวัด</option>
										<?php foreach($provinces as $key => $value){ ?>
											<option value="<?php echo $value['province_id']; ?>"<?php echo $value['province_id']==@$data['funeral_manage_province_id']?'selected':''; ?>><?php echo $value['province_name']; ?></option>
										<?php }?>
									</select>
								</div>
							</div>
						</div>
						<div class="form-group g24-col-sm-24">
							<label class="g24-col-sm-6 control-label" for="funeral_manage_amphur_id" id="funeral_manage_amphur_id_label">อำเภอ</label>
							<div class="g24-col-sm-4">
								<div class="form-group" id="amphure_con">
									<span id="funeral_manage_amphure">
										<select name="funeral_manage_amphur_id" id="funeral_manage_amphur_id" class="form-control m-b-1" onchange="change_amphur('funeral_manage_amphur_id','funeral_manage_district','funeral_manage_district_id')">
											<option value="">เลือกอำเภอ</option>
											<?php foreach($amphurs as $key => $value){ ?>
												<option value="<?php echo $value['amphur_id']; ?>"<?php echo $value['amphur_id']==@$data['funeral_manage_amphur_id']?'selected':''; ?>><?php echo $value['amphur_name']; ?></option>
											<?php }?>
										</select>
									</span>
								</div>
							</div>
							<label class="g24-col-sm-3 control-label" for="funeral_manage_district_id" id="funeral_manage_district_id_label">ตำบล</label>
							<div class="g24-col-sm-4">
								<div class="form-group"  id="district_con">
									<span id="funeral_manage_district">
										<select name="funeral_manage_district_id" id="funeral_manage_district_id" class="form-control m-b-1">
											<option value="">เลือกตำบล</option>
											<?php foreach($districts as $key => $value){ ?>
												<option value="<?php echo $value['district_id']; ?>"<?php echo $value['district_id']==@$data['funeral_manage_district_id']?'selected':''; ?>><?php echo $value['district_name']; ?></option>
											<?php }?>
										</select>
									</span>
								</div>
							</div>
						</div>
						<div class="form-group g24-col-sm-24">
							<label class="g24-col-sm-6 control-label" for="funeral_manage_zipcode">รหัสไปรษณีย์</label>
							<div class="g24-col-sm-4">
								<div class="form-group">
									<input id="funeral_manage_zipcode" name="funeral_manage_zipcode" class="form-control m-b-1" type="text" value="<?php echo @$data['funeral_manage_zip_code']; ?>">
								</div>
							</div>
							<label class="g24-col-sm-3 control-label" for="funeral_manage_district_id">โทรศัพท์</label>
							<div class="g24-col-sm-4">
								<div class="form-group">
									<input id="funeral_manage_phone" name="funeral_manage_phone" class="form-control m-b-1" type="text" value="<?php echo @$data['funeral_manage_phone']; ?>">
								</div>
							</div>
						</div>
						<div class="form-group g24-col-sm-24">
							<label class="g24-col-sm-6 control-label"></label>
							<div class="g24-col-sm-4">
								<div class="form-group">
									<input id="submit_btn" class="btn btn-primary" type="button" value="บันทึก">
								</div>
							</div>
						</div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="member-search-modal" role="dialog">
	<div class="modal-dialog">
		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">ข้อมูลสมาชิก</h4>
			</div>
			<div class="modal-body">
				<div class="input-with-icon">
					<div class="row">
						<div class="col">
							<label class="col-sm-2 control-label">รูปแบบค้นหา</label>
							<div class="col-sm-4">
								<div class="form-group">
									<select id="search_list" name="search_list" class="form-control m-b-1">
										<option value="">เลือกรูปแบบค้นหา</option>
										<option value="member_id">รหัสสมาชิก</option>
										<option value="id_card">หมายเลขบัตรประชาชน</option>
										<option value="firstname_th">ชื่อสมาชิก</option>
										<option value="lastname_th">นามสกุล</option>
									</select>
								</div>
							</div>
							<label class="col-sm-1 control-label" style="white-space: nowrap;"> ค้นหา </label>
							<div class="col-sm-4">
								<div class="form-group">
									<div class="input-group">
										<input id="search_text" name="search_text" class="form-control m-b-1" type="text" value="">
										<span class="input-group-btn">
											<button type="button" id="member_search" class="btn btn-info btn-search"><span class="icon icon-search"></span></button>
										</span>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="bs-example" data-example-id="striped-table">
					<table class="table table-striped">
						<tbody id="table_data">
						</tbody>
					</table>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" id="close" class="btn btn-default" data-dismiss="modal">ปิดหน้าต่าง</button>
			</div>
		</div>
	</div>
</div>
<div class="modal fade" id="cremation-search-modal" role="dialog">
	<div class="modal-dialog" style="width: 80%;">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">ฌาปนกิจสงเคราะห์</h4>
			</div>
			<div class="modal-body">
				<div class="input-with-icon">
					<div class="row">
						<div class="col">
							<label class="col-sm-2 control-label">รูปแบบค้นหา</label>
							<div class="col-sm-4">
								<div class="form-group">
									<select id="cre_search_list" name="search_list" class="form-control m-b-1">
										<option value="">เลือกรูปแบบค้นหา</option>
										<option value="cremation_no">เลขที่คำร้อง</option>
										<option value="member_cremation_id">เลขฌาปนกิจสงเคราะห์</option>
										<option value="member_id">รหัสสมาชิก</option>
										<option value="id_card">หมายเลขบัตรประชาชน</option>
										<option value="firstname_th">ชื่อสมาชิก</option>
									</select>
								</div>
							</div>
							<label class="col-sm-1 control-label" style="white-space: nowrap;"> ค้นหา </label>
							<div class="col-sm-4">
								<div class="form-group">
									<div class="input-group">
										<input id="cre_search_text" name="search_text" class="form-control m-b-1" type="text" value="">
										<span class="input-group-btn">
											<button type="button" id="cremation_search" class="btn btn-info btn-search"><span class="icon icon-search"></span></button>
										</span>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="bs-example" data-example-id="striped-table">
					<table class="table table-striped">
						<thead>
							<th class="text-center">เลขที่คำร้อง</th>
							<th class="text-center">เลขฌาปนกิจสงเคราะห์</th>
							<th class="text-center">รหัสสมาชิก</th>
							<th class="text-center">ชื่อสมาชิก</th>
							<th></th>
						</thead>
						<tbody id="cre-table_data">
						</tbody>
					</table>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" id="close" class="btn btn-default" data-dismiss="modal">ปิดหน้าต่าง</button>
			</div>
		</div>
	</div>
</div>
<input type="hidden" id="modal-type" value=""/>
<script>
    $(document).ready(function() {
		$(".mydate").datepicker({
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
        $('.cre-input').prop('readonly', true);
        $('.cre-input').attr("style", "pointer-events: none;");
        if($("#birthday").val()) {
			var today = new Date();
			var birthday = $("#birthday").val().split("/");
			age = today.getFullYear() + 543 - birthday[2];
			if((today.getMonth() + 1) > birthday[1]) {
				age--;
			} else if ((today.getMonth() + 1) == birthday[1] && today.getDate() < birthday[0]) {
				age--;
			}
			$("#age").val(age);
        }
        $('#member_search').click(function(){
			if($('#search_list').val() == '') {
				swal('กรุณาเลือกรูปแบบค้นหา','','warning');
			} else if ($('#search_text').val() == ''){
				swal('กรุณากรอกข้อมูลที่ต้องการค้นหา','','warning');
			} else {
				$.ajax({
					url: base_url+"ajax/search_member_by_type_jquery",
					method:"post",
					data: {
						search_text : $('#search_text').val(), 
						search_list : $('#search_list').val()
					},  
					dataType:"text",
					success:function(data) {
						$('#table_data').html(data);
					},
					error: function(xhr){
						console.log('Request Status: ' + xhr.status + ' Status Text: ' + xhr.statusText + ' ' + xhr.responseText);
					}
				});
			}
		});
		$("#cremation_search").click(function() {
			if($('#cre_search_list').val() == '') {
				swal('กรุณาเลือกรูปแบบค้นหา','','warning');
			} else if ($('#cre_search_text').val() == ''){
				swal('กรุณากรอกข้อมูลที่ต้องการค้นหา','','warning');
			} else {
				is_member = $("#modal-type").val() == '2' ? 1 : 0;
				$.ajax({
					url: base_url+"sp_cremation/<?php echo $path;?>/search_cremation_by_type_jquery",
					method:"post",
					data: {
						search_text : $('#cre_search_text').val(), 
						search_list : $('#cre_search_list').val(),
						is_member : is_member
					},
					dataType:"text",
					success:function(data) {
						$('#cre-table_data').html(data);
					},
					error: function(xhr){
						console.log('Request Status: ' + xhr.status + ' Status Text: ' + xhr.statusText + ' ' + xhr.responseText);
					}
				});
			}
		});

		$("#birthday").change(function() {
			var today = new Date();
			var birthday = $(this).val().split("/");
			age = today.getFullYear() + 543 - birthday[2];
			if((today.getMonth() + 1) > birthday[1]) {
				age--;
			} else if ((today.getMonth() + 1) == birthday[1] && today.getDate() < birthday[0]) {
				age--;
			}
			$("#age").val(age);
		});

		$("#member_cremation_id_input").keypress(function() {
			var keycode = (event.keyCode ? event.keyCode : event.which);
			if(keycode == '13'){
				var member_cremation_id = $('#member_cremation_id_input').val();
				$.post(base_url+"cremation/check_member_cremation_id?id="+member_cremation_id, 
				function(result) {
					obj = JSON.parse(result);
					if (obj) {
						id = obj.id;
						get_cremation_info(id,2);
					} else {
						swal('ไม่พบเลขฌาปนกิจสงเคราะห์ท่านเลือก','','warning'); 
					}
				});
			}
		});

		$("#cremation_no").keypress(function() {
			var keycode = (event.keyCode ? event.keyCode : event.which);
			if(keycode == '13'){
				var cremation_no = $(this).val();
				get_cremation_info(cremation_no, 3);
			}
        });
		$("#member_id_modal_btn").click(function() {
			$("#modal-member-id-type").val("main");
			$('#member-search-modal').modal('toggle');
		});
		$("#cremetion_member_id_modal_btn").click(function() {
			$("#modal-member-id-type").val("ref");
			$('#member-search-modal').modal('toggle');
		});
		$("#cremetion_member_id_input").keypress(function() {
			var keycode = (event.keyCode ? event.keyCode : event.which);
			if(keycode == '13'){
				var member_id = $(this).val();
				$.post(base_url+"cremation/check_member_id",
				{
				member_id: member_id
				}
				, function(result) {
					obj = JSON.parse(result);
					mem_id = obj.member_id;
					if(mem_id != undefined){
						$("#modal-member-id-type").val("ref");
						get_data(mem_id, null, null);
					}else{
						swal('ไม่พบรหัสสมาชิกที่ท่านเลือก','','warning'); 
					}
				});
			}
		});

        $(document).on('click','.cre-modal-btn',function(){
			if($("#modal-type").val()== '1') {
				get_cremation_info($(this).attr("data-member-cremation-raw-id"),4);
				$("#cremation-search-modal").modal("hide");
			} else if ($("#modal-type").val()== '2') {
				get_member_relate_data($(this).attr("data-member-cremation-id"),2)
				$('#cremation-search-modal').modal('hide');
			}
        });

		$("#member_cremation_id_modal_btn").click(function() {
			$("#modal-type").val(1);
			$('#cremation-search-modal').modal('toggle');
		});

		$("#relate_member_id_modal_btn").click(function() {
			$("#modal-type").val(2);
			$('#cremation-search-modal').modal('toggle');
        });
		
		<?php
			if(!empty($data["id"])) {
		?>
			get_cremation_info(<?php echo $data["id"]?>,5);
		<?php
			}
		?>

		$("#cremation_no_modal_btn").click(function() {
			$("#modal-type").val(1);
			$('#cremation-search-modal').modal('toggle');
		});

		$("#submit_btn").click(function() {
			$.blockUI({
				message: 'กรุณารอสักครู่...',
				css: {
					border: 'none',
					padding: '15px',
					backgroundColor: '#000',
					'-webkit-border-radius': '10px',
					'-moz-border-radius': '10px',
					opacity: .5,
					color: '#fff'
				},
				baseZ: 5000,
				bindEvents: false
			});
			wraning_message = "";
			if($("#period_id").val() == '') {
				wraning_message += " - กรุณาเลือกรอบการสมัคร\n";
			}
			if($("#member_id").val() == '') {
				wraning_message += " - กรุณาเลือกเลือกรหัสสมาชิกหรือเลขฌาปนกิจสงเคราะห์\n";
			}
			
			if(wraning_message == "") {
				$.post(base_url+"sp_cremation/<?php echo $path;?>/ajax_save_register_request",
				$("#form1").serialize()
				, function(result) {
					data = JSON.parse(result);
					register_id = data.register_id;
					if(register_id != undefined){
						get_cremation_info(register_id, 5);
						$.unblockUI();
						swal('บันทึกข้อมูลเรียบร้อยแล้ว','','success');
					}else{
						$.unblockUI();
						swal('ทำรายการไม่ถูกต้องกรุณาลองอีกครั้ง','','warning');
					}
				});
			} else {
				swal('ไม่สามารถทำรายการได้', wraning_message,'warning');
				$.unblockUI();
			}
		});

		$("#add_btn").click(function() {
			document.location.href = base_url + "sp_cremation/<?php echo $path;?>/";
		});
    });

    function format_the_number_decimal(ele){
        var value = $('#'+ele.id).val();
        value = value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');
        var num = value.split(".");
        var decimal = '';
        var num_decimal = '';
        if(typeof num[1] !== 'undefined'){
            if(num[1].length > 2){
                num_decimal = num[1].substring(0, 2);
            }else{
                num_decimal =  num[1];
            }
            decimal =  "."+num_decimal;
        }
        if(value!=''){
            if(value == 'NaN'){
                $('#'+ele.id).val('');
            }else{
                value = (num[0] == '')?0:parseInt(num[0]);
                value = value.toLocaleString()+decimal;
                $('#'+ele.id).val(value);
            }
        }else{
            $('#'+ele.id).val('');
        }
    }

    
	function check_member_id() {
		var member_id = $('#member_id_input').val();
		var keycode = (event.keyCode ? event.keyCode : event.which);
		if(keycode == '13'){
			$.post(base_url+"cremation/check_member_id",
			{
			member_id: member_id
			}
			, function(result) {
				obj = JSON.parse(result);
				if(obj.id != undefined) {
					get_cremation_info(obj.id,1);
				} else {
					mem_id = obj.member_id;
					if(mem_id != undefined){
						$("#modal-member-id-type").val("main");
						get_data(mem_id, null, null);
					}else{
						swal('ไม่พบรหัสสมาชิกที่ท่านเลือก','','warning');
					}
				}
			});
		}
	}

	function get_data(id, name, data_row) {
		get_cremation_info(id, 1);
		$("#member-search-modal").modal("hide");
	}

	function get_member_relate_data(id) {
		$.ajax({
			method: 'GET',
			url: base_url+'cremation/get_cremation_info?member_cremation_id='+id,
			success: function(result){
				data = JSON.parse(result);
				$("#cremetion_member_id").val(data.member_id);
				$("#cremetion_member_id_input").val(data.member_id);
				$("#relate-member-name").val(data.prename_full + data.assoc_firstname + " " + data.assoc_lastname);
				$("#relate_member_id").val(data.member_cremation_id);
				$("#relate_member_id_hide").val(data.member_cremation_id);
			}
		});
	}

	function change_province(id, id_to, id_input_amphur, district_space, id_input_district){
		var province_id = $('#'+id).val();
		$.ajax({
			method: 'POST',
			url: base_url+'manage_member_share/get_amphur_list',
			data: {
				province_id : province_id,
				id_input_amphur : id_input_amphur,
				district_space : district_space,
				id_input_district : id_input_district
			},
			success: function(msg){
				$('#'+id_to).html(msg.replace(`class="form-control `, `class="form-control cre-input `));
			}
		});
	}

	function change_amphur(id, id_to, id_input_district){
		var amphur_id = $('#'+id).val();
		$.ajax({
			method: 'POST',
			url: base_url+'manage_member_share/get_district_list',
			data: {
				amphur_id : amphur_id,
				id_input_district : id_input_district
			},
			success: function(msg){
				$('#'+id_to).html(msg.replace(`class="form-control `, `class="form-control cre-input `));
			}
		});
	}

	function address_change(province_id, amphur_id, dustrict_id, type) {
		if(province_id == "" || province_id == null) {
			$('#'+type+"province_id").val("")
		} else {
			$('#'+type+"province_id").html($('#'+type+"province_id").html().replace(`"`+province_id+`"`, `"`+province_id+`" selected`));
		}

		mem_type_id = $('input[name=type]:checked').val();
		member_id = $("#member_id").val();
		$.ajax({
			method: 'POST',
			url: base_url+'manage_member_share/get_amphur_list',
			data: {
				province_id : province_id,
				id_input_amphur : type+"amphur_id",
				district_space : "district",
				id_input_district : type+"district_id"
			},
			success: function(msg){
				$('#'+type+"amphure").html(msg.replace(`"`+amphur_id+`"`, `"`+amphur_id+`" selected`).replace(`class="form-control `, `class="form-control cre-input `));
				if(member_id != "") {
					$('#'+type+"amphure").attr("style", "pointer-events: none;");
				} else {
					$('#'+type+"amphure").attr("style", "pointer-events: unset;");
				}
			}
		});
		$.ajax({
			method: 'POST',
			url: base_url+'manage_member_share/get_district_list',
			data: {
				amphur_id : amphur_id,
				id_input_district : type+"district_id"
			},
			success: function(msg){
				$('#'+type+"district").html(msg.replace(`"`+dustrict_id+`"`, `"`+dustrict_id+`" selected`).replace(`class="form-control `, `class="form-control cre-input `));
				if(member_id != "") {
					$('#'+type+"district").attr("style", "pointer-events: none;");
				} else {
					$('#'+type+"district").attr("style", "pointer-events: unset;");
				}
			}
		});
	}

    //type = 1 search by member_id
    //type = 2 search by cremation_member_id
    //type = 3 search by cremation_registration_id
    //type = 4 search by Raw cremation member id  coop_sp_cremation_member.id
    //type = 5 search by Raw register id  coop_sp_cremation_registration.id
	function get_cremation_info(id, type) {
		$.blockUI({
            message: 'กรุณารอสักครู่...',
            css: {
                border: 'none',
                padding: '15px',
                backgroundColor: '#000',
                '-webkit-border-radius': '10px',
                '-moz-border-radius': '10px',
                opacity: .5,
                color: '#fff'
            },
            baseZ: 2000,
            bindEvents: false
		});

		var parameter = {};
		if(type == 1) {
			parameter = {member_id: id};
		} else if(type == 2) {
			parameter = {cremation_member_id: id};
		} else if(type == 3) {
			parameter = {registration_id: id};
		} else if(type == 4) {
			parameter = {cremation_member_raw_id: id};
		}else if(type == 5) {
			parameter = {cremation_register_raw_id: id};
		}

		$.post(base_url+"sp_cremation/<?php echo $path;?>/get_cremation_member_info"
		, parameter
        , function(result) {
			data = $.parseJSON(result);
			status = "";
			if(data.member_status) {
				// 1=เป็นสมาชิก,2=ขอลาออก,3=ลาออก,4=ขอรับเงินฌาปนกิจ,5=เสียชีวิต
				status = data.member_status == 1 ? "อนุมัติ" :
							(data.member_status == 2 ? "ขอลาออก" : 
							(data.member_status == 3 ? "ลาออก" :
							(data.member_status == 4 ? "ขอรับเงินฌาปนกิจ" :
							(data.member_status == 5 ? "เสียชีวิต" : ""))));
			} else if(data.register_status) {
				status = data.register_status == 1 ? "รออนุมัติ" : "";
			}
			$("#cremation_status").val(status);
			$("#prename_id").val(data.prename_id);
			$("#firstname").val(data.firstname_th);
			if(data.approve_date) {
				$.ajax({
					method: "GET",
					url: base_url+'cremation/get_convert_to_thai_date?date='+data.approve_date,
					success: function(result){
						$("#approve_date").val(result);
					}
				});
			}
			if(data.request_date) {
				req_date_arr = data.request_date.split(" ")[0].split("-");
				$("#request_date").val(req_date_arr[2]+"/"+req_date_arr[1]+"/"+(parseInt(req_date_arr[0]) + 543));
				$("#request_date_read").val(req_date_arr[2]+"/"+req_date_arr[1]+"/"+(parseInt(req_date_arr[0]) + 543));
				if(data.register_status && data.register_status == 1) {
					$("#request_date").removeClass('display_none');
					$("#request_date_read").addClass('display_none');
				} else {
					$("#request_date").addClass('display_none');
					$("#request_date_read").removeClass('display_none');
				}
			} else {
				$("#request_date").removeClass('display_none');
				$("#request_date_read").addClass('display_none');
			}

			if(data.birthday) {
				$.ajax({
					method: "GET",
					url: base_url+'cremation/get_convert_to_thai_date?date='+data.birthday,
					success: function(result){
						$("#birthday").val(result);
					}
				});

				birthday_arr = data.birthday.split("-");
				year = parseInt(birthday_arr[0])+543;
				var today = new Date();
				age = today.getFullYear() + 543 - year;
				if((today.getMonth() + 1) > birthday_arr[1]) {
					age--;
				} else if ((today.getMonth() + 1) == birthday_arr[1] && today.getDate() < birthday_arr[2]) {
					age--;
				}
				$("#age").val(age);
			}

			$("#cremation_no").val(data.request_id);
			$("#member_cremation_id_input").val(data.cremation_member_id);
			$("#member_cremation_id").val(data.cremation_member_raw_id);
			$("#cremation_request_id").val(data.id);
			$("#member_id_input").val(data.member_id);
			$("#member_id").val(data.member_id);
			$("#prename_id").val(data.prename_id);
			$("#firstname").val(data.firstname_th);
			$("#lastname").val(data.lastname_th);
			$("#personal_id").val(data.id_card);
			$("#marry_name").val(data.marry_name);
			$("#position").val(data.position);
			$("#office_tel").val(data.office_tel);
			$("#department").val(data.department);
			$("#faction").val(data.faction);
			$("#level").val(data.level);
			$("#period_id").val(data.period_id);

			$("#address_no").val(data.address_no);
			$("#address_moo").val(data.address_moo);
			$("#address_village").val(data.address_village);
			$("#address_soi").val(data.address_soi);
			$("#address_road").val(data.address_road);
			$("#province_id").val(data.province_id);
			$("#amphur_id").val(data.amphur_id);
			$("#district_id").val(data.district_id);
			$("#zipcode").val(data.zipcode);
			
			$("#c_address_no").val(data.c_address_no);
			$("#c_address_moo").val(data.c_address_moo);
			$("#c_address_village").val(data.c_address_village);
			$("#c_address_soi").val(data.c_address_soi);
			$("#c_address_road").val(data.c_address_road);
			$("#c_province_id").val(data.c_province_id);
			$("#c_amphur_id").val(data.c_amphur_id);
			$("#c_district_id").val(data.c_district_id);
			$("#c_zipcode").val(data.c_zipcode);

			$("#funeral_manage_profile_id").val(data.funeral_manager_prename_id);
			$("#funeral_manage_firstname").val(data.funeral_manager_firstname);
			$("#funeral_manage_lastname").val(data.funeral_manager_lastname);
			$("#funeral_manage_relate").val(data.funeral_manager_relate_id);
			$("#funeral_manage_address_no").val(data.funeral_manager_address_no);
			$("#funeral_manage_address_moo").val(data.funeral_manager_address_moo);
			$("#funeral_manage_address_village").val(data.funeral_manager_address_village);
			$("#funeral_manage_address_soi").val(data.funeral_manager_address_soi);
			$("#funeral_manage_address_road").val(data.funeral_manager_address_road);
			$("#funeral_manage_province_id").val(data.funeral_manager_address_province);
			$("#funeral_manage_amphur_id").val(data.funeral_manager_address_amphur);
			$("#funeral_manage_district_id").val(data.funeral_manager_address_tambol);
			$("#funeral_manage_zipcode").val(data.funeral_manager_zipcode);
			$("#funeral_manage_phone").val(data.funeral_manager_phone_number);

			$.unblockUI();
		});
    }
</script>
