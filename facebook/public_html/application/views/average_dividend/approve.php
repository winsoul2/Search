<div class="layout-content">
    <div class="layout-content-body">
        <style>
            .modal-header-alert {
                padding: 9px 15px;
                border: 1px solid #FF0033;
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

            .left {
                text-align: left;
            }

            .modal-dialog-account {
                margin: auto;
                margin-top: 7%;
            }

            .modal-dialog-data {
                width: 50% !important;
                margin: auto;
                margin-top: 5%;
                margin-bottom: 1%;
            }

            .modal_data_input {
                margin-bottom: 5px;
            }

            .form-group {
                margin-bottom: 5px;
            }

            .control-label {
                text-align: right;
                margin-top: 6px;
            }
        </style>
        <h1 class="title_top">อนุมัติปันผลเฉลี่ยคืน</h1>
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 padding-l-r-0">
            <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 padding-l-r-0">
                <?php $this->load->view('breadcrumb'); ?>
            </div>
        </div>
        <div class="row gutter-xs">
            <div class="col-xs-12 col-md-12">
                <div class="panel panel-body">
                    <div class="bs-example" data-example-id="striped-table">
                        <table class="table table-bordered table-striped table-center">
                            <thead>
                            <tr class="bg-primary">
                                <th>ปี</th>
                                <th width="18%">ปันผล</th>
                                <th width="18%">เฉลี่ยคืน</th>
                                <th width="18%">เงินของขวัญ</th>
                                <th width="18%">สถานะ</th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php

                            $i = 1;
                            $status = array('รอการตรวจสอบ', 'อนุมัติ', 'ไม่อนุมัติ');
                            foreach ($data as $key => $row) {

                                $date = explode('-',explode(' ', $row['approve_date'])[0]);
                                $year = $date[0] + 543;
                                $month = $month_short_arr[intval($date[1])];
                                $day = $date[2];

                                $str_date = $day." ".$month." ".$year;

                                ?>

                                <tr id="master-id-<?php echo $row['id']; ?>">
                                    <td><?php echo $row['year']; ?></td>
                                    <td><?php echo $row['dividend_percent'] . "%"; ?></td>
                                    <td><?php echo $row['average_percent'] . "%"; ?></td>
                                    <td><?php echo number_format($row['gift_varchar'], '2', '.', ","); ?></td>
                                    <td><?php echo $status[$row['status']]; ?> <?php echo $row['status'] == '1' ? 'เมื่อ '.$str_date : ''; ?></td>
                                    <td>
                                        <?php if ($row['status'] == '0') { ?>
                                            <a href="<?php echo base_url(PROJECTPATH . "/average_dividend/average_dividend_excel?master_id=" . $row['id'] . "&year=" . $row['year']); ?>">ดูรายงาน </a>
                                            <!--                                |<a href="--><?php //echo base_url(PROJECTPATH.'/average_dividend/approve?id='.$row['id'].'&status_to=1'); ?><!--"> อนุมัติ </a>-->
                                            <!--                                |<a href="--><?php //echo base_url(PROJECTPATH.'/average_dividend/approve?id='.$row['id'].'&status_to=2'); ?><!--" style="color:red"> ไม่อนุมัติ</a>                     -->
                                            |<a title="อนุมัติ" onclick="approveConfirm(<?php echo $row['id']; ?>)" style="cursor: pointer"> อนุมัติ </a>
                                            |<a title="ไม่อนุมัติ" onclick="cancelConfirm(<?php echo $row['id']; ?>)" style="color:red; cursor: pointer;"> ไม่อนุมัติ</a>
                                        <?php } else if($row['status'] == '2') { ?>
                                            <a onclick="deleteMaster('<?php echo $row['id']; ?>')" style="color: red; cursor: pointer"> ลบ </a>
                                        <?php } else { ?>
                                            <a href="<?php echo base_url(PROJECTPATH . "/average_dividend/average_dividend_excel?master_id=" . $row['id'] . "&year=" . $row['year']); ?>">ดูรายงาน </a>
                                        <?php } ?>
                                    </td>
                                </tr>
                            <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>

    function deleteMaster(master) {
        swal({
            title: "ยืนยันการลบข้อมูล",
            text: "ท่านกำลังลบข้อมูลนี้แน่ใจหรือไม่",
            type: "warning",
            showCancelButton: true,
            confirmButtonText: "ยืนยัน",
            closeOnConfirm: false,
            showLoaderOnConfirm: true
        }, function () {
            $.post("/average_dividend/delete", {master_id : master}, function(res){
                if(res.status === true) {
                    swal("ข้อมูลของท่านถูกลบแล้ว! ");
                    setTimeout(function(){
                        location.reload();
                    }, 1500);
                }else{
                    swal("ไม่สามารถลบข้อมูลได้!", "เนื่องจากเกิดข้อผิดพลาดบางอย่าง", "error");
                }
            });
        });
    }

    function approveConfirm(master) {
        swal({
            title: "ยืนยันการอนุมัติปันผลเฉลี่ยคืน",
            showCancelButton: true,
            confirmButtonText: "ยืนยัน",
            closeOnConfirm: false,
            closeOnCancel: false,
            showLoaderOnConfirm: true
        }, function (isConfirm) {
            if (isConfirm) {
                $.post('/average_dividend/status_to',{ id : master, status_to: 1}, function(res){
                    if(res.status) {
                        swal("ยืนยันการอนุมัติปันผลเฉลี่ยคืนแล้้ว");
                        setTimeout(function(){
                            location.reload();
                        }, 1500);
                    }else{
                        if(typeof res.msg !== 'undefined' && res.msg === 'padding') {
                            swal("กรุณารอสักครู่...", "กำลังประมวลผล");
                        }else{
                            swal("เกิดข้อผิดพลาดบางอย่าง", "ไม่สามารถยืนยันการอนุมัติปันผลเฉลี่ยคืน");
                        }
                    }
                });
            } else {
                swal("ยกเลิกการยืนยันการอนุมัติปันผลเฉลี่ยคืนแล้้ว");
            }
        });
    }

    function cancelConfirm(master) {
        swal({
            title: "ยืนยันไม่อนุมัติปันผลเฉลี่ยคืน",
            showCancelButton: true,
            confirmButtonText: "ยืนยัน",
            closeOnConfirm: false,
            closeOnCancel: false,
            showLoaderOnConfirm: true
        }, function (isConfirm) {
            if (isConfirm) {
                $.post('/average_dividend/status_to', {id: master, status_to: 2}, function(res){
                    if(res.status){
                        swal("ยืนยันไม่อนุมัติปันผลเฉลี่ยคืนแล้ว");
                        setTimeout(function(){
                            location.reload();
                        }, 1500);
                    }else{
                        swal("เกิดข้อผิดพลาดบางอย่าง","ไม่สามารถยืนยันไม่อนุมัติปันผลเฉลี่ยคืน");
                    }
                });

            } else {
                swal("ยกเลิกการยืนยันไม่อนุมัติปันผลเฉลี่ยคืนแล้ว");
            }
        });
    }
</script>