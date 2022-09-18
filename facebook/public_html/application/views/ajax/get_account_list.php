<select id="account_list" class="form-control all_input" onchange="change_account()">
<option value="">เลือกบัญชี</option>
<?php
	foreach($rs as $key => $row){ ?>
		<option value="<?php echo $row['account_id'];?>" <?php echo $row['account_id']==$_POST['account_id']?'selected':''; ?> account_name="<?php echo $row['account_name']; ?>"><?php echo $row['account_id']." : ".$row['account_name'];?></option>
	<?php } ?>
</select>
<?php exit; ?>