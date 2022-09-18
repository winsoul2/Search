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
		<h1 style="margin-bottom: 0">รายงานเรียกเก็บ</h1>
		<?php $this->load->view('breadcrumb'); ?>
		<div class="row gutter-xs">
			<div class="col-xs-12 col-md-12">
				<div id="main-panel" class="panel panel-body" style="padding-top:0px !important;">
				<form action="<?php echo base_url(PROJECTPATH.'/sp_cremation/'.$path.'/fee_charge_pdf'); ?>" id="form1" method="POST" target="_blank">
					<h3></h3>
					<div class="row">
						<div class="form-group">
							<label class="col-sm-4 control-label">ปีที่เรียกเก็บ</label>
							<div class="col-sm-1 text-center">
								<select id="search_year" name="year" class="form-control m-b-1">
								<?php
									$year = !empty($_POST["year"]) ? $_POST["year"] : date('Y')+543;
									for($i=((date('Y')+543)-5); $i<=((date('Y')+543)+5); $i++){
								?>
									<option value="<?php echo $i; ?>" <?php echo $i == $year ? 'selected' : ''; ?>><?php echo $i; ?></option>
								<?php
									}
								?>
								</select>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="form-group">
							<label class="col-sm-4 control-label">รอบสมัคร</label>
							<div class="col-sm-3  m-b-1">
								<select id="search_period_id" name="period_id" class="js-data-example-ajax">
									<option value=""></option>
									<?php
										foreach($periods as $period) {
									?>
										<option value="<?php echo $period["id"];?>"><?php echo $period["name"];?></option>
									<?php
										}
									?>
								</select>
							</div>
						</div>
					</div>
					<div class="form-group g24-col-sm-24">
					</div>
					<div class="form-group g24-col-sm-24">
						<label class="g24-col-sm-6 control-label right"></label>
						<div class="g24-col-sm-10">
							<input type="button" class="btn btn-primary" style="" value="PDF" id="submit_btn">
							<input type="button" class="btn btn-default" style="" value="EXCEL" id="submit_excel_btn">
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
        $("#submit_btn").click(function() {
            $.ajax({
                url: base_url+'/sp_cremation/<?php echo $path;?>/check_fee_charge_report',	
                method:"post",
                data:$("#form1").serialize(),
                dataType:"text",
                success:function(result){
                    data = JSON.parse(result);
                    if(data["status"] == 'success'){
						$('#form1').attr('action', base_url+'/sp_cremation/<?php echo $path;?>/fee_charge_pdf');
                        $('#form1').submit();
                    } else {
                        $('#alertNotFindModal').appendTo("body").modal('show');
                    }
                }
            });
        });

		$("#submit_excel_btn").click(function() {
            $.ajax({
                url: base_url+'/sp_cremation/<?php echo $path;?>/check_fee_charge_report',	
                method:"post",
                data:$("#form1").serialize(),
                dataType:"text",
                success:function(result){
                    data = JSON.parse(result);
                    if(data["status"] == 'success'){
						$('#form1').attr('action', base_url+'/sp_cremation/<?php echo $path;?>/fee_charge_excel');
                        $('#form1').submit();
                    } else {
                        $('#alertNotFindModal').appendTo("body").modal('show');
                    }
                }
            });
        });

		createSelect2("main-panel");
	});

	function createSelect2(id){
        $('.js-data-example-ajax').select2({
            dropdownParent: $("#"+id)
        });
    }
</script>


