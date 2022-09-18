<select name="<?php echo $id_input_amphur; ?>" id="<?php echo $id_input_amphur; ?>" class="form-control m-b-1" onchange="change_amphur('<?php echo $id_input_amphur; ?>','<?php echo $district_space; ?>','<?php echo $id_input_district; ?>')">
    <option value="">เลือกอำเภอ</option>
    <?php foreach($amphur as $key => $value){ ?>
        <option value="<?php echo $value['amphur_id']; ?>"><?php echo $value['amphur_name']; ?></option>
    <?php }?>
</select>