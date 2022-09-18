<select name="<?php echo $id_input_district; ?>" id="<?php echo $id_input_district; ?>" class="form-control m-b-1">
    <option value="">เลือกตำบล</option>
    <?php foreach($district as $key => $value){ ?>
        <option value="<?php echo $value['district_id']; ?>"><?php echo $value['district_name']; ?></option>
    <?php }?>
</select>