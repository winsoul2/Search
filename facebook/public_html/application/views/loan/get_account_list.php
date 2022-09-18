<select class="form-control all_input" id="account_id" name="account_id" onchange="">
	<option value="">เลือกบัญชี</option>
	<?php
		foreach($rs_account as $key => $row_account){			
	?>
		<option <?php echo @$account_id==$row_account['account_id']?'selected':''; ?> value="<?php echo $row_account['account_id'];?>" account_name="<?php echo $row_account['account_name']; ?>"><?php echo $row_account['account_id']." : ".$row_account['account_name'];?></option>
	<?php
		} 
	?>
</select>