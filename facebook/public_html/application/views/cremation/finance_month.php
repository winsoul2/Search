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
			.right {
				text-align: right;
			}
			.modal-dialog-account {
				margin:auto;
				margin-top:7%;
			}
			label{
				padding-top:7px;
			}
			.form-group{
				margin-bottom: 5px;
			}
		</style>
		<h1 style="margin-bottom: 0">รายการเรียกเก็บประจำเดือน</h1>
		<?php $this->load->view('breadcrumb'); ?>
		<div class="row gutter-xs">
			<div class="col-xs-12 col-md-12">
				<div class="panel panel-body" style="padding-top:0px !important;">
					<form method="POST" id="form_1" action="<?php echo base_url(PROJECTPATH.'/cremation/finance_month'); ?>">
					<h3></h3>
					<div class="form-group g24-col-sm-24">
						<input type="hidden" name="yymm_now" id="yymm_now" value="<?php echo (date('Y')+543).date('m');?>">
						<label class="g24-col-sm-6 control-label right"> เดือน </label>
						<div class="g24-col-sm-4">
							<select id="month" name="month" class="form-control">
								<?php foreach($this->month_arr as $key => $value){ ?>
								<?php
									if($key < ((int)date('m'))){
										$check_disabled = "";	
									}else{
										$check_disabled = "";
									}
								?>
									<option value="<?php echo $key; ?>" <?php echo $key==((int)date('m'))?'selected':''; ?> <?php echo $check_disabled;?>><?php echo $value; ?></option>
								<?php } ?>
							</select>
						</div>
						<label class="g24-col-sm-1 control-label right"> ปี </label>
						<div class="g24-col-sm-4">
							<select id="year" name="year" class="form-control">
								<?php for($i=((date('Y')+543)-5); $i<=((date('Y')+543)+5); $i++){ ?>
									<option value="<?php echo $i; ?>" <?php echo $i==(date('Y')+543)?'selected':''; ?>><?php echo $i; ?></option>
								<?php } ?>
							</select>
						</div>
					</div>
					<div class="form-group g24-col-sm-24">
						<label class="g24-col-sm-6 control-label right"></label>
						<div class="g24-col-sm-9">
							<input type="button" id="submit-btn" class="btn btn-primary" style="width:100%" value="รายการเรียกเก็บรายเดือน">
						</div>
					</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>

<script>
$( document ).ready(function() {
	$("#submit-btn").click(function() {
		$.ajax({
			url:base_url+"cremation/check_finance_month_process", 
			method:"post",
			data:$("#form_1").serialize(),
			dataType:"text",
			success:function(response){
				if(response == 'success'){
					$("#form_1").submit();
				}else{
					swal('ไม่สามารถดำเนินการได้', response, 'warning');
				}
			}
		});
	});

});

</script>