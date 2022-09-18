<div class="layout-content">
    <div class="layout-content-body">
        <?php
        $month_arr = array('1'=>'มกราคม','2'=>'กุมภาพันธ์','3'=>'มีนาคม','4'=>'เมษายน','5'=>'พฤษภาคม','6'=>'มิถุนายน','7'=>'กรกฎาคม','8'=>'สิงหาคม','9'=>'กันยายน','10'=>'ตุลาคม','11'=>'พฤศจิกายน','12'=>'ธันวาคม');
        ?>
        <style>
            .modal.fade {
                z-index: 10000000 !important;
            }
            .modal-backdrop.in{
                opacity: 0;
            }
            .modal-backdrop {
                position: relative;
                top: 0;
                right: 0;
                bottom: 0;
                left: 0;
                z-index: 1040;
                background-color: #000;
            }

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
        <h1 style="margin-bottom: 0">ไฟล์ส่งหักเงินเดือน</h1>
        <?php $this->load->view('breadcrumb'); ?>
        <div class="row gutter-xs">
            <div class="col-xs-12 col-md-12">
                <div class="panel panel-body" style="padding-top:0px !important;">
                    <form action="<?php echo base_url(PROJECTPATH.'/text_file/hun'); ?>" id="form1" method="GET" target="_blank">
                        <div class="form-group g24-col-sm-24">
                            <div class="g24-col-sm-5 right">
                                <h3>หุ้น</h3>
                            </div>
                        </div>
                        <div class="form-group g24-col-sm-24">
                            <label class="g24-col-sm-6 control-label right"> ประเภทพนักงาน </label>
                            <div class="g24-col-sm-4">
                                <select name="hun_mem_type" id="hun_mem_type" class="form-control" onchange="check_total_share('SHARE')">
                                    <option value="1">พนักงาน</option>
                                    <option value="2">ลูกจ้าง</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group g24-col-sm-24">
                            <label class="g24-col-sm-6 control-label right"> เดือน </label>
                            <div class="g24-col-sm-4">
                                <select id="hun_month" name="hun_month" class="form-control" onchange="check_total_share('SHARE')">
                                    <?php foreach($month_arr as $key => $value){ ?>
                                        <option value="<?php echo $key; ?>" <?php echo $key==date('m')?'selected':''; ?>><?php echo $value; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group g24-col-sm-24">
                            <label class="g24-col-sm-6 control-label right"> ปี </label>
                            <div class="g24-col-sm-4">
                                <select id="hun_year" name="hun_year" class="form-control" onchange="check_total_share('SHARE')">
                                    <?php for($i=((date('Y')+543)-5); $i<=((date('Y')+543)+5); $i++){ ?>
                                        <option value="<?php echo $i; ?>" <?php echo $i==(date('Y')+543)?'selected':''; ?>><?php echo $i; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="g24-col-sm-2">
                                <button class="btn btn-primary btn-after-input" type="button"  onclick="check_empty('SHARE')"><span> แสดงผล</span></button>
                            </div>
                        </div>
						<div class="form-group g24-col-sm-24">
                            <label class="g24-col-sm-6 control-label right"> ยอดรวม </label>
                            <label class="g24-col-sm-4" id="total_share"></label>
                        </div>
                    </form>
                    <form action="<?php echo base_url(PROJECTPATH.'/text_file/coper'); ?>" id="form2" method="GET" target="_blank">
                        <div class="form-group g24-col-sm-24">
                            <div class="g24-col-sm-5 right">
                                <h3>สินเชื่อ</h3>
                            </div>
                        </div>
                        <div class="form-group g24-col-sm-24">
                            <label class="g24-col-sm-6 control-label right"> ประเภทพนักงาน</label>
                            <div class="g24-col-sm-4">
                                <select name="loan_mem_type" id="loan_mem_type" class="form-control" onchange="check_total_share('LOAN')">
                                    <option value="1">พนักงาน</option>
                                    <option value="2">ลูกจ้าง</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group g24-col-sm-24">
                            <label class="g24-col-sm-6 control-label right"> เดือน </label>
                            <div class="g24-col-sm-4">
                                <select id="loan_month" name="loan_month" class="form-control" onchange="check_total_share('LOAN')">
                                    <?php foreach($month_arr as $key => $value){ ?>
                                        <option value="<?php echo $key; ?>" <?php echo $key==date('m')?'selected':''; ?>><?php echo $value; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group g24-col-sm-24">
                            <label class="g24-col-sm-6 control-label right"> ปี </label>
                            <div class="g24-col-sm-4">
                                <select id="loan_year" name="loan_year" class="form-control" onchange="check_total_share('LOAN')">
                                    <?php for($i=((date('Y')+543)-5); $i<=((date('Y')+543)+5); $i++){ ?>
                                        <option value="<?php echo $i; ?>" <?php echo $i==(date('Y')+543)?'selected':''; ?>><?php echo $i; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="g24-col-sm-2">
                                <button class="btn btn-primary btn-after-input" type="button"  onclick="check_empty('LOAN')"><span> แสดงผล</span></button>
                            </div>
                        </div>
						<div class="form-group g24-col-sm-24">
                            <label class="g24-col-sm-6 control-label right"> ยอดรวม </label>
                            <label class="g24-col-sm-4" id="total_loan"></label>
                        </div>
                    </form>
                    <form action="<?php echo base_url(PROJECTPATH.'/text_file/sav'); ?>" id="form3" method="GET" target="_blank">
                        <div class="form-group g24-col-sm-24">
                            <div class="g24-col-sm-5 right">
                                <h3>เงินฝาก</h3>
                            </div>
                        </div>
                        <div class="form-group g24-col-sm-24">
                            <label class="g24-col-sm-6 control-label right"> ประเภทพนักงาน </label>
                            <div class="g24-col-sm-4">
                                <select name="sav_mem_type" id="sav_mem_type" class="form-control" onchange="check_total_share('DEPOSIT')">
                                    <option value="1">พนักงาน</option>
                                    <option value="2">ลูกจ้าง</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group g24-col-sm-24">
                            <label class="g24-col-sm-6 control-label right"> เดือน </label>
                            <div class="g24-col-sm-4">
                                <select id="sav_month" name="sav_month" class="form-control" onchange="check_total_share('DEPOSIT')">
                                    <?php foreach($month_arr as $key => $value){ ?>
                                        <option value="<?php echo $key; ?>" <?php echo $key==date('m')?'selected':''; ?>><?php echo $value; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group g24-col-sm-24">
                            <label class="g24-col-sm-6 control-label right"> ปี </label>
                            <div class="g24-col-sm-4">
                                <select id="sav_year" name="sav_year" class="form-control" onchange="check_total_share('DEPOSIT')">
                                    <?php for($i=((date('Y')+543)-5); $i<=((date('Y')+543)+5); $i++){ ?>
                                        <option value="<?php echo $i; ?>" <?php echo $i==(date('Y')+543)?'selected':''; ?>><?php echo $i; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="g24-col-sm-2">
                                <button class="btn btn-primary btn-after-input" type="button"  onclick="check_empty('DEPOSIT')"><span> แสดงผล</span></button>
                            </div>
                        </div>
						<div class="form-group g24-col-sm-24">
                            <label class="g24-col-sm-6 control-label right"> ยอดรวม </label>
                            <label class="g24-col-sm-4" id="total_deposit"></label>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
$v = date('YmdHis');
$link = array(
    'src' => PROJECTJSPATH.'assets/js/coop_text_files.js?v='.$v,
    'type' => 'text/javascript'
);
echo script_tag($link);

?>


