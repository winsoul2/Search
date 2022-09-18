<div class="layout-content">
    <div class="layout-content-body">
<style>
label {
    padding-top: 6px;
    text-align: right;
}
.text-center{
	text-align:center;
}

input[type=checkbox], input[type=radio] {
    margin: 11px 0 0;
}
</style> 
<?php
	$transfer_status = array(''=>'ยังไม่ได้โอนเงิน','0'=>'โอนเงินแล้ว');
	//$transfer_status = array('0'=>'โอนเงินแล้ว','1'=>'รออนุมัติยกเลิก','อนุมัติยกเลิกรายการ');
?>


		<div class="row">
			<div class="form-group">
				<div class="col-sm-6">
					<h1 class="title_top">คืนเงินบัญชีหลักประกัน</h1>
					<?php $this->load->view('breadcrumb'); ?>
				</div>
				<div class="col-sm-6">
					<br>
				</div>
			</div>
		</div>
		
		<div class="row gutter-xs">
			<div class="col-xs-12 col-md-12">
				<div class="panel panel-body" style="padding-top:0px !important;">
					<h3></h3>
					<div class="row">
						<div class="col-md-offset-4 col-md-4">
							<label for="">ประเภทเงินกู้</label>
							<select name="loan_type" id="loan_type" class="form-control">
								<!-- <option value="">เลือก</option> -->
								<?php
									foreach ($loan_type as $key => $value) {
										echo "<option value='".$value['id']."'>".$value['loan_type']."</option>";
									}
								?>
							</select>
						</div>
					</div>
					<br>

					 <?php
					 	$count = 0;
					 	foreach ($data as $data) {
							?>
								<form action="<?=base_url('finance/finance_return_closing_account_save');?>" method="POST" id="form_<?=$loan_type[$count]['id']?>">
									<table class="table table-bordered table-striped table-center table_build" id="table_<?=$loan_type[$count++]['id']?>" style="<?=($count>1 ? 'display: none;' : '')?>">
										<thead> 
											<tr class="bg-primary">
												<th>ลำดับ</th>
												<th>รหัสสมาชิก</th>
												<th>ชื่อสมาชิก</th>
												<th>ประเภทเงินกู้</th>
												<th>เลขที่สัญญา</th>
												<th>หนี้คงเหลือ</th>
												<th>เลขที่บัญชี</th>
												<th>ยอดเงิน</th>
												<th>ดอกเบี้ย</th>
												<th><a href="#" class="select_all" id="select_all_id_<?=$loan_type[($count-1)]['id']?>" data-value="false" style="color: white;"><u>เลือกทั้งหมด</u></a></th> 
											</tr> 
										</thead>
										<tbody id="table_first">
												<?php 
												if(!empty($data)){
												$c = 1;
												foreach($data as $key => $row ){ ?>							
													<tr> 
														<td><?php echo $c++; ?></td>
														<td><?php echo @$row['member_id']; ?></td> 
														<td class="text-left"><?php echo $row['fullname']; ?></td> 
														<td class="text-center"><?php echo @$row['loan_name']; ?></td> 
														<td class="text-center"><?php echo @$row['contract_number']; ?></td> 
														<td class="text-right"><?php echo number_format(@$row['loan_amount_balance'],2); ?></td> 
														<td class="text-center"><?php echo $this->center_function->convert_account_id($row['account_id']); ?></td> 
														<td class="text-right"><?php echo number_format(@$row['balance'],2); ?></td> 
														<th class="text-right"><?php echo number_format($row['interest'],2); ?></th>
														<td>
															<input type="checkbox" class="account_<?=$loan_type[($count-1)]['id']?>" name="account_id[<?=$row['member_id']?>]" value="<?php echo $row['account_id']; ?>">
														</td>
													</tr>
												<?php } 
												}else{?>
													<tr> 
														<td colspan="10">ไม่พบข้อมูล</td>
													</tr>
												<?php } ?>
											</tbody> 
										</table>

										
									</form>
	
							<?php
						}
					 ?>

						<div class="row">
							<div class="col-md-offset-4 col-md-4 text-center">
								<button type="button" class="btn btn-primary" id="save">บันทึก</button>
							</div>
						</div>

				  </div>
			</div>
		</div>
		
		

	</div>
</div>

<script>
	$("#save").click(function() {

		var loan_type = $('select[name=loan_type]').val();
		console.log("select "+loan_type);
		$("#form_"+loan_type).submit();

	});
	
	$(".select_all").click(function() {
		var loan_type = $('select[name=loan_type]').val();
		console.log("select all : "+$(this).data("value"));
		var select = !$(this).data("value");
		var id = $(this).attr('id');
		console.log("set id : "+id);
		$('.account_'+loan_type).prop('checked', select);
		$("#"+id).data('value', select); //setter

	});

	$('#loan_type').on('change', function() {
		$(".table_build").hide();
		var table_id = this.value;
		$("#table_"+table_id).toggle(500, function () {
				// $("#table_"+table_id).show();
		});
	});
</script>