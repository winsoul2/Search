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
		font-family: Tahoma;
		font-size: 12px;
	}	
	.border-bottom{
	    border-bottom: 1px solid #000 !important;
		font-weight: bold;
	}
	.table-view-2>tbody>tr>td>span{
		font-family: Tahoma;
		font-size: 12px !important;
	}
	.foot-border{
	    border-top: 1px solid #000 !important;
		border-bottom: double !important;
		font-weight: bold;
	}
	.table {
		color: #000;
	}
	.table>tbody>tr>td, .table>tbody>tr>th, .table>tfoot>tr>td, .table>tfoot>tr>th, .table>thead>tr>td, .table>thead>tr>th {
		padding:6px;
	}
</style>
<?php
	if($_GET['month']!='' && $_GET['year']!=''){
		$day = '';
		$month = $_GET['month'];
		$year = $_GET['year'];
		$title_date = " เดือน ".$this->month_arr[$month]." ปี ".($year);
	}else{
		$day = '';
		$month = '';
		$year = ($_GET['year']);
		$title_date = " ปี ".($year);
	}

	$runno = 0;
	$total = 0;
	$charged_total = 0;
	$paid_total = 0;
	$debt_total = 0;
	foreach($datas AS $page=>$data){
?>
<div style="width: 1000px;" class="page-break">
	<div class="panel panel-body" style="padding-top:10px !important;height: 1200px;">
		<table style="width: 100%;">
		<?php 
			if(@$page == 1){
		?>	
			<tr>
				<td style="width:100px;vertical-align: top;">

				</td>
				<td class="text-center">
					<img src="<?php echo base_url(PROJECTPATH.$this->logo_path); ?>" alt="Logo" style="height: 80px;" />
					<h3 class="title_view"><?php echo @$_SESSION['COOP_NAME'];?></h3>
					<h3 class="title_view">รายงานรายการเรียกเก็บ</h3>
					<h3 class="title_view"><?php echo " ประจำ ".@$title_date;?></h3>
				</td>
				<td style="width:100px;vertical-align: top;" class="text-right">
					<a class="no_print" onclick="window.print();"><button class="btn btn-perview btn-after-input" type="button"><span class="icon icon-print" aria-hidden="true"></span></button></a>
					<?php
						$get_param = '?';
						foreach(@$_GET as $key => $value){
							$get_param .= $key.'='.$value.'&';
						}
						$get_param = substr($get_param,0,-1);
					?>
					<a class="no_print"  target="_blank" href="<?php echo base_url(PROJECTPATH.'/report_cremation_data/coop_report_finance_month_excel_non_member'.$get_param); ?>">
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
				<?php
					if(@$page == 1){
				?>
				<?php
					}
				?>
				<tr>
					<th style="vertical-align: middle; width:5%;">ลำดับ</th>
					<th style="vertical-align: middle; width:15%;">วันที่</th>
					<th style="vertical-align: middle; width:10%;">เลขฌาปนกิจ</th>
					<th style="vertical-align: middle; width:23%;">ชื่อสกุล</th>
					<th style="vertical-align: middle; width:10%;">รหัสสมาชิก</th>
					<th style="vertical-align: middle; width:8%;">ยอดเรียกเก็บ</th>
					<th style="vertical-align: middle; width:8%;">เก็บได้</th>
					<th style="vertical-align: middle; width:13%;">เลขที่ใบเสร็จ</th>
					<th style="vertical-align: middle; width:8%;">คงค้าง</th>
				</tr>
			</thead>
			<tbody>

			<?php
				foreach($data as $key => $row){
					$charged_total += $row["pay_amount"];
					$paid_total += $row["real_pay_amount"];
					$debt_total += $row["pay_amount"] - $row["real_pay_amount"];
			?>
				<tr>
					<td class="text-center" style="vertical-align: middle;"><?php echo ++$runno;?></td>
					<td class="text-center" style="vertical-align: middle;"><?php echo $this->center_function->ConvertToThaiDate($row["create_datetime"]);?></td>
					<td class="text-center" style="vertical-align: middle;">
						<?php
							foreach($row["member_cremation_id"] as $index => $member_cremation_id) {
								echo $index >= 1 ? ", ".$member_cremation_id : $member_cremation_id;
							}
						?>
					</td>
					<td class="text-left" style="vertical-align: middle;"><?php echo $row["prename_full"].$row["firstname_th"]." ".$row["lastname_th"];?></td>
					<td class="text-center" style="vertical-align: middle;"><?php echo $row["member_id"];?></td>
					<td class="text-right" style="vertical-align: middle;"><?php echo number_format($row["pay_amount"],2);?></td>
					<td class="text-right" style="vertical-align: middle;"><?php echo number_format($row["real_pay_amount"],2);?></td>
					<td class="text-center" style="vertical-align: middle;"><?php echo $row["receipt_id"];?></td>
					<td class="text-right" style="vertical-align: middle;"><?php echo number_format($row["pay_amount"] - $row["real_pay_amount"],2);?></td>
				</tr>
			<?php
					$total += $row["benefits_approved_amount"];
				}
				if ($page == $page_all) {
			?>
				<tr>
					<td colspan="5" class="text-center" style="vertical-align: middle;">รวม</td>
					<td class="text-right" style="vertical-align: middle;"><?php echo number_format($charged_total,2);?></td>
					<td class="text-right" style="vertical-align: middle;"><?php echo number_format($paid_total,2);?></td>
					<td class="text-center" style="vertical-align: middle;"></td>
					<td class="text-center" style="vertical-align: middle;"><?php echo number_format($debt_total,2);?></td>
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
?>