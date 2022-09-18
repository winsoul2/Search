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
<h1 style="margin-bottom: 0">รายงานบัญชีแยกประเภทย่อย</h1>
<?php $this->load->view('breadcrumb'); ?>
<div class="row gutter-xs">
	<div class="col-xs-12 col-md-12">
		<div class="panel panel-body" style="padding-top:0px !important;">
		<form action="<?php echo base_url(PROJECTPATH.'/account/account_subsidiary_ledge_excel'); ?>" id="form1" method="GET" target="_blank">
			<div class="form-group g24-col-sm-24">
				<div class="g24-col-sm-5 right">
					<h3>รายวัน</h3>
				</div>
			</div>
			<div class="form-group g24-col-sm-24">
                <div class="form-group g24-col-sm-24" style="margin: 2px">
                    <label class="g24-col-sm-6 control-label right"> เดือน </label>
                    <div class="g24-col-sm-6">
                        <select id="account_chart_main" name="account_chart_main" class="form-control">
                            <?php foreach($account_chart_main as $key => $value){ ?>
                                <option value="<?php echo $key; ?>" ><?php echo $key .'   '. $value; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="form-group g24-col-sm-24" style="margin: 2px">
                    <label class="g24-col-sm-6 control-label right"> วันที่ </label>
                    <div class="g24-col-sm-6">
                        <div class="input-with-icon">
                            <div class="form-group">
                                <input id="report_date_start" name="report_date_start" class="form-control m-b-1 mydate" style="padding-left: 50px;" type="text" value="<?php echo $this->center_function->mydate2date(date('Y-m-d')); ?>" data-date-language="th-th">
                                <span class="icon icon-calendar input-icon m-f-1"></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group g24-col-sm-24" style="margin: 2px">
                    <label class="g24-col-sm-6 control-label right">ถึง วันที่ </label>
                    <div class="g24-col-sm-6">
                        <div class="input-with-icon">
                            <div class="form-group">
                                <input id="report_date_end" name="report_date_end" class="form-control m-b-1 mydate" style="padding-left: 50px;" type="text" value="<?php echo $this->center_function->mydate2date(date('Y-m-d')); ?>" data-date-language="th-th">
                                <span class="icon icon-calendar input-icon m-f-1"></span>
                            </div>
                        </div>
                    </div>
                    <div class="g24-col-sm-2">
                        <button class="btn btn-primary btn-after-input" type="button"  onclick="check_empty('1')"><span> แสดงผล</span></button>
                    </div>
                </div>
			</div>
		</form>
		</div>
	</div>
</div>
	</div>
</div>
<script>
function check_empty(type){
	var account_chart_main = '';
    var report_date_start = '';
    var report_date_end = '';
    var month = '';
	var year = '';
	if(type == '1'){
        account_chart_main = $('#account_chart_main').val();
        report_date_start = $('#report_date_start').val();
        report_date_end = $('#report_date_end').val();
	}

	$.ajax({
		 url:base_url+"account/ajax_account_subsidiary_ledge",
		 method:"post",
		 data:{
             report_date_start: report_date_start,
             account_chart_main: account_chart_main,
             report_date_end: report_date_end
		 },
		 dataType:"text",
		 success:function(data){
			if(data == 'success'){
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