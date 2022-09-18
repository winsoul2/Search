<div class="layout-content">
    <div class="layout-content-body">
		<style>
			.center {
				text-align: center;
			}
			.right {
				text-align: right;
			}
			.left {
				text-align: left;
			}
			.option-radio {
				/* position: relative; */
    			/* left: 30px; */
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
			th {
				text-align: center;
			}
		</style>
		<h1 style="margin-bottom: 0">ใบเสร็จรายการเรียกเก็บ</h1>
		<?php $this->load->view('breadcrumb'); ?>
		<div class="row gutter-xs">
			<div class="col-xs-12 col-md-12">
				<div class="panel panel-body" style="padding-top:0px !important;">
					<h3></h3>
					<form action="<?php echo base_url(PROJECTPATH.'/admin/receipt_account_month_spkt_pdf'); ?>" target="_blank" id="receiptForm" method="GET">
					<!-- <input type="hidden" id="month" name="month" value="<?php echo $month; ?>">
					<input type="hidden" id="year" name="year" value="<?php echo $year; ?>">
					<input type="hidden" id="action_type" name="action_type" value=""> -->
						<div class="form-group g24-col-sm-24">
							<label class="g24-col-sm-8 control-label right"> ปี </label>
							<div class="g24-col-sm-4">
								<select id="year_choose" name="year" class="form-control" onChange="change_month_year()">
									<?php for($m=((date('Y')+543)-5); $m<=((date('Y')+543)+5); $m++){ ?>
										<option value="<?php echo $m; ?>" <?php echo $m==($year)?'selected':''; ?>><?php echo $m; ?></option>
									<?php } ?>
								</select>
							</div>
							<label class="g24-col-sm-1 control-label right"> เดือน </label>
							<div class="g24-col-sm-4">
								<select id="month_choose" name="month" class="form-control" onChange="change_month_year()">
									<?php foreach($month_arr as $key => $value){ ?>
										<option value="<?php echo $key; ?>" <?php echo $key==((int)$month)?'selected':''; ?>><?php echo $value; ?></option>
									<?php } ?>
								</select>
							</div>														
						</div>
						<div class="form-group g24-col-sm-24">
							<!-- <label class="g24-col-sm-6 text-right"><input type="radio" onclick="radio_check('1')" name="choose_receipt" value="1" checked=""></label> -->
							<label class="g24-col-sm-6 control-label right option-radio">
								<input type="radio" name="choose_receipt" value="1" checked="checked">
							</label>
							<label class="g24-col-sm-2 control-label right">							
							 หน่วยงานหลัก </label>
							<div class="g24-col-sm-11">
								<select id="department" name="department" class="form-control">
									<option value="">เลือกข้อมูล</option>
									<?php foreach($departments as $department): ?>
									<option value="<?php echo $department['id']; ?>"><?php echo $department['mem_group_name']; ?></option>
									<?php endforeach; ?>
								</select>
							</div>
						</div>
						<div class="form-group g24-col-sm-24">
							<label class="g24-col-sm-8 control-label right"> อำเภอ </label>
							<div class="g24-col-sm-4">
								<select id="faction" name="faction" class="form-control">
									<option value="">Select</option>
								</select>
							</div>
							<label class="g24-col-sm-2 control-label right"> หน่วยงานย่อย </label>
							<div class="g24-col-sm-5">
								<select id="level" name="level" class="form-control">
									<option value="">Select</option>
								</select>
							</div>
						</div>
						<div class="form-group g24-col-sm-24">
							<label class="g24-col-sm-8 control-label right"> แผ่นที่ </label>
							<div class="g24-col-sm-4">
								<select id="page_number" name="page_number" class="form-control">									
									<?php foreach($page_numbers as $key => $page_number): ?>
									<option value="<?php echo $key; ?>"><?php echo $page_number; ?></option>
									<?php endforeach; ?>
								</select>
							</div>
						</div>
						<div class="form-group g24-col-sm-24">
							<label class="g24-col-sm-6 control-label right option-radio">
								<input type="radio" name="choose_receipt" value="2">
							</label>
							<label class="g24-col-sm-2 control-label right">							
							รหัสสมาชิก </label>
							<div class="g24-col-sm-4">
								<input type="text" class="form-control" name="member_id">
							</div>
						</div>
						<div class="form-group g24-col-sm-24">
							<label class="g24-col-sm-6 control-label right option-radio">
								<input type="radio" name="choose_receipt" value="3">
							</label>
							<label class="g24-col-sm-2 control-label right">							
							รหัสสมาชิก </label>
							<div class="g24-col-sm-4">
								<input type="text" class="form-control" name="member_id_begin">
							</div>
							<label class="g24-col-sm-2 control-label right"> ถึง รหัสสมาชิก </label>
							<div class="g24-col-sm-5">
								<input type="text" class="form-control" name="member_id_end">
							</div>
						</div>
					</form>
					<div class="form-group g24-col-sm-24" style="margin-top:20px;">
						<label class="g24-col-sm-7 control-label right"></label>
						<div class="g24-col-sm-10">
							<button type="button" class="btn btn-primary" onclick="submit_form('real_print')" style="width:100%">แสดงใบเสร็จ</button>
						</div>
					</div>					
				</div>				 
			</div>
		</div>
	</div>
</div>
<?php
$link = array(
    'src' => PROJECTJSPATH.'assets/js/cashier_month.js',
    'type' => 'text/javascript'
);
echo script_tag($link);
?>