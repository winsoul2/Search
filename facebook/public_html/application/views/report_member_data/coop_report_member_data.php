
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
		<h1 style="margin-bottom: 0">รายงานข้อมูลสมาชิก</h1>
		<?php $this->load->view('breadcrumb'); ?>
		<div class="row gutter-xs">
			<div class="col-xs-12 col-md-12">
				<div class="panel panel-body" style="padding-top:0px !important;">
                    <div class="form-group g24-col-sm-24" style="text-align: center">
				<form action="<?php echo base_url(PROJECTPATH.'/report_member_data/coop_report_member_address_excel'); ?>" id="form1" method="GET" target="_blank">
					<h3></h3>
					<!-- 
					<div class="form-group g24-col-sm-24">
						<label class="g24-col-sm-6 control-label right show_department"> สังกัดหน่วยงาน </label>
						<div class="g24-col-sm-4 show_department">
							<select name="department" id="department" onchange="change_mem_group('department', 'faction')" class="form-control">
								<option value="">เลือกทั้งหมด</option>
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
								<option value="">เลือกทั้งหมด</option>
							</select>
						</div>
					</div>
					<div class="form-group g24-col-sm-24 show_level">
						<label class="g24-col-sm-6 control-label right"> หน่วยงานย่อย </label>
						<div class="g24-col-sm-4">
							<select name="level" id="level" class="form-control">
								<option value="">เลือกทั้งหมด</option>
							</select>
						</div>
					</div> -->

							<input type="button" class="btn btn-default" style="width:30%" value="รายงานที่อยู่ของสมาชิก" onclick="check_empty()">
				    </form>
                        <div style="margin-top: 15px">
                            <form action="<?php echo base_url(PROJECTPATH.'/report_member_data/coop_report_member_age_preview'); ?>" id="form2" method="GET" target="_blank">
                                <input type="button" class="btn btn-default" style="width:30%" value="รายงานอายุของสมาชิก" onclick="member_age_on_click()">
                            </form>
                        </div>
                    </div>
				</div>
			</div>
		</div>
	</div>
</div>
<script>	
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

	function check_empty(type){
		$('#form1').submit();
	}

	function member_age_on_click(){
        $('#form2').submit();
    }

</script>


