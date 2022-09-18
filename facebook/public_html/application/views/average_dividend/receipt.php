<div class="layout-content">
    <div class="layout-content-body">
        <style>
            .center {
                text-align: center;
            }
            .right {
                text-align: right;
            }
            .left {
                text-align: left;
            }
            .option-radio {
                /* position: relative; */
                /* left: 30px; */
            }
            .modal-dialog-account {
                margin:auto;
                margin-top:7%;
            }
            label{
                padding-top:7px;
            }
            .form-group{
                margin-bottom: 5px;
            }
            th {
                text-align: center;
            }
        </style>
        <h1 style="margin-bottom: 0">ใบเสร็จรับเงิน</h1>
        <?php $this->load->view('breadcrumb'); ?>
        <div class="row gutter-xs">
            <div class="col-xs-12 col-md-12">
                <div class="panel panel-body" style="padding-top:0px !important;">
                    <h3></h3>
                    <form action="<?php echo base_url(PROJECTPATH.'/average_dividend/receipt_pdf'); ?>" target="_blank" id="receiptForm" method="GET">
                        <!-- <input type="hidden" id="month" name="month" value="<?php echo $month; ?>">
					<input type="hidden" id="year" name="year" value="<?php echo $year; ?>">
					<input type="hidden" id="action_type" name="action_type" value=""> -->
                        <input type="hidden" name="id" value="<?php echo $_GET['id']; ?>">
                        <input type="hidden" name="mem_group_id" value="">
                        <input type="hidden" name="sect" value="department">
                        <input type="hidden" name="all" value="1">
                        <div class="form-group g24-col-sm-24">
                            <!-- <label class="g24-col-sm-6 text-right"><input type="radio" onclick="radio_check('1')" name="choose_receipt" value="1" checked=""></label> -->
                            <label class="g24-col-sm-6 control-label right option-radio">
                                <input type="radio" name="choose_receipt" value="1" checked="checked">
                            </label>
                            <label class="g24-col-sm-2 control-label right">
                                หน่วยงานหลัก </label>
                            <div class="g24-col-sm-11">
                                <select id="department" name="department" class="form-control">
                                    <option value="">เลือกข้อมูล</option>
                                    <?php foreach($departments as $department): ?>
                                        <option value="<?php echo $department['id']; ?>"><?php echo $department['mem_group_name']; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group g24-col-sm-24">
                            <label class="g24-col-sm-8 control-label right"> อำเภอ </label>
                            <div class="g24-col-sm-4">
                                <select id="faction" name="faction" class="form-control">
                                    <option value="">Select</option>
                                </select>
                            </div>
                            <label class="g24-col-sm-2 control-label right"> หน่วยงานย่อย </label>
                            <div class="g24-col-sm-5">
                                <select id="level" name="level" class="form-control">
                                    <option value="">Select</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group g24-col-sm-24">
                            <label class="g24-col-sm-8 control-label right"> แผ่นที่ </label>
                            <div class="g24-col-sm-11">
                                <select id="page_number" name="page_number" class="form-control">
                                    <?php foreach($page_numbers as $key => $page_number): ?>
                                        <option value="<?php echo $key; ?>"><?php echo $page_number['text']; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group g24-col-sm-24">
                            <label class="g24-col-sm-6 control-label right option-radio">
                                <input type="radio" name="choose_receipt" value="2">
                            </label>
                            <label class="g24-col-sm-2 control-label right">
                                รหัสสมาชิก </label>
                            <div class="g24-col-sm-4">
                                <input type="text" class="form-control" name="member_id">
                            </div>
                        </div>
                        <div class="form-group g24-col-sm-24">
                            <label class="g24-col-sm-6 control-label right option-radio">
                                <input type="radio" name="choose_receipt" value="3">
                            </label>
                            <label class="g24-col-sm-2 control-label right">
                                รหัสสมาชิก </label>
                            <div class="g24-col-sm-4">
                                <input type="text" class="form-control" name="member_id_begin">
                            </div>
                            <label class="g24-col-sm-2 control-label right"> ถึง รหัสสมาชิก </label>
                            <div class="g24-col-sm-5">
                                <input type="text" class="form-control" name="member_id_end">
                            </div>
                        </div>
                    </form>
                    <div class="form-group g24-col-sm-24" style="margin-top:20px;">
                        <label class="g24-col-sm-7 control-label right"></label>
                        <div class="g24-col-sm-10">
                            <button type="button" class="btn btn-primary" onclick="submit_form('real_print')" style="width:100%">แสดงใบเสร็จ</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="application/javascript">
    var master = $('input[name=id]').val();

//    $(document).on('click', '#controller-form-2', function (e) {
    //        var url = 'receipt_pdf?all=1&id=' + master + '&member_id=' + $('#form-control-2').val();
    //        window.open(url, "_blank");
    //    }).on('keypress', '#form-control-2', function (e) {
    //        if (e.keyCode === 13) {
    //            var url = 'receipt_pdf?all=1&id=' + master + '&member_id=' + $('#form-control-2').val();
    //            window.open(url, "_blank");
    //        }
    //    });
    //
    //    $(document).on('click', '#controller-form-1', function (e) {
    //        var url= 'receipt_pdf?all=1&id=' + master + '&page=' + $('#form-control-1').val();
    //        window.open(url, "_blank");
    //    });

var base_url = $('#base_url').attr('class');
var mem_group = $('input[name=mem_group_id]');
var sect= $('input[name=sect]');
$(document).ready(function() {
    $('.member_id').attr('disabled', 'true');
    $('.employee_id').attr('disabled', 'true');

    // function get mem_group
    var getMemGroup = function (id, inHtml, section) {
        mem_group.val(id);
        sect.val(section);
        $.ajax({
            method: 'POST',
            url: base_url + 'average_dividend/find_receipt_mem_group',
            data: {
                mem_group_id: id,
                section: section,
                id: master
            },
            success: function (msg) {
                $('#page_number').html(buildPageOption(msg));
                if(inHtml === null){
                    return;
                }else {
                    inHtml.html(buildGroupOption(msg));
                }
            }
        });
    }

    var buildGroupOption = function(json){
        var option = '<option value="0" selected="">เลือกข้อมูลทั้งหมด</option>';
        if(json.mem_group.length === 0){
            return option;
        }
        json.mem_group.forEach(function(item, index){
            option += '<option value="'+ item.id +'">'+item.mem_group_name+'</option>';
        });
        return option;
    }

    var buildPageOption = function(json){
        var option = '';
        if(json.page_numbers.length === 0){
            return option;
        }
        json.page_numbers.forEach(function(item, index){
            option += '<option value="'+ item.page +'">'+item.text+'</option>';
        });
        return option;
    }

    // get sub department when on change department
    $('body').on('change', '#department', function () {
        // clear when change to empty(); if has value to append on sub level
        if ($(this).val() === '') {
            var defaultOption = '<option value="">เลือกข้อมูล</option>'
            $('#faction').html(defaultOption);
            $('#level').html(defaultOption);
        } else {
            getMemGroup($(this).val(), $('#faction'),'department');
        }
    });

    // get level when on change faction
    $('body').on('change', '#faction', function () {
        if ($(this).val() === '' || $(this).val() === 0) {
            var defaultOption = '<option value="">เลือกข้อมูล</option>'
            $('#level').html(defaultOption);
        } else {
            getMemGroup($(this).val(), $('#level'), 'faction');
        }
    });


    // get level when on change faction
    $('body').on('change', '#level', function () {
        if ($(this).val() === '' || $(this).val() === 0) {
            var defaultOption = '<option value="">เลือกข้อมูล</option>'
            $('#level').html(defaultOption);
        } else {
            getMemGroup($(this).val(), null, 'level');
        }
    });


    // onload page then disable not selected
    setTimeout(function(){
        toggleMember(false);
        toggleDepartment(true);
        toggleMemberRange(false);
    }, 500);

    // radio condition when selected
    $('body').on('click', 'input[name=choose_receipt]', function(){
        var onChecked = $(this).val();
        if(onChecked == '2'){
            toggleMember(true);
            toggleDepartment(false);
            toggleMemberRange(false);
        }else if(onChecked == '3'){
            toggleMember(false);
            toggleDepartment(false);
            toggleMemberRange(true);
        }else{
            toggleMember(false);
            toggleDepartment(true);
            toggleMemberRange(false);
        }
    });

    // function disabled
    var toggleDepartment = function(onToggle) {
        if(onToggle){
            $('#department').removeAttr('disabled');
            $('#faction').removeAttr('disabled');
            $('#level').removeAttr('disabled');
            $('#page_number').removeAttr('disabled');
        } else {
            $('#department').attr('disabled', true);
            $('#faction').attr('disabled', true);
            $('#level').attr('disabled', true);
            $('#page_number').attr('disabled', true);
        }
    }

    var toggleMember = function(onToggle) {
        if(onToggle){
            $('input[name=member_id]').removeAttr('disabled');
        } else {
            $('input[name=member_id]').attr('disabled', true);
        }
    }

    var toggleMemberRange = function(onToggle) {
        if(onToggle) {
            $('input[name=member_id_begin]').removeAttr('disabled');
            $('input[name=member_id_end]').removeAttr('disabled');
        } else {
            $('input[name=member_id_begin]').attr('disabled', true);
            $('input[name=member_id_end]').attr('disabled', true);
        }
    }

    // validation
    var validateForm = function(){
        var chooseReceipt = $('input[name=choose_receipt]:checked').val();
        var chkErr = true;
        if(chooseReceipt == 2) {
            chkErr = ($('input[name=member_id]').val() != '') ? true : false;
        } else if(chooseReceipt == 3) {
            chkErr = ($('input[name=member_id_begin]').val() != '' && $('input[name=member_id_end]').val() != '') ? true : false;
        }
        if(!chkErr) {
            swal({
                type: 'error',
                title: 'แจ้งเตือน',
                text: 'กรุณาระบุข้อมูลให้ครบถ้วน!'
            });
            return false;
        }
        return true;
    }

    // submit form
    submit_form = function(action_type){
        var checkValid = validateForm();
        if(!checkValid) return;

        $('#action_type').val(action_type);
        $('#receiptForm').submit();
    }
});

</script>