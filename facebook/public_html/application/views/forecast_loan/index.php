<div class="layout-content">
    <div class="layout-content-body">
        <h1 style="margin-bottom: 0">คำนวณเงินกู้</h1>
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 padding-l-r-0">
            <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 padding-l-r-0">
                <?php $this->load->view('breadcrumb'); ?>
            </div>
        </div>
        <div class="row gutter-xs">
            <div class="col-xs-12 col-md-12">
                <div class="panel panel-body">
                    <div class="panel-body" style="padding:0px; margin:0px;">
                        <h3 style="padding:0px; margin:0px;">คำนวณเงินกู้</h3>
                        <input type="hidden" id="datenow" value="<?php echo date('Y-m-d'); ?>">
                        <input type="hidden" id="datenow_is" value="<?php echo date('Y-m-d H:i:s'); ?>">
                        <input type="hidden" name="updatetimestamp" id="updatetimestamp">
                        <div class="g24-col-sm-24 modal_data_input">
                            <label class="g24-col-xs-6 g24-col-sm-6 control-label ">วงเงินกู้</label>
                            <div class="g24-col-xs-14 g24-col-sm-5">
                                <input type="text" id="loan" onkeyup="format_the_number_decimal(this);" class="form-control form-loan inline-block loan"/>
                            </div>
                            <label class="g24-col-xs-4 g24-col-sm-1 control-label ">บาท</label>
                            <label class="g24-col-xs-6 g24-col-sm-5 control-label ">อัตราดอกเบี้ย</label>
                            <div class="g24-col-xs-14 g24-col-sm-5">
                                <input type="number" id="interest" class="form-control form-loan interest_rate" step="0.01" value="">
                            </div>
                            <label class="g24-col-xs-4 g24-col-sm-1 control-label ">%</label>
                        </div>
                        <div class="g24-col-sm-24 modal_data_input">
                            <label class="g24-col-sm-6 control-label ">จำนวน</label>
                            <div class="g24-col-sm-5">
                                <select name="data[coop_loan][period_type]" id="period_type" class="form-control">
                                    <option value="1"> งวดที่ต้องการผ่อน </option>
                                    <option value="2"> เงินที่ต้องการผ่อนต่องวด </option>
                                </select>
                            </div>
                            <div class="g24-col-sm-5">
                                <input type="nuumber" id="period" onkeyup="format_the_number_decimal(this)" class="form-control form-loan inline-block " data-meta="max_period" data-optional="loan" />
                                <input type="nuumber" id="period_amount_bath" onkeyup="format_the_number_decimal(this)" class="form-control form-loan inline-block" />
                                <input type="hidden" id="period_old" onkeyup="format_the_number_decimal(this)" class="form-control form-loan inline-block" />
                            </div>
                            <label class="g24-col-sm-1 control-label " id="type_period"></label>
                        </div>
                        <div class="g24-col-sm-24 modal_data_input">
                            <label class="g24-col-sm-6 control-label ">ประเภทการชำระเงิน</label>
                            <div class="g24-col-sm-5">
                                <select id="pay_type" name="data[coop_loan][pay_type]"  class="form-control">
                                    <option value="1" selected>ชำระต้นเท่ากันทุกงวด</option>
                                    <option value="2">ชำระยอดเท่ากันทุกงวด</option>
                                </select>
                            </div>
                        </div>
                        <div class="g24-col-sm-24 modal_data_input">
                            <div class=""></div>
                            <div class="g24-col-sm-offset-6 g24-col-sm-6">
                                <button class="btn btn-primary" onclick="calc_loan()"> คำนวน </button>
                            </div>
                        </div>
                        <h3 style="padding:0px; margin:0px;">ประมาณการ</h3>
                        <div class="g24-col-sm-24 modal_data_input">
                            <label class="g24-col-sm-6 control-label" >งวดชำระต่อเดือน</label>
                            <div class="g24-col-sm-5">
                                <div class="form-group">
                                    <input class="form-control estimate_value" type="text" id="estimate_receive_money" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="g24-col-sm-24 modal_data_input">
                            <label class="g24-col-sm-6 control-label" >ดอกเบี้ย</label>
                            <div class="g24-col-sm-5">
                                <div class="form-group">
                                    <input class="form-control estimate_value" type="text" id="estimate_interest" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="g24-col-sm-24 modal_data_input">
                            <label class="g24-col-sm-6 control-label" >เงินต้น</label>
                            <div class="g24-col-sm-5">
                                <div class="form-group">
                                    <input class="form-control estimate_value" type="text" id="estimate_principle" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
$v = date('YmdHis');
$link = array(
    'src' => PROJECTJSPATH.'assets/js/forecast_loan.js?v='.$v,
    'type' => 'text/javascript'
);
echo script_tag($link);
?>
