<div class="layout-content">
    <div class="layout-content-body">
		<style>
			.bt-add{
				float:none;
			}			
			.control-label{
				text-align:right;
				padding-top: 7px;
			}
			.form-group {
				margin-bottom: 5px;
			}
			.input-with-icon {
				margin-bottom: 5px;
			}
			
			.input-with-icon .form-control{
				padding-left: 40px;
			}
			.m-b-1{
				margin-bottom: 5px;
			}
		</style>

		<h1 style="margin-bottom: 0">สวัสดิการสมาชิก</h1>
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 padding-l-r-0">
		<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6 padding-l-r-0">
		<?php $this->load->view('breadcrumb'); ?>
		</div>
		<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 " style="padding-right:0px;text-align:right;">			   
			<button name="bt_view" id="bt_view" type="button" class="btn btn-primary btn-lg bt-add" onclick="view_request()">
				<span class="icon icon-search"></span>
				<span>ดูสวัสดิการ</span>
			</button>
			
			<button name="bt_add" id="bt_add" type="button" class="btn btn-primary btn-lg bt-add" onclick="add_request()">
				<span class="icon icon-plus-circle"></span>
				<span>เพิ่มคำร้อง</span>
			</button>		   
		</div>
		</div>
		<div class="row gutter-xs">
			<div class="col-xs-12 col-md-12">
				<div class="panel panel-body">
					<form data-toggle="validator" method="post" action="" class="g24 form form-horizontal" enctype="multipart/form-data" autocomplete="off" id="myForm">
						<div class="m-t-1">

							<div class="g24-col-sm-20">
								<div class="form-group">
									<label class="g24-col-sm-6 control-label">รหัสสมาชิก <span id="naja"></span> </label>
									<div class="g24-col-sm-6">
										<div class="form-group">
											<div class="input-group">
												<input id="member_id" name="member_id" class="form-control" style="text-align:left;" type="number" value="<?php echo empty($row_member) ? '': $row_member['member_id']; ?>" required title="กรุณาป้อน รหัสสมาชิก" onkeypress="check_member_id();"/>
												<span class="input-group-btn">
													<a data-toggle="modal" data-target="#myModal" id="modal-search" class="fancybox_share fancybox.iframe" href="#">
														<button id="" type="button" class="btn btn-info btn-search"><span class="icon icon-search"></span>
														</button>
													</a>
												</span>	
											</div>
										</div>
									</div>
									<label class="g24-col-sm-4 control-label" for="budget_year">ชื่อสกุล</label>
									<div class="g24-col-sm-8">
										<div class="form-group">
											<input type="text" class="form-control " name="member_name" id="member_name" value="<?php echo @$row_member['firstname_th'].' '.@$row_member['lastname_th'] ?>"  readonly="readonly">
										</div>
									</div>
								</div>
								<div class="form-group">
									<label class="g24-col-sm-6 control-label" for="birthday"> วันเกิด </label>
									<div class="g24-col-sm-6" id="birthday_con">
										<div class="form-group">
											<input type="text" class="form-control " name="birthday" id="birthday" value="<?php echo (!empty($row_member['birthday'])?$this->center_function->mydate2date($row_member['birthday']):''); ?>"  readonly="readonly">
										</div>
									</div>
									
									<label class="g24-col-sm-4 control-label">อายุ</label>
									<div class="g24-col-sm-6">
										<div class="form-group" id="birthday_border">
											<input type="text" class="form-control " name="age" id="age" value="<?php echo (!empty($row_member['birthday']))?$this->center_function->diff_year($row_member['birthday'],date('Y-m-d')):'';  ?>"  readonly="readonly">
										</div>
									</div>
								</div>

								<div class="form-group">
									<label class="g24-col-sm-6 control-label">วันที่เข้าเป็นสมาชิก </label>
									<div class="g24-col-sm-6">
										<div class="form-group">
											<input type="text" class="form-control " name="apply_date" id="apply_date" value="<?php echo (!empty($row_member['apply_date'])?$this->center_function->mydate2date($row_member['apply_date']):''); ?>"  readonly="readonly">
										</div>
									</div>
									<label class="g24-col-sm-4 control-label">อายุสมาชิก </label>
									<div class="g24-col-sm-6">
										<div class="form-group">
											<input type="text" class="form-control " name="apply_age" id="apply_age" value="<?php echo (!empty($row_member['apply_date']))?$this->center_function->diff_year($row_member['apply_date'],date('Y-m-d')):'';  ?>"  readonly="readonly">
										</div>
									</div>
								</div>

								<div class="form-group">
									<label class="g24-col-sm-6 control-label">กำหนดอายุเกษียณ </label>
									<div class="g24-col-sm-6">
										<div class="form-group">
											<input type="text" class="form-control " name="retry_date" id="retry_date" value="<?php echo ((!empty($row_member['retry_date']) && $row_member['retry_date'] != '0000-00-00')?$this->center_function->mydate2date($row_member['retry_date']):''); ?>"  readonly="readonly">
										</div>
									</div>
									<label class="g24-col-sm-4 control-label">สถานะการเกษียณ </label>
									<div class="g24-col-sm-6">
										<div class="form-group">
											<input type="text" class="form-control " name="retry_status" id="retry_status" value="<?php echo @$row_member['retry_status'];?>"  readonly="readonly">
										</div>
									</div>
								</div>
							</div>                 
						</div>
					</form>

					<div class="bs-example" data-example-id="striped-table">
						<div id="tb_wrap">
							<table class="table table-bordered table-striped table-center">
								<thead>
								<tr class="bg-primary">
									<th>ลำดับ</th>
									<th>สวัสดิการที่ได้รับ</th>
									<th>วันที่ได้รับ</th>
									<th>เลขที่คำร้อง</th>
									<th>สถานะ</th>
									<th>ผู้ทำรายการ</th>
									<th></th>
								</tr>
								</thead>
								<tbody>
								<?php
								$j=1;						
								if(!empty($row)){
									foreach(@$row as $key => $value){ ?>
										<tr>
											<td style="width: 80px;"><?php echo @$j++; ?></td>
											<td style="width: 20%;text-align: left;"><?php echo @$value['benefits_name']; ?></td>
											<td><?php echo $this->center_function->ConvertToThaiDate(@$value['createdatetime']); ?></td>
											<td>
												<?php if(@$value['benefits_transfer_id']){?>
													<a class="" id="transfer_<?php echo @$value['benefits_request_id']; ?>_2" title="แสดงรายการโอน/ชำระ" style="cursor: pointer;padding-left:2px;padding-right:2px" onclick="transfer_benefits('<?php echo @$value['benefits_request_id']; ?>','view')">
														<?php echo @$value['benefits_no']; ?>
													</a>
												<?php 
													}else{												
														echo @$value['benefits_no'];
													}
												?>
											</td>
											<td>
												<?php
													if(@$value['transfer_status'] == '0'){		
												?>
														<span>
															<?php echo $benefits_status[$value['benefits_status']];?>
															<?php echo (!empty($value['record_date']))?' '.$this->center_function->ConvertToThaiDate(@$value['record_date']):'';?>
														</span>															 
												<?php
													}else{
												?>
														<span class="text-status" style="background:<?php echo $status_bg_color[$value['benefits_status']];?>">
															<?php echo $benefits_status[$value['benefits_status']];?>
														</span>	
												<?php	
													}
												?>																									
											</td>
											<td><?php echo @$value['user_name']; ?></td>
											<td style="width: 80px;">
												<?php 
												$arr_show_edit = array("1","4","5");
												if(!in_array($value['benefits_status'],$arr_show_edit)){ 
												?>
												<span class="icon icon-pencil text-edit" aria-hidden="true" style="font-size: 16px;" title="แก้ไข" onclick="edit_request('<?php echo @$value['benefits_request_id'] ?>','<?php echo @$value['member_id'] ?>')"></span>
												<?php }?>
												<!--<span class="icon icon-print text-edit" aria-hidden="true" style="font-size: 16px;" title="พิมพ์"></span>-->
												<?php 
												$arr_show_del = array("4");
												if(!in_array($value['benefits_status'],$arr_show_del)){ 
												?>
												<span class="icon icon-trash-o text-edit" aria-hidden="true" style="font-size: 16px;" title="ลบ" onclick="del_coop_data('<?php echo @$value['benefits_request_id'] ?>','<?php echo @$value['member_id'] ?>')"></span>
												<?php }?>
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
					<?php echo @$paging ?>
				</div>
			</div>
		</div>
    </div>
</div>

<div class="modal fade" id="myModal" role="dialog">
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
										<option value="employee_id">รหัสพนักงาน</option>
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
										<input id="search_text" name="search_text" class="form-control m-b-1" type="text" value="<?php echo @$data['id_card']; ?>">
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

<div class="modal fade" id="myModalRequest"  tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-account" style="width:80%">
        <div class="modal-content">
            <div class="modal-header modal-header-confirmSave">
                <button type="button" class="close" data-dismiss="modal"></button>
                <h2 class="modal-title">คำร้องขอสวัสดิการ</h2>
            </div>
            <div class="modal-body">
				<form data-toggle="validator" method="post" action="<?php echo base_url(PROJECTPATH.'/benefits/benefits_request_save'); ?>" class="g24 form form-horizontal" enctype="multipart/form-data" autocomplete="off" id="from_save">
					<input type="hidden" name="benefits_request_id" id="benefits_request_id" value=""/>
					<div class="g24-col-sm-24 m-t-1">
						<div class="form-group">
							<label class="g24-col-sm-6 control-label">รหัสสมาชิก <span id="naja"></span> </label>
							<div class="g24-col-sm-6">
								<div class="form-group">
									<input id="member_id" name="member_id" class="form-control" style="text-align:left;" type="number" value="<?php echo @$row_member['member_id']; ?>" readonly="readonly" required title="กรุณาป้อน รหัสสมาชิก" />
								</div>
							</div>
							<label class="g24-col-sm-3 control-label" for="budget_year">ชื่อสกุล</label>
							<div class="g24-col-sm-8">
								<div class="form-group">
									<input type="text" class="form-control " name="member_name" id="member_name" value="<?php echo @$row_member['firstname_th'].' '.@$row_member['lastname_th'] ?>"  readonly="readonly">
								</div>
							</div>
						</div>
						<div class="form-group">
							<label class="g24-col-sm-6 control-label" for="birthday"> วันเกิด </label>
							<div class="g24-col-sm-6" id="birthday_con">
								<div class="form-group">
									<input type="text" class="form-control " name="birthday" id="birthday" value="<?php echo (!empty($row_member['birthday'])?$this->center_function->mydate2date($row_member['birthday']):''); ?>"  readonly="readonly">
								</div>
							</div>
							
							<label class="g24-col-sm-3 control-label">อายุ</label>
							<div class="g24-col-sm-6">
								<div class="form-group" id="birthday_border">
									<input type="text" class="form-control " name="age" id="age" value="<?php echo (!empty($row_member['birthday']))?$this->center_function->diff_year($row_member['birthday'],date('Y-m-d')):'';  ?>"  readonly="readonly">
								</div>
							</div>
						</div>

						<div class="form-group">
							<label class="g24-col-sm-6 control-label">วันที่เข้าเป็นสมาชิก </label>
							<div class="g24-col-sm-6">
								<div class="form-group">
									<input type="text" class="form-control " name="apply_date" id="apply_date" value="<?php echo (!empty($row_member['apply_date'])?$this->center_function->mydate2date($row_member['apply_date']):''); ?>"  readonly="readonly">
								</div>
							</div>
							<label class="g24-col-sm-3 control-label">อายุสมาชิก </label>
							<div class="g24-col-sm-6">
								<div class="form-group">
									<input type="text" class="form-control " name="apply_age" id="apply_age" value="<?php echo (!empty($row_member['apply_date']))?$this->center_function->diff_year($row_member['apply_date'],date('Y-m-d')):'';  ?>"  readonly="readonly">
								</div>
							</div>
						</div>

						<div class="form-group">
							<label class="g24-col-sm-6 control-label">กำหนดอายุเกษียณ </label>
							<div class="g24-col-sm-4">
								<div class="form-group">
									<input type="text" class="form-control " name="retry_date" id="retry_date" value="<?php echo ((!empty($row_member['retry_date']) && $row_member['retry_date'] != '0000-00-00')?$this->center_function->mydate2date($row_member['retry_date']):''); ?>"  readonly="readonly">
								</div>
							</div>
							<label class="g24-col-sm-3 control-label">สถานะการเกษียณ </label>
							<div class="g24-col-sm-8">
								<div class="form-group">
									<input type="text" class="form-control " name="retry_status" id="retry_status" value="<?php echo @$row_member['retry_status'];?>"  readonly="readonly">
								</div>
							</div>
						</div>
						<div class="form-group">
							<label class="g24-col-sm-6 control-label">เลือกสวัสดิการ </label>
							<div class="g24-col-sm-18">
								<div class="form-group">
									<select name="benefits_type_id" id="benefits_type_id" class="form-control" style="width:50%;" onchange="change_type()" required title="กรุณาเลือก สวัสดิการ">
										<option value="">เลือกสวัสดิการ</option>
									<?php 
										if(!empty($benefits_type)){
											foreach($benefits_type as $key => $value){ ?>
											<option value="<?php echo $value['benefits_id']; ?>" <?php echo $value['benefits_id']==$data['benefits_type_id'] ? 'selected' : ''; ?>><?php echo $value['benefits_name']; ?></option>
									<?php 
											}
										} 
									?>
									</select>
								</div>
							</div>
						</div>

						<div class="form-group">
							<label class="g24-col-sm-6 control-label">รายละเอียดสวัสดิการ </label>
							<div class="g24-col-sm-18">
								<div class="form-group">
									<div id="benefits_request_detail" style="border: 1px solid #e0e0e0;margin-top: 10px;margin: 5px 0px 5px 0px;width: 100%;height: 300px;padding: 5px;border-radius: 3px; overflow-y: scroll;"><?php echo @$row['benefits_request_detail']; ?></div>
								</div>
							</div>
						</div>
						<div class="form-group">
							<div class="g24-col-sm-6 control-label text-left" >
								<label class="g24-col-sm-24 control-label">เงื่อนไข </label>
							</div>
							<div class="g24-col-sm-18 control-label text-left" id="conditions">
								<label class="g24-col-sm-24 control-label text-left"> - </label>
                                <input type = "hidden" value="ทดสอบ">
                                <input type = "hidden" value="ทดสอบ">
                                <input type = "hidden" value="ทดสอบ">
                                <input type = "hidden" value="ทดสอบ">
							</div>
							<input type="hidden" id="lastest_condition_created" value=""/>
						</div>
						<div class="form-group">
							<label class="g24-col-sm-6 control-label">แนบไฟล์คำร้อง </label>
							<div class="g24-col-sm-7">
								<div class="g24-col-sm-12">
									<div class="form-group">
                                        <!--<label class="fileContainer btn btn-info">
                                            <span class="icon icon-paperclip"></span>
                                            เลือกไฟล์
                                            <input type="file" class="form-control" name="benefits_request_file[0]" value="" multiple>
										</label>
                                        -->
                                        <input type="button" value="เพิ่มไฟล์" onclick="add_file()" class="btn btn-primary">
									</div>
								</div>
								<div class="g24-col-sm-12">
									<button class="btn btn-primary" id="btn_show_file" type="button" onclick="show_file()" style="display:none;"><span>แสดงไฟล์แนบ</span></button>
								</div>
							</div>
							<label class="g24-col-sm-4 control-label">ยอดเงินสวัสดิการที่อนุมัติ </label>
							<div class="g24-col-sm-5">
								<div class="form-group">
									<input type="text" class="form-control benefits_approved_req" name="benefits_approved_amount" id="benefits_approved_amount" value=""  required title="กรุณาป้อน ยอดเงินสวัสดิการที่อนุมัติ">
								</div>
							</div>
						</div>

                        <div id="file_upload_list" style="display:block; padding-left: 25%">
                        </div>

						<div class="form-group">
							<label class="g24-col-sm-6 control-label"> &nbsp;</label>
							<div class="g24-col-sm-18">
								<label class="control-label">
									<input type="checkbox" id="benefits_check_condition" name="benefits_check_condition"  value="1">
									<span>ตรวจสอบแล้วผ่านเกณฑ์เงื่อนไข</span>								
									<span style="padding-left: 15px;">ผู้ตรวจสอบและทำรายการ  <span id="user_name"></span></span>
									<ib>
								</label>
								<input type="hidden" class="form-control " name="user_name_session" id="user_name_session" value="<?php echo $_SESSION['USER_NAME'];?>">								 
							</div>
						</div>
						<div class="form-group">
							<label class="g24-col-sm-24 control-label"> &nbsp;</label>
						</div>
					</div>
                    <div class="modal fade" id="add_file_modal" role="dialog" now-file = "0">
                        <div class="modal-dialog modal-dialog-file">
                            <div class="modal-content data_modal">
                                <div class="modal-header modal-header-confirmSave">
                                    <button type="button" class="close" onclick="close_modal('add_file_modal')">&times;</button>
                                    <h2 class="modal-title">เพิ่มไฟล์</h2>
                                </div>
                                <div class="modal-body" id="file_input">
                                </div>
                                <div>

                                </div>
                            </div>
                        </div>
				</form>
            </div>
            <div class="text-center m-t-1" style="padding-top:10px;">
                <button class="btn btn-info" onclick="check_form()"><span class="icon icon-save"></span> บันทึก</button>
				<button class="btn btn-info" onclick="close_modal('myModalRequest')"><span class="icon icon-close"></span> ออก</button>
            </div>
			<div class="text_center m-t-1">&nbsp;</div>
        </div>
    </div>
</div>

<div class="modal fade" id="show_file_attach" role="dialog">
	<div class="modal-dialog modal-dialog-file">
	  <div class="modal-content data_modal">
		<div class="modal-header modal-header-confirmSave">
		  <button type="button" class="close" onclick="close_modal('show_file_attach')">&times;</button>
		  <h2 class="modal-title">แสดงไฟล์แนบ</h2>
		</div>
		<div class="modal-body" id="show_file_space">
		</div>
	  </div>
	</div>
</div>

<div class="modal fade" id="viewRequest"  tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-account" style="width:80%">
        <div class="modal-content">
            <div class="modal-header modal-header-confirmSave">
                <button type="button" class="close" data-dismiss="modal"></button>
                <h2 class="modal-title">ดูสวัสดิการ</h2>
            </div>
            <div class="modal-body">
				<form data-toggle="validator" method="post" action="" class="g24 form form-horizontal" enctype="multipart/form-data" autocomplete="off" id="from_view">
					<input type="hidden" name="benefits_request_id" id="benefits_request_id" value=""/>
					<div class="g24-col-sm-24 m-t-1">
						<div class="form-group">
							<label class="g24-col-sm-6 control-label">เลือกสวัสดิการ </label>
							<div class="g24-col-sm-10">
								<div class="form-group">
									<select name="benefits_type_id_view" id="benefits_type_id_view" class="form-control" style="" onchange="change_type_view()">
										<option value="">เลือกสวัสดิการ</option>
									<?php 
										if(!empty($benefits_type)){
											foreach($benefits_type as $key => $value){ ?>
											<option value="<?php echo $value['benefits_id']; ?>"><?php echo $value['benefits_name']; ?></option>
									<?php 
											}
										} 
									?>
									</select>
								</div>
							</div>
							<label class="g24-col-sm-3 control-label">มีผลวันที่ </label>
							<div class="g24-col-sm-5">
								<div class="form-group">
									<input type="text" class="form-control " name="start_date_view" id="start_date_view" value=""  readonly="readonly">
								</div>
							</div>
						</div>
						<div class="form-group">
							<label class="g24-col-sm-6 control-label">รายละเอียดสวัสดิการ </label>
							<div class="g24-col-sm-18">
								<div class="form-group">
									<div id="benefits_request_detail_view" style="border: 1px solid #e0e0e0;margin-top: 10px;margin: 5px 0px 5px 0px;width: 100%;height: 300px;padding: 5px;border-radius: 3px;"><?php echo @$row['benefits_request_detail']; ?></div>
								</div>
							</div>
						</div>						
					</div>
				</form>
            </div>
            <div class="text-center m-t-1" style="padding-top:10px;">
				<button class="btn btn-info" onclick="close_modal('viewRequest')"><span class="icon icon-close"></span> ออก</button>
            </div>
			<div class="text_center m-t-1">&nbsp;</div>
        </div>
    </div>
</div>

<div class="modal fade" id="show_transfer"  tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-account" style="width:80%">
        <div class="modal-content">
            <div class="modal-header modal-header-confirmSave">
                <button type="button" class="close" data-dismiss="modal"></button>
                <h2 class="modal-title">โอนเงิน/ชำระเงิน</h2>
            </div>
            <div class="modal-body">
				<form data-toggle="validator" method="post" action="<?php echo base_url(PROJECTPATH.'/benefits/benefits_transfer_save'); ?>" class="g24 form form-horizontal" enctype="multipart/form-data" autocomplete="off" id="from_transfer">
					<input type="hidden" name="benefits_request_id" id="benefits_request_id" class="benefits_request_id" value=""/>
					<input type="hidden" name="action" id="action" value=""/>
					<div class="g24-col-sm-24 m-t-1">
						<div class="form-group">
							<label class="g24-col-sm-6 control-label">รหัสสมาชิก <span id="naja"></span> </label>
							<div class="g24-col-sm-6">
								<div class="form-group">
									<input id="member_id" name="member_id" class="form-control member_id" style="text-align:left;" type="number" value="" readonly="readonly" required title="กรุณาป้อน รหัสสมาชิก" />
								</div>
							</div>
							<label class="g24-col-sm-3 control-label" for="member_name">ชื่อสกุล</label>
							<div class="g24-col-sm-8">
								<div class="form-group">
									<input type="text" class="form-control  member_name" name="member_name" id="member_name" value=""  readonly="readonly">
								</div>
							</div>
						</div>
						<div class="form-group">
							<label class="g24-col-sm-6 control-label" for="benefits_no"> เลขที่คำร้อง </label>
							<div class="g24-col-sm-6" id="birthday_con">
								<div class="form-group">
									<input type="text" class="form-control  benefits_no" name="benefits_no" id="benefits_no" value=""  readonly="readonly">
								</div>
							</div>
							
							<label class="g24-col-sm-3 control-label">สวัสดิการ</label>
							<div class="g24-col-sm-6">
								<div class="form-group" id="benefits_type_name">
									<input type="text" class="form-control  benefits_type_name" name="benefits_type_name" id="benefits_type_name" value=""  readonly="readonly">
								</div>
							</div>
						</div>
						<div class="form-group">
							<label class="g24-col-sm-6 control-label">ยอดเงิน </label>
							<div class="g24-col-sm-6">
								<div class="form-group">
									<input type="text" class="form-control  benefits_approved_amount" name="benefits_approved_amount" id="benefits_approved_amount" value=""  readonly="readonly">
								</div>
							</div>
							<label class="g24-col-sm-3 control-label">ผู้ทำรายการ </label>
							<div class="g24-col-sm-6">
								<div class="form-group">
									<input type="text" class="form-control  admin_request" name="admin_request" id="admin_request" value=""  readonly="readonly">
								</div>
							</div>
						</div>
						<div class="form-group">
							<?php
								if(@$data['bank_type'] == ''){
									$checked_1 = 'checked';
									$checked_2 = '';
								}else{
									if(@$data['bank_type'] == '1'){
										$checked_1 = 'checked';
										$checked_2 = '';
									}else if(@$data['bank_type'] == '2'){
										$checked_1 = '';
										$checked_2 = 'checked';
									}
								}
							?>
							<div class="g24-col-sm-6  " style="text-align:right;"><input type="radio" id="bank_choose_1" name="bank_type" value="1" onclick="change_bank_type()" <?php echo $checked_1; ?>></div>
							<div class=" g24-col-sm-18">
								<div class="form-group">
									บัญชีสหกรณ์ <input type="radio" id="bank_choose_2" name="bank_type" value="2" onclick="change_bank_type()" <?php echo $checked_2; ?>> บัญชีอื่นๆ 
								</div>
							</div>
						</div>
						<div class="form-group">
							<div id="bank_type_1" style="display:none;">
								<label class="g24-col-sm-6 control-label" for="">ธนาคาร</label>
								<div class="g24-col-sm-18">
									<div class="form-group">
										<select name="account_id" id="account_id" class="form-control " style="width:50%;" onchange="" required title="กรุณาเลือก บัญชี" >
											<option value="">เลือกบัญชี</option>
										</select>
									</div>
								</div>
							</div>
							<div id="bank_type_2" style="display:none;">
								<label class="g24-col-sm-6 control-label" for="">ธนาคาร</label>
								<div class="g24-col-sm-2">
									<div class="form-group">
										<input id="bank_id_show" class="form-control group-bank-left" type="text" value="<?php echo @$data["bank_id"]; ?>" readonly>
									</div>
								</div>
								<div class=" g24-col-sm-7">
									<div class="form-group">
										<select id="dividend_bank_id" name="dividend_bank_id" class="form-control group-bank-right" onchange="change_bank()">
											<option value="">เลือกธนาคาร</option>
											<?php foreach($bank as $key => $value) { ?>
											<option value="<?php echo $value["bank_id"]; ?>" <?php if($value["bank_id"]==@$data["bank_id"]) { ?> selected="selected"<?php } ?> > <?php echo $value["bank_name"]; ?>
												</option><?php } ?>
										</select>
									</div>
								</div>
								<div class="g24-col-sm-7" style="height: 40px;">
									&nbsp;
								</div>
								<label class="g24-col-sm-6 control-label" for="">สาขา</label>
								<div class="g24-col-sm-2">
									<div class="form-group">
										<input id="branch_id_show" class="form-control group-bank-left" type="text" value="<?php echo @$data["bank_branch_id"]; ?>" readonly>
									</div>
								</div>
								<div class=" g24-col-sm-7">
									<div class="form-group">
										<span id="bank_branch">
											<select id="dividend_bank_branch_id"  name="dividend_bank_branch_id" class="form-control group-bank-right" onchange="change_branch()">
												<option value="">เลือกสาขาธนาคาร</option>
												<?php foreach($bank_branch as $key => $value) { ?>
													<option value="<?php echo $value["branch_id"]; ?>" <?php if($value["branch_id"] == @$data["bank_branch_id"]) { ?> selected="selected"<?php } ?>><?php echo $value["branch_name"]; ?></option>
												<?php } ?>
											</select>
										</span>
									</div>
								</div>
								<div class="g24-col-sm-7" style="height: 40px;">
									&nbsp;
								</div>

								<label class="g24-col-sm-6 control-label" for="">เลขที่บัญชี</label>
								<div class=" g24-col-sm-9">
									<div class="form-group">
										<input id="bank_account_no" class="form-control  clear_pay" name="bank_account_no"  type="text" value="<?php echo @$data["bank_account_no"]; ?>">
									</div>
								</div>
								<div class="g24-col-sm-7" style="height: 40px;">
									&nbsp;
								</div>
								<div class="g24-col-sm-24 modal_data_input">
									<label class="g24-col-sm-6 control-label " >วันที่โอนเงิน</label>
									<div class="input-with-icon g24-col-sm-5">
										<div class="form-group">
											<input id="date_transfer_picker" name="date_transfer" class="form-control " type="text" value="<?php echo $this->center_function->mydate2date(date('Y-m-d')); ?>" data-date-language="th-th">
											<span class="icon icon-calendar input-icon m-f-1"></span>
										</div>
									</div>
								</div>
								<div class="g24-col-sm-24 modal_data_input">
									<label class="g24-col-sm-6 control-label " >เวลาโอนเงิน</label>
									<div class="input-with-icon g24-col-sm-5">
										<div class="form-group">
											<input id="time_transfer" name="time_transfer" class="form-control " type="text" value="<?php echo date('H:i'); ?>">
											<span class="icon icon-clock-o input-icon m-f-1"></span>
										</div>
									</div>
								</div>
								<label class="g24-col-sm-6 control-label">แนบหลักฐานการโอนเงิน</label>
								<div class="g24-col-sm-6">
									<div class="form-group">
										<input type="file" class=" form-control" name="file_name" id="file_name">
									</div>
								</div>
								<div class="g24-col-sm-7" style="height: 47px;">
									&nbsp;
								</div>
								<div id="file_show" style="display:none">
									<label class="g24-col-sm-6 control-label"></label>
									<div class="g24-col-sm-6">
										<div class="form-group">
											<img src="" id="file_transfer" class="" width="150px" height="150px">
										</div>
									</div>
								</div>									
							</div>
						</div>
						<!---->
						<div class="form-group">
							<label class="g24-col-sm-6 control-label">ผู้ทำรายการโอนเงิน/ชำระเงิน </label>
							<div class="g24-col-sm-6">
								<div class="form-group">
									<input type="text" class="form-control  admin_transfer" name="admin_transfer" id="admin_transfer" value=""  readonly="readonly">
								</div>
							</div>
							<label class="g24-col-sm-3 control-label">วันที่ทำรายการ </label>
							<div class="g24-col-sm-6">
								<div class="form-group">
									<input type="text" class="form-control " name="createdatetime" id="createdatetime" value=""  readonly="readonly">
								</div>
							</div>
						</div>
						<div class="form-group">
							<label class="g24-col-sm-6 control-label">ส่ง SMS ไปที่เบอร์ </label>
							<div class="g24-col-sm-6">
								<div class="form-group">
									<input type="text" class="form-control  mobile" name="mobile" id="mobile" value=""  readonly="readonly">
								</div>
							</div>
						</div>
					</div>
				</form>
            </div>
            <div class="text-center m-t-1" style="padding-top:10px;">
				<button class="btn btn-info" onclick="check_form_transfer()" id="bt_save"><span class="icon icon-save"></span> บันทึกการทำรายการโอน/ชำระ</button>
				<button class="btn btn-info" onclick="close_modal('show_transfer')" id="bt_close"><span class="icon icon-close"></span> ออก</button>
            </div>
			<div class="text_center m-t-1">&nbsp;</div>
        </div>
    </div>
</div>

<div class="modal fade" id="confirm_sp_req" role="dialog">
    <div class="modal-dialog modal-sm">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">ยืนยันตัวตน</h4>
        </div>
        <div class="modal-body">
			<p>ชื่อผู้ใช้งาน</p>
			<input type="text" class="form-control" id="confirm_user">
			<p>รหัสผ่าน</p>
			<input type="password" class="form-control" id="confirm_pwd">
			  <br>
			<input type="hidden" id="transaction_id_err">
			<div class="row">
				<div class="col-sm-12 text-center">
					<button class="btn btn-info" id="submit_auth_confirm">บันทึก</button>
				</div>
			</div>
        </div>
        <div class="modal-footer">
        </div>
      </div>
    </div>
</div>
<?php
$link = array(
    'src' => PROJECTJSPATH.'assets/js/coop_benefits_request.js',
    'type' => 'text/javascript'
);
echo script_tag($link);
?>

<script>
	function check_member_id() {
		var member_id = $('#member_id').first().val();
		var keycode = (event.keyCode ? event.keyCode : event.which);
		if(keycode == '13'){
			$.post(base_url+"save_money/check_member_id", 
			{	
			member_id: member_id
			}
			, function(result) {
				obj = JSON.parse(result);
				mem_id = obj.member_id;
				if(mem_id != undefined){
					document.location.href = '<?php echo base_url(uri_string())?>?id='+mem_id
				}else{					
					swal('ไม่พบรหัสสมาชิกที่ท่านเลือก','','warning'); 
				}
			});		
		}
	}

	$('#member_search').click(function(){
		if($('#search_list').val() == '') {
			swal('กรุณาเลือกรูปแบบค้นหา','','warning');
		} else if ($('#search_text').val() == ''){
			swal('กรุณากรอกข้อมูลที่ต้องการค้นหา','','warning');
		} else {
			$.ajax({  
				url: base_url+"ajax/search_member_by_type",
				method:"post",  
				data: {
					search_text : $('#search_text').val(), 
					search_list : $('#search_list').val()
				},  
				dataType:"text",  
				success:function(data) {
					$('#table_data').html(data.replace("?member_id=", "?id="));  
				}  ,
				error: function(xhr){
					console.log('Request Status: ' + xhr.status + ' Status Text: ' + xhr.statusText + ' ' + xhr.responseText);
				}
			});  
		}
	});

	function add_file(){
	    let index = parseInt($("#add_file_modal").attr("now-file"));
	    console.log("now index = " + index);
	    //let old_input = $("#file_input").html();
	    //console.log("Old input = " + old_input);
	    //$("#file_input_invisible").append(old_input);
        //$("#file_input").html("");
	    let input_html = `<div id = row_add_file_`+index+` style="">
                             <label>ชื่อไฟล์</label>
                             <input type="text" class="form-control" id = "file_name[`+index+`]" name = "file_name[`+index+`]" val="test">
                             <br>
                             <label class="fileContainer btn btn-info">
                                <span class="icon icon-paperclip"></span>
                                เลือกไฟล์
                                <input type="file" class="form-control" id ="benefits_request_file[`+index+`]" name="benefits_request_file[`+index+`]" value="">
                             </label>
                             <input type="button" class="btn btn-primary" value="บันทึก" onclick="save_file()">
                          </div>`;
	    $("#file_input").append(input_html);
	    $("#add_file_modal").modal('show');
    }

    function save_file(){
        let index = parseInt($("#add_file_modal").attr("now-file"));
        console.log(`#benefits_request_file[`+index+`]`);
        console.log(document.getElementById(`benefits_request_file[`+index+`]`).files.length == 0);
        console.log(document.getElementById(`file_name[`+index+`]`).value);
        if (document.getElementById(`benefits_request_file[`+index+`]`).files.length == 0 || document.getElementById(`file_name[`+index+`]`).value == ""){
            let alt_text = "";
            if (document.getElementById(`benefits_request_file[`+index+`]`).files.length == 0 && document.getElementById(`file_name[`+index+`]`).value == ""){
                alt_text += "กรุณาอัพโหลดไฟล์และชื่อไฟล์";
            } else if (document.getElementById(`file_name[`+index+`]`).value == ""){
                alt_text += "กรุณาใส่ชื่อไฟล์";
            } else if (document.getElementById(`benefits_request_file[`+index+`]`).files.length == 0){
                alt_text += "กรุณาอัพโหลดไฟล์";
            }
            swal(alt_text);
        } else {
            console.log("text value = "+document.getElementById(`file_name[`+index+`]`).value);
            console.log($(`#file_name[`+index+`]`).attr("val"));
            document.getElementById(`file_name[`+index+`]`).setAttribute("val",document.getElementById(`file_name[`+index+`]`).value);
            //$(`#file_name[`+index+`]`).attr("val",document.getElementById(`file_name[`+index+`]`).value);
            $(`#row_add_file_`+index).attr("style","display:none;");
            $("#add_file_modal").attr("now-file",index+1);
            $("#add_file_modal").modal('hide');
            let val = document.getElementById(`file_name[`+index+`]`).value;
            let name_text =`<div id = "row_add_file_name_`+index+`" style="display: inline-block; margin-left: 15px">
                                <label>`+val+`</label>
                                <span id = "index_at_`+index+`"class = "del-file-name icon icon-ban" style="color: red" index-at = `+ index +` ></span>
                            </div>`
            $("#file_upload_list").append(name_text);
        }
    }

    $("#add_file_modal").on('hide.bs.modal', function(){
        //alert('The modal is about to be hidden.');
        let index = parseInt($("#add_file_modal").attr("now-file"));
        if ($(`#row_add_file_`+index).attr("style") == ""){
            $(`#row_add_file_`+index).remove();
        }
    });

    $(document).on("click", ".del-file-name",function () {
        let index = parseInt($(this).attr("index-at"));
        swal({
            title: "ท่านต้องการลบไฟล์ใช่หรือไม่?",
            text: "",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: '#DD6B55',
            confirmButtonText: 'ยืนยัน',
            cancelButtonText: "ยกเลิก",
            closeOnConfirm: true,
            closeOnCancel: true
        } , function(isConfirm) {
            if (isConfirm) {
                console.log($(this).attr("index-at"));
                console.log("work!");
                $(`#row_add_file_name_`+index+``).remove();
                $(`#row_add_file_`+index+``).remove();
                console.log("done");
            }else{

            }
        });
    });
</script>
