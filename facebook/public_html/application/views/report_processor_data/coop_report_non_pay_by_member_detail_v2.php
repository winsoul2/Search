<style>
	.table-view>thead, .table-view>thead>tr>td, .table-view>thead>tr>th {
		font-size: 14px;
	}
</style>		
<?php
//class="page-break"
if(!empty($row_group)){
	$page=1;
	foreach(@$row_group AS $key_group => $value_group){
		if(!empty($value_group['non_pay_data'])){
		$runno = 0;
		for($i=1;$i<=count($value_group['non_pay_data']);$i++){
	?>
		
		<div style="width: 950px;" class="page-break">
			<div class="panel panel-body" style="padding-top:10px !important;min-height: 1000px;">
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
							 <h3 class="title_view">รายงานเก็บรายเดือนไม่ครบ</h3>
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
							<a class="no_print"  target="_blank" href="<?php echo base_url('/report_processor_data/coop_report_non_pay_by_member_detail_excel'.$get_param); ?>">
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
							<th style="text-align:left;" colspan="9"><?php echo ":: ".$value_group['mem_group_name']; ?></th>
						</tr>
						<tr>
							<th style="vertical-align: middle;">ลำดับ</th>
							<th style="vertical-align: middle;">หน่วยงานย่อย</th>
							<th style="vertical-align: middle;">รูปแบบ</th>
							<th style="vertical-align: middle;">เลขที่สมาชิก</th>
							<th style="vertical-align: middle;">ชื่อ-นามสกุล</th>
							<th style="vertical-align: middle;">เงินเรียกเก็บ</th>
							<th style="vertical-align: middle;">เงินเก็บไม่ได้</th>
							<th style="vertical-align: middle;">เงินเก็บได้</th>
							<th style="vertical-align: middle;">สาเหตุที่เก็บไม่ได้</th>
						</tr>
					</thead>
					<tbody>
					
					<?php
						if(!empty($value_group['non_pay_data'][$i])){
							foreach(@$value_group['non_pay_data'][$i] as $key2 => $row){
								$runno++;
					?>
							<tr> 
								<td style="text-align: center;"><?php echo @$runno; ?></td>
								<td style="text-align: left;"><?php echo @$row['mem_group_name']; ?></td>
								<td style="text-align: left;"><?php echo (@$row['mem_type_id'] != '')?@$row['mem_type_name']:'ไม่ระบุ'; ?></td>
								<td style="text-align: center;"><?php echo @$row['member_id']; ?></td>
								<td style="text-align: left;"><?php echo @$row['member_name']; ?></td>
								<td style="text-align: right;"><?php echo number_format(@$row['pay_amount'],2); ?></td>
								<td style="text-align: right;"><?php echo number_format(@$row['non_pay_amount'],2); ?></td>
								<td style="text-align: right;"><?php echo number_format(@$row['balance'],2); ?></td>
								<td style="text-align: left;"><?php echo @$row['non_pay_reason']; ?></td>
							</tr>										
					
					<?php									
							}
					?>
							<tr> 
								<td style="text-align: center;" colspan="5">รวม <?php echo $value_group['mem_group_name'].":: ".count($value_group['non_pay_data'][$i])." รายการ"; ?></td>
								<td style="text-align: right;"><?php echo number_format(@$total_data[$value_group['id']]['pay_amount'],2); ?></td>
								<td style="text-align: right;"><?php echo number_format(@$total_data[$value_group['id']]['non_pay_amount'],2); ?></td>
								<td style="text-align: right;"><?php echo number_format(@$total_data[$value_group['id']]['balance'],2); ?></td>
								<td style="text-align: right;"></td>
							</tr>
					<?php		
						}
					?>
						
					<?php	
						if(@$page == @$page_all){							
					?>
							<tr class="foot-border"> 
								<td style="text-align: center;font-weight:bold;" colspan="5">รวมทั้งหมด</td>
								<td style="text-align: right;"><?php echo number_format(@$total_all_data['pay_amount'],2); ?></td>
								<td style="text-align: right;"><?php echo number_format(@$total_all_data['non_pay_amount'],2); ?></td>
								<td style="text-align: right;"><?php echo number_format(@$total_all_data['balance'],2); ?></td>
								<td style="text-align: right;"></td>
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