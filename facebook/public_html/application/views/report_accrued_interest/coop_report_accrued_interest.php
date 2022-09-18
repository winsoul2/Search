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
		<h1 style="margin-bottom: 0">รายงานดอกเบี้ยค้างจ่าย/ทะเบียนเงินฝาก</h1>
		<?php $this->load->view('breadcrumb'); ?>
		<div class="row gutter-xs">
			<div class="col-xs-12 col-md-12">
				<div class="panel panel-body" style="padding-top:0px !important;">
					<form action="<?php echo base_url(PROJECTPATH.'/report_accrued_interest/coop_report_accrued_interest_preview'); ?>" id="form1" method="GET" target="_blank" autocomplete="off">
						<h3></h3>
						<div class="form-group g24-col-sm-24">
						<label class="g24-col-sm-6 control-label right"> รูปแบบ </label>
							<div class="g24-col-sm-18">
								<div class="form-group">
									<label><input type="radio" name="report_type" value="1" class="chk_report_type" checked="checked"> สรุปรายคน(ตามประเภทบัญชี)</label> &nbsp;
									<label><input type="radio" name="report_type" value="2" class="chk_report_type"> รายละเอียดรายคน(ตามประเภทบัญชี)</label> &nbsp;
								</div>
							</div>
						</div>
						<div class="form-group g24-col-sm-24">
							<label label class="g24-col-sm-6 control-label right"> ปี </label>
							<div class="g24-col-sm-3">
								<select id="year" name="year" class="form-control">
								<?php for($i=((date('Y')+543)-5); $i<=((date('Y')+543)+5); $i++){ ?>
									<option value="<?php echo $i; ?>" <?php echo $i==(date('Y')+543)?'selected':''; ?>><?php echo $i; ?></option>
								<?php } ?>
							</select>
							</div>
						</div>
						<div class="form-group g24-col-sm-24">
							<label class="g24-col-sm-6 control-label right"> ประเภทบัญชี </label>
							<div class="g24-col-sm-7">
								<select class="form-control" name="type_id" id="type_id">
									<option value="">เลือกประเภทบัญชี</option>
									<?php foreach (@$type_ids as $key => $type){ ?>
										<option value="<?php echo $type['type_id']; ?>"><?php echo $type['type_name']; ?></option>
									<?php } ?>
								</select>
							</div>
						</div>
						<!--
						<div class="form-group g24-col-sm-24">
							<label class="g24-col-sm-6 control-label right"> รหัสสมาชิก </label>
							<div class="g24-col-sm-7">
								<input id="member_id" class="form-control " type="text" name="member_id" value="">
							</div>
						</div>
						-->
						
						<!--<div class="form-group g24-col-sm-24">
							<label class="g24-col-sm-6 control-label right"> เลขที่บัญชี </label>
							<div class="g24-col-sm-7">
								<input id="account_id" class="form-control " type="text" name="account_id" value="">
							</div>
						</div>
						-->
						<!--<div class="form-group g24-col-sm-24">
							<label class="g24-col-sm-6 control-label right"></label>
							<div class="g24-col-sm-7">
								<input type="button" class="btn btn-primary" style="width:100%" value="Export Excel" onclick="check_empty()">
							</div>
						</div>
						-->
						<!--<div class="form-group g24-col-sm-24">
							<label class="g24-col-sm-6 control-label right"></label>
							<div class="g24-col-sm-8">
								<a id="btn_link_export" href="<?php echo PROJECTPATH; ?>/report_deposit_data/coop_report_gov_bank_excel?report_type=1&type_id=<?php echo $type_id; ?>&start_date=<?php echo $this->center_function->mydate2date(date('Y-m-d')); ?>" target="_blank" class="btn btn-default" style="width:100%">Export Excel</a>
							</div>
						</div>-->
						<div class="form-group g24-col-sm-24">
							<label class="g24-col-sm-6 control-label right"></label>
							<div class="g24-col-sm-7">
								<input type="button" class="btn btn-primary" style="width:100%" value="รายงานดอกเบี้ยค้างจ่าย" onclick="check_empty()">
							</div>
						</div>
						

					</form>
				</div>
			</div>
		</div>
	</div>
</div>
<script>
	$(document).ready(function() {
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
	})
	function check_empty() {
		var member_id = $('#member_id').val();
		var type_id = $('#type_id').val();
		//if(member_id == ''){
		//	swal("กรุณากรอกรหัสสมาชิก");
		//	return false;
		//}
		
		if(type_id == ''){
			swal("กรุณาเลือกประเภทบัญชี");
			return false;
		}
		
		// check_report_deposit_month_transaction
		$.blockUI({
			message: 'กรุณารอสักครู่...',
			css: {
				border: 'none',
				padding: '15px',
				backgroundColor: '#000',
				'-webkit-border-radius': '10px',
				'-moz-border-radius': '10px',
				opacity: .5,
				color: '#fff'
			},
			baseZ: 2000,
			bindEvents: false
		})

		$.ajax({
			url: base_url+'/report_accrued_interest/check_report_deposit_month_transaction',
			method:"post",
			data: $("#form1").serializeArray(),
			dataType:"text",
			success:function(data){
				$.unblockUI();
				if(data == 'success'){
					$('#form1').submit()
				}else{
					$('#alertNotFindModal').appendTo("body").modal('show')
				}
			}
		})
	}
</script>
