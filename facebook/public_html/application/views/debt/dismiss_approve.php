<div class="layout-content">
	<div class="layout-content-body">
<style>
	.modal-header-confirmSave {
		padding:9px 15px;
		color: #fff;
		-webkit-border-top-left-radius: 5px;
		-webkit-border-top-right-radius: 5px;
		-moz-border-radius-topleft: 5px;
		-moz-border-radius-topright: 5px;
		border-top-left-radius: 5px;
		border-top-right-radius: 5px;
	}
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
	.modal-fired {
		width: 800px;
	}
  .form-group{
    margin-bottom: 5px;
  }
</style>
<h1 class="title_top">อนุมัติการให้ออก</h1>
		<?php $this->load->view('breadcrumb'); ?>
<div class="row gutter-xs">
        <div class="col-xs-12 col-md-12">
			<div class="panel panel-body" style="padding-top:0px !important;">
		    <h3 >รายการยื่นขอลาออก</h3>
			<form action="" method="GET" id="form_search" autocomplete="off">
				<div class="form-group g24-col-sm-24">
					<label class="g24-col-sm-4 control-label right"> หมายเลขสมาชิก </label>
					<div class="g24-col-sm-4">
						<input class='form-control' type='text' id="member_id_search" name="member_id" value='<?php echo !empty($_GET["member_id"]) ? $_GET["member_id"] : ""?>'>
					</div>
				</div>
				<div class="form-group g24-col-sm-24">
					<label class="g24-col-sm-4 control-label right"> ช่วงการค้นหา </label>
					<div class="g24-col-sm-4">
						<select class="form-control m-b-1" id="search_type"  name="search_type">
							<option value="">เลือก</option>
							<option value="1" <?php echo $_GET["search_type"] == '1' ? "selected" : ""?>>วันที่ลาออก</option>
							<option value="2" <?php echo $_GET["search_type"] == '2' ? "selected" : ""?>>วันที่สิ้นสภาพ</option>
						</select>
					</div>
					<label class="g24-col-sm-1 control-label right search_date_group"> วันที่ </label>
					<div class="g24-col-sm-4 search_date_group">
						<div class="input-with-icon">
							<div class="form-group">
								<input id="start_date" name="start_date" class="form-control m-b-1 form_date_picker" style="padding-left: 50px;" type="text" value="<?php echo ($_GET['start_date'] != '')?$_GET['start_date']:$this->center_function->mydate2date(date('Y-m-d')); ?>" data-date-language="th-th">
								<span class="icon icon-calendar input-icon m-f-1"></span>
							</div>
						</div>
					</div>
					<label class="g24-col-sm-1 control-label right search_date_group"> ถึง </label>
					<div class="g24-col-sm-4 search_date_group">
						<div class="input-with-icon">
							<div class="form-group">
								<input id="end_date" name="end_date" class="form-control m-b-1 form_date_picker" style="padding-left: 50px;" type="text" value="<?php echo ($_GET['end_date'] != '')?$_GET['end_date']:$this->center_function->mydate2date(date('Y-m-d')); ?>" data-date-language="th-th">
								<span class="icon icon-calendar input-icon m-f-1"></span>
							</div>
						</div>
					</div>
				</div>
				<div class="form-group g24-col-sm-24">
					<label class="g24-col-sm-4 control-label right"> สถานะ </label>
					<div class="g24-col-sm-4">
						<select name="resign_status" id="resign_status" class="form-control">
							<option value="">ทั้งหมด</option>
							<option value="0" <?php echo $_GET["resign_status"] == '0' ? "selected" : ""?>>ยื่นคำร้อง</option>
							<option value="1" <?php echo $_GET["resign_status"] == '1' ? "selected" : ""?>>อนุมัติ</option>
							<option value="2" <?php echo $_GET["resign_status"] == '2' ? "selected" : ""?>>ไม่อนุมัติ</option>
						</select>
					</div>
				</div>
				<div class="form-group g24-col-sm-24">
					<label class="g24-col-sm-4 control-label right"></label>
					<div class="g24-col-sm-4">
						<input type="submit" class="btn btn-primary" style="width:100%" value="ค้นหา">
					</div>
				</div>
			</form>
            <table class="table table-bordered table-striped table-center">
             <thead> 
                <tr class="bg-primary">
					<th>วันที่ขอลาออก</th>
					<th>เลขที่คำร้อง</th>
					<th>รหัสสมาชิก</th>
					<th>ชื่อสกุลสมาชิก</th>
					<th>วันสิ้นสภาพ</th>
					<th>สาเหตุการลาออก</th>
					<th>หมายเหตุ</th>
					<th>มติที่ประชุม</th>
					<th>ผู้ทำรายการ</th>
					<th>สถานะ</th>
					<th>จัดการ</th>
                </tr> 
             </thead>
                <tbody >
                  <?php
					$req_resign_status = array('0'=>'ยื่นคำร้อง','1'=>'อนุมัติ','2'=>'ไม่อนุมัติ');
					
					foreach($row as $key => $value){ ?>
					  <tr> 
						  <td><?php echo $this->center_function->ConvertToThaiDate($value['req_resign_date'],'1','0'); ?></td>
						  <td><?php echo $value['req_resign_no']; ?></td>
						  <td><?php echo $value['member_id']; ?></td>
						  <td><?php echo $value['firstname_th']." ".$value['lastname_th']; ?></td>
						  <td><?php echo $this->center_function->ConvertToThaiDate($value['resign_date'],'1','0'); ?></td>
						  <td><?php echo $value['resign_cause_name']; ?></td>
						  <td><?php echo $value['remark']; ?></td>
						  <td><?php echo $value['conclusion']; ?></td>
						  <td><?php echo $value['user_name']; ?></td>
						  <td><?php echo $req_resign_status[$value['req_resign_status']]; ?></td>
						  <td>
						  <?php if($value['req_resign_status'] == '0'){ ?>
							<?php
								if($value['check_debt'] == 1) {
								
							?>
						 	<a style="cursor:pointer;" onclick="open_approve_fire_modal('1','<?php echo $value['req_resign_id']; ?>','<?php echo $value['conclusion']; ?>','<?php echo $value['member_id']; ?>')">อนุมัติ</a>
							<?php
								} else {
							?>
							<a style="cursor:pointer;" onclick="open_approve_modal('1','<?php echo $value['req_resign_id']; ?>','<?php echo $value['conclusion']; ?>')">อนุมัติ</a>
							<?php
								}
							?>
						  | 
						  <a style="color:red;cursor:pointer;" onclick="open_approve_modal('2','<?php echo $value['req_resign_id']; ?>','<?php echo $value['conclusion']; ?>')">ไม่อนุมัติ</a>
						  <?php }else if ($value['req_resign_status'] == '1'){ ?>	
						  <?php if(!empty($value['receipt_id'])) { ?>					  
						  <a style="cursor:pointer;" href="<?php echo base_url(PROJECTPATH.'/admin/receipt_form_pdf/'.$value['receipt_id']); ?>" target="_blank">ใบเสร็จรับเงิน</a>	
						  |
						  <?php } ?>	 
						  <a style="cursor:pointer;" href="<?php echo base_url(PROJECTPATH.'/report_member_data/coop_report_member_people_retire_preview?member_id='.$value['member_id']); ?>" target="_blank">รายงานการลาออก</a>
						  <?php
						 	if($value['check_debt'] == 1) {
						  ?>
						  |
						  <a style="cursor:pointer;" href="<?php echo base_url(PROJECTPATH.'/resignation/coop_report_resign_debt_interest_payment?member_id='.$value['member_id']); ?>" target="_blank">รายงานการชำระดอกคงค้าง</a>
						  <?php
							}
							} 
						  ?>
						  </td>
					  </tr>
                  <?php } ?>
                  </tbody> 
                  </table> 
          </div>
          </div>
                </div>
                  <?php echo $paging ?>
	  </div>
</div>
<div class="modal fade" id="approve_modal" role="dialog">
    <div class="modal-dialog modal-dialog-data">
      <div class="modal-content data_modal">
        <div id="modal_head" class="modal-header modal-header-confirmSave">
          <button type="button" class="close" onclick="close_modal('approve_modal')">&times;</button>
          <h2 class="modal-title" id="modal_title">อนุมัติ</h2>
        </div>
        <div class="modal-body">
			<form action="" method="POST" id="form_approve">
				<input type="hidden" name="req_resign_id" id="req_resign_id">
				<input type="hidden" name="req_resign_status" id="req_resign_status">
				<div class="form-group g24-col-sm-24">
					<label class="g24-col-sm-5 control-label ">มติที่ประชุม</label>
					<div class="g24-col-sm-14">
						<textarea id="conclusion" name="conclusion" class="form-control" ></textarea>
					</div>
				</div>
				<div class="row ">
					<div class="form-group text-center">
						<button type="submit" id="btn_save" class="btn btn-primary min-width-100 m-t-2">บันทึก</button>
						<button class="btn btn-default min-width-100 m-t-2" type="button" onclick="close_modal('approve_modal')">ปิดหน้าต่าง</button>
					</div>
				</div>
			</form>
			<table><tr><td>&nbsp;</td></tr></table>
        </div>
      </div>
    </div>
</div>
<div class="modal fade" id="approve_fired_modal" role="dialog">
    <div class="modal-dialog modal-dialog-data modal-fired">
		<div class="modal-content data_modal">
			<div id="modal_head" class="modal-header modal-header-confirmSave">
				<button type="button" class="close" onclick="close_modal('approve_fired_modal')">&times;</button>
				<h2 class="modal-title" id="modal_title">อนุมัติ</h2>
			</div>
			<div class="modal-body">
				<form action="" method="POST" id="form_fired_approve">
					<input type="hidden" name="req_resign_id" id="req_resign_id_fire">
					<input type="hidden" name="req_resign_status" id="req_resign_status_fire">
					<input type="hidden" id="income_balance" value=''>
					<input type="hidden" name="is_fired_process" value="1">
					<input type="hidden" id="debt_total" value="0">
					<div class="form-group g24-col-sm-24" >
						<label class="g24-col-sm-5 control-label ">รายรับรวม</label>
						<div class="g24-col-sm-5">
							<input class='form-control' type='text' id="income_balance_text" value='' readonly>
						</div>
						<label class="g24-col-sm-5 control-label ">คงเหลือ</label>
						<div class="g24-col-sm-5">
							<input class='form-control' type='text' id="income_balance_left" value='' readonly>
						</div>
					</div>
					<div id="loan-list" class="form-group g24-col-sm-24">
						<table class="table" id="loan-list-table">
							<tr>
								<th class='text-center'>เลขที่สัญญา</th>
								<th class='text-center'>เงินต้น</th>
								<th class='text-center'>ดอกเบี้ย</th>
								<th class='text-center'>ดอกเบี้ยคงค้าง</th>
							</tr>
						</table>
					</div>
					<div class="form-group g24-col-sm-24">
						<label class="g24-col-sm-5 control-label ">มติที่ประชุม</label>
						<div class="g24-col-sm-14">
							<textarea id="conclusion" name="conclusion" class="form-control" ></textarea>
						</div>
					</div>
					<div class="row ">
						<div class="form-group text-center">
							<button type="button" id="fired_approve_submit" class="btn btn-primary min-width-100 m-t-2">บันทึก</button>
							<button class="btn btn-default min-width-100 m-t-2" type="button" onclick="close_modal('approve_fired_modal')">ปิดหน้าต่าง</button>
						</div>
					</div>
				</form>
				<table>
					<tr>
						<td>&nbsp;</td>
					</tr>
				</table>
			</div>
		</div>
    </div>
</div>
<script>
 function open_approve_modal(status_to, req_resign_id, conclusion){
	 $('#req_resign_status').val(status_to);
	 $('#req_resign_id').val(req_resign_id);
	 $('#conclusion').html(conclusion);
	 if(status_to == '1'){
		 $('#modal_title').html('อนุมัติ');
		 $('#modal_head').attr('class','modal-header modal-header-confirmSave');
		 $('#btn_save').attr('class','btn btn-primary min-width-100 m-t-2');
	 }else{
		 $('#modal_title').html('ไม่อนุมัติ');
		 $('#modal_head').attr('class','modal-header modal-header-alert');
		 $('#btn_save').attr('class','btn btn-danger min-width-100 m-t-2');
	 }
	 $('#approve_modal').modal('show');
 }
 function open_approve_fire_modal(status_to, req_resign_id, conclusion, member_id){
	$('#req_resign_status_fire').val(status_to);
	$('#req_resign_id_fire').val(req_resign_id);
	$.ajax({
			method: 'GET',
			url: base_url+'resignation/get_member_loans?member_id='+member_id,
			success: function(result){
				console.log(result)
				data = JSON.parse(result)

				$("#debt_total").val(data.debt_total)

				loans = data.loans
				$("#loan-list-table").html('')

				var tr = $("<tr></tr>")
				var td0 = $("<td class='text-center'>เลขสัญญา</td>")
				var td1 = $("<td class='text-center'>เงินต้น</td>")
				var td2 = $("<td class='text-center'>ดอกเบี้ย</td>")
				var td3 = $("<td class='text-center'>ดอกคงค้าง</td>")
				tr.append(td0)
				tr.append(td1)
				tr.append(td2)
				tr.append(td3)
				$("#loan-list-table").append(tr)

				for(i = 0; i < loans.length; i++) {
					loan = loans[i]
					var tr = $("<tr></tr>")
					var td0 = $("<td class='text-left'>"+loan.contract_number+"</td>")
					var td1 = $("<td class='text-right'>"+loan.loan_balance_text+"</td>")
					var td2 = $("<td class='text-right'>"+loan.interest_text+"</td>")
					var td3 = $("<td class='text-right'>"+loan.interest_dept_text+"</td>")
					tr.append(td0)
					tr.append(td1)
					tr.append(td2)
					tr.append(td3)
					$("#loan-list-table").append(tr)
					var tr = $("<tr></tr>")
					var td0 = $("<td class='text-left'>ชำระ</td>")
					var td1 = $("<td class='text-right'><input class='form-control input-payment' data-warning='เงินต้นสัญญา"+loan.contract_number+"' data-max='"+loan.loan_balance+"' type='text' name='"+loan.id+"_"+loan.type+"_loan_balance' value='' ></td>")
					var td2 = $("<td class='text-right'><input class='form-control input-payment' data-warning='ดอกเบี้ยสัญญา"+loan.contract_number+"' data-max='"+loan.interest+"' type='text' name='"+loan.id+"_"+loan.type+"_interest_text' value='' ></td>")
					var td3 = $("<td class='text-right'><input class='form-control input-payment input-interest-debt' data-warning='ดอกเบี้ยคงค้างสัญญา"+loan.contract_number+"' data-max='"+loan.interest_dept+"' type='text' name='"+loan.id+"_"+loan.type+"_interest_dept_text' value='' ></td>")
					tr.append(td0)
					tr.append(td1)
					tr.append(td2)
					tr.append(td3)
					$("#loan-list-table").append(tr)
				}

				$("#income_balance_left").val(data.income_balance_text)
				$("#income_balance_text").val(data.income_balance_text)
				$("#income_balance").val(data.income_balance)
			}
	});
	$('#approve_fired_modal').modal('show');
 }
 function close_modal(id){
	 $('#'+id).modal('hide');
 }

$('#fired_approve_submit').click(function(){
	var total_payment = parseFloat(0)

	text_alert = "";
	$(".input-payment").each(function(index) {
		if($(this).val()) total_payment += parseFloat($(this).val())
		if(parseFloat($(this).val()) > parseFloat($(this).attr('data-max')) || ($(this).attr('data-max') == 'null' && $(this).val() > 0)) {
			text_alert += ' - ยอดชำระ'+$(this).attr('data-warning')+'มากกว่าจำนวนที่สามารถชำระได้\n';
		}
	});

	if(total_payment > parseFloat($("#income_balance").val())) {
		text_alert += ' - จำนวนที่ต้องการชำระมากกว่ารายรับรวม\n';
	} else if (total_payment != parseFloat($("#income_balance").val()) && (total_payment < parseFloat($("#debt_total").val()))) {
		remain = parseFloat($("#income_balance").val()) - total_payment
		text_alert += " - ยังเหลือรายรับที่สามารถชำระหนี้ได้เป็นจำนวน "+$("#income_balance_left").val()+"\n";
	}

	if(text_alert != ''){
		swal('ไม่สามารถบันทึกข้อมูลได้',text_alert,'warning');
	}else{
		interest_debt_warning = "";
		$(".input-interest-debt").each(function(index) {
			if($(this).attr('data-max') != 'null' && parseFloat($(this).val()) != parseFloat($(this).attr('data-max'))) {
				interest_debt_warning += $(this).attr('data-warning')+'\n'
			}
		})
		if(interest_debt_warning != ""){
			swal({
				title: "ยอดชำระต่อไปนี้ยังชำระไม่ครบต้องการจะทำรายการต่อไปหรือไม่",
				text: interest_debt_warning,
				type: "warning",
				showCancelButton: true,
				confirmButtonColor: '#DD6B55',
				confirmButtonText: 'ยืนยัน',
				cancelButtonText: "ยกเลิก",
				closeOnConfirm: false,
				closeOnCancel: true
			},
			function(isConfirm) {
				if (isConfirm) {
					$("#form_fired_approve").submit();
				} else {
				}
			});
			return false;
		}
		$("#form_fired_approve").submit();
	}
});
 
$(document).on('change', '.input-payment', function() {
	total_payment = 0;
	$(".input-payment").each(function(index) {
		if($(this).val()) total_payment += parseFloat($(this).val())
	});
	remain = parseFloat($("#income_balance").val()) - total_payment
	$("#income_balance_left").val(remain.toLocaleString())
});

$( document ).ready(function() {
	if($("#search_type").val()) {
		$(".search_date_group").show()
	} else {
		$(".search_date_group").hide()
	}
});
$(document).on("change", "#search_type", function() {
	if($("#search_type").val()) {
		$(".search_date_group").show()
	} else {
		$(".search_date_group").hide()
	}
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
})
</script>