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
		<h1 style="margin-bottom: 0">รายงานค่าเสื่อม</h1>
		<?php $this->load->view('breadcrumb'); ?>
		<div class="row gutter-xs">
			<div class="col-xs-12 col-md-12">
				<div class="panel panel-body" style="padding-top:0px !important;">
					<form action="<?php echo base_url(PROJECTPATH.'/report_facility/depreciation_preview'); ?>" id="form1" method="GET" target="_blank">
						<h3></h3>
						
						<div class="form-group g24-col-sm-24">
							<label class="g24-col-sm-6 control-label right">หมวด</label>
							<div class="g24-col-sm-11">
								<select name="facility_type_id" id="facility_type_id" class="form-control">
									<option value="">- ทั้งหมด -</option>
									<?php foreach ($facility_type as $key => $_row) { ?>
										<option value="<?php echo $_row["facility_type_id"]?>"><?php echo $_row["facility_type_name"]?></option>
									<?php } ?>
								</select>
							</div>
						</div>
						
						<div class="form-group g24-col-sm-24">
							<label class="g24-col-sm-6 control-label right"></label>
							<div class="g24-col-sm-7">
								<input type="button" class="btn btn-primary" style="width:100%" value="แสดงข้อมูล" onclick="check_empty()">
							</div>
							<div class="g24-col-sm-4">
								<a id="btn_link_export" href="<?php echo PROJECTPATH; ?>/report_facility/depreciation_excel" target="_blank" class="btn btn-default" style="width:100%">Export Excel</a>
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
			$("#btn_link_export").prop("href", base_url + "/report_facility/depreciation_excel?facility_type_id=" + $("#facility_type_id").val());
		}
		$("#facility_type_id").change(function() {
			link_export();
		});
	});

	function check_empty(){
		var facility_type_id = $('#facility_type_id').val();
		
		$.ajax({
			url: base_url+'/report_facility/depreciation_check',
			method:"post",
			data:{ 
				facility_type_id: facility_type_id
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


