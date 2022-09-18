<?php
    $i = 1;
    foreach($data as $key => $value){
?>
    <tr>
			<td><?php echo $i++; ?></td>
			<td><?php echo @$value['contract_number']; ?></td>
			<td><?php echo @$value['member_id']; ?></td>
			<td><?php echo @$value['prename_short'].@$value['firstname_th']." ".@$value['lastname_th']; ?></td>
			<td><?php echo number_format(@$value['total_amount_approve'],2); ?></td>
			<td><?php echo number_format(@$value['total_amount_balance'],2); ?></td>
			<td>
				<a href="<?php echo base_url(PROJECTPATH.'/loan_atm/show_loan_atm_detail/'.$value['loan_atm_id']); ?>">ดูรายละเอียด</a>
			</td>
    </tr>
<?php
    }
?>