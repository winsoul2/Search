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
	
	.border-bottom-dotted{border-bottom: 1px dotted #75758a;}
	.border-bottom-dotted-red{border-bottom: 1px dotted red;}
	
	
	table {
		border-collapse: initial;
		border-spacing: 0;
	}

	.bordered {
		border: solid #333333 1px;
		-moz-border-radius: 6px;
		-webkit-border-radius: 6px;
		border-radius: 6px;    
		width: 100%;
		font-family: upbean;
		font-size: 15px;
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
		font-size: 25px;		
		font-weight: bold;
	}
	
	.text-title{    
		font-size: 16px;
		font-weight: bold;
		margin-top: 5px;
	}
	
	.text-red{color:red;}
	
	@media print {	
		.text-red{color:red !important;}
	}
	
	div{font-family: upbean;font-size: 15px;}
	.normal_text{font-size: 25px;}
	
	.border-bottom{border-bottom: 1px solid #75758a;}
	td{
		padding-top:6px;
	}
	.text-center{
		text-align:center;
	}
</style>	
<?php $paragraph = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'; ?>
		<div style="width: 1000px;" class="page-break">
			<div class="panel panel-body" style="padding-top:10px !important; height: 1420px;">
				<table style="width: 100%;">
					<tr>						
						<td style="vertical-align: top;" class="text-right">
							<a class="no_print" onclick="window.print();"><button class="btn btn-perview btn-after-input" type="button"><span class="icon icon-print" aria-hidden="true"></span></button></a>
						</td>
					</tr> 
				</table>
				<div style="border:1px solid;height: 1350px;">
					<table style="width: 95%;" border=0>
						<tr>
							<td class="normal_text text-center" colspan="2">
								ชื่อสมาชิก <?php echo $loan_data['prename_short'].$loan_data['firstname_th']." ".$loan_data['lastname_th']; ?>
							</td>
						</tr>
						<tr>
							<td class="normal_text" width="50%">
								ยอดกู้
							</td>
							<td class="normal_text border-bottom text-center">
								<?php echo number_format($loan_data['loan_amount'],2); ?>
							</td>
						</tr>
						<tr>
							<td class="normal_text text-center" style="text-decoration: underline;">
								รายการหัก
							</td>
							<td class="normal_text text-center">
								<?php echo @number_format(@$loan_deduct['deduct_pay_prev_loan'],2);?>
							</td>
						</tr>
						<tr>
							<td class="font-bold" style="text-align:left;" colspan="2">
								<?php echo $paragraph; ?>ผู้กู้สามารถเลือกหักเข้าทุนเรือนหุ้น (1.) หรือเงินฝากเล่มสีน้ำเงิน (2.) ได้อย่างใดอย่างหนึ่ง<br>หมายเหตุเลือกแล้วไม่สามารถเปลี่ยนแปลงได้
							</td>
						</tr>
						<?php 
						$i=1;
						foreach($loan_deduct_list as $key => $value){ ?>
						<tr>
							<td class="normal_text">
								<?php 
									echo $paragraph; 
									echo $i++; 
									echo ". ".$value['loan_deduct_list']; 
								?>
							</td>
							<td class="normal_text border-bottom text-center">
								<?php echo @$loan_deduct[$value['loan_deduct_list_code']]!=''?number_format($loan_deduct[$value['loan_deduct_list_code']],2):'-';?>
							</td>
						</tr>
						<?php } ?>
						<tr>
							<td class="normal_text"></td>
							<td class="normal_text"></td>
						</tr>
						<tr>
							<td class="normal_text">
								ผ่อนชำระต่อเดือน
							</td>
							<td class="normal_text border-bottom text-center">
								<?php echo @$loan_deduct_profile['pay_per_month']!=''?number_format($loan_deduct_profile['pay_per_month'],2):'-'; ?>
							</td>
						</tr>
						<tr>
							<td class="normal_text">
								<?php echo $paragraph; ?>รูปแบบการผ่อนชำระ 
							</td>
							<td class="normal_text text-center" >
								<?php if($loan_data['pay_type']=='1'){ 
									echo "สหกรณ์";
								}else{
									echo "ธนาคาร";
								} ?>
							</td>
						</tr>
						<tr>
							<td class="normal_text">
								ยอดเงินที่จะได้รับโดยประมาณ
							</td>
							<td class="normal_text border-bottom text-center">
								<?php echo number_format($loan_deduct_profile['estimate_receive_money'],2); ?>
							</td>
						</tr>
						<tr>
							<td class="normal_text">
								ได้รับเงินประมาณ วันที่
							</td>
							<td class="normal_text border-bottom text-center">
								<?php echo $this->center_function->ConvertToThaiDate($loan_deduct_profile['date_receive_money']); ?>
							</td>
						</tr>
						<tr>
							<td class="normal_text">
								สัญญาเงินกู้นี้ ต้องชำระหนี้งวดแรกในเดือน
							</td>
							<td class="normal_text border-bottom text-center">
								<?php 
								$date_period = explode(' ',$this->center_function->ConvertToThaiDate($loan_deduct_profile['date_first_period']));
									echo $date_period[0]." ".$date_period[1]." ".$date_period[2]; 
								?>
							</td>
						</tr>
						<tr>
							<td class="normal_text">
								ดอกเบี้ยในการชำระงวดแรก
							</td>
							<td class="normal_text border-bottom text-center">
								<?php echo number_format($loan_deduct_profile['first_interest'],2); ?>
							</td>
						</tr>
						<tr>
							<td class="normal_text">
								ต้องทำหลักประกันผู้ค้ำประกันกรณี
							</td>
							<td class="normal_text border-bottom text-center">
								<?php echo $loan_deduct_profile['event_guarantee']!=''?$loan_deduct_profile['event_guarantee']:'-'; ?>
							</td>
						</tr>
						<tr height="300px">
							<td class="normal_text" style="vertical-align:bottom" >
								รับทราบ..................................................................................
							</td>
							<td class="normal_text text-center" style="vertical-align:bottom">
							<span class="normal_text" style="float:left">(</span>
								<?php echo $loan_data['prename_short'].$loan_data['firstname_th']." ".$loan_data['lastname_th']; ?>
							<span class="normal_text" style="float:right">)</span>
							</td>
						</tr>
					</table>
				</div>	
			</div>
		</div>