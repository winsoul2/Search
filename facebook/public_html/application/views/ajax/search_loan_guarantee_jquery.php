<?php
    $data_row = @$_POST["data_row"];
    if($data=="" || count($data)==0){
        ?>
            <tr>
                <td colspan=4 align="center"><strong>ไม่พบข้อมูล</strong></td>
            </tr>
        <?php
    }
    foreach($data as $key => $value){ ?>
        <tr>
            
            <td scope="row"><?php echo $value['firstname_th'].' '.$value['lastname_th']; ?></td>
            <!-- <th><?php echo $value['age']?> ปี</th> -->
            <td><?php echo $value['contract_number']; ?></td>
            <td align="right">
				<button style="padding: 2px 12px;" id="<?php echo $value['member_id']?>" type="button" class="btn btn-info" onclick="get_data('<?php echo $value['member_id']; ?>','<?php echo $value['firstname_th'].' '.$value['lastname_th']; ?>', '<?=$value['department_name']?>', '<?=$value['level_name']?>')">เลือก</button>
            </td>
        </tr>
<?php } ?>