<?php
    $i = 1;
    foreach($data as $key => $value){
?>
    <tr>
        <td><?php echo $i++; ?></td>	
		<?php if($form_target == 'index'){ ?>	
		<td align="center"><?php echo $value['mem_apply_id']; ?></td>
		<?php } ?>
        <td align="center"><?php echo $value['member_id']; ?></td>
        <td><?php echo $value['firstname_th']." ".$value['lastname_th']; ?></td>
        <td align="center"><?php echo $this->center_function->mydate2date($value['apply_date']); ?></td>	     
		<?php if($form_target == 'index'){ ?>
		<td align="center"><?php echo @$member_status[$value['member_status']]; ?></td>	
		<td>
				<a href="<?php echo base_url(PROJECTPATH.'/manage_member_share/add/'.$value['id']);?>">แก้ไข</a> 
				<!--a data-toggle="modal" data-target="#Del" data-id="<?php echo $value['mem_apply_id']  ?>" class="text-del">ลบ</a-->
		</td>
		<td align="center" width="5%">
			<?php if(in_array($value['member_status'],array('3','4'))){ ?>
			<input type="checkbox" class="check_member" name="del_member[]" value="<?php echo $value['id']; ?>">
			<?php } ?>
		</td>		
		<?php }else if($form_target == 'add'){ ?>
		<td>
			<a href="<?php echo base_url(PROJECTPATH.'/manage_member_share/add/'.$value['id']);?>">
				<button style="padding: 2px 12px;" type="button" class="btn btn-info">เลือก</button>
            </a>
        </td>
		<?php } ?>
    </tr>
<?php
    }
?>