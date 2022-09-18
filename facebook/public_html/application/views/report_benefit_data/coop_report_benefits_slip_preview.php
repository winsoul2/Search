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
		font-size: 14px;
	}	
	.border-bottom{
	    border-bottom: 1px solid #000 !important;
		font-weight: bold;
	}
	.table-view-2>tbody>tr>td>span{
		font-family: Tahoma;
		font-size: 14px !important;
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
	if(@$_GET['print_date']){
		$start_date_arr = explode('/',@$_GET['print_date']);
		$start_day = $start_date_arr[0];
		$start_month = $start_date_arr[1];
		$start_year = $start_date_arr[2];
		$start_year -= 543;
		$start_date = $start_year.'-'.$start_month.'-'.$start_day;
	}

	$runno = 0;
	foreach($datas AS $page=>$data){
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
					<h3 class="title_view">รายงานนำจ่ายสวัสดิการ</h3>
					<h3 class="title_view">
					<?php
						echo " ประจำวันที่ ".$this->center_function->ConvertToThaiDate($start_date);
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
				<?php
					if(@$page == 1){
				?>
				<?php
					}
				?>
				<tr>
					<th style="vertical-align: middle;">ลำดับ</th>
					<th style="vertical-align: middle;">ชื่อสวัสดิการ</th>
					<th style="vertical-align: middle;">รหัสสมาชิก</th>
					<th style="vertical-align: middle;">ชื่อสกุล</th>
					<th style="vertical-align: middle;">จำนวนเงิน</th>
				</tr>
			</thead>
			<tbody>

			<?php
				foreach($data as $key => $row){
			?>
				<tr>
					<td class="text-center" style="vertical-align: middle;"><?php echo ++$runno;?></td>
					<td class="text-left" style="vertical-align: middle;"><?php echo $row["benefits_name"];?></td>
					<td class="text-center" style="vertical-align: middle;"><?php echo $row["member_id"];?></td>
					<td class="text-left" style="vertical-align: middle;"><?php echo $row["prename_full"].$row["firstname_th"]." ".$row["lastname_th"];?></td>
					<td class="text-right" style="vertical-align: middle;"><?php echo number_format($row["benefits_approved_amount"],2);?></td>
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