<div class="layout-content">
    <div class="layout-content-body">
<?php
$a_mthai[1]    =    'มกราคม';
$a_mthai[2]    =    'กุมภาพันธ์';
$a_mthai[3]    =    'มีนาคม';
$a_mthai[4]    =    'เมษายน';
$a_mthai[5]    =    'พฤษภาคม';
$a_mthai[6]    =    'มิถุนายน';
$a_mthai[7]    =    'กรกฎาคม';
$a_mthai[8]    =    'สิงหาคม';
$a_mthai[9]    =    'กันยายน ';
$a_mthai[10]    =    'ตุลาคม';
$a_mthai[11]    =    'พฤศจิกายน';
$a_mthai[12]    =    'ธันวาคม';

?>

<?php if (@$act != "add") { ?>
	<h1 style="margin-bottom: 0">อายุเกษียณ</h1>
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
					<form class="form form-horizontal" action="<?php echo base_url(PROJECTPATH.'/setting_member_data/coop_retire_save'); ?>" method="post">
						<input name="profile_id" type="hidden" value="<?php echo @$row['profile_id'] ?>">
						<div class="row">
							<label class="col-sm-2 control-label" for="form-control-1">อายุเกษียณ</label>
							<div class="col-sm-5">
							  <input id="form-control-1" name="retire_age" class="form-control m-b-1" type="number" value="<?php echo @$row['retire_age']; ?>" required maxlength="3">
							</div>
						</div>

					   <div class="row">
						<label class="col-sm-2 control-label" for="form-control-1">เดือนเกษียณ</label>
						<div class="col-sm-5">
						  <select id="form-control-6" class="form-control m-b-1" name="retire_month" required>
							  <option value="">เลือกเดือน</option> 
							  <?php  foreach ($a_mthai as $key => $val) { ?>
								<?php if (@$row['retire_month'] == $key) { ?>
								<option value="<?php echo @$key; ?>" selected><?php echo @$val; ?></option> 
								<?php }else{ ?>
								<option value="<?php echo @$key; ?>"><?php echo @$val; ?></option> 
								<?php } ?>
							  <?php } ?> 
						  </select>
						</div>
					  </div>

					  <div class="form-group m-t-1">
						<label class="col-sm-2 control-label" for="form-control-1"></label>
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


