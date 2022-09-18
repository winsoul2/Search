<div class="layout-content">
    <div class="layout-content-body">
	<style>
		input[type=file] {
			margin-left: -10px !important;
		}
		.loan_list { margin: 7px 0px 0px 0px; padding: 0; list-style: none; }
		.loan_list ul { margin: 0 0 0 20px; padding: 0; list-style: none; }
	</style>
	<?php
	$act = @$_GET['act'];
	$id = @$_GET['id'];
	?>

	<?php if (@$act != "add") { ?>
	<h1 style="margin-bottom: 0">ลำดับรายการหัก</h1>
	<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 padding-l-r-0">
	<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 padding-l-r-0">
	<?php $this->load->view('breadcrumb'); ?>
	</div>
	<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 padding-l-r-0">
	<?php if (@$act == "order") {?>
		<a class="link-line-none" href="?">
		<button class="btn btn-primary btn-lg bt-add" type="button">
		<span class="icon icon-reply"></span>
		ย้อนกลับ
		</button>
		</a>
	<?php }else{ ?>			
		<a class="link-line-none" href="?act=add">
		<button class="btn btn-primary btn-lg bt-add" type="button">
		<span class="icon icon-plus-circle"></span>
		เพิ่มรายการหัก
		</button>
		</a>
	<?php } ?>
	</div>
	</div>
	<?php } ?>

	<?php if (@$act == "") { ?>
		<div class="row gutter-xs">
			<div class="col-xs-12 col-md-12">
			  <div class="panel panel-body">	                
				<div class="bs-example" data-example-id="striped-table">
					<div id="tb_wrap">
						<table class="table table-bordered table-striped table-center">
							 <thead> 
								  <tr class="bg-primary">
									<th width="100">ลำดับรายการหัก</th>
									<th>รายการ</th>
									<th>รูปแบบการหัก</th>
									<th width="250"></th> 
								  </tr> 
							 </thead>

							<tbody>
						   <?php  
							if(!empty($rs)){
								foreach(@$rs as $key => $row){ ?>
									<tr> 
									<td >	
										<ul style="padding-left: 0px;">
											<li style="display: inline-block;">											
												<?php if(@$i != 1 ){ ?>
												<div>
													<a href="<?php echo base_url(PROJECTPATH.'/setting_basic_data/coop_order_seq?do=up&id='.@$row["deduct_id"]);?>">
														<img src="data:image/gif;base64,R0lGODlhFQAEAIAAACMtMP///yH5BAEAAAEALAAAAAAVAAQAAAINjI8Bya2wnINUMopZAQA7" style="width: 30px;">
													</a>
												</div>
											<?php }  ?>
											<?php if(@$i != @$num_rows ){ ?>
												<div style="margin-top: -12px;margin-left: 2px;">
													<a href="<?php echo base_url(PROJECTPATH.'/setting_basic_data/coop_order_seq?do=down&id='.@$row["deduct_id"]);?>">
														<img src="data:image/gif;base64,R0lGODlhFQAEAIAAACMtMP///yH5BAEAAAEALAAAAAAVAAQAAAINjB+gC+jP2ptn0WskLQA7" style="width: 30px;">
													</a>
												</div>
											<?php } ?>	
											</li>	
											<li style="display: inline-block;">
												<span><?php echo @$i++; ?></span>
											</li>
										</ul>	
									</th>
									<td style="text-align: left;"><?php echo @$row['deduct_detail']; ?></td> 
									<td style="text-align: left;">
									<?php if(!empty($row['deduct_format'])){?>
										<i class="fa <?php echo (@$row['deduct_format']=='1')?'fa-circle':'fa-circle-o';?>"></i> หักดอกเบี้ย
										&nbsp;&nbsp;&nbsp;&nbsp;
										<i class="fa <?php echo (@$row['deduct_format']=='2')?'fa-circle':'fa-circle-o';?>"></i> หักเงินต้น
									<?php } ?>	
									</td> 
									<td>
										<?php if(@$row['deduct_type'] == '1'){?>
										<a href="?act=order&id=<?php echo @$row["deduct_id"] ?>">เรียงลำดับรายละเอียด</a> | 
										<?php } ?>
										<a href="?act=add&id=<?php echo @$row["deduct_id"] ?>">แก้ไข</a> | 
										<span class="text-del del"  onclick="del_coop_basic_data('<?php echo @$row['deduct_id'] ?>')">ลบ</span>
									</td> 
									</tr>
							<?php 
									}
								} 
							?>

							</tbody> 
						</table> 
					</div>
				</div>
	       </div>
           <?php echo @$paging; ?>
		</div>
	</div>
	<?php }else if (@$act == "order") { ?>
		<div class="row gutter-xs">
			<div class="col-xs-12 col-md-12">
			  <div class="panel panel-body">	                
				<div class="bs-example" data-example-id="striped-table">
					<div id="tb_wrap">
						<table class="table table-bordered table-striped table-center">
							 <thead> 
								  <tr class="bg-primary">
									<th width="100">ลำดับรายการหัก</th>
									<th>ประเภทเงินกู้</th>
								  </tr> 
							 </thead>

							<tbody>
						   <?php  
							$j = 1;	
							if(!empty($rs_detail)){
								foreach(@$rs_detail as $key_detail => $row_detail){ ?>
									<tr> 
									<td >	
										<ul style="padding-left: 0px;">
											<li style="display: inline-block;">											
												<?php if(@$j != 1 ){ ?>
												<div>
													<a href="<?php echo base_url(PROJECTPATH.'/setting_basic_data/coop_order_detail_seq?do=up&id='.@$row_detail["deduct_id"].'&id_detail='.@$row_detail["deduct_detail_id"].'&act='.@$_GET['act']);?>">
														<img src="data:image/gif;base64,R0lGODlhFQAEAIAAACMtMP///yH5BAEAAAEALAAAAAAVAAQAAAINjI8Bya2wnINUMopZAQA7" style="width: 30px;">
													</a>
												</div>
											<?php }  ?>
											<?php if(@$j != @$num_rows_detail ){ ?>
												<div style="margin-top: -12px;margin-left: 2px;">
													<a href="<?php echo base_url(PROJECTPATH.'/setting_basic_data/coop_order_detail_seq?do=down&id='.@$row_detail["deduct_id"].'&id_detail='.@$row_detail["deduct_detail_id"].'&act='.@$_GET['act']);?>">
														<img src="data:image/gif;base64,R0lGODlhFQAEAIAAACMtMP///yH5BAEAAAEALAAAAAAVAAQAAAINjB+gC+jP2ptn0WskLQA7" style="width: 30px;">
													</a>
												</div>
											<?php } ?>	
											</li>	
											<li style="display: inline-block;">
												<span><?php echo @$j++; ?></span>
											</li>
										</ul>	
									</th>
									<td style="text-align: left;"><?php echo @$row_detail['loan_name']; ?></td> 
									</tr>
							<?php 
									}
								} 
							?>

							</tbody> 
						</table> 
					</div>
				</div>
	       </div>
		</div>
	</div>
	<?php }else{ ?>

			<div class="col-md-6 col-md-offset-3">

				<h1 class="text-center m-t-1 m-b-2"><?php echo  (!empty($id)) ? "แก้ไขรายการหัก" : "เพิ่มรายการหัก" ; ?></h1>

				<form id='form_save' data-toggle="validator" novalidate="novalidate" action="<?php echo base_url(PROJECTPATH.'/setting_basic_data/coop_deduct_save'); ?>" method="post"  enctype="multipart/form-data">	
					<?php if (!empty($id)) { ?>
					<input name="type_add"  type="hidden" value="edit" required>
					<input id="id" name="id"  type="hidden" value="<?php echo $id; ?>" required>
					<?php }else{ ?>
					<input name="type_add"  type="hidden" value="add" required>
					<?php } ?>	
					
	
					<div class="row">
						<label class="col-sm-3 control-label" for="form-control-2">รายการ</label>
						<div class="col-sm-9">
							<div class="form-group">
							  <input id="deduct_detail" name="deduct_detail" class="form-control m-b-1" type="text" value="<?php echo @$row['deduct_detail'] ?>" required title="กรุณากรอก รายการ">
							</div>
						</div>
					</div>
					<div class="row">
						<label class="col-sm-3 control-label" for="form-control-2">รายการชำระเงิน</label>
						<div class="col-sm-9">
							<div class="form-group">
								<select id="account_list_id" name="account_list_id" class="form-control" required title="กรุณาเลือกรายการชำระเงิน">
									<option value="">เลือกรายการ</option>
									<?php foreach($account_list as $key => $value){ ?>
										<option value="<?php echo $value['account_id']; ?>" <?php echo @$row['account_list_id']==$value['account_id']?'selected':''; ?>><?php echo $value['account_list']; ?></option>
									<?php } ?>
								</select>
							</div>
						</div>
					</div>
					<div class="row m-t-1">
						<label class="col-sm-3 control-label" for="form-control-2">ประเภทการหัก</label>
						<div class="col-sm-9 m-t-1">
							<input type="radio" id="deduct_type1" name="deduct_type" value="1" onchange="change_deduct_type()" <?php echo (@$row['deduct_type'] == '1')?'checked':'' ?>> เงินกู้
							<br>
							<input type="radio" id="deduct_type2" name="deduct_type" value="2" onchange="change_deduct_type()" <?php echo (@$row['deduct_type'] == '2')?'checked':'' ?>> เงินฝาก
						</div>
					</div>
					<div class="row m-t-1 show_format" style="display:none;">
						<label class="col-sm-3 control-label" for="form-control-2">รูปแบบการหัก</label>
						<div class="col-sm-9 m-t-1">
							<input type="radio" id="deduct_format1" name="deduct_format" value="1" <?php echo (@$row['deduct_format'] == '1' || empty($row))?'checked':'' ?>> หักดอกเบี้ย
							<br>
							<input type="radio" id="deduct_format2" name="deduct_format" value="2" <?php echo (@$row['deduct_format'] == '2')?'checked':'' ?>> หักเงินต้น
							<!--หักทุกสัญญา (หักดอกเบี้ยก่อน) , หักทีละสัญญา (หักทั้งหมดทีละสัญญา) -->
						</div>
					</div>
					<div class="row m-t-1 show_format" style="display:none;">
						<label class="col-sm-3 control-label" for="form-control-2">ประเภทการกู้เงิน</label>						
						<div class="col-sm-9">
							<ul class="loan_list">
								<?php	
									function get_loan_list($rs_loan_name,$row,$deduct_detail) {
										$html = "";							
										foreach($rs_loan_name as $value) {
											$ckeck_box = '';
											if(@in_array(@$value["id"],@$deduct_detail)){
												$ckeck_box = ' checked';
											}else{
												$ckeck_box = '';
											}
											
											$html .= '<li>
															<label class="custom-control custom-control-primary custom-checkbox">
																<input type="checkbox" id="loan_type_'.@$value["id"].'" name="loan_type['.@$value["id"].']" value="1" class="custom-control-input loan_item " '.$ckeck_box.' type_loan_id="'.@$value["id"].'">
																<span class="custom-control-indicator"></span>
																<span class="custom-control-label">'.@$value["name"].'</span>
															</label>';
											//$html .= '<input type="checkbox" id="loan_type['.@$value["id"].']" name="loan_type['.@$value["id"].']" value="1" class="custom-control-input loan_item" '.$ckeck_box.'>';		
											if(!empty($value["submenus"])) {
												$html .= '<ul>';
												$html .= get_loan_list(@$value["submenus"],@$row,@$deduct_detail);
												$html .= '</ul>';
											}
											$html .= '</li>';
										}
										
										return $html;
									}
									
									echo get_loan_list(@$rs_loan_name,@$row,@$deduct_detail);									
									?>
							</ul>
						</div>
					</div>
					<div class="row m-t-1 show_deposit_type" style="display:none;">
						<label class="col-sm-3 control-label" for="form-control-2">ประเภทบัญชี</label>
						<div class="col-sm-9">
							<select class="form-control m-b-1" id="deposit_type_id"  name="deposit_type_id" >
								<option value="">เลือกประเภทบัญชี</option>
								<?php foreach($type_id as $key => $value){ ?>
									<option value="<?php echo $value['type_id']; ?>" <?php echo $value['type_id']==@$row['deposit_type_id']?'selected':''; ?>><?php echo $value['type_name']; ?></option>
								<?php } ?>
							</select>
						</div>
					</div>
					<div class="row m-t-1 show_deposit_amount" style="display:none;">
						<label class="col-sm-3 control-label" for="deposit_amount">จำนวนเงิน</label>
						<div class="col-sm-9">
							<div class="form-group">
							  <input id="deposit_amount" name="deposit_amount" class="form-control m-b-1" type="number" value="<?php echo @$row['deposit_amount'] ?>">
							</div>
						</div>
					</div>
					<div class="row">&nbsp;</div>
					<div class="form-group text-center">
						<button type="button"  onclick="check_form()" class="btn btn-primary min-width-100">ตกลง</button>
						<a href="?"><button class="btn btn-danger min-width-100" type="button">ยกเลิก</button></a>
					</div>
			  </form>
			</div>

<?php } ?>


	</div>
</div>
<script>	
	var base_url = $('#base_url').attr('class');
	$( document ).ready(function() {
		change_deduct_type();
	});
	
	function check_form(){
		 $('#form_save').submit();	 
	}	
	
	function del_coop_basic_data(id){	
		swal({
			title: "ท่านต้องการลบข้อมูลใช่หรือไม่",
			text: "",
			type: "warning",
			showCancelButton: true,
			confirmButtonColor: '#DD6B55',
			confirmButtonText: 'ลบ',
			cancelButtonText: "ยกเลิก",
			closeOnConfirm: false,
			closeOnCancel: true
		},
		function(isConfirm) {
			if (isConfirm) {			
				$.ajax({
					url: base_url+'/setting_basic_data/del_coop_basic_data',
					method: 'POST',
					data: {
						'table': 'coop_deduct',
						'id': id,
						'field': 'deduct_id',
						'table_sub':'coop_deduct_detail'
					},
					success: function(msg){
					  // console.log(msg); return false;
						if(msg == 1){
						  document.location.href = base_url+'setting_basic_data/coop_deduct';
						}else{

						}
					}
				});
			} else {
				
			}
		});
		
	}
	
	function change_deduct_type(){
		if($('#deduct_type1').is(':checked')){
			$('.show_deposit_type').hide();
			$('.show_deposit_amount').hide();
			$('.show_format').show();			
		}else if($('#deduct_type2').is(':checked')){
			$('.show_deposit_type').show();
			$('.show_deposit_amount').show();
			$('.show_format').hide();
		}
	}
	
	$(".loan_item").click(function() {
		
		$(this).parents("li").parents("li").children("label").children(".loan_item").prop("checked", true);
		$(this).parent("label").parent("li").children("ul").find(".loan_item").prop("checked", $(this).prop("checked"));
		
		var loan_type_id = $(this).attr('type_loan_id');

		check_loan_type(loan_type_id);
		//custom-checkbox-seq
	});
	
	function check_loan_type(loan_type_id){
		var deduct_format = $('input[name=deduct_format]:checked').val();
		var deduct_id = '<?php echo @$_GET['id'];?>';
	
		if($('#loan_type_'+loan_type_id).is(":checked")){
			$.ajax({
				url: base_url+'/setting_basic_data/check_loan_type',
				method: 'POST',
				data: {
					'loan_type_id': loan_type_id,
					'deduct_format': deduct_format,
					'deduct_id': deduct_id
				},
				success: function(msg){
					if(msg == 1){
					  //alert('OK');
					}else{
						//alert('NO');
						$('#loan_type_'+loan_type_id).removeAttr('checked');
						swal('ท่านไม่สามารถเลือกประเภทการกู้เงินนี้ได้เนื่องจาก', 'ประเภทการกู้เงินนี้มีการตั้งค่าแล้ว' , 'warning');
					}
				}
			});
			
		}else{
			
		}			
		
	}	
</script>	
    