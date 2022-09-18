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
		<h1 style="margin-bottom: 0">รายการเรียกเก็บประจำปี</h1>
		<?php $this->load->view('breadcrumb'); ?>
		<div class="row gutter-xs">
			<div class="col-xs-12 col-md-12">
				<div class="panel panel-body" style="padding-top:0px !important;">
					<h3></h3>
					<input type="hidden" name="deposit_setting_id" value="<?php echo @$row['deposit_setting_id']; ?>">
					<div class="form-group g24-col-sm-24">
						<label class="g24-col-sm-8 control-label right"> ปี </label>
						<div class="g24-col-sm-4">
							<select id="year_choose" class="form-control" onChange="change_month_year()">
								<?php for($i=((date('Y')+543)-5); $i<=((date('Y')+543)+5); $i++){ ?>
									<option value="<?php echo $i; ?>" <?php echo $i==(date('Y')+544)?'selected':''; ?>><?php echo $i; ?></option>
								<?php } ?>
							</select>
						</div>
					</div>
					<div class="form-group g24-col-sm-24" style="margin-top:20px;">
						<label class="g24-col-sm-5 control-label right"></label>
						<div class="g24-col-sm-10">
							<a id="link_1" href="<?php echo base_url(PROJECTPATH.'/report_loan_data/coop_finance_year_preview?year='.(date('Y')+544)); ?>" target="_blank"><button type="button" class="btn btn-primary" style="width:100%">รายละเอียดยอดลูกหนี้คงเหลือ</button></a>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<script>
	var base_url = $('#base_url').attr('class');

	function change_month_year(){
		var year = $('#year_choose').val();
		$('#link_1').attr('href', base_url+'report_loan_data/coop_finance_year_preview?year='+year);
	}
</script>
