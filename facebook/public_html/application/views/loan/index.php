<div class="layout-content">
    <div class="layout-content-body">
		<style>
			.center {
				text-align: center;
			}
			.left {
				text-align: left;
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
					<h1 class="title_top">การกู้เงิน</h1>
					<?php $this->load->view('breadcrumb'); ?>
				</div>
				<div class="col-sm-6">
				<br>
					<div class="g24-col-sm-24">
					<?php if($member_id!=''){ ?>
						<div class="g24-col-sm-10">
							<select id="loan_type_choose" class="form-control" onchange="change_type()">
								<option value="">เลือกประเภทการกู้เงิน</option>
								<?php foreach($rs_loan_type as $key => $value){ ?>
									<option value="<?php echo $value['id']; ?>" ><?php echo $value['loan_type']; ?></option>
								<?php } ?>
							</select>
						</div>
						<div class="g24-col-sm-9">
							<select id="loan_type_select" class="form-control">
								<option value="">เลือกชื่อเงินกู้</option>
							</select>
						</div>
						<div class="g24-col-sm-1">
							<a class="link-line-none" id="normal_loan_btn" onclick="change_modal()">
								<button class="btn btn-primary" style="margin-right:5px;">เพิ่มคำร้อง</button>
							</a>
						</div>
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
									<input id="form-control-2"  class="form-control " type="text" value="<?php echo number_format(@$row_member['other_income']); ?>"  readonly>
								</div>
							</div>
							<div class="form-group g24-col-sm-8">
								<label class="g24-col-sm-10 control-label" for="form-control-2">อายุสมาชิก</label>
								<div class="g24-col-sm-14" >
									<input id="form-control-2"  class="form-control " type="text" value="<?php echo (@$row_member['member_date'] == "")?"":$this->center_function->cal_age(@$row_member['member_date'])." ปี ".$this->center_function->cal_age(@$row_member['member_date'],'m')." เดือน";?>"  readonly>
								</div>
							</div>
						</div>
					<div class="" style="padding-top:0;">
						<h3 >หุ้น</h3>
						<div class="g24-col-sm-24">
							<div class="form-group g24-col-sm-8">
								<label class="g24-col-sm-10 control-label ">ทุนเรือนหุ้นสะสม</label>
								<div class="g24-col-sm-14">
									<input class="form-control" id="share_total" type="text" value="<?php echo number_format(@$cal_share,2); ?>" readonly>
								</div>
							</div>
							<div class="form-group g24-col-sm-8">
								<label class="g24-col-sm-10 control-label ">ส่งหุ้นงวดละ</label>
								<div class="g24-col-sm-14">
									<input class="form-control" type="text" value="<?php echo number_format(@$row_member['share_month']); ?>"  readonly>
								</div>
							</div>
							<div class="form-group g24-col-sm-8">
								<label class="g24-col-sm-10 control-label ">งวดที่</label>
								<div class="g24-col-sm-14">
									<input class="form-control" type="text" value="<?php echo number_format(@$share_period); ?>"  readonly>
								</div>
							</div>
						</div>
					</div>
					<div class="" style="padding-top:0;">
						<h3 >เงินฝาก</h3>
						<div class="g24-col-sm-24">
							<div class="form-group g24-col-sm-8">
								<label class="g24-col-sm-10 control-label ">จำนวนบัญชี</label>
								<div class="g24-col-sm-14">
									<input class="form-control" type="text" value="<?php echo number_format(@$count_account); ?>"  readonly>
								</div>
							</div>
							<div class="form-group g24-col-sm-8">
								<label class="g24-col-sm-10 control-label ">ยอดรวมทั้งสิ้น</label>
								<div class="g24-col-sm-14">
									<input class="form-control" type="text" value="<?php echo number_format(@$cal_account,2); ?>"  readonly>
								</div>
							</div>
							<div class="form-group g24-col-sm-8">
								<div class="g24-col-sm-24">
								<button class="btn btn-primary btn_show btn-after-input" id="button_1" onclick="change_table('1')"><span class="icon icon-search" ></span> แสดง</button>
								</div>
							</div>
						</div>
					</div>
					<div class="g24-col-sm-24 m-t-1 hidden_table" id="table_1" style="display:none;">
						<div class="bs-example" data-example-id="striped-table">
							<table class="table table-bordered table-striped table-center">
								<thead> 
									<tr class="bg-primary">
										<th>#</th>
										<th>เลขที่บัญชี</th>
										<th>ชื่อบัญชี</th>
										<th>ยอดเงิน</th>
									</tr> 
								</thead>
								<tbody>
								<?php
									$i=1;
									if(!empty($data_account)){
									foreach(@$data_account as $key => $row_account){
								?>
									<tr> 
										<td><?php echo $i++; ?></td>
										<td><?php echo $row_account['account_id']; ?></td>
										<td><?php echo $row_account['account_name']; ?></td> 
										<td><?php echo number_format($row_account['transaction_balance'],2); ?></td> 
									</tr>
								<?php }
									}else{ ?>
									<tr><td colspan="9">ไม่พบข้อมูล</td></tr>
									<?php } ?>							
								</tbody> 
							</table> 
						</div>
					</div>
					<div class="" style="padding-top:0;">
						<h3 >ภาระค้ำประกัน</h3>
						<div class="g24-col-sm-24">
							<div class="form-group g24-col-sm-8">
								<label class="g24-col-sm-10 control-label ">จำนวนสัญญา</label>
								<div class="g24-col-sm-14">
									<input class="form-control" type="text" value="<?php echo number_format(@$count_contract); ?>"  readonly>
								</div>
							</div>
							<div class="form-group g24-col-sm-8">
								<label class="g24-col-sm-10 control-label ">เงินต้นคงเหลือ</label>
								<div class="g24-col-sm-14">
									<input class="form-control" type="text" value="<?php echo number_format(@$sum_guarantee_balance,2); ?>"  readonly>
								</div>
							</div>
							<div class="form-group g24-col-sm-8">
								<div class="g24-col-sm-24">
								<button class="btn btn-primary btn_show btn-after-input" id="button_2" onclick="change_table('2')"><span class="icon icon-search"></span> แสดง</button>
								</div>
							</div>
						</div>
					</div>
					<div class="g24-col-sm-24 m-t-1 hidden_table" id="table_2" style="display:none;">
						<div class="bs-example" data-example-id="striped-table">
							<table class="table table-bordered table-striped table-center">
								<thead> 
									<tr class="bg-primary">
										<th>#</th>
										<th>เลขที่สัญญา</th>
										<th>รหัสสมาชิก</th>
										<th>ชื่อสมาชิก</th>
										<th>ยอดเงิน</th>
										<th>คงเหลือ</th>
									</tr>
								</thead>
								<tbody>
								<?php
									$i=1;
									if(!empty($rs_guarantee)){
									foreach(@$rs_guarantee as $key => $row_guarantee){
								?>
									<tr> 
										<td><?php echo $i++; ?></td>
										<td><?php echo $row_guarantee['contract_number']; ?></td>
										<td><?php echo $row_guarantee['member_id']; ?></td> 
										<td><?php echo $row_guarantee['firstname_th']." ".$row_guarantee['lastname_th']; ?></td> 
										<td><?php echo number_format($row_guarantee['loan_amount'],2); ?></td>
										<td><?php echo number_format($row_guarantee['loan_amount_balance'],2); ?></td>
									</tr>
									<?php }
									}else{ ?>
									<tr><td colspan="9">ไม่พบข้อมูล</td></tr>
									<?php } ?>
								</tbody> 
							</table> 
						</div>
					</div>
					
					<div class="" style="padding-top:0;">
						<h3 >ประวัติการผิดนัดชำระ</h3>
						<div class="g24-col-sm-24">
							<div class="form-group g24-col-sm-8">
								<label class="g24-col-sm-10 control-label ">จำนวนงวด</label>
								<div class="g24-col-sm-14">
									<input class="form-control" type="text" value="<?php echo number_format(@$count_debt); ?>"  readonly>
								</div>
							</div>
							<div class="form-group g24-col-sm-8">
								<label class="g24-col-sm-10 control-label ">ยอดรวม</label>
								<div class="g24-col-sm-14">
									<input class="form-control" type="text" value="<?php echo number_format(@$sum_debt_balance,2); ?>"  readonly>
								</div>
							</div>
							<div class="form-group g24-col-sm-8">
								<div class="g24-col-sm-24">
								<button class="btn btn-primary btn_show btn-after-input" id="button_4" onclick="change_table('4')"><span class="icon icon-search"></span> แสดง</button>
								</div>
							</div>
						</div>
					</div>
					<div class="g24-col-sm-24 m-t-1 hidden_table" id="table_4" style="display:none;">
						<div class="bs-example" data-example-id="striped-table">
							<table class="table table-bordered table-striped table-center">
								<thead> 
									<tr class="bg-primary">
										<th>ครั้งที่</th>
										<th>งวด</th>
										<th>สัญญาเลขที่</th>
										<th>ยอดเรียกเก็บ</th>
										<th>ค้างชำระ</th>
										<th>สถานะ</th>
									</tr>
								</thead>
								<tbody>
								<?php
									$arr_status = array(
													'1'=>'ยังไม่ได้ชำระ',
													'2'=>'ชำระแล้ว',
													'3'=>'ออกจดหมายเตือน',
													'6'=>'ให้ออกจากสมาชิก',
													);
		
									$i=1;
									//echo '<pre>'; print_r($rs_debt); echo '</pre>';
									if(!empty($rs_debt)){
									foreach(@$rs_debt as $key => $row_debt){
										if(@$row_debt['profile_id'] != '' ){
											$non_pay_year_month = @$row_debt['non_pay_year'].'/'.@$month_arr[$row_debt['non_pay_month']];
											
											$this->db->select(array('non_pay_amount_balance'));
											$this->db->from('coop_non_pay_detail');
											$this->db->where("member_id = '{$row_debt['member_id']}'
																AND	finance_month_profile_id = '{$row_debt['profile_id']}'
																AND deduct_code = 'LOAN' AND pay_type = 'principal'");
											$rs_principal = $this->db->get()->result_array();
											$amount_principal = @$rs_principal[0]['non_pay_amount_balance'];  
											
											$this->db->select(array('non_pay_amount_balance'));
											$this->db->from('coop_non_pay_detail');
											$this->db->where("member_id = '{$row_debt['member_id']}'
																AND	finance_month_profile_id = '{$row_debt['profile_id']}'
																AND deduct_code = 'LOAN' AND pay_type = 'interest'");
											$rs_interest = $this->db->get()->result_array();
											$amount_interest = @$rs_interest[0]['non_pay_amount_balance'];  
											
											@$non_pay_amount_balance = @$amount_principal+@$amount_interest;
											
											$non_pay_detail = '';
											if($amount_principal != 0 AND $amount_interest != 0){
												$non_pay_detail = '('.number_format(@$amount_principal,2).' + '.number_format(@$amount_interest,2).')';
											}
								?>
										<tr> 
											<td><?php echo @$i++; ?></td>
											<td><?php echo @$non_pay_year_month; ?></td>
											<td><?php echo @$row_debt['contract_number']; ?></td> 
											<td><?php echo number_format(@$row_debt['pay_amount'],2); ?></td> 
											<td><?php echo number_format(@$non_pay_amount_balance,2).@$non_pay_detail ; ?></td>
											<td>
												<?php 
													$text_time = (@$row_debt['non_pay_status'] == "3")?" ครั้งที่ ".@$letter_runno:"";
													echo @$arr_status[$row_debt['non_pay_status']].@$text_time;
												?>
											</td>
										</tr>
									<?php 
											}else{
									?>
												<tr><td colspan="9">ไม่พบข้อมูล</td></tr>
									<?php 
											}
										}
									}else{ ?>
									<tr><td colspan="9">ไม่พบข้อมูล</td></tr>
									<?php } ?>
								</tbody> 
							</table> 
						</div>
					</div>
					
					<div class="" style="padding-top:0;">
						<h3 >การกู้เงิน</h3>
						<div class="g24-col-sm-24">
							<div class="form-group g24-col-sm-8">
								<label class="g24-col-sm-10 control-label ">จำนวนสัญญา</label>
								<div class="g24-col-sm-14">
									<input class="form-control" type="text" value="<?php echo number_format(@$count_loan); ?>"  readonly>
								</div>
							</div>
							<div class="form-group g24-col-sm-8">
								<label class="g24-col-sm-10 control-label ">เงินต้นคงเหลือ</label>
								<div class="g24-col-sm-14">
									<input class="form-control" type="text" value="<?php echo number_format(@$sum_loan_balance,2); ?>"  readonly>
								</div>
							</div>
							<div class="form-group g24-col-sm-8">
								<div class="g24-col-sm-24">
								<button class="btn btn-primary btn_show btn-after-input" id="button_3" onclick="change_table('3')"><span class="icon icon-search"></span> แสดง</button>
								</div>
							</div>
						</div>
					</div>
					<div class="g24-col-sm-24 m-t-1 hidden_table" id="table_3" >
						<div class="bs-example" data-example-id="striped-table">
							<table class="table table-bordered table-striped table-center">
								<thead> 
									<tr class="bg-primary">
										<th>#</th>
										<th>วันที่ทำรายการ</th>
										<th>เลขที่คำร้อง/เลขที่สัญญา</th>
										<th width="30%">ประเภทการกู้</th>
										<th>ยอดเงิน</th>
										<th>เงินต้นคงเหลือ</th>
										<th>ผู้ทำรายการ</th>
										<th>สถานะ</th>
										<th>จัดการ</th>
									</tr>
								</thead>
								<tbody>
								<?php
									$i=1;
									$loan_status = array('0'=>'รออนุมัติ', '1'=>'อนุมัติ' , '2'=>'ยื่นขอยกเลิกรายการ', '3'=>'<span style="color:red;">ยกเลิก</span>', '4'=>'ชำระเงินครบถ้วน', '5'=>'ไม่อนุมัติ','6'=>'เบี้ยวหนี้','7'=>'โอนหนี้ไปผู้ค้ำประกัน','8'=>'ผู้กู้รับสภาพหนี้');
									if(!empty($rs_loan)){
									foreach(@$rs_loan as $key => $row_loan){
										$this->db->select(array('t1.*'));
										$this->db->from('coop_loan_guarantee_person as t1');
										$this->db->where("t1.loan_id = '".$row_loan['id']."' AND t1.guarantee_person_id != ''");
										$this->db->order_by("t1.id ASC");
										$row_guarantee = $this->db->get()->result_array();
								?>
									<tr> 
										<!--td><a title="แก้ไข" style="cursor:pointer;padding-left:2px;padding-right:2px" onclick="edit_loan('<?php echo $row_loan['id']?>','<?php echo $row_loan['loan_type']; ?>')"><?php echo @$i; ?></a></td-->
										<td><?php echo @$i; ?></td>
										<td><?php echo $this->center_function->ConvertToThaiDate(@$row_loan['createdatetime']); ?></td>
										<td>
										<?php 
											$petition_number = "<a href='".base_url(PROJECTPATH.'/loan/'.@$row_loan['petition_file'].'/'.$row_loan['id'])."' target='_blank'>".$row_loan['petition_number']."</a>";
											if(@$row_loan['contract_number']!=''){
												echo @$petition_number."/".@$row_loan['contract_number'];
											}else{
												echo @$petition_number;
											}
											if($_GET['dev'] == 'dev'){
                                                echo ' | '.$row_loan['id'];
                                            }

										?>
										</td> 
										<td><?php
											echo @$row_loan['loan_type_detail']." ".@$row_loan['loan_name_description'];
											echo !empty($row_loan["com_firstname"]) ? "(".$row_loan["com_prename"].$row_loan["com_firstname"]." ".$row_loan["com_lastname"].")": "";
										?></td> 
										<td><?php echo number_format(@$row_loan['loan_amount'],2); ?></td>
										<td><?php echo number_format(@$row_loan['loan_amount_balance'],2); ?></td> 
										<td><?php echo @$row_loan['user_name']; ?></td>
										<td><?php echo @$loan_status[@$row_loan['loan_status']]; ?></td> 
										<td>
											<?php if(in_array($row_loan['loan_status'],array('0','1','2','4','7','8'))){ ?>
											<div class="dropdown">
												<button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown" style="width:90%">จัดการ
												<span class="caret"></span></button>
												<ul class="dropdown-menu">
													<?php if($row_loan['loan_group_id'] == '3' && $row_loan['loan_status'] == '1'){ ?>
														<li><a title="จัดการงวดโอน" style="cursor: pointer;padding-left:2px;padding-right:2px" href="<?php echo base_url("/installment/index/".$row_loan['id']); ?>"><span class="icon icon-money"></span>  จัดการงวดโอน</a></li>
													<?php } ?>
													<?php if(@$row_loan['loan_status']=='2'){ ?>
														<li><a title="ยกเลิกการยกเลิกรายการ" style="cursor: pointer;padding-left:2px;padding-right:2px" onclick="del_loan('<?php echo $row_loan['id']?>','<?php echo $member_id; ?>','0')"><span class="icon icon-trash-o"></span> ยกเลิกการยกเลิกรายการ</a></li>
													<?php }else if(in_array($row_loan['loan_status'],array('0','1','4','7','8'))){ ?>
														<?php if($row_loan['loan_status']=='0' && $row_loan['guarantee_for_id']==''){ ?>
															<li><a title="แก้ไข" style="cursor:pointer;padding-left:2px;padding-right:2px" onclick="edit_loan('<?php echo $row_loan['id']?>','<?php echo $row_loan['loan_type']; ?>')"><span style="cursor: pointer;" class="icon icon-edit"></span> แก้ไข</a></li>
														<?php } ?>
														<?php if($row_loan['loan_status']=='0'){ ?>
															<li><a title="ยกเลิก" style="cursor: pointer;padding-left:2px;padding-right:2px" onclick="del_loan('<?php echo $row_loan['id']?>','<?php echo $member_id; ?>','3')"><span class="icon icon-trash-o"></span> ยกเลิก</a> </li>
														<?php } ?>
													<?php } ?>
														<li><a title="ตารางจ่ายเงิน" style="cursor: pointer;padding-left:2px;padding-right:2px" onclick="show_period_table('<?php echo $row_loan['id']?>')"><span class="icon icon-table"></span> ตารางจ่ายเงิน</a></li>
														<?php if($row_loan['transfer_file']!=''){ ?>
															<li><a title="หลักฐานการโอนเงิน" style="cursor: pointer;padding-left:2px;padding-right:2px" href="<?php echo PROJECTPATH."/assets/uploads/loan_transfer_attach/".@$row_loan['transfer_file'];?>" target="_blank"><span class="icon icon-picture-o"></span> หลักฐานการโอนเงิน</a></li>
														<?php } ?>
														<?php //if($row_loan['loan_status']=='0'){ ?>
															<li><a title="เอกสารพิจารณาเงินกู้" style="cursor: pointer;padding-left:2px;padding-right:2px" href="<?php echo PROJECTPATH."/report_loan_data/coop_report_loan_detail_preview?member_id=".$member_id."&loan_id=".$row_loan['id']; ?>" target="_blank"><span class="icon icon-list-alt"></span> เอกสารพิจารณาเงินกู้</a></li>
														<?php //} ?>
														<?php if($row_loan['deduct_status']=='1'){ ?>
															<li><a title="รายการหัก" style="cursor: pointer;padding-left:2px;padding-right:2px" href="<?php echo PROJECTPATH."/report_loan_data/coop_report_loan_deduct/".$row_loan['id']; ?>" target="_blank"><span class="icon icon-list-alt"></span> รายการหัก</a></li>
														<?php } ?>
														<?php if($row_loan['loan_status']=='1' || $row_loan['loan_status']=='8'){ ?>
															<?php if(!empty($row_guarantee) && $row_loan['transfer_id']!=''){ ?>
															<li><a title="เบี้ยวหนี้" style="cursor: pointer;padding-left:2px;padding-right:2px" onclick="send_debt_settlement('<?php echo $row_loan['id']?>');"><span class="icon icon-file"></span> เบี้ยวหนี้</a></li>
															<?php } ?>
															<li><a href="#" style="cursor: pointer;padding-left:2px;padding-right:2px" title="อัพเดทเงินเดือนในเอกสารข้อกู้เงิน" onclick="updateSalaryDetail('<?php echo $member_id;?>','<?php echo $row_loan['id']?>')"><span class="icon icon-list-alt"></span>  อัพเดทเงินเดือนในเอกสารพิจารณาเงินกู้</a></li>
														<?php } ?>

														<li><a title="รายละเอียดการชำระเงิน" style="cursor: pointer;padding-left:2px;padding-right:2px" href="<?php echo PROJECTPATH."/loan/loan_payment_detail/".$row_loan['id']; ?>" target="_blank"><span class="icon icon-list-alt"></span> รายละเอียดการชำระเงิน</a></li>
														<li><a title="แก้ไขรายการเคลื่อนไหวสินเชื่อ" style="cursor: pointer;padding-left:2px;padding-right:2px" href="<?php echo PROJECTPATH."/loan/loan_transaction_edit/".$member_id."/".$row_loan['id']; ?>" target="_blank"><span class="icon icon-list-alt"></span> แก้ไขรายการเคลื่อนไหวสินเชื่อ</a></li>
														<li><a title="อัพเดทข้อมูลตารางงวดชำระ" style="cursor: pointer;padding-left:2px;padding-right:2px" onclick="re_create('<?php echo $row_loan['id'];?>')"><span class="icon icon-table"></span> อัพเดทข้อมูลตารางงวดชำระ</a></li>
												</ul>
											</div>
											<?php } ?>
										</td>
									</tr>
									<?php $i++; }
									}else{ ?>
									<tr><td colspan="9">ไม่พบข้อมูล</td></tr>
									<?php } ?>
								</tbody> 
							</table> 
						</div>
					</div>
					<input type="hidden" id="show_status_1" value="">
					<input type="hidden" id="show_status_2" value="">
					<input type="hidden" id="show_status_3" value="1">
					<input type="hidden" id="show_status_4" value="">
					
					<!--div class="g24-col-sm-24">
					<?php if($member_id!=''){ ?>
						<div class="g24-col-sm-5">
							<select id="loan_type_choose" class="form-control" onchange="change_type()">
								<option value="">เลือกประเภทการกู้เงิน</option>
								<?php foreach($rs_loan_type as $key => $value){ ?>
									<option value="<?php echo $value['id']; ?>" ><?php echo $value['loan_type']; ?></option>
								<?php } ?>
							</select>
						</div>
						<div class="g24-col-sm-5">
							<select id="loan_type_select" class="form-control">
								<option value="">เลือกชื่อเงินกู้</option>
							</select>
						</div>
						<div class="g24-col-sm-12">
							<a class="link-line-none" id="normal_loan_btn" onclick="change_modal()">
								<button class="btn btn-primary" style="margin-right:5px;">เพิ่มคำร้อง</button>
							</a>
						</div>
					<?php } ?>
					</div-->
				</div>
			</div>
		</div>
		<?php 
			if(!empty($rs_rule)){
				foreach($rs_rule as $key => $row_rule){ 
					?>
					<input 
						type="hidden" 
						id = "loan_rule_<?php echo $row_rule['type_id']; ?>" 
						type_id = "<?php echo $row_rule['type_id']; ?>" 
						type_name = "<?php echo $row_rule['type_name']; ?>" 
						credit_limit = "<?php echo $row_rule['credit_limit']; ?>" 
						less_than_multiple_salary = "<?php echo $row_rule['less_than_multiple_salary']; ?>" 
						num_guarantee = "<?php echo $row_rule['num_guarantee']; ?>" 
						percent_share_guarantee = "<?php echo $row_rule['percent_share_guarantee']; ?>" 
						percent_fund_quarantee = "<?php echo $row_rule['percent_fund_quarantee']; ?>"
						interest_rate = "<?php echo $this->Interest_modal->get_interest($row_rule['type_id'], date("Y-m-d"), "0" ); ?>"
					>
			<?php 
				} 
			}
			?>
		<input type="hidden" id="share_value" value="<?php echo $share_value; ?>">
	</div>
</div>
<?php $this->load->view('search_member_new_modal'); ?>
<form action="<?php echo base_url(PROJECTPATH.'/loan/coop_loan_save?member='.@$member_id); ?>" method="POST" id="form_normal_loan" enctype="multipart/form-data">
	<div class="modal fade" id="normal_loan" role="dialog" style="overflow-x: hidden;overflow-y: auto;">
		<div class="modal-dialog modal-dialog-data">
		  <div class="modal-content data_modal">
				<div class="modal-header modal-header-confirmSave">
				  <button type="button" class="close" data-dismiss="modal">x</button>
				  <h2 class="modal-title" id="type_name">กู้เงินสามัญ</h2>
				</div>
				<div class="modal-body">
					<?php $this->load->view('loan/normal_loan_modal'); ?>
			</div>
		  </div>
		</div>
	</div>
	<div class="modal fade cal_period" id="cal_period_normal_loan" role="dialog">
		<div class="modal-dialog modal-dialog-cal">
		  <div class="modal-content data_modal">
			<div class="modal-header modal-header-confirmSave">
			  <button type="button" class="close" onclick="close_modal('cal_period_normal_loan')">&times;</button>
			  <h2 class="modal-title">คำนวณการส่งค่างวด</h2>
			</div>
			<div class="modal-body">
				<?php $this->load->view('loan/calculate_loan'); ?>
			</div>
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
			</div>
		  </div>
		</div>
	</div>
</form>
<!--
<div class="modal fade" id="search_member_loan_modal" role="dialog"> 
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
			<button type="button" class="close" data-dismiss="modal">&times;</button>
			<h4 class="modal-title">ข้อมูลสมาชิก</h4>
        </div>
        <div class="modal-body">
       		<div class="input-with-icon">
					  <input class="form-control input-thick pill m-b-2" type="text" placeholder="กรอกเลขทะเบียนหรือชื่อ-สกุล" name="search_text" id="search_member_loan">
					  <span class="icon icon-search input-icon"></span>
					</div>

			<div class="bs-example" data-example-id="striped-table">
				 <table class="table table-striped">
					<tbody id="result_member_search">
					</tbody>
				</table>
			</div>
        </div>
        <div class="modal-footer">
			<input type="hidden" id="input_id">
			<button type="button" id="close" class="btn btn-default" data-dismiss="modal">ปิดหน้าต่าง</button>
        </div>
      </div>
    </div>
</div>
-->
<div class="modal fade" id="search_member_loan_modal" role="dialog"> 
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
			<button type="button" class="close" data-dismiss="modal">&times;</button>
			<h4 class="modal-title">ข้อมูลสมาชิก</h4>
        </div>
        <div class="modal-body">
       		<div class="input-with-icon">
				<div class="row">
					<div class="col">
						<label class="col-sm-2 control-label">รูปแบบค้นหา</label>
						<div class="col-sm-4">
							<div class="form-group">
								<select id="member_search_list" name="member_search_list" class="form-control m-b-1">
									<option value="">เลือกรูปแบบค้นหา</option>
									<option value="member_id">รหัสสมาชิก</option>
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
								<input id="member_search_text" name="member_search_text" class="form-control m-b-1" type="text" value="<?php echo @$data['id_card']; ?>">
								<span class="input-group-btn">
									<button type="button" id="member_loan_search" class="btn btn-info btn-search"><span class="icon icon-search"></span></button>
								</span>	
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="bs-example" data-example-id="striped-table">
				 <table class="table table-striped">
					<tbody id="result_member_search">
					</tbody>
				</table>
			</div>
        </div>
        <div class="modal-footer">
			<input type="hidden" id="input_id">
			<button type="button" id="close" class="btn btn-default" data-dismiss="modal">ปิดหน้าต่าง</button>
        </div>
      </div>
    </div>
</div>
<div class="modal fade" id="guarantee_person_data_modal" role="dialog"> 
    <div class="modal-dialog modal-dialog-file">
      <div class="modal-content data_modal">
        <div class="modal-header modal-header-confirmSave">
			<button type="button" class="close" data-dismiss="modal">&times;</button>
			<h3 class="modal-title">ข้อมูลผู้ค้ำประกัน</h3>
        </div>
        <div class="modal-body">
			<div class="bs-example" data-example-id="striped-table" id="guarantee_person_data">
			
			</div>
        </div>
        <div class="modal-footer">
			<button type="button" id="close" class="btn btn-default" data-dismiss="modal">ปิดหน้าต่าง</button>
        </div>
      </div>
    </div>
</div>
<div class="modal fade" id="period_table" role="dialog">
    <div class="modal-dialog modal-dialog-data">
      <div class="modal-content data_modal">
        <div class="modal-header modal-header-confirmSave">
          <button type="button" class="close" onclick="close_modal('period_table')">&times;</button>
          <h2 class="modal-title" id="type_name">ตารางคำนวณการชำระเงิน</h2>
        </div>
        <div class="modal-body period_table">
			
        </div>
      </div>
    </div>
</div>
<div class="modal fade" id="check_term_of_loan_result_modal" role="dialog">
    <div class="modal-dialog modal-dialog-file">
      <div class="modal-content data_modal">
        <div class="modal-header modal-header-confirmSave">
          <button type="button" class="close" onclick="close_modal('check_term_of_loan_result_modal')">&times;</button>
          <h2 class="modal-title" id="type_name">ตรวจสอบเงื่อนไขการกู้เงิน</h2>
        </div>
        <div class="modal-body">
		<div id="check_term_of_loan_result"></div>
		<div class="center"> 
			<button type="button" class="btn btn-primary" style="width:100px" onclick="submit_form()">ทำรายการต่อ</button> 
			<button type="button" style="width:100px" class="btn btn-danger" onclick="close_modal('check_term_of_loan_result_modal')">ยกเลิก</button>
		</div>
        </div>
      </div>
    </div>
</div>
<div class="modal fade" id="check_term_of_loan_before_result_modal" role="dialog">
    <div class="modal-dialog modal-dialog-file">
      <div class="modal-content data_modal">
        <div class="modal-header modal-header-confirmSave">
          <button type="button" class="close" onclick="close_modal('check_term_of_loan_before_result_modal')">&times;</button>
          <h2 class="modal-title" id="type_name">ตรวจสอบเงื่อนไขการกู้เงิน</h2>
        </div>
        <div class="modal-body">
		<div id="check_term_of_loan_before_result"></div>
		<div class="center"> 
			<button type="button" class="btn btn-primary" style="width:100px" onclick="open_modal('normal_loan')">ทำรายการต่อ</button> 
			<button type="button" style="width:100px" class="btn btn-danger" onclick="close_modal('check_term_of_loan_before_result_modal')">ยกเลิก</button>
		</div>
        </div>
      </div>
    </div>
</div>
<div class="modal fade" id="update_salary_modal" role="dialog"> 
    <div class="modal-dialog modal-dialog-file">
      <div class="modal-content data_modal">
        <div class="modal-header modal-header-confirmSave">
			<button type="button" class="close" data-dismiss="modal">&times;</button>
			<h3 class="modal-title">อัพเดทรายได้สมาชิก</h3>
        </div>
        <div class="modal-body">
			<div class="row m-b-1">
				<div class="col-sm-12">
					<div class="form-group">
						<label class="control-label col-sm-4">เงินเดือนค่าจ้าง</label>
						<div class="col-sm-4">
							<input type="text" id="update_salary" class="form-control" value="<?php echo number_format($row_member['salary']); ?>" onblur="format_the_number_decimal(this)">
						</div>
					</div>
				</div>
			</div>
            <!--<div class="row m-b-1">
                <div class="col-sm-12">
                    <div class="form-group">
                        <label class="control-label col-sm-4">งพช, งพศ</label>
                        <div class="col-sm-4">
                            <input type="text" id="money_special" class="form-control" value="<?php /*echo number_format($row_member['salary']); */?>" onkeyup="format_the_number(this)">
                        </div>
                    </div>
                </div>
            </div>
            <div class="row m-b-1">
                <div class="col-sm-12">
                    <div class="form-group">
                        <label class="control-label col-sm-4">สคบ, ชคบ</label>
                        <div class="col-sm-4">
                            <input type="text" id="money_help" class="form-control" value="<?php /*echo number_format($row_member['salary']); */?>" onkeyup="format_the_number(this)">
                        </div>
                    </div>
                </div>
            </div>-->
			<div class="row m-b-1">
				<div class="col-sm-12">
					<div class="form-group">
						<label class="control-label col-sm-4">รายได้อื่นๆ</label>
						<div class="col-sm-4">
							<input type="text" id="update_other_income" class="form-control" value="<?php echo number_format($row_member['other_income']); ?>" onkeyup="format_the_number_decimal(this)">
						</div>
					</div>
				</div>
			</div>
			<div class="row m-b-1">
				<div class="col-sm-12">
					<div class="form-group" style="text-align:center">
						<input type="button" class="btn btn-primary" value="บันทึก" onclick="update_salary();">
					</div>
				</div>
			</div>
        </div>
      </div>
    </div>
</div>

<?php
$v = date('YmdHis');
$link = array(
    'src' => PROJECTJSPATH.'assets/js/loan.js?v='.$v,
    'type' => 'text/javascript'
);
echo script_tag($link);
$link = array(
    'src' => PROJECTJSPATH.'assets/js/validation.js?v='.$v,
    'type' => 'text/javascript'
);
echo script_tag($link);
?>
<script>
$( document ).ready(function() {
	<?php if(@$_GET['loan_id']!=''){?>
		edit_loan('<?php echo $_GET['loan_id']; ?>','<?php echo $_GET['loan_type']; ?>');
	<?php } ?> 
});
</script>
