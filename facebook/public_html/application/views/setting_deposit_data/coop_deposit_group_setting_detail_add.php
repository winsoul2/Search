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
		.tab_1{
			margin-left: 40px;
		}
		.tab_2{
			margin-left: 60px;
		}
		.col-small{
			display: -webkit-inline-box;
		}
		.col-small label,input, select {
			margin-right: 10px;
		}
		.col-small-input {
			width: 100px;
		}
		.col-small-input-2 {
			width: 50px;
		}
		
		.percent_fee_w{
			margin-left: -25px;
		}
		
		@media (max-width: 768px) {
			.percent_fee_w{
				margin-left: 68px;
			}		
		}
				
		
	</style>
		<h1 style="margin-bottom: 0">ประเภทเงินฝาก</h1>
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 padding-l-r-0">
			<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6 padding-l-r-0">
				<?php $this->load->view('breadcrumb'); ?>
			</div>
		</div>	
		<div class="row gutter-xs">
			<div class="col-xs-12 col-md-12">
				<div class="panel panel-body">
				<form id='form1' data-toggle="validator" novalidate="novalidate" action="<?php echo base_url(PROJECTPATH.'/setting_deposit_data/coop_deposit_group_setting_detail_save'); ?>" method="post">
				<input type="hidden" class="form-control" name="id" id="id" value="<?php echo @$_GET['id']; ?>">
					<input type="hidden" class="form-control" name="status" id="status" value="<?php echo @$_GET['status']; ?>">
					<input type="hidden" class="form-control" name="id_type_name" id="id_type_name" value="<?php echo @$_GET['id_type_name']; ?>">
					<div class="row m-b-1">
						<label class="control-label g24-col-sm-6">รหัส</label>
						<div class="g24-col-sm-6"><input type="text" class="form-control" name="group_name_transaction" id="group_name_transaction" value="<?php echo @$row['id_type_name']; ?>" readonly></div>
					</div>
					<div class="row">
					<div class="row m-b-1">
						<label class="control-label g24-col-sm-6">ชื่อประเภท</label>
						<div class="g24-col-sm-6"><input type="text" class="form-control" name="type_name_transection" id="type_name_transection" value="<?php echo @$row['type_name_transection']; ?>"></div>
					</div>
						<div class="row">
							<label class="g24-col-sm-6"></label>
							<div class="g24-col-sm-16 tab_1 col-small m-b-1">
								<label class="control-label_2">สถานะการใช้งาน </label>
								<select id="status" name="status" class="form-control col-small-input">
									<option value="1" <?php if($row['status'] == 1) { ?> selected="selected"<?php } ?>>ใช้งาน</option>
									<option value="0" <?php if($row['status'] == 0) { ?> selected="selected"<?php } ?>>ไม่ใช้งาน</option>
								</select>
							</div>
						</div>
					<div class="row">
						<label class="control-label g24-col-sm-6">มีผลวันที่</label>
						<div class="g24-col-sm-3">
							<input id="start_date" name="start_date" class="form-control m-b-1" style="padding-left: 30px;" type="text" value="<?php echo empty($row_detail['createdatetime']) ? "" : $this->center_function->mydate2date($row_detail['createdatetime']); ?>" data-date-language="th-th" required title="กรุณากรอกวันที่มีผล">
							<span class="icon icon-calendar input-icon m-f-1"></span>
						</div>
						<label class="control-label g24-col-sm-1">ถึงวันที่</label>
						<div class="g24-col-sm-3">
							<input id="end_date" name="end_date" class="form-control m-b-1" style="padding-left: 30px;" type="text" value="<?php echo empty($row_detail['updatedatetime']) ? "" : $this->center_function->mydate2date($row_detail['updatedatetime']); ?>" data-date-language="th-th">
							<span class="icon icon-calendar input-icon m-f-1"></span>
						</div>
					</div>

					<div class="row">&nbsp;</div>
					<div class="row m-b-1">
						<div class="form-group center">
							<button type="button" class="btn btn-primary" style="width:100px" onclick="submit_form('<?php echo @$_GET['id']; ?>')"> ยืนยัน </button>
							<button type="button" class="btn btn-danger" style="width:100px" onclick="go_back_add('<?php echo @$_GET['id']; ?>')"> ยกเลิก </button>
						</div>
					</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div> 
<?php
$v = date('YmdHis');
$link = array(
    'src' => 'assets/js/coop_deposit_type_setting_detail_add.js?v='.$v,
    'type' => 'text/javascript'
);
echo script_tag($link);
?>
