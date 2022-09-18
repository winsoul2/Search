<div class="layout-content">
    <div class="layout-content-body">
		<?php
		/*	
		if($_POST){
			//echo"<pre>";print_r($_POST);echo"</pre>";exit;
			foreach($_POST['data'] as $key => $value){
				$sql = "UPDATE coop_money_type SET 
					money_type_name_short = '".$value['money_type_name_short']."',
					money_type_name_eng = '".$value['money_type_name_eng']."',
					money_type_name_th = '".$value['money_type_name_th']."'
				WHERE id = '".$key."'
				";
				echo $sql."<br>";
				$mysqli->query($sql);
			}
			//exit;
			toast("บันทึกข้อมูลเรียบร้อยแล้ว");
			echo "<script>document.location.href = '/admin/coop_transactions_new.php';</script>";
		}
		*/

		?>
		
		<style>
		label {
			padding-top: 6px;
			text-align: right;
		  }
		</style>
		<h1 style="margin-bottom: 0">ประเภทเงินทำรายการ</h1>
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 padding-l-r-0">
		<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 padding-l-r-0">
		<?php $this->load->view('breadcrumb'); ?>
		</div>
		</div>

		<div class="row gutter-xs">
			<div class="col-xs-12 col-md-12">
				<div class="panel panel-body">
					<div class="bs-example" data-example-id="striped-table">
					<form action="<?php echo base_url(PROJECTPATH.'/setting_basic_data/coop_transactions_new_save'); ?>" method="POST">
						<div class="g24-col-sm-24">
							<div class="form-group g24-col-sm-8">
								<label class="g24-col-sm-10 control-label"></label>
								<label class="g24-col-sm-14 control-label" style="text-align:center;">ตัวย่อ</label>
							</div>
							<div class="form-group g24-col-sm-5">
								<label class="g24-col-sm-24 control-label" style="text-align:center;">ภาษาอังกฤษ</label>
							</div>
							<div class="form-group g24-col-sm-5">
								<label class="g24-col-sm-24 control-label" style="text-align:center;">ภาษาไทย</label>
							</div>
							<div class="form-group g24-col-sm-5">
								<label class="g24-col-sm-24 control-label" style="text-align:center;">ตัวย่อภาษาไทย</label>
							</div>
						</div>
					<?php  
						if(!empty($rs)){
							foreach(@$rs as $key => $row){ 
					?>
						<div class="g24-col-sm-24">
							<div class="form-group g24-col-sm-8">
								<label class="g24-col-sm-10 control-label"><?php echo @$row['description']?></label>
								<div class="g24-col-sm-14"><input type="text" class="form-control" name="data[<?php echo @$row['id']; ?>][money_type_name_short]" value="<?php echo @$row['money_type_name_short']; ?>"></div>
							</div>
							<div class="form-group g24-col-sm-5">
								<div class="g24-col-sm-24"><input type="text" class="form-control" name="data[<?php echo @$row['id']; ?>][money_type_name_eng]" value="<?php echo @$row['money_type_name_eng']; ?>"></div>
							</div>
							<div class="form-group g24-col-sm-5">
								<div class="g24-col-sm-24"><input type="text" class="form-control" name="data[<?php echo @$row['id']; ?>][money_type_name_th]" value="<?php echo @$row['money_type_name_th']; ?>"></div>
							</div>
							<div class="form-group g24-col-sm-5">
								<div class="g24-col-sm-24"><input type="text" class="form-control" name="data[<?php echo @$row['id']; ?>][money_type_name_th_short]" value="<?php echo @$row['money_type_name_th_short']; ?>"></div>
							</div>
						</div>
					<?php 
							}
						} 
					?>
						<div class="g24-col-sm-24">
							<div class="form-group g24-col-sm-21" style="text-align:center;">
								<input type="submit" class="btn btn-info" value="บันทึก">
							</div>
						</div>
					</form>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>