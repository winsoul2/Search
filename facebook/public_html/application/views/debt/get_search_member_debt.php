<?php
    $i = 1;
    foreach($data as $key => $value){
?>
    <tr>
        <td><?php echo $i++; ?></td>
        <td><?php echo $value['member_id']; ?></td>
        <td><?php echo $value['firstname_th']." ".$value['lastname_th']; ?></td>
        <td><?php echo $this->center_function->mydate2date($value['apply_date']); ?></td>
        <td>
			<a href="<?php echo base_url(PROJECTPATH.'/debt/debt_pay?id='.@$value['member_id']);?>">
				<button style="padding: 2px 12px;" type="button" class="btn btn-info">เลือก</button>
            </a>
        </td>
    </tr>
<?php
    }
?>