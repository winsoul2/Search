<div class="layout-content">
    <div class="layout-content-body">
	<?php if (@$act != "add") { ?>
		<h1 style="margin-bottom: 0">การเพิ่มลดหุ้น</h1>
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 padding-l-r-0">
			<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 padding-l-r-0">
				<?php $this->load->view('breadcrumb'); ?>
			</div>
		</div>
	<?php } ?>

		<div class="row gutter-xs">
			<div class="col-xs-12 col-md-12">
				<div class="panel panel-body" style="padding-top:0px !important;">
						<form id='form1' data-toggle="validator" novalidate="novalidate" action="<?php echo base_url(PROJECTPATH.'/setting_share_data/coop_increase_share_save'); ?>" method="post">
							<input name="id"  type="hidden" value="<?php echo @$row['id']?>">
							<h3 ></h3>
							<div class="g24-col-sm-24 m-b-1">
								<div class="form-group">
									<label class="g24-col-sm-8 control-label right"> เพิ่มได้ไม่เกิน </label>
									<div class="g24-col-sm-4">
										<input class="form-control m-b-1" type="number" name="share_increase" id="share_increase" value="<?php echo @$row['share_increase']?>">
									</div>
									<label class="g24-col-sm-8 control-label text-left"> ครั้งต่อปี</label>
								</div>
							</div>
							<div class="g24-col-sm-24 m-b-1">
								<div class="form-group">
									<label class="g24-col-sm-8 control-label right"> ลดได้ไม่เกิน </label>
									<div class="g24-col-sm-4">
										<input class="form-control m-b-1" type="number" name="share_decrease" id="share_decrease" value="<?php echo @$row['share_decrease']?>">
									</div>
									<label class="g24-col-sm-8 control-label text-left"> ครั้งต่อปี  แต่ไม่ต่ำกว่าเกณฑ์ที่กำหนด</label>
								</div>
							</div>
							<div class="g24-col-sm-24">
								<div class="form-group">
									<label class="g24-col-sm-8 control-label "></label>
									<div class="g24-col-sm-4" style="text-align:center;">
										<button class="btn btn-primary" type="button" onclick="submit_form()"><span class="icon icon-save"></span> บันทึก</button>
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
 function submit_form(){
	var text_alert = '';
	if($.trim($('#share_increase').val())== ''){
		text_alert += ' - จำนวนครั้งของการเพิ่มหุ้น\n';
	}
	if($.trim($('#share_decrease').val())== ''){
		text_alert += ' - จำนวนครั้งของการลดหุ้น\n';
	}
	
	if(text_alert != ''){
		swal('กรุณากรอกข้อมูลต่อไปนี้',text_alert,'warning');
	}else{
		$('#form1').submit();
	}
 }
</script>
