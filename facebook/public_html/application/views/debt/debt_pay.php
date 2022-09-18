<div class="layout-content">
    <div class="layout-content-body">
		<style>
			.bt-add{
				float:none;
			}						
			.input-with-icon .form-control{
				padding-left: 40px;
			}
			
			input[type=file]{    
				margin-left: -8px;
			}
			
			.input-with-icon {
				margin-bottom: 5px;
			}
			
			.input-with-icon .form-control{
				padding-left: 40px;
			}
			.modal_data_input{
				margin-left:-5px;
			}
			
			.scrollbar {
				/* height: 360px; */
			}
		</style>
		
		<h1 style="margin-bottom: 0">ชำระหนี้คงค้าง</h1>
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 padding-l-r-0">
		<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6 padding-l-r-0">
		<?php $this->load->view('breadcrumb'); ?>
		</div>
		<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6 " style="padding-right:0px;text-align:right;">			   
					   
		</div>
		</div>
		<div class="row gutter-xs">
			<div class="col-xs-12 col-md-12">
				<div class="panel panel-body">	
					<form data-toggle="validator" method="post" action="" class="g24 form form-horizontal" enctype="multipart/form-data" autocomplete="off" id="myForm">
						<div class="m-t-1">
							<div class="g24-col-sm-20">
								<div class="form-group">
									<label class="g24-col-sm-4 control-label">รหัสสมาชิก <span id="naja"></span> </label>
									<div class="g24-col-sm-6">
										<div class="form-group">
											<div class="input-group">
												<input id="member_id" name="member_id" class="form-control" style="text-align:left;" type="number" value="<?php echo empty($row_member) ? '': $row_member['member_id']; ?>" onkeypress="check_member_id();" required title="กรุณาป้อน รหัสสมาชิก" />
												<span class="input-group-btn">
													<a data-toggle="modal" data-target="#myModal" id="modal-search" class="fancybox_share fancybox.iframe" href="#">
														<button id="" type="button" class="btn btn-info btn-search"><span class="icon icon-search"></span>
														</button>
													</a>
												</span>	
											</div>											
										</div>
									</div>
									<label class="g24-col-sm-4 control-label" for="budget_year">ชื่อสกุล</label>
									<div class="g24-col-sm-8">
										<div class="form-group">
											<input type="text" class="form-control" name="member_name" id="member_name" value="<?php echo @$row_member['firstname_th'].' '.@$row_member['lastname_th'] ?>"  readonly="readonly">
										</div>
									</div>
								</div>
							</div>                 
						</div>
					</form>
					<div class="bs-example" data-example-id="striped-table">
						<div id="tb_wrap">
							<table class="table table-bordered table-striped table-center">
								<thead> 
									<tr class="bg-primary">
										<th>ลำดับ</th>
										<th>ปี/เดือน</th>
										<th>รหัสสมาชิก</th>
										<th>ชื่อสกุล</th>
										<th>ยอดเรียกเก็บ</th>
										<th>ค้างชำระ</th>
										<th>วันที่ทำรายการ</th>
										<th>ผู้ทำรายการ</th>
										<th style="width: 130px;">เลขที่ใบเสร็จ</th>
										<th style="width: 130px;"></th>										
									</tr>
								</thead>
								<tbody>
								<?php
									$i=1;
									if(!empty($row)){
										foreach(@$row as $key => $row_debt){
											$this->db->select(array('SUM(pay_amount) AS pay_amount'));
											$this->db->from('coop_finance_month_detail');
											$this->db->join("coop_finance_month_profile","coop_finance_month_detail.profile_id=coop_finance_month_profile.profile_id","left");
											$this->db->where("coop_finance_month_detail.member_id = '{$row_debt['member_id']}' AND coop_finance_month_profile.profile_month = '{$row_debt['non_pay_month']}' AND coop_finance_month_profile.profile_year = '{$row_debt['non_pay_year']}'");
											
											$rs_receipt = $this->db->get()->result_array();
											$receipt_amount = number_format(@$rs_receipt[0]['pay_amount']-@$row_debt['non_pay_amount_balance'],2); //ยอดที่เก็บได้
											$non_pay_amount_balance = number_format(@$row_debt['non_pay_amount_balance'],2);//ค้างชำระ
											$pay_amount = number_format(@$rs_receipt[0]['pay_amount'],2);//ยอดเรียกเก็บ
											if(@$_GET['dev']=='dev'){
												print_r($this->db->last_query());
												echo $pay_amount.'<hr>';
											}
											$non_pay_id = $row_debt['non_pay_id'];
											$non_pay_year_month = $row_debt['non_pay_year'].'/'.$month_arr[$row_debt['non_pay_month']];
											
											
									?>
										<tr> 
											<td><?php echo @$i;?></td>
											<td><?php echo $non_pay_year_month;?></td>											
											<td><?php echo $row_debt['member_id'];?></td>											
											<td class="text-left"><?php echo $row_debt['firstname_th'].'  '.$row_debt['lastname_th'];?></td>											
											<td class="text-right"><?php echo $pay_amount;?></td>
											<td class="text-right"><?php echo $non_pay_amount_balance;?></td>
											<td><?php echo $this->center_function->ConvertToThaiDate($row_debt['updatetimestamp'],1,0);?></td>
											<td><?php echo $row_debt['user_name'];?></td>											
											<td>
												<?php
												$is_resign = 0;
												if(!empty($row_debt['receipt_id'])){
													// var_dump($row_debt);
													foreach($row_debt['receipt_id'] AS $receipt){
														$is_void = ($receipt['receipt_status']==1) ? "style='color: red;'" : "";		
														if($receipt['receipt_status']==1){
															$title =  "ยกเลิกโดย ".$receipt['user_name']." วันที่ ".$this->center_function->ConvertToThaiDate($receipt['updatetime'],1,1);
														}
														if(!empty($receipt["req_resign_id"])) {
															$is_resign = 1;
														}
												?>
													<a href="<?php echo base_url(PROJECTPATH.'/admin/receipt_form_pdf/'.@$receipt['receipt_id']); ?>" target="_blank" <?=$is_void?> title="<?=$title ?>"><?php echo @$receipt['receipt_id'];?></a>
													<br>
												<?php 
													}
												}
												?>
											</td>
											<td>
												<?php 
												if(@$row_debt['non_pay_status'] == '2'){
													if(!empty($is_resign)) {
														echo 'ชำระแล้ว(ออกจากสมาชิก)';
													} else {
														echo 'ชำระแล้ว';
													}
												} elseif($row_debt['non_pay_status'] == '6') {
													echo 'ชำระแล้ว(ออกจากสมาชิก)';
												}else{	
												?>
												<button name="bt_add" id="bt_add" type="button" class="btn btn-primary" onclick="view_detail('<?php echo $row_debt['non_pay_id'];?>','<?php echo $non_pay_year_month;?>','<?php echo $receipt_amount;?>','<?php echo $non_pay_amount_balance;?>','<?php echo $pay_amount;?>')">
													<span>ชำระหนี้คงค้าง</span>
												</button>
												<?php }?>
											</td>
										</tr>
									<?php
											
											$i++; 
										}
									}else{ ?>
										<tr><td colspan="10">ไม่พบข้อมูล</td></tr>
									<?php } ?>
								</tbody> 
							</table>
						</div>
					</div>
					<?php echo @$paging ?>
				</div>
			</div>
		</div>
    </div>
</div>

<div class="modal fade" id="viewDetail"  tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-account" style="width:60%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"></button>
                <h2 class="modal-title">ชำระหนี้คงค้าง</h2>
            </div>
            <div class="modal-body" style="height: 600px;">
				<form data-toggle="validator" method="post" action="<?php echo base_url(PROJECTPATH.'/debt/save_debt_pay'); ?>" class="g24 form form-horizontal" enctype="multipart/form-data" autocomplete="off" id="from_view">
					<input type="hidden" class="form-control" name="non_pay_id" id="non_pay_id" value="">					
					<input type="hidden" name="bank_id" id="bank_id">
            		<input type="hidden" name="branch_code" id="branch_code">
					<div class="g24-col-sm-24 m-t-1">
						<div class="form-group">
							<label class="g24-col-sm-5 control-label">ปี/เดือน </label>
							<div class="g24-col-sm-5">
								<div class="form-group">
									<input type="text" class="form-control" name="non_pay_year_month" id="non_pay_year_month" value=""  readonly="readonly">
								</div>
							</div>
							<label class="g24-col-sm-5 control-label">ยอดเรียกเก็บ </label>
							<div class="g24-col-sm-5">
								<div class="form-group">
									<input type="text" class="form-control" name="pay_amount" id="pay_amount" value=""  readonly="readonly">
								</div>
							</div>
						</div>					
					</div>
					<div class="g24-col-sm-24 m-t-1">
						<div class="form-group">							
							<label class="g24-col-sm-5 control-label">ยอดที่เก็บได้ </label>
							<div class="g24-col-sm-5">
								<div class="form-group">
									<input type="text" class="form-control" name="receipt_amount" id="receipt_amount" value=""  readonly="readonly">
								</div>
							</div>
							<label class="g24-col-sm-5 control-label">ค้างชำระ </label>
							<div class="g24-col-sm-5">
								<div class="form-group">
									<input type="text" class="form-control" name="non_pay_amount_balance" id="non_pay_amount_balance" value=""  readonly="readonly">
								</div>
							</div>
						</div>					
					</div>
					<div class="g24-col-sm-24 m-t-1">&nbsp;</div>
					<div class="bs-example" data-example-id="striped-table">
						<div id="tb_wrap">
							<h3>รายการค้างชำระ</h3>
							<div class="col-sm-10 col-sm-offset-1 ">
								<table class="table table-bordered table-striped table-center">
									<thead> 
										<tr class="bg-primary">
											<th width="80">ลำดับ</th>
											<th>รายการหัก</th>
											<th width="150">ยอดเงิน</th>
											<th width="150">ยอดชำระ</th>
										</tr>
									</thead>
									<tbody id="table_data_debt">

									</tbody>
								</table>
							</div>
							<div class="row">
								<div class=" g24-col-sm-24">
									<label class="g24-col-sm-3 control-label font-normal" for="form-control-2">ช่องทางชำระเงิน</label>
									<div class="g24-col-sm-6" style="padding-top:7px;">
										<input type="radio" id="pay_type_cash" name="pay_type" checked value="cash" onclick="set_bank('');set_branch_code('');show('');"> เงินสด
										<input type="radio" id="pay_type_transfer" name="pay_type" value="transfer" onclick="set_bank('');set_branch_code('');show('xd_sec');"> เงินโอน
										<input type="radio" id="pay_type_transfer" name="pay_type" value="cheque" onclick="set_bank('');set_branch_code('');show('che_sec');"> เช็คเงินสด
										<input type="radio" id="pay_type_transfer" name="pay_type" value="" onclick="set_bank('');set_branch_code('');show('other_sec');"> อื่นๆ
									</div>
								</div>
							</div>
							<div class="row">
								<div class="g24-col-sm-24" id="xd_sec" style="display: none;">
									<div class="form-group g24-col-sm-16">
										<label class="g24-col-sm-5 control-label right"></label>
										<div class="g24-col-sm-19">
											<div id="transfer_deposit">
												<div class="transfer_content">
													<div class="row transfer">
														<div class="g24-col-sm-24">
															<div class="form-group">
																<label class="control-label g24-col-sm-1" for="transfer_bank_account_name"></label>
																<input type="radio" name="xd_bank_id" id="xd_1" onclick="set_bank('006');set_branch_code('0071');"><label for="xd_1"> ธ.กรุงไทย จำกัด สาขาการปิโตรเลียม</label>
															</div>
														</div>
														<div class="g24-col-sm-24">
															<div class="form-group">
																<label class="control-label g24-col-sm-1" for="transfer_bank_account_name"></label>
																<input type="radio" name="xd_bank_id" id="xd_2" onclick="set_bank('002');set_branch_code('1082');"><label for="xd_2"> ธ.กรุงเทพ จำกัด สาขาเอนเนอร์ยี่ คอมเพล็กซ์</label>
															</div>
														</div>
														<div class="g24-col-sm-24">
															<div class="form-group">
																<label class="control-label g24-col-sm-1" for="transfer_bank_account_name"></label>
																<input type="radio" name="xd_bank_id" id="xd_3" onclick="set_bank('011');set_branch_code('0211');"><label for="xd_3"> ธ.ทหารไทย จำกัด สาขาการปิโตรเลียม </label>
															</div>
														</div>
													</div>
													<div class="row transfer">
														<div class="g24-col-sm-24">
															<div class="form-group">
																<label class="control-label g24-col-sm-1" for="local_account_id"></label>
																<input type="radio" name="xd_bank_id" id="xd_4" onclick="set_bank('');set_branch_code('');"><label for="xd_4"> บัญชีเงินฝาก </label>
																<select class="form-control" name="local_account_id" id="local_account_id" style="display: initial !important;width: 230px !important;">
																	<option value="">เลือกบัญชี</option>
																	<?php
																		foreach ($maco_account as $key => $value) {
																			echo '<option value="'.$value['account_id'].'">'.$value['account_id'].' '.$value['account_name'].'</option>';
																		}
																	?>
																</select>
															</div>
														</div>
													</div>
													<div class="row transfer">
														<div class="g24-col-sm-24">
															<div class="form-group">
																<label class="control-label g24-col-sm-1" for="transfer_other"></label>
																<input type="radio" name="xd_bank_id" id="xd_5" onclick="set_bank('');set_branch_code('');"><label for="xd_5"> อื่นๆ </label>
																<input type="text" name="transfer_other" id="transfer_other" class="form-control" style="display: initial !important;width: 200px !important;">
															</div>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
								<div class="g24-col-sm-24" id="che_sec" style="display: none;">
									<div class="form-group g24-col-sm-16">
										<label class="g24-col-sm-5 control-label right"></label>
										<div class="g24-col-sm-19">
											<div id="cheque_deposit">
												<div class="cheque_content">
													<div class="row cheque">
														<div class="g24-col-sm-24">
															<div class="form-group">
																<label class="control-label g24-col-sm-1" for="transfer_bank_account_name"></label>
																<input type="radio" name="che_bank_id" id="che_1" onclick="set_bank('006');set_branch_code('0071');"><label for="che_1"> ธ.กรุงไทย จำกัด สาขาการปิโตรเลียม</label>
															</div>
														</div>
														<div class="g24-col-sm-24">
															<div class="form-group">
																<label class="control-label g24-col-sm-1" for="transfer_bank_account_name"></label>
																<input type="radio" name="che_bank_id" id="che_2" onclick="set_bank('002');set_branch_code('1082');"><label for="che_2"> ธ.กรุงเทพ จำกัด สาขาเอนเนอร์ยี่ คอมเพล็กซ์</label>
															</div>
														</div>
														<div class="g24-col-sm-24">
															<div class="form-group">
																<label class="control-label g24-col-sm-1" for="transfer_bank_account_name"></label>
																<input type="radio" name="che_bank_id" id="che_3" onclick="set_bank('011');set_branch_code('0211');"><label for="che_3"> ธ.ทหารไทย จำกัด สาขาการปิโตรเลียม </label>
															</div>
														</div>
													</div>
													<div class="row cheque">
														<div class="g24-col-sm-24">
															<div class="form-group">
																<input class="form-control g24-col-sm-14" name="cheque_no" id="cheque_no" placeholder="ระบุบัญชีเงินฝาก" />
															</div>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
								<div class="g24-col-sm-24" id="other_sec" style="display: none;">
									<div class="form-group g24-col-sm-16">
										<label class="g24-col-sm-5 control-label right"></label>
										<div class="g24-col-sm-19">
											<div id="cheque_deposit">
												<div class="cheque_content">
													<div class="row cheque">
														<div class="g24-col-sm-24">
															<div class="form-group">
																<label class="control-label g24-col-sm-8" for="other">อื่นๆ :</label>
																<input class="form-control g24-col-sm-14" name="other" id="other" placeholder="ระบุ" />
															</div>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					
					<div class="g24-col-sm-24  m-t-1 text-center">
						<div class="form-group">						
							<button type="button" id="bt_save" class="btn btn-info bt_check_submit" onclick="">บันทึก</button>							
						</div>					
					</div>	
				</form>
            </div>
			<div class="text_center m-t-1">&nbsp;</div>
        </div>
    </div>
</div>

<div class="modal fade" id="myModal" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">ข้อมูลสมาชิก</h4>
            </div>
            <div class="modal-body">
                <div class="input-with-icon">
                    <!-- <input class="form-control input-thick pill m-b-2" type="text" placeholder="กรอกเลขทะเบียนหรือชื่อ-สกุล" name="search_text" id="search_text" onkeyup="get_search_member_debt()">
					<span class="icon icon-search input-icon"></span> -->
					<div class="row">
						<div class="col">
							<label class="col-sm-2 control-label">รูปแบบค้นหา</label>
							<div class="col-sm-4">
								<div class="form-group">
									<select id="search_list" name="search_list" class="form-control m-b-1">
										<option value="">เลือกรูปแบบค้นหา</option>
										<option value="member_id">รหัสสมาชิก</option>
										<option value="employee_id">รหัสพนักงาน</option>
										<option value="id_card">หมายเลขบัตรประชาชน</option>
										<option value="firstname_th">ชื่อสมาชิก</option>
										<option value="lastname_th">นามสกุล</option>
									</select>
								</div>
							</div>
							<label class="col-sm-1 control-label" style="white-space: nowrap;"> ค้นหา </label>
							<div class="col-sm-4">
								<div class="form-group">
									<div class="input-group">
										<input id="search_text" name="search_text" class="form-control m-b-1" type="text" value="<?php echo @$data['id_card']; ?>">
										<span class="input-group-btn">
											<button type="button" id="member_search" class="btn btn-info btn-search"><span class="icon icon-search"></span></button>
										</span>	
									</div>
								</div>
							</div>	
						</div>
					</div>
                </div>
                <div class="bs-example" data-example-id="striped-table">
                    <table class="table table-striped">
                        <!-- <tbody id="table_data"> -->
						<tbody id="result_member">

                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" id="close" class="btn btn-default" data-dismiss="modal">ปิดหน้าต่าง</button>
            </div>
        </div>
    </div>
</div>

<script>
var base_url = $('#base_url').attr('class');
$( document ).ready(function() {	
	$('#myModal').on('shown.bs.modal', function () {
		$('#search_text').focus();
	})  
});

function get_search_member_debt(){
	$.ajax({
		type: "POST",
		 url: base_url+'debt/get_search_member_debt',
		data: {
			search_text : $("#search_text").val(),
			form_target : 'add'
		},
		success: function(msg) {
			console.log(msg);
			$("#table_data").html(msg);
		}
	});
}

function view_detail(non_pay_id,non_pay_year_month,receipt_amount,non_pay_amount_balance,pay_amount){
	console.log("view-detail", receipt_amount);
	var new_receipt_amount = numeral(receipt_amount).value();
	$("#non_pay_year_month").val(non_pay_year_month);
	$("#receipt_amount").val( (new_receipt_amount<=0 ? 0 : new_receipt_amount) );
	$("#non_pay_amount_balance").val(non_pay_amount_balance);
	$("#pay_amount").val(pay_amount);
	$("#non_pay_id").val(non_pay_id);
	
	$.ajax({
		type: "POST",
		url: base_url+'debt/get_non_pay_detail',
		data: {
			non_pay_id : non_pay_id
		},
		success: function(data) {
			//console.log(data);	
			$('#table_data_debt').html(data);			
		}
	});
	
	$('#viewDetail').modal('show');
}		

function sum_pay_amount_all(ele){	
	// format_the_number(ele);
	var pay_debt_amount_old = 0;
	var pay_debt_amount = 0;
	var pay_amount_all = 0;

	$("input[name^=pay_debt_amount]").each(function() { 
		var amount_old = $('#'+this.id+'_old').val();
		pay_debt_amount_old = numeral(amount_old).value();
		pay_debt_amount = numeral(this.value).value();
		if(isNaN(pay_debt_amount)){
			pay_debt_amount = 0;
		}	
		
		if(pay_debt_amount <= pay_debt_amount_old){
			//
			$('#'+this.id).val();
		}else{
			$('#'+this.id).val('');
			pay_debt_amount = 0;
			swal('ยอดชำระต้องไม่เกินยอดค้างชำระ');
		}
		
		pay_amount_all += pay_debt_amount;
    })
	

		var numeraljs = numeral($(ele).val()).value();
		var this_amount = $(ele).val();
		var new_format = 0;
		var check_number = this_amount.split('.');
		if(check_number.length >= 2){
			if(check_number[1].length==1){
				new_format = numeral(numeraljs).format('0,0.0');
			}else if(this_amount.split('.')[1].length==2){
				new_format = numeral(numeraljs).format('0,0.00');
				$(ele).val(new_format);
			}else if(check_number[1].length!=0){
				new_format = numeral(numeraljs).format('0,0.00');
				$(ele).val(new_format);
				var real_number = parseFloat("0."+check_number[1]);
			}
		}else{
			new_format = numeral(this_amount).format('0,0');
			$(ele).val(new_format);
		}
		$('#pay_amount_all').html( numeral(pay_amount_all).format('0,0.00') );
}


function format_number(ele){
	alert("onblur");
	var this_amount = $(ele).val();
	console.log("onchange: ", this_amount);
	console.log(this_amount.split('.').length);
	var new_format = 0;
	if(this_amount.split('.').length >= 1){
		new_format = numeral(this_amount).format('0,0.00');
		return;
	}else{
		new_format = numeral(this_amount).format('0,0.00');
	}
	$(ele).val(new_format);
}

function format_the_number(ele){
	var value = $('#'+ele.id).val();
	if(value!=''){
		value = value.replace(',','');
		value = parseInt(value);
		value = value.toLocaleString();
		if(value == 'NaN'){
			$('#'+ele.id).val('');
		}else{
			$('#'+ele.id).val(value);
		}
	}else{
		$('#'+ele.id).val('');
	}
	
}

function check_member_id() {
	var member_id = $('#member_id').first().val();
	var keycode = (event.keyCode ? event.keyCode : event.which);
	if(keycode == '13'){
		$.post(base_url+"save_money/check_member_id", 
		{	
		member_id: member_id
		}
		, function(result) {
			obj = JSON.parse(result);
			mem_id = obj.member_id;
			if(mem_id != undefined){
				document.location.href = '<?php echo base_url(uri_string())?>?id='+mem_id
			}else{					
				swal('ไม่พบรหัสสมาชิกที่ท่านเลือก','','warning'); 
			}
		});		
	}
}

$('#member_search').click(function(){
	if($('#search_list').val() == '') {
		swal('กรุณาเลือกรูปแบบค้นหา','','warning');
	} else if ($('#search_text').val() == ''){
		swal('กรุณากรอกข้อมูลที่ต้องการค้นหา','','warning');
	} else {
		$.ajax({  
			url: base_url+"ajax/search_member_by_type",
			method:"post",  
			data: {
				search_text : $('#search_text').val(), 
				search_list : $('#search_list').val()
			},  
			dataType:"text",  
			success:function(data) {
				$('#result_member').html(data.replace("?member_id=", "?id="));  
			}  ,
			error: function(xhr){
				console.log('Request Status: ' + xhr.status + ' Status Text: ' + xhr.statusText + ' ' + xhr.responseText);
			}
		});  
	}
});

$(document).ready(function() {
  $(window).keydown(function(event){
    if(event.keyCode == 13) {
		if(window.location.search != ""){
			var res = window.location.search.split("=");
			console.log(res);
			if(res[1] == $("#member_id").val()){
				event.preventDefault();
      			return false;
			}
			
		}
    }
  });
});

$('#bt_save').click(function(){   
	$('#from_view').submit();   
});

function set_bank(val) {
	$("#bank_id").val(val);
}

function set_branch_code(val) {
	$("#branch_code").val(val);
}

function show(val) {
	if (val == 'xd_sec') {
		$("#xd_sec").show();
		$("#che_sec").hide();
		$("#other_sec").hide();
	} else if (val == 'che_sec') {
		$("#xd_sec").hide();
		$("#che_sec").show();
		$("#other_sec").hide();
	} else if(val == 'other_sec') {
		$("#xd_sec").hide();
		$("#che_sec").hide();
		$("#other_sec").show();
	}else {
		$("#xd_sec").hide();
		$("#che_sec").hide();
		$("#other_sec").hide();
	}

}
</script>

<script src="//cdnjs.cloudflare.com/ajax/libs/numeral.js/2.0.6/numeral.min.js"></script>
