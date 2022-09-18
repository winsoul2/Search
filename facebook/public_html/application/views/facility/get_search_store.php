<?php
    $i = 1;
    foreach($data as $key => $value){
?>
    <tr>
		<td><input type="checkbox" id="store_id[<?php echo @$value['store_id'];?>]" name="store_id[<?php echo @$value['store_id'];?>]" value="<?php echo @$value['store_id'];?>"></td>
		<td><?php echo $i++; ?></td>
		<td><?php echo @$value['store_code']; ?></td>
		<td><?php echo @$value['store_name']; ?></td>
		<td class="text-right"><?php echo number_format(@$value['store_price'],2); ?></td>
		<td class="text-right"><?php echo number_format(@$value['depreciation_price'],2); ?></td>
		<th></th>
		<td><?php echo @$value['department_name']; ?></td>
		<td><?php echo @$value['facility_status_name']; ?></td>
		<td>
			<a href="<?php echo base_url(PROJECTPATH.'/facility/add?s_id='.@$value['store_id']);?>">แก้ไข</a> 
			|
			<span class="text-del del"  onclick="del_coop_data('<?php echo @$value['store_id'] ?>')">ลบ</span>
		</td>
    </tr>
<?php
    }
?>