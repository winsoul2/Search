<div class="layout-content">
    <div class="layout-content-body">
		<style>
			.center {
				text-align: center;
			}
			.modal-dialog-account {
				margin:auto;
				margin-top:7%;
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
			.btn-col {
				padding-right:5px !important;
				padding-left:5px !important;
			}
		</style> 
		<h1 style="margin-bottom: 0">อนุมัติสวัสดิการ</h1>
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 padding-l-r-0">
			<div class="col-xs-12 col-sm-6 col-md-6 col-lg-7 padding-l-r-0">
			<?php $this->load->view('breadcrumb'); ?>
			</div>
			<div class="col-xs-12 col-sm-6 col-md-6 col-lg-5 " style="padding-right:0px;text-align:right;">
				<div class="col-sm-3"></div>
				<div class="col-sm-3 btn-col">
					<button name="bt_view" id="bt_view" type="button" class="btn btn-primary btn-lg bt-add block" style="width:100% !important;" onclick="approve_request()">
						<span>อนุมัติ</span>
					</button>
				</div>
				<div class="col-sm-3 btn-col">
					<button name="bt_view" id="bt_view" type="button" class="btn btn-primary btn-lg bt-add" style="width:100% !important;" onclick="delete_request()">
						<span>ลบ</span>
					</button>
				</div>
				<!-- <div class="col-sm-3 btn-col">
					<button name="bt_view" id="bt_view" type="button" class="btn btn-primary btn-lg bt-add" style="width:100% !important;" onclick="print_slip_modal()">
						<span>พิมพ์ใบนำจ่าย</span>
					</button>
				</div> -->
				<div class="col-sm-3 btn-col">
					<button name="bt_view" id="bt_view" type="button" class="btn btn-primary btn-lg bt-add" style="width:100% !important;" onclick="view_request()">
						<span class="icon icon-search"></span>
						<span>ดูสวัสดิการ</span>
					</button>
				</div>   
			</div>
		</div>
		<div class="row gutter-xs">
			<div class="col-xs-12 col-md-12">
				<div class="panel panel-body" style="padding-top:0px !important;">
					<h3 >รายการขออนุมัติสวัสดิการ</h3>
					<br/>
					<div class="col-xs-12 col-md-12">
						<form data-toggle="validator" method="get" action="" class="g24 form form-horizontal" enctype="multipart/form-data" autocomplete="off" id="from_view">
							<label class="control-label g24-col-sm-2">ประเภท</label>
							<div class="g24-col-sm-7 form-group">
								<select name="benefits_type_id_search" id="benefits_type_id_search" class="form-control" style="">
									<option value="">เลือกสวัสดิการ</option>
								<?php 
									if(!empty($benefits_type)){
										foreach($benefits_type as $key => $value){ ?>
										<option value="<?php echo $value['benefits_id']; ?>" <?php echo $_GET['benefits_type_id_search'] == $value['benefits_id'] ? "selected" : "";?>><?php echo $value['benefits_name']; ?></option>
								<?php 
										}
									} 
								?>
								</select>
							</div>
							<label class="g24-col-sm-1 control-label right"> วันที่ </label>
							<div class="g24-col-sm-3">
								<div class="input-with-icon">
									<div class="form-group">
										<input id="start_date" name="start_date" class="form-control m-b-1 mydate" style="padding-left: 50px;" type="text" value="<?php echo empty($_GET["start_date"]) ? $this->center_function->mydate2date(date('Y-m-d')) : $_GET["start_date"]; ?>" data-date-language="th-th">
										<span class="icon icon-calendar input-icon m-f-1"></span>
									</div>
								</div>
							</div>
							<label class="g24-col-sm-1 control-label center"> ถึงวันที่ </label>
							<div class="g24-col-sm-3">
								<div class="input-with-icon">
									<div class="form-group">
										<input id="end_date" name="end_date" class="form-control m-b-1 mydate" style="padding-left: 50px;" type="text" value="<?php echo empty($_GET["end_date"]) ? $this->center_function->mydate2date(date('Y-m-d')) : $_GET["end_date"]; ?>" data-date-language="th-th">
										<span class="icon icon-calendar input-icon m-f-1"></span>
									</div>
								</div>
							</div>
							<div class="g24-col-sm-3 btn-col">
								<button name="bt_view" id="bt_view" type="submit" class="btn btn-primary btn-lg bt-add block" style="width:80% !important; margin:0;">
									<span>แสดงผล</span>
								</button>
							</div>
						</form>
					</div>
					<br/>
					<br/>
					<br/>
					 <table class="table table-bordered table-striped table-center">
					 <thead> 
						<tr class="bg-primary">
							<th>
								<input type="checkbox" id="req_check_all" value="">
							</th>
							<th>วันที่ทำรายการ</th>
							<th>ประเภทสวัสดิการ</th>
							<th>ชื่อสมาชิก</th>
							<th>เลขที่คำร้อง</th>
							<th>ยอดเงิน</th>
							<th>ผู้ทำรายการ</th>
							<th>สถานะ</th>
							<th>จัดการ</th> 
						</tr> 
					 </thead>
					 <tbody id="table_first">
					 	<form data-toggle="validator" method="post" action="" class="g24 form form-horizontal" enctype="multipart/form-data" autocomplete="off" id="form_list">
						 	<input type="hidden" id="status_to" name="status_to" value=""/>
					  <?php 
						$benefits_status = array('0'=>'รอการอนุมัติ', '1'=>'อนุมัติ', '5'=>'ไม่อนุมัติ');
						
						foreach($data as $key => $row ){ ?>
						  <tr> 
							  <td>
								<?php
									if($row['benefits_status']!='1') {  
								?>
							  	<input type="checkbox" class="req_check" id="req_check_<?php echo $row['benefits_request_id']; ?>" name="benefits_request_id[]" value="<?php echo $row['benefits_request_id']; ?>">
								<?php
									}
								?>
							  </td>
							  <td><?php echo $this->center_function->ConvertToThaiDate($row['createdatetime']); ?></td>
							  <td><?php echo $row['benefits_name']; ?></td>
							  <td><?php echo $row['firstname_th']." ".$row['lastname_th']; ?></td> 
							  <td><a class="text-edit" onclick="edit_request('<?php echo @$row['benefits_request_id'] ?>','<?php echo @$row['member_id'] ?>')"><?php echo $row['benefits_no']; ?></a></td> 
							  <td><?php echo number_format($row['benefits_approved_amount'],2); ?></td> 
							  <td><?php echo $row['user_name']; ?></td> 
							  <td><span id="benefits_status_<?php echo $row['benefits_request_id']; ?>" ><?php echo $benefits_status[$row['benefits_status']]; ?></span></td>
							  <td style="font-size: 14px;">
								<?php 
									if($row['benefits_status']=='0'){
								?>
									<a class="btn-radius btn-info" id="approve_<?php echo $row['benefits_request_id']; ?>_1" title="อนุมัติ" onclick="approve_benefits('<?php echo $row['benefits_request_id']; ?>','1')">
										อนุมัติ
									</a>
									<a class="btn-radius btn-danger" id="approve_<?php echo $row['benefits_request_id']; ?>_1" title="ไม่อนุมัติ" onclick="approve_benefits('<?php echo $row['benefits_request_id']; ?>','5')">
										ไม่อนุมัติ
									</a>
								<?php } ?>
							  </td>
						  </tr>
					  <?php } ?>
					  	</form>
					  </tbody> 
					  </table> 
					</div>
			  </div>
		</div>
		<?php echo @$paging ?>
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
				<form data-toggle="validator" method="post" action="" class="g24 form form-horizontal" enctype="multipart/form-data" autocomplete="off" id="from_save">
					<input type="hidden" name="benefits_request_id" id="benefits_request_id" value=""/>
					<div class="g24-col-sm-24 m-t-1">
						<div class="form-group">
							<label class="g24-col-sm-6 control-label">รหัสสมาชิก <span id="naja"></span> </label>
							<div class="g24-col-sm-6">
								<div class="form-group">
									<input id="member_id" name="member_id" class="form-control" style="text-align:left;" type="number" value="" readonly="readonly" required title="กรุณาป้อน รหัสสมาชิก" />
								</div>
							</div>
							<label class="g24-col-sm-3 control-label" for="budget_year">ชื่อสกุล</label>
							<div class="g24-col-sm-8">
								<div class="form-group">
									<input type="text" class="form-control" name="member_name" id="member_name" value=""  readonly="readonly">
								</div>
							</div>
						</div>
						<div class="form-group">
							<label class="g24-col-sm-6 control-label" for="birthday"> วันเกิด </label>
							<div class="g24-col-sm-6" id="birthday_con">
								<div class="form-group">
									<input type="text" class="form-control" name="birthday" id="birthday" value=""  readonly="readonly">
								</div>
							</div>
							
							<label class="g24-col-sm-3 control-label">อายุ</label>
							<div class="g24-col-sm-6">
								<div class="form-group" id="birthday_border">
									<input type="text" class="form-control" name="age" id="age" value=""  readonly="readonly">
								</div>
							</div>
						</div>
						
						<div class="form-group">
							<label class="g24-col-sm-6 control-label">วันที่เข้าเป็นสมาชิก </label>
							<div class="g24-col-sm-6">
								<div class="form-group">
									<input type="text" class="form-control" name="apply_date" id="apply_date" value=""  readonly="readonly">
								</div>
							</div>
							<label class="g24-col-sm-3 control-label">อายุสมาชิก </label>
							<div class="g24-col-sm-6">
								<div class="form-group">
									<input type="text" class="form-control" name="apply_age" id="apply_age" value=""  readonly="readonly">
								</div>
							</div>
						</div>
						
						<div class="form-group">
							<label class="g24-col-sm-6 control-label">กำหนดอายุเกษียณ </label>
							<div class="g24-col-sm-4">
								<div class="form-group">
									<input type="text" class="form-control" name="retry_date" id="retry_date" value=""  readonly="readonly">
								</div>
							</div>
							<label class="g24-col-sm-3 control-label">สถานะการเกษียณ </label>
							<div class="g24-col-sm-8">
								<div class="form-group">
									<input type="text" class="form-control" name="retry_status" id="retry_status" value=""  readonly="readonly">
								</div>
							</div>
						</div>
						<div class="form-group">
							<label class="g24-col-sm-6 control-label">เลือกสวัสดิการ </label>
							<div class="g24-col-sm-18">
								<div class="form-group">
									<select name="benefits_type_id" id="benefits_type_id" class="form-control" style="width:50%;" onchange="change_type()" required title="กรุณาเลือก สวัสดิการ" readonly="readonly">
										<option value="">เลือกสวัสดิการ</option>
									<?php 
										if(!empty($benefits_type)){
											foreach($benefits_type as $key => $value){ ?>
											<option value="<?php echo $value['benefits_id']; ?>" <?php echo $value['benefits_id']==@$data['benefits_type_id']?'selected':''; ?>><?php echo $value['benefits_name']; ?></option>
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
									<div id="benefits_request_detail" style="background: #e0e0e0;border: 1px solid #e0e0e0;margin-top: 10px;margin: 5px 0px 5px 0px;width: 100%;height: 300px;padding: 5px;border-radius: 3px; overflow-y: scroll;"><?php echo @$row['benefits_request_detail']; ?></div>
								</div>
							</div>
						</div>
						<div class="form-group">
							<label class="g24-col-sm-6 control-label">แนบไฟล์คำร้อง </label>
							<div class="g24-col-sm-7">
								<!--<div class="g24-col-sm-12">
									<div class="form-group">
										<input type="file" class="form-control" name="benefits_request_file[]" value="" multiple>
									</div>
								</div>-->
								<div class="g24-col-sm-12">
									<button class="btn btn-primary btn-after-input" id="btn_show_file" type="button" onclick="show_file()" style="display:none;"><span>แสดงไฟล์แนบ</span></button>
									<button class="btn btn-danger btn-after-input" id="btn_show_not_file" type="button" style="display:none;"><span>ไม่พบไฟล์แนบ</span></button>
								</div>
							</div>
							<label class="g24-col-sm-4 control-label">ยอดเงินสวัสดิการที่อนุมัติ </label>
							<div class="g24-col-sm-5">
								<div class="form-group">
									<input type="number" class="form-control" name="benefits_approved_amount" id="benefits_approved_amount" value=""  required title="กรุณาป้อน ยอดเงินสวัสดิการที่อนุมัติ" readonly="readonly">
								</div>
							</div>
						</div>
						<div class="form-group">
							<label class="g24-col-sm-6 control-label"> &nbsp;</label>
							<div class="g24-col-sm-18">
								<label class="control-label">
									<input type="checkbox" id="benefits_check_condition" name="benefits_check_condition"  value="1" disabled="disabled">
									<span>ตรวจสอบแล้วผ่านเกณฑ์เงื่อนไข</span>								
									<span style="padding-left: 15px;">ผู้ตรวจสอบและทำรายการ  <span id="user_name"></span></span>
									<ib>
								</label>
								<input type="hidden" class="form-control" name="user_name_session" id="user_name_session" value="<?php echo $_SESSION['USER_NAME'];?>">								 
							</div>
						</div>
					</div>
				</form>
            </div>
			
            <div class="text-center m-t-1" style="padding-top:10px;">
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
									<input type="text" class="form-control" name="start_date_view" id="start_date_view" value=""  readonly="readonly">
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
<div class="modal fade" id="print_slip_modal" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-dialog-account">
	  <div class="modal-content data_modal">
		<div class="modal-header modal-header-confirmSave">
		  <button type="button" class="close" onclick="close_modal('print_slip_modal')">&times;</button>
		  <h2 class="modal-title">พิมพ์ใบนำจ่าย</h2>
		</div>
		<div class="modal-body">
			<div class="form-group g24-col-sm-24">
				<label class="g24-col-sm-6 control-label text-right"> วันที่ </label>
				<div class="g24-col-sm-12">
					<div class="input-with-icon">
						<div class="form-group">
							<input id="print_date" name="print_date" class="form-control m-b-1 mydate" style="padding-left: 50px;" type="text" value="<?php echo $this->center_function->mydate2date(date('Y-m-d')); ?>" data-date-language="th-th">
							<span class="icon icon-calendar input-icon m-f-1"></span>
						</div>
					</div>
				</div>
			</div>
			<div class="text-center" style="padding-top:10px;">
				<button class="btn btn-info" onclick="print_slip_submit('viewRequest')">พิมพ์</button>
            </div>
		</div>
	  </div>
	</div>
</div>
<script>
$( document ).ready(function() {
	$("#req_check_all").change(function() {
		if ($(this).is(':checked')) {
			$(".req_check").prop("checked", true)
		} else {
			$(".req_check").prop("checked", false)
		}
	});
	$(".req_check").change(function() {
		if ($('.req_check:checked').length == $('.req_check').length) {
			$("#req_check_all").prop("checked", true)
		} else {
			$("#req_check_all").prop("checked", false)
		}
	});

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
});
function approve_benefits(id, status_to){
	 swal({
		title: 'อนุมัติสวัสดิการ',
		text: "",
		type: "warning",
		showCancelButton: true,
		confirmButtonColor: '#0288d1',
		confirmButtonText: 'ยืนยัน',
		cancelButtonText: "ปิดหน้าต่าง",
		closeOnConfirm: false,
		closeOnCancel: true
	},
	function(isConfirm) {
		if (isConfirm) {
			document.location.href = base_url+'/benefits/benefits_approve?id='+id+'&status_to='+status_to;
		} else {
			
		}
	});
}
 
function view_request(){
	$('#viewRequest').modal('show');
}

function change_type_view(){
	var benefits_type_id = $("#benefits_type_id_view").val();
	$.ajax({
		type: "POST",
		url: base_url+'benefits/get_benefits_type',
		data: {
			id : benefits_type_id
		},
		success: function(msg) {
			response = $.parseJSON(msg);
			//console.log(response);
			if(response){
				$("#benefits_request_detail_view").html(response.benefits_detail);
				$("#start_date_view").val(response.start_date);
			}else{
				$("#benefits_request_detail_view").html('');
				$("#start_date_view").val('');
			}
		}
	});	
}

function close_modal(id){
	$('#'+id).modal('hide');
}

function show_file(){
	 $('#show_file_attach').modal('show');
}

function edit_request(benefits_request_id,member_id){
	$('#btn_show_file').hide();
	$('#btn_show_not_file').hide();
	$.ajax({
		type: "POST",
		url: base_url+'benefits/get_benefits_request',
		data: {
			id : benefits_request_id
		},
		success: function(msg) {
			response = $.parseJSON(msg);
			//console.log(response);			
			$("#benefits_request_id").val(response.benefits_request_id);
			$("#benefits_type_id").val(response.benefits_type_id);
			$("#benefits_approved_amount").val(response.benefits_approved_amount);
			$("#benefits_request_detail").html(response.benefits_detail);
			$("#user_name").html(response.user_name);
			$("#member_id").val(response.member_id);
			$("#member_name").val(response.firstname_th+' '+response.lastname_th);
			$("#birthday").val(response.birthday);
			$("#age").val(response.age);
			$("#apply_date").val(response.apply_date);
			$("#apply_age").val(response.apply_age);
			$("#retry_date").val(response.retry_date);
			$("#retry_status").val(response.retry_status);
			
			if(response.benefits_check_condition == '1'){
				$('#benefits_check_condition').prop('checked', true);
			}else{
				$('#benefits_check_condition').prop('checked', false);
			}	
			
			var txt_file_attach = '<table width="100%">';
			var i=1;
			console.log(response.coop_file_attach);
			for(var key in response.coop_file_attach){
				txt_file_attach += '<tr class="file_row" id="file_'+response.coop_file_attach[key].id+'">\n';
				txt_file_attach += '<td><a href="'+base_url+'/assets/uploads/benefits_request/'+response.coop_file_attach[key].file_name+'" target="_blank">'+response.coop_file_attach[key].file_old_name+'</a></td>\n';
				txt_file_attach += '<td style="color:red;font-size: 20px;cursor:pointer;" align="center" width="10%"></td>\n';
				//txt_file_attach += '<td style="color:red;font-size: 20px;cursor:pointer;" align="center" width="10%"><span class="icon icon-ban" onclick="del_file(\''+response.coop_file_attach[key].id+'\')"></span></td>\n';
				txt_file_attach += '</tr>\n';
				i++;
			}
			txt_file_attach += '</table>';
			$('#show_file_space').html(txt_file_attach);
			if(i>1){
				$('#btn_show_file').show();
			}else{
				$('#btn_show_not_file').show();				
			}
		}
	});
	$('#myModalRequest').modal('show');
}

function approve_request(){
	event.preventDefault();
	swal({
		title: 'อนุมัติสวัสดิการ',
		text: "",
		type: "warning",
		showCancelButton: true,
		confirmButtonColor: '#0288d1',
		confirmButtonText: 'ยืนยัน',
		cancelButtonText: "ปิดหน้าต่าง",
		closeOnConfirm: false,
		closeOnCancel: true
	},
	function(isConfirm) {
		if (isConfirm) {
			$("#status_to").val(1)
			$("#form_list").submit()
		} else {
		}
	});
}

function delete_request() {
	event.preventDefault();
	swal({
		title: 'ลบคำขอสวัสดิการ',
		text: "",
		type: "warning",
		showCancelButton: true,
		confirmButtonColor: '#0288d1',
		confirmButtonText: 'ยืนยัน',
		cancelButtonText: "ปิดหน้าต่าง",
		closeOnConfirm: false,
		closeOnCancel: true
	},
	function(isConfirm) {
		if (isConfirm) {
			$("#status_to").val("del")
			$("#form_list").submit()
		} else {
		}
	});
}

function print_slip_modal() {
	event.preventDefault();
	$('#print_slip_modal').modal('show');
}

function print_slip_submit() {
	event.preventDefault();
	$('#print_slip_modal').modal('hide');
	print_date = $("#print_date").val()
	data = $("#form_list").serialize() + "&print_date=" + print_date
	window.open(base_url+"report_benefits_data/coop_report_benefits_slip?"+data, '_blank');
}
</script>