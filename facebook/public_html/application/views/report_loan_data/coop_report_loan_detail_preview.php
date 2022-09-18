<style>
	.table-view>thead, .table-view>thead>tr>td, .table-view>thead>tr>th {
		font-size: 15px;
	}	

	.box-radius{
		border-radius: 4px;border: 1px solid #333333;padding: 5px;
	}	
	
	.group-box{
		text-align: left;display: -webkit-inline-box;
	}
	
	.border-bottom-dotted{border-bottom: 1px dotted #75758a;color: #000000;}
	.border-bottom-dotted-red{border-bottom: 1px dotted red;}
	
	
	table {
		border-collapse: initial;
		border-spacing: 0;
	}

	.text-center {
		text-align: center !important;
	}

	.bordered {
		border: solid #333333 1px;
		-moz-border-radius: 6px;
		-webkit-border-radius: 6px;
		border-radius: 6px;    
		width: 100%;
		font-family: Tahoma;
		font-size: 14px;
		color: #000000;
	}   
		
	.bordered th {
		border-left: 1px solid #333333;
		border-top: 1px solid #333333;
		border-bottom: 1px solid #333333;
		padding: 0px 2px 0px 2px;
		text-align: left;  
		-webkit-box-shadow: 0 1px 0 rgba(255,255,255,.8) inset; 
		-moz-box-shadow:0 1px 0 rgba(255,255,255,.8) inset;  
		box-shadow: 0 1px 0 rgba(255,255,255,.8) inset;        
		border-top: none;
		text-shadow: 0 1px 0 rgba(255,255,255,.5); 
		text-align: center;
	} 
	
	.bordered td{
		border-left: 1px solid #333333;
		padding: 0px 2px 0px 2px;
		text-align: left;
		vertical-align: top;		
	}
	
	.bordered tfoot td{
		border-top: 1px solid #333333;
		padding: 0px 2px 0px 2px;
		text-align: left; 
		background-color: #ffffff;
		font-weight: bold;
	}	

	.bordered td:first-child, .bordered th:first-child {
		border-left: none;
	}

	.bordered th:first-child {
		-moz-border-radius: 6px 0 0 0;
		-webkit-border-radius: 6px 0 0 0;
		border-radius: 6px 0 0 0;
	}
	
	.bordered th:last-child {
		-moz-border-radius: 0 6px 0 0;
		-webkit-border-radius: 0 6px 0 0;
		border-radius: 0 6px 0 0;
	}

	.bordered th:only-child{
		-moz-border-radius: 6px 6px 0 0;
		-webkit-border-radius: 6px 6px 0 0;
		border-radius: 6px 6px 0 0;
	}

	.bordered tr:last-child td:first-child {
		-moz-border-radius: 0 0 0 6px;
		-webkit-border-radius: 0 0 0 6px;
		border-radius: 0 0 0 6px;
	} 

	.bordered tr:last-child td:last-child {
		-moz-border-radius: 0 0 6px 0;
		-webkit-border-radius: 0 0 6px 0;
		border-radius: 0 0 6px 0;
	}
	
	.border-left{
		border-left: 1px solid #333333 !important;
		border-radius: 0 !important;
	}
	
	.border-top{
		border-top: 1px solid #333333 !important;
	}
	
	.font-bold{	
		font-weight: bold;
	}
	
	.text-title{    
		font-size: 15px;
		font-weight: bold;
		margin-top: 5px;
	}
	
	.text-red{color:red;}
	
	@media print {	
		.text-red{color:red !important;}
	}
	
	div{font-family: Tahoma;font-size: 14px;}
	
	.no-border{
		font-family: Tahoma;
		font-size: 14px;
	}
	.no-border td{
		border-left: 0px;
		padding: 0px 2px 0px 2px;
		text-align: left;
		vertical-align: top;		
	}
	
	span{font-family: Tahoma;font-size: 14px;color: #000000;}
	
	.signature_part {
		width: 100%;
		font-family: Tahoma;
		font-size: 14px;
		color: #000000;
	}
	.no-border-b{
		font-family: Tahoma;
		font-size: 14px;
		color: #000000;
	}
</style>		
		<?php
			$year = @$_GET['year'];	
			$petition_number = (empty($row_loan['contract_number']))?@$row_loan['petition_number']:@$row_loan['petition_number'].'/'.@$row_loan['contract_number'];

			//echo '<pre>'; print_r($row_loan); echo '</pre>';		
		?>
		
		<div style="width: 1000px;" class="page-break">
			<div class="panel panel-body" style="padding-top:20px !important;height: 1400px;">
				<table style="width: 100%;">
					<tr>						
						<td style="vertical-align: top;" class="text-right">
							<a class="no_print" onclick="window.print();"><button class="btn btn-perview btn-after-input" type="button"><span class="icon icon-print" aria-hidden="true"></span></button></a>
						</td>
					</tr> 
				</table>
				<table style="width: 100%;">
					<tr>
						<td style="width:100px;vertical-align: top;">
							<img src="<?php echo base_url(PROJECTPATH.'/assets/images/coop_profile/'.$_SESSION['COOP_IMG']); ?>" alt="Logo" style="height: 80px;" />
						</td>
						<td>
							 <h3 class="title_view"><?php echo @$_SESSION['COOP_NAME'];?></h3>
							 <h3 class="title_view">รายละเอียดการขอกู้เงิน <?php echo @$row_loan['loan_type_detail'];?></h3>
							<p>&nbsp;</p>	
						 </td>
						 <td style="width:250px;vertical-align: top;" class="text-right">
							<div class="group-box">
								<span>เลขที่คำขอ</span>	
								<div class="border-bottom-dotted" style="width: 156px;text-align: center;">&nbsp;<?php echo @$petition_number;?></div>	
							</div>		
							<div class="group-box">
								<span>วันที่</span>	
								<div class="border-bottom-dotted" style="width: 181px;text-align: center;">&nbsp;<?php echo $this->center_function->ConvertToThaiDate(@$row_loan['createdatetime'],0,0);?></div>	
							</div>	
							<?php
								if(@$row_loan['approve_date']!=""){
									?>
									<div class="group-box">
											<span>วันที่อนุมัติ</span>	
											<div class="border-bottom-dotted" style="width: 181px;text-align: center;">&nbsp;<?php echo $this->center_function->ConvertToThaiDate(@$row_loan['approve_date'],0,0);?></div>	
										</div>	
									<?php
								}
							?>
							
						 </td>
					</tr> 
					<tr>
						<td colspan="3">
							<h3 class="title_view">
							</h3>
						</td>
					</tr> 
				</table>
				<table>
					<tr>
						<td style="vertical-align: top;">
							<div class="box-radius" style="width:520px;">
								<div class="group-box">
									<span>รหัสสมาชิก</span>	
									<div class="border-bottom-dotted" style="width: 110px;text-align: center;">&nbsp;<?php echo @$row_member['member_id'];?></div>	
									<span>ชื่อ - นามสกุล</span>	
									<div class="border-bottom-dotted" style="width: 252px;text-align: center;">&nbsp;<?php echo @$row_member['prename_full']. @$row_member['firstname_th'].'  '.@$row_member['lastname_th'];?></div>
								</div>
								<div class="group-box">
									<span>สังกัด</span>	
									<div class="border-bottom-dotted" style="width: 476px;text-align: center;">&nbsp;<?php echo (empty($row_member['department_name']))?"-":@$row_member['department_name'];?></div>	
								</div>								
								<div class="group-box">
									<span>หน่วยงาน</span>	
									<div class="border-bottom-dotted" style="width: 453px;text-align: center;">&nbsp;<?php echo (empty($row_member['faction_name']))?"-":@$row_member['faction_name'];?></div>	
								</div>								
								<div class="group-box">
									<span>กลุ่ม</span>
									<div class="border-bottom-dotted" style="width: 207px;text-align: center;">&nbsp;<?php echo (empty($row_member['level_name']))?"-":@$row_member['level_name'];?></div>	
									<span>รูปแบบสมาชิก</span>	
									<div class="border-bottom-dotted" style="width: 194px;text-align: center;">&nbsp;<?php echo (empty($row_member['mem_type_name']))?"-":@$row_member['mem_type_name'];?></div>
								</div>								
								<div class="group-box">
									<span>ว/ด/ป เกิด</span>	
									<div class="border-bottom-dotted" style="width: 181px;text-align: center;">&nbsp;<?php echo $this->center_function->ConvertToThaiDate(@$row_member['birthday'],0,0);?></div>	
									<span>อายุ</span>
									<div class="border-bottom-dotted" style="width: 70px;text-align: center;">&nbsp;<?php echo $this->center_function->diff_birthday(@$row_member['birthday']);?></div>
									<span>ปี</span>
								</div>
								<div class="group-box">
									<span>สังกัด</span>	
									<div class="border-bottom-dotted" style="width: 307px;text-align: center;">&nbsp;<?php echo (empty($row_member['faction_name']))?"-":@$row_member['faction_name'];?></div>	
								</div>
								<!--<div class="group-box">
									<span>มีการกำหนดกรณีไม่ผ่านเกณฑ์</span>	
									<div class="border-bottom-dotted" style="width: 384px;text-align: center;">&nbsp;<?php echo (@$deduct_person_guarantee == 0)?"-":number_format(@$deduct_person_guarantee,2)." บาท";?></div>
								</div>
								<div class="group-box">
									<span>ค่าธรรมเนียม</span>
									<div class="border-bottom-dotted" style="width: 452px;text-align: center;">&nbsp;<?php echo (@$deduct_loan_fee == 0)?"-":number_format(@$deduct_loan_fee,2)." บาท";?></div>	
								</div>
								-->
								<div class="group-box" style="width: 100%;">
									<span>&nbsp;</span>	
									<div style="border-bottom: 1px dotted #ffffff;">&nbsp;</div>	
								</div>
								<div class="group-box" style="width: 100%;">
									<span>&nbsp;</span>	
									<div style="border-bottom: 1px dotted #ffffff;">&nbsp;</div>		
								</div>
								<div class="group-box">
									<span>&nbsp;</span>	
									<div>&nbsp;</div>	
								</div>	
							</div>
						</td>
						<td style="padding-left: 5px;vertical-align: top;">
							<div class="box-radius" style="width:430px; min-height: 188px;">
								<div class="group-box">
									<span>รูปแบบดอกเบี้ย</span>
									<div class="border-bottom-dotted" style="width: 190px;text-align: center;">&nbsp;<?php echo @$pay_type;?></div>
									<span>อัตราดอกเบี้ย</span>	
									<div class="border-bottom-dotted" style="width: 60px;text-align: center;">&nbsp;<?php echo  (@$row_loan['interest_per_year'] == 0)?"-":number_format(@$row_loan['interest_per_year'],2)." %";?></div>	
								</div>	
								<div class="group-box">
									<span>วงเงินที่ขออนุมัติ</span>	
									<div class="border-bottom-dotted" style="width: 130px;text-align: right;"><?php echo (@$row_loan['loan_amount'] == 0)?"-":number_format(@$row_loan['loan_amount'],2);?>&nbsp;</div>
									<span>บาท&nbsp;&nbsp;&nbsp;</span>										
									<span>จำนวนงวด</span>	
									<div class="border-bottom-dotted" style="width: 65px;text-align: center;">&nbsp;<?php echo (@$row_loan['period_amount'] == '')?"-":@$row_loan['period_amount'];?></div>
									<span>งวด</span>	
								</div>
								<div class="group-box">
									<span>ผ่อนต่อเดือน</span>	
									<div class="border-bottom-dotted" style="width: 152px;text-align: right;"><?php echo (@$total_paid_per_month == 0)?"-":number_format(@$total_paid_per_month,2);?>&nbsp;</div>
									<span>บาท&nbsp;&nbsp;&nbsp;</span>
									<span>พ.ร.บ</span>
									<div class="border-bottom-dotted" style="width: 89px;text-align: center;">&nbsp;<?php echo (@$deduct_law_insurance == 0)?"-":@$deduct_law_insurance;?></div>
									<span>บาท</span>
								</div>	
								<div class="group-box">
									<span>หักเงินกู้เดิม</span>	
									<div class="border-bottom-dotted" style="width: 156px;text-align: right;"><?php echo (@$existing_loan == 0)?"-":number_format(@$existing_loan,2);?>&nbsp;</div>
									<span>บาท</span>

								</div>	
<!--								<div class="group-box">-->
<!--									<span>ภาระเงินต้น</span>	-->
<!--									<div class="border-bottom-dotted" style="width: 158px;text-align: right;">--><?php //echo (@$principal_load == 0)?"-":number_format(@$principal_load,2);?><!--&nbsp;</div>-->
<!--									<span>บาท</span>	-->
<!--								</div>	-->
<!--								<div class="group-box">-->
<!--									<span>ภาระดอกเบี้ย</span>	-->
<!--									<div class="border-bottom-dotted" style="width: 149px;text-align: right;">--><?php //echo (@$interest_burden == 0)?"-":number_format(@$interest_burden,2);?><!--&nbsp;</div>-->
<!--									<span>บาท</span>	-->
<!--								</div>	-->
								<div class="group-box">
									<span>ดอกเบี้ย</span>
									<div class="border-bottom-dotted" style="width: 177px;text-align: right;"><?php echo (@$deductible == 0)?"-":number_format($deductible,2);?>&nbsp;</div>
									<span>บาท</span>	
								</div>
								<div class="group-box">
									<span>ค่าทำนิติกรรม</span>
									<div class="border-bottom-dotted" style="width: 152px;text-align: right;"><?php echo (@$deduct_law == 0)?"-":number_format($deduct_law,2);?>&nbsp;</div>
									<span>บาท</span>
								</div>
								<div class="group-box">
									<span>ค่าเช็ค</span>	
									<div class="border-bottom-dotted" style="width: 186px;text-align: right;"><?php echo (@$deduct_cheque == 0)?"-":number_format($deduct_cheque,2);?>&nbsp;</div>
									<span>บาท</span>	
								</div>
								<div class="group-box">
									<span>เจ้าหนี้บริษัท</span>
									<div class="border-bottom-dotted" style="width: 152px;text-align: right;"><?php echo (@$dedect_buyer == 0)?"-":number_format($dedect_buyer,2);?>&nbsp;</div>
									<span>บาท</span>
								</div>
								<div class="group-box text-red">
									<span class="text-red">ยอดรับสุทธิ</span>	
									<div class="border-bottom-dotted-red" style="width: 159px;text-align: right;"><?php echo (@$total_amount == 0)?"-":number_format(@$total_amount,2);?>&nbsp;</div>
									<span class="text-red">บาท</span>	
								</div>		
							</div>
						</td>
					</tr>
				</table>
				
				<table style="margin: 5px;">
					<tr>
						<td style="vertical-align: top;width:330px;">
							<span class="text-title">รายได้/ค่าใช้จ่าย</span>
							<?php
								$salary = (!empty($row_report_detail))?@$row_report_detail['salary']:@$row_member['salary'];
								$other_income = (!empty($row_report_detail))?@$row_report_detail['other_income']:@$row_member['other_income'];
								$total_income =  @$salary+@$other_income;
								
								//$rules_share = (!empty($row_report_detail))?@$row_report_detail['rules_share']:@$rules_share;
								$cal_share = (!empty($row_report_detail))?@$row_report_detail['now_share']:@$cal_share;
								$account_blue_deposit = (!empty($row_report_detail))?@$row_report_detail['account_blue_deposit']:@$account_blue_deposit;
								

                                if(!isset($loan_cost_code) && count($loan_cost_code) == 0) {
                                    $total_expenses = 0;
                                }else{
                                    $total_expenses = array_sum(array_map( function($item){ return $item['loan_cost_amount']; }, $loan_cost_code));
                                }
								$income_costs_balance = $total_income - $total_expenses;
								
							?>
                            <input type="hidden" name="debug" value="<?php print_r($coop_loan_cost)?>">
							<table class="bordered">
								<tr>
									<th>รายการ</th>        
									<th style="width:100px;">จำนวนเงิน (บาท)</th>
								</tr>
								<tr>
									<td>รายได้</td>        
									<td style="text-align:right;"></td>
								</tr>
								<tr>
									<td>- เงินเดือน</td>        
									<td style="text-align:right;"><?php echo (@$salary == 0)?"-":number_format(@$salary,2);?></td>
								</tr> 
								<tr>      
									<td>- รายได้อื่นๆ</td>
									<td style="text-align:right;"><?php echo (@$other_income == 0)?"-":number_format(@$other_income,2);?></td>
								</tr> 
								<tr>      
									<td class="font-bold">รวมรายได้</td>
									<td class="font-bold" style="text-align:right;"><?php echo (@$total_income == 0)?"-":number_format(@$total_income,2);?></td>
								</tr>
                                <?php if(isset($loan_cost_code)){ ?>
                                <tr>
                                    <td>ค่าใช้จ่าย</td>
                                    <td style="text-align:right;"></td>
                                </tr>
                                <?php foreach ($loan_cost_code as $key => $item) { ?>
                                <tr>
                                    <td>- <?php echo $item['outgoing_name'];?></td>
                                    <td style="text-align:right;"><?php echo (@$item['loan_cost_amount'] == 0)?"-":number_format(@$item['loan_cost_amount'],2);?></td>
                                </tr>
                                <?php }
                                } ?>
								<tr>      
									<td class="font-bold">รวมค่าใช้จ่าย</td>
									<td class="font-bold" style="text-align:right;"><?php echo (@$total_expenses == 0)?"-":number_format(@$total_expenses,2);?></td>
								</tr> 
								<tr>      
									<td class="font-bold border-top" style="text-align:center;">คงเหลือ</td>
									<td class="font-bold border-top" style="text-align:right;"><?php echo number_format(@$income_costs_balance,2);?></td>
								</tr>   
							</table>
							<?php if(isset($loan_deduct_list) && count($loan_deduct_list) >= 1){ ?>
							<span class="text-title">ประกันชีวิต</span>
							<?php
								$life_insurance_1 = 0;
								$life_insurance_2 = (@$cremation_type_2 > 0)?@$cremation_type_2:0;
								$life_insurance_3 = (@$cremation_type_1 > 0)?@$cremation_type_1:0;
								$life_insurance_4 = (@$life_insurance_4 > 0)?@$life_insurance_4:0;
								$life_insurance_5 = (@$life_insurance_5 > 0)?@$life_insurance_5:0;
								$life_insurance_6 = (@$life_insurance_6 > 0)?@$life_insurance_6:0;
								foreach($loan_deduct_list as $key => $value){
									if(@$loan_deduct['buy_s_s_o_k'] > 0 ){
										$life_insurance_2 = 600000;
									}

									if(@$loan_deduct['buy_ch_s_o'] > 0){
										$life_insurance_3 = 600000;
									}
								}	
							?>
							<table class="bordered">
								<tr>
									<th>รายการ</th>        
									<th style="width:100px;">จำนวนเงิน (บาท)</th>
								</tr>
								<tr>
									<td>หุ้น</td>        
									<td style="text-align:right;"><?php echo (@$cal_share > @$rules_share)?number_format(@$cal_share,2):number_format(@$rules_share,2);?></td>
								</tr>
								<tr>
									<td>สสอค. </td>        
									<td style="text-align:right;"><?php echo (@$life_insurance_2 == 0)?"-":number_format(@$life_insurance_2,2);?></td>
								</tr>
								<tr>
									<td>ชสอ. </td>        
									<td style="text-align:right;"><?php echo (@$life_insurance_3 == 0)?"-":number_format(@$life_insurance_3,2);?></td>
								</tr> 
								<!--<tr>      
									<td>ทุนประกันสังคม</td>
									<td style="text-align:right;"><?php echo (@$life_insurance_3 == 0)?"-":number_format(@$life_insurance_3,2);?></td>
								</tr>-->
								<tr>      
									<td class="font-bold border-top">ทุนประกันเดิม</td>
									<td class="font-bold border-top" style="text-align:right;"><?php echo (@$life_insurance_4 == 0)?"-":number_format(@$life_insurance_4,2);?></td>
								</tr> 
								<!--<tr>      
									<td class="font-bold">ทุนประกันใหม่</td>
									<td class="font-bold" style="text-align:right;"><?php echo (@$life_insurance_5 == 0)?"-":number_format(@$life_insurance_5,2);?></td>
								</tr>-->
								<tr>      
									<td class="font-bold">ทุนประกันเพิ่ม</td>
									<td class="font-bold" style="text-align:right;"><?php echo (@$life_insurance_6 == 0)?"-":number_format(@$life_insurance_6,2);?></td>
								</tr>  
								<tr>      
									<td class="font-bold">เบี้ยประกัน</td>
									<td class="font-bold" style="text-align:right;"><?php echo (@$deduct_insurance == 0)?"-":number_format(@$deduct_insurance,2);?></td>
								</tr>  
							</table>
							<?php } ?>
							<span class="text-title">หุ้น</span>
							<table class="bordered">
								<tr>
									<th>รายการ</th>        
									<th style="width:100px;">จำนวนเงิน (บาท)</th>
								</tr>
								<tr>
									<td>หุ้นตามหลักเกณฑ์</td>        
									<td style="text-align:right;"><?php echo (@$rules_share == 0)?"-":number_format(@$rules_share,2);?></td>
								</tr>
								<tr>
									<td>หุ้นที่มี</td>        
									<td style="text-align:right;"><?php echo (@$cal_share == 0)?"-":number_format(@$cal_share,2); ?></td>
								</tr> 
								<?php
									foreach($guarantee_saving as $k => $v){
										?>
											<tr>
												<td>เงินฝากบัญชี <?=$v['account_id']?></td>        
												<td style="text-align:right;"><?php echo (@$v['transaction_balance'] == 0)?"-":number_format($v['transaction_balance'],2); ?></td>
											</tr>
										<?php
									}
								?>
								<!-- <tr>      
									<td>เงินฝากสีน้ำเงิน</td>
									<td style="text-align:right;"><?php echo number_format(@$account_blue_deposit,2);?></td>
								</tr> -->
								<!--<tr>
									<td>เดิม</td>        
									<td style="text-align:right;"><?php echo number_format(@$old_share,2);?></td>
								</tr> 
								<tr>      
									<td>เข้าบัญชีเงินฝาก</td>
									<td style="text-align:right;"><?php echo number_format(@$deposit_account_in,2);?></td>
								</tr> -->
							</table>
						</td>
						<td style="padding-left: 5px;vertical-align: top;width:330px;">
							<span class="text-title">รายการผ่อนชำระสหกรณ์ปัจจุบัน/เดือน</span>
							<table class="bordered">								
								<tr>
									<th rowspan="2">รายการ</th>        
									<th colspan="2">จำนวนเงิน (บาท)</th>
								</tr>
								<tr>
									<th class="border-left" style="width: 65px;">เงินต้น</th>        
									<th class="border-left" style="width: 65px;">ดอกเบี้ย</th>
								</tr>
							
								<tr>
									<td>หุ้นหักรายเดือน</td>        
									<td style="text-align:right;"><?php echo (@$share_month == 0)?"-":number_format(@$share_month,2);?></td>
									<td style="text-align:right;"><?php echo (@$share_month_interest == 0)?"-":number_format(@$share_month_interest,2);?></td>
								</tr>
								<tr>
									<td>เงินฝากหักรายเดือน</td>        
									<td style="text-align:right;"><?php echo (@$deposit_month == 0)?"-":number_format(@$deposit_month,2);?></td>
									<td style="text-align:right;"><?php echo (@$deposit_month_interest == 0)?"-":number_format(@$deposit_month_interest,2);?></td>
								</tr> 
								<?php								 
								if(!empty($list_loan)){
									foreach($list_loan AS $key=>$value){
								?>
								<tr>
									<td><?php echo @$value['loan_name'];?></td>        
									<td style="text-align:right;"><?php echo (@$value['loan_principle'] == 0)?"-":number_format(@$value['loan_principle'],2);?></td>
									<td style="text-align:right;"><?php echo (@$value['loan_interest'] == 0)?"-":number_format(@$value['loan_interest'],2);?></td>
								</tr> 
								<?php
									}
								 }
								?>							
								<tr>      
									<td class="font-bold border-top" style="text-align:center;">รวมทั้งสิ้น</td>
									<td class="font-bold border-top" style="text-align:right;"><?php echo (@$total_month == 0)?"-":number_format(@$total_month,2);?></td>
									<td class="font-bold border-top" style="text-align:right;"><?php echo (@$total_month_interest == 0)?"-":number_format(@$total_month_interest,2);?></td>
								</tr> 
							</table>
							
							<span class="text-title">รายการรับเงิน</span>
							<table class="bordered">
								<tr>
									<th>รายการ</th>        
									<th style="width:100px;">จำนวนเงิน (บาท)</th>
								</tr>
								<?php 
									if(!empty($receiving_money)){
										foreach($receiving_money AS $key=>$row_receiving_money){
											if($key==sizeof($receiving_money)-1)
												$row_receiving_money['total_received'] += @$extra_debt['total_princical'];
								?>
								<tr>
									<td style="vertical-align: top;vertical-align: top;"><?php echo @$row_receiving_money['transfer_type'];?></td>        
									<td style="text-align:right;vertical-align: top;"><?php echo (@$row_receiving_money['total_received'] == 0)?"-":number_format(@$row_receiving_money['total_received'],2);?></td>
								</tr>
								<?php 
										}
									}
								?>
							</table>
							
							<span class="text-title">เงินฝาก</span>
							<table class="bordered">
								<tr>
									<th>รายการ</th>        
									<th style="width:100px;">จำนวนเงิน (บาท)</th>
								</tr>
								<!--
								<?php
								if(!empty($account_list)){
									foreach($account_list AS $key => $value){
								?>
								<tr>
									<td><?php echo @$value['account_id'].':'.@$value['account_name'];?></td>        
									<td style="text-align:right;"><?php echo (@$value['account_balance'] == 0)?"-":number_format(@$value['account_balance'],2);?></td>
								</tr> 
								
								<?php	}
								}
								?>
								-->
							</table>
						</td>
						<td style="padding-left: 5px;vertical-align: top;width:330px;">
							<?php
								$broken = 0; //ยอดหัก
								$total_installment = $income_costs_balance-$broken-$total_month-$total_paid_per_month; //คงเป็นยอดผ่อนชำระได้
							?>
							<!--<span class="text-title">คำนวณสิทธิ์</span>
							<table class="bordered">
								<tr>
									<th>รายการ</th>        
									<th style="width:100px;">จำนวนเงิน (บาท)</th>
								</tr>
								<tr>
									<td>เงินเหลือรายเดือน</td>        
									<td style="text-align:right;"><?php echo (@$income_costs_balance == 0)?"-":number_format(@$income_costs_balance,2);?></td>
								</tr> 
								<tr>
									<td>ยอดหัก <?php echo number_format(10,2);?></td>        
									<td style="text-align:right;"><?php echo (@$broken == 0)?"-":number_format(@$broken,2);?></td>
								</tr> 
								<tr>
									<td>รวมหักรายเดือน</td>        
									<td style="text-align:right;"><?php echo (@$total_month == 0)?"-":number_format(@$total_month,2);?></td>
								</tr> 
								<tr>
									<td>รวมหักรายเดือน(ใหม่)</td>        
									<td style="text-align:right;"><?php echo (@$total_paid_per_month == 0)?"-":number_format(@$total_paid_per_month,2);?></td>
								</tr>
								<tr>
									<td class="font-bold">คงเป็นยอดผ่อนชำระได้</td>        
									<td class="font-bold" style="text-align:right;"><?php echo (@$total_installment == 0)?"-":number_format(@$total_installment,2);?></td>
								</tr>  
							</table>
							-->
							
							<span class="text-title">ปิดสัญญาเดิม</span>
							<table class="bordered">
								<tr>
									<th rowspan="2">เลขที่สัญญา</th>        
									<th colspan="4">จำนวนเงิน (บาท)</th>
								</tr>
								<tr>
									<th class="border-left" style="width:50px;">เงินต้น</th>        
									<th class="border-left" style="width:50px;">ดอกเบี้ย</th>
									<th class="border-left" style="width:50px;">ดอกเบี้ยค้างชำระ</th>
									<th class="border-left" style="width:50px;">ค่าธรรมเนียม</th>
								</tr>
								<?php
								$total_loan_balance = 0;
								$total_loan_interest = 0;
								$total_loan_outstanding = 0;
								$total_loan_fee = 0;
								if(!empty($list_old_loan)){
									foreach($list_old_loan AS $key => $value){
										$loan_balance = @$value['loan_amount_balance'];
										$loan_interest = @$value['loan_interest_amount'];
										$loan_outstanding = 0;
										$loan_fee = 0;		

										$total_loan_balance += @$loan_balance;
										$total_loan_interest += @$loan_interest;
										$total_loan_outstanding += @$loan_outstanding;
										$total_loan_fee += @$loan_fee;
								?>
								<tr>
									<td><?php echo @$value['contract_number'];?></td>
									<td style="text-align:right;"><?php echo (@$loan_balance == 0)?"-":number_format(@$loan_balance,2);?></td>
									<td style="text-align:right;"><?php echo (@$loan_interest == 0)?"-":number_format(@$loan_interest,2);?></td>
									<td style="text-align:right;"><?php echo (@$loan_outstanding == 0)?"-":number_format(@$loan_outstanding,2);?></td>
									<td style="text-align:right;"><?php echo (@$loan_fee == 0)?"-":number_format(@$loan_fee,2);?></td>
								</tr> 
								
								<?php	}
								}
								?>
								
								<tr>      
									<td class="font-bold border-top" style="text-align:center;">รวมทั้งสิ้น</td>
									<td class="font-bold border-top" style="text-align:right;"><?php echo (@$total_loan_balance == 0)?"-":number_format(@$total_loan_balance,2);?></td>
									<td class="font-bold border-top" style="text-align:right;"><?php echo (@$total_loan_interest == 0)?"-":number_format(@$total_loan_interest,2);?></td>
									<td class="font-bold border-top" style="text-align:right;"><?php echo (@$total_loan_outstanding == 0)?"-":number_format(@$total_loan_outstanding,2);?></td>
									<td class="font-bold border-top" style="text-align:right;"><?php echo (@$total_loan_fee == 0)?"-":number_format(@$total_loan_fee,2);?></td>
								</tr> 
							</table>
							<?php if(isset($loan_deduct_list) && count($loan_deduct_list) >= 1){ ?>
							<span class="text-title">รายการซื้อ</span>
							<table class="bordered">
								<tr>
									<th>รายการ</th>        
									<th style="width:100px;">จำนวนเงิน (บาท)</th>
								</tr>
								<?php
								$total_buy = 0;
								$total_deduct = 0;
								foreach($loan_deduct_list as $key => $value){
									$total_deduct += @$loan_deduct[$value['loan_deduct_list_code']];
								?>								
								<tr>
									<td><?php echo @$value['loan_deduct_list'];?></td>        
									<td style="text-align:right;vertical-align: top;"><?php echo @$loan_deduct[$value['loan_deduct_list_code']]!=''?number_format($loan_deduct[$value['loan_deduct_list_code']],2):'-';?></td>
								</tr> 
								<?php
									
								}
								$total_buy = @$total_deduct;								
								?>
								
								<tr>      
									<td class="font-bold border-top" style="text-align:center;">รวมทั้งสิ้น</td>
									<td class="font-bold border-top" style="text-align:right;"><?php echo (@$total_buy == 0)?"-":number_format(@$total_buy,2);?></td>
								</tr>  
							</table>
							<?php } ?>
							<span class="text-title">&nbsp;</span>
							<?php 
								$total_pay_external = @$total_expenses;
								//จากเงินกู้ ATM
								if(@$check_type_loan == 'loan_atm'){
									$total_pay_in = @$total_month+$total_month_interest+(@$total_paid_per_month+@$interest_30_day);
									//$total_pay_in = @$total_month+(@$total_paid_per_month+@$interest_30_day);
									//echo @$total_month.'+'.$total_month_interest.'+('.@$total_paid_per_month.'+'.@$interest_30_day.')<hr>';
								}else{								
									//ถ้าเขาหักกลบก็เอารายเดือนยอดใหม่เลย แต่ถ้าไม่หักกลบก็ต้องเอายอดที่ต้องผ่อนหนี้เก่ามาด้วย
									if(!empty($list_old_loan)){
										$type_offset = 1; //0=ไม่หักกลบ,1=หักลบ  
									}else{
										$type_offset = 0; //0=ไม่หักกลบ,1=หักลบ  
									}		
									
									if($type_offset == 0){
										//$total_pay_in = @$total_month+@$total_paid_per_month;
										//ค่าหุ้น600+งวดสามัญ ต้น10779+งวดสามัญ ดอก12221 และเงินงวดฉุกเฉินที่กู้ใหม่คือ 600+66 เป็นค่าใช้จ่ายภายในสหกรณ์
										$total_pay_in = @$total_month+@$total_month_interest+@$total_paid_per_month+@$interest_30_day;
										if(@$_GET['dev'] == 'dev'){
											echo @$pay_type_id.'<br>';
											echo @$pay_type.'<hr>';
											$total_pay_in_a = @$total_month+@$total_month_interest+@$total_paid_per_month+@$interest_30_day;
											echo @$total_month.'+'.@$total_month_interest.'+'.@$total_paid_per_month.'+'.@$interest_30_day.'<br>';
											echo $total_pay_in_a.'<br>';

										}
					
									}else if($type_offset == 1){
										$loan_list_loan = 0;
										$loan_list_loan_interest = 0;
										foreach($list_old_loan AS $key_old=>$value_old){
											foreach($arr_list_loan AS $key_list=>$value_list){
												//echo '<pre>'; print_r($value_list); echo '</pre>';
												if($value_old['loan_id'] == $value_list['loan_id']){
													$loan_list_loan += @$value_list['loan_principle'];		
													$loan_list_loan_interest += @$value_list['loan_interest'];		
												}
											}	
										}
										//ตัวอย่างการคิดยอดหักภายในสหกรณ์( สามัญต้น 5000+ดอก 7228+หุ้น1000+ฉฉที่กู้ใหม่ 4600+520
										$total_pay_in = (@$total_month + $total_month_interest - @$loan_list_loan - @$loan_list_loan_interest)+@$total_paid_per_month+@$interest_30_day;
										/*
											2019_02_15 แก้ไข เงื่อนไข การณีชำระแบบคงต้น
											ค่าใช้จ่ายภายในสหกรณ์
											1.เงินกู้ใหม่ เงินต้น 4,700 บาท
											2.เงินกู้ใหม่ ดอกเบี้ย 6,837 บาท
											3.หุ้นรายเดือน 600 บาท
											รวมเป็น 12,137 บาท
										*/
										if(@$_GET['dev'] == 'dev'){
											echo @$pay_type_id.'<br>';
											echo @$pay_type.'<hr>';
											$total_pay_in_a = (@$total_month + $total_month_interest - @$loan_list_loan - @$loan_list_loan_interest)+@$total_paid_per_month+@$interest_30_day;
											echo '('.@$total_month.' + '.$total_month_interest.' - '.@$loan_list_loan.' - '.@$loan_list_loan_interest.')+'.@$total_paid_per_month.'+'.@$interest_30_day.'<br>';
											echo $total_pay_in_a.'<br>';

										}

										//$total_pay_in = (@$total_month - @$loan_list_loan)+@$total_paid_per_month+@$interest_30_day;
										//echo $total_pay_in.'<hr>';
										//echo '('.@$total_month.' + '.$total_month_interest.' - '.@$loan_list_loan.' - '.@$loan_list_loan_interest.')+'.@$total_paid_per_month.'+'.@$interest_30_day.'<br>'; 
									}	
								}
								
								$total_pay_all = @$total_pay_external+@$total_pay_in;
								$total_amount_all = @$total_income-@$total_pay_all;
								$percent_amount_all = @(@$total_amount_all*100/@$total_income);
							?>
							<!--<table class="bordered">
									<tr>
										<th>รายการ</th>
										<th style="width:100px;">จำนวนเงิน (บาท)</th>
									</tr>
									<tr>
										<td>รวมค่าใช้จ่ายภายนอก</td>
										<td style="text-align:right;"><?php /*echo (@$total_pay_external == 0)?"-":number_format(@$total_pay_external,2);*/?></td>
									</tr>
									<tr>
										<td>รวมค่าใช้จ่ายภายในสหกรณ์</td>
										<td style="text-align:right;"><?php /*echo (@$total_pay_in == 0)?"-":number_format(@$total_pay_in,2);*/?></td>
									</tr>
									<tr>
										<td class="font-bold">รวมค่าใช้จ่ายทั้งสิน</td>
										<td class="font-bold" style="text-align:right;"><?php /*echo (@$total_pay_all == 0)?"-":number_format(@$total_pay_all,2);*/?></td>
									</tr>
									<tr>
										<td class="font-bold">คงเหลือ <?php /*echo number_format(@$total_income,2);*/?> - <?php /*echo number_format(@$total_pay_all,2);*/?></td>
										<td class="font-bold" style="text-align:right;"><?php /*echo (@$total_amount_all == 0)?"-":number_format(@$total_amount_all,2);*/?></td>
									</tr>
									<tr>
										<td class="font-bold border-top" style="text-align:center;">คิดเป็น</td>
										<td class="font-bold border-top" style="text-align:right;"><?php /*echo (@$percent_amount_all <= 0)?"-":number_format(@$percent_amount_all,2).'  %';*/?></td>
									</tr>
							</table>-->
						</td>
					</tr>
				</table>
				<table style="margin: 5px;">
					<tr>
						<td style="width:50%">
							<table class="signature_part">
								<tr><td>&nbsp;</td></tr>
								<tr><td>&nbsp;</td></tr>
								<tr><td>ลงชื่อ......................................จนท.ฝ่ายสินเชื่อฯ</td></tr>
								<tr><td>&nbsp;</td></tr>
								<tr><td>&nbsp;</td></tr>
								<tr><td>ลงชื่อ......................................ผู้ช่วยผู้จัดการฝ่ายข้อมูล</td></tr>
								<tr><td>&nbsp;</td></tr>
								<tr><td>&nbsp;</td></tr>
								<tr><td>จึงเรียน คณะกรรมการเงินกู้เพื่อโปรดพิจารณาอนุมัติ ให้แก่สมาชิกต่อไป</td></tr>
								<tr><td>&nbsp;</td></tr>
								<tr><td>&nbsp;</td></tr>
								<tr><td class="text-center">ขอแสดงความนับถือ</td></tr>
								<tr><td>&nbsp;</td></tr>
								<tr><td>&nbsp;</td></tr>
								<!-- <tr><td class="text-center">(<?php echo $boards["vice_manager"]["name"]?>)</td></tr>
								<tr><td class="text-center">รองผู้จัดการ</td></tr>
								<tr><td class="text-center">ปฏิบัติหน้าที่แทน ผจก.</td></tr> -->
							</table>
						</td>
						<td style="width:50%;">
							<table class="signature_part" style="margin-left:50px;">
								<?php
									$i = 1;
									if(!empty($boards)) {
										foreach($boards["boards"] as $board) {
								?>
									<tr><td>&nbsp;</td></tr>
									<tr><td><?php echo $i++;?>. ลงชื่อ......................................(<?php echo $board["name"];?>)</td></tr>
									<tr><td>&nbsp;</td></tr>
								<?php
										}
									}
								?>

							</table>
						</td>
					<tr>
				</table>
			</div>
		</div>
		<!--page 2-->
		<div style="width: 1000px;">
			<div class="panel panel-body" style="padding-top:20px !important;height: 1400px;">
				<table style="width: 100%;">
					<tr>
						<td style="width:100px;vertical-align: top;">
							<img src="<?php echo base_url(PROJECTPATH.'/assets/images/coop_profile/'.$_SESSION['COOP_IMG']); ?>" alt="Logo" style="height: 80px;" />
						</td>
						<td>
							 <h3 class="title_view"><?php echo @$_SESSION['COOP_NAME'];?></h3>
							 <h3 class="title_view">รายละเอียดการขอกู้เงิน <?php echo @$type_loan;?></h3>
							<p>&nbsp;</p>	
						 </td>
						 <td style="width:250px;vertical-align: top;" class="text-right">
							<div class="group-box">
								<span>เลขที่คำขอ</span>	
								<div class="border-bottom-dotted" style="width: 156px;text-align: center;">&nbsp;<?php echo @$petition_number;?></div>	
							</div>		
							<div class="group-box">
								<span>วันที่</span>	
								<div class="border-bottom-dotted" style="width: 181px;text-align: center;">&nbsp;<?php echo $this->center_function->ConvertToThaiDate(@$row_loan['createdatetime'],0,0);?></div>	
							</div>	
						 </td>
					</tr> 
					<tr>
						<td colspan="3">
							<h3 class="title_view">
							</h3>
						</td>
					</tr> 
				</table>
				<table>
					<tr>
						<td style="vertical-align: top;">
							<div class="box-radius" style="width:520px;">
								<div class="group-box">
									<span>รหัสสมาชิก</span>	
									<div class="border-bottom-dotted" style="width: 110px;text-align: center;">&nbsp;<?php echo @$row_member['member_id'];?></div>	
									<span>ชื่อ - นามสกุล</span>	
									<div class="border-bottom-dotted" style="width: 252px;text-align: center;">&nbsp;<?php echo  @$row_member['prename_full'].@$row_member['firstname_th'].'  '.@$row_member['lastname_th'];?></div>
								</div>
								<div class="group-box">
									<span>สังกัด</span>	
									<div class="border-bottom-dotted" style="width: 476px;text-align: center;">&nbsp;<?php echo (empty($row_member['department_name']))?"-":@$row_member['department_name'];?></div>	
								</div>								
								<div class="group-box">
									<span>หน่วยงาน</span>	
									<div class="border-bottom-dotted" style="width: 453px;text-align: center;">&nbsp;<?php echo (empty($row_member['faction_name']))?"-":@$row_member['faction_name'];?></div>	
								</div>								
								<div class="group-box">
									<span>กลุ่ม</span>
									<div class="border-bottom-dotted" style="width: 207px;text-align: center;">&nbsp;<?php echo (empty($row_member['level_name']))?"-":@$row_member['level_name'];?></div>	
									<span>รูปแบบสมาชิก</span>	
									<div class="border-bottom-dotted" style="width: 194px;text-align: center;">&nbsp;<?php echo (empty($row_member['mem_type_name']))?"-":@$row_member['mem_type_name'];?></div>
								</div>								
								<div class="group-box">
									<span>ว/ด/ป เกิด</span>	
									<div class="border-bottom-dotted" style="width: 181px;text-align: center;">&nbsp;<?php echo $this->center_function->ConvertToThaiDate(@$row_member['birthday'],0,0);?></div>	
									<span>อายุ</span>
									<div class="border-bottom-dotted" style="width: 70px;text-align: center;">&nbsp;<?php echo $this->center_function->diff_birthday(@$row_member['birthday']);?></div>
									<span>ปี</span>
								</div>
								<!--<div class="group-box">
									<span>มีการกำหนดกรณีไม่ผ่านเกณฑ์</span>	
									<div class="border-bottom-dotted" style="width: 384px;text-align: center;">&nbsp;<?php echo (@$deduct_person_guarantee == 0)?"-":number_format(@$deduct_person_guarantee,2)." บาท";?></div>
								</div>
								<div class="group-box">
									<span>ค่าธรรมเนียม</span>	
									<div class="border-bottom-dotted" style="width: 452px;text-align: center;">&nbsp;<?php echo (@$deduct_loan_fee == 0)?"-":number_format(@$deduct_loan_fee,2)." บาท";?></div>	
								</div>-->
								<div class="group-box" style="width: 100%;">
									<span>&nbsp;</span>	
									<div style="border-bottom: 1px dotted #ffffff;">&nbsp;</div>	
								</div>
								<div class="group-box" style="width: 100%;">
									<span>&nbsp;</span>	
									<div style="border-bottom: 1px dotted #ffffff;">&nbsp;</div>	
								</div>
								<div class="group-box">
									<span>&nbsp;</span>	
									<div>&nbsp;</div>	
								</div>	
							</div>
						</td>
						<td style="padding-left: 5px;vertical-align: top;">
							<div class="box-radius" style="width:430px; min-height: 188px;">
								<div class="group-box">
									<span>รูปแบบดอกเบี้ย</span>	
									<div class="border-bottom-dotted" style="width: 190px;text-align: center;">&nbsp;<?php echo @$pay_type;?></div>
									<span>อัตราดอกเบี้ย</span>	
									<div class="border-bottom-dotted" style="width: 60px;text-align: center;">&nbsp;<?php echo  (@$row_loan['interest_per_year'] == 0)?"-":number_format(@$row_loan['interest_per_year'],2)." %";?></div>	
								</div>	
								<div class="group-box">
									<span>วงเงินที่ขออนุมัติ</span>	
									<div class="border-bottom-dotted" style="width: 130px;text-align: right;"><?php echo (@$row_loan['loan_amount'] == 0)?"-":number_format(@$row_loan['loan_amount'],2);?>&nbsp;</div>
									<span>บาท&nbsp;&nbsp;&nbsp;</span>										
									<span>จำนวนงวด</span>	
									<div class="border-bottom-dotted" style="width: 65px;text-align: center;">&nbsp;<?php echo (@$row_loan['period_amount'] == '')?"-":@$row_loan['period_amount'];?></div>
									<span>งวด</span>	
								</div>
								<div class="group-box">
									<span>ผ่อนต่อเดือน</span>	
									<div class="border-bottom-dotted" style="width: 152px;text-align: right;"><?php echo (@$total_paid_per_month == 0)?"-":number_format(@$total_paid_per_month,2);?>&nbsp;</div>
									<span>บาท</span>
								</div>	
								<div class="group-box">
									<span>หักเงินกู้เดิม</span>	
									<div class="border-bottom-dotted" style="width: 156px;text-align: right;"><?php echo (@$existing_loan == 0)?"-":number_format(@$existing_loan,2);?>&nbsp;</div>
									<span>บาท&nbsp;&nbsp;&nbsp;</span>
								</div>	
<!--								<div class="group-box">-->
<!--									<span>ภาระเงินต้น</span>	-->
<!--									<div class="border-bottom-dotted" style="width: 158px;text-align: right;">--><?php //echo (@$principal_load == 0)?"-":number_format(@$principal_load,2);?><!--&nbsp;</div>-->
<!--									<span>บาท</span>	-->
<!--								</div>	-->
<!--								<div class="group-box">-->
<!--									<span>ภาระดอกเบี้ย</span>	-->
<!--									<div class="border-bottom-dotted" style="width: 149px;text-align: right;">--><?php //echo (@$interest_burden == 0)?"-":number_format(@$interest_burden,2);?><!--&nbsp;</div>-->
<!--									<span>บาท</span>	-->
<!--								</div>	-->
								<div class="group-box">
									<span>ดอกเบี้ย</span>
									<div class="border-bottom-dotted" style="width: 177px;text-align: right;"><?php echo (@$deductible == 0)?"-":number_format($deductible,2);?>&nbsp;</div>
									<span>บาท</span>	
								</div>									
								<div class="group-box text-red">
									<span class="text-red">ยอดรับสุทธิ</span>	
									<div class="border-bottom-dotted-red" style="width: 159px;text-align: right;"><?php echo (@$total_amount == 0)?"-":number_format(@$total_amount,2);?>&nbsp;</div>
									<span class="text-red">บาท</span>	
								</div>	
							</div>
						</td>
					</tr>
				</table>
				
				
				<!-- <div style="text-align: left;width: 955px;">
					<span class="text-title">บุคคลค้ำประกัน</span>
				</div>	
				<table class="bordered" style="width: 955px;">
					<tr>
						<td style="width: 150px;">&nbsp;</td>
						<td style="height: 150px;border-left: 0px;padding-top: 10px;padding-bottom: 10px;">
								<table  class="no-border">
								<?php
								//echo '<pre>'; print_r(@$row_guarantee); echo '</pre>';
								$guarantee_no = 0;
								foreach(@$row_guarantee AS $key=>$guarantee){
									$guarantee_no++;
								?>
									<tr>
										<td>
											ผู้ค้ำลำดับที่ <?php echo @$guarantee_no;?>
										</td>
										<td>
											<?php echo @$guarantee['guarantee_person_id'];?>
										</td>
										<td>
											ชื่อ-สกุล
										</td>
										<td>
											<?php echo  @$guarantee['prename_full'].@$guarantee['firstname_th']." ".@$guarantee['lastname_th'];?>
										</td>
										<td>
											สังกัด
										</td>
										<td>
											<?php echo @$guarantee['mem_group_name'];?>
										</td>
									</tr>
									<tr>
										<td>
											ภาระค้ำประกัน
										</td>
										<td>
											<?php echo (@$guarantee['guarantee_person_amount'] == 0)?"-":number_format(@$guarantee['guarantee_person_amount'],2);?>
										</td>
										<td colspan="3"> -->
											<!--ค้ำแล้ว
											<?php echo @$guarantee['count_guarantee'];?>	
											สัญญา
											-->
										<!-- </td>
									</tr>
								<?php	
								}
								?>	
								</table>							
						</td> 
						<td style="border-left: 0px;">&nbsp;</td>
					</tr>								  
				</table> -->
				
				<!-- <div style="text-align: left;width: 955px;">
					<span class="text-title">อสังหาทรัพย์ค้ำประกัน</span>
				</div>	
				<table class="bordered" style="width: 955px;">
					<tr>
						<td style="width: 150px;">&nbsp;</td>
						<td style="height: 150px;border-left: 0px;padding-top: 10px;padding-bottom: 10px;">
								<?php if(!empty($row_real_estate)){?>
								<table  class="no-border">
									<tr>
										<td colspan="3" class="font-bold">										
											ตำแหน่งที่ดิน
										</td>
									</tr>	
									<tr>
										<td>
											ระวาง 
											
											<?php echo @$row_real_estate['real_estate_position_1'];?>										
											|||
											<?php echo @$row_real_estate['real_estate_position_2'];?>
										</td>
										<td>เลขที่ดิน <?php echo @$row_real_estate['land_number'];?></td>
										<td>หน้าสำรวจ <?php echo @$row_real_estate['survey_page'];?></td>
									</tr>	
									<tr>
										<td>จังหวัด <?php echo @$row_real_estate['province_name'];?></td>
										<td>อำเภอ <?php echo @$row_real_estate['amphur_name'];?></td>
										<td>ตำบล <?php echo @$row_real_estate['district_name'];?></td>
									</tr>	
									<tr>
										<td colspan="3" class="font-bold">โฉนดที่ดิน</td>
									</tr>	
									<tr>
										<td>เลขที่ <?php echo @$row_real_estate['deed_number'];?></td>
										<td>เล่ม <?php echo @$row_real_estate['deed_book'];?></td>
										<td>หน้า <?php echo @$row_real_estate['deed_page'];?></td>
									</tr>	
									<tr>
										<td>จำนวนที่ดิน <?php echo @$row_real_estate['rai'];?>  ไร่</td>
										<td><?php echo @$row_real_estate['ngan'];?> งาน</td>
										<td><?php echo @$row_real_estate['tarangwah'];?> ตารางวา</td>
									</tr>
								</table>
							<?php }?>		
						</td> 
						<td style="border-left: 0px;">&nbsp;</td>
					</tr>								  
				</table>
				
				<div class="text-title">&nbsp;</div>
				<div class="text-title">&nbsp;</div>
				<table style="width: 955px;">
					<tr>
						<td>
							<div class="box-radius" style="width:475px;height: 300px;vertical-align: top;padding-left:10px;">
								<div>&nbsp;</div>
								<div class="group-box">
									<span>ลงชื่อ</span>	
									<div class="border-bottom-dotted" style="width: 350px;">&nbsp;</div>
									<span>ผู้ทำรายการ</span>	
								</div>	
								<div style="text-align: center;"><span><?php echo @$row_loan['admin_name'];?></span></div>
							</div>	
						</td>
						<td style="padding-left: 5px;">	
							<div class="box-radius" style="width:475px;height: 300px;vertical-align: top;padding-left:10px;">
								<div>&nbsp;</div>
								<div class="group-box">
									<span>ลงชื่อ</span>	
									<div class="border-bottom-dotted" style="width: 410px;">&nbsp;</div>
								</div>
																
								<div class="group-box" style="margin-left: 20px;">											
									<span>(</span>	
									<div class="border-bottom-dotted" style="width: 410px;">&nbsp;</div>
									<span>)</span>
								</div>
								<div style="text-align: center;"><span>หัวหน้าฝ่ายสินเชื่อ</span></div>	
					
								<div>&nbsp;</div>
								<div class="group-box">
									<span>ลงชื่อ</span>	
									<div class="border-bottom-dotted" style="width: 410px;">&nbsp;</div>
								</div>
																
								<div class="group-box" style="margin-left: 20px;">											
									<span>(</span>	
									<div class="border-bottom-dotted" style="width: 410px;">&nbsp;</div>
									<span>)</span>
								</div>
								<div style="text-align: center;"><span>รองผู้จัดการฝ่ายบริหาร</span></div>	
							
								<div>&nbsp;</div>
								<div class="group-box">
									<span>ลงชื่อ</span>	
									<div class="border-bottom-dotted" style="width: 410px;">&nbsp;</div>
								</div>
												
								<div class="group-box" style="margin-left: 20px;">											
									<span>(</span>	
									<div class="border-bottom-dotted" style="width: 410px;">&nbsp;</div>
									<span>)</span>
								</div>
								<div style="text-align: center;"><span>ผู้จัดการ</span></div>								
							</div>
						</td> 	
					</tr>								  
				</table>			
				
				<span class="text-title">&nbsp;</span>
				<table class="bordered"  style="width: 955px;">
					<tr>
						<td style="width:320px;"></td>
						<td style="border-left: 0px;">
							<div>&nbsp;</div>
							<table>
								<tr>
									<td>
										<div class="group-box">											
											<span>อนุมัติให้กู้ได้</span>	
											<div class="border-bottom-dotted" style="width: 363px;text-align:center;">&nbsp;<?php echo (@$row_loan['loan_amount'] == 0)?"-":number_format(@$row_loan['loan_amount'],2);?></div>
											<span>บาท</span>	
										</div>	
									</td> 
								</tr>
								<tr>
									<td>
										<div>&nbsp;</div>
										<div class="group-box">
											<span>ลงชื่อ</span>	
											<div class="border-bottom-dotted" style="width: 405px;">&nbsp;</div>
										</div>
									</td> 
								</tr>
								<tr>
									<td>											
										<div class="group-box" style="margin-left: 28px;">											
											<span>(</span>	
											<div class="border-bottom-dotted" style="width: 405px;">&nbsp;</div>
											<span>)</span>
										</div>
									</td> 
								</tr>
								<tr>
									<td style="text-align: center;">										
										<div class="group-box">
											<span>ประธานฝ่ายการเงินและสินเชื่อ</span>	
										</div>	
									</td> 
								</tr>
							</table>
							<div>&nbsp;</div>
						</td>
						<td style="width:320px;border-left: 0px;"></td>
					</tr>								
				</table> -->
				
			</div>
		</div>

		<!-- loanee info -->
		<div style="width: 1000px;">
			<div class="panel panel-body" style="padding-top:20px !important;height: 1400px;">
				<table style="width: 100%;">
					<tr>
						<td style="width:100px;vertical-align: top;">
							<img src="<?php echo base_url(PROJECTPATH.'/assets/images/coop_profile/'.$_SESSION['COOP_IMG']); ?>" alt="Logo" style="height: 80px;" />
						</td>
						<td>
							 <h3 class="title_view"><?php echo @$_SESSION['COOP_NAME'];?></h3>
							 <h3 class="title_view">รายการแจ้งหุ้นหนี้สมาชิก ประจำวันที่ <?php echo $this->center_function->ConvertToThaiDate(@$row_loan['createdatetime'],0,0);?></h3>
							<p>&nbsp;</p>	
						 </td>
					</tr> 
					<tr>
						<td colspan="3">
							<h3 class="title_view">
							</h3>
						</td>
					</tr> 
				</table>
				<div>&nbsp;</div>
				<table style="width: 100%;" class="no-border-b">
					<tr>
						<td style="width:10%;" class="text-center">
							ชื่อสมาชิก
						</td>
						<td>
							<?php echo $row_member["prename_full"].$row_member["firstname_th"]." ".$row_member["lastname_th"];?>
						</td>
						<td style="width:10%;" class="text-center">
							เลขที่สมาชิก
						</td>
						<td style="width:10%;" class="text-center">
							<?php echo $row_member["member_id"];?>
						</td>
					<tr>
					<tr>
						<td style="width:10%;" class="text-center">
							สังกัด
						</td>
						<td>
							<?php echo $row_member["level_name"];?>
						</td>
						<td style="width:10%;" class="text-center">
						</td>
						<td style="width:10%;" class="text-center">
						</td>
					<tr>
				</table>
				<div>&nbsp;</div>
				<div>&nbsp;</div>
				<table style="width: 100%;"  class="bordered">
					<tr>
						<th>เงินเดือน</th>
						<th>ระยะเวลาเป็นสมาชิก</th>
						<th>หุ้นงวดที่</th>
						<th>หุ้นชำระต่อเดือน</th>
						<th>ทุนเรือนหุ้น</th>
					</tr>
					<tr>
						<td class="text-right"><?php echo number_format($row_member["salary"],2);?></td>
						<td class="text-center"><?php
							$diff_date = $this->center_function->diff_date(@$row_member["member_date"], $row_loan['createdatetime']);
							echo $diff_date['year']." ปี ".$diff_date['month']."เดือน";
						?></td>
						<td class="text-right"><?php echo number_format($share_period,2);?></td>
						<td class="text-right"><?php echo number_format($share_month,2);?></td>
						<td class="text-right"><?php echo number_format($cal_share,2);?></td>
					</tr>
				</table>
				<div>&nbsp;</div>
				<div>&nbsp;</div>
				<?php
					if(!empty($guarantors)) {
				?>
				<table style="width: 100%;" class="no-border-b">
					<tr>
						<th class="text-left text-title" colspan="4">
							รายการค้ำประกัน
						</th>
					<tr>
				</table>
				<table style="width: 100%;" class="bordered">
					<tr>
						<th style="width:20%;" class="text-center text-title">
							เลขที่สมาชิก
						</th>
						<th style="width:20%;" class="text-center text-title">
							เลขที่สัญญา
						</th>
						<th class="text-center text-title">
							ชื่อสมาชิก
						</th>
					<tr>
					<?php
						foreach($guarantors as $guarantor) {
					?>
					<tr>
						<td class="text-center">
							<?php echo $guarantor["member_id"];?>
						</td>
						<td class="text-center">
							<?php echo $guarantor['prefix_code'].$guarantor["contract_number"];?>
						</td>
						<td class="text-left" style="padding-left: 100px;">
							<?php echo $guarantor["prename_full"].$guarantor["firstname_th"]." ".$guarantor["lastname_th"];?>
						</td>
					<tr>
					<?php
						}
					?>
				</table>
				<?php
					}
				?>
				<div>&nbsp;</div>
				<div>&nbsp;</div>
				<?php
					if(!empty($loans)) {
				?>
				<table style="width: 100%;" class="no-border-b">
					<tr>
						<th class="text-left" colspan="9">
							รายการสัญญาเงินกู้
						</th>
					<tr>
				</table>
				<table style="width: 100%;" class="bordered">
					<tr>
						<th class="text-center text-title">
							เลขที่สัญญา
						</th>
						<th class="text-center text-title">
							วันที่ทำสัญญา
						</th>
						<th class="text-center text-title">
							ชำระล่าสุด
						</th>
						<th class="text-center text-title">
							จำนวนเงินกู้
						</th>
						<th class="text-center text-title">
							เงินชำระต่องวด
						</th>
						<th class="text-center text-title">
							จำนวนงวด
						</th>
						<th class="text-center text-title">
							งวดที่ชำระ
						</th>
						<th class="text-center text-title">
							เงินกู้ชำระแล้ว
						</th>
						<th class="text-center text-title">
							เงินกู้คงเหลือ
						</th>
					<tr>
					<?php
						foreach($loans as $loan) {
					?>
					<tr>
						<td class="text-center">
							<?php echo $loan['prefix_code'].$loan["contract_number"];?>
						</td>
						<td class="text-center">
							<?php echo $this->center_function->ConvertToThaiDate(@$loan['approve_date'],0,0);?>
						</td>
						<td class="text-center">
							<?php echo $this->center_function->ConvertToThaiDate(@$loan['last_payment'],0,0);?>
						</td>
						<td class="text-center">
							<?php echo number_format($loan["loan_amount"],2);?>
						</td>
						<td class="text-center">
							<?php echo $loan["money_per_period"]?>
						</td>
						<td class="text-center">
							<?php echo $loan["period_amount"]?>
						</td>
						<td class="text-center">
							<?php echo $loan["period_now"];?>
						</td>
						<td class="text-center">
							<?php echo number_format($loan["loan_amount"] - $loan["balance"],2);?>
						</td>
						<td class="text-center">
							<?php echo number_format($loan["balance"],2);?>
						</td>
					<tr>
					<?php
							foreach($loan['guarantors'] as $guarantor) {
								?>
									<tr>
										<td class="text-center"><?=$guarantor["member_id"]?></td>
										<td colspan="8"><?=$guarantor["prename_full"].$guarantor["firstname_th"]." ".$guarantor["lastname_th"];?></td>
									</tr>
								<?php
							}
						}
					?>
				</table>	
				<?php
					}
				?>
				<div>&nbsp;</div>
				<div>&nbsp;</div>
				<table style="width: 100%;" class="no-border-b">
					<tr>
						<th class="text-right" colspan="9">
							สมาชิกผู้กู้
						</th>
					<tr>
				</table>
				<div>&nbsp;</div>
				<div>&nbsp;</div>
				<?php if(!empty($specomm)) { ?>
				<table style="width: 100%;" class="no-border">
					<tr>
						<th class="text-left" colspan="9">
							<?php echo !empty($specomm['note']) ? $specomm['note'] : "";?>
						</th>
					<tr>
				</table>
				<?php } ?>
			</div>
		</div>

		<!-- guarantor info -->
		<?php
			foreach($row_guarantee as $guarantee) {
		?>
		<div style="width: 1000px;">
			<div class="panel panel-body" style="padding-top:20px !important;height: 1400px;">
				<table style="width: 100%;">
					<tr>
						<td style="width:100px;vertical-align: top;">
							<img src="<?php echo base_url(PROJECTPATH.'/assets/images/coop_profile/'.$_SESSION['COOP_IMG']); ?>" alt="Logo" style="height: 80px;" />
						</td>
						<td>
							 <h3 class="title_view"><?php echo @$_SESSION['COOP_NAME'];?></h3>
							 <h3 class="title_view">รายการแจ้งหุ้นหนี้สมาชิก ประจำวันที่ <?php echo $this->center_function->ConvertToThaiDate(@$row_loan['createdatetime'],0,0);?></h3>
							<p>&nbsp;</p>	
						 </td>
					</tr> 
					<tr>
						<td colspan="3">
							<h3 class="title_view">
							</h3>
						</td>
					</tr> 
				</table>
				<div>&nbsp;</div>
				<table style="width: 100%;" class="no-border-b">
					<tr>
						<td style="width:10%;" class="text-center">
							ชื่อสมาชิก
						</td>
						<td>
							<?php echo $guarantee["prename_full"].$guarantee["firstname_th"]." ".$guarantee["lastname_th"];?>
						</td>
						<td style="width:10%;" class="text-center">
							เลขที่สมาชิก
						</td>
						<td style="width:10%;" class="text-center">
							<?php echo $guarantee["member_id"];?>
						</td>
					<tr>
					<tr>
						<td style="width:10%;" class="text-center">
							สังกัด
						</td>
						<td>
							<?php echo ($guarantee["mem_group_name"]!="" ? $guarantee["mem_group_name"] : "-")." ".($guarantee['mem_group_name_faction']!="" ? $guarantee['mem_group_name_faction'] : "-");?>
						</td>
						<td style="width:10%;" class="text-center">
						</td>
						<td style="width:10%;" class="text-center">
						</td>
					<tr>
				</table>
				<div>&nbsp;</div>
				<div>&nbsp;</div>
				<table style="width: 100%;"  class="bordered">
					<tr>
						<th>เงินเดือน</th>
						<th>ระยะเวลาเป็นสมาชิก</th>
						<th>หุ้นงวดที่</th>
						<th>หุ้นชำระต่อเดือน</th>
						<th>ทุนเรือนหุ้น</th>
					</tr>
					<tr>
						<td class="text-right"><?php echo number_format($guarantee["salary"],2);?></td>
						<td class="text-center"><?php
							$diff_date = $this->center_function->diff_date(@$guarantee["member_date"], $row_loan['createdatetime']);
							echo $diff_date['year']." ปี ".$diff_date['month']."เดือน";
						?></td>
						<td class="text-right"><?php echo number_format($guarantee['share_period'],2);?></td>
						<td class="text-right"><?php echo number_format($guarantee['share_month'],2);?></td>
						<td class="text-right"><?php echo number_format($guarantee['share_balance'],2);?></td>
					</tr>
				</table>

				<div>&nbsp;</div>
				<div>&nbsp;</div>
				<?php
					if(!empty($guarantee['guarantors'])) {
				?>
				<table style="width: 100%;" class="no-border-b">
					<tr>
						<th class="text-left text-title" colspan="4">
							รายการค้ำประกัน
						</th>
					<tr>
				</table>
				<table style="width: 100%;" class="bordered">
					<tr>
						<th style="width:20%;" class="text-center text-title">
							เลขที่สมาชิก
						</th>
						<th style="width:20%;" class="text-center text-title">
							เลขที่สัญญา
						</th>
						<th class="text-center text-title">
							ชื่อสมาชิก
						</th>
					<tr>
					<?php
						foreach($guarantee['guarantors'] as $guarantor) {
					?>
					<tr>
						<td class="text-center">
							<?php echo $guarantor["member_id"];?>
						</td>
						<td class="text-center">
							<?php echo $guarantor['prefix_code'].$guarantor["contract_number"];?>
						</td>
						<td class="text-left" style="padding-left: 100px;">
							<?php echo $guarantor["prename_full"].$guarantor["firstname_th"]." ".$guarantor["lastname_th"];?>
						</td>
					<tr>
					<?php
						}
					?>
				</table>
				<div>&nbsp;</div>
				<div>&nbsp;</div>
				<?php
					}
				?>
				<?php
					if(!empty($guarantee['loans'])) {
				?>
				<table style="width: 100%;" class="no-border-b">
					<tr>
						<th class="text-left" colspan="9">
							รายการสัญญาเงินกู้
						</th>
					<tr>
				</table>
				<table style="width: 100%;" class="bordered">
					<tr>
						<th class="text-center text-title">
							เลขที่สัญญา
						</th>
						<th class="text-center text-title">
							วันที่ทำสัญญา
						</th>
						<th class="text-center text-title">
							ชำระล่าสุด
						</th>
						<th class="text-center text-title">
							จำนวนเงินกู้
						</th>
						<th class="text-center text-title">
							เงินชำระต่องวด
						</th>
						<th class="text-center text-title">
							จำนวนงวด
						</th>
						<th class="text-center text-title">
							งวดที่ชำระ
						</th>
						<th class="text-center text-title">
							เงินกู้ชำระแล้ว
						</th>
						<th class="text-center text-title">
							เงินกู้คงเหลือ
						</th>
					<tr>
					<?php
						foreach($guarantee['loans'] as $loan) {
							if($loan["balance"]<= 0) continue;
					?>
					<tr>
						<td class="text-center" style="border-bottom: 1px solid black;">
							<?php echo $loan["prefix_code"].$loan["contract_number"];?>
						</td>
						<td class="text-center" style="border-bottom: 1px solid black;">
							<?php echo $this->center_function->ConvertToThaiDate(@$loan['approve_date'],0,0);?>
						</td>
						<td class="text-center" style="border-bottom: 1px solid black;">
							<?php echo $this->center_function->ConvertToThaiDate(@$loan['last_payment'],0,0);?>
						</td>
						<td class="text-center" style="border-bottom: 1px solid black;">
							<?php echo number_format($loan["loan_amount"],2);?>
						</td>
						<td class="text-center" style="border-bottom: 1px solid black;">
							<?php echo $loan["money_per_period"]?>
						</td>
						<td class="text-center" style="border-bottom: 1px solid black;">
							<?php echo $loan["period_amount"]?>
						</td>
						<td class="text-center" style="border-bottom: 1px solid black;">
							<?php echo $loan["period_now"];?>
						</td>
						<td class="text-center" style="border-bottom: 1px solid black;">
							<?php echo number_format($loan["loan_amount"] - $loan["balance"],2);?>
						</td>
						<td class="text-center" style="border-bottom: 1px solid black;">
							<?php echo number_format($loan["balance"],2);?>
						</td>
					<tr>
					<?php
							foreach($loan['guarantors'] as $guarantor) {
								?>
									<tr>
										<td class="text-center"><?=$guarantor["member_id"]?></td>
										<td colspan="8"><?=$guarantor["prename_full"].$guarantor["firstname_th"]." ".$guarantor["lastname_th"];?></td>
									</tr>
								<?php
							}

						}
					?>
				</table>	
				<?php
					}
				?>
				<div>&nbsp;</div>
				<div>&nbsp;</div>
				<table style="width: 100%;" class="no-border-b">
					<tr>
						<th class="text-right" colspan="9">
							สมาชิกผู้ค้ำประกันเงินกู้
						</th>
					<tr>
				</table>
				<div>&nbsp;</div>
				<div>&nbsp;</div>
				<?php if(!empty($guarantee['specomm'])) { ?>
				<table style="width: 100%;" class="no-border">
					<tr>
						<th class="text-left" colspan="9">
							<?php echo !empty($guarantee['specomm']['com_1']) ? $guarantee['specomm']['com_1'] : "";?>
						</th>
					<tr>
					<tr>
						<th class="text-left" colspan="9">
							<?php echo !empty($guarantee['specomm']['com_2']) ? $guarantee['specomm']['com_2'] : "";?>
						</th>
					<tr>
				</table>
				<?php } ?>
			</div>
		</div>
		<?php
			}
		?>
