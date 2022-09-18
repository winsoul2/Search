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
        <h1 style="margin-bottom: 0">รายงานรายการเรียกเก็บ</h1>
        <?php $this->load->view('breadcrumb'); ?>
        <div class="row gutter-xs">
            <div class="col-xs-12 col-md-12">
                <div class="panel panel-body" style="padding-top:0px !important;">
                    <form action="<?php echo base_url(PROJECTPATH.'/report_cremation_data/coop_report_finance_month_preview'); ?>" id="form1" method="GET" target="_blank">
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
                            <label class="g24-col-sm-6 control-label right"> แสดงผล </label>
                            <div class="g24-col-sm-11">
                                <div class="form-group">
                                    <label class="radio-inline"><input type="radio" name="type" value="" checked="checked" class="" checked="checked"> ทั้งหมด </label> &nbsp;
                                    <label class="radio-inline"><input type="radio" name="type" value="completed" class=""> ชำระครบ </label>
                                    <label class="radio-inline"><input type="radio" name="type" value="owe" class=""> ค้างชำระ </label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group g24-col-sm-24">
                            <label class="g24-col-sm-6 control-label right">ประเภทสมาชิก</label>
                            <div class="g24-col-sm-18">
                                <label class="radio-inline">
                                    <input type="radio" name="member_type" value="" id="select_all" <?php echo empty($_GET["member_type"]) ? 'checked' : '';?>> ทั้งหมด
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="member_type" value="ordinary_member" id="select_member" <?php echo $_GET["member_type"] == "ordinary_member" ? 'checked' : '';?>> สามัญ
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="member_type" value="assoc_member" id="select_not_member" <?php echo $_GET["member_type"] == "assoc_member" ? 'checked' : '';?>> สมทบ
                                </label>
                            </div>
                        </div>
                        <div class="form-group g24-col-sm-24">
                            <label class="g24-col-sm-6 control-label right"></label>
                            <div class="g24-col-sm-18">
                                <label class="radio-inline">
                                    <input type="radio" name="is_coop_member" value="" id="select_all" <?php echo empty($_GET["is_coop_member"]) ? 'checked' : '';?>> ทั้งหมด
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="is_coop_member" value="1" id="select_member" <?php echo $_GET["is_coop_member"] == "1" ? 'checked' : '';?>> เป็นสมาชิกสหกรณ์
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="is_coop_member" value="2" id="select_not_member" <?php echo $_GET["is_coop_member"] == "2" ? 'checked' : '';?>> ไม่เป็นสมาชิกสหกรณ์
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
			url: base_url+'report_cremation_data/check_report_finance_month',	
			method:"post",
			data: $("#form1").serializeArray(),
			dataType:"text",
			success:function(data){
                $.unblockUI();	
                if(data == 'success'){
                    link_to =  base_url+'report_cremation_data/coop_report_finance_month_preview';
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
			url: base_url+'report_cremation_data/check_report_finance_month',	
			method:"post",
			data: $("#form1").serializeArray(),
			dataType:"text",
			success:function(data){
                $.unblockUI();	
                if(data == 'success'){
                    link_to =  base_url+'report_cremation_data/coop_report_finance_month_excel';
				    $('#form1').attr('action', link_to);
                    $('#form1').submit()
                }else{
                    $('#alertNotFindModal').appendTo("body").modal('show')
                }
			}
		})
    }


</script>