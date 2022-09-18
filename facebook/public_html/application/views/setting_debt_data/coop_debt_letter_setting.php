<div class="layout-content">
    <div class="layout-content-body">
<?php if (@$act != "add") { ?>
	<h1 style="margin-bottom: 0">ออกจดหมายติดตามหนี้</h1>
	<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 padding-l-r-0">
	<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 padding-l-r-0">
	<?php $this->load->view('breadcrumb'); ?>
	</div>
	</div>
<?php } ?>

		<div class="row gutter-xs"> 
			<div class="row">
				<div  class="col-md-8 col-md-offset-2">
                   <br>
                   <br>
	                 <br>

					<form class="form form-horizontal" action="<?php echo base_url(PROJECTPATH.'/setting_debt_data/coop_debt_letter_setting_save'); ?>" method="post">
						<?php
							foreach($rows as $row) {
						?>
						<!-- <input name="letter_setting" type="hidden" value="<?php echo @$row['id'] ?>"> -->
						<div class="row">
							<label class="col-sm-5 control-label" for="form-control-1"><?php echo $row['text']?></label>
							<div class="col-sm-2">
							  <input id="form-control-1" name="num_letter_<?php echo $row['id']?>" class="form-control m-b-1" type="number" value="<?php echo @$row['num_letter']; ?>" required maxlength="3" style="text-align: center;">
							</div>
							<label class="col-sm-1 control-label" for="form-control-1"><?php echo $row['unit']?></label>
						</div>
						<?php
							}
						?>

					  <div class="form-group m-t-1">
						<label class="col-sm-5 control-label" for="form-control-1"></label>
						<div class="col-sm-5">
							<button type="submit" class="btn btn-primary min-width-100">ตกลง</button>
						</div>
					  </div>  
					</form>
				</div>
			</div>
		</div>
	</div>
</div>


