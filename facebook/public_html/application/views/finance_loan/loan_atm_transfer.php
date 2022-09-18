<div class="layout-content">
    <div class="layout-content-body">
<style>
label {
    padding-top: 6px;
    text-align: right;
}
.text-center{
	text-align:center;
}

input[type=checkbox], input[type=radio] {
    margin: 11px 0 0;
}
</style> 

		<div class="row">
			<div class="form-group">
				<div class="col-sm-6">
					<h1 class="title_top">โอนเงินกู้ฉุกเฉิน ATM</h1>
					<?php $this->load->view('breadcrumb'); ?>
				</div>
				<div class="col-sm-6">
				<br>
					<div class="g24-col-sm-24" style="text-align:right;padding-right:0px;margin-right:0px;">
						<a class="link-line-none">
							<button class="btn btn-primary" style="margin-right:5px;" onclick="open_print_modal()">พิมพ์รายงาน</button>
						</a>
					</div>
				</div>
			</div>
		</div>
		
		<div class="row gutter-xs">
			<div class="col-xs-12 col-md-12">
				<div class="panel panel-body" style="padding-top:0px !important;">
				  <h3></h3>
					 <table class="table table-bordered table-striped table-center">
					 <thead> 
						<tr class="bg-primary">
							<th>วันที่ทำรายการ</th>
							<th>เลขที่สัญญา</th>
							<th>รหัสสมาชิก</th>
							<th>ชื่อสมาชิก</th>
							<th>ยอดเงิน</th>
							<th>ผู้ทำรายการ</th>
							<th>สถานะ</th>
							<th>จัดการ</th> 
						</tr> 
					 </thead>
						<tbody id="table_first">
						<?php //echo '<pre>'; print_r($data); echo '</pre>';?>
						  <?php 
							if(!empty($data)){
							foreach($data as $key => $row ){ ?>							
							  <tr> 
								  <td><?php echo @$this->center_function->ConvertToThaiDate($row['loan_date']); ?></td>
								  <td><?php echo @$row['contract_number']; ?></td> 
								  <td><?php echo @$row['member_id']; ?></td> 
								  <td class="text-left"><?php echo @$row['firstname_th']." ".@$row['lastname_th']; ?></td> 
								  <td class="text-right"><?php echo number_format(@$row['loan_amount'],2); ?></td> 
								  <td><?php echo @$row['user_name']; ?></td> 
								  <td><?php echo $transfer_status[@$row['transfer_status']]; ?></td> 
								  <td style="font-size: 14px;">
										<a class="btn btn-info" id="" title="จ่ายเงินกู้" onclick="open_transfer_modal('<?php echo @$row['loan_id']; ?>');">
											จ่ายเงินกู้
										</a>
								  </td>
							  </tr>
						  <?php } 
							}else{?>
							<tr> 
								  <td colspan="7">ไม่พบข้อมูล</td>
							  </tr>
							<?php } ?>
						  </tbody> 
						</table> 
				  </div>
			</div>
		</div>
		<?php echo @$paging ?>
	</div>
</div>

<div class="modal fade" id="print_modal" role="dialog" style="overflow-x: hidden;overflow-y: auto;">
	<div class="modal-dialog modal-dialog-file">
		<div class="modal-content data_modal">
			<div class="modal-header modal-header-confirmSave">
				<button type="button" class="close" data-dismiss="modal">x</button>
				<h2 class="modal-title" id="type_name">พิมพ์รายงาน</h2>
			</div>
			<form action="<?php echo base_url(PROJECTPATH.'/finance_loan/loan_atm_transfer_open'); ?>" method="POST" id="form_print" enctype="multipart/form-data">
				<div class="modal-body">
					<div class="g24-col-sm-24 modal_data_input">
						<div class="form-group g24-col-sm-24">
							<label class="g24-col-sm-6 control-label right"> วันที่ </label>
							<div class="g24-col-sm-8">
								<div class="input-with-icon">
									<div class="form-group">
										<input id="start_date" name="start_date" class="form-control m-b-1 mydate" style="padding-left: 50px;" type="text" value="<?php echo $this->center_function->mydate2date(date('Y-m-d')); ?>" data-date-language="th-th">
										<span class="icon icon-calendar input-icon m-f-1"></span>
									</div>
								</div>
							</div>
							<label class="g24-col-sm-1 control-label right"> ถึง </label>
							<div class="g24-col-sm-8">
								<div class="input-with-icon">
									<div class="form-group">
										<input id="end_date" name="end_date" class="form-control m-b-1 mydate" style="padding-left: 50px;" type="text" value="<?php echo $this->center_function->mydate2date(date('Y-m-d')); ?>" data-date-language="th-th">
										<span class="icon icon-calendar input-icon m-f-1"></span>
									</div>
								</div>
							</div>
						</div>
						
						<div class="form-group g24-col-sm-24">
							<label class="g24-col-sm-6 control-label right"> วิธีการชำระเงิน</label>
							<div class="g24-col-sm-17">
								<select class="form-control m-b-1" id="transfer_type"  name="transfer_type" >
									<option value="">ทั้งหมด</option>
									<?php foreach($pay_type as $key => $value){ ?>
										<option value="<?php echo $key; ?>"><?php echo $value; ?></option>
									<?php } ?>
								</select>
							</div>
						</div>
						
						<div class="text-center">
							<button class="btn btn-primary" type="submit" onclick="">แสดงรายงาน</button>
						</div>
						
					</div>
					&nbsp;
				</div>
			</form>
		</div>
	</div>
</div>

<div class="modal fade" id="transfer_modal" role="dialog" style="overflow-x: hidden;overflow-y: auto;">
	<div class="modal-dialog modal-dialog-file">
		<div class="modal-content data_modal">
			<div class="modal-header modal-header-confirmSave">
				<button type="button" class="close" data-dismiss="modal">x</button>
				<h2 class="modal-title" id="type_name">จ่ายเงินกู้</h2>
			</div>
			<form action="<?php echo base_url(PROJECTPATH.'/finance_loan/loan_atm_transfer_save'); ?>" method="POST" id="form_loan_transfer" enctype="multipart/form-data">
				<div class="modal-body">
					<input id="loan_id" name="loan_id" type="hidden">
					<div class="g24-col-sm-24 modal_data_input">
						<div class="form-group g24-col-sm-24">
							<label class="g24-col-sm-6 control-label" for="form-control-2">เลขที่สัญญา</label>
							<div class="g24-col-sm-6" >
								<input id="contract_number" class="form-control" type="text" value="" readonly>					
							</div>
						</div>
						<div class="form-group g24-col-sm-24">
							<label class="g24-col-sm-6 control-label" for="form-control-2">รหัสสมาชิก</label>
							<div class="g24-col-sm-6" >
								<input class="form-control member_id all_input" id="member_id" type="text" value=""  readonly>
							</div>
							<label class="g24-col-sm-3 control-label" for="form-control-2">ชื่อสกุล</label>
							<div class="g24-col-sm-8" >
								<input class="form-control all_input" id="member_name" type="text" value=""  readonly>
							</div>
						</div>
						<div class="form-group g24-col-sm-24">
							<label class="g24-col-sm-6 control-label" for="form-control-2">ยอดเงินกู้</label>
							<div class="g24-col-sm-6" >
								<input class="form-control all_input" id="loan_amount" type="text" value=""  readonly>
							</div>
						</div>
						<!--<div class="form-group g24-col-sm-24">
							<label class="g24-col-sm-6 control-label" for="form-control-2">ยอดเงินที่ได้รับ</label>
							<div class="g24-col-sm-6" >
								<input class="form-control all_input" id="amount_transfer" name="amount_transfer" type="text" value="" required title="กรุณาป้อน ยอดเงินที่ได้รับ" onkeyup="format_the_number(this);" readonly>
							</div>
						</div>-->
						<div class="form-group g24-col-sm-24">
							<label class="g24-col-sm-6 control-label" for="form-control-2">วิธีการชำระเงิน</label>
							<div class="g24-col-sm-14">
								<span id="show_pay_type2" style="">
									<input type="radio" name="pay_type" id="pay_type_0" onclick="change_pay_type()" value="0"> เงินสด &nbsp;&nbsp;
									<input type="radio" name="pay_type" id="pay_type_3" onclick="change_pay_type()" value="3"> โอนเงินบัญชีสหกรณ์ &nbsp;&nbsp;
								</span>
							</div>
						</div>
						<div class="form-group g24-col-sm-24">
							<label class="g24-col-sm-6 control-label" for="form-control-2"></label>
							<div class="g24-col-sm-14">
								<span id="show_pay_type2" style="">
									<input type="radio" name="pay_type" id="pay_type_1" onclick="change_pay_type()" value="1"> โอนเงินบัญชีกรุงไทย &nbsp;&nbsp;
									<input type="radio" name="pay_type" id="pay_type_2" onclick="change_pay_type()" value="2"> ATM
								</span>
							</div>
						</div>
						
						<div class="form-group g24-col-sm-24 pay_type_1" style="display:none;">						
							<label class="g24-col-sm-6 control-label" for="form-control-2">บัญชี</label>
							<div class="g24-col-sm-17" >
								<input class="form-control" id="dividend_bank_id" name="dividend_bank_id" type="hidden" value="">
								<input class="form-control all_input" id="dividend_acc_num" name="dividend_acc_num" type="text" value=""  readonly>
							</div>
						</div>		
						<div class="form-group g24-col-sm-24 pay_type_3" style="display:none;">						
							<label class="g24-col-sm-6 control-label" for="form-control-2">บัญชี</label>
							<div class="g24-col-sm-17" >
								<input class="form-control all_input" id="account_id" name="account_id" type="text" value=""  readonly>
							</div>
						</div>						
						<div class="text-center">
							<button id="bt_loan_transfer" class="btn btn-primary" type="button" onclick="cash_submit()">จ่ายเงินกู้</button>
						</div>
						
					</div>
					&nbsp;
				</div>
			</form>
		</div>
	</div>
</div>

<script>
	var base_url = $('#base_url').attr('class');
	$( document ).ready(function() {
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
		
		$(".modal").on("hidden.bs.modal", function(){
			$('#contract_number').val("");
			$('#member_id').val("");
			$('#member_name').val("");
			$('#loan_amount').val("");
			$('#dividend_bank_id').val("");
			$('#dividend_bank_branch_id').val("");
			$('#dividend_acc_num').val("");
			$("input:radio").removeAttr("checked");
			$('.pay_type_1').hide();
		});
	});
	
	function open_print_modal(){
		$('#print_modal').modal('show');
	}
	
	function open_transfer_modal(loan_id){
		$.ajax({
			url:base_url+"/finance_loan/get_loan_atm_data",
			method:"post",
			data:{loan_id:loan_id},
			dataType:"text",
			success:function(data)
			{
				var obj = JSON.parse(data);
				console.log(obj);
				$('#loan_id').val(loan_id);
				$('#pay_type_'+obj.pay_type).attr('checked','checked');
				$('#contract_number').val(obj.contract_number);
				$('#member_id').val(obj.member_id);
				$('#member_name').val(obj.firstname_th+"  "+obj.lastname_th);
				$('#loan_amount').val(obj.loan_amount);
				if(obj.bank_account_id!='' && obj.bank_account_id != null){
					$('#dividend_bank_id').val(obj.bank_id);
					$('#dividend_acc_num').val(obj.bank_account_id);
				}else{
					$('#dividend_bank_id').val(obj.dividend_bank_id);
					$('#dividend_acc_num').val(obj.dividend_acc_num);
				}
				$('#account_id').val(obj.account_id);
				$('#transfer_modal').modal('show');
				change_pay_type();
			}
		});
		
	}
	
	function change_pay_type(){
		if($('#pay_type_1').is(':checked')){
			$('.pay_type_1').show();
		}else{
			$('.pay_type_1').hide();
		}
		if($('#pay_type_3').is(':checked')){
			$('.pay_type_3').show();
		}else{
			$('.pay_type_3').hide();
		}
	}
	
	function cash_submit(){ 
		if($('input[name=pay_type]').is(":checked") == false){
			swal("กรุณาเลือกวิธีการชำระเงิน");
		}else{	
			$("#bt_loan_transfer").attr('disabled','disabled');	
			$('#form_loan_transfer').submit();
		}
	}
</script>