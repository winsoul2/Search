<div class="layout-content">
    <div class="layout-content-body">
		<style>
			.modal-dialog-account {
				margin:auto;
				margin-top:7%;
			}
		</style>

		<style type="text/css">
		  .form-group{
			margin-bottom: 5px;
		  }
		</style>
		<h1 style="margin-bottom: 0">ตั้งค่าเงินฝาก</h1>
		<div class="row gutter-xs">
			<div class="col-xs-12 col-md-12">
				<div class="panel panel-body" style="padding-top:0px !important;">
					
						<form id="form1" method="POST" action="<?php echo base_url(PROJECTPATH.'/setting_deposit_data/coop_deposit_setting_save'); ?>">
						<h3></h3>
						<input type="hidden" name="deposit_setting_id" value="<?php echo @$row['deposit_setting_id']; ?>">
						<?php $type_id = '1'; ?>
							<div class="g24-col-sm-24 m-b-1">
								<div class="form-group">
									<label class="g24-col-sm-8 control-label right"> เงินฝากขั้นต่ำในการเปิดบัญชีครั้งแรก </label>
									<div class="g24-col-sm-7">
										<input type="number" class="form-control" name="min_first_deposit" value="<?php echo @$row['min_first_deposit']; ?>">
									</div>
									<label class="g24-col-sm-4 control-label text-left">บาท</label>
								</div>
							</div>
							<div class="g24-col-sm-24 m-b-1" style="display: none;">
								<div class="form-group">
									<label class="g24-col-sm-8 control-label right"> ไม่คิดดอกเบี้ยเมื่อเงินคงเหลือต่ำกว่า </label>
									<div class="g24-col-sm-7">
										<input type="number" class="form-control" name="min_money_non_interest_rate" value="<?php echo @$row['min_money_non_interest_rate']; ?>">
									</div>
									<label class="g24-col-sm-4 control-label text-left">บาท </label>
								</div>
							</div>
							<div class="g24-col-sm-24 m-b-1">
								<div class="form-group">
									<label class="g24-col-sm-8 control-label right"> พิมพ์สมุดบัญชีแบบสรุปยอดเมื่อเกิน </label>
									<div class="g24-col-sm-7">
										<input type="number" class="form-control" name="month_conclude" value="<?php echo @$row['month_conclude']; ?>">
									</div>
									<label class="g24-col-sm-4 control-label text-left">เดือน </label>
								</div>
							</div>
							<!--<div class="g24-col-sm-24">
								<div class="form-group g24-col-sm-17">
									<label class="g24-col-sm-8 control-label right"> อัตราดอกเบี้ยเงินฝาก </label>
									<div class="g24-col-sm-7">
										<input type="number" class="form-control" name="interest_rate" value="<?php echo @$row['interest_rate']; ?>">
									</div>
									<label class="g24-col-sm-4 control-label "> % </label>
								</div>
							</div>-->
							<div class="g24-col-sm-24 m-b-1">
								<div class="form-group">
									<label class="g24-col-sm-8 control-label "></label>
									<div class="g24-col-sm-7" style="text-align:center">
										<button class="btn btn-primary" onclick="submit_form()"><span class="icon icon-save"></span> บันทึก</button>
									</div>
								</div>
							</div>
						</form>
				</div>
			</div>
		</div>
	</div>
</div>

<script>
 function submit_form(){
	 $('#form1').submit();
 }
</script>

