<?php foreach($data as $key => $value){ ?>
	<tr>
		<td align="center"><?php echo $value['receipt_id']; ?></td>
		<td align="center"><?php echo $value['member_id']; ?></td>
		<td align="center"><?php echo $value['prename_short'].$value['firstname_th']." ".$value['lastname_th']; ?></td>
		<td align="center"><?php echo number_format($value['total_amount'],2); ?></td>
		<td align="center"><?php echo $value['user_name']; ?></td>
		<td align="center"><?php echo $this->center_function->mydate2date($value['receipt_datetime']); ?></td>
		<td align="center"><?php echo $value['receipt_status'].(@$value['cancel_by']!="" ? "<br>โดย ".$value['cancel_by']."<br>".$this->center_function->mydate2date($value['cancel_date']) : ""); ?></td>
        <td align="center"><a class="fa fa-pencil-square-o" style="cursor:pointer;" title="แก้ไขใบเสร็จ" target="_blank" href="<?php echo base_url('tool/receipt_edit/'.$value['receipt_id'])?>"></a></td>
		<td align="center"><a class="icon icon-print" style="cursor:pointer" title="พิมพ์ใบเสร็จ" target="_blank" href="<?php echo PROJECTPATH."/admin/receipt_form_pdf/".$value['receipt_id']; ?>"></a></td>
	</tr>
<?php } ?>