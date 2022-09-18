<?php foreach($data as $key => $value){ ?>
	<tr>
		<td><?php echo $value['description']; ?></td>
		<td style="text-align:right"><?php echo number_format(@$value['return_interest_amount'],2); ?></td>
	</tr>
	<input type="hidden" name="return_id[]" value="<?php echo $value['run_id']; ?>">
<?php } ?>