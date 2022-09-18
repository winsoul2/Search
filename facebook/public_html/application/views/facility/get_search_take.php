<?php
    $i = 1;
    foreach($data as $key => $value){
?>
	<tr>
		<td><?php echo @$value['receive_no']; ?></td>
		<td><div id="sign_date_wrap_<?php echo @$value['facility_take_id']; ?>"><?php echo $this->center_function->ConvertToThaiDate(@$value['sign_date'],true,false);?></div></td>
		<td><?php echo @$value['voucher_no']; ?></td>
		<td><div id="department_name_wrap_<?php echo @$value['facility_take_id']; ?>"><?php echo @$value['department_name']; ?></div></td>
		<td><div id="receive_name_wrap_<?php echo @$value['facility_take_id']; ?>"><?php echo @$value['receive_name']; ?></div></td>
		<td>
			<a href="#" class="btn_transfer" data-id="<?php echo @$value['facility_take_id']; ?>">โอนย้าย</a> |
			<a href="<?php echo '?act=add&id='.@$value['facility_take_id'];?>">ดูรายการ</a>
		</td>
	</tr>
<?php
    }
?>