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
        <h1 style="margin-bottom: 0">ประมวลผลบัญชีแยกประเภท</h1>
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 padding-l-r-0"  style="padding-bottom:5px;">
            <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6 padding-l-r-0">
                <?php $this->load->view('breadcrumb'); ?>
            </div>
            <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 padding-l-r-0 text-right">
                <button class="btn btn-primary btn-lg" type="button" id="approve_all">
                    อนุมัติ
                </button>
                &nbsp
                <button class="btn btn-danger btn-lg" type="button" id="cancel_all">
                    ยกเลิก
                </button>
            </div>
        </div>
        <div class="row gutter-xs">
            <div class="col-xs-12 col-md-12">
                <div class="panel panel-body">
                    <div class="bs-example" data-example-id="striped-table">
                        <div class="col-xs-12 col-md-12">
                            <div class="panel panel-body" style="padding-top:0px !important;">
                                <form action="" id="form1" method="GET">
                                    <h3></h3>
                                    <div class="form-group g24-col-sm-24">
                                        <label class="g24-col-sm-6 control-label right"> วันที่ </label>
                                        <div class="g24-col-sm-4">
                                            <div class="input-with-icon">
                                                <div class="form-group">
                                                    <input id="start_date" name="start_date" class="form-control m-b-1 mydate" style="padding-left: 50px;" type="text" value="<?php echo $this->center_function->mydate2date(!empty($start_date) ? $start_date : date('Y-m-d')); ?>" data-date-language="th-th">
                                                    <span class="icon icon-calendar input-icon m-f-1"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <label class="g24-col-sm-1 control-label right"> ถึง </label>
                                        <div class="g24-col-sm-4">
                                            <div class="input-with-icon">
                                                <div class="form-group">
                                                    <input id="end_date" name="end_date" class="form-control m-b-1 mydate" style="padding-left: 50px;" type="text" value="<?php echo $this->center_function->mydate2date(!empty($end_date) ? $end_date : date('Y-m-d')); ?>" data-date-language="th-th">
                                                    <span class="icon icon-calendar input-icon m-f-1"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group g24-col-sm-24">
                                        <label class="g24-col-sm-6 control-label right"> ประเภท </label>
                                        <div class="g24-col-sm-4">
                                            <select id="journal_type" name="journal_type" class="form-control">
                                                <option value="">ทั้งหมด</option>
                                                <option value="P" <?php echo $_GET["journal_type"] == 'P' ? "selected" : "";?>>จ่าย</option>
                                                <option value="R" <?php echo $_GET["journal_type"] == 'R' ? "selected" : "";?>>รับ</option>
                                                <option value="J" <?php echo $_GET["journal_type"] == 'J' ? "selected" : "";?>>โอน</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group g24-col-sm-24">
                                        <label class="g24-col-sm-6 control-label right"> สถานะ </label>
                                        <div class="g24-col-sm-4">
                                            <select id="run_status" name="run_status" class="form-control">
                                                <option value="">ทั้งหมด</option>
                                                <option value="not_process" <?php echo $_GET["run_status"] == 'not_process' ? "selected" : "";?>>ยังไม่ผ่านรายการ</option>
                                                <option value="processed" <?php echo $_GET["run_status"] == 'processed' ? "selected" : "";?>>ผ่านรายการแล้ว</option>
                                                <option value="cancel" <?php echo $_GET["run_status"] == 'cancel' ? "selected" : "";?>>ยกเลิก</option>
                                                <option value="not_process_cancel" <?php echo $_GET["run_status"] == 'not_process_cancel' ? "selected" : "";?>>ยังไม่ผ่านรายการ + ยกเลิก</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group g24-col-sm-24">
                                        <label class="g24-col-sm-6 control-label right"></label>
                                        <div class="g24-col-sm-2">
                                            <input type="submit" class="btn btn-primary" style="width:100%" value="ค้นหา">
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <table class="table table-striped">
                            <thead>
                            <tr>
                                <th class="" width="5%">
                                    <input type="checkbox" id="check_all" value="">
                                </th>
                                <th class="">รายการ</th>
                                <th class="" width="20%">ประเภท</th>
                                <th class="" width="20%">สถานะ</th>
                                <th class="" width="20%"></th>
                            </tr>
                            </thead>
                            <tbody>
                                <form action="" id="form2" method="POST">
                                    <input type="hidden" id="do" name="do" value=""/>
                                <?php
                                    if(!empty($accounts)) {
                                        foreach($accounts as $account) {
                                ?>
                                    <tr>
                                        <td>
                                            <input type="checkbox" class="account-id" id="account_id" name="account_ids[]" value="<?php echo $account["account_id"]?>" data-status="<?php echo $account["run_status"];?>">
                                        </td>
                                        <td><?php echo $account["account_description"];?></td>
                                        <td class="text-center">
                                <?php
                                            if($account["journal_type"] == "R") {
                                                echo "รับ";
                                            } else if($account["journal_type"] == "P") {
                                                echo "จ่าย";
                                            } else if($account["journal_type"] == "J") {
                                                echo "โอน";
                                            } else {
                                                echo $account["journal_type"];
                                            }
                                ?>
                                        </td>
                                        <td>
                                <?php
                                            if ($account["run_status"] == 1) {
                                                echo "ผ่านรายการแล้ว";
                                            } else if ($account["run_status"] == 2) {
                                                echo "ยกเลิก";
                                            }
                                ?>
                                        </td>
                                        <td>
                                <?php
                                            if($account["run_status"] == 1) {
                                ?>
                                                <input type="button" class="btn btn-danger cancel-btn" id="btn_account_<?php echo $account["account_id"];?>" data-account-id="<?php echo $account["account_id"];?>" value="ยกเลิก">
                                <?php
                                            } else {
                                ?>
                                                <input type="button" class="btn btn-primary approve-btn" id="btn_account_<?php echo $account["account_id"];?>" data-account-id="<?php echo $account["account_id"];?>" value="อนุมัติ">
                                <?php
                                            }
                                ?>
                                        </td>
                                    </tr>
                                <?php
                                        }
                                    } else {
                                ?>
                                    <tr>
                                        <td colspan="5">
                                            ไม่พบข้อมูล
                                        </td>
                                    </tr>
                                <?php
                                    }
                                ?>
                                </form>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<form action="" id="form3" method="POST">
    <input type="hidden" id="h-do" name="do" value="">
    <input type="hidden" id="h-account_id" name="account_ids[]" value="">
</form>
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

        $("#check_all").change(function() {
            isCheck = $(this).is(':checked');
            if(isCheck) {
                $(".account-id").prop("checked", true);
            } else {
                $(".account-id").prop("checked", false);
            }
        });
        $("#approve_all").click(function() {
            $(this).prop('disabled', true);
            i = 0;
            $(".account-id").each(function(index) {
                if($(this).attr("data-status") == 1) {
                    i++;
                }
            });
            if(i > 0) {
                swal('ไม่สามารถทำรายการอนุมัติรายการที่อนุมัติแล้วได้','','warning');
                $(this).prop('disabled', false);
            } else {
                $("#do").val("approve");
                $("#form2").submit();
            }
        });
        $("#cancel_all").click(function() {
            $(this).prop('disabled', true);
            i = 0;
            $(".account-id").each(function(index) {
                if($(this).attr("data-status") != 1) {
                    i++;
                }
            });
            if(i > 0) {
                swal('สามารถยกเลิกได้เฉพาะรายการที่อนุมัติแล้วเท่านั้น','','warning');
                $(this).prop('disabled', false);
            } else {
                $("#do").val("cancel");
                $("#form2").submit();
            }
        });
        $(".cancel-btn").click(function() {
            $(this).prop('disabled', true);
            $("#h-do").val("cancel");
            $("#h-account_id").val($(this).attr("data-account-id"));
            $("#form3").submit();
        });
        $(".approve-btn").click(function() {
            $(this).prop('disabled', true);
            $("#h-do").val("approve");
            $("#h-account_id").val($(this).attr("data-account-id"));
            $("#form3").submit();
        });
    });
</script>
