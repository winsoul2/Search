<div class="" style="padding-top:0;">
	<h3>รายจ่าย</h3>

	<div class="g24-col-sm-24 m-t-1 hidden_table" id="">	
		<div class="form-group g24-col-sm-3"></div>
		<div class="form-group g24-col-sm-18" class="bs-example" data-example-id="striped-table">
			<table class="table table-bordered table-striped table-center">
			<thead>								
				<tr class="bg-primary">
					<th>รายจ่าย</th>
					<th style="width: 30%;">จำนวนเงิน</th>
				</tr>
			</thead>

			<tbody id="table_first">
			<?php
			if(!empty($rs_loan_cost_mod)){
				foreach($rs_loan_cost_mod as $key => $row_loan_cost_mod){
			?>
					<tr>
						<td class="text-left"><?php echo @$row_loan_cost_mod['outgoing_name'];?></td>
						<td class="text-right"><?php echo number_format(@$row_loan_cost_mod['loan_cost_amount'],2); ?></td>
					</tr>
			<?php 
				}
			}else{
			?>	
				<tr><td colspan="2">ไม่พบข้อมูล</td></tr>
			<?php											
			} 
			?>
			</tbody>
		</table>
		</div>
		<div class="form-group g24-col-sm-3"></div>
	</div>

</div>
