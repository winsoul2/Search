<?php
    foreach($datas as $key => $value){ ?>
        <tr>
            <th scope="row" class="text-center"><?php echo $value['cremation_no']?></th>
            <th scope="row" class="text-center"><?php echo $value['member_cremation_id']?></th>
            <th scope="row" class="text-center"><?php echo $value['member_id']?></th>
            <td><?php echo $value['assoc_firstname'].' '.$value['assoc_lastname']; ?></td>
            <td align="right">
                <button data-member-cremation-raw-id="<?php echo $value['id']?>" data-member-cremation-id="<?php echo $value['member_cremation_id']?>" data-cremation-member-name="<?php echo $value['prename_full'].$value['assoc_firstname']." ".$value['assoc_lastname']?>" data-cremation-request-id="<?php echo $value['cremation_request_id']?>" style="padding: 2px 12px;"  id="<?php echo $value['cremation_request_id']?>" type="button" class="btn btn-info cre-modal-btn">เลือก</button>
            </td>
        </tr>
<?php } ?>