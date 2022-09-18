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
        </style>

        <style type="text/css">
          .form-group{
            margin-bottom: 5px;
          }
        </style>
        <h1 style="margin-bottom: 0">ตั้งค่าบอร์ดบริหาร</h1>

        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 " style="padding-right:0px;padding-left:0px">
            <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 " style="padding-right:0px;padding-left:0px">
                <?php $this->load->view('breadcrumb'); ?>
            </div>
            <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 " style="padding-right:0px;text-align:right;">
                <button class="btn btn-primary btn-lg bt-add" type="button" id="add_btn"><span class="icon icon-plus-circle"></span> เพิ่ม</button> 
            </div>
        </div>
        <div class="row gutter-xs">
            <div class="col-xs-12 col-md-12">
                <div class="panel panel-body" style="padding-top:0px !important;">
                        <div class="g24-col-sm-24 m-t-1 hidden_table" id="table_1">
                        <div class="bs-example" data-example-id="striped-table">
                            <table class="table table-bordered table-striped table-center">
                                <thead> 
                                    <tr class="bg-primary">
                                        <th>#</th>
                                        <th>ผู้จัดการ</th>
                                        <th>ผู้ช่วยผู้จัดการ</th>
                                        <th>วันที่เริ่มใช้งาน</th>
                                        <th></th>
                                    </tr> 
                                </thead>
                                <tbody>
                                <?php  
                                    if(!empty($datas)){
                                        foreach(@$datas as $key => $data){ 
                                ?>
                                    <tr> 
                                        <td><?php echo $i++; ?></td>
                                        <td style="text-align:left;"><?php echo $data['manager']; ?></td>
                                        <td style="text-align:left;"><?php echo $data['vice_manager']; ?></td>
                                        <td style="text-align:left;"><?php echo $this->center_function->ConvertToThaiDate($data['start_at'],false,false); ?></td>
                                        <td>
                                            <a title="แก้ไข" style="cursor:pointer;padding-left:2px;padding-right:2px" data_id="<?php echo $data['id']?>" id="edit_<?php echo $data['id']?>" class="edit_btn"><span style="cursor: pointer;" class="icon icon-edit"></span>
                                            </a>
                                            |
                                            <a title="ลบ" style="cursor:pointer;padding-left:2px;padding-right:2px" data_id="<?php echo $data['id']?>" id="delete_<?php echo $data['id']?>" class="delete_btn"><span style="cursor: pointer;" class="icon icon-trash-o"></span>
                                            </a>
                                        </td> 
                                    </tr>
                                <?php 
                                        }
                                    } 
                                ?>
                                </tbody> 
                            </table> 
                        </div>
                        <?php echo @$paging ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="edit_modal" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 id="edit_modal_title" class="modal-title"></h4>
            </div>
            <div class="modal-body">
                <input id="edit_id" name="id" type="hidden" value=""/>
                <div class="row">
                    <div class="form-group">
                        <label class="col-sm-5 control-label">วันที่เริ่มใช้งาน</label>
                        <div class="col-sm-3">
                            <div class="input-with-icon">
                                <div class="form-group">
                                    <input id="edit_start_at" name="start_at" class="form-control m-b-1 mydate" type="text" value="<?php echo $this->center_function->mydate2date(date('Y-m-d')); ?>" data-date-language="th-th" style="padding-left:38px;">
                                    <span class="icon icon-calendar input-icon m-f-1"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group">
                        <label class="col-sm-5 control-label">ผู้จัดการ</label>
                        <div class="col-sm-3">
                            <div class="input-with-icon">
                                <div class="form-group">
                                    <input id="edit_manager" name="manager" class="form-control m-b-1" type="text" value="">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group">
                        <label class="col-sm-5 control-label">ผู้ช่วยผู้จัดการ</label>
                        <div class="col-sm-3">
                            <div class="input-with-icon">
                                <div class="form-group">
                                    <input id="edit_vice_manager" name="vice_manager" class="form-control m-b-1" type="text" value="">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group">
                        <label class="col-sm-5 control-label">กรรมการ</label>
                        <div class="col-sm-3">
                            <div class="input-with-icon">
                                <div class="form-group">
                                    <input id="edit_board_1" name="board[]" class="form-control m-b-1" type="text" value="">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group">
                        <label class="col-sm-5 control-label">กรรมการ</label>
                        <div class="col-sm-3">
                            <div class="input-with-icon">
                                <div class="form-group">
                                    <input id="edit_board_2" name="board[]" class="form-control m-b-1" type="text" value="">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group">
                        <label class="col-sm-5 control-label">กรรมการ</label>
                        <div class="col-sm-3">
                            <div class="input-with-icon">
                                <div class="form-group">
                                    <input id="edit_board_3" name="board[]" class="form-control m-b-1" type="text" value="">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group">
                        <label class="col-sm-5 control-label">กรรมการ</label>
                        <div class="col-sm-3">
                            <div class="input-with-icon">
                                <div class="form-group">
                                    <input id="edit_board_4" name="board[]" class="form-control m-b-1" type="text" value="">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group">
                        <label class="col-sm-5 control-label">กรรมการ</label>
                        <div class="col-sm-3">
                            <div class="input-with-icon">
                                <div class="form-group">
                                    <input id="edit_board_5" name="board[]" class="form-control m-b-1" type="text" value="">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer text-center">
                <button type="button" id="submit_btn" class="btn btn-primary">บันทึก</button>
                <button type="button" id="close" class="btn btn-default" data-dismiss="modal">ปิดหน้าต่าง</button>
            </div>
        </div>
    </div>
</div>

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

    $(".edit_btn").click(function() {
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
            baseZ: 100000,
            bindEvents: false
        });
        $("#edit_modal_title").html("แก้ไข");
        id = $(this).attr("data_id");
        $.ajax({
            url: base_url + "setting_basic_data/ajax_get_board_data_by_id?id="+id,
            method: "get",
            success: function (response) {
                var data = JSON.parse(response);
                $("#edit_start_at").val(data.start_at);
                $("#edit_id").val(id);
                $("#edit_manager").val(data.manager);
                $("#edit_vice_manager").val(data.vice_manager);
                if(data.boards) {
                    for(i=0; i<data.boards.length; i++) {
                        board = data.boards[i];
                        $("#edit_board_"+(i+1)).val(board)
                    }
                    $("#edit_modal").modal("show");
                    $.unblockUI();
                }
            },
            error: function (xhr) {
                console.log('Request Status: ' + xhr.status + ' Status Text: ' + xhr.statusText + ' ' + xhr.responseText);
            }
        });

    })

    $("#add_btn").click(function() {
        $("#edit_modal_title").html("เพิ่ม");
        $("#edit_start_at").val('<?php echo $this->center_function->mydate2date(date('Y-m-d')); ?>');
        $("#edit_id").val('');
        $("#edit_manager").val('');
        $("#edit_vice_manager").val('');
        $("#edit_board_1").val('');
        $("#edit_board_2").val('');
        $("#edit_board_3").val('');
        $("#edit_board_4").val('');
        $("#edit_board_5").val('');
        $("#edit_modal").modal("show");
    });

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
                    baseZ: 100000,
                    bindEvents: false
                });
                var boards = [];
                boards.push($("#edit_board_1").val());
                boards.push($("#edit_board_2").val());
                boards.push($("#edit_board_3").val());
                boards.push($("#edit_board_4").val());
                boards.push($("#edit_board_5").val());
                $.ajax({
                    url: base_url + "setting_basic_data/ajax_save_boards",
                    method: "post",
                    data: {
                        id: $("#edit_id").val(),
                        start_at: $("#edit_start_at").val(),
                        manager: $("#edit_manager").val(),
                        vice_manager: $("#edit_vice_manager").val(),
                        boards: boards,
                    },
                    dataType: "text",
                    success: function (response) {
                        if(response == 'success') {
                            location.href = base_url+"setting_basic_data/setting_boards";
                        }
                        $.unblockUI();
                    },
                    error: function (xhr) {
                        console.log('Request Status: ' + xhr.status + ' Status Text: ' + xhr.statusText + ' ' + xhr.responseText);
                    }
                });
            }
        });
    });
});
</script>
