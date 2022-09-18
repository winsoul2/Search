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
		</style>

		<style type="text/css">
		  .form-group{
			margin-bottom: 5px;
		  }
		</style>
		<h1 style="margin-bottom: 0">ตั้งค่าปีงบประมาณ</h1>
		<div class="row gutter-xs">
			<div class="col-xs-12 col-md-12">
				<div class="panel panel-body" style="padding-top:0px !important;">
					
						<form id="form1" method="POST" action="<?php echo base_url(PROJECTPATH.'/setting_facility_data/budget_year'); ?>">
						<h3></h3>
						<input type="hidden" name="year_id" value="<?php echo @$data['year_id']; ?>">
							<div class="g24-col-sm-24">
								<div class="form-group g24-col-sm-17">
									<label class="g24-col-sm-8 control-label right"> วันที่เริ่มต้นปีงบประมาณ </label>
									<div class="g24-col-sm-7">
										<select name="month_start" id="month_start" class="form-control" onchange="change_month('start')">
										<?php foreach($month_arr as $key => $value){ ?>
											<option value="<?php echo $key; ?>" <?php echo $key==@$data['month_start']?'selected':''; ?>><?php echo $value; ?></option>
										<?php } ?>
										</select>
									</div>
									<div class="g24-col-sm-4" id="date_start_space">
										<select name="date_start" class="form-control">
										<?php for($i=1;$i<=$date_start_limit;$i++){ ?>
											<option value="<?php echo $i; ?>" <?php echo $i==@$data['date_start']?'selected':''; ?>><?php echo $i; ?></option>
										<?php } ?>
										</select>
									</div>
								</div>
							</div>
							<div class="g24-col-sm-24">
								<div class="form-group g24-col-sm-17">
									<label class="g24-col-sm-8 control-label right"> วันที่สิ้นสุดปีงบประมาณ </label>
									<div class="g24-col-sm-7">
										<select name="month_end" id="month_end" class="form-control" onchange="change_month('end')">
										<?php foreach($month_arr as $key => $value){ ?>
											<option value="<?php echo $key; ?>" <?php echo $key==@$data['month_end']?'selected':''; ?>><?php echo $value; ?></option>
										<?php } ?>
										</select>
									</div>
									<div class="g24-col-sm-4" id="date_end_space">
										<select name="date_end" class="form-control">
										<?php for($i=1;$i<=$date_end_limit;$i++){ ?>
											<option value="<?php echo $i; ?>" <?php echo $i==@$data['date_end']?'selected':''; ?>><?php echo $i; ?></option>
										<?php } ?>
										</select>
									</div>
								</div>
							</div>
							<div class="g24-col-sm-24">
								<div class="form-group g24-col-sm-17">
									<label class="g24-col-sm-8 control-label "></label>
									<div class="g24-col-sm-10">
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
function change_month(type){
	var month = $('#month_'+type).val();
	$.ajax({
		 url:base_url+"/setting_facility_data/change_month",
		 method:"post",
		 data:{month:month, type:type},
		 dataType:"text",
		 success:function(data)
		 {
			 console.log(data);
			$('#date_'+type+'_space').html(data);
		 }
	});
}
</script>