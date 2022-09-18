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
        <h1 style="margin-bottom: 0">รายงานสรุปวงเงินกู้และวัตถุประสงค์</h1>
        <?php $this->load->view('breadcrumb'); ?>
        <div class="row gutter-xs">
            <div class="col-xs-12 col-md-12">
                <div class="panel panel-body" style="padding-top:0px !important;">
                    <form action="<?php echo base_url(PROJECTPATH.'/report_summary_and_purpose/summary_and_purpose_preview'); ?>" id="form1" method="GET" target="_blank">
                        <br>
                        <div class="form-group g24-col-sm-24">
                            <label class="g24-col-sm-6 control-label right"> ระหว่างวันที่ </label>
                            <div class="g24-col-sm-5">
                                <div class="input-with-icon">
                                    <div class="form-group">
                                        <input id="start_date" name="start_date" class="form-control m-b-1 mydate" style="padding-left: 50px;" type="text" value="<?php echo $this->center_function->mydate2date(date('Y-m-d')); ?>" data-date-language="th-th" autocomplete="off" >
                                        <span class="icon icon-calendar input-icon m-f-1"></span>
                                    </div>
                                </div>
                            </div>
                            <label class="g24-col-sm-1 control-label right"> ถึง </label>
                            <div class="g24-col-sm-5">
                                <div class="input-with-icon">
                                    <div class="form-group">
                                        <input id="start_date" name="end_date" class="form-control m-b-1 mydate" style="padding-left: 50px;" type="text" value="<?php echo $this->center_function->mydate2date(date('Y-m-d')); ?>" data-date-language="th-th" autocomplete="off" >
                                        <span class="icon icon-calendar input-icon m-f-1"></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group g24-col-sm-24"  style="margin-top: 10px;">
                            <label class="g24-col-sm-6 control-label right"> ประเภทเงินกู้ </label>
                            <div class="g24-col-sm-11">
                                <select name="loan_type" class="form-control">
                                    <option value="">เลือกทั้งหมด</option>
                                    <?php foreach($loan_type as $key => $value){?>
                                    <option value="<?php echo $value['id'];?>"><?php echo $value['loan_type'];?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>

                        <div class="form-group g24-col-sm-24" style="margin-top: 10px;">
                            <label class="g24-col-sm-6 control-label right"></label>
                            <div class="g24-col-sm-11">
                                <!-- <input type="submit" class="btn btn-primary" style="width:100%" value="รายงานการถอนเงินสามัญหมุนเวียน" > -->
                                <button id="btn_link_preview" type="submit" name="view" value="preview" class="btn btn-primary" style="width:100%">
                                    รายงานสรุปวงเงินกู้และวัตถุประสงค์
                                </button>
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
        $("#type_date").change(function() {
            if($(this).val() == 1) {
                $(".end-date-label").hide();
            } else {
                $(".end-date-label").show();
            }
        });

        $("#type_id").change(function() {
            link_export();
        });
        $("#start_date").change(function() {
            link_export();
        });
    });

    $("form").submit(function(e){
        return true;
    });

</script>


