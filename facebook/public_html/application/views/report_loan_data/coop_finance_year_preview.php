<style>
	.table-view>thead, .table-view>thead>tr>td, .table-view>thead>tr>th {
		font-size: 15px;
	}
	.table {
		color: #000;
	}		
	@page { size: landscape; }
</style>		
		<?php
			$year = @$_GET['year'];							
		?>
		
		<div style="width: 1500px;">
			<div class="panel panel-body" style="padding-top:10px !important;height: 950px;">
				<table style="width: 100%;">
					<tr>
						<td style="width:100px;vertical-align: top;">
							<img src="<?php echo base_url(PROJECTPATH.'/assets/images/coop_profile/'.$_SESSION['COOP_IMG']); ?>" alt="Logo" style="height: 80px;" />
						</td>
						<td class="text-center">
							 <h3 class="title_view"><?php echo @$_SESSION['COOP_NAME'];?></h3>
							 <h3 class="title_view">รายละเอียดยอดลูกหนี้คงเหลือ</h3>
							 <h3 class="title_view">
								<?php echo " สำหรับสิ้นสุดวันที่ 31 ธันวาคม ".@$year;?>
							</h3>
							 <p>&nbsp;</p>	
						 </td>
						 <td style="width:100px;vertical-align: top;" class="text-right">
							<?php //if(@$i == '1'){?>
								<a class="no_print" onclick="window.print();"><button class="btn btn-perview btn-after-input" type="button"><span class="icon icon-print" aria-hidden="true"></span></button></a>
								<a href="<?php echo base_url(PROJECTPATH.'/report_loan_data/coop_finance_year_report?year='.@$year); ?>" class="no_print"><button class="btn btn-perview btn-after-input" type="button"><span>XLS</span></button></a>	
							<?php //} ?>
						 </td>
					</tr> 
					<tr>
						<td colspan="3">
							<h3 class="title_view">
							</h3>
						</td>
					</tr> 
				</table>
				<table class="table table-view table-center">
					<thead> 
						<tr>
							<th rowspan="3" style="width: 40px;vertical-align: middle;">ที่</th>
							<th rowspan="3" style="width: 80px;vertical-align: middle;">เลขทะเบียนสมาชิก</th>
							<th rowspan="3" style="width: 200px;vertical-align: middle;">ชื่อ - สกุล</th> 
							<th rowspan="3" style="width: 200px;vertical-align: middle;"></th> 
							<th rowspan="3" style="width: 85px;vertical-align: middle;">จำนวน<br>หุ้น</th> 
							<th rowspan="3" style="width: 85px;vertical-align: middle;">ทุนเรือนหุ้น<br>จำนวนเงิน<br>(บาท)</th> 
							<th rowspan="3" style="width: 85px;vertical-align: middle;">เงินฝาก<br>ออมทรัพย์<br>พิเศษ</th> 
							<th rowspan="3" style="width: 85px;vertical-align: middle;">เงินกู้ฉุกเฉิน<br>คงเหลือ</th> 
							<th rowspan="3" style="width: 85px;vertical-align: middle;">เงินกู้ฉุกเฉิน<br>กรณีพิเศษ<br>คงเหลือ</th> 
							<th rowspan="3" style="width: 85px;vertical-align: middle;">เลขที่<br>สัญญา<br>เงินกู้สามัญ</th> 
							<th colspan="4" style="width: 85px;vertical-align: middle;">เงินกู้สามัญ</th> 
							<th colspan="3" style="width: 85px;vertical-align: middle;">เงินกู้พิเศษ</th> 
							<th colspan="4" style="width: 85px;vertical-align: middle;">ดอกเบี้ยเงินให้กู้ค้างรับ</th> 
							<th rowspan="3" style="width: 85px;vertical-align: middle;">ลงชื่อ<br>ยืนยันยอด</th> 
						</tr> 
						<tr>
							<th colspan="2" style="width: 85px;vertical-align: middle;">ลูกหนี้</th>
							<th rowspan="2" style="width: 85px;vertical-align: middle;">ลูกหนี้<br>ระยะยาว</th>
							<th rowspan="2" style="width: 85px;vertical-align: middle;">เงินกู้สามัญ<br>คงเหลือ</th> 
							<th rowspan="2" style="width: 85px;vertical-align: middle;">ลูกหนี้<br>ระยะสั้น</th> 
							<th rowspan="2" style="width: 85px;vertical-align: middle;">ลูกหนี้<br>ระยะยาว</th> 
							<th rowspan="2" style="width: 85px;vertical-align: middle;">เงินกู้พิเศษ<br>คงเหลือ</th> 
							<th rowspan="2" style="width: 85px;vertical-align: middle;">ฉุกเฉิน</th> 
							<th rowspan="2" style="width: 85px;vertical-align: middle;">ฉุกเฉิน<br>พิเศษ</th> 
							<th rowspan="2" style="width: 85px;vertical-align: middle;">สามัญ</th> 
							<th rowspan="2" style="width: 85px;vertical-align: middle;">พิเศษ</th> 
						</tr> 
						<tr>
							<th style="width: 85px;vertical-align: middle;">ชำระคืน<br>งวดละ</th>
							<th style="width: 85px;vertical-align: middle;">ระยะสั้น</th>
						</tr> 
					</thead>
					<tbody>
					  <?php 
						$this->db->select(array('t1.*','t2.prename_short'));
						$this->db->from('coop_mem_apply as t1');
						$this->db->join('coop_prename as t2','t1.prename_id = t2.prename_id','left');
						$rs = $this->db->get()->result_array();
							
						$k=1;
						if(!empty($rs)){
							foreach(@$rs as $key => $row){	
								//if(@$row['level']!=''){
									$mem_group = @$row['level'];
								//}else if(@$row['faction']!=''){
									//$mem_group = @$row['faction'];
								//}else{
									//$mem_group = @$row['department'];
								//}
								$this->db->select(array('share_collect','share_collect_value'));
								$this->db->from('coop_mem_share');			
								$this->db->where("member_id = '".@$row['member_id']."'");
								$this->db->order_by('share_id DESC');
								$this->db->limit(1);		
								$rs_share = $this->db->get()->result_array();
								$row_share = @$rs_share[0];
								
								$this->db->select(array('t1.transaction_balance'));
								$this->db->from('coop_account_transaction as t1');
								$this->db->join('coop_maco_account as t2','t1.account_id = t2.account_id','inner');
								$this->db->where("t2.mem_id = '".@$row['member_id']."'");
								$this->db->order_by('t1.transaction_id DESC');
								$this->db->limit(1);
								$rs_deposit = $this->db->get()->result_array();
								$row_deposit = @$rs_deposit[0];
								
								$this->db->select(array('*','coop_loan.id'));
								$this->db->from('coop_loan');
								$this->db->join('coop_loan_transfer','coop_loan.id = coop_loan_transfer.loan_id','inner');
								$this->db->where("member_id = '".@$row['member_id']."' AND loan_status = '1'");
								$rs_loan = $this->db->get()->result_array();
								
								$loan_arr = array();
								if(!empty($rs_loan)){
									foreach(@$rs_loan as $key => $row_loan){	
										@$loan_arr[@$row_loan['loan_type']]['contract_number'] = @$row_loan['contract_number'];
										@$loan_arr[@$row_loan['loan_type']]['loan_amount_balance'] += @$row_loan['loan_amount_balance'];
										@$loan_amount_balance = @$row_loan['loan_amount_balance'];
										
										$this->db->select(array('principal_payment'));
										$this->db->from('coop_loan_period');			
										$this->db->where("loan_id = '".@$row_loan['id']."' AND date_period LIKE '".(@$year-543)."%'");		
										$rs_period = $this->db->get()->result_array();
										$row_period = @$rs_period[0];
										
										$loan_arr[@$row_loan['loan_type']]['money_per_period'] = @$row_period['principal_payment'];
										$principal_payment_in_year = 0;
										for($a=1;$a<=12;$a++){
											if(@$row_period['principal_payment'] < @$loan_amount_balance){
												$principal_payment_in_year += @$row_period['principal_payment'];
											}else{
												$principal_payment_in_year += $loan_amount_balance;
											}
											$loan_amount_balance -= @$row_period['principal_payment'];
											if($loan_amount_balance <= 0){
												break;
											}
										}
										@$loan_arr[@$row_loan['loan_type']]['principal_payment_in_year'] += @$principal_payment_in_year;
									}
								}
								
								
								$this->db->select(array('t1.loan_amount_balance',
														't1.loan_type',
														't2.date_transfer',
														't1.id',
														't1.interest_per_year'));
								$this->db->from('coop_loan as t1');
								$this->db->join('coop_loan_transfer as t2','t1.id = t2.loan_id','inner');
								$this->db->where("t1.member_id = '".@$row['member_id']."' AND t1.loan_status = '1' AND t1.loan_amount_balance > 0 ");
								$rs_loan_residue = $this->db->get()->result_array();
								
								$loan_residue = array();
								if(!empty($rs_loan_residue)){
									foreach(@$rs_loan_residue as $key => $row_loan_residue){
										//if($row_loan_residue['loan_type'] == '3' || $row_loan_residue['loan_type'] == '4' && date('d',strtotime($row_loan_residue['date_transfer'])) >= '16'){
											//continue;
										//}
										$date_interesting = ($year-543)."-12-31";
										
										$this->db->select(array('payment_date'));
										$this->db->from('coop_finance_transaction');			
										$this->db->where("loan_id = '".@$row_loan_residue['id']."'");	
										$this->db->order_by("payment_date DESC");	
										$rs_date_prev_paid = $this->db->get()->result_array();
										$row_date_prev_paid = @$rs_date_prev_paid[0];
										
										$date_prev_paid = @$row_date_prev_paid['payment_date']!=''?@$row_date_prev_paid['payment_date']:@$row_loan_residue['date_transfer'];
										$diff = date_diff(date_create($date_prev_paid),date_create($date_interesting));
										$date_count = $diff->format("%a");
										$date_count = $date_count+1;
										
										$interest = (((@$row_loan_residue['loan_amount_balance']*@$row_loan_residue['interest_per_year'])/100)/365)*@$date_count;
										@$loan_residue[@$row_loan_residue['loan_type']] += @$interest;
									}
								}
							
						?>
						  <tr> 
							  <td style="text-align: center;"><?php echo @$k++;?></td>
							  <td style="text-align: center;"><?php echo @$row['member_id']; ?></td>						 
							  <td style="text-align: left;"><?php echo @$row['prename_short'].@$row['firstname_th'].'  '.@$row['lastname_th']; ?></td>						 
							  <td style="text-align: left;"><?php echo @$mem_group_arr[@$mem_group]; ?></td> 							 
							  <td style="text-align: right;"><?php echo number_format(@$row_share['share_collect'],2); ?></td> 						 
							  <td style="text-align: right;"><?php echo number_format(@$row_share['share_collect']*@$share_value,2);?></td> 					 
							  <td style="text-align: right;"><?php echo number_format(@$row_deposit['transaction_balance'],2);?></td> 					 
							  <td style="text-align: right;"><?php echo number_format(@$loan_arr['3']['loan_amount_balance'],2);?></td> 					 
							  <td style="text-align: right;"><?php echo number_format(@$loan_arr['4']['loan_amount_balance'],2);?></td> 
							  <?php
								if(@$loan_arr['1']['contract_number']!=''){
							   ?>
									<td style="text-align: right;"><?php echo @$loan_arr['1']['contract_number'];?></td> 					 
									<td style="text-align: right;"><?php echo number_format(@$loan_arr['1']['money_per_period'],2);?></td> 
								<?php
								}else{
								?>	
									<td style="text-align: right;"><?php echo @$loan_arr['2']['contract_number'];?></td> 					 
									<td style="text-align: right;"><?php echo number_format(@$loan_arr['2']['money_per_period'],2);?></td> 
							  <?php	
								}	
							  ?>		
							  <td style="text-align: right;"><?php echo number_format(@$loan_arr['1']['principal_payment_in_year'],2);?></td> 					 
							  <td style="text-align: right;"><?php echo number_format(@$loan_arr['1']['loan_amount_balance']-@$loan_arr['1']['principal_payment_in_year'],2);?></td> 					 
							  <td style="text-align: right;"><?php echo number_format(@$loan_arr['1']['loan_amount_balance'],2);?></td> 					 
							  <td style="text-align: right;"><?php echo number_format(@$loan_arr['2']['principal_payment_in_year'],2);?></td> 					 
							  <td style="text-align: right;"><?php echo number_format(@$loan_arr['2']['loan_amount_balance']-@$loan_arr['2']['principal_payment_in_year'],2);?></td> 					 
							  <td style="text-align: right;"><?php echo number_format(@$loan_arr['2']['loan_amount_balance'],2);?></td> 					 
							  <td style="text-align: right;"><?php echo number_format(@$loan_residue['3'],2);?></td> 					 
							  <td style="text-align: right;"><?php echo number_format(@$loan_residue['4'],2);?></td> 					 
							  <td style="text-align: right;"><?php echo number_format(@$loan_residue['1'],2);?></td> 					 
							  <td style="text-align: right;"><?php echo number_format(@$loan_residue['2'],2);?></td> 				 
							  <td style="text-align: left;"><?php echo ''; ?></td> 							 
						  </tr>
					<?php 
							}
						} 
					?>							
					</tbody>  
				</table>
			</div>
		</div>