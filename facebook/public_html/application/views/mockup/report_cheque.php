<div class="layout-content">
    <style type="text/css">
        .content-dialog{
            display: flex;
            justify-content: center;
            flex-direction: column;
            align-items: center;
            width: auto;
            height: 15vh;
        }
        .dialog {
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .form-group label{
            margin-right: 10px !important;
        }
        .input-icon{
            left: unset;
            margin-right: 15px;
            margin-left: 30px;
        }

        input.form-control{
            padding-left: 50px;
        }

        .form-group .icon{
            padding: 0 30px;
        }

        .form-group label{
            min-width: 35px !important;
        }
    </style>
    <div class="layout-content-body">
        <h1 class="title_top"><?php echo $title; ?></h1>
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 padding-l-r-0">
            <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 padding-l-r-0">
                <?php $this->load->view('breadcrumb'); ?>
            </div>
        </div>
        <div class="row gutter-xs">
            <div class="col-xs-12 col-md-12">
                <div class="panel panel-body">
                    <form class="form-inline content-dialog">
                        <div class="col-xs-12 col-md-12 text-center dialog" style="margin-bottom: 15px">
                            <div class="radio" style="margin: 0 15px 0 0;">
                                <label>
                                    <input type="radio" name="optradio" id="all" value="all" checked> ทั้งหมด
                                </label>
                            </div>
                            <div class="radio" style="margin: 0 15px 0 0;">
                                <label>
                                    <input type="radio" name="optradio" id="dist" value="range"> เลือกช่วงเวลา
                                </label>
                            </div>
                            <div class="form-group" style="margin: 0 15px 0 0;">
                                <label for="date"> วันที่ </label>
                                <input type="text" class="form-control datetime mydate" id="date" data-date-language="th-th" value="<?php echo $this->center_function->mydate2date(date('Y-m-d')); ?>">
                                <span class="icon icon-calendar input-icon m-f-1"></span>
                            </div>
                            <div class="form-group" style="margin: 0 15px 0 0;">
                                <label for="to"> ถึง </label>
                                <input type="text" class="form-control datetime mydate" id="to" data-date-language="th-th" value="<?php echo $this->center_function->mydate2date(date('Y-m-d')); ?>">
                                <span class="icon icon-calendar input-icon m-f-1"></span>
                            </div>
                        </div>
                        <div class="col-xs-12 col-md-12">
                            <div class="dialog">
                                <button type="button" class="btn btn-primary text-center" onclick="alert()">แสดงรายงาน
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="application/javascript">
    var $date = $('.datetime');

    $(document).ready(function(){
        $date.prop('readonly', 'readonly');
        $date.prop('disabled', 'disabled');

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

    });

    $(document).on("loan change", "input[name='optradio']:checked", function (e) {
        if($(this).val() === 'range') {
            $date.prop('disabled', false);
            $date.prop('readonly', false);
        }else{
            $date.prop('readonly', 'readonly');
            $date.prop('disabled', 'disabled');
        }
    });
    function alert() {
        swal("ไม่พบข้อมูล");
        return false;
    }
</script>
