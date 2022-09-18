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
	@media print {
		.pagination {
			display: none;
		}
	}
</style>		
<?php
$month_arr = array('1'=>'มกราคม','2'=>'กุมภาพันธ์','3'=>'มีนาคม','4'=>'เมษายน','5'=>'พฤษภาคม','6'=>'มิถุนายน','7'=>'กรกฎาคม','8'=>'สิงหาคม','9'=>'กันยายน','10'=>'ตุลาคม','11'=>'พฤศจิกายน','12'=>'ธันวาคม');
		
//class="page-break"
//
$last_runno = 0;
$all_withdrawal = 0;
$all_deposit = 0;
$all_balance = 0;
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
							<h3 class="title_view">รายงานการ<?php echo $_GET["pickup_type"] == "0" ? "รับพัสดุ" : "เบิกพัสดุ" ?></h3>
							<h3 class="title_view"><?php echo $month_arr[$_GET["month"]]." ".$_GET["year"];?></h3>
							
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
							<a class="no_print" target="_blank" href="<?php echo base_url(PROJECTPATH.'/report_facility/pickup_excel'.$get_param); ?>">
								<button class="btn btn-perview btn-after-input" type="button"><span class="icon icon icon-file-excel-o" aria-hidden="true"></span></button>
							</a>
						</td>
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
							<th style="vertical-align: middle;">ลำดับ</th>
							<th style="vertical-align: middle;">หมวดพัสดุ</th>
							<th style="vertical-align: middle;">วันที่<?php echo $_GET["pickup_type"] == "0" ? "รับ" : "เบิก" ?></th>
							<th style="vertical-align: middle;">เลขพัสดุหลัก</th>
							<th style="vertical-align: middle;">รายการ</th>
							<th style="vertical-align: middle;">จำนวน</th>
							<th style="vertical-align: middle;">หน่วยงาน</th>
						</tr>  
					</thead>
					<tbody>
					
					<?php	
						$runno = $last_runno;
						$total_qty = 0;
						if(!empty($data_row)){
							foreach(@$data_row as $key => $row){
								$transaction_time = strtotime($row['transaction_time']);
								$date = date('Y-m-d', $transaction_time);
								$date_format = $this->center_function->ConvertToThaiDate($date);
								$time_format = date('H:i', $transaction_time);
								$total_qty += $row['qty'];
					?>
							<tr> 
							  <td style="text-align: center;vertical-align: top;"><?php echo $row['runno']; ?></td>
							  <td style="text-align: center;vertical-align: top;"><?php echo $row['facility_type_name'];?></td>
							  <td style="text-align: center;vertical-align: top;"><?php echo $this->center_function->ConvertToThaiDate($row[$_GET["pickup_type"] == "0" ? "receive_date" : "sign_date"],true,false);?></td>
							  <td style="text-align: center;vertical-align: top;"><?php echo $row['facility_main_code'];?></td>
							  <td style="text-align: left;vertical-align: top;"><?php echo $row['store_name'];?></td>
							  <td style="text-align: right;vertical-align: top;"><?php echo number_format($row['qty']);?></td>
							  <td style="text-align: center;vertical-align: top;"><?php echo $row['department_name'];?></td>
							</tr>										
					
					<?php
							}
						}
						$last_runno = $runno;
					?>
					<?php if(@$page == @$page_all){ ?>
						<tr style="font-weight: bold;">
							<td colspan="5" style="text-align: center;vertical-align: top;">รวม</td>
							<td style="text-align: right;vertical-align: top;"><?php echo number_format($total_qty); ?></td>
							<td style="text-align: right;vertical-align: top;"></td>
						</tr>
					<?php } ?>
					</tbody>
				</table>
				<?php echo @$paging ?>
			</div>
		</div>
<?php 
	}
} 
?>