<div class="layout-content">
    <div class="layout-content-body">
		<style>
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
		<h1 style="margin-bottom: 0">เงื่อนไขการลาออก</h1>

		<div class="row gutter-xs">
			<div class="col-xs-12 col-md-12">
				<div class="panel panel-body" style="padding-top:0px !important;">
						<form id='form1' data-toggle="validator" novalidate="novalidate" action="<?php echo base_url(PROJECTPATH.'/setting_member_data/coop_quite_save'); ?>" method="post">
						<h3 ></h3>
							<div class="g24-col-sm-24 m-b-1">
								<div class="form-group">
									<label class="g24-col-sm-8 control-label right"> ลาออกแล้ว </label>
									<div class="g24-col-sm-4">
										<input class="form-control m-b-1" type="number" name="year_quite" value="<?php echo @$row['year_quite']?>">
										<input  name="id" type="hidden" value="<?php echo @$row['id']?>">
									</div>
									<label class="g24-col-sm-8 control-label text-left"> ปี จึงจะกลับมาเป็นสมาชิกใหม่ได้ </label>
								</div>
							</div>
							<div class="g24-col-sm-24">
								<div class="form-group">
									<label class="g24-col-sm-8 control-label "></label>
									<div class="g24-col-sm-4" style="text-align:center;">
										<button class="btn btn-primary" type="button" onclick="submit_form()"><span class="icon icon-save"></span> บันทึก</button>
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
