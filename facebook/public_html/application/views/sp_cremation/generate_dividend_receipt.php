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
        <h1 style="margin-bottom: 0">สร้างใบเสร็จจากการปันผล</h1>
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
                        <form action="" method="post" id="form1">
                            <div class="row">
                                <div class="form-group">
                                    <label class="col-sm-4 control-label">ปี</label>
                                    <div class="col-sm-1">
                                        <select id="master_id" name="master_id" class="form-control m-b-1">
                                            <option value=""></option>
                                        <?php
                                            foreach($datas as $data){
                                        ?>
                                            <option value="<?php echo $data["id"]; ?>"><?php echo $data["year"]; ?></option>
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
                                    <div class="col-sm-1">
                                        <button type="button" class="btn btn-primary min-width-100" id="submit_btn">ดำเนินการ</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
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

        $("#submit_btn").click(function() {
            master_id = $("#master_id").val();
            if(master_id != "") {
                swal({
                    title: "ท่านต้องการดำเนินการใช่หรือไม่?",
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
                        $.post(base_url+"sp_cremation/<?php echo $path;?>/save_generate_dividend_receipt", $("#form1").serialize() , function(result) {
                            data = JSON.parse(result);
                            if(data.status == "success"){
                                $.unblockUI();
                                swal('ดำเนินการเสร็จสมบรูณ์','','success');
                            }else{
                                $.unblockUI();
                                swal('ทำรายการไม่ถูกต้องกรุณาลองอีกครั้ง','','warning');
                            }
                        });
                    }
                });
            } else {
                swal('กรุณาเลือกปีปันผล','','warning');
            }
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