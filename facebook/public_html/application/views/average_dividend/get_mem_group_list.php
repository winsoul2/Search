    <option value="0" selected>เลือกข้อมูลทั้งหมด</option>
    <?php foreach($mem_group as $key => $value){ ?>
        <option value="<?php echo $value['id']; ?>"><?php echo $value['mem_group_name']; ?></option>
    <?php }?>