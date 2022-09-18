<style>
	.table-view>thead, .table-view>thead>tr>td, .table-view>thead>tr>th {
		font-size: 16px;
	}
	.table-view-2>thead>tr>th{
	    border-top: 1px solid #000 !important;
		border-bottom: 1px solid #000 !important;
		font-size: 16px;
	}
	.table-view-2>tbody>tr>td{
	    border: 0px !important;
		/*font-family: upbean;
		font-size: 16px;*/
		font-family: Tahoma;
		font-size: 12px;
	}	
	.border-bottom{
	    border-bottom: 1px solid #000 !important;
		font-weight: bold;
	}
	
	.foot-border{
	    border-top: 1px solid #000 !important;
		border-bottom: double !important;
		font-weight: bold;
	}
	.table {
		color: #000;
	}
</style>		
<?php

if(@$_GET['start_date']){
	$start_date_arr = explode('/',@$_GET['start_date']);
	$start_day = $start_date_arr[0];
	$start_month = $start_date_arr[1];
	$start_year = $start_date_arr[2];
	$start_year -= 543;
	$start_date = $start_year.'-'.$start_month.'-'.$start_day;
}

if(@$_GET['end_date']){
	$end_date_arr = explode('/',@$_GET['end_date']);
	$end_day = $end_date_arr[0];
	$end_month = $end_date_arr[1];
	$end_year = $end_date_arr[2];
	$end_year -= 543;
	$end_date = $end_year.'-'.$end_month.'-'.$end_day;
}		
		
//class="page-break"
//
$last_runno = 0;
$all_withdrawal = 0;
$all_deposit = 0;
$all_balance = 0;

//echo '<pre>'; print_r($data); echo '</per>';		
if(!empty($data)){
	foreach(@$data AS $page=>$data_row){
	?>
		
		<div style="width: 1000px;" class="page-break">
			<div class="panel panel-body" style="padding-top:10px !important;min-height: 1200px;">
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
							 <h3 class="title_view">รายการสรุปการกู้ฉุกเฉิน ATM</h3>
							 
							 <h3 class="title_view">
								<?php 
									echo "ประจำวันที่ ".$this->center_function->ConvertToThaiDate($start_date);
								?>
							</h3>
						 </td>
						 <td style="width:100px;vertical-align: top;" class="text-right">
							<a class="no_print" onclick="window.print();"><button class="btn btn-perview btn-after-input" type="button"><span class="icon icon-print" aria-hidden="true"></span></button></a>
								<?php
								$get_param = '?';
								foreach(@$_GET as $key => $value){
									//if($key != 'month' && $key != 'year' && $value != ''){
										$get_param .= $key.'='.$value.'&';
									//}
								}
								$get_param = substr($get_param,0,-1);
							?>
							<a class="no_print"  target="_blank" href="<?php echo base_url('/report_deposit_data/coop_report_summary_emergent_atm_excel'.$get_param); ?>">
								<button class="btn btn-perview btn-after-input" type="button"><span class="icon icon icon-file-excel-o" aria-hidden="true"></span></button>
							</a>
						</td>
					</tr>  					
				<?php 
					}else{
				?>
					<tr>
						<td colspan="3" style="text-align: left;">&nbsp;</td>
					</tr>
				<?php
					} 
				?>
					
					<tr>
						<td colspan="3" style="text-align: left;">
							<span class="title_view">วันที่ <?php echo $this->center_function->ConvertToThaiDate(@date('Y-m-d'),1,0);?></span>				
							<span class="title_view">   เวลา <?php echo date('H:i:s');?></span>	
						</td>
					</tr> 
					<tr>
						<td colspan="3" style="text-align: left;">
							<span class="title_view">หน้าที่ <?php echo @$page.'/'.@$page_all;?></span><br>						
						</td>
					</tr>
				</table>
			
				<table class="table table-view table-center">
					<thead> 
						<tr>							
							<th style="width: 80px;vertical-align: middle;">เลขที่สมาชิก</th>
							<th style="width: 180px;vertical-align: middle;">ชื่อ-นามสกุล</th>
							<th style="width: 60px;vertical-align: middle;">รหัสสังกัด</th>
							<th style="width: 160px;vertical-align: middle;">หน่วยงานหลัก::หน่วยงานรอง</th>
							<th style="width: 160px;vertical-align: middle;">หน่วยงานย่อย</th>
							<th style="width: 80px;vertical-align: middle;">เลขที่สัญญา</th>							
							<th style="width: 80px;vertical-align: middle;">เงินคงเหลือ</th>
						</tr>  
					</thead>
					<tbody>
					
					<?php	
						$runno = $last_runno;
						if(!empty($data_row)){
							foreach(@$data_row as $key => $row){
								$runno++;
								$member_name =	@$row['prename_short'].@$row['firstname_th'].'  '.@$row['lastname_th'];
					?>
							<tr> 
							  <td style="text-align: center;vertical-align: top;"><?php echo @$row['member_id'];?></td>						 
							  <td style="text-align: left;vertical-align: top;"><?php echo @$member_name;?></td> 					 
							  <td style="text-align: left;vertical-align: top;"><?php echo @$row['mem_group_id'];?></td> 					 
							  <td style="text-align: left;vertical-align: top;"><?php echo @$row['main_name'].' :: '.@$row['sub_name'];?></td> 					 
							  <td style="text-align: left;vertical-align: top;"><?php echo @$row['mem_group_name'];?></td> 					 
							  <td style="text-align: center;vertical-align: top;"><?php echo @$row['contract_number'];?></td>	
							  <td style="text-align: right;vertical-align: top;"><?php echo number_format(@$row['loan_amount_balance'],2); ?></td> 					 
							</tr>										
					
					<?php									
							}
						}
						$last_runno = $runno;
					?> 						
					</tbody>    
				</table>
			</div>
		</div>
<?php 
	}
} 
?>