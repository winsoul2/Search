<div class="layout-content">
    <div class="layout-content-body">
        <style>
            label {
                padding-top: 7px;
            }

            .control-label {
                padding-top: 7px;
            }

            .indent {
                text-indent: 40px;

            .modal-dialog-data {
                width: 90% !important;
                margin: auto;
                margin-top: 1%;
                margin-bottom: 1%;
            }

            }
            .bt-add {
                float: none;
            }

            .modal-dialog {
                width: 80%;
            }

            small {
                display: none !important;
            }

            .cke_contents {
                height: 500px !important;
            }

            th {
                text-align: center;
            }

            .money-textbox {
                width: 85px;
                display: unset;
                margin: 5px;
                padding-left: 0px;
                padding-right: 0px;
            }

            .year-textbox {
                width: 60px;
                display: unset;
                margin: 5px;
            }

            .modal-footer {
                border-top: 0;
            }

            .row {
                margin-top: 5px;
            }

            .cond-del-btn {
                vertical-align: top;
            }
        </style>
        <?php
        $act = @$_GET['act'];
        $id = @$_GET['id'];
        $detail_id = @$_GET['detail_id'];
        ?>
        <?php
        if (empty($act)) {
            ?>
            <h1 style="margin-bottom: 0">สวัสดิการสมาชิก</h1>
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 padding-l-r-0">
                <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6 padding-l-r-0">
                    <?php $this->load->view('breadcrumb'); ?>
                </div>
                <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 " style="padding-right:0px;text-align:right;">
                    <button class="btn btn-primary btn-lg bt-add" type="button" onclick="add_type();"><span
                                class="icon icon-plus-circle"></span> เพิ่มสวัสดิการ
                    </button>
                </div>
            </div>
            <div class="row gutter-xs">
                <div class="col-xs-12 col-md-12">
                    <div class="panel panel-body">
                        <div class="bs-example" data-example-id="striped-table">
                            <table class="table table-striped">
                                <thead>
                                <tr>
                                    <th class="font-normal" width="5%">ลำดับ</th>
                                    <th class="font-normal"> ชื่อสวัสดิการ</th>
                                    <th class="font-normal" style="width: 15%"> วันที่เริ่มใช้</th>
                                    <th class="font-normal" style="width: 15%"> จัดการ</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                if (!empty($rs)) {
                                    foreach (@$rs as $key => $row) {
                                        $this->db->select(array('*'));
                                        $this->db->from('coop_benefits_type_detail');
                                        $this->db->where("benefits_id = '" . @$row["benefits_id"] . "' AND start_date <= '" . date('Y-m-d') . "'");
                                        $this->db->order_by('start_date DESC');
                                        $rs_detail = $this->db->get()->result_array();
                                        ?>
                                        <tr>
                                            <td scope="row" align="center"><?php echo $i++; ?></td>
                                            <td class="text-left"><?php echo @$row['benefits_name']; ?></td>
                                            <td align="center"><?php echo @$rs_detail[0]['start_date'] == '' ? 'ไม่ระบุ' : $this->center_function->ConvertToThaiDate(@$rs_detail[0]['start_date']); ?></td>
                                            <td align="center">
                                                <a href="?act=detail&id=<?php echo @$row["benefits_id"] ?>">ดูรายละเอียด</a>
                                                |
                                                <a style="cursor:pointer;"
                                                   onclick="edit_type('<?php echo @$row['benefits_id']; ?>','<?php echo @$row['benefits_name']; ?>','');">แก้ไข</a>
                                                |
                                                <a href="#"
                                                   onclick="del_coop_data('<?php echo @$row['benefits_id']; ?>')"
                                                   class="text-del"> ลบ </a>
                                            </td>
                                        </tr>
                                        <?php
                                    }
                                }
                                ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <?php echo @$paging ?>
                </div>
            </div>
            <?php
        } else if ($act == "detail") {
            ?>
            <h1 style="margin-bottom: 0">สวัสดิการสมาชิก</h1>
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 padding-l-r-0">
                <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6 padding-l-r-0">
                    <?php $this->load->view('breadcrumb'); ?>
                </div>
                <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 " style="padding-right:0px;text-align:right;">
                    <a href="<?php echo base_url(PROJECTPATH . '/setting_benefits_data/benefits_type?act=add&id=' . $_GET['id']); ?>">
                        <button class="btn btn-primary btn-lg bt-add" type="button" onclick="add_detail();"><span
                                    class="icon icon-plus-circle"></span> เพิ่มรายการ
                        </button>
                    </a>
                </div>
            </div>
            <div class="row gutter-xs">
                <div class="col-xs-12 col-md-12">
                    <div class="panel panel-body">
                        <h1 class="text-left m-t-1 m-b-1"><?php echo @$benefits_type['benefits_name']; ?></h1>
                        <div class="bs-example" data-example-id="striped-table">
                            <table class="table table-striped">
                                <thead>
                                <tr>
                                    <th class="font-normal" width="5%">ลำดับ</th>
                                    <th class="font-normal"> วันที่เพิ่ม</th>
                                    <th class="font-normal"> วันที่มีผล</th>
                                    <th class="font-normal"> สถานะ</th>
                                    <th class="font-normal" style="width: 150px;"> จัดการ</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                $i = 1;
                                if (!empty($rs_detail)) {
                                    foreach (@$rs_detail as $key => $row_detail) {
                                        $this->db->select(array('*'));
                                        $this->db->from('coop_benefits_type_detail');
                                        $this->db->where("benefits_id = '" . @$_GET['id'] . "' AND start_date <= '" . date('Y-m-d') . "'");
                                        $this->db->order_by('start_date DESC');
                                        $rs_status = $this->db->get()->result_array();
                                        $row_status = @$rs_status[0];
                                        ?>
                                        <tr>
                                            <td scope="row" align="center"><?php echo $i++; ?></td>
                                            <td align="center"><?php echo $this->center_function->ConvertToThaiDate(@$row_detail['createdatetime']); ?></td>
                                            <td align="center"><?php echo $this->center_function->ConvertToThaiDate(@$row_detail['start_date']); ?></td>
                                            <td align="center">
                                                <?php echo $row_status['id'] == @$row_detail['id'] ? 'ใช้งาน' : 'ไม่ใช้งาน'; ?>
                                            </td>
                                            <td align="center">
                                                <a href="?act=edit&id=<?php echo @$row_detail["benefits_id"] ?>&detail_id=<?php echo @$row_detail["id"] ?>">ดูรายละเอียด</a>
                                                |
                                                <a href="#"
                                                   onclick="del_coop_detail_data('<?php echo @$row_detail["benefits_id"] ?>','<?php echo @$row_detail["id"]; ?>')"
                                                   class="text-del"> ลบ </a>
                                            </td>
                                        </tr>
                                        <?php
                                    }
                                }
                                ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <?php echo @$paging ?>
                </div>
            </div>

            <?php
        } else { ?>

            <div class="col-md-12">
                <h1 class="text-left m-t-1 m-b-1"><?php echo @$benefits_type['benefits_name']; ?></h1>
                <form id='form_save' data-toggle="validator" novalidate="novalidate"
                      action="<?php echo base_url(PROJECTPATH . '/setting_benefits_data/benefits_type_detail_save'); ?>"
                      method="post">
                    <input id="id" name="id" type="hidden" value="<?php echo $id; ?>" required>
                    <?php if (!empty($detail_id)) { ?>
                        <input id="type_add" name="type_add" type="hidden" value="edit" required>
                        <input id="detail_id" name="detail_id" type="hidden" value="<?php echo $detail_id; ?>" required>

                    <?php } else { ?>
                        <input id="type_add" name="type_add" type="hidden" value="add" required>
                    <?php } ?>

                    <div class="row">
                        <label class="col-sm-2 control-label text-right" for="benefits_detail">รายละเอียด</label>
                        <div class="col-sm-9">
                            <div class="form-group">
                                <textarea id="benefits_detail" name="benefits_detail" required
                                          title="กรุณากรอก รายละเอียด"><?php echo @$row['benefits_detail']; ?></textarea>
                            </div>
                        </div>
                    </div>

                    <div id="limit-div" data-index="<?php count($limits); ?>">
                        <div class="row">
                            <label class="col-sm-2 control-label text-right">ข้อจำกัด</label>
                        </div>
                        <?php
                        foreach ($limits as $key => $limit) {
                            ?>
                            <div class="row" id="limit-<?php echo $key; ?>">
                                <div class="form-group">
                                    <input type="hidden" name="limit_code[<?php echo $key; ?>]"
                                           value="<?php echo $limit["type_code"]; ?>">
                                    <input type="hidden" name="limit_val[<?php echo $key; ?>]"
                                           value="<?php echo $limit["value"]; ?>">
                                    <label class="col-sm-2 control-label text-right"></label>
                                    <div class="col-sm-10">
                                        <label class="control-label text-right" for="">
                                            - <?php echo $limit["prefix"] ?> <?php echo $limit["value"] ?> <?php echo $limit["postfix_unit"] ?></label>
                                        &nbsp&nbsp&nbsp&nbsp
                                        <input type="button" id="limit-del-btn-<?php echo $key; ?>"
                                               class="btn btn-danger limit-del-btn" data-index="<?php echo $key; ?>"
                                               value="ลบ">
                                    </div>
                                </div>
                            </div>
                            <?php
                        }
                        ?>
                    </div>
                    <div class="row">&nbsp</div>
                    <div id="cond-div" data-index="<?php echo count($choices); ?>">
                        <div class="row">
                            <label class="col-sm-2 control-label text-right">เงื่อนไข</label>
                        </div>
                        <?php
                        $choice_index = 0;
                        foreach ($choices as $key => $choice) {
                            $detail_text = " - " . $choice["detail"];

                            foreach ($choice["cond"] as $cond_index => $cond) {
                                if (!empty($cond["cond_data_code"]) && !empty($cond["cond_data_operation"]) && !empty($cond["cond_data_value"])) {
                                    $detail_text .= "<br/>&nbsp;&nbsp;";
                                    $detail_text .= $cond["cond_data_code"] == "age" ? "อายุ" : ($cond["cond_data_code"] == "member_age" ? "เป็นสมาชิก" : "");
                                    if ($cond["cond_data_operation"] == "grester_than"){
                                        $detail_text .= "มากกว่า";
                                    } else if ($cond["cond_data_operation"] == "equal"){
                                        $detail_text .= "เท่ากับ";
                                    } else if ($cond["cond_data_operation"] == "less_than"){
                                        $detail_text .= "น้อยกว่า";
                                    } else if ($cond["cond_data_operation"] == "grester_than_or_equa"){
                                        $detail_text .= "มากกว่าหรือเท่ากับ";
                                    } else if ($cond["cond_data_operation"] == "less_than_or_equal"){
                                        $detail_text .= "น้อยกว่าหรือเท่ากับ";
                                    }
                                    //$detail_text .= $cond["cond_data_operation"] == "grester_than" ? "มากกว่า" : ($cond["cond_data_operation"] == "equal" ? "เท่ากับ" : ($cond["cond_data_operation"] == "less_than" ? "น้อยกว่า" : ""));
                                    $detail_text .= " " . $cond["cond_data_value"] . " ปี";
                                }
                            }
                            $number_add_text = "";
                            if ($choice["has_number"]) {
                                $number_add_text = "  *สามารถกำหนดปริมาณได้";
                            }
                            ?>
                            <div class="row" id="cond-<?php echo $choice_index; ?>">
                                <div class="form-group">
                                    <div class="row" id="cond-<?php echo $choice_index; ?>">
                                        <input type="hidden" name="cond[<?php echo $choice_index; ?>][detail]"
                                               value="<?php echo $choice["detail"]; ?>">
                                        <?php
                                        $count = 0;
                                        $index_list = "";
                                        foreach ($choice["cond"] as $cond_index => $cond) {
                                            ?>
                                            <input type="hidden"
                                                   name="cond[<?php echo $choice_index; ?>][data][<?php echo $cond_index; ?>][cond_data_add]"
                                                   value="<?php echo $cond["cond_data_code"]; ?>">
                                            <input type="hidden"
                                                   name="cond[<?php echo $choice_index; ?>][data][<?php echo $cond_index; ?>][operation]"
                                                   value="<?php echo $cond["cond_data_operation"]; ?>">
                                            <input type="hidden"
                                                   name="cond[<?php echo $choice_index; ?>][data][<?php echo $cond_index; ?>][data_val]"
                                                   value="<?php echo $cond["cond_data_value"]; ?>">
                                            <?php
                                            $count++;
                                            $index_list .= $cond_index . ",";
                                        }
                                        ?>
                                        <input type="hidden" name="cond[<?php echo $choice_index; ?>][count]"
                                               value="<?php echo $count; ?>">
                                        <input type="hidden" name="cond[<?php echo $choice_index; ?>][index_list]"
                                               value="<?php echo $index_list; ?>">
                                        <input type="hidden" name="cond[<?php echo $choice_index; ?>][benefit_amount]"
                                               value="<?php echo $choice["amount"]; ?>">
                                        <input type="hidden" name="cond[<?php echo $choice_index; ?>][number_add]"
                                               value="<?php echo $choice["has_number"]; ?>">
                                        <label class="col-sm-2 control-label"></label>
                                        <div class="col-sm-10">
                                            <label class="control-label text-left" for=""><?php echo $detail_text; ?>
                                                <br/>&nbsp;&nbsp;จ่าย <?php echo number_format($choice["amount"], 2) ?>
                                                บาท <?php echo $number_add_text; ?></label>
                                            &nbsp&nbsp&nbsp&nbsp
                                            <div>
                                                <input type="button" id="cond-del-btn-<?php echo $choice_index; ?>"
                                                       class="btn btn-danger cond-del-btn"
                                                       data-index="<?php echo $choice_index; ?>" value="ลบ">
                                                <input type="button" id="cond-edit-btn-<?php echo $choice_index; ?>"
                                                       class="btn btn-primary cond-edit-btn"
                                                       data-index="<?php echo $choice_index; ?>" value="แก้ไข">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">&nbsp</div>
                                </div>
                            </div>
                            <?php
                            $choice_index++;
                        }
                        ?>
                    </div>

                    <div class="row">
                        <label class="col-sm-2 control-label text-right" for="add-limit-modal-btn"></label>
                        <div class="col-sm-10">
                            <div class=" form-group">
                                <input type="button" id="add-limit-modal-btn" class="btn btn-primary"
                                       value="เพิ่มข้อจำกัด">
                                <input type="button" id="add-cond-modal-btn" class="btn btn-primary"
                                       value="เพิ่มเงื่อนไข">
                            </div>
                        </div>
                    </div>

                    <div class="row">&nbsp</div>

                    <div class="row">
                        <label class="col-sm-2 control-label text-right"
                               for="choice_type">ประเภทการเลือกเงื่อนไข</label>
                        <div class="col-sm-2">
                            <div class="form-group">
                                <select id="choice_type" name="choice_type" class="form-control" style="">
                                    <option value="1">เลือกได้ 1 ข้อ</option>
                                    <option value="2" <?php echo $row['choice_type'] == 2 ? "selected" : ""; ?>>
                                        เลือกได้มากกว่า 1 ข้อ
                                    </option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <label class="col-sm-2 control-label text-right" for="start_date">มีผลวันที่</label>
                        <div class="col-sm-2">
                            <div class="form-group">
                                <input id="start_date" name="start_date" class="form-control m-b-1"
                                       style="padding-left: 50px;" type="text"
                                       value="<?php echo $this->center_function->mydate2date(empty($row['start_date']) ? '' : @$row['start_date']); ?>"
                                       data-date-language="th-th" required title="กรุณาเลือก มีผลวันที่">
                                <span class="icon icon-calendar input-icon m-f-1"></span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group text-center">
                        <button type="button" onclick="check_form()" class="btn btn-primary min-width-100">ตกลง</button>
                        <a href="?act=detail&id=<?php echo $_GET['id']; ?>">
                            <button class="btn btn-danger min-width-100" type="button">ยกเลิก</button>
                        </a>
                    </div>
                </form>
            </div>

        <?php } ?>
    </div>
</div>
<div id="benefits_type_modal" tabindex="-1" role="dialog" class="modal fade">
    <div class="modal-dialog modal-dialog-data">
        <div class="modal-content">
            <div class="modal-header modal-header-confirmSave">
                <button type="button" class="close" data-dismiss="modal">x</button>
                <h2 class="modal-title"><span id="title_1">เพิ่มสวัสดิการ</span></h2>
            </div>
            <div class="modal-body">
                <div class="form-group" style="padding-bottom: 50px;">
                    <form id='form1' data-toggle="validator" novalidate="novalidate"
                          action="<?php echo base_url(PROJECTPATH . '/setting_benefits_data/benefits_type_save'); ?>"
                          method="post">
                        <input type="hidden" class="form-control" id="benefits_id" name="benefits_id" value="">
                        <div class="row">
                            <label class="col-sm-4 control-label text-right" for="benefits_name">ชื่อสวัสดิการ</label>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <input id="benefits_name" name="benefits_name" class="form-control m-b-1"
                                           type="text" value="" required title="กรุณากรอก ชื่อสวัสดิการ">
                                </div>
                            </div>
                            <label class="col-sm-2 control-label">&nbsp;</label>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-12" style="text-align:center;margin-top:20px;margin-bottom:20px;">
                                <button type="button" class="btn btn-primary" onclick="save_type()">บันทึก</button>&nbsp;&nbsp;&nbsp;
                                <button type="button" class="btn btn-default" data-dismiss="modal">ปิดหน้าต่าง</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="add_limit_modal" tabindex="-1" role="dialog" class="modal fade">
    <div class="modal-dialog modal-dialog-data">
        <div class="modal-content">
            <div class="modal-header modal-header-confirmSave">
                <button type="button" class="close" data-dismiss="modal">x</button>
                <h2 class="modal-title"><span id="title_1">เพิ่มข้อจำกัด</span></h2>
            </div>
            <div class="modal-body">
                <div class="row">
                    <label class="col-sm-2 control-label text-right" for="start_date">ประเภท</label>
                    <div class="col-sm-2">
                        <select id="limit_type_add" name="limit_type_add" class="form-control" style="">
                            <option value=""></option>
                            <?php
                            foreach ($limit_types as $type) {
                                ?>
                                <option value="<?php echo $type["code"] ?>" data-prefix="<?php echo $type["prefix"] ?>"
                                        data-postfix="<?php echo $type["postfix_unit"] ?>"><?php echo $type["name"] ?></option>
                                <?php
                            }
                            ?>
                        </select>
                    </div>
                    <label class="col-sm-1 control-label text-right" for="start_date">ปริมาณ/ปี</label>
                    <div class="col-sm-2">
                        <input id="time_add" name="time_add" class="form-control m-b-1" style="padding-left: 50px;"
                               type="text" value="">
                    </div>
                    <div class="col-sm-1">
                        <input type="button" id="add-limit-btn" class="btn btn-primary" value="เพิ่ม"/>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="add_cond_modal" tabindex="-1" role="dialog" class="modal fade">
    <div class="modal-dialog modal-dialog-data">
        <div class="modal-content">
            <div class="modal-header modal-header-confirmSave">
                <button type="button" class="close" data-dismiss="modal">x</button>
                <h2 class="modal-title"><span id="title_2">เพิ่มเงื่อนไข</span></h2>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="form-group">
                        <label class="col-sm-3 control-label text-right" for="cond_detail_add">รายละเอียด</label>
                        <div class="col-sm-9">
                            <input type="text" id="cond_detail_add" class="form-control" value="">
                        </div>
                    </div>
                </div>
                <div id="data-cond-div">
                    <div class="row">
                        <label class="col-sm-3 control-label text-right" for="cond_data_add">เทียบข้อมูล</label>
                        <div class="col-sm-2">
                            <select id="cond_data_add" name="cond_data_add" class="form-control" style="">
                                <option selected value="age">อายุ</option>
                                <option value="member_age">อายุการเป็นสมาชิก</option>
                            </select>
                        </div>
                        <div class="col-sm-2">
                            <select id="data_operation" class="form-control" style="">
                                <option value="grester_than">มากกว่า</option>
                                <option value="grester_than_or_equa">มากกว่าหรือเท่ากับ</option>
                                <option value="equal">เท่ากับ</option>
                                <option value="less_than_or_equal">น้อยกว่าหรือเท่ากับ</option>
                                <option value="less_than">น้อยกว่า</option>
                            </select>
                        </div>
                        <div class="col-sm-2">
                            <input type="text" id="cond_detail_data_val_add" class="form-control" value="">
                        </div>
                        <div class="col-sm-2">
                            <input type="button" id="add-data-cond-btn" class="btn btn-primary" data-add-index="1"
                                   value="เพิ่มเงื่อนไข"/>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <label class="col-sm-3 control-label text-right"
                           for="cond_detail_money_add">ปริมาณเงินที่จะได้รับ</label>
                    <div class="col-sm-9">
                        <input type="text" id="cond_detail_money_add" class="form-control" value="">
                    </div>
                </div>
                <div class="row">
                    <label class="col-sm-3 control-label text-right" for="cond_detail_money_add">กำหนดปริมาณได้</label>
                    <div class="col-sm-9">
                        <input type="checkbox" data-account="" id="cond_has_number_add">
                    </div>
                </div>
                <div class="row text-center">
                    <input type="button" id="add-cond-btn" class="btn btn-primary" value="บันทึก"/>
                </div>
            </div>
            <div class="modal-footer">
            </div>
        </div>
    </div>
</div>
<div id="edit_cond_modal" tabindex="-1" role="dialog" class="modal fade">
    <div class="modal-dialog modal-dialog-data">
        <div class="modal-content">
            <div class="modal-header modal-header-confirmSave">
                <button type="button" class="close" data-dismiss="modal">x</button>
                <h2 class="modal-title"><span id="title_2">เพิ่มเงื่อนไข</span></h2>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="form-group">
                        <label class="col-sm-3 control-label text-right" for="cond_detail_edit">รายละเอียด</label>
                        <div class="col-sm-9">
                            <input type="text" id="cond_detail_edit" class="form-control" value="">
                        </div>
                    </div>
                </div>
                <div id="data-cond-div-edit" now-edit-index="0" edit-index = "" cond-count="">
                </div>
                <div class="row">
                    <label class="col-sm-3 control-label text-right"
                           for="cond_detail_money_edit">ปริมาณเงินที่จะได้รับ</label>
                    <div class="col-sm-9">
                        <input type="text" id="cond_detail_money_edit" class="form-control" value="">
                    </div>
                </div>
                <div class="row">
                    <label class="col-sm-3 control-label text-right" for="cond_detail_money_edit">กำหนดปริมาณได้</label>
                    <div class="col-sm-9">
                        <input type="checkbox" data-account="" id="cond_has_number_edit_e">
                    </div>
                </div>
                <div class="row text-center">
                    <input type="button" id="edit-cond-btn" class="btn btn-primary" value="แก้ไข"/>
                </div>
            </div>
            <div class="modal-footer">
            </div>
        </div>
    </div>
</div>
<?php

$link = array(
    'src' => PROJECTJSPATH . 'assets/ckeditor/ckeditor.js',
    'type' => 'text/javascript'
);
echo script_tag($link);

$link = array(
    'src' => PROJECTJSPATH . 'assets/ckeditor/adapters/jquery.js',
    'type' => 'text/javascript'
);
echo script_tag($link);


$link = array(
    'src' => PROJECTJSPATH . 'assets/js/coop_benefits_type.js',
    'type' => 'text/javascript'
);
echo script_tag($link);
?>
<script>
    $(document).ready(function () {
        if ($("#benefits_detail").length) {
            $("#benefits_detail").ckeditor({
                height: 146,
                customConfig: '<?php echo PROJECTPATH; ?>/assets/ckeditor/config-admin-color.js'
            });
        }

        $("#add-limit-btn").click(function () {
            let warning_text = ""
            if ($("#limit_type_add").val() == "") {
                warning_text += "กรุณาเลือก ประเภทข้อจำกัด\n";
            }
            if ($("#time_add").val() == "") {
                warning_text += "กรุณาระบุ ปริมาณ/ปี";
            }
            if (warning_text == "") {
                let index = parseInt($("#limit-div").attr("data-index")) + 1;
                let code = $('option:selected', "#limit_type_add").val();
                let prefix = $('option:selected', "#limit_type_add").attr('data-prefix');
                let postfix = $('option:selected', "#limit_type_add").attr('data-postfix');
                let value = $("#time_add").val();
                limit_html = `<div class="row" id="limit-` + index + `">
								<div class="form-group">
									<input type="hidden" name="limit_code[` + index + `]" value="` + code + `">
									<input type="hidden" name="limit_val[` + index + `]" value="` + value + `">
									<label class="col-sm-2 control-label text-right"></label>
									<div class="col-sm-10">
										<label class="control-label text-right" for=""> - ` + prefix + ` ` + value + ` ` + postfix + `</label>
										&nbsp&nbsp&nbsp&nbsp
										<input type="button" id="limit-del-btn-` + index + `" class="btn btn-danger limit-del-btn" data-index="` + index + `" value="ลบ">
									</div>
								</div>
							</div>`;
                $("#limit-div").append(limit_html)
                $("#limit-div").attr("data-index", index)
                $("#add_limit_modal").modal("hide")
            } else {
                swal('ไม่สามารถเพิ่มข้อจำกัดได้', warning_text, 'warning');
            }
        });

        $("#edit-cond-btn").click(function () {
            let warning_text = ""
            if ($("#cond_detail_edit").val() == "") {
                warning_text += "กรุณาเลือกกรอกรายละเอียด\n";
            }
            if ($("#cond_detail_money_edit").val() == "") {
                warning_text += "กรุณาระบุ ปริมาณเงินที่จะได้รับ";
            }
            if (warning_text == "") {
                let index = parseInt($("#cond-div-edit").attr("data-index")) + 1;
                let now_edit_index = parseInt($("#data-cond-div-edit").attr("edit-index")); //index ที่แก้ไขตอนนี้
                let this_cond_count = parseInt($("#data-cond-div-edit").attr("cond-count")); //จำนวนเงื่อนไขของ index นี้
                let detail = $("#cond_detail_edit").val(); //ชื่อเงื่อนไข
                //let cond_data_add = $("#cond_data_edit").val();
                let operation = $("#data_operation_edit").val();
                let data_val = $("#cond_detail_data_val_edit").val();
                let benefit_amount = $("#cond_detail_money_edit").val(); //เงินที่ได้รับ
                let number_add = $("#cond_has_number_edit_e").is(":checked") ? 1 : 0; //ปุ่ม check box
                console.log("now_edit_index = " + now_edit_index);
                console.log("cond_count = " + this_cond_count);
                console.log("detail = " + detail);
                console.log("cond_data_add = " + cond_data_add);
                console.log("operation = " + operation);
                console.log("data_val = " + data_val);
                console.log("benefits_amount = " + benefit_amount );
                console.log("number_add = " + number_add);
                number_add_text = ""
                console.log(this);
                if (number_add) {
                    number_add_text = "  *สามารถกำหนดปริมาณได้"
                }
                let data_cond_text = "";
                //data_cond_size = parseInt($("#edit-data-cond-btn").attr("data-edit-index"));
                data_input_text = "";
                cond_count = 0;
                let count = document.getElementsByName(`cond[` + now_edit_index + `][count]`)[0].value;
                let index_list = document.getElementsByName(`cond[` + now_edit_index + `][index_list]`)[0].value;
                console.log("count = "  +count);
                console.log("index_list = "+ index_list);
                let now_edit_index_attr = parseInt($("#data-cond-div-edit").attr("now-edit-index"));
                console.log("now_edit_index_attr = "+ now_edit_index_attr);
                console.log("cond_count = " + cond_count);
                let new_index_list = "";
                for (i = 0; i < this_cond_count; i++) {
                    let at = now_edit_index_attr - this_cond_count + i;
                    if (document.getElementById("data-cond-div-edit-" + at) == null){
                        continue;
                    }
                    new_index_list += at+",";
                    console.log("at = "+ at);
                    console.log("data con div = "+document.getElementById("data-cond-div-edit-" + at));
                    cond_data_add_i = $("#cond_data_edit_" + at).val();
                    operation_i = $("#data_operation_edit_" + at).val();
                    data_val_i = $("#cond_detail_data_val_edit_" + at).val();
                    console.log("cond_data_add_i = " + cond_data_add_i);
                    console.log("operation_i = " + operation_i);
                    console.log("data_val_i = " + data_val_i);
                    if (data_val_i != "" && cond_data_add_i != "" && operation_i != "" && data_val_i != undefined && cond_data_add_i != undefined && operation_i != undefined) {
                        data_cond_text += "<br/>&nbsp;&nbsp;"
                        data_cond_text += cond_data_add_i == "age" ? "อายุ" : (cond_data_add_i == "member_age" ? "เป็นสมาชิก" : "");
                        if (operation_i == "grester_than"){
                            data_cond_text += "มากกว่า";
                        } else if (operation_i == "equal"){
                            data_cond_text += "เท่ากับ";
                        } else if (operation_i == "less_than"){
                            data_cond_text += "น้อยกว่า";
                        } else if (operation_i == "grester_than_or_equa"){
                            data_cond_text += "มากกว่าหรือเท่ากับ";
                        } else if (operation_i == "less_than_or_equal"){
                            data_cond_text += "น้อยกว่าหรือเท่ากับ";
                        }
                        data_cond_text += " " + data_val_i + " ปี";
                        data_input_text += `<input type="hidden" name="cond[` + now_edit_index + `][data][` + at + `][cond_data_add]" value="` + cond_data_add_i + `">
											<input type="hidden" name="cond[` + now_edit_index + `][data][` + at + `][operation]" value="` + operation_i + `">
											<input type="hidden" name="cond[` + now_edit_index + `][data][` + at + `][data_val]" value="` + data_val_i + `">`;
                        cond_count++;
                    }
                }
                console.log("data_cond_text = " + data_cond_text);
                console.log(data_input_text);

                let cond_html = `
									<input type="hidden" name="cond[` + now_edit_index + `][detail]" id = "cond[` + now_edit_index + `][detail]" value="` + detail + `">
									<input type="hidden" name="cond[` + now_edit_index + `][benefit_amount]" value="` + benefit_amount + `">
									<input type="hidden" name="cond[` + now_edit_index + `][number_add]" value="` + number_add + `">
									` + data_input_text + `
                                    <input type="hidden" name=cond[` + now_edit_index + `][count] = value="` + cond_count + `">
                                    <input type="hidden" name=cond[` + now_edit_index + `][index_list] = value="` + new_index_list + `">
									<label class="col-sm-2 control-label"></label>
									<div class="col-sm-10">
										<label class="control-label text-left" for=""> - ` + detail + data_cond_text + `<br/>&nbsp;&nbsp;จ่าย ` + format_number(benefit_amount) + ` บาท` + number_add_text + `</label>
										&nbsp&nbsp&nbsp&nbsp
                                              <div>
										            <input type="button" id="cond-del-btn-` + now_edit_index + `" class="btn btn-danger cond-del-btn" data-index="` + now_edit_index + `" value="ลบ">
                                                    <input type="button" id="cond-edit-btn-` + now_edit_index + `"
                                                       class="btn btn-primary cond-edit-btn"
                                                       data-index="` + now_edit_index + `" value="แก้ไข">
                                              </div>
									</div>
								<div class="row">&nbsp</div>`
                console.log(cond_html);
                document.getElementById(`cond-` + now_edit_index + ``).innerHTML = cond_html;
                $("#cond-div-edit").append(cond_html);
                $("#cond-div-edit").attr("data-index", now_edit_index);
                console.log(document.getElementById("data-cond-div-edit"));
                document.getElementById("data-cond-div-edit").innerHTML = "";
                $("#edit_cond_modal").modal("hide");
            }
        });

        $("#add-limit-modal-btn").click(function () {
            $("#limit_type_add").val("");
            $("#time_add").val("");
            $('#add_limit_modal').modal('show');
        });

        $(document).on("click", ".limit-del-btn", function () {
            let index = $(this).attr("data-index");
            $("#limit-" + index).remove();
        });

        $("#add-cond-modal-btn").click(function () {
            $("#cond_detail_add").val("");
            $("#cond_data_add").val("");
            $("#cond_detail_money_add").val("");
            $("#cond_has_number_add").prop("checked", false);
            $("#add_cond_modal").modal("show");
        });


        $("#add-cond-btn").click(function () {
            let warning_text = ""
            if ($("#cond_detail_add").val() == "") {
                warning_text += "กรุณาเลือกกรอกรายละเอียด\n";
            }
            if ($("#cond_detail_money_add").val() == "") {
                warning_text += "กรุณาระบุ ปริมาณเงินที่จะได้รับ";
            }
            if ($("#cond_data_add").val() == null){
                warning_text += "กรุณาระบุเงื่อนไข";
            }
            if ($("#cond_detail_data_val_add").val() == ""){
                warning_text += "กรุณาระบุปี";
            }
            if (warning_text == "") {
                let index = parseInt($("#cond-div").attr("data-index")) + 1;
                let detail = $("#cond_detail_add").val();
                let cond_data_add = $("#cond_data_add").val();
                let operation = $("#data_operation").val();
                let data_val = $("#cond_detail_data_val_add").val();
                let benefit_amount = $("#cond_detail_money_add").val();
                let number_add = $("#cond_has_number_add").is(":checked") ? 1 : 0;
                number_add_text = "";
                if (number_add) {
                    number_add_text = "  *สามารถกำหนดปริมาณได้"
                }
                let data_cond_text = "";
                if (data_val != undefined && cond_data_add != undefined && operation != undefined && data_val != "" && cond_data_add != "" && operation != "") {
                    data_cond_text = "<br/>&nbsp;&nbsp;"
                    data_cond_text += cond_data_add == "age" ? "อายุ" : (cond_data_add == "member_age" ? "เป็นสมาชิก" : "");
                    data_cond_text += operation == "grester_than" ? "มากกว่า" : (operation == "equal" ? "เท่ากับ" : (operation == "less_than" ? "น้อยกว่า" : ""));
                    data_cond_text += " " + data_val + " ปี";
                }

                data_cond_size = parseInt($("#add-data-cond-btn").attr("data-add-index"));
                data_input_text = "";
                count = 1;
                index_list = "0,";
                for (i = 0; i <= data_cond_size; i++) {
                    cond_data_add_i = $("#cond_data_add_" + i).val();
                    operation_i = $("#data_operation_" + i).val();
                    data_val_i = $("#cond_detail_data_val_add_" + i).val();
                    if (data_val_i != "" && cond_data_add_i != "" && operation_i != "" && data_val_i != undefined && cond_data_add_i != undefined && operation_i != undefined) {
                        data_cond_text += "<br/>&nbsp;&nbsp;"
                        data_cond_text += cond_data_add_i == "age" ? "อายุ" : (cond_data_add_i == "member_age" ? "เป็นสมาชิก" : "");
                        data_cond_text += operation_i == "grester_than" ? "มากกว่า" : (operation_i == "equal" ? "เท่ากับ" : (operation_i == "less_than" ? "น้อยกว่า" : ""));
                        data_cond_text += " " + data_val_i + " ปี";
                        data_input_text += `<input type="hidden" name="cond[` + index + `][data][` + i + `][cond_data_add]" value="` + cond_data_add_i + `">
											<input type="hidden" name="cond[` + index + `][data][` + i + `][operation]" value="` + operation_i + `">
											<input type="hidden" name="cond[` + index + `][data][` + i + `][data_val]" value="` + data_val_i + `">`;
                        count++;
                        index_list += i + ",";
                    }
                }

                let cond_html = `<div class="row" id="cond-` + index + `">
									<input type="hidden" name="cond[` + index + `][detail]" id = "cond[` + index + `][detail]" value="` + detail + `">
									<input type="hidden" name="cond[` + index + `][data][0][cond_data_add]" value="` + cond_data_add + `">
									<input type="hidden" name="cond[` + index + `][data][0][operation]" value="` + operation + `">
									<input type="hidden" name="cond[` + index + `][data][0][data_val]" value="` + data_val + `">
									<input type="hidden" name="cond[` + index + `][benefit_amount]" value="` + benefit_amount + `">
									<input type="hidden" name="cond[` + index + `][number_add]" value="` + number_add + `">
									` + data_input_text + `
                                    <input type="hidden" name=cond[` + index + `][count] = value="` + count + `">
                                    <input type="hidden" name=cond[` + index + `][index_list] = value="` + index_list + `">
									<label class="col-sm-2 control-label"></label>
									<div class="col-sm-10">
										<label class="control-label text-left" for=""> - ` + detail + data_cond_text + `<br/>&nbsp;&nbsp;จ่าย ` + format_number(benefit_amount) + ` บาท` + number_add_text + `</label>
										&nbsp&nbsp&nbsp&nbsp
                                        <div>
                                             <input type="button" id="cond-del-btn-` + index + `" class="btn btn-danger cond-del-btn" data-index="` + index + `" value="ลบ">
                                             <input type="button" id="cond-edit-btn-` + index + `"
                                                    class="btn btn-primary cond-edit-btn"
                                                    data-index="` + index + `" value="แก้ไข">
                                        </div>
									</div>
								</div>
								<div class="row">&nbsp</div>`
                $("#cond-div").append(cond_html);
                $("#cond-div").attr("data-index", index)
                $("#add_cond_modal").modal("hide");
            }
        });

        $(document).on("click", ".cond-del-btn", function () {
            let index = $(this).attr("data-index");
            $("#cond-" + index).remove();
        });

        $(document).on("click", ".cond-edit-btn", function () {
            let index = $(this).attr("data-index");
            document.getElementById("data-cond-div-edit").setAttribute("edit-index",index);
            console.log(index);
            //$("#cond-"+index).remove();
            document.getElementById("data-cond-div-edit").innerHTML = "";
            let count = document.getElementsByName(`cond[` + index + `][count]`)[0].value;
            console.log("1");
            let index_list = document.getElementsByName(`cond[` + index + `][index_list]`)[0].value;
            console.log("2");
            let cond_count = 0;
            document.getElementById("data-cond-div-edit").setAttribute("cond-count",cond_count);
            console.log("3");
            for (i = 0 ; i < count ; i++){
                let button_count = parseInt($("#data-cond-div-edit").attr("now-edit-index"));
                console.log("4");
                let at = index_list.split(",")[i];
                console.log("5");
                //console.log(document.getElementsByName(`cond[`+index+`][detail]`)[0].value);
                let cond_data_add = document.getElementsByName(`cond[` + index + `][data][`+at+`][cond_data_add]`)[0].value;
                console.log("6");
                let operation = document.getElementsByName(`cond[` + index + `][data][`+at+`][operation]`)[0].value;
                console.log("7");
                let value = document.getElementsByName(`cond[` + index + `][data][`+at+`][data_val]`)[0].value;
                console.log("8");
                let base_cond = `<div class="row" id="data-cond-div-edit-` + button_count + `">
                                <label class="col-sm-3 control-label text-right" for="cond_data_edit">เทียบข้อมูล</label>
                                    <div class="col-sm-2">`;
                console.log("9");
                console.log("cond data add = " + cond_data_add);
                console.log("operation = " + operation);
                if (cond_data_add == 'member_age'){
                    base_cond += `<select id="cond_data_edit_` + button_count + `" name="cond_data_edit" class="form-control" style="">
                                <option selected value="member_age">อายุการเป็นสมาชิก</option>
                                <option value="age">อายุ</option>
                              </select>`;
                } else if (cond_data_add == 'age'){
                    base_cond += `<select id="cond_data_edit_` + button_count + `" name="cond_data_edit" class="form-control" style="">
                                  <option selected value="age">อายุ</option>
                                  <option value="member_age">อายุการเป็นสมาชิก</option>
                              </select>`;
                } else if (cond_data_add == ''){
                    base_cond += `<select id="cond_data_edit_` + button_count + `" name="cond_data_edit" class="form-control" style="">
                                  <option selected value="age">อายุ</option>
                                  <option value="member_age">อายุการเป็นสมาชิก</option>
                              </select>`;
                } else {
                    base_cond += `<select id="cond_data_edit_` + button_count + `" name="cond_data_edit" class="form-control" style="">
                                  <option selected value="age">อายุ</option>
                                  <option value="member_age">อายุการเป็นสมาชิก</option>
                              </select>`;
                }
                base_cond += `</div>
                              <div class="col-sm-2">`;
                if (operation == "grester_than"){
                    base_cond += `<select id="data_operation_edit_` + button_count + `" class="form-control" style="">
                                    <option selected value="grester_than">มากกว่า</option>
                                    <option value="grester_than_or_equa">มากกว่าหรือเท่ากับ</option>
                                    <option value="equal">เท่ากับ</option>
                                    <option value="less_than_or_equal">น้อยกว่าหรือเท่ากับ</option>
                                    <option value="less_than">น้อยกว่า</option>
                                  </select>`;
                } else if (operation == ""){
                    base_cond += `<select id="data_operation_edit_` + button_count + `" class="form-control" style="">
                                    <option value="grester_than">มากกว่า</option>
                                    <option value="grester_than_or_equa">มากกว่าหรือเท่ากับ</option>
                                    <option value="equal">เท่ากับ</option>
                                    <option value="less_than_or_equal">น้อยกว่าหรือเท่ากับ</option>
                                    <option value="less_than">น้อยกว่า</option>
                                  </select>`;
                } else if (operation == "grester_than_or_equa"){
                    base_cond += `<select id="data_operation_edit_` + button_count + `" class="form-control" style="">
                                    <option value="grester_than">มากกว่า</option>
                                    <option selected value="grester_than_or_equa">มากกว่าหรือเท่ากับ</option>
                                    <option value="equal">เท่ากับ</option>
                                    <option value="less_than_or_equal">น้อยกว่าหรือเท่ากับ</option>
                                    <option value="less_than">น้อยกว่า</option>
                                  </select>`;
                } else if (operation == "equal") {
                    base_cond +=`<select id="data_operation_edit_` + button_count + `" class="form-control" style="">
                                    <option value="grester_than">มากกว่า</option>
                                    <option value="grester_than_or_equa">มากกว่าหรือเท่ากับ</option>
                                    <option selected value="equal">เท่ากับ</option>
                                    <option value="less_than_or_equal">น้อยกว่าหรือเท่ากับ</option>
                                    <option value="less_than">น้อยกว่า</option>
                                  </select>`;
                } else if (operation == "less_than_or_equal"){
                    base_cond += `<select id="data_operation_edit_` + button_count + `" class="form-control" style="">
                                    <option value="grester_than">มากกว่า</option>
                                    <option value="grester_than_or_equa">มากกว่าหรือเท่ากับ</option>
                                    <option value="equal">เท่ากับ</option>
                                    <option selected value="less_than_or_equal">น้อยกว่าหรือเท่ากับ</option>
                                    <option value="less_than">น้อยกว่า</option>
                                  </select>`;
                } else if (operation == "less_than" ){
                    base_cond +=`<select id="data_operation_edit_` + button_count + `" class="form-control" style="">
                                    <option value="grester_than">มากกว่า</option>
                                    <option value="grester_than_or_equa">มากกว่าหรือเท่ากับ</option>
                                    <option value="equal">เท่ากับ</option>
                                    <option value="less_than_or_equal">น้อยกว่าหรือเท่ากับ</option>
                                    <option selected value="less_than">น้อยกว่า</option>
                                  </select>`;
                } else {
                    base_cond +=`<select id="data_operation_edit_` + button_count + `" class="form-control" style="">
                                    <option value="grester_than">มากกว่า</option>
                                    <option value="grester_than_or_equa">มากกว่าหรือเท่ากับ</option>
                                    <option value="equal">เท่ากับ</option>
                                    <option value="less_than_or_equal">น้อยกว่าหรือเท่ากับ</option>
                                    <option value="less_than">น้อยกว่า</option>
                                  </select>`;
                }
                if (i == 0){
                    base_cond += `</div>
                                   <div class="col-sm-2">
                                      <input type="text" id="cond_detail_data_val_edit_` + button_count + `" class="form-control" value=`+value+`>
                                   </div>
                                   <div class="col-sm-2">
                                     <input type="button" id="edit-data-cond-btn" class="btn btn-primary edit-data-cond-btn" data-edit-index="1" value="เพิ่มเงื่อนไข"/>
                                   </div>
                                </div>`;
                } else {
                    base_cond += `</div>
                                   <div class="col-sm-2">
                                      <input type="text" id="cond_detail_data_val_edit_` + button_count + `" class="form-control" value=`+value+`>
                                   </div>
                                   <div class="col-sm-2">
                                     <input type="button" id="del-edit-data-cond-btn" class="btn btn-danger del-edit-data-cond-btn" data-edit-index="`+button_count+`" value="ลบเงื่อนไข"/>
                                   </div>
                                </div>`;
                }
                cond_count++;
                document.getElementById("data-cond-div-edit").setAttribute("cond-count",cond_count);
                document.getElementById("data-cond-div-edit").setAttribute("now-edit-index",button_count + 1);
                $("#data-cond-div-edit").append(base_cond);
            }
            console.log("index = " + index);
            console.log("number_add = "+document.getElementsByName(`cond[` + index + `][number_add]`)[0].value);
            if (document.getElementsByName(`cond[` + index + `][number_add]`)[0].value == 1){
                console.log("in number_add");
                $("#cond_has_number_edit_e").prop("checked", true);
            } else {
                $("#cond_has_number_edit_e").prop("checked", false);
            }
            let cond_cnt = document.getElementsByName(`cond[` + index + `][count]`)[0].value;
            $("#cond_detail_edit").val(document.getElementsByName(`cond[` + index + `][detail]`)[0].value);
            $("#cond_data_edit").val("");
            $("#cond_detail_money_edit").val(document.getElementsByName(`cond[` + index + `][benefit_amount]`)[0].value);
            //$("#cond_has_number_edit").prop("checked", false);
            $("#edit_cond_modal").modal("show");
        });

        $("#add-data-cond-btn").click(function () {
            index = parseInt($(this).attr("data-add-index")) + 1;
            let html = `<div class="row" id="data-cond-div-` + index + `">
							<label class="col-sm-3 control-label text-right" for="cond_data_add">เทียบข้อมูล</label>
							<div class="col-sm-2">
								<select id="cond_data_add_` + index + `" name="cond_data_add" class="form-control" style="">
									<option selected value="age">อายุ</option>
									<option value="member_age">อายุการเป็นสมาชิก</option>
								</select>
							</div>
							<div class="col-sm-2">
								<select id="data_operation_` + index + `" class="form-control" style="">
									<option value="grester_than">มากกว่า</option>
									<option value="equal">เท่ากับ</option>
									<option value="less_than">น้อยกว่า</option>
								</select>
							</div>
							<div class="col-sm-2">
								<input type="text" id="cond_detail_data_val_add_` + index + `" class="form-control" value="">
							</div>
							<div class="col-sm-2">
								<input type="button" id="del-data-cond-btn_` + index + `" class="btn btn-danger del-data-cond-btn" data-index="` + index + `" value="ลบเงื่อนไข"/>
							</div>
						</div>`;
            $("#data-cond-div").append(html);
            $(this).attr("data-add-index", index);
        });

        $(document).on("click", ".edit-data-cond-btn",function () {
            let index = parseInt($("#data-cond-div-edit").attr("now-edit-index"));
            let html = `<div class="row" id="data-cond-div-edit-` + index + `">
							<label class="col-sm-3 control-label text-right" for="cond_data_edit">เทียบข้อมูล</label>
							<div class="col-sm-2">
								<select id="cond_data_edit_` + index + `" name="cond_data_edit" class="form-control" style="">
									<option value="age">อายุ</option>
									<option value="member_age">อายุการเป็นสมาชิก</option>
								</select>
							</div>
							<div class="col-sm-2">
								<select id="data_operation_edit_` + index + `" class="form-control" style="">
									<option value="grester_than">มากกว่า</option>
									<option value="equal">เท่ากับ</option>
									<option value="less_than">น้อยกว่า</option>
								</select>
							</div>
							<div class="col-sm-2">
								<input type="text" id="cond_detail_data_val_edit_` + index + `" class="form-control" value="">
							</div>
							<div class="col-sm-2">
								<input type="button" id="del-edit-data-cond-btn_` + index + `" class="btn btn-danger del-edit-data-cond-btn" data-edit-index="` + index + `" value="ลบเงื่อนไข"/>
							</div>
						</div>`;
            $("#data-cond-div-edit").append(html);
            let cond_count = parseInt($("#data-cond-div-edit").attr("cond-count"));
            document.getElementById("data-cond-div-edit").setAttribute("cond-count",cond_count+1);
            document.getElementById("data-cond-div-edit").setAttribute("now-edit-index",index + 1);
            $(this).attr("data-edit-index", index);
        });

        $(document).on("click", ".del-data-cond-btn", function () {
            let index = $(this).attr("data-index");
            console.log(this);
            $("#data-cond-div-" + index).remove();
        });

        $(document).on("click", ".del-edit-data-cond-btn", function () {
            console.log(this);
            let index = $(this).attr("data-edit-index");
            console.log(this);
            $("#data-cond-div-edit-" + index).remove();
        });
    });

    function format_the_number_decimal(ele) {
        var value = $('#' + ele.id).val();
        value = value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');
        var num = value.split(".");
        var decimal = '';
        var num_decimal = '';
        if (typeof num[1] !== 'undefined') {
            if (num[1].length > 2) {
                num_decimal = num[1].substring(0, 2);
            } else {
                num_decimal = num[1];
            }
            decimal = "." + num_decimal;
        }

        if (value != '') {
            if (value == 'NaN') {
                $('#' + ele.id).val('');
            } else {
                value = (num[0] == '') ? 0 : parseInt(num[0]);
                value = value.toLocaleString() + decimal;
                $('#' + ele.id).val(value);
            }
        } else {
            $('#' + ele.id).val('');
        }
    }

</script>       