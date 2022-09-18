<div class="layout-content">
    <div class="layout-content-body">
        <style>
            .center {
                text-align: center;
            }
            .modal-dialog-account {
                margin:auto;
                margin-top:7%;
            }
            .input-with-icon {
                margin-bottom: 5px;
            }

            .input-with-icon .form-control{
                padding-left: 40px;
            }
            .modal_data_input{
                margin-left:-5px;
            }
            .blockOverlay {
                z-index:6000 !important;
            }
            .blockPage {
                z-index:6001 !important;
            }
        </style>
        <h1 style="margin-bottom: 0">โอนเงิน สฌ.สสอค.</h1>
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 padding-l-r-0">
            <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6 padding-l-r-0">
                <?php $this->load->view('breadcrumb'); ?>
            </div>
        </div>
        <div class="row gutter-xs">
            <div class="col-xs-12 col-md-12">
                <div class="panel panel-body" style="padding-top:0px !important;">
                    <h3></h3>
                    <table class="table table-bordered table-striped table-center">
                        <thead>
                        <tr class="bg-primary">
                            <th style="width:150px;">ลำดับ</th>
                            <th style="width:250px;">เลขณาปากิจ</th>
                            <th>ชื่อ-สกุล</th>
                            <th>ผู้ขอรับเงิน</th>
                            <th>ยอดเงิน</th>
                            <th>สถานะ</th>
                            <th>จัดการ</th>
                        </tr>
                        </thead>
                        <tbody id="table_first">
                        <?php
                            if($row){
                                $j = 1;
                                foreach ($row as $key => $value){
                        ?>
                            <tr>
                                <td align="center"><?php echo $j++;?></td>
                                <td align="center"><?php echo $value['member_cremation_id'];?></td>
                                <td align="left"><?php echo $value['assoc_firstname']." ".$value['assoc_lastname'] ;?></td>
                                <td align="left"><?php echo $value[$value["receiver"]];?></td>
                                <td align="right"><?php echo empty($value["resign_id"]) ? number_format($value["cremation_balance_amount"],2) : number_format($value["adv_payment_balance"],2);?></td>
                                <td align="center"><?php echo $transfer_type[$value['transfer_status']];?></td>
                                <td>
                                    <?php
                                    if(@$value['transfer_status'] == '1'){
                                        ?>
                                        <a class="btn btn-info" id="approve_<?php echo @$value['cremation_receive_id']; ?>_1" title="แสดงรายการอนุมัติขอรับเงินฌาปนกิจสงเคราะห์" style="cursor: pointer;"  href="<?php echo base_url('cremation/receipt_form_pdf/'.$value['voucher_no']);?>" target="_blank">
                                            พิมพ์ใบเสร็จ
                                        </a>
                                    <?php }else if (empty($value["resign_id"])){ ?>
                                    <button class="btn btn-primary" onclick="transfer_cremation('<?php echo @$value['cremation_receive_id']; ?>','add')">โอนเงิน</button>
                                    <?php } else { ?>
                                    <button class="btn btn-primary" onclick="transfer_resign_cremation('<?php echo @$value['resign_id']; ?>','add')">โอนเงิน</button>
                                    <?php }?>
                                </td>
                            </tr>
                        <?php } } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php echo @$paging ?>
    </div>
</div>

<?php $this->load->view('cremation/cremation_approve_receive_modal'); ?>
<?php
$link = array(
    'src' => 'assets/js/coop_cremation_approve_receive.js',
    'type' => 'text/javascript'
);
echo script_tag($link);
?>