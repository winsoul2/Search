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
		<h1 style="margin-bottom: 0">รายงานดอกเบี้ยเงินฝากล่วงหน้าสมาชิก</h1>
		<?php $this->load->view('breadcrumb'); ?>
		<div class="row gutter-xs">
			<div class="col-xs-12 col-md-12">
				<div class="panel panel-body" style="padding-top:0px !important;">
					<form action="<?php echo base_url(PROJECTPATH.'/report_deposit_data/coop_report_member_interest_forecast_preview'); ?>" id="form1" method="GET" target="_blank">
					<h3></h3>
					<div class="form-group g24-col-sm-24">
						<label class="g24-col-sm-6 control-label right">รหัสสมาชิก</label>
						<div class="g24-col-sm-4">
							<div class="input-group">
								<input id="mem_id" name="mem_id" class="form-control member_id" type="text" value="<?php echo @$row_member['member_id']; ?>" onkeypress="check_member_id();">
								<span class="input-group-btn">
									<a data-toggle="modal" data-target="#myModal" id="test" class="fancybox_share fancybox.iframe" href="#">
										<button id="" type="button" class="btn btn-info btn-search"><span class="icon icon-search"></span></button>
									</a>
								</span>	
							</div>
						</div>
					</div>
					<div class="form-group g24-col-sm-24">
						<label class="g24-col-sm-6 control-label right">ชื่อ-สกุล</label>
						<div class="g24-col-sm-11">
							<input id="form-control-2" class="form-control " type="text" value="<?php echo@$row_member['prename_short'].@$row_member['firstname_th'].' '.@$row_member['lastname_th'] ?>" readonly>
						</div>
					</div>
					<div class="form-group g24-col-sm-24">
						<label class="g24-col-sm-6 control-label right"></label>
						<div class="g24-col-sm-7">
							<input type="button" class="btn btn-primary" style="width:100%" value="แสดงข้อมูล" onclick="check_empty()">
						</div>
						<div class="g24-col-sm-4">
							<a id="btn_link_export" href="<?php echo PROJECTPATH; ?>/report_deposit_data/coop_report_member_interest_forecast_excel?mem_id=<?php echo @$row_member['member_id']; ?>" target="_blank" class="btn btn-default" style="width:100%">Export Excel</a>
						</div>
					</div>
				</form>				
				</div>
			</div>
		</div>
	</div>
</div>

<?php $this->load->view('search_member_new_modal'); ?>

<script>
	var base_url = $('#base_url').attr('class');
	
	function check_member_id() {
		var member_id = $('.member_id').first().val();
		var keycode = (event.keyCode ? event.keyCode : event.which);
		if(keycode == '13'){
		  $.post(base_url+"save_money/check_member_id", 
		  {	
			member_id: member_id
		  }
		  , function(result){
			 obj = JSON.parse(result);
			 console.log(obj.member_id);
			 mem_id = obj.member_id;
			 if(mem_id != undefined){
			   document.location.href = '<?php echo base_url(uri_string())?>?member_id='+mem_id
			 }else{					
			   swal('ไม่พบรหัสสมาชิกที่ท่านเลือก','','warning');
			 }
		   });
		 }
	}
	
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
	});

	function check_empty(){
		var start_date = $('#start_date').val();
		var end_date = $('#end_date').val();
		var type_id = $('#type_id').val();
		var ruid = $('input[name=ruid]:checked', '#form1').val();
		
		if($(".member_id").val() != ""){
			$('#form1').submit();
		}else{
			swal('กรุณาป้อนรหัสสมาชิก','','warning');
		}
	}
</script>