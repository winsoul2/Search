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
            .top-btn {
                margin-bottom: 1em;
            }
        </style>
        <h1 style="margin-bottom: 0">อนุมัติการสมัคร</h1>
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 padding-l-r-0">
            <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6 padding-l-r-0">
                <?php $this->load->view('breadcrumb'); ?>
            </div>
            <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 padding-l-r-0 text-right">
                <button class="btn btn-primary btn-lg btn-add top-btn" type="button" id="approve_all">
                    อนุมัติ
                </button>
                <button class="btn btn-danger btn-lg btn-delete top-btn" type="button" id="cancel_all">
                    ไม่อนุมัติ
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
                                    <th class="font-normal" width="5%"><input type="checkbox" id="chk_all" value="all"></th>
                                    <th class="font-normal" width="10%">เลขที่คำร้อง</th>
                                    <th class="font-normal" width="10%">วันที่ยื่นคำร้อง</th>
                                    <th class="font-normal" width="10%">รหัสสมาชิก</th>
                                    <th class="font-normal" width="15%">ชื่อ-นามสกุล</th>
                                    <th class="font-normal" width="15%"> ชื่อรอบ </th>
                                    <th class="font-normal" width="10%"> ค่าบำรุงรายปี </th>
                                    <th class="font-normal" width="5%"> ค่าสมัคร </th>
                                    <th class="font-normal"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <form action="" method="post" id="form1">
                                <?php
                                    foreach($datas as $request) {
                                ?>
                                    <tr>
                                        <td class="text-center">
                                        <?php
                                            if($request["register_status"] == 2) {
                                        ?>
                                            <input type="checkbox" id="chk_<?php echo $request["id"];?>" name="register_ids[]" class="chk_register" value="<?php echo $request["id"];?>">
                                        <?php
                                            }
                                        ?>
                                        </td>
                                        <td class="text-center"><?php echo $request["request_id"]?></td>
                                        <td class="text-center"><?php echo $this->center_function->ConvertToThaiDate($request["request_date"],'1','0'); ?></td>
                                        <td class="text-center"><?php echo $request["member_id"]; ?></td>
                                        <td class="text-left"><?php echo $request["prename_full"].$request["firstname_th"]." ".$request["lastname_th"];?></td>
                                        <td class="text-left"><?php echo $request["period_name"];?></td>
                                        <td class="text-right"><?php echo number_format($request["annual_fee"],2);?></td>
                                        <td class="text-right"><?php echo number_format($request["fee"],2);?></td>
                                        
                                        <?php
                                            if($request["register_status"] == 2) {
                                        ?>
                                        <td class="text-center">
                                            <input type="button" class="btn btn-danger btn_delete" data-id="<?php echo $request["id"];?>" id="del_btn_<?php echo $request["id"]?>" value="ไม่อนุมัติ"/>
                                            <input type="button" class="btn btn-primary btn_edit" data-id="<?php echo $request["id"];?>" id="edit_btn_<?php echo $request["id"]?>" value="อนุมัติ"/>
                                        </td>
                                        <?php
                                            } else if ($request["register_status"] == 3) {
                                        ?>
                                        <td class="text-center">อนุมัติ</td>
                                        <?php
                                            } else if ($request["register_status"] == 4) {
                                        ?>
                                        <td class="text-center">ไม่อนุมัติ</td>
                                        <?php
                                            }
                                        ?>
                                        
                                    </tr>
                                <?php
                                    }
                                ?>
                                </form>
                            </tbody>
                        </table>
                    </div>
                </div>
                <?php echo @$paging ?>
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

        $("#approve_all").click(function() {
            id = $(this).attr("data-id");
            swal({
                title: "ยืนยันการทำรายการอนุมัติการสมัคร",
                text: "",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: '#DD6B55',
                confirmButtonText: 'ยืนยัน',
                cancelButtonText: "ยกเลิก",
                closeOnConfirm: false,
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
                    $.post(base_url+"sp_cremation/<?php echo $path;?>/approve_registers", $("#form1").serialize() , function(result) {
                        if(result == "success") {
                            location.reload();
                        } else {
                            $.unblockUI();
                            swal('ทำรายการไม่ถูกต้องกรุณาลองอีกครั้ง', '', 'warning');
                        }
                    }).fail(function(response) {
                        $.unblockUI();
                        console.log(response);
                        swal('ทำรายการไม่ถูกต้องกรุณาลองอีกครั้ง', '', 'warning');
                    });
                }
            });
        });

        $("#cancel_all").click(function() {
            id = $(this).attr("data-id");
            swal({
                title: "ยืนยันการทำรายการอนุมัติการสมัคร",
                text: "",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: '#DD6B55',
                confirmButtonText: 'ยืนยัน',
                cancelButtonText: "ยกเลิก",
                closeOnConfirm: false,
                closeOnCancel: true
            },
            function(isConfirm) {
                if (isConfirm) {
                    $.post(base_url+"sp_cremation/<?php echo $path;?>/disapprove_registers", $("#form1").serialize() , function(result) {
                        if(result == "success") {
                            location.reload();
                        } else {
                            swal('ทำรายการไม่ถูกต้องกรุณาลองอีกครั้ง', '', 'warning');
                        }
                    }).fail(function(response) {
                        $.unblockUI();
                        console.log(response);
                        swal('ทำรายการไม่ถูกต้องกรุณาลองอีกครั้ง', '', 'warning');
                    });
                }
            });
        });

        $(".btn_delete").click(function() {
            id = $(this).attr("data-id");
            swal({
                title: "ยืนยันการทำรายการไม่อนุมัติการสมัคร",
                text: "",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: '#DD6B55',
                confirmButtonText: 'ยืนยัน',
                cancelButtonText: "ยกเลิก",
                closeOnConfirm: false,
                closeOnCancel: true
            },
            function(isConfirm) {
                if (isConfirm) {
                    $.post(base_url+"sp_cremation/<?php echo $path;?>/disapprove_registers", {'register_ids[]':[id]} , function(result) {
                        if(result == "success") {
                            location.reload();
                        } else {
                            swal('ทำรายการไม่ถูกต้องกรุณาลองอีกครั้ง', '', 'warning');
                        }
                    }).fail(function(response) {
                        $.unblockUI();
                        console.log(response);
                        swal('ทำรายการไม่ถูกต้องกรุณาลองอีกครั้ง', '', 'warning');
                    });
                }
            });
        });

        $(".btn_edit").click(function() {
            id = $(this).attr("data-id");
            swal({
                title: "ยืนยันการทำรายการอนุมัติการสมัคร",
                text: "",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: '#DD6B55',
                confirmButtonText: 'ยืนยัน',
                cancelButtonText: "ยกเลิก",
                closeOnConfirm: false,
                closeOnCancel: true
            },
            function(isConfirm) {
                if (isConfirm) {
                    $.post(base_url+"sp_cremation/<?php echo $path;?>/approve_registers", {'register_ids[]':[id]} , function(result) {
                        if(result == "success") {
                            location.reload();
                        } else {
                            swal('ทำรายการไม่ถูกต้องกรุณาลองอีกครั้ง', '', 'warning');
                        }
                    }).fail(function(response) {
                        $.unblockUI();
                        console.log(response);
                        swal('ทำรายการไม่ถูกต้องกรุณาลองอีกครั้ง', '', 'warning');
                    });
                }
            });
        });

        $("#chk_all").change(function() {
            var status = $(this).is(":checked") ? true : false;
            $(".chk_register").prop("checked",status);
        });

        $(".chk_register").change(function() {
            var status = $(this).is(":checked") ? true : false;
            if(!status) $("#chk_all").prop("checked",false);
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