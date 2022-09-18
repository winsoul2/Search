<?php
    $i = 1;
    foreach($data as $key => $value){
?>
    <tr>
        <td><?php echo $i++; ?></td>
        <td><?php echo $value['facility_main_name']; ?></td>
        <td style="width:70px;">
			<a style="cursor:pointer;" onclick="choose_facility('<?php echo @$value['facility_main_code']; ?>','<?php echo @$value['facility_main_name']; ?>','<?php echo @$value['unit_type_id']; ?>','<?php echo @$value['unit_type_name']; ?>','<?php echo @$value['facility_main_price']; ?>');">
				<button style="padding: 2px 12px;" type="button" class="btn btn-info">เลือก</button>
            </a>
        </td>
    </tr>
<?php
    }
?>