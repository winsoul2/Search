<style>
	.alert { padding: 5px; }
</style>

<div class="layout-content">
	<div class="layout-content-body">
		<style>
			
		</style>
		<h1 style="margin-bottom: 0">คำนวณดอกเบี้ยเงินฝาก</h1>
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 padding-l-r-0">
			<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4 padding-l-r-0">
				<?php $this->load->view('breadcrumb'); ?>
			</div>
			<div class="col-xs-8 col-sm-8 col-md-8 col-lg-8 padding-l-r-0">
				
			</div>
		</div>
		<div class="row gutter-xs">
			<div class="col-xs-12 col-md-12">
				<div class="panel panel-body">
					<!--<button type="button" class="btn_delete_interest btn btn-danger pull-right">ลบดอกเบี้ย</button>-->
					<h3 class="m-t-0">คำนวณดอกเบี้ย</h3>
					
					<form id="frm_cal" data-toggle="validator" novalidate="novalidate" action="<?php echo base_url(PROJECTPATH . '/save_money/cal_interest_process'); ?>" method="post">
						<div class="row gutter-xs">
							<label class="g24-col-sm-9 control-label" for="type_prefix">เลือกบัญชีเงินฝาก</label>
							<div class="g24-col-sm-7">
								<select class="form-control m-b-1" id="type_id"  name="type_id" >
									<option value="">- ทั้งหมด -</option>
									<?php foreach($type_id as $key => $value){ ?>
										<option value="<?php echo $value['type_id']; ?>" <?php echo $value['type_id']==@$row['type_id']?'selected':''; ?>><?php echo $value['type_code']." ".$value['type_name']; ?></option>
									<?php } ?>
								</select>
							</div>
						</div>
						<div class="row gutter-xs">
							<label class="g24-col-sm-9 control-label" for="type_prefix">วันที่เริ่มคำนวณ</label>
							<div class="g24-col-sm-3">
								<div class="input-with-icon">
									<div class="form-group">
										<input id="start_date" name="start_date" class="form-control m-b-1 mydate" type="text" value="<?php echo $data["start_date"]; ?>" data-date-language="th-th">
										<span class="icon icon-calendar input-icon"></span>
									</div>
								</div>
							</div>
							<label class="g24-col-sm-1 control-label" for="type_prefix">ถึงวันที่</label>
							<div class="g24-col-sm-3">
								<div class="input-with-icon">
									<div class="form-group">
										<input id="end_date" name="end_date" class="form-control m-b-1 mydate" type="text" value="<?php echo $data["end_date"]; ?>" data-date-language="th-th">
										<span class="icon icon-calendar input-icon"></span>
									</div>
								</div>
							</div>
						</div>
						<div class="row gutter-xs">
							<label class="g24-col-sm-9 control-label" for="type_prefix">เวลา</label>
							<div class="g24-col-sm-3">
								<div class="input-with-icon">
									<div class="form-group">
										<input id="time" name="time" class="form-control m-b-1" type="text" value="<?php echo $data["time"]; ?>">
										<span class="icon icon-clock-o input-icon"></span>
									</div>
								</div>
							</div>
						</div>
						
						<div class="row">
							<div class="col-sm-12 m-t-1" style="text-align:center;">
								<button type="button" class="btn_cal btn btn-primary">คำนวณ</button>
							</div>
						</div>
						
						<div class="row gutter-xs">
							<div class="g24-col-sm-8 g24-col-sm-offset-8">
								<div id="msg_status" class="text-center m-t-2"></div>
							</div>
						</div>
					</form>
				</div>
				
				
			</div>
		</div>
	</div>
</div>

<script>
	$(function () {
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
		
		$(".btn_cal").click(function() {
			$('.btn_cal').addClass('spinner spinner-inverse spinner-sm').prop('disabled', true);
			$("#msg_status").html('<span class="alert alert-success">กำลังคำนวณดอกเบี้ยเงินฝาก...</span>');
			
			var params = new FormData($("#frm_cal")[0]);
			params.append("_t", Math.random());
			
			$.ajax({
				type: "POST"
				, url: $("#frm_cal").prop("action")
				, data: params
				, contentType: false
				, cache: false
				, processData: false
				, success: function( msg ) {
					data = jQuery.parseJSON(msg);
					
					if(data["result"] == "true") {
						$("#msg_status").html('<span class="alert alert-success">คำนวณดอกเบี้ยเงินฝากเรียบร้อยแล้ว</span>');
					}
					
					$('.btn_cal').removeClass('spinner spinner-inverse spinner-sm').prop('disabled', false);
				}
			});
			
		});
		
		$(".btn_delete_interest").click(function() {
			swal({
				title: "ลบดอกเบี้ย",
				text: "ยืนยันการลบดอกเบี้ย",
				type: "warning",
				showCancelButton: true,
				confirmButtonColor: '#DD6B55',
				confirmButtonText: 'ลบดอกเบี้ย',
				cancelButtonText: "ยกเลิก",
				closeOnConfirm: false,
				closeOnCancel: true
			},
			function(isConfirm) {
				if (isConfirm) {
					swal.close();
					
				} else {
					
				}
			});
		});
	});
</script>
