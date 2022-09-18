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
        <h1 style="margin-bottom: 0">ชำระเงินค่าสมัคร</h1>
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 padding-l-r-0">
            <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6 padding-l-r-0">
                <?php $this->load->view('breadcrumb'); ?>
            </div>
            <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 padding-l-r-0 text-right">
            </div>
        </div>

        <div class="row gutter-xs">
            <div class="col-xs-12 col-md-12">
                <div class="panel panel-body">
                    <div class="bs-example" data-example-id="striped-table">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th class="font-normal">เลขที่คำร้อง</th>
                                    <th class="font-normal">วันที่ยื่นคำร้อง</th>
                                    <th class="font-normal">รหัสสมาชิก</th>
                                    <th class="font-normal">ชื่อ-นามสกุล</th>
                                    <th class="font-normal"> ชื่อรอบ </th>
                                    <th class="font-normal"> ค่าบำรุงรายปี </th>
                                    <th class="font-normal"> ค่าสมัคร </th>
                                    <th class="font-normal"> ค่าบำรุงสมาคม </th>
                                    <th class="font-normal"> ค่าใช้จ่ายอื่นๆ </th>
                                    <th class="font-normal"></th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php
                                foreach($datas as $request) {
                            ?>
                                <tr>
                                    <form action="<?php echo base_url(PROJECTPATH.'/sp_cremation/'.$path); ?>" method="post" id="form_register_<?php echo $request["id"];?>" target="_blank">
                                        <input type="hidden" name="register_id" value="<?php echo $request["id"];?>"/>
                                        <td class="text-center"><a id="request_btn_<?php echo $request["id"];?>" class="request_btn" data-id="<?php echo $request["id"];?>" href="#"><?php echo $request["request_id"]?></a></td>
                                    </form>
                                    <td class="text-center"><?php echo $this->center_function->ConvertToThaiDate($request["request_date"],'1','0'); ?></td>
                                    <td class="text-center"><?php echo $request["member_id"]; ?></td>
                                    <td class="text-left"><?php echo $request["prename_full"].$request["firstname_th"]." ".$request["lastname_th"];?></td>
                                    <td class="text-left"><?php echo $request["period_name"];?></td>
                                    <td class="text-right"><?php echo number_format($request["annual_fee"],2);?></td>
                                    <td class="text-right"><?php echo number_format($request["fee"],2);?></td>
                                    <td class="text-right"><?php echo number_format($request["assoc_fee"],2);?></td>
                                    <td class="text-right"><?php echo number_format($request["other_fee"],2);?></td>
                                    
                                    <?php
                                        if(!empty($request["receipt_id"])) {
                                    ?>
                                        <td class="text-center">
                                            <a id="receipt_btn_<?php echo $request["id"];?>" class="receipt_btn" target="_blank" href="<?php echo base_url(PROJECTPATH.'/sp_cremation/'.$path."/receipt?receipt_id=".$request["receipt_id"]); ?>"><?php echo $request["receipt_no"]?></a>
                                        </td>
                                    <?php
                                        } else {
                                    ?>
                                        <td class="text-center">
                                            <input type="button" class="btn btn-primary btn_edit" data-id="<?php echo $request["id"];?>" id="edit_btn_<?php echo $request["id"]?>" data-annual-fee="<?php echo $request["annual_fee"];?>"
                                                    data-fee="<?php echo $request["fee"];?>" data-period-name="<?php echo $request["period_name"];?>" data-assoc-fee="<?php echo $request["assoc_fee"]?>" data-other-fee="<?php echo $request["other_fee"]?>"
                                                    data-name="<?php echo $request["prename_full"].$request["firstname_th"]." ".$request["lastname_th"];?>" value="ดำเนินงาน"/>
                                        </td>
                                    <?php
                                        }
                                    ?>
                                   
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
                <form action="<?php echo base_url(PROJECTPATH.'/sp_cremation/'.$path.'/pay_register_fee/'); ?>" method="post" id="form1">
                    <input id="register_id" name="register_id" type="hidden" class="type_input" value="">
                    <div class="row">
                        <div class="form-group">
                            <label class="col-sm-4 control-label">ชื่อ-นามสกุล</label>
                            <div class="col-sm-3">
                                <input id="member_name" class="form-control m-b-1 type_input" type="text" value="" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group">
                            <label class="col-sm-4 control-label">ชื่อรอบ</label>
                            <div class="col-sm-6">
                                <div class="input-with-icon">
                                    <div class="form-group">
                                        <input id="period_name" class="form-control m-b-1 type_input" type="text" value="" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group req-date-div">
                            <input id="request_date" name="request_date" class="form-control m-b-1 mydate" style="padding-left: 50px;" type="text" value="" data-date-language="th-th">
                            <span class="icon icon-calendar input-icon m-f-1"></span>
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

            if($("#annual_fee").val() == "") {
                warning_message += " - ค่าบำรุงรายปี\n";
            }
            if($("#fee").val() == "") {
                warning_message += " - ค่าสมัคร\n";
            }
            if($("#assoc_fee").val() == "") {
                warning_message += " - ค่าบำรุงสมาคม\n";
            }
            if($("#other_fee").val() == "") {
                warning_message += " - ค่าใช้จ่ายอื่นๆ\n";
            }
            if(warning_message != "") {
                swal('กรุณากรอกข้อมูล', warning_message, 'warning');
            } else {
                $.post(base_url+"sp_cremation/<?php echo $path;?>/pay_register_fee/", $("#form1").serialize() , function(result) {
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

        $(".btn_edit").click(function() {
            $("#register_id").val($(this).attr("data-id"));
            $("#period_name").val($(this).attr("data-period-name"));
            $("#member_name").val($(this).attr("data-name"));
            $("#fee").val($(this).attr("data-fee"));
            $("#annual_fee").val($(this).attr("data-annual-fee"));
            $("#assoc_fee").val($(this).attr("data-assoc-fee"));
            $("#other_fee").val($(this).attr("data-other-fee"));
            $('#add_madal').modal('show');
        });

        $("#chk_all").change(function() {
            var status = $(this).is(":checked") ? true : false;
            $(".chk_register").prop("checked",status);
        });

        $(".chk_register").change(function() {
            var status = $(this).is(":checked") ? true : false;
            if(!status) $("#chk_all").prop("checked",false);
        });

        $(".request_btn").click(function() {
            id = $(this).attr("data-id");
            $("#form_register_"+id).submit();
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