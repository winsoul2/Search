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
        <h1 style="margin-bottom: 0">ตั้งค่าส่วนแบ่งเงินฌาปนกิจ</h1>
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 padding-l-r-0">
            <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6 padding-l-r-0">
                <?php $this->load->view('breadcrumb'); ?>
            </div>
            <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 padding-l-r-0">
            </div>
        </div>
        <div class="row gutter-xs">
            <div class="col-xs-12 col-md-12">
                <div class="panel panel-body">
                    <div class="bs-example" data-example-id="striped-table">
                        <form action="" method="post" id="form1">
                            <?php
                                foreach($settings as $setting) {
                            ?>
                            <div class="row">
                                <label class="col-sm-4 control-label"><?php echo $setting["description"]?></label>
                                <div class="col-sm-1">
                                    <div class="form-group">
                                        <input id="<?php echo $setting["code"]?>" name="data[<?php echo $setting["code"]?>]" data-desc="<?php echo $setting["description"]?>" class="form-control num_input" style="text-align:left;" type="number" value="<?php echo $setting["value"]?>"/>
                                    </div>
                                </div>
                                <label class="col-sm-5 control-label text-left">%</label>
                            </div>
                            <?php
                                }
                            ?>
                            <div class="row form-group">
                                <label class="col-sm-4 control-label"></label>
                                <div class="col-sm-6 text-left">
                                    <button type="button" class="btn btn-primary min-width-100" id="submit_btn">บันทึก</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <?php echo @$paging ?>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        $("#submit_btn").click(function() {
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
                    warning_message = "";
                    percent_total = 0;
                    $(".num_input").each(function(index) {
                        percent_total += $(this).val();
                    });
                    if(percent_total > 100) {
                        wraning_message += " - ผลรวมไม่สามารถเกิน 100% ได้\n";
                    }
                    if(warning_message != "") {
                        swal('ไม่สามารถทำรายการได้', warning_message, 'warning');
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
                        $.ajax({
                            url: base_url+"sp_cremation/<?php echo $path;?>/save_request_money_payment_setting",
                            method:"post",
                            data: $("#form1").serialize(),
                            dataType:"text",
                            success:function(result) {
                                data = JSON.parse(result);
                                if(data.status == "success"){
                                    location.reload();
                                } else {
                                    $.unblockUI();
                                    swal('ไม่สามารถทำรายการได้',data.message,'warning'); 
                                }
                            },
                            error: function(xhr){
                                $.unblockUI();
                                console.log('Request Status: ' + xhr.status + ' Status Text: ' + xhr.statusText + ' ' + xhr.responseText);
                            }
                        });
                    }
                }
            });
        });

        $(".num_input").change(function() {
            if($(this).val() == '') {
                $(this).val(0);
            }
        });

    });

</script>