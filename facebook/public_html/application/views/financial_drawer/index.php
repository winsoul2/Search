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
            .text_center{
                text-align:center;
            }
        </style>
        <?php
        //                echo"<pre>";print_r($data);
        //                exit;
        ?>
        <h1 style="margin-bottom: 0">รายงานเคาน์เตอร์ ลิ้นชัก</h1>
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 padding-l-r-0">
            <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6 padding-l-r-0">
                <?php $this->load->view('breadcrumb'); ?>
            </div>
            <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 padding-l-r-0">

            </div>
        </div>
        <div class="row gutter-xs">
            <div class="col-xs-12 col-md-12">
                <div class="panel panel-body">
                    <div class="bs-example" data-example-id="striped-table">
                        <table class="table table-striped">
                            <thead>
                            <tr>
                                <th class = "font-normal" width="10%"> ลำดับที่ </th>
                                <th class = "font-normal" width="10%"> วันที่ </th>
                                <th class = "font-normal" width="20%"> รายการ </th>
                                <th class = "font-normal" width="15%"> ผู้ทำรายการ </th>
                                <th class = "font-normal" width="15%"> รายงานการทำรายการ </th>
                                <th class = "font-normal" width="15%"> รายงานสรุปยอดหน้าเค้าเตอร์ </th>

                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            $k_count=1;
                            //  echo"<pre>";print_r($sort_array);
                            $i=1;
                            foreach($data as $key => $row) {
                            ?>
                                <tr>
                                    <td><?php echo $i ?></td>
                                    <td><?php echo $this->center_function->ConvertToThaiDate($row['payment_date'],'1','0'); ?></td>
                                    <td width="35%" class="text_center"> รายงานเคาน์เตอร์ ลิ้นชัก </td>
                                    <td class="text_center"><?php echo $row['user_name'] ?></td>
                                    <td class="text_center">
                                        <button name="bt_add" id="bt_add" type="button" class="btn btn-primary" onclick="account_excel_tranction_voucher('<?php echo  $row['user_officer_id'] ?>','<?php echo  $row['payment_date'] ?>')" >
                                            <span>excel</span>
                                        </button>
                                    </td>
                                    <td class="text_center">
                                        <button name="bt_add_result" id="bt_add_resultห" type="button" class="btn btn-primary" onclick="account_excel_tranction_voucher_result('<?php echo  $row['user_officer_id'] ?>','<?php echo  $row['payment_date'] ?>')" >
                                            <span>excel สรุป</span>
                                        </button>
                                    </td>
                                </tr>
                            <?php
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
<div class="modal-body">
    <form action="<?php echo base_url(PROJECTPATH.'/financial_drawer/account_excel_financial_drawer'); ?>" method="post" id="from_excel_day">
        <input id="user_officer_id" name="user_officer_id" type="hidden" value="">
        <input id="payment_date" name="payment_date" type="hidden" value="">
    </form>
</div>

<div class="modal-body">
    <form action="<?php echo base_url(PROJECTPATH.'/financial_drawer/account_excel_financial_drawer_result'); ?>" method="post" id="from_excel_day_result">
        <input id="user_officer_id_result" name="user_officer_id_result" type="hidden" value="">
        <input id="payment_date_result" name="payment_date_result" type="hidden" value="">
    </form>
</div>
<?php
$link = array(
    'src' => PROJECTJSPATH.'assets/js/financial_drawer.js',
    'type' => 'text/javascript'
);
echo script_tag($link);
?>
