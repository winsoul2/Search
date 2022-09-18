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
		<h1 style="margin-bottom: 0">สรุปรายการเรียกเก็บ</h1>
		<?php $this->load->view('breadcrumb'); ?>
		<div class="row gutter-xs">
			<div class="col-xs-12 col-md-12">
				<div class="panel panel-body" style="padding-top:0px !important;">
				<form action="<?php echo base_url('/report_processor_data/coop_report_charged_department_preview'); ?>" id="form1" method="GET" target="_blank">
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
						<label class="g24-col-sm-6 control-label right"> รูปแบบหน่วยงาน </label>
						<div class="g24-col-sm-4">
							<select name="type_department" id="type_department" onchange="" class="form-control">
							    <option value="">เลือกรูปแบบหน่วยงาน</option>
								<option value="1">หน่วยงานหลัก</option>
								<option value="2">หน่วยงานย่อย</option>
								<option value="3">รายบุคคล</option>
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
						<label class="g24-col-sm-6 control-label right"> ประเภทรายงาน </label>
						<div class="g24-col-sm-4">
							<select name="term_of_loan" id="term_of_loan" onchange="" class="form-control">
							    <option value="">เลือกประเภทรายงาน</option>
								<?php
									foreach($term_of_loans as $term_of_loan) {
								?>
								<option value="<?php echo $term_of_loan['type_id'];?>"><?php echo $term_of_loan['type_name'];?></option>
								<?php
									}
								?>
							</select>
						</div>
					</div>
					
					<div class="form-group g24-col-sm-24">
						<label class="g24-col-sm-6 control-label right"> รูปแบบสมาชิก </label>
						<div class="g24-col-sm-12 mem_type_list">	
							<label class="custom-control custom-control-primary custom-checkbox g24-col-sm-8" style="padding-top: 9px;margin-left: 15px;">
								<input type="checkbox" class="custom-control-input type_item" id="mem_type_all" name="mem_type[]" value="all">
								<span class="custom-control-indicator" style="margin-top: 9px;"></span>
								<span class="custom-control-label">ทั้งหมด</span>
							</label>
							<?php
								if(!empty($mem_type)){
									foreach($mem_type AS $key=>$type_value){
							?>
										<label class="custom-control custom-control-primary custom-checkbox g24-col-sm-8" style="padding-top: 9px;">
											<input type="checkbox" class="custom-control-input type_item" id="" name="mem_type[]" value="<?php echo @$type_value['mem_type_id'];?>">
											<span class="custom-control-indicator" style="margin-top: 9px;"></span>
											<span class="custom-control-label"><?php echo @$type_value['mem_type_name'];?></span>
										</label>
							<?php
									}
								}
							?>
						</div>
					</div>
					<div class="form-group g24-col-sm-24">
						<label class="g24-col-sm-5 control-label right"></label>
						<div class="g24-col-sm-10">
							<input type="button" class="btn btn-primary" style="width:100%" value="รายการเรียกเก็บ" onclick="check_empty()">
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
		$(".show_department").hide();
		$(".show_level").hide();
		$("#type_department").change(function() {
			var type_department = $(this).val();
			if(type_department == '1'){
				$(".show_department").show();
				$(".show_level").hide();
			}else if(type_department == '2'){
				$(".show_department").show();
				$(".show_level").show();
			}
			
		  //alert($(this).val());
		});
		$("#mem_type_all").change(function() {
            if($("#mem_type_all").attr('checked') == "checked"){
                $('.type_item').prop('checked', true)
            } else {
                $('.type_item').prop('checked', false)
            }
        });
        $(".type_item").change(function() {
            if($(this).attr('checked') != "checked"){
                $('#mem_type_all').prop('checked', false)
            }
        });
	});	
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
		$('#form1').submit();
	}
</script>


