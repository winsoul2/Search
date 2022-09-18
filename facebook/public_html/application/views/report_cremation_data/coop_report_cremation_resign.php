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
        <h1 style="margin-bottom: 0">รายงานสมาชิกลาออก</h1>
        <?php $this->load->view('breadcrumb'); ?>
        <div class="row gutter-xs">
            <div class="col-xs-12 col-md-12">
                <div class="panel panel-body" style="padding-top:0px !important;">
                    <form action="<?php echo base_url(PROJECTPATH.'/report_cremation_data/coop_report_resign_preview'); ?>" id="form1" method="GET" target="_blank">
                        <h3></h3>
                        <div class="form-group g24-col-sm-24">
                            <label class="g24-col-sm-6 control-label right"> วันที่ </label>
                            <div class="g24-col-sm-4">
                                <div class="input-with-icon">
                                    <div class="form-group">
                                        <input id="start_date" name="start_date" class="form-control m-b-1 mydate" style="padding-left: 50px;" type="text" value="<?php echo $this->center_function->mydate2date(date('Y-m-d')); ?>" data-date-language="th-th">
                                        <span class="icon icon-calendar input-icon m-f-1"></span>
                                    </div>
                                </div>
                            </div>
                            <label class="g24-col-sm-1 control-label right"> ถึง </label>
                            <div class="g24-col-sm-4">
                                <div class="input-with-icon">
                                    <div class="form-group">
                                        <input id="end_date" name="end_date" class="form-control m-b-1 mydate" style="padding-left: 50px;" type="text" value="<?php echo $this->center_function->mydate2date(date('Y-m-d')); ?>" data-date-language="th-th">
                                        <span class="icon icon-calendar input-icon m-f-1"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group g24-col-sm-24">
                            <label class="g24-col-sm-6 control-label right"> สถานะ </label>
                            <div class="g24-col-sm-18">
                                <label class="radio-inline">
                                    <input type="radio" name="status" value="" id="select_all" <?php echo empty($_GET["status"]) ? 'checked' : '';?>> ทั้งหมด
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="status" value="pending" id="select_paid" <?php echo $_GET["status"] == "pending" ? 'checked' : '';?>> รออนุมัติ
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="status" value="approved" id="select_non_paid" <?php echo $_GET["status"] == "approved" ? 'checked' : '';?>> อนุมัติ
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="status" value="reject" id="select_non_paid" <?php echo $_GET["status"] == "reject" ? 'checked' : '';?>> ไม่อนุมัติ
                                </label>
                            </div>
                        </div>
                        <div class="form-group g24-col-sm-24">
                            <label class="g24-col-sm-5 control-label right"></label>
                            <div class="g24-col-sm-4">
                                <input type="button" class="btn btn-primary" style="width:100%" value="แสดงรายงาน" onclick="check_empty()">
                            </div>
                            <div class="g24-col-sm-4">
                                <input type="button" class="btn btn-default" style="width:100%" value="Export Excel" onclick="check_empty_excel()">
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
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
	})
	function check_empty() {
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
		})
		$.ajax({
			url: base_url+'/report_cremation_data/check_report_resign',	
			method:"post",
			data: $("#form1").serializeArray(),
			dataType:"text",
			success:function(data){
                $.unblockUI();	
                if(data == 'success'){
                    link_to =  base_url+'report_cremation_data/coop_report_resign_preview';
                    $('#form1').attr('action', link_to);
                    $('#form1').submit()
                }else{
                    $('#alertNotFindModal').appendTo("body").modal('show')
                }
			}
		})
    }

    function check_empty_excel() {
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
		})
		$.ajax({
			url: base_url+'/report_cremation_data/check_report_resign',	
			method:"post",
			data: $("#form1").serializeArray(),
			dataType:"text",
			success:function(data){
                $.unblockUI();	
                if(data == 'success'){
                    link_to =  base_url+'report_cremation_data/coop_report_resign_excel';
				    $('#form1').attr('action', link_to);
                    $('#form1').submit()
                }else{
                    $('#alertNotFindModal').appendTo("body").modal('show')
                }
			}
		})
    }


</script>