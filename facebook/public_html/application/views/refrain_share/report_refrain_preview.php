<?php 
if(@$_GET['download']!=""){
     header("Content-type: application/vnd.ms-excel;charset=utf-8;");
     header("Content-Disposition: attachment; filename=รายงานการงดหุ้น ".$text_date.".xls"); 
     date_default_timezone_set('Asia/Bangkok');
?>
<style>
	.num {
	  mso-number-format:General;
	}
	.text{
	  mso-number-format:"\@";/*force text*/ 
	}
	.text-center{
		text-align: center;
	}
	.text-left{
		text-align: left;
	}
	.table_title{
		font-family: AngsanaUPC, MS Sans Serif;
		font-size: 22px;
		font-weight: bold;
		text-align:center;
	}
	.table_title_right{
		font-family: AngsanaUPC, MS Sans Serif;
		font-size: 16px;
		font-weight: bold;
		text-align:right;
	}
	.table_header_top{
		font-family: AngsanaUPC, MS Sans Serif;
		font-size: 19px;
		font-weight: bold;
		text-align:center;
		border-top: thin solid black;
		border-left: thin solid black;
		border-right: thin solid black;
		color: #000000 !important;
	}
	.table_header_mid{
		font-family: AngsanaUPC, MS Sans Serif;
		font-size: 19px;
		font-weight: bold;
		text-align:center;
		border-left: thin solid black;
		border-right: thin solid black;
	}
	.table_header_bot{
		font-family: AngsanaUPC, MS Sans Serif;
		font-size: 19px;
		font-weight: bold;
		text-align:center;
		border-bottom: thin solid black;
		border-left: thin solid black;
		border-right: thin solid black;
	}
	.table_header_bot2{
		font-family: AngsanaUPC, MS Sans Serif;
		font-size: 19px;
		font-weight: bold;
		text-align:center;
		border: thin solid black;
	}
	.table_body{
		font-family: AngsanaUPC, MS Sans Serif;
		font-size: 21px;
		border: thin solid black;
		color: #000000 !important;
	}
	.table_body_right{
		font-family: AngsanaUPC, MS Sans Serif;
		font-size: 21px;
		border: thin solid black;
		text-align:right;
	}
	
	h3{
		font-family: AngsanaUPC, MS Sans Serif;
		font-size: 22px;
		color: #000000 !important;
	}
	
	.body-excel{
		background: #FFFFFF !important;
		width: 100%;
	}
	
	.title_view{
		font-family: AngsanaUPC, MS Sans Serif;
		color: #000000 !important;
	}
</style>

<?php
}else{
?>
<style>
	.table-view>thead, .table-view>thead>tr>td, .table-view>thead>tr>th {
		font-size: 14px;
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
	.table>tbody>tr>td, .table>tbody>tr>th, .table>tfoot>tr>td, .table>tfoot>tr>th, .table>thead>tr>td, .table>thead>tr>th {
		padding:4px;
	}
</style>
<?php
}
?>

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
	
if(!empty($datas)){
	foreach($datas AS $page=>$data){
		//echo '<pre>'; print_r($data); echo '</pre>';
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
				<td class="text-center"	colspan="6">
						<?php
							if(@$_GET['download']!="excel"){
						?>
						<img src="<?php echo base_url(PROJECTPATH.'/assets/images/coop_profile/'.$_SESSION['COOP_IMG']); ?>" alt="Logo" style="height: 80px;" />
						<?php
							}
						?>
						<h3 class="title_view"><?php echo @$_SESSION['COOP_NAME'];?></h3>
						<h3 class="title_view">รายงานการงดหุ้น</h3>
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
					<a class="no_print"  target="_blank" href="<?php echo base_url(PROJECTPATH.'/refrain_share/report_refrain_preview'.$get_param.'&download=excel'); ?>">
						<button class="btn btn-perview btn-after-input" type="button"><span class="icon icon icon-file-excel-o" aria-hidden="true"></span></button>
					</a>
				</td>
			</tr>
		<?php } ?>
			<tr>
				<td colspan="8" style="text-align: right;">
					<span class="title_view">หน้าที่ <?php echo @$page.'/'.@$page_all;?></span><br>
				</td>
			</tr> 
			<tr>
				<td colspan="8" style="text-align: right;">
					<span class="title_view">วันที่ <?php echo $this->center_function->ConvertToThaiDate(@date('Y-m-d'),1,0);?></span>
				</td>
			</tr> 
			<tr>
				<td colspan="8" style="text-align: right;">
					<span class="title_view">เวลา <?php echo date('H:i:s');?></span>
				</td>
			</tr>
			<tr>
				<td colspan="8" style="text-align: right;">
					<span class="title_view">ผู้ทำรายการ <?php echo $_SESSION['USER_NAME'];?></span>
				</td>
			</tr>
		</table>

		<table class="table table-view table-center">
			<thead>
				<tr>
					<th style="width: 30px;vertical-align: middle;" class="table_header_top">ลำดับ</th>
					<th style="width: 110px;vertical-align: middle;" class="table_header_top">วันที่ทำรายการ</th>
					<th style="width: 80px;vertical-align: middle;" class="table_header_top">เลขสมาชิก</th>
					<th style="width: 200px;vertical-align: middle;" class="table_header_top">ชื่อ-นามสกุล</th>
					<th style="width: 80px;vertical-align: middle;" class="table_header_top">ประเภทการงดหุ้น</th>
					<th style="width: 100px;vertical-align: middle;" class="table_header_top">เดือน/ปี</th>
					<th style="width: 80px;vertical-align: middle;" class="table_header_top">ยอดเงิน</th>
					<th style="width: 100px;vertical-align: middle;" class="table_header_top">ผู้ทำรายการ</th>
				</tr>
			</thead>
			<tbody>

			<?php
				foreach($data as $key => $row){
					$runno++;
					$mmyy = (@$row['month_refrain'] != '')?@$month_arr[(int)@$row['month_refrain']]."/".@$row['year_refrain']:'';
			?>
				<tr>
					<td style="text-align: center;vertical-align: top;" class="table_body"><?php echo $runno; ?></td>
					<td style="text-align: center;vertical-align: top;" class="table_body"><?php echo ($row['createdatetime'])?$this->center_function->ConvertToThaiDate($row['createdatetime'],1,1):"";?></td>
					<td style="text-align: center;vertical-align: top;mso-number-format:'\@'" class="table_body"><?php echo $row['member_id'];?></td>
					<td style="text-align: left;vertical-align: top;" class="table_body"><?php echo $row['full_name'];?></td>
					<td style="text-align: center;vertical-align: top;" class="table_body"><?php echo @$type_refrain_list[@$row['type_refrain']];?></td>
					<td style="text-align: center;vertical-align: top;" class="table_body"><?php echo @$mmyy; ?></td>
					<td style="text-align: right;vertical-align: top;" class="table_body"><?php echo number_format($row['total_amount'],2); ?></td>
					<td style="text-align: left;vertical-align: top;" class="table_body"><?php echo $row['user_name'];?></td>
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