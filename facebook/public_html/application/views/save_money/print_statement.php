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
		<h1 style="margin-bottom: 0">รายงานการทำรายการประจำวัน (statement)</h1>
		<?php $this->load->view('breadcrumb'); ?>
		<div class="row gutter-xs">
			<div class="col-xs-12 col-md-12">
				<div class="panel panel-body" style="padding-top:0px !important;">
					<form action="<?php echo base_url(PROJECTPATH.'/save_money/statement_preview'); ?>" id="form1" method="POST" target="_blank">
					<h3></h3>

                    <div class="form-group g24-col-sm-24">
						<label class="g24-col-lg-6 control-label right"> เลขที่บัญชี </label>
						<div class="g24-col-lg-11">
							<input type="text"  class="form-control" value="<?=@$this->center_function->format_account_number($account_id);?>" readonly >
							<input type="hidden" name="account_id" class="form-control" value="<?=@$account_id?>" readonly >
						</div>
					</div>

					<div class="form-group g24-col-sm-24">
						<label class="g24-col-lg-6 control-label right"> ชื่อบัญชี </label>
						<div class="g24-col-lg-11">
							<input type="text" name="account_name" class="form-control" value="<?=@$account_name?>" readonly >
						</div> 
					</div>

					  
					<div class="form-group g24-col-sm-24">

						<label class="g24-col-lg-6 control-label right"> เลือกประเภท </label>
						<div class="g24-col-lg-4">
							<label class="radio-inline">
								<input type="radio" name="select_type" value='all' id="select_all" checked>ทั้งหมดถึงวันที่เลือก
							</label>
							
						</div> 
						<div class="g24-col-lg-4">
							<label class="radio-inline">
								<input type="radio" name="select_type" value='peroid' id="select_peroid">ช่วงเวลาที่กำหนด
							</label>
						</div> 

					</div>

					<div class="form-group g24-col-sm-24" id="zone_1">
						<label class="g24-col-lg-6 control-label right"> ประจำวันที่ </label>
						<div class="g24-col-lg-4">
							<div class="input-with-icon">
								<div class="form-group">
									<input id="start_date" name="start_date" class="form-control m-b-1 mydate" style="padding-left: 50px;" type="text" value="<?php echo $this->center_function->mydate2date(date('Y-m-d')); ?>" data-date-language="th-th">
									<span class="icon icon-calendar input-icon m-f-1"></span>
								</div>
							</div>
						</div> 
						<label class="g24-col-lg-3 control-label right end-date-label"> ถึงวันที่ </label>
						<div class="g24-col-lg-4 end-date-label">
							<div class="input-with-icon">
								<div class="form-group">
									<input id="end_date" name="end_date" class="form-control m-b-1 mydate" style="padding-left: 50px;" type="text" value="<?php echo $this->center_function->mydate2date(date('Y-m-d')); ?>" data-date-language="th-th">
									<span class="icon icon-calendar input-icon m-f-1"></span>
								</div>
							</div>
						</div>
					</div>
					<div class="form-group g24-col-sm-24" id="zone_2">
						<label class="g24-col-lg-6 control-label right end-date-label"> ถึงวันที่ </label>
						<div class="g24-col-lg-4 end-date-label">
							<div class="input-with-icon">
								<div class="form-group">
									<input id="end_date" name="end_date" class="form-control m-b-1 mydate" style="padding-left: 50px;" type="text" value="<?php echo $this->center_function->mydate2date(date('Y-m-d')); ?>" data-date-language="th-th">
									<span class="icon icon-calendar input-icon m-f-1"></span>
								</div>
							</div>
						</div>
					</div>

                    <h3></h3>
					<div class="form-group g24-col-sm-24">
						<label class="g24-col-lg-6 control-label right"></label>
						<div class="g24-col-lg-11">
                            <input type="submit" class="btn btn-primary" style="width:100%" value="แสดงข้อมูล">
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
            orientation: "bottom"
		});
		$("#type_date").change(function() {
			if($(this).val() == 1) {
				$(".end-date-label").hide();
			} else {
				$(".end-date-label").show();
			}
		});
	});

	function check_empty(){
		var start_date = $('#start_date').val();
		var end_date = $('#end_date').val();
		var type_id = $('#type_id').val();
		
		$.ajax({
			url: base_url+'/report_deposit_data/check_coop_report_account_transaction',	
			method:"post",
			data:{ 
				start_date: start_date, 
				end_date: end_date,
				type_id:type_id
			},
			dataType:"text",
			success:function(data){
			console.log(data);
			if(data == 'success'){
				$('#form1').submit();
			}else{
				$('#alertNotFindModal').appendTo("body").modal('show');
			}
			}
		});
	}	

	$('#select_peroid').click(function() {
		$("#zone_1").show();
		$("#zone_2").hide();
	});

	$('#select_all').click(function() {
		$("#zone_1").hide();
		$("#zone_2").show();
	});

	$("#zone_1").hide();
	$("#zone_2").show();
</script>


