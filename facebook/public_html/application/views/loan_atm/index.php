<div class="layout-content">
    <div class="layout-content-body">
		<style>
			.center {
				text-align: center;
			}
			.left {
				text-align: left !important;
			}
			.right {
				text-align: right !important;
			}
			.modal-dialog-account {
				margin:auto;
				margin-top:7%;
			}
			.modal-dialog-data {
				width:90% !important;
				margin:auto;
				margin-top:1%;
				margin-bottom:1%;
			}
			.modal-dialog-cal {
				width:80% !important;
				margin:auto;
				margin-top:1%;
				margin-bottom:1%;
			}
			.modal-dialog-file {
				width:50% !important;
				margin:auto;
				margin-top:1%;
				margin-bottom:1%;
			}
			.modal_data_input{
				margin-bottom: 5px;
			}
			.form-group{
				margin-bottom: 5px;
			  }
			  .red{
				color: red;
			  }
			  .green{
				color: green;
			  }
		</style> 
		<div class="row">
			<div class="form-group">
				<div class="col-sm-6">
					<h1 class="title_top">การกู้เงินฉุกเฉิน ATM</h1>
					<?php $this->load->view('breadcrumb'); ?>
				</div>
				<div class="col-sm-6">
				<br>
					<div class="g24-col-sm-24" style="text-align:right;padding-right:0px;margin-right:0px;">
					<?php if(@$member_id!=''){ ?>
						<?php if(empty($row_loan_atm)){ ?>
							<a class="link-line-none">
								<button class="btn btn-primary" style="margin-right:5px;" onclick="open_modal('loan_contract_modal')">สร้างสัญญา</button>
							</a>
						<?php }else{ ?>
							<?php if($row_loan_atm['loan_atm_status']=='0'){ ?>
								<a class="link-line-none">
									<button class="btn btn-primary" style="margin-right:5px;" onclick="open_modal('loan_contract_modal')">แก้ไขสัญญา</button>
								</a>
							<?php //}else if($row_loan_atm['loan_atm_status'] == '1' && $row_loan_atm['activate_status'] == '0'){ ?>
							<?php }else if($row_loan_atm['loan_atm_status'] == '1'){ ?>
								<?php if(@$loan_atm_detail_id_no_transfer != ''){ ?>
								<a class="link-line-none" href="<?php echo base_url(PROJECTPATH.'/loan_atm/payment_slip/'.$loan_atm_detail_id_no_transfer); ?>" target="_blank">
									<button class="btn btn-primary" style="margin-right:5px;">พิมพ์ใบนำจ่าย</button>
								</a>
								<?php } ?>
								<?php if($row_loan_atm['change_amount_status'] != '1'){ ?>
								<a class="link-line-none">
									<!--<button class="btn btn-primary" style="margin-right:5px;" onclick="open_modal('loan_change_amount_modal')">เปลี่ยนแปลงวงเงิน</button>-->
									<button class="btn btn-primary" style="margin-right:5px;" onclick="check_modal('loan_change_amount_modal')">เปลี่ยนแปลงวงเงิน</button>
								</a>
								<?php } ?>
								<a class="link-line-none">
									<!--<button class="btn btn-primary" onclick="open_modal('loan_modal')">ทำรายการกู้เงิน</button>-->
									<button class="btn btn-primary" onclick="check_modal('loan_modal')">ทำรายการกู้เงิน</button>
								</a>
							<?php } ?>
						<?php } ?>
					<?php } ?>
					</div>
				</div>
			</div>
		</div>
		<div class="row gutter-xs">
			<div class="col-xs-12 col-md-12">
				<div class="panel panel-body" style="padding-top:0px !important;">
						<?php $this->load->view('search_member_new'); ?>
						<div class="g24-col-sm-24">
							<div class="form-group g24-col-sm-8">
								<label class="g24-col-sm-10 control-label" for="form-control-2">เงินเดือน</label>
								<div class="g24-col-sm-14" >
									<input id="form-control-2"  class="form-control " type="text" value="<?php echo number_format(@$row_member['salary']); ?>"  readonly>
								</div>
							</div>
							<div class="form-group g24-col-sm-8">
								<label class="g24-col-sm-10 control-label" for="form-control-2">รายได้อื่นๆ</label>
								<div class="g24-col-sm-14" >
									<input id="form-control-2"  class="form-control " type="text" value=""  readonly>
								</div>
							</div>
							<div class="form-group g24-col-sm-8">
								<label class="g24-col-sm-10 control-label" for="form-control-2">ยอดชำระเดือนล่าสุด</label>
								<div class="g24-col-sm-14" >
									<input id="form-control-2"  class="form-control " type="text" value=""  readonly>
								</div>
							</div>
						</div>
					<div class="" style="padding-top:0;">
						<h3 >หุ้น</h3>
						<div class="g24-col-sm-24">
							<div class="form-group g24-col-sm-8">
								<label class="g24-col-sm-10 control-label ">จำนวนหุ้นสะสม</label>
								<div class="g24-col-sm-14">
									<input class="form-control" type="text" id="share_total" value="<?php echo number_format(@$count_share); ?>"  readonly>
								</div>
							</div>
							<div class="form-group g24-col-sm-8">
								<label class="g24-col-sm-10 control-label ">คิดเป็นมูลค่า</label>
								<div class="g24-col-sm-14">
									<input class="form-control" type="text" value="<?php echo number_format(@$cal_share,2); ?>"  readonly>
								</div>
							</div>
						</div>
					</div>
					<div class="" style="padding-top:0;">
						<h3 >เงินกู้ฉุกเฉิน ATM</h3>
						<div class="g24-col-sm-24">
							<div class="form-group g24-col-sm-8">
								<label class="g24-col-sm-10 control-label ">เลขที่คำร้อง</label>
								<div class="g24-col-sm-14">
									<input class="form-control" type="hidden" id="activate_status" name="activate_status" value="<?php echo @$row_loan_atm['activate_status']; ?>">
									<input class="form-control" type="text" value="<?php echo @$row_loan_atm['petition_number']; ?>"  readonly>
								</div>
							</div>
							<div class="form-group g24-col-sm-8">
								<label class="g24-col-sm-10 control-label ">เลขที่สัญญา</label>
								<div class="g24-col-sm-14">
									<input class="form-control" type="text" value="<?php echo @$row_loan_atm['contract_number']; ?>" readonly>
								</div>
							</div>
							<div class="form-group g24-col-sm-8">
								<label class="g24-col-sm-10 control-label ">เลขที่บัญชี</label>
								<div class="g24-col-sm-14">
									<input class="form-control" type="text" value="<?php echo @$row_loan_atm['account_id']; ?>" readonly>
								</div>
							</div>
						</div>
						<div class="g24-col-sm-24">
							<div class="form-group g24-col-sm-8">
								<label class="g24-col-sm-10 control-label ">วงเงินกู้ทั้งหมด</label>
								<div class="g24-col-sm-14">
									<?php
										if(@$row_loan_atm['total_amount_approve']!=''){
											$total_amount = @$row_loan_atm['total_amount_approve'];
										}else{
											$total_amount = @$row_loan_atm['total_amount'];
										}
									?>
									<input class="form-control" type="text" value="<?php echo number_format($total_amount,2); ?>"  readonly>
								</div>
							</div>
							<div class="form-group g24-col-sm-8">
								<label class="g24-col-sm-10 control-label ">วงเงินกู้คงเหลือ</label>
								<div class="g24-col-sm-14">
									<input class="form-control" type="text" id="total_amount_balance" value="<?php echo @$row_loan_atm['total_amount_balance']!=''?number_format($row_loan_atm['total_amount_balance'],2):''; ?>"  readonly>
								</div>
							</div>
							<div class="form-group g24-col-sm-8">
								<label class="g24-col-sm-10 control-label ">สถานะ</label>
								<div class="g24-col-sm-14">
									<input class="form-control" type="text" id="total_amount_balance" value="<?php echo @$loan_atm_status[$row_loan_atm['loan_atm_status']]; ?>"  readonly>
								</div>
							</div>
						</div>
					</div>
					<div class="g24-col-sm-24 m-t-1 hidden_table" id="table_3" >
						<div class="bs-example" data-example-id="striped-table">
							<table class="table table-bordered table-striped table-center">
								<thead>
									<tr class="bg-primary">
										<th width="5%">#</th>
										<th width="15%">วันที่ทำรายการ</th>
										<th width="15%">เลขที่คำร้อง/เลขที่สัญญา</th>
										<th width="15%">วงเงินกู้</th>
										<th width="15%">วงกู้คงเหลือ</th>
										<th width="15%">ยอดหนี้คงเหลือ</th>
										<th width="10%">สถานะสัญญา</th>
										<th width="10%">สถานะการใช้งาน</th>
										<th width="10%">จัดการ</th>
									</tr>
								</thead>
								<tbody>
								<?php if(!empty($row_loan_atm_all)){ $i=1; ?>
									<?php foreach($row_loan_atm_all as $key => $value){ ?>
										<tr>
											<td><?php echo $i++; ?></td>
											<td><?php echo $this->center_function->ConvertToThaiDate($value['createdatetime']); ?></td>
											<td>
												<a href="<?php echo base_url(PROJECTPATH.'/loan_atm/petition_emergent_atm_pdf/'.$value['loan_atm_id']); ?>" target="_blank"><?php echo $value['petition_number']; ?></a>
												<?php echo ($value['contract_number']!='')?"/".$value['contract_number']:''; ?>
											</td>
											<td class="right">
												<?php
													$total_amount_approve = (@$value['total_amount_approve']>0)?@$value['total_amount_approve']:@$value['total_amount'];
													echo number_format(@$total_amount_approve,2);
												?>
											</td>
											<td class="right"><?php echo (@$value['total_amount_balance']>@$value['total_amount_approve'])?number_format(@$value['total_amount_approve'],2):number_format(@$value['total_amount_balance'],2); ?></td>											
											<td class="right"><?php echo number_format(@$total_amount_approve-@$value['total_amount_balance'],2); ?></td>
											<td><?php echo $loan_atm_status[$value['loan_atm_status']]; ?></td>
											<td><?php echo $loan_atm_activate_status[$value['activate_status']]; ?></td>
											<td>
												<div class="dropdown">
													<button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown" style="width:90%">จัดการ
													<span class="caret"></span></button>
													<ul class="dropdown-menu">
														<?php if($value['loan_atm_status'] == '0'){ ?>
															<li><a title="แก้ไข" style="cursor:pointer;padding-left:2px;padding-right:2px" onclick="open_modal('loan_contract_modal','<?php echo $value['loan_atm_id']; ?>')"><span style="cursor: pointer;" class="icon icon-edit"></span> แก้ไข</a></li>
															<li><a title="เอกสารพิจารณาเงินกู้" style="cursor: pointer;padding-left:2px;padding-right:2px" href="<?php echo PROJECTPATH."/report_loan_data/coop_report_loan_atm_detail_preview?member_id=".@$row_member['member_id']."&loan_id=".$value['loan_atm_id']; ?>" target="_blank"><span class="icon icon-list-alt"></span> เอกสารพิจารณาเงินกู้</a></li>
														<?php }else{ ?>
															<?php if($value['loan_atm_status'] == '1'){ ?>
																<?php if($value['activate_status'] == '0'){ ?>
																	<li><a title="ทำรายการกู้เงิน" style="cursor:pointer;padding-left:2px;padding-right:2px" onclick="open_modal('loan_modal')"><span style="cursor: pointer;" class="icon icon-usd"></span> ทำรายการกู้เงิน</a></li>
																<?php } ?>
															<!--li><a title="ปิดยอดเงิน" style="cursor:pointer;padding-left:2px;padding-right:2px" onclick="open_modal('loan_pay_all_modal')"><span style="cursor: pointer;" class="icon icon-edit"></span> ปิดยอดเงิน</a></li-->
															<li><a title="ยกเลิกสัญญา" style="cursor:pointer;padding-left:2px;padding-right:2px" onclick="open_modal('loan_cancel_contract_modal')"><span style="cursor: pointer;" class="icon icon-remove"></span> ยกเลิกสัญญา</a></li>
																<?php if($value['activate_status'] == '0'){ ?>
																	<li><a title="ระงับสัญญา" style="cursor:pointer;padding-left:2px;padding-right:2px" onclick="loan_atm_lock('<?php echo $value['loan_atm_id']; ?>','<?php echo $value['member_id']; ?>')"><span style="cursor: pointer;" class="icon icon-lock"></span> ระงับสัญญา</a></li>
																<?php }else{ ?>
																	<li><a title="ปลดระงับสัญญา" style="cursor:pointer;padding-left:2px;padding-right:2px" onclick="loan_atm_unlock('<?php echo $value['loan_atm_id']; ?>','<?php echo $value['member_id']; ?>')"><span style="cursor: pointer;" class="icon icon-unlock"></span> ปลดระงับสัญญา</a></li>
																<?php } ?>
															<?php } ?>

															<!--<li><a title="ดูรายละเอียด" style="cursor:pointer;padding-left:2px;padding-right:2px" href="<?php echo base_url(PROJECTPATH.'/loan_atm/show_loan_atm_detail/'.$value['loan_atm_id']); ?>" target="_blank"><span style="cursor: pointer;" class="icon icon-edit"></span> ดูรายละเอียด</a></li>-->
														<?php } ?>
															<li><a title="สัญญาเงินกู้ ATM" style="cursor:pointer;padding-left:2px;padding-right:2px" href="<?php echo base_url(PROJECTPATH.'/loan_atm/petition_emergent_atm_pdf/'.$value['loan_atm_id']); ?>" target="_blank"><span style="cursor: pointer;" class="icon icon-edit"></span> สัญญาเงินกู้ ATM</a></li>
															<li><a title="รายละเอียดการชำระเงิน" style="cursor: pointer;padding-left:2px;padding-right:2px" href="<?php echo PROJECTPATH."/loan_atm/loan_atm_payment_detail/".$value['loan_atm_id']; ?>" target="_blank"><span class="icon icon-list-alt"></span> รายละเอียดการชำระเงิน</a></li>
															<?php if($value['loan_atm_detail_id_no_transfer']!=''){ ?>
															<li><a title="ใบนำจ่าย" style="cursor:pointer;padding-left:2px;padding-right:2px" href="<?php echo base_url(PROJECTPATH.'/loan_atm/payment_slip/'.$value['loan_atm_detail_id_no_transfer']); ?>" target="_blank"><span style="cursor: pointer;" class="icon icon-edit"></span> ใบนำจ่าย</a></li>
															<?php } ?>
													</ul>
												</div>
											</td>
										</tr>
									<?php } ?>
								<?php }else{ ?>
									<tr><td colspan="8" align="center">ไม่พบข้อมูล</td></tr>
								<?php } ?>
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<form action="<?php echo base_url(PROJECTPATH.'/loan_atm/loan_atm_save'); ?>" method="POST" id="form_normal_loan" enctype="multipart/form-data">
	<div class="modal fade" id="loan_modal" role="dialog" style="overflow-x: hidden;overflow-y: auto;">
		<div class="modal-dialog modal-dialog-data">
			<div class="modal-content data_modal">
				<div class="modal-header modal-header-confirmSave">
					<button type="button" class="close" data-dismiss="modal">x</button>
					<h2 class="modal-title" id="type_name">กู้เงินฉุกเฉิน ATM</h2>
				</div>
				<div class="modal-body">
					<?php $this->load->view('loan_atm/loan_modal'); ?>
				</div>
			</div>
		</div>
	</div>
</form>
<div class="modal fade" id="loan_contract_modal" role="dialog" style="overflow-x: hidden;overflow-y: auto;">
	<div class="modal-dialog modal-dialog-data">
		<div class="modal-content data_modal">
			<div class="modal-header modal-header-confirmSave">
				<button type="button" class="close" data-dismiss="modal">x</button>
				<h2 class="modal-title" id="type_name">สร้างสัญญากู้เงินฉุกเฉิน ATM</h2>
			</div>
			<form action="<?php echo base_url(PROJECTPATH.'/loan_atm/loan_contract_save'); ?>" method="POST" id="form_contract" enctype="multipart/form-data">
				<!--<input type="text" name="loan_atm_id" id="loan_atm_id" value="<?php echo @$row_loan_atm['loan_atm_id']; ?>">-->
				<input type="hidden" name="loan_atm_id" id="loan_atm_id" class="loan_atm_id" value="">
				<input type="hidden" name="loan_id" value="<?php echo @$loan_id; ?>">
                <input type="hidden" name="principal_amount" value="<?php echo number_format($principal_amount,2,".","");?>">
                <input type="hidden" name="interest_amount" value="<?php echo number_format($interest_amount,2,".","");?>">
                <input type="hidden" name="pay_amount" value="<?php echo number_format($deduct_amount,2,".","");?>">

				<div class="modal-body">
					<div class="g24-col-sm-24 modal_data_input">
						<div class="g24-col-sm-24 modal_data_input">
							<label class="g24-col-sm-4 control-label ">คำร้องเลขที่</label>
							<div class="g24-col-sm-5">
								<input class="form-control" type="text" id="petition_number" name="petition_number" value="<?php echo @$row_loan_atm['petition_number']; ?>" readonly>
							</div>
							<label class="g24-col-sm-3 control-label ">แนบไฟล์คำขอกู้</label>
							<div class="g24-col-sm-5">
								<label class="fileContainer btn btn-info">
									<span class="icon icon-paperclip"></span>
									เลือกไฟล์
									<input type="file" class="form-control" name="file_attach[]" value="" multiple>
								</label>

							</div>
							<?php if(!empty($row_loan_atm_file)){ ?>
							<div class="g24-col-sm-1">
								<button class="btn btn-primary" id="btn_show_file" type="button" onclick="open_modal('show_file_attach')">แสดงไฟล์แนบ</button>
							</div>
							<?php } ?>
						</div>
						<div class="g24-col-sm-24 modal_data_input">
							<label class="g24-col-sm-4 control-label">รหัสสมาชิก</label>
							<div class="g24-col-sm-5">
								<input class="form-control" id="member_id" type="text" name="member_id" value="<?php echo @$row_member['member_id']; ?>" readonly>
							</div>
							<label class="g24-col-sm-3 control-label ">ชื่อ-สกุล</label>
							<div class="g24-col-sm-7">
								<input class="form-control" type="text" value="<?php echo @$row_member['firstname_th'].' '.@$row_member['lastname_th'] ?>" readonly>
							</div>
						</div>
						<div class="g24-col-sm-24 modal_data_input" style="margin-bottom: 20px;">
							<label class="g24-col-sm-4 control-label">วงเงินที่ขอกู้</label>
							<div class="g24-col-sm-5">
								<?php
									$max_loan_amount = (@$share_collect_value > @$loan_atm_setting['max_loan_amount'])?@$loan_atm_setting['max_loan_amount']:@$share_collect_value;
								?>
								<input class="form-control" type="text" id="total_amount" name="total_amount" onkeyup="format_the_number(this)" onBlur="change_prev_loan_pay_type('loan_contract_modal');" value="<?php echo number_format(@$row_loan_atm['total_amount']!=''?$row_loan_atm['total_amount']:@$max_loan_amount); ?>">
								<input type="hidden" id="max_loan_amount" value="<?php echo number_format(@$max_loan_amount); ?>">
								<input type="hidden" id="total_amount_val" value="<?php echo number_format(@$row_loan_atm['total_amount']!=''?$row_loan_atm['total_amount']:@$max_loan_amount); ?>">
								<input type="hidden" id="loan_reason_val" value="<?php echo @$row_loan_atm['loan_reason']!=''?@$row_loan_atm['loan_reason']:""; ?>">
							</div>
							<label class="g24-col-sm-3 control-label ">เหตุผลการกู้</label>
							<div class="g24-col-sm-9">
								<select name="loan_reason" class="form-control" id="loan_reason">
									<option value="">ไม่ระบุ</option>
									<?php
									foreach($rs_loan_reason as $key => $row_loan_reason){
									?>
									<option value="<?php echo $row_loan_reason['loan_reason_id']; ?>" <?php echo @$row_loan_atm['loan_reason']==$row_loan_reason['loan_reason_id']?'selected':''; ?>><?php echo $row_loan_reason['loan_reason']; ?></option>
									<?php } ?>
								</select>
							</div>
						</div>
						<?php 
						if(!empty($prev_loan_active)){
							$i=0;
							$total_deduct = 0;
						foreach($prev_loan_active as $key => $value){ 
							// var_dump($value);
							//echo '<pre>'; print_r($value); echo '</pre>';
							$is_checked = "";
							if(@$value['ref_loan_deduct']){
								$is_checked = "checked";
								//echo "<script>$( document ).ready(function() {change_prev_loan_pay_type('loan_contract_modal');});</script>";
							}
							// for ($j=0; $j < sizeof($row_loan_atm['prev_loan_deduct']); $j++) { 
							// 	if($value['id']==$row_loan_atm['prev_loan_deduct'][$j]['ref_id']){
							// 		$is_checked = "checked";
							// 		$total_deduct += $row_loan_atm['prev_loan_deduct'][$j]['pay_amount'];
							// 		break;
							// 	}
							// }
						?>
							<div class="g24-col-sm-24 modal_data_input">
								<label class="g24-col-sm-6 control-label" ><?php echo $i==0?'หักกลบสัญญาเดิม':''; ?></label>
								<div class="g24-col-sm-5">
									<div class="form-group">
										<label class="custom-control custom-control-primary custom-checkbox" style="padding-top: 9px;">
											<input type="checkbox" class="custom-control-input prev_loan_checkbox" id="prev_loan_checkbox_<?php echo $i; ?>" name="prev_loan[<?php echo $i; ?>][id]" value="<?php echo $value['id']; ?>" onclick="change_prev_loan_pay_type('loan_contract_modal')" ref_id="<?php echo $value['id']; ?>" data_type="<?php echo $value['type']; ?>" attr_index="<?php echo $i; ?>" <?php echo $is_checked?>>
											<span class="custom-control-indicator" style="margin-top: 9px;"></span>
											<span class="custom-control-label"><?php echo $value['contract_number']." (".number_format($value['loan_amount_balance'],2).")"; ?></span>
										</label>
										<input type="hidden" name="prev_loan[<?php echo $i; ?>][type]" value="<?php echo $value['type']; ?>">
										<input type="hidden" id="prev_loan_total_<?php echo $i; ?>" value="<?php echo number_format($value['prev_loan_total'],2); ?>">
										<input type="hidden" id="principal_without_finance_month_<?php echo $i; ?>" value="<?php echo number_format($value['principal_without_finance_month'],2); ?>">
									</div>
								</div>
								<div class="g24-col-sm-6">
									<label class="custom-control custom-control-primary custom-checkbox" style="padding-top: 3px;">
									<input type="radio" name="prev_loan[<?php echo $i; ?>][pay_type]" id="prev_loan_pay_type_1_<?php echo $i; ?>" onclick="change_prev_loan_pay_type('loan_contract_modal')" value="principal" <?php echo ($value['checked']=="principal" ? "checked" : "")?>> คืนดอกเบี้ยส่วนต่าง 
									<input type="radio" name="prev_loan[<?php echo $i; ?>][pay_type]" id="prev_loan_pay_type_2_<?php echo $i; ?>" onclick="change_prev_loan_pay_type('loan_contract_modal')" value="all" <?php echo ($value['checked']=="all" ? "checked" : "")?>> คืนต้นและดอก
									</label>
								</div>
								<label class="g24-col-sm-2" >ยอดเงิน</label>
								<div class="g24-col-sm-3">
									<input type="hidden" name="prev_loan[<?php echo $i; ?>][interest]" value="<?=$value['interest']?>">
									<input type="hidden" name="prev_loan[<?php echo $i; ?>][principal]" value="<?=$value['principal']?>">
									<!--<input class="form-control prev_loan_amount" attr_index="<?php echo $i; ?>" type="text" name="prev_loan[<?php echo $i; ?>][amount]" id="prev_loan_amount_<?php echo $i; ?>" value="<?php echo number_format($value['prev_loan_total']);?>">-->
									<input class="form-control prev_loan_amount" attr_index="<?php echo $i; ?>" type="text" name="prev_loan[<?php echo $i; ?>][amount]" id="prev_loan_amount_<?php echo $i; ?>" value="">
								</div>
							</div>
						<?php $i++; }
						}
						?>

						<!-- <div class="g24-col-sm-24 modal_data_input" style="margin-bottom: 20px;">
							<label class="g24-col-sm-4 control-label">หักกลบเงินต้น</label>
							<div class="g24-col-sm-5">
								<input class="form-control" type="text" value="<?php echo number_format($principal_amount,2); ?>" readonly>
							</div>
							<label class="g24-col-sm-3 control-label">หักกลบดอกเบี้ย</label>
							<div class="g24-col-sm-5">
								<input class="form-control" type="text" value="<?php echo number_format($interest_amount,2); ?>" readonly>
							</div>
						</div> -->
						<div class="g24-col-sm-24 modal_data_input" style="margin-bottom: 20px;">
							<label class="g24-col-sm-4 control-label">ยอดหักกลบ</label>
							<div class="g24-col-sm-5">
								<input class="form-control" type="text" id="deduct_amount" name="deduct_amount" value="<?php echo number_format($total_deduct, 2)?>" readonly>
							</div>
						</div>
						<div class="g24-col-sm-24 modal_data_input" style="margin-bottom: 20px;">
							<label class="g24-col-sm-4 control-label">ยอดรับสุทธิ</label>
							<div class="g24-col-sm-5">
								<input class="form-control" type="text" id="net_amount" value="0" readonly>
							</div>
						</div>
						<div class="center">
							<button class="btn btn-primary" id="submit_button" type="button" onclick="check_submit_contract()">บันทึกคำร้อง</button>&nbsp;&nbsp;&nbsp;
							<button class="btn btn-default" type="button" data-dismiss="modal">ยกเลิก</button>
						</div>

					</div>
					&nbsp;
				</div>
			</form>
		</div>
	</div>
</div>
<div class="modal fade" id="show_file_attach" role="dialog">
	<div class="modal-dialog modal-dialog-file">
	  <div class="modal-content data_modal">
		<div class="modal-header modal-header-confirmSave">
		  <button type="button" class="close" onclick="close_modal('show_file_attach')">&times;</button>
		  <h2 class="modal-title">แสดงไฟล์แนบ</h2>
		</div>
		<div class="modal-body" id="show_file_space">
			<table width="100%">
				<?php foreach($row_loan_atm_file as $key => $value){ ?>
					<tr class="file_row" id="file_<?php echo $value['id']; ?>">
						<td>
							<a href="<?php echo base_url(PROJECTPATH.'/assets/uploads/loan_atm_attach/'.$value['file_name']); ?>" target="_blank">
								<?php echo $value['file_old_name']; ?>
							</a>
						</td>
						<td style="color:red;font-size: 20px;cursor:pointer;" align="center" width="10%">
							<span class="icon icon-ban" onclick="del_file('<?php echo $value['id']; ?>')"></span>
						</td>
					</tr>
				<?php } ?>
			</table>
		</div>
	  </div>
	</div>
</div>
<div class="modal fade" id="loan_cancel_contract_modal" role="dialog" style="overflow-x: hidden;overflow-y: auto;">
	<div class="modal-dialog modal-dialog-file">
		<div class="modal-content data_modal">
			<div class="modal-header modal-header-confirmSave">
				<button type="button" class="close" data-dismiss="modal">x</button>
				<h2 class="modal-title" id="type_name">ยกเลิกสัญญา</h2>
			</div>
			<form action="<?php echo base_url(PROJECTPATH.'/loan_atm/loan_cancel_contract'); ?>" method="POST" id="form_cancel_contract" enctype="multipart/form-data">
				<input type="hidden" name="loan_atm_id" value="<?php echo @$row_loan_atm['loan_atm_id']; ?>">
				<input type="hidden" name="member_id" value="<?php echo @$row_loan_atm['member_id']; ?>">
				<div class="modal-body">
					<div class="g24-col-sm-24 modal_data_input">
						<div class="g24-col-sm-24 modal_data_input" style="margin-bottom: 20px;">
							<label class="g24-col-sm-11 control-label ">ยอดเงินต้นที่ต้องชำระ</label>
							<div class="g24-col-sm-5">
								<input class="form-control" type="text" id="principal_amount" name="principal_amount" value="<?php echo number_format($principal_amount); ?>" readonly>
							</div>
						</div>
						<div class="g24-col-sm-24 modal_data_input" style="margin-bottom: 20px;">
							<label class="g24-col-sm-24 control-label" style="text-align:center;">กรุณาชำระยอดเงินทั้งหมดก่อนเพื่อยกเลิกสัญญา</label>
						</div>
						<div class="center">
							<button class="btn btn-primary" type="button" onclick="cancel_contract('<?php echo $principal_amount; ?>')">ยกเลิกสัญญา</button>&nbsp;&nbsp;&nbsp;
							<button class="btn btn-default" type="button" data-dismiss="modal">ยกเลิก</button>
						</div>

					</div>
					&nbsp;
				</div>
			</form>
		</div>
	</div>
</div>
<div class="modal fade" id="loan_change_amount_modal" role="dialog" style="overflow-x: hidden;overflow-y: auto;">
	<div class="modal-dialog modal-dialog-data">
		<div class="modal-content data_modal">
			<div class="modal-header modal-header-confirmSave">
				<button type="button" class="close" data-dismiss="modal">x</button>
				<h2 class="modal-title" id="type_name">เปลี่ยนแปลงวงเงิน</h2>
			</div>
			<form action="<?php echo base_url(PROJECTPATH.'/loan_atm/loan_change_amount'); ?>" method="POST" id="form_change_amount" enctype="multipart/form-data">
				<input type="hidden" name="loan_atm_id" value="<?php echo @$row_loan_atm['loan_atm_id']; ?>">
                <input type="hidden" name="principal_amount" value="<?php echo number_format($principal_amount,2,".","");?>">
                <input type="hidden" name="interest_amount" value="<?php echo number_format($interest_amount,2,".","");?>">
                <input type="hidden" name="pay_amount" value="<?php echo $deduct_amount;?>">
				<div class="modal-body">
					<div class="g24-col-sm-24 modal_data_input">
						<div class="g24-col-sm-24 modal_data_input">
							<label class="g24-col-sm-4 control-label ">คำร้องเลขที่</label>
							<div class="g24-col-sm-5">
								<input class="form-control" type="text" id="petition_number_c" name="petition_number" value="" readonly>
							</div>
							<label class="g24-col-sm-3 control-label ">แนบไฟล์คำขอกู้</label>
							<div class="g24-col-sm-5">
								<label class="fileContainer btn btn-info">
									<span class="icon icon-paperclip"></span> 
									เลือกไฟล์
									<input type="file" class="form-control" name="file_attach[]" value="" multiple>
								</label>
								
							</div>
						</div>
						<div class="g24-col-sm-24 modal_data_input">
							<label class="g24-col-sm-4 control-label">รหัสสมาชิก</label>
							<div class="g24-col-sm-5">
								<input class="form-control" id="member_id_c" type="text" name="member_id" value="<?php echo @$row_member['member_id']; ?>" readonly>
							</div>
							<label class="g24-col-sm-3 control-label ">ชื่อ-สกุล</label>
							<div class="g24-col-sm-7">
								<input class="form-control" type="text" value="<?php echo @$row_member['firstname_th'].' '.@$row_member['lastname_th'] ?>" readonly>
							</div>
						</div>
						<div class="g24-col-sm-24 modal_data_input" >
							<label class="g24-col-sm-4 control-label">วงเงินที่ขอกู้</label>
							<div class="g24-col-sm-5">
								<input class="form-control" type="text" id="total_amount_c" name="total_amount" onkeyup="format_the_number(this)" value="<?php echo number_format(@$row_loan_atm['total_amount']!=''?$row_loan_atm['total_amount']:$loan_atm_setting['max_loan_amount']); ?>">
								<input type="hidden" id="max_loan_amount_c" value="<?php echo number_format($loan_atm_setting['max_loan_amount']); ?>">
							</div>
							<label class="g24-col-sm-3 control-label ">เหตุผลการกู้</label>
							<div class="g24-col-sm-9">
								<select name="loan_reason" class="form-control" id="loan_reason_c">
									<option value="">ไม่ระบุ</option>
									<?php 
									foreach($rs_loan_reason as $key => $row_loan_reason){
									?>
									<option value="<?php echo $row_loan_reason['loan_reason_id']; ?>" <?php echo @$row_loan_atm['loan_reason']==$row_loan_reason['loan_reason_id']?'selected':''; ?>><?php echo $row_loan_reason['loan_reason']; ?></option>
									<?php } ?>
								</select>
							</div>
						</div>
						<div class="g24-col-sm-24 modal_data_input" style="margin-bottom: 20px;">
							<label class="g24-col-sm-4 control-label">หักกลบเงินต้น</label>
							<div class="g24-col-sm-5">
								<input class="form-control" type="text" value="<?php echo number_format($principal_amount,2); ?>" readonly>
							</div>
							<label class="g24-col-sm-3 control-label">หักกลบดอกเบี้ย</label>
							<div class="g24-col-sm-5">
								<input class="form-control" type="text" value="<?php echo number_format($interest_amount,2); ?>" readonly>
							</div>
						</div>
						<div class="g24-col-sm-24 modal_data_input" style="margin-bottom: 20px;">
							<label class="g24-col-sm-4 control-label">ยอดหักกลบ</label>
							<div class="g24-col-sm-5">
								<input class="form-control" type="text" id="deduct_amount" name="deduct_amount" value="<?php echo number_format($deduct_amount); ?>" readonly>
							</div>
						</div>
						<div class="center">
							<button class="btn btn-primary" id="submit_button" type="button" onclick="check_submit_change_amount()">บันทึกคำร้อง</button>&nbsp;&nbsp;&nbsp;
							<button class="btn btn-default" type="button" data-dismiss="modal">ยกเลิก</button>
						</div>
						
					</div>
					&nbsp;
				</div>
			</form>
		</div>
	</div>
</div>
<?php $this->load->view('search_member_new_modal'); ?>
<?php
$v = date('YmdHis');
$link = array(
    'src' => PROJECTJSPATH.'assets/js/loan_atm.js?v='.$v,
    'type' => 'text/javascript'
);
echo script_tag($link);
?>