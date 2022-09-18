
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
@keyframes spinner_x {
  to {transform: rotate(360deg);}
}
 
.spinner_x:before {
  content: '';
  box-sizing: border-box;
  position: absolute;
  /* top: 50%; */
  /* left: 50%; */
  width: 30px;
  height: 30px;
  /* margin-top: -15px; */
  margin-right: -15px !important;
  border-radius: 50%;
  border: 2px solid #ccc;
  border-top-color: #07d;
  animation: spinner_x .6s linear infinite;
}


</style>
<?php
	$transfer_status = array(''=>'ยังไม่ได้โอนเงิน','0'=>'โอนเงินแล้ว');
	//$transfer_status = array('0'=>'โอนเงินแล้ว','1'=>'รออนุมัติยกเลิก','อนุมัติยกเลิกรายการ');
?>


		<div class="row">
			<div class="form-group">
				<div class="col-sm-6">
					<h1 class="title_top">บัญชีหลักประกันรอปิด</h1>
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
												<th>เลขที่บัญชี</th>
												<th>ยอดเงิน</th>
												<th>หนี้คงเหลือ</th>
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
														<td class="text-center"><?php echo $this->center_function->convert_account_id($row['account_id']); ?></td> 
														<td class="text-right"><?php echo number_format(@$row['balance'],2); ?></td> 
														<td class="text-right"><?php echo @$row['loan_amount_balance']; ?></td> 
														<th class="text-right"><?php echo number_format( ($row['interest'] - $row['interest_return']) ,2); ?></th>
														<td id="result_<?=$loan_type[($count-1)]['id']?><?php echo $row['account_id']; ?>">
															<span id="spin_<?=$loan_type[($count-1)]['id']?><?php echo $row['account_id']; ?>"></span>
															<input type="checkbox" 
															id="checkbox_<?=$loan_type[($count-1)]['id']?><?php echo $row['account_id']; ?>"
															class="account_<?=$loan_type[($count-1)]['id']?>" 
															name="account_id[<?=$row['member_id']?>]" 
															value="<?php echo $row['account_id']; ?>">

															<input type="hidden" id="close_account_principal_<?=$loan_type[($count-1)]['id']?><?php echo $row['account_id']; ?>" value="<?=@$row['balance']?>">
															<input type="hidden" id="close_account_interest_<?=$loan_type[($count-1)]['id']?><?php echo $row['account_id']; ?>" value="<?=@$row['interest']?>">
															<input type="hidden" id="close_account_interest_return_<?=$loan_type[($count-1)]['id']?><?php echo $row['account_id']; ?>" value="<?=@$row['interest_return']?>">
														</td>
													</tr>
												<?php } 
												}else{?>
													<tr> 
														<td colspan="7">ไม่พบข้อมูล</td>
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
								<div id="foo"></div>
								<button type="button" class="btn btn-primary" id="save" onclick="save()">บันทึก</button>
							</div>
						</div>

				  </div>
			</div>
		</div>
		
		

	</div>
</div>



<script>
	async function save(){
		var loan_type = $('select[name=loan_type]').val();
		console.log("select "+loan_type);
		// $("#form_"+loan_type).submit();

		
		let list = [];
		$('.account_'+loan_type).each(function(index){
		//do stuff
			if ($(this).is(':checked')) {
				console.log(index, $(this).val());
				var account_id = $(this).val();
				var close_account_principal = $("#close_account_principal_"+loan_type+""+account_id).val();
				var close_account_interest = $("#close_account_interest_"+loan_type+""+account_id).val();
				var close_account_interest_return = $("#close_account_interest_return_"+loan_type+""+account_id).val();
				list.push({
					close_account_principal : close_account_principal,
					close_account_interest : close_account_interest,
					close_account_interest_return : close_account_interest_return,
					account_id : account_id
				});
				
				
			}
		});
		console.log("--- ",list);
		for (let index = 0; index < list.length; index++) {
			const element = list[index];
			console.log(element);
			
			// $("#checkbox_"+loan_type+""+element.account_id).hide();
			$("#spin_"+loan_type+""+element.account_id).addClass( "spinner_x" );
			await sleep(500);
			var result = await do_closing(element.account_id, element.close_account_principal, element.close_account_interest, element.close_account_interest_return);
			console.log("#spin_7"+element.account_id);
			$("#spin_"+loan_type+""+element.account_id).removeClass( "spinner_x" );
			$("#checkbox_"+loan_type+""+element.account_id).hide();
			$("#result_"+loan_type+""+element.account_id).html('<i class="fa fa-check" style="font-size:28px;color:green"></i>');
			
			await sleep(500);
			
		}

		
	}
	
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

	function sleep(ms) {
		return new Promise(resolve => setTimeout(resolve, ms));
	}

	function do_closing(account_id, close_account_principal, close_account_interest, close_account_interest_return){
		// var target = document.getElementById('spin_700126000266');
		// 			new Spinner(opts).spin(target);
		// 			test.spin(target);
		
		return new Promise(resolve => {
			$.ajax({
				method: 'POST',
				url: base_url+'save_money/close_account',
				data: {
					close_account_principal 		: close_account_principal,
					close_account_interest 			: close_account_interest,
					close_account_interest_return 	: close_account_interest_return,
					account_id						: account_id,
					pay_type						: "0"
				},
				success: function(msg){
					console.log("promise: ", msg);
					var obj = msg;

					resolve(obj);
				},
				error: function(xhr,status,error){
					console.log("err", xhr);
					// $('#in_process').modal('hide');
					// swal('ประมวลผลไม่สำเร็จ', 'โปรดลองใหม่อีกครั้ง');
					// $.unblockUI();
				}
			});
		});
	}



	


</script>
<link rel="stylesheet" href="<?=base_url('assets/vendor/spin/spin')?>.css">