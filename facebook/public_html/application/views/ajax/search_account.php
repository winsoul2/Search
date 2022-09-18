<table class="table table-bordered table-striped table-center">	
	<thead>
		 <tr class='bg-primary'> 
		   <th>ลำดับ</th>
		   <th>ประเภทบัญชี</th>
		   <th>เลขบัญชี</th>
		   <th>ชื่อบัญชี</th>
		   <th>รหัสสมาชิก</th>
		   <th>ชื่อ - นามสกุล</th>
		   <th>วันที่เปิดบัญชี</th>
		   <th>จัดการ</th>
		 </tr>
	</thead>
	<tbody>
		<?php 
		$i = 1;
		if(!empty($rs)){
			foreach(@$rs as $key => $row){ 
		?>
			<tr>
				<td><?php echo $i++; ?></td>
				<td><?php echo $row['type_code']; ?></td>
				<td>
				<a href="<?php echo base_url(PROJECTPATH.'/save_money/account_detail?account_id='.$row['account_id']); ?>">
					<?php echo $this->center_function->format_account_number($row['account_id']); ?>
				</a>
				</td>
				<td style="text-align:left"><?php echo $row['account_name']; ?></td>
				<td><?php echo $row['mem_id']; ?></td>
				<td style="text-align:left"><?php echo $row['member_name']; ?></td>
				<td><?php echo $this->center_function->ConvertToThaiDate($row['created']); ?></td>
				<td>
					<?php if($row['account_status'] == '0'){ ?>
					<a onclick="add_account('<?php echo @$row["account_id"];?>','<?php echo $row['mem_id']; ?>')" style="cursor:pointer;"> แก้ไข </a> |
					<a class="text-del" onclick="close_account('<?php echo @$row["account_id"];?>', '<?php echo @$row['mem_id'];?>')">ปิดบัญชี</a>
				<?php 
					}else{
						$receipt_refund_id = @$arr_receipt_refund[$row["account_id"]];
						if(@$receipt_refund_id != ''){
				?>
						<a href="<?php echo base_url(PROJECTPATH.'/receipt/receipt_refund/'.$receipt_refund_id); ?>"  target="_blank" style="cursor:pointer;"> ใบเสร็จคืนเงิน </a>
				<?php
						}
					}
				?>
				</td>
			</tr>
		<?php 
			}
		}
		?>
	</tbody>
</table>
