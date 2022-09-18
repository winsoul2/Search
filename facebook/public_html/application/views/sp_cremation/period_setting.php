<div class="layout-content">
    <div class="layout-content-body">
        <style>
            input[type=number]::-webkit-inner-spin-button,
            input[type=number]::-webkit-outer-spin-button {
                -webkit-appearance: none;
                margin: 0;
            }
            th, td {
                text-align: center;
            }
            .modal-dialog-delete {
                margin:0 auto;
                width: 350px;
                margin-top: 8%;
            }
            .modal-dialog-account {
                margin:auto;
                width: 70%;
                margin-top:7%;
            }
            .control-label {
                text-align:right;
                padding-top:5px;
            }
            .text_left {
                text-align:left;
            }
            .text_right {
                text-align:right;
            }
        </style>
        <h1 style="margin-bottom: 0">ตั้งค่ารอบสมัคร</h1>
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 padding-l-r-0">
            <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6 padding-l-r-0">
                <?php $this->load->view('breadcrumb'); ?>
            </div>
            <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 padding-l-r-0">
                <button class="btn btn-primary btn-lg bt-add" type="button" id="add_btn">
                    <span class="icon icon-plus-circle"></span>
                    เพิ่มรายการ
                </button>
            </div>
        </div>
        <div class="row gutter-xs">
            <div class="col-xs-12 col-md-12">
                <div class="panel panel-body">
                    <div class="bs-example" data-example-id="striped-table">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th class="font-normal" width="10%">วันที่เริ่มต้น</th>
                                    <th class="font-normal" width="10%">วันที่สิ้นสุด</th>
                                    <th class="font-normal"> ชื่อรอบ </th>
                                    <th class="font-normal" width="10%"> ค่าบำรุงรายปี </th>
                                    <th class="font-normal" width="10%"> ค่าสมัคร </th>
                                    <th class="font-normal" width="10%"> ค่าบำรุงสมาคม </th>
                                    <th class="font-normal" width="10%"> ค่าใช้จ่ายอื่นๆ </th>
                                    <th class="font-normal" width="20%"></th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php
                                foreach($datas as $period) {
                            ?>
                                <tr>
                                    <td class="text-center"><?php echo $this->center_function->ConvertToThaiDate($period["start_date"],'1','0'); ?></td>
                                    <td class="text-center"><?php echo $this->center_function->ConvertToThaiDate($period["end_date"],'1','0'); ?></td>
                                    <td class="text-left"><?php echo $period["name"];?></td>
                                    <td class="text-right"><?php echo number_format($period["annual_fee"],2);?></td>
                                    <td class="text-right"><?php echo number_format($period["fee"],2);?></td>
                                    <td class="text-right"><?php echo number_format($period["assoc_fee"],2);?></td>
                                    <td class="text-right"><?php echo number_format($period["other_fee"],2);?></td>
                                    <td class="text-right">
                            <?php
                                    if(empty($period["reg_id"])) {
                            ?>
                                        <input type="button" class="btn btn-danger btn_delete" data-id="<?php echo $period["id"];?>" id="del_btn_<?php echo $period["id"]?>" value="ลบ"/>
                            <?php
                                    }
                            ?>
                                        <input type="button" class="btn btn-primary btn_edit" data-id="<?php echo $period["id"];?>" data-start-date="<?php echo $this->center_function->mydate2date($period["start_date"]);?>"
                                                data-end-date="<?php echo $this->center_function->mydate2date($period["end_date"]);?>" data-name="<?php echo $period["name"];?>" data-annual-fee="<?php echo $period["annual_fee"];?>"
                                                data-fee="<?php echo $period["fee"];?>" id="edit_btn_<?php echo $period["id"]?>" data-assoc-fee="<?php echo $period["assoc_fee"];?>" data-other-fee="<?php echo $period["other_fee"];?>" value="แก้ไข"/>
                                    </td>
                                </tr>
                            <?php
                                }
                            ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <?php echo @$paging ?>
            </div>
        </div>
    </div>
</div>
<div id="add_madal" tabindex="-1" role="dialog" class="modal fade">
    <div class="modal-dialog modal-dialog-account">
        <div class="modal-content">
            <div class="modal-header modal-header-confirmSave">
                <h2 class="modal-title">บันทึก</h2>
            </div>
            <div class="modal-body">
                <form action="<?php echo base_url(PROJECTPATH.'/sp_cremation/'.$path.'/period_setting/'); ?>" method="post" id="form1">
                    <input id="period_id" name="period_id" type="hidden" class="type_input" value="">
                    <div class="row">
                        <div class="form-group">
                            <label class="col-sm-4 control-label">วันที่เริ่มต้น</label>
                            <div class="col-sm-3">
                                <div class="input-with-icon">
                                    <div class="form-group">
                                        <input id="start_date" name="start_date" class="form-control m-b-1 type_input date_time" type="text" value="<?php echo $this->center_function->mydate2date(date('Y-m-d')); ?>" data-date-language="th-th" style="padding-left:38px;">
                                        <span class="icon icon-calendar input-icon m-f-1"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group">
                            <label class="col-sm-4 control-label">วันที่สิ้นสุด</label>
                            <div class="col-sm-3">
                                <div class="input-with-icon">
                                    <div class="form-group">
                                        <input id="end_date" name="end_date" class="form-control m-b-1 type_input date_time" type="text" value="<?php echo $this->center_function->mydate2date(date('Y-m-d')); ?>" data-date-language="th-th" style="padding-left:38px;">
                                        <span class="icon icon-calendar input-icon m-f-1"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group">
                            <label class="col-sm-4 control-label">ชื่อรอบ</label>
                            <div class="col-sm-6">
                                <div class="input-with-icon">
                                    <div class="form-group">
                                        <input id="name" name="name" class="form-control m-b-1 type_input" type="text" value="">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group">
                            <label class="col-sm-4 control-label">ค่าบำรุงรายปี</label>
                            <div class="col-sm-3">
                                <div class="input-with-icon">
                                    <div class="form-group">
                                        <input id="annual_fee" name="annual_fee" class="form-control m-b-1 type_input" type="text" value="" onkeyup="format_the_number_decimal(this)">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group">
                            <label class="col-sm-4 control-label">ค่าสมัคร</label>
                            <div class="col-sm-3">
                                <div class="input-with-icon">
                                    <div class="form-group">
                                        <input id="fee" name="fee" class="form-control m-b-1 type_input" type="text" value="" onkeyup="format_the_number_decimal(this)">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group">
                            <label class="col-sm-4 control-label">ค่าบำรุงสมาคม</label>
                            <div class="col-sm-3">
                                <div class="input-with-icon">
                                    <div class="form-group">
                                        <input id="assoc_fee" name="assoc_fee" class="form-control m-b-1 type_input" type="text" value="" onkeyup="format_the_number_decimal(this)">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group">
                            <label class="col-sm-4 control-label">ค่าใช้จ่ายอื่นๆ</label>
                            <div class="col-sm-3">
                                <div class="input-with-icon">
                                    <div class="form-group">
                                        <input id="other_fee" name="other_fee" class="form-control m-b-1 type_input" type="text" value="" onkeyup="format_the_number_decimal(this)">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group text-center">
                        <button type="button" class="btn btn-primary min-width-100" id="submit_btn">ตกลง</button>
                        <button class="btn btn-danger min-width-100" type="button" id="cancel_btn">ยกเลิก</button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>
<form action="<?php echo base_url(PROJECTPATH.'/sp_cremation/'.$path.'/period_setting'); ?>" method="post" id="del_form">
    <input id="delete_id" name="delete_id" type="hidden" value="">
</form>
<script>
    $(document).ready(function() {
        $(".date_time").datepicker({
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

        $("#add_btn").click(function() {
            $(".type_input").val("");
            $(".date_time").val("<?php echo $this->center_function->mydate2date(date('Y-m-d'));?>");
            $('#add_madal').modal('show');
        });

        $("#cancel_btn").click(function() {
            $('#add_madal').modal('hide');
        });

        $("#submit_btn").click(function() {
            warning_message = "";
            if($("#start_date").val() == "") {
                warning_message += " - วันที่เริ่มต้น\n";
            }
            if($("#end_date").val() == "") {
                warning_message += " - วันที่สิ้นสุด\n";
            }
            if($("#name").val() == "") {
                warning_message += " - ชื่อรอบ\n";
            }
            if($("#annual_fee").val() == "") {
                warning_message += " - ค่าบำรุงรายปี\n";
            }
            if($("#fee").val() == "") {
                warning_message += " - ค่าสมัคร\n";
            }
            if(warning_message != "") {
                swal('กรุณากรอกข้อมูล', warning_message, 'warning');
            } else {
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
                    baseZ: 5000,
                    bindEvents: false
                });
                $("#form1").submit();
            }
        });

        $(".btn_delete").click(function() {
            period_id = $(this).attr("data-id");
            swal({
                title: "ท่านต้องการลบข้อมูลใช่หรือไม่?",
                text: "",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: '#DD6B55',
                confirmButtonText: 'ยืนยัน',
                cancelButtonText: "ยกเลิก",
                closeOnConfirm: true,
                closeOnCancel: true
            },
            function(isConfirm) {
                if (isConfirm) {
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
                        baseZ: 5000,
                        bindEvents: false
                    });
                    $("#delete_id").val(period_id);
                    $("#del_form").submit();
                } else {
                }
            });
        });

        $(".btn_edit").click(function() {
            $("#period_id").val($(this).attr("data-id"));
            $("#start_date").val($(this).attr("data-start-date"));
            $("#end_date").val($(this).attr("data-end-date"));
            $("#name").val($(this).attr("data-name"));
            $("#fee").val($(this).attr("data-fee"));
            $("#annual_fee").val($(this).attr("data-annual-fee"));
            $("#assoc_fee").val($(this).attr("data-assoc-fee"));
            $("#other_fee").val($(this).attr("data-other-fee"));
            $('#add_madal').modal('show');
        });
    });

    function format_the_number_decimal(ele){
        var value = $('#'+ele.id).val();
        value = value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');
        var num = value.split(".");
        var decimal = '';
        var num_decimal = '';
        if(typeof num[1] !== 'undefined'){
            if(num[1].length > 2){
                num_decimal = num[1].substring(0, 2);
            }else{
                num_decimal =  num[1];
            }
            decimal =  "."+num_decimal;
        }
        if(value!=''){
            if(value == 'NaN'){
                $('#'+ele.id).val('');
            }else{
                value = (num[0] == '')?0:parseInt(num[0]);
                value = value.toLocaleString()+decimal;
                $('#'+ele.id).val(value);
            }
        }else{
            $('#'+ele.id).val('');
        }
    }
</script>