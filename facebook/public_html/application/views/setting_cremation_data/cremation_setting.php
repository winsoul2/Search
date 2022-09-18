<div class="layout-content">
    <div class="layout-content-body">
        <style>
            label{
                padding-top:7px;
            }
            .control-label{
                padding-top:7px;
                text-align:right;
            }
            .control-label_2{
                padding-top:7px;
            }
            .center{
                text-align:center;
            }
        </style>
        <h1 style="margin-bottom: 0">ฌาปนกิจสงเคราะห์</h1>
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 padding-l-r-0">
            <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6 padding-l-r-0">
                <?php $this->load->view('breadcrumb'); ?>
            </div>
        </div>
        <div class="row gutter-xs">
            <div class="col-xs-12">
                <div class="panel panel-body">
                    <form action="<?php echo base_url(PROJECTPATH.'/setting_cremation_data/save_cremation_setting');?>" method="POST" id="form1" data-toggle="validator" role="form">
                        <input type="hidden" name="cremation_id" value="<?php echo $_GET['cremation_id']?>">
                        <input type="hidden" name="cremation_detail_id" value="<?php echo $_GET['cremation_detail_id']?>">
                        <div class="row m-b-1">
                            <div class="form-group">
                                <label class="control-label col-sm-5">สมาชิกสามัญ อายุต้องไม่เกิน</label>
                                <div class="col-sm-2">
                                    <input type="text" class="form-control check_number required" name="ordinary_member_age_limit"  value="<?php echo $data['ordinary_member_age_limit']?>">
                                </div>
                                <label class="control-label_2 col-sm-2">ปี</label>
                                
                            </div>
                        </div>
                        <div class="row m-b-1">
                            <div class="form-group">
                                <label class="control-label col-sm-5">สมาชิกสมทบ อายุต้องไม่เกิน</label>
                                <div class="col-sm-2">
                                    <input type="text" class="form-control check_number required" name="associate_member_age_limit"  value="<?php echo $data['associate_member_age_limit']?>">
                                </div>
                                <label class="control-label_2 col-sm-2">ปี</label>
                                
                            </div>
                        </div>
                        <div class="row m-b-1">
                            <div class="form-group">
                                <label class="control-label col-sm-5">ค่าสมัคร</label>
                                <div class="col-sm-2">
                                    <input type="text" class="form-control check_number required" name="application_fee"  value="<?php echo $data['application_fee']?>">
                                </div>
                                <label class="control-label_2 col-sm-2">บาท</label>
                            </div>
                        </div>
                        <div class="row m-b-1">
                            <div class="form-group">
                                <label class="control-label col-sm-5">ค่าบำรุงรายปี</label>
                                <div class="col-sm-2">
                                    <input type="text" class="form-control check_number required" name="maintenance_fee"  value="<?php echo $data['maintenance_fee']?>">
                                </div>
                                <label class="control-label_2 col-sm-2">บาท</label>
                            </div>
                        </div>
                        <div class="row m-b-1 <?php if(!empty($setting->hide_finance_period_type_edit)) echo 'hide';?>">
                            <label class="col-sm-5 control-label text-right" for="maintenance">ประเภทการเรียกเก็บ</label>
                            <div class="col-sm-7">
                                <div class="form-group">
                                    <label class=""><input type="radio" class="finance_period_type" id="finance_period_type_1" name="finance_period_type" value="1" <?php echo $data['finance_period_type']=='1' || empty($data['finance_period_type']) ?'checked':''; ?>>รายเดือน</label>
                                    &nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp
                                    <label class=""><input type="radio" class="finance_period_type" id="finance_period_type_2" name="finance_period_type" value="2" <?php echo $data['finance_period_type']=='2'?'checked':''; ?>>รายปี</label>
                                </div>
                            </div>
                        </div>
                        <div class="row m-b-1 <?php if(!empty($setting->hide_finance_collect_type)) echo 'hide';?>">
                            <label class="col-sm-5 control-label text-right" for="maintenance">ประเภทการคำนวนเรียกเก็บ</label>
                            <div class="col-sm-7">
                                <div class="form-group">
                                    <label class=""><input type="radio" class="finance_collect_type" id="finance_collect_type_1" name="finance_collect_type" value="1" <?php echo $data['finance_collect_type']=='1' || empty($data['finance_collect_type']) ?'checked':''; ?>>เติมเต็มเงินสงเคราะห์ล่วงหน้า</label>
                                    &nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp
                                    <label class=""><input type="radio" class="finance_collect_type" id="finance_collect_type_2" name="finance_collect_type" value="2" <?php echo $data['finance_collect_type']=='2'?'checked':''; ?>>เรียกเก็บจากค่าคงตัว</label>
                                </div>
                            </div>
                        </div>
                        <div class="row m-b-1 dividend_deduction_div <?php if(!empty($setting->hide_finance_period_type_edit)) echo 'hide';?>">
                            <div class="form-group">
                                <label class="control-label col-sm-5"></label>
                                <div class="col-sm-2">
                                    <input type="checkbox" id="dividend_deduction" name="dividend_deduction" <?php echo !empty($data["dividend_deduction"]) ? "checked" : "";?> value="1">
                                    <label>ชำระเงินจากการหักเงินปันผลเฉลี่ยคืน</label>
                                </div>
                            </div>
                        </div>
                        <div class="row m-b-1 advance_pay_div">
                            <div class="form-group">
                                <label class="control-label col-sm-5">เงินสงเคราะห์ล่วงหน้า</label>
                                <div class="col-sm-2">
                                    <input type="text" class="form-control check_number" name="advance_pay" id="advance_pay" value="<?php echo $data['advance_pay']?>">
                                </div>
                                <label class="control-label_2 col-sm-2">บาท</label>
                            </div>
                        </div>
                        <div class="row m-b-1 finance_amount_div">
                            <div class="form-group">
                                <label class="control-label col-sm-5" for="finance_amount">จำนวนเงินเรียกเก็บ</label>
                                <div class="col-sm-2">
                                    <input type="text" class="form-control check_number" name="finance_amount" id="finance_amount" value="<?php echo $data['finance_amount']?>">
                                </div>
                                <label class="control-label_2 col-sm-2">บาท</label>
                            </div>
                        </div>
                        <div class="row m-b-1">
                            <div class="form-group">
                                <label class="control-label col-sm-5">ค่าดำเนินการสมาคม</label>
                                <div class="col-sm-2">
                                    <input type="text" class="form-control check_number required" name="action_fee_percent"  value="<?php echo $data['action_fee_percent']?>">
                                </div>
                                <label class="control-label_2 col-sm-2">%</label>
                            </div>
                        </div>
                        <div class="row m-b-1">
                            <div class="form-group">
                                <label class="control-label col-sm-5">เงินฌาปนกิจที่จะได้รับ</label>
                                <div class="col-sm-2">
                                    <input type="text" class="form-control check_number required" name="money_received_per_member"  value="<?php echo $data['money_received_per_member']?>">
                                </div>
                                <label class="control-label_2 col-sm-2">คูณ จำนวนสมาชิก</label>
                            </div>
                        </div>
                        <div class="row m-b-1">
                            <label class="col-sm-5 control-label text-right" for="start_date">มีผลวันที่</label>
                            <div class="col-sm-2">
                                <div class="form-group">
                                    <input id="start_date" name="start_date" class="form-control m-b-1 required" style="padding-left: 50px;"  type="text" value="<?php echo date('d/m/Y', strtotime($data['start_date']." +543 year"));?>" data-date-language="th-th" required  title="กรุณาเลือก มีผลวันที่">
                                    <span class="icon icon-calendar input-icon m-f-1"></span>
                                </div>
                            </div>
                        </div>
                        <div class="row m-b-1">
                            <div class="form-group center">
                                <button type="button" class="btn btn-primary" style="width:100px" onclick="submit_form()"> ยืนยัน </button>
                                <button type="button" class="btn btn-danger" style="width:100px" onclick="go_back('<?php echo @$row['cremation_id']; ?>')"> ยกเลิก </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
$link = array(
    'src' => PROJECTJSPATH.'assets/js/cremation_setting.js',
    'type' => 'text/javascript'
);
echo script_tag($link);
?>