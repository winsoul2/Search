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
            .control-label{
                text-align:right;
                padding-top:5px;
            }
            .text_left{
                text-align:left;
            }
            .text_right{
                text-align:right;
            }
        </style>
        <?php

        $data_account_detail = array();

        foreach($data as $key => $row) {
            $account_datetime ='';
            $account_datetime =  explode(" ",$row['account_datetime']);
            foreach($row as $key2 => $row_detail){
                $account_datetime ='';
                $account_datetime =  explode(" ",$row_detail['account_datetime']);
                $data_account_detail[$account_datetime[0]][$row_detail['account_description']][$row_detail['account_chart_id'].$row_detail['account_type']]['account_chart_id'] = $row_detail['account_chart_id'];
                $data_account_detail[$account_datetime[0]][$row_detail['account_description']][$row_detail['account_chart_id'].$row_detail['account_type']]['account_chart'] = $row_detail['account_chart'];
                $data_account_detail[$account_datetime[0]][$row_detail['account_description']][$row_detail['account_chart_id'].$row_detail['account_type']]['account_type'] = $row_detail['account_type'];
                $data_account_detail[$account_datetime[0]][$row_detail['account_description']][$row_detail['account_chart_id'].$row_detail['account_type']]['account_amount'] += $row_detail['account_amount'];
            }
        }


        ?>
        <h1 style="margin-bottom: 0">รายการชำระ</h1>
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 padding-l-r-0">
            <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6 padding-l-r-0">
                <?php $this->load->view('breadcrumb'); ?>
            </div>
            <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 padding-l-r-0">
                <button class="btn btn-primary btn-lg bt-add" type="button" onclick="add_account()">
                    <span class="icon icon-plus-circle"></span>
                    เพิ่มรายการ
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
                                <th class = "font-normal" width="20%">วันที่</th>
                                <th class = "font-normal"> รายการ </th>
                                <th class = "font-normal" width="15%"> รหัสบัญชี </th>
                                <th class = "font-normal" width="15%"> เดบิต </th>
                                <th class = "font-normal" width="15%"> เครดิต </th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            $k_count=1;
                            //  echo"<pre>";print_r($sort_array);

                            $i=1;
                            foreach($data_account_detail as $key_main => $row) {
//                                echo"<pre>";print_r($row);
                                foreach($row as $key => $row) {
                                    $i=1;
                                        foreach($row as $key2 => $row_detail){
                                            ?>
                                            <tr>
                                                <td><?php echo $i=='1'?$this->center_function->ConvertToThaiDate($key_main,'1','0'):''; ?></td>
                                                <td width="35%" class="text_left">
                                                    <?php echo $row_detail['account_type']=='debit'?$row_detail['account_chart']:$space.$row_detail['account_chart']; ?>
                                                </td>
                                                <td><?php echo $row_detail['account_chart_id']; ?></td>
                                                <td class="text_right"><?php echo $row_detail['account_type']=='debit'?number_format($row_detail['account_amount'],2):''; ?></td>
                                                <td class="text_right"><?php echo $row_detail['account_type']=='credit'?number_format($row_detail['account_amount'],2):''; ?></td>

                                                <td class="text_right">

                                                </td>
                                            </tr>
                                            <?php
                                            $i++;
                                        }
                                        ?>
                                        <tr>
                                            <td></td>
                                            <td class="text_left"><?php echo $key;?></td>
                                            <td></td>
                                            <td class="text_right"></td>
                                            <td class="text_right"></td>
                                        </tr>
                                        <?php
                                        $i++;

                                    $k_count++;

                                }
                                $k_count++;
                                $i++;
                            }
                            ?>

                            </tbody>
                        </table>
                    </div>
                </div>
                <?php echo $paging ?>
            </div>
        </div>
    </div>
</div>
<div id="add_account" tabindex="-1" role="dialog" class="modal fade">
    <div class="modal-dialog modal-dialog-account">
        <div class="modal-content">
            <div class="modal-header modal-header-confirmSave">
                <h2 class="modal-title">บันทึกรายการบัญชี</h2>
            </div>
            <div class="modal-body">
                <form action="<?php echo base_url(PROJECTPATH.'/account/account_save'); ?>" method="post" id="form1">
                    <input id="input_number" type="hidden" value="0">
                    <div class="row">
                        <div class="form-group">
                            <label class="col-sm-3 control-label">วันที่</label>
                            <div class="col-sm-3">
                                <div class="input-with-icon">
                                    <div class="form-group">
                                        <input id="account_datetime" name="data[coop_account][account_datetime]" class="form-control m-b-1 type_input" type="text" value="<?php echo $this->center_function->mydate2date(date('Y-m-d')); ?>" data-date-language="th-th" style="padding-left:38px;">
                                        <span class="icon icon-calendar input-icon m-f-1"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group">
                            <label class="col-sm-3 control-label">รายละเอียดรายการบัญชี</label>
                            <div class="col-sm-6">
                                <input id="account_description" name="data[coop_account][account_description]" class="form-control m-b-1 type_input" type="text" value="">
                            </div>
                        </div>
                    </div>
                    <div class="form-group text-right">
                        <button type="button" id="btn_debit" class="btn btn-primary min-width-100 btn-width-auto" onclick="add_account_detail('debit')">เพิ่มรายการเดบิต</button>
                        <button type="button" id="btn_credit" class="btn btn-primary min-width-100 btn-width-auto" onclick="add_account_detail('credit')">เพิ่มรายการเครดิต</button>
                    </div>
                    <div class="bs-example" data-example-id="striped-table">
                        <table class="table table-striped">
                            <thead>
                            <tr>
                                <th class = "font-normal" width="40%"> รหัสบัญชี </th>
                                <th class = "font-normal" width="30%"> เดบิต </th>
                                <th class = "font-normal" width="30%"> เครดิต </th>
                            </tr>
                            </thead>
                            <tbody id="account_data">
                            </tbody>
                        </table>
                    </div>
                    <div class="form-group text-center">
                        <button type="button" class="btn btn-primary min-width-100" onclick="form_submit()">ตกลง</button>
                        <button class="btn btn-danger min-width-100" type="button" onclick="clear_modal()">ยกเลิก</button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>
<?php
$link = array(
    'src' => PROJECTJSPATH.'assets/js/account.js',
    'type' => 'text/javascript'
);
echo script_tag($link);
?>
