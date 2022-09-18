<style>
	.table-view>thead, .table-view>thead>tr>td, .table-view>thead>tr>th {
		font-size: 14px;
	}
	@page { size: landscape; }
</style>		
<?php
//class="page-break"
if(!empty($data)){
	foreach(@$data AS $page=>$data_row){
	?>
		
		<div style="width: 1500px;" >
			<div class="panel panel-body" style="padding-top:10px !important;min-height: 950px;">
				<table style="width: 100%;">
				<?php 
					
					if(@$page == 1){
				?>	
					<tr>
						<td style="width:100px;vertical-align: top;">
							
						</td>
						<td class="text-center">
							 <img src="<?php echo base_url(PROJECTPATH.'/assets/images/coop_profile/'.$_SESSION['COOP_IMG']); ?>" alt="Logo" style="height: 80px;" />	
							 <h3 class="title_view"><?php echo @$_SESSION['COOP_NAME'];?></h3>
							 <h3 class="title_view">รายงานเก็บไม่ได้ (แยกตามหน่วยงาน)</h3>
							 <h3 class="title_view">
								ตามประเภทเงินกู้หลัก ประจำเดือน :: <?php echo $month_text; ?> พ.ศ. :: <?php echo $year; ?>
							</h3>
						 </td>
						 <td style="width:100px;vertical-align: top;" class="text-right">
							<a class="no_print" onclick="window.print();"><button class="btn btn-perview btn-after-input" type="button"><span class="icon icon-print" aria-hidden="true"></span></button></a>
							<?php
								$get_param = '?';
								foreach(@$_POST as $key => $value){
									if($key != 'mem_type'){
										$get_param .= $key.'='.$value.'&';
									}
									
									if($key == 'mem_type'){
										foreach($value as $key2 => $value2){
											$get_param .= $key.'[]='.$value2.'&';
										}
									}	
								}
								$get_param = substr($get_param,0,-1);
								
							?>
							<a class="no_print"  target="_blank" href="<?php echo base_url('/report_processor_data/coop_report_non_pay_by_department_excel'.$get_param); ?>">
								<button class="btn btn-perview btn-after-input" type="button"><span class="icon icon icon-file-excel-o" aria-hidden="true"></span></button>
							</a>
						</td>
					</tr>  					
				<?php } ?>
					<tr>
						<td colspan="3" style="text-align: right;">
							<span class="title_view">หน้าที่ <?php echo @$page.'/'.@$page_all;?></span><br>						
						</td>
					</tr> 
					<tr>
						<td colspan="3" style="text-align: right;">
							<span class="title_view">วันที่ <?php echo $this->center_function->ConvertToThaiDate(@date('Y-m-d'),1,0);?></span>				
						</td>
					</tr> 
					<tr>
						<td colspan="3" style="text-align: right;">
							<span class="title_view">เวลา <?php echo date('H:i:s');?></span>				
						</td>
					</tr>  
					<tr>
						<td colspan="3" style="text-align: right;">
							<span class="title_view">ผู้ทำรายการ <?php echo $_SESSION['USER_NAME'];?></span>
						</td>
					</tr>
				</table>
			
				<table class="table table-view table-center">
					<thead> 
						<tr>
							<th style="width: 40px;vertical-align: middle;" rowspan="2">ลำดับ</th>
							<th style="width: 100px;vertical-align: middle;" rowspan="2">หน่วยงาน</th>
							<th style="width: 80px;vertical-align: middle;" rowspan="2">ค่าธรรมเนียมแรกเข้า</th>
							<?php foreach($loan_type as $key => $value){ ?>
								<th style="width: 100px;vertical-align: middle;" colspan="2"><?php echo $value['loan_type']; ?></th>
							<?php } ?>
							<th style="width: 100px;vertical-align: middle;" rowspan="2">หุ้น</th>
							<th style="width: 100px;vertical-align: middle;" rowspan="2">เงินฝาก</th>
							<th style="width: 100px;vertical-align: middle;" rowspan="2">ณสอ สป</th>
							<th style="width: 100px;vertical-align: middle;" rowspan="2">อื่นๆ</th>
							<th style="width: 100px;vertical-align: middle;" rowspan="2">ชำระหนี้ค้ำประกัน</th>
							<th style="width: 100px;vertical-align: middle;" rowspan="2">รวม</th>
						</tr>  
						<tr>
							<?php foreach($loan_type as $key => $value){ ?>
								<th style="width: 100px;vertical-align: middle;">เงินต้น</th>
								<th style="width: 100px;vertical-align: middle;">ดอกเบี้ย</th>
							<?php } ?>
						</tr>  
					</thead>
					<tbody>
					
					<?php	
						$runno = $last_runno;
						if($_GET['dev']=='dev'){
							echo '<pre>'; print_r(@$data_row); echo '</pre>';
						}
						if(!empty($data_row)){
							foreach(@$data_row as $key => $row){
								$runno++;
					?>
							<tr> 
								<td style="text-align: center;"><?php echo @$runno; ?></td>
								<td style="text-align: center;"><?php echo $row['mem_group_name']; ?></td>
								<td style="text-align: right;"><?=number_format(@$row['non_pay_data']['REGISTER_FEE'],2)?></td>						 
								<?php foreach($loan_type as $key_loan_type => $value_loan_type){
									if($value_loan_type['loan_type_code'] == 'emergent'){
										$principal = $row['non_pay_data']['LOAN'][$value_loan_type['id']]['principal'] + $row['non_pay_data']['ATM']['principal'];
										$interest = $row['non_pay_data']['LOAN'][$value_loan_type['id']]['interest'] + $row['non_pay_data']['ATM']['interest'];
									}else{
										$principal = $row['non_pay_data']['LOAN'][$value_loan_type['id']]['principal'];
										$interest = $row['non_pay_data']['LOAN'][$value_loan_type['id']]['interest'];
									}
								?>
									<td style="text-align: right;"><?php echo number_format($principal,2); ?></td>
									<td style="text-align: right;"><?php echo number_format($interest,2); ?></td>
								<?php } ?>								
								<td style="text-align: center;"><?php echo number_format($row['non_pay_data']['SHARE'],2); ?></td> 		
								<td style="text-align: center;"><?php echo number_format($row['non_pay_data']['DEPOSIT'],2); ?></td> 		
								<td style="text-align: center;"><?php echo number_format($row['non_pay_data']['CREMATION'],2); ?></td> 	
								<td style="text-align: center;"><?php echo number_format($row['non_pay_data']['OTHER'],2); ?></td> 		
								<td style="text-align: center;"><?php echo number_format($row['non_pay_data']['GUARANTEE_AMOUNT'],2); ?></td> 		
								<td style="text-align: center;"><?php echo number_format($row['non_pay_data']['total'],2); ?></td> 									  
							</tr>										
					
					<?php									
							}
							$last_runno = $runno;
						}
					?>
						
					<?php	
						if(@$page == @$page_all){							
					?>
						   <tr class="foot-border"> 
								<td style="text-align: center;" colspan="2">รวมทั้งสิ้น</td>
								<td style="text-align: right;"><?php echo number_format($total_data['REGISTER_FEE'],2); ?></td>						 
								<?php foreach($loan_type as $key_loan_type => $value_loan_type){
									if($value_loan_type['loan_type_code'] == 'emergent'){
										$principal = $total_data['LOAN'][$value_loan_type['id']]['principal'] + $total_data['ATM']['principal'];
										$interest = $total_data['LOAN'][$value_loan_type['id']]['interest'] + $total_data['ATM']['interest'];
									}else{
										$principal = $total_data['LOAN'][$value_loan_type['id']]['principal'];
										$interest = $total_data['LOAN'][$value_loan_type['id']]['interest'];
									}
								?>
									<td style="text-align: right;"><?php echo number_format($principal,2); ?></td>
									<td style="text-align: right;"><?php echo number_format($interest,2); ?></td>
								<?php } ?>								
								<td style="text-align: center;"><?php echo number_format($total_data['SHARE'],2); ?></td> 		
								<td style="text-align: center;"><?php echo number_format($total_data['DEPOSIT'],2); ?></td> 		
								<td style="text-align: center;"><?php echo number_format($total_data['CREMATION'],2); ?></td> 		
								<td style="text-align: center;"><?php echo number_format($total_data['OTHER'],2); ?></td> 	
								<td style="text-align: center;"><?php echo number_format($total_data['GUARANTEE_AMOUNT'],2); ?></td> 	
								<td style="text-align: center;"><?php echo number_format($total_data['total'],2); ?></td>							  
						  </tr>
					<?php } ?>	  
						
					</tbody>    
				</table>
			</div>
		</div>
<?php 
	}
} 
?>