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
		<h1 style="margin-bottom: 0">รายงานตรวจสอบการทำงานระบบ ATM</h1>
		<?php $this->load->view('breadcrumb'); ?>
		<div class="row gutter-xs">
			<div class="col-xs-12 col-md-12">
				<div class="panel panel-body" style="padding-top:0px !important;">
					<!--<form action="<?php echo base_url(PROJECTPATH.'/report_loan_atm/atm_detail_report'); ?>" id="form1" method="GET" target="_blank">-->
					<form action="<?php echo base_url(PROJECTPATH.'/_atm_detail_report_log.php'); ?>" id="form1" method="GET" target="_blank">
						<br>
						<!--<input type="hidden" id="account_id" name="account_id" value="<?php echo @$_GET['account_id'];?>">-->
						<!--<h3>เลขที่บัญชี : <?php echo @$this->center_function->convert_account_id(@$_GET['account_id']);?></h3>-->
						<div class="form-group g24-col-sm-24" >									
							<label class="g24-col-sm-6 control-label">เลือกวันที่</label>
							<div class="input-with-icon g24-col-sm-4" >
								<div class="form-group">
									<input id="date_start" name="date_start" class="form-control" style="padding-left: 50px;" type="text" value="<?php echo $this->center_function->mydate2date(date('Y-m-d')); ?>" data-date-language="th-th" required autocomplete="off" >
									<span class="icon icon-calendar input-icon m-f-1"></span>
								</div>	
							</div>
							<label class="g24-col-sm-3 control-label" style="text-align:center;">ถึง วันที่</label>
							<div class="input-with-icon g24-col-sm-4" >
								<div class="form-group">
									<input id="date_end" name="date_end" class="form-control" style="padding-left: 50px;" type="text" value="<?php echo $this->center_function->mydate2date(date('Y-m-d')); ?>" data-date-language="th-th" required autocomplete="off" >
									<span class="icon icon-calendar input-icon m-f-1"></span>
								</div>	
							</div>
							<div class="g24-col-sm-2">
								<button id="btn_link_preview" type="submit" name="view" value="preview" class="btn btn-primary" style="width:100%">
									แสดง
								</button>
							</div>
							<div class="g24-col-sm-3">
								<button id="btn_link_preview" type="submit" name="update_log_atm" id="update_log_atm" value="update_log_atm" class="btn btn-primary" style="width:100%">
									อัพเดต log ATM
								</button>
							</div>
						</div>
						<div class="form-group g24-col-sm-24" >									
							<label class="g24-col-sm-6 control-label">รหัสสมาชิก</label>
							<div class="g24-col-sm-4" >
								<div class="form-group">
									<input id="member_id" name="member_id" class="form-control" type="text">
								</div>	
							</div>						
						</div>
					</form>	
					<br>
					<br>
					<!--<div style="padding-top:10px;">
						<table style="width: 100%;" border="0" cellpadding="0" cellspacing="0" class="table table-view table-center">
							<thead>
								<tr>
									<th style='width: 15%;'>วันเวลา</th>
									<th style='width: 10%;'>รหัสสมาชิก</th>
									<th style='width: 40%;'>รายการ</th>
									<th style='width: 10%;'>D1</th>
									<th style='width: 10%;'>Upbean</th>
									<th style='width: 15%;'>Status</th>
								</tr>
							</thead>
							<tbody>								
							<?php 
								//echo '<pre>'; print_r($row); echo '</pre>';
								foreach($row AS $key=>$value){
									//echo '<pre>'; print_r($value); echo '</pre>';
							?>
								<tr>
									<td><?php echo $this->center_function->ConvertToThaiDate(@$value['createdatetime'],1,1,1);?></td>
									<td><?php echo @$value['mem_id'];?></td>
									<td class="text-left">
										<?php 
											if(@$value['messageType'] == '0410'){
												echo 'คืนเงิน';
											}else{
												echo $type_list[@$value['tranType']];
											}
										?>
									</td>
									<td></td>
									<td></td>
									<td><?php echo (@$value['responseCode']=='000')?'Complete':'Not Complete';?></td>
								</tr>
							<?php } ?>
							</tbody>
						</table>	
					</div>
					-->
				</div>
			</div>
		</div>
	</div>
</div>
  
<script>	
	var base_url = $('#base_url').attr('class');
	$( document ).ready(function() {        
        $("#date_start").datepicker({
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
        $("#date_end").datepicker({
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

    $("form").submit(function(e){
        return true;
    });
</script>


