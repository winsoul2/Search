<?php
    $i = 1;
    foreach($data as $key => $value){
?>
    <tr>
			<td><?php echo $i++; ?></td>
			<td><?php echo @$value['contract_number']; ?></td>
			<td><?php echo @$value['member_id']; ?></td>
			<td><?php echo @$value['prename_short'].@$value['firstname_th']." ".@$value['lastname_th']; ?></td>
			<td><?php echo number_format(@$value['loan_amount'],2); ?></td>
			<td><?php echo number_format(@$value['loan_amount_balance'],2); ?></td>
			<!--td>
				<a href="">ค้างชำระ</a>                                     
			</td-->
    </tr>
<?php
    }
?>