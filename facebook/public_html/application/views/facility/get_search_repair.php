<?php
    $i = 1;
    foreach($data as $key => $value){
?>
	<tr>
		<td><?php echo $i++; ?></td>
		<td><?php echo $this->center_function->ConvertToThaiDate(@$value['repair_date'],true,false);?></td>
		<td><?php echo @$value['repair_code']; ?></td>
		<td><?php echo @$value['store_code']; ?></td>
		<td><?php echo @$value['store_name']; ?></td>
		<td><?php echo @$value['company']; ?></td>
		<td>
			<div id="repair_status_wrap_<?php echo $value['repair_id']; ?>">
				<?php if($value['repair_status'] == 0) { ?>ส่งซ่อม<?php } ?>
				<?php if($value['repair_status'] == 1) { ?>ซ่อมไม่ได้<?php } ?>
				<?php if($value['repair_status'] == 2) { ?>ได้รับคืนแล้ว<?php } ?>
			</div>
		</td>
		<td>
			<a href="#" class="btn_return btn btn-primary btn-xs" data-id="<?php echo @$value['repair_id']; ?>">ได้รับคืนแล้ว</a>
		</td>
	</tr>
<?php
    }
?>