<div class="layout-content">
    <div class="layout-content-body">
        <style>
            .border1 { border: solid 1px #ccc; padding: 0 15px; }
            .mem_pic { margin-top: -1em;float: right; width: 150px; }
            .mem_pic img { width: 100%; border: solid 1px #ccc; }
            .mem_pic button { display: block; width: 100%; }
            .modal-backdrop.in{
                opacity: 0;
            }
            .modal-backdrop {
                position: relative;
                top: 0;
                right: 0;
                bottom: 0;
                left: 0;
                z-index: 1040;
                background-color: #000;
            }
            .font-normal{
                font-weight:normal;
            }
            .table-bordered>tbody>tr>td, .table-bordered>tbody>tr>th, .table-bordered>tfoot>tr>td, .table-bordered>tfoot>tr>th, .table-bordered>thead>tr>td, .table-bordered>thead>tr>th {
                border: 1px solid #fff;
            }
            th {
                text-align: center;
            }

            .modal-dialog-search {
                width: 700px;
            }
        </style>
        <link rel="stylesheet" href="<?=base_url('assets/css/select2.min.css')?>">
        <script src="<?=base_url('assets/js/select2.min.js')?>"></script>
        <h1 style="margin-bottom: 0"> จัดการเงินลิ้นซัก </h1>
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 padding-l-r-0">
            <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 padding-l-r-0" id="breadcrumb">
                <?php $this->load->view('breadcrumb'); ?>
            </div>
        </div>
        <div class="panel panel-body col-xs-12 col-sm-12 col-md-12 col-lg-12 " >
            <form action="<?php echo base_url(PROJECTPATH.'/finance_process/finance_month_other_save'); ?>" method="POST" id="form2">
                <div class="row m-t-1">
                    <div class="g24-col-sm-24">
                        <div class="form-group">
                            <div class=" g24-col-sm-24">
                                <label class="g24-col-sm-3 control-label font-normal" for="form-control-2">วันที่</label>
                                <div class="g24-col-sm-4 m-b-1">
                                    <input type="text" class="form-control"  name="date" id="date" value="<?=date('d/m/Y') ?>" disabled readonly>
                                </div>

                                <label class="g24-col-sm-3 control-label font-normal" for="form-control-2">จำนวนเงิน</label>
                                <div class="g24-col-sm-4">
                                    <input type="text" class="form-control"  name="budget" id="budget" value="<?=$drawer->balance?>" disabled readonly>
                                </div>
                            </div>
                        </div>

                        <div class="form-group" id="btn_space">

                            <?php if( (($user_primary->user_primary == 1 && count($drawer_list) == 0) || ($user_primary->user_primary == 2)) && !empty($drawer) ) { ?>

                                <div class=" g24-col-sm-12">
                                    <button type="button" onclick="add_row()" class="add-member btn btn-primary min-width-100">
                                        <span class="icon icon-plus"></span>
                                        เพิ่มสมาชิก
                                    </button>

                                </div>
                                <div class=" g24-col-sm-12" style="text-align:right;">
                                    <button type="button" class="btn btn-primary drawer-save min-width-100">
                                        <span class="icon icon-save"></span>
                                        บันทึก
                                    </button>
                                </div>
                            <?php } ?>

                        </div>
                    </div>
                </div>
                <div class="bs-example" data-example-id="striped-table">
                    <table class="table table-bordered table-striped">
                        <thead>
                        <tr class="bg-primary">
                            <th class = "font-normal" style="width: 15%">รหัสสมาชิก</th>
                            <th class = "font-normal" style="width: 25%;">ชื่อ-สกุล</th>
                            <th class = "font-normal" style="width: 10%;">จำนวนเงิน</th>
                            <th class = "font-normal" style="width: 5%;"></th>
                        </tr>
                        </thead>
                        <tbody id="table_data">
                        <?php foreach ($drawer_list as $key => $value) { ?>
                            <tr>
                                <td>

                                    <select name='' disabled class='form-control select2' >
                                        <option ><?=$value['employee_id']?></option>
                                    </select>
                                </td>
                                <td><input type='text' id='' name='' class='form-control' value="<?=$value['user_name']?>" readonly></td>
                                <td><input type='number' class='form-control' id='' name='' value="<?=$value['amount']?>" readonly></td>
                                <td align='center'></td>
                            </tr>
                        <?php } ?>

                        </tbody>
                    </table>

                </div>
                <div class="row m-t-1 table_footer" style="display:none;">
                    <center>
                        <button class="btn btn-primary" type="button" id="save" style="width:auto;" onclick="submit_form();">
                            <span class="icon icon-print"></span>
                            บันทึก
                        </button>
                    </center>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="search_member_modal" role="dialog">
    <div class="modal-dialog modal-dialog-search">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">ข้อมูลสมาชิก</h4>
            </div>
            <div class="modal-body">
                <div class="input-with-icon">
                    <div class="row">
                        <div class="col">
                            <label class="col-sm-2 control-label">รูปแบบค้นหา</label>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <select id="member_search_list" name="member_search_list" class="form-control m-b-1">
                                        <option value="">เลือกรูปแบบค้นหา</option>
                                        <option value="member_id">รหัสสมาชิก</option>
                                        <option value="id_card">หมายเลขบัตรประชาชน</option>
                                        <option value="firstname_th">ชื่อสมาชิก</option>
                                        <option value="lastname_th">นามสกุล</option>
                                    </select>
                                </div>
                            </div>
                            <label class="col-sm-1 control-label" style="white-space: nowrap;"> ค้นหา </label>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <div class="input-group">
                                        <input id="member_search_text" name="member_search_text" class="form-control m-b-1" type="text" value="<?php echo @$data['id_card']; ?>">
                                        <span class="input-group-btn">
									<button type="button" id="member_search" class="btn btn-info btn-search"><span class="icon icon-search"></span></button>
								</span>
                                    </div>
                                </div>
                            </div>
                            <input id="data_row" name="data_row" class="form-control m-b-1" type="hidden" value="">
                        </div>
                    </div>
                </div>

                <div class="bs-example" data-example-id="striped-table">
                    <table class="table table-striped">
                        <tbody id="result_member_search">
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <input type="hidden" id="input_id">
                <button type="button" id="close" class="btn btn-default" data-dismiss="modal">ปิดหน้าต่าง</button>
            </div>
        </div>
    </div>
</div>
<script>
    var listDrawer = <?php echo json_encode($user_child) ?>;
    var i=0;
    function check_number_row() {
        <?php if( $user_primary->user_primary == 1  ) { ?>
        if(i == 1) {
            $('.add-member').hide()
        } else {
            $('.add-member').show()

        }
        <?php } ?>
    }
    function add_row(){

        check_number_row()
        // $('#value_null').hide();
        var new_row = "";
        new_row += "<tr class='new_row' id='new_row_"+i+"'>\n";
        new_row += "<td>";
        new_row += "<select name='data[list_data]["+i+"][add_drawer_amount]' class='form-control select2' >";
        new_row += '<option data-id="" data-name="" data-employee-id="" value="">เลือกข้อมูล</option>';
        $.each(listDrawer, function(key, value) {

            new_row += '<option data-id="'+value.user_id+'" data-name="'+value.user_name+'" data-employee-id="'+value.employee_id+'" value="'+value.user_name+'">'+value.employee_id+' '+value.user_name+'</option>';

        })
        new_row += "</select>";
        // new_row += "<div class='input-group'>";
        // new_row += "<input type='text' id='member_id_"+i+"' class='form-control center member_id' name='data[list_data]["+i+"][member_id]' onchange=\"keypress_search_member('"+i+"')\">";
        // new_row += "<span class='input-group-btn'>";
        // new_row += "<a data-toggle='modal' class='fancybox_share fancybox.iframe' href='#' onclick=\"open_modal('search_member_modal','"+i+"')\">";
        // new_row += "<button id='' type='button' class='btn btn-info btn-search'><span class='icon icon-search'></span></button>";
        // new_row += "</a>";
        // new_row += "</span>";
        // new_row += "</div>";
        new_row += "</td>";
        new_row += "<td><input type='text' id='member_name_"+i+"' name='data[list_data]["+i+"][member_name]' class='form-control member_name' readonly></td>";
        new_row += "<td><input type='text' class='form-control amount' id='pay_amount_"+i+"' name='data[list_data]["+i+"][amount]' ></td>";
        new_row += "<td align='center'><a style='cursor:pointer;' class='icon icon-trash-o dele-drawer' titla='ลบ'></a></td>";
        new_row += "</tr>\n";
        $('#table_data').append(new_row);
        $('.select2').select2({
            matcher: function(params, data) {
                if ($.trim(params.term) === '') return data;
                if (typeof data.text === 'undefined') return null;

                // `params.term` should be the term that is used for searching
                // `data.text` is the text that is displayed for the data object
                if (data.text.indexOf(params.term) > -1 || $(data.element).data("name").indexOf(params.term) > -1) {
                    var modifiedData = $.extend({}, data, true);
                    modifiedData.text += ' (matched)';

                    return modifiedData;
                }

                // Return `null` if the term should not be displayed
                return null;
            }
        });
        i++;
        check_number_row()

    }

    $(document).on('click' , '.dele-drawer' , function() {
        $(this).parents('tr').remove()
        i--;
        check_number_row()

    })
    $(document).on('change' , '.select2' , function(){
        var data = $(this).parents('tr').find(".select2 option:selected").val();
        console.log($(this).parents('tr').find('.member_name'));

        $(this).parents('tr').find('.member_name').val(data);

    })
    $(document).on('click' , '.drawer-save' , function(){
        var idArray = []
        var amountArray = []
        // $(this).attr('disabled' , true)

        var checkError = true;

        if ($('#table_data tr.new_row').length == 0) {


            swal('กรุณาเลือกรหัสสมาชิกให้ครบ','','warning');
            checkError = false
        }
        $.each($('#table_data tr.new_row') , function(key,value) {

            if($(value).find('.select2 option:selected').val() == "") {


                swal('กรุณาเลือกรหัสสมาชิกให้ครบ','','warning');
                checkError = false

            }
            idArray.push($(value).find('.select2 option:selected').attr('data-id'))

            if ($(value).find('.amount').val() == "" || parseInt($(value).find('.amount').val()) == 0) {


                swal('กรุณากรอกจำนวนเงิน','','warning');
                checkError = false

            }
            amountArray.push($(value).find('.amount').val())

        })
        var totalAmount = 0;

        var result = amountArray.map(amount => ( totalAmount += parseInt(amount.replace(',','')) ));
        var budget = $('#budget').val()
        if ( totalAmount > parseInt(budget)){


            swal('กรุณากรอกจำนวนเงินไม่เกิน '+budget+' บาท','','warning');
            checkError = false


        }

        let findDuplicates = (arr) => arr.filter((item, index) => arr.indexOf(item) != index)
        console.log(idArray);

        if (findDuplicates(idArray) > 0) {

            swal('กรุณาเลือกรหัสสมาชิกไม่ซ้ำกัน','','warning');
            checkError = false

        }
        if (checkError) {

            $.ajax({
                type: 'POST',
                url: base_url + 'drawer/submitDrawer',
                dataType: "json",
                data: {
                    drawer_id: idArray,
                    amount : amountArray
                },
                success: function (msg) {
                    console.log(msg);

                    if (msg.status) {
                        swal({
                                title: "แจ้งเตือน",
                                text: msg.message,
                                type: "success",
                                confirmButtonColor: '#DD6B55',
                                confirmButtonText: 'ตกลง',
                                closeOnConfirm: false,
                            },
                            function(isConfirm) {
                                if (isConfirm) {
                                    window.location.reload();
                                } else {

                                }
                            });
                        $(this).attr('disabled' ,  false)
                    } else {
                        swal(msg.message,'','warning');


                    }

                }
            });
        } else {
            $(this).attr('disabled' , false)

        }

    })
    // function validateNumber(event) {

    // };
    // $('.amount').keypress(validateNumber);
    $(document).on('keypress keyup' , '.amount' , function(event) {
        var key = window.event ? event.keyCode : event.which;
        if (event.keyCode === 8 || event.keyCode === 46) {
            return true;
        } else if ( key < 48 || key > 57 ) {
            return false;
        } else {
            return true;
        }
    })

</script>