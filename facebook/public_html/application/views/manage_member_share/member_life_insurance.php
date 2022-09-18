<div class="" style="padding-top:0;">
	<h3 >ประวัติประกันชีวิต</h3>

	<div class="g24-col-sm-24 m-t-1 hidden_table" id="">
		<div class="bs-example" data-example-id="striped-table">
			<table class="table table-bordered table-striped table-center">
			<thead>								
				<tr class="bg-primary">
					<th>ประจำปี</th>
					<th>วันที่ซื้อ</th>
					<th>เลขที่สัญญา</th>
					<th>ทุนประกัน</th>
					<th>เบี้ยประกัน</th>
					<th>ประเภทการซื้อ</th>
				</tr>
			</thead>

			<tbody id="table_first">
			<?php
			if(!empty($rs_life_insurance)){
				foreach($rs_life_insurance as $key => $row_life_insurance){
			?>
					<tr>
						<td><?php echo @$row_life_insurance['insurance_year'];?></td>
						<td><?php echo @$this->center_function->ConvertToThaiDate(@$row_life_insurance['insurance_date'],1,0); ?></td>
						<td><?php echo @$row_life_insurance['contract_number'];?></td>						
						<td class="text-right"><?php echo number_format(@$row_life_insurance['insurance_amount'],2); ?></td>
						<td class="text-right"><?php echo number_format(@$row_life_insurance['insurance_premium'],2); ?></td>
						<td><?php echo @$row_life_insurance['insurance_type_name'];?></td>
					</tr>
			<?php 
				}
			}else{
			?>	
				<tr><td colspan="6">ไม่พบข้อมูล</td></tr>
			<?php											
			} 
			?>
			</tbody>
		</table>
		</div>
	</div>

</div>
