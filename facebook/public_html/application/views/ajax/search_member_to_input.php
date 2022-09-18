<?php
    foreach($data as $key => $value){ ?>
        <tr>
            <th scope="row"><?php echo $value['member_id']?></th>
            <td><?php echo $value['firstname_th'].' '.$value['lastname_th']; ?></td>
            <td align="right">
				<button style="padding: 2px 12px;"  id="<?php echo $value['member_id']?>" type="button" class="btn btn-info" onclick="get_data('<?php echo $value['member_id']; ?>')">เลือก</button>
            </td>
        </tr>
<?php } ?>