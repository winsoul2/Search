<div class="panel-body" style="padding:0px; margin:0px;">
    <h3 style="padding:0px; margin:0px;">คำนวณเงินกู้</h3>
    <input type="hidden" id="datenow" value="<?php echo date('Y-m-d'); ?>">
    <input type="hidden" id="datenow_is" value="<?php echo date('Y-m-d H:i:s'); ?>">
    <input type="hidden" name="updatetimestamp" id="updatetimestamp">
    <div class="g24-col-sm-24 modal_data_input">
        <label class="g24-col-sm-6 control-label ">วงเงินกู้</label>
        <div class="g24-col-sm-5">
            <input type="text" id="loan" onBlur="copy_value('loan', 'loan_amount');re_already_cal();check_share();check_loan_deduct();check_life_insurance();" onkeyup="format_the_number_decimal(this);" class="form-control form-loan inline-block loan"/>
        </div>
        <label class="g24-col-sm-1 control-label ">บาท</label>
        <label class="g24-col-sm-5 control-label ">อัตราดอกเบี้ย</label>
        <div class="g24-col-sm-5">
            <input type="number" id="interest" class="form-control form-loan interest_rate" step="0.01" value="" readonly>
        </div>
        <label class="g24-col-sm-1 control-label ">%</label>
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
            <input type="nuumber" id="period" onkeyup="format_the_number(this)" class="form-control form-loan inline-block " data-meta="max_period" data-optional="loan" />
            <input type="nuumber" id="period_amount_bath" onkeyup="format_the_number(this)" class="form-control form-loan inline-block" />
            <input type="hidden" id="period_old" onkeyup="format_the_number(this)" class="form-control form-loan inline-block" />
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
        <label class="g24-col-sm-6 control-label " style="display: none;">วันที่เริ่มคำนวณ</label>
        <div class="input-with-icon g24-col-sm-5">
            <div class="form-group"  style="display: none;">
                <input id="apply_date" name="apply_date" class="form-control m-b-1" style="padding-left: 50px;" type="text" value="<?php echo $this->center_function->mydate2date(date('Y-m-d')); ?>" data-date-language="th-th" required title="" >
                <span class="icon icon-calendar input-icon m-f-1"></span>
            </div>
        </div>
    </div>
    <h3 style="padding:0px; margin:0px;">ประมาณการ</h3>
    <div class="g24-col-sm-24 modal_data_input">
        <label class="g24-col-sm-6 control-label" >ยอดเงินที่จะได้รับโดยประมาณ</label>
        <div class="g24-col-sm-5">
            <div class="form-group">
                <input class="form-control estimate_value" type="text" name="data[loan_deduct_profile][estimate_receive_money]" id="estimate_receive_money" readonly>
            </div>
        </div>
        <label class="g24-col-sm-6 control-label" >ได้รับเงินประมาณ วันที่</label>
        <div class="g24-col-sm-5">
            <div class="form-group">
                <div class="input-with-icon">
                    <div class="form-group">
                        <input id="date_receive_money" name="data[loan_deduct_profile][date_receive_money]" class="form-control m-b-1" style="padding-left: 50px;" type="text" value="<?php echo $this->center_function->mydate2date(date('Y-m-d'));?>" data-date-language="th-th" required title="" >
                        <span class="icon icon-calendar input-icon m-f-1"></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="g24-col-sm-24 modal_data_input">
        <label class="g24-col-sm-6 control-label" >วันที่ชำระเงินงวดแรก</label>
        <div class="g24-col-sm-5">
            <div class="form-group">
                <input class="form-control estimate_value" type="text" id="date_first_period_label" readonly>
                <input type="hidden" class="estimate_value" name="data[loan_deduct_profile][date_first_period]" id="date_first_period" readonly>
            </div>
        </div>
        <label class="g24-col-sm-6 control-label" >ดอกเบี้ยในการชำระงวดแรก</label>
        <div class="g24-col-sm-5">
            <div class="form-group">
                <input class="form-control estimate_value" type="text" name="data[loan_deduct_profile][first_interest]" id="first_interest" readonly>
            </div>
        </div>
    </div>
    <div class="g24-col-sm-24 modal_data_input">
        <label class="g24-col-sm-6 control-label" >คงเหลือ</label>
        <div class="g24-col-sm-5">
            <div class="form-group">
                <input class="form-control estimate_value" type="text" name="salary_balance" id="salary_balance" readonly>
            </div>
        </div>
        <label class="g24-col-sm-6 control-label" >คิดเป็น</label>
        <div class="g24-col-sm-5">
            <div class="form-group">
                <input class="form-control estimate_value" type="text" name="percent_salary_balance" id="percent_salary_balance" readonly>
            </div>
        </div>
        <label class="g24-col-sm-1 control-label ">%</label>
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
