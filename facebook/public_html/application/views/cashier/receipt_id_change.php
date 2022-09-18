<div class="layout-content">
    <div class="layout-content-body">
        <style>
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
            th {
                text-align: center;
            }
            .modal-dialog-cal {
                width:80% !important;
                margin:auto;
                margin-top:1%;
                margin-bottom:1%;
            }
            .modal-dialog-search {
                width: 700px;
            }
        </style>
        <h1 style="margin-bottom: 0">เปลี่ยนแปลงเลขที่ใบเสร็จ</h1>
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 padding-l-r-0">
            <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 padding-l-r-0">
                <?php $this->load->view('breadcrumb'); ?>
            </div>
            <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 padding-l-r-0">

            </div>

        </div>
        <div class="row gutter-xs">
            <div class="col-xs-12 col-md-12">
                <div class="panel panel-body" style="padding-top:0px !important;">
                    <h3></h3>
                    <form action="" method="POST">
                        <div class="g24-col-sm-24 m-t-1">
                            <div class="form-group">
                                <label class="g24-col-sm-8 control-label"> เลขที่ใบเสร็จเดิม </label>
                                <div class="g24-col-sm-3">
                                    <input class="form-control" id="receipt_id" name="receipt_id" type="text" value="">
                                </div>
                            </div>
                            <div class="g24-col-sm-2">
                                <input type="button" id="receipt_id_check" class="btn btn-primary" value="ตรวจสอบ">
                            </div>
                        </div>
                        <div class="g24-col-sm-24 m-t-1">
                            <div class="form-group">
                                <label class="g24-col-sm-8 control-label"> เลขที่ใบเสร็จที่ต้องการเปลี่ยนให้เป็น </label>
                                <div class="g24-col-sm-3">
                                    <input class="form-control" id="new_receipt_id" name="new_receipt_id" type="text" value="">
                                </div>
                                <div class="g24-col-sm-2">
                                    <input type="button" id="new_receipt_id_check" class="btn btn-primary" value="ตรวจสอบ">
                                </div>
                            </div>
                        </div>
                        <div class="g24-col-sm-24 m-t-1">
                            <div class="form-group">
                                <label class="g24-col-sm-8 control-label"></label>
                                <div class="g24-col-sm-2">
                                    <input type="button" id="submit_btn" class="btn btn-primary" value="ดำเนินการ">
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $("#receipt_id_check").click(function() {
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
                baseZ: 6000,
                bindEvents: false
            });
            $.ajax({
                url: base_url+"cashier/check_receipt_id_json",
                method:"post",
                data: {
                    receipt_id : $('#receipt_id').val()
                },
                dataType:"json",
                success:function(result) {
                    data = JSON.parse(JSON.stringify(result));
                    $.unblockUI();
                    if(data.receipt_id == null) {
                        swal("ไม่พบเลขที่ใบเสร็จ");
                    } else {
                        swal({
                            title: "พบใบเสร็จมีสถานะ"+data.status_name,
                            text: 'ต้องการดูใบเสร็จ',
                            type: "success",
                            showCancelButton: true,
                            confirmButtonColor: '#DD6B55',
                            confirmButtonText: 'ยืนยัน',
                            cancelButtonText: "ยกเลิก",
                            closeOnConfirm: true,
                            closeOnCancel: true
                        },
                        function (isConfirm) {
                            if (isConfirm) {
                                window.open(base_url+"/admin/receipt_form_pdf/"+data.encode_receipt_id, '_blank');
                            }
                        });
                    }
                }
            });
        });
        $("#new_receipt_id_check").click(function() {
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
                baseZ: 6000,
                bindEvents: false
            });
            $.ajax({
                url: base_url+"cashier/check_receipt_id_with_coop_buy_json",
                method:"post",
                data: {
                    receipt_id : $('#new_receipt_id').val()
                },
                dataType:"json",
                success:function(result) {
                    data = JSON.parse(JSON.stringify(result));
                    $.unblockUI();
                    if(data.receipt_id == null) {
                        swal("ไม่พบเลขที่ใบเสร็จ");
                    } else {
                        swal({
                            title: "พบใบเสร็จมีสถานะ"+data.status_name,
                            text: 'ต้องการดูใบเสร็จ',
                            type: "warning",
                            showCancelButton: true,
                            confirmButtonColor: '#DD6B55',
                            confirmButtonText: 'ยืนยัน',
                            cancelButtonText: "ยกเลิก",
                            closeOnConfirm: true,
                            closeOnCancel: true
                        },
                        function (isConfirm) {
                            if (isConfirm) {
                                if(data.receipt_route == 'account_buy_number'){
                                    window.open(base_url+"/coop_buy/coop_buy_pdf?account_buy_id="+data.encode_receipt_id, '_blank');
                                }
                                else if(data.receipt_route == 'receipt') {
                                    window.open(base_url+"/admin/receipt_form_pdf/"+data.encode_receipt_id, '_blank');
                                }
                                else{
                                    window.open(base_url+"/admin/receipt_form_pdf/"+data.encode_receipt_id, '_blank');
                                }
                            }
                        });
                    }
                }
            });
        });
        $("#submit_btn").click(function() {
            if(!$('#new_receipt_id').val()) {
                swal('ไม่สามารถทำรายการได้เนื่องจาก', "เลขที่ใบเสร็จที่ต้องการเปลี่ยนให้เป็นไม่สามารถเป็นค่าว่างได้", 'warning');
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
                    baseZ: 6000,
                    bindEvents: false
                });
                $.ajax({
                    url: base_url+"cashier/change_receipt_id_json",
                    method:"post",
                    data: {
                        receipt_id : $('#receipt_id').val(),
                        new_receipt_id : $('#new_receipt_id').val()
                    },
                    dataType:"json",
                    success:function(result) {
                        data = JSON.parse(JSON.stringify(result));
                        $.unblockUI();
                        if(data.status == 1) {
                            swal({
                                title: "ทำรายการสำเร็จ",
                                text: 'ต้องการดูใบเสร็จ',
                                type: "success",
                                showCancelButton: true,
                                confirmButtonColor: '#DD6B55',
                                confirmButtonText: 'ใช่',
                                cancelButtonText: "ปิดหน้าต่าง",
                                closeOnConfirm: true,
                                closeOnCancel: true
                            },
                            function (isConfirm) {
                                if (isConfirm) {
                                    window.open(base_url+"/admin/receipt_form_pdf/"+data.encode_receipt_id, '_blank');
                                    $('#receipt_id').val("")
                                    $('#new_receipt_id').val("")
                                } else {
                                    $('#receipt_id').val("")
                                    $('#new_receipt_id').val("")
                                }
                            });
                        } else {
                            swal('ไม่สามารถทำรายการได้เนื่องจาก', data.message, 'warning');
                        }
                    }
                });
            }
        });
    });
</script>
