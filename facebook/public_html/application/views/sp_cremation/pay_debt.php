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
        <h1 style="margin-bottom: 0">เรียกเก็บ</h1>
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 padding-l-r-0">
            <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6 padding-l-r-0">
                <?php $this->load->view('breadcrumb'); ?>
            </div>
            <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 padding-l-r-0">
                <button class="btn btn-primary btn-lg bt-add" type="button" id="all_add_btn">
                    <span class="icon icon-plus-circle"></span>
                    ประมวลผลเรียกเก็บ
                </button>
            </div>
        </div>
        <div class="row gutter-xs">
            <div class="col-xs-12 col-md-12">
                <div class="panel panel-body">
                    <div class="bs-example" data-example-id="striped-table">
                        <div id="search-section">
                            <form id="search-form" method="post" action="">
                                <div class="row">
                                    <div class="form-group">
                                        <label class="col-sm-4 control-label">ปีที่เรียกเก็บ</label>
                                        <div class="col-sm-3">
                                            <select id="search_year" name="year" class="form-control m-b-1">
                                                <option value=""></option>
                                            <?php
                                                $year = !empty($_POST["year"]) ? $_POST["year"] : date('Y')+543;
                                                for($i=((date('Y')+543)-5); $i<=((date('Y')+543)+1); $i++){
                                            ?>
                                                <option value="<?php echo $i; ?>" <?php echo $i == $year ? 'selected' : ''; ?>><?php echo $i; ?></option>
                                            <?php
                                                }
                                            ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group">
                                        <label class="col-sm-4 control-label">เลขที่ฌาปนกิจ</label>
                                        <div class="col-sm-3">
                                            <input type="text" name="cremation_member_id" class="form-control m-b-1" value="<?php echo $_POST["cremation_member_id"]?>">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group">
                                        <label class="col-sm-4 control-label">รอบสมัคร</label>
                                        <div class="col-sm-3  m-b-1">
                                            <select id="search_period_id" name="period_id" class="js-data-example-ajax">
                                                <option value=""></option>
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
                                        <label class="col-sm-4 control-label"></label>
                                        <div class="col-sm-3">
                                            <input type="submit" id="search-btn" class="btn btn-primary btn-lg" value="ค้นหา">
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th class="font-normal" width="5%"></th>
                                    <th class="font-normal"> เลขฌาปนกิจ </th>
                                    <th class="font-normal"> ชื่อ-นามสกุล </th>
                                    <th class="font-normal"> รอบสมัคร </th>
                                    <th class="font-normal"> อัตราการเรียกเก็บ </th>
                                    <th class="font-normal"> ค่าบำรุง </th>
                                    <th class="font-normal"> รวม </th>
                                    <th class="font-normal"> คงเหลือ </th>
                                    <th class="font-normal"> ปี </th>
                                    <th class="font-normal">  </th>
                                    <th class="font-normal" width="10%"></th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php
                                $i = 1;
                                foreach($datas as $data) {
                            ?>
                                <tr>
                                    <td class="text-center"></td>
                                    <td class="text-center"><?php echo $data["cremation_member_id"];?></td>
                                    <td class="text-left"><?php echo $data["prename_full"].$data["firstname_th"]." ".$data["lastname_th"];?></td>
                                    <td class="text-left"><?php echo $data["period_name"];?></td>
                                    <td class="text-right"><?php echo number_format($data["fee"],2);?></td>
                                    <td class="text-right"><?php echo number_format($data["assoc_fee"],2);?></td>
                                    <td class="text-right"><?php echo number_format($data["fee"] + $data["assoc_fee"],2);?></td>
                                    <td class="text-right"><?php echo number_format($data["balance"],2);?></td>
                                    <td class="text-center"><?php echo $data["year"];?></td>
                                    <td class="text-center">
                                        <?php
                                            if(!empty($receipts[$data["debt_id"]])) {
                                                foreach($receipts[$data["debt_id"]] as $receipt) {
                                        ?>
                                        <a id="receipt_btn_<?php echo $receipt["id"];?>" class="receipt_btn" target="_blank" href="<?php echo base_url(PROJECTPATH.'/sp_cremation/'.$path."/receipt?receipt_id=".$receipt["id"]); ?>"><?php echo $receipt["receipt_no"]?></a><br/>
                                        <?php
                                                }
                                            }
                                        ?>
                                    </td>
                                    <td class="text-right">
                                    <?php
                                        if($data["status"] == 1 || $data["status"] == 2) {
                                    ?>
                                        <input type="button" class="btn btn-primary btn_edit" data-id="<?php echo $data["debt_id"];?>" data-amount="<?php echo number_format($data["amount"],2);?>"
                                                data-name="<?php echo $data["prename_full"].$data["firstname_th"]." ".$data["lastname_th"];?>" data-year="<?php echo $data["year"]?>" data-period-name="<?php echo $data["period_name"]?>"
                                                data-cremation-member-id="<?php echo $data["cremation_member_id"]?>" data-balance="<?php echo $data['balance']?>" id="edit_btn_<?php echo $data["debt_id"]?>" value="ชำระเงิน"/>
                                    <?php
                                        }
                                    ?>
                                    </td>
                                </tr>
                            <?php
                                }
                            ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="payment_modal" tabindex="-1" role="dialog" class="modal fade">
    <div class="modal-dialog modal-dialog-account">
        <div class="modal-content">
            <div class="modal-header modal-header-confirmSave">
                <h2 class="modal-title">บันทึก</h2>
            </div>
            <div class="modal-body">
                <form action="" method="post" id="form_payment">
                    <input type="hidden" id="debt_id" name="debt_id" value=""/>
                    <div class="row">
                        <div class="form-group">
                            <label class="col-sm-4 control-label">ปี</label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control m-b-1" id="edit_year" value="" readonly/>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group">
                            <label class="col-sm-4 control-label">ชื่อ-นามสกุล</label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control m-b-1" id="edit_name" value="" readonly/>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group">
                            <label class="col-sm-4 control-label">ยอดเรียกเก็บ</label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control m-b-1" id="edit_total" value="" readonly/>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group">
                            <label class="col-sm-4 control-label">หนี้คงเหลือ</label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control m-b-1" id="edit_balance" value="" readonly/>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group">
                            <label class="col-sm-4 control-label">ชำระจำนวน</label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control m-b-1 type_input" id="payment_amount" name="amount" value="" onkeyup="format_the_number_decimal(this)"/>
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

        $(".btn_edit").click(function() {
            $("#debt_id").val($(this).attr("data-id"));
            $("#edit_year").val($(this).attr("data-year"));
            $("#edit_name").val($(this).attr("data-name"));
            $("#edit_total").val(numeral($(this).attr("data-amount")).format('0,0.00'));
            $("#edit_balance").val(numeral($(this).attr("data-balance")).format('0,0.00'));
            $("#payment_amount").val(numeral($(this).attr("data-balance")).format('0,0.00'));
            $("#payment_modal").modal("show");
        });
        $("#edit_cancel_btn").click(function() {
            $("#payment_modal").modal("hide");
        });
        $("#edit_submit_btn").click(function() {
            swal({
                title: "ท่านต้องการชำระหนี้ใช่หรือไม่?",
                text: "",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: '#DD6B55',
                confirmButtonText: 'ยืนยัน',
                cancelButtonText: "ยกเลิก",
                closeOnConfirm: true,
                closeOnCancel: true
            }, function(isConfirm) {
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
                    $('.type_input').each(function(){
                        $(this).val($(this).val().replace(/,/g, ''))
                    });
                    $.post(base_url+"sp_cremation/<?php echo $path;?>/debt_payment", $("#form_payment").serialize() , function(result) {
                        data = JSON.parse(result);
                        if(data.status == "success"){
                            window.open(base_url+"sp_cremation/<?php echo $path;?>/receipt?receipt_id="+data.receipt_id, '_blank');
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

        createSelect2("search-section");
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