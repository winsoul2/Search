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
		<h1 style="margin-bottom: 0">รายงานรายชื่อสมาชิก</h1>
		<?php $this->load->view('breadcrumb'); ?>
		<div class="row gutter-xs">
			<div class="col-xs-12 col-md-12">
				<div class="panel panel-body" style="padding-top:0px !important;">
				<form action="<?php echo base_url(PROJECTPATH.'/report_member_data/coop_report_member_name_preview'); ?>" id="form1" method="GET" target="_blank">
					<h3></h3>
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
						<label class="g24-col-sm-1 control-label right show_level"> ฝ่าย </label>
						<div class="g24-col-sm-4 show_level">
							<select name="faction" id="faction" onchange="change_mem_group('faction','level')" class="form-control">
								<option value="">เลือกข้อมูล</option>
							</select>
						</div>
					</div>
					<div class="form-group g24-col-sm-24 show_level">
						<label class="g24-col-sm-6 control-label right"> สังกัด </label>
						<div class="g24-col-sm-4">
							<select name="level" id="level" class="form-control">
								<option value="">เลือกข้อมูล</option>
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
        $.ajax({
			url: base_url+'/report_member_data/check_data_report_member_name',	
			method:"post",
			data: $("#form1").serializeArray(),
			dataType:"text",
			success:function(data){
                $.unblockUI();	
                if(data == 'success'){
                    $("#form1").submit()
                }else{
                    $('#alertNotFindModal').appendTo("body").modal('show')
                }
			}
		})
	}
</script>


