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
		<h1 style="margin-bottom: 0">รายงานการรับเบิก</h1>
		<?php $this->load->view('breadcrumb'); ?>
		<div class="row gutter-xs">
			<div class="col-xs-12 col-md-12">
				<div class="panel panel-body" style="padding-top:0px !important;">
					<form action="<?php echo base_url(PROJECTPATH.'/report_facility/pickup_preview'); ?>" id="form1" method="GET" target="_blank">
						<h3></h3>
						
						<div class="form-group g24-col-sm-24">
							<label class="g24-col-sm-6 control-label right"> เดือน </label>
							<div class="g24-col-sm-4">
								<select id="month" name="month" class="form-control">
									<?php foreach($month_arr as $key => $value){ ?>
										<option value="<?php echo $key; ?>" <?php echo $key==((int)date('m'))?'selected':''; ?>><?php echo $value; ?></option>
									<?php } ?>
								</select>
							</div>
							<label class="g24-col-sm-1 control-label right"> ปี </label>
							<div class="g24-col-sm-4">
								<select id="year" name="year" class="form-control">
									<?php for($i=((date('Y')+543)-5); $i<=((date('Y')+543)+5); $i++){ ?>
										<option value="<?php echo $i; ?>" <?php echo $i==(date('Y')+543)?'selected':''; ?>><?php echo $i; ?></option>
									<?php } ?>
								</select>
							</div>
						</div>
						
						<div class="form-group g24-col-sm-24">
							<label class="g24-col-sm-6 control-label right">รายการ</label>
							<div class="g24-col-sm-11">
								<label style="font-weight: normal;"><input type="radio" id="pickup_type_0" name="pickup_type" value="0" class="pickup_type" checked="checked"> รับพัสดุ</label> &nbsp;
								<label style="font-weight: normal;"><input type="radio" id="pickup_type_1" name="pickup_type" value="1" class="pickup_type"> เบิกพัสดุ</label>
							</div>
						</div>
						
						<div class="form-group g24-col-sm-24">
							<label class="g24-col-sm-6 control-label right"></label>
							<div class="g24-col-sm-7">
								<input type="button" class="btn btn-primary" style="width:100%" value="แสดงข้อมูล" onclick="check_empty()">
							</div>
							<div class="g24-col-sm-4">
								<a id="btn_link_export" href="<?php echo PROJECTPATH; ?>/report_facility/pickup_excel?month=<?php echo (int)date('m'); ?>&year=<?php echo date('Y') + 543; ?>&pickup_type=0" target="_blank" class="btn btn-default" style="width:100%">Export Excel</a>
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
			$("#btn_link_export").prop("href", base_url + "/report_facility/pickup_excel?month=" + $("#month").val() + "&year=" + $("#year").val() + "&pickup_type=" + $("input[name=pickup_type]:checked").val());
		}
		$("#month").change(function() {
			link_export();
		});
		$("#year").change(function() {
			link_export();
		});
		$(".pickup_type").change(function() {
			link_export();
		});
	});

	function check_empty(){
		var month = $('#month').val();
		var year = $('#year').val();
		var pickup_type = $('input[name=pickup_type]:checked').val();
		
		$.ajax({
			url: base_url+'/report_facility/pickup_check',
			method:"post",
			data:{ 
				month: month, 
				year: year,
				pickup_type: pickup_type
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
</script>


