<style>
	.table-view > thead, .table-view > thead > tr > td, .table-view > thead > tr > th {
		font-size: 14px;
	}

	.table-view-2 > thead > tr > th {
		border-top: 1px solid #000 !important;
		border-bottom: 1px solid #000 !important;
		font-size: 16px;
	}

	.table-view-2 > tbody > tr > td {
		border: 0px !important;
		/*font-family: upbean;
		font-size: 16px;*/
		font-family: Tahoma;
		font-size: 12px;
	}

	.border-bottom {
		border-bottom: 1px solid #000 !important;
		font-weight: bold;
	}

	.foot-border {
		border-top: 1px solid #000 !important;
		border-bottom: double !important;
		font-weight: bold;
	}

	.table {
		color: #000;
	}
	
	.title_view_small{
		font-size: 12px;
		font-family: THSarabunNew;
		color: #000;
		line-height: 23px;
	}

	@media print {
		.pagination {
			display: none;
		}
	}
</style>
<?php
//class="page-break"
//
$last_runno = 0;
$sum_interest = 0 ;
$type_name = $data['0']['type_name'];
$page = 1;
$page_all = count($data);
//echo count($data); echo '<br>';
//echo '<pre>'; print_r($data); echo '</pre>'; exit;
if(!empty($data)){
 	foreach(@$data AS $keys=>$datas){
?>

<div style="width: 1000px;" class="page-break">
	<div class="panel panel-body" style="padding-top:10px !important;min-height: 1200px;">
		<table style="width: 100%;" class="title_view_small">
			<tr>
				<td colspan="7" style="text-align: right;"><?php echo @$text_title_1; ?></td>
			</tr>
			<tr>
				<td colspan="7" style="text-align: right;"><?php echo @$text_title_2; ?></td>
			</tr>
			<tr>
				<td colspan="7" style="text-align: right;">หน้าที่ <?php echo $page.'/'.$page_all; ?></td>
			</tr>
			<tr>
				<td>ทะเบียนเงินฝาก</td>
				<td colspan="6"><?php echo $deposit_type; ?></td>
			</tr>
			<tr>
				<td style="width:90px;">ชื่อบัญชี</td>
				<td style="width:180px;"><?php echo $datas['row_head']['account_name']; ?></td>
				<td style="width:90px;">เลขที่บัญชี</td>
				<td style="width:120px;"><?php echo $this->center_function->format_account_number($datas['row_head']['account_id']); ?></td>
				<td style="width:80px;">สถานภาพบัญชี</td>
				<td style="width:120px;"><?php echo $datas['row_head']['account_status']; ?></td>
				<td style="width:300px;">&nbsp;</td>
			</tr>
			<tr>
				<td>รหัสสมาชิก</td>
				<td><?php echo $datas['row_head']['member_id']; ?></td>
				<td>วันที่เปิดบัญชี</td>
				<td><?php echo $datas['row_head']['created']; ?></td>
				<td>วันที่ปิดบัญชี</td>
				<td><?php echo $datas['row_head']['close_account_date']; ?></td>
				<td>&nbsp;</td>
			</tr>
				<!--				
				<td style="width:100px;vertical-align: top;" class="text-right">
					<a class="no_print" onclick="window.print();">
						<button class="btn btn-perview btn-after-input" type="button"><span
								class="icon icon-print" aria-hidden="true"></span></button>
					</a>
					<a class="no_print" onclick="export_excel()">
						<button class="btn btn-perview btn-after-input" type="button"><span
								class="fa fa-file-excel-o" aria-hidden="true"></span></button>
					</a>
				</td>
				-->	
		</table>
		<table class="table table-view table-center">
			<thead>
			<tr>
				<th style="vertical-align: middle;width: 80px;">รายการที่</th>
				<th style="vertical-align: middle;">วันที่</th>
				<th style="vertical-align: middle;">คำย่อ</th>
				<th style="vertical-align: middle;">ถอน</th>
				<th style="vertical-align: middle;">ฝาก</th>
				<th style="vertical-align: middle;">เงินฝากคงเหลือ</th>
				<th style="vertical-align: middle;">วันที่ถอน</th>
				<th style="vertical-align: middle;">ดอกเบี้ย</th>
				<th style="vertical-align: middle;">ภาษี</th>
				<th style="vertical-align: middle;">ดอกเบี้ยค้างจ่าย</th>
			</tr>
			</thead>
			<tbody>
			<?php
			if (!empty($datas['row_detail'])) {
				foreach (@$datas['row_detail'] as $key => $row) {
			?>
					<tr>
						<td style="text-align: center;vertical-align: top;"><?php echo @$row['transaction_no']; ?></td>
						<td style="text-align: center;vertical-align: top;"><?php echo @$this->center_function->mydate2date(@$row['transaction_time']); ?></td>
						<td style="text-align: center;vertical-align: top;"><?php echo @$row['transaction_list']; ?></td>
						<td style="text-align: right;vertical-align: top;"><?php echo @number_format(@$row['transaction_withdrawal'],2); ?></td>
						<td style="text-align: right;vertical-align: top;"><?php echo @number_format(@$row['transaction_deposit'],2); ?></td>
						<td style="text-align: right;vertical-align: top;"><?php echo @number_format($row['transaction_balance'],2); ?></td>
						<td style="text-align: center;vertical-align: top;"><?php echo @$row['date_withdrawal']; ?></td>
						<td style="text-align: right;vertical-align: top;"><?php echo @number_format($row['interest'],2); ?></td>
						<td style="text-align: right;vertical-align: top;"><?php echo @number_format($row['tax'],2); ?></td>
						<td style="text-align: right;vertical-align: top;"><?php echo @number_format($row['accrued_interest'],2); ?></td>
					</tr>
			<?php
				}
			}
			//$last_runno = $runno;
			?>
			</tbody>
		</table>
	</div>
</div>
<?php
		$page++;
	}
 }	
?>
<script>
	function export_excel() {
		var url = window.location.href + "&excel=export";
		window.location = url;
	}
</script>
