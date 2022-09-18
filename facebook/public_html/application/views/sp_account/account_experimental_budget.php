<div class="layout-content">
    <div class="layout-content-body">
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
		<h1 style="margin-bottom: 0">รายงานงบทดลอง</h1>
		<?php $this->load->view('breadcrumb'); ?>
		<div class="row gutter-xs">
			<div class="col-xs-12 col-md-12">
				<div class="panel panel-body" style="padding-top:0px !important;">
				<form action="<?php echo base_url(PROJECTPATH.'/sp_account/'.$path.'/coop_account_experimental_budget_excel'); ?>" id="form1" method="GET">
					<div class="form-group g24-col-sm-24">
						<div class="g24-col-sm-5 right">
							<h3>รายวัน</h3>
						</div>
					</div>
					<div class="form-group g24-col-sm-24">
						<label class="g24-col-sm-6 control-label right"> วันที่ </label>
						<div class="g24-col-sm-4">
							<div class="input-with-icon">
								<div class="form-group">
									<input id="report_date" name="report_date" class="form-control m-b-1 mydate" style="padding-left: 50px;" type="text" value="<?php echo $this->center_function->mydate2date(date("Y-m-d")); ?>" data-date-language="th-th">
									<span class="icon icon-calendar input-icon m-f-1"></span>
								</div>
							</div>
						</div>
					</div>
					<div class="form-group g24-col-sm-24">
						<label class="g24-col-sm-6 control-label right"></label>
						<div class="g24-col-sm-2">
							<button class="btn btn-primary btn-after-input" type="button"  onclick="check_empty('1',2)"><span> PDF</span></button>
						</div>
						<div class="g24-col-sm-2">
							<button class="btn btn-default btn-after-input" type="button"  onclick="check_empty('1',1)"><span> EXCEL</span></button>
						</div>
					</div>
				</form>
				<form action="<?php echo base_url(PROJECTPATH.'/sp_account/'.$path.'/coop_account_experimental_budget_excel'); ?>" id="form2" method="GET">
					<div class="form-group g24-col-sm-24">
						<div class="g24-col-sm-5 right">
							<h3>รายเดือน</h3>
						</div>
					</div>
					<div class="form-group g24-col-sm-24">
						<label class="g24-col-sm-6 control-label right"> เดือน </label>
						<div class="g24-col-sm-4">
							<select id="report_month" name="month" class="form-control">
								<?php foreach($month_arr as $key => $value){ ?>
									<option value="<?php echo $key; ?>" <?php echo $key==date('m')?'selected':''; ?>><?php echo $value; ?></option>
								<?php } ?>
							</select>
						</div>
					</div>
					<div class="form-group g24-col-sm-24">
						<label class="g24-col-sm-6 control-label right"> ปี </label>
						<div class="g24-col-sm-4">
							<select id="report_year" name="year" class="form-control">
								<?php for($i=((date('Y')+543)-5); $i<=((date('Y')+543)+5); $i++){ ?>
									<option value="<?php echo $i; ?>" <?php echo $i==(date('Y')+543)?'selected':''; ?>><?php echo $i; ?></option>
								<?php } ?>
							</select>
						</div>
					</div>
					<div class="form-group g24-col-sm-24">
						<label class="g24-col-sm-6 control-label right"></label>
						<div class="g24-col-sm-2">
							<button class="btn btn-primary btn-after-input" type="button"  onclick="check_empty('2',2)"><span> PDF</span></button>
						</div>
						<div class="g24-col-sm-2">
							<button class="btn btn-default btn-after-input" type="button"  onclick="check_empty('2',1)"><span> EXCEL</span></button>
						</div>
					</div>
				</form>
                    <form action="<?php echo base_url(PROJECTPATH.'/sp_account/'.$path.'/coop_account_experimental_budget_excel'); ?>" id="form3" method="GET">
                        <div class="form-group g24-col-sm-24">
                            <div class="g24-col-sm-5 right">
                                <h3>รายปี</h3>
                            </div>
                        </div>
                        <div class="form-group g24-col-sm-24">
                            <label class="g24-col-sm-6 control-label right"> ปี </label>
                            <div class="g24-col-sm-4">
                                <select id="report_year_only" name="year" class="form-control">
                                    <?php for($i=((date('Y')+543)-5); $i<=((date('Y')+543)+5); $i++){ ?>
                                        <option value="<?php echo $i; ?>" <?php echo $i==(date('Y')+543)?'selected':''; ?>><?php echo $i; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
						</div>
						<div class="form-group g24-col-sm-24">
							<label class="g24-col-sm-6 control-label right"></label>
							<div class="g24-col-sm-2">
								<button class="btn btn-primary btn-after-input" type="button"  onclick="check_empty('3',2)"><span> PDF</span></button>
							</div>
							<div class="g24-col-sm-2">
								<button class="btn btn-default btn-after-input" type="button"  onclick="check_empty('3',1)"><span> EXCEL</span></button>
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
function check_empty(type,format){
	var report_date = '';
	var month = '';
	var year = '';
	if(type == '1'){
		report_date = $('#report_date').val();
	}else if(type == '2'){
		month = $('#report_month').val();
		year = $('#report_year').val();
	}else{
		year = $('#report_year_only').val();
	}
	$.ajax({
		 url: base_url+'/sp_account/<?php echo $path;?>/ajax_check_account_experimental_budget',	
		 method:"post",
		 data:{ 
			 report_date: report_date, 
			 month: month,
			 year: year
		 },
		 dataType:"text",
		 success:function(data){
			if(data == 'success'){
				if(type == 1) {
					if(format == 1) {
						$('#form1').attr('action', base_url+"/sp_account/<?php echo $path;?>/coop_account_experimental_budget_excel");
					} else if(format == 2) {
						$('#form1').attr('action', base_url+"/sp_account/<?php echo $path;?>/coop_account_experimental_budget_pdf");
					}
				} else if (type == 2) {
					if(format == 1) {
						$('#form2').attr('action', base_url+"/sp_account/<?php echo $path;?>/coop_account_experimental_budget_excel");
					} else if(format == 2) {
						$('#form2').attr('action', base_url+"/sp_account/<?php echo $path;?>/coop_account_experimental_budget_pdf");
					}
				} else if (type == 3) {
					if(format == 1) {
						$('#form3').attr('action', base_url+"/sp_account/<?php echo $path;?>/coop_account_experimental_budget_excel");
					} else if(format == 2) {
						$('#form3').attr('action', base_url+"/sp_account/<?php echo $path;?>/coop_account_experimental_budget_pdf");
					}
				}

				$('#form'+type).submit();
			}else{
				$('#alertNotFindModal').appendTo("body").modal('show');
			}
		 }
	});
}
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
});
</script>
