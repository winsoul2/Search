<div style="width: 1100px;" class="page-break">
	<div class="panel panel-body" style="padding-top:10px !important;">
		<br>
		<h3>รายงานตรวจสอบการทำงาน ระบบ ATM</h3>
		<h3><?php echo 'วันที่ '.@$_GET['date_start'].' ถึง วันที่ '.@$_GET['date_end']; ?></h3>					
		<br>
		<div style="padding-top:10px;">
			<table style="width: 100%;" border="0" cellpadding="0" cellspacing="0" class="table table-view table-center">
				<thead>
					<tr>
						<th style='width: 15%;'>วันเวลา</th>
						<th style='width: 10%;'>รหัสสมาชิก</th>
						<th style='width: 10%;'>เลขที่บัญชี</th>
						<th style='width: 20%;'>รายการ</th>
						<th style='width: 10%;'>D1</th>
						<th style='width: 10%;'>Upbean</th>
						<th style='width: 15%;'>Status</th>
					</tr>
				</thead>
				<tbody>								
				<?php 
					//echo '<pre>'; print_r($row); echo '</pre>';
					foreach($row AS $key=>$value){
						//echo '<pre>'; print_r($value); echo '</pre>';
				?>
					<tr>
						<td><?php echo $this->center_function->ConvertToThaiDate(@$value['createdatetime'],1,1,1);?></td>
						<td><?php echo @$value['mem_id'];?></td>
						<td><?php echo @$this->center_function->convert_account_id(@$value['account_id']);?></td>
						<td class="text-left">
							<?php 
								if(@$value['messageType'] == '0410'){
									echo 'คืนเงิน';
								}else{
									echo $type_list[@$value['tranType']];
								}
							?>
						</td>
						<td></td>
						<td></td>
						<td><?php echo (@$value['responseCode']=='000')?'Complete':'Not Complete';?></td>
					</tr>
				<?php } ?>
				</tbody>
			</table>	
		</div>
	</div>
</div>


