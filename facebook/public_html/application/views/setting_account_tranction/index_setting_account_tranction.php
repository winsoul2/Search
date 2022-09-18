<!--  เอาไว้ตั้งค่าระบบว่า การบันทึกบัญชีแต่ละแระเภทนั้น มีรายละเอียดอย่างไง และกำหนดว่าแต่ละ process มีการบันทึกบัญชีอย่างไร โดยอ้างอิงตาราง  setting_account_tranction เพื่อเก็บข้อมูลหลัก
  และใช้ตาราง coop_account_match  เพื่อระบุบัญชีว่าต้องบันทึกบัญชีในฝังบัญชีอะไร-->


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
                margin-top:7%;
            }
            .control-label{
                text-align:right;
                padding-top:5px;
            }
        </style>
        <h1 style="margin-bottom: 0">จัดการการบันทึกบัญชี</h1>
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 padding-l-r-0">
            <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6 padding-l-r-0">
                <?php $this->load->view('breadcrumb'); ?>
            </div>

            <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 padding-l-r-0">

                <button class="btn btn-primary btn-lg bt-add" type="button" onclick="add_account_chart()">
                    <span class="icon icon-plus-circle"></span>
                    เพิ่มรายการ
                </button>
                    <a href="<?php echo base_url('/setting_account_tranction/sub_index_setting_account_match'); ?>" class="btn btn-primary btn-lg bt-add" style="margin-right: 30px;" >รายการตั้งค่าฝังบัญชี</a>
            </div>
        </div>
        <div class="row gutter-xs">
            <div class="col-xs-12 col-md-12">
                <div class="panel panel-body">
                    <div class="bs-example" data-example-id="striped-table">
                        <table class="table table-striped">
                            <thead>
                            <tr>
                                <th class = "font-normal" width="5%">#</th>
                                <th class = "font-normal" width="30%"> รายการบันทึกบัญชี </th>
                                <th class = "font-normal text-left"> ที่แสดงในบัญชี </th>
                                <th class = "font-normal text-left"> มาจากขั้นตอนใด </th>
                                <th class = "font-normal text-left"> อ้างอิงการบันทึกบัญชี </th>
                                <th class = "font-normal text-left"> อ้างอิงการค้นหาฝังบัญชี </th>
                                <th class = "font-normal"> จัดการ </th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            if(!empty($rs)){
                                foreach(@$rs as $key => $row){
                                    ?>
                                    <tr>
                                        <td scope="row"><?php echo $i++; ?></td>
                                        <td><?php echo @$row['setting_name_list']; ?></td>
                                        <td class="text-left"><?php echo @$row['description']; ?></td>
                                        <td class="text-left"><?php echo @$row['process']; ?></td>
                                        <td class="text-left"><?php echo @$row['ref_type']; ?></td>
                                        <td class="text-left"><?php echo @$row['match_type']; ?></td>
                                        <td>
                                            <?php if(@$row['is_fix']!='1'){ ?>
                                                <a href="#" onclick="edit_account_chart('<?php echo @$row['setting_id']; ?>','<?php echo @$row['setting_name_list']; ?>','<?php echo @$row['description']; ?>','<?php echo @$row['process']; ?>','<?php echo @$row['ref_type']; ?>','<?php echo @$row['match_type']; ?>')">แก้ไข</a> |
                                                <a href="#" onclick="del_account_tranction('<?php echo @$row['setting_id']; ?>')" class="text-del"> ลบ </a>
                                            <?php } ?>
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
    </div>
</div>

<div id="add_account_chart" tabindex="-1" role="dialog" class="modal fade">
    <div class="modal-dialog modal-dialog-account">
        <div class="modal-content">
            <div class="modal-header modal-header-confirmSave">
                <h2 class="modal-title" id="modal_title">เพิ่มผังบัญชี</h2>
            </div>
            <div class="modal-body">
                <form action="<?php echo base_url('/setting_account_tranction/coop_account_setting_save'); ?>" method="post" id="form1">
                    <input type="hidden" name="action_delete" id="action_delete" class="type_input" value="">
                    <input id="old_account_chart_id" name="old_account_chart_id" class="type_input" type="hidden" value="">
                    <div class="form-group ">
                        <label class="col-sm-4 control-label">รายการบันทึกบัญชี</label>
                        <div class="col-sm-8">
                            <input id="setting_name_list" name="setting_name_list" class="form-control m-b-1 type_input" type="text" value="">
                        </div>
                        <label class="col-sm-4 control-label">ที่แสดงในบัญชี</label>
                        <div class="col-sm-8">
                            <input id="description" name="description" class="form-control m-b-1 type_input" type="text" value="">
                        </div>
                        <label class="col-sm-4 control-label">มาจากขั้นตอนใด</label>
                        <div class="col-sm-8">
                            <input id="process" name="process" class="form-control m-b-1 type_input" type="text" value="">
                        </div>
                        <label class="col-sm-4 control-label">อ้างอิงการบันทึกบัญชี</label>
                        <div class="col-sm-8">
                            <input id="ref_type" name="ref_type" class="form-control m-b-1 type_input" type="text" value="">
                        </div>
                        <label class="col-sm-4 control-label">อ้างอิงการค้นหาฝังบัญชี</label>
                        <div class="col-sm-8">
                            <input id="match_type" name="match_type" class="form-control m-b-1 type_input" type="text" value="">
                        </div>
                    </div>
                    <div class="form-group text-center">
                        <button type="button" class="btn btn-primary min-width-100" onclick="form_submit()">ตกลง</button>
                        <button class="btn btn-danger min-width-100" type="button" onclick="close_modal('add_account_chart')">ยกเลิก</button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>


<?php
$link = array(
    'src' => PROJECTJSPATH.'assets/js/setting_account_tranction.js',
    'type' => 'text/javascript'
);
echo script_tag($link);
?>