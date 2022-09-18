<?php
    $i = 1;
    foreach($data as $key => $value){
?>
    <tr>
        <td><?php echo $i++; ?></td>
        <td><?php echo $value['department_name']; ?></td>
        <td style="width:70px;">
			<a style="cursor:pointer;" onclick="choose_department('<?php echo @$value['department_id']; ?>','<?php echo @$value['department_name']; ?>');">
				<button style="padding: 2px 12px;" type="button" class="btn btn-info">เลือก</button>
            </a>
        </td>
    </tr>
<?php
    }
?>