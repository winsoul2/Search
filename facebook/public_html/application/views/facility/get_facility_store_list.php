<?php
    foreach($data as $key => $value){
?>
    <tr>
		<td><?php echo $value['store_code']; ?></td>
        <td><?php echo $value['store_name']; ?></td>
        <td style="width:70px;">
			<a style="cursor:pointer;" onclick="choose_facility_store('<?php echo @$value['store_code']; ?>');">
				<button style="padding: 2px 12px;" type="button" class="btn btn-info">เลือก</button>
            </a>
        </td>
    </tr>
<?php
    }
?>
