<div class="bs-example" data-example-id="striped-table">
	<table class="table table-bordered table-striped">
		<thead class="bg-primary"> 
			<tr>
				<th style="width: 5%;">ลำดับ</th>
				<th style="width: 6.25%;">ชื่อนามสกุล</th>
				<th style="width: 6.25%;">ทะเบียนสมาชิก</th>
				<th style="width: 6.25%;">ประเภท</th>
				<th style="width: 6.25%;">จำนวนเงิน</th>
				<th style="width: 6.25%;">จำนวนจ่ายจริง</th>
			</tr> 
		</thead>
		<tbody id="table_Finance" class="bg-primary">	
			<?php 
			$total=0;
			foreach($_POST['save_edit'] as $key => $value){ 
				$total++;
				if ($value != ''){ ?>
			<tr>
				<th style="width: 5%;"><?=$total?></th>
				<th style="width: 6.25%;"><?=$value['full_name'] ?></th>
				<th style="width: 6.25%;"><?=$value['member_id'] ?></th>
				<th style="width: 6.25%;"><?=$value['deduct_detail'] ?></th>
				<th style="width: 6.25%;"><?=$value['pay_amount']?></th>
				<th style="width: 6.25%;"><?=$value['real_pay_amount']?></th>
			</tr> 
			<?php }}
			?>
		</tbody> 
	</table> 
	
</div>