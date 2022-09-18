<select name="dividend_bank_branch_id[]" id="dividend_bank_branch_id" class="form-control m-b-1 group-bank-right" onchange="change_branch()">
    <option value="">เลือกสาขาธนาคาร</option>
    <?php foreach($bank_branch as $key => $value){ ?>
        <option value="<?php echo $value['branch_id']; ?>"><?php echo $value['branch_name']; ?></option>
    <?php }?>
</select>