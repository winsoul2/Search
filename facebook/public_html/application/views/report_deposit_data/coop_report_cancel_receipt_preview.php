<style>
	.table-view>thead, .table-view>thead>tr>td, .table-view>thead>tr>th {
		font-size: 14px;
	}
	.table-view-2>thead>tr>th{
	    border-top: 1px solid #000 !important;
		border-bottom: 1px solid #000 !important;
		font-size: 14;
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
$title_date = "";
if($_GET['month']!='' && $_GET['year']!=''){
	$day = '';
	$month = $_GET['month'];
	$year = $_GET['year'];
	$title_date = " เดือน ".$month_arr[$month]." ปี ".($year);
}
$last_runno = 0;
$all_withdrawal = 0;
$all_deposit = 0;
$all_balance = 0;

$prev_member_id = 'x';
$total = array();
if(!empty($datas)){
	foreach($datas AS $page=>$data_row){
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
							 <h3 class="title_view">รายการผิดนัดชำระหนี้ประจำ<?php echo $title_date;?></h3>
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
							<a class="no_print"  target="_blank" href="<?php echo base_url('/report_deposit_data/coop_report_cancel_receipt_excel'.$get_param); ?>">
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
						<td colspan="11" style="text-align: right;">
							<span class="title_view">วันที่ <?php echo $this->center_function->ConvertToThaiDate(@date('Y-m-d'),1,0);?></span>				
							<span class="title_view">   เวลา <?php echo date('H:i:s');?></span>	
						</td>
					</tr> 
					<tr>
						<td colspan="11" style="text-align: right;">
							<span class="title_view">หน้าที่ <?php echo $page.'/'.$page_all;?></span><br>						
						</td>
					</tr>
				</table>
			
				<table class="table table-view table-center">
					<thead> 
						<tr>
							<th style="width: 40px;vertical-align: middle;">ลำดับ</th>
							<th style="width: 80px;vertical-align: middle;">เลขที่สมาชิก</th>
							<th style="width: 160px;vertical-align: middle;">ชื่อสมาชิก</th>
							<th style="width: 200px;vertical-align: middle;">หน่วยงานหลัก</th>
							<th style="width: 100px;vertical-align: middle;">เหตุผล</th>
							<th style="width: 160px;vertical-align: middle;">สัญญา</th>
							<th style="width: 80px;vertical-align: middle;">เรียกเก็บ</th>
							<th style="width: 80px;vertical-align: middle;">เก็บได้</th>
							<th style="width: 80px;vertical-align: middle;">ผลต่าง</th>
							<th style="width: 80px;vertical-align: middle;">ยอดหนี้คงเหลือ</th>
							<th style="width: 80px;vertical-align: middle;">ผิดนัดครั้งที่</th>
						</tr>
					</thead>
					<tbody>
					
					<?php	
						$runno = $last_runno;
						if(!empty($data_row)){
							foreach($data_row as $key => $row){
					?>
						<tr> 
					<?php
								if($prev_member_id != $row['member_id']) {
									$runno++;	
					?>
							<td style="text-align: center;vertical-align: top;"><?php echo $runno;?></td>
							<td style="text-align: center;vertical-align: top;"><?php echo $row['member_id'];?></td>
							<td style="text-align: left;vertical-align: top;"><?php echo $row['member_name'];?></td>
							<td style="text-align: left;vertical-align: top;"><?php echo $row['department_name'];?></td>
					<?php
								} else {
					?>
							<td style="text-align: center;vertical-align: top;"></td>
							<td style="text-align: center;vertical-align: top;"><?php echo $row['member_id'];?></td>
							<td style="text-align: left;vertical-align: top;"></td>
							<td style="text-align: left;vertical-align: top;"><?php echo $row['department_name'];?></td>
					<?php
								}
					?>
							<td style="text-align: center;vertical-align: top;"></td>
							<td style="text-align: center;vertical-align: top;"><?php echo $row['contract'];?></td>
							<td style="text-align: right;vertical-align: top;"><?php echo number_format($row['pay_amount'],2);?></td>
							<td style="text-align: right;vertical-align: top;"><?php echo number_format($row['real_pay_amount'],2);?></td>
							<td style="text-align: right;vertical-align: top;"><?php echo number_format($row['pay_amount'] - $row['real_pay_amount'],2);?></td>			 
					<?php
							if($prev_member_id != $row['member_id']) {
					?>
							<td style="text-align: right;vertical-align: top;"><?php echo number_format($row['dept_total'],2);?></td>
							<td style="text-align: center;vertical-align: top;"><?php echo number_format($row['num_non_pay'],0);?></td>
					<?php
								$total['dept_total'] += $row['dept_total'];
							} else {
					?>
							<td style="text-align: center;vertical-align: top;"></td>
							<td style="text-align: center;vertical-align: top;"><?php echo number_format($row['num_non_pay'],0);?></td>
					<?php
							}
					?>
						</tr>
					<?php
							$total['pay_amount'] += $row['pay_amount'];
							$total['real_pay_amount'] += $row['real_pay_amount'];
							$prev_member_id = $row['member_id'];
							}
						}
						if($page == $page_all) {
					?>
					
						<tr>
							<td colspan="6" style="text-align: center;vertical-align: top;">ยอดรวม</td>
							<td style="text-align: right;vertical-align: top;"><?php echo number_format($total['pay_amount'],2);?></td>
							<td style="text-align: right;vertical-align: top;"><?php echo number_format($total['real_pay_amount'],2);?></td>
							<td style="text-align: right;vertical-align: top;"><?php echo number_format($total['pay_amount'] - $total['real_pay_amount'],2);?></td>
							<td style="text-align: right;vertical-align: top;"><?php echo number_format($total['dept_total'] ,2);?></td>
							<td style="text-align: center;vertical-align: top;"></td>
						</tr>
					<?php
						}
					?>
					</tbody>    
				</table>
			</div>
		</div>
<?php 
	}
} 
?>