<style>
	.table-view>thead, .table-view>thead>tr>td, .table-view>thead>tr>th {
		font-size: 14px;
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

$last_runno = 0;

$all_income = 0;
$all_receive = 0;
$all_pay = 0;
$all_increase_decrease = 0;
$all_balance = 0;

if(!empty($data)){
	foreach(@$data AS $page=>$data_row){
		//class="page-break"
	?>
		
		<div style="width: 1000px;" >
			<div class="panel panel-body" style="padding-top:10px !important;min-height: 1400px;">
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
							 <h3 class="title_view">รายงานรายรับ-จ่ายระบบเงินฝาก</h3>
							 <h3 class="title_view">
								<?php 
									echo " วันที่ ".$this->center_function->ConvertToThaiDate($start_date);
								?>
							</h3>
						 </td>
						 <td style="width:100px;vertical-align: top;" class="text-right">
							<a class="no_print" onclick="window.print();"><button class="btn btn-perview btn-after-input" type="button"><span class="icon icon-print" aria-hidden="true"></span></button></a>
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
			
				<table class="table table-view-2 table-center">
					<thead> 
						<tr>
							<th style="width: 200px;vertical-align: middle;">ประเภทบัญชีเงินฝาก</th>
							<th style="width: 100px;vertical-align: middle;text-align: right;">ยอดยกมา</th>
							<th style="width: 100px;vertical-align: middle;text-align: right;">รับ</th>
							<th style="width: 100px;vertical-align: middle;text-align: right;">จ่าย</th>
							<th style="width: 100px;vertical-align: middle;text-align: right;">เพิ่ม/ลด</th>
							<th style="width: 100px;vertical-align: middle;text-align: right;">ยอดคงเหลือ</th>
						</tr>  
					</thead>
					<tbody>
					
					<?php	
						$total_income = 0;
						$total_receive = 0;
						$total_pay = 0;
						$total_increase_decrease = 0;
						$total_balance = 0;						
						if(!empty($data_row)){
							foreach(@$data_row as $key => $row){	
					?>
							<tr> 
							  <td style="text-align: left;"><?php echo @$row['type_name'];?></td>
							  <td style="text-align: right;"><?php echo number_format(@$row['income'],2); ?></td> 					 
							  <td style="text-align: right;"><?php echo number_format(@$row['receive'],2); ?></td> 					 
							  <td style="text-align: right;"><?php echo number_format(@$row['pay'],2); ?></td> 					 
							  <td style="text-align: right;"><?php echo number_format(@$row['increase_decrease'],2); ?></td> 					 
							  <td style="text-align: right;"><?php echo number_format(@$row['balance'],2); ?></td> 						 
							</tr>										
					
					<?php		
								$total_income += @$row['income'];
								$total_receive += @$row['receive'];
								$total_pay += @$row['pay'];
								$total_increase_decrease += @$row['increase_decrease'];
								$total_balance += @$row['balance'];
							}
				
						}
						
						$all_income += @$total_income;
						$all_receive += @$total_receive;
						$all_pay += @$total_pay;
						$all_increase_decrease += @$total_increase_decrease;
						$all_balance += @$total_balance;
						
						if(@$page == @$page_all){							
					?>
						   <tr class="foot-border"> 
							  <td style="text-align: center;">รวม</td>				 
							  <td style="text-align: right;"><?php echo number_format(@$all_income,2); ?></td> 						 
							  <td style="text-align: right;"><?php echo number_format(@$all_receive,2); ?></td> 						 
							  <td style="text-align: right;"><?php echo number_format(@$all_pay,2); ?></td> 					 
							  <td style="text-align: right;"><?php echo number_format(@$all_increase_decrease,2); ?></td> 					 
							  <td style="text-align: right;"><?php echo number_format(@$all_balance,2); ?></td> 					 
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