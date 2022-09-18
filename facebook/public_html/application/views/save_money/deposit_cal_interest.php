<div class="layout-content">
    <div class="layout-content-body">
		<style>
			.center {
				text-align: center;
			}
			.left {
				text-align: left;
			}
			.modal-dialog-account {
				margin:auto;
				margin-top:7%;
			}
			.modal-dialog-data {
				width:90% !important;
				margin:auto;
				margin-top:1%;
				margin-bottom:1%;
			}
			.modal-dialog-cal {
				width:80% !important;
				margin:auto;
				margin-top:1%;
				margin-bottom:1%;
			}
			.modal-dialog-file {
				width:50% !important;
				margin:auto;
				margin-top:1%;
				margin-bottom:1%;
			}
			.modal_data_input{
				margin-bottom: 5px;
			}
			.form-group{
				margin-bottom: 5px;
			  }
			  .red{
				color: red;
			  }
			  .green{
				color: green;
			  }
		</style> 
		<div class="row">
			<div class="form-group">
				<div class="col-sm-6">
					<h1 class="title_top">คำนวณดอกเบี้ยเงินฝาก</h1>
					<?php $this->load->view('breadcrumb'); ?>
				</div>
			</div>
		</div>
		<div class="row gutter-xs">
			<div class="col-xs-12 col-md-12">
				<div class="panel panel-body" style="padding-top:0px !important;">
					<form action="" method="POST">
						<div class="g24-col-sm-24" style="margin-top:20px;">
							<div class="form-group g24-col-sm-8">
								<label class="g24-col-sm-10 control-label" for="form-control-2">เลือกบัญชี</label>
								<div class="g24-col-sm-14" >
									<input type="text" class="form-control" name="account_id" value="<?php echo $_POST['account_id']; ?>">
								</div>
							</div>
							<div class="form-group g24-col-sm-8">
								<label class="g24-col-sm-10 control-label" for="form-control-2">วันที่คำนวณ</label>
								<div class="input-with-icon g24-col-sm-14">
									<div class="form-group">
										<input id="date_interest" name="date_interest" class="form-control m-b-1" style="padding-left: 50px;" type="text" value="<?php echo $_POST['date_interest']!=''?$_POST['date_interest']:$this->center_function->mydate2date(date('Y-m-d')); ?>" data-date-language="th-th" required title="" >
										<span class="icon icon-calendar input-icon m-f-1"></span>
									</div>
								</div>
							</div>
						</div>
						<div class="g24-col-sm-24" style="margin-top:20px;">
							<div class="form-group g24-col-sm-8">
								<label class="g24-col-sm-10 control-label" for="form-control-2"></label>
								<div class="g24-col-sm-14" >
									<input class="btn btn-primary" type="submit" value="คำนวณ">
								</div>
							</div>
						</div>
					</form>
					<div class="g24-col-sm-24" style="margin-top:20px;">
						<table class="table">
							<thead>
								<tr>
									<th>วันที่เริ่มต้น</th>
									<th>วันที่สิ้นสุด</th>
									<th>จำนวนวัน</th>
									<th>ยอดเงินคงเหลือ</th>
									<th>ยอดเงินคงเหลือ(ไม่รวมดอกเบี้ย)</th>
									<th>ดอกเบี้ย(อัตราจริง)</th>
									<th>อัตราดอกเบี้ย</th>
								</tr>
							</thead>
							<tbody>
								<?php if(!empty($interest_data)){ $sum_in = 0; ?>
									<?php foreach($interest_data as $key => $value){ ?>
									<tr>
										<td><?php echo $this->center_function->ConvertToThaiDate($value['date_start']); ?></td>
										<td><?php echo $this->center_function->ConvertToThaiDate($value['date_end']); ?></td>
										<td><?php echo $value['date_count']; ?></td>
										<td><?php echo number_format($value['transaction_balance'],2); ?></td>
										<td><?php echo number_format($value['transaction_no_in_balance'],2); ?></td>
										<td><?php echo number_format($value['interest'],2); ?></td>
										<td><?php echo @$value['interest_rate']." %"; ?></td>
									<tr>
									<?php 
									$sum_in += $value['interest']; 
									} ?>
									<tr>
										<td colspan="5" align="center">รวม</td>
										<td><?php echo number_format($sum_in,2); ?></td>
										<td></td>
										<td></td>
									</tr>
								<?php } ?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<script>
	$('document').ready(function() {
		$("#date_interest").datepicker({
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
</script>