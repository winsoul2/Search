<style>
	.table-view>thead, .table-view>thead>tr>td, .table-view>thead>tr>th {
		font-size: 14px;
	}
	@page { size: landscape; }
</style>		
<?php
//class="page-break"
$member_id_check = 'x';
if(!empty($row_group)){
	$page=1;
	foreach(@$row_group AS $key_group => $value_group){
		if(!empty($value_group['non_pay_data'])){
		$runno = 0;
		for($i=1;$i<=count($value_group['non_pay_data']);$i++){
	?>
		
		<div style="width: 1500px;"  class="page-break">
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
							 <h3 class="title_view">รายงานเก็บได้บางส่วน (รายละเอียดรายบุคคล)</h3>
							 <h3 class="title_view">
								ประจำเดือน :: <?php echo $month_text; ?> พ.ศ. :: <?php echo $year; ?>
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
							<a class="no_print"  target="_blank" href="<?php echo base_url('/report_processor_pay_some_data/coop_report_pay_some_by_member_detail_excel'.$get_param); ?>">
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
							<th colspan="19" style="text-align:left;"><?php echo $value_group['department_name']."::".$value_group['faction_name']."::".$value_group['mem_group_name']; ?></th>
						</tr>
						<tr>
							<th style="width: 40px;vertical-align: middle;" rowspan="2">ลำดับ</th>
							<th style="width: 70px;vertical-align: middle;" rowspan="2">เลขที่สมาชิก</th>
							<th style="width: 100px;vertical-align: middle;" rowspan="2">ชื่อ-นามสกุล</th>
							<th style="width: 80px;vertical-align: middle;" rowspan="2">ค่าธรรมเนียมแรกเข้า</th>
							<?php foreach($loan_type as $key => $value){ ?>
								<th style="width: 100px;vertical-align: middle;" colspan="3"><?php echo $value['loan_type']; ?></th>
							<?php } ?>
							<th style="width: 90px;vertical-align: middle;" rowspan="2">หุ้น</th>
							<th style="width: 90px;vertical-align: middle;" rowspan="2">เงินฝาก</th>
							<th style="width: 90px;vertical-align: middle;" rowspan="2">ณสอ สป</th>
							<th style="width: 90px;vertical-align: middle;" rowspan="2">อื่นๆ</th>
							<th style="width: 90px;vertical-align: middle;" rowspan="2">ชำระหนี้ค้ำประกัน</th>
							<th style="width: 90px;vertical-align: middle;" rowspan="2">รวม</th>
						</tr>
						<tr>
							<?php foreach($loan_type as $key => $value){ ?>
								<th style="width: 70px;vertical-align: middle;">เลขที่สัญญา</th>
								<th style="width: 90px;vertical-align: middle;">เงินต้น</th>
								<th style="width: 90px;vertical-align: middle;">ดอกเบี้ย</th>
							<?php } ?>
						</tr>
					</thead>
					<tbody>

					<?php	
						foreach(@$value_group['non_pay_data'][$i] as $key3 => $row_parent){
							foreach(@$row_parent as $key2 => $row){
								$runno++;
					?>
							<tr> 
								<td style="text-align: center;"><?php echo @$runno; ?></td>
								<td style="text-align: center;"><?php echo @$row['member_id']; ?></td>
								<td style="text-align: left;"><?php echo @$row['member_name']; ?></td>
							<?php
								if($member_id_check != $row['member_id']) {
							?>
								<td style="text-align: right;"><?php echo number_format(@$row['REGISTER_FEE'],2); ?></td>
							<?php
								} else {
							?>
								<td style="text-align: right;"></td>
							<?php
								}
							?>

							<?php
								if(!empty($row['emergent']['contract_number'])) {
							?>
								<td style="text-align: center;"><?php echo $row['emergent']['contract_number']; ?></td>
								<td style="text-align: right;"><?php echo number_format(@$row['emergent']['principal'],2); ?></td>
								<td style="text-align: right;"><?php echo number_format(@$row['emergent']['interest'],2); ?></td>
							<?php
								} else {
							?>
								<td style="text-align: right;"></td>
								<td style="text-align: right;"></td>
								<td style="text-align: right;"></td>
							<?php
								}
								if(!empty($row['normal']['contract_number'])) {
							?>
								<td style="text-align: center;"><?php echo $row['normal']['contract_number']; ?></td>
								<td style="text-align: right;"><?php echo number_format(@$row['normal']['principal'],2); ?></td>
								<td style="text-align: right;"><?php echo number_format(@$row['normal']['interest'],2); ?></td>
								<?php
								} else {
							?>
								<td style="text-align: right;"></td>
								<td style="text-align: right;"></td>
								<td style="text-align: right;"></td>
							<?php
								}
								if(!empty($row['special']['contract_number'])) {
							?>
								<td style="text-align: center;"><?php echo $row['special']['contract_number']; ?></td>
								<td style="text-align: right;"><?php echo number_format(@$row['special']['principal'],2); ?></td>
								<td style="text-align: right;"><?php echo number_format(@$row['special']['interest'],2); ?></td>
							<?php
							 	} else {
							?>
								<td style="text-align: right;"></td>
								<td style="text-align: right;"></td>
								<td style="text-align: right;"></td>
							<?php
								}
								if($member_id_check != $row['member_id']) {
							?>
								<td style="text-align: right;"><?php echo number_format(@$row['SHARE'],2); ?></td>
								<td style="text-align: right;"><?php echo number_format(@$row['DEPOSIT'],2); ?></td>
								<td style="text-align: right;"><?php echo number_format(@$row['CREMATION'],2); ?></td>
								<td style="text-align: right;"><?php echo number_format(@$row['OTHER'],2); ?></td>
								<td style="text-align: right;"><?php echo number_format(@$row['GUARANTEE_AMOUNT'],2); ?></td>
								<td style="text-align: right;"><?php echo number_format(@$row['total'],2); ?></td>
							<?php
								} else {
							?>
								<td style="text-align: right;"></td>
								<td style="text-align: right;"></td>
								<td style="text-align: right;"></td>
								<td style="text-align: right;"></td>
								<td style="text-align: right;"></td>
							<?php
								}
							?>
							</tr>
					<?php	
								$member_id_check = $row['member_id'];
							}
							if ($runno == $key_mem_counts[$value_group['id']]) {
					?>
							<tr>
								<td style="text-align: center;" colspan="3">รวมทั้งสิ้น</td>
								<td style="text-align: right;"><?php echo number_format(@$total_data[$value_group['id']]['REGISTER_FEE'],2); ?></td>
								<td style="text-align: right;"></td>
								<td style="text-align: right;"><?php echo number_format(@$total_data[$value_group['id']]['LOAN']['emergent']['principal'],2); ?></td>
								<td style="text-align: right;"><?php echo number_format(@$total_data[$value_group['id']]['LOAN']['emergent']['interest'],2); ?></td>
								<td style="text-align: right;"></td>
								<td style="text-align: right;"><?php echo number_format(@$total_data[$value_group['id']]['LOAN']['normal']['principal'],2); ?></td>
								<td style="text-align: right;"><?php echo number_format(@$total_data[$value_group['id']]['LOAN']['normal']['interest'],2); ?></td>
								<td style="text-align: right;"></td>
								<td style="text-align: right;"><?php echo number_format(@$total_data[$value_group['id']]['LOAN']['special']['principal'],2); ?></td>
								<td style="text-align: right;"><?php echo number_format(@$total_data[$value_group['id']]['LOAN']['special']['interest'],2); ?></td>
								<td style="text-align: right;"><?php echo number_format(@$total_data[$value_group['id']]['SHARE'],2); ?></td>
								<td style="text-align: right;"><?php echo number_format(@$total_data[$value_group['id']]['DEPOSIT'],2); ?></td>
								<td style="text-align: right;"><?php echo number_format(@$total_data[$value_group['id']]['CREMATION'],2); ?></td>
								<td style="text-align: right;"><?php echo number_format(@$total_data[$value_group['id']]['OTHER'],2); ?></td>
								<td style="text-align: right;"><?php echo number_format(@$total_data[$value_group['id']]['GUARANTEE_AMOUNT'],2); ?></td>
								<td style="text-align: right;"><?php echo number_format(@$total_data[$value_group['id']]['total'],2); ?></td>
							</tr>
					<?php
							}
						}
					?>

					<?php	
						if(@$page == @$page_all){							
					?>
							<tr class="foot-border"> 
								<td style="text-align: center;font-weight:bold;" colspan="3">รวมทั้งหมด</td>
								<td style="text-align: right;"><?php echo number_format(@$total_all_data['REGISTER_FEE'],2); ?></td>
								<td style="text-align: right;"></td>
								<td style="text-align: right;"><?php echo number_format(@$total_all_data['LOAN']['emergent']['principal'],2); ?></td>
								<td style="text-align: right;"><?php echo number_format(@$total_all_data['LOAN']['emergent']['interest'],2); ?></td>
								<td style="text-align: right;"></td>
								<td style="text-align: right;"><?php echo number_format(@$total_all_data['LOAN']['normal']['principal'],2); ?></td>
								<td style="text-align: right;"><?php echo number_format(@$total_all_data['LOAN']['normal']['interest'],2); ?></td>
								<td style="text-align: right;"></td>
								<td style="text-align: right;"><?php echo number_format(@$total_all_data['LOAN']['special']['principal'],2); ?></td>
								<td style="text-align: right;"><?php echo number_format(@$total_all_data['LOAN']['special']['interest'],2); ?></td>
								<td style="text-align: right;"><?php echo number_format(@$total_all_data['SHARE'],2); ?></td>
								<td style="text-align: right;"><?php echo number_format(@$total_all_data['DEPOSIT'],2); ?></td>
								<td style="text-align: right;"><?php echo number_format(@$total_all_data['CREMATION'],2); ?></td>
								<td style="text-align: right;"><?php echo number_format(@$total_all_data['OTHER'],2); ?></td>
								<td style="text-align: right;"><?php echo number_format(@$total_all_data['GUARANTEE_AMOUNT'],2); ?></td>
								<td style="text-align: right;"><?php echo number_format(@$total_all_data['total'],2); ?></td>
							</tr>
					<?php } ?>	  
						
					</tbody>    
				</table>
			</div>
		</div>
<?php
	$page++;
		}	
		}
	}
} 
?>