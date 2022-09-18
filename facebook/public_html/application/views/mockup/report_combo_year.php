<div class="layout-content">
    <style type="text/css">
        .content-dialog {
            display: flex;
            justify-content: center;
            flex-direction: column;
            align-items: center;
            margin: auto;
            width: auto;
            height: 15vh;
        }

        .dialog {
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: row;
            margin: auto;
        }

        .form-group label {
            margin-right: 10px !important;
        }

        .input-icon {
            left: unset;
            margin-right: 15px;
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
                    <form class="content-dialog">
                        <div class="col-xs-12 col-md-12 dialog">
                            <div class="form-group col-xs-5 col-md-5">
                                <label class="control-label col-xs-4 col-md-4">เลือกปี </label>
                                <div class="col-xs-6 col-md-6">
                                    <select class="form-control m-b-1" id="year">
                                        <?php
                                        $current = $year ? $year : date('Y');
                                        for ($i = date('Y') - 5; $i <= date('Y') + 5; $i++) { ?>
                                            <option value="<?php echo $i; ?>" <?php echo $i == $current  ? 'selected' : ''; ?> > <?php echo($i + 543); ?> </option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group col-xs-5 col-md-5">
                                <label class="control-label col-xs-4 col-md-4">แสดงปี </label>
                                <div class="col-xs-6 col-md-6">
                                    <input type="text" readonly="readonly" class="form-control"
                                           id="display-range"
                                           value="<?php echo ($current + 542) . " - " . ($current + 543) ?>"/>
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-12 col-md-12 dialog">
                            <div class="form-group col-xs-5 col-md-5">
                                <label class="control-label col-xs-4 col-md-4"> รหัสสมาชิก </label>
                                <div class="col-xs-6 col-md-6">
                                    <div class="input-group">
                                        <input class="form-control" id="search-member" type="text" value="<?php echo $member_id; ?>" onkeypress="check_member_id()">
                                        <span class="input-group-btn">
								        <a data-toggle="modal" data-target="#myModal" id="test"
                                           class="fancybox_share fancybox.iframe" href="#">
									        <button id="" type="button" class="btn btn-info btn-search"><span
                                                        class="icon icon-search"></span></button>
								        </a>
							            </span>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group col-xs-5 col-md-5">
                                <label class="control-label col-xs-4 col-md-4"> ชื่อสกุล </label>
                                <div class="col-xs-6 col-md-6">
                                    <input class="form-control" readonly="readonly" type="text" id="member_id"
                                           value="<?php echo $fullname; ?>">
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-12 col-md-12 dialog" style="margin-top: 15px">
                            <div class="col-xs-12 col-md-12">
                                <div class="dialog">
                                    <button type="button" class="btn btn-primary text-center" onclick="alert()">
                                        แสดงรายงาน
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $this->load->view('search_member_new_modal'); ?>
<script type="application/javascript">
    var $date = $('.datetime');

    $(document).ready(function () {
        // $date.prop('readonly', 'readonly');
        // $date.prop('disabled', 'disabled');

        $(".mydate").datepicker({
            prevText: "ก่อนหน้า",
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
            autoclose: true
        });

    });

    function alert() {
        swal("ไม่พบข้อมูล");
        return false;
    }

    function check_member_id() {
        var member_id = $('#search-member').val();
        var year = $('#year').val();
        var keycode = (event.keyCode ? event.keyCode : event.which);
        if(keycode == '13'){
            $.post(base_url+"save_money/check_member_id",
                {
                    member_id: member_id
                }
                , function(result){
                    obj = JSON.parse(result);
                    console.log(obj.member_id);
                    mem_id = obj.member_id;
                    if(mem_id != undefined){
                        document.location.href = window.location.href.split('?')[0]+'?member_id='+mem_id+"&year="+year
                    }else{
                        swal('ไม่พบรหัสสมาชิกที่ท่านเลือก','','warning');
                    }
                });
        }
    }
</script>
