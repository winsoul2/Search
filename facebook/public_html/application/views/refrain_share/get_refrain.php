<?php
$i = 0;
foreach($data as $key => $row){ 
$i++;
$mmyy = (@$row['month_refrain'] != '')?@$month_arr[@$row['month_refrain']]."/".@$row['year_refrain']:'';
?>

<tr> 
	<td><?php echo @$i; ?></td>
	<td><?php echo @$this->center_function->ConvertToThaiDate(@$row['createdatetime']); ?></td>
	<td><?php echo @$type_refrain_list[@$row['type_refrain']]; ?></td> 
	<td><?php echo @$mmyy; ?></td> 
	<td><?php echo number_format(@$row['total_amount'],2); ?></td>
	<td><?php echo @$row['user_name']; ?></td> 
	<td style="padding:0px;vertical-align:middle;">
	  <span class="text-del del"  onclick="del_coop_refrain_share('<?php echo @$row['refrain_id'] ?>')">ลบ</span>
	</td> 
</tr>
<?php } ?>