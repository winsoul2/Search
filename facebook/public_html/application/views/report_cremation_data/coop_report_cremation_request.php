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
        <h1 style="margin-bottom: 0">รายงานผู้สมัคร</h1>
        <?php $this->load->view('breadcrumb'); ?>
        <div class="row gutter-xs">
            <div class="col-xs-12 col-md-12">
                <div class="panel panel-body" style="padding-top:0px !important;">
                    <form action="<?php echo base_url(PROJECTPATH.'/report_cremation_data/preview_report_cremation_request'); ?>" id="form1" method="GET" target="_blank">
                        <h3></h3>
                        <div class="form-group g24-col-sm-24">
                            <label class="g24-col-sm-6 control-label right"> แสดงผล </label>
                            <div class="g24-col-sm-18">
                                <div class="form-group">
                                    <label><input type="radio" name="filter_type" value="date" class="" checked="checked"> ตามวันที่กำหนด </label>
                                    <label><input type="radio" name="filter_type" value="all" class=""> ทั้งหมด </label>
                                </div>
                            </div>
                        </div>
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
                                    <input type="radio" name="status" value="member" id="select_non_member" <?php echo $_GET["status"] == "member" ? 'member' : '';?>> ปกติ
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="status" value="reject" id="select_non_paid" <?php echo $_GET["status"] == "reject" ? 'checked' : '';?>> ไม่อนุมัติ
                                </label>
                            </div>
                        </div>
                        <div class="form-group g24-col-sm-24">
                            <label class="g24-col-sm-5 control-label right"></label>
                            <div class="g24-col-sm-10">
                                <input type="button" class="btn btn-primary" style="width:100%" value="แสดงรายงาน" onclick="check_empty()">
                            </div>
                        </div>
                        <!-- <div class="form-group g24-col-sm-24">
                            <div class="g24-col-sm-2">
                                <button class="btn btn-primary btn-after-input" type="button"  onclick="check_empty()"><span> แสดงผล</span></button>
                            </div>
                        </div> -->
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
$link = array(
    'src' => PROJECTJSPATH.'assets/js/coop_report_cremation_req.js',
    'type' => 'text/javascript'
);
echo script_tag($link);
?>


