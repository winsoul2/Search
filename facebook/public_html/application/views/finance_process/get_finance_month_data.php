<?php foreach($data as $key => $value){ ?>
		<tr class="prev_row" id="prev_row_<?php echo $value['run_id']; ?>">
			<td class='num_row' align='center'></td>
			<td align="center"><?php echo $value['member_id']?></td>
			<td><?php echo $value['prename_short'].$value['firstname_th']." ".$value['lastname_th']; ?></td>
			<td align="center"><?php echo $value['ref_data']?></td>
			<td align="right"><?php echo number_format($value['pay_amount'],2); ?></td>
			<td align="center">
				<?php if($value['run_status']=='0'){ ?>
					<a style='cursor:pointer;' class='icon icon-trash-o' titla='ลบ' onclick="del_data('<?php echo $value['run_id']; ?>')"></a>
				<?php } ?>
			</td>
		</tr>
<?php } ?>
					