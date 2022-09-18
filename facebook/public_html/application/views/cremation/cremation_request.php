<div class="layout-content">
    <div class="layout-content-body">
		<style>
			.margin-group {
				margin-left: 5px !important;
				margin-right: 5px !important;
			}
			.form-inline .control-label {
				vertical-align: unset;
			}
			.no-horizontal-padding {
				padding-left: 0;
				padding-right: 0;
			}
		</style>

		<h1 style="margin-bottom: 0">ฌาปนกิจสงเคราะห์</h1>
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 padding-l-r-0">
		<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6 padding-l-r-0">
		<?php $this->load->view('breadcrumb'); ?>
		</div>
		<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 " style="padding-right:0px;text-align:right;">
			<a href="?">
				<button name="bt_add" id="bt_add" type="button" class="btn btn-primary btn-lg bt-add">
					<span class="icon icon-plus-circle"></span>
					<span>เพิ่มคำร้อง</span>
				</button>
			</a>
		</div>
		</div>
		<div class="row gutter-xs">
			<div class="col-xs-12 col-md-12">
				<div class="panel panel-body">
					<form data-toggle="validator" method="post" action="" class="g24 form form-horizontal" enctype="multipart/form-data" autocomplete="off" id="myForm">
						<div class="form-group g24-col-sm-24">
							<label class="g24-col-sm-6 control-label right">ประเภทการสมัคร</label>
							<div class="g24-col-sm-6 control-label text-left option-radio">
                                <input class="type-radio cre-input" type="radio" id="type-ordinary" name="type" value="1" <?php echo $data["mem_type_id"] == 1 ? 'checked' : "";?>>
								สามัญ&nbsp&nbsp&nbsp&nbsp
								<input class="type-radio cre-input" type="radio" id="type-assoc" name="type" value="2" <?php echo $data["mem_type_id"] == 2 ? 'checked' : "";?>>
								สมทบ
                            </div>
						</div>
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
								<div class="form-group">
									<input id="request_date" class="form-control request_date" type="text" value="<?php echo !empty($data["createdatetime"]) ? $this->center_function->mydate2date($data["createdatetime"]) : "";?>" readonly>
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
							<label class="g24-col-sm-6 control-label">คำนำหน้า</label>
                            <div class="g24-col-sm-4">
								<div class="form-group">
									<select id="prename_id" name="prename_id" class="form-control cre-input" title="กรุณาเลือก คำนำหน้า">
                                        <?php foreach($prename as $key => $value) { ?>
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
						<div class="form-group g24-col-sm-24 assoc-group-div">
							<label class="g24-col-sm-6 control-label">เป็น</label>
							<div class="g24-col-sm-18 control-label text-left ">
								<input class="type-radio cre-input" type="radio" id="marry_relate" name="relation_type" value="1" <?php echo $data["relation"] == 1 ? 'checked' : "";?> >
								คู่สมรส&nbsp&nbsp&nbsp&nbsp
								<input class="type-radio cre-input" type="radio" id="child_relate" name="relation_type" value="2" <?php echo $data["relation"] == 2 ? 'checked' : "";?> >
								บุตร&nbsp&nbsp&nbsp&nbsp
								<input class="type-radio cre-input" type="radio" id="father_relate" name="relation_type" value="3" <?php echo $data["relation"] == 3 ? 'checked' : "";?> >
								บิดา&nbsp&nbsp&nbsp&nbsp
								<input class="type-radio cre-input" type="radio" id="mother_relate" name="relation_type" value="4" <?php echo $data["relation"] == 4 ? 'checked' : "";?> >
								มารดา
							</div>
						</div>
						<div class="form-group g24-col-sm-24 assoc-group-div form-inline">
							<label class="g24-col-sm-6 control-label"></label>
							<label class="control-label">ซึ่งเป็นสมาชิกฌาปนกิจเลขที่</label>
							<div class="form-group margin-group">
								<div class="input-group">	
									<input id="relate_member_id"  class="form-control cre-input" style="text-align:left;" type="number" value="<?php echo $data['ref_cremation_request_id']?>"/>
									<input id="relate_member_id_hide" name="relate_member_id" type="hidden" value="<?php echo $data['ref_cremation_request_id']?>"/>
									<span class="input-group-btn">
										<a data-toggle="modal" id="relate_member_id_modal_btn" class="fancybox_share fancybox.iframe" href="#">
											<button id="" type="button" class="btn btn-info btn-search"><span class="icon icon-search"></span></button>
										</a>
									</span>	
								</div>
							</div>
							<label class="control-label">รหัสสมาชิกสหกรณ์</label>
							<div class="form-group margin-group">
								<div class="input-group">
									<!-- <input id="cremetion_member_id" name="cremetion_member_id" class="form-control" style="text-align:left;" type="text" value="<?php echo $data['ref_member_id']?>" readonly/> -->
									<input id="cremetion_member_id_input" class="form-control cre-input" style="text-align:left;" type="number" value="<?php echo $data['ref_member_id']?>"/>
									<input type="hidden" id="cremetion_member_id" name="cremetion_member_id" value="<?php echo $data['ref_member_id']?>"/>
									<span class="input-group-btn">
										<a data-toggle="modal" id="cremetion_member_id_modal_btn" class="fancybox_share fancybox.iframe" href="#">
											<button id="" type="button" class="btn btn-info btn-search"><span class="icon icon-search"></span></button>
										</a>
									</span>
								</div>
							</div>
						</div>
						<div class="form-group g24-col-sm-24 assoc-group-div form-inline">
							<label class="g24-col-sm-6 control-label"></label>
							<label class="control-label">ชื่อสกุล</label>	
							<div class="form-group margin-group" style="width:40%">
								<input id="relate-member-name" class="form-control" style="text-align:left; width:100%" type="text" value="<?php echo $data["prename_full_ref"].$data["firstname_ref"]." ".$data["lastname_ref"]?>" readonly/>
							</div>
						</div>
						<div class="form-group g24-col-sm-24 assoc-group-div form-inline">
							<label class="g24-col-sm-6 control-label">และเป็น</label>
							<label class="control-label">สมาชิกสมทบของสหกรณ์ เลขที่สมาชิก</label>
							<div class="form-group  margin-group">
								<input id="associate_member_id" name="associate_member_id" class="form-control" style="text-align:left;" type="text" value="<?php echo $data["mem_type_id"] == 2 ? $data["member_id"] : "";?>" readonly/>
							</div>
						</div>
						<div class="form-group g24-col-sm-24">
							<label class="g24-col-sm-6 control-label">อาชีพผู้สมัคร</label>
                            <div class="g24-col-sm-4">
								<div class="form-group">
								<input id="career" name="career" class="form-control cre-input" style="text-align:left;" type="text" value="<?php echo $data['occupation']; ?>"/>
								</div>
                            </div>
                            <label class="g24-col-sm-3 control-label">ตำแหน่ง</label>
                            <div class="g24-col-sm-4">
								<div class="form-group">
									<input id="position" name="position" class="form-control cre-input" style="text-align:left;" type="text" value="<?php echo $data["position"]?>"/>
								</div>
                            </div>
						</div>
						<div class="form-group g24-col-sm-24">
							<label class="g24-col-sm-6 control-label">สถานที่ทำงาน</label>
                            <div class="g24-col-sm-4">
								<div class="form-group">
								<input id="workplace" name="workplace" class="form-control cre-input" style="text-align:left;" type="text" value="<?php echo $data['workplace']; ?>"/>
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
										<option value="">เลือกจังหวัด</option>
										<?php foreach($province as $key => $value){ ?>
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
											<option value="">เลือกอำเภอ</option>
											<?php foreach($amphur as $key => $value){ ?>
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
											<option value="">เลือกตำบล</option>
											<?php foreach($district as $key => $value){ ?>
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
							<div class="g24-col-sm-10" >
								<input type="checkbox" id="use_same_address"  value="1" class="cre-input"/> ใช้ที่อยู่ตามทะเบียนบ้าน
							</div>
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
										<option value="">เลือกจังหวัด</option>
										<?php foreach($province as $key => $value){ ?>
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
											<option value="">เลือกอำเภอ</option>
											<?php foreach($c_amphur as $key => $value){ ?>
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
											<option value="">เลือกตำบล</option>
											<?php foreach($c_district as $key => $value){ ?>
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
						<div id="bank_account_div">
							<div class="form-group g24-col-sm-24 account-set-0">
								<label class="g24-col-sm-6 control-label" for="">เลขบัญชีสมาชิก</label>
							</div>
							<div class="form-group g24-col-sm-24 account-set-0">
								<label class="g24-col-sm-6 control-label" for="">ธนาคาร</label>
								<div class="g24-col-sm-4 no-horizontal-padding">
									<div class="g24-col-sm-6">
										<div class="form-group">
											<input id="bank_id_show_0" class="form-control m-b-1 group-bank-left" type="text" value="006" readonly>
										</div>
									</div>
									<div class=" g24-col-sm-18">
										<div class="form-group">
											<select id="dividend_bank_id_0" name="dividend_bank_id[]" class="form-control m-b-1 group-bank-right js-data-example-ajax bank-select cre-input" onchange="change_bank(0)">
												<option value="">เลือกธนาคาร</option>
												<?php foreach($bank as $key => $value) {
													if($value["bank_id"]=='006'){
														$selected = "selected";
													}else{
														$selected = "";
													}
												?>
												<option value="<?php echo $value["bank_id"]; ?>" <?php echo @$selected; ?> > <?php echo $value["bank_name"]; ?></option>
												<?php } ?>
											</select>
										</div>
									</div>
								</div>
							</div>
							<div class="form-group g24-col-sm-24 account-set-0">
								<label class="g24-col-sm-6 control-label clearfix" for="">สาขา</label>
								<div class="g24-col-sm-4 no-horizontal-padding">
									<div class="g24-col-sm-6">
										<div class="form-group">
											<input id="branch_id_show_0" class="form-control m-b-1 group-bank-left" type="text" value="<?php echo @$data["dividend_bank_branch_id"]; ?>" readonly>
										</div>
									</div>
									<div class=" g24-col-sm-18">
										<div class="form-group">
											<span id="bank_branch_0">
												<select id="dividend_bank_branch_id_0"  name="dividend_bank_branch_id[]" data-id="0" class="form-control m-b-1 group-bank-right js-data-example-ajax-branch cre-input" onchange="change_branch(0, this)">
													<option value="">เลือกสาขาธนาคาร</option>
													<?php foreach($bank_branch as $key => $value) { ?>
														<option value="<?php echo $value["branch_id"]; ?>" <?php if($value["branch_id"] == @$data["dividend_bank_branch_id"]) { ?> selected="selected"<?php } ?>><?php echo $value["branch_name"]; ?></option>
													<?php } ?>												</select>
											</span>
										</div>
									</div>
								</div>
							</div>
							<div class="form-group g24-col-sm-24 account-set-0">
								<label class="g24-col-sm-6 control-label" for="">เลขที่บัญชี</label>
								<div class=" g24-col-sm-4">
									<div class="form-group">
										<input id="dividend_acc_num_0" class="form-control m-b-1 clear_pay cre-input" name="dividend_acc_num[]"  type="text" value="<?php echo @$data["dividend_acc_num"]; ?>">
									</div>
								</div>
							</div>
						</div>
						<div class="form-group g24-col-sm-24">
							<label class="g24-col-sm-6 control-label" for=""></label>
							<div class="g24-col-sm-4 no-horizontal-padding">
								<button type="button" id="addBank" class="btn btn-primary cre-input"> <span class="icon icon-plus-circle"></span> เพิ่ม</button>
								<button type="button" id="removeBank" class="btn btn-danger cre-input"> <span class="icon icon-minus-circle"></span> ลบ</button>
							</div>
						</div>
						<div class="form-group g24-col-sm-24">
							<label class="g24-col-sm-6 control-label" for="">ผู้รับเงินฌาปนกิจสงเคราะห์</label>
						</div>
						<div class="form-group g24-col-sm-24">
							<label class="g24-col-sm-6 control-label" for="">ลำดับที่ 1.</label>
							<div class="g24-col-sm-4">
								<div class="form-group">
									<input id="receiver_1" name="receiver_1" class="form-control m-b-1" type="text" value="<?php echo @$data['receiver_1']; ?>">
								</div>
							</div>
							<label class="g24-col-sm-3 control-label" for="relate_1" id="relate_1_label">เกี่ยวข้องเป็น</label>
							<div class="g24-col-sm-4">
								<div class="form-group">
									<input id="relate_1" name="relate_1" class="form-control m-b-1" type="text" value="<?php echo @$data['relate_1']; ?>">
								</div>
							</div>
						</div>
						<div class="form-group g24-col-sm-24">
							<label class="g24-col-sm-6 control-label" for="">ลำดับที่ 2.</label>
							<div class="g24-col-sm-4">
								<div class="form-group">
									<input id="receiver_2" name="receiver_2" class="form-control m-b-1" type="text" value="<?php echo @$data['receiver_2']; ?>">
								</div>
							</div>
							<label class="g24-col-sm-3 control-label" for="relate_2" id="relate_2_label">เกี่ยวข้องเป็น</label>
							<div class="g24-col-sm-4">
								<div class="form-group">
									<input id="relate_2" name="relate_2" class="form-control m-b-1" type="text" value="<?php echo @$data['relate_2']; ?>">
								</div>
							</div>
						</div>
						<div class="form-group g24-col-sm-24">
							<label class="g24-col-sm-6 control-label" for="">ลำดับที่ 3.</label>
							<div class="g24-col-sm-4">
								<div class="form-group">
									<input id="receiver_3" name="receiver_3" class="form-control m-b-1" type="text" value="<?php echo @$data['receiver_3']; ?>">
								</div>
							</div>
							<label class="g24-col-sm-3 control-label" for="relate_3" id="relate_3_label">เกี่ยวข้องเป็น</label>
							<div class="g24-col-sm-4">
								<div class="form-group">
									<input id="relate_3" name="relate_3" class="form-control m-b-1" type="text" value="<?php echo @$data['relate_3']; ?>">
								</div>
							</div>
						</div>
						<div class="form-group g24-col-sm-24">
							<label class="g24-col-sm-6 control-label" for="">ลำดับที่ 4.</label>
							<div class="g24-col-sm-4">
								<div class="form-group">
									<input id="receiver_4" name="receiver_4" class="form-control m-b-1" type="text" value="<?php echo @$data['receiver_4']; ?>">
								</div>
							</div>
							<label class="g24-col-sm-3 control-label" for="relate_4" id="relate_4_label">เกี่ยวข้องเป็น</label>
							<div class="g24-col-sm-4">
								<div class="form-group">
									<input id="relate_4" name="relate_4" class="form-control m-b-1" type="text" value="<?php echo @$data['relate_4']; ?>">
								</div>
							</div>
						</div>
						<div class="form-group g24-col-sm-24">
							<label class="g24-col-sm-6 control-label" for="funeral_manager" id="funeral_manager_label">ผู้จัดการศพ</label>
							<div class="g24-col-sm-4">
								<div class="form-group">
									<input id="funeral_manager" name="funeral_manager" class="form-control m-b-1" type="text" value="<?php echo @$data['funeral_manager']; ?>">
								</div>
							</div>
							<label class="g24-col-sm-3 control-label" for="heir_phone">เบอร์โทรของทายาท</label>
							<div class="g24-col-sm-4">
								<div class="form-group">
									<input id="heir_phone" name="heir_phone" class="form-control m-b-1" type="text" value="<?php echo @$data['heir_phone']; ?>">
								</div>
							</div>
						</div>
						<div class="form-group g24-col-sm-24">
							<label class="g24-col-sm-6 control-label" for="cremation_status">เงินสงเคราะห์คงเหลือ</label>
							<div class="g24-col-sm-4">
								<div class="form-group">
									<input id="amount_balance" name="amount_balance" class="form-control m-b-1 text-right" type="text" value="<?php echo @$data['amount_balance']; ?>" readonly>
								</div>
							</div>
						</div>
						<div class="form-group g24-col-sm-24 text-center">
							<button type="button" id="submit-btn" class="btn btn-primary" >บันทึก</button>
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
<input type="hidden" id="modal-member-id-type" value=""/>
<?php

	$link = array(
		'src' => PROJECTJSPATH.'assets/js/zepto.min.js',
		'type' => 'text/javascript'
	);
	echo script_tag($link);
	$link = array(
		'src' => PROJECTJSPATH.'assets/js/jquery.mask.js',
		'type' => 'text/javascript'
	);
	echo script_tag($link);
	$link = array(
		'src' => PROJECTJSPATH.'assets/js/select2.full.js',
		'type' => 'text/javascript'
	);
	echo script_tag($link);
?>
<script>
	$( document ).ready(function() {
		createSelect2();
		if($('input[name=type]:checked').val() == 1) {
			$('.cre-input').prop('readonly', true);
			$('.cre-input').attr("style", "pointer-events: none;");
		}
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
					url: base_url+"cremation/search_cremation_by_type_jquery",
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

		$("#relate_member_id").keypress(function() {
			var keycode = (event.keyCode ? event.keyCode : event.which);
			if(keycode == '13'){
				var member_cremation_id = $('#relate_member_id').val();
				$.post(base_url+"cremation/check_member_cremation_id?id="+member_cremation_id, 
				function(result) {
					obj = JSON.parse(result);
					if (obj) {
						member_cremation_id = obj.member_cremation_id;
						get_member_relate_data(member_cremation_id);
					} else {
						swal('ไม่พบเลขฌาปนกิจสงเคราะห์ท่านเลือก','','warning'); 
					}
				});
			}
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
						get_cremation_info(id);
					} else {
						swal('ไม่พบเลขฌาปนกิจสงเคราะห์ท่านเลือก','','warning'); 
					}
				});

				// get_cremation_request_from_cremation_no
			}
		});

		$("#cremation_no").keypress(function() {
			var keycode = (event.keyCode ? event.keyCode : event.which);
			if(keycode == '13'){
				var cremation_no = $(this).val();
				get_cremation_info_from_cremation_no(cremation_no);
			}
		});

		$("#type-assoc").click(function() {
			$(".assoc-group-div").show();
		});

		$("#type-ordinary").click(function() {
			$(".assoc-group-div").hide();
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

		$("#use_same_address").change(function() {
			if($(this).is(":checked")) {
				$("#c_address_moo").val($("#address_moo").val());
				$("#c_address_no").val($("#address_no").val());
				$("#c_address_road").val($("#address_road").val());
				$("#c_address_soi").val($("#address_soi").val());
				$("#c_address_village").val($("#address_village").val());
				address_change($("#province_id").val(), $("#amphur_id").val(), $("#district_id").val(), "c_");
				$("#c_zipcode").val($("#zipcode").val());
				$('#c_amphure').attr("style", "pointer-events: unset;");
				$('#c_district').attr("style", "pointer-events: unset;");
			}
		});

		$('input[type=radio][name=relation_type]').change(function() {
			if ($(this).val() == '5') {
				$("#associate_member_id").val($("#member_id").val());
			} else {
				$("#associate_member_id").val("");
			}
		});

		$("#submit-btn").click(function() {
			if (!$("input:radio[name='type']").is(":checked")) {
				swal('ไม่สามารถทำรายการได้','กรุณาเลือกประเภทการสมัคร','warning');
				return false;
			}
			if(!$("#cremation_request_id").val()) {
				$.post(base_url+"cremation/creamation_request_validation",
				{
				data: $("#myForm").serialize()
				}
				, function(result) {
					if(result == "success") {
						$("#myForm").submit();
					} else {
						swal('ไม่สามารถทำรายการได้',result,'warning');
					}
				});
			} else {
				if($("#member_cremation_id").val() != "") {
					swal({
						title: 'สมาชิกท่านนี้เป็นสมาชิกฌาปนกิจสงเคราะห์แล้วต้องการแก้ไขข้อมูล',
						text: "",
						type: "warning",
						showCancelButton: true,
						confirmButtonColor: '#0288d1',
						confirmButtonText: 'ยืนยัน',
						cancelButtonText: "ยกเลิก",
						closeOnConfirm: false,
						closeOnCancel: true
					},
					function(isConfirm) {
						if (isConfirm) {
							$("#myForm").submit();
						}
					});
				} else {
					swal({
						title: 'สมาชิกท่านนี้กำลังการอนุมัติเพื่อเป็นสมาชิกฌาปนกิจสงเคราะห์ต้องการแก้ไขข้อมูล',
						text: "",
						type: "warning",
						showCancelButton: true,
						confirmButtonColor: '#0288d1',
						confirmButtonText: 'ยืนยัน',
						cancelButtonText: "ยกเลิก",
						closeOnConfirm: false,
						closeOnCancel: true
					},
					function(isConfirm) {
						if (isConfirm) {
							$("#myForm").submit();
						}
					});
				}
			}
		});
	
		$("#type-ordinary").click(function() {
			$('.cre-input').prop('readonly', true);
			$('.cre-input').attr("style", "pointer-events: none;");
			$("#type-assoc").prop('readonly', false);
			$('#type-assoc').attr("style", "pointer-events: unset;");
		});

		$("#type-assoc").click(function() {
			$('.cre-input').prop('readonly', false);
			$('.cre-input').attr("style", "pointer-events: unset;");
		});

		$(document).on('click','.cre-modal-btn',function(){
			if($("#modal-type").val()== '1') {
				get_cremation_info($(this).attr("data-member-cremation-raw-id"));
			} else if ($("#modal-type").val()== '2') {
				get_member_relate_data($(this).attr("data-member-cremation-id"))
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

		$("#addBank").click(function() {
			add_bank_acc();
		});

		$("#removeBank").click(function() {
			var index = $('.bank-select').length;
			remove_bank_acc(index);
		});

		if("<?php echo $data["id"]?>" != "") {
			get_cremation_info(<?php echo $data["id"]?>);
		}

		$("#cremation_no_modal_btn").click(function() {
			$("#modal-type").val(1);
			$('#cremation-search-modal').modal('toggle');
		});

	});

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
					get_cremation_info(obj.id);
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
		if($("#modal-member-id-type").val() == "main") {
			$.post(base_url+"cremation/check_member_id",
			{
			member_id: id
			}
			, function(result) {
				obj = JSON.parse(result);
				if(obj.id != undefined) {
					get_cremation_info(obj.id);
					$('#member-search-modal').modal('hide');
				} else {
					$.post(base_url+"ajax/get_member",
					{
						member_id: id
					}
					, function(result) {
						console.log(result)
						$("#associate_member_id").val("");
						$("#career").val("");
						$("#position").val("");
						$("#workplace").val("");
						$("#office_tel").val("");
						$('input[type=radio][name=relation_type]').prop("checked", false);
						data = $.parseJSON(result);
						if(data.apply_type_id == 2) {
							$("#type-assoc").prop("checked", true);
						} else {
							$("#type-ordinary").prop("checked", true);
						}
						$('#member-search-modal').modal('hide');
						if(data.prename_id) {
							$("#prename_id").val(data.prename_id).change();
						}
						$("#firstname").val(data.firstname_th);
						$("#lastname").val(data.lastname_th);
						if(data.birthday) {
							birthday_arr = data.birthday.split("-");
							year = parseInt(birthday_arr[0])+543;
							$("#birthday").val(birthday_arr[2]+"/"+birthday_arr[1]+"/"+year);
						}
						$("#personal_id").val(data.id_card);
						$("#marry_name").val(data.marry_name);
						$("#address_moo").val(data.address_moo);
						$("#address_no").val(data.address_no);
						$("#address_road").val(data.address_road);
						$("#address_soi").val(data.address_soi);
						$("#address_village").val(data.address_village);
						address_change(data.province_id, data.amphur_id, data.district_id, "");
						$("#zipcode").val(data.zipcode);
						$("#c_address_moo").val(data.c_address_moo);
						$("#c_address_no").val(data.c_address_no);
						$("#c_address_road").val(data.c_address_road);
						$("#c_address_soi").val(data.c_address_soi);
						$("#c_address_village").val(data.c_address_village);
						address_change(data.c_province_id, data.c_amphur_id, data.c_district_id, "c_");
						$("#c_zipcode").val(data.c_zipcode);
						$("#office_tel").val(data.office_tel);
						if(data.apply_type_id == 2) {
							$("#associate_member_id").val(data.member_id);
						}
						$("#member_id").val(data.member_id);
						$("#member_id_input").val(data.member_id);
						$('.cre-input').prop('readonly', true);
						$('.cre-input').attr("style", "pointer-events: none;");
						$("#member_cremation_id_input").val("");
						$("#member_cremation_id").val("");
						$("#cremation_request_id").val("");
						$("#receiver_1").val("");
						$("#receiver_2").val("");
						$("#receiver_3").val("");
						$("#receiver_4").val("");
						$("#relate_1").val("");
						$("#relate_2").val("");
						$("#relate_3").val("");
						$("#relate_4").val("");
						$("#cremation_status").val("");
						$("#amount_balance").val("");
						$("#cremation_no").val("");
						$("#cremation_no").val("");
						$("#relate_member_id_hide").val("");
						$("#relate_member_id").val("");
						$("#cremetion_member_id").val("");
						$("#cremetion_member_id").val("");
						$("#relate-member-name").val("");
						$("#funeral_manager").val("");
						$("#heir_phone").val("");

						get_member_bank_account();
					});
				}
			});
		} else if ($("#modal-member-id-type").val() == "ref") {
			$.post(base_url+"cremation/check_member_id", { member_id: id }
			, function(result) {
				obj = JSON.parse(result);
				if(!obj.member_cremation_id) {
					swal('สมาชิกท่านนี้ไม่ได้เป็นสมาชิกฌาปนกิจ','','warning');
				} else {
					$.ajax({
						method: 'GET',
						url: base_url+'cremation/get_cremation_info?id='+obj.id,
						success: function(result){
							data = $.parseJSON(result);
							$("#cremetion_member_id").val(data.member_id);
							$("#cremetion_member_id_input").val(data.member_id);
							$("#relate_member_id").val(data.member_cremation_id);
							$("#relate_member_id_hide").val(data.member_cremation_id);
							$("#relate-member-name").val(data.prename_full+data.assoc_firstname+" "+data.assoc_lastname);
						}
					});
				}
			});
		}
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

	function get_cremation_info(id) {
		console.log(id)

		$.ajax({
			method: 'GET',
			url: base_url+'cremation/get_cremation_info?id='+id,
			success: function(result){
				data = $.parseJSON(result);
				console.log(data)
				$("#address_moo").val(data.addr_moo);
				$("#address_no").val(data.addr_no);
				$("#address_road").val(data.addr_street);
				$("#address_soi").val(data.addr_soi);
				$("#address_village").val(data.addr_village);
				$("#amount_balance").val(data.amount_balance);
				if(data.assoc_birthday) {
					birthday_arr = data.assoc_birthday.split("-");
					year = parseInt(birthday_arr[0])+543;
					$("#birthday").val(birthday_arr[2]+"/"+birthday_arr[1]+"/"+year);
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
				$("#c_address_moo").val(data.cur_addr_moo);
				$("#c_address_no").val(data.cur_addr_no);
				$("#c_address_road").val(data.cur_addr_street);
				$("#c_address_soi").val(data.cur_addr_soi);
				$("#c_address_village").val(data.cur_addr_village);
				$("#c_zipcode").val(data.cur_zip_code);
				$("#career").val(data.occupation);
				$("#cremation_no").val(data.cremation_no);
				$("#personal_id").val(data.id_card);
				$("#firstname").val(data.assoc_firstname);
				$("#lastname").val(data.assoc_lastname);
				$("#marry_name").val(data.marry_name);
				$("#member_id").val(data.member_id);
				$("#member_id_input").val(data.member_id);
				$("#office_tel").val(data.office_phone);
				$("#position").val(data.position);
				if(data.ref_member_cremation_id != 0) $("#relate_member_id").val(data.ref_member_cremation_id);
				$("#workplace").val(data.workplace);
				$("#zipcode").val(data.zip_code);
				$("#cremation_request_id").val(data.cremation_request_id);
				$("#member_cremation_id").val(data.member_cremation_id);
				$("#member_cremation_id_input").val(data.member_cremation_id);
				if(data.firstname_ref)$("#relate-member-name").val(data.prename_full_ref + data.firstname_ref + " " + data.lastname_ref);
				$("#relate_member_id").val(data.ref_cremation_request_id);
				$("#relate_member_id_hide").val(data.ref_cremation_request_id);
				$("#cremetion_member_id").val(data.ref_member_id);
				$("#receiver_1").val(data.receiver_1);
				$("#receiver_2").val(data.receiver_2);
				$("#receiver_3").val(data.receiver_3);
				$("#receiver_4").val(data.receiver_4);
				$("#relate_1").val(data.relate_1);
				$("#relate_2").val(data.relate_2);
				$("#relate_3").val(data.relate_3);
				$("#relate_4").val(data.relate_4);
				$("#funeral_manager").val(data.funeral_manager);
				$("#heir_phone").val(data.heir_phone);
				
				if(data.adv_payment_balance) $("#amount_balance").val(format_number(data.adv_payment_balance));

				$.ajax({
					method: "GET",
					url: base_url+'cremation/get_convert_to_thai_date?date='+data.createdatetime,
					success: function(result){
						$("#request_date").val(result);
					}
				});

				$.ajax({
					method: "GET",
					url: base_url+'cremation/get_convert_to_thai_date?date='+data.approve_date,
					success: function(result){
						$("#approve_date").val(result);
					}
				});

				if(data.cremation_status == 0) {
					$("#cremation_status").val("รออนุมัติ");
				} else if (data.cremation_status == 1) {
					$("#cremation_status").val("อนุมัติ");
				} else if (data.cremation_status == 2) {
					$("#cremation_status").val("ขอยกเลิก");
				} else if (data.cremation_status == 3) {
					$("#cremation_status").val("อนุมัติยกเลิก");
				} else if (data.cremation_status == 4) {
					$("#cremation_status").val("ชำระเงินแล้ว");
				} else if (data.cremation_status == 5) {
					$("#cremation_status").val("ไม่อนุมัติ");
				} else if (data.cremation_status == 6) {
					$("#cremation_status").val("ชำระเงินค่าสมัคร");
				} else if (data.cremation_status == 7) {
					$("#cremation_status").val("ขอรับเงิน");
				} else if (data.cremation_status == 8) {
					$("#cremation_status").val("อนุมัติขอรับเงิน");
				} else if (data.cremation_status == 9) {
					$("#cremation_status").val("ลาออก");
				} else if (data.cremation_status == 10) {
					$("#cremation_status").val("ขอลาออก");
				} else if (data.cremation_status == 11) {
					$("#cremation_status").val("ให้ออก");
				}

				if(data.mem_type_id == 1) {
					$("#type-ordinary").prop("checked", true);
				} else if (data.mem_type_id == 2) {
					$("#associate_member_id").val(data.member_id);
					$("#type-assoc").prop("checked", true);
				}

				if(data.relation == 1) {
					$("#marry_relate").prop("checked", true);
				} else if (data.relation == 2) {
					$("#child_relate").prop("checked", true);
				} else if (data.relation == 3) {
					$("father_relate").prop("checked", true);
				} else if (data.relation == 4) {
					$("#mother_relate").prop("checked", true);
				}

				$("#prename_id").val(data.prename_id);
				address_change(data.province_id, data.amphur_id, data.district_id, "");
				address_change(data.cur_province_id, data.cur_amphur_id, data.cur_district_id, "c_");

				if(!(data.mem_type_id == 2 && data.member_id == null)) {
					$('.cre-input').prop('readonly', true);
					$('.cre-input').attr("style", "pointer-events: none;");
				} else {
					$('.cre-input').prop('readonly', false);
					$('.cre-input').attr("style", "pointer-events: unset;");
				}
				$('#cremation-search-modal').modal('hide');
				$("#modal-type").val("");

				$(".add-bank-div").remove();
				if(data.banks.length > 0) {
					for (i = 0; i < data.banks.length; i++) {
						bank_acc_index = i
						if(i > 0) {
							bank_acc_index = i+1;
							add_bank_acc();
						}
						$("#bank_id_show_"+bank_acc_index).val(data.banks[i].dividend_bank_id)
						$("#dividend_bank_id_"+bank_acc_index).val(data.banks[i].dividend_bank_id)
						$("#branch_id_show_"+bank_acc_index).val(data.banks[i].dividend_bank_branch_id)
						$('#dividend_bank_branch_id_'+bank_acc_index).append('<option value="'+data.banks[i].dividend_bank_branch_id+'" selected="selected">'+data.banks[i].branch_name+'</option>');
						$("#dividend_bank_branch_id_"+bank_acc_index).val(data.banks[i].dividend_bank_branch_id)
						$("#dividend_acc_num_"+bank_acc_index).val(data.banks[i].dividend_acc_num)
					}
				} else {
					bank_acc_index = 0;
					$("#bank_id_show_"+bank_acc_index).val("")
					$("#dividend_bank_id_"+bank_acc_index).val("")
					$("#branch_id_show_"+bank_acc_index).val("")
					$('#dividend_bank_branch_id_'+bank_acc_index).html('<option value="">เลือกสาขาธนาคาร</option>');
					$("#dividend_acc_num_"+bank_acc_index).val("")
					change_bank(0)
				}
			}
		});
	}

	function add_bank_acc() {
		var index = $('.bank-select').length + 1;
		var new_html = `<div class="form-group g24-col-sm-24 account-set-`+index+` add-bank-div">
							<label class="g24-col-sm-6 control-label" for="">ธนาคาร</label>
							<div class="g24-col-sm-4 no-horizontal-padding">
								<div class="g24-col-sm-6">
									<div class="form-group">
										<input id="bank_id_show_`+index+`" class="form-control m-b-1 group-bank-left" type="text" value="006" readonly>
									</div>
								</div>
								<div class=" g24-col-sm-18">
									<div class="form-group">
										<select id="dividend_bank_id_`+index+`" name="dividend_bank_id[]" class="form-control m-b-1 group-bank-right js-data-example-ajax bank-select" onchange="change_bank(`+index+`)">
											<option value="">เลือกธนาคาร</option>
											<?php foreach($bank as $key => $value) {
												if($value["bank_id"]=='006'){
													$selected = "selected";
												}else{
													$selected = "";
												}
											?>
											<option value="<?php echo $value["bank_id"]; ?>" <?php echo @$selected; ?> > <?php echo $value["bank_name"]; ?></option>
											<?php } ?>
										</select>
									</div>
								</div>
							</div>
						</div>
						<div class="form-group g24-col-sm-24 account-set-`+index+` add-bank-div">
							<label class="g24-col-sm-6 control-label clearfix" for="">สาขา</label>
							<div class="g24-col-sm-4 no-horizontal-padding">
								<div class="g24-col-sm-6">
									<div class="form-group">
										<input id="branch_id_show_`+index+`" class="form-control m-b-1 group-bank-left" type="text" value="" readonly>
									</div>
								</div>
								<div class=" g24-col-sm-18">
									<div class="form-group">
										<span id="bank_branch_`+index+`">
											<select id="dividend_bank_branch_id_`+index+`"  name="dividend_bank_branch_id[]" data-id="`+index+`" class="form-control m-b-1 group-bank-right js-data-example-ajax-branch" onchange="change_branch(`+index+`, this)">
												<option value="">เลือกสาขาธนาคาร</option>
											</select>
										</span>
									</div>
								</div>
							</div>
						</div>
						<div class="form-group g24-col-sm-24 account-set-`+index+` add-bank-div">
							<label class="g24-col-sm-6 control-label" for="">เลขที่บัญชี</label>
							<div class=" g24-col-sm-4">
								<div class="form-group">
									<input id="dividend_acc_num_`+index+`" class="form-control m-b-1 clear_pay cre-input" name="dividend_acc_num[]"  type="text" value="">
								</div>
							</div>
						</div>`;
		$("#bank_account_div").append(new_html);
		if($("#member_id").val() != "") {
			$('.cre-input').prop('readonly', true);
			$('.cre-input').attr("style", "pointer-events: none;");
		} else {
			$('.cre-input').prop('readonly', false);
			$('.cre-input').attr("style", "pointer-events: unset;");
		}
		createSelect2();
	}

	function remove_bank_acc(index) {
		$(".account-set-"+index).remove();
	}

	function change_bank(index) {
		$("#bank_id_show_"+index).val($("#dividend_bank_id_"+index).val());
		$("#branch_id_show_"+index).val("");
		$("#dividend_bank_branch_id_"+index).val("");
		$("#dividend_acc_num_"+index).val("");
		createSelect2();
	}

    function createSelect2(){
		$('.js-data-example-ajax').select2();
        $('.js-data-example-ajax-branch').select2({
            ajax: {
                url: '<?=base_url("ajax/get_branch_json")?>',
                dataType: 'json',
                data: function (params) {
                    return {
                        q: params.term, // search term
                        bank_id: jQuery("#bank_id_show_"+$(this).attr('data-id') ).val()
                    };
                },
                processResults: function (data) {
                    return {
                        results: data.items
                    };
                }
			}
		});
		if($("#member_id").val() != "") {
			$('.select2').attr("style", "pointer-events: none;");
		} else {
			$('.select2').attr("style", "pointer-events: unset;");
		}
	}

	function change_branch(setHtmlId, val){
		var branch_id = $(val).val();
		$('#branch_id_show_'+setHtmlId).val(branch_id);
		$("#dividend_acc_num_"+setHtmlId).val("");
	}

	function get_cremation_info_from_cremation_no(cremation_no) {
		$.ajax({
			method: 'GET',
			url: base_url+'cremation/get_cremation_request_from_cremation_no?cremation_no='+cremation_no,
			success: function(result){
				data = JSON.parse(result);
				if(data.message == "success") {
					get_cremation_info(data.data.member_cremation_raw_id);
				} else {
					swal(data.message,'','warning');
				}
			}
		});
	}

	function get_member_bank_account() {
		$.ajax({
			method: 'GET',
			url: base_url+'ajax/get_bank_account_by_member_id?member_id='+$("#member_id").val(),
			success: function(result){
				data = JSON.parse(result);
				$(".add-bank-div").remove();
				if(data.length > 0) {
					for (i = 0; i < data.length; i++) {
						bank_acc_index = i
						if(i > 0) {
							bank_acc_index = i+1;
							add_bank_acc();
						}
						$("#bank_id_show_"+bank_acc_index).val(data[i].dividend_bank_id)
						$("#dividend_bank_id_"+bank_acc_index).val(data[i].dividend_bank_id)
						$("#branch_id_show_"+bank_acc_index).val(data[i].dividend_bank_branch_id)
						$('#dividend_bank_branch_id_'+bank_acc_index).append('<option value="'+data[i].dividend_bank_branch_id+'" selected="selected">'+data[i].branch_name+'</option>');
						$("#dividend_bank_branch_id_"+bank_acc_index).val(data[i].dividend_bank_branch_id)
						$("#dividend_acc_num_"+bank_acc_index).val(data[i].dividend_acc_num)
						if($("#member_id").val() != "") {
							$('.select2').attr("style", "pointer-events: none;");
						} else {
							$('.select2').attr("style", "pointer-events: unset;");
						}
					}
				} else {
					bank_acc_index = 0;
					$("#bank_id_show_"+bank_acc_index).val("")
					$("#dividend_bank_id_"+bank_acc_index).val("")
					$("#branch_id_show_"+bank_acc_index).val("")
					$('#dividend_bank_branch_id_'+bank_acc_index).html('<option value="">เลือกสาขาธนาคาร</option>');
					$("#dividend_acc_num_"+bank_acc_index).val("")
					change_bank(0)
					if($("#member_id").val() != "") {
						$('.select2').attr("style", "pointer-events: none;");
					} else {
						$('.select2').attr("style", "pointer-events: unset;");
					}
				}
			}
		});
	}
</script>