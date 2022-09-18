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
            .bt-add {
                margin:5px;
            }
            .select_form {
                padding-top: 5px;
            }
        </style>
        <h1 style="margin-bottom: 0">ตั้งค่าเรียกเก็บ</h1>
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 padding-l-r-0">
            <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6 padding-l-r-0">
                <?php $this->load->view('breadcrumb'); ?>
            </div>
            <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 padding-l-r-0">
                <button class="btn btn-primary btn-lg bt-add" type="button" id="single_add_btn">
                    <span class="icon icon-plus-circle"></span>
                    ตั้งค่าเรียกเก็บรายรอบสมัคร
                </button>
                <button class="btn btn-primary btn-lg bt-add" type="button" id="all_add_btn">
                    <span class="icon icon-plus-circle"></span>
                    ตั้งค่าเรียกเก็บทั้งหมด
                </button>
            </div>
        </div>
        <div class="row gutter-xs">
            <div class="col-xs-12 col-md-12">
                <div class="panel panel-body">
                    <div class="bs-example" data-example-id="striped-table">
                        <div class="row">
                            <div class="form-group">
                                <label class="col-sm-4 control-label">ปีที่เรียกเก็บ</label>
                                <div class="col-sm-3">
                                    <select id="search_year" name="year" class="form-control">
                                    <?php
                                        for($i=((date('Y')+543)-5); $i<=((date('Y')+543)+5); $i++){
                                    ?>
                                        <option value="<?php echo $i; ?>" <?php echo $i == $_POST["year"] ? 'selected' : ''; ?>><?php echo $i; ?></option>
                                    <?php
                                        }
                                    ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th class="font-normal" width="5%"></th>
                                    <th class="font-normal"> ชื่อรอบ </th>
                                    <th class="font-normal" width="10%"> อัตราการเรียกเก็บ </th>
                                    <th class="font-normal" width="10%"> ค่าบำรุง </th>
                                    <th class="font-normal" width="10%"> รวม </th>
                                    <th class="font-normal" width="10%"> ปี </th>
                                    <th class="font-normal" width="20%"></th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php
                                $i = 1;
                                foreach($datas as $period) {
                            ?>
                                <tr>
                                    <td class="text-center"><?php echo $i++; ?></td>
                                    <td class="text-left"><?php echo $period["name"];?></td>
                                    <td class="text-right"><?php echo number_format($period["fee"],2);?></td>
                                    <td class="text-right"><?php echo number_format($period["assoc_fee"],2);?></td>
                                    <td class="text-right"><?php echo number_format($period["fee"] + $period["assoc_fee"],2);?></td>
                                    <td class="text-right"><?php echo $period["year"];?></td>
                                    <td class="text-right">
                                        <input type="button" class="btn btn-danger btn_delete" data-id="<?php echo $period["id"];?>" id="del_btn_<?php echo $period["id"]?>" value="ลบ"/>
                                        <input type="button" class="btn btn-primary btn_edit" data-id="<?php echo $period["id"];?>" data-period-id="<?php echo $period["period_id"];?>" data-assoc-fee="<?php echo number_format($period["assoc_fee"],2);?>"
                                                data-fee="<?php echo number_format($period["fee"],2);?>" data-year="<?php echo $period["year"]?>" id="edit_btn_<?php echo $period["id"]?>" value="แก้ไข"/>
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
<div id="all_add_modal" tabindex="-1" role="dialog" class="modal fade">
    <div class="modal-dialog modal-dialog-account">
        <div class="modal-content">
            <div class="modal-header modal-header-confirmSave">
                <h2 class="modal-title">บันทึก</h2>
            </div>
            <div class="modal-body">
                <form action="" method="post" id="form_all">
                    <div class="row">
                        <div class="form-group">
                            <label class="col-sm-4 control-label">ปี</label>
                            <div class="col-sm-3">
                                <select id="all_year" name="year" class="form-control">
                                <?php
                                    for($i=((date('Y')+543)-5); $i<=((date('Y')+543)+5); $i++){
                                ?>
                                    <option value="<?php echo $i; ?>" <?php echo $i==(date('Y')+543)?'selected':''; ?>><?php echo $i; ?></option>
                                <?php
                                    }
                                ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group">
                            <label class="col-sm-4 control-label">อัตราเรียกเก็บ</label>
                            <div class="col-sm-3 control-label">
                                <input id="all_fee" name="fee" class="form-control m-b-1 type_input" type="text" value="" onkeyup="format_the_number_decimal(this)">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group">
                            <label class="col-sm-4 control-label">ค่าบำรุง</label>
                            <div class="col-sm-3">
                                <input id="all_assoc_fee" name="assoc_fee" class="form-control m-b-1 type_input" type="text" value="" onkeyup="format_the_number_decimal(this)">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group">
                            <label class="col-sm-4 control-label"></label>
                            <div class="col-sm-3">
                                <input type="checkbox" id="replace" name="replace" value="1"> ถ้ามีข้อมูลเรียกเก็บอยู่แล้วให้ทับข้อมูลเดิม
                            </div>
                        </div>
                    </div>
                    <div class="form-group text-center">
                        <button type="button" class="btn btn-primary min-width-100" id="all_submit_btn">ตกลง</button>
                        <button class="btn btn-danger min-width-100" type="button" id="all_cancel_btn">ยกเลิก</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<div id="single_add_modal" tabindex="-1" role="dialog" class="modal fade">
    <div class="modal-dialog modal-dialog-account">
        <div class="modal-content">
            <div class="modal-header modal-header-confirmSave">
                <h2 class="modal-title">บันทึก</h2>
            </div>
            <div class="modal-body">
                <form action="" method="post" id="form_single">
                    <div class="row">
                        <div class="form-group">
                            <label class="col-sm-4 control-label">ปี</label>
                            <div class="col-sm-3">
                                <select id="year" name="year" class="form-control">
                                <?php
                                    for($i=((date('Y')+543)-5); $i<=((date('Y')+543)+5); $i++){
                                ?>
                                    <option value="<?php echo $i; ?>" <?php echo $i==(date('Y')+543)?'selected':''; ?>><?php echo $i; ?></option>
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
                            <div class="col-sm-3 select_form">
                                <select id="period_id" name="period_id" class="js-data-example-ajax">
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
                    <div class="row">
                        <div class="form-group">
                            <label class="col-sm-4 control-label">อัตราเรียกเก็บ</label>
                            <div class="col-sm-3 control-label">
                                <input id="fee" name="fee" class="form-control m-b-1 type_input" type="text" value="" onkeyup="format_the_number_decimal(this)">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group">
                            <label class="col-sm-4 control-label">ค่าบำรุง</label>
                            <div class="col-sm-3 control-label">
                                <input id="assoc_fee" name="assoc_fee" class="form-control m-b-1 type_input" type="text" value="" onkeyup="format_the_number_decimal(this)">
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
<div id="single_edit_modal" tabindex="-1" role="dialog" class="modal fade">
    <div class="modal-dialog modal-dialog-account">
        <div class="modal-content">
            <div class="modal-header modal-header-confirmSave">
                <h2 class="modal-title">บันทึก</h2>
            </div>
            <div class="modal-body">
                <form action="" method="post" id="form_edit_single">
                    <div class="row">
                        <div class="form-group">
                            <label class="col-sm-4 control-label">ปี</label>
                            <div class="col-sm-3">
                                <select id="edit_year" name="year" class="form-control">
                                <?php
                                    for($i=((date('Y')+543)-5); $i<=((date('Y')+543)+5); $i++){
                                ?>
                                    <option value="<?php echo $i; ?>" <?php echo $i==(date('Y')+543)?'selected':''; ?>><?php echo $i; ?></option>
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
                            <div class="col-sm-3 select_form">
                                <select id="edit_period_id" name="period_id" class="form-control js-data-example-ajax">
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
                    <div class="row">
                        <div class="form-group">
                            <label class="col-sm-4 control-label">อัตราเรียกเก็บ</label>
                            <div class="col-sm-3 control-label">
                                <input id="edit_fee" name="fee" class="form-control m-b-1 type_input" type="text" value="" onkeyup="format_the_number_decimal(this)">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group">
                            <label class="col-sm-4 control-label">ค่าบำรุง</label>
                            <div class="col-sm-3 control-label">
                                <input id="edit_assoc_fee" name="assoc_fee" class="form-control m-b-1 type_input" type="text" value="" onkeyup="format_the_number_decimal(this)">
                            </div>
                        </div>
                    </div>
                    <div class="form-group text-center">
                        <button type="button" class="btn btn-primary min-width-100" id="edit_submit_btn">ตกลง</button>
                        <button class="btn btn-danger min-width-100" type="button" id="edit_cancel_btn">ยกเลิก</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
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

        $("#all_add_btn").click(function() {
            var d = new Date();
            var year_be = d.getFullYear() + 543;
            $("#all_year").val(year_be);
            $('#all_add_modal').modal('show');
        });

        $("#all_cancel_btn").click(function() {
            $('#all_add_modal').modal('hide');
        });

        $("#single_add_btn").click(function() {
            var d = new Date();
            var year_be = d.getFullYear() + 543;
            $("#all_year").val(year_be);
            $('#single_add_modal').modal('show');
        });
        $("#cancel_btn").click(function() {
            $('#single_add_modal').modal('hide');
        });

        $("#submit_btn").click(function() {
            warning_message = "";

            if(warning_message != "") {
                swal('กรุณากรอกข้อมูล', warning_message, 'warning');
            } else {
                swal({
                    title: "ท่านต้องการบันทึกข้อมูลใช่หรือไม่?",
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
                            baseZ: 7000,
                            bindEvents: false
                        });
                        $.post(base_url+"sp_cremation/<?php echo $path;?>/check_save_period_fee", $("#form_single").serialize() , function(result) {
                            data = JSON.parse(result);
                            if(data.status == "no_data"){
                                $.post(base_url+"sp_cremation/<?php echo $path;?>/save_period_fee", $("#form_single").serialize() , function(result) {
                                    data = JSON.parse(result);
                                    if(data.status == "success"){
                                        location.reload();
                                        $.unblockUI();
                                    }else{
                                        $.unblockUI();
                                        swal('ทำรายการไม่ถูกต้องกรุณาลองอีกครั้ง','','warning');
                                    }
                                });
                            } else if (data.status == "success") {
                                $.unblockUI();
                                swal({
                                    title: "รอบสมัครนี้มีการบันทึกข้อมูลเรียกเก็บแล้วต้องการเปลี่ยนแปลงข้อมูล?",
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
                                            baseZ: 7000,
                                            bindEvents: false
                                        });
                                        $.post(base_url+"sp_cremation/<?php echo $path;?>/save_period_fee", $("#form_single").serialize() , function(result) {
                                            data = JSON.parse(result);
                                            if(data.status == "success"){
                                                location.reload();
                                                $.unblockUI();
                                            }else{
                                                $.unblockUI();
                                                swal('ทำรายการไม่ถูกต้องกรุณาลองอีกครั้ง','','warning');
                                            }
                                        });
                                    }
                                });
                            } else {
                                $.unblockUI();
                                swal('ทำรายการไม่ถูกต้องกรุณาลองอีกครั้ง','','warning');
                            }
                        });
                    }
                });
            }
        });

        $(".btn_edit").click(function() {
            $("#edit_period_id").val($(this).attr("data-period-id"));
            $("#edit_assoc_fee").val($(this).attr("data-assoc-fee"));
            $("#edit_fee").val($(this).attr("data-fee"));
            $("#edit_year").val($(this).attr("data-year"));
            $('#single_edit_modal').modal('show');
        });

        $("#edit_cancel_btn").click(function() {
            $('#single_edit_modal').modal('hide');
        });

        $(".btn_delete").click(function() {
            id = $(this).attr("data-id");
            swal({
                title: "ท่านต้องการลบใช่หรือไม่?",
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
                        baseZ: 7000,
                        bindEvents: false
                    });
                    $.post(base_url+"sp_cremation/<?php echo $path;?>/delete_period_fee", {id: id} , function(result) {
                        data = JSON.parse(result);
                        if(data.status == "success"){
                            location.reload();
                            $.unblockUI();
                        }else{
                            $.unblockUI();
                            swal('ทำรายการไม่ถูกต้องกรุณาลองอีกครั้ง','','warning');
                        }
                    });
                }
            });
        });

        $("#edit_submit_btn").click(function() {
            swal({
                title: "ท่านต้องการบันทึกข้อมูลใช่หรือไม่?",
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
                        baseZ: 7000,
                        bindEvents: false
                    });
                    $.post(base_url+"sp_cremation/<?php echo $path;?>/save_period_fee", $("#form_edit_single").serialize() , function(result) {
                        data = JSON.parse(result);
                        if(data.status == "success"){
                            location.reload();
                            $.unblockUI();
                        }else{
                            $.unblockUI();
                            swal('ทำรายการไม่ถูกต้องกรุณาลองอีกครั้ง','','warning');
                        }
                    });
                }
            });
        });

        $("#all_submit_btn").click(function() {
            swal({
                title: "ท่านต้องการบันทึกข้อมูลใช่หรือไม่?",
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
                        baseZ: 7000,
                        bindEvents: false
                    });
                    $.post(base_url+"sp_cremation/<?php echo $path;?>/save_period_fee", $("#form_all").serialize() , function(result) {
                        data = JSON.parse(result);
                        if(data.status == "success"){
                            location.reload();
                            $.unblockUI();
                        }else{
                            $.unblockUI();
                            swal('ทำรายการไม่ถูกต้องกรุณาลองอีกครั้ง','','warning');
                        }
                    });
                }
            });
        });

        createSelect2("single_add_modal");
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

    function createSelect2(id){
        $('.js-data-example-ajax').select2({
            dropdownParent: $("#"+id)
        });
    }
</script>