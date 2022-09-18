<?php foreach($row_month_detail_data as $key => $value){ ?>
		<tr class="table_data" id="data_<?php echo $value['run_id']; ?>">
			<td align="left"><?php echo $value['show_text']; ?></td>
			<td align="right"><?php echo number_format($value['pay_amount'],2); ?></td>
			<input type="hidden" class="pay_amount" value="<?php echo $value['pay_amount']; ?>">
			<td align="center"><a style="cursor:pointer" onclick="delete_data('<?php echo $value['run_id']; ?>')">ลบ</a></td>
		</tr>
<?php } ?>
<tr id="value_null" style="display:none">
	<td colspan='3' align='center'> ยังไม่มีรายการใดๆ </td>
</tr>
					