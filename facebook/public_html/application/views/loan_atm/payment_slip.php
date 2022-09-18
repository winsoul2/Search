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
		font-size: 30px;		
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
	.normal_text{font-size: 30px;}
	.normal_value{font-size: 25px;}
	.border-bottom{border-bottom: 1px solid #75758a;}
	td{
		padding-top:15px;
		padding-bottom:15px;
	}
	.text-center{
		text-align:center;
	}
	.text-right{
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
				<div style=" height: 1350px;">
					<table style="width: 95%;" border=0>
						<tr>
							<td class="normal_text text-center m-b-3" colspan="4">
								ใบนำจ่ายเงินกู้ฉุกเฉิน ATM
							</td>
						</tr>
						<tr>
							<td class="normal_text" width="50%" colspan="2">
								สัญญาเงินกู้ฉุกเฉิน ATM เลขที่  <?php echo $data['contract_number']; ?>
							</td>
							<td class="normal_text text-right" width="15%">
								วันที่ 
							</td>
							<td class="normal_value" style="padding-left:10px">
								<?php echo $this->center_function->ConvertToThaiDate($data['loan_date']); ?>
							</td>
						</tr>
						<tr>
							<td class="normal_text" width="15%">
								ชื่อสมาชิก
							</td>
							<td class="normal_value">
								 <?php echo $data['prename_short'].$data['firstname_th']." ".$data['lastname_th']; ?>
							</td>
							<td class="normal_text text-right">
								เลขที่สมาชิก 
							</td>
							<td class="normal_value" style="padding-left:10px">
								 <?php echo $data['member_id']; ?>
							</td>
						</tr>
						<tr>
							<td class="normal_text">
								สังกัด
							</td>
							<td class="normal_value">
								 <?php echo $data['department_name']." ".$data['faction_name']; ?>
							</td>
							<td class="normal_text text-right">
								กลุ่มย่อย 
							</td>
							<td class="normal_value" style="padding-left:10px">
								 <?php echo $data['level_name']; ?>
							</td>
						</tr>
						<tr>
							<td class="normal_text">
								วงเงินกู้ 
							</td>
							<td class="normal_value" colspan="3">
								<?php echo number_format($data['total_amount_approve']); ?> 
								(<?php echo $this->center_function->convert($data['total_amount_approve']); ?>)
							</td>
						</tr>
						<tr>
							<td class="normal_text" colspan="4">
								ถอนเงินกู้ฉุกเฉิน ATM จำนวน <span class="normal_value"><?php echo number_format($data['loan_amount']); ?></span>
								( <span class="normal_value"><?php echo $this->center_function->convert($data['loan_amount']); ?></span>)
							</td>
						</tr>
						<!--tr>
							<td class="normal_text" colspan="4">
								เงินสด/โอนไปยัง ธนาคารกรุงไทย เลขที่บัญชี 
								<span class="normal_value"><?php echo $data['dividend_acc_num']; ?></span>
							</td>
						</tr-->
						<tr>
							<td class="normal_text">
								จำนวนเงิน 
							</td>
							<td class="normal_value" colspan="3">
								 <?php echo number_format($data['loan_amount']); ?> 
								(<?php echo $this->center_function->convert($data['loan_amount']); ?>)
							</td>
						</tr>
						<?php 
						$sign_space = "";
						for($i=0;$i<=80;$i++){
							$sign_space .= "&nbsp;";
						}
						$sign_space2 = "";
						for($i=0;$i<=50;$i++){
							$sign_space2 .= "&nbsp;";
						}
						?>
						<tr>
							<td class="normal_text text-center" colspan="2">
								ลงชื่อ<span class="border-bottom"><?php echo $sign_space; ?></span>ผู้รับเงิน
							</td>
							<td class="normal_text text-center" colspan="2">
								ลงชื่อ<span class="border-bottom"><?php echo $sign_space; ?></span>ผู้จ่ายเงิน
							</td>
						</tr>
						<tr>
							<td class="normal_text text-center" colspan="2">
								(<?php echo $sign_space2; ?>)
							</td>
							<td class="normal_text text-center" colspan="2">
								(<?php echo $sign_space2; ?>)
							</td>
						</tr>
					</table>
				</div>	
			</div>
		</div>