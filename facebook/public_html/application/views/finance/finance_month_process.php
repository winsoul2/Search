<div class="layout-content">
    <div class="layout-content-body">
<style>
    /*.form-group { margin-bottom: 0; }*/
    .border1 { border: solid 1px #ccc; padding: 0 15px; }
    .mem_pic { float: right; width: 150px; }
    .mem_pic img { width: 100%; border: solid 1px #ccc; }
    .mem_pic button { display: block; width: 100%; }

    .hide_error{color : inherit;border-color : inherit;}

    .has-error{color : #d50000;border-color : #d50000;}

    input::-webkit-outer-spin-button,
    input::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }
    .alert-danger {
        background-color: #F2DEDE;
        border-color: #e0b1b8;
        color: #B94A48;
    }
    .modal-backdrop.in{
        opacity: 0;
    }
    .modal-backdrop {
        position: relative;
        top: 0;
        right: 0;
        bottom: 0;
        left: 0;
        z-index: 1040;
        background-color: #000;
    }
    .modal.fade {
        z-index: 10000000 !important;
    }
	.control-label{
		text-align: right;
		margin-bottom: 0;
		padding-top: 7px;
	}
	th{
		text-align: center;
	}
	
	.modal-dialog-data {
		width:60% !important;
		margin:auto;
		margin-top:1%;
		margin-bottom:1%;
	}
	
</style>
<h1 style="margin-bottom: 0">ประมวลผลผ่านรายการ</h1>
<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 padding-l-r-0">
    <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 padding-l-r-0">
        <?php $this->load->view('breadcrumb'); ?>
    </div>
	<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 padding-l-r-0">
		<?php
		$get_param = '?month='.$month.'&year='.$year;
		foreach(@$_GET as $key => $value){
			if($key != 'month' && $key != 'year' && $value != ''){
				$get_param .= '&'.$key.'='.$value;
			}
		}
		?>
		<!--<a class="btn btn-primary btn-lg bt-add" href="<?php echo base_url(PROJECTPATH.'/finance/finance_month_run_process'.$get_param); ?>">
			<span class="icon icon-plus-circle"></span> ประมวลผล
		</a>-->
		<a class="btn btn-primary btn-lg bt-add" onclick="open_process_modal()">
			<span class="icon icon-plus-circle"></span> ประมวลผล
		</a>
		<a class="btn btn-primary btn-lg bt-add" href="<?php echo base_url(PROJECTPATH.'/cashier/non_pay'); ?>" style="margin-right:10px">
			<span class="icon icon-plus-circle"></span> เพิ่มรายการชำระเงินไม่ครบ
		</a>
		<?php if(!empty($last_profile)){ ?>
		<!-- <a class="btn btn-primary btn-lg bt-add" onclick="finance_month_cancel_process()" style="margin-right:10px">
			<span class="icon icon-minus-circle"></span> ยกเลิกรายการล่าสุด
		</a> -->
		<?php } ?>
	</div>
</div>
<div class="row gutter-xs">
    <div class="col-xs-12 col-md-12">
        <div class="panel panel-body">
			<form method="GET" action="">
				<div class="g24-col-sm-24">
					<label class="g24-col-sm-3 control-label">ปี</label>
					<div class="g24-col-sm-3 m-b-1">
						<select class="form-control" name="year">
							<?php for($y=(date('Y')+540);$y<=(date('Y')+546);$y++){ ?>
								<option value="<?php echo $y; ?>" <?php echo $y==$year?'selected':''; ?>><?php echo $y; ?></option>
							<?php } ?>
						</select>
					</div>
					<label class="g24-col-sm-2 control-label">เดือน</label>
					<div class="g24-col-sm-3 m-b-1">
						<select class="form-control" name="month">
							<?php foreach($month_arr as $key => $value){ ?>
								<option value="<?php echo $key; ?>" <?php echo $key==$month?'selected':''; ?>><?php echo $value; ?></option>
							<?php } ?>
						</select>
					</div>
					<!--label class="g24-col-sm-4 control-label right"> ประเภทการชำระเงิน </label>
					<div class="g24-col-sm-3">
						<select name="pay_type" id="pay_type" class="form-control">
							<option value="">ทั้งหมด</option>
							<option value="0" <?php echo @$_GET['pay_type']=='0'?'selected':''; ?>>เงินสด</option>
							<option value="1" <?php echo @$_GET['pay_type']=='1'?'selected':''; ?>>โอนเงิน</option>
						</select>
					</div-->
				</div>
				<div class="g24-col-sm-24">
					<label class="g24-col-sm-3 control-label">หน่วยงานหลัก</label>
					<div class="g24-col-sm-3 m-b-1">
						<select class="form-control" name="department" id="department" onchange="change_mem_group('department', 'faction')">
							<option value="">เลือกข้อมูล</option>
							<?php foreach($mem_group as $key => $value){ ?>
								<option value="<?php echo $value['id']; ?>" <?php echo @$_GET['department']==$value['id']?'selected':''; ?>><?php echo $value['mem_group_name']; ?></option>
							<?php } ?>
						</select>
					</div>
					<label class="g24-col-sm-2 control-label right"> ฝ่าย </label>
					<div class="g24-col-sm-3">
						<select name="faction" id="faction" onchange="change_mem_group('faction','level')" class="form-control">
							<option value="">เลือกข้อมูล</option>
							<?php foreach($faction as $key => $value){ ?>
								<option value="<?php echo $value['id']; ?>" <?php echo @$_GET['faction']==$value['id']?'selected':''; ?>><?php echo $value['mem_group_name']; ?></option>
							<?php } ?>
						</select>
					</div>
					<label class="g24-col-sm-4 control-label right"> สังกัด </label>
					<div class="g24-col-sm-3">
						<select name="level" id="level" class="form-control">
							<option value="">เลือกข้อมูล</option>
							<?php foreach($level as $key => $value){ ?>
								<option value="<?php echo $value['id']; ?>" <?php echo @$_GET['level']==$value['id']?'selected':''; ?>><?php echo $value['mem_group_name']; ?></option>
							<?php } ?>
						</select>
					</div>
				</div>
				<div class="g24-col-sm-24">
					<label class="g24-col-sm-3 control-label">ประเภทสมาชิก</label>
					<div class="g24-col-sm-3 m-b-1">
						<select class="form-control" name="mem_type_id" id="mem_type_id">
							<option value="">เลือกข้อมูล</option>
							<?php foreach($mem_type as $key => $value){ ?>
								<option value="<?php echo $value['mem_type_id']; ?>" <?php echo @$_GET['mem_type_id']==$value['mem_type_id']?'selected':''; ?>><?php echo $value['mem_type_name']; ?></option>
							<?php } ?>
						</select>
					</div>
					<label class="g24-col-sm-2 control-label right"> รหัสสมาชิก </label>
					<div class="g24-col-sm-3">
						<input class="form-control" type="text" name="member_id" id="member_id" value="<?php echo @$_GET['member_id']; ?>">
					</div>
					<label class="g24-col-sm-4 control-label right"> จำนวนแสดงรายการ </label>
					<div class="g24-col-sm-3">
						<select name="show_row" id="show_row" class="form-control">
							<option value="100" <?php echo $show_row=='100'?'selected':''; ?>>100</option>
							<option value="500" <?php echo $show_row=='500'?'selected':''; ?>>500</option>
							<option value="1000" <?php echo $show_row=='1000'?'selected':''; ?>>1000</option>
						</select>
					</div>
					<div class="g24-col-sm-3">
						<input type="submit" class="btn btn-primary" value="ค้นหา">
					</div>
					<div class="g24-col-sm-3">
						<?php
							$get_param = '?';
							foreach(@$_GET as $key => $value){
								$get_param .= $key.'='.$value.'&';
							}
							$get_param = substr($get_param,0,-1);
						?>
						<a target="_blank" href="<?php echo base_url('/finance/finance_month_process_excel'.$get_param); ?>">
							<button class="btn btn-primary" type="button"><span class="icon icon icon-file-excel-o" aria-hidden="true"></span> ส่งออก Excel</button>
						</a>
					</div>
				</div>
			</form>
            <div class="bs-example" data-example-id="striped-table">
				<form action="<?php echo base_url(PROJECTPATH.'/finance/finance_month_run_process'.$get_param); ?>" method="POST" id="form_process" enctype="multipart/form-data">
					<input type="hidden" name="process_date" id="process_date2">
					<input type="hidden" name="pay_type" id="pay_type">
					<table class="table table-bordered table-striped table-center">
						<thead>
						<tr class="bg-primary">
							<th><input type="checkbox" id="check_all" onclick="check_it_all();"></th>
							<th>ลำดับ</th>
							<th>รหัสสมาชิก</th>
							<th>ชื่อ - นามสกุล</th>
							<th>หน่วยงาน</th>
							<th>จำนวนเงินทั้งหมด</th>
							<th>จำนวนเงินที่หักได้</th>
							<th>เลขที่ใบเสร็จ</th>
							<th>วิธีชำระเงิน</th>
						</tr>
						</thead>
						<tbody id="table_data">
						<?php 
							$total_pay_amount = 0;
							$total_real_pay_amount = 0;
							foreach($row as $key => $value){ 
								$total_pay_amount  += @$value['pay_amount'];
								$total_real_pay_amount  += @$value['real_pay_amount'];
						?>
							<tr>
								<td>
								<?php if($value['receipt_id']==''){ ?>
									<input type="checkbox" class="check_box" pay_amount="<?php echo $value['pay_amount']; ?>" real_pay_amount="<?php echo $value['real_pay_amount']; ?>" name="member_id[]" value="<?php echo $value['member_id']; ?>">
								<?php } ?>
								</td>
								<td><?php echo $i++; ?></td>
								<td><?php echo $value['member_id']; ?></td>
								<td style="text-align:left;"><?php echo $value['prename_short'].$value['firstname_th']." ".$value['lastname_th']; ?></td>
								<td ><?php echo $value['mem_group_name']; ?></td>
								<td style="text-align:right;"><?php echo number_format($value['pay_amount'],2); ?></td>
								<td style="text-align:right;"><?php echo number_format($value['real_pay_amount'],2); ?></td>
								<td ><?php echo $value['receipt_id']; ?></td>
								<td ><?php echo $value['pay_type']; ?></td>
							</tr>
						<?php 
							} 
						?>
							<tr>
								<td style="text-align:right;font-weight: bold;" colspan="5">ยอดรวม</td>
								<td style="text-align:right;font-weight: bold;"><?php echo number_format(@$total_pay_amount,2); ?></td>
								<td style="text-align:right;font-weight: bold;"><?php echo number_format(@$total_real_pay_amount,2); ?></td>
								<td></td>
								<td></td>
							</tr>
						</tbody>
					</table>
				</form>
            </div>
        </div>
        <div id="page_wrap">
            <?php echo $paging ?>
        </div>
    </div>
</div>
    </div>
</div>

<div class="modal fade" id="process_modal" role="dialog" style="overflow-x: hidden;overflow-y: auto;">
	<div class="modal-dialog modal-dialog-data">
		<div class="modal-content data_modal">
			<div class="modal-header modal-header-confirmSave">
				<button type="button" class="close" data-dismiss="modal">x</button>
				<h2 class="modal-title" id="type_name">ประมวลผลผ่านรายการ</h2>
			</div>
			<?php //echo '<pre>'; print_r($row); echo '</pre>';
				$month = @$_GET['month']!=''?$_GET['month']:(int)date('m');
				$year = @$_GET['year']!=''?$_GET['year']:(date('Y')+543);
			?>
				<div class="modal-body">
					<div class="g24-col-sm-24 modal_data_input">
						<div class="form-group g24-col-sm-24">
							<label class="g24-col-sm-4 control-label" for="form-control-2">ปี</label>
							<div class="g24-col-sm-6" >
								<input class="form-control member_id all_input" id="member_id" type="text" value="<?php echo @$year;?>"  readonly>
							</div>
							<label class="g24-col-sm-5 control-label" for="form-control-2">เดือน</label>
							<div class="g24-col-sm-6" >
								<input class="form-control all_input" id="member_name" type="text" value="<?php echo @$month_arr[@$month];?>"  readonly>
							</div>
						</div>
						<?php if(@$_GET['department']!=''){ ?>
						<div class="form-group g24-col-sm-24">
							<label class="g24-col-sm-4 control-label" for="form-control-2">หน่วยงานหลัก</label>
							<div class="g24-col-sm-17" >
								<input class="form-control all_input" id="loan_amount" type="text" value="<?php echo @$arr_mem_group[@$_GET['department']];?>"  readonly>
							</div>
						</div>
						<?php } ?>
						<?php if(@$_GET['faction']!=''){ ?>
						<div class="form-group g24-col-sm-24">
							<label class="g24-col-sm-4 control-label" for="form-control-2">อำเภอ</label>
							<div class="g24-col-sm-17" >
								<input class="form-control all_input" id="loan_amount" type="text" value="<?php echo @$arr_faction[@$_GET['faction']];?>"  readonly>
							</div>
						</div>
						<?php } ?>
						<?php if(@$_GET['level']!=''){ ?>
						<div class="form-group g24-col-sm-24">
							<label class="g24-col-sm-4 control-label" for="form-control-2">หน่วยงานย่อย</label>
							<div class="g24-col-sm-17" >
								<input class="form-control all_input" id="loan_amount" type="text" value="<?php echo @$arr_level[@$_GET['level']];?>"  readonly>
							</div>
						</div>
						<?php } ?>
						<div class="form-group g24-col-sm-24">
							<label class="g24-col-sm-4 control-label" for="form-control-2">จำนวน</label>
							<div class="g24-col-sm-6" >
								<input class="form-control all_input" id="total_process" type="text" value="<?php echo @$pay_num;?>"  readonly>
							</div>
							<label class="g24-col-sm-2 control-label text-left" for="form-control-2">รายการ</label>
						</div>
						<div class="form-group g24-col-sm-24">
							<label class="g24-col-sm-4 control-label" for="form-control-2">ชำระครบ</label>
							<div class="g24-col-sm-6" >
								<input class="form-control all_input" id="complete_process" type="text" value="<?php echo @$pay_num;?>"  readonly>
							</div>
							<label class="g24-col-sm-2 control-label text-left" for="form-control-2">รายการ</label>
							<label class="g24-col-sm-3 control-label" for="form-control-2">ชำระไม่ครบ</label>
							<div class="g24-col-sm-6" >
								<input class="form-control all_input" id="incomplete_process" type="text" value="<?php echo @$real_pay_num;?>"  readonly>
							</div>
							<label class="g24-col-sm-2 control-label text-left" for="form-control-2">รายการ</label>
						</div>
						<div class="form-group g24-col-sm-24">
							<label class="g24-col-sm-4 control-label" for="form-control-2">รวมเงินทั้งสิ้น</label>
							<div class="g24-col-sm-6" >
								<input class="form-control all_input" id="sum_all" type="text" value="<?php echo number_format(@$total_pay_amount,2);?>"  readonly>
							</div>
							<label class="g24-col-sm-1 control-label text-left" for="form-control-2">บาท</label>
							<label class="g24-col-sm-4 control-label" for="form-control-2">วันที่ประมวลผล</label>
							<div class="input-with-icon g24-col-sm-6">
                                <div class="form-group">
                                    <input id="process_date" name="process_date" class="form-control m-b-1" style="padding-left: 50px;" type="text" value="<?php echo $this->center_function->mydate2date(date("Y-m-d")); ?>" data-date-language="th-th">
                                    <span class="icon icon-calendar input-icon m-f-1"></span>
                                </div>
                            </div>
						</div>
						<div class="form-group g24-col-sm-24">
							<label class="g24-col-sm-4 control-label" for="form-control-2">วิธีการชำระเงิน</label>
							<div class="g24-col-sm-6" >
								<label class="control-label left">
									<input type="radio" name="pay_type_tmp" id="pay_type_0" value="0" > เงินสด
									<input type="radio" name="pay_type_tmp" id="pay_type_1" value="1" checked> โอนเงิน
								</label>
							</div>
						</div>					
						<div class="text-center">
							<button class="btn btn-danger" class="close" data-dismiss="modal" type="button">ยกเลิก</button>
							<button class="btn btn-primary" type="button" id="bt_confirm" onclick="submit_form()">ยืนยัน</button>
						</div>
						
					</div>
					&nbsp;
				</div>
		</div>
	</div>
</div>

<script>
	function change_mem_group(id, id_to){
		var mem_group_id = $('#'+id).val();
		$('#level').html('<option value="">เลือกข้อมูล</option>');
		$.ajax({
			method: 'POST',
			url: base_url+'manage_member_share/get_mem_group_list',
			data: {
				mem_group_id : mem_group_id
			},
			success: function(msg){
				$('#'+id_to).html(msg);
			}
		});
	}
	function submit_form(){
		$('#process_date2').val($('#process_date').val());
		if($('#pay_type_0').is(':checked')){
			$('#pay_type').val('0');
		}else{
			$('#pay_type').val('1');
		}
		$('#bt_confirm').attr('disabled','disabled');
		$('#form_process').submit();
	}
	function open_process_modal(){
		var total_process = 0;
		var complete_process = 0;
		var incomplete_process = 0;
		var sum_all = 0;
		$('.check_box').each(function(){
			if($(this).is(':checked')){
				total_process++;
				if($(this).attr('pay_amount') == $(this).attr('real_pay_amount')){
					complete_process++;
				}else{
					incomplete_process++;
				}
				sum_all += parseFloat($(this).attr('real_pay_amount'));
			}
		});
		$('#total_process').val(addCommas(total_process));
		$('#complete_process').val(addCommas(complete_process));
		$('#incomplete_process').val(addCommas(incomplete_process));
		$('#sum_all').val(addCommas(sum_all));
		$('#process_modal').modal('show');
	}
	$( document ).ready(function() {
		$("#process_date").datepicker({
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
	function finance_month_cancel_process(){
		swal({
			title: "",
			text: "ท่านต้องการยกเลิกการประมวลผลเดือน <?php echo @$month_arr[@$last_profile['profile_month']]." ".@$last_profile['profile_year']; ?> ใช่หรือไม่?",
			type: "warning",
			showCancelButton: true,
			confirmButtonColor: '#DD6B55',
			confirmButtonText: 'ยืนยัน',
			cancelButtonText: "ยกเลิก",
			closeOnConfirm: true,
			closeOnCancel: true
		},
		function(isConfirm){
			if (isConfirm){
				document.location.href = base_url+"finance/finance_month_cancel_process";
			} 
		});
	}
	function check_it_all(){
		if($('#check_all').is(':checked')){
			$('.check_box').attr('checked',true);
		}else{
			$('.check_box').attr('checked',false);
		}
	}
	function removeCommas(str) {
		return(str.replace(/,/g,''));
	}
	function addCommas(x){
	  return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
	}
</script>
