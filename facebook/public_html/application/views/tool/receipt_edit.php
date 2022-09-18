<div class="layout-content">
    <div class="layout-content-body">
        <style>
            .center {
                text-align: center;
            }

            .left {
                text-align: left;
            }

            .right {
                text-align: right;
            }

            .modify {
                background-color: lightyellow;
            }

            .form-group {
                margin-bottom: 5px;
            }

            .red {
                color: red;
            }

            .green {
                color: green;
            }
        </style>
        <div class="row">
            <div class="form-group">
                <div class="col-sm-6">
                    <h1 class="title_top">แก้ไขใบเสร็จ</h1>
                    <?php $this->load->view('breadcrumb'); ?>
                </div>
            </div>
        </div>
        <div class="row gutter-xs">
            <div class="col-xs-12 col-md-12">
                <div class="panel panel-body" style="padding-top:0px !important;">
                    <h1>ใบเสร็จเลขที่ <small><?= $receipt_id ?></small></h1>
                    <br>
                    <form action="<?= base_url('tool/receipt_edit_save') ?>" method="post">
                        <table class="table table-border table-hover">
                            <thead>
                                <tr>
                                    <th width="30%">รายการชำระ</th>
                                    <th width="10%">งวดที่</th>
                                    <th width="15%">เงินต้น</th>
                                    <th width="15%">ดอกเบี้ย</th>
                                    <!-- <th>ดอกคงค้าง</th> -->
                                    <th width="15%">จำนวนเงิน</th>
                                    <th width="15%">คงเหลือ</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                // var_dump($transaction_data);
                                foreach ($transaction_data as $key => $value) {
                                ?>
                                    <tr>
                                        <td>
                                            <input type="text" class="form-control " value="<?= @$value['transaction_text'] ?>" readonly>
                                            <input type="hidden" name="role[]" value="<?= ($value['finance_transaction_id'] != "" ? 'finance_transaction_id' : 'loan_id') ?>">
                                            <input type="hidden" name="key[]" value="<?= ($value['finance_transaction_id'] != "" ? $value['finance_transaction_id'] : $key) ?>">
                                        </td>
                                        <td>
                                            <input type="text" class="form-control change_period" name="period_count[]" id="period_count_<?=$key?>" value="<?= @$value['period_count'] ?>" data-default="<?= @$value['period_count'] ?>" data-key="<?=$key?>">
                                            <input type="hidden" class="form-control" name="change_period[]" id="change_period_<?=$key?>" value="" >
                                        </td>
                                        <td>
                                            <input type="text" class="form-control right is_numeral" name="principal_payment[]" value="<?= number_format(@$value['principal_payment'], 2) ?>">
                                        </td>
                                        <td>
                                            <input type="text" class="form-control right is_numeral" name="interest[]" value="<?= number_format(@$value['interest'], 2) ?>">
                                        </td>
                                        <td>
                                            <input type="text" class="form-control right is_numeral" value="<?= number_format(@$value['principal_payment'] + @$value['interest'], 2) ?>" readonly>
                                        </td>
                                        <td>
                                            <input type="text" class="form-control right is_numeral" name="loan_amount_balance[]" value="<?= number_format(@$value['loan_amount_balance'], 2) ?>">
                                        </td>
                                    </tr>
                                <?php

                                }
                                ?>
                            </tbody>
                        </table>
                        <br>
                        <br>
                        <div class="row">
                            <div class="col-md-12 center">
                                <input type="hidden" name="receipt_id" value="<?=$receipt_id?>">
                                <button class="btn btn-primary" type="submit">บันทึก</button>
                            </div>
                        </div>
                    </form>

                </div>
            </div>
        </div>

    </div>
</div>

<script>
    $("body").on('change', '.is_numeral', function() { // 2nd (B)
        var val = numeral($(this).val()).value();

        val = numeral(val).format('0,0.00');
        console.log("is_numeral", val);
        $(this).val(val);
        $(this).addClass("modify");
    });

    $("body").on('change', '.change_period', function() { // 2nd (B)
        var key = $(this).data("key");
        var default_val = $(this).data("default");
        var val = numeral($(this).val()).value();

        if(default_val!=val){
            swal({
                title: "แจ้งเตือน",
                text: "ต้องการอัพเดทงวดที่ ให้เป็นงวดปัจจุบันหรือไม่ ?",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "ใช่",
                cancelButtonText: "ไม่ใช่",
                closeOnConfirm: false,
                closeOnCancel: false
            }, function (isConfirm) {
                    if (isConfirm) {
                        swal("บันทึกร่าง!", "งวดที่ จะถูกบันทึกหลังจากกดบันทึก", "success");
                        $("#change_period_"+key).val(1);
                    } else {
                        swal("ยกเลิก", "คืนค่างวดที่เรียบร้อย", "error");
                        $(this).val(default_val);
                        $("#change_period_"+key).val("");
                    }
            });
        }else{
            $("#change_period_"+key).val("");
        }
        $(this).addClass("modify");
    });
    
</script>