<div class="layout-content">
    <div class="layout-content-body">
		<style>
			.center {
				text-align: center;
			}
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
		<h1 style="margin-bottom: 0">รอบการอนุมัติ</h1>
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 padding-l-r-0">
		<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6 padding-l-r-0">
		<?php $this->load->view('breadcrumb'); ?>
		</div>
		</div>

		<div class="row gutter-xs">
			<div class="col-xs-12 col-md-12">
				<div class="panel panel-body" style="padding-top:0px !important;margin-top:20px;">
						<form id="form1" method="POST" action="<?php echo base_url(PROJECTPATH.'/setting_member_data/coop_approval_cycle_save?member='.@$member_id); ?>">
						<div class="">
						<h3 >รายการตั้งค่ารอบการอนุมัติ</h3>
						<?php  
							if(!empty($rs)){
								foreach(@$rs as $key => $row){ 
						?>
							<div class="g24-col-sm-24">
								<div class="form-group g24-col-sm-14">
									<label class="g24-col-sm-8 control-label "><?php echo @$row['approval_name']; ?> ทุกวันที่ </label>
									<div class="g24-col-sm-7">
										<select name="approval_id[<?php echo @$row['id']; ?>]" class="form-control">
											<?php for($i=1;$i<=28;$i++){ ?>
												<option value="<?php echo $i; ?>" <?php echo (@$row['approval_date']==$i)?'selected':''; ?>><?php echo $i; ?></option>
											<?php }?>
											<option value="last" <?php echo (@$row['approval_date']=='last')?'selected':''; ?>>ทุกสิ้นเดือน</option>
										</select>
									</div>
									<label class="g24-col-sm-4 control-label text-left">ของเดือน </label>
								</div>
							</div>
						<?php 
								}
							} 
						?>
							<div class="g24-col-sm-24">
								<div class="form-group g24-col-sm-14">
									<label class="g24-col-sm-8 control-label "></label>
									<div class="g24-col-sm-10">
										<button class="btn btn-primary" onclick="submit_form()"><span class="icon icon-save"></span> บันทึก</button>
									</div>
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
