<?php if($_POST['search_type'] == 1){ ?>
<label class="col-sm-3 control-label">เลขที่สัญญา</label>
<div class="col-sm-9">
    <div class="form-group">
        <select name="search_loan_list" id="search_loan_list" class="form-control m-b-1">
            <option value="">กรุณาเลือกเลขที่สัญญา</option>
            <?php foreach($lish as $key => $value){ ?>
                <option value="<?php echo $value['id']; ?>"><?php echo $value['loan_name']." ".$value['contract_number']; ?></option>
            <?php } ?>
        </select>
    </div>
</div>
<?php } ?>
<?php if($_POST['search_type'] == 1){ ?>
<label class="col-sm-3 control-label">รูปแบบการหัก</label>
<div class="col-sm-9">
    <div class="form-group">
        <select name="search_deduct" id="search_deduct" class="form-control m-b-1">
            <option value="">กรุณาเลือกรูปแบบการหัก</option>
            <option value="1">ดอกเบี้ย</option>
            <option value="2">เงินต้น</option>
        </select>
    </div>
</div>
<?php } ?>
<?php if($_POST['search_type'] == 2){ ?>
<label class="col-sm-3 control-label">เลขบัญชี</label>
<div class="col-sm-9">
    <div class="form-group">
        <select name="search_account_list" id="search_account_list" class="form-control m-b-1">
            <option value="">กรุณาเลือกเลขที่สัญญา</option>
            <?php foreach($lish as $key => $value){ ?>
                <option value="<?php echo $value['account_id']; ?>"><?php echo $value['type_name'].' '.$value['account_id']; ?></option>
            <?php } ?>
        </select>
    </div>
</div>
<?php } ?>