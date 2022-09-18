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

	$runno = 0;
	$all_withdrawal = 0;
	$all_deposit = 0;
	$all_balance = 0;
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
					<h3 class="title_view">รายงานการหักรายการฝากเงินเข้าบัญชีสีชมพู</h3>
					<h3 class="title_view">
					<?php
						echo " ประจำวันที่ ".$this->center_function->ConvertToThaiDate($start_date);
						echo (@$_GET['start_date'] == @$_GET['end_date'])?"":"  ถึง  ".$this->center_function->ConvertToThaiDate($end_date);
					?>
					</h3>
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
					<a class="no_print"  target="_blank" href="<?php echo base_url(PROJECTPATH.'/report_deposit_data/coop_report_deposit_month_account_transaction_excel'.$get_param); ?>">
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

		<table class="table table-view-2 table-center">
			<thead>
				<?php
					if(@$page == 1){
				?>
				<tr>
					<th colspan="10" style="text-align: left;">
						ประเภทบัญชี : เงินฝากออมทรัพย์
					</th>
				</tr>
				<?php
					}
				?>
				<tr>
					<th style="width: 30px;vertical-align: middle;">ลำดับ</th>
					<th style="width: 100px;vertical-align: middle;">วันที่</th>
					<th style="width: 120px;vertical-align: middle;">เลขบัญชี</th>
					<th style="width: 140px;vertical-align: middle;">ชื่อ-นามสกุล</th>
					<th style="width: 30px;vertical-align: middle;">เลขสมาชิก</th>
					<th style="width: 80px;vertical-align: middle;">เงินเดือน</th>
					<th style="width: 80px;vertical-align: middle;">ส่ง</th>
					<th style="vertical-align: middle;">หน่วย</th>
					<th style="width: 140px;vertical-align: middle;">ผู้ทำรายการ</th>
				</tr>
			</thead>
			<tbody>

			<?php
				foreach($data as $key => $row){
					$runno++;
			?>
				<tr>
					<td style="text-align: center;vertical-align: top;"><?php echo $runno; ?></td>
					<td style="text-align: center;vertical-align: top;"><?php echo ($row['transaction_time'])?$this->center_function->ConvertToThaiDate($row['transaction_time'],1,0):"";?></td>
					<td style="text-align: center;vertical-align: top;">
						<?php echo @$this->center_function->format_account_number($row['account_id']); ?>
					</td>
					<td style="text-align: left;vertical-align: top;"><?php echo $row['prename_full'].$row["firstname_th"]." ".$row["lastname_th"];?></td>
					<td style="text-align: center;vertical-align: top;"><?php echo $row['member_id'];?></td>
					<td style="text-align: right;vertical-align: top;"><?php echo number_format($row['salary'],2); ?></td>
					<td style="text-align: right;vertical-align: top;"><?php echo number_format($row['transaction_deposit'],2); ?></td>
					<td style="text-align: left;vertical-align: top;"><?php echo $row["level_name"];?></td>
					<td style="text-align: left;vertical-align: top;"><?php echo $row["user_name"];?></td>
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