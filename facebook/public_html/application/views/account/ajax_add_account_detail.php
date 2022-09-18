<tr id="list_<?php echo $input_number; ?>" class="add-tr">
	<td>
		<select class="form-control account_detail_sel js-data-example-ajax" id="sel_input_<?php echo $input_number; ?>" name="data[coop_account_detail][<?php echo $input_number; ?>][account_chart_id]">
			<option value="" >เลือกรหัสผังบัญชี</option>
			<?php 
				foreach($row_account_chart as $key => $row){
			?>
				<option value="<?php echo $row['account_chart_id']; ?>"><?php echo $row['account_chart_id']." : ".$row['account_chart'];; ?></option>
			<?php } ?>
		</select>
		<input type="hidden" name="data[coop_account_detail][<?php echo $input_number; ?>][account_type]" value="<?php echo $type; ?>">
	</td>
	<td>
		<input type="text" class="form-control" id="desc_input_<?php echo $input_number; ?>"  name="data[coop_account_detail][<?php echo $input_number; ?>][account_description]">			
	</td>
	<?php if($type=="debit"){ ?>
		<td><input type="text" class="form-control account_detail debit_input" id="debit_input<?php echo $input_number; ?>" name="data[coop_account_detail][<?php echo $input_number; ?>][account_amount]" onKeyUp="format_the_number_decimal(this)" onchange="call_sum_credit_debit(this.value,'credit');$('.countn').val(this.value);"  ></td>
        <input class="countn" type="hidden" id="countnum<?php echo $input_number; ?>" name="countnum" value="<?php echo $input_number; ?>" >
		<td></td>
	<?php }else{ ?>
		<td></td>
		<td><input type="text" class="form-control account_detail credit_input" id="credit_input<?php echo $input_number; ?>"  name="data[coop_account_detail][<?php echo $input_number; ?>][account_amount]" onKeyUp="format_the_number_decimal(this)" onchange="call_sum_credit_debit(this.value,'debit'); $('.countn').val(this.value);" ></td>
        <input class="countn" type="hidden" id="countnum<?php echo $input_number; ?>" name="countnum" value="<?php echo $input_number; ?>" >
	<?php } ?>
    <td onclick=" $('#list_<?php echo $input_number; ?>').remove();"><a href="#">ลบ</a></td>
</tr>