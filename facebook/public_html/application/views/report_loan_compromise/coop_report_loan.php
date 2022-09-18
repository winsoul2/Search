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
        <h1 style="margin-bottom: 0">รายงานเงินกู้กลุ่มประนอมหนี้</h1>
        <?php $this->load->view('breadcrumb'); ?>
        <div class="row gutter-xs">
            <div class="col-xs-12 col-md-12">
                <div class="panel panel-body" style="padding-top:0px !important;">
                    <form action="<?php echo base_url(PROJECTPATH.'/report_loan_compromise/coop_report_loan_emergent_preview'); ?>" id="form1" name="form1" method="GET" target="_blank">
                        <div class="form-group g24-col-sm-24">
                            <div class="g24-col-sm-5 right">
                                <h3>รายวัน</h3>
                            </div>
                        </div>
                        <div class="form-group g24-col-sm-24">
                            <label class="g24-col-sm-6 control-label right"> ประเภทเงินกู้ </label>
                            <div class="g24-col-sm-8">
                                <select name="loan_type" id="loan_type1" class="form-control" onchange="change_loan_type('1');">
                                    <?php
                                    if(!empty($loan_type)){
                                        foreach($loan_type as $key => $value){
                                            ?>
                                            <option value="<?php echo @$key; ?>"><?php echo @$value; ?></option>
                                            <?php
                                        }
                                    }
                                    ?>
                                </select>
                                <?php
                                if(!empty($loan_type)){
                                    foreach($loan_type as $key => $value){
                                        ?>
                                        <div class="box_loan_name box_loan_name_1_<?=$key?> hide" style="border-radius: 6px;background-color: #D7D7D7;padding: 10px 15px 10px;">
                                            <?php
//                                            echo $key;
//                                            echo '<pre>';print_r($loan_name[$key]);exit;
                                            foreach($loan_name[$key] as $k => $v){
                                                ?>
                                                <div class="row <?="loan_type1_".$key?>">
                                                    <div class="col-sm-1"><input type="checkbox" name="loan_name[1][]" value="<?=$v['loan_name_id']?>"></div>
                                                    <div class="col-sm-7"><?=$v['loan_name']?></div>
                                                </div>
                                                <?php
                                            }
                                            ?>
                                        </div>
                                        <?php
                                    }
                                }
                                ?>
                            </div>
                        </div>
                        <div class="form-group g24-col-sm-24">
                            <label class="g24-col-sm-6 control-label right"> วันที่ </label>
                            <div class="g24-col-sm-8">
                                <div class="input-with-icon">
                                    <div class="form-group">
                                        <input id="report_date" name="report_date" class="form-control m-b-1 mydate" style="padding-left: 50px;" type="text" value="<?php echo $this->center_function->mydate2date(date('Y-m-d')); ?>" data-date-language="th-th">
                                        <span class="icon icon-calendar input-icon m-f-1"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="g24-col-sm-2">
                                <input type="hidden" name="report" value='1'>
                                <button class="btn btn-primary btn-after-input" type="button"  onclick="check_empty('1', 'compromise')"><span> แสดงผล</span></button>
                            </div>
                        </div>
                    </form>
                    <form action="<?php echo base_url(PROJECTPATH.'/report_loan_compromise/coop_report_loan_emergent_preview'); ?>" id="form2" method="GET" target="_blank">
                        <div class="form-group g24-col-sm-24">
                            <div class="g24-col-sm-5 right">
                                <h3>รายเดือน</h3>
                            </div>
                        </div>
                        <div class="form-group g24-col-sm-24">
                            <label class="g24-col-sm-6 control-label right"> ประเภทเงินกู้ </label>
                            <div class="g24-col-sm-8">
                                <select name="loan_type" id="loan_type2" class="form-control" onchange="change_loan_type('2');">
                                    <?php
                                    if(!empty($loan_type)){
                                        foreach($loan_type as $key => $value){
                                            ?>
                                            <option value="<?php echo $key; ?>"><?php echo $value; ?></option>
                                            <?php
                                        }
                                    }
                                    ?>
                                </select>
                                <?php
                                if(!empty($loan_type)){
                                    foreach($loan_type as $key => $value){
                                        ?>
                                        <div class="box_loan_name box_loan_name_2_<?=$key?> hide" style="border-radius: 6px;background-color: #D7D7D7;padding: 10px 15px 10px;">
                                            <?php
                                            foreach($loan_name[$key] as $k => $v){
                                                ?>
                                                <div class="row <?="loan_type2_".$key?>">
                                                    <div class="col-sm-1"><input type="checkbox" name="loan_name[2][]" value="<?=$v['loan_name_id']?>"></div>
                                                    <div class="col-sm-7"><?=$v['loan_name']?></div>
                                                </div>
                                                <?php
                                            }
                                            ?>
                                        </div>
                                        <?php
                                    }
                                }
                                ?>
                            </div>
                        </div>
                        <div class="form-group g24-col-sm-24">
                            <label class="g24-col-sm-6 control-label right"> เดือน </label>
                            <div class="g24-col-sm-8">
                                <select id="report_month" name="month" class="form-control">
                                    <?php foreach($month_arr as $key => $value){ ?>
                                        <option value="<?php echo $key; ?>" <?php echo $key==date('m')?'selected':''; ?>><?php echo $value; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group g24-col-sm-24">
                            <label class="g24-col-sm-6 control-label right"> ปี </label>
                            <div class="g24-col-sm-8">
                                <select id="report_year" name="year" class="form-control">
                                    <?php for($i=((date('Y')+543)-5); $i<=((date('Y')+543)+5); $i++){ ?>
                                        <option value="<?php echo $i; ?>" <?php echo $i==(date('Y')+543)?'selected':''; ?>><?php echo $i; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="g24-col-sm-2">
                                <input type="hidden" name="report" value='2'>
                                <button class="btn btn-primary btn-after-input" type="button"  onclick="check_empty('2', 'compromise')"><span> แสดงผล</span></button>
                            </div>
                        </div>
                    </form>
                    <form action="<?php echo base_url(PROJECTPATH.'/report_loan_compromise/coop_report_loan_emergent_preview'); ?>" id="form3" method="GET" target="_blank">
                        <div class="form-group g24-col-sm-24">
                            <div class="g24-col-sm-5 right">
                                <h3>รายปี</h3>
                            </div>
                        </div>
                        <div class="form-group g24-col-sm-24">
                            <label class="g24-col-sm-6 control-label right"> ประเภทเงินกู้ </label>
                            <div class="g24-col-sm-8">
                                <select name="loan_type" id="loan_type3" class="form-control" onchange="change_loan_type('3');">
                                    <?php
                                    if(!empty($loan_type)){
                                        foreach($loan_type as $key => $value){
                                            ?>
                                            <option value="<?php echo $key; ?>"><?php echo $value; ?></option>
                                            <?php
                                        }
                                    }
                                    ?>
                                </select>
                                <?php
                                if(!empty($loan_type)){
                                    foreach($loan_type as $key => $value){
                                        ?>
                                        <div class="box_loan_name box_loan_name_3_<?=$key?> hide" style="border-radius: 6px;background-color: #D7D7D7;padding: 10px 15px 10px;">
                                            <?php
                                            foreach($loan_name[$key] as $k => $v){
                                                ?>
                                                <div class="row <?="loan_type3_".$key?>">
                                                    <div class="col-sm-1"><input type="checkbox" name="loan_name[3][]" value="<?=$v['loan_name_id']?>"></div>
                                                    <div class="col-sm-7"><?=$v['loan_name']?></div>
                                                </div>
                                                <?php
                                            }
                                            ?>
                                        </div>
                                        <?php
                                    }
                                }
                                ?>
                            </div>
                        </div>
                        <div class="form-group g24-col-sm-24">
                            <label class="g24-col-sm-6 control-label right"> ปี </label>
                            <div class="g24-col-sm-8">
                                <select id="report_only_year" name="year" class="form-control">
                                    <?php for($i=((date('Y')+543)-5); $i<=((date('Y')+543)+5); $i++){ ?>
                                        <option value="<?php echo $i; ?>" <?php echo $i==(date('Y')+543)?'selected':''; ?>><?php echo $i; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="g24-col-sm-2">
                                <input type="hidden" name="report" value='3'>
                                <button class="btn btn-primary btn-after-input" type="button"  onclick="check_empty('3', 'compromise')"><span> แสดงผล</span></button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php

$link = array(
    'src' => PROJECTJSPATH.'assets/js/coop_report_loan.js?v=3',
    'type' => 'text/javascript'
);
echo script_tag($link);

?>


<script>
    $( document ).ready(function() {
        $(".box_loan_name_1_7").removeClass("hide");
        $(".box_loan_name_2_7").removeClass("hide");
        $(".box_loan_name_3_7").removeClass("hide");
    });
</script>