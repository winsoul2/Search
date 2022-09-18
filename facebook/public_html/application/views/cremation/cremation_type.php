<div  class="col-sm-12">	
	<div class="row">
		<label class="control-label_2 col-sm-3"><h3>ประเภทสมาชิก</h3></label>
		<input id="start_date" name="start_date" class="form-control m-b-1"type="hidden" value="<?php echo @$row['start_date']; ?>">
	</div>
	<?php for($i=1;$i<=5;$i++){ ?>
	<div class="row">
		<div class="form-group">
			<label class="control-label col-sm-3">ประเภทที่ <?php echo ($i); ?></label>
			<label class="control-label col-sm-3 f-normal"><?php echo @$row_mem_type[$i]['mem_type_name']; ?></label>
			<label class="control-label col-sm-2">อายุไม่เกิน</label>
			<label class="control-label col-sm-2 f-normal"><?php echo @$row_mem_type[$i]['age_limit']; ?></label>
			<label class="control-label col-sm-1">ปี</label>
		</div>
	</div>
	<?php } ?>
	<div class="row">
		<label class="control-label_2 col-sm-3"><h3>ค่าสมัคร/ค่าบำรุง</h3></label>
	</div>
	<div class="row">
		<div class="form-group">
			<label class="col-sm-1"></label>
			<label class=" col-sm-5"><i class="fa <?php echo (@$row['maintenance_fee_type']=='1')?'fa-circle':'fa-circle-o';?>"></i> แบบคิดครั้งเดียว</label>
			<label class=" col-sm-5"><i class="fa <?php echo (@$row['maintenance_fee_type']=='2')?'fa-circle':'fa-circle-o';?>"></i> เแบบคิดตามเดือน</label>
		</div>
	</div>
	<?php if((@$row['maintenance_fee_type']=='1')){?>
		<div class="row">
			<div class="form-group">
				<label class="control-label col-sm-2">ชำระเมื่อแรกเข้า</label>
			</div>
		</div>
		<?php for($i=1;$i<=5;$i++){ ?>
			<div class="row">
				<div class="form-group">
					<label class="control-label col-sm-3">รายการที่ <?php echo ($i); ?></label>
					<label class="control-label col-sm-3 text-left f-normal"><?php echo @$row_maintenance_fee[$i]['maintenance_fee_detail']; ?></label>
					<label class="control-label col-sm-2 text-left">จำนวนเงิน</label>
					<label class="control-label col-sm-2 f-normal"><?php echo number_format(@$row_maintenance_fee[$i]['maintenance_fee_amount']); ?></label>
					<label class="control-label col-sm-1">บาท</label>
				</div>
			</div>
		<?php } ?>
		<div class="row">
			<div class="form-group">
				<label class="control-label col-sm-4">ค่าบำรุงสมาคม/เรียกเก็บรายปี</label>
				<label class="control-label col-sm-2 f-normal"><?php echo number_format(@$row['maintenance_fee_type']=='1'?$row['maintenance_fee']:''); ?></label>
				<label class="control-label col-sm-2 text-left">บาทต่อปี</label>
			</div>
		</div>
	<?php }else{?>
		<div class="row">
			<div class="form-group">
				<label class="control-label col-sm-2">เมื่อสมัครเดือน</label>
			</div>
		</div>
		<?php for($i=1;$i<=12;$i++){ ?>
			<div class="row">
				<div class="form-group">
					<label class="control-label col-sm-4"> <?php echo $month_arr[$i]; ?></label>
					<label class="control-label col-sm-2 f-normal"><?php echo number_format(@$row_maintenance_fee_2[$i]['maintenance_fee_amount']); ?></label>
					<label class="control-label col-sm-1 text-left">บาท</label>
					<label class="control-label col-sm-2">มีผลเดือน</label>
					<label class="control-label col-sm-2 f-normal text-left"><?php echo @$month_arr[@$row_maintenance_fee_2[$i]['maintenance_fee_start_month']]; ?></label>
				</div>
			</div>
		<?php } ?>
		<div class="row">
			<div class="form-group">
				<label class="control-label col-sm-4">ค่าบำรุงสมาคม/เรียกเก็บรายปี</label>
				<label class="control-label col-sm-2 f-normal"><?php echo $row['maintenance_fee_type']=='2'?$row['maintenance_fee']:''; ?></label>
				<label class="control-label col-sm-2 text-left">บาทต่อปี</label>
			</div>
		</div>
	<?php } ?>
		
	<div class="row">
		<label class="control-label_2 col-sm-3"><h3>การจ่ายเงินสงเคราะห์ศพ</h3></label>
	</div>
	<div class="row">
		<div class="form-group">
			<label class="col-sm-1"></label>
			<label class="col-sm-5"><i class="fa <?php echo (@$row['pay_type']=='1')?'fa-circle':'fa-circle-o';?>"></i> แบบจ่ายตามจำนวนสมาชิก</label>
			<label class="col-sm-5"><i class="fa <?php echo (@$row['pay_type']=='2')?'fa-circle':'fa-circle-o';?>"></i> แบบจ่ายคงที่</label>
		</div>
	</div>
	<?php if(@$row['pay_type']=='1'){?>
	<div class="row">
		<div class="form-group">
			<label class="control-label col-sm-3">จ่ายตามจำนวนสมาชิก</label>
			<label class="control-label col-sm-2 f-normal"><?php echo $row['pay_per_person']; ?></label>
			<label class="control-label col-sm-7 text-left">บาทต่อคน</label>
		</div>
	</div>
	<?php }else{?>
	<div class="row">
		<div class="form-group">
			<label class="control-label col-sm-3">จำนวนเงินที่ได้รับเมื่อเสียชีวิต</label>
			<label class="control-label col-sm-2 f-normal"><?php echo number_format($row['pay_per_person_stable']); ?></label>
			<label class="control-label col-sm-7 text-left">บาทต่อคน</label>
		</div>
	</div>
	<?php }?>
	<div class="row">
		<div class="form-group">
			<label class="control-label col-sm-3">ค่าดำเนินการสมาคม</label>
			<label class="control-label col-sm-2 f-normal"><?php echo number_format($row['action_fee_percent']); ?></label>
			<label class="control-label col-sm-7 text-left">% ของเงินสงเคราะห์สมาชิก</label>
		</div>
	</div>
	<p>&nbsp;</p>
</div>