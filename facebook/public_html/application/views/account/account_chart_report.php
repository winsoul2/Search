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
		<h1 style="margin-bottom: 0">รายงานบัญชีแยกประเภท</h1>
		<?php $this->load->view('breadcrumb'); ?>
		<div class="row gutter-xs">
			<div class="col-xs-12 col-md-12">
				<div class="panel panel-body" style="padding-top:0px !important;" id="search-div">
					<form action="<?php echo base_url(PROJECTPATH.'/account/account_chart_report_excel'); ?>" id="form_dialy" method="GET" target="_blank">
						<div class="form-group g24-col-sm-24">
							<div class="g24-col-sm-5 right">
								<h3>รายวัน</h3>
							</div>
						</div>
						<div class="form-group g24-col-sm-24">
							<label class="g24-col-sm-6 control-label right"> เลขที่บัญชี </label>
							<div class="g24-col-sm-9">
								<div class="input-with-icon">
									<div class="form-group">
										<select id="dialy_chart_id" name="account_chart_id" class="form-control m-b-1 js-data-example-ajax">
                                            <option value="">ทั้งหมด</option>
                                            <?php 
                                                foreach($account_charts as $key => $row) {
                                            ?>
                                            <option value="<?php echo $row['account_chart_id']; ?>"><?php echo $row['account_chart_id']." : ".$row['account_chart'];?></option>
                                            <?php
                                                }
                                            ?>
                                        </select>
									</div>
								</div>
							</div>
						</div>
						<div class="form-group g24-col-sm-24">
							<label class="g24-col-sm-6 control-label right"> วันที่ </label>
							<div class="g24-col-sm-4">
								<div class="input-with-icon">
									<div class="form-group">
										<input id="from_date" name="from_date" class="form-control m-b-1 mydate" style="padding-left: 50px;" type="text" value="<?php echo $this->center_function->mydate2date(date('Y-m-d')); ?>" data-date-language="th-th">
										<span class="icon icon-calendar input-icon m-f-1"></span>
									</div>
								</div>
							</div>
							<label class="g24-col-sm-1 control-label right"> ถึง </label>
							<div class="g24-col-sm-4">
								<div class="input-with-icon">
									<div class="form-group">
										<input id="thru_date" name="thru_date" class="form-control m-b-1 mydate" style="padding-left: 50px;" type="text" value="<?php echo $this->center_function->mydate2date(date('Y-m-d')); ?>" data-date-language="th-th">
										<span class="icon icon-calendar input-icon m-f-1"></span>
									</div>
								</div>
							</div>
						</div>
						<div class="form-group g24-col-sm-24">
							<label class="g24-col-sm-6 control-label right"></label>
							<div class="g24-col-sm-2"> 
								<button class="btn btn-primary btn-after-input" type="button" onclick="check_empty('1',1)"><span> แสดงผล PDF</span></button>
							</div>
							<div class="g24-col-sm-2"> 
								<button class="btn btn-default btn-after-input" type="button" onclick="check_empty('2',1)"><span> แสดงผล Excel</span></button>
							</div>
						</div>
					</form>
					<form action="<?php echo base_url(PROJECTPATH.'/account/account_chart_report_excel'); ?>" id="form_monthly" method="GET" target="_blank">
						<div class="form-group g24-col-sm-24">
							<div class="g24-col-sm-5 right">
								<h3>รายเดือน</h3>
							</div>
						</div>
						<div class="form-group g24-col-sm-24">
							<label class="g24-col-sm-6 control-label right"> เลขที่บัญชี </label>
							<div class="g24-col-sm-9">
								<div class="input-with-icon">
									<div class="form-group">
										<select id="monthly_chart_id" name="account_chart_id" class="form-control m-b-1 js-data-example-ajax">
                                            <option value="">ทั้งหมด</option>
                                            <?php 
                                                foreach($account_charts as $key => $row) {
                                            ?>
                                            <option value="<?php echo $row['account_chart_id']; ?>"><?php echo $row['account_chart_id']." : ".$row['account_chart'];?></option>
                                            <?php
                                                }
                                            ?>
                                        </select>
									</div>
								</div>
							</div>
						</div>
						<div class="form-group g24-col-sm-24">
							<label class="g24-col-sm-6 control-label right"> เดือน </label>
							<div class="g24-col-sm-4">
								<select id="report_month_month" name="month" class="form-control">
									<?php foreach($month_arr as $key => $value){ ?>
										<option value="<?php echo $key; ?>" <?php echo $key==date('m')?'selected':''; ?>><?php echo $value; ?></option>
									<?php } ?>
								</select>
							</div>
						</div>
						<div class="form-group g24-col-sm-24">
							<label class="g24-col-sm-6 control-label right"> ปี </label>
							<div class="g24-col-sm-4">
								<select id="report_month_year" name="year" class="form-control">
									<?php for($i=((date('Y')+543)-5); $i<=((date('Y')+543)+5); $i++){ ?>
										<option value="<?php echo $i; ?>" <?php echo $i==(date('Y')+543)?'selected':''; ?>><?php echo $i; ?></option>
									<?php } ?>
								</select>
							</div>
						</div>
						<div class="form-group g24-col-sm-24">
							<label class="g24-col-sm-6 control-label right"></label>
							<div class="g24-col-sm-2"> 
								<button class="btn btn-primary btn-after-input" type="button" onclick="check_empty('1',2)"><span> แสดงผล PDF</span></button>
							</div>
							<div class="g24-col-sm-2"> 
								<button class="btn btn-default btn-after-input" type="button" onclick="check_empty('2',2)"><span> แสดงผล Excel</span></button>
							</div>
						</div>
					</form>
					<form action="<?php echo base_url(PROJECTPATH.'/account/account_chart_report_excel'); ?>" id="form_yearly" method="GET" target="_blank">
						<div class="form-group g24-col-sm-24">
							<div class="g24-col-sm-5 right">
								<h3>รายปี</h3>
							</div>
						</div>
						<div class="form-group g24-col-sm-24">
							<label class="g24-col-sm-6 control-label right"> เลขที่บัญชี </label>
							<div class="g24-col-sm-9">
								<div class="input-with-icon">
									<div class="form-group">
										<select id="yearly_chart_id" name="account_chart_id" class="form-control m-b-1 js-data-example-ajax">
                                            <option value="">ทั้งหมด</option>
                                            <?php 
                                                foreach($account_charts as $key => $row) {
                                            ?>
                                            <option value="<?php echo $row['account_chart_id']; ?>"><?php echo $row['account_chart_id']." : ".$row['account_chart'];?></option>
                                            <?php
                                                }
                                            ?>
                                        </select>
									</div>
								</div>
							</div>
						</div>
						<div class="form-group g24-col-sm-24">
							<label class="g24-col-sm-6 control-label right"> ปี </label>
							<div class="g24-col-sm-4">
								<div class="input-with-icon">
									<div class="form-group">
										<select id="report_year_year" name="year" class="form-control">
											<?php for($i=((date('Y')+543)-5); $i<=((date('Y')+543)); $i++){ ?>
												<option value="<?php echo $i; ?>" <?php echo $i==(date('Y')+543)?'selected':''; ?>><?php echo $i; ?></option>
											<?php } ?>
										</select>
									</div>
								</div>
							</div>
						</div>
						<div class="form-group g24-col-sm-24">
							<label class="g24-col-sm-6 control-label right"></label>
							<div class="g24-col-sm-2"> 
								<button class="btn btn-primary btn-after-input" type="button" onclick="check_empty('1',3)"><span> แสดงผล PDF</span></button>
							</div>
							<div class="g24-col-sm-2"> 
								<button class="btn btn-default btn-after-input" type="button" onclick="check_empty('2',3)"><span> แสดงผล Excel</span></button>
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
		'src' => PROJECTJSPATH.'assets/js/select2.full.js',
		'type' => 'text/javascript'
	);
	echo script_tag($link);
?>
<script>
function check_empty(type,form_type){
	data = null;
	if(form_type == 1) {
		data = $("#form_dialy").serialize();
	} else if (form_type == 2) {
		data = $("#form_monthly").serialize();
	} else if (form_type == 3) {
		data = $("#form_yearly").serialize();
	}

	$.ajax({
		 url:base_url+"account/ajax_check_account_chart_report", 
		 method:"post",
		 data:data,
		 dataType:"text",
		 success:function(data){
			if(data == 'success'){
				if(form_type == 1) {
					if(type == 1) {
						$('#form_dialy').attr('action', base_url+"account/account_chart_report_pdf");
					} else if(type == 2) {
						$('#form_dialy').attr('action', base_url+"account/account_chart_report_excel");
					}
					$('#form_dialy').submit();
				} else if (form_type == 2) {
					if(type == 1) {
						$('#form_monthly').attr('action', base_url+"account/account_chart_report_pdf");
					} else if(type == 2) {
						$('#form_monthly').attr('action', base_url+"account/account_chart_report_excel");
					}
					$('#form_monthly').submit();
				} else if (form_type == 3) {
					if(type == 1) {
						$('#form_yearly').attr('action', base_url+"account/account_chart_report_pdf");
					} else if(type == 2) {
						$('#form_yearly').attr('action', base_url+"account/account_chart_report_excel");
					}
					$('#form_yearly').submit();
				}
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
	$('.js-data-example-ajax').select2({
		dropdownParent: $("#search-div"),
		matcher: matchStart
	});
});

function matchStart(params, data) {
	// If there are no search terms, return all of the data
	if ($.trim(params.term) === '') {
	  return data;
	}

	// Display only term macth with text begin chars
	if(data.text.indexOf(params.term) == 0) {
		return data;
	}

	// Return `null` if the term should not be displayed
	return null;
}
</script>
