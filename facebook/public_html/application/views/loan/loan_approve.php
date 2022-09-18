<div class="layout-content">
    <div class="layout-content-body">
<style>
	.modal-header-alert {
		padding:9px 15px;
		border:1px solid #FF0033;
		background-color: #FF0033;
		color: #fff;
		-webkit-border-top-left-radius: 5px;
		-webkit-border-top-right-radius: 5px;
		-moz-border-radius-topleft: 5px;
		-moz-border-radius-topright: 5px;
		border-top-left-radius: 5px;
		border-top-right-radius: 5px;
	}
	.center {
		text-align: center;
	}
	.modal-dialog-account {
		margin:auto;
		margin-top:7%;
	}
    .form-group{
        margin-bottom: 5px;
    }
</style>
<h1 class="title_top">อนุมัติเงินกู้</h1>
<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 padding-l-r-0">
    <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 padding-l-r-0">
        <?php $this->load->view('breadcrumb'); ?>
    </div>
	<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 padding-l-r-0">
		<?php
		$get_param = '?';
		foreach(@$_GET as $key => $value){
			if($key != 'month' && $key != 'year' && $value != ''){
				$get_param .= $key.'='.$value.'&';
			}
		}
		$get_param = substr($get_param,0,-1);
		?>
		<a class="btn btn-primary btn-lg bt-add" target="_blank" href="<?php echo base_url(PROJECTPATH.'/report_loan_data/loan_ready_to_transfer_report'.$get_param); ?>">
			 รายงานการสั่งจ่ายเงินกู้
		</a>
        <a class="btn btn-primary btn-lg bt-add" style="margin-right: 10px;" target="_blank" href="<?php echo base_url(PROJECTPATH.'/report_loan_request/report_loan_request_excel'.$get_param); ?>">
            รายงานคำขอกู้
        </a>
	</div>
</div>
<div class="row gutter-xs">
        <div class="col-xs-12 col-md-12">
                <div class="panel panel-body" style="padding-top:0px !important;">
		  <h3 >รายการขออนุมัติเงินกู้</h3>
				<form method="GET" action="">
					<div class="g24-col-sm-24">
						<label class="g24-col-sm-2 control-label">ประเภทเงินกู้</label>
						<div class="g24-col-sm-3 m-b-1">
							<select class="form-control" name="loan_type" id="loan_type" onchange="change_type()">
								<option value="">เลือกประเภทเงินกู้</option>
								<?php foreach($loan_type as $key => $value){ ?>
									<option value="<?php echo $key; ?>" <?php echo $key == @$_GET['loan_type']?'selected':'';?>><?php echo $value; ?></option>
								<?php } ?>
							</select>
						</div>
						<label class="g24-col-sm-1 control-label">ชื่อเงินกู้</label>
						<div class="g24-col-sm-3 m-b-1">
							<select class="form-control" name="loan_name" id="loan_name">
								<option value="">เลือกชื่อเงินกู้</option>
								<?php foreach($loan_name as $key => $value){ ?>
									<option value="<?php echo $key; ?>" <?php echo $key==@$_GET['loan_name']?'selected':''; ?>><?php echo $value; ?></option>
								<?php } ?>
							</select>
						</div>

						<div class="g24-col-sm-3">
							<div class="form-group">
								<select class="form-control" id="key" name="search_key_date">
									<option value="createdatetime">วันที่ทำรายการขอกู้</option>
									<option value="approve_date">วันที่สั่งจ่ายเงินกู้</option>
								</select>
							</div>
						</div>
						<div class="input-with-icon g24-col-sm-3">
							<div class="form-group">
								<input id="start_date" name="start_date" class="form-control m-b-1 form_date_picker" type="text" value="<?php echo (@$_GET['start_date'] != '')?@$_GET['start_date']:''; ?>" data-date-language="th-th" autocomplete="off">
								<span class="icon icon-calendar input-icon m-f-1"></span>
							</div>
						</div>
						<label class="g24-col-sm-1 control-label datepicker1 text-center" style="text-align:center" for="thru_date">ถึง</label>
						<div class="input-with-icon g24-col-sm-3">
							<div class="form-group">
								<input id="thru_date" name="thru_date" class="form-control m-b-1 form_date_picker" type="text" value="<?php echo (@$_GET['thru_date'] != '')?@$_GET['thru_date']:''; ?>" data-date-language="th-th" autocomplete="off">
								<span class="icon icon-calendar input-icon m-f-1"></span>
							</div>
						</div>

						<label class="g24-col-sm-1 control-label">สถานะ</label>
						<div class="g24-col-sm-2 m-b-1">
							<select class="form-control" name="loan_status" id="loan_status">
								<option value="">ทั้งหมด</option>
								<option value="0" <?php echo @$_GET['loan_status']=='0'?'selected':''; ?>>รออนุมัติ</option>
								<option value="1" <?php echo @$_GET['loan_status']=='1'?'selected':''; ?>>อนุมัติ</option>
								<option value="5" <?php echo @$_GET['loan_status']=='5'?'selected':''; ?>>ไม่อนุมัติ</option>
							</select>
						</div>
						<div class="g24-col-sm-1">
							<input type="submit" class="btn btn-primary" value="ค้นหา">
						</div>
					</div>
				</form>
             <table class="table table-bordered table-striped table-center">
             <thead> 
                <tr class="bg-primary">
					<th>วันที่ทำรายการ</th>
					<th>วันที่สั่งจ่ายเงินกู้</th>
					<th>รหัสสมาชิก</th>
					<th>ชื่อสมาชิก</th>
					<th>เลขที่คำร้อง</th>
					<th>ยอดเงินกู้</th>
					<th>ผู้ทำรายการ</th>
					<th>สถานะ</th>
					<th>จัดการ</th> 
                </tr> 
             </thead>
                <tbody id="table_first">
                  <?php 
					$loan_status = array('0'=>'รอการอนุมัติ', '1'=>'อนุมัติ', '5'=>'ไม่อนุมัติ');
					if(!empty($data)){
					foreach($data as $key => $row ){ 
						
						$today = date("Y-m-d");
						$loan_update = (empty($row['updatetimestamp']))?"":date("Y-m-d", strtotime(@$row['updatetimestamp']));
						$check_date = ($today == $loan_update)?"Y":"N";
						
					?>
						  <tr> 
							  <td><?php echo $this->center_function->ConvertToThaiDate($row['createdatetime']); ?></td>
							  <td><?php echo $this->center_function->ConvertToThaiDate($row['approve_date']); ?></td>
							  <td><?php echo $row['member_id']; ?></td>
							  <td><?php echo $row['firstname_th']." ".$row['lastname_th']; ?></td> 
							  <td>
								<a href='<?php echo base_url(PROJECTPATH.'/loan/'.@$row['petition_file'].'/'.$row['id']); ?>' target='_blank'><?php echo $row['petition_number']; ?></a> | 
								<?php //if(@$row['deduct_receipt_id'] == ''){?>
								<a title="เอกสารพิจารณาเงินกู้" style="cursor: pointer;padding-left:2px;padding-right:2px" href="<?php echo PROJECTPATH."/report_loan_data/coop_report_loan_detail_preview?member_id=".$row['member_id']."&loan_id=".$row['id']; ?>" target="_blank"><span class="icon icon-list-alt"></span></a>
								<?php //} ?>
								<?php if($row['loan_status']=='0'){ ?>
								<a title="แก้ไข" style="cursor: pointer;padding-left:2px;padding-right:2px" href="<?php echo PROJECTPATH."/loan/index?member_id=".$row['member_id']."&loan_id=".$row['id']."&loan_type=".$row['loan_type']; ?>"><span class="icon icon-pencil"></span></a>
								<?php } ?>
							  </td> 
							  <td><?php echo number_format($row['loan_amount'],2); ?></td> 
							  <td><?php echo $row['user_name']; ?></td> 
							  <td><span id="loan_status_<?php echo $row['id']; ?>" ><?php echo $loan_status[$row['loan_status']]; ?></span></td>
							  <td style="font-size: 14px;">
								<?php 
									if($row['loan_status']=='0'){
								?>
									<?php if($row['loan_group_id'] == "3"){ ?>
										<a class="btn btn-info"  title="อนุมัติ" href="<?php echo base_url("/installment/index/".$row['id'])?>" target="_blank">
											<!--span style="cursor: pointer;" class="icon icon-check-square-o"></span-->
											อนุมัติ
										</a>
									<?php }else{ ?>
										<a class="btn btn-info" id="approve_<?php echo $row['id']; ?>_1" title="อนุมัติ" onclick="approve_loan('<?php echo $row['id']; ?>','1','<?php echo $check_date;?>')">
											<!--span style="cursor: pointer;" class="icon icon-check-square-o"></span-->
											อนุมัติ
										</a>
								  	<?php } ?>
									<a class="btn btn-danger" id="approve_<?php echo $row['id']; ?>_1" title="ไม่อนุมัติ" onclick="approve_loan('<?php echo $row['id']; ?>','5')">
										<!--span style="cursor: pointer;" class="icon icon-check-square-o"></span-->
										ไม่อนุมัติ
									</a>
								<?php }else if($_GET['loan_status']!='5'){ 
									if($row['deduct_receipt_id']!=''){
										$token = sha1(md5($row['id']));
								?>
										<a href="<?php echo base_url(PROJECTPATH.'/admin/receipt_form_pdf/'.$row['deduct_receipt_id']); ?>" target="_blank">ใบเสร็จ</a>
										|
										<a class="text-danger" href="#" onclick="rollback(<?php echo $row['id']; ?>, '<?php echo $row['firstname_th'].' '.$row['lastname_th']; ?>', '<?php echo number_format($row['loan_amount'],2); ?>', '<?php echo $this->center_function->ConvertToThaiDate($row['approve_date']); ?>', '<?=$token?>')" >ยกเลิก</a>
									<?php } ?>
								<?php } ?>
							  </td>
						  </tr>
                  <?php 
						}
					}else{
					?>
						<tr> 
							<td colspan="7" class="text-center">ไม่พบข้อมูล</td>
						</tr>  
					<?php	
					}
					?>
                  </tbody> 
                  </table> 
          </div>
          </div>
                </div>
                  <?php echo $paging ?>
	</div>
</div>

<div id="date_approve_modal" tabindex="-1" role="dialog" class="modal fade">
	<div class="modal-dialog modal-sm" style="width:400px;">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">กำหนดวันที่อนุมัติ</h4>
			</div>
			<div class="modal-body">
				<div class="row m-b-1">
					<div class="form-group">
						<input type="hidden" name="loan_id" id="loan_id">
						<input type="hidden" name="status_to" id="status_to">
						<label for="money" class="control-label g24-col-sm-2"></label>
						<div class="g24-col-sm-6 text-right" style="margin-bottom: 5px;padding-top: 5px;">
							วันที่อนุมัติ
						</div>
						<div class="g24-col-sm-14" style="margin-bottom: 5px;padding-top: 5px;">
							<div class="input-with-icon g24-col-sm-24">
								<div class="form-group">
									<input id="date_approve" name="date_approve" class="form-control m-b-1" style="padding-left: 50px;" type="text" data-date-language="th-th" value="<?php echo $this->center_function->mydate2date(date('Y-m-d'));?>" title="กรุณาป้อน วันที่">
									<span class="icon icon-calendar input-icon m-f-1"></span>
								</div>
							</div>
						</div>
					</div>
				</div>
				<br>
				<div class="row">
					<div class="col-sm-12 text-center">
						<button class="btn btn-info" id="submit_approve_close">ยืนยัน</button>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<script>
 function approve_loan(id, status_to,check_date=""){
	 var text_alert = "";
		if(status_to == '1'){
			if(check_date == "Y"){
				text_alert = "อนุมัติการกู้เงิน";
			}else{
				text_alert = "กรุณาอัปเดตคำร้องอีกครั้งก่อนทำการอนุมัติ";
			}		
		}else{
			text_alert = "ไม่อนุมัติการกู้เงิน";
		}
		
	 swal({
        title: text_alert,
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
            //document.location.href = base_url+'/loan/loan_approve_save?loan_id='+id+'&status_to='+status_to;
			swal.close();			
			$('#loan_id').val(id);
			$('#status_to').val(status_to);
			$('#date_approve_modal').modal("show");
			
        } else {
			
        }
    });
 }
 function change_type(){
	$.ajax({
		url: base_url+'loan/change_loan_type',
		method: 'POST',
		data: {
			'type_id': $('#loan_type').val()
		},
		success: function(msg){
		   $('#loan_name').html(msg);
		}
	});		
	$('#type_name').val($('#type_id :selected').text());
}

	function rollback(loan_id, name, loan_amount, date, token){
		event.preventDefault(); // prevent form submit
		swal({
			title: "ต้องการยกเลิกรายการนี้หรือไม่ ?",
			text: "ผู้กู้ :: "+name+"\n วันที่อนุมัติ :: "+date+"\n ยอดกู้ :: "+loan_amount,
			type: "warning",
			showCancelButton: true,
			confirmButtonColor: "#DD6B55",
			confirmButtonText: "ตกลง",
			cancelButtonText: "ปิด",
			closeOnConfirm: false,
			closeOnCancel: false
		},
		function(isConfirm){
			if (isConfirm) {
				window.location.href = base_url+"loan/rollback_loan/"+loan_id+"/"+token;
			} else {
				swal.close();
			}
		});
	}

$(document).ready(function() {
	$(".form_date_picker").datepicker({
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
	
	$("#date_approve").datepicker({
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

	function addCommas(x){
		x = x || "";
		return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
	}

	var is_btn_clicked = true;
	$("#submit_approve_close").on('click', function (){
		if(is_btn_clicked) {
			is_btn_clicked = false;
			var loan_id = $('#loan_id').val();
			var status_to = $('#status_to').val();
			var date_approve = $('#date_approve').val();
			new Promise(resolve => {
				$.get(base_url + "loan/loan_contract", {loan_id: loan_id, date_approve: date_approve}, function (data) {
					resolve(data);
					return;
				});
			}).then((data) => {
				let txt = "เลขสัญญา :\t" + data.contract.contract_year + data.contract.run_contract_number.toString().padStart(6, '0') + " \n";
				txt += "ยอดโอน :\t" + addCommas(data.transfer_total) + " บาท \n";
				txt += "ยอดรับสุทธิ์\t: " + addCommas(data.estimate_receive_money) + " บาท \n";
				swal({
					title: "กรุณาตรวจสอบข้อมูลในสัญญา",
					text: txt,
					type: "warning",
					showCancelButton: true,
					confirmButtonColor: "#DD6B55",
					confirmButtonText: "ตกลง",
					cancelButtonText: "ปิด",
					closeOnConfirm: true,
					closeOnCancel: true
				}, function (isConfirm) {
					if (isConfirm) {
						$.ajax({
							url: base_url + 'loan/loan_approve_save',
							method: 'GET',
							data: {
								loan_id: loan_id,
								status_to: status_to,
								date_approve: date_approve
							},
							success: function (data) {
								swal(data);
								is_btn_clicked = true;
								document.location.href = base_url + '/loan/loan_approve';
							}
						});
					}else{
						is_btn_clicked = true;
					}
				});
			});
		}
	});
});
</script>
