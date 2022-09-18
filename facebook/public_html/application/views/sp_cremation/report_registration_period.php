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
		<h1 style="margin-bottom: 0">รายงานรอบสมัคร</h1>
		<?php $this->load->view('breadcrumb'); ?>
		<div class="row gutter-xs">
			<div class="col-xs-12 col-md-12">
				<div class="panel panel-body" style="padding-top:0px !important;">
				<form action="<?php echo base_url(PROJECTPATH.'/sp_cremation/'.$path.'/registration_period_pdf'); ?>" id="form1" method="POST" target="_blank">
					<h3></h3>
					<div class="form-group g24-col-sm-24">
						<label class="g24-col-sm-6 control-label right"> วันที่ </label>
						<div class="g24-col-sm-4">
							<div class="input-with-icon">
								<div class="form-group">
									<input id="start_date" name="start_date" class="form-control m-b-1 mydate" style="padding-left: 50px;" type="text" value="" data-date-language="th-th">
									<span class="icon icon-calendar input-icon m-f-1"></span>
								</div>
							</div>
						</div>
						<label class="g24-col-sm-2 control-label right"> ถึงวันที่ </label>
						<div class="g24-col-sm-4">
							<div class="input-with-icon">
								<div class="form-group">
									<input id="end_date" name="end_date" class="form-control m-b-1 mydate" style="padding-left: 50px;" type="text" value="" data-date-language="th-th">
									<span class="icon icon-calendar input-icon m-f-1"></span>
								</div>
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

        $("#submit_btn").click(function() {
            $.ajax({
                url: base_url+'/sp_cremation/<?php echo $path;?>/check_registration_period_report',	
                method:"post",
                data:$("#form1").serialize(),
                dataType:"text",
                success:function(result){
                    data = JSON.parse(result);
                    if(data["status"] == 'success'){
						$('#form1').attr('action', base_url+'/sp_cremation/<?php echo $path;?>/registration_period_pdf');
                        $('#form1').submit();
                    } else {
                        $('#alertNotFindModal').appendTo("body").modal('show');
                    }
                }
            });
        });

		$("#submit_excel_btn").click(function() {
            $.ajax({
                url: base_url+'/sp_cremation/<?php echo $path;?>/check_registration_period_report',	
                method:"post",
                data:$("#form1").serialize(),
                dataType:"text",
                success:function(result){
                    data = JSON.parse(result);
                    if(data["status"] == 'success'){
						$('#form1').attr('action', base_url+'/sp_cremation/<?php echo $path;?>/registration_period_excel');
                        $('#form1').submit();
                    } else {
                        $('#alertNotFindModal').appendTo("body").modal('show');
                    }
                }
            });
        });

        $("#mem_type_all").change(function() {
            if ($(this).is(':checked')) {
                $(".type_item").prop("checked","checked");
            } else {
                $(".type_item").prop("checked","");
            }
        });

        $(".type_item").change(function() {
            if (!$(this).is(':checked')) {
                $("#mem_type_all").prop("checked","");
            }
        });
	});
</script>


