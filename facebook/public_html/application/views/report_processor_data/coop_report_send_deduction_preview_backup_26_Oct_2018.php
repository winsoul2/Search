<style>
	.table-view>thead, .table-view>thead>tr>td, .table-view>thead>tr>th {
		font-size: 14px;
	}	
	.table {
		color: #000;
	}	
	@page { size: landscape; }
	.underline{
		text-decoration: underline;
	}
	@media print {
		.pagination {
			display: none;
		}
	}
</style>
	<?php foreach($row_data as $key_mem_group => $value){ if(empty($value['data'])){continue;} ?>
		<?php foreach($value['data'] as $page => $value_data){ ?>
			<div style="width: 1500px;"  class="page-break">
				<div class="panel panel-body" style="padding-top:10px !important;min-height: 950px;">
					<table style="width: 100%;">
					<?php 
						// if(@$page == 1){
					?>	
						<tr>
							<td style="width:100px;vertical-align: top;">
								
							</td>
							<td class="text-center">
								<img src="<?php echo base_url(PROJECTPATH.'/assets/images/coop_profile/'.$_SESSION['COOP_IMG']); ?>" alt="Logo" style="height: 80px;" />	
								 <h3 class="title_view"><?php echo @$_SESSION['COOP_NAME'];?></h3>
								 <h3 class="title_view">รายงานการส่ง-หักเงินเดือน(รายละเอียดรายบุคคล)</h3>
								 <h3 class="title_view">
									<?php echo " ประจำ ".@$title_date;?>
								</h3>
							 </td>
							 <td style="width:100px;vertical-align: top;" class="text-right">
								<a class="no_print" onclick="window.print();"><button class="btn btn-perview btn-after-input" type="button"><span class="icon icon-print" aria-hidden="true"></span></button></a>
							</td>
						</tr>  
					<?php
						// }
					?>
						<tr>
							<td colspan="3" style="text-align: right;">
								<span class="title_view">หน้าที่ <?php echo $page_get.'/'.$all_page;?></span><br>						
							</td>
						</tr> 
					</table>
				
					<table class="table table-view table-center">
						<thead> 
							<tr>
								<th rowspan="2" style="width: 40px;vertical-align: middle;">ลำดับ</th>
								<th rowspan="2" style="width: 40px;vertical-align: middle;">เลขที่ สมาชิก</th>
								<th rowspan="2" style="width: 300px;vertical-align: middle;">ชื่อ-นามสกุล</th>
								<th rowspan="2" style="width: 80px;vertical-align: middle;">หุ้น</th>
								<?php 
								foreach($loan_type AS $key=>$row_loan_type){
								?>
								<th colspan="4" style="width: 80px;vertical-align: middle;"><?php echo str_replace('เงินกู้','',$row_loan_type['loan_type']);?></th> 
								<?php }?>
								<th colspan="2" style="width: 80px;vertical-align: middle;">เงินฝาก</th> 
								<th rowspan="2" style="width: 80px;vertical-align: middle;">ฌสอ สป</th> 
								<th rowspan="2" style="width: 80px;vertical-align: middle;">ค่าธรรมเนียมแรกเข้า</th> 
								<th rowspan="2" style="width: 80px;vertical-align: middle;">ค่าธรรมเนียม ฉ ATM</th> 
								<th rowspan="2" style="width: 80px;vertical-align: middle;">ชำระหนี้ค้ำประกัน</th> 
								<th rowspan="2" style="width: 80px;vertical-align: middle;">รวม</th> 
							</tr>
							<tr>
								<?php 
								foreach($loan_type AS $key=>$row_loan_type){
								?>
								<th style="width: 80px;vertical-align: middle;">งวด</th>
								<th style="width: 80px;vertical-align: middle;">เลขที่สัญญา</th>
								<th style="width: 80px;vertical-align: middle;">เงินต้น</th> 
								<th style="width: 80px;vertical-align: middle;">ดอกเบี้ย</th> 
								<?php }?>
								<th style="width: 80px;vertical-align: middle;">เลขที่บัญชี</th> 
								<th style="width: 80px;vertical-align: middle;">จำนวนเงิน</th> 
							</tr> 
						</thead>
						<tbody>
						<tr><td colspan="23" style="text-align: left;"><?php echo $value['mem_group_name']; ?></td></tr>
							<?php 
							foreach($value['data'][$page] as $key => $value2){ ?>
								<tr> 
									<td style="text-align: center;"><?php echo @$value2['runno']; ?></td>
									<td style="text-align: center;"><?php echo @$key; ?></td>
									<td style="text-align: left;"><?php echo @$value2['full_name']; ?></td>						 
									<td style="text-align: right;"><?php echo @$value2['SHARE']!=''?number_format(@$value2['SHARE']):'';?></td>	

									<?php
									foreach($loan_type AS $key_loan_type => $row_loan_type){
									?>
									<td style="text-align: center;"><?php echo @$value2['LOAN'][$row_loan_type['id']]['period']!=''?number_format(@$value2['LOAN'][$row_loan_type['id']]['period']):'';?></td> 					 
									<td style="text-align: center;"><?php echo @$value2['LOAN'][$row_loan_type['id']]['contract_number'];?></td> 					 
									<td style="text-align: right;"><?php echo @$value2['LOAN'][$row_loan_type['id']]['principal']!=''?number_format(@$value2['LOAN'][$row_loan_type['id']]['principal'],2):''; ?></td> 					 
									<td style="text-align: right;"><?php echo @$value2['LOAN'][$row_loan_type['id']]['interest']!=''?number_format(@$value2['LOAN'][$row_loan_type['id']]['interest'],2):'';?></td> 
									<?php 
									}	
									?>
									<td style="text-align: center;"><?php echo @$value2['DEPOSIT']['account_id'];?></td> 					 
									<td style="text-align: right;"><?php echo @$value2['DEPOSIT']['pay_amount']!=''?number_format(@$value2['DEPOSIT']['pay_amount'],2):'';?></td> 					 
									<td style="text-align: right;"><?php echo @$value2['CREMATION']!=''?number_format(@$value2['CREMATION'],2):'';?></td> 					 
									<td style="text-align: right;"><?php echo @$value2['REGISTER_FEE']!=''?number_format(@$value2['REGISTER_FEE'],2):'';?></td> 					 
									<td style="text-align: right;"><?php echo @$value2['FEE_ATM']!=''?number_format(@$value2['FEE_ATM'],2):'';?></td> 					 
									<td style="text-align: right;"><?php echo @$value2['GUARANTEE_AMOUNT']!=''?number_format(@$value2['GUARANTEE_AMOUNT'],2):'';?></td> 				 
									<td style="text-align: right;"><?php echo @$value2['sum_all']!=''?number_format(@$value2['sum_all'],2):'';?></td> 							 
								</tr>										
							<?php } ?>
							<?php if($page == $page_all_arr[$key_mem_group]){ ?>
								<tr> 
									<td style="text-align: center;" class="underline" colspan="3"><?php echo "รวม ".number_format(@$total_data[$key_mem_group]['total']['count_member'])." คน";?></td>
									<td style="text-align: center;" class="underline"><?php echo (@$total_data[$key_mem_group]['total']['SHARE'] == '')?'':number_format(@$total_data[$key_mem_group]['total']['SHARE']);?></td>
									<?php foreach($loan_type AS $key_loan_type => $row_loan_type){ ?>
										<td style="text-align: center;" class="underline" colspan="2"><?php echo (@$total_data[$key_mem_group]['total']['LOAN'][$row_loan_type['id']]['count_contract_number'] == '')?'':number_format(@$total_data[$key_mem_group]['total']['LOAN'][$row_loan_type['id']]['count_contract_number'])." สัญญา";?></td>						 
										<td style="text-align: right;" class="underline"><?php echo (@$total_data[$key_mem_group]['total']['LOAN'][$row_loan_type['id']]['principal'] == '')?'':number_format(@$total_data[$key_mem_group]['total']['LOAN'][$row_loan_type['id']]['principal'],2);?></td> 							 
										<td style="text-align: right;" class="underline"><?php echo (@$total_data[$key_mem_group]['total']['LOAN'][$row_loan_type['id']]['interest'] == '')?'':number_format(@$total_data[$key_mem_group]['total']['LOAN'][$row_loan_type['id']]['interest'],2);?></td> 
									<?php } ?>
									<td style="text-align: right;" class="underline"></td> 					 
									<td style="text-align: right;" class="underline"><?php echo (@$total_data[$key_mem_group]['total']['DEPOSIT'] == '')?'-':number_format(@$total_data[$key_mem_group]['total']['DEPOSIT'],2);?></td> 					 
									<td style="text-align: right;" class="underline"><?php echo (@$total_data[$key_mem_group]['total']['CREMATION'] == '')?'':number_format(@$total_data['total']['CREMATION'],2);?></td> 					 
									<td style="text-align: right;" class="underline"><?php echo (@$total_data[$key_mem_group]['total']['FEE_MAINTENANCE'] == '')?'':number_format(@$total_data[$key_mem_group]['total']['FEE_MAINTENANCE'],2);?></td> 					 
									<td style="text-align: right;" class="underline"><?php echo (@$total_data[$key_mem_group]['total']['FEE_ATM'] == '')?'':number_format(@$total_data[$key_mem_group]['total']['FEE_ATM'],2);?></td> 					 
									<td style="text-align: right;" class="underline"><?php echo (@$total_data[$key_mem_group]['total']['GUARANTEE'] == '')?'':number_format(@$total_data[$key_mem_group]['total']['GUARANTEE'],2);?></td> 					 
									<td style="text-align: right;" class="underline"><?php echo (@$total_data[$key_mem_group]['total']['sum_all'] == '')?'':number_format(@$total_data[$key_mem_group]['total']['sum_all'],2);?></td> 						 
								</tr>
							<?php } ?>						  
						</tbody>    
					</table>
					<?php echo @$paging ?>
				</div>
			</div>
		<?php } ?>
	<?php } ?>