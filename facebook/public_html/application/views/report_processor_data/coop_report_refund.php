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
			.show_level{
				display:none;
			}
		</style>

		<style type="text/css">
		  .form-group{
			margin-bottom: 5px;
		  }
		</style>
		<h1 style="margin-bottom: 0">รายงานการคืนเงิน</h1>
		<?php $this->load->view('breadcrumb'); ?>
		<div class="row gutter-xs">
			<div class="col-xs-12 col-md-12">
				<div class="panel panel-body" style="padding-top:0px !important;">
				<form action="<?php echo base_url(PROJECTPATH.'/report_processor_data/coop_report_refund_preview'); ?>" id="form1" method="POST" target="_blank">
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
						<label class="g24-col-sm-4 control-label right"> ปี </label>
						<div class="g24-col-sm-4">
							<select id="year" name="year" class="form-control">
								<?php for($i=((date('Y')+543)-5); $i<=((date('Y')+543)+5); $i++){ ?>
									<option value="<?php echo $i; ?>" <?php echo $i==(date('Y')+543)?'selected':''; ?>><?php echo $i; ?></option>
								<?php } ?>
							</select>
						</div>
					</div>
					<div class="form-group g24-col-sm-24">
						<label class="g24-col-sm-6 control-label right"> รูปแบบหน่วยงาน </label>
						<div class="g24-col-sm-4">
							<select name="type_report" id="type_report" class="form-control" onchange="change_report_type()">
							    <option value="0">แยกตามหน่วยงาน</option>
								<option value="1">รายละเอียดรายบุคคล</option>
							</select>
						</div>
					</div>
					<div class="form-group g24-col-sm-24">
						<label class="g24-col-sm-6 control-label right show_department"> สังกัดหน่วยงาน </label>
						<div class="g24-col-sm-4 show_department">
							<select name="department" id="department" onchange="change_mem_group('department', 'faction')" class="form-control">
								<option value="">เลือกข้อมูล</option>
								<?php 
									foreach($row_mem_group as $key => $value){
									?>
									<option value="<?php echo $value['id']; ?>"><?php echo $value['mem_group_name']; ?></option>
								<?php 
								} ?>
							</select>
						</div>
						<label class="g24-col-sm-1 control-label right show_level"> อำเภอ </label>
						<div class="g24-col-sm-4 show_level">
							<select name="faction" id="faction" onchange="change_mem_group('faction','level')" class="form-control">
								<option value="">เลือกข้อมูล</option>
							</select>
						</div>
					</div>
					<div class="form-group g24-col-sm-24 show_level">
						<label class="g24-col-sm-6 control-label right"> หน่วยงานย่อย </label>
						<div class="g24-col-sm-4">
							<select name="level" id="level" class="form-control">
								<option value="">เลือกข้อมูล</option>
							</select>
						</div>
					</div>
					<div class="form-group g24-col-sm-24">
						<!-- <label class="g24-col-sm-6 control-label right"> รายการคืน </label>
						<div class="g24-col-sm-4">
							<select name="return_status" id="return_status" class="form-control">
								<option value="">เลือกรายการคืน</option>
								<option value="atm">ฉุกเฉิน ATM</option>
								<?php
									foreach($loan_names as $loan_name) {
								?>
								<option value="<?php echo $loan_name['loan_name_id']?>"><?php echo $loan_name['loan_name']?></option>
								<?php
									}
								?>
							</select>
						</div> -->
						<label class="g24-col-sm-6 control-label right"> สถานะการคืน </label>
						<div class="g24-col-sm-4">
							<select name="return_status" id="return_status" class="form-control">
								<option value="">เลือกสถานะ</option>
							    <option value="0">รออนุมัติ</option>
								<option value="1">อนุมัติ</option>
								<option value="2">คืนเงินแล้ว</option>
							</select>
						</div>
					</div>
					<div class="form-group g24-col-sm-24">
						<label class="g24-col-sm-6 control-label right"> ประเภทการคืนเงิน </label>
						<div class="g24-col-sm-4">
							<select name="transfer_type" id="transfer_type" class="form-control">
								<option value="">เลือกประเภท</option>
							    <option value="0">เงินสด</option>
								<option value="1">โอนบัญชีสหกรณ์</option>
								<option value="2">โอนบัญชีธนาคาร</option>
							</select>
						</div>
					</div>
					<div class="form-group g24-col-sm-24">
						<label class="g24-col-sm-5 control-label right"></label>
						<div class="g24-col-sm-10">
							<input type="button" class="btn btn-primary" style="width:100%" value="รายการคืนเงิน" onclick="check_empty()">
						</div>
					</div>
				</form>				
				</div>
			</div>
		</div>
	</div>
</div>
<script>	
	$( document ).ready(function() {
		
	});	
	function change_report_type(){
		var type_report = $('#type_report').val();
		if(type_report == '0'){
			$('.show_level').hide();
		}else{
			$('.show_level').show();
		}
	}
	function change_mem_group(id, id_to){
		var mem_group_id = $('#'+id).val();
		$('#level').html('<option value="">เลือกข้อมูล</option>');
		$.ajax({
			method: 'POST',
			url: base_url+'manage_member_share/get_mem_group_list',
			data: {
				mem_group_id : mem_group_id
			},
			success: function(msg){
				$('#'+id_to).html(msg);
			}
		});
	}

	function check_empty(){
		if ($("#type_report").val() == "1") {
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
			});
			datas = $('#form1').serializeArray();
			$.ajax({
				url: base_url+'/report_processor_data/check_coop_report_refund',	
				method:"post",
				data:datas,
				dataType:"text",
				success:function(data){
					$.unblockUI();
					if(data == 'success'){
						$('#form1').submit();
					}else{
						$('#alertNotFindModal').appendTo("body").modal('show');
					}
				}
			});
		} else {
			$('#form1').submit();
		}
	}
</script>


