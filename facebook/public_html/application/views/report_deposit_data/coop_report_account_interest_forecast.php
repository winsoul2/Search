<style>
	.modal-dialog {
        width: 700px;
    }
</style>
<div class="layout-content">
    <div class="layout-content-body">
		<?php
		$month_arr = array('1'=>'มกราคม','2'=>'กุมภาพันธ์','3'=>'มีนาคม','4'=>'เมษายน','5'=>'พฤษภาคม','6'=>'มิถุนายน','7'=>'กรกฎาคม','8'=>'สิงหาคม','9'=>'กันยายน','10'=>'ตุลาคม','11'=>'พฤศจิกายน','12'=>'ธันวาคม');
		?>
		<style>
			.modal-header-alert {
				padding:9px 15px;
				border:1px solid #FF0033;
				background-color: #FF0033;
				color: #fff;
				-webkit-border-top-left-radius: 5px;
				-webkit-border-top-right-radius: 5px;
				-moz-border-radius-topleft: 5px;
				-moz-border-radius-topright: 5px;
				border-top-left-radius: 5px;
				border-top-right-radius: 5px;
			}
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
		<h1 style="margin-bottom: 0">รายงานดอกเบี้ยเงินฝากล่วงหน้า</h1>
		<?php $this->load->view('breadcrumb'); ?>
		<div class="row gutter-xs">
			<div class="col-xs-12 col-md-12">
				<div class="panel panel-body" style="padding-top:0px !important;">
					<form action="<?php echo base_url(PROJECTPATH.'/report_deposit_data/coop_report_account_interest_forecast_preview'); ?>" id="form1" method="GET" target="_blank">
					<h3></h3>
					<div class="form-group g24-col-sm-24">
						<label class="g24-col-sm-6 control-label right"> เลือกวันที่ </label>
						<div class="g24-col-sm-4">
							<div class="input-with-icon">
								<div class="form-group">
									<input id="start_date" name="start_date" class="form-control m-b-1 mydate" style="padding-left: 50px;" type="text" value="<?php echo $this->center_function->mydate2date(date('Y-m-d')); ?>" data-date-language="th-th">
									<span class="icon icon-calendar input-icon m-f-1"></span>
								</div>
							</div>
						</div> 
						<label class="g24-col-sm-3 control-label right end-date-label"> ถึงวันที่ </label>
						<div class="g24-col-sm-4 end-date-label">
							<div class="input-with-icon">
								<div class="form-group">
									<input id="end_date" name="end_date" class="form-control m-b-1 mydate" style="padding-left: 50px;" type="text" value="<?php echo $this->center_function->mydate2date(date('Y-m-d')); ?>" data-date-language="th-th">
									<span class="icon icon-calendar input-icon m-f-1"></span>
								</div>
							</div>
						</div>
					</div>
					<div class="form-group g24-col-sm-24">
						<label class="g24-col-sm-6 control-label right">ประเภทบัญชี</label>
						<div class="g24-col-sm-11">
							<select name="type_id" id="type_id" onchange="" class="form-control">
							    <option value="">- ทั้งหมด -</option>
							    <?php
							    	foreach ($type_ids as $key => $type) {
							    ?>
									<option value="<?php echo $type["type_id"]?>"><?php echo $type["type_code"]?> <?php echo $type["type_name"]?></option>
								<?php
									}
								?>
							</select>
						</div>
					</div>
					<div class="form-group g24-col-sm-24">
						<label class="g24-col-sm-6 control-label right">การแสดงผล</label>
						<div class="g24-col-sm-11">
							<div class="form-group">
								<label><input type="radio" name="report_type" value="0" checked="checked" class="report_type"> ดอกเบี้ยยอดรวม</label> &nbsp;
								<label><input type="radio" name="report_type" value="1" class="report_type"> ดอกเบี้ยรายคน</label>
							</div>
						</div>
					</div>
					<div class="form-group g24-col-sm-24">
						<label class="g24-col-sm-6 control-label right"></label>
						<div class="g24-col-sm-7">
							<input type="button" class="btn btn-primary" style="width:100%" value="แสดงข้อมูล" onclick="check_empty()">
						</div>
						<div class="g24-col-sm-4">
							<a id="btn_link_export" href="<?php echo PROJECTPATH; ?>/report_deposit_data/coop_report_account_interest_forecast_excel?type_id=&start_date=<?php echo $this->center_function->mydate2date(date('Y-m-d')); ?>&end_date=<?php echo $this->center_function->mydate2date(date('Y-m-d')); ?>&report_type=0" target="_blank" class="btn btn-default" style="width:100%">Export Excel</a>
						</div>
					</div>
				</form>				
				</div>
			</div>
		</div>
	</div>
</div>
  
<script>	
	var base_url = $('#base_url').attr('class');
	$( document ).ready(function() {
		$(".mydate").datepicker({
			prevText : "ก่อนหน้า",
			nextText: "ถัดไป",
			currentText: "Today",
			changeMonth: true,
			changeYear: true,
			isBuddhist: true,
			monthNamesShort: ['ม.ค.', 'ก.พ.', 'มี.ค.', 'เม.ย.', 'พ.ค.', 'มิ.ย.', 'ก.ค.', 'ส.ค.', 'ก.ย.', 'ต.ค.', 'พ.ย.', 'ธ.ค.'],
			dayNamesMin: ['อา', 'จ', 'อ', 'พ', 'พฤ', 'ศ', 'ส'],
			constrainInput: true,
			dateFormat: "dd/mm/yy",
			yearRange: "c-50:c+10",
			autoclose: true,
		});
		$("#type_date").change(function() {
			if($(this).val() == 1) {
				$(".end-date-label").hide();
			} else {
				$(".end-date-label").show();
			}
		});
		
		function link_export() {
			$("#btn_link_export").prop("href", base_url + "/report_deposit_data/coop_report_account_interest_forecast_excel?type_id=" + $("#type_id").val() + "&start_date=" + $("#start_date").val() + "&end_date=" + $("#end_date").val() + "&report_type=" + $('input[name=report_type]:checked', '#form1').val());
		}
		$("#type_id").change(function() {
			link_export();
		});
		$("#start_date").change(function() {
			link_export();
		});
		$("#end_date").change(function() {
			link_export();
		});
		$(".report_type").change(function() {
			link_export();
		});
	});

	function daysInMonth(month, year) {
		return new Date(year, month, 0).getDate();
	}
	
	function check_empty(){
		var now_date = new Date();
		var end_date_str = $('#end_date').val();
		var end_date = new Date((parseInt(end_date_str.substr(6, 4)) - 543) + "-" + end_date_str.substr(3, 2) + "-" + end_date_str.substr(0, 2))
		var next_date = now_date;
		next_date.setMonth(next_date.getMonth() + 1);
		var last_date = new Date(next_date.getFullYear() + "-" + (next_date.getMonth() + 1) + "-" + daysInMonth(next_date.getMonth() + 1, next_date.getFullYear()));
		
		if(end_date > last_date){
			swal('ดูดอกเบี้ยเงินฝากล่วงหน้าได้ไม่เกิน 1 เดือน','','warning');
			return;
		}
		
		$('#form1').submit();
	}	
</script>