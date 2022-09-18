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
<h1 style="margin-bottom: 0">รายการชำระเงินไม่ได้</h1>
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
					<label class="g24-col-sm-2 control-label right"> อำเภอ </label>
					<div class="g24-col-sm-3">
						<select name="faction" id="faction" onchange="change_mem_group('faction','level')" class="form-control">
							<option value="">เลือกข้อมูล</option>
							<?php foreach($faction as $key => $value){ ?>
								<option value="<?php echo $value['id']; ?>" <?php echo @$_GET['faction']==$value['id']?'selected':''; ?>><?php echo $value['mem_group_name']; ?></option>
							<?php } ?>
						</select>
					</div>
					<label class="g24-col-sm-4 control-label right"> หน่วยงานย่อย </label>
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
						<a onclick="submit_form()">
							<button class="btn btn-primary" type="button"><span class="icon icon icon-save"></span> บันทึก</button>
						</a>
					</div>
				</div>
			</form>
            <div class="bs-example" data-example-id="striped-table">
				<form action="<?php echo base_url(PROJECTPATH.'/cashier/non_pay_all_amount_save'.$get_param); ?>" method="POST" id="form_process" enctype="multipart/form-data">
					<input type="hidden" name="month_non_pay" id="month_non_pay" value="<?php echo (@$_GET['month'] != '')?@$_GET['month']:date('m');?>">
					<input type="hidden" name="year_non_pay" id="year_non_pay" value="<?php echo (@$_GET['year'] != '')?@$_GET['year']:(date('Y')+543);?>">
					<table class="table table-bordered table-striped table-center">
						<thead>
						<tr class="bg-primary">
							<th><input type="checkbox" id="check_all" onclick="check_it_all();"></th>
							<th>ลำดับ</th>
							<th>รหัสสมาชิก</th>
							<th>ชื่อ - นามสกุล</th>
							<th>หน่วยงาน</th>
							<th style="width:15%;">จำนวนเงินทั้งหมดที่หักไม่ได้</th>
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
								<td style="text-align:left;"><?php echo $value['mem_group_name']; ?></td>
								<td style="text-align:right;"><?php echo number_format($value['pay_amount'],2); ?></td>
							</tr>
						<?php 
							} 
						?>
							<tr>
								<td style="text-align:right;font-weight: bold;" colspan="5">ยอดรวม</td>
								<td style="text-align:right;font-weight: bold;"><?php echo number_format(@$total_pay_amount,2); ?></td>
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
		swal({
			title: "",
			text: "ท่านต้องการทำรายการชำระเงินไม่ได้ ใช่หรือไม่?",
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
				$('#form_process').submit();
			} 
		});
		
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