<div class="layout-content">
    <div class="layout-content-body">
	<style>
		label{
			padding-top:7px;
		}
		.control-label{
			padding-top:7px;
			text-align:right;
		}
		.control-label_2{
			padding-top:7px;
		}
		.center{
			text-align:center;
		}
	</style>
		<h1 style="margin-bottom: 0">ฌาปนกิจสงเคราะห์</h1>
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 padding-l-r-0">
			<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6 padding-l-r-0">
				<?php $this->load->view('breadcrumb'); ?>
			</div>
		</div>	
		<div class="row gutter-xs">
			<div class="col-xs-12 col-md-12">
				<div class="panel panel-body">
				<form action="<?php echo base_url(PROJECTPATH.'/setting_cremation_data/save_cremation_data_detail'); ?>/save_cremation_data_detail" method="POST" id="form1">
				<input type="hidden" name="cremation_id" value="<?php echo @$row['cremation_id']; ?>">
				<input type="hidden" name="cremation_detail_id" value="<?php echo @$row_detail['cremation_detail_id']; ?>">
					<div class="row m-b-1">
						<div class="form-group">
							<label class="control-label col-sm-3">ชื่อฌาปนกิจสงเคราะห์</label>
							<div class="col-sm-5"><input type="text" class="form-control" value="<?php echo @$row['cremation_name']; ?>" readonly></div>
						</div>
					</div>
					<div class="row m-b-1">
						<div class="form-group">
							<label class="control-label col-sm-3">ชื่อย่อ</label>
							<div class="col-sm-3"><input type="text" class="form-control" value="<?php echo @$row['cremation_name_short']; ?>" readonly></div>
						</div>
					</div>
					<div class="row m-b-1">
						<div class="form-group">
							<label class="control-label col-sm-3">มีผลวันที่</label>
							<div class="col-sm-3">
								<input id="start_date" name="start_date" class="form-control m-b-1" style="padding-left: 50px;" type="text" value="<?php echo $this->center_function->mydate2date(empty($row_detail) ? date("Y-m-d") : @$row_detail['start_date']); ?>" data-date-language="th-th" required title="กรุณากรอกวันที่มีผล">
								<span class="icon icon-calendar input-icon m-f-1"></span>
							</div>
						</div>
					</div>
					<div class="row m-b-1">
						<label class="control-label_2 col-sm-3"><h3>ประเภทสมาชิก</h3></label>
					</div>
					<?php for($i=1;$i<=5;$i++){ ?>
					<div class="row m-b-1">
						<div class="form-group">
							<label class="control-label col-sm-3">ประเภทที่ <?php echo ($i); ?></label>
							<div class="col-sm-3">
								<select class="form-control" name="member[<?php echo $i; ?>][type]">
									<option value="">เลือกประเภทสมาชิก</option>
									<?php foreach($mem_type as $key => $value){ ?>
									<option value="<?php echo $value['mem_type_id']; ?>" <?php echo $value['mem_type_id']==@$row_mem_type[$i]['mem_type_id']?'selected':'' ?>><?php echo $value['mem_type_name']; ?></option>
									<?php } ?>
								</select>
							</div>
							<label class="control-label col-sm-2">อายุไม่เกิน</label>
							<div class="col-sm-2">
								<input type="number" class="form-control" name="member[<?php echo $i; ?>][age_limit]" value="<?php echo @$row_mem_type[$i]['age_limit']; ?>">
							</div>
							<label class="control-label_2 col-sm-1">ปี</label>
						</div>
					</div>
					<?php } ?>
					<div class="row m-b-1">
						<label class="control-label_2 col-sm-3"><h3>ค่าสมัคร/ค่าบำรุง</h3></label>
					</div>
					<div class="row m-b-1">
						<div class="form-group">
							<label class="col-sm-1"></label>
							<label class=" col-sm-3"><input type="radio" id="maintenance_1" name="maintenance_fee_type" value="1" onclick="change_maintenance_radio()" <?php echo $row_detail['maintenance_fee_type']=='1'?'checked':''; ?>> แบบคิดครั้งเดียว</label>
							<label class=" col-sm-3"><input type="radio" id="maintenance_2" name="maintenance_fee_type" value="2" onclick="change_maintenance_radio()" <?php echo $row_detail['maintenance_fee_type']=='2'?'checked':''; ?>> เแบบคิดตามเดือน</label>
						</div>
					</div>
				<div id="maintenance_fee_type_1" style="display:none;">
					<div class="row m-b-1">
						<div class="form-group">
							<label class="control-label col-sm-2">ชำระเมื่อแรกเข้า</label>
						</div>
					</div>
					<?php for($i=1;$i<=5;$i++){ ?>
						<div class="row m-b-1">
							<div class="form-group">
								<label class="control-label col-sm-3">รายการที่ <?php echo ($i); ?></label>
								<div class="col-sm-3">
									<input type="text" class="form-control" name="maintenance_fee_detail[<?php echo $i; ?>][detail]" value="<?php echo @$row_maintenance_fee[$i]['maintenance_fee_detail']?>">
								</div>
								<label class="control-label col-sm-2">จำนวนเงิน</label>
								<div class="col-sm-2">
									<input type="text" class="form-control maintenance_fee_amount" name="maintenance_fee_detail[<?php echo $i; ?>][amount]" value="<?php echo @$row_maintenance_fee[$i]['maintenance_fee_amount']?>">
								</div>
								<label class="control-label_2 col-sm-1">บาท</label>
							</div>
						</div>
					<?php } ?>
					<div class="row m-b-1">
						<div class="form-group">
							<label class="control-label col-sm-3">ค่าบำรุงสมาคม/เรียกเก็บรายปี</label>
							<div class="col-sm-2">
								<input type="text" class="form-control" id="maintenance_fee" name="maintenance_fee" value="<?php echo $row_detail['maintenance_fee_type']=='1'?$row_detail['maintenance_fee']:''; ?>">
							</div>
							<label class="control-label_2 col-sm-1">บาทต่อปี</label>
						</div>
					</div>
				</div>
				<div id="maintenance_fee_type_2" style="display:none;">
					<div class="row m-b-1">
						<div class="form-group">
							<label class="control-label col-sm-2">เมื่อสมัครเดือน</label>
						</div>
					</div>
					<?php for($i=1;$i<=12;$i++){ ?>
						<div class="row m-b-1">
							<div class="form-group">
								<label class="control-label col-sm-3"> <?php echo $month_arr[$i]; ?></label>
								<div class="col-sm-3">
									<input type="hidden" name="maintenance_fee_detail_2[<?php echo $i; ?>][detail]" value="<?php echo $month_arr[$i]; ?>">
									<input type="text" class="form-control maintenance_fee_amount_2" name="maintenance_fee_detail_2[<?php echo $i; ?>][amount]" value="<?php echo @$row_maintenance_fee_2[$i]['maintenance_fee_amount']?>">
								</div>
								<label class="control-label_2 col-sm-1">บาท</label>
								<label class="control-label col-sm-1">มีผลเดือน</label>
								<div class="col-sm-2">
									<select class="form-control" name="maintenance_fee_detail_2[<?php echo $i; ?>][start_month]">
										<option value=""> เลือกเดือน </option>
										<?php foreach($month_arr as $key => $value){ 
										$month_default = $i+1;
										if($month_default > 12){
											$month_default = 1;
										}
										if(@$row_maintenance_fee_2[$i]['maintenance_fee_start_month']!=''){
											if(@$row_maintenance_fee_2[$i]['maintenance_fee_start_month'] == $key){
												$selected = 'selected';
											}else{
												$selected = '';
											}
										}else{
											if(@$month_default == $key){
												$selected = 'selected';
											}else{
												$selected = '';
											}
										}
										?>
											<option value="<?php echo $key; ?>" <?php echo $selected; ?>> <?php echo $value; ?> </option>
										<?php } ?>
									</select>
								</div>
							</div>
						</div>
					<?php } ?>
					<div class="row m-b-1">
						<div class="form-group">
							<label class="control-label col-sm-3">ค่าบำรุงสมาคม/เรียกเก็บรายปี</label>
							<div class="col-sm-2">
								<input type="text" class="form-control" id="maintenance_fee_2" name="maintenance_fee_2" value="<?php echo $row_detail['maintenance_fee_type']=='2'?$row_detail['maintenance_fee']:''; ?>">
							</div>
							<label class="control-label_2 col-sm-1">บาทต่อปี</label>
						</div>
					</div>
				</div>
					
					<div class="row m-b-1">
						<label class="control-label_2 col-sm-3"><h3>การจ่ายเงินสงเคราะห์ศพ</h3></label>
					</div>
					<div class="row m-b-1">
						<div class="form-group">
							<label class="col-sm-1"></label>
							<label class="col-sm-3"><input type="radio" id="pay_1" name="pay_type" value="1" onclick="change_pay_radio()" <?php echo $row_detail['pay_type']=='1'?'checked':''; ?>> แบบจ่ายตามจำนวนสมาชิก</label>
							<label class="col-sm-3"><input type="radio" id="pay_2" name="pay_type" value="2" onclick="change_pay_radio()" <?php echo $row_detail['pay_type']=='2'?'checked':''; ?>> แบบจ่ายคงที่</label>
						</div>
					</div>
					<div id="pay_type_1" style="display:none;">
						<div class="row m-b-1">
							<div class="form-group">
								<label class="control-label col-sm-3">จ่ายตามจำนวนสมาชิก</label>
								<div class="col-sm-2">
									<input type="text" class="form-control" name="pay_per_person" value="<?php echo $row_detail['pay_per_person']; ?>">
								</div>
								<label class="control-label_2 col-sm-1">บาทต่อคน</label>
							</div>
						</div>
					</div>
					<div id="pay_type_2" style="display:none;">
						<div class="row m-b-1">
							<div class="form-group">
								<label class="control-label col-sm-3">จำนวนเงินที่ได้รับเมื่อเสียชีวิต</label>
								<div class="col-sm-2">
									<input type="text" class="form-control" name="pay_per_person_stable" value="<?php echo $row_detail['pay_per_person_stable']; ?>">
								</div>
								<label class="control-label_2 col-sm-1">บาทต่อคน</label>
							</div>
						</div>
					</div>
					<div class="row m-b-1">
						<div class="form-group">
							<label class="control-label col-sm-3">ค่าดำเนินการสมาคม</label>
							<div class="col-sm-2">
								<input type="text" class="form-control" name="action_fee_percent" value="<?php echo $row_detail['action_fee_percent']; ?>">
							</div>
							<label class="control-label_2 col-sm-3">% ของเงินสงเคราะห์สมาชิก</label>
						</div>
					</div>
					<div class="row m-b-1">
						<div class="form-group center">
							<button type="button" class="btn btn-primary" style="width:100px" onclick="submit_form()"> ยืนยัน </button>
							<button type="button" class="btn btn-danger" style="width:100px" onclick="go_back('<?php echo @$row['cremation_id']; ?>')"> ยกเลิก </button>
						</div>
					</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div> 
<?php
$link = array(
    'src' => PROJECTJSPATH.'assets/js/add_cremation_data_detail.js',
    'type' => 'text/javascript'
);
echo script_tag($link);
?>