<table class="table table-bordered table-striped table-center">
	<thead> 
		<tr class="bg-primary">
			<th>เลขที่สัญญา</th>
			<th>รหัสสมาชิก</th>
			<th>ชื่อ -  สกุล</th>
			<th>ยอดเงินกู้</th>
			<th>ภาระค้ำประกันคงเหลือ</th>
		</tr>
	</thead>
	<tbody>
<?
	foreach(@$rs as $key => $row){ 
?>
	<tr>
		<td><?php echo @$row['contract_number']." (".@$row['loan_name'].")"; ?></td>
		<td><?php echo @$row['member_id']; ?></td>
		<td><?php echo @$row['prename_short'].@$row['firstname_th']." ".@$row['lastname_th']; ?></td>
		<td><?php echo number_format(@$row['loan_amount'],2); ?></td>
		<td><?php echo number_format(@$row['guarantee_person_amount_balance'],2); ?></td>
	</tr>
	<?php } ?>
	</tbody> 
</table> 

<?php exit; ?>
