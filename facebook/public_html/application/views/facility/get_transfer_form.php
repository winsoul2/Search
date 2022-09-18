<form method="post" id="frm_transfer" action="">
	<input type="hidden" id="m_facility_take_id" name="facility_take_id" value="<?php echo $data['facility_take_id'] ?>">

	<div class="g24-col-sm-24">
		<div class="form-group">
			<label class="g24-col-sm-6 control-label">หน่วยงานเดิม</label>
			<div class="g24-col-sm-10">
				<p class="form-control-static"><?php echo $data['department_name']; ?></p>
			</div>
		</div>
	</div>

	<div class="g24-col-sm-24">
		<div class="form-group">
			<label class="g24-col-sm-6 control-label">หน่วยงานใหม่</label>
			<div class="g24-col-sm-10">
				<select id="m_department_id" name="department_id" class="form-control m-b-1">
					<option value="">เลือกหน่วยงาน</option>
					<?php foreach($department as $key => $value){ ?>
						<option value="<?php echo $value['department_id']; ?>"><?php echo $value['department_name']; ?></option>
					<?php } ?>
				</select>
			</div>
		</div>
	</div>

	<div class="g24-col-sm-24">
		<div class="form-group">
			<label class="g24-col-sm-6 control-label">ผู้ขอโอนย้าย</label>
			<div class="g24-col-sm-10">
				<div id="m_receiver_wrap"></div>
			</div>
		</div>
	</div>
</form>
<div class="clearfix"></div>