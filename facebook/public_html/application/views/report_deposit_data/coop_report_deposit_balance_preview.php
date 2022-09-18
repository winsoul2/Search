<style>
	span.title_view{
		font-size: 18px;
	}
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
		font-size: 11px;
	}
	.border-bottom{
		border-bottom: 1px solid #000 !important;
		font-weight: bold;
	}

	.table-view-2>tbody>tr>td>span{
		font-family: Tahoma;
		font-size: 11px;
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
$all_interest = 0;
$chk_account_id = '';
$total_account= 0;
if(!empty($data)){
	foreach(@$data AS $page=>$data_row){
		?>

		<div style="width: 1323px;" class="page-break">
			<div class="panel panel-body" style="padding-top:10px !important;min-height: 832px;">
				<table style="width: 100%;">
						<tr>
							<td class="text-left">
								<span class="title_view">รายงานเงินฝากคงเหลือ <?php echo (@$_GET['type_id']!='all') ? " ประเภทบัญชี ".@$type_deposit[@$_GET['type_id']] : ""?></span>
							</td>
							<td colspan="3" style="text-align: right;">
								<span class="title_view"><?php echo @$_SESSION['COOP_NAME']; ?></span>
							</td>
						</tr>
						<tr>
							<td><span class="title_view">
									<?php
									echo " ระหว่างวันที่ ".$this->center_function->ConvertToThaiDate($start_date, false);
									echo (@$_GET['start_date'] == @$_GET['end_date'])?"":"  -  ".$this->center_function->ConvertToThaiDate($end_date, false);
									?>
								</span>
							</td>
							<td colspan="3" style="text-align: right;">
								<span class="title_view">หน้าที่ <?php echo @$page.'/'.@$page_all;?></span><br>
							</td>
						</tr>
				</table>

				<table class="table table-view-2 table-center">
					<thead>
					<tr>
						<th rowspan="2" style="width: 100px;vertical-align: middle;">เลขที่บัญชี</th>
						<th rowspan="2" style="width: 180px;vertical-align: middle;">ชื่อบัญชี</th>
						<th rowspan="2" style="width: 90px;vertical-align: middle;">เงินฝากคงเหลือยกมา</th>
						<th rowspan="2" style="width: 90px;vertical-align: middle;">รายการที่ี</th>
						<th colspan="4">รายการถอน/ปิดบัญชี</th>
						<th colspan="4">รายการฝาก/เปิดบัญชี</th>
						<th rowspan="2" style="width: 80px;vertical-align: middle;">เงินฝากคงเหลือ</th>
					</tr>
					<tr>
						<th style="width: 100px;vertical-align: middle;">วันที่</th>
						<th style="width: 70px;vertical-align: middle;">คำย่อ</th>
						<th style="width: 80px;vertical-align: middle;">ถอน</th>
						<th style="width: 80px;vertical-align: middle;">ดอกเบี้ย</th>
						<th style="width: 100px;vertical-align: middle;">วันที่</th>
						<th style="width: 70px;vertical-align: middle;">คำย่อ</th>
						<th style="width: 80px;vertical-align: middle;">ฝาก</th>
						<th style="width: 80px;vertical-align: middle;">ดอกเบี้ย</th>
					</tr>
					</thead>
					<tbody>

					<?php
					$runno = $last_runno;
					$total_transaction_withdrawal = 0;
					$total_transaction_deposit = 0;
					$total_transaction_balance = 0;
					$wd_interest = 0;
					$cd_interest = 0;
					$total_bf_transaction_balance = 0;
					$flag = 0;
					$temp_data = array();

					$chk_account_id_frist='';

					if(!empty($data_row)){
						foreach(@$data_row as $key => $row){
							$runno++;
							
							if($chk_account_id != $row['account_id']){
								$chk_account_id = $row['account_id'];
								$total_account++;
							}

							if($chk_account_id_frist != $row['account_id']){
								$account_id = $this->center_function->format_account_number(@$row['account_id']);
								$account_name = @$row['account_name'];

								$chk_account_id_frist = $row['account_id'];
								$i_frist=1;
							}else{
								$account_id = '';
								$account_name = '';
								$i_frist++;
							}

							$total_bf_transaction_balance += $row['bf_transaction_balance'];
							
							$total_transaction_withdrawal += @$row['transaction_withdrawal'];
							$wd_interest += $row['w_interest'];

							$total_transaction_deposit += @$row['transaction_deposit'];
							$cd_interest += @$row['d_interest'];

							$total_transaction_balance += $row['transaction_balance'];
							?>
							<tr>
								<td style="text-align: center;vertical-align: top;"><?php echo $account_id; ?></td>
								<td style="text-align: left;vertical-align: top;"><?php echo @$account_name;?></td>
								<td style="text-align: right;vertical-align: top;"><?php echo ($row['bf_transaction_balance']>0)?number_format($row['bf_transaction_balance'],2):''; ?></td>
								<td style="text-align: center;vertical-align: top;">
									<?php echo $this->center_function->format_req_account($row['seq_no']); ?>
								</td>
								<td style="text-align: center;vertical-align: top;">
									<?php 
										echo (@$row['w_transaction_time'])?$this->center_function->ConvertToThaiDate(@$row['w_transaction_time'],1,0):"";
									?>
								</td>
								<td style="text-align: center;vertical-align: top;"><?php echo $code_th[@$row['w_transaction_list']];?></td>
								<td style="text-align: right;vertical-align: top;"><?php echo ($row['transaction_withdrawal'] > 0) ? number_format($row['transaction_withdrawal'],2) : ""; ?></td>
								<td style="text-align: right;vertical-align: top;"><?php echo ($row['w_interest'] > 0) ? number_format($row['w_interest'],2) : "";  ?></td>
								<td style="text-align: center;vertical-align: top;">
									<?php 
										echo (@$row['d_transaction_time'])?$this->center_function->ConvertToThaiDate(@$row['d_transaction_time'],1,0):"";
									?>
								</td>
								<td style="text-align: center;vertical-align: top;"><?php echo $code_th[@$row['d_transaction_list']];?></td>
								<td style="text-align: right;vertical-align: top;"><?php echo ($row['transaction_deposit'] > 0) ? number_format($row['transaction_deposit'],2) : ""; ?></td>
								<td style="text-align: right;vertical-align: top;"><?php echo ($row['d_interest'] > 0) ? number_format($row['d_interest'],2) : "";   ?></td>
								<td style="text-align: right;vertical-align: top;"><?php echo ($row['transaction_balance'] > 0) ? number_format($row['transaction_balance'],2) : "";  ?></td>
							</tr>

							<?php
						}
						$last_runno = $runno;
						?>
						<tr class="border-bottom">
							<td colspan="2">เงินรวมหน้า</td>
							<td style="text-align: right;"><span style="border-bottom: 1px solid #000;"><?php echo number_format(@$total_bf_transaction_balance,2); ?></span></td>
							<td style="text-align: right;"  colspan="3"></td>
							<td style="text-align: right;"><span style="border-bottom: 1px solid #000;"><?php echo number_format(@$total_transaction_withdrawal,2); ?></span></td>
							<td style="text-align: right;"><span style="border-bottom: 1px solid #000;"><?php echo number_format(@$wd_interest,2); ?></span></td>
							<td style="text-align: right;"  colspan="2"></td>
							<td style="text-align: right;"><span style="border-bottom: 1px solid #000;"><?php echo number_format(@$total_transaction_deposit,2); ?></span></td>
							<td style="text-align: right;"><span style="border-bottom: 1px solid #000;"><?php echo number_format(@$cd_interest,2); ?></span></td>
							<td style="text-align: right;"><span style="border-bottom: 1px solid #000;"><?php echo number_format(@$total_transaction_balance,2); ?></span></td>
						</tr>
						<?php
					}

					$all_withdrawal += @$total_transaction_withdrawal;
					$all_deposit +=  @$total_transaction_deposit;
					$all_balance +=  @$total_transaction_balance;
					$all_interest +=  @$total_interest;
					$all_wd_interest += @$wd_interest;
					$all_cd_interest += @$cd_interest;
					$all_bf_transaction_balance += @$total_bf_transaction_balance;
					?>

					<?php
					if(@$page == @$page_all){
						?>
						<tr class="foot-border">
							<td style="text-align: center;" colspan="2">รวมทั้งหมด <?php echo @$total_account;?> รายการ &nbsp;&nbsp;&nbsp;&nbsp;จำนวนเงินทั้งหมด</td>
							<td style="text-align: right;"><?php echo number_format(@$all_bf_transaction_balance,2); ?></td>
							<td style="text-align: right;"  colspan="3"></td>

							<td style="text-align: right;"><?php echo number_format(@$all_withdrawal,2); ?></td>
							<td style="text-align: right;"><?php echo number_format(@$all_wd_interest,2); ?></td>
							<td style="text-align: right;"  colspan="2"></td>
							<td style="text-align: right;"><?php echo number_format(@$all_deposit,2); ?></td>
							<td style="text-align: right;"><?php echo number_format(@$all_cd_interest,2); ?></td>
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
<script>
	function export_excel(){
		var url = window.location.href+"&excel=export";
		window.location  = url;
	}
</script>
